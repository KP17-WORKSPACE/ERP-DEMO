<?php

namespace App\Http\Controllers;

use App\HrmsApproverChain;
use App\HrmsApproverChainStep;
use App\SmLeaveRequest;
use App\SmLeaveType;
use App\SmRolePermission;
use App\SmStaff;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ApprovalController extends Controller
{
    public function index(Request $request)
    {
        return $this->renderLeaveScreen($request, false);
    }

    public function track(Request $request, $id = null)
    {
        if ($id && !$request->filled('active')) {
            $request->merge(['active' => $id]);
        }

        return $this->renderLeaveScreen($request, true);
    }

    private function renderLeaveScreen(Request $request, bool $isTrack)
    {
        $authUser = Auth::user();
        $userId = (int) $authUser->id;
        $isAdmin = in_array((int) $authUser->role_id, [1, 2], true);

        $q = $this->baseLeaveQuery();

        if ($isTrack) {
            $q->where('approve_status', '!=', 'D')
                ->whereHas('chain.steps');

            if (!$isAdmin) {
                $q->whereHas('chain.steps', function ($sq) use ($userId) {
                    $sq->where('approver_id', $userId);
                });
            }
        } else {
            $q->where(function ($query) use ($userId, $isAdmin) {
                $query->where('staff_id', $userId)
                    ->orWhere(function ($q2) use ($userId, $isAdmin) {
                        $q2->where('approve_status', '!=', 'D');
                        if ($isAdmin) {
                            $q2->whereHas('chain.steps');
                        } else {
                            $q2->whereHas('chain.steps', function ($sq) use ($userId) {
                                $sq->where('approver_id', $userId);
                            });
                        }
                    });
            });
        }

        $this->applyFilters($q, $request);

        $leaves = $q->orderBy('id', 'desc')
            ->paginate(12)
            ->appends($request->query());

        $activeId = (int) $request->get('active', 0);
        $action = false;
        $editLeave = null;
        $selectedLeave = null;

        if (!$isTrack && $request->has('leave_action')) {
            $requestedAction = (string) $request->get('leave_action');
            if ($requestedAction === 'add') {
                $action = 'add';
            } elseif ($requestedAction === 'edit' && $activeId > 0) {
                $editLeave = $this->findLeaveForPanel($activeId);
                if ($editLeave) {
                    $action = 'edit';
                }
            }
        }

        if (!$action && $activeId) {
            $selectedLeave = $this->findLeaveForPanel($activeId);
            if ($isTrack && !$this->canViewInTrack($selectedLeave, $authUser)) {
                $selectedLeave = null;
            }
        }

        if (!$action && !$selectedLeave && $leaves->count() > 0) {
            $selectedLeave = $this->findLeaveForPanel($leaves->first()->id);
        }

        $formData = $this->leaveFormData($authUser);
        $isTrack = $isTrack;
        $permissions = SmRolePermission::where('role_id', $authUser->role_id)->get();

        return view('backEnd.approvals.inbox', array_merge($formData, compact(
            'leaves',
            'selectedLeave',
            'action',
            'editLeave',
            'authUser',
            'isTrack',
            'permissions'
        )));
    }

    public function show(Request $request, $id)
    {
        $leave = $this->findLeaveForPanel((int) $id);
        $trackMode = $request->get('context') === 'track';

        if (!$leave) {
            abort(404);
        }

        if ($trackMode && !$this->canViewInTrack($leave, Auth::user())) {
            return response('<p class="text-danger">You are not authorized to view this leave request in Leave Track.</p>', 403);
        }

        return view('backEnd.approvals._details', [
            'leave' => $leave,
            'trackMode' => $trackMode,
        ])->render();
    }

    public function action(Request $request)
    {
        $step = HrmsApproverChainStep::with(['chain.leaveRequest', 'chain.steps'])->findOrFail($request->input('step_id'));
        $chain = $step->chain;
        $leave = $chain ? $chain->leaveRequest : null;

        if (!$chain || !$leave) {
            return redirect()->back()->with('error', 'Approval chain is not available.');
        }

        if (!$this->canActOnStep($step)) {
            return redirect()->back()->with('error', 'You are not authorized to approve this stage.');
        }

        $role = strtolower((string) $request->input('actor_role'));
        $decision = 'Approve';

        if (strpos($role, 'report') !== false || strpos($role, 'manager') !== false) {
            $request->validate([
                'l1_decision' => 'required|in:Approve,Reject,Return',
                'l1_workload' => 'required',
                'l1_coverage' => 'required',
                'l1_eligibility' => 'required',
                'l1_duration_ok' => 'required',
                'l1_notice_compliance' => 'required',
                'l1_emergency' => 'required',
            ]);
        } elseif (strpos($role, 'hr') !== false) {
            $request->validate([
                'l2_decision' => 'required|in:Approve,Reject',
                'l2_balance_verify' => 'required|in:Yes,No',
                'l2_policy_verify' => 'required|in:Yes,No',
                'l2_docs_verify' => 'required|in:Yes,No',
                'management_approval_req' => 'required|in:Yes,No',
                'l2_balance' => 'required',
                'l2_unpaid' => 'required',
                'l2_encash' => 'required',
                'l2_cost' => 'required',
                'l2_policy' => 'required',
                'l2_docs' => 'required',
            ]);
        } else {
            $request->validate([
                'l3_decision' => 'required|in:Approve,Reject',
                'l3_limits' => 'required',
                'l3_critical' => 'required',
                'l3_blackout' => 'required',
                'l3_exceptional' => 'required',
            ]);
        }

        DB::transaction(function () use ($request, $step, $chain, $leave, $role, &$decision) {
            if (strpos($role, 'report') !== false || strpos($role, 'manager') !== false) {
                $step->fill($request->only([
                    'l1_workload',
                    'l1_coverage',
                    'l1_eligibility',
                    'l1_duration_ok',
                    'l1_notice_compliance',
                    'l1_emergency',
                    'l1_recommended_action',
                    'l1_decision',
                    'l1_remark',
                ]));
                $decision = $request->input('l1_decision', 'Approve');
                $step->comment = $request->input('l1_remark');
            } elseif (strpos($role, 'hr') !== false) {
                $step->fill($request->only([
                    'l2_balance_verify',
                    'l2_policy_verify',
                    'l2_docs_verify',
                    'l2_balance',
                    'l2_unpaid',
                    'l2_encash',
                    'l2_cost',
                    'l2_policy',
                    'l2_docs',
                    'l2_decision',
                    'l2_remark',
                ]));
                $decision = $request->input('l2_decision', 'Approve');
                $step->comment = $request->input('l2_remark');

                if ($request->has('management_approval_req')) {
                    $leave->management_approval_req = $request->input('management_approval_req');
                }
            } else {
                $step->fill($request->only([
                    'l3_limits',
                    'l3_critical',
                    'l3_blackout',
                    'l3_exceptional',
                    'l3_decision',
                    'l3_remark',
                ]));
                $decision = $request->input('l3_decision', $request->input('l2_decision', 'Approve'));
                $step->comment = $request->input('l3_remark', $request->input('l2_remark'));
            }

            $step->status = $decision === 'Approve' ? 'A' : ($decision === 'Reject' ? 'R' : 'C');
            $step->acted_at = now();
            $step->approver_id = Auth::id();
            $step->save();

            if ((strpos($role, 'report') !== false || strpos($role, 'manager') !== false) && $step->status === 'A') {
                $hrStep = $chain->steps()->where('role', 'HR')->first();
                if ($hrStep && $hrStep->status === 'S') {
                    $hrStep->status = 'P';
                    $hrStep->save();
                }
            }

            if (strpos($role, 'hr') !== false && $step->status === 'A') {
                $managementStep = $chain->steps()->where('role', 'Management')->first();
                if ($leave->management_approval_req === 'Yes') {
                    if ($managementStep) {
                        $managementStep->status = 'P';
                        $managementStep->save();
                    }
                } elseif ($managementStep && $managementStep->status === 'P') {
                    $managementStep->status = 'S';
                    $managementStep->save();
                }
            }

            if ($step->status === 'R') {
                $chain->overall_status = 'R';
                $chain->save();
                $leave->approve_status = 'R';
                $leave->rejected_by = Auth::id();
                $leave->rejected_at = now();
                $leave->save();
                return;
            }

            if ($step->status === 'C') {
                $chain->overall_status = 'C';
                $chain->save();
                $leave->approve_status = 'C';
                $leave->save();
                return;
            }

            $hasPending = $chain->steps()->where('status', 'P')->exists();

            if ($hasPending) {
                $chain->overall_status = 'P';
                $chain->save();
                $leave->approve_status = 'P';
                $leave->save();
                return;
            }

            $chain->overall_status = 'A';
            $chain->save();
            $leave->approve_status = 'A';
            $leave->approved_by = Auth::id();
            $leave->approved_at = now();
            $leave->save();
        });

        return redirect()->back()->with('success', 'Approval updated successfully.');
    }

    public function updateHandover(Request $request)
    {
        $request->validate([
            'leave_id' => 'required|integer',
            'pending_tasks' => 'nullable|in:Yes,No',
            'access_transfer_required' => 'nullable|in:Yes,No',
            'handover_completion_confirmation' => 'nullable|in:Yes,No',
            'manager_verification_of_handover' => 'nullable|in:Yes,No',
        ]);

        $leave = SmLeaveRequest::findOrFail($request->input('leave_id'));
        $anyApprovalDone = $leave->chain && $leave->chain->steps
            ? $leave->chain->steps->contains(function ($step) {
                return $step->status === 'A';
            })
            : false;

        if ($leave->approve_status !== 'P' || $anyApprovalDone) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Handover information cannot be edited after approval has started.',
                ], 422);
            }

            return redirect()->back()->with('error', 'Handover information cannot be edited after approval has started.');
        }

        $leaveFill = $request->only([
            'pending_tasks',
            'client_responsibilities',
            'access_transfer_required',
            'handover_completion_confirmation',
            'manager_verification_of_handover',
            'handover_additional_remarks',
        ]);

        if (!empty($leaveFill)) {
            $leave->update($leaveFill);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Handover information updated successfully.',
                'values' => $leaveFill,
            ]);
        }

        return redirect()->back()->with('success', 'Handover information updated successfully.');
    }

    private function baseLeaveQuery()
    {
        return SmLeaveRequest::query()->with([
            'type',
            'company',
            'staffs' => function ($s) {
                $s->select('id', 'user_id', 'first_name', 'last_name', 'full_name', 'email', 'designation_id', 'department_id');
            },
            'staffs.designations:id,title',
            'staffs.departments:id,name',
            'chain.steps' => function ($s) {
                $s->orderBy('step_no')->with('approver');
            },
        ]);
    }

    private function findLeaveForPanel(int $id)
    {
        $leave = $this->baseLeaveQuery()->find($id);
        if ($leave) {
            $this->normalizeLeaveApprovalChain($leave);
            $leave = $this->baseLeaveQuery()->find($id);
        }
        return $leave;
    }

    private function normalizeLeaveApprovalChain(SmLeaveRequest $leave)
    {
        if ($leave->approve_status === 'D' || !$leave->chain) {
            return;
        }

        $chainId = (int) $leave->chain->id;
        $steps = HrmsApproverChainStep::where('approver_chain_id', $chainId)->get();
        $usedIds = [];

        $nextAvailableStepNo = function () use ($chainId) {
            $existing = HrmsApproverChainStep::where('approver_chain_id', $chainId)
                ->pluck('step_no')
                ->map(function ($value) {
                    return (int) $value;
                })
                ->toArray();
            $next = empty($existing) ? 1 : (max($existing) + 1);
            while (in_array($next, $existing, true)) {
                $next++;
            }
            return $next;
        };

        $resolveStep = function ($stage, $stepNo) use ($steps, &$usedIds, $chainId, $nextAvailableStepNo) {
            $byStage = $steps->first(function ($step) use ($stage, $usedIds) {
                if (in_array((int) $step->id, $usedIds, true)) {
                    return false;
                }
                $role = strtolower((string) $step->role);
                if ($stage === 'reporting') {
                    return Str::contains($role, 'report');
                }
                if ($stage === 'hr') {
                    return $role === 'hr';
                }
                return Str::contains($role, 'management') || Str::contains($role, 'finance');
            });

            if ($byStage) {
                $usedIds[] = (int) $byStage->id;
                return $byStage;
            }

            $byOrder = $steps->first(function ($step) use ($stepNo, $usedIds) {
                return (int) $step->step_no === (int) $stepNo
                    && !in_array((int) $step->id, $usedIds, true);
            });

            if ($byOrder) {
                $usedIds[] = (int) $byOrder->id;
                return $byOrder;
            }

            $targetStepNo = HrmsApproverChainStep::where('approver_chain_id', $chainId)
                ->where('step_no', $stepNo)
                ->exists()
                ? $nextAvailableStepNo()
                : $stepNo;

            $defaultRole = $stage === 'hr'
                ? 'HR'
                : ($stage === 'management' ? 'Management' : 'Reporting Manager');

            $step = HrmsApproverChainStep::firstOrCreate(
                ['approver_chain_id' => $chainId, 'step_no' => $targetStepNo],
                ['role' => $defaultRole, 'status' => 'S']
            );
            $usedIds[] = (int) $step->id;
            return $step;
        };

        $reportingStep = $resolveStep('reporting', 1);
        $hadReporting = Str::contains(strtolower((string) $reportingStep->role), 'report');
        $reportingStep->role = 'Reporting Manager';
        if (in_array($reportingStep->status, ['P', 'S'])) {
            $reportingStep->approver_id = (int) ($leave->reporting_manager_id ?: $reportingStep->approver_id);
        }
        if (!$hadReporting && $reportingStep->status === 'S') {
            $reportingStep->status = 'P';
        }
        $reportingStep->save();

        $hrStep = $resolveStep('hr', 2);
        $hrStep->role = 'HR';
        if (in_array($hrStep->status, ['P', 'S'])) {
            $hrStep->approver_id = (int) ($hrStep->approver_id ?: 7);
        }
        if ($reportingStep->status === 'A' && $hrStep->status === 'S') {
            $hrStep->status = 'P';
        } elseif ($hrStep->status === 'P' && $reportingStep->status !== 'A') {
            $hrStep->status = 'S';
        }
        $hrStep->save();

        $managementStep = $resolveStep('management', 3);
        $managementStep->role = 'Management';
        if (in_array($managementStep->status, ['P', 'S'])) {
            $managementStep->approver_id = (int) ($managementStep->approver_id ?: 12);
        }
        if ($hrStep->status === 'A' && $leave->management_approval_req === 'Yes' && $managementStep->status === 'S') {
            $managementStep->status = 'P';
        } elseif (($hrStep->status !== 'A' || $leave->management_approval_req !== 'Yes') && $managementStep->status === 'P') {
            $managementStep->status = 'S';
        }
        $managementStep->save();

        HrmsApproverChainStep::where('approver_chain_id', $chainId)
            ->whereNotIn('id', $usedIds)
            ->where('status', 'P')
            ->update(['status' => 'S']);
    }

    private function leaveFormData($authUser)
    {
        $authStaff = SmStaff::where('user_id', $authUser->id)->first();

        return [
            'leaveTypes' => SmLeaveType::where('is_active', 1)->orderBy('name')->get(),
            'employees' => SmStaff::where('active_status', 1)->orderBy('full_name')->get(),
            'reportingManager' => User::where('id', '!=', $authUser->id)->orderBy('full_name')->get(),
            'leaveApplicationNo' => $this->nextLeaveApplicationNo(optional($authStaff)->company_id ?: ($authUser->company_id ?? null)),
        ];
    }

    private function applyFilters($q, Request $request)
    {
        $status = strtoupper((string) $request->get('status', ''));
        $qText = trim((string) $request->get('q', ''));
        $fromDate = $this->toDate($request->get('from', ''));
        $toDate = $this->toDate($request->get('to', ''));
        $appNo = trim((string) $request->get('app_no', ''));
        $type = (int) $request->get('type', 0);
        $category = trim((string) $request->get('category', ''));
        $attachment = (int) $request->get('attachment', 0);
        $filterBy = trim((string) $request->get('filter_by', ''));

        if ($status !== '' && $status !== 'ALL') {
            $map = ['NEW' => 'D', 'PENDING' => 'P', 'APPROVED' => 'A', 'REJECTED' => 'R', 'RETURNED' => 'C'];
            $q->where('approve_status', $map[$status] ?? $status);
        }

        if ($fromDate) {
            $q->whereDate('leave_from', '>=', $fromDate);
        }
        if ($toDate) {
            $q->whereDate('leave_to', '<=', $toDate);
        }

        if ($appNo !== '') {
            $q->where('leave_application_no', 'like', '%' . $appNo . '%');
        }
        if ($type > 0) {
            $q->where('type_id', $type);
        }
        if ($category !== '') {
            $q->where('leave_category', $category);
        }
        if ($attachment === 1) {
            $q->whereNotNull('file')->where('file', '!=', '');
        } elseif ($attachment === 2) {
            $q->where(function ($w) {
                $w->whereNull('file')->orWhere('file', '');
            });
        }
        if ($filterBy !== '') {
            if ($filterBy === 'today') {
                $q->whereDate('leave_from', \Carbon\Carbon::today());
            } elseif ($filterBy === 'this_week') {
                $q->whereBetween('leave_from', [\Carbon\Carbon::now()->startOfWeek(\Carbon\Carbon::SUNDAY), \Carbon\Carbon::now()->endOfWeek(\Carbon\Carbon::SATURDAY)]);
            } elseif ($filterBy === 'last_week') {
                $q->whereBetween('leave_from', [\Carbon\Carbon::now()->subWeek()->startOfWeek(\Carbon\Carbon::SUNDAY), \Carbon\Carbon::now()->subWeek()->endOfWeek(\Carbon\Carbon::SATURDAY)]);
            } elseif ($filterBy === 'this_month') {
                $q->whereBetween('leave_from', [\Carbon\Carbon::now()->startOfMonth(), \Carbon\Carbon::now()->endOfMonth()]);
            } elseif ($filterBy === 'last_month') {
                $q->whereBetween('leave_from', [\Carbon\Carbon::now()->subMonth()->startOfMonth(), \Carbon\Carbon::now()->subMonth()->endOfMonth()]);
            } elseif ($filterBy === 'this_quarter') {
                $q->whereBetween('leave_from', [\Carbon\Carbon::now()->firstOfQuarter(), \Carbon\Carbon::now()->lastOfQuarter()]);
            } elseif ($filterBy === 'pre_quarter') {
                $q->whereBetween('leave_from', [\Carbon\Carbon::now()->subQuarter()->firstOfQuarter(), \Carbon\Carbon::now()->subQuarter()->lastOfQuarter()]);
            } elseif ($filterBy === 'this_year') {
                $q->whereBetween('leave_from', [\Carbon\Carbon::now()->startOfYear(), \Carbon\Carbon::now()->endOfYear()]);
            } elseif ($filterBy === 'last_year') {
                $q->whereBetween('leave_from', [\Carbon\Carbon::now()->subYear()->startOfYear(), \Carbon\Carbon::now()->subYear()->endOfYear()]);
            }
        }

        if ($qText !== '') {
            $q->where(function ($w) use ($qText) {
                $w->where('reason', 'like', '%' . $qText . '%')
                    ->orWhere('leave_application_no', 'like', '%' . $qText . '%')
                    ->orWhere('id', (int) $qText)
                    ->orWhereHas('staffs', function ($sw) use ($qText) {
                        $sw->where('full_name', 'like', '%' . $qText . '%')
                            ->orWhere('email', 'like', '%' . $qText . '%')
                            ->orWhere('user_id', 'like', '%' . $qText . '%');
                    });
            });
        }
    }

    private function canActOnStep(HrmsApproverChainStep $step)
    {
        $auth = Auth::user();
        if ($step->status !== 'P') {
            return false;
        }
        if ((int) $step->approver_id !== (int) $auth->id && !in_array((int) $auth->role_id, [1, 2], true)) {
            return false;
        }

        $previousPending = HrmsApproverChainStep::where('approver_chain_id', $step->approver_chain_id)
            ->where('step_no', '<', $step->step_no)
            ->where('status', '!=', 'A')
            ->exists();

        return !$previousPending;
    }

    private function canViewInTrack($leave, $auth)
    {
        if (!$leave || $leave->approve_status === 'D' || !$leave->chain) {
            return false;
        }
        if (in_array((int) $auth->role_id, [1, 2], true)) {
            return true;
        }

        return $leave->chain->steps->contains(function ($step) use ($auth) {
            return (int) $step->approver_id === (int) $auth->id;
        });
    }

    private function toDate($date)
    {
        $date = trim((string) $date);
        if ($date === '') {
            return null;
        }

        if (Str::contains($date, '/')) {
            try {
                return Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) ? $date : null;
    }

    private function nextLeaveApplicationNo($companyId = null)
    {
        $companyId = $companyId ?: (Auth::user()->company_id ?? 1);
        $code = DB::table('sys_company')->where('id', $companyId)->value('other_code') ?: 'D';
        $prefix = 'LR' . $code . '-';

        $latest = DB::table('sm_leave_requests')
            ->where('leave_application_no', 'like', $prefix . '%')
            ->where('company_id', $companyId)
            ->pluck('leave_application_no');

        $max = 1000;
        foreach ($latest as $number) {
            if (preg_match('/(\d+)$/', (string) $number, $m)) {
                $max = max($max, (int) $m[1]);
            }
        }

        return $prefix . ($max + 1);
    }
}

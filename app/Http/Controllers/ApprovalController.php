<?php

namespace App\Http\Controllers;

use App\HrmsApproverChain;
use App\HrmsApproverChainStep;
use App\SmLeaveRequest;
use App\SmStaff;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprovalController extends Controller
{
    //
  public function index(Request $r)
{
    $userId = (int) Auth::id();

    $status = strtoupper((string) $r->get('status', ''));
    $qText  = trim((string) $r->get('q', ''));
    $from   = trim((string) $r->get('from', ''));
    $to     = trim((string) $r->get('to', ''));

    $fromDate = $this->toDate($from);
    $toDate   = $this->toDate($to);

    $q = SmLeaveRequest::query()
        ->with([
            'type',
            'staffs' => function ($s) {
                $s->select('user_id', 'first_name', 'last_name', 'email', 'designation_id', 'department_id');
            },
            'staffs.designations:id,title',
            'staffs.departments:id,name',
        ])
        // ✅ Show leaves where this user is approver (no status filter)
        ->whereHas('chain.steps', function ($sq) use ($userId) {
            $sq->where('approver_id', $userId);
        });

    if ($status !== '' && $status !== 'ALL') {
        $map  = ['PENDING' => 'P', 'APPROVED' => 'A', 'REJECTED' => 'R', 'CANCELLED' => 'C'];
        $code = $map[$status] ?? $status;
        $q->where('approve_status', $code);
    }

    if ($fromDate) $q->whereDate('leave_from', '>=', $fromDate);
    if ($toDate)   $q->whereDate('leave_to',   '<=', $toDate);

    if ($qText !== '') {
        $term = $qText;
        $q->where(function ($w) use ($term) {
            $w->where('reason', 'like', '%'.$term.'%')
              ->orWhere('id', (int) $term)
              ->orWhereHas('staffs', function ($sw) use ($term) {
                  $sw->where('full_name', 'like', '%'.$term.'%')
                     ->orWhere('email', 'like', '%'.$term.'%')
                     ->orWhere('user_id', 'like', '%'.$term.'%');
              });
        });
    }

    $leaves = $q->orderBy('id', 'desc')
        ->paginate(12)
        ->appends($r->query());

    // Details panel
    $activeId = (int) $r->get('active', 0);
    $selectedLeave = null;

    if ($activeId) {
        $selectedLeave = SmLeaveRequest::with([
            'type',
            'staffs' => function ($s) {
                $s->select('user_id', 'first_name', 'last_name', 'email', 'designation_id', 'department_id');
            },
            'staffs.designations:id,title',
            'staffs.departments:id,name',
            'chain.steps' => function ($s) { $s->orderBy('step_no'); }
        ])->find($activeId);
    }

    if (!$selectedLeave && $leaves->count() > 0) {
        $selectedLeave = $leaves->first();
    }

    return view('backEnd.approvals.inbox', compact('leaves', 'selectedLeave'));
}


    public function show(Request $r, $id)
    {
        $userId = (int) Auth::id();

        $leave = SmLeaveRequest::with([
            'type',
            'staffs.designations',
            'staffs.departments',
            'chain.steps' => function ($s) {
                $s->orderBy('step_no');
            }
        ])->findOrFail((int) $id);

        // ✅ approver_id based current step
        $currentStep = null;
        $currentRole = null;

        if ($leave->chain && $leave->chain->steps) {
            foreach ($leave->chain->steps as $step) {
                if ($step->status === 'P' && (int)($step->approver_id ?: 0) === $userId) {
                    $currentStep = $step;
                    $currentRole = $step->role;
                    break;
                }
            }
        }

        return view('backEnd.approvals._details', array(
            'leave' => $leave,
            'currentStep' => $currentStep,
            'currentRole' => $currentRole,
        ))->render();
    }

    // dd/mm/YYYY or YYYY-mm-dd -> Y-m-d
    private function toDate($d)
    {
        $d = trim((string) $d);
        if ($d === '') return null;

        if (\Illuminate\Support\Str::contains($d, '/')) {
            try {
                return Carbon::createFromFormat('d/m/Y', $d)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $d)) {
            return $d;
        }
        return null;
    }

    public function action(Request $r)
{
    $step = \App\HrmsApproverChainStep::findOrFail($r->input('step_id'));

    // Detect which level submitted
    $role = strtolower($r->input('actor_role'));

    // Common update
    $step->comment = $r->input("{$role}_remark") ?? '';
    $step->acted_at = now();

    if (strpos($role, 'report') !== false) {
        $step->fill($r->only([
            'l1_workload', 'l1_coverage', 'l1_eligibility', 'l1_duration_ok',
            'l1_notice_compliance', 'l1_decision', 'l1_remark'
        ]));
        $step->status = $r->l1_decision === 'Approve' ? 'A' : ($r->l1_decision === 'Reject' ? 'R' : 'S');
    }
    elseif (strpos($role, 'finance') !== false) {
        $step->fill($r->only([
            'l2_balance', 'l2_unpaid', 'l2_encash', 'l2_cost',
            'l2_policy', 'l2_decision', 'l2_remark'
        ]));
        $step->status = $r->l2_decision === 'Approve' ? 'A' : ($r->l2_decision === 'Reject' ? 'R' : 'S');
    }
    else { // HR
        $step->fill($r->only([
            'l3_docs', 'l3_policy', 'l3_system', 'l3_payroll',
            'l3_legal', 'l3_decision', 'l3_remark'
        ]));
        $step->status = $r->l3_decision === 'Approve' ? 'A' : 'R';
    }

    $step->save();

    return redirect()->back()->with('success', 'Action recorded successfully.');
}



    
}

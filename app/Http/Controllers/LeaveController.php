<?php

namespace App\Http\Controllers;

use App\HrmsApproverChain;
use App\HrmsApproverChainStep;
use App\SmLeaveRequest;
use App\SmLeaveType;
use App\SmStaff;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LeaveController extends Controller
{
    //


    public function index(Request $request)
    {
        $staffId = $this->getStaffId();

        $q = $request->input('q');
        $status = $request->input('status');
        $typeId = $request->input('type_id');
        $from = $request->input('from');
        $to = $request->input('to');

        $leaves = SmLeaveRequest::where('staff_id', $staffId)
            ->when($q, function ($query) use ($q) {
                $query->where('id', $q)
                    ->orWhere('reason', 'like', "%$q%");
            })
            ->when($status, function ($query) use ($status) {
                $query->where('approve_status', $status);
            })
            ->when($typeId, function ($query) use ($typeId) {
                $query->where('type_id', $typeId);
            })
            ->when($from, function ($query) use ($from) {
                $query->whereDate('leave_from', '>=', $from);
            })
            ->when($to, function ($query) use ($to) {
                $query->whereDate('leave_to', '<=', $to);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $selectedLeave = null;
        if ($request->filled('active')) {
            $selectedLeave = $leaves->getCollection()
                ->first(function ($item) use ($request) {
                    return $item->id == $request->active;
                });
        }
        if (!$selectedLeave) {
            $selectedLeave = $leaves->first();
        }

        return view('backEnd.employee.leaves.index', compact('leaves', 'selectedLeave'));
    }


    public function create()
    {
        $authUser = Auth::user();
        $leaveTypes = SmLeaveType::where('is_active', 1)->orderBy('name')->get();
        $reportingManager = User::where('id', '!=', $authUser->id)   // exclude current auth user
            ->get();
        $employees = SmStaff::where('active_status', 1)->orderBy('full_name')->get();
        $authStaff = SmStaff::where('user_id', $authUser->id)->first();
        $leaveApplicationNo = $this->nextLeaveApplicationNo(optional($authStaff)->company_id ?: ($authUser->company_id ?? null));

        return view('backEnd.employee.leaves.create', compact('authUser', 'reportingManager', 'leaveTypes', 'employees', 'leaveApplicationNo'));
    }


    private function getStaffId()
    {
        // Adjust to your mapping. If you have an SmStaff table with user_id, use that.
        // Example fallback: assume staff_id == users.id
        return (int) Auth::id();
    }



    public function show($id)
    {
        $leave = \App\SmLeaveRequest::where('staff_id', Auth::id())->findOrFail($id);

        $chain = HrmsApproverChain::where('leave_request_id', $leave->id)
            ->with([
                'steps' => function ($q) {
                    $q->orderBy('step_no')->with(['approver.user']);
                }
            ])->first();

        $flow = [];
        if ($chain) {
            $flow = $chain->steps->map(function ($s) {
                // Build display name safely (full_name -> first+last -> user->name)
                $name = 'Unassigned';
                if ($s->approver) {
                    $full = trim((string) ($s->approver->full_name ?? ''));
                    if ($full === '') {
                        $first = trim((string) ($s->approver->first_name ?? ''));
                        $last = trim((string) ($s->approver->last_name ?? ''));
                        $full = trim($first . ' ' . $last);
                    }
                    if ($full === '' && $s->approver->user) {
                        $full = trim((string) ($s->approver->user->name ?? ''));
                    }
                    if ($full !== '')
                        $name = $full;
                }

                return [
                    'name' => $name,                 // << yeh name ab role ke person ka hai
                    'role' => $s->role,              // Reporting Manager / HR / Finance
                    'status' => ['P' => 'Pending', 'A' => 'Approved', 'R' => 'Rejected', 'S' => 'Skipped'][$s->status] ?? 'Pending',
                    'acted_at' => $s->acted_at ? \Carbon\Carbon::parse($s->acted_at)->format('d M Y, h:i A') : null,
                    'comment' => $s->comment,
                ];
            })->toArray();
        }

        return view('backEnd.employee.leaves._details', compact('leave', 'flow'));
    }


    public function store(Request $r)
    {
        $tz = config('app.timezone', 'Asia/Kolkata');
        $action = $r->input('action_type') === 'draft' ? 'draft' : 'submit';

        // --- Helpers ---
        $getTypeCode = function (int $typeId) {
            return DB::table('sm_leave_types')->where('id', $typeId)->value('code');
        };

        $resolveChain = function (?int $rmId): array {
            $hrId = 98;
            $finId = 27;
            $steps = [];
            if (!empty($rmId))
                $steps[] = ['role' => 'Reporting Manager', 'uid' => (int) $rmId];
            if ($hrId > 0)
                $steps[] = ['role' => 'HR', 'uid' => $hrId, 'status' => 'S'];
            if ($finId > 0)
                $steps[] = ['role' => 'Management', 'uid' => $finId, 'status' => 'S'];
            return $steps;
        };

        // --- 1) Validate ---
        $todayLocal = Carbon::now($tz)->format('d/m/Y');

        // base rules
        $rules = [
            'type_id' => 'required|integer',
            'reporting_manager_id' => 'nullable|integer',
            'leave_from' => 'required|date_format:d/m/Y|after_or_equal:' . $todayLocal,
            'leave_to' => 'required|date_format:d/m/Y',
            'return_to_work_date' => 'nullable|date_format:d/m/Y',
            'leave_category' => 'required|in:Paid,Unpaid',
            'urgency_level' => 'required|in:Normal,Urgent,Critical',
            'nature_of_leave' => 'required|in:Planned,Emergency',
            'notice_period' => 'nullable|in:Yes,No',
            'availability_during_leave' => 'required|in:Available,Limited,Not Available',
            'half_session' => 'nullable|in:FIRST_HALF,SECOND_HALF,NONE',
            'contact_number_during_leave' => 'nullable|string|max:50',
            'email_during_leave' => 'nullable|email|max:191',
            'handover_required' => 'nullable|in:Yes,No',
            'handover_employee_id' => 'required_if:handover_required,Yes|nullable|integer',
            'pending_tasks' => 'nullable|in:Yes,No',
            'client_responsibilities' => 'nullable|string',
            'access_transfer_required' => 'nullable|in:Yes,No',
            'handover_completion_confirmation' => 'nullable|in:Yes,No',
            'manager_verification_of_handover' => 'nullable|in:Yes,No',
            'handover_additional_remarks' => 'nullable|string',
            'leaving_country' => 'nullable|in:Yes,No',
            'destination_country' => 'required_if:leaving_country,Yes|nullable|string|max:100',
            'departure_date' => 'required_if:leaving_country,Yes|nullable|date_format:d/m/Y',
            'expected_return_date' => 'required_if:leaving_country,Yes|nullable|date_format:d/m/Y',
            'travel_ticket_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'accommodation_address' => 'nullable|string',
            'emergency_contact_person' => 'nullable|string|max:100',
            'emergency_contact_number' => 'nullable|string|max:50',
            'emergency_contact_relationship' => 'nullable|string|max:80',
            'reason' => 'required|string',
            'note' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'handover_to' => 'nullable|string|max:191',
        ];
        if ($action === 'submit') {
            $rules['declaration_info_confirmed'] = 'accepted';
            $rules['declaration_handover_confirmed'] = 'accepted';
            $rules['declaration_policy_agreed'] = 'accepted';
        }

        // emergency contacts (loose optional validation)
        for ($i = 1; $i <= 3; $i++) {
            $rules["emergency_name_$i"] = 'nullable|string|max:100';
            $rules["emergency_relation_$i"] = 'nullable|string|max:50';
            $rules["emergency_phone_$i"] = 'nullable|string|max:20';
            $rules["emergency_country_$i"] = 'nullable|string|max:60';
        }
        $this->validate($r, $rules);

        // --- 2) Dates ---
        $from = Carbon::createFromFormat('d/m/Y', $r->input('leave_from'), $tz)->startOfDay();
        $to = Carbon::createFromFormat('d/m/Y', $r->input('leave_to'), $tz)->startOfDay();

        // --- 3) Leave type & half-day ---
        $typeId = (int) $r->input('type_id');
        $typeCode = strtoupper((string) $getTypeCode($typeId));
        $isHalf = in_array($typeCode, ['HD', 'EL'], true);

        if ($isHalf) {
            $to = $from->copy();
            $days = 0.5;
        } else {
            if ($to->lt($from)) {
                return back()->withErrors(['leave_to' => 'Leave To must be after or equal to Leave From.'])->withInput();
            }
            $days = $from->diffInDays($to) + 1;
        }
        $halfInput = $r->input('half_session');
        $halfSession = $halfInput && $halfInput !== 'NONE' ? $halfInput : ($isHalf ? 'SECOND_HALF' : null);
        if ($halfSession) {
            $to = $from->copy();
            $days = 0.5;
        }
        $returnToWork = $r->filled('return_to_work_date')
            ? Carbon::createFromFormat('d/m/Y', $r->input('return_to_work_date'), $tz)->startOfDay()
            : $to->copy()->addDay();
        $departureDate = $r->filled('departure_date') ? Carbon::createFromFormat('d/m/Y', $r->input('departure_date'), $tz)->startOfDay() : null;
        $expectedReturnDate = $r->filled('expected_return_date') ? Carbon::createFromFormat('d/m/Y', $r->input('expected_return_date'), $tz)->startOfDay() : null;

        // --- 4) File upload (optional) ---
        $filePath = null;
        if ($r->hasFile('file')) {
            $filePath = $r->file('file')->store('leaves', 'public');
        }
        $travelTicketPath = null;
        if ($r->hasFile('travel_ticket_file')) {
            $travelTicketPath = $r->file('travel_ticket_file')->store('leaves', 'public');
        }

        // --- 5) Resolve approver chain automatically from employee setup ---
        $rmId = (int) $this->getReportingManagerId(Auth::id());
        $chainSteps = $resolveChain($rmId);
        if ($action === 'submit' && empty($chainSteps)) {
            return back()->withErrors(['reporting_manager_id' => 'Approver chain is not configured.'])->withInput();
        }

        $handoverEmployee = $r->filled('handover_employee_id') ? SmStaff::find((int) $r->input('handover_employee_id')) : null;

        // --- 5.1) Build emergency contacts array ---
        $contacts = [];
        for ($i = 1; $i <= 3; $i++) {
            $name = trim((string) $r->input("emergency_name_$i"));
            $relation = trim((string) $r->input("emergency_relation_$i"));
            $phone = trim((string) $r->input("emergency_phone_$i"));
            $country = trim((string) $r->input("emergency_country_$i"));

            // skip fully empty row
            if ($name === '' && $relation === '' && $phone === '' && $country === '') {
                continue;
            }

            $contacts[] = [
                'name' => $name,
                'relation' => $relation,
                'phone' => $phone,
                'country' => $country,
            ];
        }

        // --- 6) Transaction: create leave + approver chain ---
        $leave = DB::transaction(function () use ($r, $from, $to, $days, $halfSession, $filePath, $travelTicketPath, $typeId, $chainSteps, $tz, $rmId, $contacts, $action, $returnToWork, $departureDate, $expectedReturnDate, $handoverEmployee) {
            $now = Carbon::now($tz);

            $companyId = data_get(session('logged_session_data'), 'company_id')
                ?? (Auth::user()->company_id ?? null);

            $leave = \App\SmLeaveRequest::create([
                'leave_application_no' => $this->nextLeaveApplicationNo($companyId),
                'leave_define_id' => null,
                'staff_id' => Auth::id(),
                'role_id' => Auth::user()->role_id ?? null,

                'apply_date' => $now->toDateString(),
                'leave_year' => (int) $now->year,

                'type_id' => $typeId,
                'reporting_manager_id' => $rmId ?: 0,

                'leave_from' => $from->toDateString(),
                'leave_to' => $to->toDateString(),
                'return_to_work_date' => $returnToWork->toDateString(),
                'days' => $days,
                'is_half_day' => $halfSession ? 1 : 0,
                'half_session' => $halfSession,
                'leave_category' => $r->input('leave_category'),
                'urgency_level' => $r->input('urgency_level'),
                'nature_of_leave' => $r->input('nature_of_leave'),
                'notice_period' => $r->input('notice_period', 'No'),
                'availability_during_leave' => $r->input('availability_during_leave'),
                'contact_number_during_leave' => $r->input('contact_number_during_leave'),
                'email_during_leave' => $r->input('email_during_leave'),
                'handover_required' => $r->input('handover_required', 'No'),
                'handover_employee_id' => $r->input('handover_required') === 'Yes' ? $r->input('handover_employee_id') : null,
                'pending_tasks' => $r->input('handover_required') === 'Yes' ? $r->input('pending_tasks') : null,
                'client_responsibilities' => $r->input('handover_required') === 'Yes' ? $r->input('client_responsibilities') : null,
                'access_transfer_required' => $r->input('handover_required') === 'Yes' ? $r->input('access_transfer_required') : null,
                'handover_completion_confirmation' => $r->input('handover_required') === 'Yes' ? $r->input('handover_completion_confirmation') : null,
                'manager_verification_of_handover' => $r->input('handover_required') === 'Yes' ? $r->input('manager_verification_of_handover') : null,
                'handover_additional_remarks' => $r->input('handover_required') === 'Yes' ? $r->input('handover_additional_remarks') : null,

                'company_id' => $companyId,
                'handover_to' => $r->input('handover_required') === 'Yes' && $handoverEmployee
                    ? ($handoverEmployee->full_name ?: trim($handoverEmployee->first_name . ' ' . $handoverEmployee->last_name))
                    : '',

                'reason' => $r->input('reason'),
                'note' => $r->input('note'),
                'file' => $filePath,
                'leaving_country' => $r->input('leaving_country', 'No'),
                'destination_country' => $r->input('leaving_country') === 'Yes' ? $r->input('destination_country') : null,
                'departure_date' => $r->input('leaving_country') === 'Yes' && $departureDate ? $departureDate->toDateString() : null,
                'expected_return_date' => $r->input('leaving_country') === 'Yes' && $expectedReturnDate ? $expectedReturnDate->toDateString() : null,
                'travel_ticket_file' => $r->input('leaving_country') === 'Yes' ? $travelTicketPath : null,
                'accommodation_address' => $r->input('accommodation_address'),
                'emergency_contact_person' => $r->input('emergency_contact_person'),
                'emergency_contact_number' => $r->input('emergency_contact_number'),
                'emergency_contact_relationship' => $r->input('emergency_contact_relationship'),

                'emergency_contacts' => $contacts ?: null, // <-- save JSON

                'approve_status' => $action === 'draft' ? 'D' : 'P',
                'submitted_at' => $action === 'submit' ? $now : null,
                'declaration_info_confirmed' => $r->filled('declaration_info_confirmed') ? 1 : 0,
                'declaration_handover_confirmed' => $r->filled('declaration_handover_confirmed') ? 1 : 0,
                'declaration_policy_agreed' => $r->filled('declaration_policy_agreed') ? 1 : 0,
                'declaration_accepted_at' => $r->filled('declaration_info_confirmed') ? $now : null,
                'declaration_accepted_by' => $r->filled('declaration_info_confirmed') ? Auth::id() : null,
                'active_status' => 1,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            if ($action === 'submit') {
                $chain = \App\HrmsApproverChain::create([
                    'leave_request_id' => $leave->id,
                    'staff_id' => Auth::id(),
                    'overall_status' => 'P',
                ]);

                foreach ($chainSteps as $i => $step) {
                    \App\HrmsApproverChainStep::create([
                        'approver_chain_id' => $chain->id,
                        'step_no' => $i + 1,
                        'role' => $step['role'],
                        'approver_id' => $step['uid'],
                        'status' => $step['status'] ?? ($i === 0 ? 'P' : 'S'),
                    ]);
                }
            }

            return $leave;
        });

        return redirect()
            ->route('approvals.inbox', ['active' => $leave->id])
            ->with('success', $action === 'draft' ? 'Leave draft saved successfully.' : 'Leave submitted and approval flow created.');
    }




    // --- Helpers ---

    /**
     * Get leave type code by id (e.g. HD, EL).
     */
    protected function getTypeCode(int $typeId)
    {
        return DB::table('sm_leave_types')->where('id', $typeId)->value('code');
    }

    /**
     * Build approval user ids from employee hierarchy plus static HR/ACC.
     */
    protected function buildChainForNewLeave(int $staffId, ?int $rmIdFromForm): array
    {
        $rmId = $rmIdFromForm ?: (int) $this->getReportingManagerId($staffId);
        $hrId = 7;   // static default
        $accId = 12;  // static default

        $raw = [(int) $rmId, (int) $hrId, (int) $accId];

        // filter + dedupe
        $chain = [];
        foreach ($raw as $uid) {
            if ($uid > 0 && !in_array($uid, $chain, true))
                $chain[] = $uid;
        }
        return $chain;
    }

    /**
     * Position-based labels for approvals_json (no DB dependency).
     */
    protected function seedApprovalsByIndex(array $chain): array
    {
        $labels = ['RM', 'HR', 'ACC']; // extend if you add more levels
        $out = [];
        foreach ($chain as $i => $uid) {
            $out[] = [
                'uid' => (int) $uid,
                'role' => isset($labels[$i]) ? $labels[$i] : null,
                'status' => 'pending',   // pending|approved|rejected
                'acted_at' => null,
                'comment' => null,
                'index' => $i,
            ];
        }
        return $out;
    }

    protected function getReportingManagerId(int $staffId): ?int
    {
        $staff = SmStaff::where('user_id', $staffId)
            ->orWhere('id', $staffId)
            ->first();

        if ($staff && !empty($staff->reporting_manager)) {
            foreach ($this->parseApproverValues($staff->reporting_manager) as $value) {
                $resolved = $this->resolveApproverUserId($value);
                if ($resolved && $resolved !== (int) $staffId) {
                    return $resolved;
                }
            }
        }

        $roleManager = User::where('role_id', 8)
            ->where('id', '!=', $staffId)
            ->orderBy('id')
            ->value('id');
        if ($roleManager) {
            return (int) $roleManager;
        }

        $adminManager = User::whereIn('role_id', [1, 2])
            ->where('id', '!=', $staffId)
            ->orderBy('id')
            ->value('id');

        return $adminManager ? (int) $adminManager : (int) $staffId;
    }

    private function parseApproverValues($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        $decoded = json_decode((string) $value, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        return array_filter(array_map('trim', explode(',', (string) $value)), function ($item) {
            return $item !== '';
        });
    }

    private function resolveApproverUserId($value): ?int
    {
        $id = (int) $value;
        if ($id <= 0) {
            return null;
        }

        if (User::where('id', $id)->exists()) {
            return $id;
        }

        $staffUserId = SmStaff::where('id', $id)->value('user_id');
        if ($staffUserId) {
            return (int) $staffUserId;
        }

        $roleUserId = User::where('role_id', $id)->orderBy('id')->value('id');
        return $roleUserId ? (int) $roleUserId : null;
    }

    private function nextLeaveApplicationNo($companyId = null)
    {
        if (!$companyId) {
            $companyId = Auth::user()->company_id ?? 1;
        }
        $code2 = DB::table('sys_company')->where('id', $companyId)->value('other_code') ?: 'D';
        $prefix = 'LR' . $code2 . '-';

        $latest = DB::table('sm_leave_requests')
            ->where('leave_application_no', 'like', $prefix . '%')
            ->where('company_id', $companyId)
            ->get(['leave_application_no']);

        $maxNum = 1000;
        foreach ($latest as $row) {
            preg_match('/(\d+)$/', (string) $row->leave_application_no, $match);
            $num = isset($match[1]) ? (int) $match[1] : 0;
            if ($num > $maxNum) {
                $maxNum = $num;
            }
        }
        return $prefix . ($maxNum + 1);
    }

    public function edit(\App\SmLeaveRequest $leave)
    {
        $authUser = Auth::user();
        $reportingManager = \App\User::where('company_id', $authUser->company_id)
            ->where('id', '!=', $authUser->id)
            ->select('id', 'full_name')
            ->get();

        $leaveTypes = DB::table('sm_leave_types')->select('id', 'name', 'code')->get();

        return view('backEnd.employee.leaves.edit', compact('leave', 'authUser', 'reportingManager', 'leaveTypes'));
    }



    public function update(Request $r, \App\SmLeaveRequest $leave)
    {
        $tz = config('app.timezone', 'Asia/Kolkata');
        $action = $r->input('action_type') === 'submit' ? 'submit' : 'draft';

        $rules = [
            'type_id' => 'required|integer',
            'reporting_manager_id' => 'nullable|integer',
            'leave_from' => 'required|date_format:d/m/Y',
            'leave_to' => 'required|date_format:d/m/Y',
            'return_to_work_date' => 'nullable|date_format:d/m/Y',
            'leave_category' => 'required|in:Paid,Unpaid',
            'urgency_level' => 'required|in:Normal,Urgent,Critical',
            'nature_of_leave' => 'required|in:Planned,Emergency',
            'notice_period' => 'nullable|in:Yes,No',
            'availability_during_leave' => 'required|in:Available,Limited,Not Available',
            'half_session' => 'nullable|in:FIRST_HALF,SECOND_HALF,NONE',
            'contact_number_during_leave' => 'nullable|string|max:50',
            'email_during_leave' => 'nullable|email|max:191',
            'handover_required' => 'nullable|in:Yes,No',
            'handover_employee_id' => 'required_if:handover_required,Yes|nullable|integer',
            'pending_tasks' => 'nullable|in:Yes,No',
            'client_responsibilities' => 'nullable|string',
            'access_transfer_required' => 'nullable|in:Yes,No',
            'handover_completion_confirmation' => 'nullable|in:Yes,No',
            'manager_verification_of_handover' => 'nullable|in:Yes,No',
            'handover_additional_remarks' => 'nullable|string',
            'leaving_country' => 'nullable|in:Yes,No',
            'destination_country' => 'required_if:leaving_country,Yes|nullable|string|max:100',
            'departure_date' => 'required_if:leaving_country,Yes|nullable|date_format:d/m/Y',
            'expected_return_date' => 'required_if:leaving_country,Yes|nullable|date_format:d/m/Y',
            'travel_ticket_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'accommodation_address' => 'nullable|string',
            'emergency_contact_person' => 'nullable|string|max:100',
            'emergency_contact_number' => 'nullable|string|max:50',
            'emergency_contact_relationship' => 'nullable|string|max:80',
            'reason' => 'required|string',
            'note' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'handover_to' => 'nullable|string|max:191',
        ];
        if ($action === 'submit') {
            $rules['declaration_info_confirmed'] = 'accepted';
            $rules['declaration_handover_confirmed'] = 'accepted';
            $rules['declaration_policy_agreed'] = 'accepted';
        }
        $this->validate($r, $rules);

        $from = Carbon::createFromFormat('d/m/Y', $r->input('leave_from'), $tz)->startOfDay();
        $to = Carbon::createFromFormat('d/m/Y', $r->input('leave_to'), $tz)->startOfDay();
        $typeId = (int) $r->input('type_id');
        $typeCode = strtoupper((string) $this->getTypeCode($typeId));
        $isHalf = in_array($typeCode, ['HD', 'EL'], true);

        if ($isHalf) {
            $to = $from->copy();
            $days = 0.5;
        } else {
            if ($to->lt($from)) {
                return back()->withErrors(['leave_to' => 'Leave To must be after or equal to Leave From.'])->withInput();
            }
            $days = $from->diffInDays($to) + 1;
        }

        $halfInput = $r->input('half_session');
        $halfSession = $halfInput && $halfInput !== 'NONE' ? $halfInput : ($isHalf ? 'SECOND_HALF' : null);
        if ($halfSession) {
            $to = $from->copy();
            $days = 0.5;
        }
        $returnToWork = $r->filled('return_to_work_date')
            ? Carbon::createFromFormat('d/m/Y', $r->input('return_to_work_date'), $tz)->startOfDay()
            : $to->copy()->addDay();
        $departureDate = $r->filled('departure_date') ? Carbon::createFromFormat('d/m/Y', $r->input('departure_date'), $tz)->startOfDay() : null;
        $expectedReturnDate = $r->filled('expected_return_date') ? Carbon::createFromFormat('d/m/Y', $r->input('expected_return_date'), $tz)->startOfDay() : null;

        $filePath = $leave->file;
        if ($r->hasFile('file')) {
            if ($leave->file && Storage::disk('public')->exists($leave->file)) {
                Storage::disk('public')->delete($leave->file);
            }
            $filePath = $r->file('file')->store('leaves', 'public');
        }

        $travelTicketPath = $leave->travel_ticket_file;
        if ($r->hasFile('travel_ticket_file')) {
            if ($leave->travel_ticket_file && Storage::disk('public')->exists($leave->travel_ticket_file)) {
                Storage::disk('public')->delete($leave->travel_ticket_file);
            }
            $travelTicketPath = $r->file('travel_ticket_file')->store('leaves', 'public');
        }

        $rmId = (int) $this->getReportingManagerId(Auth::id());
        $handoverEmployee = $r->filled('handover_employee_id') ? SmStaff::find((int) $r->input('handover_employee_id')) : null;

        DB::transaction(function () use ($r, $leave, $from, $to, $days, $halfSession, $typeId, $filePath, $travelTicketPath, $action, $returnToWork, $departureDate, $expectedReturnDate, $rmId, $handoverEmployee, $tz) {
            $now = Carbon::now($tz);
            $wasDraft = $leave->approve_status === 'D';

            $leave->update([
                'type_id' => $typeId,
                'reporting_manager_id' => $rmId ?: 0,
                'leave_from' => $from->toDateString(),
                'leave_to' => $to->toDateString(),
                'return_to_work_date' => $returnToWork->toDateString(),
                'days' => $days,
                'is_half_day' => $halfSession ? 1 : 0,
                'half_session' => $halfSession,
                'leave_category' => $r->input('leave_category'),
                'urgency_level' => $r->input('urgency_level'),
                'nature_of_leave' => $r->input('nature_of_leave'),
                'notice_period' => $r->input('notice_period', 'No'),
                'availability_during_leave' => $r->input('availability_during_leave'),
                'contact_number_during_leave' => $r->input('contact_number_during_leave'),
                'email_during_leave' => $r->input('email_during_leave'),
                'handover_required' => $r->input('handover_required', 'No'),
                'handover_employee_id' => $r->input('handover_required') === 'Yes' ? $r->input('handover_employee_id') : null,
                'pending_tasks' => $r->input('handover_required') === 'Yes' ? $r->input('pending_tasks') : null,
                'client_responsibilities' => $r->input('handover_required') === 'Yes' ? $r->input('client_responsibilities') : null,
                'access_transfer_required' => $r->input('handover_required') === 'Yes' ? $r->input('access_transfer_required') : null,
                'handover_completion_confirmation' => $r->input('handover_required') === 'Yes' ? $r->input('handover_completion_confirmation') : null,
                'manager_verification_of_handover' => $r->input('handover_required') === 'Yes' ? $r->input('manager_verification_of_handover') : null,
                'handover_additional_remarks' => $r->input('handover_required') === 'Yes' ? $r->input('handover_additional_remarks') : null,
                'handover_to' => $r->input('handover_required') === 'Yes' && $handoverEmployee
                    ? ($handoverEmployee->full_name ?: trim($handoverEmployee->first_name . ' ' . $handoverEmployee->last_name))
                    : '',
                'reason' => $r->input('reason'),
                'note' => $r->input('note'),
                'file' => $filePath,
                'leaving_country' => $r->input('leaving_country', 'No'),
                'destination_country' => $r->input('leaving_country') === 'Yes' ? $r->input('destination_country') : null,
                'departure_date' => $r->input('leaving_country') === 'Yes' && $departureDate ? $departureDate->toDateString() : null,
                'expected_return_date' => $r->input('leaving_country') === 'Yes' && $expectedReturnDate ? $expectedReturnDate->toDateString() : null,
                'travel_ticket_file' => $r->input('leaving_country') === 'Yes' ? $travelTicketPath : null,
                'accommodation_address' => $r->input('accommodation_address'),
                'emergency_contact_person' => $r->input('emergency_contact_person'),
                'emergency_contact_number' => $r->input('emergency_contact_number'),
                'emergency_contact_relationship' => $r->input('emergency_contact_relationship'),
                'approve_status' => $action === 'submit' ? 'P' : $leave->approve_status,
                'submitted_at' => $action === 'submit' ? ($leave->submitted_at ?: $now) : $leave->submitted_at,
                'declaration_info_confirmed' => $r->filled('declaration_info_confirmed') ? 1 : 0,
                'declaration_handover_confirmed' => $r->filled('declaration_handover_confirmed') ? 1 : 0,
                'declaration_policy_agreed' => $r->filled('declaration_policy_agreed') ? 1 : 0,
                'declaration_accepted_at' => $r->filled('declaration_info_confirmed') ? ($leave->declaration_accepted_at ?: $now) : $leave->declaration_accepted_at,
                'declaration_accepted_by' => $r->filled('declaration_info_confirmed') ? ($leave->declaration_accepted_by ?: Auth::id()) : $leave->declaration_accepted_by,
                'updated_by' => Auth::id(),
            ]);

            if ($action === 'submit' && $wasDraft && !$leave->chain) {
                $steps = [];
                if ($rmId)
                    $steps[] = ['role' => 'Reporting Manager', 'uid' => $rmId];
                $steps[] = ['role' => 'HR', 'uid' => 98, 'status' => 'S'];
                $steps[] = ['role' => 'Management', 'uid' => 27, 'status' => 'S'];

                $chain = HrmsApproverChain::create([
                    'leave_request_id' => $leave->id,
                    'staff_id' => $leave->staff_id,
                    'overall_status' => 'P',
                ]);

                foreach ($steps as $i => $step) {
                    HrmsApproverChainStep::create([
                        'approver_chain_id' => $chain->id,
                        'step_no' => $i + 1,
                        'role' => $step['role'],
                        'approver_id' => $step['uid'],
                        'status' => $step['status'] ?? ($i === 0 ? 'P' : 'S'),
                    ]);
                }
            }
        });

        return redirect()
            ->route('approvals.inbox', ['active' => $leave->id])
            ->with('success', $action === 'submit' ? 'Leave submitted and approval flow created.' : 'Leave draft saved successfully.');
    }

    public function updateLegacy(Request $r, \App\SmLeaveRequest $leave)
    {
        $tz = config('app.timezone', 'Asia/Kolkata');

        // helper
        $getTypeCode = function (int $typeId) {
            return DB::table('sm_leave_types')->where('id', $typeId)->value('code');
        };

        // 1) Validate (Edit: allow past dates too)
        $rules = [
            'type_id' => ['required', 'integer'],
            'reporting_manager_id' => ['nullable', 'integer'],
            'leave_from' => ['required', 'date_format:d/m/Y'],
            'leave_to' => ['required', 'date_format:d/m/Y'],
            'half_session' => ['nullable'],
            'reason' => ['nullable', 'string'],
            'note' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'handover_to' => ['nullable', 'string', 'max:191'],
        ];
        for ($i = 1; $i <= 3; $i++) {
            $rules["emergency_name_$i"] = 'nullable|string|max:100';
            $rules["emergency_relation_$i"] = 'nullable|string|max:50';
            $rules["emergency_phone_$i"] = 'nullable|string|max:20';
            $rules["emergency_country_$i"] = 'nullable|string|max:60';
        }
        $this->validate($r, $rules);

        // 2) Dates
        $from = Carbon::createFromFormat('d/m/Y', $r->input('leave_from'), $tz)->startOfDay();
        $to = Carbon::createFromFormat('d/m/Y', $r->input('leave_to'), $tz)->startOfDay();

        // 3) Type + half-day logic
        $typeId = (int) $r->input('type_id');
        $typeCode = strtoupper((string) $getTypeCode($typeId));
        $isHalf = in_array($typeCode, ['HD', 'EL'], true);

        if ($isHalf) {
            $to = $from->copy();
            $days = 0.5;
        } else {
            if ($to->lt($from)) {
                return back()->withErrors(['leave_to' => 'Leave To must be after or equal to Leave From.'])->withInput();
            }
            $days = $from->diffInDays($to) + 1;
        }
        $halfSession = $isHalf ? ($r->input('half_session') ?: 'SECOND_HALF') : null;

        // 4) File (replace if new, keep old if none)
        $newFilePath = $leave->file; // column name 'file' used in store()
        if ($r->hasFile('file')) {
            // delete old if present
            if ($leave->file && Storage::disk('public')->exists($leave->file)) {
                Storage::disk('public')->delete($leave->file);
            }
            $newFilePath = $r->file('file')->store('leaves', 'public');
        }

        // 5) Emergency contacts
        $contacts = [];
        for ($i = 1; $i <= 3; $i++) {
            $name = trim((string) $r->input("emergency_name_$i"));
            $relation = trim((string) $r->input("emergency_relation_$i"));
            $phone = trim((string) $r->input("emergency_phone_$i"));
            $country = trim((string) $r->input("emergency_country_$i"));
            if ($name === '' && $relation === '' && $phone === '' && $country === '')
                continue;
            $contacts[] = compact('name', 'relation', 'phone', 'country');
        }

        // 6) Update (donΓÇÖt recreate chain; optionally refresh RM step if RM changed & no action taken)
        DB::transaction(function () use ($r, $leave, $from, $to, $days, $isHalf, $halfSession, $typeId, $newFilePath, $contacts) {

            $leave->update([
                'type_id' => $typeId,
                'reporting_manager_id' => $r->filled('reporting_manager_id') ? (int) $r->input('reporting_manager_id') : null,

                'leave_from' => $from->toDateString(),
                'leave_to' => $to->toDateString(),
                'days' => $days,
                'is_half_day' => $isHalf ? 1 : 0,
                'half_session' => $halfSession,

                'handover_to' => $r->input('handover_to'),
                'reason' => $r->input('reason'),
                'note' => $r->input('note'),
                'file' => $newFilePath,

                'emergency_contacts' => $contacts, // [] or array; model casts -> JSON
                'updated_by' => Auth::id(),
            ]);

            // OPTIONAL: if approver chain logic depends on RM and no approvals yet,
            // yahan refresh kar sakte ho. Abhi simple rakha hai.
        });

        return redirect()
            ->route('employee.leaves.index')
            ->with('success', 'Leave updated successfully.');
    }


    public function destroy(\App\SmLeaveRequest $leave)
    {
        $action = request()->input('leave_action', '');
        
        if (!$leave->can_be_deleted) {
            return redirect()->back()->with('error', 'This leave request cannot be deleted because it has already been processed by an approver.');
        }

        if ($leave->file && \Illuminate\Support\Facades\Storage::disk('public')->exists($leave->file)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($leave->file);
        }
        if ($leave->travel_ticket_file && \Illuminate\Support\Facades\Storage::disk('public')->exists($leave->travel_ticket_file)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($leave->travel_ticket_file);
        }

        if ($leave->chain) {
            $leave->chain->steps()->delete();
            $leave->chain()->delete();
        }
        
        $leave->delete();

        return redirect()->back()->with('success', 'Leave request deleted successfully.');
    }
}

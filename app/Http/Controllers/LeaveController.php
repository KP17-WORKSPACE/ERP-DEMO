<?php

namespace App\Http\Controllers;

use App\ApproverChain;
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

        $q      = $request->input('q');
        $status = $request->input('status');
        $typeId = $request->input('type_id');
        $from   = $request->input('from');
        $to     = $request->input('to');

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

        return view('backEnd.employee.leaves.create', compact('authUser', 'reportingManager', 'leaveTypes'));
    }


    private function getStaffId()
    {
        // Adjust to your mapping. If you have an SmStaff table with user_id, use that.
        // Example fallback: assume staff_id == users.id
        return (int)Auth::id();
    }



    public function show($id)
{
    $leave = \App\SmLeaveRequest::where('staff_id', Auth::id())->findOrFail($id);

    $chain = HrmsApproverChain::where('leave_request_id', $leave->id)
        ->with(['steps' => function ($q) {
            $q->orderBy('step_no')->with(['approver.user']);
        }])->first();

    $flow = [];
    if ($chain) {
        $flow = $chain->steps->map(function ($s) {
            // Build display name safely (full_name -> first+last -> user->name)
            $name = 'Unassigned';
            if ($s->approver) {
                $full = trim((string)($s->approver->full_name ?? ''));
                if ($full === '') {
                    $first = trim((string)($s->approver->first_name ?? ''));
                    $last  = trim((string)($s->approver->last_name ?? ''));
                    $full  = trim($first.' '.$last);
                }
                if ($full === '' && $s->approver->user) {
                    $full = trim((string)($s->approver->user->name ?? ''));
                }
                if ($full !== '') $name = $full;
            }

            return [
                'name'     => $name,                 // << yeh name ab role ke person ka hai
                'role'     => $s->role,              // Reporting Manager / HR / Finance
                'status'   => ['P'=>'Pending','A'=>'Approved','R'=>'Rejected','S'=>'Skipped'][$s->status] ?? 'Pending',
                'acted_at' => $s->acted_at ? \Carbon\Carbon::parse($s->acted_at)->format('d M Y, h:i A') : null,
                'comment'  => $s->comment,
            ];
        })->toArray();
    }

    return view('backEnd.employee.leaves._details', compact('leave','flow'));
}


public function store(Request $r)
{
    $tz = config('app.timezone', 'Asia/Kolkata');

    // --- Helpers ---
    $getTypeCode = function (int $typeId) {
        return DB::table('sm_leave_types')->where('id', $typeId)->value('code');
    };

    $resolveChain = function (?int $rmId): array {
        $hrId  = 98;
        $finId = 27;
        $steps = [];
        if (!empty($rmId)) $steps[] = ['role' => 'Reporting Manager', 'uid' => (int) $rmId];
        if ($hrId > 0)     $steps[] = ['role' => 'HR', 'uid' => $hrId];
        if ($finId > 0)    $steps[] = ['role' => 'Finance', 'uid' => $finId];
        return $steps;
    };

    // --- 1) Validate ---
    $todayLocal = Carbon::now($tz)->format('d/m/Y');

    // base rules
    $rules = [
        'type_id'              => 'required|integer',
        'reporting_manager_id' => 'nullable|integer',
        'leave_from'           => 'required|date_format:d/m/Y|after_or_equal:' . $todayLocal,
        'leave_to'             => 'required|date_format:d/m/Y',
        'half_session'         => 'nullable|in:FIRST_HALF,SECOND_HALF',
        'reason'               => 'nullable|string',
        'note'                 => 'nullable|string',
        'file'                 => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        'handover_to'          => 'nullable|string|max:191',
    ];

    // emergency contacts (loose optional validation)
    for ($i = 1; $i <= 3; $i++) {
        $rules["emergency_name_$i"]     = 'nullable|string|max:100';
        $rules["emergency_relation_$i"] = 'nullable|string|max:50';
        $rules["emergency_phone_$i"]    = 'nullable|string|max:20';
        $rules["emergency_country_$i"]  = 'nullable|string|max:60';
    }
    $this->validate($r, $rules);

    // --- 2) Dates ---
    $from = Carbon::createFromFormat('d/m/Y', $r->input('leave_from'), $tz)->startOfDay();
    $to   = Carbon::createFromFormat('d/m/Y', $r->input('leave_to'),   $tz)->startOfDay();

    // --- 3) Leave type & half-day ---
    $typeId   = (int) $r->input('type_id');
    $typeCode = strtoupper((string) $getTypeCode($typeId));
    $isHalf   = in_array($typeCode, ['HD', 'EL'], true);

    if ($isHalf) {
        $to   = $from->copy();
        $days = 0.5;
    } else {
        if ($to->lt($from)) {
            return back()->withErrors(['leave_to' => 'Leave To must be after or equal to Leave From.'])->withInput();
        }
        $days = $from->diffInDays($to) + 1;
    }
    $halfSession = $isHalf ? ($r->input('half_session') ?: 'SECOND_HALF') : null;

    // --- 4) File upload (optional) ---
    $filePath = null;
    if ($r->hasFile('file')) {
        $filePath = $r->file('file')->store('leaves', 'public');
    }

    // --- 5) Resolve approver chain (from request only) ---
    $rmId = $r->filled('reporting_manager_id') ? (int) $r->input('reporting_manager_id') : null;
    $chainSteps = $resolveChain($rmId);
    if (empty($chainSteps)) {
        return back()->withErrors(['reporting_manager_id' => 'Approver chain is not configured.'])->withInput();
    }

    // --- 5.1) Build emergency contacts array ---
    $contacts = [];
    for ($i = 1; $i <= 3; $i++) {
        $name     = trim((string) $r->input("emergency_name_$i"));
        $relation = trim((string) $r->input("emergency_relation_$i"));
        $phone    = trim((string) $r->input("emergency_phone_$i"));
        $country  = trim((string) $r->input("emergency_country_$i"));

        // skip fully empty row
        if ($name === '' && $relation === '' && $phone === '' && $country === '') {
            continue;
        }

        $contacts[] = [
            'name'     => $name,
            'relation' => $relation,
            'phone'    => $phone,
            'country'  => $country,
        ];
    }

    // --- 6) Transaction: create leave + approver chain ---
    $leave = DB::transaction(function () use (
        $r, $from, $to, $days, $isHalf, $halfSession, $filePath, $typeId, $chainSteps, $tz, $rmId, $contacts
    ) {
        $now = Carbon::now($tz);

        $companyId = data_get(session('logged_session_data'), 'company_id')
                  ?? (Auth::user()->company_id ?? null);

        $leave = \App\SmLeaveRequest::create([
            'leave_define_id'       => null,
            'staff_id'              => Auth::id(),
            'role_id'               => Auth::user()->role_id ?? null,

            'apply_date'            => $now->toDateString(),
            'leave_year'            => (int) $now->year,

            'type_id'               => $typeId,
            'reporting_manager_id'  => $rmId,

            'leave_from'            => $from->toDateString(),
            'leave_to'              => $to->toDateString(),
            'days'                  => $days,
            'is_half_day'           => $isHalf ? 1 : 0,
            'half_session'          => $halfSession,

            'company_id'            => $companyId,
            'handover_to'           => $r->input('handover_to'),

            'reason'                => $r->input('reason'),
            'note'                  => $r->input('note'),
            'file'                  => $filePath,

            'emergency_contacts'    => $contacts ?: null, // <-- save JSON

            'approve_status'        => 'P',
            'active_status'         => 1,
            'created_by'            => Auth::id(),
            'updated_by'            => Auth::id(),
        ]);

        $chain = \App\HrmsApproverChain::create([
            'leave_request_id' => $leave->id,
            'staff_id'         => Auth::id(),
            'overall_status'   => 'P',
        ]);

        foreach ($chainSteps as $i => $step) {
            \App\HrmsApproverChainStep::create([
                'approver_chain_id' => $chain->id,
                'step_no'           => $i + 1,
                'role'              => $step['role'],
                'approver_id'       => $step['uid'],
                'status'            => 'P',
            ]);
        }

        return $leave;
    });

    return redirect()
        ->route('employee.leaves.index')
        ->with('success', 'Leave submitted and approval flow created.');
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
     * If there is an ApproverChain row for this staff, use it.
     * Else fall back to RM (from form or staff table) + static HR/ACC.
     */
    protected function buildChainForNewLeave(int $staffId, ?int $rmIdFromForm): array
{
    $rmId  = $rmIdFromForm ?: (int) $this->getReportingManagerId($staffId);
    $hrId  = 7;   // static default
    $accId = 12;  // static default

    $raw = [(int)$rmId, (int)$hrId, (int)$accId];

    // filter + dedupe
    $chain = [];
    foreach ($raw as $uid) {
        if ($uid > 0 && !in_array($uid, $chain, true)) $chain[] = $uid;
    }
    return $chain;
}

    /**
     * Upsert (create/update) ApproverChain for a staff with a new RM.
     * Keeps existing HR/ACC if present; otherwise sets static defaults.
     */
    protected function upsertApproverChainRM(int $staffId, int $rmId): void
    {
        $row = ApproverChain::where('staff_id', $staffId)->first();

        if ($row) {
            $row->reporting_manager_id = $rmId;
            // keep existing hr_id / accounts_id as-is; if empty, set static
            if (empty($row->hr_id))      $row->hr_id = 7;
            if (empty($row->accounts_id))$row->accounts_id = 12;
            $row->save();
        } else {
            ApproverChain::create([
                'staff_id'             => $staffId,
                'reporting_manager_id' => $rmId,
                'hr_id'                => 7,   // static default
                'accounts_id'          => 12,  // static default
            ]);
        }
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
                'uid'      => (int) $uid,
                'role'     => isset($labels[$i]) ? $labels[$i] : null,
                'status'   => 'pending',   // pending|approved|rejected
                'acted_at' => null,
                'comment'  => null,
                'index'    => $i,
            ];
        }
        return $out;
    }

    /**
     * Example: fetch RM from staff table if not in form/ApproverChain.
     * Replace with your actual schema/joins.
     */
    protected function getReportingManagerId(int $staffId): ?int
    {
        return DB::table('staff')->where('user_id', $staffId)->value('reporting_manager_id');
    }

public function edit(\App\SmLeaveRequest $leave)
{
    $authUser = Auth::user();
    $reportingManager = \App\User::where('company_id', $authUser->company_id)
        ->where('id', '!=', $authUser->id)
        ->select('id','full_name')
        ->get();

    $leaveTypes = DB::table('sm_leave_types')->select('id','name','code')->get();

    return view('backEnd.employee.leaves.edit', compact('leave', 'authUser', 'reportingManager', 'leaveTypes'));
}



public function update(Request $r, \App\SmLeaveRequest $leave)
{
    $tz = config('app.timezone', 'Asia/Kolkata');

    // helper
    $getTypeCode = function (int $typeId) {
        return DB::table('sm_leave_types')->where('id', $typeId)->value('code');
    };

    // 1) Validate (Edit: allow past dates too)
    $rules = [
        'type_id'              => ['required','integer'],
        'reporting_manager_id' => ['nullable','integer'],
        'leave_from'           => ['required','date_format:d/m/Y'],
        'leave_to'             => ['required','date_format:d/m/Y'],
        'half_session'         => ['nullable'],
        'reason'               => ['nullable','string'],
        'note'                 => ['nullable','string'],
        'file'                 => ['nullable','file','mimes:pdf,jpg,jpeg,png','max:2048'],
        'handover_to'          => ['nullable','string','max:191'],
    ];
    for ($i=1; $i<=3; $i++) {
        $rules["emergency_name_$i"]     = 'nullable|string|max:100';
        $rules["emergency_relation_$i"] = 'nullable|string|max:50';
        $rules["emergency_phone_$i"]    = 'nullable|string|max:20';
        $rules["emergency_country_$i"]  = 'nullable|string|max:60';
    }
    $this->validate($r, $rules);

    // 2) Dates
    $from = Carbon::createFromFormat('d/m/Y', $r->input('leave_from'), $tz)->startOfDay();
    $to   = Carbon::createFromFormat('d/m/Y', $r->input('leave_to'),   $tz)->startOfDay();

    // 3) Type + half-day logic
    $typeId   = (int) $r->input('type_id');
    $typeCode = strtoupper((string) $getTypeCode($typeId));
    $isHalf   = in_array($typeCode, ['HD','EL'], true);

    if ($isHalf) {
        $to   = $from->copy();
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
    for ($i=1; $i<=3; $i++) {
        $name     = trim((string) $r->input("emergency_name_$i"));
        $relation = trim((string) $r->input("emergency_relation_$i"));
        $phone    = trim((string) $r->input("emergency_phone_$i"));
        $country  = trim((string) $r->input("emergency_country_$i"));
        if ($name === '' && $relation === '' && $phone === '' && $country === '') continue;
        $contacts[] = compact('name','relation','phone','country');
    }

    // 6) Update (don’t recreate chain; optionally refresh RM step if RM changed & no action taken)
    DB::transaction(function () use ($r, $leave, $from, $to, $days, $isHalf, $halfSession, $typeId, $newFilePath, $contacts) {

        $leave->update([
            'type_id'              => $typeId,
            'reporting_manager_id' => $r->filled('reporting_manager_id') ? (int)$r->input('reporting_manager_id') : null,

            'leave_from'           => $from->toDateString(),
            'leave_to'             => $to->toDateString(),
            'days'                 => $days,
            'is_half_day'          => $isHalf ? 1 : 0,
            'half_session'         => $halfSession,

            'handover_to'          => $r->input('handover_to'),
            'reason'               => $r->input('reason'),
            'note'                 => $r->input('note'),
            'file'                 => $newFilePath,

            'emergency_contacts'   => $contacts, // [] or array; model casts -> JSON
            'updated_by'           => Auth::id(),
        ]);

        // OPTIONAL: if approver chain logic depends on RM and no approvals yet,
        // yahan refresh kar sakte ho. Abhi simple rakha hai.
    });

    return redirect()
        ->route('employee.leaves.index')
        ->with('success', 'Leave updated successfully.');
}


}

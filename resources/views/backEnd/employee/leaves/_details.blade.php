@php
    use Carbon\Carbon;
    use Illuminate\Support\Str;

    $leaveNumber = $leave->leave_application_no ?: ('LR' . optional($leave->company)->other_code . '-' . $leave->id);
    $employeeName = optional($leave->staffs)->full_name ?: trim(optional($leave->staffs)->first_name . ' ' . optional($leave->staffs)->last_name);

    if (!function_exists('leaveStatusClass')) {
        function leaveStatusClass($status) {
            if ($status === 'A') return 'bg-success text-white';
            if ($status === 'R') return 'bg-danger text-white';
            if ($status === 'C') return 'bg-secondary text-white';
            if ($status === 'P') return 'bg-warning text-dark';
            return 'bg-lightgreen text-dark';
        }
    }
    
    $statusLabel = ['D'=>'New','P'=>'Pending','A'=>'Approved','R'=>'Rejected','C'=>'Cancelled'][$leave->approve_status] ?? 'Pending';
    
    $halfLabel = '';
    if (!empty($leave->is_half_day)) {
        $session = $leave->half_session ? ' (' . str_replace('_', ' ', ucwords(strtolower($leave->half_session))) . ')' : '';
        $halfLabel = '<span class="badge bg-warning text-dark ms-1">Half Day' . $session . '</span>';
    }
@endphp

<style>
    #leave-details label {
        font-weight: 600 !important;
        background-color: #deebe1 !important;
        margin-bottom: 3px !important;
        text-align: center !important;
        color: #212529 !important;
    }
    #leave-details .green-heading p {
        font-weight: 600 !important;
        background-color: #deebe1 !important;
        margin-bottom: 3px !important;
        text-align: center !important;
        color: #212529 !important;
    }
    #leave-details .green-heading { text-align: center !important; }
    #leave-details .form-control-plaintext { text-align: center !important; }
    #leave-details .detail-item-table-sm td { text-align: start !important; }
    .bg-lightgreen { background-color: #deebe1 !important; }
</style>

<div id="leave-details">

<div class="purchase-order-content-header sticky-top d-flex justify-content-between align-items-center" style="background-color: #f7f8fd; padding: 15px;">
    <div class="d-flex align-items-center">
        <h4 class="purchase-order-content-header-left mb-0">#{{ $leaveNumber }}</h4>
        @php 
            $badgeColor = $statusLabel === 'Approved' ? 'success' : ($statusLabel === 'Rejected' ? 'danger' : ($statusLabel === 'Pending' ? 'warning text-dark' : ($statusLabel === 'New' ? 'primary' : 'secondary'))); 
        @endphp
        <span class="badge bg-{{ $badgeColor }} ms-2 px-2 py-1" style="font-size: 14px; font-weight: 500;">{{ $statusLabel }}</span>
    </div>
    
    <div class="purchase-order-content-header-right d-flex align-items-center">
        @if(in_array($leave->approve_status, ['D', 'P']))
            <a href="{{ route('employee.leaves.edit', $leave->id) }}" class="btn btn-light text-dark">
                <i class="ico icon-outline-pen-2 text-success btn-icon"></i><span class="btn-text ms-1">Edit</span>
            </a>
        @endif
        <a href="{{ route('employee.leaves.index', ['leave_action' => 'add']) }}" class="btn btn-light text-dark ms-2">
            <i class="ico icon-outline-add-square text-success btn-icon"></i><span class="btn-text ms-1">Add</span>
        </a>
        <div class="dropdown" style="display:inline-block;margin-left:5px;">
            <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="ico icon-outline-hamburger-menu"></i>
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('employee.leaves.index') }}"><i class="ico icon-outline-list-down text-success"></i> My Leaves</a></li>
            </ul>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <div class="row">
            <div class="col-2 mb-2">
                <label class="form-label">Request Number</label>
                <div class="form-control-plaintext truncate-text-custom">{{ $leaveNumber }}</div>
            </div>
            <div class="col-2 mb-2">
                <label class="form-label">Employee</label>
                <div class="form-control-plaintext truncate-text-custom">{{ $employeeName ?: 'N/A' }}</div>
            </div>
            <div class="col-2 mb-2">
                <label class="form-label">Department</label>
                <div class="form-control-plaintext truncate-text-custom">{{ optional(optional($leave->staffs)->departments)->name ?: 'N/A' }}</div>
            </div>
            <div class="col-2 mb-2">
                <label class="form-label">Designation</label>
                <div class="form-control-plaintext truncate-text-custom">{{ optional(optional($leave->staffs)->designations)->title ?: 'N/A' }}</div>
            </div>
            <div class="col-2 mb-2">
                <label class="form-label">Leave Status</label>
                <div class="form-control-plaintext truncate-text-custom">{{ $statusLabel }}</div>
            </div>
            <div class="col-2 mb-2">
                <label class="form-label">Applied On</label>
                <div class="form-control-plaintext truncate-text-custom">{{ optional($leave->apply_date)->format('d/m/Y') ?: (optional($leave->created_at)->format('d/m/Y') ?: '-') }}</div>
            </div>
        </div>
    </div>
</div>

<div class="tab-wrap mb-3">
    <ul class="nav nav-tabs" id="leaveDetailsTabs" role="tablist">
        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#leave-details-tab" type="button">Leave Details</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#travel-info-tab" type="button">Travel Information</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#handover-info-tab" type="button">Handover Information</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#emergency-contact-tab" type="button">Emergency Contact</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#leave-balance-tab" type="button">Leave Balance</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#approval-info-tab" type="button">Approval Information</button></li>
    </ul>

    <div class="tab-content mb-3">
        <!-- Tab 1: Leave Details -->
        <div class="tab-pane fade show active" id="leave-details-tab">
            <div class="row text-center">
                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading"><p class="mb-0">Leave Type</p>{{ optional($leave->type)->name ?? 'N/A' }}</div>
                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading"><p class="mb-0">Leave Category</p>{{ $leave->leave_category ?? 'N/A' }}</div>
                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading"><p class="mb-0">Leave From</p>{{ optional($leave->leave_from)->format('d/m/Y') ?? 'N/A' }}</div>
                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading"><p class="mb-0">Leave To</p>{{ optional($leave->leave_to)->format('d/m/Y') ?? 'N/A' }}</div>
                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading"><p class="mb-0">Number of Days</p>{{ (float)$leave->days }} {!! $halfLabel !!}</div>
                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading"><p class="mb-0">Return To Work</p>{{ optional($leave->return_to_work_date)->format('d/m/Y') ?? 'N/A' }}</div>
                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading"><p class="mb-0">Notice Period</p>{{ $leave->notice_period ?? 'N/A' }}</div>
                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading"><p class="mb-0">Urgency Level</p>{{ $leave->urgency_level ?? 'N/A' }}</div>
                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading"><p class="mb-0">Nature of Leave</p>{{ $leave->nature_of_leave ?? 'N/A' }}</div>
                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading"><p class="mb-0">Expected Availability</p>{{ $leave->availability_during_leave ?? 'N/A' }}</div>
                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading"><p class="mb-0">Reason for Leave</p>{{ $leave->reason ?? 'N/A' }}</div>
                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading"><p class="mb-0">Contact During Leave</p>{{ $leave->contact_number_during_leave ?? 'N/A' }}</div>
                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading"><p class="mb-0">Email During Leave</p>{{ $leave->email_during_leave ?? 'N/A' }}</div>
                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading"><p class="mb-0">Attachment</p>
                    @if (!empty($leave->file))
                        <a href="{{ \Illuminate\Support\Facades\Storage::url($leave->file) }}" target="_blank" class="text-success">View File</a>
                    @else
                        N/A
                    @endif
                </div>
            </div>
        </div>

        <!-- Tab 2: Travel Information -->
        <div class="tab-pane fade" id="travel-info-tab">
            @if(($leave->leaving_country ?? 'No') === 'Yes')
                <div class="row text-center">
                    <div class="col-xxl-3 col-lg-4 col-md-6 col-12 mb-3 green-heading"><p class="mb-0">Leaving Country</p>{{ $leave->leaving_country }}</div>
                    <div class="col-xxl-3 col-lg-4 col-md-6 col-12 mb-3 green-heading"><p class="mb-0">Destination Country</p>{{ $leave->destination_country ?? 'N/A' }}</div>
                    <div class="col-xxl-3 col-lg-4 col-md-6 col-12 mb-3 green-heading"><p class="mb-0">Departure Date</p>{{ optional($leave->departure_date)->format('d/m/Y') ?? 'N/A' }}</div>
                    <div class="col-xxl-3 col-lg-4 col-md-6 col-12 mb-3 green-heading"><p class="mb-0">Expected Return</p>{{ optional($leave->expected_return_date)->format('d/m/Y') ?? 'N/A' }}</div>
                    <div class="col-xxl-6 col-lg-6 col-md-12 col-12 mb-3 green-heading"><p class="mb-0">Accommodation Address</p>{{ $leave->accommodation_address ?? 'N/A' }}</div>
                    <div class="col-xxl-3 col-lg-4 col-md-6 col-12 mb-3 green-heading"><p class="mb-0">Travel Ticket</p>
                        @if (!empty($leave->travel_ticket_file))
                            <a href="{{ \Illuminate\Support\Facades\Storage::url($leave->travel_ticket_file) }}" target="_blank" class="text-success">View Ticket</a>
                        @else
                            N/A
                        @endif
                    </div>
                </div>
            @else
                <div class="p-4 text-center text-muted">
                    Travel information not applicable.
                </div>
            @endif
        </div>

        <!-- Tab 3: Handover Information -->
        <div class="tab-pane fade" id="handover-info-tab">
            @php
                $isNoHandover = ($leave->handover_required ?? 'No') !== 'Yes';
                $valOrDash = function($val) use ($isNoHandover) {
                    return $isNoHandover ? '-' : ($val ?: 'N/A');
                };
                $handoverStaff = $leave->handover_employee_id ? \App\SmStaff::find($leave->handover_employee_id) : null;
                $formatName = function ($staff) {
                    if (!$staff) return '';
                    return trim($staff->first_name . ' ' . $staff->last_name);
                };
            @endphp
            <div class="row text-center">
                <div class="col-xxl-2 col-lg-3 col-md-2 col-6 mb-3 green-heading"><p class="mb-0">Required</p>{{ $leave->handover_required ?? 'No' }}</div>
                <div class="col-xxl-2 col-lg-3 col-md-2 col-6 mb-3 green-heading"><p class="mb-0">To Employee</p>{{ $valOrDash($handoverStaff ? ($formatName($handoverStaff) ?: 'N/A') : ($leave->handover_to ?? 'N/A')) }}</div>
                <div class="col-xxl-2 col-lg-3 col-md-2 col-6 mb-3 green-heading"><p class="mb-0" title="Employee Department">Emp. Department</p>{{ $valOrDash(optional(optional($handoverStaff)->departments)->name) }}</div>
                <div class="col-xxl-2 col-lg-3 col-md-2 col-6 mb-3 green-heading"><p class="mb-0" title="Employee Designation">Emp. Designation</p>{{ $valOrDash(optional(optional($handoverStaff)->designations)->title) }}</div>
                <div class="col-xxl-2 col-lg-3 col-md-2 col-6 mb-3 green-heading"><p class="mb-0 text-nowrap">Pending Tasks</p><span>{{ $valOrDash(data_get($leave, 'pending_tasks')) }}</span></div>
                <div class="col-xxl-2 col-lg-3 col-md-2 col-6 mb-3 green-heading"><p class="mb-0 text-nowrap" title="Client Responsibilities">Client Resp.</p><span>{{ $valOrDash(data_get($leave, 'client_responsibilities')) }}</span></div>
                <div class="col-xxl-2 col-lg-3 col-md-2 col-6 mb-3 green-heading"><p class="mb-0 text-nowrap" title="Access Transfer Required">Access Transfer</p><span>{{ $valOrDash(data_get($leave, 'access_transfer_required')) }}</span></div>
                <div class="col-xxl-2 col-lg-3 col-md-2 col-6 mb-3 green-heading"><p class="mb-0 text-nowrap" title="Completion Confirmation">Completion Conf.</p><span>{{ $valOrDash(data_get($leave, 'handover_completion_confirmation')) }}</span></div>
                <div class="col-xxl-2 col-lg-3 col-md-2 col-6 mb-3 green-heading"><p class="mb-0 text-nowrap" title="Manager Verification">Manager Verif.</p><span>{{ $valOrDash(data_get($leave, 'manager_verification_of_handover')) }}</span></div>
                <div class="col-xxl-4 col-lg-4 col-md-2 col-12 mb-3 green-heading"><p class="mb-0 text-nowrap" title="Additional Remarks">Addl. Remarks</p><span>{{ $valOrDash(data_get($leave, 'handover_additional_remarks', $leave->note)) }}</span></div>
            </div>
        </div>

        <!-- Tab 4: Emergency Contact -->
        <div class="tab-pane fade" id="emergency-contact-tab">
            <div class="row text-center">
                @php
                    $contacts = is_array($leave->emergency_contacts) ? $leave->emergency_contacts : (json_decode($leave->emergency_contacts, true) ?: []);
                @endphp
                @if(count($contacts))
                    @foreach($contacts as $i => $c)
                        <div class="col-xxl-4 col-lg-4 col-md-6 col-12 mb-3 green-heading"><p class="mb-0">Emergency Contact Person</p>{{ $c['name'] ?? 'N/A' }}</div>
                        <div class="col-xxl-4 col-lg-4 col-md-6 col-12 mb-3 green-heading"><p class="mb-0">Emergency Contact Number</p>{{ $c['phone'] ?? 'N/A' }}</div>
                        <div class="col-xxl-4 col-lg-4 col-md-6 col-12 mb-3 green-heading"><p class="mb-0">Relationship</p>{{ $c['relation'] ?? 'N/A' }}</div>
                    @endforeach
                @else
                    <div class="col-xxl-4 col-lg-4 col-md-6 col-12 mb-3 green-heading"><p class="mb-0">Emergency Contact Person</p>{{ $leave->emergency_contact_person ?? 'N/A' }}</div>
                    <div class="col-xxl-4 col-lg-4 col-md-6 col-12 mb-3 green-heading"><p class="mb-0">Emergency Contact Number</p>{{ $leave->emergency_contact_number ?? 'N/A' }}</div>
                    <div class="col-xxl-4 col-lg-4 col-md-6 col-12 mb-3 green-heading"><p class="mb-0">Relationship</p>{{ $leave->emergency_contact_relationship ?? 'N/A' }}</div>
                @endif
            </div>
        </div>

        <!-- Tab 5: Leave Balance -->
        <div class="tab-pane fade" id="leave-balance-tab">
            <div class="p-4 text-center text-muted">
                No leave balance information available.
            </div>
        </div>

        <!-- Tab 6: Approval Information -->
        <div class="tab-pane fade" id="approval-info-tab">
            <div class="row">
                @if($leave->chain && $leave->chain->steps)
                    @foreach($leave->chain->steps as $step)
                        @php
                            $stepClass = leaveStatusClass($step->status);
                        @endphp
                        <div class="col-12 p-1 mb-3">
                            <div class="card">
                                <table class="detail-item-table-sm" width="100%" style="table-layout: fixed;width:100%">
                                    <tr>
                                        <td class="{{ $stepClass }} d-flex align-items-center justify-content-start gap-1" style="height:23px; padding: 0 15px;">
                                            <div class="d-flex align-items-center justify-content-start flex-grow-1 gap-1 header-height">
                                                <b>{{ $step->role }}</b>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start truncate-text-custom" style="padding: 10px 15px;">
                                            <span class="fw-bold">Status</span> : 
                                            @if ($step->status === 'A') Approved <i class="ico icon-outline-check-read title-15 text-success"></i>
                                            @elseif($step->status === 'R') Rejected <i class="ico icon-outline-close text-danger"></i>
                                            @else Pending <i class="ico icon-outline-clock-circle text-info"></i>
                                            @endif
                                        </td>
                                    </tr>
                                    @if(strtoupper($step->role) == 'REPORTING MANAGER')
                                        @if($step->l1_coverage)<tr><td class="text-start truncate-text-custom" style="padding: 5px 15px;"><span class="fw-bold">Team Staffing Availability</span> : {{ $step->l1_coverage }}</td></tr>@endif
                                        @if($step->l1_workload)<tr><td class="text-start truncate-text-custom" style="padding: 5px 15px;"><span class="fw-bold">Operational Impact</span> : {{ $step->l1_workload }}</td></tr>@endif
                                        @if($step->l1_duration_ok)<tr><td class="text-start truncate-text-custom" style="padding: 5px 15px;"><span class="fw-bold">Project Deadlines</span> : {{ $step->l1_duration_ok }}</td></tr>@endif
                                        @if($step->l1_notice_compliance)<tr><td class="text-start truncate-text-custom" style="padding: 5px 15px;"><span class="fw-bold">Work Handover Adequacy</span> : {{ $step->l1_notice_compliance }}</td></tr>@endif
                                        @if($step->l1_eligibility)<tr><td class="text-start truncate-text-custom" style="padding: 5px 15px;"><span class="fw-bold">Leave Pattern Review</span> : {{ $step->l1_eligibility }}</td></tr>@endif
                                    @elseif(strtoupper($step->role) == 'HR')
                                        @if($step->l2_cost)<tr><td class="text-start truncate-text-custom" style="padding: 5px 15px;"><span class="fw-bold">Leave Eligibility</span> : {{ $step->l2_cost }}</td></tr>@endif
                                        @if($step->l2_unpaid)<tr><td class="text-start truncate-text-custom" style="padding: 5px 15px;"><span class="fw-bold">Service Validation</span> : {{ $step->l2_unpaid }}</td></tr>@endif
                                        @if($step->l2_balance)<tr><td class="text-start truncate-text-custom" style="padding: 5px 15px;"><span class="fw-bold">Entitlement Validation</span> : {{ $step->l2_balance }}</td></tr>@endif
                                        @if($step->l2_policy)<tr><td class="text-start truncate-text-custom" style="padding: 5px 15px;"><span class="fw-bold">Compliance</span> : {{ $step->l2_policy }}</td></tr>@endif
                                        @if($step->l2_encash)<tr><td class="text-start truncate-text-custom" style="padding: 5px 15px;"><span class="fw-bold">Previous Leave History</span> : {{ $step->l2_encash }}</td></tr>@endif
                                    @endif
                                    @if($step->comment)
                                    <tr>
                                        <td class="text-start truncate-text-custom" style="padding: 5px 15px;">
                                            <span class="fw-bold">Remarks</span> : {{ $step->comment }}
                                        </td>
                                    </tr>
                                    @endif
                                    @if($step->acted_at)
                                    <tr>
                                        <td class="text-start truncate-text-custom" style="padding: 5px 15px;">
                                            <span class="fw-bold">Approval Date</span> : {{ \Carbon\Carbon::parse($step->acted_at)->format('d M Y h:i A') }}
                                        </td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="p-4 text-center text-muted col-12">
                        No approval information available.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

</div>

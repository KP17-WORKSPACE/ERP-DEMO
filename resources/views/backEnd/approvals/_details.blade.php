@php
    if (!function_exists('firstLastName')) {
        function firstLastName($name)
        {
            if (!$name)
                return '-';
            $parts = array_filter(explode(' ', trim($name)));
            if (count($parts) > 1) {
                return reset($parts) . ' ' . end($parts);
            }
            return $name;
        }
    }
@endphp
@php
    $trackMode = $trackMode ?? false;
    $leaveNumber = $leave->leave_application_no ?: ('LR' . optional($leave->company)->other_code . '-' . $leave->id);
    $employeeName = optional($leave->staffs)->full_name ?: trim(optional($leave->staffs)->first_name . ' ' . optional($leave->staffs)->last_name);
    $statusLabel = ['D' => 'New', 'P' => 'Pending', 'A' => 'Approved', 'R' => 'Rejected', 'C' => 'Returned'][$leave->approve_status] ?? 'Pending';
    $badgeColor = $statusLabel === 'Approved' ? 'success' : ($statusLabel === 'Rejected' ? 'danger' : ($statusLabel === 'Pending' ? 'warning' : ($statusLabel === 'New' ? 'primary' : 'secondary')));
    $handoverStaff = $leave->handover_employee_id ? \App\SmStaff::with(['departments', 'designations'])->find($leave->handover_employee_id) : null;
    $contacts = is_array($leave->emergency_contacts) ? $leave->emergency_contacts : (json_decode($leave->emergency_contacts, true) ?: []);
    $firstPendingStep = optional($leave->chain)->steps ? $leave->chain->steps->firstWhere('status', 'P') : null;
    $firstPendingStepId = optional($firstPendingStep)->id;

    $fmtDate = function ($value, $format = 'd/m/Y') {
        if (empty($value))
            return '-';
        try {
            return \Carbon\Carbon::parse($value)->format($format);
        } catch (\Exception $e) {
            return '-';
        }
    };
    $stepStatusText = function ($status) {
        return ['P' => 'Pending', 'A' => 'Approved', 'R' => 'Rejected', 'C' => 'Returned', 'S' => 'Not Required'][$status] ?? 'Pending';
    };
    $stepStatusClass = function ($status) {
        return ['A' => 'bg-success text-white', 'R' => 'bg-danger text-white', 'C' => 'bg-secondary text-white', 'P' => 'bg-lightgreen text-dark', 'S' => 'bg-lightgreen text-dark'][$status] ?? 'bg-lightgreen text-dark';
    };
    $stepIcon = function ($status) {
        if ($status === 'A')
            return '<i class="ico icon-outline-check-read title-15 text-success"></i>';
        if ($status === 'R')
            return '<i class="ico icon-outline-close text-danger"></i>';
        if ($status === 'C')
            return '<i class="ico icon-outline-close text-warning"></i>';
        return '<i class="ico icon-outline-clock-circle text-info"></i>';
    };
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

    #leave-details .green-heading {
        text-align: center !important;
    }

    #leave-details .form-control-plaintext {
        text-align: center !important;
    }

    #leave-details .detail-item-table-sm td {
        text-align: start !important;
    }

    #leave-details .handover-inline .green-heading p {
        white-space: nowrap;
    }

    #leave-details .handover-inline .form-select-sm,
    #leave-details .handover-inline .form-control-sm {
        font-size: 13px;
        min-height: 30px;
        padding: 3px 8px;
        text-align: center;
    }

    #leave-details .handover-inline textarea.form-control-sm {
        text-align: left;
        min-height: 46px;
        resize: vertical;
    }

    #leave-details .handover-field-edit {
        color: #0f5132;
        font-size: 12px;
        line-height: 1;
        text-decoration: none;
    }

    .bg-lightgreen {
        background-color: #deebe1 !important;
    }

    .header-height {
        height: 1rem;
    }
</style>

<div id="leave-details">
    <div class="purchase-order-content-header sticky-top" style="background-color:#f7f8fd">
        <div class="d-flex align-items-center gap-2">
            <h4 class="purchase-order-content-header-left mb-0">{{ $leaveNumber }}</h4>
            <div class="pipeline-arrow {{ $badgeColor }}">{{ $statusLabel }}</div>
        </div>
        <div class="purchase-order-content-header-right d-flex align-items-center">
            @if(!$trackMode)
                <a href="{{ route('approvals.inbox', ['leave_action' => 'add']) }}"
                    class="btn btn-light text-dark d-inline-flex align-items-center">
                    <i class="ico icon-outline-add-square text-success"></i><span class="btn-text ms-1">Add</span>
                </a>
                @if(in_array($leave->approve_status, ['D', 'P'], true))
                    <a href="{{ route('approvals.inbox', ['active' => $leave->id, 'leave_action' => 'edit']) }}"
                        class="btn btn-light text-dark d-inline-flex align-items-center ms-2">
                        <i class="ico icon-outline-pen-2 text-success btn-icon"></i><span class="btn-text ms-1">Edit</span>
                    </a>
                @endif
            @endif
            <div class="dropdown" style="display:inline-block;margin-left:5px;">
                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ico icon-outline-hamburger-menu"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        @if($trackMode)
                            <a class="dropdown-item" href="{{ route('approvals.inbox', ['active' => $leave->id]) }}">
                                <i class="ico icon-outline-list-down text-success"></i> Leaves
                            </a>
                        @elseif($leave->approve_status === 'D')
                            <a class="dropdown-item" href="javascript:void(0)"
                                onclick="alert('Draft requests are not available in Leave Track. Please submit for approval first.')">
                                <i class="ico icon-outline-list-down text-success"></i> Leave Track
                            </a>
                        @else
                            <a class="dropdown-item" href="{{ route('approvals.leave-track', $leave->id) }}">
                                <i class="ico icon-outline-list-down text-success"></i> Leave Track
                            </a>
                        @endif
                    </li>
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
                    <div class="form-control-plaintext truncate-text-custom">
                        {{ optional(optional($leave->staffs)->departments)->name ?: 'N/A' }}
                    </div>
                </div>
                <div class="col-2 mb-2">
                    <label class="form-label">Designation</label>
                    <div class="form-control-plaintext truncate-text-custom">
                        {{ optional(optional($leave->staffs)->designations)->title ?: 'N/A' }}
                    </div>
                </div>
                <div class="col-2 mb-2">
                    <label class="form-label">Leave Status</label>
                    <div class="form-control-plaintext truncate-text-custom">{{ $statusLabel }}</div>
                </div>
                <div class="col-2 mb-2">
                    <label class="form-label">Applied On</label>
                    <div class="form-control-plaintext truncate-text-custom">
                        {{ $fmtDate($leave->apply_date ?: $leave->created_at) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-wrap mb-3">
        <ul class="nav nav-tabs" id="leaveDetailsTabs" role="tablist">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab"
                    data-bs-target="#leave-details-tab" type="button">Leave Details</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#travel-info-tab"
                    type="button">Travel Information</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#handover-info-tab"
                    type="button">Handover Information</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#emergency-contact-tab"
                    type="button">Emergency Contact</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#leave-balance-tab"
                    type="button">Leave Balance</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#attachments-remarks-tab"
                    type="button">Attachments / Remarks</button></li>
        </ul>

        <div class="tab-content mb-3">
            <div class="tab-pane fade show active" id="leave-details-tab">
                <div class="row text-center">
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                        <p class="mb-0" title="Leave Type">Type</p>{{ optional($leave->type)->name ?? 'N/A' }}
                    </div>
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                        <p class="mb-0" title="Leave Category">Category</p>{{ $leave->leave_category ?? 'N/A' }}
                    </div>
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                        <p class="mb-0" title="Leave From">From</p>{{ $fmtDate($leave->leave_from) }}
                    </div>
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                        <p class="mb-0" title="Leave To">To</p>{{ $fmtDate($leave->leave_to) }}
                    </div>
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                        <p class="mb-0" title="Number of Leave Days">Days</p>
                        {{ number_format((float) $leave->days, 2) }}
                    </div>
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                        <p class="mb-0" title="Return To Work">Return Date</p>
                        {{ $fmtDate($leave->return_to_work_date) }}
                    </div>
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                        <p class="mb-0">Notice Period</p>{{ $leave->notice_period ?? 'N/A' }}
                    </div>
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                        <p class="mb-0">Urgency Level</p>{{ $leave->urgency_level ?? 'N/A' }}
                    </div>
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                        <p class="mb-0">Nature of Leave</p>{{ $leave->nature_of_leave ?? 'N/A' }}
                    </div>
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                        <p class="mb-0" title="Expected Availability">Availability</p>
                        {{ $leave->availability_during_leave ?? 'N/A' }}
                    </div>
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                        <p class="mb-0" title="Reason for Leave">Reason</p>{{ $leave->reason ?? 'N/A' }}
                    </div>
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                        <p class="mb-0" title="Contact During Leave">Contact</p>
                        {{ $leave->contact_number_during_leave ?? 'N/A' }}
                    </div>
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                        <p class="mb-0" title="Email During Leave">Email</p>{{ $leave->email_during_leave ?? 'N/A' }}
                    </div>
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                        <p class="mb-0">Attachment</p>
                        @if (!empty($leave->file))
                            <a href="{{ \Illuminate\Support\Facades\Storage::url($leave->file) }}" target="_blank"
                                class="text-success">View File</a>
                        @else
                            N/A
                        @endif
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="travel-info-tab">
                @if(($leave->leaving_country ?? 'No') === 'Yes')
                    <div class="row text-center">
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                            <p class="mb-0" title="Leaving The Country">Leaving Country</p>{{ $leave->leaving_country }}
                        </div>
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                            <p class="mb-0" title="Destination Country">Destination</p>
                            {{ $leave->destination_country ?? 'N/A' }}
                        </div>
                        <div class="col-xxl-2 col-lg-3 col-md-6 col-6 mb-3 green-heading">
                            <p class="mb-0" title="Accommodation Address">Address</p>
                            {{ $leave->accommodation_address ?? 'N/A' }}
                        </div>
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                            <p class="mb-0" title="Departure Date">Departure</p>{{ $fmtDate($leave->departure_date) }}
                        </div>
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                            <p class="mb-0" title="Expected Return Date">Return Date</p>
                            {{ $fmtDate($leave->expected_return_date) }}
                        </div>
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                            <p class="mb-0" title="Travel Ticket Attached">Ticket</p>
                            @if (!empty($leave->travel_ticket_file))
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($leave->travel_ticket_file) }}"
                                    target="_blank" class="text-success">View Ticket</a>
                            @else
                                No
                            @endif
                        </div>
                    </div>
                @else
                    <div class="p-4 text-center text-muted">Travel information not applicable.</div>
                @endif
            </div>

            <div class="tab-pane fade" id="handover-info-tab">
                @php
                    $handoverSuppliedByEmployee = ($leave->handover_required ?? 'No') === 'Yes';
                    $anyApprovalDone = $leave->chain && $leave->chain->steps
                        ? $leave->chain->steps->contains(function ($step) {
                            return $step->status === 'A';
                        })
                        : false;
                    $canEditHandover = $trackMode && $leave->approve_status === 'P' && !$anyApprovalDone;
                @endphp

                @if($canEditHandover)
                    <div class="row text-center handover-inline align-items-start">
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                            <p class="mb-0">Required</p>{{ $leave->handover_required ?: '-' }}
                        </div>
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                            <p class="mb-0">To Employee</p>
                            {{ firstLastName(optional($handoverStaff)->full_name ?: $leave->handover_to) }}
                        </div>
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                            <p class="mb-0" title="Employee Department">Emp. Dept.</p>
                            {{ optional(optional($handoverStaff)->departments)->name ?: '-' }}
                        </div>
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                            <p class="mb-0" title="Employee Designation">Emp. Desig.</p>
                            {{ optional(optional($handoverStaff)->designations)->title ?: '-' }}
                        </div>
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                            <p class="mb-0">Pending Tasks</p>
                            <span data-handover-field="pending_tasks">{{ data_get($leave, 'pending_tasks') ?: '-' }}</span>
                            <a href="#" class="ms-1 handover-field-edit" data-bs-toggle="modal"
                                data-bs-target="#handoverFieldEditModal" data-title="Update Pending Tasks"
                                data-field="pending_tasks" data-type="select"
                                data-value="{{ e(data_get($leave, 'pending_tasks') ?: 'No') }}"><i
                                    class="ico icon-outline-pen-new-square text-danger" style="font-size: 12px"></i></a>
                        </div>
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                            <p class="mb-0" title="Access Transfer Required">Access Transfer</p>
                            <span
                                data-handover-field="access_transfer_required">{{ data_get($leave, 'access_transfer_required') ?: '-' }}</span>
                            <a href="#" class="ms-1 handover-field-edit" data-bs-toggle="modal"
                                data-bs-target="#handoverFieldEditModal" data-title="Update Access Transfer"
                                data-field="access_transfer_required" data-type="select"
                                data-value="{{ e(data_get($leave, 'access_transfer_required') ?: 'No') }}"><i
                                    class="ico icon-outline-pen-new-square text-danger" style="font-size: 12px"></i></a>
                        </div>
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                            <p class="mb-0" title="Handover Completion Confirmation">Completion</p>
                            <span
                                data-handover-field="handover_completion_confirmation">{{ data_get($leave, 'handover_completion_confirmation') ?: '-' }}</span>
                            <a href="#" class="ms-1 handover-field-edit" data-bs-toggle="modal"
                                data-bs-target="#handoverFieldEditModal" data-title="Update Completion"
                                data-field="handover_completion_confirmation" data-type="select"
                                data-value="{{ e(data_get($leave, 'handover_completion_confirmation') ?: 'No') }}"><i
                                    class="ico icon-outline-pen-new-square text-danger" style="font-size: 12px"></i></a>
                        </div>
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                            <p class="mb-0" title="Manager Verification of Handover">Manager Verify</p>
                            <span
                                data-handover-field="manager_verification_of_handover">{{ data_get($leave, 'manager_verification_of_handover') ?: '-' }}</span>
                            <a href="#" class="ms-1 handover-field-edit" data-bs-toggle="modal"
                                data-bs-target="#handoverFieldEditModal" data-title="Update Manager Verification"
                                data-field="manager_verification_of_handover" data-type="select"
                                data-value="{{ e(data_get($leave, 'manager_verification_of_handover') ?: 'No') }}"><i
                                    class="ico icon-outline-pen-new-square text-danger" style="font-size: 12px"></i></a>
                        </div>
                        <div class="col-xxl-4 col-lg-6 col-md-6 col-12 mb-3 green-heading">
                            <p class="mb-0" title="Client Responsibilities">Client Resp.</p>
                            <span
                                data-handover-field="client_responsibilities">{{ data_get($leave, 'client_responsibilities') ?: '-' }}</span>
                            <a href="#" class="ms-1 handover-field-edit" data-bs-toggle="modal"
                                data-bs-target="#handoverClientRespEditModal"><i
                                    class="ico icon-outline-pen-new-square text-danger" style="font-size: 12px"></i></a>
                        </div>
                        <div class="col-xxl-4 col-lg-6 col-md-6 col-12 mb-3 green-heading">
                            <p class="mb-0" title="Additional Remarks">Remarks</p>
                            <span
                                data-handover-field="handover_additional_remarks">{{ data_get($leave, 'handover_additional_remarks') ?: '-' }}</span>
                            <a href="#" class="ms-1 handover-field-edit" data-bs-toggle="modal"
                                data-bs-target="#handoverRemarksEditModal"><i
                                    class="ico icon-outline-pen-new-square text-danger" style="font-size: 12px"></i></a>
                        </div>
                    </div>
                @else
                    <div class="row text-center">
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                            <p class="mb-0">Required</p>{{ $leave->handover_required ?: '-' }}
                        </div>
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                            <p class="mb-0" title="To Employee">To Employee</p>
                            {{ firstLastName(optional($handoverStaff)->full_name ?: $leave->handover_to) }}
                        </div>
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                            <p class="mb-0" title="Employee Department">Emp. Dept.</p>
                            {{ optional(optional($handoverStaff)->departments)->name ?: '-' }}
                        </div>
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                            <p class="mb-0" title="Employee Designation">Emp. Desig.</p>
                            {{ optional(optional($handoverStaff)->designations)->title ?: '-' }}
                        </div>
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                            <p class="mb-0">Pending Tasks</p>{{ data_get($leave, 'pending_tasks') ?: '-' }}
                        </div>
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                            <p class="mb-0" title="Client Responsibilities">Client Resp.</p>
                            {{ data_get($leave, 'client_responsibilities') ?: '-' }}
                        </div>
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                            <p class="mb-0" title="Access Transfer Required">Access Transfer</p>
                            {{ data_get($leave, 'access_transfer_required') ?: '-' }}
                        </div>
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                            <p class="mb-0" title="Completion Confirmation">Completion</p>
                            {{ data_get($leave, 'handover_completion_confirmation') ?: '-' }}
                        </div>
                        <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                            <p class="mb-0" title="Manager Verification">Manager Verify</p>
                            {{ data_get($leave, 'manager_verification_of_handover') ?: '-' }}
                        </div>
                        <div class="col-xxl-3 col-lg-4 col-md-6 col-12 mb-3 green-heading">
                            <p class="mb-0" title="Additional Remarks">Remarks</p>
                            {{ data_get($leave, 'handover_additional_remarks') ?: '-' }}
                        </div>
                    </div>
                @endif
            </div>

            <div class="tab-pane fade" id="emergency-contact-tab">
                <div class="row text-center">
                    @if(count($contacts))
                        @foreach($contacts as $contact)
                            <div class="col-xxl-4 col-lg-4 col-md-6 col-12 mb-3 green-heading">
                                <p class="mb-0" title="Emergency Contact Person">Contact Person</p>
                                {{ $contact['name'] ?? 'N/A' }}
                            </div>
                            <div class="col-xxl-4 col-lg-4 col-md-6 col-12 mb-3 green-heading">
                                <p class="mb-0" title="Emergency Contact Number">Contact No.</p>{{ $contact['phone'] ?? 'N/A' }}
                            </div>
                            <div class="col-xxl-4 col-lg-4 col-md-6 col-12 mb-3 green-heading">
                                <p class="mb-0">Relationship</p>{{ $contact['relation'] ?? 'N/A' }}
                            </div>
                        @endforeach
                    @else
                        <div class="col-xxl-4 col-lg-4 col-md-6 col-12 mb-3 green-heading">
                            <p class="mb-0" title="Emergency Contact Person">Contact Person</p>
                            {{ $leave->emergency_contact_person ?? 'N/A' }}
                        </div>
                        <div class="col-xxl-4 col-lg-4 col-md-6 col-12 mb-3 green-heading">
                            <p class="mb-0" title="Emergency Contact Number">Contact No.</p>
                            {{ $leave->emergency_contact_number ?? 'N/A' }}
                        </div>
                        <div class="col-xxl-4 col-lg-4 col-md-6 col-12 mb-3 green-heading">
                            <p class="mb-0">Relationship</p>{{ $leave->emergency_contact_relationship ?? 'N/A' }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="tab-pane fade" id="leave-balance-tab">
                <div class="p-4 text-center text-muted">No leave balance information available.</div>
            </div>

            <div class="tab-pane fade" id="attachments-remarks-tab">
                <div class="row text-center">
                    <div class="col-xxl-3 col-lg-4 col-md-6 col-12 mb-3 green-heading">
                        <p class="mb-0" title="Supporting Documents">Docs</p>
                        @if (!empty($leave->file))
                            <a href="{{ \Illuminate\Support\Facades\Storage::url($leave->file) }}" target="_blank"
                                class="text-success">View File</a>
                        @else
                            -
                        @endif
                    </div>
                    <div class="col-xxl-3 col-lg-4 col-md-6 col-12 mb-3 green-heading">
                        <p class="mb-0" title="Travel Ticket Attached">Ticket</p>
                        @if (!empty($leave->travel_ticket_file))
                            <a href="{{ \Illuminate\Support\Facades\Storage::url($leave->travel_ticket_file) }}"
                                target="_blank" class="text-success">View Ticket</a>
                        @else
                            -
                        @endif
                    </div>
                    <div class="col-xxl-3 col-lg-4 col-md-3 col-12 mb-3 green-heading">
                        <p class="mb-0">Remarks</p>{{ $leave->note ?: '-' }}
                    </div>
                    <div class="col-xxl-3 col-lg-4 col-md-3 col-12 mb-3 green-heading">
                        <p class="mb-0">Submitted At</p>{{ $fmtDate($leave->submitted_at, 'd/m/Y h:i A') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @if($leave->chain && $leave->chain->steps && $leave->chain->steps->count())
            @foreach($leave->chain->steps as $step)
                @php
                    $stageRole = strtolower((string) $step->role);
                    if ((strpos($stageRole, 'management') !== false || strpos($stageRole, 'finance') !== false) && $step->status === 'S') {
                        continue;
                    }
                    $stageName = strpos($stageRole, 'report') !== false ? 'Reporting Manager' : ($stageRole === 'hr' ? 'HR' : 'Management');
                    $displayStatus = ($stageName === 'HR' && $step->status === 'S') ? 'P' : $step->status;
                    $isYou = ((int) ($step->approver_id ?? 0) === (int) Auth::id());
                    $canAct = $trackMode && $isYou && $step->status === 'P' && (int) $step->id === (int) $firstPendingStepId;
                    if ($trackMode && in_array((int) Auth::user()->role_id, [1, 2], true) && $step->status === 'P' && (int) $step->id === (int) $firstPendingStepId) {
                        $canAct = true;
                    }
                    $actedByLabel = $step->status === 'A' ? 'Approved By' : ($step->status === 'R' ? 'Rejected By' : ($step->status === 'C' ? 'Returned By' : null));
                    $actedAtLabel = $step->status === 'A' ? 'Approved At' : ($step->status === 'R' ? 'Rejected At' : ($step->status === 'C' ? 'Returned At' : null));
                    $approverName = optional($step->approver)->full_name ?: trim(optional($step->approver)->first_name . ' ' . optional($step->approver)->last_name);
                    if (!$approverName && $step->approver_id) {
                        $fallbackUser = \App\User::find($step->approver_id);
                        if ($fallbackUser) {
                            $approverName = $fallbackUser->full_name ?: trim($fallbackUser->first_name . ' ' . $fallbackUser->last_name);
                        }
                    }
                @endphp
                <div class="col p-1">
                    <div class="card mb-3">
                        <table class="detail-item-table-sm" width="100%" style="table-layout: fixed;width:100%">
                            <tr>
                                <td class="{{ $stepStatusClass($displayStatus) }} d-flex align-items-center justify-content-start gap-1"
                                    style="height:23px; padding: 0 15px;">
                                    <div
                                        class="d-flex align-items-center justify-content-start flex-grow-1 gap-1 header-height">
                                        <b>{{ $stageName }}</b>
                                        @if($canAct)
                                            <a class="btn-md light" style="display: contents; cursor: pointer;"
                                                data-bs-toggle="modal" data-bs-target="#approvalActionModal"
                                                data-leave-id="{{ $leave->id }}" data-step-id="{{ $step->id }}"
                                                data-role="{{ $stageName }}">
                                                <i class="ico icon-outline-pen-new-square title-15 {{ $step->status === 'P' ? 'text-dark' : 'text-white' }}"
                                                    title="Approval Action" style="font-size: 12px"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-start truncate-text-custom" style="padding: 10px 15px;">
                                    <span class="fw-bold">Status</span> : {{ $stepStatusText($displayStatus) }}
                                    {!! $stepIcon($displayStatus) !!}
                                </td>
                            </tr>
                            @if($stageName === 'HR' && $step->status !== 'P')
                                <tr>
                                    <td class="text-start truncate-text-custom" style="padding: 5px 15px;"><span
                                            class="fw-bold">Leave Balance Verification</span> :
                                        {{ $step->l2_balance_verify ?: '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-start truncate-text-custom" style="padding: 5px 15px;"><span
                                            class="fw-bold">Policy Compliance</span> : {{ $step->l2_policy_verify ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-start truncate-text-custom" style="padding: 5px 15px;"><span
                                            class="fw-bold">Documentation Verified</span> : {{ $step->l2_docs_verify ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-start truncate-text-custom" style="padding: 5px 15px;"><span
                                            class="fw-bold">Management Approval Req</span> :
                                        {{ $leave->management_approval_req ?: 'No' }}
                                    </td>
                                </tr>
                            @endif
                            @if($stageName === 'Management' && $step->status !== 'P')
                                <tr>
                                    <td class="text-start truncate-text-custom" style="padding: 5px 15px;"><span
                                            class="fw-bold">Leave Exceeds Limits</span> : {{ $step->l3_limits ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-start truncate-text-custom" style="padding: 5px 15px;"><span
                                            class="fw-bold">Critical Role</span> : {{ $step->l3_critical ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-start truncate-text-custom" style="padding: 5px 15px;"><span
                                            class="fw-bold">Blackout Period</span> : {{ $step->l3_blackout ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-start truncate-text-custom" style="padding: 5px 15px;"><span
                                            class="fw-bold">Exceptional Circumstances</span> : {{ $step->l3_exceptional ?: '-' }}
                                    </td>
                                </tr>
                            @endif
                            @if($step->comment && $step->status !== 'P')
                                <tr>
                                    <td class="text-start truncate-text-custom" style="padding: 5px 15px;"><span
                                            class="fw-bold">Remarks</span> : {{ $step->comment }}</td>
                                </tr>
                            @endif
                            @if($step->acted_at && $step->status !== 'P')
                                @if($actedByLabel)
                                    <tr>
                                        <td class="text-start truncate-text-custom" style="padding: 5px 15px;"><span
                                                class="fw-bold">{{ $actedByLabel }}</span> : {{ $approverName ?: '-' }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td class="text-start truncate-text-custom" style="padding: 5px 15px;"><span
                                            class="fw-bold">{{ $actedAtLabel ?: 'Action At' }}</span> :
                                        {{ $fmtDate($step->acted_at, 'd/m/Y h:i A') }}
                                    </td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>
            @endforeach
        @else
            <div class="p-4 text-center text-muted col-12">No approval information available.</div>
        @endif
    </div>
</div>

@if($trackMode)
    <div class="modal fade" id="approvalActionModal" tabindex="-1" aria-hidden="true" style="background: rgba(0,0,0,0.5);"
        data-bs-backdrop="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('approvals.action') }}" id="approvalActionForm">
                    @csrf
                    <input type="hidden" name="leave_id" id="act_leave_id">
                    <input type="hidden" name="step_id" id="act_step_id">
                    <input type="hidden" name="actor_role" id="act_role">

                    <div class="modal-header m-0">
                        <h5 class="modal-title">Approval Action</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="level-section level-l1 d-none">
                            <h6 class="mb-3">Reporting Manager Approval</h6>
                            <div class="row g-3">
                                <div class="col-12 col-sm-6"><label class="form-label">Approval <span
                                            class="text-danger">*</span></label><select class="form-select"
                                        name="l1_decision" required>
                                        <option value="Approve">Approve</option>
                                        <option value="Reject">Reject</option>
                                    </select></div>
                                <!-- <div class="col-12 col-sm-6"><label class="form-label">Recommended Action</label><input type="text" class="form-control" name="l1_recommended_action"></div> -->
                                <div class="col-12"><label class="form-label">Remarks</label><textarea class="form-control"
                                        name="l1_remark" rows="2"></textarea></div>
                            </div>
                            <h6 class="mb-3 mt-4">Manager Review Checks</h6>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="form-check"><input class="form-check-input l1-check" type="checkbox"
                                            name="l1_coverage" value="Checked" id="l1_coverage" required><label
                                            class="form-check-label" for="l1_coverage">Team staffing availability</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-check"><input class="form-check-input l1-check" type="checkbox"
                                            name="l1_workload" value="Checked" id="l1_workload" required><label
                                            class="form-check-label" for="l1_workload">Operational impact</label></div>
                                </div>
                                <div class="col-12">
                                    <div class="form-check"><input class="form-check-input l1-check" type="checkbox"
                                            name="l1_duration_ok" value="Checked" id="l1_duration_ok" required><label
                                            class="form-check-label" for="l1_duration_ok">Project deadlines</label></div>
                                </div>
                                <div class="col-12">
                                    <div class="form-check"><input class="form-check-input l1-check" type="checkbox"
                                            name="l1_notice_compliance" value="Checked" id="l1_notice_compliance"
                                            required><label class="form-check-label" for="l1_notice_compliance">Adequacy of
                                            work handover</label></div>
                                </div>
                                <div class="col-12">
                                    <div class="form-check"><input class="form-check-input l1-check" type="checkbox"
                                            name="l1_eligibility" value="Checked" id="l1_eligibility" required><label
                                            class="form-check-label" for="l1_eligibility">Leave pattern review</label></div>
                                </div>
                                <div class="col-12">
                                    <div class="form-check"><input class="form-check-input l1-check" type="checkbox"
                                            name="l1_emergency" value="Checked" id="l1_emergency" required><label
                                            class="form-check-label" for="l1_emergency">Emergency justification</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="level-section level-l2 d-none">
                            <h6 class="mb-3" id="level_l2_title">HR Approval</h6>
                            <div class="row g-3">
                                <div class="col-12 col-sm-6"><label class="form-label">HR Approval Status <span
                                            class="text-danger">*</span></label><select class="form-select"
                                        name="l2_decision" required>
                                        <option value="Approve">Approve</option>
                                        <option value="Reject">Reject</option>
                                    </select></div>
                                <div class="col-12 col-sm-6"><label class="form-label">Leave Balance Verification <span
                                            class="text-danger">*</span></label><select class="form-select"
                                        name="l2_balance_verify">
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select></div>
                                <div class="col-12 col-sm-6"><label class="form-label">Policy Compliance <span
                                            class="text-danger">*</span></label><select class="form-select"
                                        name="l2_policy_verify">
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select></div>
                                <div class="col-12 col-sm-6"><label class="form-label">Documentation Verified <span
                                            class="text-danger">*</span></label><select class="form-select"
                                        name="l2_docs_verify">
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select></div>
                                <div class="col-12 col-sm-6"><label class="form-label">Management Approval Req <span
                                            class="text-danger">*</span></label><select class="form-select"
                                        name="management_approval_req">
                                        <option value="No">No</option>
                                        <option value="Yes">Yes</option>
                                    </select></div>
                                <div class="col-12"><label class="form-label">Remarks</label><textarea class="form-control"
                                        name="l2_remark" rows="2"></textarea></div>
                            </div>
                            <h6 class="mb-3 mt-4">HR Checks</h6>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="form-check"><input class="form-check-input l2-check" type="checkbox"
                                            name="l2_cost" value="Checked" id="l2_cost" required><label
                                            class="form-check-label" for="l2_cost">Leave eligibility</label></div>
                                </div>
                                <div class="col-12">
                                    <div class="form-check"><input class="form-check-input l2-check" type="checkbox"
                                            name="l2_unpaid" value="Checked" id="l2_unpaid" required><label
                                            class="form-check-label" for="l2_unpaid">Service period validation</label></div>
                                </div>
                                <div class="col-12">
                                    <div class="form-check"><input class="form-check-input l2-check" type="checkbox"
                                            name="l2_balance" value="Checked" id="l2_balance" required><label
                                            class="form-check-label" for="l2_balance">Leave entitlement availability</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-check"><input class="form-check-input l2-check" type="checkbox"
                                            name="l2_docs" value="Checked" id="l2_docs" required><label
                                            class="form-check-label" for="l2_docs">Supporting documents verification</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-check"><input class="form-check-input l2-check" type="checkbox"
                                            name="l2_policy" value="Checked" id="l2_policy" required><label
                                            class="form-check-label" for="l2_policy">Statutory compliance</label></div>
                                </div>
                                <div class="col-12">
                                    <div class="form-check"><input class="form-check-input l2-check" type="checkbox"
                                            name="l2_encash" value="Checked" id="l2_encash" required><label
                                            class="form-check-label" for="l2_encash">Previous leave history review</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="level-section level-l3 d-none">
                            <h6 class="mb-3">Management Approval</h6>
                            <div class="row g-3">
                                <div class="col-12 col-sm-6"><label class="form-label">Final Approval Status <span
                                            class="text-danger">*</span></label><select class="form-select"
                                        name="l3_decision">
                                        <option value="Approve">Approve</option>
                                        <option value="Reject">Reject</option>
                                    </select></div>
                                <div class="col-12"><label class="form-label">Remarks</label><textarea class="form-control"
                                        name="l3_remark" rows="2"></textarea></div>
                            </div>
                            <h6 class="mb-3 mt-4">Management Required-When Checks</h6>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="form-check"><input class="form-check-input l3-check" type="checkbox"
                                            name="l3_limits" value="Checked" id="l3_limits" required><label
                                            class="form-check-label" for="l3_limits">Leave exceeds predefined limits</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-check"><input class="form-check-input l3-check" type="checkbox"
                                            name="l3_critical" value="Checked" id="l3_critical" required><label
                                            class="form-check-label" for="l3_critical">Critical role employees apply</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-check"><input class="form-check-input l3-check" type="checkbox"
                                            name="l3_blackout" value="Checked" id="l3_blackout" required><label
                                            class="form-check-label" for="l3_blackout">Leave during blackout periods</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-check"><input class="form-check-input l3-check" type="checkbox"
                                            name="l3_exceptional" value="Checked" id="l3_exceptional" required><label
                                            class="form-check-label" for="l3_exceptional">Exceptional circumstances</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer p-2">
                        <button type="submit" class="btn btn-light add-btn ms-2"><i
                                class="ico icon-outline-send-square text-success"></i> Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        (function () {
            var modal = document.getElementById('approvalActionModal');
            if (!modal) return;

            modal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var role = button.getAttribute('data-role') || '';
                document.getElementById('act_role').value = role;
                document.getElementById('act_step_id').value = button.getAttribute('data-step-id');
                document.getElementById('act_leave_id').value = button.getAttribute('data-leave-id');

                modal.querySelectorAll('.level-section').forEach(function (el) { el.classList.add('d-none'); });
                modal.querySelectorAll('input, select, textarea').forEach(function (el) {
                    el.removeAttribute('required');
                });
                if (role.toLowerCase().indexOf('report') !== -1 || role.toLowerCase().indexOf('manager') !== -1) {
                    modal.querySelector('.level-l1').classList.remove('d-none');
                    modal.querySelector('[name="l1_decision"]').setAttribute('required', 'required');
                    modal.querySelectorAll('.l1-check').forEach(function (el) { el.setAttribute('required', 'required'); });
                } else if (role.toLowerCase().indexOf('hr') !== -1) {
                    modal.querySelector('.level-l2').classList.remove('d-none');
                    modal.querySelector('[name="l2_decision"]').setAttribute('required', 'required');
                    modal.querySelector('[name="l2_balance_verify"]').setAttribute('required', 'required');
                    modal.querySelector('[name="l2_policy_verify"]').setAttribute('required', 'required');
                    modal.querySelector('[name="l2_docs_verify"]').setAttribute('required', 'required');
                    modal.querySelector('[name="management_approval_req"]').setAttribute('required', 'required');
                    modal.querySelectorAll('.l2-check').forEach(function (el) { el.setAttribute('required', 'required'); });
                } else {
                    modal.querySelector('.level-l3').classList.remove('d-none');
                    modal.querySelector('[name="l3_decision"]').setAttribute('required', 'required');
                    modal.querySelectorAll('.l3-check').forEach(function (el) { el.setAttribute('required', 'required'); });
                }
            });
        })();
    </script>
@endif

@if($canEditHandover ?? false)
    <div class="modal fade" id="handoverFieldEditModal" tabindex="-1" aria-hidden="true"
        style="background: rgba(0,0,0,0.5); z-index: 1060;" data-bs-backdrop="false">
        <div class="modal-dialog modal-dialog-centered" style="z-index: 1061; max-width: 380px;">
            <div class="modal-content">
                <form action="{{ route('approvals.handover_update') }}" method="POST" id="handoverFieldEditForm"
                    class="handover-update-form">
                    @csrf
                    <input type="hidden" name="leave_id" value="{{ $leave->id }}">
                    <div class="modal-header m-0">
                        <h5 class="modal-title" id="handoverFieldEditTitle">Update Handover Field</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="handoverSelectWrap">
                            <label class="form-label" for="handoverFieldSelect">Value</label>
                            <select class="form-select" id="handoverFieldSelect">
                                <option value="No">No</option>
                                <option value="Yes">Yes</option>
                            </select>
                        </div>
                        <div id="handoverTextareaWrap" class="d-none">
                            <label class="form-label" for="handoverFieldTextarea">Value</label>
                            <textarea class="form-control" id="handoverFieldTextarea" rows="2"
                                style="min-height: 60px;"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer p-2">
                        <button type="submit" class="btn btn-light add-btn ms-2"><i
                                class="ico icon-outline-send-square text-success"></i> Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="handoverClientRespEditModal" tabindex="-1" aria-hidden="true"
        style="background: rgba(0,0,0,0.5); z-index: 1060;" data-bs-backdrop="false">
        <div class="modal-dialog modal-dialog-centered" style="z-index: 1061; max-width: 380px;">
            <div class="modal-content">
                <form action="{{ route('approvals.handover_update') }}" method="POST" class="handover-update-form">
                    @csrf
                    <input type="hidden" name="leave_id" value="{{ $leave->id }}">
                    <div class="modal-header m-0">
                        <h5 class="modal-title">Update Client Responsibilities</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label" for="handoverClientResponsibilitiesInput">Client Responsibilities</label>
                        <textarea class="form-control" id="handoverClientResponsibilitiesInput"
                            name="client_responsibilities" rows="2"
                            style="min-height: 70px;">{{ data_get($leave, 'client_responsibilities') }}</textarea>
                    </div>
                    <div class="modal-footer p-2">
                        <button type="submit" class="btn btn-light add-btn ms-2"><i
                                class="ico icon-outline-send-square text-success"></i> Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="handoverRemarksEditModal" tabindex="-1" aria-hidden="true"
        style="background: rgba(0,0,0,0.5); z-index: 1060;" data-bs-backdrop="false">
        <div class="modal-dialog modal-dialog-centered" style="z-index: 1061; max-width: 380px;">
            <div class="modal-content">
                <form action="{{ route('approvals.handover_update') }}" method="POST" class="handover-update-form">
                    @csrf
                    <input type="hidden" name="leave_id" value="{{ $leave->id }}">
                    <div class="modal-header m-0">
                        <h5 class="modal-title">Update Remarks</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label" for="handoverRemarksInput">Remarks</label>
                        <textarea class="form-control" id="handoverRemarksInput" name="handover_additional_remarks" rows="2"
                            style="min-height: 70px;">{{ data_get($leave, 'handover_additional_remarks') }}</textarea>
                    </div>
                    <div class="modal-footer p-2">
                        <button type="submit" class="btn btn-light add-btn ms-2"><i
                                class="ico icon-outline-send-square text-success"></i> Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        (function () {
            var textFields = ['client_responsibilities', 'additional_remarks', 'remarks', 'handover_additional_remarks'];
            var selectFields = ['pending_tasks', 'access_transfer_required', 'completion_confirmation', 'handover_completion_confirmation', 'manager_verification', 'manager_verification_of_handover'];

            function configureHandoverFieldModal(button) {
                if (button && button.closest) {
                    button = button.closest('.handover-field-edit');
                }
                if (!button || button.getAttribute('data-bs-target') !== '#handoverFieldEditModal') return;

                var title = button.getAttribute('data-title') || 'Update Handover Field';
                var field = button.getAttribute('data-field') || '';
                var type = textFields.indexOf(field) !== -1 ? 'textarea' : (selectFields.indexOf(field) !== -1 ? 'select' : (button.getAttribute('data-type') || 'select'));
                var value = button.getAttribute('data-value') || '';
                var selectWrap = document.getElementById('handoverSelectWrap');
                var textareaWrap = document.getElementById('handoverTextareaWrap');
                var select = document.getElementById('handoverFieldSelect');
                var textarea = document.getElementById('handoverFieldTextarea');

                document.getElementById('handoverFieldEditTitle').textContent = title;
                select.removeAttribute('name');
                textarea.removeAttribute('name');

                if (type === 'textarea') {
                    selectWrap.classList.add('d-none');
                    textareaWrap.classList.remove('d-none');
                    textarea.setAttribute('name', field);
                    textarea.value = value;
                } else {
                    textareaWrap.classList.add('d-none');
                    selectWrap.classList.remove('d-none');
                    select.setAttribute('name', field);
                    select.value = value === 'Yes' ? 'Yes' : 'No';
                }
            }

            $(document)
                .off('click.handoverFieldEdit')
                .on('click.handoverFieldEdit', '.handover-field-edit', function () {
                    $('#handoverFieldEditModal').data('handover-trigger', this);
                    configureHandoverFieldModal(this);
                });

            $(document)
                .off('show.bs.modal.handoverFieldEdit')
                .on('show.bs.modal.handoverFieldEdit', '#handoverFieldEditModal', function (event) {
                    var button = event.relatedTarget || (event.originalEvent && event.originalEvent.relatedTarget) || $(this).data('handover-trigger') || document.activeElement;
                    configureHandoverFieldModal(button);
                });

            $(document)
                .off('hidden.bs.modal.handoverFieldEdit')
                .on('hidden.bs.modal.handoverFieldEdit', '#handoverFieldEditModal', function () {
                    $('.modal-backdrop').remove();
                    if (!$('.modal.show').length) {
                        $('body').removeClass('modal-open').css({ overflow: '', paddingRight: '' });
                    }
                });

            $(document)
                .off('submit.handoverFieldAjax')
                .on('submit.handoverFieldAjax', '.handover-update-form', function (event) {
                    event.preventDefault();

                    var form = $(this);
                    var button = form.find('button[type="submit"]');
                    var originalHtml = button.html();

                    button.prop('disabled', true);

                    $.ajax({
                        url: form.attr('action'),
                        method: 'POST',
                        data: form.serialize(),
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        success: function () {
                            var updatedField = null;
                            var updatedValue = '';

                            form.serializeArray().forEach(function (item) {
                                if (item.name !== '_token' && item.name !== 'leave_id') {
                                    updatedField = item.name;
                                    updatedValue = item.value;
                                }
                            });

                            if (updatedField) {
                                $('[data-handover-field="' + updatedField + '"]').text(updatedValue || '-');
                                $('[data-field="' + updatedField + '"]').attr('data-value', updatedValue);
                            }

                            var modalEl = form.closest('.modal')[0];
                            if (modalEl && window.bootstrap && bootstrap.Modal.getInstance(modalEl)) {
                                bootstrap.Modal.getInstance(modalEl).hide();
                            } else {
                                form.closest('.modal').modal('hide');
                            }

                            $('.modal-backdrop').remove();
                            if (!$('.modal.show').length) {
                                $('body').removeClass('modal-open').css({ overflow: '', paddingRight: '' });
                            }
                        },
                        error: function (xhr) {
                            var message = 'Unable to update handover information.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }
                            alert(message);
                        },
                        complete: function () {
                            button.prop('disabled', false).html(originalHtml);
                        }
                    });
                });
        })();
    </script>
@endif
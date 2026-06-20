@php
    use Carbon\Carbon;
    use Illuminate\Support\Str;

    $auth = Auth::user();
    $typeMap = isset($loanTypes) ? $loanTypes->pluck('name', 'id')->toArray() : [
        1 => 'Salary Advance', 2 => 'Personal Loan', 3 => 'Emergency Loan', 4 => 'Festival Advance'
    ];
    $staff = $loan->staffDetails;
    $employeeName = optional($staff)->full_name ?: trim(optional($staff)->first_name . ' ' . optional($staff)->last_name);
    $loanNumber = $loan->document_number;
    $loanPermissions = $loanPermissions ?? ['create' => true, 'view' => true, 'edit' => true, 'delete' => true, 'export' => true, 'attach' => true];
    $trackPermissions = $trackPermissions ?? ['create' => true, 'view' => true, 'edit' => true, 'delete' => true, 'export' => true, 'attach' => true];
    $reportPermissions = $reportPermissions ?? ['create' => true, 'view' => true, 'edit' => true, 'delete' => true, 'export' => true, 'attach' => true];

    $managementRequired = false;
    if ($loan->hr_management_approval_req === 'Yes') {
        $managementRequired = true;
    } elseif ($loan->hr_management_approval_req === 'No') {
        $managementRequired = false;
    } elseif ($loan->finance_management_approval_req === 'Yes') {
        $managementRequired = true;
    }

    $nextStage = null;
    if ($loan->status === 'Draft') {
        $nextStage = null;
    } elseif (($loan->manager_approval ?: 'Pending') === 'Pending') {
        $nextStage = 'manager';
    } elseif ($loan->manager_approval !== 'Rejected' && ($loan->finance_approval ?: 'Pending') === 'Pending') {
        $nextStage = 'finance';
    } elseif ($loan->finance_approval !== 'Rejected' && ($loan->hr_approval ?: 'Pending') === 'Pending') {
        $nextStage = 'hr';
    } elseif ($loan->hr_approval !== 'Rejected' && $managementRequired && ($loan->management_approval ?: 'Pending') === 'Pending') {
        $nextStage = 'management';
    } elseif (!in_array('Rejected', [$loan->manager_approval, $loan->finance_approval, $loan->hr_approval, $loan->management_approval]) && ($loan->payment_approval ?: 'Pending') === 'Pending') {
        $nextStage = 'payment';
    }

    $managerIds = [];
    if ($staff && !empty($staff->reporting_manager)) {
        $managerIds = array_map('trim', explode(',', (string) $staff->reporting_manager));
    }
    $canTrackEdit = !empty($trackPermissions['edit']);
    $canAct = in_array($auth->role_id, [1, 2]) ||
        ($canTrackEdit && (
            ($nextStage === 'manager' && ($auth->role_id == 8 || in_array($auth->id, $managerIds) || in_array($auth->role_id, $managerIds))) ||
            ($nextStage === 'finance' && $auth->role_id == 27) ||
            ($nextStage === 'hr' && $auth->role_id == 3) ||
            ($nextStage === 'management' && !in_array($auth->role_id, [3, 8, 27, 28])) ||
            ($nextStage === 'payment' && $auth->role_id == 28)
        ));

    $stageTitle = [
        'manager' => 'Reporting Approval',
        'finance' => 'Finance Approval',
        'hr' => 'HR Approval',
        'management' => 'Management Approval',
        'payment' => 'Payment Processing',
    ][$nextStage] ?? '';

    if (!function_exists('loanStatusClass')) {
        function loanStatusClass($status) {
            if ($status === 'Approved') return 'bg-success text-white';
            if ($status === 'Rejected') return 'bg-danger text-white';
            if ($status === 'Returned') return 'bg-warning text-dark';
            return 'bg-lightgreen text-dark';
        }
    }
@endphp

<style>
    #loan-details label {
        font-weight: 600 !important;
        background-color: #deebe1 !important;
        margin-bottom: 3px !important;
        text-align: center !important;
        color: #212529 !important;
    }
    #loan-details .green-heading p {
        font-weight: 600 !important;
        background-color: #deebe1 !important;
        margin-bottom: 3px !important;
        text-align: center !important;
        color: #212529 !important;
    }
    #loan-details .green-heading { text-align: center !important; }
    #loan-details .form-control-plaintext { text-align: center !important; }
    #loan-details .detail-item-table-sm td { text-align: start !important; }
    #modalLoanApproval label,
    #modalLoanApproval .form-check-label {
        background: transparent !important;
        color: #212529 !important;
        display: block;
        font-weight: 500 !important;
        font-size: 14px;
        margin-bottom: 2px !important;
        text-align: left !important;
    }
    #modalLoanApproval .modal-body {
        padding: 6px 12px;
        overflow-y: auto;
    }
    #modalLoanApproval .modal-content {
        max-height: calc(100vh - 60px);
    }
    #modalLoanApproval .loan-reporting-approval-dialog {
        max-width: 700px;
    }
    #modalLoanApproval .loan-management-approval-dialog {
        max-width: 640px;
    }
    #modalLoanApproval .form-control {
        height: 29px;
        min-height: 29px;
        padding-top: 2px;
        padding-bottom: 2px;
        font-size: 14px;
    }
    #modalLoanApproval textarea.form-control {
        height: auto;
    }
    #modalLoanApproval .form-check-label {
        display: inline-block !important;
        margin-left: 4px;
        font-size: 14px;
    }
    #modalLoanApproval .loan-approval-checks .border {
        background: #fff;
    }
    #modalLoanApproval .loan-approval-checks .form-check {
        min-height: 20px;
        padding-top: 0;
        padding-bottom: 0;
    }
    #modalLoanApproval .loan-reporting-fields {
        align-items: flex-start;
    }
    #modalLoanApproval .loan-reporting-fields textarea.form-control {
        width: 100%;
        min-height: 76px;
    }
    #modalLoanApproval .loan-reporting-fields .loan-approval-checks .border {
        display: inline-block;
        width: 100%;
    }
    .bg-lightgreen { background-color: #deebe1 !important; }
    .track-action-btn {
        border-radius: 4px;
        border: 1px solid rgba(255,255,255,0.4);
        background: rgba(255,255,255,0.2);
        color: white;
        font-size: 0.8rem;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }
    .track-action-btn:hover {
        background: rgba(255,255,255,0.35);
        color: white;
        border-color: rgba(255,255,255,0.6);
    }
    .track-stage-actions {
        display: flex;
        gap: 0.3rem;
        align-items: center;
    }
</style>

<div id="loan-details">

<div class="purchase-order-content-header sticky-top" style="background-color:#f7f8fd">
    <h4 class="purchase-order-content-header-left">{{ $loanNumber }}</h4>
    <div class="purchase-order-content-header-right d-flex align-items-center">
        @if(!($isApprovals ?? false))
            @if(!empty($loanPermissions['edit']) && (!in_array($loan->status, ['Approved','Disbursed','Rejected']) || in_array($auth->role_id, [1,2])))
                <button type="button" class="btn btn-light text-dark loan-edit-btn" data-id="{{ $loan->id }}">
                    <i class="ico icon-outline-pen-2 text-success"></i> Edit
                </button>
            @endif
            @if(!empty($loanPermissions['create']))
            <button type="button" class="btn btn-light text-dark loan-add-btn">
                <i class="ico icon-outline-add-square text-success"></i> Add
            </button>
            @endif
        @else
            <a href="{{ url('employee/loans?active=' . $loan->id) }}" target="_blank" class="btn btn-light text-dark">
                <i class="ico icon-outline-eye text-success"></i> View
            </a>
        @endif
        <div class="dropdown" style="display:inline-block;margin-left:5px;">
            <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="ico icon-outline-hamburger-menu"></i>
            </button>
            <ul class="dropdown-menu">
                @if(!empty($loanPermissions['export']))
                <li><a class="dropdown-item" href="{{ route('employee.loans.export', request()->query()) }}"><i class="ico icon-outline-export text-success"></i> Download / Export</a></li>
                @endif
                @if(!empty($trackPermissions['view']))
                <li>
                    @if($loan->status === 'Draft')
                        <a class="dropdown-item" href="javascript:void(0)" onclick="alert('Draft requests are not available in Loan Track. Please submit for approval first.')">
                            <i class="ico icon-outline-list-down text-success"></i> Loan Track
                        </a>
                    @else
                        <a class="dropdown-item" href="{{ route('employee.loans.approvals', $loan->id) }}">
                            <i class="ico icon-outline-list-down text-success"></i> Loan Track
                        </a>
                    @endif
                </li>
                @endif
                @if(!empty($reportPermissions['view']))
                <li><a class="dropdown-item" href="{{ route('employee.loans.report') }}"><i class="ico icon-outline-document-text text-success"></i> Loan Report</a></li>
                @endif
            </ul>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <div class="row">
            <div class="col-2 mb-2">
                <label class="form-label">Request Number</label>
                <div class="form-control-plaintext truncate-text-custom">{{ $loanNumber }}</div>
            </div>
            <div class="col-2 mb-2">
                <label class="form-label">Employee</label>
                <div class="form-control-plaintext truncate-text-custom">{{ $employeeName ?: 'N/A' }}</div>
            </div>
            <div class="col-2 mb-2">
                <label class="form-label">Department</label>
                <div class="form-control-plaintext truncate-text-custom">{{ optional(optional($staff)->departments)->name ?: 'N/A' }}</div>
            </div>
            <div class="col-2 mb-2">
                <label class="form-label">Designation</label>
                <div class="form-control-plaintext truncate-text-custom">{{ optional(optional($staff)->designations)->title ?: 'N/A' }}</div>
            </div>
            <div class="col-2 mb-2">
                <label class="form-label">Loan Amount</label>
                <div class="form-control-plaintext truncate-text-custom">{{ number_format((float)$loan->amount, 2) }}</div>
            </div>
            <div class="col-2 mb-2">
                <label class="form-label">Loan Status</label>
                <div class="form-control-plaintext truncate-text-custom">{{ $loan->status ?: 'Pending' }}</div>
            </div>
            <div class="col-2 mb-2">
                <label class="form-label">Request Type</label>
                <div class="form-control-plaintext truncate-text-custom">{{ $loan->request_type ?: ($typeMap[$loan->type_id] ?? '-') }}</div>
            </div>
            <div class="col-2 mb-2">
                <label class="form-label">Applied On</label>
                <div class="form-control-plaintext truncate-text-custom">{{ optional($loan->created_at)->format('d/m/Y') ?: '-' }}</div>
            </div>
        </div>
    </div>
</div>

<div class="tab-wrap mb-3">
    <ul class="nav nav-tabs" id="loanDetailsTabs" role="tablist">
        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#loan-details-tab" type="button">Loan / Advance Details</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#repayment-details-tab" type="button">Repayment Details</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#guarantor-details-tab" type="button">Guarantor Details</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#attachments-tab" type="button">Attachments / Remarks</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#payment-tab" type="button">Payment Processing</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#history-tab" type="button">Loan History</button></li>
    </ul>

    <div class="tab-content mb-3">
        <div class="tab-pane fade show active" id="loan-details-tab">
            <div class="row text-center">
                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading"><p class="mb-0">Request Type</p>{{ $loan->request_type ?: ($typeMap[$loan->type_id] ?? '-') }}</div>
                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading"><p class="mb-0">Loan Category</p>{{ $loan->loan_category ?: ($typeMap[$loan->type_id] ?? '-') }}</div>
                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading"><p class="mb-0">Request Date</p>{{ $loan->date ? date('d/m/Y', strtotime($loan->date)) : optional($loan->created_at)->format('d/m/Y') }}</div>
                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading"><p class="mb-0">Loan Amount</p>{{ number_format((float)$loan->amount, 2) }}</div>
                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading"><p class="mb-0">Purpose</p>{{ $loan->purpose ?: '-' }}</div>
                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading"><p class="mb-0">Urgency Level</p>{{ $loan->urgency_level ?: (Str::contains(strtolower($loan->purpose), 'urgent') ? 'Urgent' : 'Normal') }}</div>
                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading"><p class="mb-0">Current Status</p>{{ $loan->status ?: 'Pending' }}</div>
                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading"><p class="mb-0">Disbursement Date</p>{{ $loan->requested_disbursement_date ? date('d/m/Y', strtotime($loan->requested_disbursement_date)) : '-' }}</div>
            </div>
        </div>

        <div class="tab-pane fade" id="repayment-details-tab">
            <div class="row text-center">
                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading"><p class="mb-0">Installments</p>{{ $loan->installments ?: '-' }}</div>
                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading"><p class="mb-0">Monthly Deduction</p>{{ number_format((float)$loan->amount_per_month, 2) }}</div>
                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading"><p class="mb-0">Repayment Start</p>{{ $loan->repayment_start ? Carbon::parse($loan->repayment_start)->format('M Y') : '-' }}</div>
                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading"><p class="mb-0">Repayment End</p>{{ $loan->repayment_end_month ? Carbon::parse($loan->repayment_end_month)->format('M Y') : '-' }}</div>
                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading"><p class="mb-0">Repayment Mode</p>{{ $loan->repayment_mode ?: '-' }}</div>
                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading"><p class="mb-0">Early Settlement</p>{{ $loan->early_settlement_allowed ?: '-' }}</div>
                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading"><p class="mb-0">Grace Period</p>{{ $loan->grace_period_required === 'Yes' ? ($loan->grace_period_months . ' month(s)') : ($loan->grace_period_required ?: '-') }}</div>
                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading"><p class="mb-0">Finance Approved Amount</p>{{ $loan->finance_approved_amount ? number_format((float)$loan->finance_approved_amount, 2) : '-' }}</div>
            </div>
        </div>

        <div class="tab-pane fade" id="guarantor-details-tab">
            <div class="row text-center">
                @php $guarantor = $loan->guarantor_employee_id ? \App\SmStaff::find($loan->guarantor_employee_id) : null; @endphp
                <div class="col-xxl-3 col-lg-4 col-md-6 col-12 mb-3 green-heading"><p class="mb-0">Guarantor Name</p>{{ optional($guarantor)->full_name ?: '-' }}</div>
                <div class="col-xxl-3 col-lg-4 col-md-6 col-12 mb-3 green-heading"><p class="mb-0">Guarantor Employee ID</p>{{ $loan->guarantor_employee_no ?: '-' }}</div>
                <div class="col-xxl-3 col-lg-4 col-md-6 col-12 mb-3 green-heading"><p class="mb-0">Guarantor Department</p>{{ $loan->guarantor_department ?: '-' }}</div>
                <div class="col-xxl-3 col-lg-4 col-md-6 col-12 mb-3 green-heading"><p class="mb-0">Guarantor Contact</p>{{ $loan->guarantor_contact_number ?: '-' }}</div>
            </div>
        </div>

        <div class="tab-pane fade" id="attachments-tab">
            <div class="row text-center">
                <div class="col-xxl-3 col-lg-4 col-md-6 col-12 mb-3 green-heading">
                    <p class="mb-0">Attachment</p>
                    @if($loan->attachment)
                        <a class="text-success" target="_blank" href="{{ asset('uploads/loan_docs/'.$loan->attachment) }}">{{ basename($loan->attachment) }}</a>
                    @else
                        -
                    @endif
                </div>
                <div class="col-xxl-4 col-lg-6 col-md-12 col-12 mb-3 green-heading"><p class="mb-0">Attachment Remarks</p>{{ $loan->attachment_remarks ?: '-' }}</div>
                <div class="col-xxl-4 col-lg-6 col-md-12 col-12 mb-3 green-heading"><p class="mb-0">Remarks</p>{{ $loan->note ?: '-' }}</div>
                <div class="col-xxl-4 col-lg-6 col-md-12 col-12 mb-3 green-heading"><p class="mb-0">Declaration Accepted</p>{{ $loan->declaration_accepted_at ? date('d/m/Y h:i A', strtotime($loan->declaration_accepted_at)) : '-' }}</div>
            </div>
        </div>

        <div class="tab-pane fade" id="payment-tab">
            <div class="row text-center">
                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading"><p class="mb-0">Voucher No</p>{{ $loan->payment_voucher_no ?: '-' }}</div>
                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading"><p class="mb-0">Payment Date</p>{{ $loan->payment_date ? date('d/m/Y', strtotime($loan->payment_date)) : '-' }}</div>
                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading"><p class="mb-0">Payment Method</p>{{ $loan->payment_method ?: '-' }}</div>
                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading"><p class="mb-0">Paid Amount</p>{{ $loan->paid_amount ? number_format((float)$loan->paid_amount, 2) : '-' }}</div>
                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading"><p class="mb-0">Payment Status</p>{{ $loan->payment_status ?: '-' }}</div>
                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading"><p class="mb-0">Payment Reference</p>{{ $loan->payment_reference ?: '-' }}</div>
            </div>
        </div>

        <div class="tab-pane fade" id="history-tab">
            <div class="row text-center">
                <div class="col-xxl-3 col-lg-4 col-md-6 col-12 mb-3 green-heading"><p class="mb-0">Previous Loan Balance</p>-</div>
                <div class="col-xxl-3 col-lg-4 col-md-6 col-12 mb-3 green-heading"><p class="mb-0">Total Payroll Deduction</p>{{ number_format((float)\App\SmAdvanceloan::totalDeduction($loan->staff_id), 2) }}</div>
                <div class="col-xxl-3 col-lg-4 col-md-6 col-12 mb-3 green-heading"><p class="mb-0">Last Updated</p>{{ optional($loan->updated_at)->format('d/m/Y h:i A') ?: '-' }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Reporting Approval -->
    <div class="col p-1">
        <div class="card mb-3">
            <table class="detail-item-table-sm" width="100%" style="table-layout: fixed;width:100%">
                @php
                    $manager_status = $loan->manager_approval ?: 'Pending';
                    $manager_class = loanStatusClass($manager_status);
                    $canEditManager = ($isApprovals ?? false) && $canAct && $nextStage === 'manager';
                @endphp
                <tr>
                    <td class="{{ $manager_class }} d-flex align-items-center justify-content-start gap-1" style="height:23px; padding: 0 15px;">
                        <div class="d-flex align-items-center justify-content-start flex-grow-1 gap-1 header-height">
                            <b>Reporting</b>
                            @if($canEditManager)
                                <a class="btn-md light" style="display: contents; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#modalLoanApproval">
                                    <i class="ico icon-outline-pen-new-square title-15 {{ $manager_class == 'bg-lightgreen text-dark' ? 'text-dark' : 'text-white' }}" title="Reporting Approval" style="font-size: 12px"></i>
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="text-start truncate-text-custom" style="padding: 10px 15px;">
                        <span class="fw-bold">Status</span> : 
                        @if ($manager_status === 'Approved')
                            Approved <i class="ico icon-outline-check-read title-15 text-success"></i>
                        @elseif($manager_status === 'Rejected')
                            Rejected <i class="ico icon-outline-close text-danger"></i>
                        @elseif($manager_status === 'Returned')
                            Returned <i class="ico icon-outline-close text-warning"></i>
                        @else
                            Pending <i class="ico icon-outline-clock-circle text-info"></i>
                        @endif
                    </td>
                </tr>
                @if($loan->recommended_amount)
                <tr>
                    <td class="text-start truncate-text-custom" style="padding: 5px 15px;">
                        <span class="fw-bold">Recommended Amount</span> : {{ number_format($loan->recommended_amount, 2) }}
                    </td>
                </tr>
                @endif
                @if($loan->manager_remarks)
                <tr>
                    <td class="text-start truncate-text-custom" style="padding: 5px 15px;">
                        <span class="fw-bold">Remarks</span> : {{ $loan->manager_remarks }}
                    </td>
                </tr>
                @endif
                @if($manager_status !== 'Pending' && $loan->approved_by)
                @php $managerUser = \App\User::find($loan->approved_by); @endphp
                <tr>
                    <td class="text-start truncate-text-custom" style="padding: 5px 15px;">
                        <span class="fw-bold">
                            @if($manager_status === 'Approved') Approved By @elseif($manager_status === 'Rejected') Rejected By @elseif($manager_status === 'Returned') Returned By @endif
                        </span> : {{ optional($managerUser)->full_name }}
                    </td>
                </tr>
                @endif
                @if($manager_status !== 'Pending' && $loan->approved_at)
                <tr>
                    <td class="text-start truncate-text-custom" style="padding: 5px 15px;">
                        <span class="fw-bold">Created At</span> : {{ date('d/m/Y h:i A', strtotime($loan->approved_at)) }}
                    </td>
                </tr>
                @endif
            </table>
        </div>
    </div>

    <!-- Finance Approval -->
    <div class="col p-1">
        <div class="card mb-3">
            <table class="detail-item-table-sm" width="100%" style="table-layout: fixed;width:100%">
                @php
                    $finance_status = $loan->finance_approval ?: 'Pending';
                    $finance_class = loanStatusClass($finance_status);
                    $canEditFinance = ($isApprovals ?? false) && $canAct && $nextStage === 'finance';
                @endphp
                <tr>
                    <td class="{{ $finance_class }} d-flex align-items-center justify-content-start gap-1" style="height:23px; padding: 0 15px;">
                        <div class="d-flex align-items-center justify-content-start flex-grow-1 gap-1 header-height">
                            <b>Finance</b>
                            @if($canEditFinance)
                                <a class="btn-md light" style="display: contents; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#modalLoanApproval">
                                    <i class="ico icon-outline-pen-new-square title-15 {{ $finance_class == 'bg-lightgreen text-dark' ? 'text-dark' : 'text-white' }}" title="Finance Approval" style="font-size: 12px"></i>
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="text-start truncate-text-custom" style="padding: 10px 15px;">
                        <span class="fw-bold">Status</span> : 
                        @if ($finance_status === 'Approved')
                            Approved <i class="ico icon-outline-check-read title-15 text-success"></i>
                        @elseif($finance_status === 'Rejected')
                            Rejected <i class="ico icon-outline-close text-danger"></i>
                        @elseif($finance_status === 'Returned')
                            Returned <i class="ico icon-outline-close text-warning"></i>
                        @else
                            Pending <i class="ico icon-outline-clock-circle text-info"></i>
                        @endif
                    </td>
                </tr>
                @if($loan->financial_review_status)
                <tr>
                    <td class="text-start truncate-text-custom" style="padding: 5px 15px;">
                        <span class="fw-bold">Financial Review</span> : {{ $loan->financial_review_status }}
                    </td>
                </tr>
                @endif
                @if($loan->outstanding_loan_verification)
                <tr>
                    <td class="text-start truncate-text-custom" style="padding: 5px 15px;">
                        <span class="fw-bold">Outstanding Loan</span> : {{ $loan->outstanding_loan_verification }}
                    </td>
                </tr>
                @endif
                @if($loan->monthly_deduction_feasibility)
                <tr>
                    <td class="text-start truncate-text-custom" style="padding: 5px 15px;">
                        <span class="fw-bold">Monthly Deduction</span> : {{ $loan->monthly_deduction_feasibility }}
                    </td>
                </tr>
                @endif
                @if($loan->finance_approved_amount)
                <tr>
                    <td class="text-start truncate-text-custom" style="padding: 5px 15px;">
                        <span class="fw-bold">Approved Amount</span> : {{ number_format($loan->finance_approved_amount, 2) }}
                    </td>
                </tr>
                @endif
                @if($loan->finance_management_approval_req)
                <tr>
                    <td class="text-start truncate-text-custom" style="padding: 5px 15px;">
                        <span class="fw-bold">Management Req.</span> : {{ $loan->finance_management_approval_req }}
                    </td>
                </tr>
                @endif
                @if($loan->finance_remarks)
                <tr>
                    <td class="text-start truncate-text-custom" style="padding: 5px 15px;">
                        <span class="fw-bold">Remarks</span> : {{ $loan->finance_remarks }}
                    </td>
                </tr>
                @endif
                @if($finance_status !== 'Pending' && $loan->finance_approved_by)
                @php $financeUser = \App\User::find($loan->finance_approved_by); @endphp
                <tr>
                    <td class="text-start truncate-text-custom" style="padding: 5px 15px;">
                        <span class="fw-bold">
                            @if($finance_status === 'Approved') Approved By @elseif($finance_status === 'Rejected') Rejected By @elseif($finance_status === 'Returned') Returned By @endif
                        </span> : {{ optional($financeUser)->full_name }}
                    </td>
                </tr>
                @endif
                @if($finance_status !== 'Pending' && $loan->finance_approved_at)
                <tr>
                    <td class="text-start truncate-text-custom" style="padding: 5px 15px;">
                        <span class="fw-bold">Created At</span> : {{ date('d/m/Y h:i A', strtotime($loan->finance_approved_at)) }}
                    </td>
                </tr>
                @endif
            </table>
        </div>
    </div>

    <!-- HR Approval -->
    <div class="col p-1">
        <div class="card mb-3">
            <table class="detail-item-table-sm" width="100%" style="table-layout: fixed;width:100%">
                @php
                    $hr_status = $loan->hr_approval ?: 'Pending';
                    $hr_class = loanStatusClass($hr_status);
                    $canEditHR = ($isApprovals ?? false) && $canAct && $nextStage === 'hr';
                @endphp
                <tr>
                    <td class="{{ $hr_class }} d-flex align-items-center justify-content-start gap-1" style="height:23px; padding: 0 15px;">
                        <div class="d-flex align-items-center justify-content-start flex-grow-1 gap-1 header-height">
                            <b>HR</b>
                            @if($canEditHR)
                                <a class="btn-md light" style="display: contents; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#modalLoanApproval">
                                    <i class="ico icon-outline-pen-new-square title-15 {{ $hr_class == 'bg-lightgreen text-dark' ? 'text-dark' : 'text-white' }}" title="HR Approval" style="font-size: 12px"></i>
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="text-start truncate-text-custom" style="padding: 10px 15px;">
                        <span class="fw-bold">Status</span> : 
                        @if ($hr_status === 'Approved')
                            Approved <i class="ico icon-outline-check-read title-15 text-success"></i>
                        @elseif($hr_status === 'Rejected')
                            Rejected <i class="ico icon-outline-close text-danger"></i>
                        @elseif($hr_status === 'Returned')
                            Returned <i class="ico icon-outline-close text-warning"></i>
                        @else
                            Pending <i class="ico icon-outline-clock-circle text-info"></i>
                        @endif
                    </td>
                </tr>
                @if($loan->hr_approval_status)
                <tr>
                    <td class="text-start truncate-text-custom" style="padding: 5px 15px;">
                        <span class="fw-bold">HR Approval Status</span> : {{ $loan->hr_approval_status }}
                    </td>
                </tr>
                @endif
                @if($loan->policy_compliance)
                <tr>
                    <td class="text-start truncate-text-custom" style="padding: 5px 15px;">
                        <span class="fw-bold">Policy Compliance</span> : {{ $loan->policy_compliance }}
                    </td>
                </tr>
                @endif
                @if($loan->eligibility_verified)
                <tr>
                    <td class="text-start truncate-text-custom" style="padding: 5px 15px;">
                        <span class="fw-bold">Eligibility Verified</span> : {{ $loan->eligibility_verified }}
                    </td>
                </tr>
                @endif
                @if($loan->hr_management_approval_req)
                <tr>
                    <td class="text-start truncate-text-custom" style="padding: 5px 15px;">
                        <span class="fw-bold">Management Req.</span> : {{ $loan->hr_management_approval_req }}
                    </td>
                </tr>
                @endif
                @if($loan->hr_remarks)
                <tr>
                    <td class="text-start truncate-text-custom" style="padding: 5px 15px;">
                        <span class="fw-bold">Remarks</span> : {{ $loan->hr_remarks }}
                    </td>
                </tr>
                @endif
                @if($hr_status !== 'Pending' && $loan->hr_approved_by)
                @php $hrUser = \App\User::find($loan->hr_approved_by); @endphp
                <tr>
                    <td class="text-start truncate-text-custom" style="padding: 5px 15px;">
                        <span class="fw-bold">
                            @if($hr_status === 'Approved') Approved By @elseif($hr_status === 'Rejected') Rejected By @elseif($hr_status === 'Returned') Returned By @endif
                        </span> : {{ optional($hrUser)->full_name }}
                    </td>
                </tr>
                @endif
                @if($hr_status !== 'Pending' && $loan->hr_approved_at)
                <tr>
                    <td class="text-start truncate-text-custom" style="padding: 5px 15px;">
                        <span class="fw-bold">Created At</span> : {{ date('d/m/Y h:i A', strtotime($loan->hr_approved_at)) }}
                    </td>
                </tr>
                @endif
            </table>
        </div>
    </div>

    @if($managementRequired)
    <!-- Management Approval -->
    <div class="col p-1">
        <div class="card mb-3">
            <table class="detail-item-table-sm" width="100%" style="table-layout: fixed;width:100%">
                @php
                    $management_status = $loan->management_approval ?: 'Pending';
                    $management_class = loanStatusClass($management_status);
                    $canEditManagement = ($isApprovals ?? false) && $canAct && $nextStage === 'management';
                @endphp
                <tr>
                    <td class="{{ $management_class }} d-flex align-items-center justify-content-start gap-1" style="height:23px; padding: 0 15px;">
                        <div class="d-flex align-items-center justify-content-start flex-grow-1 gap-1 header-height">
                            <b>Management</b>
                            @if($canEditManagement)
                                <a class="btn-md light" style="display: contents; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#modalLoanApproval">
                                    <i class="ico icon-outline-pen-new-square title-15 {{ $management_class == 'bg-lightgreen text-dark' ? 'text-dark' : 'text-white' }}" title="Management Approval" style="font-size: 12px"></i>
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="text-start truncate-text-custom" style="padding: 10px 15px;">
                        <span class="fw-bold">Status</span> : 
                        @if ($management_status === 'Approved')
                            Approved <i class="ico icon-outline-check-read title-15 text-success"></i>
                        @elseif($management_status === 'Rejected')
                            Rejected <i class="ico icon-outline-close text-danger"></i>
                        @elseif($management_status === 'Returned')
                            Returned <i class="ico icon-outline-close text-warning"></i>
                        @else
                            Pending <i class="ico icon-outline-clock-circle text-info"></i>
                        @endif
                    </td>
                </tr>
                @if($loan->management_remarks)
                <tr>
                    <td class="text-start truncate-text-custom" style="padding: 5px 15px;">
                        <span class="fw-bold">Remarks</span> : {{ $loan->management_remarks }}
                    </td>
                </tr>
                @endif
                @if($management_status !== 'Pending' && $loan->management_approved_by)
                @php $managementUser = \App\User::find($loan->management_approved_by); @endphp
                <tr>
                    <td class="text-start truncate-text-custom" style="padding: 5px 15px;">
                        <span class="fw-bold">
                            @if($management_status === 'Approved') Approved By @elseif($management_status === 'Rejected') Rejected By @elseif($management_status === 'Returned') Returned By @endif
                        </span> : {{ optional($managementUser)->full_name }}
                    </td>
                </tr>
                @endif
                @if($management_status !== 'Pending' && $loan->management_approved_at)
                <tr>
                    <td class="text-start truncate-text-custom" style="padding: 5px 15px;">
                        <span class="fw-bold">Created At</span> : {{ date('d/m/Y h:i A', strtotime($loan->management_approved_at)) }}
                    </td>
                </tr>
                @endif
            </table>
        </div>
    </div>
    @endif

    <!-- Payment Processing -->
    <div class="col p-1">
        <div class="card mb-3">
            <table class="detail-item-table-sm" width="100%" style="table-layout: fixed;width:100%">
                @php
                    $payment_status = $loan->payment_approval ?: 'Pending';
                    $payment_class = loanStatusClass($payment_status);
                    $canEditPayment = ($isApprovals ?? false) && $canAct && $nextStage === 'payment';
                @endphp
                <tr>
                    <td class="{{ $payment_class }} d-flex align-items-center justify-content-start gap-1" style="height:23px; padding: 0 15px;">
                        <div class="d-flex align-items-center justify-content-start flex-grow-1 gap-1 header-height">
                            <b>Payment Processing</b>
                            @if($canEditPayment)
                                <a class="btn-md light" style="display: contents; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#modalLoanApproval">
                                    <i class="ico icon-outline-pen-new-square title-15 {{ $payment_class == 'bg-lightgreen text-dark' ? 'text-dark' : 'text-white' }}" title="Payment Processing Approval" style="font-size: 12px"></i>
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="text-start truncate-text-custom" style="padding: 10px 15px;">
                        <span class="fw-bold">Status</span> : 
                        @if ($payment_status === 'Approved')
                            Approved <i class="ico icon-outline-check-read title-15 text-success"></i>
                        @elseif($payment_status === 'Rejected')
                            Rejected <i class="ico icon-outline-close text-danger"></i>
                        @elseif($payment_status === 'Returned')
                            Returned <i class="ico icon-outline-close text-warning"></i>
                        @else
                            Pending <i class="ico icon-outline-clock-circle text-info"></i>
                        @endif
                    </td>
                </tr>
                @if($loan->payment_voucher_no)
                <tr>
                    <td class="text-start truncate-text-custom" style="padding: 5px 15px;">
                        <span class="fw-bold">Voucher No</span> : {{ $loan->payment_voucher_no }}
                    </td>
                </tr>
                @endif
                @if($loan->payment_method)
                <tr>
                    <td class="text-start truncate-text-custom" style="padding: 5px 15px;">
                        <span class="fw-bold">Payment Method</span> : {{ $loan->payment_method }}
                    </td>
                </tr>
                @endif
                @if($loan->payment_reference)
                <tr>
                    <td class="text-start truncate-text-custom" style="padding: 5px 15px;">
                        <span class="fw-bold">Payment Reference</span> : {{ $loan->payment_reference }}
                    </td>
                </tr>
                @endif
                @if($loan->payment_remarks)
                <tr>
                    <td class="text-start truncate-text-custom" style="padding: 5px 15px;">
                        <span class="fw-bold">Remarks</span> : {{ $loan->payment_remarks }}
                    </td>
                </tr>
                @endif
                @if($payment_status !== 'Pending' && $loan->payment_approved_by)
                @php $paymentUser = \App\User::find($loan->payment_approved_by); @endphp
                <tr>
                    <td class="text-start truncate-text-custom" style="padding: 5px 15px;">
                        <span class="fw-bold">
                            @if($payment_status === 'Approved') Approved By @elseif($payment_status === 'Rejected') Rejected By @elseif($payment_status === 'Returned') Returned By @endif
                        </span> : {{ optional($paymentUser)->full_name }}
                    </td>
                </tr>
                @endif
                @if($payment_status !== 'Pending' && $loan->payment_approved_at)
                <tr>
                    <td class="text-start truncate-text-custom" style="padding: 5px 15px;">
                        <span class="fw-bold">Created At</span> : {{ date('d/m/Y h:i A', strtotime($loan->payment_approved_at)) }}
                    </td>
                </tr>
                @endif
            </table>
        </div>
    </div>
</div>


@if($canAct && $nextStage)
@php
    $current_status = 'Pending';
    if ($nextStage === 'manager') {
        $current_status = $loan->manager_approval;
    } elseif ($nextStage === 'finance') {
        $current_status = $loan->finance_approval;
    } elseif ($nextStage === 'hr') {
        $current_status = $loan->hr_approval;
    } elseif ($nextStage === 'management') {
        $current_status = $loan->management_approval;
    } elseif ($nextStage === 'payment') {
        $current_status = $loan->payment_approval;
    }
    $status_to_select = (empty($current_status) || $current_status === 'Pending') ? 'Approved' : $current_status;

    $fin_review_selected = (empty($loan->financial_review_status)) ? 'Passed' : $loan->financial_review_status;
    $outstanding_selected = (empty($loan->outstanding_loan_verification)) ? 'Verified - No outstanding' : $loan->outstanding_loan_verification;
    $feasibility_selected = (empty($loan->monthly_deduction_feasibility)) ? 'Feasible' : $loan->monthly_deduction_feasibility;
    $fin_mgmt_selected = (empty($loan->finance_management_approval_req)) ? 'Yes' : $loan->finance_management_approval_req;

    $hr_app_status_selected = (empty($loan->hr_approval_status)) ? 'Passed' : $loan->hr_approval_status;
    $policy_selected = (empty($loan->policy_compliance)) ? 'Compliant' : $loan->policy_compliance;
    $eligibility_selected = (empty($loan->eligibility_verified)) ? 'Yes' : $loan->eligibility_verified;
    $hr_mgmt_selected = (empty($loan->hr_management_approval_req)) ? 'Yes' : $loan->hr_management_approval_req;

    $mgmt_status_selected = (empty($loan->management_approval_status)) ? 'Approved' : $loan->management_approval_status;

    $payment_status_selected = (empty($loan->payment_status)) ? 'Paid' : $loan->payment_status;

    $approvalChecks = [
        'manager' => [
            'Employee performance',
            'Attendance record',
            'Business necessity',
            'Recommendation',
        ],
        'finance' => [
            'Existing liabilities',
            'Salary deduction capability',
            'Payroll impact',
            'Policy compliance',
        ],
        'hr' => [
            'Service period',
            'Employment status',
            'Loan policy compliance',
        ],
    ];
    $approvalDialogClass = $nextStage === 'management'
        ? 'loan-management-approval-dialog'
        : ($nextStage === 'manager' ? 'loan-reporting-approval-dialog' : 'modal-lg');
@endphp
<div class="modal fade" id="modalLoanApproval" data-bs-backdrop="false" tabindex="-1" role="dialog" aria-hidden="true" style="background: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-dialog-centered {{ $approvalDialogClass }}" role="document">
        <div class="modal-content">
            <div class="modal-header m-0">
                <h4 class="modal-title">{{ $stageTitle }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('employee.loans.approve', $loan->id) }}" method="POST" id="loanApprovalForm">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-danger d-none" id="loanApprovalChecksAlert">
                        Please complete all checks before approval.
                    </div>
                    @if($nextStage === 'manager')
                        <div class="row loan-reporting-fields">
                            <div class="col-md-6 mb-1">
                                <label>Approval Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-control" required>
                                    <option value="">-Select-</option>
                                    <option value="Approved" {{ $status_to_select === 'Approved' ? 'selected' : '' }}>Approve</option>
                                    <option value="Rejected" {{ $status_to_select === 'Rejected' ? 'selected' : '' }}>Reject</option>
                                    <option value="Returned" {{ $status_to_select === 'Returned' ? 'selected' : '' }}>Return</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-1">
                                <label>Recommended Loan Amount</label>
                                <input type="number" step="0.01" name="recommended_amount" class="form-control" value="{{ $loan->recommended_amount ?: $loan->amount }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-1">
                                <label>Remarks</label>
                                <textarea name="remarks" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-1 loan-approval-checks">
                                <label>Checks</label>
                                <div class="border rounded p-1">
                                    @foreach($approvalChecks[$nextStage] as $checkIndex => $checkLabel)
                                        <div class="form-check mb-0">
                                            <input class="form-check-input loan-approval-check" type="checkbox" name="approval_checks[]" value="{{ $checkLabel }}" id="loan_approval_check_{{ $checkIndex }}" checked>
                                            <label class="form-check-label" for="loan_approval_check_{{ $checkIndex }}">{{ $checkLabel }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @elseif($nextStage === 'finance')
                        <div class="row">
                            <div class="col-md-4 mb-1">
                                <label>Approval Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-control" required>
                                    <option value="">-Select-</option>
                                    <option value="Approved" {{ $status_to_select === 'Approved' ? 'selected' : '' }}>Approve</option>
                                    <option value="Rejected" {{ $status_to_select === 'Rejected' ? 'selected' : '' }}>Reject</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-1">
                                <label>Financial Review Status</label>
                                <select name="financial_review_status" class="form-control">
                                    <option value="">-Select-</option>
                                    <option value="Passed" {{ $fin_review_selected === 'Passed' ? 'selected' : '' }}>Passed</option>
                                    <option value="Failed" {{ $fin_review_selected === 'Failed' ? 'selected' : '' }}>Failed</option>
                                    <option value="Requires Attention" {{ $fin_review_selected === 'Requires Attention' ? 'selected' : '' }}>Requires Attention</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-1">
                                <label>Outstanding Loan Verification</label>
                                <select name="outstanding_loan_verification" class="form-control">
                                    <option value="">-Select-</option>
                                    <option value="Verified - No outstanding" {{ $outstanding_selected === 'Verified - No outstanding' ? 'selected' : '' }}>Verified - No outstanding</option>
                                    <option value="Verified - Has outstanding" {{ $outstanding_selected === 'Verified - Has outstanding' ? 'selected' : '' }}>Verified - Has outstanding</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-1">
                                <label>Monthly Deduction Feasibility</label>
                                <select name="monthly_deduction_feasibility" class="form-control">
                                    <option value="">-Select-</option>
                                    <option value="Feasible" {{ $feasibility_selected === 'Feasible' ? 'selected' : '' }}>Feasible</option>
                                    <option value="Not Feasible" {{ $feasibility_selected === 'Not Feasible' ? 'selected' : '' }}>Not Feasible</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-1">
                                <label>Management Approval Required</label>
                                <select name="management_approval_req" class="form-control">
                                    <option value="">-Select-</option>
                                    <option value="Yes" {{ $fin_mgmt_selected === 'Yes' ? 'selected' : '' }}>Yes</option>
                                    <option value="No" {{ $fin_mgmt_selected === 'No' ? 'selected' : '' }}>No</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-1">
                                <label>Approved Amount</label>
                                <input type="number" step="0.01" name="approved_amount" class="form-control" value="{{ $loan->finance_approved_amount ?: ($loan->recommended_amount ?: $loan->amount) }}">
                            </div>
                            <div class="col-md-8 mb-1">
                                <label>Remarks</label>
                                <textarea name="remarks" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="col-md-4 mb-1 loan-approval-checks">
                                <label>Checks</label>
                                <div class="border rounded p-1">
                                    @foreach($approvalChecks[$nextStage] as $checkIndex => $checkLabel)
                                        <div class="form-check mb-0">
                                            <input class="form-check-input loan-approval-check" type="checkbox" name="approval_checks[]" value="{{ $checkLabel }}" id="loan_approval_check_{{ $checkIndex }}" checked>
                                            <label class="form-check-label" for="loan_approval_check_{{ $checkIndex }}">{{ $checkLabel }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @elseif($nextStage === 'hr')
                        <div class="row">
                            <div class="col-md-4 mb-1">
                                <label>Approval Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-control" required>
                                    <option value="">-Select-</option>
                                    <option value="Approved" {{ $status_to_select === 'Approved' ? 'selected' : '' }}>Approve</option>
                                    <option value="Rejected" {{ $status_to_select === 'Rejected' ? 'selected' : '' }}>Reject</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-1">
                                <label>HR Approval Status</label>
                                <select name="hr_approval_status" class="form-control">
                                    <option value="">-Select-</option>
                                    <option value="Passed" {{ $hr_app_status_selected === 'Passed' ? 'selected' : '' }}>Passed</option>
                                    <option value="Failed" {{ $hr_app_status_selected === 'Failed' ? 'selected' : '' }}>Failed</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-1">
                                <label>Policy Compliance</label>
                                <select name="policy_compliance" class="form-control">
                                    <option value="">-Select-</option>
                                    <option value="Compliant" {{ $policy_selected === 'Compliant' ? 'selected' : '' }}>Compliant</option>
                                    <option value="Non-Compliant" {{ $policy_selected === 'Non-Compliant' ? 'selected' : '' }}>Non-Compliant</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-1">
                                <label>Eligibility Verified</label>
                                <select name="eligibility_verified" class="form-control">
                                    <option value="">-Select-</option>
                                    <option value="Yes" {{ $eligibility_selected === 'Yes' ? 'selected' : '' }}>Yes</option>
                                    <option value="No" {{ $eligibility_selected === 'No' ? 'selected' : '' }}>No</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-1">
                                <label>Management Approval Required</label>
                                <select name="management_approval_req" class="form-control" required>
                                    <option value="">-Select-</option>
                                    <option value="Yes" {{ $hr_mgmt_selected === 'Yes' ? 'selected' : '' }}>Yes</option>
                                    <option value="No" {{ $hr_mgmt_selected === 'No' ? 'selected' : '' }}>No</option>
                                </select>
                            </div>
                            <div class="col-md-8 mb-1">
                                <label>Remarks</label>
                                <textarea name="remarks" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="col-md-4 mb-1 loan-approval-checks">
                                <label>Checks</label>
                                <div class="border rounded p-1">
                                    @foreach($approvalChecks[$nextStage] as $checkIndex => $checkLabel)
                                        <div class="form-check mb-0">
                                            <input class="form-check-input loan-approval-check" type="checkbox" name="approval_checks[]" value="{{ $checkLabel }}" id="loan_approval_check_{{ $checkIndex }}" checked>
                                            <label class="form-check-label" for="loan_approval_check_{{ $checkIndex }}">{{ $checkLabel }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @elseif($nextStage === 'management')
                        <div class="row">
                            <div class="col-md-6 mb-1">
                                <label>Approval Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-control" required>
                                    <option value="">-Select-</option>
                                    <option value="Approved" {{ $status_to_select === 'Approved' ? 'selected' : '' }}>Approve</option>
                                    <option value="Rejected" {{ $status_to_select === 'Rejected' ? 'selected' : '' }}>Reject</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-1">
                                <label>Final Approval Status</label>
                                <select name="management_approval_status" class="form-control">
                                    <option value="">-Select-</option>
                                    <option value="Approved" {{ $mgmt_status_selected === 'Approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="Rejected" {{ $mgmt_status_selected === 'Rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-1">
                                <label>Remarks</label>
                                <textarea name="remarks" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                    @elseif($nextStage === 'payment')
                        <div class="row">
                            <div class="col-md-4 mb-1">
                                <label>Approval Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-control" required>
                                    <option value="">-Select-</option>
                                    <option value="Approved" {{ $status_to_select === 'Approved' ? 'selected' : '' }}>Approve</option>
                                    <option value="Rejected" {{ $status_to_select === 'Rejected' ? 'selected' : '' }}>Reject</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-1"><label>Voucher No</label><input type="text" name="payment_voucher_no" class="form-control" value="{{ $loan->payment_voucher_no }}"></div>
                            <div class="col-md-4 mb-1"><label>Disbursement Date</label><input type="date" name="payment_date" class="form-control" value="{{ $loan->payment_date ?: date('Y-m-d') }}"></div>
                            <div class="col-md-4 mb-1">
                                <label>Payment Method</label>
                                <select name="payment_method" class="form-control">
                                    <option value="">-Select-</option>
                                    @foreach(['Cash','Bank Transfer','Cheque'] as $method)
                                        <option value="{{ $method }}" {{ $loan->payment_method === $method ? 'selected' : '' }}>{{ $method }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-1"><label>Payment Reference</label><input type="text" name="payment_reference" class="form-control" value="{{ $loan->payment_reference }}"></div>
                            <div class="col-md-4 mb-1">
                                <label>Loan Status</label>
                                <select name="payment_status" class="form-control">
                                    <option value="">-Select-</option>
                                    <option value="Pending" {{ $payment_status_selected === 'Pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="Paid" {{ $payment_status_selected === 'Paid' ? 'selected' : '' }}>Paid</option>
                                </select>
                            </div>
                            <input type="hidden" name="paid_amount" value="{{ $loan->finance_approved_amount ?: $loan->amount }}">
                            <div class="col-md-8 mb-1">
                                <label>Remarks</label>
                                <textarea name="remarks" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer m-0 p-0 d-flex justify-content-center">
                    <button type="submit" class="btn btn-light add-btn ms-2">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
$(function () {
    $('#loanApprovalForm').on('submit', function (e) {
        var status = $(this).find('[name="status"]').val();
        var $checks = $(this).find('.loan-approval-check');
        var $alert = $('#loanApprovalChecksAlert');

        if (status === 'Approved' && $checks.length && $checks.filter(':checked').length !== $checks.length) {
            e.preventDefault();
            $alert.removeClass('d-none');
            return false;
        }

        $alert.addClass('d-none');
    });

    $('#loanApprovalForm [name="status"], #loanApprovalForm .loan-approval-check').on('change', function () {
        $('#loanApprovalChecksAlert').addClass('d-none');
    });
});
</script>
@endif

</div> <!-- /#loan-details -->


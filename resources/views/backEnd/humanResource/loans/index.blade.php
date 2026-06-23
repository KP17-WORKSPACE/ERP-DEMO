@extends('backEnd.newmasterpage')
@section('mainContent')
@php
    use Illuminate\Support\Str;
    $typeMap = $loanTypes->pluck('name', 'id')->toArray();
    $hasFilters = request()->has('q') || request()->has('status') || request()->has('approval_status') ||
        request()->has('request_type') || request()->has('loan_category') || request()->has('type_id') || request()->has('employee_id') || request()->has('from') ||
        request()->has('to') || request()->has('urgency_level') || request()->has('repayment_mode');
    $loanPermissions = $loanPermissions ?? ['create' => true, 'view' => true, 'edit' => true, 'delete' => true, 'export' => true, 'attach' => true];
    $trackPermissions = $trackPermissions ?? ['create' => true, 'view' => true, 'edit' => true, 'delete' => true, 'export' => true, 'attach' => true];
@endphp
<style>
    #filters-long .loan-list-toolbar {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
        flex-wrap: wrap;
    }
    #filters-long .loan-list-toolbar .loan-list-search {
        width: 320px;
        flex: 0 0 320px;
        font-size: 13px;
        height: 32px;
    }
    #filters-long .loan-list-toolbar .btn {
        position: static;
        top: auto;
        right: auto;
        margin-right: 0 !important;
    }
    #filters-long .loan-list-toolbar .list_style_expand_btn,
    #filters-long .loan-list-toolbar .list_style_search_btn {
        position: static;
        top: auto;
        right: auto;
    }
    #filters-short .search-filter-container {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    #filters-short .search-filter-container .input-group {
        flex: 1 1 auto;
        min-width: 0;
    }
    #filters-short .search-filter-container .list_style_expand_btn {
        position: static;
        top: auto;
        right: auto;
        flex: 0 0 auto;
    }
    @media (max-width: 992px) {
        #filters-long .loan-list-toolbar {
            justify-content: flex-start;
        }
        #filters-long .loan-list-toolbar .loan-list-search {
            width: 100%;
            flex: 1 1 280px;
        }
    }
</style>

<aside class="left-nav {{ $hasFilters ? 'col-12' : 'col-3' }}" id="leftSidebar" data-view="{{ $hasFilters ? 'full' : 'compact' }}">
    <div class="resizer" id="sidebarResizer"></div>

    <div class="short-list {{ $hasFilters ? 'd-none' : '' }}" id="filters-short">
        <h4 class="mb-2">Loans & Advance Request List</h4>
        <form method="get" action="{{ route('employee.loans.index') }}" id="loan-search">
            <div class="search-filter-container mb-4" id="short-list">
                <div class="input-group flex-nowrap">
                    <input type="text" name="q" class="form-control" placeholder="Request No / Employee / Purpose" value="{{ request('q') }}">
                </div>
                <button type="button" class="btn btn-light list_style_expand_btn" id="list_style_button" onclick="list_style_new_loans()">
                    <i class="ico icon-outline-list-down"></i>
                </button>
            </div>
        </form>
    </div>

    <div class="long-list {{ $hasFilters ? '' : 'd-none' }}" id="filters-long">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0">Loans & Advance Request List</h4>

            <div class="search-filter-container mb-0 loan-list-toolbar">
                <input type="text" id="tableSearch" class="form-control loan-list-search" placeholder="Search">
                @if(!empty($loanPermissions['export']))
                <a href="{{ route('employee.loans.export', request()->query()) }}" class="btn btn-light list_style_search_btn">
                    <i class="ico icon-outline-export text-success"></i> Export
                </a>
                @endif
                <button type="button" class="btn btn-light list_style_search_btn" onclick="search_box_show_hide()" title="Search / Filter">
                    <i class="ico icon-outline-magnifer"></i>
                </button>
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle list_style_expand_btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Menu">
                        <i class="ico icon-outline-hamburger-menu"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        @if(!empty($loanPermissions['export']))
                        <li><a class="dropdown-item" href="{{ route('employee.loans.export', request()->query()) }}"><i class="ico icon-outline-export text-success"></i> Download / Export</a></li>
                        @endif
                        @if(!empty($trackPermissions['view']))
                        <li>
                            @if($selectedLoan && $selectedLoan->status !== 'Draft')
                                <a class="dropdown-item" href="{{ route('employee.loans.approvals', $selectedLoan->id) }}">
                                    <i class="ico icon-outline-list-down text-success"></i> Loan Track
                                </a>
                            @else
                                <a class="dropdown-item" href="javascript:void(0)" onclick="alert('Please select a submitted loan request to open Loan Track.')">
                                    <i class="ico icon-outline-list-down text-success"></i> Loan Track
                                </a>
                            @endif
                        </li>
                        @endif
                    </ul>
                </div>
                <button type="button" class="btn btn-light list_style_expand_btn" onclick="list_style_new_loans()" title="Compact list">
                    <i class="ico icon-outline-list-down"></i>
                </button>
            </div>
        </div>

        <div class="search-filter-container mt-1 mb-4 filter-field border" id="long-filters-box" style="display: {{ $hasFilters ? 'block' : 'none' }};">
            <form method="get" action="{{ route('employee.loans.index') }}" id="loan-filter">
                <div class="row">
                    <div class="col-1-5 mb-2 filter-field">
                        <label class="form-label">Request Type</label>
                        <select class="form-control" name="request_type">
                            <option value="">-Select-</option>
                            <option value="Loan" {{ request('request_type') === 'Loan' ? 'selected' : '' }}>Loan</option>
                            <option value="Salary Advance" {{ request('request_type') === 'Salary Advance' ? 'selected' : '' }}>Salary Advance</option>
                        </select>
                    </div>
                    <div class="col-1-5 mb-2 filter-field">
                        <label class="form-label">Loan Category</label>
                        <select class="form-control" name="loan_category">
                            <option value="">-Select-</option>
                            @foreach ($loanCategories as $category)
                                <option value="{{ $category }}" {{ request('loan_category') === $category ? 'selected' : '' }}>{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-1-5 mb-2 filter-field">
                        <label class="form-label">Loan Status</label>
                        <select class="form-control" name="status">
                            <option value="">-Select-</option>
                            @foreach (['Draft','Pending','Pending Reporting Manager Approval','Approved','Rejected','Disbursed'] as $status)
                                <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-1-5 mb-2 filter-field">
                        <label class="form-label">Approval Status</label>
                        <select class="form-control" name="approval_status">
                            <option value="">-Select-</option>
                            @foreach (['Pending','Approved','Rejected'] as $status)
                                <option value="{{ $status }}" {{ request('approval_status') === $status ? 'selected' : '' }}>{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-1-5 mb-2 filter-field">
                        <label class="form-label">Employee</label>
                        <select class="form-control" name="employee_id">
                            <option value="">-Select-</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->full_name ?: trim($employee->first_name . ' ' . $employee->last_name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-1-5 mb-2 filter-field">
                        <label class="form-label">From Date</label>
                        <input type="date" name="from" class="form-control" value="{{ request('from') }}">
                    </div>
                    <div class="col-1-5 mb-2 filter-field">
                        <label class="form-label">To Date</label>
                        <input type="date" name="to" class="form-control" value="{{ request('to') }}">
                    </div>
                    <div class="col-1-5 mb-2 filter-field">
                        <label class="form-label">Urgency Level</label>
                        <select class="form-control" name="urgency_level">
                            <option value="">-Select-</option>
                            @foreach (['Normal','Urgent','Critical'] as $level)
                                <option value="{{ $level }}" {{ request('urgency_level') === $level ? 'selected' : '' }}>{{ $level }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-1-5 mb-2 filter-field">
                        <label class="form-label">Repayment Mode</label>
                        <select class="form-control" name="repayment_mode">
                            <option value="">-Select-</option>
                            @foreach (['Salary Deduction','Bank Transfer','Cash Payment','Adjustment'] as $mode)
                                <option value="{{ $mode }}" {{ request('repayment_mode') === $mode ? 'selected' : '' }}>{{ $mode }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-1-5 mb-2 filter-field">
                        <label class="form-label">Search</label>
                        <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Request No / Purpose">
                    </div>
                    <div class="col-1-5 mb-2 filter-field d-flex align-items-end">
                        <button type="submit" class="btn btn-light me-2">
                            <i class="ico icon-outline-magnifer"></i> Filter
                        </button>
                        <a href="{{ route('employee.loans.index') }}" class="btn btn-light">
                            <i class="ico icon-bold-restart text-success"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="left-nav-list">
        <ul id="loanShortList" class="nav flex-column nav-pills {{ $hasFilters ? 'd-none' : '' }}" role="tablist">
            @forelse ($loans as $loan)
                @php $staff = $loan->staffDetails; @endphp
                <li class="nav-item w-100" role="presentation">
                    <button class="nav-link lv-item {{ $selectedLoan && $selectedLoan->id == $loan->id ? 'active' : '' }}" data-id="{{ $loan->id }}" type="button">
                        <div class="row w-100">
                            <div class="col-12">
                                <label class="form-control-plaintext truncate-text">{{ optional($staff)->full_name ?: optional($staff)->first_name ?: 'Employee' }}</label>
                            </div>
                            <div class="col-4">
                                <div class="form-control-plaintext" style="font-size:11px">{{ $loan->document_number }}</div>
                            </div>
                            <div class="col-4 pl-2">
                                <div class="form-control-plaintext truncate-text" style="font-size:11px">{{ optional($loan->created_at)->format('d/m/Y') }}</div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="form-control-plaintext truncate-text" style="font-size:11px">{{ number_format((float)$loan->amount, 2) }}</div>
                            </div>
                        </div>
                    </button>
                </li>
            @empty
                <div class="p-3 text-muted">No loan or advance requests found. Use Add to create a new request.</div>
            @endforelse
        </ul>

        <div id="long-list" class="{{ $hasFilters ? '' : 'd-none' }}">
            <style>
              .loan-table-wrapper {
                max-height: calc(100vh - 160px);
                overflow: auto;
              }
              .loan-table-wrapper thead th {
                position: sticky;
                top: 0;
                z-index: 10;
                background-color: #deebe1 !important;
                box-shadow: 0 1px 1px rgba(0,0,0,0.1);
                white-space: nowrap !important;
                vertical-align: middle;
                line-height: 1.2;
                padding: 7px 4px !important;
                overflow: hidden;
                text-overflow: ellipsis;
              }
              .loan-table-wrapper tbody td {
                vertical-align: middle;
                white-space: nowrap;
                word-break: normal;
                overflow: hidden;
                text-overflow: ellipsis;
                padding-left: 4px !important;
                padding-right: 4px !important;
              }
              .loan-table-wrapper .loan-action-buttons {
                gap: 3px;
                min-width: 78px;
              }
              .loan-table-wrapper .loan-action-buttons .btn {
                width: 24px;
                height: 24px;
                padding: 2px !important;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                flex: 0 0 24px;
              }
              .loan-table-wrapper .badge {
                max-width: 100%;
                overflow: hidden;
                text-overflow: ellipsis;
                vertical-align: middle;
              }
            </style>
            <div class="table-responsive mb-4 mt-4 loan-table-wrapper">
                <table class="table table-hover mt-0 data-table" style="table-layout: fixed;width:100%">
                    <thead>
                        <tr>
                            <th style="width: 5.5%;" title="Date">Date</th>
                            <th style="width: 6.5%;" title="Doc No">Doc No</th>
                            <th style="width: 8.5%;" title="Employee Name">Employee Name</th>
                            <th style="width: 6.5%;" title="Request Type">Request Type</th>
                            <th style="width: 7%;" title="Loan Category">Loan Category</th>
                            <th style="width: 7%;" class="text-end" title="Amount Requested">Amount Requested</th>
                            <th style="width: 6.5%;" class="text-end" title="Installment Number">Installment Number</th>
                            <th style="width: 8%;" class="text-end" title="Monthly Deduction Amount">Monthly Deduction Amount</th>
                            <th style="width: 7%;" title="Repayment Start Month">Repayment Start Month</th>
                            <th style="width: 7%;" title="Repayment Mode">Repayment Mode</th>
                            <th style="width: 6.5%;" title="Disbursement Date">Disbursement Date</th>
                            <th style="width: 9%;" title="Purpose">Purpose</th>
                            <th style="width: 8%;" title="Guarantor Employee">Guarantor Employee</th>
                            <th style="width: 6%;" title="Status">Status</th>
                            <th style="width: 7%;" class="text-center" title="Actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody style="font-size:12px">
                        @forelse ($loans as $loan)
                            @php
                                $staff = $loan->staffDetails;
                            @endphp
                            <tr>
                                <td class="text-center">{{ $loan->list_date ?: '-' }}</td>
                                <td class="text-center">
                                    <a class="text-success" href="{{ route('employee.loans.track', $loan->id) }}" target="_blank" style="cursor:pointer">{{ $loan->document_number }}</a>
                                </td>
                                <td class="truncate-cell" title="{{ $loan->list_employee_name }}">{{ $loan->list_employee_name ?: '-' }}</td>
                                <td title="{{ $loan->request_type ?: ($typeMap[$loan->type_id] ?? '-') }}">{{ $loan->request_type ?: ($typeMap[$loan->type_id] ?? '-') }}</td>
                                <td title="{{ $loan->loan_category ?: ($typeMap[$loan->type_id] ?? '-') }}">{{ $loan->loan_category ?: ($typeMap[$loan->type_id] ?? '-') }}</td>
                                <td class="text-end">{{ $loan->list_original_amount }}</td>
                                <td class="text-end">{{ $loan->installments ?: '-' }}</td>
                                <td class="text-end">{{ $loan->list_monthly_deduction }}</td>
                                <td title="{{ $loan->list_repayment_start }}">{{ $loan->list_repayment_start }}</td>
                                <td title="{{ $loan->repayment_mode ?: '-' }}">{{ $loan->repayment_mode ?: '-' }}</td>
                                <td class="text-center">{{ $loan->list_disbursement_date }}</td>
                                <td class="truncate-cell" title="{{ $loan->purpose }}">{{ $loan->purpose ?: '-' }}</td>
                                <td class="truncate-cell" title="{{ $loan->list_guarantor_name }}">{{ $loan->list_guarantor_name ?: '-' }}</td>
                            @php
                                if ($loan->status === 'Draft') {
                                    $workflowStatus = 'New';
                                } elseif ($loan->status === 'Rejected' || in_array('Rejected', [$loan->manager_approval, $loan->finance_approval, $loan->hr_approval, $loan->management_approval, $loan->payment_approval], true)) {
                                    $workflowStatus = 'Rejected';
                                } elseif (($loan->manager_approval ?: 'Pending') !== 'Approved') {
                                    $workflowStatus = 'Pending';
                                } elseif (($loan->finance_approval ?: 'Pending') !== 'Approved') {
                                    $workflowStatus = 'Pending Finance';
                                } elseif (($loan->hr_approval ?: 'Pending') !== 'Approved') {
                                    $workflowStatus = 'Pending HR';
                                } elseif ($loan->status === 'Pending' && (($loan->management_approval ?: 'Pending') !== 'Approved')) {
                                    $workflowStatus = 'Pending Management';
                                } else {
                                    $workflowStatus = 'Approved';
                                }

                                $statusBadgeClass = 'badge bg-warning';
                                if ($workflowStatus === 'Approved') $statusBadgeClass = 'badge bg-success';
                                elseif ($workflowStatus === 'Rejected') $statusBadgeClass = 'badge bg-danger';
                                elseif ($workflowStatus === 'New') $statusBadgeClass = 'badge bg-info';
                            @endphp
                                <td class="text-center" title="{{ $workflowStatus }}"><span class="{{ $statusBadgeClass }}">{{ $workflowStatus }}</span></td>
                                <td class="text-center align-middle">
                                    <div class="d-flex justify-content-center align-items-center loan-action-buttons flex-nowrap">
                                        @if(!empty($loanPermissions['edit']) && (!in_array($loan->status, ['Approved','Disbursed','Rejected']) || in_array(Auth::user()->role_id, [1,2])))
                                            <button type="button" class="btn btn-sm btn-light loan-edit-btn" data-id="{{ $loan->id }}" title="Edit">
                                                <i class="ico icon-outline-pen-2 text-dark" style="font-size: 16px;"></i>
                                            </button>
                                        @endif
                                        @if(!empty($loan->attachment))
                                            <a class="btn btn-sm btn-light" href="{{ asset('public/uploads/loan_docs/'.$loan->attachment) }}" target="_blank" title="Download Attachment">
                                                <i class="ico icon-bold-download-minimalistic text-dark" style="font-size: 16px;"></i>
                                            </a>
                                        @endif
                                        @if(!empty($loanPermissions['delete']))
                                            <a class="btn btn-sm btn-light" href="{{ url('loan-delete/'.$loan->id) }}" onclick="return confirm('Are you sure?')" title="Delete">
                                                <i class="ico icon-outline-trash-bin-minimalistic text-dark" style="font-size: 16px;"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="15" class="text-center text-muted p-4">No loan or advance requests found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $loans->links() }}</div>
        </div>
    </div>
</aside>

<div class="content-container {{ $hasFilters ? 'd-none' : 'col-9' }}">
    <div class="tab-content display-flex-tabs" id="loanTabContent">
        <div role="tabpanel" id="loan-details">
            @if ($selectedLoan)
                <div class="p-4 text-muted" data-initial-loan-id="{{ $selectedLoan->id }}">Loading loan details...</div>
            @else
                <div class="container-fluid d-flex flex-column justify-content-center align-items-center" style="min-height:60vh;">
                    <div class="text-center mb-4">
                        @if(!empty($loanPermissions['create']))
                        <button type="button" class="loan-add-btn border-0 rounded-circle bg-success text-white d-flex justify-content-center align-items-center mx-auto text-decoration-none" style="width:80px;height:80px;font-size:36px;">
                            <i class="ico icon-outline-add-square"></i>
                        </button>
                        <h1 class="fw-bold mt-3">
                            <button type="button" class="loan-add-btn border-0 bg-transparent text-dark text-decoration-none">Loans & Advance Request List</button>
                        </h1>
                        @else
                        <h1 class="fw-bold mt-3">Loans & Advance Request List</h1>
                        @endif
                        <p class="text-muted">No request selected.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="d-none" id="loan-edit-data">
    @foreach($loans as $loan)
        <input type="hidden" id="edit_request_no_{{ $loan->id }}" value="{{ $loan->document_number }}">
        <input type="hidden" id="edit_status_{{ $loan->id }}" value="{{ $loan->status }}">
        <input type="hidden" id="edit_request_type_{{ $loan->id }}" value="{{ $loan->request_type ?: (($loan->type_id == 1 || $loan->type_id == 4) ? 'Salary Advance' : 'Loan') }}">
        <input type="hidden" id="edit_loan_category_{{ $loan->id }}" value="{{ $loan->loan_category ?: ($typeMap[$loan->type_id] ?? '') }}">
        <input type="hidden" id="edit_amount_{{ $loan->id }}" value="{{ $loan->amount }}">
        <input type="hidden" id="edit_installments_{{ $loan->id }}" value="{{ $loan->installments }}">
        <input type="hidden" id="edit_repayment_start_{{ $loan->id }}" value="{{ $loan->list_edit_repayment_start }}">
        <input type="hidden" id="edit_repayment_end_{{ $loan->id }}" value="{{ $loan->list_edit_repayment_end }}">
        <input type="hidden" id="edit_repayment_mode_{{ $loan->id }}" value="{{ $loan->repayment_mode }}">
        <input type="hidden" id="edit_requested_disbursement_date_{{ $loan->id }}" value="{{ $loan->requested_disbursement_date }}">
        <input type="hidden" id="edit_purpose_{{ $loan->id }}" value="{{ $loan->purpose }}">
        <input type="hidden" id="edit_urgency_level_{{ $loan->id }}" value="{{ $loan->urgency_level ?: 'Normal' }}">
        <input type="hidden" id="edit_guarantor_employee_id_{{ $loan->id }}" value="{{ $loan->guarantor_employee_id }}">
        <input type="hidden" id="edit_guarantor_employee_no_{{ $loan->id }}" value="{{ $loan->guarantor_employee_no }}">
        <input type="hidden" id="edit_guarantor_department_{{ $loan->id }}" value="{{ $loan->guarantor_department }}">
        <input type="hidden" id="edit_guarantor_contact_number_{{ $loan->id }}" value="{{ $loan->guarantor_contact_number }}">
        <input type="hidden" id="edit_early_settlement_allowed_{{ $loan->id }}" value="{{ $loan->early_settlement_allowed ?: 'No' }}">
        <input type="hidden" id="edit_grace_period_required_{{ $loan->id }}" value="{{ $loan->grace_period_required ?: 'No' }}">
        <input type="hidden" id="edit_grace_period_months_{{ $loan->id }}" value="{{ $loan->grace_period_months }}">
        <input type="hidden" id="edit_attachment_remarks_{{ $loan->id }}" value="{{ $loan->attachment_remarks }}">
        <input type="hidden" id="edit_attachment_file_{{ $loan->id }}" value="{{ $loan->attachment }}">
        <input type="hidden" id="edit_declaration_accepted_{{ $loan->id }}" value="{{ $loan->declaration_accepted_at ? 1 : 0 }}">
    @endforeach
</div>

<div class="modal side-panel fade" id="loanRequestModal" data-bs-backdrop="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="left:30%">
        <form class="form-horizontal" files="true" action="{{ route('employee.loans.store') }}" method="POST" enctype="multipart/form-data" id="loanRequestForm">
            @csrf
            <input type="hidden" name="_method" id="loan_form_method" value="POST">
            <input type="hidden" name="action_type" id="loan_action_type" value="draft">
            <input type="hidden" name="declaration_info_confirmed" id="declaration_info_confirmed" value="">
            <input type="hidden" name="declaration_salary_deduction_authorized" id="declaration_salary_deduction_authorized" value="">
            <input type="hidden" name="declaration_policy_agreed" id="declaration_policy_agreed" value="">
            <input type="hidden" name="declaration_final_settlement_agreed" id="declaration_final_settlement_agreed" value="">
            <input type="hidden" name="declaration_false_info_understood" id="declaration_false_info_understood" value="">
            <input type="hidden" name="guarantor_employee_no" id="loan_guarantor_employee_no">
            <input type="hidden" name="guarantor_department" id="loan_guarantor_department">
            <input type="hidden" name="guarantor_contact_number" id="loan_guarantor_contact_number">

            <div class="modal-content" style="max-height:80vh">
                <div class="modal-header">
                    <h4 class="modal-title" id="loanModalTitle">New ({{ $requestNumber }})</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body">
                            <div id="loanFormAlert" class="alert alert-danger d-none"></div>
                            <div class="row gap-rows">
                                <div class="col-md-3">
                                    <label class="form-label">Request Type*</label>
                                    <select class="form-control js-example-basic-single" name="request_type" id="loan_request_type" required>
                                        <option value="">-Select-</option>
                                        <option value="Loan" selected>Loan</option>
                                        <option value="Salary Advance">Salary Advance</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Loan Category*</label>
                                    <select class="form-control js-example-basic-single" name="loan_category" id="loan_category" required>
                                        <option value="">-Select-</option>
                                        @foreach($loanCategories as $category)
                                            <option value="{{ $category }}" {{ $category === 'Personal' ? 'selected' : '' }}>{{ $category }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Amount Requested*</label>
                                    <input type="text" inputmode="decimal" class="form-control text-end" name="amount" id="loan_amount" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Installment Number*</label>
                                    <input type="number" min="1" class="form-control text-end" name="installments" id="loan_installments" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Monthly Deduction Amount</label>
                                    <input type="text" class="form-control text-end" id="loan_monthly_deduction" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Repayment Start Month*</label>
                                    <input type="month" class="form-control" name="repayment_start" id="loan_repayment_start" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Repayment Mode*</label>
                                    <select class="form-control js-example-basic-single" name="repayment_mode" id="loan_repayment_mode" required>
                                        <option value="">-Select-</option>
                                        @foreach(['Salary Deduction','Bank Transfer','Cash Payment','Adjustment'] as $mode)
                                            <option value="{{ $mode }}" {{ $mode === 'Salary Deduction' ? 'selected' : '' }}>{{ $mode }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Disbursement Date*</label>
                                    <input type="date" class="form-control" name="requested_disbursement_date" id="loan_requested_disbursement_date" min="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Urgency Level*</label>
                                    <select class="form-control js-example-basic-single" name="urgency_level" id="loan_urgency_level" required>
                                        <option value="">-Select-</option>
                                        <option value="Normal">Normal</option>
                                        <option value="Urgent" selected>Urgent</option>
                                        <option value="Critical">Critical</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Guarantor Employee<span class="loan-submit-required text-danger">*</span></label>
                                    <select class="form-control js-example-basic-single" name="guarantor_employee_id" id="loan_guarantor_employee_id">
                                        <option value="">-Select-</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}">{{ $employee->full_name ?: trim($employee->first_name . ' ' . $employee->last_name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Early Settlement Allowed</label>
                                    <select class="form-control" name="early_settlement_allowed" id="loan_early_settlement_allowed">
                                        <option value="No" selected>No</option>
                                        <option value="Yes">Yes</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Grace Period Required</label>
                                    <select class="form-control" name="grace_period_required" id="loan_grace_period_required">
                                        <option value="No" selected>No</option>
                                        <option value="Yes">Yes</option>
                                    </select>
                                </div>
                                <div class="col-md-3" id="loan_grace_months_box" style="display:none">
                                    <label class="form-label">Grace Period Months*</label>
                                    <input type="number" min="1" class="form-control text-end" name="grace_period_months" id="loan_grace_period_months">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Reason / Purpose*</label>
                                    <textarea class="form-control" name="purpose" id="loan_purpose" rows="2" required></textarea>
                                </div>
                                @if(!empty($loanPermissions['attach']))
                                <div class="col-md-3">
                                    <label class="form-label">Attachment</label>
                                    <input type="file" class="form-control" name="attachment" id="loan_attachment">
                                    <div id="loan_existing_attachment" class="mt-1"></div>
                                </div>
                                @endif
                                <div class="col-md-6">
                                    <label class="form-label">Remarks</label>
                                    <input type="text" class="form-control" name="attachment_remarks" id="loan_attachment_remarks">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light add-btn ms-2 loan-action-btn" data-action="draft">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Save as Draft
                    </button>
                    <button type="button" class="btn btn-light add-btn ms-2 loan-action-btn" data-action="submit">
                        <i class="ico icon-outline-send-square text-success"></i> Submit for Approval
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="loanDeclarationModal" data-bs-backdrop="false" tabindex="-1" aria-hidden="true" style="background:rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Employee Declaration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="loanDeclarationAlert" class="alert alert-danger d-none">Please accept all declaration confirmations before continuing.</div>
                @php
                    $declarationEmployeeName = optional($employee)->full_name ?: trim(optional($employee)->first_name . ' ' . optional($employee)->last_name);
                    $declarationEmployeeName = $declarationEmployeeName ?: (Auth::user()->full_name ?: Auth::user()->email);
                    $declarationEmployeeId = optional($employee)->staff_no ?: optional($employee)->id ?: Auth::user()->id;
                @endphp
                <div class="border rounded p-3 mb-3" style="max-height:360px;overflow-y:auto;background:#fff;line-height:1.55;">
                    <p class="mb-3">I, <strong>{{ $declarationEmployeeName }}</strong>, Employee ID <strong>{{ $declarationEmployeeId }}</strong>, hereby apply for a loan/salary advance from the Company and declare that the information, documents, and supporting evidence provided in this application are true, complete, and accurate to the best of my knowledge.</p>
                    <p class="mb-3">I understand and agree to the following terms and conditions:</p>

                    <h6 class="fw-bold mb-2">1. Accuracy of Information</h6>
                    <ul class="mb-3 ps-4">
                        <li>I confirm that all information submitted in this application is correct and free from any misrepresentation.</li>
                        <li>I acknowledge that providing false, misleading, or incomplete information may result in rejection of my application, disciplinary action, recovery proceedings, or other actions as deemed appropriate by the Company.</li>
                    </ul>

                    <h6 class="fw-bold mb-2">2. Purpose of Loan</h6>
                    <ul class="mb-3 ps-4">
                        <li>I declare that the requested loan amount will be used solely for the purpose stated in this application.</li>
                        <li>I understand that the Company may request additional supporting documents or clarification regarding the purpose of the loan.</li>
                    </ul>

                    <h6 class="fw-bold mb-2">3. Repayment Authorization</h6>
                    <ul class="mb-3 ps-4">
                        <li>I irrevocably authorize the Company to deduct the approved loan installments, including any outstanding balances, from my monthly salary, incentives, commissions, end-of-service benefits, leave salary, bonuses, or any other amounts payable to me by the Company.</li>
                        <li>I agree that such deductions may continue until the loan is fully settled.</li>
                    </ul>

                    <h6 class="fw-bold mb-2">4. Employment Separation</h6>
                    <ul class="mb-3 ps-4">
                        <li>In the event of resignation, termination, retirement, absconding, or any cessation of employment for any reason, I authorize the Company to recover any outstanding loan balance from my final settlement, gratuity, leave encashment, or any other dues payable to me.</li>
                        <li>If the final settlement amount is insufficient to cover the outstanding balance, I undertake to repay the remaining amount immediately upon demand by the Company.</li>
                    </ul>

                    <h6 class="fw-bold mb-2">5. Loan Approval Discretion</h6>
                    <ul class="mb-3 ps-4">
                        <li>I understand that submission of this application does not guarantee approval.</li>
                        <li>The Company reserves the right to approve, partially approve, modify, defer, or reject my application without assigning any reason.</li>
                        <li>The approved amount, repayment period, and loan conditions may differ from the amount requested.</li>
                    </ul>

                    <h6 class="fw-bold mb-2">6. Policy Compliance</h6>
                    <ul class="mb-3 ps-4">
                        <li>I confirm that I have read, understood, and agree to comply with the Company's Employee Loan Policy and any amendments issued from time to time.</li>
                        <li>I acknowledge that the loan is subject to Company policies, management approval, and applicable legal requirements.</li>
                    </ul>

                    <h6 class="fw-bold mb-2">7. Recovery of Overdue Amounts</h6>
                    <ul class="mb-3 ps-4">
                        <li>In the event of missed repayments, payroll processing limitations, or any outstanding balance, I authorize the Company to recover the amount through salary deductions, direct payment requests, or any other lawful recovery mechanism.</li>
                    </ul>

                    <h6 class="fw-bold mb-2">8. Document Verification</h6>
                    <ul class="mb-3 ps-4">
                        <li>I authorize the Company to verify any information, supporting documents, or references provided as part of this application.</li>
                        <li>I agree to provide any additional information or documentation requested by the Company during the review process.</li>
                    </ul>

                    <h6 class="fw-bold mb-2">9. Data Privacy &amp; Records</h6>
                    <ul class="mb-3 ps-4">
                        <li>I consent to the Company collecting, storing, processing, and maintaining my personal information and loan-related records for administrative, financial, audit, compliance, and legal purposes.</li>
                    </ul>

                    <h6 class="fw-bold mb-2">10. Acknowledgment</h6>
                    <ul class="mb-0 ps-4">
                        <li>I acknowledge that I have carefully read and understood all the terms and conditions associated with this loan application and voluntarily agree to abide by them.</li>
                    </ul>
                </div>

                <h6 class="fw-bold mb-2">Employee Confirmation</h6>
                @foreach([
                    'loan_decl_info' => 'I confirm that the information provided is true and accurate.',
                    'loan_decl_deduction' => 'I authorize the Company to deduct loan installments and any outstanding balance from my salary and other dues.',
                    'loan_decl_policy' => "I have read, understood, and agree to comply with the Company's Employee Loan Policy.",
                    'loan_decl_separation' => 'I agree to repay any outstanding balance upon separation from employment if not fully recovered through my final settlement.',
                ] as $id => $text)
                    <div class="form-check mb-2">
                        <input class="form-check-input loan-declaration-check" type="checkbox" value="1" id="{{ $id }}">
                        <label class="form-check-label" for="{{ $id }}">{{ $text }}</label>
                    </div>
                @endforeach
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light add-btn" id="loanDeclarationContinue" disabled>
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Agreed & Submit
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function list_style_new_loans() {
        var leftNav = document.getElementById('leftSidebar');
        var content = document.querySelector('.content-container');
        var full = leftNav.dataset.view !== 'full';

        leftNav.dataset.view = full ? 'full' : 'compact';
        leftNav.classList.toggle('col-3', !full);
        leftNav.classList.toggle('col-12', full);
        if (content) content.classList.toggle('d-none', full);
        $('#loanShortList').toggleClass('d-none', full);
        $('#filters-short').toggleClass('d-none', full);
        $('#filters-long').toggleClass('d-none', !full);
        $('#long-list').toggleClass('d-none', !full);
    }

    function search_box_show_hide() {
        $('#long-filters-box').slideToggle(200);
    }

    $(function () {
        var detailsTpl = @json(route('employee.loans.show', ['id' => ':id']));
        var updateTpl = @json(route('employee.loans.update', ['id' => ':id']));
        var indexUrl = @json(route('employee.loans.index'));
        var storeUrl = @json(route('employee.loans.store'));
        var guarantorTpl = @json(route('employee.loans.guarantor', ['id' => ':id']));
        var nextRequestNumber = @json($requestNumber);
        var pendingLoanAction = 'draft';
        var currentLoanEditId = null;
        var initialLoanId = @json(optional($selectedLoan)->id);

        function detailUrl(id) { return detailsTpl.replace(':id', encodeURIComponent(id)); }
        function updateUrl(id) { return updateTpl.replace(':id', encodeURIComponent(id)); }
        function guarantorUrl(id) { return guarantorTpl.replace(':id', encodeURIComponent(id)); }

        function showLoanFormAlert(message) {
            $('#loanFormAlert').removeClass('d-none').html(message || 'Please complete required fields.');
        }

        function clearLoanFormAlert() {
            $('#loanFormAlert').addClass('d-none').html('');
        }

        function setLoanSelectValue(selector, value) {
            $(selector).val(value || '').trigger('change');
        }

        function cleanMoneyValue(value) {
            return String(value || '').replace(/,/g, '').replace(/[^\d.]/g, '').trim();
        }

        function formatMoneyValue(value) {
            var clean = cleanMoneyValue(value);
            if (clean === '' || isNaN(clean)) {
                return '';
            }
            return Number(clean).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function calculateLoanRepayment() {
            var amount = parseFloat(cleanMoneyValue($('#loan_amount').val())) || 0;
            var installments = parseInt($('#loan_installments').val(), 10) || 0;
            var start = $('#loan_repayment_start').val();

            $('#loan_monthly_deduction').val(amount > 0 && installments > 0 ? formatMoneyValue((amount / installments).toFixed(2)) : '');
            $('#loan_repayment_end').val('');

            if (!start || installments <= 0) return;
            var parts = start.split('-');
            var date = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, 1);
            date.setMonth(date.getMonth() + installments - 1);
            var month = String(date.getMonth() + 1).padStart(2, '0');
            $('#loan_repayment_end').val(date.getFullYear() + '-' + month);
        }

        function toggleLoanGracePeriod() {
            var required = $('#loan_grace_period_required').val() === 'Yes';
            $('#loan_grace_months_box').toggle(required);
            $('#loan_grace_period_months').prop('required', required);
            if (!required) {
                $('#loan_grace_period_months').val('');
            }
        }

        function renderLoanAttachment(file) {
            if (!file) {
                $('#loan_existing_attachment').html('');
                return;
            }
            $('#loan_existing_attachment').html('<a class="text-success" target="_blank" href="{{ asset('public/uploads/loan_docs') }}/' + file + '">' + file + '</a>');
        }

        function resetDeclarationModal() {
            $('.loan-declaration-check').prop('checked', false);
            $('#loanDeclarationContinue').prop('disabled', true);
            $('#loanDeclarationAlert').addClass('d-none');
        }

        function resetLoanForm() {
            var form = $('#loanRequestForm')[0];
            form.reset();
            clearLoanFormAlert();
            $('#loanRequestForm').attr('action', storeUrl);
            currentLoanEditId = null;
            $('#loan_form_method').val('POST');
            $('#loan_action_type').val('draft');
            $('#loanModalTitle').text('New (' + nextRequestNumber + ')');
            $('#loan_request_no').val(nextRequestNumber);
            $('#loan_request_date').val('{{ date('Y-m-d') }}');
            $('#loan_monthly_deduction').val('');
            $('#loan_repayment_end').val('');
            $('#loan_existing_attachment').html('');
            $('#loan_attachment').val('');
            $('#declaration_info_confirmed, #declaration_salary_deduction_authorized, #declaration_policy_agreed, #declaration_final_settlement_agreed, #declaration_false_info_understood').val('');
            setLoanSelectValue('#loan_request_type', 'Loan');
            setLoanSelectValue('#loan_category', 'Personal');
            setLoanSelectValue('#loan_repayment_mode', 'Salary Deduction');
            setLoanSelectValue('#loan_urgency_level', 'Urgent');
            setLoanSelectValue('#loan_guarantor_employee_id', '');
            $('#loan_guarantor_employee_no, #loan_guarantor_department, #loan_guarantor_contact_number').val('');
            $('#loan_early_settlement_allowed').val('No');
            $('#loan_grace_period_required').val('No');
            toggleLoanGracePeriod();
        }

        function prepareLoanAddForm() {
            resetLoanForm();
            $('#loanRequestModal').modal('show');
        }

        function getEditValue(name, id) {
            return $('#edit_' + name + '_' + id).val() || '';
        }

        function prepareLoanEditForm(id) {
            resetLoanForm();
            currentLoanEditId = id;
            $('#loanRequestForm').attr('action', updateUrl(id));
            $('#loan_form_method').val('PUT');
            $('#loanModalTitle').text('Update (' + getEditValue('request_no', id) + ')');
            $('#loan_request_no').val(getEditValue('request_no', id));
            setLoanSelectValue('#loan_request_type', getEditValue('request_type', id));
            setLoanSelectValue('#loan_category', getEditValue('loan_category', id));
            $('#loan_amount').val(formatMoneyValue(getEditValue('amount', id)));
            $('#loan_installments').val(getEditValue('installments', id));
            $('#loan_repayment_start').val(getEditValue('repayment_start', id));
            setLoanSelectValue('#loan_repayment_mode', getEditValue('repayment_mode', id));
            $('#loan_requested_disbursement_date').val(getEditValue('requested_disbursement_date', id));
            $('#loan_purpose').val(getEditValue('purpose', id));
            setLoanSelectValue('#loan_urgency_level', getEditValue('urgency_level', id));
            setLoanSelectValue('#loan_guarantor_employee_id', getEditValue('guarantor_employee_id', id));
            $('#loan_guarantor_employee_no').val(getEditValue('guarantor_employee_no', id));
            $('#loan_guarantor_department').val(getEditValue('guarantor_department', id));
            $('#loan_guarantor_contact_number').val(getEditValue('guarantor_contact_number', id));
            $('#loan_early_settlement_allowed').val(getEditValue('early_settlement_allowed', id) || 'No');
            $('#loan_grace_period_required').val(getEditValue('grace_period_required', id) || 'No');
            $('#loan_grace_period_months').val(getEditValue('grace_period_months', id));
            $('#loan_attachment_remarks').val(getEditValue('attachment_remarks', id));
            renderLoanAttachment(getEditValue('attachment_file', id));
            toggleLoanGracePeriod();
            calculateLoanRepayment();
            $('#loanRequestModal').modal('show');
        }

        function validateLoanForm() {
            clearLoanFormAlert();
            var form = $('#loanRequestForm')[0];
            if (!form.checkValidity()) {
                form.reportValidity();
                return false;
            }
            if ((parseFloat(cleanMoneyValue($('#loan_amount').val())) || 0) <= 0) {
                showLoanFormAlert('Amount Requested must be greater than zero.');
                $('#loan_amount').focus();
                return false;
            }
            if ($('#loan_grace_period_required').val() === 'Yes' && !$('#loan_grace_period_months').val()) {
                showLoanFormAlert('Grace Period Months is required when Grace Period Required is Yes.');
                $('#loan_grace_period_months').focus();
                return false;
            }
            if (pendingLoanAction === 'submit' && !$('#loan_guarantor_employee_id').val()) {
                showLoanFormAlert('Guarantor Employee is required.');
                $('#loan_guarantor_employee_id').addClass('is-invalid');
                $('#loan_guarantor_employee_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                $('#loan_guarantor_employee_id').select2('open');
                return false;
            }
            return true;
        }

        function submitLoanForm(acceptDeclaration) {
            if (acceptDeclaration) {
                $('#declaration_info_confirmed').val('1');
                $('#declaration_salary_deduction_authorized').val('1');
                $('#declaration_policy_agreed').val('1');
                $('#declaration_final_settlement_agreed').val('1');
                $('#declaration_false_info_understood').val('1');
            } else {
                $('#declaration_info_confirmed, #declaration_salary_deduction_authorized, #declaration_policy_agreed, #declaration_final_settlement_agreed, #declaration_false_info_understood').val('');
            }
            $('#loan_action_type').val(pendingLoanAction);

            var form = $('#loanRequestForm')[0];
            var formData = new FormData(form);
            formData.set('amount', cleanMoneyValue($('#loan_amount').val()));
            $('#loading_bg').show();
            $.ajax({
                url: $('#loanRequestForm').attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: { 'Accept': 'application/json' },
                success: function (response) {
                    $('#loanDeclarationModal').modal('hide');
                    $('#loanRequestModal').modal('hide');
                    var id = response && response.id ? response.id : '';
                    window.location.href = indexUrl + (id ? '?active=' + encodeURIComponent(id) : '');
                },
                error: function (xhr) {
                    var message = 'Unable to save loan request.';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        var lines = [];
                        $.each(xhr.responseJSON.errors, function (field, values) {
                            lines.push(values.join('<br>'));
                        });
                        message = lines.join('<br>');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    $('#loanDeclarationModal').modal('hide');
                    showLoanFormAlert(message);
                },
                complete: function () {
                    $('#loading_bg').hide();
                }
            });
        }

        $(document).on('click', '.loan-add-btn', function (e) {
            e.preventDefault();
            prepareLoanAddForm();
        });

        $(document).on('click', '.loan-edit-btn', function (e) {
            e.preventDefault();
            e.stopPropagation();
            var id = $(this).data('id');
            if (id) {
                prepareLoanEditForm(id);
            }
        });

        $('.loan-action-btn').on('click', function () {
            pendingLoanAction = $(this).data('action') === 'submit' ? 'submit' : 'draft';
            if (!validateLoanForm()) return;
            if (pendingLoanAction === 'draft') {
                submitLoanForm(false);
                return;
            }
            var editAlreadyAccepted = currentLoanEditId && $('#edit_declaration_accepted_' + currentLoanEditId).val() == '1';
            var editStatus = currentLoanEditId ? ($('#edit_status_' + currentLoanEditId).val() || '') : '';
            if (editAlreadyAccepted || (currentLoanEditId && editStatus !== 'Draft')) {
                submitLoanForm(false);
                return;
            }
            resetDeclarationModal();
            $('#loanDeclarationModal').modal('show');
        });

        $('.loan-declaration-check').on('change', function () {
            var allAccepted = $('.loan-declaration-check').length === $('.loan-declaration-check:checked').length;
            $('#loanDeclarationContinue').prop('disabled', !allAccepted);
            if (allAccepted) {
                $('#loanDeclarationAlert').addClass('d-none');
            }
        });

        $('#loanDeclarationContinue').on('click', function () {
            var allAccepted = $('.loan-declaration-check').length === $('.loan-declaration-check:checked').length;
            if (!allAccepted) {
                $('#loanDeclarationAlert').removeClass('d-none');
                return;
            }
            submitLoanForm(true);
        });

        $('#loan_amount').on('focus', function () {
            $(this).val(cleanMoneyValue($(this).val()));
        });
        $('#loan_amount').on('input', function () {
            $(this).val(cleanMoneyValue($(this).val()));
            calculateLoanRepayment();
        });
        $('#loan_amount').on('blur change', function () {
            $(this).val(formatMoneyValue($(this).val()));
            calculateLoanRepayment();
        });
        $('#loan_installments, #loan_repayment_start').on('input change', calculateLoanRepayment);
        $('#loan_grace_period_required').on('change', toggleLoanGracePeriod);
        $('#loan_guarantor_employee_id').on('change', function () {
            var id = $(this).val();
            $(this).removeClass('is-invalid');
            $(this).next('.select2-container').find('.select2-selection').removeClass('is-invalid');
            $('#loan_guarantor_employee_no, #loan_guarantor_department, #loan_guarantor_contact_number').val('');
            if (!id) return;
            $.ajax({
                url: guarantorUrl(id),
                method: 'GET',
                success: function (data) {
                    $('#loan_guarantor_employee_no').val(data.employee_id || '');
                    $('#loan_guarantor_department').val(data.department || '');
                    $('#loan_guarantor_contact_number').val(data.contact_number || '');
                }
            });
        });

        $('#loanRequestModal').on('shown.bs.modal', function () {
            $('#loanRequestModal .js-example-basic-single').select2({
                dropdownParent: $('#loanRequestModal')
            });
        });

        function loadLoanDetails(id, updateUrl) {
            if (!id) return;

            $('.lv-item').removeClass('active');
            $('.lv-item[data-id="' + id + '"]').addClass('active');

            var newUrl = indexUrl + '?{{ http_build_query(request()->except('active')) }}&active=' + encodeURIComponent(id);
            if (updateUrl && window.history && window.history.pushState) {
                window.history.pushState({ path: newUrl }, '', newUrl);
            }

            $('#loading_bg').show();
            $.ajax({
                url: detailUrl(id),
                method: 'GET',
                cache: false,
                success: function (html) {
                    $('#loan-details').html(html && $.trim(html).length ? html : '<p class="text-danger">No Details Available.</p>');
                },
                error: function () {
                    $('#loan-details').html('<p class="text-danger">No Details Available.</p>');
                },
                complete: function () {
                    $('#loading_bg').hide();
                }
            });
        }

        if (initialLoanId) {
            loadLoanDetails(initialLoanId, false);
        }

        $(document).on('click', '.lv-item', function (e) {
            e.preventDefault();
            loadLoanDetails($(this).data('id'), true);
        });

        $('#tableSearch').on('input', function () {
            var needle = (this.value || '').toLowerCase();
            $('#long-list tbody tr').each(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(needle) !== -1);
            });
        });
    });
</script>
@endsection

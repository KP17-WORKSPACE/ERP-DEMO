@extends('backEnd.newmasterpage')
@section('mainContent')
@php
    $loanPermissions = $loanPermissions ?? ['create' => true, 'view' => true, 'edit' => true, 'delete' => true, 'export' => true, 'attach' => true];
    $trackPermissions = $trackPermissions ?? ['create' => true, 'view' => true, 'edit' => true, 'delete' => true, 'export' => true, 'attach' => true];
    $reportPermissions = $reportPermissions ?? ['create' => true, 'view' => true, 'edit' => true, 'delete' => true, 'export' => true, 'attach' => true];
@endphp
<style>
    .loan-report-toolbar {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
        flex-wrap: wrap;
    }
    .loan-report-toolbar .loan-list-search {
        width: 320px;
        flex: 0 0 320px;
        font-size: 13px;
        height: 32px;
    }
    .loan-report-toolbar .btn,
    .loan-report-toolbar .list_style_expand_btn,
    .loan-report-toolbar .list_style_search_btn {
        position: static;
        top: auto;
        right: auto;
        margin-right: 0 !important;
    }
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
    .loan-table-wrapper .badge {
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        vertical-align: middle;
    }
    @media (max-width: 992px) {
        .loan-report-toolbar {
            justify-content: flex-start;
        }
        .loan-report-toolbar .loan-list-search {
            width: 100%;
            flex: 1 1 280px;
        }
    }
</style>

<aside class="left-nav col-12" id="leftSidebar" data-view="full">
    <div class="resizer" id="sidebarResizer"></div>

    <div class="long-list" id="filters-long">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0">Loan Report</h4>

            <div class="search-filter-container mb-0 loan-report-toolbar">
                <input type="text" id="tableSearch" class="form-control loan-list-search" placeholder="Search">
                @if(!empty($reportPermissions['export']))
                <a href="{{ route('employee.loans.report.export', request()->query()) }}" class="btn btn-light list_style_search_btn">
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
                        @if(!empty($reportPermissions['export']))
                        <li><a class="dropdown-item" href="{{ route('employee.loans.report.export', request()->query()) }}"><i class="ico icon-outline-export text-success"></i> Download / Export</a></li>
                        @endif
                        @if(!empty($loanPermissions['view']))
                        <li><a class="dropdown-item" href="{{ route('employee.loans.index') }}"><i class="ico icon-outline-list-down text-success"></i> Loans &amp; Advances</a></li>
                        @endif
                        @if(!empty($trackPermissions['view']))
                        <li><a class="dropdown-item" href="{{ route('employee.loans.approvals') }}"><i class="ico icon-outline-list-down text-success"></i> Loan Track</a></li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <div class="search-filter-container mt-1 mb-4 filter-field border" id="long-filters-box" style="display: {{ request()->query() ? 'block' : 'none' }};">
            <form method="get" action="{{ route('employee.loans.report') }}" id="loan-report-filter">
                <div class="row">
                    <div class="col-1-5 mb-2 filter-field">
                        <label class="form-label">Status</label>
                        <select class="form-control" name="status">
                            <option value="">-Select-</option>
                            @foreach (['Pending','Approved','Disbursed','Closed','Rejected'] as $status)
                                <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ $status }}</option>
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
                        <label class="form-label">Repayment Mode</label>
                        <select class="form-control" name="repayment_mode">
                            <option value="">-Select-</option>
                            @foreach (['Salary Deduction','Bank Transfer','Cash Payment','Adjustment'] as $mode)
                                <option value="{{ $mode }}" {{ request('repayment_mode') === $mode ? 'selected' : '' }}>{{ $mode }}</option>
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
                        <label class="form-label">Search</label>
                        <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Doc No / Employee / Status">
                    </div>
                    <div class="col-1-5 mb-2 filter-field d-flex align-items-end">
                        <button type="submit" class="btn btn-light me-2">
                            <i class="ico icon-outline-magnifer"></i> Filter
                        </button>
                        <a href="{{ route('employee.loans.report') }}" class="btn btn-light">
                            <i class="ico icon-bold-restart text-success"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="left-nav-list">
        <div id="long-list">
            <div class="table-responsive mb-4 mt-4 loan-table-wrapper">
                <table class="table table-hover mt-0 data-table" style="table-layout: fixed;width:100%">
                    <thead>
                        <tr>
                            <th style="width: 5.5%;" title="Date">Date</th>
                            <th style="width: 6.5%;" title="Doc No">Doc No</th>
                            <th style="width: 8%;" title="Employee Name">Employee Name</th>
                            <th style="width: 7%;" class="text-end" title="Amount Requested">Amount Requested</th>
                            <th style="width: 6.5%;" class="text-end" title="Installment Number">Installment Number</th>
                            <th style="width: 8%;" class="text-end" title="Monthly Deduction Amount">Monthly Deduction Amount</th>
                            <th style="width: 7%;" title="Repayment Start Month">Repayment Start Month</th>
                            <th style="width: 7%;" title="Repayment Mode">Repayment Mode</th>
                            <th style="width: 7%;" class="text-end" title="Original Loan Amount">Original Loan Amount</th>
                            <th style="width: 7%;" class="text-end" title="Recovered Amount">Recovered Amount</th>
                            <th style="width: 7.5%;" class="text-end" title="Outstanding Balance">Outstanding Balance</th>
                            <th style="width: 7.5%;" class="text-end" title="Remaining Installments">Remaining Installments</th>
                            <th style="width: 7%;" title="Next Deduction Date">Next Deduction Date</th>
                            <th style="width: 6%;" title="Status">Status</th>
                        </tr>
                    </thead>
                    <tbody style="font-size:12px">
                        @forelse ($loans as $loan)
                            @php
                                $statusBadgeClass = 'badge bg-warning';
                                if ($loan->report_status === 'Approved' || $loan->report_status === 'Active' || $loan->report_status === 'Closed') $statusBadgeClass = 'badge bg-success';
                                elseif ($loan->report_status === 'Rejected') $statusBadgeClass = 'badge bg-danger';
                            @endphp
                            <tr>
                                <td class="text-center">{{ $loan->list_date ?: '-' }}</td>
                                <td class="text-center">
                                    <a class="text-success" href="{{ route('employee.loans.track', $loan->id) }}" target="_blank" style="cursor:pointer" title="{{ $loan->document_number }}">{{ $loan->document_number }}</a>
                                </td>
                                <td title="{{ $loan->list_employee_name }}">{{ $loan->list_employee_name ?: '-' }}</td>
                                <td class="text-end">{{ $loan->list_original_amount }}</td>
                                <td class="text-end">{{ $loan->installments ?: '-' }}</td>
                                <td class="text-end">{{ $loan->list_monthly_deduction === '-' ? '0.00' : $loan->list_monthly_deduction }}</td>
                                <td title="{{ $loan->list_repayment_start }}">{{ $loan->list_repayment_start }}</td>
                                <td title="{{ $loan->repayment_mode ?: '-' }}">{{ $loan->repayment_mode ?: '-' }}</td>
                                <td class="text-end">{{ $loan->list_original_amount }}</td>
                                <td class="text-end">{{ $loan->list_recovered_amount }}</td>
                                <td class="text-end">{{ $loan->list_outstanding_amount }}</td>
                                <td class="text-end">{{ $loan->list_remaining_installments }}</td>
                                <td class="text-center">{{ $loan->list_next_deduction_date }}</td>
                                <td class="text-center" title="{{ $loan->report_status }}"><span class="{{ $statusBadgeClass }}">{{ $loan->report_status }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="14" class="text-center text-muted p-4">No loan report records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $loans->links() }}</div>
        </div>
    </div>
</aside>

<script>
    function search_box_show_hide() {
        $('#long-filters-box').slideToggle(200);
    }

    $(function () {
        $('#tableSearch').on('input', function () {
            var needle = (this.value || '').toLowerCase();
            $('#long-list tbody tr').each(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(needle) !== -1);
            });
        });
    });
</script>
@endsection

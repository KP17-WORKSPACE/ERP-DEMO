@extends('backEnd.newmasterpage')
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/exceljs@4.3.0/dist/exceljs.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
@endpush
@section('mainContent')
    <div class="content-container col-12">
        <div class="tab-content display-flex-tabs">
            <div>
                <div class="purchase-order-content-header">
                    <div class="purchase-order-content-header-left">
                        <div class="dropdown report-type-dropdown">
                            <a class="text-dark report-type-trigger" href="javascript:void(0);" id="salesReportTypeMenu" data-bs-toggle="dropdown" aria-expanded="false">
                                Sales Report Type <i class="icon-outline-alt-arrow-down ms-1"></i>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="salesReportTypeMenu">
                                <li class="dropend">
                                    <a class="dropdown-item dropdown-toggle report-submenu-trigger text-dark" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false">Sales Report</a>
                                    <ul class="dropdown-menu">
                                        <li><a class="text-dark dropdown-item" href="{{ route('sales.invoice.report.detail', ['report_group' => 'company_wise']) }}">Company Wise</a></li>
                                        <li><a class="text-dark dropdown-item" href="{{ route('sales.invoice.report.detail', ['report_group' => 'date_wise']) }}">Date Wise</a></li>
                                        <li><a class="text-dark dropdown-item" href="{{ route('sales.invoice.report.detail', ['report_group' => 'customer_wise']) }}">Customer Wise</a></li>
                                        <li><a class="text-dark dropdown-item" href="{{ route('sales.invoice.report.detail', ['report_group' => 'sales_person_wise']) }}">Sales Person Wise</a></li>
                                    </ul>
                                </li>
                                <li class="dropend">
                                    <a class="dropdown-item dropdown-toggle report-submenu-trigger text-dark" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false">Sales Return Report</a>
                                    <ul class="dropdown-menu">
                                        <li><a class="text-dark dropdown-item" href="{{ route('sales.return.report.detail', ['report_group' => 'company_wise']) }}">Company Wise</a></li>
                                        <li><a class="text-dark dropdown-item" href="{{ route('sales.return.report.detail', ['report_group' => 'date_wise']) }}">Date Wise</a></li>
                                        <li><a class="text-dark dropdown-item" href="{{ route('sales.return.report.detail', ['report_group' => 'customer_wise']) }}">Customer Wise</a></li>
                                        <li><a class="text-dark dropdown-item" href="{{ route('sales.return.report.detail', ['report_group' => 'sales_person_wise']) }}">Sales Person Wise</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="purchase-order-content-header-right">
                        <button type="button" class="btn btn-light" id="exportSalesReturnReport" title="Export to Excel">
                            <i class="ico icon-outline-export text-success"></i> Export
                        </button>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-body">
                        {{ Form::open(['class' => 'form-horizontal', 'url' => 'sales-return-report-detail', 'method' => 'get', 'id' => 'sales-return-report']) }}
                        <input type="hidden" name="report_group" value="{{ $report_group ?? 'date_wise' }}">
                        <input type="hidden" name="scope_company_id" value="{{ $scope_company_id ?? '' }}">
                        <div class="row">
                            <div class="col-1-5 mb-2">
                                <label class="form-label">Documents Number</label>
                                <input class="form-control" type="text" autocomplete="off" name="documents_number" value="{{ $ctrl_doc_no }}">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Customer</label>
                                <select class="form-control js-example-basic-single" name="customer" id="customer">
                                    <option value="">All</option>
                                    @foreach ($customer_list as $value)
                                        <option value="{{ $value->id }}" {{ $ctrl_customer == $value->id ? 'selected' : '' }}>{{ $value->account_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-1-5 mb-2">
                                <label class="form-label">Deal ID</label>
                                <input class="form-control" type="text" autocomplete="off" name="deal_number" value="{{ $ctrl_deal_id }}">
                            </div>
                            <div class="col-1 mb-2">
                                <label class="form-label">Amount</label>
                                <input class="form-control" type="number" autocomplete="off" name="amount" value="{{ $ctrl_amount }}">
                            </div>
                            <div class="col-1-5 mb-2">
                                <label class="form-label">From Date</label>
                                <input class="form-control date-picker" type="text" autocomplete="off" name="from_date" id="from_date" value="{{ $ctrl_date ? \Carbon\Carbon::parse($ctrl_date)->format('d/m/Y') : '' }}" onchange="set_filter()">
                            </div>
                            <div class="col-1-5 mb-2">
                                <label class="form-label">To Date</label>
                                <input class="form-control date-picker" type="text" autocomplete="off" name="to_date" id="to_date" value="{{ $ctrl_date2 ? \Carbon\Carbon::parse($ctrl_date2)->format('d/m/Y') : '' }}" onchange="set_filter()">
                            </div>
                            @if (($report_group ?? '') === 'customer_wise')
                                <div class="col-1 mb-2">
                                    <label class="form-label">From Day</label>
                                    <input class="form-control" type="number" min="0" autocomplete="off" name="from_day" id="from_day" value="{{ $ctrl_from_day ?? '' }}" onchange="set_filter_days()">
                                </div>
                                <div class="col-1 mb-2">
                                    <label class="form-label">To Day</label>
                                    <input class="form-control" type="number" min="0" autocomplete="off" name="to_day" id="to_day" value="{{ $ctrl_to_day ?? '' }}" onchange="set_filter_days()">
                                </div>
                            @endif
                            <div class="col-1-5 mb-2">
                                <label class="form-label">Sales Person</label>
                                <select class="form-control js-example-basic-single" name="sales_person" id="sales_person">
                                    <option value="">All</option>
                                    @foreach ($sales_person_list as $value)
                                        <option value="{{ $value->user_id }}" {{ $ctrl_sales_person == $value->user_id ? 'selected' : '' }}>{{ $value->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if (session('logged_session_data.company_id') == 1)
                                <div class="col-1-5 mb-2">
                                    <label class="form-label">Company</label>
                                    <select class="form-control js-example-basic-single" name="company" id="company">
                                        <option value=""></option>
                                        @foreach ($company_list as $value)
                                            <option value="{{ $value->id }}" {{ $ctrl_company == $value->id ? 'selected' : '' }}>{{ $value->company_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <div class="col-1-5 mb-2">
                                <label class="form-label">Filter By</label>
                                <select class="form-control" name="filter_by" id="filter_by">
                                    <option value="" @if ($filter_by == '') selected @endif>-Select-</option>
                                    <option value="this_month" @if ($filter_by == 'this_month') selected @endif>This Month</option>
                                    <option value="today" @if ($filter_by == 'today') selected @endif>Today</option>
                                    <option value="this_week" @if ($filter_by == 'this_week') selected @endif>This Week</option>
                                    <option value="last_week" @if ($filter_by == 'last_week') selected @endif>Last Week</option>
                                    <option value="last_month" @if ($filter_by == 'last_month') selected @endif>Last Month</option>
                                    <option value="this_quarter" @if ($filter_by == 'this_quarter') selected @endif>This Quarter</option>
                                    <option value="pre_quarter" @if ($filter_by == 'pre_quarter') selected @endif>Previous Quarter</option>
                                    <option value="this_year" @if ($filter_by == 'this_year') selected @endif>This Year</option>
                                    <option value="last_year" @if ($filter_by == 'last_year') selected @endif>Last Year</option>
                                </select>
                            </div>
                            <div class="col-1-5 mb-2">
                                <label class="form-label">Show All</label>
                                <select class="form-control" name="show_all" id="show_all">
                                    <option value="0" @if (($ctrl_show_all ?? 0) != 1) selected @endif>No</option>
                                    <option value="1" @if (($ctrl_show_all ?? 0) == 1) selected @endif>Yes</option>
                                </select>
                            </div>
                            <div class="col-1"><br />
                                <button type="submit" class="btn btn-light"><i class="ico icon-outline-magnifer"></i> Search</button>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-body">
                        @php
                            $activeReportGroup = $report_group ?? 'date_wise';
                            $reportGroupLabels = ['company_wise' => 'Company Wise', 'date_wise' => 'Date Wise', 'customer_wise' => 'Customer Wise', 'sales_person_wise' => 'Sales Person Wise'];
                            $activeReportLabel = $reportGroupLabels[$activeReportGroup] ?? 'Date Wise';
                            $selectedCompanyName = '';
                            if (!empty($scope_company_id) && !empty($company_list)) {
                                $selectedCompanyName = optional(collect($company_list)->firstWhere('id', $scope_company_id))->company_name ?? '';
                            }
                            if (empty($selectedCompanyName) && !empty($ctrl_company) && !empty($company_list)) {
                                $selectedCompanyName = optional(collect($company_list)->firstWhere('id', $ctrl_company))->company_name ?? '';
                            }
                            $showCompanyContextHeading = in_array($activeReportGroup, ['date_wise', 'customer_wise', 'sales_person_wise']) && !empty($selectedCompanyName);
                            $hideCompanyColumn = $activeReportGroup === 'date_wise' && $showCompanyContextHeading;
                        @endphp

                        <form id="receivableOutstandingRedirectForm" method="POST" action="{{ route('receivable-outstanding') }}" target="_blank" style="display:none;">
                            @csrf
                            <input type="hidden" name="account_id[]" id="receivableOutstandingCustomerId" value="">
                            <input type="hidden" name="till_date" id="receivableOutstandingTillDate" value="">
                        </form>
                        <form id="generalLedgerRedirectForm" method="POST" action="{{ url('generalledger') }}" target="_blank" style="display:none;">
                            @csrf
                            <input type="hidden" name="account_id[]" id="generalLedgerCustomerId" value="">
                            <input type="hidden" name="from_date" id="generalLedgerFromDate" value="">
                            <input type="hidden" name="to_date" id="generalLedgerToDate" value="">
                            <input type="hidden" name="filter_by" value="">
                        </form>

                        @if ($showCompanyContextHeading)
                            <div class="mb-2 fw-bold">{{ $activeReportLabel }} Report - Company: {{ $selectedCompanyName }}</div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-hover" id="long-list" style="table-layout: fixed;width:100%">
                                @if (($report_group ?? 'date_wise') === 'date_wise')
                                    <thead>
                                        <tr>
                                            @if (!$hideCompanyColumn)
                                                <th style="width: 80px;">@lang('Company')</th>
                                            @endif
                                            <th style="width: 70px;" class="text-center">@lang('Deal')</th>
                                            <th class="text-center" style="width: 70px;">@lang('SR No')</th>
                                            <th class="text-center" style="width: 80px;">@lang('SR Date')</th>
                                            <th style="width: 130px;">@lang('Customer')</th>
                                            <th style="width: 80px;" class="text-center">@lang('Sales Invoice No')</th>
                                            <th style="width: 80px;" class="text-end">@lang('Taxable')</th>
                                            <th style="width: 80px;" class="text-end">@lang('Tax')</th>
                                            <th style="width: 80px;" class="text-end">@lang('Amount')</th>
                                            <th style="width: 110px;">@lang('Sales Person')</th>
                                            <th style="width:60px" class="text-center">@lang('LPO')</th>
                                            <th style="width:80px" class="text-center">@lang('LPO Date')</th>
                                            <th style="width:60px" class="text-center">@lang('Currency')</th>
                                            <th style="width: 50px;" class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php($total_taxable_amount = 0)
                                        @php($total_tax = 0)
                                        @php($total_amount = 0)
                                        @foreach ($salesreturn as $value)
                                            @php
                                                $total_taxable_amount += $value->total_taxableamount;
                                                $total_tax += $value->total_vatamount;
                                                $total_amount += $value->amount;
                                            @endphp
                                            <tr>
                                                @if (!$hideCompanyColumn)
                                                    <td>{{ optional(\App\SysCompany::find($value->company_id))->company_name }}</td>
                                                @endif
                                                <td class="text-center">{{ $value->code ?: '--' }}</td>
                                                <td class="text-center"><a href="{{ url('sales-return/' . $value->id) }}" target="_blank">{{ $value->doc_number }}</a></td>
                                                <td class="text-center">{{ !empty($value->doc_date) ? date('d/m/Y', strtotime($value->doc_date)) : '' }}</td>
                                                <td>{{ optional($value->accountname)->account_name }}</td>
                                                <td class="text-center">{{ $value->si_doc_number }}</td>
                                                <td class="text-end">{{ @App\SysHelper::com_curr_format($value->total_taxableamount, 2, '.', ',') }}</td>
                                                <td class="text-end">{{ @App\SysHelper::com_curr_format($value->total_vatamount, 2, '.', ',') }}</td>
                                                <td class="text-end">{{ @App\SysHelper::com_curr_format($value->amount, 2, '.', ',') }}</td>
                                                <td>{{ optional($value->salesman_name)->full_name }}</td>
                                                <td class="text-center">{{ $value->lpo_number }}</td>
                                                <td class="text-center">{{ !empty($value->lpo_date) ? date('d/m/Y', strtotime($value->lpo_date)) : '' }}</td>
                                                <td class="text-center">{{ optional($value->currency_name)->code }}</td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-end gap-2">
                                                        @if (!empty($value->attachment))
                                                            <a href="{{ url('public/uploads/sales_return_doc/' . $value->attachment) }}" target="_blank"><i class="ico icon-bold-paperclip"></i></a>
                                                        @endif
                                                        <a href="{{ url('sales-return/' . $value->id . '/download') }}" target="_blank"><i class="ico icon-bold-download-minimalistic text-dark" style="font-size: 16px;"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                        @if ($salesreturn->count() == 0)
                                            <tr><td colspan="{{ $hideCompanyColumn ? 13 : 14 }}" class="text-center">No data found</td></tr>
                                        @endif
                                    </tbody>
                                    <footer>
                                        <tr>
                                            <th colspan="{{ $hideCompanyColumn ? 6 : 7 }}"></th>
                                            <th class="text-end">{{ @App\SysHelper::com_curr_format($total_taxable_amount, 2, '.', ',') }}</th>
                                            <th class="text-end">{{ @App\SysHelper::com_curr_format($total_tax, 2, '.', ',') }}</th>
                                            <th class="text-end">{{ @App\SysHelper::com_curr_format($total_amount, 2, '.', ',') }}</th>
                                            <th colspan="5"></th>
                                        </tr>
                                    </footer>
                                @else
                                    <thead>
                                        <tr>
                                            <th style='width:260px'>{{ in_array($report_group, ['company_wise']) ? 'Company Name' : (in_array($report_group, ['customer_wise']) ? 'Customer Name' : 'Sales Person Name') }}</th>
                                            <th class="text-center">No. of Returns</th>
                                            <th class="text-end">Taxable Amount</th>
                                            <th class="text-end">Tax</th>
                                            <th class="text-end">Amount</th>
                                            @if ($report_group === 'customer_wise')
                                                <th class="text-end" style="width:160px">Outstanding</th>
                                                <th class="text-center" style="width:120px">Last Return Date</th>
                                                <th class="text-start" style="width:110px">Sales Person</th>
                                                <th class="text-center" style="width:40px">GL</th>
                                            @endif
                                            @if ($report_group === 'company_wise')
                                                <th class="text-center" style="width:320px">Reports</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php($sum_return_count = 0)
                                        @php($sum_taxable = 0)
                                        @php($sum_tax = 0)
                                        @php($sum_amount = 0)
                                        @php($sum_customer_balance = 0)
                                        @foreach ($report_rows as $row)
                                            @php
                                                $sum_return_count += $row->return_count;
                                                $sum_taxable += $row->taxable;
                                                $sum_tax += $row->tax;
                                                $sum_amount += $row->amount;
                                                if ($report_group === 'customer_wise') {
                                                    $sum_customer_balance += ($row->customer_balance ?? 0);
                                                }
                                                $drillFilters = ['report_group' => 'date_wise'];
                                                if ($report_group === 'company_wise') {
                                                    $drillFilters['company'] = $row->company_id;
                                                } elseif ($report_group === 'customer_wise') {
                                                    $drillFilters['customer'] = $row->customer;
                                                    if (!empty($scope_company_id)) {
                                                        $drillFilters['company'] = $scope_company_id;
                                                    } elseif (isset($row->company_id) && !empty($row->company_id)) {
                                                        $drillFilters['company'] = $row->company_id;
                                                    }
                                                } else {
                                                    $drillFilters['sales_person'] = $row->sales_man;
                                                    if (!empty($scope_company_id)) {
                                                        $drillFilters['company'] = $scope_company_id;
                                                    } elseif (isset($row->company_id) && !empty($row->company_id)) {
                                                        $drillFilters['company'] = $row->company_id;
                                                    }
                                                }
                                            @endphp
                                            <tr>
                                                <td>
                                                    @if ($report_group === 'company_wise' && !empty($row->company_id))
                                                        <a href="{{ url('company/' . $row->company_id . '/edit') }}" target="_blank">{{ $row->group_name }}</a>
                                                    @elseif ($report_group === 'customer_wise' && !empty($row->customer))
                                                        <a href="{{ url('get-url-customer-from-chart-of-accounts/' . $row->customer) }}" target="_blank">{{ $row->group_name }}</a>
                                                    @elseif ($report_group === 'sales_person_wise' && !empty($row->staff_id))
                                                        <a href="{{ url('view-staff/' . $row->staff_id) }}" target="_blank">{{ $row->group_name }}</a>
                                                    @else
                                                        {{ $row->group_name }}
                                                    @endif
                                                </td>
                                                <td class="text-center"><a href="{{ route('sales.return.report.detail', $drillFilters) }}">{{ $row->return_count }}</a></td>
                                                <td class="text-end">{{ @App\SysHelper::com_curr_format($row->taxable, 2, '.', ',') }}</td>
                                                <td class="text-end">{{ @App\SysHelper::com_curr_format($row->tax, 2, '.', ',') }}</td>
                                                <td class="text-end">{{ @App\SysHelper::com_curr_format($row->amount, 2, '.', ',') }}</td>
                                                @if ($report_group === 'customer_wise')
                                                    <td class="text-end">
                                                        <a href="javascript:void(0)" class="open-receivable-outstanding" data-customer-id="{{ $row->customer }}" data-till-date="{{ !empty($ctrl_date2) ? \Carbon\Carbon::parse($ctrl_date2)->format('d/m/Y') : date('d/m/Y') }}">
                                                            {{ @App\SysHelper::com_curr_format($row->customer_balance ?? 0, 2, '.', ',') }}
                                                        </a>
                                                    </td>
                                                    <td class="text-center">
                                                        @if (!empty($row->last_return_date))
                                                            {{ date('d/m/Y', strtotime($row->last_return_date)) }} ({{ \Carbon\Carbon::parse($row->last_return_date)->diffInDays(\Carbon\Carbon::today()) }}d)
                                                        @endif
                                                    </td>
                                                    <td>{{ $row->sales_person_names ?? '' }}</td>
                                                    <td class="text-center">
                                                        <a href="javascript:void(0)" class="open-general-ledger" data-customer-id="{{ $row->customer }}" data-from-date="{{ !empty($ctrl_date) ? \Carbon\Carbon::parse($ctrl_date)->format('d/m/Y') : date('01/01/Y') }}" data-to-date="{{ !empty($ctrl_date2) ? \Carbon\Carbon::parse($ctrl_date2)->format('d/m/Y') : date('d/m/Y') }}" title="Open General Ledger">
                                                            <i class="ico icon-outline-eye text-success"></i>
                                                        </a>
                                                    </td>
                                                @endif
                                                @if ($report_group === 'company_wise')
                                                    <td class="text-center">
                                                        <div class="d-inline-flex gap-1 flex-nowrap">
                                                            <a class="btn btn-sm btn-light py-0 px-2 text-nowrap" href="{{ route('sales.return.report.detail', ['report_group' => 'date_wise', 'scope_company_id' => $row->company_id]) }}">Date Wise</a>
                                                            <a class="btn btn-sm btn-light py-0 px-2 text-nowrap" href="{{ route('sales.return.report.detail', ['report_group' => 'customer_wise', 'scope_company_id' => $row->company_id]) }}">Customer Wise</a>
                                                            <a class="btn btn-sm btn-light py-0 px-2 text-nowrap" href="{{ route('sales.return.report.detail', ['report_group' => 'sales_person_wise', 'scope_company_id' => $row->company_id]) }}">Sales Person Wise</a>
                                                        </div>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                        @if ($report_rows->count() == 0)
                                            <tr><td colspan="{{ $report_group === 'company_wise' ? 6 : ($report_group === 'customer_wise' ? 9 : 5) }}" class="text-center">No data found</td></tr>
                                        @endif
                                    </tbody>
                                    <footer>
                                        <tr>
                                            <th>Total</th>
                                            <th class="text-center">{{ $sum_return_count }}</th>
                                            <th class="text-end">{{ @App\SysHelper::com_curr_format($sum_taxable, 2, '.', ',') }}</th>
                                            <th class="text-end">{{ @App\SysHelper::com_curr_format($sum_tax, 2, '.', ',') }}</th>
                                            <th class="text-end">{{ @App\SysHelper::com_curr_format($sum_amount, 2, '.', ',') }}</th>
                                            @if ($report_group === 'customer_wise')
                                                <th class="text-end">{{ @App\SysHelper::com_curr_format($sum_customer_balance, 2, '.', ',') }}</th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                            @endif
                                            @if ($report_group === 'company_wise')
                                                <th></th>
                                            @endif
                                        </tr>
                                    </footer>
                                @endif
                            </table>
                        </div>
                        @if (($report_group ?? 'date_wise') === 'date_wise' && ($ctrl_show_all ?? 0) != 1 && $salesreturn instanceof \Illuminate\Pagination\LengthAwarePaginator)
                            <div class="d-flex justify-content-start mt-3">
                                {{ $salesreturn->appends(request()->input())->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
function set_filter() {
    if ($('#from_date').val() !== "" || $('#to_date').val() !== "") {
        $('#filter_by').val('');
    }
}
function set_filter_days() {
    if ($('#from_day').val() !== "" || $('#to_day').val() !== "") {
        $('#filter_by').val('');
    }
}
$(document).ready(function () {
    $(document).on('click', '.open-receivable-outstanding', function (e) {
        e.preventDefault();
        $('#receivableOutstandingCustomerId').val($(this).data('customer-id'));
        $('#receivableOutstandingTillDate').val($(this).data('till-date') || '');
        $('#receivableOutstandingRedirectForm').trigger('submit');
    });
    $(document).on('click', '.open-general-ledger', function (e) {
        e.preventDefault();
        $('#generalLedgerCustomerId').val($(this).data('customer-id'));
        $('#generalLedgerFromDate').val($(this).data('from-date') || '');
        $('#generalLedgerToDate').val($(this).data('to-date') || '');
        $('#generalLedgerRedirectForm').trigger('submit');
    });

    $('#exportSalesReturnReport').on('click', function (e) {
        e.preventDefault();
        var reportGroup = @json($report_group ?? 'date_wise');
        var headers = [];
        var rows = [];
        $('#long-list thead tr').last().find('th').each(function () {
            var label = $(this).text().trim();
            if (label && !/action/i.test(label)) headers.push(label);
        });
        if (!headers.length) return;

        rows.push(['Sales Return Report']);
        rows.push(['Report Type: ' + (reportGroup.replaceAll('_', ' '))]);
        rows.push([]);
        rows.push(headers);
        $('#long-list tbody tr').each(function () {
            var row = [];
            $(this).find('td').each(function (index) {
                if (index < headers.length) row.push($(this).text().trim().replace(/\s+/g, ' '));
            });
            if (row.length) rows.push(row);
        });

        var workbook = new ExcelJS.Workbook();
        var worksheet = workbook.addWorksheet('Sales Return Report');
        rows.forEach(function (r) { worksheet.addRow(r); });
        workbook.xlsx.writeBuffer().then(function (buffer) {
            saveAs(new Blob([buffer], {type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'}), 'sales_return_report.xlsx');
        });
    });
});
</script>
@endsection

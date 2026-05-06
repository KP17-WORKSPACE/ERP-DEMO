@extends('backEnd.newmasterpage')
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/exceljs@4.3.0/dist/exceljs.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
@endpush
@section('mainContent')

<?php
$module_links = [];
$permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
$currentRouteName = \Illuminate\Support\Facades\Route::currentRouteName();
$currentReportGroup = $report_group ?? 'date_wise';
?>
<?php try { ?>

<div class="content-container col-12">
    <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
        <div role="tabpanel" id="data-details">
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
                                    <li><a class="dropdown-item {{ $currentRouteName === 'sales.invoice.report.detail' && $currentReportGroup === 'company_wise' ? 'text-success fw-bold' : 'text-dark' }}" href="{{ route('sales.invoice.report.detail', ['report_group' => 'company_wise']) }}">Company Wise</a></li>
                                    <li><a class="dropdown-item {{ $currentRouteName === 'sales.invoice.report.detail' && $currentReportGroup === 'date_wise' ? 'text-success fw-bold' : 'text-dark' }}" href="{{ route('sales.invoice.report.detail', ['report_group' => 'date_wise']) }}">Date Wise</a></li>
                                    <li><a class="dropdown-item {{ $currentRouteName === 'sales.invoice.report.detail' && $currentReportGroup === 'customer_wise' ? 'text-success fw-bold' : 'text-dark' }}" href="{{ route('sales.invoice.report.detail', ['report_group' => 'customer_wise']) }}">Customer Wise</a></li>
                                    <li><a class="dropdown-item {{ $currentRouteName === 'sales.invoice.report.detail' && $currentReportGroup === 'sales_person_wise' ? 'text-success fw-bold' : 'text-dark' }}" href="{{ route('sales.invoice.report.detail', ['report_group' => 'sales_person_wise']) }}">Sales Person Wise</a></li>
                                </ul>
                            </li>
                            <li class="dropend">
                                <a class="dropdown-item dropdown-toggle report-submenu-trigger text-dark" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false">Sales Return Report</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item {{ $currentRouteName === 'sales.return.report.detail' && $currentReportGroup === 'company_wise' ? 'text-success fw-bold' : 'text-dark' }}" href="{{ route('sales.return.report.detail', ['report_group' => 'company_wise']) }}">Company Wise</a></li>
                                    <li><a class="dropdown-item {{ $currentRouteName === 'sales.return.report.detail' && $currentReportGroup === 'date_wise' ? 'text-success fw-bold' : 'text-dark' }}" href="{{ route('sales.return.report.detail', ['report_group' => 'date_wise']) }}">Date Wise</a></li>
                                    <li><a class="dropdown-item {{ $currentRouteName === 'sales.return.report.detail' && $currentReportGroup === 'customer_wise' ? 'text-success fw-bold' : 'text-dark' }}" href="{{ route('sales.return.report.detail', ['report_group' => 'customer_wise']) }}">Customer Wise</a></li>
                                    <li><a class="dropdown-item {{ $currentRouteName === 'sales.return.report.detail' && $currentReportGroup === 'sales_person_wise' ? 'text-success fw-bold' : 'text-dark' }}" href="{{ route('sales.return.report.detail', ['report_group' => 'sales_person_wise']) }}">Sales Person Wise</a></li>
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
                                    <option value="{{ $value->id }}" @if ($ctrl_customer == $value->id) selected @endif>{{ $value->account_name }}</option>
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
                                    <option value="{{ $value->user_id }}" @if ($ctrl_sales_person == $value->user_id) selected @endif>{{ $value->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if (session('logged_session_data.company_id') == 1)
                            <div class="col-1-5 mb-2">
                                <label class="form-label">Company</label>
                                <select class="form-control js-example-basic-single" name="company" id="company">
                                    <option value=""></option>
                                    @foreach ($company_list as $value)
                                        <option value="{{ $value->id }}" @if ($ctrl_company == $value->id) selected @endif>{{ $value->company_name }}</option>
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
                        <script>
                            function set_filter(){ if($('#from_date').val()!="" || $('#to_date').val()!=""){ $('#filter_by').val(''); } }
                            function set_filter_days(){ if($('#from_day').val()!="" || $('#to_day').val()!=""){ $('#filter_by').val(''); } }
                        </script>
                        <div class="col-1"><br />
                            <button type="submit" class="btn btn-light" id="btnSubmit"><i class="ico icon-outline-magnifer"></i> Search</button>
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

                    @if ($showCompanyContextHeading)
                        <div class="mb-2 fw-bold">{{ $activeReportLabel }} Report - Company: {{ $selectedCompanyName }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover" id="long-list" style="table-layout: fixed;width:100%">
                            @if (($report_group ?? 'date_wise') === 'date_wise')
                                <thead>
                                    <tr>
                                        @if (!$hideCompanyColumn)<th style="width:80px;">Company</th>@endif
                                        <th style="width:70px;" class="text-center">Deal</th>
                                        <th class="text-center" style="width:70px;">SR No</th>
                                        <th class="text-center" style="width:80px;">SR Date</th>
                                        <th style="width:130px;">Customer</th>
                                        <th style="width:80px;" class="text-end">Taxable</th>
                                        <th style="width:80px;" class="text-end">Tax</th>
                                        <th style="width:80px;" class="text-end">Amount</th>
                                        <th style="width:80px;" class="text-end">GP</th>
                                        <th style="width:60px;" class="text-end">GP%</th>
                                        <th style="width:110px;">Sales Person</th>
                                        <th style="width:60px" class="text-center">LPO</th>
                                        <th style="width:80px" class="text-center">LPO Date</th>
                                        <th style="width:60px" class="text-center">Currency</th>
                                        <th style="width:90px" class="text-center">Sales Invoice No</th>
                                        <th style="width:50px;" class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $total_taxable_amount=0; $total_tax=0; $total_amount=0; $total_gp = 0; @endphp
                                    @foreach ($salesreturn as $value)
                                        @php
                                            $taxable = (float)($value->total_taxableamount ?: 0);
                                            $tax = (float)($value->total_vatamount ?: 0);
                                            $amount = (float)($value->amount ?: 0);
                                            $dealValue = (float) (@App\SysHelper::get_aed_amount_new($value->deal_currency ?? '', $value->deal_value ?? 0) ?: 0);
                                            $dealProfit = (float) (@App\SysHelper::get_aed_amount_new($value->deal_currency ?? '', $value->deal_profit ?? 0) ?: 0);
                                            $dealPercentage = $dealValue != 0 ? round(($dealProfit / $dealValue) * 100, 2) : 0;
                                            $gp = ($taxable * $dealPercentage) / 100;
                                            $total_taxable_amount += $taxable;
                                            $total_tax += $tax;
                                            $total_amount += $amount;
                                            $total_gp += $gp;
                                        @endphp
                                        <tr>
                                            @if (!$hideCompanyColumn)<td>{{ optional(\App\SysCompany::find($value->company_id))->company_name }}</td>@endif
                                            <td class="text-center">@if (!empty($value->code)) <a href="{{ url('get-url-deal-track/' . $value->code) }}" target="_blank">{{ $value->code }}</a> @else -- @endif</td>
                                            <td class="text-center"><a href="{{ url('sales-return/' . $value->id) }}" target="_blank">{{ $value->doc_number }}</a></td>
                                            <td class="text-center">{{ !empty($value->doc_date) ? date('d/m/Y', strtotime($value->doc_date)) : '' }}</td>
                                            <td>{{ optional($value->accountname)->account_name }}</td>
                                            <td class="text-end">{{ @App\SysHelper::com_curr_format($taxable,2,'.',',') }}</td>
                                            <td class="text-end">{{ @App\SysHelper::com_curr_format($tax,2,'.',',') }}</td>
                                            <td class="text-end">{{ @App\SysHelper::com_curr_format($amount,2,'.',',') }}</td>
                                            <td class="text-end">{{ @App\SysHelper::com_curr_format($gp,2,'.',',') }}</td>
                                            <td class="text-end">{{ $dealPercentage }}%</td>
                                            <td>{{ optional($value->salesman_name)->full_name }}</td>
                                            <td class="text-center">{{ $value->lpo_number }}</td>
                                            <td class="text-center">{{ !empty($value->lpo_date) ? date('d/m/Y', strtotime($value->lpo_date)) : '' }}</td>
                                            <td class="text-center">{{ optional($value->currency_name)->code }}</td>
                                            <td class="text-center">{{ $value->si_doc_number }}</td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-end gap-2">
                                                    @if (!empty($value->attachment))
                                                        <a href="{{ url('public/uploads/sales_return_doc/' . $value->attachment) }}" target="_blank"><i class="ico icon-bold-paperclip"></i></a>
                                                    @endif
                                                    <a href="{{ url('sales-return/' . $value->id . '/download') }}" target="_blank"><i class="ico icon-bold-download-minimalistic text-dark" style="font-size:16px;"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @if ($salesreturn->count() == 0)
                                        <tr><td colspan="{{ $hideCompanyColumn ? 15 : 16 }}" class="text-center">No data found</td></tr>
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="{{ $hideCompanyColumn ? 4 : 5 }}" class="text-end">Total</th>
                                        <th class="text-end">{{ @App\SysHelper::com_curr_format($total_taxable_amount,2,'.',',') }}</th>
                                        <th class="text-end">{{ @App\SysHelper::com_curr_format($total_tax,2,'.',',') }}</th>
                                        <th class="text-end">{{ @App\SysHelper::com_curr_format($total_amount,2,'.',',') }}</th>
                                        <th class="text-end">{{ @App\SysHelper::com_curr_format($total_gp,2,'.',',') }}</th>
                                        <th class="text-end">{{ $total_taxable_amount != 0 ? round(($total_gp / $total_taxable_amount) * 100, 2) : 0 }}%</th>
                                        <th colspan="6"></th>
                                    </tr>
                                </tfoot>
                            @else
                                <thead>
                                    <tr>
                                        <th style="width:260px">{{ in_array($report_group, ['company_wise']) ? 'Company Name' : (in_array($report_group, ['customer_wise']) ? 'Customer Name' : 'Sales Person Name') }}</th>
                                        <th class="text-center">No. of Returns</th>
                                        <th class="text-end">Taxable Amount</th>
                                        <th class="text-end">Tax</th>
                                        <th class="text-end">Amount</th>
                                        <th class="text-end">GP</th>
                                        <th class="text-end">GP%</th>
                                        @if ($report_group === 'company_wise')
                                            <th class="text-center" style="width:320px">Reports</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $sum_return_count=0; $sum_taxable=0; $sum_tax=0; $sum_amount=0; $sum_gp=0; @endphp
                                    @foreach ($report_rows as $row)
                                        @php
                                            $sum_return_count += $row->return_count;
                                            $sum_taxable += $row->taxable;
                                            $sum_tax += $row->tax;
                                            $sum_amount += $row->amount;
                                            $rowGp = (float)($row->gp ?? 0);
                                            $rowGpPercent = isset($row->gp_percent) ? (float)$row->gp_percent : (($row->taxable ?? 0) != 0 ? (($rowGp / $row->taxable) * 100) : 0);
                                            $sum_gp += $rowGp;
                                            $drillFilters = ['report_group' => 'date_wise'];
                                            if ($report_group === 'company_wise') { $drillFilters['company'] = $row->company_id; }
                                            elseif ($report_group === 'customer_wise') { $drillFilters['customer'] = $row->customer; if (!empty($scope_company_id)) { $drillFilters['company'] = $scope_company_id; } }
                                            else { $drillFilters['sales_person'] = $row->sales_man; if (!empty($scope_company_id)) { $drillFilters['company'] = $scope_company_id; } }
                                        @endphp
                                        <tr>
                                            <td>
                                                @if ($report_group === 'company_wise' && !empty($row->company_id))
                                                    <a href="{{ url('company?active=' . $row->company_id) }}" target="_blank">{{ $row->group_name }}</a>
                                                @else
                                                    {{ $row->group_name }}
                                                @endif
                                            </td>
                                            <td class="text-center"><a href="{{ route('sales.return.report.detail', $drillFilters) }}">{{ $row->return_count }}</a></td>
                                            <td class="text-end">{{ @App\SysHelper::com_curr_format($row->taxable,2,'.',',') }}</td>
                                            <td class="text-end">{{ @App\SysHelper::com_curr_format($row->tax,2,'.',',') }}</td>
                                            <td class="text-end">{{ @App\SysHelper::com_curr_format($row->amount,2,'.',',') }}</td>
                                            <td class="text-end">{{ @App\SysHelper::com_curr_format($rowGp,2,'.',',') }}</td>
                                            <td class="text-end">{{ round($rowGpPercent, 2) }}%</td>
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
                                        <tr><td colspan="{{ $report_group === 'company_wise' ? 8 : 7 }}" class="text-center">No data found</td></tr>
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Total</th>
                                        <th class="text-center">{{ $sum_return_count }}</th>
                                        <th class="text-end">{{ @App\SysHelper::com_curr_format($sum_taxable,2,'.',',') }}</th>
                                        <th class="text-end">{{ @App\SysHelper::com_curr_format($sum_tax,2,'.',',') }}</th>
                                        <th class="text-end">{{ @App\SysHelper::com_curr_format($sum_amount,2,'.',',') }}</th>
                                        <th class="text-end">{{ @App\SysHelper::com_curr_format($sum_gp,2,'.',',') }}</th>
                                        <th class="text-end">{{ $sum_taxable != 0 ? round(($sum_gp / $sum_taxable) * 100, 2) : 0 }}%</th>
                                        @if ($report_group === 'company_wise')
                                            <th></th>
                                        @endif
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>

                    <style>
                        .report-type-trigger{color:#212529;text-decoration:none;font-weight:500;display:inline-flex;align-items:center;}
                        .report-type-trigger:hover{color:#499258;}
                        .dropdown-menu .dropend .dropdown-menu{top:0;left:100%;margin-top:-1px;}
                        .report-type-dropdown .dropdown-item.active,
                        .report-type-dropdown .dropdown-item.text-success{color:#499258 !important;background-color:transparent !important;font-weight:600;}
                        .pagination .page-link{color:#499258;}
                        .pagination .page-item.active .page-link{background-color:#499258;color:#fff;}
                    </style>

                    @if (($report_group ?? 'date_wise') === 'date_wise' && ($ctrl_show_all ?? 0) != 1 && $salesreturn instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        <div class="d-flex justify-content-start mt-3">{{ $salesreturn->appends(request()->input())->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

<script>
$(document).ready(function () {
    $('.report-submenu-trigger').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var $submenu = $(this).next('.dropdown-menu');
        var isShown = $submenu.hasClass('show');
        $('.report-type-dropdown .dropend .dropdown-menu').removeClass('show');
        $('.report-submenu-trigger').attr('aria-expanded', 'false');
        if (!isShown) {
            $submenu.addClass('show');
            $(this).attr('aria-expanded', 'true');
        }
    });
    $(document).on('click', function () {
        $('.report-type-dropdown .dropend .dropdown-menu').removeClass('show');
        $('.report-submenu-trigger').attr('aria-expanded', 'false');
    });
    $('.report-type-dropdown .dropdown-menu').on('click', function (e) {
        e.stopPropagation();
    });

    $('#exportSalesReturnReport').on('click', function (e) {
        e.preventDefault();

        var companyName = @json(@App\SysCompany::find(session('logged_session_data.company_id') ?? '')->trade_name ?? '');
        var fromDate = @json($ctrl_date ?? '');
        var toDate = @json($ctrl_date2 ?? '');
        var reportGroup = @json($report_group ?? 'date_wise');
        var reportGroupLabelMap = {
            company_wise: 'Company Wise',
            date_wise: 'Date Wise',
            customer_wise: 'Customer Wise',
            sales_person_wise: 'Sales Person Wise'
        };
        var reportGroupLabel = reportGroupLabelMap[reportGroup] || 'Date Wise';

        function selectedText(selector) {
            var text = $(selector + ' option:selected').text() || '';
            return text.trim();
        }

        var selectedCompany = selectedText('#company');
        var selectedCustomer = selectedText('#customer');
        var selectedSalesPerson = selectedText('#sales_person');
        if (!selectedCompany) {
            selectedCompany = @json($selectedCompanyName ?? '');
        }

        function formatDMY(value) {
            if (!value) return '';
            var normalized = String(value).trim().replace(/\s+/g, '');
            var parts = normalized.split(/[\/\-\.]/);
            if (parts.length === 3) {
                if (parts[0].length === 4) {
                    return parts[2] + '/' + parts[1] + '/' + parts[0];
                }
                return parts[0] + '/' + parts[1] + '/' + parts[2];
            }
            return value;
        }

        function buildRowsFromTable($table) {
            var headers = [];
            var visibleIndexes = [];
            $table.find('thead tr').last().find('th').each(function (index) {
                var label = $(this).text().trim();
                if (!label) return;
                if (/action/i.test(label)) return;
                headers.push(label);
                visibleIndexes.push(index);
            });

            if (headers.length === 0) {
                return null;
            }

            var rows = [];
            rows.push([companyName]);
            rows.push(['Sales Return Report']);
            rows.push(['Report Type: ' + reportGroupLabel]);
            if (selectedCompany && selectedCompany.toLowerCase() !== 'all') {
                rows.push(['Company: ' + selectedCompany]);
            }
            if (selectedCustomer && selectedCustomer.toLowerCase() !== 'all') {
                rows.push(['Customer: ' + selectedCustomer]);
            }
            if (selectedSalesPerson && selectedSalesPerson.toLowerCase() !== 'all') {
                rows.push(['Sales Person: ' + selectedSalesPerson]);
            }
            if (fromDate || toDate) {
                var parts = [];
                if (fromDate) parts.push('From: ' + formatDMY(fromDate));
                if (toDate) parts.push('To: ' + formatDMY(toDate));
                rows.push([parts.join('   ')]);
            }
            rows.push([]);
            rows.push(headers);

            $table.find('tbody tr').each(function () {
                var row = [];
                var $cells = $(this).find('td');
                visibleIndexes.forEach(function (idx) {
                    var text = $cells.eq(idx).text().trim().replace(/\s+/g, ' ');
                    row.push(text);
                });
                if (row.length > 0) {
                    rows.push(row);
                }
            });

            var $footer = $table.find('tfoot tr').first();
            if ($footer.length) {
                var totalRow = [];
                $footer.children('th').each(function () {
                    var text = $(this).text().trim().replace(/\s+/g, ' ');
                    totalRow.push(text);
                });
                if (totalRow.length > 0) {
                    rows.push([]);
                    rows.push(totalRow);
                }
            }

            return { rows: rows, headers: headers };
        }

        function exportRows(rows, headers) {
            var workbook = new ExcelJS.Workbook();
            var worksheet = workbook.addWorksheet('Sales Return Report');

            worksheet.columns = headers.map(function () {
                return { width: 18 };
            });

            var rowIndex = 0;
            var headerRowIndex = rows.findIndex(function (r) { return Array.isArray(r) && r.length && r[0] === headers[0]; }) + 1;
            rows.forEach(function (rowData) {
                rowIndex++;
                var row = worksheet.addRow(rowData);
                if (rowData.length === 1 && headers.length > 1 && rowIndex !== 1) {
                    worksheet.mergeCells(rowIndex, 1, rowIndex, headers.length);
                }
                if (rowIndex === 1) {
                    worksheet.mergeCells(rowIndex, 1, rowIndex, headers.length);
                    row.getCell(1).font = { bold: true, size: 14 };
                    row.getCell(1).alignment = { horizontal: 'center', vertical: 'middle' };
                } else if (rowIndex === 2) {
                    row.getCell(1).font = { bold: true, size: 12 };
                    row.getCell(1).alignment = { horizontal: 'center', vertical: 'middle' };
                } else if (rowData.length === 1 && rowIndex < headerRowIndex) {
                    row.getCell(1).font = { size: 10, bold: rowIndex === 3 };
                    row.getCell(1).alignment = { horizontal: 'left', vertical: 'middle' };
                }

                if (rowIndex === headerRowIndex) {
                    row.eachCell({ includeEmpty: true }, function (cell) {
                        cell.font = { bold: true, color: { argb: 'FFFFFFFF' }, size: 11 };
                        cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF2D5496' } };
                        cell.alignment = { horizontal: 'center', vertical: 'middle' };
                        cell.border = {
                            top: { style: 'thin', color: { argb: 'FFB8C4D8' } },
                            left: { style: 'thin', color: { argb: 'FFB8C4D8' } },
                            bottom: { style: 'thin', color: { argb: 'FFB8C4D8' } },
                            right: { style: 'thin', color: { argb: 'FFB8C4D8' } }
                        };
                    });
                }
            });

            if (rows.length <= 5) {
                alert('No data available for export.');
                return;
            }

            workbook.xlsx.writeBuffer().then(function (buffer) {
                var blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                function pad(n) { return n < 10 ? '0' + n : '' + n; }
                var d = new Date();
                saveAs(blob, 'sales_return_report_' + pad(d.getDate()) + '-' + pad(d.getMonth() + 1) + '-' + d.getFullYear() + '.xlsx');
            });
        }

        var isDateWise = @json(($report_group ?? 'date_wise') === 'date_wise');
        var isShowAll = @json((int)($ctrl_show_all ?? 0) === 1);

        function exportFromCurrentTable() {
            var data = buildRowsFromTable($('#long-list'));
            if (!data) {
                alert('No table headers found for export.');
                return;
            }
            exportRows(data.rows, data.headers);
        }

        if (!isDateWise || isShowAll) {
            exportFromCurrentTable();
            return;
        }

        var params = new URLSearchParams(new FormData(document.getElementById('sales-return-report')));
        params.set('show_all', '1');
        params.set('report_group', 'date_wise');
        var exportUrl = "{{ url('sales-return-report-detail') }}" + '?' + params.toString();

        $.get(exportUrl, function (html) {
            var parsed = $('<div>').html(html);
            var fullTable = parsed.find('#long-list').first();
            if (!fullTable.length) {
                alert('Unable to prepare full data for export.');
                return;
            }
            var data = buildRowsFromTable(fullTable);
            if (!data) {
                alert('No table headers found for export.');
                return;
            }
            exportRows(data.rows, data.headers);
        }).fail(function () {
            alert('Unable to fetch full data for export.');
        });
    });
});
</script>
@endsection
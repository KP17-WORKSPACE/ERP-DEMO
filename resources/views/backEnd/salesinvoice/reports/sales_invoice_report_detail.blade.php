@extends('backEnd.newmasterpage')
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/exceljs@4.3.0/dist/exceljs.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
@endpush
@section('mainContent')

    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    //$permissions = App\SmRolePermission::where('role_id', 8)->get();
    ?>
    <script>
        $.fn.dataTableExt.sErrMode = 'none';
        $(document).ready(function() {
            if (!$.fn.DataTable.isDataTable('#dataTable')) {
                $('#dataTable').DataTable({
                    "paging": false,
                    "lengthChange": false,
                });
            }
        });
    </script>
    <?php try { ?>

    <div class="content-container col-12">
        <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
            <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
                <div class="purchase-order-content-header">
                    <div class="purchase-order-content-header-left">
                        <div class="dropdown report-type-dropdown">
                            @php
                                $menuReportGroup = $report_group ?? 'date_wise';
                                $isSalesInvoiceRoute = request()->routeIs('sales.invoice.report.detail');
                                $isSalesReturnRoute = request()->routeIs('sales.return.report.detail');
                            @endphp
                            <a class="text-dark report-type-trigger" href="javascript:void(0);" id="salesReportTypeMenu"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                Sales Report Type <i class="icon-outline-alt-arrow-down ms-1"></i>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="salesReportTypeMenu">
                                <li class="dropend">
                                    <a class="dropdown-item dropdown-toggle report-submenu-trigger text-dark" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false">Sales Report</a>
                                    <ul class="dropdown-menu">
                                        <li><a class="text-dark dropdown-item {{ $isSalesInvoiceRoute && $menuReportGroup === 'company_wise' ? 'active' : '' }}" href="{{ route('sales.invoice.report.detail', ['report_group' => 'company_wise']) }}">Company Wise</a></li>
                                        <li><a class="text-dark dropdown-item {{ $isSalesInvoiceRoute && $menuReportGroup === 'date_wise' ? 'active' : '' }}" href="{{ route('sales.invoice.report.detail', ['report_group' => 'date_wise']) }}">Date Wise</a></li>
                                        <li><a class="text-dark dropdown-item {{ $isSalesInvoiceRoute && $menuReportGroup === 'customer_wise' ? 'active' : '' }}" href="{{ route('sales.invoice.report.detail', ['report_group' => 'customer_wise']) }}">Customer Wise</a></li>
                                        <li><a class="text-dark dropdown-item {{ $isSalesInvoiceRoute && $menuReportGroup === 'sales_person_wise' ? 'active' : '' }}" href="{{ route('sales.invoice.report.detail', ['report_group' => 'sales_person_wise']) }}">Sales Person Wise</a></li>
                                    </ul>
                                </li>
                                <li class="dropend">
                                    <a class="dropdown-item dropdown-toggle report-submenu-trigger text-dark" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false">Sales Return Report</a>
                                    <ul class="dropdown-menu">
                                        <li><a class="text-dark dropdown-item {{ $isSalesReturnRoute && $menuReportGroup === 'company_wise' ? 'active' : '' }}" href="{{ route('sales.return.report.detail', ['report_group' => 'company_wise']) }}">Company Wise</a></li>
                                        <li><a class="text-dark dropdown-item {{ $isSalesReturnRoute && $menuReportGroup === 'date_wise' ? 'active' : '' }}" href="{{ route('sales.return.report.detail', ['report_group' => 'date_wise']) }}">Date Wise</a></li>
                                        <li><a class="text-dark dropdown-item {{ $isSalesReturnRoute && $menuReportGroup === 'customer_wise' ? 'active' : '' }}" href="{{ route('sales.return.report.detail', ['report_group' => 'customer_wise']) }}">Customer Wise</a></li>
                                        <li><a class="text-dark dropdown-item {{ $isSalesReturnRoute && $menuReportGroup === 'sales_person_wise' ? 'active' : '' }}" href="{{ route('sales.return.report.detail', ['report_group' => 'sales_person_wise']) }}">Sales Person Wise</a></li>
                                    </ul>   
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="purchase-order-content-header-right">
                        <button type="button" class="btn btn-light" id="exportSalesInvoiceReport" title="Export to Excel">
                            <i class="ico icon-outline-export text-success"></i> Export
                        </button>
                    
                    </div>
                </div>


                <div class="card mb-3">
                    <div class="card-body">
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'sales-invoice-report-detail', 'method' => 'get', 'id' => 'sales-invoice-report']) }}
                        <input type="hidden" name="report_group" value="{{ $report_group ?? 'date_wise' }}">
                        <input type="hidden" name="scope_company_id" value="{{ $scope_company_id ?? '' }}">
                        <div class="row">

                            <div class="col-1-5 mb-2">
                                <label for="" class="form-label">Documents Number</label>
                                <input class="form-control" type="text" autocomplete="off" name="documents_number"
                                    value="{{ $ctrl_doc_no }}">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="" class="form-label">Customer</label>
                                <select class="form-control js-example-basic-single" name="customer" id="customer">
                                    <option value="">All</option>
                                    @foreach ($customer_list as $value)
                                        <option value="{{ @$value->id }}"
                                            @if ($ctrl_customer == @$value->id) selected @endif>{{ @$value->account_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-1-5 mb-2">
                                <label for="" class="form-label">Deal ID</label>
                                <input class="form-control" type="text" autocomplete="off" name="deal_number"
                                    value="{{ $ctrl_deal_id }}">
                            </div>
                            <div class="col-1 mb-2">
                                <label for="" class="form-label">Amount</label>
                                <input class="form-control" type="number" autocomplete="off" name="amount"
                                    value="{{ $ctrl_amount }}">
                            </div>
                            <div class="col-1-5 mb-2">
                                <label for="" class="form-label">From Date</label>
                                <input class="form-control date-picker" type="text" autocomplete="off" name="from_date"
                                    id="from_date" value="{{  $ctrl_date ? \Carbon\Carbon::parse($ctrl_date)->format('d/m/Y') : '' }}" onchange="set_filter()">
                            </div>
                            <div class="col-1-5 mb-2">
                                <label for="" class="form-label">To Date</label>
                                <input class="form-control date-picker" type="text" autocomplete="off" name="to_date" id="to_date"
                                    value="{{ $ctrl_date2 ? \Carbon\Carbon::parse($ctrl_date2)->format('d/m/Y') : '' }}" onchange="set_filter()">
                            </div>
                            @if (($report_group ?? '') === 'customer_wise')
                                <div class="col-1 mb-2">
                                    <label for="" class="form-label">From Day</label>
                                    <input class="form-control" type="number" min="0" autocomplete="off" name="from_day"
                                        id="from_day" value="{{ $ctrl_from_day ?? '' }}" onchange="set_filter_days()">
                                </div>
                                <div class="col-1 mb-2">
                                    <label for="" class="form-label">To Day</label>
                                    <input class="form-control" type="number" min="0" autocomplete="off" name="to_day"
                                        id="to_day" value="{{ $ctrl_to_day ?? '' }}" onchange="set_filter_days()">
                                </div>
                            @endif
                            <div class="col-1-5 mb-2">
                                <label for="" class="form-label">Sales Person</label>
                                <select class="form-control js-example-basic-single" name="sales_person" id="sales_person">
                                    <option value="">All</option>
                                    @foreach ($sales_person_list as $value)
                                        <option value="{{ @$value->user_id }}"
                                            @if ($ctrl_sales_person == @$value->user_id) selected @endif>{{ @$value->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            @if (session('logged_session_data.company_id') == 1)
                                <div class="col-1-5 mb-2">
                                    <label for="" class="form-label">Company</label>
                                    <select class="form-control js-example-basic-single" name="company" id="company">
                                        <option value=""></option>
                                        @foreach ($company_list as $value)
                                            <option value="{{ @$value->id }}"
                                                @if ($ctrl_company == @$value->id) selected @endif>
                                                {{ @$value->company_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div class="col-1-5 mb-2">
                                <label for="" class="form-label">Filter By</label>
                                <select class="form-control" name="filter_by" id="filter_by">
                                    <option value="" @if ($filter_by == '') selected @endif>-Select-
                                    </option>
                                    <option value="this_month" @if ($filter_by == 'this_month') selected @endif>This Month
                                    </option>
                                    <option value="today" @if ($filter_by == 'today') selected @endif>Today</option>
                                    <option value="this_week" @if ($filter_by == 'this_week') selected @endif>This Week
                                    </option>
                                    <option value="last_week" @if ($filter_by == 'last_week') selected @endif>Last Week
                                    </option>
                                    <option value="last_month" @if ($filter_by == 'last_month') selected @endif>Last Month
                                    </option>
                                    <option value="this_quarter" @if ($filter_by == 'this_quarter') selected @endif>This
                                        Quarter</option>
                                    <option value="pre_quarter" @if ($filter_by == 'pre_quarter') selected @endif>Previous
                                        Quarter</option>
                                    <option value="this_year" @if ($filter_by == 'this_year') selected @endif>This Year
                                    </option>
                                    <option value="last_year" @if ($filter_by == 'last_year') selected @endif>Last Year
                                    </option>
                                </select>
                            </div>
                            <div class="col-1-5 mb-2">
                                <label for="" class="form-label">Show All</label>
                                <select class="form-control" name="show_all" id="show_all">
                                    <option value="0" @if (($ctrl_show_all ?? 0) != 1) selected @endif>No</option>
                                    <option value="1" @if (($ctrl_show_all ?? 0) == 1) selected @endif>Yes</option>
                                </select>
                            </div>
                            <script>
                                function set_filter() {
                                    if ($('#from_date').val() != "" || $('#to_date').val() != "") {
                                        $('#filter_by').val('')
                                    }
                                }
                                function set_filter_days() {
                                    if ($('#from_day').val() != "" || $('#to_day').val() != "") {
                                        $('#filter_by').val('');
                                    }
                                }
                            </script>

                            <div class="col-1"><br />
                                <button type="submit" class="btn btn-light" id="btnSubmit">
                                    <i class="ico icon-outline-magnifer"></i> Search
                                </button>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-body">

                        @php
                            $activeReportGroup = $report_group ?? 'date_wise';
                            $reportGroupLabels = [
                                'company_wise' => 'Company Wise',
                                'date_wise' => 'Date Wise',
                                'customer_wise' => 'Customer Wise',
                                'sales_person_wise' => 'Sales Person Wise',
                            ];
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
                            <div class="mb-2 fw-bold">
                                {{ $activeReportLabel }} Report - Company: {{ $selectedCompanyName }}
                            </div>
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
                                            <th class="text-center" style="width: 70px;">@lang('SI No')</th>
                                            <th class="text-center" style="width: 80px;">@lang('SI Date')</th>
                                            <th style="width: 130px;">@lang('Customer')</th>
                                            <th style="width: 80px;" class="text-end">@lang('Value')</th>
                                            <th style="width: 60px;" class="text-end">@lang('Discount')</th>
                                            <th style="width: 80px;" class="text-end">@lang('Taxable')</th>
                                            <th style="width: 80px;" class="text-end">@lang('Tax')</th>
                                            <th style="width: 80px;" class="text-end">@lang('Amount')</th>
                                            <th style="width: 80px;" class="text-end">@lang('GP')</th>
                                            <th class="text-end" style="width: 60px;">@lang('GP%')</th>
                                            <th style="width: 110px;">@lang('Sales Person')</th>
                                            <th style="width:60px" class="text-center">@lang('LPO')</th>
                                            <th style="width:80px" class="text-center">@lang('LPO Date')</th>
                                            <th style="width:60px" class="text-center">@lang('Currency')</th>
                                            <th style="width:60px" class="text-center">@lang('Payment')</th>
                                            <th style="width: 50px;" class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $total_taxable_amount = 0;
                                            $total_tax = 0;
                                            $total_amount = 0;
                                            $total_value = 0;
                                            $total_discount = 0;
                                            $total_gp = 0;
                                        @endphp
                                        @foreach ($salesinvoice as $value)
                                            @php
                                                $deal_value = @App\SysHelper::get_aed_amount_new($value->deal_currency, $value->deal_value);
                                                $deal_profit = @App\SysHelper::get_aed_amount_new($value->deal_currency, $value->deal_profit);
                                                $deal_percentage = $deal_value != 0 ? round(($deal_profit / $deal_value) * 100, 2) : 0;
                                                $gp = (($value->total_taxableamount - $value->deal_discount) * $deal_percentage) / 100;
                                                $total_value += $value->value;
                                                $total_discount += ($value->discount + $value->deal_discount);
                                                $total_taxable_amount += ($value->total_taxableamount - $value->deal_discount);
                                                $total_tax += $value->total_vatamount;
                                                $total_amount += $value->amount;
                                                $total_gp += $gp;
                                            @endphp
                                            <tr>
                                                @if (!$hideCompanyColumn)
                                                    <td>{{ @$value->company->company_name }}</td>
                                                @endif
                                                <td class="text-center">
                                                    @if (@$value->code == '')
                                                        --
                                                    @else
                                                        <a href="{{ url('get-url-deal-track/' . $value->code) }}" target="_blank">{{ @$value->code }}</a>
                                                    @endif
                                                </td>
                                                <td class="text-center"><a href="{{ url('sales-invoice/' . $value->id) }}" target="_blank">{{ @$value->doc_number }}</a></td>
                                                <td class="text-center">{{ date('d/m/Y', strtotime(@$value->doc_date)) }}</td>
                                                <td>{{ @$value->accountname->account_name }}</td>
                                                <td class="text-end">{{ @App\SysHelper::com_curr_format(@$value->value, 2, '.', ',') }}</td>
                                                <td class="text-end">{{ @App\SysHelper::com_curr_format(@$value->discount + $value->deal_discount, 2, '.', ',') }}</td>
                                                <td class="text-end">{{ @App\SysHelper::com_curr_format(@$value->total_taxableamount - $value->deal_discount, 2, '.', ',') }}</td>
                                                <td class="text-end">{{ @App\SysHelper::com_curr_format(@$value->total_vatamount, 2, '.', ',') }}</td>
                                                <td class="text-end">{{ @App\SysHelper::com_curr_format(@$value->amount, 2, '.', ',') }}</td>
                                                <td class="text-end">{{ @App\SysHelper::com_curr_format($gp, 2, '.', ',') }}</td>
                                                <td class="text-end">{{ $deal_percentage }}%</td>
                                                <td>{{ @$value->salesman->full_name }}</td>
                                                <td class="text-center">{{ @$value->lpo_number }}</td>
                                                <td class="text-center">{{ !empty($value->lpo_date) ? date('d/m/Y', strtotime($value->lpo_date)) : '' }}</td>
                                                <td class="text-center">{{ @$value->currency_name->code }}</td>
                                                <td class="text-center">
                                                    @if (isset($paid_doc_numbers[$value->doc_number]))
                                                        <span class="text-success">Paid</span>
                                                    @else
                                                        <span class="text-danger">Pending</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-end gap-2">
                                                        @if (!empty($value->attach))
                                                            @foreach (explode(',', $value->attach) as $att)
                                                                <a href="{{ url(trim($att)) }}" target="_blank"><i class="ico icon-bold-paperclip"></i></a>
                                                            @endforeach
                                                        @endif
                                                        <a href="{{ url('sales-invoice/' . $value->id . '/download/t') }}" target="_blank">
                                                            <i class="ico icon-bold-download-minimalistic text-dark" style="font-size: 16px;"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                        @if ($salesinvoice->count() == 0)
                                            <tr><td colspan="{{ $hideCompanyColumn ? 17 : 18 }}" class="text-center">No data found</td></tr>
                                        @endif
                                    </tbody>
                                    <footer>
                                        <tr>
                                            <th colspan="{{ $hideCompanyColumn ? 4 : 5 }}"></th>
                                            <th class="text-end">{{ @App\SysHelper::com_curr_format($total_value, 2, '.', ',') }}</th>
                                            <th class="text-end">{{ @App\SysHelper::com_curr_format($total_discount, 2, '.', ',') }}</th>
                                            <th class="text-end">{{ @App\SysHelper::com_curr_format($total_taxable_amount, 2, '.', ',') }}</th>
                                            <th class="text-end">{{ @App\SysHelper::com_curr_format($total_tax, 2, '.', ',') }}</th>
                                            <th class="text-end">{{ @App\SysHelper::com_curr_format($total_amount, 2, '.', ',') }}</th>
                                            <th class="text-end">{{ @App\SysHelper::com_curr_format($total_gp, 2, '.', ',') }}</th>
                                            <th colspan="7"></th>
                                        </tr>
                                    </footer>
                                @else
                                    <thead>
                                        <tr>
                                            <th style='width:260px'>{{ in_array($report_group, ['company_wise']) ? 'Company Name' : (in_array($report_group, ['customer_wise']) ? 'Customer Name' : 'Sales Person Name') }}</th>
                                            <th class="text-center">No. of Invoices</th>
                                            <th class="text-end">Value</th>
                                            <th class="text-end">Discount</th>
                                            <th class="text-end">Taxable Amount</th>
                                            <th class="text-end">Tax</th>
                                            <th class="text-end">Amount</th>
                                            <th class="text-end">GP</th>
                                            <th class="text-end">GP%</th>
                                            @if ($report_group === 'customer_wise')
                                                <th class="text-end" style="width:160px">Outstanding</th>
                                                <th class="text-center" style="width:120px">Last Invoice Date</th>
                                                <th class="text-start" style="width:110px">Sales Person</th>
                                                <th class="text-center" style="width:40px">GL</th>
                                            @endif
                                            @if ($report_group === 'company_wise')
                                                <th class="text-center" style="width:320px">Reports</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $sum_invoice_count = 0;
                                            $sum_value = 0;
                                            $sum_discount = 0;
                                            $sum_taxable = 0;
                                            $sum_tax = 0;
                                            $sum_amount = 0;
                                            $sum_gp = 0;
                                            $sum_customer_balance = 0;
                                        @endphp
                                        @foreach ($report_rows as $row)
                                            @php
                                                $sum_invoice_count += $row->invoice_count;
                                                $sum_value += $row->value;
                                                $sum_discount += $row->discount;
                                                $sum_taxable += $row->taxable;
                                                $sum_tax += $row->tax;
                                                $sum_amount += $row->amount;
                                                $sum_gp += $row->gp;
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
                                                        <a href="{{ url('company?active=' . $row->company_id) }}" target="_blank">{{ $row->group_name }}</a>
                                                    @elseif ($report_group === 'customer_wise' && !empty($row->customer))
                                                        <a href="{{ url('get-url-customer-from-chart-of-accounts/' . $row->customer) }}" target="_blank">{{ $row->group_name }}</a>
                                                    @elseif ($report_group === 'sales_person_wise' && !empty($row->staff_id))
                                                        <a href="{{ url('view-staff/' . $row->staff_id) }}" target="_blank">{{ $row->group_name }}</a>
                                                    @else
                                                        {{ $row->group_name }}
                                                    @endif
                                                </td>
                                                <td class="text-center"><a href="{{ route('sales.invoice.report.detail', $drillFilters) }}">{{ $row->invoice_count }}</a></td>
                                                <td class="text-end">{{ @App\SysHelper::com_curr_format($row->value, 2, '.', ',') }}</td>
                                                <td class="text-end">{{ @App\SysHelper::com_curr_format($row->discount, 2, '.', ',') }}</td>
                                                <td class="text-end">{{ @App\SysHelper::com_curr_format($row->taxable, 2, '.', ',') }}</td>
                                                <td class="text-end">{{ @App\SysHelper::com_curr_format($row->tax, 2, '.', ',') }}</td>
                                                <td class="text-end">{{ @App\SysHelper::com_curr_format($row->amount, 2, '.', ',') }}</td>
                                                <td class="text-end">{{ @App\SysHelper::com_curr_format($row->gp, 2, '.', ',') }}</td>
                                                <td class="text-end">{{ round($row->gp_percent, 2) }}%</td>
                                                @if ($report_group === 'customer_wise')
                                                    <td class="text-end">
                                                        <a href="javascript:void(0)" class="open-receivable-outstanding"
                                                           data-customer-id="{{ $row->customer }}"
                                                           data-till-date="{{ !empty($ctrl_date2) ? \Carbon\Carbon::parse($ctrl_date2)->format('d/m/Y') : date('d/m/Y') }}">
                                                            {{ @App\SysHelper::com_curr_format($row->customer_balance ?? 0, 2, '.', ',') }}
                                                        </a>
                                                    </td>
                                                    <td class="text-center">
                                                        @if (!empty($row->last_invoice_date))
                                                            {{ date('d/m/Y', strtotime($row->last_invoice_date)) }}
                                                            ({{ \Carbon\Carbon::parse($row->last_invoice_date)->diffInDays(\Carbon\Carbon::today()) }}d)
                                                        @endif
                                                    </td>
                                                    <td>{{ $row->sales_person_names ?? '' }}</td>
                                                    <td class="text-center">
                                                        <a href="javascript:void(0)" class="open-general-ledger"
                                                           data-customer-id="{{ $row->customer }}"
                                                           data-from-date="{{ !empty($ctrl_date) ? \Carbon\Carbon::parse($ctrl_date)->format('d/m/Y') : date('01/01/Y') }}"
                                                           data-to-date="{{ !empty($ctrl_date2) ? \Carbon\Carbon::parse($ctrl_date2)->format('d/m/Y') : date('d/m/Y') }}"
                                                           title="Open General Ledger">
                                                            <i class="ico icon-outline-eye text-success"></i>
                                                        </a>
                                                    </td>
                                                @endif
                                                @if ($report_group === 'company_wise')
                                                    <td class="text-center">
                                                        <div class="d-inline-flex gap-1 flex-nowrap">
                                                            <a class="btn btn-sm btn-light py-0 px-2 text-nowrap" href="{{ route('sales.invoice.report.detail', ['report_group' => 'date_wise', 'scope_company_id' => $row->company_id]) }}">Date Wise</a>
                                                            <a class="btn btn-sm btn-light py-0 px-2 text-nowrap" href="{{ route('sales.invoice.report.detail', ['report_group' => 'customer_wise', 'scope_company_id' => $row->company_id]) }}">Customer Wise</a>
                                                            <a class="btn btn-sm btn-light py-0 px-2 text-nowrap" href="{{ route('sales.invoice.report.detail', ['report_group' => 'sales_person_wise', 'scope_company_id' => $row->company_id]) }}">Sales Person Wise</a>
                                                        </div>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                        @if ($report_rows->count() == 0)
                                            <tr><td colspan="{{ $report_group === 'company_wise' ? 10 : ($report_group === 'customer_wise' ? 13 : 9) }}" class="text-center">No data found</td></tr>
                                        @endif
                                    </tbody>
                                    <footer>
                                        <tr>
                                            <th>Total</th>
                                            <th class="text-center">{{ $sum_invoice_count }}</th>
                                            <th class="text-end">{{ @App\SysHelper::com_curr_format($sum_value, 2, '.', ',') }}</th>
                                            <th class="text-end">{{ @App\SysHelper::com_curr_format($sum_discount, 2, '.', ',') }}</th>
                                            <th class="text-end">{{ @App\SysHelper::com_curr_format($sum_taxable, 2, '.', ',') }}</th>
                                            <th class="text-end">{{ @App\SysHelper::com_curr_format($sum_tax, 2, '.', ',') }}</th>
                                            <th class="text-end">{{ @App\SysHelper::com_curr_format($sum_amount, 2, '.', ',') }}</th>
                                            <th class="text-end">{{ @App\SysHelper::com_curr_format($sum_gp, 2, '.', ',') }}</th>
                                            <th class="text-end">{{ $sum_value != 0 ? round(($sum_gp / $sum_value) * 100, 2) : 0 }}%</th>
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
                        <style>
                            .report-type-trigger {
                                color: #212529;
                                text-decoration: none;
                                font-weight: 500;
                                display: inline-flex;
                                align-items: center;
                            }
                            .report-type-trigger:hover {
                                color: #499258;
                            }
                            .dropdown-menu .dropend .dropdown-menu {
                                top: 0;
                                left: 100%;
                                margin-top: -1px;
                            }
                            .report-type-dropdown .dropdown-item.active,
                            .report-type-dropdown .dropdown-item.text-success {
                                color: #499258 !important;
                                background-color: transparent !important;
                                font-weight: 600;
                            }
                            .pagination .page-link {
                                color: #499258; /* Bootstrap green */
                        
                            }
                        
                          
                            .pagination .page-item.active .page-link {
                                background-color: #499258;
                                color: #fff;
                            }
                        </style>

                        
                        @if (($report_group ?? 'date_wise') === 'date_wise' && ($ctrl_show_all ?? 0) != 1 && $salesinvoice instanceof \Illuminate\Pagination\LengthAwarePaginator)
                            <div class="d-flex justify-content-start mt-3">
                                {{ $salesinvoice->appends(request()->input())->links() }}
                            </div>
                        @endif

                        <script>
                            function show_tool_tip(id) {
                                $('#desc_' + id).css('white-space', '');
                            }

                            function hide_tool_tip(id) {
                                $('#desc_' + id).css('white-space', 'nowrap');
                            }
                        </script>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

<script>
$(document).ready(function () {
    $(document).on('click', '.open-receivable-outstanding', function (e) {
        e.preventDefault();
        var customerId = $(this).data('customer-id');
        var tillDate = $(this).data('till-date') || '';
        $('#receivableOutstandingCustomerId').val(customerId);
        $('#receivableOutstandingTillDate').val(tillDate);
        $('#receivableOutstandingRedirectForm').trigger('submit');
    });
    $(document).on('click', '.open-general-ledger', function (e) {
        e.preventDefault();
        $('#generalLedgerCustomerId').val($(this).data('customer-id'));
        $('#generalLedgerFromDate').val($(this).data('from-date') || '');
        $('#generalLedgerToDate').val($(this).data('to-date') || '');
        $('#generalLedgerRedirectForm').trigger('submit');
    });

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

    $('#exportSalesInvoiceReport').on('click', function (e) {
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
            rows.push(['Sales Invoice Report']);
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

            var $footer = $table.find('footer tr').first();
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
            var worksheet = workbook.addWorksheet('Sales Invoice Report');

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
                saveAs(blob, 'sales_invoice_report_' + pad(d.getDate()) + '-' + pad(d.getMonth() + 1) + '-' + d.getFullYear() + '.xlsx');
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

        var params = new URLSearchParams(new FormData(document.getElementById('sales-invoice-report')));
        params.set('show_all', '1');
        params.set('report_group', 'date_wise');
        var exportUrl = "{{ url('sales-invoice-report-detail') }}" + '?' + params.toString();

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

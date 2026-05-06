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
                    <h4 class="purchase-order-content-header-left">
                        Sales Report
                    </h4>
                    <div class="purchase-order-content-header-right">
                        <button type="button" class="btn btn-light" id="exportSalesInvoiceReport" title="Export to Excel">
                            <i class="ico icon-outline-export text-success"></i> Export
                        </button>
                        {{-- <a class="btn btn-light" href="{{ url('chartofaccounts-import') }}"> Account Import</a> --}}

                        {{-- <a class="btn btn-light" href="{{url('payment-add')}}">
                        <i class="ico icon-outline-add-square text-success"></i> Add Payment
                    </a> --}}
                        {{-- <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addChequeModal">
                        <i class="ico icon-outline-add-square text-success"></i> Search
                    </button> --}}
                    </div>
                </div>


                <div class="card mb-3">
                    <div class="card-body">
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'sales-invoice-report', 'method' => 'get', 'id' => 'sales-invoice-report']) }}
                        <div class="row">

                            <div class="col-1-5 mb-2">
                                <label for="" class="form-label">Documents Number</label>
                                <input class="form-control" type="text" autocomplete="off" name="documents_number"
                                    value="{{ $ctrl_doc_no }}">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="" class="form-label">Customer</label>
                                <select class="form-control js-example-basic-single" name="customer" id="customer">
                                    <option value=""></option>
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
                            <div class="col-1-5 mb-2">
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
                            <div class="col-1-5 mb-2">
                                <label for="" class="form-label">Sales Person</label>
                                <select class="form-control js-example-basic-single" name="sales_person" id="sales_person">
                                    <option value=""></option>
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
                            <script>
                                function set_filter() {
                                    if ($('#from_date').val() != "" || $('#to_date').val() != "") {
                                        $('#filter_by').val('')
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

                        <div class="table-responsive">
                            <table class="table table-hover" id="long-list" style="table-layout: fixed;width:100%">
                                <thead>
                                    @if (session()->has('message-success') != '' || session()->get('message-danger') != '')
                                        <tr>
                                            <td colspan="11">
                                                @if (session()->has('message-success'))
                                                    <div class="alert alert-success">
                                                        {{ session()->get('message-success') }}
                                                    </div>
                                                @elseif(session()->has('message-danger'))
                                                    <div class="alert alert-danger">
                                                        {{ session()->get('message-danger') }}
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        @if (session('logged_session_data.company_id') == 1)
                                            <th style="width: 80px;">@lang('Company')</th>
                                        @endif
                                        <th style="width: 120px;" class="text-center">@lang('Deal')</th>
                                        <th class="text-center" style="width: 100px;">@lang('SI No')</th>
                                        <th class="text-center">@lang('SI Date')</th>
                                        <th>@lang('Customer')</th>
                                        <th style="width: 120px;" class="text-end">@lang('Value')</th>
                                        <th style="width: 120px;" class="text-end">@lang('Discount')</th>

                                        <th style="width: 120px;" class="text-end">@lang('Taxable')</th>
                                        <th style="width: 120px;" class="text-end">@lang('Tax')</th>
                                        <th style="width: 120px;" class="text-end">@lang('Amount')</th>
                                        <th style="width: 120px;" class="text-end">@lang('GP')</th>
                                        <th class="text-end">@lang('GP%')</th>
                                        <th>@lang('Salesman')</th>
                                        <th>@lang('LPO')</th>
                                        <th>@lang('LPO No')</th>
                                        <th>@lang('Currency')</th>
                                        <th style="width: 30px;"><i class="ico icon-bold-paperclip"></i> </th>

                                        <th class="text-center">@lang('lang.action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $count = 1;
                                        $total_taxable_amount = 0;
                                        $total_tax = 0;
                                        $total_amount = 0;
                                        $total_value = 0;
                                        $total_discount = 0;
                                        $total_gp = 0;
                                    @endphp
                                    @foreach ($salesinvoice as $value)
                                        <tr @if (@$value->status == 2) class="bg-dark" @endif>
                                            @if (session('logged_session_data.company_id') == 1)
                                                <td>{{ @$value->company->company_name }}</td>
                                            @endif
                                            <td class="text-center">
                                                @if (@$value->code == '')
                                                    --
                                                @else
                                                    <a href="{{ url('get-url-deal-track/' . $value->code) }}"
                                                        target="_blank">{{ @$value->code }}</a>
                                                @endif
                                            </td>
                                            <td class="text-center"><a href="{{ url('sales-invoice/' . $value->id) }}"
                                                    target="_blank">{{ @$value->doc_number }}</a></td>
                                            <td class="text-center">{{ date('d/m/Y', strtotime(@$value->doc_date)) }}</td>
                                            <td>{{ @$value->accountname->account_name }}</td>
                                            <td class="text-end">
                                                {{ @App\SysHelper::com_curr_format(@$value->value, 2, '.', ',') }}<?php $total_value += $value->value; ?>
                                            </td>
                                            <td class="text-end">
                                                {{ @App\SysHelper::com_curr_format(@$value->discount + $value->deal_discount, 2, '.', ',') }}<?php $total_discount += $value->discount + $value->deal_discount; ?>
                                            </td>

                                            <td class="text-end">
                                                {{ @App\SysHelper::com_curr_format(@$value->total_taxableamount - $value->deal_discount, 2, '.', ',') }}<?php $total_taxable_amount += $value->total_taxableamount - $value->deal_discount; ?>
                                            </td>
                                            <td class="text-end">
                                                {{ @App\SysHelper::com_curr_format(@$value->total_vatamount, 2, '.', ',') }}<?php $total_tax += $value->total_vatamount; ?>
                                            </td>
                                            <td class="text-end">
                                                {{ @App\SysHelper::com_curr_format(@$value->amount, 2, '.', ',') }}<?php $total_amount += $value->amount; ?>
                                            </td>


                                            @php
                                                $deal_value = @App\SysHelper::get_aed_amount_new(
                                                    $value->deal_currency,
                                                    $value->deal_value
                                                );
                                                $deal_profit = @App\SysHelper::get_aed_amount_new(
                                                    $value->deal_currency,
                                                    $value->deal_profit
                                                );

                                                if ($deal_value != 0) {
                                                    $deal_percentage = round(($deal_profit / $deal_value) * 100, 2);
                                                } else {
                                                    $deal_percentage = 0;
                                                }

                                                $gp =
                                                    (($value->total_taxableamount - $value->deal_discount) *
                                                        $deal_percentage) /
                                                    100;
                                            @endphp

                                            <td class="text-end">

                                                {{ @App\SysHelper::com_curr_format($gp, 2, '.', ',') }}

                                                {{-- {{ @App\SysHelper::com_curr_format(@$deal_profit,2,'.',',') }} --}}

                                                <?php $total_gp += $gp; ?>

                                            </td>
                                            <td class="text-end">
                                                @if ($deal_value != 0)
                                                    {{ round(($deal_profit / $deal_value) * 100, 2) }}%
                                                @else
                                                    0%
                                                @endif
                                            </td>
                                            <td class="">{{ @$value->salesman->full_name }}</td>


                                            <td>{{ @$value->lpo_date }}</td>
                                            <td>{{ @$value->lpo_number }}</td>

                                            <td>{{ @$value->currency_name->code }}</td>

                                            <td>
                                                @if (empty(@$value->attach))
                                                @else
                                                    @foreach (explode(',', @$value->attach) as $att)
                                                        <a href="{{ url(trim($att)) }}" target="_blank"><i
                                                                class="fa fa-paperclip" aria-hidden="true"></i></a>&nbsp;
                                                    @endforeach
                                                @endif
                                            </td>

                                            <td class="text-end">
                                                <a class="btn btn-sm btn-light text-center d-block"
                                                    href="{{ url('sales-invoice/' . $value->id . '/download/t') }}"><i
                                                        class="ico icon-bold-download-minimalistic text-dark"
                                                        style="font-size: 16px;"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <footer>
                                    <tr>
                                        @if (session('logged_session_data.company_id') == 1)
                                            <th></th>
                                        @endif
                                        <th colspan="4"></th>
                                        <th class="text-end">
                                            {{ @App\SysHelper::com_curr_format($total_value, 2, '.', ',') }}
                                        </th>
                                        <th class="text-end">
                                            {{ @App\SysHelper::com_curr_format($total_discount, 2, '.', ',') }}</th>
                                        <th class="text-end">
                                            {{ @App\SysHelper::com_curr_format($total_taxable_amount, 2, '.', ',') }}</th>
                                        <th class="text-end">
                                            {{ @App\SysHelper::com_curr_format($total_tax, 2, '.', ',') }}
                                        </th>
                                        <th class="text-end">
                                            {{ @App\SysHelper::com_curr_format($total_amount, 2, '.', ',') }}
                                        </th>
                                        <th class="text-end">
                                            {{ @App\SysHelper::com_curr_format($total_gp, 2, '.', ',') }}</th>
                                        <th colspan="7"></th>
                                    </tr>
                                    {{-- <tr>
                                <th colspan="15">
                                    {{ $salesinvoice->appends(request()->input())->links() }}
                                </th>
                            </tr> --}}
                                </footer>
                            </table>
                        </div>


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
    $('#exportSalesInvoiceReport').on('click', function (e) {
        e.preventDefault();

        var companyName = @json(@App\SysCompany::find(session('logged_session_data.company_id') ?? '')->trade_name ?? '');
        var fromDate = @json($ctrl_date ?? '');
        var toDate = @json($ctrl_date2 ?? '');

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

        var $table = $('#long-list');
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
            alert('No table headers found for export.');
            return;
        }

        var rows = [];
        rows.push([companyName]);
        rows.push(['Sales Invoice Report']);
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
            $footer.children('th').each(function (index) {
                var text = $(this).text().trim().replace(/\s+/g, ' ');
                totalRow.push(text);
            });
            if (totalRow.length > 0) {
                rows.push([]);
                rows.push(totalRow);
            }
        }

        var workbook = new ExcelJS.Workbook();
        var worksheet = workbook.addWorksheet('Sales Invoice Report');

        worksheet.columns = headers.map(function () {
            return { width: 18 };
        });

        var rowIndex = 0;
        var headerRowIndex = (fromDate || toDate) ? 5 : 4;
        rows.forEach(function (rowData) {
            rowIndex++;
            var row = worksheet.addRow(rowData);
            if (rowIndex === 1) {
                worksheet.mergeCells(rowIndex, 1, rowIndex, headers.length);
                row.getCell(1).font = { bold: true, size: 14 };
                row.getCell(1).alignment = { horizontal: 'center', vertical: 'middle' };
            } else if (rowIndex === 2) {
                worksheet.mergeCells(rowIndex, 1, rowIndex, headers.length);
                row.getCell(1).font = { bold: true, size: 12 };
                row.getCell(1).alignment = { horizontal: 'center', vertical: 'middle' };
            } else if (rowIndex === 3 && (fromDate || toDate)) {
                worksheet.mergeCells(rowIndex, 1, rowIndex, headers.length);
                row.getCell(1).font = { size: 10 };
                row.getCell(1).alignment = { horizontal: 'center', vertical: 'middle' };
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
    });
});
</script>

@endsection

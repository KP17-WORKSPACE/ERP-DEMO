@extends('backEnd.newmasterpage')
@section('mainContent')
    @php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp

    <?php try { ?>

    <script src="https://cdn.jsdelivr.net/npm/exceljs@4.3.0/dist/exceljs.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

    <div class="content-container col-12">
        <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
            <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
                <div class="purchase-order-content-header">
                    <h4 class="purchase-order-content-header-left">
                        Customer Ageing Report
                    </h4>
                    <div class="purchase-order-content-header-right">

   <button type="button" id="exportExcelAgeing" class="btn btn-light me-2">
                            <i class="ico icon-outline-export text-success"></i> Export
                        </button>


                        <div class="dropdown">
                            <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ico icon-outline-hamburger-menu"></i>
                            </button>
                            <ul class="dropdown-menu" style="">

                                <li>
                                    <a href="{{ url('receivable-outstanding') }}"
                                        class="dropdown-item d-flex align-items-center"><i
                                            class="ico icon-outline-document-text text-success title-15 me-2"></i>
                                        Receivable Outstanding</a>
                                </li>

                                 


                            </ul>
                        </div>


                    </div>
                </div>





                <div class="card mb-3">

                    <div class="card-body">
                        @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
                            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'customer-ageing-report', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}

                            <div class="row">
                                <div class="col-1-5  mb-20">
                                    <div class="no-gutters input-right-icon">
                                        <div class="col">
                                            <div class="form-label">
                                                <label>@lang('As of Date')</label>
                                                <input class="form-control date-picker" id="till_date" type="text"
                                                    name="till_date"
                                                    value="{{ @App\SysHelper::normalizeToDmy($till_date) }}"
                                                    autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-1-5  mb-2">

                                    <label for="" class="form-label">Internal/External</label>
                                    <select class="form-control js-example-basic-single" name="list_in_ex" id="list_in_ex">
                                        <option value="" @if (@$ctrl_intext == '') selected @endif>-Select-
                                        </option>
                                        <option value="1" @if (@$ctrl_intext == '1') selected @endif>Internal
                                        </option>
                                        <option value="0" @if (@$ctrl_intext == '0') selected @endif>External
                                        </option>
                                    </select>
                                </div>
                                <div class="col-1-5  mt-4" >
                                            <button class="btn btn-light" type="submit">
                                                <i class="ico icon-outline-minimalistic-magnifer text-success"></i> Filter
                                            </button>
                                        </div>
                            </div>

                            {{ Form::close() }}
                        @endif
                    </div>


                </div>
            </div>


            <form id="receivableOutstandingRedirectForm" method="POST" action="{{ route('receivable-outstanding') }}" target="_blank" style="display:none;">
                @csrf
                <input type="hidden" name="account_id[]" id="receivableOutstandingCustomerId" value="">
                <input type="hidden" name="till_date" id="receivableOutstandingTillDate" value="">
            </form>

            <div class="card mb-3">
                <div class="card-body">

                    @if (count($data_all) == 0)
                        <div class="row">
                            <div class="col-md-12 m-2 text-center">
                                <b>No Customer Ageing Data Found!</b>
                            </div>
                        </div>
                    @else
                        <div class="table-responsive mb-4 mt-4">
                            <table id="long-list" class="table table-hover" style="border: solid 1px #e3e6f0;">
                                <thead>
                                    <tr style="background: #eeeeee; color: #000000;">
                                        <th class=" text-start" width="300px">Customer Name</th>
                                        <th class=" text-end" width="120px">Net Invoice Amount</th>
                                        <th class=" text-end" width="120px">Net Balance</th>
                                        <th class=" text-end" width="120px">0-30</th>
                                        <th class=" text-end" width="120px">31-60</th>
                                        <th class=" text-end" width="120px">61-90</th>
                                        <th class=" text-end" width="120px">>90</th>
                                        <th class=" text-end" width="120px">Total Finance Cost</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $grand_total_invoice_amount = 0;
                                        $grand_total_balance = 0;
                                        $grand_total_0_30 = 0;
                                        $grand_total_31_60 = 0;
                                        $grand_total_61_90 = 0;
                                        $grand_total_90_above = 0;
                                        $grand_total_finance_cost = 0;
                                        $total_customers = 0;
                                    @endphp

                                    @php
                                        $opbinvoice_map = $opbinvoice_map ?? collect([]);
                                        $payment_terms_map = $payment_terms_map ?? collect([]);
                                        $sales_invoice_map = $sales_invoice_map ?? collect([]);
                                        $receivable_finance_rate = $receivable_finance_rate ?? 0;
                                        $list_of_unadjusted = $list_of_unadjusted ?? collect([]);
                                        $list_of_unadjusted_jv_to_jv = $list_of_unadjusted_jv_to_jv ?? collect([]);
                                        $list_of_adjusted_pdc = $list_of_adjusted_pdc ?? collect([]);
                                        $opb_balance_amount = $opb_balance_amount ?? collect([]);
                                        $com_id = $com_id ?? session('logged_session_data.company_id');
                                    @endphp
                                    @if (count($data_all) > 0)
                                        @foreach ($data_all as $data)
                                            <?php
                                            if(count($data)>0){
                                                $aname = $accounts->where('id', $data[0]->account_id)->first();

                                                $customerTotals = App\SysHelper::getReceivableOutstandingCustomerTotals(
                                                    $data[0]->account_id,
                                                    $com_id,
                                                    $till_date,
                                                    $data,
                                                    $list_of_unadjusted,
                                                    $list_of_unadjusted_jv_to_jv,
                                                    $payment_terms_map,
                                                    $sales_invoice_map,
                                                    $opbinvoice_map,
                                                    $receivable_finance_rate,
                                                    $list_of_adjusted_pdc
                                                );
                                                $customer_total_invoice_amount = $customerTotals['net_invoice_amount'];
                                                $customer_total_balance = $customerTotals['net_balance'];
                                                $customer_total_0_30 = $customerTotals['0_30'];
                                                $customer_total_31_60 = $customerTotals['31_60'];
                                                $customer_total_61_90 = $customerTotals['61_90'];
                                                $customer_total_90_above = $customerTotals['90_plus'];
                                                $customer_total_finance_cost = $customerTotals['finance_cost'];
                                                $opb_record = $opb_balance_amount->where('account_id', $data[0]->account_id)->first();
                                                $opb_total = $opb_record ? (float) $opb_record->opb_amount : 0;
                                                $is_total_attention = !empty($customerTotals['has_overdue']) || round((float) $customer_total_balance, 2) != round($opb_total, 2);
                                                
                                                // Match receivable outstanding visibility: the customer header is shown only when its main total is non-zero.
                                                if(abs($customer_total_balance) > 0.01) {
                                                    $grand_total_invoice_amount += $customer_total_invoice_amount;
                                                    $grand_total_balance += $customer_total_balance;
                                                    $grand_total_0_30 += $customer_total_0_30;
                                                    $grand_total_31_60 += $customer_total_31_60;
                                                    $grand_total_61_90 += $customer_total_61_90;
                                                    $grand_total_90_above += $customer_total_90_above;
                                                    $grand_total_finance_cost += $customer_total_finance_cost;
                                                    $total_customers++;
                                            ?>
                                            <tr>
                                                <td style="cursor: pointer;"
                                                    class="text-start open-receivable-outstanding"
                                                    data-customer-id="{{ $aname->id }}"
                                                    data-till-date="{{ @App\SysHelper::normalizeToDmy($till_date) }}">
                                                    {{ $aname->account_name }} @if (@App\SysHelper::getCompanyCodeSettings()['is_customer_code']) ({{ $aname->account_code }})
                                                    @endif
                                                </td>
                                                <td class=" text-end">
                                                    {{ @App\SysHelper::com_curr_format($customer_total_invoice_amount, 2, '.', ',') }}
                                                </td>
                                                <td class=" text-end {{ $is_total_attention ? 'text-danger' : '' }}">
                                                    {{ @App\SysHelper::com_curr_format($customer_total_balance, 2, '.', ',') }}
                                                </td>
                                                <td class=" text-end">
                                                    {{ @App\SysHelper::com_curr_format($customer_total_0_30, 2, '.', ',') }}
                                                </td>
                                                <td class=" text-end">
                                                    {{ @App\SysHelper::com_curr_format($customer_total_31_60, 2, '.', ',') }}
                                                </td>
                                                <td class=" text-end">
                                                    {{ @App\SysHelper::com_curr_format($customer_total_61_90, 2, '.', ',') }}
                                                </td>
                                                <td class=" text-end">
                                                    {{ @App\SysHelper::com_curr_format($customer_total_90_above, 2, '.', ',') }}
                                                </td>
                                                <td class=" text-end">
                                                    {{ ($customer_total_finance_cost ?? 0) != 0 ? App\SysHelper::com_curr_format($customer_total_finance_cost, 2, '.', ',') : '' }}
                                                </td>
                                            </tr>
                                            <?php
                                                }
                                            }
                                            ?>
                                        @endforeach
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr style="background: #f8f9fa; font-weight: bold;">
                                        <th class=" text-start">Grand Total ({{ $total_customers }} Customers)</th>
                                        <th class=" text-end">
                                            {{ @App\SysHelper::com_curr_format($grand_total_invoice_amount, 2, '.', ',') }}</th>
                                        <th class=" text-end">
                                            {{ @App\SysHelper::com_curr_format($grand_total_balance, 2, '.', ',') }}</th>
                                        <th class=" text-end">
                                            {{ @App\SysHelper::com_curr_format($grand_total_0_30, 2, '.', ',') }}</th>
                                        <th class=" text-end">
                                            {{ @App\SysHelper::com_curr_format($grand_total_31_60, 2, '.', ',') }}</th>
                                        <th class=" text-end">
                                            {{ @App\SysHelper::com_curr_format($grand_total_61_90, 2, '.', ',') }}</th>
                                        <th class=" text-end">
                                            {{ @App\SysHelper::com_curr_format($grand_total_90_above, 2, '.', ',') }}</th>
                                        <th class=" text-end">
                                            {{ ($grand_total_finance_cost ?? 0) != 0 ? App\SysHelper::com_curr_format($grand_total_finance_cost, 2, '.', ',') : '' }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
    </div>

    <script>
        // Export customer ageing table to Excel
        $(document).ready(function () {
            $(document).on('click', '.open-receivable-outstanding', function (e) {
                e.preventDefault();
                var customerId = $(this).data('customer-id');
                var tillDate = $(this).data('till-date') || $('#till_date').val() || '';
                $('#receivableOutstandingCustomerId').val(customerId);
                $('#receivableOutstandingTillDate').val(tillDate);
                $('#receivableOutstandingRedirectForm').trigger('submit');
            });

            $('#exportExcelAgeing').on('click', function () {
                var companyName = @json(@App\SysCompany::find(session('logged_session_data.company_id') ?? '')->trade_name ?? '');
                var dateFrom = $('#from_date').val() ? $('#from_date').val().trim() : '';
                var dateTo = $('#to_date').val() ? $('#to_date').val().trim() : '';
                var tillDate = $('#till_date').val() ? $('#till_date').val().trim() : '';
                var totalCustomers = $('#long-list tbody tr').length;
                var reportTitle = 'Customer Ageing Report' + (totalCustomers ? ' (' + totalCustomers + ' Customers)' : '');

                function formatDMY(value) {
                    if (!value) return value;
                    var normalized = value.trim().replace(/\s+/g, '');
                    var parts = normalized.split(/[\/\-\.]/);
                    if (parts.length === 3) {
                        if (parts[0].length === 4) {
                            return parts[2] + '/' + parts[1] + '/' + parts[0];
                        }
                        return parts[0] + '/' + parts[1] + '/' + parts[2];
                    }
                    return value;
                }

                var visibleColIndexes = [];
                var headerLabels = [];
                var $table = $('#long-list');
                var lastIndex = $table.find('thead tr th').length - 1;

                $table.find('thead tr th').each(function (i) {
                    if ($(this).css('display') !== 'none') {
                        var label = $(this).text().trim();
                        if (['actions', 'action', 'actions '].includes(label.toLowerCase().trim())) {
                            return;
                        }
                        visibleColIndexes.push(i);
                        headerLabels.push(label);
                    }
                });

                var rows = [];
                rows.push([companyName]);
                rows.push([reportTitle]);

                if (dateFrom || dateTo) {
                    var parts = [];
                    if (dateFrom) { parts.push('From: ' + formatDMY(dateFrom)); }
                    if (dateTo) { parts.push('To: ' + formatDMY(dateTo)); }
                    rows.push([parts.join('  ')]);
                } else if (tillDate) {
                    rows.push(['As of: ' + formatDMY(tillDate)]);
                }

                rows.push([]);
                rows.push(headerLabels);

                $('#long-list tbody tr').each(function () {
                    var $cells = $(this).find('td');
                    var rowData = [];
                    visibleColIndexes.forEach(function (i) {
                        var cellText = $cells.eq(i).text().trim().replace(/\s+/g, ' ');
                        rowData.push(cellText);
                    });
                    if (rowData.length) {
                        rows.push(rowData);
                    }
                });

                if (rows.length <= 5) {
                    alert('No data available for export');
                    return;
                }

                var workbook = new ExcelJS.Workbook();
                var worksheet = workbook.addWorksheet('Customer Ageing');
                var wsCols = [];
                for (var ci = 0; ci < headerLabels.length; ci++) {
                    wsCols.push({ width: 22 });
                }
                worksheet.columns = wsCols;

                var hdrIdx = rows.indexOf(headerLabels);
                if (hdrIdx < 0) hdrIdx = rows.length - 1;

                var wsRowNum = 0;
                for (var ri = 0; ri < hdrIdx; ri++) {
                    if (!(rows[ri] && rows[ri][0])) continue;
                    wsRowNum++;
                    var wsRow = worksheet.addRow([]);
                    wsRow.height = ri === 0 ? 26 : ri === 1 ? 20 : 16;
                    if (headerLabels.length > 1) worksheet.mergeCells(wsRowNum, 1, wsRowNum, headerLabels.length);
                    wsRow.getCell(1).value = rows[ri][0] || '';
                    if (ri === 0) wsRow.getCell(1).font = { bold: true, size: 14 };
                    else if (ri === 1) wsRow.getCell(1).font = { bold: true, size: 12 };
                    wsRow.getCell(1).alignment = { horizontal: 'center', vertical: 'middle' };
                }

                wsRowNum++;
                worksheet.addRow([]);

                wsRowNum++;
                var wsHdrRow = worksheet.addRow(headerLabels);
                wsHdrRow.height = 20;
                wsHdrRow.eachCell({ includeEmpty: true }, function (cell) {
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

                for (var di = hdrIdx + 1; di < rows.length; di++) {
                    var wsDataRow = worksheet.addRow(rows[di]);
                    wsDataRow.eachCell({ includeEmpty: true }, function (cell) {
                        cell.border = {
                            top: { style: 'thin', color: { argb: 'FFCCCCCC' } },
                            left: { style: 'thin', color: { argb: 'FFCCCCCC' } },
                            bottom: { style: 'thin', color: { argb: 'FFCCCCCC' } },
                            right: { style: 'thin', color: { argb: 'FFCCCCCC' } }
                        };
                    });
                }

                workbook.xlsx.writeBuffer().then(function (buffer) {
                    var blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                    function pad(n) { return n < 10 ? '0' + n : n; }
                    var d = new Date();
                    var filename = 'customer_ageing_' + pad(d.getDate()) + '-' + pad(d.getMonth() + 1) + '-' + d.getFullYear() + '.xlsx';
                    saveAs(blob, filename);
                });
            });
        });
    </script>

    <?php }catch (\Exception $e) { ?> {{ $e }}
    <?php  } ?>
@endsection

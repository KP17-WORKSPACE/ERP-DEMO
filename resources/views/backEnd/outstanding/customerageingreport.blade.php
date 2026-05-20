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
                                        <th class=" text-end" width="120px">Net Balance</th>
                                        <th class=" text-end" width="120px">0-30</th>
                                        <th class=" text-end" width="120px">31-60</th>
                                        <th class=" text-end" width="120px">61-90</th>
                                        <th class=" text-end" width="120px">>90</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $grand_total_balance = 0;
                                        $grand_total_0_30 = 0;
                                        $grand_total_31_60 = 0;
                                        $grand_total_61_90 = 0;
                                        $grand_total_90_above = 0;
                                        $total_customers = 0;
                                    @endphp

                                    @if (count($data_all) > 0)
                                        @foreach ($data_all as $data)
                                            <?php
                                            if(count($data)>0){
                                                $aname = $accounts->where('id', $data[0]->account_id)->first();
                                                
                                                $a1 = clone $data_adjestment_all;
                                                $a2 = clone $data_receipt_all;
                                                $a3 = clone $data_receipt2_all;
                                                $a4 = clone $data_receipt3_all;
                                                $a5 = clone $data_return_all;
                                                $a6 = clone $data_receipt_opb;

                                                $data_adjestment = $a1->wherein('srn_no',$data->pluck("transaction_no"));
                                                $data_receipt = $a2->where('account_id',$data[0]->account_id)->wherein('bi_doc_no',$data->pluck("transaction_no"))->get();
                                                $data_receipt2 = $a3->where('account_id',$data[0]->account_id)->wherein('bi_doc_no',$data->pluck("transaction_no"))->get();
                                                $data_receipt3 = $a4->where('account_id',$data[0]->account_id)->wherein('bi_doc_no',$data->pluck("transaction_no"))->get();
                                                $data_receipt6 = $a6->where('account_id',$data[0]->account_id)->wherein('bi_doc_no',$data->pluck("transaction_no"))->get();
                                                $data_return = $a5->where('customer',$data[0]->account_id)->wherein('srn_no',$data->pluck("transaction_no"))->get();
                                                
                                                // Initialize customer totals
                                                $customer_total_balance = 0;
                                                $customer_total_0_30 = 0;
                                                $customer_total_31_60 = 0;
                                                $customer_total_61_90 = 0;
                                                $customer_total_90_above = 0;
                                                
                                                // Process each transaction for this customer
                                                foreach ($data as $dt) {
                                                    $paid = 0;
                                                    
                                                    // Calculate adjustments
                                                    $adjustments = $data_adjestment->where('srn_no', $dt->transaction_no)->max('paid_amount');
                                                    $paid += $adjustments;
                                                    
                                                    // Calculate receipts
                                                    $bi_amount = $data_receipt->where('bi_doc_no', $dt->transaction_no)->sum('bi_amount');
                                                    $paid += $bi_amount;
                                                    
                                                    $bi_amount2 = $data_receipt2->where('bi_doc_no', $dt->transaction_no)->sum('bi_amount');
                                                    $paid += $bi_amount2;
                                                    
                                                    $bi_amount6 = $data_receipt6->where('bi_doc_no', $dt->transaction_no)->sum('bi_amount');
                                                    $paid += $bi_amount6;
                                                    
                                                    // Calculate returns (subtract)
                                                    $bi_amount3 = $data_receipt3->where('bi_doc_no', $dt->transaction_no)->sum('bi_amount');
                                                    $bi_amount4 = $data_return->where('siv_no', $dt->transaction_no)->sum('paid_amount');
                                                    $paid -= ($bi_amount3 + $bi_amount4);
                                                    
                                                    // Calculate balance based on transaction type
                                                    $balance = $dt->debit_amount - abs($paid);
                                                    
                                                    // Check if row should be hidden (matches receivable outstanding logic)
                                                    $is_hide2 = 0;
                                                    if (str_contains($dt->transaction_no, 'SR')) {
                                                        if ($dt->credit_amount >= $paid) {
                                                            $is_hide2 = 1;
                                                        }
                                                    }
                                                    if (str_contains($dt->transaction_no, 'SI')) {
                                                        if (abs($dt->debit_amount) == abs($paid)) {
                                                            $is_hide2 = 1;
                                                        }
                                                    }
                                                    
                                                    // Only process if row would be visible OR has credit amount
                                                    if ((($dt->debit_amount != $paid) || ($dt->credit_amount > 0)) && $is_hide2 == 0) {
                                                        // Calculate running balance like receivable outstanding
                                                        if (str_contains($dt->transaction_no, 'SR')) {
                                                            if ($dt->credit_amount >= $paid) {
                                                                $customer_total_balance -= $dt->credit_amount;
                                                            }
                                                        } else {
                                                            $customer_total_balance += $balance;
                                                        }
                                                        
                                                        // Get ageing bucket
                                                        $DueData = @App\SysHelper::get_due_date_sales_invoice($dt->transaction_no, $dt->transaction_date);
                                                        $ageing_bucket = isset($DueData[3]) ? $DueData[3] : 0;
                                                        
                                                        // Add to ageing bucket - ALWAYS use balance (debit_amount - abs(paid))
                                                        // This matches receivable outstanding logic
                                                        if($ageing_bucket == 1) {
                                                            $customer_total_0_30 += $balance;
                                                        } elseif($ageing_bucket == 2) {
                                                            $customer_total_31_60 += $balance;
                                                        } elseif($ageing_bucket == 3) {
                                                            $customer_total_61_90 += $balance;
                                                        } elseif($ageing_bucket == 4) {
                                                            $customer_total_90_above += $balance;
                                                        }
                                                    }
                                                }
                                                
                                                // Show customer if they have any outstanding transactions
                                                // Match receivable outstanding logic: show if balance exists OR has activity
                                                if(abs($customer_total_balance) > 0.01 || $customer_total_0_30 != 0 || $customer_total_31_60 != 0 || $customer_total_61_90 != 0 || $customer_total_90_above != 0) {
                                                    $grand_total_balance += $customer_total_balance;
                                                    $grand_total_0_30 += $customer_total_0_30;
                                                    $grand_total_31_60 += $customer_total_31_60;
                                                    $grand_total_61_90 += $customer_total_61_90;
                                                    $grand_total_90_above += $customer_total_90_above;
                                                    $total_customers++;
                                            ?>
                                            <tr>
                                                <td style="cursor: pointer;"
                                                    onclick="window.open('{{ url('get-url-customer/' . $aname->account_code) }}', '_blank')"
                                                    class=" text-start">
                                                   
                                                    {{ $aname->account_name }} @if (@App\SysHelper::getCompanyCodeSettings()['is_customer_code']) ({{ $aname->account_code }})

                                                  
                                                        
                                                    @endif </td>
                                                <td class=" text-end">
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
                                            {{ @App\SysHelper::com_curr_format($grand_total_balance, 2, '.', ',') }}</th>
                                        <th class=" text-end">
                                            {{ @App\SysHelper::com_curr_format($grand_total_0_30, 2, '.', ',') }}</th>
                                        <th class=" text-end">
                                            {{ @App\SysHelper::com_curr_format($grand_total_31_60, 2, '.', ',') }}</th>
                                        <th class=" text-end">
                                            {{ @App\SysHelper::com_curr_format($grand_total_61_90, 2, '.', ',') }}</th>
                                        <th class=" text-end">
                                            {{ @App\SysHelper::com_curr_format($grand_total_90_above, 2, '.', ',') }}</th>
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

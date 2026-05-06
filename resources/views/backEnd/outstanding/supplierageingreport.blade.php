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
                        Supplier Ageing Report
                    </h4>
                    <div class="purchase-order-content-header-right">
                        <button type="button" id="exportSupplierAgeing" class="btn btn-light me-2">
                            <i class="ico icon-outline-export text-success"></i> Export
                        </button>



                        <div class="dropdown">
                            <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ico icon-outline-hamburger-menu"></i>
                            </button>
                            <ul class="dropdown-menu" style="">

                                <li>
                                    <a href="{{ url('payables-outstanding') }}"
                                        class="dropdown-item d-flex align-items-center"><i
                                            class="ico icon-outline-document-text text-success title-15 me-2"></i>
                                        Payables Outstanding</a>
                                </li>


                            </ul>
                        </div>


                    </div>
                </div>





                <div class="card mb-3">

                    <div class="card-body">
                        @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
                            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'supplier-ageing-report', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}

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
                                <b>No Supplier Ageing Data Found!</b>
                            </div>
                        </div>
                    @else
                        <div class="table-responsive mb-4 mt-4">
                            <table id="long-list" class="table table-hover" style="border: solid 1px #e3e6f0;">
                                <thead>
                                    <tr style="background: #eeeeee; color: #000000;">
                                        <th class=" text-start" width="300px">Supplier Name</th>
                                        <th class=" text-end" width="120px">Total Balance</th>
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
                                        $total_suppliers = 0;
                                    @endphp

                                    @if (count($data_all) > 0)
                                        @foreach ($data_all as $data)
                                            <?php
                                            if(count($data)>0){
                                                $aname = $accounts->where('id', $data[0]->account_id)->first();
                                                
                                                $data_adjestment = @App\SysPurchaseReturnAdjestment::select('piv_no',DB::raw('sum(paid_amount) as paid_amount'))->wherein('piv_no',$data->pluck("transaction_no"))->groupby('piv_no')->get();
        
                                                $data_payment = DB::table('sys_payment as p')->select('pa.bi_doc_no','p.doc_number','pa.bi_amount','p.payment_through','p.payment_date','p.cheque_number','p.cheque_bank_name')
                                                ->join('sys_payment_adjustments as pa','pa.bi_doc_number','p.doc_number')->where('pa.account_id',$data[0]->account_id)->wherein('bi_doc_no',$data->pluck("transaction_no"))->where('p.status',1)->get();
                                                
                                                $data_payment2 = DB::table('sys_journalvoucher as j')->select('pa.bi_doc_no','j.doc_number','pa.bi_amount','j.doc_date')
                                                ->join('sys_payment_adjustments as pa','pa.bi_doc_number','j.doc_number')->where('pa.account_id',$data[0]->account_id)->wherein('bi_doc_no',$data->pluck("transaction_no"))->where('j.status',1)->get();

                                                $data_payment3 = DB::table('sys_journalvoucher as j')->select('ra.bi_doc_no','j.doc_number','ra.bi_amount','j.doc_date')
                                                ->join('sys_receipt_adjustments as ra','ra.bi_doc_number','j.doc_number')->where('ra.account_id',$data[0]->account_id)->wherein('bi_doc_no',$data->pluck("transaction_no"))->where('j.status',1)->get();

                                                $data_return = DB::table('sys_purchase_return as r')->select('ra.piv_no','r.doc_number','ra.paid_amount','r.doc_date')
                                                ->join('sys_purchase_return_adjestment as ra','ra.pri_no','r.doc_number')->where('r.vendors',$data[0]->account_id)->wherein('pri_no',$data->pluck("transaction_no"))->where('r.status',1)->get();
                                                
                                                // Initialize supplier totals
                                                $supplier_total_balance = 0;
                                                $supplier_total_0_30 = 0;
                                                $supplier_total_31_60 = 0;
                                                $supplier_total_61_90 = 0;
                                                $supplier_total_90_above = 0;
                                                
                                                // Process each transaction for this supplier
                                                foreach ($data as $dt) {
                                                    $paid = 0;
                                                    
                                                    // Calculate adjustments
                                                    $adjustments = $data_adjestment->where('piv_no', $dt->transaction_no)->max('paid_amount');
                                                    $paid += $adjustments;
                                                    
                                                    // Calculate payments
                                                    $bi_amount = $data_payment->where('bi_doc_no', $dt->transaction_no)->sum('bi_amount');
                                                    $paid += $bi_amount;
                                                    
                                                    $bi_amount2 = $data_payment2->where('bi_doc_no', $dt->transaction_no)->sum('bi_amount');
                                                    $paid += $bi_amount2;
                                                    
                                                    // Calculate returns (subtract)
                                                    $bi_amount3 = $data_payment3->where('bi_doc_no', $dt->transaction_no)->sum('bi_amount');
                                                    $bi_amount4 = $data_return->where('piv_no', $dt->transaction_no)->sum('paid_amount');
                                                    $paid -= ($bi_amount3 - $bi_amount4);
                                                    
                                                    // Calculate balance based on transaction type
                                                    $balance = $dt->credit_amount - abs($paid);
                                                    
                                                    // Check if row should be hidden (matches payable outstanding logic)
                                                    $is_hide2 = 0;
                                                    if (str_contains($dt->transaction_no, 'PR')) {
                                                        if ($dt->debit_amount >= $paid) {
                                                            $is_hide2 = 1;
                                                        }
                                                    }
                                                    
                                                    // Only process if row would be visible OR has debit amount
                                                    if ((($dt->credit_amount != $paid) || ($dt->debit_amount > 0)) && $is_hide2 == 0) {
                                                        // Calculate running balance like payable outstanding
                                                        if (str_contains($dt->transaction_no, 'PR')) {
                                                            if ($dt->debit_amount >= $paid) {
                                                                $supplier_total_balance -= $dt->debit_amount;
                                                            }
                                                        } else {
                                                            $supplier_total_balance += $balance;
                                                        }
                                                        
                                                        // Get ageing bucket
                                                        $DueData = @App\SysHelper::get_due_date_purchase_invoice($dt->transaction_no, $dt->transaction_date);
                                                        $ageing_bucket = isset($DueData[3]) ? $DueData[3] : 0;
                                                        
                                                        // Add to ageing bucket - ALWAYS use balance (credit_amount - abs(paid))
                                                        // This matches payable outstanding logic
                                                        if($ageing_bucket == 1) {
                                                            $supplier_total_0_30 += $balance;
                                                        } elseif($ageing_bucket == 2) {
                                                            $supplier_total_31_60 += $balance;
                                                        } elseif($ageing_bucket == 3) {
                                                            $supplier_total_61_90 += $balance;
                                                        } elseif($ageing_bucket == 4) {
                                                            $supplier_total_90_above += $balance;
                                                        }
                                                    }
                                                }
                                                
                                                // Show supplier if they have any outstanding transactions
                                                // Match payable outstanding logic: show if balance exists OR has activity
                                                if(abs($supplier_total_balance) > 0.01 || $supplier_total_0_30 != 0 || $supplier_total_31_60 != 0 || $supplier_total_61_90 != 0 || $supplier_total_90_above != 0) {
                                                    $grand_total_balance += $supplier_total_balance;
                                                    $grand_total_0_30 += $supplier_total_0_30;
                                                    $grand_total_31_60 += $supplier_total_31_60;
                                                    $grand_total_61_90 += $supplier_total_61_90;
                                                    $grand_total_90_above += $supplier_total_90_above;
                                                    $total_suppliers++;
                                            ?>
                                            <tr>
                                                <td style="cursor: pointer;"
                                                    onclick="window.open('{{ url('get-url-supplier/' . $aname->account_code) }}', '_blank')"
                                                    class=" text-start"> 
                                                    {{ $aname->account_name }}
                                                @if (@App\SysHelper::getCompanyCodeSettings()['is_supplier_code'])
                                                ({{ $aname->account_code }})
                                                    
                                                @endif
                                                </td>
                                                <td class=" text-end">
                                                    {{ @App\SysHelper::com_curr_format($supplier_total_balance, 2, '.', ',') }}
                                                </td>
                                                <td class=" text-end">
                                                    {{ @App\SysHelper::com_curr_format($supplier_total_0_30, 2, '.', ',') }}
                                                </td>
                                                <td class=" text-end">
                                                    {{ @App\SysHelper::com_curr_format($supplier_total_31_60, 2, '.', ',') }}
                                                </td>
                                                <td class=" text-end">
                                                    {{ @App\SysHelper::com_curr_format($supplier_total_61_90, 2, '.', ',') }}
                                                </td>
                                                <td class=" text-end">
                                                    {{ @App\SysHelper::com_curr_format($supplier_total_90_above, 2, '.', ',') }}
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
                                        <th class=" text-start">Grand Total ({{ $total_suppliers }} Suppliers)</th>
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
        $(document).ready(function() {
            $('#exportSupplierAgeing').on('click', function(e) {
                e.preventDefault();

                var companyName = @json(@App\SysCompany::find(session('logged_session_data.company_id') ?? '')->trade_name ?? '');
                var dateFrom = $('#from_date').length ? $('#from_date').val().trim() : '';
                var dateTo = $('#to_date').length ? $('#to_date').val().trim() : '';
                var tillDate = $('#till_date').length ? $('#till_date').val().trim() : '';

                function formatDMY(value) {
                    if (!value) return '';
                    var text = value.trim();
                    if (/^\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4}$/.test(text)) {
                        return text.replace(/-/g, '/');
                    }
                    var normalized = text.replace(/-/g, '/');
                    var parts = normalized.split('/');
                    if (parts.length === 3) {
                        if (parts[0].length === 4) {
                            return parts[2] + '/' + parts[1] + '/' + parts[0];
                        }
                        return normalized;
                    }
                    return text;
                }

                var $table = $('#long-list');
                var headerLabels = [];
                $table.find('thead th').each(function() {
                    headerLabels.push($(this).text().trim());
                });

                var rows = [];
                rows.push([companyName]);
                rows.push(['Supplier Ageing Report (' + $table.find('tbody tr').length + ')']);

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

                $table.find('tbody tr').each(function() {
                    var rowData = [];
                    $(this).find('td').each(function() {
                        rowData.push($(this).text().trim().replace(/\s+/g, ' '));
                    });
                    rows.push(rowData);
                });

                if (rows.length <= 5) {
                    alert('No data available for export');
                    return;
                }

                var N = headerLabels.length || 1;
                var workbook = new ExcelJS.Workbook();
                var worksheet = workbook.addWorksheet('Supplier Ageing');
                worksheet.columns = Array.from({ length: N }, function() { return { width: 22 }; });

                var hdrIdx = rows.indexOf(headerLabels);
                if (hdrIdx < 0) hdrIdx = rows.length - 1;

                var wsRowNum = 0;
                for (var ri = 0; ri < hdrIdx; ri++) {
                    if (!(rows[ri] && rows[ri][0])) continue;
                    wsRowNum++;
                    var wsRow = worksheet.addRow([]);
                    wsRow.height = ri === 0 ? 26 : ri === 1 ? 20 : 16;
                    if (N > 1) worksheet.mergeCells(wsRowNum, 1, wsRowNum, N);
                    var cell = wsRow.getCell(1);
                    cell.value = rows[ri][0] || '';
                    cell.font = ri === 0 ? { bold: true, size: 14 } : ri === 1 ? { bold: true, size: 12 } : { bold: true, size: 11 };
                    cell.alignment = { horizontal: 'center', vertical: 'middle' };
                }

                wsRowNum++;
                worksheet.addRow([]);

                wsRowNum++;
                var wsHdrRow = worksheet.addRow(headerLabels);
                wsHdrRow.height = 20;
                wsHdrRow.eachCell({ includeEmpty: true }, function(cell) {
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
                    wsDataRow.eachCell({ includeEmpty: true }, function(cell) {
                        cell.border = {
                            top: { style: 'thin', color: { argb: 'FFCCCCCC' } },
                            left: { style: 'thin', color: { argb: 'FFCCCCCC' } },
                            bottom: { style: 'thin', color: { argb: 'FFCCCCCC' } },
                            right: { style: 'thin', color: { argb: 'FFCCCCCC' } }
                        };
                    });
                }

                workbook.xlsx.writeBuffer().then(function(buffer) {
                    var blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                    function pad(n){ return n<10 ? ('0'+n) : n; }
                    var d = new Date();
                    var filename = 'supplier_ageing_' + pad(d.getDate()) + '-' + pad(d.getMonth()+1) + '-' + d.getFullYear() + '.xlsx';
                    saveAs(blob, filename);
                });
            });
        });
    </script>
    <?php }catch (\Exception $e) { ?> {{ $e }}
    <?php  } ?>
@endsection

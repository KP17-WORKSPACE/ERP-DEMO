@extends('backEnd.newmasterpage')
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/exceljs@4.3.0/dist/exceljs.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
@endpush
@section('mainContent')

    @php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp

    <?php try { ?>
        
<div class="content-container col-12">
    <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
        <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
            <div class="purchase-order-content-header">
                <h4 class="purchase-order-content-header-left">
                    Trading Account
                </h4>
                <div class="purchase-order-content-header-right">
                <button type="button" class="btn btn-light" id="exportTradingAccount" title="Export to Excel">
                    <i class="ico icon-outline-export text-success"></i> Export
                </button>
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ico icon-outline-hamburger-menu"></i>
                    </button>
                    <ul class="dropdown-menu" style="">
                            <li><a class="dropdown-item" href="{{url('trial-balance')}}"><i class="ico icon-outline-chart-square text-success"></i> Trial Balance</a></li>
                            <li><a class="dropdown-item" href="{{url('profit-and-loss-account')}}"><i class="ico icon-outline-chart-square text-success"></i> Profit & Loss Account</a></li>
                            <li><a class="dropdown-item" href="{{url('balancesheet')}}"><i class="ico icon-outline-chart-square text-success"></i> Balancesheet</a></li>
                    </ul>
                </div>

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
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'trading-account', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                    <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                    <div class="row">
                       <div class="col-md-2 mb-20">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label>@lang('From Date')</label>
                                        <input class="form-control date-picker" id="from_date" type="text" name="from_date" value="{{ \Carbon\Carbon::parse($from_date)->format('d/m/Y')  }}" autocomplete="off" onchange="set_filter()" >
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 mb-20">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label>@lang('To Date')</label>
                                        <input class="form-control date-picker" id="to_date" type="text" name="to_date" value="{{ \Carbon\Carbon::parse($to_date)->format('d/m/Y') }}" autocomplete="off" onchange="set_filter()" >
                                    </div>
                                </div>
                            </div>
                        </div>
                                    <div class="col-md-1 mb-20">
                    <label for="" class="form-check-label">Filter By</label>
                    <select class="form-control" name="filter_by" id="filter_by" onchange="set_filter2()">
                        <option value="" >-Select-</option>
                        <option value="this_month" @if($filter_by=="this_month") selected @endif>This Month</option>
                        <option value="today" @if($filter_by=="today") selected @endif>Today</option>
                        <option value="this_week" @if($filter_by=="this_week") selected @endif>This Week</option>
                        <option value="last_week" @if($filter_by=="last_week") selected @endif>Last Week</option>
                        <option value="last_month" @if($filter_by=="last_month") selected @endif>Last Month</option>
                        <option value="this_quarter" @if($filter_by=="this_quarter") selected @endif>This Quarter</option>
                        <option value="pre_quarter" @if($filter_by=="pre_quarter") selected @endif>Previous Quarter</option>
                        <option value="this_year" @if($filter_by=="this_year") selected @endif @if($filter_by=="") selected @endif>This Year</option>
                        <option value="last_year" @if($filter_by=="last_year") selected @endif>Last Year</option>
                    </select>
                </div>
                <script>
                    function set_filter(){
                        if($('#from_date').val()!="" || $('#to_date').val() != "")
                        {
                            $('#filter_by').val('')
                        }
                    }
                    function set_filter2(){
                        if($('#filter_by').val()!="")
                        {
                            $('#from_date').val('');
                            $('#to_date').val('');
                        }
                    }
                </script>
                <div class="col-1"><br />
                    <button type="submit" class="btn btn-light" id="btnSubmit">
                        <i class="ico icon-outline-magnifer"></i> Search
                    </button>
                </div>
                <input type="text" id="tableSearchTrialBalance" class="form-control w-25 list_style_expand_btn" style="margin: 22px 0px 0 0" placeholder="Search in List">
                    </div>
                {{ Form::close() }}
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-body">
                    <?php $total_debit=0; $total_credit=0; ?>
                <table class="table table-hover data-table" id="trading-account-table" style="border: solid 1px #e3e6f0;">
                    <thead>
                    <thead>
                      <tr>
                          <th class="border text-center" width="35%">Particular</th>
                          <th class="border text-center" width="15%">Debit</th>
                          <th class="border text-center" width="35%">Particular</th>
                          <th class="border text-center" width="15%">Credit</th>
                      </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border font-weight-bold" width="35%">Opening Stock ({{ date('d/m/Y', strtotime($from_date)) }})</td>
                            <td class="border text-end font-weight-bold text-dark" width="15%">{{ @App\SysHelper::com_curr_format($opening_stock,2,'.',',') }} @php $total_debit += $opening_stock; @endphp</td>
                            <td class="border font-weight-bold" width="35%">Sales <span class="float-right">{{ $sales }}</span></td>
                            <td class="border text-end font-weight-bold text-dark" width="15%"></td>
                        </tr>
                        <tr>
                            <td class="border font-weight-bold" width="35%">Purchase </td>
                            <td class="border text-end font-weight-bold text-dark" width="15%"><span class="float-right">{{ @App\SysHelper::com_curr_format($purchase,2,'.',',') }}</span></td>
                            <td class="border font-weight-bold" width="35%">Less: Sales Return <span class="float-right">{{ @App\SysHelper::com_curr_format($sales_return, 2, '.', '') }}</span></td>
                            <td class="border text-end font-weight-bold text-dark" width="15%">{{ @App\SysHelper::com_curr_format(abs($sales) - abs($sales_return), 2, '.', '') }} @php $total_credit += abs($sales) - abs($sales_return); @endphp</td>
                        </tr>
                        <tr>
                            <td class="border font-weight-bold" width="35%">Less: Purchase Return <span class="float-right">{{ @App\SysHelper::com_curr_format($purchase_return, 2, '.', '') }}</span></td>
                            <td class="border text-end font-weight-bold text-dark" width="15%">{{ @App\SysHelper::com_curr_format(abs($purchase) - abs($purchase_return), 2, '.', '') }} @php $total_debit += abs($purchase) - abs($purchase_return); @endphp</td>
                            <td class="border font-weight-bold" width="35%">Closing Stock  ({{ date('d/m/Y', strtotime($to_date)) }})</td>
                            <td class="border text-end font-weight-bold text-dark" width="15%">{{ @App\SysHelper::com_curr_format($closing_stock,2,'.',',') }} @php $total_credit += $closing_stock; @endphp</td>
                        </tr>


                        <tr>
                            <td class="border-right font-weight-bold" width="35%">Direct Expense</td>
                            <td class="border-right text-end font-weight-bold text-dark" width="15%"></td>
                            <td class="border-right font-weight-bold" width="35%">Direct Income</td>
                            <td class="border-right text-end font-weight-bold text-dark" width="15%"></td>
                        </tr>

                        <?php
                        if (count($subgruop2) > 0){
                            $d_exp = $subgruop2->where('sub_id',13); //Direct Expense
                            $d_inc = $subgruop2->where('sub_id',15); //Direct Income
                        } else {
                            $d_exp = [];
                            $d_inc = [];
                        }
                        ?>
                        
                        <tr>
                            <td class="border-0 align-top p-0" colspan="2">
                                @if (count($d_exp) > 0)
                                <table width="100%">
                                    @foreach ($d_exp as $dt)
                                    <tr>
                                        <td class="border-right border-top pl-2 font-weight-bold" width="70%">
                                        <a class="text-dark" data-bs-toggle="collapse" href="#collapse_1{{ $dt->id }}">{{ $dt->title }}</a></td>
                                        <td class="border-right border-top text-end font-weight-bold text-dark" width="30%">{{ @App\SysHelper::com_curr_format(($dt->total_debit - $dt->total_credit),2,'.',',') }} @php $total_debit += abs($dt->total_debit - $dt->total_credit); @endphp</td>
                                    </tr>

                                    @if (count($directIncomeExpence)>0)
                                    @foreach ($directIncomeExpence as $dt2)
                                    @if($dt2->id == $dt->id)
                                    <tr class="collapse" id="collapse_1{{ $dt->id }}">
                                        <td class="border-right border-top pl-3" width="70%">&nbsp;&nbsp;&nbsp;&nbsp;-- {{ $dt2->account_name }}</td>
                                        <td class="border-right border-top text-end" width="30%"><span class="float-right">{{ @App\SysHelper::com_curr_format(($dt2->debit_amount - $dt2->credit_amount),2,'.',',') }}</span></td>
                                    </tr>
                                    @endif                                        
                                    @endforeach                                        
                                    @endif

                                    @endforeach
                                </table>
                                @endif
                            </td>
                            <td class="border-0 align-top p-0" colspan="2">
                                @if (count($d_inc) > 0)
                                <table width="100%">
                                    @foreach ($d_inc as $dt)
                                    <tr>
                                        <td class="border-right border-top pl-2 font-weight-bold" width="70%">
                                        <a class="text-dark" data-bs-toggle="collapse" href="#collapse_1{{ $dt->id }}">{{ $dt->title }}</a></td>
                                        <td class="border-top text-end font-weight-bold text-dark" width="30%">{{ @App\SysHelper::com_curr_format(($dt->total_credit - $dt->total_debit),2,'.',',') }} @php $total_credit += abs($dt->total_credit - $dt->total_debit); @endphp</td>
                                    </tr>
                                    
                                    @if (count($directIncomeExpence)>0)
                                    @foreach ($directIncomeExpence as $dt2)
                                    @if($dt2->id == $dt->id)
                                    <tr class="collapse" id="collapse_1{{ $dt->id }}">
                                        <td class="border-right border-top pl-3" width="70%">&nbsp;&nbsp;&nbsp;&nbsp;-- {{ $dt2->account_name }}</td>
                                        <td class="border-top text-end" width="30%" ><span class="float-right">{{ @App\SysHelper::com_curr_format(($dt2->credit_amount - $dt2->debit_amount),2,'.',',') }}</span></td>
                                    </tr>
                                    @endif                                        
                                    @endforeach                                        
                                    @endif

                                    @endforeach
                                </table>
                                @endif
                            </td>
                        </tr>

                        @php $total_debit = abs($total_debit); $total_credit = abs($total_credit); @endphp
                        @if ($total_debit > $total_credit)
                        <tr>
                            <td class="border font-weight-bold"></td>
                            <td class="border text-end font-weight-bold"></td>
                            <td class="border font-weight-bold">Gross Loss c/d </td>
                            <td class="border text-end font-weight-bold text-danger">{{ @App\SysHelper::com_curr_format((abs($total_debit) - abs($total_credit)),2,'.',',') }}</td>
                        </tr>
                        <tr>
                            <td class="border font-weight-bold"></td>
                            <td class="border text-end font-weight-bold text-dark">{{ @App\SysHelper::com_curr_format($total_debit,2,'.',',') }}</td>
                            <td class="border font-weight-bold"></td>
                            <td class="border text-end font-weight-bold text-dark">{{ @App\SysHelper::com_curr_format($total_debit,2,'.',',') }}</td>
                        </tr>
                        @else
                        <tr>
                            <td class="border font-weight-bold">Gross Profit c/d </td>
                            <td class="border text-end font-weight-bold text-success">{{ @App\SysHelper::com_curr_format((abs($total_credit) - abs($total_debit)),2,'.',',') }}</td>
                            <td class="border font-weight-bold"></td>
                            <td class="border text-end font-weight-bold"></td>
                        </tr>
                        <tr>
                            <td class="border font-weight-bold"></td>
                            <td class="border text-end font-weight-bold text-dark">{{ @App\SysHelper::com_curr_format($total_credit,2,'.',',') }}</td>
                            <td class="border font-weight-bold"></td>
                            <td class="border text-end font-weight-bold text-dark">{{ @App\SysHelper::com_curr_format($total_credit,2,'.',',') }}</td>
                        </tr>
                        @endif

                    </tbody>
                  </table>
                </div>
            </div>

        </div>
    </div>
</div>
        <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

<script>
$(document).ready(function () {
    $('#exportTradingAccount').on('click', function (e) {
        e.preventDefault();

        var companyName = @json(@App\SysCompany::find(session('logged_session_data.company_id') ?? '')->trade_name ?? '');
        var fromDate = @json($from_date ? \Carbon\Carbon::parse($from_date)->format('d/m/Y') : '');
        var toDate   = @json($to_date ? \Carbon\Carbon::parse($to_date)->format('d/m/Y') : '');

        var workbook = new ExcelJS.Workbook();
        var ws = workbook.addWorksheet('Trading Account');
        ws.columns = [
            { width: 40 },
            { width: 18 },
            { width: 40 },
            { width: 18 }
        ];

        function addMetaRow(text, fontSize, bold) {
            var row = ws.addRow([text]);
            ws.mergeCells(row.number, 1, row.number, 4);
            var cell = row.getCell(1);
            cell.font      = { bold: bold || false, size: fontSize || 11 };
            cell.alignment = { horizontal: 'center', vertical: 'middle' };
        }

        addMetaRow(companyName, 14, true);
        addMetaRow('Trading Account', 12, true);
        var periodText = [];
        if (fromDate) periodText.push('From: ' + fromDate);
        if (toDate)   periodText.push('To: '   + toDate);
        if (periodText.length) addMetaRow(periodText.join('   '), 10, false);
        ws.addRow([]);

        var headerRow = ws.addRow(['Particular', 'Debit', 'Particular', 'Credit']);
        headerRow.eachCell(function (cell) {
            cell.font      = { bold: true, color: { argb: 'FFFFFFFF' } };
            cell.fill      = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF2D5496' } };
            cell.alignment = { horizontal: 'center', vertical: 'middle' };
            cell.border    = {
                top:    { style: 'thin', color: { argb: 'FFB8C4D8' } },
                left:   { style: 'thin', color: { argb: 'FFB8C4D8' } },
                bottom: { style: 'thin', color: { argb: 'FFB8C4D8' } },
                right:  { style: 'thin', color: { argb: 'FFB8C4D8' } }
            };
        });

        function cleanText(el) {
            return $(el).text().trim().replace(/\s+/g, ' ');
        }

        $('#trading-account-table tbody > tr').each(function () {
            var $tds = $(this).find('> td');

            if ($tds.length === 4) {
                // Standard 4-column row
                var row = [];
                $tds.each(function () { row.push(cleanText(this)); });
                ws.addRow(row);

            } else if ($tds.length === 2) {
                // Complex row: debit-side table (col 1) and credit-side table (col 2)
                var $debitRows  = $tds.eq(0).find('table > tbody > tr, table > tr').not('.collapse:not(.show)');
                var $creditRows = $tds.eq(1).find('table > tbody > tr, table > tr').not('.collapse:not(.show)');
                var maxLen = Math.max($debitRows.length, $creditRows.length);

                for (var i = 0; i < maxLen; i++) {
                    var row = ['', '', '', ''];
                    if (i < $debitRows.length) {
                        var dCells = $debitRows.eq(i).find('> td');
                        if (dCells.length >= 2) {
                            row[0] = cleanText(dCells.eq(0));
                            row[1] = cleanText(dCells.eq(1));
                        }
                    }
                    if (i < $creditRows.length) {
                        var cCells = $creditRows.eq(i).find('> td');
                        if (cCells.length >= 2) {
                            row[2] = cleanText(cCells.eq(0));
                            row[3] = cleanText(cCells.eq(1));
                        }
                    }
                    ws.addRow(row);
                }
            }
        });

        workbook.xlsx.writeBuffer().then(function (buffer) {
            var blob = new Blob([buffer], { type: 'application/octet-stream' });
            saveAs(blob, 'trading_account_' + (fromDate || 'all') + '_' + (toDate || 'all') + '.xlsx');
        });
    });
});
</script>
@endsection

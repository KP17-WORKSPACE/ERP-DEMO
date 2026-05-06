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

<style>
.border { border: solid 1px #e3e6f0; }
.amt-divider { float: right;
    border-left: solid 1px #dee2e6;
    padding-left: 10px;
    text-align: right;
    width: 100px;
}
</style>

    <?php try { ?>
        
<div class="content-container col-12">
    <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
        <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
            <div class="purchase-order-content-header">
                <h4 class="purchase-order-content-header-left">
                    Profit & Loss Account
                </h4>
                <div class="purchase-order-content-header-right">
                <button type="button" class="btn btn-light" id="exportProfitLoss" title="Export to Excel">
                    <i class="ico icon-outline-export text-success"></i> Export
                </button>
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ico icon-outline-hamburger-menu"></i>
                    </button>
                    <ul class="dropdown-menu" style="">
                            <li><a class="dropdown-item" href="{{url('trading-account')}}"><i class="ico icon-outline-chart-square text-success"></i> Trading Account</a></li>
                            <li><a class="dropdown-item" href="{{url('trial-balance')}}"><i class="ico icon-outline-chart-square text-success"></i> Trial Balance</a></li>
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
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'profit-and-loss-account', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                            <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                            <div class="row">
                                <div class="col-md-2 mb-20">
                                    <div class="no-gutters input-right-icon">
                                        <div class="col">
                                            <div class="input-effect">
                                                @php
                                                
                                                 $value = date('01/01/Y'); // default 01/01/currentYear in d/m/Y
                                                    if (!empty($from_date)) {
                                                        $value = date('d/m/Y', strtotime($from_date)); // convert to d/m/Y
                                                    }
                                                @endphp
                                                <label>@lang('From Date')</label>
                                                <input class="form-control date-picker" id="from_date" type="text" name="from_date" value="{{ @$value }}" autocomplete="off" onchange="set_filter()">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 mb-20">
                                    <div class="no-gutters input-right-icon">
                                        <div class="col">
                                            <div class="input-effect">
                                                @php
                                                  $value = date('d/m/Y'); // default 01/01/currentYear in d/m/Y
                                                    if (!empty($to_date)) {
                                                        $value = date('d/m/Y', strtotime($to_date)); // convert to d/m/Y
                                                    }
                                                @endphp
                                                <label>@lang('To Date')</label>
                                                <input class="form-control date-picker" id="to_date" type="text" name="to_date" value="{{ @$value }}" autocomplete="off" onchange="set_filter()">
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
                    <table class="table table-hover data-table" id="profit-loss-table">
                          <thead>
                            <tr>
                                <th class="border text-center" width="35%">Particulars</th>
                                <th class="border text-end" width="15%">Debit Amount</th>
                                <th class="border text-center" width="35%">Particulars</th>
                                <th class="border text-end" width="15%">Credit Amount</th>
                            </tr>
                          </thead>
                          <tbody>


                            <tr>
                                <td colspan="2" class="align-top p-0">

                                @if($gross_loss != 0)
                                <table width="100%">
                                    <tr>
                                        <td class="border font-weight-bold text-dark" width="70%">Gross Loss b/d</td>
                                        <td class="border font-weight-bold text-dark text-end" width="30%">{{ @App\SysHelper::com_curr_format(($gross_loss),2,'.',',') }}</td>
                                    </tr>
                                </table>
                                @endif
                                    
                                @if (count($indirect_expenses_group)>0)
                                @foreach ( $indirect_expenses_group as $data)
                                <table width="100%">
                                    <tr>
                                        <td class="border font-weight-bold text-dark" width="70%" data-bs-toggle="collapse" href="#collapse_1{{ $data->id }}">{{ $data->title }}</td>
                                        <td class="border font-weight-bold text-dark text-end" width="30%">{{ @App\SysHelper::com_curr_format(($data->debit_amount - $data->credit_amount),2,'.',',') }}</td>
                                    </tr>
                                    @php
                                        $list = $indirect_expenses_data->where('subgroup2',$data->id);
                                    @endphp
                                    @if (count($list)>0)
                                    @foreach ($list as $data2)
                                        <tr class="collapse" id="collapse_1{{ $data->id }}">
                                            <td class="border pl-2 text-success" width="70%"> --{{ $data2->account_name }}<div class="amt-divider">{{ @App\SysHelper::com_curr_format(($data2->total_debit),2,'.',',') }}</div></td>
                                            <td class="border text-end" width="30%"></td>
                                        </tr>
                                    @endforeach                                        
                                    @endif


                                </table>                                    
                                @endforeach
                                @endif
                                
                                @if($net_profit != 0)
                                <table width="100%">
                                    <tr>
                                        <td class="border font-weight-bold text-dark" width="70%">Net Profit c/d</td>
                                        <td class="border font-weight-bold text-dark text-end" width="30%">{{ @App\SysHelper::com_curr_format(($net_profit),2,'.',',') }}</td>
                                    </tr>
                                </table>
                                @endif

                                </td>
                                <td colspan="2" class="align-top p-0">

                                @if($gross_profit != 0)
                                <table width="100%">
                                    <tr>
                                        <td class="border font-weight-bold text-dark" width="70%">Gross Profit b/d</td>
                                        <td class="border font-weight-bold text-dark text-end" width="30%">{{ @App\SysHelper::com_curr_format(($gross_profit),2,'.',',') }}</td>
                                    </tr>
                                </table>
                                @endif


                                @if (count($indirect_income_group)>0)
                                @foreach ( $indirect_income_group as $data)
                                <table width="100%">
                                    <tr>
                                        <td class="border font-weight-bold text-dark" width="70%" data-bs-toggle="collapse" href="#collapse_1{{ $data->id }}">{{ $data->title }}</td>
                                        <td class="border font-weight-bold text-dark text-end" width="30%">{{ @App\SysHelper::com_curr_format(($data->credit_amount - $data->debit_amount),2,'.',',') }}</td>
                                    </tr>
                                    
                                    @php
                                        $list = $indirect_income_data->where('subgroup2',$data->id);
                                    @endphp
                                    @if (count($list)>0)
                                    @foreach ($list as $data2)
                                        <tr class="collapse" id="collapse_1{{ $data->id }}">
                                            <td class="border text-success pl-2" width="70%"> --{{ $data2->account_name }}<div class="amt-divider">{{ @App\SysHelper::com_curr_format(($data2->total_credit),2,'.',',') }}</div></td>
                                            <td class="border text-end" width="30%"></td>
                                        </tr>
                                    @endforeach                                        
                                    @endif

                                </table>                                    
                                @endforeach
                                @endif
                                
                                @if($net_loss != 0)
                                <table width="100%">
                                    <tr>
                                        <td class="border font-weight-bold text-dark" width="70%">Net Loss c/d</td>
                                        <td class="border font-weight-bold text-dark text-end" width="30%">{{ @App\SysHelper::com_curr_format(($net_loss),2,'.',',') }}</td>
                                    </tr>
                                </table>
                                @endif
                                   
                                </td>
                            </tr>
                            
                            

                            <tr>
                                <td class="border text-end font-weight-bold text-dark">Total</td>
                                <td class="border text-end font-weight-bold text-dark">{{ @App\SysHelper::com_curr_format($total_indirect_expenses, 2, '.', ',') }}</td>
                                <td class="border text-end font-weight-bold text-dark">Total</td>
                                <td class="border text-end font-weight-bold text-dark">{{ @App\SysHelper::com_curr_format($total_indirect_income, 2, '.', ',') }}</td>
                            </tr>
                            




                            {{--  delete below codes --}}
                            
                            <?php if(1==2) { ?>
                            @if($gloss==0 && $gprofit==0)
                            <tr>
                                <td colspan="2" class="align-top p-0">
                                    <table width="100%">
                                        <tr>
                                        <td class="border" width="35%">Indirect Expenses
                                            <?php if(count($indirect_expenses_list)>0){ echo "<table width=100%>";
                                                foreach($indirect_expenses_list as $list){
                                                   ?>
                                                   <tr>
                                                       <td width="70%">&nbsp;&nbsp;&nbsp;&nbsp;{{ $list['account_name'] }}</td>
                                                       <td class="text-end" width="30%">{{ @App\SysHelper::com_curr_format($list['total_debit'], 2, '.', ',') }}</td>
                                                   </tr>
                                                   <?php
                                                } echo "</table>";
                                               } ?>
                                        </td>
                                        <td class="border text-end align-top" width="15%">{{ $indirect_expenses }}
                                        </td>
                                    </tr>
                                    </table>
                                </td>
                                <td colspan="2" class="align-top p-0">
                                    <table width="100%">
                                        <tr>
                                        <td class="border" width="35%">Indirect Income
                                            <?php if(count($indirect_income_list)>0){ echo "<table width=100%>";
                                                foreach($indirect_income_list as $list){
                                                   ?>
                                                   <tr>
                                                       <td width="70%">&nbsp;&nbsp;&nbsp;&nbsp;{{ $list['account_name'] }}</td>
                                                       <td class="text-end" width="30%">{{ @App\SysHelper::com_curr_format($list['total_credit'], 2, '.', ',') }}</td>
                                                   </tr>
                                                   <?php
                                                } echo "</table>";
                                               } ?>
                                        </td>
                                        <td class="border text-end align-top" width="15%">{{ $indirect_income }} </td>
                                    </tr>
                                    </table>
                                </td>
                            </tr>
                            @endif




                            @if($gloss==0 && $gprofit!=0)
                            <tr>
                                <td colspan="2" class="align-top p-0">
                                    <table width="100%">
                                        <tr>
                                        <td class="border" width="35%">Indirect Expenses
                                            <?php if(count($indirect_expenses_list)>0){ echo "<table width=100%>";
                                                foreach($indirect_expenses_list as $list){
                                                   ?>
                                                   <tr>
                                                       <td width="70%">&nbsp;&nbsp;&nbsp;&nbsp;{{ $list['account_name'] }}</td>
                                                       <td class="text-end" width="30%">{{ @App\SysHelper::com_curr_format($list['total_debit'], 2, '.', ',') }}</td>
                                                   </tr>
                                                   <?php
                                                } echo "</table>";
                                               } ?>
                                        </td>
                                        <td class="border text-end align-top" width="15%">{{ $indirect_expenses }} 
                                        </td>
                                    </tr>
                                        <tr>
                                
                                            <td class="border" width="35%">Net Profit c/d</td>
                                            <td class="border text-end" width="15%">{{ $gprofit }} </td>
                                        </tr>
                                    </table>
                                </td>
                                <td colspan="2" class="align-top p-0">
                                    <table width="100%">
                                        <tr>
                                        <td class="border" width="35%">Indirect Income
                                            <?php if(count($indirect_income_list)>0){ echo "<table width=100%>";
                                                foreach($indirect_income_list as $list){
                                                   ?>
                                                   <tr>
                                                       <td width="70%">&nbsp;&nbsp;&nbsp;&nbsp;{{ $list['account_name'] }}</td>
                                                       <td class="text-end" width="30%">{{ @App\SysHelper::com_curr_format($list['total_credit'], 2, '.', ',') }}</td>
                                                   </tr>
                                                   <?php
                                                } echo "</table>";
                                               } ?>
                                        </td>
                                        <td class="border text-end align-top" width="15%">{{ $indirect_income }} </td>
                                    </tr>
                                    </table>
                                </td>
                            </tr>
                            @endif

                            @if($gprofit==0 && $gloss!=0)
                            <tr>
                                <td colspan="2" class="align-top p-0">
                                    <table width="100%">
                                        <tr>
                                <td class="border" width="35%">Indirect Expenses
                                    <?php if(count($indirect_expenses_list)>0){ echo "<table width=100%>";
                                     foreach($indirect_expenses_list as $list){
                                        ?>
                                        <tr>
                                            <td width="70%">&nbsp;&nbsp;&nbsp;&nbsp;{{ $list['account_name'] }}</td>
                                            <td class="text-end" width="30%">{{ @App\SysHelper::com_curr_format($list['total_debit'], 2, '.', ',') }}</td>
                                        </tr>
                                        <?php
                                     } echo "</table>";
                                    } ?>
                                </td>
                                <td class="border text-end align-top" width="15%">{{ $indirect_expenses }} </td>
                            </tr>
                                    </table>
                                </td>
                                <td colspan="2" class="align-top p-0">
                                    <table width="100%">
                                        <tr>
                                        <td class="border" width="35%">Indirect Income
                                            <?php if(count($indirect_income_list)>0){ echo "<table width=100%>";
                                                foreach($indirect_income_list as $list){
                                                   ?>
                                                   <tr>
                                                       <td>&nbsp;&nbsp;&nbsp;&nbsp;{{ $list['account_name'] }}</td>
                                                       <td class="text-end">{{ @App\SysHelper::com_curr_format($list['total_credit'], 2, '.', ',') }}</td>
                                                   </tr>
                                                   <?php
                                                } echo "</table>";
                                               } ?>
                                        </td>
                                        <td class="border text-end align-top" width="15%">{{ $indirect_income }} </td>
                                    </tr>
                                    <tr>
                                    <td class="border" width="35%">Net Loss c/d</td>
                                    <td class="border text-end" width="15%">{{ $gloss }}</td>
                                    </tr>
                                    </table>
                                </td>
                            </tr>
                            @endif

                            <?php } ?>




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
    $('#exportProfitLoss').on('click', function (e) {
        e.preventDefault();

        var companyName = @json(@App\SysCompany::find(session('logged_session_data.company_id') ?? '')->trade_name ?? '');
        var fromDate = @json(!empty($from_date) ? \Carbon\Carbon::parse($from_date)->format('d/m/Y') : '');
        var toDate   = @json(!empty($to_date) ? \Carbon\Carbon::parse($to_date)->format('d/m/Y') : '');

        var workbook = new ExcelJS.Workbook();
        var ws = workbook.addWorksheet('Profit and Loss');
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
        addMetaRow('Profit & Loss Account', 12, true);
        var periodText = [];
        if (fromDate) periodText.push('From: ' + fromDate);
        if (toDate)   periodText.push('To: '   + toDate);
        if (periodText.length) addMetaRow(periodText.join('   '), 10, false);
        ws.addRow([]);

        var headerRow = ws.addRow(['Particulars', 'Debit Amount', 'Particulars', 'Credit Amount']);
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

        $('#profit-loss-table tbody > tr').each(function () {
            var $tds = $(this).find('> td');

            if ($tds.length === 4) {
                var row = [];
                $tds.each(function () { row.push(cleanText(this)); });
                ws.addRow(row);

            } else if ($tds.length === 2) {
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
            saveAs(blob, 'profit_and_loss_' + (fromDate || 'all') + '_' + (toDate || 'all') + '.xlsx');
        });
    });
});
</script>
    
@endsection
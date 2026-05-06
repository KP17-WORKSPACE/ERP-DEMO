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
        <style>
            .bg-gray {
                background: #ababab !important;
            }
        </style>

<div class="content-container col-12">
    <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
        <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
            <div class="purchase-order-content-header">
                <h4 class="purchase-order-content-header-left">
                    Trial Balance
                </h4>
                <div class="purchase-order-content-header-right">
                    
                <button type="button" class="btn btn-light" id="exportTrialBalance" title="Export to Excel">
                    <i class="ico icon-outline-export text-success"></i> Export
                </button>
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ico icon-outline-hamburger-menu"></i>
                    </button>
                    <ul class="dropdown-menu" style="">
                            <li><a class="dropdown-item" href="{{url('trading-account')}}"><i class="ico icon-outline-chart-square text-success"></i> Trading Account</a></li>
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
{{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'trial-balance', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                                <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                                <div class="row">
                                    <div class="col-md-2 mb-20">
                                        <div class="no-gutters input-right-icon">
                                            <div class="col">
                                                <div class="input-effect">
                                                    <label>@lang('From Date')</label>
                                                    @php
                                                    $value = date('01/01/Y');
                                                    if($from_date != ""){ @$value = \Carbon\Carbon::parse($from_date)->format('d/m/Y'); }                                                   
                                                    @endphp
                                                    <input class="form-control date-picker" id="from_date" type="text" name="from_date" value="{{ @$value }}" autocomplete="off" onchange="set_filter()">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 mb-20">
                                        <div class="no-gutters input-right-icon">
                                            <div class="col">
                                                <div class="input-effect">
                                                    <label>@lang('To Date')</label>
                                                    @php
                                                    $value = date('d/m/Y');
                                                    if($to_date != "" ){ @$value =  \Carbon\Carbon::parse($to_date)->format('d/m/Y'); }
                                                    @endphp
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
                <div class="col-md-1 mb-20">
                    <label for="" class="form-check-label">Level</label>
                    <select class="form-control" name="type" id="type">
                        <option value="" >-Select-</option>
                        <option value="1" @if($type=="1") selected @endif>Type 1</option>
                        <option value="2" @if($type=="2") selected @endif>Type 2</option>
                        <option value="3" @if($type=="3") selected @endif>Type 3</option>
                        <option value="4" @if($type=="4") selected @endif>Type 4</option>
                    </select>
                </div>
                
                                    {{--  <div class="col-md-2 mb-20">
                                        <div class="input-effect">
                                            <label>@lang('Duration')</label>
                                            <select class="form-control" name="period" id="period" onchange="period_change()">
                                                <option value="">Select</option>
                                                <option value="1" @if($period==1) selected @endif>All</option>
                                                <option value="2" @if($period==2) selected @endif>Today</option>
                                                <option value="3" @if($period==3) selected @endif>This Week</option>
                                                <option value="4" @if($period==4) selected @endif>This Month</option>
                                                <option value="5" @if($period==5) selected @endif>This Quarter</option>
                                                <option value="6" @if($period==6) selected @endif>This Financial Year</option>
                                                <option value="7" @if($period==7) selected @endif>Yesterday</option>
                                                <option value="8" @if($period==8) selected @endif>Previous Month</option>
                                                <option value="9" @if($period==9) selected @endif>Previous Month to Date</option>
                                                <option value="10" @if($period==10) selected @endif>Previous Quarter</option>
                                                <option value="11" @if($period==11) selected @endif>Previous Financial Year</option>
                                                <option value="12" @if($period==12) selected @endif>Previous Financial Year to Date</option>
                                                <option value="13" @if($period==13) selected @endif>Month Start (to Date)</option>
                                                <option value="14" @if($period==14) selected @endif>Month End (from Date)</option>
                                                <option value="15" @if($period==15) selected @endif>Year Start (to Date)</option>
                                                <option value="16" @if($period==16) selected @endif>Year End (from Date)</option>
                                            </select>
                                            <script>
                                                function period_change(){
                                                    $('#btnsearch').click();
                                                }
                                            </script>
                                        </div>
                                    </div>  --}}
                <div class="col-1"><br />
                    <button type="submit" class="btn btn-light" id="btnSubmit">
                        <i class="ico icon-outline-magnifer"></i> Filter
                    </button>
                </div>
                                        <input type="text" id="tableSearchTrialBalance" class="form-control w-25 list_style_expand_btn" style="margin: 22px 0px 0 0" placeholder="Search in List">
                                </div>

                                {{ Form::close() }}
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-body">
<table class="table table-hover data-table" id="long-list" style="border: solid 1px #e3e6f0;">
                <thead>
                    <tr>
                        <th class="border text-center"></th>
                        <th class="border text-center" colspan="2">Opening</th>
                        <th class="border text-center" colspan="2">Transaction</th>
                        <th class="border text-center" colspan="2">Closing</th>
                    </tr>
                  <tr>
                      <th class="border text-center" width="30%">Particular</th>
                      <th class="border text-center" width="10%">Debit</th>
                      <th class="border text-center" width="10%">Credit</th>
                      <th class="border text-center" width="10%">Debit</th>
                      <th class="border text-center" width="10%">Credit</th>
                      <th class="border text-center" width="10%">Debit</th>
                      <th class="border text-center" width="10%">Credit</th>
                  </tr>
                </thead>
                <tbody>
                    <?php $tot = 0; $total=0; $total_dr=0; $total_cr=0; $total_cls_dr=0; $total_cls_cr=0; $total_opn_dr=0; $total_opn_cr=0; ?>
                    @foreach ($group as $dt)
                    <?php $cls_dr=0; $cls_cr=0; ?>
                    <?php $cls_dr2=0; $cls_cr2=0; ?>
                    <tr>
                        <td class="border font-weight-bold text-dark"><a class="text-dark" data-bs-toggle="collapse" href="#collapse_1{{ $dt->id }}">{{ $dt->title }}</a></td>
                        <?php $dt_open_val = App\SysHelper::get_trial_balance_opening_by_group_id($dt->id,$dt->group_id,$from_date,$to_date); ?>
                        <td class="border text-end font-weight-bold text-dark">@if($dt->group_id==1 || $dt->group_id==3){{ App\SysHelper::minus_format(@App\SysHelper::com_curr_format($dt_open_val, 2, '.', ',')) }} 
                            
                            {{--  {{ $dt->id}},{{ $dt->group_id}},{{ $from_date }},{{ $to_date }}  --}}

                            @php $cls_dr=$dt_open_val; $total_opn_dr+=$dt_open_val; @endphp @else 0.00 @endif</td>
                        <td class="border text-end font-weight-bold text-dark">@if($dt->group_id==2 || $dt->group_id==4 || $dt->group_id==5){{ App\SysHelper::minus_format(@App\SysHelper::com_curr_format($dt_open_val, 2, '.', ',')) }} 
                            
                            {{--  {{ $dt->id}},{{ $dt->group_id}},{{ $from_date }},{{ $to_date }}  --}}

                            @php $cls_cr=$dt_open_val; $total_opn_cr+=$dt_open_val; @endphp @else 0.00 @endif</td>
                        <?php $dt_val = App\SysHelper::get_trial_balance_by_group_id($dt->id,$dt->group_id,$from_date,$to_date); ?>
                        
                        <td class="border text-end font-weight-bold text-dark ">@if($dt->group_id==1 || $dt->group_id==3){{ App\SysHelper::minus_format(@App\SysHelper::com_curr_format($dt_val, 2, '.', ',')) }} @php $total_dr += $dt_val; $cls_dr+=$dt_val;  @endphp @else 0.00 @endif</td>
                        <td class="border text-end font-weight-bold text-dark ">@if($dt->group_id==2 || $dt->group_id==4 || $dt->group_id==5){{ App\SysHelper::minus_format(@App\SysHelper::com_curr_format($dt_val, 2, '.', ',')) }} 
                            
                            {{--  {{ $dt->id }}, {{ $dt->group_id }}, {{ $from_date }}, {{ $to_date }}  --}}
                            
                            @php $total_cr += $dt_val; $cls_cr+=$dt_val; @endphp @else 0.00 @endif</td>
                        <td class="border text-end font-weight-bold text-dark ">{{ App\SysHelper::minus_format(@App\SysHelper::com_curr_format($cls_dr, 2, '.', ',')) }} @php $total_cls_dr+=$cls_dr; @endphp</td>
                        <td class="border text-end font-weight-bold text-dark ">{{ App\SysHelper::minus_format(@App\SysHelper::com_curr_format($cls_cr, 2, '.', ',')) }} @php $total_cls_cr+=$cls_cr; @endphp</td>
                    </tr>
                    <?php $data2=$data->where('sub_id',$dt->id) ?>
                    @if (isset($data2))
                      @foreach ($data2 as $dt2)
                      @if ($dt2->title != "Opening Stock exe" )
                      <tr class="collapse {{ $sub1 }}" id="collapse_1{{ $dt->id }}">
                          <td class="border pl-2 font-weight-bold text-primary"><a class="text-success" data-bs-toggle="collapse" href="#collapse_sub_{{ $dt2->id }}">{{ $dt2->title }}
                        
                        </a></td>
                          <?php $dt2_open_val = App\SysHelper::get_trial_balance_opening_by_group2_id($dt2->id,$dt2->group_id,$from_date,$to_date); ?>
                          <td class="border text-end font-weight-bold text-success ">@if($dt2->group_id==1 || $dt2->group_id==3){{ App\SysHelper::minus_format(@App\SysHelper::com_curr_format($dt2_open_val, 2, '.', ',')) }} @php $cls_dr2=$dt2_open_val; @endphp @else 0.00 @endif </td>
                          <td class="border text-end font-weight-bold text-success ">@if($dt2->group_id==2 || $dt2->group_id==4 || $dt2->group_id==5){{ App\SysHelper::minus_format(@App\SysHelper::com_curr_format($dt2_open_val, 2, '.', ',')) }} @php $cls_cr2=$dt2_open_val; @endphp @else 0.00 @endif </td>
                          <?php $dt2_val = App\SysHelper::get_trial_balance_by_group2_id($dt2->id,$dt2->group_id,$from_date,$to_date); ?>
                          <td class="border text-end font-weight-bold text-success ">@if($dt2->group_id==1 || $dt2->group_id==3){{ App\SysHelper::minus_format(@App\SysHelper::com_curr_format($dt2_val, 2, '.', ',')) }} @php $cls_dr2+=$dt2_val; @endphp @else 0.00 @endif </td>
                          
                          <td class="border text-end font-weight-bold text-success ">@if($dt2->group_id==2 || $dt2->group_id==4 || $dt2->group_id==5){{ App\SysHelper::minus_format(@App\SysHelper::com_curr_format($dt2_val, 2, '.', ',')) }} 
                            
                            {{--  {{ $dt2->id }}, {{ $dt2->group_id }}, {{ $from_date }}, {{ $to_date }} = {{ $dt2->group_id }}  --}}

                            @php $cls_cr2+=$dt2_val; @endphp @else 0.00 @endif </td>
                          
                          <td class="border text-end font-weight-bold text-success ">{{ App\SysHelper::minus_format(@App\SysHelper::com_curr_format($cls_dr2, 2, '.', ',')) }}</td>
                          <td class="border text-end font-weight-bold text-success ">{{ App\SysHelper::minus_format(@App\SysHelper::com_curr_format($cls_cr2, 2, '.', ',')) }}</td>
                      </tr>
                      @endif
                      
                      {!! App\SysHelper::get_trial_balance_items($dt2->id,$from_date,$to_date,$sub2,$sub3) !!}

                      @endforeach
                      @endif
                    @endforeach

                </tbody>
                <thead>
                  <tr>
                      <th class="border text-center font-weight-bold text-dark">Total</th>
                      <th class="border text-end font-weight-bold @if($total_opn_dr!=$total_opn_cr) text-danger @else text-dark @endif">{{ @App\SysHelper::com_curr_format($total_opn_dr, 2, '.', ',') }}</th>
                      <th class="border text-end font-weight-bold @if($total_opn_dr!=$total_opn_cr) text-danger @else text-dark @endif">{{ @App\SysHelper::com_curr_format($total_opn_cr, 2, '.', ',') }}</th>
                      <th class="border text-end font-weight-bold @if($total_dr!=$total_cr) text-danger @else text-dark @endif">{{ @App\SysHelper::com_curr_format($total_dr, 2, '.', ',') }}</th>
                      <th class="border text-end font-weight-bold @if($total_dr!=$total_cr) text-danger @else text-dark @endif">{{ @App\SysHelper::com_curr_format($total_cr, 2, '.', ',') }}</th>
                      {{--  App\SysHelper::minus_format(@App\SysHelper::com_curr_format($total_cr, 2, '.', ''))  --}}
                      <th class="border text-end font-weight-bold @if($total_cls_dr!=$total_cls_cr) text-danger @else text-dark @endif">{{ @App\SysHelper::com_curr_format($total_cls_dr, 2, '.', ',') }}</th>
                      <th class="border text-end font-weight-bold @if($total_cls_dr!=$total_cls_cr) text-danger @else text-dark @endif">{{ @App\SysHelper::com_curr_format($total_cls_cr, 2, '.', ',') }}</th>
                  </tr>
                  <tr>
                    @php
                    $value = date('d/m/Y');
                    if($to_date != "" ){ @$value =  \Carbon\Carbon::parse($to_date)->format('d/m/Y'); }
                    @endphp

                    <th colspan="7">Closing Stock ({{ $value }}) : {{ App\SysHelper::com_curr_format($closing_stock, 2, '.', ',') }}</th>
                  </tr>
                </thead>
              </table>
                </div>
            </div>

        </div>
    </div>
</div>


    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

{{--  {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'trialbalance-search', 'method' => 'GET', 'enctype' => 'multipart/form-data']) }}
{{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'trialbalance-search', 'method' => 'GET', 'enctype' => 'multipart/form-data']) }}
<input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">  --}}
                            
    <script type="text/javascript">
        var sttr1 = window.location.pathname;
        if (sttr1.indexOf("search") >= 0) {

        } else {
            $(window).on('load', function() {
                //$('#trialbalance_search_popup_win').modal('show');
            });
        }
    </script>

    <script>
        $(document).ready(function () {
            $('#exportTrialBalance').on('click', function (e) {
                e.preventDefault();

                var companyName = @json(@App\SysCompany::find(session('logged_session_data.company_id') ?? '')->trade_name ?? '');
                var fromDate = @json($from_date ? \Carbon\Carbon::parse($from_date)->format('d/m/Y') : '');
                var toDate = @json($to_date ? \Carbon\Carbon::parse($to_date)->format('d/m/Y') : '');

                var workbook = new ExcelJS.Workbook();
                var ws = workbook.addWorksheet('Trial Balance');
                ws.columns = [
                    { width: 40 },
                    { width: 18 },
                    { width: 18 },
                    { width: 18 },
                    { width: 18 },
                    { width: 18 },
                    { width: 18 }
                ];

                function addMetaRow(text, fontSize, bold) {
                    var row = ws.addRow([text]);
                    var rowNumber = row.number;
                    ws.mergeCells(rowNumber, 1, rowNumber, 7);
                    var cell = row.getCell(1);
                    cell.font = { bold: bold || false, size: fontSize || 11 };
                    cell.alignment = { horizontal: 'center', vertical: 'middle' };
                }

                addMetaRow(companyName, 14, true);
                addMetaRow('Trial Balance', 12, true);
                var periodText = [];
                if (fromDate) periodText.push('From: ' + fromDate);
                if (toDate) periodText.push('To: ' + toDate);
                if (periodText.length) {
                    addMetaRow(periodText.join('   '), 10, false);
                }
                ws.addRow([]);

                var headerRow = ws.addRow(['Particular', 'Opening Debit', 'Opening Credit', 'Transaction Debit', 'Transaction Credit', 'Closing Debit', 'Closing Credit']);
                headerRow.eachCell(function (cell) {
                    cell.font = { bold: true, color: { argb: 'FFFFFFFF' } };
                    cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF2D5496' } };
                    cell.alignment = { horizontal: 'center', vertical: 'middle' };
                    cell.border = {
                        top: { style: 'thin', color: { argb: 'FFB8C4D8' } },
                        left: { style: 'thin', color: { argb: 'FFB8C4D8' } },
                        bottom: { style: 'thin', color: { argb: 'FFB8C4D8' } },
                        right: { style: 'thin', color: { argb: 'FFB8C4D8' } }
                    };
                });

                $('#long-list tbody tr').each(function () {
                    var $row = $(this);
                    var cells = $row.find('td');
                    if (cells.length !== 7) {
                        return;
                    }
                    var rowData = [];
                    cells.each(function () {
                        var text = $(this).text().trim().replace(/\s+/g, ' ');
                        rowData.push(text);
                    });
                    ws.addRow(rowData);
                });

                $('#long-list thead tr').slice(-2).each(function () {
                    var cells = $(this).find('th');
                    if (cells.length === 0) {
                        return;
                    }
                    var rowData = [];
                    cells.each(function () {
                        var text = $(this).text().trim().replace(/\s+/g, ' ');
                        rowData.push(text);
                    });
                    ws.addRow(rowData);
                });

                workbook.xlsx.writeBuffer().then(function (buffer) {
                    var blob = new Blob([buffer], { type: 'application/octet-stream' });
                    saveAs(blob, 'trial_balance_' + (fromDate || 'all') + '_' + (toDate || 'all') + '.xlsx');
                });
            });
        });
    </script>
@endsection

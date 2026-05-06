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

<script>
    function set_total_val2(step_id, total_value)
    {
        $("#lbl2_"+step_id).text(total_value);
    }
    function set_total_val(step_id, total_value)
    {
        $("#lbl_"+step_id).text(total_value);
    }
</script>
<style>
.amt-divider { float: right;
    border-left: solid 1px #dee2e6;
    padding-left: 10px;
    text-align: right;
    width: 100px;
}
</style>

@php $disp=0; @endphp

    <?php try { ?>
        
<div class="content-container col-12">
    <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
        <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
            <div class="purchase-order-content-header">
                <h4 class="purchase-order-content-header-left">
                    Balancesheet
                </h4>
                <div class="purchase-order-content-header-right">
                <button type="button" class="btn btn-light" id="exportBalanceSheet" title="Export to Excel">
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
                            <li><a class="dropdown-item" href="{{url('trial-balance')}}"><i class="ico icon-outline-chart-square text-success"></i> Trial Balance</a></li>
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
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'balancesheet', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                            <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                            <div class="row">
                                <div class="col-md-2 mb-20">
                                    <div class="no-gutters input-right-icon">
                                        <div class="col">
                                            <div class="input-effect">
                                                <label>@lang('From Date')</label>
                                               @php
                                                    $value = date('01/01/Y'); // default 01/01/currentYear in d/m/Y
                                                    if (!empty($from_date)) {
                                                        $value = date('d/m/Y', strtotime($from_date)); // convert to d/m/Y
                                                    }
                                                @endphp
                                                <input class="form-control date-picker" id="from_date" type="text" name="from_date" value="{{ @$value }}" autocomplete="off" onchange="set_filter()">
                                                @if ($errors->has('from_date'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('from_date') }}</strong>
                                                    </span>
                                                @endif
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
                                                
                                                $value = date('d/m/Y'); // default 01/01/currentYear in d/m/Y
                                                    if (!empty($to_date)) {
                                                        $value = date('d/m/Y', strtotime($to_date)); // convert to d/m/Y
                                                    }
                                               
                                               @endphp
                                                <input class="form-control date-picker" id="to_date" type="text" name="to_date" value="{{ @$value }}" autocomplete="off" onchange="set_filter()">
                                                @if ($errors->has('to_date'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('to_date') }}</strong>
                                                    </span>
                                                @endif
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
                    <table class="table table-hover data-table" id="balance-sheet-table">
                            <thead>
                              <tr>
                                  <th class="border text-center" width="35%">Liabilities</th>
                                  <th class="border text-end" width="15%">Amount</th>
                                  <th class="border text-center" width="35%">Assets</th>
                                  <th class="border text-end" width="15%">Amount</th>
                              </tr>
                            </thead>
                            <tbody>
                                @php $d=0; @endphp
                                @if (count($sub_group)>0)
                              <tr>
                                <td colspan="2" class="p-0 align-top">
                                    @php $liabilities = $sub_group->wherein('group_id',[2,5]); @endphp
                                    @if (count($liabilities)>0)
                                        @foreach ($liabilities as $sub)
                                        <table width="100%">
                                            <tr>
                                                <td class="border font-weight-bold text-success" width="70%" data-bs-toggle="collapse" href="#collapse_1{{ $sub->id }}">{{ $sub->title }}</td>
                                                <td class="border font-weight-bold text-success text-end" width="30%">
                                                    @if ($sub->id == 10)
                                                        {{ @App\SysHelper::com_curr_format((abs($sub->amount) + abs($net_profit) - abs($net_loss) + abs($net_profit_till) - abs($net_loss_till)),2,'.','') }}
                                                    @else
                                                        {{ @App\SysHelper::com_curr_format(($sub->amount),2,'.',',') }}
                                                    @endif
                                                </td>
                                            </tr>
                                            @php $liab_sub2 = $sub_group_2->where('sub_id',$sub->id); @endphp
                                            @if (count($liab_sub2)>0)
                                                @foreach ($liab_sub2 as $sub2)
                                                    <tr class="collapse" id="collapse_1{{ $sub->id }}">
                                                        <td class="border font-weight-bold" width="70%" data-bs-toggle="collapse" href="#collapse_2{{ $sub2->id }}">&nbsp;&nbsp;-- {{ $sub2->title }}</td>
                                                        <td class="border font-weight-bold text-end" width="30%">
                                                            @if ($sub2->id == 16)
                                                                {{ @App\SysHelper::com_curr_format(abs($sub2->amount + (abs($net_profit) - abs($net_loss))+ (abs($net_profit_till) - abs($net_loss_till))),2,'.','') }}
                                                            @else
                                                                {{ @App\SysHelper::com_curr_format(($sub2->amount),2,'.',',') }}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @php $liab_accounts = $accounts->where('subgroup2',$sub2->id); @endphp

                                                    @if (count($liab_accounts)>0)
                                                        @foreach ($liab_accounts as $account)
                                                            @if (str_contains($account->account_name, "Profit & Loss A/c"))
                                                                @if ($net_profit != 0)
                                                                <tr class="collapse" id="collapse_2{{ $sub2->id }}">
                                                                    <td class="border" width="70%">&nbsp;&nbsp;&nbsp;&nbsp;-- {{ $account->account_name }}<span class="float-right text-success">{{ @App\SysHelper::com_curr_format($net_profit,2,'.',',') }}</span></td>
                                                                    <td class="border text-end font-weight-bold text-success" width="30%"></td>
                                                                </tr>
                                                                @else
                                                                <tr class="collapse" id="collapse_2{{ $sub2->id }}">
                                                                    <td class="border" width="70%">&nbsp;&nbsp;&nbsp;&nbsp;-- {{ $account->account_name }}<span class="float-right text-danger">-{{ @App\SysHelper::com_curr_format($net_loss,2,'.',',') }}</span></td>
                                                                    <td class="border text-end font-weight-bold text-danger" width="30%"></td>
                                                                </tr>
                                                                @endif
                                                            @elseif (str_contains($account->account_name, "General Reserve"))
                                                            <tr class="collapse" id="collapse_2{{ $sub2->id }}">
                                                                <td class="border" width="70%">&nbsp;&nbsp;&nbsp;&nbsp;-- {{ $account->account_name }}<div class="amt-divider">{{ @App\SysHelper::com_curr_format(($account->amount + $net_profit_till - $net_loss_till),2,'.',',') }}</div></td>
                                                                <td class="border text-end" width="30%"></td>
                                                            </tr>
                                                            @else
                                                            <tr class="collapse" id="collapse_2{{ $sub2->id }}">
                                                                <td class="border" width="70%">&nbsp;&nbsp;&nbsp;&nbsp;-- {{ $account->account_name }}<div class="amt-divider">{{ @App\SysHelper::com_curr_format(($account->amount),2,'.',',') }}</div></td>
                                                                <td class="border text-end" width="30%"></td>
                                                            </tr>
                                                            @endif
                                                        @endforeach

                                                        @if($isPL==0 && $d==0 && $sub2->id==16)
                                                            @if ($net_profit != 0)
                                                                <tr class="collapse" id="collapse_2{{ $sub2->id }}">
                                                                    <td class="border" width="70%">&nbsp;&nbsp;&nbsp;&nbsp;-- Profit & Loss A/c (Reserve)<span class="float-right text-success">{{ @App\SysHelper::com_curr_format($net_profit,2,'.',',') }}</span></td>
                                                                    <td class="border text-end font-weight-bold text-success" width="30%"></td>
                                                                </tr>
                                                                @else
                                                                <tr class="collapse" id="collapse_2{{ $sub2->id }}">
                                                                    <td class="border" width="70%">&nbsp;&nbsp;&nbsp;&nbsp;-- Profit & Loss A/c (Reserve)<span class="float-right text-danger">-{{ @App\SysHelper::com_curr_format($net_loss,2,'.',',') }}</span></td>
                                                                    <td class="border text-end font-weight-bold text-danger" width="30%">-</td>
                                                                </tr>
                                                            @endif
                                                            @php $d = 1; @endphp
                                                        @endif

                                                    @endif
                                                @endforeach
                                            @endif
                                        </table>                                        
                                        @endforeach
                                    @endif
                                </td>
                                <td colspan="2" class="p-0 align-top">
                                    @php $assets = $sub_group->wherein('group_id',[1]); @endphp
                                    @if (count($assets)>0)
                                        @foreach ($assets as $sub)
                                        <table width="100%">
                                            <tr>
                                                <td class="border font-weight-bold text-success" width="70%" data-bs-toggle="collapse" href="#collapse_1{{ $sub->id }}">{{ $sub->title }}</td>
                                                @if($sub->title == "Current Assets")
                                                <td class="border font-weight-bold text-success text-end" width="30%">{{ @App\SysHelper::com_curr_format(($sub->amount+$stock),2,'.',',') }}</td>
                                                @else
                                                <td class="border font-weight-bold text-success text-end" width="30%">{{ @App\SysHelper::com_curr_format(($sub->amount),2,'.',',') }}</td>
                                                @endif

                                            </tr>
                                            @php $asset_sub2 = $sub_group_2->where('sub_id',$sub->id); @endphp
                                            @if (count($asset_sub2)>0)
                                                @foreach ($asset_sub2 as $sub2)
                                                
                                                
                                                @if($sub->title == "Current Assets" && $disp == 0)
                                                <tr class="collapse" id="collapse_1{{ $sub->id }}">
                                                    <td class="border font-weight-bold" width="70%">&nbsp;-- Closing Stock</td>
                                                    <td class="border font-weight-bold text-end" width="30%">{{ @App\SysHelper::com_curr_format(($stock),2,'.',',') }}</td>
                                                </tr>
                                                @php $disp=1; @endphp
                                                @endif


                                                @if($sub2->title != "Closing Stock")
                                                    <tr class="collapse" id="collapse_1{{ $sub->id }}">
                                                        <td class="border font-weight-bold" width="70%" data-bs-toggle="collapse" href="#collapse_2{{ $sub2->id }}">&nbsp;-- {{ $sub2->title }}</td>
                                                        <td class="border font-weight-bold text-end" width="30%">{{ @App\SysHelper::com_curr_format(($sub2->amount),2,'.',',') }}</td>
                                                    </tr>
                                                    @php $asset_accounts = $accounts->where('subgroup2',$sub2->id); @endphp
                                                    @if (count($asset_accounts)>0)
                                                        @foreach ($asset_accounts as $account)
                                                            <tr class="collapse" id="collapse_2{{ $sub2->id }}">
                                                                <td class="border" width="70%">&nbsp;&nbsp;&nbsp;&nbsp;-- {{ $account->account_name }}<div class="amt-divider">{{ @App\SysHelper::com_curr_format(($account->amount),2,'.',',') }}</div></td>
                                                                <td class="border text-end" width="30%"></td>
                                                            </tr>                                                
                                                        @endforeach                                                
                                                    @endif
                                                @endif
                                                
                                                @endforeach
                                            @endif
                                        </table>                                        
                                        @endforeach
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                
                                <td class="border text-end font-weight-bold">Total Amount</td>
                                <td class="border text-end font-weight-bold @if ($liability_sum == $asset_sum) text-primary @else text-danger @endif" width="200px">{{ @App\SysHelper::com_curr_format($liability_sum, 2, '.', ',') }}</td>
                                <td class="border text-end font-weight-bold">Total Amount</td>
                                <td class="border text-end font-weight-bold @if ($liability_sum == $asset_sum) text-primary @else text-danger @endif" width="200px">{{ @App\SysHelper::com_curr_format($asset_sum, 2, '.', ',') }}</td>
                            </tr> 
                            @endif
                            </tbody>
                        </table>


                               
                        @if(1==2)
                            <div class="row"> 
                                <div class="col-md-6 p-0 pb-0 mb-0">

                                    <table class="table" style="border: solid 1px #e3e6f0;">
                                        <thead>
                                            <tr>
                                                <th class="border text-center">Liabilities</th>
                                                <th class="border text-center" width="200px">Amount</th>
                                            </tr>
                                        </thead>                          
                                        <tbody>

                                            



                                                
                                                    <?php $data_2 = $data2->where('group_id', 2); $totval2=0.00; $tot_dr=0.00; ?>
                                                    @foreach ($data_2 as $dt2)
                                                    <tr>
                                                        <td class="border pl-4"><a class="text-dark" data-bs-toggle="collapse" href="#collapsel1{{ $dt2->id }}">{{ $dt2->title }}</a></td>
                                                        <td class="border"><span class="float-right font-weight-bold text-md"><label class="m-0 p-0 text-primary" id="lbl2_{{ $dt2->id }}"></label></span></td>
                                                    </tr>
                                                
                                                        <?php $data_3 = $data3->where('sub_id', $dt2->id); ?>
                                                        @foreach ($data_3 as $dt3)
                                                        
                                                        {{--  Suppliers id is 6  --}}
                                                        @if($dt3->id==6)
                                                        <tr class="collapse" id="collapsel1{{ $dt2->id }}">
                                                            <td class="border pl-4">
                                                                <a data-bs-toggle="collapse" href="#collapsea_supp">&nbsp;&nbsp;&nbsp;&nbsp;{{ $dt3->title }}</a></td>
                                                            <td class="border"><span class="float-right font-bold">{!! App\SysHelper::get_supplier_total_for_balance_sheet() !!}</span></td>
                                                        </tr>
                                                        {!! App\SysHelper::get_supplier_for_balance_sheet() !!}
                                                        <?php $totval2 += App\SysHelper::get_supplier_total_for_balance_sheet(); ?>

                                                        {{--  Share Holder Fund id is 14  --}}
                                                        @elseif($dt3->id==31)
                                                        <tr class="collapse" id="collapsel1{{ $dt2->id }}">
                                                            <td class="border pl-4">
                                                                <a data-bs-toggle="collapse" href="#collapsel_partners_capital">&nbsp;&nbsp;&nbsp;&nbsp;{{ $dt3->title }}</a></td>
                                                            <td class="border"><span class="float-right font-bold">{!! trim(App\SysHelper::get_account_balance_by_group2_id($dt3->id,$from_date,$to_date), '-') !!}</span></td>
                                                        </tr>
                                                        {!! App\SysHelper::get_share_holder_fund_for_balance_sheet($from_date,$to_date) !!}
                                                        <?php $totval2 += trim(App\SysHelper::get_account_balance_by_group2_id($dt3->id,$from_date,$to_date), '-'); ?>

                                                        {{--  Share Holder Fund id is 14  --}}
                                                        @elseif($dt3->id==32)
                                                        
                                                        <?php $pl = App\SysHelper::get_gross_profit_for_balance_sheet($from_date,$to_date); ?>

                                                        <tr class="collapse" id="collapsel1{{ $dt2->id }}">
                                                            <td class="border pl-4">
                                                                <a data-bs-toggle="collapse" href="#collapsel_gross_profit">&nbsp;&nbsp;&nbsp;&nbsp;{{ $dt3->title }}</a></td>
                                                            <td class="border"><span class="float-right font-bold">{{ $pl["profit"] }}</span></td>
                                                        </tr>

                                                        @if($pl["profit"] != 0)
                                                            {!! $pl["data"] !!}
                                                        @endif                                                        
                                                        <?php $totval2 += $pl["profit"]; ?>

                                                        @else
                                                        <tr class="collapse" id="collapsel1{{ $dt2->id }}">
                                                            <td class="border pl-4">&nbsp;&nbsp;&nbsp;&nbsp;{{ $dt3->title }}</td>
                                                            <td class="border">&nbsp;</td>
                                                        </tr>
                                                        @endif

                                                        @endforeach

                                                        @php $totval2 = @App\SysHelper::com_curr_format($totval2, 2, '.', ''); $tot_dr += $totval2; @endphp
                                                        <script>
                                                            set_total_val2({{ $dt2->id }}, "{{ $totval2 }}");
                                                        </script>
                                                        <?php $totval2=0.00;?>

                                                    @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-6 p-0 pb-0 mb-0">
                                    <table class="table" style="border: solid 1px #e3e6f0;">
                                        <thead>
                                            <tr>
                                                <th class="border text-center">Assets</th>
                                                <th class="border text-center" width="200px">Amount</th>
                                            </tr>
                                        </thead>                          
                                        <tbody>
                                                
                                            <?php $data_2 = $data2->where('group_id', 1); $totval=0.00; $tot_cr=0.00; ?>
                                            @foreach ($data_2 as $dt2)
                                            <tr>
                                                <td class="border pl-4"><a data-bs-toggle="collapse" href="#collapsea2{{ $dt2->id }}">{{ $dt2->title }}</a></td>
                                                <td class="border"><span class="float-right font-weight-bold text-md"><label class="m-0 p-0 text-primary" id="lbl_{{ $dt2->id }}"></label></span></td>
                                            </tr>

                                                <?php $data_3 = $data3->where('sub_id', $dt2->id); ?>
                                                @foreach ($data_3 as $dt3)
                                                
                                                {{--  Stock id is 3  --}}
                                                @if($dt3->id==3)
                                                <tr class="collapse" id="collapsea2{{ $dt2->id }}">
                                                    <td class="border pl-4">
                                                        <a data-bs-toggle="collapse" href="#collapsea_sto">&nbsp;&nbsp;&nbsp;&nbsp;{{ $dt3->title }}</a></td>
                                                    <td class="border"><span class="float-right font-bold">{!! App\SysHelper::get_stock_total_for_balance_sheet() !!}</span></td>
                                                </tr>
                                                {!! App\SysHelper::get_stock_for_balance_sheet() !!}
                                                <?php $totval += App\SysHelper::get_stock_total_for_balance_sheet(); ?>


                                                {{--  Sundry Debtors id is 4  --}}
                                                @elseif($dt3->id==4)
                                                <tr class="collapse" id="collapsea2{{ $dt2->id }}">
                                                    <td class="border pl-4">
                                                        <a data-bs-toggle="collapse" href="#collapsea_cust">&nbsp;&nbsp;&nbsp;&nbsp;{{ $dt3->title }}</a></td>
                                                    <td class="border"><span class="float-right font-bold">{!! App\SysHelper::get_customer_total_for_balance_sheet() !!}</span></td>
                                                </tr>
                                                {!! App\SysHelper::get_customer_for_balance_sheet() !!}
                                                <?php $totval += App\SysHelper::get_customer_total_for_balance_sheet(); ?>
                                                
                                                {{--  Cash in Hand id is 15  --}}
                                                @elseif($dt3->id==15)
                                                <tr class="collapse" id="collapsea2{{ $dt2->id }}">
                                                    <td class="border pl-4">
                                                        <a data-bs-toggle="collapse" href="#collapsea_cash_in_hand">&nbsp;&nbsp;&nbsp;&nbsp;{{ $dt3->title }}</a></td>
                                                    <td class="border"><span class="float-right font-bold">{!! App\SysHelper::get_account_balance_by_group2_id($dt3->id,$from_date,$to_date) !!}</span></td>
                                                </tr>
                                                {!! App\SysHelper::get_cash_in_hand_for_balance_sheet($from_date,$to_date) !!}
                                                <?php $totval += App\SysHelper::get_account_balance_by_group2_id($dt3->id,$from_date,$to_date); ?>
                                                
                                                {{--  Cash at Bank id is 16  --}}
                                                @elseif($dt3->id==16)
                                                <tr class="collapse" id="collapsea2{{ $dt2->id }}">
                                                    <td class="border pl-4">
                                                        <a data-bs-toggle="collapse" href="#collapsea_cash_at_bank">&nbsp;&nbsp;&nbsp;&nbsp;{{ $dt3->title }}</a></td>
                                                    <td class="border"><span class="float-right font-bold">{!! App\SysHelper::get_account_balance_by_group2_id($dt3->id,$from_date,$to_date) !!}</span></td>
                                                </tr>
                                                {!! App\SysHelper::get_cash_at_bank_for_balance_sheet($from_date,$to_date) !!}
                                                <?php $totval += App\SysHelper::get_account_balance_by_group2_id($dt3->id,$from_date,$to_date); ?>
                                                
                                                @else
                                                <tr class="collapse" id="collapsea2{{ $dt2->id }}">
                                                    <td class="border pl-4">&nbsp;&nbsp;&nbsp;&nbsp;{{ $dt3->title }}</td>
                                                    <td class="border">&nbsp;</td>
                                                </tr>
                                                @endif


                                                @endforeach

                                                @php $totval = @App\SysHelper::com_curr_format($totval, 2, '.', ''); $tot_cr += $totval; @endphp
                                                <script>
                                                    set_total_val({{ $dt2->id }}, "{{ $totval }}");
                                                </script>
                                                <?php $totval=0.00;?>

                                            @endforeach
                                        </tbody>
                                    </table>



                                </div>

                                <table class="table" style="border: solid 1px #e3e6f0;">
                                    <tr>
                                        <td class="border text-end font-weight-bold">Total Amount</td>
                                        <td class="border text-end font-weight-bold text-primary" width="200px">{{ @App\SysHelper::com_curr_format($tot_dr, 2, '.', '') }}</td>
                                        <td class="border text-end font-weight-bold">Total Amount</td>
                                        <td class="border text-end font-weight-bold text-primary" width="200px">{{ @App\SysHelper::com_curr_format($tot_cr, 2, '.', '') }}</td>
                                    </tr>                                    
                                </table>
                            </div>
                            @endif
                </div>
            </div>

        </div>
    </div>
</div>

    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

<script>
$(document).ready(function () {
    $('#exportBalanceSheet').on('click', function (e) {
        e.preventDefault();

        var companyName = @json(@App\SysCompany::find(session('logged_session_data.company_id') ?? '')->trade_name ?? '');
        var fromDate = @json(!empty($from_date) ? \Carbon\Carbon::parse($from_date)->format('d/m/Y') : '');
        var toDate   = @json(!empty($to_date) ? \Carbon\Carbon::parse($to_date)->format('d/m/Y') : '');

        var workbook = new ExcelJS.Workbook();
        var ws = workbook.addWorksheet('Balance Sheet');
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
        addMetaRow('Balance Sheet', 12, true);
        var periodText = [];
        if (fromDate) periodText.push('From: ' + fromDate);
        if (toDate)   periodText.push('To: '   + toDate);
        if (periodText.length) addMetaRow(periodText.join('   '), 10, false);
        ws.addRow([]);

        var headerRow = ws.addRow(['Liabilities', 'Amount', 'Assets', 'Amount']);
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

        var $table = $('#balance-sheet-table');
        if ($table.length === 0) {
            $table = $('#long-list');
        }

        $table.find('tbody > tr').each(function () {
            var $tds = $(this).find('> td');

            if ($tds.length === 4) {
                var row = [];
                $tds.each(function () { row.push(cleanText(this)); });
                ws.addRow(row);
            } else if ($tds.length === 2) {
                var $liabilityRows = $tds.eq(0).find('table > tbody > tr, table > tr').not('.collapse:not(.show)');
                var $assetRows     = $tds.eq(1).find('table > tbody > tr, table > tr').not('.collapse:not(.show)');
                var maxLen = Math.max($liabilityRows.length, $assetRows.length);

                for (var i = 0; i < maxLen; i++) {
                    var row = ['', '', '', ''];
                    if (i < $liabilityRows.length) {
                        var lCells = $liabilityRows.eq(i).find('> td');
                        if (lCells.length >= 2) {
                            row[0] = cleanText(lCells.eq(0));
                            row[1] = cleanText(lCells.eq(1));
                        }
                    }
                    if (i < $assetRows.length) {
                        var aCells = $assetRows.eq(i).find('> td');
                        if (aCells.length >= 2) {
                            row[2] = cleanText(aCells.eq(0));
                            row[3] = cleanText(aCells.eq(1));
                        }
                    }
                    ws.addRow(row);
                }
            }
        });

        workbook.xlsx.writeBuffer().then(function (buffer) {
            var blob = new Blob([buffer], { type: 'application/octet-stream' });
            saveAs(blob, 'balance_sheet_' + (fromDate || 'all') + '_' + (toDate || 'all') + '.xlsx');
        });
    });
});
</script>
    
@endsection
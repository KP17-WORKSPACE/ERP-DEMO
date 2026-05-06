@extends('backEnd.masterpage')
@section('mainContent')

<?php try { ?>
<div class="container-fluid">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h2 class="page-heading m-0">Sales Report by User</h2>
            <span class="page-label">Home - Sales Report by User</span>
        </div>
        
    </div>
    <div >
                    <div class="card shadow mb-4 p-4">
                        
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deals-sales-report-list', 'method' => 'POST', 'id' => 'crm-deals-sales-report']) }}
                    <input type="hidden" name="company_id" id="company_id" value="{{ session('logged_session_data.company_id') }}" />
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <label for="" class="form-check-label">Owner</label>
                                <select class="form-control js-example-basic-single" name="owner_id" id="owner_id">
                                    <option value="">-Select-</option>
                                    @foreach ($staff as $value)
                                    <option value="{{ @$value->user_id }}" @if($ctrl_owner ==$value->user_id) selected @endif>{{ @$value->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="" class="form-check-label">From Date</label>
                                <input class="form-control datepicker" id="date" type="date" autocomplete="off" name="date" value="{{ $ctrl_date }}" required onchange="set_filter()">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="" class="form-check-label">To Date</label>
                                <input class="form-control" id="date2" type="date" autocomplete="off" name="date2" value="{{ $ctrl_date2 }}" required onchange="set_filter()">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="" class="form-check-label">Filter By</label>
                                <select class="form-control" name="filter_by" id="filter_by">
                                    <option value="" @if($filter_by == "") selected @endif>-Select-</option>
                                    <option value="this_month" @if($filter_by == "this_month") selected @endif>This Month</option>
                                    <option value="today" @if($filter_by == "today") selected @endif>Today</option>
                                    <option value="this_week" @if($filter_by == "this_week") selected @endif>This Week</option>
                                    <option value="last_week" @if($filter_by == "last_week") selected @endif>Last Week</option>                                    
                                    <option value="last_month" @if($filter_by == "last_month") selected @endif>Last Month</option>
                                    <option value="this_quarter" @if($filter_by == "this_quarter") selected @endif>This Quarter</option>
                                    <option value="pre_quarter" @if($filter_by == "pre_quarter") selected @endif>Previous Quarter</option>
                                    <option value="this_year" @if($filter_by == "this_year") selected @endif>This Year</option>
                                    <option value="last_year" @if($filter_by == "last_year") selected @endif>Last Year</option>
                                </select>
                            </div>
                            <script>
                                function set_filter(){
                                if($('#date').val()!="" || $('#date2').val() != "")
                                {
                                    $('#filter_by').val('')
                                }
                                }
                            </script>

                            <div class="col-2 mb-2"><br />
                                <button type="submit" class="btn btn-primary" id="btnSubmit">Filter</button>
                            </div>
                        </div>
                    {{ Form::close() }}
                    </div>
    </div>
    <style>
        .div_over_flow_hide{ text-overflow: ellipsis; white-space: nowrap; overflow: hidden; }
        .div_over_flow_show{ text-overflow: ellipsis; white-space: wrap; overflow: auto; word-wrap: break-word; }
    </style>
    <script>
        function changeClass(element, className) {
          element.className = className;
        }
      </script>
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        @if(session()->has('message-success') != "" || session()->get('message-danger') != "")
                                        <tr>
                                            <td colspan="7">
                                                @if(session()->has('message-success'))
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
                                            <th>@lang('Company')</th>
                                            <th>@lang('Deal ID')</th>
                                            <th>@lang('Invoice')</th>
                                            <th>@lang('Date')</th>
                                            <th>@lang('Company Name')</th>
                                            <th>@lang('Deal Name')</th>
                                            {{-- <th>@lang('Payment Terms')</th> --}}
                                            <th style="display: none;">@lang('Accounts')</th>
                                            <th style="display: none;">@lang('Sales')</th>
                                            <th>@lang('Purchase')</th>
                                            <th>@lang('Invoice')</th>
                                            <th>@lang('Delivery')</th>
                                            <th>@lang('Receivables')</th>
                                            <th class="text-right">@lang('Invoice Value')</th>
                                            <th class="text-right">@lang('GP')</th>
                                            <th class="text-right">@lang('GP %')</th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php $deal_value=0; $deal_value_total=0; $deal_profit=0; $deal_profit_total=0; ?>
                                        @foreach($deals1 as $value)
                                <tr>
                                    <td><div style="width:200px;" class="div_over_flow_hide" onmouseover="changeClass(this, 'div_over_flow_show')" 
                                        onmouseout="changeClass(this, 'div_over_flow_hide')">{{ @$value->company->company_name }}</div></td>
                                    <td><a href="{{url('crm-deal-track-approval/'.$value->trackid)}}" target="_blank">{{@$value->code}}</a></td>
                                    <td><a href="{{url('get-url-sales-invoice/'.$value->doc_number)}}" target="_blank">{{@$value->doc_number}}</a></td>
                                    <td>{{date('d/m/Y', strtotime(@$value->doc_date))}}</td>
                                    <td>
                                        <div style="width:200px;" class="div_over_flow_hide" onmouseover="changeClass(this, 'div_over_flow_show')" 
                                        onmouseout="changeClass(this, 'div_over_flow_hide')">{{@$value->customername->name}}</div>
                                    </td>
                                    <td>{{@$value->deal_code->deal_name}}</td>                                    
                                    {{-- <td><div style="width:100px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{{@$value->title}}</div></td> --}}
                                    <td style="display: none;">
                                            @if($value->accounts==1)
                                            <span class="success btn-badge py-1 px-2">Accounts Approved</span>
                                            @elseif($value->accounts==2)
                                            <span class="danger btn-badge py-1 px-2">Accounts Rejected</span>
                                            @elseif($value->accounts==3)
                                            <span class="primary btn-badge py-1 px-2">Accounts Pending</span>
                                            @else
                                            <span class="warning btn-badge py-1 px-2">Pending</span>
                                            @endif
                                        </td>
                                        <td style="display: none;">
                                            @if($value->sales==1)
                                            <span class="success btn-badge py-1 px-2">Sales Approved</span>
                                            @elseif($value->sales==2)
                                            <span class="danger btn-badge py-1 px-2">Sales Rejected</span>
                                            @elseif($value->sales==3)
                                            <span class="primary btn-badge py-1 px-2">Sales Pending</span>
                                            @else
                                            <span class="warning btn-badge py-1 px-2">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($value->purchease_approval==0)
                                                <span class="info btn-badge py-1 px-2">Not Applicable</span>
                                            @else
                                                @if($value->purchease==1)
                                                <span class="success btn-badge py-1 px-2">Purchase Approved</span>
                                                @elseif($value->purchease==2)
                                                <span class="danger btn-badge py-1 px-2">Purchase Rejected</span>
                                                @elseif($value->purchease==3)
                                                <span class="primary btn-badge py-1 px-2">Purchase Pending</span>
                                                @elseif($value->purchease==4)
                                                <span class="primary btn-badge py-1 px-2">Partial Delivery</span>
                                                @else
                                                <span class="warning btn-badge py-1 px-2">Pending</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if($value->invoice_approval==0)
                                                <span class="info btn-badge py-1 px-2">Not Applicable</span>
                                            @else
                                                @if($value->invoice==1)
                                                <span class="success btn-badge py-1 px-2">Invoice Approved</span>
                                                @elseif($value->invoice==2)
                                                <span class="danger btn-badge py-1 px-2">Invoice Rejected</span>
                                                @elseif($value->invoice==3)
                                                <span class="primary btn-badge py-1 px-2">Invoice Pending</span>
                                                @else
                                                <span class="warning btn-badge py-1 px-2">Pending</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if($value->delivery_approval==0)
                                                <span class="info btn-badge py-1 px-2">Not Applicable</span>
                                            @else
                                                @if($value->delivery==1)
                                                <span class="success btn-badge py-1 px-2">Delivery Completed</span>
                                                @elseif($value->delivery==2)
                                                <span class="danger btn-badge py-1 px-2">Delivery Rejected</span>
                                                @elseif($value->delivery==3)
                                                <span class="primary btn-badge py-1 px-2">Out For Delivery</span>
                                                @elseif($value->delivery==4)
                                                <span class="primary btn-badge py-1 px-2">Pending For Delivery</span>
                                                @elseif($value->delivery==5)
                                                <span class="primary btn-badge py-1 px-2">Ready For Delivery</span>
                                                @elseif ($value->delivery == 6)
                                                <span class="primary btn-badge py-1 px-2">Partial Delivery</span>
                                                @else
                                                <span class="warning btn-badge py-1 px-2">Pending</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if($value->receivables_approval==0)
                                                <span class="info btn-badge py-1 px-2">Not Applicable</span>
                                            @else
                                                @if($value->receivables==1)
                                                <span class="success btn-badge py-1 px-2">Payment Received</span>
                                                @elseif($value->receivables==2)
                                                <span class="danger btn-badge py-1 px-2">Rejected</span>
                                                @elseif($value->receivables==3)
                                                <span class="primary btn-badge py-1 px-2">Payment Pending</span>
                                                @elseif($value->receivables==4)
                                                <span class="dark btn-badge py-1 px-2">Order Cancelled</span>
                                                @else
                                                <span class="warning btn-badge py-1 px-2">Pending</span>
                                                @endif
                                            @endif
                                        </td>
                                        <?php
                                        $deal_value=@App\SysHelper::get_aed_amount_new($value->deal_currency,$value->deal_value);
                                        $deal_profit=@App\SysHelper::get_aed_amount_new($value->deal_currency,$value->deal_profit);
                                        $deal_percentage= round($deal_profit/$deal_value*100,2);
                                        $gp=($value->total_taxableamount-$value->deal_discount)*$deal_percentage/100;
                                        ?>
                                    <td class="text-right">

                                        @if ($value->source=="Fulfillment")
                                        ({{ @App\SysHelper::com_curr_format(@App\SysHelper::get_aed_amount_new($value->deal_currency,$value->deal_value), 2, '.', ',') }})&nbsp;&nbsp;&nbsp;
                                        @endif
                                        
                                        @php 
                                        /*if ($value->is_partial_invoice==1){
                                            $aed=@App\SysHelper::get_deal_value(abs($value->partial_invoice_amount),$value->source,$value->deal_currency,$value->deal_percent,$value->cust_id); 
                                        } else {
                                            $aed=@App\SysHelper::get_deal_value($value->deal_value,$value->source,$value->deal_currency,$value->deal_percent,$value->cust_id); 
                                        }*/
                                        if ($value->is_partial_invoice==1){
                                            $aed=@App\SysHelper::get_aed_amount_new($value->currency, $value->partial_invoice_amount);
                                        } else {
                                            $aed=@App\SysHelper::get_aed_amount_new($value->currency, $value->total_taxableamount - $value->deal_discount);
                                        }
                                        @endphp
                                        {{ @App\SysHelper::com_curr_format($aed, 2, '.', ',') }} <?php $deal_value_total += $aed; ?>
                                    </td>
                                    <td class="text-right">
                                        {{ @App\SysHelper::com_curr_format($gp,2,'.',',') }}
                                        <?php $deal_profit_total += $gp; ?></td>
                                    <td class="text-right">@if($value->deal_profit != 0){{ round($deal_percentage,2)  }}% @else 0% @endif</td>
                                    <td class="text-right">
                                        {{ @$value->currency_name->code }}</td>
                                    <td class="text-right">
                                        <a class="btn-sm btn-info" href="{{url('crm-deal-track-approval/'.$value->trackid)}}" target="_blank"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                    </td>
                                    
                                </tr>
                                  
                                @endforeach
                                    </tbody>
                                    <tfoot>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th class="text-right">Total: </th>
                                        <th class="text-right">{{ @App\SysHelper::com_curr_format($deal_value_total, 2, '.', ',') }}</th>
                                        <th class="text-right">{{ @App\SysHelper::com_curr_format($deal_profit_total, 2, '.', ',') }}</th>
                                        <th></th>
                                        <th class="text-right"></th>
                                        <th></th>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
<?php
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
?>


<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

@endsection
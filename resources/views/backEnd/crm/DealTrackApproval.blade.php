@extends('backEnd.masterpage')
@section('mainContent')
    @php
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp

<?php try { ?>

    <style>
        .head {font-size: 14px;}
        .card h2{font-size: 14px;}
        .card h4{font-size: 14px;}
        .card h5{font-size: 14px;}
        .card h6{font-size: 12px;}
        .card p{font-size: 11px;}
        .card span{font-size: 11px;}
        .card b{font-size: 11px;}
        .modal-body h4{font-size: 17px;}
        .table th, .table td { padding: 1px; font-size: 12px; }
    </style>

  <div class="container-fluid mb-1">
      <div class="d-flex justify-content-between align-items-center">
          <div class="mb-1">
              <h2 class="page-heading m-0" title="{{ $del->id }}">Deal Approval (Deal ID - {{ $del->deal_code->code }})</h2>
              <span class="page-label">Home - Deal - Deal Track Approval  - {{ $del->companyname->company_name }}</span>
          </div>
          <div>
              <a href="{{ url('crm-deals/'.$del->id.'/view') }}" target="_blank" type="button" class="btn btn-primary">View Deal</a>
              <a href="{{ url()->previous() }}" type="button" class="btn btn-warning"><i class="fa fa-arrow-left"></i> Back</a>
              <!-- Input with Search -->
              <div style="float: left; margin-right:5px; position: relative; width: 200px;">
                  <input type="text" id="quick_search_doc_number" placeholder="Deal ID" class="form-control pr-4" /> 
                  <span style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); color: #aaa; pointer-events: none;">
                  <i class="fas fa-search"></i>
                  </span>
              </div>
              <script>
                  const baseUrl = "{{ url('get-url-deal-track') }}";                
                  document.getElementById('quick_search_doc_number').addEventListener('keypress', function(e) {
                      if (e.key === 'Enter') {
                          var val = this.value.trim();
                          if (val !== '') {                                
                              window.location.href = baseUrl + '/' + val;
                          }
                      }
                  });
              </script>
              <!-- Input with Search -->
          </div>
      </div>

      <div class="row">
        <div class="col-lg-3 mb-1 p-1">
            <div class="p-3 card h-100">
                <h2 class="head">Deal Info {!! App\SysHelper::deal_type_new($del->isproject) !!}
                </h2>
                <p class="mb-1 text-white-100 text-uppercase">Deal Name: {{ $del->deal_name }}</p>

                @if($del->tags != "")
                    <p class="mb-1">Brand : 
                    <?php $myArray = explode(',', $del->tags); ?>
                    @foreach ($myArray as $item)
                    {{ $item }}
                    @endforeach </p>    
                @endif

                <span class="mb-1">Deal Value : {{ App\SysHelper::currancy_format_deal($del->deal_value,$del->company_id) }} {{ $del->dealcurrency->code }}</span>
                <span class="mb-1">Profit Amount : {{ App\SysHelper::currancy_format_deal(($del->deal_profit),$del->company_id) }} {{ $del->dealcurrency->code }}
                    <?php
                    $dealvalue = $del->deal_value;
                    $dealprofit = $del->deal_profit;
                    if($dealprofit!=0 && $dealvalue != 0){ $dealpercentage = $dealprofit / $dealvalue * 100; }
                    else{ $dealpercentage=0; }
                    ?>
                    <a class="btn-xs @if($dealpercentage < 0) btn-danger @else btn-success @endif p-0 pl-1 pr-1">{{ @App\SysHelper::com_curr_format($dealpercentage,2,'.',',') }}%</a>
                </span>
                @if ($del->estimated_close_date != '')
                <span class="mb-1">Estimated Close Date : {{ date('m/d/Y', strtotime($del->estimated_close_date)) }}</span>
                @endif
                <div class="text-capitalize">Stage : <b class="">
                    @if($del->stage==1) <span class="btn-warning btn-badge py-1 px-2">Prospecting</span> @endif
                    @if($del->stage==2) <span class="btn-success btn-badge py-1 px-2">Quote</span> @endif
                    @if($del->stage==3) <span class="btn-info btn-badge py-1 px-2">Closure</span> @endif
                    @if($del->stage==4) 
                    <?php
                    $data = App\SysHelper::deal_track_status($del->id);
                    $color = "btn-danger";
                    if($data=="Pending"){
                        $color = "btn-warning";
                    } else if($data=="completed"){
                        $color = "btn-primary";                                            
                    } else if($data=="OnProcess"){
                        $color = "btn-info";                                            
                    } else{
                        $color = "btn-danger";
                    }
                    ?>
                    @if($data!="completed")
                    <span class="btn-primary btn-badge py-1 px-2">Won</span>@endif
                    
                    @if(App\SysHelper::set_track($del->id)==1)
                        <span class="{{ $color }} btn-badge py-1 px-2" >
                        @if($data=="Fulfill")<span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>@endif {{ $data }} </span>
                    @endif
                        
                    @endif
                    @if($del->stage==5) <span class="btn-danger btn-badge py-1 px-2">Lost</span> @endif
                    @if($del->stage==6) <span class="btn-dark btn-badge py-1 px-2">Cancelled</span> @endif
                </b>
              </div>
            </div>
        </div>
        <div class="col-lg-3 mb-1 p-1">
            <div class="p-3 card h-100">
                <h2 class="head">Sales Person Info</h2>
                <h6 class="sub-head text-capitalize text-dark">{{ $del->ownername->first_name }} {{ $del->ownername->middle_name }} {{ $del->ownername->last_name }}</h6>
                <p class="mb-1 text-gray-800">Added On : {{ date('d/m/Y H:i:s', strtotime(@$del->created_at)) }} @if ($del->source != '') | Source : {{ $del->source }}
                    @if ($del->source_o != '') - {{ $del->source_o }} @endif @endif
            </p>
                <span class="mb-1"> <span class="font-semibold">Mob No :</span> {{ $del->ownername->mobile }}</span>
                <span class="mb-1"><span class="font-semibold">Mail :</span> {{ $del->ownername->email }}</span>
                <span class="mb-1"><span class="font-semibold">Ext No :</span> {{ $del->ownername->ext_no ?? '--' }}</span>
            </div>
        </div>
        <div class="col-lg-3 mb-1 p-1">
            <div class="p-3 card h-100">
                <h2 class="head p-0 m-0">Customer Info
                    @if (App\SysHelper::get_company_status($del->customername) == 0)
                    <a class="btn p-0 pr-1 pl-1 btn-danger float-right" target="_blank" href="{{url('customer-edit', $del->customername->id)}}">Update Info</a>
                    @else
                
                    @endif
                </h2>

                <h6 class="sub-head text-capitalize text-dark p-0 m-0">
                    <span class="pt-0 pb-0 pl-1 pr-1 text-sm"
                    @if($del->customername->type==1) style="background: #228c22; color: #ffffff;" @endif
                    @if($del->customername->type==2) style="background: #FFA500; color: #ffffff;" @endif
                    @if($del->customername->type==3) style="background: #FF0000; color: #ffffff;" @endif
                    @if($del->customername->type==4) style="background: #000000; color: #ffffff;" @endif>
                    
                    <a href="{{url('view-customer')}}/{{@$del->customername->id}}" class="text-white" target="_blank">
                    {{ $del->customername->name }}</a>
                    
                    </span>
                    @if(Auth::user()->role_id==1 || session('logged_session_data.designation_id')==8 || session('logged_session_data.designation_id')==2)
                        <a class="btn text-info m-0 p-0" onclick="upcolordiv()" title="Edit Color"><i class="fa fa-edit pb-2" aria-hidden="true"></i></a>
                    @endif
                    <div class="border border-primary rounded bg-white text-sm p-2" id="div_update_color" style="display: none;">
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-customer-color', 'method' => 'POST', 'id' => 'color_customer_form']) }}
                        Change Color :
                        <select class="dynamicstxt w-50" name="edit_color" id="edit_color" required>
                            <option value="1">Green</option>
                            <option value="2">Orange</option>
                            <option value="3">Red</option>
                            <option value="4">Black</option>
                        </select>
                        <input type="hidden" name="color_customer_id" value="{{ $del->customername->id }}" />
                        <button id="btn_edit_color" type="submit" class="btn btn-xs btn-primary text-xs pt-0 pb-0">Change</button>
                        
                      {{ Form::close() }}
                    </div>
                    <script>
                        function upcolordiv() {
                            $("#div_update_color").css("display", "block");
                        }
                    </script>
                
                
                </h6>

                    <span class="mb-1"> <span class="font-semibold">Contact :</span> {{ $del->cust_name }}</span>
                    <span class="mb-1"> <span class="font-semibold">Designation :</span> {{ $del->designation }}</span>
                    <span class="mb-1"><span class="font-semibold">M :</span> {{ $del->cust_no }} | <span class="font-semibold">E :</span> {{ $del->cust_email }}</span>
                    <p class="mb-2 text-gray-800">Add: {{ $del->address }}</p>


            </div>
        </div>

        <div class="col-lg-3 mb-1 p-1">
        <div class="p-3 card ">
            <div class="d-flex justify-content-between align-items-center mb-0">
                <h2 class=head>Delivery Location /Address</h2>
            </div>
            <?php /*@if (isset($addressbook))
            <div class="row">
                <div class="col-3"> <b> Company </b></div>
                <span class="col-9">: {{ $addressbook->customername->name }}</span>
            </div>
            <div class="row">
                <div class="col-3"> <b>Contact Person</b></div>
                <span class="col-9">: {{ $addressbook->contact_person }}</span>
            </div>
            <div class="row">
                <div class="col-3"> <b>Mob Num</b></div>
                <span class="col-9">: {{ $addressbook->contact_number }}</span>
            </div>
            <div class="row">
                <div class="col-3"> <b>Email </b></div>
                <span class="col-9">: {{ $addressbook->contact_email }}</span>
            </div>
            <div class="row">
                <div class="col-3"> <b>Address </b></div>
                <span class="col-9">: {{ $addressbook->address }}</span>
            </div>
            @else */ ?>
            <div class="row">
                <div class="col-3"> <b> Company </b></div>
                <span class="col-9">: {{ $del->delivery_company }}</span>
            </div>
            <div class="row">
                <div class="col-3"> <b>Name</b></div>
                <span class="col-9">: {{ $del->delivery_name }}</span>
            </div>
            <div class="row">
                <div class="col-3"> <b>Mob</b></div>
                <span class="col-9">: {{ $del->delivery_number }}</span>
            </div>
            <div class="row">
                <div class="col-3"> <b>Email </b></div>
                <span class="col-9">: {{ $del->delivery_email }}</span>
            </div>

            <div class="row">
                <div class="mb-1 col-3"> <b>Address</b></div>
                <span class="col-9">: @if($del->delivery_address1 != "") {{ $del->delivery_address1 }} @else {{ $del->address }} @endif
                    @if($del->delivery_address2 != ""), {{ $del->delivery_address2 }}@endif
                    @if($del->delivery_city != ""), {{ $del->delivery_city }}@endif
                    @if($del->delivery_state != ""), {{ $del->state->name }}@endif
                    @if($del->delivery_country != ""), {{ $del->country->name }}@endif
                    @if($del->delivery_zip_code != ""), {{ $del->delivery_zip_code }}@endif
                </span>
            </div>

            

            <?php /* @endif */ ?>

        </div> 
        </div>


    </div>
    
    <div class="row">
    <div class="col-lg-12 h-100 mb-1 p-1">
        @if (count($quoteitems) > 0)
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header p-3">
                        <h4 class="header-title m-0 p-0" style="float: left;">Quote items</h4>

                        {{--  <a class="btn-small" href="{{url('crm-quote/'.$deal->deal_id.'/downloadwp/'.$del->quote_id)}}"> <i class="fa fa-download"></i>Quotation</a>  --}}

                        <a class="btn-small" style="float: right;" href="{{url('crm-quote/'.$deal->deal_id.'/downloadev/'.$del->quote_id)}}"> <i class="fa fa-download"></i>VAT Excluded</a>
                        <a class="btn-small mr-1" style="float: right;" href="{{url('crm-quote/'.$del->id.'/download/'.$del->quote_id)}}"><i class="fa fa-download" aria-hidden="true"></i> Download Quotation</a>
                        
                        <?php $list_po = App\SysPurchaseOrder::select('id','doc_number')->where('deal_id',$deal->deal_id)->get(); ?>
                        @if (count($list_po)>0)
                        @foreach ($list_po as $po)
                            <a class="btn-sm btn-primary mr-1" style="float: right;" href="{{url('purchase-order/'.$po->id.'/print')}}"> <i class="fa fa-download"></i>{{ $po->doc_number }}</a>
                        @endforeach                            
                        @endif

                    </div>
                    <div class="card-body p-0 pl-3 pr-3 pb-2">
                        <div class="table-responsive table-bordered">
                            <table class="table table-nowrap table-centered mb-0 table-striped">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Part Number</th>
                                        <th>Description</th>
                                        <th class="text-center">Delivery</th>
                                        <th class="text-right">Cost</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-right">Unit Price</th>
                                        <th class="text-right">@lang('Value')</th>
                                        <th class="text-right">Discount</th>
                                        <th class="text-right">@lang('Taxable Amount')</th>
                                        <th class="text-right">@lang('VAT Amount')</th>
                                        <th class="text-right">Total</th>
                                    </tr>
                                </thead>
                                <?php $t_qty = 0; $t_value=0; $t_deli=0; $t_discount=0; $t_taxableamount=0; $t_vatamount=0; $t_price = 0; $t_discount = 0; $t_net_amount = 0; $t_cost=0;
                                $vat =$quoteitems->max('vat'); $deal_discount_sum_amount=0;?>
                                
                                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-update-quote-sort-order', 'method' => 'POST', 'id' => 'crm-update-quote-sort-order']) }}
                                <tbody>
                                    @foreach ($quoteitems as $Item)
                                    @php
                                        $value = $Item->price * $Item->qty;
                                        $taxableamount = $value - $Item->discount;
                                        $vatamount = $taxableamount * $Item->vat / 100;
                                        $deli = App\SysHelper::get_deal_delivery_qty($Item->id);

                                        $t_cost += $Item->cost * $Item->qty;
                                        $t_deli += $deli;
                                        $t_qty += $Item->qty;
                                        $t_value += $value;
                                        $t_discount += $Item->discount;
                                        $t_taxableamount += $taxableamount;
                                        $t_vatamount += $vatamount;
                                    @endphp
                                    <tr>
                                        <td>
                                            <input type="text" name="sort_id[]" value="{{ $Item->sort_id }}" style="width: 35px; border: solid 1px #cccccc;">
                                            <input type="hidden" class="form-control" name="item_id[]" value="{{ $Item->id }}">
                                        </td>
                                        <td><?php try{ ?> {{ $Item->productname->part_number }} <?php }catch (\Exception $e){} ?></td>{{--  nl2br($Item->description)  --}}
                                        <td><div id="desc_{{ $Item->id }}" onclick="toggle_tool_tip({{ $Item->id }})" style="width:350px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{!! $Item->description !!}</div></td>
                                        <td class="text-center">{{ $deli }}</td>
                                        <td class="text-right">{{ $Item->cost }}</td>
                                        <td class="text-center">{{ $Item->qty }}</td>
                                        <td class="text-right">{{ App\SysHelper::currancy_format($Item->price,$Item->currency_id) }}</td>
                                        <td class="text-right">{{ App\SysHelper::currancy_format($value,$Item->currency_id) }}</td>
                                        <td class="text-right">{{ App\SysHelper::currancy_format($Item->discount,$Item->currency_id) }}</td>
                                        <td class="text-right">{{ App\SysHelper::currancy_format($taxableamount,$Item->currency_id) }}</td>
                                        <td class="text-right">{{ App\SysHelper::currancy_format($vatamount,$Item->currency_id) }}</td>
                                        <td class="text-right">{{ App\SysHelper::currancy_format(($taxableamount + $vatamount),$Item->currency_id) }}</td>
                                    </tr>
                                    <?php $currency_id = $Item->currency_id; ?>
                                    @endforeach
                                </tbody>
                                <thead>
                                    <tr>
                                        <th><button class="btn-sm btn-danger p-0 m-0" type="submit">Update</button></th>
                                        <th></th>
                                        <th></th>
                                        <th class="text-center">{{ $t_deli }}</th>
                                        <th class="text-right">{{ App\SysHelper::currancy_format($t_cost,$currency_id) }}</th>
                                        <th class="text-center">{{ $t_qty }}</th>
                                        <th></th>
                                        <th class="text-right">{{ App\SysHelper::currancy_format($t_value,$currency_id) }}</th>
                                        <th class="text-right">{{ App\SysHelper::currancy_format($t_discount,$currency_id) }}</th>
                                        <th class="text-right">{{ App\SysHelper::currancy_format($t_taxableamount,$currency_id) }}</th>
                                        <th class="text-right">{{ App\SysHelper::currancy_format($t_vatamount,$currency_id) }}</th>
                                        <th class="text-right">{{ $Item->currency->code }} {{ App\SysHelper::currancy_format($t_taxableamount+$t_vatamount,$currency_id) }}</th>
                                    </tr>
                                    @if($del->deal_discount > 0)
                                    <tr>
                                        <?php
                                        $deal_discount_taxable_amount = $del->deal_discount;
                                        $deal_discount_vat_amount = $del->deal_discount*($vat)/100;
                                        $deal_discount_sum_amount = $deal_discount_taxable_amount+$deal_discount_vat_amount;
                                        ?>
                                        <th colspan="8" class="text-right font-weight-bold">Aditional Discount</th>
                                        <th class="text-right">{{ App\SysHelper::currancy_format(($del->deal_discount), $currency_id) }}</th>
                                        <th class="text-right">{{ App\SysHelper::currancy_format(($deal_discount_taxable_amount), $currency_id) }}</th>
                                        <th class="text-right">{{ App\SysHelper::currancy_format(($deal_discount_vat_amount), $currency_id) }}</th>
                                        <th class="text-right">{{ $Item->currency->code }} {{ App\SysHelper::currancy_format(($deal_discount_sum_amount), $currency_id) }}</th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th class="text-right">{{ App\SysHelper::currancy_format($t_cost,$currency_id) }}</th>
                                        <th class="text-center">{{ $t_qty }}</th>
                                        <th></th>
                                        <th class="text-right">{{ App\SysHelper::currancy_format($t_value,$currency_id) }}</th>
                                        <th class="text-right">{{ App\SysHelper::currancy_format($t_discount+$del->deal_discount,$currency_id) }}</th>
                                        <th class="text-right">{{ App\SysHelper::currancy_format($t_taxableamount-$deal_discount_taxable_amount, $currency_id) }}</th>
                                        <th class="text-right">{{ App\SysHelper::currancy_format($t_vatamount-$deal_discount_vat_amount, $currency_id) }}</th>                              
                                        <th class="text-right">{{ $Item->currency->code }} {{ App\SysHelper::currancy_format(($t_taxableamount+$t_vatamount-$deal_discount_sum_amount), $currency_id) }}</th>
                                    </tr>
                                    @endif 
                                </thead>
                                {{ Form::close() }}


                                <tbody>
                                    @if (count($poitems)>0)
                                    <?php $po_sum = 0; ?>
                                    <tr>
                                        <th colspan="12"><b>Aditional Items (Purchase Order)</b></th>
                                    </tr>
                                    @foreach ($poitems as $Item)
                                    <tr>
                                        <td></td>
                                        <td>{{ $Item->partno }}</td>
                                        <td>{{ $Item->description }}</td>
                                        <td class="text-center">0</td>
                                        <td class="text-right">{{ @App\SysHelper::com_curr_format($Item->unitprice,2,'.',',') }}</td>
                                        <td class="text-center">{{ $Item->qty }}</td>
                                        <td class="text-right">0.00</td>
                                        <td class="text-right">0.00</td>
                                        <td class="text-right">0.00</td>
                                        <td class="text-right">0.00</td>
                                        <td class="text-right">0.00</td>
                                        <td class="text-right">0.00</td>
                                    </tr>
                                    <?php $po_sum += $Item->unitprice*$Item->qty; ?>
                                    @endforeach
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th class="text-center">0</th>
                                        <th class="text-right">{{ @App\SysHelper::com_curr_format($po_sum,2,'.',',') }}</th>
                                        <th class="text-center">{{ $poitems->sum('qty') }}</th>
                                        <th class="text-right"></th>
                                        <th class="text-right"></th>
                                        <th class="text-right"></th>
                                        <th class="text-right"></th>
                                        <th class="text-right"></th>
                                        <th class="text-right"></th>
                                    </tr>
                                    @endif
                                </tbody>

                                <tbody>
                                    @if (count($dnitems)>0)
                                    <tr>
                                        <th colspan="12"><b>Aditional Items (Delivery Note)</b></th>
                                    </tr>
                                    @foreach ($dnitems as $Item)
                                    <tr>
                                        <td></td>
                                        <td>{{ $Item->partno }}</td>
                                        <td>{{ $Item->description }}</td>
                                        <td class="text-center">0</td>
                                        <td class="text-right">{{ $Item->taxableamount }}</td>
                                        <td class="text-center">{{ $Item->qty }}</td>
                                        <td class="text-right">0.00</td>
                                        <td class="text-right">0.00</td>
                                        <td class="text-right">0.00</td>
                                        <td class="text-right">0.00</td>
                                        <td class="text-right">0.00</td>
                                        <td class="text-right">0.00</td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th class="text-center">0</th>
                                        <th class="text-right">{{ @App\SysHelper::com_curr_format($dnitems->sum('taxableamount'),2,'.',',') }}</th>
                                        <th class="text-center">{{ $dnitems->sum('qty') }}</th>
                                        <th class="text-right"></th>
                                        <th class="text-right"></th>
                                        <th class="text-right"></th>
                                        <th class="text-right"></th>
                                        <th class="text-right"></th>
                                        <th class="text-right"></th>
                                    </tr>
                                    @endif
                                </tbody>


                                
                            </table>
                            <br />
                            <table class="table table-nowrap table-centered mb-0 table-striped">
                                <thead>
                                    <tr>
                                        <th style="width:350px;" class="text-left">Selling Exp Account</th>
                                        <th style="width:350px;" class="text-left">Credit Account</th>
                                        <th style="width:150px;" class="text-right">Amount</th>
                                        <th class="text-left pl-5">Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>                                    
                                @if(count($quote_charges) > 0)
                                @foreach ($quote_charges as $charges)
                                <tr>
                                    <td class="text-left">{{ $charges->sellingexpaccount->account_name }}</td>
                                    <td class="text-left">{{ $charges->creditaccount->account_name }}</td>
                                    <td class="text-right">{{ $charges->amount }}</td>
                                    <td class="text-left pl-5">{{ $charges->remarks }}</td>
                                </tr>
                                @endforeach
                                @endif
                                
                            @if(count($list_journalvoucher_det)>0)
                            <?php $total_jv_amount=0; ?>
                            @foreach ($list_journalvoucher_det as $jv_det)
                            
                            @php $main_acc=$list_journalvoucher_det_other->where('is_main_account',0)->where('transaction_no',$jv_det->transaction_no)->where('credit_amount',$jv_det->debit_amount)->max('account_name');
                            $main_acc_credit_amount = $list_journalvoucher_det->where('is_main_account',1)->where('transaction_no',$jv_det->transaction_no)->max('credit_amount'); @endphp

                            @if($jv_det->debit_amount > 0)
                            <tr>
                                <td class="text-left">{{ $jv_det->account_name }}</td>
                                <td class="text-left">{{ $main_acc }}</td>
                                <td class="text-right">{{ @App\SysHelper::com_curr_format($jv_det->debit_amount,2,'.',',') }} <?php $total_jv_amount += $jv_det->debit_amount; ?></td>
                                <td class="text-left pl-5">{{ $jv_det->remarks }} &nbsp; [ {{ $jv_det->transaction_no }} ]</td>
                            </tr>
                            @endif
                            
                            @endforeach
                            <tr>
                                <td class="text-left"></td>
                                <td class="text-left font-weight-bold">Total Expenses</td>
                                <td class="text-right font-weight-bold">{{ @App\SysHelper::com_curr_format($total_jv_amount,2,'.',',') }}</td>
                                <td class="text-left pl-5"></td>
                            </tr>
                            @endif

                                </tbody>
                            </table>



                        </div> <!-- end table-responsive-->
                        
                    </div> <!-- end card-body-->
                </div> <!-- end card-->

            </div>
        </div>
        @endif
    </div>
    </div>
    
    <div class="row">
      <div class="col-lg-6 p-1">
        <div class="card p-3 mb-1 ">
            <h2 class="page-heading mb-3 border-bottom">Submited Details</h2>
            <div class="p-1">
                <div class="row">
                    <div class="col-lg-4 col-md-3 col-sm-4">
                        <div class="">
                            <h6 class="sub-head mb-1">Expected Delivery Date</h6>
                            <p class="text-muted">{{date('d-M-Y', strtotime(@$deal->delivery_date))}}</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-3 col-sm-4">
                        <div class="">
                            <h6 class="sub-head mb-1">Payment Terms</h6>
                            <p class="text-muted">{{@$deal->paymentterms->title}} @if(@$deal->payment_terms == 22) - {{@$deal->payment_terms_txt}} @endif
                              @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 27)
                                <a class="btn btn-xs text-info m-0 p-0" onclick="update_payment_terms_mode()" ><i class="fa fa-edit" aria-hidden="true"></i></a>
                              @endif
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-3 col-sm-4">
                        <div class="">
                            <h6 class="sub-head mb-1">Payment mode</h6>
                            <p class="text-muted">
                                @if($deal->payment_mode==1) Cash @endif
                                @if($deal->payment_mode==2) Cheque @endif
                                @if($deal->payment_mode==3) Bank Transfer @endif
                                @if($deal->payment_mode==4) Open Credit @endif
                                @if($deal->payment_mode==5) Credit Card @endif
                                @if($deal->payment_mode==6) Bank TT @endif
                                @if($deal->payment_mode==7) Letter of Credit @endif
                    
                                @if($deal->payment_mode_sec==1) , Cash @endif
                                @if($deal->payment_mode_sec==2) , Cheque @endif
                                @if($deal->payment_mode_sec==3) , Bank Transfer @endif
                                @if($deal->payment_mode_sec==4) , Open Credit @endif
                                @if($deal->payment_mode_sec==5) , Credit Card @endif
                                @if($deal->payment_mode_sec==6) , Bank TT @endif
                                @if($deal->payment_mode_sec==7) , Letter of Credit @endif
                                
                    @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 27 || Auth::user()->role_id == 28)
                      <a class="btn btn-xs text-info m-0 p-0" onclick="update_payment_terms_mode()" ><i class="fa fa-edit" aria-hidden="true"></i></a>
                    @endif 
        
                            </p>
                        </div>
                    </div>

                    @if($deal->purchease_required==1)
                    <div class="col-lg-4 col-md-3 col-sm-4">
                        <div class="">
                            <h6 class="sub-head mb-1">Product Purchase</h6>
                            <p class="text-muted"><span class="text-danger text-bold" id="blink">Purchase Required</span></p>
                            <?php try{ ?>
                              @if($purchease[0]->validation == 3) <p class="text-muted"><span class="text-success text-bold text-xs">Under Purchase</span></p> @endif
                              <?php } catch (\Throwable $th){}?>
                              @if(session('logged_session_data.designation_id')==20)
                              <script type="text/javascript">
                              var blink = document.getElementById('blink');
                              setInterval(function() {
                                  blink.style.opacity = (blink.style.opacity == 0 ? 1 : 0);
                              }, 500);
                              </script>
                              @endif
                        </div>
                    </div>            
                    @endif
                  
                  @if($deal->partial_delivery==1)
                  <div class="col-lg-4 col-md-3 col-sm-4">
                      <div class="">
                          <h6 class="sub-head mb-1">Partial Delivery</h6>
                          <p class="text-muted">Partial Delivery</p>
                      </div>
                  </div>
                  @endif
        
                  @if($deal->technical==1)
                  <div class="col-lg-4 col-md-3 col-sm-4">
                      <div class="">
                          <h6 class="sub-head mb-1">Professional Service</h6>
                          <p class="text-muted">
                              @if($deal->technical==0) NO @endif
                              @if($deal->technical==1) YES @endif</p>
                      </div>
                  </div>
                  @endif
                  
                  @if($deal->purchease_approval==0 || $deal->invoice_approval==0 || $deal->delivery_approval==0 || $deal->receivables_approval==0)
                  <div class="col-lg-4 col-md-3 col-sm-4">
                      <div class="">
                          <h6 class="sub-head mb-1">Approval Not Required</h6>
                          <p class="text-danger">
                              @if($deal->purchease_approval==0) Purchase, @endif
                              @if($deal->invoice_approval==0) Invoice, @endif
                              @if($deal->delivery_approval==0) Delivery, @endif
                              @if($deal->receivables_approval==0) Receivables @endif
                            </p>
                      </div>
                  </div>
                  @endif

        
                  @if($deal->lpo !="")
                    <div class="col-lg-4 col-md-3 col-sm-4">
                        <div class="">
                            <h6 class="sub-head mb-1">LPO</h6>
                    <?php $file = explode("|",$deal->lpo); ?>
                    @foreach ($file as $f)
                      <a class="btn-sm btn-primary" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $f }}" target="_blank">Download</a>
                    @endforeach
                        </div>
                    </div>
                    @endif
                    
                    @if($deal->cheque_copy !="")
                    <div class="col-lg-4 col-md-3 col-sm-4">
                        <div class="">
                            <h6 class="sub-head mb-1">Cheque//TT Copy</h6>
                            <p class="text-muted">
                                <?php $file = explode("|",$deal->cheque_copy); ?>
                                @foreach ($file as $f)
                                <a class="btn-sm btn-primary" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $f }}" target="_blank">Download</a>
                                @endforeach
                            </p>
                        </div>
                    </div>
                    @endif

                    @if($deal->purchease_quote !="")
                    <div class="col-lg-4 col-md-2 col-sm-6">
                        <div class="">
                            <h6 class="sub-head mb-1">Puchase Quote</h6>
                            <p class="text-muted">
                                <?php $file = explode("|",$deal->purchease_quote); ?>
                                @foreach ($file as $f)
                                <a class="btn-sm btn-primary" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $f }}" target="_blank">Download</a>
                                @endforeach
                            </p>
                        </div>
                    </div>
                    @endif
        
                    <div class="col-lg-6 col-md-2 col-sm-6">
                    <div id="div_update_payment_mode" style="display: none; width: 500px;" class="border border-danger p-1">
                      {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-approval-receivables-payment-terms-mode', 'method' => 'POST', 'id' => 'update_payment_terms_mode']) }}
                      <b>Change Payment Terms :</b>
                      <select class="form-control js-example-basic-single" name="edit_payment_terms" id="edit_payment_terms" required>
                        <option value="">-Select-</option>
                        @foreach ($paymentterms as $key => $value)
                            <option value="{{ @$value->id }}" @if (@$deal->payment_terms == @$value->id) selected @endif >{{ @$value->title }}</option>
                        @endforeach                                                    
                    </select>
                              <script>
                                    $('#edit_payment_terms').on('change', function(e) {
                                        if ($('#edit_payment_terms').val() == 22) {
                                            $('#edit_payment_terms_txt').css("display", "block");
                                            $('#edit_payment_terms_txt').prop('required', true);
                                        } else {
                                            $('#edit_payment_terms_txt').css("display", "none");
                                            $('#edit_payment_terms_txt').prop('required', false);
                                        }
                                    });
                                    $('#edit_payment_terms').change();
                                </script>
                              <input class="form-control" id="edit_payment_terms_txt" type="text" value="{{ @$deal->payment_terms_txt }}" autocomplete="off" placeholder="Payment Terms" name="edit_payment_terms_txt" @if(@$deal->payment_terms != 22) style="display: none;" @else required @endif><br />
                      <b>Change Payment Mode :</b>
                      <select class="form-control js-example-basic-single" name="edit_payment_mode" required>
                          <option value="1" @if($deal->payment_mode==1) selected @endif>Cash</option>
                          <option value="2" @if($deal->payment_mode==2) selected @endif>Cheque</option>
                          <option value="3" @if($deal->payment_mode==3) selected @endif>Bank Transfer</option>
                          <option value="4" @if($deal->payment_mode==4) selected @endif>Open Credit</option>
                          <option value="5" @if($deal->payment_mode==5) selected @endif>Credit Card</option>
                          <option value="6" @if($deal->payment_mode==6) selected @endif>Bank TT</option>
                      </select>
                      <input type="hidden" name="edit_payment_mode_id" value="{{ $deal->deal_id }}" />
                      <button type="submit" class="btn btn-xs btn-danger text-xs pt-1 pb-1">Change</button>
                      
                    {{ Form::close() }}
                    </div>
                    <script>
                        function update_payment_terms_mode() {
                          if($('#div_update_payment_mode').css('display') == 'none'){
                            $("#div_update_payment_mode").css("display", "block");
                          }
                          else{
                            $("#div_update_payment_mode").css("display", "none");
                          }
                        }
                    </script>
                    </div>
        
                </div>
            </div>

                <div class="col-lg-12 p-1">
        
                    @if(count($list_performa_invoice)>0)
                            @foreach($list_performa_invoice as $list)
                                <a class="btn-sm btn-info" href="{{url('proforma-invoice/'.$list->id.'/download')}}" target="_blank">{{ $list->doc_number }}</a>
                            @endforeach
                    @endif
                    @if(count($list_sales_invoice)>0)                    
                            @foreach($list_sales_invoice as $list)
                                <a class="btn-sm btn-info" href="{{url('sales-invoice/'.$list->id.'/view')}}" target="_blank">{{ $list->doc_number }}</a>
                            @endforeach
                    @endif
                    @if(count($list_delivery_note)>0)
                            @foreach($list_delivery_note as $list)
                                <a class="btn-sm btn-info" href="{{url('delivery-note/'.$list->id.'/view')}}" target="_blank">{{ $list->doc_number }}</a>
                            @endforeach
                    @endif
                    @if(count($list_sales_return)>0)
                            @foreach($list_sales_return as $list)
                                <a class="btn-sm btn-info" href="{{url('sales-return/'.$list->id.'/view')}}" target="_blank">{{ $list->doc_number }}</a>
                            @endforeach
                    @endif
                    @if(count($list_receipt)>0)
                            @foreach($list_receipt as $list)
                                <a class="btn-sm btn-info" href="{{url('receipt/'.$list->id.'/view')}}" target="_blank">{{ $list->doc_number }}</a>
                            @endforeach
                    @endif

                    <br /><br />

                    @if(count($list_purchase_order)>0)
                            @foreach($list_purchase_order as $list)
                                <a class="btn-sm btn-primary" href="{{url('purchase-order/'.$list->id.'/view')}}" target="_blank">{{ $list->doc_number }}</a>
                            @endforeach
                    @endif
                    @if(count($list_goods_receipt_note)>0)
                            @foreach($list_goods_receipt_note as $list)
                                <a class="btn-sm btn-primary" href="{{url('goods-receipt-note/'.$list->id.'/view')}}" target="_blank">{{ $list->doc_number }}</a>
                            @endforeach
                    @endif
                    @if(count($list_purchase_invoice)>0)
                            @foreach($list_purchase_invoice as $list)
                                <a class="btn-sm btn-primary" href="{{url('purchase-invoice/'.$list->id.'/view')}}" target="_blank">{{ $list->doc_number }}</a>
                            @endforeach
                    @endif
                    @if(count($list_purchase_return)>0)
                            @foreach($list_purchase_return as $list)
                                <a class="btn-sm btn-primary" href="{{url('purchase-return/'.$list->id.'/view')}}" target="_blank">{{ $list->doc_number }}</a>
                            @endforeach
                    @endif
                    @if(count($list_payment)>0)
                            @foreach($list_payment as $list)
                                <a class="btn-sm btn-primary" href="{{url('payment/'.$list->id.'/view')}}" target="_blank">{{ $list->doc_number }}</a>
                            @endforeach
                    @endif
                    @if(count($list_journalvoucher)>0)
                            @foreach($list_journalvoucher as $list)
                                <a class="btn-sm btn-primary" href="{{url('journalvoucher/'.$list->id.'/view')}}" target="_blank">{{ $list->doc_number }}</a>
                            @endforeach
                    @endif
                    
                    @if (count($check_cl) > 0)
                    @foreach ($check_cl as $cl)<a class="btn-sm btn-warning" href="{{url('clearance/'.$cl->id.'/download')}}" target="_blank">&nbsp;{{ $cl->invoice_no }}&nbsp;</a>
                    @endforeach
                    @endif
                    
        
        
                </div>


        </div>      
                
  
      </div>
      
      <div class="col-lg-6 p-1 mb-1 h-100">
          <div class="p-3 card">
              <div>
                  <label for="" class="font-weight-bold">Internal Note</label>
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deals-comments-add', 'method' => 'POST', 'id' => 'crm-deals-comments-add']) }}
                        <input type="hidden" id="commentsid" name="commentsid" value="{{ $deal->deal_id }}" />
                        <textarea name="comments" class="form-control" id="comments" cols="10" rows="3"></textarea>
                        
                        <input type="file" class="form-control w-75 mt-2 mb-2" name="commentsdoc" id="commentsdoc" style="float: left;">
                        <div class="mt-0 justify-content-end d-flex mt-2">
                            <button type="submit" class=" btn-small">Add Internal Note</button>
                        </div>
                    {{ Form::close() }}
              </div>
              
              <div class="notes border py-2 px-3 p-0 mt-3">
                  @if(isset($comments))
                  <div>
                      @if($del->note != "")Note : {!! nl2br($del->note) !!} <hr>@endif
                  </div>
                  @foreach ($comments as $cmts)
                  <div>
                      <p class="mb-0 p-0 m-0">{!! $cmts->comments !!}
                          @if ($cmts->commentsdoc!="")<br /><br />
                              <a class="btn-xs btn-info p-0" href="{{asset('public/uploads/crm_deal_doc/')}}/{{ $cmts->commentsdoc }}" target="_blank">&nbsp;&nbsp;<i class="fa fa-paperclip" aria-hidden="true"></i>&nbsp;&nbsp;View File&nbsp;&nbsp;</a>
                          @endif
                      </p>
                      <p class="text-muted text-right p-0 m-0">{{ $cmts->createdby->first_name }} {{ $cmts->createdby->last_name }} on {{date('d/m/Y H:i:s', strtotime($cmts->created_at))}}</p>
                  </div>
                  <hr class="m-0 p-0">
                  @endforeach
                  @endif
              </div>
          </div>
      </div>
  
    </div>

  {{--  -----------------------------------  --}}
@if($del->stage!=6)
  @include('backEnd.crm.DealTrackApprovalStatus')
  {{--  @include('backEnd.crm.DealTrackApprovalStatusForms')  --}}
  @include('backEnd.crm.DealTrackApprovalStatusFormsPopup')
@endif
  {{--  -----------------------------------  --}}


  



  </div>

  <script>
    function toggle_tool_tip(id) {
        var element = $('#desc_' + id);
        var currentWhiteSpace = element.css('white-space');

        if (currentWhiteSpace === 'nowrap') {
            element.css('white-space', '');
        } else {
            element.css('white-space', 'nowrap');
        }
    }
  </script>

<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>



    

    <style>
      .files input {
          outline: 2px dashed #92b0b3;
          outline-offset: -10px;
          -webkit-transition: outline-offset .15s ease-in-out, background-color .15s linear;
          transition: outline-offset .15s ease-in-out, background-color .15s linear;
          padding: 20px 0px 60px 35%;
          text-align: center !important;
          margin: 0;
          width: 100% !important;
      }
      .files input:focus{     outline: 2px dashed #92b0b3;  outline-offset: -10px;
          -webkit-transition: outline-offset .15s ease-in-out, background-color .15s linear;
          transition: outline-offset .15s ease-in-out, background-color .15s linear; border:1px solid #92b0b3;
       }
      .files{ position:relative}
      .files:after {  pointer-events: none;
          position: absolute;
          top: 60px;
          left: 0;
          width: 100%;
          right: 0;
          height: 30px;
          content: "";
          /*background-image: url(https://image.flaticon.com/icons/png/128/109/109612.png);*/
          display: block;
          margin: 0 auto;
          background-size: 100%;
          background-repeat: no-repeat;
      }
      .color input{ background-color:#f1f1f1;}
      .files:before {
          position: absolute;
          bottom: 10px;
          left: 0;  pointer-events: none;
          width: 100%;
          right: 0;
          height: 30px;
          content: " or drag it here. ";
          display: block;
          margin: 0 auto;
          color: #2ea591;
          font-weight: 600;
          text-transform: capitalize;
          text-align: center;
      }
  </style>
  
    <script>

$(window).ready(function() {
        $("#item-store-form").on("keypress", function (event) {           
            var keyPressed = event.keyCode || event.which;
            if (keyPressed === 13) {
                event.preventDefault();
                return false;
            }
        });
});


$(document).on("change", "#deliver_by", function () {
  var deliver_by = $("#deliver_by").val();
  var driver = $("#driver").val();
  var action = "{{ URL::to('getdriverbyshipping') }}";
    $.ajax({
        url: action,
        type: "GET",
        data: {
            _token: '{{ csrf_token() }}',
            deliver_by: deliver_by,
        },
        cache: false,
        success: function(dataResult) {
            //alert(dataResult);
            var dataResult = JSON.parse(dataResult);
            var len = 0;
            if(dataResult['data']=="ERROR")
            {
                alert("Error found in something!!");
            }
            else{
                if(dataResult['data'] != null){
                len = dataResult['data'].length;
                }
                if(len > 0){
                    
                    $('#driver').find('option').not(':first').remove();
                    for(var i=0; i<len; i++){
                        var id = dataResult['data'][i].driver_name;
                        var name = dataResult['data'][i].driver_name;
                        var option = "<option value='"+id+"'>"+name+"</option>";
                        $("#driver").append(option);
                    }
                }
            }
          }
    });
});



    </script>
@endsection
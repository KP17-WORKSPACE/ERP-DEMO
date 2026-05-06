@extends('backEnd.masterpage')
@section('mainContent')
    @php
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp

<?php try { ?>

    <div class="container-fluid mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div class="mb-3">
                <h2 class="page-heading m-0">Edit Quote (Deal ID - {{ $quotation->id }}) / Quote No {{ $quotationitems[0]->quote_id }}</h2>
                <span class="page-label">Home - Deal - Edit Quote</span>
            </div>
            <div>
                <a href="{{ url('crm-deals/'.$quotation->id.'/view') }}" type="button" class="btn btn-primary"><i class="fa fa-list"></i> View Deal {{ $quotation->id }}</a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4 mb-3">
                <div class="p-4 card h-100">
                    <table width="100%" class="text-md">
                        <tr><td width="130px">Deal Name </td><td>: <b>{{ $quotation->deal_name }}</b></td></tr>
                        <tr><td>Deal Id </td><td>: <b>{{ $quotation->id }}</b></td></tr>
                        <tr><td>Customer Name </td><td>: {{ $quotation->customername->name }}</td></tr>
                        <tr><td>Download Quote </td><td>: <a class="text-info" href="{{url('crm-quote/'.$quotation->id.'/download/'.$quotationitems[0]->quote_id)}}">Download</a></td></tr>
                    </table>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="p-4 card h-100">
                    <table width="100%" class="text-sm">
                        <tr><td>Contact Person </td><td>: {{ $quotation->cust_name }}</td></tr>
                        <tr><td>Address </td><td>: {{ $quotation->customername->address }}</td></tr>
                        <tr><td>Mobile </td><td>: {{ $quotation->cust_no }}</td></tr>
                        <tr><td>Email </td><td>: {{ $quotation->cust_email }}</td></tr>
                    </table>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="p-4 card h-100">
                    <table width="100%" class="text-sm">                                
                        <tr><td>Company </td><td>: {{ $quotationitems[0]->company->company_name }}</td></tr>
                        <tr><td>Quote Owner </td><td>: {{ $quotation->ownername->first_name }} {{ $quotation->ownername->middle_name }} {{ $quotation->ownername->last_name }}</td></tr>
                        <tr><td>Quote Currency </td><td>: {{ $currency_code }}</td></tr>
                        <tr><td>Quote Validity </td><td>: {{ $quotation->quote_validity }}</td></tr>
                        <tr><td>Payment Terms </td><td>: {{ $payment_terms_name }}</td></tr>
                        <tr><td>Closing Date </td><td>: {{ date('d/m/Y', strtotime($delivery_date)) }} 
                            | <a class="text-primary" style="cursor: pointer;" data-toggle="modal" data-target="#exampleModalCenter">Edit</a></td></tr>
                        <tr><td>Delivery Time </td><td>: {{ $delivery_time }}</td></tr>
                        <tr><td colspan="2"><a class="text-primary" style="cursor: pointer;" data-toggle="modal" data-target="#exampleModalCenter2">Update Terms and Condition</a></td></tr>
                    </table>
                </div>
            </div>
        </div>

        <input type="hidden" name="deal_id" id="deal_id" value="{{ $deal_id }}"/>
        <input type="hidden" name="company_id" id="company_id" value="{{ $company_id }}"/>
        <input type="hidden" name="currency_id" id="currency_id" value="{{ $currency_id }}"/>
        <input type="hidden" name="customer_type" id="customer_type" value="{{ $customer_type }}"/>
        <input type="hidden" name="payment_terms" id="payment_terms" value="{{ $payment_terms }}"/>
        <input type="hidden" name="delivery_date" id="delivery_date" value="{{ $delivery_date }}"/>
        <input type="hidden" name="payment_terms_txt" id="payment_terms_txt" value="{{ $payment_terms_txt }}"/>
        <input type="hidden" name="delivery_time" id="delivery_time" value="{{ $delivery_time }}"/>
        <input type="hidden" name="quote_id" id="quote_id" value="{{ $quotationitems[0]->quote_id }}"/>
  
        <div class="row">
            <div class="col-lg-12 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title m-0 pb-0">Products</h4>
                        <a href="{{url('crm-quote/'.$deal_id.'/addnew/'.$quotationitems[0]->quote_id)}}" class=" btn-small float-right"><i class="fa fa-plus" aria-hidden="true"></i> Add More Products</a>
                    <?php $t_qty=0; $t_price=0; $t_discount=0; $t_netamount=0; $t_currency=""; ?>
                    <table class="table table-bordered table-striped"  width="100%" cellspacing="0">
                        @if(isset($quotationitems))
                        <thead>
                            <tr>
                                <th style="width: 5%;"></th>
                                <th style="width: 10%;">@lang('Part Number')</th>
                                <th style="width: 30%;">@lang('Description')</th>
                                <th style="width: 10%;">@lang('Qty')</th>
                                <th style="width: 10%;">@lang('Unit Price')</th>
                                <th style="width: 10%;">@lang('Unit Discount')</th>
                                <th style="width: 10%;">@lang('Total Amount')</th>
                                <th style="width: 15%;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i=0; @endphp
                            @if(count($quotationitems) == 1 && $quotationitems[0]->status ==0)

                            @else
                        @foreach ($quotationitems as $Item)
                            @php $i++; @endphp
                            
                            <tr style="line-height: 35px;">
                                <td>
                                    @if ($i!=count($quotationitems))
                                    &nbsp;&nbsp;<a onclick="sort_down({{$Item->id}},{{$Item->sort_id}},{{$Item->deal_id}})" role="button"><i class="fa fa-arrow-down text-danger" aria-hidden="true"></i></a>
                                    @else
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    @endif
                                    @if ($i!=1)
                                    &nbsp;&nbsp;<a onclick="sort_up({{$Item->id}},{{$Item->sort_id}},{{$Item->deal_id}})" role="button"><i class="fa fa-arrow-up text-success" aria-hidden="true"></i></a>
                                    @endif                                
                                </td>
                                <td>{{ $Item->productname->part_number }}</td>
                                <td><textarea type="text" class="form-control" id="txt_udescription_{{$Item->id}}">{!! nl2br($Item->description) !!}</textarea></td>
                                <td><input type="number" class="form-control" id="txt_uqty_{{$Item->id}}" value="{{$Item->qty}}"></td>
                                <td><input type="number" class="form-control" id="txt_uprice_{{$Item->id}}" value="{{ App\SysHelper::currancy_format_textbox($Item->price,$currency_id) }}"></td>
                                <td><input type="number" class="form-control" id="txt_udiscount_{{$Item->id}}" value="{{ App\SysHelper::currancy_format_textbox($Item->discount,$currency_id) }}"></td>
                                <td><input type="number" class="form-control" id="txt_utotal_price_{{$Item->id}}" value="{{ App\SysHelper::currancy_format_textbox((($Item->price * $Item->qty) - ($Item->discount * $Item->qty)),$currency_id) }}" readonly></td>
                                <td align="right"><input type="hidden" id="txt_upro_id_{{$Item->product_id}}" value="{{$Item->product_id}}">
                                    <button class="btn btn-warning btn-xs" title="Update" id="txt_btn_upd_{{$Item->id}}" onclick="upd_toquote({{$Item->id}})">Update</button>
                                    <button class="btn btn-danger btn-xs" title="Delete" id="txt_btn_del_{{$Item->id}}" onclick="del_toquote({{$Item->id}})">Delete</button></td>
                            </tr>
                            
                            <?php $t_qty += $Item->qty;
                            $t_price += ($Item->price * $Item->qty);
                            $t_discount += $Item->discount * $Item->qty;
                            $t_netamount += ($Item->price * $Item->qty) - ($Item->discount * $Item->qty);
                            $t_currency = $Item->currency->code; ?>
                        @endforeach
                        @endif

                    </tbody>
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-quote-discount', 'method' => 'POST', 'id' => 'crm-quote-discount']) }}
                        <thead>
                        <tr style="height: 60px;">
                        <td colspan="3"></td>
                        <td colspan="2" class="text-bold text-dark text-right">Aditional Discount : </td>
                        <td><input type="number" step="any" class="form-control" name="deal_discount" id="deal_discount" value="{{ App\SysHelper::currancy_format_textbox($quotation->deal_discount,$currency_id)}}"></td>
                        <input type="hidden" name="dis_deal_id" id="dis_deal_id" value="{{ $deal_id }}"/>
                        <input type="hidden" name="quote_dis_deal_id" id="quote_dis_deal_id" value="{{ $quotationitems[0]->quote_id }}"/>
                        <td colspan="2" ><button type="submit" class="btn btn-warning btn-xs" title="Update">Update</button></td>
                    </tr>
                        </thead>
                    {{ Form::close() }}
                        @endif
                    </table>
                    </div>
                </div>
            </div>

        </div>
        <br style="clear: both;"/><br /><br /><br />

        <div class="row">
            <div class="col-lg-4 mb-3">
                <div class="p-4 card" style="width: 100%; position: fixed; bottom: 0px; z-index:9; padding: 0px;">
            <div >
                <div class="row">
                    <div class="col-lg-1 leadbox2">Items : <b>{{ $t_qty }} </b>Qty</div>
                    <div class="col-lg-2 leadbox2">Amount : <b>{{ App\SysHelper::currancy_format(($t_price),$currency_id) }} {{ $t_currency }}</b></div>
                    <div class="col-lg-2 leadbox2">Discount : <b>{{ App\SysHelper::currancy_format(($t_discount+$quotation->deal_discount),$currency_id) }} {{ $t_currency }}</b></div>
                    <?php $t_vat = (($t_price * $quotationitems[0]->company->net_vat/100) - (($t_discount+$quotation->deal_discount) * $quotationitems[0]->company->net_vat/100)); ?>
                    <div class="col-lg-2 leadbox2">Vat : <b>{{ App\SysHelper::currancy_format(($t_vat),$currency_id) }} {{ $t_currency }}</b></div>
                    <div class="col-lg-2 leadbox2">Net Amount : <b>{{ App\SysHelper::currancy_format(($t_netamount+$t_vat-$quotation->deal_discount),$currency_id) }} {{ $t_currency }}</b></div>                    
                    <div class="col-lg-1">
                    <a class="btn btn-primary mt-1 float-right" href="{{url('crm-deals/'.$deal_id.'/view')}}">Save</a></div>
                </div>
            </div>
                </div>
            </div>
        </div>
    </div>

<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>



<style>
    .leadbox{border: solid 1px #ededef; border-radius: 5px; background: #ffffff; padding: 5px 5px 10px 15px; margin-right: 15px;}
    .leadbox2{border: solid 1px #ededef; border-radius: 5px; background: #ffffff; padding: 7px 5px 0px 15px; font-size: 17px;}
    .pro-box{ border: solid 1px #ededef; margin: 5px 15px 5px 5px; padding: 10px;}
    .pro-box:hover{ background: #eff3e7;}
</style>


    <!-- Modal -->
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Update Quote</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-quote-update-payment-terms', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
          <div class="modal-body">

            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Select Closing Date<span>*</span> </label>
                    <input class="form-control" id="delivery_date" type="date" autocomplete="off" name="delivery_date" value="{{ date('Y-m-d', strtotime($delivery_date)) }}">
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Select Payment Terms<span>*</span> </label>
                    <select class="form-control" name="payment_terms" id="payment_terms" required>
                        <option value="">-Select-</option>
                        @foreach ($paymenttermslist as $key => $value)
                            <option value="{{ @$value->id }}" @if($value->title==$payment_terms_name) selected @endif>{{ @$value->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Payment terms, if Other, please specify<span>*</span> </label>
                    <input class="form-control" id="payment_terms_txt" type="text" autocomplete="off" placeholder="Payment Terms" name="payment_terms_txt">
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Quote Validity<span></span> </label>
                    <input class="form-control" id="quote_validity" type="text" autocomplete="off" placeholder="Quote Validity" name="quote_validity" value="{{ $quotation->quote_validity }}" required>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Delivery Time<span></span> </label>
                    <input class="form-control" id="delivery_time" type="text" autocomplete="off" placeholder="Delivery Time" name="delivery_time" value="{{ $delivery_time }}">
                </div>
            </div>
          </div>
          <div class="modal-footer">
            <input type="hidden" name="edit_deal_id" value="{{ $deal_id }}"/>
            <input type="hidden" name="edit_deal_quote_id" value="{{ $quotationitems[0]->quote_id }}"/>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save changes</button>
          </div>          
          {{ Form::close() }}
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModalCenter2" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Update Terms and Condition</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-quote-update-terms-condition', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
          <div class="modal-body">

            <div class="row">
                <div class="col-lg-12">
                    <label class="txtlbl">Terms and Condition<span></span> </label>
                    @if ($quotation->terms_and_condition=="")
                    <textarea class="form-control" name="terms_and_condition" rows="6" autocomplete="off" id="terms_and_condition" placeholder="Terms and Condition">1. Quote/Order will be subject to approval of payment/credit terms by our finance.
2. Please mention our Quotation No.in your Purchase Order
3. In case of non-availability of quote products {{ $quotationitems[0]->company->company_name }} reserved the rights to supply a functionally similar or better product.</textarea>                        
                    @else
                    <textarea class="form-control" name="terms_and_condition" rows="6" autocomplete="off" id="terms_and_condition" placeholder="Terms and Condition">{{ @$quotation->terms_and_condition }}</textarea>                        
                    @endif
                </div>
            </div>
          </div>
          <div class="modal-footer">
            <input type="hidden" name="edit_tc_deal_id" value="{{ $deal_id }}"/>
            <input type="hidden" name="edit_tc_deal_quote_id" value="{{ $quotationitems[0]->quote_id }}"/>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save changes</button>
          </div>          
          {{ Form::close() }}
        </div>
      </div>
    </div>

    <script>
        function add_toquote(id) {
            $("#loading_bg").css("display", "block");
            var qty = $("#txt_qty_"+id).val();
            var price = $("#txt_price_"+id).val();
            var description = $("#txt_description_"+id).val();
            var deal_id = $("#deal_id").val();
            var company_id = $("#company_id").val();
            var currency_id = $("#currency_id").val();
            var customer_type = $("#customer_type").val();
            var payment_terms = $("#payment_terms").val();
            var delivery_date = $("#delivery_date").val();
            var payment_terms_txt = $("#payment_terms_txt").val();
            var delivery_time = $("#delivery_time").val();
            var quote_id = $("#quote_id").val();

            if (qty == "" || qty <= 0) {
                alert("Please Add Qty");
                $("#txt_qty_"+id).focus();
                $("#loading_bg").css("display", "none");
                return false;
            }
            $("#txt_btn_"+id).attr('disabled', true);
    
            var action = "{{ URL::to('crm-quote-additemsedit') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    qty: qty,
                    price: price,
                    description: description,
                    deal_id: deal_id,
                    company_id: company_id,
                    currency_id: currency_id,
                    customer_type: customer_type,
                    payment_terms: payment_terms,
                    delivery_date: delivery_date,
                    payment_terms_txt: payment_terms_txt,
                    delivery_time: delivery_time,
                    quote_id:quote_id,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    if (dataResult['data'] == "ERROR") {
                        alert("Error found in something!!");
                    } else {
                        $("#loading_bg").css("display", "none");
                        //alert("Renewed! Please update and continue");
                        location.reload(true);
                    }
                }
            });
        }
        function upd_toquote(id) {
            $("#loading_bg").css("display", "block");
            var qty = $("#txt_uqty_"+id).val();
            var price = $("#txt_uprice_"+id).val();
            var description = $("#txt_udescription_"+id).val();
            var discount = $("#txt_udiscount_"+id).val();
            var pro_id = $("#txt_upro_id_"+id).val();
            var deal_id = $("#deal_id").val();
            
            if (qty == "" || qty <= 0) {
                alert("Please Add Qty");
                $("#txt_uqty_"+id).focus();
                $("#loading_bg").css("display", "none");
                return false;
            }
            $("#txt_ubtn_"+id).attr('disabled', true);
    
            var action = "{{ URL::to('crm-quote-upditemsedit') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    qty: qty,
                    price: price,
                    description: description,
                    discount: discount,
                    pro_id: pro_id,
                    deal_id: deal_id,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    if (dataResult['data'] == "ERROR") {
                        alert("Error found in something!!");
                    } else {
                        $("#loading_bg").css("display", "none");
                        //alert("Renewed! Please update and continue");
                        location.reload(true);
                    }
                }
            });
        }
        function del_toquote(id) {
            
            var result = confirm('Are you sure you want to delete this item?');
            if (!result) {
                return false;
            }

            $("#loading_bg").css("display", "block");
            var btn = $("#del_btn_del_"+id).val();
            $(btn).attr('disabled', true);
            var deal_id = $("#deal_id").val();
            var quote_id = $("#quote_id").val();
    
            var action = "{{ URL::to('crm-quote-deleteitemsedit') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    deal_id: deal_id,
                    quote_id: quote_id,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    if (dataResult['data'] == "ERROR") {
                        alert("Error found in something!!");
                    } else {
                        $("#loading_bg").css("display", "none");
                        //alert("Renewed! Please update and continue");
                        location.reload(true);
                    }
                }
            });
        }
        function sort_up(id,sort_id,deal_id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('crm-quote-sort-up') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    sort_id: sort_id,
                    deal_id: deal_id,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    if (dataResult['data'] == "ERROR") {
                        alert("Error found in something!!");
                    } else {
                        $("#loading_bg").css("display", "none");
                        //alert("Renewed! Please update and continue");
                        location.reload(true);
                    }
                }
            });
        }
        function sort_down(id,sort_id,deal_id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('crm-quote-sort-down') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    sort_id: sort_id,
                    deal_id: deal_id,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    if (dataResult['data'] == "ERROR") {
                        alert("Error found in something!!");
                    } else {
                        $("#loading_bg").css("display", "none");
                        //alert("Renewed! Please update and continue");
                        location.reload(true);
                    }
                }
            });
        }

    </script>
@endsection
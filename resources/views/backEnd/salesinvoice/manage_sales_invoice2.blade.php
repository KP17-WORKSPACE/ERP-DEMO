@extends('backEnd.masterpage')
@section('mainContent')
    @php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp
    
    <?php try { ?>
        <div class="container-fluid">
            <div class="d-sm-flex justify-content-between">
                <div class="mb-3">
                    <h2 class="page-heading m-0">Sales Invoice</h2>
                    <span class="page-label">Home - Sales Invoice</span>
                </div>
                <div>
                    <a href="{{ url('sales-invoice/create') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> New</a>
                    <a href="{{ url('sales-invoice') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> List</a>
                </div>
            </div>
            <div class="card p-4 mb-2">
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'sales-invoice-store2', 'method' => 'POST', 'id' => 'sales-invoice-create-form']) }}
                <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                <input type="hidden" name="id" value="{{ isset($edit) ? $edit->id : '' }}">
                <input type="hidden" id="net_vat" name="net_vat" value="{{ $net_vat }}">
                
                <div class="white-box">

                    <div class="col-lg-12 text-right">
                        @if (session()->has('message-success') != '' || session()->get('message-danger') != '')
                            @if (session()->has('message-success'))
                                <p class="text-success">
                                    {{ session()->get('message-success') }}
                                </p>
                            @elseif(session()->has('message-danger'))
                                <p class="text-danger">
                                    {{ session()->get('message-danger') }}
                                </p>
                            @endif
                        @endif
                    </div>

                    <div class="add-visitor">
                              
                                <div class="row">
                                    <div class="col-lg-4 mb-2">
                                            <div class="input-effect">
                                                <label class="dynamicslbl">Customer <span>*</span></label>
                                                <select class="form-control js-account-select" name="customer" id="customer" required>
                                                    <option value=""></option>
                                                    @foreach ($customer as $value)
                                                        <option value="{{ @$value->id }}"
                                                            {{ isset($account_id) ? (!empty($account_id) ? (@$account_id == @$value->id ? 'selected' : '') : '') : '' }}>{{ @$value->account_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="row">
                                            <div class="col-lg-4 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">@lang('Doc') @lang('Number')<span>*</span></label>
                                                    <?php
                                                        $invno=@App\SysHelper::get_new_sales_invoice_code();
                                                    ?>

                                                    <input class="form-control" type="text" name="doc_number" autocomplete="off" id="doc_number" value="{{ isset($edit) ? (!empty(@$edit->title) ? @$edit->title : old('doc_number')) : $invno }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">Invoice Date</label>
                                                    @php
                                                    $value = date('Y-m-d');
                                                    if(isset($edit) && !empty($edit->doc_date) ){ @$value =
                                                    date('Y-m-d', strtotime(@$edit->doc_date)); }
                                                    else{ if(!empty(old('doc_date'))){ @$value = old('doc_date');
                                                    }else{
                                                    @$value = date('Y-m-d'); } }
                                                    @endphp
                                                    <input class="form-control" id="doc_date" type="date" autocomplete="off"
                                                        name="doc_date" value="{{ @$value }}" required>
                                                </div>
                                            </div>
        
                                        <div class="col-lg-4 mb-2">
                                            <div class="input-effect">
                                                <label class="dynamicslbl">Currency</label>
                                                <?php
                                                    $currency1=1;
                                                    if(session('logged_session_data.company_id')==8){
                                                        $currency1=2;
                                                    }
                                                    if(isset($deal_details)) {
                                                        $currency1 = $deal_details->deal_currency;
                                                    }
                                                ?>
                                                <select class="form-control js-example-basic-single" name="currency" id="currency">
                                                    {{-- <option data-display="@lang('Currency') *" value="">@lang('Currency') *</option> --}}

                                                    @foreach ($currency as $value)
                                                        <option value="{{ @$value->id }}"
                                                            @if (isset($edit))
                                                                @if($edit->currency == @$value->id) selected @endif
                                                            @else
                                                                @if($value->id == $currency1) selected @endif
                                                            @endif 
                                                            >
                                                            {{ @$value->code }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        

                                        </div>
                                    </div>
                                    <div class="col-lg-4 mb-2">
                                        <div class="input-effect">
                                            <label class="txtlbl">Pending list</label>
                                            <div id="plist"
                                                style="width: 100%; height: 250px; border: solid 1px #dfe1d7; border-radius: 5px; padding: 5px 5px; overflow-y: scroll;">
                                            </div>
                                            <a data-modal-size="modal-md" data-target="#profo_pending_popup_win" id="addProfoPending"
                                                data-toggle="modal"></a>
                                            <input type="hidden" id="grn_id" name="profo_id">
                                            <input type="hidden" id="vat_percentage" name="vat_percentage" value="5">
                                        </div>
                    
                                    </div>
                                    <div class="col-lg-8 mb-2">
                                        <div class="row">
                                            <div class="col-lg-4 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">@lang('Delivery Terms')<span>*</span></label>
                                                    <input class="form-control" type="text" name="delivery_terms" autocomplete="off" id="delivery_terms" value="{{ isset($edit) ? (!empty(@$edit->delivery_terms) ? @$edit->delivery_terms : old('delivery_terms')) : 'Ex-Dubai' }}" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">@lang('Printed Invoice Number')<span></span></label>
                                                    <input class="form-control" type="text" name="printed_invoice_number" autocomplete="off" id="printed_invoice_number" value="{{ isset($edit) ? (!empty(@$edit->printed_invoice_number) ? @$edit->printed_invoice_number : old('printed_invoice_number')) : '' }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-4 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">@lang('Salesman')<span>*</span></label>
                                                    <select class="form-control" name="sales_man" id="sales_man" required>
                                                        <option value="">-Select-</option>
                                                        @foreach ($staff as $value)
                                                        <option value="{{ @$value->user_id }}"
                                                            @if(isset($deal_details)) @if($deal_details->owner == $value->user_id) selected @endif @else @if($value->user_id == Auth::user()->id) selected  @endif @endif
                                                            >{{ @$value->full_name }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">@lang('Payment Terms')<span>*</span></label>
                                                    <select class="form-control" name="payment_terms" id="payment_terms" onchange="fn_payment_terms()" required>
                                                        <option value="" ></option>
                                                        @foreach($paymentterms as $value)
                                                             <option value="{{@$value->id}}" {{isset($select_cart)? !empty(@$select_cart[0]->payment_terms)? @$select_cart[0]->payment_terms == @$value->id ? 'selected':'':'':''}} >{{@$value->title}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                            <div id="div_payment_terms" style="display: none; padding-top: px;">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Other Payment Terms')<span>*</span></label>
                                    <input class="txtbx primary-input form-control" type="text" name="payment_terms2" autocomplete="off" id="payment_terms2" value="{{ @$select_cart[0]->payment_terms_txt }}">
                                </div>
                            </div>
                                            </div>
                                            <div class="col-lg-4 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">LPO/Reference No<span>*</span></label>
                                                    <input class="form-control" type="text" name="reference_no" autocomplete="off" id="reference_no" value="{{ $select_cart[0]->reference_no }}" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">LPO/Reference Date<span>*</span></label>
                                                    <input class="form-control" type="date" name="reference_date" autocomplete="off" id="reference_date" value="{{ $select_cart[0]->reference_date }}" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">Deal ID<span>*</span></label>
                                                    <input class="form-control" type="text" name="deal_id" autocomplete="off" id="deal_id" value="{{ @App\SysHelper::get_code_from_dealid($select_cart[0]->deal_id) }}" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">Supplier Name<span>*</span></label>
                                                    <?php if ($supplier_name=="") { $supplier_name="Taken from stock"; } ?>
                                                    <input class="form-control" type="text" name="supplier_name" autocomplete="off" id="supplier_name" value="{{ $supplier_name }}" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">@lang('Created') @lang('By')<span>*</span></label>
                                                    <input class="form-control" type="text" name="createdby" autocomplete="off" id="createdby" value="{{ isset($edit) ? (!empty(@$edit->number) ? @$edit->number : old('createdby')) : Auth::user()->full_name }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-lg-8 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">Narration<span></span></label>
                                                    <input class="form-control" type="text" name="narration" autocomplete="off" id="narration" value="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                            <div class="row">
                                
                <div class="col-lg-12 mb-0">

                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                          <a class="nav-link active" id="shipping-tab" data-toggle="tab" href="#shipping" role="tab" aria-controls="shipping" aria-selected="true">Shipping Details</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="vat-tab" data-toggle="tab" href="#vat" role="tab" aria-controls="vat" aria-selected="false">VAT Details</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="enduser-tab" data-toggle="tab" href="#enduser" role="tab" aria-controls="enduser" aria-selected="false">End User Details</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="shipping" role="tabpanel" aria-labelledby="shipping-tab">
                            <div class="row mt-2">
                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="dynamicslbl">@lang('Name') <span></span></label>
                                        <input type="text" class="form-control" id="shipping_name" name="shipping_name" value="{{ $deal_details->delivery_name }}">
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="dynamicslbl">@lang('Address') <span></span></label>
                                        <input type="text" class="form-control" id="shipping_address" name="shipping_address" value="{{ $deal_details->delivery_address }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="vat" role="tabpanel" aria-labelledby="vat-tab">
                            <div class="row mt-2">
                                <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="dynamicslbl">@lang('Customer Type')</label>
                                            <select class="form-control" name="customer_type" id="customer_type">
                                                <option value="0" ></option>
                                                @foreach($customertype as $value)
                                                        <option value="{{@$value->id}}" {{isset($customer_det->customer_type)? !empty(@$customer_det->customer_type)? @$customer_det->customer_type==@$value->id ? 'selected':'':'':''}} >{{@$value->title}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="dynamicslbl">@lang('Sale Type')</label>
                                            <select class="form-control" name="sale_type" id="sale_type">
                                                <option value="0" ></option>
                                                @foreach($saletype as $value)
                                                        <option value="{{@$value->id}}" {{isset($customer_det->sale_type)? !empty(@$customer_det->sale_type)? @$customer_det->sale_type==@$value->id ? 'selected':'':'':''}} >{{@$value->title}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="dynamicslbl">@lang('Customer Country') <span></span></label>
                                            <select class="form-control" name="customer_country" id="country">
                                                <option data-display="" value="0"></option>
                                                @foreach ($countries as $key => $value)
                                                    <option value="{{ @$value->id }}"
                                                        <?php try{?>                                                        
                                                        @if (isset($customer_det->vat_country)) @if (@$customer_det->vat_country == $value->id) selected @endif
                                                        @endif
                                                        <?php } catch (\Throwable $th) {} ?>
                                                        >{{ @$value->name }} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <div class="tab-pane" id="enduser" role="tabpanel" aria-labelledby="enduser-tab">
                            <div class="row mt-2">
                                <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="dynamicslbl">@lang('End User Name') <span></span></label>
                                            <input type="text" class="form-control" name="end_user_name" id="end_user_name" autocomplete="off" value="@if(isset($enduser_details)) {{ @$enduser_details->end_user_company_name }} @endif" />
                                            
                                        </div>
                                    </div>
                                    <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="dynamicslbl">@lang('Contact Person Name') <span></span></label>
                                            <input type="text" class="form-control" name="contact_person_name" id="contact_person_name" autocomplete="off" value="@if(isset($enduser_details)) {{ @$enduser_details->end_user_contact_person }} @endif">
                                            
                                        </div>
                                    </div>
                                    <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="dynamicslbl">@lang('Contact Person Email') <span></span></label>
                                            <input type="text" class="form-control" name="contact_person_email" id="contact_person_email" autocomplete="off" value="@if(isset($enduser_details)) {{ @$enduser_details->email }} @endif">
                                            
                                        </div>
                                    </div>
                                    <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="dynamicslbl">@lang('Contact Person No') <span></span></label>
                                            <input type="text" class="form-control" name="contact_person_no" id="contact_person_no" autocomplete="off" value="@if(isset($enduser_details)) {{ @$enduser_details->mobile_no }} @endif">
                                            
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
                            
                            
                        
                        
                        </div>


                        <div class="equipment comon-status row d-block">
                            <hr />
                            <h6 class="primary-color">@lang('Item Details'):</h6>
                            

                            <table class="table table-bordered table-striped" id="si-table" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th style="width:100px;">@lang('Part No')</th>
                                        <th style="width:150px;">@lang('Description')</th>
                                        <th style="width:70px;">@lang('VAT')</th>
                                        <th style="width:70px;">@lang('Qty')</th>
                                        <th style="width:80px;">@lang('Unit Price')</th>
                                        <th style="width:70px;">@lang('Value')</th>
                                        <th style="width:70px;">@lang('Discount')</th>
                                        <th style="width:120px;">@lang('Taxable Amount')</th>
                                        <th style="width:100px;">@lang('VAT Amount')</th>
                                        <th style="width:100px;">@lang('Total Amount')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $roid = 1; @endphp
                                    @if (count($select_cart)>0)
                                    @foreach ($select_cart as $items)                                        
                                    <tr id="rowone{{$roid}}" onclick="fn_addRow({{$roid}})">
                                        <td>
                                            <input class="form-control" type="text" id="part_number_txt_{{$roid}}" name="part_number_txt[]" autocomplete="off" min="0" value="{{ $items->part_number_txt }}" readonly>
                                            <input type="hidden" id="part_number_{{$roid}}" name="part_number[]" value="{{ $items->part_number }}">
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" id="description_{{$roid}}" name="description[]" value="{{$items->description}}" readonly="true">
                                        </td>
                                        <td>
                                            <?php /*<input class="form-control vat" type="number" id="vat_{{$roid}}" name="vat[]" autocomplete="off" value="{{ $net_vat }}" min="0" readonly>*/ ?>
                                            <input class="form-control vat" type="number" id="vat_{{$roid}}" name="vat[]" autocomplete="off" value="{{ round($items->tax) }}" min="0" readonly>
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" id="qty_{{$roid}}" name="qty[]" autocomplete="off" value="{{$items->qty}}" min="0" onchange="calc_change({{$roid}})">
                                        </td>
                                        <td>
                                            <input class="form-control text-right" type="number" id="unitprice_{{$roid}}" step="any" name="unitprice[]" autocomplete="off" value="{{$items->unitprice}}" min="0" onchange="calc_change({{$roid}})">
                                        </td>
                                        <td>
                                            <input class="form-control text-right" type="number" id="value_{{$roid}}" name="value[]" autocomplete="off" value="{{$items->value}}" min="0" readonly>
                                        </td>
                                        <td>
                                            <input class="form-control text-right" type="number" id="discount_{{$roid}}"  step="any" name="discount[]" autocomplete="off" value="{{$items->discount}}" min="0" onchange="calc_change({{$roid}})">
                                        </td>
                                        <td>
                                            <input class="form-control text-right" type="number" id="taxamount_{{$roid}}" name="taxamount[]" value="{{$items->taxableamount}}" autocomplete="off" min="0" readonly>
                                        </td>
                                        <td>
                                            <input class="form-control text-right" type="number" id="vatamount_{{$roid}}" name="vatamount[]" value="{{$items->vatamount}}" autocomplete="off" min="0" readonly>
                                        </td>
                                        <td>
                                            <input class="form-control text-right" type="number" id="totalamount_{{$roid}}" name="totalamount[]" value="{{$items->vatamount + $items->taxableamount}}" autocomplete="off" min="0" readonly>
                                        </td>
                                    </tr>
                                    @php $roid++; @endphp
                                    @endforeach                                        
                                    @endif
                                    <?php /*$roid--;*/?>
                                </tbody>
                                <thead>
                                    <?php $roid--; ?>
                                    <input type="hidden" id="si-row-count" value="{{ $roid }}">
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th class="text-right"><label id="qty_total">{{ $select_cart->sum("qty") }}</label></th>
                                        <th class="text-right"><label id="unitprice_total">{{ @App\SysHelper::com_curr_format($select_cart->sum("unitprice"),2,',','') }}</label></th>
                                        <th class="text-right"><label id="value_total">{{ @App\SysHelper::com_curr_format($select_cart->sum("value"),2,',','') }}</label></th>
                                        <th class="text-right"><label id="discount_total">{{ @App\SysHelper::com_curr_format($select_cart->sum("discount"),2,',','') }}</label></th>
                                        <th class="text-right"><label id="taxableamount_total">{{ @App\SysHelper::com_curr_format($select_cart->sum("taxableamount"),2,',','') }}</label></th>
                                        <th class="text-right"><label id="vatamount_total">{{ @App\SysHelper::com_curr_format($select_cart->sum("vatamount"),2,',','') }}</label></th>
                                        <th class="text-right"><label id="net_total">{{ @App\SysHelper::com_curr_format($select_cart->sum("taxableamount")+$select_cart->sum("vatamount"),2,',','') }}</label></th>
                                    </tr>
                                    <tr>
                                        <th colspan="9" class="text-right">Aditional Discount</th>
                                        <th><input type="number" class="form-control text-right" name="deal_discount" step="any" value="{{ @App\SysHelper::com_curr_format($deal_details->deal_discount,2,'.','') }}" /></th>
                                    </tr>
                                </thead>
                            </table>
                            <?php /*
                            <div style="display: none;">
                                <button type="button" class="primary-btn small fix-gr-bg" id="addRowSI"><span class="ti-plus pr-2"></span>@lang('lang.item')</button>
                            </div>
                            */ ?>

<script>
function fn_addRow(id)
{
var rownum = document.getElementById('si-row-count').value;
if(id==rownum)
{
document.getElementById('si-row-count').value = (Number(rownum) + Number(1));
document.getElementById('addRowSI').click();
}
}
function ddl_part_change(id)
{
var selOpt = $('#part_number_'+id+' :selected').val();
$('#part_number_txt_'+id+' option[value='+selOpt+']').attr('selected','selected');        
var selOpt2 = $('#part_number_txt_'+id+' :selected').text();
$('#description_'+id+'').val(selOpt2);
$('#description_'+id+'').focus();
}

function calc_change(id) {
    var net_vat = $('#net_vat').val();
    //var net_vat = $('#vat_percentage').val();

    var qty = $('#qty_' + id + '').val();
    var unitprice = $('#unitprice_' + id + '').val();
    var value = $('#value_' + id + '').val();
    var discount = $('#discount_' + id + '').val();
    var taxamount = $('#taxamount_' + id + '').val();
    var vatamount = $('#vatamount_' + id + '').val();
    var totalamount = $('#totalamount_' + id + '').val();


    qty = (qty === '') ? '0' : qty;
    unitprice = (unitprice === '') ? '0' : unitprice;
    var fin_value = (unitprice * qty);
    $('#value_' + id + '').val(fin_value.toFixed(@json(session('logged_session_data.decimal_point'))));


    value = (value === '') ? '0' : value;
    discount = (discount === '') ? '0' : discount;
    var fin_taxableamount = ((unitprice * qty) - Number(discount));
    $('#taxamount_' + id + '').val(fin_taxableamount.toFixed(@json(session('logged_session_data.decimal_point'))));

    var fin_vatableamount = ((unitprice * qty) - Number(discount)) * (Number(net_vat) / 100);
    $('#vatamount_' + id + '').val(fin_vatableamount.toFixed(@json(session('logged_session_data.decimal_point'))));

    var fin_totalamount = (fin_taxableamount + fin_vatableamount);
    $('#totalamount_' + id + '').val(fin_totalamount.toFixed(@json(session('logged_session_data.decimal_point'))));

    calc_total();
}

$(document).on("change", ".unitprice", function () {
    var tot = 0;
    $(".unitprice").each(function() {
        var vale = $(this).val();
        if(!isNaN(parseFloat(vale))){
            tot = parseInt(tot) + parseInt(vale);
        }
    });
    alert(tot);
});


function calc_total()
{
var countrow = document.getElementById('si-row-count').value;

//var countrow = $('#si-table >tbody >tr').length;
var t1=0, t2=0, t3=0, t4=0, t5=0, t6=0, t7=0;
for(var i=1; i<=countrow; i++)
{
t1 += Number($('#qty_'+i).val());
t2 += Number($('#unitprice_'+i).val());
t3 += Number($('#value_'+i).val());
t4 += Number($('#discount_'+i).val());
t5 += Number($('#customcharges_'+i).val());
t6 += Number($('#taxamount_'+i).val());
t7 += Number($('#vatamount_'+i).val());
}
$('#qty_total').text(t1);
$('#unitprice_total').text(t2.toFixed(@json(session('logged_session_data.decimal_point'))));
$('#value_total').text(t3.toFixed(@json(session('logged_session_data.decimal_point'))));
$('#discount_total').text(t4.toFixed(@json(session('logged_session_data.decimal_point'))));
$('#customcharges_total').text(t5.toFixed(@json(session('logged_session_data.decimal_point'))));
$('#taxableamount_total').text(t6.toFixed(@json(session('logged_session_data.decimal_point'))));
$('#vatamount_total').text(t7.toFixed(@json(session('logged_session_data.decimal_point'))));
$('#net_total').text((t6+t7).toFixed(@json(session('logged_session_data.decimal_point'))));
}

function fn_payment_terms()
{
    var val_payment_terms = $('#payment_terms').val();
    if(val_payment_terms==22)
    {
    $('#div_payment_terms').css('display','block');
    }
    else
    {
    $('#div_payment_terms').css('display','none');
    }
}
$('#payment_terms').change();

function fn_shipping_name()
{
var shipping_id = $('#shipping_name').val();
var shipping_data = $('#ship_'+shipping_id).val();        
var ret = shipping_data.split("#");
$('#shipping_address_1').val(ret[0]);
$('#shipping_address_1').focus();
$('#shipping_address_2').val(ret[1]);
$('#shipping_address_2').focus();
$('#shipping_contact_no').val(ret[2]);
$('#shipping_contact_no').focus();
}

</script>



                            </div>

                            <div class="equipment comon-status row mt-25 d-block" style="display:none !important;">
                                <div class="col-lg-12 text-right">
                                    <button type="button" class="primary-btn small fix-gr-bg"
                                        id="addRowEquipment">
                                        <span class="ti-plus pr-2"></span>@lang('lang.item')</button>
                                </div>
                            </div>
                            
                       

                        <div class="row mt-40" style="display: none;">
                            <div class="col-lg-12">
                                <div class="input-effect">
                                    <label class="dynamicslbl">@lang('lang.note') <span></span></label>
                                    <textarea class="primary-input form-control" cols="0" rows="4"
                                        name="note">{{ isset($edit) ? (!empty(@$edit->note) ? @$edit->note : '') : old('description') }}</textarea>
                                    
                                </div>
                            </div>
                        </div>


                        <div class="equipment comon-status row mt-4 d-block">
                            <table class="table table-bordered table-striped" id="pi-table2" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th style="width:100px;">@lang('Name')</th>
                                        <th style="width:350px;">@lang('Credit Account')</th>
                                        <th style="width:70px;">@lang('Amount')</th>
                                        <th style="width:80px;">@lang('Remarks')
                                            <input type="hidden" value="1" id="fright_row" />
                                            <a style="cursor: pointer;" class="btn-md float-right" onclick="add_fright()"><i class="fa fa-plus-square" aria-hidden="true"></i></a></th>
                                    </tr>
                                    <script>
                                        function add_fright()
                                        {
                                            var id = $('#fright_row').val();
                                            id=Number(id)+1;
                                            $('#fright_row').val(id);
                                            $('#fright_row_'+id).css("display", "");
                                        }
                                        
                                        function add_fright_edit(id)
                                        {
                                            $('#fright_row_'+id).css("display", "");
                                        }
                                        function cfc_row_delete(id)
                                        {
                                            $('#fright_row_'+id).remove();
                                            //$(this).closest("tr").remove();
                                        } 
                                    </script>
                                </thead>
                                <tbody>
                                    <tr id="fright_row_1">
                                        <td>
                                            <select class="form-control js-example-basic-single" name="cfc_name[]" id="cfc_name_1">
                                                <option value=""></option>
                                                @foreach ($customs_freight_account as $key => $value)
                                                    <option value="{{ @$value->id }}" {{isset($edit_cfc[0])? !empty(@$edit_cfc[0]->selling_exp_account)? @$edit_cfc[0]->selling_exp_account==@$value->id ? 'selected':'':'':''}}>{{ @$value->account_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control js-example-basic-single" name="cfc_credit_account[]" id="cfc_credit_account_1"
                                                readonly="true">
                                                <option value="none"></option>
                                                @foreach ($supplier as $key => $value)
                                                    <option value="{{ @$value->id }}" {{isset($edit_cfc[0])? !empty(@$edit_cfc[0]->credit_account)? @$edit_cfc[0]->credit_account==@$value->id ? 'selected':'':'':''}}>{{ @$value->account_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" id="cfc_amount_1" name="cfc_amount[]"
                                                autocomplete="off" min="0" step="any" onchange="cfc_amount_change(1)" value="{{ isset($edit_cfc[0]) ? (!empty(@$edit_cfc[0]->amount) ? @$edit_cfc[0]->amount : old('')) : old('') }}">
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" id="cfc_remarks_1" name="cfc_remarks[]"
                                                autocomplete="off" value="{{ isset($edit_cfc[0]) ? (!empty(@$edit_cfc[0]->remarks) ? @$edit_cfc[0]->remarks : old('')) : old('') }}">
                                        </td>
                                    </tr>
                                    <tr style="display: none;" id="fright_row_2">
                                        @if (isset($edit_cfc[1]))
                                        @if (@$edit_cfc[1]->amount != "")
                                        <script>
                                            add_fright_edit(2);
                                        </script>
                                        @endif
                                        @endif
                                        <td>
                                            <select class="form-control js-example-basic-single" name="cfc_name[]" id="cfc_name_2">
                                                <option value=""></option>
                                                @foreach ($customs_freight_account as $key => $value)
                                                    <option value="{{ @$value->id }}" {{isset($edit_cfc[1])? !empty(@$edit_cfc[1]->selling_exp_account)? @$edit_cfc[1]->selling_exp_account==@$value->id ? 'selected':'':'':''}}>{{ @$value->account_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control js-example-basic-single" name="cfc_credit_account[]" id="cfc_credit_account_2"
                                                readonly="true">
                                                <option value="none"></option>
                                                @foreach ($supplier as $key => $value)
                                                    <option value="{{ @$value->id }}" {{isset($edit_cfc[1])? !empty(@$edit_cfc[1]->credit_account)? @$edit_cfc[1]->credit_account==@$value->id ? 'selected':'':'':''}}>{{ @$value->account_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" id="cfc_amount_2" name="cfc_amount[]"
                                                autocomplete="off" min="0" step="any" onchange="cfc_amount_change(2)" value="{{ isset($edit_cfc[1]) ? (!empty(@$edit_cfc[1]->amount) ? @$edit_cfc[1]->amount : old('')) : old('') }}">
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" id="cfc_remarks_2" name="cfc_remarks[]"
                                                autocomplete="off" value="{{ isset($edit_cfc[1]) ? (!empty(@$edit_cfc[1]->remarks) ? @$edit_cfc[1]->remarks : old('')) : old('') }}">
                                        </td>
                                    </tr>
                                    <tr style="display: none;" id="fright_row_3">
                                        @if (isset($edit_cfc[2]))
                                        @if (@$edit_cfc[2]->amount != "")
                                        <script>
                                            add_fright_edit(3);
                                        </script>
                                        @endif
                                        @endif
                                        <td>
                                            <select class="form-control js-example-basic-single" name="cfc_name[]" id="cfc_name_3">
                                                <option value=""></option>
                                                @foreach ($customs_freight_account as $key => $value)
                                                    <option value="{{ @$value->id }}" {{isset($edit_cfc[2])? !empty(@$edit_cfc[2]->selling_exp_account)? @$edit_cfc[2]->selling_exp_account==@$value->id ? 'selected':'':'':''}}>{{ @$value->account_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control js-example-basic-single" name="cfc_credit_account[]" id="cfc_credit_account_3"
                                                readonly="true">
                                                <option value="none"></option>
                                                @foreach ($supplier as $key => $value)
                                                    <option value="{{ @$value->id }}" {{isset($edit_cfc[2])? !empty(@$edit_cfc[2]->credit_account)? @$edit_cfc[2]->credit_account==@$value->id ? 'selected':'':'':''}}>{{ @$value->account_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" id="cfc_amount_3" name="cfc_amount[]"
                                                autocomplete="off" min="0" step="any" onchange="cfc_amount_change(3)" value="{{ isset($edit_cfc[2]) ? (!empty(@$edit_cfc[2]->amount) ? @$edit_cfc[2]->amount : old('')) : old('') }}">
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" id="cfc_remarks_3" name="cfc_remarks[]"
                                                autocomplete="off" value="{{ isset($edit_cfc[2]) ? (!empty(@$edit_cfc[2]->remarks) ? @$edit_cfc[2]->remarks : old('')) : old('') }}">
                                        </td>
                                    </tr>
                                    <tr style="display: none;" id="fright_row_4">
                                        @if (isset($edit_cfc[3]))
                                        @if (@$edit_cfc[3]->amount != "")
                                        <script>
                                            add_fright_edit(4);
                                        </script>
                                        @endif
                                        @endif
                                        <td>
                                            <select class="form-control js-example-basic-single" name="cfc_name[]" id="cfc_name_4">
                                                <option value=""></option>
                                                @foreach ($customs_freight_account as $key => $value)
                                                    <option value="{{ @$value->id }}" {{isset($edit_cfc[3])? !empty(@$edit_cfc[3]->selling_exp_account)? @$edit_cfc[3]->selling_exp_account==@$value->id ? 'selected':'':'':''}}>{{ @$value->account_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control js-example-basic-single" name="cfc_credit_account[]" id="cfc_credit_account_4"
                                                readonly="true">
                                                <option value="none"></option>
                                                @foreach ($supplier as $key => $value)
                                                    <option value="{{ @$value->id }}" {{isset($edit_cfc[3])? !empty(@$edit_cfc[3]->credit_account)? @$edit_cfc[3]->credit_account==@$value->id ? 'selected':'':'':''}}>{{ @$value->account_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" id="cfc_amount_4" name="cfc_amount[]"
                                                autocomplete="off" min="0" step="any" onchange="cfc_amount_change(4)" value="{{ isset($edit_cfc[3]) ? (!empty(@$edit_cfc[3]->amount) ? @$edit_cfc[3]->amount : old('')) : old('') }}">
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" id="cfc_remarks_4" name="cfc_remarks[]"
                                                autocomplete="off" value="{{ isset($edit_cfc[3]) ? (!empty(@$edit_cfc[3]->remarks) ? @$edit_cfc[3]->remarks : old('')) : old('') }}">
                                        </td>
                                    </tr>
                                    <tr style="display: none;" id="fright_row_5">
                                        @if (isset($edit_cfc[4]))
                                        @if (@$edit_cfc[4]->amount != "")
                                        <script>
                                            add_fright_edit(5);
                                        </script>
                                        @endif
                                        @endif
                                        <td>
                                            <select class="form-control js-example-basic-single" name="cfc_name[]" id="cfc_name_5">
                                                <option value=""></option>
                                                @foreach ($customs_freight_account as $key => $value)
                                                    <option value="{{ @$value->id }}" {{isset($edit_cfc[4])? !empty(@$edit_cfc[4]->selling_exp_account)? @$edit_cfc[4]->selling_exp_account==@$value->id ? 'selected':'':'':''}}>{{ @$value->account_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control js-example-basic-single" name="cfc_credit_account[]" id="cfc_credit_account_5"
                                                readonly="true">
                                                <option value="none"></option>
                                                @foreach ($supplier as $key => $value)
                                                    <option value="{{ @$value->id }}" {{isset($edit_cfc[4])? !empty(@$edit_cfc[4]->credit_account)? @$edit_cfc[4]->credit_account==@$value->id ? 'selected':'':'':''}}>{{ @$value->account_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" id="cfc_amount_5" name="cfc_amount[]"
                                                autocomplete="off" min="0" step="any" onchange="cfc_amount_change(5)" value="{{ isset($edit_cfc[4]) ? (!empty(@$edit_cfc[4]->amount) ? @$edit_cfc[4]->amount : old('')) : old('') }}">
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" id="cfc_remarks_5" name="cfc_remarks[]"
                                                autocomplete="off" value="{{ isset($edit_cfc[4]) ? (!empty(@$edit_cfc[4]->remarks) ? @$edit_cfc[4]->remarks : old('')) : old('') }}">
                                        </td>
                                    </tr>


                                </tbody>
                            </table>
                        </div>



                        <div class="row mt-40">
                            <div class="col-lg-12 text-right">
                                <button type="submit" class="btn btn-primary" id="btnSubmit">
                                    <span class="ti-check"></span>
                                    @if (isset($edit))
                                        @lang('lang.update')
                                    @else
                                        @lang('lang.save')
                                    @endif
                                    @lang('Sales Invoice')

                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
        
        <script>
        $('#customer').change();
        </script>

    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>



    {{-- popup --}}
    <form id="po">
        <div class="modal fade admin-query" id="profo_pending_popup_win" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 80%;">
                <div class="modal-content">
                    <div class="modal-header m-0 p-3">
                        <h4 class="modal-title">Invoice Pending List</h4>
                        <button class="close" data-dismiss="modal" type="button">
                            ×
                        </button>
                    </div>
                    <div class="modal-body m-0 p-3">
                        <input type="hidden" id="hd_pending_profo_id" />
                        <div class="container-fluid">

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="equipment comon-status row mt-40 d-block">
                                        <table id="table_id" class="display school-table" cellspacing="0"
                                            width="100%">
                                            <thead>
                                                <tr>
                                                    <th>@lang('#') </th>
                                                    <th>@lang('Part No')</th>
                                                    <th>@lang('Qty')</th>
                                                    <th>@lang('Unit Price')</th>
                                                    <th>@lang('Value')</th>
                                                    <th>@lang('Discount')</th>
                                                    <th>@lang('Taxable Amount')</th>
                                                    <th>@lang('VAT Amount')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="col-lg-12 text-right">
                                        <button class="btn btn-primary bg-warning" data-dismiss="modal" type="button"
                                            id="btn_close2">
                                            @lang('Close')
                                        </button>

                                        <button class="btn btn-primary bg-success" type="button" id="addProfoPendingItems">
                                            Add Selected
                                        </button>
                                        {{-- <input class="btn btn-primary fix-gr-bg" type="" value="save" onclick="return validateAttachForm()"> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    {{-- popup --}}


    <div class="modal fade admin-query" id="add_to_do">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header m-0 p-3">
                    <h4 class="modal-title">Add Shipping</h4>
                    <button class="close" data-dismiss="modal" type="button">
                        ×
                    </button>
                </div>
                <div class="modal-body m-0 p-3">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12 mb-20">
                                <div class="input-effect">
                                    <input class="primary-input form-control {{$errors->has('shipping_name') ? 'is-invalid' : ' '}}" type="text" id="shipping_name_add" name="shipping_name" value="{{isset($editData)?@$editData->shipping_name:old('shipping_name')}}" >
                                    <label class="dynamicslbl">  @lang('Shipping Name') <span>*</span> </label>
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_1 red_alert"></span>                                    
                                </div>
                            </div>
                            <div class="col-lg-12 mb-20">
                                <div class="input-effect">
                                    <input class="primary-input form-control {{$errors->has('contact_name') ? 'is-invalid' : ' '}}" type="text" id="contact_name_add" name="contact_name" value="{{isset($editData)?@$editData->contact_name:old('contact_name')}}" >
                                    <label class="dynamicslbl">  @lang('Contact Name') <span>*</span> </label>
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_2 red_alert"></span>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-20">
                                <div class="input-effect">
                                    <input class="primary-input form-control{{ $errors->has('contact_no') ? ' is-invalid' : '' }}" type="number" id="contact_no_add" name="contact_no" value="{{isset($editData)?@$editData->contact_no:old('contact_no')}}">
                                    <label class="dynamicslbl">  @lang('Contact No') <span>*</span> </label>
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_3 red_alert"></span>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-20">
                                <div class="input-effect">
                                    <input class="primary-input form-control{{ $errors->has('address1') ? ' is-invalid' : '' }}" type="text" id="address1_add" name="address1" value="{{isset($editData)?@$editData->address1:old('address1')}}">
                                    <label class="dynamicslbl">  @lang('Address 1') <span>*</span> </label>  
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_4 red_alert"></span>                                  
                                </div>
                            </div>
                            <div class="col-lg-12 mb-20">
                                <div class="input-effect">
                                    <input class="primary-input form-control{{ $errors->has('address2') ? ' is-invalid' : '' }}" type="text" id="address2_add" name="address2" value="{{isset($editData)?@$editData->address2:old('address2')}}">
                                    <label class="dynamicslbl">  @lang('Address 2') <span>*</span> </label>    
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_5 red_alert"></span>                              
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">    
                                <div class="col-lg-12 text-center">
                                    <div class="mt-40 d-flex justify-content-between">
                                        <button class="primary-btn tr-bg" data-dismiss="modal" type="button" id="btn_close2">
                                            @lang('lang.cancel')
                                        </button>
                                        <input class="primary-btn fix-gr-bg" type="submit" value="save" onclick="return validateAttachForm()">
    
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script>

    function popup_profo_pending(id) {
        $("#loading_bg").css("display", "block");
        $("#hd_pending_profo_id").val(id);
        $("#profo_id").val(id);
        document.getElementById('addProfoPending').click();
        $("#loading_bg").css("display", "none");
    }

    $(document).on("change", "#customer", function () {
        var id = $("#customer").val();
        get_vat(id);
        get_profo_list(id);
    });
    
    function get_vat(id) {
        $("#loading_bg").css("display", "block");        
        var action = "{{ URL::to('get-vat-by-ca') }}";
            $.ajax({
                url: action,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    if (dataResult['data'] == "ERROR") {
                        alert("Error found in something!!");
                        $("#loading_bg").css("display", "none");
                    } else {
                        $("#net_vat").val(dataResult['data'].vat_percentage);
                        $(".vat").val(dataResult['data'].vat_percentage);
                        $("#loading_bg").css("display", "none");     }
                    }
            });
    }

    function get_profo_list(id) {
        $("#loading_bg").css("display", "block");
        var action = "{{ URL::to('get-proforma-invoice-for-si') }}";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                id: id,
            },
            cache: false,
            success: function(dataResult) {
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                var len = 0;
                    if(dataResult['data'] != null){
                        len = dataResult['data'].length;
                    }
                    if(len > 0){
                        for(var i=0; i<len; i++){
                                var id = dataResult['data'][i].id;
                                var doc_number = dataResult['data'][i].doc_number;
                                var option = "<option value='" + id + "'>" + doc_number +
                                    "</option>";
                                var innerHtml =
                                    "<input type='radio' onclick='popup_profo_pending(" + id +
                                    ")' id='pending_grn_" + i +
                                    "' name='pending_grn' value='" + doc_number +
                                    "'> <label for='pending_grn_" + i + "'> " + doc_number +
                                    "</label><br />";

                                $("#plist").append(innerHtml);
                                
                  
                        }                        
                    }
                    else{
                        $("#plist").empty();
                    }
                    $("#loading_bg").css("display", "none");
            }
        });
    }


    function validateAttachForm() {
    var val1 = $("#shipping_name_add").val();
    var val2 = $("#contact_name_add").val();
    var val3 = $("#contact_no_add").val();
    var val4 = $("#address1_add").val();
    var val5 = $("#address2_add").val();

    if (val1 === "") {
        $('.modal_input_validation_1').show();
        $(".modal_input_validation_1").html("<font style='color:red;'>Must be Fill Up</font>");
        $("span.modal_input_validation_1").addClass("red_alert");
        return false;
    }
    if (val2 === "") {
        $('.modal_input_validation_2').show();
        $(".modal_input_validation_2").html("<font style='color:red;'>Must be Fill Up</font>");
        $("span.modal_input_validation_2").addClass("red_alert");
        return false;
    }
    if (val3 === "") {
        $('.modal_input_validation_3').show();
        $(".modal_input_validation_3").html("<font style='color:red;'>Must be Fill Up</font>");
        $("span.modal_input_validation_3").addClass("red_alert");
        return false;
    }
    if (val4 === "") {
        $('.modal_input_validation_4').show();
        $(".modal_input_validation_4").html("<font style='color:red;'>Must be Fill Up</font>");
        $("span.modal_input_validation_4").addClass("red_alert");
        return false;
    }
    if (val5 === "") {
        $('.modal_input_validation_5').show();
        $(".modal_input_validation_5").html("<font style='color:red;'>Must be Fill Up</font>");
        $("span.modal_input_validation_5").addClass("red_alert");
        return false;
    }
    //return true;

    var url = $('#url').val();
    $.ajax({
        type: "POST",
        data: {
            shipping_name: val1,
            contact_name: val2,
            contact_no: val3,
            address1: val4,
            address2: val5
        },

        //url: 'http://syscom.company/venus-erp/shipping-store2',
        //url: 'http://localhost:81/venus-erp/shipping-store2',
        url: url + '/' + 'shipping-store2',
              cache: false,
        success: function(response) {
            var response = JSON.parse(response);
            var len = 0;
            if(response['data']=="ERROR")
            {
                alert("Error found in something!!");
            }
            else{
                if (response['data'] != null) {
                len = response['data'].length;
                }
                if(len > 0){
                    
                    //$('#shipping_name').find('option').not(':first').remove();

                    for(var i=0; i<len; i++){
                        var id = response['data'][i].id;
                        var name = response['data'][i].shipping_name;
                        var option = "<option value='"+id+"'>"+name+"</option>";
                        //$("#shipping_name").append($(option));
                        //$('#shipping_name').append(new Option(name, id));
                        $("#shipping_name").append(option);
                        $("#vendor").append(option);
                    }
                    
                    alert('Shipping Added Successfully!!');
                        $('#btn_close2').click();
                }
            }            
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {}
    });


    //preventDefault();
    }

    function cfc_amount_change(id)
    {
        var amt = $("#cfc_amount_"+id).val();
        $("#cfc_cal_amount_"+id).val(amt);
    }



    function add_invoice_items() {
        $("#loading_bg").css("display", "block");
        var part_number = $("#part_number").val();
        var description = $("#description").val();
        var qty_new = $("#qty_new").val();
        var price_new = $("#price_new").val();
        var value_new = $("#value_new").val();
        var discount_new = $("#discount_new").val();
        var taxable_amount_new = $("#taxable_amount_new").val();
        var vat_amount_new = $("#vat_amount_new").val();
        var total_amount_new = $("#total_amount_new").val();
        
        if (qty_new == "" || qty_new <= 0) {
            alert("Please Add Qty");
            $("#qty_new").focus();
            $("#loading_bg").css("display", "none");
            return false;
        }
        $("#btn_add_invoice_items").attr('disabled', true);

        var action = "{{ URL::to('item-store-updateitem') }}";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                id: id,
                qty: qty,
                price: price,
                description: description,
                remarks: remarks,
                refno: refno,
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
<script>
$(document).ready(function () {
    function initAccountSelect2(selector) {
        $(selector).select2({
            ajax: {
                url: '{{ route("autocomplete.get_cust_account_list_ajax") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        search_text: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.map(function (item) {
                            return {
                                id: item.id,
                                text: item.account_code + ' - ' + item.account_name
                            };
                        })
                    };
                },
                cache: true
            },
            placeholder: 'Select Account',
            minimumInputLength: 2
        });
    }

    // Initial init
    initAccountSelect2('.js-account-select');

    // Re-initialize on focus (if needed for dynamically added fields)
    $(document).on('focus', '.js-account-select', function () {
        if (!$(this).hasClass("select2-hidden-accessible")) {
            initAccountSelect2(this);
        }
    });

    // Open dropdown and focus search box on click
    $(document).on('click', '.js-account-select', function () {
        $(this).select2('open');
    });

    // Focus the search input inside the opened Select2 dropdown
    $(document).on('select2:open', function () {
        setTimeout(function () {
            const searchInput = document.querySelector('.select2-container--open .select2-search__field');
            if (searchInput) {
                searchInput.focus();
            }
        }, 0);
    });
});
</script>
@endsection

@section('script')
    <script>
        // $(document).ready(function () {
        //     $("#btnSubmit").click(function () {
        //         setTimeout(function () { disableButton(); }, 0);
        //     });
        //     function disableButton() {
        //         $("#btnSubmit").prop('disabled', true);
        //     }
        // });
    </script>
@endsection
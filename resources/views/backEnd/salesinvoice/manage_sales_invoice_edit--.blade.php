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
                
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'sales-invoice-update', 'method' => 'POST', 'id' => 'tender-create-form']) }}
                
                <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                <input type="hidden" name="id" value="{{ isset($edit_si) ? $edit_si->id : '' }}">
                <input type="hidden" id="net_vat" name="net_vat" value="{{round($company->net_vat)}}">
                
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
                                                <select class="form-control js-example-basic-single" name="customer" id="customer" required>
                                                    <option value=""></option>
                                                    @foreach ($customer as $value)
                                                        <option value="{{ @$value->id }}"
                                                            {{ isset($edit_si) ? (!empty($edit_si->customer) ? (@$edit_si->customer == @$value->id ? 'selected' : '') : '') : '' }}>{{ @$value->account_name }}
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
                                                        $invno=@App\SysHelper::get_new_maxid('sys_sales_invoice','id');
                                                        if($invno==1001){$invno=1034;}
                                                    ?>

                                                    <input class="form-control" type="text" name="doc_number" autocomplete="off" id="doc_number" value="{{ isset($edit_si) ? (!empty(@$edit_si->doc_number) ? @$edit_si->doc_number : old('doc_number')) : 'SIV-' . $invno }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">Invoice Date</label>
                                                    @php
                                                    $value = date('Y-m-d');
                                                    if(isset($edit_si) && !empty($edit_si->doc_date) ){ @$value =
                                                    date('Y-m-d', strtotime(@$edit_si->doc_date)); }
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
                                                ?>
                                                <select class="form-control js-example-basic-single" name="currency" id="currency">
                                                    {{-- <option data-display="@lang('Currency') *" value="">@lang('Currency') *</option> --}}

                                                    @foreach ($currency as $value)
                                                        <option value="{{ @$value->id }}"
                                                            @if (isset($edit))
                                                                @if($edit_si->currency == @$value->id) selected @endif
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
                                                style="width: 100%; height: 175px; border: solid 1px #dfe1d7; border-radius: 5px; padding: 5px 5px; overflow-y: scroll;">
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
                                                    <input class="form-control" type="text" name="delivery_terms" autocomplete="off" id="delivery_terms" value="{{ isset($edit_si) ? (!empty(@$edit_si->delivery_terms) ? @$edit_si->delivery_terms : old('delivery_terms')) : 'Ex-Dubai' }}" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">@lang('Printed Invoice Number')<span></span></label>
                                                    <input class="form-control" type="text" name="printed_invoice_number" autocomplete="off" id="printed_invoice_number" value="{{ isset($edit_si) ? (!empty(@$edit_si->printed_invoice_number) ? @$edit_si->printed_invoice_number : old('printed_invoice_number')) : '' }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-4 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">@lang('Salesman')<span>*</span></label>
                                                    <select class="form-control" name="sales_man" id="sales_man" required>
                                                        <option value="">-Select-</option>
                                                        @foreach ($staff as $value)
                                                        <option value="{{ @$value->user_id }}"
                                                            @if(isset($edit_si)) @if($edit_si->sales_man == $value->user_id) selected @endif @else @if($value->user_id == Auth::user()->id) selected  @endif @endif
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
                                                             <option value="{{@$value->id}}" {{isset($edit_si)? !empty(@$edit_si->payment_terms)? @$edit_si->payment_terms==@$value->id ? 'selected':'':'':''}} >{{@$value->title}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 mb-2">
                                            <div class="input-effect">
                                                <label class="dynamicslbl">@lang('Created') @lang('By')<span>*</span></label>
                                                <input class="form-control" type="text" name="createdby" autocomplete="off" id="createdby" value="{{ isset($edit_si) ? (!empty(@$edit_si->created_by) ? @$edit_si->createdby->full_name : old('createdby')) : Auth::user()->full_name }}" readonly>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    
                                
                                <div class="col-lg-3 mb-2" style="display: none;">
                                    
                                    <div class="input-effect">
                                        <label class="dynamicslbl">@lang('Other Payment Terms')<span>*</span></label>
                                        <input class="form-control"
                                            type="text" name="payment_terms2" autocomplete="off"
                                            id="payment_terms2"
                                            value="{{ isset($edit_si) ? (!empty(@$edit_si->payment_terms2) ? @$edit_si->payment_terms2 : old('payment_terms2')) : '' }}">
                                        <span class="focus-border"></span>
                                        @if ($errors->has('payment_terms2'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('payment_terms2') }}</strong>
                                            </span>
                                        @endif
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
                                        <input type="text" class="form-control" id="shipping_name" name="shipping_name" value="{{ @$edit_si->shipping_name }}">
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="dynamicslbl">@lang('Address') <span></span></label>
                                        <input type="text" class="form-control" id="shipping_address" name="shipping_address" value="{{ @$edit_si->shipping_address }}">
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
                                                        <option value="{{@$value->id}}" {{isset($edit_si)? !empty(@$edit_si->customer_type)? @$edit_si->customer_type==@$value->id ? 'selected':'':'':''}} >{{@$value->title}}</option>
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
                                                        <option value="{{@$value->id}}" {{isset($edit_si)? !empty(@$edit_si->sale_type)? @$edit_si->sale_type==@$value->id ? 'selected':'':'':''}} >{{@$value->title}}</option>
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
                                                        @if (isset($edit_si)) @if (@$edit_si->customer_country == $value->id) selected @endif
                                                        @endif
                                                        <?php } catch (\Throwable $th) {} ?>
                                                        >{{ @$value->name }} </option>
                                                @endforeach
                                            </select>
                                            
                                        </div>
                                    </div>
                                    <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="dynamicslbl">@lang('Customer State') <span></span></label>
    
                                            <div id="sectionStateDiv">
                                                <select class="form-control" name="customer_state" id="state">
                                                    <option data-display="" value="0"></option>
                                                    <?php try{?>
                                                        @foreach ($states as $key => $value)
                                                    @if (isset($edit))
                                                        <option data-display="{{ $edit->vatstate->name }}"
                                                            value="{{ $edit->customer_state }}" selected>
                                                            {{ $edit->vatstate->name }}</option>
                                                            @else
                                                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                                                    @endif
                                                    @endforeach
                                                    <?php } catch (\Throwable $th) {} ?>
                                                </select>
                                            </div>
    
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <div class="tab-pane" id="enduser" role="tabpanel" aria-labelledby="enduser-tab">
                            <div class="row mt-2">
                                <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="dynamicslbl">@lang('End User Name') <span></span></label>
                                            <input type="text" class="form-control" name="end_user_name" id="end_user_name" autocomplete="off" value="{{ isset($edit_si) ? (!empty(@$edit_si->end_user_name) ? @$edit_si->end_user_name : '') : old('end_user_name') }}" />
                                            
                                        </div>
                                    </div>
                                    <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="dynamicslbl">@lang('Contact Person Name') <span></span></label>
                                            <input type="text" class="form-control" name="contact_person_name" id="contact_person_name" autocomplete="off" value="{{ isset($edit_si) ? (!empty(@$edit_si->contact_person_name) ? @$edit_si->contact_person_name : '') : old('contact_person_name') }}">
                                            
                                        </div>
                                    </div>
                                    <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="dynamicslbl">@lang('Contact Person Email') <span></span></label>
                                            <input type="text" class="form-control" name="contact_person_email" id="contact_person_email" autocomplete="off" value="{{ isset($edit_si) ? (!empty(@$edit_si->contact_person_email) ? @$edit_si->contact_person_email : '') : old('contact_person_email') }}">
                                            
                                        </div>
                                    </div>
                                    <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="dynamicslbl">@lang('Contact Person No') <span></span></label>
                                            <input type="text" class="form-control" name="contact_person_no" id="contact_person_no" autocomplete="off" value="{{ isset($edit_si) ? (!empty(@$edit_si->contact_person_no) ? @$edit_si->contact_person_no : '') : old('contact_person_no') }}">
                                            
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
                            {{--  <a class="btn btn-success mb-2 pb-0 pt-0 float-right" onclick="updiv()">Add Items</a>  --}}
                            <script>
                                function updiv() {
                                    if($('#div_update').css('display') == 'none'){
                                        $("#div_update").css("display", "block");
                                    }
                                    else{
                                        $("#div_update").css("display", "none");
                                    }
                                }
                            </script>
                            <div id="div_update" style="display: none;">
                            <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th style="width:100px;">@lang('Part No')</th>
                                        <th style="width:150px;">@lang('Description')</th>
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
                                    <tr>
                                        <td>
                                            <select class="form-control js-example-basic-single" name="part_number" id="part_number" onchange="ddl_part_change1()">
                                                <option value="none"></option>
                                                @foreach ($items as $key => $value)
                                                    <option value="{{ @$value->id }}">{{ @$value->part_number }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control" name="part_number_new" id="part_number_new" readonly="true" hidden>
                                                <option value="none"></option>
                                                @foreach ($items as $key => $value)
                                                    <option value="{{ @$value->id }}">{{ @$value->description }}</option>
                                                @endforeach
                                            </select>
                                            <input class="form-control" type="text" id="description_new" name="description_new" autocomplete="off" readonly="true" value="">
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" id="qty_new" name="qty_new" autocomplete="off" min="0" onchange="calc_change1()" value="">
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" id="price_new" name="price_new" autocomplete="off" min="0" onchange="calc_change1()" value="">
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" id="value_new" name="value_new" autocomplete="off" min="0" readonly value="">
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" id="discount_new" name="discount_new" autocomplete="off" min="0" onchange="calc_change1()" value="">
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" id="taxable_amount_new" name="taxable_amount_new" autocomplete="off" min="0" onchange="calc_change1()" value="" readonly>
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" id="vat_amount_new" name="vat_amount_new" autocomplete="off" min="0" onchange="calc_change1()" value="" readonly>
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" id="total_amount_new" name="total_amount_new" autocomplete="off" min="0" onchange="calc_change1()" value="" readonly>
                                        </td>
                                        </tr>
                                        <tr>
                                        <td colspan="9">
                                            <input type="hidden" name="doc_number" value="" />
                                            <button class="float-right btn btn-danger btn-xs" id="btn_add_invoice_items" title="Add" onclick="add_invoice_items()" >Add Item</button>
                                            <script>
                                                function calc_change1() {
                                                    var net_vat = $('#net_vat').val();                                                
                                                    var qty = $('#qty_new').val();
                                                    var unitprice = $('#price_new').val();
                                                    var value = $('#value_new').val();
                                                    var discount = $('#discount_new').val();
                                                    var taxamount = $('#taxable_amount_new').val();
                                                    var vatamount = $('#vat_amount_new').val();
                                                    var totalamount = $('#total_amount_new').val();
                                                
                                                
                                                    qty = (qty === '') ? '0' : qty;
                                                    unitprice = (unitprice === '') ? '0' : unitprice;
                                                    var fin_value = (unitprice * qty);
                                                    $('#value_new').val(fin_value.toFixed(@json(session('logged_session_data.decimal_point'))));
                                                
                                                
                                                    value = (value === '') ? '0' : value;
                                                    discount = (discount === '') ? '0' : discount;
                                                    var fin_taxableamount = ((unitprice * qty) - Number(discount));
                                                    $('#taxable_amount_new').val(fin_taxableamount.toFixed(@json(session('logged_session_data.decimal_point'))));
                                                
                                                    var fin_vatableamount = ((unitprice * qty) - Number(discount)) * (Number(net_vat) / 100);
                                                    $('#vat_amount_new').val(fin_vatableamount.toFixed(@json(session('logged_session_data.decimal_point'))));
                                                
                                                    var fin_totalamount = (fin_taxableamount + fin_vatableamount);
                                                    $('#total_amount_new').val(fin_totalamount.toFixed(@json(session('logged_session_data.decimal_point'))));
                                                
                                                }
                                                function ddl_part_change1(){
                                                    var selOpt = $('#part_number :selected').val();
                                                    $('#part_number_new option[value='+selOpt+']').attr('selected','selected');
                                                    var selOpt2 = $('#part_number_new :selected').text();
                                                    $('#description_new').val(selOpt2);
                                                    $('#description_new').focus();
                                                }
                                            </script>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            </div>

                            <table class="table table-bordered table-striped" id="si-table" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th style="width:100px;">@lang('Part No')</th>
                                        <th style="width:150px;">@lang('Description')</th>
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
                                    @for ($roid= 1;  $roid <= count($edit_si_items) ; $roid++)
                                    <tr id="rowone{{$roid}}" onclick="fn_addRow({{$roid}})">
                                        <td><select class="form-control js-example-basic-single" name="part_number[]" id="part_number_{{$roid}}" onchange="ddl_part_change({{$roid}})">
                                                <option value="none"></option>
                                                @foreach ($items as $key => $value)
                                                <option value="{{ @$value->id }}" {{isset($edit_si_items[$roid-1])? !empty(@$edit_si_items[$roid-1]->part_number)? @$edit_si_items[$roid-1]->part_number==@$value->id ? 'selected':'':'':''}}>{{ @$value->part_number }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control" name="part_number_txt[]" id="part_number_txt_{{$roid}}" readonly="true" hidden>
                                                <option value="none"></option>
                                                @foreach ($items as $key => $value)
                                                <option value="{{ @$value->id }}" {{isset($edit_si_items[$roid-1])? !empty(@$edit_si_items[$roid-1]->part_number_txt)? @$edit_si_items[$roid-1]->part_number_txt==@$value->id ? 'selected':'':'':''}}>{{ @$value->description }}</option>
                                                @endforeach
                                            </select>
                                            @if (isset($edit_si_items[$roid-1])) @if(!empty(@$edit_si_items[$roid-1]->part_number))
                                                            @php $abc =  @App\SmItem::select('description')->where('id',@$edit_si_items[$roid-1]->part_number)->first(); @endphp
                                                            @endif @endif
                                                            <input class="form-control" type="text" id="description_{{$roid}}" name="description[]" autocomplete="off" readonly="true"
                                                            value="{{ $abc->description  }}" >
                                        </td>
                                        {{--  <td>
                                            <select class=" sstxtbx" name="tax[]" id="tax_{{$roid}}" readonly="true" onchange="calc_change({{$roid}})">
                                                <option value="{{round($company->net_vat)}}">VAT {{round($company->net_vat)}}%</option>
                                                <option value="0">None</option>
                                            </select>
                                        </td>  --}}
                                        <td>
                                            <input class="form-control" type="number" id="qty_{{$roid}}" name="qty[]" value="{{@$edit_si_items[$roid-1]->qty}}" autocomplete="off" min="0" onchange="calc_change({{$roid}})">
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" id="unitprice_{{$roid}}" name="unitprice[]" value="{{@$edit_si_items[$roid-1]->unitprice}}" autocomplete="off" min="0" onchange="calc_change({{$roid}})">
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" id="value_{{$roid}}" name="value[]" value="{{@$edit_si_items[$roid-1]->value}}" autocomplete="off" min="0" readonly>
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" id="discount_{{$roid}}" name="discount[]" value="{{@$edit_si_items[$roid-1]->discount}}" autocomplete="off" min="0" onchange="calc_change({{$roid}})">
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" id="customcharges_{{$roid}}" name="customcharges[]" value="{{@$edit_si_items[$roid-1]->customcharges}}" autocomplete="off" min="0" onchange="calc_change({{$roid}})">
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" id="taxableamount_{{$roid}}" name="taxableamount[]" value="{{@$edit_si_items[$roid-1]->taxableamount}}" autocomplete="off" min="0" readonly>
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" id="vatamount_{{$roid}}" name="vatamount[]" value="{{@$edit_si_items[$roid-1]->vatamount}}" autocomplete="off" min="0" readonly>
                                        </td>
                                    </tr>
                                    @endfor
                                    <?php /*$roid--;*/?>
                                </tbody>
                                <thead>
                                    <?php $roid--; ?>
                                    <input type="hidden" id="si-row-count" value="{{ $roid }}">
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th class="sstablefoot"><label id="qty_total">0</label></th>
                                        <th class="sstablefoot"><label id="unitprice_total">0.00</label></th>
                                        <th class="sstablefoot"><label id="value_total">0.00</label></th>
                                        <th class="sstablefoot"><label id="discount_total">0.00</label></th>
                                        <th class="sstablefoot"><label id="taxableamount_total">0.00</label></th>
                                        <th class="sstablefoot"><label id="vatamount_total">0.00</label></th>
                                        <th class="sstablefoot"><label id="net_total">0.00</label></th>
                                    </tr>
                                </thead>
                            </table>
                            <div style="display: none;">
                                <button type="button" class="primary-btn small fix-gr-bg" id="addRowSI"><span class="ti-plus pr-2"></span>@lang('lang.item')</button>
                            </div>

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
t6 += Number($('#taxableamount_'+i).val());
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
                            <table class="table table-bordered table-striped" id="po-table" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th style="width:100px;">@lang('Name')</th>
                                        <th style="width:350px;">@lang('Credit Account')</th>
                                        <th style="width:70px;">@lang('Amount')</th>
                                        <th style="width:80px;">@lang('Remarks')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <select class="form-control" name="cfc_name[]" id="cfc_name_1">
                                                <option value=""></option>
                                                <option value="3567" {{isset($edit_cfc[0])? !empty(@$edit_cfc[0]->cfc_name)? @$edit_cfc[0]->cfc_name=='3567' ? 'selected':'':'':''}}>Customs</option>
                                                <option value="3566" {{isset($edit_cfc[0])? !empty(@$edit_cfc[0]->cfc_name)? @$edit_cfc[0]->cfc_name=='3566' ? 'selected':'':'':''}}>Freight</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control js-example-basic-single" name="cfc_credit_account[]" id="cfc_credit_account_1">
                                                <option value="none"></option>
                                                @foreach ($supplier as $key => $value)
                                                <option value="{{ @$value->id }}" {{isset($edit_cfc[0])? !empty(@$edit_cfc[0]->cfc_credit_account)? @$edit_cfc[0]->cfc_credit_account==@$value->id ? 'selected':'':'':''}}>{{ @$value->account_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" id="cfc_amount_1" name="cfc_amount[]" autocomplete="off" min="0" onchange="cfc_amount_change(1)"
                                            value="{{ isset($edit_cfc[0]) ? (!empty(@$edit_cfc[0]->cfc_amount) ? @$edit_cfc[0]->cfc_amount : old('')) : old('') }}" >
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" id="cfc_remarks_1" name="cfc_remarks[]" autocomplete="off" value="{{ isset($edit_cfc[0]) ? (!empty(@$edit_cfc[0]->cfc_remarks) ? @$edit_cfc[0]->cfc_remarks : old('')) : old('') }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <select class="form-control" name="cfc_name[]" id="cfc_name_2">
                                                <option value=""></option>
                                                <option value="3567" {{isset($edit_cfc[1])? !empty(@$edit_cfc[1]->cfc_name)? @$edit_cfc[1]->cfc_name=='3567' ? 'selected':'':'':''}}>Customs</option>
                                                <option value="3566" {{isset($edit_cfc[1])? !empty(@$edit_cfc[1]->cfc_name)? @$edit_cfc[1]->cfc_name=='3566' ? 'selected':'':'':''}}>Freight</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control js-example-basic-single" name="cfc_credit_account[]" id="cfc_credit_account_2">
                                                <option value="none"></option>
                                                @foreach ($supplier as $key => $value)
                                                <option value="{{ @$value->id }}" {{isset($edit_cfc[1])? !empty(@$edit_cfc[1]->cfc_credit_account)? @$edit_cfc[1]->cfc_credit_account==@$value->id ? 'selected':'':'':''}}>{{ @$value->account_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" id="cfc_amount_2" name="cfc_amount[]" autocomplete="off" min="0" onchange="cfc_amount_change(2)"
                                            value="{{ isset($edit_cfc[1]) ? (!empty(@$edit_cfc[1]->cfc_amount) ? @$edit_cfc[1]->cfc_amount : old('')) : old('') }}" >
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" id="cfc_remarks_2" name="cfc_remarks[]" autocomplete="off"
                                            value="{{ isset($edit_cfc[1]) ? (!empty(@$edit_cfc[1]->cfc_remarks) ? @$edit_cfc[1]->cfc_remarks : old('')) : old('') }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <select class="form-control" name="cfc_name[]" id="cfc_name_3">
                                                <option value=""></option>
                                                <option value="3567" {{isset($edit_cfc[2])? !empty(@$edit_cfc[2]->cfc_name)? @$edit_cfc[2]->cfc_name=='3567' ? 'selected':'':'':''}}>Customs</option>
                                                <option value="3566" {{isset($edit_cfc[2])? !empty(@$edit_cfc[2]->cfc_name)? @$edit_cfc[2]->cfc_name=='3566' ? 'selected':'':'':''}}>Freight</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control js-example-basic-single" name="cfc_credit_account[]" id="cfc_credit_account_3">
                                                <option value="none"></option>
                                                @foreach ($supplier as $key => $value)
                                                <option value="{{ @$value->id }}" {{isset($edit_cfc[2])? !empty(@$edit_cfc[2]->cfc_credit_account)? @$edit_cfc[2]->cfc_credit_account==@$value->id ? 'selected':'':'':''}}>{{ @$value->account_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" id="cfc_amount_3" name="cfc_amount[]" autocomplete="off" min="0" onchange="cfc_amount_change(3)"
                                            value="{{ isset($edit_cfc[2]) ? (!empty(@$edit_cfc[2]->cfc_amount) ? @$edit_cfc[2]->cfc_amount : old('')) : old('') }}" >
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" id="cfc_remarks_3" name="cfc_remarks[]" autocomplete="off"
                                            value="{{ isset($edit_cfc[2]) ? (!empty(@$edit_cfc[2]->cfc_remarks) ? @$edit_cfc[2]->cfc_remarks : old('')) : old('') }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <select class="form-control" name="cfc_name[]" id="cfc_name_4">
                                                <option value=""></option>
                                                <option value="3567" {{isset($edit_cfc[3])? !empty(@$edit_cfc[3]->cfc_name)? @$edit_cfc[3]->cfc_name=='3567' ? 'selected':'':'':''}}>Customs</option>
                                                <option value="3566" {{isset($edit_cfc[3])? !empty(@$edit_cfc[3]->cfc_name)? @$edit_cfc[3]->cfc_name=='3566' ? 'selected':'':'':''}}>Freight</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control js-example-basic-single" name="cfc_credit_account[]" id="cfc_credit_account_4">
                                                <option value="none"></option>
                                                @foreach ($supplier as $key => $value)
                                                <option value="{{ @$value->id }}" {{isset($edit_cfc[3])? !empty(@$edit_cfc[3]->cfc_credit_account)? @$edit_cfc[3]->cfc_credit_account==@$value->id ? 'selected':'':'':''}}>{{ @$value->account_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" id="cfc_amount_4" name="cfc_amount[]" autocomplete="off" min="0" onchange="cfc_amount_change(4)"
                                            value="{{ isset($edit_cfc[3]) ? (!empty(@$edit_cfc[3]->cfc_amount) ? @$edit_cfc[3]->cfc_amount : old('')) : old('') }}" >
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" id="cfc_remarks_4" name="cfc_remarks[]" autocomplete="off"
                                            value="{{ isset($edit_cfc[3]) ? (!empty(@$edit_cfc[3]->cfc_remarks) ? @$edit_cfc[3]->cfc_remarks : old('')) : old('') }}">
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
        get_profo_list(id);
    });
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
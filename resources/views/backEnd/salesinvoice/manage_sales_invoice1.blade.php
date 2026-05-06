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
                    <a data-modal-size="modal-md" data-target="#attachment_popup_win" data-toggle="modal" class="btn btn-primary" onclick="view_attachment()"><i class="fa fa-plus"></i> Attachment</a>
                    <a href="{{ url('sales-invoice/create') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> New</a>
                    <a href="{{ url('sales-invoice') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> List</a>
                </div>
            </div>
            <div class="card p-4 mb-2">
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'sales-invoice-store', 'method' => 'POST', 'id' => 'sales-invoice-create-form']) }}
                <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                <input type="hidden" name="id" value="{{ isset($edit) ? $edit->id : '' }}">
                <input type="hidden" id="net_vat" name="net_vat">
                
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
                                                    {{-- @foreach ($customer as $value)
                                                        <option value="{{ @$value->id }}"
                                                            {{ isset($edit) ? (!empty($edit->vendor_id) ? (@$edit->vendor_id == @$value->id ? 'selected' : '') : '') : '' }}>{{ @$value->account_name }}
                                                        </option>
                                                    @endforeach --}}
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
                                                ?>
                                                <select class="form-control js-example-basic-single" name="currency" id="currency">
                                                    {{-- <option data-display="@lang('Currency') *" value="">@lang('Currency') *</option> --}}

                                                    @foreach ($currency as $value)
                                                        <option value="{{ @$value->id }}"
                                                            @if($company->currency_id == $value->id) selected @endif>
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
                                            <div class="col-lg-4 mb-2" style="display: none;">
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
                                                            @if(isset($edit)) @if($edit->sales_man == $value->user_id) selected @endif @else @if($value->user_id == Auth::user()->id) selected  @endif @endif
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
                                                             <option value="{{@$value->id}}" {{isset($edit)? !empty(@$edit->payment_terms)? @$edit->payment_terms==@$value->id ? 'selected':'':'':''}} >{{@$value->title}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                            <div id="div_payment_terms" style="display: none; padding-top: px;">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Other Payment Terms')<span>*</span></label>
                                    <input
                                        class="txtbx primary-input form-control {{ $errors->has('payment_terms2') ? ' is-invalid' : '' }}"
                                        type="text" name="payment_terms2" autocomplete="off" id="payment_terms2"
                                        value="{{ isset($edit) ? (!empty(@$edit->payment_terms2) ? @$edit->payment_terms2 : old('payment_terms2')) : '' }}">
                                </div>
                            </div>
                                            </div>
                                            <div class="col-lg-4 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">LPO/Reference No<span>*</span></label>
                                                    <input class="form-control" type="text" name="reference_no" autocomplete="off" id="reference_no" value="" onchange="updateNarration()" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">LPO/Reference Date<span>*</span></label>
                                                    <input class="form-control" type="date" name="reference_date" autocomplete="off" id="reference_date" value="" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 mb-2" id="div_deal_id" style="display: none;">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">Deal ID<span>*</span></label>
                                                    <input class="form-control" type="text" name="deal_id" autocomplete="off" id="deal_id" value="Without Deal" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">Supplier Name<span>*</span></label>
                                                    <input class="form-control" type="text" name="supplier_name" autocomplete="off" id="supplier_name" value="TAKEN FROM STOCK" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">@lang('Created') @lang('By')<span>*</span></label>
                                                    <input class="form-control" type="text" name="createdby" autocomplete="off" id="createdby" value="{{ isset($edit) ? (!empty(@$edit->number) ? @$edit->number : old('createdby')) : Auth::user()->full_name }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">@lang('Create Deal')</label>
                                                    <select class="form-control" name="create_deal" id="create_deal" required onchange="create_deal_change()">
                                                        <option value="1">Yes</option>
                                                        <option value="0">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <script>
                                                function create_deal_change()
                                                {
                                                    if($('#create_deal').val()==1){
                                                        $('#div_deal_id').css('display','none');
                                                        $('#supplier_name').val('TAKEN FROM STOCK');

                                                    } else{
                                                        $('#div_deal_id').css('display','');
                                                        $('#supplier_name').val('');
                                                    }
                                                }
                                            </script>
                                            <div class="col-lg-4 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">@lang('Create Delivery Note')</label>
                                                    <select class="form-control" name="create_dn" id="create_dn" required>
                                                        <option value="">Select</option>
                                                        <option value="0">No</option>
                                                        <option value="1">Yes</option>
                                                    </select>
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
                        @if(session('logged_session_data.company_id') == 8 || session('logged_session_data.company_id') == 10)
                        <li class="nav-item">
                          <a class="nav-link" id="arabic-tab" data-toggle="tab" href="#arabic" role="tab" aria-controls="arabic" aria-selected="false">Arabic Address</a>
                        </li>
                        @endif
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="shipping" role="tabpanel" aria-labelledby="shipping-tab">
                            <div class="row mt-2">
                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="dynamicslbl">@lang('Name') <span></span></label>
                                        <input type="text" class="form-control" value="" id="shipping_name" name="shipping_name">
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="dynamicslbl">@lang('Address') <span></span></label>
                                        <input type="text" class="form-control" value="" id="shipping_address" name="shipping_address">
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
                                                        <option value="{{@$value->id}}" {{isset($edit)? !empty(@$edit->customer_type)? @$edit->customer_type==@$value->id ? 'selected':'':'':''}} >{{@$value->title}}</option>
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
                                                        <option value="{{@$value->id}}" {{isset($edit)? !empty(@$edit->sale_type)? @$edit->sale_type==@$value->id ? 'selected':'':'':''}} >{{@$value->title}}</option>
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
                                                        @if (isset($edit)) @if (@$edit->customer_country == $value->id) selected @endif
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
                                            <input type="text" class="form-control" name="end_user_name" id="end_user_name" autocomplete="off" value="{{ isset($edit) ? (!empty(@$edit->end_user_name) ? @$edit->end_user_name : '') : old('end_user_name') }}" />
                                            
                                        </div>
                                    </div>
                                    <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="dynamicslbl">@lang('Contact Person Name') <span></span></label>
                                            <input type="text" class="form-control" name="contact_person_name" id="contact_person_name" autocomplete="off" value="{{ isset($edit) ? (!empty(@$edit->contact_person_name) ? @$edit->contact_person_name : '') : old('contact_person_name') }}">
                                            
                                        </div>
                                    </div>
                                    <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="dynamicslbl">@lang('Contact Person Email') <span></span></label>
                                            <input type="text" class="form-control" name="contact_person_email" id="contact_person_email" autocomplete="off" value="{{ isset($edit) ? (!empty(@$edit->contact_person_email) ? @$edit->contact_person_email : '') : old('contact_person_email') }}">
                                            
                                        </div>
                                    </div>
                                    <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="dynamicslbl">@lang('Contact Person No') <span></span></label>
                                            <input type="text" class="form-control" name="contact_person_no" id="contact_person_no" autocomplete="off" value="{{ isset($edit) ? (!empty(@$edit->contact_person_no) ? @$edit->contact_person_no : '') : old('contact_person_no') }}">
                                            
                                        </div>
                                    </div>
                                </div>
                        </div>
                        @if(session('logged_session_data.company_id') == 8 || session('logged_session_data.company_id') == 10)
                        <div class="tab-pane" id="arabic" role="tabpanel" aria-labelledby="arabic-tab">
                            <div class="row mt-2">
                                <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="dynamicslbl">@lang('Company Name in Arabic') <span></span></label>
                                            <input type="text" class="form-control text-right" name="company_name_ar" id="company_name_ar" autocomplete="off" />
                                            
                                        </div>
                                    </div>
                                    <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="dynamicslbl">@lang('Contact Person in Arabic') <span></span></label>
                                            <input type="text" class="form-control text-right" name="contact_person_ar" id="contact_person_ar" autocomplete="off" />
                                            
                                        </div>
                                    </div>
                                    <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="dynamicslbl">@lang('Address in Arabic') <span></span></label>
                                            <textarea class="form-control text-right" name="address_ar" id="address_ar" rows="4"></textarea>
                                            
                                        </div>
                                    </div>
                                </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>





                        <div class="equipment comon-status row d-block">
                            <hr />
                            <h6 class="primary-color">@lang('Item Details'):</h6> 
                            
                            <table class="table table-bordered table-striped" id="table_id" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th style="width:50px;">@lang('SL')</th>
                                        <th style="width:150px;">@lang('Part No')</th>
                                        <th style="width:150px;">@lang('Description')</th>
                                        <th style="width:100px;">@lang('Cost')</th>
                                        <th style="width:100px;">@lang('VAT')</th>
                                        <th style="width:100px;">@lang('Qty')</th>
                                        <th style="width:120px;">@lang('Unit Price')</th>
                                        <th style="width:120px;">@lang('Value')</th>
                                        <th style="width:100px;">@lang('Discount')</th>
                                        <th style="width:130px;">@lang('Taxable Amount')</th>
                                        <th style="width:130px;">@lang('VAT Amount')</th>
                                        <th style="width:130px;">@lang('Total')</th>
                                        <th style="width:130px;">@lang('Serial No')</th>
                                        <th style="width:20px;"></th>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input class="form-control" type="number" id="sort_id" name="sort_id[]" >
                                        </td>
                                        <td><input type="checkbox" checked hidden>
                                            <select class="form-control js-product-select" name="part_number[]" id="part_number_new">
                                                <option value="none"></option>
                                                {{-- @foreach ($items as $key => $value)
                                                    <option value="{{ @$value->id }}">{{ @$value->part_number }}</option>
                                                @endforeach --}}
                                            </select>
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" id="description_new" name="description[]" autocomplete="off" readonly="true">
                                        </td>
                                        <td>
                                            <input class="form-control cost" type="number" id="cost" step="any" name="cost[]" autocomplete="off" min="0">
                                        </td>
                                        <td>
                                            <input class="form-control vat" type="number" id="vat" name="vat[]" autocomplete="off" min="0">
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" id="qty" name="qty[]" autocomplete="off" min="0" onchange="calc_change_new()">
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" id="unitprice" step="any" name="unitprice[]" autocomplete="off" min="0" onchange="calc_change_new()">
                                        </td>
                                        <script>
                                            $("#unitprice").on('keyup', function (e) {
                                                if (e.key === 'Enter' || e.keyCode === 13) {
                                                    calc_change_new();
                                                    if($('#btn_add_row').css('display') == 'none'){
                                                        $('#update_add_row').click();
                                                    }
                                                    if($('#update_add_row').css('display') == 'none'){
                                                        $('#btn_add_row').click();
                                                    }
                                                }
                                            });
                                        </script>
                                        <td>
                                            <input class="form-control" type="number" id="value" name="value[]" autocomplete="off" min="0" readonly>
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" id="discount" step="any" name="discount[]" autocomplete="off" min="0" value="0" onchange="calc_change_new()">
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" id="taxableamount" name="taxableamount[]" autocomplete="off" min="0" readonly>
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" id="vatamount" name="vatamount[]" autocomplete="off" min="0" readonly>
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" id="totalamount" name="totalamount[]" autocomplete="off" min="0" readonly>
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" id="serialno" name="serialno[]" autocomplete="off" onclick="srlno_add()">
                                        </td>
                                        <td>
                                            <input type="hidden" id="cart_item_id" />
                                            <input type="hidden" id="deal_ref_id" />
                                            <a id="btn_add_row" onclick="return add_rows()" class="btn btn-primary"><i class="fa fa-plus" aria-hidden="true"></i></a>
                                            <a id="update_add_row" style="display: none;" onclick="return row_update()" class="btn btn-warning">Update</a>
                                        </td>
                                    </tr>
{{-- Product Search --}}
<script>
$(document).ready(function () {
    let $sortInputs = $("input[name='sortid[]']");
    let firstVal = $sortInputs.first().val();
    if (!firstVal || firstVal == 0) {
        $sortInputs.each(function (index) {
            $(this).val(index + 1);
        });
    }
    let lastVal = parseInt($sortInputs.last().val()) || 0;
    $("#sort_id").val(lastVal + 1);
});
</script>
<script>
$(document).ready(function () {
    function initAccountSelect2(selector) {
        $(selector).select2({
            ajax: {
                url: '{{ route("autocomplete.get_product_list_ajax") }}',
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
                                text: item.part_number,
                                description: item.description
                            };
                        })
                    };
                },
                cache: true
            },
            placeholder: 'Select Product',
            minimumInputLength: 2,
            dropdownParent: $(selector).parent() // optional: ensures dropdown shows in modals
        });

        $(selector).on('select2:select', function (e) {
            var selectedData = e.params.data;
            $('#description_new').val(selectedData.description || '');
        });
    }

    initAccountSelect2('.js-product-select');

    // Re-initialize on focus if needed
    $(document).on('focus', '.js-product-select', function () {
        if (!$(this).hasClass("select2-hidden-accessible")) {
            initAccountSelect2(this);
        }
    });

    // On click, open dropdown and focus on search field
    $(document).on('click', '.js-product-select', function () {
        $(this).select2('open');
    });

    // Optional: Auto focus on search input when dropdown opens
    $(document).on('select2:open', function () {
        setTimeout(function () {
            document.querySelector('.select2-container--open .select2-search__field')?.focus();
        }, 0);
    });
});
</script>
{{-- Product Search --}}

                                    <script>
                                        // Bind once on DOM ready
                                        document.addEventListener('DOMContentLoaded', function () {
                                            // Focus qty on Enter in tax
                                            document.querySelectorAll('input[name="cost[]"]').forEach(function (el) {
                                                el.addEventListener('keydown', function (e) {
                                                    if (e.key === 'Enter') {
                                                        e.preventDefault();
                                                        this.closest('tr').querySelector('input[name="qty[]"]').focus();
                                                    }
                                                });
                                            });
                                    
                                            // Focus unitprice on Enter in qty
                                            document.querySelectorAll('input[name="qty[]"]').forEach(function (el) {
                                                el.addEventListener('keydown', function (e) {
                                                    if (e.key === 'Enter') {
                                                        e.preventDefault();
                                                        this.closest('tr').querySelector('input[name="unitprice[]"]').focus();
                                                    }
                                                });
                                            });
                                    
                                            // Call add_rows on Enter in unitprice
                                            document.querySelectorAll('input[name="unitprice[]"]').forEach(function (el) {
                                                el.addEventListener('keydown', function (e) {
                                                    if (e.key === 'Enter') {
                                                        e.preventDefault();
                                                        $('#btn_add_row').prop('disabled', true);
                                                        calc_change_new();
                                                        return add_rows();
                                                    }
                                                });
                                            });
                                        });                                
                                    </script>
                                    <script>
                                    function calc_change_new(id) {
                                        var net_vat = $('#net_vat').val();
                
                                        var qty = $('#qty').val();
                                        var unitprice = $('#unitprice').val();
                                        var value = $('#value').val();
                                        var discount = $('#discount').val();
                
                                        qty = (qty === '') ? '0' : qty;
                                        unitprice = (unitprice === '') ? '0' : unitprice;
                                        var fin_value = (unitprice * qty);
                                        $('#value').val(fin_value.toFixed(@json(session('logged_session_data.decimal_point'))));
                
                
                                        value = (value === '') ? '0' : value;
                                        discount = (discount === '') ? '0' : discount;
                                        var fin_taxableamount = ((unitprice * qty) - Number(discount));
                                        $('#taxableamount').val(fin_taxableamount.toFixed(@json(session('logged_session_data.decimal_point'))));
                
                                        var fin_vatamount = ((unitprice * qty) - Number(discount)) * ((Number(net_vat)) / 100);
                                        var vatamount = $('#vatamount').val(fin_vatamount.toFixed(@json(session('logged_session_data.decimal_point'))));
        
                                        $('#totalamount').val((Number(fin_taxableamount) + Number(fin_vatamount)).toFixed(@json(session('logged_session_data.decimal_point'))));
                
                                    }
                                    function add_rows() {
        
                                        if($("#part_number_new").val()=="none"){$("#part_number_new").focus(); return false;}
                                        if($("#qty").val()==""){$("#qty").focus(); return false;}
                                        if($("#unitprice").val()==""){$("#unitprice").focus(); return false;}
                                        if($("#taxableamount").val()==""){$("#taxableamount").focus(); return false;}
                                        if($("#vatamount").val()==""){$("#vatamount").focus(); return false;}
        
                                        $("#loading_bg").css("display", "block");
                                        var action = "{{ URL::to('add-sales-invoice-items-cart') }}";
                                        $.ajax({
                                            url: action,
                                            type: "POST",
                                            data: {
                                                _token: '{{ csrf_token() }}',
                                                part_number: $("#part_number_new").val(),
                                                description : $('#description_new').val(),
                                                sort_id : $('#sort_id').val(),
                                                cost: $("#cost").val(),
                                                tax: $("#net_vat").val(),
                                                qty: $("#qty").val(),
                                                unitprice: $("#unitprice").val(),
                                                value: $("#value").val(),
                                                discount: $("#discount").val(),
                                                fright: $("#fright").val(),
                                                customcharges: $("#customcharges").val(),
                                                taxableamount: $("#taxableamount").val(),
                                                vatamount: $("#vatamount").val(),
                                                serialno:$('#serialno').val(),
                                            },
                                            cache: false,
                                            success: function(dataResult) {
                                                var dataResult = JSON.parse(dataResult);
                                                var len = 0;
                                                var getSelectedRows="";
                                                    if(dataResult['data'] != null){
                                                        len = dataResult['data'].length;
                                                    }
                                                    if(len > 0){
                                                        for(var i=0; i<len; i++){
        
        
                                                            getSelectedRows +="<tr>\
                                                                <td>"+dataResult['data'][i].sort_id+"</td><td>"+dataResult['data'][i].partno+" <input type='hidden' name='sortid[]' id='sort_id_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].sort_id+"' /><input type='hidden' id='partno_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].partno+"' /><input type='hidden' id='pid_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].part_number+"' /></td>\
                                                                <td>"+dataResult['data'][i].description+"<input type='hidden' id='description_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].description+"' /></td>\
                                                                <td>"+dataResult['data'][i].cost+" <input type='hidden' id='cost_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].cost+"' /></td>\
                                                                <td>"+dataResult['data'][i].tax+" <input type='hidden' id='tax_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].tax+"' /></td>\
                                                                <td>"+dataResult['data'][i].qty+" <input type='hidden' id='qty_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].qty+"' /></td>\
                                                                <td class='text-right'>"+dataResult['data'][i].unitprice+" <input type='hidden' id='unitprice_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].unitprice+"' /></td>\
                                                                <td class='text-right'>"+dataResult['data'][i].value+" <input type='hidden' id='value_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].value+"' /></td>\
                                                                <td class='text-right'>"+dataResult['data'][i].discount+" <input type='hidden' id='discount_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].discount+"' /></td>\
                                                                <td class='text-right'>"+dataResult['data'][i].taxableamount+" <input type='hidden' id='taxableamount_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].taxableamount+"' /></td>\
                                                                <td class='text-right'>"+dataResult['data'][i].vatamount+" <input type='hidden' id='vatamount_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].vatamount+"' /></td>\
                                                                <td class='text-right'>"+(Number(dataResult['data'][i].taxableamount)+Number(dataResult['data'][i].vatamount))+" <input type='hidden' name='total_amount[]' id='totalamount_"+dataResult['data'][i].id+"' value='"+(Number(dataResult['data'][i].taxableamount)+Number(dataResult['data'][i].vatamount))+"' /></td>\
                                                                <td class='text-right'>"+dataResult['data'][i].serialno+" <input type='hidden' id='serialno_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].serialno+"' /></td>\
                                                                <td>\
                                                                    <input type='hidden' id='cart_item_id_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].id+"' />\
                                                                    <input type='hidden' id='deal_ref_id_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].refid+"' />\
                                                                    <a onclick='row_edit("+dataResult['data'][i].id+")' class='btn-sm btn-primary'><i class='fa fa-edit' aria-hidden='true'></i></a>\
                                                                    <a onclick='row_delete("+dataResult['data'][i].id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a>\
                                                                </td>\
                                                                </tr>";
                                                                
                                                        }
        
                                                        $("#part_number_new").val("none");
                                                        $("#description_new").val("");
                                                        $("#cost").val("0");
                                                        $("#qty").val("");
                                                        $("#unitprice").val("");
                                                        $("#value").val("");
                                                        $("#discount").val("0");
                                                        $("#fright").val("0");
                                                        $("#customcharges").val("0");
                                                        $("#taxableamount").val("");
                                                        $("#vatamount").val("");
                                                        $("#serialno").val("");
        
                                                        $('#po-table tbody').empty();
                                                        $("#po-table tbody").append(getSelectedRows); 
                                                    }
                                                    else{
                                                        
                                                    }
                                            }
                                        });
                                        $("#loading_bg").css("display", "none");
                                    }
                                    function row_edit(id) {
                                        $('#btn_add_row').css("display",'none');
                                        $('#update_add_row').css("display",'block');
        
                                        var partno = $('#partno_'+id).val();
                                        var pid = $('#pid_'+id).val();
                                        //alert(partno);
                                        //alert(pid);
                                        
                                        const targetSelect1 = $('#part_number_new');
                                        const option = new Option(partno, pid, true, true);
                                        targetSelect1.append(option).trigger('change');
                                        
                                        //$('#part_number_new').addClass('js-example-basic-single');
                                        $('#description_new').val($('#description_'+id).val());
                                        $('#sort_id').val($('#sortid_'+id).val());
                                        $('#qty').val($('#qty_'+id).val());
                                        $('#cost').val($('#cost_'+id).val());
                                        $('#unitprice').val($('#unitprice_'+id).val());
                                        $('#value').val($('#value_'+id).val());
                                        $('#discount').val($('#discount_'+id).val());
                                        $('#taxableamount').val($('#taxableamount_'+id).val());
                                        $('#vatamount').val($('#vatamount_'+id).val());
                                        $('#taxableamount').val($('#taxableamount_'+id).val());
                                        $('#totalamount').val($('#totalamount_'+id).val());                                        
                                        $("#serialno").val($('#serialno_'+id).val());
        
                                        $('#cart_item_id').val($('#cart_item_id_'+id).val());
                                        $('#deal_ref_id').val($('#deal_ref_id_'+id).val());
                                    }
                                    
                                    function row_update() {
                                        $("#loading_bg").css("display", "block");
                                        var itm_id = $('#cart_item_id').val();
                                        if($('#deal_ref_id').val() != ""){
                                            var deal_ref_id = $('#deal_ref_id').val();
                                        } else { var deal_ref_id = 0; }
                                        var part_number = $('#part_number_new').val();
                                        var description = $('#description_new').val();
                                        var tax = $("#net_vat").val();
                                        var qty = $('#qty').val();
                                        var unitprice = $('#unitprice').val();
                                        var value = $('#value').val();
                                        var discount = $('#discount').val();
                                        var taxableamount = $('#taxableamount').val();
                                        var vatamount = $('#vatamount').val();
        
                                        var action = "{{ URL::to('update-sales-invoice-items-cart') }}";
                                        $.ajax({
                                            url: action,
                                            type: "POST",
                                            data: {
                                                _token: '{{ csrf_token() }}',
                                                itm_id: itm_id,
                                                deal_ref_id: deal_ref_id,
                                                part_number: part_number,
                                                description: description,
                                                sort_id : $('#sort_id').val(),
                                                cost: $("#cost").val(),
                                                tax: tax,
                                                qty: qty,
                                                unitprice: unitprice,
                                                value: value,
                                                discount: discount,
                                                taxableamount: taxableamount,
                                                vatamount: vatamount,
                                                serialno:$('#serialno').val(),
                                            },
                                            cache: false,
                                            success: function(dataResult) {
                                                var dataResult = JSON.parse(dataResult);
                                                var len = 0;
                                                var getSelectedRows="";
                                                    if(dataResult['data'] != null){
                                                        len = dataResult['data'].length;
                                                    }
                                                    if(len > 0){
                                                        for(var i=0; i<len; i++){
        
                                                            getSelectedRows +="<tr>\
                                                                <td>"+dataResult['data'][i].sort_id+"</td><td>"+dataResult['data'][i].partno+" <input type='hidden' name='sortid[]' id='sort_id_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].sort_id+"' /><input type='hidden' id='partno_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].partno+"' /><input type='hidden' id='pid_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].part_number+"' /></td>\
                                                                <td>"+dataResult['data'][i].description+"<input type='hidden' id='description_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].description+"' /></td>\
                                                                <td>"+dataResult['data'][i].cost+" <input type='hidden' id='cost_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].cost+"' /></td>\
                                                                <td>"+dataResult['data'][i].tax+" <input type='hidden' id='tax_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].tax+"' /></td>\
                                                                <td>"+dataResult['data'][i].qty+" <input type='hidden' id='qty_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].qty+"' /></td>\
                                                                <td class='text-right'>"+dataResult['data'][i].unitprice+" <input type='hidden' id='unitprice_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].unitprice+"' /></td>\
                                                                <td class='text-right'>"+dataResult['data'][i].value+" <input type='hidden' id='value_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].value+"' /></td>\
                                                                <td class='text-right'>"+dataResult['data'][i].discount+" <input type='hidden' id='discount_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].discount+"' /></td>\
                                                                <td class='text-right'>"+dataResult['data'][i].taxableamount+" <input type='hidden' id='taxableamount_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].taxableamount+"' /></td>\
                                                                <td class='text-right'>"+dataResult['data'][i].vatamount+" <input type='hidden' id='vatamount_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].vatamount+"' /></td>\
                                                                <td class='text-right'>"+(Number(dataResult['data'][i].taxableamount)+Number(dataResult['data'][i].vatamount))+" <input type='hidden' name='total_amount[]' id='totalamount_"+dataResult['data'][i].id+"' value='"+(Number(dataResult['data'][i].taxableamount)+Number(dataResult['data'][i].vatamount))+"' /></td>\
                                                                <td class='text-right'>"+dataResult['data'][i].serialno+" <input type='hidden' id='serialno_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].serialno+"' /></td>\
                                                                <td>\
                                                                    <input type='hidden' id='cart_item_id_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].id+"' />\
                                                                    <input type='hidden' id='deal_ref_id_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].refid+"' />\
                                                                    <a onclick='row_edit("+dataResult['data'][i].id+")' class='btn-sm btn-primary'><i class='fa fa-edit' aria-hidden='true'></i></a>\
                                                                    <a onclick='row_delete("+dataResult['data'][i].id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a>\
                                                                </td>\
                                                                </tr>";
                                                        }
        
                                                        $("#part_number_new").val("none");
                                                        $("#description_new").val("");
                                                        $("#cost").val("0");
                                                        $("#qty").val("");
                                                        $("#unitprice").val("");
                                                        $("#value").val("");
                                                        $("#discount").val("0");
                                                        $("#taxableamount").val("");
                                                        $("#vatamount").val("");
                                                        $("#totalamount").val("");
                                                        $("#serialno").val("");
                                                        $("#select2-part_number_new-container").html('');                                               
        
                                                        $('#po-table tbody').empty();
                                                        $("#po-table tbody").append(getSelectedRows); 
                                                        
                                                        $('#btn_add_row').css("display",'block');
                                                        $('#update_add_row').css("display",'none');
        
                                                    }
                                                    else{
                                                        $('#po-table tbody').empty();
                                                    }
                                            }
                                        });
                                        $("#loading_bg").css("display", "none");
                                        $("#edit_cart_close").click();
                                    }
        
                                    function row_delete(id) {
                                        if (confirm("Are you sure you want to delete this item?") == false) {
                                            return false;
                                        }
                                        $("#loading_bg").css("display", "block");
                                        var action = "{{ URL::to('delete-sales-invoice-items-cart') }}";
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
                                                var getSelectedRows="";
                                                    if(dataResult['data'] != null){
                                                        len = dataResult['data'].length;
                                                    }
                                                    if(len > 0){
                                                        for(var i=0; i<len; i++){
        
        
                                                            getSelectedRows +="<tr>\
                                                                <td>"+dataResult['data'][i].sort_id+"</td><td>"+dataResult['data'][i].partno+" <input type='hidden' name='sortid[]' id='sort_id_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].sort_id+"' /><input type='hidden' id='partno_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].partno+"' /><input type='hidden' id='pid_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].part_number+"' /></td>\
                                                                <td>"+dataResult['data'][i].description+"<input type='hidden' id='description_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].description+"' /></td>\
                                                                <td>"+dataResult['data'][i].cost+" <input type='hidden' id='cost_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].cost+"' /></td>\
                                                                <td>"+dataResult['data'][i].tax+" <input type='hidden' id='tax_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].tax+"' /></td>\
                                                                <td>"+dataResult['data'][i].qty+" <input type='hidden' id='qty_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].qty+"' /></td>\
                                                                <td class='text-right'>"+dataResult['data'][i].unitprice+" <input type='hidden' id='unitprice_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].unitprice+"' /></td>\
                                                                <td class='text-right'>"+dataResult['data'][i].value+" <input type='hidden' id='value_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].value+"' /></td>\
                                                                <td class='text-right'>"+dataResult['data'][i].discount+" <input type='hidden' id='discount_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].discount+"' /></td>\
                                                                <td class='text-right'>"+dataResult['data'][i].taxableamount+" <input type='hidden' id='taxableamount_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].taxableamount+"' /></td>\
                                                                <td class='text-right'>"+dataResult['data'][i].vatamount+" <input type='hidden' id='vatamount_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].vatamount+"' /></td>\
                                                                <td class='text-right'>"+(Number(dataResult['data'][i].taxableamount)+Number(dataResult['data'][i].vatamount))+" <input type='hidden' name='total_amount[]' id='totalamount_"+dataResult['data'][i].id+"' value='"+(Number(dataResult['data'][i].taxableamount)+Number(dataResult['data'][i].vatamount))+"' /></td>\
                                                                <td class='text-right'>"+dataResult['data'][i].serialno+" <input type='hidden' id='serialno_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].serialno+"' /></td>\
                                                                <td>\
                                                                    <input type='hidden' id='cart_item_id_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].id+"' />\
                                                                    <input type='hidden' id='deal_ref_id_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].refid+"' />\
                                                                    <a onclick='row_edit("+dataResult['data'][i].id+")' class='btn-sm btn-primary'><i class='fa fa-edit' aria-hidden='true'></i></a>\
                                                                    <a onclick='row_delete("+dataResult['data'][i].id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a>\
                                                                </td>\
                                                                </tr>";
                                                        }
        
                                                        $("#part_number_new").val("none");
                                                        $("#description_new").val("");
                                                        $("#cost").val("0");
                                                        $("#qty").val("");
                                                        $("#unitprice").val("");
                                                        $("#value").val("");
                                                        $("#discount").val("0");
                                                        $("#taxableamount").val("");
                                                        $("#vatamount").val("");
                                                        $("#serialno").val("");
        
                                                        $('#po-table tbody').empty();
                                                        $("#po-table tbody").append(getSelectedRows); 
                                                    }
                                                    else{
                                                        $('#po-table tbody').empty();
                                                    }
                                            }
                                        });
                                        $("#loading_bg").css("display", "none");
                                    }
                                </script>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>

                            <table class="table table-bordered table-striped" id="po-table" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th style="width:50px;">@lang('SL')</th>
                                        <th style="width:100px;">@lang('Part No')</th>
                                        <th style="width:350px;">@lang('Description')</th>
                                        <th style="width:70px;">@lang('Cost')</th>
                                        <th style="width:70px;">@lang('Tax')</th>
                                        <th style="width:70px;">@lang('Qty')</th>
                                        <th class="text-right"style="width:80px;">@lang('Unit Price')</th>
                                        <th class="text-right"style="width:70px;">@lang('Value')</th>
                                        <th class="text-right"style="width:70px;">
                                            <a style="cursor: pointer;" class="text-danger float-right" data-toggle="modal" data-target="#modalDiscount">Discount</a>
                                        </th>
                                        <th class="text-right"style="width:120px;">@lang('Taxable Amount')</th>
                                        <th class="text-right"style="width:100px;">@lang('VAT Amount')</th>
                                        <th class="text-right"style="width:100px;">@lang('Total Amount')</th>
                                        <th style="width:100px;">@lang('Serial No')</th>
                                        <th class="text-right"style="width:65px;">
                                            <a class="btn-sm btn-danger float-right pt-0 pb-0" data-toggle="modal" data-target="#ModalExcelQuote" data-backdrop="static" data-keyboard="false">Import</a></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (count($cart)>0)
                                    @foreach ($cart as $dt)
                                    <tr>
                                        <td>{{ $dt->sort_id }} <input type="hidden" name="sortid[]" id="sortid_{{ $dt->id }}" value="{{ $dt->sort_id }}" /></td>
                                        <td>{{ $dt->partno }} <input type="hidden" id="partno_{{ $dt->id }}" value="{{ $dt->partno }}" />
                                            <input type="hidden" id="pid_{{ $dt->id }}" value="{{ $dt->part_number }}" /></td>
                                        <td>{{ $dt->description }} <input type="hidden" id="description_{{ $dt->id }}" value="{{ $dt->description }}" /></td>
                                        <td>{{ @App\SysHelper::com_curr_format($dt->cost,2,'.',',') }} <input type="hidden" id="cost_{{ $dt->id }}" value="{{ intval($dt->cost) }}" /></td>
                                        <td>{{ @App\SysHelper::com_curr_format($dt->tax,2,'.',',') }} <input type="hidden" id="tax_{{ $dt->id }}" value="{{ intval($dt->tax) }}" /></td>
                                        <td>{{ $dt->qty }} <input type="hidden" id="qty_{{ $dt->id }}" value="{{ $dt->qty }}" /></td>
                                        <td align="right">{{ @App\SysHelper::com_curr_format($dt->unitprice,2,'.',',') }} <input type="hidden" id="unitprice_{{ $dt->id }}" value="{{ $dt->unitprice }}" /></td>
                                        <td align="right">{{ @App\SysHelper::com_curr_format($dt->value,2,'.',',') }} <input type="hidden" id="value_{{ $dt->id }}" value="{{ $dt->value }}" /></td>
                                        <td align="right">{{ @App\SysHelper::com_curr_format($dt->discount,2,'.',',') }} <input type="hidden" id="discount_{{ $dt->id }}" value="{{ $dt->discount }}" /></td>
                                        <td align="right">{{ @App\SysHelper::com_curr_format($dt->taxableamount,2,'.',',') }} <input type="hidden" id="taxableamount_{{ $dt->id }}" value="{{ $dt->taxableamount }}" /></td>
                                        <td align="right">{{ @App\SysHelper::com_curr_format($dt->vatamount,2,'.',',') }} <input type="hidden" id="vatamount_{{ $dt->id }}" value="{{ $dt->vatamount }}" /></td>
                                        <td align="right">{{ @App\SysHelper::com_curr_format($dt->taxableamount+$dt->vatamount,2,'.',',') }} <input type="hidden" name='total_amount[]' id="totalamount_{{ $dt->id }}" value="{{ $dt->taxableamount+$dt->vatamount }}" /></td>
                                        <td>{{ $dt->serialno }} <input type="hidden" id="serialno_{{ $dt->id }}" value="{{ $dt->serialno }}" /></td>
                                        <td>
                                            <input type="hidden" id="cart_item_id_{{ $dt->id }}" value="{{ $dt->id }}" />
                                            <input type="hidden" id="deal_ref_id_{{ $dt->id }}" value="{{ $dt->refid }}" />
                                            <a onclick="row_edit({{ $dt->id }})" class="btn-sm btn-primary"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                            <a onclick="row_delete({{ $dt->id }})" class="btn-sm btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                        </td>
                                        </tr>
                                    @endforeach                            
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light">
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="font-weight-bold"></td>
                                        <td class="font-weight-bold"><label id="qty_total">{{ $cart->sum('qty') }}</label></td>
                                        <td class="text-right font-weight-bold"><label id="unitprice_total"></label></td>
                                        <td class="text-right font-weight-bold"><label id="value_total">{{ @App\SysHelper::com_curr_format($cart->sum('value'),2,'.',',') }}</label></td>
                                        <td class="text-right font-weight-bold"><label id="discount_total">{{ @App\SysHelper::com_curr_format($cart->sum('discount'),2,'.',',') }}</label></td>
                                        <td class="text-right font-weight-bold"><label id="taxableamount_total">{{ @App\SysHelper::com_curr_format($cart->sum('taxableamount'),2,'.',',') }}</label></td>
                                        <td class="text-right font-weight-bold"><label id="vatamount_total">{{ @App\SysHelper::com_curr_format($cart->sum('vatamount'),2,'.',',') }}</label></td>
                                        <td class="text-right font-weight-bold"><label id="amount_total">{{ @App\SysHelper::com_curr_format($cart->sum('taxableamount') + $cart->sum('vatamount'),2,'.',',') }}</label></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>

<script>
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
                                    </script>
                                </thead>
                                <tbody>
                                    <tr id="fright_row_1">
                                        <td>
                                            <select class="form-control js-example-basic-single" name="cfc_name[]" id="cfc_name_1">
                                                <option value=""></option>
                                                @foreach ($customs_freight_account as $key => $value)
                                                    <option value="{{ @$value->id }}">{{ @$value->account_code }} - {{ @$value->account_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control js-example-basic-single" name="cfc_credit_account[]" id="cfc_credit_account_1"
                                                readonly="true">
                                                <option value="none"></option>
                                                @foreach ($supplier as $key => $value)
                                                    <option value="{{ @$value->id }}">{{ @$value->account_code }} - {{ @$value->account_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" id="cfc_amount_1" name="cfc_amount[]"
                                                autocomplete="off" min="0" step="any" onchange="cfc_amount_change(1)">
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" id="cfc_remarks_1" name="cfc_remarks[]" onchange="updateNarration()"
                                                autocomplete="off">
                                        </td>
                                    </tr>
                                    <tr style="display: none;" id="fright_row_2">
                                        <td>
                                            <select class="form-control js-example-basic-single" name="cfc_name[]" id="cfc_name_2">
                                                <option value=""></option>
                                                @foreach ($customs_freight_account as $key => $value)
                                                    <option value="{{ @$value->id }}">{{ @$value->account_code }} - {{ @$value->account_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control js-example-basic-single" name="cfc_credit_account[]" id="cfc_credit_account_2"
                                                readonly="true">
                                                <option value="none"></option>
                                                @foreach ($supplier as $key => $value)
                                                    <option value="{{ @$value->id }}">{{ @$value->account_code }} - {{ @$value->account_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" id="cfc_amount_2" name="cfc_amount[]"
                                                autocomplete="off" min="0" step="any" onchange="cfc_amount_change(2)">
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" id="cfc_remarks_2" name="cfc_remarks[]" onchange="updateNarration()"
                                                autocomplete="off">
                                        </td>
                                    </tr>
                                    <tr style="display: none;" id="fright_row_3">
                                        <td>
                                            <select class="form-control js-example-basic-single" name="cfc_name[]" id="cfc_name_3">
                                                <option value=""></option>
                                                @foreach ($customs_freight_account as $key => $value)
                                                    <option value="{{ @$value->id }}">{{ @$value->account_code }} - {{ @$value->account_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control js-example-basic-single" name="cfc_credit_account[]" id="cfc_credit_account_3"
                                                readonly="true">
                                                <option value="none"></option>
                                                @foreach ($supplier as $key => $value)
                                                    <option value="{{ @$value->id }}">{{ @$value->account_code }} - {{ @$value->account_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" id="cfc_amount_3" name="cfc_amount[]"
                                                autocomplete="off" min="0" step="any" onchange="cfc_amount_change(3)">
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" id="cfc_remarks_3" name="cfc_remarks[]" onchange="updateNarration()"
                                                autocomplete="off">
                                        </td>
                                    </tr>
                                    <tr style="display: none;" id="fright_row_4">
                                        <td>
                                            <select class="form-control js-example-basic-single" name="cfc_name[]" id="cfc_name_4">
                                                <option value=""></option>
                                                @foreach ($customs_freight_account as $key => $value)
                                                    <option value="{{ @$value->id }}">{{ @$value->account_code }} - {{ @$value->account_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control js-example-basic-single" name="cfc_credit_account[]" id="cfc_credit_account_4"
                                                readonly="true">
                                                <option value="none"></option>
                                                @foreach ($supplier as $key => $value)
                                                    <option value="{{ @$value->id }}">{{ @$value->account_code }} - {{ @$value->account_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" id="cfc_amount_4" name="cfc_amount[]"
                                                autocomplete="off" min="0" step="any" onchange="cfc_amount_change(4)">
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" id="cfc_remarks_4" name="cfc_remarks[]" onchange="updateNarration()"
                                                autocomplete="off">
                                        </td>
                                    </tr>
                                    <tr style="display: none;" id="fright_row_5">
                                        <td>
                                            <select class="form-control js-example-basic-single" name="cfc_name[]" id="cfc_name_5">
                                                <option value=""></option>
                                                @foreach ($customs_freight_account as $key => $value)
                                                    <option value="{{ @$value->id }}">{{ @$value->account_code }} - {{ @$value->account_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control js-example-basic-single" name="cfc_credit_account[]" id="cfc_credit_account_5"
                                                readonly="true">
                                                <option value="none"></option>
                                                @foreach ($supplier as $key => $value)
                                                    <option value="{{ @$value->id }}">{{ @$value->account_code }} - {{ @$value->account_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input class="form-control" type="number" id="cfc_amount_5" name="cfc_amount[]"
                                                autocomplete="off" min="0" step="any" onchange="cfc_amount_change(5)">
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" id="cfc_remarks_5" name="cfc_remarks[]" onchange="updateNarration()"
                                                autocomplete="off">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>



                        <div class="row mt-40">
                            <div class="col-lg-12 text-right">
                                
                                <a class="btn btn-danger" onclick="get_adjustments()">@lang('Adjustment')</a>

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

    <script>
        function updateNarration() {
            const billNumber = document.getElementById('reference_no').value.trim();
            const remarks = document.querySelectorAll('input[name="cfc_remarks[]"]');
            let remarkValues = [];

            remarks.forEach(input => {
                if (input.value.trim() !== '') {
                    remarkValues.push(input.value.trim());
                }
            });

            const narrationValue = billNumber + ' - ' + remarkValues.join(', ');
            document.getElementById('narration').value = narrationValue;
        }

        // Trigger when reference_no or any cfc_remarks[] is changed
        document.getElementById('reference_no').addEventListener('input', updateNarration);
        document.querySelectorAll('input[name="cfc_remarks[]"]').forEach(input => {
            input.addEventListener('input', updateNarration);
        });
    </script>


    <div class="modal fade admin-query" id="dn_srlno_popup_win" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
            <div class="modal-content">
                <div class="modal-header m-0 p-3">
                    <h4 class="modal-title"><div id="div_serialno_title"></div></h4>
                    <button class="close" data-dismiss="modal" type="button">
                        ×
                    </button>
                </div>
                <div class="modal-body m-0 p-3">
                    <input type="hidden" id="hd_pending_dn_id"/>
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12 mb-20">
                                <div class="input-effect">
                                    <label class="dynamicslbl">  @lang('Serial No') <span>*</span> </label>
                                    <textarea class="dynamicstxt primary-input form-control" id="srlno_textarea" name="srlno_textarea"></textarea>
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_2 red_alert"></span>
                                </div>
                            </div>
                        </div>

                        <br />

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="col-lg-12 text-right">
                                        <button class="btn btn-warning" data-dismiss="modal" type="button" id="add_srl_cls">
                                            @lang('Close')
                                        </button>
                                        <input type="hidden" id="srl_id" />
                                        <button class="btn btn-success" type="button" onclick="srlno_add_item()">
                                            Add Selected
                                        </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<a data-modal-size="modal-md" data-target="#dn_srlno_popup_win" id="add_srlno_popup" data-toggle="modal"></a>
<script>
function srlno_add(){
    var hdtxt = $("#description_new").val();
    var srl = $("#serialno").val();
    $("#srlno_textarea").val(srl);
    $("#div_serialno_title").html(hdtxt);
    document.getElementById('add_srlno_popup').click();
    $("#srlno_textarea").focus();
}
function srlno_add_item(){
    var srltxt = $("#srlno_textarea").val();
    $("#serialno").val(srltxt);
    document.getElementById('add_srl_cls').click();
}
</script>
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
        
    <div class="modal fade admin-query" id="attachment_popup_win" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header m-0 p-3">
                    <h4 class="modal-title">Attachments - <label id="att_cust_name"></label></h4>
                    <button class="close" data-dismiss="modal" type="button">
                        ×
                    </button>
                </div>
                <div class="modal-body m-0 p-3">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-4 mb-20">
                                <div class="input-effect">
                                    <label class="dynamicslbl">  @lang('Attach File') <span>*</span> </label>
                                    <input class="form-control" type="file" id="att_file" name="att_file" onchange="updateDocName()"/>
                                </div>
                            </div>
                            <div class="col-lg-4 mb-20">
                                <div class="input-effect">
                                    <label class="dynamicslbl">  @lang('Date') <span>*</span> </label>
                                    <input class="form-control" type="date" id="att_date" name="att_date" value="{{ date('Y-m-d') }}"/>
                                </div>
                            </div>
                            <div class="col-lg-4 mb-20">
                                <div class="input-effect">
                                    <label class="dynamicslbl">  @lang('File Name') <span>*</span> </label>
                                    <input class="form-control" type="text" id="doc_name" name="doc_name" value=""/>
                                </div>
                            </div>
                            <script>
                                function updateDocName() {
                                    var fileInput = document.getElementById('att_file');
                                    var fileName = fileInput.files[0] ? fileInput.files[0].name : '';
                                    var fileNameWithoutExtension = fileName.substring(0, fileName.lastIndexOf('.')) || fileName;
                                    document.getElementById('doc_name').value = fileNameWithoutExtension;
                                }
                            </script>
                        </div>
                        
                    <div class="row">
                        <div class="col-md-12 mt-2">
                            <table id="att-table" class="table table-bordered table-striped" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th style="width: 10%;">No</th>
                                        <th style="width: 30%;">Date</th>
                                        <th style="width: 50%;">Attachment</th>
                                        <th style="width: 10%;"></th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>

                        <br />

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="col-lg-12 text-right">
                                        <button class="btn btn-warning" data-dismiss="modal" type="button" id="add_srl_cls">
                                            @lang('Close')
                                        </button>
                                        <input type="hidden" id="srl_id" />
                                        <button class="btn btn-success" type="button" onclick="add_attachment()">
                                            Add Attachment
                                        </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    
    <!-- Modal Adjustment-->
    <script>
        function get_adjustments() {

                    $("#loading_bg").css("display", "block");

                    $('#adj_siv_amount_actual').val($("input[name='total_amount[]']").val());
                    $('#adj_cus_id').val($('#customer').val());

                    var action = "{{ URL::to('sales-invoice-get-adjustment') }}";
                    $.ajax({
                        url: action,
                        type: "POST",
                        data: {
                            _token: '{{ csrf_token() }}',
                            customer: $("#customer").val(),
                        },
                        cache: false,
                        success: function(dataResult) {
                            var data = JSON.parse(dataResult);
                            // Handle 'unadjusted'
                            if (data.unadjusted && data.unadjusted.length > 0) {
                            var getSelectedRows="";
                                for (var i = 0; i < data.unadjusted.length; i++) {
                                    var a= (data.unadjusted[i].amount-data.unadjusted[i].adj_amount).toFixed(@json(session('logged_session_data.decimal_point'))).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                                    getSelectedRows +="<tr>\
                                         <td class='border'>"+data.unadjusted[i].doc_date+"</td>\
                                         <td class='border'>"+data.unadjusted[i].doc_number+"</td>\
                                         <td class='border'>"+data.unadjusted[i].account_name+"</td>\
                                        <td class='border text-right'>"+a+"</td>\
                                        <td class='border text-right'><input type='text' name='set_amt[]' id='set_amt_"+data.unadjusted[i].doc_number+"' class='form-control text-right' value='' onclick=\"set_adjust('"+(data.unadjusted[i].amount-data.unadjusted[i].adj_amount)+"','"+data.unadjusted[i].doc_number+"')\" />\
                                            <input type='hidden' name='receiptno[]' value='"+data.unadjusted[i].doc_number+"'/>\
                                            <input type='hidden' name='set_amt_act[]' value='"+a+"'/>\
                                        </td>\
                                        </tr>";
                                }
        
                            }

                            // Handle 'unadjusted_pdc'
                            if (data.unadjusted_pdc && data.unadjusted_pdc.length > 0) {
                            var getSelectedRows2="";
                                for (var j = 0; j < data.unadjusted_pdc.length; j++) {
                                    getSelectedRows2 +="<tr>\
                                         <td class='border'>"+data.unadjusted_pdc[i].doc_date+"</td>\
                                         <td class='border'>"+data.unadjusted_pdc[i].doc_number+"</td>\
                                         <td class='border'>"+data.unadjusted_pdc[i].account_name+"</td>\
                                        <td class='border text-right'>"+(data.unadjusted_pdc[i].amount-data.unadjusted_pdc[i].adj_amount)+"</td>\
                                        <td class='border text-right'><input type='text' name='set_amt[]' id='set_amt_"+data.unadjusted_pdc[i].doc_number+"' class='form-control text-right' value='"+data.unadjusted_pdc[i].adj_amount+"' onclick=\"set_adjust('"+(data.unadjusted_pdc[i].amount-data.unadjusted_pdc[i].adj_amount)+"','"+data.unadjusted[i].doc_number+"')\" />\
                                            <input type='hidden' name='receiptno[]' value='"+data.unadjusted_pdc[i].doc_number+"'/>\
                                            <input type='hidden' name='set_amt_act[]' value='"+(data.unadjusted_pdc[i].amount-data.unadjusted_pdc[i].adj_amount)+"'/>\
                                        </td>\
                                        </tr>";
                                }
                            }
                            
                                $('#adjustment_table tbody').empty();
                                $("#adjustment_table tbody").append(getSelectedRows);
                                $("#adjustment_table tbody").append(getSelectedRows2);
                        }
                    });
                    $("#btnModalAdjustment").click();
                    $("#loading_bg").css("display", "none");
                }
    </script>
    <script>
$(document).ready(function() {
    $('#adjustmentForm').on('submit', function(e) {
        e.preventDefault();

        // Collect the form data
        let formData = $(this).serialize();

        // Optional: basic validation
        

        // AJAX submission
        $.ajax({
            url: "{{ url('sales-invoice-add-adjustment-cart') }}", // Replace with your actual route
            type: "POST",
            data: formData,
            success: function(response) {
                // Handle success response
                alert('Adjustment saved successfully.');
                $('#ModalAdjustment').modal('hide'); // Hide modal if using Bootstrap
            },
            error: function(xhr) {
                // Handle errors
                alert('Error occurred while saving. Check console.');
                console.log(xhr.responseText);
            }
        });
    });
});
</script>
    <a id="btnModalAdjustment" data-toggle="modal" data-target="#ModalAdjustment"></a>
    <div class="modal fade" id="ModalAdjustment" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Unadjusted List</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form id="adjustmentForm" method="POST">
                @csrf
                {{-- {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'sales-invoice-update-adjustment', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }} --}}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table" id="adjustment_table" style="border: solid 1px #e3e6f0; width:auto; width:100%;">
                                <thead>
                                    <tr>
                                        <th class="border">Doc Date</th>
                                        <th class="border">Receipt No</th>
                                        <th class="border">Account Name</th>
                                        <th class="border text-right">Amount</th>
                                        <th class="border text-right">Adjusement</th>
                                    </tr>
                                </thead>
                                <tbody>


                                    {{-- @if(count($list_of_unadjusted) > 0)
                                    @foreach ($list_of_unadjusted as $p)
                                    <tr>
                                         <td class="border">{{ date('d/m/Y', strtotime(@$p->doc_date)) }}</td>
                                        <td class="border"><a href="{{url('get-url-receipt/' . @$p->doc_number)}}" target="_blank">{{ @$p->doc_number }}</a></td>
                                        <td class="border">{{ @$p->account_name }}</td>
                                        <td class="border text-right">{{ @$p->amount-@$p->adj_amount }}</td>
                                        <td class="border text-right"><input type="text" name="set_amt[]" id="set_amt_{{ @$p->doc_number }}" class="form-control text-right" id="" name="" value="{{ @$p->adj_amount }}" onclick="set_adjust('{{ @$p->amount-@$p->adj_amount }}','{{ @$p->doc_number }}')" />
                                            <input type="hidden" name="receiptno[]" value="{{ @$p->doc_number }}"/>
                                            <input type="hidden" name="set_amt_act[]" value="{{ @$p->amount-@$p->adj_amount }}"/>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                    @if(count($list_of_unadjusted_pdc) > 0)
                                    @foreach ($list_of_unadjusted_pdc as $p)
                                    <tr>
                                         <td class="border">{{ date('d/m/Y', strtotime(@$p->doc_date)) }}</td>
                                        <td class="border"><a href="{{url('get-url-receipt/' . @$p->doc_number)}}" target="_blank">{{ @$p->doc_number }}</a></td>
                                        <td class="border">{{ @$p->account_name }}</td>
                                        <td class="border text-right">{{ @$p->amount-@$p->adj_amount }}</td>
                                        <td class="border text-right"><input type="text" name="set_amt[]" id="set_amt_{{ @$p->doc_number }}" class="form-control text-right" id="" name="" value="{{ @$p->adj_amount }}" onclick="set_adjust('{{ @$p->amount-@$p->adj_amount }}','{{ @$p->doc_number }}')" />
                                            <input type="hidden" name="receiptno[]" value="{{ @$p->doc_number }}"/>
                                            <input type="hidden" name="set_amt_act[]" value="{{ @$p->amount-@$p->adj_amount }}"/>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="adj_cus_id" name="adj_cus_id" value=""/>
                    <input type="hidden" id="adj_siv_id" name="adj_siv_id" value=""/>
                    <input type="hidden" id="adj_siv_no" name="adj_siv_no" value=""/>
                    <input type="hidden" id="adj_siv_date" name="adj_siv_date" value=""/>
                    <input type="hidden" id="adj_siv_amount" name="adj_siv_amount" value=""/>
                    <input type="hidden" id="adj_siv_amount_actual" name="adj_siv_amount_actual" value=""/>
                    <input type="hidden" id="adj_siv_amount_adjusted" name="adj_siv_amount_adjusted" value="0"/>
                    <button class="btn btn-success" type="submit" >Adjust</button>
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
                </div>
                {{-- {{ Form::close() }} --}}
                </form>
            </div>
        </div>
    </div>
    <script>
function set_adjust(amt,id) {
    let maxAdjustable = parseFloat($("input[name='adj_siv_amount']").val());
    let currentAdjusted = 0;

    // Sum up all currently adjusted values
    $("input[id^='set_amt_']").each(function () {
        let val = parseFloat($(this).val());
        if (!isNaN(val)) {
            currentAdjusted += val;
        }
    });

    let remaining = maxAdjustable - currentAdjusted;

    if (remaining <= 0) {
        alert("No more amount left to adjust.");
        return;
    }

    // Check how much is available for this line
    let adjustAmount = parseFloat(amt);
    if (adjustAmount > remaining) {
        adjustAmount = remaining;
    }

    $('#set_amt_' + id).val(adjustAmount);

    // Optional: update hidden adjusted total
    $("input[name='adj_siv_amount_adjusted']").val(currentAdjusted + adjustAmount);
}
</script>
    <!-- Modal Adjustment-->



    <script>

        function add_attachment(){
            $("#loading_bg").css("display", "block");

            if($('#att_file').val()==""){ $('#att_file').focus(); $("#loading_bg").css("display", "none"); return false; }

            var action = "{{ URL::to('add-sales-invoice-attachment') }}";
            
            var formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');  // Append CSRF token
            formData.append('siv_id', 0);
            formData.append('att_date', $('#att_date').val()); // Append other form data
            formData.append('att_file', $('#att_file')[0].files[0]);
            formData.append('doc_name', $('#doc_name').val());


            $.ajax({
                url: action,
                type: "POST",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var getSelectedRows="";
                        if(dataResult['data'] != null){
                            len = dataResult['data'].length;
                        }
                        if(len > 0){
                            for(var i=0; i<len; i++){
                                getSelectedRows +="<tr>\
                                    <td>"+ Number(i+1) +"</td>\
                                    <td>"+get_format_date(dataResult['data'][i].doc_date)+"</td>\
                                <td><a href='../../"+dataResult['data'][i].doc_file+"' target='_blank'>"+dataResult['data'][i].doc_name+"</a></td>\
                                    <td><a onclick='delete_attachment("+dataResult['data'][i].id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
                                    </tr>";
                            }
                            $('#att_file').val('');
                            $('#doc_name').val('');
                            $('#att-table tbody').empty();
                            $("#att-table tbody").append(getSelectedRows); 
                        }
                        else{
                            $('#att-table tbody').empty();
                        }
                }
            });
            $("#loading_bg").css("display", "none");
        }
        function view_attachment(){
            $("#loading_bg").css("display", "block");
            $('#att_cust_name').text($('#customer :selected').text() + " " + $('#doc_number').val());

            var action = "{{ URL::to('view-sales-invoice-attachment') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    siv_id : 0,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var getSelectedRows="";
                        if(dataResult['data'] != null){
                            len = dataResult['data'].length;
                        }
                        if(len > 0){
                            for(var i=0; i<len; i++){
                                getSelectedRows +="<tr>\
                                    <td>"+ Number(i+1) +"</td>\
                                    <td>"+get_format_date(dataResult['data'][i].doc_date)+"</td>\
                                <td><a href='../../"+dataResult['data'][i].doc_file+"' target='_blank'>"+dataResult['data'][i].doc_name+"</a></td>\
                                    <td><a onclick='delete_attachment("+dataResult['data'][i].id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
                                    </tr>";
                            }
                            $('#att_file').val('');
                            $('#doc_name').val('');
                            $('#att-table tbody').empty();
                            $("#att-table tbody").append(getSelectedRows); 
                        }
                        else{
                            $('#att-table tbody').empty();
                        }
                }
            });
            $("#loading_bg").css("display", "none");
        }
        function delete_attachment(id){
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('delete-sales-invoice-attachment') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id : id,
                    siv_id : 0,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var getSelectedRows="";
                        if(dataResult['data'] != null){
                            len = dataResult['data'].length;
                        }
                        if(len > 0){
                            for(var i=0; i<len; i++){
                                getSelectedRows +="<tr>\
                                    <td>"+ Number(i+1) +"</td>\
                                    <td>"+get_format_date(dataResult['data'][i].doc_date)+"</td>\
                                <td><a href='../../"+dataResult['data'][i].doc_file+"' target='_blank'>"+dataResult['data'][i].doc_name+"</a></td>\
                                    <td><a onclick='delete_attachment("+dataResult['data'][i].id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
                                    </tr>";
                            }
                            $('#att_file').val('');
                            $('#doc_name').val('');
                            $('#att-table tbody').empty();
                            $("#att-table tbody").append(getSelectedRows); 
                        }
                        else{
                            $('#att-table tbody').empty();
                        }
                }
            });
            $("#loading_bg").css("display", "none");
        }

    function popup_profo_pending(id) {
        $("#loading_bg").css("display", "block");
        $("#hd_pending_profo_id").val(id);
        $("#profo_id").val(id);
        document.getElementById('addProfoPending').click();
        $("#loading_bg").css("display", "none");
    }

    $(document).on("change", "#customer", function () {
        var id = $("#customer").val();
        get_cust_details(id);
        get_cust_details_arabic(id);
    });

    function get_cust_details(id) {
        $("#loading_bg").css("display", "block");
        var action = "{{ URL::to('get-customer-details') }}";
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
                            if(dataResult['data'][i].status==3){
                                alert("Customer Information is incompleated! Please Update Customer.");
                                $('#btnSubmit').css('display','none');
                            } else { $('#btnSubmit').css('display',''); }
                            $('#payment_terms').val(dataResult['data'][i].payment_terms);
                            $('#shipping_name').val(dataResult['data'][i].contcat_person);
                            $('#shipping_address').val(dataResult['data'][i].address);
                            $('#customer_type').val(dataResult['data'][i].customer_type);
                            $('#sale_type').val(dataResult['data'][i].sale_type);
                            $('#country').val(dataResult['data'][i].vat_country);
                            $('#state').val(dataResult['data'][i].vat_state);
                            $('#net_vat').val(dataResult['data'][i].vat_percentage);
                            $('.vat').val(dataResult['data'][i].vat_percentage);
                        }                        
                    }
                    else{
                        $('#payment_terms').val('');
                        $('#shipping_name').val('');
                        $('#shipping_address').val('');
                        $('#customer_type').val('');
                        $('#sale_type').val('');
                        $('#country').val('');
                        $('#state').val('');
                        $('#net_vat').val('');
                        $('.vat').val('');
                    }
                    $("#loading_bg").css("display", "none");
            }
        });
    }
    function get_cust_details_arabic(id) {
        $("#loading_bg").css("display", "block");
        var action = "{{ URL::to('get-customer-details-arabic') }}";
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
                            $('#company_name_ar').val(dataResult['data'][i].company_name_ar);
                            $('#contact_person_ar').val(dataResult['data'][i].contact_person_ar);
                            $('#address_ar').val(dataResult['data'][i].address_ar);
                        }
                    }
                    else{
                        $('#company_name_ar').val('');
                        $('#contact_person_ar').val('');
                        $('#address_ar').val('');
                    }
                    $("#loading_bg").css("display", "none");
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

    </script>

<script>
    let isSubmitting = false;

    document.getElementById('sales-invoice-create-form').addEventListener('submit', function(e) {
        if (isSubmitting) {
            e.preventDefault(); // Prevent the form from submitting
        } else {
            isSubmitting = true; // Mark as submitting
            document.getElementById('btnSubmit').disabled = true; // Optionally disable button
        }
    });
</script>


{{-- ModalDiscount --}}
<div class="modal fade" id="modalDiscount" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Discount</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'add-sales-invoice-items-cart-discount', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="" class="form-label">Discount Amount</label>
                            <input type="text" class="form-control" id="discount_amount" name="discount_amount" required />
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="discount_amount_si_id" value="{{ @$edit->id }}"/>                    
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Split Discount</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
{{-- ModalDiscount --}}

<!-- Modal Excel Quote-->
<div class="modal fade" id="ModalExcelQuote" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="max-width: 1200px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Sales Invoice Items Excel Import</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'add-sales-invoice-items-excel-cart', 'method' => 'POST', 'id' => 'add-sales-invoice-items-excel-cart']) }}
            <input type="hidden" id="excel_deal_id" name="excel_deal_id" value="{{ @$edit->id }}" />
            <input type="hidden" id="excel_cust_id" name="excel_cust_id" value="{{ @$edit->cust_id }}" />
            <input type="hidden" id="excel_vat" name="excel_vat" value="{{ @$edit->customername->vat_percentage ?? 0 }}" />
            <input type="hidden" id="excel_company_id" name="excel_company_id" value="" />
            <input type="hidden" id="excel_currency_id" name="excel_currency_id" value="" />
            <input type="hidden" id="excel_customer_type" name="excel_customer_type" value="" />
            <input type="hidden" id="excel_quote_validity" name="excel_quote_validity" value="" />
            <input type="hidden" id="excel_payment_terms" name="excel_payment_terms" value="" />
            <input type="hidden" id="excel_delivery_date" name="excel_delivery_date" value="" />
            <input type="hidden" id="excel_payment_terms_txt" name="excel_payment_terms_txt" value="" />
            <input type="hidden" id="excel_delivery_time" name="excel_delivery_time" value="" />

            <script>
                function add_excel_data()
                {
                    $('#excel_company_id').val($('#company_id').val());
                    $('#excel_currency_id').val($('#currency_id').val());
                    $('#excel_customer_type').val($('#customer_type').val());
                    $('#excel_quote_validity').val($('#quote_validity').val());
                    $('#excel_payment_terms').val($('#payment_terms').val());
                    $('#excel_delivery_date').val($('#delivery_date').val());
                    $('#excel_payment_terms_txt').val($('#payment_terms_txt').val());
                    $('#excel_delivery_time').val($('#delivery_time').val());
                }
            </script>


            <div class="modal-body">
                <div class="row">
                    <div class="col-md-2">
                            <label for="" class="form-label">Select File (.csv)</label>
                    </div>
                    <div class="col-md-4">
                            <input class="form-control" type="file" id="excel-file" accept=".xlsx, .xls, .csv" />
                    </div>
                    <div class="col-md-4">
                            <button type="button" onclick="readExcel()" class="btn btn-success">Preview</button>
                            {{-- <input type="file" name="import_file" class="btn-danger" required /> --}}
                            (<a href="{{ url('public/uploads/product_upload/si_items_sample_format.csv') }}" target="_blank">Sample File</a>)
                    </div>

                    <div class="col-md-12 mt-2">
                        <table id="excel-table" class="table table-bordered table-striped" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="width:220px;">Part No</th>
                                    <th>Description</th>
                                    <th style="width:70px;">Qty</th>
                                    <th style="width:100px;" class="text-right">Unit Price</th>
                                    <th style="width:100px;" class="text-right">Discount</th>
                                    <th style="width:100px;" class="text-right">VAT</th>
                                    <th style="width:100px;" class="text-right">Cost</th>
                                    <th style="width:50px;" class="text-right"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be inserted here -->
                            </tbody>
                        </table>

                        <?php
                        $part_number = $items->pluck('part_number');
                        ?>
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>
                        <script>
                            function readExcel() {
                                add_excel_data();
                                var file = document.getElementById('excel-file').files[0];
                                if (!file) {
                                    alert("Please select an Excel file.");
                                    return;
                                }
                        
                                var reader = new FileReader();
                                reader.onload = function(event) {
                                    var data = event.target.result;
                                    var workbook = XLSX.read(data, { type: 'binary' });
                        
                                    // Assuming the data is in the first sheet
                                    var sheet = workbook.Sheets[workbook.SheetNames[0]];
                                    var rows = XLSX.utils.sheet_to_json(sheet, { header: 1 });
                        
                                    var tableBody = document.getElementById('excel-table').getElementsByTagName('tbody')[0];
                                    tableBody.innerHTML = "";  // Clear any previous data
                        
                                    // Loop through each row and add data to the table
                                    for (var i = 1; i < rows.length; i++) {  // Skip header row
                                        var row = rows[i];
                                        if (row.length < 6) continue;  // Skip invalid rows


                                        
                                        var part_number = <?php echo json_encode($part_number); ?>; // Convert PHP array to JS array

                                        var lowercase_part_number = part_number.map(function(value) {
                                            return value.toLowerCase();
                                        });

                                        var json_output = JSON.stringify(lowercase_part_number);

                                        var newRow = tableBody.insertRow(tableBody.rows.length);
                                        
                                        var rowVal = String(row[0] ?? '');
                                        var trimmedValue = rowVal.trim();
                                        
                                        if (json_output.includes(trimmedValue.toLowerCase())) {  // Use .includes() for array checking

                                        } else {
                                            newRow.style.backgroundColor = "#ffbebe";
                                        }
                        
                                        // Part No
                                        var partNoCell = newRow.insertCell(0);
                                        var partNoInput = document.createElement('input');
                                        partNoInput.type = 'text';  // Change to text input
                                        partNoInput.name = 'excel_part_no[]';
                                        partNoInput.value = rowVal.trim();
                                        partNoInput.classList.add('form-control');
                                        partNoCell.appendChild(partNoInput);
                        
                                        // Description
                                        var descriptionCell = newRow.insertCell(1);
                                        var descriptionInput = document.createElement('input');
                                        descriptionInput.type = 'text';  // Change to text input
                                        descriptionInput.name = 'excel_description[]';
                                        descriptionInput.value = row[1].trim();
                                        descriptionInput.classList.add('form-control');
                                        descriptionCell.appendChild(descriptionInput);
                                                    
                                        // Qty
                                        var qtyCell = newRow.insertCell(2);
                                        var qtyInput = document.createElement('input');
                                        qtyInput.type = 'text';  // Change to text input
                                        qtyInput.name = 'excel_qty[]';
                                        qtyInput.value = row[2];
                                        qtyInput.classList.add('form-control');
                                        qtyCell.appendChild(qtyInput);
                        
                                        // Unit Price (Right-aligned)
                                        var unitPriceCell = newRow.insertCell(3);
                                        var unitPriceInput = document.createElement('input');
                                        unitPriceInput.type = 'text';  // Change to text input
                                        unitPriceInput.name = 'excel_unit_price[]';
                                        unitPriceInput.value = row[3];
                                        unitPriceInput.classList.add('text-right');
                                        unitPriceInput.classList.add('form-control');
                                        unitPriceCell.appendChild(unitPriceInput);
                        
                                        // Discount (Right-aligned)
                                        var discountCell = newRow.insertCell(4);
                                        var discountInput = document.createElement('input');
                                        discountInput.type = 'text';  // Change to text input
                                        discountInput.name = 'excel_discount[]';
                                        discountInput.value = row[4];
                                        discountInput.classList.add('text-right');
                                        discountInput.classList.add('form-control');
                                        discountCell.appendChild(discountInput);
                                        
                                        // VAT (Right-aligned)
                                        var vatCell = newRow.insertCell(5);
                                        var vatInput = document.createElement('input');
                                        vatInput.type = 'text';  // Change to text input
                                        vatInput.name = 'vat_excel[]';
                                        vatInput.value = row[5];
                                        vatInput.classList.add('text-right');
                                        vatInput.classList.add('form-control');
                                        vatCell.appendChild(vatInput);

                                        var costCell = newRow.insertCell(6);
                                        var costInput = document.createElement('input');
                                        costInput.type = 'text';  // Change to text input
                                        costInput.name = 'cost_excel[]';
                                        costInput.value = row[6];
                                        costInput.classList.add('text-right');
                                        costInput.classList.add('form-control');
                                        costCell.appendChild(costInput);
                                        
                                        var deleteCell = newRow.insertCell(7);  // Last cell for delete button
                                        var deleteButton = document.createElement('button');
                                        deleteButton.type = 'button';  // Make sure the button doesn't submit a form
                                        deleteButton.textContent = 'Delete';
                                        deleteButton.classList.add('btn-sm');
                                        deleteButton.classList.add('btn-danger');
                                        deleteButton.onclick = function() {
                                            // Delete the row when the button is clicked
                                            var rowToDelete = this.parentNode.parentNode;
                                            rowToDelete.remove();
                                        };
                                        deleteCell.appendChild(deleteButton);

                                    }
                                };
                                reader.readAsBinaryString(file);
                            }
                        </script>
                    </div>
                    
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary excel_model_close" type="button" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Upload</button>
                {{-- onclick="return add_excel_data()" --}}
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
<!-- Modal Excel Quote-->

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
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
                    <h2 class="page-heading m-0">Sales Return Edit</h2>
                    <span class="page-label">Home - Sales Return</span>
                </div>
                <div>
                    <a href="{{ url('sales-return-add') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> New</a>
                    <a href="{{ url('sales-return/'.$edit->id.'/view') }}" type="button" class="btn btn-warning"><i class="fa fa-edit"></i> Edit</a>
                    <!-- Input with Search -->
                    <div style="float: left; margin-right:5px; position: relative; width: 200px;">
                        <input type="text" id="quick_search_doc_number" placeholder="SR Number" class="form-control pr-4" /> 
                        <span style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); color: #aaa; pointer-events: none;">
                        <i class="fas fa-search"></i>
                        </span>
                    </div>
                    <script>
                        const baseUrl = "{{ url('get-edit-url-sales-return') }}";                
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
                    <a href="{{ url('sales-return') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> List</a>
                </div>
            </div>
            <div class="card p-4 mb-2">
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'sales-return-update/'.$edit->id, 'method' => 'PUT', 'enctype' => 'multipart/form-data', 'id' => 'sales-return-update']) }}
                <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                <input type="hidden" name="id" value="{{ isset($edit) ? $edit->id : '' }}">
                <input type="hidden" id="sr_id" value="{{ isset($edit) ? $edit->id : '' }}">                
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
                                                    @foreach ($customer as $value)
                                                        <option value="{{ @$value->id }}"
                                                            {{ isset($edit) ? (!empty($edit->customer) ? (@$edit->customer == @$value->id ? 'selected' : '') : '') : '' }}>{{ @$value->account_name }}
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
                                                    <input class="form-control" type="text" name="doc_number" autocomplete="off" id="doc_number" value="{{ isset($edit) ? (!empty(@$edit->doc_number) ? @$edit->doc_number : old('doc_number')) : '' }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">Invoice Date</label>
                                                    @php
                                                    $value = date('Y-m-d');
                                                    if(isset($edit) && !empty($edit->doc_date) ){ @$value = date('Y-m-d', strtotime(@$edit->doc_date)); }
                                                    @endphp
                                                    <input class="form-control" id="doc_date" type="date" autocomplete="off"
                                                        name="doc_date" value="{{ @$value }}" required>
                                                </div>
                                            </div>
        
                                        <div class="col-lg-4 mb-2">
                                            <div class="input-effect">
                                                <label class="dynamicslbl">Currency</label>
                                                <a class="text-danger float-right" data-toggle="modal" data-target="#ModalChangeCurrancy">Change Currency</a>
                                                <select class="form-control js-example-basic-single" name="currency" id="currency">
                                                    @foreach ($currency as $value)
                                                    @if($edit->currency == @$value->id)
                                                        <option value="{{ @$value->id }}">{{ @$value->code }}</option>
                                                    @endif
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
                                            <a data-modal-size="modal-md" data-target="#dn_pending_popup_win" id="addDnPending"
                                                data-toggle="modal"></a>
                                            <input type="hidden" id="dn_id" name="dn_id">
                                            <input type="hidden" id="vat_percentage" name="vat_percentage" value="5">
                                        </div>
                    
                                    </div>
                                    <div class="col-lg-8 mb-2">
                                        <div class="row">
                                            <div class="col-lg-4 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">@lang('DLN') @lang('Number')<span>*</span></label>
                                                    <input class="form-control" type="text" name="dn_doc_number" autocomplete="off" id="dn_doc_number" value="{{ isset($edit) ? (!empty(@$edit->dn_doc_number) ? @$edit->dn_doc_number : old('dn_doc_number')) : '' }}" >
                                                </div>
                                            </div>
                                            <div class="col-lg-4 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">DLN Date</label>
                                                    @php
                                                    $value = date('Y-m-d');
                                                    if(isset($edit) && !empty($edit->dn_doc_date) ){ @$value = date('Y-m-d', strtotime(@$edit->dn_doc_date)); }
                                                    @endphp
                                                    <input class="form-control" id="dn_doc_date" type="date" autocomplete="off" name="dn_doc_date" value="{{ @$value }}" required>
                                                </div>
                                            </div>
                                            
                                            <div class="col-lg-4 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">@lang('SIV') @lang('Number')<span>*</span></label>
                                                    <input class="form-control" type="text" name="si_doc_number" autocomplete="off" id="si_doc_number" value="{{ isset($edit) ? (!empty(@$edit->si_doc_number) ? @$edit->si_doc_number : old('si_doc_number')) : '' }}" >
                                                </div>
                                            </div>
                                            <div class="col-lg-4 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">SIV Date</label>
                                                    @php
                                                    $value = date('Y-m-d');
                                                    if(isset($edit) && !empty($edit->si_doc_date) ){ @$value =
                                                    date('Y-m-d', strtotime(@$edit->si_doc_date)); }
                                                    @endphp
                                                    <input class="form-control" id="si_doc_date" type="date" autocomplete="off" name="si_doc_date" value="{{ @$value }}" required>
                                                </div>
                                            </div>
                                            
                                            <div class="col-lg-4 mb-2" style="display: none;">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">@lang('Delivery Terms')<span>*</span></label>
                                                    <input class="form-control" type="text" name="delivery_terms" autocomplete="off" id="delivery_terms" value="{{ isset($edit) ? (!empty(@$edit->delivery_terms) ? @$edit->delivery_terms : old('delivery_terms')) : 'Ex-Dubai' }}">
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
                                            </div>
                                            <div class="col-lg-4 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">LPO/Reference No<span>*</span></label>
                                                    <input class="form-control" type="text" name="reference_no" autocomplete="off" id="reference_no" value="{{ @$edit->lpo_number }}" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">LPO/Reference Date<span>*</span></label>
                                                    <input class="form-control" type="date" name="reference_date" autocomplete="off" id="reference_date" value="{{ @$edit->lpo_date }}" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">Deal ID<span>*</span></label>
                                                    <input class="form-control" type="text" name="deal_id" autocomplete="off" id="deal_id" value="{{ @$edit->deal_id }}" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">Supplier Name<span>*</span></label>
                                                    <input class="form-control" type="text" name="supplier_name" autocomplete="off" id="supplier_name" value="{{ @$edit->supplier_name }}" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-8 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">Narration<span></span></label>
                                                    <input class="form-control" type="text" name="narration" autocomplete="off" id="narration" value="{{ @$edit->narration }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-4 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">@lang('Created') @lang('By')<span>*</span></label>
                                                    <input class="form-control" type="text" name="createdby" autocomplete="off" id="createdby" value="{{ isset($edit) ? (!empty(@$edit->created_by) ? @$edit->createdby->full_name : old('createdby')) : Auth::user()->full_name }}" readonly>
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
                                            value="{{ isset($edit) ? (!empty(@$edit->payment_terms2) ? @$edit->payment_terms2 : old('payment_terms2')) : '' }}">
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
                                        <input type="text" class="form-control" value="{{ $edit->shipping_name }}" id="shipping_name" name="shipping_name">
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="dynamicslbl">@lang('Address') <span></span></label>
                                        <input type="text" class="form-control" value="{{ $edit->shipping_address }}" id="shipping_address" name="shipping_address">
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
                                                        @if (isset($edit)) @if (@$edit->customer_country == $value->id) selected @endif @endif
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
                                                        @foreach ($states as $key => $value)
                                                            <option value="{{ $value->id }}"
                                                                @if (isset($edit)) @if (@$edit->customer_state == $value->id) selected @endif @endif
                                                                >{{ $value->name }}</option>
                                                        @endforeach
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
                    </div>
                </div>
            </div>




            <div class="equipment comon-status row d-block">
                <hr />
                <h6 class="primary-color">@lang('Item Details'):</h6>

                <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th style="width:150px;">@lang('Part No')</th>
                            <th style="width:150px;">@lang('Description')</th>
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
                            <td><input type="checkbox" checked hidden>
                                <select class="form-control js-product-select" id="part_number_new">
                                    <option value="none"></option>
                                    @foreach ($items as $key => $value)
                                        <option value="{{ @$value->id }}">{{ @$value->part_number }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input class="form-control" type="text" id="description_new" autocomplete="off" readonly="true">
                            </td>
                            <td>
                                <input class="form-control vat" type="number" id="tax" autocomplete="off" min="0" value="{{ $edit->net_vat }}" onchange="calc_change_new()">
                            </td>
                            <td>
                                <input class="form-control" type="number" id="qty" autocomplete="off" min="0" onchange="calc_change_new()">
                            </td>
                            <td>
                                <input class="form-control" type="number" id="unitprice" autocomplete="off" min="0" onchange="calc_change_new()">
                            </td>
                            <td>
                                <input class="form-control" type="number" id="value" autocomplete="off" min="0" readonly>
                            </td>
                            <td>
                                <input class="form-control" type="number" id="discount" step="any" autocomplete="off" min="0" value="0" onchange="calc_change_new()">
                            </td>
                            <td>
                                <input class="form-control" type="number" id="taxableamount" autocomplete="off" min="0" readonly>
                            </td>
                            <td>
                                <input class="form-control" type="number" id="vatamount" autocomplete="off" min="0" readonly>
                            </td>
                            <td>
                                <input class="form-control" type="number" id="totalamount" autocomplete="off" min="0" readonly>
                            </td>
                            <td>
                                <input class="form-control" type="text" id="serial_no" autocomplete="off">
                            </td>
                            <td>
                                <input type="hidden" id="cart_item_id" />
                                <input type="hidden" id="deal_ref_id" />
                                <a id="btn_add_row" onclick="return add_rows()" class="btn btn-primary"><i class="fa fa-plus" aria-hidden="true"></i></a>
                                <a id="update_add_row" style="display: none;" onclick="return row_update()" class="btn btn-warning">Update</a>
                            </td>
                        </tr>
                        <script>
                        function calc_change_new(id) {
                            var net_vat = $('#tax').val();
    
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
                            var action = "{{ URL::to('add-sales-return-items') }}";
                            $.ajax({
                                url: action,
                                type: "POST",
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    sr_id: $("#sr_id").val(),
                                    part_number: $("#part_number_new").val(),
                                    tax: $("#tax").val(),
                                    qty: $("#qty").val(),
                                    unitprice: $("#unitprice").val(),
                                    value: $("#value").val(),
                                    discount: $("#discount").val(),
                                    taxableamount: $("#taxableamount").val(),
                                    vatamount: $("#vatamount").val(),
                                    serial_no: $("#serial_no").val(),
                                },
                                cache: false,
                                success: function(dataResult) {
                                    var dataResult = JSON.parse(dataResult);
                                    var len = 0;
                                    var t_qty = 0; var t_value = 0; var t_discount = 0; var t_taxableamount = 0; var t_vatamount = 0;

                                    var getSelectedRows="";
                                        if(dataResult['data'] != null){
                                            len = dataResult['data'].length;
                                        }
                                        if(len > 0){
                                            for(var i=0; i<len; i++){

                                                t_qty += Number(dataResult['data'][i].qty);
                                                t_value += Number(dataResult['data'][i].value);
                                                t_discount += Number(dataResult['data'][i].discount);
                                                t_taxableamount += Number(dataResult['data'][i].taxableamount);
                                                t_vatamount += Number(dataResult['data'][i].vatamount);

                                                getSelectedRows +="<tr>";
                                                
                                                    getSelectedRows +="<input type=hidden id='part_id_"+ i +"' name='part_id[]'' value='"+dataResult['data'][i].part_number+"' />";
                                                    getSelectedRows +="<td><input class='form-control' type='text' id='part_number_"+ i +"' name='part_number[]' value='"+dataResult['data'][i].partno+"' readonly=''><input type='hidden' id='partno_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].partno+"' /><input type='hidden' id='pid_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].part_number+"' /></td>";
                                                    
                                                    getSelectedRows +="<td><input class='form-control' type='text' id='description_"+ i +"' name='description[]' value='"+dataResult['data'][i].description+"' readonly=''><input type='hidden' id='description_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].description+"' /></td>";
                                                    
                                                    getSelectedRows +="<td><input class='form-control' type='text' id='tax_"+ i +"' name='tax[]' value='"+dataResult['data'][i].tax+"' readonly=''> <input type='hidden' id='tax_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].tax+"' /></td>";
                                                    getSelectedRows +="<td><input class='form-control' type='text' id='qty_"+i+"' name='qty[]' value='"+dataResult['data'][i].qty+"' readonly=''><input type='hidden' id='qty_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].qty+"' /></td>";
                                                    
                                                    getSelectedRows +="<td class='text-right'><input class='form-control' type='text' id='unitprice_"+ i +"' name='unitprice[]' value='"+dataResult['data'][i].unitprice+"' readonly=''><input type='hidden' id='unitprice_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].unitprice+"' /></td>";
                                                    
                                                    getSelectedRows +="<td class='text-right'><input class='form-control' type='text' id='value_"+ i +"' name='value[]' value='"+dataResult['data'][i].value+"' readonly=''><input type='hidden' id='value_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].value+"' /></td>";
                                                    
                                                    getSelectedRows +="<td class='text-right'><input class='form-control' type='text' id='discount_"+ i +"' name='discount[]' value='"+dataResult['data'][i].discount+"' readonly=''><input type='hidden' id='discount_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].discount+"' /></td>";
                                                    
                                                    getSelectedRows +="<td class='text-right'><input class='form-control' type='text' id='taxableamount_"+ i +"' name='taxableamount[]' value='"+dataResult['data'][i].taxableamount+"' readonly=''><input type='hidden' id='taxableamount_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].taxableamount+"' /></td>";
                                                    
                                                    getSelectedRows +="<td class='text-right'><input class='form-control' type='text' id='vatamount_"+ i +"' name='vatamount[]' value='"+dataResult['data'][i].vatamount+"' readonly=''><input type='hidden' id='vatamount_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].vatamount+"' /></td>";
                                                    
                                                    getSelectedRows +="<td class='text-right'><input class='form-control' type='text' id='totalamount_"+ i +"' name='totalamount[]' value='"+(Number(dataResult['data'][i].taxableamount)+Number(dataResult['data'][i].vatamount))+"' readonly=''></td>";
                                                    
                                                    getSelectedRows +="<td class='text-right'><input class='form-control' type='text' id='srl_"+ i +"' name='srl[]' value='"+dataResult['data'][i].partno+"' readonly=''></td>";
                                                    
                                                    getSelectedRows +="<td><input type='hidden' id='cart_item_id_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].id+"' /><input type='hidden' id='deal_ref_id_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].refid+"' /><a onclick='row_edit("+dataResult['data'][i].id+")' class='btn-sm btn-primary'><i class='fa fa-edit' aria-hidden='true'></i></a><a onclick='row_delete("+dataResult['data'][i].id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>";
                                                    
                                                    getSelectedRows +="</tr>";
                                                    
                                            }

                                            $("#part_number_new").val("none");
                                            $("#description_new").val("");
                                            //$("#tax").val("");
                                            $("#qty").val("");
                                            $("#unitprice").val("");
                                            $("#value").val("");
                                            $("#discount").val("0");
                                            $("#fright").val("0");
                                            $("#customcharges").val("0");
                                            $("#taxableamount").val("");
                                            $("#vatamount").val("");

                                            $("#qty_total").text(t_qty);
                                            $("#value_total").text(t_value);
                                            $("#discount_total").text(t_discount);
                                            $("#taxableamount_total").text(t_taxableamount);
                                            $("#vatamount_total").text(t_vatamount);
                                            $("#amount_total").text(t_taxableamount + t_vatamount);

                                            $('#si-table tbody').empty();
                                            $("#si-table tbody").append(getSelectedRows); 
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
                            $('#tax').val($('#tax_'+id).val());
                            
                            $('#qty').val($('#qty_'+id).val());
                            $('#unitprice').val($('#unitprice_'+id).val());
                            $('#value').val($('#value_'+id).val());
                            $('#discount').val($('#discount_'+id).val());
                            $('#taxableamount').val($('#taxableamount_'+id).val());
                            $('#vatamount').val($('#vatamount_'+id).val());
                            $('#taxableamount').val($('#taxableamount_'+id).val());
                            $('#totalamount').val($('#totalamount_'+id).val());
                            $('#serial_no').val($('#srl_'+id).val());

                            $('#cart_item_id').val($('#cart_item_id_'+id).val());
                            $('#deal_ref_id').val($('#deal_ref_id_'+id).val());
                            calc_change_new();
                        }
                        
                        function row_update() {
                            $("#loading_bg").css("display", "block");
                            var itm_id = $('#cart_item_id').val();
                            if($('#deal_ref_id').val() != ""){
                                var deal_ref_id = $('#deal_ref_id').val();
                            } else { var deal_ref_id = 0; }
                            var part_number = $('#part_number_new').val();
                            //var description = $('#description_new').val();
                            var tax = $("#tax").val();
                            var qty = $('#qty').val();
                            var unitprice = $('#unitprice').val();
                            var value = $('#value').val();
                            var discount = $('#discount').val();
                            var taxableamount = $('#taxableamount').val();
                            var vatamount = $('#vatamount').val();
                            var serial_no = $('#serial_no').val();

                            var action = "{{ URL::to('update-sales-return-items') }}";
                            $.ajax({
                                url: action,
                                type: "POST",
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    sr_id: $("#sr_id").val(),
                                    itm_id: itm_id,
                                    part_number: part_number,
                                    tax: tax,
                                    qty: qty,
                                    unitprice: unitprice,
                                    value: value,
                                    discount: discount,
                                    taxableamount: taxableamount,
                                    vatamount: vatamount,
                                    serial_no: serial_no,
                                },
                                cache: false,
                                success: function(dataResult) {
                                    var dataResult = JSON.parse(dataResult);
                                    var len = 0;
                                    var t_qty = 0; var t_value = 0; var t_discount = 0; var t_taxableamount = 0; var t_vatamount = 0;
                                    var getSelectedRows="";
                                        if(dataResult['data'] != null){
                                            len = dataResult['data'].length;
                                        }
                                        if(len > 0){
                                            for(var i=0; i<len; i++){
                                                t_qty += Number(dataResult['data'][i].qty);
                                                t_value += Number(dataResult['data'][i].value);
                                                t_discount += Number(dataResult['data'][i].discount);
                                                t_taxableamount += Number(dataResult['data'][i].taxableamount);
                                                t_vatamount += Number(dataResult['data'][i].vatamount);

                                                getSelectedRows +="<tr>";
                                                
                                                    getSelectedRows +="<input type=hidden id='part_id_"+ i +"' name='part_id[]'' value='"+dataResult['data'][i].part_number+"' />";
                                                    getSelectedRows +="<td><input class='form-control' type='text' id='part_number_"+ i +"' name='part_number[]' value='"+dataResult['data'][i].partno+"' readonly=''><input type='hidden' id='partno_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].partno+"' /><input type='hidden' id='pid_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].part_number+"' /></td>";
                                                    
                                                    getSelectedRows +="<td><input class='form-control' type='text' id='description_"+ i +"' name='description[]' value='"+dataResult['data'][i].description+"' readonly=''><input type='hidden' id='description_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].description+"' /></td>";
                                                    
                                                    getSelectedRows +="<td><input class='form-control' type='text' id='tax_"+ i +"' name='tax[]' value='"+dataResult['data'][i].tax+"' readonly=''> <input type='hidden' id='tax_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].tax+"' /></td>";
                                                    getSelectedRows +="<td><input class='form-control' type='text' id='qty_"+i+"' name='qty[]' value='"+dataResult['data'][i].qty+"' readonly=''><input type='hidden' id='qty_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].qty+"' /></td>";
                                                    
                                                    getSelectedRows +="<td class='text-right'><input class='form-control' type='text' id='unitprice_"+ i +"' name='unitprice[]' value='"+dataResult['data'][i].unitprice+"' readonly=''><input type='hidden' id='unitprice_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].unitprice+"' /></td>";
                                                    
                                                    getSelectedRows +="<td class='text-right'><input class='form-control' type='text' id='value_"+ i +"' name='value[]' value='"+dataResult['data'][i].value+"' readonly=''><input type='hidden' id='value_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].value+"' /></td>";
                                                    
                                                    getSelectedRows +="<td class='text-right'><input class='form-control' type='text' id='discount_"+ i +"' name='discount[]' value='"+dataResult['data'][i].discount+"' readonly=''><input type='hidden' id='discount_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].discount+"' /></td>";
                                                    
                                                    getSelectedRows +="<td class='text-right'><input class='form-control' type='text' id='taxableamount_"+ i +"' name='taxableamount[]' value='"+dataResult['data'][i].taxableamount+"' readonly=''><input type='hidden' id='taxableamount_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].taxableamount+"' /></td>";
                                                    
                                                    getSelectedRows +="<td class='text-right'><input class='form-control' type='text' id='vatamount_"+ i +"' name='vatamount[]' value='"+dataResult['data'][i].vatamount+"' readonly=''><input type='hidden' id='vatamount_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].vatamount+"' /></td>";
                                                    
                                                    getSelectedRows +="<td class='text-right'><input class='form-control' type='text' id='totalamount_"+ i +"' name='totalamount[]' value='"+(Number(dataResult['data'][i].taxableamount)+Number(dataResult['data'][i].vatamount))+"' readonly=''></td>";
                                                    
                                                    getSelectedRows +="<td class='text-right'><input class='form-control' type='text' id='srl_"+ i +"' name='srl[]' value='"+dataResult['data'][i].partno+"' readonly=''></td>";
                                                    
                                                    getSelectedRows +="<td><input type='hidden' id='cart_item_id_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].id+"' /><input type='hidden' id='deal_ref_id_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].refid+"' /><a onclick='row_edit("+dataResult['data'][i].id+")' class='btn-sm btn-primary'><i class='fa fa-edit' aria-hidden='true'></i></a><a onclick='row_delete("+dataResult['data'][i].id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>";
                                                    
                                                    getSelectedRows +="</tr>";
                                            }

                                            $("#part_number_new").val("none");
                                            $("#description_new").val("");
                                            //$("#tax").val("");
                                            $("#qty").val("");
                                            $("#unitprice").val("");
                                            $("#value").val("");
                                            $("#discount").val("0");
                                            $("#taxableamount").val("");
                                            $("#vatamount").val("");
                                            $("#totalamount").val(""); 
                                            $("#select2-part_number_new-container").html('');  

                                            $("#qty_total").text(t_qty);
                                            $("#value_total").text(t_value);
                                            $("#discount_total").text(t_discount);
                                            $("#taxableamount_total").text(t_taxableamount);
                                            $("#vatamount_total").text(t_vatamount);
                                            $("#amount_total").text(t_taxableamount + t_vatamount);                                             

                                            $('#si-table tbody').empty();
                                            $("#si-table tbody").append(getSelectedRows); 
                                            
                                            $('#btn_add_row').css("display",'block');
                                            $('#update_add_row').css("display",'none');

                                        }
                                        else{
                                            $('#si-table tbody').empty();
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
                            var action = "{{ URL::to('delete-sales-return-items') }}";
                            $.ajax({
                                url: action,
                                type: "POST",
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    id: id,
                                    sr_id: $("#sr_id").val(),
                                },
                                cache: false,
                                success: function(dataResult) {
                                    var dataResult = JSON.parse(dataResult);
                                    var len = 0;
                                    var t_qty = 0; var t_value = 0; var t_discount = 0; var t_taxableamount = 0; var t_vatamount = 0;
                                    var getSelectedRows="";
                                        if(dataResult['data'] != null){
                                            len = dataResult['data'].length;
                                        }
                                        if(len > 0){
                                            for(var i=0; i<len; i++){
                                                t_qty += Number(dataResult['data'][i].qty);
                                                t_value += Number(dataResult['data'][i].value);
                                                t_discount += Number(dataResult['data'][i].discount);
                                                t_taxableamount += Number(dataResult['data'][i].taxableamount);
                                                t_vatamount += Number(dataResult['data'][i].vatamount);


                                                getSelectedRows +="<tr>";
                                                
                                                getSelectedRows +="<input type=hidden id='part_id_"+ i +"' name='part_id[]'' value='"+dataResult['data'][i].part_number+"' />";
                                                getSelectedRows +="<td><input class='form-control' type='text' id='part_number_"+ i +"' name='part_number[]' value='"+dataResult['data'][i].partno+"' readonly=''><input type='hidden' id='partno_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].partno+"' /><input type='hidden' id='pid_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].part_number+"' /></td>";
                                                
                                                getSelectedRows +="<td><input class='form-control' type='text' id='description_"+ i +"' name='description[]' value='"+dataResult['data'][i].description+"' readonly=''><input type='hidden' id='description_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].description+"' /></td>";
                                                
                                                getSelectedRows +="<td><input class='form-control' type='text' id='tax_"+ i +"' name='tax[]' value='"+dataResult['data'][i].tax+"' readonly=''> <input type='hidden' id='tax_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].tax+"' /></td>";
                                                getSelectedRows +="<td><input class='form-control' type='text' id='qty_"+i+"' name='qty[]' value='"+dataResult['data'][i].qty+"' readonly=''><input type='hidden' id='qty_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].qty+"' /></td>";
                                                
                                                getSelectedRows +="<td class='text-right'><input class='form-control' type='text' id='unitprice_"+ i +"' name='unitprice[]' value='"+dataResult['data'][i].unitprice+"' readonly=''><input type='hidden' id='unitprice_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].unitprice+"' /></td>";
                                                
                                                getSelectedRows +="<td class='text-right'><input class='form-control' type='text' id='value_"+ i +"' name='value[]' value='"+dataResult['data'][i].value+"' readonly=''><input type='hidden' id='value_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].value+"' /></td>";
                                                
                                                getSelectedRows +="<td class='text-right'><input class='form-control' type='text' id='discount_"+ i +"' name='discount[]' value='"+dataResult['data'][i].discount+"' readonly=''><input type='hidden' id='discount_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].discount+"' /></td>";
                                                
                                                getSelectedRows +="<td class='text-right'><input class='form-control' type='text' id='taxableamount_"+ i +"' name='taxableamount[]' value='"+dataResult['data'][i].taxableamount+"' readonly=''><input type='hidden' id='taxableamount_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].taxableamount+"' /></td>";
                                                
                                                getSelectedRows +="<td class='text-right'><input class='form-control' type='text' id='vatamount_"+ i +"' name='vatamount[]' value='"+dataResult['data'][i].vatamount+"' readonly=''><input type='hidden' id='vatamount_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].vatamount+"' /></td>";
                                                
                                                getSelectedRows +="<td class='text-right'><input class='form-control' type='text' id='totalamount_"+ i +"' name='totalamount[]' value='"+(Number(dataResult['data'][i].taxableamount)+Number(dataResult['data'][i].vatamount))+"' readonly=''></td>";
                                                
                                                getSelectedRows +="<td class='text-right'><input class='form-control' type='text' id='srl_"+ i +"' name='srl[]' value='"+dataResult['data'][i].partno+"' readonly=''></td>";
                                                
                                                getSelectedRows +="<td><input type='hidden' id='cart_item_id_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].id+"' /><input type='hidden' id='deal_ref_id_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].refid+"' /><a onclick='row_edit("+dataResult['data'][i].id+")' class='btn-sm btn-primary'><i class='fa fa-edit' aria-hidden='true'></i></a><a onclick='row_delete("+dataResult['data'][i].id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>";
                                                
                                                getSelectedRows +="</tr>";
                                            }

                                            $("#part_number_new").val("none");
                                            $("#description_new").val("");
                                            //$("#tax").val("");
                                            $("#qty").val("");
                                            $("#unitprice").val("");
                                            $("#value").val("");
                                            $("#discount").val("0");
                                            $("#taxableamount").val("");
                                            $("#vatamount").val("");

                                            $("#qty_total").text(t_qty);
                                            $("#value_total").text(t_value);
                                            $("#discount_total").text(t_discount);
                                            $("#taxableamount_total").text(t_taxableamount);
                                            $("#vatamount_total").text(t_vatamount);
                                            $("#amount_total").text(t_taxableamount + t_vatamount);

                                            $('#si-table tbody').empty();
                                            $("#si-table tbody").append(getSelectedRows); 
                                        }
                                        else{
                                            $('#si-table tbody').empty();
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
var discount = $('#discount_' + id + '').val();


qty = (qty === '') ? '0' : qty;
unitprice = (unitprice === '') ? '0' : unitprice;
discount = (discount === '') ? '0' : discount;

var fin_value = (unitprice * qty);
$('#value_' + id + '').val(fin_value.toFixed(@json(session('logged_session_data.decimal_point'))));

var fin_taxableamount = ((unitprice * qty) - Number(discount));
$('#taxableamount_' + id + '').val(fin_taxableamount.toFixed(@json(session('logged_session_data.decimal_point'))));

var fin_vatableamount = ((unitprice * qty) - Number(discount)) * (Number(net_vat) / 100);
$('#vatamount_' + id + '').val(fin_vatableamount.toFixed(@json(session('logged_session_data.decimal_point'))));

var fin_totalamount = (fin_taxableamount + fin_vatableamount);
$('#totalamount_' + id + '').val(fin_totalamount.toFixed(@json(session('logged_session_data.decimal_point'))));

calc_total();
}


function calc_total()
{
var countrow = $('#dn-row-count').val();
alert(countrow);

//var countrow = $('#si-table >tbody >tr').length;
var t1=0, t2=0, t3=0, t4=0, t5=0, t6=0;
for(var i=0; i < countrow; i++)
{
t1 += Number($('#qty_'+i).val());
t2 += Number($('#unitprice_'+i).val());
t3 += Number($('#value_'+i).val());
t4 += Number($('#discount_'+i).val());
t5 += Number($('#taxableamount_'+i).val());
t6 += Number($('#vatamount_'+i).val());
}
$('#qty_total').text(t1);
$('#unitprice_total').text(t2.toFixed(@json(session('logged_session_data.decimal_point'))));
$('#value_total').text(t3.toFixed(@json(session('logged_session_data.decimal_point'))));
$('#discount_total').text(t4.toFixed(@json(session('logged_session_data.decimal_point'))));
$('#taxableamount_total').text(t5.toFixed(@json(session('logged_session_data.decimal_point'))));
$('#vatamount_total').text(t6.toFixed(@json(session('logged_session_data.decimal_point'))));
$('#net_total').text((t5+t6).toFixed(@json(session('logged_session_data.decimal_point'))));
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








                        <div class="equipment comon-status row d-block">
                            <hr />
                            <h6 class="primary-color">@lang('Item Details'):</h6>
                            <input type="hidden" id="dn-row-count" value="{{ count($edit_list) }}" />
                            <table class="table table-bordered table-striped" id="si-table" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th style="width:150px;">@lang('Part No')</th>
                                        <th>@lang('Description')</th>
                                        <th style="width:70px;">@lang('Vat')</th>
                                        <th style="width:70px;">@lang('Qty')</th>
                                        <th class="text-right" style="width:150px;">@lang('Unit Price')</th>
                                        <th class="text-right" style="width:150px;">@lang('Value')</th>
                                        <th class="text-right" style="width:150px;">@lang('Discount')</th>
                                        <th class="text-right" style="width:150px;">@lang('Taxable Amount')</th>
                                        <th class="text-right" style="width:150px;">@lang('VAT Amount')</th>
                                        <th class="text-right" style="width:150px;">@lang('Total Amount')</th>
                                        <th style="width:150px;">@lang('Serial No')</th>
                                        <th>@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (count($edit_list)>0)
                                    @php $i=0; @endphp
                                        @foreach ($edit_list as $cart)
                                        @php                                        
                                        $value = @App\SysHelper::com_curr_format($cart->qty * $cart->unitprice, 2, '.', '');
                                        $taxamount=@App\SysHelper::com_curr_format($value - $cart->discount, 2, '.', '');
                                        $vatamount = @App\SysHelper::com_curr_format($cart->vatamount, 2, '.', '');
                                        $totalamount = @App\SysHelper::com_curr_format($vatamount + $taxamount, 2, '.', '');
                                        @endphp
                                        <tr>
                                            <td>
                                                
                                                <input type="hidden" id="partno_{{ $i }}" value="{{ $cart->product->part_number }}" />
                                                <input type="hidden" id="pid_{{ $i }}" value="{{ $cart->part_number }}" />
                                                
                                                <input class="form-control" type="text" id="part_number_{{ $i }}" name="part_number[]" value="{{ $cart->product->part_number }}" readonly>


                                                <input type="hidden" id="part_id_{{ $i }}" name="part_id[]" value="{{ $cart->part_number }}" /></td>
                                            <td class="jshide"><input class="form-control" type="text" id="description_{{ $i }}" name="description[]" autocomplete="off" min="0" value="{{ $cart->product->description }}" ></td>
                                            <td><input class="form-control" type="number" id="tax_{{ $i }}" name="tax[]" autocomplete="off" min="0" value="{{ $cart->tax }}" ></td>
                                            <td><input class="form-control qty" type="number" id="qty_{{ $i }}" name="qty[]" autocomplete="off" min="0" value="{{ $cart->qty }}" onchange="calc_change({{ $i }})" onkeypress="set_license_key_po({{ $i }},{{ $cart->product_type }})"></td>
                                            <td class="jshide"><input class="form-control text-right" type="text" id="unitprice_{{ $i }}" value="{{ @App\SysHelper::com_curr_format( $cart->unitprice, 2, '.', '')}}" name="unitprice[]" autocomplete="off" min="0" onchange="calc_change({{ $i }})"></td>
                                            <td class="jshide"><input class="form-control text-right" type="text" id="value_{{ $i }}" value="{{ $value }}" name="value[]" autocomplete="off" min="0" readonly></td>
                                            <td class="jshide"><input class="form-control text-right" type="text" id="discount_{{ $i }}" value=" {{ @App\SysHelper::com_curr_format( $cart->discount , 2, '.', '') }}" name="discount[]" autocomplete="off" min="0" onchange="calc_change({{ $i }})"></td>
                                            <td class="jshide"><input class="form-control text-right" type="text" id="taxableamount_{{ $i }}" value="{{ $taxamount }}" name="taxableamount[]" readonly></td>
                                            <td class="jshide"><input class="form-control text-right" type="text" id="vatamount_{{ $i }}" value="{{ $vatamount }}" name="vatamount[]" readonly></td>
                                            <td class="jshide"><input class="form-control text-right" type="text" id="totalamount_{{ $i }}" value="{{ @App\SysHelper::com_curr_format($totalamount , 2, '.', '') }}" name="totalamount[]" readonly></td>
                                            <?php
                                                $srno = $edit_list_srl->where('part_number',$cart->part_number)->pluck('srl_no');
                                                $array = explode(',', trim($srno, '[""]'));
                                                $string = implode(', ', $array);                                            
                                            ?>
                                            <td class="jshide"><input class="form-control srl" type="test" id="srl_{{ $i }}" name="srl[]" onclick="srlno_add({{ $i }})" value="{{ $string }}" ></td>
                                            <td>
                                                
                                            <input type="hidden" id="cart_item_id_{{ $i }}" value="{{ $cart->id }}" />
                                            <input type="hidden" id="deal_ref_id_{{ $i }}" value="{{ $cart->refid }}" />
                                            <a onclick="row_edit({{ $i }})" class="btn-sm btn-primary"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                            <a onclick="row_delete({{ $cart->id }})" class="btn-sm btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></a>

                                            </td>
                                            </tr>
                                        @php $i++; @endphp
                                        @endforeach                                        
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light">
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="font-weight-bold"><label id="qty_total">{{ $edit_list->sum('qty') }}</label></td>
                                        <td class="text-right font-weight-bold"></td>
                                        <td class="text-right font-weight-bold"><label id="value_total">{{ @App\SysHelper::com_curr_format($edit_list->sum('value'),2,'.',',') }}</label></td>
                                        <td class="text-right font-weight-bold"><label id="discount_total">{{ @App\SysHelper::com_curr_format($edit_list->sum('discount'),2,'.',',') }}</label></td>
                                        <td class="text-right font-weight-bold"><label id="taxableamount_total">{{ @App\SysHelper::com_curr_format($edit_list->sum('taxableamount'),2,'.',',') }}</label></td>
                                        <td class="text-right font-weight-bold"><label id="vatamount_total">{{ @App\SysHelper::com_curr_format($edit_list->sum('vatamount'),2,'.',',') }}</label></td>
                                        <td class="text-right font-weight-bold"><label id="amount_total">{{ @App\SysHelper::com_curr_format(($edit_list->sum('taxableamount')+$edit_list->sum('vatamount')),2,'.',',') }}</label></td>
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
    var discount = $('#discount_' + id + '').val();


    qty = (qty === '') ? '0' : qty;
    unitprice = (unitprice === '') ? '0' : unitprice;
    discount = (discount === '') ? '0' : discount;

    var fin_value = (unitprice * qty);
    $('#value_' + id + '').val(fin_value.toFixed(@json(session('logged_session_data.decimal_point'))));

    var fin_taxableamount = ((unitprice * qty) - Number(discount));
    $('#taxableamount_' + id + '').val(fin_taxableamount.toFixed(@json(session('logged_session_data.decimal_point'))));

    var fin_vatableamount = ((unitprice * qty) - Number(discount)) * (Number(net_vat) / 100);
    $('#vatamount_' + id + '').val(fin_vatableamount.toFixed(@json(session('logged_session_data.decimal_point'))));

    var fin_totalamount = (fin_taxableamount + fin_vatableamount);
    $('#totalamount_' + id + '').val(fin_totalamount.toFixed(@json(session('logged_session_data.decimal_point'))));

    calc_total();
}


function calc_total()
{
var countrow = $('#dn-row-count').val();

//var countrow = $('#si-table >tbody >tr').length;
var t1=0, t2=0, t3=0, t4=0, t5=0, t6=0;
for(var i=0; i < countrow; i++)
{
    t1 += Number($('#qty_'+i).val());
    t2 += Number($('#unitprice_'+i).val());
    t3 += Number($('#value_'+i).val());
    t4 += Number($('#discount_'+i).val());
    t5 += Number($('#taxableamount_'+i).val());
    t6 += Number($('#vatamount_'+i).val());
}
    $('#qty_total').text(t1);
    $('#unitprice_total').text(t2.toFixed(@json(session('logged_session_data.decimal_point'))));
    $('#value_total').text(t3.toFixed(@json(session('logged_session_data.decimal_point'))));
    $('#discount_total').text(t4.toFixed(@json(session('logged_session_data.decimal_point'))));
    $('#taxableamount_total').text(t5.toFixed(@json(session('logged_session_data.decimal_point'))));
    $('#vatamount_total').text(t6.toFixed(@json(session('logged_session_data.decimal_point'))));
    $('#net_total').text((t5+t6).toFixed(@json(session('logged_session_data.decimal_point'))));
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

                        
                    
                    <div class="row mt-40">
                    <div class="col-lg-12 text-left mb-2">
                        @if(count($editDataAdjustments)>0)
                        <b>Adjusted Items</b>
                            <table class="table table-bordered table-striped" id="br-table" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th style="width:50px;">@lang('#')</th>
                                        <th style="width:100px;">@lang('Doc Number')</th>
                                        <th style="width:100px;">@lang('Doc Date')</th>
                                        <th style="width:100px;">@lang('DLN NO')</th>
                                        <th style="width:100px;" class="text-right">Total</th>
                                        <th style="width:100px;" class="text-right">Paid</th>
                                        <th style="width:100px;" class="text-right">Balance</th>
                                        <th style="width:100px;" class="text-right">Adjusted</th>
                                        <th style="width:100px;" class="text-right"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach ($editDataAdjustments as $item)
                                    <tr>
                                        <td>{{ @$loop->iteration }}</td>
                                        <td>{{ @$item->siv_no }}</td>
                                        <td>{{ @$item->doc_date }}</td>
                                        <td>{{ @$item->dln_no }}</td>
                                        <td class="text-right">{{ @$item->total_amount }}</td>
                                        <td class="text-right">{{ @$item->paid_amount }}</td>
                                        <td class="text-right">{{ @$item->balance_amount }}</td>
                                        <td class="text-right">{{ @$item->paid_amount }}</td>
                                        <td class="text-right"><a class="btn-sm btn-danger" href="{{url('delete-sales-return-adjustment/'.$item->id)}}" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>


                        <div class="row mt-40">
                            <div class="col-lg-12 text-right">
                                <a class="btn btn-info" data-modal-size="modal-md" data-target="#adj_popup_win" data-toggle="modal">Adjustment</a>
                                <button type="submit" class="btn btn-primary" value="1" name="btnSubmit" id="btnSubmit">
                                    <span class="ti-check"></span>
                                    Save & Print Sales Return
                                </button>
                                <button type="submit" class="btn btn-primary" name="btnSubmit" id=" ">
                                    <span class="ti-check"></span>
                                    @if (isset($edit))
                                        @lang('lang.update')
                                    @else
                                        @lang('lang.save')
                                    @endif
                                    @lang('Sales Return')
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
        <div class="modal fade admin-query" id="dn_pending_popup_win" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 80%;">
                <div class="modal-content">
                    <div class="modal-header m-0 p-3">
                        <h4 class="modal-title">Invoice Pending List</h4>
                        <button class="close" data-dismiss="modal" type="button">
                            ×
                        </button>
                    </div>
                    <div class="modal-body m-0 p-3">
                        <input type="hidden" id="hd_pending_dn_id" />
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

                                        <button class="btn btn-primary bg-success" type="button" id="addDnPendingItems">
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

    {{-- popup --}}    
    <div class="modal fade admin-query" id="adj_popup_win">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 80%;">
            <div class="modal-content">
                <div class="modal-header m-0 p-3">
                    <h4 class="modal-title">Bill Wise Adjestment</h4>
                    <button class="close" data-dismiss="modal" type="button">
                        ×
                    </button>
                </div>
                <div class="modal-body m-0 p-3">
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'sales-return-add-adjestment', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'sales-return-add-adjestment']) }}
                    <input type="hidden" value="{{ $edit->doc_number }}" name="adj_srn_no">
                    <input type="hidden" value="{{ $edit->dn_doc_number }}" name="adj_dn_doc_number">
                    <input type="hidden" value="{{ $edit->doc_date }}" name="edit_adj_doc_date">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="equipment comon-status row mt-40 d-block">
                                    <input type="text" id="act_srn_adj_amount" value="{{ ($edit_list->sum('taxableamount')+$edit_list->sum('vatamount')) }}" hidden/>
                                    <input type="text" id="srn_adj_amount" value="{{ ($edit_list->sum('taxableamount')+$edit_list->sum('vatamount')) }}" hidden />

                                    <table class="table table-bordered table-striped" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                {{-- <th style="width:100px;">@lang('Doc Date')</th>
                                                <th style="width:100px;">@lang('SIV NO')</th>
                                                <th style="width:100px;">@lang('Total')</th>
                                                <th style="width:100px;">@lang('Paid')</th>
                                                <th style="width:100px;">@lang('Balance')</th> --}}

                                                
                                                <th style="width:100px;">@lang('Doc No')</th>
                                                <th style="width:100px;">@lang('Doc Date')</th>
                                                <th style="width:100px;">@lang('LPO NO')</th>
                                                <th style="width:100px;">@lang('Total')</th>
                                                <th style="width:100px;">@lang('Balance')</th>
                                                <th style="width:100px;">@lang('Paid')</th>
                                                {{-- <th style="width:100px;">@lang('Adjustment')</th> --}}
                                                <th style="width:100px;">@lang('Narration')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $i=0; @endphp
                                            @if (count($srn_adjestment)>0)
                                            @foreach ($srn_adjestment as $dt)
                                            @php
                                            
                                            if($dt->total_paid_amount==""){$paid_amount = 0;} else {$paid_amount = $dt->total_paid_amount;}
                                            $balance_amount = abs($dt->total_amount - $paid_amount);

                                            @endphp
                                            @if($balance_amount > 0)
                                            <tr>
                                                <td style="width:100px;"><input type="text" class="form-control" name="adj_siv_no[]" id="adj_siv_no_{{ $i }}" value="{{ $dt->doc_number }}" readonly /></td>
                                                <td style="width:100px;"><input type="text" class="form-control" name="adj_doc_date[]" id="adj_doc_date_{{ $i }}" value="{{ date('d/m/Y', strtotime($dt->doc_date)) }}" readonly /></td>
                                                <td style="width:100px;"><input type="text" class="form-control" name="lpo_number[]" id="lpo_number_{{ $i }}" value="{{ $dt->lpo_number }}" readonly /></td>
                                                <td style="width:100px;"><input type="text" class="form-control" name="adj_total[]" id="adj_total_{{ $i }}" value="{{ $dt->total_amount }}" readonly /></td>
                                                <td style="width:100px;"><input type="text" class="form-control" name="adj_balance[]" id="adj_balance_{{ $i }}" value="{{ $balance_amount }}" readonly /></td>
                                                <td style="width:100px;"><input type="text" class="form-control class_adj_paid" name="adj_paid[]" id="adj_paid_{{ $i }}" value="" onchange="get_set_amount()" onclick="set_adjestment({{ $i }})" required /></td>
                                                {{-- <td style="width:100px;"><input type="text" class="form-control" name="bi_amount[]" id="bi_amount_{{ $i }}" value="{{ $balance_amount }}" readonly /></td> --}}
                                                <td style="width:100px;"><input type="text" class="form-control" name="narration[]" id="narration_{{ $i }}" value="{{ $dt->narration }}" /></td>
                                            </tr>
                                            @php $i++; @endphp
                                            @endif
                                            {{--  <tr>
                                                <td style="width:100px;"><input type="text" class="form-control" name="adj_doc_date" value="{{ date('d/m/Y', strtotime($dt->doc_date)) }}" readonly /></td>
                                                <td style="width:100px;"><input type="text" class="form-control" name="adj_dln_no" value="{{ $dt->dln_no }}" readonly /></td>
                                                <td style="width:100px;"><input type="text" class="form-control" name="adj_siv_no" value="{{ $dt->siv_no }}" readonly /></td>
                                                <td style="width:100px;"><input type="text" class="form-control" name="adj_total" id="adj_total" value="{{ $dt->total_amount }}" readonly /></td>
                                                <td style="width:100px;"><input type="text" class="form-control class_adj_paid" name="adj_paid" id="adj_paid" value="{{ $dt->paid_amount }}" onchange="get_set_amount()" required /></td>
                                                <td style="width:100px;"><input type="text" class="form-control" name="adj_balance" id="adj_balance" value="{{ $dt->balance_amount }}" readonly /></td>
                                            </tr>  --}}
                                            @endforeach
                                            <?php /*
                                            @else
                                            <tr>
                                                <td style="width:100px;"><input type="text" class="form-control" name="adj_doc_date" value="{{ $edit->doc_date }}" readonly /></td>
                                                <td style="width:100px;"><input type="text" class="form-control" name="adj_dln_no" value="{{ $edit->dn_doc_number }}" readonly /></td>
                                                <td style="width:100px;"><input type="text" class="form-control" name="adj_siv_no" value="{{ $edit->si_doc_number }}" readonly /></td>
                                                <td style="width:100px;"><input type="text" class="form-control" name="adj_total" id="adj_total" value="{{ $invoice_amount }}" readonly /></td>
                                                <td style="width:100px;"><input type="text" class="form-control" name="adj_paid" id="adj_paid" value="" onchange="get_set_amount()" required /></td>
                                                <td style="width:100px;"><input type="text" class="form-control" name="adj_balance" id="adj_balance" value="" readonly /></td>
                                            </tr>
                                            */ ?>
                                            @endif
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th><label id="footer_total" /></th>
                                                <th><label id="footer_balance" /></th>
                                                <th><label id="footer_paid" /></th>
                                                {{-- <th></th> --}}
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <script>
                            function get_set_amount(id)
                            {
                                set_adjestment(id);
                                var adj_total = Number($('#adj_total_'+id).val());
                                var adj_paid = Number($('#adj_paid_'+id).val());
                                $('#adj_balance_'+id).val(adj_total - adj_paid);
                            }

                            function set_adjestment(id){
                                var sum = $('#act_srn_adj_amount').val();
                                var numItems = $('.class_adj_paid').length;
                                var adj=0;
                                for(i=0; i < numItems; i++){
                                    if(i!=id){
                                        adj +=  Number($('#adj_paid_'+i).val());
                                    }
                                }
                                var adj2 = sum - adj;
                                

                                if(adj2 > 0){
                                    $('#srn_adj_amount').val(adj2);
                                }
                                else { $('#srn_adj_amount').val(0); }

                                var adj3 = $('#srn_adj_amount').val();

                                if(adj3 > 0){
                                    var adj_total = Number($('#adj_balance_'+id).val());
                                    if(adj3 >= adj_total){
                                        $('#adj_paid_'+id).val(adj_total);
                                    }
                                    else{
                                        $('#adj_paid_'+id).val(adj3);
                                    }
                                }
                            }

                        </script>

                        <div class="row">
                            <div class="col-lg-12">    
                                <div class="col-lg-12 text-right">
                                    <div class="">
                                        @if (count($srn_adjestment)>0)
                                        <button class="btn btn-success fix-gr-bg" type="submit">Add</button>
                                        @else
                                        <button class="btn btn-success fix-gr-bg" type="submit">Add</button>
                                        @endif
    
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
    {{-- popup --}}

    <!-- Modal Change Currancy-->
    <div class="modal fade" id="ModalChangeCurrancy" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Change Currancy</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'sales-return-update-currency', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Currancy From</label>
                                <select class="form-control" name="from_currency_id" required>
                                    @foreach ($currency as $value)
                                        @if($edit->currency == $value->id)
                                            <option value="{{ @$value->id }}" >{{ @$value->code }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Currancy To</label>
                                <select class="form-control" name="to_currency_id" id="to_currency_id" required onchange="set_rate()">
                                    <option value="">Select</option>
                                    @foreach ($currencylist2 as $value)
                                        <option value="{{ @$value->id }}">{{ @$value->code }}</option>
                                    @endforeach
                                </select>
                                @foreach ($currencylist2 as $value)
                                    <input type="hidden" id="rate_{{ @$value->id }}" name="rate_{{ @$value->id }}" value="{{ @$value->rate }}" />
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Default Currency Conversion Rate</label>
                                <input type="text" class="form-control" id="to_currency_rate" name="to_currency_rate" required />
                            </div>
                        </div>
                        <script>
                            function set_rate(){
                                var id = $('#to_currency_id').val();
                                var rate = $('#rate_'+id).val();

                                $('#to_currency_rate').val(rate);
                            }

                        </script>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="cur_sr_id" value="{{ @$edit->id }}"/>
                    <input type="hidden" name="cur_sr_doc_no" value="{{ @$edit->doc_number }}"/>                    
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Change</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!-- Modal Change Currancy-->


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

    function popup_dn_pending(id) {
        $("#loading_bg").css("display", "block");
        $("#hd_pending_dn_id").val(id);
        $("#dn_id").val(id);
        document.getElementById('addDnPending').click();
        $("#loading_bg").css("display", "none");
    }

    $(document).on("change", "#customer", function () {
        var id = $("#customer").val();
        get_dn_list(id);
        get_cust_details(id);
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
                            $('#shipping_name').val(dataResult['data'][i].contcat_person);
                            $('#shipping_address').val(dataResult['data'][i].address);
                            $('#customer_type').val(dataResult['data'][i].customer_type);
                            $('#sale_type').val(dataResult['data'][i].sale_type);
                            $('#country').val(dataResult['data'][i].vat_country);
                            $('#state').val(dataResult['data'][i].vat_state);
                            $('#sales_man').val(dataResult['data'][i].sales_person);
                            $('#net_vat').val(dataResult['data'][i].vat_percentage);
                        }
                    }
                    else{
                        $('#shipping_name').val();
                        $('#shipping_address').val();
                        $('#customer_type').val();
                        $('#sale_type').val();
                        $('#country').val();
                        $('#state').val();
                        $('#net_vat').val();
                    }
                    $("#loading_bg").css("display", "none");
            }
        });
    }
    
    function get_dn_list(id) {
        $("#loading_bg").css("display", "block");
        var action = "{{ URL::to('get-dn-list') }}";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                id: id,
            },
            cache: false,
            success: function(dataResult) {
                $("#plist").empty();
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
                                "<input type='radio' onclick='popup_dn_pending(" + id +
                                ")' id='pending_dn_" + i +
                                "' name='pending_dn' value='" + doc_number +
                                "'> <label for='pending_dn" + i + "'> " + doc_number +
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



<!-- Modal License Key-->
    <a id="btn_ModalLicenseKey" data-toggle="modal" data-target="#ModalLicenseKey" data-backdrop="static" data-keyboard="false"></a>
    <div class="modal fade" id="ModalLicenseKey" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Select License Key (<label id="ModalLabelHeading" ></label> )</h5>
                    <input type="hidden" id="part_no" />
                    <input type="hidden" id="update_id" />
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mt-2">
                            <table id="lk-table" class="table table-bordered table-striped" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;">Select</th>
                                        <th style="width: 15%;">Expiry Date</th>
                                        <th style="width: 50%;">Licence Key</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mt-2">
                            <label id="selected_key">0</label> Keys Selected out of <label id="total_key">0</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    {{--  <button class="btn btn-primary" onclick="set_license_key()" type="button">Add Selected</button>  --}}
                    <button class="btn btn-primary" type="button" data-dismiss="modal" disabled>Save</button>
                    <button class="btn btn-secondary" id="popup_close" type="button" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        function set_license_key_po(rowid,producttype){
            $('#qty_'+rowid).keypress(function (e) {
                var key = e.which;
                if(key === 13) { //the enter key code
                    var pt = producttype;
                    if(pt == 2) {
                        var part_id =$('#part_id_'+rowid).val();
                        $('#ModalLabelHeading').text($('#part_number_'+rowid).val());    
                        $('#part_no').val(part_id);
                        $('#btn_ModalLicenseKey').click();
                        get_license_key(part_id);
                    }
                    return true;
                }
            });
        }
        function get_license_key(part_id){
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('sales-return-get-license-key') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    item_id : part_id,
                    sales_return_id : $('#sr_id').val(),
                },
                cache: false,
                success: function(dataResult) {
                    //alert(dataResult);
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var getSelectedRows="";
                        if(dataResult['data'] != null){
                            len = dataResult['data'].length;
                            $('#total_key').text(len);
                        }
                        if(len > 0){
                            for(var i=0; i<len; i++){
                                getSelectedRows +="<tr>\
                                    <td><input class='chk_key' checked type='checkbox' id='select_key_"+ Number(i+1) +"' onclick='key_select_change("+ Number(i+1) +")'  /><input type='hidden' id='item_key_id_"+ Number(i+1) +"' value='"+dataResult['data'][i].id+"' /></td>\
                                    <td>"+dataResult['data'][i].exp_date+"</td>\
                                    <td>"+dataResult['data'][i].license_key+"</td>\
                                    </tr>";                                    
                            }
                            $('#license_key').val('');
                            $('#exp_date').val('');
                            $('#lk-table tbody').empty();
                            $("#lk-table tbody").append(getSelectedRows); 
                        }
                        else{
                            $('#lk-table tbody').empty();
                        }
                }
            });
            $("#loading_bg").css("display", "none");
        }

        function key_select_change(id){
            $('#select_key_'+id).on('change', function() { 
                if (this.checked) {
                }
            });

            var a = 0;
            var b = 1;
            var itm_id = 0;
            $(".chk_key").each(function() {
                if(this.checked){
                    a = Number(a+1);
                    if(itm_id == 0){
                        itm_id = $('#item_key_id_'+b).val();
                    }
                    else{
                        itm_id += ','+$('#item_key_id_'+b).val();
                    }
                }
                b++;
            });
            $('#update_id').val(itm_id);
            $('#selected_key').text(a);
        }
        function set_license_key(){
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('sales-return-update-license-key') }}";
            var myArray = $('#update_id').val(); 
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id : myArray,
                    item_id : $('#part_no').val(),
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    $('#popup_close').click();
                }
            });
            $("#loading_bg").css("display", "none");
        }

        
        $(window).ready(function() {
            $("#sales-return-update").on("keypress", function (event) {
                var keyPressed = event.keyCode || event.which;
                if (keyPressed === 13) {
                    event.preventDefault();
                    return false;
                }
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
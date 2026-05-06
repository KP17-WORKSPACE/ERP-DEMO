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
                <h2 class="page-heading m-0">Purchase Invoice Edit</h2>
                <span class="page-label">Home - Purchase Invoice</span>
            </div>
            <div>
                <a data-modal-size="modal-md" data-target="#attachment_popup_win" data-toggle="modal" class="btn btn-primary" onclick="view_attachment()"><i class="fa fa-plus"></i> Attachment</a>
                <a href="{{ url('purchase-invoice/create') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i>New</a>
                <a href="{{ url('purchase-invoice/'.$edit_pi->id.'/view') }}" type="button" class="btn btn-warning"><i class="fa fa-list"></i> View</a>
                <!-- Input with Search -->
                <div style="float: left; margin-right:5px; position: relative; width: 200px;">
                    <input type="text" id="quick_search_doc_number" placeholder="PI Number" class="form-control pr-4" /> 
                    <span style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); color: #aaa; pointer-events: none;">
                    <i class="fas fa-search"></i>
                    </span>
                </div>
                <script>
                    const baseUrl = "{{ url('get-edit-url-purchase-invoice') }}";                
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
                <a href="{{ url('purchase-invoice') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i>List</a>
            </div>
        </div>
        <div class="card p-4 mb-2">

            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'purchase-invoice-update', 'method' => 'POST', 'id' => 'purchase-invoice-create-form']) }}

            <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
            <input type="hidden" name="id" id="pi_id" value="{{ isset($edit_pi) ? $edit_pi->id : '' }}">
                <input type="hidden" name="net_vat" id="net_vat">

            <div class="row">
                <div class="col-lg-4 mb-2">
                    <label class="txtlbl">@lang('Vendor') <span>*</span></label>
                    <select class="form-control js-account-select" name="vendors" id="vendors">
                        <option value=""></option>
                        @foreach ($vendors as $value)
                        <option value="{{ @$value->id }}" {{ isset($edit_pi) ? (!empty($edit_pi->vendors) ? (@$edit_pi->vendors == @$value->id ? 'selected' : '') : '') : '' }}>
                            {{ @$value->account_name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="input-effect">
                                <label class="txtlbl">PIV Number<span>*</span></label>
                                <input class="form-control" type="text" name="doc_number" autocomplete="off" id="doc_number" value="{{ @$edit_pi->doc_number }}" >
                                    <input type="hidden" name="doc_number_main" value="{{ $edit_pi->doc_number }}" >
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="txtlbl">PIV Date</label>
                                        @php
                                            $value = date('Y-m-d');
                                            if (isset($edit_pi) && !empty($edit_pi->pi_date)) {
                                                @$value = date('Y-m-d', strtotime(@$edit_pi->pi_date));
                                            } else {
                                                if (!empty(old('pi_date'))) {
                                                    @$value = old('pi_date');
                                                } else {
                                                    @$value = date('Y-m-d');
                                                }
                                            }
                                        @endphp
                                        <input class="form-control" id="pi_date" type="date" autocomplete="off"
                                            name="pi_date" value="{{ @$value }}" style="margin-top: 0px">
                                        <span class="focus-border"></span>
                                        @if ($errors->has('pi_date'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('pi_date') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">Currency<span>*</span></label>
                                <a class="text-danger float-right" data-toggle="modal" data-target="#ModalChangeCurrancy">Change Currency</a>
                                <select class="form-control js-example-basic-single" name="currency" id="currency">
                                    @foreach ($currency as $value)
                                    @if($edit_pi->currency == @$value->id)
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
                            style="width: 100%; height: 320px; border: solid 1px #dfe1d7; border-radius: 5px; padding: 5px 5px; overflow-y: scroll;">
                        </div>
                        <a data-modal-size="modal-md" data-target="#grn_pending_popup_win" id="addGRNPending"
                            data-toggle="modal"></a>
                        <input type="hidden" id="grn_id" name="grn_id">
                        <input type="hidden" id="po_id" name="po_id">
                        <input type="hidden" id="vat_percentage" name="vat_percentage" value="5">
                    </div>

                </div>
                
                        
                <div class="col-lg-8 mb-2">
                    <div class="row">
                        <div class="col-lg-4 mb-0">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('LPO Number') <span>*</span></label>
                                <input
                                    class="txtbx primary-input form-control {{ $errors->has('lpo_number') ? ' is-invalid' : '' }}"
                                    type="text" name="lpo_number" autocomplete="off" id="lpo_number"
                                    value="{{ isset($edit_pi) ? (!empty(@$edit_pi->lpo_number) ? @$edit_pi->lpo_number : old('lpo_number')) : '' }}">
                                <span class="focus-border"></span>
                                @if ($errors->has('lpo_number'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('lpo_number') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('LPO Date') <span>*</span></label>
                                @php
                                    $value = date('Y-m-d');
                                    if (isset($edit_pi) && !empty($edit_pi->lpo_date)) {
                                        @$value = date('Y-m-d', strtotime(@$edit_pi->lpo_date));
                                    } else {
                                        if (!empty(old('lpo_date'))) {
                                            @$value = old('lpo_date');
                                        } else {
                                            @$value = date('Y-m-d');
                                        }
                                    }
                                @endphp
                                <input class="form-control" id="lpo_date" type="date" autocomplete="off" name="lpo_date" value="{{ @$value }}" style="margin-top:0px;">
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Payment Terms')<span>*</span></label>
                                <select
                                    class="form-control" required
                                    name="payment_terms" id="payment_terms" onchange="fn_payment_terms()">
                                    <option value=""></option>
                                    @foreach ($paymentterms as $value)
                                        <option value="{{ @$value->id }}"
                                            {{ isset($edit_pi) ? (!empty(@$edit_pi->payment_terms) ? (@$edit_pi->payment_terms == @$value->id ? 'selected' : '') : '') : '' }}>
                                            {{ @$value->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="div_payment_terms" style="display: none; padding-top: px;">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Other Payment Terms')<span>*</span></label>
                                    <input
                                        class="txtbx primary-input form-control {{ $errors->has('payment_terms2') ? ' is-invalid' : '' }}"
                                        type="text" name="payment_terms2" autocomplete="off" id="payment_terms2"
                                        value="{{ isset($edit_pi) ? (!empty(@$edit_pi->payment_terms2) ? @$edit_pi->payment_terms2 : old('payment_terms2')) : '' }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Bill Number')<span>*</span></label>
                                <input
                                    class="form-control"
                                    type="text" name="bill_number" autocomplete="off" id="bill_number"
                                    value="{{ isset($edit_pi) ? (!empty(@$edit_pi->bill_number) ? @$edit_pi->bill_number : old('bill_number')) : '' }}">
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Bill Date')*</label>
                                @php
                                    $value = date('Y-m-d');
                                    if (isset($edit_pi) && !empty($edit_pi->bill_date)) {
                                        @$value = date('Y-m-d', strtotime(@$edit_pi->bill_date));
                                    } else {
                                        if (!empty(old('bill_date'))) {
                                            @$value = old('bill_date');
                                        } else {
                                            @$value = date('Y-m-d');
                                        }
                                    }
                                @endphp
                                <input class="form-control" id="bill_date" type="date" autocomplete="off"
                                    name="bill_date" value="{{ @$value }}" required >
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('AWB No') <span>*</span></label>
                                <input class="txtbx primary-input form-control {{ $errors->has('awbno') ? ' is-invalid' : '' }}"
                                    type="text" name="awbno" autocomplete="off"
                                    value="{{ isset($edit_pi) ? (!empty(@$edit_pi->awbno) ? @$edit_pi->awbno : old('awbno')) : old('awbno') }}"
                                    id="awbno">
                            </div>        
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('BOE No') <span>*</span></label>
                                <input class="txtbx primary-input form-control {{ $errors->has('boeno') ? ' is-invalid' : '' }}"
                                    type="text" name="boeno" autocomplete="off"
                                    value="{{ isset($edit_pi) ? (!empty(@$edit_pi->boeno) ? @$edit_pi->boeno : old('boeno')) : old('boeno') }}"
                                    id="boeno">
                            </div>        
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Warehouse') <span>*</span></label>
                                <input
                                    class="form-control"
                                    type="text" name="warehouse" autocomplete="off"
                                    value="{{ isset($edit_pi) ? (!empty(@$edit_pi->warehouse) ? @$edit_pi->warehouse : old('warehouse')) : old('warehouse') }}"
                                    id="warehouse">
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Reference') <span>*</span></label>
                                <input
                                    class="form-control"
                                    type="text" name="reference" autocomplete="off"
                                    value="{{ isset($edit_pi) ? (!empty(@$edit_pi->reference) ? @$edit_pi->reference : old('reference')) : old('reference') }}"
                                    id="reference">
                                <span class="focus-border"></span>
                                @if ($errors->has('reference'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('reference') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Created') @lang('By')<span>*</span></label>
                                <input
                                    class="form-control"
                                    type="text" name="createdby" autocomplete="off" id="createdby"
                                    value="{{ isset($edit_pi) ? (!empty(@$edit_pi->created_by) ? @$edit_pi->createdby->full_name : old('createdby')) : Auth::user()->full_name }}"
                                    readonly>
                                <span class="focus-border"></span>
                                @if ($errors->has('createdby'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('createdby') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('GRN No')<span>*</span></label>
                                <input
                                    class="form-control"
                                    type="text" name="grn_no" autocomplete="off" id="grn_no"
                                    value="{{ isset($edit_pi) ? (!empty(@$edit_pi->grn_no) ? @$edit_pi->grn_no : old('grn_no')) : '' }}">
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('GRN Date')<span>*</span></label>
                                <input
                                    class="form-control" type="date" name="grn_date" autocomplete="off" id="grn_date" required
                                    value="{{ isset($edit_pi) ? (!empty(@$edit_pi->grn_date) ? date('Y-m-d', strtotime(@$edit_pi->grn_date)) : old('grn_date')) : '' }}">
                            </div>
                        </div>
                        <div class="col-lg-3 mb-2">
                            <div class="input-effect">
                                <label class="dynamicslbl">@lang('Salesman Name')*</label>
                                <select class="form-control js-example-basic-single" required name="sales_person" id="sales_person">
                                    <option value=""></option>
                                    @foreach ($salesman as $value)
                                        <option value="{{ @$value->user_id }}" @if(@$edit_pi->sales_person==$value->user_id) selected @endif>{{ @$value->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-8 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Narration')<span>*</span></label>
                                <input
                                    class="form-control"
                                    type="text" name="narration" autocomplete="off" id="narration"
                                    value="{{ isset($edit_pi) ? (!empty(@$edit_pi->narration) ? @$edit_pi->narration : old('narration')) : '' }}">
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Deal Id')<span>*</span></label>
                                <input
                                    class="form-control"
                                    type="text" name="deal_id" autocomplete="off" id="deal_id"
                                    value="{{ isset($edit_pi) ? (!empty(@$edit_pi->deal_id) ? @App\SysHelper::get_code_from_dealid_list($edit_pi->deal_id) : old('deal_id')) : '' }}">
                            </div>
                        </div>

                    </div>
                    
                </div>

                <div class="col-lg-12 mb-0">

                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                          <a class="nav-link active" id="shipping-tab" data-toggle="tab" href="#shipping" role="tab" aria-controls="shipping" aria-selected="true">Shipping Details</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="vat-tab" data-toggle="tab" href="#vat" role="tab" aria-controls="vat" aria-selected="false">VAT Details</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="shipping" role="tabpanel" aria-labelledby="shipping-tab">
                            <div class="row mt-2">
                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <a class="primary-btn fix-gr-bg" data-modal-size="modal-md" data-target="#add_to_do"
                                            data-toggle="modal"
                                            style="padding: 2px 7px !important; margin:5px 0 0 5px; float: right;"><span
                                                data-toggle="tooltip" title="Add Shipping Details" class="ti-plus"></span></a>
                                        <label class="dynamicslbl">@lang('Shipping Name') <span></span></label>
                                        <textarea type="text" class="form-control" cols="0"
                                        rows="4" name="shipping_name"
                                        id="shipping_name">{{ isset($edit_pi) ? (!empty(@$edit_pi->shipping_name) ? @$edit_pi->shipping_name : '') : old('shipping_name') }}</textarea>
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="dynamicslbl">@lang('Shipping Address 1') <span></span></label>
                                        <textarea type="text" class="form-control" cols="0"
                                            rows="4" name="shipping_address_1"
                                            id="shipping_address_1">{{ isset($edit_pi) ? (!empty(@$edit_pi->shipping_address_1) ? @$edit_pi->shipping_address_1 : '') : old('shipping_address_1') }}</textarea>
                                        <span class="focus-border textarea"></span>
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="dynamicslbl">@lang('Ship to Address 2') <span></span></label>
                                        <textarea type="text" class="form-control" cols="0"
                                            rows="4" name="shipping_address_2"
                                            id="shipping_address_2">{{ isset($edit_pi) ? (!empty(@$edit_pi->shipping_address_2) ? @$edit_pi->shipping_address_2 : '') : old('shipping_address_2') }}</textarea>
                                        <span class="focus-border textarea"></span>
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="dynamicslbl">@lang('Contact No') <span></span></label>
                                        <textarea type="text" class="form-control" cols="0"
                                            rows="4" name="shipping_contact_no"
                                            id="shipping_contact_no">{{ isset($edit_pi) ? (!empty(@$edit_pi->shipping_contact_no) ? @$edit_pi->shipping_contact_no : '') : old('shipping_contact_no') }}</textarea>
                                        <span class="focus-border textarea"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="vat" role="tabpanel" aria-labelledby="vat-tab">
                            <div class="row mt-2">
                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="dynamicslbl">@lang('Supplier Type') <span></span></label>
                                        <select
                                            class="dynamicstxt niceSelect w-100 bb form-control {{ $errors->has('supplier_type') ? ' is-invalid' : '' }}"
                                            name="supplier_type" id="supplier_type">
                                            <option value="0"></option>
                                            @foreach ($suppliertype as $value)
                                                <option value="{{ @$value->id }}"
                                                    {{ isset($edit_pi) ? (!empty(@$edit_pi->supplier_type) ? (@$edit_pi->supplier_type == @$value->id ? 'selected' : '') : '') : '' }}>
                                                    {{ @$value->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="dynamicslbl">@lang('Purchase Type') <span></span></label>
                                        <select
                                            class="dynamicstxt niceSelect w-100 bb form-control {{ $errors->has('purchase_type') ? ' is-invalid' : '' }}"
                                            name="purchase_type" id="purchase_type">
                                            <option value="0"></option>
                                            @foreach ($purchasetype as $value)
                                                <option value="{{ @$value->id }}"
                                                    {{ isset($edit_pi) ? (!empty(@$edit_pi->supplier_type) ? (@$edit_pi->supplier_type == @$value->id ? 'selected' : '') : '') : '' }}>
                                                    {{ @$value->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="txtlbl">@lang('Supplier Country')<span>*</span></label>
                                        <select class="form-control" name="supplier_country" id="country" required>
                                            <option data-display="" value=""></option>
                                            @foreach ($countries as $key => $value)
                                                <option value="{{ @$value->id }}"
                                                    <?php try{?>                                                        
                                                    @if (isset($edit_pi)) @if (@$edit_pi->supplier_country == $value->id) selected @endif
                                                    @endif
                                                    <?php } catch (\Throwable $th) {} ?>
                                                    >{{ @$value->name }} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="txtlbl">@lang('Supplier State')<span>*</span></label>
                                        <div id="sectionStateDiv">
                                            <select class="form-control" name="supplier_state" id="state">
                                                <option data-display="" value=""></option>
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
                    </div>
                </div>
        </div>
        <div class="equipment comon-status row mt-4 d-block">

            <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th style="width:50px;">SL</th>
                        <th style="width:150px;">@lang('Part No')</th>
                        <th>@lang('Description')</th>
                        <th style="width:100px;">@lang('Tax')</th>
                        <th style="width:100px;">@lang('Qty')</th>
                        <th style="width:120px;">@lang('Unit Price')</th>
                        <th style="width:120px;">@lang('Value')</th>
                        <th style="width:100px;">@lang('Discount')</th>
                        <th style="width:100px;">@lang('Freight')</th>
                        <th style="width:100px;">@lang('Customs')</th>
                        <th style="width:130px;">@lang('Taxable Amount')</th>
                        <th style="width:130px;">@lang('VAT Amount')</th>
                        <th style="width:130px;">@lang('Total')</th>
                        <th style="width:20px;"></th>
                    </tr>
                    <tr>
                        <td>
                            <input class="form-control" type="number" id="sort_id" name="sort_id[]" value="0" />
                        </td>
                        <td><input type="checkbox" checked hidden>
                            <select class="form-control js-product-select" name="part_number[]" id="part_number_new">
                                <option value=""></option>
                                {{-- @foreach ($items as $key => $value)
                                    <option value="{{ @$value->id }}">{{ @$value->part_number }}</option>
                                @endforeach --}}
                            </select>
                        </td>
                        <td>
                            <input class="form-control" type="text" id="description_new" name="description[]" autocomplete="off" readonly="true">
                        </td>
                        <td>
                            <input class="form-control" type="number" id="tax" name="tax[]" value="0" autocomplete="off" min="0" onchange="calc_change_new()">
                        </td>
                        <td>
                            <input class="form-control" type="number" id="qty" name="qty[]" autocomplete="off" min="0" onchange="calc_change_new()">
                        </td>
                        <td>
                            <input class="form-control" type="number" id="unitprice" name="unitprice[]" autocomplete="off" min="0" onchange="calc_change_new()">
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
                        </td>
                        <td>
                            <input class="form-control" type="number" id="value" name="value[]" autocomplete="off" min="0" readonly>
                        </td>
                        <td>
                            <input class="form-control" type="number" id="discount" name="discount[]" autocomplete="off" min="0" value="0" onchange="calc_change_new()">
                        </td>
                        <td>
                            <input class="form-control" type="number" id="fright" name="fright[]" autocomplete="off" min="0" value="0" onchange="calc_change_new()">
                        </td>
                        <td>
                            <input class="form-control" type="number" id="customcharges" name="customcharges[]" autocomplete="off" min="0" value="0" onchange="calc_change_new()">
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
                        <td><input type="hidden" id="item_id" />
                            <a onclick="return add_rows()" id="btn_add_row" class="btn btn-primary"><i class="fa fa-plus" aria-hidden="true"></i></a>
                            <a onclick="return update_rows()" style="display: none;" id="update_add_row" class="btn btn-warning"><i class="fa fa-plus" aria-hidden="true"></i></a>
                        </td>
                    </tr>
                    
                    <script>
                        // Bind once on DOM ready
                        document.addEventListener('DOMContentLoaded', function () {
                            // Focus qty on Enter in tax
                            document.querySelectorAll('input[name="tax[]"]').forEach(function (el) {
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
                    function calc_change_new(id) {
                        //var net_vat = $('#net_vat').val();
                        var net_vat = $('#tax').val();

                        var qty = $('#qty').val();
                        var unitprice = $('#unitprice').val();
                        var value = $('#value').val();
                        var discount = $('#discount').val();
                        var fright = $('#fright').val();
                        var customcharges = $('#customcharges').val();

                        qty = (qty === '') ? '0' : qty;
                        unitprice = (unitprice === '') ? '0' : unitprice;
                        var fin_value = (unitprice * qty);
                        $('#value').val(fin_value.toFixed(@json(session('logged_session_data.decimal_point'))));


                        value = (value === '') ? '0' : value;
                        discount = (discount === '') ? '0' : discount;
                        var fin_taxableamount = ((unitprice * qty) + Number(customcharges) + Number(fright) - Number(discount));
                        $('#taxableamount').val(fin_taxableamount.toFixed(@json(session('logged_session_data.decimal_point'))));

                        var fin_vatamount = ((unitprice * qty) + Number(customcharges) + Number(fright) - Number(discount)) * ((Number(net_vat)) / 100);
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
                        var action = "{{ URL::to('add-purchase-invoice-items') }}";
                        $.ajax({
                            url: action,
                            type: "POST",
                            data: {
                                _token: '{{ csrf_token() }}',
                                part_number: $("#part_number_new").val(),
                                tax: $("#tax").val(),
                                qty: $("#qty").val(),
                                unitprice: $("#unitprice").val(),
                                value: $("#value").val(),
                                discount: $("#discount").val(),
                                fright: $("#fright").val(),
                                customcharges: $("#customcharges").val(),
                                taxableamount: $("#taxableamount").val(),
                                vatamount: $("#vatamount").val(),
                                pi_id: $("#pi_id").val(),
                                sort_id: $("#sort_id").val(),
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

                                        var qty_total=0; var value_total=0; var discount_total=0; var taxableamount_total=0; var vatamount_total=0; var taxableamount_total1=0; var vatamount_total1=0; var amount_total=0;

                                        for(var i=0; i<len; i++){


                                            getSelectedRows +="<tr>\
                                                <td class='text-center'>"+dataResult['data'][i].sort_id+"<input type='hidden' name='sortid[]' id='sort_id_"+ (i+1) +"' value='"+dataResult['data'][i].sort_id+"' /></td>\
                                                <td>"+dataResult['data'][i].partno+"<input type='hidden' id='partno_"+ (i+1) +"' value='"+dataResult['data'][i].partno+"' /><input type='hidden' id='pid_"+ (i+1) +"' value='"+dataResult['data'][i].part_number+"' /></td>\
                                                <td>"+dataResult['data'][i].description+"<input type='hidden' id='description_"+ (i+1) +"' value='"+dataResult['data'][i].description+"' /></td>\
                                                <td class='text-right'>"+dataResult['data'][i].tax+"<input type='hidden' id='tax_"+ (i+1) +"' value='"+dataResult['data'][i].tax+"' /></td>\
                                                <td class='text-center'>"+dataResult['data'][i].qty+"<input type='hidden' id='qty_"+ (i+1) +"' value='"+dataResult['data'][i].qty+"' /></td>\
                                                <td class='text-right'>"+dataResult['data'][i].unitprice+"<input type='hidden' id='unitprice_"+ (i+1) +"' value='"+dataResult['data'][i].unitprice+"' /></td>\
                                                <td class='text-right'>"+dataResult['data'][i].value+"<input type='hidden' id='value_"+ (i+1) +"' value='"+dataResult['data'][i].value+"' /></td>\
                                                <td class='text-right'>"+dataResult['data'][i].discount+"<input type='hidden' id='discount_"+ (i+1) +"' value='"+dataResult['data'][i].discount+"' /></td>\
                                                <td class='text-right'>"+dataResult['data'][i].fright+"<input type='hidden' id='fright_"+ (i+1) +"' value='"+dataResult['data'][i].fright+"' /></td>\
                                                <td class='text-right'>"+dataResult['data'][i].customcharges+"<input type='hidden' id='customcharges_"+ (i+1) +"' value='"+dataResult['data'][i].customcharges+"' /></td>\
                                                <td class='text-right'>"+dataResult['data'][i].taxableamount+"<input type='hidden' id='taxableamount_"+ (i+1) +"' value='"+dataResult['data'][i].taxableamount+"' /></td>\
                                                <td class='text-right'>"+dataResult['data'][i].vatamount+"<input type='hidden' id='vatamount_"+ (i+1) +"' value='"+dataResult['data'][i].vatamount+"' /></td>\
                                                <td class='text-right'>"+(Number(dataResult['data'][i].taxableamount)+Number(dataResult['data'][i].vatamount))+"<input type='hidden' id='totalamount_"+ (i+1) +"' value='"+Number(dataResult['data'][i].taxableamount)+Number(dataResult['data'][i].vatamount)+"' /></td>\
                                                <td><input type='hidden' id='item_"+ (i+1) +"' value='"+dataResult['data'][i].id+"' /><a onclick='row_edit("+ (i+1) +")' class='btn-sm btn-info'><i class='fa fa-edit' aria-hidden='true'></i></a><a onclick='row_delete("+dataResult['data'][i].id+","+dataResult['data'][i].pi_id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
                                                </tr>";
                                                
                                                qty_total += Number(dataResult['data'][i].qty);
                                                value_total += Number(dataResult['data'][i].value);
                                                discount_total += Number(dataResult['data'][i].discount);
                                                taxableamount_total += Number(dataResult['data'][i].taxableamount);
                                                vatamount_total += Number(dataResult['data'][i].vatamount);

                                                taxableamount_total1 = Number(dataResult['data'][i].taxableamount);
                                                vatamount_total1 = Number(dataResult['data'][i].vatamount);        
                                                amount_total += Number(taxableamount_total1 + vatamount_total1);

                                            /*$("#payment_terms").val(dataResult['data'][i].part_number_new);
                                            $("#shipping_name").val(dataResult['data'][i].contcat_person);
                                            $("#shipping_address_1").val(dataResult['data'][i].address);
                                            $("#shipping_address_2").val(dataResult['data'][i].address2);
                                            $("#shipping_contact_no").val(dataResult['data'][i].contcat_number);
                                            $("#country").val(dataResult['data'][i].vat_country);
                                            $("#state").val(dataResult['data'][i].vat_state);*/
                                        }

                                        $("#part_number_new").val("none");
                                        $("#description_new").val("");
                                        //$("#tax").val("");
                                        $("#qty_total").text(qty_total);
                                        $("#value_total").text(value_total);
                                        $("#discount_total").text(discount_total);
                                        $("#taxableamount_total").text(taxableamount_total);
                                        $("#vatamount_total").text(vatamount_total);
                                        $("#amount_total").text(amount_total);

                                        $('#grn-pi-table tbody').empty();
                                        $("#grn-pi-table tbody").append(getSelectedRows);
                                        row_clear();
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
                        
                        $("#item_id").val($('#item_'+id).val());
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
                        $('#fright').val($('#fright_'+id).val());
                        $('#customcharges').val($('#customcharges_'+id).val());
                        $('#sort_id').val($('#sort_id_'+id).val());
                        //$('#totalamount').val($('#totalamount_'+id).val());
                        calc_change_new();

                    }

                    function row_clear() {
                        $("#part_number_new").val('');
                        $("#select2-part_number_new-container").html('');
                        $('#description_new').val('');
                        $('#sort_id').val('0');
                        $('#tax').val('0');
                        $('#qty').val('');
                        $('#unitprice').val('');
                        $('#value').val('');
                        $('#discount').val('0');
                        $('#taxableamount').val('');
                        $('#vatamount').val('');
                        $('#taxableamount').val('');
                        $('#totalamount').val('');
                        $("#fright").val('0'),
                        $("#customcharges").val('0'),
                        
                        $('#btn_add_row').css("display",'block');
                        $('#update_add_row').css("display",'none');
                    }
                    
                    function update_rows() {
                        $("#loading_bg").css("display", "block");
                        var action = "{{ URL::to('update-purchase-invoice-items') }}";
                        $.ajax({
                            url: action,
                            type: "POST",
                            data: {
                                _token: '{{ csrf_token() }}',
                                id : $("#item_id").val(),
                                part_number: $("#part_number_new").val(),
                                tax: $("#tax").val(),
                                qty: $("#qty").val(),
                                unitprice: $("#unitprice").val(),
                                value: $("#value").val(),
                                discount: $("#discount").val(),
                                fright: $("#fright").val(),
                                customcharges: $("#customcharges").val(),
                                taxableamount: $("#taxableamount").val(),
                                vatamount: $("#vatamount").val(),
                                pi_id: $("#pi_id").val(),
                                sort_id: $("#sort_id").val(),
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
                                        
                                    var qty_total=0; var value_total=0; var discount_total=0; var taxableamount_total=0; var vatamount_total=0; var taxableamount_total1=0; var vatamount_total1=0; var amount_total=0;
                                        
                                        for(var i=0; i<len; i++){

                                            getSelectedRows +="<tr>\
                                                <td class='text-center'>"+dataResult['data'][i].sort_id+"<input type='hidden' name='sortid[]' id='sort_id_"+ (i+1) +"' value='"+dataResult['data'][i].sort_id+"' /></td>\
                                                <td>"+dataResult['data'][i].partno+"<input type='hidden' id='partno_"+ (i+1) +"' value='"+dataResult['data'][i].partno+"' /><input type='hidden' id='pid_"+ (i+1) +"' value='"+dataResult['data'][i].part_number+"' /></td>\
                                                <td>"+dataResult['data'][i].description+"<input type='hidden' id='description_"+ (i+1) +"' value='"+dataResult['data'][i].description+"' /></td>\
                                                <td class='text-right'>"+dataResult['data'][i].tax+"<input type='hidden' id='tax_"+ (i+1) +"' value='"+dataResult['data'][i].tax+"' /></td>\
                                                <td class='text-center'>"+dataResult['data'][i].qty+"<input type='hidden' id='qty_"+ (i+1) +"' value='"+dataResult['data'][i].qty+"' /></td>\
                                                <td class='text-right'>"+dataResult['data'][i].unitprice+"<input type='hidden' id='unitprice_"+ (i+1) +"' value='"+dataResult['data'][i].unitprice+"' /></td>\
                                                <td class='text-right'>"+dataResult['data'][i].value+"<input type='hidden' id='value_"+ (i+1) +"' value='"+dataResult['data'][i].value+"' /></td>\
                                                <td class='text-right'>"+dataResult['data'][i].discount+"<input type='hidden' id='discount_"+ (i+1) +"' value='"+dataResult['data'][i].discount+"' /></td>\
                                                <td class='text-right'>"+dataResult['data'][i].fright+"<input type='hidden' id='fright_"+ (i+1) +"' value='"+dataResult['data'][i].fright+"' /></td>\
                                                <td class='text-right'>"+dataResult['data'][i].customcharges+"<input type='hidden' id='customcharges_"+ (i+1) +"' value='"+dataResult['data'][i].customcharges+"' /></td>\
                                                <td class='text-right'>"+dataResult['data'][i].taxableamount+"<input type='hidden' id='taxableamount_"+ (i+1) +"' value='"+dataResult['data'][i].taxableamount+"' /></td>\
                                                <td class='text-right'>"+dataResult['data'][i].vatamount+"<input type='hidden' id='vatamount_"+ (i+1) +"' value='"+dataResult['data'][i].vatamount+"' /></td>\
                                                <td class='text-right'>"+(Number(dataResult['data'][i].taxableamount)+Number(dataResult['data'][i].vatamount))+"<input type='hidden' id='totalamount_"+ (i+1) +"' value='"+Number(dataResult['data'][i].taxableamount)+Number(dataResult['data'][i].vatamount)+"' /></td>\
                                                <td><input type='hidden' id='item_"+ (i+1) +"' value='"+dataResult['data'][i].id+"' /><a onclick='row_edit("+ (i+1) +")' class='btn-sm btn-info'><i class='fa fa-edit' aria-hidden='true'></i></a><a onclick='row_delete("+dataResult['data'][i].id+","+dataResult['data'][i].pi_id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
                                                </tr>";
                                                qty_total += Number(dataResult['data'][i].qty);
                                                value_total += Number(dataResult['data'][i].value);
                                                discount_total += Number(dataResult['data'][i].discount);
                                                taxableamount_total += Number(dataResult['data'][i].taxableamount);
                                                vatamount_total += Number(dataResult['data'][i].vatamount);
                                                
                                                taxableamount_total1 = Number(dataResult['data'][i].taxableamount);
                                                vatamount_total1 = Number(dataResult['data'][i].vatamount);        
                                                amount_total += Number(taxableamount_total1 + vatamount_total1);
                                        }

                                        $("#part_number_new").val("none");
                                        $("#description_new").val("");
                                        //$("#tax").val("");
                                        $("#qty_total").text(qty_total);
                                        $("#value_total").text(value_total);
                                        $("#discount_total").text(discount_total);
                                        $("#taxableamount_total").text(taxableamount_total);
                                        $("#vatamount_total").text(vatamount_total);
                                        $("#amount_total").text(amount_total);

                                        $('#grn-pi-table tbody').empty();
                                        $("#grn-pi-table tbody").append(getSelectedRows);
                                        row_clear();
                                    }
                                    else{
                                        
                                    }
                            }
                        });
                        $("#loading_bg").css("display", "none");
                    }

                    function row_delete(id,pi_id) {
                        if (confirm("Are you sure you want to delete this item?") == false) {
                            return false;
                        }
                        $("#loading_bg").css("display", "block");
                        var action = "{{ URL::to('delete-purchase-invoice-items') }}";
                        $.ajax({
                            url: action,
                            type: "POST",
                            data: {
                                _token: '{{ csrf_token() }}',
                                id: id,
                                pi_id: pi_id,
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
                                        
                                    var qty_total=0; var value_total=0; var discount_total=0; var taxableamount_total=0; var vatamount_total=0; var taxableamount_total1=0; var vatamount_total1=0; var amount_total=0;
                                        
                                        for(var i=0; i<len; i++){

                                            getSelectedRows +="<tr>\
                                                <td class='text-center'>"+dataResult['data'][i].sort_id+"<input type='hidden' name='sortid[]' id='sort_id_"+ (i+1) +"' value='"+dataResult['data'][i].sort_id+"' /></td>\
                                                <td>"+dataResult['data'][i].partno+"<input type='hidden' id='partno_"+ (i+1) +"' value='"+dataResult['data'][i].partno+"' /><input type='hidden' id='pid_"+ (i+1) +"' value='"+dataResult['data'][i].part_number+"' /></td>\
                                                <td>"+dataResult['data'][i].description+"<input type='hidden' id='description_"+ (i+1) +"' value='"+dataResult['data'][i].description+"' /></td>\
                                                <td class='text-right'>"+dataResult['data'][i].tax+"<input type='hidden' id='tax_"+ (i+1) +"' value='"+dataResult['data'][i].tax+"' /></td>\
                                                <td class='text-center'>"+dataResult['data'][i].qty+"<input type='hidden' id='qty_"+ (i+1) +"' value='"+dataResult['data'][i].qty+"' /></td>\
                                                <td class='text-right'>"+dataResult['data'][i].unitprice+"<input type='hidden' id='unitprice_"+ (i+1) +"' value='"+dataResult['data'][i].unitprice+"' /></td>\
                                                <td class='text-right'>"+dataResult['data'][i].value+"<input type='hidden' id='value_"+ (i+1) +"' value='"+dataResult['data'][i].value+"' /></td>\
                                                <td class='text-right'>"+dataResult['data'][i].discount+"<input type='hidden' id='discount_"+ (i+1) +"' value='"+dataResult['data'][i].discount+"' /></td>\
                                                <td class='text-right'>"+dataResult['data'][i].fright+"<input type='hidden' id='fright_"+ (i+1) +"' value='"+dataResult['data'][i].fright+"' /></td>\
                                                <td class='text-right'>"+dataResult['data'][i].customcharges+"<input type='hidden' id='customcharges_"+ (i+1) +"' value='"+dataResult['data'][i].customcharges+"' /></td>\
                                                <td class='text-right'>"+dataResult['data'][i].taxableamount+"<input type='hidden' id='taxableamount_"+ (i+1) +"' value='"+dataResult['data'][i].taxableamount+"' /></td>\
                                                <td class='text-right'>"+dataResult['data'][i].vatamount+"<input type='hidden' id='vatamount_"+ (i+1) +"' value='"+dataResult['data'][i].vatamount+"' /></td>\
                                                <td class='text-right'>"+(Number(dataResult['data'][i].taxableamount)+Number(dataResult['data'][i].vatamount))+"<input type='hidden' id='totalamount_"+ (i+1) +"' value='"+Number(dataResult['data'][i].taxableamount)+Number(dataResult['data'][i].vatamount)+"' /></td>\
                                                <td><input type='hidden' id='item_"+ (i+1) +"' value='"+dataResult['data'][i].id+"' /><a onclick='row_edit("+ (i+1) +")' class='btn-sm btn-info'><i class='fa fa-edit' aria-hidden='true'></i></a><a onclick='row_delete("+dataResult['data'][i].id+","+dataResult['data'][i].pi_id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
                                                </tr>";
                                                qty_total += Number(dataResult['data'][i].qty);
                                                value_total += Number(dataResult['data'][i].value);
                                                discount_total += Number(dataResult['data'][i].discount);
                                                taxableamount_total += Number(dataResult['data'][i].taxableamount);
                                                vatamount_total += Number(dataResult['data'][i].vatamount);
                                                
                                                taxableamount_total1 = Number(dataResult['data'][i].taxableamount);
                                                vatamount_total1 = Number(dataResult['data'][i].vatamount);        
                                                amount_total += Number(taxableamount_total1 + vatamount_total1);
                                        }

                                        $("#part_number_new").val("none");
                                        $("#description_new").val("");
                                        //$("#tax").val("");
                                        $("#qty_total").text(qty_total);
                                        $("#value_total").text(value_total);
                                        $("#discount_total").text(discount_total);
                                        $("#taxableamount_total").text(taxableamount_total);
                                        $("#vatamount_total").text(vatamount_total);
                                        $("#amount_total").text(amount_total);

                                        $('#grn-pi-table tbody').empty();
                                        $("#grn-pi-table tbody").append(getSelectedRows);
                                        row_clear();
                                    }
                                    else{
                                        
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


            <table class="table table-bordered table-striped" id="grn-pi-table" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th style="width:50px;">SL</th>
                        <th style="width:120px;">@lang('Part No')</th>
                        <th>@lang('Description')</th>
                        <th style="width:100px; text-align:right">@lang('Tax')</th>
                        <th style="width:100px; text-align:center">@lang('Qty')</th>
                        <th style="width:100px; text-align:right;">@lang('Unit Price')</th>
                        <th style="width:100px; text-align:right;">@lang('Value')</th>
                        <th style="width:70px;" class="text-right">
                            <a style="cursor: pointer;" class="text-danger float-right" data-toggle="modal" data-target="#modalDiscount">Discount</a>
                        </th>
                        <th style="width:70px;" class="text-right">
                            <a style="cursor: pointer;" class="text-danger float-right" data-toggle="modal" data-target="#modalFreight">Freight</a>
                        </th>
                        <th style="width:130px;" class="text-right">
                            <a style="cursor: pointer;" class="text-danger float-right" data-toggle="modal" data-target="#modalCustom">Custom Charges</a>
                        </th>
                        <th style="width:100px; text-align:right;">@lang('Taxable Amount')</th>
                        <th style="width:100px; text-align:right;">@lang('VAT Amount')</th>
                        <th style="width:100px; text-align:right;">@lang('Amount')</th>
                        <th style="width:100px; text-align:right;"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $qty = 0; $unitprice = 0; $value = 0; $discount = 0; $fright=0; $custom=0; $taxableamount = 0; $vatamount = 0; $totalamount = 0; $i=1; ?>
                    @if (count($edit_pi_items)>0)
                    @foreach ($edit_pi_items as $dt)
                    <tr id="pi_row_{{ $i }}">
                        <td class="text-center">{{ $dt->sort_id}}<input type="hidden" name='sortid[]' id="sort_id_{{ $i }}" value="{{ $dt->sort_id}}" /></td>
                        <td>{{ $dt->partno }}<input type="hidden" id="partno_{{ $i }}" value="{{ $dt->partno}}" />
                            <input type="hidden" id="pid_{{ $i }}" value="{{ $dt->part_number}}"/></td>
                        <td>{{ $dt->description}}<input type="hidden" id="description_{{ $i }}" value="{{ $dt->description}}" /></td>
                        <td class="text-right">{{ $dt->tax}}<input type="hidden" id="tax_{{ $i }}" value="{{ $dt->tax}}" /></td>
                        <td class="text-center">{{ $dt->qty}}<input type="hidden" id="qty_{{ $i }}" value="{{ $dt->qty}}" /></td>
                        <td class="text-right">{{ $dt->unitprice}}<input type="hidden" id="unitprice_{{ $i }}" value="{{ $dt->unitprice}}" /></td>
                        <td class="text-right">{{ $dt->value}}<input type="hidden" id="value_{{ $i }}" value="{{ $dt->value}}" /></td>
                        <td class="text-right">{{ $dt->discount}}<input type="hidden" id="discount_{{ $i }}" value="{{ $dt->discount}}" /></td>
                        <td class="text-right">{{ $dt->fright}}<input type="hidden" id="fright_{{ $i }}" value="{{ $dt->fright}}" /></td>
                        <td class="text-right">{{ $dt->customcharges}}<input type="hidden" id="customcharges_{{ $i }}" value="{{ $dt->customcharges}}" /></td>
                        <td class="text-right">{{ $dt->taxableamount}}<input type="hidden" id="taxableamount_{{ $i }}" value="{{ $dt->taxableamount}}" /></td>
                        <td class="text-right">{{ $dt->vatamount}}<input type="hidden" id="vatamount_{{ $i }}" value="{{ $dt->vatamount}}" /></td>
                        <td class="text-right">{{ $dt->taxableamount + $dt->vatamount}}<input type="hidden" id="totalamount_{{ $i }}" value="{{ $dt->taxableamount+$dt->vatamount}}" /></td>
                        <td><input type="hidden" id="item_{{ $i }}" value="{{ $dt->id}}" />
                        <a onclick="row_edit({{ $i }})" class="btn-sm btn-info"><i class="fa fa-edit" aria-hidden="true"></i></a>
                        <a onclick="row_delete({{ $dt->id }},{{ $dt->pi_id }})" class="btn-sm btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></a></td>

                        </tr>
                        <?php $i++; $qty += $dt->qty; $unitprice += $dt->unitprice; $value += $dt->value; $discount += $dt->discount; $fright += $dt->fright; $custom += $dt->customcharges; $taxableamount += $dt->taxableamount; $vatamount += $dt->vatamount; $totalamount += ($dt->taxableamount+$dt->vatamount); ?>
                    @endforeach                            
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <td class="sstablefoot font-weight-bold"></td>
                        <td class="sstablefoot font-weight-bold"></td>
                        <td class="sstablefoot font-weight-bold"></td>
                        <td class="sstablefoot font-weight-bold"></td>
                        <td align="center" class="sstablefoot font-weight-bold"><label id="qty_total">{{ $qty }}</label></td>
                        <td align="right" class="sstablefoot font-weight-bold"></td>
                        <td align="right" class="sstablefoot font-weight-bold"><label id="value_total">{{ @App\SysHelper::com_curr_format($value, 2, '.', ',') }}</label></td>
                        <td align="right" class="sstablefoot font-weight-bold"><label id="discount_total">{{ @App\SysHelper::com_curr_format($discount, 2, '.', ',') }}</label></td>
                        <td align="right" class="sstablefoot font-weight-bold">{{ @App\SysHelper::com_curr_format($fright, 2, '.', ',') }}</td>
                        <td align="right" class="sstablefoot font-weight-bold">{{ @App\SysHelper::com_curr_format($custom, 2, '.', ',') }}</td>
                        <td align="right" class="sstablefoot font-weight-bold"><label id="taxableamount_total">{{ @App\SysHelper::com_curr_format($taxableamount, 2, '.', ',') }}</label></td>
                        <td align="right" class="sstablefoot font-weight-bold"><label id="vatamount_total">{{ @App\SysHelper::com_curr_format($vatamount, 2, '.', ',') }}</label></td>
                        <td align="right" class="sstablefoot font-weight-bold"><label id="amount_total">{{ @App\SysHelper::com_curr_format($totalamount, 2, '.', ',') }}</label></td>

                    </tr>
                </tfoot>
            </table>

            <div style="display: none;">
                <button type="button" class="primary-btn small fix-gr-bg" id="addRowPO"><span
                        class="ti-plus pr-2"></span>@lang('lang.item')</button>
            </div>


            <script>
                {{--  function row_delete(id){
                    $('#pi_row_' + id + '').css('display', 'none');
                    $('#isdelete_' + id + '').val(1);
                }  --}}
                function calc_change(id) {
        
                    var qty = $('#qty_' + id + '').val();
                    var tax = $('#tax_' + id + '').val();
                    var unitprice = $('#unitprice_' + id + '').val();                    
                    var discount = $('#discount_' + id + '').val();
        
                    qty = (qty === '') ? '0' : qty;
                    tax = (tax === '') ? '0' : tax;
                    unitprice = (unitprice === '') ? '0' : unitprice;
                    discount = (discount === '') ? '0' : discount;

                    var fin_value = (unitprice * qty);
                    $('#value_' + id + '').val(fin_value.toFixed(@json(session('logged_session_data.decimal_point'))));
        
                    var fin_taxableamount = ((unitprice * qty) - Number(discount));
                    $('#taxableamount_' + id + '').val(fin_taxableamount.toFixed(@json(session('logged_session_data.decimal_point'))));
        
                    var fin_vatableamount = ((unitprice * qty) - Number(discount)) * ((Number(tax)) / 100);
                    $('#vatamount_' + id + '').val(fin_vatableamount.toFixed(@json(session('logged_session_data.decimal_point'))));

                    var fin_totalamount = (Number(fin_taxableamount) + Number(fin_vatableamount));
                    $('#totalamount_' + id + '').val(fin_totalamount.toFixed(@json(session('logged_session_data.decimal_point'))));
        
                    //calc_total();
                }
        
                function calc_total() {
                    var numItems = $('.rno').length;
        
                    //alert(numItems);
        
                    var countrow = document.getElementById('row-count').value;
                    var t1 = 0,
                        t2 = 0,
                        t3 = 0,
                        t4 = 0,
                        t5 = 0,
                        t6 = 0,
                        t7 = 0;
                    for (var i = 1; i <= countrow; i++) {
                        t1 += Number($('#qty_' + i).val());
                        t2 += Number($('#unitprice_' + i).val());
                        t3 += Number($('#value_' + i).val());
                        t4 += Number($('#discount_' + i).val());
                        t5 += Number($('#taxamount_' + i).val());
                        t6 += Number($('#vatamount_' + i).val());
                    }
                    $('#qty_total').text(t1);
                    $('#unitprice_total').text(t2.toFixed(@json(session('logged_session_data.decimal_point'))));
                    $('#value_total').text(t3.toFixed(@json(session('logged_session_data.decimal_point'))));
                    $('#discount_total').text(t4.toFixed(@json(session('logged_session_data.decimal_point'))));
                    $('#taxableamount_total').text(t5.toFixed(@json(session('logged_session_data.decimal_point'))));
                    $('#vatamount_total').text(t6.toFixed(@json(session('logged_session_data.decimal_point'))));
                }

                function fn_addRow(id) {
                    var rownum = document.getElementById('po-row-count').value;
                    if (id == rownum) {
                        document.getElementById('po-row-count').value = (Number(rownum) + Number(1));
                        document.getElementById('addRowPO').click();
                    }
                }

                function ddl_part_change(id) {
                    var selOpt = $('#part_number_' + id + ' :selected').val();
                    $('#part_number_txt_' + id + ' option[value=' + selOpt + ']').attr('selected', 'selected');
                    var selOpt2 = $('#part_number_txt_' + id + ' :selected').text();
                    $('#description_' + id + '').val(selOpt2);
                    $('#description_' + id + '').focus();
                }



                function fn_payment_terms() {
                    var val_payment_terms = $('#payment_terms').val();
                    if (val_payment_terms == 22) {
                        $('#div_payment_terms').css('display', 'block');
                    } else {
                        $('#div_payment_terms').css('display', 'none');
                    }
                }

                function fn_shipping_name() {
                    var shipping_id = $('#shipping_name').val();
                    var shipping_data = $('#ship_' + shipping_id).val();
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
                <button type="button" class="primary-btn small fix-gr-bg" id="addRowEquipment">
                    <span class="ti-plus pr-2"></span>@lang('lang.item')</button>
            </div>
        </div>





        <div class="equipment comon-status row mt-4 d-block">
            <table class="table table-bordered table-striped" id="pi-table2" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th style="width:100px;">@lang('Name')</th>
                        <th style="width:350px;">@lang('Credit Account')</th>
                        <th style="width:70px;">@lang('Amount')</th>
                        <th style="width:80px;">@lang('Remarks')</th>
                        <th style="width:10px;"><input type="hidden" value="1" id="fright_row" />
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
                                    <option value="{{ @$value->id }}" {{isset($edit_cfc[0])? !empty(@$edit_cfc[0]->cfc_name)? @$edit_cfc[0]->cfc_name==$value->id ? 'selected':'':'':''}} >{{ @$value->account_code }} - {{ @$value->account_name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="form-control js-example-basic-single" name="cfc_credit_account[]" id="cfc_credit_account_1"
                                readonly="true">
                                <option value="none"></option>
                                @foreach ($vendors2 as $key => $value)
                                <option value="{{ @$value->id }}" {{isset($edit_cfc[0])? !empty(@$edit_cfc[0]->cfc_credit_account)? @$edit_cfc[0]->cfc_credit_account==@$value->id ? 'selected':'':'':''}}>{{ @$value->account_code }} - {{ @$value->account_name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input class="form-control" type="number" id="cfc_amount_1" name="cfc_amount[]" step="Any"
                                autocomplete="off" min="0" onchange="cfc_amount_change(1)" value="{{ isset($edit_cfc[0]) ? (!empty(@$edit_cfc[0]->cfc_amount) ? @$edit_cfc[0]->cfc_amount : old('')) : old('') }}" >
                        </td>
                        <td>
                            <input class="form-control" type="text" id="cfc_remarks_1" name="cfc_remarks[]"
                                autocomplete="off" value="{{ isset($edit_cfc[0]) ? (!empty(@$edit_cfc[0]->cfc_remarks) ? @$edit_cfc[0]->cfc_remarks : old('')) : old('') }}">
                        </td>
                        <td><a onclick="cfc_row_delete(1)" class="btn-sm btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
                    </tr>
                    <tr style="display: none;" id="fright_row_2">
                        @if (isset($edit_cfc[1]))
                        @if (@$edit_cfc[1]->cfc_amount != "")
                        <script>
                            add_fright_edit(2);
                        </script>                            
                        @endif                            
                        @endif
                        <td>
                            <select class="form-control js-example-basic-single" name="cfc_name[]" id="cfc_name_2">
                                <option value=""></option>
                                @foreach ($customs_freight_account as $key => $value)
                                    <option value="{{ @$value->id }}" {{isset($edit_cfc[1])? !empty(@$edit_cfc[1]->cfc_name)? @$edit_cfc[1]->cfc_name==$value->id ? 'selected':'':'':''}} >{{ @$value->account_code }} - {{ @$value->account_name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="form-control js-example-basic-single" name="cfc_credit_account[]" id="cfc_credit_account_2"
                                readonly="true">
                                <option value="none"></option>
                                @foreach ($vendors2 as $key => $value)
                                <option value="{{ @$value->id }}" {{isset($edit_cfc[1])? !empty(@$edit_cfc[1]->cfc_credit_account)? @$edit_cfc[1]->cfc_credit_account==@$value->id ? 'selected':'':'':''}}>{{ @$value->account_code }} - {{ @$value->account_name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input class="form-control" type="number" id="cfc_amount_2" name="cfc_amount[]" step="Any"
                                autocomplete="off" min="0" onchange="cfc_amount_change(2)" value="{{ isset($edit_cfc[1]) ? (!empty(@$edit_cfc[1]->cfc_amount) ? @$edit_cfc[1]->cfc_amount : old('')) : old('') }}" >
                        </td>
                        <td>
                            <input class="form-control" type="text" id="cfc_remarks_2" name="cfc_remarks[]"
                                autocomplete="off" value="{{ isset($edit_cfc[1]) ? (!empty(@$edit_cfc[1]->cfc_remarks) ? @$edit_cfc[1]->cfc_remarks : old('')) : old('') }}">
                        </td>
                        <td><a onclick="cfc_row_delete(2)" class="btn-sm btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
                    </tr>
                    <tr style="display: none;" id="fright_row_3">                        
                    @if (isset($edit_cfc[2]))
                    @if (@$edit_cfc[2]->cfc_amount != "")
                    <script>
                        add_fright_edit(3);
                    </script>                            
                    @endif                            
                    @endif
                    <td>
                        <select class="form-control js-example-basic-single" name="cfc_name[]" id="cfc_name_3">
                            <option value=""></option>
                            @foreach ($customs_freight_account as $key => $value)
                                <option value="{{ @$value->id }}" {{isset($edit_cfc[2])? !empty(@$edit_cfc[2]->cfc_name)? @$edit_cfc[2]->cfc_name==$value->id ? 'selected':'':'':''}} >{{ @$value->account_code }} - {{ @$value->account_name }}</option>
                            @endforeach
                        </select>
                    </td>
                        <td>
                            <select class="form-control js-example-basic-single" name="cfc_credit_account[]" id="cfc_credit_account_3"
                                readonly="true">
                                <option value="none"></option>
                                @foreach ($vendors2 as $key => $value)
                                <option value="{{ @$value->id }}" {{isset($edit_cfc[2])? !empty(@$edit_cfc[2]->cfc_credit_account)? @$edit_cfc[2]->cfc_credit_account==@$value->id ? 'selected':'':'':''}}>{{ @$value->account_code }} - {{ @$value->account_name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input class="form-control" type="number" id="cfc_amount_3" name="cfc_amount[]" step="Any"
                                autocomplete="off" min="0" onchange="cfc_amount_change(3)" value="{{ isset($edit_cfc[2]) ? (!empty(@$edit_cfc[2]->cfc_amount) ? @$edit_cfc[2]->cfc_amount : old('')) : old('') }}" >
                        </td>
                        <td>
                            <input class="form-control" type="text" id="cfc_remarks_3" name="cfc_remarks[]"
                                autocomplete="off" value="{{ isset($edit_cfc[2]) ? (!empty(@$edit_cfc[2]->cfc_remarks) ? @$edit_cfc[2]->cfc_remarks : old('')) : old('') }}">
                        </td>
                        <td><a onclick="cfc_row_delete(3)" class="btn-sm btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
                    </tr>
                    <tr style="display: none;" id="fright_row_4">
                    @if (isset($edit_cfc[3]))
                    @if (@$edit_cfc[3]->cfc_amount != "")
                    <script>
                        add_fright_edit(4);
                    </script>                            
                    @endif                            
                    @endif
                    <td>
                        <select class="form-control js-example-basic-single" name="cfc_name[]" id="cfc_name_4">
                            <option value=""></option>
                            @foreach ($customs_freight_account as $key => $value)
                                <option value="{{ @$value->id }}" {{isset($edit_cfc[3])? !empty(@$edit_cfc[3]->cfc_name)? @$edit_cfc[3]->cfc_name==$value->id ? 'selected':'':'':''}} >{{ @$value->account_code }} - {{ @$value->account_name }}</option>
                            @endforeach
                        </select>
                    </td>
                        <td>
                            <select class="form-control js-example-basic-single" name="cfc_credit_account[]" id="cfc_credit_account_4"
                                readonly="true">
                                <option value="none"></option>
                                @foreach ($vendors2 as $key => $value)
                                <option value="{{ @$value->id }}" {{isset($edit_cfc[3])? !empty(@$edit_cfc[3]->cfc_credit_account)? @$edit_cfc[3]->cfc_credit_account==@$value->id ? 'selected':'':'':''}}>{{ @$value->account_code }} - {{ @$value->account_name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input class="form-control" type="number" id="cfc_amount_4" name="cfc_amount[]" step="Any"
                                autocomplete="off" min="0" onchange="cfc_amount_change(4)" value="{{ isset($edit_cfc[3]) ? (!empty(@$edit_cfc[3]->cfc_amount) ? @$edit_cfc[3]->cfc_amount : old('')) : old('') }}" >
                        </td>
                        <td>
                            <input class="form-control" type="text" id="cfc_remarks_4" name="cfc_remarks[]"
                                autocomplete="off" value="{{ isset($edit_cfc[3]) ? (!empty(@$edit_cfc[3]->cfc_remarks) ? @$edit_cfc[3]->cfc_remarks : old('')) : old('') }}">
                        </td>
                        <td><a onclick="cfc_row_delete(4)" class="btn-sm btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
                    </tr>                    
                    <tr style="display: none;" id="fright_row_5">                        
                    @if (isset($edit_cfc[4]))
                    @if (@$edit_cfc[4]->cfc_amount != "")
                    <script>
                        add_fright_edit(5);
                    </script>                            
                    @endif                            
                    @endif
                    <td>
                        <select class="form-control js-example-basic-single" name="cfc_name[]" id="cfc_name_5">
                            <option value=""></option>
                            @foreach ($customs_freight_account as $key => $value)
                                <option value="{{ @$value->id }}" {{isset($edit_cfc[4])? !empty(@$edit_cfc[4]->cfc_name)? @$edit_cfc[4]->cfc_name==$value->id ? 'selected':'':'':''}} >{{ @$value->account_code }} - {{ @$value->account_name }}</option>
                            @endforeach
                        </select>
                    </td>
                        <td>
                            <select class="form-control js-example-basic-single" name="cfc_credit_account[]" id="cfc_credit_account_5"
                                readonly="true">
                                <option value="none"></option>
                                @foreach ($vendors2 as $key => $value)
                                <option value="{{ @$value->id }}" {{isset($edit_cfc[4])? !empty(@$edit_cfc[4]->cfc_credit_account)? @$edit_cfc[4]->cfc_credit_account==@$value->id ? 'selected':'':'':''}}>{{ @$value->account_code }} - {{ @$value->account_name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input class="form-control" type="number" id="cfc_amount_5" name="cfc_amount[]" step="Any"
                                autocomplete="off" min="0" onchange="cfc_amount_change(5)" value="{{ isset($edit_cfc[4]) ? (!empty(@$edit_cfc[4]->cfc_amount) ? @$edit_cfc[3]->cfc_amount : old('')) : old('') }}" >
                        </td>
                        <td>
                            <input class="form-control" type="text" id="cfc_remarks_5" name="cfc_remarks[]"
                                autocomplete="off" value="{{ isset($edit_cfc[4]) ? (!empty(@$edit_cfc[4]->cfc_remarks) ? @$edit_cfc[4]->cfc_remarks : old('')) : old('') }}">
                        </td>
                        <td><a onclick="cfc_row_delete(5)" class="btn-sm btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="row mt-40">
                            <div class="col-lg-12 text-left mb-2">
                                @if(count($paymentAdjustments)>0 || count($returnAdjustments)>0)
                                <b>Adjusted Items</b>
                                    <table class="table table-bordered table-striped" id="br-table" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th style="width:50px;">@lang('#')</th>
                                                <th style="width:100px;">@lang('Receipt Number')</th>
                                                <th style="width:100px;">@lang('Receipt Date')</th>
                                                <th style="width:100px;" class="text-right">Total</th>
                                                <th style="width:100px;" class="text-right">Paid</th>
                                                <th style="width:100px;" class="text-right">Balance</th>
                                                <th style="width:100px;" class="text-right">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                @if(count($paymentAdjustments)>0)
                                        @foreach ($paymentAdjustments as $item)
                                            <tr>
                                                <td>{{ @$loop->iteration }}</td>
                                                <td>{{ @$item->bi_doc_number }}</td>
                                                <td>{{ @$item->bi_doc_date }}</td>
                                                <td class="text-right">{{ @$item->bi_total }}</td>
                                                <td class="text-right">{{ @$item->bi_paid }}</td>
                                                <td class="text-right">{{ @$item->bi_balance }}</td>
                                                <td class="text-right"><a class="btn-sm btn-danger" href="{{url('delete-payment-adjustment/'.$item->id)}}" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
                                            </tr>
                                        @endforeach
                                        @endif
                                @if(count($returnAdjustments)>0)
                                        @foreach ($returnAdjustments as $item)
                                            <tr>
                                                <td>{{ @$loop->iteration }}</td>
                                                <td>{{ @$item->srn_no }}</td>
                                                <td>{{ @$item->doc_date }}</td>
                                                <td class="text-right">{{ @$item->total_amount }}</td>
                                                <td class="text-right">{{ @$item->paid_amount }}</td>
                                                <td class="text-right">{{ @$item->balance_amount }}</td>
                                                <td class="text-right"><a class="btn-sm btn-danger" href="{{url('delete-purchase-return-adjustment/'.$item->id)}}" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
                                            </tr>
                                        @endforeach
                                        @endif
                                        </tbody>
                                    </table>
                                @endif
                            </div>
                        </div>



        <div class="row mt-40">
            <div class="col-lg-8 text-right font-weight-bold">
                Total Amount : {{ @App\SysHelper::com_curr_format(($totalamount + $edit_cfc->sum('cfc_amount')), 2, '.', '') }}
            </div>
            <div class="col-lg-4 text-right">
                <a class="btn btn-danger" data-toggle="modal" data-target="#ModalAdjustment">Adjustment</a>
                <button type="submit" class="btn btn-warning" id="btnSubmit">
                    <span class="ti-check"></span>
                        @lang('Update Purchase Invoice')

                </button>
            </div>
        </div>

        {{ Form::close() }}

    </div>
    </div>

    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>



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
                
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'purchase-invoice-update-currency', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Currancy From</label>
                                <select class="form-control" name="from_currency_id" required>
                                    @foreach ($currency as $value)
                                        @if($edit_pi->currency == $value->id)
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
                    <input type="hidden" name="cur_pi_id" value="{{ @$edit_pi->id }}"/>
                    <input type="hidden" name="cur_pi_doc_no" value="{{ @$edit_pi->doc_number }}"/>                    
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Change</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!-- Modal Change Currancy-->

    
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
                
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'purchase-invoice-update-discount', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
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
                    <input type="hidden" name="discount_amount_pi_id" value="{{ @$edit_pi->id }}"/>                    
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Split Discount</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    {{-- ModalDiscount --}}
    {{-- ModalFreight --}}
    <div class="modal fade" id="modalFreight" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Freight</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'purchase-invoice-update-freight', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Freight Amount</label>
                                <input type="text" class="form-control" id="freight_amount" name="freight_amount" required />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="freight_amount_pi_id" value="{{ @$edit_pi->id }}"/>                    
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Split freight</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    {{-- ModalFreight --}}
    {{-- ModalCustom --}}
    <div class="modal fade" id="modalCustom" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Custom Charges</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'purchase-invoice-update-custom', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Custom Charges Amount</label>
                                <input type="text" class="form-control" id="custom_amount" name="custom_amount" required />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="custom_amount_pi_id" value="{{ @$edit_pi->id }}"/>                    
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Split Custom Charges</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    {{-- ModalCustom --}}

    {{-- popup --}}
    <form id="po">
        <div class="modal fade admin-query" id="grn_pending_popup_win" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 80%;">
                <div class="modal-content">
                    <div class="modal-header m-0 p-3">
                        <h4 class="modal-title">Purchase Invoice Pending List</h4>
                        <button class="close" data-dismiss="modal" type="button">
                            ×
                        </button>
                    </div>
                    <div class="modal-body m-0 p-3">
                        <input type="hidden" id="hd_pending_grn_id" />
                        <div class="container-fluid">
                            {{-- <div class="row">
                            <div class="col-lg-4 mb-20">
                                <div class="input-effect">
                                    <label class="dynamicslbl">  @lang('Select All') <span>*</span> </label>
                                    <input class="dynamicstxt primary-input form-control" type="text" id="bi_new_reference" name="bi_new_reference" value="" >
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_1 red_alert"></span>                                    
                                </div>
                            </div>
                            <div class="col-lg-4 mb-20">
                                <div class="input-effect">
                                    <label class="dynamicslbl">  @lang('Product Code') <span>*</span> </label>
                                    <input class="dynamicstxt primary-input form-control" type="text" id="bi_amount_to_adjust" name="bi_amount_to_adjust" value="" >
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_2 red_alert"></span>                                    
                                </div>
                            </div>
                            <div class="col-lg-4 mb-20">
                                <div class="input-effect">
                                    <label class="dynamicslbl">  @lang('Contains') <span>*</span> </label>
                                    <input class="dynamicstxt primary-input form-control" type="text" id="bi_contains" name="bi_contains" value="" >
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_6 red_alert"></span>                                    
                                </div>
                            </div>
                        </div> --}}

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="equipment comon-status row mt-40 d-block">
                                        <table id="table_id" class="display school-table" cellspacing="0"
                                            width="100%">
                                            <thead>
                                                <tr>
                                                    <th>@lang('#') </th>
                                                    <th>@lang('Part No')</th>
                                                    <th>@lang('GRN Qty')</th>
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

                                        <button class="btn btn-primary bg-success" type="button" id="addGRNPendingItems">
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
                                    <input
                                        class="primary-input form-control {{ $errors->has('shipping_name') ? 'is-invalid' : ' ' }}"
                                        type="text" id="shipping_name_add" name="shipping_name"
                                        value="{{ isset($editData) ? @$editData->shipping_name : old('shipping_name') }}">
                                    <label> @lang('Shipping Name') <span>*</span> </label>
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_1 red_alert"></span>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-20">
                                <div class="input-effect">
                                    <input
                                        class="primary-input form-control {{ $errors->has('contact_name') ? 'is-invalid' : ' ' }}"
                                        type="text" id="contact_name_add" name="contact_name"
                                        value="{{ isset($editData) ? @$editData->contact_name : old('contact_name') }}">
                                    <label> @lang('Contact Name') <span>*</span> </label>
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_2 red_alert"></span>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-20">
                                <div class="input-effect">
                                    <input
                                        class="primary-input form-control{{ $errors->has('contact_no') ? ' is-invalid' : '' }}"
                                        type="number" id="contact_no_add" name="contact_no"
                                        value="{{ isset($editData) ? @$editData->contact_no : old('contact_no') }}">
                                    <label> @lang('Contact No') <span>*</span> </label>
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_3 red_alert"></span>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-20">
                                <div class="input-effect">
                                    <input
                                        class="primary-input form-control{{ $errors->has('address1') ? ' is-invalid' : '' }}"
                                        type="text" id="address1_add" name="address1"
                                        value="{{ isset($editData) ? @$editData->address1 : old('address1') }}">
                                    <label> @lang('Address 1') <span>*</span> </label>
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_4 red_alert"></span>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-20">
                                <div class="input-effect">
                                    <input
                                        class="primary-input form-control{{ $errors->has('address2') ? ' is-invalid' : '' }}"
                                        type="text" id="address2_add" name="address2"
                                        value="{{ isset($editData) ? @$editData->address2 : old('address2') }}">
                                    <label> @lang('Address 2') <span>*</span> </label>
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_5 red_alert"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="col-lg-12 text-center">
                                    <div class="mt-40 d-flex justify-content-between">
                                        <button class="btn btn-primary tr-bg" data-dismiss="modal" type="button"
                                            id="btn_close2">
                                            @lang('lang.cancel')
                                        </button>
                                        <input class="btn btn-primary fix-gr-bg" type="submit" value="save"
                                            onclick="return validateAttachForm()">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- -------------------------------------------------------- --}}

    
{{-- attachment start--}}
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
<script>
    function add_attachment(){
        $("#loading_bg").css("display", "block");

        if($('#att_file').val()==""){ $('#att_file').focus(); $("#loading_bg").css("display", "none"); return false; }

        var action = "{{ URL::to('add-purchase-invoice-attachment') }}";
        
        var formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');  // Append CSRF token
        formData.append('doc_id', $('#pi_id').val());
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
        $('#att_cust_name').text($('#vendors :selected').text() + " " + $('#doc_number').val());

        var action = "{{ URL::to('view-purchase-invoice-attachment') }}";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                doc_id : $('#pi_id').val(),
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
        var action = "{{ URL::to('delete-purchase-invoice-attachment') }}";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                id : id,
                doc_id : $('#pi_id').val(),
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
    </script>

{{-- attachment end--}}

    <script>
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
                    if (response['data'] == "ERROR") {
                        alert("Error found in something!!");
                    } else {
                        if (response['data'] != null) {
                            len = response['data'].length;
                        }
                        if (len > 0) {

                            //$('#shipping_name').find('option').not(':first').remove();

                            for (var i = 0; i < len; i++) {
                                var id = response['data'][i].id;
                                var name = response['data'][i].shipping_name;
                                var option = "<option value='" + id + "'>" + name + "</option>";
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

        function cfc_amount_change(id) {
            var amt = $("#cfc_amount_" + id).val();
            $("#cfc_cal_amount_" + id).val(amt);
        }

        function popup_grn_pending(id) {
            $("#loading_bg").css("display", "block");
            $("#hd_pending_grn_id").val(id);
            $("#grn_id").val(id);
            document.getElementById('addGRNPending').click();
            $("#loading_bg").css("display", "none");
        }
    </script>
    
    <script>
                
        $(document).on("change", "#vendors", function () {
            var id = $("#vendors").val();
            get_vat(id);
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
                            $("#loading_bg").css("display", "none");     }
                        }
                });
        }

        function get_po_list(id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('goods-receipt-note-for-pi') }}";
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
                                        "<input type='radio' onclick='popup_grn_pending(" + id +
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


    </script>
<script>
$(document).ready(function () {
    function initAccountSelect2(selector) {
        $(selector).select2({
            ajax: {
                url: '{{ route("autocomplete.get_supp_account_list_ajax") }}',
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

    <div class="modal fade" id="ModalAdjustment" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Unadjusted List</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'purchase-invoice-update-adjustment', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table" id="adjustment_table" style="border: solid 1px #e3e6f0; width:auto; width:100%;">
                                <thead>
                                    <tr>
                                        <th class="border">Doc Date</th>
                                        <th class="border">Payment No</th>
                                        <th class="border">Account Name</th>
                                        <th class="border text-right">Amount</th>
                                        <th class="border text-right">Adjusement</th>
                                    </tr>
                                </thead>
                                <tbody>


                                    @if(count($list_of_unadjusted) > 0)
                                    @foreach ($list_of_unadjusted as $p)
                                    <tr>
                                         <td class="border">{{ date('d/m/Y', strtotime(@$p->doc_date)) }}</td>
                                        <td class="border"><a href="{{url('get-url-payment/' . @$p->doc_number)}}" target="_blank">{{ @$p->doc_number }}</a></td>
                                        <td class="border">{{ @$p->account_name }}</td>
                                        <td class="border text-right">{{ @$p->amount-@$p->adj_amount }}</td>
                                        <td class="border text-right"><input type="text" name="set_amt[]" id="set_amt_{{ @$p->doc_number }}" class="form-control text-right" id="" name="" value="{{ @$p->adj_amount }}" onclick="set_adjust('{{ @$p->amount-@$p->adj_amount }}','{{ @$p->doc_number }}')" />
                                            <input type="hidden" name="paymentno[]" value="{{ @$p->doc_number }}"/>
                                            <input type="hidden" name="set_amt_act[]" value="{{ @$p->amount-@$p->adj_amount }}"/>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                    @if(count($list_of_unadjusted_pdc) > 0)
                                    @foreach ($list_of_unadjusted_pdc as $p)
                                    <tr>
                                         <td class="border">{{ date('d/m/Y', strtotime(@$p->doc_date)) }}</td>
                                        <td class="border"><a href="{{url('get-url-payment/' . @$p->doc_number)}}" target="_blank">{{ @$p->doc_number }}</a></td>
                                        <td class="border">{{ @$p->account_name }}</td>
                                        <td class="border text-right">{{ @$p->amount-@$p->adj_amount }}</td>
                                        <td class="border text-right"><input type="text" name="set_amt[]" id="set_amt_{{ @$p->doc_number }}" class="form-control text-right" id="" name="" value="{{ @$p->adj_amount }}" onclick="set_adjust('{{ @$p->amount-@$p->adj_amount }}','{{ @$p->doc_number }}')" />
                                            <input type="hidden" name="paymentno[]" value="{{ @$p->doc_number }}"/>
                                            <input type="hidden" name="set_amt_act[]" value="{{ @$p->amount-@$p->adj_amount }}"/>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="adj_sup_id" name="adj_sup_id" value="{{ $edit_pi->vendors }}"/>
                    <input type="hidden" id="adj_piv_id" name="adj_piv_id" value="{{ $edit_pi->id }}"/>
                    <input type="hidden" id="adj_piv_no" name="adj_piv_no" value="{{ $edit_pi->doc_number }}"/>
                    <input type="hidden" id="adj_piv_date" name="adj_piv_date" value="{{ $edit_pi->pi_date }}"/>
                    <input type="hidden" id="adj_piv_amount" name="adj_piv_amount" value="{{ $adjusted_amt }}"/>
                    <input type="hidden" id="adj_piv_amount_actual" name="adj_piv_amount_actual" value="{{ $adjusted_amt_actual }}"/>
                    <input type="hidden" id="adj_piv_amount_adjusted" name="adj_piv_amount_adjusted" value="0"/>
                    <button class="btn btn-success" type="submit" >Adjust</button>
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <script>
function set_adjust(amt,id) {
    let maxAdjustable = parseFloat($("input[name='adj_piv_amount']").val());
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
    $("input[name='adj_piv_amount_adjusted']").val(currentAdjusted + adjustAmount);
}
</script>

@endsection

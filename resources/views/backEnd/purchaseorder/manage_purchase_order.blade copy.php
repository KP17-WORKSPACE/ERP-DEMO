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
                <h2 class="page-heading m-0">Purchase Order</h2>
                <span class="page-label">Home - Purchase Order</span>
            </div>
            <div>
                <a data-modal-size="modal-md" data-target="#attachment_popup_win" data-toggle="modal"
                    class="btn btn-primary" onclick="view_attachment()"><i class="fa fa-plus"></i> Attachment</a>
                <a href="{{ url('purchase-order/create') }}" type="button" class="btn btn-info"><i
                        class="fa fa-plus"></i>New</a>
                <a href="{{ url('purchase-order') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> List</a>
            </div>
        </div>
        <div class="card p-4 mb-2">
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'purchase-order-store', 'method' => 'POST', 'id' => 'tender-create-form']) }}
            <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
            <input type="hidden" name="id" value="{{ isset($edit) ? $edit->id : '' }}">

            <div class="row">
                <div class="col-lg-4 mb-2">
                    <div class="input-effect">
                        <label class="dynamicslbl">@lang('Vendor Name')</label>
                        <select class="form-control js-account-select" name="vendors" id="vendors" required>
                            <option value=""></option>
                            {{-- @foreach ($vendors as $value)
                            <option value="{{ @$value->id }}" {{ isset($edit) ? (!empty($edit->vendor_id) ?
                                (@$edit->vendor_id == @$value->id ? 'selected' : '') : '') : '' }}>
                                {{ @$value->account_name }}
                            </option>
                            @endforeach --}}
                        </select>
                    </div>
                </div>
                <div class="col-lg-2 mb-2">
                    <div class="input-effect">
                        <label class="dynamicslbl">@lang('PO') @lang('Number')<span>*</span></label>
                        <input class="form-control" type="text" name="doc_number" autocomplete="off" id="doc_number"
                            value="{{ isset($edit) ? (!empty(@$edit->title) ? @$edit->title : old('doc_number')) : @App\SysHelper::get_new_code('sys_purchase_order','PO' ,'doc_number') }}"
                            readonly>
                    </div>
                </div>
                <div class="col-lg-2 mb-2">
                    <div class="input-effect">
                        <label class="dynamicslbl">@lang('PO') @lang('lang.date')</label>
                        @php
                            $value = date('Y-m-d');
                            if (isset($edit) && !empty($edit->date)) {
                                @$value = date('Y-m-d', strtotime(@$edit->date));
                            } else {
                                if (!empty(old('po_date'))) {
                                    @$value = old('po_date');
                                } else {
                                    @$value = date('Y-m-d');
                                }
                            }
                        @endphp
                        <input class="form-control" id="po_date" type="date" name="po_date" value="{{ @$value }}">
                    </div>
                </div>
                <div class="col-lg-2 mb-2">
                    <div class="input-effect">
                        <label class="dynamicslbl">@lang('Currency')</label>
                        <select class="form-control" name="currency" id="currency">
                            @foreach ($currency as $value)
                                <option value="{{ @$value->id }}" @if($company->currency_id == $value->id) selected @endif>
                                    {{ @$value->code }}
                                </option>
                            @endforeach
                        </select>
                        @if ($errors->has('currency'))
                            <span class="invalid-feedback invalid-select" role="alert">
                                <strong>{{ $errors->first('currency') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-lg-2 mb-2">
                    <div class="input-effect">
                        <label class="dynamicslbl">@lang('Created') @lang('By')<span>*</span></label>
                        <input class="form-control" type="text" name="createdby" autocomplete="off" id="createdby" readonly
                            value="{{ isset($edit) ? (!empty(@$edit->number) ? @$edit->number : old('createdby')) : Auth::user()->full_name }}">

                        @if ($errors->has('createdby'))
                            <span class="invalid-feedback"
                                role="alert"><strong>{{ $errors->first('createdby') }}</strong></span>
                        @endif
                    </div>
                </div>
                <div class="col-lg-3 mb-2"></div>
                <div class="col-lg-3 mb-2" style="display: none;">
                    <div class="input-effect">
                        <label class="dynamicslbl">@lang('Bill to Name') <span></span></label>
                        <input type="text" class="form-control" value="{{ @$company->company_name }}">
                        <span class="focus-border textarea"></span>
                    </div>
                </div>
                <div class="col-lg-3 mb-2" style="display: none;">
                    <div class="input-effect">
                        <label class="dynamicslbl">@lang('Address') <span></span></label>
                        <input type="text" class="form-control" value="{{ @$company->company_address }}">
                        <span class="focus-border textarea"></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="extra-tab" data-toggle="tab" href="#extra" role="tab"
                                aria-controls="extra" aria-selected="true">Extra Fields</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="shipping-tab" data-toggle="tab" href="#shipping" role="tab"
                                aria-controls="shipping" aria-selected="true">Shipping Details</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="vat-tab" data-toggle="tab" href="#vat" role="tab" aria-controls="vat"
                                aria-selected="false">VAT Details</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane active" id="extra" role="tabpanel" aria-labelledby="extra-tab">
                            <div class="row mt-2">
                                <div class="col-lg-2 mb-2">
                                    <div class="input-effect">
                                        <label class="dynamicslbl">@lang('Delivery Date')</label>
                                        @php
                                            $value = date('Y-m-d');
                                            if (isset($edit) && !empty($edit->date)) {
                                                @$value = date('Y-m-d', strtotime(@$edit->date));
                                            } else {
                                                if (!empty(old('delivery_date'))) {
                                                    @$value = old('delivery_date');
                                                } else {
                                                    @$value = date('Y-m-d');
                                                }
                                            }
                                        @endphp
                                        <input class="form-control" id="delivery_date" type="date" name="delivery_date"
                                            value="{{ @$value }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="dynamicslbl">@lang('Payment Terms')*</label>
                                        <select class="form-control" required name="payment_terms" id="payment_terms"
                                            onchange="fn_payment_terms()">
                                            <option value=""></option>
                                            @foreach ($paymentterms as $value)
                                                <option value="{{ @$value->id }}" {{ isset($edit) ? (!empty(@$edit->payment_terms) ? (@$edit->payment_terms == @$value->id ? 'selected' : '') : '') : '' }}>
                                                    {{ @$value->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div id="div_payment_terms" style="display: none; padding-top: px;">
                                        <div class="input-effect">
                                            <label class="txtlbl">@lang('Other Payment Terms')<span>*</span></label>
                                            <input class="txtbx primary-input form-control" type="text"
                                                name="payment_terms2" autocomplete="off" id="payment_terms2"
                                                value="{{ @$edit->payment_terms2 }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="dynamicslbl">@lang('Customer Reference')*</label>
                                        <input class="form-control" id="narration" type="text" name="narration" value=""
                                            required>
                                    </div>
                                </div>

                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="dynamicslbl">@lang('Salesman Name')*</label>
                                        <select class="form-control js-example-basic-single" required name="sales_person"
                                            id="sales_person">
                                            <option value=""></option>
                                            @foreach ($salesman as $value)
                                                <option value="{{ @$value->user_id }}">{{ @$value->full_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="dynamicslbl">@lang('Contact Person Name')*</label>
                                        <input class="form-control" id="contact_person_name" type="text"
                                            name="contact_person_name" value="" required>
                                    </div>
                                </div>

                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="dynamicslbl">@lang('Contact Person Email')*</label>
                                        <input class="form-control" id="contact_person_email" type="text"
                                            name="contact_person_email" value="" required>
                                    </div>
                                </div>

                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="dynamicslbl">@lang('Contact Person Telephone')*</label>
                                        <input class="form-control" id="contact_person_telephone" type="text"
                                            name="contact_person_telephone" value="" required>
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="dynamicslbl">@lang('Internal Transfer')*</label>
                                        <select class="form-control" id="internal_transfer" name="internal_transfer"
                                            required>
                                            <option value="">Select</option>
                                            <option value="1">Yes</option>
                                            <option value="2">No</option>
                                        </select>
                                    </div>
                                </div>


                                <div class="col-lg-2 mb-2" id="div_deal_id" style="display: none;">
                                    <div class="input-effect">
                                        <label class="dynamicslbl">@lang('Deal ID')*</label>
                                        <input class="form-control" id="deal_id" type="text" name="deal_id"
                                            value="Without Deal" required>
                                    </div>
                                </div>


                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="dynamicslbl">@lang('Create Deal')</label>
                                        <select class="form-control" name="create_deal" id="create_deal" required
                                            onchange="create_deal_change()">
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                </div>
                                <script>
                                    function create_deal_change() {
                                        if ($('#create_deal').val() == 1) {
                                            $('#div_deal_id').css('display', 'none');

                                        } else {
                                            $('#div_deal_id').css('display', '');
                                        }
                                    }

                                    function create_deal_change() {
                                        if ($('#internal_transfer').val() == 1) {
                                            $('#div_deal_id').css('display', '');
                                            $('#create_deal').val('0');
                                            $('#create_deal').change();
                                            $('#deal_id').val('');
                                            $('#deal_id').prop('required', true);
                                        }
                                    }
                                </script>
                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="dynamicslbl">@lang('Create Goods Receipt Note')</label>
                                        <select class="form-control" name="create_grn" id="create_grn"
                                            onchange="fn_create_grn_pi()">
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="dynamicslbl">@lang('Create Purchase Invoice')</label>
                                        <select class="form-control" name="create_pi" id="create_pi"
                                            onchange="fn_create_grn_pi()">
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                </div>
                                <script>
                                    function fn_create_grn_pi() {
                                        var create_grn = $("#create_grn").val();
                                        var create_pi = $("#create_pi").val();

                                        if (create_grn == "1" || create_pi == "1") {
                                            $(".create_grn_pi").show();
                                        } else {
                                            $(".create_grn_pi").hide();
                                        }
                                    }

                                    $(document).ready(function () {
                                        fn_create_grn_pi(); // Ensure correct initial state
                                        $("#create_grn, #create_pi").change(fn_create_grn_pi);
                                    });
                                </script>


                                <div class="col-lg-3 mb-2 create_grn_pi" style="display: none;">
                                    <div class="input-effect">
                                        <label class="dynamicslbl">@lang('Bill Number')</label>
                                        <input class="form-control" id="bill_number" type="text" name="bill_number">
                                    </div>
                                </div>
                                <div class="col-lg-2 mb-2 create_grn_pi" style="display: none;">
                                    <div class="input-effect">
                                        <label class="dynamicslbl">@lang('Bill Date')</label>
                                        <input class="form-control" id="bill_date" type="date" name="bill_date">
                                    </div>
                                </div>
                                <div class="col-lg-2 mb-2 create_grn_pi" style="display: none;">
                                    <div class="input-effect">
                                        <label class="dynamicslbl">@lang('AWB No')</label>
                                        <input class="form-control" id="awbno" type="text" name="awbno">
                                    </div>
                                </div>
                                <div class="col-lg-2 mb-2 create_grn_pi" style="display: none;">
                                    <div class="input-effect">
                                        <label class="dynamicslbl">@lang('BOE No')</label>
                                        <input class="form-control" id="boeno" type="text" name="boeno">
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-2 create_grn_pi" style="display: none;">
                                    <div class="input-effect">
                                        <label class="dynamicslbl">@lang('Narration')</label>
                                        <input class="form-control" id="reference" type="text" name="reference">
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="tab-pane" id="shipping" role="tabpanel" aria-labelledby="shipping-tab">
                            <div class="row mt-2">
                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="dynamicslbl">@lang('Company Name') <span></span></label>
                                        <select class="form-control js-example-basic-single" name="shipping_supplier"
                                            id="shipping_supplier" required>
                                            <option value=""></option>
                                            @foreach ($customer as $value)
                                                @php $s = @App\SysHelper::internal_transfer_customer_id(@$value->id, session('logged_session_data.company_id')); @endphp
                                                <option value="{{ @$value->id }}" {{ $s }}>{{ @$value->account_code }} -
                                                    {{ @$value->account_name }}</option>
                                            @endforeach
                                        </select>
                                        <script>
                                            $(function () { $("#shipping_supplier").change(); });
                                        </script>
                                    </div>
                                </div>

                                <div class="col-lg-2 mb-2">
                                    <div class="input-effect">
                                        <label class="dynamicslbl">@lang('Contact Name') <span></span></label>
                                        <input type="text" class="form-control" name="shipping_name" id="shipping_name"
                                            value="{{ session('logged_session_data.full_name') }}" />
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="dynamicslbl">@lang('Shipping Address') <span></span></label>
                                        <textarea type="text" class="form-control" cols="0" rows="4"
                                            name="shipping_address_1" id="shipping_address_1"></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-2 mb-2">
                                    <div class="input-effect">
                                        <label class="dynamicslbl">@lang('Email') <span></span></label>
                                        <input type="text" class="form-control" name="shipping_email" id="shipping_email"
                                            value="{{ session('logged_session_data.email') }}" />
                                    </div>
                                </div>
                                <div class="col-lg-2 mb-2">
                                    <div class="input-effect">
                                        <label class="dynamicslbl">@lang('Contact No') <span></span></label>
                                        <input type="text" class="form-control" name="shipping_contact_no"
                                            id="shipping_contact_no" value="{{ session('logged_session_data.mobile') }}" />
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
                                                <option value="{{ @$value->id }}" {{ isset($edit) ? (!empty(@$edit->supplier_type) ? (@$edit->supplier_type == @$value->id ? 'selected' : '') : '') : '' }}>
                                                    {{ @$value->title }}
                                                </option>
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
                                                <option value="{{ @$value->id }}" {{ isset($edit) ? (!empty(@$edit->supplier_type) ? (@$edit->supplier_type == @$value->id ? 'selected' : '') : '') : '' }}>
                                                    {{ @$value->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="dynamicslbl">@lang('Supplier Country') <span></span></label>
                                        <select class="form-control" name="supplier_country" id="country" required>
                                            <option data-display="" value=""></option>
                                            @foreach ($countries as $key => $value)
                                                                                <option value="{{ @$value->id }}" <?php        try {?> @if (isset($edit)) @if (@$edit->supplier_country == $value->id) selected @endif @endif <?php        } catch (\Throwable $th) {
                                                } ?>>{{ @$value->name }} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12 mt-2">
                    <table class="table table-bordered table-striped" id="table_id" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th style="width:150px;">@lang('Part No')</th>
                                <th style="width:150px;">@lang('Description')</th>
                                <th style="width:100px;">@lang('Tax')</th>
                                <th style="width:100px;">@lang('Qty')</th>
                                <th style="width:120px;">@lang('Unit Price')</th>
                                <th style="width:120px;">@lang('Value')</th>
                                <th style="width:100px;">@lang('Discount')</th>
                                <th style="width:100px;">@lang('Freight')</th>
                                <th style="width:100px;">@lang('Custom Charges')</th>
                                <th style="width:130px;">@lang('Taxable Amount')</th>
                                <th style="width:130px;">@lang('VAT Amount')</th>
                                <th style="width:130px;">@lang('Total')</th>
                                <th style="width:130px;">@lang('Serial No')</th>
                                <th style="width:20px;">
                                    <a class="btn-sm btn-danger float-right pt-0 pb-0" data-toggle="modal"
                                        data-target="#ModalExcelQuote" data-backdrop="static"
                                        data-keyboard="false">Import</a>
                            </tr>
                            <tr>
                                <td><input type="checkbox" checked hidden>
                                    <select class="form-control js-product-select" name="part_number[]"
                                        id="part_number_new">
                                        <option value="none"></option>
                                        {{-- @foreach ($items as $key => $value)
                                        <option value="{{ @$value->id }}">{{ @$value->part_number }}</option>
                                        @endforeach --}}
                                    </select>
                                </td>
                                <td>
                                    <input class="form-control" type="text" id="description_new" name="description[]"
                                        autocomplete="off">
                                </td>
                                <td>
                                    <input type="number" class="form-control" name="tax[]" id="tax"
                                        onchange="calc_change_new()">
                                </td>
                                <td>
                                    <input class="form-control" type="number" id="qty" name="qty[]" autocomplete="off"
                                        min="0" onchange="calc_change_new()">
                                </td>
                                <td>
                                    <input class="form-control" type="number" id="unitprice" name="unitprice[]"
                                        autocomplete="off" min="0" onchange="calc_change_new()">
                                    <script>
                                        $("#unitprice").on('keyup', function (e) {
                                            if (e.key === 'Enter' || e.keyCode === 13) {
                                                calc_change_new();
                                                if ($('#btn_add_row').css('display') == 'none') {
                                                    $('#update_add_row').click();
                                                }
                                                if ($('#update_add_row').css('display') == 'none') {
                                                    $('#btn_add_row').click();
                                                }
                                            }
                                        });
                                    </script>
                                </td>
                                <td>
                                    <input class="form-control" type="number" id="value" name="value[]" autocomplete="off"
                                        min="0" readonly>
                                </td>
                                <td>
                                    <input class="form-control" type="number" id="discount" name="discount[]"
                                        autocomplete="off" min="0" value="0" onchange="calc_change_new()">
                                </td>
                                <td>
                                    <input class="form-control" type="number" id="fright" name="fright[]" autocomplete="off"
                                        min="0" value="0" onchange="calc_change_new()">
                                </td>
                                <td>
                                    <input class="form-control" type="number" id="customcharges" name="customcharges[]"
                                        autocomplete="off" min="0" value="0" onchange="calc_change_new()">
                                </td>
                                <td>
                                    <input class="form-control" type="number" id="taxableamount" name="taxableamount[]"
                                        autocomplete="off" min="0" readonly>
                                </td>
                                <td>
                                    <input class="form-control" type="number" id="vatamount" name="vatamount[]"
                                        autocomplete="off" min="0" readonly>
                                </td>
                                <td>
                                    <input class="form-control" type="number" id="totalamount" name="totalamount[]"
                                        autocomplete="off" min="0" readonly>
                                </td>
                                <td>
                                    <input class="form-control" type="text" id="serialno" name="serialno[]"
                                        autocomplete="off" onclick="srlno_add()">
                                </td>
                                <td>
                                    <input type="hidden" id="cart_item_id" />
                                    <input type="hidden" id="deal_ref_id" />
                                    <a id="btn_add_row" onclick="return add_rows()" class="btn btn-primary"><i
                                            class="fa fa-plus" aria-hidden="true"></i></a>
                                    <a id="update_add_row" style="display: none;" onclick="return row_update()"
                                        class="btn btn-warning">Update</a>
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
                                    fright = (fright === '') ? '0' : fright;
                                    customcharges = (customcharges === '') ? '0' : customcharges;
                                    var fin_taxableamount = ((unitprice * qty) + Number(customcharges) + Number(fright) - Number(discount));
                                    $('#taxableamount').val(fin_taxableamount.toFixed(@json(session('logged_session_data.decimal_point'))));

                                    var fin_vatamount = ((unitprice * qty) + Number(customcharges) + Number(fright) - Number(discount)) * ((Number(net_vat)) / 100);
                                    var vatamount = $('#vatamount').val(fin_vatamount.toFixed(@json(session('logged_session_data.decimal_point'))));

                                    $('#totalamount').val((Number(fin_taxableamount) + Number(fin_vatamount)).toFixed(@json(session('logged_session_data.decimal_point'))));

                                }
                                function add_rows() {

                                    if ($("#part_number_new").val() == "none") { $("#part_number_new").focus(); return false; }
                                    if ($("#tax").val() == "") { $("#tax").focus(); return false; }
                                    if ($("#qty").val() == "") { $("#qty").focus(); return false; }
                                    if ($("#unitprice").val() == "") { $("#unitprice").focus(); return false; }
                                    if ($("#taxableamount").val() == "") { $("#taxableamount").focus(); return false; }
                                    if ($("#vatamount").val() == "") { $("#vatamount").focus(); return false; }

                                    $("#loading_bg").css("display", "block");
                                    var action = "{{ URL::to('add-purchase-order-items-cart') }}";
                                    $.ajax({
                                        url: action,
                                        type: "POST",
                                        data: {
                                            _token: '{{ csrf_token() }}',
                                            part_number: $("#part_number_new").val(),
                                            description: $("#description_new").val(),
                                            tax: $("#tax").val(),
                                            qty: $("#qty").val(),
                                            unitprice: $("#unitprice").val(),
                                            value: $("#value").val(),
                                            discount: $("#discount").val(),
                                            fright: $("#fright").val(),
                                            customcharges: $("#customcharges").val(),
                                            taxableamount: $("#taxableamount").val(),
                                            vatamount: $("#vatamount").val(),
                                            serialno: $('#serialno').val(),
                                        },
                                        cache: false,
                                        success: function (dataResult) {
                                            var dataResult = JSON.parse(dataResult);
                                            var len = 0;
                                            var getSelectedRows = "";
                                            if (dataResult['data'] != null) {
                                                len = dataResult['data'].length;
                                            }
                                            if (len > 0) {

                                                var qty_total = 0; var value_total = 0; var discount_total = 0; var fright_total = 0; var customcharges_total = 0; var taxableamount_total = 0; var vatamount_total = 0; var taxableamount_total1 = 0; var vatamount_total1 = 0; var amount_total = 0;

                                                for (var i = 0; i < len; i++) {


                                                    getSelectedRows += "<tr>\
                                                            <td>"+ dataResult['data'][i].sort_id + "</td>\
                                                            <td>"+ dataResult['data'][i].partno + " <input type='hidden' id='partno_" + dataResult['data'][i].id + "' value='" + dataResult['data'][i].partno + "' /><input type='hidden' id='pid_" + dataResult['data'][i].id + "' value='" + dataResult['data'][i].part_number + "' /></td>\
                                                            <td>"+ dataResult['data'][i].description + "<input type='hidden' id='description_" + dataResult['data'][i].id + "' value='" + dataResult['data'][i].description + "' /></td>\
                                                            <td>"+ dataResult['data'][i].tax + " <input type='hidden' id='tax_" + dataResult['data'][i].id + "' value='" + dataResult['data'][i].tax + "' /></td>\
                                                            <td class='text-center'>"+ dataResult['data'][i].qty + " <input type='hidden' id='qty_" + dataResult['data'][i].id + "' value='" + dataResult['data'][i].qty + "' /></td>\
                                                            <td class='text-right'>"+ dataResult['data'][i].unitprice + " <input type='hidden' id='unitprice_" + dataResult['data'][i].id + "' value='" + dataResult['data'][i].unitprice + "' /></td>\
                                                            <td class='text-right'>"+ dataResult['data'][i].value + " <input type='hidden' id='value_" + dataResult['data'][i].id + "' value='" + dataResult['data'][i].value + "' /></td>\
                                                            <td class='text-right'>"+ dataResult['data'][i].discount + " <input type='hidden' id='discount_" + dataResult['data'][i].id + "' value='" + dataResult['data'][i].discount + "' /></td>\
                                                            <td class='text-right'>"+ dataResult['data'][i].fright + " <input type='hidden' id='fright_" + dataResult['data'][i].id + "' value='" + dataResult['data'][i].fright + "' /></td>\
                                                            <td class='text-right'>"+ dataResult['data'][i].customcharges + " <input type='hidden' id='customcharges_" + dataResult['data'][i].id + "' value='" + dataResult['data'][i].customcharges + "' /></td>\
                                                            <td class='text-right'>"+ dataResult['data'][i].taxableamount + " <input type='hidden' id='taxableamount_" + dataResult['data'][i].id + "' value='" + dataResult['data'][i].taxableamount + "' /></td>\
                                                            <td class='text-right'>"+ dataResult['data'][i].vatamount + " <input type='hidden' id='vatamount_" + dataResult['data'][i].id + "' value='" + dataResult['data'][i].vatamount + "' /></td>\
                                                            <td class='text-right'>"+ (Number(dataResult['data'][i].taxableamount) + Number(dataResult['data'][i].vatamount)) + " <input type='hidden' id='totalamount_" + dataResult['data'][i].id + "' value='" + (Number(dataResult['data'][i].taxableamount) + Number(dataResult['data'][i].vatamount)) + "' /></td>\
                                                            <td class='text-right'>"+ dataResult['data'][i].serialno + " <input type='hidden' id='serialno_" + dataResult['data'][i].id + "' value='" + dataResult['data'][i].serialno + "' /></td>\
                                                            <td>\
                                                                <input type='hidden' id='cart_item_id_"+ dataResult['data'][i].id + "' value='" + dataResult['data'][i].id + "' />\
                                                                <input type='hidden' id='deal_ref_id_"+ dataResult['data'][i].id + "' value='" + dataResult['data'][i].refid + "' />\
                                                                <a onclick='row_edit("+ dataResult['data'][i].id + ")' class='btn-sm btn-primary'><i class='fa fa-edit' aria-hidden='true'></i></a>\
                                                                <a onclick='row_delete("+ dataResult['data'][i].id + ")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a>\
                                                            </td>\
                                                            </tr>";

                                                    qty_total += Number(dataResult['data'][i].qty);
                                                    value_total += Number(dataResult['data'][i].value);
                                                    discount_total += Number(dataResult['data'][i].discount);
                                                    fright_total += Number(dataResult['data'][i].fright);
                                                    customcharges_total += Number(dataResult['data'][i].customcharges);
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
                                                $("#fright_total").text(fright_total);
                                                $("#customcharges_total").text(customcharges_total);
                                                $("#taxableamount_total").text(taxableamount_total);
                                                $("#vatamount_total").text(vatamount_total);
                                                $("#amount_total").text(amount_total);

                                                $('#po-table tbody').empty();
                                                $("#po-table tbody").append(getSelectedRows);
                                                row_clear();
                                            }
                                            else {

                                            }
                                        }
                                    });
                                    $("#loading_bg").css("display", "none");
                                }
                                function row_edit(id) {
                                    $('#btn_add_row').css("display", 'none');
                                    $('#update_add_row').css("display", 'block');

                                    var partno = $('#partno_' + id).val();
                                    var pid = $('#pid_' + id).val();
                                    //alert(partno);
                                    //alert(pid);
                                    const targetSelect1 = $('#part_number_new');
                                    const option = new Option(partno, pid, true, true);
                                    targetSelect1.append(option).trigger('change');
                                    //$('#part_number_new').addClass('js-example-basic-single');
                                    $('#description_new').val($('#description_' + id).val());
                                    $('#tax').val($('#tax_' + id).val());
                                    $('#qty').val($('#qty_' + id).val());
                                    $('#unitprice').val($('#unitprice_' + id).val());
                                    $('#value').val($('#value_' + id).val());
                                    $('#discount').val($('#discount_' + id).val());
                                    $('#fright').val($('#fright_' + id).val());
                                    $('#customcharges').val($('#customcharges_' + id).val());
                                    $('#taxableamount').val($('#taxableamount_' + id).val());
                                    $('#vatamount').val($('#vatamount_' + id).val());
                                    $('#taxableamount').val($('#taxableamount_' + id).val());
                                    $('#totalamount').val($('#totalamount_' + id).val());
                                    $("#serialno").val($('#serialno_' + id).val());

                                    $('#cart_item_id').val($('#cart_item_id_' + id).val());
                                    $('#deal_ref_id').val($('#deal_ref_id_' + id).val());
                                }

                                function row_clear() {
                                    $("#part_number_new").val('');
                                    $("#select2-part_number_new-container").html('');
                                    $('#description_new').val('');
                                    $('#tax').val('');
                                    $('#qty').val('');
                                    $('#unitprice').val('');
                                    $('#value').val('');
                                    $('#discount').val('0');
                                    $('#fright').val('0');
                                    $('#customcharges').val('0');
                                    $('#taxableamount').val('');
                                    $('#vatamount').val('');
                                    $('#taxableamount').val('');
                                    $('#totalamount').val('');
                                    $("#serialno").val("");
                                    var id = $("#vendors").val();
                                    get_vendors_detail(id);
                                }

                                function row_update() {
                                    $("#loading_bg").css("display", "block");
                                    var itm_id = $('#cart_item_id').val();
                                    if ($('#deal_ref_id').val() != "") {
                                        var deal_ref_id = $('#deal_ref_id').val();
                                    } else { var deal_ref_id = 0; }
                                    var part_number = $('#part_number_new').val();
                                    var description = $("#description_new").val();
                                    //var description = $('#description_new').val();
                                    var tax = $('#tax').val();
                                    var qty = $('#qty').val();
                                    var unitprice = $('#unitprice').val();
                                    var value = $('#value').val();
                                    var discount = $('#discount').val();
                                    var fright = $('#fright').val();
                                    var customcharges = $('#customcharges').val();
                                    var taxableamount = $('#taxableamount').val();
                                    var vatamount = $('#vatamount').val();

                                    var action = "{{ URL::to('update-purchase-order-items-cart') }}";
                                    $.ajax({
                                        url: action,
                                        type: "POST",
                                        data: {
                                            _token: '{{ csrf_token() }}',
                                            itm_id: itm_id,
                                            deal_ref_id: deal_ref_id,
                                            part_number: part_number,
                                            description: description,
                                            tax: tax,
                                            qty: qty,
                                            unitprice: unitprice,
                                            value: value,
                                            discount: discount,
                                            fright: fright,
                                            customcharges: customcharges,
                                            taxableamount: taxableamount,
                                            vatamount: vatamount,
                                            serialno: $('#serialno').val(),
                                        },
                                        cache: false,
                                        success: function (dataResult) {
                                            var dataResult = JSON.parse(dataResult);
                                            var len = 0;
                                            var getSelectedRows = "";
                                            if (dataResult['data'] != null) {
                                                len = dataResult['data'].length;
                                            }
                                            if (len > 0) {

                                                var qty_total = 0; var value_total = 0; var discount_total = 0; var fright_total = 0; var customcharges_total = 0; var taxableamount_total = 0; var vatamount_total = 0; var taxableamount_total1 = 0; var vatamount_total1 = 0; var amount_total = 0;

                                                for (var i = 0; i < len; i++) {

                                                    getSelectedRows += "<tr>\
                                                            <td>"+ dataResult['data'][i].sort_id + "</td>\
                                                            <td>"+ dataResult['data'][i].partno + " <input type='hidden' id='partno_" + dataResult['data'][i].id + "' value='" + dataResult['data'][i].partno + "' /><input type='hidden' id='pid_" + dataResult['data'][i].id + "' value='" + dataResult['data'][i].part_number + "' /></td>\
                                                            <td>"+ dataResult['data'][i].description + "<input type='hidden' id='description_" + dataResult['data'][i].id + "' value='" + dataResult['data'][i].description + "' /></td>\
                                                            <td>"+ dataResult['data'][i].tax + " <input type='hidden' id='tax_" + dataResult['data'][i].id + "' value='" + dataResult['data'][i].tax + "' /></td>\
                                                            <td class='text-center'>"+ dataResult['data'][i].qty + " <input type='hidden' id='qty_" + dataResult['data'][i].id + "' value='" + dataResult['data'][i].qty + "' /></td>\
                                                            <td class='text-right'>"+ dataResult['data'][i].unitprice + " <input type='hidden' id='unitprice_" + dataResult['data'][i].id + "' value='" + dataResult['data'][i].unitprice + "' /></td>\
                                                            <td class='text-right'>"+ dataResult['data'][i].value + " <input type='hidden' id='value_" + dataResult['data'][i].id + "' value='" + dataResult['data'][i].value + "' /></td>\
                                                            <td class='text-right'>"+ dataResult['data'][i].discount + " <input type='hidden' id='discount_" + dataResult['data'][i].id + "' value='" + dataResult['data'][i].discount + "' /></td>\
                                                            <td class='text-right'>"+ dataResult['data'][i].fright + " <input type='hidden' id='fright_" + dataResult['data'][i].id + "' value='" + dataResult['data'][i].fright + "' /></td>\
                                                            <td class='text-right'>"+ dataResult['data'][i].customcharges + " <input type='hidden' id='customcharges_" + dataResult['data'][i].id + "' value='" + dataResult['data'][i].customcharges + "' /></td>\
                                                            <td class='text-right'>"+ dataResult['data'][i].taxableamount + " <input type='hidden' id='taxableamount_" + dataResult['data'][i].id + "' value='" + dataResult['data'][i].taxableamount + "' /></td>\
                                                            <td class='text-right'>"+ dataResult['data'][i].vatamount + " <input type='hidden' id='vatamount_" + dataResult['data'][i].id + "' value='" + dataResult['data'][i].vatamount + "' /></td>\
                                                            <td class='text-right'>"+ (Number(dataResult['data'][i].taxableamount) + Number(dataResult['data'][i].vatamount)) + " <input type='hidden' id='totalamount_" + dataResult['data'][i].id + "' value='" + (Number(dataResult['data'][i].taxableamount) + Number(dataResult['data'][i].vatamount)) + "' /></td>\
                                                            <td class='text-right'>"+ dataResult['data'][i].serialno + " <input type='hidden' id='serialno_" + dataResult['data'][i].id + "' value='" + dataResult['data'][i].serialno + "' /></td>\
                                                            <td>\
                                                                <input type='hidden' id='cart_item_id_"+ dataResult['data'][i].id + "' value='" + dataResult['data'][i].id + "' />\
                                                                <input type='hidden' id='deal_ref_id_"+ dataResult['data'][i].id + "' value='" + dataResult['data'][i].refid + "' />\
                                                                <a onclick='row_edit("+ dataResult['data'][i].id + ")' class='btn-sm btn-primary'><i class='fa fa-edit' aria-hidden='true'></i></a>\
                                                                <a onclick='row_delete("+ dataResult['data'][i].id + ")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a>\
                                                            </td>\
                                                            </tr>";
                                                    qty_total += Number(dataResult['data'][i].qty);
                                                    value_total += Number(dataResult['data'][i].value);
                                                    discount_total += Number(dataResult['data'][i].discount);
                                                    fright_total += Number(dataResult['data'][i].fright);
                                                    customcharges_total += Number(dataResult['data'][i].customcharges);
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
                                                $("#fright_total").text(fright_total);
                                                $("#customcharges_total").text(customcharges_total);
                                                $("#taxableamount_total").text(taxableamount_total);
                                                $("#vatamount_total").text(vatamount_total);
                                                $("#amount_total").text(amount_total);

                                                $("#select2-part_number_new-container").html('');

                                                $('#po-table tbody').empty();
                                                $("#po-table tbody").append(getSelectedRows);

                                                $('#btn_add_row').css("display", 'block');
                                                $('#update_add_row').css("display", 'none');
                                                row_clear();
                                            }
                                            else {
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
                                    var action = "{{ URL::to('delete-purchase-order-items-cart') }}";
                                    $.ajax({
                                        url: action,
                                        type: "POST",
                                        data: {
                                            _token: '{{ csrf_token() }}',
                                            id: id,
                                        },
                                        cache: false,
                                        success: function (dataResult) {
                                            var dataResult = JSON.parse(dataResult);
                                            var len = 0;
                                            var getSelectedRows = "";
                                            if (dataResult['data'] != null) {
                                                len = dataResult['data'].length;
                                            }
                                            if (len > 0) {

                                                var qty_total = 0; var value_total = 0; var discount_total = 0; var fright_total = 0; var customcharges_total = 0; var taxableamount_total = 0; var vatamount_total = 0; var amount_total = 0;


                                                for (var i = 0; i < len; i++) {


                                                    getSelectedRows += "<tr>\
                                                            <td>"+ dataResult['data'][i].sort_id + "</td>\
                                                            <td>"+ dataResult['data'][i].partno + " <input type='hidden' id='partno_" + dataResult['data'][i].id + "' value='" + dataResult['data'][i].partno + "' /><input type='hidden' id='pid_" + dataResult['data'][i].id + "' value='" + dataResult['data'][i].part_number + "' /></td>\
                                                            <td>"+ dataResult['data'][i].description + "<input type='hidden' id='description_" + dataResult['data'][i].id + "' value='" + dataResult['data'][i].description + "' /></td>\
                                                            <td>"+ dataResult['data'][i].tax + " <input type='hidden' id='tax_" + dataResult['data'][i].id + "' value='" + dataResult['data'][i].tax + "' /></td>\
                                                            <td class='text-center'>"+ dataResult['data'][i].qty + " <input type='hidden' id='qty_" + dataResult['data'][i].id + "' value='" + dataResult['data'][i].qty + "' /></td>\
                                                            <td class='text-right'>"+ dataResult['data'][i].unitprice + " <input type='hidden' id='unitprice_" + dataResult['data'][i].id + "' value='" + dataResult['data'][i].unitprice + "' /></td>\
                                                            <td class='text-right'>"+ dataResult['data'][i].value + " <input type='hidden' id='value_" + dataResult['data'][i].id + "' value='" + dataResult['data'][i].value + "' /></td>\
                                                            <td class='text-right'>"+ dataResult['data'][i].discount + " <input type='hidden' id='discount_" + dataResult['data'][i].id + "' value='" + dataResult['data'][i].discount + "' /></td>\
                                                            <td class='text-right'>"+ dataResult['data'][i].fright + " <input type='hidden' id='fright_" + dataResult['data'][i].id + "' value='" + dataResult['data'][i].fright + "' /></td>\
                                                            <td class='text-right'>"+ dataResult['data'][i].customcharges + " <input type='hidden' id='customcharges_" + dataResult['data'][i].id + "' value='" + dataResult['data'][i].customcharges + "' /></td>\
                                                            <td class='text-right'>"+ dataResult['data'][i].taxableamount + " <input type='hidden' id='taxableamount_" + dataResult['data'][i].id + "' value='" + dataResult['data'][i].taxableamount + "' /></td>\
                                                            <td class='text-right'>"+ dataResult['data'][i].vatamount + " <input type='hidden' id='vatamount_" + dataResult['data'][i].id + "' value='" + dataResult['data'][i].vatamount + "' /></td>\
                                                            <td class='text-right'>"+ (Number(dataResult['data'][i].taxableamount) + Number(dataResult['data'][i].vatamount)) + " <input type='hidden' id='totalamount_" + dataResult['data'][i].id + "' value='" + (Number(dataResult['data'][i].taxableamount) + Number(dataResult['data'][i].vatamount)) + "' /></td>\
                                                            <td class='text-right'>"+ dataResult['data'][i].serialno + " <input type='hidden' id='serialno_" + dataResult['data'][i].id + "' value='" + dataResult['data'][i].serialno + "' /></td>\
                                                            <td>\
                                                                <input type='hidden' id='cart_item_id_"+ dataResult['data'][i].id + "' value='" + dataResult['data'][i].id + "' />\
                                                                <input type='hidden' id='deal_ref_id_"+ dataResult['data'][i].id + "' value='" + dataResult['data'][i].refid + "' />\
                                                                <a onclick='row_edit("+ dataResult['data'][i].id + ")' class='btn-sm btn-primary'><i class='fa fa-edit' aria-hidden='true'></i></a>\
                                                                <a onclick='row_delete("+ dataResult['data'][i].id + ")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a>\
                                                            </td>\
                                                            </tr>";

                                                    qty_total += Number(dataResult['data'][i].qty);
                                                    value_total += Number(dataResult['data'][i].value);
                                                    discount_total += Number(dataResult['data'][i].discount);
                                                    fright_total += Number(dataResult['data'][i].fright);
                                                    customcharges_total += Number(dataResult['data'][i].customcharges);
                                                    taxableamount_total += Number(dataResult['data'][i].taxableamount);
                                                    vatamount_total += Number(dataResult['data'][i].vatamount);
                                                    amount_total += Number(dataResult['data'][i].taxableamount + dataResult['data'][i].vatamount);

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
                                                $("#fright_total").text(fright_total);
                                                $("#customcharges_total").text(customcharges_total);
                                                $("#taxableamount_total").text(taxableamount_total);
                                                $("#vatamount_total").text(vatamount_total);
                                                $("#amount_total").text(amount_total);

                                                $('#po-table tbody').empty();
                                                $("#po-table tbody").append(getSelectedRows);
                                            }
                                            else {
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
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 mt-2">
                    <table class="table table-bordered table-striped" id="po-table" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th style="width:30px;"></th>
                                <th style="width:100px;">@lang('Part No')</th>
                                <th style="width:350px;">@lang('Description')</th>
                                <th style="width:70px;">@lang('Tax')</th>
                                <th class="text-center" style="width:70px;">@lang('Qty')</th>
                                <th class="text-right" style="width:80px;">@lang('Unit Price')</th>
                                <th class="text-right" style="width:70px;">@lang('Value')</th>
                                <th class="text-right" style="width:70px;">
                                    <a style="cursor: pointer;" class="text-danger float-right" data-toggle="modal"
                                        data-target="#modalDiscount">Discount</a>
                                </th>
                                <th class="text-right" style="width:70px;">
                                    <a style="cursor: pointer;" class="text-danger float-right" data-toggle="modal"
                                        data-target="#modalFreight">Freight</a>
                                </th>
                                <th class="text-right" style="width:130px;">
                                    <a style="cursor: pointer;" class="text-danger float-right" data-toggle="modal"
                                        data-target="#modalCustom">Custom Charges</a>
                                </th>
                                <th class="text-right" style="width:120px;">@lang('Taxable Amount')</th>
                                <th class="text-right" style="width:100px;">@lang('VAT Amount')</th>
                                <th class="text-right" style="width:100px;">@lang('Total Amount')</th>
                                <th class="text-right" style="width:100px;">@lang('Serial No')</th>
                                <th class="text-right" style="width:70px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($cart) > 0)
                                @foreach ($cart as $dt)
                                    <tr>
                                        <td><input type="number" class="form-control2" id="sort_id_{{ $dt->id }}"
                                                value="{{ $dt->sort_id }}" /></td>
                                        <td>{{ $dt->partno }} <input type="hidden" id="partno_{{ $dt->id }}"
                                                value="{{ $dt->partno }}" />
                                            <input type="hidden" id="pid_{{ $dt->id }}" value="{{ $dt->part_number }}" />
                                        </td>
                                        <td>{{ $dt->description }} <input type="hidden" id="description_{{ $dt->id }}"
                                                value="{{ $dt->description }}" /></td>
                                        <td>{{ $dt->tax }} <input type="hidden" id="tax_{{ $dt->id }}"
                                                value="{{ intval($dt->tax) }}" /></td>
                                        <td class="text-center">{{ $dt->qty }} <input type="hidden" id="qty_{{ $dt->id }}"
                                                value="{{ $dt->qty }}" /></td>
                                        <td align="right">{{ @App\SysHelper::com_curr_format($dt->unitprice,2,'.',',') }} <input
                                                type="hidden" id="unitprice_{{ $dt->id }}" value="{{ $dt->unitprice }}" /></td>
                                        <td align="right">{{ @App\SysHelper::com_curr_format($dt->value,2,'.',',') }} <input
                                                type="hidden" id="value_{{ $dt->id }}" value="{{ $dt->value }}" /></td>
                                        <td align="right">{{ @App\SysHelper::com_curr_format($dt->discount,2,'.',',') }} <input
                                                type="hidden" id="discount_{{ $dt->id }}" value="{{ $dt->discount }}" /></td>
                                        <td align="right">{{ @App\SysHelper::com_curr_format($dt->fright,2,'.',',') }} <input
                                                type="hidden" id="fright_{{ $dt->id }}" value="{{ $dt->fright }}" /></td>
                                        <td align="right">{{ @App\SysHelper::com_curr_format($dt->customcharges,2,'.',',') }} <input
                                                type="hidden" id="customcharges_{{ $dt->id }}" value="{{ $dt->customcharges }}" />
                                        </td>
                                        <td align="right">{{ @App\SysHelper::com_curr_format($dt->taxableamount,2,'.',',') }} <input
                                                type="hidden" id="taxableamount_{{ $dt->id }}" value="{{ $dt->taxableamount }}" />
                                        </td>
                                        <td align="right">{{ @App\SysHelper::com_curr_format($dt->vatamount,2,'.',',') }} <input
                                                type="hidden" id="vatamount_{{ $dt->id }}" value="{{ $dt->vatamount }}" /></td>
                                        <td align="right">{{
                                            @App\SysHelper::com_curr_format($dt->taxableamount+$dt->vatamount,2,'.',',') }} <input
                                                type="hidden" id="totalamount_{{ $dt->id }}"
                                                value="{{ $dt->taxableamount + $dt->vatamount }}" /></td>
                                        <td>{{ $dt->serialno }} <input type="hidden" id="serialno_{{ $dt->id }}"
                                                value="{{ $dt->serialno }}" /></td>
                                        <td>
                                            <input type="hidden" id="cart_item_id_{{ $dt->id }}" value="{{ $dt->id }}" />
                                            <input type="hidden" id="deal_ref_id_{{ $dt->id }}" value="{{ $dt->refid }}" />
                                            <a onclick="row_edit({{ $dt->id }})" class="btn-sm btn-primary"><i class="fa fa-edit"
                                                    aria-hidden="true"></i></a>
                                            <a onclick="row_delete({{ $dt->id }})" class="btn-sm btn-danger"><i class="fa fa-trash"
                                                    aria-hidden="true"></i></a>
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
                                <td class="font-weight-bold"></td>
                                <td class="text-center font-weight-bold"><label
                                        id="qty_total">{{ $cart->sum('qty') }}</label></td>
                                <td class="text-right font-weight-bold"><label id="unitprice_total"></label></td>
                                <td class="text-right font-weight-bold"><label id="value_total">{{
                                        @App\SysHelper::com_curr_format($cart->sum('value'),2,'.',',') }}</label></td>
                                <td class="text-right font-weight-bold"><label id="discount_total">{{
                                        @App\SysHelper::com_curr_format($cart->sum('discount'),2,'.',',') }}</label></td>
                                <td class="text-right font-weight-bold"><label id="fright_total">{{
                                        @App\SysHelper::com_curr_format($cart->sum('fright'),2,'.',',') }}</label></td>
                                <td class="text-right font-weight-bold"><label id="customcharges_total">{{
                                        @App\SysHelper::com_curr_format($cart->sum('customcharges'),2,'.',',') }}</label>
                                </td>
                                <td class="text-right font-weight-bold"><label id="taxableamount_total">{{
                                        @App\SysHelper::com_curr_format($cart->sum('taxableamount'),2,'.',',') }}</label>
                                </td>
                                <td class="text-right font-weight-bold"><label id="vatamount_total">{{
                                        @App\SysHelper::com_curr_format($cart->sum('vatamount'),2,'.',',') }}</label></td>
                                <td class="text-right font-weight-bold"><label id="amount_total">{{
                                        @App\SysHelper::com_curr_format($cart->sum('taxableamount') +
                                        $cart->sum('vatamount'),2,'.',',') }}</label></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div style="display: none;">
                    <button type="button" class="primary-btn small fix-gr-bg" id="addRowPO"><span
                            class="ti-plus pr-2"></span>@lang('lang.item')</button>
                </div>


                <script>
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
                        $('#description_' + id + '').val(selOpt2.trim());
                        $('#description_' + id + '').focus();
                    }

                    function calc_change(id) {
                        //var net_vat = $('#net_vat').val();
                        var net_vat = $('#tax_' + id + '').val();

                        var qty = $('#qty_' + id + '').val();
                        var unitprice = $('#unitprice_' + id + '').val();
                        var value = $('#value_' + id + '').val();
                        var discount = $('#discount_' + id + '').val();
                        var fright = $('#fright_' + id + '').val();
                        var customcharges = $('#customcharges_' + id + '').val();

                        qty = (qty === '') ? '0' : qty;
                        unitprice = (unitprice === '') ? '0' : unitprice;
                        var fin_value = (unitprice * qty);
                        $('#value_' + id + '').val(fin_value.toFixed(@json(session('logged_session_data.decimal_point'))));


                        value = (value === '') ? '0' : value;
                        discount = (discount === '') ? '0' : discount;
                        fright = (fright === '') ? '0' : fright;
                        customcharges = (customcharges === '') ? '0' : customcharges;
                        var fin_taxableamount = ((unitprice * qty) + Number(customcharges) + Number(fright) - Number(discount)) * ((Number(net_vat) +
                            100) / 100);
                        $('#taxableamount_' + id + '').val(fin_taxableamount.toFixed(@json(session('logged_session_data.decimal_point'))));

                        var fin_vatamount = ((unitprice * qty) + Number(customcharges) + Number(fright) - Number(discount)) * ((Number(net_vat)) / 100);
                        var vatamount = $('#vatamount_' + id + '').val(fin_vatamount.toFixed(@json(session('logged_session_data.decimal_point'))));

                        calc_total();
                    }

                    function calc_total() {
                        var countrow = document.getElementById('po-row-count').value;
                        var t1 = 0,
                            t2 = 0,
                            t3 = 0,
                            t4 = 0,
                            t5 = 0,
                            t6 = 0,
                            t7 = 0;
                        t8 = 0;
                        for (var i = 1; i <= countrow; i++) {
                            t1 += Number($('#qty_' + i).val());
                            t2 += Number($('#unitprice_' + i).val());
                            t3 += Number($('#value_' + i).val());
                            t4 += Number($('#discount_' + i).val());
                            t8 += Number($('#fright_' + i).val());
                            t5 += Number($('#customcharges_' + i).val());
                            t6 += Number($('#taxableamount_' + i).val());
                            t7 += Number($('#vatamount_' + i).val());
                        }
                        $('#qty_total').text(t1);
                        $('#unitprice_total').text(t2.toFixed(@json(session('logged_session_data.decimal_point'))));
                        $('#value_total').text(t3.toFixed(@json(session('logged_session_data.decimal_point'))));
                        $('#discount_total').text(t4.toFixed(@json(session('logged_session_data.decimal_point'))));
                        $('#fright_total').text(t8.toFixed(@json(session('logged_session_data.decimal_point'))));
                        $('#customcharges_total').text(t5.toFixed(@json(session('logged_session_data.decimal_point'))));
                        $('#taxableamount_total').text(t6.toFixed(@json(session('logged_session_data.decimal_point'))));
                        $('#vatamount_total').text(t7.toFixed(@json(session('logged_session_data.decimal_point'))));
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



            <div class="row mt-40" style="display: none;">
                <div class="col-lg-12">
                    <div class="input-effect">
                        <label class="dynamicslbl">@lang('lang.note') <span></span></label>
                        <textarea class="txtbx primary-input form-control" cols="0" rows="4"
                            name="note">{{ isset($edit) ? (!empty(@$edit->note) ? @$edit->note : '') : old('description') }}</textarea>
                        <span class="focus-border textarea"></span>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-12 text-right">
                    <button type="submit" class="btn btn-info" value="1" name="btnSubmit" id="btnSubmit"
                        onclick="return validate_form_submission()">
                        <span class="ti-check"></span>
                        Save & Print Purchase Order
                    </button>

                    <button type="submit" class="btn btn-primary" name="btnSubmit" id="btnSubmit"
                        onclick="return validate_form_submission()">
                        <span class="ti-check"></span>
                        @if (isset($edit))
                            @lang('lang.update')
                        @else
                            @lang('lang.save')
                        @endif
                        @lang('Purchase Order')
                    </button>
                </div>
                {{ Form::close() }}
            </div>

        </div>
    </div>

    <!-- Modal Excel Quote-->
    <div class="modal fade" id="ModalExcelQuote" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document" style="max-width: 1200px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">PO Items Excel Import</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'add-purchase-order-items-excel-cart', 'method' => 'POST', 'id' => 'add-purchase-order-items-excel-cart']) }}
                <input type="hidden" id="excel_deal_id" name="excel_deal_id" value="{{ @$edit->id }}" />
                <input type="hidden" id="excel_cust_id" name="excel_cust_id" value="{{ @$edit->cust_id }}" />
                <input type="hidden" id="excel_vat" name="excel_vat"
                    value="{{ @$edit->customername->vat_percentage ?? 0 }}" />
                <input type="hidden" id="excel_company_id" name="excel_company_id" value="" />
                <input type="hidden" id="excel_currency_id" name="excel_currency_id" value="" />
                <input type="hidden" id="excel_customer_type" name="excel_customer_type" value="" />
                <input type="hidden" id="excel_quote_validity" name="excel_quote_validity" value="" />
                <input type="hidden" id="excel_payment_terms" name="excel_payment_terms" value="" />
                <input type="hidden" id="excel_delivery_date" name="excel_delivery_date" value="" />
                <input type="hidden" id="excel_payment_terms_txt" name="excel_payment_terms_txt" value="" />
                <input type="hidden" id="excel_delivery_time" name="excel_delivery_time" value="" />

                <script>
                    function add_excel_data() {
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
                            (<a href="{{ url('public/uploads/product_upload/po_items_sample_format.csv') }}"
                                target="_blank">Sample File</a>)
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
                                    reader.onload = function (event) {
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



                                            var part_number = <?php    echo json_encode($part_number); ?>; // Convert PHP array to JS array

                                            var lowercase_part_number = part_number.map(function (value) {
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

                                            var deleteCell = newRow.insertCell(6);  // Last cell for delete button
                                            var deleteButton = document.createElement('button');
                                            deleteButton.type = 'button';  // Make sure the button doesn't submit a form
                                            deleteButton.textContent = 'Delete';
                                            deleteButton.classList.add('btn-sm');
                                            deleteButton.classList.add('btn-danger');
                                            deleteButton.onclick = function () {
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


    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

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
                                    <label class="dynamicslbl">@lang('Shipping Name') <span>*</span> </label>
                                    <input
                                        class="dynamicstxt primary-input form-control {{ $errors->has('shipping_name') ? 'is-invalid' : ' ' }}"
                                        type="text" id="shipping_name_add" name="shipping_name"
                                        value="{{ isset($editData) ? @$editData->shipping_name : old('shipping_name') }}">


                                    <span class="modal_input_validation_1 red_alert"></span>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-20">
                                <div class="input-effect">
                                    <label class="dynamicslbl"> @lang('Contact Name') <span>*</span> </label>
                                    <input
                                        class="dynamicstxt primary-input form-control {{ $errors->has('contact_name') ? 'is-invalid' : ' ' }}"
                                        type="text" id="contact_name_add" name="contact_name"
                                        value="{{ isset($editData) ? @$editData->contact_name : old('contact_name') }}">


                                    <span class="modal_input_validation_2 red_alert"></span>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-20">
                                <div class="input-effect">
                                    <label class="dynamicslbl"> @lang('Contact No') <span>*</span> </label>
                                    <input
                                        class="dynamicstxt primary-input form-control{{ $errors->has('contact_no') ? ' is-invalid' : '' }}"
                                        type="number" id="contact_no_add" name="contact_no"
                                        value="{{ isset($editData) ? @$editData->contact_no : old('contact_no') }}">


                                    <span class="modal_input_validation_3 red_alert"></span>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-20">
                                <div class="input-effect">
                                    <label class="dynamicslbl"> @lang('Address 1') <span>*</span> </label>
                                    <input
                                        class="dynamicstxt primary-input form-control{{ $errors->has('address1') ? ' is-invalid' : '' }}"
                                        type="text" id="address1_add" name="address1"
                                        value="{{ isset($editData) ? @$editData->address1 : old('address1') }}">


                                    <span class="modal_input_validation_4 red_alert"></span>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-20">
                                <div class="input-effect">
                                    <label class="dynamicslbl"> @lang('Address 2') <span>*</span> </label>
                                    <input
                                        class="dynamicstxt primary-input form-control{{ $errors->has('address2') ? ' is-invalid' : '' }}"
                                        type="text" id="address2_add" name="address2"
                                        value="{{ isset($editData) ? @$editData->address2 : old('address2') }}">


                                    <span class="modal_input_validation_5 red_alert"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="col-lg-12 text-center">
                                    <div class="mt-40 d-flex justify-content-between">
                                        <button class="primary-btn tr-bg" data-dismiss="modal" type="button"
                                            id="btn_close2">
                                            @lang('lang.cancel')
                                        </button>
                                        <input class="primary-btn fix-gr-bg" type="submit" value="save"
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

    <div class="modal fade admin-query" id="dn_srlno_popup_win" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
            <div class="modal-content">
                <div class="modal-header m-0 p-3">
                    <h4 class="modal-title">
                        <div id="div_serialno_title"></div>
                    </h4>
                    <button class="close" data-dismiss="modal" type="button">
                        ×
                    </button>
                </div>
                <div class="modal-body m-0 p-3">
                    <input type="hidden" id="hd_pending_dn_id" />
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12 mb-20">
                                <div class="input-effect">
                                    <label class="dynamicslbl"> @lang('Serial No') <span>*</span> </label>
                                    <textarea class="dynamicstxt primary-input form-control" id="srlno_textarea"
                                        name="srlno_textarea"></textarea>
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

                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'add-purchase-order-items-cart-discount', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Discount Amount</label>
                                <input type="text" class="form-control" id="discount_amount" name="discount_amount"
                                    required />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="discount_amount_po_id" value="{{ @$po->id }}" />
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

                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'add-purchase-order-items-cart-freight', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Freight Amount</label>
                                <input type="text" class="form-control" id="freight_amount" name="freight_amount"
                                    required />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="freight_amount_po_id" value="{{ @$po->id }}" />
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

                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'add-purchase-order-items-cart-custom', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
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
                    <input type="hidden" name="custom_amount_po_id" value="{{ @$po->id }}" />
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Split Custom Charges</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    {{-- ModalCustom --}}

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
                                    <label class="dynamicslbl"> @lang('Attach File') <span>*</span> </label>
                                    <input class="form-control" type="file" id="att_file" name="att_file"
                                        onchange="updateDocName()" />
                                </div>
                            </div>
                            <div class="col-lg-4 mb-20">
                                <div class="input-effect">
                                    <label class="dynamicslbl"> @lang('Date') <span>*</span> </label>
                                    <input class="form-control" type="date" id="att_date" name="att_date"
                                        value="{{ date('Y-m-d') }}" />
                                </div>
                            </div>
                            <div class="col-lg-4 mb-20">
                                <div class="input-effect">
                                    <label class="dynamicslbl"> @lang('File Name') <span>*</span> </label>
                                    <input class="form-control" type="text" id="doc_name" name="doc_name" value="" />
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
                                <table id="att-table" class="table table-bordered table-striped" width="100%"
                                    cellspacing="0">
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
        function add_attachment() {
            $("#loading_bg").css("display", "block");

            if ($('#att_file').val() == "") { $('#att_file').focus(); $("#loading_bg").css("display", "none"); return false; }

            var action = "{{ URL::to('add-purchase-order-attachment') }}";

            var formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');  // Append CSRF token
            formData.append('doc_id', 0);
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
                success: function (dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var getSelectedRows = "";
                    if (dataResult['data'] != null) {
                        len = dataResult['data'].length;
                    }
                    if (len > 0) {
                        for (var i = 0; i < len; i++) {
                            getSelectedRows += "<tr>\
                                    <td>"+ Number(i + 1) + "</td>\
                                    <td>"+ get_format_date(dataResult['data'][i].doc_date) + "</td>\
                                    <td><a href='../../"+ dataResult['data'][i].doc_file + "' target='_blank'>" + dataResult['data'][i].doc_name + "</a></td>\
                                    <td><a onclick='delete_attachment("+ dataResult['data'][i].id + ")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
                                    </tr>";
                        }
                        $('#att_file').val('');
                        $('#doc_name').val('');
                        $('#att-table tbody').empty();
                        $("#att-table tbody").append(getSelectedRows);
                    }
                    else {
                        $('#att-table tbody').empty();
                    }
                }
            });
            $("#loading_bg").css("display", "none");
        }
        function view_attachment() {
            $("#loading_bg").css("display", "block");
            $('#att_cust_name').text($('#vendors :selected').text() + " " + $('#doc_number').val());

            var action = "{{ URL::to('view-purchase-order-attachment') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    doc_id: 0,
                },
                cache: false,
                success: function (dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var getSelectedRows = "";
                    if (dataResult['data'] != null) {
                        len = dataResult['data'].length;
                    }
                    if (len > 0) {
                        for (var i = 0; i < len; i++) {
                            getSelectedRows += "<tr>\
                                    <td>"+ Number(i + 1) + "</td>\
                                    <td>"+ get_format_date(dataResult['data'][i].doc_date) + "</td>\
                                    <td><a href='../../"+ dataResult['data'][i].doc_file + "' target='_blank'>" + dataResult['data'][i].doc_name + "</a></td>\
                                    <td><a onclick='delete_attachment("+ dataResult['data'][i].id + ")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
                                    </tr>";
                        }
                        $('#att_file').val('');
                        $('#doc_name').val('');
                        $('#att-table tbody').empty();
                        $("#att-table tbody").append(getSelectedRows);
                    }
                    else {
                        $('#att-table tbody').empty();
                    }
                }
            });
            $("#loading_bg").css("display", "none");
        }
        function delete_attachment(id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('delete-purchase-order-attachment') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    doc_id: 0,
                },
                cache: false,
                success: function (dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var getSelectedRows = "";
                    if (dataResult['data'] != null) {
                        len = dataResult['data'].length;
                    }
                    if (len > 0) {
                        for (var i = 0; i < len; i++) {
                            getSelectedRows += "<tr>\
                                    <td>"+ Number(i + 1) + "</td>\
                                    <td>"+ get_format_date(dataResult['data'][i].doc_date) + "</td>\
                                    <td><a href='../../"+ dataResult['data'][i].doc_file + "' target='_blank'>" + dataResult['data'][i].doc_name + "</a></td>\
                                    <td><a onclick='delete_attachment("+ dataResult['data'][i].id + ")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
                                    </tr>";
                        }
                        $('#att_file').val('');
                        $('#doc_name').val('');
                        $('#att-table tbody').empty();
                        $("#att-table tbody").append(getSelectedRows);
                    }
                    else {
                        $('#att-table tbody').empty();
                    }
                }
            });
            $("#loading_bg").css("display", "none");
        }
    </script>

    {{-- attachment end--}}

    <script>
        function srlno_add() {
            var hdtxt = $("#description_new").val();
            var srl = $("#serialno").val();
            $("#srlno_textarea").val(srl);
            $("#div_serialno_title").html(hdtxt);
            document.getElementById('add_srlno_popup').click();
            $("#srlno_textarea").focus();
        }
        function srlno_add_item() {
            var srltxt = $("#srlno_textarea").val();
            $("#serialno").val(srltxt);
            document.getElementById('add_srl_cls').click();
        }
    </script>

    <script>

        $(document).on("change", "#vendors", function () {
            var id = $("#vendors").val();
            get_vendors_detail(id);
        });
        function get_vendors_detail(id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('get-chartofaccounts-info') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                },
                cache: false,
                success: function (dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var len = 0;
                    if (dataResult['data'] != null) {
                        len = dataResult['data'].length;
                    }
                    if (len > 0) {
                        for (var i = 0; i < len; i++) {
                            $("#payment_terms").val(dataResult['data'][i].payment_terms);
                            $("#contact_person_name").val(dataResult['data'][i].contcat_person);
                            $("#contact_person_email").val(dataResult['data'][i].email);
                            //$("#shipping_address_2").val(dataResult['data'][i].address2);
                            $("#contact_person_telephone").val(dataResult['data'][i].contcat_number);

                            $("#supplier_type").val(dataResult['data'][i].supplier_type);
                            $("#purchase_type").val(dataResult['data'][i].purchase_type);

                            //$("select[id=tax] option:first").text(dataResult['data'][i].vat_percentage +'%');
                            //$("select[id=tax] option:first").val(dataResult['data'][i].vat_percentage);
                            $("#tax").val(dataResult['data'][i].vat_percentage);

                            $("#country").val(dataResult['data'][i].vat_country);
                            $("#state").val(dataResult['data'][i].vat_state);
                        }
                    }
                    else {
                        $("#payment_terms").val("");
                        $("#contact_person_name").val("");
                        $("#contact_person_email").val("");
                        //$("#shipping_address_2").val("");
                        $("#contact_person_telephone").val("");
                        $("#country").val("");
                        $("#state").val("");
                    }
                }
            });
            $("#loading_bg").css("display", "none");
        }

        $(document).on("change", "#shipping_supplier", function () {
            var id = $("#shipping_supplier").val();
            get_shipping_supplier_detail2(id);
        });
        function get_shipping_supplier_detail2(id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('get-chartofaccounts-info') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                },
                cache: false,
                success: function (dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var len = 0;
                    if (dataResult['data'] != null) {
                        len = dataResult['data'].length;
                    }
                    if (len > 0) {
                        for (var i = 0; i < len; i++) {
                            //$("#shipping_name").val(dataResult['data'][i].customer_salutation+'. '+dataResult['data'][i].first_name+' '+dataResult['data'][i].last_name);
                            //$("#shipping_name").val(dataResult['data'][i].contcat_person);
                            $("#shipping_address_1").val(dataResult['data'][i].address + '\n' + dataResult['data'][i].address2);
                            //$("#shipping_email").val(dataResult['data'][i].email);
                            //$("#shipping_contact_no").val(dataResult['data'][i].contcat_number);
                        }
                    }
                    else {
                        //$("#shipping_name").val("");
                        $("#shipping_address_1").val("");
                        //$("#shipping_email").val("");
                        //$("#shipping_contact_no").val("");    
                    }
                }
            });
            $("#loading_bg").css("display", "none");
        }
        function get_shipping_supplier_detail(id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('get-chartofaccounts-info') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                },
                cache: false,
                success: function (dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var len = 0;
                    if (dataResult['data'] != null) {
                        len = dataResult['data'].length;
                    }
                    if (len > 0) {
                        for (var i = 0; i < len; i++) {
                            $("#shipping_name").val(dataResult['data'][i].customer_salutation + '. ' + dataResult['data'][i].first_name + ' ' + dataResult['data'][i].last_name);
                            //$("#shipping_name").val(dataResult['data'][i].contcat_person);
                            $("#shipping_address_1").val(dataResult['data'][i].address + '\n' + dataResult['data'][i].address2);
                            $("#shipping_email").val(dataResult['data'][i].email);
                            $("#shipping_contact_no").val(dataResult['data'][i].contcat_number);
                        }
                    }
                    else {
                        $("#shipping_name").val("");
                        $("#shipping_address_1").val("");
                        $("#shipping_email").val("");
                        $("#shipping_contact_no").val("");
                    }
                }
            });
            $("#loading_bg").css("display", "none");
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
                success: function (response) {
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
                error: function (XMLHttpRequest, textStatus, errorThrown) { }
            });

            //preventDefault();
        }


        jQuery(document).ready(function () {
            jQuery('input').keypress(function (event) {
                var enterOkClass = jQuery(this).attr('class');
                if (event.which == 13 && enterOkClass != 'enterSubmit') {
                    event.preventDefault();
                    return false;
                }
            });
        });
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
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            $("#btnSubmit").click(function () {
                //setTimeout(function () { disableButton(); }, 0);
            });

            function disableButton() {
                $("#btnSubmit").prop('disabled', true);
            }
        });

        function validate_form_submission() {
            if ($("#vendors").val() == "") {
                alert("Please Fill Vendors");
                $("#vendors").focus();
                return false;
            }
            if ($("#payment_terms").val() == "") {
                alert("Please Fill Payment Terms");
                $("#payment_terms").focus();
                return false;
            }
            if ($("#shipping_name").val() == "") {
                alert("Please Fill Shipping Name");
                $("#shipping_name").focus();
                return false;
            }
            if ($("#shipping_contact_no").val() == "") {
                alert("Please Fill Shipping Contact No");
                $("#shipping_contact_no").focus();
                return false;
            }
            if ($("#supplier_type").val() == "") {
                alert("Please Fill Supplier Type");
                $("#supplier_type").focus();
                return false;
            }
            if ($("#purchase_type").val() == "") {
                alert("Please Fill Purchase Type");
                $("#purchase_type").focus();
                return false;
            }
            if ($("#part_number_1").val() == "none") {
                alert("Please Fill Part Number");
                $("#part_number_1").focus();
                return false;
            }
            if ($("#description_1").val() == "") {
                alert("Please Fill Description");
                $("#description_1").focus();
                return false;
            }
            if ($("#qty_1").val() == "") {
                alert("Please Fill Qty");
                $("#qty_1").focus();
                return false;
            }
            if ($("#unitprice_1").val() == "") {
                alert("Please Fill Unit Price");
                $("#unitprice_1").focus();
                return false;
            }
            if ($("#taxableamount_1").val() == "") {
                alert("Please Fill Taxable Amount");
                $("#taxableamount_1").focus();
                return false;
            }
        }
    </script>

@endsection
<?php try { ?>

<style>
    .form-control:disabled,
    .form-control[readonly] {
        background-color: #ffffff;
    }
</style>

<style>
    /* Custom hover color for Select2 options */
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #deebe1 !important;
        /* Dodger blue */
        color: #1E2224 !important;
        border-bottom-color: #deebe1;
    }

    .select2-container--default .select2-results__option[aria-selected="true"] {
        background-color: #deebe1 !important;
        /* e.g., info blue */
        color: #1E2224 !important;
        border-bottom-color: #deebe1;
    }
</style>




{{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'purchase-order-store', 'method' => 'POST', 'id' => 'tender-create-form']) }}
<input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
<input type="hidden" name="id" value="{{ isset($edit) ? $edit->id : '' }}">
<input type="hidden" name="net_vat" id="net_vat" value="0">

<div class="tab-pane fade show active" id="purchase-order-1" role="tabpanel" aria-labelledby="purchase-order-1-tab">
    <div class="purchase-order-content-header d-flex align-items-center justify-content-between mb-1 gap-2">
        <h4 class="purchase-order-content-header-left">
            New Purchase Order
        </h4>
        <div class="purchase-order-content-header-right d-flex align-items-center justify-content-end gap-2">
            <button type="submit" value="1" name="btnSubmit" class="btn btn-light">
                <i class="ico icon-outline-archive-down-minimlistic text-warning"></i> Save &
                Download
            </button>
            <button type="submit" name="btnSubmit" class="btn btn-light">
                <i class="ico icon-outline-bookmark-opened text-success"></i> Save
            </button>

            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ico icon-outline-hamburger-menu"></i>
                </button>
                <ul class="dropdown-menu" style="">
                    <li data-bs-toggle="modal" data-bs-target="#addpoexcelimport"><a href="#" class="dropdown-item">
                            <i class="ico icon-outline-import text-success"></i>
                            Import</a></li>

                    <li data-bs-toggle="modal" data-bs-target="#attachment_popup_win"><a href="#" class="dropdown-item">
                            <i class="ico icon-bold-file-text text-success"></i>
                            Attachment</a></li>
                </ul>
            </div>

        </div>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <div class="row gap-rows">
                <div class="col-4">
                    <label for="" class="form-check-label">Vendor</label>
                    <select class=" js-account-select" name="vendors" id="vendors" required style="width: 100%;">
                        <option value=""></option>

                    </select>
                </div>
                <div class="col-2">
                    <label class="form-label">PO Number:</label>
                    <div class="form-group">
                        <input readonly type="text" class="form-control" name="doc_number" autocomplete="off"
                            id="doc_number"
                            value="{{ isset($edit) ? (!empty(@$edit->title) ? @$edit->title : old('doc_number')) : @App\SysHelper::get_new_code('sys_purchase_order','PO' ,'doc_number') }}" />
                    </div>
                </div>
                <div class="col-2">
                    <label class="form-label">PO date:</label>
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
                    <div class="form-group">
                        <input type="date" id="po_date" type="date" name="po_date" class="form-control"
                            value="{{ @$value }}" />
                    </div>
                </div>
                <div class="col-2">
                    <label class="form-label">Currency:</label>
                    <div class="form-group">
                        <select class="form-control select2" name="currency" id="currency">
                            @foreach ($currency as $value)
                                <option value="{{ @$value->id }}" @if($company->currency_id == $value->id) selected @endif>
                                    {{ @$value->code }}
                                </option>
                            @endforeach
                        </select>

                        <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>
                    </div>
                    @if ($errors->has('currency'))
                        <span class="invalid-feedback invalid-select" role="alert">
                            <strong>{{ $errors->first('currency') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="col-2">
                    <label class="form-label">Created By:</label>
                    <div class="form-group">
                        <input type="text" class="form-control" name="createdby" autocomplete="off" id="createdby"
                            readonly
                            value="{{ isset($edit) ? (!empty(@$edit->number) ? @$edit->number : old('createdby')) : Auth::user()->full_name }}" />
                    </div>
                    @if ($errors->has('createdby'))
                        <span class="invalid-feedback"
                            role="alert"><strong>{{ $errors->first('createdby') }}</strong></span>
                    @endif
                </div>

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
        </div>
    </div>
    <div class="tab-wrap mb-3">
        <ul class="nav nav-tabs" id="purchaseDetailsTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="extra-fields-tab" data-bs-toggle="tab"
                    data-bs-target="#extra-fields" type="button" role="tab" aria-controls="extra-fields"
                    aria-selected="true">Extra Fields</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="shipping-details-info-tab" data-bs-toggle="tab"
                    data-bs-target="#shipping-details-info" type="button" role="tab"
                    aria-controls="shipping-details-info" aria-selected="false">Shipping
                    Details</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="vat-details-tab" data-bs-toggle="tab" data-bs-target="#vat-details"
                    type="button" role="tab" aria-controls="vat-details" aria-selected="false">VAT
                    Details</button>
            </li>
        </ul>
        <div class="tab-content mb-3" id="purchaseDetailsTabsContent">
            <div class="tab-pane fade show active" id="extra-fields" role="tabpanel" aria-labelledby="extra-fields-tab">
                <div class="row gap-rows">
                    <div class="col-2">
                        <label class="form-label">Delivery Date:</label>

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
                        <div class="form-group">
                            <input type="date" class="form-control" id="delivery_date" name="delivery_date"
                                value="{{ @$value }}" required />
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Payment Terms:*</label>
                        <div class="form-group">
                            <select class="form-control"
                                onchange="this.setAttribute('title', this.options[this.selectedIndex].text)"
                                onmouseover="this.setAttribute('title', this.options[this.selectedIndex].text)" required
                                name="payment_terms" id="payment_terms" onchange="fn_payment_terms()">
                                <option value="">Select</option>
                                @foreach ($paymentterms as $value)
                                    <option value="{{ @$value->id }}" {{ isset($edit) ? (!empty(@$edit->payment_terms) ? (@$edit->payment_terms == @$value->id ? 'selected' : '') : '') : '' }}>
                                        {{ @$value->title }}
                                    </option>
                                @endforeach
                            </select>
                            <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>
                        </div>
                    </div>

                    <div class="col-2" id="div_payment_terms" style="display: none; padding-top: px;">
                        <div class="input-effect">
                            <label class="txtlbl">@lang('Other Payment Terms')<span>*</span></label>
                            <input class="txtbx primary-input form-control" type="text" name="payment_terms2"
                                autocomplete="off" id="payment_terms2" value="{{ @$edit->payment_terms2 }}">
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Customer Reference:</label>
                        <div class="form-group">
                            <input type="text" class="form-control" id="narration" type="text" name="narration" value="STOCK ORDER"
                                required />
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Sales Person Name:</label>
                        <div class="form-group">
                            <select class="form-control js-example-basic-single" required name="sales_person"
                                id="sales_person">
                                <option value=""></option>
                                @foreach ($salesman as $value)
                                    <option value="{{ @$value->user_id }}">{{ @$value->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Contact Person Name:</label>
                        <div class="form-group">
                            <input type="text" class="form-control" id="contact_person_name" type="text"
                                name="contact_person_name" value="" required />
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Contact Person Email:</label>
                        <div class="form-group">
                            <input type="text" class="form-control" id="contact_person_email" type="text"
                                name="contact_person_email" value="" required />
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Contact Person Telephone:</label>
                        <div class="form-group">
                            <input type="text" class="form-control" id="contact_person_telephone" type="text"
                                name="contact_person_telephone" value="" required />
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Internal Transfer:</label>
                        <div class="form-group">
                            <select class="form-control select2" id="internal_transfer" name="internal_transfer"
                                required>
                                <option value="" selected>Select</option>
                                <option value="1">Yes</option>
                                <option value="2">No</option>
                            </select>
                            <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>
                        </div>
                    </div>
                    <div class="col-lg-2 mb-2" id="div_deal_id" style="display: none;">
                        <div class="input-effect">
                            <label class="dynamicslbl">@lang('Deal ID')*</label>
                            <input class="form-control" id="deal_id" type="text" name="deal_id" value="Without Deal"
                                required>
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Create Deal:</label>
                        <div class="form-group">
                            <select class="form-control select2" name="create_deal" id="create_deal" required
                                onchange="create_deal_change()">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                            <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>
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
                    </scrip>

                    <div class="col-2">
                        <label class="form-label">Create Goods Receipt Note:</label>
                        <div class="form-group">
                            <select class="form-control select2" name="create_grn" id="create_grn"
                                onchange="fn_create_grn_pi()">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                            <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>
                        </div>
                    </div>

                    <div class="col-2">
                        <label class="form-label">Create Purchase Invoice:</label>
                        <div class="form-group">
                            <select class="form-control select2" name="create_pi" id="create_pi"
                                onchange="fn_create_grn_pi()">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                            <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>
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

                    <div class="col-2 mb-2 create_grn_pi" style="display: none;">
                        <div class="input-effect">
                            <label class="dynamicslbl">@lang('Bill Number')</label>
                            <input class="form-control" id="bill_number" type="text" name="bill_number">
                        </div>
                    </div>
                    <div class="col-2 mb-2 create_grn_pi" style="display: none;">
                        <div class="input-effect">
                            <label class="dynamicslbl">@lang('Bill Date')</label>
                            <input class="form-control" id="bill_date" type="date" name="bill_date">
                        </div>
                    </div>
                    <div class="col-2 mb-2 create_grn_pi" style="display: none;">
                        <div class="input-effect">
                            <label class="dynamicslbl">@lang('AWB No')</label>
                            <input class="form-control" id="awbno" type="text" name="awbno">
                        </div>
                    </div>
                    <div class="col-2 mb-2 create_grn_pi" style="display: none;">
                        <div class="input-effect">
                            <label class="dynamicslbl">@lang('BOE No')</label>
                            <input class="form-control" id="boeno" type="text" name="boeno">
                        </div>
                    </div>
                    <div class="col-2 mb-2">
                        <div class="input-effect">
                            <label class="dynamicslbl">@lang('Narration')</label>
                            <input class="form-control" id="reference" type="text" name="reference">
                        </div>
                    </div>


                </div>
            </div>
            <div class="tab-pane fade" id="shipping-details-info" role="tabpanel"
                aria-labelledby="shipping-details-info-tab">
                <div class="row gap-rows">
                    <div class="col-3">
                        <label class="form-label">Company Name:</label>
                        <div class="form-group">
                            <select class="form-control js-example-basic-single" name="shipping_supplier"
                                id="shipping_supplier" required style="width: 100%;">
                                <option value=""></option>
                                @foreach ($customer as $value)
                                    @php $s = @App\SysHelper::internal_transfer_customer_id(@$value->id, session('logged_session_data.company_id')); @endphp
                                    <option value="{{ @$value->id }}" {{ $s }}>{{ @$value->account_code }} -
                                        {{ @$value->account_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <script>
                            $(function () { $("#shipping_supplier").change(); });
                        </script>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Contact Name:</label>
                        <div class="form-group">
                            <input type="text" class="form-control" name="shipping_name" id="shipping_name"
                                value="{{ session('logged_session_data.full_name') }}" />
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Email:</label>
                        <div class="form-group">
                            <input type="text" class="form-control" name="shipping_email" id="shipping_email"
                                value="{{ session('logged_session_data.email') }}" />
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Contact No:</label>
                        <div class="form-group">
                            <input type="text" class="form-control" name="shipping_contact_no" id="shipping_contact_no"
                                value="{{ session('logged_session_data.mobile') }}" />
                        </div>
                    </div>
                    <div class="col-3">
                        <label class="form-label">Shipping Address:</label>
                        <div class="form-group">
                            <input type="text" class="form-control" name="shipping_address_1" id="shipping_address_1" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="vat-details" role="tabpanel" aria-labelledby="vat-details-tab">
                <div class="row gap-rows">
                    <div class="col-3">
                        <label class="form-label">Supplier Type:</label>
                        <div class="form-group">
                            <select class="form-control {{ $errors->has('supplier_type') ? ' is-invalid' : '' }}"
                                name="supplier_type" id="supplier_type">
                                <option value="0"></option>
                                @foreach ($suppliertype as $value)
                                    <option value="{{ @$value->id }}" {{ isset($edit) ? (!empty(@$edit->supplier_type) ? (@$edit->supplier_type == @$value->id ? 'selected' : '') : '') : '' }}>
                                        {{ @$value->title }}
                                    </option>
                                @endforeach
                            </select>
                            <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>
                        </div>
                    </div>
                    <div class="col-3">
                        <label class="form-label">Purchase Type:</label>
                        <div class="form-group">
                            <select name="purchase_type" id="purchase_type"
                                class="form-control  {{ $errors->has('purchase_type') ? ' is-invalid' : '' }}"
                                id="inputVendorName">

                                @foreach ($purchasetype as $value)
                                    <option value="{{ @$value->id }}" {{ isset($edit) ? (!empty(@$edit->supplier_type) ? (@$edit->supplier_type == @$value->id ? 'selected' : '') : '') : '' }}>
                                        {{ @$value->title }}
                                    </option>
                                @endforeach
                            </select>
                            <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>
                        </div>
                    </div>
                    <div class="col-3">
                        <label class="form-label">Supplier Country:</label>
                        <div class="form-group">
                            <select class="form-control js-example-basic-single" style="width: 100%;"
                                name="supplier_country" id="country" required>
                                <option data-display="" value=""></option>
                                @foreach ($countries as $key => $value)
                                                                                                                    <option value="{{ @$value->id }}" <?php        try {?> @if (isset($edit)) @if (@$edit->supplier_country == $value->id) selected @endif @endif <?php        } catch (\Throwable $th) {
                                    } ?>>{{ @$value->name }} </option>
                                @endforeach
                            </select>
                            {{-- <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i> --}}
                        </div>
                    </div>
                </div>
            </div>
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

    <div class="table-container" style="border: solid 1px #d9d9d9;">
        <table class="table table-hover form-item-table" id="myTable">
            <thead>
                <tr>
                    <th class="resizable text-center" width="50px">@lang('No')
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="150px">@lang('Part No') <a
                            class="icon icon-outline-book text-dark" data-bs-toggle="modal"
                            data-bs-target="#addproductModal"></a>
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center">@lang('Description')
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="50px">@lang('Tax')
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="50px">@lang('Qty')
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="100px">@lang('Price')
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="100px">@lang('Value')
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="80px" scope="col">Dis <a
                            class="icon icon-outline-book text-dark" data-bs-toggle="modal"
                            data-bs-target="#discountModal"></a>
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="80px" scope="col">Freight <a
                            class="icon icon-outline-book text-dark" data-bs-toggle="modal"
                            data-bs-target="#freightModal"></a>
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="80px" scope="col">Custom <a
                            class="icon icon-outline-book text-dark" data-bs-toggle="modal"
                            data-bs-target="#customModal"></a>
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="100px">@lang('Taxable')
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="100px">@lang('VAT')
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="100px">@lang('Total')
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="100px">@lang('Serial No')
                        <div class="resizer"></div>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="text" class="form-control" id="inputPONumber" value="1" /></td>
                    <td class="noborder">
                        <select class="form-control noborder " name="part_number[]">
                        </select>
                        {{-- on focus add this class and its funcanalities js-product-select --}}
                    </td>
                    <td>
                        <input class="form-control" type="text" name="description[]" autocomplete="off" readonly="true">
                        <input class="form-control" type="text" name="part_number_txt[]" autocomplete="off"
                            readonly="true" hidden>
                        <input class="form-control" type="text" name="hscode_txt[]" autocomplete="off" readonly="true"
                            hidden>
                        <input class="form-control" type="text" name="product_type[]" autocomplete="off" readonly="true"
                            hidden>
                        <input class="form-control" type="text" name="product_type_part_number_text[]"
                            autocomplete="off" readonly="true" hidden>
                    </td>
                    <td><input type="number" class="form-control" name="tax[]" onchange="calc_change_new(this)"></td>
                    <td><input class="form-control" type="number" name="qty[]" autocomplete="off" min="0"
                            onchange="calc_change_new(this)" onkeypress="set_license_key()"></td>
                    <td><input class="form-control" type="number" name="unitprice[]" step="any" autocomplete="off"
                            min="0" onchange="calc_change_new(this)"></td>
                    <td><input class="form-control" type="number" name="value[]" autocomplete="off" min="0" readonly>
                    </td>
                    <td><input class="form-control" type="number" name="discount[]" autocomplete="off" step="0.01"
                            min="0" value="0.00" onchange="calc_change_new(this)"></td>
                    <td><input class="form-control" type="number" name="fright[]" autocomplete="off" step="0.01" min="0"
                            value="0.00" onchange="calc_change_new(this)"></td>
                    <td><input class="form-control" type="number" name="customcharges[]" autocomplete="off" step="0.01"
                            min="0" value="0.00" onchange="calc_change_new(this)"></td>
                    <td><input class="form-control" type="number" name="taxableamount[]" autocomplete="off" step="0.01"
                            min="0" readonly></td>
                    <td><input class="form-control" type="number" name="vatamount[]" autocomplete="off" min="0"
                            readonly></td>
                    <td><input class="form-control" type="number" name="totalamount[]" autocomplete="off" min="0"
                            readonly></td>
                    <td><input class="form-control" type="text" name="serial_no[]"></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" scope="col">Total</th>
                    <th class="text-center"><label id="lbl_total_qty">0</label></th>
                    <th class="text-end" scope="col"><label id="lbl_total_price">0</label></th>
                    <th class="text-end" scope="col"><label id="lbl_total_value">0</label></th>
                    <th class="text-end" scope="col"><label id="lbl_total_discount">0</label></th>
                    <th class="text-end" scope="col"><label id="lbl_total_fright">0</label></th>
                    <th class="text-end" scope="col"><label id="lbl_total_customcharges">0</label></th>
                    <th class="text-end" scope="col"><label id="lbl_total_taxableamount">0</label></th>
                    <th class="text-end" scope="col"><label id="lbl_total_vatamount">0</label></th>
                    <th class="text-end" scope="col"><label id="lbl_total_totalamount">0</label></th>
                    <th class="text-end" scope="col"></th>
                </tr>
            </tfoot>
        </table>
        <div id="contextMenu">
            <button type="button" id="addRow">Add Row</button>
            <button type="button" id="deleteRow">Delete Row</button>
        </div>
    </div>
</div>

{{ Form::close() }}










{{-- Models --}}
<!-- <a data-bs-toggle="modal" data-bs-target="#editModal"></a> -->

@include('backEnd.inventory.itemAddModal')




<div class="modal side-panel fade" id="discountModal" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" style="height: 155px !important; width:170px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editModalLabel">Add Discount</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-2">
                                <label class="form-label">Discount Amount:</label>
                                <div class="form-group">
                                    <input type="text" class="form-control" id="discountInput" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light add-btn ms-2" id="discount_add_btn">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Split Discount
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal side-panel fade" id="freightModal" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" style="height: 155px !important; width:170px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editModalLabel">Add Freight</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-2">
                                <label class="form-label">Freight Amount:</label>
                                <div class="form-group">
                                    <input type="text" class="form-control" id="freightInput" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light add-btn ms-2" id="freight_add_btn">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Split Freight
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal side-panel fade" id="customModal" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" style="height: 155px !important; width:170px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editModalLabel">Add Custom</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-2">
                                <label class="form-label">Custom Charges:</label>
                                <div class="form-group">
                                    <input type="text" class="form-control" id="customCharges" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light add-btn ms-2" id="custom_add_btn">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Split Custom
                </button>
            </div>
        </div>
    </div>
</div>


<div class="modal side-panel fade" id="serialNoModal" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" style="height: 155px !important; width:170px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editModalLabel">Serial No</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-2">
                                <label class="form-label">Serial No:</label>
                                <div class="form-group">
                                    <input type="text" class="form-control" id="add_serial_no" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light add-btn ms-2" onclick="addSerialNo()">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Add
                </button>
            </div>
        </div>
    </div>
</div>
{{-- Models --}}


<div class="modal  fade" id="addpoexcelimport" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" style="height: 464px !important;">

        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'add-purchase-order-items-excel-cart', 'method' => 'POST', 'id' => 'add-purchase-order-items-excel-cart']) }}

        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="poexcelimport">PO Items Excel Import</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">

                    <div class="card-body">
                        <div class="row">
                            <div class="col-auto">
                                <label for="excel-file" class="form-label mb-0">Select File (.csv)</label>
                            </div>

                            <div class="col-auto">
                                <input class="form-control" type="file" id="excel-file" accept=".xlsx, .xls, .csv">
                            </div>

                            <div class="col-auto">
                                <button type="button" onclick="readExcel()" class="btn btn-success">Preview</button>
                            </div>

                            <div class="col-auto">
                                <small>(<a href="{{ url('public/uploads/product_upload/po_items_sample_format.csv') }}"
                                        target="_blank">Sample File</a>)</small>
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

                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>


                            </div>

                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="saveExcelItems()" class="btn btn-light add-btn ms-2">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                </button>
            </div>
        </div>
        {{ Form::close() }}


    </div>
</div>

<div class="modal  fade" id="attachment_popup_win" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="height: 464px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="poexcelimport">Attachments - <label id="att_cust_name"></label></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">

                    <div class="card-body">
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
                                <table id="att-table" class="table table-hover" width="100%" cellspacing="0">
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


                    </div>

                </div>
            </div>
            <div class="modal-footer">

                <input type="hidden" id="srl_id" />

                <button type="button" onclick="add_attachment()" class="btn btn-light add-btn ms-2">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>




<script>
    function splitAmount(modalInputId, targetFieldName) {
        const amount = parseFloat(document.getElementById(modalInputId).value);
        if (isNaN(amount) || amount <= 0) {
            alert("Please enter a valid amount.");
            return;
        }

        const valueFields = document.querySelectorAll('input[name="value[]"]');
        const targetFields = document.querySelectorAll(`input[name="${targetFieldName}[]"]`);

        let totalValue = 0;
        let validRows = [];

        valueFields.forEach((input, index) => {
            const val = parseFloat(input.value);
            if (!isNaN(val) && val > 0) {
                totalValue += val;
                validRows.push({ index, input });
            }
        });

        if (totalValue === 0) {
            alert("All rows have empty or zero 'Value'. Nothing to split.");
            return;
        }

        validRows.forEach(({ index, input }) => {
            const rowVal = parseFloat(input.value);
            const share = (rowVal / totalValue) * amount;

            const targetInput = targetFields[index];
            targetInput.value = share.toFixed(2);

            const row = targetInput.closest('tr');
            calc_change_new(row);
        });

        if (typeof update_totals === 'function') {
            update_totals();
        }
    }

    document.getElementById("discount_add_btn").addEventListener("click", function () {
        splitAmount('discountInput', 'discount');
        $('#discountModal').modal('hide');
    });

    document.getElementById("freight_add_btn").addEventListener("click", function () {
        splitAmount('freightInput', 'fright');
        $('#freightModal').modal('hide');
    });

    document.getElementById("custom_add_btn").addEventListener("click", function () {
        splitAmount('customCharges', 'customcharges');
        $('#customModal').modal('hide');
    });
</script>

<script>
    let serialNoModal;
    document.addEventListener("DOMContentLoaded", function () {
        const modalElement = document.getElementById('serialNoModal');
        serialNoModal = new bootstrap.Modal(modalElement);
    });
    let currentSerialInput = null;

    $(document).on('click', 'input[name="serial_no[]"]', function () {
        currentSerialInput = $(this);
        $('#add_serial_no').val(currentSerialInput.val());
        serialNoModal.show();
    });
    function addSerialNo() {
        if (currentSerialInput) {
            const val = $('#add_serial_no').val();
            currentSerialInput.val(val);
            serialNoModal.hide();
            currentSerialInput = null;
        }
    }
</script>

<script>


    function calc_change_new(el) {
        $("#loading_bg").css("display", "block");

        // Get the current row
        var $row = $(el).closest('tr');

        // Read values from the current row
        var net_vat = $row.find('input[name="tax[]"]').val() || '0';

        var qty = $row.find('input[name="qty[]"]').val() || '0';
        var unitprice = $row.find('input[name="unitprice[]"]').val() || '0';
        var discount = $row.find('input[name="discount[]"]').val() || '0';
        var fright = $row.find('input[name="fright[]"]').val() || '0';
        var customcharges = $row.find('input[name="customcharges[]"]').val() || '0';

        var decimal_point = @json(session('logged_session_data.decimal_point'));

        // Calculate value
        var fin_value = parseFloat(unitprice) * parseFloat(qty);
        $row.find('input[name="value[]"]').val(fin_value.toFixed(decimal_point));

        // Calculate taxable amount
        var fin_taxableamount = fin_value + parseFloat(customcharges) + parseFloat(fright) - parseFloat(discount);
        $row.find('input[name="taxableamount[]"]').val(fin_taxableamount.toFixed(decimal_point));

        // Calculate VAT
        var fin_vatamount = fin_taxableamount * (parseFloat(net_vat) / 100);
        $row.find('input[name="vatamount[]"]').val(fin_vatamount.toFixed(decimal_point));

        // Calculate total amount
        var total_amount = fin_taxableamount + fin_vatamount;
        $row.find('input[name="totalamount[]"]').val(total_amount.toFixed(decimal_point));

        $("#loading_bg").css("display", "none");
        update_totals();
    }
    function update_totals() {
        let total_qty = 0,
            total_price = 0,
            total_value = 0,
            total_discount = 0,
            total_fright = 0,
            total_customcharges = 0,
            total_taxableamount = 0,
            total_vatamount = 0,
            total_totalamount = 0;

        const decimal_point = @json(session('logged_session_data.decimal_point'));

        $('#myTable tbody tr').each(function () {
            const $row = $(this);

            total_qty += parseFloat($row.find('input[name="qty[]"]').val()) || 0;
            total_price += parseFloat($row.find('input[name="unitprice[]"]').val()) || 0;
            total_value += parseFloat($row.find('input[name="value[]"]').val()) || 0;
            total_discount += parseFloat($row.find('input[name="discount[]"]').val()) || 0;
            total_fright += parseFloat($row.find('input[name="fright[]"]').val()) || 0;
            total_customcharges += parseFloat($row.find('input[name="customcharges[]"]').val()) || 0;
            total_taxableamount += parseFloat($row.find('input[name="taxableamount[]"]').val()) || 0;
            total_vatamount += parseFloat($row.find('input[name="vatamount[]"]').val()) || 0;
            total_totalamount += parseFloat($row.find('input[name="totalamount[]"]').val()) || 0;
        });

        $('#lbl_total_qty').text(total_qty.toFixed(decimal_point));
        $('#lbl_total_price').text(total_price.toFixed(decimal_point));
        $('#lbl_total_value').text(total_value.toFixed(decimal_point));
        $('#lbl_total_discount').text(total_discount.toFixed(decimal_point));
        $('#lbl_total_fright').text(total_fright.toFixed(decimal_point));
        $('#lbl_total_customcharges').text(total_customcharges.toFixed(decimal_point));
        $('#lbl_total_taxableamount').text(total_taxableamount.toFixed(decimal_point));
        $('#lbl_total_vatamount').text(total_vatamount.toFixed(decimal_point));
        $('#lbl_total_totalamount').text(total_totalamount.toFixed(decimal_point));
    }
</script>
<script>

    $(document).on('focus', 'select[name="part_number[]"]', function () {
        const $select = $(this);

        // Add the class if not present
        if (!$select.hasClass('js-product-select')) {
            $select.addClass('js-product-select');
            //$select.remove('select2-hidden-accessible');

            // Initialize Select2
            initAccountSelect2(this); // your existing function
        }
    });




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
                                    description: item.description,
                                    hscode: item.hscode,
                                    product_type: item.product_type
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
                var $row = $(this).closest('tr'); // find the closest row

                // Set values using "name" attribute selectors inside the same row
                $row.find('input[name="description[]"]').val(selectedData.description || '');
                $row.find('input[name="part_number_txt[]"]').val(selectedData.text || '');
                $row.find('input[name="hscode_txt[]"]').val(selectedData.hscode || '');
                $row.find('input[name="product_type[]"]').val(selectedData.product_type || '');
                $row.find('input[name="product_type_part_number_text[]"]').val(selectedData.description || '');
                $row.find('input[name="discount[]"]').val(0);
                $row.find('input[name="fright[]"]').val(0);
                $row.find('input[name="customcharges[]"]').val(0);
                $row.find('input[name="tax[]"]').val($('#net_vat').val());
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
    /*table row fill based on layout height*/
    window.onload = function () {
        const table = document.getElementById('myTable');
        const tbody = table.querySelector('tbody');

        // If there are no rows, do nothing
        if (tbody.rows.length === 0) return;

        const rowHeight = tbody.rows[0].offsetHeight;
        const pageHeight = window.innerHeight - 65;
        const tableTop = table.getBoundingClientRect().top;
        const availableHeight = pageHeight - tableTop;

        let existingRows = tbody.rows.length;
        let totalRows = Math.floor(availableHeight / rowHeight);

        const lastRow = tbody.rows[tbody.rows.length - 1];

        for (let i = existingRows + 1; i <= totalRows; i++) {
            const newRow = lastRow.cloneNode(true); // clone entire row

            const firstCellInput = newRow.cells[0].querySelector('input');
            if (firstCellInput) {
                firstCellInput.value = i;
            }
            const inputs = newRow.querySelectorAll('input');
            inputs.forEach((input, index) => {
                if (index !== 0) input.value = "";
            });

            tbody.appendChild(newRow);
        }
    };
    /*table row fill based on layout height*/
</script>


<script>
    $(document).ready(function () {
        $('#add-btn-modal').on('click', function (e) {
            e.preventDefault();

            var formData = $('#productForm').serialize();

            $.ajax({
                url: "{{ route('product.modalsave') }}", // Update with your route name
                type: "POST",
                data: formData,
                success: function (response) {
                    if (response.success) {
                        alert('Product saved successfully.');
                        $('#addproductModal').modal('hide'); // optional
                        // Optionally reload table or clear form
                    } else {
                        alert('Something went wrong.');
                    }
                },
                error: function (xhr) {
                    console.log(xhr.responseText);
                    alert('An error occurred. Please check console.');
                }
            });
        });


        $(document).on("change", "#vendors", function () {
            var id = $("#vendors").val();
            get_vendors_detail(id);
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
                success: function (dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    if (dataResult['data'] == "ERROR") {
                        alert("Error found in something!!");
                        $("#loading_bg").css("display", "none");
                    } else {
                        $("#net_vat").val(dataResult['data'].vat_percentage);
                        $("#loading_bg").css("display", "none");
                    }
                }
            });
        }
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
                    console.log(dataResult);
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

                            $("#country").val(dataResult['data'][i].vat_country).trigger('change');
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







    });




</script>


<script>
    function fn_payment_terms() {
        var val_payment_terms = $('#payment_terms').val();
        if (val_payment_terms == 22) {
            $('#div_payment_terms').css('display', 'block');
        } else {
            $('#div_payment_terms').css('display', 'none');
        }
    }
    $('#payment_terms').change();
</script>


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
                                    <td><button onclick='delete_attachment("+ dataResult['data'][i].id + ")' class='btn btn-sm btn-danger'><i class='ico icon-bold-trash-bin-2' style='font-size:16px' aria-hidden='true'></i></button></td>\
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
                                    <td><button onclick='delete_attachment("+ dataResult['data'][i].id + ")' class='btn btn-sm btn-danger'><i class='ico icon-bold-trash-bin-2' style='font-size:16px' aria-hidden='true'></i></button></td>\
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
                                    <td><button onclick='delete_attachment("+ dataResult['data'][i].id + ")' class='btn btn-sm btn-danger'><i class='ico icon-bold-trash-bin-2' style='font-size:16px' aria-hidden='true'></i></button></td>\
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

    function get_format_date(date) {
        if (date == null) {
            return "--";
        }
        const dateStr = date;
        const dateObj = new Date(dateStr);

        // Get day, month, and year
        const day = String(dateObj.getDate()).padStart(2, '0'); // Ensure 2 digits
        const month = String(dateObj.getMonth() + 1).padStart(2, '0'); // Month is 0-based
        const year = dateObj.getFullYear();

        // Format as "dd/mm/yyyy"
        const formattedDate = `${day}/${month}/${year}`;
        return formattedDate;
    }
</script>


<?php
    $part_number = $items->pluck('part_number');
                            ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>
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
                deleteButton.type = 'button';  // Prevent form submission
                deleteButton.classList.add('btn-sm', 'btn-danger');
                deleteButton.innerHTML = '<i class="ico icon-bold-trash-bin-2" style="font-size: 16px;"></i>';
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


<?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
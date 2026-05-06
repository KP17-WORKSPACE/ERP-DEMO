    <?php try { ?>

    @if (isset($select_cart))
        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'delivery-note-store', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'delivery-note-create-form', 'novalidate' => true, 'novalidate' => true]) }}
        <input type="hidden" name="store_id" value="cart" />
    @else
        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'delivery-note-store', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'delivery-note-create-form', 'novalidate' => true]) }}
        <input type="hidden" name="store_id" value="sales" />
    @endif
    <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
    <input type="hidden" name="id" value="{{ isset($edit) ? $edit->id : '' }}">
    <input type="hidden" id="net_vat" name="net_vat">




    <div class="purchase-order-content-header">
        <h4 class="purchase-order-content-header-left">
            New
            ({{ isset($edit) ? (!empty(@$edit->title) ? @$edit->title : old('doc_number')) : @App\SysHelper::get_new_code('sys_delivery_note', 'DN', 'doc_number') }})
        </h4>
        <div class="purchase-order-content-header-right">
            <button type="submit" class="btn btn-light">
                <i class="ico icon-outline-bookmark-opened text-success"></i> Save
            </button>
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ico icon-outline-hamburger-menu"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><button class="dropdown-item"><i class="ico icon-outline-document-medicine text-success"></i>
                            Save & Download</button></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div class="row gap-rows">
                <div class="col-4">
                    <label class="form-label">Customer</label>
                    <div class="form-group">
                        <select class="form-control js-account-select" name="customer_id" id="customer_id" required
                            onchange="get_pending_si_list()">
                            @if (isset($deal_acc))
                                <option value="{{ $deal_acc->id }}">
                                    @if (@App\SysHelper::getCompanyCodeSettings()['is_customer_code'])
                                        {{ $deal_acc->account_name }} ({{ $deal_acc->account_code }})
                                    @else
                                        {{ $deal_acc->account_name }}
                                    @endif

                                </option>
                            @endif
                            <option value=""></option>
                            {{-- @foreach ($vendors as $value)
                                                <option value="{{ @$value->id }}" {{ isset($edit) ? (!empty($edit->vendor_id) ? (@$edit->vendor_id == @$value->id ? 'selected' : '') : '') : '' }}>
                                                    {{ @$value->account_name }}
                                                </option>
                                                @endforeach --}}
                        </select>
                    </div>
                </div>
                <div class="col-2">
                    <label class="form-label">DLN Number</label>
                    <div class="form-group">
                        <input class="form-control" type="text" name="doc_number" autocomplete="off" id="doc_number"
                            value="{{ isset($edit) ? (!empty(@$edit->title) ? @$edit->title : old('doc_number')) : @App\SysHelper::get_new_code('sys_delivery_note', 'DN', 'doc_number') }}"
                            readonly>
                    </div>
                </div>
                <div class="col-2">
                    <label class="form-label">DLN Date</label>
                    <div class="form-group">
                        @php
                            $value = date('d/m/Y');
                        @endphp
                        <input class="form-control date-picker" id="doc_date" type="text" autocomplete="off"
                            name="doc_date" value="{{ @$value }}" required>
                    </div>
                </div>
                <div class="col-2">
                    <label class="form-label">Currency</label>
                    <div class="form-group">
                        <select class="form-control" name="currency" id="currency">
                            @foreach ($currency as $value)
                                <option value="{{ @$value->id }}" @if ($company->currency_id == $value->id) selected @endif>
                                    {{ @$value->code }}
                                </option>
                            @endforeach
                        </select>
                        <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>

                    </div>
                </div>
                <div class="col-2">
                    <label class="form-label">Created By</label>
                    <div class="form-group">
                        <input class="form-control" type="text" name="createdby" autocomplete="off" id="created_by"
                            value="{{ Auth::user()->full_name }}" readonly>
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
                <button class="nav-link" id="shipping-details-tab" data-bs-toggle="tab"
                    data-bs-target="#shipping-details" type="button" role="tab" aria-controls="shipping-details"
                    aria-selected="true">Shipping Details</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="vat-details-tab" data-bs-toggle="tab" data-bs-target="#vat-details"
                    type="button" role="tab" aria-controls="vat-details" aria-selected="true">VAT
                    Details</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="end-user-details-tab" data-bs-toggle="tab"
                    data-bs-target="#end-user-details" type="button" role="tab"
                    aria-controls="end-user-details" aria-selected="true">End User Details</button>
            </li>
        </ul>
        <div class="tab-content mb-3" id="purchaseDetailsTabsContent">
            <div class="tab-pane fade show active" id="extra-fields" role="tabpanel"
                aria-labelledby="extra-fields-tab">
                <div class="row gap-rows">


                    <div class="col-2 mb-2">
                        <div class="input-effect">
                            <label class="txtlbl">Pending list</label>
                            <div id="plist"
                                style="width: 100%; height: 130px; border: solid 1px #dfe1d7; border-radius: 5px; padding: 5px 5px; overflow-y: scroll;">
                            </div>
                            <a id="addDNPending"></a>
                            <input type="hidden" id="si_id" name="si_id" value="0">
                            <input type="hidden" id="hd_pending_dn_id" name="hd_pending_dn_id" value="0">
                            <input type="hidden" id="vat_percentage" name="vat_percentage" value="5">
                        </div>
                    </div>
                    <div class="col-lg-10 mb-2">
                        <div class="row">
                            <div class="col-lg-3 mb-2">
                                <div class="input-effect">
                                    <label class="form-label"> @lang('SIV No') <span>*</span> </label>
                                    @php
                                        $invoice_no = '';
                                        $invoice_date = date('d/m/Y'); // default today in d/m/Y

                                        if (isset($siv_det)) {
                                            $invoice_no = $siv_det->doc_number;
                                            $invoice_date = date('d/m/Y', strtotime($siv_det->doc_date));
                                        }

                                        if (isset($si_no) && !empty($si_no)) {
                                            $invoice_no = $si_no;
                                        }

                                        if (isset($si_date) && !empty($si_date)) {
                                            $invoice_date = date('d/m/Y', strtotime($si_date));
                                        }
                                    @endphp

                                    <input class="form-control" type="text" id="invoice_no" name="invoice_no"
                                        value="{{ $invoice_no }}">
                                </div>
                            </div>
                            <div class="col-lg-3 mb-2">
                                <div class="input-effect">
                                    <label class="form-label">@lang('SIV Date')</label>
                                    <input class="form-control date-picker" id="invoice_date" type="text"
                                        name="invoice_date" value="{{ @$invoice_date }}">
                                </div>
                            </div>
                            <div class="col-lg-3 mb-2">
                                <div class="input-effect">
                                    <label class="form-label"> @lang('LPO No') <span>*</span> </label>
                                    @php
                                        $lpoValue = old('lpo_no');

                                        if (!empty($lpo_no)) {
                                            $lpoValue = $lpo_no;
                                        } elseif (!empty($select_cart[0] ?? null) && !empty($select_cart[0]->reference_no ?? null)) {
                                            $lpoValue = $select_cart[0]->reference_no;
                                        }
                                    @endphp
                                    <input class="form-control" type="text" id="lpo_no" name="lpo_no"
                                        value="{{ $lpoValue }}" required>
                                </div>
                            </div>
                            <div class="col-lg-3 mb-2">
                                <div class="input-effect">
                                    <label class="form-label">@lang('LPO Date')</label>
                                    @php
                                        $value = date('d/m/Y'); // default today in dmy

                                        if (!empty($select_cart[0] ?? null) && !empty($select_cart[0]->reference_date)) {
                                            $value = date('d/m/Y', strtotime($select_cart[0]->reference_date));
                                        }

                                        if (isset($lpo_date) && !empty($lpo_date)) {
                                            $value = date('d/m/Y', strtotime($lpo_date));
                                        }
                                    @endphp

                                    <input class="form-control date-picker" id="lpo_date" type="text"
                                        name="lpo_date" value="{{ $value }}" required>
                                </div>
                            </div>
                            <div class="col-lg-3 mb-2">
                                <div class="input-effect">
                                    <label class="form-label">@lang('Payment Terms') <span>*</span></label>
                                    <div class="form-group">
                                        <select class="form-control js-example-basic-single" name="payment_terms"
                                            id="payment_terms" required>
                                            <option value=""></option>
                                            @foreach ($paymentterms as $value)
                                                <option value="{{ @$value->id }}"
                                                    {{ !empty($select_cart[0] ?? null) ? (!empty(@$select_cart[0]->payment_terms) ? (@$select_cart[0]->payment_terms == @$value->id ? 'selected' : '') : '') : '' }}
                                                    {{ isset($payment_terms) ? (!empty(@$payment_terms) ? (@$payment_terms == @$value->id ? 'selected' : '') : '') : '' }}>
                                                    {{ @$value->title }}</option>
                                            @endforeach
                                        </select>


                                    </div>

                                </div>
                            </div>

                            <div class="col-lg-3 mb-2">
                                <div class="input-effect">
                                    <label class="form-label">@lang('Sales Person Name')<span>*</span></label>
                                    <div class="form-group">
                                        <select class="form-control js-example-basic-single" name="sales_man"
                                            id="sales_man" required>
                                            <option value=""></option>
                                            @foreach ($staff as $value)
                                                <option value="{{ @$value->user_id }}" <?php
                                                            if (@$sales_man == $value->user_id) {
                                                                ?> selected
                                                    <?php
                                                            } elseif (isset($deal_det) && $deal_det->owner == $value->user_id) {
                                                                ?> selected <?php
                                                            } /*elseif ($value->user_id == Auth::id()) {
                                                                ?> selected }*/ ?>
                                                    >{{ @$value->full_name }}
                                                </option>
                                            @endforeach
                                        </select>

                                    </div>

                                </div>
                            </div>
                            <div class="col-lg-3 mb-2">
                                <div class="input-effect">
                                    <label class="form-label"> @lang('Warehouse') <span>*</span> </label>
                                    <input class="form-control" type="text" id="warehouse" name="warehouse"
                                        value="{{ isset($editData) ? (!empty(@$editData->warehouse) ? @$edit->warehouse : old('warehouse')) : 'Taken from stock' }}"
                                        required>
                                </div>
                            </div>
                            <script>
                                $('#warehouse').val($('#main_company_id  option:selected').text());
                            </script>

                            <div class="col-lg-3 mb-2">
                                <div class="input-effect">
                                    <label class="form-label"> @lang('Driver') <span></span> </label>
                                    <input class="form-control" type="text" id="driver" name="driver"
                                        value="{{ isset($editData) ? (!empty(@$editData->driver) ? @$edit->driver : old('driver')) : 'Salman' }}">
                                </div>
                            </div>
                            <div class="col-lg-3 mb-2">
                                <div class="input-effect">
                                    <label class="form-label"> @lang('Vehicle No') <span>*</span> </label>
                                    <input class="form-control" type="text" id="vehicleno" name="vehicleno"
                                        value="{{ isset($editData) ? (!empty(@$editData->vehicleno) ? @$edit->vehicleno : old('vehicleno')) : '' }}">
                                </div>
                            </div>
                            <div class="col-lg-3 mb-2">
                                <div class="input-effect">
                                    @php
                                        $supplier_name = '';
                                        if (isset($supp_name) && !empty($supp_name)) {
                                            $supplier_name = $supp_name;
                                        }
                                        if (isset($sup_name) && !empty(@$sup_name->supplier_name)) {
                                            $supplier_name = $sup_name->supplier_name;
                                        }
                                    @endphp
                                    <label class="form-label"> @lang('Supplier Name') <span>*</span> </label>
                                    <input class="form-control" type="text" id="supplier_name"
                                        name="supplier_name"
                                        value="@if ($supplier_name == '') Taken from stock @else {{ $supplier_name }} @endif"
                                        required>
                                </div>
                            </div>
                            <div class="col-lg-3 mb-2">
                                <div class="input-effect">
                                    <label class="form-label"> @lang('Deal Id') <span>*</span> </label>
                                    <?php
                                    $dealid = 0;
                                    if (isset($deal_id)) {
                                        $dealid = @App\SysHelper::get_code_from_dealid($deal_id);
                                    } else {
                                        if (!empty($select_cart[0] ?? null)) {
                                            $dealid = @App\SysHelper::get_code_from_dealid($select_cart[0]->deal_id);
                                        }
                                    }
                                    ?>
                                    <input class="form-control" type="text" id="deal_id" name="deal_id"
                                        value="{{ $dealid }}" required>
                                </div>
                            </div>
                            <div class="col-lg-3 mb-2">
                                <div class="input-effect">
                                    <label class="form-label">@lang('Narration') <span>*</span></label>
                                    <input class="form-control" data-bs-toggle="modal"
                                        data-bs-target="#narrationModal" type="text" name="narration"
                                        autocomplete="off"
                                        value="{{ isset($editData) ? (!empty(@$editData->narration) ? @$editData->narration : old('narration')) : old('narration') }}"
                                        id="narration">
                                </div>
                            </div>
                        </div>
                    </div>



                </div>
            </div>
            <div class="tab-pane fade show" id="shipping-details" role="tabpanel"
                aria-labelledby="shipping-details-tab">
                <div class="row gap-rows">

                    <div class="col-3">
                        @php
                            $customer = @App\SysHelper::get_customer_supplier_list($company_id);

                        @endphp
                        <label class="form-label">Company (Ship To)</label>
                        <div class="form-group">
                            <select class="form-control js-example-basic-single" name="shipping_supplier"
                                id="shipping_supplier" required style="width: 100%;">
                                <option value=""></option>
                                @foreach ($customer as $value)
                                    {{-- @php $s = @App\SysHelper::internal_transfer_customer_id(@$value->id, session('logged_session_data.company_id')); @endphp --}}

                                    <option value="{{ @$value->id }}"
                                        @if (@$deal_det->delivery_company == $value->id) selected @endif>
                                        {{ @$value->account_name }}
                                        @if (@App\SysHelper::getCompanyCodeSettings()['is_customer_code'])
                                            ({{ @$value->account_code }})
                                        @endif

                                    </option>
                                @endforeach
                            </select>




                        </div>
                        <script>
                            $(document).ready(function() {
                                setTimeout(function() {
                                    $("#shipping_supplier").trigger("change");
                                }, 300);
                            });
                        </script>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Contact Name</label>
                        <div class="form-group">
                            <input type="text" class="form-control" name="shipping_name" id="shipping_name"
                                value="{{ @$deal_det->delivery_name }}" />
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Contact Email</label>
                        <div class="form-group">
                            <input type="text" class="form-control" name="shipping_email" id="shipping_email"
                                value="{{ @$deal_det->delivery_email }}" />
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Contact No</label>
                        <div class="form-group">
                            <input type="text" class="form-control" name="shipping_contact_no"
                                id="shipping_contact_no" value="{{ @$deal_det->delivery_number }}" />
                        </div>
                    </div>
                    @php

                        if (isset($deal_det)) {
                            @$state = \App\SysStates::find(@$deal_det->delivery_state);
                            @$country = \App\SysCountries::find(@$deal_det->delivery_country);

                            $shipping_address = collect([
                                @$deal_det->delivery_flat_office_no ?? null,
                                @$deal_det->delivery_building ?? null,
                                @$deal_det->delivery_area ?? null,
                                @$deal_det->delivery_city ?? null,
                                optional(@$state)->name,
                                optional(@$country)->name,
                            ])
                                ->filter()
                                ->implode(', ');
                        } else {
                            $shipping_address = '';
                        }
                    @endphp
                    <div class="col-3">
                        <label class="form-label">Shipping Address</label>
                        <div class="form-group">
                            <input type="text" class="form-control" name="shipping_address_1"
                                id="shipping_address_1" value="{{ @$shipping_address }}" />
                        </div>
                    </div>

                    <!-- <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="form-label">@lang('Name') <span></span></label>
                                        <input type="text" class="form-control" value="@if (isset($deal_det)) {{ $deal_det->delivery_company }} @endif" id="shipping_name" name="shipping_name">
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="form-label">@lang('Address') <span></span></label>
                                        <input type="text" class="form-control" value="@if (isset($deal_det)) {{ $deal_det->delivery_address }} @endif" id="shipping_address" name="shipping_address">
                                    </div>
                                </div> -->
                </div>
            </div>
            <div class="tab-pane fade show" id="vat-details" role="tabpanel" aria-labelledby="vat-details-tab">
                <div class="row gap-rows">

                    <div class="col-lg-2 mb-2">
                        <div class="input-effect">
                            <label class="form-label">@lang('Customer Country') <span></span></label>
                            <select class="form-control js-example-basic-single" name="customer_country"
                                id="country">
                                <option value=""></option>
                                @foreach ($countries as $key => $value)
                                    <option value="{{ @$value->id }}" <?php try{?>
                                        @if (isset($deal_cust)) @if (@$deal_cust->vat_country == $value->id) selected @endif
                                        @endif
                                        <?php } catch (\Throwable $th) {} ?>
                                        >{{ @$value->name }} </option>
                                @endforeach
                            </select>

                        </div>
                    </div>


                    <div class="col-lg-2 mb-2">
                        <div class="input-effect">
                            <label class="form-label">@lang('Customer State')</label>

                            <div id="sectionStateDiv">
                                <select class="form-control js-example-basic-single" name="customer_state"
                                    id="state">
                                    <option value=""></option>

                                    @foreach ($states as $state)
                                        <option value="{{ $state->id }}"
                                            @if (isset($deal_cust) && $deal_cust->vat_state == $state->id) selected @endif>
                                            {{ $state->name }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>

                        </div>
                    </div>


                    <div class="col-2">
                        <label class="form-label">VAT %</label>
                        <div class="form-group">

                            <input class="form-control" type="number" name="vat_percent" id="vat_percent"
                                value="">
                        </div>
                    </div>

                    <div class="col-2">
                        <label class="form-label">VAT Number</label>
                        <div class="form-group">

                            <input class="form-control" type="number" name="vat_number" id="vat_number"
                                value="">
                        </div>
                    </div>

                    <div class="col-lg-2 mb-2">
                        <div class="input-effect">
                            <label class="form-label">@lang('Customer Type')</label>
                            <div class="form-group">
                                <select class="form-control" name="customer_type" id="customer_type">
                                    <option value="0"></option>
                                    @foreach ($customertype as $value)
                                        <option value="{{ @$value->id }}"
                                            {{ isset($deal_cust) ? (!empty(@$deal_cust->customer_type) ? (@$deal_cust->customer_type == @$value->id ? 'selected' : '') : '') : '' }}>
                                            {{ @$value->title }}</option>
                                    @endforeach
                                </select>
                                <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>

                            </div>

                        </div>
                    </div>
                    <div class="col-lg-2 mb-2">
                        <div class="input-effect">
                            <label class="form-label">@lang('Sale Type')</label>
                            <div class="form-group">
                                <select class="form-control" name="sale_type" id="sale_type">
                                    <option value="0"></option>
                                    @foreach ($saletype as $value)
                                        <option value="{{ @$value->id }}"
                                            {{ isset($deal_cust) ? (!empty(@$deal_cust->sale_type) ? (@$deal_cust->sale_type == @$value->id ? 'selected' : '') : '') : '' }}>
                                            {{ @$value->title }}</option>
                                    @endforeach
                                </select>
                                <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>

                            </div>

                        </div>
                    </div>


                </div>
            </div>
            <div class="tab-pane fade show" id="end-user-details" role="tabpanel"
                aria-labelledby="end-user-details-tab">
                <div class="row gap-rows">
                    <div class="col mb-2">
                        <div class="input-effect">
                            <label class="form-label">@lang('End User Name') <span></span></label>
                            <input type="text" class="form-control" name="end_user_name" id="end_user_name"
                                autocomplete="off"
                                value="@if (isset($deal_enduser)) {{ $deal_enduser->end_user_company_name }} @endif" />

                        </div>
                    </div>
                    <div class="col-lg-3 mb-2">
                        <div class="input-effect">
                            <label class="form-label">@lang('Contact Person Name') <span></span></label>
                            <input type="text" class="form-control" name="contact_person_name"
                                id="contact_person_name" autocomplete="off"
                                value="@if (isset($deal_enduser)) {{ $deal_enduser->end_user_contact_person }} @endif">

                        </div>
                    </div>
                    <div class="col mb-2">
                        <div class="input-effect">
                            <label class="form-label">@lang('Contact Person Email') <span></span></label>
                            <input type="text" class="form-control" name="contact_person_email"
                                id="contact_person_email" autocomplete="off"
                                value="@if (isset($deal_enduser)) {{ $deal_enduser->email }} @endif">

                        </div>
                    </div>
                    <div class="col mb-2">
                        <div class="input-effect">
                            <label class="form-label">@lang('Contact Person No') <span></span></label>
                            <input type="text" class="form-control" name="contact_person_no"
                                id="contact_person_no" autocomplete="off"
                                value="@if (isset($deal_enduser)) {{ $deal_enduser->mobile_no }} @endif">

                        </div>
                    </div>
                    <div class="col mb-2" id="device_serial_container" style="display:none;">
                        <div class="mb-3">
                            <label class="form-label">Device Serial</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="device_serial" id="device_serial"
                                    readonly style="cursor:pointer;" placeholder="Click to enter serial numbers..." />
                                <button type="button" class="btn btn-light border" id="device_serial_btn_modal">
                                    <i class="ico icon-outline-list-down"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="table-container" style="border: solid 1px #d9d9d9;">
        <table class="table table-hover form-item-table" id="myTable">
            <thead>
                <tr>
                    <th class="resizable text-center" width="30px">@lang('No')<div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="210px">@lang('Part No') <a
                            class="icon icon-outline-book text-dark" data-bs-toggle="modal"
                            data-bs-target="#addproductModal"></a>
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="280px">@lang('Description')<div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="30px">@lang('Tax')<div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="30px">@lang('Qty')<div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="80px">@lang('Price')<div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="80px">@lang('Value')<div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="80px" scope="col">Dis <a
                            class="icon icon-outline-book text-dark" data-bs-toggle="modal"
                            data-bs-target="#discountModal"></a>
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="80px">@lang('Taxable')<div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="80px">@lang('VAT')<div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="100px">@lang('Total')<div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="100px">@lang('SRL No')<div class="resizer"></div>
                    </th>
                </tr>
            </thead>
            <tbody>
                @php
                    $qty_total = 0;
                    $value_total = 0;
                    $discount_total = 0;
                    $taxableamount_total = 0;
                    $vatamount_total = 0;
                    $total_amount = 0;
                @endphp
                @if (count($select_cart) > 0)
                    @php $i=0; @endphp
                    @foreach ($select_cart as $cart)
                        @php
                            $value = @App\SysHelper::com_curr_format($cart->qty * $cart->unitprice, 2, '.', ',');
                            $taxamount = @App\SysHelper::com_curr_format($cart->value - $cart->discount, 2, '.', ',');
                            $vatamount = @App\SysHelper::com_curr_format(
                                (($cart->value - $cart->discount) * $cart->tax) / 100,
                                2,
                                '.',
                                ',',
                            );
                            $totalamount =
                                $cart->qty * $cart->unitprice -
                                $cart->discount +
                                (($cart->qty * $cart->unitprice - $cart->discount) * $cart->tax) / 100;
                        @endphp
                        <tr>
                            <td><input type="text" class="form-control text-center" name="sort_id[]"
                                    value="{{ $i + 1 }}" /></td>
                            <td>
                                <select class="form-control noborder " name="part_number[]">
                                    <option value="{{ $cart->part_number }}">{{ $cart->part_number_txt }}</option>
                                </select>
                            </td>
                            <td>
                                <textarea class="form-control" name="description[]" rows="1">{{ $cart->description }}</textarea>
                                <input type="hidden" name="product_type[]" value="{{ $cart->product_type }}">
                            </td>
                            <td><input class="form-control text-center qty rc" type="number" id="tax_{{ $i }}"
                                    name="tax[]" autocomplete="off" min="0" value="{{ $cart->tax }}"
                                    onchange="calc_change({{ $i }})"></td>
                            <td><input class="form-control text-center qty rc" type="number" id="qty_{{ $i }}"
                                    data-enter-skip name="qty[]" autocomplete="off" min="0"
                                    value="{{ $cart->qty }}" onchange="calc_change({{ $i }})"
                                    onkeydown="return set_license_key_normal(event, this)"></td>
                            <td class="jshide"><input class="form-control text-end" type="text"
                                    id="unitprice_{{ $i }}"
                                    value="{{ @App\SysHelper::com_curr_format($cart->unitprice, 2, '.', ',') }}"
                                    name="unitprice[]" autocomplete="off" min="0"
                                    onchange="calc_change({{ $i }})" onblur="formatCurrency(this)"></td>
                            <td class="jshide"><input class="form-control text-end" type="text"
                                    id="value_{{ $i }}" value="{{ $value }}" name="value[]"
                                    autocomplete="off" min="0" readonly></td>
                            <td class="jshide"><input class="form-control text-end" type="text"
                                    id="discount_{{ $i }}"
                                    value=" {{ @App\SysHelper::com_curr_format($cart->discount, 2, '.', ',') }}"
                                    name="discount[]" autocomplete="off" min="0"
                                    onchange="calc_change({{ $i }})" onblur="formatCurrency(this)"></td>
                            <td class="jshide"><input class="form-control text-end" type="text"
                                    id="taxableamount_{{ $i }}" value="{{ $taxamount }}"
                                    name="taxableamount[]" readonly></td>
                            <td class="jshide"><input class="form-control text-end" type="text"
                                    id="vatamount_{{ $i }}" value="{{ $vatamount }}"
                                    name="vatamount[]" readonly></td>
                            <td class="jshide"><input class="form-control text-end" type="text"
                                    id="totalamount_{{ $i }}"
                                    value="{{ @App\SysHelper::com_curr_format($totalamount, 2, '.', ',') }}"
                                    name="totalamount[]" readonly></td>
                            <td class="jshide"><input class="form-control srl" type="test"
                                    id="srl_{{ $i }}" name="serial_no[]"
                                    onclick="srlno_add({{ $i }})"></td>
                        </tr>
                        @php $i++;
                        @endphp
                    @endforeach

                @endif

                <tr>
                    <td><input type="text" class="form-control text-center" name="sort_id[]"
                            value="{{ $select_cart ? count($select_cart) + 1 : 1 }}" /></td>
                    <td class="noborder">
                        <select class="form-control noborder " name="part_number[]">
                        </select>
                        {{-- on focus add this class and its funcanalities js-product-select --}}
                    </td>
                    <td>
                        <textarea class="form-control" name="description[]" rows="1"></textarea>
                        <input class="form-control" type="text" name="part_number_txt[]" autocomplete="off"
                            readonly="true" hidden>
                        <input class="form-control" type="text" name="hscode_txt[]" autocomplete="off"
                            readonly="true" hidden>
                        <input class="form-control" type="text" name="product_type[]" autocomplete="off"
                            readonly="true" hidden>
                        <input class="form-control" type="text" name="product_type_part_number_text[]"
                            autocomplete="off" readonly="true" hidden>
                    </td>
                    <td><input type="number" class="form-control text-center" name="tax[]"
                            onchange="calc_change_new(this)"></td>
                    <td><input class="form-control text-center" type="number" data-enter-skip name="qty[]"
                            autocomplete="off" min="0" onchange="calc_change_new(this)"
                            onkeydown="return set_license_key_normal(event, this)"></td>
                    <td><input class="form-control text-end" type="text" name="unitprice[]" step="any"
                            autocomplete="off" min="0" onchange="calc_change_new(this)"
                            onblur="formatCurrency(this)"></td>
                    <td><input class="form-control text-end" type="text" name="value[]" autocomplete="off"
                            min="0" readonly></td>
                    <td><input class="form-control text-end" type="text" name="discount[]" autocomplete="off"
                            min="0" onchange="calc_change_new(this)" onblur="formatCurrency(this)"></td>
                    <td><input class="form-control text-end" type="text" name="taxableamount[]"
                            autocomplete="off" min="0" readonly></td>
                    <td><input class="form-control text-end" type="text" name="vatamount[]" autocomplete="off"
                            min="0" readonly></td>
                    <td><input class="form-control text-end" type="text" name="totalamount[]" autocomplete="off"
                            min="0" readonly></td>
                    <td><input class="form-control text-end" type="text" name="serial_no[]"></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" scope="col">Total</th>
                    <th class="text-center"><label id="lbl_total_qty">0</label></th>
                    <th class="text-end" scope="col"><label id="lbl_total_price">0</label></th>
                    <th class="text-end" scope="col"><label id="lbl_total_value">0</label></th>
                    <th class="text-end" scope="col"><label id="lbl_total_discount">0</label></th>
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
    @php
        $r = @App\SysHelper::get_data_by_role();
        $company_id = $r[0];
        $customs_freight_account = @App\SysHelper::get_customs_freight_accounts_for_sales($company_id);
        $vendors2 = @App\SysHelper::get_supplier_list_all($company_id);
    @endphp
    <div class="equipment comon-status row mt-4 d-block">
        <style>
            #fright_table {
                table-layout: fixed;
            }

            #fright_table th,
            #fright_table td {
                overflow: hidden;
            }

            #fright_table input,
            #fright_table select {
                width: 100%;
                box-sizing: border-box;
            }
        </style>
        <table class="table table-hover" id="fright_table" width="100%" cellspacing="0" style="table-layout:fixed;">
            <thead>
                <tr>
                    <th style="width:50px;" class="text-center">@lang('Date')</th>
                    <th style="width:70px;" class="text-center">@lang('Bill No')</th>
                    <th style="width:100px;" class="text-center">@lang('Name')</th>
                    <th style="width:150px;" class="text-center">@lang('Credit Account')</th>
                    <th style="width:70px;" class="text-center">@lang('Amount')</th>
                    <th style="width:100px;" class="text-center">@lang('Remarks')
                        <input type="hidden" value="1" id="fright_row" />
                        <a style="cursor: pointer;" class="btn-md float-right" data-bs-popover="popover"
                            data-bs-trigger="hover" data-bs-delay="500" data-bs-content="Add new freight charge row"
                            data-bs-placement="bottom" onclick="add_fright()"><i
                                class="ico icon-outline-add-square text-success"></i></a>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr id="fright_row_1">
                    <td>
                        <input class="form-control date-picker" type="text" id="cfc_date_1" name="cfc_date[]"
                            autocomplete="off">
                    </td>
                    <td>
                        <input class="form-control" type="text" id="cfc_bill_no_1" name="cfc_bill_no[]"
                            autocomplete="off">
                    </td>
                    <td>
                        <select class="form-control js-example-basic-single" name="cfc_name[]" id="cfc_name_1">
                            <option value=""></option>
                            @foreach ($customs_freight_account as $value)
                                <option value="{{ $value->id }}">{{ $value->account_name }}
                                    @if (@App\SysHelper::getCompanyCodeSettings()['is_account_code'])
                                        ({{ @$value->account_code }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <select class="form-control js-example-basic-single" name="cfc_credit_account[]"
                            id="cfc_credit_account_1">
                            <option value=""></option>
                            @foreach ($vendors2 as $value)
                                <option value="{{ $value->id }}">{{ $value->account_name }}
                                    @if (@App\SysHelper::getCompanyCodeSettings()['is_supplier_code'])
                                        ({{ @$value->account_code }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input class="form-control text-end" type="text" id="cfc_amount_1" name="cfc_amount[]"
                            autocomplete="off" min="0">
                    </td>
                    <td>
                        <input class="form-control" type="text" id="cfc_remarks_1" name="cfc_remarks[]"
                            autocomplete="off">
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4">@lang('Total')</th>
                    <th class="text-end" id="fright_total_amount">0</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
    <script>
        $(document).ready(function() {
            window.add_fright = function() {
                var id = parseInt($('#fright_row').val()) || 0;
                id = id + 1;
                $('#fright_row').val(id);
                var $last = $('#fright_table tbody tr:last');
                $last.find('.date-picker').each(function() {
                    if (this._flatpickr) {
                        this._flatpickr.destroy();
                    }
                });
                $last.find('.js-example-basic-single').select2('destroy');
                var $new = $last.clone();
                $last.find('.js-example-basic-single').select2({
                    width: '100%'
                });
                $last.find('.date-picker').each(function() {
                    flatpickr(this, {
                        dateFormat: 'd/m/Y',
                        allowInput: true
                    });
                });
                $new.attr('id', 'fright_row_' + id);
                $new.find('select, input').each(function() {
                    var elem = $(this);
                    var oldId = elem.attr('id');
                    if (oldId) {
                        var base = oldId.substring(0, oldId.lastIndexOf('_') + 1);
                        elem.attr('id', base + id);
                    }
                    elem.val('');
                });
                $('#fright_table tbody').append($new);
                $new.find('.js-example-basic-single').select2({
                    width: '100%'
                });
                $new.find('.date-picker').each(function() {
                    flatpickr(this, {
                        dateFormat: 'd/m/Y',
                        allowInput: true
                    });
                });
                updateFrightTotals();
            };

            function updateFrightTotals() {
                var total = 0;
                $('#fright_table tbody tr').each(function() {
                    var val = $(this).find('input[name="cfc_amount[]"]').val().replace(/,/g, '') || '0';
                    total += parseFloat(val) || 0;
                });
                $('#fright_total_amount').text(formatAmount(total));
            }

            $(document).on('input', 'input[name="cfc_amount[]"]', function() {
                updateFrightTotals();
            });
            $(document).on('blur', 'input[name="cfc_amount[]"]', function() {
                this.value = formatAmount(this.value);
                updateFrightTotals();
            });

            $('#fright_table .js-example-basic-single').select2({
                width: '100%'
            });
            $('#fright_table .date-picker').each(function() {
                flatpickr(this, {
                    dateFormat: 'd/m/Y',
                    allowInput: true
                });
            });
            updateFrightTotals();
        });
    </script>
    {{ Form::close() }}


    {{-- Models  --}}
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
                                    <label class="form-label">Discount Amount</label>
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

    <div class="modal side-panel fade" id="serialNoModal" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" style="height: 279px !important;">
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
                                    <label class="form-label">Serial No</label>
                                    <div class="form-group">
                                        <textarea type="text" class="form-control" id="add_serial_no" style="height: 150px;"></textarea>
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

    {{-- Models  --}}

    <div class="modal side-panel fade" id="descriptionModal" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" style="height: 300px !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel">Description</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <label class="form-label">Description:</label>
                                    <div class="form-group">
                                        <textarea type="text" class="form-control" id="add_description" style="height: 150px;"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light add-btn ms-2" onclick="addDescription()">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Update
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal side-panel fade" id="narrationModal" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="poexcelimport">Enter Narration</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body">
                            <textarea style="height: 109px !important;" class="form-control" id="narrationTextarea" rows="6"
                                placeholder="Write narration here..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="insertNarration" class="btn btn-light add-btn ms-2">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                    </button>
                </div>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const referenceInput = document.getElementById('narration');
            const narrationTextarea = document.getElementById('narrationTextarea');
            const insertButton = document.getElementById('insertNarration');
            const narrationModal = document.getElementById('narrationModal');

            // Pre-fill textarea when modal opens
            narrationModal.addEventListener('shown.bs.modal', () => {
                narrationTextarea.value = referenceInput.value;
                setTimeout(() => narrationTextarea.focus(), 500);
            });

            // On insert button click, update input and close modal
            insertButton.addEventListener('click', () => {
                referenceInput.value = narrationTextarea.value;
                bootstrap.Modal.getInstance(narrationModal).hide();
            });
        });
    </script>

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
                    validRows.push({
                        index,
                        input
                    });
                }
            });

            if (totalValue === 0) {
                alert("All rows have empty or zero 'Value'. Nothing to split.");
                return;
            }

            validRows.forEach(({
                index,
                input
            }) => {
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

        document.getElementById("discount_add_btn").addEventListener("click", function() {
            splitAmount('discountInput', 'discount');
            $('#discountModal').modal('hide');
        });
    </script>

    <script>
        let serialNoModal;
        document.addEventListener("DOMContentLoaded", function() {
            const modalElement = document.getElementById('serialNoModal');
            serialNoModal = new bootstrap.Modal(modalElement);
        });
        let currentSerialInput = null;

        $(document).on('click', 'input[name="serial_no[]"]', function() {
            currentSerialInput = $(this);
            $('#add_serial_no').val(currentSerialInput.val());
            serialNoModal.show();
            setTimeout(() => $('#add_serial_no').focus(), 500);
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
        let descriptionModal;
        document.addEventListener("DOMContentLoaded", function() {
            const descriptionElement = document.getElementById('descriptionModal');
            descriptionModal = new bootstrap.Modal(descriptionElement);
        });
        let currentDescriptionInput = null;

        $(document).on('click', 'textarea[name="description[]"]', function() {
            currentDescriptionInput = $(this);
            $('#add_description').val(currentDescriptionInput.val());
            descriptionModal.show();
            setTimeout(() => $('#add_description').focus(), 500);

        });

        function addDescription() {
            if (currentDescriptionInput) {
                const val = $('#add_description').val();
                currentDescriptionInput.val(val);
                descriptionModal.hide();
                currentDescriptionInput = null;
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
            var unitprice = $row.find('input[name="unitprice[]"]').val().replace(/,/g, '') || '0';
            var discount = $row.find('input[name="discount[]"]').val().replace(/,/g, '') || '0';
            var fright = 0;
            var customcharges = 0;

            var decimal_point = @json(session('logged_session_data.decimal_point'));

            // Calculate value
            var fin_value = parseFloat(unitprice) * parseFloat(qty);
            $row.find('input[name="value[]"]').val(formatAmount(fin_value));

            // Calculate taxable amount
            var fin_taxableamount = fin_value + parseFloat(customcharges) + parseFloat(fright) - parseFloat(discount);
            $row.find('input[name="taxableamount[]"]').val(formatAmount(fin_taxableamount));

            // Calculate VAT
            var fin_vatamount = fin_taxableamount * (parseFloat(net_vat) / 100);
            $row.find('input[name="vatamount[]"]').val(formatAmount(fin_vatamount));

            // Calculate total amount
            var total_amount = fin_taxableamount + fin_vatamount;
            $row.find('input[name="totalamount[]"]').val(formatAmount(total_amount));

            $("#loading_bg").css("display", "none");
            update_totals();
        }

        function update_totals() {
            let total_qty = 0,
                total_price = 0,
                total_value = 0,
                total_discount = 0,
                //total_fright = 0,
                //total_customcharges = 0,
                total_taxableamount = 0,
                total_vatamount = 0,
                total_totalamount = 0;

            const decimal_point = @json(session('logged_session_data.decimal_point'));

            $('#myTable tbody tr').each(function() {
                const $row = $(this);

                total_qty += parseFloat($row.find('input[name="qty[]"]').val()) || 0;
                total_price += parseFloat($row.find('input[name="unitprice[]"]').val().replace(/,/g, '')) || 0;
                total_value += parseFloat($row.find('input[name="value[]"]').val().replace(/,/g, '')) || 0;
                total_discount += parseFloat($row.find('input[name="discount[]"]').val().replace(/,/g, '')) || 0;
                //total_fright += parseFloat($row.find('input[name="fright[]"]').val()) || 0;
                //total_customcharges += parseFloat($row.find('input[name="customcharges[]"]').val()) || 0;
                total_taxableamount += parseFloat($row.find('input[name="taxableamount[]"]').val().replace(/,/g,
                    '')) || 0;
                total_vatamount += parseFloat($row.find('input[name="vatamount[]"]').val().replace(/,/g, '')) || 0;
                total_totalamount += parseFloat($row.find('input[name="totalamount[]"]').val().replace(/,/g, '')) ||
                    0;
            });

            $('#lbl_total_qty').text(total_qty);
            $('#lbl_total_price').text(formatAmount(total_price));
            $('#lbl_total_value').text(formatAmount(total_value));
            $('#lbl_total_discount').text(formatAmount(total_discount));
            //$('#lbl_total_fright').text(total_fright.toFixed(decimal_point));
            //$('#lbl_total_customcharges').text(total_customcharges.toFixed(decimal_point));
            $('#lbl_total_taxableamount').text(formatAmount(total_taxableamount));
            $('#lbl_total_vatamount').text(formatAmount(total_vatamount));
            $('#lbl_total_totalamount').text(formatAmount(total_totalamount));
        }
    </script>
    <script>
        update_totals();
    </script>
    <script>
        $(document).on('focus', 'select[name="part_number[]"]', function() {
            const $select = $(this);

            // Add the class if not present
            if (!$select.hasClass('js-product-select')) {
                $select.addClass('js-product-select');
                //$select.remove('select2-hidden-accessible');

                // Initialize Select2
                initAccountSelect2(this); // your existing function
            }
        });


        const SHOW_CUSTOMER_CODE = {{ @App\SysHelper::getCompanyCodeSettings()['is_customer_code'] ? 'true' : 'false' }};


        $(document).ready(function() {
            function initAccountSelect2(selector) {
                $(selector).select2({
                    ajax: {
                        url: '{{ route('autocomplete.get_cust_account_list_ajax') }}',
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                search_text: params.term
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.map(function(item) {
                                    let text = "";

                                    if (SHOW_CUSTOMER_CODE) {
                                        text = item.account_name + " (" + item.account_code +
                                            ")";
                                    } else {
                                        text = item.account_name; // no code
                                    }

                                    return {
                                        id: item.id,
                                        text: text
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
            $(document).on('focus', '.js-account-select', function() {
                if (!$(this).hasClass("select2-hidden-accessible")) {
                    initAccountSelect2(this);
                    $(this).select2('open');
                }
            });

            // Open dropdown and focus search box on click
            $(document).on('click', '.js-account-select', function() {
                $(this).select2('open');
            });

            // Focus the search input inside the opened Select2 dropdown
            $(document).on('select2:open', function() {
                setTimeout(function() {
                    const searchInput = document.querySelector(
                        '.select2-container--open .select2-search__field');
                    if (searchInput) {
                        searchInput.focus();
                    }
                }, 0);
            });

            // When any .js-account-select select2 opens, prefill the search box with the currently selected value
            $(document).on('select2:open', function(e) {
                // Find the select2 element that triggered the event
                var $select = $(document.activeElement).closest('.js-account-select');
                if ($select.length === 0) {
                    // fallback: try to get the open dropdown's select
                    $select = $('.js-account-select').filter(function() {
                        return $(this).data('select2') && $(this).data('select2').isOpen();
                    });
                }
                if ($select.length > 0) {
                    var sel = $select.select2('data');
                    if (sel && sel.length && sel[0].text) {
                        setTimeout(function() {
                            const searchInput = document.querySelector(
                                '.select2-container--open .select2-search__field');
                            if (searchInput) {
                                // Put current selected text into search box so user can edit / refine
                                searchInput.value = sel[0].text.trim();
                                // trigger input so select2 filters on prefilling
                                var event = new Event('input', {
                                    bubbles: true
                                });
                                searchInput.dispatchEvent(event);

                                // Move cursor to end of the text
                                try {
                                    var len = searchInput.value.length;
                                    searchInput.setSelectionRange(len, len);
                                } catch (err) {
                                    // ignore if not supported
                                }
                            }
                        }, 0);
                    }
                }
            });

        });
    </script>

    <script>
        $(document).ready(function() {
            function initAccountSelect2(selector) {
                $(selector).select2({
                    ajax: {
                        url: '{{ route('autocomplete.get_product_list_ajax') }}',
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                search_text: params.term
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.map(function(item) {
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
                    placeholder: '',
                    minimumInputLength: 2,
                    dropdownParent: $(selector).parent() // optional: ensures dropdown shows in modals
                });

                $(selector).on('select2:select', function(e) {
                    var selectedData = e.params.data;
                    var $row = $(this).closest('tr'); // find the closest row

                    // Set values using "name" attribute selectors inside the same row
                    $row.find('textarea[name="description[]"]').val(selectedData.description || '');
                    $row.find('input[name="part_number_txt[]"]').val(selectedData.text || '');
                    $row.find('input[name="hscode_txt[]"]').val(selectedData.hscode || '');
                    $row.find('input[name="product_type[]"]').val(selectedData.product_type || '');
                    $row.find('input[name="product_type_part_number_text[]"]').val(selectedData
                        .description || '');
                    // $row.find('input[name="discount[]"]').val(0);
                    $row.find('input[name="fright[]"]').val(0);
                    $row.find('input[name="customcharges[]"]').val(0);
                    $row.find('input[name="tax[]"]').val(parseInt($('#net_vat').val()));
                    $row.find('input[name="qty[]"]').focus();

                });


                // prefill Select2 search with currently selected value when dropdown opens
                $(selector).on('select2:open', function() {
                    try {
                        var sel = $(this).select2('data');
                        if (sel && sel.length && sel[0].text) {
                            setTimeout(function() {
                                const searchInput = document.querySelector(
                                    '.select2-container--open .select2-search__field');
                                if (searchInput) {
                                    searchInput.value = sel[0].text.trim();
                                    // trigger input event so select2 filters on prefilling
                                    var event = new Event('input', {
                                        bubbles: true
                                    });
                                    searchInput.dispatchEvent(event);
                                    try {
                                        var len = searchInput.value.length;
                                        searchInput.setSelectionRange(len, len);
                                    } catch (err) {
                                        /* ignore */
                                    }
                                }
                            }, 0);
                        }
                    } catch (err) {
                        console.error('Error prefilling product search field', err);
                    }
                });


            }

            initAccountSelect2('.js-product-select');

            // Re-initialize on focus if needed
            $(document).on('focus', '.js-product-select', function() {
                if (!$(this).hasClass("select2-hidden-accessible")) {
                    initAccountSelect2(this);
                    $(this).select2('open');
                }
            });

            // On click, open dropdown and focus on search field
            $(document).on('click', '.js-product-select', function() {
                $(this).select2('open');
            });

            // Optional: Auto focus on search input when dropdown opens
            $(document).on('select2:open', function() {
                setTimeout(function() {
                    document.querySelector('.select2-container--open .select2-search__field')
                        ?.focus();
                }, 0);
            });
        });
    </script>

    <script>
        /*table row fill based on layout height*/
        window.onload = function() {
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
        function without_po(id) {
            $("#loading_bg").css("display", "block");
            $("#si_id").val(id);
            $("#table_id2").css("display", "");
            if (typeof applySiChargesToDnFrightTable === 'function') {
                applySiChargesToDnFrightTable([]);
            }
            $("#loading_bg").css("display", "none");
        }

        function popup_si_pending(id) {
            console.log("clicked")
            $("#loading_bg").css("display", "block");
            $("#hd_pending_dn_id").val(id);
            $("#si_id").val(id);
            $("#addDNPending").click();
            $("#addDNPending").prop("disabled", true);
            $("#loading_bg").css("display", "none");
        }

        function get_pending_si_list() {
            var cus_id = $("#customer_id").val();
            get_vat(cus_id);
            get_dn_list(cus_id);
            get_cust_details(cus_id);

        }

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
                    console.log("dataResult", dataResult)
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var len = 0;
                    if (dataResult['data'] != null) {
                        len = dataResult['data'].length;
                    }
                    if (len > 0) {
                        for (var i = 0; i < len; i++) {
                            if (dataResult['data'][i].status == 3) {


                            } else {
                                $('#btnSubmit').css('display', '');
                            }
                            $('#payment_terms').val(dataResult['data'][i].payment_terms).trigger('change');
                            $('#shipping_supplier').val(dataResult['data'][i].account_id).trigger('change');
                            // $('#shipping_name').val(dataResult['data'][i].contcat_person);
                            // $('#shipping_address').val(dataResult['data'][i].address);
                            $('#customer_type').val(dataResult['data'][i].customer_type).trigger('change');
                            $('#sale_type').val(dataResult['data'][i].sale_type).trigger('change');
                            $('#country').val(dataResult['data'][i].vat_country).trigger('change');
                            // $('#state').val(dataResult['data'][i].vat_state).trigger('change');
                            window.SELECTED_STATE_ID = dataResult['data'][i].vat_state;


                            console.log("cat=", dataResult['data'][i].vat_percentage);
                            $('#net_vat').val(dataResult['data'][i].vat_percentage);
                            $('.vat').val(dataResult['data'][i].vat_percentage);
                            $('#vat_percent').val(dataResult['data'][i].vat_percentage);
                            $('#vat_number').val(dataResult['data'][i].vat_number);
                        }
                    } else {
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
                        $("#loading_bg").css("display", "none");
                    }
                }
            });
        }

        function get_dn_list(cus_id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('sales-invoice-pending') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    cus_id: cus_id,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var len = 0;
                    if (dataResult['data'] != null) {
                        len = dataResult['data'].length;
                    }
                    if (len > 0) {
                        $("#plist").empty();
                        for (var i = 0; i < len; i++) {
                            var id = dataResult['data'][i].id;
                            var doc_number = dataResult['data'][i].doc_number;
                            var option = "<option value='" + id + "'>" + doc_number +
                                "</option>";
                            var innerHtml =
                                "<input type='radio' onclick='popup_si_pending(" + id +
                                ")' id='pending_dn_" + i +
                                "' name='pending_dn' value='" + doc_number +
                                "'> <label for='pending_dn_" + i + "'> " + doc_number +
                                "</label><br />";
                            $("#plist").append(innerHtml);
                        }
                    } else {
                        $("#plist").empty();
                    }

                    var innerHtml =
                        "<input type='radio' onclick='without_po(0)' id='pending_po_0' name='pending_po' value='0'> <label for='pending_po_0'> Without SIV</label><br />";
                    $("#plist").append(innerHtml);

                    $("#loading_bg").css("display", "none");
                }
            });
        }
    </script>
    <!-- Modal License Key-->
    <button id="btn_ModalLicenseKey" data-bs-target="#ModalLicenseKey" data-bs-toggle="modal" hidden></button>
    <div class="modal side-panel fade" id="ModalLicenseKey" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="ModalLicenseKey" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Select License Key <label class="" style="margin-left: 68px"
                            id="ModalLabelHeading"></label> <span style="margin-left: 116px">Available Qty</span> - <label id="total_key">0</label></h5>
                    <input type="hidden" id="part_no" />
                    <input type="hidden" id="update_id" />
                    <input type="hidden" id="license_qty_limit" value="0" />
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="popup_close"></button>
                </div>


                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-2">
                            <label for="" class="form-label">Qty</label>
                            <input type="hidden" id="item_id" />
                            <input type="hidden" id="edit_license_id" value="" />
                            <input type="number" class="form-control" name="license_qty" id="license_qty"
                                value="1" readonly />

                        </div>
                        <div class="col-md-5">
                            <label for="" class="form-label"> Selected: <label id="selected_key">0</label> of <label id="license_qty_cap">0</label></label>
                            <input type="text" id="license_key_search" placeholder="Search license key..."
                                class="form-control" />
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-12 mt-2">
                            <table id="lk-table" class="table table-hover long-list" width="100%"
                                cellspacing="0">
                                <thead>
                                    <tr class="text-center">
                                        <th style="width: 5%;">Select</th>

                                        <th style="width: 30%;" class="text-start">Licence Key</th>
                                        <th style="width: 15%;">Expiry Date</th>
                                        <th style="width: 12%;">Doc No</th>
                                        <th style="width: 10%;">Doc Date</th>
                                        <th style="width: 13%;">Name</th>
                                        <th style="width: 15%;">Bill Number</th>
                                        <th style="width: 15%;">Deal ID</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mt-2">


                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button onclick="set_license_key()" type="button" class="btn btn-light add-btn ms-2">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Add Selected
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script>
        function filterLicenseRows() {
            var query = ($('#license_key_search').val() || '').toString().toLowerCase().trim();
            $('#lk-table tbody tr').each(function() {
                var rowText = ($(this).text() || '').toLowerCase();
                $(this).toggle(query === '' || rowText.indexOf(query) !== -1);
            });
        }

        $(document).ready(function() {
            $(document).on('click', '#lk-table > tbody > tr', function(e) {
                // prevent triggering when clicking inside a nested table
                if ($(e.target).closest('table').attr('id') !== 'lk-table') {
                    return;
                }

                if ($(e.target).closest('td').hasClass('no-toggle')) {
                    return; // do nothing if inside excluded cells
                }

                $(this).toggleClass('expand');
            });

            $(document).on('input keyup change', '#license_key_search', function() {
                filterLicenseRows();
            });

            $(document).on('shown.bs.modal', '#ModalLicenseKey', function() {
                $('#license_key_search').val('');
                filterLicenseRows();
                var partId = ($('#part_no').val() || '').toString().trim();
                if (partId) {
                    var $sel = $('#myTable select[name="part_number[]"]').filter(function() {
                        return ($(this).val() || '').toString().trim() === partId;
                    }).first();
                    if ($sel.length) {
                        dnLicenseSetSerialTargetFromRow($sel.closest('tr'));
                    }
                }
                setTimeout(function() {
                    $('#license_key_search').focus();
                }, 50);
            });
        });

        function dnLicenseSetSerialTargetFromRow($row) {
            window.dnLicenseSerialInput = null;
            if ($row && $row.length) {
                var $el = $row.find('input[name="serial_no[]"]').first();
                if ($el.length) {
                    window.dnLicenseSerialInput = $el;
                }
            }
        }

        function dnLicenseResolveSerialInput() {
            var $inp = window.dnLicenseSerialInput;
            if ($inp && $inp.length) {
                return $inp;
            }
            var partId = ($('#part_no').val() || '').toString().trim();
            if (!partId) {
                return $();
            }
            var $found = $();
            $('#myTable tbody tr').each(function() {
                var $sel = $(this).find('select[name="part_number[]"]').first();
                if (!$sel.length) {
                    return;
                }
                if (($sel.val() || '').toString().trim() !== partId) {
                    return;
                }
                $found = $(this).find('input[name="serial_no[]"]').first();
                if ($found.length) {
                    return false;
                }
            });
            return $found;
        }

        function dnLicenseAppendSelectedKeysToSerial() {
            var keys = [];
            $('#lk-table tbody tr').each(function() {
                var $tr = $(this);
                if (!$tr.find('.chk_key').is(':checked')) {
                    return;
                }
                var $cells = $tr.find('td');
                if ($cells.length < 2) {
                    return;
                }
                var t = $cells.eq(1).text().replace(/\s+/g, ' ').trim();
                if (t) {
                    keys.push(t);
                }
            });
            var $inp = dnLicenseResolveSerialInput();
            if (!$inp || !$inp.length) {
                return;
            }
            $inp.val(keys.join(', '));
        }

        function set_license_key_normal(e, el) {
            e = e || window.event;
            var key = e.which || e.keyCode;
            if (key !== 13) {
                return true;
            }
            var $row = $(el).closest("tr");
            var pt = $row.find('input[name="product_type[]"]').val();
            if (pt != 2) {
                return true;
            }
            var partId = $row.find('select[name="part_number[]"] option:selected').val();
            if (!partId) {
                toastr.warning('Select a part number before assigning license keys.');
                e.preventDefault();
                return false;
            }
            $('#part_no').val(partId);
            dnLicenseSetSerialTargetFromRow($row);
            var rowQty = parseInt($(el).val(), 10) || 0;
            $('#license_qty_limit').val(rowQty);
            $('#license_qty').val(rowQty);
            $("#ModalLabelHeading").text($row.find('select[name="part_number[]"] option:selected').text());
            $("#btn_ModalLicenseKey").click();
            get_license_key(String(partId));
            e.preventDefault();
            return false;
        }
        // function set_license_key_normal(){
        //     $('#qty').keypress(function (e) {
        //         var key = e.which;
        //         if(key === 13) { //the enter key code
        //             var pt = 2;
        //             if(pt == 2) {
        //                 var part_id =$('#part_number_new').val();
        //                 $('#ModalLabelHeading').text($('#part_number_new').val());    
        //                 $('#part_no').val(part_id);
        //                 $('#btn_ModalLicenseKey').click();
        //                 get_license_key(part_id);
        //             }
        //             return true;
        //         }
        //     });
        // }

        function set_license_key_po(rowid, producttype) {
            $('#qty_' + rowid).keypress(function(e) {
                var key = e.which;
                if (key === 13) { //the enter key code
                    var pt = producttype;
                    if (pt == 2) {
                        var part_id = $('#part_id_' + rowid).val();
                        $('#ModalLabelHeading').text($('#part_number_' + rowid).val());
                        $('#part_no').val(part_id);
                        dnLicenseSetSerialTargetFromRow($('#qty_' + rowid).closest('tr'));
                        var rowQty = parseInt($('#qty_' + rowid).val(), 10) || 0;
                        $('#license_qty_limit').val(rowQty);
                        $('#license_qty').val(rowQty);
                        $('#btn_ModalLicenseKey').click();
                        get_license_key(part_id);
                    }
                    return true;
                }
            });
        }

        function get_license_key(part_id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('dn-get-grn-license-key') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    item_id: part_id,
                },
                cache: false,
                success: function(dataResult) {
                    try {
                        dataResult = typeof dataResult === 'string' ? JSON.parse(dataResult) : dataResult;
                    } catch (err) {
                        toastr.error('Could not load license keys.');
                        $('#lk-table tbody').empty();
                        $('#selected_key').text(0);
                        $('#total_key').text(0);
                        return;
                    }
                    var len = 0;
                    var getSelectedRows = "";
                    var selectedCount = 0;
                    var qtyLimit = parseInt($('#license_qty_limit').val(), 10) || 0;
                    $('#license_qty').val(qtyLimit);
                    $('#license_qty_cap').text(qtyLimit);
                    $('#update_id').val('');
                    if (dataResult['data'] != null) {
                        len = dataResult['data'].length;
                        $('#total_key').text(len);
                    }
                    if (len > 0) {
                        for (var i = 0; i < len; i++) {
                            var row = dataResult['data'][i];
                            // Draft reservations for this cart/session (dn_id = -1)
                            var isSelected = parseInt(row.dn_id, 10) === -1;
                            if (isSelected) {
                                selectedCount++;
                            }
                            var isSalesReturn = parseInt(row.sales_return_id, 10) > 0;
                            var isStockIn = !isSalesReturn && parseInt(row.type, 10) === 3;
                            var isOpeningStock = !isSalesReturn && !isStockIn && parseInt(row.opening_stock_id, 10) > 0;
                            var docNo = isSalesReturn ? (row.sr_doc_number || '') : (isStockIn ? (row.stkin_doc_number || '') : (isOpeningStock ? (row.ops_doc_number || '') : (row.grn_no || '')));
                            var docDate = isSalesReturn ? (row.sr_doc_date ? get_format_date(row.sr_doc_date) : '') :
                                (isStockIn ? (row.stkin_doc_date ? get_format_date(row.stkin_doc_date) : '') : (isOpeningStock ? (row.ops_doc_date ? get_format_date(row.ops_doc_date) : '') : (row.grn_date ? get_format_date(row.grn_date) : '')));
                            var partyName = isSalesReturn ? (row.sr_customer_name || '') : (isStockIn ? 'Stock In' : (isOpeningStock ? 'Opening Stock' : (row.supplier_name || '')));
                            var billNumber = isSalesReturn ? (row.sr_lpo_number || '') : ((isOpeningStock || isStockIn) ? '' : (row.grn_bill_number || ''));
                            var dealId = isSalesReturn ? (row.sr_deal_code || row.sr_deal_id || '') :
                                ((isOpeningStock || isStockIn) ? '' : (row.grn_deal_code || row.grn_deal_id || ''));
                            var grnDocUrlBase = "{{ URL::to('get-url-purchase-grn') }}";
                            var srDocUrlBase = "{{ URL::to('get-url-sales-return') }}";
                            var opsEditUrlBase = "{{ URL::to('item-store') }}";
                            var stkinEditUrlBase = "{{ URL::to('get-url-stock-in') }}";
                            var docUrl = '';
                            if (docNo) {
                                if (isSalesReturn) {
                                    docUrl = srDocUrlBase + "/" + encodeURIComponent(docNo);
                                } else if (isStockIn) {
                                    docUrl = stkinEditUrlBase + "/" + encodeURIComponent(docNo);
                                } else if (isOpeningStock) {
                                    var opsId = parseInt(row.opening_stock_id, 10) || 0;
                                    if (opsId > 0) {
                                        docUrl = opsEditUrlBase + "/" + opsId + "/edit";
                                    }
                                } else {
                                    docUrl = grnDocUrlBase + "/" + encodeURIComponent(docNo);
                                }
                            }
                            var safeDocNo = $('<div>').text(docNo || '').html();
                            var docNoHtml = docNo ? (docUrl ? ("<a href='" + docUrl +
                                "' target='_blank' rel='noopener noreferrer'>" + safeDocNo + "</a>") : safeDocNo) : '';
                            getSelectedRows += "<tr class='text-center' data-lk-status=\"" + (row.status != null ? row.status : '1') + "\">\
                                                           <td><input class='chk_key' type='checkbox' id='select_key_" +
                                Number(i +
                                    1) +
                                "' onclick='key_select_change(" + Number(i + 1) + ")' " + (isSelected ?
                                    "checked" : "") + " /><input type='hidden' id='item_key_id_" + Number(i +
                                    1) + "' value='" + row.id + "' /></td>\
                                                           <td class='text-start'>" + (row.license_key || "") + "</td>\
                                                           <td>" + (row.exp_date ? get_format_date(row.exp_date) : "") + "</td>\
                                                           <td>" + docNoHtml + "</td>\
                                                           <td>" + docDate + "</td>\
                                                           <td class='text-start'>" + partyName + "</td>\
                                                           <td>" + billNumber + "</td>\
                                                           <td>" + dealId + "</td>\
                                                           </tr>";
                        }
                        $('#license_key').val('');
                        $('#exp_date').val('');
                        $('#lk-table tbody').empty();
                        $("#lk-table tbody").append(getSelectedRows);
                        filterLicenseRows();
                        $('#selected_key').text(selectedCount);
                        key_select_change(0);
                    } else {
                        $('#lk-table tbody').empty();
                        $('#selected_key').text(0);
                        $('#total_key').text(0);
                    }
                },
                complete: function() {
                    $("#loading_bg").css("display", "none");
                }
            });
        }

        function key_select_change(id) {
            var qtyLimit = parseInt($('#license_qty_limit').val(), 10) || 0;
            if (id > 0) {
                var nowSelected = $('.chk_key:checked').length;
                if (qtyLimit > 0 && nowSelected > qtyLimit) {
                    $('#select_key_' + id).prop('checked', false);
                    toastr.error('Only ' + qtyLimit + ' license keys can be selected for this item quantity.');
                }
            }

            var selected = 0;
            var b = 1;
            var itm_id = 0;
            $(".chk_key").each(function() {
                if (this.checked) {
                    selected = Number(selected + 1);
                    if (itm_id == 0) {
                        itm_id = $('#item_key_id_' + b).val();
                    } else {
                        itm_id += ',' + $('#item_key_id_' + b).val();
                    }
                }
                b++;
            });
            $('#update_id').val(itm_id);
            $('#selected_key').text(selected);
        }

        function set_license_key() {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('dn-update-grn-license-key') }}";
            var myArray = $('#update_id').val();
            var qtyLimit = parseInt($('#license_qty_limit').val(), 10) || 0;
            var selectedCount = $('.chk_key:checked').length;
            if (qtyLimit > 0 && selectedCount > qtyLimit) {
                toastr.error('Only ' + qtyLimit + ' license keys can be selected for this item quantity.');
                $("#loading_bg").css("display", "none");
                return false;
            }
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: myArray,
                    item_id: $('#part_no').val(),
                    qty_limit: qtyLimit,
                },
                cache: false,
                success: function(dataResult) {
                    try {
                        dataResult = typeof dataResult === 'string' ? JSON.parse(dataResult) : dataResult;
                    } catch (err) {
                        toastr.error('Unexpected response from server.');
                        return;
                    }
                    if (dataResult.error) {
                        toastr.error(dataResult.error);
                        return;
                    }
                    dnLicenseAppendSelectedKeysToSerial();
                    $('#popup_close').click();
                },
                complete: function() {
                    $("#loading_bg").css("display", "none");
                }
            });
        }
    </script>





    <script>
        $(document).ready(function() {

            // Run on page load (initial)
            get_pending_si_list();


        });
    </script>




    <script>
        $(document).ready(function() {


            $(document).on("change", "#shipping_supplier", function() {
                console.log("changed");
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
                    success: function(dataResult) {
                        console.log(dataResult);
                        var dataResult = JSON.parse(dataResult);
                        var len = 0;
                        var len = 0;
                        if (dataResult['data'] != null) {
                            len = dataResult['data'].length;
                        }
                        if (len > 0) {
                            for (var i = 0; i < len; i++) {
                                $("#shipping_name").val(dataResult['data'][i].contact_person);
                                //$("#shipping_name").val(dataResult['data'][i].contcat_person);
                                // $("#shipping_address_1").val(dataResult['data'][i].address + '\n' + dataResult['data'][i].address2);
                                $("#shipping_address_1").val(dataResult['data'][i].shipping_address);
                                $("#shipping_email").val(dataResult['data'][i].email);
                                $("#shipping_contact_no").val(dataResult['data'][i].contcat_number);
                            }
                        } else {
                            $("#shipping_name").val("");
                            $("#shipping_address_1").val("");
                            $("#shipping_email").val("");
                            $("#shipping_contact_no").val("");
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
                    success: function(dataResult) {
                        console.log(dataResult);
                        var dataResult = JSON.parse(dataResult);
                        var len = 0;
                        var len = 0;
                        if (dataResult['data'] != null) {
                            len = dataResult['data'].length;
                        }
                        if (len > 0) {
                            for (var i = 0; i < len; i++) {
                                $("#shipping_name").val(dataResult['data'][i].customer_salutation +
                                    '. ' + dataResult['data'][i].first_name + ' ' + dataResult[
                                        'data'][i].last_name);
                                //$("#shipping_name").val(dataResult['data'][i].contcat_person);
                                $("#shipping_address_1").val(dataResult['data'][i].address + '\n' +
                                    dataResult['data'][i].address2);
                                $("#shipping_email").val(dataResult['data'][i].email);
                                $("#shipping_contact_no").val(dataResult['data'][i].contcat_number);
                            }
                        } else {
                            $("#shipping_name").val("");
                            $("#shipping_address_1").val("");
                            $("#shipping_email").val("");
                            $("#shipping_contact_no").val("");
                        }
                    }
                });
                $("#loading_bg").css("display", "none");
            }

        });
    </script>



    {{-- Device Serial Modal (populated dynamically when pending SI is loaded) --}}
    <div class="modal fade" id="DeviceSerialModal" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="DeviceSerialModalLabel" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" style="max-width: 22rem;">
            <div class="modal-content">
                <div class="modal-header mb-2">
                    <h4 class="modal-title" id="DeviceSerialModalLabel">Device Serial Numbers</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0">
                    <div id="serial-parts-container">
                        {{-- Dynamically built by JS after pending SI items are loaded --}}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="btn_save_all_serials" class="btn btn-light add-btn ms-2">
                        <i class="ico icon-outline-bookmark-opened text-success" style="font-size:16px"></i> Save All
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .serial-input-row {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            gap: 8px;
        }
    </style>

    <script>
        $(document).ready(function() {

            // Initialize DeviceSerialModal
            (function() {
                const _el = document.getElementById('DeviceSerialModal');
                let _modal = null;
                try {
                    if (_el && window.bootstrap && typeof window.bootstrap.Modal === 'function') {
                        _modal = new bootstrap.Modal(_el);
                    }
                } catch (e) {
                    console.warn('DeviceSerialModal init failed', e);
                }

                window._deviceSerialModal = _modal;

                $(document).on('click', '#device_serial, #device_serial_btn_modal', function() {
                    if (_modal) {
                        _modal.show();
                        return;
                    }
                });
            })();

            // --- Device serial modal UX & validation ---
            (function() {
                const normalize = s => (s || '').toString().trim().toLowerCase();

                window.updateSerialCountForSection = function($section) {
                    const qty = parseInt($section.attr('data-qty')) || 0;
                    const filled = $section.find('.part-serial-input').filter(function() {
                        return $(this).val().trim() !== '';
                    }).length;
                    const $display = $section.find('.serial-count-display');
                    $display.text(filled + ' of ' + qty);
                    $display.toggleClass('complete', filled === qty && qty > 0);
                    $display.toggleClass('incomplete', filled > 0 && filled < qty);
                };

                // Enter navigation
                $(document).on('keydown', '.part-serial-input', function(e) {
                    if (e.key !== 'Enter') return;
                    e.preventDefault();
                    const $current = $(this).closest('.serial-input-row');
                    const $next = $current.next().find('.part-serial-input');
                    if ($next.length) {
                        $next.focus();
                        return;
                    }
                    const $section = $(this).closest('.part-serial-section');
                    const $nextSection = $section.nextAll('.part-serial-section').first();
                    if ($nextSection.length) {
                        const $first = $nextSection.find('.part-serial-input').first();
                        if ($first.length) {
                            $first.focus();
                            return;
                        }
                    }
                    $('#btn_save_all_serials').focus();
                });

                // clear invalid state while typing
                $(document).on('input', '.part-serial-input', function() {
                    $(this).removeClass('is-invalid');
                });

                // Duplicate check on blur/change
                $(document).on('blur change', '.part-serial-input', function() {
                    const $this = $(this);
                    const val = $this.val().trim();
                    if (!val) {
                        updateSerialCountForSection($this.closest('.part-serial-section'));
                        $this.removeClass('is-invalid');
                        return;
                    }
                    const key = normalize(val);
                    let duplicate = false;
                    $('.part-serial-input').not($this).each(function() {
                        if (normalize($(this).val()) === key) {
                            duplicate = true;
                            return false;
                        }
                    });
                    if (!duplicate) {
                        $('input[name="serial_no[]"]').each(function() {
                            const v = $(this).val() || '';
                            v.split(',').map(s => s.trim()).forEach(function(tok) {
                                if (!duplicate && normalize(tok) === key && tok !==
                                    '') {
                                    duplicate = true;
                                }
                            });
                            if (duplicate) return false;
                        });
                    }
                    if (duplicate) {
                        toastr.error('Duplicate serial detected — remove or change it.');
                        $this.addClass('is-invalid');
                        $this.val('');
                        $this.focus();
                    } else {
                        $this.removeClass('is-invalid');
                    }
                    updateSerialCountForSection($this.closest('.part-serial-section'));
                });

                // Final validation & save
                $(document).on('click', '#btn_save_all_serials', function() {
                    const seen = {};
                    let dupFound = false;

                    function markSeen(str) {
                        const k = normalize(str);
                        if (!k) return true;
                        if (seen[k]) {
                            dupFound = true;
                            return false;
                        }
                        seen[k] = true;
                        return true;
                    }
                    $('input[name="serial_no[]"]').each(function() {
                        const v = $(this).val() || '';
                        v.split(',').map(s => s.trim()).forEach(s => {
                            if (s) markSeen(s);
                        });
                    });
                    $('.part-serial-input').each(function() {
                        const v = $(this).val().trim();
                        if (!v) return;
                        if (!markSeen(v)) return false;
                    });
                    if (dupFound) {
                        toastr.error(
                            'Duplicate serial numbers found. Remove duplicates before saving.');
                        return;
                    }
                    const summary = [];
                    $('.part-serial-section').each(function() {
                        const $sec = $(this);
                        const part = $sec.attr('data-part-number');
                        const serials = [];
                        $sec.find('.part-serial-input').each(function() {
                            const v = $(this).val().trim();
                            if (v) serials.push(v);
                        });
                        if (serials.length) {
                            summary.push(part + ': ' + serials.join(', '));
                            // update matching table rows
                            $('#myTable tbody tr').each(function() {
                                const partTxt = $(this).find(
                                    'input[name="part_number_txt[]"]').val();
                                if (partTxt && partTxt.trim() === part) {
                                    $(this).find('input[name="serial_no[]"]').val(
                                        serials.join(', '));
                                }
                            });
                        }
                        updateSerialCountForSection($sec);
                    });
                    $('#device_serial').val(summary.join(' | '));
                    if (typeof bootstrap !== 'undefined' && $('#DeviceSerialModal').hasClass('show')) {
                        $('#DeviceSerialModal').modal('hide');
                    }
                    toastr.success('Serial numbers saved');
                });

                // Initialize counts when modal opens
                $(document).on('shown.bs.modal', '#DeviceSerialModal', function() {
                    $('.part-serial-section').each(function() {
                        updateSerialCountForSection($(this));
                    });
                    const $firstEmpty = $('.part-serial-input').filter(function() {
                        return $(this).val().trim() === '';
                    }).first();
                    if ($firstEmpty.length) $firstEmpty.focus();
                });
            })();

        });
    </script>

    <script src="{{ asset('public/js/form-validation-toastr.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize form validation for crm-deals-form
            FormValidator.init('delivery-note-create-form', {
                showAllErrors: true,
                scrollToFirst: true,
                highlightFields: true,
                toastrPosition: 'toast-top-right',
                toastrTimeout: 6000
            });
        });
    </script>


    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

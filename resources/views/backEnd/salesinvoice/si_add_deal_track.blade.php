<?php try { ?>

{{ Form::open([
        'class' => 'form-horizontal',
        'files' => true,
        'url' => 'sales-invoice-store',
        'method' => 'POST',
        'id' => 'sales-invoice-create-form',
        'novalidate' => true,
    ]) }}
<input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
<input type="hidden" name="id" value="{{ isset($edit) ? $edit->id : '' }}">


<input type="hidden" id="net_vat" name="net_vat" value="">
<input type="hidden" name="deal_track_page" id="deal_track_page" value="deal_track_page">

<?php
    $invno = @App\SysHelper::get_new_sales_invoice_code();
?>

<div class="purchase-order-content-header">
    <h4 class="purchase-order-content-header-left">
        New ({{ isset($edit) ? (!empty(@$edit->title) ? @$edit->title : old('doc_number')) : $invno }})
    </h4>
    <div class="purchase-order-content-header-right">
        <button type="submit" class="btn btn-light">
            <i class="ico icon-outline-bookmark-opened text-success"></i> Save
        </button>
        <div class="dropdown">
            <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button" data-bs-toggle="dropdown"
                aria-expanded="false">
                <i class="ico icon-outline-hamburger-menu"></i>
            </button>
            <ul class="dropdown-menu">
                <li><button class="dropdown-item"><i class="ico icon-outline-document-medicine text-success"></i> Save &
                        Download</button></li>
                <li><button type="button" class="dropdown-item" data-modal-size="modal-md"
                        data-bs-target="#attachment_popup_win" data-bs-toggle="modal" class="btn btn-primary"
                        onclick="view_attachment()"><i
                            class="ico icon-outline-calculator-minimalistic text-warning"></i> Attachment</button></li>
                <li><button type="button" class="dropdown-item" onclick="get_adjustments()"><i
                            class="ico icon-outline-calculator-minimalistic text-danger"></i> Adjustment</button></li>
            </ul>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <div class="row gap-rows">
            <div class="col-4">
                <label class="form-label mb-0 d-flex justify-content-between align-items-center">
                    <span>Customer</span>
                    <a href="{{ url('customers?customer_action=add') }}" target="__blank" class="btn btn-sm p-0 ms-2"
                        style="border:none;background:none;">
                        <i class="ico icon-outline-add-square text-success" style="font-size:18px;"></i>
                    </a>
                </label>
                <div class="form-group">
                    <select class="form-control js-account-select" name="customer" id="customer"
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
                        <option value="{{ @$value->id }}" {{ isset($edit) ? (!empty($edit->vendor_id) ?
                            (@$edit->vendor_id == @$value->id ? 'selected' : '') : '') : '' }}>
                            {{ @$value->account_name }}
                        </option>
                        @endforeach --}}
                    </select>
                </div>
            </div>
            <div class="col-2">
                <label class="form-label">Doc Number</label>
                <div class="form-group">


                    <input class="form-control" type="text" name="doc_number" autocomplete="off" id="doc_number"
                        value="{{ isset($edit) ? (!empty(@$edit->title) ? @$edit->title : old('doc_number')) : $invno }}"
                        readonly>
                </div>
            </div>
            <div class="col-2">
                <label class="form-label">Invoice Date</label>
                <div class="form-group">
                    @php
                        // Default: today's date in d/m/Y format
                        $value = date('d/m/Y');

                        if (isset($edit) && !empty($edit->doc_date)) {
                            // Convert stored MySQL date to d/m/Y for date-picker
                            $value = date('d/m/Y', strtotime($edit->doc_date));
                        } else {
                            if (!empty(old('doc_date'))) {
                                $value = old('doc_date'); // already in d/m/Y from user input
                            } else {
                                $value = date('d/m/Y');
                            }
                        }
                    @endphp

                    <input class="form-control date-picker" id="doc_date" type="text" autocomplete="off" name="doc_date"
                        value="{{ @$value }}" required>
                </div>
            </div>
            <div class="col-2">
                <label class="form-label">Currency</label>
                <div class="form-group">
                    <?php
    $currency1 = 1;
    if (session('logged_session_data.company_id') == 8) {
        $currency1 = 2;
    }
                    ?>
                    <select class="form-control js-example-basic-single" name="currency" id="currency">
                        {{-- <option data-display="@lang('Currency') *" value="">@lang('Currency') *</option> --}}

                        @foreach ($currency as $value)
                            <option value="{{ @$value->id }}" @if ($company->currency_id == $value->id) selected @endif>
                                {{ @$value->code }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-2">
                <label class="form-label">Created By</label>
                <div class="form-group">
                    <input class="form-control" type="text" name="createdby" autocomplete="off" id="createdby"
                        value="{{ isset($edit) ? (!empty(@$edit->number) ? @$edit->number : old('createdby')) : Auth::user()->full_name }}"
                        readonly>
                </div>
            </div>
        </div>
    </div>
</div>



<div class="tab-wrap mb-3">
    <ul class="nav nav-tabs" id="purchaseDetailsTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="extra-fields-tab" data-bs-toggle="tab" data-bs-target="#extra-fields"
                type="button" role="tab" aria-controls="extra-fields" aria-selected="true">Extra Fields</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="shipping-details-tab" data-bs-toggle="tab" data-bs-target="#shipping-details"
                type="button" role="tab" aria-controls="shipping-details" aria-selected="true">Shipping Details</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="vat-details-tab" data-bs-toggle="tab" data-bs-target="#vat-details"
                type="button" role="tab" aria-controls="vat-details" aria-selected="true">VAT Details</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="end-user-details-tab" data-bs-toggle="tab" data-bs-target="#end-user-details"
                type="button" role="tab" aria-controls="end-user-details" aria-selected="true">End User Details</button>
        </li>
    </ul>
    <div class="tab-content mb-3" id="purchaseDetailsTabsContent">
        <div class="tab-pane fade show active" id="extra-fields" role="tabpanel" aria-labelledby="extra-fields-tab">
            <div class="row gap-rows">


                <div class="col-2 mb-2">
                    <div class="input-effect">
                        <label class="txtlbl">Pending list</label>
                        <div id="plist"
                            style="width: 100%; height: 130px; border: solid 1px #dfe1d7; border-radius: 5px; padding: 5px 5px; overflow-y: scroll;">
                        </div>
                        <a data-modal-size="modal-md" data-target="#profo_pending_popup_win" id="addProfoPending"
                            data-toggle="modal"></a>
                        <input type="hidden" id="grn_id" name="profo_id">
                        <input type="hidden" id="vat_percentage" name="vat_percentage" value="5">
                    </div>
                </div>
                <div class="col-10 mb-2">
                    <div class="row">
                           <div class="col-lg-3 mb-2">
                            <div class="input-effect">
                                <label class="form-label">LPO/Reference No<span>*</span></label>
                                <input class="form-control" type="text" name="reference_no" autocomplete="off"
                                    id="reference_no" value="@if (count($cart) > 0) {{ $cart[0]->reference_no }} @endif"
                                    required>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-2">
                            <div class="input-effect">
                                <label class="form-label">LPO/Reference Date<span>*</span></label>
                                <input class="form-control date-picker" type="text" name="reference_date"
                                    autocomplete="off" id="reference_date"
                                    value="@if (count($cart) > 0) {{ $cart[0]->reference_date }} @endif" required>
                            </div>
                        </div>
                         <div class="col-lg-3 mb-2">
                            <div class="input-effect">
                                <label class="form-label">@lang('Payment Terms')<span>*</span></label>
                                <div class="form-group">
                                    <select class="form-control js-example-basic-single" name="payment_terms"
                                        id="payment_terms" required>
                                        <option value=""></option>
                                        @foreach ($paymentterms as $value)
                                                                        <option value="{{ @$value->id }}" {{ isset($edit)
                                            ? (!empty(@$edit->payment_terms)
                                                ? (@$edit->payment_terms == @$value->id
                                                    ? 'selected'
                                                    : '')
                                                : '')
                                            : '' }} @if (count($cart) > 0) @if ($cart[0]->payment_terms == @$value->id) selected @endif @endif>
                                                                            {{ @$value->title }}</option>
                                        @endforeach
                                    </select>


                                </div>

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
                        <div class="col-lg-3 mb-2">
                            <div class="input-effect">
                                <label class="form-label">Supplier Name<span>*</span></label>


                                @php
                                    $refSupplierIds = App\SysCrmDealTrackApprovalPurchease::where('deal_id', @$deal_det->id)
                                        ->whereNotNull('ref_supplier_id')
                                        ->where('ref_supplier_id', '!=', '')
                                        ->pluck('ref_supplier_id')
                                        ->first();


                                    $selectedIds = $refSupplierIds ? explode(',', $refSupplierIds) : [];
                                @endphp


                                <select class="form-control js-example-basic-single" name="ref_supplier_id[]"
                                    id="ref_supplier_id" multiple>
                                    <option value="">-Select-</option>
                                    <option value="TFS" @if ($selectedIds) @else selected @endif>TAKEN FROM STOCK
                                    </option>

                                    @foreach ($supplier_reference_list as $value)
                                        <option value="{{ $value->id }}" @if(in_array($value->id, $selectedIds)) selected
                                        @endif>
                                            {{ $value->account_name }}
                                            @if(@App\SysHelper::getCompanyCodeSettings()['is_supplier_code'])
                                                ({{ $value->account_code }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>



                                <input class="form-control" type="hidden" name="supplier_name" autocomplete="off"
                                    id="supplier_name" value="TAKEN FROM STOCK" required>
                            </div>
                        </div>
                         <div class="col-lg-3 mb-2">
                            <div class="input-effect">
                                <label class="form-label">@lang('Sales Person Name')<span>*</span></label>
                                <div class="form-group">
                                    <select class="form-control js-example-basic-single" required name="sales_man"
                                        id="sales_man" required>
                                        <option value=""></option>
                                        @foreach ($staff as $value)
                                            {{-- <option value="{{ @$value->user_id }}" @if (isset($edit)) @if ($edit->
                                                sales_man == $value->user_id) selected @endif @endif @if ($value->user_id ==
                                                Auth::user()->id) selected @endif @if (isset($deal_details))
                                                @if (isset($deal_details->owner) == Auth::user()->id) selected @endif @endif
                                                >{{ @$value->full_name }}
                                            </option> --}}
                                            <option value="{{ $value->user_id }}" @if (isset($edit) && $edit->sales_man == $value->user_id) selected
                                            @elseif($value->user_id == Auth::user()->id) selected
                                                @elseif(isset($deal_details) && $deal_details->owner == $value->user_id)
                                                selected @endif>
                                                {{ $value->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>


                            </div>
                        </div>
                        <div class="col-lg-3 mb-2" style="display: none;">
                            <div class="input-effect">
                                <label class="form-label">@lang('Delivery Terms')<span>*</span></label>
                                <input class="form-control" type="text" name="delivery_terms" autocomplete="off"
                                    id="delivery_terms"
                                    value="{{ isset($edit) ? (!empty(@$edit->delivery_terms) ? @$edit->delivery_terms : old('delivery_terms')) : 'Ex-Dubai' }}"
                                    required>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-2">
                            <div class="input-effect">
                                <label class="form-label">@lang('Printed Invoice Number')<span></span></label>
                                <input class="form-control" type="text" name="printed_invoice_number" autocomplete="off"
                                    id="printed_invoice_number"
                                    value="{{ isset($edit) ? (!empty(@$edit->printed_invoice_number) ? @$edit->printed_invoice_number : old('printed_invoice_number')) : '' }}">
                            </div>
                        </div>
                       
                       
                     

                        <div class="col-lg-3 mb-2" id="div_deal_id">
                            <div class="input-effect">
                                <label class="form-label">Deal ID<span>*</span></label>
                                <input class="form-control" type="text" name="deal_id" autocomplete="off" id="deal_id"
                                    value="@if(isset($deal_det)){{ @$deal_det->code }}@endif" required>
                            </div>
                        </div>
                        


                        <div class="col-lg-3 mb-2">
                            <div class="input-effect">
                                <label class="form-label">@lang('Create Delivery Note')</label>
                                <div class="form-group">
                                    <select class="form-control js-example-basic-single" name="create_dn" id="create_dn"
                                        required>
                                        <option value="">Select</option>
                                        <option value="0" selected>No</option>
                                        <option value="1">Yes</option>
                                    </select>


                                </div>

                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="input-effect">
                                <label class="form-label">Narration<span></span></label>
                                <input class="form-control" type="text" name="narration" autocomplete="off"
                                    id="narration" value="">
                            </div>
                        </div>
                    </div>
                </div>



            </div>
        </div>
        <div class="tab-pane fade show" id="shipping-details" role="tabpanel" aria-labelledby="shipping-details-tab">
            <div class="row gap-rows">


                <div class="col-3">
                    @php
                        $customer = @App\SysHelper::get_customer_supplier_list($company_id);

                        $customer_code = @App\SysCustSuppl::where('id', $deal_det->delivery_company)->first()->code ?? '';

                       


                       

                    @endphp
                    <label class="form-label">Company (Ship To)</label>
                    <div class="form-group">
                        <select class="form-control js-example-basic-single" name="shipping_supplier"
                            id="shipping_supplier" required style="width: 100%;">
                            <option value=""></option>
                            @foreach ($customer as $value)
                                {{-- @php $s = @App\SysHelper::internal_transfer_customer_id(@$value->id,
                                session('logged_session_data.company_id')); @endphp --}}

                                <option value="{{ @$value->id }}" @if ($customer_code == $value->account_code) selected @endif>
                                    {{ @$value->account_name }}
                                    @if(@App\SysHelper::getCompanyCodeSettings()['is_customer_code'])
                                        ({{ @$value->account_code }})
                                    @endif

                                </option>
                            @endforeach
                        </select>




                    </div>
                    <script>
                        $(document).ready(function () {
                            setTimeout(function () {
                                $("#shipping_supplier").trigger("change");
                            }, 300);
                        });
                    </script>
                </div>
                <div class="col-2">
                    <label class="form-label">Contact Name</label>
                    <div class="form-group">
                        <input type="text" class="form-control" name="shipping_name" id="shipping_name"
                            value="{{ $deal_det->delivery_name }}" />
                    </div>
                </div>
                <div class="col-2">
                    <label class="form-label">Contact Email</label>
                    <div class="form-group">
                        <input type="text" class="form-control" name="shipping_email" id="shipping_email"
                            value="{{ $deal_det->delivery_email }}" />
                    </div>
                </div>
                <div class="col-2">
                    <label class="form-label">Contact No</label>
                    <div class="form-group">
                        <input type="text" class="form-control" name="shipping_contact_no" id="shipping_contact_no"
                            value="{{ $deal_det->delivery_number }}" />
                    </div>
                </div>
                @php
                    $state = \App\SysStates::find($deal_det->delivery_state);
                    $country = \App\SysCountries::find($deal_det->delivery_country);

                    $shipping_address = collect([
                        $deal_det->delivery_flat_office_no ?? null,
                        $deal_det->delivery_building ?? null,
                        $deal_det->delivery_area ?? null,
                        $deal_det->delivery_city ?? null,
                        optional($state)->name,
                        optional($country)->name,
                    ])->filter()->implode(', ');
                @endphp
                <div class="col-3">
                    <label class="form-label">Shipping Address</label>
                    <div class="form-group">
                        <input type="text" class="form-control" name="shipping_address_1" id="shipping_address_1"
                            value="{{ $shipping_address }}" />
                    </div>
                </div>

                <!-- <div class="col-lg-3 mb-2">
                    <div class="input-effect">
                    
                        <label class="form-label">@lang('Name') <span></span></label>
                        <input type="text" class="form-control"
                            value="@if (isset($deal_det)) {{ @$deal_det->delivery_company }} @endif"
                            id="shipping_name" name="shipping_name">
                    </div>
                </div>
                <div class="col-lg-3 mb-2">
                    <div class="input-effect">
                        <label class="form-label">@lang('Address') <span></span></label>
                        <input type="text" class="form-control"
                            value="@if (isset($deal_det)) {{ @$deal_det->delivery_address }} @endif"
                            id="shipping_address" name="shipping_address">
                    </div>
                </div> -->
            </div>
        </div>
        <div class="tab-pane fade show" id="vat-details" role="tabpanel" aria-labelledby="vat-details-tab">
            <div class="row gap-rows">

                <div class="col-lg-2 mb-2">
                    <div class="input-effect">
                        <label class="form-label">@lang('Customer Country') <span></span></label>
                        <select class="form-control js-example-basic-single" name="customer_country" id="country">
                            <option value=""></option>
                            @foreach ($countries as $key => $value)
                                                    <option value="{{ @$value->id }}" <?php        try {?> @if (isset($deal_cust)) @if (@$deal_cust->vat_country == $value->id) selected @endif @endif <?php        } catch (\Throwable $th) {
                                } ?>>{{ @$value->name }}
                                                    </option>
                            @endforeach
                        </select>

                    </div>
                </div>


                <div class="col-lg-2 mb-2">
                    <div class="input-effect">
                        <label class="form-label">@lang('Customer State') <span></span></label>

                        <div id="sectionStateDiv">
                            <select class="form-control js-example-basic-single" name="customer_state" id="state">
                                <option value=""></option>

                                @foreach ($states as $value)
                                    <option value="{{ $value->id }}" {{ isset($deal_cust) && $deal_cust->vat_state == $value->id ? 'selected' : '' }}>
                                        {{ $value->name }}
                                    </option>
                                @endforeach
                            </select>

                        </div>

                    </div>
                </div>

                <div class="col-2">
                    <label class="form-label">VAT %</label>
                    <div class="form-group">

                        <input class="form-control" type="number" name="vat_percent" id="vat_percent" value="">
                    </div>
                </div>

                <div class="col-2">
                    <label class="form-label">VAT Number</label>
                    <div class="form-group">

                        <input class="form-control" type="number" name="vat_number" id="vat_number" value="">
                    </div>
                </div>

                <div class="col-lg-2 mb-2">
                    <div class="input-effect">
                        <label class="form-label">@lang('Customer Type')</label>
                        <div class="form-group">
                            <select class="form-control js-example-basic-single" name="customer_type"
                                id="customer_type">
                                <option value="0"></option>
                                @foreach ($customertype as $value)
                                                        <option value="{{ @$value->id }}" {{ isset($deal_cust)
                                    ? (!empty(@$deal_cust->customer_type)
                                        ? (@$deal_cust->customer_type == @$value->id
                                            ? 'selected'
                                            : '')
                                        : '')
                                    : '' }}>
                                                            {{ @$value->title }}
                                                        </option>
                                @endforeach
                            </select>


                        </div>

                    </div>
                </div>
                <div class="col-lg-2 mb-2">
                    <div class="input-effect">
                        <label class="form-label">@lang('Sale Type')</label>
                        <div class="form-group">
                            <select class="form-control js-example-basic-single" name="sale_type" id="sale_type">
                                <option value="0"></option>
                                @foreach ($saletype as $value)
                                                        <option value="{{ @$value->id }}" {{ isset($deal_cust)
                                    ? (!empty(@$deal_cust->sale_type)
                                        ? (@$deal_cust->sale_type == @$value->id
                                            ? 'selected'
                                            : '')
                                        : '')
                                    : '' }}>
                                                            {{ @$value->title }}
                                                        </option>
                                @endforeach
                            </select>


                        </div>

                    </div>
                </div>



            </div>
        </div>
        <div class="tab-pane fade show" id="end-user-details" role="tabpanel" aria-labelledby="end-user-details-tab">
            <div class="row gap-rows">

                <div class="col mb-2">
                    <div class="input-effect">
                        <label class="form-label">@lang('Company Name') <span></span></label>
                        <input type="text" class="form-control" name="end_user_name" id="end_user_name"
                            autocomplete="off"
                            value="@if (isset($deal_enduser)) {{ $deal_enduser->end_user_company_name }} @endif" />

                    </div>
                </div>
                <div class="col mb-2">
                    <div class="input-effect">
                        <label class="form-label">@lang('Contact Person') <span></span></label>
                        <input type="text" class="form-control" name="contact_person_name" id="contact_person_name"
                            autocomplete="off"
                            value="@if (isset($deal_enduser)) {{ $deal_enduser->end_user_contact_person }} @endif">

                    </div>
                </div>
                <div class="col mb-2">
                    <div class="input-effect">
                        <label class="form-label">@lang('Mobile No') <span></span></label>
                        <input type="text" class="form-control" name="contact_person_no" id="contact_person_no"
                            autocomplete="off" value="@if (isset($deal_enduser)) {{ $deal_enduser->mobile_no }} @endif">

                    </div>
                </div>
                <div class="col mb-2">
                    <div class="input-effect">
                        <label class="form-label">@lang('Email') <span></span></label>
                        <input type="text" class="form-control" name="contact_person_email" id="contact_person_email"
                            autocomplete="off" value="@if (isset($deal_enduser)) {{ $deal_enduser->email }} @endif">

                    </div>
                </div>


                @if(!empty($deal_enduser->device_serial))
                @php
                    $devices_2 = json_decode($deal_enduser->device_serial, true);

                    // build human-readable summary for the visible `device_serial` field
                    $deviceSerialSummary = '';
                    if (!empty($devices_2) && is_array($devices_2)) {
                        $parts = [];
                        foreach ($devices_2 as $part => $vals) {
                            $vals = (array) $vals;
                            $vals = array_filter(array_map('trim', $vals), function($x) { return $x !== null && $x !== ''; });
                            if (count($vals)) $parts[] = @App\SmItem::where('part_number', $part)->first()->id . ': ' . implode(', ', $vals);
                        }
                        $deviceSerialSummary = implode(' | ', $parts);
                    }
                @endphp
                    <div class="col">
                        <div class="mb-3">
                            <label for="" class="form-label">Device Serial</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="device_serial"
                                    value="{{ $deviceSerialSummary }}" id="device_serial" data-bs-toggle="modal"
                                    data-bs-target="#DeviceSerialModal" readonly style="cursor:pointer;" />
                                <button type="button" class="btn btn-light border" data-bs-toggle="modal"
                                    data-bs-target="#DeviceSerialModal">
                                    <i class="ico icon-outline-list-down"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif


            </div>
        </div>
    </div>
</div>
<div class="table-container" style="border: solid 1px #d9d9d9;">
    <table class="table table-hover form-item-table" id="myTable">
        <thead>
            <tr>
                <th class="resizable text-center" width="30px">@lang('No')
                    <div class="resizer"></div>
                </th>
                <th class="resizable text-center" width="210px">@lang('Part No') <a
                        class="icon icon-outline-book text-dark" data-bs-toggle="modal"
                        data-bs-target="#addproductModal"></a>
                    <div class="resizer"></div>
                </th>
                <th class="resizable text-center" width="280px">@lang('Description')
                    <div class="resizer"></div>
                </th>

                @if (count($cart) == 0)
                    <th class="resizable text-center" width="50px">@lang('Cost')
                        <div class="resizer"></div>
                    </th>
                @endif

                <th class="resizable text-center" width="30px">@lang('Tax')
                    <div class="resizer"></div>
                </th>
                <th class="resizable text-center" width="30px">@lang('Qty')
                    <div class="resizer"></div>
                </th>
                <th class="resizable text-center" width="80px">@lang('Price')
                    <div class="resizer"></div>
                </th>
                <th class="resizable text-center" width="80px">@lang('Value')
                    <div class="resizer"></div>
                </th>
                <th class="resizable text-center" width="80px" scope="col">Dis <a
                        class="icon icon-outline-book text-dark" data-bs-toggle="modal"
                        data-bs-target="#discountModal"></a>
                    <div class="resizer"></div>
                </th>
                <th class="resizable text-center" width="80px">@lang('Taxable')
                    <div class="resizer"></div>
                </th>
                <th class="resizable text-center" width="80px">@lang('VAT')
                    <div class="resizer"></div>
                </th>
                <th class="resizable text-center" width="100px">@lang('Total')
                    <div class="resizer"></div>
                </th>
                <th class="resizable text-center" width="100px">@lang('SRL No')
                    <div class="resizer"></div>
                </th>
            </tr>
        </thead>
        <tbody>

            <?php    $sort = 1; ?>

            @if (count($cart) > 0)
                @foreach ($cart as $items)
                    <tr>
                        <td><input type="text" class="form-control text-center" name="sort_id[]" value="{{ $sort }}" /></td>
                        <td class="noborder">
                            <select class="form-control noborder " name="part_number[]">
                                <option value="{{ $items->part_number }}">{{ $items->part_number_txt }}</option>
                            </select>
                            {{-- on focus add this class and its funcanalities js-product-select --}}
                        </td>
                        <td>
                            <textarea class="form-control" name="description[]" rows="1">{{ $items->description }}</textarea>
                        </td>
                        <td style="display: none;">
                            <input class="form-control text-end" type="text" name="cost[]" autocomplete="off"
                                onblur="formatCurrency(this)" value="0">
                            <input class="form-control" type="text" name="part_number_txt[]" autocomplete="off" readonly="true"
                                hidden>
                            <input class="form-control" type="text" name="hscode_txt[]" autocomplete="off" readonly="true"
                                hidden>
                            <input class="form-control" type="text" name="product_type[]" autocomplete="off" readonly="true"
                                hidden>
                            <input class="form-control" type="text" name="product_type_part_number_text[]" autocomplete="off"
                                readonly="true" hidden>
                        </td>
                        <td><input type="number" class="form-control text-center" name="tax[]" onchange="calc_change_new(this)"
                                value="{{ number_format($items->tax, 0) }}"></td>
                        <td><input class="form-control text-center" type="number" name="qty[]" autocomplete="off" min="0"
                                onchange="calc_change_new(this)" value="{{ $items->qty }}"></td>
                        <td><input class="form-control text-end" type="text" name="unitprice[]" step="any" autocomplete="off"
                                min="0" onchange="calc_change_new(this)" onblur="formatCurrency(this)"
                                value="{{ @App\SysHelper::com_curr_format($items->unitprice, 2, '.', ',') }}"></td>
                        <td><input class="form-control text-end" type="text" name="value[]" autocomplete="off" min="0" readonly
                                value="{{ @App\SysHelper::com_curr_format($items->value, 2, '.', ',') }}"></td>
                        <td><input class="form-control text-end" type="text" step="Any" name="discount[]" autocomplete="off"
                                min="0" onchange="calc_change_new(this)" onblur="formatCurrency(this)"
                                value="{{ @App\SysHelper::com_curr_format($items->discount, 2, '.', ',') }}"></td>
                        <td><input class="form-control text-end" type="text" name="taxableamount[]" autocomplete="off" min="0"
                                readonly value="{{ @App\SysHelper::com_curr_format($items->taxableamount, 2, '.', ',') }}"></td>
                        <td><input class="form-control text-end" type="text" name="vatamount[]" autocomplete="off" min="0"
                                readonly value="{{ @App\SysHelper::com_curr_format($items->vatamount, 2, '.', ',') }}"></td>
                        <td><input class="form-control text-end" type="text" name="totalamount[]" autocomplete="off" min="0"
                                readonly
                                value="{{ @App\SysHelper::com_curr_format($items->vatamount + $items->taxableamount, 2, '.', ',') }}">
                        </td>
                        <td><input class="form-control text-end" type="text" name="serial_no[]" value="{{ isset($devices_2[$items->part_number_txt]) ? implode(', ', (array) $devices_2[$items->part_number_txt]) : (isset($items->serialno) ? $items->serialno : '') }}"></td>
                    </tr>

                    <?php            $sort++; ?>
                @endforeach
            @endif


            <tr>
                <td><input type="text" class="form-control text-center" name="sort_id[]" value="{{ $sort }}" /></td>
                <td class="noborder">
                    <select class="form-control noborder " name="part_number[]">
                    </select>
                    {{-- on focus add this class and its funcanalities js-product-select --}}
                </td>
                <td>
                    <textarea class="form-control" name="description[]" rows="1"></textarea>
                </td>

                <td style="@if (count($cart) > 0) display:none; @endif">
                    <input class="form-control text-end" type="text" name="cost[]" autocomplete="off"
                        onblur="formatCurrency(this)">
                    <input class="form-control" type="text" name="part_number_txt[]" autocomplete="off" readonly="true"
                        hidden>
                    <input class="form-control" type="text" name="hscode_txt[]" autocomplete="off" readonly="true"
                        hidden>
                    <input class="form-control" type="text" name="product_type[]" autocomplete="off" readonly="true"
                        hidden>
                    <input class="form-control" type="text" name="product_type_part_number_text[]" autocomplete="off"
                        readonly="true" hidden>
                </td>
                <td><input type="number" class="form-control text-center" name="tax[]" onchange="calc_change_new(this)">
                </td>
                <td><input class="form-control text-center" type="number" name="qty[]" autocomplete="off" min="0"
                        onchange="calc_change_new(this)"></td>
                <td><input class="form-control text-end" type="text" name="unitprice[]" step="any" autocomplete="off"
                        min="0" onchange="calc_change_new(this)" onblur="formatCurrency(this)"></td>
                <td><input class="form-control text-end" type="text" name="value[]" autocomplete="off" min="0" readonly>
                </td>
                <td><input class="form-control text-end" type="text" step="Any" name="discount[]" autocomplete="off"
                        min="0" onchange="calc_change_new(this)" onblur="formatCurrency(this)"></td>
                <td><input class="form-control text-end" type="text" name="taxableamount[]" autocomplete="off" min="0"
                        readonly></td>
                <td><input class="form-control text-end" type="text" name="vatamount[]" autocomplete="off" min="0"
                        readonly></td>
                <td><input class="form-control text-end" type="text" name="totalamount[]" autocomplete="off" min="0"
                        readonly></td>
                <td><input class="form-control text-end" type="text" name="serial_no[]"></td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="@if (count($cart) == 0) 5 @else 4 @endif" scope="col">Total</th>
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



<div class="equipment comon-status row mt-4 d-block">
            <style>
                /* keep freight table columns fixed width even when long values are entered */
                #fright_table { table-layout: fixed; }
                #fright_table th, #fright_table td { overflow: hidden; }
                #fright_table input, #fright_table select { width: 100%; box-sizing: border-box; }
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
                            <!-- header plus clones last row -->
                            <a style="cursor: pointer;" class="btn-md float-right" data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                            data-bs-content="Add new freight charge row"
                            data-bs-placement="bottom" onclick="add_fright()"><i class="ico icon-outline-add-square text-success"></i></a></th>
                    </tr>

                </thead>
                <tbody>
                    @php
                        $dealTrackCharges = session('deal_track_si_charge', []);
                        $hasDealTrackCharges = !empty($dealTrackCharges['selling_exp_account_id']);
                    @endphp

                    @if($hasDealTrackCharges)
                        @foreach($dealTrackCharges['selling_exp_account_id'] as $idx => $sellExpId)
                            <tr id="fright_row_{{ $idx + 1 }}">
                                <td>
                                    <input class="form-control date-picker" type="text" id="cfc_date_{{ $idx + 1 }}" name="cfc_date[]" value="" autocomplete="off">
                                </td>
                                <td>
                                    <input class="form-control" type="text" id="cfc_bill_no_{{ $idx + 1 }}" name="cfc_bill_no[]" value="" autocomplete="off">
                                </td>
                                <td>
                                    <select class="form-control js-example-basic-single" name="cfc_name[]" id="cfc_name_{{ $idx + 1 }}">
                                        <option value=""></option>
                                        @foreach ($customs_freight_account as $key => $value)
                                            <option value="{{ @$value->id }}" @if($sellExpId == @$value->id) selected @endif>
                                                {{ @$value->account_name }} @if (@App\SysHelper::getCompanyCodeSettings()['is_account_code'])
                                                    ({{ @$value->account_code }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control js-example-basic-single" name="cfc_credit_account[]" id="cfc_credit_account_{{ $idx + 1 }}">
                                        <option value=""></option>
                                        @foreach ($supplier as $key => $value)
                                            <option value="{{ @$value->id }}" @if(($dealTrackCharges['selling_exp_credit_account'][$idx] ?? '') == @$value->id) selected @endif>
                                                {{ @$value->account_name }} @if (@App\SysHelper::getCompanyCodeSettings()['is_supplier_code'])
                                                    ({{ @$value->account_code }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input class="form-control text-end" type="text" id="cfc_amount_{{ $idx + 1 }}" name="cfc_amount[]" value="{{ $dealTrackCharges['selling_exp_account_amount'][$idx] ?? '' }}" autocomplete="off" min="0">
                                </td>
                                <td>
                                    <input class="form-control" type="text" id="cfc_remarks_{{ $idx + 1 }}" name="cfc_remarks[]" value="{{ $dealTrackCharges['selling_exp_remarks'][$idx] ?? '' }}" autocomplete="off">
                                </td>
                            </tr>
                        @endforeach
                    @else
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
                                    @foreach ($customs_freight_account as $key => $value)
                                        <option value="{{ @$value->id }}">{{ @$value->account_name }}  @if (@App\SysHelper::getCompanyCodeSettings()['is_account_code'])
                                                 ({{ @$value->account_code }})
                                                
                                            @endif</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select class="form-control js-example-basic-single" name="cfc_credit_account[]" id="cfc_credit_account_1"
                                    readonly="true">
                                    <option value="none"></option>
                                     @foreach ($supplier as $key => $value)
                                                        <option value="{{ @$value->id }}">{{ @$value->account_name }} @if (@App\SysHelper::getCompanyCodeSettings()['is_supplier_code'])
                                                 ({{ @$value->account_code }})
                                                
                                            @endif</option>
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
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4"></th>
                        <th class="text-end" id="fright_total_amount">0</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>



<br>

    <!-- freight row add/duplicate functions -->
        <script>
            $(document).ready(function() {
                window.add_fright = function() {
                    var id = parseInt($('#fright_row').val()) || 0;
                    id = id + 1;
                    $('#fright_row').val(id);
                    var $last = $('#fright_table tbody tr:last');

                    // remove flatpickr instance from original so clone won't carry calendar markup
                    $last.find('.date-picker').each(function() {
                        if (this._flatpickr) {
                            this._flatpickr.destroy();
                        }
                    });
                    // temporarily destroy select2 on original so clone is clean
                    $last.find('.js-example-basic-single').select2('destroy');

                    var $new = $last.clone();

                    // reinit select2 on original row
                    $last.find('.js-example-basic-single').select2({width:'100%'});
                    // reinit flatpickr on original row
                    $last.find('.date-picker').each(function() {
                        flatpickr(this, {dateFormat: 'd/m/Y', allowInput: true});
                    });

                    $new.attr('id','fright_row_'+id);
                    $new.find('select, input').each(function(){
                        var elem = $(this);
                        var oldId = elem.attr('id');
                        if(oldId){
                            var base = oldId.substring(0, oldId.lastIndexOf('_')+1);
                            elem.attr('id', base + id);
                        }
                        elem.val('');
                    });
                    $('#fright_table tbody').append($new);
                    // initialize select2 on any new selects
                    $new.find('.js-example-basic-single').select2({width:'100%'});
                    // initialize flatpickr on new inputs
                    $new.find('.date-picker').each(function() {
                        flatpickr(this, {dateFormat: 'd/m/Y', allowInput: true});
                    });
                    updateFrightTotals();
                };

                window.duplicateFrightRow = function(el) {
                    var $row = $(el).closest('tr');
                    var id = parseInt($('#fright_row').val()) || 0;
                    id = id + 1;
                    $('#fright_row').val(id);

                    // destroy existing flatpickr instance on this row before cloning
                    $row.find('.date-picker').each(function() {
                        if (this._flatpickr) {
                            this._flatpickr.destroy();
                        }
                    });
                    // destroy existing select2 on this row before cloning
                    $row.find('.js-example-basic-single').select2('destroy');

                    var $new = $row.clone();
                    // reinit select2 on original row
                    $row.find('.js-example-basic-single').select2({width:'100%'});
                    // reinit flatpickr on original row
                    $row.find('.date-picker').each(function() {
                        flatpickr(this, {dateFormat: 'd/m/Y', allowInput: true});
                    });

                    $new.attr('id','fright_row_'+id);
                    $new.find('select, input').each(function(){
                        var elem = $(this);
                        var oldId = elem.attr('id');
                        if(oldId){
                            var base = oldId.substring(0, oldId.lastIndexOf('_')+1);
                            elem.attr('id', base + id);
                        }
                        elem.val('');
                    });
                    $('#fright_table tbody').append($new);
                    // initialize select2 on cloned elements
                    $new.find('.js-example-basic-single').select2({width:'100%'});
                    // initialize flatpickr on new inputs
                    $new.find('.date-picker').each(function() {
                        flatpickr(this, {dateFormat: 'd/m/Y', allowInput: true});
                    });
                    updateFrightTotals();
                };

                // calculate freight sum
                function updateFrightTotals() {
                    var total = 0;
                    $('#fright_table tbody tr').each(function() {
                        var val = $(this).find('input[name="cfc_amount[]"]').val().replace(/,/g,'') || '0';
                        total += parseFloat(val) || 0;
                    });
                    $('#fright_total_amount').text(formatAmount(total));
                }

                // recalc when amount field edited
                $(document).on('input', 'input[name="cfc_amount[]"]', function() {
                    updateFrightTotals();
                });
                // format and recalc on blur
                $(document).on('blur', 'input[name="cfc_amount[]"]', function() {
                    this.value = formatAmount(this.value);
                    updateFrightTotals();
                });

                // initialize totals on load
                updateFrightTotals();
            });
        </script>

{{ Form::close() }}



{{-- Models --}}
<!-- <a data-bs-toggle="modal" data-bs-target="#editModal"></a> -->

@include('backEnd.inventory.itemAddModal')


<script>
    $(document).on("keydown", 'input[name="tax[]"], input[name="qty[]"], input[name="unitprice[]"], input[name="discount[]"], input[name="fright[]"], input[name="customcharges[]"], input[name="serial_no[]"]', function (e) {
        if (e.key === "Enter") {
            e.preventDefault(); // prevent form submit

            let row = $(this).closest("tr"); // current row
            let name = $(this).attr("name");

            if (name === "tax[]") {
                row.find('input[name="qty[]"]').focus();
            }
            else if (name === "qty[]") {
                row.find('input[name="unitprice[]"]').focus();
            }
            else if (name === "unitprice[]") {
                row.find('input[name="discount[]"]').focus();
            }
            else if (name === "discount[]") {
                row.find('input[name="fright[]"]').focus();
            }
            else if (name === "fright[]") {
                row.find('input[name="customcharges[]"]').focus();
            }
            else if (name === "customcharges[]") {
                row.find('input[name="serial_no[]"]').focus();
            }
        }
    });
</script>


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
                                    <textarea type="text" class="form-control" id="add_description"
                                        style="height: 150px;"></textarea>
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
                                <label class="form-label">Serial No:</label>
                                <div class="form-group">
                                    <textarea type="text" class="form-control" id="add_serial_no" autofocus
                                        style="height: 150px;"></textarea>
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

<script>
    $(document).ready(function () {
        // Hide SRL No column initially if needed
        toggleSrlColumn($('#create_dn').val());

        // On dropdown change
        $('#create_dn').on('change', function () {
            toggleSrlColumn($(this).val());
        });

        // Function to show/hide SRL No column
        function toggleSrlColumn(value) {
            if (value == '1') {
                // Show SRL No column (last column)
                $('#myTable th:last-child, #myTable td:last-child').show();
            } else {
                // Hide SRL No column (last column)
                $('#myTable th:last-child, #myTable td:last-child').hide();
            }
        }
    });
</script>

<script>
    $(document).on("keydown",
        'input[name="qty[]"], input[name="unitprice[]"], input[name="discount[]"], input[name="serial_no[]"]',
        function (e) {
            if (e.key === "Enter") {
                e.preventDefault(); // prevent form submit

                let row = $(this).closest("tr"); // get current row
                let name = $(this).attr("name");

                if (name === "qty[]") {
                    row.find('input[name="unitprice[]"]').focus();
                } else if (name === "unitprice[]") {
                    row.find('input[name="discount[]"]').focus();
                } else if (name === "discount[]") {
                    row.find('input[name="serial_no[]"]').focus();
                }
            }
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

    document.getElementById("discount_add_btn").addEventListener("click", function () {
        splitAmount('discountInput', 'discount');
        $('#discountModal').modal('hide');
    });
</script>

<script>
    let serialNoModal;
    let currentSerialInput = null;

    document.addEventListener("DOMContentLoaded", function () {
        const modalElement = document.getElementById('serialNoModal');
        serialNoModal = new bootstrap.Modal(modalElement);

        // 🔥 Focus input when modal is shown
        modalElement.addEventListener('shown.bs.modal', function () {
            $('#add_serial_no').trigger('focus');
        });
    });

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
    let descriptionModal;
    document.addEventListener("DOMContentLoaded", function () {
        const descriptionElement = document.getElementById('descriptionModal');
        descriptionModal = new bootstrap.Modal(descriptionElement);
    });
    let currentDescriptionInput = null;

    $(document).on('click', 'textarea[name="description[]"]', function () {
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

        $('#myTable tbody tr').each(function () {
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
        //$('#lbl_total_fright').text(formatAmount(total_fright));
        //$('#lbl_total_customcharges').text(formatAmount(total_customcharges));
        $('#lbl_total_taxableamount').text(formatAmount(total_taxableamount));
        $('#lbl_total_vatamount').text(formatAmount(total_vatamount));
        $('#lbl_total_totalamount').text(formatAmount(total_totalamount));
    }
</script>
<script>
    update_totals();

    
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


    const SHOW_CUSTOMER_CODE = {{ @App\SysHelper:: getCompanyCodeSettings()['is_customer_code'] ? 'true' : 'false' }};


    $(document).ready(function () {
        function initAccountSelect2(selector) {
            $(selector).select2({
                ajax: {
                    url: '{{ route('autocomplete.get_cust_account_list_ajax') }}',
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
        $(document).on('focus', '.js-account-select', function () {
            if (!$(this).hasClass("select2-hidden-accessible")) {
                initAccountSelect2(this);
                $(this).select2('open');
            }
        });

        // Open dropdown and focus search box on click
        $(document).on('click', '.js-account-select', function () {
            $(this).select2('open');
        });

        // Focus the search input inside the opened Select2 dropdown
        $(document).on('select2:open', function () {
            setTimeout(function () {
                const searchInput = document.querySelector(
                    '.select2-container--open .select2-search__field');
                if (searchInput) {
                    searchInput.focus();
                }
            }, 0);
        });

        // When any .js-account-select select2 opens, prefill the search box with the currently selected value
        $(document).on('select2:open', function (e) {
            // Find the select2 element that triggered the event
            var $select = $(document.activeElement).closest('.js-account-select');
            if ($select.length === 0) {
                // fallback: try to get the open dropdown's select
                $select = $('.js-account-select').filter(function () {
                    return $(this).data('select2') && $(this).data('select2').isOpen();
                });
            }
            if ($select.length > 0) {
                var sel = $select.select2('data');
                if (sel && sel.length && sel[0].text) {
                    setTimeout(function () {
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
    $(document).ready(function () {
        function initAccountSelect2(selector) {
            $(selector).select2({
                ajax: {
                    url: '{{ route('autocomplete.get_product_list_ajax') }}',
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
                placeholder: '',
                minimumInputLength: 2,
                dropdownParent: $(selector).parent() // optional: ensures dropdown shows in modals
            });

            $(selector).on('select2:select', function (e) {
                var selectedData = e.params.data;
                var $row = $(this).closest('tr'); // find the closest row

                // Set values using "name" attribute selectors inside the same row
                //$row.find('input[name="description[]"]').val(selectedData.description || '');
                $row.find('textarea[name="description[]"]').val(selectedData.description || '');
                $row.find('input[name="part_number_txt[]"]').val(selectedData.text || '');
                $row.find('input[name="hscode_txt[]"]').val(selectedData.hscode || '');
                $row.find('input[name="product_type[]"]').val(selectedData.product_type || '');
                $row.find('input[name="product_type_part_number_text[]"]').val(selectedData
                    .description || '');
                // $row.find('input[name="discount[]"]').val(0);
                $row.find('input[name="tax[]"]').val(parseInt($('#net_vat').val()));
                $row.find('input[name="qty[]"]').focus();

            });



            // prefill Select2 search with currently selected value when dropdown opens
            $(selector).on('select2:open', function () {
                try {
                    var sel = $(this).select2('data');
                    if (sel && sel.length && sel[0].text) {
                        setTimeout(function () {
                            const searchInput = document.querySelector('.select2-container--open .select2-search__field');
                            if (searchInput) {
                                searchInput.value = sel[0].text.trim();
                                // trigger input event so select2 filters on prefilling
                                var event = new Event('input', { bubbles: true });
                                searchInput.dispatchEvent(event);
                                try {
                                    var len = searchInput.value.length;
                                    searchInput.setSelectionRange(len, len);
                                } catch (err) { /* ignore */ }
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
        $(document).on('focus', '.js-product-select', function () {
            if (!$(this).hasClass("select2-hidden-accessible")) {
                initAccountSelect2(this);
                $(this).select2('open');
            }
        });

        // On click, open dropdown and focus on search field
        $(document).on('click', '.js-product-select', function () {
            $(this).select2('open');
        });

        // Optional: Auto focus on search input when dropdown opens
        $(document).on('select2:open', function () {
            setTimeout(function () {
                document.querySelector('.select2-container--open .select2-search__field')
                    ?.focus();
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



    function get_pending_si_list() {
        var id = $('#customer').select2('val'); // or .val()

        get_cust_details(id);
        get_cust_details_arabic(id);
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
            success: function (dataResult) {

                var dataResult = JSON.parse(dataResult);
                var len = 0;
                var len = 0;
                var state = null;
                if (dataResult['data'] != null) {
                    len = dataResult['data'].length;
                }
                if (len > 0) {
                    for (var i = 0; i < len; i++) {
                        if (dataResult['data'][i].status == 3) {
                            alert("Customer Information is incompleated! Please Update Customer.");
                            $('#btnSubmit').css('display', 'none');
                        } else {
                            $('#btnSubmit').css('display', '');
                        }
                        $('#payment_terms').val(dataResult['data'][i].payment_terms).trigger('change');
                        $('#shipping_name').val(dataResult['data'][i].contcat_person);
                        $('#shipping_address').val(dataResult['data'][i].address);
                        $('#customer_type').val(dataResult['data'][i].customer_type).trigger('change');
                        $('#sale_type').val(dataResult['data'][i].sale_type).trigger('change');
                        $('#country').val(dataResult['data'][i].vat_country).trigger('change');


                        window.SELECTED_STATE_ID = dataResult['data'][i].vat_state;

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
            success: function (dataResult) {
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                var len = 0;
                if (dataResult['data'] != null) {
                    len = dataResult['data'].length;
                }
                if (len > 0) {
                    for (var i = 0; i < len; i++) {
                        $('#company_name_ar').val(dataResult['data'][i].company_name_ar);
                        $('#contact_person_ar').val(dataResult['data'][i].contact_person_ar);
                        $('#address_ar').val(dataResult['data'][i].address_ar);
                    }
                } else {
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
            success: function (dataResult) {
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                var len = 0;
                if (dataResult['data'] != null) {
                    len = dataResult['data'].length;
                }
                if (len > 0) {
                    for (var i = 0; i < len; i++) {
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
                } else {
                    $("#plist").empty();
                }
                $("#loading_bg").css("display", "none");
            }
        });
    }
</script>

<div class="modal side-panel fade" id="attachment_popup_win" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header m-0 p-3">
                <h4 class="modal-title">Attachments - <label id="att_cust_name"></label></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-3">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-4 mb-20">
                            <div class="input-effect">
                                <label class="form-label"> @lang('Attach File') <span>*</span> </label>
                                <input class="form-control" type="file" id="att_file" name="att_file"
                                    onchange="updateDocName()" />
                            </div>
                        </div>
                        <div class="col-lg-4 mb-20">
                            <div class="input-effect">
                                <label class="form-label"> @lang('Date') <span>*</span> </label>
                                <input class="form-control" type="date" id="att_date" name="att_date"
                                    value="{{ date('Y-m-d') }}" />
                            </div>
                        </div>
                        <div class="col-lg-4 mb-20">
                            <div class="input-effect">
                                <label class="form-label"> @lang('File Name') <span>*</span> </label>
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
                            <table id="att-table" class="table table-hover form-item-table" width="100%"
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

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light add-btn ms-2" onclick="add_attachment()">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Add Attachment
                </button>
            </div>
        </div>
    </div>
</div>
<script>
    function add_attachment() {
        $("#loading_bg").css("display", "block");

        if ($('#att_file').val() == "") {
            $('#att_file').focus();
            $("#loading_bg").css("display", "none");
            return false;
        }

        var action = "{{ URL::to('add-sales-invoice-attachment') }}";

        var formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}'); // Append CSRF token
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
                                    <td>" + Number(i + 1) + "</td>\
                                    <td>" + get_format_date(dataResult['data'][i].doc_date) + "</td>\
                                <td><a href='../../" + dataResult['data'][i].doc_file + "' target='_blank'>" +
                            dataResult['data'][i].doc_name + "</a></td>\
                                    <td><button onclick='delete_attachment(" + dataResult['data'][i].id + ")' class='btn btn-sm btn-danger text-white'><i class='ico icon-bold-trash-bin-2' style='font-size:16px' aria-hidden='true'></i></button></td>\
                                    </tr>";
                    }
                    $('#att_file').val('');
                    $('#doc_name').val('');
                    $('#att-table tbody').empty();
                    $("#att-table tbody").append(getSelectedRows);
                } else {
                    $('#att-table tbody').empty();
                }
            }
        });
        $("#loading_bg").css("display", "none");
    }

    function view_attachment() {
        $("#loading_bg").css("display", "block");
        $('#att_cust_name').text($('#customer :selected').text() + " " + $('#doc_number').val());

        var action = "{{ URL::to('view-sales-invoice-attachment') }}";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                siv_id: 0,
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
                                    <td>" + Number(i + 1) + "</td>\
                                    <td>" + get_format_date(dataResult['data'][i].doc_date) + "</td>\
                                <td><a href='../../" + dataResult['data'][i].doc_file + "' target='_blank'>" +
                            dataResult['data'][i].doc_name + "</a></td>\
                                    <td><button onclick='delete_attachment(" + dataResult['data'][i].id + ")' class='btn btn-sm btn-danger text-white'><i class='ico icon-bold-trash-bin-2' style='font-size:16px' aria-hidden='true'></i></button></td>\
                                    </tr>";
                    }
                    $('#att_file').val('');
                    $('#doc_name').val('');
                    $('#att-table tbody').empty();
                    $("#att-table tbody").append(getSelectedRows);
                } else {
                    $('#att-table tbody').empty();
                }
            }
        });
        $("#loading_bg").css("display", "none");
    }

    function delete_attachment(id) {
        $("#loading_bg").css("display", "block");
        var action = "{{ URL::to('delete-sales-invoice-attachment') }}";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                id: id,
                siv_id: 0,
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
                                    <td>" + Number(i + 1) + "</td>\
                                    <td>" + get_format_date(dataResult['data'][i].doc_date) + "</td>\
                                <td><a href='../../" + dataResult['data'][i].doc_file + "' target='_blank'>" +
                            dataResult['data'][i].doc_name + "</a></td>\
                                    <td><button onclick='delete_attachment(" + dataResult['data'][i].id + ")' class='btn btn-sm btn-danger text-white'><i class='ico icon-bold-trash-bin-2' style='font-size:16px' aria-hidden='true'></i></button></td>\
                                    </tr>";
                    }
                    $('#att_file').val('');
                    $('#doc_name').val('');
                    $('#att-table tbody').empty();
                    $("#att-table tbody").append(getSelectedRows);
                } else {
                    $('#att-table tbody').empty();
                }
            }
        });
        $("#loading_bg").css("display", "none");
    }
</script>
<!-- Modal Adjustment-->
<script>
    function get_adjustments() {
        $("#loading_bg").css("display", "block");

        $('#adj_siv_amount_actual').val($("input[name='totalamount[]']").val());
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
            success: function (dataResult) {
                var data = JSON.parse(dataResult);
                // Handle 'unadjusted'
                if (data.unadjusted && data.unadjusted.length > 0) {
                    var getSelectedRows = "";
                    for (var i = 0; i < data.unadjusted.length; i++) {
                        var a = (data.unadjusted[i].amount - data.unadjusted[i].adj_amount).toFixed(
                            @json(session('logged_session_data.decimal_point'))).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                        getSelectedRows += "<tr>\
                                         <td class='border'>" + data.unadjusted[i].doc_date + "</td>\
                                         <td class='border'>" + data.unadjusted[i].doc_number + "</td>\
                                         <td class='border'>" + data.unadjusted[i].account_name + "</td>\
                                        <td class='border text-right'>" + a +
                            "</td>\
                                        <td class='border text-right'><input type='text' name='set_amt[]' id='set_amt_" + data.unadjusted[i].doc_number +
                            "' class='form-control text-right' value='' onclick=\"set_adjust('" + (data
                                .unadjusted[i].amount - data.unadjusted[i].adj_amount) + "','" + data
                                    .unadjusted[i].doc_number + "')\" />\
                                            <input type='hidden' name='receiptno[]' value='" + data.unadjusted[i]
                                .doc_number + "'/>\
                                            <input type='hidden' name='set_amt_act[]' value='" + a + "'/>\
                                        </td>\
                                        </tr>";
                    }

                }

                // Handle 'unadjusted_pdc'
                if (data.unadjusted_pdc && data.unadjusted_pdc.length > 0) {
                    var getSelectedRows2 = "";
                    for (var j = 0; j < data.unadjusted_pdc.length; j++) {
                        getSelectedRows2 += "<tr>\
                                         <td class='border'>" + data.unadjusted_pdc[i].doc_date + "</td>\
                                         <td class='border'>" + data.unadjusted_pdc[i].doc_number + "</td>\
                                         <td class='border'>" + data.unadjusted_pdc[i].account_name + "</td>\
                                        <td class='border text-right'>" + (data.unadjusted_pdc[i].amount - data
                                .unadjusted_pdc[i].adj_amount) +
                            "</td>\
                                        <td class='border text-right'><input type='text' name='set_amt[]' id='set_amt_" + data.unadjusted_pdc[i]
                                .doc_number + "' class='form-control text-right' value='" + data.unadjusted_pdc[
                                    i].adj_amount + "' onclick=\"set_adjust('" + (data.unadjusted_pdc[i]
                                        .amount - data.unadjusted_pdc[i].adj_amount) + "','" + data.unadjusted[i]
                                .doc_number + "')\" />\
                                            <input type='hidden' name='receiptno[]' value='" + data.unadjusted_pdc[i]
                                .doc_number + "'/>\
                                            <input type='hidden' name='set_amt_act[]' value='" + (data.unadjusted_pdc[
                                i].amount - data.unadjusted_pdc[i].adj_amount) + "'/>\
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
    $(document).ready(function () {
        $('#adjustmentForm').on('submit', function (e) {
            e.preventDefault();

            // Collect the form data
            let formData = $(this).serialize();

            // Optional: basic validation


            // AJAX submission
            $.ajax({
                url: "{{ url('sales-invoice-add-adjustment-cart') }}", // Replace with your actual route
                type: "POST",
                data: formData,
                success: function (response) {
                    // Handle success response
                    alert('Adjustment saved successfully.');
                    $('#ModalAdjustment').modal('hide'); // Hide modal if using Bootstrap
                },
                error: function (xhr) {
                    // Handle errors
                    alert('Error occurred while saving. Check console.');

                }
            });
        });
    });
</script>
<button type="button" id="btnModalAdjustment" data-bs-toggle="modal" data-bs-target="#ModalAdjustment" hidden></button>
<div class="modal side-panel fade" id="ModalAdjustment" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="height: 500px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel">Unadjusted List</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="adjustmentForm" method="POST">
                @csrf
                {{-- {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' =>
                'sales-invoice-update-adjustment', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }} --}}
                <div class="card-body" style="height: 420px; overflow-y: scroll;">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-hover form-item-table" id="adjustment_table">
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


                                    {{-- @if (count($list_of_unadjusted) > 0)
                                    @foreach ($list_of_unadjusted as $p)
                                    <tr>
                                        <td class="border">{{ date('d/m/Y', strtotime(@$p->doc_date)) }}</td>
                                        <td class="border"><a href="{{url('get-url-receipt/' . @$p->doc_number)}}"
                                                target="_blank">{{ @$p->doc_number }}</a></td>
                                        <td class="border">{{ @$p->account_name }}</td>
                                        <td class="border text-right">{{ @$p->amount-@$p->adj_amount }}</td>
                                        <td class="border text-right"><input type="text" name="set_amt[]"
                                                id="set_amt_{{ @$p->doc_number }}" class="form-control text-right" id=""
                                                name="" value="{{ @$p->adj_amount }}"
                                                onclick="set_adjust('{{ @$p->amount-@$p->adj_amount }}','{{ @$p->doc_number }}')" />
                                            <input type="hidden" name="receiptno[]" value="{{ @$p->doc_number }}" />
                                            <input type="hidden" name="set_amt_act[]"
                                                value="{{ @$p->amount-@$p->adj_amount }}" />
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                    @if (count($list_of_unadjusted_pdc) > 0)
                                    @foreach ($list_of_unadjusted_pdc as $p)
                                    <tr>
                                        <td class="border">{{ date('d/m/Y', strtotime(@$p->doc_date)) }}</td>
                                        <td class="border"><a href="{{url('get-url-receipt/' . @$p->doc_number)}}"
                                                target="_blank">{{ @$p->doc_number }}</a></td>
                                        <td class="border">{{ @$p->account_name }}</td>
                                        <td class="border text-right">{{ @$p->amount-@$p->adj_amount }}</td>
                                        <td class="border text-right"><input type="text" name="set_amt[]"
                                                id="set_amt_{{ @$p->doc_number }}" class="form-control text-right" id=""
                                                name="" value="{{ @$p->adj_amount }}"
                                                onclick="set_adjust('{{ @$p->amount-@$p->adj_amount }}','{{ @$p->doc_number }}')" />
                                            <input type="hidden" name="receiptno[]" value="{{ @$p->doc_number }}" />
                                            <input type="hidden" name="set_amt_act[]"
                                                value="{{ @$p->amount-@$p->adj_amount }}" />
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif --}}
                                </tbody>
                            </table>
                            <input type="hidden" id="adj_cus_id" name="adj_cus_id" value="" />
                            <input type="hidden" id="adj_siv_id" name="adj_siv_id" value="" />
                            <input type="hidden" id="adj_siv_no" name="adj_siv_no" value="" />
                            <input type="hidden" id="adj_siv_date" name="adj_siv_date" value="" />
                            <input type="hidden" id="adj_siv_amount" name="adj_siv_amount" value="" />
                            <input type="hidden" id="adj_siv_amount_actual" name="adj_siv_amount_actual" value="" />
                            <input type="hidden" id="adj_siv_amount_adjusted" name="adj_siv_amount_adjusted"
                                value="0" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-light add-btn ms-2" id="discount_add_btn">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Adjust
                    </button>
                </div>
                {{-- {{ Form::close() }} --}}
            </form>
        </div>
    </div>
</div>
<script>
    function set_adjust(amt, id) {
        let maxAdjustable = parseFloat($("input[name='adj_siv_amount_actual']").val());
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

        // Recalculate the adjusted total after the update
        currentAdjusted += adjustAmount;

        // Optional: update hidden adjusted total
        $("input[name='adj_siv_amount_adjusted']").val(currentAdjusted);
    }
</script>
<!-- Modal Adjustment-->

<script>
    $(document).ready(function () {

        // Run on page load (initial)
        get_pending_si_list();


    });




</script>




<script src="{{ asset('public/js/form-validation-toastr.js') }}"></script>
<script>
    $(document).ready(function () {
        // Initialize form validation for crm-deals-form
        FormValidator.init('sales-invoice-create-form', {
            showAllErrors: true,
            scrollToFirst: true,
            highlightFields: true,
            toastrPosition: 'toast-top-right',
            toastrTimeout: 6000
        });
    });
</script>


</script>


<script>


    $(document).ready(function () {


        $(document).on("change", "#shipping_supplier", function () {
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


<style>
    .serial-input-row {
        display: flex;
        align-items: center;
        margin-bottom: 8px;
        gap: 8px;
    }
</style>

@if(!empty($deal_enduser->device_serial))

@php
$devices = json_decode($deal_enduser->device_serial, true);
@endphp




    <div class="modal fade" id="DeviceSerialModal" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="DeviceSerialModalLabel" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" style="    max-width: 22rem;">
            <div class="modal-content">
                <div class="modal-header mb-2">
                    <h4 class="modal-title" id="DeviceSerialModalLabel">Device Serial Numbers</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0">
                    <div id="serial-parts-container">
                        @php
                            $i_1 = 1;

                            // parse stored device_serial summary (formats like "PART: s1, s2 | PART2: s3, s4")
                            $deviceSerialMap = [];
                           
                        @endphp
                             @php
$groupedItems = $cart
    ->groupBy('part_number')
    ->map(function ($items) {
        return [
            'part_number' => $items->first()->part_number,
            'total_qty' => $items->sum('qty'),
            'part_number_txt' => $items->first()->part_number_txt,
        ];
    });
  
@endphp
                        @forelse ($groupedItems as $dt)
                       
                        @php
                            $product_type = @App\SmItem::find($dt['part_number'])->product_type;
                        @endphp

                            @if ($product_type != 2)
                                @continue
                            @endif

                            <div class="part-serial-section" data-part-number="{{ $dt['part_number'] }}" data-qty="{{ $dt['total_qty'] }}"
                                data-row-index="{{ $loop->index }}">
                                <div class="part-serial-header d-flex align-items-center justify-content-between mb-2   ">
                                    <div>
                                        <div class="part-name">Row {{ $i_1++ }}: {{ $dt['part_number_txt'] }}</div>
                                        <small class="text-muted">Qty: {{ $dt['total_qty'] }}</small>
                                    </div>
                                    <div class="serial-count-display qty-badge">0 of {{ $dt['total_qty'] }}</div>
                                </div>

                                <div class="serial-inputs-list" data-qty="{{ $dt['total_qty'] }}" data-part-number="{{ $dt['part_number'] }}">
                                   
                                  

                                    <input type="hidden" value="{{ $dt['part_number'] }}" name="part_number[]" />

                                    @for ($j = 1; $j <= ($dt['total_qty']); $j++)
                                        <div class="serial-input-row" data-index="{{ $j }}">
                                            <span class="text-muted" style="min-width: 20px;">{{ $j }}.</span>
                                            <input type="text" name="serial_no[{{ $dt['part_number'] }}][]"
                                                class="form-control form-control-sm part-serial-input"
                                                value="{{ $devices[$dt['part_number_txt']][$j - 1] ?? '' }}"
                                                autocomplete="off">
                                            <!-- <button type="button" class="btn btn-sm btn-light border remove-serial-btn" title="Remove"><i class="ico icon-outline-minus-circle text-danger"></i></button> -->
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        @empty
                        @endforelse
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
@endif

<script>


    $(document).ready(function () {

        // Initialize DeviceSerialModal so clicking the field/button reliably opens it
        (function () {
            const _el = document.getElementById('DeviceSerialModal');
            let _modal = null;
            try {
                if (_el && window.bootstrap && typeof window.bootstrap.Modal === 'function') {
                    _modal = new bootstrap.Modal(_el);
                }
            } catch (e) { console.warn('DeviceSerialModal init failed', e); }

            $(document).on('click', '#device_serial, #device_serial_btn_modal', function (e) {
                // prefer Bootstrap API, fallback to data-bs attributes
                if (_modal) { _modal.show(); return; }
                // fallback: trigger click so data-bs-toggle works
                $(this).trigger('click');
            });
        })();

        // --- Device serial modal UX & validation ---
        (function(){
            const normalize = s => (s || '').toString().trim().toLowerCase();

            function updateSerialCountForSection($section) {
                const qty = parseInt($section.attr('data-qty')) || 0;
                const filled = $section.find('.part-serial-input').filter(function(){ return $(this).val().trim() !== ''; }).length;
                const $display = $section.find('.serial-count-display');
                $display.text(filled + ' of ' + qty);
                $display.toggleClass('complete', filled === qty && qty > 0);
                $display.toggleClass('incomplete', filled > 0 && filled < qty);
            }

            // Enter navigation: next field in same section → next section first field → Save button
            $(document).on('keydown', '.part-serial-input', function(e){
                if (e.key !== 'Enter') return;
                e.preventDefault();
                const $current = $(this).closest('.serial-input-row');
                const $next = $current.next().find('.part-serial-input');
                if ($next.length) { $next.focus(); return; }

                // move to first input of next section
                const $section = $(this).closest('.part-serial-section');
                const $nextSection = $section.nextAll('.part-serial-section').first();
                if ($nextSection.length) {
                    const $first = $nextSection.find('.part-serial-input').first();
                    if ($first.length) { $first.focus(); return; }
                }

                // nothing next — focus Save button
                $('#btn_save_all_serials').focus();
            });

            // clear invalid state while typing
            $(document).on('input', '.part-serial-input', function(){
                $(this).removeClass('is-invalid');
            });

            // Remove row button (reindex inputs)
            $(document).on('click', '.remove-serial-btn', function(){
                const $row = $(this).closest('.serial-input-row');
                const $list = $row.closest('.serial-inputs-list');
                $row.remove();
                $list.find('.serial-input-row').each(function(idx){
                    $(this).attr('data-index', idx+1);
                    $(this).find('span').first().text((idx+1) + '.');
                });
                const $section = $row.closest('.part-serial-section');
                updateSerialCountForSection($section);
            });

            // Duplicate check on blur/change
            $(document).on('blur change', '.part-serial-input', function(){
                const $this = $(this);
                const val = $this.val().trim();
                if (!val) { updateSerialCountForSection($this.closest('.part-serial-section')); $this.removeClass('is-invalid'); return; }

                const key = normalize(val);
                let duplicate = false;
                let where = null;

                // check other modal inputs
                $('.part-serial-input').not($this).each(function(){
                    if (normalize($(this).val()) === key) { duplicate = true; where = 'modal'; return false; }
                });

                // check table serial_no[] (may contain comma-separated lists)
                if (!duplicate) {
                    $('input[name="serial_no[]"]').each(function(){
                        const v = $(this).val() || '';
                        v.split(',').map(s => s.trim()).forEach(function(tok){ if (!duplicate && normalize(tok) === key && tok !== '') { duplicate = true; where = 'table'; } });
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
            $(document).on('click', '#btn_save_all_serials', function(){
                // build seen map (include existing table values)
                const seen = {};
                let dupFound = false;

                function markSeen(str) {
                    const k = normalize(str);
                    if (!k) return true; // skip
                    if (seen[k]) { dupFound = true; return false; }
                    seen[k] = true; return true;
                }

                // include table serials (split by comma)
                $('input[name="serial_no[]"]').each(function(){
                    const v = $(this).val() || '';
                    v.split(',').map(s => s.trim()).forEach(s => { if (s) markSeen(s); });
                });

                // include modal inputs
                $('.part-serial-input').each(function(){
                    const v = $(this).val().trim();
                    if (!v) return;
                    if (!markSeen(v)) return false;
                });

                if (dupFound) {
                    toastr.error('Duplicate serial numbers found. Remove duplicates before saving.');
                    return;
                }

                // collect per-part serials and update table rows
                const summary = [];
                $('.part-serial-section').each(function(){
                    const $sec = $(this);
                    const part = $sec.attr('data-part-number');
                    const serials = [];
                    $sec.find('.part-serial-input').each(function(){
                        const v = $(this).val().trim(); if (v) serials.push(v);
                    });
                    if (serials.length) {
                        summary.push(part + ': ' + serials.join(', '));

                        // update matching table rows (all rows that have this part)
                        $('#myTable tbody tr').each(function(){
                            const partTxt = $(this).find('input[name="part_number_txt[]"]').val();
                            if (partTxt && partTxt.trim() === part) {
                                $(this).find('input[name="serial_no[]"]').val(serials.join(', '));
                            }
                        });
                    }

                    updateSerialCountForSection($sec);
                });

                // update main device_serial field (use ' | ' as separator)
                $('#device_serial').val(summary.join(' | '));

                // close modal
                if (typeof bootstrap !== 'undefined' && $('#DeviceSerialModal').hasClass('show')) {
                    $('#DeviceSerialModal').modal('hide');
                }
                toastr.success('Serial numbers saved');
            });

            // initialize counts when modal opens (in case of pre-filled values)
            $(document).on('shown.bs.modal', '#DeviceSerialModal', function(){
                $('.part-serial-section').each(function(){ updateSerialCountForSection($(this)); });
                // focus first empty input if any
                const $firstEmpty = $('.part-serial-input').filter(function(){ return $(this).val().trim() === ''; }).first();
                if ($firstEmpty.length) $firstEmpty.focus();
            });
        })();

    });
</script>



<?php } catch (\Exception $e) { ?> {{ $e }}
<?php  } ?>
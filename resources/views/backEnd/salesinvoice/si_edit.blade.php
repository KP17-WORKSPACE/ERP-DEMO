<?php try { ?>

{{ Form::open([
        'class' => 'form-horizontal',
        'files' => true,
        'url' => 'sales-invoice-update',
        'method' => 'POST',
        'id'
        => 'sales-invoice-create-form',
        'novalidate' => true
    ]) }}
<input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
<input type="hidden" id="si_id" name="id" value="{{ isset($edit_si) ? $edit_si->id : '' }}">
<input type="hidden" id="net_vat" name="net_vat" value="{{ $edit_si->net_vat }}">




<div class="purchase-order-content-header">
    <h4 class="purchase-order-content-header-left">
        Edit - {{ @$edit_si->doc_number }}
    </h4>
    <div class="purchase-order-content-header-right">
        <a type="submit" class="btn btn-light text-dark"
            href="{{url('sales-invoice/' . $edit_si->id . '?si_action=add')}}">
            <i class="ico icon-outline-add-square text-success"></i> Add
        </a>
        <button type="submit" class="btn btn-light">
            <i class="ico icon-outline-bookmark-square text-warning"></i> Update
        </button>
        <div class="dropdown">
            <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button" data-bs-toggle="dropdown"
                aria-expanded="false">
                <i class="ico icon-outline-hamburger-menu"></i>
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{url('sales-invoice/' . $edit_si->id . '/delete')}}"><i
                            class="ico icon-outline-trash-bin-minimalistic text-danger"></i> Cancel SI</a></li>

                <li><a class="dropdown-item" href="{{url('sales-invoice/' . $edit_si->id . '/download/t')}}"><i
                            class="ico icon-outline-document-medicine text-success"></i> Print</a></li>

                <li><a class="dropdown-item" href="{{url('sales-invoice/' . $edit_si->id . '/download')}}"><i
                            class="ico icon-outline-document-medicine text-success"></i> Download</a></li>

                <li><button type="button" class="dropdown-item" data-modal-size="modal-md"
                        data-bs-target="#attachment_popup_win" data-bs-toggle="modal" class="btn btn-primary"
                        onclick="view_attachment()"><i
                            class="ico icon-outline-calculator-minimalistic text-warning"></i> Attachment</button></li>
                {{-- <li><button type="button" class="dropdown-item" data-bs-toggle="modal"
                        data-bs-target="#adjustmentModal"><i
                            class="ico icon-outline-calculator-minimalistic text-warning"></i> Adjustment</button></li>
                --}}
                <li><button type="button" class="dropdown-item" data-bs-toggle="modal"
                        data-bs-target="#ModalAdjustment"><i
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
                    <a href="{{ url('customers?customer_action=add') }}" class="btn btn-sm p-0 ms-2"
                        style="border:none;background:none;">
                        <i class="ico icon-outline-add-square text-success" style="font-size:18px;"></i>
                    </a>
                </label>
                <div class="form-group">
                    <select class="form-control js-account-select" name="customer" id="customer" required>
                        <option value=""></option>
                        @foreach ($customer as $value)
                                        <option value="{{ @$value->id }}" {{ isset($edit_si) ? (!empty($edit_si->customer) ?
                            (@$edit_si->customer == @$value->id ? 'selected' : '') : '') : '' }}>

                                            @if (@App\SysHelper::getCompanyCodeSettings()['is_customer_code'])
                                                {{ $value->account_name }} ({{ $value->account_code }})
                                            @else
                                                {{ $value->account_name }}
                                            @endif


                                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-2">
                <label class="form-label">Doc Number</label>
                <div class="form-group">
                    <input class="form-control" type="text" name="doc_number" autocomplete="off" id="doc_number"
                        value="{{ $edit_si->doc_number }}">
                    <input type="hidden" name="doc_number_main" value="{{ $edit_si->doc_number }}">
                </div>
            </div>
            <div class="col-2">
                <label class="form-label">Doc Date</label>
                <div class="form-group">
                    @php
                        $value = date('d/m/Y'); // default today
                        if (isset($edit_si) && !empty($edit_si->doc_date)) {
                            $value = date('d/m/Y', strtotime($edit_si->doc_date)); // convert MySQL date to dmy
                        }
                    @endphp
                    <input class="form-control date-picker" id="doc_date" type="text" autocomplete="off" name="doc_date"
                        value="{{ @$value }}" required>
                </div>
            </div>
            <div class="col-2">
                <label class="form-label">Currency<a style="float: right;" data-bs-target="#ModalChangeCurrancy"
                        data-bs-toggle="modal"><i class="ico icon-outline-pen-2"></i></a></label>
                <div class="form-group">
                    <?php
    $currency1 = 1;
    if (session('logged_session_data.company_id') == 8) {
        $currency1 = 2;
    }
                                                ?>
                    <select class="form-control js-example-basic-single" name="currency" id="currency" readonly>
                        {{-- <option data-display="@lang('Currency') *" value="">@lang('Currency') *</option> --}}

                        @foreach ($currency as $value)
                            @if($edit_si->currency == @$value->id)
                                <option value="{{ @$value->id }}">
                                    {{ @$value->code }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-2">
                <label class="form-label">Created By</label>
                <div class="form-group">
                    <input class="form-control" type="text" name="createdby" autocomplete="off" id="createdby"
                        value="{{ isset($edit_si) ? (!empty(@$edit_si->created_by) ? @$edit_si->createdby->full_name : old('createdby')) : Auth::user()->full_name }}"
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
                        <input type="hidden" id="vat_percentage" name="vat_percentage" value="{{ $edit_si->net_vat }}">
                    </div>
                </div>
                <div class="col-lg-10 mb-2">
                    <div class="row">
                        <div class="col-lg-3 mb-2">
                            <div class="input-effect">
                                <label class="form-label">LPO/Reference No<span>*</span></label>
                                <input class="form-control" type="text" name="reference_no" autocomplete="off"
                                    id="reference_no" value="{{ $edit_si->lpo_number }}" required>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-2">
                            <div class="input-effect">
                                <label class="form-label">LPO/Reference Date<span>*</span></label>
                                @php
                                    $value = date('d/m/Y'); // default today
                                    if (isset($edit_si) && !empty($edit_si->lpo_date)) {
                                        $value = date('d/m/Y', strtotime($edit_si->lpo_date)); // convert MySQL date to dmy
                                    }
                                @endphp
                                <input class="form-control date-picker" type="text" name="reference_date"
                                    autocomplete="off" id="reference_date" value="{{ $value }}" required>
                            </div>
                        </div>
                               <div class="col-lg-3 mb-2">
                            <div class="input-effect">
                                <label class="form-label">@lang('Payment Terms')<span>*</span></label>
                                <div class="form-group">
                                    <select class="form-control js-example-basic-single" name="payment_terms"
                                        id="payment_terms" onchange="fn_payment_terms()" required>
                                        <option value=""></option>
                                        @foreach($paymentterms as $value)
                                                                        <option value="{{@$value->id}}" {{isset($edit_si) ? !empty(@$edit_si->
                                            payment_terms) ? @$edit_si->payment_terms == @$value->id ?
                                            'selected' : '' : '' : ''}}>{{@$value->title}}</option>
                                        @endforeach
                                        <option value="22">Other</option>
                                    </select>


                                </div>

                            </div>
                            <div id="div_payment_terms" style="display: none; padding-top: px;">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Other Payment Terms')<span>*</span></label>
                                    <input
                                        class="txtbx primary-input form-control {{ $errors->has('payment_terms2') ? ' is-invalid' : '' }}"
                                        type="text" name="payment_terms2" autocomplete="off" id="payment_terms2"
                                        value="{{ isset($edit_si) ? (!empty(@$edit_si->payment_terms2) ? @$edit_si->payment_terms2 : old('payment_terms2')) : '' }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-2">
                            <div class="input-effect">
                                <label class="form-label">Supplier Name<span>*</span></label>
                                @php
                                    $selectedIds = $edit_si->ref_supplier_id
                                        ? explode(',', $edit_si->ref_supplier_id)
                                        : [];
                                @endphp


                                <select class="form-control js-example-basic-single" name="ref_supplier_id[]"
                                    id="ref_supplier_id" multiple>
                                    <option value="">-Select-</option>
                                    <option value="TFS" @if(in_array('TFS', $selectedIds)) selected @endif>TAKEN FROM
                                        STOCK</option>

                                    @foreach ($supplier_reference_list as $value)
                                        <option value="{{ @$value->id }}" @if(in_array($value->id, $selectedIds)) selected
                                        @endif>{{ @$value->account_name }}
                                            @if(@App\SysHelper::getCompanyCodeSettings()['is_supplier_code'])
                                                ({{ @$value->account_code }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>

                                <input class="form-control" type="hidden" name="supplier_name" autocomplete="off"
                                    id="supplier_name" value="{{ $edit_si->supplier_name }}" required>
                            </div>
                        </div>
                         <div class="col-lg-3 mb-2">
                            <div class="input-effect">
                                <label class="form-label">@lang('Sales Person Name')<span>*</span></label>
                                <div class="form-group">
                                    <select class="form-control js-example-basic-single" name="sales_man" id="sales_man"
                                        required>
                                        <option value=""></option>
                                        @foreach ($staff as $value)
                                                                        <option value="{{ @$value->user_id }}" @if(isset($edit_si)) @if(
                                                                            $edit_si->
                                                                                sales_man == $value->user_id
                                                                        ) selected @endif @else @if(
                                                $value->user_id ==
                                                Auth::user()->id
                                            ) selected @endif @endif>
                                                                            {{ @$value->full_name }}
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
                                    value="{{ isset($edit_si) ? (!empty(@$edit_si->delivery_terms) ? @$edit_si->delivery_terms : old('delivery_terms')) : 'Ex-Dubai' }}"
                                    required>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-2">
                            <div class="input-effect">
                                <label class="form-label">@lang('Printed Invoice Number')<span></span></label>
                                <input class="form-control" type="text" name="printed_invoice_number" autocomplete="off"
                                    id="printed_invoice_number"
                                    value="{{ isset($edit_si) ? (!empty(@$edit_si->printed_invoice_number) ? @$edit_si->printed_invoice_number : old('printed_invoice_number')) : '' }}">
                            </div>
                        </div>
                       
                 
                        
                        <div class="col-lg-3 mb-2">
                            <div class="input-effect">
                                <label class="form-label">Deal ID<span>*</span></label>
                                <input class="form-control" type="text" name="deal_id" autocomplete="off" id="deal_id"
                                    value="{{ @App\SysHelper::get_code_from_dealid($edit_si->deal_id) }}" required>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 mb-2">
                            <div class="input-effect">
                                <label class="form-label">@lang('Update Deal')</label>
                                <div class="form-group">
                                    <select class="form-control" name="create_deal" id="create_deal" required
                                        onchange="create_deal_change()">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                    <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>

                                </div>

                            </div>
                        </div>
                        <script>
                            function create_deal_change() {
                                if ($('#create_deal').val() == 1) {
                                    $('#div_deal_id').css('display', 'none');
                                    $('#supplier_name').val('TAKEN FROM STOCK');

                                } else {
                                    $('#div_deal_id').css('display', '');
                                    $('#ref_supplier_id').val('');
                                }
                            }
                        </script>
                        <div class="col-lg-3 mb-2">
                            <div class="input-effect">
                                <label class="form-label">@lang('Update Delivery Note')</label>
                                <select class="form-control js-example-basic-single" name="create_dn" id="create_dn"
                                    required>
                                    <option value="0" selected>No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-8 mb-2">
                            <div class="input-effect">
                                <label class="form-label">Narration<span></span></label>
                                <input class="form-control" type="text" name="narration" autocomplete="off"
                                    id="narration" value="{{ $edit_si->narration }}">
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

                    @endphp

                    <label class="form-label">Company (Ship To)</label>
                    <div class="form-group">
                        <select class="form-control js-example-basic-single" name="shipping_supplier"
                            id="shipping_supplier" required style="width: 100%;">
                            <option value=""></option>
                            @foreach ($customer as $value)
                                <option value="{{ @$value->id }}" @if(isset($edit_si))
                                    @if(!empty($edit_si->shipping_supplier)) @if ($edit_si->shipping_supplier == @$value->id)
                                selected @endif @endif @endif>{{ @$value->account_name }}
                                    @if(@App\SysHelper::getCompanyCodeSettings()['is_customer_code'])
                                        ({{ @$value->account_code }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>
                <div class="col-2">
                    <label class="form-label">Contact Name</label>
                    <div class="form-group">
                        <input type="text" class="form-control" name="shipping_name" id="shipping_name"
                            value="{{ isset($edit_si) ? (!empty(@$edit_si->shipping_name) ? @$edit_si->shipping_name : '') : old('shipping_name') }}" />
                    </div>
                </div>
                <div class="col-2">
                    <label class="form-label">Email</label>
                    <div class="form-group">
                        <input type="text" class="form-control" name="shipping_email" id="shipping_email"
                            value="{{ isset($edit_si) ? (!empty(@$edit_si->shipping_email) ? @$edit_si->shipping_email : '') : old('shipping_email') }}" />
                    </div>
                </div>
                <div class="col-2">
                    <label class="form-label">Contact No</label>
                    <div class="form-group">
                        <input type="text" class="form-control" name="shipping_contact_no" id="shipping_contact_no"
                            value="{{ isset($edit_si) ? (!empty(@$edit_si->shipping_contact_no) ? @$edit_si->shipping_contact_no : '') : old('shipping_contact_no') }}" />
                    </div>
                </div>
                <div class="col-3">
                    <label class="form-label">Shipping Address</label>
                    <div class="form-group">
                        <input type="text" class="form-control"
                            value="{{ isset($edit_si) ? (!empty(@$edit_si->shipping_address) ? @$edit_si->shipping_address : '') : old('shipping_address_1') }}"
                            name="shipping_address_1" id="shipping_address_1" />
                    </div>
                </div>


            </div>
        </div>
        <div class="tab-pane fade show" id="vat-details" role="tabpanel" aria-labelledby="vat-details-tab">
            <div class="row gap-rows">



                <div class="col-lg-2 mb-2">
                    <div class="input-effect">
                        <label class="form-label">@lang('Customer Country') <span></span></label>
                        <div class="form-group">
                            <select class="form-control js-example-basic-single" name="customer_country" id="country">
                                <option value=""></option>
                                @foreach ($countries as $key => $value)
                                                            <option value="{{ @$value->id }}" <?php        try {?> @if (isset($edit_si)) @if (@$edit_si->customer_country == $value->id) selected @endif @endif <?php        } catch (\Throwable $th) {
                                    } ?>>{{ @$value->name }}
                                                            </option>
                                @endforeach
                            </select>


                        </div>


                    </div>
                </div>



                <div class="col-lg-2 mb-2">
                    <div class="input-effect">
                        <label class="form-label">@lang('Customer State') <span></span></label>

                        <div class="form-group" id="sectionStateDiv">
                            <select class="form-control js-example-basic-single" name="customer_state" id="state">
                                <option value=""></option>
                                <?php    try {?>
                                @foreach ($states as $key => $value)
                                    <option value="{{ $value->id }}" @if (isset($edit_si)) @if (
                                        @$edit_si->customer_state ==
                                        $value->id
                                    ) selected @endif @endif>{{ $value->name }}
                                    </option>
                                @endforeach
                                <?php    } catch (\Throwable $th) {
    } ?>
                            </select>


                        </div>

                    </div>

                </div>
                <div class="col-2">
                    <label class="form-label">VAT %</label>
                    <div class="form-group">

                        <input class="form-control" type="number" name="vat_percent" id="vat_percent"
                            value="{{ @$edit_si->vat_percent }}">
                    </div>
                </div>

                <div class="col-2">
                    <label class="form-label">VAT Number</label>
                    <div class="form-group">

                        <input class="form-control" type="number" name="vat_number" id="vat_number"
                            value="{{ @$edit_si->vat_number }}">
                    </div>
                </div>


                <div class="col-lg-2 mb-2">
                    <div class="input-effect">
                        <label class="form-label">@lang('Customer Type')</label>
                        <div class="form-group">
                            <select class="form-control js-example-basic-single" name="customer_type"
                                id="customer_type">
                                <option value="0"></option>
                                @foreach($customertype as $value)
                                                        <option value="{{@$value->id}}" {{isset($edit_si) ? !empty(@$edit_si->customer_type) ?
                                    @$edit_si->customer_type == @$value->id ? 'selected' : '' : '' : ''}}>
                                                            {{@$value->title}}
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
                                @foreach($saletype as $value)
                                                        <option value="{{@$value->id}}" {{isset($edit_si) ? !empty(@$edit_si->sale_type) ?
                                    @$edit_si->sale_type == @$value->id ? 'selected' : '' : '' : ''}}>{{@$value->title}}
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
                        <label class="form-label">@lang('End User Name') <span></span></label>
                        <input type="text" class="form-control" name="end_user_name" id="end_user_name"
                            autocomplete="off"
                            value="{{ isset($edit_si) ? (!empty(@$edit_si->end_user_name) ? @$edit_si->end_user_name : '') : old('end_user_name') }}" />

                    </div>
                </div>



                <div class="col mb-2">
                    <div class="input-effect">
                        <label class="form-label">@lang('Contact Person Name') <span></span></label>
                        <input type="text" class="form-control" name="contact_person_name" id="contact_person_name"
                            autocomplete="off"
                            value="{{ isset($edit_si) ? (!empty(@$edit_si->contact_person_name) ? @$edit_si->contact_person_name : '') : old('contact_person_name') }}">

                    </div>
                </div>
                <div class="col mb-2">
                    <div class="input-effect">
                        <label class="form-label">@lang('Contact Person Email') <span></span></label>
                        <input type="text" class="form-control" name="contact_person_email" id="contact_person_email"
                            autocomplete="off"
                            value="{{ isset($edit_si) ? (!empty(@$edit_si->contact_person_email) ? @$edit_si->contact_person_email : '') : old('contact_person_email') }}">

                    </div>
                </div>
                <div class="col mb-2">
                    <div class="input-effect">
                        <label class="form-label">@lang('Contact Person No') <span></span></label>
                        <input type="text" class="form-control" name="contact_person_no" id="contact_person_no"
                            autocomplete="off"
                            value="{{ isset($edit_si) ? (!empty(@$edit_si->contact_person_no) ? @$edit_si->contact_person_no : '') : old('contact_person_no') }}">

                    </div>
                </div>

        @if ($edit_si_items->where('productname.product_type', 2)->count() > 0)


                <div class="col">
                    <div class="mb-3">
                        <label for="" class="form-label">Device Serial</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="device_serial"
                                value="{{ $edit_si->device_serial }}" id="device_serial" data-bs-toggle="modal"
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
                <th class="resizable text-center" width="50px">@lang('Cost')
                    <div class="resizer"></div>
                </th>
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
            @if(isset($edit_si_items) && count($edit_si_items) > 0)
                    <?php        $qty = 0;
                $unitprice = 0;
                $value = 0;
                $discount = 0;
                $taxableamount = 0;
                $vatamount = 0;
                $totalamount = 0;
                $i = 1;
                $deal_discount_sum_amount = 0; ?>
                    @if (count($edit_si_items) > 0)
                        @foreach ($edit_si_items as $dt)
                            <tr>
                                <td><input type="text" class="form-control text-center" name="sort_id[]" value="{{ $i }}" /></td>
                                <td><select class="form-control noborder " name="part_number[]">
                                        <option value="{{ $dt->part_number }}">{{ $dt->productname->part_number }}</option>
                                    </select></td>
                                <td><textarea class="form-control" name="description[]" rows="1">{{ $dt->description }}</textarea></td>

                                <td>
                                    <input class="form-control text-end" type="text" name="cost[]" autocomplete="off"
                                        value="{{ @App\SysHelper::com_curr_format($dt->cost,2,'.',',') }}">
                                    <input class="form-control" type="text" name="part_number_txt[]" autocomplete="off" readonly="true"
                                        value="{{ $dt->productname->part_number }}" hidden>
                                    <input class="form-control" type="text" name="hscode_txt[]" autocomplete="off" readonly="true"
                                        hidden>
                                    <input class="form-control" type="text" name="product_type[]" autocomplete="off" readonly="true"
                                        hidden>
                                    <input class="form-control" type="text" name="product_type_part_number_text[]" autocomplete="off"
                                        readonly="true" hidden>
                                </td>
                                <td><input type="text" class="form-control text-center" name="tax[]"
                                        value="{{ number_format($dt->tax, 0) }}" onchange="calc_change_new(this)"></td>
                                <td><input class="form-control text-center" type="number " name="qty[]" autocomplete="off" min="0"
                                        value="{{ $dt->qty }}" onchange="calc_change_new(this)"></td>
                                <td><input class="form-control text-end" type="text" name="unitprice[]" step="any"
                                        value="{{ @App\SysHelper::com_curr_format($dt->unitprice,2,'.',',') }}" autocomplete="off"
                                        min="0" onchange="calc_change_new(this)" onblur="formatCurrency(this)"></td>
                                <td><input class="form-control text-end" type="text" name="value[]" autocomplete="off" min="0"
                                        value="{{ @App\SysHelper::com_curr_format($dt->value,2,'.',',') }}" readonly></td>
                                <td><input class="form-control text-end" type="text" name="discount[]" autocomplete="off" min="0"
                                        value="{{ @App\SysHelper::com_curr_format($dt->discount,2,'.',',') }}"
                                        onchange="calc_change_new(this)" onblur="formatCurrency(this)"></td>
                                <td><input class="form-control text-end" type="text" name="taxableamount[]" autocomplete="off" min="0"
                                        value="{{ @App\SysHelper::com_curr_format($dt->taxableamount,2,'.',',') }}" readonly></td>
                                <td><input class="form-control text-end" type="text" name="vatamount[]" autocomplete="off" min="0"
                                        value="{{ @App\SysHelper::com_curr_format($dt->vatamount,2,'.',',') }}" readonly></td>
                                <td><input class="form-control text-end" type="text" name="totalamount[]" autocomplete="off" min="0"
                                        value="{{ @App\SysHelper::com_curr_format($dt->taxableamount+$dt->vatamount,2,'.',',') }}"
                                        readonly></td>
                                <td><input class="form-control text-end" type="text" name="serial_no[]" value="{{ $dt->serialno }}">
                                </td>

                            </tr>

                            <?php                $i++;
                                $qty += $dt->qty;
                                $unitprice += $dt->unitprice;
                                $value += $dt->value;
                                $discount += $dt->discount;
                                $taxableamount += $dt->taxableamount;
                                $vatamount += $dt->vatamount;
                                $totalamount += ($dt->taxableamount + $dt->vatamount); ?>
                        @endforeach
                    @endif
            @endif
            <tr>
                <td><input type="text" class="form-control text-center" name="sort_id[]" value="{{ $i }}" />
                </td>
                <td class="noborder">
                    <select class="form-control noborder " name="part_number[]">
                    </select>
                    {{-- on focus add this class and its funcanalities js-product-select --}}
                </td>
                <td><textarea class="form-control" name="description[]" rows="1"></textarea></td>
                <td>
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
                <td><input type="text" class="form-control text-center" name="tax[]" onchange="calc_change_new(this)">
                </td>
                <td><input class="form-control text-center" type="number" name="qty[]" autocomplete="off" min="0"
                        onchange="calc_change_new(this)"></td>
                <td><input class="form-control text-end" type="text" name="unitprice[]" step="any" autocomplete="off"
                        min="0" onchange="calc_change_new(this)" onblur="formatCurrency(this)"></td>
                <td><input class="form-control text-end" type="text" name="value[]" autocomplete="off" min="0" readonly>
                </td>
                <td><input class="form-control text-end" type="text" name="discount[]" autocomplete="off" min="0"
                        onchange="calc_change_new(this)" onblur="formatCurrency(this)"></td>
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
                <th colspan="5" scope="col">Total</th>
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




 <!-- freight charges -->
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
                        @php $cfcCount = isset($edit_cfc) ? $edit_cfc->count() : 0; @endphp
                        <input type="hidden" value="{{ $cfcCount > 0 ? $cfcCount : 1 }}" id="fright_row" />
                        <!-- header plus clones last row -->
                        <a style="cursor: pointer;" data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                            data-bs-content="Add new freight charge row"
                            data-bs-placement="bottom" class="btn-md float-right" onclick="add_fright()"><i class="ico icon-outline-add-square text-success"></i></a></th>
                </tr>

            </thead>
            <tbody>
                @if(isset($edit_cfc) && $edit_cfc->count() > 0)
                    @foreach($edit_cfc as $cfc)
                        <tr id="fright_row_{{ $loop->iteration }}">
                            <td>
                                <input class="form-control date-picker" type="text" id="cfc_date_{{ $loop->iteration }}" name="cfc_date[]"  
                                    autocomplete="off" value="{{ $cfc->date ? \Carbon\Carbon::parse($cfc->date)->format('d/m/Y') : '' }}">
                            </td>
                            <td>
                                <input class="form-control" type="text" id="cfc_bill_no_{{ $loop->iteration }}" name="cfc_bill_no[]"  
                                    autocomplete="off" value="{{ $cfc->bill_number }}">
                            </td>
                            <td>
                                <select class="form-control js-example-basic-single" name="cfc_name[]" id="cfc_name_{{ $loop->iteration }}">
                                    <option value=""></option>
                                    @foreach ($customs_freight_account as $value)
                                        <option value="{{ $value->id }}" {{ $cfc->cfc_name == $value->id ? 'selected' : '' }}>{{ $value->account_name }}  @if (@App\SysHelper::getCompanyCodeSettings()['is_account_code'])
                                             ({{ @$value->account_code }})
                                            
                                        @endif</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select class="form-control js-example-basic-single" name="cfc_credit_account[]" id="cfc_credit_account_{{ $loop->iteration }}" readonly="true">
                                    <option value="none"></option>
                                    @foreach ($supplier as $key => $value)
                                        <option value="{{ $value->id }}" {{ $cfc->cfc_credit_account == $value->id ? 'selected' : '' }}>{{ $value->account_name }}  @if (@App\SysHelper::getCompanyCodeSettings()['is_supplier_code'])
                                             ({{ @$value->account_code }})
                                            
                                        @endif</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input class="form-control text-end" type="text" id="cfc_amount_{{ $loop->iteration }}" name="cfc_amount[]" autocomplete="off" min="0" value="{{ @App\SysHelper::com_curr_format($cfc->cfc_amount,'','',',') }}">
                            </td>
                            <td>
                                <input class="form-control" type="text" id="cfc_remarks_{{ $loop->iteration }}" name="cfc_remarks[]" 
                                    autocomplete="off" value="{{ $cfc->cfc_remarks }}">
                                <!-- per-row copy icon -->
                               
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
                                @foreach ($customs_freight_account as $value)
                                    <option value="{{ $value->id }}">{{ $value->account_code }} - {{ $value->account_name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="form-control js-example-basic-single" name="cfc_credit_account[]" id="cfc_credit_account_1" readonly="true">
                                <option value="none"></option>
                                  @foreach ($supplier as $key => $value)
                                                <option value="{{ @$value->id }}">{{ @$value->account_code }} - {{ @$value->account_name }}</option>
                                  @endforeach
                            </select>
                        </td>
                        <td>
                            <input class="form-control text-end" type="text" id="cfc_amount_1" name="cfc_amount[]" autocomplete="off" min="0">
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
                    <th colspan="3"></th>
                    <th class="text-end" ></th>
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
                $last.find('.js-example-basic-single').select2({width:'100%'});
                $last.find('.date-picker').each(function() {
                    flatpickr(this, {dateFormat:'d/m/Y', allowInput: true});
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
                $new.find('.js-example-basic-single').select2({width:'100%'});
                $new.find('.date-picker').each(function() {
                    flatpickr(this, {dateFormat:'d/m/Y', allowInput: true});
                });
                updateFrightTotals();
            };
            window.duplicateFrightRow = function(el) {
                var $row = $(el).closest('tr');
                var id = parseInt($('#fright_row').val()) || 0;
                id = id + 1;
                $('#fright_row').val(id);
                $row.find('.date-picker').each(function() {
                    if (this._flatpickr) {
                        this._flatpickr.destroy();
                    }
                });
                $row.find('.js-example-basic-single').select2('destroy');
                var $new = $row.clone();
                $row.find('.js-example-basic-single').select2({width:'100%'});
                $row.find('.date-picker').each(function() {
                    flatpickr(this, {dateFormat:'d/m/Y', allowInput: true});
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
                $new.find('.js-example-basic-single').select2({width:'100%'});
                $new.find('.date-picker').each(function() {
                    flatpickr(this, {dateFormat:'d/m/Y', allowInput: true});
                });
                updateFrightTotals();
            };
            
            function updateFrightTotals() {
                var total = 0;
                $('#fright_table tbody tr').each(function() {
                    var val = $(this).find('input[name="cfc_amount[]"]').val().replace(/,/g,'') || '0';
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

            $('#fright_table .js-example-basic-single').select2({width:'100%'});
            $('#fright_table .date-picker').each(function(){
                flatpickr(this, {dateFormat:'d/m/Y', allowInput: true});
            });
            updateFrightTotals();
        });
    </script>




{{ Form::close() }}

<br>

<div class="row mt-40">
    <div class="col-lg-12 text-left mb-2">
        <b>Adjusted Items</b>
        <table class="table table-hover " id="long-list" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th style="width:50px;">@lang('#')</th>
                    <th style="width:100px;">@lang('Doc Number')</th>
                    <th style="width:100px;">@lang('Doc Date')</th>
                    <th style="width:100px;">@lang('LPO NO')</th>
                    <th style="width:100px;" class="text-end">Total</th>
                    <th style="width:100px;" class="text-end">Paid</th>
                    <th style="width:100px;" class="text-end">Balance</th>
                    <th style="width:100px;" class="text-end">Adjusted</th>
                    <th style="width:100px;" class="text-end">Unadjusted</th>
                    <th style="width:100px;" class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @if(count($adj_list) > 0)
                    @foreach ($adj_list as $item)
                        <tr>
                            <td>{{ @$loop->iteration }}</td>
                            <td>{{ @$item->bi_doc_no }}</td>
                            <td>{{ date('d/m/Y', strtotime(@$item->bi_doc_date)) }}</td>
                            <td>{{ @$item->bi_lpo_no }}</td>
                            <td class="text-end">{{ @App\SysHelper::com_curr_format(@$item->bi_total,2,'.',',') }}</td>
                            <td class="text-end">{{ @App\SysHelper::com_curr_format(@$item->bi_paid,2,'.',',') }}</td>
                            <td class="text-end">{{ @App\SysHelper::com_curr_format(@$item->bi_balance_to_adjust,2,'.',',') }}
                            </td>
                            <td class="text-end">{{ @App\SysHelper::com_curr_format(@$item->bi_amount,2,'.',',') }}</td>
                            <td class="text-end">{{ @App\SysHelper::com_curr_format(@$item->bi_cheque_amount -
                                @$item->bi_amount_adjusted,2,'.',',') }}</td>
                            <td class="text-center"><a class="btn-sm btn-light"
                                    onclick="return delete_adjestments({{ $item->id }});"><i
                                        class="ico ico ico icon-outline-trash-bin-minimalistic text-darkphp -S text-dark"
                                        style="font-size: 16px;"></i></a></td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>

<script>
    function delete_adjestments(id) {
        var action = "{{ URL::to('delete-receipt-adjustment-json') }}";

        if (!confirm('Are you sure you want to delete this item?')) {
            return false; // Cancelled
        }

        $("#loading_bg").css("display", "block");
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                id: id,
                doc_number: $('#doc_number').val(),

            },
            cache: false,
            success: function (dataResult) {
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                var getSelectedRows = "";
                var decimalPoint = @json(session('logged_session_data.decimal_point'));

                if (dataResult['data'] != null) {
                    len = dataResult['data'].length;
                }

                if (len > 0) {
                    for (var i = 0; i < len; i++) {
                        var item = dataResult['data'][i];
                        getSelectedRows += "<tr>\
                            <td>" + (i + 1) + "</td>\
                            <td>" + (item.bi_doc_no || '') + "</td>\
                            <td>" + (item.bi_doc_date || '') + "</td>\
                            <td>" + (item.bi_lpo_no || '') + "</td>\
                            <td class='text-end'>" + formatNumber(item.bi_total, decimalPoint) + "</td>\
                            <td class='text-end'>" + formatNumber(item.bi_paid, decimalPoint) + "</td>\
                            <td class='text-end'>" + formatNumber(item.bi_balance_to_adjust, decimalPoint) + "</td>\
                            <td class='text-end'>" + formatNumber(item.bi_amount, decimalPoint) + "</td>\
                            <td class='text-end'>" + formatNumber((item.bi_cheque_amount - item.bi_amount_adjusted), decimalPoint) + "</td>\
                            <td class='text-end'>\
                                <a class='btn-sm btn-danger' onclick='return delete_adjestments(" + item.id + ")' >\
                                    <i class='fa fa-trash' aria-hidden='true'></i>\
                                </a>\
                            </td>\
                        </tr>";
                    }
                    $('#adjustment-table tbody').empty();
                    $("#adjustment-table tbody").append(getSelectedRows);
                    $('#narration').val('');
                    $('#deal_id').val('');
                    $("input[name='amount[]']").val('');
                    $("input[name='remarks[]']").val('');
                    alert('Adjustments Deleted Successfully');
                    location.reload();
                } else {
                    $('#adjustment-table tbody').empty();
                    //alert('Error: Something went wrong!');
                    location.reload();
                }
                $("#loading_bg").css("display", "none");
                $('#btn_adj_close').click();
            }
        });
    }
</script>


{{-- Models --}}
<!-- <a data-bs-toggle="modal" data-bs-target="#editModal"></a> -->

@include('backEnd.inventory.itemAddModal')


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
                                    <textarea type="text" class="form-control" id="add_serial_no"
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
    $(document).on("keydown", 'input[name="qty[]"], input[name="unitprice[]"], input[name="discount[]"], input[name="serial_no[]"]', function (e) {
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
    update_totals();
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
            total_taxableamount += parseFloat($row.find('input[name="taxableamount[]"]').val().replace(/,/g, '')) || 0;
            total_vatamount += parseFloat($row.find('input[name="vatamount[]"]').val().replace(/,/g, '')) || 0;
            total_totalamount += parseFloat($row.find('input[name="totalamount[]"]').val().replace(/,/g, '')) || 0;
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
                const searchInput = document.querySelector('.select2-container--open .select2-search__field');
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
                        const searchInput = document.querySelector('.select2-container--open .select2-search__field');
                        if (searchInput) {
                            // Put current selected text into search box so user can edit / refine
                            searchInput.value = sel[0].text.trim();
                            // trigger input so select2 filters on prefilling
                            var event = new Event('input', { bubbles: true });
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
                $row.find('input[name="product_type_part_number_text[]"]').val(selectedData.description || '');
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
    function get_pending_si_list() {
        var id = $("#customer").val();
        alert(id);
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
                alert(dataResult);
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                var len = 0;
                if (dataResult['data'] != null) {
                    len = dataResult['data'].length;
                }
                if (len > 0) {
                    for (var i = 0; i < len; i++) {
                        if (dataResult['data'][i].status == 3) {
                            alert("Customer Information is incompleated! Please Update Customer.");
                            $('#btnSubmit').css('display', 'none');
                        } else { $('#btnSubmit').css('display', ''); }
                        $('#payment_terms').val(dataResult['data'][i].payment_terms);
                        $('#shipping_supplier').val(dataResult['data'][i].account_id).trigger('change');

                        // $('#shipping_name').val(dataResult['data'][i].contcat_person);
                        // $('#shipping_address').val(dataResult['data'][i].address);
                        $('#customer_type').val(dataResult['data'][i].customer_type);
                        $('#sale_type').val(dataResult['data'][i].sale_type);
                        $('#country').val(dataResult['data'][i].vat_country);


                        window.SELECTED_STATE_ID = dataResult['data'][i].vat_state;


                        $('#net_vat').val(dataResult['data'][i].vat_percentage);
                        $('.vat').val(dataResult['data'][i].vat_percentage);
                    }
                }
                else {
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
                alert(dataResult);
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
                }
                else {
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
                }
                else {
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
                <input type="hidden" id="hd_pending_dn_id" />
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

        if ($('#att_file').val() == "") { $('#att_file').focus(); $("#loading_bg").css("display", "none"); return false; }

        var action = "{{ URL::to('add-sales-invoice-attachment') }}";

        var formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');  // Append CSRF token
        formData.append('siv_id', $('#si_id').val());
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
                                <td><a onclick='delete_attachment("+ dataResult['data'][i].id + ")' class='btn-sm btn-danger text-white'>Delete</a></td>\
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
        $('#att_cust_name').text($('#customer :selected').text() + " " + $('#doc_number').val());
        var action = "{{ URL::to('view-sales-invoice-attachment') }}";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                siv_id: $('#si_id').val(),
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
                                <td><a onclick='delete_attachment("+ dataResult['data'][i].id + ")' class='btn-sm btn-danger text-white'>Delete</a></td>\
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
        var action = "{{ URL::to('delete-sales-invoice-attachment') }}";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                id: id,
                siv_id: $('#si_id').val(),
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
                                <td><a onclick='delete_attachment("+ dataResult['data'][i].id + ")' class='btn-sm btn-danger text-white'>Delete</a></td>\
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


<!-- Modal Adjustment-->
<div class="modal side-panel fade" id="ModalAdjustment" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="height: 500px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel">Unadjusted List</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{ Form::open([
        'class' => 'form-horizontal',
        'files' => true,
        'url' => 'sales-invoice-update-adjustment',
        'method' => 'POST',
        'enctype' => 'multipart/form-data'
    ]) }}
            <div class="card-body" style="height: 420px; overflow-y: scroll;">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-hover form-item-table">
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
                                @if(count($list_of_unadjusted) > 0)
                                    @foreach ($list_of_unadjusted as $p)
                                        <tr>
                                            <td class="border">{{ date('d/m/Y', strtotime(@$p->doc_date)) }}</td>
                                            <td class="border"><a href="{{url('get-url-receipt/' . @$p->doc_number)}}"
                                                    target="_blank">{{ @$p->doc_number }}</a></td>
                                            <td class="border">{{ @$p->account_name }}</td>
                                            <td class="border text-right">{{ @$p->amount - @$p->adj_amount }}</td>
                                            <td class="border text-right"><input type="text" name="set_amt[]"
                                                    id="set_amt_{{ @$p->doc_number }}" class="form-control text-right" id=""
                                                    name="" value="{{ @$p->adj_amount }}"
                                                    onclick="set_adjust('{{ @$p->amount - @$p->adj_amount }}','{{ @$p->doc_number }}')" />
                                                <input type="hidden" name="receiptno[]" value="{{ @$p->doc_number }}" />
                                                <input type="hidden" name="set_amt_act[]"
                                                    value="{{ @$p->amount - @$p->adj_amount }}" />
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                @if(count($list_of_unadjusted_pdc) > 0)
                                    @foreach ($list_of_unadjusted_pdc as $p)
                                        <tr>
                                            <td class="border">{{ date('d/m/Y', strtotime(@$p->doc_date)) }}</td>
                                            <td class="border"><a href="{{url('get-url-receipt/' . @$p->doc_number)}}"
                                                    target="_blank">{{ @$p->doc_number }}</a></td>
                                            <td class="border">{{ @$p->account_name }}</td>
                                            <td class="border text-right">{{ @$p->amount - @$p->adj_amount }}</td>
                                            <td class="border text-right"><input type="text" name="set_amt[]"
                                                    id="set_amt_{{ @$p->doc_number }}" class="form-control text-right" id=""
                                                    name="" value="{{ @$p->adj_amount }}"
                                                    onclick="set_adjust('{{ @$p->amount - @$p->adj_amount }}','{{ @$p->doc_number }}')" />
                                                <input type="hidden" name="receiptno[]" value="{{ @$p->doc_number }}" />
                                                <input type="hidden" name="set_amt_act[]"
                                                    value="{{ @$p->amount - @$p->adj_amount }}" />
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                        <input type="hidden" name="adj_cus_id" value="{{ @$edit_si->customer }}" />
                        <input type="hidden" name="adj_siv_id" value="{{ @$edit_si->id }}" />
                        <input type="hidden" name="adj_siv_no" value="{{ @$edit_si->doc_number }}" />
                        <input type="hidden" name="adj_siv_date" value="{{ @$edit_si->doc_date }}" />
                        <input type="hidden" name="adj_siv_amount" value="{{ $adjusted_amt }}" />
                        <input type="hidden" name="adj_siv_amount_actual" value="{{ $adjusted_amt_actual }}" />
                        <input type="hidden" name="adj_siv_amount_adjusted" value="0" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-light add-btn ms-2" id="discount_add_btn">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Adjust
                </button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
<!-- Modal Adjustment-->
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

        // Optional: update hidden adjusted total
        $("input[name='adj_siv_amount_adjusted']").val(currentAdjusted + adjustAmount);
    }
</script>

<!-- Modal Change Currancy-->
<div class="modal side-panel fade" id="ModalChangeCurrancy" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel">Change Currancy</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{ Form::open([
        'class' => 'form-horizontal',
        'files' => true,
        'url' => 'sales-invoice-update-currency',
        'method' => 'POST',
        'enctype' => 'multipart/form-data'
    ]) }}
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="" class="form-label">Currancy From</label>
                            <select class="form-control" name="from_currency_id" required>
                                @foreach ($currency as $value)
                                    @if($edit_si->currency == $value->id)
                                        <option value="{{ @$value->id }}">{{ @$value->code }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="" class="form-label">Currancy To</label>
                            <select class="form-control" name="to_currency_id" id="to_currency_id" required
                                onchange="set_rate()">
                                <option value="">Select</option>
                                @foreach ($currencylist2 as $value)
                                    <option value="{{ @$value->id }}">{{ @$value->code }}</option>
                                @endforeach
                            </select>
                            @foreach ($currencylist2 as $value)
                                <input type="hidden" id="rate_{{ @$value->id }}" name="rate_{{ @$value->id }}"
                                    value="{{ @$value->rate }}" />
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="" class="form-label">Default Currency Conversion Rate</label>
                            <input type="text" class="form-control" id="to_currency_rate" name="to_currency_rate"
                                required />
                        </div>
                    </div>
                    <script>
                        function set_rate() {
                            var id = $('#to_currency_id').val();
                            var rate = $('#rate_' + id).val();

                            $('#to_currency_rate').val(rate);
                        }

                    </script>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="cur_si_id" value="{{ @$edit_si->id }}" />
                <button type="submit" class="btn btn-light add-btn ms-2">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Change
                </button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
<!-- Modal Change Currancy-->

<script>
    document.addEventListener("DOMContentLoaded", function () {

        // --- Restore last active tab ---
        let lastTab = localStorage.getItem("active-siedit-tab");
        if (lastTab) {
            let tabTrigger = document.querySelector('[data-bs-target="' + lastTab + '"]');
            if (tabTrigger) {
                let tab = new bootstrap.Tab(tabTrigger);
                tab.show();
            }
        }

        // --- Save tab when user changes it ---
        let tabButtons = document.querySelectorAll('#purchaseDetailsTabs button[data-bs-toggle="tab"]');

        tabButtons.forEach(btn => {
            btn.addEventListener("shown.bs.tab", function (e) {
                localStorage.setItem("active-siedit-tab", e.target.getAttribute("data-bs-target"));
            });
        });

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

<style>
    .serial-input-row {
        display: flex;
        align-items: center;
        margin-bottom: 8px;
        gap: 8px;
    }
</style>




        @if ($edit_si_items->where('productname.product_type', 2)->count() > 0)


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
                                if (!empty($edit_si->device_serial)) {
                                    $segments = preg_split('/\|/', $edit_si->device_serial);
                                    foreach ($segments as $seg) {
                                        $seg = trim($seg);
                                        if ($seg === '') continue;
                                        $kv = preg_split('/: */', $seg, 2);
                                        if (count($kv) !== 2) continue;
                                        $key = trim($kv[0]);
                                        $vals = array_filter(array_map('trim', preg_split('/,/', $kv[1])));
                                        if ($key !== '') $deviceSerialMap[$key] = $vals;
                                    }
                                }
                            @endphp
                            @php
$groupedItems = $edit_si_items
    ->where('productname.product_type', 2)
    ->groupBy('part_number')
    ->map(function ($items) {
        return [
            'part_number' => $items->first()->part_number,
            'total_qty' => $items->sum('qty'),
            'product' => $items->first()->productname
        ];
    });
@endphp
                            @forelse ($groupedItems as $dt)
                               

                                <div class="part-serial-section" data-part-number="{{ $dt['part_number'] }}" data-qty="{{ $dt['total_qty'] }}" data-row-index="{{ $loop->index }}">
                                    <div class="part-serial-header d-flex align-items-center justify-content-between mb-2   ">
                                        <div>
                                            <div class="part-name">Row {{ $i_1++ }}: {{ $dt['product']->part_number }}</div>
                                            <small class="text-muted">Qty: {{ $dt['total_qty'] }}</small>
                                        </div>
                                        <div class="serial-count-display qty-badge">0 of {{ $dt['total_qty'] }}</div>
                                    </div>

                                    <div class="serial-inputs-list" data-qty="{{ $dt['total_qty'] }}">
                                        @php
                                            // prefer per-line serials if present, otherwise try to extract from $edit_si->device_serial
                                            $existingSerials = [];
                                            
                                                $candidates = [ $dt['part_number'], $dt['product']->part_number ?? '', (string)($loop->iteration) ];
                                                foreach ($candidates as $cand) {
                                                    if (!$cand) continue;
                                                    if (isset($deviceSerialMap[$cand])) { $existingSerials = $deviceSerialMap[$cand]; break; }
                                                    $digits = preg_replace('/\D+/', '', $cand);
                                                    if ($digits && isset($deviceSerialMap[$digits])) { $existingSerials = $deviceSerialMap[$digits]; break; }
                                                }
                                            
                                        @endphp

                                        <input type="hidden" value="{{ $dt['part_number'] }}" name="part_number[]" />

                                        @for ($j = 1; $j <= ($dt['total_qty']); $j++)
                                            <div class="serial-input-row" data-index="{{ $j }}">
                                                <span class="text-muted" style="min-width: 20px;">{{ $j }}.</span>
                                                <input type="text" name="serial_no[{{ $dt['part_number'] }}][]" class="form-control form-control-sm part-serial-input" value="{{ isset($existingSerials[$j-1]) ? e($existingSerials[$j-1]) : '' }}" autocomplete="off">
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
                    $('.serial-inputs-list input[name="serial_no[]"]').each(function(){
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
                $('.serial-inputs-list input[name="serial_no[]"]').each(function(){
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
<?php } catch (\Exception $e) { ?> {{ $e }}
<?php  } ?>
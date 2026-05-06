<?php try { ?>

<style>
    .form-control:disabled,
    .form-control[readonly] {
        background-color: #ffffff;
    }
</style>

<style>
    label {
        white-space: nowrap;
        /* Keep text on one line */
        overflow: hidden;
        /* Hide overflow */
        text-overflow: ellipsis;
        /* Add "..." */
        display: block;
        /* Ensure it behaves like a block (or inline-block) */
        width: 100%;
        /* Required for truncation */
    }
</style>

<style>
/* Custom hover color for Select2 options */
.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #deebe1 !important;  /* Dodger blue */
    color: #1E2224 !important;
    border-bottom-color: #deebe1;
}
.select2-container--default .select2-results__option[aria-selected="true"] {
    background-color: #deebe1 !important; /* e.g., info blue */
     color: #1E2224 !important;
    border-bottom-color: #deebe1;
}

</style>


{{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'purchase-order-update', 'method' => 'POST', 'id' => 'tender-create-form']) }}
<input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
<input type="hidden" name="id" id="po_id" value="{{ isset($po) ? $po->id : '' }}">
<input type="hidden" name="net_vat" id="net_vat" value="{{ $net_vat }}">

<div class="tab-pane fade show active" id="purchase-order-1" role="tabpanel" aria-labelledby="purchase-order-1-tab">
    <div class="purchase-order-content-header d-flex align-items-center justify-content-between mb-1 gap-2">
        <h4 class="purchase-order-content-header-left">
            Edit - {{@$po->doc_number}}
        </h4>
        <div class="purchase-order-content-header-right d-flex align-items-center justify-content-end gap-2">
            <a class="btn btn-light" href="{{url('purchase-order/' . @$po->id . '?po_action=add')}}">
                <i class="ico icon-outline-add-square text-success"></i> Add
            </a>
            <button type="submit" class="btn btn-light">
                <i class="ico icon-outline-bookmark-square text-warning"></i> Update
            </button>

                <div class="dropdown">
                                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="ico icon-outline-hamburger-menu"></i>
                                </button>
                                <ul class="dropdown-menu" style="">
                                    <li><a class="dropdown-item" href="{{url('purchase-order/'.@$po->id.'/print')}}">
                                            <i class="ico icon-outline-import text-success"></i>
                                            Download</a></li>
                                    {{-- <li><a class="dropdown-item" href="#">
                                            <i class="ico icon-outline-import text-success"></i>
                                            Import</a></li> --}}
                                    <li><a class="dropdown-item" href="{{url('purchase-order/'.@$po->id.'/delete')}}"><i class="ico icon-outline-trash-bin-minimalistic text-danger"></i>
                                            Delete</a></li>

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

                        @foreach ($vendors as $value)
                            <option value="{{ @$value->id }}" {{ isset($po) ? (!empty($po->vendors) ? (@$po->vendors == @$value->id ? 'selected' : '') : '') : '' }}>
                                {{ @$value->account_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-2">
                    <label class="form-label">PO Number</label>
                    <div class="form-group">
                        <input readonly type="text" class="form-control" name="doc_number" autocomplete="off"
                            id="doc_number" value="{{ $po->doc_number }}" />
                        <input type="hidden" name="doc_number_main" value="{{ $po->doc_number }}">
                    </div>
                </div>
                <div class="col-2">
                    <label class="form-label">PO Date</label>
                    @php
                        $value = \Carbon\Carbon::parse(old('po_date') ?? ($po->po_date ?? now()))->format('d/m/Y');
                    @endphp
                    <div class="form-group">
                        <input type="text" id="po_date" type="date" name="po_date" class="form-control date-picker"
                            value="{{ @$value }}" />
                    </div>
                </div>
                <div class="col-2">
                    <label class="form-label">Currency</label>
                    

                    <div class="form-group">
                        <select class="form-control select2" name="currency" id="currency">
                            @foreach ($currency as $value)
                                
                                    <option value="{{ @$value->id }}" @if($po->currency == @$value->id) selected @endif>{{ @$value->code }}</option>
                               
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
                    <label class="form-label">Created By</label>
                    <div class="form-group">
                        <input type="text" class="form-control" name="createdby" autocomplete="off" id="createdby"
                            readonly
                            value="{{ isset($po) ? (!empty(@$po->created_by) ? @$po->createdby->full_name : old('createdby')) : Auth::user()->full_name }}" />
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
                        <label class="form-label">Delivery Date</label>
                        @php
                        $value = \Carbon\Carbon::parse(old('delivery_date') ?? ($po->delivery_date ?? now()))->format('d/m/Y');
                        @endphp
                        <div class="form-group">
                            <input type="text" class="form-control date-picker" id="delivery_date" name="delivery_date"
                                value="{{ @$value }}" required />
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Payment Terms*</label>
                        <div class="form-group">
                            <select class="form-control" required name="payment_terms" id="payment_terms"
                                onchange="fn_payment_terms()">
                                <option value="">Select</option>
                                @foreach ($paymentterms as $value)
                                    <option value="{{ @$value->id }}" {{ isset($po) ? (!empty(@$po->payment_terms) ? (@$po->payment_terms == @$value->id ? 'selected' : '') : '') : '' }}>
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
                                autocomplete="off" id="payment_terms2" value="{{ @$po->payment_terms2 }}">
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Customer Reference</label>
                        <div class="form-group">
                            <input type="text" class="form-control" id="narration" type="text" name="narration" value="{{ $po->narration }}" required />
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Salesman Name</label>
                        <div class="form-group">
                            <select class="form-control js-example-basic-single" required name="sales_person"
                                id="sales_person">
                                <option value=""></option>
                                    @foreach ($salesman as $value)
                                        <option value="{{ @$value->user_id }}" @if($po->sales_person == $value->user_id) selected @endif>{{ @$value->full_name }}</option>
                                    @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Deal ID</label>
                        <div class="form-group">
                            <input type="text" class="form-control" id="deal_id" type="text"
                                name="deal_id" value="{{ App\SysHelper::get_code_from_dealid($po->deal_id) }}" required />
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Contact Person Name</label>
                        <div class="form-group">
                            <input type="text" class="form-control" id="contact_person_name" type="text"
                                name="contact_person_name" value="{{ $po->contact_person_name }}" required />
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Contact Person Email</label>
                        <div class="form-group">
                            <input type="text" class="form-control" id="contact_person_email" type="text"
                                name="contact_person_email"value="{{ $po->contact_person_email }}"  required />
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Contact Person Telephone</label>
                        <div class="form-group">
                            <input type="text" class="form-control" id="contact_person_telephone" type="text"
                                name="contact_person_telephone" value="{{ $po->contact_person_telephone }}" required />
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Internal Transfer</label>
                        <div class="form-group">
                            <select class="form-control select2" id="internal_transfer" name="internal_transfer"
                                required>
                                <option value="">Select</option>
                                <option value="1" @if($po->internal_transfer == 1) selected @endif>Yes</option>
                                <option value="2" @if($po->internal_transfer == 2) selected @endif>No</option>
                            </select>
                            <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>
                        </div>
                    </div>
                
                 
                    <div class="col-2 mb-2">
                        <div class="input-effect">
                            <label class="dynamicslbl form-label">@lang('Narration')</label>
                            <input class="form-control" data-bs-toggle="modal" data-bs-target="#narrationModal" value="{{$po->reference}}" id="reference" type="text" name="reference">
                        </div>
                    </div>

                </div>
            </div>
            <div class="tab-pane fade" id="shipping-details-info" role="tabpanel"
                aria-labelledby="shipping-details-info-tab">
                <div class="row gap-rows">
                    <div class="col-3">
                        
                        <label class="form-label">Company Name</label>
                        <div class="form-group">
                            <select class="form-control js-example-basic-single" name="shipping_supplier"
                                id="shipping_supplier" required style="width: 100%;">
                                <option value=""></option>
                                @foreach ($customer as $value)
                                            <option value="{{ @$value->id }}"
                                                @if(isset($po))
                                                    @if(!empty($po->shipping_supplier))
                                                        @if ($po->shipping_supplier == @$value->id)
                                                            selected
                                                        @endif
                                                    @else
                                                        @if (session('logged_session_data.company_id') == 2) //SYSCOM FZE
                                                            @if($value->id == 6262) selected @endif
                                                        @elseif (session('logged_session_data.company_id') == 3) //SYSCOM DISTRIBUTIONS LLC BRANCH ABU DHABI 1
                                                            @if($value->id == 3864) selected @endif
                                                        @elseif (session('logged_session_data.company_id') == 4) //SYSCOM DISTRIBUTION LTD
                                                            @if($value->id == 6259) selected @endif
                                                        @elseif (session('logged_session_data.company_id') == 5) //SYSCOM IT SOLUTIONS LLC
                                                            @if($value->id == 9364) selected @endif
                                                        @elseif (session('logged_session_data.company_id') == 6) //SYSCOM DISTRIBUTIONS LLC
                                                            @if($value->id == 208) selected @endif
                                                        @elseif (session('logged_session_data.company_id') == 7) //STACK LINK UK LTD
                                                            @if($value->id == 6217) selected @endif
                                                        @elseif (session('logged_session_data.company_id') == 8) //SUPREME SYSTEM TRADING ESTABLISHMENT
                                                            @if($value->id == 6250) selected @endif
                                                        @elseif (session('logged_session_data.company_id') == 9) //SYSCOM DISTRIBUTION WLL
                                                            @if($value->id == 6260) selected @endif
                                                        @elseif (session('logged_session_data.company_id') == 10) //SUPREME SYSTEM DISTRIBUTORS SPC
                                                            @if($value->id == 6251) selected @endif
                                                        @endif     
                                                    @endif
                                                @endif                                                                                                
                                                >{{ @$value->account_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                    </div>
                    <div class="col-2">
                        <label class="form-label">Contact Name</label>
                        <div class="form-group">
                            <input type="text" class="form-control" name="shipping_name" id="shipping_name"
                                value="{{ isset($po) ? (!empty(@$po->shipping_name) ? @$po->shipping_name : '') : old('shipping_name') }}" />
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Email</label>
                        <div class="form-group">
                            <input type="text" class="form-control" name="shipping_email" id="shipping_email"
                                value="{{ isset($po) ? (!empty(@$po->shipping_email) ? @$po->shipping_email : '') : old('shipping_email') }}" />
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Contact No</label>
                        <div class="form-group">
                            <input type="text" class="form-control" name="shipping_contact_no" id="shipping_contact_no"
                                value="{{ isset($po) ? (!empty(@$po->shipping_contact_no) ? @$po->shipping_contact_no : '') : old('shipping_contact_no') }}" />
                        </div>
                    </div>
                    <div class="col-3">
                        <label class="form-label">Shipping Address</label>
                        <div class="form-group">
                            <input type="text" class="form-control" value="{{ isset($po) ? (!empty(@$po->shipping_address_1) ? @$po->shipping_address_1 : '') : old('shipping_address_1') }}" name="shipping_address_1" id="shipping_address_1" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="vat-details" role="tabpanel" aria-labelledby="vat-details-tab">
                <div class="row gap-rows">
                    <div class="col-3">
                        <label class="form-label">Supplier Type</label>
                        <div class="form-group">
                            <select class="form-control {{ $errors->has('supplier_type') ? ' is-invalid' : '' }}"
                                name="supplier_type" id="supplier_type">
                                <option value="0"></option>
                                @foreach ($suppliertype as $value)
                                            <option value="{{ @$value->id }}"
                                                {{ isset($po) ? (!empty(@$po->supplier_type) ? (@$po->supplier_type == @$value->id ? 'selected' : '') : '') : '' }}>
                                                {{ @$value->title }}</option>
                                        @endforeach
                            </select>
                            <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>
                        </div>
                    </div>
                    <div class="col-3">
                        <label class="form-label">Purchase Type</label>
                        <div class="form-group">
                            <select name="purchase_type" id="purchase_type"
                                class="form-control  {{ $errors->has('purchase_type') ? ' is-invalid' : '' }}"
                                id="inputVendorName">

                                 @foreach ($purchasetype as $value)
                                            <option value="{{ @$value->id }}"
                                                {{ isset($po) ? (!empty(@$po->supplier_type) ? (@$po->supplier_type == @$value->id ? 'selected' : '') : '') : '' }}>
                                                {{ @$value->title }}</option>
                                        @endforeach
                            </select>
                            <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>
                        </div>
                    </div>
                    <div class="col-3">
                        <label class="form-label">Supplier Country</label>
                        <div class="form-group">
                            <select class="form-control js-example-basic-single" style="width: 100%;"
                                name="supplier_country" id="country" required>
                                <option data-display="" value=""></option>
                                @foreach ($countries as $key => $value)
                                            <option value="{{ @$value->id }}"
                                                <?php        try {?>                                                        
                                                @if (isset($po)) @if (@$po->supplier_country == $value->id) selected @endif
                                                @endif
                                                <?php        } catch (\Throwable $th) {
        } ?>
                                                >{{ @$value->name }} </option>
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
                    @if($po->bill_number)
                    <th class="resizable text-center" width="100px">@lang('Serial No')
                        <div class="resizer"></div>
                    </th>
                    @endif
                </tr>
            </thead>
            <tbody>
@php $i = 1;
    $qty_total = 0;
    $value_total = 0;
    $discount_total = 0;
    $fright_total = 0;
    $customcharges_total = 0;
    $taxableamount_total = 0;
    $vatamount_total = 0;
$amount_total = 0; @endphp

@if (count($po_items) > 0)
    @foreach ($po_items as $items)


<tr>
        <td><input type="text" class="form-control text-center" name="sort_id[]" value="{{ $items->sort_id }}" />
            <input type="hidden" name="product_type[]" value="{{ $items->product_type }}" />
            <input type="hidden" name="item_po_id[]" value="{{ $items->po_id }}" />
        </td>
        <td><input type="text" class="form-control" name="part_number_txt[]" value="{{ $items->partno ?? 0 }}" readonly/>
        <input type="hidden"  name="part_number[]" value="{{ $items->part_number }}" /></td>
        <td><input type="text" class="form-control" name="description[]" value="{{ $items->description ?? 0 }}"/></td>
        


        
        <td><input type="text" class="form-control text-center" name="tax[]" value="{{ @App\SysHelper::com_curr_format($items->tax ?? 0,2,'.','') }}" onchange="calc_change_new(this)"/></td>
        <td><input type="text" class="form-control text-center" name="qty[]" value="{{ $items->qty }}"  onkeypress="set_license_key_po({{ $i }})" onchange="calc_change_new(this)"/></td>
        
        <td><input type="text" class="form-control text-end" step="Any" id="unitprice_{{ $i }}" name="unitprice[]" value="{{ @App\SysHelper::com_curr_format($items->unitprice,2,'.','') }}" onchange="calc_change_new(this)"/></td>
        <td><input type="text" class="form-control text-end" name="value[]" value="{{ @App\SysHelper::com_curr_format($items->value,2,'.','') }}" onchange="calc_change_new(this)"/></td>
        <td><input type="text" class="form-control text-end" name="discount[]" value="{{ @App\SysHelper::com_curr_format($items->discount,2,'.','') }}" onchange="calc_change_new(this)"/></td>
        <td><input type="text" class="form-control text-end" name="fright[]" value="{{ @App\SysHelper::com_curr_format($items->fright,2,'.','') }}" onchange="calc_change_new(this)"/></td>
        <td><input type="text" class="form-control text-end" name="customcharges[]" value="{{ @App\SysHelper::com_curr_format($items->customcharges,2,'.','') }}" onchange="calc_change_new(this)"/></td>
        
        <td><input type="text" class="form-control text-end" name="taxableamount[]" value="{{ @App\SysHelper::com_curr_format($items->taxableamount,2,'.','') }}" readonly/></td>
        <td><input type="text" class="form-control text-end" name="vatamount[]" value="{{ @App\SysHelper::com_curr_format($items->vatamount,2,'.','') }}" readonly/></td>
        <td><input type="text" class="form-control text-end" name="totalamount[]" value="{{ @App\SysHelper::com_curr_format($items->taxableamount+$items->vatamount, 2, '.', '') }}" readonly/></td>
         @if($po->bill_number) 
        <td >

                                <?php
                                    $srno = $edit_list_srl->where('part_number',$items->part_number)->pluck('srl_no');
                                    $array = explode(',', trim($srno, '[""]'));
                                    $string = implode(', ', $array);

                                    if($string!=""){
                                        $string=str_replace('"', '',$string);
                                    }
                                ?>
                                <input type="text" class="form-control" name="serial_no[]" value="{{ $string }}" /></td>
        @endif
</tr>


        @php $qty_total += $items->qty;
            $value_total += $items->value;
            $discount_total += $items->discount;
            $fright_total += $items->fright;
            $customcharges_total += $items->customcharges;
            $taxableamount_total += $items->taxableamount;
            $vatamount_total += $items->vatamount;
            $amount_total += ($items->taxableamount + $items->vatamount);
        $i++; @endphp


    @endforeach
@endif


                <tr>
                    <td><input type="text" class="form-control text-center" name="sort_id[]" value="{{ count($po_items)+1 }}" /></td>
                    <td class="noborder">
                        <select class="form-control noborder " name="part_number[]">
                        </select>
                        {{-- on focus add this class and its funcanalities js-product-select --}}
                        <input type="hidden" name="item_id[]" value="0" />
                    </td> 
                    
                    <td>
                        <input class="form-control" type="text" name="description[]" autocomplete="off">
                        <input class="form-control" type="text" name="part_number_txt[]" autocomplete="off"
                            readonly="true" hidden>
                        <input class="form-control" type="text" name="hscode_txt[]" autocomplete="off" readonly="true"
                            hidden>
                        <input class="form-control" type="text" name="product_type[]" autocomplete="off" readonly="true"
                            hidden>
                        <input class="form-control" type="text" name="product_type_part_number_text[]"
                            autocomplete="off" readonly="true" hidden>
                    </td>
                    <td><input type="number" class="form-control text-center" name="tax[]" onchange="calc_change_new(this)"></td>
                    <td><input class="form-control text-center" type="number" name="qty[]" autocomplete="off" min="0"
                            onchange="calc_change_new(this)" onkeypress="set_license_key()"></td>
                    <td><input class="form-control text-end" type="number" name="unitprice[]" step="any" autocomplete="off"
                            min="0" onchange="calc_change_new(this)"></td>
                    <td><input class="form-control text-end" type="number" name="value[]" autocomplete="off" min="0" readonly>
                    </td>
                    <td><input class="form-control text-end" type="number" name="discount[]" autocomplete="off" step="0.01"
                            min="0" onchange="calc_change_new(this)"></td>
                    <td><input class="form-control text-end" type="number" name="fright[]" autocomplete="off" step="0.01" min="0"
                            onchange="calc_change_new(this)"></td>
                    <td><input class="form-control text-end" type="number" name="customcharges[]" autocomplete="off" step="0.01"
                            min="0" onchange="calc_change_new(this)"></td>
                    <td><input class="form-control text-end" type="number" name="taxableamount[]" autocomplete="off" step="0.01"
                            min="0" readonly></td>
                    <td><input class="form-control text-end" type="number" name="vatamount[]" autocomplete="off" min="0"
                            readonly></td>
                    <td><input class="form-control text-end" type="number" name="totalamount[]" autocomplete="off" min="0"
                            readonly></td>
                    
                     @if($po->bill_number)
                            <td><input class="form-control" type="text" name="serial_no[]"></td>
                    @endif

                 
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


<div class="modal side-panel fade" id="narrationModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" >
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="poexcelimport">Enter Narration</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body">
                        <textarea class="form-control" style="height: 109px !important;" id="narrationTextarea" rows="6"
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
    <div class="modal-dialog  modal-md" style="height: 300px !important;">
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
{{-- Models --}}



<script>
    document.addEventListener('DOMContentLoaded', function () {
        const referenceInput = document.getElementById('reference');
        const narrationTextarea = document.getElementById('narrationTextarea');
        const insertButton = document.getElementById('insertNarration');
        const narrationModal = document.getElementById('narrationModal');

        // Pre-fill textarea when modal opens
        narrationModal.addEventListener('show.bs.modal', () => {
            narrationTextarea.value = referenceInput.value;
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
    let descriptionModal;
    document.addEventListener("DOMContentLoaded", function () {
        const descriptionElement = document.getElementById('descriptionModal');
        descriptionModal = new bootstrap.Modal(descriptionElement);
    });
    let currentDescriptionInput = null;

    $(document).on('click', 'input[name="description[]"]', function () {
        currentDescriptionInput = $(this);
        $('#add_description').val(currentDescriptionInput.val());
        descriptionModal.show();
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
    update_totals();

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
    const pageHeight = window.innerHeight-65;
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
                            $("#contact_person_name").val(dataResult['data'][i].contact_person);
                            $("#contact_person_email").val(dataResult['data'][i].email);
                            //$("#shipping_address_2").val(dataResult['data'][i].address2);
                            $("#contact_person_telephone").val(dataResult['data'][i].contcat_number);

                            $("#supplier_type").val(dataResult['data'][i].supplier_type);
                            $("#purchase_type").val(dataResult['data'][i].purchase_type);

                            //$("select[id=tax] option:first").text(dataResult['data'][i].vat_percentage +'%');
                            //$("select[id=tax] option:first").val(dataResult['data'][i].vat_percentage);
                            $("#tax").val(dataResult['data'][i].vat_percentage);

                            $("#country").val(dataResult['data'][i].vat_country).trigger('change');;
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
                            $("#shipping_name").val(dataResult['data'][i].contact_person);
                            $("#shipping_email").val(dataResult['data'][i].email);
                            $("#shipping_contact_no").val(dataResult['data'][i].contcat_number);
                       
                            //$("#shipping_name").val(dataResult['data'][i].customer_salutation+'. '+dataResult['data'][i].first_name+' '+dataResult['data'][i].last_name);
                            //$("#shipping_name").val(dataResult['data'][i].contcat_person);
                            $("#shipping_address_1").val(dataResult['data'][i].address + '\n' + dataResult['data'][i].address2);
                            //$("#shipping_email").val(dataResult['data'][i].email);
                            //$("#shipping_contact_no").val(dataResult['data'][i].contcat_number);
                        }
                    }
                    else {
                        $("#shipping_name").val("");
                        $("#shipping_email").val("");
                        $("#shipping_contact_no").val("");
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

<?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
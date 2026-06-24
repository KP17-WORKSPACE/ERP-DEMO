
<?php try { ?>






{{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'purchase-order-store-all', 'method' => 'POST', 'id' => 'tender-create-form', 'novalidate' => true]) }}
<input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
<input type="hidden" name="id" value="{{ isset($edit) ? $edit->id : '' }}">
<input type="hidden" name="net_vat" id="net_vat" value="0">

<div class="tab-pane fade show active" id="purchase-order-1" role="tabpanel" aria-labelledby="purchase-order-1-tab">
    <div class="purchase-order-content-header d-flex align-items-center justify-content-between mb-1 gap-2">
        <h4 class="purchase-order-content-header-left">
            New
            ({{ isset($edit) ? (!empty(@$edit->title) ? @$edit->title : old('doc_number')) : @App\SysHelper::get_new_code('sys_purchase_order', 'PO', 'doc_number') }})
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
                    <li data-bs-toggle="modal" data-bs-target="#addpoexcelimport"><a href="#"
                            class="dropdown-item">
                            <i class="ico icon-outline-import text-success"></i>
                            Import</a></li>

                    <li data-bs-toggle="modal" data-bs-target="#attachment_popup_win"><a href="#"
                            class="dropdown-item">
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
                       <label class="form-label mb-0 d-flex justify-content-between align-items-center">
                                <span>Vendor</span>
                                <a style="float: right; cursor: pointer;" class="text-success" data-bs-toggle="modal" data-bs-target="#addcompany"><i class="ico icon-bold-buildings-2" aria-hidden="true"></i> Add
                                            </a>
                            </label>
                    <select class=" js-account-select" name="vendors" id="vendors" required style="width: 100%;">
                        <option value=""></option>

                    </select>
                </div>
                <div class="col-2">
                    <label class="form-label">PO Number</label>
                    <div class="form-group">
                        <input readonly type="text" class="form-control" name="doc_number" autocomplete="off"
                            id="doc_number"
                            value="{{ isset($edit) ? (!empty(@$edit->title) ? @$edit->title : old('doc_number')) : @App\SysHelper::get_new_code('sys_purchase_order', 'PO', 'doc_number') }}" />
                    </div>
                </div>
                <div class="col-2">
                    <label class="form-label">PO Date</label>
                    @php
                        $value = \Carbon\Carbon::parse(old('po_date') ?? ($edit->date ?? now()))->format('d/m/Y');
                    @endphp

                    <div class="form-group">
                        <input type="text" id="po_date" name="po_date" class="form-control date-picker"
                            value="{{ @$value }}" />
                    </div>
                </div>
                <div class="col-2">
                    <label class="form-label">Currency</label>
                    <div class="form-group">
                        <select class="form-control select2 js-example-basic-single" name="currency" id="currency">
                            @foreach ($currency as $value)
                                <option value="{{ @$value->id }}" @if ($company->currency_id == $value->id) selected @endif>
                                    {{ @$value->code }}
                                </option>
                            @endforeach
                        </select>

                        
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
                       <input type="text" readonly class="form-control" name="created_by" id="created_by" value="{{ old('created_by', Auth::user()->full_name) }}">

                       
                    </div>
                    @if ($errors->has('currency'))
                        <span class="invalid-feedback invalid-select" role="alert">
                            <strong>{{ $errors->first('currency') }}</strong>
                        </span>
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
            <div class="tab-pane fade show active" id="extra-fields" role="tabpanel"
                aria-labelledby="extra-fields-tab">
                <div class="row gap-rows">
                    <div class="col-2">
                        <label class="form-label">Delivery Date</label>

                        @php
                            $value = \Carbon\Carbon::parse(old('delivery_date') ?? ($edit->date ?? now()))->format('d/m/Y');
                        @endphp

                        
                        <div class="form-group">
                            <input type="text" class="form-control date-picker" style="background-color: #deebe1;" id="delivery_date" name="delivery_date"
                                value="{{ @$value }}" required />
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Payment Terms*</label>
                        <div class="form-group">
                            <select onchange="this.setAttribute('title', this.options[this.selectedIndex].text)"
                                onmouseover="this.setAttribute('title', this.options[this.selectedIndex].text)"
                                required class="form-control js-example-basic-single" name="payment_terms" id="payment_terms"
                                onchange="fn_payment_terms()">
                                <option value="">Select</option>
                                @foreach ($paymentterms as $value)
                                    <option value="{{ @$value->id }}"
                                        {{ isset($edit) ? (!empty(@$edit->payment_terms) ? (@$edit->payment_terms == @$value->id ? 'selected' : '') : '') : '' }}>
                                        {{ @$value->title }}
                                    </option>
                                @endforeach
                            </select>
                         
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
                        <label class="form-label">Contact Person Name</label>
                        <div class="form-group">
                            <input type="text" class="form-control" id="contact_person_name" type="text"
                                name="contact_person_name" value="" required />
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Contact Person Email</label>
                        <div class="form-group">
                            <input type="text" class="form-control" id="contact_person_email" type="text"
                                name="contact_person_email" value="" required />
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Contact No</label>
                        <div class="form-group">
                            <input type="text" class="form-control" id="contact_person_telephone" type="text"
                                name="contact_person_telephone" value="" required />
                        </div>
                    </div>

                       <div class="col-2">
                        <label class="form-label">Customer Reference</label>

                         <!-- Visible text input (opens modal for multi-select) -->
                        <input class="form-control" type="text" name="customer_reference_input"
                            autocomplete="off" id="customer_reference_input" readonly value="{{ @$deal->customername->customer_name_display }}" placeholder="Click to select references" />

                        <!-- Hidden container to hold actual selected IDs for form submission -->
                        <div id="ref_company_hidden_inputs" style="display:none;">
                         
                            <input type="hidden" name="ref_company_id[]" value="{{ @$deal->cust_id }}">
                        </div>

                        
                        <!-- Modal with multi-select for choosing references -->
                        <div class="modal fade" id="customerReferenceModal" tabindex="-1" data-bs-backdrop="false" aria-hidden="true">
                            <div class="modal-dialog modal-md draggable" style="top:10rem;">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Select Customer References</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <label class="form-label">References</label>
                                        <select id="modal_ref_company_select" class="form-control js-example-basic-single" multiple style="width:100%">
                                             <option value="SO">STOCK ORDER</option>
                            @foreach ($customer_reference_list as $value)
                            <option value="{{ @$value->id }}" @if(@$deal->cust_id == @$value->id) selected @endif >{{ @$value->name }} 
                                @if(@App\SysHelper::getCompanyCodeSettings()['is_customer_code'])
                                        ({{ @$value->code }})
                                        @endif
                            </option>
                            @endforeach
                                        </select>
                                    </div>
                                    <div class="modal-footer">
                                        
                                        <button type="button" id="save_customer_reference" class="btn btn-light"><i class="ico icon-outline-bookmark-opened text-success"></i> Save</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <script>
                            $(document).ready(function () {
                                // Initialize Select2 inside modal
                                $('#modal_ref_company_select').select2({
                                    placeholder: 'Select references',
                                    dropdownParent: $('#customerReferenceModal'),
                                    width: '100%'
                                });

                                // Open modal on input click
                                $('#customer_reference_input').on('click', function () {
                                    // preload selection from hidden inputs
                                    let vals = $('#ref_company_hidden_inputs input[name="ref_company_id[]"]').map(function() { return $(this).val(); }).get();
                                    $('#modal_ref_company_select').val(vals).trigger('change');
                                    $('#customerReferenceModal').modal('show');
                                });

                                // Save selections back to visible input and hidden inputs
                                $('#save_customer_reference').on('click', function () {
                                    let selectedVals = $('#modal_ref_company_select').val() || [];
                                    let selectedTexts = $('#modal_ref_company_select').select2('data').map(function(d) { return d.text; });

                                    // Update visible text input to comma-separated names
                                    $('#customer_reference_input').val(selectedTexts.join(', '));

                                    // Update hidden inputs for form submission
                                    let $container = $('#ref_company_hidden_inputs');
                                    $container.empty();
                                    if (selectedVals.length === 0) {
                                        // keep an empty state (no inputs)
                                    } else {
                                        selectedVals.forEach(function(v) {
                                            // create one hidden input per selected value
                                            $container.append('<input type="hidden" name="ref_company_id[]" value="' + $('<div>').text(v).html() + '" />');
                                        });
                                    }

                                    $('#customerReferenceModal').modal('hide');
                                });

                                // If modal closed without save, do nothing (retain previous selection)
                            });
                        </script>

                        <!-- <div class="form-group">
                            <select class="form-control js-example-basic-single" name="ref_company_id[]" multiple id="ref_company_id">
                            <option value="">-Select-</option>
                            <option value="SO">STOCK ORDER</option>
                            @foreach ($customer_reference_list as $value)
                            <option value="{{ @$value->id }}" @if(@$deal->cust_id == @$value->id) selected @endif >{{ @$value->name }} 
                                @if(@App\SysHelper::getCompanyCodeSettings()['is_customer_code'])
                                        ({{ @$value->code }})
                                        @endif
                            </option>
                            @endforeach
                            

                        </select>
                            <input type="hidden" class="form-control" id="narration" type="text"
                                name="narration" value="{{ @$deal->customername->customer_name_display }}"  />
                        </div> -->
                    </div>
                    

                         <div class="col-2">
                        <label class="form-label">Sales Person Name</label>
                        <div class="form-group">
                            <select class="form-control js-example-basic-single" required name="sales_person"
                                id="sales_person">
                                <option value=""></option>
                                @foreach ($salesman as $value)
                                    <option value="{{ @$value->user_id }}" @if(isset($deal) && @$deal->owner == @$value->user_id) selected @endif>{{ @$value->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                   <div class="col-2">
                        <div class="input-effect">
                            <label class="dynamicslbl">@lang('Deal ID')*</label>
                            <input class="form-control" id="deal_id" type="text" name="deal_id"
                                value="@if(isset($deal)){{ App\SysHelper::get_code_from_dealid($deal->id) }}@else{{ 'Without Deal' }}@endif" required>
                        </div>
                    </div>

                

                  
                   
                    {{-- <div class="col-2">
                        <label class="form-label">Create Deal</label>
                        <div class="form-group">
                            <select class="form-control select2 js-example-basic-single" name="create_deal" id="create_deal" required
                                onchange="create_deal_change()">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                            
                        </div>
                    </div> --}}

                    

                    <div class="col-2">
                        <label class="form-label">Create Goods Receipt Note</label>
                        <div class="form-group">
                            <select class="form-control select2 js-example-basic-single" name="create_grn" id="create_grn"
                                onchange="fn_create_grn_pi()">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                                {{-- @if($deal_cart_det->req_grn == 1) selected @endif --}}
                            </select>
                            
                        </div>
                    </div>

                    <div class="col-2">
                        <label class="form-label">Create Purchase Invoice</label>
                        <div class="form-group">
                            <select class="form-control select2 js-example-basic-single" name="create_pi" id="create_pi"
                                onchange="fn_create_grn_pi()">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                                {{-- @if($deal_cart_det->req_pi == 1) selected @endif --}}
                            </select>
                            
                        </div>
                    </div>

                    <script>
                        function fn_create_grn_pi() {
                            var create_grn = $("#create_grn").val();


                            if (create_grn == "0") {
                                $("#create_pi").val("0");
                            }



                            var create_pi = $("#create_pi").val();


                            if (create_grn == "1" || create_pi == "1") {
                                $(".create_grn_pi").show();
                            } else {
                                $(".create_grn_pi").hide();
                            }


                            if (create_grn == "0") {
                                $(".serial-no-column").hide();
                            } else {
                                $(".serial-no-column").show();
                            }

                        }

                        $(document).ready(function() {
                            fn_create_grn_pi(); // Ensure correct initial state
                            $("#create_grn, #create_pi").change(fn_create_grn_pi);
                        });
                    </script>

                      <div class="col-2">
                        <label class="form-label">Internal Transfer</label>
                        <div class="form-group">
                            <select class="form-control select2 js-example-basic-single" id="internal_transfer" name="internal_transfer"
                                required>
                                <option value="">Select</option>
                                <option value="1">Yes</option>
                                <option value="2" selected>No</option>
                            </select>
                            
                        </div>
                    </div>

                    <div class="col-2  create_grn_pi" style="display: none;">
                        <div class="input-effect">
                            <label class="dynamicslbl form-label">@lang('Bill Number')</label>
                            <input class="form-control" id="bill_number" type="text" name="bill_number">
                        </div>
                    </div>
                    <div class="col-2 create_grn_pi" style="display: none;">
                        <div class="input-effect">
                            <label class="dynamicslbl form-label">@lang('Bill Date')</label>
                            <input class="form-control date-picker" id="bill_date" type="text" name="bill_date"
                                value="{{ old('bill_date', \Carbon\Carbon::now()->format('d/m/Y')) }}">
                        </div>
                    </div>
                    <div class="col-2  create_grn_pi" style="display: none;">
                        <div class="input-effect">
                            <label class="dynamicslbl">@lang('AWB No')</label>
                            <input class="form-control" id="awbno" type="text" name="awbno">
                        </div>
                    </div>
                    <div class="col-2 create_grn_pi" style="display: none;">
                        <div class="input-effect">
                            <label class="dynamicslbl form-label">@lang('BOE No')</label>
                            <input class="form-control" id="boeno" type="text" name="boeno">
                        </div>
                    </div>
                    
                    <div id="property-value-div" style="display: none;">
                        <input type="hidden" name="property_value" id="property_value" value="">
                        <input type="hidden" name="property_name" id="property_name" value="">
                    </div>
                    <div class="col-2">
                        <div class="input-effect">
                             <label class="form-label mb-0 d-flex justify-content-between align-items-center">
                                <span>Narration</span>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#ExtraNoteModal"  class="btn btn-sm p-0 ms-2" style="border:none;background:none;">
                                    <i class="ico icon-outline-add-square text-success" style="font-size:18px;"></i> Extra Note
                                </a>
                            </label>
                            <input class="form-control" data-bs-toggle="modal" data-bs-target="#narrationModal"
                                id="reference" type="text" name="reference">
                        </div>
                    </div>


                </div>
            </div>
            <div class="tab-pane fade" id="shipping-details-info" role="tabpanel"
                aria-labelledby="shipping-details-info-tab">
                <div class="row gap-rows">
                    <div class="col-3">
                        <label class="form-label">Company (Ship To)</label>
                        <div class="form-group">
                            <select class="form-control js-example-basic-single" name="shipping_supplier"
                                id="shipping_supplier" required style="width: 100%;">
                                <option value=""></option>
                                @foreach ($customer as $value)
                                   
                                    
                                    <option value="{{ @$value->id }}" @if ($value->company_ship_to_id == session('logged_session_data.company_id'))

                                        selected
                                            
                                        @endif>
                                        {{ @$value->account_name }} 
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
                                value="{{ session('logged_session_data.full_name') }}" />
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Contact Email</label>
                        <div class="form-group">
                            <input type="text" class="form-control" name="shipping_email" id="shipping_email"
                                value="{{ session('logged_session_data.email') }}" />
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Contact No</label>
                        <div class="form-group">
                            <input type="text" class="form-control" name="shipping_contact_no"
                                id="shipping_contact_no" value="{{ session('logged_session_data.mobile') }}" />
                        </div>
                    </div>
                    <div class="col-3">
                        <label class="form-label">Shipping Address</label>
                        <div class="form-group">
                            <input type="text" class="form-control" name="shipping_address_1"
                                id="shipping_address_1" />
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="tab-pane fade" id="vat-details" role="tabpanel" aria-labelledby="vat-details-tab">
                <div class="row row-cols-6 gap-rows">


                     <div class="col">
                        <label class="form-label">Supplier Country</label>
                        <div class="form-group">
                            <select class="form-control js-example-basic-single" style="width: 100%;"
                                name="supplier_country" id="country" required>
                                <option data-display="" value=""></option>
                                @foreach ($countries as $key => $value)
                                    <option value="{{ @$value->id }}" <?php        try {?>
                                        @if (isset($edit)) @if (@$edit->supplier_country == $value->id) selected @endif
                                        @endif <?php        } catch (\Throwable $th) {
                                    } ?>>{{ @$value->name }} </option>
                                @endforeach
                            </select>
                            {{--  --}}
                        </div>
                    </div>

                    <div class="col mb-2">
                    <div class="input-effect">
                        <label class="form-label">@lang('Supplier State') <span></span></label>

                        <div id="sectionStateDiv">
                         <select class="form-control js-example-basic-single" name="supplier_state" id="state" required>
                            <option value=""></option>

                            @foreach ($states as $value)
                                <option 
                                    value="{{ $value->id }}">
                                    {{ $value->name }}
                                </option>
                            @endforeach
                        </select>

                        </div>

                    </div>
                    </div>

                      <div class="col">
                        <label class="form-label">VAT %</label>
                        <div class="form-group">
                          
                           <input class="form-control" type="number"  name="vat_percent" id="vat_percent" value="{{ @$editData->vat_percent }}">
                        </div>
                    </div>

                    <div class="col">
                        <label class="form-label">VAT Number</label>
                        <div class="form-group">
                          
                           <input class="form-control" type="number"  name="vat_number" id="vat_number" value="{{ @$editData->vat_number }}">
                        </div>
                    </div>


                    

                    <div class="col">
                        <label class="form-label">Supplier Type</label>
                        <div class="form-group">
                            <select class="form-control js-example-basic-single {{ $errors->has('supplier_type') ? ' is-invalid' : '' }}"
                                name="supplier_type" id="supplier_type">
                                <option value="0"></option>
                                @foreach ($suppliertype as $value)
                                    <option value="{{ @$value->id }}"
                                        {{ isset($edit) ? (!empty(@$edit->supplier_type) ? (@$edit->supplier_type == @$value->id ? 'selected' : '') : '') : '' }}>
                                        {{ @$value->title }}
                                    </option>
                                @endforeach
                            </select>
                            
                        </div>
                    </div>
                    <div class="col">
                        <label class="form-label">Purchase Type</label>
                        <div class="form-group">
                            <select name="purchase_type" id="purchase_type"
                                class="form-control js-example-basic-single  {{ $errors->has('purchase_type') ? ' is-invalid' : '' }}"
                                id="inputVendorName">

                                @foreach ($purchasetype as $value)
                                    <option value="{{ @$value->id }}"
                                        {{ isset($edit) ? (!empty(@$edit->supplier_type) ? (@$edit->supplier_type == @$value->id ? 'selected' : '') : '') : '' }}>
                                        {{ @$value->title }}
                                    </option>
                                @endforeach
                            </select>
                            
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
                <textarea class="txtbx primary-input form-control" cols="0" rows="4" name="note">{{ isset($edit) ? (!empty(@$edit->note) ? @$edit->note : '') : old('description') }}</textarea>
                <span class="focus-border textarea"></span>
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
                    <th class="resizable text-center" width="80px">@lang('Taxable')
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="80px">@lang('VAT')
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="100px">@lang('Total')
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center serial-no-column" width="100px">@lang('Serial No')
                        <div class="resizer"></div>
                    </th>
                </tr>
            </thead>
            <tbody>




                
<?php $sort = 1; ?>

@if (count($deal_cart_items) > 0)
    @foreach ($deal_cart_items as $items)
     @php
         $vat_amount_cal = $items->tax * $items->value / 100;
     @endphp
        <tr>
            <td><input name="sort_id[]" type="text" class="form-control text-center" id="inputPONumber"
                    value="{{ $sort }}" /></td>
            <td class="noborder">
                <select class="form-control noborder " name="part_number[]">
                    <option value="{{ $items->part_number }}">{{ @$items->productname->part_number }}</option>
                </select>
                {{-- on focus add this class and its funcanalities js-product-select --}}
            </td>
            <td><textarea class="form-control" name="description[]" rows="1">{{ $items->productname->description }}</textarea>
                        <input class="form-control" type="text" name="part_number_txt[]" autocomplete="off"
                            readonly="true" hidden>
                        <input class="form-control" type="text" name="hscode_txt[]" autocomplete="off"
                            readonly="true" hidden>
                        <input class="form-control" type="text" name="product_type[]" autocomplete="off"
                            readonly="true" hidden>
                        <input class="form-control" type="text" name="product_type_part_number_text[]"
                            autocomplete="off" readonly="true" hidden>
                    </td>
                    <td><input type="text" class="form-control text-center" name="tax[]"
                            onchange="calc_change_new(this)" value="{{ number_format($items->tax, 0) }}"></td>
                    <td><input class="form-control text-center" type="text" name="qty[]" autocomplete="off" value="{{ @$items->qty }}"
                            min="0" onchange="calc_change_new(this)" onkeypress="set_license_key()"></td>
                    <td><input class="form-control text-end" type="text" name="unitprice[]" step="any" value="{{ @App\SysHelper::com_curr_format($items->unitprice,2,'.',',') }}"
                            autocomplete="off" min="0" onchange="calc_change_new(this)" onblur="formatCurrency(this)"></td>
                    <td><input class="form-control text-end" type="text" name="value[]" autocomplete="off" value="{{ @App\SysHelper::com_curr_format($items->value,2,'.',',') }}"
                            min="0" readonly>
                    </td>
                    <td><input class="form-control text-end" type="text" name="discount[]" autocomplete="off" value="{{ @App\SysHelper::com_curr_format($items->discount,2,'.',',') }}"
                            step="0.01" min="0" value="" onchange="calc_change_new(this)" onblur="formatCurrency(this)"></td>
                    <td><input class="form-control text-end" type="text" name="fright[]" autocomplete="off" value="{{ @App\SysHelper::com_curr_format($items->fright,2,'.',',') }}"
                            step="0.01" min="0" value="" onchange="calc_change_new(this)" onblur="formatCurrency(this)"></td>
                    <td><input class="form-control text-end" type="text" name="customcharges[]" value="{{ @App\SysHelper::com_curr_format($items->customcharges,2,'.',',') }}"
                            autocomplete="off" step="0.01" min="0" value=""
                            onchange="calc_change_new(this)"></td>
                    <td><input class="form-control text-end" type="text" name="taxableamount[]" value="{{ @App\SysHelper::com_curr_format($items->taxableamount,2,'.',',') }}"
                            autocomplete="off" step="0.01" min="0" readonly></td>
                    <td><input class="form-control text-end" type="text" name="vatamount[]" autocomplete="off" value="{{ @App\SysHelper::com_curr_format($vat_amount_cal,2,'.',',') }}"
                            min="0" readonly></td>
                    <td><input class="form-control text-end" type="text" name="totalamount[]" autocomplete="off" value="{{ @App\SysHelper::com_curr_format(($items->taxableamount ?? 0) + ($items->vatamount ?? 0),2,'.',',') ?: '0.00' }}" min="0" readonly></td>
                    <td class="serial-no-column"><input class="form-control" type="text" name="serial_no[]"></td>
        </tr>
<script>
// $(document).ready(function() {
//     $('input[name="qty[]"]').each(function() {
//         calc_change_new(this);
//     });
// });
</script>

        <?php $sort++; ?>
      
    @endforeach
     <script>
        update_totals();
    </script>
@endif




                <tr>
                    <td><input name="sort_id[]" type="text" class="form-control text-center" id="inputPONumber"
                            value="{{ $sort }}" /></td>
                    <td class="noborder">
                        <select class="form-control noborder " name="part_number[]">
                        </select>
                        {{-- on focus add this class and its funcanalities js-product-select --}}
                    </td>
                    <td><textarea class="form-control" name="description[]"  rows="1"></textarea>
                        <input class="form-control" type="text" name="part_number_txt[]" autocomplete="off"
                            readonly="true" hidden>
                        <input class="form-control" type="text" name="hscode_txt[]" autocomplete="off"
                            readonly="true" hidden>
                        <input class="form-control" type="text" name="product_type[]" autocomplete="off"
                            readonly="true" hidden>
                        <input class="form-control" type="text" name="product_type_part_number_text[]"
                            autocomplete="off" readonly="true" hidden>
                    </td>
                    <td><input type="text" class="form-control text-center" name="tax[]"
                            onchange="calc_change_new(this)"></td>
                    <td><input class="form-control text-center" type="text" name="qty[]" autocomplete="off"
                            min="0" onchange="calc_change_new(this)" onkeypress="set_license_key()"></td>
                    <td><input class="form-control text-end" type="text" name="unitprice[]" step="any"
                            autocomplete="off" min="0" onchange="calc_change_new(this)" onblur="formatCurrency(this)"></td>
                    <td><input class="form-control text-end" type="text" name="value[]" autocomplete="off"
                            min="0" readonly>
                    </td>
                    <td><input class="form-control text-end" type="text" name="discount[]" autocomplete="off"
                            step="0.01" min="0" value="" onchange="calc_change_new(this)" onblur="formatCurrency(this)"></td>
                    <td><input class="form-control text-end" type="text" name="fright[]" autocomplete="off"
                            step="0.01" min="0" value="" onchange="calc_change_new(this)" onblur="formatCurrency(this)"></td>
                    <td><input class="form-control text-end" type="text" name="customcharges[]"
                            autocomplete="off" step="0.01" min="0" value=""
                            onchange="calc_change_new(this)" onblur="formatCurrency(this)"></td>
                    <td><input class="form-control text-end" type="text" name="taxableamount[]"
                            autocomplete="off" step="0.01" min="0" readonly></td>
                    <td><input class="form-control text-end" type="text" name="vatamount[]" autocomplete="off"
                            min="0" readonly></td>
                    <td><input class="form-control text-end" type="text" name="totalamount[]" autocomplete="off"
                            min="0" readonly></td>
                    <td class="serial-no-column"><input class="form-control" type="text" name="serial_no[]"></td>
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
                    <th class="text-end serial-no-column" scope="col"></th>
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

<div class="modal side-panel fade" id="narrationModal" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg draggable">
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
    <div class="modal-dialog modal-md" style="height: 300px !important;">
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
    <div class="modal-dialog modal-md draggable" style="height: 300px !important;">
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
{{-- Models --}}


<div class="modal  fade" id="addpoexcelimport" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
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
                                <input class="form-control" type="file" id="excel-file"
                                    accept=".xlsx, .xls, .csv">
                            </div>

                            <div class="col-auto">
                                <button type="button" onclick="readExcel()" class="btn btn-success">Preview</button>
                            </div>

                            <div class="col-auto">
                                <small>(<a
                                        href="{{ url('public/uploads/product_upload/po_items_sample_format.csv') }}"
                                        target="_blank">Sample File</a>)</small>
                            </div>

                            <div class="col-md-12 mt-2">
                                <table id="excel-table" class="table table-bordered table-striped"
                                    style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="width:220px;">Part No</th>
                                            <th>Description</th>
                                            <th style="width:70px;">Qty</th>
                                            <th style="width:100px;" class="text-end">Unit Price</th>
                                            <th style="width:100px;" class="text-end">Discount</th>
                                            <th style="width:100px;" class="text-end">VAT</th>

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
                <button type="submit" class="btn btn-light add-btn ms-2">
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
                                    <input class="form-control" type="text" id="doc_name" name="doc_name"
                                        value="" />
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
    document.addEventListener('DOMContentLoaded', function() {
        const referenceInput = document.getElementById('reference');
        const narrationTextarea = document.getElementById('narrationTextarea');
        const insertButton = document.getElementById('insertNarration');
        const narrationModal = document.getElementById('narrationModal');

        // Pre-fill textarea when modal opens
        narrationModal.addEventListener('shown.bs.modal', () => {
            narrationTextarea.value = referenceInput.value;
            setTimeout(() => narrationTextarea.focus(), 100);
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

    document.getElementById("freight_add_btn").addEventListener("click", function() {
        splitAmount('freightInput', 'fright');
        $('#freightModal').modal('hide');
    });

    document.getElementById("custom_add_btn").addEventListener("click", function() {
        splitAmount('customCharges', 'customcharges');
        $('#customModal').modal('hide');
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
$(document).on("keydown", 'input[name="tax[]"], input[name="qty[]"], input[name="unitprice[]"], input[name="discount[]"], input[name="fright[]"], input[name="customcharges[]"], input[name="serial_no[]"]', function(e) {
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

<script>
    update_totals();

    function calc_change_new(el) {
        $("#loading_bg").css("display", "block");

        // Get the current row
        var $row = $(el).closest('tr');

        // Read values from the current row
        var net_vat = $row.find('input[name="tax[]"]').val() || '0';

        var qty = $row.find('input[name="qty[]"]').val() || '';
        var unitprice = ($row.find('input[name="unitprice[]"]').val() || '').replace(/,/g, '');
        var discount = ($row.find('input[name="discount[]"]').val() || '').replace(/,/g, '') || '0';
        var fright = ($row.find('input[name="fright[]"]').val() || '').replace(/,/g, '') || '0';
        var customcharges = ($row.find('input[name="customcharges[]"]').val() || '').replace(/,/g, '') || '0';

        var decimal_point = @json(session('logged_session_data.decimal_point'));

        // Calculate value
        var qtyNum = parseFloat(qty) || 0;
        var unitpriceNum = parseFloat(unitprice) || 0;
        var fin_value = unitpriceNum * qtyNum;
        $row.find('input[name="value[]"]').val(formatAmount(fin_value));

        

        // console.log( parseFloat(customcharges), parseFloat(fright), parseFloat(discount))
        // Calculate taxable amount
        var fin_taxableamount = fin_value + (parseFloat(customcharges) || 0) + (parseFloat(fright) || 0) - (parseFloat(discount) || 0);
        $row.find('input[name="taxableamount[]"]').val(formatAmount(fin_taxableamount));

        // Calculate VAT
        var fin_vatamount = fin_taxableamount * ((parseFloat(net_vat) || 0) / 100);
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
            total_fright = 0,
            total_customcharges = 0,
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
            total_fright += parseFloat($row.find('input[name="fright[]"]').val().replace(/,/g, '')) || 0;
            total_customcharges += parseFloat($row.find('input[name="customcharges[]"]').val().replace(/,/g, '')) || 0;
            total_taxableamount += parseFloat($row.find('input[name="taxableamount[]"]').val().replace(/,/g, '')) || 0;
            total_vatamount += parseFloat($row.find('input[name="vatamount[]"]').val().replace(/,/g, '')) || 0;
            total_totalamount += parseFloat($row.find('input[name="totalamount[]"]').val().replace(/,/g, '')) || 0;
        });

        $('#lbl_total_qty').text(total_qty.toFixed(decimal_point));
        $('#lbl_total_price').text(formatAmount(total_price));
        $('#lbl_total_value').text(formatAmount(total_value));
        $('#lbl_total_discount').text(formatAmount(total_discount));
        $('#lbl_total_fright').text(formatAmount(total_fright));
        $('#lbl_total_customcharges').text(formatAmount(total_customcharges));
        $('#lbl_total_taxableamount').text(formatAmount(total_taxableamount));
        $('#lbl_total_vatamount').text(formatAmount(total_vatamount));
        $('#lbl_total_totalamount').text(formatAmount(total_totalamount));
    }
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

   const SHOW_SUPPLIER_CODE = {{ @App\SysHelper::getCompanyCodeSettings()['is_supplier_code'] ? 'true' : 'false' }};


    $(document).ready(function() {
        function initAccountSelect2(selector) {
            $(selector).select2({
                ajax: {
                    url: '{{ route('autocomplete.get_supp_account_list_ajax') }}',
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

                                if (SHOW_SUPPLIER_CODE) {
                                    text = item.account_name + " (" + item.account_code + ")";
                                } else {
                                    text = item.account_name;  // no code
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

        // When the vendors select2 opens, prefill the search box with the currently selected vendor name
        $('#vendors').on('select2:open', function(e) {
            try {
                var sel = $(this).select2('data');
                if (sel && sel.length && sel[0].text) {
                    setTimeout(function() {
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
            } catch (err) {
                console.error('Error prefilling vendors search field', err);
            }
        });

        // Auto-open vendors dropdown on page load
        setTimeout(function() {
            $('#vendors').select2('open');
        }, 500);
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

                // Default monetary fields to 0.00 when a part is selected
                $row.find('input[name="discount[]"]').val('0.00');
                $row.find('input[name="fright[]"]').val('0.00');
                $row.find('input[name="customcharges[]"]').val('0.00');

                $row.find('input[name="tax[]"]').val($('#net_vat').val());

                // Recalculate the row (pass a field within the row to the calc function)
                calc_change_new($row.find('input[name="qty[]"]')[0]);

                $row.find('input[name="qty[]"]').focus();
            });

 // prefill Select2 search with currently selected value when dropdown opens
            $(selector).on('select2:open', function() {
                try {
                    var sel = $(this).select2('data');
                    if (sel && sel.length && sel[0].text) {
                        setTimeout(function() {
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
    $(document).ready(function() {
        $('#add-btn-modal').on('click', function(e) {
            e.preventDefault();

            var formData = $('#productForm').serialize();

            $.ajax({
                url: "{{ route('product.modalsave') }}", // Update with your route name
                type: "POST",
                data: formData,
                success: function(response) {
                    if (response.success) {
                        alert('Product saved successfully.');
                        $('#addproductModal').modal('hide'); // optional
                        // Optionally reload table or clear form
                    } else {
                        alert('Something went wrong.');
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    alert('An error occurred. Please check console.');
                }
            });
        });


        $(document).on("change", "#vendors", function() {
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

        window.get_vendors_detail = function(id) {
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

                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var len = 0;
                    var state = null;
                    if (dataResult['data'] != null) {
                        len = dataResult['data'].length;
                    }
                    if (len > 0) {
                        for (var i = 0; i < len; i++) {
                            console.log(dataResult['data'][i]);
                            $("#payment_terms").val(dataResult['data'][i].payment_terms).trigger('change');
                            $("#contact_person_name").val(dataResult['data'][i].contact_person).trigger('change');
                            $("#contact_person_email").val(dataResult['data'][i].email);
                            //$("#shipping_address_2").val(dataResult['data'][i].address2);

                             const numbers = [
    dataResult?.data?.[i]?.contcat_number,
    dataResult?.data?.[i]?.mobile
]
.map(n => n?.trim())
.filter(Boolean);

$("#contact_person_telephone").val(numbers.join(' / '));

                            // $("#contact_person_telephone").val(dataResult['data'][i]
                            // .contcat_number);

                            $("#supplier_type").val(dataResult['data'][i].supplier_type).trigger('change');
                            $("#purchase_type").val(dataResult['data'][i].purchase_type).trigger('change');
                            $('#vat_percent').val(dataResult['data'][i].vat_percentage);
                            $('#vat_number').val(dataResult['data'][i].vat_number);

                            //$("select[id=tax] option:first").text(dataResult['data'][i].vat_percentage +'%');
                            //$("select[id=tax] option:first").val(dataResult['data'][i].vat_percentage);
                            $("#tax").val(dataResult['data'][i].vat_percentage);

                            $("#country").val(dataResult['data'][i].vat_country).trigger('change');

                    

                             window.SELECTED_STATE_ID = dataResult['data'][i].vat_state;
                            
                         
                        }

                        console.log(state)


                    } else {
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
        };

        $(document).on("change", "#shipping_supplier", function() {
            var id = $("#shipping_supplier").val();
            get_shipping_supplier_detail2(id);
        });

        function get_shipping_supplier_detail2(id) {
            console.log(id);
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

        $("#shipping_supplier").change();


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


        jQuery(document).ready(function() {
            jQuery('input').keypress(function(event) {
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

        if ($('#att_file').val() == "") {
            $('#att_file').focus();
            $("#loading_bg").css("display", "none");
            return false;
        }

        var action = "{{ URL::to('add-purchase-order-attachment') }}";

        var formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}'); // Append CSRF token
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
            success: function(dataResult) {
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
                                    <td><button onclick='delete_attachment(" + dataResult['data'][i].id + ")' class='btn btn-sm btn-danger'><i class='ico icon-bold-trash-bin-2' style='font-size:16px' aria-hidden='true'></i></button></td>\
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
            success: function(dataResult) {
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
                                    <td><button onclick='delete_attachment(" + dataResult['data'][i].id + ")' class='btn btn-sm btn-danger'><i class='ico icon-bold-trash-bin-2' style='font-size:16px' aria-hidden='true'></i></button></td>\
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
            success: function(dataResult) {
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
                                    <td><button onclick='delete_attachment(" + dataResult['data'][i].id + ")' class='btn btn-sm btn-danger'><i class='ico icon-bold-trash-bin-2' style='font-size:16px' aria-hidden='true'></i></button></td>\
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
        reader.onload = function(event) {
            var data = event.target.result;
            var workbook = XLSX.read(data, {
                type: 'binary'
            });

            // Assuming the data is in the first sheet
            var sheet = workbook.Sheets[workbook.SheetNames[0]];
            var rows = XLSX.utils.sheet_to_json(sheet, {
                header: 1
            });

            var tableBody = document.getElementById('excel-table').getElementsByTagName('tbody')[0];
            tableBody.innerHTML = ""; // Clear any previous data

            // Loop through each row and add data to the table
            for (var i = 1; i < rows.length; i++) { // Skip header row
                var row = rows[i];
                if (row.length < 6) continue; // Skip invalid rows



                var part_number = <?php echo json_encode($part_number); ?>; // Convert PHP array to JS array

                var lowercase_part_number = part_number.map(function(value) {
                    return value.toLowerCase();
                });

                var json_output = JSON.stringify(lowercase_part_number);

                var newRow = tableBody.insertRow(tableBody.rows.length);

                var rowVal = String(row[0] ?? '');
                var trimmedValue = rowVal.trim();

                if (json_output.includes(trimmedValue.toLowerCase())) { // Use .includes() for array checking

                } else {
                    newRow.style.backgroundColor = "#ffbebe";
                }

                // Part No
                var partNoCell = newRow.insertCell(0);
                var partNoInput = document.createElement('input');
                partNoInput.type = 'text'; // Change to text input
                partNoInput.name = 'excel_part_no[]';
                partNoInput.value = rowVal.trim();
                partNoInput.classList.add('form-control');
                partNoCell.appendChild(partNoInput);

                // Description
                var descriptionCell = newRow.insertCell(1);
                var descriptionInput = document.createElement('input');
                descriptionInput.type = 'text'; // Change to text input
                descriptionInput.name = 'excel_description[]';
                descriptionInput.value = row[1].trim();
                descriptionInput.classList.add('form-control');
                descriptionCell.appendChild(descriptionInput);

                // Qty
                var qtyCell = newRow.insertCell(2);
                var qtyInput = document.createElement('input');
                qtyInput.type = 'text'; // Change to text input
                qtyInput.name = 'excel_qty[]';
                qtyInput.value = row[2];
                qtyInput.classList.add('form-control');
                qtyCell.appendChild(qtyInput);

                // Unit Price (Right-aligned)
                var unitPriceCell = newRow.insertCell(3);
                var unitPriceInput = document.createElement('input');
                unitPriceInput.type = 'text'; // Change to text input
                unitPriceInput.name = 'excel_unit_price[]';
                unitPriceInput.value = row[3];
                unitPriceInput.classList.add('text-end');
                unitPriceInput.classList.add('form-control');
                unitPriceCell.appendChild(unitPriceInput);

                // Discount (Right-aligned)
                var discountCell = newRow.insertCell(4);
                var discountInput = document.createElement('input');
                discountInput.type = 'text'; // Change to text input
                discountInput.name = 'excel_discount[]';
                discountInput.value = row[4];
                discountInput.classList.add('text-end');
                discountInput.classList.add('form-control');
                discountCell.appendChild(discountInput);

                // VAT (Right-aligned)
                var vatCell = newRow.insertCell(5);
                var vatInput = document.createElement('input');
                vatInput.type = 'text'; // Change to text input
                vatInput.name = 'vat_excel[]';
                vatInput.value = row[5];
                vatInput.classList.add('text-end');
                vatInput.classList.add('form-control');
                vatCell.appendChild(vatInput);

                var deleteCell = newRow.insertCell(6); // Last cell for delete button
                var deleteButton = document.createElement('button');
                deleteButton.type = 'button'; // Prevent form submission
                deleteButton.classList.add('btn-sm', 'btn-danger');
                deleteButton.innerHTML = '<i class="ico icon-bold-trash-bin-2" style="font-size: 16px;"></i>';
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

>


{{-- Form Validation Script --}}
<script src="{{ asset('public/js/form-validation-toastr.js') }}"></script>
<script>
$(document).ready(function() {
    // Initialize form validation for crm-deals-form
    FormValidator.init('tender-create-form', {
        showAllErrors: true,
        scrollToFirst: true,
        highlightFields: true,
        toastrPosition: 'toast-top-right',
        toastrTimeout: 6000
    });
});
</script>

  <script>

(function () {

    let dragging = false;
    let startX, startY, startLeft, startTop;
    let currentModal = null;

    // Bind drag start
    $(document).on('mousedown', '.modal-dialog.draggable .modal-header', function (e) {
        currentModal = $(this).closest('.modal-dialog');

        dragging = true;

        startX = e.clientX;
        startY = e.clientY;

        const offset = currentModal.offset();
        startLeft = offset.left;
        startTop = offset.top;

        $('body').addClass('unselectable'); // Prevents text selection while dragging
    });

    // Dragging movement
    $(document).on('mousemove', function (e) {
        if (!dragging || !currentModal) return;

        let newLeft = startLeft + (e.clientX - startX);
        let newTop = startTop + (e.clientY - startY);

        currentModal.offset({
            top: newTop,
            left: newLeft
        });
    });

    // Stop drag
    $(document).on('mouseup', function () {
        dragging = false;
        $('body').removeClass('unselectable');
    });

    // Reset modal on open (production behavior)
    $(document).on('show.bs.modal', '.modal', function () {
        let dialog = $(this).find('.modal-dialog.draggable');
        dialog.css({
            top: '10%',
            left: '65%',
            transform: 'translateX(-50%)'
        });
    });

})();
</script> 


<div class="modal side-panel fade" id="ExtraNoteModal" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg draggable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="poexcelimport">Enter Extra Note (This note will be shown in PDF)</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                   <div class="card-body">
    <div class="d-flex gap-3">
        <div class="flex-fill">
            <label for="extraProperty" class="form-label">Property</label>
            <input type="text" class="form-control" id="extraProperty" placeholder="Enter property">
        </div>

        <div class="flex-fill">
            <label for="extraValue" class="form-label">Value</label>
            <input type="text" class="form-control" id="extraValue" placeholder="Enter value">
        </div>
    </div>
</div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="insertExtraNote" class="btn btn-light add-btn ms-2">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('insertExtraNote').addEventListener('click', function () {
    const property = document.getElementById('extraProperty').value.trim();
    const value = document.getElementById('extraValue').value.trim();

    // Set hidden inputs
    document.getElementById('property_name').value = property;
    document.getElementById('property_value').value = value;

    // Optional: close modal
    const modalEl = document.getElementById('ExtraNoteModal');
    const modal = bootstrap.Modal.getInstance(modalEl);
    if (modal) modal.hide();
});
</script>


<div class="modal side-panel fade" id="addcompany" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" style="top:25%;max-width:1000px!important;left: 37%;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLabel">Add Supplier</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body m-0 p-0">
                        <div class="card mb-0 mt-0">
                    <div class="card-body">
                        <div class="row gap-rows row-cols-5">
                            
                            <div class="col">
                                <div class="">
                                    <label for="" class="form-label">Supplier Name</label>
                                    <input class="form-control" placeholder="" type="text" aria-describedby="" autocomplete="off" id="company_name_add" required>
                                    <style>
                                         #company_name_add_list ul {
                                                width: 380px;
                                                left:16rem
                                            }
                                    </style>
                                    <div id="company_name_add_list">
                                    </div>                            
                                    <script>
                                        $(document).ready(function(){

                                         $('#company_name_add').keyup(function(){ 
                                                var query = $(this).val();
                                                if(query != '')
                                                {
                                                 var _token = $('input[name="_token"]').val();
                                                 $.ajax({
                                                  url:"{{ route('autocomplete.supplier_name') }}",
                                                  method:"POST",
                                                  data:{query:query, _token:_token},
                                                  success:function(data){
                                                   $('#company_name_add_list').fadeIn();  
                                                            $('#company_name_add_list').html(data);
                                                  }
                                                 });
                                                }
                                            });

                                            $(document).on('click', 'li', function(){  
                                                $('#customer_name').val('');
                                        $('#customer_name_display').val('');
                                        // toastr.info('Customer Already Exists.', 'Info');



                                        $('#company_name_add_list').fadeOut(); 
                                            });  

                                            $(document).click(function(e) {
                                            if (!$(e.target).closest('#company_name_add, #company_name_add_list').length) {
                                                $('#company_name_add_list').fadeOut();
                                            }
                                        });

                                        });
                                        </script>
                                </div>
                            </div>
                          
                            <div class="col">
                                <label for="" class="form-label">Contact Person</label>
                                <div class="d-flex gap-2 align-items-end">
                                    <div style="min-width: 54px; max-width: 54px;">
                                        <select class="form-control js-example-basic-single" id="salutation_cust" name="customer_salutation_add" required>
                                            <option value="Mr.">Mr.</option>
                                            <option value="Mrs.">Mrs.</option>
                                            <option value="Miss.">Miss.</option>
                                        </select>
                                    </div>
                                    <div class="flex-grow-1">
                                        <input class="form-control" type="text" autocomplete="off" id="cust_name_add" name="cust_name_add" required placeholder="Name">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col">
                                <div class="">
                                    <label for="" class="form-label">Mobile</label>
                                    <input class="form-control" type="text" autocomplete="off" id="cust_no_add" required value="+971">
                                </div>
                            </div>
                            <div class="col">
                                <div class="">
                                    <label for="" class="form-label">Email</label>
                                    <input class="form-control" type="text" autocomplete="off" id="cust_email_add" required >
                                </div>
                            </div>

                            @php
                                $country_1 = @App\SysCountries::select('id', 'name', 'iso2')->get();
                                $designation_1 = @App\SmDesignation::select('title')->where('active_status', 1)->get();

                                $sales_person = @App\SysHelper::get_only_sales_persons();
                            @endphp

                            <div class="col">
                                <div class="">
                                    <label for="" class="form-label">Country</label>
                                    <select class="form-control js-example-basic-single" name="country_ship" id="country_ship">
                                        <option value="">-Select-</option>
                                        @foreach ($country_1 as $value)
                                        <option value="{{ @$value->id }}" {{ trim(strtolower($value->name)) == 'united arab emirates' ? 'selected' : '' }}>{{ @$value->name }}</option>
                                        @endforeach
                                    </select>

                                       <div style="display:none">

                                           <select class="form-select js-example-basic-single" style="width:30px;display:none" name="country_telephone" id="country_telephone" required>
                                                <option value="" disabled selected>Select Country</option>
                                                @foreach ($country_1 as $key => $value)
                                                    <option value="{{ @$value->iso2 }}|{{ @$value->id }}">{{ @$value->name }}</option>
                                                @endforeach
                                            </select>
                                </div>

                                </div>
                            </div>
                            
                          
                            <div class="col">
                                <div class="">
                                    <label for="" class="form-label">State</label>                                
                                    <div id="sectionStateDiv_ship">
                                        <select class="form-control js-example-basic-single" name="state_ship" id="state_ship">
                                            <option data-display="" value=""></option>
                                            <?php    try { ?>
                                            @if (isset($editData) && $editData->vat_state != '')
                                                <option data-display="{{ $editData->vatstate->name }}"
                                                    value="{{ $editData->vat_state }}" selected>
                                                    {{ $editData->vatstate->name }}</option>
                                            @endif
                                            <?php    } catch (\Exception $e) {
        } ?>
                                        </select>
                                    </div>

                                </div>
                            </div>

                             <div class="col">
                                <div class="">
                                    <label for="" class="form-label">City</label>
                                    <input class="form-control" type="text" autocomplete="off" id="cust_city" required>
                                </div>
                            </div>

                            
                             <div class="col">
                                <label for="" class="form-label">Area</label>
                                <input class="form-control" type="text" autocomplete="off" id="cust_area"
                                    required>

                            </div>

                              <div class="col">
                                <label for="" class="form-label">Building Name</label>
                                <input class="form-control" type="text" autocomplete="off" id="cust_building_name"
                                    required>

                            </div>

                              <div class="col">
                                <label for="" class="form-label">Flat/Office No</label>
                                <input class="form-control" type="text" autocomplete="off" id="cust_flat_office_no"
                                    required>

                            </div>
                           
                             <div class="col">
                                <div class="">
                                    <label for="" class="form-label">PO Box</label>
                                    <input class="form-control" type="text" autocomplete="off" id="cust_pobox" required>
                                </div>
                            </div>
                                <input type="hidden" name="place_id" id="place_id" value="">
                            <input type="hidden" name="customer_website" id="customer_website" value="">
                            <input type="hidden" name="maps_location" id="maps_location" value="">

                           
                            <div class="col">
                                <div class="">
                                    <label for="" class="form-label">Payment Terms</label>
                                    <select class="form-control js-example-basic-single" id="customer_payment_terms" required>
                                        @foreach ($paymentterms as $key => $value)
                                            <option value="{{ @$value->id }}" @if ($value->id == 3) selected @endif>{{ @$value->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col">
                                <div class="">
                                    <label for="" class="form-label">Supplier Type</label>
                                    <select class="form-control js-example-basic-single" id="account_type" required>
                                        <option value="">-Select-</option>
                                        <option value="1" selected>Vendor</option>
                                        <option value="2">Forwader</option>
                                        <option value="3">Courier</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col">
                                <div class="">
                                    <label for="" class="form-label">Designation</label>
                                    <select class="form-control js-example-basic-single" name="designation_add" id="designation_add" required>
                                        <option value="">--Designation--</option>
                                        @if (count($designation_1) > 0)
                                            @foreach ($designation_1 as $val)
                                                <option value="{{ $val->title }}" {{ trim(strtolower($val->title)) == 'purchase' ? 'selected' : '' }}>{{ $val->title }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                          


                        </div>
                        </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        
						<button type="button" class="btn btn-light add-btn ms-2" id="btn_add_company">
							<i class="ico icon-outline-bookmark-opened text-success"></i> Save & Close
						</button>
                    </div>
                </div>
            </div>
        </div>



        <script
  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBhC6fAWLvqoE4znv7d8ovf8y3pMR0OG7s&libraries=places&language=en">
</script>



 <script>
        $(document).ready(function () {
            const input = document.getElementById("company_name_add");
            
            const autocomplete = new google.maps.places.Autocomplete(input, {
                types: ["establishment"],
                fields: [
                    "place_id",
                    "name",
                    "formatted_address",
                    "address_components",
                    "geometry",
                    "plus_code",
                    "international_phone_number",
                    "formatted_phone_number",
                    "website",
                    "url"
            ]
                  
            });
            
            autocomplete.addListener("place_changed", () => {
                const place = autocomplete.getPlace();
                
                if (!place.place_id) {
                    console.error("No place details found");
                    return;
                }

                 $('#company_name_add_list').fadeOut();

                
                console.log("📍 COMPLETE PLACE DATA:", place);
                
                // Extract structured address
                let address = {
                    country: "",
                    country_code: "",
                    state: "",
                    city: "",
                    area: "",
                    building_name: "",
                    street_number: "",
                    route: "",
                    po_box: "",
                    postal_code: ""
                };
                
                if (place.address_components) {
                    place.address_components.forEach(component => {
                        const types = component.types;
                        
                        if (types.includes("country")) {
                            address.country = component.long_name;
                            address.country_code = component.short_name;
                        }
                        if (types.includes("administrative_area_level_1")) {
                            address.state = component.long_name;
                        }
                        if (types.includes("locality")) {
                            address.city = component.long_name;
                        }
                        if (types.includes("sublocality") || types.includes("sublocality_level_1")) {
                            address.area = component.long_name;
                        }
                        if (types.includes("premise") || types.includes("neighborhood")) {
                            address.building_name = component.long_name;
                        }
                        if (types.includes("street_number")) {
                            address.street_number = component.long_name;
                        }
                        if (types.includes("route")) {
                            address.route = component.long_name;
                        }
                        if (types.includes("post_box")) {
                            address.po_box = component.long_name;
                        }
                        if (types.includes("postal_code")) {
                            address.postal_code = component.long_name;
                            console.log("Postal Code:", address.postal_code);
                        }
                    });
                }
                
                // Extract country mobile code from phone number
                let mobileCode = "";
                if (place.international_phone_number) {
                    const match = place.international_phone_number.match(/^\+(\d{1,4})/);
                    if (match) {
                        mobileCode = "+" + match[1];
                    }
                }
                
                // Get coordinates
                const lat = place.geometry?.location?.lat() || "";
                const lng = place.geometry?.location?.lng() || "";
                
                // Fill all form fields
                setFieldvalue("company_name_add", place.name || "");
                setFieldvalue("cust_no_add", place.international_phone_number || "");
                // setFieldvalue("mobile_code", mobileCode);
                setFieldvalue("customer_website", place.website || "");
            
                
                setFieldvalue("maps_location", place.url || "");
                
                // setFieldvalue("country", address.country);
                var targetName = address.country || ""; // or any value you want to match


                function normalize(str) {
    return str.toLowerCase().replace(/\s+/g, '');
}

                // Select the option based on its text (the visible name)
                $('#country_ship option').each(function() {
                     if (normalize($(this).text()) === normalize(targetName)) {
        $(this).prop('selected', true);
          $('#country_ship').trigger('change');
    }
                });

                

                var targetName = address.state || ""; // or any value you want to match


              // Delay execution to ensure options are loaded
setTimeout(function() {
    var matched = false;

    $('#state_ship option').each(function() {
        if (normalize($(this).text()) === normalize(targetName)) {
            $(this).prop('selected', true);
            matched = true;
            return false; // break the loop
        }
    });

    if (matched) {
        $('#state_ship').trigger('change');
    }

    console.log("State selection attempted for:", targetName);
}, 600); // adjust 300ms as needed


                setFieldvalue("cust_city", address.city);
                setFieldvalue("cust_area", address.area);
                setFieldvalue("cust_building_name", address.building_name);
                setFieldvalue("cust_pobox", address.postal_code);
                setFieldvalue("place_id", place.place_id);

                setFieldvalue("longitude", lng);
                setFieldvalue("latitude", lat);


                // Show success message
                $("#successAlert").addClass("show");
                setTimeout(() => {
                    $("#successAlert").removeClass("show");
                }, 3000);
                
            });
            
            // Helper function to set field value and add visual feedback
            function setFieldvalue(fieldId, value) {
                const field = $("#" + fieldId);
                if (field.length) {
                    field.val(value);
                    if (value) {
                        field.addClass("filled");
                    } else {
                        field.removeClass("filled");
                    }
                }
            }
        });
</script>


        <!-- External JS for country codes -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"></script>

<script>
    $(document).ready(function () {

    $('#country_ship').on('change', function () {

        console.log("!222121")

        let shipId = $(this).val();    // example: "5"

        if (!shipId) {
            $('#country_telephone').val("").trigger('change');
            return;
        }

        // Loop through telephone options
        $('#country_telephone option').each(function () {
            let value = $(this).val(); // example: "AE|5"

            if (!value) return;

            let parts = value.split('|');
            let telId = parts[1]; // country ID

            if (telId == shipId) {
                $('#country_telephone').val(value).trigger('change');
            }
        });

    });

});

</script>
<script>
$(document).ready(function() {
    var countryCodes = {};

    $.each(window.intlTelInputGlobals.getCountryData(), function(index, country) {
        countryCodes[country.iso2.toLowerCase()] = country.dialCode;
    });

    $('#country_telephone').on('change', function() {
        var fullValue = $(this).val();
        var iso2 = fullValue ? fullValue.split('|')[0].toLowerCase() : '';
        var code = countryCodes[iso2] || '';
        var currentNumber = $('#cust_no_add').val().replace(/^\+\d+\s?/, '');

        $('#cust_no_add').val(code ? '+' + code + ' ' + currentNumber : currentNumber);
    });
});
</script>
<script>
$(document).ready(function() {
    if ($('#country_ship').val() !== '') {
        $('#country_ship').trigger('change');
    }
});


  $(document).on("click", "#btn_add_company", function () {

                //$("#btn_add_company").css("display", "none");

                var company_name_add = $("#company_name_add").val();
                var cust_name_add = $("#cust_name_add").val();
                var designation_add = $("#designation_add").val();
                var cust_no_add = $("#cust_no_add").val();
                var cust_email_add = $("#cust_email_add").val();

                var cust_area = $("#cust_area").val();
                var place_id = $("#place_id").val();
                var cust_building_name = $("#cust_building_name").val();
                var cust_flat_office_no = $("#cust_flat_office_no").val();
                var customer_website = $("#customer_website").val();
                var maps_location = $("#maps_location").val();
                var customer_salutation_add = $("#salutation_cust").val();


            

             
                var country_add = $("#country_ship").val();

                var cust_city = $("#cust_city").val();
                var state_ship = $("#state_ship").val();
                var cust_pobox = $("#cust_pobox").val();
                var sales_person = $("#cust_sales_person").val();

                var payment_terms = $("#customer_payment_terms").val();
                var account_type = $("#account_type").val();
                var company_id = $("#company").val();

                var action = "{{ URL::to('add-customer-detail-popup') }}";
            $("#loading_bg").show();

                $.ajax({
                    url: action,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}',
                        company_name_add: company_name_add,
                        cust_name_add: cust_name_add,
                        customer_salutation_add: customer_salutation_add,
                        designation_add: designation_add,
                        cust_no_add: cust_no_add,
                        cust_email_add: cust_email_add,
                        vat_country: country_add,
                        city: cust_city,
                        vat_state: state_ship,
                        zip_code: cust_pobox,
                        payment_terms: payment_terms,
                        account_type: account_type,
                        supplier_type: account_type,
                        company_id: company_id,
                        customer_website: customer_website,
                        maps_location: maps_location,
                        place_id: place_id,
                        area: cust_area,
                        building_name: cust_building_name,
                        flat_no: cust_flat_office_no,
                        type : 2,
                    },
                    cache: false,
                    success: function(dataResult) {
                        //alert(dataResult);
                        var dataResult = JSON.parse(dataResult);
                        var len = 0;
                        if(dataResult['data']=="ERROR")
                        {
                            alert("Error found in something!!");
                            $("#btn_add_company").css("display", "block");
                        }
                        else if(dataResult['data']=="ERROR2")
                        {
                            alert("Supplier Name already exists!! Please Contact Support");
                            $('#company_name_add').css("border", "1px solid red"); $('#company_name_add').focus();
                            $("#btn_add_company").css("display", "block");
                        }
                        else{
                            if(dataResult['data'] != null){
                            len = dataResult['data'].length;
                            }
                            if(len > 0){

                                var newAccountId = dataResult['new_account_id'];
                                if (newAccountId) {
                                    var showSupplierCode = typeof SHOW_SUPPLIER_CODE !== 'undefined' && SHOW_SUPPLIER_CODE;
                                    var supplierText = dataResult['new_company_name'] + (showSupplierCode ? ' (' + dataResult['new_company_code'] + ')' : '');
                                    var option = new Option(supplierText, newAccountId, true, true);
                                    $("#vendors").append(option).val(newAccountId).trigger('change.select2');
                                    get_vendors_detail(newAccountId);
                                   
                                }

                                 $("#loading_bg").hide();
                                $("#addcompany").modal('hide');

                            toastr.success("Supplier added successfully", "Success");
                                // $("#btn_add_company").css("display", "block");
                                //location.reload();
                                //$("#company_name").change();
                            }
                        }
                      },
                error: function(xhr, status, error) {
                    // Optional: show error message
                    alert("Failed To Add Supplier: " + error);
                },
                complete: function() {
                    // This runs always, after success or error
                    $("#loading_bg").hide();
                }
                });
            });


</script>

<?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

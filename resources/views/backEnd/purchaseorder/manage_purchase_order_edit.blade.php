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


{{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'purchase-order-update', 'method' => 'POST', 'id' => 'tender-create-form', 'novalidate'=>true]) }}
<input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
<input type="hidden" name="id" id="po_id" value="{{ isset($po) ? $po->id : '' }}">
<input type="hidden" name="net_vat" id="net_vat" value="{{ $net_vat }}">

<div class="tab-pane fade show active" id="purchase-order-1" role="tabpanel" aria-labelledby="purchase-order-1-tab">
    <div class="purchase-order-content-header d-flex align-items-center justify-content-between mb-1 gap-2">
        <h4 class="purchase-order-content-header-left">
            Edit - {{@$po->doc_number}}
        </h4>
        <div class="purchase-order-content-header-right d-flex align-items-center justify-content-end gap-2">
            <a class="btn btn-light text-dark" href="{{url('purchase-order/' . @$po->id . '?po_action=add')}}">
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
                                    <li><a class="dropdown-item d-flex align-items-center" href="{{url('purchase-order/'.@$po->id.'/print')}}">
                                            <i class="ico icon-outline-import text-success  title-15 me-2"></i>
                                            Download</a></li>
                                    {{-- <li><a class="dropdown-item" href="#">
                                            <i class="ico icon-outline-import text-success"></i>
                                            Import</a></li> --}}
                                    <li><a class="dropdown-item d-flex align-items-center" href="{{url('purchase-order/'.@$po->id.'/delete')}}"><i class="ico icon-outline-trash-bin-minimalistic text-danger  title-15 me-2"></i>
                                            Delete</a></li>

                                            
                    <li><button type="button" class="dropdown-item d-flex align-items-center" data-modal-size="modal-md" data-bs-target="#attachment_popup_win" data-bs-toggle="modal" class="btn btn-primary" onclick="view_attachment()"><i class="ico icon-outline-file text-warning  title-15 me-2"></i> Attachment</button></li>
                        <li><a class="dropdown-item d-flex align-items-center" href="{{url('purchase-order/'.@$po->id.'/delete')}}"><i class="ico icon-outline-letter  title-15 me-2"></i>
                                                Email</a></li>
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
                                <a href="{{ url('suppliers?supplier_action=add') }}" class="btn btn-sm p-0 ms-2" style="border:none;background:none;">
                                    <i class="ico icon-outline-add-square text-success" style="font-size:18px;"></i>
                                </a>
                            </label>
                    <select class=" js-account-select" name="vendors" id="vendors" required style="width: 100%;">

                        @foreach ($vendors as $value)
                            <option value="{{ @$value->id }}" {{ isset($po) ? (!empty($po->vendors) ? (@$po->vendors == @$value->id ? 'selected' : '') : '') : '' }}>
                                {{ @$value->account_name }} @if (@App\SysHelper::getCompanyCodeSettings()['is_supplier_code'])({{ @$value->account_code }})@endif
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
                    <label class="form-label">Currency<a style="float: right;" data-bs-target="#ModalChangeCurrancy" data-bs-toggle="modal"><i class="ico icon-outline-pen-2"></i></a></label>
                    
                    

                    <div class="form-group">
                        <select class="form-control select2 js-example-basic-single" name="currency" id="currency">
                            @foreach ($currency as $value)
                                
                                    <option value="{{ @$value->id }}" @if($po->currency == @$value->id) selected @endif>{{ @$value->code }}</option>
                               
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
                        <input type="text" class="form-control" value="{{ $po->createdby->full_name }}" name="createdby" id="createdby" readonly>
                       
                    </div>
                   
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
                            <input type="text" class="form-control date-picker" style="background-color: #deebe1;" id="delivery_date" name="delivery_date"
                                value="{{ @$value }}" required />
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Payment Terms*</label>
                        <div class="form-group">
                            <select class="form-control js-example-basic-single" required name="payment_terms" id="payment_terms"
                                onchange="fn_payment_terms()">
                                <option value="">Select</option>
                                @foreach ($paymentterms as $value)
                                    <option value="{{ @$value->id }}" {{ isset($po) ? (!empty(@$po->payment_terms) ? (@$po->payment_terms == @$value->id ? 'selected' : '') : '') : '' }}>
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
                                autocomplete="off" id="payment_terms2" value="{{ @$po->payment_terms2 }}">
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
                        <label class="form-label">Contact No</label>
                        <div class="form-group">
                            <input type="text" class="form-control" id="contact_person_telephone" type="text"
                                name="contact_person_telephone" value="{{ $po->contact_person_telephone }}" required />
                        </div>
                    </div>
                     
                    <div class="col-2">
                        @php
    $selectedCompanies = $po->ref_company_id
        ? explode(',', $po->ref_company_id)
        : [];
@endphp
                        <label class="form-label">Customer Reference</label>

                                       <input class="form-control" type="text" name="customer_reference_input"
                            autocomplete="off" id="customer_reference_input" readonly value="{{ implode(', ', array_map(function($id) use ($customer_reference_list) {
                                if ($id === 'SO') {
                                    return 'STOCK ORDER';
                                }
                                $company = collect($customer_reference_list)->firstWhere('id', $id);
                                return $company ? $company->name : '';
                            }, $selectedCompanies)) }}" placeholder="Click to select references" style="cursor: pointer;" />

                        <!-- Hidden container to hold actual selected IDs for form submission -->
                        <div id="ref_company_hidden_inputs" style="display:none;">
                            <!-- <input type="hidden" name="ref_company_id[]" value="SO" /> -->
                            @foreach ($selectedCompanies as $companyId)
                                @if($companyId != 'SO')
                                    <input type="hidden" name="ref_company_id[]" value="{{ $companyId }}" />
                                @endif
                            @endforeach
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
                                               <option value="SO" {{ in_array('SO', $selectedCompanies) ? 'selected' : '' }}>STOCK ORDER</option>
                            @foreach ($customer_reference_list as $value)
                            <option value="{{ @$value->id }}" {{ in_array($value->id, $selectedCompanies) ? 'selected' : '' }} >{{ @$value->name }} 
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

                                // Save selections back to visible input and hidden inputs (trim and sanitize values)
                                $('#save_customer_reference').on('click', function () {
                                    let selectedVals = $('#modal_ref_company_select').val() || [];
                                    // Trim and normalize display texts (collapse multiple spaces)
                                    let selectedTexts = $('#modal_ref_company_select').select2('data')
                                        .map(function(d) { return (d.text || '').replace(/\s+/g, ' ').trim(); })
                                        .filter(function(t) { return t.length > 0; });

                                    // Update visible text input to comma-separated names (no extra spaces)
                                    $('#customer_reference_input').val(selectedTexts.join(', '));

                                    // Update hidden inputs for form submission (trim values and skip empty)
                                    let $container = $('#ref_company_hidden_inputs');
                                    $container.empty();
                                    if (selectedVals && selectedVals.length > 0) {
                                        selectedVals.forEach(function(v) {
                                            var val = (v || '').toString().trim();
                                            if (val.length === 0) return; // skip empty
                                            // create one hidden input per selected value (escaped)
                                            $container.append('<input type="hidden" name="ref_company_id[]" value="' + $('<div>').text(val).html() + '" />');
                                        });
                                    }

                                    $('#customerReferenceModal').modal('hide');
                                });

                                // If modal closed without save, do nothing (retain previous selection)
                            });
                        </script>

                        <!-- <div class="form-group">
                             <select class="form-control js-example-basic-single" name="ref_company_id[]" id="ref_company_id" multiple>
                            <option value="">-Select-</option>
                            <option value="SO" {{ in_array('SO', $selectedCompanies) ? 'selected' : '' }}>STOCK ORDER</option>
                            @foreach ($customer_reference_list as $value)
                            <option value="{{ @$value->id }}" {{ in_array($value->id, $selectedCompanies) ? 'selected' : '' }} >{{ @$value->name }} 
                                @if(@App\SysHelper::getCompanyCodeSettings()['is_customer_code'])
                                        ({{ @$value->code }})
                                        @endif
                            </option>
                            @endforeach
                               
                        </select>
                            <input type="hidden" class="form-control" id="narration" type="text" name="narration" value="{{ $po->narration }}" required />
                        </div> -->
                    </div>
                    
                    <div class="col-2">
                        <label class="form-label">Sales Person Name</label>
                        <div class="form-group">
                            <select class="form-control js-example-basic-single" required name="sales_person"
                                id="sales_person">
                                <option value=""></option>
                                    @foreach ($salesman as $value)
                                        <option value="{{ @$value->user_id }}" @if($po->sales_person == $value->user_id) selected @endif>{{ @$value->full_name }}</option>
                                    @endforeach

                                    {{-- If the PO has a sales_person set but it's not in $salesman, try to fetch and append it so it can be selected --}}
                                    @if(isset($po) && $po->sales_person)
                                        @php
                                            $selectedId = $po->sales_person;
                                            $exists = collect($salesman)->contains(function($item) use ($selectedId) {
                                                return isset($item->user_id) && (string)$item->user_id === (string)$selectedId;
                                            });
                                        @endphp

                                        @if(!$exists)
                                            @php
                                                $staff = \App\SmStaff::where('user_id', $selectedId)->first();
                                                $fallbackUser = null;
                                                if(!$staff) {
                                                    $fallbackUser = \App\User::find($selectedId);
                                                }
                                            @endphp

                                            @if($staff)
                                                <option value="{{ $staff->user_id }}" selected>{{ $staff->full_name ?? trim($staff->first_name . ' ' . $staff->last_name) }}</option>
                                            @elseif($fallbackUser)
                                                <option value="{{ $fallbackUser->id }}" selected>{{ $fallbackUser->full_name ?? $fallbackUser->name ?? $fallbackUser->email }}</option>
                                            @endif
                                        @endif
                                    @elseif(isset($po) && !is_null($po->sales_person_name) && $po->sales_person_name !== '')
                                        {{-- If sales_person_name is set (manual entry), append it as a manual option --}}
                                        <option value="{{ $po->sales_person_name }}" selected>{{ $po->sales_person_name }}</option>
                                    
                                    @endif
                                <option value="OTH">Other</option>

                            </select>
                        </div>
                    </div>
                      {{-- Other Sales Person Modal (select a user when "Other" is chosen) --}}
                    <?php // NOTE: For production it's better to pass $allUsers from the controller rather than querying in the view. ?>
                   

                    
                    <div class="modal fade" id="otherSalesPersonModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="false">
                        <div class="modal-dialog modal-sm draggable" style="top:10rem;left:10rem">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Select Other Sales Person</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <label class="form-label">Sales Person</label>
                    

                <input
                    type="text"
                    id="other_sales_person_input"
                    class="form-control"
                    placeholder="">

                
                                </div>
                                <div class="modal-footer">
                                 
                                    <button type="button" id="save_other_sales_person" class="btn btn-light"><i class="ico icon-outline-bookmark-opened text-success"></i> Save</button>
                                </div>
                            </div>
                        </div>
                    </div>

                      <script>
                        $(document).ready(function() {
                           

                                    // Store previous value on focus so we can restore on cancel
                            $('#sales_person').on('focus mousedown', function() {
                                $(this).data('prev', $(this).val());
                            });

                            // When "Other" is selected open the modal (preserve previous value)
                            $('#sales_person').on('change', function() {
                                var $this = $(this);
                                if ($this.val() === 'OTH') {
                                    // Keep previous value stored
                                    if (!$this.data('prev')) $this.data('prev', '');
                                    // reset modal inputs
                                    // $('#other_sales_person_select').val(null).trigger('change');
                                    $('#other_sales_person_input').val('');
                                    $('#otherSalesPersonModal').modal('show');
                                } else {
                                    // user selected a real entry; remove any previous manual-name hidden input
                                    $('input[name="sales_person_name"]').remove();
                                    // clear stored prev if a normal selection was made
                                    $this.removeData('prev');
                                }
                            });

                            // Save selected user or manual input and append to sales_person list
                            $('#save_other_sales_person').on('click', function() {
                                // var selectedUid = $('#other_sales_person_select').val();
                                // var selectedText = $('#other_sales_person_select option:selected').text().trim();
                                var manualName = $('#other_sales_person_input').val().trim();

                                if (!manualName) {
                                    alert('Please select a user or enter a name');
                                    return;
                                }

                                var $sales = $('#sales_person');

                                if (manualName) {
                                    // create unique value for manual option
                                    var val = manualName;
                                    var text = manualName;

                                    // If an identical manual option already exists (match by data-name), reuse it
                                    var existing = $sales.find('option[data-manual][data-name="' + manualName.replace(/"/g,'&quot;') + '"]');
                                    if (existing.length) {
                                        $sales.val(existing.val()).trigger('change');
                                    } else {
                                        var $newOpt = $('<option>').val(val).text(text).attr({'data-manual':'1','data-name':manualName});
                                        $sales.append($newOpt);
                                        $sales.val(val).trigger('change');
                                    }

                                    // Add or update hidden input so server receives manual name
                                    var $hidden = $('input[name="sales_person_name"]');
                                    if ($hidden.length) {
                                        $hidden.val(manualName);
                                    } else {
                                        $('<input>').attr({type:'hidden', name:'sales_person_name', value: manualName}).appendTo('form#tender-create-form');
                                    }

                                } 

                                // update stored prev to the newly selected value
                                $sales.data('prev', $sales.val());

                                // Close modal
                                $('#otherSalesPersonModal').modal('hide');
                            });

                            // If modal is closed without saving, restore previous selection
                            $('#otherSalesPersonModal').on('hidden.bs.modal', function () {
                                var $sales = $('#sales_person');
                                if ($sales.val() === 'OTH') {
                                    var prev = $sales.data('prev') || '';
                                    if (prev) {
                                        $sales.val(prev).trigger('change');
                                    } else {
                                        $sales.val('').trigger('change');
                                    }
                                }
                            });
                        });
                    </script>
                    <div class="col-2">
                        <label class="form-label">Internal Transfer</label>
                        <div class="form-group">
                            <select class="form-control select2 js-example-basic-single" id="internal_transfer" name="internal_transfer"
                                required>
                                <option value="">Select</option>
                                <option value="1" @if($po->internal_transfer == 1) selected @endif>Yes</option>
                                <option value="2" @if($po->internal_transfer == 2) selected @endif>No</option>
                            </select>
                            
                        </div>
                    </div>

                    
                    <div id="property-value-div" style="display: none;">
                        <input type="hidden" name="property_value" id="property_value" value="{{ isset($po) && $po->property_value ? $po->property_value : '' }}">
                        <input type="hidden" name="property_name" id="property_name" value="{{ isset($po) && $po->property_name ? $po->property_name : '' }}">
                    </div>
                
                 
                    <div class="col-6 mb-2">
                        <div class="input-effect">
                        <label class="form-label mb-0 d-flex justify-content-between align-items-center">
                                <span>Narration</span>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#ExtraNoteModal"  class="btn btn-sm p-0 ms-2" style="border:none;background:none;">
                                    <i class="ico icon-outline-add-square text-success" style="font-size:18px;"></i> Extra Note
                                </a>
                            </label>
                            <input class="form-control" data-bs-toggle="modal" data-bs-target="#narrationModal" value="{{$po->reference}}" id="reference" type="text" name="reference">
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
                                                >{{ @$value->account_name }}   @if(@App\SysHelper::getCompanyCodeSettings()['is_customer_code'])
                                        ({{ @$value->account_code }})
                                        @endif</option>
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

                     <div class="col">
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
                                    value="{{ $value->id }}" @if (isset($po)) @if (@$po->supplier_state == $value->id) selected @endif @endif>
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
                          
                           <input class="form-control" type="number"  name="vat_percent" id="vat_percent" value="{{ @$po->vat_percent }}">
                        </div>
                    </div>

                    <div class="col">
                        <label class="form-label">VAT Number</label>
                        <div class="form-group">
                          
                           <input class="form-control" type="number"  name="vat_number" id="vat_number" value="{{ @$po->vat_number }}">
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
                                                {{ isset($po) ? (!empty(@$po->supplier_type) ? (@$po->supplier_type == @$value->id ? 'selected' : '') : '') : '' }}>
                                                {{ @$value->title }}</option>
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
                                                {{ isset($po) ? (!empty(@$po->supplier_type) ? (@$po->supplier_type == @$value->id ? 'selected' : '') : '') : '' }}>
                                                {{ @$value->title }}</option>
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
        <td><input type="text" class="form-control text-center" name="sort_id[]" value="{{ $i }}" />
            <input type="hidden" name="product_type[]" value="{{ $items->product_type }}" />
            <input type="hidden" name="item_po_id[]" value="{{ $items->po_id }}" />
        </td>
        <td>
             <select class="form-control noborder " name="part_number[]">
                <option value="{{ $items->part_number }}">
                {{ $items->partno ?? 0 }}</option>
            </select>
        </td>
        <td><input class="form-control" name="description[]" value="{{ $items->description ?? 0 }}"></td>
        


        
        <td><input type="text" class="form-control text-center" name="tax[]" value="{{ number_format($items->tax, 0) }}" onchange="calc_change_new(this)"/></td>
        <td><input type="text" class="form-control text-center" name="qty[]" value="{{ $items->qty }}"  onkeypress="set_license_key_po({{ $i }})" onchange="calc_change_new(this)"/></td>
        
        <td><input type="text" class="form-control text-end" step="Any" id="unitprice_{{ $i }}" name="unitprice[]" value="{{ @App\SysHelper::com_curr_format($items->unitprice,2,'.',',') }}" onchange="calc_change_new(this)" onblur="formatCurrency(this)"/></td>
        <td><input type="text" class="form-control text-end" name="value[]" readonly value="{{ @App\SysHelper::com_curr_format($items->value,2,'.',',') }}" onchange="calc_change_new(this)"/></td>
        <td><input type="text" class="form-control text-end" name="discount[]" value="{{ @App\SysHelper::com_curr_format($items->discount,2,'.',',') }}" onchange="calc_change_new(this)" onblur="formatCurrency(this)"/></td>
        <td><input type="text" class="form-control text-end" name="fright[]" value="{{ @App\SysHelper::com_curr_format($items->fright,2,'.',',') }}" onchange="calc_change_new(this)" onblur="formatCurrency(this)"/></td>
        <td><input type="text" class="form-control text-end" name="customcharges[]" value="{{ @App\SysHelper::com_curr_format($items->customcharges,2,'.',',') }}" onchange="calc_change_new(this)" onblur="formatCurrency(this)"/></td>
        
        <td><input type="text" class="form-control text-end" name="taxableamount[]" value="{{ @App\SysHelper::com_curr_format($items->taxableamount,2,'.',',') }}" readonly/></td>
        <td><input type="text" class="form-control text-end" name="vatamount[]" value="{{ @App\SysHelper::com_curr_format($items->vatamount,2,'.',',') }}" readonly/></td>
        <td><input type="text" class="form-control text-end" name="totalamount[]" value="{{ @App\SysHelper::com_curr_format($items->taxableamount+$items->vatamount, 2, '.', ',') }}" readonly/></td>
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
                        <input class="form-control" name="description[]">
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
                    <td><input class="form-control text-end" type="text" name="unitprice[]" step="any" autocomplete="off"
                            min="0" onchange="calc_change_new(this)" onblur="formatCurrency(this)"></td>
                    <td><input class="form-control text-end" type="text" name="value[]" autocomplete="off" min="0" readonly>
                    </td>
                    <td><input class="form-control text-end" type="text" name="discount[]" autocomplete="off" step="0.01"
                            min="0" onchange="calc_change_new(this)" onblur="formatCurrency(this)"></td>
                    <td><input class="form-control text-end" type="text" name="fright[]" autocomplete="off" step="0.01" min="0"
                            onchange="calc_change_new(this)" onblur="formatCurrency(this)"></td>
                    <td><input class="form-control text-end" type="text" name="customcharges[]" autocomplete="off" step="0.01"
                            min="0" onchange="calc_change_new(this)" onblur="formatCurrency(this)"></td>
                    <td><input class="form-control text-end" type="text" name="taxableamount[]" autocomplete="off" step="0.01"
                            min="0" readonly></td>
                    <td><input class="form-control text-end" type="text" name="vatamount[]" autocomplete="off" min="0"
                            readonly></td>
                    <td><input class="form-control text-end" type="text" name="totalamount[]" autocomplete="off" min="0"
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
                    @if($po->bill_number)
                    <th class="text-end" scope="col"></th>
                    @endif
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
    <div class="modal-dialog modal-lg draggable" >
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
            <input type="text" class="form-control" id="extraProperty" placeholder="Enter property" value="{{ isset($po) && $po->property_name ? $po->property_name : '' }}">
        </div>

        <div class="flex-fill">
            <label for="extraValue" class="form-label">Value</label>
            <input type="text" class="form-control" id="extraValue" placeholder="Enter value" value="{{ isset($po) && $po->property_value ? $po->property_value : '' }}">
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
    document.addEventListener('DOMContentLoaded', function () {
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
        if('input[name="part_number[]"]' != ""){
            currentDescriptionInput = $(this);
            $('#add_description').val(currentDescriptionInput.val());
            descriptionModal.show();
        }
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
    var unitprice = $row.find('input[name="unitprice[]"]').val().replace(/,/g, '') || '0';
    var discount = $row.find('input[name="discount[]"]').val().replace(/,/g, '') || '0';
    var fright = $row.find('input[name="fright[]"]').val().replace(/,/g, '') || '0';
    var customcharges = $row.find('input[name="customcharges[]"]').val().replace(/,/g, '') || '0';

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
        total_fright = 0,
        total_customcharges = 0,
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
        total_fright += parseFloat($row.find('input[name="fright[]"]').val().replace(/,/g, '')) || 0;
        total_customcharges += parseFloat($row.find('input[name="customcharges[]"]').val().replace(/,/g, '')) || 0;
        total_taxableamount += parseFloat($row.find('input[name="taxableamount[]"]').val().replace(/,/g, '')) || 0;
        total_vatamount += parseFloat($row.find('input[name="vatamount[]"]').val().replace(/,/g, '')) || 0;
        total_totalamount += parseFloat($row.find('input[name="totalamount[]"]').val().replace(/,/g, '')) || 0;
    });

    $('#lbl_total_qty').text(total_qty);
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
            $row.find('input[name="description[]"]').val(selectedData.description || '');
            $row.find('input[name="part_number_txt[]"]').val(selectedData.text || '');
            $row.find('input[name="hscode_txt[]"]').val(selectedData.hscode || '');
            $row.find('input[name="product_type[]"]').val(selectedData.product_type || '');
            $row.find('input[name="product_type_part_number_text[]"]').val(selectedData.description || '');
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
                    var state = null;

                    if (dataResult['data'] != null) {
                        len = dataResult['data'].length;
                    }
                    if (len > 0) {
                        for (var i = 0; i < len; i++) {
                            $("#payment_terms").val(dataResult['data'][i].payment_terms);
                            $("#contact_person_name").val(dataResult['data'][i].contact_person);
                            $("#contact_person_email").val(dataResult['data'][i].email);
                            //$("#shipping_address_2").val(dataResult['data'][i].address2);
                             const numbers = [
    dataResult?.data?.[i]?.contcat_number,
    dataResult?.data?.[i]?.mobile
]
.map(n => n?.trim())
.filter(Boolean);

$("#contact_person_telephone").val(numbers.join(' / '));
                            // $("#contact_person_telephone").val(dataResult['data'][i].contcat_number);

                            $("#supplier_type").val(dataResult['data'][i].supplier_type);
                            $("#purchase_type").val(dataResult['data'][i].purchase_type);

                            //$("select[id=tax] option:first").text(dataResult['data'][i].vat_percentage +'%');
                            //$("select[id=tax] option:first").val(dataResult['data'][i].vat_percentage);
                            $("#tax").val(dataResult['data'][i].vat_percentage);

                            $("#country").val(dataResult['data'][i].vat_country).trigger('change');;
                            window.SELECTED_STATE_ID = dataResult['data'][i].vat_state;

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
                            $("#shipping_address_1").val(dataResult['data'][i].shipping_address);
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


{{-- attachment start--}}

        <div class="modal side-panel fade" id="attachment_popup_win" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="width:45rem">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title w-100" style="padding-left:10px">Attachments - <span class="font-weight-500" id="att_cust_name"></span></h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            <div class="modal-body m-0 p-0 mb-1">
                <div class="container-fluid " style="padding:0.2rem 1rem">
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
                                <input class="form-control date-picker" type="text" id="att_date" name="att_date" value="{{ date('d/m/Y') }}"/>
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
                    
                <div class="row mt-2">
                    <div class="col-md-12">
                        <table id="att-table" class="table table-hover" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th >No</th>
                                    <th >Date</th>
                                    <th >Attachment</th>
                                    <th >Action</th>
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
                                    <input type="hidden" id="srl_id" />
						<button type="button" class="btn btn-light add-btn ms-2" onclick="add_attachment()">
							<i class="ico icon-outline-bookmark-opened text-success"></i> Save
						</button>
                </div>
        </div>
    </div>
</div>
<script>
    function add_attachment(){
        $("#loading_bg").css("display", "block");

        if($('#att_file').val()==""){ $('#att_file').focus(); $("#loading_bg").css("display", "none"); return false; }

        var action = "{{ URL::to('add-purchase-order-attachment') }}";
        
        var formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');  // Append CSRF token
        formData.append('doc_id', $('#po_id').val());
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
                                <td><a onclick='delete_attachment("+dataResult['data'][i].id+")' class='btn-sm btn-light'><i class='ico icon-outline-trash-bin-minimalistic title-15 text-dark'></i></a></td>\
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

        var action = "{{ URL::to('view-purchase-order-attachment') }}";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                doc_id : $('#po_id').val(),
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
                                <td><a onclick='delete_attachment("+dataResult['data'][i].id+")' class='btn-sm btn-light'><i class='ico icon-outline-trash-bin-minimalistic title-15 text-dark'></i></a></td>\
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
        var action = "{{ URL::to('delete-purchase-order-attachment') }}";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                id : id,
                doc_id : $('#po_id').val(),
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
                                <td><a onclick='delete_attachment("+dataResult['data'][i].id+")' class='btn-sm btn-light'><i class='ico icon-outline-trash-bin-minimalistic title-15 text-dark'></i></a></td>\
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

    <!-- Modal Change Currancy-->
        <div class="modal side-panel fade" id="ModalChangeCurrancy" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">Change Currency</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'purchase-order-update-currency', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Currency From</label>
                                <select class="form-control js-example-basic-single" name="from_currency_id" required>
                                    @foreach ($currency as $value)
                                        @if($po->currency == $value->id)
                                            <option value="{{ @$value->id }}" >{{ @$value->code }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Currency To</label>
                                <select class="form-control js-example-basic-single" name="to_currency_id" id="to_currency_id" required onchange="set_rate()">
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
                        <div class="col-md-12">
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
                    <input type="hidden" name="cur_po_id" value="{{ @$po->id }}"/>
                    <input type="hidden" name="cur_po_doc_no" value="{{ @$po->doc_number }}"/>          
						<button type="submit" class="btn btn-light add-btn ms-2"><i class="ico icon-outline-bookmark-opened text-success"></i> Change
						</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!-- Modal Change Currancy-->

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

<?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
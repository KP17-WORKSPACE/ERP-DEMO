    <?php try { ?>

    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'purchase-invoice-store', 'method' => 'POST', 'id' => 'purchase-invoice-create-form', 'novalidate' => true]) }}
    {{-- @endif --}}
    <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
    <input type="hidden" name="id" value="{{ isset($edit) ? $edit->id : '' }}">
    <input type="hidden" name="net_vat" id="net_vat" value="0">




    <div class="purchase-order-content-header">
        <h4 class="purchase-order-content-header-left">
            New
            ({{ isset($edit) ? (!empty(@$edit->title) ? @$edit->title : old('doc_number')) : @App\SysHelper::get_new_code('sys_purchase_invoice', 'PI', 'doc_number') }})
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
                    <li><button class="dropdown-item" type="button" onclick="get_adjustment_list()"><i
                                class="ico icon-outline-calculator-minimalistic text-warning"></i> Adjustment</button>
                    </li>
                    <li><button type="button" class="dropdown-item" data-modal-size="modal-md"
                            data-bs-target="#attachment_popup_win" data-bs-toggle="modal" class="btn btn-primary"
                            onclick="view_attachment()"><i
                                class="ico icon-outline-calculator-minimalistic text-warning"></i> Attachment</button>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div class="row gap-rows">
                <div class="col-4">
                    <label class="form-label">Vendor</label>
                    <div class="form-group">
                        <select class="form-control js-account-select" name="vendors" id="vendors"
                            onchange="get_pending_grn_list()">
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
                    <label class="form-label">PIV Number</label>
                    <div class="form-group">
                        <input class="form-control" type="text" name="doc_number" autocomplete="off" id="doc_number"
                            value="{{ isset($edit) ? (!empty(@$edit->title) ? @$edit->title : old('doc_number')) : @App\SysHelper::get_new_code('sys_purchase_invoice', 'PI', 'doc_number') }}"
                            readonly>
                    </div>
                </div>
                <div class="col-2">
                    <label class="form-label">PI Date</label>
                    <div class="form-group">
                        @php $value_date = \Carbon\Carbon::parse( now())->format('d/m/Y'); @endphp
                        <input class="form-control date-picker" id="pi_date" type="text" autocomplete="off"
                            name="pi_date" value="{{ @$value_date }}" style="margin-top: 0px">
                    </div>
                </div>
                <div class="col-2">
                    <label class="form-label">Currency</label>
                    <div class="form-group"><select class="form-control js-example-basic-single" name="currency"
                            id="currency">
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
                <button class="nav-link active" id="extra-fields-tab" data-bs-toggle="tab"
                    data-bs-target="#extra-fields" type="button" role="tab" aria-controls="extra-fields"
                    aria-selected="true">Extra Fields</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="shipping-details-tab" data-bs-toggle="tab"
                    data-bs-target="#shipping-details" type="button" role="tab"
                    aria-controls="shipping-details" aria-selected="true">Shipping Details</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="vat-details-tab" data-bs-toggle="tab" data-bs-target="#vat-details"
                    type="button" role="tab" aria-controls="vat-details" aria-selected="true">VAT
                    Details</button>
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
                            <a data-modal-size="modal-md" data-target="#grn_pending_popup_win" id="addGRNPending"
                                data-toggle="modal"></a>
                            <input type="hidden" id="grn_id" name="grn_id">
                            <input type="hidden" id="po_id" name="po_id">
                            <input type="hidden" id="vat_percentage" name="vat_percentage" value="5">
                            <input type="hidden" id="hd_pending_grn_id" />
                            <input type="hidden" id="hd_pending_po_id" />
                        </div>
                    </div>
                    <div class="col-10 mb-2">
                        <div class="row gap-rows">

                        <div class="col-2">
                                <label class="form-label">GRN No</label>
                                <div class="form-group">
                                    <input class="form-control" type="text" name="grn_no" autocomplete="off"
                                        id="grn_no"
                                        value="{{ isset($edit) ? (!empty(@$edit->grn_no) ? @$edit->grn_no : old('grn_no')) : '' }}">
                                </div>
                            </div>

                            @php
                                $value_date = old('grn_date') ?? ($edit->grn_date ?? now());
                                $value_date = \Carbon\Carbon::parse($value_date)->format('d/m/Y');
                            @endphp
                            <div class="col-2">
                                <label class="form-label">GRN Date</label>
                                <div class="form-group">
                                    <input class="form-control date-picker" type="text" name="grn_date"
                                        autocomplete="off" id="grn_date" required value="{{ $value_date }}">
                                </div>
                            </div>



                            <div class="col-2">
                                <label class="form-label">Bill Number</label>
                                <div class="form-group">
                                    <input class="form-control" required type="text" name="bill_number"
                                        autocomplete="off" id="bill_number"
                                        value="{{ isset($edit) ? (!empty(@$edit->bill_number) ? @$edit->bill_number : old('bill_number')) : '' }}"
                                        onchange="updateNarration()">
                                </div>
                            </div>
                            <div class="col-2">
                                <label class="form-label">Bill Date</label>
                                <div class="form-group">
                                    @php
                                        $value_date = old('bill_date') ?? ($edit->bill_date ?? now());
                                        $value_date = \Carbon\Carbon::parse($value_date)->format('d/m/Y');
                                    @endphp
                                    <input class="form-control date-picker" id="bill_date" type="text"
                                        autocomplete="off" name="bill_date" value="{{ @$value_date }}" required>
                                </div>
                            </div>

                            <div class="col-2">
                                <label class="form-label">Payment Terms</label>
                                <div class="form-group">
                                    <select class="form-control js-example-basic-single" name="payment_terms"
                                        id="payment_terms">
                                        <option value=""></option>
                                        @foreach ($paymentterms as $value)
                                            <option value="{{ @$value->id }}"
                                                {{ isset($edit) ? (!empty(@$edit->payment_terms) ? (@$edit->payment_terms == @$value->id ? 'selected' : '') : '') : '' }}>
                                                {{ @$value->title }}</option>
                                        @endforeach
                                    </select>


                                </div>
                                <div id="div_payment_terms" style="display: none; padding-top: px;">
                                    <div class="input-effect">
                                        <label class="txtlbl">@lang('Other Payment Terms')<span>*</span></label>
                                        <input
                                            class="txtbx primary-input form-control {{ $errors->has('payment_terms2') ? ' is-invalid' : '' }}"
                                            type="text" name="payment_terms2" autocomplete="off"
                                            id="payment_terms2"
                                            value="{{ isset($edit) ? (!empty(@$edit->payment_terms2) ? @$edit->payment_terms2 : old('payment_terms2')) : '' }}">
                                    </div>
                                </div>
                            </div>

                            <div class="col-2">
                                <label class="form-label">Deal ID</label>
                                <div class="form-group">
                                    <input class="form-control" type="text" name="deal_id" autocomplete="off"
                                        id="deal_id"
                                        value="{{ isset($edit) ? (!empty(@$edit->deal_id) ? @$edit->deal_id : old('deal_id')) : '' }}">
                                </div>
                            </div>

                            <div class="col-2">
                                <label class="form-label">Customer Reference</label>


                                
                                  <!-- Visible text input (opens modal for multi-select) -->
                        <input class="form-control" type="text" name="customer_reference_input"
                            autocomplete="off" id="customer_reference_input" readonly value="">

                        <!-- Hidden container to hold actual selected IDs for form submission -->
                        <div id="ref_company_hidden_inputs" style="display:none;">
                          
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
                                      
                                            @foreach ($customer_reference_list as $value)
                                                <option value="{{ @$value->id }}" @if(@$deal->cust_id == @$value->id) selected @endif>{{ @$value->name }} @if(@App\SysHelper::getCompanyCodeSettings()['is_customer_code']) ({{ @$value->code }}) @endif</option>
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
                                    <select class="form-control js-example-basic-single" name="ref_company_id"
                                        id="ref_company_id" required>
                                        <option value="">-Select-</option>
                                        @foreach ($customer_reference_list as $value)
                                            <option value="{{ @$value->id }}"
                                                @if (@$deal->cust_id == @$value->id) selected @endif>{{ @$value->name }}
                                                @if (@App\SysHelper::getCompanyCodeSettings()['is_customer_code'])
                                                    ({{ @$value->code }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>

                                    <input class="form-control" type="hidden" name="reference" autocomplete="off"
                                        value="{{ isset($edit) ? (!empty(@$edit->reference) ? @$edit->reference : old('reference')) : old('reference') }}"
                                        id="reference">
                                </div> -->
                            </div>
                            <div class="col-2">
                                <label class="form-label">Sales Person</label>
                                <div class="form-group">
                                    <select class="form-control js-example-basic-single" required name="sales_person"
                                        id="sales_person">
                                        <option value=""></option>
                                        @foreach ($salesman as $value)
                                            <option value="{{ @$value->user_id }}">{{ @$value->full_name }}</option>
                                        @endforeach
                                         <option value="OTH">Other</option>
                            
                                    </select>
                                </div>
                            </div>

                         
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
                                <label class="form-label">LPO Number</label>
                                <div class="form-group">
                                    <input
                                        class="txtbx primary-input form-control {{ $errors->has('lpo_number') ? ' is-invalid' : '' }}"
                                        type="text" name="lpo_number" autocomplete="off" id="lpo_number"
                                        value="{{ isset($edit) ? (!empty(@$edit->lpo_number) ? @$edit->lpo_number : old('lpo_number')) : '' }}">
                                </div>
                            </div>
                            <div class="col-2">
                                <label class="form-label">LPO Date</label>
                                <div class="form-group">
                                    <input class="form-control date-picker" id="lpo_date" type="text"
                                        autocomplete="off" name="lpo_date" value="{{ @$value_date }}"
                                        style="margin-top:0px;">

                                </div>
                            </div>

                            <div class="col-2">
                                <label class="form-label">Warehouse</label>
                                <div class="form-group">
                                    <!-- <input class="form-control" type="text" name="warehouse" autocomplete="off"
                                        value="{{ isset($edit) ? (!empty(@$edit->warehouse) ? @$edit->warehouse : old('warehouse')) : old('warehouse') }}"
                                        id="warehouse"> -->

                                          @php
                                        $warehouses = App\SysHelper::getCompanyWarehouses();
                                        @endphp

                                         <select class="form-control js-example-basic-single" required name="warehouse" id="warehouse">
                                       
                                        @foreach ($warehouses as $value)
                                            <option value="{{ @$value->id }}">{{ @$value->warehouse_name }}</option>
                                        @endforeach
                                        
                                    </select>
                                </div>
                            </div>

                            <div class="col-2">
                                <label class="form-label">BOE No</label>
                                <div class="form-group">
                                    <input
                                        class="txtbx primary-input form-control {{ $errors->has('boeno') ? ' is-invalid' : '' }}"
                                        type="text" name="boeno" autocomplete="off"
                                        value="{{ isset($edit) ? (!empty(@$edit->boeno) ? @$edit->boeno : old('boeno')) : old('boeno') }}"
                                        id="boeno">
                                </div>
                            </div>

                            <div class="col-2">
                                <label class="form-label">AWB No</label>
                                <div class="form-group">
                                    <input
                                        class="txtbx primary-input form-control {{ $errors->has('awbno') ? ' is-invalid' : '' }}"
                                        type="text" name="awbno" autocomplete="off"
                                        value="{{ isset($edit) ? (!empty(@$edit->awbno) ? @$edit->awbno : old('awbno')) : old('awbno') }}"
                                        id="awbno">
                                </div>
                            </div>



                            

                            <div class="col">
                                <label class="form-label">Remarks</label>
                                <div class="form-group">
                                    <input class="form-control" data-bs-toggle="modal"
                                        data-bs-target="#narrationModal" type="text" name="narration"
                                        autocomplete="off" id="narration"
                                        value="{{ isset($edit) ? (!empty(@$edit->narration) ? @$edit->narration : old('narration')) : '' }}">
                                </div>
                            </div>


                        </div>
                    </div>



                </div>
            </div>
            <div class="tab-pane fade show" id="shipping-details" role="tabpanel"
                aria-labelledby="shipping-details-tab">
                {{-- <div class="row gap-rows">
                                            <div class="col-2">
                                                <label class="form-label">Shipping Name</label>
                                                <div class="form-group">
                                        <input type="text" class="form-control" cols="0"
                                        rows="4" name="shipping_name"
                                        id="shipping_name">
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <label class="form-label">Shipping Address 1</label>
                                                <div class="form-group">
                                        <input type="text" class="form-control" cols="0"
                                            rows="4" name="shipping_address_1"
                                            id="shipping_address_1">
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <label class="form-label">Ship to Address 2</label>
                                                <div class="form-group">
                                        <input type="text" class="form-control" cols="0"
                                            rows="4" name="shipping_address_2"
                                            id="shipping_address_2">
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <label class="form-label">Contact No</label>
                                                <div class="form-group">
                                        <input type="text" class="form-control" cols="0"
                                            rows="4" name="shipping_contact_no"
                                            id="shipping_contact_no">
                                                </div>
                                            </div>
                                        </div> --}}
                <div class="row gap-rows">

                    <div class="col-3">
                        <label class="form-label">Company (Ship To)</label>
                        <div class="form-group">
                            <select class="form-control js-example-basic-single" name="shipping_supplier"
                                id="shipping_supplier" required style="width: 100%;">
                                <option value=""></option>
                                @foreach ($customer as $value)
                                    @php $s = @App\SysHelper::internal_transfer_customer_id(@$value->id, session('logged_session_data.company_id')); @endphp

                                    <option value="{{ @$value->id }}" {{ $s }}>
                                        {{ @$value->account_name }}
                                        @if (@App\SysHelper::getCompanyCodeSettings()['is_customer_code'])
                                            ({{ @$value->account_code }})
                                        @endif

                                    </option>
                                @endforeach
                            </select>




                        </div>
                        <script>
                            $(function() {
                                $("#shipping_supplier").change();
                            });
                        </script>
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
            <div class="tab-pane fade show" id="vat-details" role="tabpanel" aria-labelledby="vat-details-tab">
                <div class="row gap-rows">

                    <div class="col-2">
                        <label class="form-label">Supplier Country</label>
                        <div class="form-group">
                            <select class="form-control js-example-basic-single" name="supplier_country"
                                id="country" required>

                                @foreach ($countries as $key => $value)
                                    <option value="{{ @$value->id }}" <?php try{?>
                                        @if (isset($edit)) @if (@$edit->supplier_country == $value->id) selected @endif
                                        @endif
                                        <?php } catch (\Throwable $th) {} ?>
                                        >{{ @$value->name }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Supplier State</label>
                        <div class="form-group">
                            <div id="sectionStateDiv">
                                <select class="form-control js-example-basic-single" name="supplier_state"
                                    id="state">
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

                    <div class="col">
                        <label class="form-label">VAT %</label>
                        <div class="form-group">

                            <input class="form-control" type="number" name="vat_percent" id="vat_percent"
                                value="{{ @$editData->vat_percent }}">
                        </div>
                    </div>


                    <div class="col">
                        <label class="form-label">VAT Number</label>
                        <div class="form-group">

                            <input class="form-control" type="number" name="vat_number" id="vat_number"
                                value="{{ @$editData->vat_number }}">
                        </div>
                    </div>

                    <div class="col-2">
                        <label class="form-label">Supplier Type</label>
                        <div class="form-group">
                            <select
                                class="dynamicstxt niceSelect w-100 bb form-control js-example-basic-single {{ $errors->has('supplier_type') ? ' is-invalid' : '' }}"
                                name="supplier_type" id="supplier_type">
                                <option value="0"></option>
                                @foreach ($suppliertype as $value)
                                    <option value="{{ @$value->id }}"
                                        {{ isset($edit) ? (!empty(@$edit->supplier_type) ? (@$edit->supplier_type == @$value->id ? 'selected' : '') : '') : '' }}>
                                        {{ @$value->title }}</option>
                                @endforeach
                            </select>


                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Purchase Type</label>
                        <div class="form-group">
                            <select
                                class="dynamicstxt niceSelect w-100 bb form-control js-example-basic-single {{ $errors->has('purchase_type') ? ' is-invalid' : '' }}"
                                name="purchase_type" id="purchase_type">
                                <option value="0"></option>
                                @foreach ($purchasetype as $value)
                                    <option value="{{ @$value->id }}"
                                        {{ isset($edit) ? (!empty(@$edit->supplier_type) ? (@$edit->supplier_type == @$value->id ? 'selected' : '') : '') : '' }}>
                                        {{ @$value->title }}</option>
                                @endforeach
                            </select>


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
                    <th class="resizable text-center" width="50px">@lang('No')<div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="150px">@lang('Part No') <a
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
                    <th class="resizable text-center" width="80px">@lang('Taxable')<div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="100px">@lang('VAT')<div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="100px">@lang('Total')<div class="resizer"></div>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="text" class="form-control text-center" name="sort_id[]" value="1" />
                    </td>
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
                    <td><input class="form-control" type="number text-center" name="qty[]" autocomplete="off"
                            min="0" onchange="calc_change_new(this)" onkeypress="set_license_key()"></td>
                    <td><input class="form-control" type="text" name="unitprice[]" step="any"
                            autocomplete="off" min="0" onchange="calc_change_new(this)"
                            onblur="formatCurrency(this)"></td>
                    <td><input class="form-control" type="text" name="value[]" autocomplete="off" min="0"
                            readonly></td>
                    <td><input class="form-control" type="text" name="discount[]" autocomplete="off"
                            min="0" onchange="calc_change_new(this)" onblur="formatCurrency(this)"></td>
                    <td><input class="form-control" type="text" name="fright[]" autocomplete="off"
                            min="0" onchange="calc_change_new(this)" onblur="formatCurrency(this)"></td>
                    <td><input class="form-control" type="text" name="customcharges[]" autocomplete="off"
                            min="0" onchange="calc_change_new(this)" onblur="formatCurrency(this)"></td>
                    <td><input class="form-control" type="text" name="taxableamount[]" autocomplete="off"
                            min="0" readonly></td>
                    <td><input class="form-control" type="text" name="vatamount[]" autocomplete="off"
                            min="0" readonly></td>
                    <td><input class="form-control" type="text" name="totalamount[]" autocomplete="off"
                            min="0" readonly></td>
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
                </tr>
            </tfoot>
        </table>
        <div id="contextMenu">
            <button type="button" id="addRow">Add Row</button>
            <button type="button" id="deleteRow">Delete Row</button>
        </div>
    </div>
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
        <div class="modal-dialog modal-md draggable" style="height: 279px !important;">
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

    <button id="btnModalAdjustmentNew" data-bs-toggle="modal" data-bs-target="#adjustmentModalNew" hidden></button>
    <div class="modal side-panel fade" id="adjustmentModalNew" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="adjustmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="adjustmentModalLabel">Unadjusted List</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="adjustmentForm" method="POST">
                    @csrf
                    <div class="modal-body m-0 p-0">
                        <div class="card mb-0 mt-0">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 mb-2">\
                                        <table class="table table-hover form-item-table" id="adjustment_table">
                                            <thead>
                                                <tr>
                                                    <th>Doc Date</th>
                                                    <th>PIV No</th>
                                                    <th class="text-end">Total</th>
                                                    <th class="text-end">Paid</th>
                                                    <th class="text-end">Balance</th>
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
                            <i class="ico icon-outline-bookmark-opened text-success"></i> Adjust
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal side-panel fade" id="narrationModal" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg draggable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="poexcelimport">Enter Remarks</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body">
                            <textarea style="height: 109px !important;" class="form-control" id="narrationTextarea" rows="6"
                                placeholder="Write remarks here..."></textarea>
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
    {{-- Models  --}}

    <style>
        /* Fixed height modal */
        #po_pending_popup_win .modal-dialog {
            /* adjust if needed */
            display: flex;
            flex-direction: column;
        }

        #po_pending_popup_win .modal-content {
            display: flex;
            flex-direction: column;
        }

        /* Make body scroll */
        #po_pending_popup_win .modal-body {
            overflow-y: auto;
            flex: 1;
        }

        /* Sticky submit button at top */
        #po_pending_popup_win .modal-footer {
            position: sticky;
            top: 0;
            z-index: 999;
            background: white;
            border-bottom: 1px solid #ddd;
            padding: 6px 10px !important;
        }
    </style>

    <script>
        $(document).ready(function() {
            $(document).on('click', '#long-list > tbody > tr', function(e) {
                // prevent triggering when clicking inside a nested table
                if ($(e.target).closest('table').attr('id') !== 'long-list') {
                    return;
                }

                if ($(e.target).closest('td').hasClass('no-toggle')) {
                    return; // do nothing if inside excluded cells
                }

                $(this).toggleClass('expand');
            });
        });
    </script>

    {{-- Modal PO --}}
    <div class="modal  fade" id="po_pending_popup_win" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl draggable modal-dialog-scrollable"
            style="top: 50px;left: 285px;width:1055px">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title ps-0">Purchase Invoice Pending List</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="container-fluid">

                        <div class="row">
                            <div class="col-lg-12 p-0">
                                <div class="">
                                    <table class="table table-hover popupPI" id="long-list"
                                        style="table-layout: fixed;width:100%">
                                        <thead>
                                            <tr>
                                                <th style="width:15px"><input type="checkbox" id="po_check_all"
                                                        onclick="po_check_fun()" checked />
                                                    <script>
                                                        function po_check_fun() {
                                                            if ($("#po_check_all").prop('checked') == true) {
                                                                $('.po_check').prop('checked', true);
                                                            } else {
                                                                $('.po_check').prop('checked', false);
                                                            }
                                                        }
                                                    </script>
                                                </th>
                                                <th style="width:30px" class="text-center">@lang('SL')</th>
                                                <th style="width:70px">@lang('Part No')</th>
                                                <th style="width:80px">@lang('Description')</th>
                                                <th style="width:30px" class="text-center">@lang('Tax')</th>
                                                <th style="width:30px" class="text-center">@lang('GRN Qty')</th>
                                                <th style="width:30px" class="text-center">@lang('Qty')</th>
                                                <th style="width:60px" class="text-end">@lang('Unit Price')</th>
                                                <th style="width:60px" class="text-end">@lang('Value')</th>
                                                <th class="resizable text-end" width="60px" scope="col">Dis</th>
                                                <th class="resizable text-end" width="60px" scope="col">Freight
                                                </th>
                                                <th class="resizable text-end" width="60px" scope="col">Custom
                                                </th>
                                                <th class="resizable text-end" width="80px">@lang('Taxable')</th>
                                                <th class="resizable text-end" width="80px">@lang('VAT')</th>
                                                <th class="resizable text-end" width="80px">@lang('Total')</th>
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
                <div class="modal-footer d-flex justify-content-center p-0">

                    <button type="submit" class="btn btn-light add-btn ms-2" id="addGRNPendingINMAINTable">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Submit
                    </button>
                </div>
            </div>
        </div>
    </div>
    {{-- Modal PO --}}

    <script>
        $(document).on("keydown",
            'input[name="qty[]"], input[name="unitprice[]"], input[name="discount[]"], input[name="serial_no[]"]',
            function(e) {
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
        document.addEventListener('DOMContentLoaded', function() {
            const referenceInput = document.getElementById('narration');
            const narrationTextarea = document.getElementById('narrationTextarea');
            const insertButton = document.getElementById('insertNarration');
            const narrationModal = document.getElementById('narrationModal');

            // Pre-fill textarea when modal opens
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
        function get_adjustment_list() {

            $("#loading_bg").css("display", "block");

            $('#adj_piv_amount_actual').val($("input[name='totalamount[]']").val());
            $('#adj_sup_id').val($('#vendors').val());

            var action = "{{ URL::to('purchase-invoice-get-adjustment') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    vendors: $("#vendors").val(),
                },
                cache: false,
                success: function(dataResult) {
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
                                            <td class='border text-end'>" + a + "</td>\
                                            <td class='border text-end'><input type='text' name='set_amt[]' id='set_amt_" +
                                data.unadjusted[i].doc_number +
                                "' class='form-control text-end' value='' onclick=\"set_adjust('" + (data
                                    .unadjusted[i].amount - data.unadjusted[i].adj_amount) + "','" + data
                                .unadjusted[i].doc_number + "')\" />\
                                                <input type='hidden' name='paymentno[]' value='" + data.unadjusted[i]
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
                                            <td class='border text-end'>" + (data.unadjusted_pdc[i].amount - data
                                    .unadjusted_pdc[i].adj_amount) + "</td>\
                                            <td class='border text-end'><input type='text' name='set_amt[]' id='set_amt_" +
                                data.unadjusted_pdc[i].doc_number + "' class='form-control text-end' value='" +
                                data.unadjusted_pdc[i].adj_amount + "' onclick=\"set_adjust('" + (data
                                    .unadjusted_pdc[i].amount - data.unadjusted_pdc[i].adj_amount) + "','" +
                                data.unadjusted[i].doc_number + "')\" />\
                                                <input type='hidden' name='paymentno[]' value='" + data.unadjusted_pdc[i]
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
            $("#btnModalAdjustmentNew").click();
            $("#loading_bg").css("display", "none");
        }
    </script>
    <script>
        $(document).ready(function() {
            $('#adjustmentForm').on('submit', function(e) {
                e.preventDefault();

                // Collect the form data
                let formData = $(this).serialize();

                // Optional: basic validation


                // AJAX submission
                $.ajax({
                    url: "{{ url('purchase-invoice-add-adjustment-cart') }}", // Replace with your actual route
                    type: "POST",
                    data: formData,
                    success: function(response) {
                        // Handle success response
                        alert('Adjustment saved successfully.');
                        $('#ModalAdjustment').modal('hide'); // Hide modal if using Bootstrap
                    },
                    error: function(xhr) {
                        // Handle errors
                        alert('Error occurred while saving. Check console.');
                        console.log(xhr.responseText);
                    }
                });
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
        function popup_grn_pending(id, po_id) {
            $("#loading_bg").css("display", "block");
            $("#hd_pending_grn_id").val(id);
            $("#hd_pending_po_id").val(po_id);
            $("#grn_id").val(id);
            document.getElementById('addGRNPending').click();
            $("#loading_bg").css("display", "none");
        }

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

            $('#myTable tbody tr').each(function() {
                const $row = $(this);

                total_qty += parseFloat($row.find('input[name="qty[]"]').val()) || 0;
                total_price += parseFloat($row.find('input[name="unitprice[]"]').val().replace(/,/g, '')) || 0;
                total_value += parseFloat($row.find('input[name="value[]"]').val().replace(/,/g, '')) || 0;
                total_discount += parseFloat($row.find('input[name="discount[]"]').val().replace(/,/g, '')) || 0;
                total_fright += parseFloat($row.find('input[name="fright[]"]').val().replace(/,/g, '')) || 0;
                total_customcharges += parseFloat($row.find('input[name="customcharges[]"]').val().replace(/,/g,
                    '')) || 0;
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



            // When the vendors select2 opens, prefill the search box with the currently selected vendor name
            $('#vendors').on('select2:open', function(e) {
                try {
                    var sel = $(this).select2('data');
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
                    $row.find('input[name="discount[]"]').val(0);
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
                                        /* ignore */ }
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
            $(document).on("change", "#vendors", function() {
                var id = $("#vendors").val();
                console.log("12121212121212")
                get_vendors_detail(id);
            });
        });

        function get_vendors_detail(id) {
            console.log(id, "dfdfedfed")
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
                            $("#payment_terms").val(dataResult['data'][i].payment_terms).trigger('change');
                            $("#contact_person_name").val(dataResult['data'][i].contact_person);
                            $("#contact_person_email").val(dataResult['data'][i].email);
                            //$("#shipping_address_2").val(dataResult['data'][i].address2);
                            $("#contact_person_telephone").val(dataResult['data'][i]
                                .contcat_number);

                            $("#supplier_type").val(dataResult['data'][i].supplier_type).trigger('change');
                            $("#purchase_type").val(dataResult['data'][i].purchase_type).trigger('change');
                            $('#vat_percent').val(dataResult['data'][i].vat_percentage);
                            $('#vat_number').val(dataResult['data'][i].vat_number);

                            //$("select[id=tax] option:first").text(dataResult['data'][i].vat_percentage +'%');
                            //$("select[id=tax] option:first").val(dataResult['data'][i].vat_percentage);
                            $("#tax").val(dataResult['data'][i].vat_percentage);

                            $("#country").val(dataResult['data'][i].vat_country).trigger('change');

                             window.SELECTED_STATE_ID = dataResult['data'][i].vat_state;

                            // state = dataResult['data'][i].vat_state;

                            // setTimeout(function() {
                            //     $("#state").val(state).trigger('change');
                            // }, 1000);

                        }

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
        }
    </script>


    <script>
        function get_pending_grn_list() {
            var id = $("#vendors").val();
            get_vat(id);
            get_po_list(id);
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
                    if (dataResult['data'] != null) {
                        len = dataResult['data'].length;
                    }
                    if (len > 0) {
                        $("#plist").empty();
                        for (var i = 0; i < len; i++) {
                            var id = dataResult['data'][i].id;
                            var po_id = dataResult['data'][i].po_id;
                            var doc_number = dataResult['data'][i].doc_number;
                            var option = "<option value='" + id + "'>" + doc_number +
                                "</option>";
                            var innerHtml =
                                "<input type='checkbox' onclick='popup_grn_pending(" + id +
                                ", " + po_id + ")' id='pending_grn_" + i +
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

        function get_deal_code() {
            var action = "{{ URL::to('get-deal-code-from-id') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    deal_id: $('#deal_id').val(),
                },
                cache: false,
                success: function(dataResult) {
                    $("#deal_id").val(dataResult);
                }
            });
        }
    </script>



    <!-- Modal License Key-->
    <a id="btn_ModalLicenseKey" data-toggle="modal" data-target="#ModalLicenseKey" data-backdrop="static"
        data-keyboard="false"></a>
    <div class="modal fade" id="ModalLicenseKey" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg draggable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add License Key - <label
                            id="ModalLabelHeading"></label></h5>
                    <a class="btn-sm btn-danger float-right" data-toggle="modal"
                        data-target="#ModalExcelQuote">License Excel Import</a>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>


                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-2">
                            <label for="" class="form-label">Qty</label><input type="hidden"
                                id="item_id" />
                            <input type="number" class="form-control" name="license_qty" id="license_qty"
                                value="1" readonly />
                        </div>
                        <div class="col-md-6">
                            <label for="" class="form-label">License Key</label>
                            <input type="text" class="form-control" name="license_key" id="license_key" />
                        </div>
                        <div class="col-md-3">
                            <label for="" class="form-label">Exp Date</label>
                            <input type="date" class="form-control" name="exp_date" id="exp_date" />
                        </div>
                        <div class="col-md-1"><br />
                            <button type="button" id="license_add" class="btn btn-primary"
                                onclick="return add_license_key()">Add</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mt-2">
                            <table id="lk-table" class="table table-bordered table-striped" width="100%"
                                cellspacing="0">
                                <thead>
                                    <tr>
                                        <th style="width: 10%;">Sr.No</th>
                                        <th style="width: 60%;">Licence Key</th>
                                        <th style="width: 20%;">Expiry Date</th>
                                        <th style="width: 10%;"></th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="button" data-dismiss="modal">Save</button>
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Excel Quote-->
    <div class="modal fade" id="ModalExcelQuote" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg draggable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">License Excel Import</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Select File (.csv)</label>
                                <input type="file" name="import_file" id="import_file" class="btn-danger" />
                                (<a href="{{ url('public/uploads/product_upload/grn_license_sample_format.csv') }}"
                                    target="_blank">Sample File</a>)
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary"
                        onclick="return excel_license_key()">Upload</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Excel Quote-->


    <script>
        function set_license_key() {
            
            $(document).on("keypress", 'input[name="qty[]"]', function(e) {
                if (e.which === 13) { // Enter key
                    let $row = $(this).closest("tr"); // current row
                    let pt = $row.find('input[name="product_type[]"]').val();


                    if (pt == 2) {
                        $('#item_id').val($row.find('select[name="part_number[]"]').val());
                        $("#ModalLabelHeading").text($row.find('select[name="part_number[]"] option:selected')
                        .text());
                        $("#license_qty").val($(this).val()); // qty value from current input
                        $("#btn_ModalLicenseKey").click();
                        view_license_key();
                        e.preventDefault();
                        return false;
                    }

                    return true;
                }
            });
        }

        function set_license_key_po(rowid, producttype) {
            $('#qty_' + rowid).keypress(function(e) {
                var key = e.which;
                if (key === 13) { //the enter key code
                    var pt = producttype;
                    if (pt == 2) {
                        $('#item_id').val($('#part_id_' + rowid).val());
                        $('#ModalLabelHeading').text($('#part_number_' + rowid).val());
                        $('#license_qty').val($('#qty_' + rowid).val())
                        $('#btn_ModalLicenseKey').click();
                        view_license_key();
                    }
                    return true;
                }
            });
        }


        function add_license_key() {
            $("#loading_bg").css("display", "block");

            if ($('#license_key').val() == "") {
                $('#license_key').focus();
                $("#loading_bg").css("display", "none");
                return false;
            }
            if ($('#exp_date').val() == "") {
                $('#exp_date').focus();
                $("#loading_bg").css("display", "none");
                return false;
            }
            if ($('#license_qty').val() == "") {
                $('#license_qty').focus();
                $("#loading_bg").css("display", "none");
                return false;
            }

            var action = "{{ URL::to('add-grn-license-key-cart') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    item_id: $('#item_id').val(),
                    license_key: $('#license_key').val(),
                    exp_date: $('#exp_date').val(),
                    license_qty: $('#license_qty').val(),

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
                            if (Number(i + 1) >= $('#license_qty').val()) {
                                $('#license_add').prop('disabled', true);
                            } else {
                                $('#license_add').prop('disabled', false);
                            }
                            getSelectedRows += "<tr>\
                                                        <td>" + Number(i + 1) + "</td>\
                                                        <td>" + dataResult['data'][i].license_key + "</td>\
                                                        <td>" + get_format_date(dataResult['data'][i].exp_date) + "</td>\
                                                        <td><a onclick='delete_license_key(" + dataResult['data'][i].id + ")' class='btn-sm btn-light'><i class='ico icon-outline-trash-bin-trash'></i></a></td>\
                                                        </tr>";
                        }
                        $('#license_key').val('');
                        $('#exp_date').val('');
                        $('#lk-table tbody').empty();
                        $("#lk-table tbody").append(getSelectedRows);
                    } else {
                        $('#lk-table tbody').empty();
                    }
                }
            });
            $("#loading_bg").css("display", "none");
        }

        function excel_license_key() {
            $("#loading_bg").css("display", "block");

            if ($('#import_file').val() == "") {
                $('#import_file').focus();
                $("#loading_bg").css("display", "none");
                return false;
            }

            var action = "{{ URL::to('add-grn-license-key-cart-excel') }}";

            var formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}'); // Append CSRF token
            formData.append('item_id', $('#part_number_new').val()); // Append other form data
            formData.append('license_qty', $('#license_qty').val()); // Append other form data            
            formData.append('import_file', $('#import_file')[0].files[0]);


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
                            if (Number(i + 1) >= $('#license_qty').val()) {
                                $('#license_add').prop('disabled', true);
                            } else {
                                $('#license_add').prop('disabled', false);
                            }
                            getSelectedRows += "<tr>\
                                                        <td>" + Number(i + 1) + "</td>\
                                                        <td>" + dataResult['data'][i].license_key + "</td>\
                                                        <td>" + get_format_date(dataResult['data'][i].exp_date) + "</td>\
                                                        <td><a onclick='delete_license_key(" + dataResult['data'][i].id + ")' class='btn-sm btn-light'><i class='ico icon-outline-trash-bin-trash'></i></a></td>\
                                                        </tr>";
                        }
                        $('#license_key').val('');
                        $('#exp_date').val('');
                        $('#lk-table tbody').empty();
                        $("#lk-table tbody").append(getSelectedRows);
                    } else {
                        $('#lk-table tbody').empty();
                    }
                }
            });
            $("#loading_bg").css("display", "none");
        }

        function view_license_key() {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('view-grn-license-key-cart') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    item_id: $('#part_number_new').val(),
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
                            if (Number(i + 1) >= $('#license_qty').val()) {
                                $('#license_add').prop('disabled', true);
                            } else {
                                $('#license_add').prop('disabled', false);
                            }
                            getSelectedRows += "<tr>\
                                                        <td>" + Number(i + 1) + "</td>\
                                                        <td>" + dataResult['data'][i].license_key + "</td>\
                                                        <td>" + get_format_date(dataResult['data'][i].exp_date) + "</td>\
                                                        <td><a onclick='delete_license_key(" + dataResult['data'][i].id + ")' class='btn-sm btn-danger'><i class='ico icon-outline-trash-bin-trash'></i></a></td>\
                                                        </tr>";
                        }
                        $('#license_key').val('');
                        $('#exp_date').val('');
                        $('#lk-table tbody').empty();
                        $("#lk-table tbody").append(getSelectedRows);
                    } else {
                        $('#lk-table tbody').empty();
                    }
                }
            });
            $("#loading_bg").css("display", "none");
        }

        function delete_license_key(id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('delete-grn-license-key-cart') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    item_id: $('#part_number_new').val(),
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
                            if (Number(i + 1) >= $('#license_qty').val()) {
                                $('#license_add').prop('disabled', true);
                            } else {
                                $('#license_add').prop('disabled', false);
                            }
                            getSelectedRows += "<tr>\
                                                        <td>" + Number(i + 1) + "</td>\
                                                        <td>" + dataResult['data'][i].license_key + "</td>\
                                                        <td>" + get_format_date(dataResult['data'][i].exp_date) + "</td>\
                                                        <td><a onclick='delete_license_key(" + dataResult['data'][i].id + ")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
                                                        </tr>";
                        }
                        $('#license_key').val('');
                        $('#exp_date').val('');
                        $('#lk-table tbody').empty();
                        $("#lk-table tbody").append(getSelectedRows);
                    } else {
                        $('#lk-table tbody').empty();
                    }
                }
            });
            $("#loading_bg").css("display", "none");
        }
    </script>
    <!-- Modal License Key-->
    <!-- Modal License Key-->



    {{-- attachment start --}}
    <div class="modal side-panel fade" id="attachment_popup_win" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg draggable">
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

                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="srl_id" />
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

            var action = "{{ URL::to('add-purchase-invoice-attachment') }}";

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
                                    <td><a onclick='delete_attachment(" + dataResult['data'][i].id + ")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
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

            var action = "{{ URL::to('view-purchase-invoice-attachment') }}";
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
                                    <td><a onclick='delete_attachment(" + dataResult['data'][i].id + ")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
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
            var action = "{{ URL::to('delete-purchase-invoice-attachment') }}";
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
                                    <td><a onclick='delete_attachment(" + dataResult['data'][i].id + ")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
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

    {{-- attachment end --}}

    <script src="{{ asset('public/js/form-validation-toastr.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize form validation for crm-deals-form
            FormValidator.init('purchase-invoice-create-form', {
                showAllErrors: true,
                scrollToFirst: true,
                highlightFields: true,
                toastrPosition: 'toast-top-right',
                toastrTimeout: 6000
            });
        });
    </script>

    <script>
            $(document).ready(function () {

    $(document).on("change", "#shipping_supplier", function () {
        var id = $("#shipping_supplier").val();
        get_shipping_supplier_detail2(id);
    });

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
</script>


   <script>
                            $(document).ready(function () {
                                var $bill = $('#bill_number');
                                var $narr = $('#narration');

                                // Initialize tracking: if narration already equals bill, mark it as auto-filled
                                var initialBill = ($bill.val() || '').toString().trim();
                                var initialNarr = ($narr.val() || '').toString().trim();
                                if (initialBill !== '' && initialNarr === initialBill) {
                                    $narr.data('autoBill', initialBill);
                                } else {
                                    $narr.data('autoBill', '');
                                }

                                // When bill_number changes, update narration only if narration is empty or was previously auto-filled
                                $bill.on('input change', function () {
                                    var bill = ($(this).val() || '').toString().trim();
                                    var currentNarr = ($narr.val() || '').toString().trim();
                                    var lastAuto = $narr.data('autoBill') || '';

                                    if (currentNarr === '' || currentNarr === lastAuto) {
                                        $narr.val(bill);
                                        $narr.data('autoBill', bill);
                                    }
                                });

                                // If user manually edits narration, stop auto-overwrites
                                $narr.on('input', function () {
                                    var currentNarr = ($narr.val() || '').toString().trim();
                                    var currentBill = ($bill.val() || '').toString().trim();
                                    if (currentNarr === currentBill) {
                                        // still matches bill -> keep tracked
                                        $narr.data('autoBill', currentBill);
                                    } else {
                                        // user edited manually -> mark as manual
                                        $narr.data('autoBill', null);
                                    }
                                });
                            });
                        </script>

   <script>
                            $(document).ready(function () {
                                var $bill = $('#bill_number');
                                var $narr = $('#narration');

                                // Initialize tracking: if narration already equals bill, mark it as auto-filled
                                var initialBill = ($bill.val() || '').toString().trim();
                                var initialNarr = ($narr.val() || '').toString().trim();
                                if (initialBill !== '' && initialNarr === initialBill) {
                                    $narr.data('autoBill', initialBill);
                                } else {
                                    $narr.data('autoBill', '');
                                }

                                // When bill_number changes, update narration only if narration is empty or was previously auto-filled
                                $bill.on('input change', function () {
                                    var bill = ($(this).val() || '').toString().trim();
                                    var currentNarr = ($narr.val() || '').toString().trim();
                                    var lastAuto = $narr.data('autoBill') || '';

                                    if (currentNarr === '' || currentNarr === lastAuto) {
                                        $narr.val(bill);
                                        $narr.data('autoBill', bill);
                                    }
                                });

                                // If user manually edits narration, stop auto-overwrites
                                $narr.on('input', function () {
                                    var currentNarr = ($narr.val() || '').toString().trim();
                                    var currentBill = ($bill.val() || '').toString().trim();
                                    if (currentNarr === currentBill) {
                                        // still matches bill -> keep tracked
                                        $narr.data('autoBill', currentBill);
                                    } else {
                                        // user edited manually -> mark as manual
                                        $narr.data('autoBill', null);
                                    }
                                });
                            });
                        </script>



    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

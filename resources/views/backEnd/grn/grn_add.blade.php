    <?php try { ?>

    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'goods-receipt-note-store', 'method' => 'POST', 'id' => 'goods-receipt-note-store-form', 'novalidate' => true]) }}
    <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
    <input type="hidden" name="id" value="{{ isset($edit) ? $edit->id : '' }}">
    <input type="hidden" name="net_vat" id="net_vat" value="0">
    <input type="hidden" id="hd_pending_po_id" name="hd_pending_po_id" />
    <input type="hidden" id="company_id" value="{{ session('logged_session_data.company_id') }}" />




    <div class="purchase-order-content-header">
        <h4 class="purchase-order-content-header-left">
            New
            ({{ isset($edit) ? (!empty(@$edit->title) ? @$edit->title : old('doc_number')) : @APP\SysHelper::get_new_code('sys_purchase_grn', 'GR', 'doc_number') }})
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
                    <label class="form-label">Vendor</label>
                    <div class="form-group">
                        <select class="form-control js-account-select" name="vendors" id="vendors"
                            onchange="get_pending_po_list()">
                            <option value=""></option>
                            {{-- @foreach ($vendors as $value)
                                                    <option value="{{ @$value->id }}"
                                                        {{ isset($edit) ? (!empty($edit->vendor_id) ? (@$edit->vendor_id == @$value->id ? 'selected' : '') : '') : '' }}>
                                                        {{ @$value->account_name }}
                                                    </option>
                                                @endforeach --}}
                        </select>



                    </div>
                </div>
                <div class="col-2">
                    <label class="form-label">GRN Number</label>
                    <div class="form-group">
                        <input class="form-control" type="text" name="doc_number" autocomplete="off" id="doc_number"
                            value="{{ isset($edit) ? (!empty(@$edit->title) ? @$edit->title : old('doc_number')) : @APP\SysHelper::get_new_code('sys_purchase_grn', 'GR', 'doc_number') }}"
                            readonly>
                    </div>
                </div>
                <div class="col-2">
                    <label class="form-label">GRN Date</label>
                    <div class="form-group">
                        @php  $value_date = \Carbon\Carbon::parse( now())->format('d/m/Y'); @endphp
                        <input class="form-control date-picker" id="grn_date" type="text" autocomplete="off"
                            name="grn_date" value="{{ @$value_date }}">
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
                        <input readonly value="{{ Auth::user()->full_name }}" type="text" class="form-control"
                            name="createdby" id="createdby">

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


                    <div class="col-2 mb-2">
                        <div class="input-effect">
                            <label class="txtlbl">Pending list</label>
                            <div id="plist"
                                style="width: 100%; height: 80px; border: solid 1px #dfe1d7; border-radius: 5px; padding: 5px 5px; overflow-y: scroll;">
                            </div>
                            <a data-modal-size="modal-md" data-target="#po_pending_popup_win" id="addPoPending"
                                data-toggle="modal"></a>
                            <input type="hidden" id="po_id" name="po_id">
                            <input type="hidden" id="vat_percentage" name="vat_percentage">
                        </div>
                    </div>
                    <div class="col-10 mb-2">
                        <div class="row gap-rows">



                            <div class="col-2">
                                <label class="form-label">Bill Number</label>
                                <div class="form-group">
                                    <input class="form-control" type="text" name="bill_number" autocomplete="off"
                                        id="bill_number" required
                                        value="{{ isset($edit) ? (!empty(@$edit->bill_number) ? @$edit->bill_number : old('bill_number')) : '' }}">
                                </div>
                            </div>
                            <div class="col-2">
                                <label class="form-label">Bill Date</label>
                                <div class="form-group">

                                    @php
                                        $raw_date =
                                            old('bill_date') ??
                                            (isset($edit) && !empty($edit->bill_date) ? $edit->bill_date : now());
                                        $value_date = \Carbon\Carbon::parse($raw_date)->format('d/m/Y');
                                    @endphp

                                    <input class="form-control date-picker" id="bill_date" type="text"
                                        autocomplete="off" name="bill_date" value="{{ @$value_date }}"
                                        style="margin-top: 0px;">
                                </div>
                            </div>

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
                                @php
                                    $raw_date =
                                        old('lpo_date') ??
                                        (isset($edit) && !empty($edit->lpo_date) ? $edit->lpo_date : now());
                                    $value_date = \Carbon\Carbon::parse($raw_date)->format('d/m/Y');
                                @endphp

                                <div class="form-group">
                                    <input
                                        class="txtbx primary-input form-control date-picker {{ $errors->has('lpo_date') ? ' is-invalid' : '' }}"
                                        type="text" name="lpo_date" autocomplete="off" id="lpo_date"
                                        value="{{ $value_date }}">
                                </div>
                            </div>

                            <div class="col-2">
                                <label class="form-label">Payment Terms</label>
                                <div class="form-group">
                                    <select class="form-control js-example-basic-single" name="payment_terms"
                                        id="payment_terms" required>
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
                                        <label class="form-label">Other Payment Terms</label>
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
                                        value="{{ isset($edit) ? (!empty(@$edit->deal_id) ? @$edit->deal_id : old('deal_id')) : old('deal_id') }}"
                                        id="deal_id">
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
                                <div class="modal fade" id="customerReferenceModal" tabindex="-1"
                                    data-bs-backdrop="false" aria-hidden="true">
                                    <div class="modal-dialog modal-md draggable" style="top:10rem;">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Select Customer References</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <label class="form-label">References</label>
                                                <select id="modal_ref_company_select"
                                                    class="form-control js-example-basic-single" multiple
                                                    style="width:100%">

                                                    @foreach ($customer_reference_list as $value)
                                                        <option value="{{ @$value->id }}"
                                                            @if (@$deal->cust_id == @$value->id) selected @endif>
                                                            {{ @$value->name }} @if (@App\SysHelper::getCompanyCodeSettings()['is_customer_code'])
                                                                ({{ @$value->code }})
                                                            @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="modal-footer">

                                                <button type="button" id="save_customer_reference"
                                                    class="btn btn-light"><i
                                                        class="ico icon-outline-bookmark-opened text-success"></i>
                                                    Save</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <script>
                                    $(document).ready(function() {
                                        // Initialize Select2 inside modal
                                        $('#modal_ref_company_select').select2({
                                            placeholder: 'Select references',
                                            dropdownParent: $('#customerReferenceModal'),
                                            width: '100%'
                                        });

                                        // Open modal on input click
                                        $('#customer_reference_input').on('click', function() {
                                            // preload selection from hidden inputs
                                            let vals = $('#ref_company_hidden_inputs input[name="ref_company_id[]"]').map(function() {
                                                return $(this).val();
                                            }).get();
                                            $('#modal_ref_company_select').val(vals).trigger('change');
                                            $('#customerReferenceModal').modal('show');
                                        });

                                        // Save selections back to visible input and hidden inputs
                                        $('#save_customer_reference').on('click', function() {
                                            let selectedVals = $('#modal_ref_company_select').val() || [];
                                            let selectedTexts = $('#modal_ref_company_select').select2('data').map(function(d) {
                                                return d.text;
                                            });

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
                                                    $container.append('<input type="hidden" name="ref_company_id[]" value="' +
                                                        $('<div>').text(v).html() + '" />');
                                                });
                                            }

                                            $('#customerReferenceModal').modal('hide');
                                        });

                                        // If modal closed without save, do nothing (retain previous selection)
                                    });
                                </script>

                                <!-- Auto-fill narration from bill_number (production-ready, preserves manual edits) -->
                                <script>
                                    $(document).ready(function() {
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
                                        $bill.on('input change', function() {
                                            var bill = ($(this).val() || '').toString().trim();
                                            var currentNarr = ($narr.val() || '').toString().trim();
                                            var lastAuto = $narr.data('autoBill') || '';

                                            if (currentNarr === '' || currentNarr === lastAuto) {
                                                $narr.val(bill);
                                                $narr.data('autoBill', bill);
                                            }
                                        });

                                        // If user manually edits narration, stop auto-overwrites
                                        $narr.on('input', function() {
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


                                <!-- <div class="form-group">

                                    <select class="form-control js-example-basic-single" name="ref_company_id[]" id="ref_company_id" required multiple>
                            <option value="">-Select-</option>
                            @foreach ($customer_reference_list as $value)
<option value="{{ @$value->id }}" @if (@$deal->cust_id == @$value->id) selected @endif >{{ @$value->name }}
                                @if (@App\SysHelper::getCompanyCodeSettings()['is_customer_code'])
({{ @$value->code }})
@endif
                            </option>
@endforeach
                        </select>
                                    <input class="form-control" type="hidden" name="reference" autocomplete="off"
                                        value="{{ isset($grn) ? (!empty(@$grn->reference) ? @$grn->reference : old('reference')) : old('reference') }}"
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

                            <div class="modal fade" id="otherSalesPersonModal" tabindex="-1" aria-hidden="true"
                                data-bs-backdrop="false">
                                <div class="modal-dialog modal-sm draggable" style="top:10rem;left:10rem">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Select Other Sales Person</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <label class="form-label">Sales Person</label>

                                            <input type="text" id="other_sales_person_input" class="form-control"
                                                placeholder="">


                                        </div>
                                        <div class="modal-footer">

                                            <button type="button" id="save_other_sales_person"
                                                class="btn btn-light"><i
                                                    class="ico icon-outline-bookmark-opened text-success"></i>
                                                Save</button>
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
                                            var existing = $sales.find('option[data-manual][data-name="' + manualName.replace(/"/g,
                                                '&quot;') + '"]');
                                            if (existing.length) {
                                                $sales.val(existing.val()).trigger('change');
                                            } else {
                                                var $newOpt = $('<option>').val(val).text(text).attr({
                                                    'data-manual': '1',
                                                    'data-name': manualName
                                                });
                                                $sales.append($newOpt);
                                                $sales.val(val).trigger('change');
                                            }

                                            // Add or update hidden input so server receives manual name
                                            var $hidden = $('input[name="sales_person_name"]');
                                            if ($hidden.length) {
                                                $hidden.val(manualName);
                                            } else {
                                                $('<input>').attr({
                                                    type: 'hidden',
                                                    name: 'sales_person_name',
                                                    value: manualName
                                                }).appendTo('form#tender-create-form');
                                            }

                                        }
                                        // update stored prev to the newly selected value
                                        $sales.data('prev', $sales.val());

                                        // Close modal
                                        $('#otherSalesPersonModal').modal('hide');
                                    });

                                    // If modal is closed without saving, restore previous selection
                                    $('#otherSalesPersonModal').on('hidden.bs.modal', function() {
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
                                <label class="form-label">Warehouse</label>
                                <div class="form-group">
                                    <!-- <input class="form-control" type="text" name="warehouse" autocomplete="off"
                                        value="{{ isset($edit) ? (!empty(@$edit->warehouse) ? @$edit->warehouse : old('warehouse')) : old('warehouse') }}"
                                        id="warehouse"> -->
                                    @php
                                        $warehouses = App\SysHelper::getCompanyWarehouses();
                                    @endphp


                                    <select class="form-control js-example-basic-single" required name="warehouse"
                                        id="warehouse">

                                        @foreach ($warehouses as $value)
                                            <option value="{{ @$value->id }}">{{ @$value->warehouse_name }}
                                            </option>
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



                            <div class="col-2">
                                <label class="form-label">Remarks</label>
                                <div class="form-group">
                                    <input data-bs-toggle="modal" data-bs-target="#narrationModal"
                                        class="form-control" type="text" name="narration" autocomplete="off"
                                        value="{{ isset($edit) ? (!empty(@$edit->narration) ? @$edit->narration : old('narration')) : old('narration') }}"
                                        id="narration">
                                </div>
                            </div>



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
                                <!-- <option value=""></option> -->
                                @foreach ($customer as $value)
                                    <option value="{{ @$value->id }}"
                                        @if ($value->company_ship_to_id == session('logged_session_data.company_id')) selected @endif>
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
                                <select class="form-control js-example-basic-single" name="supplier_state"
                                    id="state" required>
                                    <option value=""></option>

                                    @foreach ($states as $value)
                                        <option value="{{ $value->id }}">
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

                    <div class="col">
                        <label class="form-label">Supplier Type</label>
                        <div class="form-group">
                            <select
                                class="form-control js-example-basic-single {{ $errors->has('supplier_type') ? ' is-invalid' : '' }}"
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
                                class="form-control  js-example-basic-single  {{ $errors->has('purchase_type') ? ' is-invalid' : '' }}"
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
    <div class="table-container" style="border: solid 1px #d9d9d9;">
        <table class="table table-hover form-item-table" id="myTable">
            <thead>
                <tr>
                    <th class="resizable text-center" width="30px">@lang('No')<div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="150px">@lang('Part No')<div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="280px">@lang('Description')<div class="resizer"></div>
                    </th>

                    @if (session('logged_session_data.company_id') == 2)
                        <th class="resizable text-center" width="60px">@lang('HS Code')<div class="resizer"></div>
                        </th>
                    @endif

                    <th class="resizable text-center" width="30px">@lang('Tax')<div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="30px">@lang('Qty')<div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="80px">@lang('Price')<div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="80px">@lang('Value')<div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="80px" scope="col">Dis <a
                            class="icon icon-outline-book" data-bs-popover="popover" data-bs-trigger="hover"
                            data-bs-delay="500" data-bs-content="Add Discount" data-bs-placement="top"
                            data-bs-toggle="modal" data-bs-target="#discountModal"></a>
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="80px" scope="col">Freight <a
                            class="icon icon-outline-book" data-bs-popover="popover" data-bs-trigger="hover"
                            data-bs-delay="500" data-bs-content="Add Freight" data-bs-placement="top"
                            data-bs-toggle="modal" data-bs-target="#freightModal"></a>
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="80px" scope="col">Custom <a
                            class="icon icon-outline-book" data-bs-popover="popover" data-bs-trigger="hover"
                            data-bs-delay="500" data-bs-content="Add Custom" data-bs-placement="top"
                            data-bs-toggle="modal" data-bs-target="#customModal"></a>
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="80px">@lang('Taxable')<div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="80px">@lang('VAT')<div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="100px">@lang('Total')<div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="80px">@lang('Serial No')<div class="resizer"></div>
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

                        <input class="form-control" type="text" name="product_type[]" autocomplete="off"
                            readonly="true" hidden>
                        <input class="form-control" type="text" name="product_type_part_number_text[]"
                            autocomplete="off" readonly="true" hidden>
                    </td>


                    @if (session('logged_session_data.company_id') == 2)
                        <td>
                            <input class="form-control text-center" type="text" name="hscode_txt[]"
                                autocomplete="off" readonly="true">

                        </td>
                    @endif

                    <td><input type="number" class="form-control text-center" name="tax[]"
                            onchange="calc_change_new(this)">
                    </td>
                    <td><input class="form-control  text-center" data-enter-skip type="number" name="qty[]"
                            autocomplete="off" min="0" onchange="calc_change_new(this)"
                            onkeypress="return set_license_key(this, event)"></td>
                    <td><input class="form-control text-end" type="text" name="unitprice[]" step="any"
                            onblur="formatCurrency(this)" autocomplete="off" min="0"
                            onchange="calc_change_new(this)"></td>
                    <td><input class="form-control text-end" type="text" name="value[]" autocomplete="off"
                            min="0" readonly></td>
                    <td><input class="form-control text-end" type="text" name="discount[]" autocomplete="off"
                            onblur="formatCurrency(this)" min="0" onchange="calc_change_new(this)"></td>
                    <td><input class="form-control text-end" type="text" name="fright[]" autocomplete="off"
                            onblur="formatCurrency(this)" min="0" onchange="calc_change_new(this)"></td>
                    <td><input class="form-control text-end" type="text" name="customcharges[]"
                            onblur="formatCurrency(this)" autocomplete="off" min="0"
                            onchange="calc_change_new(this)"></td>
                    <td><input class="form-control text-end" type="text" name="taxableamount[]"
                            autocomplete="off" min="0" readonly></td>
                    <td><input class="form-control text-end" type="text" name="vatamount[]" autocomplete="off"
                            min="0" readonly></td>
                    <td><input class="form-control text-end" type="text" name="totalamount[]" autocomplete="off"
                            min="0" readonly></td>
                    <td><input class="form-control" type="text" name="serial_no[]"></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" scope="col">Total</th>


                    @if (session('logged_session_data.company_id') == 2)
                        <th class="text-center"></th>

                    @endif

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















     @php
        $r = @App\SysHelper::get_data_by_role();
        $company_id = $r[0];
        $customs_freight_account = @App\SysHelper::get_customs_freight_accounts_for_purchase($company_id);
        $cfc_pi_id = request('cfc_pi_id') ?? request('pi_id');
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
                            @foreach ($customs_freight_account as $key => $value)
                                <option value="{{ @$value->id }}">{{ @$value->account_name }}
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
                            @foreach ($vendors as $key => $value)
                                <option value="{{ @$value->id }}">{{ @$value->account_name }}
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
                    <th colspan="4"></th>
                    <th class="text-end" id="fright_total_amount">0</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
        @if (!empty($cfc_pi_id))
            <input type="hidden" name="cfc_pi_id" value="{{ $cfc_pi_id }}" />
        @endif
    </div>

    {{ Form::close() }}


    {{-- Models  --}}
    <!-- <a data-bs-toggle="modal" data-bs-target="#editModal"></a> -->

    @include('backEnd.inventory.itemAddModal')

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


    {{-- Modal PO --}}
    <div class="modal  fade" id="po_pending_popup_win" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg draggable modal-dialog-scrollable" style="top: 50px;left: 285px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title ps-0">Goods Receipt Note (GRN) Pending List</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="container-fluid">

                        <div class="row">
                            <div class="col-lg-12 p-0">
                                <div class="">
                                    <table class="table table-hover popupGRN" id="long-list"
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
                                                <th style="width:90px">@lang('Part No')</th>
                                                <th style="width:100px">@lang('Description')</th>
                                                @if (session('logged_session_data.company_id') == 2)
                                                    <th style="width:30px" class="text-center">@lang('HS Code')</th>
                                                @endif

                                                <th style="width:30px" class="text-center">@lang('Tax')</th>
                                                <th style="width:30px" class="text-center">@lang('Qty')</th>
                                                <th style="width:60px" class="text-end">@lang('Unit Price')</th>
                                                <th style="width:60px" class="text-end">@lang('Discount')</th>
                                                <th style="width:60px" class="text-end">@lang('Value')</th>


                                            </tr>
                                        </thead>
                                        <tbody>



                                        </tbody>
                                        <tfoot class="table-light"
                                            style="position: sticky; bottom: 0; background: #f8f9fa;">
                                            <tr>
                                                <th colspan="2" class="text-end fw-bold">Total:</th>
                                                <th></th>
                                                <th></th>
                                                @if (session('logged_session_data.company_id') == 2)
                                                    <th></th>
                                                @endif
                                                <th></th>
                                                <th class="text-center fw-bold" id="popup_total_qty">0</th>
                                                <th class="text-end fw-bold" id="popup_total_price">0.00</th>
                                                <th class="text-end fw-bold" id="popup_total_discount">0.00</th>
                                                <th class="text-end fw-bold" id="popup_total_value">0.00</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>




                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-center p-0">

                    <button type="submit" class="btn btn-light add-btn ms-2" id="addPoPendingINMAINTable">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Submit
                    </button>
                </div>
            </div>
        </div>
    </div>
    {{-- Modal PO --}}
    <script>
        window.COMPANY_ID = {{ (int) (session('logged_session_data.company_id') ?? 0) }};
    </script>
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

    <script>
        $(document).on("keydown", 'input[name="unitprice[]"], input[name="discount[]"], input[name="serial_no[]"]',
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
        $('#goods-receipt-note-store-form').on('keypress', function(e) {
            if (e.which === 13 && !$(e.target).is('input[name="qty[]"]') && !$(e.target).is(
                    'input[name="unitprice[]"]')) {
                e.preventDefault();
                return false;
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

        // Normalize serials: convert newlines and multiple separators into a clean comma-separated list
        function normalizeSerials(text) {
            if (!text) return '';
            // unify line endings and split on newline or comma (one or more), trim each token and remove empties
            const parts = text.replace(/\r/g, '\n').split(/[\n,]+/).map(p => p.trim()).filter(Boolean);
            return parts.join(', ');
        }

        $(document).on('click', 'input[name="serial_no[]"]', function() {
            currentSerialInput = $(this);
            // Prefill textarea with normalized value for clarity
            const formatted = normalizeSerials(currentSerialInput.val());
            $('#add_serial_no').val(formatted);
            serialNoModal.show();
            setTimeout(() => $('#add_serial_no').focus(), 500);

        });

        function addSerialNo() {
            if (currentSerialInput) {
                const raw = $('#add_serial_no').val();
                const formatted = normalizeSerials(raw);
                // Update source input and textarea with normalized value
                currentSerialInput.val(formatted);
                $('#add_serial_no').val(formatted);
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
            console.log("232323232333223")
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
                    $row.find('input[name="product_type[]"]').val(selectedData.product_type || '0');
                    $row.find('input[name="product_type_part_number_text[]"]').val(selectedData
                        .description || '');
                    $row.find('input[name="discount[]"]').val(0);
                    $row.find('input[name="fright[]"]').val(0);
                    $row.find('input[name="customcharges[]"]').val(0);
                    $row.find('input[name="tax[]"]').val(parseInt($('#net_vat').val()));
                    applyLicenseQtyHighlightForRow($row);
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
        function fillTableToFitScreenHeight(tableId, offset = 65) {
            const table = document.getElementById(tableId);
            if (!table) return;

            const tbody = table.querySelector('tbody');
            if (!tbody || tbody.rows.length === 0) return;

            const rowHeight = tbody.rows[0].offsetHeight;
            const pageHeight = window.innerHeight - offset;
            const tableTop = table.getBoundingClientRect().top;
            const availableHeight = pageHeight - tableTop;

            let existingRows = tbody.rows.length;
            let totalRows = Math.floor(availableHeight / rowHeight);

            const lastRow = tbody.rows[existingRows - 1];

            for (let i = existingRows + 1; i <= totalRows; i++) {
                const newRow = lastRow.cloneNode(true); // Clone the last row

                // Set first cell input value to row number
                const firstCellInput = newRow.cells[0]?.querySelector('input');
                if (firstCellInput) {
                    firstCellInput.value = i;
                }

                // Clear all other input fields
                const inputs = newRow.querySelectorAll('input');
                inputs.forEach((input, index) => {
                    if (index !== 0) input.value = "";
                });

                tbody.appendChild(newRow);
            }
            if (typeof applyLicenseQtyHighlightForRow === 'function' && window.jQuery) {
                window.jQuery('#myTable > tbody > tr').each(function() {
                    applyLicenseQtyHighlightForRow(window.jQuery(this));
                });
            }
        }

        window.onload = function() {
            fillTableToFitScreenHeight('myTable', 65);
        };
    </script>


    <script>
        function get_pending_po_list() {
            var id = $("#vendors").val();
            get_vat(id);
            get_po_list(id);


        }

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
                            // console.log(state,"statestate")
                            // setTimeout(function() {
                            //     $("#state").val(state).trigger('change');
                            // }, 900);


                            // $("#state").val(dataResult['data'][i].vat_state);
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
                        $('#net_vat').val(dataResult['data'].vat_percentage);
                        //$("select[id=tax] option:first").text(dataResult['data'].vat_percentage +'%');
                        //$("select[id=tax] option:first").val(dataResult['data'].vat_percentage);
                        $("#tax").val(dataResult['data'].vat_percentage);
                        $("#loading_bg").css("display", "none");
                    }
                }
            });
        }

        $(document).ready(function() {

            $(document).on("change", "#shipping_supplier", function() {
                if (!isAutoFilling) return;
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


        function get_po_list(id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('goods-receipt-note-pending') }}";
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
                            var doc_number = dataResult['data'][i].doc_number;
                            var option = "<option value='" + id + "'>" + doc_number +
                                "</option>";
                            var innerHtml =
                                "<input type='checkbox' onclick='popup_po_pending(" + id +
                                ")' id='pending_po_" + (i + 1) +
                                "' name='pending_po' value='" + id +
                                "'/> <label for='pending_po_" + (i + 1) + "'> " + doc_number +
                                "</label><br />";

                            $("#plist").append(innerHtml);


                        }
                    } else {
                        $("#plist").empty();
                    }
                    var innerHtml =
                        "<input type='checkbox' onclick='fillTableToFitScreenHeight('myTable', 65)' id='pending_po_0' name='pending_po' value='0'> <label for='pending_po_0'> Without PO</label><br />";
                    $("#plist").append(innerHtml);

                    $("#loading_bg").css("display", "none");
                }
            });
        }

        function popup_po_pending(id) {
            console.log("clicked", id)
            var selectedValues = [];
            $('input[name="pending_po"]:checked').each(function() {
                selectedValues.push($(this).val());
            });
            $("#loading_bg").css("display", "block");
            console.log(selectedValues)
            $("#hd_pending_po_id").val(selectedValues);
            $("#po_id").val(id);
            if (selectedValues != "") {
                document.getElementById('addPoPending').click();
            } else {
                const rowHtml = '<tr>\
                        <td><input type="text" class="form-control text-center" name="sort_id[]" value="1" /></td>\
                        <td class="noborder"><select class="form-control noborder" name="part_number[]"></select></td> \
                        <td><input class="form-control" type="text" name="description[]" autocomplete="off" readonly="true">\
                            <input class="form-control" type="text" name="part_number_txt[]" autocomplete="off" readonly="true" hidden>\
                            <input class="form-control" type="text" name="hscode_txt[]" autocomplete="off" readonly="true" hidden>\
                            <input class="form-control" type="text" name="product_type[]" autocomplete="off" readonly="true" hidden>\
                            <input class="form-control" type="text" name="product_type_part_number_text[]" autocomplete="off" readonly="true" hidden></td>\
                        <td><input type="number" class="form-control" name="tax[]" onchange="calc_change_new(this)"></td>\
                        <td><input class="form-control" type="number" name="qty[]" autocomplete="off" min="0" onchange="calc_change_new(this)" onkeypress="return set_license_key(this, event)"></td>\
                        <td><input class="form-control" type="number" name="unitprice[]" step="any" autocomplete="off" min="0" onchange="calc_change_new(this)"></td>\
                        <td><input class="form-control" type="number" name="value[]" autocomplete="off" min="0" readonly></td>\
                        <td><input class="form-control" type="number" name="discount[]" autocomplete="off" min="0" onchange="calc_change_new(this)"></td>\
                        <td><input class="form-control" type="number" name="fright[]" autocomplete="off" min="0" onchange="calc_change_new(this)"></td>\
                        <td><input class="form-control" type="number" name="customcharges[]" autocomplete="off" min="0" onchange="calc_change_new(this)"></td>\
                        <td><input class="form-control" type="number" name="taxableamount[]" autocomplete="off" min="0" readonly></td>\
                        <td><input class="form-control" type="number" name="vatamount[]" autocomplete="off" min="0" readonly></td>\
                        <td><input class="form-control" type="number" name="totalamount[]" autocomplete="off" min="0" readonly></td>\
                        <td><input class="form-control" type="text" name="serial_no[]"></td>\
                    </tr>';

                $('#myTable tbody').empty();
                $("#myTable tbody").append(rowHtml);
                fillTableToFitScreenHeight('myTable', 65);
            }

            if (id != 0) {
                //$("#table_id2").css("display", "none");    
            }

            $("#loading_bg").css("display", "none");
        }
    </script>


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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const referenceInput = document.getElementById('narration');
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
    <!-- Modal License Key-->
    <button id="btn_ModalLicenseKey" data-bs-target="#ModalLicenseKey" data-bs-toggle="modal" hidden></button>
    <div class="modal side-panel fade" id="ModalLicenseKey" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="ModalLicenseKey" aria-hidden="true">
        <div class="modal-dialog modal-lg draggable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">
                        Add License  <label style="margin-left: 117px" id="ModalLabelHeading"></label>
                    </h4>

                    <!-- Right side buttons -->
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-light" data-bs-toggle="modal"
                            data-bs-target="#ModalExcelQuote" title="Import license keys from CSV or Excel">
                            Import
                        </button>

                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                </div>


                <div class="modal-body mt-2">
                    <div class="row">
                        <div class="col-md-2">
                            <label for="" class="form-label">Qty</label>
                            <input type="hidden" id="item_id" />
                            <input type="hidden" id="license_row_index" value="" />
                            <input type="hidden" id="edit_license_id" value="" />
                            <input type="number" class="form-control" name="license_qty" id="license_qty"
                                value="1" readonly />
                           
                        </div>
                        <div class="col-md-5">
                            <label for="" class="form-label">License Key  (<span id="licenseCountSummary" class="text-muted small mt-2">0 out of 0 selected</span>)</label>
                            <input type="text" class="form-control" name="license_key" id="license_key" />
                        </div>
                        <div class="col-md-3">
                            <label for="" class="form-label">Expiry Date</label>
                            <input type="text" class="form-control date-picker" name="exp_date" id="exp_date"
                                autocomplete="off" />
                        </div>
                        <div class="col-md-2"><br />
                            <button type="button" id="license_add" class="btn btn-light"
                                onclick="return add_license_key()"><i class="ico icon-outline-add-square text-success me-1"></i>Add</button>
                            <button type="button" id="license_cancel_edit"
                                class="btn btn-sm btn-outline-secondary ms-1" onclick="cancel_license_edit()"
                                style="display:none;" title="Cancel edit">&#x2715;</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mt-2">
                            <div id="licenseKeyMessage" class="text-danger small mb-2" style="display:none;"></div>
                            <table id="lk-table" class="table table-hover" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th style="width: 8%;">Sr.No</th>
                                        <th style="width: 55%;">Licence Key</th>
                                        <th style="width: 20%;">Expiry Date</th>
                                        <th style="width: 17%;"></th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light add-btn ms-2" onclick="return save_license_keys()"
                        aria-label="Close">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Save & Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Excel Quote-->
    <div class="modal fade" id="ModalExcelQuote" data-bs-backdrop="false" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm draggable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">License Excel Import</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Select File (.csv)</label>
                                <input type="file" name="import_file" id="import_file" class="form-control"
                                    accept=".csv, .xls, .xlsx" />
                                <div class="form-text">
                                    Supported formats:
                                    <a href="{{ url('public/uploads/product_upload/grn_license_sample_format.csv') }}"
                                        target="_blank">Download sample file</a>.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-light" onclick="return excel_license_key()">Import</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Excel Quote-->


    <script>
        function set_license_key(el, e) {
            var key = e.which || e.keyCode;
            if (key !== 13) {
                return true;
            }
            var $row = $(el).closest("tr");
            var pt = $row.find('input[name="product_type[]"]').first().val();
            if (parseInt(String(pt == null ? '' : pt).trim(), 10) === 2) {
                $('#item_id').val($row.find('select[name="part_number[]"]').val());
                $('#license_row_index').val($('#myTable > tbody > tr').index($row));
                $("#ModalLabelHeading").text($row.find('select[name="part_number[]"] option:selected').text());
                $("#license_qty").val($(el).val());
                $("#btn_ModalLicenseKey").click();
                view_license_key();
                e.preventDefault();
                return false;
            }
            return true;
        }

        function set_license_key_po(rowid, producttype) {
            $('#qty_' + rowid).keypress(function(e) {
                var key = e.which;
                if (key === 13) { //the enter key code
                    var pt = producttype;
                    if (parseInt(String(pt == null ? '' : pt).trim(), 10) === 2) {
                        var $targetRow = $('#qty_' + rowid).closest('tr');
                        $('#item_id').val($('#part_id_' + rowid).val());
                        $('#license_row_index').val($targetRow.length ? $('#myTable > tbody > tr').index($targetRow) : '');
                        $('#ModalLabelHeading').text($('#part_number_' + rowid).val());
                        $('#license_qty').val($('#qty_' + rowid).val())
                        $('#btn_ModalLicenseKey').click();
                        view_license_key();
                    }
                    return true;
                }
            });
        }


        function showLicenseKeyMessage(message, type = 'danger') {
            var $msg = $('#licenseKeyMessage');
            $msg.removeClass('text-danger text-warning text-success');
            if (!message) {
                $msg.hide();
                return;
            }
            $msg
                .text(message)
                .addClass(type === 'success' ? 'text-success' : type === 'warning' ? 'text-warning' : 'text-danger')
                .show();
        }

        function getLicenseQty() {
            var qty = parseInt($('#license_qty').val(), 10);
            return isNaN(qty) ? 0 : qty;
        }

        function normalizeLicenseDateForStore(value) {
            var raw = (value || '').toString().trim();
            if (!raw || raw === '0000-00-00') {
                return '';
            }

            if (/^\d{4}-\d{2}-\d{2}$/.test(raw)) {
                return raw;
            }

            var normalized = raw.replace(/\./g, '/').replace(/-/g, '/');
            var parts = normalized.split('/');
            if (parts.length !== 3) {
                return '';
            }

            var day = parts[0].padStart(2, '0');
            var month = parts[1].padStart(2, '0');
            var year = parts[2];
            if (year.length === 2) {
                year = '20' + year;
            }

            if (!/^\d{4}$/.test(year) || !/^\d{2}$/.test(month) || !/^\d{2}$/.test(day)) {
                return '';
            }

            return year + '-' + month + '-' + day;
        }

        function formatLicenseDateForDisplay(value) {
            var ymd = normalizeLicenseDateForStore(value);
            if (!ymd) {
                return '';
            }
            var parts = ymd.split('-');
            return parts[2] + '/' + parts[1] + '/' + parts[0];
        }

        function dateForInput(value) {
            return formatLicenseDateForDisplay(value);
        }

        let grnLicenseDrafts = [];
        let grnDraftCurrentItemId = null;

        function getDraftRowId(row, index) {
            return String(row.local_id || row.id || 'draft-' + index);
        }

        function setLicenseAddButtonMode(mode) {
            if (mode === 'update') {
                $('#license_add').html('<i class="ico icon-outline-pen-2 me-1"></i>Update');
                return;
            }
            $('#license_add').html('<i class="ico icon-outline-add-square text-success me-1"></i>Add');
        }

        function getActiveLicenseTargetRow(itemId) {
            var $rows = $('#myTable > tbody > tr');
            var rowIndex = parseInt($('#license_row_index').val(), 10);
            if (!isNaN(rowIndex) && rowIndex >= 0 && rowIndex < $rows.length) {
                var $byIdx = $rows.eq(rowIndex);
                if ($byIdx.length) {
                    if (!itemId || String($byIdx.find('select[name="part_number[]"]').val()) === String(itemId)) {
                        return $byIdx;
                    }
                }
            }
            var $matches = $rows.filter(function() {
                return String($(this).find('select[name="part_number[]"]').val()) === String(itemId);
            });
            if ($matches.length === 0) {
                return $();
            }
            if ($matches.length === 1) {
                return $matches.first();
            }
            if (!isNaN(rowIndex) && rowIndex >= 0 && rowIndex < $rows.length) {
                var $cand = $rows.eq(rowIndex);
                if ($cand.length && $matches.toArray().indexOf($cand[0]) !== -1) {
                    return $cand;
                }
            }
            return $matches.first();
        }

        function getCommaSeparatedLicenseKeys(rows) {
            var seen = {};
            return (rows || []).map(function(row) {
                    return (row.license_key || '').toString().trim();
                })
                .filter(function(key) {
                    if (!key) {
                        return false;
                    }
                    var normalized = key.toLowerCase();
                    if (seen[normalized]) {
                        return false;
                    }
                    seen[normalized] = true;
                    return true;
                });
        }

        function parseGrnLineQty($row) {
            var raw = ($row.find('input[name="qty[]"]').val() || '').toString().replace(/,/g, '').trim();
            var n = parseFloat(raw);
            return isNaN(n) ? 0 : n;
        }

        function getLicenseKeyTokensFromSerial(serialVal) {
            var seen = {};
            var keys = [];
            (serialVal || '').toString().split(',').forEach(function(part) {
                var k = part.trim();
                if (!k) {
                    return;
                }
                var nk = k.toLowerCase();
                if (seen[nk]) {
                    return;
                }
                seen[nk] = true;
                keys.push(k);
            });
            return keys;
        }

        function isGrnLicenseProductType(pt) {
            return parseInt(String(pt == null ? '' : pt).trim(), 10) === 2;
        }

        function applyLicenseQtyHighlightForRow($row, keyCountOverride) {
            if (!$row || !$row.length) {
                return;
            }
            var $qty = $row.find('input[name="qty[]"]');
            var rawPt = $row.find('input[name="product_type[]"]').first().val();
            if (!isGrnLicenseProductType(rawPt)) {
                $qty.css('color', '');
                return;
            }
            var lineQty = parseGrnLineQty($row);
            var keyCount;
            if (typeof keyCountOverride === 'number' && !isNaN(keyCountOverride)) {
                keyCount = keyCountOverride;
            } else {
                keyCount = getLicenseKeyTokensFromSerial($row.find('input[name="serial_no[]"]').val()).length;
            }
            if (lineQty > 0 && keyCount < lineQty) {
                $qty.css('color', '#dc3545');
            } else {
                $qty.css('color', '');
            }
        }

        function applyLicenseKeysToSerialInput(itemId, rows) {
            var $targetRow = getActiveLicenseTargetRow(itemId);
            if (!$targetRow.length) {
                return;
            }
            var serialText = getCommaSeparatedLicenseKeys(rows).join(', ');
            $targetRow.find('input[name="serial_no[]"]').val(serialText);
        }

        function setDraftLicenseRows(itemId, rows) {
            grnDraftCurrentItemId = itemId;
            grnLicenseDrafts = (rows || []).map(function(row, index) {
                return {
                    local_id: String(row.local_id || row.id || 'draft-' + index + '-' + Math.random().toString(36)
                        .substr(2, 5)),
                    id: row.id || null,
                    license_key: (row.license_key || '').toString().trim(),
                    exp_date: normalizeLicenseDateForStore(row.exp_date || ''),
                };
            });
            cancel_license_edit();
            renderLicenseRows(grnLicenseDrafts);
        }

        function getExistingLicenseKeys() {
            return grnLicenseDrafts
                .map(function(row) {
                    return (row.license_key || '').toString().trim().toLowerCase();
                })
                .filter(Boolean);
        }

        function updateLicenseAddState() {
            var maxQty = getLicenseQty();
            var currentCount = getExistingLicenseKeys().length;
            $('#license_add').prop('disabled', maxQty <= 0 || currentCount >= maxQty);
            $('#licenseCountSummary').text('Selected: ' + currentCount + ' of ' + maxQty);
        }

        function renderLicenseRows(rows) {
            var maxQty = getLicenseQty();
            var seen = {};
            var duplicates = [];
            var getSelectedRows = '';
            var uniqueCount = 0;

            rows = rows || [];
            rows.forEach(function(row, index) {
                var licenseKey = (row.license_key || '').toString().trim();
                if (!licenseKey) {
                    return;
                }
                var normalized = licenseKey.toLowerCase();
                if (seen[normalized]) {
                    duplicates.push(licenseKey);
                    return;
                }
                seen[normalized] = true;
                uniqueCount += 1;
                var safeKey = $('<div>').text(licenseKey).html();
                var rowId = getDraftRowId(row, index);
                var formattedExpDate = formatLicenseDateForDisplay(row.exp_date);
                getSelectedRows += '<tr data-local-id="' + rowId + '" data-exp-date="' + $('<div>').text(row
                        .exp_date || '').html() + '">' +
                    '<td>' + uniqueCount + '</td>' +
                    '<td>' + safeKey + '</td>' +
                    '<td>' + formattedExpDate + '</td>' +
                    '<td style="white-space:nowrap;">' +
                    '<a onclick="edit_license_key_mode(\'' + rowId +
                    '\', this)" class="btn-sm btn-light me-1" title="Edit"><i class="ico icon-outline-pen-2"></i></a>' +
                    '<a onclick="delete_license_key(\'' + rowId +
                    '\')" class="btn-sm btn-light" title="Delete"><i class="ico icon-outline-trash-bin-trash"></i></a>' +
                    '</td>' +
                    '</tr>';
            });

            if (uniqueCount === 0) {
                getSelectedRows = '<tr><td colspan="4" class="text-center text-muted">No keys added.</td></tr>';
            }

            $('#lk-table tbody').empty().append(getSelectedRows);
            if (duplicates.length) {
                showLicenseKeyMessage('Duplicate license keys were ignored: ' + duplicates.join(', '), 'warning');
            } else {
                showLicenseKeyMessage('');
            }
            updateLicenseAddState();
            if ($('#ModalLicenseKey').hasClass('show')) {
                applyLicenseQtyHighlightForRow(getActiveLicenseTargetRow($('#item_id').val()), uniqueCount);
            }
        }

        function findDraftRowIndex(localId) {
            return grnLicenseDrafts.findIndex(function(row, index) {
                return getDraftRowId(row, index) === localId;
            });
        }

        function edit_license_key_mode(localId, btn) {
            var index = findDraftRowIndex(localId);
            if (index === -1) {
                return;
            }
            var row = grnLicenseDrafts[index];
            $('#edit_license_id').val(localId);
            $('#license_key').val(row.license_key).focus();
            $('#exp_date').val(dateForInput(row.exp_date));
            setLicenseAddButtonMode('update');
            $('#license_cancel_edit').show();
            $('#lk-table tbody tr').removeClass('table-warning');
            $(btn).closest('tr').addClass('table-warning');
        }

        function cancel_license_edit() {
            $('#edit_license_id').val('');
            $('#license_key').val('');
            $('#exp_date').val('');
            setLicenseAddButtonMode('add');
            $('#license_cancel_edit').hide();
            $('#lk-table tbody tr').removeClass('table-warning');
        }

        function canAddLicenseKey(newKey, skipDuplicateCheck) {
            var maxQty = getLicenseQty();
            var currentCount = getExistingLicenseKeys().length;
            if (maxQty <= 0) {
                showLicenseKeyMessage('License quantity must be greater than zero.', 'danger');
                return false;
            }
            if (currentCount >= maxQty) {
                showLicenseKeyMessage('Cannot add more than ' + maxQty + ' license keys.', 'danger');
                return false;
            }
            if (!newKey) {
                showLicenseKeyMessage('Enter a license key.', 'danger');
                return false;
            }
            if (!skipDuplicateCheck && getExistingLicenseKeys().indexOf(newKey.toLowerCase()) !== -1) {
                showLicenseKeyMessage('This license key has already been added.', 'danger');
                return false;
            }
            return true;
        }

        function add_license_key() {
            $("#loading_bg").css("display", "block");
            showLicenseKeyMessage('');

            var licenseKey = ($('#license_key').val() || '').toString().trim();
            var expDate = normalizeLicenseDateForStore($('#exp_date').val());
            var maxQty = getLicenseQty();
            var editId = $('#edit_license_id').val();

            if (!licenseKey) {
                $('#license_key').focus();
                showLicenseKeyMessage('Enter a license key.', 'danger');
                $("#loading_bg").css("display", "none");
                return false;
            }

            if (editId) {
                var editIndex = findDraftRowIndex(editId);
                if (editIndex === -1) {
                    showLicenseKeyMessage('Unable to find the selected license key for update.', 'danger');
                    $("#loading_bg").css("display", "none");
                    return false;
                }
                if (!canAddLicenseKey(licenseKey, true)) {
                    $("#loading_bg").css("display", "none");
                    return false;
                }

                var existingIndex = getExistingLicenseKeys().indexOf(licenseKey.toLowerCase());
                if (existingIndex !== -1 && grnLicenseDrafts[existingIndex] && getDraftRowId(grnLicenseDrafts[
                        existingIndex], existingIndex) !== editId) {
                    showLicenseKeyMessage('This license key has already been added.', 'danger');
                    $("#loading_bg").css("display", "none");
                    return false;
                }

                grnLicenseDrafts[editIndex].license_key = licenseKey;
                grnLicenseDrafts[editIndex].exp_date = expDate;
                cancel_license_edit();
                renderLicenseRows(grnLicenseDrafts);
                $("#loading_bg").css("display", "none");
                return false;
            }

            if (!canAddLicenseKey(licenseKey)) {
                $("#loading_bg").css("display", "none");
                return false;
            }

            if (getExistingLicenseKeys().length + 1 > maxQty) {
                showLicenseKeyMessage('Adding this license would exceed the allowed quantity of ' + maxQty + '.', 'danger');
                $("#loading_bg").css("display", "none");
                return false;
            }

            grnLicenseDrafts.push({
                local_id: 'draft-' + Date.now() + '-' + Math.random().toString(36).substr(2, 5),
                license_key: licenseKey,
                exp_date: expDate,
            });

            $('#license_key').val('');
            $('#exp_date').val('');
            renderLicenseRows(grnLicenseDrafts);
            $("#loading_bg").css("display", "none");
            return false;
        }

        function excel_license_key() {
            $("#loading_bg").css("display", "block");
            showLicenseKeyMessage('');

            var maxQty = getLicenseQty();
            var itemId = $('#item_id').val();
            var fileInput = $('#import_file')[0];

            if (!itemId) {
                showLicenseKeyMessage('Select a product before importing license keys.', 'danger');
                $("#loading_bg").css("display", "none");
                return false;
            }

            if (maxQty <= 0) {
                showLicenseKeyMessage('License quantity must be greater than zero before importing.', 'danger');
                $("#loading_bg").css("display", "none");
                return false;
            }

            if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
                $('#import_file').focus();
                showLicenseKeyMessage('Select a valid CSV or Excel file to import.', 'danger');
                $("#loading_bg").css("display", "none");
                return false;
            }

            var fileName = fileInput.files[0].name.toLowerCase();
            var allowedExtensions = ['csv', 'xls', 'xlsx'];
            var extension = fileName.split('.').pop();
            if ($.inArray(extension, allowedExtensions) === -1) {
                showLicenseKeyMessage('Unsupported file type. Use .csv, .xls, or .xlsx.', 'danger');
                $("#loading_bg").css("display", "none");
                return false;
            }

            var action = "{{ URL::to('add-grn-license-key-cart-excel') }}";
            var formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('item_id', itemId);
            formData.append('license_qty', maxQty);
            formData.append('import_file', fileInput.files[0]);
            formData.append('context', 'grn');

            $.ajax({
                url: action,
                type: "POST",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(dataResult) {
                    try {
                        var response = typeof dataResult === 'string' ? JSON.parse(dataResult) : dataResult;
                        if (response.error) {
                            showLicenseKeyMessage(response.error, 'danger');
                            return;
                        }

                        var currentKeys = getExistingLicenseKeys();
                        var duplicates = [];
                        var addedCount = 0;
                        (response.data || []).forEach(function(row) {
                            var key = (row.license_key || '').toString().trim();
                            if (!key) {
                                return;
                            }
                            if (currentKeys.indexOf(key.toLowerCase()) !== -1) {
                                duplicates.push(key);
                                return;
                            }
                            if (getExistingLicenseKeys().length + 1 > maxQty) {
                                return;
                            }
                            grnLicenseDrafts.push({
                                local_id: 'draft-' + Date.now() + '-' + Math.random().toString(
                                    36).substr(2, 5),
                                license_key: key,
                                exp_date: normalizeLicenseDateForStore(row.exp_date || ''),
                            });
                            addedCount++;
                        });

                        renderLicenseRows(grnLicenseDrafts);
                        applyLicenseQtyHighlightForRow(getActiveLicenseTargetRow($('#item_id').val()), getExistingLicenseKeys().length);
                        $('#license_key').val('');
                        $('#exp_date').val('');
                        $('#import_file').val('');
                        $('#ModalExcelQuote').modal('hide');

                        if (duplicates.length) {
                            showLicenseKeyMessage(
                                'Imported keys saved in the draft list. Duplicate entries were skipped: ' +
                                duplicates.join(', '), 'warning');
                        } else {
                            showLicenseKeyMessage('Imported license keys added to the draft list.', 'success');
                        }
                    } catch (err) {
                        showLicenseKeyMessage('Unable to import license keys. Please try again.', 'danger');
                    }
                },
                error: function() {
                    showLicenseKeyMessage('Unable to import license keys. Please try again.', 'danger');
                },
                complete: function() {
                    $("#loading_bg").css("display", "none");
                }
            });
        }

        function save_license_keys() {
            $("#loading_bg").css("display", "block");
            showLicenseKeyMessage('');

            var itemId = $('#item_id').val();
            var maxQty = getLicenseQty();

            if (!itemId) {
                showLicenseKeyMessage('Select a product before saving license keys.', 'danger');
                $("#loading_bg").css("display", "none");
                return false;
            }

            if (grnLicenseDrafts.length > maxQty) {
                showLicenseKeyMessage('Cannot save more than the allowed quantity of ' + maxQty + '.', 'danger');
                $("#loading_bg").css("display", "none");
                return false;
            }

            var action = "{{ URL::to('add-grn-license-key-cart') }}";
            var rows = grnLicenseDrafts.map(function(row) {
                return {
                    license_key: row.license_key,
                    exp_date: normalizeLicenseDateForStore(row.exp_date),
                };
            });

            $.ajax({
                url: action,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    item_id: itemId,
                    license_qty: maxQty,
                    rows: JSON.stringify(rows),
                    context: 'grn',
                },
                cache: false,
                success: function(dataResult) {
                    try {
                        var response = typeof dataResult === 'string' ? JSON.parse(dataResult) : dataResult;
                        if (response.error) {
                            showLicenseKeyMessage(response.error, 'danger');
                            return;
                        }
                        if (response.duplicate || (response.duplicate_keys && response.duplicate_keys.length)) {
                            var duplicateText = response.message || ('Duplicate license keys were skipped: ' + (response.duplicate_keys || []).join(', '));
                            showLicenseKeyMessage(duplicateText, 'warning');
                            toastr.warning(duplicateText);
                        }

                        setDraftLicenseRows(itemId, response.data || []);
                        applyLicenseKeysToSerialInput(itemId, response.data || []);
                        var $tgt = getActiveLicenseTargetRow(itemId);
                        var savedCount = getCommaSeparatedLicenseKeys(response.data || []).length;
                        var lineQty = parseGrnLineQty($tgt);
                        applyLicenseQtyHighlightForRow($tgt, savedCount);
                        if (lineQty > 0 && savedCount < lineQty) {
                            toastr.warning('All qty license keys are not added. Added ' + savedCount + ' of ' + lineQty + '.');
                        }
                        $('#ModalLicenseKey').modal('hide');
                    } catch (err) {
                        showLicenseKeyMessage('Unable to save license keys. Please try again.', 'danger');
                    }
                },
                error: function() {
                    showLicenseKeyMessage('Unable to save license keys. Please try again.', 'danger');
                },
                complete: function() {
                    $("#loading_bg").css("display", "none");
                }
            });
            return false;
        }

        function view_license_key() {
            $("#loading_bg").css("display", "block");
            showLicenseKeyMessage('');
            var action = "{{ URL::to('view-grn-license-key-cart') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    item_id: $('#item_id').val(),
                    context: 'grn',
                },
                cache: false,
                success: function(dataResult) {
                    try {
                        var response = typeof dataResult === 'string' ? JSON.parse(dataResult) : dataResult;
                        setDraftLicenseRows($('#item_id').val(), response.data || []);
                        var cnt = getCommaSeparatedLicenseKeys(response.data || []).length;
                        applyLicenseQtyHighlightForRow(getActiveLicenseTargetRow($('#item_id').val()), cnt);
                    } catch (err) {
                        showLicenseKeyMessage('Unable to load current license keys.', 'danger');
                    }
                },
                error: function() {
                    showLicenseKeyMessage('Unable to load current license keys.', 'danger');
                },
                complete: function() {
                    $("#loading_bg").css("display", "none");
                }
            });
        }

        function delete_license_key(id) {
            var index = findDraftRowIndex(id);
            if (index === -1) {
                showLicenseKeyMessage('Unable to remove this license key.', 'danger');
                return;
            }
            grnLicenseDrafts.splice(index, 1);
            renderLicenseRows(grnLicenseDrafts);
        }

        $(function() {
            $('#myTable > tbody > tr').each(function() {
                applyLicenseQtyHighlightForRow($(this));
            });
            $(document).on('change', '#myTable tbody input[name="qty[]"]', function() {
                applyLicenseQtyHighlightForRow($(this).closest('tr'));
            });
            $(document).on('change input', '#myTable tbody input[name="serial_no[]"]', function() {
                applyLicenseQtyHighlightForRow($(this).closest('tr'));
            });
        });
    </script>
    <!-- Modal License Key-->

    <script>
        (function() {

            let dragging = false;
            let startX, startY, startLeft, startTop;
            let currentModal = null;

            // Bind drag start
            $(document).on('mousedown', '.modal-dialog.draggable .modal-header', function(e) {
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
            $(document).on('mousemove', function(e) {
                if (!dragging || !currentModal) return;

                let newLeft = startLeft + (e.clientX - startX);
                let newTop = startTop + (e.clientY - startY);

                currentModal.offset({
                    top: newTop,
                    left: newLeft
                });
            });

            // Stop drag
            $(document).on('mouseup', function() {
                dragging = false;
                $('body').removeClass('unselectable');
            });

            // Reset modal on open (production behavior)
            $(document).on('show.bs.modal', '.modal', function() {
                let dialog = $(this).find('.modal-dialog.draggable');
                dialog.css({
                    top: '10%',
                    left: '65%',
                    transform: 'translateX(-50%)'
                });
            });

        })();
    </script>

    <script src="{{ asset('public/js/form-validation-toastr.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize form validation for crm-deals-form
            FormValidator.init('goods-receipt-note-store-form', {
                showAllErrors: true,
                scrollToFirst: true,
                highlightFields: true,
                toastrPosition: 'toast-top-right',
                toastrTimeout: 6000
            });
        });
    </script>


    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

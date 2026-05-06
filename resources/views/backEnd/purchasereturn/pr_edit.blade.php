    <?php try { ?>

            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'purchase-return-update/'. @$edit->id, 'method' => 'PUT', 'enctype' => 'multipart/form-data', 'id' => 'purchase-return-update-form']) }}

            <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
            <input type="hidden" name="id" id="pr_id" value="{{ isset($edit) ? $edit->id : '' }}">
            <input type="hidden" name="net_vat" id="net_vat" value="{{ @$editList[0]->vat }}">
            <input type="hidden" name="doc_number_main" id="doc_number_main" value="{{ $edit->doc_number }}">
            



    <div class="purchase-order-content-header">
        <h4 class="purchase-order-content-header-left">
            Edit - {{ @$edit->doc_number }}
        </h4>
        <div class="purchase-order-content-header-right">
            <a type="submit" class="btn btn-light text-dark" href="{{url('purchase-return/'.$edit->id.'?pr_action=add')}}">
                <i class="ico icon-outline-add-square text-success"></i> Add
            </a>
            <button type="submit" class="btn btn-light">
                <i class="ico icon-outline-bookmark-square text-warning"></i> Update
            </button>
             <div class="dropdown">
                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ico icon-outline-hamburger-menu"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{url('purchase-return/'.$edit->id.'/delete')}}"><i class="ico icon-outline-trash-bin-minimalistic text-danger"></i> Cancel PR</a></li>
                    <li><a class="dropdown-item" href="{{url('purchase-return/'.$edit->id.'/download')}}"><i class="ico icon-outline-document-medicine text-success"></i> Download</a></li>
                    <li><button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#adjustmentModal"><i class="ico icon-outline-calculator-minimalistic text-warning"></i> Adjustment</button></li>
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
                                                <select class="form-control " name="vendors" id="vendors" onchange="get_pending_po_list()">
                                                <option value=""></option>
                                                @foreach ($vendors as $value)
                                                    <option value="{{ @$value->id }}" @if(isset($grn) && $edit->vendors == $value->id) selected @endif>
                                                        {{ @$value->account_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                    


                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <label class="form-label">Return Number</label>
                                            <div class="form-group">
                                                <input
                                class="form-control"
                                type="text" name="doc_number" autocomplete="off" id="doc_number"
                                value="{{ @$edit->doc_number }}"
                                readonly>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <label class="form-label">Return Date</label>
                                            <div class="form-group">
                                           @php
    $value = !empty($edit->doc_date) ? \Carbon\Carbon::parse($edit->doc_date)->format('d/m/Y') : '';
@endphp
                                            <input class="form-control date-picker" id="doc_date" type="text" autocomplete="off" name="doc_date" value="{{ @$value }}">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <label class="form-label">Currency<a style="float: right;" data-bs-target="#ModalChangeCurrancy" data-bs-toggle="modal"><i class="ico icon-outline-pen-2"></i></a></label>
                                            <div class="form-group"><select
                                class="form-control js-example-basic-single"
                                name="currency" id="currency">
                                {{-- <option data-display="@lang('Currency') *" value="">@lang('Currency') *</option> --}}
                                @foreach ($currency as $value)
                                    <option value="{{ @$value->id }}"
                                    @if($edit->currency_id == $value->id) selected @endif>
                                        {{ @$value->code }}
                                    </option>
                                @endforeach
                            </select>
                            <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>

                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <label class="form-label">Created By</label>
                                            <input
                                    class="form-control"
                                    type="text" name="createdby" autocomplete="off" id="createdby"
                                    value="{{$edit->createdby->full_name }}"
                                    readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-wrap mb-3">
                                <ul class="nav nav-tabs" id="purchaseDetailsTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="extra-fields-tab" data-bs-toggle="tab" data-bs-target="#extra-fields" type="button" role="tab" aria-controls="extra-fields" aria-selected="true">Extra Fields</button>
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


                <div class="col-2 mb-2">
                    <div class="input-effect">
                        <label class="txtlbl">Pending list</label>
                        <div id="plist" style="width: 100%; height: 130px; border: solid 1px #dfe1d7; border-radius: 5px; padding: 5px 5px; overflow-y: scroll;"></div>
                        <a data-modal-size="modal-md" data-target="#pi_pending_popup_win" id="addPIPending" data-toggle="modal"></a>
                        <input type="hidden" id="hd_pending_pi_id" name="hd_pending_pi_id">
                        <input type="hidden" id="pi_id" name="pi_id" value="{{ @$edit->pi_id }}">
                        <input type="hidden" id="vat_percentage" name="vat_percentage" value="5">
                    </div>
                </div>    
                <div class="col-10 mb-2">
                    <div class="row gap-rows">

                        <div class="col-2">
                            <label class="form-label">PIV Number</label>
                            <div class="form-group">
                                <input class="form-control" type="text" name="pi_number" autocomplete="off" id="pi_number" value="{{ @$edit->pi_number }}" readonly>
                            </div>
                        </div>
                        <div class="col-2">
                            <label class="form-label">PIV Date</label>
                            <div class="form-group">
                                   @php
    $value = !empty($edit->pi_date) ? \Carbon\Carbon::parse($edit->pi_date)->format('d/m/Y') : '';
@endphp

                                <input class="form-control date-picker" id="pi_date" type="text" autocomplete="off" name="pi_date" value="{{ @$value }}" style="margin-top: 0px" readonly>
                            </div>
                        </div>
                         <div class="col-2">
                                                <label class="form-label">Bill Number</label>
                                                <div class="form-group">
                                <input
                                    class="form-control"
                                    type="text" name="bill_number" autocomplete="off" id="bill_number"
                                    value="{{ isset($edit) ? (!empty(@$edit->bill_number) ? @$edit->bill_number : old('bill_number')) : '' }}">
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                   @php
    $value = !empty($edit->bill_date) ? \Carbon\Carbon::parse($edit->bill_date)->format('d/m/Y') : '';
@endphp
                                                <label class="form-label">Bill Date</label>
                                                <div class="form-group">
                                <input class="form-control date-picker" id="bill_date" type="text" autocomplete="off"
                                    name="bill_date" value="{{ @$value}}" required >
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
                            <div class="form-group">
                                @php
    $value = !empty($edit->lpo_date) ? \Carbon\Carbon::parse($edit->lpo_date)->format('d/m/Y') : '';
@endphp
                                <input class="form-control date-picker" id="lpo_date" type="text" autocomplete="off" name="lpo_date" value="{{ @$value }}" style="margin-top:0px;">
                            </div>
                        </div>
                                            <div class="col-2">
                                                <label class="form-label">Payment Terms:</label>
                                                <div class="form-group">
                                                    <select class="form-control js-example-basic-single" name="payment_terms" id="payment_terms"  required>
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
                                        type="text" name="payment_terms2" autocomplete="off" id="payment_terms2"
                                        value="{{ isset($edit) ? (!empty(@$edit->payment_terms2) ? @$edit->payment_terms2 : old('payment_terms2')) : '' }}">
                                </div>
                            </div>
                                            </div>
                                           
                                            <div class="col-2">
                                                <label class="form-label">AWB No</label>
                                                <div class="form-group">
                                <input class="txtbx primary-input form-control {{ $errors->has('awbno') ? ' is-invalid' : '' }}"
                                    type="text" name="awbno" autocomplete="off"
                                    value="{{ isset($edit) ? (!empty(@$edit->awbno) ? @$edit->awbno : old('awbno')) : old('awbno') }}"
                                    id="awbno">
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <label class="form-label">Warehouse</label>
                                                <div class="form-group">

                                                                        @php
                                        $warehouses = App\SysHelper::getCompanyWarehouses();
                                        @endphp

                                         <select class="form-control js-example-basic-single" required name="warehouse" id="warehouse">
                                       
                                        @foreach ($warehouses as $value)
                                            <option value="{{ @$value->id }}" @if (@$edit->warehouse == $value->id) selected
                                                
                                            @endif>{{ @$value->warehouse_name }}</option>
                                        @endforeach
                                        
                                    </select>
                                <!-- <input
                                    class="form-control"
                                    type="text" name="warehouse" autocomplete="off"
                                    value="{{ isset($edit) ? (!empty(@$edit->warehouse) ? @$edit->warehouse : old('warehouse')) : old('warehouse') }}"
                                    id="warehouse"> -->
                                                </div>
                                            </div>
                                            
                              <div class="col-2">
                                <label class="form-label">Customer Reference</label>

                                             @php
$selectedCompanies = $edit->ref_company_id
    ? explode(',', $edit->ref_company_id)
    : [];

$selectedCompanyNames = [];
foreach ($customer_reference_list as $company) {
    if (in_array($company->id, $selectedCompanies)) {
        $selectedCompanyNames[] = $company->name;
    }
}
@endphp


  <input class="form-control" type="text" name="customer_reference_input"
                            autocomplete="off" id="customer_reference_input" readonly value="{{ implode(', ', $selectedCompanyNames) }}">

                        <!-- Hidden container to hold actual selected IDs for form submission -->
                        <div id="ref_company_hidden_inputs" style="display:none;">
                            @foreach ($selectedCompanies as $ScompanyId)
                                <input type="hidden" name="ref_company_id[]" value="{{ $ScompanyId }}">
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
                                      
                                            @foreach ($customer_reference_list as $value)
                                                <option value="{{ @$value->id }}">{{ @$value->name }} @if(@App\SysHelper::getCompanyCodeSettings()['is_customer_code']) ({{ @$value->code }}) @endif</option>
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

                                <div class="form-group">

                                


                                    <!-- <select class="form-control js-example-basic-single" name="ref_company_id" id="ref_company_id" required>
                                        <option value="">-Select-</option>
                                        @foreach ($customer_reference_list as $value)
                                        <option value="{{ @$value->id }}" @if(@$edit->ref_company_id == @$value->id) selected @endif >{{ @$value->name }} 
                                            @if(@App\SysHelper::getCompanyCodeSettings()['is_customer_code'])
                                                    ({{ @$value->code }})
                                                    @endif
                                        </option>
                                        @endforeach
                                    </select> -->
                                    <input class="form-control" type="hidden" name="reference" autocomplete="off"
                                        value="{{ isset($edit) ? (!empty(@$edit->reference) ? @$edit->reference : old('reference')) : old('reference') }}"
                                        id="reference">
                                </div>
                            </div>
                                            <div class="col-2">
                                                <label class="form-label">Deal Id</label>
                                                <div class="form-group">
                                <input
                                    class="form-control"
                                    type="text" name="deal_id" autocomplete="off" id="deal_id"
                                    value="{{ @App\SysHelper::get_code_from_dealid($edit->deal_id) }}">
                                                </div>
                                            </div>

                                            
                                            <div class="col-2">
                                                <label class="form-label">Sales Person</label>
                                                  <!-- <select class="form-control js-example-basic-single" required name="sales_person" id="sales_person">
                                    <option value=""></option>
                                    @foreach ($salesman as $value)
                                        <option value="{{ @$value->user_id }}" @if($edit->sales_person==$value->user_id) selected @endif>{{ @$value->full_name }}</option>
                                    @endforeach
                                </select>
                                </select> -->

                                <select class="form-control js-example-basic-single" required name="sales_person" id="sales_person">
                                    <option value=""></option>
                                    @foreach ($salesman as $value)
                                        <option value="{{ @$value->user_id }}" @if($edit->sales_person == $value->user_id) selected @endif>{{ @$value->full_name }}</option>
                                    @endforeach

                                    {{-- If the PO has a sales_person set but it's not in $salesman, try to fetch and append it so it can be selected --}}
                                    @if(isset($edit) && $edit->sales_person)
                                        @php
                                            $selectedId = $edit->sales_person;
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
                                    @elseif(isset($edit) && !is_null($edit->sales_person_name) && $edit->sales_person_name !== '')
                                        {{-- If sales_person_name is set (manual entry), append it as a manual option --}}
                                        <option value="{{ $edit->sales_person_name }}" selected>{{ $edit->sales_person_name }}</option>
                                    
                                    @endif
                                <option value="OTH">Other</option>
                                </select>

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
                                                <label class="form-label">Debit Note</label>
                                                <div class="form-group">
                               

                                     <div class="form-group">
                                  
                                         <select class="form-control js-example-basic-single" required name="debit_note" id="debit_note">
                                       
                           
                                            <option value="DN" @if ($edit->debit_note == "DN") selected @endif>Debit Note</option>
                                            <option value="PR" @if ($edit->debit_note == "PR") selected @endif>Purchase Return</option>
                                  
                                        
                                    </select>
                                </div>

                                                </div>
                                            </div>

                                            <div class="col">
                                                <label class="form-label">Remarks</label>
                                                <div class="form-group">
                                 <input
                                    class="form-control"
                                    type="text" name="narration" autocomplete="off" id="narration"
                                    value="{{ isset($edit) ? (!empty(@$edit->narration) ? @$edit->narration : old('narration')) : '' }}">
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
                                <option value=""></option>
                                @foreach ($customer as $value)
                                    @php $s = @App\SysHelper::internal_transfer_customer_id(@$value->id, session('logged_session_data.company_id')); @endphp
                                    
                                    <option value="{{ @$value->id }}" {{ $s }} @if($edit->shipping_supplier == $value->id) selected @endif>
                                        {{ @$value->account_name }} 
                                        @if(@App\SysHelper::getCompanyCodeSettings()['is_customer_code'])
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
                                value="{{ $edit->shipping_name }}" />
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Contact Email</label>
                        <div class="form-group">
                            <input type="text" class="form-control" name="shipping_email" id="shipping_email"
                                value="{{ $edit->shipping_email }}" />
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Contact No</label>
                        <div class="form-group">
                            <input type="text" class="form-control" name="shipping_contact_no"
                                id="shipping_contact_no" value="{{ $edit->shipping_contact_no }}" />
                        </div>
                    </div>
                    <div class="col-3">
                        <label class="form-label">Shipping Address</label>
                        <div class="form-group">
                            <input type="text" class="form-control" name="shipping_address_1"
                                id="shipping_address_1" value="{{ $edit->shipping_address_1 }}" />
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
                         <select class="form-control js-example-basic-single" name="supplier_state" id="state">
                            <option value=""></option>

                            @foreach ($states as $value)
                                <option 
                                    value="{{ $value->id }}" @if( $edit->supplier_state == $value->id) selected @endif>
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
                          
                           <input class="form-control" type="number"  name="vat_percent" id="vat_percent" value="{{ @$edit->vat_percent }}">
                        </div>
                    </div>

                    
                         <div class="col">
                        <label class="form-label">VAT Number</label>
                        <div class="form-group">
                          
                           <input class="form-control" type="number"  name="vat_number" id="vat_number" value="{{ @$edit->vat_number }}">
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
                                class="form-control  js-example-basic-single  {{ $errors->has('purchase_type') ? ' is-invalid' : '' }}"
                                id="inputVendorName">

                                @foreach ($purchasetype as $value)
                                    <option value="{{ @$value->id }}"
                                        {{ isset($edit) ? (!empty(@$edit->purchase_type) ? (@$edit->purchase_type == @$value->id ? 'selected' : '') : '') : '' }}>
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
                                            <th class="resizable text-center" width="50px">@lang('No')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="150px">@lang('Part No') <a class="icon icon-outline-book text-dark" data-bs-toggle="modal" data-bs-target="#addproductModal"></a><div class="resizer"></div></th>
                                            <th class="resizable text-center" width="50px">@lang('Description')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="50px">@lang('Tax')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="50px">@lang('Qty')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="100px">@lang('Price')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="100px">@lang('Value')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="80px" scope="col" >Dis <a class="icon icon-outline-book text-dark" data-bs-toggle="modal" data-bs-target="#discountModal"></a><div class="resizer"></div></th>
                                            <th class="resizable text-center" width="100px">@lang('Taxable')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="100px">@lang('VAT')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="100px">@lang('Total')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="100px">@lang('SRL No')<div class="resizer"></div></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($editList) && count($editList) > 0)
                                         @php $i=1; $po_qty=0; $qty=0; $executed_qty=0; $balance_qty=0; $unitprice=0; $value=0; $discount=0; $taxableamount = 0; $vatamount = 0; $total = 0; $grn_qty=0; @endphp
                    @if (count($editList)>0)
                        @foreach ($editList as $items)
                        <tr>
{{-- 
                            <td><input type="number" class="form-control" name="sort_id[]" id="sort_id_{{ $i }}" value="{{ $list->sort_id }}" /></td>
                                <td><input type="text" class="form-control" name="part_number[]" id="part_number_{{ $i }}" value="{{ $list->partnumber->part_number }}" /></td>
                                <td><input type="number" class="form-control text-right" name="vat[]" id="vat_{{ $i }}" value="{{ $list->vat }}" onchange="calc_change({{ $i }})" /></td>
                                <td><input type="number" class="form-control" name="qty[]" id="qty_{{ $i }}" value="{{ $list->qty }}" onchange="calc_change({{ $i }})" /></td>
                                <td><input type="number" class="form-control text-right" step="any" name="unitprice[]" id="unitprice_{{ $i }}" value="{{ $list->unitprice }}" onchange="calc_change({{ $i }})" /></td>
                                <td><input type="number" class="form-control text-right" step="any" name="value[]" id="value_{{ $i }}" value="{{ $list->value }}" /></td>
                                <td><input type="number" class="form-control text-right" step="any" name="discount[]" id="discount_{{ $i }}" value="{{ $list->discount }}" onchange="calc_change({{ $i }})" /></td>
                                <td><input type="number" class="form-control text-right" step="any" name="taxableamount[]" id="taxableamount_{{ $i }}" value="{{ $list->taxableamount }}" /></td>
                                <td><input type="number" class="form-control text-right" step="any" name="vatamount[]" id="vatamount_{{ $i }}" value="{{ $list->vatamount }}" /></td>
                                <td><input type="number" class="form-control text-right" step="any" name="totalamount[]" id="totalamount_{{ $i }}" value="{{ $list->taxableamount + $list->vatamount }}" /></td>
                                <td>
                                    <input type="hidden" name="pr_id[]" value="{{ $list->pr_id }}" />
                                    <input type="hidden" name="item_id[]" value="{{ $list->id }}" />
                                    <input type="hidden" name="partno[]" value="{{ $list->partno }}" />
        
                                    <input type="hidden" name="isdelete[]" id="isdelete_{{ $i }}" value="0" />
                                    <a onclick="row_delete({{ $i }},{{ $list->id }})" class="btn-sm btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></a></td> --}}




                            <td><input type="text" class="form-control text-center" name="sort_id[]" value="{{ $i }}" />
                                <input type="hidden" name="product_type[]" value="{{ $items->product_type }}" />
                                <input type="hidden" name="item_id[]" value="{{ $items->id }}" />
                                <input type="hidden" name="part_number_txt[]" value="{{ @$items->partnumber->part_number }}" />
                            </td>
                            <td>
                                <select class="form-control noborder " name="part_number[]">
                                    <option value="{{ $items->partno }}">{{ @$items->partnumber->part_number ?? 0 }}</option>
                                </select>
                            </td>
                            <td>
                                                <textarea class="form-control" name="description[]" rows="1">{{ $items->description }}</textarea>
                                            </td>
                            <td><input type="text" class="form-control text-center" name="tax[]" value="{{ number_format($items->vat ?? 0,0,'.','') }}" onchange="calc_change_new(this)"/></td>
                            <td><input type="text" class="form-control text-center" data-enter-skip name="qty[]" value="{{ $items->qty }}" onchange="calc_change_new(this)" onkeydown="return set_license_key_normal(event, this)"/></td>
                            <td><input type="text" class="form-control text-end" step="Any" name="unitprice[]" value="{{ @App\SysHelper::com_curr_format($items->unitprice,2,'.',',') }}" onchange="calc_change_new(this)" onblur="formatCurrency(this)"/></td>
                            <td><input type="text" class="form-control text-end" name="value[]" value="{{ @App\SysHelper::com_curr_format($items->value,2,'.',',') }}" onchange="calc_change_new(this)"/></td>
                            <td><input type="text" class="form-control text-end" name="discount[]" value="{{ @App\SysHelper::com_curr_format($items->discount,2,'.',',') }}" onchange="calc_change_new(this)" onblur="formatCurrency(this)"/></td>
                           
                            
                            <td><input type="text" class="form-control text-end" name="taxableamount[]" value="{{ @App\SysHelper::com_curr_format($items->taxableamount,2,'.',',') }}" readonly/></td>
                            <td><input type="text" class="form-control text-end" name="vatamount[]" value="{{ @App\SysHelper::com_curr_format($items->vatamount,2,'.',',') }}" readonly/></td>
                            <td><input type="text" class="form-control text-end" name="totalamount[]" value="{{ @App\SysHelper::com_curr_format($items->taxableamount+$items->vatamount, 2, '.', ',') }}" readonly/></td>
                            <td><input class="form-control" type="text" value="{{$items->serialno}}" name="serial_no[]"></td>
                            {{-- <td >

                                /*
                                    $srno = $edit_list_srl->where('part_no',$items->part_no)->where('item_id',$items->id)->pluck('srl_no');
                                    $array = explode(',', trim($srno, '[""]'));
                                    $string = implode(', ', $array);

                                    if($string!=""){
                                        $string=str_replace('"', '',$string);
                                    }*/
                                
                                <input type="text" class="form-control" name="serial_no[]" value="{{ $string }}" /></td> --}}
                            
                        </tr>
                        
                        @php
                        $po_qty += $items->po_qty;
                        $qty += $items->qty;
                        $grn_qty += $items->grn_qty;
                        $balance_qty += abs($items->po_qty - $items->grn_qty);
                        $unitprice += $items->unitprice;
                        $value += $items->value;
                        $discount += $items->discount;
                        $taxableamount += $items->taxableamount;
                        $vatamount += $items->vatamount;
                        $total += $items->taxableamount+$items->vatamount;
                        $i++;
                        @endphp
                        @endforeach
                    @endif
                    @endif
                    <tr>
                                            <td><input type="text" class="form-control text-center" name="sort_id[]" value="{{ $i }}" />
                                                <input class="form-control" type="text" name="hscode_txt[]" autocomplete="off" readonly="true" hidden>
                                                <input class="form-control" type="text" name="product_type[]" autocomplete="off" readonly="true" hidden>
                                                <input class="form-control" type="text" name="product_type_part_number_text[]" autocomplete="off" readonly="true" hidden>
                                            </td>
                                            <td class="noborder">
                                                <select class="form-control noborder " name="part_number[]">
                                                </select>
                                                <input class="form-control" type="text" name="part_number_txt[]" autocomplete="off" readonly="true" hidden>
                                                {{-- on focus add this class and its funcanalities js-product-select --}}
                                            </td>
                                             <td>
                                                <textarea class="form-control" name="description[]" rows="1"></textarea>
                                            </td>
                                            <td><input type="number" class="form-control text-center" name="tax[]" onchange="calc_change_new(this)"></td>
                                            <td><input class="form-control text-center" data-enter-skip type="number" name="qty[]" autocomplete="off" min="0" onchange="calc_change_new(this)" onkeydown="return set_license_key_normal(event, this)"></td>
                                            <td><input class="form-control text-end" type="text" name="unitprice[]" step="any" autocomplete="off" min="0" onchange="calc_change_new(this)" onblur="formatCurrency(this)"></td>
                                            <td><input class="form-control text-end" type="text" name="value[]" autocomplete="off" min="0" readonly></td>
                                            <td><input class="form-control text-end" type="text" name="discount[]" autocomplete="off" min="0" onchange="calc_change_new(this)" onblur="formatCurrency(this)"></td>
                                            <td><input class="form-control text-end" type="text" name="taxableamount[]" autocomplete="off" min="0" readonly></td>
                                            <td><input class="form-control text-end" type="text" name="vatamount[]" autocomplete="off" min="0" readonly></td>
                                            <td><input class="form-control text-end" type="text" name="totalamount[]" autocomplete="off" min="0" readonly></td>
                                            <td><input class="form-control" type="text" name="serial_no[]"></td>
                                        </tr>

                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="4" scope="col" >Total</th>
                                            <th class="text-center"><label id="lbl_total_qty" >0</label></th>
                                            <th class="text-end" scope="col" ><label id="lbl_total_price" >0</label></th>
                                            <th class="text-end" scope="col" ><label id="lbl_total_value" >0</label></th>
                                            <th class="text-end" scope="col" ><label id="lbl_total_discount" >0</label></th>
                                            <th class="text-end" scope="col" ><label id="lbl_total_taxableamount" >0</label></th>
                                            <th class="text-end" scope="col" ><label id="lbl_total_vatamount" >0</label></th>
                                            <th class="text-end" scope="col" ><label id="lbl_total_totalamount" >0</label></th>
                                            <th class="text-end" scope="col" ></th>
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

        <div class="modal side-panel fade" id="discountModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
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

        <div class="modal side-panel fade" id="freightModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
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

        <div class="modal side-panel fade" id="customModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
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

        <div class="modal side-panel fade" id="serialNoModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
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

        <div class="modal side-panel fade" id="adjustmentModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" style="height: 500px !important;"> 
              	<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title" id="editModalLabel">Bill Wise Adjustments</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'purchase-return-add-adjestment', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'purchase-return-add-adjestment']) }}
                    <input type="hidden" value="{{ $edit->doc_number }}" name="adj_pri_no">
                    <input type="hidden" value="{{ $edit->lpo_number }}" name="edit_adj_lpo_no">
                    <input type="hidden" value="{{ $edit->doc_date }}" name="edit_adj_doc_date">
					<div class="modal-body m-0 p-0">
						<div class="card mb-0 mt-0">
							<div class="card-body" style="height: 420px; overflow-y: scroll;">
                                <div class="row">
                                    <div class="col-12 mb-2">
                                        <input type="text" id="act_pri_adj_amount" value="{{ ($editList->sum('taxableamount')+$editList->sum('vatamount')) }}" hidden/>
                                        <input type="text" id="pri_adj_amount" value="{{ ($editList->sum('taxableamount')+$editList->sum('vatamount')) }}"  hidden/>
                                <table class="table table-hover form-item-table" id="table_adjestment">
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
                                    @php $i=0; @endphp
                                            @if (count($pri_adjestment)>0)
                                            @foreach ($pri_adjestment as $dt)
                                            @php
                                            
                                            if($dt->paid_amount==""){$paid_amount = 0;} else {$paid_amount = $dt->paid_amount;}
                                            $balance_amount = abs($dt->total_amount - $paid_amount);

                                            @endphp
                                            <tr>
                                                <td style="width:100px;"><input type="text" class="form-control" name="adj_doc_date[]" id="adj_doc_date_{{ $i }}" value="{{ date('d/m/Y', strtotime($dt->doc_date)) }}" readonly /></td>
                                                <td style="width:100px;"><input type="text" class="form-control" name="adj_pi_no[]" id="adj_pi_no_{{ $i }}" value="{{ $dt->piv_no }}" readonly /></td>
                                                <td style="width:100px;"><input type="text" class="form-control text-end" name="adj_total[]" id="adj_total_{{ $i }}" value="{{ $dt->total_amount }}" readonly /></td>
                                                <td style="width:100px;"><input type="text" class="form-control text-end class_adj_paid" name="adj_paid[]" id="adj_paid_{{ $i }}" value="{{ $paid_amount }}" onchange="get_set_amount()" onclick="set_adjestment({{ $i }})" required /></td>
                                                <td style="width:100px;"><input type="text" class="form-control text-end" name="adj_balance[]" id="adj_balance_{{ $i }}" value="{{ $balance_amount }}" readonly /></td>
                                            </tr>
                                            @php $i++; @endphp
                                            @endforeach
                                            @else
                                            <tr>
                                                <td style="width:100px;"><input type="text" class="form-control" name="adj_doc_date" value="{{ date('d/m/Y', strtotime($edit->doc_date)) }}" readonly /></td>
                                                <td style="width:100px;"><input type="text" class="form-control" name="adj_pi_no" value="{{ $edit->pi_number }}" readonly /></td>
                                                <td style="width:100px;"><input type="text" class="form-control" name="adj_lpo_no" value="{{ $edit->lpo_number }}" readonly /></td>
                                                <td style="width:100px;"><input type="text" class="form-control text-end" name="adj_total" id="adj_total" value="{{ $invoice_amount }}" readonly /></td>
                                                <td style="width:100px;"><input type="text" class="form-control text-end class_adj_paid" name="adj_paid" id="adj_paid" value="" onchange="get_set_amount()" required /></td>
                                                <td style="width:100px;"><input type="text" class="form-control text-end" name="adj_balance" id="adj_balance" value="" readonly /></td>
                                            </tr>
                                            @endif
                                </tbody>
                                        <tfoot>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th class="text-end"><label id="footer_total"></label></th>
                                                <th class="text-end"><label id="footer_paid"></label></th>
                                                <th class="text-end"><label id="footer_balance"></label></th>
                                            </tr>
                                        </tfoot>
                            </table>
                                    </div>
                                </div>
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
        <script>
                            function get_set_amount(id)
                            {
                                set_adjestment(id);
                                var adj_total = Number($('#adj_total_'+id).val() || 0);
                                var adj_paid = Number($('#adj_paid_'+id).val() || 0);
                                $('#adj_balance_'+id).val(adj_total - adj_paid);

                                updateAdjustmentTotals();
                            }

                            function set_adjestment(id){
                                var sum = Number($('#act_pri_adj_amount').val() || 0);
                                var numItems = $('.class_adj_paid').length;
                                var adj=0;
                                for(i=0; i < numItems; i++){
                                    if(i!=id){
                                        adj +=  Number($('#adj_paid_'+i).val() || 0);
                                    }
                                }

                                var adj2 = sum - adj;

                                if(adj2 > 0){
                                    $('#pri_adj_amount').val(adj2);
                                }
                                else { $('#pri_adj_amount').val(0); }

                                var adj3 = Number($('#pri_adj_amount').val() || 0);

                                if(adj3 > 0){
                                    var adj_total = Number($('#adj_balance_'+id).val() || 0);
                                    if(adj3 >= adj_total){
                                        $('#adj_paid_'+id).val(adj_total);
                                    }
                                    else{
                                        $('#adj_paid_'+id).val(adj3);
                                    }
                                }

                                updateAdjustmentTotals();
                            }

                            function updateAdjustmentTotals() {
                                var total = 0;
                                var paid = 0;
                                var balance = 0;

                                $('#table_adjestment tbody tr').each(function() {
                                    var rowTotal = Number($(this).find('input[name="adj_total[]"]').val() || $(this).find('input[name="adj_total"]').val() || 0);
                                    var rowPaid = Number($(this).find('input[name="adj_paid[]"]').val() || $(this).find('input[name="adj_paid"]').val() || 0);
                                    var rowBalance = Number($(this).find('input[name="adj_balance[]"]').val() || $(this).find('input[name="adj_balance"]').val() || 0);

                                    total += rowTotal;
                                    paid += rowPaid;
                                    balance += rowBalance;
                                });

                                $('#footer_total').text(total.toFixed(2));
                                $('#footer_paid').text(paid.toFixed(2));
                                $('#footer_balance').text(balance.toFixed(2));
                            }

                            // Recalculate footer totals when paid field loses focus, without altering balance values.
                            $(document).on('blur', '.class_adj_paid', function() {
                                updateAdjustmentTotals();
                            });

                            $(document).ready(function() {
                                updateAdjustmentTotals();
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

{{-- Models  --}}

<script>
$(window).ready(function() {
    $("#purchase-return-update-form").on("keypress", function(event) {
        var keyPressed = event.keyCode || event.which;
        if (keyPressed === 13) {
            event.preventDefault();
            return false;
        }
    });
});

$(document).on("keydown", 'input[name="unitprice[]"], input[name="discount[]"], input[name="serial_no[]"]', function(e) {
    if (e.key === "Enter") {
        e.preventDefault(); // prevent form submit

        let row = $(this).closest("tr"); // get current row
        let name = $(this).attr("name");
        
        if (name === "unitprice[]") {
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

        // Focus textarea when modal is fully shown
        modalElement.addEventListener('shown.bs.modal', function () {
            const ta = document.getElementById('add_serial_no');
            if (ta) {
                ta.focus();
                // move caret to end
                ta.selectionStart = ta.selectionEnd = ta.value.length;
            }
        });
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
        total_taxableamount += parseFloat($row.find('input[name="taxableamount[]"]').val().replace(/,/g, '')) || 0;
        total_vatamount += parseFloat($row.find('input[name="vatamount[]"]').val().replace(/,/g, '')) || 0;
        total_totalamount += parseFloat($row.find('input[name="totalamount[]"]').val().replace(/,/g, '')) || 0;
    });

    $('#lbl_total_qty').text(total_qty);
    $('#lbl_total_price').text(formatAmount(total_price));
    $('#lbl_total_value').text(formatAmount(total_value));
    $('#lbl_total_discount').text(formatAmount(total_discount));
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
            $row.find('textarea[name="description[]"]').val(selectedData.description || '');
            $row.find('input[name="part_number_txt[]"]').val(selectedData.text || '');
            $row.find('input[name="hscode_txt[]"]').val(selectedData.hscode || '');
            $row.find('input[name="product_type[]"]').val(selectedData.product_type || '');
            $row.find('input[name="product_type_part_number_text[]"]').val(selectedData.description || '');
            $row.find('input[name="discount[]"]').val(0);
            $row.find('input[name="tax[]"]').val(parseInt($('#net_vat').val()));
                $row.find('input[name="qty[]"]').focus();
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

    <!-- Modal Change Currancy-->
        <div class="modal side-panel fade" id="ModalChangeCurrancy" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">Change Currancy</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'purchase-return-update-currency', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Currancy From</label>
                                <select class="form-control" name="from_currency_id" required>
                                    @foreach ($currency as $value)
                                        @if($edit->currency == $value->id)
                                            <option value="{{ @$value->id }}" >{{ @$value->code }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Currancy To</label>
                                <select class="form-control" name="to_currency_id" id="to_currency_id" required onchange="set_rate()">
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
                    <input type="hidden" name="cur_pr_id" value="{{ @$edit->id }}"/>
                    <input type="hidden" name="cur_pr_doc_no" value="{{ @$edit->doc_number }}"/>
						<button type="submit" class="btn btn-light add-btn ms-2">
							<i class="ico icon-outline-bookmark-opened text-success"></i> Change
						</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!-- Modal Change Currancy-->

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
                        <label for="" class="form-label">Selected: <label id="selected_key">0</label> of <label id="license_qty_cap">0</label></label>
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
                                    <th style="width: 30%;">Licence Key</th>
                                    <th style="width: 15%;">Expiry Date</th>
                                    <th style="width: 12%;">Doc No</th>
                                    <th style="width: 10%;">Doc Date</th>
                                    <th style="width: 13%;">Name</th>
                                    <th style="width: 15%;">Bill Number</th>
                                    <th style="width: 15%;">Deal ID</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
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
            if ($(e.target).closest('table').attr('id') !== 'lk-table') {
                return;
            }
            if ($(e.target).closest('td').hasClass('no-toggle')) {
                return;
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
                    prLicenseSetSerialTargetFromRow($sel.closest('tr'));
                }
            }
            setTimeout(function() {
                $('#license_key_search').focus();
            }, 50);
        });
    });

    function prLicenseSetSerialTargetFromRow($row) {
        window.prLicenseSerialInput = null;
        if ($row && $row.length) {
            var $el = $row.find('input[name="serial_no[]"]').first();
            if ($el.length) {
                window.prLicenseSerialInput = $el;
            }
        }
    }

    function prLicenseResolveSerialInput() {
        var $inp = window.prLicenseSerialInput;
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

    function prLicenseAppendSelectedKeysToSerial() {
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
        var $inp = prLicenseResolveSerialInput();
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
        var pt = $row.find('input[name="product_type[]"]').first().val();
        var partId = $row.find('select[name="part_number[]"] option:selected').val();
        var hasValidPart = partId !== undefined && partId !== null && String(partId).trim() !== '';
        var isLicenseType = parseInt(String(pt == null ? '' : pt).trim(), 10) === 2;

        if (isLicenseType || hasValidPart) {
            $('#part_no').val(partId);
            prLicenseSetSerialTargetFromRow($row);
            var rowQty = parseInt($row.find('input[name="qty[]"]').val(), 10) || 0;
            $('#license_qty_limit').val(rowQty);
            $('#license_qty').val(rowQty);
            $("#ModalLabelHeading").text($row.find('select[name="part_number[]"] option:selected').text());
            $("#btn_ModalLicenseKey").click();
            get_license_key($('#part_no').val());
            e.preventDefault();
            return false;
        }

        return true;
    }

    function get_license_key(part_id) {
        $("#loading_bg").css("display", "block");
        var prId = parseInt($('#pr_id').val() || 0, 10);
        var action = "{{ URL::to('purchase-return-get-dn-license-key') }}";
        var requestData = {
            _token: '{{ csrf_token() }}',
            item_id: part_id,
            pr_id: prId
        };
        var qtyLimit = parseInt($('#license_qty_limit').val(), 10) || 0;
        $('#license_qty').val(qtyLimit);
        $('#license_qty_cap').text(qtyLimit);
        $.ajax({
            url: action,
            type: "POST",
            data: requestData,
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
                if (dataResult['data'] != null) {
                    len = dataResult['data'].length;
                    $('#total_key').text(len);
                }
                if (len > 0) {
                    for (var i = 0; i < len; i++) {
                        var row = dataResult['data'][i];
                        var isSelected = Number(row.status) === 2 && Number(row.purchase_return_id) === prId;
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
                        getSelectedRows +=
                            "<tr class='text-center' data-lk-status=\"" + (row.status != null ? row.status : '') + "\">\
                                <td><input class='chk_key' type='checkbox' id='select_key_" +
                            Number(i + 1) + "' onclick='key_select_change(" + Number(i + 1) + ")'" + (isSelected ? ' checked' : '') + " /><input type='hidden' id='item_key_id_" +
                            Number(i + 1) + "' value='" + row.id + "' /></td>\
                                <td class='text-start'>" + (row.license_key || "") + "</td>\
                                <td>" + (row.exp_date ? get_format_date(row.exp_date) : "") + "</td>\
                                <td>" + docNoHtml + "</td>\
                                <td>" + docDate + "</td>\
                                <td class='text-start'>" + partyName + "</td>\
                                <td>" + billNumber + "</td>\
                                <td>" + dealId + "</td>\
                                </tr>";
                    }
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
        var qtyLimit = parseInt($('#license_qty_limit').val(), 10) || 0;
        var selectedCount = $('.chk_key:checked').length;
        if (qtyLimit > 0 && selectedCount > qtyLimit) {
            toastr.error('Only ' + qtyLimit + ' license keys can be selected for this item quantity.');
            $("#loading_bg").css("display", "none");
            return false;
        }

        var stagingIds = [];
        var keepPrKeyIds = [];
        $('.chk_key:checked').each(function() {
            var st = parseInt($(this).closest('tr').attr('data-lk-status'), 10);
            var hid = $(this).closest('td').find('input[type="hidden"]').val();
            if (!hid) {
                return;
            }
            if (st === 1) {
                stagingIds.push(hid);
            } else if (st === 2) {
                keepPrKeyIds.push(hid);
            }
        });

        $.ajax({
            url: "{{ URL::to('purchase-return-update-dn-license-key') }}",
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                id: stagingIds.join(','),
                item_id: $('#part_no').val(),
                qty_limit: qtyLimit,
                pr_id: $('#pr_id').val(),
                keep_pr_key_ids: keepPrKeyIds.join(',')
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
                prLicenseAppendSelectedKeysToSerial();
                $('#popup_close').click();
            },
            complete: function() {
                $("#loading_bg").css("display", "none");
            }
        });
    }
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
                                    console.log('Bill number changed');
                                    var bill = ($(this).val() || '').toString().trim();
                                    var currentNarr = ($narr.val() || '').toString().trim();
                                    var lastAuto = $narr.data('autoBill') || '';

                                    console.log('Current Narration: ' + currentNarr);
                                    console.log('Last Auto-filled Bill: ' + lastAuto);

                                    if (currentNarr === '' || currentNarr === lastAuto) {
                                        console.log('Updating narration to match bill number');
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
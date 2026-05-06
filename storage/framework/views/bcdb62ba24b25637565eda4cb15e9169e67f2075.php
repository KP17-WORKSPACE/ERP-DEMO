    <?php try { ?>

            <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'purchase-invoice-update', 'method' => 'POST', 'id' => 'purchase-invoice-create-form', 'novalidate' => true])); ?>


            <input type="hidden" name="url" id="url" value="<?php echo e(URL::to('/')); ?>">
            <input type="hidden" name="id" id="pi_id" value="<?php echo e(isset($edit_pi) ? $edit_pi->id : ''); ?>">
            <input type="hidden" name="net_vat" id="net_vat" value="<?php echo e(@$edit_pi_items[0]->tax); ?>">
            <input type="hidden" name="doc_number_main" id="doc_number_main" value="<?php echo e($edit_pi->doc_number); ?>">
            



    <div class="purchase-order-content-header">
        <h4 class="purchase-order-content-header-left">
            Edit - <?php echo e(@$edit_pi->doc_number); ?>

        </h4>
        <div class="purchase-order-content-header-right">
            <a type="submit" class="btn btn-light text-dark" href="<?php echo e(url('purchase-invoice/'. $edit_pi->id .'?pi_action=add')); ?>">
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
                    <li><a class="dropdown-item" href="<?php echo e(url('purchase-invoice/'.$edit_pi->id.'/delete')); ?>"><i class="ico icon-outline-trash-bin-minimalistic text-danger"></i> Cancel PI</a></li>
                    <li><a class="dropdown-item" href="<?php echo e(url('purchase-invoice/'.$edit_pi->id.'/download')); ?>"><i class="ico icon-outline-document-medicine text-success"></i> Download</a></li>
                    <li><button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#adjustmentModal"><i class="ico icon-outline-calculator-minimalistic text-warning"></i> Adjustment</button></li>
                    <li><button type="button" class="dropdown-item" data-modal-size="modal-md" data-bs-target="#attachment_popup_win" data-bs-toggle="modal" class="btn btn-primary" onclick="view_attachment()"><i class="ico icon-outline-calculator-minimalistic text-warning"></i> Attachment</button></li>
                </ul>
            </div>
        </div>
    </div>
    
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row gap-rows">
                                        <div class="col-4">
                                            <label class="form-label">Vendor Name</label>
                                            <div class="form-group">
                                                <select class="form-control " name="vendors" id="vendors" onchange="get_pending_po_list()">
                                                <!-- <option value=""></option> -->
                                                <?php $__currentLoopData = $vendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e(@$value->id); ?>" <?php if(isset($grn) && $edit_pi->vendors == $value->id): ?> selected <?php endif; ?>>
                                                        <?php echo e(@$value->account_name); ?> <?php if(@App\SysHelper::getCompanyCodeSettings()['is_supplier_code']): ?>
                                            (<?php echo e(@$value->account_code); ?>)
                                            <?php endif; ?>
                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                    


                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <label class="form-label">PI Number</label>
                                            <div class="form-group">
                                                <input
                                class="form-control"
                                type="text" name="doc_number" autocomplete="off" id="doc_number"
                                value="<?php echo e(@$edit_pi->doc_number); ?>"
                                readonly>
                                            </div>
                                        </div>
                                         <div class="col-2">
                                            <label class="form-label">PI Date</label>
                                            <div class="form-group">
                                           <?php
                                           
                                                $rawDate = old('pi_date') ?? ($edit_pi->pi_date ?? null);
                                                $value = $rawDate ? \Carbon\Carbon::parse($rawDate)->format('d/m/Y') : '';
                                            ?>
                                            <input class="form-control date-picker" id="pi_date" type="text" autocomplete="off" name="pi_date" value="<?php echo e(@$value); ?>">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <label class="form-label">Currency<a style="float: right;" data-bs-target="#ModalChangeCurrancy" data-bs-toggle="modal"><i class="ico icon-outline-pen-2"></i></a></label>
                                            <div class="form-group"><select
                                class="form-control js-example-basic-single"
                                name="currency" id="currency">
                                
                                <?php $__currentLoopData = $currency; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e(@$value->id); ?>"
                                    <?php if($edit_pi->currency_id == $value->id): ?> selected <?php endif; ?>>
                                        <?php echo e(@$value->code); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        

                                            </div>
                                        </div>
                                       <div class="col-2">
                                            <label class="form-label">Created By</label>
                                          
                                                
                                                <input readonly type="text" class="form-control" name="createdby" id="createdby" value="<?php echo e($edit_pi->createdby->full_name); ?>">

                                            
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
                                        <button class="nav-link" id="shipping-details-tab" data-bs-toggle="tab" data-bs-target="#shipping-details" type="button" role="tab" aria-controls="shipping-details" aria-selected="true">Shipping Details</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="vat-details-tab" data-bs-toggle="tab" data-bs-target="#vat-details" type="button" role="tab" aria-controls="vat-details" aria-selected="true">VAT Details</button>
                                    </li>
                                </ul>
                                <div class="tab-content mb-3" id="purchaseDetailsTabsContent">
                                    <div class="tab-pane fade show active" id="extra-fields" role="tabpanel" aria-labelledby="extra-fields-tab">
                                        <div class="row gap-rows">


                <div class="col-2 mb-2">
                    <div class="input-effect">
                        <label class="txtlbl">Pending list</label>
                        <div id="plist" style="width: 100%; height: 130px; border: solid 1px #dfe1d7; border-radius: 5px; padding: 5px 5px; overflow-y: scroll;"></div>
                        <a data-modal-size="modal-md" data-target="#grn_pending_popup_win" id="addGRNPending" data-toggle="modal"></a>
                        <input type="hidden" id="grn_id" name="grn_id">
                        <input type="hidden" id="po_id" name="po_id">
                        <input type="hidden" id="vat_percentage" name="vat_percentage" value="5">
                    </div>
                </div>    
                <div class="col-10 mb-2">
                    <div class="row gap-rows">

                     <div class="col-2">
                                                <label class="form-label">GRN No</label>
                                                <div class="form-group">
                                <input
                                    class="form-control"
                                    type="text" name="grn_no" autocomplete="off" id="grn_no"
                                    value="<?php echo e(isset($edit_pi) ? (!empty(@$edit_pi->grn_no) ? @$edit_pi->grn_no : old('grn_no')) : ''); ?>">
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <label class="form-label">GRN Date</label>
                                                <div class="form-group">
                                                    <?php
    $rawDate = old('grn_date') ?? ($edit_pi->grn_date ?? null);
    $grnDate = $rawDate ? \Carbon\Carbon::parse($rawDate)->format('d/m/Y') : '';
?>
                                <input
                                    class="form-control date-picker"
                                    type="text" name="grn_date" autocomplete="off" id="grn_date" required
                                    value="<?php echo e($grnDate); ?>">
                                                </div>
                                            </div>

                      
                                           
                                            <div class="col-2">
                                                <label class="form-label">Bill Number</label>
                                                <div class="form-group">
                                <input class="form-control" type="text" name="bill_number" autocomplete="off" id="bill_number" value="<?php echo e(isset($edit_pi) ? (!empty(@$edit_pi->bill_number) ? @$edit_pi->bill_number : old('bill_number')) : ''); ?>" onchange="updateNarration()">
                                                </div>
                                            </div>
                                         <div class="col-2">
                                                <label class="form-label">Bill Date</label>
                                                <div class="form-group">
                                                  <?php
                                                    $rawDate = old('bill_date') ?? ($edit_pi->bill_date ?? now());
                                                    $value = \Carbon\Carbon::parse($rawDate)->format('d/m/Y');
                                                ?>
                                <input class="form-control date-picker" id="bill_date" type="text" autocomplete="off"
                                    name="bill_date" value="<?php echo e(@$value); ?>" required >
                                                </div>
                                            </div>

                                             <div class="col-2">
                                                <label class="form-label">LPO Number</label>
                                                <div class="form-group">
                                <input
                                    class="txtbx primary-input form-control <?php echo e($errors->has('lpo_number') ? ' is-invalid' : ''); ?>"
                                    type="text" name="lpo_number" autocomplete="off" id="lpo_number"
                                    value="<?php echo e(isset($edit_pi) ? (!empty(@$edit_pi->lpo_number) ? @$edit_pi->lpo_number : old('lpo_number')) : ''); ?>">
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <label class="form-label">LPO Date</label>
                                                <div class="form-group">
                                                      <?php
                                                $rawDate = old('grn_date') ?? ($edit_pi->lpo_date ?? null);
                                                $value = $rawDate ? \Carbon\Carbon::parse($rawDate)->format('d/m/Y') : '';
                                            ?>
                                                    <input class="form-control date-picker" id="lpo_date" type="text" autocomplete="off" name="lpo_date" value="<?php echo e(@$value); ?>" style="margin-top:0px;">
                                                </div>
                                            </div>

                                             <div class="col-2">
                                                <label class="form-label">Payment Terms</label>
                                                <div class="form-group">
                                                    <select
                                    class="form-control js-example-basic-single"
                                    name="payment_terms" id="payment_terms">
                                    <option value=""></option>
                                    <?php $__currentLoopData = $paymentterms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e(@$value->id); ?>"
                                            <?php echo e(isset($edit_pi) ? (!empty(@$edit_pi->payment_terms) ? (@$edit_pi->payment_terms == @$value->id ? 'selected' : '') : '') : ''); ?>>
                                            <?php echo e(@$value->title); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                        

                                                </div>
                                                <div id="div_payment_terms" style="display: none; padding-top: px;">
                                                    <div class="input-effect">
                                                        <label class="txtlbl"><?php echo app('translator')->getFromJson('Other Payment Terms'); ?><span>*</span></label>
                                                        <input
                                                            class="txtbx primary-input form-control <?php echo e($errors->has('payment_terms2') ? ' is-invalid' : ''); ?>"
                                                            type="text" name="payment_terms2" autocomplete="off" id="payment_terms2"
                                                            value="<?php echo e(isset($edit_pi) ? (!empty(@$edit_pi->payment_terms2) ? @$edit_pi->payment_terms2 : old('payment_terms2')) : ''); ?>">
                                                    </div>
                                                </div>
                                            </div>

                                               <div class="col-2">
                                                <label class="form-label">Deal ID</label>
                                                <div class="form-group">
                                <input class="form-control"
                                    type="text" name="deal_id" autocomplete="off" id="deal_id"
                                    value="<?php echo e(isset($edit_pi) ? (!empty(@$edit_pi->deal_id) ? @App\SysHelper::get_code_from_dealid(@$edit_pi->deal_id) : old('deal_id')) : ''); ?>">
                                                </div>
                                            </div>


                                             <div class="col-2">
                                                <label class="form-label">Customer Reference</label>


                                                
                                                   <?php
$selectedCompanies = $edit_pi->ref_company_id
    ? explode(',', $edit_pi->ref_company_id)
    : [];

$selectedCompanyNames = [];
foreach ($customer_reference_list as $company) {
    if (in_array($company->id, $selectedCompanies)) {
        $selectedCompanyNames[] = $company->name;
    }
}
?>

  <input class="form-control" type="text" name="customer_reference_input"
                            autocomplete="off" id="customer_reference_input" readonly value="<?php echo e(implode(', ', $selectedCompanyNames)); ?>">

                        <!-- Hidden container to hold actual selected IDs for form submission -->
                        <div id="ref_company_hidden_inputs" style="display:none;">
                            <?php $__currentLoopData = $selectedCompanies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ScompanyId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <input type="hidden" name="ref_company_id[]" value="<?php echo e($ScompanyId); ?>">
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                                      
                                            <?php $__currentLoopData = $customer_reference_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e(@$value->id); ?>"><?php echo e(@$value->name); ?> <?php if(@App\SysHelper::getCompanyCodeSettings()['is_customer_code']): ?> (<?php echo e(@$value->code); ?>) <?php endif; ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                                                      <select class="form-control js-example-basic-single" name="ref_company_id" id="ref_company_id" required>
                            <option value="">-Select-</option>
                            <?php $__currentLoopData = $customer_reference_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e(@$value->id); ?>" <?php if(@$edit_pi->ref_company_id == @$value->id): ?> selected <?php endif; ?> ><?php echo e(@$value->name); ?> 
                                <?php if(@App\SysHelper::getCompanyCodeSettings()['is_customer_code']): ?>
                                        (<?php echo e(@$value->code); ?>)
                                        <?php endif; ?>
                            </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                                            <input
                                                class="form-control"
                                                type="hidden" name="reference" autocomplete="off"
                                                value="<?php echo e(isset($edit_pi) ? (!empty(@$edit_pi->reference) ? @$edit_pi->reference : old('reference')) : old('reference')); ?>"
                                                id="reference">
                                                </div> -->
                                            </div>

                                              <div class="col-2">
                                                <label class="form-label">Sales Person</label>
                                                <div class="form-group">
                                <select class="form-control js-example-basic-single" required name="sales_person" id="sales_person">
                                    <option value=""></option>
                                    <?php $__currentLoopData = $salesman; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e(@$value->user_id); ?>" <?php if($edit_pi->sales_person == $value->user_id): ?> selected <?php endif; ?>><?php echo e(@$value->full_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                    
                                    <?php if(isset($edit_pi) && $edit_pi->sales_person): ?>
                                        <?php
                                            $selectedId = $edit_pi->sales_person;
                                            $exists = collect($salesman)->contains(function($item) use ($selectedId) {
                                                return isset($item->user_id) && (string)$item->user_id === (string)$selectedId;
                                            });
                                        ?>

                                        <?php if(!$exists): ?>
                                            <?php
                                                $staff = \App\SmStaff::where('user_id', $selectedId)->first();
                                                $fallbackUser = null;
                                                if(!$staff) {
                                                    $fallbackUser = \App\User::find($selectedId);
                                                }
                                            ?>

                                            <?php if($staff): ?>
                                                <option value="<?php echo e($staff->user_id); ?>" selected><?php echo e($staff->full_name ?? trim($staff->first_name . ' ' . $staff->last_name)); ?></option>
                                            <?php elseif($fallbackUser): ?>
                                                <option value="<?php echo e($fallbackUser->id); ?>" selected><?php echo e($fallbackUser->full_name ?? $fallbackUser->name ?? $fallbackUser->email); ?></option>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php elseif(isset($edit_pi) && !is_null($edit_pi->sales_person_name) && $edit_pi->sales_person_name !== ''): ?>
                                        
                                        <option value="<?php echo e($edit_pi->sales_person_name); ?>" selected><?php echo e($edit_pi->sales_person_name); ?></option>
                                    
                                    <?php endif; ?>
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
                                                <label class="form-label">Warehouse</label>
                                                <div class="form-group">

                                                <?php
                                        $warehouses = App\SysHelper::getCompanyWarehouses();
                                        ?>

                                         <select class="form-control js-example-basic-single" required name="warehouse" id="warehouse">
                                       
                                        <?php $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e(@$value->id); ?>" <?php if(@$edit_pi->warehouse == $value->id): ?> selected
                                                
                                            <?php endif; ?>><?php echo e(@$value->warehouse_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        
                                    </select>

                                                <!-- <input class="form-control"
                                                    type="text" name="warehouse" autocomplete="off"
                                                    value="<?php echo e(isset($edit_pi) ? (!empty(@$edit_pi->warehouse) ? @$edit_pi->warehouse : old('warehouse')) : old('warehouse')); ?>"
                                                    id="warehouse"> -->
                                                </div> 
                                            </div>

                                               <div class="col-2">
                                                <label class="form-label">BOE No</label>
                                                <div class="form-group">
                                <input class="txtbx primary-input form-control <?php echo e($errors->has('boeno') ? ' is-invalid' : ''); ?>"
                                    type="text" name="boeno" autocomplete="off"
                                    value="<?php echo e(isset($edit_pi) ? (!empty(@$edit_pi->boeno) ? @$edit_pi->boeno : old('boeno')) : old('boeno')); ?>"
                                    id="boeno">
                                                </div>
                                            </div>

                                            <div class="col-2">
                                                <label class="form-label">AWB No</label>
                                                <div class="form-group">
                                <input class="txtbx primary-input form-control <?php echo e($errors->has('awbno') ? ' is-invalid' : ''); ?>"
                                    type="text" name="awbno" autocomplete="off"
                                    value="<?php echo e(isset($edit_pi) ? (!empty(@$edit_pi->awbno) ? @$edit_pi->awbno : old('awbno')) : old('awbno')); ?>"
                                    id="awbno">
                                                </div>
                                            </div>
                                         
                                           
                                           
                                           
                                          
                                            <div class="col">
                                                <label class="form-label">Remarks</label>
                                                <div class="form-group">
                                <input  data-bs-toggle="modal"
                                        data-bs-target="#narrationModal"
                                    class="form-control"
                                    type="text" name="narration" autocomplete="off" id="narration"
                                    value="<?php echo e(isset($edit_pi) ? (!empty(@$edit_pi->narration) ? @$edit_pi->narration : old('narration')) : ''); ?>">
                                                </div>
                                            </div>
                                         

                    </div>
                </div>


                                            
                                        </div>
                                    </div>
                                    <div class="tab-pane fade show" id="shipping-details" role="tabpanel" aria-labelledby="shipping-details-tab">
                                        

                                         <div class="row gap-rows">
                   
                    <div class="col-3">
                        <label class="form-label">Company (Ship To)</label>
                        <div class="form-group">
                            <select class="form-control js-example-basic-single" name="shipping_supplier"
                                id="shipping_supplier" required style="width: 100%;">
                                <option value=""></option>
                                <?php $__currentLoopData = $customer; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $s = @App\SysHelper::internal_transfer_customer_id(@$value->id, session('logged_session_data.company_id')); ?>
                                    
                                    <option value="<?php echo e(@$value->id); ?>" <?php echo e($s); ?> <?php if($edit_pi->shipping_supplier == $value->id): ?> selected <?php endif; ?>>
                                        <?php echo e(@$value->account_name); ?> 
                                        <?php if(@App\SysHelper::getCompanyCodeSettings()['is_customer_code']): ?>
                                        (<?php echo e(@$value->account_code); ?>)
                                        <?php endif; ?>
                                       
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                                value="<?php echo e($edit_pi->shipping_name); ?>" />
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Contact Email</label>
                        <div class="form-group">
                            <input type="text" class="form-control" name="shipping_email" id="shipping_email"
                                value="<?php echo e($edit_pi->shipping_email); ?>" />
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Contact No</label>
                        <div class="form-group">
                            <input type="text" class="form-control" name="shipping_contact_no"
                                id="shipping_contact_no" value="<?php echo e($edit_pi->shipping_contact_no); ?>" />
                        </div>
                    </div>
                    <div class="col-3">
                        <label class="form-label">Shipping Address</label>
                        <div class="form-group">
                            <input type="text" class="form-control" name="shipping_address_1"
                                id="shipping_address_1" value="<?php echo e($edit_pi->shipping_address_1); ?>" />
                        </div>
                    </div>
                </div>

                                    </div>
                                    <div class="tab-pane fade show" id="vat-details" role="tabpanel" aria-labelledby="vat-details-tab">
                                        <div class="row gap-rows">


                                             <div class="col-2">
                                                <label class="form-label">Supplier Country</label>
                                                <div class="form-group">
                                        <select class="form-control js-example-basic-single" name="supplier_country" id="country" required>
                                            <option data-display="" value=""></option>
                                            <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e(@$value->id); ?>"
                                                    <?php try{?>                                                        
                                                    <?php if(isset($edit_pi)): ?> <?php if(@$edit_pi->supplier_country == $value->id): ?> selected <?php endif; ?>
                                                    <?php endif; ?>
                                                    <?php } catch (\Throwable $th) {} ?>
                                                    ><?php echo e(@$value->name); ?> </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <label class="form-label">Supplier State</label>
                                                <div class="form-group">
                                                    <div id="sectionStateDiv">
                                            <select class="form-control js-example-basic-single" name="supplier_state" id="state">
                                                <option data-display="" value=""></option>
                                                <?php try{?>
                                                    <?php $__currentLoopData = $states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>                                                    
                                                        <option value="<?php echo e($value->id); ?>" <?php if($edit_pi->supplier_state==$value->id): ?> selected <?php endif; ?>><?php echo e($value->name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php } catch (\Throwable $th) {} ?>


                                            </select>
                                        </div>
                                                </div>
                                            </div>

                                                     <div class="col">
                        <label class="form-label">VAT %</label>
                        <div class="form-group">
                          
                           <input class="form-control" type="number"  name="vat_percent" id="vat_percent" value="<?php echo e(@$edit_pi->vat_percent); ?>">
                        </div>
                    </div>

                    
                         <div class="col">
                        <label class="form-label">VAT Number</label>
                        <div class="form-group">
                          
                           <input class="form-control" type="number"  name="vat_number" id="vat_number" value="<?php echo e(@$edit_pi->vat_number); ?>">
                        </div>
                    </div>

                                            <div class="col-2">
                                                <label class="form-label">Supplier Type</label>
                                                <div class="form-group"> 
                                        <select
                                            class="dynamicstxt niceSelect w-100 bb form-control js-example-basic-single <?php echo e($errors->has('supplier_type') ? ' is-invalid' : ''); ?>"
                                            name="supplier_type" id="supplier_type">
                                            <option value="0"></option>
                                            <?php $__currentLoopData = $suppliertype; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e(@$value->id); ?>"
                                                    <?php echo e(isset($edit_pi) ? (!empty(@$edit_pi->supplier_type) ? (@$edit_pi->supplier_type == @$value->id ? 'selected' : '') : '') : ''); ?>>
                                                    <?php echo e(@$value->title); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                        
                                        
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <label class="form-label">Purchase Type</label>
                                                <div class="form-group">
                                        <select
                                            class="dynamicstxt niceSelect w-100 bb form-control js-example-basic-single <?php echo e($errors->has('purchase_type') ? ' is-invalid' : ''); ?>"
                                            name="purchase_type" id="purchase_type">
                                            <option value="0"></option>
                                            <?php $__currentLoopData = $purchasetype; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e(@$value->id); ?>"
                                                    <?php echo e(isset($edit_pi) ? (!empty(@$edit_pi->purchase_type) ? (@$edit_pi->purchase_type == @$value->id ? 'selected' : '') : '') : ''); ?>>
                                                    <?php echo e(@$value->title); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                                            <th class="resizable text-center" width="50px"><?php echo app('translator')->getFromJson('No'); ?><div class="resizer"></div></th>
                                            <th class="resizable text-center" width="150px"><?php echo app('translator')->getFromJson('Part No'); ?> <a class="icon icon-outline-book text-dark" data-bs-toggle="modal" data-bs-target="#addproductModal"></a><div class="resizer"></div></th>
                                            <th class="resizable text-center" width="280px"><?php echo app('translator')->getFromJson('Description'); ?><div class="resizer"></div></th>
                                            <th class="resizable text-center" width="30px"><?php echo app('translator')->getFromJson('Tax'); ?><div class="resizer"></div></th>
                                            <th class="resizable text-center" width="30px"><?php echo app('translator')->getFromJson('Qty'); ?><div class="resizer"></div></th>
                                            <th class="resizable text-center" width="80px"><?php echo app('translator')->getFromJson('Price'); ?><div class="resizer"></div></th>
                                            <th class="resizable text-center" width="80px"><?php echo app('translator')->getFromJson('Value'); ?><div class="resizer"></div></th>
                                            <th class="resizable text-center" width="80px" scope="col" >Dis <a class="icon icon-outline-book text-dark" data-bs-toggle="modal" data-bs-target="#discountModal"></a><div class="resizer"></div></th>
                                            <th class="resizable text-center" width="80px" scope="col" >Freight <a class="icon icon-outline-book text-dark" data-bs-toggle="modal" data-bs-target="#freightModal"></a><div class="resizer"></div></th>
                                            <th class="resizable text-center" width="80px" scope="col" >Custom <a class="icon icon-outline-book text-dark" data-bs-toggle="modal" data-bs-target="#customModal"></a><div class="resizer"></div></th>
                                            <th class="resizable text-center" width="80px"><?php echo app('translator')->getFromJson('Taxable'); ?><div class="resizer"></div></th>
                                            <th class="resizable text-center" width="100px"><?php echo app('translator')->getFromJson('VAT'); ?><div class="resizer"></div></th>
                                            <th class="resizable text-center" width="100px"><?php echo app('translator')->getFromJson('Total'); ?><div class="resizer"></div></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(isset($edit_pi_items) && count($edit_pi_items) > 0): ?>
                                         <?php $i=1; $po_qty=0; $qty=0; $executed_qty=0; $balance_qty=0; $unitprice=0; $value=0; $discount=0; $fright=0; $custom=0; $taxableamount = 0; $vatamount = 0; $total = 0; $grn_qty=0; ?>
                    <?php if(count($edit_pi_items)>0): ?>
                        <?php $__currentLoopData = $edit_pi_items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><input type="text" class="form-control text-center" name="sort_id[]" value="<?php echo e($i); ?>" />
                                <input type="hidden" ame="product_type[]" value="<?php echo e($items->product_type); ?>" />
                                <input type="hidden" name="item_po_id[]" value="<?php echo e($items->po_id); ?>" />
                                <input type="hidden" name="item_id[]" value="<?php echo e($items->id); ?>" />
                                <input type="hidden" name="part_number_txt[]" value="<?php echo e($items->partno); ?>" />
                            </td>
                            <td>
                                <select class="form-control noborder " name="part_number[]">
                                    <option value="<?php echo e($items->part_number); ?>"><?php echo e($items->partno ?? 0); ?></option>
                                </select>
                            </td>
                            <td>
                        <textarea class="form-control" name="description[]" rows="1"><?php echo e($items->description ?? 0); ?></textarea></td>
                            
                        <?php if(session('logged_session_data.company_id')==2): ?>
                        <td><?php echo e($items->hscode); ?></td>
                        <?php endif; ?>

                            <td style="display: none;"><input type="text" class="form-control" id="po_qty_<?php echo e($i); ?>" name="po_qty[]" value="<?php echo e($items->po_qty); ?>" /></td>
                            <td><input type="text" class="form-control text-center" name="tax[]" value="<?php echo e(number_format($items->tax ?? 0,0,'.',',')); ?>" onchange="calc_change_new(this)"/></td>
                            <td><input type="text" class="form-control text-center" name="qty[]" value="<?php echo e($items->qty); ?>"  onkeypress="set_license_key_po(<?php echo e($i); ?>)" onchange="calc_change_new(this)"/></td>
                            <td style="display: none;"><input type="text" class="form-control" name="grn_qty[]" value="<?php echo e($items->grn_qty); ?>" /></td>
                            <td style="display: none;"><input type="text" class="form-control" name="balance_qty[]" value="<?php echo e(abs($items->po_qty - $items->grn_qty)); ?>" readonly /></td>
                            <td><input type="text" class="form-control text-end" step="Any" id="unitprice_<?php echo e($i); ?>" name="unitprice[]" value="<?php echo e(@App\SysHelper::com_curr_format($items->unitprice,2,'.',',')); ?>" onchange="calc_change_new(this)" onblur="formatCurrency(this)"/></td>
                            <td><input type="text" class="form-control text-end" name="value[]" value="<?php echo e(@App\SysHelper::com_curr_format($items->value,2,'.',',')); ?>" onchange="calc_change_new(this)"/></td>
                            <td><input type="text" class="form-control text-end" name="discount[]" value="<?php echo e(@App\SysHelper::com_curr_format($items->discount,2,'.',',')); ?>" onchange="calc_change_new(this)" onblur="formatCurrency(this)"/></td>
                            <td><input type="text" class="form-control text-end" name="fright[]" value="<?php echo e(@App\SysHelper::com_curr_format($items->fright,2,'.',',')); ?>" onchange="calc_change_new(this)" onblur="formatCurrency(this)"/></td>
                            <td><input type="text" class="form-control text-end" name="customcharges[]" value="<?php echo e(@App\SysHelper::com_curr_format($items->customcharges,2,'.',',')); ?>" onchange="calc_change_new(this)" onblur="formatCurrency(this)"/></td>
                            
                            <td><input type="text" class="form-control text-end" name="taxableamount[]" value="<?php echo e(@App\SysHelper::com_curr_format($items->taxableamount,2,'.',',')); ?>" readonly/></td>
                            <td><input type="text" class="form-control text-end" name="vatamount[]" value="<?php echo e(@App\SysHelper::com_curr_format($items->vatamount,2,'.',',')); ?>" readonly/></td>
                            <td><input type="text" class="form-control text-end" name="totalamount[]" value="<?php echo e(@App\SysHelper::com_curr_format($items->taxableamount+$items->vatamount, 2, '.', ',')); ?>" readonly/></td>
                            
                            
                        </tr>
                        
                        <?php
                        $po_qty += $items->po_qty;
                        $qty += $items->qty;
                        $grn_qty += $items->grn_qty;
                        $balance_qty += abs($items->po_qty - $items->grn_qty);
                        $unitprice += $items->unitprice;
                        $value += $items->value;
                        $discount += $items->discount;
                        $fright += $items->fright;
                        $custom += $items->customcharges;
                        $taxableamount += $items->taxableamount;
                        $vatamount += $items->vatamount;
                        $total += $items->taxableamount+$items->vatamount;
                        $i++;
                        ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                    <?php endif; ?>
                    <tr>
                                            <td><input type="text" class="form-control text-center" name="sort_id[]" value="<?php echo e($i); ?>" /></td>
                                            <td class="noborder">
                                                <select class="form-control noborder " name="part_number[]">
                                                </select>
                                                
                                                <input type="hidden" name="item_id[]" value="0" />
                                                <input type="hidden" name="item_po_id[]" value="<?php echo e($edit_pi_items[0]->pi_id); ?>" />
                                            </td> 
                                            <td>                                                                    
                        <textarea class="form-control" name="description[]" rows="1"></textarea>
                                                <input class="form-control" type="text" name="part_number_txt[]" autocomplete="off" readonly="true" hidden>
                                                <input class="form-control" type="text" name="hscode_txt[]" autocomplete="off" readonly="true" hidden>
                                                <input class="form-control" type="text" name="product_type[]" autocomplete="off" readonly="true" hidden>
                                                <input class="form-control" type="text" name="product_type_part_number_text[]" autocomplete="off" readonly="true" hidden>                                            
                                            </td>
                                            <td><input type="number" class="form-control text-center" name="tax[]" onchange="calc_change_new(this)"></td>
                                            <td><input class="form-control text-center" type="number" name="qty[]" autocomplete="off" min="0" onchange="calc_change_new(this)" onkeypress="set_license_key()"></td>
                                            <td><input class="form-control text-end" type="text" name="unitprice[]" step="any" autocomplete="off" min="0" onchange="calc_change_new(this)" onblur="formatCurrency(this)"></td>
                                            <td><input class="form-control text-end" type="text" name="value[]" autocomplete="off" min="0" readonly></td>
                                            <td><input class="form-control text-end" type="text" name="discount[]" autocomplete="off" min="0" onchange="calc_change_new(this)" onblur="formatCurrency(this)"></td>
                                            <td><input class="form-control text-end" type="text" name="fright[]" autocomplete="off" min="0" onchange="calc_change_new(this)" onblur="formatCurrency(this)"></td>
                                            <td><input class="form-control text-end" type="text" name="customcharges[]" autocomplete="off" min="0" onchange="calc_change_new(this)" onblur="formatCurrency(this)"></td>
                                            <td><input class="form-control text-end" type="text" name="taxableamount[]" autocomplete="off" min="0" readonly></td>
                                            <td><input class="form-control text-end" type="text" name="vatamount[]" autocomplete="off" min="0" readonly></td>
                                            <td><input class="form-control text-end" type="text" name="totalamount[]" autocomplete="off" min="0" readonly></td>
                                        </tr>

                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="4" scope="col" >Total</th>
                                            <th class="text-center"><label id="lbl_total_qty" >0</label></th>
                                            <th class="text-end" scope="col" ><label id="lbl_total_price" >0</label></th>
                                            <th class="text-end" scope="col" ><label id="lbl_total_value" >0</label></th>
                                            <th class="text-end" scope="col" ><label id="lbl_total_discount" >0</label></th>
                                            <th class="text-end" scope="col" ><label id="lbl_total_fright" >0</label></th>
                                            <th class="text-end" scope="col" ><label id="lbl_total_customcharges" >0</label></th>
                                            <th class="text-end" scope="col" ><label id="lbl_total_taxableamount" >0</label></th>
                                            <th class="text-end" scope="col" ><label id="lbl_total_vatamount" >0</label></th>
                                            <th class="text-end" scope="col" ><label id="lbl_total_totalamount" >0</label></th>
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
                    <th style="width:50px;" class="text-center"><?php echo app('translator')->getFromJson('Date'); ?></th>
                    <th style="width:70px;" class="text-center"><?php echo app('translator')->getFromJson('Bill No'); ?></th>
                    <th style="width:100px;" class="text-center"><?php echo app('translator')->getFromJson('Name'); ?></th>
                    <th style="width:150px;" class="text-center"><?php echo app('translator')->getFromJson('Credit Account'); ?></th>
                    <th style="width:70px;" class="text-center"><?php echo app('translator')->getFromJson('Amount'); ?></th>
                    <th style="width:100px;" class="text-center"><?php echo app('translator')->getFromJson('Remarks'); ?>
                        <?php $cfcCount = isset($edit_cfc) ? $edit_cfc->count() : 0; ?>
                        <input type="hidden" value="<?php echo e($cfcCount > 0 ? $cfcCount : 1); ?>" id="fright_row" />
                        <!-- header plus clones last row -->
                        <a style="cursor: pointer;" data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                            data-bs-content="Add new freight charge row"
                            data-bs-placement="bottom" class="btn-md float-right" onclick="add_fright()"><i class="ico icon-outline-add-square text-success"></i></a></th>
                </tr>

            </thead>
            <tbody>
                <?php if(isset($edit_cfc) && $edit_cfc->count() > 0): ?>
                    <?php $__currentLoopData = $edit_cfc; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cfc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr id="fright_row_<?php echo e($loop->iteration); ?>">
                            <td>
                                <input class="form-control date-picker" type="text" id="cfc_date_<?php echo e($loop->iteration); ?>" name="cfc_date[]"  
                                    autocomplete="off" value="<?php echo e($cfc->date ? \Carbon\Carbon::parse($cfc->date)->format('d/m/Y') : ''); ?>">
                            </td>
                            <td>
                                <input class="form-control" type="text" id="cfc_bill_no_<?php echo e($loop->iteration); ?>" name="cfc_bill_no[]"  
                                    autocomplete="off" value="<?php echo e($cfc->bill_number); ?>">
                            </td>
                            <td>
                                <select class="form-control js-example-basic-single" name="cfc_name[]" id="cfc_name_<?php echo e($loop->iteration); ?>">
                                    <option value=""></option>
                                    <?php $__currentLoopData = $customs_freight_account; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($value->id); ?>" <?php echo e($cfc->cfc_name == $value->id ? 'selected' : ''); ?>><?php echo e($value->account_name); ?>  <?php if(@App\SysHelper::getCompanyCodeSettings()['is_account_code']): ?>
                                             (<?php echo e(@$value->account_code); ?>)
                                            
                                        <?php endif; ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </td>
                            <td>
                                <select class="form-control js-example-basic-single" name="cfc_credit_account[]" id="cfc_credit_account_<?php echo e($loop->iteration); ?>" readonly="true">
                                    <option value="none"></option>
                                    <?php $__currentLoopData = $vendors2; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($value->id); ?>" <?php echo e($cfc->cfc_credit_account == $value->id ? 'selected' : ''); ?>><?php echo e($value->account_name); ?>  <?php if(@App\SysHelper::getCompanyCodeSettings()['is_supplier_code']): ?>
                                             (<?php echo e(@$value->account_code); ?>)
                                            
                                        <?php endif; ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </td>
                            <td>
                                <input class="form-control text-end" type="text" id="cfc_amount_<?php echo e($loop->iteration); ?>" name="cfc_amount[]" autocomplete="off" min="0" value="<?php echo e(@App\SysHelper::com_curr_format($cfc->cfc_amount,'','',',')); ?>">
                            </td>
                            <td>
                                <input class="form-control" type="text" id="cfc_remarks_<?php echo e($loop->iteration); ?>" name="cfc_remarks[]" 
                                    autocomplete="off" value="<?php echo e($cfc->cfc_remarks); ?>">
                                <!-- per-row copy icon -->
                               
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
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
                                <?php $__currentLoopData = $customs_freight_account; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($value->id); ?>"><?php echo e($value->account_code); ?> - <?php echo e($value->account_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </td>
                        <td>
                            <select class="form-control js-example-basic-single" name="cfc_credit_account[]" id="cfc_credit_account_1" readonly="true">
                                <option value="none"></option>
                                <?php $__currentLoopData = $vendors2; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($value->id); ?>"> <?php echo e($value->account_name); ?>  <?php if(@App\SysHelper::getCompanyCodeSettings()['is_supplier_code']): ?>
                                             (<?php echo e(@$value->account_code); ?>)
                                            
                                        <?php endif; ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                <?php endif; ?>
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
    <?php echo e(Form::close()); ?>



        <div class="row mt-3">
                    <div class="col-lg-12 text-left mb-2">
                        <b>Adjusted Items</b>
                            <table class="table table-hover " id="long-list" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th style="width:50px;"><?php echo app('translator')->getFromJson('#'); ?></th>
                                        <th style="width:100px;"><?php echo app('translator')->getFromJson('Doc Number'); ?></th>
                                        <th style="width:100px;"><?php echo app('translator')->getFromJson('Doc Date'); ?></th>
                                        <th style="width:100px;"><?php echo app('translator')->getFromJson('LPO NO'); ?></th>
                                        <th style="width:100px;"><?php echo app('translator')->getFromJson('Bill NO'); ?></th>
                                        <th style="width:100px;" class="text-end">Total</th>
                                        <th style="width:100px;" class="text-end">Paid</th>
                                        <th style="width:100px;" class="text-end">Balance</th>
                                        <th style="width:100px;" class="text-end">Adjusted</th>
                                        <th style="width:100px;" class="text-end">Unadjusted</th>
                                        <th style="width:100px;" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                        <?php if(count($adj_list)>0): ?>
                                <?php $__currentLoopData = $adj_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e(@$loop->iteration); ?></td>
                                        <td><?php echo e(@$item->bi_doc_no); ?></td>
                                        <td><?php echo e(date('d/m/Y', strtotime(@$item->bi_doc_date))); ?></td>
                                        <td><?php echo e(@$item->bi_lpo_no); ?></td>
                                        <td><?php echo e(@$item->bi_bill_number); ?></td>
                                        <td class="text-end"><?php echo e(@App\SysHelper::com_curr_format(@$item->bi_total,2,'.',',')); ?></td>
                                        <td class="text-end"><?php echo e(@App\SysHelper::com_curr_format(@$item->bi_paid,2,'.',',')); ?></td>
                                        <td class="text-end"><?php echo e(@App\SysHelper::com_curr_format(@$item->bi_balance_to_adjust,2,'.',',')); ?></td>
                                        <td class="text-end"><?php echo e(@App\SysHelper::com_curr_format(@$item->bi_amount,2,'.',',')); ?></td>
                                        <td class="text-end"><?php echo e(@App\SysHelper::com_curr_format(@$item->bi_cheque_amount - @$item->bi_amount_adjusted,2,'.',',')); ?></td>
                                        <td class="text-center"><a class="btn-sm btn-light" onclick="return delete_adjestments(<?php echo e($item->id); ?>);"><i class="ico ico ico icon-outline-trash-bin-minimalistic text-darkphp -S text-dark" style="font-size: 16px;"></i></a></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                                </tbody>
                            </table>
                    </div>
                </div>
<script>
                    
    function delete_adjestments(id) {
        var action = "<?php echo e(URL::to('delete-payment-adjustment-json')); ?>";

         if (!confirm('Are you sure you want to delete this item?')) {
        return false; // Cancelled
    }
        
        $("#loading_bg").css("display", "block");
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '<?php echo e(csrf_token()); ?>',
                id:id,
                doc_number : $('#doc_number').val(),

            },
            cache: false,
           success: function(dataResult) {
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                var getSelectedRows = "";
                var decimalPoint = <?php echo json_encode(session('logged_session_data.decimal_point'), 15, 512) ?>;

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
                    // alert('Adjustments Deleted Successfully');
                    // show toastr when deleted
                    toastr.success('Adjustments Deleted Successfully');
                    location.reload();
                } else {
                    $('#adjustment-table tbody').empty();
                    location.reload();
                    //alert('Error: Something went wrong!');
                }
                $("#loading_bg").css("display", "none");
                $('#btn_adj_close').click();
            }
        });
    }
    </script>
                            

<!-- <a data-bs-toggle="modal" data-bs-target="#editModal"></a> -->

					<?php echo $__env->make('backEnd.inventory.itemAddModal', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

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

        <div class="modal side-panel fade" id="serialNoModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
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

        <div class="modal side-panel fade" id="adjustmentModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg draggable" > 
              	<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title" id="editModalLabel">Unadjusted List</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
                    <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'purchase-invoice-update-adjustment', 'method' => 'POST', 'enctype' => 'multipart/form-data'])); ?>

					<div class="modal-body m-0 p-0">
						<div class="card mb-0 mt-0">
							<div class="card-body">
                                <div class="row">
                                    <div class="col-12 mb-2">
                                        <table class="table" id="adjustment_table" style="border: solid 1px #e3e6f0; width:auto; width:100%;">
                                <thead>
                                    <tr>
                                        <th class="border">Doc Date</th>
                                        <th class="border">Payment No</th>
                                        <th class="border">Account Name</th>
                                        <th class="border text-end">Amount</th>
                                        <th class="border text-end">Adjusement</th>
                                    </tr>
                                </thead>
                                <tbody>


                                    <?php if(count($list_of_unadjusted) > 0): ?>
                                    <?php $__currentLoopData = $list_of_unadjusted; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                         <td class="border"><?php echo e(date('d/m/Y', strtotime(@$p->doc_date))); ?></td>
                                        <td class="border"><a href="<?php echo e(url('get-url-payment/' . @$p->doc_number)); ?>" target="_blank"><?php echo e(@$p->doc_number); ?></a></td>
                                        <td class="border"><?php echo e(@$p->account_name); ?></td>
                                        <td class="border text-end"><?php echo e(@$p->amount-@$p->adj_amount); ?></td>
                                        <td class="border text-end"><input type="text" name="set_amt[]" id="set_amt_<?php echo e(@$p->doc_number); ?>" class="form-control text-end" id="" name="" value="<?php echo e(@$p->adj_amount); ?>" onclick="set_adjust('<?php echo e(@$p->amount-@$p->adj_amount); ?>','<?php echo e(@$p->doc_number); ?>')" />
                                            <input type="hidden" name="paymentno[]" value="<?php echo e(@$p->doc_number); ?>"/>
                                            <input type="hidden" name="set_amt_act[]" value="<?php echo e(@$p->amount-@$p->adj_amount); ?>"/>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                    <?php if(count($list_of_unadjusted_pdc) > 0): ?>
                                    <?php $__currentLoopData = $list_of_unadjusted_pdc; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                         <td class="border"><?php echo e(date('d/m/Y', strtotime(@$p->doc_date))); ?></td>
                                        <td class="border"><a href="<?php echo e(url('get-url-payment/' . @$p->doc_number)); ?>" target="_blank"><?php echo e(@$p->doc_number); ?></a></td>
                                        <td class="border"><?php echo e(@$p->account_name); ?></td>
                                        <td class="border text-end"><?php echo e(@$p->amount-@$p->adj_amount); ?></td>
                                        <td class="border text-end"><input type="text" name="set_amt[]" id="set_amt_<?php echo e(@$p->doc_number); ?>" class="form-control text-end" id="" name="" value="<?php echo e(@$p->adj_amount); ?>" onclick="set_adjust('<?php echo e(@$p->amount-@$p->adj_amount); ?>','<?php echo e(@$p->doc_number); ?>')" />
                                            <input type="hidden" name="paymentno[]" value="<?php echo e(@$p->doc_number); ?>"/>
                                            <input type="hidden" name="set_amt_act[]" value="<?php echo e(@$p->amount-@$p->adj_amount); ?>"/>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                                    </div>
                                </div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
                        <input type="hidden" id="adj_sup_id" name="adj_sup_id" value="<?php echo e($edit_pi->vendors); ?>"/>
                        <input type="hidden" id="adj_piv_id" name="adj_piv_id" value="<?php echo e($edit_pi->id); ?>"/>
                        <input type="hidden" id="adj_piv_no" name="adj_piv_no" value="<?php echo e($edit_pi->doc_number); ?>"/>
                        <input type="hidden" id="adj_piv_date" name="adj_piv_date" value="<?php echo e($edit_pi->pi_date); ?>"/>
                        <input type="hidden" id="adj_piv_amount" name="adj_piv_amount" value="<?php echo e($adjusted_amt); ?>"/>
                        <input type="hidden" id="adj_piv_amount_actual" name="adj_piv_amount_actual" value="<?php echo e($adjusted_amt_actual); ?>"/>
                        <input type="hidden" id="adj_piv_amount_adjusted" name="adj_piv_amount_adjusted" value="0"/>
						<button type="submit" class="btn btn-light add-btn ms-2" id="discount_add_btn">
							<i class="ico icon-outline-bookmark-opened text-success"></i> Adjust
						</button>
					</div>
                <?php echo e(Form::close()); ?>

              	</div>
            </div>
        </div>


<script>
$(document).on("keydown", 'input[name="qty[]"], input[name="unitprice[]"], input[name="discount[]"], input[name="serial_no[]"]', function(e) {
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

    var decimal_point = <?php echo json_encode(session('logged_session_data.decimal_point'), 15, 512) ?>;

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

    const decimal_point = <?php echo json_encode(session('logged_session_data.decimal_point'), 15, 512) ?>;

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
                url: '<?php echo e(route("autocomplete.get_supp_account_list_ajax")); ?>',
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
                url: '<?php echo e(route("autocomplete.get_product_list_ajax")); ?>',
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
            $row.find('input[name="fright[]"]').val(0);
            $row.find('input[name="customcharges[]"]').val(0);
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



        <div class="modal side-panel fade" id="attachment_popup_win" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
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
                                <label class="dynamicslbl">  <?php echo app('translator')->getFromJson('Attach File'); ?> <span>*</span> </label>
                                <input class="form-control" type="file" id="att_file" name="att_file" onchange="updateDocName()"/>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-20">
                            <div class="input-effect">
                                <label class="dynamicslbl">  <?php echo app('translator')->getFromJson('Date'); ?> <span>*</span> </label>
                                <input class="form-control" type="date" id="att_date" name="att_date" value="<?php echo e(date('Y-m-d')); ?>"/>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-20">
                            <div class="input-effect">
                                <label class="dynamicslbl">  <?php echo app('translator')->getFromJson('File Name'); ?> <span>*</span> </label>
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
						<button type="button" class="btn btn-light add-btn ms-2" onclick="add_attachment()">
							<i class="ico icon-outline-bookmark-opened text-success"></i> Add Attachment
						</button>
					</div>
        </div>
    </div>
</div>
<script>
    function add_attachment(){
        $("#loading_bg").css("display", "block");

        if($('#att_file').val()==""){ $('#att_file').focus(); $("#loading_bg").css("display", "none"); return false; }

        var action = "<?php echo e(URL::to('add-purchase-invoice-attachment')); ?>";
        
        var formData = new FormData();
        formData.append('_token', '<?php echo e(csrf_token()); ?>');  // Append CSRF token
        formData.append('doc_id', $('#pi_id').val());
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
                                <td><a onclick='delete_attachment("+dataResult['data'][i].id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
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

        var action = "<?php echo e(URL::to('view-purchase-invoice-attachment')); ?>";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '<?php echo e(csrf_token()); ?>',
                doc_id : $('#pi_id').val(),
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
                                <td><a onclick='delete_attachment("+dataResult['data'][i].id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
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
        var action = "<?php echo e(URL::to('delete-purchase-invoice-attachment')); ?>";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '<?php echo e(csrf_token()); ?>',
                id : id,
                doc_id : $('#pi_id').val(),
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
                                <td><a onclick='delete_attachment("+dataResult['data'][i].id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
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



<!-- Modal Change Currancy-->
        <div class="modal side-panel fade" id="ModalChangeCurrancy" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog    modal-lg draggable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">Change Currancy</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'purchase-invoice-update-currency', 'method' => 'POST', 'enctype' => 'multipart/form-data'])); ?>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Currancy From</label>
                                <select class="form-control" name="from_currency_id" required>
                                    <?php $__currentLoopData = $currency; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($edit_pi->currency == $value->id): ?>
                                            <option value="<?php echo e(@$value->id); ?>" ><?php echo e(@$value->code); ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Currancy To</label>
                                <select class="form-control" name="to_currency_id" id="to_currency_id" required onchange="set_rate()">
                                    <option value="">Select</option>
                                    <?php $__currentLoopData = $currencylist2; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e(@$value->id); ?>"><?php echo e(@$value->code); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__currentLoopData = $currencylist2; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <input type="hidden" id="rate_<?php echo e(@$value->id); ?>" name="rate_<?php echo e(@$value->id); ?>" value="<?php echo e(@$value->rate); ?>" />
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                    <input type="hidden" name="cur_pi_id" value="<?php echo e(@$edit_pi->id); ?>"/>
                    <input type="hidden" name="cur_pi_doc_no" value="<?php echo e(@$edit_pi->doc_number); ?>"/>
						<button type="submit" class="btn btn-light add-btn ms-2">
							<i class="ico icon-outline-bookmark-opened text-success"></i> Change
						</button>
                </div>
                <?php echo e(Form::close()); ?>

            </div>
        </div>
    </div>
    <!-- Modal Change Currancy-->

        <script src="<?php echo e(asset('public/js/form-validation-toastr.js')); ?>"></script>
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
            var action = "<?php echo e(URL::to('get-chartofaccounts-info')); ?>";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '<?php echo e(csrf_token()); ?>',
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

<?php }catch (\Exception $e) { ?> <?php echo e($e); ?> <?php  } ?>
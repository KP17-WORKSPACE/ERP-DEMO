    <?php try { ?>

    <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'goods-receipt-note-update', 'method' => 'POST', 'id' => 'goods-receipt-note-update','novalidate' => true])); ?>

    <input type="hidden" name="url" id="url" value="<?php echo e(URL::to('/')); ?>">
    <input type="hidden" id="grn_id" name="id" value="<?php echo e(isset($grn) ? $grn->id : ''); ?>">
    <input type="hidden" name="grn_po_id" id="grn_po_id" value="<?php echo e($grn->po_id); ?>">
    <input type="hidden" id="company_id" value="<?php echo e(session('logged_session_data.company_id')); ?>" />
    <input type="hidden" name="doc_number_main" id="doc_number_main" value="<?php echo e($grn->doc_number); ?>">
    <input type="hidden" name="net_vat" id="net_vat" value="<?php echo e(optional(optional($grn->accountname)->cust_suppl)->vat_percentage ?? 0); ?>">


    <div class="purchase-order-content-header">
        <h4 class="purchase-order-content-header-left">
            Edit - <?php echo e(@$grn->doc_number); ?> 
        </h4>
        <div class="purchase-order-content-header-right">

        

            <a type="submit" class="btn btn-light text-dark" href="<?php echo e(url('goods-receipt-note-list/' . @$grn->id)); ?>?grn_action=add">
                <i class="ico icon-outline-add-square text-success"></i> Add
            </a>

            <button type="submit" class="btn btn-light">
                <i class="ico icon-outline-bookmark-square text-warning"></i> Update
            </button>
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ico icon-outline-hamburger-menu"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="<?php echo e(url('goods-receipt-note/' . $grn->id . '/delete')); ?>"><i
                                class="ico icon-outline-trash-bin-minimalistic text-danger"></i> Cancel GRN</a></li>
                    <li><a class="dropdown-item" href="<?php echo e(url('goods-receipt-note/' . $grn->id . '/download')); ?>"><i
                                class="ico icon-outline-document-medicine text-success"></i> Download</a></li>
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
                        
                        <select class="form-control"  name="vendors" id="vendors" onchange="get_pending_po_list()">
                          
                           
                            <?php $__currentLoopData = $vendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e(@$value->id); ?>" <?php if(isset($grn) && $grn->vendors == $value->id): ?> selected <?php endif; ?>>
                                 <?php echo e(@$value->account_name); ?> <?php if(@App\SysHelper::getCompanyCodeSettings()['is_supplier_code']): ?>
                                            (<?php echo e(@$value->account_code); ?>)
                                            <?php endif; ?>
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>



                    </div>
                </div>
                <div class="col-2">
                    <label class="form-label">GRN Number</label>
                    <div class="form-group">
                        <input class="form-control" type="text" name="doc_number" autocomplete="off" id="doc_number"
                            value="<?php echo e(@$grn->doc_number); ?>" readonly>
                    </div>
                </div>
                <div class="col-2">
                    <label class="form-label">GRN Date</label>
                    <div class="form-group">
                        <?php
                            $value = !empty($grn->grn_date)
                                ? \Carbon\Carbon::parse($grn->grn_date)->format('d/m/Y')
                                : '';
                        ?>

                        <input class="form-control date-picker" id="grn_date" type="text" autocomplete="off"
                            name="grn_date" value="<?php echo e(@$value); ?>">
                    </div>
                </div>
                <div class="col-2">
                    <label class="form-label">Currency<a style="float: right;" data-bs-target="#ModalChangeCurrancy" data-bs-toggle="modal"><i class="ico icon-outline-pen-2"></i></a></label>
                    <div class="form-group"><select class="form-control" name="currency" id="currency">
                            
                            <?php $__currentLoopData = $currency; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e(@$value->id); ?>" <?php if($grn->currency_id == $value->id): ?> selected <?php endif; ?>>
                                    <?php echo e(@$value->code); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>

                    </div>
                </div>
                <div class="col-2">
                    <label class="form-label">Created By</label>
                    <div class="form-group">
                        <input readonly type="text" class="form-control" name="createdby" id="createdby"
                            value="<?php echo e($grn->createdby->full_name); ?>">

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
                           
                            
                            <div class="col-2">
                                <label class="form-label">Bill Number</label>
                                <div class="form-group">
                                    <input class="form-control" type="text" name="bill_number" autocomplete="off"
                                        id="bill_number" required
                                        value="<?php echo e(isset($grn) ? (!empty(@$grn->bill_number) ? @$grn->bill_number : old('bill_number')) : ''); ?>">
                                </div>
                            </div>
                            <div class="col-2">
                                <label class="form-label">Bill Date</label>
                                <div class="form-group">
                                      <?php
                            $value = !empty($grn->bill_date)
                                ? \Carbon\Carbon::parse($grn->bill_date)->format('d/m/Y')
                                : '';
                        ?>
                                    <input class="form-control date-picker" id="bill_date" type="text" autocomplete="off"
                                        name="bill_date" value="<?php echo e(@$value); ?>" style="margin-top: 0px;">
                                </div>
                            </div>

                               <div class="col-2">
                                <label class="form-label">LPO Number</label>
                                <div class="form-group">
                                    <input
                                        class="txtbx primary-input form-control <?php echo e($errors->has('lpo_number') ? ' is-invalid' : ''); ?>"
                                        type="text" name="lpo_number" autocomplete="off" id="lpo_number"
                                        value="<?php echo e(isset($grn) ? (!empty(@$grn->lpo_number) ? @$grn->lpo_number : old('lpo_number')) : ''); ?>">
                                </div>
                            </div>
                            <div class="col-2">
                                <label class="form-label">LPO Date</label>
                                <div class="form-group">
                                       <?php
                            $value = !empty($grn->lpo_date)
                                ? \Carbon\Carbon::parse($grn->lpo_date)->format('d/m/Y')
                                : '';
                        ?>
                                    <input
                                        class="txtbx primary-input form-control date-picker"
                                        type="text" name="lpo_date" autocomplete="off" id="lpo_date"
                                        value="<?php echo e($value); ?>">
                                </div>
                            </div>

                            <div class="col-2">
                                <label class="form-label">Payment Terms</label>
                                <div class="form-group">
                                    <select class="form-control js-example-basic-single" name="payment_terms" id="payment_terms"
                                         required>
                                        <option value=""></option>
                                        <?php $__currentLoopData = $paymentterms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e(@$value->id); ?>"
                                                <?php echo e(isset($grn) ? (!empty(@$grn->payment_terms) ? (@$grn->payment_terms == @$value->id ? 'selected' : '') : '') : ''); ?>>
                                                <?php echo e(@$value->title); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                       

                                </div>
                                <div id="div_payment_terms" style="display: none; padding-top: px;">
                                    <div class="input-effect">
                                        <label class="form-label">Other Payment Terms</label>
                                        <input
                                            class="txtbx primary-input form-control <?php echo e($errors->has('payment_terms2') ? ' is-invalid' : ''); ?>"
                                            type="text" name="payment_terms2" autocomplete="off"
                                            id="payment_terms2"
                                            value="<?php echo e(isset($grn) ? (!empty(@$grn->payment_terms2) ? @$grn->payment_terms2 : old('payment_terms2')) : ''); ?>">
                                    </div>
                                </div>
                            </div>

                             <div class="col-2">
                                <label class="form-label">Deal ID</label>
                                <div class="form-group">
                                    <input class="form-control" type="text" name="deal_id" autocomplete="off"
                                        value="<?php echo e(isset($grn) ? (!empty(@$grn->deal_id) ? @App\SysHelper::get_code_from_dealid_list($grn->deal_id) : old('deal_id')) : old('deal_id')); ?>"
                                        id="deal_id">
                                </div>
                            </div>

                              <div class="col-2">
                                <label class="form-label">Customer Reference</label>

                                                   <?php
$selectedCompanies = $grn->ref_company_id
    ? explode(',', $grn->ref_company_id)
    : [];

$selectedCompanyNames = [];
foreach ($customer_reference_list as $company) {
    if (in_array($company->id, $selectedCompanies)) {
        $selectedCompanyNames[] = $company->name;
    }
}
?>

                                          <!-- Visible text input (opens modal for multi-select) -->
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
                                                <option value="<?php echo e(@$value->id); ?>" <?php if(@$deal->cust_id == @$value->id): ?> selected <?php endif; ?>><?php echo e(@$value->name); ?> <?php if(@App\SysHelper::getCompanyCodeSettings()['is_customer_code']): ?> (<?php echo e(@$value->code); ?>) <?php endif; ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                    <div class="modal-footer">
                                        
                                        <button type="button" id="save_customer_reference" class="btn btn-light"><i class="ico icon-outline-bookmark-opened text-success"></i> Save</button>
                                    </div>
                                </div>
                            </div>
                        </div>


                                <div class="form-group">
                 
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

                                    <!-- <select class="form-control js-example-basic-single" name="ref_company_id[]" id="ref_company_id" multiple required>
                            <option value="">-Select-</option>
                            <?php $__currentLoopData = $customer_reference_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e(@$value->id); ?>"   <?php if(in_array($value->id, $selectedCompanies)): ?> selected <?php endif; ?> ><?php echo e(@$value->name); ?> 
                                <?php if(@App\SysHelper::getCompanyCodeSettings()['is_customer_code']): ?>
                                        (<?php echo e(@$value->code); ?>)
                                        <?php endif; ?>
                            </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select> -->
                                    <input class="form-control" type="hidden" name="reference" autocomplete="off"
                                        value="<?php echo e(isset($grn) ? (!empty(@$grn->reference) ? @$grn->reference : old('reference')) : old('reference')); ?>"
                                        id="reference">
                                </div>
                            </div>

                            <div class="col-2">
                                <label class="form-label">Sales Person</label>
                                <div class="form-group">
                                     <select class="form-control js-example-basic-single" required name="sales_person"
                                id="sales_person">
                                <option value=""></option>
                                    <?php $__currentLoopData = $salesman; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e(@$value->user_id); ?>" <?php if($grn->sales_person == $value->user_id): ?> selected <?php endif; ?>><?php echo e(@$value->full_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                    
                                    <?php if(isset($grn) && $grn->sales_person): ?>
                                        <?php
                                            $selectedId = $grn->sales_person;
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
                                    <?php elseif(isset($grn) && !is_null($grn->sales_person_name) && $grn->sales_person_name !== ''): ?>
                                        
                                        <option value="<?php echo e($grn->sales_person_name); ?>" selected><?php echo e($grn->sales_person_name); ?></option>
                                    
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
                                       <?php
                                        $warehouses = App\SysHelper::getCompanyWarehouses();
                                        ?>
                                <div class="form-group">
                                     <select class="form-control js-example-basic-single" required name="warehouse" id="warehouse">
                                       
                                        <?php $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e(@$value->id); ?>" <?php if(@$grn->warehouse == $value->id): ?> selected
                                                
                                            <?php endif; ?>><?php echo e(@$value->warehouse_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        
                                    </select>
                                </div>
                            </div>

                              <div class="col-2">
                                <label class="form-label">BOE No</label>
                                <div class="form-group">
                                    <input
                                        class="txtbx primary-input form-control <?php echo e($errors->has('boeno') ? ' is-invalid' : ''); ?>"
                                        type="text" name="boeno" autocomplete="off"
                                        value="<?php echo e(isset($grn) ? (!empty(@$grn->boeno) ? @$grn->boeno : old('boeno')) : old('boeno')); ?>"
                                        id="boeno">
                                </div>
                            </div>

                            <div class="col-2">
                                <label class="form-label">AWB No</label>
                                <div class="form-group">
                                    <input
                                        class="txtbx primary-input form-control <?php echo e($errors->has('awbno') ? ' is-invalid' : ''); ?>"
                                        type="text" name="awbno" autocomplete="off"
                                        value="<?php echo e(isset($grn) ? (!empty(@$grn->awbno) ? @$grn->awbno : old('awbno')) : old('awbno')); ?>"
                                        id="awbno">
                                </div>
                            </div>
                          
                          
                            
                            <div class="col-2">
                                <label class="form-label">Remarks</label>
                                <div class="form-group">
                                    <input data-bs-toggle="modal" data-bs-target="#narrationModal"
                                        class="form-control" type="text" name="narration" autocomplete="off"
                                        value="<?php echo e(isset($grn) ? (!empty(@$grn->narration) ? @$grn->narration : old('narration')) : old('narration')); ?>"
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
                                <option value=""></option>
                                <?php $__currentLoopData = $customer; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $s = @App\SysHelper::internal_transfer_customer_id(@$value->id, session('logged_session_data.company_id')); ?>
                                    
                                    <option value="<?php echo e(@$value->id); ?>" <?php echo e($s); ?> <?php if($grn->shipping_supplier == $value->id): ?> selected <?php endif; ?>>
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
                                value="<?php echo e($grn->shipping_name); ?>" />
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Contact Email</label>
                        <div class="form-group">
                            <input type="text" class="form-control" name="shipping_email" id="shipping_email"
                                value="<?php echo e($grn->shipping_email); ?>" />
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Contact No</label>
                        <div class="form-group">
                            <input type="text" class="form-control" name="shipping_contact_no"
                                id="shipping_contact_no" value="<?php echo e($grn->shipping_contact_no); ?>" />
                        </div>
                    </div>
                    <div class="col-3">
                        <label class="form-label">Shipping Address</label>
                        <div class="form-group">
                            <input type="text" class="form-control" name="shipping_address_1"
                                id="shipping_address_1" value="<?php echo e($grn->shipping_address_1); ?>" />
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
                                <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e(@$value->id); ?>" <?php        try {?>
                                        <?php if(isset($grn)): ?> <?php if(@$grn->supplier_country == $value->id): ?> selected <?php endif; ?>
                                        <?php endif; ?> <?php        } catch (\Throwable $th) {
                                    } ?>><?php echo e(@$value->name); ?> </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            
                        </div>
                    </div>

                    
                       <div class="col mb-2">
                    <div class="input-effect">
                        <label class="form-label"><?php echo app('translator')->getFromJson('Supplier State'); ?> <span></span></label>

                        <div id="sectionStateDiv">
                         <select class="form-control js-example-basic-single" name="supplier_state" id="state">
                            <option value=""></option>

                            <?php $__currentLoopData = $states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option 
                                    value="<?php echo e($value->id); ?>" <?php if( $grn->supplier_state == $value->id): ?> selected <?php endif; ?>>
                                    <?php echo e($value->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>

                        </div>

                    </div>
                    </div>


                            <div class="col">
                        <label class="form-label">VAT %</label>
                        <div class="form-group">
                          
                           <input class="form-control" type="number"  name="vat_percent" id="vat_percent" value="<?php echo e(@$grn->vat_percent); ?>">
                        </div>
                    </div>

                    
                         <div class="col">
                        <label class="form-label">VAT Number</label>
                        <div class="form-group">
                          
                           <input class="form-control" type="number"  name="vat_number" id="vat_number" value="<?php echo e(@$grn->vat_number); ?>">
                        </div>
                    </div>

                    <div class="col">
                        <label class="form-label">Supplier Type</label>
                        <div class="form-group">
                            <select class="form-control js-example-basic-single <?php echo e($errors->has('supplier_type') ? ' is-invalid' : ''); ?>"
                                name="supplier_type" id="supplier_type">
                                <option value="0"></option>
                                <?php $__currentLoopData = $suppliertype; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e(@$value->id); ?>"
                                        <?php echo e(isset($grn) ? (!empty(@$grn->supplier_type) ? (@$grn->supplier_type == @$value->id ? 'selected' : '') : '') : ''); ?>>
                                        <?php echo e(@$value->title); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            
                        </div>
                    </div>
                    <div class="col">
                        <label class="form-label">Purchase Type</label>
                        <div class="form-group">
                            <select name="purchase_type" id="purchase_type"
                                class="form-control  js-example-basic-single  <?php echo e($errors->has('purchase_type') ? ' is-invalid' : ''); ?>"
                                id="inputVendorName">

                                <?php $__currentLoopData = $purchasetype; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e(@$value->id); ?>"
                                        <?php echo e(isset($grn) ? (!empty(@$grn->purchase_type) ? (@$grn->purchase_type == @$value->id ? 'selected' : '') : '') : ''); ?>>
                                        <?php echo e(@$value->title); ?>

                                    </option>
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
                    <th class="resizable text-center" width="30px"><?php echo app('translator')->getFromJson('No'); ?><div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="150px"><?php echo app('translator')->getFromJson('Part No'); ?><div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="280px"><?php echo app('translator')->getFromJson('Description'); ?><div class="resizer"></div>
                    </th>

                     <?php if(session('logged_session_data.company_id')==2): ?>      
                    <th class="resizable text-center" width="60px"><?php echo app('translator')->getFromJson('HS Code'); ?><div class="resizer"></div>
                    </th>
                      <?php endif; ?>

                    <th class="resizable text-center" width="30px"><?php echo app('translator')->getFromJson('Tax'); ?><div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="30px"><?php echo app('translator')->getFromJson('Qty'); ?><div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="80px"><?php echo app('translator')->getFromJson('Price'); ?><div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="80px"><?php echo app('translator')->getFromJson('Value'); ?><div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="80px" scope="col">Dis <a
                            class="icon icon-outline-book" data-bs-toggle="modal"
                            data-bs-target="#discountModal"></a>
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="80px" scope="col">Freight <a
                            class="icon icon-outline-book" data-bs-toggle="modal" data-bs-target="#freightModal"></a>
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="80px" scope="col">Custom <a
                            class="icon icon-outline-book" data-bs-toggle="modal" data-bs-target="#customModal"></a>
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="80px"><?php echo app('translator')->getFromJson('Taxable'); ?><div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="80px"><?php echo app('translator')->getFromJson('VAT'); ?><div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="100px"><?php echo app('translator')->getFromJson('Total'); ?><div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="80px"><?php echo app('translator')->getFromJson('Serial No'); ?><div class="resizer"></div>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php if(isset($grn_items) && count($grn_items) > 0): ?>
                    <?php
                        $i = 1;
                        $po_qty = 0;
                        $qty = 0;
                        $executed_qty = 0;
                        $balance_qty = 0;
                        $unitprice = 0;
                        $value = 0;
                        $discount = 0;
                        $fright = 0;
                        $custom = 0;
                        $taxableamount = 0;
                        $vatamount = 0;
                        $total = 0;
                        $grn_qty = 0;
                    ?>
                    <?php if(count($grn_items) > 0): ?>
                        <?php $__currentLoopData = $grn_items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><input type="text" class="form-control text-center" name="sort_id[]"
                                        value="<?php echo e($i); ?>" />
                                    <input type="hidden" id="product_type[]" name="product_type[]" value="<?php echo e($items->product_type); ?>" />
                                    <input type="hidden" name="item_po_id[]" value="<?php echo e($items->po_id); ?>" />
                                    <input type="hidden" name="part_number_txt[]"
                                        value="<?php echo e($items->part_number); ?>" />
                                    <input type="hidden" name="grn_line_item_id[]" value="<?php echo e($items->id); ?>" />
                                </td>
                                <td>
                                    <select class="form-control noborder " name="part_number[]">
                                        <option value="<?php echo e($items->part_no); ?>"><?php echo e($items->part_number ?? 0); ?></option>
                                    </select>
                                </td>



                                <td><textarea class="form-control" name="description[]" rows="1"><?php echo e($items->description ?? 0); ?></textarea></td>

                                <?php if(session('logged_session_data.company_id') == 2): ?>
                                    <td class="text-center">
                        <input class="form-control text-center" type="text" value="<?php echo e($items->hscode); ?>" name="hscode_txt[]" autocomplete="off" readonly="true">

                                        

                                    </td>
                                <?php endif; ?>

                                <td style="display: none;"><input type="text" data-enter-skip class="form-control"
                                        id="po_qty_<?php echo e($i); ?>" name="po_qty[]"
                                        value="<?php echo e($items->po_qty); ?>" /></td>
                                <td><input type="text" class="form-control text-center" name="tax[]"
                                        value="<?php echo e(number_format($items->tax ?? 0, 0, '.', '')); ?>"
                                        onchange="calc_change_new(this)" /></td>
                                <td><input type="text" class="form-control text-center" data-enter-skip   name="qty[]"
                                        value="<?php echo e($items->qty); ?>" onkeypress="return set_license_key(this, event)" onchange="calc_change_new(this)" /></td>
                                <td style="display: none;"><input type="text" class="form-control"
                                        name="grn_qty[]" value="<?php echo e($items->grn_qty); ?>" /></td>
                                <td style="display: none;"><input type="text" class="form-control"
                                        name="balance_qty[]" value="<?php echo e(abs($items->po_qty - $items->grn_qty)); ?>"
                                        readonly /></td>
                                <td><input type="text" class="form-control text-end" step="Any"
                                        id="unitprice_<?php echo e($i); ?>" name="unitprice[]" onblur="formatCurrency(this)"
                                        value="<?php echo e(@App\SysHelper::com_curr_format($items->unitprice, 2, '.', ',')); ?>"
                                        onchange="calc_change_new(this)" /></td>
                                <td><input type="text" class="form-control text-end" name="value[]" readonly
                                        value="<?php echo e(@App\SysHelper::com_curr_format($items->value, 2, '.', ',')); ?>"
                                        onchange="calc_change_new(this)" /></td>
                                <td><input type="text" class="form-control text-end" name="discount[]" onblur="formatCurrency(this)"
                                        value="<?php echo e(@App\SysHelper::com_curr_format($items->discount, 2, '.', ',')); ?>"
                                        onchange="calc_change_new(this)" /></td>
                                <td><input type="text" class="form-control text-end" name="fright[]" onblur="formatCurrency(this)"
                                        value="<?php echo e(@App\SysHelper::com_curr_format($items->fright, 2, '.', ',')); ?>"
                                        onchange="calc_change_new(this)" /></td>
                                <td><input type="text" class="form-control text-end" name="customcharges[]" onblur="formatCurrency(this)"
                                        value="<?php echo e(@App\SysHelper::com_curr_format($items->customcharges, 2, '.', ',')); ?>"
                                        onchange="calc_change_new(this)" /></td>

                                <td><input type="text" class="form-control text-end" name="taxableamount[]"
                                        value="<?php echo e(@App\SysHelper::com_curr_format($items->taxableamount, 2, '.', ',')); ?>"
                                        readonly /></td>
                                <td><input type="text" class="form-control text-end" name="vatamount[]"
                                        value="<?php echo e(@App\SysHelper::com_curr_format($items->vatamount, 2, '.', ',')); ?>"
                                        readonly /></td>
                                <td><input type="text" class="form-control text-end" name="totalamount[]"
                                        value="<?php echo e(@App\SysHelper::com_curr_format($items->taxableamount + $items->vatamount, 2, '.', ',')); ?>"
                                        readonly /></td>
                                <td>

                                    <?php
                                    $srno = $edit_list_srl->where('part_no', $items->part_no)->where('item_id', $items->id)->pluck('srl_no');
                                    $array = explode(',', trim($srno, '[""]'));
                                    $string = implode(', ', $array);
                                    
                                    if ($string != '') {
                                        $string = str_replace('"', '', $string);
                                    }
                                    ?>
                                    <input type="text" class="form-control" name="serial_no[]"
                                        value="<?php echo e($string); ?>" />
                                </td>

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
                                $total += $items->taxableamount + $items->vatamount;
                                $i++;
                            ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                <?php endif; ?>
                <tr>
                    <td><input type="text" class="form-control text-center" name="sort_id[]"
                            value="<?php echo e($i); ?>" />
                        <input type="hidden" name="grn_line_item_id[]" value="" />
                    </td>
                    <td class="noborder">
                        <select class="form-control noborder " name="part_number[]">
                        </select>
                        

                        <input type="hidden" name="item_po_id[]" value="<?php echo e($grn_items[0]->po_id); ?>" />
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
                    <td><input class="form-control text-center" type="number" name="qty[]" data-enter-skip autocomplete="off"
                            min="0" onchange="calc_change_new(this)" onkeypress="return set_license_key(this, event)"></td>
                    <td><input class="form-control text-end" type="text" name="unitprice[]" step="any" onblur="formatCurrency(this)"
                            autocomplete="off" min="0" onchange="calc_change_new(this)"></td>
                    <td><input class="form-control text-end" type="text" name="value[]" autocomplete="off"
                            min="0" readonly></td>
                    <td><input class="form-control text-end" type="text" name="discount[]" autocomplete="off" onblur="formatCurrency(this)"
                            min="0" onchange="calc_change_new(this)"></td>
                    <td><input class="form-control text-end" type="text" name="fright[]" autocomplete="off" onblur="formatCurrency(this)"
                            min="0" onchange="calc_change_new(this)"></td>
                    <td><input class="form-control text-end" type="text" name="customcharges[]" onblur="formatCurrency(this)"
                            autocomplete="off" min="0" onchange="calc_change_new(this)"></td>
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
                     <?php if(session('logged_session_data.company_id')==2): ?> 
                    <th class="text-center"></th>

                      <?php endif; ?>
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





<?php
       $r = @App\SysHelper::get_data_by_role();
            $company_id = $r[0];
$purchase_invoice = $grn->invoices->first();
if (empty($purchase_invoice)) {
    $purchase_invoice = @App\SysPurchaseInvoice::whereRaw('FIND_IN_SET(?, ref_grn_id)', [$grn->id])->first();
}


$customs_freight_account = @App\SysHelper::get_customs_freight_accounts_for_purchase($company_id);

$vendors2 = @App\SysHelper::get_supplier_list_all($company_id);

$edit_cfc = collect();
if (!empty($purchase_invoice) && !empty($purchase_invoice->id)) {
    $pi_cfc = @App\SysPurchaseInvoiceCFCharges::where('pi_id', '=', $purchase_invoice->id)->get();
    if ($pi_cfc->count() > 0) {
        $edit_cfc = $pi_cfc;
    } else {
        $edit_cfc = @App\SysPurchaseGrnCfCharges::where('grn_id', '=', $grn->id)->get();
    }
} else {
    $edit_cfc = @App\SysPurchaseGrnCfCharges::where('grn_id', '=', $grn->id)->get();
}


?>
<?php if(!empty($purchase_invoice) && !empty($purchase_invoice->id)): ?>
    <input type="hidden" name="cfc_pi_id" value="<?php echo e($purchase_invoice->id); ?>" />
<?php endif; ?>

<?php if(true): ?>

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
                                <select class="form-control js-example-basic-single" name="cfc_credit_account[]" id="cfc_credit_account_<?php echo e($loop->iteration); ?>">
                                    <option value=""></option>
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
                            <select class="form-control js-example-basic-single" name="cfc_credit_account[]" id="cfc_credit_account_1">
                                <option value=""></option>
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
                    <th colspan="4"><?php echo app('translator')->getFromJson('Total'); ?></th>
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
    
<?php endif; ?>







    <?php echo e(Form::close()); ?>



    
    <!-- <a data-bs-toggle="modal" data-bs-target="#editModal"></a> -->

    <?php echo $__env->make('backEnd.inventory.itemAddModal', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

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

<script>
$(document).on("keydown", 'input[name="unitprice[]"], input[name="discount[]"], input[name="serial_no[]"]', function(e) {
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
$('#goods-receipt-note-update').on('keypress', function (e) {
    if (e.which === 13 && !$(e.target).is('input[name="qty[]"]') && !$(e.target).is('input[name="unitprice[]"]')) {
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

            const decimal_point = <?php echo json_encode(session('logged_session_data.decimal_point'), 15, 512) ?>;

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
                        url: '<?php echo e(route('autocomplete.get_supp_account_list_ajax')); ?>',
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
        });
    </script>

    <script>
        $(document).ready(function() {
            function initAccountSelect2(selector) {
                $(selector).select2({
                    ajax: {
                        url: '<?php echo e(route('autocomplete.get_product_list_ajax')); ?>',
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
                    applyLicenseQtyHighlightForRow($row);
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
            if (typeof applyLicenseQtyHighlightForRow === 'function' && window.jQuery) {
                window.jQuery('#myTable > tbody > tr').each(function () {
                    applyLicenseQtyHighlightForRow(window.jQuery(this));
                });
            }
        };
        /*table row fill based on layout height*/
    </script>
    
    <!-- Modal Change Currancy-->
        <div class="modal side-panel fade" id="ModalChangeCurrancy" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm draggable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">Change Currancy</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'goods-receipt-note-update-currency', 'method' => 'POST', 'enctype' => 'multipart/form-data'])); ?>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Currancy From</label>
                                <select class="form-control" name="from_currency_id" required>
                                    <?php $__currentLoopData = $currency; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($grn->currency == $value->id): ?>
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
                    <input type="hidden" name="cur_grn_id" value="<?php echo e(@$grn->id); ?>"/>
                    <input type="hidden" name="cur_grn_doc_no" value="<?php echo e(@$grn->doc_number); ?>"/>
						<button type="submit" class="btn btn-light add-btn ms-2">
							<i class="ico icon-outline-bookmark-opened text-success"></i> Change
						</button>
                </div>
                <?php echo e(Form::close()); ?>

            </div>
        </div>
    </div>
    <!-- Modal Change Currancy-->

    <!-- Modal License Key-->
        <button id="btn_ModalLicenseKey" data-bs-target="#ModalLicenseKey" data-bs-toggle="modal" hidden></button>    
        <div class="modal side-panel fade" id="ModalLicenseKey" data-bs-backdrop="false" tabindex="-1" aria-labelledby="ModalLicenseKey" aria-hidden="true">
        <div class="modal-dialog modal-lg draggable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">License Key  <label style="margin-left: 117px"
                            id="ModalLabelHeading"></label></h4>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-light" data-bs-toggle="modal"
                            data-bs-target="#ModalExcelQuote" title="Import license keys from CSV or Excel">
                            Import
                        </button>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        <input type="hidden" id="part_number_id" />
                    </div>
                </div>


                <div class="modal-body mt-2">
                    <div class="row">
                        <div class="col-md-2">
                            <label for="" class="form-label">Qty</label>
                            <input type="hidden" id="item_id" />
                            <input type="hidden" id="license_row_index" value="" />
                            <input type="hidden" id="license_grn_line_item_id" value="" />
                            <input type="hidden" id="edit_license_id" value="" />
                            <input type="number" class="form-control" name="license_qty" id="license_qty"
                                value="1" readonly />
                            
                        </div>
                        <div class="col-md-5">
                            <label for="" class="form-label">License Key  (<span id="licenseCountSummary" class="text-muted small mt-2">Selected: 0 of 0</span>)</label>
                            <input type="text" class="form-control" name="license_key" id="license_key" />
                        </div>
                        <div class="col-md-3">
                            <label for="" class="form-label">Expiry Date</label>
                            <input type="text" class="form-control date-picker" name="exp_date" id="exp_date" />
                        </div>
                        <div class="col-md-2"><br />
                            <button type="button" id="license_add" class="btn btn-light"
                                onclick="return add_license_key()"><i class="ico icon-outline-add-square text-success me-1"></i>Add</button>
                            <button type="button" id="license_cancel_edit" class="btn btn-sm btn-outline-secondary ms-1"
                                onclick="cancel_license_edit()" style="display:none;" title="Cancel edit">&#x2715;</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mt-2">
                            <div id="licenseKeyMessage" class="text-danger small mb-2" style="display:none;"></div>
                            <table id="lk-table" class="table table-hover" width="100%"
                                cellspacing="0">
                                <thead>
                                    <tr>
                                        <th style="width: 8%;" class="text-center">Sr.No</th>
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
                        <button type="button" class="btn btn-light add-btn ms-2" onclick="return save_license_keys()">
                            <i class="ico icon-outline-bookmark-opened text-success"></i> Save &amp; Close
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
                                    <a href="<?php echo e(url('public/uploads/product_upload/grn_license_sample_format.csv')); ?>"
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
                $('#license_grn_line_item_id').val(($row.find('input[name="grn_line_item_id[]"]').val() || '').toString());
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

        let grnLicenseDrafts = [];

        function setLicenseAddButtonMode(mode) {
            if (mode === 'update') {
                $('#license_add').html('<i class="ico icon-outline-pen-2 me-1"></i>Update');
                return;
            }
            $('#license_add').html('<i class="ico icon-outline-add-square text-success me-1"></i>Add');
        }

        function getActiveLicenseTargetRow(itemId) {
            var $rows = $('#myTable > tbody > tr');
            var linePk = ($('#license_grn_line_item_id').val() || '').toString().trim();
            if (linePk !== '') {
                var $byPk = $('input[name="grn_line_item_id[]"]').filter(function () {
                    return String($(this).val()) === String(linePk);
                }).first().closest('tr');
                if ($byPk.length && $byPk.closest('#myTable').length) {
                    return $byPk;
                }
            }
            var rowIndex = parseInt($('#license_row_index').val(), 10);
            if (!isNaN(rowIndex) && rowIndex >= 0 && rowIndex < $rows.length) {
                var $byIdx = $rows.eq(rowIndex);
                if ($byIdx.length) {
                    if (!itemId || String($byIdx.find('select[name="part_number[]"]').val()) === String(itemId)) {
                        return $byIdx;
                    }
                }
            }
            var $matches = $rows.filter(function () {
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
            return (rows || []).map(function (row) {
                    return (row.license_key || '').toString().trim();
                })
                .filter(function (key) {
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
            (serialVal || '').toString().split(',').forEach(function (part) {
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

        function setDraftLicenseRows(rows) {
            grnLicenseDrafts = (rows || []).map(function (row, index) {
                return {
                    local_id: String(row.id || ('draft-' + index + '-' + Math.random().toString(36).substr(2, 5))),
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
                .map(function (row) {
                    return (row.license_key || '').toString().trim().toLowerCase();
                })
                .filter(Boolean);
        }

        function updateLicenseAddState() {
            var maxQty = getLicenseQty();
            var currentCount = getExistingLicenseKeys().length;
            $('#license_add').prop('disabled', maxQty <= 0 || currentCount >= maxQty);
            $('#licenseCountSummary').text('Selected: ' + currentCount + ' of ' + maxQty);
            if (maxQty <= 0) {
                showLicenseKeyMessage('License quantity must be greater than zero.', 'danger');
            } else {
                showLicenseKeyMessage('');
            }
        }

        function renderLicenseRows(rows) {
            var maxQty = getLicenseQty();
            var seen = {};
            var duplicates = [];
            var getSelectedRows = '';
            var normalized;
            var uniqueCount = 0;

            rows = rows || [];
            rows.forEach(function (row) {
                var licenseKey = (row.license_key || '').toString().trim();
                if (!licenseKey) {
                    return;
                }
                normalized = licenseKey.toLowerCase();
                if (seen[normalized]) {
                    duplicates.push(licenseKey);
                    return;
                }
                seen[normalized] = true;
                uniqueCount += 1;
                var safeKey = $('<div>').text(licenseKey).html();
                getSelectedRows += '<tr data-local-id="' + (row.local_id || row.id) + '" data-exp-date="' + $('<div>').text(row.exp_date || '').html() + '">' +
                    '<td class="text-center">' + uniqueCount + '</td>' +
                    '<td>' + safeKey + '</td>' +
                    '<td>' + formatLicenseDateForDisplay(row.exp_date) + '</td>' +
                    '<td style="white-space:nowrap;">' +
                        '<a onclick="edit_license_key_mode(\'' + (row.local_id || row.id) + '\', this)" class="btn-sm btn-light me-1" title="Edit"><i class="ico icon-outline-pen-2"></i></a>' +
                        '<a onclick="delete_license_key(\'' + (row.local_id || row.id) + '\')" class="btn-sm btn-light" title="Delete"><i class="ico icon-outline-trash-bin-trash"></i></a>' +
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
            return grnLicenseDrafts.findIndex(function (row) {
                return String(row.local_id) === String(localId);
            });
        }

        function edit_license_key_mode(localId, btn) {
            var index = findDraftRowIndex(localId);
            if (index === -1) {
                return;
            }
            var $row = $(btn).closest('tr');
            var row = grnLicenseDrafts[index];
            $('#edit_license_id').val(localId);
            $('#license_key').val(row.license_key).focus();
            $('#exp_date').val(formatLicenseDateForDisplay(row.exp_date));
            setLicenseAddButtonMode('update');
            $('#license_cancel_edit').show();
            $('#lk-table tbody tr').removeClass('table-warning');
            $row.addClass('table-warning');
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

        function add_license_key(){
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
                if (existingIndex !== -1 && grnLicenseDrafts[existingIndex] && String(grnLicenseDrafts[existingIndex].local_id) !== String(editId)) {
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
                id: null,
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

            var action = "<?php echo e(URL::to('add-grn-license-key-cart-excel')); ?>";
            var formData = new FormData();
            formData.append('_token', '<?php echo e(csrf_token()); ?>');
            formData.append('item_id', itemId);
            formData.append('license_qty', maxQty);
            formData.append('import_file', fileInput.files[0]);

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
            return false;
        }

        function view_license_key(){
            $("#loading_bg").css("display", "block");
            showLicenseKeyMessage('');
            var action = "<?php echo e(URL::to('view-grn-license-key')); ?>";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '<?php echo e(csrf_token()); ?>',
                    item_id : $('#item_id').val(),
                    grn_id : $('#grn_id').val(),
                },
                cache: false,
                success: function(dataResult) {
                    try {
                        var response = typeof dataResult === 'string' ? JSON.parse(dataResult) : dataResult;
                        setDraftLicenseRows(response.data || []);
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

        function delete_license_key(localId){
            var index = findDraftRowIndex(localId);
            if (index === -1) {
                showLicenseKeyMessage('Unable to remove this license key.', 'danger');
                return;
            }
            grnLicenseDrafts.splice(index, 1);
            renderLicenseRows(grnLicenseDrafts);
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

            if (getExistingLicenseKeys().length > maxQty) {
                showLicenseKeyMessage('Cannot save more than the allowed quantity of ' + maxQty + '.', 'danger');
                $("#loading_bg").css("display", "none");
                return false;
            }

            var action = "<?php echo e(URL::to('add-grn-license-key')); ?>";
            var rows = grnLicenseDrafts.map(function (row) {
                return {
                    license_key: row.license_key,
                    exp_date: normalizeLicenseDateForStore(row.exp_date),
                };
            });

            $.ajax({
                url: action,
                type: 'POST',
                data: {
                    _token: '<?php echo e(csrf_token()); ?>',
                    item_id: itemId,
                    license_qty: maxQty,
                    grn_id: $('#grn_id').val(),
                    rows: JSON.stringify(rows),
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
                        setDraftLicenseRows(response.data || []);
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

        $(function() {
            $('#myTable > tbody > tr').each(function () {
                applyLicenseQtyHighlightForRow($(this));
            });
            $(document).on('change', '#myTable tbody input[name="qty[]"]', function () {
                applyLicenseQtyHighlightForRow($(this).closest('tr'));
            });
            $(document).on('change input', '#myTable tbody input[name="serial_no[]"]', function () {
                applyLicenseQtyHighlightForRow($(this).closest('tr'));
            });
        });
    </script>
    <!-- Modal License Key-->


     <script src="<?php echo e(asset('public/js/form-validation-toastr.js')); ?>"></script>
    <script>
        $(document).ready(function() {
            // Initialize form validation for crm-deals-form
            FormValidator.init('goods-receipt-note-update', {
                showAllErrors: true,
                scrollToFirst: true,
                highlightFields: true,
                toastrPosition: 'toast-top-right',
                toastrTimeout: 6000
            });
        });
    </script>

    <?php }catch (\Exception $e) { ?> <?php echo e($e); ?> <?php  } ?>

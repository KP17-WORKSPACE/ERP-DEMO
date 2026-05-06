<?php try { ?>

<?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'sales-invoice-store', 'method' => 'POST', 'id' => 'sales-invoice-create-form', 'novalidate' => true,])); ?>

<input type="hidden" name="url" id="url" value="<?php echo e(URL::to('/')); ?>">
<input type="hidden" name="id" value="<?php echo e(isset($edit) ? $edit->id : ''); ?>">
<input type="hidden" id="net_vat" name="net_vat" >


<?php
    $invno = @App\SysHelper::get_new_sales_invoice_code();
                                                    ?>

<div class="purchase-order-content-header">
    <h4 class="purchase-order-content-header-left">
        New (<?php echo e(isset($edit) ? (!empty(@$edit->title) ? @$edit->title : old('doc_number')) : $invno); ?>)
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
                <label class="form-label">Customer</label>
                <div class="form-group">
                    <select class="form-control js-account-select" name="customer" id="customer"
                        onchange="get_pending_si_list()">
                        <?php if(isset($deal_acc)): ?>
                            <option value="<?php echo e($deal_acc->id); ?>">
                                <?php if(@App\SysHelper::getCompanyCodeSettings()['is_customer_code']): ?>
                                    <?php echo e($deal_acc->account_name); ?> (<?php echo e($deal_acc->account_code); ?>)
                                <?php else: ?>
                                    <?php echo e($deal_acc->account_name); ?>

                                <?php endif; ?>


                            </option>
                        <?php endif; ?>
                        <option value=""></option>

                        
                    </select>
                </div>
            </div>
            <div class="col-2">
                <label class="form-label">Doc Number</label>
                <div class="form-group">


                    <input class="form-control" type="text" name="doc_number" autocomplete="off" id="doc_number"
                        value="<?php echo e(isset($edit) ? (!empty(@$edit->title) ? @$edit->title : old('doc_number')) : $invno); ?>"
                        readonly>
                </div>
            </div>
            <div class="col-2">
                <label class="form-label">Invoice Date</label>
                <div class="form-group">
                    <?php
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
                    ?>

                    <input class="form-control date-picker" id="doc_date" type="text" autocomplete="off" name="doc_date"
                        value="<?php echo e(@$value); ?>" required>
                </div>
            </div>
            <div class="col-2">
                <label class="form-label">Currency</label>
                <div class="form-group"> <?php
    $currency1 = 1;
    if (session('logged_session_data.company_id') == 8) {
        $currency1 = 2;
    }
                                                ?>
                    <select class="form-control js-example-basic-single" name="currency" id="currency">
                        

                        <?php $__currentLoopData = $currency; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e(@$value->id); ?>" <?php if($company->currency_id == $value->id): ?> selected <?php endif; ?>>
                                <?php echo e(@$value->code); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
            <div class="col-2">
                <label class="form-label">Created By</label>
                <div class="form-group">
                    <input class="form-control" type="text" name="createdby" autocomplete="off" id="createdby"
                        value="<?php echo e(isset($edit) ? (!empty(@$edit->number) ? @$edit->number : old('createdby')) : Auth::user()->full_name); ?>"
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
                                    id="reference_no" value="<?php if(count($cart) > 0): ?> <?php echo e($cart[0]->reference_no); ?> <?php endif; ?>"
                                     required>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-2">
                            <div class="input-effect">
                                <label class="form-label">LPO/Reference Date<span>*</span></label>
                                <input class="form-control date-picker" type="text" name="reference_date"
                                    autocomplete="off" id="reference_date"
                                    value="<?php if(count($cart) > 0): ?> <?php echo e($cart[0]->reference_date); ?> <?php endif; ?>" required>
                            </div>
                        </div>
                         <div class="col-lg-3 mb-2">
                            <div class="input-effect">
                                <label class="form-label"><?php echo app('translator')->getFromJson('Payment Terms'); ?><span>*</span></label>
                                <div class="form-group">
                                    <select class="form-control js-example-basic-single" name="payment_terms"
                                        id="payment_terms" required>
                                        <option value=""></option>
                                        <?php $__currentLoopData = $paymentterms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e(@$value->id); ?>" <?php echo e(isset($edit) ? !empty(@$edit->payment_terms) ? @$edit->payment_terms == @$value->id ? 'selected' : '' : '' : ''); ?>

                                                <?php if(count($cart) > 0): ?> <?php if($cart[0]->payment_terms == @$value->id): ?> selected
                                                <?php endif; ?> <?php endif; ?>><?php echo e(@$value->title); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>


                                </div>

                            </div>
                            <div id="div_payment_terms" style="display: none; padding-top: px;">
                                <div class="input-effect">
                                    <label class="txtlbl"><?php echo app('translator')->getFromJson('Other Payment Terms'); ?><span>*</span></label>
                                    <input
                                        class="txtbx primary-input form-control <?php echo e($errors->has('payment_terms2') ? ' is-invalid' : ''); ?>"
                                        type="text" name="payment_terms2" autocomplete="off" id="payment_terms2"
                                        value="<?php echo e(isset($edit) ? (!empty(@$edit->payment_terms2) ? @$edit->payment_terms2 : old('payment_terms2')) : ''); ?>">
                                </div>
                            </div>
                        </div>
                         <div class="col-lg-3 mb-2">
                            <div class="input-effect">
                                <label class="form-label">Supplier Name<span>*</span></label>

                                <select class="form-control js-example-basic-single" name="ref_supplier_id[]"
                                    id="ref_supplier_id" multiple>
                                    <option value="">-Select-</option>

                                    <?php $__currentLoopData = $supplier_reference_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($value->id); ?>">
                                            <?php echo e($value->account_name); ?>

                                            <?php if(App\SysHelper::getCompanyCodeSettings()['is_supplier_code']): ?>
                                                (<?php echo e($value->account_code); ?>)
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <input class="form-control" type="hidden" name="supplier_name" autocomplete="off"
                                    id="supplier_name" value="TAKEN FROM STOCK" required>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-2">
                            <div class="input-effect">
                                <label class="form-label"><?php echo app('translator')->getFromJson('Sales Person Name'); ?><span>*</span></label>
                                <div class="form-group">
                                    <select class="form-control js-example-basic-single" required name="sales_man"
                                        id="sales_man" required>
                                        <option value=""></option>
                                        <?php $__currentLoopData = $staff; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            
                                            <option value="<?php echo e($value->user_id); ?>" <?php if(isset($edit) && $edit->sales_man == $value->user_id): ?> selected
                                            <?php elseif($value->user_id == Auth::user()->id): ?> selected
                                                <?php elseif(isset($deal_details) && $deal_details->owner == $value->user_id): ?>
                                                selected <?php endif; ?>>
                                                <?php echo e($value->full_name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>


                            </div>
                        </div>
                        <div class="col-lg-3 mb-2" style="display: none;">
                            <div class="input-effect">
                                <label class="form-label"><?php echo app('translator')->getFromJson('Delivery Terms'); ?><span>*</span></label>
                                <input class="form-control" type="text" name="delivery_terms" autocomplete="off"
                                    id="delivery_terms"
                                    value="<?php echo e(isset($edit) ? (!empty(@$edit->delivery_terms) ? @$edit->delivery_terms : old('delivery_terms')) : 'Ex-Dubai'); ?>"
                                    required>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-2">
                            <div class="input-effect">
                                <label class="form-label"><?php echo app('translator')->getFromJson('Printed Invoice Number'); ?><span></span></label>
                                <input class="form-control" type="text" name="printed_invoice_number" autocomplete="off"
                                    id="printed_invoice_number"
                                    value="<?php echo e(isset($edit) ? (!empty(@$edit->printed_invoice_number) ? @$edit->printed_invoice_number : old('printed_invoice_number')) : ''); ?>">
                            </div>
                        </div>
                        
                       
                        
                        <div class="col-lg-3 mb-2" id="div_deal_id" style="display: none;">
                            <div class="input-effect">
                                <label class="form-label">Deal ID<span>*</span></label>
                                <input class="form-control" type="text" name="deal_id" autocomplete="off" id="deal_id"
                                    value="Without Deal" required>
                            </div>
                        </div>
                       
                        <div class="col-lg-3 mb-2">
                            <div class="input-effect">
                                <label class="form-label"><?php echo app('translator')->getFromJson('Create Deal'); ?></label>
                                <div class="form-group">
                                    <select class="form-control js-example-basic-single" name="create_deal" id="create_deal" required
                                        onchange="create_deal_change()">
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
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
                                    $('#supplier_name').val('');
                                }
                            }
                        </script>
                        <div class="col-lg-3 mb-2">
                            <div class="input-effect">
                                <label class="form-label"><?php echo app('translator')->getFromJson('Create Delivery Note'); ?></label>
                                <div class="form-group">
                                    <select class="form-control" name="create_dn" id="create_dn" required>
                                        <option value="">Select</option>
                                        <option value="0" selected>No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                    <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>

                                </div>

                            </div>
                        </div>
                        <div class="col">
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
                <?php
                    $customer = @App\SysHelper::get_customer_supplier_list($company_id);
                    
                ?>
                        <label class="form-label">Company (Ship To)</label>
                        <div class="form-group">
                            <select class="form-control js-example-basic-single" name="shipping_supplier"
                                id="shipping_supplier" required style="width: 100%;">
                                <option value=""></option>
                                <?php $__currentLoopData = $customer; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    
                                    
                                    <option value="<?php echo e(@$value->id); ?>" 
                                        
                                        >
                                        <?php echo e(@$value->account_name); ?> 
                                        <?php if(@App\SysHelper::getCompanyCodeSettings()['is_customer_code']): ?>
                                        (<?php echo e(@$value->account_code); ?>)
                                        <?php endif; ?>
                                       
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                                value="<?php echo e(session('logged_session_data.full_name')); ?>" />
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Contact Email</label>
                        <div class="form-group">
                            <input type="text" class="form-control" name="shipping_email" id="shipping_email"
                                value="<?php echo e(session('logged_session_data.email')); ?>" />
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Contact No</label>
                        <div class="form-group">
                            <input type="text" class="form-control" name="shipping_contact_no"
                                id="shipping_contact_no" value="<?php echo e(session('logged_session_data.mobile')); ?>" />
                        </div>
                    </div>
                    <div class="col-3">
                        <label class="form-label">Shipping Address</label>
                        <div class="form-group">
                            <input type="text" class="form-control" name="shipping_address_1"
                                id="shipping_address_1" />
                        </div>
                    </div>

                <!-- <div class="col-lg-3 mb-2">
                    <div class="input-effect">
                        <label class="form-label"><?php echo app('translator')->getFromJson('Name'); ?> <span></span></label>
                        <input type="text" class="form-control"
                            value="<?php if(isset($deal_det)): ?> <?php echo e($deal_det->delivery_company); ?> <?php endif; ?>" id="shipping_name"
                            name="shipping_name">
                    </div>
                </div> -->
                <!-- <div class="col-lg-3 mb-2">
                    <div class="input-effect">
                        <label class="form-label"><?php echo app('translator')->getFromJson('Address'); ?> <span></span></label>
                        <input type="text" class="form-control"
                            value="<?php if(isset($deal_det)): ?> <?php echo e($deal_det->delivery_address); ?> <?php endif; ?>" id="shipping_address"
                            name="shipping_address">
                    </div>
                </div> -->
            </div>
        </div>
        <div class="tab-pane fade show" id="vat-details" role="tabpanel" aria-labelledby="vat-details-tab">
            <div class="row gap-rows">



                <div class="col-lg-2 mb-2">
                    <div class="input-effect">
                        <label class="form-label"><?php echo app('translator')->getFromJson('Customer Country'); ?> <span></span></label>
                        <select class="form-control js-example-basic-single" name="customer_country" id="country">
                            <option value=""></option>
                            <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e(@$value->id); ?>" <?php        try {?> <?php if(isset($deal_cust)): ?> <?php if(@$deal_cust->vat_country == $value->id): ?> selected <?php endif; ?> <?php endif; ?> <?php        } catch (\Throwable $th) {
                                } ?>><?php echo e(@$value->name); ?> </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>

                    </div>
                </div>


                <div class="col-lg-2 mb-2">
                    <div class="input-effect">
                        <label class="form-label"><?php echo app('translator')->getFromJson('Customer State'); ?> <span></span></label>

                        <div id="sectionStateDiv">
                            <select class="form-control js-example-basic-single" name="customer_state" id="state">
                                <option value=""></option>
                                <?php    try {?>
                                <?php $__currentLoopData = $states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(isset($deal_cust)): ?>
                                        <option data-display="<?php echo e($deal_cust->vatstate->name); ?>"
                                            value="<?php echo e($deal_cust->vat_state); ?>" selected>
                                            <?php echo e($deal_cust->vatstate->name); ?>

                                        </option>
                                    <?php else: ?>
                                        <option value="<?php echo e($value->id); ?>"><?php echo e($value->name); ?></option>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php    } catch (\Throwable $th) {
    } ?>
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
                        <label class="form-label"><?php echo app('translator')->getFromJson('Customer Type'); ?></label>
                        <div class="form-group">
                            <select class="form-control js-example-basic-single" name="customer_type"
                                id="customer_type">
                                <option value="0"></option>
                                <?php $__currentLoopData = $customertype; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e(@$value->id); ?>" <?php echo e(isset($deal_cust) ? !empty(@$deal_cust->customer_type) ? @$deal_cust->customer_type == @$value->id ? 'selected' : '' : '' : ''); ?>><?php echo e(@$value->title); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>


                        </div>

                    </div>
                </div>
                <div class="col-lg-2 mb-2">
                    <div class="input-effect">
                        <label class="form-label"><?php echo app('translator')->getFromJson('Sale Type'); ?></label>
                        <div class="form-group">
                            <select class="form-control js-example-basic-single" name="sale_type" id="sale_type">
                                <option value="0"></option>
                                <?php $__currentLoopData = $saletype; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e(@$value->id); ?>" <?php echo e(isset($deal_cust) ? !empty(@$deal_cust->sale_type) ? @$deal_cust->sale_type == @$value->id ? 'selected' : '' : '' : ''); ?>><?php echo e(@$value->title); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>


                        </div>

                    </div>
                </div>


            </div>
        </div>
        <div class="tab-pane fade show" id="end-user-details" role="tabpanel" aria-labelledby="end-user-details-tab">
            <div class="row gap-rows">
                <div class="col-lg-3 mb-2">
                    <div class="input-effect">
                        <label class="form-label"><?php echo app('translator')->getFromJson('End User Name'); ?> <span></span></label>
                        <input type="text" class="form-control" name="end_user_name" id="end_user_name"
                            autocomplete="off"
                            value="<?php if(isset($deal_enduser)): ?> <?php echo e($deal_enduser->end_user_company_name); ?> <?php endif; ?>" />

                    </div>
                </div>
                <div class="col-lg-3 mb-2">
                    <div class="input-effect">
                        <label class="form-label"><?php echo app('translator')->getFromJson('Contact Person Name'); ?> <span></span></label>
                        <input type="text" class="form-control" name="contact_person_name" id="contact_person_name"
                            autocomplete="off"
                            value="<?php if(isset($deal_enduser)): ?> <?php echo e($deal_enduser->end_user_contact_person); ?> <?php endif; ?>">

                    </div>
                </div>
                <div class="col-lg-3 mb-2">
                    <div class="input-effect">
                        <label class="form-label"><?php echo app('translator')->getFromJson('Contact Person Email'); ?> <span></span></label>
                        <input type="text" class="form-control" name="contact_person_email" id="contact_person_email"
                            autocomplete="off" value="<?php if(isset($deal_enduser)): ?> <?php echo e($deal_enduser->email); ?> <?php endif; ?>">

                    </div>
                </div>
                <div class="col-lg-3 mb-2">
                    <div class="input-effect">
                        <label class="form-label"><?php echo app('translator')->getFromJson('Contact Person No'); ?> <span></span></label>
                        <input type="text" class="form-control" name="contact_person_no" id="contact_person_no"
                            autocomplete="off" value="<?php if(isset($deal_enduser)): ?> <?php echo e($deal_enduser->mobile_no); ?> <?php endif; ?>">

                    </div>
                </div>
            </div>
        </div>
        <style>
            .venus-app .tab-wrap .tab-pane {
    padding: 15px;
    padding-bottom: 0;
}
        </style>

        <div class="d-flex justify-content-end" style="margin-right:16px;padding-bottom: 10px;">
            <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#ModalExcelQuote">
                <i class="ico icon-outline-import text-success" style="font-size: 16px"></i> Import
            </button>
        </div>
    </div>
</div>
<div class="table-container" style="border: solid 1px #d9d9d9;">
    <table class="table table-hover form-item-table" id="myTable">
        <thead>
            <tr>
                <th class="resizable text-center" width="30px"><?php echo app('translator')->getFromJson('No'); ?>
                    <div class="resizer"></div>
                </th>
                <th class="resizable text-center" width="210px"><?php echo app('translator')->getFromJson('Part No'); ?> <a
                        class="icon icon-outline-book text-dark" data-bs-toggle="modal"
                        data-bs-target="#addproductModal"></a>
                    <div class="resizer"></div>
                </th>
                <th class="resizable text-center" width="280px"><?php echo app('translator')->getFromJson('Description'); ?>
                    <div class="resizer"></div>
                </th>

                <?php if(count($cart) == 0): ?>
                    <th class="resizable text-center" width="50px"><?php echo app('translator')->getFromJson('Cost'); ?>
                        <div class="resizer"></div>
                    </th>
                <?php endif; ?>

                <th class="resizable text-center" width="30px"><?php echo app('translator')->getFromJson('Tax'); ?>
                    <div class="resizer"></div>
                </th>
                <th class="resizable text-center" width="30px"><?php echo app('translator')->getFromJson('Qty'); ?>
                    <div class="resizer"></div>
                </th>
                <th class="resizable text-center" width="80px"><?php echo app('translator')->getFromJson('Price'); ?>
                    <div class="resizer"></div>
                </th>
                <th class="resizable text-center" width="80px"><?php echo app('translator')->getFromJson('Value'); ?>
                    <div class="resizer"></div>
                </th>
                <th class="resizable text-center" width="80px" scope="col">Dis <a
                        class="icon icon-outline-book text-dark" data-bs-toggle="modal"
                        data-bs-target="#discountModal"></a>
                    <div class="resizer"></div>
                </th>
                <th class="resizable text-center" width="80px"><?php echo app('translator')->getFromJson('Taxable'); ?>
                    <div class="resizer"></div>
                </th>
                <th class="resizable text-center" width="80px"><?php echo app('translator')->getFromJson('VAT'); ?>
                    <div class="resizer"></div>
                </th>
                <th class="resizable text-center" width="100px"><?php echo app('translator')->getFromJson('Total'); ?>
                    <div class="resizer"></div>
                </th>
                <th class="resizable text-center" width="100px"><?php echo app('translator')->getFromJson('SRL No'); ?>
                    <div class="resizer"></div>
                </th>
            </tr>
        </thead>
        <tbody>

            <?php    $sort = 1; ?>

            <?php if(count($cart) > 0): ?>
                <?php $__currentLoopData = $cart; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                    <tr>
                        <td><input type="text" class="form-control text-center" name="sort_id[]" value="<?php echo e($sort); ?>" /></td>
                        <td class="noborder">
                            <select class="form-control noborder " name="part_number[]">
                                <option value="<?php echo e($items->part_number); ?>"><?php echo e($items->partno); ?></option>
                            </select>
                            
                        </td>
                        <td><textarea class="form-control" name="description[]" rows="1"><?php echo e($items->description); ?></textarea>
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
                                value="<?php echo e($items->tax); ?>"></td>
                        <td><input class="form-control text-center" type="number" name="qty[]" autocomplete="off" min="0"
                                onchange="calc_change_new(this)" value="<?php echo e($items->qty); ?>"></td>
                        <td><input class="form-control text-end" type="text" name="unitprice[]" step="any" autocomplete="off"
                                min="0" onchange="calc_change_new(this)" onblur="formatCurrency(this)"
                                value="<?php echo e(@App\SysHelper::com_curr_format($items->unitprice,2,'.',',')); ?>"></td>
                        <td><input class="form-control text-end" type="text" name="value[]" autocomplete="off" min="0" readonly
                                value="<?php echo e(@App\SysHelper::com_curr_format($items->value,2,'.',',')); ?>"></td>
                        <td><input class="form-control text-end" type="text" step="Any" name="discount[]" autocomplete="off"
                                min="0" onchange="calc_change_new(this)" onblur="formatCurrency(this)"
                                value="<?php echo e(@App\SysHelper::com_curr_format($items->discount,2,'.',',')); ?>"></td>
                        <td><input class="form-control text-end" type="text" name="taxableamount[]" autocomplete="off" min="0"
                                readonly value="<?php echo e(@App\SysHelper::com_curr_format($items->taxableamount,2,'.',',')); ?>"></td>
                        <td><input class="form-control text-end" type="text" name="vatamount[]" autocomplete="off" min="0"
                                readonly value="<?php echo e(@App\SysHelper::com_curr_format($items->vatamount,2,'.',',')); ?>"></td>
                        <td><input class="form-control text-end" type="text" name="totalamount[]" autocomplete="off" min="0"
                                readonly
                                value="<?php echo e(@App\SysHelper::com_curr_format($items->vatamount + $items->taxableamount,2,'.',',')); ?>">
                        </td>
                        <td><input class="form-control text-end" type="text" name="serial_no[]"></td>
                    </tr>

                    <?php            $sort++; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>


            <tr>
                <td><input type="text" class="form-control text-center" name="sort_id[]" value="<?php echo e($sort); ?>" /></td>
                <td class="noborder">
                    <select class="form-control noborder " name="part_number[]">
                    </select>
                    
                </td>
                <td><textarea class="form-control" name="description[]" rows="1"></textarea></td>

                <td style="<?php if(count($cart) > 0): ?> display:none; <?php endif; ?>">
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
                <th colspan="<?php if(count($cart) == 0): ?> 5 <?php else: ?> 4 <?php endif; ?>" scope="col">Total</th>
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
                        <th style="width:50px;" class="text-center"><?php echo app('translator')->getFromJson('Date'); ?></th>
                        <th style="width:70px;" class="text-center"><?php echo app('translator')->getFromJson('Bill No'); ?></th>
                        <th style="width:100px;" class="text-center"><?php echo app('translator')->getFromJson('Name'); ?></th>
                        <th style="width:150px;" class="text-center"><?php echo app('translator')->getFromJson('Credit Account'); ?></th>
                        <th style="width:70px;" class="text-center"><?php echo app('translator')->getFromJson('Amount'); ?></th>
                        <th style="width:100px;" class="text-center"><?php echo app('translator')->getFromJson('Remarks'); ?>
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
                                <?php $__currentLoopData = $customs_freight_account; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e(@$value->id); ?>"><?php echo e(@$value->account_name); ?>  <?php if(@App\SysHelper::getCompanyCodeSettings()['is_account_code']): ?>
                                             (<?php echo e(@$value->account_code); ?>)
                                            
                                        <?php endif; ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </td>
                        <td>
                            <select class="form-control js-example-basic-single" name="cfc_credit_account[]" id="cfc_credit_account_1"
                                readonly="true">
                                <option value="none"></option>
                                 <?php $__currentLoopData = $supplier; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e(@$value->id); ?>"><?php echo e(@$value->account_code); ?> - <?php echo e(@$value->account_name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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

<?php echo e(Form::close()); ?>





<!-- <a data-bs-toggle="modal" data-bs-target="#editModal"></a> -->

<?php echo $__env->make('backEnd.inventory.itemAddModal', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>


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
            //total_fright = 0,
            //total_customcharges = 0,
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


    //    const SHOW_CUSTOMER_CODE = <?php echo e(@App\SysHelper::getCompanyCodeSettings()['is_customer_code'] ? 'true' : 'false'); ?>;


    $(document).ready(function () {
        function initAccountSelect2(selector) {
            $(selector).select2({
                ajax: {
                    url: '<?php echo e(route("autocomplete.get_cust_account_list_ajax")); ?>',
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

        // Auto-open vendors dropdown on page load
        setTimeout(function () {
            $('#customer').select2('open');
        }, 500);


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
        var id = $('#customer').select2('val'); // or .val()
        get_cust_details(id);
        get_cust_details_arabic(id);
    }

    function get_cust_details(id) {
        $("#loading_bg").css("display", "block");
        var action = "<?php echo e(URL::to('get-customer-details')); ?>";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '<?php echo e(csrf_token()); ?>',
                id: id,
            },
            cache: false,
            success: function (dataResult) {
                var dataResult = JSON.parse(dataResult);
                console.log("Customer details fetched:", dataResult);
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
                        } else { $('#btnSubmit').css('display', ''); }
                        $('#payment_terms').val(dataResult['data'][i].payment_terms).trigger('change');
                        $('#shipping_supplier').val(dataResult['data'][i].account_id).trigger('change');

                        // $('#shipping_name').val(dataResult['data'][i].contcat_person);
                        // $('#shipping_address').val(dataResult['data'][i].address);
                        $('#customer_type').val(dataResult['data'][i].customer_type).trigger('change');
                        $('#sale_type').val(dataResult['data'][i].sale_type).trigger('change');
                        $('#country').val(dataResult['data'][i].vat_country).trigger('change');
                        // $('#state').val(dataResult['data'][i].vat_state).trigger('change');

                        window.SELECTED_STATE_ID = dataResult['data'][i].vat_state;


                        $('#net_vat').val(dataResult['data'][i].vat_percentage);
                        $('.vat').val(dataResult['data'][i].vat_percentage);
                        $('#vat_percent').val(dataResult['data'][i].vat_percentage);
                        $('#vat_number').val(dataResult['data'][i].vat_number);
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
        var action = "<?php echo e(URL::to('get-customer-details-arabic')); ?>";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '<?php echo e(csrf_token()); ?>',
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
        var action = "<?php echo e(URL::to('get-proforma-invoice-for-si')); ?>";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '<?php echo e(csrf_token()); ?>',
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
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-4 mb-20">
                            <div class="input-effect">
                                <label class="form-label"> <?php echo app('translator')->getFromJson('Attach File'); ?> <span>*</span> </label>
                                <input class="form-control" type="file" id="att_file" name="att_file"
                                    onchange="updateDocName()" />
                            </div>
                        </div>
                        <div class="col-lg-4 mb-20">
                            <div class="input-effect">
                                <label class="form-label"> <?php echo app('translator')->getFromJson('Date'); ?> <span>*</span> </label>
                                <input class="form-control" type="date" id="att_date" name="att_date"
                                    value="<?php echo e(date('Y-m-d')); ?>" />
                            </div>
                        </div>
                        <div class="col-lg-4 mb-20">
                            <div class="input-effect">
                                <label class="form-label"> <?php echo app('translator')->getFromJson('File Name'); ?> <span>*</span> </label>
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

        var action = "<?php echo e(URL::to('add-sales-invoice-attachment')); ?>";

        var formData = new FormData();
        formData.append('_token', '<?php echo e(csrf_token()); ?>');  // Append CSRF token
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
                                    <td>"+ Number(i + 1) + "</td>\
                                    <td>"+ get_format_date(dataResult['data'][i].doc_date) + "</td>\
                                <td><a href='../../"+ dataResult['data'][i].doc_file + "' target='_blank'>" + dataResult['data'][i].doc_name + "</a></td>\
                                    <td><button onclick='delete_attachment("+ dataResult['data'][i].id + ")' class='btn btn-sm btn-light text-white'><i class='ico icon-bold-trash-bin-2' style='font-size:16px' aria-hidden='true'></i></button></td>\
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

        var action = "<?php echo e(URL::to('view-sales-invoice-attachment')); ?>";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '<?php echo e(csrf_token()); ?>',
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
                                    <td>"+ Number(i + 1) + "</td>\
                                    <td>"+ get_format_date(dataResult['data'][i].doc_date) + "</td>\
                                <td><a href='../../"+ dataResult['data'][i].doc_file + "' target='_blank'>" + dataResult['data'][i].doc_name + "</a></td>\
                                    <td><button onclick='delete_attachment("+ dataResult['data'][i].id + ")' class='btn btn-sm btn-light text-white'><i class='ico icon-bold-trash-bin-2' style='font-size:16px' aria-hidden='true'></i></button></td>\
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
        var action = "<?php echo e(URL::to('delete-sales-invoice-attachment')); ?>";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '<?php echo e(csrf_token()); ?>',
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
                                    <td>"+ Number(i + 1) + "</td>\
                                    <td>"+ get_format_date(dataResult['data'][i].doc_date) + "</td>\
                                <td><a href='../../"+ dataResult['data'][i].doc_file + "' target='_blank'>" + dataResult['data'][i].doc_name + "</a></td>\
                                    <td><button onclick='delete_attachment("+ dataResult['data'][i].id + ")' class='btn btn-sm btn-light text-white'><i class='ico icon-bold-trash-bin-2' style='font-size:16px' aria-hidden='true'></i></button></td>\
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
<script>
    function get_adjustments() {
        $("#loading_bg").css("display", "block");

        $('#adj_siv_amount_actual').val($("input[name='totalamount[]']").val());
        $('#adj_cus_id').val($('#customer').val());

        var action = "<?php echo e(URL::to('sales-invoice-get-adjustment')); ?>";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '<?php echo e(csrf_token()); ?>',
                customer: $("#customer").val(),
            },
            cache: false,
            success: function (dataResult) {
                var data = JSON.parse(dataResult);
                // Handle 'unadjusted'
                if (data.unadjusted && data.unadjusted.length > 0) {
                    var getSelectedRows = "";
                    for (var i = 0; i < data.unadjusted.length; i++) {
                        var a = (data.unadjusted[i].amount - data.unadjusted[i].adj_amount).toFixed(<?php echo json_encode(session('logged_session_data.decimal_point'), 15, 512) ?>).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                        getSelectedRows += "<tr>\
                                         <td class='border'>"+ data.unadjusted[i].doc_date + "</td>\
                                         <td class='border'>"+ data.unadjusted[i].doc_number + "</td>\
                                         <td class='border'>"+ data.unadjusted[i].account_name + "</td>\
                                        <td class='border text-right'>"+ a + "</td>\
                                        <td class='border text-right'><input type='text' name='set_amt[]' id='set_amt_"+ data.unadjusted[i].doc_number + "' class='form-control text-right' value='' onclick=\"set_adjust('" + (data.unadjusted[i].amount - data.unadjusted[i].adj_amount) + "','" + data.unadjusted[i].doc_number + "')\" />\
                                            <input type='hidden' name='receiptno[]' value='"+ data.unadjusted[i].doc_number + "'/>\
                                            <input type='hidden' name='set_amt_act[]' value='"+ a + "'/>\
                                        </td>\
                                        </tr>";
                    }

                }

                // Handle 'unadjusted_pdc'
                if (data.unadjusted_pdc && data.unadjusted_pdc.length > 0) {
                    var getSelectedRows2 = "";
                    for (var j = 0; j < data.unadjusted_pdc.length; j++) {
                        getSelectedRows2 += "<tr>\
                                         <td class='border'>"+ data.unadjusted_pdc[i].doc_date + "</td>\
                                         <td class='border'>"+ data.unadjusted_pdc[i].doc_number + "</td>\
                                         <td class='border'>"+ data.unadjusted_pdc[i].account_name + "</td>\
                                        <td class='border text-right'>"+ (data.unadjusted_pdc[i].amount - data.unadjusted_pdc[i].adj_amount) + "</td>\
                                        <td class='border text-right'><input type='text' name='set_amt[]' id='set_amt_"+ data.unadjusted_pdc[i].doc_number + "' class='form-control text-right' value='" + data.unadjusted_pdc[i].adj_amount + "' onclick=\"set_adjust('" + (data.unadjusted_pdc[i].amount - data.unadjusted_pdc[i].adj_amount) + "','" + data.unadjusted[i].doc_number + "')\" />\
                                            <input type='hidden' name='receiptno[]' value='"+ data.unadjusted_pdc[i].doc_number + "'/>\
                                            <input type='hidden' name='set_amt_act[]' value='"+ (data.unadjusted_pdc[i].amount - data.unadjusted_pdc[i].adj_amount) + "'/>\
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
                url: "<?php echo e(url('sales-invoice-add-adjustment-cart')); ?>", // Replace with your actual route
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
                    console.log(xhr.responseText);
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
                <?php echo csrf_field(); ?>
                
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


<script src="<?php echo e(asset('public/js/form-validation-toastr.js')); ?>"></script>
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



<!-- Modal Excel Quote-->
    <div class="modal fade" id="ModalExcelQuote" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
             <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">Sales Invoice Items Excel Import</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
           

            <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'add-sales-invoice-items-excel-cart', 'method' => 'POST', 'id' => 'add-sales-invoice-items-excel-cart'])); ?>

            <input type="hidden" id="excel_deal_id" name="excel_deal_id" value="<?php echo e(@$edit->id); ?>" />
            <input type="hidden" id="excel_cust_id" name="excel_cust_id" value="<?php echo e(@$edit->cust_id); ?>" />
            <input type="hidden" id="excel_vat" name="excel_vat"
                value="<?php echo e(@$edit->customername->vat_percentage ?? 0); ?>" />
            <input type="hidden" id="excel_company_id" name="excel_company_id" value="" />
            <input type="hidden" id="excel_currency_id" name="excel_currency_id" value="" />
            <input type="hidden" id="excel_customer_type" name="excel_customer_type" value="" />
            <input type="hidden" id="excel_quote_validity" name="excel_quote_validity" value="" />
            <input type="hidden" id="excel_payment_terms" name="excel_payment_terms" value="" />
            <input type="hidden" id="excel_delivery_date" name="excel_delivery_date" value="" />
            <input type="hidden" id="excel_payment_terms_txt" name="excel_payment_terms_txt" value="" />
            <input type="hidden" id="excel_delivery_time" name="excel_delivery_time" value="" />

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
            </script>


            <div class="modal-body">
                <div class="row">
                    <div class="col-auto">
                        <label for="" class="form-label">Select File (.csv)</label>
                    </div>
                    <div class="col-auto">
                        <input class="form-control" type="file" id="excel-file" accept=".xlsx, .xls, .csv" />
                    </div>
                     <div class="col-auto">
                        <button type="button" onclick="readExcel()" class="btn btn-light">Preview</button>
                    </div>
                    <div class="col-auto">
                        
                        
                        (<a href="<?php echo e(url('public/uploads/product_upload/si_items_sample_format.csv')); ?>"
                            target="_blank">Sample File</a>)
                    </div>

                    <div class="col-md-12 mt-2">
                        <table id="excel-table" class="table table-bordered table-striped" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="width:220px;">Part No</th>
                                    <th>Description</th>
                                    <th style="width:70px;">Qty</th>
                                    <th style="width:100px;" class="text-right">Unit Price</th>
                                    <th style="width:100px;" class="text-right">Discount</th>
                                    <th style="width:100px;" class="text-right">VAT</th>
                                    <th style="width:100px;" class="text-right">Cost</th>
                                    <th style="width:50px;" class="text-right"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be inserted here -->
                            </tbody>
                        </table>

                        <?php
    $part_number = $items->pluck('part_number');
                        ?>
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>
                        <script>
                            function readExcel() {
                                add_excel_data();
                                var file = document.getElementById('excel-file').files[0];
                                if (!file) {
                                    alert("Please select an Excel file.");
                                    return;
                                }

                                var reader = new FileReader();
                                reader.onload = function (event) {
                                    var data = event.target.result;
                                    var workbook = XLSX.read(data, { type: 'binary' });

                                    // Assuming the data is in the first sheet
                                    var sheet = workbook.Sheets[workbook.SheetNames[0]];
                                    var rows = XLSX.utils.sheet_to_json(sheet, { header: 1 });

                                    var tableBody = document.getElementById('excel-table').getElementsByTagName('tbody')[0];
                                    tableBody.innerHTML = "";  // Clear any previous data

                                    // Loop through each row and add data to the table
                                    for (var i = 1; i < rows.length; i++) {  // Skip header row
                                        var row = rows[i];
                                        if (row.length < 6) continue;  // Skip invalid rows



                                        var part_number = <?php    echo json_encode($part_number); ?>; // Convert PHP array to JS array

                                        var lowercase_part_number = part_number.map(function (value) {
                                            return value.toLowerCase();
                                        });

                                        var json_output = JSON.stringify(lowercase_part_number);

                                        var newRow = tableBody.insertRow(tableBody.rows.length);

                                        var rowVal = String(row[0] ?? '');
                                        var trimmedValue = rowVal.trim();

                                        if (json_output.includes(trimmedValue.toLowerCase())) {  // Use .includes() for array checking

                                        } else {
                                            newRow.style.backgroundColor = "#ffbebe";
                                        }

                                        // Part No
                                        var partNoCell = newRow.insertCell(0);
                                        var partNoInput = document.createElement('input');
                                        partNoInput.type = 'text';  // Change to text input
                                        partNoInput.name = 'excel_part_no[]';
                                        partNoInput.value = rowVal.trim();
                                        partNoInput.classList.add('form-control');
                                        partNoCell.appendChild(partNoInput);

                                        // Description
                                        var descriptionCell = newRow.insertCell(1);
                                        var descriptionInput = document.createElement('input');
                                        descriptionInput.type = 'text';  // Change to text input
                                        descriptionInput.name = 'excel_description[]';
                                        descriptionInput.value = (row[1] || '').toString().trim();
                                        descriptionInput.classList.add('form-control');
                                        descriptionCell.appendChild(descriptionInput);

                                        // Qty
                                        var qtyCell = newRow.insertCell(2);
                                        var qtyInput = document.createElement('input');
                                        qtyInput.type = 'text';  // Change to text input
                                        qtyInput.name = 'excel_qty[]';
                                        qtyInput.value = row[2];
                                        qtyInput.classList.add('form-control');
                                        qtyCell.appendChild(qtyInput);

                                        // Unit Price (Right-aligned)
                                        var unitPriceCell = newRow.insertCell(3);
                                        var unitPriceInput = document.createElement('input');
                                        unitPriceInput.type = 'text';  // Change to text input
                                        unitPriceInput.name = 'excel_unit_price[]';
                                        unitPriceInput.value = row[3];
                                        unitPriceInput.classList.add('text-right');
                                        unitPriceInput.classList.add('form-control');
                                        unitPriceCell.appendChild(unitPriceInput);

                                        // Discount (Right-aligned)
                                        var discountCell = newRow.insertCell(4);
                                        var discountInput = document.createElement('input');
                                        discountInput.type = 'text';  // Change to text input
                                        discountInput.name = 'excel_discount[]';
                                        discountInput.value = row[4];
                                        discountInput.classList.add('text-right');
                                        discountInput.classList.add('form-control');
                                        discountCell.appendChild(discountInput);

                                        // VAT (Right-aligned)
                                        var vatCell = newRow.insertCell(5);
                                        var vatInput = document.createElement('input');
                                        vatInput.type = 'text';  // Change to text input
                                        vatInput.name = 'vat_excel[]';
                                        vatInput.value = row[5];
                                        vatInput.classList.add('text-right');
                                        vatInput.classList.add('form-control');
                                        vatCell.appendChild(vatInput);

                                        var costCell = newRow.insertCell(6);
                                        var costInput = document.createElement('input');
                                        costInput.type = 'text';  // Change to text input
                                        costInput.name = 'cost_excel[]';
                                        costInput.value = row[6];
                                        costInput.classList.add('text-right');
                                        costInput.classList.add('form-control');
                                        costCell.appendChild(costInput);

                                        var deleteCell = newRow.insertCell(7);  // Last cell for delete button
                                        var deleteButton = document.createElement('button');
                                        deleteButton.type = 'button';  // Make sure the button doesn't submit a form
                                       deleteButton.classList.add('btn-sm', 'btn-light');
                                                deleteButton.innerHTML = '<i class="ico icon-bold-trash-bin-2" style="font-size: 16px;"></i>';
                                        deleteButton.onclick = function () {
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
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-light add-btn ms-2">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                    </button>
                
            </div>
            <?php echo e(Form::close()); ?>

        </div>
    </div>
</div>
<!-- Modal Excel Quote-->

<script>


    $(document).ready(function() {


     $(document).on("change", "#shipping_supplier", function() {
            var id = $("#shipping_supplier").val();
            get_shipping_supplier_detail2(id);
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


<?php } catch (\Exception $e) { ?> <?php echo e($e); ?> <?php  } ?>
<?php try { ?>

  

<?php if(isset($edit)): ?>
    <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deals/' . $edit->id, 'method' => 'PUT', 'id' => 'crm-deals-form', 'novalidate' => true])); ?>

<?php else: ?>
    <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deals', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-deals-form', 'novalidate' => true])); ?>

<?php endif; ?>
<input type="hidden" name="url" id="url" value="<?php echo e(URL::to('/')); ?>">
<input type="hidden" name="id" value="<?php echo e(isset($edit) ? $edit->id : ''); ?>">
<input type="hidden" name="quote_id" value="<?php echo e($quote_id); ?>">
<input type="hidden" name="net_vat" id="net_vat" value="<?php echo e($edit->customername->vat_percentage); ?>">

  <style>
    .no-dim {
        pointer-events: none !important;
        opacity: 1 !important;

    }
                                        </style>
<style>
.form-item-table .select2-container--default .select2-selection--single{ border: none !important;}
.form-item-table.select2-container--default .select2-selection--single .select2-selection__arrow b { display: none !important; }
</style>

<?php
    $dealTrackForRecallHeader = App\SysCrmDealTrack::where('deal_id', $edit->id)->orderBy('id', 'desc')->first();
    $canRecallDealTrackHeader = !empty($dealTrackForRecallHeader) && ((int) ($dealTrackForRecallHeader->accounts ?? 0) !== 1);
?>
<div class="purchase-order-content-header sticky-top" style="background-color: #f7f8fd">
    <h4 class="purchase-order-content-header-left">
        Edit - <?php echo e($edit->code); ?>    <?php if($edit->stage == 4): ?> 

         <?php
                    $data = App\SysHelper::deal_track_status($edit->id);
                    $color = "danger";
                    if ($data == "Pending") {
                        $color = "warning";
                    } else if ($data == "completed") {
                        $color = "primary";
                    } else if ($data == "OnProcess") {
                        $color = "info";
                    } else {
                        $color = "danger";
                    }
            ?>
                                            

                                            <?php if(App\SysHelper::set_track($edit->id) == 1): ?>
                                                <a class="badge bg-<?php echo e($color); ?>  py-1 px-2 <?php if($data == "Fulfill"): ?> <?php else: ?> deal-track-sales-person <?php endif; ?>" <?php if($data == "Fulfill"): ?> href="<?php echo e(url('crm-deals/show/'.$edit->id.'?deal_action=edit')); ?>" <?php endif; ?>  data-id="<?php echo e($edit->id); ?>"  title="Click to Fullfill">
                                                 <?php echo e($data); ?> </a>
                                            <?php endif; ?>
                                            <?php endif; ?>
    </h4>
    <div class="purchase-order-content-header-right">

         <a class="btn btn-light text-dark" href="<?php echo e(url('crm-deals-add')); ?>">
                <i class="ico icon-outline-add-square text-success"></i> Add
            </a>

        <button type="submit" class="btn btn-light">
            <i class="ico icon-outline-bookmark-opened text-warning"></i> Update
        </button>
        <?php if($canRecallDealTrackHeader): ?>
            <button type="button" class="btn btn-light text-dark" id="btnRecallTop">
                <i class="ico icon-outline-close-circle text-danger"></i> Recall
            </button>
        <?php endif; ?>
        <div class="dropdown">
            <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button" data-bs-toggle="dropdown"
                aria-expanded="false">
                <i class="ico icon-outline-hamburger-menu"></i>
            </button>
            <ul class="dropdown-menu">

                
                               <li><button type="button" class="dropdown-item d-flex align-items-center text-dark"  data-bs-toggle="modal" data-bs-target="#staticBackdrop"><i
                            class="ico icon-bold-download-minimalistic text-success  title-15 me-2"></i> Download</button>
                </li>

                <?php if($edit->stage == 4 || $edit->stage == 1): ?>
                    <?php if(count($support)==0): ?>
                    <li><a type="button"  data-modal-size="modal-md" data-bs-target="#ModalSupport" data-bs-toggle="modal" class="dropdown-item d-flex align-items-center  text-dark"><i class="ico icon-outline-add-square text-success title-15 me-2"></i> Add Pre-Sales Request</a></li>
                    <?php else: ?>
                    <li><a type="button"  data-modal-size="modal-md" data-bs-target="#ModalSupportCmt" data-bs-toggle="modal" class="dropdown-item d-flex align-items-center text-dark"><i class="ico icon-outline-add-square text-success  title-15 me-2"></i> Add Pre-Sales Request Comments</a></li>
                    <?php endif; ?>
                <?php endif; ?>
                    <li><a type="button"  data-modal-size="modal-md" data-bs-target="#ModalCollaboration" data-bs-toggle="modal" class="dropdown-item d-flex align-items-center text-dark"><i class="ico icon-outline-add-square text-success  title-15 me-2"></i> Add Collaboration</a></li>
        <?php if($quotationitems->where('product_type', 2)->count() < 1): ?>
                   
                    <li><a type="button"  data-modal-size="modal-md" data-bs-target="#ModalEndUserDetails" data-bs-toggle="modal" class="dropdown-item d-flex align-items-center  text-dark"><i class="ico icon-outline-add-square text-success  title-15 me-2"></i> End User Details</a></li>
        <?php endif; ?>

   <?php if(!empty($edit->track) && !empty($edit->track->id)): ?>
                        <li>
                            <a target="__blank" 
                            href="<?php echo e(url('crm-deal-track-approval-list/' . $edit->track->id)); ?>" 
                            class="dropdown-item d-flex align-items-center text-dark">
                                <i class="ico icon-outline-document-text text-success title-15 me-2"></i>
                                Deal Track
                            </a>
                        </li>
                    <?php endif; ?>


            </ul>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade side-panel" id="staticBackdrop" data-bs-backdrop="false" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog draggable modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Download</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body d-flex justify-content-center align-items-center gap-3">
           <a href="<?php echo e(url('crm-quote/' . $edit->id . '/download/' . $edit->quote_id)); ?>" class="btn btn-light text-dark"> <i
                            class="ico icon-bold-download-minimalistic text-success" style="font-size:13px"></i> Business Proposal</a>
        <a href="<?php echo e(url('crm-quote-pdf/' . $edit->id)); ?>" class="btn btn-light text-dark"><i
                            class="ico icon-bold-download-minimalistic text-success" style="font-size:13px"></i> Quotation</a>
      </div>
     
    </div>
  </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <div class="row gap-rows">

              <div class="col-4">
                <label class="form-label">Customer</label>
                <div class="form-group">
                    <select class="form-control js-example-basic-single" name="cust_id" id="cust_id" required
                        onchange="change_cust_id()">
                        <option value=""></option>
                        <?php $__currentLoopData = $vendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                              <option value="<?php echo e(@$value->id); ?>" <?php if(@$edit->cust_id == $value->id): ?> selected <?php endif; ?>><?php echo e(trim(@$value->name)); ?><?php if(@App\SysHelper::getCompanyCodeSettings()['is_customer_code'] == 1): ?> (<?php echo e(trim(@$value->code)); ?>)<?php endif; ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>

            <div class="col-2">
                <label class="form-label">Deal Name</label>
                <div class="form-group">
                    <input class="form-control capitalize-title" type="text" name="deal_name" autocomplete="off" id="deal_name"
                        value="<?php echo e(isset($edit) ? (!empty(@$edit->deal_name) ? @$edit->deal_name : old('deal_name')) : old('deal_name')); ?>"
                        required>
                </div>
            </div>

           
          

            <div class="col-2">
                <label class="form-label">Est. Closing Date *</label>
                <div class="form-group">


                    <?php
                        @$value = @$edit->estimated_close_date;

                    ?>
                    <input class="form-control date-picker" id="estimated_close_date" type="text" autocomplete="off"
                        name="estimated_close_date" value="<?php echo e(@App\SysHelper::normalizeToDmy(@$value)); ?>" required>
                </div>
            </div>
           
            <style>
                .venus-app .input-group .input-group-text {
    background-color: transparent;
    /* border-radius: 8px; */
    font-size: 10px;
                }
            </style>


                <div class="col-lg-2">
                    <div class="input-effect">
                        <label class="form-label">Value<span>*</span></label>
                        <?php
                            $selectedCurrency = collect($currency)->firstWhere('id', $edit->deal_currency);
                            $dealCurrencyCode = $selectedCurrency ? $selectedCurrency->code : '';
                        ?>
                        <div class="input-group">
                            <span class="input-group-text" id="deal_value_currency"><?php echo e($dealCurrencyCode); ?></span>
                            <input class="form-control text-end" type="text" step="any" autocomplete="off"
                                id="deal_value_display" readonly disabled
                                value="<?php echo e(isset($edit) ? (!empty(@$edit->deal_value) ? @App\SysHelper::currancy_format($edit->deal_value, $edit->currency_id) : old('deal_value')) : old('deal_value')); ?>">
                        </div>
                    </div>
                </div>
            <div class="col-2 ">
                <label class="form-label">Deal Profit</label>
                <div class="input-group">
                    <span class="input-group-text" id="deal_profit_currency"><?php echo e($dealCurrencyCode); ?></span>
                    <input class="form-control text-end" type="text" autocomplete="off"
                        id="deal_profit_display"
                        value="<?php echo e(@App\SysHelper::currancy_format($edit->deal_profit, $edit->currency_id)); ?>" readonly disabled>
                </div>
            </div>


            





         


        </div>
    </div>
</div>


 <style>
                            .col-5-custom {
                                flex: 0 0 auto;
                                width: 20%;
                            }
                        </style>
<div class="tab-wrap mb-3">
    <ul class="nav nav-tabs" id="purchaseDetailsTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="extra-fields-tab" data-bs-toggle="tab" data-bs-target="#extra-fields"
                type="button" role="tab" aria-controls="extra-fields" aria-selected="true">Extra Fields</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link " id="delivery-fields-tab" data-bs-toggle="tab" data-bs-target="#delivery-fields"
                type="button" role="tab" aria-controls="delivery-fields" aria-selected="true">Delivery Location</button>
        </li>

        


                   <li class="nav-item" role="presentation">
            <button class="nav-link " id="salesperson-fields-tab" data-bs-toggle="tab" data-bs-target="#salesperson-fields"
                type="button" role="tab" aria-controls="salesperson-fields" aria-selected="true">Sales Person</button>
        </li>


        <li class="nav-item" role="presentation">
            <button class="nav-link " id="quote-fields-tab" data-bs-toggle="tab" data-bs-target="#quote-fields"
                type="button" role="tab" aria-controls="quote-fields" aria-selected="true">Quote</button>
        </li>

        <?php if($quotationitems->where('product_type', 2)->count() > 0): ?>
        
      <li class="nav-item" role="presentation">
            <button class="nav-link " id="enduser-fields-tab" data-bs-toggle="tab" data-bs-target="#enduser-fields"
                type="button" role="tab" aria-controls="enduser-fields" aria-selected="true">End User Details</button>
        </li>
        <?php endif; ?>

        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="editfullfill-fields-tab" data-bs-toggle="tab"
                data-bs-target="#editfullfill-fields" type="button" role="tab" aria-controls="editfullfill-fields"
                aria-selected="true">Edit Fullfill</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link " id="internal-fields-tab" data-bs-toggle="tab" data-bs-target="#internal-fields"
                type="button" role="tab" aria-controls="internal-fields" aria-selected="true">Internal Note</button>
        </li>
        <li class="nav-item ms-auto d-flex align-items-center ps-2" role="presentation">
            <input type="hidden" name="quotation_generated" id="quotation_generated"
                value="<?php echo e(request()->query('new') == 'yes' ? 1 : ((count($quotationitems) < 1) ? 0 : 1)); ?>">
            <?php if(count($quotationitems) < 1): ?>
                <button class="btn btn-sm btn-light add-btn" type="button" onclick="quote_generate()">
                    <i class="ico icon-bold-document-add text-success" style="font-size: 16px"></i>
                    <span>Generate Quotation</span>
                </button>
            <?php endif; ?>
        </li>
    </ul>
    <div class="tab-content mb-3" id="purchaseDetailsTabsContent">
        <div class="tab-pane fade " id="extra-fields" role="tabpanel" aria-labelledby="extra-fields-tab">
            <div class="row gap-rows">

                <div class="col-lg-2 mb-2">
                    <div class="input-effect">
                        <label class="form-label">Contact Person Name<span>*</span></label>
                        <input class="form-control capitalize-title" type="text" name="cust_name" autocomplete="off" id="cust_name"
                            value="<?php echo e(isset($edit) ? (!empty(@$edit->cust_name) ? @$edit->cust_name : old('cust_name')) : old('cust_name')); ?>"
                            required>
                    </div>
                </div>
                <div class="col-lg-2 mb-2">
                    <div class="input-effect">
                        <label class="form-label">Designation</label>
                        <input class="form-control capitalize-title" type="text" name="designation" autocomplete="off" id="designation"
                            value="<?php echo e(isset($edit) ? (!empty(@$edit->designation) ? @$edit->designation : old('designation')) : old('designation')); ?>">
                    </div>
                </div>

                <div class="col-lg-2 mb-2">
                    <div class="input-effect">
                        <label class="form-label">Mobile<span>*</span></label>
                        <input class="form-control" type="text" name="cust_no" autocomplete="off" id="cust_no"
                            value="<?php echo e(isset($edit) ? (!empty(@$edit->cust_no) ? @$edit->cust_no : old('cust_no')) : old('cust_no')); ?>">
                    </div>
                </div>
                <div class="col-lg-2 mb-2">
                    <div class="input-effect">
                        <label class="form-label">Email</label>
                        <input class="form-control" type="text" name="cust_email" autocomplete="off" id="cust_email" data-bs-target="#EmailModal" data-bs-toggle="modal"
                            value="<?php echo e(isset($edit) ? (!empty(@$edit->cust_email) ? @$edit->cust_email : old('cust_email')) : old('cust_email')); ?>">
                    </div>
                </div>
                <div class="col-lg-2 mb-2">
                    <div class="input-effect">
                        <label class="form-label">Address<span></span></label>
                        <input class="form-control capitalize-title" type="text" name="address" autocomplete="off" id="address" data-bs-target="#AddressModal" data-bs-toggle="modal"
                            value="<?php echo e(isset($edit) ? (!empty(@$edit->address) ? @$edit->address : old('address')) : old('address')); ?>">
                    </div>
                </div>
                <div class="col-lg-2 mb-2">
                
                    <div class="input-effect">
                        <label class="form-label"><?php echo app('translator')->getFromJson('Brand'); ?> <span>*</span></label>
                      <?php
    // Convert "aruba,aio,cisco wifi" into ['aruba', 'aio', 'cisco wifi']
    $selectedTags = !empty($edit->tags) ? array_map('trim', explode(',', $edit->tags)) : [];
?>

<select class="form-control js-example-basic-single" name="tags[]" id="tags" multiple>
    <?php $__currentLoopData = $brand; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($value->title); ?>"
            <?php if(in_array($value->title, $selectedTags)): ?> selected <?php endif; ?>>
            <?php echo e($value->title); ?>

        </option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</select>
                    </div>
                </div>
                <div class="col-lg-2 mb-2">
                    <div class="input-effect">
                        <label class="form-label">Date<span>*</span></label>
                        <?php
                            $value = date('Y-m-d');
                            if (isset($edit) && !empty($edit->date)) {
                                $value = date('Y-m-d', strtotime(@$edit->date));
                            }
                        ?>
                        <input class="form-control date-picker" id="date" type="text" name="date"
                            value="<?php echo e(@App\SysHelper::normalizeToDmy(@$value)); ?>">
                    </div>
                </div>
            
                <div class="col-lg-2 mb-2" style="display: none;">
                    <div class="input-effect">
                        <label class="form-label">Source<span>*</span></label>
                        <select class="form-control js-example-basic-single" name="source" id="source">
                            <option value="">-Select-</option>
                            <option value="Chat" <?php if(@$edit->source == 'Chat'): ?> selected <?php endif; ?>>Chat</option>
                            <option value="Call" <?php if(@$edit->source == 'Call'): ?> selected <?php endif; ?>>Call</option>
                            <option value="Mail" <?php if(@$edit->source == 'Mail'): ?> selected <?php endif; ?> <?php if(!isset($edit)): ?>
                            selected <?php endif; ?>>Mail</option>
                            <option value="Website" <?php if(@$edit->source == 'Website'): ?> selected <?php endif; ?>>Website
                            </option>
                            <option value="Gitex 2023" <?php if(@$edit->source == 'Gitex 2023'): ?> selected <?php endif; ?>>Gitex
                                2023</option>
                            <option value="Gitex" <?php if(@$edit->source == 'Gitex'): ?> selected <?php endif; ?>>Gitex
                            </option>
                            <option value="Fulfillment" <?php if(@$edit->source == 'Fulfillment'): ?> selected <?php endif; ?>>
                                Fulfillment</option>
                            <option value="Ecommerce" <?php if(@$edit->source == 'Ecommerce'): ?> selected <?php endif; ?>>Ecommerce
                            </option>
                            <option value="Other" <?php if(@$edit->source == 'Other'): ?> selected <?php endif; ?>>Other
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-2 mb-2" id="sourcediv" style="display: none;">
                    <div class="input-effect">
                        <label class="form-label">Other Source<span>*</span></label>
                        <input class="form-control" type="text" name="source_o" autocomplete="off" id="source_o"
                            value="<?php echo e(isset($edit) ? (!empty(@$edit->source_o) ? @$edit->source_o : old('source_o')) : old('source_o')); ?>"
                            style="display: none;" placeholder="Source">
                    </div>
                </div>


                <div class="col-lg-2 mb-2">
                    <div class="input-effect">
                        <label class="form-label">Deal Type<span></span></label>
                        <select class="form-control js-example-basic-single" name="isproject" id="isproject">
                            
                            <option value="1" <?php if(@$edit->isproject == '1'): ?> selected <?php endif; ?>>Reseller
                            </option>
                            <option value="2" <?php if(@$edit->isproject == '2'): ?> selected <?php endif; ?>>Enduser
                            </option>
                            <option value="3" <?php if(@$edit->isproject == '3'): ?> selected <?php endif; ?>>E-Commerece
                            </option>
                            <option value="5" <?php if(@$edit->isproject == '5'): ?> selected <?php endif; ?>>Marketing
                            </option>
                        </select>
                        <script>
                            $('#isproject').on('change', function (e) {
                                if ($('#isproject').val() == 4) {
                                    $('#is_professional_service').prop("checked", true);
                                } else {
                                    $('#is_professional_service').prop("checked", false);
                                }
                            });
                        </script>
                    </div>
                </div>

                
             

                <div class="col-lg-2 mb-2">
                    <div class="input-effect">
                        <label class="form-label">Project Service<span>*</span></label>
                        <div class="form-control d-flex justify-content-center align-items-center">
                            <input class="form-check-input ml-2 me-2" type="checkbox" value="1"
                                id="is_professional_service" name="is_professional_service" <?php if($edit->is_professional_service == 1): ?> checked <?php endif; ?>>
                            <label class="form-label ml-4" for="is_professional_service">Yes, Project
                                Service</label>
                        </div>
                    </div>
                </div>

          

                <div class="col-lg-2 mb-2">
                    <div class="input-effect">
                        <label class="form-label">Stage<span>*</span></label>
                        <select class="form-control js-example-basic-single" name="stage" id="stage">
                            <option value="1" <?php if(@$edit->stage == 1): ?> selected <?php endif; ?>>Prospecting
                            </option>
                            <option value="2" <?php if(@$edit->stage == 2): ?> selected <?php endif; ?>>Quote</option>
                            <option value="3" <?php if(@$edit->stage == 3): ?> selected <?php endif; ?>>Closure
                            </option>
                            <option value="4" <?php if(@$edit->stage == 4): ?> selected <?php endif; ?>>Won</option>
                            <option value="5" <?php if(@$edit->stage == 5): ?> selected <?php endif; ?>>Lost</option>
                        </select>
                        <textarea class="primary-input dynamicstxt_s w-100 form-control" name="lost_comments" rows="4"
                            style="height: 50px !important; display: none;" autocomplete="off" id="lost_comments"
                            placeholder="Reason"></textarea>
                        <script>
                            $('#stage').on('change', function (e) {
                                if ($('#stage').val() == 5) {
                                    $('#lost_comments').css("display", "block");
                                    $('#lost_comments').prop('required', true);
                                } else {
                                    $('#lost_comments').css("display", "none");
                                    $('#lost_comments').prop('required', false);
                                }
                            });
                        </script>
                    </div>
                </div>

                   <div class="col-2" id="followup_date_div">
                        <label class="form-label">FollowUp Date<span>*</span></label>
                  <?php


$followupDate = @$edit->followup_date;

// If value exists, convert from DB (UTC or system timezone) to Dubai time
if (!empty($followupDate)) {
    try {
        $followupDate = Carbon\Carbon::parse($followupDate)
           
            ->format('d/m/Y h:i A'); // Match Flatpickr
    } catch (\Exception $e) {
        // Fallback: in case parsing fails
        $followupDate = Carbon\Carbon::now()
            ->addDays(3)
            ->setTime(11, 0)
            ->format('d/m/Y h:i A');
    }
} else {
    // If not set, default = +3 days 11 AM Dubai
    $followupDate = Carbon\Carbon::now()
        ->addDays(3)
        ->setTime(11, 0)
        ->format('d/m/Y h:i A');
}
?>
                        <input type="text" class="form-control date-time-picker" name="followup_date" id="followup_date" value="<?php echo e($followupDate); ?>">
                </div>

                  <div class="col-2">
                <label class="form-label">Company</label>
                <div class="form-group">
                    <select class="form-control js-example-basic-single" name="company" id="company" required>
                       
                           
                            <?php $__currentLoopData = $company; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e(@$value->id); ?>" <?php if($edit->company_id == @$value->id): ?>
                                selected <?php endif; ?>><?php echo e(@$value->company_name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                       
                    </select>
                </div>
            </div>

                <div class="col-lg-2 mb-2">
                    <div class="input-effect">
                        <label class="form-label">Attach<span>*</span></label>
                        <input type="file" class="form-control" name="doc" id="doc">
                    </div>
                </div>
                <div class="col mb-2">
                    <div class="input-effect">
                        <label class="form-label">Notes<span>*</span></label>
                        <input class="form-control capitalize-title" name="note" rows="3" autocomplete="off" id="note" data-bs-toggle="modal" value="<?php if(isset($edit)): ?> <?php echo e($edit->note); ?> <?php endif; ?>" data-bs-target="#NoteModal">


                    </div>
                </div>

            </div>
        </div>



        <div class="tab-pane fade" id="delivery-fields" role="tabpanel" aria-labelledby="delivery-fields-tab">



            <div class="row">
                <div class="col-12 mb-2">
                    <div class="row">
                        <input type="hidden" name="cust_deal_id" value="<?php echo e($edit->id ?? ''); ?>" />
                        <input type="hidden" name="cust_id" value="<?php echo e($edit->cust_id ?? ''); ?>" />

                        
                        <div class="col-md-4">
                            <?php
                                $supplier_v_c = @App\SysHelper::getSuppliersVC();
                                $selectedVendor = $vendors->firstWhere('id', $edit->cust_id);
                            ?>
                        
                           
                            <div class="mb-3">
                                <label class="form-label">Customer Name</label>
                                <select class="form-control js-example-basic-single" name="delivery_company"
                                    id="delivery_company" >
                                    <option value="">-Select-</option>
                                    <!-- <?php if($selectedVendor): ?>
                                        <option value="<?php echo e($selectedVendor->id); ?>"
                                            <?php echo e(!empty($edit->delivery_company) && $edit->delivery_company == $selectedVendor->id ? 'selected' : ''); ?>>
                                            <?php echo e($selectedVendor->name); ?>

                                        </option>
                                    <?php endif; ?> -->

                                    <?php $__currentLoopData = $supplier_v_c ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($value->id); ?>"
                                            <?php echo e(!empty($edit->delivery_company) && $edit->delivery_company == $value->id ? 'selected' : ''); ?>>
                                            <?php echo e($value->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                </select>
                            </div>
                        </div>

                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Select Delivery Address</label>
                                <select class="form-control js-example-basic-single" name="delivery_address_select" id="delivery_address_select">
                                    <option value="">-Select Address-</option>
                                </select>
                            </div>
                        </div>

                      

                        
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Contact Person</label>
                                <input type="text" class="form-control capitalize-title" name="delivery_name" id="delivery_name"
                                    value="<?php echo e(old('delivery_name', $edit->delivery_name ?? ($leads->cust_name ?? ''))); ?>"
                                    >
                            </div>
                        </div>

                      

                        
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Contact Number</label>
                                <input type="text" class="form-control" name="delivery_number" id="delivery_number"
                                    value="<?php echo e(old('delivery_number', $edit->delivery_number ?? ($leads->cust_no ?? ''))); ?>"
                                    >
                            </div>
                        </div>

                      

                        
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Contact Email</label>
                                <input type="email" class="form-control" name="delivery_email" id="delivery_email"
                                    value="<?php echo e(old('delivery_email', $edit->delivery_email ?? ($leads->cust_email ?? ''))); ?>"
                                    >
                            </div>
                        </div>

                        
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Country</label>
                                <select class="form-control js-example-basic-single" id="country_n_e"
                                    name="delivery_country" >
                                    <option value="">Select Country</option>
                                    <?php $__currentLoopData = $countries ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($value->id); ?>" <?php echo e(($edit->delivery_country ?? ($addressbook->country ?? '')) == $value->id ? 'selected' : ''); ?>>
                                            <?php echo e($value->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>

                      

                        
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">State</label>
                                <select class="form-control js-example-basic-single" id="state_n_e"
                                    name="delivery_state" >
                                    <option value="">Select State</option>
                                    <?php $__currentLoopData = $states ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($st->id); ?>" <?php echo e(($edit->delivery_state ?? ($addressbook->state ?? '')) == $st->id ? 'selected' : ''); ?>>
                                            <?php echo e($st->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>

                        


                          <!-- 
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Address 1</label>
                                <input class="form-control capitalize-title" type="text" id="delivery_address1" name="delivery_address1"
                                    value="<?php echo e(old('delivery_address1', $edit->delivery_address1 ?? ($addressbook->address ?? ''))); ?>"
                                    >
                            </div>
                        </div>

                       
                        
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Address 2</label>
                                <input class="form-control capitalize-title" type="text" id="delivery_address2" name="delivery_address2"
                                    value="<?php echo e(old('delivery_address2', $edit->delivery_address2 ?? ($addressbook->address2 ?? ''))); ?>"
                                    >
                            </div>
                        </div> -->


                        

                        
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">City</label>
                                <input class="form-control capitalize-title" type="text" id="delivery_city" name="delivery_city"
                                    value="<?php echo e(old('delivery_city', @$edit->delivery_city ?? (@$addressbook->city ?? ''))); ?>"
                                    >
                            </div>
                        </div>



                         <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Area</label>
                                <input class="form-control capitalize-title" type="text" id="delivery_area1" name="delivery_area1"
                                    value="<?php echo e(old('delivery_area1', @$edit->delivery_area ?? (@$addressbook->area ?? ''))); ?>"
                                    >
                            </div>
                        </div>

                           <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Building Name</label>
                                <input class="form-control capitalize-title" type="text" id="delivery_building" name="delivery_building"
                                    value="<?php echo e(old('delivery_building', @$edit->delivery_building ?? (@$addressbook->building ?? ''))); ?>"
                                    >
                            </div>
                        </div>
                       
                          <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Flat/Office No</label>
                                <input class="form-control capitalize-title" type="text" id="delivery_flat_office_no" name="delivery_flat_office_no"
                                    value="<?php echo e(old('delivery_flat_office_no', @$edit->delivery_flat_office_no ?? (@$addressbook->flat_office_no ?? ''))); ?>"
                                    >
                            </div>
                        </div>

                        
                

                        
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">PO Box</label>
                                <input class="form-control" type="text" name="delivery_zip_code" id="delivery_zip_code"
                                    value="<?php echo e(old('delivery_zip_code', $edit->delivery_zip_code ?? ($addressbook->zip_code ?? ''))); ?>"
                                    >
                            </div>
                        </div>


                    </div>
                </div>
            </div>




        </div>

          <div class="tab-pane fade" id="salesperson-fields" role="tabpanel" aria-labelledby="salesperson-fields-tab">
          <div class="row text-start">

                    <!-- Sales Person -->
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 text-center">
                        <p class="font-weight-600 " style="background-color: #deebe1;margin-bottom: 3px">Sales Person</p>
                        

                           <select class="form-control js-example-basic-single" name="owner" id="owner" required>
                                        <option value="">-Select-</option>

                                        <?php $__currentLoopData = $sales_person; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($value->user_id); ?>" <?php if($edit->owner == $value->user_id): ?> selected <?php endif; ?>><?php echo e(@$value->full_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                    </div>

                    <!-- Mobile -->
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 text-center">
                        <p class="font-weight-600 " style="background-color: #deebe1;margin-bottom: 3px">Mobile</p>
                        <?php echo e(@$edit->ownername->mobile); ?>

                    </div>

                    <!-- Email -->
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 text-center">
                        <p class="font-weight-600 " style="background-color: #deebe1;margin-bottom: 3px">Email</p>
                        <?php echo e(@$edit->ownername->email); ?>

                    </div>

                    <!-- Ext No -->
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 text-center">
                        <p class="font-weight-600 " style="background-color: #deebe1;margin-bottom: 3px">Ext No</p>
                        <?php echo e(@$edit->ownername->ext_no ?? '--'); ?>

                    </div>

                    <!-- Source -->
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 text-center">
                        <p class="font-weight-600 " style="background-color: #deebe1;margin-bottom: 3px">Source</p>
                        <?php if(@$edit->source != ''): ?>
                            <?php echo e(@$edit->source); ?> <?php if(@$edit->source_o != ''): ?> - <?php echo e(@$edit->source_o); ?> <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Close Date -->
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 text-center">
                        <p class="font-weight-600 " style="background-color: #deebe1;margin-bottom: 3px">Close Date</p>
                        <?php echo e(date('d/m/Y', strtotime(@$edit->estimated_close_date))); ?>

                    </div>

                    <!-- Added By -->
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 text-center">
                        <p class="font-weight-600 " style="background-color: #deebe1;margin-bottom: 3px">Added By</p>
                        <?php echo e(@$edit->createdby->full_name); ?>

                    </div>

                    <!-- Added On -->
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 text-center">
                        <p class="font-weight-600 " style="background-color: #deebe1;margin-bottom: 3px">Added On</p>
                        <?php echo e(date('d/m/Y h:i A', strtotime(@$edit->created_at))); ?>

                    </div>

                    <!-- Updated On -->
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 text-center">
                        <p class="font-weight-600 " style="background-color: #deebe1;margin-bottom: 3px">Updated On</p>
                        <?php echo e(date('d/m/Y h:i A', strtotime(@$edit->updated_at))); ?>

                    </div>

            </div>
        </div>

        <div class="tab-pane fade" id="quote-fields" role="tabpanel" aria-labelledby="quote-fields-tab">

           
                     <script>
$(document).ready(function() {
    $('.btnDownloadQuote').on('click', function(e) {
        e.preventDefault();

        const row = $(this).closest('td');
        const id = $(this).data('id');
        const quote = $(this).data('quote');

        // Collect checkbox values within the same row
        let params = [];
        if (row.find('.withPartNumber').is(':checked')) params.push('with_partnumber=1');
        if (row.find('.excludeVat').is(':checked')) params.push('without_vat=1');
        if (row.find('.withoutTotal').is(':checked')) params.push('without_total=1');


        // Build the final URL
        let url = `/crm-quote/${id}/download/${quote}`;
        if (params.length > 0) {
            url += '?' + params.join('&');
        }

        console.log(url); // For debugging
        window.open(url, '_blank'); // Open in a new tab to download
    });
});
</script>


            <h4 class="mb-1 color-sub-head font-size-13 mb-2">Quote Revisions
            
                 
            </h4> 

            <?php    $editcheck = App\SysHelper::deal_edit_disable($edit->id); ?>




            <table class="table table-hover table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width:120px">Quote No</th>
                        <th  class="text-center">Actions</th>
                        <th style="width:80px">  
                            <a class="btn btn-sm btn-light text-center text-dark"  href="<?php echo e(url('crm-deals-create-quote/'.$edit->id )); ?>" style="padding: 0px 8px 0px 8px;border-radius:4px">
                                             <svg style="height:14px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M352 128C352 110.3 337.7 96 320 96C302.3 96 288 110.3 288 128L288 288L128 288C110.3 288 96 302.3 96 320C96 337.7 110.3 352 128 352L288 352L288 512C288 529.7 302.3 544 320 544C337.7 544 352 529.7 352 512L352 352L512 352C529.7 352 544 337.7 544 320C544 302.3 529.7 288 512 288L352 288L352 128z"></path></svg>
                                 Quotation
                            </a>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php
@$quote_no = App\SysCrmQuoteItems::select('quote_id', 'document_number')
    ->where('deal_id', $edit->id)
    ->groupBy('quote_id', 'document_number')
    ->orderBy('quote_id', 'asc')
    ->get();

                        ?>
                    <?php $__currentLoopData = @$quote_no; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <!-- Quote Number -->
                             <?php if($item->document_number): ?>
                            <td>
                                <strong><?php echo e($item->document_number); ?></strong>
                            </td>
                             <?php else: ?>
                                   <td>
                                <strong><?php echo e($edit->deal_code->code); ?> <?php if($item->quote_id != 1): ?> - <?php echo e($item->quote_id - 1); ?> <?php endif; ?></strong>
                            </td>
                             <?php endif; ?>
                          

                            <!-- Action Buttons -->
                            <td class="d-flex justify-content-start align-items-center gap-2">

                                     <div class="form-check form-check-inline m-0">
                                        <input class="form-check-input withPartNumber" type="checkbox" id="withPartNumber<?php echo e($item->quote_id); ?>" name="with_partnumber"
                                            value="1">
                                        <label class="form-label" for="withPartNumber<?php echo e($item->quote_id); ?>">Include Part Numbers</label>
                                    </div>

                                    <div class="form-check form-check-inline m-0">
                                        <input class="form-check-input excludeVat" type="checkbox" id="excludeVat<?php echo e($item->quote_id); ?>" name="without_vat" value="1">
                                        <label class="form-label" for="excludeVat<?php echo e($item->quote_id); ?>">Exclude VAT</label>
                                    </div>

                                    <div class="form-check form-check-inline me-4">
                                        <input class="form-check-input withoutTotal" type="checkbox" id="withoutTotal<?php echo e($item->quote_id); ?>" name="without_total"
                                            value="1">
                                        <label class="form-label" for="withoutTotal<?php echo e($item->quote_id); ?>">Hide Total</label>
                                    </div>


                                <!-- Download -->
                                <a class="btn btn-sm btn-light btnDownloadQuote text-dark"
                                    style="padding: 0px 8px 0px 8px;border-radius:4px"  data-id="<?php echo e($edit->id); ?>" 
                                    data-quote="<?php echo e($item->quote_id); ?>">
                                    <i class="ico icon-bold-download-minimalistic text-success" style="font-size:16px"></i>
                                    Download
                                </a>

                                <?php if($editcheck == 0): ?>
                                    <!-- Edit -->
                                    <a class="btn btn-sm btn-light  text-dark"
                                        href="<?php echo e(url('crm-deals/show/' . $edit->id . '?deal_action=edit&quote=' . $item->quote_id)); ?>">
                                        <i class="ico icon-outline-pen-2 text-success" style="font-size:16px"></i>
                                        Edit
                                    </a>

                                    <!-- Create Copy -->
                                    <a class="btn btn-sm btn-light text-dark me-4"
                                        href="<?php echo e(url('crm-quote/' . $edit->id . '/createcopy/' . $item->quote_id)); ?>">
                                        <i class="ico icon-outline-copy text-success" style="font-size:16px"></i>
                                        Create Copy
                                    </a>
                                <?php endif; ?>

                                <!-- Set as Final Quote / Final Quote Label -->
                                <?php if($item->quote_id != $edit->quote_id): ?>
                                    <?php if($editcheck == 0): ?>
                                        <a class="btn btn-sm btn-light text-dark"
                                            href="<?php echo e(url('crm-quote/' . $edit->id . '/setprimary/' . $item->quote_id)); ?>">
                                            <i class="ico icon-outline-check-square text-success" style="font-size:16px"></i> Set as
                                            Final Quote
                                        </a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="btn btn-sm btn-light text-dark "
                                        style="padding: 0px 8px 0px 8px;border-radius:4px">Final Quote</span>
                                <?php endif; ?>
                            </td>

                            <td></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


                </tbody>
            </table>

        </div>

        <?php if($quotationitems->where('product_type', 2)->count() > 0): ?>

        

           <div class="tab-pane fade" id="enduser-fields" role="tabpanel" aria-labelledby="enduser-fields-tab">

           
                 <?php if($enduser==""): ?>
                    <div id="enduser-form">

                    <input type="hidden" id="end_user_deal_id" value="<?php echo e($edit->id); ?>">

                    <div class="row">

                        <div class="col">
                            <label class="form-label">Company Name *</label>
                            <input type="text" class="form-control capitalize-title" name="end_user_company_name" id="end_user_company_name">
                        </div>

         

                    

                        <div class="col">
                            <label class="form-label">Contact Person *</label>
                            <input type="text" class="form-control capitalize-title" name="end_user_contact_person" id="end_user_contact_person">
                        </div>

                        <div class="col">
                            <label class="form-label">Mobile No</label>
                            <input type="text" class="form-control" name="end_user_mobile_no" id="mobile_no">
                        </div>

                        <div class="col">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="end_user_email" id="email">
                        </div>


                            <div class="col">
                            <label class="form-label">Device Serial</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="end_user_device_serial" id="device_serial" readonly >
                                <button type="button" class="btn btn-light border" id="device_serial_btn_modal">
                                    <i class="ico icon-outline-list-down"></i>
                                </button>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer p-0 mt-2">
                        <button type="button" id="saveEndUser" class="btn btn-light add-btn ms-2">
                            <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                        </button>
                    </div>

                </div>

                <script>
                    $(document).on("click", "#saveEndUser", function () {

    $("#loading_bg").css("display", "block");


    // Collect serial numbers organized by row (then group by part number for storage)
    let serialsByPart = {};
    $('.serial-data-storage').each(function() {
        let partNumber = $(this).data('part-number');
        let rowIndex = $(this).data('row-index');
        let serialData = $(this).val();
        
        if (serialData) {
            try {
                let serials = JSON.parse(serialData);
                if (serials.length > 0) {
                    // Group by part number for end user display
                    if (!serialsByPart[partNumber]) {
                        serialsByPart[partNumber] = [];
                    }
                    serialsByPart[partNumber] = serialsByPart[partNumber].concat(serials);
                }
            } catch(e) {
                // If not JSON, try CSV
                let serials = serialData.split(',').map(s => s.trim()).filter(s => s);
                if (serials.length > 0) {
                    if (!serialsByPart[partNumber]) {
                        serialsByPart[partNumber] = [];
                    }
                    serialsByPart[partNumber] = serialsByPart[partNumber].concat(serials);
                }
            }
        }
    });

    let data = {
        end_user_deal_id: $("#end_user_deal_id").val(),
        end_user_company_name: $("#end_user_company_name").val(),
        device_serial: JSON.stringify(serialsByPart), // Send as JSON organized by part number
        end_user_contact_person: $("#end_user_contact_person").val(),
        mobile_no: $("#mobile_no").val(),
        email: $("#email").val(),
        project_name: $("#project_name").val(),
        project_description: $("#project_description").val(),
        expected_close_date: $("#expected_close_date").val(),
        _token: "<?php echo e(csrf_token()); ?>"
    };

    // basic validation
    if (data.end_user_company_name.trim() === "") {
        toastr.error("Company Name is required"); return;
    }
    if (data.end_user_contact_person.trim() === "") {
        toastr.error("Contact Person is required"); return;
    }

    $.ajax({
        url: "/crm-deal-add-end-user",
        type: "POST",
        data: data,
        beforeSend: function () {
            // $("#saveEndUser").prop("disabled", true).html("Saving...");
        },
        success: function (resp) {
            toastr.success("End User details saved successfully");

            // refresh or reload tab
            setTimeout(() => location.reload(), 1000);
        },
        error: function (xhr) {
            toastr.error("Error saving data");
            console.log(xhr.responseText);
        },
        complete: function () {
            $("#saveEndUser")
                .prop("disabled", false)
                .html(`<i class="ico icon-outline-bookmark-opened text-success"></i> Save`);
            $("#loading_bg").css("display", "none");
        }
    });

});

                </script>
     
                <?php else: ?>
                  
                            <div class="row">

                                <div class="col text-center">
                                   <p class="font-weight-600 " style="background-color: #deebe1;margin-bottom: 3px">Company Name</p>
                                <span class="truncate-text-custom"><?php echo e($enduser->end_user_company_name); ?></span> 
                                </div>
      


                                

                                <div class="col text-center">
                                    <p class="font-weight-600" style="background-color: #deebe1;margin-bottom: 3px">Contact Person</p>
                                <span class="truncate-text-custom"><?php echo e($enduser->end_user_contact_person); ?></span> 
                                </div>

                                <div class="col text-center">
                                    <p class="font-weight-600" style="background-color: #deebe1;margin-bottom: 3px">Mobile No</p>
                                    <span class="truncate-text-custom"><?php echo e($enduser->mobile_no); ?></span> 
                                </div>

                                <div class="col text-center">
                                    <p class="font-weight-600 " style="background-color: #deebe1;margin-bottom: 3px">Email</p>
                                    <span class="truncate-text-custom"><?php echo e($enduser->email); ?></span>
                                </div>

                                <div class="col text-center">
                                       <?php
        // Try to parse as JSON (new format: organized by part number)
        $serialDisplay = '';
        $count_serial = 0;
        
        if (!empty($enduser->device_serial)) {
            $decoded = json_decode($enduser->device_serial, true);
            
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                // New format: JSON with part numbers as keys
                $parts = [];
                foreach ($decoded as $partNumber => $serials) {
                    if (is_array($serials) && count($serials) > 0) {
                        $parts[] = $partNumber . ': ' . implode(', ', $serials);
                        $count_serial += count($serials);
                    }
                }
                $serialDisplay = implode(' | ', $parts);
            } else {
                // Old format: plain comma-separated or pipe-separated
                if (strpos($enduser->device_serial, '|') !== false) {
                    // Already formatted with part numbers
                    $serialDisplay = $enduser->device_serial;
                    // Count serials by splitting on commas and pipes
                    $allSerials = preg_split('/[,|]/', $enduser->device_serial);
                    $count_serial = count(array_filter(array_map('trim', $allSerials), function($s) {
                        return !empty($s) && strpos($s, ':') === false;
                    }));
                } else {
                    // Simple comma-separated
                    $serialDisplay = $enduser->device_serial;
                    $count_serial = count(array_filter(explode(',', $enduser->device_serial)));
                }
            }
        }
    ?>
                                    <p class="font-weight-600" style="background-color: #deebe1;margin-bottom: 3px">Device Serial (<?php echo e($count_serial); ?>)</p>
                                    
                                   
                                    <div class="d-flex justify-content-center">
 <button class="btn btn-light text-success" type="button" data-bs-toggle="modal" data-bs-target="#serialModal">
                                        View All
                                    </button>
                                    </div>
                                   

                                </div>

                                <!-- SERIAL MODAL -->
<div class="modal fade" id="serialModal" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="DeviceSerialModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" style="max-width: 22rem;">
        <div class="modal-content">
            
            <div class="modal-header mb-2">
                <h4 class="modal-title" id="DeviceSerialModalLabel">Device Serials</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body pt-0">

                <?php
                    $groupedSerials = [];

                    if (!empty($enduser->device_serial)) {
                        $decoded = json_decode($enduser->device_serial, true);

                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                            // New JSON format
                            foreach ($decoded as $part => $serials) {
                                if (is_array($serials)) {
                                    $groupedSerials[$part] = $serials;
                                }
                            }
                        } else {
                            // Old formats

                            if (strpos($enduser->device_serial, '|') !== false) {
                                // Already formatted part|serial1,serial2
                                $parts = explode('|', $enduser->device_serial);
                                foreach ($parts as $p) {
                                    [$part, $ser] = array_pad(explode(':', $p), 2, '');
                                    $groupedSerials[trim($part)] = array_map('trim', explode(',', $ser));
                                }
                            } else {
                                // Simple comma separated; put under "Unknown Part"
                                $groupedSerials["Unknown Part"] = array_map('trim', explode(',', $enduser->device_serial));
                            }
                        }
                    }
                ?>

               <?php $__currentLoopData = $groupedSerials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $part => $serials): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

    <?php
        // Clean serial list (Laravel 5 compatible)
        $cleanSerials = [];
        foreach ($serials as $s) {
            if (trim($s) != '') {
                $cleanSerials[] = $s;
            }
        }
        $count = count($cleanSerials);
    ?>

    <div class="mb-3 p-2 border rounded">

        <!-- Part header with count badge -->
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="fw-bold" style="font-size: 0.95rem;"><?php echo e($part); ?></span>
            <span class="qty-badge"><?php echo e($count); ?></span>
        </div>

        <!-- Serial list -->
        <?php $__currentLoopData = $cleanSerials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          
            <div class="mb-1" style="    display: flex
;
    align-items: center;
    margin-bottom: 8px;
    gap: 8px;">
                <span class="text-muted" style="min-width: 20px;"> <?php echo e($index + 1); ?>.</span>
                <input type="text" 
                       class="form-control form-control-sm" 
                       value="<?php echo e($s); ?>" 
                       readonly 
                       style=" background: #fdfdfd;">
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    </div>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

                                

                            </div>
                      


                <?php endif; ?>

        </div>
        <?php endif; ?>

        <div class="tab-pane fade show active" id="editfullfill-fields" role="tabpanel" aria-labelledby="editfullfill-fields-tab">

            <?php    $data = App\SysHelper::deal_track_status($edit->id); ?>
            <?php if(App\SysHelper::set_track($edit->id) == 1): ?>
                <?php if($data == 'Fulfill'): ?>

                    
                   
                            <?php if(App\SysHelper::get_company_status($edit->customername) == 0): ?>
                    

                                <?php
                                    $validation = @App\SysHelper::get_customer_incomplete_fields($edit->customername);
                                ?>

                                  <?php
                                        $editDoc = @App\SysCustSupplDoc::where('cust_suppl_id', $edit->customername->id)->get();
                                    ?>

                                 

                                        <?php
                                        $ids = array_column($validation['errors'], 'id');
                                        ?>

                                    <div class="row">

                                        <?php if(in_array('vat_number', $ids)): ?>
                                         <div class="col">
                                        <label for="" class="form-label">VAT Number</label>
                                            <div class=""><input class="form-control" type="text" name="vat_number" id="ci_vat_number"
                                                    value="<?php echo e($edit->customername->vat_number); ?>">
                                                </div>
                                        </div>
                                        <?php endif; ?>
                                     
                                       
                                        <?php if(in_array('mobile', $ids)): ?>
                                        <div class="col">
                                            <label for="" class="form-label">Customer Mobile</label>
                                            <input class="form-control" type="text" name="mobile" id="ci_mobile" placeholder="Mobile"
                                                value="<?php echo e(isset($edit) ? (!empty(@$edit->cust_no) ? @$edit->cust_no : old('cust_no')) : old('cust_no')); ?>">

                                        </div>
                                        <?php endif; ?>


                                        <?php if(in_array('email', $ids)): ?>

                                        <div class="col">
                                            <label for="" class="form-label">Customer Email</label>
                                            <input class="form-control" type="text" name="email" id="ci_email" placeholder="Email"
                                                value="<?php echo e(isset($edit) ? (!empty(@$edit->cust_email) ? @$edit->cust_email : old('cust_email')) : old('cust_email')); ?>" >
                                        </div>

                                        <?php endif; ?>

                                        <?php if(in_array('first_name', $ids)): ?>

                                          <!-- First Name -->
                                        <div class="col">
                   
                                            <label class="form-label mb-0 me-3" style="min-width: 120px;">Primary
                                                Contact:</label>

                                            <input type="text" class="form-control" id="ci_firstName"
                                                name="first_name" placeholder="First Name"
                                                value="<?php echo e(isset($edit) ? (!empty(@$edit->cust_name) ? @$edit->cust_name : old('cust_name')) : old('cust_name')); ?>">
                                        </div>
                                            
                                        <?php endif; ?>

                                        <?php if(in_array('contact_number', $ids)): ?>

                                             <div class="col ">
                                            <label for="" class="form-label">Customer Phone</label>
                                            <input class="form-control" type="text" name="mobile_code" id="ci_mobile_code" placeholder="Work Phone"
                                                value="<?php echo e($edit->customername->contcat_number); ?>" >
                                        </div> 
                                            
                                        <?php endif; ?>
                                      

                                        <?php
                                            $exists = App\SysCustSupplDoc::where('cust_suppl_id', $edit->customername->id)
                                                        ->where('doc_name', 'Trade License/Commercial Registration')
                                                              ->whereNull('deleted_at') // <-- only consider not deleted
                                                        ->exists();

                                                            $existsVat = App\SysCustSupplDoc::where('cust_suppl_id', $edit->customername->id)
                                                        ->where('doc_name', 'VAT Certificate')
                                                              ->whereNull('deleted_at') // <-- only consider not deleted
                                                        ->exists();

                                                 
                                        ?>                         
                                        <?php if(!$exists): ?>
                                   
                                        
                                        <div class="col">
                                            <input class="form-control" type="hidden" name="doc_name[]"
                                            value="Trade License/Commercial Registration" readonly />
                                                <label for="" class="form-label">Trade License/Commercial Registration</label>
                                                  <input class="form-control" type="file" name="customer_documents_1" id="ci_trade_doc" />
                                                   <input class="form-control date-picker" type="text" id="ci_trade_exp_date" name="doc_exp_date[]"
                                            placeholder="Expiry Date" />
                                        </div>

                                        <?php endif; ?>

                                        <?php if(!$existsVat): ?>

                                         <div class="col ">
                                           <input class="form-control" type="hidden" name="doc_name[]"
                                            value="VAT Certificate" readonly />
                                                <label for="" class="form-label">VAT Certificate</label>
                                                 <input class="form-control" type="file" name="customer_documents_2" id="ci_vat_doc" />
                                        </div>
                                            
                                        <?php endif; ?>

                                       
                                        

                                   


                                        

                                       
                                     



                                    </div>

                                    
                              <script>
$(document).ready(function () {

    function updateCustomerEdit() {
        let fd = new FormData();


// inline DOM checks and appends (no helper functions)
let el;

el = document.getElementById('customer_edit_id'); if (el) fd.append('cust_id', el.value);
el = document.getElementById('ci_vat_number');    if (el) fd.append('vat_number', el.value);
el = document.getElementById('ci_mobile');        if (el) fd.append('mobile', el.value);
el = document.getElementById('ci_email');         if (el) fd.append('email', el.value);
el = document.getElementById('ci_salutation');    if (el) fd.append('customer_salutation', el.value);
el = document.getElementById('ci_firstName');     if (el) fd.append('first_name', el.value);
el = document.getElementById('ci_mobile_code');   if (el) fd.append('mobile_code', el.value);

// document names (only if related input exists in DOM)
if (document.getElementById('ci_trade_doc')) fd.append('doc_name[0]', 'Trade License/Commercial Registration');
if (document.getElementById('ci_vat_doc'))   fd.append('doc_name[1]', 'VAT Certificate');

// expiry dates
el = document.getElementById('ci_trade_exp_date'); if (el) fd.append('doc_exp_date[0]', el.value);

// files (check existence and length)
el = document.getElementById('ci_trade_doc');
if (el && el.files && el.files.length > 0) fd.append('customer_documents_1', el.files[0]);

el = document.getElementById('ci_vat_doc');
if (el && el.files && el.files.length > 0) fd.append('customer_documents_2', el.files[0]);

// fd is ready to send via fetch / $.ajax / XHR


        fd.append("_token", "<?php echo e(csrf_token()); ?>");

        $.ajax({
            url: "<?php echo e(url('customer-update-deal-track')); ?>",
            method: "POST",
            data: fd,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $("#loading_bg").show();
            },
            success: function (res) {
                $("#loading_bg").hide();

                if (res.status) {
                    toastr.success(res.message);
                    location.reload();
                } else {
                    toastr.error(res.message);
                }
            },
            error: function (xhr) {
                $("#loading_bg").hide();
                toastr.error("Something went wrong!");
            }
        });
    }

    $("#btnupdateCustomer").on("click", function (e) {
        e.preventDefault();
        updateCustomerEdit();
    });

});
                            </script>


                            


                                  

                                        

                            <div class="row pt-3" style="border-top: 1px solid #dee2e6">
                                <div class="col-4">
                                  
                                </div>
                                <div class="col-4 d-flex justify-content-center">
            <input type="hidden" id="customer_edit_id" name="customer_edit_id" value="<?php echo e($edit->customername->id); ?>" />
                                    <button type="button" class="btn btn-light add-btn ms-2" 
                                        id="btnupdateCustomer"><span class="ti-check"></span><i
                                            class="ico icon-outline-bookmark-opened text-success"></i> Update Customer</button>
                                </div>
                                <div class="col-4"></div>
                            </div>
                                        

                <style>
                   .deal-track-wrapper {
    position: relative;
}

/* More transparent overlay */
.deal-track-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.09);  /* <--- much lighter */
    z-index: 10;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(1px);  /* light blur */
}

/* Visible background text under message */
.deal-track-overlay-text {
    font-size: 20px;
    color: #fff;
    padding: 15px 25px;
    background: rgba(0,0,0,0.30);  /* <--- lighter text box */
    border-radius: 10px;
  
    text-align: center;
}

                </style>
                <?php endif; ?>
                
<div class="deal-track-wrapper position-relative mt-0">

<?php if(App\SysHelper::get_company_status($edit->customername) == 0): ?>

       <div class="deal-track-overlay">
        <div class="deal-track-overlay-text">
            ⚠️ Please update customer to submit for deal approval
        </div>
    </div>
<?php endif; ?>

                <h4 class="color-sub-head font-size-13">Deal Track</h4>

                        <?php
                            $delivery_date = '';
                            $payment_terms = '';
                            $payment_mode = '';
                            $purchease_required = '';
                            $partial_delivery = '';
                            $technical = '';
                            $technical_detail = '';
                            $lpo = '';
                            $cheque_copy = '';
                            $purchease_quote = '';
                            $remarks = '';
                            $special_instruction = '';
                            $reference_no = '';
                            $reference_date = '';
                            $purchease_approval = 0;
                            $invoice_approval = 1;
                            $delivery_approval = 1;
                            $receivables_approval = 1;
                            $start_date = '';
                            $end_date = '';

                            if (isset($deal_track_temp)) {

                                $delivery_date = $deal_track_temp->delivery_date;
                                $payment_terms = $deal_track_temp->payment_terms;
                                $payment_mode = $deal_track_temp->payment_mode;
                                $purchease_required = $deal_track_temp->purchease_required;
                                $partial_delivery = $deal_track_temp->partial_delivery;
                                $technical = $deal_track_temp->technical;
                                $technical_detail = $deal_track_temp->technical_detail;
                                $lpo = $deal_track_temp->lpo;
                                $cheque_copy = $deal_track_temp->cheque_copy;
                                $purchease_quote = $deal_track_temp->purchease_quote;
                                $remarks = $deal_track_temp->remarks;
                                $special_instruction = $deal_track_temp->special_instruction ?? '';
                                $reference_no = $deal_track_temp->reference_no;
                                $reference_date = $deal_track_temp->reference_date;
                                $purchease_approval = $deal_track_temp->purchease_approval;
                                $invoice_approval = $deal_track_temp->invoice_approval;
                                $delivery_approval = $deal_track_temp->delivery_approval;
                                $receivables_approval = $deal_track_temp->receivables_approval;
                                $start_date = $deal_track_temp->start_date;
                                $end_date = $deal_track_temp->end_date;
                                $invoicing = $deal_track_temp->invoicing;
                            }
                        ?>
                       
                        <div class="">
                            <div class="row">
                                <div class="col-5-custom mb-3">
                                    <div class="no-gutters input-right-icon">
                                        <div class="col">
                                            <div class="input-effect">
                                                <label class="form-label"><?php echo app('translator')->getFromJson('Expected Delivery Date'); ?><span></span></label>

                                                <input class="form-control date-picker" id="delivery_date1" type="text"
                                                    autocomplete="off"  
                                                    value="<?php if(!empty($delivery_date)): ?> <?php echo e(@App\SysHelper::normalizeToDmy($delivery_date)); ?> <?php else: ?> <?php echo e(date('d/m/Y')); ?> <?php endif; ?> ">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-5-custom mb-3">
                                    <div class="no-gutters input-right-icon">
                                        <div class="col">
                                            <div class="input-effect">
                                                <label class="form-label"><?php echo app('translator')->getFromJson('LPO/Reference No'); ?><span></span></label>
                                                <input class="form-control" id="reference_no1" type="text" autocomplete="off"
                                                     name="reference_no" value="<?php echo e($reference_no); ?>" >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-5-custom mb-3">
                                    <div class="no-gutters input-right-icon">
                                        <div class="col">
                                            <div class="input-effect">
                                                <label class="form-label"><?php echo app('translator')->getFromJson('LPO/Reference Date'); ?><span></span></label>
                                                <input class="form-control date-picker" id="reference_date1" type="text"
                                                    autocomplete="off"  name="reference_date"
                                                    value="<?php if(!empty($reference_date)): ?> <?php echo e(@App\SysHelper::normalizeToDmy($reference_date)); ?> <?php else: ?> <?php echo e(date('d/m/Y')); ?> <?php endif; ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-5-custom mb-3">
                                    <div class="input-effect">
                                        <label class="form-label">Payment Terms<span></span></label>
                                        <select class="form-control js-example-basic-single" name="payment_terms" id="payment_terms1" >
                                            <option value="">-Select-</option>
                                            <?php $__currentLoopData = $paymentterms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e(@$value->id); ?>" <?php if($payment_terms != ''): ?> <?php if(@$payment_terms == @$value->id): ?> selected <?php endif; ?> <?php else: ?> <?php if(isset($quotationitems)): ?>
                                                <?php if(@$quotationitems[0]->payment_terms == @$value->id): ?> selected <?php endif; ?> <?php endif; ?> <?php endif; ?>>
                                                    <?php echo e(@$value->title); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <script>
                                            $(document).ready(function () {
                                                // Bind change event
                                                $('#payment_terms1').on('change', function () {
                                                    const val = $(this).val();

                                                    // Show/hide payment_mode_sec_div
                                                    if (val == 20 || val == 21) {
                                                        $('#payment_mode_sec_div').hide();
                                                        // $('#payment_mode_sec').prop('required', true);
                                                    } else {
                                                        $('#payment_mode_sec_div').hide();
                                                        // $('#payment_mode_sec').prop('required', false);
                                                    }

                                                    // Set payment_mode based on terms
                                                    if (val == 1 || val == 2) {
                                                        $('#payment_mode').val(1);
                                                    } else {
                                                        $('#payment_mode').val(2);
                                                    }

                                                    // Show/hide payment_terms1_txt
                                                    if (val == 22) {
                                                        // $('#payment_terms1_txt').show().prop('required', true);
                                                    } else {
                                                        // $('#payment_terms1_txt').hide().prop('required', false);
                                                    }
                                                });

                                                // Trigger once on load in case value is already selected
                                                $('#payment_terms1').trigger('change');
                                            });
                                        </script>
                                        <input class="form-control" id="payment_terms1_txt1" type="text" value="" autocomplete="off"
                                            placeholder="Payment Terms" name="payment_terms_txt" style="display: none;">
                                    </div>
                                </div>
                                <?php
                                    $mode_sel = 0;
                                    if (@$quotationitems[0]->payment_terms == 1 || @$quotationitems[0]->payment_terms == 2) {
                                        $mode_sel = 1;
                                    } else {
                                        $mode_sel = 2;
                                    }

                                ?>
                                <div class="col-5-custom mb-3">
                                    <div class="input-effect">
                                        <label class="form-label d-flex align-items-center gap-2">
                                            <span>Payment Mode</span>
                                            <button type="button" class="btn p-0 border-0 bg-transparent text-success special-instruction-trigger"
                                                data-special-modal="#narrationModalSpecialInstruction1"
                                                data-bs-toggle="popover"
                                                data-bs-trigger="hover focus"
                                                data-bs-placement="top"
                                                data-bs-content="Special Instructions"
                                                aria-label="Special instructions">
                                                <i style='font-size:15px' class="ico icon-outline-add-square"></i>
                                            </button>
                                            <span></span>
                                        </label>
                                        <select class="form-control js-example-basic-single" name="payment_mode" id="payment_mode1" >
                                            <option value="">-Select-</option>
                                            <option value="1" <?php if($payment_mode == 1): ?> selected <?php else: ?> <?php if($mode_sel == 1): ?> selected
                                            <?php endif; ?> <?php endif; ?>>Cash</option>
                                            <option value="2" <?php if($payment_mode == 2): ?> selected <?php else: ?> <?php if($mode_sel == 2): ?> selected
                                            <?php endif; ?> <?php endif; ?>>Cheque</option>
                                            <option value="3" <?php if($payment_mode == 3): ?> selected <?php endif; ?>>Bank Transfer
                                            </option>
                                            <option value="4" <?php if($payment_mode == 4): ?> selected <?php endif; ?>>Open Credit</option>
                                            <option value="5" <?php if($payment_mode == 5): ?> selected <?php endif; ?>>Credit Card</option>
                                            <option value="6" <?php if($payment_mode == 6): ?> selected <?php endif; ?>>Bank TT</option>
                                            <option value="7" <?php if($payment_mode == 7): ?> selected <?php endif; ?>>Letter of Credit
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-5-custom mb-3" id="payment_mode_sec_div" style="display: none;">
                                    <div class="input-effect">
                                        <label class="form-label">Payment Mode<span></span></label>
                                        <select class="form-control js-example-basic-single" name="payment_mode_sec" id="payment_mode_sec1">
                                            <option value="">-Select-</option>
                                            <option value="1">Cash</option>
                                            <option value="2">Cheque</option>
                                            <option value="3">Bank Transfer</option>
                                            <option value="4">Open Credit</option>
                                            <option value="5">Credit Card</option>
                                            <option value="6">Bank TT</option>
                                            <option value="7">Letter of Credit</option>
                                        </select>
                                    </div>
                                </div>

                                   <div class="col-5-custom mb-3">
                                    <div class="input-effect">
                                           <label class="form-label d-flex justify-content-between"><?php echo app('translator')->getFromJson('LPO'); ?>

                                                                                    <?php
    $files = $lpo ? explode('|', $lpo) : [];
    $fileCount = count($files);
?>
<?php if($fileCount > 0): ?>
                                            <small id="lpo-file-count" class="text-success" data-bs-target="#SubmitLPOModal" data-bs-toggle="modal" style="cursor:pointer;">(<?php echo e($fileCount); ?> Files)</small>
<?php endif; ?>
                                        </label>

                                        <div class="form-group files">
                                            <input type="file" class="form-control dynamicstxt_s" multiple="multiple" id="lpo1"
                                                name="lpo[]">
                                        </div>
                                    </div>
                                </div>

                                 <div class="modal fade side-panel" 
                        id="SubmitLPOModal" 
                        data-bs-backdrop="false" 
                        tabindex="-1" 
                        aria-labelledby="SubmitLPOModalLabel" 
                        aria-hidden="true">

                        <div class="modal-dialog modal-lg" style="width:29rem">
                            <div class="modal-content">

                                <!-- Header -->
                                <div class="modal-header">
                                    <h4 class="modal-title" style="padding-left:0" id="SubmitLPOModal">LPO</h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <!-- Body -->
                                <div class="modal-body p-0">
                                    <div class="card m-0">
                                        <div class="card-body p-0">

                                            <div class="table-responsive">
                                    <table id="long-list" class="table table-hover data-table" style="table-layout: fixed;width:100%">

                                        <thead class="text-center">
                                            <tr>
                                                <th style="width: 80px;" class="text-start"><?php echo app('translator')->getFromJson('Files'); ?></th>
                                                <th style="width: 30px;" class="text-center"> <i class="ico icon-bold-paperclip"></i> </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $files = $lpo ? explode('|', $lpo) : [];
                                            ?>

                                            <?php $__empty_1 = true; $__currentLoopData = $files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                                            <tr>
                                                <td class="text-start"><?php echo e($f); ?></td>
                                                <td class="text-center"><a class="btn-sm btn-light text-dark btn-fixed" href="<?php echo e(asset('public/uploads/crm_deal_track_doc/' . $f)); ?>" title="<?php echo e($f); ?>" target="_blank"><i class="ico icon-bold-download-minimalistic  fw-bold title-15 text-success"></i></a>
                                                </td>

                                            </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                <p class="text-muted">No files uploaded.</p>
                                            <?php endif; ?>

                                        </tbody>
                                    </table>
                                    </div>


                                    

                                            

                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                                </div>

                                <div class="col-5-custom mb-3">
                                    <div class="input-effect">
                                         <label class="form-label d-flex justify-content-between"><?php echo app('translator')->getFromJson('Cheque/TT Copy'); ?>

                                                   

                                                    <?php
                                                        $files = $cheque_copy ? explode('|', $cheque_copy) : [];
                                                        $fileCount = count($files);
                                                    ?>
                                                    <?php if($fileCount > 0): ?>
                                            <small id="lpo-file-count" class="text-success" data-bs-target="#SubmitChequeTT" data-bs-toggle="modal" style="cursor:pointer;">(<?php echo e($fileCount); ?> Files)</small>
                                                    <?php endif; ?>       
                                                </label>
                                        <?php if($cheque_copy != ''): ?>
                                            <?php                    $file = explode('|', $cheque_copy); ?>
                                            <?php $__currentLoopData = $file; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <a class="text-primary" href="<?php echo e(asset('public/uploads/crm_deal_track_doc/')); ?>/<?php echo e($f); ?>"
                                                    target="_blank"><i class="fa fa-paperclip" aria-hidden="true"></i></a>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>

                                        <div class="form-group files">
                                            <input type="file" class="form-control dynamicstxt_s" multiple="multiple"
                                                id="cheque_copy1" name="cheque_copy[]">
                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade side-panel" 
                                                    id="SubmitChequeTT" 
                                                    data-bs-backdrop="false" 
                                                    tabindex="-1" 
                                                    aria-labelledby="SubmitChequeTT" 
                                                    aria-hidden="true">

                                                <div class="modal-dialog modal-lg" style="width:29rem">
                                                    <div class="modal-content">

                                                        <!-- Header -->
                                                        <div class="modal-header">
                                                            <h4 class="modal-title" style="padding-left:0" id="SubmitChequeTT">Cheque/TT Copy</h4>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>

                                                        <!-- Body -->
                                                        <div class="modal-body p-0">
                                                            <div class="card m-0">
                                                                <div class="card-body p-0">

                                                                    <div class="table-responsive">
                                                            <table id="long-list" class="table table-hover data-table" style="table-layout: fixed;width:100%">

                                                                <thead class="text-center">
                                                                    <tr>
                                                                        <th style="width: 80px;" class="text-start"><?php echo app('translator')->getFromJson('Files'); ?></th>
                                                                        <th style="width: 30px;" class="text-center"> <i class="ico icon-bold-paperclip"></i> </th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php
                                                                        $files = $cheque_copy ? explode('|', $cheque_copy) : [];
                                                                    ?>

                                                                    <?php $__empty_1 = true; $__currentLoopData = $files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                                                                    <tr>
                                                                        <td class="text-start"><?php echo e($f); ?></td>
                                                                        <td class="text-center"><a class="btn-sm btn-light text-dark btn-fixed" href="<?php echo e(asset('public/uploads/crm_deal_track_doc/' . $f)); ?>" title="<?php echo e($f); ?>" target="_blank"><i class="ico icon-bold-download-minimalistic  fw-bold title-15 text-success"></i></a>
                                                                        </td>

                                                                    </tr>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                                        <p class="text-muted">No files uploaded.</p>
                                                                    <?php endif; ?>

                                                                </tbody>
                                                            </table>
                                                            </div>


                                                            

                                                                    

                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                        </div>

                                <div class="col-5-custom mb-3">
                                    <div class="input-effect">
                                         <label class="form-label d-flex justify-content-between"><?php echo app('translator')->getFromJson('Purchase Quote'); ?>

                                                     <?php
                                                        $files = $purchease_quote ? explode('|', $purchease_quote) : [];
                                                        $fileCount = count($files);
                                                    ?>
                                                    <?php if($fileCount > 0): ?>
                                            <small id="lpo-file-count" class="text-success" data-bs-target="#SubmitPurchaseQuote" data-bs-toggle="modal" style="cursor:pointer;">(<?php echo e($fileCount); ?> Files)</small>
                                                    <?php endif; ?>  
                                                </label>

                                        <div class="form-group files">
                                            <input type="file" class="form-control dynamicstxt_s" multiple="multiple"
                                                id="purchease_quote1" name="purchease_quote[]">
                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade side-panel" 
                                                    id="SubmitPurchaseQuote" 
                                                    data-bs-backdrop="false" 
                                                    tabindex="-1" 
                                                    aria-labelledby="SubmitPurchaseQuote" 
                                                    aria-hidden="true">

                                                <div class="modal-dialog modal-lg" style="width:29rem">
                                                    <div class="modal-content">

                                                        <!-- Header -->
                                                        <div class="modal-header">
                                                            <h4 class="modal-title" style="padding-left:0" id="SubmitPurchaseQuote">Purchase Quote</h4>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>

                                                        <!-- Body -->
                                                        <div class="modal-body p-0">
                                                            <div class="card m-0">
                                                                <div class="card-body p-0">

                                                                    <div class="table-responsive">
                                                            <table id="long-list" class="table table-hover data-table" style="table-layout: fixed;width:100%">

                                                                <thead class="text-center">
                                                                    <tr>
                                                                        <th style="width: 80px;" class="text-start"><?php echo app('translator')->getFromJson('Files'); ?></th>
                                                                        <th style="width: 30px;" class="text-center"> <i class="ico icon-bold-paperclip"></i> </th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php
                                                                        $files = $purchease_quote ? explode('|', $purchease_quote) : [];
                                                                    ?>

                                                                    <?php $__empty_1 = true; $__currentLoopData = $files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                                                                    <tr>
                                                                        <td class="text-start"><?php echo e($f); ?></td>
                                                                        <td class="text-center"><a class="btn-sm btn-light text-dark btn-fixed" href="<?php echo e(asset('public/uploads/crm_deal_track_doc/' . $f)); ?>" title="<?php echo e($f); ?>" target="_blank"><i class="ico icon-bold-download-minimalistic  fw-bold title-15 text-success"></i></a>
                                                                        </td>

                                                                    </tr>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                                        <p class="text-muted">No files uploaded.</p>
                                                                    <?php endif; ?>

                                                                </tbody>
                                                            </table>
                                                            </div>


                                                            

                                                                    

                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                </div>

                                

                            

                                

                                 <div class="col-5-custom mb-3">

    
                                            <div class="input-effect" data-bs-toggle="modal" data-bs-target="#reserve_qty_popup">
                                                <label class="form-label">Reserve Qty<span></span></label>
                                                <div class="form-control d-flex align-items-center">
                                                    <input class="form-check-input ml-2 me-2 mt-0 no-dim" type="checkbox" <?php if($reserve_stock->isNotEmpty()): ?> checked <?php endif; ?> disabled data-bs-toggle="modal" data-bs-target="#reserve_qty_popup" value="1" id="reserve_qty"
                                                        name="reserve_qty">
                                                    <label class="form-label ml-4 " for="reserve_qty">Reserve Qty</label>
                                                </div>
                                            </div>

                                </div>


                                <div class="col-5-custom mb-3">
                                    <div class="input-effect ">
                                        <label class="form-label">Purchase Approval<span></span></label>
                                        <div class="form-control d-flex align-items-center">
                                            <input class="form-check-input ml-2 me-2" style="margin-top:.20rem!important" type="checkbox" value="1"
                                                id="purchease_approval1" name="purchease_approval" checked <?php if($purchease_approval == 0): ?>
                                                <?php else: ?> checked <?php endif; ?>>
                                            <label class="form-label ml-4" for="purchease_approval1">Yes,
                                                Required</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-5-custom mb-3">
                                    <div class="input-effect ">
                                        <label class="form-label">Invoice Approval<span></span></label>
                                        <div class="form-control d-flex align-items-center">
                                            <input class="form-check-input ml-2 me-2" style="margin-top:.20rem!important" type="checkbox" value="1"
                                                id="flexCheckDefault1" name="invoice_approval" <?php if($invoice_approval == 0): ?> <?php else: ?>
                                                checked <?php endif; ?>>
                                            <label class="form-label ml-4 " for="flexCheckDefault1">Yes,
                                                Required</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-5-custom mb-3">
                                    <div class="input-effect ">
                                        <label class="form-label">Delivery Approval<span></span></label>
                                        <div class="form-control d-flex align-items-center">
                                            <input class="form-check-input ml-2 me-2" style="margin-top:.20rem!important" type="checkbox" value="1"
                                                id="flexCheckDefault2" name="delivery_approval" <?php if($delivery_approval == 0): ?> <?php else: ?>
                                                checked <?php endif; ?>>
                                            <label class="form-label ml-4 " for="flexCheckDefault2">Yes,
                                                Required</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-5-custom mb-3">
                                    <div class="input-effect ">
                                        <label class="form-label">Receivables Approval<span></span></label>
                                        <div class="form-control d-flex align-items-center">
                                            <input class="form-check-input ml-2 me-2" style="margin-top:.20rem!important" type="checkbox" value="1"
                                                id="flexCheckDefault3" name="receivables_approval" <?php if($receivables_approval == 0): ?>
                                                <?php else: ?> checked <?php endif; ?>>
                                            <label class="form-label ml-4 " for="flexCheckDefault3">Yes,
                                                Required</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-5-custom mb-3">
                                    <div class="input-effect">
                                        <label class="form-label">Partial Delivery<span></span></label>
                                        <div class="form-control d-flex align-items-center">
                                            <input class="form-check-input me-2" style="margin-top:.20rem!important" type="checkbox" value="1" id="partial1"
                                                name="partial_delivery" <?php if($partial_delivery == 1): ?> checked <?php endif; ?>>

                                            <label class="form-label mb-0" for="partial1">Yes, Partial Delivery</label>
                                        </div>

                                    </div>
                                </div>
                                 <?php if(count($quotationitems) > 0): ?>

                              
                                        <?php if($quotationitems->contains('product_type', 3)): ?>
                                        
                                <div class="col-5-custom mb-3">
                                    <div class="input-effect">
                                        <label class="form-label">Professional Service<span></span></label>
                                        <div class="form-control d-flex align-items-center">
                                            <input type="hidden" name="technical" value="0" />
                                            <input class="form-check-input ml-2 me-2" style="margin-top:.20rem!important" type="checkbox" value="1" id="technical1"
                                                name="technical" <?php if($technical == 1 || $edit->is_professional_service == 1): ?> checked
                                                <?php endif; ?>>
                                            <label class="form-label ml-4 " for="technical1">Yes, Professional
                                                Service</label>
                                        </div>
                                    </div>
                                    <script>
                                        $('#technical1').on('change', function (e) {
                                            if ($('#technical1').prop('checked') == true) {
                                                $('#technical_div').css("display", "block");

                                                $('#professionalservice_popup').modal('show');
                                                
                                                // $('#technical_detail').prop('required', true);
                                                $('#technical_detail').val($('#technical_detail_hide').val());
                                            } else {
                                                $('#technical_div').css("display", "none");
                                                // $('#technical_detail').prop('required', false);
                                            }
                                        });
                                    </script>
                                </div>

                                <div class="col-5-custom mb-3" id="technical_div" style="display: none;">
                                    <div class="input-effect">
                                        <label class="form-label">Professional Service Note<span></span></label>
                                        <textarea class="dynamicstxt_s w-100 form-control capitalize-title"
                                            name="technical_detail" rows="1" autocomplete="off" id="technical_detail1"
                                            placeholder="Remarks"><?php echo e($technical_detail); ?></textarea>
                                    </div>
                                </div>
                                <?php if($technical == 1 || $edit->is_professional_service == 1): ?>
                                    <script>
                                        $('#technical_div').css("display", "block");
                                        // $('#technical_detail').prop('required', true);
                                    </script>
                                <?php endif; ?>
                                <?php endif; ?>
                                <?php endif; ?>

                                





                                      

                               



                             



                                <?php if($is_amc_item > 0): ?>
                                    <div class="col-5-custom mb-3">
                                        <div class="no-gutters input-right-icon">
                                            <div class="col">
                                                <div class="input-effect">
                                                    <label class="form-label"><?php echo app('translator')->getFromJson('Start Date'); ?><span></span></label>
                                                    <input class="form-control" id="start_date1" type="date" autocomplete="off" 
                                                        name="start_date" value="<?php echo e($start_date); ?>" >
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-5-custom mb-3">
                                        <div class="no-gutters input-right-icon">
                                            <div class="col">
                                                <div class="input-effect">
                                                    <label class="form-label"><?php echo app('translator')->getFromJson('End Date'); ?><span></span></label>
                                                    <input class="form-control" id="end_date1" type="date" autocomplete="off" 
                                                        name="end_date" value="<?php echo e($end_date); ?>" >
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-5-custom mb-3">
                                        <div class="form-group">
                                            <label for="">Invoicing</label>
                                            <select class="form-control js-example-basic-single" type="text" name="amc_invoice" id="amc_invoice1" >
                                                <option value="">-Select-</option>
                                                <option value="Monthly">Monthly</option>
                                                <option value="Quarterly">Quarterly</option>
                                                <option value="Half Yearly">Half Yearly</option>
                                                <option value="Yearly" selected>Yearly</option>
                                            </select>
                                        </div>
                                    </div>

                                <?php endif; ?>


                                <div class="col mb-3">
                                    <div class="no-gutters input-right-icon">
                                        <div class="col">
                                            <div class="input-effect">
                                                <label class="form-label"><?php echo app('translator')->getFromJson('Remarks'); ?><span></span></label>
                                                   <input class=" w-100 form-control" value="<?php echo e($remarks); ?>" data-bs-toggle="modal" data-bs-target="#narrationModalremarks1"
                                                    name="remarks" rows="1" autocomplete="off" id="remarks1"
                                                    placeholder="Remarks">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" id="special_instruction1" value="<?php echo e($special_instruction); ?>">
                            </div>
                        </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const referenceInput1 = document.getElementById('remarks1');
                        const narrationTextarea1 = document.getElementById('narrationTextarearemarks1');
                        const insertButton1 = document.getElementById('insertNarrationremarks1');
                        const narrationModal1 = document.getElementById('narrationModalremarks1');

                        // Pre-fill textarea when modal opens
                        narrationModal1.addEventListener('shown.bs.modal', () => {
                            narrationTextarea1.value = referenceInput1.value;
                        setTimeout(() => $('#narrationTextarearemarks1').focus(), 500);


                        });

                        // On insert button click, update input and close modal
                        insertButton1.addEventListener('click', () => {
                            referenceInput1.value = narrationTextarea1.value;
                            bootstrap.Modal.getInstance(narrationModal1).hide();
                        });
                    });
                </script>

                <div class="modal side-panel fade" id="narrationModalremarks1" data-bs-backdrop="false" tabindex="-1"
                    aria-labelledby="editModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="poexcelimport">Enter Remarks</h4>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body m-0 p-0">
                                <div class="card mb-0 mt-0">
                                    <div class="card-body">
                                        <textarea style="height: 109px !important;" class="form-control capitalize-title" id="narrationTextarearemarks1" rows="6"
                                            placeholder="Write remarks here..."></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" id="insertNarrationremarks1" class="btn btn-light add-btn ms-2">
                                    <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                

                        <?php
                            $dealTrackForRecall = App\SysCrmDealTrack::where('deal_id', $edit->id)->orderBy('id', 'desc')->first();
                            $canRecallDealTrack = !empty($dealTrackForRecall) && ((int) ($dealTrackForRecall->accounts ?? 0) !== 1);
                        ?>
                        <div class="modal-footer">
                            <input type="hidden" id="deal_id1" name="deal_id" value="<?php echo e($edit->id); ?>" />
                            <button type="button" class="btn btn-light add-btn ms-2" value="save" name="btnSubmit"
                                id="btnSave"><span class="ti-check"></span><i
                                    class="ico icon-outline-bookmark-opened text-success"></i> Save</button>
                            <button type="button" class="btn btn-light add-btn ms-2" value="approve" name="btnSubmit"
                                id="btnApprove"><span class="ti-check"></span><i
                                    class="ico icon-outline-bookmark-opened text-success"></i> Submit For Approval</button>
                            <?php if($canRecallDealTrack): ?>
                                <button type="button" class="btn btn-light add-btn ms-2" id="btnRecall">
                                    <span class="ti-back-left"></span><i
                                        class="ico icon-outline-close-circle text-danger"></i> Recall
                                </button>
                            <?php endif; ?>
                        </div>


                        <script>
                            $(document).ready(function () {
                                let isDealTrackSubmitting = false;

                                // Function to handle the form submission
                                function submitDealTrack(action) {
                                    if (isDealTrackSubmitting) {
                                        return;
                                    }

                                    var formData = new FormData();

                                    // Collect all inputs by ID
                                    formData.append('delivery_date', $('#delivery_date1').val());
                                    formData.append('payment_terms', $('#payment_terms1').val());
                                    formData.append('payment_terms_txt', $('#payment_terms1_txt1').val());
                                    formData.append('payment_mode', $('#payment_mode1').val());
                                    formData.append('payment_mode_sec', $('#payment_mode_sec1').val());
                                    // formData.append('purchease_required', $('#purchease_required1').is(':checked') ? 1 : 0);
                                    formData.append('partial_delivery', $('#partial1').is(':checked') ? 1 : 0);
                                    // formData.append('technical', $('#technical1').is(':checked') ? 1 : 0);
                                    if ($('#technical1').length) {
                                        // element exists
                                        technical = $('#technical1').is(':checked') ? 1 : 0;
                                    } else {
                                        // element does not exist
                                        technical = 0;
                                    }
                                    formData.append('technical', technical);
                                    formData.append('start_date', $('#start_date1').val() ? $('#start_date1').val() : '');
                                    formData.append('end_date', $('#end_date1').val() ? $('#end_date1').val() : '');
                                    formData.append('amc_invoice', $('#amc_invoice1').val());
                                    formData.append('technical_detail', $('#technical_detail1').val());
                                    formData.append('purchease_approval', $('#purchease_approval1').is(':checked') ? 1 : 0);
                                    formData.append('invoice_approval', $('#flexCheckDefault1').is(':checked') ? 1 : 0);
                                    formData.append('delivery_approval', $('#flexCheckDefault2').is(':checked') ? 1 : 0);
                                    formData.append('receivables_approval', $('#flexCheckDefault3').is(':checked') ? 1 : 0);
                                    
                                       let referenceNo = $('#reference_no1').val().trim();
                                    // If empty → show alert and stop request
                                    if (!referenceNo) {
                                        alert("Reference number is required!");
                                        return; // stop AJAX request
                                    }

                                    formData.append('reference_no', $('#reference_no1').val());
                                    formData.append('reference_date', $('#reference_date1').val() ? $('#reference_date1').val() : null);
                                    formData.append('remarks', $('#remarks1').val());
                                    formData.append('special_instruction', $('#special_instruction1').val());
                                    formData.append('deal_id', $('#deal_id1').val());
                                    formData.append('quote_id', $('input[name="quote_id"]').first().val() || '');
                                    formData.append('btnSubmit', action); // save or approve

                                    // Attach multiple files
                                    var lpoEl = $('#lpo1')[0];
                                    if (lpoEl && lpoEl.files) {
                                        for (var i = 0; i < lpoEl.files.length; i++) {
                                            formData.append('lpo[]', lpoEl.files[i]);
                                        }
                                    }

                                    var chequeEl = $('#cheque_copy1')[0];
                                    if (chequeEl && chequeEl.files) {
                                        for (var j = 0; j < chequeEl.files.length; j++) {
                                            formData.append('cheque_copy[]', chequeEl.files[j]);
                                        }
                                    }

                                    var purchaseEl = $('#purchease_quote1')[0];
                                    if (purchaseEl && purchaseEl.files) {
                                        for (var k = 0; k < purchaseEl.files.length; k++) {
                                            formData.append('purchease_quote[]', purchaseEl.files[k]);
                                        }
                                    }

                                    isDealTrackSubmitting = true;

                                    // AJAX call
                                    $.ajax({
                                        url: "<?php echo e(url('crm-deal-track-submit')); ?>",
                                        type: 'POST',
                                        data: formData,
                                        contentType: false,
                                        processData: false,
                                        headers: {
                                            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                                        },
                                        beforeSend: function () {
                                            $("#loading_bg").css("display", "block");
                                            $('#btnSave, #btnApprove, #btnRecall, #btnRecallTop, #btnRecallEditTrack').prop('disabled', true);
                                        },
                                        success: function (response) {
                                            console.log(response)
                                            $("#loading_bg").css("display", "none");
                                            

                                            if(action === 'approve') {
                                            toastr.success("Deal Track Submitted For Approval", "Success");
                                            // location.reload();
                                             // CALL THE SAME FUNCTION HERE
                                             loadDealTrackDetails(response.id);
                                                // window.location.href = "/crm-deal-track-approval-list/" + response.id;
                                            
                                                return;
                                            }else{
                                            toastr.success("Deal Track Submitted successfully", "Success");
                                            location.reload();

                                            }




                                        },
                                        error: function (xhr) {
                                            $("#loading_bg").css("display", "none");


                                            var err = xhr.responseJSON;
                                            if (err && err.errors) {
                                                var msg = '';
                                                $.each(err.errors, function (key, value) {
                                                    msg += value[0] + "\n";
                                                });
                                                alert(msg);
                                            } else {
                                                alert('An error occurred, please try again.');
                                            }
                                        },
                                        complete: function () {
                                            isDealTrackSubmitting = false;
                                            $('#btnSave, #btnApprove, #btnRecall, #btnRecallTop, #btnRecallEditTrack').prop('disabled', false);
                                        }
                                    });
                                }

                                // Save button
                                $('#btnSave').click(function (e) {
                                    e.preventDefault();
                                    submitDealTrack('save');
                                });

                                // Submit for Approval button
                                $('#btnApprove').click(function (e) {
                                    e.preventDefault();
                                    submitDealTrack('approve');
                                });

                                $('#btnRecall, #btnRecallTop, #btnRecallEditTrack').click(function (e) {
                                    e.preventDefault();

                                    if (!confirm('Are you sure you want to recall this deal track?')) {
                                        return;
                                    }

                                    $.ajax({
                                        url: "<?php echo e(url('crm-deal-track-recall')); ?>",
                                        type: 'POST',
                                        data: {
                                            deal_id: $('#deal_id2').val() || $('#deal_id1').val()
                                        },
                                        headers: {
                                            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                                        },
                                        beforeSend: function () {
                                            $("#loading_bg").css("display", "block");
                                            $('#btnSave, #btnApprove, #btnRecall, #btnRecallTop, #btnRecallEditTrack').prop('disabled', true);
                                        },
                                        success: function () {
                                            $("#loading_bg").css("display", "none");
                                            toastr.success("Deal Track Recalled Successfully", "Success");
                                            location.reload();
                                        },
                                        error: function (xhr) {
                                            $("#loading_bg").css("display", "none");
                                            var msg = 'Unable to recall deal track.';
                                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                                msg = xhr.responseJSON.message;
                                            }
                                            toastr.error(msg, "Failed");
                                        },
                                        complete: function () {
                                            $('#btnSave, #btnApprove, #btnRecall, #btnRecallTop, #btnRecallEditTrack').prop('disabled', false);
                                        }
                                    });
                                });

                            });
                        </script>

</div>
                  
                <?php else: ?>
                
                    <?php if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || $check_edit_fullfill == 0): ?>
                        
                        
                           


                            <?php if(App\SysHelper::get_company_status($edit->customername) == 0): ?>

                

                             
                                <?php
                                    $validation = @App\SysHelper::get_customer_incomplete_fields($edit->customername);
                                ?>

                                  <?php
                                        $editDoc = @App\SysCustSupplDoc::where('cust_suppl_id', $edit->customername->id)->get();
                                    ?>

                                 

                                        <?php
                                        $ids = array_column($validation['errors'], 'id');
                                        ?>

                                    <div class="row">

                                        <?php if(in_array('vat_number', $ids)): ?>
                                         <div class="col">
                                        <label for="" class="form-label">VAT Number</label>
                                            <div class=""><input class="form-control" type="text" name="vat_number" id="ci_vat_number"
                                                    value="<?php echo e($edit->customername->vat_number); ?>">
                                                </div>
                                        </div>
                                        <?php endif; ?>
                                     
                                       
                                        <?php if(in_array('mobile', $ids)): ?>
                                        <div class="col">
                                            <label for="" class="form-label">Customer Mobile</label>
                                            <input class="form-control" type="text" name="mobile" id="ci_mobile" placeholder="Mobile"
                                                value="<?php echo e($edit->customername->mobile); ?>">

                                        </div>
                                        <?php endif; ?>


                                        <?php if(in_array('email', $ids)): ?>

                                        <div class="col">
                                            <label for="" class="form-label">Customer Email</label>
                                            <input class="form-control" type="text" name="email" id="ci_email" placeholder="Email"
                                                value="<?php echo e($edit->customername->email); ?>" >
                                        </div>

                                        <?php endif; ?>

                                        <?php if(in_array('first_name', $ids)): ?>

                                          <!-- First Name -->
                                        <div class="col">
                   
                                            <label class="form-label mb-0 me-3" style="min-width: 120px;">Primary
                                                Contact:</label>

                                            <input type="text" class="form-control" id="ci_firstName"
                                                name="first_name" placeholder="First Name"
                                                value="<?php echo e(isset($edit->customername) ? @$edit->customername->first_name : ''); ?> <?php echo e(isset($edit->customername) ? @$edit->customername->last_name : ''); ?>">
                                        </div>
                                            
                                        <?php endif; ?>

                                        <?php if(in_array('contact_number', $ids)): ?>

                                             <div class="col ">
                                            <label for="" class="form-label">Customer Phone</label>
                                            <input class="form-control" type="text" name="mobile_code" id="ci_mobile_code" placeholder="Work Phone"
                                                value="<?php echo e($edit->customername->contcat_number); ?>" >
                                        </div> 
                                            
                                        <?php endif; ?>
                                      

                                        <?php
                                            $exists = App\SysCustSupplDoc::where('cust_suppl_id', $edit->customername->id)
                                                        ->where('doc_name', 'Trade License/Commercial Registration')
                                                              ->whereNull('deleted_at') // <-- only consider not deleted

                                                        ->exists();

                                                            $existsVat = App\SysCustSupplDoc::where('cust_suppl_id', $edit->customername->id)
                                                        ->where('doc_name', 'VAT Certificate')
                                                              ->whereNull('deleted_at') // <-- only consider not deleted

                                                        ->exists();

                                                 
                                        ?>                         
                                        <?php if(!$exists): ?>
                                   
                                        
                                        <div class="col">
                                            <input class="form-control" type="hidden" name="doc_name[]"
                                            value="Trade License/Commercial Registration" readonly />
                                                <label for="" class="form-label">Trade License/Commercial Registration</label>
                                                  <input class="form-control" type="file" name="customer_documents_1" id="ci_trade_doc" />
                                                   <input class="form-control date-picker" type="text" id="ci_trade_exp_date" name="doc_exp_date[]"
                                            placeholder="Expiry Date" />
                                        </div>

                                        <?php endif; ?>

                                        <?php if(!$existsVat): ?>

                                         <div class="col ">
                                           <input class="form-control" type="hidden" name="doc_name[]"
                                            value="VAT Certificate" readonly />
                                                <label for="" class="form-label">VAT Certificate</label>
                                                 <input class="form-control" type="file" name="customer_documents_2" id="ci_vat_doc" />
                                        </div>

                                        
                                            
                                        <?php endif; ?>


                                       
                                        

                                   
                                 
                                  

                               

                                        

                                       
                                     



                                    </div>

                                    
                              <script>
$(document).ready(function () {

    function updateCustomerEdit() {
        let fd = new FormData();


// inline DOM checks and appends (no helper functions)
let el;

el = document.getElementById('customer_edit_id'); if (el) fd.append('cust_id', el.value);
el = document.getElementById('ci_vat_number');    if (el) fd.append('vat_number', el.value);
el = document.getElementById('ci_mobile');        if (el) fd.append('mobile', el.value);
el = document.getElementById('ci_email');         if (el) fd.append('email', el.value);
el = document.getElementById('ci_salutation');    if (el) fd.append('customer_salutation', el.value);
el = document.getElementById('ci_firstName');     if (el) fd.append('first_name', el.value);
el = document.getElementById('ci_mobile_code');   if (el) fd.append('mobile_code', el.value);

// document names (only if related input exists in DOM)
if (document.getElementById('ci_trade_doc')) fd.append('doc_name[0]', 'Trade License/Commercial Registration');
if (document.getElementById('ci_vat_doc'))   fd.append('doc_name[1]', 'VAT Certificate');

// expiry dates
el = document.getElementById('ci_trade_exp_date'); if (el) fd.append('doc_exp_date[0]', el.value);

// files (check existence and length)
el = document.getElementById('ci_trade_doc');
if (el && el.files && el.files.length > 0) fd.append('customer_documents_1', el.files[0]);

el = document.getElementById('ci_vat_doc');
if (el && el.files && el.files.length > 0) fd.append('customer_documents_2', el.files[0]);

// fd is ready to send via fetch / $.ajax / XHR


        fd.append("_token", "<?php echo e(csrf_token()); ?>");

        $.ajax({
            url: "<?php echo e(url('customer-update-deal-track')); ?>",
            method: "POST",
            data: fd,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $("#loading_bg").show();
            },
            success: function (res) {
                $("#loading_bg").hide();

                if (res.status) {
                    toastr.success(res.message);
                    location.reload();
                } else {
                    toastr.error(res.message);
                }
            },
            error: function (xhr) {
                $("#loading_bg").hide();
                toastr.error("Something went wrong!");
            }
        });
    }

    $("#btnupdateCustomer").on("click", function (e) {
        e.preventDefault();
        updateCustomerEdit();
    });

});
                            </script>


                            


                                  

                                        

                            <div class="row pt-3" style="border-top: 1px solid #dee2e6">
                                <div class="col-4">
                                  
                                </div>
                                <div class="col-4 d-flex justify-content-center">
            <input type="hidden" id="customer_edit_id" name="customer_edit_id" value="<?php echo e($edit->customername->id); ?>" />
                                    <button type="button" class="btn btn-light add-btn ms-2" 
                                        id="btnupdateCustomer"><span class="ti-check"></span><i
                                            class="ico icon-outline-bookmark-opened text-success"></i> Update Customer</button>
                                </div>
                                <div class="col-4"></div>
                            </div>
                                        

                <style>
                   .deal-track-wrapper {
    position: relative;
}

/* More transparent overlay */
.deal-track-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.15);  /* <--- much lighter */
    z-index: 10;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(1px);  /* light blur */
}

/* Visible background text under message */
.deal-track-overlay-text {
    font-size: 20px;
    color: #fff;
    padding: 15px 25px;
    background: rgba(0,0,0,0.35);  /* <--- lighter text box */
    border-radius: 10px;
  
    text-align: center;
}

                </style>
                            <?php endif; ?>

                            <div class="deal-track-wrapper position-relative mt-1">

<?php if(App\SysHelper::get_company_status($edit->customername) == 0): ?>

       <div class="deal-track-overlay">
        <div class="deal-track-overlay-text">
            ⚠️ Please update customer to submit for deal approval
        </div>
    </div>
<?php endif; ?>

                             <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="mb-0" style="font-size: 1.1rem;">Edit Deal Track</h4>
                                <a target="__blank" href="<?php echo e(url('crm-deal-track-approval-list/' . $edit->track->id)); ?>"
                                    class=" btn-light btn-sm ">
                                    View Deal Track
                                </a>
                            </div>

                                <?php
                                    $edit_delivery_date = '';
                                    $edit_payment_terms = '';
                                    $edit_payment_mode = '';
                                    $edit_purchease_required = '';
                                    $edit_partial_delivery = '';
                                    $edit_technical = '';
                                    $edit_technical_detail = '';
                                    $edit_lpo = '';
                                    $edit_cheque_copy = '';
                                    $edit_purchease_quote = '';
                                    $edit_remarks = '';
                                    $edit_special_instruction = '';
                                    $edit_reference_no = '';
                                    $edit_reference_date = '';
                                    $edit_purchease_approval = 1;
                                    $edit_invoice_approval = 1;
                                    $edit_delivery_approval = 1;
                                    $edit_receivables_approval = 1;
                                    $start_date = '';
                                    $end_date = '';

                                    if (isset($deal_track)) {
                                        $edit_delivery_date = $deal_track->delivery_date;
                                        $edit_payment_terms = $deal_track->payment_terms;
                                        $edit_payment_mode = $deal_track->payment_mode;
                                        $edit_purchease_required = $deal_track->purchease_required;
                                        $edit_partial_delivery = $deal_track->partial_delivery;
                                        $edit_technical = $deal_track->technical;
                                        $edit_technical_detail = $deal_track->technical_detail;
                                        $edit_lpo = $deal_track->lpo;
                                        $edit_cheque_copy = $deal_track->cheque_copy;
                                        $edit_purchease_quote = $deal_track->purchease_quote;
                                        $edit_remarks = $deal_track->remarks;
                                        $edit_special_instruction = $deal_track->special_instruction ?? '';
                                        $edit_reference_no = $deal_track->reference_no;
                                        $edit_reference_date = $deal_track->reference_date;
                                        $edit_purchease_approval = $deal_track->purchease_approval;
                                        $edit_invoice_approval = $deal_track->invoice_approval;
                                        $edit_delivery_approval = $deal_track->delivery_approval;
                                        $edit_receivables_approval = $deal_track->receivables_approval;
                                        $start_date = $deal_track->start_date;
                                        $end_date = $deal_track->end_date;
                                        $invoicing = $deal_track->invoicing;
                                    }
                                ?>
                                <div class="">
                                    <script>
                                    $(document).on('keydown', 'input, select, textarea', function (e) {
                                        if (e.key === 'Enter') {
                                            e.preventDefault(); // stop form submit
                                            
                                            let focusable = $('input, select, textarea')
                                                .filter(':visible:not([disabled])'); // all visible fields
                                            
                                            let index = focusable.index(this); // current field index
                                            
                                            if (index > -1 && index + 1 < focusable.length) {
                                                focusable.eq(index + 1).focus();
                                            }
                                        }
                                    });
                                    </script>

                                    <div class="row">
                                        <div class="col-5-custom mb-3">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="input-effect">
                                                        <label class="form-label"><?php echo app('translator')->getFromJson('Expected Delivery Date'); ?><span></span></label>

                                                        <input class="form-control date-picker" id="delivery_date2" type="text" autofocus
                                                            autocomplete="off"  name="delivery_date"
                                                            value="<?php echo e(@App\SysHelper::normalizeToDmy($edit_delivery_date)); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                           <div class="col-5-custom mb-3">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="input-effect">
                                                        <label class="form-label"><?php echo app('translator')->getFromJson('LPO/Reference No'); ?><span></span></label>
                                                        <input class="form-control" id="reference_no" type="text" autocomplete="off"
                                                             name="reference_no" value="<?php echo e($edit_reference_no); ?>" >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                           <div class="col-5-custom mb-3">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="input-effect">
                                                        <label class="form-label"><?php echo app('translator')->getFromJson('LPO/Reference Date'); ?><span></span></label>
                                                        <input class="form-control date-picker" id="reference_date" type="text"
                                                            autocomplete="off"  name="reference_date"
                                                            value="<?php echo e(@App\SysHelper::normalizeToDmy($edit_reference_date)); ?>" >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-5-custom mb-3" >
                                            <div class="input-effect">
                                                <label class="form-label">Payment Terms<span></span></label>
                                                <select class="form-control js-example-basic-single" name="payment_terms" id="payment_terms2" >
                                                    <option value="">-Select-</option>
                                                    <?php $__currentLoopData = $paymentterms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e(@$value->id); ?>" <?php if($edit_payment_terms != ''): ?> <?php if(@$edit_payment_terms == @$value->id): ?> selected <?php endif; ?> <?php else: ?> <?php if(isset($quotationitems)): ?> <?php if(@$quotationitems[0]->payment_terms == @$value->id): ?>
                                                        selected <?php endif; ?> <?php endif; ?> <?php endif; ?>><?php echo e(@$value->title); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                                <script>
                                                    $('#payment_terms2').on('change', function (e) {
                                                        if ($('#payment_terms2').val() == 20 || $('#payment_terms2').val() == 21) {
                                                            $('#payment_mode_sec_div2').css("display", "none");
                                                            //$('#payment_mode_sec').prop('required', true);
                                                        } else {
                                                            $('#payment_mode_sec_div2').css("display", "none");
                                                            //$('#payment_mode_sec').prop('required', false);
                                                        }

                                                        if ($('#payment_terms2').val() == 1 || $('#payment_terms2').val() == 2) {
                                                            $('#payment_mode2').val(1);
                                                        } else {
                                                            $('#payment_mode2').val(2);
                                                        }

                                                        if ($('#payment_terms2').val() == 22) {
                                                            $('#payment_terms2_txt').css("display", "block");
                                                            // $('#payment_terms2_txt').prop('required', true);
                                                        } else {
                                                            $('#payment_terms2_txt').css("display", "none");
                                                            // $('#payment_terms2_txt').prop('required', false);
                                                        }
                                                    });
                                                </script>
                                                <input class="form-control" id="payment_terms2_txt" type="text"
                                                    value="<?php echo e(@$quotationitems[0]->payment_terms_txt); ?>" autocomplete="off"
                                                    placeholder="Payment Terms" name="payment_terms_txt" style="display: none;">
                                            </div>
                                        </div>
                                        <?php
                                            $mode_sel = 0;
                                            if (@$quotationitems[0]->payment_terms == 1 || @$quotationitems[0]->payment_terms == 2) {
                                                $mode_sel = 1;
                                            } else {
                                                $mode_sel = 2;
                                            }

                                        ?>
                                        <div class="col-5-custom mb-3">
                                            <div class="input-effect">
                                                <label class="form-label d-flex align-items-center gap-2">
                                                    <span>Payment Mode</span>
                                                    <button type="button" class="btn p-0 border-0 bg-transparent text-success special-instruction-trigger"
                                                        data-special-modal="#narrationModalSpecialInstruction"
                                                        data-bs-toggle="popover"
                                                        data-bs-trigger="hover focus"
                                                        data-bs-placement="top"
                                                        data-bs-content="Special Instructions"
                                                        aria-label="Special instructions">
                                                        <i style='font-size:15px' class="ico icon-outline-add-square"></i>
                                                    </button>
                                                    <span></span>
                                                </label>
                                                <select class="form-control js-example-basic-single" name="payment_mode" id="payment_mode2" >
                                                    <option value="">-Select-</option>
                                                    <option value="1" <?php if($edit_payment_mode == 1): ?> selected <?php else: ?> <?php if($mode_sel == 1): ?>
                                                    selected <?php endif; ?> <?php endif; ?>>Cash</option>
                                                    <option value="2" <?php if($edit_payment_mode == 2): ?> selected <?php else: ?> <?php if($mode_sel == 2): ?>
                                                    selected <?php endif; ?> <?php endif; ?>>Cheque</option>
                                                    <option value="3" <?php if($edit_payment_mode == 3): ?> selected <?php endif; ?>>Bank Transfer
                                                    </option>
                                                    <option value="4" <?php if($edit_payment_mode == 4): ?> selected <?php endif; ?>>Open Credit
                                                    </option>
                                                    <option value="5" <?php if($edit_payment_mode == 5): ?> selected <?php endif; ?>>Credit Card
                                                    </option>
                                                    <option value="6" <?php if($edit_payment_mode == 6): ?> selected <?php endif; ?>>Bank TT</option>
                                                    <option value="7" <?php if($edit_payment_mode == 7): ?> selected <?php endif; ?>>Letter of Credit
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-5-custom mb-3" id="payment_mode_sec_div2" style="display: none;">
                                            <div class="input-effect">
                                                <label class="form-label">Payment Mode<span></span></label>
                                                <select class="form-control js-example-basic-single" name="payment_mode_sec" id="payment_mode_sec">
                                                    <option value="">-Select-</option>
                                                    <option value="1">Cash</option>
                                                    <option value="2">Cheque</option>
                                                    <option value="3">Bank Transfer</option>
                                                    <option value="4">Open Credit</option>
                                                    <option value="5">Credit Card</option>
                                                    <option value="6">Bank TT</option>
                                                    <option value="7">Letter of Credit</option>
                                                </select>
                                            </div>
                                        </div>

                                         <div class="col-5-custom mb-3">
                                            <div class="input-effect">
                                                   <label class="form-label d-flex justify-content-between">
                                            <span><?php echo app('translator')->getFromJson('LPO'); ?></span>
                                            <?php
    $files = $edit_lpo ? explode('|', $edit_lpo) : [];
    $fileCount = count($files);
?>
<?php if($fileCount > 0): ?>
                                            <small id="lpo-file-count" class="text-success" data-bs-target="#LPOModal" data-bs-toggle="modal" style="cursor:pointer;">(<?php echo e($fileCount); ?> Files)</small>
<?php endif; ?>
                                        </label>

                                                <div class="form-group files">
                                                    <input type="file" id="lpo2" class="form-control dynamicstxt_s" multiple="multiple"
                                                        name="lpo[]">
                                                </div>
                                            </div>
                                        </div>

                                               <div class="modal fade side-panel" 
                                                        id="LPOModal" 
                                                        data-bs-backdrop="false" 
                                                        tabindex="-1" 
                                                        aria-labelledby="LPOModalLabel" 
                                                        aria-hidden="true">

                                                        <div class="modal-dialog modal-lg" style="width:29rem">
                                                            <div class="modal-content">

                                                                <!-- Header -->
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title" style="padding-left:0" id="LPOModalLabel">LPO</h4>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>

                                                                <!-- Body -->
                                                                <div class="modal-body p-0">
                                                                    <div class="card m-0">
                                                                        <div class="card-body p-0">

                                                                            <div class="table-responsive">
                                                                    <table id="long-list" class="table table-hover data-table" style="table-layout: fixed;width:100%">

                                                                        <thead class="text-center">
                                                                            <tr>
                                                                                <th style="width: 80px;" class="text-start"><?php echo app('translator')->getFromJson('Files'); ?></th>
                                                                                <th style="width: 30px;" class="text-center"> <i class="ico icon-bold-paperclip"></i> </th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <?php
                                                                                $files = $edit_lpo ? explode('|', $edit_lpo) : [];
                                                                            ?>

                                                                            <?php $__empty_1 = true; $__currentLoopData = $files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                                                                            <tr>
                                                                                <td class="text-start"><?php echo e($f); ?></td>
                                                                                <td class="text-center"><a class="btn-sm btn-light text-dark btn-fixed" href="<?php echo e(asset('public/uploads/crm_deal_track_doc/' . $f)); ?>" title="<?php echo e($f); ?>" target="_blank"><i class="ico icon-bold-download-minimalistic  fw-bold title-15 text-success"></i></a>
                                                                                </td>

                                                                            </tr>
                                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                                                <p class="text-muted">No files uploaded.</p>
                                                                            <?php endif; ?>

                                                                        </tbody>
                                                                    </table>
                                                                    </div>


                                                                    

                                                                            

                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                </div>


                                        <div class="col-5-custom mb-3">
                                            <div class="input-effect">
                                               <label class="form-label d-flex justify-content-between"><?php echo app('translator')->getFromJson('Cheque/TT Copy'); ?>

                                                   

                                                    <?php
                                                        $files = $edit_cheque_copy ? explode('|', $edit_cheque_copy) : [];
                                                        $fileCount = count($files);
                                                    ?>
                                                    <?php if($fileCount > 0): ?>
                                            <small id="lpo-file-count" class="text-success" data-bs-target="#ChequeTT" data-bs-toggle="modal" style="cursor:pointer;">(<?php echo e($fileCount); ?> Files)</small>
                                                    <?php endif; ?>       
                                                </label>
                                                <div class="form-group files">
                                                    <input type="file" id="cheque2" class="form-control dynamicstxt_s"
                                                        multiple="multiple" name="cheque_copy[]">
                                                </div>
                                            </div>
                                        </div>

                                            <div class="modal fade side-panel" 
                                                    id="ChequeTT" 
                                                    data-bs-backdrop="false" 
                                                    tabindex="-1" 
                                                    aria-labelledby="ChequeTT" 
                                                    aria-hidden="true">

                                                <div class="modal-dialog modal-lg" style="width:29rem">
                                                    <div class="modal-content">

                                                        <!-- Header -->
                                                        <div class="modal-header">
                                                            <h4 class="modal-title" style="padding-left:0" id="ChequeTT">Cheque/TT Copy</h4>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>

                                                        <!-- Body -->
                                                        <div class="modal-body p-0">
                                                            <div class="card m-0">
                                                                <div class="card-body p-0">

                                                                    <div class="table-responsive">
                                                            <table id="long-list" class="table table-hover data-table" style="table-layout: fixed;width:100%">

                                                                <thead class="text-center">
                                                                    <tr>
                                                                        <th style="width: 80px;" class="text-start"><?php echo app('translator')->getFromJson('Files'); ?></th>
                                                                        <th style="width: 30px;" class="text-center"> <i class="ico icon-bold-paperclip"></i> </th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php
                                                                        $files = $edit_cheque_copy ? explode('|', $edit_cheque_copy) : [];
                                                                    ?>

                                                                    <?php $__empty_1 = true; $__currentLoopData = $files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                                                                    <tr>
                                                                        <td class="text-start"><?php echo e($f); ?></td>
                                                                        <td class="text-center"><a class="btn-sm btn-light text-dark btn-fixed" href="<?php echo e(asset('public/uploads/crm_deal_track_doc/' . $f)); ?>" title="<?php echo e($f); ?>" target="_blank"><i class="ico icon-bold-download-minimalistic  fw-bold title-15 text-success"></i></a>
                                                                        </td>

                                                                    </tr>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                                        <p class="text-muted">No files uploaded.</p>
                                                                    <?php endif; ?>

                                                                </tbody>
                                                            </table>
                                                            </div>


                                                            

                                                                    

                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                        </div>
                                        <div class="col-5-custom mb-3">
                                            <div class="input-effect">
                                                  <label class="form-label d-flex justify-content-between"><?php echo app('translator')->getFromJson('Purchase Quote'); ?>

                                                     <?php
                                                        $files = $edit_purchease_quote ? explode('|', $edit_purchease_quote) : [];
                                                        $fileCount = count($files);
                                                    ?>
                                                    <?php if($fileCount > 0): ?>
                                            <small id="lpo-file-count" class="text-success" data-bs-target="#PurchaseQuote" data-bs-toggle="modal" style="cursor:pointer;">(<?php echo e($fileCount); ?> Files)</small>
                                                    <?php endif; ?>  
                                                </label>
                                             

                                                <div class="form-group files">
                                                    <input type="file" id="poquote2" class="form-control dynamicstxt_s"
                                                        multiple="multiple" name="purchease_quote[]">
                                                </div>
                                            </div>
                                        </div>

                                        
                                        <div class="modal fade side-panel" 
                                                    id="PurchaseQuote" 
                                                    data-bs-backdrop="false" 
                                                    tabindex="-1" 
                                                    aria-labelledby="PurchaseQuote" 
                                                    aria-hidden="true">

                                                <div class="modal-dialog modal-lg" style="width:29rem">
                                                    <div class="modal-content">

                                                        <!-- Header -->
                                                        <div class="modal-header">
                                                            <h4 class="modal-title" style="padding-left:0" id="PurchaseQuote">Purchase Quote</h4>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>

                                                        <!-- Body -->
                                                        <div class="modal-body p-0">
                                                            <div class="card m-0">
                                                                <div class="card-body p-0">

                                                                    <div class="table-responsive">
                                                            <table id="long-list" class="table table-hover data-table" style="table-layout: fixed;width:100%">

                                                                <thead class="text-center">
                                                                    <tr>
                                                                        <th style="width: 80px;" class="text-start"><?php echo app('translator')->getFromJson('Files'); ?></th>
                                                                        <th style="width: 30px;" class="text-center"> <i class="ico icon-bold-paperclip"></i> </th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php
                                                                        $files = $edit_purchease_quote ? explode('|', $edit_purchease_quote) : [];
                                                                    ?>

                                                                    <?php $__empty_1 = true; $__currentLoopData = $files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                                                                    <tr>
                                                                        <td class="text-start"><?php echo e($f); ?></td>
                                                                        <td class="text-center"><a class="btn-sm btn-light text-dark btn-fixed" href="<?php echo e(asset('public/uploads/crm_deal_track_doc/' . $f)); ?>" title="<?php echo e($f); ?>" target="_blank"><i class="ico icon-bold-download-minimalistic  fw-bold title-15 text-success"></i></a>
                                                                        </td>

                                                                    </tr>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                                        <p class="text-muted">No files uploaded.</p>
                                                                    <?php endif; ?>

                                                                </tbody>
                                                            </table>
                                                            </div>


                                                            

                                                                    

                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                        </div>

                                        
                                       
                                    

                                        

                                          <div class="col-5-custom mb-3">
                                            <div class="input-effect" data-bs-toggle="modal" data-bs-target="#reserve_qty_popup">
                                                <label class="form-label">Reserve Qty<span></span></label>
                                                <div class="form-control d-flex align-items-center">
                                                    <input class="form-check-input ml-2 me-2 mt-0 no-dim" type="checkbox" <?php if($reserve_stock->isNotEmpty()): ?> checked <?php endif; ?> disabled data-bs-toggle="modal" data-bs-target="#reserve_qty_popup" value="1" id="reserve_qty"
                                                        name="reserve_qty">
                                                    <label class="form-label ml-4 " for="reserve_qty">Reserve Qty</label>
                                                </div>
                                            </div>

                                        </div>



                                         <div class="col-5-custom mb-3">
                                            <div class="input-effect ">
                                                <label class="form-label">Purchase Approval<span></span></label>
                                                <div class="form-control d-flex align-items-center">
                                                    <input class="form-check-input ml-2 me-2 mt-0"  type="checkbox" value="1"
                                                        id="purchease_approval2" name="purchease_approval" <?php if($edit_purchease_approval == 0): ?> <?php else: ?> checked <?php endif; ?> <?php if(Auth::user()->role_id != 1 && Auth::user()->role_id != 2): ?> <?php if($deal_track->accounts == 1): ?> disabled <?php endif; ?>
                                                        <?php endif; ?>>
                                                    <label class="form-label ml-4 " for="purchease_approval2">Yes,
                                                        Required</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-5-custom mb-3">
                                            <div class="input-effect ">
                                                <label class="form-label">Invoice Approval<span></span></label>
                                                <div class="form-control d-flex align-items-center">
                                                    <input class="form-check-input ml-2 me-2 mt-0" type="checkbox" value="1"
                                                        id="flexCheckDefaultinvoice" name="invoice_approval" <?php if($edit_invoice_approval == 0): ?> <?php else: ?> checked <?php endif; ?> <?php if(Auth::user()->role_id != 1 && Auth::user()->role_id != 2): ?> <?php if($deal_track->accounts == 1): ?> disabled <?php endif; ?>
                                                        <?php endif; ?>>
                                                    <label class="form-label ml-4" for="flexCheckDefaultinvoice">Yes,
                                                        Required</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-5-custom mb-3">
                                            <div class="input-effect ">
                                                <label class="form-label">Delivery Approval<span></span></label>
                                                <div class="form-control d-flex align-items-center">
                                                    <input class="form-check-input ml-2 me-2 mt-0" type="checkbox" value="1"
                                                        id="flexCheckDefaultdel" name="delivery_approval" <?php if($edit_delivery_approval == 0): ?> <?php else: ?> checked <?php endif; ?> <?php if(Auth::user()->role_id != 1 && Auth::user()->role_id != 2): ?> <?php if($deal_track->accounts == 1): ?> disabled <?php endif; ?>
                                                        <?php endif; ?>>
                                                    <label class="form-label ml-4 " for="flexCheckDefaultdel">Yes,
                                                        Required</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-5-custom mb-3">
                                            <div class="input-effect ">
                                                <label class="form-label">Receivables Approval<span></span></label>
                                                <div class="form-control d-flex align-items-center">
                                                    <input class="form-check-input ml-2 me-2 mt-0" type="checkbox" value="1"
                                                        id="flexCheckDefaultrec" name="receivables_approval" <?php if($edit_receivables_approval == 0): ?> <?php else: ?> checked <?php endif; ?> <?php if(Auth::user()->role_id != 1 && Auth::user()->role_id != 2): ?> <?php if($deal_track->accounts == 1): ?> disabled <?php endif; ?>
                                                        <?php endif; ?>>
                                                    <label class="form-label ml-4" for="flexCheckDefaultrec">Yes,
                                                        Required</label>
                                                </div>
                                            </div>
                                        </div>
                                        <script>
                                            $('#payment_terms2').change();
                                            // $(document).ready(function () {
                                            //     $('#purchease_required2').change(function () {
                                            //         if (this.checked) {
                                            //             $('#purchease_approval2').attr("checked", true);
                                            //         } else {
                                            //             $('#purchease_approval2').attr("checked", false);
                                            //         }
                                            //     });
                                            // });
                                        </script>
                                        <div class="col-5-custom mb-3">
                                            <div class="input-effect">
                                                <label class="form-label">Partial Delivery<span></span></label>
                                                <div class="form-control d-flex align-items-center">
                                                    <input class="form-check-input ml-2 me-2 mt-0" type="checkbox" value="1" id="partial2"
                                                        name="partial_delivery" <?php if($edit_partial_delivery == 1): ?> checked <?php endif; ?>>
                                                    <label class="form-label ml-4 " for="partial2">Yes, Partial
                                                        Delivery</label>
                                                </div>
                                            </div>

                                        </div>
                                        <?php if(count($quotationitems) > 0): ?>

                                       

                                        <?php if($quotationitems->contains('product_type', 3)): ?>
                                            <div class="col-5-custom mb-3">
                                            <div class="input-effect">
                                                <label class="form-label">Professional Service<span></span></label>
                                                <div class="form-control d-flex align-items-center">
                                                    <input type="hidden" name="technical" value="0" />
                                                    <input class="form-check-input ml-2 me-2 mt-0" type="checkbox" value="1" id="technical2"
                                                        name="technical" <?php if($edit_technical == 1): ?> checked <?php endif; ?>>
                                                    <label class="form-label ml-4 " for="technical2">Yes, Professional
                                                        Service</label>
                                                </div>
                                            </div>
                                            <script>
                                                $('#technical2').on('change', function (e) {
                                                    if ($('#technical2').prop('checked') == true) {
                                                        $('#technical_div2').css("display", "block");
                                                        // $('#technical_detail2').prop('required', true);
                                                    } else {
                                                        $('#technical_div2').css("display", "none");
                                                        // $('#technical_detail2').prop('required', false);
                                                        alert('Project service will be delete!!');
                                                    }
                                                });
                                            </script>
                                        </div>
                                        <?php endif; ?>
                                            
                                        <?php endif; ?>
                                        
                                        <?php if($is_amc_item > 0): ?>
                                            <div class="col-5-custom mb-3">
                                                <div class="no-gutters input-right-icon">
                                                    <div class="col">
                                                        <div class="input-effect">
                                                            <label class="form-label"><?php echo app('translator')->getFromJson('Start Date'); ?><span></span></label>
                                                            <input class="form-control" id="start_date" type="date" autocomplete="off"
                                                                 name="start_date" value="<?php echo e($start_date); ?>" >
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-5-custom mb-3">
                                                <div class="no-gutters input-right-icon">
                                                    <div class="col">
                                                        <div class="input-effect">
                                                            <label class="form-label"><?php echo app('translator')->getFromJson('End Date'); ?><span></span></label>
                                                            <input class="form-control" id="end_date" type="date" autocomplete="off"
                                                                 name="end_date" value="<?php echo e($end_date); ?>" >
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-5-custom mb-3">
                                                <div class="form-group">
                                                    <label for="">Invoicing</label>
                                                    <select class="form-control js-example-basic-single" type="text" name="amc_invoice" id="amc_invoice" >
                                                        <option value="">-Select-</option>
                                                        <option <?php if($invoicing == 'Monthly'): ?> selected <?php endif; ?> value="Monthly">Monthly
                                                        </option>
                                                        <option <?php if($invoicing == 'Quarterly'): ?> selected <?php endif; ?> value="Quarterly">
                                                            Quarterly
                                                        </option>
                                                        <option <?php if($invoicing == 'Half Yearly'): ?> selected <?php endif; ?> value="Half Yearly">Half
                                                            Yearly
                                                        </option>
                                                        <option <?php if($invoicing == 'Yearly'): ?> selected <?php endif; ?> value="Yearly">Yearly
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>

                                           
                                        <?php endif; ?>
                                       


  <div class="col-5-custom mb-3" id="technical_div2" style="display: none;">
                                            <div class="input-effect">
                                                <label class="form-label">Professional Service Note<span></span></label>
                                                <textarea class="dynamicstxt_s w-100 form-control" 
                                                    name="technical_detail" rows="1" autocomplete="off" id="technical_detail2"
                                                    placeholder="Remarks"><?php echo e($edit_technical_detail); ?></textarea>
                                            </div>
                                        </div>
                                        <?php if($edit_technical == 1): ?>
                                            <script>
                                                $('#technical_div2').css("display", "block");
                                                    
                                            </script>
                                        <?php endif; ?>
                                       


                   
                                        <div class="col-5-custom mb-3">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="input-effect">
                                                        <label class="form-label"><?php echo app('translator')->getFromJson('Remarks'); ?><span></span></label>
                                                        <input class="w-100 form-control" data-bs-toggle="modal" data-bs-target="#narrationModalremarks"
                                                            name="remarks" rows="1" autocomplete="off"
                                                            id="remarks" placeholder="Remarks" value="<?php echo e($edit_remarks); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" id="special_instruction" value="<?php echo e($edit_special_instruction); ?>">

                                       

                                       

                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <?php
                                        $dealTrackForRecallEdit = isset($deal_track) && !empty($deal_track->id)
                                            ? $deal_track
                                            : App\SysCrmDealTrack::where('deal_id', $edit->id)->orderBy('id', 'desc')->first();
                                        $canRecallDealTrackEdit = !empty($dealTrackForRecallEdit) && ((int) ($dealTrackForRecallEdit->accounts ?? 0) !== 1);
                                    ?>
                                    <input type="hidden" id="deal_id2" name="deal_id" value="<?php echo e($edit->id); ?>" />
                                    <button type="button" class="btn btn-light add-btn ms-2" value="approve" name="btnSubmit"
                                        id="btneditSubmit"><span class="ti-check"></span><i
                                            class="ico icon-outline-bookmark-opened text-success"></i> Update</button>
                                    <?php if($canRecallDealTrackEdit): ?>
                                        <button type="button" class="btn btn-light add-btn ms-2" id="btnRecallEditTrack">
                                            <span class="ti-back-left"></span><i
                                                class="ico icon-outline-close-circle text-danger"></i> Recall
                                        </button>
                                    <?php endif; ?>
                                </div>

                            
                        </div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const referenceInput = document.getElementById('remarks');
        const narrationTextarea = document.getElementById('narrationTextarearemarks');
        const insertButton = document.getElementById('insertNarrationremarks');
        const narrationModal = document.getElementById('narrationModalremarks');

        // Pre-fill textarea when modal opens
        narrationModal.addEventListener('shown.bs.modal', () => {
            narrationTextarea.value = referenceInput.value;
            setTimeout(() => $('#narrationTextarearemarks').focus(), 500);

        });


        // On insert button click, update input and close modal
        insertButton.addEventListener('click', () => {
            referenceInput.value = narrationTextarea.value;
            bootstrap.Modal.getInstance(narrationModal).hide();
        });
    });
</script>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const referenceInput1 = document.getElementById('remarks1');
        const narrationTextarea1 = document.getElementById('narrationTextarearemarks1');
        const insertButton1 = document.getElementById('insertNarrationremarks1');
        const narrationModal1 = document.getElementById('narrationModalremarks1');

        // Pre-fill textarea when modal opens
        narrationModal1.addEventListener('shown.bs.modal', () => {
            narrationTextarea1.value = referenceInput1.value;
            setTimeout(() => $('#narrationTextarearemarks1').focus(), 500);

        });

        // On insert button click, update input and close modal
        insertButton1.addEventListener('click', () => {
            referenceInput1.value = narrationTextarea1.value;
            bootstrap.Modal.getInstance(narrationModal1).hide();
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('special_instruction1');
        const textarea = document.getElementById('narrationTextareaSpecialInstruction1');
        const saveButton = document.getElementById('insertSpecialInstruction1');
        const modalEl = document.getElementById('narrationModalSpecialInstruction1');

        if (input && textarea && saveButton && modalEl) {
            modalEl.addEventListener('shown.bs.modal', () => {
                textarea.value = input.value || '';
                setTimeout(() => $('#narrationTextareaSpecialInstruction1').focus(), 300);
            });

            saveButton.addEventListener('click', () => {
                input.value = textarea.value || '';
                bootstrap.Modal.getInstance(modalEl).hide();
            });
        }
    });
</script>

<div class="modal side-panel fade" id="narrationModalremarks1" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="poexcelimport">Enter Remarks</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body">
                        <textarea style="height: 109px !important;" class="form-control capitalize-title" id="narrationTextarearemarks1" rows="6"
                            placeholder="Write remarks here..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="insertNarrationremarks1" class="btn btn-light add-btn ms-2">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal side-panel fade" id="narrationModalSpecialInstruction1" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Special Instructions</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body">
                        <textarea style="height: 109px !important;" class="form-control capitalize-title" id="narrationTextareaSpecialInstruction1" rows="6"
                            placeholder="Write special instructions here..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="insertSpecialInstruction1" class="btn btn-light add-btn ms-2">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>


<div class="modal side-panel fade" id="narrationModalremarks" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="poexcelimport">Enter Remarks</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body">
                        <textarea style="height: 109px !important;" class="form-control capitalize-title" id="narrationTextarearemarks" rows="6"
                            placeholder="Write remarks here..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="insertNarrationremarks" class="btn btn-light add-btn ms-2">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal side-panel fade" id="narrationModalSpecialInstruction" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Special Instructions</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body">
                        <textarea style="height: 109px !important;" class="form-control capitalize-title" id="narrationTextareaSpecialInstruction" rows="6"
                            placeholder="Write special instructions here..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="insertSpecialInstruction" class="btn btn-light add-btn ms-2">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('special_instruction');
        const textarea = document.getElementById('narrationTextareaSpecialInstruction');
        const saveButton = document.getElementById('insertSpecialInstruction');
        const modalEl = document.getElementById('narrationModalSpecialInstruction');

        if (input && textarea && saveButton && modalEl) {
            modalEl.addEventListener('shown.bs.modal', () => {
                textarea.value = input.value || '';
                setTimeout(() => $('#narrationTextareaSpecialInstruction').focus(), 300);
            });

            saveButton.addEventListener('click', () => {
                input.value = textarea.value || '';
                bootstrap.Modal.getInstance(modalEl).hide();
            });
        }
    });
</script>

<script>
    $(function () {
        // Initialize popovers with jQuery API where available.
        if (typeof $.fn.popover === 'function') {
            $('.special-instruction-trigger').popover();
        } else if (typeof window.bootstrap !== 'undefined' && bootstrap.Popover) {
            $('.special-instruction-trigger').each(function () {
                if (!bootstrap.Popover.getInstance(this)) {
                    new bootstrap.Popover(this);
                }
            });
        }

        $(document).off('click.specialInstructionModal').on('click.specialInstructionModal', '.special-instruction-trigger', function (e) {
            e.preventDefault();
            var target = $(this).data('special-modal');
            if (!target) {
                return;
            }

            // Prefer jQuery modal API.
            if (typeof $.fn.modal === 'function') {
                $(target).modal('show');
                return;
            }

            // Bootstrap 5 fallback.
            if (typeof window.bootstrap !== 'undefined' && bootstrap.Modal) {
                var modalEl = document.querySelector(target);
                if (!modalEl) {
                    return;
                }
                var modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                modalInstance.show();
            }
        });
    });
</script>


                        <script>
                            $(document).ready(function () {
                                let isDealTrackEditSubmitting = false;
                                let isDealTrackRecallSubmitting = false;

                                // Core submission function
                                function submitEditDealTrack() {
                                    if (isDealTrackEditSubmitting) {
                                        return;
                                    }

                                    var formData = new FormData();

                                    formData.append('delivery_date', $('#delivery_date2').val());
                                    formData.append('payment_terms', $('#payment_terms2').val());
                                    formData.append('payment_terms_txt', $('#payment_terms2_txt').val());
                                    formData.append('payment_mode', $('#payment_mode2').val());
                                    formData.append('payment_mode_sec', $('#payment_mode_sec').val() || '');
                                    // formData.append('purchease_required', $('#purchease_required2').is(':checked') ? 1 : 0);
                                    formData.append('partial_delivery', $('#partial2').is(':checked') ? 1 : 0);
                                    // formData.append('technical', $('#technical2').is(':checked') ? 1 : 0);

                                    let technical;

                                        if ($('#technical2').length) {
                                            // element exists
                                            technical = $('#technical2').is(':checked') ? 1 : 0;
                                        } else {
                                            // element does not exist
                                            technical = 0;
                                        }

                                        formData.append('technical', technical);


                                    formData.append('start_date', $('#start_date').val() || '');
                                    formData.append('end_date', $('#end_date').val() || '');
                                    formData.append('amc_invoice', $('#amc_invoice').val());
                                    formData.append('technical_detail', $('#technical_detail2').val());
                                    formData.append('purchease_approval', $('#purchease_approval2').is(':checked') ? 1 : 0);
                                    formData.append('invoice_approval', $('#flexCheckDefaultinvoice').is(':checked') ? 1 : 0);
                                    formData.append('delivery_approval', $('#flexCheckDefaultdel').is(':checked') ? 1 : 0);
                                    formData.append('receivables_approval', $('#flexCheckDefaultrec').is(':checked') ? 1 : 0);

                                    let referenceNo = $('#reference_no').val().trim();
                                    // If empty → show alert and stop request
                                    if (!referenceNo) {
                                        alert("Reference number is required!");
                                        return; // stop AJAX request
                                    }

                                    formData.append('reference_no', $('#reference_no').val());
                                    formData.append('reference_date', $('#reference_date').val() || '');
                                    formData.append('remarks', $('#remarks').val());
                                    formData.append('special_instruction', $('#special_instruction').val());
                                    formData.append('deal_id', $('#deal_id2').val());
                                    formData.append('quote_id', $('input[name="quote_id"]').first().val() || '');

                                    // Attach multiple files
                                    var lpo2 = $('#lpo2')[0];
                                    if (lpo2 && lpo2.files) {
                                        $.each(lpo2.files, function (i, file) { formData.append('lpo[]', file); });
                                    }
                                    var ch2 = $('#cheque2')[0];
                                    if (ch2 && ch2.files) {
                                        $.each(ch2.files, function (i, file) { formData.append('cheque_copy[]', file); });
                                    }
                                    var pq2 = $('#poquote2')[0];
                                    if (pq2 && pq2.files) {
                                        $.each(pq2.files, function (i, file) { formData.append('purchease_quote[]', file); });
                                    }

                                    isDealTrackEditSubmitting = true;

                                    // AJAX submission
                                    $.ajax({
                                        url: "<?php echo e(url('crm-deal-track-submit-edit')); ?>",
                                        type: 'POST',
                                        data: formData,
                                        contentType: false,
                                        processData: false,
                                        headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' },
                                        beforeSend: function () {
                                            $("#loading_bg").show();
                                            $('#btneditSubmit').prop('disabled', true);
                                        },
                                        success: function (response) {
                                            $("#loading_bg").hide();
                                              
                                            toastr.success("Deal Track Updated successfully", "Success");
                                            // location.reload();
                                            loadDealTrackDetails(response.id);
                                            return;
                                        },
                                        error: function (xhr) {
                                            $("#loading_bg").hide();
                                            let msg = 'An error occurred';
                                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                                msg = Object.values(xhr.responseJSON.errors).map(e => e[0]).join("\n");
                                            }
                                            alert(msg);
                                        },
                                        complete: function () {
                                            isDealTrackEditSubmitting = false;
                                            $('#btneditSubmit').prop('disabled', false);
                                        }
                                    });
                                }

                                // Bind buttons
                                $('#btneditSubmit').on('click', function (e) {
                                    e.preventDefault();
                                    submitEditDealTrack();
                                });

                                // Recall in Edit Deal Track section
                                $(document).on('click', '#btnRecallEditTrack', function (e) {
                                    e.preventDefault();

                                    if (isDealTrackRecallSubmitting) {
                                        return;
                                    }

                                    if (!confirm('Are you sure you want to recall this deal track?')) {
                                        return;
                                    }

                                    isDealTrackRecallSubmitting = true;

                                    $.ajax({
                                        url: "<?php echo e(url('crm-deal-track-recall')); ?>",
                                        type: 'POST',
                                        data: {
                                            deal_id: $('#deal_id2').val() || $('#deal_id1').val()
                                        },
                                        headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' },
                                        beforeSend: function () {
                                            $("#loading_bg").show();
                                            $('#btneditSubmit, #btnRecallEditTrack').prop('disabled', true);
                                        },
                                        success: function () {
                                            $("#loading_bg").hide();
                                            toastr.success("Deal Track Recalled Successfully", "Success");
                                            location.reload();
                                        },
                                        error: function (xhr) {
                                            $("#loading_bg").hide();
                                            let msg = 'Unable to recall deal track.';
                                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                                msg = xhr.responseJSON.message;
                                            }
                                            toastr.error(msg, "Failed");
                                        },
                                        complete: function () {
                                            isDealTrackRecallSubmitting = false;
                                            $('#btneditSubmit, #btnRecallEditTrack').prop('disabled', false);
                                        }
                                    });
                                });
                            });
                        </script>


                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>

                    </div>





        <div class="tab-pane fade" id="internal-fields" role="tabpanel" aria-labelledby="internal-fields-tab">


            <div class="row">

                <div class="col-7">
            <div id="scrollBox"  style="max-height: 12rem; overflow-y: auto;">

                    <?php
                        $internalRemarks = trim((string) ($edit_remarks ?? $remarks ?? ''));
                        $internalSpecialInstructions = trim((string) ($edit_special_instruction ?? $special_instruction ?? ''));
                    ?>
                    <?php if($internalRemarks !== ''): ?>
                        <b>Remarks :- </b>
                        <div class="card mb-2">
                            <div class="card-body">
                                <div class="fw-semibold" style="font-size:11px"><?php echo nl2br(e($internalRemarks)); ?></div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if($internalSpecialInstructions !== ''): ?>
                        <b>Special Instructions :- </b>
                        <div class="card mb-2">
                            <div class="card-body">
                                <div class="fw-semibold" style="font-size:11px"><?php echo nl2br(e($internalSpecialInstructions)); ?></div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if($edit->note != ''): ?><b>Deal Notes :- </b>

                        <div class="card">
                            <div class="card-body">
                                <div class="fw-semibold" style="font-size:11px"> <?php echo nl2br($edit->note); ?> </div>

                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if(count($comments) > 0): ?>
                        <div class="mt-2" style="">
                            <?php $__currentLoopData = $comments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cmts): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                     <div class="card  rounded-3 mb-2 comments-card">
                                                        <div class="card-body py-0">

                                                        

                                                            <!-- Top Row: Right-Aligned Icons -->
                                                            <div class="d-flex justify-content-between mb-0">


                                                                <!-- Comment -->
                                                                        <p class="mb-0 text-break fw-semibold <?php if($cmts->deleted_at): ?> text-decoration-line-through text-muted <?php endif; ?>" style="font-size:11px">
                                                                                <?php echo nl2br($cmts->comments); ?>

                                                                        </p>


                                                                        <div class="d-flex align-items-baseline gap-2">
                                                                                <?php if($cmts->commentsdoc): ?>
                                                                                                <a href="<?php echo e(asset('public/uploads/crm_deal_doc/' . $cmts->commentsdoc)); ?>"
                                                                                                target="_blank" class="btn btn-sm btn-light me-1"  style="min-height:17px">
                                                                                                    <i class="ico icon-bold-paperclip" style="font-size:11px"></i>
                                                                                                </a>
                                                                                            <?php endif; ?>

                                                                                            <?php if($cmts->created_by == Auth::user()->id): ?>
                                                                                                <?php if($cmts->deleted_at): ?>
                                                                                                    <a href="<?php echo e(url('crm-deals-comments-restore/' . $cmts->id)); ?>"
                                                                                                    onclick="return confirm('Are you sure you want to restore this comment?')"
                                                                                                    class="btn btn-sm btn-light"  style="min-height:17px">
                                                                                                        <i class="ico icon-bold-restart" style="font-size:11px"></i>
                                                                                                    </a>
                                                                                                <?php else: ?>
                                                                                                    <a href="<?php echo e(url('crm-deals-comments-delete/' . $cmts->id)); ?>"
                                                                                                    onclick="return confirm('Are you sure you want to delete this comment?')"
                                                                                                    class="btn btn-sm btn-light"  style="min-height:17px">
                                                                                                        <i class="ico icon-outline-trash-bin-minimalistic" style="font-size:11px"></i>
                                                                                                    </a>
                                                                                                <?php endif; ?>
                                                                                    <?php endif; ?>
                                                                        </div>


                                                            

                                                            </div>

                                                            <!-- Username + Date + Deleted At (Right-Aligned Below Icons) -->
                                                            <div class="text-end small text-muted">

                                                                <span style="font-size:10px">
                                                                   
                                                                    <?php echo e($cmts->createdby->first_name); ?> <?php echo e($cmts->createdby->last_name); ?>

                                                                </span>

                                                                <span>•</span>

                                                                <span style="font-size:10px">
                                                                    <i class="ico icon-bold-clock me-1"></i>
                                                                    <?php echo e(date('d/m/Y h:i A', strtotime($cmts->created_at))); ?>

                                                                </span>

                                                                <?php if($cmts->deleted_at): ?>
                                                                <span>•</span>
                                                                    
                                                                    <span class="text-danger" style="font-size:10px">
                                                                        Deleted: <?php echo e(date('d/m/Y h:i A', strtotime($cmts->deleted_at))); ?>

                                                                    </span>
                                                                <?php endif; ?>

                                                            </div>

                                                        </div>
                                                </div>

                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>
                </div>
                </div>

                <div class="col-5">
                        <div class="d-flex justify-content-between align-items-center mb-2 w-100">
    
                            <label class="font-weight-bold form-label mb-0 w-50">Internal Note</label>

                            <button type="button" id="viewbnotemodal" data-bs-toggle="modal" data-bs-target="#ViewNotesModal"
                                class="btn btn-light btn-sm d-inline-flex align-items-center gap-1 px-2 py-1" style="font-size:11px">
                                <i class="ico icon-outline-notebook text-success" style="font-size:15px"></i>
                                <span>View Notes</span>
                            </button>

                            <!-- Modal Account-->
                            <div class="modal side-panel fade" id="ViewNotesModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header"><h4 class="modal-title" id="exampleModalLongTitle">Internal Note</h4>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body">

                                            <div class="row">
                                                    <div id="scrollBox"  style="max-height: 420px; overflow-y: auto;">


                                    <?php
                                        $internalRemarks = trim((string) ($edit_remarks ?? $remarks ?? ''));
                                        $internalSpecialInstructions = trim((string) ($edit_special_instruction ?? $special_instruction ?? ''));
                                    ?>
                                    <?php if($internalRemarks !== ''): ?>
                                        <div class="card mb-2">
                                            <div class="card-body">
                                                <div class="fw-semibold" style="font-size:12px">
                                                    <b>Remarks :- </b><?php echo nl2br(e($internalRemarks)); ?>

                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if($internalSpecialInstructions !== ''): ?>
                                        <div class="card mb-2">
                                            <div class="card-body">
                                                <div class="fw-semibold" style="font-size:12px">
                                                    <b>Special Instructions :- </b><?php echo nl2br(e($internalSpecialInstructions)); ?>

                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if($edit->note != ''): ?>

                        <div class="card">
                            <div class="card-body">
                                <div class="fw-semibold" style="font-size:12px"> <?php echo nl2br($edit->note); ?> </div>

                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if(count($comments) > 0): ?>
                        <div class="mt-2" style="">
                            <?php $__currentLoopData = $comments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cmts): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                     <div class="card  rounded-3 mb-2 comments-card">
                                                        <div class="card-body py-0">

                                                        

                                                            <!-- Top Row: Right-Aligned Icons -->
                                                            <div class="d-flex justify-content-between mb-0">


                                                                <!-- Comment -->
                                                                        <p class="mb-0 text-break fw-semibold <?php if($cmts->deleted_at): ?> text-decoration-line-through text-muted <?php endif; ?>" style="font-size:12px">
                                                                                <?php echo nl2br($cmts->comments); ?>

                                                                        </p>


                                                                        <div class="d-flex align-items-baseline gap-2">
                                                                                <?php if($cmts->commentsdoc): ?>
                                                                                                <a href="<?php echo e(asset('public/uploads/crm_deal_doc/' . $cmts->commentsdoc)); ?>"
                                                                                                target="_blank" class="btn btn-sm btn-light me-1"  style="min-height:17px">
                                                                                                    <i class="ico icon-bold-paperclip" style="font-size:12px"></i>
                                                                                                </a>
                                                                                            <?php endif; ?>

                                                                                            <?php if($cmts->created_by == Auth::user()->id): ?>
                                                                                                <?php if($cmts->deleted_at): ?>
                                                                                                    <a href="<?php echo e(url('crm-deals-comments-restore/' . $cmts->id)); ?>"
                                                                                                    onclick="return confirm('Are you sure you want to restore this comment?')"
                                                                                                    class="btn btn-sm btn-light"  style="min-height:17px">
                                                                                                        <i class="ico icon-bold-restart" style="font-size:12px"></i>
                                                                                                    </a>
                                                                                                <?php else: ?>
                                                                                                    <a href="<?php echo e(url('crm-deals-comments-delete/' . $cmts->id)); ?>"
                                                                                                    onclick="return confirm('Are you sure you want to delete this comment?')"
                                                                                                    class="btn btn-sm btn-light"  style="min-height:17px">
                                                                                                        <i class="ico icon-outline-trash-bin-minimalistic" style="font-size:12px"></i>
                                                                                                    </a>
                                                                                                <?php endif; ?>
                                                                                    <?php endif; ?>
                                                                        </div>


                                                            

                                                            </div>

                                                            <!-- Username + Date + Deleted At (Right-Aligned Below Icons) -->
                                                            <div class="text-end small text-muted">

                                                                <span style="font-size:11px">
                                                                   
                                                                    <?php echo e($cmts->createdby->first_name); ?> <?php echo e($cmts->createdby->last_name); ?>

                                                                </span>

                                                                <span>•</span>

                                                                <span style="font-size:11px">
                                                                    <i class="ico icon-bold-clock me-1"></i>
                                                                    <?php echo e(date('d/m/Y h:i A', strtotime($cmts->created_at))); ?>

                                                                </span>

                                                                <?php if($cmts->deleted_at): ?>
                                                                <span>•</span>
                                                                    
                                                                    <span class="text-danger" style="font-size:11px">
                                                                        Deleted: <?php echo e(date('d/m/Y h:i A', strtotime($cmts->deleted_at))); ?>

                                                                    </span>
                                                                <?php endif; ?>

                                                            </div>

                                                        </div>
                                                </div>

                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>



                                </div>
                                            </div>

                                        </div>
                                    
                                    </div>
                                </div>
                                </div>
                            <!-- Modal Account-->

                        </div>
                    <div id="deal-comments-form">
                        <textarea name="comments" class="form-control capitalize-title" cols="10" rows="3"></textarea>
                        
                        <input type="hidden" name="commentsid" value="<?php echo e($edit->id); ?>" />

                          <div class="row mt-2">
                                                        <div class="col-md-4 d-flex justify-content-start align-items-center">
                                                        <button type="button" id="submitComment"
                                                            class="btn btn-light d-inline-flex align-items-center gap-2">
                                                            <i class="ico icon-outline-add-square fs-5 text-success"></i>
                                                            <span>Add Note</span>
                                                        </button>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="file" class="form-control" name="commentsdoc" id="commentsdoc">
                                                    </div>

                                                    
                                                </div>
                    </div>

                </div>

            </div>


            <script>
                $(document).on('click', '#submitComment', function (e) {
                    e.preventDefault();

                    let formData = new FormData();
                    formData.append('comments', $('textarea[name="comments"]').val());
                    formData.append('commentsid', $('input[name="commentsid"]').val());

                    let fileInput = $('#commentsdoc')[0];
                    if (fileInput.files.length > 0) {
                        formData.append('commentsdoc', fileInput.files[0]);
                    }


                    $.ajax({
                        url: '<?php echo e(url('crm-deals-comments-add')); ?>',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                        },
                        beforeSend: function () {
                           $("#loading_bg").css("display", "block");
                        },
                        success: function (response) {
                            console.log("response", response); // Debugging line to check response
                           
                           
                            $('textarea[name="comments"]').val('');
                            $('#commentsdoc').val('');
                            $("#loading_bg").css("display", "none");
                            location.reload();
                            // Optionally append new comment to comment list
                        },
                        error: function (xhr) {
                            $("#loading_bg").css("display", "none");
                            alert('Something went wrong: ' + xhr.responseText);
                        }
                    });
                });
            </script>


        </div>

    </div>
</div>



<div class="deal-list-content-header">
    <table width="100%">
        <tbody>

            <tr>
                <td class="text-end float-end">
                   
                </td>
            </tr>
        </tbody>
    </table>
    <script>
        function quote_generate() {
            var x = document.getElementById("generate-quotation");
            if (x.style.height === "0px") {
                x.style.height = "auto";
                document.getElementById("quotation_generated").value = "1";
            } else {
                x.style.height = "0px";
                document.getElementById("quotation_generated").value = "0";
            }
        }
    </script>
    
    
    <div id="generate-quotation"
        style="height: <?php echo e((count($quotationitems) > 0 || count($cart) > 0) ||  request()->query('new') == 'yes' ? 'auto' : '0px'); ?>; overflow: hidden; transition: all 0.5s ease;">

        <div class="tab-wrap mb-3">
            <ul class="nav nav-tabs" id="purchaseDetailsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="extra-fields-tab" data-bs-toggle="tab"
                        data-bs-target="#extra-fields" type="button" role="tab" aria-controls="extra-fields"
                        aria-selected="true">Quotation</button>
                </li>
            </ul>
            <div class="tab-content mb-3" id="purchaseDetailsTabsContent">
                <div class="tab-pane fade show active" id="extra-fields" role="tabpanel"
                    aria-labelledby="extra-fields-tab">
                     
                    <div class="row gap-rows">
                        <div class="col-2">
                            <label class="form-label">Quote Validity:</label>
                            <div class="form-group">
                                <input class="form-control" id="quote_validity" type="text" autocomplete="off"
                                    placeholder="Quote Validity" name="quote_validity" value="2 Weeks" required>
                            </div>
                        </div>
                        <div class="col-2" style="margin-top:-5px">
                            <label class="form-label mb-0 d-flex justify-content-between align-items-center">
                                <span>Payment Terms</span>
                                <button type="button" class="btn btn-sm p-0 ms-2" style="border:none;background:none;" data-bs-toggle="modal" data-bs-target="#paymenttermsModal">
                                    <i class="ico icon-outline-add-square text-success" style="font-size:18px;"></i>
                                </button>
                            </label>
                            <div class="form-group">
                                <select class="form-control js-example-basic-single" name="payment_terms"
                                    id="payment_terms" required>
                                    <option value="">-Select-</option>
                                    <?php $__currentLoopData = $paymentterms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e(@$value->id); ?>" <?php if(count($quotationitems) > 0): ?> <?php if($quotationitems[0]->payment_terms == $value->id): ?> selected <?php endif; ?> <?php elseif(@$edit->customername->payment_terms == $value->id): ?> selected <?php endif; ?>>
                                            <?php echo e(@$value->title); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                </select>
                                <input class="form-control" id="payment_terms_txt" type="text" value=""
                                    autocomplete="off" placeholder="Payment Terms" name="payment_terms_txt"
                                    style="display: none;">
                                <script>
                                    $(document).ready(function () {
                                        $('#payment_terms').on('change', function () {
                                            if ($(this).val() == 22) {
                                                $('#payment_terms_txt').show().prop('required', true);
                                            } else {
                                                $('#payment_terms_txt').hide().prop('required', false);
                                            }
                                        });

                                        // Trigger once on load (in case the field already has value 22)
                                        $('#payment_terms').trigger('change');
                                    });
                                </script>
                            </div>
                        </div>
                        <div class="col-2">
                            <label class="form-label">Delivery Time:</label>
                            <div class="form-group">
                                <input class="form-control" id="delivery_time" type="text" autocomplete="off"
                                    placeholder="Delivery Time" name="delivery_time" value="2 Weeks" required>
                            </div>
                        </div>

                  
                        
                        <div class="col-2">
                            <label class="form-label">Currency:<a style="float: right;"
                                    data-bs-target="#ModalChangeCurrancy" data-bs-toggle="modal"><i
                                        class="ico icon-outline-pen-2"></i></a></label>
                            <div class="form-group">
                                <select class="form-control js-example-basic-single" name="currency_id" id="currency_id" required>
                                    <option value="">-Select-</option>
                                    <?php $__currentLoopData = $currency; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e(@$value->id); ?>" <?php if(@$edit->deal_currency == $value->id): ?> selected
                                        <?php endif; ?>>
                                            <?php echo e(@$value->code); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>

                        <script>
                            $(document).ready(function() {
                                $('#currency_id').on('change', function() {
                                    var selectedCode = $('#currency_id option:selected').text();
                                    $('#deal_value_currency').text(selectedCode);
                                    $('#deal_profit_currency').text(selectedCode);
                                });
                            });
                        </script>

                        <div class="col-3">
                            <label class="form-label">Terms and Condition:</label>
                            <div class="form-group">
                                <textarea class="form-control" rows="3" data-bs-toggle="modal"
                                    data-bs-target="#narrationModal" id="terms_and_condition" autocomplete="off"
                                    name="terms_and_condition"><?php echo e(@$edit->terms_and_condition ?? '1. Quote/Order will be subject to approval of payment/credit terms by our finance.
2. Please mention our Quotation No. in your Purchase Order
3. In case of non-availability of quote products SYSCOM reserves the right to supply a functionally similar or better product.'); ?></textarea>
                            </div>
                            
                        </div>

                        <div class="col-1 mt-4">
  <button type="button" class="btn btn-sm btn-light" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#ModalExcelQuote">
                                        <i class="ico icon-outline-import text-success" style="font-size: 16px"></i> Import
                                    </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>




        <div class="table-container mb-3" style="border: solid 1px #d9d9d9;">
            <table class="table table-hover form-item-table" id="myTable">
                <thead>
                    <tr>
                        <th class="resizable text-center" width="50px"><?php echo app('translator')->getFromJson('No'); ?>
                            <div class="resizer"></div>
                        </th>
                        <th class="resizable text-center" width="250px"><?php echo app('translator')->getFromJson('Part No'); ?> <a
                                class="icon icon-outline-book text-dark" data-bs-toggle="modal"
                                data-bs-target="#addproductModal"></a>
                            <div class="resizer"></div>
                        </th>
                        <th class="resizable text-center" width="250px"><?php echo app('translator')->getFromJson('Description'); ?>
                            <div class="resizer"></div>
                        </th>
                        <th class="resizable text-center" width="65px"><?php echo app('translator')->getFromJson('Cost'); ?>
                            <div class="resizer"></div>
                        </th>
                        <th class="resizable text-center" width="65px"><?php echo app('translator')->getFromJson('Tax'); ?>
                            <div class="resizer"></div>
                        </th>
                        <th class="resizable text-center" width="40px"><?php echo app('translator')->getFromJson('Qty'); ?>
                            <div class="resizer"></div>
                        </th>
                        <th class="resizable text-center" width="100px"><?php echo app('translator')->getFromJson('Price'); ?>
                            <div class="resizer"></div>
                        </th>
                        <th class="resizable text-center" width="100px"><?php echo app('translator')->getFromJson('Value'); ?>
                            <div class="resizer"></div>
                        </th>
                        <th class="resizable text-center" width="80px" scope="col">Dis <a
                                class="icon icon-outline-book text-dark" data-bs-toggle="modal"
                                data-bs-target="#discountModal"></a>
                            <div class="resizer"></div>
                        </th>
                        <th class="resizable text-center" width="100px"><?php echo app('translator')->getFromJson('Taxable'); ?>
                            <div class="resizer"></div>
                        </th>
                        <th class="resizable text-center" width="100px"><?php echo app('translator')->getFromJson('VAT'); ?>
                            <div class="resizer"></div>
                        </th>
                        <th class="resizable text-center" width="100px"><?php echo app('translator')->getFromJson('Total'); ?>
                            <div class="resizer"></div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                      $i = 1;
                    ?>

                    <?php if(count($quotationitems) > 0): ?>
                        <?php $__currentLoopData = $quotationitems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        
                            <tr>
                                <td><input type="text" class="form-control text-center" name="sort_id[]"
                                        value="<?php echo e($i++); ?>" /></td>
                                <td class="noborder">
                                    <select class="form-control noborder " name="part_number[]">
                                 
                                        <option value="<?php echo e($item->product_id); ?>">
                                            <?php echo e($item->productname->part_number); ?>

                                        </option>
                                    </select>
                                </td>
                                <td><textarea class="form-control" name="description[]" rows="1"><?php echo e($item->description); ?></textarea></td>
                                <td>
                                    <input class="form-control text-end" type="text" name="cost[]" autocomplete="off"
                                        value="<?php echo e(number_format((float) $item->cost, 2, '.', '')); ?>"
 onchange="calc_change_new(this)" onblur="formatCurrency(this)">
                                    <input class="form-control" type="text" name="part_number_txt[]" autocomplete="off"
                                        readonly="true" hidden>
                                    <input class="form-control" type="text" name="hscode_txt[]" autocomplete="off"
                                        readonly="true" hidden>
                                    <input class="form-control" type="text" name="product_type[]" value="<?php echo e($item->product_type); ?>" autocomplete="off"
                                        readonly="true" hidden>
                                    <input class="form-control" type="text" name="product_type_part_number_text[]"
                                        autocomplete="off" readonly="true" hidden>
                                </td>
                                <td><input type="number" class="form-control text-center" name="tax[]"
                                        onchange="calc_change_new(this)" value="<?php echo e($item->vat); ?>"></td>
                                <td><input class="form-control text-center" type="number" id="qty_<?php echo e($item->id); ?>" name="qty[]"
                                        autocomplete="off" min="0" onchange="calc_change_new(this)" value="<?php echo e($item->qty); ?>">
                                </td>
                                <td><input class="form-control text-end" type="text" name="unitprice[]" step="any"
                                        autocomplete="off" min="0" onchange="calc_change_new(this)" onblur="formatCurrency(this)" value="<?php echo e(@App\SysHelper::com_curr_format($item->price,2,'.',',')); ?>">
                                </td>
                                <td><input class="form-control text-end" type="text" name="value[]" autocomplete="off" min="0"
                                        readonly></td>
                                <td><input class="form-control text-end" type="text" step="Any" name="discount[]"
                                        autocomplete="off" min="0" onchange="calc_change_new(this)" onblur="formatCurrency(this)"
                                        value="<?php echo e(@App\SysHelper::com_curr_format($item->discount,2,'.',',')); ?>"></td>
                                <td><input class="form-control text-end" type="text" name="taxableamount[]" autocomplete="off"
                                        min="0" readonly></td>
                                <td><input class="form-control text-end" type="text" name="vatamount[]" autocomplete="off"
                                        min="0" readonly></td>
                                <td><input class="form-control text-end" type="text" name="totalamount[]" autocomplete="off"
                                        min="0" readonly></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <script>
                            document.addEventListener("DOMContentLoaded", function () {
                                // trigger change on all qty fields once
                                document.querySelectorAll('input[name="qty[]"]').forEach(function (el) {
                                    el.dispatchEvent(new Event("change"));
                                });
                            });
                        </script>
                    <?php endif; ?>

                  

                    <?php if(isset($cart) && count($cart) > 0): ?>

                    <?php $__currentLoopData = $cart; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cart_items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                   

                     <tr>
                                <td><input type="text" class="form-control text-center" name="sort_id[]"
                                        value="<?php echo e($i++); ?>" /></td>
                                <td class="noborder">
                                    <select class="form-control noborder " name="part_number[]">
                                        <option value=""></option>
                                        <option value="<?php echo e($cart_items->product_id); ?>">
                                            <?php echo e($cart_items->partnumber); ?>

                                        </option>
                                    </select>
                                </td>
                                <td><textarea class="form-control" name="description[]" rows="1"><?php echo e($cart_items->description); ?></textarea></td>
                                <td>
                                    <input class="form-control text-end" type="text" name="cost[]" autocomplete="off"
                                        value="<?php echo e($cart_items->cost); ?>" onchange="calc_change_new(this)" onblur="formatCurrency(this)">
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
                                        onchange="calc_change_new(this)" value="<?php echo e($cart_items->vat); ?>"></td>
                                <td><input class="form-control text-center" type="number" id="qty_<?php echo e($cart_items->id); ?>" name="qty[]"
                                        autocomplete="off" min="0" onchange="calc_change_new(this)" value="<?php echo e($cart_items->qty); ?>">
                                </td>
                                <td><input class="form-control text-end" type="text" name="unitprice[]" step="any"
                                        autocomplete="off" min="0" onchange="calc_change_new(this)" onblur="formatCurrency(this)" value="<?php echo e(@App\SysHelper::com_curr_format($cart_items->price,2,'.',',')); ?>">
                                </td>
                                <td><input class="form-control text-end" type="text" name="value[]" autocomplete="off" min="0"
                                        readonly></td>
                                <td><input class="form-control text-end" type="text" step="Any" name="discount[]"
                                        autocomplete="off" min="0" onchange="calc_change_new(this)" onblur="formatCurrency(this)"
                                        value="<?php echo e(@App\SysHelper::com_curr_format($cart_items->discount,2,'.',',')); ?>"></td>
                                <td><input class="form-control text-end" type="text" name="taxableamount[]" autocomplete="off"
                                        min="0" readonly></td>
                                <td><input class="form-control text-end" type="text" name="vatamount[]" autocomplete="off"
                                        min="0" readonly></td>
                                <td><input class="form-control text-end" type="text" name="totalamount[]" autocomplete="off"
                                        min="0" readonly></td>
                            </tr>
                    
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                
                    <script>
                            document.addEventListener("DOMContentLoaded", function () {
                                // trigger change on all qty fields once
                                document.querySelectorAll('input[name="qty[]"]').forEach(function (el) {
                                    el.dispatchEvent(new Event("change"));
                                });
                            });
                        </script>
                     <?php endif; ?>


                    <tr>
                        <td><input type="text" class="form-control text-center" name="sort_id[]"
                                value="<?php echo e($i); ?>" /></td>
                            
                        <td class="noborder">
                            <select class="form-control noborder " name="part_number[]">
                                <option value="" selected></option>
                            </select>
                            
                        </td>
                        <td><textarea class="form-control" name="description[]" rows="1"></textarea></td>
                        <td>
                            <input class="form-control text-end" type="text" name="cost[]" autocomplete="off" onchange="calc_change_new(this)" onblur="formatCurrency(this)">
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
                        <td><input class="form-control text-center" type="number" name="qty[]" autocomplete="off"
                                min="0" onchange="calc_change_new(this)"></td>
                        <td><input class="form-control text-end" type="text" name="unitprice[]" step="any"
                                autocomplete="off" min="0" onchange="calc_change_new(this)" onblur="formatCurrency(this)"></td>
                        <td><input class="form-control text-end" type="text" name="value[]"  min="0"  autocomplete="off"
                                readonly></td>
                        <td><input class="form-control text-end" type="text" step="Any" name="discount[]"
                                autocomplete="off" min="0" onchange="calc_change_new(this)" onblur="formatCurrency(this)"></td>
                        <td><input class="form-control text-end" type="text" name="taxableamount[]" autocomplete="off"
                                min="0" readonly></td>
                        <td><input class="form-control text-end" type="text" name="vatamount[]" autocomplete="off"
                                min="0" readonly></td>
                        <td><input class="form-control text-end" type="text" name="totalamount[]" autocomplete="off"
                                min="0" readonly></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" scope="col">Total</th>
                        <th class="text-end"><label id="lbl_total_cost" >0</label></th>
                        <th class="text-center"></th>
                        <th class="text-center"><label id="lbl_total_qty">0</label></th>
                        <th class="text-end" scope="col"><label id="lbl_total_price">0</label></th>
                        <th class="text-end" scope="col"><label id="lbl_total_value">0</label></th>
                        <th class="text-end" scope="col"><label id="lbl_total_discount">0</label></th>
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
      
          <table class="table form-item-table mb-3">
    <tr class="align-middle text-center"> <!-- centers vertically + horizontally -->
        <td class="text-end"><b>Additional Discount :</b></td>
        <td class="text-end" style="width: 50px;">
                                    <input type="number" class="form-control text-end" id="deal_discount_vat" name="deal_discount_vat" value="<?php echo e($edit->deal_discount_vat ?? ''); ?>"  step="any" placeholder="VAT %" />
                                </td>
        <td style="width: 103px;">
            <input type="text" class="form-control text-center"
                id="deal_discount" name="deal_discount" step="any"
                placeholder="0.00"
                value="<?php if(!empty($edit->deal_discount) && $edit->deal_discount > 0 && (count($quotationitems) > 0)): ?><?php echo e(@App\SysHelper::com_curr_format($edit->deal_discount,2,'.','')); ?><?php endif; ?>"
            />
        </td>
    </tr>
</table>


        <table class="table table-hover form-item-table" id="">
            <thead>
                <tr>
                    <th class="resizable text-center" width="300px" scope="col">Name<div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" scope="col">Credit Account<div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="200px">Amount<div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="250px">Remarks<div class="resizer"></div>
                    </th>
                </tr>
            </thead>
            <tbody>
      
                <tr>
                    <td><select class="form-control js-example-basic-single noborder" name="cfc_name[]" id="cfc_name_1">
                            <option value=""></option>
                            <?php $__currentLoopData = $customs_freight_account; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                            <?php
    $settings = App\SysHelper::getCompanyCodeSettings(session('logged_session_data.company_id'));

    $code = @$value->account_code;
    $showCode = true;

    // ensure $code is a string before checking
    $codeStr = (string) ($code ?? '');

    if (!$settings['is_account_code'] && \Illuminate\Support\Str::startsWith($codeStr, 'ACC')) {
        $showCode = false;
    } elseif (!$settings['is_subaccount_code'] && \Illuminate\Support\Str::startsWith($codeStr, 'SACC')) {
        $showCode = false;
    } elseif (!$settings['is_customer_code'] && \Illuminate\Support\Str::startsWith($codeStr, 'CUS')) {
        $showCode = false;
    } elseif (!$settings['is_supplier_code'] && \Illuminate\Support\Str::startsWith($codeStr, 'SUP')) {
        $showCode = false;
    }
?>

                                <option value="<?php echo e(@$value->id); ?>" <?php echo e(isset($edit_cfc[0]) ? (!empty(@$edit_cfc[0]->selling_exp_account) ? (@$edit_cfc[0]->selling_exp_account == $value->id ? 'selected' : '') : '') : ''); ?>>
                                    
                                    <?php if($showCode): ?>
                                        <?php echo e(@$value->account_name); ?> (<?php echo e(@$value->account_code); ?>)
                                    <?php else: ?>
                                        <?php echo e(@$value->account_name); ?>

                                    <?php endif; ?>
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select></td>
                    <td> <select class="form-control js-example-basic-single noborder" name="cfc_credit_account[]" id="cfc_credit_account_1"
                            readonly="true">
                            <option value="none"></option>
                            <?php $__currentLoopData = $supplier; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e(@$value->id); ?>" <?php echo e(isset($edit_cfc[0]) ? (!empty(@$edit_cfc[0]->credit_account) ? (@$edit_cfc[0]->credit_account == @$value->id ? 'selected' : '') : '') : ''); ?>>
                                    
                                                 

                                                    <?php if(@App\SysHelper::getCompanyCodeSettings(session('logged_session_data.company_id'))['is_supplier_code']): ?>
                                                        <?php echo e(@$value->account_name); ?> (<?php echo e(@$value->account_code); ?>)
                                                    <?php else: ?>
                                                        <?php echo e(@$value->account_name); ?>

                                                    <?php endif; ?>


                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select></td>
                    <td><input class="form-control text-end" type="number" id="cfc_amount_1" name="cfc_amount[]"
                            autocomplete="off" min="0" onchange="cfc_amount_change(1)"
                            value="<?php echo e(isset($edit_cfc[0]) ? (!empty(@$edit_cfc[0]->amount) ? @$edit_cfc[0]->amount : old('')) : old('')); ?>"
                            step="any"></td>
                    <td><input class="form-control" type="text" id="cfc_remarks_1" name="cfc_remarks[]"
                            autocomplete="off"
                            value="<?php echo e(isset($edit_cfc[0]) ? (!empty(@$edit_cfc[0]->remarks) ? @$edit_cfc[0]->remarks : old('')) : old('')); ?>">
                    </td>
                </tr>
                <tr>
                    <td><select class="form-control js-example-basic-single noborder" name="cfc_name[]" id="cfc_name_2">
                            <option value=""></option>
                            <?php $__currentLoopData = $customs_freight_account; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e(@$value->id); ?>" <?php echo e(isset($edit_cfc[1]) ? (!empty(@$edit_cfc[1]->selling_exp_account) ? (@$edit_cfc[1]->selling_exp_account == $value->id ? 'selected' : '') : '') : ''); ?>>
                                   <?php if($showCode): ?>
                                        <?php echo e(@$value->account_name); ?> (<?php echo e(@$value->account_code); ?>)
                                    <?php else: ?>
                                        <?php echo e(@$value->account_name); ?>

                                    <?php endif; ?>
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select></td>
                    <td><select class="form-control js-example-basic-single noborder" name="cfc_credit_account[]" id="cfc_credit_account_2"
                            readonly="true">
                            <option value="none"></option>
                            <?php $__currentLoopData = $supplier; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e(@$value->id); ?>" <?php echo e(isset($edit_cfc[1]) ? (!empty(@$edit_cfc[1]->credit_account) ? (@$edit_cfc[1]->credit_account == @$value->id ? 'selected' : '') : '') : ''); ?>>
                                    <?php if(@App\SysHelper::getCompanyCodeSettings(session('logged_session_data.company_id'))['is_supplier_code']): ?>
                                                        <?php echo e(@$value->account_name); ?> (<?php echo e(@$value->account_code); ?>)
                                                    <?php else: ?>
                                                        <?php echo e(@$value->account_name); ?>

                                                    <?php endif; ?>
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select></td>
                    <td><input class="form-control text-end" type="number" id="cfc_amount_2" name="cfc_amount[]"
                            autocomplete="off" min="0" onchange="cfc_amount_change(2)"
                            value="<?php echo e(isset($edit_cfc[1]) ? (!empty(@$edit_cfc[1]->amount) ? @$edit_cfc[1]->amount : old('')) : old('')); ?>"
                            step="any"></td>
                    <td><input class="form-control" type="text" id="cfc_remarks_2" name="cfc_remarks[]"
                            autocomplete="off"
                            value="<?php echo e(isset($edit_cfc[1]) ? (!empty(@$edit_cfc[1]->remarks) ? @$edit_cfc[1]->remarks : old('')) : old('')); ?>">
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</div>


<!-- Hidden inputs to store serial numbers per row (JSON format) -->
<?php $__currentLoopData = $quotationitems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php if($item->product_type == 2): ?>
        <?php
            $existingSerials = !empty($item->serial_numbers) ? $item->serial_numbers : '';
            $partNumber = $item->productname->part_number;
            $rowIndex = $index;
        ?>
        <input type="hidden" 
               id="serial_data_row_<?php echo e($rowIndex); ?>" 
               name="serial_numbers_by_row[<?php echo e($rowIndex); ?>]" 
               class="serial-data-storage" 
               data-row-index="<?php echo e($rowIndex); ?>"
               data-product-id="<?php echo e($item->product_id); ?>"
               data-part-number="<?php echo e($partNumber); ?>"
               data-qty="<?php echo e($item->qty); ?>"
               value="<?php echo e($existingSerials); ?>">

           <input type="hidden"
       name="part_number_by_row[<?php echo e($rowIndex); ?>]"
       value="<?php echo e($partNumber); ?>">

      
        
    <?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php if(isset($cart) && count($cart) > 0): ?>
    <?php $__currentLoopData = $cart; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cartIndex => $cart_items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $cartProduct = @App\SmItem::find($cart_items->product_id);
            $cartProductType = $cartProduct ? $cartProduct->product_type : null;
            $cartPartNumber = $cart_items->partnumber;
            $cartRowIndex = 'cart_' . $cartIndex;
        ?>
        <?php if($cartProductType == 2): ?>
            <input type="hidden" 
                   id="serial_data_row_<?php echo e($cartRowIndex); ?>" 
                   name="serial_numbers_by_row[<?php echo e($cartRowIndex); ?>]" 
                   class="serial-data-storage" 
                   data-row-index="<?php echo e($cartRowIndex); ?>"
                   data-product-id="<?php echo e($cart_items->product_id); ?>"
                   data-part-number="<?php echo e($cartPartNumber); ?>"
                   data-qty="<?php echo e($cart_items->qty); ?>"
                   value="">
            <input type="text" name="part_number_for_serial_<?php echo e($cartRowIndex); ?>" 
                   id="part_number_for_serial_<?php echo e($cartRowIndex); ?>"
                   value="<?php echo e($cartPartNumber); ?>" hidden>
            <input type="text" name="product_id_for_serial_<?php echo e($cartRowIndex); ?>" 
                   id="product_id_for_serial_<?php echo e($cartRowIndex); ?>" 
                   value="<?php echo e($cart_items->product_id); ?>" hidden>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                                <label class="form-label">Discount Amount</label>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const referenceInput2 = document.getElementById('note');
        const narrationTextarea2 = document.getElementById('narrationTextarea2');
        const insertButton2 = document.getElementById('insertNarration2');
        const narrationModal2 = document.getElementById('NoteModal');

        // Pre-fill textarea when modal opens
        narrationModal2.addEventListener('shown.bs.modal', () => {
            narrationTextarea2.value = referenceInput2.value;
        setTimeout(() => $('#narrationTextarea2').focus(), 500);

        });

        // On insert button click, update input and close modal
        insertButton2.addEventListener('click', () => {
            referenceInput2.value = narrationTextarea2.value;
            bootstrap.Modal.getInstance(narrationModal2).hide();
        });
    });
</script>

<div class="modal side-panel fade" id="NoteModal" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="poexcelimport">Enter Notes</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body">
                        <textarea style="height: 109px !important;" class="form-control capitalize-title" id="narrationTextarea2" rows="6"
                            placeholder="Write narration here..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="insertNarration2" class="btn btn-light add-btn ms-2">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>




<script>
$(document).on("keydown", 'input[name="cost[]"], input[name="tax[]"], input[name="qty[]"], input[name="unitprice[]"], input[name="discount[]"]', function(e) {
    if (e.key === "Enter") {
        e.preventDefault(); // prevent form submit

        let row = $(this).closest("tr"); // current row
        let name = $(this).attr("name");

        if (name === "cost[]") {
            row.find('input[name="qty[]"]').focus();
        } 
        else if (name === "tax[]") {
            row.find('input[name="qty[]"]').focus();
        }
        else if (name === "qty[]") {
            row.find('input[name="unitprice[]"]').focus();
        } 
        else if (name === "unitprice[]") {
            row.find('input[name="discount[]"]').focus();
        } 
        else if (name === "discount[]") {
            // Jump to next row's part_number[] and open Select2 dropdown
            let nextRow = row.next("tr");
            if (nextRow.length) {
                let partNumberSelect = nextRow.find('select[name="part_number[]"]');
                if (partNumberSelect.length) {
                    // Add the js-product-select class so the focus handler can initialize Select2
                    if (!partNumberSelect.hasClass('js-product-select')) {
                        partNumberSelect.addClass('js-product-select');
                    }
                    
                    // Trigger focus - the existing focus handler for .js-product-select 
                    // will initialize Select2 and open the dropdown automatically
                    partNumberSelect.trigger('focus');
                }
            }
        }
        
    }
});

// Normalize discount input while typing: remove leading zeros except when decimal like 0.x
$(document).on('input', 'input[name="discount[]"]', function () {
    var $el = $(this);
    var val = ($el.val() || '').toString();
    if (!val) return;

    // Remove commas (formatting) for checking
    var raw = val.replace(/,/g, '');

    // if raw starts with 0 followed by non-dot, strip leading zeros
    if (raw.length > 1 && raw.charAt(0) === '0' && raw.charAt(1) !== '.') {
        var cleaned = raw.replace(/^0+/, '');
        if (cleaned === '') cleaned = '0';
        // set the cleaned value (no commas). formatting/blur will handle display later.
        $el.val(cleaned);
    }
});
</script>


<script>
    let descriptionModal;
    document.addEventListener("DOMContentLoaded", function () {
        const descriptionElement = document.getElementById('descriptionModal');
        descriptionModal = new bootstrap.Modal(descriptionElement);
    });
    let currentDescriptionInput = null;

    $(document).on('click', 'textarea[name="description[]"]', function () {
        var $row = $(this).closest('tr');
        var partNumber = $row.find('select[name="part_number[]"]').val();
        if (!partNumber) {
            return; // do not open popup if no part number is selected in this row
        }

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

    document.getElementById("discount_add_btn").addEventListener("click", function () {
        splitAmount('discountInput', 'discount');
        $('#discountModal').modal('hide');
    });
</script>




<script>
    function calc_change_new(el) {
        $("#loading_bg").css("display", "block");

        // Get the current row
        var $row = $(el).closest('tr');

        // Read values from the current row
        var net_vat = $row.find('input[name="tax[]"]').val() || '0';

        var qty = $row.find('input[name="qty[]"]').val() || '0';
        // unitprice may contain expressions like +100, -50, +10%, -10% or absolute numbers like 1000
        var unitpriceRaw = ($row.find('input[name="unitprice[]"]').val() || '').toString().trim();

        function parseUnitPrice(raw, cost) {
            if (!raw || raw === '') return null;
            raw = raw.replace(/\s+/g, '').replace(/,/g, '');

            var m = raw.match(/^([+-]?)(\d+(?:\.\d+)?)%$/);
            if (m) {
                var sign = m[1];
                var pct = parseFloat(m[2]);
                if (!Number.isFinite(pct)) return null;
                if (!Number.isFinite(cost)) cost = 0;
                return sign === '-' ? (cost * (1 - pct / 100)) : (cost * (1 + pct / 100));
            }

            var m2 = raw.match(/^([+-])(\d+(?:\.\d+)?)$/);
            if (m2) {
                var sign2 = m2[1];
                var val = parseFloat(m2[2]);
                if (!Number.isFinite(val)) return null;
                if (!Number.isFinite(cost)) cost = 0;
                return sign2 === '-' ? (cost - val) : (cost + val);
            }

            var v = parseFloat(raw);
            if (Number.isFinite(v)) return v;
            return null;
        }

        var unitprice = null;
        var discount = $row.find('input[name="discount[]"]').val().replace(/,/g, '') || '0';
        var fright = 0;
        var customcharges = 0;

        var decimal_point = <?php echo json_encode(session('logged_session_data.decimal_point'), 15, 512) ?>;

        // Compute unitprice numeric value — support relative/percentage expressions
        var costRaw = $row.find('input[name="cost[]"]').val().replace(/,/g, '') || '0';
        var cost = parseFloat(costRaw);
        if (!Number.isFinite(cost)) cost = 0;

        if (typeof unitpriceRaw === 'string' && (unitpriceRaw.indexOf('%') !== -1 || unitpriceRaw[0] === '+' || unitpriceRaw[0] === '-')) {
            var computed = parseUnitPrice(unitpriceRaw, cost);
            if (computed !== null) {
                unitprice = computed;
                var decimal_point = parseInt(<?php echo json_encode(session('logged_session_data.decimal_point') ?? 2, 15, 512) ?>);
                if (!Number.isFinite(decimal_point)) decimal_point = 2;
                try { $row.find('input[name="unitprice[]"]').val(typeof formatAmount === 'function' ? formatAmount(Number(unitprice).toFixed(decimal_point)) : Number(unitprice).toFixed(decimal_point)); } catch (err) { $row.find('input[name="unitprice[]"]').val(Number(unitprice).toFixed(decimal_point)); }
            } else {
                unitprice = parseFloat(unitpriceRaw.replace(/,/g, '')) || 0;
            }
        } else {
            unitprice = parseFloat(unitpriceRaw.replace(/,/g, '')) || 0;
        }

        // Calculate value
        var fin_value = parseFloat(unitprice) * parseFloat(qty);
         if (parseFloat(qty) > 0 && parseFloat(unitprice) > 0) {
            $row.find('input[name="value[]"]').val(formatAmount(fin_value));
        } else {
            $row.find('input[name="value[]"]').val('');
        }

        // Calculate taxable amount
        var fin_taxableamount = fin_value + parseFloat(customcharges) + parseFloat(fright) - parseFloat(discount);
           if (parseFloat(qty) > 0 && parseFloat(unitprice) > 0) {
            $row.find('input[name="taxableamount[]"]').val(formatAmount(fin_taxableamount));
        } else {
            $row.find('input[name="taxableamount[]"]').val('');
        }

        // Calculate VAT
        var fin_vatamount = fin_taxableamount * (parseFloat(net_vat) / 100);
           if (parseFloat(qty) > 0 && parseFloat(unitprice) > 0) {
            $row.find('input[name="vatamount[]"]').val(formatAmount(fin_vatamount));
        } else {
            $row.find('input[name="vatamount[]"]').val('');
        }

        // Calculate total amount
        var total_amount = fin_taxableamount + fin_vatamount;
            if (parseFloat(qty) > 0 && parseFloat(unitprice) > 0) {
            $row.find('input[name="totalamount[]"]').val(formatAmount(total_amount));
        } else {
            $row.find('input[name="totalamount[]"]').val('');
        }

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
            total_cost = 0;

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
            
            total_cost += (
                parseFloat($row.find('input[name="cost[]"]').val().replace(/,/g, '')) || 0
            ) * (
                parseFloat($row.find('input[name="qty[]"]').val()) || 0
            );

        });

        $('#lbl_total_qty').text(total_qty);
        $('#lbl_total_price').text(formatAmount(total_price));
        $('#lbl_total_value').text(formatAmount(total_value));
        $('#lbl_total_discount').text(formatAmount(total_discount));
        //$('#lbl_total_fright').text(total_fright.toFixed(decimal_point));
        //$('#lbl_total_customcharges').text(total_customcharges.toFixed(decimal_point));
        $('#lbl_total_taxableamount').text(formatAmount(total_taxableamount));
        $('#lbl_total_vatamount').text(formatAmount(total_vatamount));
        $('#lbl_total_totalamount').text(formatAmount(total_totalamount));
        $('#lbl_total_cost').text(formatAmount(total_cost));
        
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
                    url: '<?php echo e(route('autocomplete.get_cust_account_list_ajax')); ?>',
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
    $(document).ready(function () {
        function initAccountSelect2(selector) {
            $(selector).select2({
                ajax: {
                    url: '<?php echo e(route('autocomplete.get_product_list_ajax')); ?>',
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
                console.log(selectedData)

                // Set values using "name" attribute selectors inside the same row
                //$row.find('input[name="description[]"]').val(selectedData.description || '');
                $row.find('textarea[name="description[]"]').val(selectedData.description || '');
                $row.find('input[name="part_number_txt[]"]').val(selectedData.text || '');
                $row.find('input[name="hscode_txt[]"]').val(selectedData.hscode || '');
                $row.find('input[name="product_type[]"]').val(selectedData.product_type || '');
                $row.find('input[name="product_type_part_number_text[]"]').val(selectedData
                    .description || '');
                $row.find('input[name="discount[]"]').val(0);
                $row.find('input[name="tax[]"]').val(parseInt($('#net_vat').val()));
                $row.find('input[name="cost[]"]').focus();
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
                document.querySelector('.select2-container--open .select2-search__field')
                    ?.focus();
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
    $(document).ready(function () {
        if ($("#source").val() == "Other") {
            $("#source_o").css("display", "block");
            $("#source_o").prop('required', true);
            $("#sourcediv").css("display", "block");
        } else {
            $("#source_o").css("display", "none");
            $("#source_o").prop('required', false);
            $("#sourcediv").css("display", "none");
        }
    });

    $(document).on("change", "#source", function () {
        if ($("#source").val() == "Other") {
            $("#source_o").css("display", "block");
            $("#source_o").prop('required', true);
            $("#sourcediv").css("display", "block");
        } else {
            $("#source_o").css("display", "none");
            $("#source_o").prop('required', false);
            $("#sourcediv").css("display", "none");
        }
    });

    function change_cust_id() {
        var id = $("#cust_id").val();
        var user = $("#user_id").val();
        get_cust_name(id);
        get_sales_person(id, user);
        get_vat(id);
    }

         function get_vat(id) {
            $("#loading_bg").css("display", "block");
            var action = "<?php echo e(URL::to('get-vat-by-ca')); ?>";
            $.ajax({
                url: action,
                type: "GET",
                data: {
                    _token: '<?php echo e(csrf_token()); ?>',
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

    function get_cust_name(id) {
        $("#loading_bg").css("display", "block");
        var action = "<?php echo e(URL::to('crm-leads-customername')); ?>";
        $.ajax({
            url: action,
            type: "GET",
            data: {
                _token: '<?php echo e(csrf_token()); ?>',
                id: id,
            },
            cache: false,
            success: function (dataResult) {
                console.log(dataResult)
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                var len = 0;
                if (dataResult['data'] != null) {
                    len = dataResult['data'].length;
                }
                if (len > 0) {
                    for (var i = 0; i < len; i++) {
                        var name = dataResult['data'][i].customer_salutation + ' ' + dataResult['data'][i]
                            .first_name + ' ' + dataResult['data'][i].last_name;
                        var address = dataResult['data'][i].address + ', ' + dataResult['data'][i]
                            .address2 + ', ' + dataResult['data'][i].city + ', ' + dataResult['data'][i]
                                .statename + ', ' + dataResult['data'][i].name;
                        $("#cust_name").val(name.replace('null ', '').replace('null', ''));
                        $("#designation").val(dataResult['data'][i].designation);
                        $("#cust_no").val(dataResult['data'][i].mobile);
                        $("#cust_email").val(dataResult['data'][i].email);
                        $("#address").val(address);
                        $('#payment_terms').val(dataResult['data'][i].payment_terms).trigger('change');

                        //1.Reseller
                        if (dataResult['data'][i].account_type == 1) {
                            $("#isproject").val(1);
                            $('#is_professional_service').prop("checked", false);
                        } //2.Enduser
                        if (dataResult['data'][i].account_type == 2) {
                            $("#isproject").val(2);
                            $('#is_professional_service').prop("checked", false);
                        } //3.Ecommerce
                        if (dataResult['data'][i].account_type == 3) {
                            $("#isproject").val(3);
                            $('#is_professional_service').prop("checked", false);
                        }
                    }
                } else {
                    $("#cust_name").val();
                    $("#designation").val();
                    $("#cust_no").val();
                    $("#cust_email").val();
                    $("#address").val();
                    $("#isproject").val();
                }
                $("#loading_bg").css("display", "none");
            }
        });
    }

    function get_sales_person(id, user) {
        $("#loading_bg").css("display", "block");
        var action = "<?php echo e(URL::to('get-salesperson-list')); ?>";
        $.ajax({
            url: action,
            type: "GET",
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
                    $('#owner').find('option').remove();
                    for (var i = 0; i < len; i++) {
                        var id = dataResult['data'][i].id;
                        var name = dataResult['data'][i].full_name;
                        var sele = '';
                        if (user == id) {
                            sele = 'selected';
                        }
                        var option = "<option value='" + id + "' " + sele + ">" + name + "</option>";
                        $("#owner").append(option);
                    }
                } else {
                    $('#owner').find('option').remove();
                }
                $("#loading_bg").css("display", "none");
            }
        });
    }

    $(document).on("click", "#btn_add_company", function () {

        //$("#btn_add_company").css("display", "none");

        var company_name_add = $("#company_name_add").val();
        var cust_name_add = $("#cust_name_add").val();
        var designation_add = $("#designation_add").val();
        var cust_no_add = $("#cust_no_add").val();
        var cust_email_add = $("#cust_email_add").val();
        var cust_address_add = $("#cust_address_add").val();
        var cust_address_add2 = $("#cust_address_add2").val();
        var country_add = $("#country_ship").val();

        var cust_city = $("#cust_city").val();
        var state_ship = $("#state_ship").val();
        var cust_pobox = $("#cust_pobox").val();
        var sales_person = $("#cust_sales_person").val();
        var payment_terms = $("#payment_terms").val();
        var account_type = $("#account_type").val();
        var company_id = $("#company").val();

        var action = "<?php echo e(URL::to('add-customer-detail-popup')); ?>";
        $.ajax({
            url: action,
            type: "GET",
            data: {
                _token: '<?php echo e(csrf_token()); ?>',
                company_name_add: company_name_add,
                cust_name_add: cust_name_add,
                designation_add: designation_add,
                cust_no_add: cust_no_add,
                cust_email_add: cust_email_add,
                cust_address_add: cust_address_add,
                cust_address_add2: cust_address_add2,
                vat_country: country_add,
                city: cust_city,
                vat_state: state_ship,
                zip_code: cust_pobox,
                sales_person: sales_person,
                payment_terms: payment_terms,
                account_type: account_type,
                company_id: company_id,
            },
            cache: false,
            success: function (dataResult) {
                //alert(dataResult);
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                if (dataResult['data'] == "ERROR") {
                    alert("Error found in something!!");
                    $("#btn_add_company").css("display", "block");
                } else if (dataResult['data'] == "ERROR2") {
                    alert("Company Name already exists!! Please Contact Support");
                    $('#company_name_add').css("border", "1px solid red");
                    $('#company_name_add').focus();
                    $("#btn_add_company").css("display", "block");
                } else {
                    if (dataResult['data'] != null) {
                        len = dataResult['data'].length;
                    }
                    if (len > 0) {

                        $('#cust_id').find('option').not(':first').remove();
                        var newCompanyId = dataResult['new_company_id'];

                        for (var i = 0; i < len; i++) {
                            var id = dataResult['data'][i].id;
                            var name = dataResult['data'][i].name;
                            var name2 = dataResult['data'][i].code;
                            var option = "<option value='" + id + "'>" + name + "</option>";
                            $("#cust_id").append(option);
                        }
                        if (newCompanyId) {
                            $("#cust_id").val(newCompanyId).trigger('change');
                        }
                        alert('Company Name Added Successfully!!');
                        $('#btn_close2').click();
                        $("#btn_add_company").css("display", "block");
                        //location.reload();
                        //$("#company_name").change();
                    }
                }
            }
        });
    });

    $(document).ready(function () {
        // Trigger change event only if a country is selected by default
        if ($('#country_ship').val() !== '') {
            $('#country_ship').trigger('change');
        }




           // When Company select2 opens, prefill the search box with the currently selected option
        // so the user can edit/change the selection easily.
        $('#cust_id').on('select2:open', function() {
            var selectedText = $(this).find('option:selected').text().trim();
            var $search = $('.select2-container--open .select2-search__field');
            if ($search.length) {
                // Don't prefill if placeholder or empty
                if (selectedText && selectedText !== 'Select') {
                    $search.val(selectedText);
                    // trigger input so Select2 reacts to the injected value
                    $search.trigger('input');

                    // move cursor to end for easier editing (works in modern browsers)
                    var el = $search.get(0);
                    try {
                        if (el && el.setSelectionRange) {
                            var len = selectedText.length * 2; // safe trick to put cursor at the end
                            el.setSelectionRange(len, len);
                        }
                    } catch (e) {
                        // ignore if setSelectionRange not supported
                    }
                } else {
                    $search.val('');
                    $search.trigger('input');
                }
            }
        });
    });
</script>







<!-- Modal Change Currancy-->
<div class="modal side-panel fade" id="ModalChangeCurrancy" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="ModalChangeCurrancy" aria-hidden="true">
    <?php
      @$currency = $currencylist->firstWhere('id', $currency_id);
@$currencyCode = $currency ? $currency->code : null;

    ?>
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel">Change Currancy (<?php echo e(@$currencyCode); ?>)</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-quote-update-currency', 'method' => 'POST', 'enctype' => 'multipart/form-data'])); ?>

            <div class="modal-body">
                <div class="row">
                            <input type="hidden" name="from_currency_id" value="<?php echo e($currency_id); ?>" />


                    <div class="col-md-12">
                        <div class="mb-3 mt-2">
                            <label for="" class="form-label">Convert To</label>
                            <select class="form-control js-example-basic-single" name="to_currency_id" id="to_currency_id" required
                                onchange="set_rate()">
                                <option value="">Select</option>
                                <?php $__currentLoopData = $currencylist2; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e(@$value->id); ?>"><?php echo e(@$value->code); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__currentLoopData = $currencylist2; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <input type="hidden" id="rate_<?php echo e(@$value->id); ?>" name="rate_<?php echo e(@$value->id); ?>"
                                    value="<?php echo e(@$value->rate); ?>" />
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                <input type="hidden" name="cur_quote_id" value="<?php echo e($quote_id); ?>" />
                <input type="hidden" name="cur_deal_id" value="<?php echo e($edit->id); ?>" />
                <button type="submit" class="btn btn-light add-btn ms-2">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Change
                </button>
            </div>
            <?php echo e(Form::close()); ?>

        </div>
    </div>
</div>
<!-- Modal Change Currancy-->



<div class="modal side-panel fade" id="narrationModal" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="poexcelimport">Terms and Condition:</h4>
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



   <!-- Modal Support-->
    <div class="modal fade" id="ModalSupport" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">Add Pre-Sales Request</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-support', 'method' => 'POST', 'enctype' => 'multipart/form-data'])); ?>

                <input type="hidden" name="support_id" value="0" />
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Customer</label>
                                <input type="text" class="form-control" value="<?php echo e($edit->customername->name); ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label for="" class="form-label">Deal Id</label>
                                <input type="text" class="form-control" value="<?php echo e($edit->deal_code->code); ?>" readonly>
                                <input type="hidden" name="deal_id" id="deal_id" value="<?php echo e($edit->id); ?>">
                            </div>
                        </div>

                          <div class="col-md-2">
                            <div class="mb-3">
                                <label for="" class="form-label">Date</label>
                                <input type="text" class="form-control date-picker" name="support_date" id="support_date" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label for="" class="form-label">From</label>
                                <input type="time" class="form-control" name="time_from" id="time_from" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label for="" class="form-label">To</label>
                                <input type="time" class="form-control" name="time_to" id="time_to" required>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Site Name</label>
                                <input type="text" class="form-control" name="site_name" id="site_name" value="<?php echo e($edit->address); ?>" required>
                            </div>
                        </div>
                      
                        <div class="col-md-12">
                            <div class="mb-3">
                                
                             
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-semibold mb-0">Scope of Work</label>
                                <button type="button" class="btn btn-sm btn-light border" onclick="add_scope_of_work()">
                                    <i class="ico icon-outline-add-square me-1"></i> Add
                                </button>
                            </div>

                        <table class="table table-sm table-borderless align-middle mb-0">
                            <tbody>
                                <tr id="row_1">
                                    <td class="text-muted text-center" width="5%">1.</td>
                                    <td>
                                        <input type="text" class="form-control" name="scope_of_work[]" id="scope_of_work_1" required>
                                    </td>
                                    <td width="5%"></td>
                                </tr>

                                <?php for($i = 2; $i <= 20; $i++): ?>
                                    <tr id="row_<?php echo e($i); ?>" style="display: none;">
                                        <td class="text-muted text-center" width="5%"><?php echo e($i); ?>.</td>
                                        <td>
                                            <input type="text" class="form-control" name="scope_of_work[]" id="scope_of_work_<?php echo e($i); ?>">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-light" onclick="delete_work(<?php echo e($i); ?>)">
                                                <i class="ico icon-outline-trash-bin-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endfor; ?>
                            </tbody>
                        </table>

                            <input type="hidden" id="scope_of_work_row_id" value="1" />



                            <script>
                            function add_scope_of_work() {
                                // Find first hidden row
                                let nextHidden = $('tr[id^="row_"]:hidden').first();

                                if (nextHidden.length > 0) {
                                    // Check the current last visible input is not empty
                                    let lastVisible = $('tr[id^="row_"]:visible').last();
                                    let input = lastVisible.find('input');
                                    if (input.val().trim() === '') {
                                        input.focus();
                                        return;
                                    }

                                    // Show next hidden row
                                    nextHidden.fadeIn();
                                    let id = nextHidden.attr('id').split('_')[1];
                                    $('#scope_of_work_' + id).prop("required", true);

                                    // Update hidden counter
                                    $('#scope_of_work_row_id').val(id);
                                }
                            }

                            function delete_work(id) {
                                // Clear value, hide row
                                $('#scope_of_work_' + id).val('').prop("required", false);
                                $('#row_' + id).fadeOut();

                                // Update counter to last visible row index
                                let lastVisible = $('tr[id^="row_"]:visible').last().attr('id');
                                let lastId = lastVisible ? parseInt(lastVisible.split('_')[1]) : 1;
                                $('#scope_of_work_row_id').val(lastId);
                            }
                            </script>


                                
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="customer_id" id="customer_id" required value="<?php echo e($edit->cust_id); ?>" />
                    <input type="hidden" name="sales_person_id" id="sales_person_id" required value="<?php echo e($edit->owner); ?>" />
                    <button type="submit" class="btn btn-light add-btn ms-2">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Add Service
                    </button>
                </div>
                <?php echo e(Form::close()); ?>

            </div>
        </div>
    </div>
    <!-- Modal Support-->
    <!-- Modal Support Cmt-->
    <div class="modal side-panel fade" id="ModalSupportCmt" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">Add Service Comments</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-support-activity-comments', 'method' => 'POST', 'enctype' => 'multipart/form-data'])); ?>

                <?php if(count($support)!=0): ?>
                    <input type="hidden" name="support_id" value="<?php echo e($support[0]->id); ?>" />
                <?php endif; ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Comments</label>
                                <textarea class="form-control" name="remarks" id="remarks3" rows="10" required></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
						<button type="submit" class="btn btn-light add-btn ms-2">
							<i class="ico icon-outline-bookmark-opened text-success"></i> Add Comments
						</button>
                </div>
                <?php echo e(Form::close()); ?>

            </div>
        </div>
    </div>
    <!-- Modal Support Cmt-->





                         
<div class="modal side-panel  fade" id="reserve_qty_popup" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg draggable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title ps-0">Reserve Qty</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                       
                            <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'reserve-qty-deal', 'method' => 'POST', 'enctype' => 'multipart/form-data'])); ?>

                         
                               
                                    <div class="">
                                        <table  class="table table-hover" id="long-list" style="table-layout: fixed;width:100%">
                                            <thead>
                                                <tr>
                                                    <th style="width:60px"><?php echo app('translator')->getFromJson('Part No'); ?></th>
                                                    <th style="width:70px"><?php echo app('translator')->getFromJson('Description'); ?></th>
                                                    <th style="width:20px" class="text-center"><?php echo app('translator')->getFromJson('Qty'); ?></th>
                                                    <th style="width:20px" class="text-center"><?php echo app('translator')->getFromJson('Bal Qty'); ?></th>
                                                    <th style="width:20px" class="text-center"><?php echo app('translator')->getFromJson('Res. Qty'); ?></th>
                                                    <th style="width:20px" class="text-center"><?php echo app('translator')->getFromJson('Avl Qty'); ?></th>
                                                    <th style="width:30px" class="text-center"><?php echo app('translator')->getFromJson('Reserve'); ?></th>
                                                    <th style="width:30px" class="text-center"><?php echo app('translator')->getFromJson('Res. Date'); ?></th>


                                                    
                                                    
                                                </tr>
                                            </thead>
                                            <tbody>


                                               
                                                <?php if(count($quotationitems) > 0): ?>
                                                    <?php $__currentLoopData = $quotationitems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                 
                                                    <td> <?php echo e(@$item->productname->part_number); ?></td>
                                                    <td><?php echo @$item->description; ?></td>
                                                    <td class="text-center no-toggle"><?php echo e(@$item->qty); ?></td>

                                      

                                                    <?php
                                                           @$stocklist = DB::table('sys_item_stock as stock')
                    ->select([
                        DB::raw('MAX(stock.partno) as partno'),
                        DB::raw('MAX(item.part_number) as part_number'),
                        DB::raw('MAX(item.id) as stockid'),
                        DB::raw('MAX(item.description) as description'),
                        DB::raw('MAX(brand.title) as brand'),
                        DB::raw('MAX(brand.id) as brandid'),
                        DB::raw('(SUM(stock.qty_in) - SUM(stock.qty_out)) as balance_qty'),
                        DB::raw('IFNULL(SUM(stock.qty_in * stock.price_in) / NULLIF(SUM(stock.qty_in), 0), 0) as avg_price'),
                        DB::raw('MAX(cat.category_name) as categoryname'),
                        DB::raw('MAX(subcat.sub_category_name) as subcategoryname'),
                        DB::raw('2 as type'),
                    ])
                    ->join('sm_items as item', 'item.id', '=', 'stock.partno')
                    ->join('sys_brand as brand', 'brand.id', '=', 'item.brand')
                    ->leftJoin('sm_item_categories as cat', 'cat.id', '=', 'item.category_name')
                    ->leftJoin('sm_item_subcategories as subcat', 'subcat.id', '=', 'item.subcategory_name')
                    ->whereDate('stock.doc_date', '<=', today())
                    ->where('stock.company_id', $edit->company_id)
                    ->where('stock.status', 1)
                    ->where('item.status', 1)
                    ->where('stock.doc_number', 'not like', 'SRN%')
                    ->where('item.id', $item->product_id)
                    ->whereIn('item.product_type', [1, 2])
                    ->groupBy('stock.partno')
                    ->orderByRaw('MAX(item.part_number) ASC') // use the unique stock ID for grouping
                    // ->paginate(100);
                ->first();
                 
                                        
                                        @$reserved_qty = @App\SysHelper::get_reserved_qty(
                                            @$stocklist->stockid,
                                            @$stocklist->part_number
                                        );
                                   
                
               
                                                    ?>
                                                       <?php
                                            @$avl_qty = @$stocklist->balance_qty - @$reserved_qty;
                                        ?>

                                                    <td class="text-center no-toggle"><?php echo e(@$stocklist->balance_qty ?? 0); ?></td>
                                                    <td class="text-center no-toggle" 
                                      data-stock='<?php echo json_encode(@$stocklist, 15, 512) ?>'
                                    data-balance="<?php echo e(@$stocklist->balance_qty); ?>"
                                        onclick="openReservedStockListModal(this)">
                                        <a href="#" style="cursor: pointer;" class="font-weight-600">
                                    
                                        <?php echo e(@$reserved_qty); ?>

                                        </a>
                                    </td>
                                                  
 <td class="text-center no-toggle"><?php echo e(@$avl_qty); ?></td>
                                                    
                                                <?php
                                                
                                                    @$r_stock = @$reserve_stock
                                                        ->where('stock_id', $item->product_id)
                                                        ->first();
                                                    
                                                ?>

                                                    <td class="no-toggle"><input type="number" name="qty[]" class="form-control text-center border-0" placeholder="Max: <?php echo e(@$item->qty); ?>" value="<?php echo e(@$r_stock->reserve_qty); ?>" max="<?php echo e(@$item->qty); ?>" /></td>


                                                    <?php
   
                                                        $date = null;


                                                        if($r_stock && $r_stock->reserve_date){
                                                            $date = $r_stock->reserve_date;
                                                        } elseif (isset($edit_delivery_date) && !empty($edit_delivery_date)) {
                                                            $date = Carbon\Carbon::parse($edit_delivery_date);
                                                        } elseif (isset($delivery_date) &&  !empty($delivery_date)) {
                                                            $date = Carbon\Carbon::parse($delivery_date);
                                                        }

                                                        // If date exists and is in the past → use today
                                                        if ($date && $date->isPast()) {
                                                            $date = Carbon\Carbon::today();
                                                        }

                                                        // Final value
                                                        $finalDate = $date
                                                            ? $date->format('d/m/Y')
                                                            : Carbon\Carbon::today()->format('d/m/Y');
                                                    ?>

                                                    <td class="no-toggle"><input type="text" data-min-date="<?php echo e(date('d/m/Y')); ?>" name="reserve_date[]" class="form-control text-center border-0 date-picker-no-past" placeholder="" value="<?php echo e($finalDate); ?>" /></td>
                                                    
                                                    
                                                    
                                                    
                                                  
                                                        
                                                 
                                                    
                                                  </tr>
                                                <input type="hidden" name="reserve_stock_id[]" value="<?php echo e(@$item->product_id); ?>" />
                                                <input type="hidden" name="reserve_part_number[]" value="<?php echo e(@$item->productname->part_number); ?>" />
                                          
                                                    
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    
                                                <?php endif; ?>
                                               
                                            </tbody>
                                        </table>
                                    </div>
                                
                           

                        <div class="modal-footer d-flex justify-content-center p-0">
                            <input type="hidden" name="req_deal_id" value="<?php echo e(@$edit->id); ?>" />
                                  <input type="hidden" name="reserve_customer_id" value="<?php echo e(@$edit->cust_id); ?>" />
                                    <input type="hidden" name="reserve_sales_person_id" value="<?php echo e(@$edit->owner); ?>" />
                            <button type="submit" class="btn btn-light add-btn ms-2">
                                <i class="ico icon-outline-bookmark-opened text-success"></i> Submit
                            </button>
                        </div>

                            <?php echo e(Form::close()); ?>

                       
                    </div>
                </div>
            </div>
        </div>












       <!-- Modal Collaboration-->
    <div class="modal side-panel fade" id="ModalCollaboration" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">Add Collaboration</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-collaboration', 'method' => 'POST', 'enctype' => 'multipart/form-data'])); ?>

                <input type="hidden" name="collaboration_deal_id" value="<?php echo e($edit->id); ?>" />
                <input type="hidden" name="collaboration_cust_id" value="<?php echo e($edit->cust_id); ?>" />
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Select Users</label>
                                <select class="form-control js-example-basic-single" name="user_id[]" multiple>
                                    <?php $__currentLoopData = $staff; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e(@$value->user_id); ?>"
                                            <?php if(isset($collaboration)): ?> <?php $__currentLoopData = $collaboration; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $coll): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($coll->user_id == $value->user_id): ?> selected <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?> ><?php echo e(@$value->full_name); ?>

                                    </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <div class="modal-footer">
						<button type="submit" class="btn btn-light add-btn ms-2">
							<i class="ico icon-outline-bookmark-opened text-success"></i> Add to Collaboration
						</button>
                </div>
                <?php echo e(Form::close()); ?>

            </div>
        </div>
    </div>
    <!-- Modal Collaboration-->

        <?php if($quotationitems->where('product_type', 2)->count() < 1): ?>

       <!-- Modal End User -->
    <div class="modal side-panel fade" id="ModalEndUserDetails" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header"> 
                    <h4 class="modal-title" id="exampleModalLabel">End User Details</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
    <?php if($enduser==""): ?>
    <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-add-end-user', 'method' => 'POST', 'enctype' => 'multipart/form-data'])); ?>

                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="end_user_deal_id" value="<?php echo e($edit->id); ?>" />
                        <div class="col">
                            <div class="mb-3">
                                <label for="" class="form-label">Company Name *</label>
                                <input type="text" class="form-control capitalize-title" name="end_user_company_name" id="end_user_company_name" required />
                            </div>
                        </div>
                          <?php if($quotationitems->where('product_type', 2)->count() > 0): ?>
                        <div class="col">
                            <div class="mb-3">
                                <label for="" class="form-label">Device Serial</label>
                                <div class="input-group">
                                    <input type="text" class="form-control capitalize-title" name="device_serial" id="device_serial" readonly style="cursor:pointer;" />
                                    <button type="button" class="btn btn-light border"  >
                                        <i class="ico icon-outline-list-down"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        
                        <div class="col">
                            <div class="mb-3">
                                <label for="" class="form-label">Contact Person *</label>
                                <input type="text" class="form-control capitalize-title" name="end_user_contact_person" id="end_user_contact_person" required />
                            </div>
                        </div>
                        
                        <div class="col">
                            <div class="mb-3">
                                <label for="" class="form-label">Mobile No</label>
                                <input type="text" class="form-control" name="mobile_no" id="mobile_no" />
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3">
                                <label for="" class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" id="email" />
                            </div>
                        </div>
                        
                    </div>
                </div>
                <div class="modal-footer">
						<button type="submit" class="btn btn-light add-btn ms-2">
							<i class="ico icon-outline-bookmark-opened text-success"></i> Save
						</button>
                </div>
                <?php echo e(Form::close()); ?>        
                <?php else: ?>
          <div class="modal-body">
    <div class="row">

        <div class="col-3">
            <p class="font-weight-600 mb-0 truncate-text-custom">Company Name</p> <br>
           <span class="truncate-text-custom"><?php echo e($enduser->end_user_company_name); ?></span> 
        </div>
                          <?php if($quotationitems->where('product_type', 2)->count() > 0): ?>


        <div class="col-3">
            <p class="font-weight-600 mb-0 truncate-text-custom">Device Serial</p><br>
             <span class="truncate-text-custom"><?php echo e($enduser->device_serial); ?></span> 
        </div>
        <?php endif; ?>

        <div class="col-3">
            <p class="font-weight-600 mb-0 truncate-text-custom">Contact Person</p><br>
           <span class="truncate-text-custom"><?php echo e($enduser->end_user_contact_person); ?></span> 
        </div>

        <div class="col-3">
            <p class="font-weight-600 mb-0 truncate-text-custom">Mobile No</p><br>
            <span class="truncate-text-custom"><?php echo e($enduser->mobile_no); ?></span> 
        </div>

        <div class="col-3">
            <p class="font-weight-600 mb-0 truncate-text-custom">Email</p><br>
            <span class="truncate-text-custom"><?php echo e($enduser->email); ?></span>
        </div>

        

    </div>
            </div>


                <?php endif; ?>

            </div>
        </div>
    </div>
    <!-- Modal End User -->
    <?php endif; ?>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const referenceInput2Address = document.getElementById('address');
        const narrationTextarea2Address = document.getElementById('narrationTextarea2Address');
        const insertButton2Address = document.getElementById('insertNarration2Address');
        const narrationModal2Address = document.getElementById('AddressModal');

        // Pre-fill textarea when modal opens
        narrationModal2Address.addEventListener('shown.bs.modal', () => {
            narrationTextarea2Address.value = referenceInput2Address.value;
        setTimeout(() => $('#narrationTextarea2Address').focus(), 500);

        });

        // On insert button click, update input and close modal
        insertButton2Address.addEventListener('click', () => {
            referenceInput2Address.value = narrationTextarea2Address.value;
            bootstrap.Modal.getInstance(narrationModal2Address).hide();
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const referenceInput2Email = document.getElementById('cust_email');
        const narrationTextarea2Email = document.getElementById('narrationTextarea2Email');
        const insertButton2Email = document.getElementById('insertNarration2Email');
        const narrationModal2Email = document.getElementById('EmailModal');

        // Pre-fill textarea when modal opens
        narrationModal2Email.addEventListener('shown.bs.modal', () => {
            narrationTextarea2Email.value = referenceInput2Email.value;
        setTimeout(() => $('#narrationTextarea2Email').focus(), 500);

        });

        // On insert button click, update input and close modal
        insertButton2Email.addEventListener('click', () => {
            referenceInput2Email.value = narrationTextarea2Email.value;
            bootstrap.Modal.getInstance(narrationModal2Email).hide();
        });
    });
</script>



<div class="modal side-panel fade" id="AddressModal" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="poexcelimport">Enter Address</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body">
                        <textarea style="height: 109px !important;" class="form-control capitalize-title" id="narrationTextarea2Address" rows="6"
                            placeholder="Write address here..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="insertNarration2Address" class="btn btn-light add-btn ms-2">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>





<div class="modal side-panel fade" id="EmailModal" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="poexcelimport">Enter Email</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body">
                        <input class="form-control" id="narrationTextarea2Email" 
                            placeholder="Write email here...">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="insertNarration2Email" class="btn btn-light add-btn ms-2">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>




<!-- Device Serial Modal -->
<div class="modal fade" id="DeviceSerialModal" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="DeviceSerialModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" style="    max-width: 22rem;">
        <div class="modal-content">
            <div class="modal-header mb-2">
                <h4 class="modal-title" id="DeviceSerialModalLabel">Device Serial Numbers</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0">
                <div id="serial-parts-container">
                    <!-- Part number sections will be generated here -->
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

<style>
.part-serial-section {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 5px;
    margin-bottom: 15px;
    
}
.part-serial-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    padding-bottom: 10px;
    border-bottom: 2px solid #dee2e6;
}
.part-name {
    font-weight: 600;
    color: #495057;
    font-size: 13px;
}
.qty-badge {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    background-color: #e8eafd !important;
    color: #2a3eb1;
}

.serial-input-row {
    display: flex;
    align-items: center;
    margin-bottom: 8px;
    gap: 8px;
}
.serial-input-row input {
    flex: 1;
}
.serial-count-display {
    font-size: 12px;
    color: #6c757d;
    margin-top: 5px;
}
.serial-count-display.complete {
    background-color: #e6f7ee !important;
  color: #1b7a3f; 
}
.serial-count-display.incomplete {
   background-color: #fff7e6 !important;
  color: #b26a00;
}
</style>

<script>
// Device Serial Modal - Per Part Number Management
$(document).ready(function() {
    
    // Open modal and populate with part numbers
    $(document).on('click', '#device_serial', function() {
        populateSerialModal();
        $('#DeviceSerialModal').modal('show');
    });

     // Open modal and populate with part numbers
    $(document).on('click', '#device_serial_btn_modal', function() {
        populateSerialModal();
        $('#DeviceSerialModal').modal('show');
    });
    
    
    function populateSerialModal() {
        var $container = $('#serial-parts-container');
        $container.empty();
        
        // Find all products with type 2 (each row gets its own section)
        $('.serial-data-storage').each(function(storageIndex) {
            var $storage = $(this);
            var rowIndex = $storage.data('row-index');
            var productId = $storage.data('product-id');
            var partNumber = $storage.data('part-number');
            
            // Validate partNumber before proceeding
            if (!partNumber || typeof partNumber !== 'string') {
                console.warn('Invalid part number for product:', productId);
                return; // Skip this item
            }
            
            var qty = parseInt($storage.data('qty')) || 0;
            var existingData = $storage.val();
            
            if (qty <= 0) return; // Skip if no quantity
            
            // Create section for this ROW (not grouped by part number)
            var rowLabel = (storageIndex + 1);
            var sectionHtml = `
                <div class="part-serial-section" data-row-index="${rowIndex}">
                    <div class="part-serial-header">
                        <div>
                            <div class="part-name">Row ${rowLabel}: ${partNumber}</div>
                            <small class="text-muted">Qty: ${qty}</small>
                        </div>
                            <div class="serial-count-display qty-badge">0 of ${qty}</div>
                    </div>
                    <div class="serial-inputs-list" data-qty="${qty}">
                        <!-- Serial inputs here -->
                    </div>
                   
                </div>
            `;
            $container.append(sectionHtml);
            
            var $section = $container.find(`[data-row-index="${rowIndex}"]`);
            var $inputsList = $section.find('.serial-inputs-list');
            
            // Load existing serials or create empty fields
            if (existingData) {
                loadSerialsForPart($inputsList, existingData, rowIndex);
            } else {
                // Create qty number of empty fields
                for (var i = 1; i <= qty; i++) {
                    addSerialInput($inputsList, i, rowIndex, '');
                }
            }
            
            updateSerialCount(rowIndex);
        });
    }
    
    function loadSerialsForPart($list, data, rowIndex) {
        // Try JSON first, otherwise treat as comma-separated list
        var serials = [];
        try {
            var parsed = JSON.parse(data);
            if (Array.isArray(parsed)) serials = parsed;
            else throw new Error('Not an array');
        } catch (e) {
            serials = (data || '').split(',').map(function(s) { return s.trim(); }).filter(function(s) { return s !== ''; });
        }

        serials.forEach(function(serial, index) {
            addSerialInput($list, index + 1, rowIndex, serial);
        });

        // Pad empty inputs up to qty so counts and UX remain consistent
        var qty = parseInt($list.data('qty')) || 0;
        var current = $list.find('.serial-input-row').length;
        for (var i = current + 1; i <= qty; i++) {
            addSerialInput($list, i, rowIndex, '');
        }
    }

    function escAttr(str) {
        if (str === undefined || str === null) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/'/g, '&#39;');
    }
    
    function addSerialInput($list, index, rowIndex, value) {
        value = value || '';
        var safe = escAttr(value);
        var html = '<div class="serial-input-row" data-index="' + index + '">' +
            '<span class="text-muted" style="min-width: 20px;">' + index + '.</span>' +
            '<input type="text" class="form-control form-control-sm part-serial-input" placeholder="Enter serial number" data-row-index="' + rowIndex + '" value="' + safe + '" autocomplete="off" />' +
            '<button type="button" class="btn btn-sm btn-light border remove-serial-btn">' +
                '<i class="ico icon-outline-minus-circle text-danger"></i>' +
            '</button>' +
        '</div>';
        $list.append(html);
    }
    
    // Add serial button
    $(document).on('click', '.add-serial-btn', function() {
        var $section = $(this).closest('.part-serial-section');
        var rowIndex = $section.data('row-index');
        var $list = $section.find('.serial-inputs-list');
        var currentCount = $list.find('.serial-input-row').length;
        
        addSerialInput($list, currentCount + 1, rowIndex, '');
        updateSerialCount(rowIndex);
        
        // Focus new input
        $list.find('.serial-input-row').last().find('input').focus();
    });
    
    // Remove serial button
    $(document).on('click', '.remove-serial-btn', function() {
        var $row = $(this).closest('.serial-input-row');
        var $list = $row.closest('.serial-inputs-list');
        var rowIndex = $row.find('input').data('row-index');
        
        $row.remove();
        
        // Reindex
        $list.find('.serial-input-row').each(function(idx) {
            $(this).attr('data-index', idx + 1);
            $(this).find('span').first().text((idx + 1) + '.');
        });
        
        updateSerialCount(rowIndex);
    });
    
    // Update serial count display
    $(document).on('input blur', '.part-serial-input', function() {
        var rowIndex = $(this).data('row-index');
        updateSerialCount(rowIndex);
    });
    
    // Handle Enter key
    $(document).on('keydown', '.part-serial-input', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            var $currentRow = $(this).closest('.serial-input-row');
            var $nextInput = $currentRow.next().find('.part-serial-input');
            
            if ($nextInput.length) {
                $nextInput.focus();
            } else {
                // Move focus to the first serial input of the next part section
                var $currentSection = $(this).closest('.part-serial-section');
                var $nextSection = $currentSection.nextAll('.part-serial-section').first();

                if ($nextSection.length) {
                    var $inputsList = $nextSection.find('.serial-inputs-list');
                    var nextRowIndex = $nextSection.data('row-index');

                    // If next section has no inputs yet, create fields up to qty
                    var $nextInputs = $inputsList.find('.part-serial-input');
                    var qty = parseInt($inputsList.data('qty')) || 0;
                    if ($nextInputs.length === 0 && qty > 0) {
                        for (var i = 1; i <= qty; i++) {
                            addSerialInput($inputsList, i, nextRowIndex, '');
                        }
                        // ensure re-select inputs after creation
                        $nextInputs = $inputsList.find('.part-serial-input');
                    }

                    // Focus the first serial input in the next section (if any)
                    if ($nextInputs.length) {
                        $nextInputs.first().focus();
                        updateSerialCount(nextRowIndex);
                    }
                }
                // else: no next part, do nothing
            }
        }
    });
    
    function updateSerialCount(rowIndex) {
        var $section = $(`.part-serial-section[data-row-index="${rowIndex}"]`);
        var qty = parseInt($section.find('.serial-inputs-list').data('qty'));
        var filledCount = 0;
        
        $section.find('.part-serial-input').each(function() {
            if ($(this).val().trim() !== '') {
                filledCount++;
            }
        });
        
        var $display = $section.find('.serial-count-display');
        $display.text(filledCount + ' of ' + qty);
        $display.removeClass('complete incomplete');
        
        if (filledCount === qty && qty > 0) {
            $display.addClass('complete');
        } else if (filledCount > 0 && filledCount < qty) {
            $display.addClass('incomplete');
        }
    }
    
    // Duplicate serial detection on blur/change (allow typing longer values)
    $(document).on('blur change', '.part-serial-input', function() {
        var $this = $(this);
        var val = ($this.val() || '').trim();
        if (val === '') { $this.removeClass('is-invalid'); return; }
        var lower = val.toLowerCase();
        var duplicateFound = false;
        $('.part-serial-input').not($this).each(function() {
            if ($(this).val().trim().toLowerCase() === lower) {
                duplicateFound = true;
                return false;
            }
        });
        if (duplicateFound) {
            toastr.error('This serial number is already used in another field.');
            $this.addClass('is-invalid');
            // Clear only after user finishes typing (on blur/change)
            $this.val('');
            $this.focus();
            updateSerialCount($this.data('row-index'));
        } else {
            $this.removeClass('is-invalid');
        }
    });

    // Save all serials
    $(document).on('click', '#btn_save_all_serials', function() {
        // final duplicate check before saving (check across ALL rows)
        var seen = {};
        var dup = false;
        $('.part-serial-input').each(function() {
            var v = $(this).val().trim();
            if (!v) return;
            var key = v.toLowerCase();
            if (seen[key]) { dup = true; return false; }
            seen[key] = true;
        });
        if (dup) {
            toastr.error('Duplicate serial numbers found across rows. Remove duplicates before saving.');
            return;
        }

        var allSerialsSummary = [];
        
        $('.part-serial-section').each(function() {
            var $section = $(this);
            var rowIndex = $section.data('row-index');
            var serials = [];
            
            $section.find('.part-serial-input').each(function() {
                var val = $(this).val().trim();
                if (val !== '') {
                    serials.push(val);
                }
            });
            
            // Store as JSON in hidden input using row_index
            $('#serial_data_row_' + rowIndex).val(JSON.stringify(serials));
            
            // Build summary (serials only)
            if (serials.length > 0) {
                allSerialsSummary.push(serials.join(', '));
            }
        });
        
        // Update device_serial field with summary
        $('#device_serial').val(allSerialsSummary.join(' | '));
        
        $('#DeviceSerialModal').modal('hide');
        // toastr.success('Serial numbers saved successfully');
    });
    
    // Auto-update qty in modal when table qty changes
    var qtyChangeTimeout;
    $(document).on('change', 'input[name="qty[]"]', function() {
        var $qtyInput = $(this);
        var newQty = parseInt($qtyInput.val()) || 0;
        var $row = $qtyInput.closest('tr');
        var rowIndex = $row.index();
        
        // Update the corresponding hidden input's data-qty
        var $storage = $('#serial_data_row_' + rowIndex);
        if ($storage.length > 0) {
            $storage.attr('data-qty', newQty);
            
            // If modal is open, refresh it after a short delay
            clearTimeout(qtyChangeTimeout);
            qtyChangeTimeout = setTimeout(function() {
                if ($('#DeviceSerialModal').hasClass('show')) {
                    populateSerialModal();
                }
            }, 500);
        }
    });
        var $qtyInput = $(this);
        
        clearTimeout(qtyChangeTimeout);
        qtyChangeTimeout = setTimeout(function() {
            // Try to find matching product ID from the row
            var $row = $qtyInput.closest('tr');
            var $select = $row.find('select[name="part_number[]"]');
            
            if ($select.length) {
                var productId = $select.val();
                var newQty = parseInt($qtyInput.val()) || 0;
                
                // Find the part_number text from the select option
                var partNumber = $select.find('option:selected').text().split(' - ')[0].trim();
                var partKey = partNumber.replace(/ /g, '_');
                
                // Update hidden storage data-qty
                var $storage = $('#serial_data_' + partKey);
                if ($storage.length) {
                    $storage.attr('data-qty', newQty);
                    
                    // If modal is open, refresh it
                    if ($('#DeviceSerialModal').is(':visible')) {
                        populateSerialModal();
                    }
                }
            }
        }, 500);
    });
    

</script>



<div class="modal side-panel  fade" id="professionalservice_popup" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg draggable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title ps-0">Professional Service</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-lg-12 p-0">
                                    <div class="">
                                        <table  class="table table-hover" id="long-list" style="table-layout: fixed;width:100%">
                                            <thead>
                                                <tr >
                                                    <th style="width:15px"><input type="checkbox" id="po_check_all_pro" onclick="po_check_fun_pro()" checked/>
                                                    <th style="width:15px">No</th>
                                                    <script>
                                                        function po_check_fun_pro(){
                                                            if($("#po_check_all_pro").prop('checked') == true){
                                                                $('.po_check_pro').prop('checked', true);
                                                            } else{
                                                                $('.po_check_pro').prop('checked', false);
                                                            }
                                                        }
                                                    </script>
                                                    </th>
                                                    <th style="width:90px"><?php echo app('translator')->getFromJson('Part No'); ?></th>
                                                    <th style="width:100px"><?php echo app('translator')->getFromJson('Description'); ?></th>
                                                    <th style="width:30px" class="text-center"><?php echo app('translator')->getFromJson('Qty'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                               
                    <?php if(count($quotationitems) > 0): ?>
                        <?php $__empty_1 = true; $__currentLoopData = $quotationitems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>   
                             <tr>
                                    <td>
                                        <input type="checkbox" class="po_check_pro" name="po_check_pro[]" value="<?php echo e($item2->id); ?>" checked />
                                    </td>
                                    <td><?php echo e($loop->iteration); ?></td>
                                    <td style="width:90px; word-wrap: break-word;">
                                        <?php echo e($item2->productname ? $item2->productname->part_number : ''); ?>

                                    </td>
                                    <td style="width:100px; word-wrap: break-word;">
                                        <?php echo e($item2->productname ? $item2->productname->description : ''); ?>

                                    </td>
                                    <td style="width:30px" class="text-center">
                                        <?php echo e($item2->qty); ?>

                                    </td>
                                </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

                            <tr>
                                <td colspan="4" class="text-center">No items found.</td>
                            </tr>

                        <?php endif; ?>
                    <?php endif; ?>

                                             
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        <div class="modal-footer d-flex justify-content-center p-0">
    <button type="button" class="btn btn-light add-btn ms-2" id="proffesional_service_submit_btn"> 
        <i class="ico icon-outline-bookmark-opened text-success"></i> Submit
    </button>
</div>

                            
                       
                        
                        
                        </div>
                    </div>
                </div>
            </div>
        </div>


 

      <!-- Modal Support-->
    <div class="modal fade" id="ModalExcelQuote" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" style="">
                     
            <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-quote-upload-excel-quote-edit', 'method' => 'POST', 'id' => 'crm-quote-upload-excel-quote-edit'])); ?>

             
              
                <input type="hidden" id="excel_deal_id" name="excel_deal_id" value="<?php echo e($edit->id); ?>" />
                <input type="hidden" id="excel_cust_id" name="excel_cust_id" value="<?php echo e($edit->cust_id); ?>" />
                <input type="hidden" id="excel_vat" name="excel_vat" value="<?php echo e(@$edit->customername->vat_percentage ?? 0); ?>" />
               
           
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">Quotation Excel Import</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
           
                <script>
                    function add_excel_data()
                    {
                        $('#excel_company_id').val($('#company_id').val());
                        $('#excel_currency_id').val($('#currency_id').val());
                        $('#excel_customer_type').val($('#customer_type').val());
                        $('#excel_quote_validity').val($('#quote_validity').val());
                        $('#excel_payment_terms').val($('#payment_terms').val());
                        $('#excel_delivery_date').val($('#delivery_date1').val());
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
                                <button type="button" onclick="readExcel()" class="btn btn-light text-success">Preview</button>
                                
                                
                            </div>
                            <div class="col-auto">
                                (<a href="<?php echo e(url('public/uploads/product_upload/quotation_sample_format.csv')); ?>"
                                    target="_blank">Sample File</a>)
                            </div>
                              <div class="col-md-12 mt-2">
                                <table id="excel-table" class="table table-bordered table-striped" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="width:220px;">Part No</th>
                                            <th>Description</th>
                                            <th style="width:100px;" class="text-end">Cost</th>
                                            <th style="width:70px;">Qty</th>
                                            <th style="width:100px;" class="text-end">Unit Price</th>
                                            <th style="width:100px;" class="text-end">Discount</th>
                                            <th style="width:100px;" class="text-end">VAT</th>
                                            <th style="width:50px;" class="text-end"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data will be inserted here -->
                                    </tbody>
                                </table>
                              </div>
                        </div>

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
                                                  descriptionInput.value = (row[1] || '').toString().trim();
                                                descriptionInput.classList.add('form-control');
                                                descriptionCell.appendChild(descriptionInput);

                                                // Cost (Right-aligned)
                                                var costCell = newRow.insertCell(2);
                                                var costInput = document.createElement('input');
                                                costInput.type = 'text'; // Change to text input
                                                costInput.name = 'excel_cost[]';
                                                costInput.value = row[2];
                                                costInput.classList.add('text-end');
                                                costInput.classList.add('form-control');
                                                costCell.appendChild(costInput);

                                                // Qty
                                                var qtyCell = newRow.insertCell(3);
                                                var qtyInput = document.createElement('input');
                                                qtyInput.type = 'text'; // Change to text input
                                                qtyInput.name = 'excel_qty[]';
                                                qtyInput.value = row[3];
                                                qtyInput.classList.add('form-control');
                                                qtyCell.appendChild(qtyInput);

                                                // Unit Price (Right-aligned)
                                                var unitPriceCell = newRow.insertCell(4);
                                                var unitPriceInput = document.createElement('input');
                                                unitPriceInput.type = 'text'; // Change to text input
                                                unitPriceInput.name = 'excel_unit_price[]';
                                                unitPriceInput.value = row[4];
                                                unitPriceInput.classList.add('text-end');
                                                unitPriceInput.classList.add('form-control');
                                                unitPriceCell.appendChild(unitPriceInput);

                                                // Discount (Right-aligned)
                                                var discountCell = newRow.insertCell(5);
                                                var discountInput = document.createElement('input');
                                                discountInput.type = 'text'; // Change to text input
                                                discountInput.name = 'excel_discount[]';
                                                discountInput.value = row[5];
                                                discountInput.classList.add('text-end');
                                                discountInput.classList.add('form-control');
                                                discountCell.appendChild(discountInput);

                                                // VAT (Right-aligned)
                                                var vatCell = newRow.insertCell(6);
                                                var vatInput = document.createElement('input');
                                                vatInput.type = 'text'; // Change to text input
                                                vatInput.name = 'vat_excel[]';
                                                vatInput.value = row[6];
                                                vatInput.classList.add('text-end');
                                                vatInput.classList.add('form-control');
                                                vatCell.appendChild(vatInput);

                                                var deleteCell = newRow.insertCell(7); // Last cell for delete button
                                                var deleteButton = document.createElement('button');
                                                deleteButton.type = 'button'; // Make sure the button doesn't submit a form
                                                
                                              deleteButton.classList.add('btn-sm', 'btn-light');
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
                  </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-light add-btn ms-2">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                    </button>
                </div>
               
            </div>
             <?php echo e(Form::close()); ?>

        </div>
    </div>
    <!-- Modal Support-->


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const referenceInput = document.getElementById('terms_and_condition');
        const narrationTextarea = document.getElementById('narrationTextarea');
        const insertButton = document.getElementById('insertNarration');
        const narrationModal = document.getElementById('narrationModal');

        // Pre-fill textarea when modal opens
        narrationModal.addEventListener('shown.bs.modal', () => {
            narrationTextarea.value = referenceInput.value;
            setTimeout(() => $('#narrationTextarea').focus(), 500);
        });

        // On insert button click, update input and close modal
        insertButton.addEventListener('click', () => {
            referenceInput.value = narrationTextarea.value;
            bootstrap.Modal.getInstance(narrationModal).hide();
        });
    });
</script>

 <script>

    $(document).ready(function () {
        $(document).on("change", "#delivery_company", function () {
            var name = $("#delivery_company").val();
            selectedDeliveryAddressId = '';
            get_cust_name2(name);
            fetchCustomerAddresses(name);
        });

        var _custAddressMap = {};
        var selectedDeliveryAddressId = <?php echo json_encode(old('delivery_address_select', $edit->delivery_address_select ?? ''), 512) ?>;

        function fetchCustomerAddresses(custId) {
            var $select = $('#delivery_address_select');
            $select.empty().append('<option value="">-Select Address-</option>');
            _custAddressMap = {};
            if (!custId) return;
            $.ajax({
                url: "<?php echo e(URL::to('crm-deals-customer-addresses')); ?>/" + custId,
                type: "GET",
                cache: false,
                success: function(response) {
                    if (response && response.length > 0) {
                        $.each(response, function(i, addr) {
                            _custAddressMap[addr.id] = addr;
                            var label = '';
                            if (addr.address) label += addr.address;
                            if (addr.city) label += (label ? ', ' : '') + addr.city;
                            if (addr.state_name) {
                                label += (label ? ', ' : '') + addr.state_name;
                            } else if (addr.state) {
                                label += (label ? ', ' : '') + addr.state;
                            }
                            if (addr.country_name) {
                                label += (label ? ', ' : '') + addr.country_name;
                            } else if (addr.country) {
                                label += (label ? ', ' : '') + addr.country;
                            }
                            var addressType = (addr.is_shipping == 1 || addr.is_shipping === '1') ? 'Shipping' : 'Billing';
                            if (!label) {
                                label = addressType + ' Address ' + (i + 1);
                            } else {
                                label += ' (' + addressType + ')';
                            }
                            $select.append('<option value="' + addr.id + '">' + label + '</option>');
                        });
                        if (selectedDeliveryAddressId) {
                            $select.val(selectedDeliveryAddressId);
                            if ($select.hasClass('select2-hidden-accessible')) {
                                $select.trigger('change.select2');
                            } else {
                                $select.trigger('change');
                            }
                        } else if ($select.hasClass('select2-hidden-accessible')) {
                            $select.trigger('change.select2');
                        }
                    }
                }
            });
        }

        $(document).on("change", "#delivery_address_select", function () {
            var addrId = $(this).val();
            if (!addrId || !_custAddressMap[addrId]) return;
            var addr = _custAddressMap[addrId];
            $('#delivery_address1').val(addr.address || '');
            $('#delivery_address2').val(addr.address2 || '');
            $('#delivery_city').val(addr.city || '');
            $('#delivery_area1').val(addr.area || '');
            $('#delivery_building').val(addr.building_name || '');
            $('#delivery_flat_office_no').val(addr.flat_office_no || '');
            $('#delivery_zip_code').val(addr.zip_code || '');
            if (addr.country) {
                $('#country_n_e').val(addr.country).trigger('change.select2');
            }
            if (addr.state) {
                $('#state_n_e').val(addr.state).trigger('change.select2');
            }
        });

        function get_cust_name2(name) {
            $("#loading_bg").css("display", "block");
            var action = "<?php echo e(URL::to('crm-deals-customername')); ?>";
            $.ajax({
                url: action,
                type: "GET",
                data: {
                    _token: '<?php echo e(csrf_token()); ?>',
                    name: name,
                },
                cache: false,
                success: function(dataResult) {
                    console.log(dataResult);
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var len = 0;
                        if(dataResult['data'] != null){
                            len = dataResult['data'].length;
                        }
                        if(len > 0){
                            for(var i=0; i<len; i++){
                                var name = dataResult['data'][i].first_name +' '+ dataResult['data'][i].last_name;
                                $("#delivery_name").val(name.replace('null ','').replace('null',''));
                                $("#delivery_number").val(dataResult['data'][i].mobile);
                                $("#delivery_email").val(dataResult['data'][i].email);
                                // $("#delivery_address1").val(dataResult['data'][i].address);
                                // $("#delivery_address2").val(dataResult['data'][i].address2);

                                $('#delivery_area1').val(dataResult['data'][i].area);
                                $('#delivery_building').val(dataResult['data'][i].building_name);
                                $('#delivery_flat_office_no').val(dataResult['data'][i].flat_office_no);

                                
                                
                                $("#delivery_city").val(dataResult['data'][i].city);
                                $("#delivery_zip_code").val(dataResult['data'][i].zip_code);
                                $("#country_n_e").val(dataResult['data'][i].country_id);
                                $("#state_n_e").val(dataResult['data'][i].state_id);

                                // Tell Select2 to refresh its display without firing 'change'
                                $("#country_n_e").trigger('change.select2');
                                $("#state_n_e").trigger('change.select2');
                            
                                
                            }
                        }
                        else{
                            $("#delivery_name").val('');
                            $("#delivery_number").val('');
                            $("#delivery_email").val('');
                            $("#cust_email").val('');
                            $("#delivery_address1").val('');
                            $("#delivery_address2").val('');
                            $("#delivery_city").val('');
                            $("#delivery_zip_code").val('');
                            $("#state_n_e").val('');
                            $("#country_n_e").val('');
                        }
                        $("#loading_bg").css("display", "none");
                }
            });
        }

        // Load addresses immediately if the delivery customer is already set
        var initialCustomerId = $('#delivery_company').val();
        if (initialCustomerId) {
            fetchCustomerAddresses(initialCustomerId);
        }
        });
        </script>



<script>
$(function() {
    // Cache selectors for performance
    const $id1 = $('#terms_and_condition');
    const $id2 = $('#payment_terms');

    // Trimmed values to ignore spaces
    const val1 = $.trim($id1.val());
    const val2 = $.trim($id2.val());

    // Check both
    if (val1 && val2) {
        console.log(' Both inputs have values:', val1, val2);
       
    } else if (val1 || val2) {
        console.log(' One of the inputs has a value.');
       
    } else {
        console.log(' Both inputs are empty.');
        change_cust_id()
         var $txt = $('#company option:selected').text();
        var $tc = "1. Quote/Order will be subject to approval of payment/credit terms by our finance.\n" +
                  "2. Please mention our Quotation No. in your Purchase Order\n" +
                  "3. In case of non-availability of quote products " + $txt + 
                  " reserves the right to supply a functionally similar or better product.";
        $id1.val($tc);
       
    }

});
</script>


             <script>
document.addEventListener("DOMContentLoaded", function () {

    // --- Restore last active tab ---
    let lastTab = localStorage.getItem("active-dealedit-tab");
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
            localStorage.setItem("active-dealedit-tab", e.target.getAttribute("data-bs-target"));
        });
    });

});
</script>



          <script>
$(document).ready(function () {
    function toggleFollowupField() {
        const stageVal = $('#stage').val();
        if (stageVal === '1' || stageVal === '2') {
            $('#followup_date_div').show();
            $('#followup_date').prop('required', true);
        } else {
            $('#followup_date_div').hide();
            $('#followup_date').prop('required', false);
        }
    }

    // Run on load (important for edit forms)
    toggleFollowupField();

    // Run on change
    $('#stage').on('change', toggleFollowupField);
});
flatpickr(".date-time-picker", {
  enableTime: true,
  dateFormat: "d/m/Y h:i K", // dd/mm/yyyy hh:mm AM/PM
  allowInput: true,          // allows typing
  time_24hr: false,          // 12-hour format with AM/PM
  minuteIncrement: 1         // finer control
});
</script>

<script src="<?php echo e(asset('public/js/form-validation-toastr.js')); ?>"></script>
<script>
$(document).ready(function() {
    // Initialize form validation for crm-deals-form
    FormValidator.init('crm-deals-form', {
        showAllErrors: true,
        scrollToFirst: true,
        highlightFields: true,
        toastrPosition: 'toast-top-right',
        toastrTimeout: 6000
    });
});
</script>

      <div class="modal fade" id="reservedStockListModal" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" style="top:10%;left:10%;max-width:100rem">
            <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => false, 'url' => 'store-reserve-qty', 'id' => 'reserve_stock_form', 'method' => 'POST'])); ?>


            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="reservedStockListTitle"></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body p-0">
                            <input type="hidden" id="reserved_stock_partno" value="">
                            <input type="hidden" id="reserved_stock_balance_qty" value="">
                            <input type="hidden" id="reserved_stock_part_number" value="">
                            <div class="table-responsive">
                                <table class="table table-hover" id="long-list" style="table-layout: fixed;width:100%">
                                    <thead>
                                        <tr>
                                            <th width="7%" class="text-center">Doc Number</th>
                                            <th width="7%" class="text-center">Deal Code</th>
                                            <th width="19%">Customer Name</th>
                                            <th width="15%">Sales Person</th>
                                            <th width="5%" class="text-center">Res. Qty</th>
                                            <th width="8%" class="text-center">Res. Date</th>
                                            <th width="15%" class="text-start">Created By</th>
                                            <th width="15%" class="text-start">Updated By</th>
                                            <!-- <th width="7%" class="text-center">Actions</th> -->

                                        </tr>
                                    </thead>
                                    <tbody id="reservedStockTableBody">
                                        <!-- Dynamic content will be loaded here -->
                                    </tbody>
                                </table>
                            </div>

                            <div class="row" id="noDataRow" style="display: none;">
                                <div class="col-md-12 text-center">
                                    <p class="text-muted">No reserved stock found for this item.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">

                </div>
            </div>
            <?php echo e(Form::close()); ?>


        </div>
    </div>

    

<script>
        

     function openReservedStockListModal(el) {
    // Show loading indicator
    $('#loading_bg').show();

    // data-stock is ALREADY an object
    var value = $(el).data('stock');
    //if value dont exist return
    if (!value) {
        console.error('No stock data found on element:', el);
        $('#loading_bg').hide();
        return;
    }
    var balance_qty = $(el).data('balance');

    console.log('Opening reserved stock list for:', value);

    $('#reservedStockListModalLabel').text('Reserved Stock - ' + value.part_number);
    $('#reserved_stock_partno').val(value.stockid);
    $('#reserved_stock_balance_qty').val(balance_qty);
    $('#reserved_stock_part_number').val(value.part_number);

    // Load reserved stock data via AJAX
    loadReservedStockData(value.stockid, value.part_number, balance_qty);

    $('#reservedStockListModal').modal('show');
}


        function loadReservedStockData(stockId, partNumber, balance_qty) {
            $('#reservedStockTableBody').html('<tr><td colspan="9" class="text-center">Loading...</td></tr>');

            $('#reservedStockListTitle').text('Reserved Stock - ' + partNumber);

            $.ajax({
                url: "<?php echo e(URL::to('get-reserved-stock-list')); ?>",
                type: "GET",
                data: {
                    _token: '<?php echo e(csrf_token()); ?>',
                    stock_id: stockId,
                    part_number: partNumber
                },
                success: function(response) {
                    console.log("response", response);
                    if (response.success && response.data.length > 0) {
                        let tableBody = '';
                        response.data.forEach(function(item) {
                            tableBody += `
                                <tr>
                                    <td class="text-center" style="padding: 1px 3px;">${item.doc_number}</td>
                                    <td class="text-center" style="padding: 1px 3px;">${item.deal_id || '-'}</td>
                                    <td style="padding: 1px 3px;">${item.customer_name}</td>
                                    <td style="padding: 1px 3px;">${item.sales_person || 'N/A'}</td>
                                    <td style="padding: 1px 3px;" class="text-center">${item.reserved_qty}</td>
                                    <td style="padding: 1px 3px;" class="text-center">${item.reserve_date}</td>
                                    <td style="padding: 1px 3px;" class="text-start">${item.created_by} ${item.created_at} </td>
                                    <td style="padding: 1px 3px;" class="text-start">${item.updated_by} ${item.updated_at}</td>
                                   
                                </tr>
                            `;
                        });
                        $('#reservedStockTableBody').html(tableBody);
                    } else {
                        $('#reservedStockTableBody').html(
                            '<tr><td colspan="9" class="text-center text-muted">No reserved stock found</td></tr>'
                        );
                    }
                    $('#loading_bg').hide(); // Hide loader after data is loaded
                },
                error: function() {
                    $('#reservedStockTableBody').html(
                        '<tr><td colspan="5" class="text-center text-danger">Error loading data</td></tr>');
                    $('#loading_bg').hide(); // Hide loader even on error
                }
            });
        }

       </script>





<?php } catch (\Exception $e) { ?> <?php echo e($e); ?> <?php  } ?>
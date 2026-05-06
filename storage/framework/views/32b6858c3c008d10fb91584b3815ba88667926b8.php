<!-- Modal Account-->
<div class="modal side-panel fade" id="modalAccount" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h4 class="modal-title" id="exampleModalLongTitle">Accounts Approval</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
          <div class="modal-body">
            
            <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-approval-accounts','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-deal-track-approval-accounts'])); ?>

            
            <input type="hidden" name="owner_id" value="<?php echo e(@$del->owner); ?>" />
            <input type="hidden" name="owner_name" value="<?php echo e(@$del->ownername->full_name); ?>" />
            <input type="hidden" name="owner_email" value="<?php echo e(@$del->ownername->email); ?>" />
        
            <div class="row">
            <div class="col-lg-4 mb-1">
                <div class="form-check-label">Customer Status
                    <select class="form-control" name="customer_status" required>
                      <option value="">-Select-</option>
                      <option value="1" <?php if(count($accounts)>0): ?> <?php if($accounts[0]->customer_status==1): ?> selected <?php endif; ?> <?php else: ?> selected <?php endif; ?>>Approved</option>
                      <option value="2" <?php if(count($accounts)>0): ?> <?php if($accounts[0]->customer_status==2): ?> selected <?php endif; ?> <?php else: ?> <?php endif; ?>>Disapproved</option>
                    </select>
                  </div>
            </div>
            <div class="col-lg-4 mb-1">
                <div class="form-check-label">Credit Limit
                    <select class="form-control" name="credit_limit" required>
                      <option value="">-Select-</option>
                      <option value="1" <?php if(count($accounts)>0): ?> <?php if($accounts[0]->credit_limit==1): ?> selected <?php endif; ?> <?php else: ?> selected <?php endif; ?>>Approved</option>
                      <option value="2" <?php if(count($accounts)>0): ?> <?php if($accounts[0]->credit_limit==2): ?> selected <?php endif; ?> <?php endif; ?>>Disapproved</option>
                    </select>
                  </div>
            </div>
            <div class="col-lg-4 mb-1">
                <div class="form-check-label">Payment Terms
                    <select class="form-control" name="payment_terms" required>
                      <option value="">-Select-</option>
                      <option value="1" <?php if(count($accounts)>0): ?> <?php if($accounts[0]->payment_terms==1): ?> selected <?php endif; ?> <?php else: ?> selected <?php endif; ?>>Approved</option>
                      <option value="2" <?php if(count($accounts)>0): ?> <?php if($accounts[0]->payment_terms==2): ?> selected <?php endif; ?> <?php endif; ?>>Disapproved</option>
                    </select>
                  </div>
            </div>
            <div class="col-lg-4 mb-1">
                <div class="form-check-label">Overdue Payment
                    <select class="form-control" name="pending_payment" required>
                      <option value="">-Select-</option>
                      <option value="2" <?php if(count($accounts)>0): ?> <?php if($accounts[0]->pending_payment==2): ?> selected <?php endif; ?> <?php endif; ?>>Yes</option>
                      <option value="1" <?php if(count($accounts)>0): ?> <?php if($accounts[0]->pending_payment==1): ?> selected <?php endif; ?> <?php else: ?> selected <?php endif; ?>>No</option>
                    </select></div>
            </div>
            <div class="col-lg-4 mb-1">
                <div class="form-check-label">Other
                    <select class="form-control" name="other" required>
                      <option value="">-Select-</option>
                      <option value="1" <?php if(count($accounts)>0): ?> <?php if($accounts[0]->other==1): ?> selected <?php endif; ?> <?php else: ?> selected <?php endif; ?>>Approved</option>
                      <option value="2" <?php if(count($accounts)>0): ?> <?php if($accounts[0]->other==2): ?> selected <?php endif; ?> <?php endif; ?>>Disapproved</option>
                    </select>
                  </div>
            </div>
            <div class="col mb-10">
                <div class="no-gutters input-right-icon">
                    <div class="col">
                        <div class="input-effect">
                            <span class="form-label"><?php echo app('translator')->getFromJson('Remarks'); ?><span></span></span>
                            <textarea class="form-control" rows="1" id="remarks"  name="remarks"><?php if(count($accounts)>0): ?> <?php echo e($accounts[0]->remarks); ?> <?php else: ?> <?php echo e(@$deal->paymentterms->title); ?> <?php if(@$deal->payment_terms == 22): ?> <?php echo e(@$deal->payment_terms_txt); ?> <?php endif; ?> <?php endif; ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer m-0 p-0">
          
          <input type="hidden" id="deal_id" name="deal_track_id" value="<?php echo e($deal->id); ?>" />
          <input type="hidden" id="deal_id" name="deal_id" value="<?php echo e($deal->deal_id); ?>" />
<button type="submit" class="btn btn-light add-btn ms-2" id="btnSubmit">
	<i class="ico icon-outline-bookmark-opened text-success"></i> Submit
</button>
        </div>
        <?php echo e(Form::close()); ?>

        
		      </div>
        </div>
      </div>
    </div>
<!-- Modal Account-->

<!-- Modal Sales-->
<div class="modal side-panel fade" id="modalSales" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg draggable">
        <div class="modal-content">
            <div class="modal-header m-0"><h4 class="modal-title" id="exampleModalLongTitle">Sales Approval</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
          <div class="modal-body">
            
            <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-approval-sales','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-deal-track-approval-sales'])); ?>

            
            <input type="hidden" name="owner_id" value="<?php echo e(@$del->owner); ?>" />
            <input type="hidden" name="owner_name" value="<?php echo e(@$del->ownername->full_name); ?>" />
            <input type="hidden" name="owner_email" value="<?php echo e(@$del->ownername->email); ?>" />
            
            <div class="row">
            <div class="col-lg-4 mb-1">
                <div class="form-check-label">Margin
                    <select class="form-control" name="margin" required>
                      <option value="">-Select-</option>
                      <option value="1" <?php if(count($sales)>0): ?> <?php if($sales[0]->margin==1): ?> selected <?php endif; ?> <?php else: ?> selected <?php endif; ?>>Approved</option>
                      <option value="2" <?php if(count($sales)>0): ?> <?php if($sales[0]->margin==2): ?> selected <?php endif; ?> <?php endif; ?>>Disapproved</option>
                    </select>
                  </div>
            </div>
            <div class="col-lg-4 mb-1">
                <div class="form-check-label">Stock
                    <select class="form-control" name="stock" required>
                      <option value="">-Select-</option>
                      <option value="1" <?php if(count($sales)>0): ?> <?php if($sales[0]->stock==1): ?> selected <?php endif; ?> <?php else: ?> selected <?php endif; ?>>Approved</option>
                      <option value="2" <?php if(count($sales)>0): ?> <?php if($sales[0]->stock==2): ?> selected <?php endif; ?> <?php endif; ?>>Disapproved</option>
                    </select>
                  </div>
            </div>
            <div class="col-lg-4 mb-1">
                <div class="form-check-label">Purchase Quote
                    <select class="form-control" name="purcease_quote" required>
                      <option value="">-Select-</option>
                      <option value="1" <?php if(count($sales)>0): ?> <?php if($sales[0]->purcease_quote==1): ?> selected <?php endif; ?> <?php else: ?> selected <?php endif; ?>>Approved</option>
                      <option value="2" <?php if(count($sales)>0): ?> <?php if($sales[0]->purcease_quote==2): ?> selected <?php endif; ?> <?php endif; ?>>Disapproved</option>
                    </select>
                  </div>
            </div>
            <div class="col-lg-4 mb-1">
                <div class="form-check-label">Other
                    <select class="form-control" name="other" required>
                      <option value="">-Select-</option>
                      <option value="1" <?php if(count($sales)>0): ?> <?php if($sales[0]->other==1): ?> selected <?php endif; ?> <?php else: ?> selected <?php endif; ?>>Approved</option>
                      <option value="2" <?php if(count($sales)>0): ?> <?php if($sales[0]->other==2): ?> selected <?php endif; ?> <?php endif; ?>>Disapproved</option>
                    </select>
                  </div>
            </div>
         
            <div class="col-lg-4 mb-1">
                <div class="form-check-label">Purchase Approval
                    <select class="form-control" name="purchase_approval" required>
                <option value="1"
    <?php if(isset($deal->purchease_approval)): ?>
        <?php if($deal->purchease_approval == 1): ?> selected <?php endif; ?>
    <?php else: ?>
        <?php if(count($sales) > 0 && $sales[0]->purchase_approval == 1): ?> selected <?php endif; ?>
    <?php endif; ?>
>
    Required
</option>

<option value="2"
    <?php if(isset($deal->purchease_approval)): ?>
        <?php if($deal->purchease_approval == 0): ?> selected <?php endif; ?>
    <?php else: ?>
        <?php if(count($sales) > 0 && $sales[0]->purchase_approval == 2): ?> selected <?php endif; ?>
    <?php endif; ?>
>
    Not Required
</option>
    </select>
                  </div>
            </div>
            <div class="col-lg-4 mb-1">
                <div class="form-check-label">Invoice Approval
                    <select class="form-control" name="invoice_approval" required>
                      <option value="1" <?php if(count($sales)>0): ?> <?php if($sales[0]->invoice_approval == 1): ?> selected <?php endif; ?> <?php else: ?> <?php if($deal->invoice_approval == 1): ?> selected <?php endif; ?> <?php endif; ?>>Required</option>
                      <option value="2" <?php if(count($sales)>0): ?> <?php if($sales[0]->invoice_approval == 2): ?> selected <?php endif; ?> <?php else: ?> <?php if($deal->invoice_approval == 0): ?> selected <?php endif; ?> <?php endif; ?>>Not Required</option>
                    </select>
                  </div>
            </div>
            <div class="col-lg-4 mb-1">
                <div class="form-check-label">Delivery Approval
                    <select class="form-control" name="delivery_approval" required>
                      <option value="1" <?php if(count($sales)>0): ?> <?php if($sales[0]->delivery_approval == 1): ?> selected <?php endif; ?> <?php else: ?> <?php if($deal->delivery_approval == 1): ?> selected <?php endif; ?> <?php endif; ?>>Required</option>
                      <option value="2" <?php if(count($sales)>0): ?> <?php if($sales[0]->delivery_approval == 2): ?> selected <?php endif; ?> <?php else: ?> <?php if($deal->delivery_approval == 0): ?> selected <?php endif; ?> <?php endif; ?>>Not Required</option>
                    </select>
                  </div>
            </div>
            <div class="col-lg-4 mb-1">
                <div class="form-check-label">Receivables Approval
                    <select class="form-control" name="receivables_approval" required>
                      <option value="1" <?php if(count($sales)>0): ?> <?php if($sales[0]->receivables_approval == 1): ?> selected <?php endif; ?> <?php else: ?> <?php if($deal->receivables_approval == 1): ?> selected <?php endif; ?> <?php endif; ?>>Required</option>
                      <option value="2" <?php if(count($sales)>0): ?> <?php if($sales[0]->receivables_approval == 2): ?> selected <?php endif; ?> <?php else: ?> <?php if($deal->receivables_approval == 0): ?> selected <?php endif; ?> <?php endif; ?>>Not Required</option>
                    </select>
                  </div>
            </div>

            <div class="col mb-10">
                <div class="no-gutters input-right-icon">
                    <div class="col">
                        <div class="input-effect">
                            <span class="form-label"><?php echo app('translator')->getFromJson('Remarks'); ?><span></span></span>
                            <textarea class="form-control" rows="1" id="remarks"  name="remarks"><?php if(count($sales)>0): ?> <?php echo e($sales[0]->remarks); ?> <?php endif; ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            
            
        </div>
        <div class="modal-footer m-0 p-0">
          
          <input type="hidden" id="deal_id" name="deal_track_id" value="<?php echo e($deal->id); ?>" />
          <input type="hidden" id="deal_id" name="deal_id" value="<?php echo e($deal->deal_id); ?>" />
<button type="submit" class="btn btn-light add-btn ms-2" id="btnSubmit">
	<i class="ico icon-outline-bookmark-opened text-success"></i> Submit
</button>

          
        </div>
        <?php echo e(Form::close()); ?>

        
		      </div>
        </div>
      </div>
    </div>

  
           

<!-- Modal Purchase-->
<div class="modal side-panel fade" id="modalPurchase" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg draggable">
        <div class="modal-content">
            <div class="modal-header m-0"><h4 class="modal-title" id="exampleModalLongTitle">Purchase Approval</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
          <div class="modal-body">
            
            <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-approval-purchease','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-deal-track-approval-purchease'])); ?>

            
            <input type="hidden" name="owner_id" value="<?php echo e(@$del->owner); ?>" />
            <input type="hidden" name="owner_name" value="<?php echo e(@$del->ownername->full_name); ?>" />
            <input type="hidden" name="owner_email" value="<?php echo e(@$del->ownername->email); ?>" />
            
            <div class="row">
            
            <div class="col-lg-4 mb-1">
                <div class="form-check-label">Purchase Status
                    <select class="form-control" id="validation_re" name="validation" required>
                     
                      
                      <option value="1" <?php if(count($purchease)>0): ?> <?php if($purchease[0]->validation==1): ?> selected <?php endif; ?> <?php endif; ?>>Purchase Completed</option>
                      <option value="3" <?php if(count($purchease)>0): ?> <?php if($purchease[0]->validation==3): ?> selected <?php endif; ?> <?php endif; ?>>Under Purchase</option>
                      <option value="4" <?php if(count($purchease)>0): ?> <?php if($purchease[0]->validation==4): ?> selected <?php endif; ?> <?php endif; ?>>Partial Delivery</option>
                      <option value="2" <?php if(count($purchease)>0): ?> <?php if($purchease[0]->validation==2): ?> selected <?php endif; ?> <?php endif; ?>>Disapproved</option>
                    </select>
                  </div>
            </div>
            <script>
              $(function () { $("#validation_re").change(); });
            </script>

         <script>
$(document).ready(function() {

  $('#validation_re').on('change', function() {
    if ($('#validation_re').val() == 1) {
      $('.div_validation_re').css("display", "block");
      $('#div_validation_re2').css("display", "none");
      $('#div_validation_re3').css("display", "none");
      $('#lpo_no_re').prop('required', true);
      $('#ref_supplier_id').prop('required', false);
      $('#cost_of_purchase_re').prop('required', true);
      $('#cost_of_purchase_currency_re').prop('required', true);
      $('#part_no_re').prop('required', false);
      $('#delivery_date_re').prop('required', false);
      $('#div_validation2_re').css("display", "none");
    } 
    else if ($('#validation_re').val() == 3) {
      $('.div_validation_re').css("display", "block");
      $('#div_validation_re2').css("display", "block");
      $('#div_validation_re3').css("display", "block");
      $('#lpo_no_re').prop('required', true);
      $('#ref_supplier_id').prop('required', true);
      $('#cost_of_purchase_re').prop('required', true);
      $('#cost_of_purchase_currency_re').prop('required', true);
      $('#part_no_re').prop('required', true);
      $('#delivery_date_re').prop('required', true);
      $('#div_validation2_re').css("display", "none");
    } 
    else if ($('#validation_re').val() == 4) {
      $('#div_validation2_re').css("display", "block");
      $('.div_validation_re').css("display", "none");
      $('#div_validation_re2').css("display", "none");
      $('#div_validation_re3').css("display", "none");
      $('#lpo_no_re').prop('required', false);
      $('#ref_supplier_id').prop('required', false);
      $('#cost_of_purchase_re').prop('required', false);
      $('#cost_of_purchase_currency_re').prop('required', false);
      $('#part_no_re').prop('required', false);
      $('#delivery_date_re').prop('required', false);
    } 
    else {
      $('.div_validation_re').css("display", "none");
      $('#div_validation_re2').css("display", "none");
      $('#div_validation_re3').css("display", "none");
      $('#lpo_no_re').prop('required', false);
      $('#ref_supplier_id').prop('required', false);
      $('#cost_of_purchase_re').prop('required', false);
      $('#cost_of_purchase_currency_re').prop('required', false);
      $('#part_no_re').prop('required', false);
      $('#delivery_date_re').prop('required', false);
      $('#div_validation2_re').css("display", "none");
    }
  });

  // ✅ Trigger it once on page load
  $('#validation_re').trigger('change');
});
</script>

            <?php
            if (count($po_detail) > 0) {
              $po_no = $po_detail[0]->doc_number;
              $po_no_doc = $po_detail->pluck('doc_number')->implode(',');
              $po_amount = trim($po_detail[0]->amount);
              $po_no_amount = $po_detail->pluck('amount')->implode(',');
          

// Get the first part number for each PO and map to its display name
$first_part_ids = $po_detail->pluck('first_part_number')->filter()->toArray();
$part_number_names = [];
foreach ($first_part_ids as $id) {
  $part_number_names[] = App\SysHelper::getPartNumberById(trim($id));
}

// Join back as a string if you need
$part_number_names_string = implode(', ', $part_number_names);

              

                $amounts = explode(',', $po_no_amount);

    $formatted_list = [];
    foreach ($amounts as $amount) {
        $formatted_list[] = App\SysHelper::com_curr_format(trim($amount), 2, '.', ',');
    }

    
    

    $formatted_amounts = implode(' / ', $formatted_list);

              $po_account_name = $po_detail[0]->account_name;
              $po_currency = $po_detail[0]->currency;
            } else{
              $po_no = $deal->reference_no;
              $po_amount = '';
              $po_account_name = '';
              $po_currency = 0;
            }
            ?>
            
            <div class="col-lg-4 mb-1 div_validation_re" style="display: none;">
              LPO No
              <input type="text" class="form-control primary-input" id="lpo_no_re" name="lpo_no" value="<?php if(count($po_detail)>0): ?> <?php echo e($po_no_doc); ?> <?php endif; ?>"/>
             
        
            </div>


            <div class="col-lg-4 mb-1 div_validation_re" style="display: none;">
            Cost of Purchase
              <input type="text" step="any" class="form-control primary-input" id="cost_of_purchase_re" name="cost_of_purchase" value="<?php if(count($po_detail)>0): ?> <?php echo e($formatted_amounts); ?> <?php endif; ?>"/>
            
        
            </div>

              <div class="col-lg-4 mb-1 div_validation_re" style="display: none;">
          
                  Currency
                  <select class="form-control js-example-basic-single" name="cost_of_purchase_currency" id="cost_of_purchase_currency_re" required>
                    <option value="">-Select-</option>
                    <?php $__currentLoopData = $currencylist; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e(@$value->id); ?>" <?php if(count($purchease)>0): ?> <?php if($purchease[0]->cost_of_purchase_currency == @$value->id): ?> selected <?php endif; ?> <?php else: ?> <?php if($po_currency == @$value->id): ?> selected <?php endif; ?> <?php endif; ?> ><?php echo e(@$value->code); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
        
            </div>

             <div class="col-lg-4 mb-1 div_validation_re" style="display: none;">
                    Supplier Name 
                    <?php
                                  @$supplier_reference_list = @App\SysHelper::get_supplierlist_charofaccounts();
                                  @$vendorIds = !empty(@$check_po) 
                                      ? collect(@$check_po)->pluck('vendors')->filter()->toArray() 
                                      : [];

                                     
                    ?>
                    

                          <select class="form-control js-example-basic-single" name="ref_supplier_id[]" id="ref_supplier_id" multiple>
                            <option value="">-Select-</option>

                            <?php $__currentLoopData = @$supplier_reference_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value->id); ?>"
                                    <?php if(!empty(@$purchease) && @$purchease[0]->ref_supplier_id == $value->id): ?> selected <?php endif; ?>   <?php if(in_array(@$value->id, @$vendorIds)): ?> selected <?php endif; ?>>

                                    <?php echo e(@$value->account_name); ?>

                                    <?php if(App\SysHelper::getCompanyCodeSettings()['is_supplier_code']): ?>
                                        (<?php echo e(@$value->account_code); ?>)
                                    <?php endif; ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    <input type="hidden" class="form-control primary-input" id="supplier_name_re" name="supplier_name" value="<?php if(count(@$purchease)>0): ?> <?php echo e($purchease[0]->supplier_name); ?> <?php else: ?> <?php echo e($po_account_name); ?> <?php endif; ?>"/>
            </div>

            <div class="col-lg-4 mb-1" id="div_validation_re2" style="display: none;">
              Delivery Date
              <input type="text" class="form-control date-picker" id="delivery_date_re" name="delivery_date" value="<?php if(count(@$purchease)>0): ?> <?php echo e(App\SysHelper::normalizeToDmy($purchease[0]->delivery_date)); ?> <?php else: ?> <?php echo e(App\SysHelper::normalizeToDmy($deal->delivery_date)); ?> <?php endif; ?>"/>
            </div>

            <div class="col-lg-4 mb-1" id="div_validation2_re" style="display: none;">
              Partial Delivery Note
              <input type="text" class="form-control" id="partial_delivery_note" name="partial_delivery_note" value="<?php if(count($purchease)>0): ?> <?php echo e($purchease[0]->partial_delivery_note); ?> <?php endif; ?>"/>
            </div>
        
            <div class="col-lg-4 mb-1 div_validation_re" style="display: none;">
              Part No
              <input type="text" class="form-control primary-input" id="part_no_re" name="part_no" value="<?php if(count($po_detail)>0): ?> <?php echo e($part_number_names_string); ?> <?php endif; ?>"/>
            
            </div>

            <div class="col-lg-4 mb-1">
                <div class="form-check-label">Purchase Quote
                    <select class="form-control" name="purchease_quote" required>
                      <option value="">-Select-</option>
                      <option value="1" <?php if(count($purchease)>0): ?> <?php if($purchease[0]->purchease_quote==1): ?> selected <?php endif; ?> <?php else: ?> selected <?php endif; ?>>Approved</option>
                      <option value="2" <?php if(count($purchease)>0): ?> <?php if($purchease[0]->purchease_quote==2): ?> selected <?php endif; ?> <?php endif; ?>>Disapproved</option>
                    </select>
                  </div>
            </div>
            <div class="col-lg-4 mb-1">
                <div class="form-check-label">Quote Request
                    <select class="form-control" name="quote_request" required>
                      <option value="">-Select-</option>
                      <option value="1" <?php if(count($purchease)>0): ?> <?php if($purchease[0]->three_quote_request==1): ?> selected <?php endif; ?> <?php else: ?> selected <?php endif; ?>>Approved</option>
                      <option value="3" <?php if(count($purchease)>0): ?> <?php if($purchease[0]->three_quote_request==3): ?> selected <?php endif; ?> <?php endif; ?>>Not Required</option>
                      <option value="2" <?php if(count($purchease)>0): ?> <?php if($purchease[0]->three_quote_request==2): ?> selected <?php endif; ?> <?php endif; ?>>Disapproved</option>
                    </select>
                  </div>
            </div>

             
                <div class="col-lg-4 mb-1">
                <div class="form-check-label">Choose File
                    <input type="file" class="form-control" id="fileone" name="fileone" >
                  </div>
            </div>
            
          
           
        
            
            <div class="col mb-10">
                <div class="no-gutters input-right-icon">
                    <div class="col">
                        <div class="input-effect">
                            <span class="form-label"><?php echo app('translator')->getFromJson('Remarks'); ?><span></span></span>
                            <textarea class="form-control" rows="1" id="remarks"  name="remarks"><?php if(count($purchease)>0): ?> <?php echo e($purchease[0]->remarks); ?> <?php endif; ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer m-0 p-0">
          
          <input type="hidden" id="deal_id" name="deal_track_id" value="<?php echo e($deal->id); ?>" />
          <input type="hidden" id="deal_id" name="deal_id" value="<?php echo e($deal->deal_id); ?>" />
<button type="submit" class="btn btn-light add-btn ms-2" id="btnSubmit">
	<i class="ico icon-outline-bookmark-opened text-success"></i> Submit
</button>
          
        </div>
        <?php echo e(Form::close()); ?>

        
		      </div>
        </div>
      </div>
    </div>
<!-- Modal Sales-->

<!-- Modal Invoice-->
<div class="modal side-panel fade" id="modalInvoice" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header m-0"><h4 class="modal-title" id="exampleModalLongTitle">Invoice Approval</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
          <div class="modal-body">
            
            <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-approval-invoice','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-deal-track-approval-invoice'])); ?>

            
            <input type="hidden" name="owner_id" value="<?php echo e(@$del->owner); ?>" />
            <input type="hidden" name="owner_name" value="<?php echo e(@$del->ownername->full_name); ?>" />
            <input type="hidden" name="owner_email" value="<?php echo e(@$del->ownername->email); ?>" />
            
            <div class="row">
            <div class="col-lg-4 mb-1">
                <div class="form-check-label">Delivery Advice
                    <select class="form-control" name="delivery_advice" required>
                      <option value="1" <?php if(count($invoice)>0): ?> <?php if($invoice[0]->delivery_advice == 1): ?> selected <?php endif; ?> <?php endif; ?>>Approved</option>
                      <option value="2" <?php if(count($invoice)>0): ?> <?php if($invoice[0]->delivery_advice == 2): ?> selected <?php endif; ?> <?php endif; ?>>Disapproved</option>
                    </select>
                  </div>
            </div>
            <div class="col-lg-4 mb-1">
                <div class="form-check-label">Validation
                    <select class="form-control" name="validation" required>
                      <option value="1" <?php if(count($invoice)>0): ?> <?php if($invoice[0]->validation == 1): ?> selected <?php endif; ?> <?php endif; ?>>Approved</option>
                      <option value="2" <?php if(count($invoice)>0): ?> <?php if($invoice[0]->validation == 2): ?> selected <?php endif; ?> <?php endif; ?>>Disapproved</option>
                    </select>
                  </div>
            </div>
            <div class="col-lg-4 mb-1">
                <div class="form-check-label">Hold
                    <select class="form-control" name="hold" required>
                      <option value="1" <?php if(count($invoice)>0): ?> <?php if($invoice[0]->hold == 1): ?> selected <?php endif; ?> <?php endif; ?>>Approved</option>
                      <option value="2" <?php if(count($invoice)>0): ?> <?php if($invoice[0]->hold == 2): ?> selected <?php endif; ?> <?php endif; ?>>Disapproved</option>
                      <option value="3" <?php if(count($invoice)>0): ?> <?php if($invoice[0]->hold == 3): ?> selected <?php endif; ?> <?php endif; ?>>Pending</option>
                    </select>
                  </div>
            </div>
            <div class="col-lg-4 mb-1">
                <div class="form-check-label">Print
                    <select class="form-control" name="print" required>
                      <option value="1" <?php if(count($invoice)>0): ?> <?php if($invoice[0]->print == 1): ?> selected <?php endif; ?> <?php endif; ?>>Approved</option>
                      <option value="2" <?php if(count($invoice)>0): ?> <?php if($invoice[0]->print == 2): ?> selected <?php endif; ?> <?php endif; ?>>Disapproved</option>
                      <option value="3" <?php if(count($invoice)>0): ?> <?php if($invoice[0]->print == 3): ?> selected <?php endif; ?> <?php endif; ?>>Pending</option>
                    </select>
                  </div>
            </div>
            <?php
              if(count($invo_detail)>0){
                $inv_no = $invo_detail[0]->doc_number;
              } else {
                $inv_no = '';
              }
            ?>
            <div class="col-lg-4 mb-10">
                <div class="form-check-label">Invoice No
                  <?php
                    @$invoice_si_list = @$list_sales_invoice->pluck('doc_number')->implode(',');
                  ?>
                    <input type="text" class="form-control" id="invoice_no" name="invoice_no" required value="<?php if(count($list_sales_invoice)>0): ?> <?php echo e(@$invoice_si_list); ?> <?php endif; ?>" />
                  </div>
            </div>
            <div class="col-lg-4 mb-1">
                <div class="form-check-label">Partial Invoice
                    <select class="form-control" name="partial_invoice" id="partial_invoice_re">
                      <option value="0" <?php if(count($invoice)>0): ?> <?php if($invoice[0]->partial_invoice == 0): ?> selected <?php endif; ?> <?php endif; ?>>No</option>
                      <option value="1" <?php if(count($invoice)>0): ?> <?php if($invoice[0]->partial_invoice == 1): ?> selected <?php endif; ?> <?php endif; ?>>Yes</option>
                    </select>
                  </div>
            </div>
            <div class="col-lg-4 mb-1" id="partial_invoice_amount_div_re" style="display: none;">
                <div class="form-check-label">Partial Invoice Amount
                    <input type="number" step="any" class="form-control" name="partial_invoice_amount" id="partial_invoice_amount_re" value="<?php if(count($invoice)>0): ?> <?php echo e($invoice[0]->partial_invoice_amount); ?> <?php endif; ?>"/>
                  </div>
            </div>
            <script>
              $(function () { $("#partial_invoice_re").change(); });
            </script>
            <script>
              $('#partial_invoice_re').on('change', function(e) {
                if ($('#partial_invoice_re').val() == 1) {
                  $('#partial_invoice_amount_div_re').css("display", "block");
                  $('#partial_invoice_amount_re').prop('required', true);
                } else {
                  $('#partial_invoice_amount_div_re').css("display", "none");
                  $('#partial_invoice_amount_re').prop('required', false);
                }
              });
              </script>
          
            <div class="col mb-10">
                <div class="no-gutters input-right-icon">
                    <div class="col">
                        <div class="input-effect">
                            <span class="form-label"><?php echo app('translator')->getFromJson('Remarks'); ?><span></span></span>
                            <textarea class="form-control" rows="1" id="remarks"  name="remarks"><?php if(count($invoice)>0): ?> <?php echo e($invoice[0]->remarks); ?> <?php endif; ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="modal-footer m-0 p-0">
          
          <input type="hidden" id="deal_id" name="deal_track_id" value="<?php echo e($deal->id); ?>" />
          <input type="hidden" id="deal_id" name="deal_id" value="<?php echo e($deal->deal_id); ?>" />
<button type="submit" class="btn btn-light add-btn ms-2" id="btnSubmit">
	<i class="ico icon-outline-bookmark-opened text-success"></i> Submit
</button>

        </div>
        <?php echo e(Form::close()); ?>

        
		      </div>
        </div>
      </div>
    </div>
<!-- Modal Invoice-->

<!-- Modal Delivery-->
<div class="modal side-panel fade" id="modalDelivery" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header m-0"><h4 class="modal-title" id="exampleModalLongTitle">Delivery Approval</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
          <div class="modal-body">
		  

<?php if(App\SysHelper::delivery_approval_access()): ?>

<?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-approval-delivery','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-deal-track-approval-delivery'])); ?>


<input type="hidden" name="owner_id" value="<?php echo e(@$del->owner); ?>" />
<input type="hidden" name="owner_name" value="<?php echo e(@$del->ownername->full_name); ?>" />
<input type="hidden" name="owner_email" value="<?php echo e(@$del->ownername->email); ?>" />

<?php

$do_status="";
$do_no="";
$print_invoice_no="";
$cheque_collection="";
$cheque_collection_file="";
$delivery_status="";
$deliver_by="";
$driver_txt="";
$remarks="";
$cash_collected="";
$contact_no="";
$id_no="";
$attach_file="";
$awb_no="";
if(count($invoice)>0){
$print_invoice_no=$invoice[0]->invoice_no;
}
if(count($delivery)>0){
foreach ($delivery as $de){
$do_status=$de->do_status;
$do_no=$de->do_no;
$print_invoice_no=$de->print_invoice_no;
$cheque_collection=$de->cheque_collection;
$cheque_collection_file=$de->cheque_collection_file;
$delivery_status=$de->delivery_status;
$deliver_by=$de->deliver_by;
$driver_txt=$de->driver;
$remarks=$de->remarks;
$cash_collected=$de->cash_collected;
$contact_no=$de->contact_no;
$id_no=$de->id_no;
$attach_file=$de->attach_file;
$awb_no=$de->awb_no;
}
}

?>

<div class="row">
<div class="col-lg-4 mb-1">
    <div class="form-check-label">DO Status
        <select class="form-control" name="do_status" required>
          <option value="1" <?php if($do_status=="1"): ?> selected <?php endif; ?>>Approved</option>
          <option value="2" <?php if($do_status=="2"): ?> selected <?php endif; ?>>Disapproved</option>
        </select>
      </div>
</div>
<?php
$dn_doc_numbers = '';

if(count($dn_detail)>0){
    $dn_doc_numbers = $dn_detail->pluck('doc_number')->implode(',');
  } else {
    $dn_doc_numbers = '';
  }

if($do_no == ""){
  if(count($dn_detail)>0){
    $dn_no = $dn_detail[0]->doc_number;
  } else {
    $dn_no = '';
  }
} else {
  $dn_no = $do_no;
}
?>
<div class="col-lg-4 mb-1">
    <div class="form-check-label">DO No
        <input type="text" class="form-control" value="<?php if($dn_doc_numbers != ''): ?><?php echo e($dn_doc_numbers); ?> <?php endif; ?>" name="do_no" required />
      </div>
</div>
<div class="col-lg-4 mb-1">
    <label class="form-check-label">Print Invoice No</label>
    <input type="text" class="form-control" name="print_invoice_no"
           value="<?php echo e(@$invoice_si_list ?? ''); ?>" required />
</div>


<?php if($deal->payment_mode==1): ?>
<div class="col-lg-4 mb-1">
    <div class="form-check-label">Cash Collected
        <input type="number" class="form-control" value="<?php echo e($cash_collected); ?>" step="any" name="cash_collected" required />
      </div>
</div>
<?php else: ?>
<div class="col-lg-4 mb-1">
    <div class="form-check-label">Cheque Collection
        <select class="form-control" name="cheque_collection" required>
          <option value="1" <?php if($cheque_collection=="1"): ?> selected <?php endif; ?>>Approved</option>
          <option value="2" <?php if($cheque_collection=="2"): ?> selected <?php endif; ?>>Disapproved</option>
          <option value="3" <?php if($cheque_collection=="3"): ?> selected <?php endif; ?>>Pending</option>
        </select>
      </div>
</div>            

<?php endif; ?>

<div class="col-lg-4 mb-1">
    <div class="form-check-label">Delivery Status
        <select class="form-control" name="delivery_status" required>
          <option value="" <?php if($delivery_status=="" ): ?> selected <?php endif; ?>>-Select-</option>
          <option value="2" <?php if($delivery_status=="2"): ?> selected <?php endif; ?>>Pending For Delivery</option>
          <option value="4" <?php if($delivery_status=="4"): ?> selected <?php endif; ?>>Ready For Delivery</option>
          <option value="3" <?php if($delivery_status=="3"): ?> selected <?php endif; ?>>Out For Delivery</option>
          <option value="1" <?php if($delivery_status=="1" || $delivery_status==""): ?> selected <?php endif; ?>>Delivery Completed</option>
          <option value="5" <?php if($delivery_status=="5"): ?> selected <?php endif; ?>>Partial Delivery</option>
        </select>
      </div>
</div>

<div class="col-lg-4 mb-1">
  <div class="form-check-label">Delivered Through
      <select class="form-control js-example-basic-single" id="deliver_by_new_re" name="deliver_by" required>
          <option value="" <?php if($deliver_by==""): ?> selected <?php endif; ?>>-Select-</option>
          <option value="1" <?php if($deliver_by=="Courier" || $deliver_by==1): ?> selected <?php endif; ?>>Courier</option>
          <option value="7" <?php if($deliver_by=="Forwarder" || $deliver_by==7): ?> selected <?php endif; ?>>Forwarder</option>
          <option value="2" <?php if($deliver_by=="Driver" || $deliver_by==2): ?> selected <?php endif; ?>>Driver</option>
          <option value="3" <?php if($deliver_by=="Local Delivery" || $deliver_by==3): ?> selected <?php endif; ?>>Local Delivery</option>
          <option value="4" <?php if($deliver_by=="Office Boy" || $deliver_by==4): ?> selected <?php endif; ?>>Office Boy</option>
          <option value="5" <?php if($deliver_by=="Collection by Client" || $deliver_by==5): ?> selected <?php endif; ?>>Collection by Client</option>
          <option value="6" <?php if($deliver_by=="By Email" || $deliver_by==6): ?> selected <?php endif; ?>>By Email</option>
      </select>
    </div>
</div>
<?php
            $forwader_suppliers = @App\SysCustSuppl::where('company_id',session('logged_session_data.company_id') )->where('catid', 2)->where('account_type',2)->orderby('name', 'asc')->get(); // 1 customers, 2 suppliers
            $logistic_dept = @App\SysHelper::getDepartmentByName('Logistic');
            $office_boy_designation_id = @App\SysHelper::getDesignationByName('Office Boy');

        
           
       $drivers  = @App\SmStaff::where('company_id',session('logged_session_data.company_id') )->where('department_id', $logistic_dept)->orderby('full_name', 'asc')->get();
       $office_boys  = @App\SmStaff::where('company_id',session('logged_session_data.company_id') )->where('designation_id', $office_boy_designation_id)->orderby('full_name', 'asc')->get();

      
       
?>

<div class="col-lg-4 mb-1" id="div_driver_re" style="display: none;">
<div class="form-check-label" >Delivered By
    <?php
      $driver_is_other = ($deliver_by == 'Driver' || $deliver_by == 2) && $driver_txt !== '' && !$drivers->pluck('id')->contains($driver_txt);
    ?>
    <select class="form-control js-example-basic-single" id="driver_re" name="driver">
        <option value="" selected>-Select-</option>
        <?php $__currentLoopData = $drivers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e(@$value->id); ?>" <?php if($driver_txt == $value->id): ?> selected <?php endif; ?>><?php echo e(@$value->first_name); ?> <?php echo e(@$value->last_name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <option value="Other" <?php if($driver_is_other): ?> selected <?php endif; ?>>Other</option>
    </select>
    <input type="text" class="form-control" id="other_driver_re" name="other_driver" placeholder="Other Driver" style="display: <?php if($driver_is_other): ?> block <?php else: ?> none <?php endif; ?>;" value="<?php if($driver_is_other): ?><?php echo e($driver_txt); ?><?php endif; ?>"/>
    <script>
      $('#driver_re').on('change', function(e) {
        if ($(this).val() === "Other") {
          $('#other_driver_re').css("display", "block");
        } else {
          $('#other_driver_re').css("display", "none");
        }
      }).trigger('change');
    </script>
</div>
</div>


<div class="col-lg-4 mb-1" id="div_byemail_re" style="display: none;">
<div class="form-check-label">Email IDs
    <input type="text" class="form-control" name="byemail" value="<?php echo e($driver_txt); ?>" placeholder="Email Ids">
</div>
</div>

  <script>
    $(document).ready(function() {
      function hideAllDeliveryBlocks() {
        $('#div_courier_re, #div_forwarder_re, #div_attach_file_re, #div_driver_re, #div_localdelivery_re, #div_officeboy_re, #div_collectionbyclient_re, #div_byemail_re').hide();
      }

      function toggleDeliveryBlocks() {
        hideAllDeliveryBlocks();
        switch ($('#deliver_by_new_re').val()) {
          case '1':
            $('#div_courier_re, #div_attach_file_re').show();
            break;
          case '7':
            $('#div_forwarder_re').show();
            break;
          case '2':
            $('#div_driver_re').show();
            break;
          case '3':
            $('#div_localdelivery_re').show();
            break;
          case '4':
            $('#div_officeboy_re').show();
            break;
          case '5':
            $('#div_collectionbyclient_re').show();
            break;
          case '6':
            $('#div_byemail_re').show();
            break;
          default:
            hideAllDeliveryBlocks();
            break;
        }
      }

      $('#deliver_by_new_re').on('change', toggleDeliveryBlocks);

      $('#courier_re').on('change', function() {
        $('#other_courier_re').toggle($(this).val() === 'Other');
      });

      $('#forwarder_re').on('change', function() {
        $('#other_forwarder_re').toggle($(this).val() === 'Other');
      });

      $('#driver_re').on('change', function() {
        $('#other_driver_re').toggle($(this).val() === 'Other');
      });

      $('#localdelivery_re').on('change', function() {
        $('#other_localdelivery_re').toggle($(this).val() === 'Other');
      });

      $('#officeboy_re').on('change', function() {
        $('#other_officeboy_re').toggle($(this).val() === 'Other');
      });

      // initialize state after all handlers are attached
      $('#courier_re, #forwarder_re, #driver_re, #localdelivery_re, #officeboy_re').trigger('change');
      setTimeout(function() { $('#deliver_by_new_re').trigger('change'); }, 200);
    });
  </script>



<div class="col-lg-4 mb-1" id="div_courier_re" style="display: none;">
<div class="form-check-label" >Courier
    <?php
      $courier_is_other = ($deliver_by == 'Courier' || $deliver_by == 1) && $driver_txt !== '' && !$shipping->pluck('id')->contains($driver_txt);
    ?>
    <select class="form-control js-example-basic-single" id="courier_re" name="courier">
        <option value="" selected>-Select-</option>
        <?php $__currentLoopData = $shipping; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e(@$value->id); ?>" <?php if($driver_txt == @$value->id): ?> selected <?php endif; ?>><?php echo e(@$value->name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <option value="Other" <?php if($courier_is_other): ?> selected <?php endif; ?>>Other</option>
      </select>
      <input type="text" class="form-control" id="other_courier_re" name="other_courier" placeholder="Other Courier" style="display: <?php if($courier_is_other): ?> block <?php else: ?> none <?php endif; ?>;" value="<?php if($courier_is_other): ?><?php echo e($driver_txt); ?><?php endif; ?>"/>
      <script>
        $('#courier_re').on('change', function(e) {
          if ($(this).val() === "Other") {
            $('#other_courier_re').css("display", "block");
          } else {
            $('#other_courier_re').css("display", "none");
          }
        }).trigger('change');
      </script>
</div>
</div>

<div class="col-lg-4 mb-1" id="div_forwarder_re" style="display: none;">
<div class="form-check-label" >Forwarder
    <?php
      $forwarder_is_other = ($deliver_by == 'Forwarder' || $deliver_by == 7) && $driver_txt !== '' && !$forwader_suppliers->pluck('id')->contains($driver_txt);
    ?>
    <select class="form-control js-example-basic-single" id="forwarder_re" name="forwarder">
        <option value="" selected>-Select-</option>
        <?php $__currentLoopData = $forwader_suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e(@$value->id); ?>" <?php if($driver_txt == @$value->id): ?> selected <?php endif; ?>><?php echo e(@$value->name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <option value="Other" <?php if($forwarder_is_other): ?> selected <?php endif; ?>>Other</option>
      </select>
      <input type="text" class="form-control" id="other_forwarder_re" name="other_forwarder" placeholder="Other Forwarder" style="display: <?php if($forwarder_is_other): ?> block <?php else: ?> none <?php endif; ?>;" value="<?php if($forwarder_is_other): ?><?php echo e($driver_txt); ?><?php endif; ?>"/>
      <script>
        $('#forwarder_re').on('change', function(e) {
          if ($(this).val() === "Other") {
            $('#other_forwarder_re').css("display", "block");
          } else {
            $('#other_forwarder_re').css("display", "none");
          }
        }).trigger('change');
      </script>
</div>
</div>



<div class="col-lg-4 mb-1" id="div_localdelivery_re" style="display: none;">
<div class="form-check-label" >Local Delivery
    <?php
      $local_delivery_options = ['Ahmed','Mohid','Usman','Shoyeb','Imran'];
      $localdelivery_is_other = ($deliver_by == 'Local Delivery' || $deliver_by == 3) && $driver_txt !== '' && !in_array($driver_txt, $local_delivery_options);
    ?>
    <select class="form-control" id="localdelivery_re" name="localdelivery">
        <option value="" <?php if($driver_txt==""): ?> selected <?php endif; ?>>-Select-</option>
        <option value="Ahmed" <?php if($driver_txt=="Ahmed"): ?> selected <?php endif; ?>>Ahmed</option>
        <option value="Mohid" <?php if($driver_txt=="Mohid"): ?> selected <?php endif; ?>>Mohid</option>
        <option value="Usman" <?php if($driver_txt=="Usman"): ?> selected <?php endif; ?>>Usman</option>
        <option value="Shoyeb" <?php if($driver_txt=="Shoyeb"): ?> selected <?php endif; ?>>Shoyeb</option>
        <option value="Imran" <?php if($driver_txt=="Imran"): ?> selected <?php endif; ?>>Imran</option>
        <option value="Other" <?php if($localdelivery_is_other): ?> selected <?php endif; ?>>Other</option>
    </select>
    <input type="text" class="form-control" id="other_localdelivery_re" name="other_localdelivery" placeholder="Other Local Delivery" style="display: <?php if($localdelivery_is_other): ?> block <?php else: ?> none <?php endif; ?>;" value="<?php if($localdelivery_is_other): ?><?php echo e($driver_txt); ?><?php endif; ?>"/>
    <script>
      $('#localdelivery_re').on('change', function(e) {
        if ($(this).val() === "Other") {
          $('#other_localdelivery_re').css("display", "block");
        } else {
          $('#other_localdelivery_re').css("display", "none");
        }
      }).trigger('change');
    </script>
</div>
</div>

<div class="col-lg-4 mb-1" id="div_officeboy_re" style="display: none;">
<div class="form-check-label" >Office Boy
    <?php
    
      $officeboy_is_other = ($deliver_by == 'Office Boy' || $deliver_by == 4) && $driver_txt !== '' && !in_array($driver_txt, $office_boys->pluck('id')->toArray());
    ?>
    <select class="form-control js-example-basic-single" id="officeboy_re" name="officeboy">
      <option value="" <?php if($driver_txt==""): ?> selected <?php endif; ?>>-Select-</option>
      <?php $__currentLoopData = $office_boys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e(@$value->id); ?>" <?php if($driver_txt == @$value->id): ?> selected <?php endif; ?>><?php echo e(@$value->first_name); ?> <?php echo e(@$value->last_name); ?></option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      <option value="Other" <?php if($officeboy_is_other): ?> selected <?php endif; ?>>Other</option>
      
    </select>
    <input type="text" class="form-control" id="other_officeboy_re" name="other_officeboy" placeholder="Other Office Boy" style="display: <?php if($officeboy_is_other): ?> block <?php else: ?> none <?php endif; ?>;" value="<?php if($officeboy_is_other): ?><?php echo e($driver_txt); ?><?php endif; ?>"/>
    <script>
      $('#officeboy_re').on('change', function(e) {
        if ($(this).val() === "Other") {
          $('#other_officeboy_re').css("display", "block");
        } else {
          $('#other_officeboy_re').css("display", "none");
        }
      }).trigger('change');
    </script>
</div>
</div>

<div class="col-lg-4 mb-1" id="div_collectionbyclient_re" style="display: none;">
<div >Collection by Client
    <input type="text" class="form-control" id="collectionbyclient" name="collectionbyclient" placeholder="Name" value="<?php echo e($driver_txt); ?>">
    <input type="text" class="form-control mt-2" id="contact_no" name="contact_no" placeholder="Contact No" value="<?php echo e($contact_no); ?>">
    <input type="text" class="form-control mt-2" id="id_no" name="id_no" placeholder="ID No" value="<?php echo e($id_no); ?>">
</div>
</div>

<div class="col-lg-4 mb-1" id="div_attach_file_re" style="display: none;">
  <div class="form-check-label" >AWB No</div>

    <input type="text" class="form-control" id="awb_no" name="awb_no" placeholder="AWB No" value="<?php echo e($awb_no); ?>">

</div>



<?php if($deal->payment_mode==1): ?>

<?php else: ?>
          
<div class="col-lg-4 mb-10">
    <div class="form-check-label">Cheque Copy
      <?php if($cheque_collection_file!= ""): ?>
      <a class="text-info text-xs" href="<?php echo e(asset('public/uploads/crm_deal_track_doc/')); ?>/<?php echo e($cheque_collection_file); ?>" target="_blank"><i class="fa fa-download" aria-hidden="true"></i> Cheque Copy</a><?php endif; ?>

        <input type="file" class="form-control" id="cheque_collection_file" name="cheque_collection_file" >
      </div>
</div>
<?php endif; ?>
<div class="col-lg-4 mb-1">
<div class="form-check-label">Attachment/AWB Copy
  <?php if($attach_file!= ""): ?>
  <a class="text-info text-xs" href="<?php echo e(asset('public/uploads/crm_deal_track_doc/')); ?>/<?php echo e($attach_file); ?>" target="_blank"><i class="fa fa-download" aria-hidden="true"></i> AWB Copy</a><?php endif; ?>
  <input type="file" class="form-control" id="attach_file" name="attach_file" >
</div>
</div>

<div class="col mb-10">
    <div class="no-gutters input-right-icon">
        <div class="col">
            <div class="input-effect">
                <span class="form-label"><?php echo app('translator')->getFromJson('Remarks'); ?><span></span></span>
                <textarea class="form-control" rows="1" id="remarks"  name="remarks"><?php echo e($remarks); ?></textarea>
            </div>
        </div>
    </div>
</div>
</div>

<div class="modal-footer m-0 p-0">
<input type="hidden" id="deal_id" name="deal_track_id" value="<?php echo e($deal->id); ?>" />
<input type="hidden" id="deal_id" name="deal_id" value="<?php echo e($deal->deal_id); ?>" />

						<button type="submit" class="btn btn-light add-btn ms-2" id="btnSubmit">
							<i class="ico icon-outline-bookmark-opened text-success"></i> Submit
						</button>
</div>
<?php echo e(Form::close()); ?>


<?php endif; ?>
		      </div>
        </div>
      </div>
    </div>
<!-- Modal Delivery-->

<!-- Modal Receivables-->
<div class="modal side-panel fade" id="modalReceivables" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header m-0"><h4 class="modal-title" id="exampleModalLongTitle">Receivables Approval</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
          <div class="modal-body">
            <?php if(($deal->technical==1 && $deal->tech==1) || ($deal->technical==0)): ?>
            
            <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-approval-receivables','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-deal-track-approval-delivery'])); ?>

            
            <input type="hidden" name="owner_id" value="<?php echo e(@$del->owner); ?>" />
            <input type="hidden" name="owner_name" value="<?php echo e(@$del->ownername->full_name); ?>" />
            <input type="hidden" name="owner_email" value="<?php echo e(@$del->ownername->email); ?>" />
            
            <div class="row">
              <div class="col-lg-4 mb-1">
                  <div class="form-check-label">Mode
                    <?php
                    if(count($receipt_details)>0){
                      $receipt_through = $receipt_details[0]->receipt_through;
                    } else { $receipt_through = ""; }
                    ?>
                      <select class="form-control payment_mode" id="payment_mode" name="payment_mode" required>
                        <option value="" selected>-Select-</option>
                        <option value="1" <?php if($receipt_through == 1): ?> selected <?php endif; ?>>Cash</option>
                        <option value="2" <?php if($receipt_through == 2 || $receipt_through == 3): ?> selected <?php endif; ?>>Bank</option>
                      </select>
                      
              <script>
                $(document).ready(function () {
                  function togglePaymentFields() {
                    const paymentVal = $('.payment_mode').val();

                    if (paymentVal == '1') {
                      $('.receipt_through_div, .pdc_cheque_div').hide();
                    } else {
                      $('.receipt_through_div').show();
                    }
                  }

                  $('.payment_mode').on('change', togglePaymentFields);

                  togglePaymentFields();
                });
              </script>
                    </div>
              </div>
              <div class="col-lg-4 mb-1">
                  <div class="form-check-label">Collection
                      <select class="form-control payment_collection" name="payment_collection" required>
                        <option value="" selected>-Select-</option>
                        <option value="1" <?php if(count($receivables)>0): ?> <?php if($receivables[0]->payment_collection == 1): ?> selected <?php endif; ?> <?php else: ?> selected <?php endif; ?>>Approved</option>
                        <option value="2" <?php if(count($receivables)>0): ?> <?php if($receivables[0]->payment_collection == 2): ?> selected <?php endif; ?> <?php endif; ?>>Disapproved</option>
                        <option value="3" <?php if(count($receivables)>0): ?> <?php if($receivables[0]->payment_collection == 3): ?> selected <?php endif; ?> <?php endif; ?>>Order Cancelled</option>
                      </select>
                    </div>
              </div>
              <script>
                $(function () { $(".payment_collection").change(); });
              </script>
              <script>
                $('.payment_collection').on('change', function(e) {
                  if ($('.payment_collection').val() == 3) {
                    $('.credit_note_div').css("display", "block");
                    $('.no_cn_div').css("display", "none");
                    $('.credit_note').prop('required', true);
                    $('.no_cn_req').prop('required', false);
                  }
                  else{
                    $('.credit_note_div').css("display", "none");
                    $('.no_cn_div').css("display", "block");
                    $('.credit_note').prop('required', false);
                    $('.no_cn_req').prop('required', true);
                  }
                });
                </script>
              <div class="col-lg-4 mb-1 credit_note_div" style="display: none;">
                  <div class="form-check-label">Credit Note
                      <input type="text" class="form-control credit_note" name="credit_note" value="<?php if(count($receivables)>0): ?> <?php echo e($receivables[0]->credit_note); ?> <?php endif; ?>" />
                    </div>
              </div>
              <div class="col-lg-4 mb-1 no_cn_div">
                  <div class="form-check-label">Payment Status
                      <select class="form-control no_cn_req" name="payment_status" id="payment_status_re" required>
                        <option value="" selected>-Select-</option>
                        <option value="1" <?php if(count($receivables)>0): ?> <?php if($receivables[0]->payment_status == 1): ?> selected <?php endif; ?> <?php else: ?> selected <?php endif; ?>>Payment Received</option>
                        <option value="2" <?php if(count($receivables)>0): ?> <?php if($receivables[0]->payment_status == 2): ?> selected <?php endif; ?> <?php endif; ?>>Pending</option>
                      </select>
                    </div>
              </div>
              <script>
                $(function () { $("#payment_status_re").change(); });
              </script>
              <script>
                $('#payment_status_re').on('change', function(e) {
                  if ($('#payment_status_re').val() == 2) {
                    $('#payment_status_div_re').css("display", "block");
                    $('#reminder_date_re').prop('required', true);
                    $('#cheque_date_re').prop('required', false);
                    $('#deposit_date_re').prop('required', false);
                    $('#open_credit_date_re').prop('required', false);
                    $('#payment_date_re').prop('required', false);
                    $('#credit_card_deposit_date_re').prop('required', false);
                    $('#banktt_date_re').prop('required', false);
                  }
                  else{
                    $('#payment_status_div_re').css("display", "none");
                    $('#reminder_date_re').prop('required', false);
                    $('#cheque_date_re').prop('required', true);
                    $('#deposit_date_re').prop('required', true);
                    $('#open_credit_date_re').prop('required', true);
                    $('#payment_date_re').prop('required', true);
                    $('#credit_card_deposit_date_re').prop('required', true);
                    $('#banktt_date_re').prop('required', true);
                  }
                });
                </script>
              <div class="col-lg-4 mb-1" id="payment_status_div_re" style="display: none;">
                  <div class="form-check-label text-danger">Reminder Date <?php if(count($receivables)>0) { $reminder_date = date('Y-m-d', strtotime($receivables[0]->reminder_date)); } else {$reminder_date = "";} ?>
                      <input type="date" class="form-control date-picker" name="reminder_date" id="reminder_date_re" placeholder="Select Date" value="<?php echo e(@App\SysHelper::normalizeToDmy($reminder_date)); ?>"/>
                      <select class="form-control" name="reminder_time">
                        <option value="" selected>-Select Time-</option>
                        <option value="09:00:00" <?php if(count($receivables)>0): ?> <?php if(date('H:i:s', strtotime($receivables[0]->reminder_date)) == "09:00:00"): ?> selected <?php endif; ?> <?php endif; ?>>09:00 AM</option>
                        <option value="10:00:00" <?php if(count($receivables)>0): ?> <?php if(date('H:i:s', strtotime($receivables[0]->reminder_date)) == "10:00:00"): ?> selected <?php endif; ?> <?php endif; ?>>10:00 AM</option>
                        <option value="11:00:00" <?php if(count($receivables)>0): ?> <?php if(date('H:i:s', strtotime($receivables[0]->reminder_date)) == "11:00:00"): ?> selected <?php endif; ?> <?php endif; ?>>11:00 AM</option>
                        <option value="12:00:00" <?php if(count($receivables)>0): ?> <?php if(date('H:i:s', strtotime($receivables[0]->reminder_date)) == "12:00:00"): ?> selected <?php endif; ?> <?php endif; ?>>12:00 PM</option>
                        <option value="13:00:00" <?php if(count($receivables)>0): ?> <?php if(date('H:i:s', strtotime($receivables[0]->reminder_date)) == "13:00:00"): ?> selected <?php endif; ?> <?php endif; ?>>01:00 PM</option>
                        <option value="14:00:00" <?php if(count($receivables)>0): ?> <?php if(date('H:i:s', strtotime($receivables[0]->reminder_date)) == "14:00:00"): ?> selected <?php endif; ?> <?php endif; ?>>02:00 PM</option>
                        <option value="15:00:00" <?php if(count($receivables)>0): ?> <?php if(date('H:i:s', strtotime($receivables[0]->reminder_date)) == "15:00:00"): ?> selected <?php endif; ?> <?php endif; ?>>03:00 PM</option>
                        <option value="16:00:00" <?php if(count($receivables)>0): ?> <?php if(date('H:i:s', strtotime($receivables[0]->reminder_date)) == "16:00:00"): ?> selected <?php endif; ?> <?php endif; ?>>04:00 PM</option>
                        <option value="17:00:00" <?php if(count($receivables)>0): ?> <?php if(date('H:i:s', strtotime($receivables[0]->reminder_date)) == "17:00:00"): ?> selected <?php endif; ?> <?php endif; ?>>05:00 PM</option>
                        <option value="18:00:00" <?php if(count($receivables)>0): ?> <?php if(date('H:i:s', strtotime($receivables[0]->reminder_date)) == "18:00:00"): ?> selected <?php endif; ?> <?php endif; ?>>06:00 PM</option>
                        <option value="19:00:00" <?php if(count($receivables)>0): ?> <?php if(date('H:i:s', strtotime($receivables[0]->reminder_date)) == "19:00:00"): ?> selected <?php endif; ?> <?php endif; ?>>07:00 PM</option>
                        <option value="20:00:00" <?php if(count($receivables)>0): ?> <?php if(date('H:i:s', strtotime($receivables[0]->reminder_date)) == "20:00:00"): ?> selected <?php endif; ?> <?php endif; ?>>08:00 PM</option>
                        <option value="21:00:00" <?php if(count($receivables)>0): ?> <?php if(date('H:i:s', strtotime($receivables[0]->reminder_date)) == "21:00:00"): ?> selected <?php endif; ?> <?php endif; ?>>09:00 PM</option>
                      </select>
                    </div>
              </div>
              <?php
                if(count($receipt_details)>0){
                  $receipt_through = $receipt_details[0]->receipt_through;
                    if($receipt_through == 2 || $receipt_through == 3){
                      $receipt_cheque_number = $receipt_details[0]->cheque_number;
                      $cheque_date = $receipt_details[0]->cheque_date;
                      $receipt_date = '';
                    } else {
                      $receipt_cheque_number = '';
                      $cheque_date = '';
                      $receipt_date = $receipt_details[0]->receipt_date;
                    }
                  $debit_amount = $receipt_details[0]->debit_amount;
                } else {
                  $debit_amount = '0.00';
                  $receipt_cheque_number = '';
                  $cheque_date = '';
                  $receipt_date = '';
                }
              ?>



              <div class="col-lg-4 mb-1 no_cn_div">
                <div class="form-check-label">Document No
                    <input type="text" class="form-control doc_number" id="doc_number" name="doc_number" required value="<?php echo e(@$receipt_details[0]->doc_number); ?>"/>
                  </div>
              </div>
              <div class="col-lg-4 mb-1 no_cn_div">
                <div class="form-check-label">Receipt Mode
                    <input type="text" class="form-control receipt_mode" id="receipt_mode_txt" name="receipt_mode_txt" required value="<?php echo e(@$receipt_details[0]->receiptmodeacc->account_name); ?>"/>
                    <input type="hidden" id="receipt_mode" name="receipt_mode" required value="<?php echo e(@$receipt_details[0]->receipt_mode); ?>"/>
                  </div>
              </div>
              <div class="col-lg-4 mb-1 no_cn_div">
                <div class="form-check-label">Receipt Date
                    <input type="text" class="form-control date-picker receipt_date" id="receipt_date" name="receipt_date" required value="<?php echo e(@$receipt_details[0]->receipt_date); ?>"/>
                  </div>
              </div>
              <div class="col-lg-4 mb-1 no_cn_div">
                <div class="form-check-label">Invoice No
                    <input type="text" class="form-control invoice_no" id="invoice_no" name="invoice_no" required value="<?php echo e(@$receipt_details[0]->bi_doc_no); ?>"/>
                  </div>
              </div>
              <div class="col-lg-4 mb-1 no_cn_div">
                <div class="form-check-label">Amount
                    <input type="text" class="form-control no_cn_req" id="amount" name="amount" required value="<?php if(count($receipt_details)>0): ?> <?php echo e($receipt_details[0]->debit_amount); ?> <?php endif; ?>"/>
                  </div>
              </div>
              <div class="col-lg-4 mb-1 receipt_through_div" style="display: none;">
                <div class="form-check-label">Receipt Through
                  <select class="form-control receipt_through" name="receipt_through" id="receipt_through">
                            <option value="1" <?php if(@$receipt_details[0]->receipt_through == 1): ?> selected <?php endif; ?>>Bank Transfer</option>
                            <option value="2" <?php if(@$receipt_details[0]->receipt_through == 2): ?> selected <?php endif; ?>>CDC Cheque</option>
                            <option value="3" <?php if(@$receipt_details[0]->receipt_through == 3): ?> selected <?php endif; ?>>PDC Cheque</option>
                    </select>
                    <script>
                      $(document).ready(function () {
                        function togglePdcChequeDiv() {
                          if ($('.receipt_through').val() == '3') {
                            $('.pdc_cheque_div').show();
                          } else {
                            $('.pdc_cheque_div').hide();
                          }
                        }
                        $('.receipt_through').on('change', togglePdcChequeDiv);
                        togglePdcChequeDiv();
                      });
                    </script>
                  </div>
              </div>
            
            
              <input type="hidden" name="payment_mode" value="<?php echo e($deal->payment_mode); ?>" />
              <input type="hidden" name="payment_mode_sec" value="<?php echo e($deal->payment_mode_sec); ?>" />

              <div class="col-lg-4 mb-1 pdc_cheque_div" style="display: none;">
                  <div class="form-check-label">Cheque Date
                      <input type="text" class="form-control date-picker" id="cheque_date_re" name="cheque_date" value="<?php if(count($receipt_details)>0): ?> <?php echo e(@App\SysHelper::normalizeToDmy($receipt_details[0]->cheque_date)); ?> <?php else: ?> <?php echo e(@App\SysHelper::normalizeToDmy($cheque_date)); ?> <?php endif; ?>" />
                    </div>
              </div>
              <div class="col-lg-4 mb-1 pdc_cheque_div" style="display: none;">
                <div class="form-check-label">Cheque No
                    <input type="text" class="form-control" id="cheque_no" name="cheque_no" value="<?php if(count($receipt_details)>0): ?> <?php echo e($receipt_details[0]->cheque_number); ?> <?php else: ?> <?php echo e($receipt_cheque_number); ?> <?php endif; ?>" />
                  </div>
              </div>
              <div class="col-lg-4 mb-1 pdc_cheque_div" style="display: none;">
                <div class="form-check-label">Bank Name
                    <input type="text" class="form-control" id="bank_name" name="bank_name" value="<?php if(count($receipt_details)>0): ?> <?php echo e($receipt_details[0]->cheque_bank_name); ?> <?php endif; ?>" />
                  </div>
              </div>
              
           
              <div class="col mb-10">
                  <div class="no-gutters input-right-icon">
                      <div class="col">
                          <div class="input-effect">
                              <span class="form-label"><?php echo app('translator')->getFromJson('Remarks'); ?><span></span></span>
                              <textarea class="form-control" rows="1" id="remarks"  name="remarks"><?php if(count($receivables)>0): ?> <?php echo e($receivables[0]->remarks); ?> <?php endif; ?></textarea>
                          </div>
                      </div>
                  </div>
              </div>
            
            </div>
            <div class="modal-footer m-0 p-0">
              
              <input type="hidden" id="deal_id" name="deal_track_id" value="<?php echo e($deal->id); ?>" />
              <input type="hidden" id="deal_id" name="deal_id" value="<?php echo e($deal->deal_id); ?>" />
<button type="submit" class="btn btn-light add-btn ms-2" id="btnSubmit">
	<i class="ico icon-outline-bookmark-opened text-success"></i> Submit
</button>
            </div>
            <?php echo e(Form::close()); ?>

            
            <?php endif; ?>
		      </div>
        </div>
      </div>
    </div>
<!-- Modal Receivables-->





















<!-- Modal Professional Service-->

    <!-- Modal Professional Service-->

    <div class="modal side-panel fade" id="ModalProfessionalService" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header m-0"><h4 class="modal-title" id="exampleModalLongTitle">Professional Service Approval</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">



<?php if(1): ?>


<?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-approval-professional-service','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-deal-track-approval-professional-service'])); ?>

<div class="row">
  <div class="col-12 mb-1">
  <div class="form-label">Status
      <select class="form-control technical_approve" name="technical_approve" required>
        <option value="" selected>-Select-</option>
        <option value="1">Approved</option>
        <option value="2">Disapproved</option>
      </select>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-12 mb-1">
  <div class="no-gutters input-right-icon">
      <div class="col">
          <div class="input-effect">
              <span class="form-label"><?php echo app('translator')->getFromJson('Remarks'); ?><span></span></span>
              <textarea class="form-control" rows="4" id="remarks"  name="remarks"></textarea>
          </div>
      </div>
  </div>
  </div>
</div>
<div class="modal-footer m-0 p-0">

  <input type="hidden" id="deal_id" name="deal_track_id" value="<?php echo e($deal->id); ?>" />
  <input type="hidden" id="deal_id" name="deal_id" value="<?php echo e($deal->deal_id); ?>" />
  <button type="submit" class="btn btn-sm btn-primary pl-3 pr-3" id="btnSubmit">
          <?php echo app('translator')->getFromJson('Submit'); ?>
  </button>  
</div>
<?php echo e(Form::close()); ?>

<?php endif; ?>

<script>
  flatpickr(".date-picker", {
  dateFormat: "d/m/Y", // dd/mm/yyyy
  allowInput: true
});
</script>

  <script>
        $(document).ready(function() {
            $('.js-example-basic-single').select2();

            // When a modal is opened, reattach Select2 dropdown inside that modal
            $('.modal').on('shown.bs.modal', function() {
                $(this).find('.js-example-basic-single').each(function() {
                    $(this).select2({
                        dropdownParent: $(this).closest('.modal'),
                        width: '100%'
                    });
                });
            });
        });
        
    </script>









{{--  crm-deal-track-approval-professional-service  --}

            
          </div>
        </div>
      </div>
    </div>


    

<?php try { 
    
    ?>
    <style>
        .header-height{
            height: 1rem
        }
        .track-action-btn {
      
            border-radius: 4px;
            border: 1px solid rgba(255,255,255,0.4);
            background: rgba(255,255,255,0.2);
            color: white;
         
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
           
           
        }
        
        .track-action-btn:hover {
            background: rgba(255,255,255,0.35);
            color: white;
            border-color: rgba(255,255,255,0.6);
        }
         .track-stage-actions {
            display: flex;
            gap: 0.3rem;
            align-items: center;
        }
        .green-track-action-btn {
  
    border-radius: 4px;
    border: 1px solid rgba(0, 0, 0, 0.15);
    /* background: rgba(0, 128, 0, 0.1); */
    color: #065f46; /* dark green text */
    font-size: 0.8rem;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
}



.green-track-stage-actions {
    display: flex;
    gap: 0.3rem;
    align-items: center;
}

    </style>
    <div class="row">

            
                            <div class="modal side-panel fade" id="ledgerModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="ledgerModal" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-scrollable draggable" style="max-width:1100px;width:1100px;left:45rem">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Ledger</h4>
                                            <button type="button" class="btn-close" id="ledger-modal-close"  aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body m-0 p-0" id="ledgermodalbody">
                                           
                                        </div>
                                    </div>
                                </div>
                            </div>

                                    
                            <div class="modal side-panel fade" id="osModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="osModal" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-scrollable draggable" style="max-width:1100px;width:1100px;left:45rem">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Receivable Outstanding</h4>
                                            <button type="button" class="btn-close" id="os-modal-close"  aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body m-0 p-0" id="osmodalbody">
                                           
                                        </div>
                                    </div>
                                </div>
                            </div>

    
            <div class="col p-1" >
                <div class="card mb-3" style="border-radius: 16px" >
                     <table class="detail-item-table-sm" width="100%" style="table-layout: fixed;width:100%">
                        <?php
                        if ($deal->accounts == 1){
                            $account_status = "bg-success text-white";
                        } else if ($deal->accounts == 2){
                            $account_status = "bg-danger text-white";
                        }
                        else if ($deal->accounts == 3){
                            $account_status = "bg-lightgreen text-dark";
                        } else{
                            $account_status = "bg-lightgreen text-dark";
                            //track-notrequired;
                        }
                    ?>
                        <tr>
                               
                            <td class="<?php echo e($account_status); ?> d-flex align-items-center justify-content-between gap-1" style="height:23px">

                              <script>
                                    $(function () {

                                        // Close Ledger Modal
                                        $('#ledger-modal-close').on('click', function () {
                                            const modalEl = document.getElementById('ledgerModal');
                                            const modal = bootstrap.Modal.getInstance(modalEl)
                                                        || new bootstrap.Modal(modalEl);
                                            modal.hide();
                                        });

                                        // Close OS Modal
                                        $('#os-modal-close').on('click', function () {
                                            const modalEl = document.getElementById('osModal');
                                            const modal = bootstrap.Modal.getInstance(modalEl)
                                                        || new bootstrap.Modal(modalEl);
                                            modal.hide();
                                        });

                                        // ESC key close only active modal
                                        $(document).on('keydown', function (e) {
                                            if (e.key === "Escape") {

                                                // Ledger Modal
                                                if ($('#ledgerModal').hasClass('show')) {
                                                    const modal = bootstrap.Modal.getInstance(document.getElementById('ledgerModal'));
                                                    modal.hide();
                                                }

                                                // OS Modal
                                                else if ($('#osModal').hasClass('show')) {
                                                    const modal = bootstrap.Modal.getInstance(document.getElementById('osModal'));
                                                    modal.hide();
                                                }

                                            }
                                        });

                                    });
                              </script>

                                <script>



                                   $(document).ready(function() {
                                        $(document).on('click', '.openLedgerModal', function(e) {
                                            e.preventDefault();
                                            console.log("Ledger modal clicked");

                                            const $form = $(this).closest('form'); 
                                            const $modalBody = $("#ledgermodalbody");
                                            const $loader = $("#loading_bg");

                                            // Show loader
                                            $modalBody.children().not($loader).remove();
                                            $loader.show();

                                            // Build FormData manually to ensure arrays are sent
                                            const formData = new FormData();

                                            // Append all account_id[] values
                                            $form.find('input[name="account_id[]"]').each(function() {
                                                formData.append('account_id[]', $(this).val());
                                            });

                                            // Append other inputs
                                            formData.append('from_date', $form.find('input[name="from_date"]').val());
                                            formData.append('to_date', $form.find('input[name="to_date"]').val());
                                            formData.append('redirect_by_dealtrack', 1);

                                            // Send AJAX
                                            $.ajax({
                                                url: $form.attr('action'),
                                                type: $form.attr('method') || 'POST',
                                                data: formData,
                                                processData: false,
                                                contentType: false,
                                                headers: {
                                                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                                                },
                                                success: function(html) {
                                                    $loader.hide();
                                                    $modalBody.html(html);
                                                    $("#ledgerModal").modal("show");
                                                },
                                                error: function(xhr) {
                                                    $loader.hide();
                                                    console.error("Failed to load modal:", xhr.responseText);
                                                    $modalBody.html('<div class="alert alert-danger">Failed to load ledger. Try again.</div>');
                                                    $("#ledgerModal").modal("show");
                                                }
                                            });
                                        });
                                    });

                                       $(document).ready(function() {
                                        $(document).on('click', '.openOSModal', function(e) {
                                            e.preventDefault();

                                            const $form = $(this).closest('form'); 
                                            const $modalBody = $("#osmodalbody");
                                            const $loader = $("#loading_bg");

                                            // Show loader
                                            $modalBody.children().not($loader).remove();
                                            $loader.show();

                                            // Build FormData manually to ensure arrays are sent
                                            const formData = new FormData();

                                            // Append all account_id[] values
                                            $form.find('input[name="account_id[]"]').each(function() {
                                                formData.append('account_id[]', $(this).val());
                                            });

                                            // Append other inputs
                                            formData.append('till_date', $form.find('input[name="till_date"]').val());
                                            formData.append('redirect_by_dealtrack', 1);

                                            // Send AJAX
                                            $.ajax({
                                                url: $form.attr('action'),
                                                type: $form.attr('method') || 'POST',
                                                data: formData,
                                                processData: false,
                                                contentType: false,
                                                headers: {
                                                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                                                },
                                                success: function(html) {
                                                    $loader.hide();
                                                    $modalBody.html(html);
                                                    $("#osModal").modal("show");
                                                },
                                                error: function(xhr) {
                                                    $loader.hide();
                                                    console.error("Failed to load modal:", xhr.responseText);
                                                    $modalBody.html('<div class="alert alert-danger">Failed to load ledger. Try again.</div>');
                                                    $("#osModal").modal("show");
                                                }
                                            });
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

                            


                        

                          
                                <div class="d-flex align-items-center justify-content-start flex-grow-1 gap-1 header-height">
                                    <b>Accounts</b>
                                    <?php if(App\SysHelper::get_company_status($del->customername) != 0 || 1): ?>
                                        <?php if(App\SysHelper::account_approval_access() && in_array($deal->accounts,[0,2,3,1]) && ($deal->sales ==0)): ?>
                                            <a class="btn-md light" style="display: contents;" data-bs-toggle="modal" data-bs-target="#modalAccount">
                                                <i class="ico icon-outline-pen-new-square title-15 <?php echo e($account_status == 'bg-lightgreen text-dark' ? 'text-dark' : 'text-white'); ?>" title="Accounts Approval" style="font-size: 12px"></i>
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                  <div class="track-stage-actions">
                                        <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'generalledger', 'target' => '_blank', 'method' => 'POST', 'enctype' => 'multipart/form-data'])); ?>

                                        <input type="hidden" id="account_id_ledger" name="account_id[]" value="<?php echo e(@$account_id->id); ?>" />
                                        <input type="hidden" id="from_date_ledger" name="from_date" value="<?php echo e(date('Y-01-01')); ?>" />
                                        <input type="hidden" id="to_date_ledger" name="to_date" value="<?php echo e(date('Y-m-d')); ?>" /> 
                                       
                                         <button class="<?php if($deal->accounts != 1 && $deal->accounts != 2): ?> green-track-action-btn <?php else: ?> track-action-btn  <?php endif; ?>  openLedgerModal" title="View Ledger">Ledger</button>
                                        <?php echo e(Form::close()); ?>


                                        <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'receivable-outstanding-modal', 'target' => '_blank', 'method' => 'POST', 'enctype' => 'multipart/form-data'])); ?>

                                        <input type="hidden" name="account_id[]" value="<?php echo e(@$account_id->id); ?>" />
                                        <input type="hidden" name="till_date" value="<?php echo e(date('Y-m-d')); ?>" />
                                        <button class="<?php if($deal->accounts != 1 && $deal->accounts != 2): ?> green-track-action-btn <?php else: ?> track-action-btn  <?php endif; ?> openOSModal" title="Receivable Outstanding" style="font-size:11px"> OS</button>                    
                                        <?php echo e(Form::close()); ?>

                                  </div>

                          
                            </td>
                       </tr>
                        
                        <?php if(count($accounts) > 0): ?>
                            <?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr ><td  class="text-start truncate-text-custom"> <span class="fw-bold">Customer Status</span>  : <?php if($val->customer_status == 1): ?>
                                        Approved <i class="ico icon-outline-check-read title-15 text-success"></i>
                                    <?php elseif($val->customer_status == 2): ?>
                                        Disapproved <i class="ico icon-outline-close text-danger"></i>
                                    <?php else: ?>
                                        Pending <i class="ico icon-outline-clock-circle text-info"></i>
                            <?php endif; ?> </td></tr>
                        <tr><td class="text-start truncate-text-custom"> <span class="fw-bold">Credit Limit</span> : <?php if($val->credit_limit == 1): ?>
                                    Approved <i class="ico icon-outline-check-read title-15 text-success"></i>
                                <?php elseif($val->credit_limit == 2): ?>
                                    Disapproved <i class="ico icon-outline-close text-danger"></i>
                                <?php else: ?>
                                    Pending <i class="ico icon-outline-clock-circle text-info"></i> 
                        <?php endif; ?></td></tr>
                        <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Payment Terms</span> : <?php if($val->payment_terms == 1): ?>
                                Approved <i class="ico icon-outline-check-read title-15 text-success"></i>
                            <?php elseif($val->payment_terms == 2): ?>
                                Disapproved <i class="ico icon-outline-close text-danger"></i>
                            <?php else: ?>
                                Pending <i class="ico icon-outline-clock-circle text-info"></i>
                        <?php endif; ?></td></tr>
                        <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Overdue Payment</span> : <?php if($val->pending_payment == 1): ?>
                                No <i class="ico icon-outline-check-read title-15 text-success"></i>
                            <?php elseif($val->pending_payment == 2): ?>
                                Yes <i class="ico icon-outline-close text-danger"></i>
                            <?php else: ?>
                                Pending <i class="ico icon-outline-clock-circle text-info"></i> 
                        <?php endif; ?></td></tr>
                        <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Other</span> :  <?php if($val->other == 1): ?>
                                Approved <i class="ico icon-outline-check-read title-15 text-success"></i>
                            <?php elseif($val->other == 2): ?>
                                Disapproved <i class="ico icon-outline-close text-danger"></i>
                            <?php else: ?>
                                Pending <i class="ico icon-outline-clock-circle text-info"></i> 
                        <?php endif; ?> 
                    </td></tr>
                        <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Remarks</span> : <?php echo $val->remarks ?: 'No remarks'; ?></td></tr>
                        <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Created By</span> : <?php echo e($val->createdby->full_name); ?></td></tr>
                        <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Created At</span> : <?php echo e(date('d/m/Y h:i A', strtotime($val->created_at))); ?> </td></tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>


                    
                       
                    </table>



                    <?php if(($deal->accounts==0 || $deal->accounts==3) && (Auth::user()->role_id == 27 || Auth::user()->role_id == 28 || Auth::user()->role_id == 1)): ?>
                            <a class="text-danger float-center text-center m-2" style="font-size:12px" onclick="acc_updiv()">Set Pending</a>

                            <?php $pendng = App\SysCrmDealTrackApprovalAccountsPending::where('deal_id',$deal->id)->get(); ?>
                            <?php if(count($pendng)>0): ?>

                                <br style="clear:both;" />
                          <?php $__currentLoopData = $pendng; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="pending-item mb-3 pb-2 border-bottom">
                                    <p class="mb-1 "><?php echo e($p->remarks); ?></p>
                                    <div class="text-muted small">
                                        <span>By: <?php echo e($p->createdby->full_name); ?></span> <br>
                                        <span><?php echo e(date('d/m/Y h:i A', strtotime($p->created_at))); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            <?php endif; ?>

                            
                        <?php endif; ?>
                    <div>

                        

                        

                        

                            
                            <div class="modal side-panel fade" id="acc_div_update" data-bs-backdrop="false" tabindex="-1"
                                aria-labelledby="editModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-sm" style="height: 464px !important;">
                                <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-approval-accounts-update', 'method' => 'POST', 'enctype' => 'multipart/form-data'])); ?>

                                  
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="editUpdateInvoice">Update Invoice</h4>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body m-0 p-0">
                                            <div class="card mb-0 mt-0">
                                                <div class="card-body">
                                                       <select class="form-control mb-1" name="acc_status" required>
                                    <option value="" selected>-Select-</option>
                                    <option value="3">Pending</option>
                                    <option value="0">Remove Pending</option>
                                </select>
                                <textarea class="form-control mb-1" name="acc_remarks" rows="4" style="height: 50px !important;" autocomplete="off" id="lost_comments" placeholder="Remarks" required></textarea>
                                <input type="hidden" name="acc_deal_id" value="<?php echo e($deal->id); ?>" />
                               
                                                        
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-light add-btn ms-2" id="add-btn-modal">
                                                <i class="ico icon-outline-bookmark-opened text-success"></i> Update
                                            </button>
                                        </div>
                                        <?php echo e(Form::close()); ?>

                                    </div>
                                </div>
                            </div>
                            <script>
                                function acc_updiv() {
                                         $("#acc_div_update").modal("show");

                                    // if($('#acc_div_update').css('display') == 'none'){
                                    //     $("#acc_div_update").css("display", "block");
                                    // }
                                    // else{
                                    //     $("#acc_div_update").css("display", "none");
                                    // }
                                }
                            </script>

                    </div>

                </div>
            </div>
            <div class="col p-1">
                <div class="card mb-3" style="border-radius: 16px">
                     <table class="detail-item-table-sm" width="100%"  style="table-layout: fixed;width:100%">
                        <?php
                        if ($deal->sales == 1){
                            $sales_status = "bg-success text-white";
                        } else if ($deal->sales == 2){
                            $sales_status = "bg-danger text-white";
                        }
                        else if ($deal->sales == 3){
                            $sales_status = "bg-lightgreen text-dark";
                        } else{
                            $sales_status = "bg-lightgreen text-dark";
                            //track-notrequired;
                        }
                    ?>
                     <tr>
                       

                        <td class="<?php echo e($sales_status); ?> d-flex align-items-center justify-content-start gap-1" style="height:23px">
                        <div class="d-flex align-items-center justify-content-start flex-grow-1 gap-1">

                            <b>Sales</b>   
                             <?php if(App\SysHelper::sales_approval_access() && $deal->accounts==1 && in_array($deal->sales,[0,2,3,1]) && (($deal->purchease==0 || in_array($deal->purchease_approval,['0',''])) && ($deal->invoice==0 || in_array($deal->invoice_approval,['0',''])) && ($deal->delivery==0 || in_array($deal->delivery_approval,['0',''])) && ($deal->receivables==0 || in_array($deal->receivables_approval,['0','']))
                        )): ?>
                        <a class="btn-md btn-light" style="display: contents;" data-bs-toggle="modal" data-bs-target="#modalSales" title="Sales Approval"><i class="ico icon-outline-pen-new-square title-15 <?php echo e($sales_status == 'bg-lightgreen text-dark' ? 'text-dark' : 'text-white'); ?>"  style="font-size: 12px"></i></a>
                        <?php endif; ?>
                        </div>
                        
                        </td>                
                    </tr>

                        <?php if(count($sales) > 0): ?>
                            <?php $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr><td  class="text-start truncate-text-custom"><span class="fw-bold">Margin</span> : <?php if($val->margin == 1): ?>
                                        Approved <i class="ico icon-outline-check-read title-15 text-success"></i>
                                    <?php elseif($val->margin == 2): ?>
                                        Disapproved <i class="ico icon-outline-close text-danger"></i>
                                    <?php else: ?>
                                        Pending <i class="ico icon-outline-clock-circle text-info"></i>
                            <?php endif; ?></td></tr>
                            <tr><td  class="text-start truncate-text-custom"><span class="fw-bold">Stock</span> : <?php if($val->stock == 1): ?>
                                        Approved <i class="ico icon-outline-check-read title-15 text-success"></i>
                                    <?php elseif($val->stock == 2): ?>
                                        Disapproved <i class="ico icon-outline-close text-danger"></i>
                                    <?php else: ?>
                                        Pending <i class="ico icon-outline-clock-circle text-info"></i>
                            <?php endif; ?></td></tr>

                             <tr><td  class="text-start truncate-text-custom"><span class="fw-bold">Purchase Quote</span> : <?php if($val->purcease_quote == 1): ?>
                                Approved <i class="ico icon-outline-check-read title-15 text-success" aria-hidden="true"></i>
                            <?php elseif($val->purcease_quote == 2): ?>
                                Disapproved <i class="ico icon-outline-close text-danger" aria-hidden="true"></i>
                            <?php else: ?>
                                Pending <i class="ico  icon-outline-clock-circle text-info" aria-hidden="true"></i>
                        <?php endif; ?>

                         <tr><td  class="text-start truncate-text-custom"><span class="fw-bold">Other</span> : <?php if($val->other == 1): ?>
                                Approved <i class="ico icon-outline-check-read title-15 text-success" aria-hidden="true"></i>
                            <?php elseif($val->other == 2): ?>
                                Disapproved <i class="ico icon-outline-close text-danger" aria-hidden="true"></i>
                            <?php else: ?>
                                Pending <i class="ico  icon-outline-clock-circle text-info" aria-hidden="true"></i></td></tr>
                        <?php endif; ?>

                         <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Purchase Approval</span> : <?php if($val->purchase_approval == 1): ?> Required
                            <?php elseif($val->purchase_approval == 2): ?> Not Required
                            <?php else: ?> &nbsp; <?php endif; ?></td></tr>

                                        <?php
    $statuses = [];

    if ($val->invoice_approval == 1) $statuses[] = 'R';
    elseif ($val->invoice_approval == 2) $statuses[] = 'NR';

    if ($val->delivery_approval == 1) $statuses[] = 'R';
    elseif ($val->delivery_approval == 2) $statuses[] = 'NR';

    if ($val->receivables_approval == 1) $statuses[] = 'R';
    elseif ($val->receivables_approval == 2) $statuses[] = 'NR';
?>
<?php
    $popoverContent = '
        <strong>Invoice Approval:</strong> ' . 
            ($val->invoice_approval == 1 ? 'Required' : ($val->invoice_approval == 2 ? 'Not Required' : '')) . '<br>
        <strong>Delivery Approval:</strong> ' . 
            ($val->delivery_approval == 1 ? 'Required' : ($val->delivery_approval == 2 ? 'Not Required' : '')) . '<br>
        <strong>Receivables Approval:</strong> ' . 
            ($val->receivables_approval == 1 ? 'Required' : ($val->receivables_approval == 2 ? 'Not Required' : '')) . '
    ';
?>

 <tr data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                                data-bs-html="true"
                                data-bs-content="<?php echo $popoverContent; ?>"
                            data-bs-placement="top"><td class="text-start truncate-text-custom"><span class="fw-bold">SI-DO-REC Approval</span> : 
                                <?php echo e(implode('-', $statuses)); ?>

                        </td></tr>

                         

                        <?php if($val->remarks != ''): ?>
                            <tr><td  class="text-start truncate-text-custom"><span class="fw-bold">Remarks</span> : <?php echo $val->remarks; ?></td></tr>
                        <?php endif; ?>
                          <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Created By</span> :
                                <?php echo e($val->createdby->full_name); ?></td></tr>
                        <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Created At</span> :
                                <?php echo e(date('d/m/Y h:i A', strtotime($val->created_at))); ?></td></tr>
                      

                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                        
                      

                     </table>
                    <div>

                    </div>

                </div>
            </div>
            <div class="col p-1 ">
                <div class="card mb-3" style="border-radius: 16px">
                <?php $check_po_pending = App\SysDealPurchaseOrderItems::where('deal_id',$deal->deal_id)->where('status',1)->where('cart_id', session('logged_session_data.cart_id'))->count(); ?>
                <table class="detail-item-table-sm" width="100%" style="table-layout: fixed;width:100%">
                    <?php
                    
                    if ($deal->purchease == 1){
                        if ($deal->purchease_approval == 0){
                            $purchease_status = "bg-secondary text-white";
                        }
                        else {
                            $purchease_status = "bg-success text-white";
                        }
                    }
                    elseif ($deal->purchease == 2){
                        $purchease_status = "bg-danger text-white";
                    }
                    elseif ($deal->purchease == 3){
                        $purchease_status = "bg-lightgreen text-dark";
                    }
                    elseif ($deal->purchease == 4){
                        $purchease_status = "bg-lightgreen text-dark";
                    }
                    else {
                        $purchease_status = "bg-lightgreen text-dark";
                    }

                   
                    ?>
                    <tr>
                       

                     <td class="<?php echo e($purchease_status); ?> d-flex align-items-center justify-content-between gap-1" style="height:23px">


                         <div class="d-flex align-items-center justify-content-start flex-grow-1 header-height gap-1">
                            <b>Purchase</b> 
                            <?php if(count($check_po) > 0): ?>

                                <a data-bs-toggle="modal" data-bs-target="#purchase_suppliers" class="btn-md light" title="Payment Voucher"><i class="ico icon-outline-airbuds-case-minimalistic title-15 <?php echo e($purchease_status == 'bg-lightgreen text-dark' ? 'text-dark' : 'text-white'); ?>" style="font-size: 12px" aria-hidden="true"></i> </a>                                
                            <?php endif; ?>
                        <?php if(App\SysHelper::purchase_approval_access() && $deal->accounts==1 && $deal->sales==1 && in_array($deal->purchease,[0,2,3,1]) && (count($invoice)==0 && ($deal->invoice==0 || $deal->invoice==3 || $deal->invoice==1) ) && (($deal->invoice==0 || in_array($deal->invoice_approval,['0',''])) || ($deal->delivery==0 || in_array($deal->delivery_approval,['0',''])) || ($deal->receivables==0 || in_array($deal->receivables_approval,['0',''])))): ?>
                       
                            <?php if($deal->purchease_approval == 1): ?>
                                <a data-bs-toggle="modal" data-bs-target="#modalPurchase" class="btn-md light" title="Purchase Approval"><i class="ico icon-outline-pen-new-square title-15 <?php echo e($purchease_status == 'bg-lightgreen text-dark' ? 'text-dark' : 'text-white'); ?>" style="font-size: 12px" aria-hidden="true"></i></a>                                
                            <?php endif; ?>
                            
                        <?php elseif(App\SysHelper::purchase_approval_access() && ($deal->purchease == 3 || $deal->purchease == 4)): ?>
                    
                        <a class="btn-md light" data-bs-toggle="modal" data-bs-target="#modalPurchase" title="Purchase Approval"><i style="font-size:16px" class="ico icon-outline-pen-new-square  <?php echo e($purchease_status == 'bg-lightgreen text-dark' ? 'text-dark' : 'text-white'); ?>" style="font-size: 12px" aria-hidden="true"></i></a>
                        <?php endif; ?>
                         </div>

                        <div class="track-stage-actions">
                        <button type="button" title="View Purchase" class="<?php if($deal->purchease != 1 && $deal->purchease != 2): ?> green-track-action-btn <?php else: ?> track-action-btn  <?php endif; ?>  d-inline-flex align-items-center gap-1" data-bs-modal-size="modal-md" data-bs-target="#purchase_auto_generate" data-bs-toggle="modal"> <svg <?php if($deal->purchease != 1 && $deal->purchease != 2): ?> style="height: 11px;margin-top: -2px;" <?php else: ?> style="height:11px;fill:white"  <?php endif; ?>    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M352 128C352 110.3 337.7 96 320 96C302.3 96 288 110.3 288 128L288 288L128 288C110.3 288 96 302.3 96 320C96 337.7 110.3 352 128 352L288 352L288 512C288 529.7 302.3 544 320 544C337.7 544 352 529.7 352 512L352 352L512 352C529.7 352 544 337.7 544 320C544 302.3 529.7 288 512 288L352 288L352 128z"/></svg> PO</button>

                           <?php if(count($check_po) > 0): ?>

                                <?php $po_item_count =  App\SysPurchaseOrderItems::where('po_id',$check_po->pluck('id'))->sum('qty'); ?>

                                <?php if($quoteitems->sum('po_qty') < $quoteitems->sum('qty')): ?>
                                    <button type="button" title="Purchase Order Pending List" class="<?php if($deal->purchease != 1 && $deal->purchease != 2): ?> green-track-action-btn <?php else: ?> track-action-btn  <?php endif; ?> d-inline-flex align-items-center gap-1" data-bs-modal-size="modal-md" data-bs-target="#po_pending_items_popup_win" id="btnsrlpopup" data-bs-toggle="modal"><svg <?php if($deal->purchease != 1 && $deal->purchease != 2): ?> style="height: 11px;margin-top: -2px;" <?php else: ?> style="height:11px;fill:white"  <?php endif; ?>  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M352 128C352 110.3 337.7 96 320 96C302.3 96 288 110.3 288 128L288 288L128 288C110.3 288 96 302.3 96 320C96 337.7 110.3 352 128 352L288 352L288 512C288 529.7 302.3 544 320 544C337.7 544 352 529.7 352 512L352 352L512 352C529.7 352 544 337.7 544 320C544 302.3 529.7 288 512 288L352 288L352 128z"/></svg> LPO</button>
                                <?php endif; ?>


                          <?php elseif($check_po_pending == 0): ?>
                                    <div>
                                        <button  type="button" title="Purchase Order Pending List" class="<?php if($deal->purchease != 1 && $deal->purchease != 2): ?> green-track-action-btn <?php else: ?> track-action-btn  <?php endif; ?> d-inline-flex align-items-center gap-1" data-bs-modal-size="modal-md" data-bs-target="#po_pending_items_popup_win" id="btnsrlpopup" data-bs-toggle="modal"><svg <?php if($deal->purchease != 1 && $deal->purchease != 2): ?> style="height: 11px;margin-top: -2px;" <?php else: ?> style="height:11px;fill:white"  <?php endif; ?>  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M352 128C352 110.3 337.7 96 320 96C302.3 96 288 110.3 288 128L288 288L128 288C110.3 288 96 302.3 96 320C96 337.7 110.3 352 128 352L288 352L288 512C288 529.7 302.3 544 320 544C337.7 544 352 529.7 352 512L352 352L512 352C529.7 352 544 337.7 544 320C544 302.3 529.7 288 512 288L352 288L352 128z"/></svg> LPO</button>
                                        <?php /*{{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'add-deal-items-to-purchase-order-cart', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                                            <input type="hidden" name="po_deal_id" value="{{ $deal->deal_id }}" />                                
                                            <button class="btn-sm btn-info p-0" style="width: 100px; float: right;">Generate PO</button>
                                        {{ Form::close() }} */ ?>
                                    </div>
                          <?php else: ?>
                                    <div>
                                        <button title="Purchase Order Pending List"  type="button" class="<?php if($deal->purchease != 1 && $deal->purchease != 2): ?> green-track-action-btn <?php else: ?> track-action-btn  <?php endif; ?> d-inline-flex align-items-center gap-1"  onclick="window.location.href='../purchase-order/create/<?php echo e($del->customername->name); ?>/<?php echo e($del->ownername->full_name); ?>/<?php echo e($deal->deal_id); ?>/<?php echo e($deal->deal_code->code); ?>'"><svg <?php if($deal->purchease != 1 && $deal->purchease != 2): ?> style="height: 11px;margin-top: -2px;" <?php else: ?> style="height:11px;fill:white"  <?php endif; ?>  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M352 128C352 110.3 337.7 96 320 96C302.3 96 288 110.3 288 128L288 288L128 288C110.3 288 96 302.3 96 320C96 337.7 110.3 352 128 352L288 352L288 512C288 529.7 302.3 544 320 544C337.7 544 352 529.7 352 512L352 352L512 352C529.7 352 544 337.7 544 320C544 302.3 529.7 288 512 288L352 288L352 128z"/></svg> LPO</button>
                                    </div>
                         <?php endif; ?>

                        </div>


                       

                        </td>
                    </tr>

                 
                        <?php if(count($purchease) > 0): ?>
                            <?php $__currentLoopData = $purchease; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          


                             <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Purchase Status</span> : <?php if($val->validation == 1): ?>
                                Purchase Completed <i class="ico icon-outline-check-read title-15 text-success" aria-hidden="true"></i>
                            <?php elseif($val->validation == 3): ?>
                                Under Purchase <i class="ico  icon-outline-clock-circle text-warning" aria-hidden="true"></i>
                            <?php elseif($val->validation == 4): ?>
                                Partial Delivery <i class="ico icon-outline-check-read title-15 text-success" aria-hidden="true"></i>
                                <p class="my-1 mb-1"><b>Partial Delivery Note</b> : <?php echo $val->partial_delivery_note; ?></p>
                            <?php elseif($val->validation == 2): ?>
                                Disapproved <i class="ico icon-outline-close text-danger" aria-hidden="true"></i>
                            <?php else: ?>
                                Pending <i class="ico  icon-outline-clock-circle text-info" aria-hidden="true"></i> </td>
                        <?php endif; ?>
                        </tr>

                         <?php if($val->lpo_no != ""): ?>
                        <tr class=""><td class="text-start truncate-text-custom"><span class="fw-bold">LPO No</span> : <?php echo e($val->lpo_no); ?></td></tr><?php endif; ?>
                        

                           <?php
    $suppliers = $val->supplier_list; // fetch in blade
?>
                        <?php if($val->ref_supplier_id != ""): ?>
                          <?php
        $names = $suppliers->pluck('account_name')->toArray();
    ?>
    <?php
    

    $supplierPopover = '';
    foreach($suppliers as $s){
        $supplierPopover .= '<span>' . $s->account_name . '</span>';
        $supplierPopover .= ' (' . $s->account_code . ')<br>';
    }
?>
                        
                        
                        <tr class=""  data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                                data-bs-html="true"
                                data-bs-content="<?php echo $supplierPopover; ?>"
                            data-bs-placement="top"><td class="text-start truncate-text-custom"><span class="fw-bold">Supplier Name</span> :             <?php echo e(implode(', ', $names)); ?></td></tr>
                        <?php endif; ?>

                          <?php if($val->part_no != ""): ?>
                        <tr class=""><td class="text-start truncate-text-custom"><span class="fw-bold">Part No</span> : <?php echo e($val->part_no); ?></td></tr>
                        <?php endif; ?>


                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Purchase Quote</span> : <?php if($val->purchease_quote == 1): ?>
                                        Approved <i class="ico icon-outline-check-read title-15 text-success"></i>
                                    <?php elseif($val->purchease_quote == 2): ?>
                                        Disapproved <i class="ico icon-outline-close text-danger"></i>
                                    <?php else: ?>
                                        Pending <i class="ico icon-outline-clock-circle text-info"></i>
                                    <?php endif; ?>

                            <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Quote Request</span> : <?php if($val->three_quote_request == 1): ?>
                                    Approved <i class="ico icon-outline-check-read title-15 text-success"></i>
                                <?php elseif($val->three_quote_request == 3): ?>
                                    Not Required <i class="ico icon-outline-check-read title-15 text-success"></i>
                                <?php elseif($val->three_quote_request == 2): ?>
                                    Disapproved <i class="ico icon-outline-close text-danger"></i>
                                <?php else: ?>
                                    Pending <i class="ico icon-outline-clock-circle text-info"></i>
                                    </td></tr>
                        <?php endif; ?>
                        
                       
                        
                       
                        
                      
                    


                        <?php
                            $grnStatus = App\SysHelper::get_deal_track_grn_status($deal->id);
                        ?>


                       <?php if($grnStatus != ""): ?>
                            <tr>
                                <td class="text-start truncate-text-custom">
                                    <?php echo $grnStatus; ?>

                                </td>
                            </tr>
                        <?php endif; ?>

                       
                        <?php if($val->delivery_date != '' && $val->delivery_date != '1970-01-01'): ?>
                            <tr class=""><td class="text-start truncate-text-custom"><span class="fw-bold">Expected Delivery</span> : <?php echo e(date('d/m/Y', strtotime($val->delivery_date))); ?>

                                </td>
                            </tr>
                        <?php endif; ?>
                        


                        

                      
                        

                        <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Remarks</span>  : <?php echo $val->remarks; ?></td></tr>
                          <tr>
                             <td class="text-start truncate-text-custom"><span class="fw-bold">Created By </span> :
                                <?php echo e($val->createdby->full_name); ?></td>
                            </tr>   
                       

                            <tr>
                                <td class="text-start truncate-text-custom"><span class="fw-bold">Created At</span> :
                                <?php echo e(date('d/m/Y h:i A', strtotime($val->created_at))); ?></td>
                            </tr>
                        
                       
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>



                       <tr>
                         <td class=" bg-white d-flex align-items-center gap-2 flex-wrap" style="border-radius: 16px">
                       
                      
                         
                             <?php if($deal->purchease_approval == 1 && $deal->purchease !=1 && !(count($check_po) > 0)): ?>
                          
                                <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-approval-purchase-not-required', 'method' => 'POST', 'enctype' => 'multipart/form-data'])); ?>

                                <input type="hidden" name="purchase_not_required_deal_id" value="<?php echo e($deal->deal_id); ?>" />
                                <button class="btn-danger text-truncate border-0"><i class="ico icon-outline-pen-new-square text-white" style="font-size: 12px"  aria-hidden="true"></i> Not Required</button>
                                <?php echo e(Form::close()); ?>


                        
                            <?php elseif($deal->purchease_approval != 1 && !(count($check_po) > 0)): ?>
                                <?php if(@App\SysHelper::purchase_approval_access()): ?>
                                    <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-approval-purchase-required', 'method' => 'POST', 'enctype' => 'multipart/form-data'])); ?>

                                    <input type="hidden" name="purchase_required_deal_id" value="<?php echo e($deal->deal_id); ?>" />
                                    <button class="btn-success text-truncate border-0"><i class="ico icon-outline-pen-new-square text-white" style="font-size: 12px"  aria-hidden="true"></i> Required</button>
                                    <?php echo e(Form::close()); ?>

                                <?php endif; ?>

                            <?php endif; ?>

                            <?php if(count($check_po) > 0): ?>

        

                            
            <script>
                 $(document).ready(function() {
                    $(document).on('click', '.po-item', function() {
                        var id = $(this).data('id');
                      
                        $('.po-item').removeClass('active');
                        $('.po-item[data-id="' + id + '"]').addClass('active');

                        // Update the browser URL to include selected ID (without reloading)
                  

                        var action = "<?php echo e(URL::to('purchase-details-pdf')); ?>/" + id;
                        $('#loading_bg').show();

                        $.ajax({
                            url: action,
                            method: 'GET',
                            success: function(response) {

                                    $('#poViewModalbody').html(response);   // load inside modal
                                    $('#poViewModal').modal('show');             
                            },
                            error: function() {
                                $('#po-details').html(
                                    '<p class="text-danger">No Details Available.</p>');
                            },
                            complete: function() {
                                $('#loading_bg').hide(); // Always hide loader after request completes
                            }
                        });
                    });
                });
            </script>




                                <div style="float: right;display:flex;align-items:center;gap:4px;flex-wrap:wrap;">
                                <?php $__currentLoopData = $check_po; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $po): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><a class="btn-sm btn-success p-0 po-item cursor-pointer text-white" data-id="<?php echo e($po->id); ?>" style="font-size: 10px;">&nbsp;<?php echo e($po->doc_number); ?>&nbsp;</a>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


                             


                                <script>
                 $(document).ready(function() {
                    $(document).on('click', '.pay-item', function() {
                        var id = $(this).data('id');
                      
                        $('.pay-item').removeClass('active');
                        $('.pay-item[data-id="' + id + '"]').addClass('active');

                        // Update the browser URL to include selected ID (without reloading)
                  

                        var action = "<?php echo e(URL::to('payment-details-pdf')); ?>/" + id;
                        $('#loading_bg').show();

                        $.ajax({
                            url: action,
                            method: 'GET',
                            success: function(response) {

                                    $('#payViewModalbody').html(response);   // load inside modal
                                    $('#payViewModal').modal('show');             
                            },
                            error: function() {
                                $('#pay-details').html(
                                    '<p class="text-danger">No Details Available.</p>');
                            },
                            complete: function() {
                                $('#loading_bg').hide(); // Always hide loader after request completes
                            }
                        });
                    });
                });
            </script>
                                <?php if(count($list_payment)>0): ?>
                                <?php $__currentLoopData = $list_payment; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><a class="btn-sm btn-success p-0 pay-item cursor-pointer text-white" data-id="<?php echo e($pay->id); ?>" style="font-size: 10px;">&nbsp;<?php echo e($pay->doc_number); ?>&nbsp;</a>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>

                                </div>


                            <?php endif; ?>

                              <?php if(App\SysHelper::purchase_approval_access()): ?>
                            <?php if(@$val->fileone != ''): ?>
                                <p class="my-1 mb-1"><a class="btn-sm  btn-light"
                                        href="<?php echo e(asset('public/uploads/crm_deal_track_doc/')); ?>/<?php echo e(@$val->fileone); ?>"
                                        target="_blank"><i class="ico icon-bold-download-minimalistic text-success fw-bold title-15" aria-hidden="true"></i> Quote 1</a>
                                </p>
                            <?php endif; ?>
                            <?php if(@$val->filetwo != ''): ?>
                                <p class="my-1 mb-1"><a class="btn-sm text-white btn-primary"
                                        href="<?php echo e(asset('public/uploads/crm_deal_track_doc/')); ?>/<?php echo e(@$val->filetwo); ?>"
                                        target="_blank"><i class="ico icon-bold-download-minimalistic text-white fw-bold title-15" aria-hidden="true"></i> Quote 2</a>
                                </p>
                            <?php endif; ?>
                            <?php if(@$val->filethree != ''): ?>
                                <p class="my-1 mb-1"><a class="btn-sm text-white btn-primary"
                                        href="<?php echo e(asset('public/uploads/crm_deal_track_doc/')); ?>/<?php echo e(@$val->filethree); ?>"
                                        target="_blank"><i class="ico icon-bold-download-minimalistic text-white fw-bold title-15" aria-hidden="true"></i> Quote 3</a>
                                </p>
                            <?php endif; ?>
                        <?php endif; ?>
            
                        </td>
                       </tr>



                </table>
                

                </div>
            </div>
            <div class="col p-1 ">
                <div class="card mb-3" style="border-radius: 16px">
                    <table class="detail-item-table-sm" width="100%" style="table-layout: fixed;width:100%">
                    <?php
                    
                    if ($deal->invoice == 1){
                            $invoice_status = "bg-success text-white";
                    }
                    elseif ($deal->invoice == 2){
                        $invoice_status = "bg-danger text-white";
                    }
                    elseif ($deal->invoice == 3){
                        $invoice_status = "bg-lightgreen text-dark";
                    }
                    elseif ($deal->invoice == 4){
                        $invoice_status = "bg-lightgreen text-dark";
                    }
                    else {
                        $invoice_status = "bg-lightgreen text-dark";
                    }

                    ?>
                    
                <tr>

                      <td class="<?php echo e($invoice_status); ?> d-flex align-items-center justify-content-between gap-1" style="height:23px">

                        <div class="d-flex align-items-center justify-content-start flex-grow-1 header-height gap-1">
                            <b>Invoice</b>     
                            
                      <?php if(
    App\SysHelper::invoice_approval_access() && 
    (
        (
            $deal->sales==1 &&
            $deal->accounts==1 &&
            in_array($deal->purchease,[1,4]) &&
            in_array($deal->invoice,[0,2,3,1]) &&
            ($deal->delivery==0 || count($delivery) == 0) &&
            (($deal->delivery==0 || in_array($deal->delivery_approval,['0',''])) ||
             ($deal->receivables==0 || in_array($deal->receivables_approval,['0','']))) &&
            $deal->invoice_approval == 1
        )
        ||
        ($del->is_partial_invoice==1)
    )
): ?>
    <a class="btn-md btn-light" title="Invoice Approval" style="display: contents;"
        data-bs-toggle="modal" data-bs-target="#modalInvoice">
        <i class="ico icon-outline-pen-new-square title-15 <?php echo e($invoice_status == 'bg-lightgreen text-dark' ? 'text-dark' : 'text-white'); ?>" 
           style="font-size: 12px"></i>
    </a>
<?php endif; ?>

                        </div>
                        <div class="track-stage-actions">
                        <?php $check_si_pending = App\SysDealSalesInvoiceItems::where('deal_id',$deal->deal_id)->where('status',1)->where('cart_id', session('logged_session_data.cart_id'))->count(); ?>
                        <?php if(count($check_si) > 0): ?>
                            <?php $si_item_count =  App\SysSalesInvoiceItems::wherein('si_id',$check_si->pluck('id'))->sum('qty'); ?>
                            <?php if($si_item_count < $quoteitems->sum('qty')): ?>
                            
                            <button type="button" title="Sales Invoice Pending List"  class="<?php if($deal->invoice != 1 && $deal->invoice != 2): ?> green-track-action-btn <?php else: ?> track-action-btn  <?php endif; ?> d-inline-flex align-items-center gap-1" data-bs-modal-size="modal-md" data-bs-target="#si_pending_items_popup_win" id="btnsrlpopup" data-bs-toggle="modal"> <svg  <?php if($deal->invoice != 1 && $deal->invoice != 2): ?> style="height: 11px;margin-top: -2px;" <?php else: ?> style="height:11px;fill:white"  <?php endif; ?>  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M352 128C352 110.3 337.7 96 320 96C302.3 96 288 110.3 288 128L288 288L128 288C110.3 288 96 302.3 96 320C96 337.7 110.3 352 128 352L288 352L288 512C288 529.7 302.3 544 320 544C337.7 544 352 529.7 352 512L352 352L512 352C529.7 352 544 337.7 544 320C544 302.3 529.7 288 512 288L352 288L352 128z"/></svg> SI</button>
                                
                            <?php endif; ?>

                        <?php elseif($check_si_pending == 0): ?>
                            <?php if($deal->invoice_approval == 1): ?>
                                <div style="float: right;">
                                    <button type="button" title="Sales Invoice Pending List"  class="<?php if($deal->invoice != 1 && $deal->invoice != 2): ?> green-track-action-btn <?php else: ?> track-action-btn  <?php endif; ?> d-inline-flex align-items-center gap-1" data-bs-modal-size="modal-md" data-bs-target="#si_pending_items_popup_win" id="btnsrlpopup" data-bs-toggle="modal"> <svg  <?php if($deal->invoice != 1 && $deal->invoice != 2): ?> style="height: 11px;margin-top: -2px;" <?php else: ?> style="height:11px;fill:white"  <?php endif; ?>  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M352 128C352 110.3 337.7 96 320 96C302.3 96 288 110.3 288 128L288 288L128 288C110.3 288 96 302.3 96 320C96 337.7 110.3 352 128 352L288 352L288 512C288 529.7 302.3 544 320 544C337.7 544 352 529.7 352 512L352 352L512 352C529.7 352 544 337.7 544 320C544 302.3 529.7 288 512 288L352 288L352 128z"/></svg> SI</button>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <?php if($deal->invoice_approval == 1): ?>
                                <div style="float: right;">
                                    <a type="button" title="Sales Invoice Pending List" class="<?php if($deal->invoice != 1 && $deal->invoice != 2): ?> green-track-action-btn <?php else: ?> track-action-btn  <?php endif; ?> d-inline-flex align-items-center gap-1" href="../sales-invoice/create/<?php echo e($del->customername->name); ?>/<?php echo e($del->ownername->full_name); ?>/<?php echo e($deal->deal_id); ?>/<?php echo e($deal->deal_code->code); ?>"><svg <?php if($deal->invoice != 1 && $deal->invoice != 2): ?> style="height: 11px;margin-top: -2px;" <?php else: ?> style="height:11px;fill:white"  <?php endif; ?>  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M352 128C352 110.3 337.7 96 320 96C302.3 96 288 110.3 288 128L288 288L128 288C110.3 288 96 302.3 96 320C96 337.7 110.3 352 128 352L288 352L288 512C288 529.7 302.3 544 320 544C337.7 544 352 529.7 352 512L352 352L512 352C529.7 352 544 337.7 544 320C544 302.3 529.7 288 512 288L352 288L352 128z"/></svg> SI</a>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                        </div>


                       



                        </td>
                    
                    
                </tr>

                    


                    <?php if(count($invoice) > 0): ?>
                            <?php $__currentLoopData = $invoice; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Delivery Advice</span> : <?php if($val->delivery_advice == 1): ?>
                                        Approved <i class="ico icon-outline-check-read title-15 text-success"></i>
                                    <?php elseif($val->delivery_advice == 2): ?>
                                        Disapproved <i class="ico icon-outline-close text-danger"></i>
                                    <?php else: ?>
                                        Pending <i class="ico icon-outline-clock-circle text-info"></i>
                            <?php endif; ?>
                        </td></tr>

                            <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Validation</span> : <?php if($val->validation == 1): ?>
                                    Approved  <i class="ico icon-outline-check-read title-15 text-success"></i>
                                <?php elseif($val->validation == 2): ?>
                                    Disapproved <i class="ico icon-outline-close text-danger"></i>
                                <?php else: ?>
                                    Pending <i class="ico icon-outline-clock-circle text-info"></i>
                        <?php endif; ?>
                        </td></tr>

                        <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Hold</span> : <?php if($val->hold == 1): ?>
                                Approved <i class="ico icon-outline-check-read title-15 text-success"></i>
                            <?php elseif($val->hold == 2): ?>
                                Disapproved <i class="ico icon-outline-close text-danger"></i>
                            <?php else: ?>
                                Pending <i class="ico icon-outline-clock-circle text-info"></i>
                        <?php endif; ?>
                        </td></tr>

                        <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Print</span> : <?php if($val->print == 1): ?>
                                Approved <i class="ico icon-outline-check-read title-15 text-success"></i>
                            <?php elseif($val->print == 2): ?>
                                Disapproved <i class="ico icon-outline-close text-danger"></i>
                            <?php else: ?>
                                Pending <i class="ico icon-outline-clock-circle text-info"></i>
                        <?php endif; ?>
                        </td></tr>

                        <tr><td  class="text-start">
                        <?php if($val->invoice_no != ''): ?>
                           <span class="fw-bold">Invoice No</span> : <?php echo e($val->invoice_no); ?>

                        <?php endif; ?>
                        
                        <?php if(Auth::user()->role_id == 1): ?>
                        <a class="text-danger float-right" onclick="updiv()"><i class="ico icon-outline-pen-new-square title-15 text-danger"></i> </a>
                        <?php endif; ?>
                        </td></tr>

                            
                        
                        <?php if($val->partial_invoice != 0): ?>
                            <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Partial Invoice</span> : Yes</td></tr>
                            <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Partial Invoice Amount</span> : <?php echo e($val->partial_invoice_amount); ?></td></tr>
                        <?php endif; ?>

                        <?php if($val->remarks != ''): ?>
                            <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Remarks</span> : <?php echo $val->remarks; ?></td></tr>
                        <?php endif; ?>
                          <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Created By</span> :
                                <?php echo e($val->createdby->full_name); ?></td></tr>
                        <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Created At</span> :
                                <?php echo e(date('d/m/Y h:i A', strtotime($val->created_at))); ?></td></tr>
                      
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>

                        <tr>

                    <td class="bg-white d-flex align-items-center gap-1 flex-wrap" style="border-radius: 16px">
                        

                        
                      

                        <?php if($deal->invoice == 1 && session('logged_session_data.company_id')==2): ?>
                            <?php if($deal->invoice_approval == 1): ?>
                       
                                <div>
                                <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'add-deal-items-to-clearance-cart', 'method' => 'POST', 'enctype' => 'multipart/form-data'])); ?>

                                    <input type="hidden" name="clearance_deal_id" value="<?php echo e($deal->deal_id); ?>" />
                                    <button class="btn-primary text-truncate border-0">Clearance</button>
                                <?php echo e(Form::close()); ?>

                                </div>                                    
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <?php if(count($check_cl) > 0): ?>
                        <?php $__currentLoopData = $check_cl; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><a class="btn-sm btn-light" style="font-size: 10px;padding: 2px 2px;" href="<?php echo e(url('clearance/'.$cl->id.'/download')); ?>" target="_blank">&nbsp;<?php echo e($cl->invoice_no); ?>&nbsp;</a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>

                      
                        <?php if(count($check_si) > 0): ?>
                          


                            <?php if(count($list_sales_invoice)>0): ?>  
                            
                               <script>
                                    $(document).ready(function() {
                                        $(document).on('click', '.si-data-item', function() {
                                            var id = $(this).data('id');



                                        

                                            var action = "<?php echo e(URL::to('sales-invoice-pdf')); ?>/" + id;


                                            $('#loading_bg').show();

                                            $.ajax({
                                                url: action,
                                                method: 'GET',
                                                success: function(response) {
                                                            $('#siViewModalbody').html(response);   // load inside modal
                                                            $('#siViewModal').modal('show');   
                                                },
                                                error: function() {
                                                    $('#data-details').html(
                                                        '<p class="text-danger">No Details Available.</p>');
                                                },
                                                complete: function() {
                                                    $('#loading_bg').hide(); // Always hide loader after request completes
                                                }
                                            });
                                        });
                                    });
                                </script>

                            <?php $__currentLoopData = $list_sales_invoice; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a class="btn-sm btn-success text-white si-data-item p-0 cursor-pointer" data-id="<?php echo e($list->id); ?>" style="font-size: 10px;padding-right:3px !important;padding-left:3px !important" ><?php echo e($list->doc_number); ?></a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                            

                        
                        <?php endif; ?>
                                                        
                    </td>

                        </tr>


                    </table>
                    

                             <div class="modal side-panel fade" id="div_update" data-bs-backdrop="false" tabindex="-1"
                                aria-labelledby="editModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-sm" style="height: 464px !important;">
                                                <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-approval-invoice-update', 'method' => 'POST', 'enctype' => 'multipart/form-data'])); ?>

                                    
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="editUpdateInvoice">Update Invoice</h4>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body m-0 p-0">
                                            <div class="card mb-0 mt-0">
                                                <div class="card-body">
                                                        <input type="text" class="form-control mb-1" name="inv_no" placeholder="Invoice No" required />
                                                        <textarea class="form-control mb-1" name="inv_remarks" rows="4" style="height: 50px !important;" autocomplete="off" id="lost_comments" placeholder="Remarks" required></textarea>
                                                        <input type="hidden" name="inv_id" value="<?php if(isset($val)): ?><?php echo e($val->id); ?><?php endif; ?>" />
                                                       
                                                        
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-light add-btn ms-2" id="add-btn-modal">
                                                <i class="ico icon-outline-bookmark-opened text-success"></i> Update
                                            </button>
                                        </div>
                                        <?php echo e(Form::close()); ?>

                                    </div>
                                </div>
                            </div>

                            <script>
                                function updiv() {
                                     $("#div_update").modal('show');
                                    // if($('#div_update').css('display') == 'none'){
                                    //     $("#div_update").css("display", "block");
                                    // }
                                    // else{
                                    //     $("#div_update").css("display", "none");
                                    // }
                                }
                            </script>



                </div>
            </div>
            <div class="col p-1">
                <div class="card mb-3" style="border-radius: 16px">
                    <table class="detail-item-table-sm" width="100%" style="table-layout: fixed;width:100%">
                    <?php
                    if ($deal->delivery == 1){
                        if ($deal->delivery_approval == 0){
                            $delivery_status = "track-notrequired";
                        } else {
                            $delivery_status = "bg-success text-white";
                        }
                    }
                    else if ($deal->delivery == 2){
                        $delivery_status = "bg-danger text-white";
                    }
                    elseif ($deal->delivery == 3){
                        $delivery_status = "bg-lightgreen text-dark";
                    }
                    elseif ($deal->delivery == 5){
                        $delivery_status = "bg-lightgreen text-dark";
                    }
                    elseif ($deal->delivery == 4){
                        $delivery_status = "bg-lightgreen text-dark";
                    }
                    elseif ($deal->delivery == 6){
                        $delivery_status = "bg-lightgreen text-dark";
                    }
                    else {
                        $delivery_status = "bg-lightgreen text-dark";
                    }

                    ?>
                        <tr>
                            <td class="<?php echo e($delivery_status); ?> d-flex align-items-center justify-content-between gap-1" style="height:23px">
                                
                                <div class="d-flex align-items-center justify-content-start flex-grow-1 header-height gap-1">
                                <b>Delivery</b>  
                                
                                <?php if(App\SysHelper::delivery_approval_access() && $deal->accounts==1 && $deal->sales==1 && $deal->purchease==1 && $deal->invoice==1 && in_array($deal->delivery,[0,2,3,1,4,5,6]) && ($deal->receivables==0 || count($receivables) == 0)): ?>
                        
                                        <?php if($deal->delivery_approval == 1): ?>
                                        <a class="btn-md btn-light" title="Delivery Approval" style="display: contents;" data-bs-toggle="modal" data-bs-target="#modalDelivery"><i class="ico icon-outline-pen-new-square title-15 <?php echo e($delivery_status == 'bg-lightgreen text-dark' ? 'text-dark' : 'text-white'); ?>" style="font-size: 12px"></i></a>
                                            <?php endif; ?>

                                    <?php endif; ?>
                                </div>

                                  <?php $po_check = 0;
                            if(count($poitems)>0){
                                if ($poitems->sum('dn_qty') < $poitems->sum('qty')){
                                    $po_check = 1;
                                }
                            }
                        ?>


                       

                        <div class="track-stage-actions">
                        <button title="View Delivery Note" class="<?php if($deal->delivery != 1 && $deal->delivery != 2): ?> green-track-action-btn <?php else: ?> track-action-btn  <?php endif; ?> d-inline-flex align-items-center gap-1" type="button"  onclick="window.location.href='<?php echo e(url('delivery-note-add-deal/'.$deal->deal_id)); ?>'" > <svg <?php if($deal->delivery != 1 && $deal->delivery != 2): ?> style="height: 11px;margin-top: -2px;" <?php else: ?> style="height:11px;fill:white"  <?php endif; ?>  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M352 128C352 110.3 337.7 96 320 96C302.3 96 288 110.3 288 128L288 288L128 288C110.3 288 96 302.3 96 320C96 337.7 110.3 352 128 352L288 352L288 512C288 529.7 302.3 544 320 544C337.7 544 352 529.7 352 512L352 352L512 352C529.7 352 544 337.7 544 320C544 302.3 529.7 288 512 288L352 288L352 128z"/></svg> DO</button>
 <?php if(($quoteitems->sum('dn_qty') < $quoteitems->sum('qty')) || ($po_check == 1 )): ?>
                           
                     <button title="Delivery Note Pending List" type="button" class="<?php if($deal->delivery != 1 && $deal->delivery != 2): ?> green-track-action-btn <?php else: ?> track-action-btn  <?php endif; ?> d-inline-flex align-items-center gap-1" data-bs-modal-size="modal-md" data-bs-target="#dln_pending_items_popup_win" id="btnsrlpopup" data-bs-toggle="modal"><svg <?php if($deal->delivery != 1 && $deal->delivery != 2): ?> style="height: 11px;margin-top: -2px;" <?php else: ?> style="height:11px;fill:white"  <?php endif; ?>  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M352 128C352 110.3 337.7 96 320 96C302.3 96 288 110.3 288 128L288 288L128 288C110.3 288 96 302.3 96 320C96 337.7 110.3 352 128 352L288 352L288 512C288 529.7 302.3 544 320 544C337.7 544 352 529.7 352 512L352 352L512 352C529.7 352 544 337.7 544 320C544 302.3 529.7 288 512 288L352 288L352 128z"/></svg> DLN </button>
                                      
                             
                            
                        <?php endif; ?>
                        </div>

                        
                        </td>
                    </tr>


                        <?php if(count($delivery) > 0): ?>
                            <?php $__currentLoopData = $delivery; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">DO Status</span> : <?php if($val->do_status == 1): ?>
                                        Approved <i class="ico icon-outline-check-read title-15 text-success" aria-hidden="true"></i>
                                    <?php elseif($val->do_status == 2): ?>
                                        Disapproved <i class="ico icon-outline-close text-danger" aria-hidden="true"></i>
                                    <?php else: ?>
                                        Pending <i class="ico  icon-outline-clock-circle text-info" aria-hidden="true"></i> 
                            <?php endif; ?>
                            </td></tr>

                            <?php if($val->do_no != ''): ?>
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Do No</span> : <?php echo e($val->do_no); ?></td></tr>
                            <?php endif; ?>

                            <?php if($val->print_invoice_no != ''): ?>
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Print Invoice No</span> : <?php echo e($val->print_invoice_no); ?></td></tr>
                            <?php endif; ?>

                            <?php if($val->cheque_collection != ''): ?>
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Cheque Collection</span> : <?php if($val->cheque_collection == 1): ?>
                                        Approved <i class="ico icon-outline-check-read title-15 text-success" aria-hidden="true"></i>
                                    <?php elseif($val->cheque_collection == 2): ?>
                                        Disapproved <i class="ico icon-outline-close text-danger" aria-hidden="true"></i>
                                    <?php else: ?>
                                        Pending <i class="ico  icon-outline-clock-circle text-info" aria-hidden="true"></i>
                                    <?php endif; ?>
                                </td></tr>
                            <?php endif; ?>

                            <?php if($val->cheque_collection_file != ''): ?>
                                <tr><td class="text-start truncate-text-custom"><a class="btn-sm text-white btn-primary"
                                        href="<?php echo e(asset('public/uploads/crm_deal_track_doc/')); ?>/<?php echo e($val->cheque_collection_file); ?>"
                                        target="_blank"><i class="ico icon-bold-download-minimalistic text-white fw-bold title-15" aria-hidden="true"></i> Cheque
                                        Copy</a></td></tr>
                            <?php endif; ?>

                            <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Delivery Status</span> : <?php if($val->delivery_status == 1): ?>
                                    Delivery Completed <i class="ico icon-outline-check-read title-15 text-success" aria-hidden="true"></i>
                                <?php elseif($val->delivery_status == 2): ?>
                                    Pending For Delivery <i class="ico icon-outline-close text-danger" aria-hidden="true"></i>
                                <?php elseif($val->delivery_status == 4): ?>
                                    Ready For Delivery <i class="ico  icon-outline-clock-circle text-info" aria-hidden="true"></i>
                                <?php elseif($val->delivery_status == 3): ?>
                                    Out For Delivery <i class="ico  icon-outline-clock-circle text-info" aria-hidden="true"></i>
                                <?php elseif($val->delivery_status == 5): ?>
                                    Partial Delivery <i class="ico  icon-outline-clock-circle text-info" aria-hidden="true"></i> <a data-bs-toggle="modal" data-bs-target="#modalUpdateItems" class="btn btn-danger p-0">&nbsp;Update Items&nbsp;</a>
                                
                                <?php endif; ?>
                                </td></tr>

                        <?php if($val->deliver_by != ''): ?>
                            <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Delivered Through</span> : <?php echo e($val->deliver_by); ?>

                                <?php if($val->driver != ''): ?>
                                    , <?php echo e($val->driver); ?>

                            </td></tr>
                        <?php endif; ?>
                        <?php endif; ?>

                        <?php if($val->cash_collected != ''): ?>
                            <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Cash Collected</span> : <?php echo e($val->cash_collected); ?></td></tr>
                        <?php endif; ?>

                        <?php if($val->id_no != ''): ?>
                            <tr><td class="text-start truncate-text-custom"><span class="fw-bold">ID No</span> : <?php echo e($val->id_no); ?></td></tr>
                        <?php endif; ?>
                        <?php if($val->contact_no != ''): ?>
                            <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Contact No</span> : <?php echo e($val->contact_no); ?></td></tr>
                        <?php endif; ?>
                        <?php if($val->awb_no != ''): ?>
                            <tr><td class="text-start truncate-text-custom"><span class="fw-bold">AWB No</span> : <?php echo e($val->awb_no); ?></td></tr>
                        <?php endif; ?>
                        <?php if($val->attach_file != ''): ?>
                           <tr><td class="text-start truncate-text-custom"><a class="btn-sm text-white btn-primary"
                                    href="<?php echo e(asset('public/uploads/crm_deal_track_doc/')); ?>/<?php echo e($val->attach_file); ?>"
                                    target="_blank"><i class="ico icon-bold-download-minimalistic text-white fw-bold title-15" aria-hidden="true"></i> Attachment</a>
                            </td></tr>
                        <?php endif; ?>

                        <?php if($val->remarks != ''): ?>
                            <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Remarks</span> : <?php echo $val->remarks; ?></td></tr>
                        <?php endif; ?>
                         <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Created By</span> :
                                <?php echo e($val->createdby->full_name); ?></td></tr>
                        <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Created At</span> :
                                <?php echo e(date('d/m/Y h:i A', strtotime($val->created_at))); ?></td></tr>
                       
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>



                      

     <?php $delivery_note = App\SysDeliveryNote::where('deal_id',$deal->deal_id)->get(); ?>

     <?php if($delivery_note->isNotEmpty()): ?>
         
    
                        
                            <tr class="bg-white d-flex align-items-center gap-1 flex-wrap">

                
               
                      
                
               

                   
                         <td>
                        <div class=" bg-white d-flex align-items-center gap-1  flex-wrap" style="border-radius: 16px">
                                <script>
                $(document).ready(function() {
                    $(document).on('click', '.dln-item', function() {
                        var id = $(this).data('id');

                        

                        var action = "<?php echo e(URL::to('delivery-note-pdf')); ?>/" + id;



                        $('#loading_bg').show();

                        $.ajax({
                            url: action,
                            method: 'GET',
                            success: function(response) {
                                 $('#dlnViewModalbody').html(response);   // load inside modal
                                $('#dlnViewModal').modal('show');  
                            },
                            error: function() {
                                $('#data-details').html(
                                    '<p class="text-danger">No Details Available.</p>');
                            },
                            complete: function() {
                                $('#loading_bg').hide(); // Always hide loader after request completes
                            }
                        });
                    });
                });
            </script>
                            <?php $__currentLoopData = $delivery_note; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><a class="btn-sm btn-success p-0 text-white dln-item cursor-pointer" data-id="<?php echo e($dn->id); ?>" style="font-size: 10px;">&nbsp;<?php echo e($dn->doc_number); ?>&nbsp;</a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        </td>

                        

                            </tr>
                        
 <?php endif; ?>

                    </table>

                    <div>

                        
                        

                    </div>

                </div>
            </div>
            <div class="col p-1">

               
            <?php if($deal->technical == 1): ?>
                <div class="card mb-3" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">

                  
            
                    <table class="detail-item-table-sm " width="100%" style="table-layout: fixed;width:100%">
                    <tr style="background:#deebe1 !important" class="text-start"><td  class="mb-2" ><b>Professional Service</b></td></tr></table>
                    <div class="text-center ">
                        <?php if($deal->tech == 1): ?>
                            <span class="badge bg-success">Approved</span>
                        <?php elseif($deal->tech == 2): ?>
                            <span class="badge bg-danger">Rejected</span>
                        <?php else: ?>
                            <span class="badge bg-warning">Waiting For Approval</span>
                        <?php endif; ?>
                        
                
                        <?php if(App\SysHelper::professional_service_approval_access() && $deal->tech!=1 && $deal->accounts==1 && $deal->sales==1 && $deal->purchease==1 && $deal->invoice==1 && $deal->delivery==1 && $deal->technical==1 && $deal->tech!=1): ?>
                            <a data-bs-toggle="modal" data-bs-target="#ModalProfessionalService" class="btn-sm btn-md"><i class="ico icon-outline-pen-new-square title-15 text-white" style="font-size: 12px" aria-hidden="true"></i></a>
                        <?php endif; ?>
                        
                        <?php if(count($tech) > 0): ?>
                            <?php $__currentLoopData = $tech; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($val->remarks != ''): ?>
                            <p class="my-1 mb-1"><b>Remarks</b> : <?php echo $val->remarks; ?></p>
                        <?php endif; ?>
                         <p class="my-1 mb-1"><span class="text-gray border rounded pt-1 pb-1 pl-2 pr-2">Created By :
                                <?php echo e($val->createdby->full_name); ?></span></p>
                        <p class="my-1 mb-1"><span class="text-gray border rounded pt-1 pb-1 pl-2 pr-2">Created At :
                                <?php echo e(date('d/m/Y h:i A', strtotime($val->created_at))); ?></span></p>
                       
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>

                    </div>
                
               
           
                </div>
            <?php endif; ?>


            <?php if(@$del->customername->grn_select == "yes"): ?>
                
          
           
              <div class="card mb-3" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
<?php
$grn_status = @App\DealTrackGrnStatus::where('deal_track', $deal->id)->first();
?>
    <table class="detail-item-table-sm" width="100%" style="table-layout: fixed; width:100%">
        
        <tr style="background:#deebe1 !important" class="text-start">
            <td class="mb-2">
                <div class="d-flex align-items-center justify-content-start flex-grow-1 header-height gap-1">
                    
                    <b>GRN</b>

                    <?php if(!$grn_status || $grn_status->grn_status != 1): ?>
                        <a class="btn-md btn-light" title="GRN Submitted"
                           style="display: contents;"
                           data-bs-toggle="modal"
                           data-bs-target="#modalGRNSubmitted">
                            <i class="ico icon-outline-pen-new-square title-15 text-dark" style="font-size: 12px"></i>
                        </a>
                    <?php endif; ?>

                </div>
            </td>
        </tr>

        <tr>
            <td class="text-start truncate-text-custom">
                <span class="fw-bold">GRN</span> :

                <?php if($grn_status && $grn_status->grn_status == 1): ?>
                  Submitted  <i class="ico icon-outline-check-read title-15 text-success"></i>
                <?php elseif($grn_status && $grn_status->grn_status == 2): ?>
                   Not Submitted <i class="ico icon-outline-close text-danger"></i>
                <?php else: ?>
                    Pending <i class="ico icon-outline-clock-circle text-info"></i>
                <?php endif; ?>

            </td>
        </tr>

        <tr>
            <td class="text-start truncate-text-custom">
                <span class="fw-bold">Remarks</span> :

                <?php if($grn_status && $grn_status->remarks != ''): ?>
                    <?php echo $grn_status->remarks; ?>

                <?php else: ?>
                    -
                <?php endif; ?>

            </td>
        </tr>

         <?php if($grn_status): ?>
         <tr>
            <td class="text-start truncate-text-custom">
                <span class="fw-bold">Created By</span> :
                <?php if($grn_status): ?>
                    <?php echo e(optional($grn_status->creator)->full_name ?: 'N/A'); ?>           <?php echo e(date('d/m/Y h:i A', strtotime($grn_status->created_at))); ?>

                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
        </tr>
        

        

        
         <tr>
            <td class="text-start truncate-text-custom">
                <span class="fw-bold">Updated By</span> :
                <?php if($grn_status): ?>
                    <?php echo e(optional($grn_status->updater)->full_name ?: 'N/A'); ?>      <?php echo e(date('d/m/Y h:i A', strtotime($grn_status->updated_at))); ?>

                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
        </tr>
        <?php endif; ?>

         

    </table>

</div>
              <?php endif; ?>

                <div class="card mb-3" style="border-radius: 16px">
                

                    <table class="detail-item-table-sm" width="100%" style="table-layout: fixed;width:100%">
                    <?php
                    if ($deal->receivables == 1){
                        if ($deal->receivables_approval == 0){
                            $receivables_status = "track-notrequired";
                        } else {
                            $receivables_status = "bg-success text-white";
                        }
                    }
                    else if ($deal->receivables == 2){
                        $receivables_status = "bg-danger text-white";
                    }
                    elseif ($deal->receivables == 3){
                        $receivables_status = "bg-lightgreen text-dark";
                    }
                    elseif ($deal->receivables == 5){
                        $receivables_status = "bg-lightgreen text-dark";
                    }
                    elseif ($deal->receivables == 4){
                        $receivables_status = "bg-lightgreen text-dark";
                    }
                    elseif ($deal->receivables == 6){
                        $receivables_status = "bg-lightgreen text-dark";
                    }
                    else {
                        $receivables_status = "bg-lightgreen text-dark";
                    }

                    ?>

                    
                    <tr>
                        <th class="<?php echo e($receivables_status); ?> d-flex align-items-center justify-content-between gap-1" style="height:23px">

                           
                        <div class="d-flex align-items-center justify-content-start flex-grow-1 header-height gap-1">
                                <b>Recievable</b>
                            <?php if(($deal->technical==1 && $deal->tech==1) || ($deal->technical==0)): ?>
                                

                                <?php if(App\SysHelper::receivables_approval_access() && $deal->accounts==1 && $deal->sales==1 && $deal->purchease==1 && $deal->invoice==1 && $deal->delivery==1 && $deal->receivables_approval ==1 && in_array($deal->delivery,[0,2,3,1])): ?>
                                <a class="btn-md btn-light" title="Receivables Approval" style="display: contents;" data-bs-toggle="modal" data-bs-target="#modalReceivables"><i class="ico icon-outline-pen-new-square title-15 <?php echo e($receivables_status == 'bg-lightgreen text-dark' ? 'text-dark' : 'text-white'); ?>" style="font-size: 12px"></i></a>
                            <?php endif; ?>
                        <?php endif; ?>
                        </div>

                        <div class="track-stage-actions">
                            <?php if($deal->receivables_approval == 1): ?>
                                <div><button type="button" title="Journal Voucher" onclick="window.location.href='<?php echo e(url('journalvoucher-add-deal/'.$deal->deal_id.'/'.$del->cust_id)); ?>'" target="_blank" class="<?php if($deal->receivables != 1 && $deal->receivables != 2): ?> green-track-action-btn <?php else: ?> track-action-btn  <?php endif; ?> d-inline-flex align-items-center gap-1"> <svg <?php if($deal->receivables != 1 && $deal->receivables != 2): ?> style="height: 11px;margin-top: -2px;" <?php else: ?> style="height:11px;fill:white"  <?php endif; ?>  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M352 128C352 110.3 337.7 96 320 96C302.3 96 288 110.3 288 128L288 288L128 288C110.3 288 96 302.3 96 320C96 337.7 110.3 352 128 352L288 352L288 512C288 529.7 302.3 544 320 544C337.7 544 352 529.7 352 512L352 352L512 352C529.7 352 544 337.7 544 320C544 302.3 529.7 288 512 288L352 288L352 128z"/></svg> JV</button></div>
                            <?php endif; ?>

                            <?php if($deal->receivables_approval == 1): ?>

                                <?php if(count($check_receipt) == 0): ?>
                                    <div><button type="button" title="Receipts" onclick="window.location.href='<?php echo e(url('receipt-add-deal/'.$deal->deal_id.'/'.$deal->payment_mode)); ?>'" target="_blank" class="<?php if($deal->receivables != 1 && $deal->receivables != 2): ?> green-track-action-btn <?php else: ?> track-action-btn  <?php endif; ?> border-0 d-inline-flex align-items-center gap-1"><svg  <?php if($deal->receivables != 1 && $deal->receivables != 2): ?> style="height: 11px;margin-top: -2px;" <?php else: ?> style="height:11px;fill:white"  <?php endif; ?>  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M352 128C352 110.3 337.7 96 320 96C302.3 96 288 110.3 288 128L288 288L128 288C110.3 288 96 302.3 96 320C96 337.7 110.3 352 128 352L288 352L288 512C288 529.7 302.3 544 320 544C337.7 544 352 529.7 352 512L352 352L512 352C529.7 352 544 337.7 544 320C544 302.3 529.7 288 512 288L352 288L352 128z"/></svg> REC</button></div>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php if(count($check_receipt) == 0): ?>
                                            
                            <?php else: ?> 

                            <?php if($check_receipt->sum('amount') < ($t_taxableamount+$t_vatamount-$deal_discount_sum_amount)): ?>
                                <div><button type="button" title="Receipts" onclick="window.location.href='<?php echo e(url('receipt-add-deal/'.$deal->deal_id.'/'.$deal->payment_mode)); ?>'" target="_blank" class="<?php if($deal->receivables != 1 && $deal->receivables != 2): ?> green-track-action-btn <?php else: ?> track-action-btn  <?php endif; ?> border-0 d-inline-flex align-items-center gap-1"><svg  <?php if($deal->receivables != 1 && $deal->receivables != 2): ?> style="height: 11px;margin-top: -2px;" <?php else: ?> style="height:11px;fill:white"  <?php endif; ?>  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M352 128C352 110.3 337.7 96 320 96C302.3 96 288 110.3 288 128L288 288L128 288C110.3 288 96 302.3 96 320C96 337.7 110.3 352 128 352L288 352L288 512C288 529.7 302.3 544 320 544C337.7 544 352 529.7 352 512L352 352L512 352C529.7 352 544 337.7 544 320C544 302.3 529.7 288 512 288L352 288L352 128z"/></svg> REC</button></div>
                            <?php endif; ?>
                    
                            <?php endif; ?>

                        </div>


                            

                    </th>
                
                </tr>

                   
                    <?php if(count($receivables) > 0): ?>
                            <?php $__currentLoopData = $receivables; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($val->payment_collection == 3): ?>
                                    <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Credit Note No</span> : <?php echo e($val->credit_note); ?></td></tr>
                                <?php else: ?>
                                    <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Payment Collection</span> : <?php if($val->payment_collection == 1): ?>
                                            Approved <i class="ico icon-outline-check-read title-15 text-success" aria-hidden="true"></i>
                                        <?php elseif($val->payment_collection == 2): ?>
                                            Disapproved <i class="ico icon-outline-close text-danger" aria-hidden="true"></i>
                                        <?php elseif($val->payment_collection == 3): ?>
                                            Order Cancelled <i class="ico icon-outline-close text-danger" aria-hidden="true"></i>
                                        <?php else: ?>
                                            Pending <i class="ico  icon-outline-clock-circle text-info" aria-hidden="true"></i>
                                <?php endif; ?>
                                </td></tr>

                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Payment Status</span> : <?php if($val->payment_status == 1): ?>
                                        Payment Received <i class="ico icon-outline-check-read title-15 text-success" aria-hidden="true"></i>
                                    <?php elseif($val->payment_status == 2): ?>
                                        Pending <i class="ico icon-outline-close text-danger" aria-hidden="true"></i>
                                    <?php else: ?>
                                        Pending <i class="ico  icon-outline-clock-circle text-info" aria-hidden="true"></i>
                            <?php endif; ?>
                            </td>
                            
    <?php if($val->reminder_date != "1970-01-01" && $val->reminder_date != ""): ?>
    <tr><td class="text-start truncate-text-custom"><b>Reminder Date</b> : <?php echo e(date('d/m/Y h:i:A', strtotime($val->reminder_date))); ?></td></tr><?php endif; ?>


    <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Receipt No</span> : <?php echo e(@$val->doc_number); ?></td></tr>
                        <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Receipt Date</span> : <?php echo e(date('d/m/Y', strtotime(@$val->receipt_date))); ?></td></tr>
                        <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Receipt Mode</span> : <?php echo e(@$val->receiptmode->account_name); ?></td></tr>
                        <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Invoice No</span> : <?php echo e(@$val->invoice_no); ?></td></tr>
                        <tr><td class="text-start truncate-text-custom"><span class="fw-bold">eceipt Through</span> : <?php if(@$val->receipt_through==1): ?> Bank Transfer <?php endif; ?> <?php if(@$val->receipt_through==2): ?> CDC Cheque <?php endif; ?> <?php if(@$val->receipt_through==3): ?> PDC Cheque <?php endif; ?></td></tr>
                        
                            <?php if($val->amount != ''): ?>
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Amount</span> : <?php echo e($val->amount); ?></td></tr>
                            <?php endif; ?>
                            <?php if($val->amount2 != ''): ?>
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Amount</span> : <?php echo e($val->amount2); ?></td></tr>
                            <?php endif; ?>
                            <?php if($val->amount3 != ''): ?>
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Amount</span> : <?php echo e($val->amount3); ?></td></tr>
                            <?php endif; ?>
                            <?php if($val->balance_amount != ""): ?>
                            <tr class=""><td class="text-start truncate-text-custom"><span class="fw-bold">Balance</span> : <?php echo e($val->balance_amount); ?></td></tr>
                            <?php endif; ?>

                            <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Payment Mode</span> :
                                <?php if($val->paymenttype == 1): ?>
                                    Cash
                                <?php endif; ?>
                                <?php if($val->paymenttype == 2): ?>
                                    Cheque
                                <?php endif; ?>
                                <?php if($val->paymenttype == 3): ?>
                                    Bank Transfer
                                <?php endif; ?>
                                <?php if($val->paymenttype == 4): ?>
                                    Open Credit
                                <?php endif; ?>
                                <?php if($val->paymenttype == 5): ?>
                                    Credit Card
                                <?php endif; ?>
                                <?php if($val->paymenttype == 6): ?>
                                    Bank TT
                                <?php endif; ?>
                            </td></tr>

                            <?php if($val->cash_date != '' && $val->cash_date != '1970-01-01'): ?>
                                <tr><td class="text-start truncate-text-custom"><b>Date</b> : <?php echo e(date('d/m/Y', strtotime($val->cash_date))); ?></td></tr>
                            <?php endif; ?>
                            <?php if($val->cash_date2 != '' && $val->cash_date2 != '1970-01-01'): ?>
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Date</span> : <?php echo e(date('d/m/Y', strtotime($val->cash_date2))); ?>

                                </td></tr>
                            <?php endif; ?>
                            <?php if($val->cash_date3 != '' && $val->cash_date3 != '1970-01-01'): ?>
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Date</span> : <?php echo e(date('d/m/Y', strtotime($val->cash_date3))); ?>

                                </td></tr>
                            <?php endif; ?>

                            <?php if($val->cheque_no != ''): ?>
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Cheque No</span> : <?php echo e($val->cheque_no); ?></td></tr>
                            <?php endif; ?>
                            <?php if($val->cheque_date != '1970-01-01' && $val->cheque_date != ''): ?>
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Cheque Date</span> :
                                    <?php echo e(date('d/m/Y', strtotime($val->cheque_date))); ?></td></tr>
                            <?php endif; ?>
                            <?php if($val->cheque_no2 != ''): ?>
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Cheque No</span> : <?php echo e($val->cheque_no2); ?></td></tr>
                            <?php endif; ?>
                            <?php if($val->cheque_date2 != '1970-01-01' && $val->cheque_date2 != ''): ?>
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Cheque Date</span> :
                                    <?php echo e(date('d/m/Y', strtotime($val->cheque_date2))); ?></td></tr>
                            <?php endif; ?>
                            <?php if($val->cheque_no3 != ''): ?>
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Cheque No</span> : <?php echo e($val->cheque_no3); ?></td></tr>
                            <?php endif; ?>
                            <?php if($val->cheque_date3 != '1970-01-01' && $val->cheque_date3 != ''): ?>
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Cheque Date</span> :
                                    <?php echo e(date('d/m/Y', strtotime($val->cheque_date3))); ?></td></tr>
                            <?php endif; ?>

                            <?php if($val->cheque_copy != ''): ?>
                                <tr><td class="text-start truncate-text-custom"><a class="text-info text-xs"
                                        href="<?php echo e(asset('public/uploads/crm_deal_track_doc/')); ?>/<?php echo e($val->cheque_copy); ?>"
                                        target="_blank"><i class="ico icon-bold-download-minimalistic text-white fw-bold title-15" aria-hidden="true"></i> Cheque
                                        Copy</a></td></tr>
                            <?php endif; ?>

                            <?php if($val->bank_name != ''): ?>
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Bank Name</span> : <?php echo e($val->bank_name); ?></td></tr>
                            <?php endif; ?>
                            <?php if($val->deposit_date != '' && $val->deposit_date != '1970-01-01'): ?>
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Deposit Date</span> :
                                    <?php echo e(date('d/m/Y', strtotime($val->deposit_date))); ?></td></tr>
                            <?php endif; ?>
                            <?php if($val->deposit_date2 != '' && $val->deposit_date2 != '1970-01-01'): ?>
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Deposit Date</span> :
                                    <?php echo e(date('d/m/Y', strtotime($val->deposit_date2))); ?></td></tr>
                            <?php endif; ?>

                            <?php if($val->open_credit_date != '' && $val->open_credit_date != '1970-01-01'): ?>
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Open Credit</span> :
                                    <?php echo e(date('d/m/Y', strtotime($val->open_credit_date))); ?></td></tr>
                            <?php endif; ?>

                            <?php if($val->credit_card_type != ''): ?>
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Credit Card</span> : <?php echo e($val->credit_card_type); ?></td></tr>
                            <?php endif; ?>
                            <?php if($val->payment_date != '' && $val->payment_date != '1970-01-01'): ?>
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Payment Date</span> :
                                    <?php echo e(date('d/m/Y', strtotime($val->payment_date))); ?></td></tr>
                            <?php endif; ?>
                            <?php if($val->credit_card_deposit_date != '' && $val->credit_card_deposit_date != '1970-01-01'): ?>
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Deposit Date</span> :
                                    <?php echo e(date('d/m/Y', strtotime($val->credit_card_deposit_date))); ?></td></tr>
                            <?php endif; ?>

                            <?php if($val->banktt_date != '' && $val->banktt_date != '1970-01-01'): ?>
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">BankTT Date</span> :
                                    <?php echo e(date('d/m/Y', strtotime($val->banktt_date))); ?></td></tr>
                            <?php endif; ?>
                            <?php if($val->banktt_date2 != '' && $val->banktt_date2 != '1970-01-01'): ?>
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">BankTT Date</span> :
                                    <?php echo e(date('d/m/Y', strtotime($val->banktt_date2))); ?></td></tr>
                            <?php endif; ?>
                            <?php if($val->banktt_date3 != '' && $val->banktt_date3 != '1970-01-01'): ?>
                                <tr><td class="text-start truncate-text-custom"><span class="fw-bold">BankTT Date</span> :
                                    <?php echo e(date('d/m/Y', strtotime($val->banktt_date3))); ?></td></tr>
                            <?php endif; ?>
                            <?php if($val->banktt_copy != ''): ?>
                                <tr><td class="text-start truncate-text-custom"><a class="btn-sm text-white btn-primary"
                                        href="<?php echo e(asset('public/uploads/crm_deal_track_doc/')); ?>/<?php echo e($val->banktt_copy); ?>"
                                        target="_blank"><i class="ico icon-bold-download-minimalistic text-white fw-bold title-15" aria-hidden="true"></i> BankTT
                                        Copy</a></td></tr>
                            <?php endif; ?>
                        <?php endif; ?>



                        <?php if($val->remarks != ''): ?>
                            <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Remarks</span> : <?php echo $val->remarks; ?></td></tr>
                        <?php endif; ?>
                        <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Created By</span> :
                                <?php echo e($val->createdby->full_name); ?></td></tr>
                       <tr><td class="text-start truncate-text-custom"><span class="fw-bold">Created At</span> :
                                <?php echo e(date('d/m/Y h:i A', strtotime($val->created_at))); ?></td></tr>
                        
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>



                    


                            <tr class="bg-white">
                                <td class=" d-flex align-items-center gap-1 flex-wrap">

                                        <script>
                        $(document).ready(function () {
                                // Delegated click works for both static + dynamic .data-item
                                $(document).on('click', '.rec-data-item', function () {
                                    
                                    $("#loading_bg").css("display", "block");

                                    var id = $(this).data('id');

                                    var action = "<?php echo e(URL::to('receipt-details-pdf')); ?>/" + id;

                                    $.ajax({            
                                        url: action,
                                        method: 'GET',
                                        success: function (response) {
                                             $('#recViewModalbody').html(response);   // load inside modal
                                $('#recViewModal').modal('show');  
                                        },
                                        error: function () {
                                            $('#data-details').html('<p class="text-danger">Error loading details.</p>');
                                        },
                                        complete: function () {
                                            // always hide loading, success or error
                                            $("#loading_bg").css("display", "none");
                                        }
                                    });
                                });
                            });
                        </script>

          <script>
                        $(document).ready(function () {
                                // Delegated click works for both static + dynamic .data-item
                                $(document).on('click', '.jv-data-item', function () {
                                    
                                    $("#loading_bg").css("display", "block");

                                    var id = $(this).data('id');

                                    var action = "<?php echo e(URL::to('jv-details-pdf')); ?>/" + id;

                                    $.ajax({            
                                        url: action,
                                        method: 'GET',
                                        success: function (response) {
                                             $('#jvViewModalbody').html(response);   // load inside modal
                                $('#jvViewModal').modal('show');  
                                        },
                                        error: function () {
                                            $('#data-details').html('<p class="text-danger">Error loading details.</p>');
                                        },
                                        complete: function () {
                                            // always hide loading, success or error
                                            $("#loading_bg").css("display", "none");
                                        }
                                    });
                                });
                            });
                        </script>

                                     <?php if($deal->receivables_approval == 1): ?>
                                            
                                            <?php if(count($check_jv) > 0): ?>
                                             
                                                    <?php $__currentLoopData = $check_jv; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <a class="btn-sm btn-success p-0 jv-data-item cursor-pointer text-white" data-id="<?php echo e($cr->id); ?>" style="font-size: 10px;" >&nbsp;<?php echo e($cr->doc_number); ?>&nbsp;</a>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                

                                            <?php endif; ?>


                                            <?php if(count($check_receipt) == 0): ?>
                                            
                                            <?php else: ?>

                                            <?php if(count($check_receipt)): ?>
                                          
                                                <?php $__currentLoopData = $check_receipt; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <a class="btn-sm btn-success p-0 rec-data-item cursor-pointer text-white" data-id="<?php echo e($cr->id); ?>" style="font-size: 10px;">&nbsp;<?php echo e($cr->doc_number); ?>&nbsp;</a>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                          
                                            <?php endif; ?>

                                          
                                    
                                            <?php endif; ?>
                                        <?php endif; ?>
                                </td>
                            </tr>

                    </table>

                </div>
            </div>

</div>
<!-- Modal -->
<div class="modal side-panel fade" id="modalUpdateItems" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Update Products</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deals-delivery-update-items', 'method' => 'POST', 'id' => 'crm-deals-delivery-update-items'])); ?>

      <div class="modal-body">        
        <?php if(count($quoteitems) > 0): ?>
        <table class="table table-nowrap table-centered mb-0 table-striped">
            <thead>
                <tr>
                    <th>Part Number</th>
                    <th>Description</th>
                    <th>Quote Qty</th>
                    <th>Delivery Qty</th>
                    <th>check</th>
                </tr>
            </thead>                                
        <?php $t_qty = 0; $t_price = 0; $t_discount = 0; $t_net_amount = 0; ?>
            <tbody>
                <input type="hidden" name="update_item_deal_id" value="<?php echo e($deal->deal_id); ?>" />
                <?php $__currentLoopData = $quoteitems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $Item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php try{ ?> <?php echo e($Item->productname->part_number); ?> <?php }catch (\Exception $e){} ?></td>
                    <td><div style="width:150px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;"><?php echo nl2br($Item->description); ?></div></td>
                    <td><?php echo e($Item->qty); ?></td>
                    <td><input type="number" class="form-control" name="qty_<?php echo e($Item->id); ?>"></td>
                    <td>
                        <input type="checkbox" class="form-control" id="check_bx" name="checkbx[]" value="<?php echo e($Item->id); ?>">
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <?php endif; ?>
      </div>
      <div class="modal-footer">
      
        <button type="submit" class="btn btn-primary">Update</button>
      </div>
      <?php echo e(Form::close()); ?>

    </div>
  </div>
</div>




<!-- Modal GRN-->
<script>
    function set_no($id)
    {
        $("#grn_id").val($id);
    }
</script>
<div class="modal side-panel fade" id="ModalGRN" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add GRN No</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-grn-no-update', 'method' => 'POST', 'enctype' => 'multipart/form-data'])); ?>

            <input type="hidden" name="grn_id" id="grn_id" />
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-10">
                        <div class="mb-3">
                            <label for="" class="form-label">GRN NO</label>
                            <input type="text" class="form-control" name="grn_no" id="grn_no" required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Update GRN No</button>
            </div>
            <?php echo e(Form::close()); ?>

        </div>
    </div>
</div>
<!-- Modal GRN-->


<div class="modal side-panel  fade" id="po_pending_items_popup_win" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg draggable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title ps-0">Purchase Order Pending List</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="container-fluid">
                            <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'add-selected-deal-items-to-purchase-order-cart', 'method' => 'POST', 'enctype' => 'multipart/form-data'])); ?>

                            <div class="row">
                                <div class="col-lg-12 p-0">
                                    <div class="">
                                        <table  class="table table-hover" id="long-list" style="table-layout: fixed;width:100%">
                                            <thead>
                                                <tr >
                                                    <th style="width:15px"><input type="checkbox" id="po_check_all" onclick="po_check_fun()" checked/>
                                                    <script>
                                                        function po_check_fun(){
                                                            if($("#po_check_all").prop('checked') == true){
                                                                $('.po_check').prop('checked', true);
                                                            } else{
                                                                $('.po_check').prop('checked', false);
                                                            }
                                                        }
                                                    </script>
                                                    </th>
                                                    <th style="width:90px"><?php echo app('translator')->getFromJson('Part No'); ?></th>
                                                    <th style="width:100px"><?php echo app('translator')->getFromJson('Description'); ?></th>
                                                    <th style="width:30px" class="text-center"><?php echo app('translator')->getFromJson('Deal'); ?></th>
                                                    <th style="width:30px" class="text-center"><?php echo app('translator')->getFromJson('Exe'); ?></th>
                                                    <th style="width:30px" class="text-center"><?php echo app('translator')->getFromJson('Qty'); ?></th>
                                                    <th style="width:60px" class="text-end"><?php echo app('translator')->getFromJson('Unit Price'); ?></th>
                                                    <th style="display: none;" class="text-end"><?php echo app('translator')->getFromJson('Discount'); ?></th>
                                                    <th style="width:60px" class="text-end"><?php echo app('translator')->getFromJson('Value'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if(count($quoteitems)>0): ?>
                                                <?php $__currentLoopData = $quoteitems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $Item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                               
                                                <?php if($Item->cost != '0.00'){
                                                    $up = $Item->cost;
                                                } else {
                                                    $up = $Item->price;
                                                } ?>

                                                <?php if((int)$Item->qty > (int)$Item->po_qty): ?>
                                                <tr>
                                                    <td class="no-toggle">
                                                        <input type="checkbox" class="po_check" name="selected_item_id[]" checked value="<?php echo e($Item->id); ?>" />
                                                        <input type="hidden" name="roids[]" value="<?php echo e($Item->id); ?>" />
                                                    </td>
                                                    <td><?php try{ ?> <?php echo e($Item->productname->part_number); ?> <?php }catch (\Exception $e){} ?></td>
                                                    <td><?php echo $Item->description; ?></td>
                                                    <td class="text-center"><?php echo e($Item->qty); ?></td>
                                                    <td class="text-center">0</td>
                                                    <td class="no-toggle"><input type="number" name="qty[]" class="form-control text-center border-0 po-qty" value="<?php echo e(abs($Item->qty-$Item->po_qty)); ?>" /></td>
                                                    <td class="text-end pe-0 no-toggle"><input name="unitprice[]" type="text" class="form-control text-end border-0 po-unitprice" value="<?php echo e(@App\SysHelper::com_curr_format($up,2,'.','')); ?>" /></td>
                                                    <td style="display: none;" class="text-end"><input name="discount[]" type="text" class="form-control text-end border-0" value="<?php echo e(@App\SysHelper::com_curr_format($Item->discount,2,'.',',')); ?>"/></td>
                                                    <td class="text-end pe-0 no-toggle"><input name="value[]" type="text" readonly class="form-control text-end border-0 po-value" value="<?php echo e(@App\SysHelper::com_curr_format((($up * $Item->qty)),2,'.',',')); ?>"/></td>
                                                </tr>
                                                <input type="hidden" name="product_id[]" value="<?php echo e($Item->product_id); ?>" />
                                                <input type="hidden" name="deal_id[]" value="<?php echo e($Item->deal_id); ?>" />
                                                <input type="hidden" name="deal_code" value="<?php echo e($deal->deal_code->code); ?>" />
                                                <input type="hidden" name="item_id[]" value="<?php echo e($Item->id); ?>" />
                                                <input type="hidden" name="deal_qty[]" value="<?php echo e($Item->qty); ?>" />
                                                <input type="hidden" name="tax[]" value="<?php echo e($Item->vat); ?>" />
                                                <input type="hidden" name="description[]" value="<?php echo e($Item->description); ?>" />
                                                <?php endif; ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        <div class="modal-footer d-flex justify-content-center p-0">
    <input type="hidden" name="req_deal_id" value="<?php echo e($del->id); ?>" />
    <button type="submit" class="btn btn-light add-btn ms-2">
        <i class="ico icon-outline-bookmark-opened text-success"></i> Submit
    </button>
</div>

                            <?php echo e(Form::close()); ?>

                        <script>
                            (function(){
                                // DLN modal: calculate line value when qty, unitprice or discount change
                                function parseNumber(v){ if (v === undefined || v === null) return 0; var s = String(v).trim(); s = s.replace(/,/g,'').replace(/[^0-9.\-\.]/g,''); var f = parseFloat(s); return Number.isFinite(f) ? f : 0; }
                                function formatNumber(v){ return (Number(v)||0).toLocaleString(undefined,{minimumFractionDigits:2, maximumFractionDigits:2}); }

                                var $modal = $('#dln_pending_items_popup_win');
                                function recalcRow($row){ var qty = parseNumber($row.find('.dln-qty').val()); var unit = parseNumber($row.find('.dln-unitprice').val()); var disc = parseNumber($row.find('.dln-discount').val()); var value = (qty * unit) - disc; $row.find('.dln-value').val(formatNumber(value)); }

                                function debounce(fn, wait){ let t; return function(){ const args = arguments; clearTimeout(t); t = setTimeout(()=> fn.apply(this, args), wait); }; }

                                $modal.on('input change', '.dln-qty, .dln-unitprice, .dln-discount', debounce(function(){ recalcRow($(this).closest('tr')); }, 120));

                                $modal.on('blur', '.dln-unitprice, .dln-discount', function(){ var n = parseNumber($(this).val()); $(this).val(formatNumber(n)); });
                            })();
                        </script>
                        
                        <script>
                            (function(){
                                // PO modal: calculate PO line value when qty or unit price change
                                function parseNumber(v){
                                    if (v === undefined || v === null) return 0;
                                    var s = String(v).trim(); s = s.replace(/,/g,'').replace(/[^0-9.\-]/g, '');
                                    var f = parseFloat(s); return Number.isFinite(f) ? f : 0;
                                }
                                function formatNumber(v){ return (Number(v)||0).toLocaleString(undefined,{minimumFractionDigits:2, maximumFractionDigits:2}); }

                                var $modal = $('#po_pending_items_popup_win');
                                var recalc = function($row){
                                    var qty = parseNumber($row.find('.po-qty').val());
                                    var unit = parseNumber($row.find('.po-unitprice').val());
                                    var value = qty * unit;
                                    $row.find('.po-value').val(formatNumber(value));
                                };

                                // debounce util
                                function debounce(fn, wait){ let t; return function(){ const args = arguments; clearTimeout(t); t = setTimeout(()=> fn.apply(this, args), wait); }; }

                                $modal.on('input change', '.po-qty, .po-unitprice', debounce(function(){
                                    recalc($(this).closest('tr'));
                                }, 120));

                                $modal.on('blur', '.po-unitprice', function(){
                                    var n = parseNumber($(this).val()); $(this).val(formatNumber(n));
                                });
                            })();
                        </script>
                        </div>
                    </div>
                </div>
            </div>
        </div>



<div class="modal side-panel fade" id="si_pending_items_popup_win" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg draggable">
                <div class="modal-content">
                    <div class="modal-header m-0 ">
                        <h4 class="modal-title ps-0">Sales Invoice Pending List</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body m-0 p-0">
                        <div class="container-fluid">
                            <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'add-selected-deal-items-to-sales-invoice-cart', 'method' => 'POST', 'enctype' => 'multipart/form-data'])); ?>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="equipment comon-status row mt-40 d-block">
                                        <table id="long-list" class="table table-hover" style="table-layout: fixed;width:100%">
                                            <thead>
                                                <tr>
                                                    <th style="width:15px"><input type="checkbox" id="si_check_all" onclick="si_check_fun()" checked/>
                                                        <script>
                                                            function si_check_fun(){
                                                                if($("#si_check_all").prop('checked') == true){
                                                                    $('.si_check').prop('checked', true);
                                                                } else{
                                                                    $('.si_check').prop('checked', false);
                                                                }
                                                            }
                                                        </script>
                                                    </th>
                                                    <th style="width:90px"><?php echo app('translator')->getFromJson('Part Number'); ?></th>
                                                    <th style="width:100px"><?php echo app('translator')->getFromJson('Description'); ?></th>
                                                    <th class="text-center" style="width:30px"><?php echo app('translator')->getFromJson('Deal Qty'); ?></th>
                                                    <th class="text-center" style="width:30px"><?php echo app('translator')->getFromJson('Executed Qty'); ?></th>
                                                    <th class="text-center" style="width:30px"><?php echo app('translator')->getFromJson('Qty'); ?></th>
                                                    <th class="text-end" style="width:60px"><?php echo app('translator')->getFromJson('Unitprice'); ?></th>
                                                    <th class="text-end" style="width:60px"><?php echo app('translator')->getFromJson('Value'); ?></th>
                                                    <th class="text-end" style="width:60px"><?php echo app('translator')->getFromJson('Discount'); ?></th>
                                                    <th class="text-end" style="width:60px"><?php echo app('translator')->getFromJson('Taxable'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if(count($quoteitems)>0): ?>
                                              
                                                <?php $__currentLoopData = $quoteitems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $Item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                
                                                <?php
                                                 /*if($Item->cost != '0.00'){
                                                    $up = $Item->cost;
                                                } else {*/
                                                    $up = $Item->price;
                                                /*}*/ ?>

                                                <?php if((int)$Item->qty > (int)$Item->si_qty): ?>
                                                <tr>
                                                    <td class="no-toggle">
                                                        <input class="si_check" type="checkbox" name="selected_item_id[]" checked value="<?php echo e($Item->id); ?>" />
                                                        <input type="hidden" name="roids[]" value="<?php echo e($Item->id); ?>" />
                                                    </td>
                                                    <td><?php try{ ?> <?php echo e($Item->productname->part_number); ?> <?php }catch (\Exception $e){} ?></td>
                                                    <td><?php echo $Item->description; ?></td>
                                                    <td class="text-center"><?php echo e($Item->qty); ?></td>
                                                    <td class="text-center">0</td>
                                                    <td class="no-toggle text-center"><input type="number" name="qty[]" class="form-control text-center border-0 si-qty" value="<?php echo e(abs($Item->qty-$Item->si_qty)); ?>" /></td>
                                                    <td class="text-end no-toggle pe-0"><input name="unitprice[]" type="text" step="any" class="form-control text-end border-0 si-unitprice" value="<?php echo e(@App\SysHelper::com_curr_format($up,2,'.','')); ?>" /></td>
                                                    
                                                    <td class="text-end no-toggle pe-0"><input name="value[]" type="text" readonly class="form-control text-end border-0 si-taxableamt" value="<?php echo e(@App\SysHelper::com_curr_format((($up * $Item->qty)),2,'.','')); ?>"/></td>
                                               <td class="text-end no-toggle pe-0"><input name="discount[]" type="text" step="any" class="form-control text-end border-0 si-discount" value="<?php echo e(@App\SysHelper::com_curr_format($Item->discount,2,'.','')); ?>"/></td>
                                                    <td class="text-end no-toggle pe-0"><input name="taxableamt[]" type="text" readonly class="form-control text-end border-0 si-value" value="<?php echo e(@App\SysHelper::com_curr_format((($up * $Item->qty) - ($Item->discount)),2,'.','')); ?>"/></td>
                                               
                                            </tr>
                                                <input type="hidden" name="product_id[]" value="<?php echo e($Item->product_id); ?>" />
                                                <input type="hidden" name="part_number[]" value="<?php echo e($Item->productname->part_number); ?>" />
                                                <input type="hidden" name="deal_id[]" value="<?php echo e($Item->deal_id); ?>" />
                                                <input type="hidden" name="deal_code" value="<?php echo e($deal->deal_code->code); ?>" />
                                                <input type="hidden" name="item_id[]" value="<?php echo e($Item->id); ?>" />
                                                <input type="hidden" name="deal_qty[]" value="<?php echo e($Item->qty); ?>" />
                                                <input type="hidden" name="tax[]" value="<?php echo e($Item->vat); ?>" />
                                                <input type="hidden" name="description[]" value="<?php echo e($Item->description); ?>" />
                                                <?php endif; ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php endif; ?>
                                                <td colspan="9" style="text-align: center; height: 19px;">
    <!-- You can put placeholder text here if needed -->
  </td>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                      <?php if(count($quote_charges) > 0 || count($list_journalvoucher_det)>0): ?>
                             
                            <table class="table table-hover" id="long-list" style="table-layout: fixed;width:100%">
                                <thead>
                                    <tr>
                                        <th style="width:30px;"></th>
                                        <th style="width:260px;" class="text-start">Selling Exp Account</th>
                                        <th style="width:240px;" class="text-start">Credit Account</th>
                                        <th style="width:65px;" class="text-end">Amount</th>
                                        <th class="text-start" style="padding-left:50px">Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>                                    
                                <?php if(count($quote_charges) > 0): ?>
                                <?php $__currentLoopData = $quote_charges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $charges): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="no-toggle">
                                        <input class="si_charge_check form-check-input" type="checkbox"
                                            name="selling_exp_account_id[]"
                                            value="<?php echo e($charges->selling_exp_account); ?>"
                                            id="si_charge_<?php echo e($charges->id); ?>"
                                            data-charge-id="<?php echo e($charges->id); ?>"
                                            checked />

                                        <input type="hidden" class="selling-exp-amount"
                                            name="selling_exp_account_amount[]"
                                            value="<?php echo e($charges->amount); ?>"
                                            data-charge-id="<?php echo e($charges->id); ?>" />

                                        <input type="hidden" class="selling-exp-credit-account"
                                            name="selling_exp_credit_account[]"
                                            value="<?php echo e($charges->credit_account); ?>"
                                            data-charge-id="<?php echo e($charges->id); ?>" />

                                        <input type="hidden" class="selling-exp-remarks"
                                            name="selling_exp_remarks[]"
                                            value="<?php echo e($charges->remarks); ?>"
                                            data-charge-id="<?php echo e($charges->id); ?>" />
                                    </td>
                                    <td class="text-start"><?php echo e($charges->sellingexpaccount->account_name); ?></td>
                                    <td class="text-start"><?php echo e($charges->creditaccount->account_name); ?></td>
                                    <td class="text-end"><?php echo e($charges->amount); ?></td>
                                    <td class="text-start" style="padding-left:50px"><?php echo e($charges->remarks); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                                
                            <?php if(count($list_journalvoucher_det)>0): ?>
                            <?php $total_jv_amount=0; ?>
                            <?php $__currentLoopData = $list_journalvoucher_det; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jv_det): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            
                            <?php $main_acc=$list_journalvoucher_det_other->where('is_main_account',0)->where('transaction_no',$jv_det->transaction_no)->where('credit_amount',$jv_det->debit_amount)->max('account_name');
                            $main_acc_credit_amount = $list_journalvoucher_det->where('is_main_account',1)->where('transaction_no',$jv_det->transaction_no)->max('credit_amount'); ?>

                            <?php if($jv_det->debit_amount > 0): ?>
                            <tr>
                                <td class="no-toggle">
                                    <input class="jv_det_check form-check-input" type="checkbox"
                                        name="jv_det_id[]"
                                        value="<?php echo e($jv_det->id); ?>"
                                        id="jv_det_<?php echo e($jv_det->id); ?>"
                                        data-jv-id="<?php echo e($jv_det->id); ?>"
                                        checked />
                                    <input type="hidden" class="jv-det-amount"
                                        name="jv_det_amount[]"
                                        value="<?php echo e($jv_det->debit_amount); ?>"
                                        data-jv-id="<?php echo e($jv_det->id); ?>" />
                                    <input type="hidden" class="jv-det-credit-account"
                                        name="jv_det_credit_account[]"
                                        value="<?php echo e($list_journalvoucher_det_other->where('transaction_no',$jv_det->transaction_no)->where('credit_amount',$jv_det->debit_amount)->max('account_id')); ?>"
                                        data-jv-id="<?php echo e($jv_det->id); ?>" />
                                    <input type="hidden" class="jv-det-remarks"
                                        name="jv_det_remarks[]"
                                        value="<?php echo e($jv_det->remarks); ?>"
                                        data-jv-id="<?php echo e($jv_det->id); ?>" />
                                </td>
                                <td class="text-start"><?php echo e($jv_det->account_name); ?></td>
                                <td class="text-start"><?php echo e($main_acc); ?></td>
                                <td class="text-right"><?php echo e(@App\SysHelper::com_curr_format($jv_det->debit_amount,2,'.',',')); ?> <?php $total_jv_amount += $jv_det->debit_amount; ?></td>
                                <td class="text-start pl-5"><?php echo e($jv_det->remarks); ?> &nbsp; [ <?php echo e($jv_det->transaction_no); ?> ]</td>
                            </tr>
                            <?php endif; ?>
                            
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="text-start"></td>
                                <td class="text-start font-weight-bold">Total Expenses</td>
                                <td class="text-right font-weight-bold"><?php echo e(@App\SysHelper::com_curr_format($total_jv_amount,2,'.',',')); ?></td>
                                <td class="text-start pl-5"></td>
                            </tr>
                            <?php endif; ?>

                                </tbody>
                            </table>
                              <br>
                            <?php endif; ?>

                            </div>

                          <div class="modal-footer p-0">
                                <button type="submit" class="btn btn-light add-btn ms-2">
                                    <i class="ico icon-outline-bookmark-opened text-success"></i> Submit
                                </button>
                            </div>
                            <?php echo e(Form::close()); ?>

                        </div>

                        <script>
                            (function(){
                                // Debounce helper
                                function debounce(fn, wait){ let t; return function(){ const args = arguments; clearTimeout(t); t = setTimeout(()=> fn.apply(this, args), wait); }; }

                                function parseNumber(val){
                                    if (val === undefined || val === null) return 0;
                                    var s = String(val).trim();
                                    // remove all commas and any non-digit except dot and minus
                                    s = s.replace(/,/g,'').replace(/[^0-9.\-]/g, '');
                                    var f = parseFloat(s);
                                    return Number.isFinite(f) ? f : 0;
                                }

                                function formatNumber(val){
                                    // format to 2 decimals with thousands separator
                                    var n = Number(val) || 0;
                                    return n.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
                                }

                                const $modal = $('#si_pending_items_popup_win');

                                function recalcRow($row){
                                    var qty = parseNumber($row.find('.si-qty').val());
                                    var unit = parseNumber($row.find('.si-unitprice').val());
                                    var disc = parseNumber($row.find('.si-discount').val());
                                    var taxableamt = qty * unit;
                                    $row.find('.si-taxableamt').val(formatNumber(taxableamt));
                                    var value = (qty * unit) - disc;
                                    $row.find('.si-value').val(formatNumber(value));
                                }

                                const handler = debounce(function(e){
                                    var $row = $(this).closest('tr');
                                    recalcRow($row);
                                }, 120);

                                $modal.on('input change', '.si-qty, .si-unitprice, .si-discount', handler);

                                // Format unit price and discount on blur
                                $modal.on('blur', '.si-unitprice, .si-discount', function(){
                                    var n = parseNumber($(this).val());
                                    $(this).val(formatNumber(n));
                                });

                            })();
                        </script>

                        <script>
                            (function(){
                                var modal = document.getElementById('si_pending_items_popup_win');
                                if (!modal) return;

                                function toggleAmountInput(chk) {
                                    var id = chk.dataset.chargeId || chk.dataset.jvId;
                                    if (!id) return;

                                    modal.querySelectorAll('input[data-charge-id="' + id + '"] , input[data-jv-id="' + id + '"]').forEach(function(input){
                                        input.disabled = !chk.checked;
                                    });
                                }

                                modal.addEventListener('change', function (e) {
                                    if (e.target && (e.target.classList.contains('si_charge_check') || e.target.classList.contains('jv_det_check'))) {
                                        toggleAmountInput(e.target);
                                    }
                                });

                                modal.addEventListener('shown.bs.modal', function () {
                                    modal.querySelectorAll('input.si_charge_check, input.jv_det_check').forEach(toggleAmountInput);
                                });
                            })();
                        </script>
                        </div>
                    </div>
                </div>
            </div>
</div>



<div class="modal side-panel fade" id="dln_pending_items_popup_win" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg draggable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title ps-0">Delivery Note Pending List</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body m-0 p-0">
                        <div class="container-fluid">
                            <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'add-deal-items-to-dln-cart', 'method' => 'POST', 'enctype' => 'multipart/form-data'])); ?>

                            <div class="row">
                                <div class="col-lg-12 p-0">
                                    <div class="">
                                        <table id="long-list" class="table table-hover" style="table-layout: fixed;width:100%">
                                            <thead>
                                                <tr>
                                                    <th style="width:15px"><input type="checkbox" id="dl_check_all" onclick="dl_check_fun()" checked/>
                                                        <script>
                                                            function dl_check_fun(){
                                                                if($("#dl_check_all").prop('checked') == true){
                                                                    $('.dl_check').prop('checked', true);
                                                                } else{
                                                                    $('.dl_check').prop('checked', false);
                                                                }
                                                            }
                                                        </script>
                                                    </th>
                                                    <th style="width:90px"><?php echo app('translator')->getFromJson('Part No'); ?></th>
                                                    
                                                    <th style="width:30px" class="text-center"><?php echo app('translator')->getFromJson('Deal'); ?></th>
                                                    <th style="width:30px" class="text-center"><?php echo app('translator')->getFromJson('Exe'); ?></th>
                                                    <th style="width:30px" class="text-center"><?php echo app('translator')->getFromJson('Qty'); ?></th>
                                                    <th class="text-end" style="width:60px"><?php echo app('translator')->getFromJson('Unitprice'); ?></th>
                                                      <th class="text-end" style="width:60px"><?php echo app('translator')->getFromJson('Value'); ?></th>
                                                    <th class="text-end" style="width:60px"><?php echo app('translator')->getFromJson('Discount'); ?></th>
                                                      <th class="text-end" style="width:60px"><?php echo app('translator')->getFromJson('Taxable'); ?></th>
                                                  
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if(count($quoteitems)>0): ?>
                                                <?php $__currentLoopData = $quoteitems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $Item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    $up = $Item->price;
                                                ?>

                                                <?php if((int)$Item->qty > (int)$Item->dn_qty): ?>
                                                <tr>
                                                    <td>
                                                        <input class="dl_check" type="checkbox" name="selected_item_id[]" checked value="<?php echo e($Item->id); ?>" />
                                                        <input type="hidden" name="roids[]" value="<?php echo e($Item->id); ?>" />
                                                    </td>
                                                    <td><?php try{ ?> <?php echo e($Item->productname->part_number); ?> <?php }catch (\Exception $e){} ?></td>
                                                    
                                                    <td class="text-center"><?php echo e($Item->qty); ?></td>
                                                    <td class="text-center">0</td>
                                                    <td class="text-center"><input type="number" name="qty[]" class="form-control text-center border-0 dln-qty delivery-qty" value="<?php echo e(abs($Item->qty-$Item->dn_qty)); ?>" /></td>
                                                    <td class="text-end pe-0"><input name="unitprice[]" type="text" step="any" class="form-control text-end border-0 dln-unitprice delivery-unitprice" value="<?php echo e(@App\SysHelper::com_curr_format($up,2,'.','')); ?>" /></td>
                                                    <td class="text-end pe-0"><input name="value[]" type="text" readonly class="form-control text-end border-0 dln-value delivery-taxable" value="<?php echo e(@App\SysHelper::com_curr_format((($up * $Item->qty)),2,'.','')); ?>"/></td>
                                                  
                                                    <td class="text-end pe-0"><input name="discount[]" type="text" step="any" class="form-control text-end border-0 dln-discount delivery-discount" value="<?php echo e(@App\SysHelper::com_curr_format($Item->discount,2,'.','')); ?>"/></td>
                                                    <td class="text-end pe-0"><input name="taxableamt[]" type="text" readonly class="form-control text-end border-0 dln-value delivery-value" value="<?php echo e(@App\SysHelper::com_curr_format((($up * $Item->qty) - ($Item->discount)),2,'.','')); ?>"/></td>
                                                 <input type="hidden" name="product_id[]" value="<?php echo e($Item->product_id); ?>" />
                                                <input type="hidden" name="part_no_text[]" value="<?php echo e($Item->productname->part_number); ?>" />
                                                <input type="hidden" name="deal_id" value="<?php echo e($Item->deal_id); ?>" />
                                                <input type="hidden" name="deal_code" value="<?php echo e($deal->deal_code->code); ?>" />
                                                <input type="hidden" name="item_id[]" value="<?php echo e($Item->id); ?>" />
                                                <input type="hidden" name="deal_qty[]" value="<?php echo e($Item->qty); ?>" />
                                                <input type="hidden" name="tax[]" value="<?php echo e($Item->vat); ?>" />
                                                <input type="hidden" name="description[]" value="<?php echo e($Item->description); ?>" />
                                                </tr>
                                               
                                                <?php endif; ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php endif; ?>

                               <tr><td colspan="8">&nbsp;</td></tr>


                                                <?php if(count($poitems)>0): ?>
                                                <tr><th colspan="9">Aditional Items</th></tr>
                                                <?php $__currentLoopData = $poitems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $Item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if((int)$Item->qty > (int)$Item->dn_qty): ?>
                                                <tr>
                                                    <td>
                                                        <input class="dl_check" type="checkbox" name="selected_item_id[]" checked value="a_<?php echo e($Item->id); ?>" />
                                                        <input type="hidden" name="roids[]" value="a_<?php echo e($Item->id); ?>" />
                                                    </td>
                                                    <td><?php echo e($Item->partno); ?></td>
                                                    <td class="text-center"><?php echo e($Item->qty); ?></td>
                                                    <td class="text-center">0</td>
                                                    <td><input type="number" name="qty[]" class="form-control border-0 dln-qty text-center  delivery-qty" value="<?php echo e(abs($Item->qty-$Item->dn_qty)); ?>" /></td>
                                                    <td class="text-end pe-0"><input name="unitprice[]" type="text" step="any" class="form-control text-end border-0 dln-unitprice delivery-unitprice" value="0" /></td>
                                                    <td class="text-end pe-0"><input name="value[]" type="text" readonly class="form-control text-end border-0 dln-value delivery-taxable" value="<?php echo e(@App\SysHelper::com_curr_format((($up * $Item->qty)),2,'.','')); ?>"/></td>
                                                   
                                                    <td class="text-end pe-0"><input name="discount[]" type="text" step="any" class="form-control text-end border-0 dln-discount delivery-discount" value="0"/></td>
                                                    <td class="text-end pe-0"><input name="taxableamt[]" type="text" readonly class="form-control text-end border-0 dln-value delivery-value" value="0"/></td>
                                                 <input type="hidden" name="product_id[]" value="<?php echo e($Item->part_number); ?>" />
                                                <input type="hidden" name="part_no_text[]" value="<?php echo e($Item->partno); ?>" />
                                                <input type="hidden" name="deal_id" value="<?php echo e($quoteitems[0]->deal_id); ?>" />
                                                <input type="hidden" name="deal_code" value="<?php echo e($deal->deal_code->code); ?>" />
                                                <input type="hidden" name="item_id[]" value="<?php echo e($Item->id); ?>" />
                                                <input type="hidden" name="deal_qty[]" value="<?php echo e($Item->qty); ?>" />
                                                <input type="hidden" name="tax[]" value="<?php echo e($quoteitems[0]->vat); ?>" />
                                                <input type="hidden" name="description[]" value="<?php echo e($Item->description); ?>" />
                                                </tr>
                                              
                                                <?php endif; ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php endif; ?>

                                                <tr>
  <td colspan="8" style="text-align:center; height:19px;">&nbsp;</td>
</tr>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="moda-footer p-0 mt-1 mb-1">
                                <div class="col-lg-12">
                                    <div class="col-lg-12 text-end d-flex justify-content-center">
                                        <button type="submit" class="btn btn-light add-btn ms-2">
                                            <i class="ico icon-outline-bookmark-opened text-success"></i> Submit
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php echo e(Form::close()); ?>

                        </div>
                    </div>
                </div>
            </div>

              <script>
                            (function(){
                                // Debounce helper
                                function debounce_dl(fn, wait){ let t; return function(){ const args = arguments; clearTimeout(t); t = setTimeout(()=> fn.apply(this, args), wait); }; }

                                function parseNumber_dl(val){
                                    if (val === undefined || val === null) return 0;
                                    var s = String(val).trim();
                                    // remove all commas and any non-digit except dot and minus
                                    s = s.replace(/,/g,'').replace(/[^0-9.\-]/g, '');
                                    var f = parseFloat(s);
                                    return Number.isFinite(f) ? f : 0;
                                }

                                function formatNumber_dl(val){
                                    // format to 2 decimals with thousands separator
                                    var n = Number(val) || 0;
                                    return n.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
                                }

                                const $modal = $('#dln_pending_items_popup_win');

                                function recalcRow_dl($row){
                                    var qty = parseNumber_dl($row.find('.delivery-qty').val());
                                    var unit = parseNumber_dl($row.find('.delivery-unitprice').val());
                                    var disc = parseNumber_dl($row.find('.delivery-discount').val());
                                    var taxableamt = qty * unit;
                                    $row.find('.delivery-taxable').val(formatNumber_dl(taxableamt));
                                    var value = (qty * unit) - disc;
                                    $row.find('.delivery-value').val(formatNumber_dl(value));
                                }

                                const handler = debounce_dl(function(e){
                                    var $row = $(this).closest('tr');
                                    recalcRow_dl($row);
                                }, 120);

                                $modal.on('input change', '.delivery-qty, .delivery-unitprice, .delivery-discount', handler);

                                // Format unit price and discount on blur
                                $modal.on('blur', '.delivery-unitprice, .delivery-discount', function(){
                                    var n = parseNumber_dl($(this).val());
                                    $(this).val(formatNumber_dl(n));
                                });

                            })();
                        </script>
        </div>




<div class="modal side-panel fade" id="purchase_auto_generate" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg draggable" style="width:32rem">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Purchase</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body m-0">
                        <div class="container-fluid">
                            <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'purchase-order-create-gen', 'method' => 'POST', 'enctype' => 'multipart/form-data'])); ?>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="equipment comon-status row mt-40 d-block">
                                        <table id="table_id" class="table table-borderless" cellspacing="0" width="100%">
                                            <tbody>
                                                <tr>
                                                    <th style="width: 150px; vertical-align: top;">
                                                        Items </th><th>
                                                            <?php $__currentLoopData = $quoteitems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $Item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if((int)$Item->qty > (int)$Item->po_qty): ?>

                                                                <p style="margin-bottom:0px; font-weight: normal; padding-left: 7px;">
                                                                    <input class="dl_check" type="checkbox" name="product_id[]" value="<?php echo e($Item->product_id); ?>" />
                                                                    <?php echo e($Item->productname->part_number); ?></p>
                                                                    <?php endif; ?>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th style="width: 150px;">
                                                        PO Required </th><th> : 
                                                        <input class="dl_check" type="checkbox" name="req_po" checked value="1" />
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th>
                                                        GRN Required </th><th> : 
                                                        <input class="dl_check" type="checkbox" name="req_grn"  value="1" />
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th>
                                                        PI Required </th><th> : 
                                                        <input class="dl_check" type="checkbox" name="req_pi"  value="1" />
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th>
                                                        Payment Required </th><th> : 
                                                        <input class="dl_check" type="checkbox" name="req_pay"  value="1" />
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th>
                                                        Payment Options </th><th> :
                                                        <select class="" name="req_mode_acc"required>
                                                            <?php if(isset($paymentmode_cash)): ?>
                                                            <?php $__currentLoopData = $paymentmode_cash; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <option value="<?php echo e(@$val->id); ?>" <?php if(isset($editData)): ?> <?php if(@$editData->payment_mode == @$val->id): ?> selected <?php endif; ?> <?php endif; ?>><?php echo e(@$val->account_name); ?></option>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                            <?php endif; ?>
                                                            <?php if(isset($paymentmode_bank)): ?>
                                                            <?php $__currentLoopData = $paymentmode_bank; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <option value="<?php echo e(@$val->id); ?>" <?php if(isset($editData)): ?> <?php if(@$editData->payment_mode == @$val->id): ?> selected <?php endif; ?> <?php endif; ?>><?php echo e(@$val->account_name); ?></option>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                            <?php endif; ?>
                                                        </select>
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th>
                                                        Add Cost </th><th> : 
                                                        <input class="dl_check" type="checkbox" name="req_cost" checked value="1" />
                                                    </th>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                      <div class="modal-footer d-flex justify-content-center p-0">
    <input type="hidden" name="req_deal_id" value="<?php echo e($del->id); ?>" />
    <button type="submit" class="btn btn-light add-btn">
        <i class="ico icon-outline-bookmark-opened text-success"></i> Submit
    </button>
</div>

                            <?php echo e(Form::close()); ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>


          <div class="modal side-panel fade" id="purchase_auto_generate_MODAL" data-bs-backdrop="false" tabindex="-1" aria-labelledby="purchase_auto_generate_MODAL" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable draggable">
                <div class="modal-content" id="purchase_auto_generate_MODALbody">
                    
                    
                </div>
            </div>
        </div>
        




       <div class="modal side-panel fade" id="poViewModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="poViewModal" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable draggable" style="max-width:818px;width:818px;left:56rem;top:7rem">
                <div class="modal-content" id="poViewModalbody">
                    
                    
                </div>
            </div>
        </div>

         <div class="modal side-panel fade" id="payViewModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="payViewModal" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable draggable" style="max-width:818px;width:818px;left:56rem;top:7rem">
                <div class="modal-content" id="payViewModalbody">
                    
                    
                </div>
            </div>
        </div>

         <div class="modal side-panel fade" id="siViewModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="siViewModal" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable draggable" style="max-width:818px;width:818px;left:56rem;top:7rem">
                <div class="modal-content" id="siViewModalbody">
                    
                    
                </div>
            </div>
        </div>

         <div class="modal side-panel fade" id="dlnViewModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="dlnViewModal" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable draggable" style="max-width:818px;width:818px;left:56rem;top:7rem">
                <div class="modal-content" id="dlnViewModalbody">
                    
                    
                </div>
            </div>
        </div>

         <div class="modal side-panel fade" id="recViewModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="recViewModal" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable draggable" style="max-width:818px;width:818px;left:56rem;top:7rem">
                <div class="modal-content" id="recViewModalbody">
                    
                    
                </div>
            </div>
        </div>

         <div class="modal side-panel fade" id="jvViewModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="jvViewModal" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable draggable" style="max-width:818px;width:818px;left:56rem;top:7rem">
                <div class="modal-content" id="jvViewModalbody">
                    
                    
                </div>
            </div>
        </div>


        
<div class="modal side-panel fade" id="purchase_suppliers" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg draggable" style="width:50rem">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Payments</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="container-fluid">
                            <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'store-payments-from-purchase-order', 'method' => 'POST', 'enctype' => 'multipart/form-data'])); ?>

                            <div class="row">
                                <div class="col-lg-12 p-0">
                                    <div class="">
        <table class="table table-hover" style="width:100%">

<?php
$executed = 0;
    $po_list = App\SysPurchaseOrder::with('suppliername')->select(
            'sys_purchase_order.*',
            DB::raw('
                (SELECT IFNULL(SUM(taxableamount),0) + IFNULL(SUM(vatamount),0)
                 FROM sys_purchase_order_items
                 WHERE po_id = sys_purchase_order.id
                ) AS amount
            ')
        )
        ->where('deal_id', $del->id)
        ->where('status', 1)
        ->orderBy('vendors')
        ->get()
        ->groupBy('vendors');


?>

<?php $__currentLoopData = $po_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vendor_id => $vendor_pos): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

<?php
    $totalAmount  = $vendor_pos->sum('amount');
    $totalEx      = 0;
    $totalPayment = $totalAmount;
    $totalBal     = $totalAmount - $totalPayment;

         $paymentIds = $vendor_pos
    ->pluck('payment_id')        // ["5,12", "12,18", null]
    ->filter()                   // remove nulls
    ->flatMap(function ($ids) {
        return array_map('trim', explode(',', $ids));
    })
    ->unique()
    ->values()
    ->toArray();




$sum = 0;

foreach($paymentIds as $id){
    $paymentList = @App\SysChartofAccountsTransaction::where('transaction_id', $id)->wherein('transaction_type', ['cashpayment', 'bankpayment'])->where('is_main_account', 0)->get();

    foreach($paymentList as $item){
           $sum += abs($item->debit_amount - $item->credit_amount);
    }
}

?>

<thead class="table-light">
    <tr class="fw-bold">
        <th style="width:15px">
            <input type="checkbox"
                   class="po_supplier_btn"
                   data-vendor="<?php echo e($vendor_id); ?>"
                   onclick="po_supplier(this)">
        </th>

        <th>
            <?php echo e(optional($vendor_pos->first()->suppliername)->account_name); ?>

        </th>

        <th class="text-end">Amount</th>
        <th class="text-end" width="100px">Payment</th>
    </tr>
</thead>

<tbody>
<?php $__currentLoopData = $vendor_pos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pos): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <tr class="po-row" data-vendor="<?php echo e($vendor_id); ?>">
        <td class="text-center">
            <input type="checkbox"
                   class="sup_po_check"
                   data-vendor="<?php echo e($vendor_id); ?>"
                   name="supplier_po_id[]"
                   value="<?php echo e($pos->id); ?>"
                   onchange="toggleRowInputs(this)">
        </td>

        <td class="text-start"><?php echo e($pos->doc_number); ?></td>

        <td class="text-end">
            <?php echo e(App\SysHelper::com_curr_format($pos->amount, 2, '.', ',')); ?>

        </td>

       

        <td class="text-end">
          <input
    data-name="payment_value[<?php echo e($pos->id); ?>]"
    data-amount="<?php echo e(number_format($pos->amount, 2, '.', '')); ?>"
    name="payment_value[<?php echo e($pos->id); ?>]"
    type="text"
    class="form-control text-end border-1 payment_value"
    value="<?php echo e(number_format($pos->amount, 2, '.', '')); ?>"
/>
        </td>

        <input type="hidden" class="po_sup_id"
               data-name="po_sup_id[]"
               name="po_sup_id[]"
               value="<?php echo e($pos->id); ?>">

        <input type="hidden" class="po_sup_deal_id"
               data-name="po_sup_deal_id"
               name="po_sup_deal_id"
               value="<?php echo e($pos->deal_id); ?>">

        <input type="hidden" class="supplier_id"
          data-name="supplier_id"
          name="supplier_id"
            value="<?php echo e($pos->vendors); ?>">
        
            <input type="hidden" class="dealtrack_id"
            data-name="dealtrack_id"
            name="dealtrack_id"
            value="<?php echo e($deal->id); ?>">
        
    </tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<tr class="fw-bold table-light">
    <td></td>
    <td class="text-end fw-semibold">Total</td>

    <td class="text-end fw-semibold">
        <?php echo e(App\SysHelper::com_curr_format($totalAmount, 2, '.', ',')); ?>

    </td>


    <td class="text-end fw-semibold">
        <!-- <?php echo e(App\SysHelper::com_curr_format($totalPayment, 2, '.', ',')); ?> -->
    </td>
</tr>
<tr class="fw-bold table-light">
    <td></td>
    <td class="text-end fw-semibold">Payment</td>

    <td class="text-end fw-semibold">
        <?php echo e(App\SysHelper::com_curr_format($sum, 2, '.', ',')); ?>

    </td>


    <td class="text-end fw-semibold">
        <!-- <?php echo e(App\SysHelper::com_curr_format($totalPayment, 2, '.', ',')); ?> -->
    </td>
</tr>

<tr class="fw-bold table-light">
    <td></td>
    <td class="text-end fw-semibold">Bal</td>

    <td class="text-end fw-semibold">
        <?php
            $executed = $totalAmount - $sum;
        ?>
        <?php echo e(App\SysHelper::com_curr_format($executed, 2, '.', ',')); ?>

    </td>


    <td class="text-end fw-semibold">
        <!-- <?php echo e(App\SysHelper::com_curr_format($totalPayment, 2, '.', ',')); ?> -->
    </td>
</tr>


<tr>
    <td colspan="4">&nbsp;</td>
</tr>
</tbody>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</table>

<script>
function po_supplier(el) {
    const vendorId = el.getAttribute('data-vendor');
    const isChecked = el.checked;

    document.querySelectorAll('.po_supplier_btn').forEach(cb => {
        if (cb !== el) cb.checked = false;
    });

    document.querySelectorAll('.sup_po_check').forEach(cb => {
        if (cb.getAttribute('data-vendor') === vendorId) {
            cb.checked = isChecked;
            toggleRowInputs(cb);
        } else {
            cb.checked = false;
            toggleRowInputs(cb);
        }
    });
}

function toggleRowInputs(checkbox) {
    const row = checkbox.closest('tr');
    const isChecked = checkbox.checked;
    const paymentInput = row.querySelector('.payment_value');

    row.querySelectorAll('input').forEach(input => {
        if (input.type === 'checkbox') return;

        if (isChecked) {
            input.name = input.getAttribute('data-name');

            // ✅ fill PO amount into payment input
            if (paymentInput && paymentInput.dataset.amount) {
                paymentInput.value = paymentInput.dataset.amount;
            }
        } else {
            input.removeAttribute('name');

            // ✅ clear payment value
            if (paymentInput) {
                paymentInput.value = '';
            }
        }
    });
}
// INITIALIZE
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll('.sup_po_check').forEach(cb => {
        toggleRowInputs(cb);
    });
});

document.addEventListener("DOMContentLoaded", function () {

    // get first supplier checkbox
    const firstSupplier = document.querySelector('.po_supplier_btn');

    if (firstSupplier) {
        firstSupplier.checked = true;
        po_supplier(firstSupplier); // auto-check all its POs
    }
});
</script>





                                    </div>
                                </div>
                            </div>

                        <div class="modal-footer d-flex justify-content-center p-0">

                        <?php if($executed > 0.1): ?>
                            <button type="submit" class="btn btn-light add-btn ms-2">
                                <i class="ico icon-outline-bookmark-opened text-success"></i> Submit
                            </button>
                        <?php else: ?>

                        <script>

                             document.querySelectorAll('.sup_po_check').forEach(cb => {
                                cb.disabled = true;
                                });
                                document.querySelectorAll('.po_supplier_btn').forEach(input => {
                                    input.disabled = true;
                                });
                                document.querySelectorAll('.payment_value').forEach(input => {
                                    input.disabled = true;
                                });

 
                        </script>
                              
                        <?php endif; ?>
   
    
</div>

                            <?php echo e(Form::close()); ?>

                       
                        </div>
                    </div>
                </div>
            </div>
        </div>
        

        
<!-- Modal Invoice-->
<div class="modal side-panel fade" id="modalGRNSubmitted" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header m-0"><h4 class="modal-title" id="exampleModalLongTitle">GRN Submitted</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
          <div class="modal-body">
            
            <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-approval-grn','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-deal-track-approval-grn'])); ?>

         
            
            <div class="row">
            <div class="col-12 mb-2">
                <div class="form-check-label">GRN Submitted</div>
                <div class="input-effect">
                    <select class="form-control js-example-basic-single" name="grn_submitted" required>
                      <option value="1" <?php echo e(isset($grn_status) && $grn_status->grn_status == 1 ? 'selected' : ''); ?>>Approved</option>
                      <option value="2" <?php echo e(isset($grn_status) && $grn_status->grn_status == 2 ? 'selected' : ''); ?>>Disapproved</option>
                    </select>
                  </div>
            </div>
            
            
          
            <div class="col-12 mb-2">
                <div class="no-gutters input-right-icon">
                    <div class="col">
                        <div class="input-effect">
                            <span class="form-label"><?php echo app('translator')->getFromJson('Remarks'); ?><span></span></span>
                            <textarea class="form-control" rows="2" id="remarks"  name="remarks"><?php echo e(isset($grn_status) ? $grn_status->remarks : ''); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="modal-footer m-0 p-0">
          
          <input type="hidden" id="grn_deal_track_id" name="grn_deal_track_id" value="<?php echo e($deal->id); ?>" />
          <input type="hidden" id="grn_deal_id" name="grn_deal_id" value="<?php echo e($deal->deal_id); ?>" />
          
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

<?php 

}catch (\Exception $e) { ?> <?php echo e($e); ?> <?php  } ?>
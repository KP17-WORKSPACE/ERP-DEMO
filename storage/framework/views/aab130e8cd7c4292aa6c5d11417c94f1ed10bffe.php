
<style>
    .pdfarea header {
        position: fixed;
        left: 20px;
        top: -50px;
        right: 20px;
        height: 80px;
        background-color: white;
        text-align: center;
        border-bottom: solid 0px #808080;
        color: #555555;
    }

   

    .pdfarea footer .page:after {
        content: counter(page, upper-roman);
    }

    .pdfarea {
        font-family: Verdana, sans-serif;
        font-size: 12px;
        color: #555555;
        background-image: url('<?php echo asset("public/" . $company->pdf_watermark . ""); ?>');
    }

    .pdfarea th,
    .pdfarea td {
        padding: 5px 5px;
    }

    .tdd {
        border: dashed 1px #9e9e9e;
        border-width: 0 0 1px 0;
    }

    b {
        font-size: 14px;
    }

    .m1 table {
        border: 0px solid #9e9e9e;
    }

    .m1 td {
        border: 1px solid #9e9e9e;
    }

    .tmc ol {
        padding: 0px;
        margin: 0px;
    }

    .bottom_b {
        font-size: 12px;
    }

    .page-break {
        page-break-after: always;
    }

    .m-0 {
        margin: 0px;
    }

    .p-0 {
        padding: 0px;
    }

    .item-head-row {
        background: #2c2b6d;
        color: #ffffff;
    }

    .item-row {
        border-bottom: solid 1px #2c2b6d;
    }
</style>
    <?php try { ?>

        



    <div class="purchase-order-content-header sticky-top d-flex align-items-center justify-content-between gap-2" style="background-color: #f7f8fd">
        <div class="d-flex align-items-center gap-2">
            <h4 class="purchase-order-content-header-left">
                <?php echo e($grn->doc_number); ?>

            </h4>
            <?php if(isset($grn->deal_id)): ?>
                <?php echo App\SysHelper::deal_pipeline_purchase($grn->deal_id); ?>

            <?php endif; ?>
        </div>
        <div class="purchase-order-content-header-right">

              <form method="GET" action="<?php echo e(url('goods-receipt-note-list', @$grn->id)); ?>">
            
            <button type="submit" name="grn_action" value="edit" class="btn btn-light">
                <i class="ico icon-outline-pen-2 text-success"></i> Edit
            </button>
        </form>

        <form method="GET" action="<?php echo e(url('goods-receipt-note-list', @$grn->id)); ?>">
            <button type="submit" name="grn_action" value="add" class="btn btn-light">
                <i class="ico icon-outline-add-square text-success"></i> Add
            </button>
        </form>


            
            
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ico icon-outline-hamburger-menu"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="<?php echo e(url('goods-receipt-note/'.$grn->id.'/delete')); ?>"><i class="ico icon-outline-trash-bin-minimalistic text-danger"></i> Cancel GRN</a></li>
                    <li><a class="dropdown-item" href="<?php echo e(url('goods-receipt-note/'.$grn->id.'/download')); ?>"><i class="ico icon-outline-document-medicine text-success"></i> Download</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="card mb-3 card-min-height">
        <div class="card-body">
            <div class="" id="deal-info" role="tabpanel" aria-labelledby="deal-info-tab">
                
                <div class="row">
                    <div class="col-2 mb-2">&nbsp;</div>
                    <div class="col-8 mb-2 pdfarea" >
                        
                        
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td align="left"><img style="margin-left:-11px"  src="<?php echo e(asset('public/'.@$company->company_logo)); ?>" width="200px"/></td>
                                <td align="right"><b style="font-size: 30px; font-weight: 400;">Goods Receipt Note</b></td>
                            </tr>
                        </table>
  <br />

    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="60%" valign="top" style="line-height: 18px;">
                                    <b>From,</b><br>

                 <b ><?php echo e(@$grn->accountname->account_name); ?></b><br>
              Attn. <?php echo e(@$contact_name); ?><br />
              <!-- <?php echo e(@$address); ?>, <?php if(@$address2 != ""): ?><?php echo e(@$address2); ?>, <?php endif; ?> <?php if(@$city != ""): ?><?php echo e(@$city); ?><?php endif; ?><br> -->
              <?php echo e(@$state); ?>, <?php echo e(@$country); ?><br>
              T: <?php echo e(@$tel); ?>, 
              M: <?php echo e(@$mob); ?><br/>
              E: <?php echo e(@$email); ?> <br>
                   <?php if($m_trnno != ''): ?>
                                        TRN No: <?php echo e(@$m_trnno); ?><br>
                                    <?php endif; ?>
          </td>
          <td>
            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="line-height: 18px;" >
              <tr>
                  <td style="padding: 0px; margin: 0px; width: 150px;">Delivery Note No</td>
                  <td style="padding: 0px; margin: 0px;">: <?php echo e(@$grn->doc_number); ?></td>
                </tr>
                <tr>
                  <td style="padding: 0px; margin: 0px;">Delivery Note Date</td>
                  <td style="padding: 0px; margin: 0px;">: <?php echo e(date('d/m/Y', strtotime(@$grn->grn_date))); ?></td>
                </tr>
                <tr>
                  <td style="padding: 0px; margin: 0px;">LPO Number</td>
                  <td style="padding: 0px; margin: 0px;">: <?php echo e(@$grn->lpo_number); ?></td>
                </tr>
                <tr>
                  <td style="padding: 0px; margin: 0px;">LPO Date</td>
                  <td style="padding: 0px; margin: 0px;">: <?php echo e(date('d/m/Y', strtotime(@$grn->lpo_date))); ?></td>
                </tr>
                <tr>
                  <td style="padding: 0px; margin: 0px;white-space: nowrap;">Payment Terms</td>
                  <td style="padding:0; margin:0; white-space:nowrap; max-width:190px; overflow:hidden; text-overflow:ellipsis;">: <?php echo e($grn->paymentterms->title); ?> <?php echo e($grn->payment_terms2); ?></td>
                </tr>
          </table>
          </td>
        </tr>
    </table>
    <br />
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="60%" valign="top" style="line-height: 18px;">Bill To,<br />
            
               <b style="font-size:90%"><?php echo e(@$company->company_name); ?></b>
               <br>
               <?php if($bill_contact_name != ''): ?>
                                        <?php echo e($bill_contact_name); ?><br />
                                    <?php endif; ?>
            <div><?php echo e(optional($company->stateRelation)->name); ?>, <?php echo e(optional($company->countryname)->name); ?></div>
            T: <?php echo e(@$company->telephone); ?>, M: <?php echo e(@$company->mobile); ?><br />
            E: <?php echo e(@$company->email); ?><br />
            TRN No: <?php echo e(@$company->vat_number); ?>

        </td>
        <td valign="top" style="line-height: 18px;">Ship To,<br />
          <b style="font-size: 90%;"><?php echo e(@$grn->shippingSupplierName->account_name); ?></b><br>
            <?php echo e(@$grn->shipping_name); ?><br />
            <?php if($delivery_state): ?><?php echo e($delivery_state); ?>,<?php endif; ?>
            <?php echo e($delivery_country); ?><br>
            T: <?php echo e(@$grn->shipping_contact_no); ?>, M: <?php echo e(@$ship_mob); ?><br />
            E: <?php echo e(@$grn->shipping_email); ?><br/>
            TRN: <?php echo e($ship_trnno); ?>

            
        </td>
      </tr>
  </table>
  <br />
    
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr style="background: #2c2b6d; color: #ffffff;">
          <td style="width: 20px;">No</td>
          <td style="width: 530px;">Description</td>
          <td style="width: 50px; text-align: center;">Qty</td>
        </tr>
    </table>
        <?php
            $i=1;
            $qty=0;
        ?>
        <?php if(count($grn_item)>0): ?>
        <?php $__currentLoopData = $grn_item; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
        <td style="width: 20px; border-bottom: solid 1px #2c2b6d; vertical-align: top;"><?php echo e($i); ?> <?php $i++;?></td>
        <td style="width: 530px; border-bottom: solid 1px #2c2b6d; font-size: 10px; vertical-align: top;">
            <b style="font-size: 11px; vertical-align: top;"><?php echo e($item->part_number); ?></b><br />
            <?php
                $srl = $grn_item_srl->where('item_id',$item->id)->pluck('srl_no');
            ?>
            <?php echo nl2br($item->description); ?> <br>
           <b style="font-size: 9.5px; vertical-align: top;"> <?php if(count($srl)>0): ?>
            <?php $__currentLoopData = $srl; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php echo e($sr); ?> <?php if(!$loop->last): ?>,<?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
       <b>
            
            <span style="width:auto; font-size: 12px; background: #ffffff; padding: 2px; margin-top:5px; font-weight: bold; font-style: italic; display: none;"><?php echo e(str_replace(',',', ',$item->serial_no)); ?></span></td>
          <td style="width: 50px; border-bottom: solid 1px #2c2b6d; text-align: center; vertical-align: top;"><?php echo e($item->qty); ?></td>
            <?php            
            $qty += $item->qty;
            ?>
        </tr>
        </table>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
              <td style="border-bottom: solid 1px #2c2b6d; font-weight: bold;">
                Note: Goods Received in Good Condition
            </td>
            <td class="text-end" style="border-bottom: solid 1px #2c2b6d; font-weight: bold">
                Total
            </td>
          <td style="border-bottom: solid 1px #2c2b6d; text-align: center; font-weight: bold;"><?php echo e($qty); ?></td>
        </tr>
      </table>
      <br ><br ><br ><br />
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td style="border: none; font-weight: bold;" align="left" valign="bottom">
                Received By: <br /><br />
                Name: <br /><br />
                Phone: <br /><br /><br /><br />
                Signature & Stamp
        
            </td>
          <td style="border: none; font-weight: bold;" align="center" valign="bottom">Approved By</td>
          <td style="border: none; font-weight: bold;" align="right" valign="bottom">For <?php echo e(@$company->company_name); ?></td>
        </tr>
      </table>

        <footer>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        
        <tr>
          <td colspan="3" style="border: none; font-size: 10px;" align="right" valign="top">
            
        </tr>
    </table>
    <img  src="<?php echo asset('public/'.$company->pdf_footer.''); ?>"  width="100%"/></td>
  </footer>


                        
                    </div>
                    <div class="col-2 mb-2">&nbsp;</div>
                </div>
            </div>
        </div>
    </div>

    <?php /*
    <div class="purchase-order-content-header">
        <h4 class="purchase-order-content-header-left">
        </h4>
        <div class="purchase-order-content-header-right">&nbsp;
            {{-- <button class="btn btn-light">
                <i class="ico icon-outline-add-square text-success"></i> Add
            </button>
            <button class="btn btn-light">
                <i class="ico icon-outline-pen-2 text-success"></i> Edit
            </button>
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ico icon-outline-hamburger-menu"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#dealcancelModal"><i class="ico icon-outline-trash-bin-minimalistic text-danger"></i> Cancel Deal</a></li>
                    <li><a class="dropdown-item" href="quote.html"><i class="ico icon-outline-document-medicine text-success"></i> Generate Quote</a></li>
                    <li><a class="dropdown-item" href="#"><i class="ico icon-outline-pen-2 text-warning"></i> Add Pre-Sales Request</a></li>
                    <li><a class="dropdown-item" href="#"> <i class="ico icon-outline-pen-2 text-warning"></i> Add Collaboration</a></li>
                    <li><a class="dropdown-item" href="#"> <i class="ico icon-outline-pen-2 text-warning"></i> End User Details</a></li>
                </ul>
            </div> --}}
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <div class="tab-pane fade show active" id="deal-info" role="tabpanel" aria-labelledby="deal-info-tab">
                <div class="d-flex align-items-center gap-3 mt-3 mb-2">
                    <h5 class="m-0 text-green">No details found</h5>
                </div>
                <div class="row">
                    <div class="col-12 mb-2">
                        No details found
                    </div>
                </div>
            </div>
        </div>
    </div> */ ?>

    <?php }catch (\Exception $e) { ?> <?php echo e($e); ?> <?php  } ?>
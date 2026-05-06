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

        



    <div class="purchase-order-content-header d-flex align-items-center justify-content-between gap-2">
        <div class="d-flex align-items-center gap-2">
            <h4 class="purchase-order-content-header-left">
                <?php echo e($dn->doc_number); ?>

            </h4>
            <?php if(isset($dn->deal_id)): ?>
                <?php echo App\SysHelper::deal_pipeline($dn->deal_id); ?>

            <?php endif; ?>
        </div>
        <div class="purchase-order-content-header-right">
             <a class="btn btn-light text-dark" href="<?php echo e(url('delivery-note/'.$dn->id.'?di_action=edit')); ?>">
                <i class="ico icon-outline-pen-2 text-success"></i> Edit
            </a>
            <a class="btn btn-light text-dark" href="<?php echo e(url('delivery-note/'.$dn->id.'?di_action=add')); ?>">
                <i class="ico icon-outline-add-square text-success"></i> Add
            </a>
           
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ico icon-outline-hamburger-menu"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="<?php echo e(url('delivery-note/'.$dn->id.'/delete')); ?>"><i class="ico icon-outline-trash-bin-minimalistic text-danger"></i> Cancel DN</a></li>
                  
                    <li><a class="dropdown-item" href="<?php echo e(url('delivery-note/'.$dn->id.'/download/t')); ?>"><i class="ico icon-outline-document-medicine text-success"></i> Print</a></li>
                   
                    <li><a class="dropdown-item" href="<?php echo e(url('delivery-note/'.$dn->id.'/download')); ?>"><i class="ico icon-outline-document-medicine text-success"></i> Download</a></li>
                    
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
                <td align="left"><img style="margin-left:-12px"  src="<?php echo e(asset('public/'.@$company->company_logo)); ?>" width="200px"/></td>
                <td align="right"><b style="font-size: 30px; font-weight: 400;">Delivery Note</b></td>
            </tr>
        </table>
  
  <br />

    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="60%" valign="top" style="line-height: 18px">Bill To,<br />
              <b style="font-size: 90%;"><?php echo e(@$dn->accountname->account_name); ?></b><br>
              <?php echo e(@$contact_name); ?><br />
              
              <?php echo e(@$state); ?>, <?php echo e(@$country); ?><br>
              T: <?php echo e(@$tel); ?>, M: <?php echo e(@$mob); ?><br/>
              E: <?php echo e(@$email); ?>  
        </td>
        <td valign="top" style="line-height: 18px;">Ship To,<br />
          <b style="font-size: 90%;"><?php echo e(@$dn->accountname->account_name); ?></b><br>
          <?php echo e(@$ship_contact_name); ?><br />
          
          <?php echo e(@$delivery_state); ?>, <?php echo e(@$delivery_country); ?><br>
          T: <?php echo e(@$ship_tel); ?>, M: <?php echo e(@$ship_mob); ?><br/>
          E: <?php echo e(@$ship_email); ?> 
        </td>
      </tr>
  </table>
  <br />
    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="" style="text-align: left; padding-left: 20px;">
      <tr>
        <td width="20%" style="line-height: 12px; font-weight:bold;">Delivery Note No</td>
        <td width="20%" style="line-height: 12px; font-weight:bold;">Delivery Note Date</td>
        <td width="20%" style="line-height: 12px; font-weight:bold;">Sales Order No</td>
        <td width="20%" style="line-height: 12px; font-weight:bold;">Sales Order Date</td>
        <td width="20%" style="line-height: 12px; font-weight:bold;">Payment Terms</td>
      </tr>
      <tr>
        <td style="line-height: 12px;"><?php echo e(@$dn->doc_number); ?></td>
        <td style="line-height: 12px;"><?php echo e(date('d/m/Y', strtotime(@$dn->doc_date))); ?></td>
        <td style="line-height: 12px;"><?php echo e(@$dn->invoice_no); ?></td>
        <td style="line-height: 12px;"><?php echo e(date('d/m/Y', strtotime(@$dn->invoice_date))); ?></td>
        <td style="line-height: 12px;"><?php echo e($dn->payment_terms->title); ?> <?php echo e($dn->payment_terms2); ?></td>
      </tr>
    </table>
    <br />
    
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr style="background: #2c2b6d; color: #ffffff;">
          <td style="width: 10px;">S.No</td>
          <td style="width: 550px;">Description</td>
          <td style="width: 50px; text-align: center;">Qty</td>
        </tr>
    </table>
        <?php
            $i=1;
            $qty=0;
        ?>
        <?php if(count($dn_item)>0): ?>
        <?php $__currentLoopData = $dn_item; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
        <td style="width: 10px; border-bottom: solid 1px #2c2b6d;"><?php echo e($i); ?> <?php $i++;?></td>
        <td style="width: 550px; border-bottom: solid 1px #2c2b6d; font-size: 10px;">
            <b style="font-size: 11px;"><?php echo e($item->product->part_number); ?></b><br />
            <span style="font-size:10px;"><?php echo nl2br($item->description); ?></span><br />
            <span style="width:auto; font-size: 8.5px; padding: 2px; margin-top:5px; font-weight: bold; font-style: italic;"><?php echo e(preg_replace('/\s*,\s*/', ', ', $item->serial_no)); ?></span></td>
          <td style="width: 50px; border-bottom: solid 1px #2c2b6d; text-align: center;"><?php echo e($item->qty); ?></td>
            <?php            
            $qty += $item->qty;
            ?>
        </tr>
        </table>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td style="border-bottom: solid 1px #2c2b6d; font-weight: bold;width:510px" colspan="2"> Note: Goods Received in Good Condition <span class="text-right">Total</span></td>
          <td style="border-bottom: solid 1px #2c2b6d; text-align: center; font-weight: bold;width:58px"><?php echo e($qty); ?></td>
        </tr>
      </table>
      <br /><br />
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td style="border: none; width:35%; line-height: 20px;" align="left" valign="top"><b class="bottom_b">Received By:</b><br >
            <b class="bottom_b">Name:</b><br >
            <b class="bottom_b">Phone:</b><br >
            <b class="bottom_b">Signature and stamp:</b>
          </td>
          <td style="border: none; width:30%; line-height: 20px;" align="center" valign="bottom"><?php echo e(@$si->createdby->full_name); ?><br /><b class="bottom_b" style="font-size: 10px;">Prepared By</b>
          </td>
          <td style="border: none; width:35%; line-height: 20px;" align="right" valign="top">
            <b class="bottom_b" style="font-size: 10px;">For <?php echo str_replace('SYSCOM DISTRIBUTIONS LLC BRANCH ABU DHABI 1','SYSCOM DISTRIBUTIONS LLC<br />BRANCH ABU DHABI 1',$company->company_name); ?></b><br /><br />
            
          </td>
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
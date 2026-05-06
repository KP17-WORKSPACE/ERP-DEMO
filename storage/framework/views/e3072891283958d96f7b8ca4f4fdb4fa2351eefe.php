
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
                <?php echo e($si->doc_number); ?>

            </h4>
            <?php if(isset($si->deal_id)): ?>
                <?php echo App\SysHelper::deal_pipeline($si->deal_id); ?>

            <?php endif; ?>
        </div>
        <div class="purchase-order-content-header-right">

           <a class="btn btn-light text-dark" href="<?php echo e(url('sales-invoice/'.$si->id.'?si_action=edit')); ?>">
                <i class="ico icon-outline-pen-2 text-success"></i> Edit
            </a>

            <a class="btn btn-light text-dark" href="<?php echo e(url('sales-invoice/' . $si->id . '?si_action=add')); ?>">
                <i class="ico icon-outline-add-square text-success"></i> Add
            </a>
           
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ico icon-outline-hamburger-menu"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="<?php echo e(url('sales-invoice/'.$si->id.'/delete')); ?>"><i class="ico icon-outline-trash-bin-minimalistic text-danger"></i> Cancel SI</a></li>
                    <li><a class="dropdown-item" href="<?php echo e(url('sales-invoice/'.$si->id.'/download/t')); ?>"><i class="ico icon-outline-document-medicine text-success"></i> Print</a></li>
                    
                    <li><a class="dropdown-item" href="<?php echo e(url('sales-invoice/'.$si->id.'/download')); ?>"><i class="ico icon-outline-document-medicine text-success"></i> Download</a></li>
                   
                    <li><button type="button" class="dropdown-item" data-modal-size="modal-md" data-bs-target="#attachment_popup_win" data-bs-toggle="modal" class="btn btn-primary" onclick="view_attachment()"><i class="ico icon-outline-calculator-minimalistic text-warning"></i> Attachment</button></li>
                    <input type="hidden" id="si_id" value="<?php echo e($si->id); ?>">
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
            <td align="left" ><img style="margin-left:-12px" src="<?php echo e(asset('public/'.@$company->company_logo)); ?>" width="200px"/></td>
            <td align="right" style="width: 195px;"><b style="font-size: 25px; font-weight: 400;">TAX INVOICE</b><br />
          <div style="text-align: center; padding-top: 10px;">TRN No: <?php echo e($company->vat_number); ?></div></td>
        </tr>
    </table> 
    <br><br>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="60%" valign="top" style="line-height: 18px;">Bill To,<br />
                <b style="font-size: 90%;"><?php echo e(@$si->accountname->account_name); ?></b><br>
                <?php echo e(@$contact_name); ?><br />
       
                
                <?php echo e(@$state); ?>, <?php echo e(@$country); ?><br>
                T: <?php echo e(@$tel); ?>, M: <?php echo e(@$mob); ?><br/>
                E: <?php echo e(@$email); ?><br/> 
                <?php if($cust_trn_no!=""): ?> TRN No: <?php echo e(@$cust_trn_no); ?> <?php endif; ?>
          </td>
          <td valign="top" style="line-height: 18px;">Ship To,<br />
            <b style="font-size: 90%;"><?php echo e($ship_company_name); ?></b><br>  
            <?php echo e(@$ship_contact_name); ?><br />
          
            
            <?php echo e(@$delivery_state); ?>, <?php echo e(@$delivery_country); ?><br>
            T: <?php echo e(@$ship_tel); ?>, M: <?php echo e(@$ship_mob); ?><br/>
            E: <?php echo e(@$ship_email); ?><br/>  
            <?php if($shipp_trn_no!=""): ?> TRN No: <?php echo e(@$shipp_trn_no); ?> <?php endif; ?>
          </td>
        </tr>
    </table>

    <br />
    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="" style="text-align: left; padding-left: 20px;">
      <tr>
        <td width="16.6666%" style="line-height: 12px; font-weight:bold;">Invoice No</td>
        <td width="16.6666%" style="line-height: 12px; font-weight:bold;">Deal ID</td>
        <td width="16.6666%" style="line-height: 12px; font-weight:bold;">Date</td>
        <td width="16.6666%" style="line-height: 12px; font-weight:bold;">Ref No</td>
        <td width="16.6666%" style="line-height: 12px; font-weight:bold;">Ref Date</td>
        <td width="16.6666%" style="line-height: 12px; font-weight:bold;">Payment Terms</td>
      </tr>
      <tr>
        <td style="line-height: 12px;"><?php echo e(@$si->doc_number); ?></td>
        <td style="line-height: 12px;"><?php echo e(@$si->deal_code->code); ?></td>
        <td style="line-height: 12px;"><?php echo e(date('d/m/Y', strtotime(@$si->doc_date))); ?></td>
        <td style="line-height: 12px;"><?php echo e(@$si->lpo_number); ?></td>
        <td style="line-height: 12px;"><?php echo e(date('d/m/Y', strtotime(@$si->lpo_date))); ?></td>
        <td style="line-height: 12px;"><?php echo e($si->paymentterms->title); ?> <?php echo e($si->payment_terms2); ?></td>
      </tr>
    </table>
    <br />
    
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr class="item-head-row">
          <td style="width: 20px; text-align: center;">No</td>
          <td>Part No</td>
          <td style="width: 20px; text-align: center;">Qty</td>
          <td style="width: 70px; text-align: right;">Rate</td>
          <td style="width: 70px; text-align: right;">Value</td>
          <td style="width: 30px; text-align: right;">VAT%</td>
          <td style="width: 80px; text-align: right;">VAT Amount</td>
          <td style="width: 80px; text-align: right;">Amount</td>
        </tr>
    </table>
        <?php
            $i=1;
            $sub_total=0;
            $discount=0; $deal_discount=0; $deal_discount_vat=0; $deal_discount_vat_amount=0; $deal_discount_amount=0;
            $taxable_amt=0;
            $customs_charges=0;
            $vat_amount=0;
            $total_amount=0;
        ?>
        <?php if(count($si_item)>0): ?>
        <?php $__currentLoopData = $si_item; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td class="item-row" style="width: 20px;"><?php echo e($i); ?> <?php $i++;?></td>
            <td class="item-row" >
              <span style="font-weight:bold;"><?php echo e($item->productname->part_number); ?></span><br />
              <span style="font-size:10px;"><?php echo nl2br($item->description); ?></span></td>
            <td class="item-row" style="width: 20px; text-align: center;"><?php echo e($item->qty); ?></td>
            <td class="item-row" style="width: 70px; text-align: right;"><?php echo e(@App\SysHelper::com_curr_format($item->unitprice,2,'.',',')); ?></td>
            <td class="item-row" style="width: 70px; text-align: right;"><?php echo e(@App\SysHelper::com_curr_format($item->unitprice*$item->qty,2,'.',',')); ?></td>
            <td class="item-row" style="width: 30px; text-align: right;"><?php echo e(@App\SysHelper::com_curr_format($item->tax,2,'.',',')); ?></td>
            <td class="item-row" style="width: 80px; text-align: right;"><?php echo e(@App\SysHelper::com_curr_format($item->vatamount,2,'.',',')); ?></td>
            <td class="item-row" style="width: 80px; text-align: right;"><?php echo e(@App\SysHelper::com_curr_format($item->taxableamount+$item->vatamount,2,'.',',')); ?></td>
            <?php
            
            $sub_total += $item->unitprice*$item->qty;
            $discount += $item->discount;
            $taxable_amt += $item->taxableamount;
            $customs_charges += $item->customcharges;
            $vat_amount += $item->vatamount;
            $total_amount += $item->taxableamount + $item->vatamount;

            ?>


        </tr>
        </table>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <?php
        
        $deal_discount += $si->deal_discount;
        $deal_discount_vat=$si_item->max('tax');
        $deal_discount_vat_amount= $deal_discount * $deal_discount_vat/100;
        $deal_discount_amount= $deal_discount + $deal_discount_vat_amount;
        ?>
        <?php endif; ?>

        <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td valign="top">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr><td>
                <?php echo e($si->currency_name->code); ?>  <?php echo ucwords(@App\SysHelper::convertAmountToWords(@App\SysHelper::com_curr_format($total_amount-$deal_discount_amount, 2, '.', ''),$si->currency_name->r_code,$si->currency_name->p_code));?>
              </td>
              </tr>
            </table>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                  <td>
                    <b style="font-size: 12px;">Terms & Conditions</b>
                    <ol style="padding: 10px 0px 0px 15px; margin: 0px; font-size: 9px;">
      
      <li>The ownership of goods will remain with us until full payment is received.</li>
      <li>Open box items are non-returnable, and all sales of such items are final.</li>
      <li>Items without serial numbers are not covered under the warranty.</li>
      <li>Damage caused by power fluctuations is not covered under the warranty.</li>
      <li>To make a warranty claim, please contact the relevant vendor&#39;s service center.</li>
      <li>Bank details:- Bank Name: <?php echo e(@$company->bank_name); ?>, Account Number: <?php echo e(@$company->account_number); ?></li>
                  </ol>          
                </td>
                </tr>
            </table>
          </td>
          <td valign="top" width="250px"><table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>              
              <td style="width: 110px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">Sub Total <?php echo e($si->currency_name->code); ?></td>
              <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;"><?php echo e(@App\SysHelper::com_curr_format($sub_total, 2, '.', ',')); ?></td>
            </tr>
          </table>
          <?php if(($discount+$deal_discount) > 0): ?>
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td style="width: 110px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">Discount <?php echo e($si->currency_name->code); ?></td>
              <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;"><?php echo e(@App\SysHelper::com_curr_format($discount+$deal_discount, 2, '.', ',')); ?></td>
            </tr>
          </table>
          <?php endif; ?>
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td style="width: 110px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">Taxable Amt. <?php echo e($si->currency_name->code); ?></td>
              <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;"><?php echo e(@App\SysHelper::com_curr_format($taxable_amt-$deal_discount, 2, '.', ',')); ?></td>
            </tr>
          </table>
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td style="width: 110px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">VAT Amount <?php echo e($si->currency_name->code); ?></td>
              <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;"><?php echo e(@App\SysHelper::com_curr_format($vat_amount-$deal_discount_vat_amount, 2, '.', ',')); ?></td>
            </tr>
          </table>
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td style="width: 110px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">Total Amount <?php echo e($si->currency_name->code); ?></td>
              <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;"><?php echo e(@App\SysHelper::com_curr_format($total_amount-$deal_discount_amount, 2, '.', ',')); ?></td>
            </tr>
          </table></td>
        </tr>
        </table>
                        
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

    <div class="modal side-panel fade" id="attachment_popup_win" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header m-0 p-3">
                    <h4 class="modal-title">Attachments</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-3">
                    <input type="hidden" id="hd_pending_dn_id"/>
                    <div class="container-fluid">
                        
                    <div class="row">
                        <div class="col-md-12 mt-2">
                            <table id="att-table" class="table table-hover form-item-table" width="100%" cellspacing="0">
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
            </div>
        </div>
    </div>

<script>
    function view_attachment(){
        $("#loading_bg").css("display", "block");
        var action = "<?php echo e(URL::to('view-sales-invoice-attachment')); ?>";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '<?php echo e(csrf_token()); ?>',
                siv_id : $('#si_id').val(),
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
                                <td><a onclick='delete_attachment("+dataResult['data'][i].id+")' class='btn-sm btn-danger text-white'>Delete</a></td>\
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

    <?php }catch (\Exception $e) { ?> <?php echo e($e); ?> <?php  } ?>
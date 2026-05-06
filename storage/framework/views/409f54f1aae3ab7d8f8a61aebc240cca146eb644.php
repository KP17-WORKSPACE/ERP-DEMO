<style>
    th,
    td {
        padding: 5px 0px;
    }

    .tdd {
        border: solid 0px #f5f5f5;
        border-width: 0 0 0px 0;
    }

    .main2 {
        margin: 0px 0px;
    }

    .dttable {}

    .dttable th,
    .dttable td {
        padding: 3px 5px;
        text-align: left;
        border: solid 0px #e7e6e6;
        border-width: 0px;
        font-size: 11px;
    }

    .dttable th {
        background: #d9d9d9;
    }

    .pdfarea {
        font-family: Verdana, sans-serif;
        font-size: 12px;
        color: #555555;
        background-image: url('<?php echo asset("public/".$company->pdf_watermark.""); ?>') !important;
    }

    .dttable2 {}

    .dttable2 th,
    .dttable2 td {
        padding: 3px 5px;
        text-align: left;
        border: solid 1px #e7e6e6;
        border-width: 0px 1px 1px 1px;
        font-size: 11px;
    }

    .dttable2 th {
        background: #d9d9d9;
    }

    .algc {
        text-align: left !important;
        font-weight: bold;
        background: #f2f2f2;
        color: #ffffff;
    }

    .subhd {
        font-weight: bold;
        background: #f2f2f2;
        color: #808080;
    }

    .cathd {
        font-weight: bold;
        background: #f2f2f2;
        color: #808080;
        text-align: center;
    }

    ol li {
        color: #808080;
    }

    hr {
        border: solid 0px #f5f5f5;
        background: #f5f5f5;
        height: 1px;
    }

    .page-break {
        page-break-after: always;
    }

    body {
        

    }

    .item-head-row {
        background: #2c2b6d;
        color: #ffffff;
    }

    .item-head-row td {
        border: solid 2px #2c2b6d !important;
        padding: 5px !important;
        margin: 0px !important;
    }

    .item-row {
        border-bottom: solid 1px #2c2b6d !important;
        border-top: solid 0px #2c2b6d !important;
    }
</style>
<?php try { ?>





<div class="purchase-order-content-header sticky-top d-flex align-items-center justify-content-between gap-2" style="background-color: #f7f8fd">
    <div class="d-flex align-items-center gap-2">
        <h4 class="purchase-order-content-header-left">
            <?php echo e($quotation->code); ?> (<?php echo e(@$quotationitems[0]->document_number); ?>)
    

             
        </h4>
        <?php echo App\SysHelper::deal_pipeline($quotation->id); ?>

    </div>
    <div class="purchase-order-content-header-right">
         <?php
        $is_deal_track_submited = App\SysCrmDealTrack::where('deal_id', $quotation->id)->count();
    ?>
        <a class="btn btn-light text-dark btn-edit-deal" href="<?php echo e(url('quotations/' . $quotation->id . '?qn_action=edit')); ?>" data-deal-track-submitted="<?php echo e($is_deal_track_submited ? 1 : 0); ?>">
            <i class="ico icon-outline-pen-2 text-success"></i> Edit
        </a>
        <script>
$(document).on("click", ".btn-edit-deal", function (e) {
    var submitted = $(this).data('deal-track-submitted');
    if (submitted) {
        e.preventDefault();
        if (typeof toastr !== 'undefined') {
            toastr.error('Deal Track already submitted. Editing is not allowed.');
        } else {
            alert('Deal Track already submitted. Editing is not allowed.');
        }
        return;
    }
    localStorage.removeItem("active-dealedit-tab");
});
</script>
        <a class="btn btn-light text-dark" href="<?php echo e(url('quotations?qn_action=add')); ?>">
            <i class="ico icon-outline-add-square text-success"></i> Add
        </a>

        <div class="dropdown">
            <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="ico icon-outline-hamburger-menu"></i>
            </button>
            <ul class="dropdown-menu">
                <li><button type="button" class="dropdown-item d-flex align-items-center text-dark"  data-bs-toggle="modal" data-bs-target="#staticBackdrop"><i
                            class="ico icon-bold-download-minimalistic text-success  title-15 me-2"></i> Download</button>
                </li>

                 <!-- <li><a class="dropdown-item d-flex align-items-center text-dark" name="po_action" value="add" 
                        href="<?php echo e(url('crm-quote/' . $quotation->id . '/download/' . $quotation->quote_id)); ?>"><i
                            class="ico icon-bold-download-minimalistic text-success  title-15 me-2"></i> Download</a>
                </li> -->

            

                <?php if(!empty($quotation->track) && !empty($quotation->track->id)): ?>
                    <li>
                        <a target="__blank" href="<?php echo e(url('crm-deal-track-approval-list/' . $quotation->track->id)); ?>"
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
           <a href="<?php echo e(url('crm-quote/' . $quotation->id . '/download/' . $quotation->quote_id)); ?>" class="btn btn-light text-dark"> <i
                            class="ico icon-bold-download-minimalistic text-success" style="font-size:13px"></i> Business Proposal</a>
        <a href="<?php echo e(url('crm-quote-pdf/' . $quotation->id)); ?>" class="btn btn-light text-dark"><i
                            class="ico icon-bold-download-minimalistic text-success" style="font-size:13px"></i> Quotation</a>
      </div>
     
    </div>
  </div>
</div>

<div class="card mb-3 card-min-height">
    <div class="card-body">
        <div class="" id="deal-info" role="tabpanel" aria-labelledby="deal-info-tab">
            
            <div class="row">
                <div class="col-2 mb-2">&nbsp;</div>
                <div class="col-8 mb-2 pdfarea">

                    
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td align="left"><img style="margin-left:-12px" src="<?php echo e(asset('public/'.@$company->company_logo)); ?>" width="200px" />
                            </td>
                            <td align="right"><b style="font-size: 30px; font-weight: 400;">Quotation</b>
                            </td>
                        </tr>
                    </table>
                    <br />
                    <div class="main2">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="60%" style="line-height: 18px; vertical-align: top;width:70% !important;">
                                    <b style="font-size: 14px;">To: <br /><?php echo e($quotation->customername->name); ?></b><br />
                                    <span style="font-size: 12px;">Attn: <?php echo e($quotation->cust_name); ?></span><br />
                                    <p style="padding: 0px; margin: 0px; width: 250px;">
                                        <?php echo e(@$quotation->customername->addresses->first()->address); ?></p>
                                   
                                    <p style="padding: 0px; margin: 0px; width: 250px;">
                                        <?php echo e(@$quotation->customername->addresses->first()->statename->name); ?>,
                                        <?php echo e(@$quotation->customername->addresses->first()->countryname->name); ?></p>
                                    <?php if($quotation->cust_no != ''): ?><b>Tel :</b>
                                        <?php echo e(@$quotation->cust_no); ?><br /><?php endif; ?>
                                    <?php if($quotation->cust_email != ''): ?><b>Email :</b>
                                        <?php echo e(@$quotation->cust_email); ?><?php endif; ?>
                                    <br /><br />
                                    
                                </td>

                                <td style="line-height: 18px; vertical-align: top;float: right;">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0"
                                        style="margin-top: 27px;">
                                        <tr>
                                            <td style="padding: 0px; margin: 0px; width: 90px;">Quote No</td>

                                            <td style="padding: 0px; margin: 0px">: <?php echo e($quotation->code); ?> <?php if(@$quotationitems[0]->quote_id != 1 && @$quotationitems[0]->quote_id != null): ?> -
                                                    <?php echo e(@$quotationitems[0]->quote_id - 1); ?> <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 0px; margin: 0px;">Quote Date</td>
                                            <td style="padding: 0px; margin: 0px">:
                                                <?php echo e(!empty($quotationitems[0]->created_at) ? date('d/m/Y', strtotime($quotationitems[0]->created_at)) : date('d/m/Y', strtotime($quotation->created_at))); ?>

                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="padding: 0px; margin: 0px;">Quote Validity</td>
                                            <td style="padding: 0px; margin: 0px">:
                                                <?php echo e(@$quotationitems[0]->quote_validity); ?></td>
                                        </tr>



                                        <?php if($deliverytime != ''): ?>
                                            <tr>
                                                <td style="padding: 0px; margin: 0px;"> Delivery Time</td>
                                                <td style="padding: 0px; margin: 0px">: <?php echo e($deliverytime); ?></td>
                                            </tr>

                                        <?php endif; ?>

                                        <tr>
                                            <td
                                                style="padding: 0px; margin: 0px;vertical-align: top; white-space: nowrap;">
                                                Payment Terms</td>
                                           <td style="padding:0; margin:0; white-space:nowrap; max-width:250px; overflow:hidden; text-overflow:ellipsis;">
    : <?php echo e($paymentterms); ?>

</td>

                                        </tr>

                                        <?php if(@$quotationitems[0]->nooflocation != ''): ?>
                                            <tr>
                                                <td style="padding: 0px; margin: 0px;"> No of Locations</td>
                                                <td style="padding: 0px; margin: 0px">:
                                                    <?php echo e(@$quotationitems[0]->nooflocation); ?></td>
                                            </tr>

                                        <?php endif; ?>

                                        <?php if(@$quotationitems[0]->connectivity != ''): ?>
                                            <tr>
                                                <td style="padding: 0px; margin: 0px;"> Connectivity Required</td>
                                                <td style="padding: 0px; margin: 0px">:
                                                    <?php echo e(@$quotationitems[0]->connectivity); ?></td>
                                            </tr>

                                        <?php endif; ?>

                                        <?php if(@$quotationitems[0]->telephonetype != ''): ?>
                                            <tr>
                                                <td style="padding: 0px; margin: 0px;"> ISP Telephone Type</td>
                                                <td style="padding: 0px; margin: 0px">:
                                                    <?php $string = @$quotationitems[0]->telephonetype;
                                    $str_arr = explode (",", $string);
                                    $string_val = @$quotationitems[0]->nolines;
                                    $str_arr2 = explode (",", $string_val);
                                    for($i=0; $i< count($str_arr); $i++) { ?>
                                                    <?php echo e($str_arr[$i]); ?> - <?php echo e($str_arr2[$i]); ?> Line,
                                                    <?php } ?></td>
                                            </tr>

                                        <?php endif; ?>

                                    </table>

                                </td>
                            </tr>
                        </table>
                    </div>



                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td colspan="3" align="center"><u><b style="font-size: 15px;">
                                        <?php if(count($quotationitems) > 0): ?>
                                            
                                        <?php else: ?>
                                            Quotation Not Yet Generated
                                        <?php endif; ?>

                                    </b></u></td>
                        </tr>
                    </table>



                    <?php if(count($quotationitems) > 0): ?>

                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr class="item-head-row">
                                <td width="5%" style="text-align: center;">No</td>
                                <td width="70%">Description</td>
                                
                                <td width="5%" style="text-align: center;">Qty</td>
                                <td width="10%" style="text-align: right;">Unit Price</td>
                                <td width="10%" style="text-align: right;">Total</td>
                            </tr>
                        </table>
                        <?php
                        $subtotal = 0;
                        $vat = 0;
                        $discount = 0;
                        $c1 = 0;
                        $c2 = 0;
                        $c3 = 0;
                        $c4 = 0;
                        $c5 = 0;
                        $c6 = 0;
                        $Hardware = 0;
                        $HardwareT = 0;
                        $License = 0;
                        $LicenseT = 0;
                        $srl = 1;
                        ?>
                        <?php foreach($quotationitems as $val){?>
                        <?php if($val->status != 0): ?>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td class="item-row" style="width: 30px;text-align: center;"><?php echo e($srl++); ?>

                                    </td>
                                    <td class="item-row" style="width: 300px;font-size: 11px;">

                                        <b style="font-size: 11px;"> <?php echo e($val->productname->part_number); ?> </b> <br>
                                        <?php echo nl2br($val->description); ?>

                                    </td>
                                    

                                    <td class="item-row" width="5%" style="text-align: center;">
                                        <?php echo e($val->qty); ?></td>
                                    <td class="item-row" width="10%" style="text-align: right;">
                                        <?php echo e(App\SysHelper::currancy_format($val->price, $val->currency_id)); ?></td>
                                    <td class="item-row" width="10%" style="text-align: right;">
                                        <?php $subtotal += $val->price * $val->qty; ?>
                                        <?php $discount += $val->discount; //$discount += $val->discount * $val->qty; ?>
                                        <?php echo e(App\SysHelper::currancy_format($val->qty * $val->price, $val->currency_id)); ?>

                                    </td>
                                </tr>
                            </table>
                        <?php endif; ?>


                        <?php 
        if($wv==1){ $vat = 0; }
        else { $vat += ((( $val->price * $val->qty) * $val->vat/100) - ($val->discount * $val->vat/100)); }
        
        $currency_id = $val->currency_id;
        }
            $discount += $quotation->deal_discount;
            if($wv==1){
                $deal_discount_vat=0;
            } else { $deal_discount_vat=$quotationitems->max('vat'); }
            $deal_discount_vat_amount= $quotation->deal_discount * $deal_discount_vat/100;
        ?>
                        </table>
                        <?php if($wt != 1): ?>


                            <div class="row">
                                <div class="col-8">





                                    <table width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td class="font-weight-600"><?php echo e(@$currency_modal->code); ?>

                                                <?php echo ucwords(@App\SysHelper::convertAmountToWords($subtotal - $discount - $deal_discount_vat_amount + $vat, @$currency_modal->r_code, @$currency_modal->p_code)); ?></td>

                                            </td>

                                        <tr>
                                            <td>
                                                <b>Terms & Conditions</b><br />

                                                <?php if($quotation->terms_and_condition == ''): ?>
                                                    <ol style="padding-left: 15px; font-size: 11px">
                                                        <li>Quote/Order will be subject to approval of payment/credit
                                                            terms by <?php echo e($quotationitems[0]->company->company_name); ?>.
                                                        </li>
                                                        <li>Please mention our Quotation No.in your Purchase Order</li>
                                                        <li>In case of non-availability of quote products
                                                            <?php echo e($quotationitems[0]->company->company_name); ?> reserved
                                                            the rights to supply a functionally similar or better
                                                            product.</li>
                                                    </ol>
                                                <?php else: ?>
                                                    <div style="padding-top: 2px; font-size: 11px">
                                                        <?php echo nl2br($quotation->terms_and_condition); ?>



                                                    </div>
                                                <?php endif; ?>
                                            </td>

                                        </tr>
                                    </table>

                                </div>
                                <div class="col-4">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td></td>
                                            <td
                                                style="width: 160px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                                Total <?php echo e($currency); ?></td>
                                            <td
                                                style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                                <?php echo e(App\SysHelper::currancy_format($subtotal, $currency_id)); ?></td>
                                        </tr>
                                    </table>
                                    <?php if($discount > 0): ?>
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td></td>
                                                <td
                                                    style="width: 160px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                                    Discount <?php echo e($currency); ?></td>
                                                <td
                                                    style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                                    <?php echo e(App\SysHelper::currancy_format($discount, $currency_id)); ?></td>
                                            </tr>
                                        </table>
                                    <?php endif; ?>
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td></td>
                                            <td
                                                style="width: 160px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                                Sub Total <?php echo e($currency); ?></td>
                                            <td
                                                style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                                <?php echo e(App\SysHelper::currancy_format($subtotal - $discount, $currency_id)); ?>

                                            </td>
                                        </tr>
                                    </table>
                                    <?php if($wv != 1): ?>
                                        <?php if($vat - $deal_discount_vat_amount != 0): ?>
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td></td>
                                                    <?php if($currency == 'INR'): ?>
                                                        <td
                                                            style="width: 160px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                                            GST <?php echo e($currency); ?></td>
                                                    <?php elseif($currency == 'USD'): ?>
                                                        <td
                                                            style="width: 160px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                                            TAX <?php echo e($currency); ?></td>
                                                    <?php else: ?>
                                                        <td
                                                            style="width: 160px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                                            VAT <?php echo e($currency); ?></td>
                                                    <?php endif; ?>
                                                    <td
                                                        style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                                        <?php echo e(App\SysHelper::currancy_format($vat - $deal_discount_vat_amount, $currency_id)); ?></b>
                                                    </td>
                                                </tr>
                                            </table>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td></td>
                                            <td
                                                style="width: 160px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                                Net Amount <?php echo e($currency); ?></td>
                                            <td
                                                style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                                <?php echo e(App\SysHelper::currancy_format($subtotal - $discount - $deal_discount_vat_amount + $vat, $currency_id)); ?>

                                            </td>
                                        </tr>
                                    </table>
                        <?php endif; ?>

                       
                </div>

            </div>




            <?php endif; ?>

            <br><br><br><br><br>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td style="width:14%; border: none;" align="left" valign="bottom">
                        <?php echo e(@$quotation->ownername->full_name); ?><br /><br /><br /><b class="bottom_b">Prepared
                            By</b></td>
                    <td style="width:60%; border: none;" align="center" valign="bottom">This document
                        is computer generated Signature is not required</td>
                    <td style="width:20%; border: none;" align="right" valign="bottom">
                        <?php echo e(@$company->company_name); ?><br /><br /><br /><b class="bottom_b">Authorised
                            Signature</b></td>
                </tr>
            </table>
            <footer>

                <img src="<?php echo asset('public/' . $company->pdf_footer . ''); ?>" width="100%" /></td>
            </footer>

         


            
        </div>
        <div class="col-2 mb-2">&nbsp;</div>
    </div>
</div>
</div>
</div>




<!-- Modal Support-->
<div class="modal side-panel fade" id="ModalSupport" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
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
                    <div class="col-md-10">
                        <div class="mb-3">
                            <label for="" class="form-label">Customer</label>
                            <input type="text" class="form-control" value="<?php echo e($edit->customername->name); ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="" class="form-label">Deal Id</label>
                            <input type="number" class="form-control" value="<?php echo e($edit->deal_code->code); ?>"
                                readonly>
                            <input type="hidden" name="deal_id" id="deal_id" value="<?php echo e($edit->id); ?>">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="" class="form-label">Site Name</label>
                            <input type="text" class="form-control" name="site_name" id="site_name"
                                value="<?php echo e($edit->address); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="" class="form-label">Date</label>
                            <input type="date" class="form-control" name="support_date" id="support_date"
                                required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="" class="form-label">From</label>
                            <input type="time" class="form-control" name="time_from" id="time_from" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="" class="form-label">To</label>
                            <input type="time" class="form-control" name="time_to" id="time_to" required>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="" class="form-label">Scope of Work</label>
                            <div class="col-md-12">
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label fw-semibold mb-0">Scope of Work</label>
                                        <button type="button" class="btn btn-sm btn-light border"
                                            onclick="add_scope_of_work()">
                                            <i class="ico icon-outline-add-square me-1"></i> Add
                                        </button>
                                    </div>

                                    <table class="table table-sm table-borderless align-middle mb-0">
                                        <tbody>
                                            <tr>
                                                <td class="text-muted" width="5%">1.</td>
                                                <td>
                                                    <input type="text" class="form-control" name="scope_of_work[]"
                                                        id="scope_of_work_1" required>
                                                </td>
                                                <td width="5%"></td>
                                            </tr>

                                            <?php for($i = 2; $i <= 20; $i++): ?>
                                                <tr id="row_<?php echo e($i); ?>" style="display: none;">
                                                    <td class="text-muted"><?php echo e($i); ?>.</td>
                                                    <td>
                                                        <input type="text" class="form-control"
                                                            name="scope_of_work[]"
                                                            id="scope_of_work_<?php echo e($i); ?>">
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            onclick="delete_work(<?php echo e($i); ?>)">
                                                            <i class="ico icon-outline-trash-bin-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endfor; ?>
                                        </tbody>
                                    </table>

                                    <input type="hidden" id="scope_of_work_row_id" value="1" />
                                </div>

                                <script>
                                    function add_scope_of_work() {
                                        let scope = parseInt($('#scope_of_work_row_id').val());
                                        let currentInput = $('#scope_of_work_' + scope);

                                        if (currentInput.val().trim() !== "") {
                                            scope++;
                                            $('#row_' + scope).fadeIn();
                                            $('#scope_of_work_row_id').val(scope);
                                            $('#scope_of_work_' + scope).prop("required", true);
                                        } else {
                                            currentInput.focus();
                                        }
                                    }

                                    function delete_work(id) {
                                        $('#row_' + id).fadeOut(() => {
                                            $('#scope_of_work_' + id).val('').prop("required", false);
                                        });
                                    }
                                </script>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="customer_id" id="customer_id" required value="<?php echo e($edit->cust_id); ?>" />
                <input type="hidden" name="sales_person_id" id="sales_person_id" required
                    value="<?php echo e($edit->owner); ?>" />
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
<div class="modal side-panel fade" id="ModalSupportCmt" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel">Add Service Comments</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-support-activity-comments', 'method' => 'POST', 'enctype' => 'multipart/form-data'])); ?>

            <?php if(count($support) != 0): ?>
                <input type="hidden" name="support_id" value="<?php echo e($support[0]->id); ?>" />
            <?php endif; ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="" class="form-label">Comments</label>
                            <textarea class="form-control" name="remarks" id="remarks" rows="10" required></textarea>
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

<!-- Modal Collaboration-->
<div class="modal side-panel fade" id="ModalCollaboration" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel">Add Collaboration</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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
<!-- Modal Service-->
<div class="modal side-panel fade" id="ModalService" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <?php if(count($service) == 0): ?>
                    <h4 class="modal-title" id="exampleModalLabel">Add Pre-Sales</h4>
                <?php else: ?>
                    <h4 class="modal-title" id="exampleModalLabel">Add Pre-Sales Comments</h4>
                <?php endif; ?>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <?php if(count($service) == 0): ?>
                <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-service', 'method' => 'POST', 'enctype' => 'multipart/form-data'])); ?>

            <?php else: ?>
                <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-service-comments-additional', 'method' => 'POST', 'enctype' => 'multipart/form-data'])); ?>

                <input type="hidden" name="service_id" value="<?php echo e($service[0]->id); ?>" />
                <input type="hidden" name="status" value="5" />
            <?php endif; ?>

            <input type="hidden" name="service_deal_id" value="<?php echo e($edit->id); ?>" />
            <input type="hidden" name="service_cust_id" value="<?php echo e($edit->cust_id); ?>" />
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="" class="form-label">Description</label>
                            <textarea class="form-control" name="comments" id="comments" rows="5"></textarea>
                        </div>
                    </div>

                    <?php /*
                        @if (count($service)==0) 
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Select Part Number</label>
                                <select class="form-control js-example-basic-single" name="part_number[]" id="part_number" multiple>
                                    @foreach ($product_list as $value)
                                        <option value="{{ @$value->part_number }}">{{ @$value->part_number }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Select Users</label>
                                <select class="form-control js-example-basic-single" name="user_id[]" id="user_id" multiple>
                                    @foreach ($support_person as $value)
                                        <option value="{{ @$value->user_id }}" >{{ @$value->full_name }} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif
                        */
                    ?>

                </div>
            </div>
            <div class="modal-footer">
                <?php if(count($service) == 0): ?>
                    <button type="submit" class="btn btn-light add-btn ms-2">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Add to Pre-Sales
                    </button>
                    <button type="submit" class="btn btn-primary">Add to Pre-Sales</button>
                <?php else: ?>
                    <button type="submit" class="btn btn-light add-btn ms-2">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Add Comment
                    </button>
                    <button type="submit" class="btn btn-primary"></button>
                <?php endif; ?>
            </div>
            <?php echo e(Form::close()); ?>


        </div>
    </div>
</div>
<!-- Modal Service-->

<!-- Modal End User -->
<div class="modal side-panel fade" id="ModalEndUserDetails" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel">End User Details</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php if($enduser == ''): ?>
                <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-add-end-user', 'method' => 'POST', 'enctype' => 'multipart/form-data'])); ?>

                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="end_user_deal_id" value="<?php echo e($edit->id); ?>" />
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Ultimate End User Company Name *</label>
                                <input type="text" class="form-control" name="end_user_company_name"
                                    id="end_user_company_name" required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Address *</label>
                                <input type="text" class="form-control" name="address_line_a" id="address_line_a"
                                    required />
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">End User Contact Person *</label>
                                <input type="text" class="form-control" name="end_user_contact_person"
                                    id="end_user_contact_person" required />
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Mobile No</label>
                                <input type="text" class="form-control" name="mobile_no" id="mobile_no" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" id="email" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Project Name</label>
                                <input type="text" class="form-control" name="project_name" id="project_name" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Brief description about this project</label>
                                <textarea class="form-control" name="project_description" id="project_description"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">When it is expected to Close</label>
                                <input type="date" class="form-control" name="expected_close_date"
                                    id="expected_close_date" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-light add-btn ms-2">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Add
                    </button>
                </div>
                <?php echo e(Form::close()); ?>

            <?php else: ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="" class="form-label">Ultimate End User Company Name </label> :
                            <?php echo e($enduser->end_user_company_name); ?>

                            <hr class="m-0 p-0 mb-1" />
                            <label for="" class="form-label">Address</label> : <?php echo e($enduser->address_line_a); ?>

                            <hr class="m-0 p-0 mb-1" />
                            
                            <label for="" class="form-label">End User Contact Person</label> :
                            <?php echo e($enduser->end_user_contact_person); ?>

                            <hr class="m-0 p-0 mb-1" />
                            
                            <label for="" class="form-label">Mobile No</label> : <?php echo e($enduser->mobile_no); ?>

                            <hr class="m-0 p-0 mb-1" />
                            <label for="" class="form-label">Email</label> : <?php echo e($enduser->email); ?>

                            <hr class="m-0 p-0 mb-1" />
                            <label for="" class="form-label">Project Name</label> :
                            <?php echo e($enduser->project_name); ?>

                            <hr class="m-0 p-0 mb-1" />
                            <label for="" class="form-label">Brief description about this project</label> :
                            <?php echo e($enduser->project_description); ?>

                            <hr class="m-0 p-0 mb-1" />
                            <label for="" class="form-label">When it is expected to Close</label> :
                            <?php echo e(date('d-M-Y', strtotime($enduser->expected_close_date))); ?>

                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>
<!-- Modal End User -->







<?php }catch (\Exception $e) { ?> <?php echo e($e); ?> <?php  } ?>

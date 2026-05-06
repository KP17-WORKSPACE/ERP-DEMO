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
        background-image: url('<?php echo asset('public/' . $company->pdf_watermark . ''); ?>');
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
        font-size: 12px;
        font-weight: bold;
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






<div class="purchase-order-content-header d-flex align-items-center justify-content-between mb-1 gap-2">
    <div class="d-flex align-items-center gap-2">
        <h4 class="purchase-order-content-header-left">
            <?php echo e(@$pfi->doc_number); ?>

        </h4>
        <?php echo App\SysHelper::deal_pipeline($quotation->id); ?>

    </div>
    <div class="purchase-order-content-header-right d-flex align-items-center justify-content-end gap-2">


      <?php
        $is_deal_track_submited = App\SysCrmDealTrack::where('deal_id', $quotation->id)->count();
        
    ?>


         <a class="btn btn-light text-dark btn-edit-deal" href="<?php echo e(url('proforma-invoice/'.@$pfi->id.'?proforma_action=edit')); ?>">
            <i class="ico icon-outline-pen-2 text-success"></i> Edit
        </a>
    
      

        <form method="GET" action="<?php echo e(url('proforma-invoice')); ?>">
            <button type="submit" name="proforma_action" value="add" class="btn btn-light">
                <i class="ico icon-outline-add-square text-success"></i> Add
            </button>
        </form>

      



        <div class="dropdown">
            <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="ico icon-outline-hamburger-menu"></i>
            </button>
            <ul class="dropdown-menu" style="">
                <li><a class="dropdown-item" href="<?php echo e(url('proforma-invoice/'.$pfi->id.'/download')); ?>">
                        <i class="ico icon-outline-import text-success"></i>
                        Download</a></li>


            </ul>
        </div>
    </div>
</div>
<div class="card mb-3">
    <div class="card-body">
        <div class="" id="deal-info" role="tabpanel" aria-labelledby="deal-info-tab">

            <div class="row">
                <div class="col-2 mb-2">&nbsp;</div>
                <div class="col-8 mb-2 pdfarea">


                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td align="left"><img style="margin-left: -12px;" src="<?php echo e(asset('public/'.@$company->company_logo)); ?>" width="200px" />
                            </td>
                            <td align="right"><b style="font-size: 30px; font-weight: 400;">
                                
                            <?php if($pfi->proforma_invoice == 'CI'): ?>
                                    Commercial Invoice
                            <?php elseif($pfi->proforma_invoice == 'PI'): ?>
                                    Proforma Invoice
                            <?php else: ?>
                                Proforma Invoice
                                
                            <?php endif; ?>
                            



                            </b><br />
          <div style="text-align: right;">TRN No: <?php echo e($company->vat_number); ?></div>
                            </td>
                        </tr>
                    </table>
                    <br />
                    <!-- <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="60%" valign="top" style="line-height: 18px;">
                                <b><?php echo e(@$company->company_name); ?></b>
                                <div><?php echo e(@$company->stateRelation->name); ?>, <?php echo e(@$company->countryRelation->name); ?></div>
                                T: <?php echo e(@$company->telephone); ?>, M: <?php echo e(@$company->mobile); ?><br />
                                E: <?php echo e(@$company->email); ?><br />
                                <?php if($company->vat_number != 0 && $company->vat_number != ''): ?>
                                    TRN No: <?php echo e(@$company->vat_number); ?>

                                <?php endif; ?>
                            </td>
                            <td valign="top" style="line-height: 18px;">
                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td style="padding: 0px; margin: 0px; width: 150px;">PFI No</td>
                                        <td style="padding: 0px; margin: 0px">: <?php echo e(@$pfi->doc_number); ?></td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 0px; margin: 0px">PFI Date</td>
                                        <td style="padding: 0px; margin: 0px">
                                            : <?php echo e(date('d/m/Y', strtotime(@$pfi->doc_date))); ?></td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 0px; margin: 0px">Ref No</td>
                                        <td style="padding: 0px; margin: 0px">: <?php echo e(@$pfi->reference_no); ?></td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 0px; margin: 0px">Ref Date</td>
                                        <td style="padding: 0px; margin: 0px">
                                            : <?php echo e(date('d/m/Y', strtotime(@$pfi->reference_date))); ?></td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 0px; margin: 0px;vertical-align: top; white-space: nowrap;">Payment Terms</td>
                                        <td style="padding:0; margin:0; white-space:nowrap; max-width:250px; overflow:hidden; text-overflow:ellipsis;">
                                            :
                                            <?php echo e(@$des = App\SysPaymentTerms::getPaymentTermsName(@$quotation->track->payment_terms)); ?>

                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table> -->
                    <br />
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="60%" valign="top" style="line-height: 18px;">Bill To,<br />
                                <b style="font-size: 90%;"><?php echo e(@$quotation->customername->name); ?></b><br>
                                <?php echo e(@$contact_name); ?><br />
                                <?php echo e(@$state); ?>, <?php echo e(@$country); ?><br>
                                T: <?php echo e(@$tel); ?>, M: <?php echo e(@$mobile); ?><br />
                                E: <?php echo e(@$email); ?><br />
                                TRN : <?php echo e($vat_number); ?>

                            </td>
                            <td valign="top" style="line-height: 18px;">Ship To,<br />
                                <b style="font-size: 90%;"><?php echo e(@$quotation->customername->name); ?></b><br>
                                <?php echo e(@$ship_contact_name); ?><br />
                             
                       <?php echo e(@$state); ?>, <?php echo e(@$country); ?><br>


                                T: <?php echo e(@$ship_tel); ?>, M: <?php echo e(@$ship_mobile); ?><br />

                                E: <?php echo e(@$ship_email); ?><br />
                                TRN : <?php echo e($vat_number); ?>

                            </td>
                        </tr>
                    </table>

                    

                     <br />
    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="" style="text-align: left; padding-left: 0px;">
      <tr>
        <td width="20%" style="line-height: 12px; font-weight:bold;">PFI No</td>
        <td width="20%" style="line-height: 12px; font-weight:bold;">PFI Date</td>
        <td width="20%" style="line-height: 12px; font-weight:bold;">Ref No</td>
        <td width="20%" style="line-height: 12px; font-weight:bold;">Ref Date</td>
        <td width="20%" style="line-height: 12px; font-weight:bold;">Payment Terms</td>
      </tr>
      <tr>
        <td style="line-height: 12px;"><?php echo e(@$pfi->doc_number); ?></td>
        <td style="line-height: 12px;"><?php echo e(date('d/m/Y', strtotime(@$pfi->doc_date))); ?></td>
        <td style="line-height: 12px;"><?php echo e(@$pfi->reference_no); ?></td>
        <td style="line-height: 12px;"><?php echo e(date('d/m/Y', strtotime(@$pfi->reference_date))); ?></td>
        <td style="line-height: 12px;"><?php echo e(@$des = App\SysPaymentTerms::getPaymentTermsName(@$quotation->track->payment_terms)); ?></td>
      </tr>
    </table>
    <br />

                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr class="item-head-row">
                            <td style="width: 30px;">No</td>
                            <td style="width: 300px;">Part No</td>
                            <td style="width: 20px; text-align:center !important;">Qty</td>
                            <td style="width: 70px; text-align:right !important;">Rate</td>
                            <td style="width: 70px; text-align:right !important;">Value</td>
                            <td style="width: 80px; text-align:right !important;">VAT Amount</td>
                            <td style="width: 80px; text-align:right !important;">Amount</td>
                        </tr>
                    </table>
                    <?php
                    $i = 1;
                    $sub_total = 0;
                    $discount = 0;
                    $taxable_amt = 0;
                    $customs_charges = 0;
                    $vat_amount = 0;
                    $total_amount = 0;
                    $currency = $pfi->currency_name->code;
                    ?>
                    <?php if(count($pfi_item) > 0): ?>
                        <?php $__currentLoopData = $pfi_item; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="width: 30px;" class="item-row"><?php echo e($i); ?>

                                        <?php $i++; ?></td>
                                    <?php @$des = App\SmItem::getItemDes($item->part_number); ?>
                                    <td style="width: 300px;" class="item-row"><?php echo nl2br($des); ?></td>
                                    <td style="width: 20px; text-align: center !important;" class="item-row">
                                        <?php echo e($item->qty); ?></td>
                                    <td style="width: 70px; text-align: right !important;" class="item-row">
                                        <?php echo e(@App\SysHelper::com_curr_format($item->unitprice, 2, '.', ',')); ?></td>
                                    <td style="width: 70px; text-align: right !important;" class="item-row">
                                        <?php echo e(@App\SysHelper::com_curr_format($item->unitprice * $item->qty, 2, '.', ',')); ?>

                                    </td>
                                    <td style="width: 80px; text-align: right !important;" class="item-row">
                                        <?php echo e(@App\SysHelper::com_curr_format($item->vatamount, 2, '.', ',')); ?></td>
                                    <td style="width: 80px; text-align: right !important;" class="item-row">
                                        <?php echo e(@App\SysHelper::com_curr_format($item->taxableamount + $item->vatamount, 2, '.', ',')); ?>

                                    </td>
                                    <?php
                                    
                                    $sub_total += $item->value;
                                    $discount += $item->discount;
                                    $taxable_amt += $item->taxableamount;
                                    $customs_charges += $item->customcharges;
                                    $vat_amount += $item->vatamount;
                                    $total_amount += $item->unitprice * $item->qty;
                                    
                                    ?>


                                </tr>
                            </table>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        <?php
                        $deal_discount_vat = $quotationitems->max('vat');
                        $deal_discount_vat_amount = ($quotation->deal_discount * $deal_discount_vat) / 100;
                        ?>

                    <?php endif; ?>

                    <div class="row">
                        <div class="col-8">

                         <table width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td class="font-weight-600"><?php echo e(@$currency_modal->code); ?>

                                                <?php echo ucwords(@App\SysHelper::convertAmountToWords($total_amount - ($quotation->deal_discount + $discount) + ($vat_amount - $deal_discount_vat_amount), @$pfi->currency_name->r_code, @$pfi->currency_name->p_code)); ?></td>

                                            </td>

                                        <tr>
                                            <td>
                                                <b>Terms & Conditions</b><br />
 <?php if($quotation->terms_and_condition == ''): ?>
                                    <ol style="padding-left: 15px; font-size: 11px">
                                        <li>Order will be subject to approval of payment/credit terms by
                                            <?php echo e(@$company->company_name); ?>.</li>
                                        <li>In case of non-availability of products
                                            <?php echo e(@$company->company_name); ?> reserved the rights to supply a
                                            functionally similar or better product.</li>
                                        <li>All payment transfer charges should be borne by the sender only.
                                        </li>
                                        <li>Bank details:- Bank Name: <?php echo e(@$company->bank_name); ?>, Account
                                            Number: <?php echo e(@$company->account_number); ?></li>
                                    </ol>
                                <?php else: ?>
                                    <div style="padding: 5px; font-size: 11px">
                                        <?php
                                        $string = $quotation->terms_and_condition;
                                        
                                        $bits = explode("\n", $string);
                                        
                                        $newstring = "<ol style='padding:0px; margin:0px 0px 0px 10px;'>";
                                        foreach ($bits as $bit) {
                                            $newstring .= '<li>' . substr($bit, 2) . '</li>';
                                        }
                                        $newstring .= '</ol>';
                                        ?>
                                        <?php echo $newstring; ?>

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
                                style="width: 130px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                <b>Total <?php echo e($currency); ?></b>
                            </td>
                            <td
                                style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                <b><?php echo e(@App\SysHelper::com_curr_format($total_amount, 2, '.', ',')); ?></b>
                            </td>
                        </tr>
                    </table>
                    <?php if($quotation->deal_discount + $discount != 0): ?>
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td></td>
                                <td
                                    style="width: 130px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                    <b>Discount <?php echo e($currency); ?></b>
                                </td>
                                <td
                                    style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                    <b><?php echo e(@App\SysHelper::com_curr_format($quotation->deal_discount + $discount, 2, '.', ',')); ?></b>
                                </td>
                            </tr>
                        </table>
                    <?php endif; ?>
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td></td>
                            <td
                                style="width: 130px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                <b>Sub Total <?php echo e($currency); ?></b>
                            </td>
                            <td
                                style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                <b><?php echo e(@App\SysHelper::com_curr_format($total_amount - ($quotation->deal_discount + $discount), 2, '.', ',')); ?></b>
                            </td>
                        </tr>
                    </table>
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td></td>
                            <?php if($currency == 'INR'): ?>
                                <td
                                    style="width: 130px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                    <b>GST <?php echo e($currency); ?></b>
                                </td>
                            <?php elseif($currency == 'USD'): ?>
                                <td
                                    style="width: 130px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                    <b>TAX <?php echo e($currency); ?></b>
                                </td>
                            <?php else: ?>
                                <td
                                    style="width: 130px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                    <b>VAT <?php echo e($currency); ?></b>
                                </td>
                            <?php endif; ?>
                            <td
                                style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                <b><?php echo e(@App\SysHelper::com_curr_format($vat_amount - $deal_discount_vat_amount, 2, '.', ',')); ?></b>
                            </td>
                        </tr>
                    </table>
                    <?php
                    $net_amount = $total_amount - ($quotation->deal_discount + $discount) + ($vat_amount - $deal_discount_vat_amount);
                    ?>
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td></td>
                            <td
                                style="width: 130px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                <b>Net Amount <?php echo e($currency); ?></b>
                            </td>
                            <td
                                style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                <b><?php echo e(@App\SysHelper::com_curr_format($net_amount, 2, '.', ',')); ?></b>
                            </td>
                        </tr>
                    </table>
                        </div>
                    </div>

                    


                   
                    <br><br><br><br><br>
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td style="width:35%; border: none;" align="left" valign="bottom">
                                <?php echo e(@$pfi->salesman->full_name); ?><br /><br /><br /><b class="bottom_b">Prepared
                                    By</b></td>
                            <td style="width:35%; border: none;" align="center" valign="bottom">This document
                                is computer generated Signature is not required</td>
                            <td style="width:35%; border: none;" align="right" valign="bottom">
                                <?php echo e(@$company->company_name); ?><br /><br /><br /><b class="bottom_b">Authorised
                                    Signature</b></td>
                        </tr>
                    </table>




                    <footer>

                        <img src="<?php echo asset('public/' . $company->pdf_footer . ''); ?>" width="100%" /></td>
                    </footer>

                       <br><br><br>


                    <?php
                    function getIndianCurrency(float $number, string $r1, string $r2)
                    {
                        $decimal = round($number - ($no = floor($number)), 2) * 100;
                        $hundred = null;
                        $digits_length = strlen($no);
                        $i = 0;
                        $str = [];
                        $words = [0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen', 19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty', 40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty', 70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety'];
                        $digits = ['', 'Hundred', 'Thousand', '', 'Crore'];
                        while ($i < $digits_length) {
                            $divider = $i == 2 ? 10 : 100;
                            $number = floor($no % $divider);
                            $no = floor($no / $divider);
                            $i += $divider == 10 ? 1 : 2;
                            if ($number) {
                                $plural = ($counter = count($str)) && $number > 9 ? 's' : null;
                                $hundred = $counter == 1 && $str[0] ? ' and ' : null;
                                $str[] = $number < 21 ? $words[$number] . ' ' . $digits[$counter] . $plural . ' ' . $hundred : $words[floor($number / 10) * 10] . ' ' . $words[$number % 10] . ' ' . $digits[$counter] . $plural . ' ' . $hundred;
                            } else {
                                $str[] = null;
                            }
                        }
                        $Rupees = implode('', array_reverse($str));
                        $paise = $decimal > 0 ? '.' . ($words[$decimal / 10] . ' ' . $words[$decimal % 10]) . ' ' . $r2 : '';
                        return ($Rupees ? $Rupees . $r1 : ' ') . $paise;
                    }
                    ?>




                </div>
                <div class="col-2 mb-2">&nbsp;</div>
            </div>
        </div>
    </div>
</div>

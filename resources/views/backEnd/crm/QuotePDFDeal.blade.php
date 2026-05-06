<html>
<head>
  {{-- <style>
    @page { margin: 100px 0px;}
    body{font-family: Verdana, sans-serif; font-size:15px; color:#2b2a6c;}
    th, td {padding: 10px 0px;}
    .tdd{border:dashed 1px #2b2a6c; border-width:0 0 1px 0;}
    b{font-size:18px;}

    header { position: fixed; top: -90px; left: 0px; right: 0px; margin:0px; padding:0px; height:18px;}
    footer { position: fixed; bottom: 0px; left: 0px; right: 0px; margin:0px; padding:0px; height:18px;}
    main{margin:20px 50px;}
    p { page-break-after: always; }
    p:last-child { page-break-after: never;}
  </style> --}}

  <style>
    @import url(http://fonts.googleapis.com/earlyaccess/droidarabickufi.css);
    .droid { font-family: 'Droid Arabic Kufi', serif; }
    
    @page { margin: 150px 20px 20px 20px; }
    footer { position: fixed; left: 0px; bottom: 0px; right: 0px; height: 100px; background-color: white; background-image: url('{!! asset("public/".$company->pdf_watermark."") !!}');}
    footer .page:after { content: counter(page, upper-roman); }

    body{font-family: Verdana, sans-serif; font-size:11px; color:#555555; background-image: url('{!! asset("public/".$company->pdf_watermark."") !!}');}
    th, td {padding: 5px 0px;}
    .tdd{border:solid 0px #f5f5f5; border-width:0 0 0px 0;}
    span{font-size:15px; font-weight: bold; color: #4b4b4b;}
    main{margin:0px 0px 100px 0px;}
    .main2{margin:0px 40px;}

    .dttable {}
    .dttable th, .dttable td {padding: 3px 5px; text-align:left; border:solid 0px #e7e6e6; border-width:0px; font-size: 11px;}
    .dttable th{background: #d9d9d9;}
    
    .dttable2 {}
    .dttable2 th, .dttable2 td {padding: 3px 5px; text-align:left; border:solid 1px #e7e6e6; border-width:0px 1px 1px 1px; font-size: 11px;}
    .dttable2 th{background: #d9d9d9;}

    .algc{text-align: left !important; font-weight: bold; background: #f2f2f2; color: #ffffff;}
    .subhd{font-weight: bold; background: #f2f2f2; color: #808080;}
    .cathd{font-weight: bold; background: #f2f2f2; color: #808080; text-align: center;}
    ol li {color: #808080;}
    hr {border: solid 0px #f5f5f5; background: #f5f5f5; height: 1px;}
    .page-break { page-break-after: always; }
    /* Page number helper */
    .pagenum:before { content: counter(page); }
    body{background-image: url('{!! asset("public/".$pdfwatermark."") !!}');}

    .item-head-row {background: #2c2b6d; color: #ffffff;}
    .item-head-row td {border: solid 2px #2c2b6d !important; padding:5px !important; margin:0px !important; }
       .item-row {border-bottom: solid 1px #2c2b6d;}


    .row {
  display: flex;
  flex-wrap: wrap;
  margin-right: -0.75rem;  /* v4 */
  margin-left: -0.75rem;
}

.col-8 {
  flex: 0 0 auto;
  width: 66.66666667%;
}

.col-4 {
  flex: 0 0 auto;
  width: 33.33333333%;
}


</style>





</head>
<body>
    {{-- ************* --}}
  <main class="m2" style="margin-top:-130px; ">

                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td align="left" valign="top"><img src="{{ asset('public/'.@$company->company_logo) }}" width="200px" />
                            </td>
                            <td align="right" valign="top"><b style="font-size: 30px; font-weight: 400;">Quotation</b>
                            </td>
                        </tr>
                    </table>
                    <br />
                    <div style="margin-left:10px">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="60%" style="line-height: 18px; vertical-align: top;width:70% !important;">
                                    <b style="font-size: 14px;">To: <br />{{ $quotation->customername->name }}</b><br />
                                    <span style="font-size: 12px;">Attn: {{ $quotation->cust_name }}</span><br />
                                  
                                    <p style="padding: 0px; margin: 0px; width: 250px;">
                                        {{ @$quotation->customername->addresses->first()->statename->name }},
                                        {{ @$quotation->customername->addresses->first()->countryname->name }}</p>
                                    @if ($quotation->cust_no != '')<b>Tel :</b>
                                        {{ @$quotation->cust_no }}<br />@endif
                                    @if ($quotation->cust_email != '')<b>Email :</b>
                                        {{ @$quotation->cust_email }}@endif
                                    <br /><br />
                                    {{-- <b style="font-size: 12px;">By</b>
                                    <br />
                                    <b>{{ $quotation->ownername->full_name }}</b>
                                    <br />
                                        <b>{{ @$quotationitems[0]->company->company_name }}</b>     --}}
                                </td>

                                <td style="line-height: 18px; vertical-align: top; float: right;">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0"
                                        style="margin-top: 0;">
                                        <tr>
                                            <td style="padding: 0px; margin: 0px; width: 90px;">Quote No</td>

                                            <td style="padding: 0px; margin: 0px">: {{ $quotation->code }} @if (@$quotationitems[0]->quote_id != 1 && @$quotationitems[0]->quote_id != null) -
                                                    {{ @$quotationitems[0]->quote_id - 1 }} @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 0px; margin: 0px;">Quote Date</td>
                                            <td style="padding: 0px; margin: 0px">:
                                                {{ !empty($quotationitems[0]->created_at) ? date('d/m/Y', strtotime($quotationitems[0]->created_at)) : date('d/m/Y', strtotime($quotation->created_at)) }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="padding: 0px; margin: 0px;">Quote Validity</td>
                                            <td style="padding: 0px; margin: 0px">:
                                                {{ @$quotationitems[0]->quote_validity }}</td>
                                        </tr>



                                        @if ($deliverytime != '')
                                            <tr>
                                                <td style="padding: 0px; margin: 0px;"> Delivery Time</td>
                                                <td style="padding: 0px; margin: 0px">: {{ $deliverytime }}</td>
                                            </tr>

                                        @endif

                                        <tr>
                                            <td
                                                style="padding: 0px; margin: 0px;vertical-align: top; white-space: nowrap;">
                                                Payment Terms</td>
                                           <td style="padding:0; margin:0; white-space:nowrap; max-width:250px; overflow:hidden; text-overflow:ellipsis;">
    : {{ $paymentterms }}
</td>

                                        </tr>

                                        @if (@$quotationitems[0]->nooflocation != '')
                                            <tr>
                                                <td style="padding: 0px; margin: 0px;"> No of Locations</td>
                                                <td style="padding: 0px; margin: 0px">:
                                                    {{ @$quotationitems[0]->nooflocation }}</td>
                                            </tr>

                                        @endif

                                        @if (@$quotationitems[0]->connectivity != '')
                                            <tr>
                                                <td style="padding: 0px; margin: 0px;"> Connectivity Required</td>
                                                <td style="padding: 0px; margin: 0px">:
                                                    {{ @$quotationitems[0]->connectivity }}</td>
                                            </tr>

                                        @endif

                                        @if (@$quotationitems[0]->telephonetype != '')
                                            <tr>
                                                <td style="padding: 0px; margin: 0px;"> ISP Telephone Type</td>
                                                <td style="padding: 0px; margin: 0px">:
                                                    <?php $string = @$quotationitems[0]->telephonetype;
                                    $str_arr = explode (",", $string);
                                    $string_val = @$quotationitems[0]->nolines;
                                    $str_arr2 = explode (",", $string_val);
                                    for($i=0; $i< count($str_arr); $i++) { ?>
                                                    {{ $str_arr[$i] }} - {{ $str_arr2[$i] }} Line,
                                                    <?php } ?></td>
                                            </tr>

                                        @endif

                                    </table>

                                </td>
                            </tr>
                        </table>
                    </div>



                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td colspan="3" align="center"><u><b style="font-size: 15px;">
                                        @if (count($quotationitems) > 0)
                                            {{-- Quotation --}}
                                        @else
                                            Quotation Not Yet Generated
                                        @endif

                                    </b></u></td>
                        </tr>
                    </table>



                    @if (count($quotationitems) > 0)

                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr class="item-head-row">
                                <td width="5%" style="text-align: center;">No</td>
                                <td width="70%">Description</td>
                                {{-- <td style="width: 70px; text-align:right !important;">Cost</td> --}}
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
                        @if ($val->status != 0)
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td class="item-row" style="width: 5%;text-align: center;">{{ $srl++ }}
                                    </td>
                                    <td class="item-row" style="width: 70%;font-size: 11px;">

                                        <b style="font-size: 11px;"> {{ $val->productname->part_number }} </b> <br>
                                        {!! nl2br($val->description) !!}
                                    </td>
                                    {{-- <td class="item-row" width="70px" style="text-align:right !important;">
                                        {{ App\SysHelper::currancy_format($val->cost, $val->currency_id) }}</td> --}}

                                    <td class="item-row" width="5%" style="text-align: center;">
                                        {{ $val->qty }}</td>
                                    <td class="item-row" width="10%" style="text-align: right;">
                                        {{ App\SysHelper::currancy_format($val->price, $val->currency_id) }}</td>
                                    <td class="item-row" width="10%" style="text-align: right;">
                                        <?php $subtotal += $val->price * $val->qty; ?>
                                        <?php $discount += $val->discount; //$discount += $val->discount * $val->qty; ?>
                                        {{ App\SysHelper::currancy_format($val->qty * $val->price, $val->currency_id) }}
                                    </td>
                                </tr>
                            </table>
                        @endif


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
                        @if ($wt != 1)

                    <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:12px;">
                        <tr valign="top">
                            <td style="width:66%; padding-right:12px; vertical-align: top;">





                                    <table width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td class="font-weight-600">{{ @$currency_modal->code }}
                                                <?php echo ucwords(@App\SysHelper::convertAmountToWords($subtotal - $discount - $deal_discount_vat_amount + $vat, @$currency_modal->r_code, @$currency_modal->p_code)); ?></td>

                                            </td>

                                        <tr>
                                            <td>
                                                <b>Terms & Conditions</b><br />

                                                @if ($quotation->terms_and_condition == '')
                                                    <ol style="padding-left: 15px; font-size: 11px">
                                                        <li>Quote/Order will be subject to approval of payment/credit
                                                            terms by {{ $quotationitems[0]->company->company_name }}.
                                                        </li>
                                                        <li>Please mention our Quotation No.in your Purchase Order</li>
                                                        <li>In case of non-availability of quote products
                                                            {{ $quotationitems[0]->company->company_name }} reserved
                                                            the rights to supply a functionally similar or better
                                                            product.</li>
                                                    </ol>
                                                @else
                                                    <div style="padding-top: 2px; font-size: 11px">
                                                        {!! nl2br($quotation->terms_and_condition) !!}


                                                    </div>
                                                @endif
                                            </td>

                                        </tr>
                                    </table>

                            </td>
                            <td style="width:34%; padding-left:12px; vertical-align: top;">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td></td>
                                            <td
                                                style="width: 160px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                                Total {{ $currency }}</td>
                                            <td
                                                style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                                {{ App\SysHelper::currancy_format($subtotal, $currency_id) }}</td>
                                        </tr>
                                    </table>
                                    @if ($discount > 0)
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td></td>
                                                <td
                                                    style="width: 160px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                                    Discount {{ $currency }}</td>
                                                <td
                                                    style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                                    {{ App\SysHelper::currancy_format($discount, $currency_id) }}</td>
                                            </tr>
                                        </table>
                                    @endif
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td></td>
                                            <td
                                                style="width: 160px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                                Sub Total {{ $currency }}</td>
                                            <td
                                                style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                                {{ App\SysHelper::currancy_format($subtotal - $discount, $currency_id) }}
                                            </td>
                                        </tr>
                                    </table>
                                    @if ($wv != 1)
                                        @if ($vat - $deal_discount_vat_amount != 0)
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td></td>
                                                    @if ($currency == 'INR')
                                                        <td
                                                            style="width: 160px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                                            GST {{ $currency }}</td>
                                                    @elseif($currency == 'USD')
                                                        <td
                                                            style="width: 160px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                                            TAX {{ $currency }}</td>
                                                    @else
                                                        <td
                                                            style="width: 160px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                                            VAT {{ $currency }}</td>
                                                    @endif
                                                    <td
                                                        style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                                        {{ App\SysHelper::currancy_format($vat - $deal_discount_vat_amount, $currency_id) }}</b>
                                                    </td>
                                                </tr>
                                            </table>
                                        @endif
                                    @endif
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td></td>
                                            <td
                                                style="width: 160px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                                Net Amount {{ $currency }}</td>
                                            <td
                                                style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                                {{ App\SysHelper::currancy_format($subtotal - $discount - $deal_discount_vat_amount + $vat, $currency_id) }}
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                        </tr>
                    </table>
                        @endif

                       
                </div>

            </div>




            @endif

            <br><br><br><br><br>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td style="width:14%; border: none;" align="left" valign="bottom">
                        {{ @$quotation->ownername->full_name }}<br /><br /><br /><b class="bottom_b">Prepared
                            By</b></td>
                    <td style="width:60%; border: none;" align="center" valign="bottom">This document
                        is computer generated Signature is not required</td>
                    <td style="width:20%; border: none;" align="right" valign="bottom">
                        {{ @$company->company_name }}<br /><br /><br /><b class="bottom_b">Authorised
                            Signature</b></td>
                </tr>
            </table>
             <footer>

                <img src="{!! asset('public/' . $company->pdf_footer . '') !!}" width="100%" /></td>
            </footer>


         </main>


            {{-- ************* --}}
</body>
</html>
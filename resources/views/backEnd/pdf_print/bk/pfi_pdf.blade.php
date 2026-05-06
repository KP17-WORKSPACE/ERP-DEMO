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
    @page { margin: 20px 20px; }
    header { position: fixed; left: 0px; top: -100px; right: 0px; height: 100px; background-color: white; text-align: center; }
    footer { position: fixed; left: 0px; bottom: -50px; right: 0px; height: 70px; background-color: white; }
    footer .page:after { content: counter(page, upper-roman); }

    body{font-family: Verdana, sans-serif; font-size:12px; color:#555555; background-image: url('{!! asset("public/backEnd/img/".$company->pdf_watermark."") !!}');}
    th, td {padding: 5px 0px;}
    .tdd{border:solid 0px #f5f5f5; border-width:0 0 0px 0;}
    span{font-size:15px; font-weight: bold; color: #4b4b4b;}
    main{margin:0px 0px;}
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

    .item-head-row {background: #2c2b6d; color: #ffffff;}
    .item-head-row td {border: solid 2px #2c2b6d !important; padding:5px !important; margin:0px !important; }
    .item-row {border-bottom: solid 1px #dfdfdf !important; border-top: solid 0px #dfdfdf !important; }
    .item-row span{font-size: 11px;}
</style>


</head>
<?php try { ?>
<body>
    <main class="m2">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
              <td align="left"><img  src="{{asset(@$company->company_logo)}}" width="200px"/></td>
              <td align="right"><b style="font-size: 30px; font-weight: 400;">Profoma Invoice</b></td>
          </tr>
      </table>
      <br />
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="55%" valign="top" style="line-height: 18px;">
            <b>{{@$company->company_name}}</b>
            <div>{!! nl2br($company->company_address) !!}</div>
            Phone: {{@$company->telephone}}<br />
            Email: {{@$company->email}}<br />
            @if($company->vat_number !=0 && $company->vat_number != "")
            TRN No: {{@$company->vat_number}}
            @endif
          </td>
          <td valign="top" style="line-height: 13px;">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td align="right" style="line-height: ;">PFI No:</td>
                    <td align="right" style="line-height: ;">{{@$pfi->doc_number}}</td>
                  </tr>
                  <tr>
                    <td align="right" style="line-height: ;">PFI Date:</td>
                    <td align="right" style="line-height: ;">{{date('d/m/Y', strtotime(@$pfi->doc_date))}}</td>
                  </tr>
                  <tr>
                    <td align="right" style="line-height: ;">Ref No:</td>
                    <td align="right" style="line-height: ;">{{@$pfi->reference_no}}</td>
                  </tr>
                  <tr>
                    <td align="right" style="line-height: ;">Ref Date:</td>
                    <td align="right" style="line-height: ;">{{date('d/m/Y', strtotime(@$pfi->reference_date))}}</td>
                  </tr>
                  <tr>
                    <td align="right" style="line-height: ;">Payment Terms:</td>
                    <td align="right" style="line-height: ;">{{ @$des=App\SysPaymentTerms::getPaymentTermsName($pfi->payment_terms)}}</td>
                  </tr>
            </table>
          </td>
        </tr>
    </table>
    <br />
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="55%" valign="top" style="line-height: 18px;">Bill To,<br />
                <b style="font-size: 90%;">{{@$quotation->customername->name}}</b><br>
                {{@$contact_name}}<br />
                {{@$address}}<br>
                {{@$address2}}, {{@$city}}<br>
                {{@$state}}, {{@$country}}<br>
                T: {{@$tel}}<br/>
                E: {{@$email}}<br/>
                TRN : {{ $vat_number }}
          </td>
          <td valign="top" style="line-height: 18px;">Ship To,<br />
            <b style="font-size: 90%;">{{@$quotation->customername->name}}</b><br>
            {{@$ship_contact_name}}<br />
            {{@$address}}<br>
                {{@$address2}}, {{@$city}}<br>
                {{@$state}}, {{@$country}}<br>
            {{--  {{@$ship_address1}}<br>
            {{@$ship_address2}}, {{@$delivery_city}}<br>
            {{@$delivery_state  }}, {{@$delivery_country}}<br>  --}}

            T: {{@$ship_tel}}<br/>
            E: {{@$ship_email}}<br/>
            TRN : {{ $vat_number }}
          </td>
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
            $i=1;
            $sub_total=0;
            $discount=0;
            $taxable_amt=0;
            $customs_charges=0;
            $vat_amount=0;
            $total_amount=0;
            $currency = $pfi->currency_name->code;
        ?>
        @if(count($pfi_item)>0)
        @foreach ($pfi_item as $item)
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
        <td  style="width: 30px;" class="item-row">{{$i}} <?php $i++;?></td>
        <?php @$des=App\SmItem::getItemDes($item->part_number); ?>
        <td style="width: 300px;" class="item-row">{!! nl2br($des) !!}</td>
            <td  style="width: 20px; text-align: center !important;" class="item-row">{{ $item->qty }}</td>
            <td  style="width: 70px; text-align: right !important;" class="item-row">{{ @App\SysHelper::com_curr_format($item->unitprice,2,'.',',') }}</td>
            <td  style="width: 70px; text-align: right !important;" class="item-row">{{ @App\SysHelper::com_curr_format($item->unitprice*$item->qty,2,'.',',') }}</td>            
            <td  style="width: 80px; text-align: right !important;" class="item-row">{{ @App\SysHelper::com_curr_format($item->vatamount,2,'.',',') }}</td>
            <td  style="width: 80px; text-align: right !important;" class="item-row">{{ @App\SysHelper::com_curr_format(($item->taxableamount+$item->vatamount),2,'.',',') }}</td>
            <?php
            
            $sub_total += $item->value;
            $discount += $item->discount;
            $taxable_amt += $item->taxableamount;
            $customs_charges += $item->customcharges;
            $vat_amount += $item->vatamount;
            $total_amount += $item->unitprice*$item->qty;

            ?>


        </tr>
    </table>
        @endforeach
        
        <?php
        $deal_discount_vat=$quotationitems->max('vat');
        $deal_discount_vat_amount= $quotation->deal_discount*$deal_discount_vat/100;
        ?>
        
        @endif

    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td></td>
            <td style="width: 110px; text-align: left; font-weight: bold; border-bottom: solid 1px #dfdfdf;"><b>Total {{$currency}}</b></td>
            <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #dfdfdf;"><b>{{ @App\SysHelper::com_curr_format($total_amount, 2, '.', ',') }}</b></td>
        </tr>
    </table>
    @if ($quotation->deal_discount+$discount != 0)
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td></td>
            <td style="width: 110px; text-align: left; font-weight: bold; border-bottom: solid 1px #dfdfdf;"><b>Discount {{$currency}}</b></td>
            <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #dfdfdf;"><b>{{ @App\SysHelper::com_curr_format($quotation->deal_discount+$discount, 2, '.', ',') }}</b></td>
        </tr>
    </table>
    @endif
    <table width="100%" border="0" cellspacing="0" cellpadding="0">                
        <tr>
            <td></td>
            <td style="width: 110px; text-align: left; font-weight: bold; border-bottom: solid 1px #dfdfdf;"><b>Sub Total {{$currency}}</b></td>
            <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #dfdfdf;"><b>{{ @App\SysHelper::com_curr_format($total_amount - ($quotation->deal_discount+$discount), 2, '.', ',') }}</b></td>
        </tr>
    </table>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td></td>
            @if($currency=="INR")
            <td style="width: 110px; text-align: left; font-weight: bold; border-bottom: solid 1px #dfdfdf;"><b>GST {{$currency}}</b></td>
            @elseif($currency=="USD")
            <td style="width: 110px; text-align: left; font-weight: bold; border-bottom: solid 1px #dfdfdf;"><b>TAX {{$currency}}</b></td>
            @else
            <td style="width: 110px; text-align: left; font-weight: bold; border-bottom: solid 1px #dfdfdf;"><b>VAT {{$currency}}</b></td>
            @endif
            <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #dfdfdf;"><b>{{ @App\SysHelper::com_curr_format(($vat_amount - $deal_discount_vat_amount), 2, '.', ',') }}</b></td>
        </tr>
    </table>
    <?php
        $net_amount = ($total_amount - ($quotation->deal_discount+$discount)) + ($vat_amount - $deal_discount_vat_amount);
    ?>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td></td>
            <td style="width: 110px; text-align: left; font-weight: bold; border-bottom: solid 1px #dfdfdf;"><b>Net Amount {{$currency}}</b></td>
            <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #dfdfdf;"><b>{{ @App\SysHelper::com_curr_format($net_amount, 2, '.', ',') }}</b></td>
        </tr>
    </table>

    
    <br />
    <br />
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <b>Terms & Conditions</b><br/>
    
                @if($quotation->terms_and_condition == "")
                <ol style="padding-left: 15px; font-size: 11px">
                    <li>Order will be subject to approval of payment/credit terms by {{@$company->company_name}}.</li>
                    <li>In case of non-availability of products {{@$company->company_name}} reserved the rights to supply a functionally similar or better product.</li>
                    <li>All payment transfer charges should be borne by the sender only.</li>
                    <li>Bank details:- Bank Name: {{@$company->bank_name}}, Account Number: {{@$company->account_number}}</li>
                </ol>
            @else
            <div style="padding: 5px; font-size: 11px">
            <?php
            $string = $quotation->terms_and_condition;
            
            $bits = explode("\n", $string);
            
            $newstring = "<ol style='padding:0px; margin:0px 0px 0px 10px;'>";
            foreach($bits as $bit)
            {
              $newstring .= "<li>" . substr($bit, 2) . "</li>";
            }
            $newstring .= "</ol>";
            ?>
        {!! $newstring !!}
        </div>
            @endif
            </td>
        </tr>
      </table>
      <br ><br ><br ><br ><br >
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td style="width:35%; border: none;" align="left" valign="bottom">{{@$pfi->salesman->full_name}}<br/><br/><br/><b class="bottom_b">Prepared By</b></td>
          <td style="width:35%; border: none;" align="center" valign="bottom">This document is computer generated Signature is not required</td>
          <td style="width:35%; border: none;" align="right" valign="bottom">{{@$company->company_name}}<br /><br/><br/><b class="bottom_b">Authorised Signature</b></td>
        </tr>
      </table>


  {{-- <table width="100%">
    <tr>
        <td colspan="3"><b>Company Detail</b></td>
    </tr>
    <tr>
        <td width="30%">Name Of The Company</td><td width="1%"> : </td><td width="69%" class="tdd">{{ $formInfo["nameofthecompany"] }}</td>
    </tr>
    <tr>
        <td>Type Of Company</td><td> : </td><td class="tdd">{{ $formInfo["typeofcompany"] }}</td>
    </tr>
    <tr>
        <td>Business Market</td><td> : </td><td class="tdd">{{ $formInfo["businessmarket"] }}</td>
    </tr>
    <tr>
        <td>Trade License Number</td><td> : </td><td class="tdd">{{ $formInfo["tradelicensenumber"] }}</td>
    </tr>
    <tr>
        <td>Issuing Authority</td><td> : </td><td class="tdd">{{ $formInfo["issuingauthority"] }}</td>
    </tr>
    <tr>
        <td>Issued Date</td><td> : </td><td class="tdd">{{ dateConvertDBtoForm($formInfo["issueddate"]) }}</td>
    </tr>
    <tr>
        <td>Expiry Date</td><td> : </td><td class="tdd">{{ dateConvertDBtoForm($formInfo["expirydate"]) }}</td>
    </tr>
    <tr>
        <td>Office Address</td><td> : </td><td class="tdd">{{ $formInfo["postaladdressandlocation"] }}</td>
    </tr>
    <tr>
        <td>Telephone Number</td><td> : </td><td class="tdd">{{ $formInfo["telephonenumber"] }}</td>
    </tr>
    <tr>
        <td>Email ID</td><td> : </td><td class="tdd">{{ $formInfo["emailid"] }}</td>
    </tr>
    <tr>
        <td>Company Turnover</td><td> : </td><td class="tdd">{{ $formInfo["companyturnover"] }}</td>
    </tr>
    <tr>
        <td colspan="3">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="3" style="background: #f2f2fe; padding:20px;">
            <table width="100%">
                <tr>
                    <td colspan="4"><b>Company Owner Details</b></td>
                </tr>
                
                <tr>
                    <td width="30%">Owner/Partners Name</td><td width="35%" class="tdd">{{ $val["ownerpartnersname"] }}</td>
                    <td width="15%" align="center">Mobile</td><td width="20%" class="tdd">{{ $val["ownerpartnersmobile"] }}</td>
                </tr>
                <tr>
                    <td colspan="4"></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="3" style="background: #f2f2fe; padding:20px;">
            <table width="100%">
                <tr>
                    <td colspan="4"><b>Company Sponsor Detail</b></td>
                </tr>
                <tr>
                    <td width="30%">Sponsor Name </td><td width="70%" class="tdd">{{ $formInfo["nameofthecompanysponsor"] }}</td>
                </tr>
                <tr>
                    <td colspan="4"></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="3">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="3">
            <table width="100%">
                <tr>
                    <td colspan="5"><b>Key Contact Persons</b></td>
                </tr>
                <tr>
                    <td width="15%">Purchase :</td>
                    <td width="10%">Name</td><td colspan="3" class="tdd">{{ $formInfo["purchasename"] }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td>Mobile</td><td class="tdd">{{ $formInfo["purchasemobile"] }}</td>
                    <td align="center">Email </td><td class="tdd">{{ $formInfo["purchaseemail"] }}</td>
                </tr>
                <tr>
                    <td width="15%">Accounts :</td>
                    <td width="10%">Name</td><td colspan="3" class="tdd">{{ $formInfo["accountsname"] }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td>Mobile</td><td class="tdd">{{ $formInfo["accountsmobile"] }}</td>
                    <td align="center">Email </td><td class="tdd">{{ $formInfo["accountsemail"] }}</td>
                </tr>
                <tr>
                    <td colspan="5"></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="3" style="background: #f2f2fe; padding:20px;">
            <table width="100%">
                <tr>
                    <td colspan="2"><b>Bank Details</b></td>
                </tr>
                <tr>
                    <td width="30%">Bank</td><td width="70%" class="tdd">{{ $val["bankname"] }}</td>
                </tr>
                <tr>
                    <td width="30%">Branch</td><td width="70%" class="tdd">{{ $val["branchname"] }}</td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="3">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="3">
            <table width="100%">
                <tr>
                    <td colspan="6"><b>Presently Credit Facilities With Suppliers</b></td>
                </tr>
                <tr>
                    <td width="15%">Supplier Name</td><td width="35%" class="tdd">{{ $val["suppliername"] }}</td>
                    <td width="13%">Credit Limit</td><td width="10%" class="tdd">{{ $val["suppliercreditlimit"] }}</td>
                    <td width="12%">Credit Days</td><td width="5%" class="tdd">{{ $val["suppliercreditdays"] }}</td>
                </tr>
                <tr>
                    <td colspan="6"></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="3">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="3" style="background: #f2f2fe; padding:20px;">
            <table width="100%">
                <tr>
                    <td colspan="4"><b>Credit Request</b></td>
                </tr>
                <tr>
                    <td colspan="4">Expected Quarterly Volume with</td>
                </tr>
                <tr>
                    <td width="60%">Syscom Distribution LLC: ({{ $pfi->currency_name->code }})</td><td width="40%" class="tdd">{{ $formInfo["amountaed"] }}</td>
                </tr>
                <tr>
                    <td width="60%">Mode Of Payment</td><td width="40%" class="tdd">{{ $formInfo["amountmode"] }}</td>
                </tr>
                <tr>
                    <td colspan="4"></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="3">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="3"><b>Credit Policies</b></td>
    </tr>
    <tr>
        <td colspan="3">1. In consideration of the extension of credit and establishment of a credit account, applicant acknowledges liability for payment of amounts due to SYSCOM for purchase of products or services.</td>
    </tr>
    <tr>
        <td colspan="3">2. If SYSCOM must take action to collect any outstanding amount, applicant agrees to pay all reasonable costs and expenses incurred in collection including attorney's fees, court fees, court costs and interest thereon at maximum legal rate.</td>
    </tr>
    <tr>
        <td colspan="3">3. In case of any change in the constitution of the applicant's organization, the applicant should inform SYSCOM immediately with appropriate documents.</td>
    </tr>
    <tr>
        <td colspan="3">4. SYSCOM reserves the right to change its policies, terms and conditions and the applicant agrees to abide by the same.</td>
    </tr>
    <tr>
        <td colspan="3">5. The applicant does not have any objection in case SYSCOM contacts their bankers or any other trade references for information.</td>
    </tr>
    <tr>
        <td colspan="3"><p></p></td>
    </tr>
    <tr>
        <td colspan="3"><b>Declaration</b></td>
    </tr>
    <tr>
        <td colspan="3">I Mr./Mrs./Ms. ............................................. (Owner/Director/Partner) Of ............................................. Hereby Declare that the particulars furnished herein are true and correct. And also agrees to abide by the terms and conditions of Syscom Distribution LLC.</td>
    </tr>
    <tr>
        <td colspan="2"><b>Signature</b></td>
        <td style="text-align: right;"><b>Company stamp</b></td>
    </tr>
  </table> --}}
  </main>
</body>

<?php }catch (\Exception $e) { ?> {{ $e }} <?php } ?>

<?php
function getIndianCurrency(float $number, string $r1, string $r2)
{
    $decimal = round($number - ($no = floor($number)), 2) * 100;
    $hundred = null;
    $digits_length = strlen($no);
    $i = 0;
    $str = array();
    $words = array(0 => '', 1 => 'One', 2 => 'Two',
        3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
        7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
        10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
        13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
        16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
        19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
        40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
        70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety');
    $digits = array('', 'Hundred','Thousand','', 'Crore');
    while( $i < $digits_length ) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += $divider == 10 ? 1 : 2;
        if ($number) {
            $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
        } else $str[] = null;
    }
    $Rupees = implode('', array_reverse($str));
    $paise = ($decimal > 0) ? "." . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . " " .$r2 : '';
    return ($Rupees ? $Rupees . $r1 : ' ') . $paise;
}
?>
</html>
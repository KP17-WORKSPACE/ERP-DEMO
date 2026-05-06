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
    @page { margin: 100px 0px; }
    header { position: fixed; left: 20px; top: -50px; right: 20px; height: 80px; background-color: white; text-align: center; border-bottom:solid 1px #808080; color:#555555;  }
    footer { position: fixed; left: 0px; bottom: -100px; right: 0px; height: 100px; background-color: white; }
    footer .page:after { content: counter(page, upper-roman); }

    body{font-family: Verdana, sans-serif; font-size:12px; color:#555555;}
    th, td {padding: 5px 5px;}
    .tdd{border:dashed 1px #9e9e9e; border-width:0 0 1px 0;}
    b{font-size:14px;}
    main{margin:40px 20px;}
    .m1 table { border: 0px solid #9e9e9e; }
    .m1 td { border: 1px solid #9e9e9e; }
    .tmc ol {padding: 0px; margin: 0px;}
    .bottom_b {font-size:12px; }
</style>


</head>
<body>
    <header>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td align="left"><img  src="{{asset(@$company->company_logo)}}" width="200px"/></td>
                <td align="right"><b>Purchase Order</b></td>
            </tr>
        </table>
      
    </header>
    <footer>
      {{-- <img  src="{!! asset('admin_assets/dist/img/pdf-footer.jpg') !!}" width="100%"> --}}
    </footer>
  <main class="m1">
    
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td rowspan="5" width="50%" valign="top">TO,<br />
            {{@$company->company_name}}<br />
            {{@$company->company_address}}<br />
            {{@$company->telephone}}<br />
            {{@$company->email}}
          </td>
          <td>Purchase Order No</td>
          <td>{{@$po->doc_number}}</td>
        </tr>
        <tr>
          <td>Purchase Order Date</td>
          <td>{{date('jS M, Y', strtotime(@$po->po_date))}}</td>
        </tr>
        <tr>
          <td>Delivery Date</td>
          <td>{{date('jS M, Y', strtotime(@$po->delivery_date))}}</td>
        </tr>
        <tr>
          <td>Payment Terms</td>
          <td rowspan="2">@if(@$po->payment_terms !=105) {{ @$des=App\SysPaymentTerms::getPaymentTermsName($po->payment_terms)}} @else {{@$po->payment_terms2}} @endif</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
        </tr>
    </table>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="50%">
            <b>Bill To Address</b><br>
                {{@$company->company_name}}<br>
                {{@$company->company_address}}<br>
                {{@$company->telephone}}<br>
                {{@$company->email}}
          </td>
          <td width="50%">
            <b>Ship To Address</b><br>  
            @if(!empty(@$po->shipping_name))
                {{@$po->shipping_name}}<br>
                {{@$po->shipping_contact_no}}<br>
                {{@$po->shipping_address_1}}<br>
                {{@$po->shipping_address_2}}
            @else
                {{@$company->company_name}}<br>
                {{@$company->company_address}}<br>
                {{@$company->telephone}}<br>
                {{@$company->email}}
            @endif
          </td>
        </tr>
      </table>
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td style="width: 20px;">No</td>
          <td>Description</td>
          <td style="width: 20px;">Qty</td>
          <td style="width: 70px;">Rate</td>
          <td style="width: 70px;">Taxable</td>
          <td style="width: 30px;">VAT%</td>
          <td style="width: 70px;">VAT Amount</td>
          <td style="width: 70px;">Amount</td>
        </tr>
        <?php
            $i=1;
            $sub_total=0;
            $discount=0;
            $taxable_amt=0;
            $customs_charges=0;
            $vat_amount=0;
            $total_amount=0;
        ?>
        @if(count($po_item)>0)
        @foreach ($po_item as $item)
        <tr>
        <td>{{$i}} <?php $i++;?></td>
        <?php @$des=App\SmItem::getItemDes($item->part_number); ?>
        <td>{!! nl2br($des) !!}</td>
            <td>{{$item->qty}}</td>
            <td align="right">{{$item->unitprice}}</td>
            <td align="right">{{$item->unitprice * $item->qty}}</td>
            <td>{{$item->tax}}</td>
            <td align="right">{{$item->vatamount}}</td>
            <td align="right">{{$item->taxableamount + $item->vatamount}}</td>
            <?php
            
            $sub_total += $item->value;
            $discount += $item->discount;
            $taxable_amt += $item->taxableamount;
            $customs_charges += $item->customcharges;
            $vat_amount += $item->vatamount;
            $total_amount += $item->taxableamount + $item->vatamount;

            ?>


        </tr>
        @endforeach
        @endif

        <tr>
          <td colspan="5" rowspan="2">AED  <?php echo ucwords(@App\SysHelper::convertAmountToWords($total_amount));?></td>
          <td colspan="2">Sub Total AED</td>
        <td align="right">{{ @App\SysHelper::com_curr_format($total_amount, 2, '.', ',') }}</td>
        </tr>
        <tr>
          <td colspan="2">Discount AED</td>
        <td align="right">{{ @App\SysHelper::com_curr_format($discount, 2, '.', ',') }}</td>
        </tr>
        <tr>
          <td colspan="5" rowspan="4" class="tmc">
            <b>Terms & Conditions</b>
            <ul style="padding: 10px 0px 0px 15px; margin: 0px;">
                <li>Kindly mention the LPO number on all correspondence, invoice and delivery notes.</li>
                <li>In the event of your failing to deliver or execute the said order on or before the stiputated date or such extended time as permitted by us, SYSCOM Distributions LLC. reserves the full right and authority to cancle such order and such incidents shall govern the future business relationship and order placements with your organization.</li>
                <li>The supplier shall at its oen coast,replace and/or rectify the goods supplied, in the event of any defects in the material.</li>
            </ul>
          </td>
          <td colspan="2">Taxable Amt. AED</td>
        <td align="right">{{ @App\SysHelper::com_curr_format($taxable_amt, 2, '.', ',') }}</td>
        </tr>
        <tr>
          <td colspan="2">Customs Charges</td>
        <td align="right">{{ @App\SysHelper::com_curr_format($customs_charges, 2, '.', ',') }}</td>
        </tr>
        <tr>
          <td colspan="2">VAT Amount AED</td>
          <td align="right">{{ @App\SysHelper::com_curr_format($vat_amount, 2, '.', ',') }}</td>
        </tr>
        <tr>
          <td colspan="2">Total Amount AED</td>
        <td align="right" style="font-weight: bold;">{{ @App\SysHelper::com_curr_format($total_amount, 2, '.', ',') }}</td>
        </tr>
      </table>
      <br ><br ><br >
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td style="border: none;" align="left" valign="bottom">{{@$po->createdby->full_name}}<br/><b class="bottom_b">Prepared By</b></td>
          <td style="border: none;" align="center" valign="bottom">This document is computer generated Signature is not required</td>
          <td style="border: none;" align="right" valign="bottom">{{@$company->company_name}} <br /><b class="bottom_b">Authorised Signature</b></td>
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
                    <td width="60%">Syscom Distribution LLC: (AED)</td><td width="40%" class="tdd">{{ $formInfo["amountaed"] }}</td>
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

<?php
function getIndianCurrency(float $number)
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
    $digits = array('', 'Hundred','Thousand','Lakh', 'Crore');
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
    $paise = ($decimal > 0) ? "." . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
    return ($Rupees ? $Rupees . 'Rupees ' : ' ') . $paise;
}
?>
</html>
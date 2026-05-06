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
    .droid {
      font-family: 'Droid Arabic Kufi', serif;
    }
    
    @page { margin: 20px 20px; }
    header { position: fixed; left: 20px; top: -50px; right: 20px; height: 80px; background-color: white; text-align: center; border-bottom:solid 0px #808080; color:#555555;  }
    footer { position: fixed; left: 0px; bottom: -100px; right: 0px; height: 100px; background-color: white; }
    footer .page:after { content: counter(page, upper-roman); }

    body{font-family: Verdana, sans-serif; font-size:12px; color:#555555; background-image: url('{!! asset("public/".$company->pdf_watermark."") !!}');}

    th, td {padding: 5px 5px;}
    .tdd{border:dashed 1px #9e9e9e; border-width:0 0 1px 0;}
    b{font-size:14px;}
    main{margin:0px 0px;}
    .m1 table { border: 0px solid #9e9e9e; }
    .m1 td { border: 1px solid #9e9e9e; }
    .tmc ol {padding: 0px; margin: 0px;}
    .bottom_b {font-size:12px; }
    .page-break { page-break-after: always; }
    .m-0{margin: 0px;}
    .p-0{padding: 0px;}
    .item-head-row {background: #2c2b6d; color: #ffffff; }
    .item-row {border-bottom: solid 1px #2c2b6d;}
</style>

</head>
<body>
  <?php /*
    <header>
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td align="left"><img  src="{{asset(@$company->company_logo)}}" width="200px"/></td>
            <td align="right"><b style="font-size: 30px; font-weight: 400;">Sales Invoice</b></td>
        </tr>
    </table>
    </header>
    <footer>
      {{-- <img  src="{!! asset('admin_assets/dist/img/pdf-footer.jpg') !!}" width="100%"> --}}
    </footer>
     */ ?>
  <main class="m2">
     <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
          <td align="left" valign="top" style="padding: 0; margin: 0; vertical-align: top;"><img  src="{{asset('public/'.@$company->company_logo)}}" width="200px"/></td>
          <td align="right" valign="top" style="padding: 0; margin: 0; vertical-align: top; text-align: right;"><b style="font-size: 30px; font-weight: 400;">

          @if ($pr->debit_note == "DN")
            Debit Note
          @elseif($pr->debit_note == "PR")
            Purchase Return
          @else
            Purchase Return
          @endif

          </b></td>
      </tr>
  </table>
  
  <br />

   <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="55%" valign="top" style="line-height: 17px;">
            <b>From,</b><br>
            <b>{{@$company->company_name}}</b> <br>
              @if ($bill_contact_name != '')
                                        {{ $bill_contact_name }}<br />
                                    @endif
            <div>{{ @$company->stateRelation->name }}, {{ @$company->countryname->name }}</div>
            T: {{@$company->telephone}}, M: {{@$company->mobile}}<br />
            E: {{@$company->email}}<br />
            TRN No: {{@$company->vat_number}}
          </td>
          <td valign="top" style="line-height: 18px;">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td style="padding: 0px; margin: 0px; width: 150px;">Invoice No:</td>
                    <td style="padding: 0px; margin: 0px">: {{@$pr->doc_number}}</td>
                  </tr>
                  <tr>
                    <td style="padding: 0px; margin: 0px">Date:</td>
                    <td style="padding: 0px; margin: 0px">: {{date('d/m/Y', strtotime(@$pr->pi_date))}}</td>
                  </tr>
                  <tr>
                    <td style="padding: 0px; margin: 0px">Ref No:</td>
                    <td style="padding: 0px; margin: 0px">: {{@$pr->lpo_number}}</td>
                  </tr>
                  <tr>
                    <td style="padding: 0px; margin: 0px">Ref Date:</td>
                    <td style="padding: 0px; margin: 0px">: {{date('d/m/Y', strtotime(@$pr->lpo_date))}}</td>
                  </tr>
                  <tr>
                    <td style="padding: 0px; margin: 0px; vertical-align: top; white-space: nowrap;">Payment Terms:</td>
                    <td style="padding:0; margin:0; white-space:nowrap; max-width:190px; overflow:hidden; text-overflow:ellipsis;">: {{ $pr->paymentterms->title }} {{ $pr->payment_terms2 }}</td>
                  </tr>
            </table>
          </td>
        </tr>
    </table>
    <br />
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="55%" valign="top" style="line-height: 18px;">To,<br />
                <b style="font-size: 90%;">{{@$pr->accountname->account_name}}</b><br>
                {{@$contact_name}}<br />
               
                {{@$state}}, {{@$country}}<br>
                T: {{@$tel}}, M: {{@$mobile}}<br/>
                E: {{@$email}}<br/>
                @if($cust_trn_no!="") TRN No: {{@$cust_trn_no}} @endif
          </td>
          <td valign="top" style="line-height: 18px;">Ship To,<br />
            <b style="font-size: 90%;">{{@$pr->accountname->account_name}}</b><br>
            {{@$ship_contact_name}}<br />
           
            {{@$delivery_state}}, {{@$delivery_country}}<br>
            T: {{@$ship_tel}}, M: {{@$ship_mob}}<br/>
            E: {{@$ship_email}}<br/>
            @if($cust_trn_no!="") TRN No: {{@$cust_trn_no}} @endif
          </td>
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
        @if(count($pi_item)>0)
        @foreach ($pi_item as $item)
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td class="item-row" style="width: 20px;">{{$i}} <?php $i++;?></td>
            <td class="item-row" >
              <span style="font-weight:bold;">{{ $item->partnumber->part_number }}</span><br />
              {!! nl2br(@$item->description) !!}
            <br />  <span style="font-weight:bold;"> {{ @$item->serialno }}</span>
            </td>
            <td class="item-row" style="width: 20px; text-align: center;">{{ $item->qty }}</td>
            <td class="item-row" style="width: 70px; text-align: right;">{{ @App\SysHelper::com_curr_format($item->unitprice,2,'.',',') }}</td>
            <td class="item-row" style="width: 70px; text-align: right;">{{ @App\SysHelper::com_curr_format($item->unitprice*$item->qty,2,'.',',') }}</td>
            <td class="item-row" style="width: 30px; text-align: right;">{{ @App\SysHelper::com_curr_format($item->tax,2,'.',',') }}</td>
            <td class="item-row" style="width: 80px; text-align: right;">{{ @App\SysHelper::com_curr_format($item->vatamount,2,'.',',') }}</td>
            <td class="item-row" style="width: 80px; text-align: right;">{{ @App\SysHelper::com_curr_format($item->taxableamount+$item->vatamount,2,'.',',') }}</td>
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
        @endforeach

        <?php
        
        $deal_discount += $pr->deal_discount;
        $deal_discount_vat=$pi_item->max('tax');
        $deal_discount_vat_amount= $deal_discount * $deal_discount_vat/100;
        $deal_discount_amount= $deal_discount + $deal_discount_vat_amount;
        ?>
        @endif

        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td>
              {{ $pr->currency_name->code }}  <?php echo ucwords(@App\SysHelper::convertAmountToWords(@App\SysHelper::com_curr_format($total_amount-$deal_discount_amount, 2, '.', ''),$pr->currency_name->r_code,$pr->currency_name->p_code));?>
            </td>
            <td style="width: 110px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">Sub Total {{ $pr->currency_name->code }}</td>
            <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">{{ @App\SysHelper::com_curr_format($sub_total, 2, '.', ',') }}</td>
          </tr>
        </table>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td></td>
            <td style="width: 110px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">Discount {{ $pr->currency_name->code }}</td>
            <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">{{ @App\SysHelper::com_curr_format($discount+$deal_discount, 2, '.', ',') }}</td>
          </tr>
        </table>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td></td>
            <td style="width: 110px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">Taxable Amt. {{ $pr->currency_name->code }}</td>
            <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">{{ @App\SysHelper::com_curr_format($taxable_amt-$deal_discount, 2, '.', ',') }}</td>
          </tr>
        </table>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td></td>
            <td style="width: 110px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">VAT Amount {{ $pr->currency_name->code }}</td>
            <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">{{ @App\SysHelper::com_curr_format($vat_amount-$deal_discount_vat_amount, 2, '.', ',') }}</td>
          </tr>
        </table>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td></td>
            <td style="width: 110px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">Total Amount {{ $pr->currency_name->code }}</td>
            <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">{{ @App\SysHelper::com_curr_format($total_amount-$deal_discount_amount, 2, '.', ',') }}</td>
          </tr>
        </table>


<div>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
              <b>Terms & Conditions</b>
              <ol style="padding: 10px 0px 0px 15px; margin: 0px;">

<li>The ownership of goods will remain with us until full payment is received.</li>
<li>Open box items are non-returnable, and all sales of such items are final.</li>
<li>Items without serial numbers are not covered under the warranty.</li>
<li>Damage caused by power fluctuations is not covered under the warranty.</li>
<li>To make a warranty claim, please contact the relevant vendor&#39;s service center.</li>
<li>Bank details:- Bank Name: {{@$company->bank_name}}, Account Number: {{@$company->account_number}}</li>
            </ol>          
          </td>
          </tr>
      </table>
      <br ><br ><br >
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td style="border: none; width:200px;" align="left" valign="top"><b class="bottom_b">Received By:</b><br ><br ></td>
          <td rowspan="4" style="border: none; width:200px;" align="center" valign="bottom">{{@$pr->createdby->full_name}}<br /><b class="bottom_b" style="font-size: 10px;">Prepared By</b></td>
          <td rowspan="4" style="border: none; width:200px;" align="right" valign="bottom"><b class="bottom_b" style="font-size: 10px;">For {!! str_replace('SYSCOM DISTRIBUTIONS LLC BRANCH ABU DHABI 1','SYSCOM DISTRIBUTIONS LLC<br />BRANCH ABU DHABI 1',$company->company_name) !!}</b></td>
        </tr>
        <tr>
          <td style="border: none;" align="left" valign="top"><b class="bottom_b">Name:</b><br ><br ></td>
        </tr>
        <tr>
          <td style="border: none;" align="left" valign="top"><b class="bottom_b">Phone:</b><br ><br ></td>
        </tr>
        <tr>
          <td style="border: none;" align="left" valign="top"><b class="bottom_b">Signature and stamp:</b></td>
        </tr>
      </table>
</div>
  
  </main>
</body>

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
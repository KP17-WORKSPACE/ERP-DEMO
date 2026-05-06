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
   

    <main class="m2">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td align="left" valign="top" style="padding: 0; margin: 0; vertical-align: top;"><img  src="{{asset('public/'.@$company->company_logo)}}" width="200px"/></td>
            <td align="right" valign="top" style="padding: 0; margin: 0; vertical-align: top;"><b style="font-size: 30px; font-weight: 400;">Sales Return</b></td>
        </tr>
    </table>
    <br />

    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td  width="60%"  valign="top" style="line-height: 18px;">

          
          To,<br />
            
          <b>{{@$sr->accountname->account_name}}</b><br>
         @if ($state !="") {{@$state}}, @endif {{ $country }}<br>
          T: {{@$tel}}, M: {{@$mobile}}<br>
          E: {{@$email}}
        </td>
        <td>

          <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                  <td style="padding: 0px; margin: 0px; width: 150px;">Sales Return No</td>
                  <td style="padding: 0px; margin: 0px">: {{@$sr->doc_number}}</td>
                </tr>
                <tr>
                  <td style="padding: 0px; margin: 0px">Sales Return Date</td>
                  <td style="padding: 0px; margin: 0px">: {{date('d/m/Y', strtotime(@$sr->doc_date))}}</td>
                </tr>
                <tr>
                  <td style="padding: 0px; margin: 0px">DO No</td>
                  <td style="padding: 0px; margin: 0px">: {{@$sr->dn_doc_number}}</td>
                </tr>
                <tr>
                  <td style="padding: 0px; margin: 0px">Invoice No</td>
                  <td style="padding: 0px; margin: 0px">: {{ @$sr->si_doc_number }}</td>
                </tr>
                <tr>
                  <td style="padding: 0px; margin: 0px">Payment Terms</td>
                  <td style="padding:0; margin:0; white-space:nowrap; max-width:250px; overflow:hidden; text-overflow:ellipsis;">: {{ $sr->paymentterms->title }} {{ $sr->payment_terms2 }}</td>
                </tr>
          </table>
        </td>
      </tr>
        
  </table>
  <br />

  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr class="item-head-row">
        <td style="width: 20px;">No</td>
        <td>Part No</td>
        <td style="width: 30px; text-align: center;">Qty</td>
        <td style="width: 70px; text-align: right;">Rate</td>
        <td style="width: 80px; text-align: right;">Taxable</td>
        <td style="width: 30px; text-align: right;">VAT%</td>
        <td style="width: 80px; text-align: right;">VAT Amount</td>
        <td style="width: 80px; text-align: right;">Amount</td>
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
      ?>
      @if(count($sr_item)>0)
      @foreach ($sr_item as $item)
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
      <td class="item-row" style="width: 20px;">{{$i}} <?php $i++;?></td>
      <td class="item-row">
          {{ $item->product->part_number }}<br />
        <span style="font-size: 10px;">  {!! nl2br($item->product->description) !!}</span><br />
          {{ $item->serial_no  }}</td>
          <td class="item-row" style="width: 30px; text-align: center;">{{ $item->qty }}</td>
          <td class="item-row" style="width: 70px; text-align: right;">{{ @App\SysHelper::com_curr_format($item->unitprice, 2, '.', ',') }}</td>
          <td class="item-row" style="width: 80px; text-align: right;">{{ @App\SysHelper::com_curr_format($item->unitprice*$item->qty, 2, '.', ',') }}</td>
          <td class="item-row" style="width: 30px; text-align: right;">{{ @App\SysHelper::com_curr_format($item->tax, 2, '.', ',') }}</td>
          <td class="item-row" style="width: 80px; text-align: right;">{{ @App\SysHelper::com_curr_format($item->vatamount, 2, '.', ',') }}</td>
          <td class="item-row" style="width: 80px; text-align: right;">{{ @App\SysHelper::com_curr_format($item->taxableamount+$item->vatamount, 2, '.', ',') }}</td>
          <?php
          
          $sub_total += $item->value;
          $discount += $item->discount;
          $taxable_amt += $item->taxableamount;
          $customs_charges += $item->customcharges;
          $vat_amount += $item->vatamount;
          $total_amount += $item->taxableamount + $item->vatamount;

          ?>


      </tr>
      </table>
      @endforeach
      @endif


            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:12px;">
                        <tr valign="top">
                            <td style="width:66%; padding-right:12px; vertical-align: top;">

                                 <table width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td class="font-weight-600">
            {{ $sr->currency_name->code }}  <?php echo ucwords(@App\SysHelper::convertAmountToWords($total_amount,$sr->currency_name->r_code,$sr->currency_name->p_code));?>

                                            </td>

                                        <tr>
                                            <td>
                                                <b>Terms & Conditions</b><br />

                                          
              <ul style="padding: 10px 0px 0px 15px; margin: 0px;">
                <li>Quote Order will be subject to approval of payment/credit terms by {{@$company->company_name}}.</li>
                <li>Please mention our Quotation No.in your Purchase Order.</li>
                <li>Incase of non-availability of quote products {{@$company->company_name}} reserved the rights to supply a functionally similar or better product.</li>
                <li>All payment transfer charges should be borne by the sender only.</li>
            </ul>  
                                            </td>

                                        </tr>
                                    </table>

                            </td>
                            <td style="width:34%; padding-left:12px; vertical-align: top;">
                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td style="width: 110px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">Sub Total {{ $sr->currency_name->code }}</td>
                                        <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">{{ @App\SysHelper::com_curr_format($sub_total, 2, '.', ',') }}</td>
                                    </tr>
                                </table>

                                @if(($discount) > 0)
                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td style="width: 110px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">Discount {{ $sr->currency_name->code }}</td>
                                        <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">{{ @App\SysHelper::com_curr_format($discount, 2, '.', ',') }}</td>
                                    </tr>
                                </table>
                                @endif

                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td style="width: 110px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">Taxable Amt. {{ $sr->currency_name->code }}</td>
                                        <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">{{ @App\SysHelper::com_curr_format($taxable_amt, 2, '.', ',') }}</td>
                                    </tr>
                                </table>

                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td style="width: 110px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">VAT Amount {{ $sr->currency_name->code }}</td>
                                        <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">{{ @App\SysHelper::com_curr_format($vat_amount, 2, '.', ',') }}</td>
                                    </tr>
                                </table>

                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td style="width: 110px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">Total Amount {{ $sr->currency_name->code }}</td>
                                        <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">{{ @App\SysHelper::com_curr_format($total_amount, 2, '.', ',') }}</td>
                                    </tr>
                                </table>
                            </td>
      
                                 </td>
                        </tr>
                            </table>

     
      <br /><br />
      <div style="bottom: 0px; height:200px;">
        
      <br ><br ><br >
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td style="border: none;" align="left" valign="bottom">{{@$si->createdby->full_name}}<br/><b class="bottom_b">Prepared By</b></td>
          <td style="border: none;" align="center" valign="bottom">This document is computer generated Signature is not required</td>
          <td style="border: none;" align="right" valign="bottom">{{@$company->company_name}} <br /><b class="bottom_b">Authorised Signature</b></td>
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
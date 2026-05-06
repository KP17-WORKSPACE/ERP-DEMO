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

    body{font-family: Verdana, sans-serif; font-size:12px; color:#555555; background-image: url('{!! asset("public/backEnd/img/".$company->pdf_watermark."") !!}');}
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
    <?php /*<header>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td align="left"><img  src="{{asset(@$company->company_logo)}}" width="200px"/></td>
                <td align="right"><b style="font-size: 30px; font-weight: 400;">Delivery Note</b></td>
            </tr>
        </table>
      
    </header>
    <footer>
      {{-- <img  src="{!! asset('admin_assets/dist/img/pdf-footer.jpg') !!}" width="100%"> --}}
    </footer> */ ?>
  <main class="m2">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
          <td align="left"><img  src="{{asset(@$company->company_logo)}}" width="200px"/></td>
          <td align="right"><b style="font-size: 30px; font-weight: 400;">Receipt Voucher</b></td>
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
            TRN No: {{@$company->vat_number}}
          </td>
          <td>
          </td>
        </tr>
    </table>
    <br />
      @if($receipt->mode==1)
      <table width="100%" border="0" cellspacing="0" cellpadding="0" class="m1" style="text-align: center;">
      <tr>
        <td width="25%" style="line-height: 18px; font-weight:bold;">Doc Date</td>
        <td width="25%" style="line-height: 18px; font-weight:bold;">Doc Number</td>
        <td width="25%" style="line-height: 18px; font-weight:bold;">Receipt Mode</td>
        <td width="25%" style="line-height: 18px; font-weight:bold;">Receipt Date</td>
      </tr>
      <tr>
        <td style="line-height: 18px;">{{ date('d/m/Y', strtotime(@$receipt->doc_date)) }}</td>
        <td style="line-height: 18px;">{{ $receipt->doc_number }}</td>
        <td style="line-height: 18px;">Cash</td>
        <td style="line-height: 18px;">{{ date('d/m/Y', strtotime(@$receipt->receipt_date)) }}</td>
      </tr>
    </table>
      @else
      <table width="100%" border="0" cellspacing="0" cellpadding="0" class="m1" style="text-align: center;">
      <tr>
        <td width="25%" style="line-height: 18px; font-weight:bold;">Doc Date</td>
        <td width="25%" style="line-height: 18px; font-weight:bold;">Doc Number</td>
        <td width="25%" style="line-height: 18px; font-weight:bold;">Receipt Mode</td>
        <td width="25%" style="line-height: 18px; font-weight:bold;">Receipt Through</td>
      </tr>
      <tr>
        <td style="line-height: 18px;">{{ date('d/m/Y', strtotime(@$receipt->doc_date)) }}</td>
        <td style="line-height: 18px;">{{ $receipt->doc_number }}</td>
        <td style="line-height: 18px;">Bank</td>
        <td style="line-height: 18px;">
          @if($receipt->receipt_through == 1) Bank Transfer @endif
          @if($receipt->receipt_through == 2) CDC Cheque @endif
          @if($receipt->receipt_through == 3) PDC Cheque @endif
        </td>
      </tr>
      </table><br />
      <table width="100%" border="0" cellspacing="0" cellpadding="0" class="m1" style="text-align: center;">
      <tr>
        <td style="line-height: 18px; font-weight:bold;">Receipt Date</td>
        <td style="line-height: 18px; font-weight:bold;">Cheque Date</td>
        @if($receipt->cheque_number !="")
        <td style="line-height: 18px; font-weight:bold;">Cheque Number</td>@endif
        @if($receipt->cheque_bank_name !="")
        <td style="line-height: 18px; font-weight:bold;">Bank Name</td>@endif
      </tr>
      <tr>
        <td style="line-height: 18px;">{{ date('d/m/Y', strtotime(@$receipt->receipt_date)) }}</td>
        <td style="line-height: 18px;">{{ date('d/m/Y', strtotime(@$receipt->cheque_date)) }}</td>
        @if($receipt->cheque_number !="")
        <td style="line-height: 18px;">{{ $receipt->cheque_number }}</td>@endif
        @if($receipt->cheque_bank_name !="")
        <td style="line-height: 18px;">{{ $receipt->cheque_bank_name }}</td>@endif
      </tr>
      </table>
      @endif
    <br />
    
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr style="background: #2c2b6d; color: #ffffff;">
          <td style="width: 20px;">S.No</td>
          <td style="width: 530px;">Particulars</td>
          <td style="width: 50px; text-align: center;">Amount</td>
        </tr>
    </table>
        <?php
            $i=1;
            $sum=0;
        ?>
        @if(count($receipt_item)>0)
        @foreach ($receipt_item as $item)
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
        <td style="width: 20px; border-bottom: solid 1px #2c2b6d;">{{$i}}. <?php $i++;?></td>
        <td style="width: 530px; border-bottom: solid 1px #2c2b6d; font-size: 10px;">{{ $item->accounts->account_name }} 
          @if($item->remarks !="")
            <br />{{ $item->remarks }}
          @endif

        
        </td>
          <td style="width: 50px; border-bottom: solid 1px #2c2b6d; text-align: center;">{{ @App\SysHelper::com_curr_format(abs($item->debit_amount - $item->credit_amount),2,'.',',') }}</td>
            <?php            
            $sum += abs($item->debit_amount - $item->credit_amount);
            ?>
        </tr>
        </table>
        @endforeach
        @endif
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td style="border-bottom: solid 1px #2c2b6d; text-align: left; width: 550px; font-weight: bold;">
            <?php echo ucwords(@App\SysHelper::convertAmountToWords($sum,$receipt->currency_name->r_code,$receipt->currency_name->p_code));?></td>
          <td style="border-bottom: solid 1px #2c2b6d; text-align: center; font-weight: bold; width: 50px;">{{ @App\SysHelper::com_curr_format($sum,2,'.',',') }}</td>
        </tr>
      </table>
      <br ><br ><br ><br />
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td style="border: none; font-weight: bold;" align="left" valign="bottom">
        
            </td>
          <td style="border: none; font-weight: bold;" align="center" valign="bottom"></td>
          <td style="border: none; font-weight: bold;" align="right" valign="bottom">Authorised Signature<br /><br /><br />{{@$company->company_name}}
          <br /><br />Printed on {{ $print }}</td>
        </tr>
      </table>
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
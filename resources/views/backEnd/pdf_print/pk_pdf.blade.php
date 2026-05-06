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
    footer {  position: fixed; left: 0px; bottom: 0px; right: 0px; height: 100px; background-color: white;  background-image: url('{!! asset("public/backEnd/img/".$company->pdf_watermark."") !!}'); }
    footer .page:after { content: counter(page, upper-roman); }

    body{font-family: Verdana, sans-serif; font-size:12px; color:#555555; background-image: url('{!! asset("public/backEnd/img/".$company->pdf_watermark."") !!}');}
    th, td {padding: 5px 5px;}
    .tdd{border:dashed 1px #9e9e9e; border-width:0 0 1px 0;}
    b{font-size:14px;}
    main{margin:0px 0px 100px 0px;}
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
  <style>
    .pagenum:before {
         content: counter(page);
     }
 </style>
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

    <footer>
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td style="border: none; width:200px;" align="left" valign="top"><b class="bottom_b">Received By:</b><br ></td>
          <td rowspan="4" style="border: none; width:200px;" align="center" valign="bottom">{{@$pk->createdby->full_name}}<br /><b class="bottom_b" style="font-size: 10px;">Prepared By</b></td>
          <td rowspan="4" style="border: none; width:200px;" align="right" valign="bottom"><b class="bottom_b" style="font-size: 10px;">For {!! str_replace('SYSCOM DISTRIBUTIONS LLC BRANCH ABU DHABI 1','SYSCOM DISTRIBUTIONS LLC<br />BRANCH ABU DHABI 1',$company->company_name) !!}</b></td>
        </tr>
        <tr>
          <td style="border: none;" align="left" valign="top"><b class="bottom_b">Name:</b><br ></td>
        </tr>
        <tr>
          <td style="border: none;" align="left" valign="top"><b class="bottom_b">Phone:</b><br ></td>
        </tr>
        <tr>
          <td style="border: none;" align="left" valign="top"><b class="bottom_b">Signature and stamp:</b></td>
        </tr>
        <tr>
          <td colspan="3" style="border: none;" align="right" valign="top">
            Page No <span style="" class="pagenum"></span></td>
        </tr>
      </table>

    </footer>

  <main class="m2">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
          <td align="left"><img  src="{{asset(@$company->company_logo)}}" width="200px"/></td>
          <td align="right"><b style="font-size: 30px; font-weight: 400;">Packing List</b></td>
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
            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="line-height: 18px;" >
              <tr>
                  <td style="padding: 0px; margin: 0px;" align="right">Document No:</td>
                  <td style="padding: 0px; margin: 0px;" align="right">{{@$pk->doc_number}}</td>
                </tr>
                <tr>
                  <td style="padding: 0px; margin: 0px;" align="right">Document Date:</td>
                  <td style="padding: 0px; margin: 0px;" align="right">{{date('d/m/Y', strtotime(@$pk->date))}}</td>
                </tr>
                <tr>
                  <td style="padding: 0px; margin: 0px;" align="right">Ref No:</td>
                  <td style="padding: 0px; margin: 0px;" align="right">{{@$pk->refno}}</td>
                </tr>
                <tr>
                  <td style="padding: 0px; margin: 0px;" align="right">Ref Date:</td>
                  <td style="padding: 0px; margin: 0px;" align="right">{{date('d/m/Y', strtotime(@$pk->refdate))}}</td>
                </tr>
          </table>
          </td>
        </tr>
    </table>
    <br />
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="55%" valign="top" style="line-height: 18px;">Bill To,<br />
              <b style="font-size: 90%;">{{@$pk->account->account_name}}</b><br>
              {{@$contact_name}}<br />
              {{@$address}}<br>
              {{@$address2}}, {{@$city}}<br>
              {{@$state}}, {{@$country}}<br>
              T: {{@$tel}}<br/>
              E: {{@$email}}
        </td>
        <td valign="top" style="line-height: 18px;">Ship To,<br />
          <b style="font-size: 90%;">{{@$pk->account->account_name}}</b><br>
            {{@$contact_name}}<br />
            {{@$address}}<br>
            {{@$address2}}, {{@$city}}<br>
            {{@$state}}, {{@$country}}<br>
            T: {{@$tel}}<br/>
            E: {{@$email}}
          {{--  {{@$ship_contact_name}}<br />
          {{@$ship_address1}}<br>
          {{@$ship_address2}}, {{@$delivery_city}}<br>
          {{@$delivery_state}}, {{@$delivery_country}}<br>
          T: {{@$ship_tel}}<br/>
          E: {{@$ship_email}}  --}}
        </td>
      </tr>
  </table>
  <br />
    
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr style="background: #2c2b6d; color: #ffffff;">
          <td style="width: 50px; border: solid 1px #2c2b6d;">Box/Pallet No</td>
          <td style="width: 150px; border: solid 1px #2c2b6d;">Part No</td>
          <td style="width: 50px; border: solid 1px #2c2b6d; text-align: center;">Qty</td>
          <td style="width: 80px; border: solid 1px #2c2b6d; text-align: center;">COO</td>
          <td style="width: 80px; border: solid 1px #2c2b6d; text-align: center;">HS Code</td>
          <td style="width: 70px; border: solid 1px #2c2b6d; text-align: center;">Weight</td>
          <td style="width: 102px; border: solid 1px #2c2b6d; text-align: center;">Dimension (L * W * H)</td>
        </tr>
    </table>
        <?php
            $qty=0; $weight=0;
            $rowid=0;
            $bxno=0;
            $bxno2=0;
            $i=1;
        ?>
        @if(count($items)>0)
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
        @foreach ($items as $item)


        <tr>
        
          
    <?php if($bxno != $item->boxno){
      $bxno = $item->boxno;
      $a = $items->where('boxno',$item->boxno)->count(); ?>
      <td rowspan="{{ $a }}" style="width: 50px; border: solid 1px #2c2b6d;">{{$item->boxno}} </td>
    <?php } ?>
        <td style="width: 150px; border: solid 1px #2c2b6d; font-size: 10px;">{{ @$item->product->part_number }}</td>
        <td style="width: 50px; border: solid 1px #2c2b6d; text-align: center;">{{ $item->qty }}<?php $qty += $item->qty; ?></td>
        <td style="width: 80px; border: solid 1px #2c2b6d; text-align: center;">{{ @$item->coo }}</td>
        <td style="width: 80px; border: solid 1px #2c2b6d; text-align: center;">{{ @$item->hscode }}</td>
        <td style="width: 70px; border: solid 1px #2c2b6d; text-align: center;">{{ @$item->weight }}<?php $weight += $item->weight; ?></td>
    <?php if($bxno2 != $item->boxno){
      $bxno2 = $item->boxno; ?>
      <td rowspan="{{ $a }}" style="width: 102px; border: solid 1px #2c2b6d; text-align: center;">{{ @$item->dimension }}</td>
    <?php } ?>
        </tr>

        
        @endforeach
      </table>
        @endif
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
          <td style="width: 211px; border: solid 1px #2c2b6d; text-align: right; font-weight: bold;">Total</td>
          <td style="width: 50px; border: solid 1px #2c2b6d; text-align: center; font-weight: bold;">{{ $qty }}</td>
          <td style="width: 80px; border: solid 1px #2c2b6d; text-align: center;">&nbsp;</td>
          <td style="width: 80px; border: solid 1px #2c2b6d; text-align: center;">&nbsp;</td>
          <td style="width: 70px; border: solid 1px #2c2b6d; text-align: center; font-weight: bold;">{{ @App\SysHelper::com_curr_format(@$weight,3,'.','') }}</td>
          <td style="width: 102px; border: solid 1px #2c2b6d; text-align: center;">&nbsp;</td>            
          </tr>
          </table>
      {{--  <br ><br ><br ><br />
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td style="border: none; font-weight: bold;" align="left" valign="bottom">
                Received By: <br /><br />
                Name: <br /><br />
                Phone: <br /><br /><br /><br />
                Signature & Stamp
        
            </td>
          <td style="border: none; font-weight: bold;" align="center" valign="bottom">Approved By</td>
          <td style="border: none; font-weight: bold;" align="right" valign="bottom">For {{@$company->company_name}}</td>
        </tr>
      </table>  --}}
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
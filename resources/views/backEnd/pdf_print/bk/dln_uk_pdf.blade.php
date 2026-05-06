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
    .item-head-row {background: #306d2b; color: #ffffff; }
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
          <td style="border: none; width:35%; line-height: 20px;" align="left" valign="top"><b class="bottom_b">Received By:</b><br >
            <b class="bottom_b">Name:</b><br >
            <b class="bottom_b">Phone:</b><br >
            {{-- <b class="bottom_b">Signature and stamp:</b> --}}
          </td>
          <td style="border: none; width:30%; line-height: 20px;" align="center" valign="bottom">{{@$si->createdby->full_name}}<br /><b class="bottom_b" style="font-size: 10px;">Prepared By</b>
          </td>
          <td style="border: none; width:35%; line-height: 20px;" align="right" valign="top">
            <b class="bottom_b" style="font-size: 10px;">For {!! str_replace('SYSCOM DISTRIBUTIONS LLC BRANCH ABU DHABI 1','SYSCOM DISTRIBUTIONS LLC<br />BRANCH ABU DHABI 1',$company->company_name) !!}</b><br /><br />
            Page No <span style="" class="pagenum"></span> of {{@$dn->doc_number}}</td>
          </td>
        </tr>
        <img  src="{!! asset('public/uploads/crm_pdf_img/new-'.$company->pdf_footer.'') !!}"  width="100%"/>
      </table>

    </footer>

  <main class="m2">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
          <td align="left"><img  src="{{asset(@$company->company_logo)}}" width="200px"/></td>
          <td align="right"><b style="font-size: 20px; font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif; font-weight: 400;">Delivery Note</b></td>
      </tr>
  </table>
  <br />

    {{--  <table width="100%" border="0" cellspacing="0" cellpadding="0">
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
                  <td style="padding: 0px; margin: 0px;" align="right">Delivery Note No:</td>
                  <td style="padding: 0px; margin: 0px;" align="right">{{@$dn->doc_number}}</td>
                </tr>
                <tr>
                  <td style="padding: 0px; margin: 0px;" align="right">Delivery Note Date:</td>
                  <td style="padding: 0px; margin: 0px;" align="right">{{date('d/m/Y', strtotime(@$dn->doc_date))}}</td>
                </tr>
                <tr>
                  <td style="padding: 0px; margin: 0px;" align="right">Sales Order No:</td>
                  <td style="padding: 0px; margin: 0px;" align="right">{{@$dn->invoice_no}}</td>
                </tr>
                <tr>
                  <td style="padding: 0px; margin: 0px;" align="right">Sales Order Date:</td>
                  <td style="padding: 0px; margin: 0px;" align="right">{{date('d/m/Y', strtotime(@$dn->invoice_date))}}</td>
                </tr>
                <tr>
                  <td style="padding: 0px; margin: 0px;" align="right">Payment Terms:</td>
                  <td style="padding: 0px; margin: 0px;" align="right">{{ $dn->payment_terms->title }} {{ $dn->payment_terms2 }}</td>
                </tr>
          </table>
          </td>
        </tr>
    </table>  --}}
    <br />
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="35%" valign="top" style="line-height: 18px; padding-left: 20px;">Bill To,<br />
              <b style="font-size: 90%;">{{@$dn->accountname->account_name}}</b><br>
              {{@$contact_name}}<br />
              {{--  {{@$address}}<br>
              {{@$address2}}, {{@$city}}<br>  --}}
              {{@$state}}, {{@$country}}<br>
              T: {{@$tel}}, M: {{@$mob}}<br/>
              {{--  E: {{@$email}}  --}}
        </td>
        <td width="35%" valign="top" style="line-height: 18px;">Ship To,<br />
          <b style="font-size: 90%;">{{@$dn->accountname->account_name}}</b><br>
          {{@$ship_contact_name}}<br />
          {{--  {{@$ship_address1}}<br>
          {{@$ship_address2}}, {{@$delivery_city}}<br>  --}}
          {{@$delivery_state}}, {{@$delivery_country}}<br>
          T: {{@$ship_tel}}, M: {{@$ship_mob}}<br/>
          {{--  E: {{@$ship_email}}  --}}
        </td>
        <td width="30%" valign="top" style="line-height: 18px;">

          Delivery Note No: {{@$dn->doc_number}}
          Delivery Note Date: {{date('d/m/Y', strtotime(@$dn->doc_date))}}
          Sales Order No: {{@$dn->invoice_no}}
          Sales Order Date: {{date('d/m/Y', strtotime(@$dn->invoice_date))}}
          Payment Terms: {{ $dn->payment_terms->title }} {{ $dn->payment_terms2 }}

        </td>
      </tr>
  </table>
  <br />
    {{-- <table width="100%" border="0" cellspacing="0" cellpadding="0" class="" style="text-align: left; padding-left: 20px;">
      <tr>
        <td width="20%" style="line-height: 12px; font-weight:bold;">Delivery Note No</td>
        <td width="20%" style="line-height: 12px; font-weight:bold;">Delivery Note Date</td>
        <td width="20%" style="line-height: 12px; font-weight:bold;">Sales Order No</td>
        <td width="20%" style="line-height: 12px; font-weight:bold;">Sales Order Date</td>
        <td width="20%" style="line-height: 12px; font-weight:bold;">Payment Terms</td>
      </tr>
      <tr>
        <td style="line-height: 12px;">{{@$dn->doc_number}}</td>
        <td style="line-height: 12px;">{{date('d/m/Y', strtotime(@$dn->doc_date))}}</td>
        <td style="line-height: 12px;">{{@$dn->invoice_no}}</td>
        <td style="line-height: 12px;">{{date('d/m/Y', strtotime(@$dn->invoice_date))}}</td>
        <td style="line-height: 12px;">{{ $dn->payment_terms->title }} {{ $dn->payment_terms2 }}</td>
      </tr>
    </table>
    <br /> --}}
    
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr style="background: #2c2b6d; color: #ffffff;">
          <td style="width: 20px;">S.No</td>
          <td style="width: 530px;">Description</td>
          <td style="width: 50px; text-align: center;">Qty</td>
        </tr>
    </table>
        <?php
            $i=1;
            $qty=0;
        ?>
        @if(count($dn_item)>0)
        @foreach ($dn_item as $item)
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
        <td style="width: 20px; border-bottom: solid 1px #2c2b6d;">{{$i}} <?php $i++;?></td>
        <td style="width: 530px; border-bottom: solid 1px #2c2b6d; font-size: 10px;">
            <b style="font-size: 11px;">{{ $item->product->part_number }}</b><br />
            <span style="font-size:10px;">{!! nl2br($item->description) !!}</span><br />
            <span style="width:auto; font-size: 10px; background: #d1d1d1; padding: 2px; margin-top:5px; font-weight: bold; font-style: italic;">{{ str_replace(',',', ',$item->serial_no)  }}</span></td>
          <td style="width: 50px; border-bottom: solid 1px #2c2b6d; text-align: center;">{{ $item->qty }}</td>
            <?php            
            $qty += $item->qty;
            ?>
        </tr>
        </table>
        @endforeach
        @endif
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td style="border-bottom: solid 1px #2c2b6d; font-weight: bold;" colspan="2"> Note: Goods Received in Good Condition <span class="text-right">Total</span></td>
          <td style="border-bottom: solid 1px #2c2b6d; text-align: center; font-weight: bold;">{{ $qty }}</td>
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
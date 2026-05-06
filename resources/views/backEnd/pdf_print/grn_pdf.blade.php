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
        @php $logoPath = public_path(trim($company->company_logo ?? '')) @endphp
          <td align="left" valign="top" style="padding: 0; margin: 0; vertical-align: top;"><img src="@if(!empty($company->company_logo) && file_exists($logoPath))file://{{ $logoPath }}@else{{ asset('public/'.@$company->company_logo) }}@endif" width="200px"/></td>
             <td align="right" valign="top" style="padding: 0; margin: 0; vertical-align: top;"><b style="font-size: 30px; font-weight: 400;">Goods Receipt Note</b></td>
      </tr>
  </table>
  <br />

    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="55%" valign="top" style="line-height: 18px; vertical-align: top;">
                                    <b>From,</b><br>
               <b >{{@$grn->accountname->account_name}}</b><br>
                   Attn. {{@$contact_name}}<br />
                            {{@$state}}, {{@$country}}<br>
                T: {{@$tel}}, 
              M: {{@$mob}}<br/>
              E: {{@$email}} <br>
            @if ($m_trnno != '')
                                        TRN No: {{ @$m_trnno }}<br>
                                    @endif
          </td>
          <td width="45%" valign="top" style="width: 40%; vertical-align: top;">
            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="line-height: 18px;" >
              <tr>
                  <td style="padding: 0px; margin: 0px; width: 150px;">Delivery Note No</td>
                  <td style="padding: 0px; margin: 0px;">:{{@$grn->doc_number}}</td>
                </tr>
                <tr>
                  <td style="padding: 0px; margin: 0px;">Delivery Note Date</td>
                  <td style="padding: 0px; margin: 0px;">:{{date('d/m/Y', strtotime(@$grn->grn_date))}}</td>
                </tr>
                <tr>
                  <td style="padding: 0px; margin: 0px;">LPO Number</td>
                  <td style="padding: 0px; margin: 0px;">:{{@$grn->lpo_number}}</td>
                </tr>
                <tr>
                  <td style="padding: 0px; margin: 0px;">LPO Date</td>
                  <td style="padding: 0px; margin: 0px;">:{{date('d/m/Y', strtotime(@$grn->lpo_date))}}</td>
                </tr>
                <tr>
                  <td style="padding: 0px; margin: 0px;">Payment Terms</td>
                  <td style="padding:0; margin:0; white-space:nowrap; max-width:190px; overflow:hidden; text-overflow:ellipsis;">:{{ $grn->paymentterms->title }} {{ $grn->payment_terms2 }}</td>
                </tr>
          </table>
          </td>
        </tr>
    </table>
    <br />
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="55%" valign="top" style="line-height: 18px; vertical-align: top;">Bill To,<br />
                             <b style="font-size:90%">{{@$company->company_name}}</b>
              <br />
                @if ($bill_contact_name != '')
                                        {{ $bill_contact_name }}<br />
                                    @endif
                        <div>{{ optional($company->stateRelation)->name }}, {{ optional($company->countryname)->name }}</div>
              
            T: {{@$company->telephone}}, M: {{@$company->mobile}}<br />
            E: {{@$company->email}}<br />
            TRN No: {{@$company->vat_number}}
        </td>
        <td width="45%" valign="top" style="line-height: 18px; width: 40%; vertical-align: top;">Ship To,<br />
                    <b style="font-size: 90%;">{{@$grn->shippingSupplierName->account_name}}</b><br>

            {{@$grn->shipping_name}}<br />
            @if($delivery_state){{ $delivery_state }},@endif
            {{ $delivery_country }}<br>
         
               T: {{@$grn->shipping_contact_no}}, M: {{ @$ship_mob }}<br />
            E: {{@$grn->shipping_email}}<br/>
            TRN: {{ $ship_trnno }}
        </td>
      </tr>
  </table>
  <br />
    
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
        @if(count($grn_item)>0)
        @foreach ($grn_item as $item)
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
        <td style="width: 20px; border-bottom: solid 1px #2c2b6d;">{{$i}} <?php $i++;?></td>
        <td style="width: 530px; border-bottom: solid 1px #2c2b6d; font-size: 10px;">
            <b style="font-size: 11px;">{{ $item->part_number }}</b><br />
            <?php
                $srl = $grn_item_srl->where('item_id',$item->id)->pluck('srl_no');
            ?>
            {!! nl2br($item->description) !!}<br />

         <b style="font-size: 9.5px; vertical-align: top;"> @if(count($srl)>0)
            @foreach ($srl as $sr)
            {{ $sr }} @if (!$loop->last),@endif
            @endforeach
            @endif
       <b>

            <span style="width:auto; font-size: 12px; background: #d1d1d1; padding: 2px; margin-top:5px; font-weight: bold; font-style: italic;">{{ str_replace(',',', ',$item->serial_no)  }}</span></td>
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
      <br ><br ><br ><br />
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
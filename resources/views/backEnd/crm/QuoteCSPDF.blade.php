<html>
<head>
  <style>
    @page { margin: 100px 0px 50px 0px; }
    header { position: fixed; left: 0px; top: -100px; right: 0px; height: 100px; background-color: white; text-align: center; }
    footer { position: fixed; left: 0px; bottom: -50px; right: 0px; height: 45px; background-color: white; }
    footer .page:after { content: counter(page, upper-roman); }

    body{font-family: Verdana, sans-serif; font-size:11px; color:#555555; background-image: url('{!! asset('public/backEnd/img/syscom-watermark-sm.png') !!}');}
    th, td {padding: 5px 0px;}
    .tdd{border:solid 0px #f5f5f5; border-width:0 0 0px 0;}
    span{font-size:15px; font-weight: bold; color: #4b4b4b;}
    main{margin:0px 30px;}
    .main2{margin:0px 40px;}

    .dttable {}
    .dttable th, .dttable td {padding: 3px 5px; text-align:left; border:solid 1px #e7e6e6; border-width:1px; font-size: 11px;}
    .dttable th{background: #d9d9d9;}
    .algc{text-align: left !important; font-weight: bold; background: #f2f2f2; color: #ffffff;}
    .subhd{font-weight: bold; background: #f2f2f2; color: #808080;}
    .cathd{font-weight: bold; background: #f2f2f2; color: #808080; text-align: center;}
    ol li {color: #808080;}
    hr {border: solid 0px #f5f5f5; background: #f5f5f5; height: 1px;}
    .page-break { page-break-after: always; }
    body{background-image: url('{!! asset('public/uploads/crm_pdf_img/'.$pdfwatermark.'') !!}');}
</style>
</head>

<body>
    <header><img  src="{!! asset('public/uploads/crm_pdf_img/'.$pdfheader.'') !!}" width="100%"></header>
    <footer><img  src="{!! asset('public/uploads/crm_pdf_img/'.$pdffooter.'') !!}" width="100%"></footer>

    <div style="margin-top:-105px; position: relative;">    
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td colspan="2">
                    <img src="{!! asset('public/uploads/crm_pdf_img/'.$pdffirstpage.'') !!}"/>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <br /><br /><br /><br /><br /><br />
                    <br /><br /><br /><br /><br /><br />
                </td>
            </tr>
            <tr>
                <td colspan="2"><div class="main2">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td width="69%"><b style="font-size: 14px;">{{ $quotation->customername->name }}</b></td><td width="13%">Quote No</td><td class="tdd" width="18%">: <b>{{$quotation->id }}</b></td>
                        </tr>
                        <tr>
                            <td><div style="width: 300px; line-height: 15px;">{{ $quotation->customername->address }}</div></td><td>Quote Date</td><td class="tdd">: {{ date('d/m/Y', strtotime($quotation->date)) }}</td>
                        </tr>
                        <tr>
                            <td><b>Tel :</b> {{ $quotation->customername->contact_number }}{{ $quotation->customername->mobile }}</td><td>Delivery Date</td><td class="tdd"> : {{ date('d/m/Y', strtotime($deliverydate)) }} </td>
                        </tr>
                        <tr>
                            <td><b>Email :</b> {{ $quotation->customername->email }}</td><td>Sales Person</td><td class="tdd">: {{ $quotation->ownername->full_name }}</td>
                        </tr>
                        <tr>
                            <td></td><td>Payment Terms</td><td class="tdd">: {{ $paymentterms }}</td>
                        </tr>
                      </table>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div class="page-break"></div>
   
  <main>
  <table width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td colspan="3" align="center"><u><b style="font-size: 15px;">Quotation</b></u></td>
    </tr>
  </table>
  {{--  
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td width="15%">Payment Terms</td><td width="50%" class="tdd">: paymentterms </td>
            <td width="15%">Sales Person</td><td width="20%" class="tdd">: {{ $quotation->ownername->first_name }} </td>
        </tr>
        <tr>
            <td>Delivery Date</td><td class="tdd">: 322</td>
            <td></td><td></td>
        </tr>
        <tr>
            <td colspan="4">&nbsp;</td>
        </tr>
    </table>  --}}
    <br /><br />

<?php
$td1=5; $td2=5; $td3=60; $rsp=2;
if($wp==1){
    $td1=5; $td2=11; $td3=54; $rsp=3;
}
?>
@if(count($quotationitems) >0)
            <table width="100%" cellpadding="0" cellspacing="0" border="1" class="dttable">
                <tr>
                    <td width="30%" class="subhd">Description</td>
                    <td width="10%" class="subhd">Employees/Work Stations</td>
                    <td width="10%" class="subhd">Price per Employee per Month</td>
                    <td width="10%" class="subhd">Critical Assets included (Special Deal)</td>
                    <td width="10%" class="subhd">Additional Critical Assets</td>
                    <td width="10%" class="subhd">Price per Critical Asset per Month</td>
                    <td width="10%" class="subhd">Total Price per Month</td>
                    <td width="10%" class="subhd" style="text-align: center;">Total Annual Cost</td>
                </tr>
                <?php
                $subtotal=0;
                $total=0;
                $vat=0;
                $discount=0;
                $c1=0; $c2=0; $c3=0; $c4=0; $c5=0; $c6=0;
                $Hardware=0; $HardwareT=0;
                $License=0; $LicenseT=0;
                ?>
                


                <?php foreach($quotationitems as $val){?>

                    <tr>
                        <td>{{ $val->description }}</td>
                        <td>{{ $val->work_stations }}</td>
                        <td>{{ $val->price_per_month }}</td>
                        <td>{{ $val->critical_assets }}</td>
                        <td>{{ $val->additional_critical_assets }}</td>
                        <td>{{ $val->price_per_critical_asset }}</td>
                        <td>{{ $val->total_price_per_month }}</td>

                        <td style="text-align: right;">
                            <?php $subtotal += $val->total_price_per_month * 12; ?>
                            <?php $total += $val->total_price_per_month;  ?>
                            {{ @App\SysHelper::com_curr_format($val->total_price_per_month*12, 2, '.', '') }}
                        </td>
                    </tr>
                    <?php } ?>

                    <?php $vat = ($subtotal * $net_vat/100); ?>
                <tr>
                    <td colspan="6" align="right">
                        Total Per Month cost to Client {{$currency}}
                    </td>
                    <td style="text-align: right;"><b>{{ @App\SysHelper::com_curr_format($total, 2, '.', '') }}</b></td>
                    <td style="text-align: right;"><b>{{ @App\SysHelper::com_curr_format($subtotal, 2, '.', '') }}</b></td>
                </tr>
                <tr>
                    <td colspan="6" rowspan="2" style="border: solid 1px #ffffff; border-width:1px 0px 1px 1px;">
                        
                    </td>
                    <td style="text-align: right;"><b>VAT {{$currency}}</b></td>
                    <td style="text-align: right;"><b>{{ @App\SysHelper::com_curr_format($vat, 2, '.', '') }}</b></td>
                </tr>
                <tr>
                    <td style="text-align: right; background: #d9d9d9; color: #4b4b4b;"><b>Net Amount {{$currency}}</b></td>
                    <td style="text-align: right; background: #d9d9d9; color: #4b4b4b;"><b>{{ @App\SysHelper::com_curr_format(($subtotal+$vat), 2, '.', '') }}</b></td>
                </tr>
            </table>
           
  <table width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td width="65%">
            <b>Terms & Conditions</b><br/>
                <ol style="padding-left: 15px; font-size: 11px">
                <li>Quote/Order will be subject to approval of payment/credit terms by {{ $quotationitems[0]->company->company_name }}.</li>
                <li>Please mention our Quotation No.in your Purchase Order</li>
                <li>In case of non-availability of quote products {{ $quotationitems[0]->company->company_name }} reserved the rights to supply a functionally similar or better product.</li>
                </ol>
        </td>
        <td width="35%" style="text-align: right; vertical-align: bottom;">
            Authorized Signature<br /><br /><br />            
                <b>{{ $quotationitems[0]->company->company_name }}</b>
        </td>
    </tr>
  </table>

            <br />
            @endif


  </main>
</body>
</html>
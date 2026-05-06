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
    
    .dttable2 {}
    .dttable2 th, .dttable2 td {padding: 3px 5px; text-align:left; border:solid 1px #e7e6e6; border-width:0px 1px 1px 1px; font-size: 11px;}
    .dttable2 th{background: #d9d9d9;}

    .algc{text-align: left !important; font-weight: bold; background: #f2f2f2; color: #ffffff;}
    .subhd{font-weight: bold; background: #f2f2f2; color: #808080;}
    .cathd{font-weight: bold; background: #f2f2f2; color: #808080; text-align: center;}
    ol li {color: #808080;}
    hr {border: solid 0px #f5f5f5; background: #f5f5f5; height: 1px;}
    .page-break { page-break-after: always; }
    .page-break-new { page-break-before:always;}
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
                    <br /><br />
                    <br /><br />
                </td>
            </tr>
            
            <tr>
                <td colspan="2"><div class="main2">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td width="60%" style="line-height: 22px; vertical-align: top;">
                                <b style="font-size: 14px;">To: <br />{{ $quotation->customername->name }}</b><br />
                                <b style="font-size: 12px;">Attn: {{ $quotation->cust_name }}</b><br />
                                @if($quotation->customername->address!="")<div style="width: 350px; word-wrap: break-word;">{{ $quotation->customername->address }}</div>@endif
                                @if($quotation->cust_no!="")<b>Tel :</b> {{ $quotation->cust_no }}<br />@endif
                                @if($quotation->cust_email!="")<b>Email :</b> {{ $quotation->cust_email }}@endif
                                <br /><br />
                                <b style="font-size: 12px;">By</b>
                                <br />
                                <b>{{ $quotation->ownername->full_name }}</b>
                                <br />
                                <b>{{ $quotationitems[0]->company->company_name }}</b>

                            </td>
                            <td width="40%" style="line-height: 22px; vertical-align: top;"><br />
                                <b style="font-size: 12px;">Quote No : {{$quotation->id }}</b><br />
                                Quote Date : {{ date('d/m/Y', strtotime($quotationitems[0]->created_at)) }}<br />
                                Quote Validity : {{ $quotation->quote_validity }}<br />
                                Payment Terms : {{ $paymentterms }}<br />
                                @if($deliverytime!="")Delivery Time : {{ $deliverytime }}@endif

                            </td>
                        </tr>
                </div></td>
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
$td1=5; $td2=5; $td3=62; $rsp=3;
if($wp==1){
    $td1=5; $td2=15; $td3=47; $rsp=4;
}
?>
@if(count($quotationitems) >0)
            <table width="100%" cellpadding="0" cellspacing="0" border="1" class="dttable">
                <tr>
                    <td width="5%" class="subhd" style="text-align: center;">No</td>
                    @if($wp==1)<td width="{{ $td2 }}%" class="subhd" style="text-align: center;">Part Number</td>@endif
                    <td width="{{ $td3 }}%" class="subhd">Description</td>
                    <td width="{{ $td1 }}%" class="subhd" style="text-align: center;">Qty</td>
                    <td width="14%" class="subhd" style="text-align: center;">Unit Price</td>
                    <td width="14%" class="subhd" style="text-align: center;">Total</td>
                </tr>
            </table>
                <?php
                $subtotal=0;
                $vat=0;
                $discount=0;
                $c1=0; $c2=0; $c3=0; $c4=0; $c5=0; $c6=0;
                $Hardware=0; $HardwareT=0;
                $License=0; $LicenseT=0;
                $srl=1;
                ?>
                

                <?php $n = "a";
                 $HS = 0; 

                 ?>
                
                
                <script>
                    let myVal = 0;
                </script>
                <script>

                    function findHeights(ids){
                    var table = document.getElementById(ids);
                        var rownum = myVal;
                        myVal += Number(table.offsetHeight) + Number(rownum);
                        //$_SESSION['rowheight'] = rh;

                    }
                </script>
                
            



                <?php foreach($quotationitems as $val){?>

                    <table width="100%" cellpadding="0" cellspacing="0" class="dttable2" id="{{ $srl }}" style="page-break-inside: auto;">
                    <tr>
                        <td width="5%" style="text-align: center;">{{ $srl++ }}</td>
                        @if($wp==1)<td width="{{ $td2 }}%">{{ $val->productname->part_number }}</td>@endif
                        <td width="{{ $td3 }}%">{!! nl2br($val->description) !!}
                            
                        <script>
                            findHeights(<?php echo $srl-1; ?>);
                        </script>
                        <script>document.write(myVal)</script>
                        </td>
                        <td width="{{ $td1 }}%" style="text-align: center;">{{ $val->qty }}</td>
                        <td width="14%" style="text-align: right;">{{ App\SysHelper::currancy_format($val->price, $val->currency_id) }}</td>
                        <td width="14%" style="text-align: right;">
                            <?php $subtotal += $val->price * $val->qty; ?>
                            <?php $discount += $val->discount * $val->qty;  ?>
                            {{ App\SysHelper::currancy_format($val->qty * $val->price, $val->currency_id) }}
                        </td>

                    </tr>
                    </table>

                    <div id="div{{ $srl }}" class="page-break-new" style="display:none;"></div>
                <script>
                    if(myVal==300){
                        document.getElementById("div5").style.display = "block";
                    }
                </script>


                    @if ($n=="b")
                    <?php $n="a"; ?>
                        <div style="page-break-before:always;">11</div>
                    @endif
                    <?php $n="a"; ?>

                    <?php 
                        $currency_id = $val->currency_id;
                     }
                    $discount += $quotation->deal_discount;
                    ?>
                    

                @if($wt!=1)
            <table width="100%" cellpadding="0" cellspacing="0"  class="dttable2">
                <tr>
                    <td colspan="{{ $rsp }}" style="border: solid 1px #e7e6e6; border-width:1px 0px 0px 0px;">

                        <?php $vat = (($subtotal * $net_vat/100) - ($discount * $net_vat/100)); ?>
                    
                    </td>
                    <td width="14%" style="text-align: right;"><b>Total {{$currency}}</b></td>
                    <td width="14%" style="text-align: right;"><b>{{ App\SysHelper::currancy_format($subtotal, $currency_id) }}</b></td>
                </tr>
                <tr>
                    <td colspan="{{ $rsp }}" style="border: solid 1px #ffffff; border-width:0px;"></td>
                    <td width="14%" style="text-align: right;"><b>Discount {{$currency}}</b></td>
                    <td width="14%" style="text-align: right;"><b>{{ App\SysHelper::currancy_format($discount, $currency_id) }}</b></td>
                </tr>
                
                <tr>
                    <td colspan="{{ $rsp }}" style="border: solid 1px #ffffff; border-width:0px;"></td>
                    <td width="14%" style="text-align: right;"><b>Sub Total {{$currency}}</b></td>
                    <td width="14%" style="text-align: right;"><b>{{ App\SysHelper::currancy_format($subtotal - $discount, $currency_id) }}</b></td>
                </tr>

                <tr>
                    <td colspan="{{ $rsp }}" style="border: solid 1px #ffffff; border-width:0px;"></td>
                    @if($currency=="INR")
                    <td width="14%" style="text-align: right;"><b>GST {{$currency}}</b></td>
                    @elseif($currency=="USD")
                    <td width="14%" style="text-align: right;"><b>TAX {{$currency}}</b></td>
                    @else
                    <td width="14%" style="text-align: right;"><b>VAT {{$currency}}</b></td>
                    @endif
                    <td width="14%" style="text-align: right;"><b>{{ App\SysHelper::currancy_format($vat, $currency_id) }}</b></td>
                </tr>
                <tr>
                    <td colspan="{{ $rsp }}" style="border: solid 1px #ffffff; border-width:0px;"></td>
                    <td width="14%" style="text-align: right; background: #d9d9d9; color: #4b4b4b;"><b>Net Amount {{$currency}}</b></td>
                    <td width="14%" style="text-align: right; background: #d9d9d9; color: #4b4b4b;"><b>{{ App\SysHelper::currancy_format((($subtotal-$discount)+$vat), $currency_id) }}</b></td>
                </tr>
            </table>
            @endif
            @if($quotation->id==4599) <div class="page-break"></div> @endif
            <br /><br /><br />
  <table width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td width="65%">
            <b>Terms & Conditions</b><br/>

            @if($quotation->terms_and_condition == "")
                <ol style="padding-left: 15px; font-size: 11px">
                @if($quotation->id==1019)
                <li>Payment Terms: Payment to Syscom Distribution LLC (50% Advance along with the PO 40 % against delivery and balance 10% after implementation and completion of the work ) - All the taxes and local levies are extra</li>
                @else
                <li>Quote/Order will be subject to approval of payment/credit terms by {{ $quotationitems[0]->company->company_name }}.</li>
                <li>Please mention our Quotation No.in your Purchase Order</li>
                <li>In case of non-availability of quote products {{ $quotationitems[0]->company->company_name }} reserved the rights to supply a functionally similar or better product.</li>
                @endif
                </ol>
            @else
            <div style="padding: 5px; font-size: 11px">
            {!! nl2br($quotation->terms_and_condition) !!}</div>
            @endif
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
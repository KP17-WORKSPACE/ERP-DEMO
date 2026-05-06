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
    body{background-image: url('{!! asset('public/uploads/crm_pdf_img/'.$pdfwatermark.'') !!}');}
    .classone{line-height: 20px; vertical-align: top; border:solid 1px #e7e6e6; padding: 10px;}
    .classtwo{line-height: 15px; vertical-align: top; border:solid 1px #e7e6e6; padding: 10px; text-align: center;}
</style>

</head>
<body>
    
    <header><img src="{!! asset('public/uploads/crm_pdf_img/'.$pdfheader.'') !!}" width="100%"></header>
    <footer><img src="{!! asset('public/uploads/crm_pdf_img/'.$pdffooter.'') !!}" width="100%">Footer</footer>
      
    <main>
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="50%" class="classone">
                                    <b style="font-size: 14px;">Bill To</b><br />
                                    {{ $quotation->accountname->account_name }}<br />
                                    {{ $si_customer->address }}<br />
                                    {{ $si_customer->mobile }}<br />
                                    {{ $si_customer->email }}


                                </td>
                                <td width="50%" class="classone">
                                    <b style="font-size: 14px;">Ship To</b><br />
                                    {{$quotation->shipping_name }}<br />
                                    {{ $quotation->shipping_address }}<br />    
                                </td>
                            </tr>
                        </table>
                        <br />
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="20%" class="classtwo">
                                    <b>Invoice No.</b>
                                </td>
                                <td width="20%" class="classtwo">
                                    <b>Invoice Date</b>
                                </td>
                                <td width="20%" class="classtwo">
                                    <b>Ref No</b>
                                </td>
                                <td width="20%" class="classtwo">
                                    <b>Ref Date</b>
                                </td>
                                <td width="20%" class="classtwo">
                                    <b>Payment Terms</b>
                                </td>
                            </tr>
                            <tr>
                                <td width="20%" class="classtwo">
                                    {{ $quotation->doc_number }}
                                </td>
                                <td width="20%" class="classtwo">
                                    {{ $quotation->doc_date }}
                                </td>
                                <td width="20%" class="classtwo">
                                    {{ $quotation->doc_number }}
                                </td>
                                <td width="20%" class="classtwo">
                                    {{ $quotation->doc_number }}
                                </td>
                                <td width="20%" class="classtwo">
                                    {{ $quotation->paymentterms->title }}
                                </td>
                            </tr>
                        </table>

        <br /><br />

    <?php $td1=5; $td2=5; $td3=62; $rsp=3;
    ?>
    @if(count($quotationitems) >0)
    <table width="100%" cellpadding="0" cellspacing="0" border="1" class="dttable">
        <tr>
            <td width="5%" class="subhd" style="text-align: center;">S.No</td>
            <td width="15%" class="subhd" style="text-align: center;">Description</td>
            <td width="5%" class="subhd" style="text-align: center;">Qty</td>
            <td width="10%" class="subhd" style="text-align: center;">Unit Price</td>
            <td width="10%" class="subhd" style="text-align: center;">Total Price</td>
            <td width="10%" class="subhd" style="text-align: center;">Discount</td>
            <td width="10%" class="subhd" style="text-align: center;">Net Price</td>
            <td width="5%" class="subhd" style="text-align: center;">VAT%</td>
            <td width="10%" class="subhd" style="text-align: center;">VAT Amount</td>
            <td width="10%" class="subhd" style="text-align: center;">Net Value</td>
        </tr>
        <?php
            $subtotal=0; $vat=0; $discount=0;
            $c1=0; $c2=0; $c3=0; $c4=0; $c5=0; $c6=0;
            $Hardware=0; $HardwareT=0;
            $License=0; $LicenseT=0;
            $srl=1;
            $currency="AED";
            ?>
        <?php foreach($quotationitems as $val){?>
        <tr>
            <td width="5%" style="text-align: center;">{{ $srl++ }}</td>
            <td width="10%">{{ $val->productname->part_number }}</td>
            <td width="5%" style="text-align: center;">{{ $val->qty }}</td>
            <td width="10%" style="text-align: right;">{{ $val->unitprice }}</td>
            <td width="10%" style="text-align: right;">{{ $val->unitprice * $val->qty }}</td>
            <td width="10%" style="text-align: right;">{{ $val->discount }}</td>
            <td width="10%" style="text-align: right;">{{ ($val->unitprice * $val->qty) - ($val->discount * $val->qty) }}</td>
            <td width="5%" style="text-align: right;">{{ $net_vat }}</td>
            <td width="10%" style="text-align: right;">{{ $val->vatamount }}</td>
            <td width="10%" style="text-align: right;">
                <?php $subtotal += $val->unitprice * $val->qty; ?>
                <?php $discount += $val->discount * $val->qty;  ?>
                {{ $val->qty * $val->unitprice }}
            </td>
        </tr>
        <?php 
        }
        ?>
    </table>
    
            <table width="100%" cellpadding="0" cellspacing="0"  class="dttable2">
                <tr>
                    <td colspan="{{ $rsp }}" style="border: solid 1px #e7e6e6; border-width:1px 0px 0px 0px;">

                        <?php $vat = (($subtotal * $net_vat/100) - ($discount * $net_vat/100)); ?>
                    
                    </td>
                    <td width="14%" style="text-align: right;"><b>Total {{$currency}}</b></td>
                    <td width="14%" style="text-align: right;"><b>{{ $subtotal }}</b></td>
                </tr>
                <tr>
                    <td colspan="{{ $rsp }}" style="border: solid 1px #ffffff; border-width:0px;"></td>
                    <td width="14%" style="text-align: right;"><b>Discount {{$currency}}</b></td>
                    <td width="14%" style="text-align: right;"><b>{{ $discount }}</b></td>
                </tr>
                
                <tr>
                    <td colspan="{{ $rsp }}" style="border: solid 1px #ffffff; border-width:0px;"></td>
                    <td width="14%" style="text-align: right;"><b>Sub Total {{$currency}}</b></td>
                    <td width="14%" style="text-align: right;"><b>{{ $subtotal - $discount }}</b></td>
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
                    <td width="14%" style="text-align: right;"><b>{{ $vat }}</b></td>
                </tr>
                <tr>
                    <td colspan="{{ $rsp }}" style="border: solid 1px #ffffff; border-width:0px;"></td>
                    <td width="14%" style="text-align: right; background: #d9d9d9; color: #4b4b4b;"><b>Net Amount {{$currency}}</b></td>
                    <td width="14%" style="text-align: right; background: #d9d9d9; color: #4b4b4b;"><b>{{ (($subtotal-$discount)+$vat) }}</b></td>
                </tr>
            </table>

            <br /><br /><br />
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="65%">
                        <b>Terms & Conditions</b><br/>
                        1. The title of goods will remain with us until full payment is settled. <br />
                        2. There is no warranty for items without seral nos. <br />
                        3. Damage caused by power fluctuation are not covered under warranty <br />
                        4. Warranty claim contact relevant vendor service center <br />
                        Received By: <br />
                        Name: <br />
                        Phone: <br />
                        Signature & Stamp:
            
                        
                    </td>
                    <td width="35%" style="text-align: right; vertical-align: bottom;">
                        Authorized Signature<br /><br /><br />            
                            <b>For Syscom Distributions LLC</b>
                    </td>
                </tr>
              </table>
    @endif

  </main>
</body>
</html>
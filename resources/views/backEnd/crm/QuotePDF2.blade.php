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
    @page { margin: 100px 0px 50px 0px; }
    header { position: fixed; left: 0px; top: -100px; right: 0px; height: 100px; background-color: white; text-align: center; }
    footer { position: fixed; left: 0px; bottom: -50px; right: 0px; height: 70px; background-color: white; }
    footer .page:after { content: counter(page, upper-roman); }

    body{font-family: Verdana, sans-serif; font-size:11px; color:#555555; background-image: url('{!! asset('public/backEnd/img/syscom-watermark-sm.png') !!}');}
    th, td {padding: 5px 0px;}
    .tdd{border:solid 0px #f5f5f5; border-width:0 0 0px 0;}
    span{font-size:15px; font-weight: bold; color: #4b4b4b;}
    main{margin:0px 30px;}
    .main2{margin:0px 40px;}

    .dttable {}
    .dttable th, .dttable td {padding: 3px 5px; text-align:left; border:solid 0px #e7e6e6; border-width:0px; font-size: 11px;}
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
    body{background-image: url('{!! asset('public/'.$pdfwatermark.'') !!}');}

    .item-head-row {background: #2c2b6d; color: #ffffff;}
    .item-head-row td {border: solid 2px #2c2b6d !important; padding:5px !important; margin:0px !important; }
    .item-row {border-bottom: solid 1px #dfdfdf !important; border-top: solid 0px #dfdfdf !important; }
</style>

</head>
<body>
    <header><img src="{!! asset('public/'.$pdfheader.'') !!}" width="100%"></header>
    <footer>
        
        <div style="padding: 0px 15px; color: #858796;"><b>Disclaimer:</b> This quotation is valid only for "{{ $quotation->customername->name }}". Any unauthorized replication or sharing of this information will be against our company policy and may result in legal formalities.</div>
        <img src="{!! asset('public/'.$pdffooter.'') !!}" width="100%"></footer>
    
    <div style="margin-top:-105px; position: relative;">    
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td colspan="2">
                    <img src="{!! asset('public/'.$pdffirstpage.'') !!}"/>
                </td>
            </tr>
            <?php $enduser = App\SysCrmEndUser::where('deal_id',$quotation->id)->first(); ?>
            <tr>
                <td colspan="2">
                    <br /><br />
                    <br /><br />
                </td>
            </tr>
            
            <tr>
                <td colspan="2">
                    <div class="main2">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="40%" style="line-height: 18px; vertical-align: top;">
                                    <b style="font-size: 14px;">To: <br />{{ $quotation->customername->name }}</b><br />
                                    <b style="font-size: 12px;">Attn: {{ $quotation->cust_name }}</b><br />
                                    <p style="padding: 0px; margin: 0px; width: 250px;">{{ @$quotation->customername->addresses->first()->statename->name }},
                                        {{ @$quotation->customername->addresses->first()->countryname->name }}</p>
                                    @if($quotation->cust_no!="")<b>Tel :</b> {{ $quotation->cust_no }}<br />@endif
                                    @if($quotation->cust_email!="")<b>Email :</b> {{ $quotation->cust_email }}@endif
                                    <br /><br /><br /><br /><br /><br />
                                    <b style="font-size: 12px;">By</b>
                                    <br />
                                    <b>{{ $quotation->ownername->full_name }}</b>
                                    <br />
                                        <b>{{ $quotationitems[0]->company->company_name }}</b>    
                                </td>
                                <td width="35%" style="line-height: 18px; vertical-align: top;">&nbsp;</td>
                                <td width="25%" style="line-height: 18px; vertical-align: top;">
                                    <b style="font-size: 14px;">&nbsp;<br />Quote No : {{ $quotation->code }} @if (@$quotationitems[0]->quote_id != 1 && @$quotationitems[0]->quote_id != null) -
                                                    {{ @$quotationitems[0]->quote_id - 1 }} @endif</b><br />
                                    Quote Date : {{ date('d/m/Y', strtotime($quotationitems[0]->created_at)) }}<br />
                                    Quote Validity : {{ $quotationitems[0]->quote_validity }}<br />
                                    @if($deliverytime!="")Delivery Time : {{ $deliverytime }}<br />@endif
                                    Payment Terms : {{ $paymentterms }}<br />
                                    
                                    
                                    @if($quotationitems[0]->nooflocation!="")No of Locations : {{ $quotationitems[0]->nooflocation }}@endif
                                    @if($quotationitems[0]->connectivity!="")<br />Connectivity Required : {{ $quotationitems[0]->connectivity }}@endif
                                    @if($quotationitems[0]->telephonetype!="")<br />ISP Telephone Type : 

                                    <?php $string = $quotationitems[0]->telephonetype;
                                    $str_arr = explode (",", $string);
                                    $string_val = $quotationitems[0]->nolines;
                                    $str_arr2 = explode (",", $string_val);
                                    for($i=0; $i< count($str_arr); $i++) { ?>
                                        {{$str_arr[$i]}} - {{ $str_arr2[$i] }} Line, 
                                    <?php } ?>
                                    
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    
    <div class="page-break"></div>
    

    <main>
        <?php /*@if ($quotationitems[0]->company_id==13)
                <br />
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="100%" style="font-size: 12px; text-align: justify; vertical-align: bottom; line-height: 20px;">
                            <h2>Syscom IT Solutions</h2>
                            SITS is an IT Consultancy and Systems Integration firm with a specialized team to provide guidance and custom solutions to achieve seamless integration between systems. We strive on our motto of technology for tomorrow to provide solutions and service to our customers.
                            <br /><br />
                            SITS is a part of Syscom which was established in 2013, provides quality brands of IT products and services to the MENA Region. Syscom product segment covers Data Centre solutions, Storage, Security, Networking, Software, Hardware, Services and Telecommunication products. With more than 7 branch locations across the world (Dubai, Abu Dhabi, Saudi Arabia,Oman,Qatar,Pakistan, India ,UK &USA). SITS ensure that their customers get their products on time. SITS offers tech solutions to global customers in a focused set of verticals that includes health sector, pharmaceuticals, banking sector, insurance, Education sector and MEP projects. We pride ourselves on providing bespoke, flexible and affordable packages without compromising on quality.
                            <br />
                            <h2>Domain Of Expertise</h2>
                            <ul style="margin:-10px 2px 2px 2px; padding:0px 0px 0px 10px;">
                                <li>Digital IT Infrastructure</li>
                                <li>Cybersecurity</li>
                                <li>Unified Communication</li>
                                <li>Managed Services</li>
                            </ul>
                            <h2>Our Partners</h2>
                            <img src="{!! asset('public/uploads/crm_pdf_img/our-partner.png') !!}"/>
                        </td>
                    </tr>
                </table>
                <div class="page-break"></div>
              @endif */ ?>

        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td colspan="3" align="center"><u><b style="font-size: 15px;">Quotation</b></u></td>
            </tr>
        </table>
        <br /><br />

    @if(count($quotationitems) >0)
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr class="item-head-row">
            <td width="5%" style="text-align: center;">No</td>
            <td width="70%" >Description</td>
            <td width="5%" style="text-align: center;">Qty</td>
            <td width="10%" style="text-align: right;">Unit Price</td>
            <td width="10%" style="text-align: right;">Total&nbsp;&nbsp;</td>
        </tr>
    </table>
        <?php
            $subtotal=0; $vat=0; $discount=0;
            $c1=0; $c2=0; $c3=0; $c4=0; $c5=0; $c6=0;
            $Hardware=0; $HardwareT=0;
            $License=0; $LicenseT=0;
            $srl=1;
            ?>
        <?php foreach($quotationitems as $val){?>
            @if ($val->status != 0)
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td class="item-row" width="5%" style="text-align: center;">{{ $srl++ }}</td>
            <td class="item-row" width="70%" style="font-size: 11px;">
                @if($wp==1)<b style="font-size: 11px;">{{ $val->productname->part_number }}</b><br />@endif
                {!! nl2br($val->description) !!}</td>
            <td class="item-row" width="5%" style="text-align: center;">{{ $val->qty }}</td>
            <td class="item-row" width="10%" style="text-align: right;">{{ App\SysHelper::currancy_format($val->price, $val->currency_id) }}</td>
            <td class="item-row" width="10%" style="text-align: right;">
                <?php $subtotal += $val->price * $val->qty; ?>
                <?php $discount += $val->discount; //$discount += $val->discount * $val->qty;  ?>
                {{ App\SysHelper::currancy_format($val->qty * $val->price, $val->currency_id) }}
            </td>
        </tr>
        </table>
        @endif

        
        <?php 
        if($wv==1){ $vat = 0; }
        else { $vat += ((( $val->price * $val->qty) * $val->vat/100) - ($val->discount * $val->vat/100)); }
        
        $currency_id = $val->currency_id;
        }
            $discount += $quotation->deal_discount;
            if($wv==1){
                $deal_discount_vat=0;
            } else { $deal_discount_vat=$quotationitems->max('vat'); }
            $deal_discount_vat_amount= $quotation->deal_discount * $deal_discount_vat/100;
        ?>
    </table>
    @if($wt!=1)
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td></td>
                    <td style="width: 110px; text-align: left; font-weight: bold; border-bottom: solid 1px #dfdfdf;"><b>Total {{$currency}}</b></td>
                    <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #dfdfdf;"><b>{{ App\SysHelper::currancy_format($subtotal, $currency_id) }}</b></td>
                </tr>
            </table>
            @if ($discount > 0)
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td></td>
                    <td style="width: 110px; text-align: left; font-weight: bold; border-bottom: solid 1px #dfdfdf;"><b>Discount {{$currency}}</b></td>
                    <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #dfdfdf;"><b>{{ App\SysHelper::currancy_format($discount, $currency_id) }}</b></td>
                </tr>
            </table>
            @endif
            <table width="100%" border="0" cellspacing="0" cellpadding="0">                
                <tr>
                    <td></td>
                    <td style="width: 110px; text-align: left; font-weight: bold; border-bottom: solid 1px #dfdfdf;"><b>Sub Total {{$currency}}</b></td>
                    <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #dfdfdf;"><b>{{ App\SysHelper::currancy_format($subtotal - $discount, $currency_id) }}</b></td>
                </tr>
            </table>
            @if($wv!=1)
            @if ($vat-$deal_discount_vat_amount != 0)
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td></td>
                    @if($currency=="INR")
                    <td style="width: 110px; text-align: left; font-weight: bold; border-bottom: solid 1px #dfdfdf;"><b>GST {{$currency}}</b></td>
                    @elseif($currency=="USD")
                    <td style="width: 110px; text-align: left; font-weight: bold; border-bottom: solid 1px #dfdfdf;"><b>TAX {{$currency}}</b></td>
                    @else
                    <td style="width: 110px; text-align: left; font-weight: bold; border-bottom: solid 1px #dfdfdf;"><b>VAT {{$currency}}</b></td>
                    @endif
                    <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #dfdfdf;"><b>{{ App\SysHelper::currancy_format($vat-$deal_discount_vat_amount, $currency_id) }}</b></td>
                </tr>
            </table>
            @endif
            @endif
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td></td>
                    <td style="width: 110px; text-align: left; font-weight: bold; border-bottom: solid 1px #dfdfdf;"><b>Net Amount {{$currency}}</b></td>
                    <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #dfdfdf;"><b>{{ App\SysHelper::currancy_format((($subtotal-$discount-$deal_discount_vat_amount)+$vat), $currency_id) }}</b></td>
                </tr>
            </table>
            @endif
            
            <?php if($enduser != "") { ?>
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="line-height: 18px;">
                            <b style="font-size: 13px;">End User Details:</b><br />
                            <b>{{ $enduser->end_user_company_name }}</b><br />
                            @if($enduser->address_line_a !="") {{ $enduser->address_line_a }}, @endif
                            @if($enduser->address_line_b !="") {{ $enduser->address_line_b }}, @endif
                            @if($enduser->city !="") {{ $enduser->city }}, @endif
                            @if($enduser->po_box !="") PB.No : {{ $enduser->po_box }} @endif <br />                            
                            @if($enduser->end_user_contact_person !="") Contact Person : {{ $enduser->end_user_contact_person }}<br /> @endif
                            @if($enduser->job_title !="") Job Title : {{ $enduser->job_title }}<br /> @endif
                            @if($enduser->mobile_no !="") Mobile No : {{ $enduser->mobile_no }},  Email : {{ $enduser->email }}<br /> @endif
                            @if($enduser->project_name !="") Project Name : {{ $enduser->project_name }}<br /> @endif
                            @if($enduser->project_description !="") Project Brief : {{ $enduser->project_description }}<br /> @endif
                            @if($enduser->expected_close_date !="") Expected to Close : {{ date('d-M-Y', strtotime($enduser->expected_close_date)) }} @endif
                        </td>
                    </tr>
                </table>
            <?php } ?>

            <br /><br /><br />
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="65%">
                        <b>Terms & Conditions</b><br/>
            
                        @if($quotation->terms_and_condition == "")
                            <ol style="padding-left: 15px; font-size: 11px">
                            <li>Quote/Order will be subject to approval of payment/credit terms by {{ $quotationitems[0]->company->company_name }}.</li>
                            <li>Please mention our Quotation No.in your Purchase Order</li>
                            <li>In case of non-availability of quote products {{ $quotationitems[0]->company->company_name }} reserved the rights to supply a functionally similar or better product.</li>
                            </ol>
                        @else
                        <div style="padding: 5px; font-size: 11px">
                        <?php
                        $string = $quotation->terms_and_condition;
                        
                        $bits = explode("\n", $string);
                        
                        $newstring = "<ol style='padding:0px; margin:0px 0px 0px 10px;'>";
                        foreach($bits as $bit)
                        {
                          $newstring .= "<li>" . substr($bit, 2) . "</li>";
                        }
                        $newstring .= "</ol>";
                        ?>
                    {!! $newstring !!}
                    </div>
                        @endif
                    </td>
                    <td width="35%" style="text-align: right; vertical-align: bottom;">
                        Authorized Signature<br /><br /><br />
                            <b>{{ $quotationitems[0]->company->company_name }}</b>
                    </td>
                </tr>
              </table>

    @endif

                            

  </main>
</body>
</html>
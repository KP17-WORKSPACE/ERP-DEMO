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
    /* Fixed footer for PDF — positioned at the very bottom of each page */
     
    
    footer .page:after { content: counter(page, upper-roman); }

    body{font-family: Verdana, sans-serif; font-size:12px; color:#555555; background-image: url('{!! asset("public/backEnd/img/".$company->pdf_watermark."") !!}'); background-repeat: no-repeat; background-position: center; background-size: contain;}
    th, td {padding: 5px 5px;}
    .tdd{border:dashed 1px #9e9e9e; border-width:0 0 1px 0;}
    b{font-size:14px;}
    /* Make room for the footer (must match footer height) */
    main{margin:0px 0px 140px 0px;}
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
    <?php /*
    <header>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td align="left"><img  src="{{asset(@$company->company_logo)}}" width="200px"/></td>
                <td align="right"><b style="font-size: 30px; font-weight: 400;">Purchase Order</b><br /><b>PO No: {{ $po->doc_number }}</b>
                </td>
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
          <td align="left" valign="top" style="padding: 0; margin: 0; vertical-align: top;"><img  src="{!! asset('public/'.$company->company_logo) !!}" width="200px"/></td>
          <td align="right" valign="top" style="padding: 0; margin: 0; vertical-align: top;"><b style="font-size: 30px; font-weight: 400;">Purchase Order</b></td>
      </tr>
  </table>
  <br />

    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="58%" valign="top" style="line-height: 17px;">
            <b>To,</b><br>
            <b>{{ @$m_company_name }}</b><br>
            Attn. {{ $m_contact_name }}<br>
            {{--  {{@$m_address1}}<br>
            {{@$m_address2}}, {{@$m_city}}<br>  --}}
            {{@$m_state}}, {{ $m_country }}<br>
            T: {{@$m_tel}}, M: {{@$m_mob}}<br>
            {{--  E: {{@$m_emali}}<br>  --}}
            @if($m_trnno != "" )TRN No: {{@$m_trnno}}<br>@endif
          </td>
          <td style="line-height: 18px; padding: 0; margin: 0; vertical-align: top;" valign="top">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td style="padding: 0px; margin: 0px; width: 150px;">Purchase Order No</td>
                  <td style="padding: 0px; margin: 0px">: {{@$po->doc_number}}</td>
                </tr>
                <tr>
                  <td style="padding: 0px; margin: 0px; width: 150px;">Purchase Order Date</td>
                  <td style="padding: 0px; margin: 0px">: {{date('d/m/Y', strtotime(@$po->po_date))}}</td>
                </tr>
                <tr>
                  <td style="padding: 0px; margin: 0px; width: 150px;">Delivery Date</td>
                  <td style="padding: 0px; margin: 0px">:
                    @if($po->delivery_date == "1970-01-01")
                    --
                    @else
                    {{date('d/m/Y', strtotime(@$po->delivery_date))}}
                    @endif
                  </td>
                </tr>
                <tr>
                  <td style="padding: 0px; margin: 0px; width: 150px; vertical-align: top; white-space: nowrap;">Payment Terms</td>
                  <td style="padding: 0px; margin: 0px;">: {{ @$des=App\SysPaymentTerms::getPaymentTermsName($po->payment_terms)}}</td>
                </tr>
                  @if($po->property_name != "" && $po->property_name != null && $po->property_value != "" && $po->property_value != null)
                <tr>
                  <td style="padding: 0px; margin: 0px;vertical-align: top; white-space: nowrap;">{{ @$po->property_name }}</td>
                  <td style="padding:0; margin:0; white-space:nowrap; max-width:190px; overflow:hidden; text-overflow:ellipsis;">: {{ @$po->property_value }}</td>
                </tr>
                @endif
            </table>
          </td>
        </tr>
    </table>
    <br />
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="58%" valign="top" style="line-height: 18px;">Bill To,<br />
            <b style="font-size: 90%;">{{@$bill_company_name}}</b><br />
            @if($bill_contact_name !="" ){{ $bill_contact_name }}<br />@endif
            {{--  {!! nl2br($bill_address1) !!} <br />  --}}
            {{--  @if($bill_address2 != ""){{ $bill_address2 }}@endif {{ $bill_city }}<br />  --}}
             @if($bill_state != ""){{ $bill_state }}, @endif @if($bill_country != ""){{ $bill_country }}<br />@endif
            T: {{@$bill_tel}}, M: {{@$bill_mob}}<br />
            {{--  E: {{@$bill_emali}}<br />  --}}
            TRN: {{ $bill_trnno }}
          </td>
          <td valign="top" style="line-height: 18px;">Ship To,<br />
            <b style="font-size: 90%;">{{@$ship_company_name}}</b><br />
            @if($ship_contact_name !="" ){{ $ship_contact_name }}<br />@endif
            {{--  {!! nl2br($ship_address1) !!} @if($ship_address2 != "")<br />@endif
            @if($ship_address2 != ""){{ $ship_address2 }}@endif {{ $ship_city }}<br />  --}}
            @if($ship_state != ""){{ $ship_state }}, @endif @if($ship_country != ""){{ $ship_country }}<br />@endif
            T: {{@$ship_tel}}, M: {{@$ship_mob}}<br />
            {{--  E: {{@$ship_emali}}<br />  --}}
            TRN: {{ $ship_trnno }}
          </td>
        </tr>
    </table>
    <br />
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr class="item-head-row">
          <td style="width: 20px;">No</td>
          <td>Description</td>
          <td style="width: 20px; text-align: center;">Qty</td>
          <td style="width: 70px; text-align: right;">Rate</td>
          <td style="width: 70px; text-align: right;">Taxable</td>
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
        @if(count($po_item)>0)
        @foreach ($po_item as $item)




        <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td class="item-row" style="width: 20px;">{{$i}} <?php $i++;?>  <span id="spn_"{{ $i }}></span></td>
            <td class="item-row" >
              <span style="font-weight:bold;">{{ $item->productname->part_number }}</span><br />
              <span style="font-size:10px;">{!! nl2br($item->description) !!}</span></td>
            <td class="item-row" style="width:20px; text-align: center;">{{$item->qty}}</td>
            <td class="item-row" style="width:70px; text-align: right;">{{@App\SysHelper::com_curr_format($item->unitprice,2,'.',',')}}</td>
            <td class="item-row" style="width:70px; text-align: right;">{{@App\SysHelper::com_curr_format($item->unitprice * $item->qty,2,'.',',')}}</td>
            <td class="item-row" style="width:30px; text-align: right;">{{@App\SysHelper::com_curr_format($item->tax,2,'.',',')}}</td>
            <td class="item-row" style="width:80px; text-align: right;">{{@App\SysHelper::com_curr_format($item->vatamount,2,'.',',')}}</td>
            <td class="item-row" style="width:80px; text-align: right;">{{@App\SysHelper::com_curr_format($item->taxableamount + $item->vatamount,2,'.',',')}}</td>
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
    
    {{-- @if($i == 11 || $i == 33 || $i == 55 || $i == 76 || $i == 98 || $i == 121 || $i == 143) --}}
    
    
            @if($po->id == 3837)
                @if($i == 10 || $i == 27)
                <div class="page-break"></div>
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr class="item-head-row">
                    <td style="width: 20px;">No</td>
                    <td>Description</td>
                    <td style="width: 20px; text-align: center;">Qty</td>
                    <td style="width: 70px; text-align: right;">Rate</td>
                    <td style="width: 70px; text-align: right;">Taxable</td>
                    <td style="width: 30px; text-align: right;">VAT%</td>
                    <td style="width: 80px; text-align: right;">VAT Amount</td>
                    <td style="width: 80px; text-align: right;">Amount</td>
                    </tr>
                </table>
                @endif
            @elseif($po->id == 3942)
                @if($i == 8 || $i == 27)
                <div class="page-break"></div>
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr class="item-head-row">
                    <td style="width: 20px;">No</td>
                    <td>Description</td>
                    <td style="width: 20px; text-align: center;">Qty</td>
                    <td style="width: 70px; text-align: right;">Rate</td>
                    <td style="width: 70px; text-align: right;">Taxable</td>
                    <td style="width: 30px; text-align: right;">VAT%</td>
                    <td style="width: 80px; text-align: right;">VAT Amount</td>
                    <td style="width: 80px; text-align: right;">Amount</td>
                    </tr>
                </table>
                @endif
            @elseif($po->id == 3713)
                @if($i == 10 || $i == 30)
                <div class="page-break"></div>
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr class="item-head-row">
                    <td style="width: 20px;">No</td>
                    <td>Description</td>
                    <td style="width: 20px; text-align: center;">Qty</td>
                    <td style="width: 70px; text-align: right;">Rate</td>
                    <td style="width: 70px; text-align: right;">Taxable</td>
                    <td style="width: 30px; text-align: right;">VAT%</td>
                    <td style="width: 80px; text-align: right;">VAT Amount</td>
                    <td style="width: 80px; text-align: right;">Amount</td>
                    </tr>
                </table>
                @endif
            @else
                @if($i == 15 || $i == 37 || $i == 59 || $i == 80 || $i == 102 || $i == 125)
                <div class="page-break"></div>
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr class="item-head-row">
                    <td style="width: 20px;">No</td>
                    <td>Description</td>
                    <td style="width: 20px; text-align: center;">Qty</td>
                    <td style="width: 70px; text-align: right;">Rate</td>
                    <td style="width: 70px; text-align: right;">Taxable</td>
                    <td style="width: 30px; text-align: right;">VAT%</td>
                    <td style="width: 80px; text-align: right;">VAT Amount</td>
                    <td style="width: 80px; text-align: right;">Amount</td>
                    </tr>
                </table>
                @endif
            @endif
        @endforeach
        @endif

        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td>{{ $po->currency_name->code }}  <?php echo ucwords(@App\SysHelper::convertAmountToWords($total_amount,$po->currency_name->r_code,$po->currency_name->p_code));?></td>
              <td style="width: 110px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">Sub Total {{ $po->currency_name->code }}</td>
              <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">{{ @App\SysHelper::com_curr_format($sub_total, 2, '.', ',') }}</td>
            </tr>
          </table>
          @if($discount != 0)
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td></td>
              <td style="width: 110px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">Discount {{ $po->currency_name->code }}</td>
              <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">{{ @App\SysHelper::com_curr_format($discount, 2, '.', ',') }}</td>
            </tr>
          </table>
          @endif
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td></td>
              <td style="width: 110px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">Taxable Amt. {{ $po->currency_name->code }}</td>
              <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">{{ @App\SysHelper::com_curr_format($taxable_amt, 2, '.', ',') }}</td>
            </tr>
          </table>
          @if($customs_charges != 0)
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td></td>
              <td style="width: 110px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">Customs Charges</td>
              <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">{{ @App\SysHelper::com_curr_format($customs_charges, 2, '.', ',') }}</td>
            </tr>
          </table>
          @endif
          @if ($vat_amount != 0)
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td></td>
              <td style="width: 110px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">VAT Amount {{ $po->currency_name->code }}</td>
              <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">{{ @App\SysHelper::com_curr_format($vat_amount, 2, '.', ',') }}</td>
            </tr>
          </table>
          @endif
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td></td>
              <td style="width: 110px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">Total Amount {{ $po->currency_name->code }}</td>
              <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">{{ @App\SysHelper::com_curr_format($total_amount, 2, '.', ',') }}</td>
            </tr>
          </table>
          
<div style="bottom: 0px; height:200px;">
    <table width="70%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td>
          <b>Terms & Conditions</b>
          <ul style="padding: 10px 0px 0px 15px; margin: 0px; font-size: 9px; text-align: justify;">
            <li>Kindly mention the LPO number on all correspondence, invoice and delivery notes.</li>
            <li>In the event of your failing to deliver or execute the said order on or before the stiputated date or such extended time as permitted by us, {{@$company->company_name}}. reserves the full right and authority to cancel such order.</li>
            <li>The supplier shall, at its own cost, replace and/or rectify the goods supplied in the event of any defects in the material.</li>
        </ul>          
      </td>
      </tr>
  </table>
</div>


  {{-- <table width="100%">
    <tr>
        <td colspan="3"><b>Company Detail</b></td>
    </tr>
    <tr>
        <td width="30%">Name Of The Company</td><td width="1%"> : </td><td width="69%" class="tdd">{{ $formInfo["nameofthecompany"] }}</td>
    </tr>
    <tr>
        <td>Type Of Company</td><td> : </td><td class="tdd">{{ $formInfo["typeofcompany"] }}</td>
    </tr>
    <tr>
        <td>Business Market</td><td> : </td><td class="tdd">{{ $formInfo["businessmarket"] }}</td>
    </tr>
    <tr>
        <td>Trade License Number</td><td> : </td><td class="tdd">{{ $formInfo["tradelicensenumber"] }}</td>
    </tr>
    <tr>
        <td>Issuing Authority</td><td> : </td><td class="tdd">{{ $formInfo["issuingauthority"] }}</td>
    </tr>
    <tr>
        <td>Issued Date</td><td> : </td><td class="tdd">{{ dateConvertDBtoForm($formInfo["issueddate"]) }}</td>
    </tr>
    <tr>
        <td>Expiry Date</td><td> : </td><td class="tdd">{{ dateConvertDBtoForm($formInfo["expirydate"]) }}</td>
    </tr>
    <tr>
        <td>Office Address</td><td> : </td><td class="tdd">{{ $formInfo["postaladdressandlocation"] }}</td>
    </tr>
    <tr>
        <td>Telephone Number</td><td> : </td><td class="tdd">{{ $formInfo["telephonenumber"] }}</td>
    </tr>
    <tr>
        <td>Email ID</td><td> : </td><td class="tdd">{{ $formInfo["emailid"] }}</td>
    </tr>
    <tr>
        <td>Company Turnover</td><td> : </td><td class="tdd">{{ $formInfo["companyturnover"] }}</td>
    </tr>
    <tr>
        <td colspan="3">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="3" style="background: #f2f2fe; padding:20px;">
            <table width="100%">
                <tr>
                    <td colspan="4"><b>Company Owner Details</b></td>
                </tr>
                
                <tr>
                    <td width="30%">Owner/Partners Name</td><td width="35%" class="tdd">{{ $val["ownerpartnersname"] }}</td>
                    <td width="15%" align="center">Mobile</td><td width="20%" class="tdd">{{ $val["ownerpartnersmobile"] }}</td>
                </tr>
                <tr>
                    <td colspan="4"></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="3" style="background: #f2f2fe; padding:20px;">
            <table width="100%">
                <tr>
                    <td colspan="4"><b>Company Sponsor Detail</b></td>
                </tr>
                <tr>
                    <td width="30%">Sponsor Name </td><td width="70%" class="tdd">{{ $formInfo["nameofthecompanysponsor"] }}</td>
                </tr>
                <tr>
                    <td colspan="4"></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="3">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="3">
            <table width="100%">
                <tr>
                    <td colspan="5"><b>Key Contact Persons</b></td>
                </tr>
                <tr>
                    <td width="15%">Purchase :</td>
                    <td width="10%">Name</td><td colspan="3" class="tdd">{{ $formInfo["purchasename"] }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td>Mobile</td><td class="tdd">{{ $formInfo["purchasemobile"] }}</td>
                    <td align="center">Email </td><td class="tdd">{{ $formInfo["purchaseemail"] }}</td>
                </tr>
                <tr>
                    <td width="15%">Accounts :</td>
                    <td width="10%">Name</td><td colspan="3" class="tdd">{{ $formInfo["accountsname"] }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td>Mobile</td><td class="tdd">{{ $formInfo["accountsmobile"] }}</td>
                    <td align="center">Email </td><td class="tdd">{{ $formInfo["accountsemail"] }}</td>
                </tr>
                <tr>
                    <td colspan="5"></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="3" style="background: #f2f2fe; padding:20px;">
            <table width="100%">
                <tr>
                    <td colspan="2"><b>Bank Details</b></td>
                </tr>
                <tr>
                    <td width="30%">Bank</td><td width="70%" class="tdd">{{ $val["bankname"] }}</td>
                </tr>
                <tr>
                    <td width="30%">Branch</td><td width="70%" class="tdd">{{ $val["branchname"] }}</td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="3">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="3">
            <table width="100%">
                <tr>
                    <td colspan="6"><b>Presently Credit Facilities With Suppliers</b></td>
                </tr>
                <tr>
                    <td width="15%">Supplier Name</td><td width="35%" class="tdd">{{ $val["suppliername"] }}</td>
                    <td width="13%">Credit Limit</td><td width="10%" class="tdd">{{ $val["suppliercreditlimit"] }}</td>
                    <td width="12%">Credit Days</td><td width="5%" class="tdd">{{ $val["suppliercreditdays"] }}</td>
                </tr>
                <tr>
                    <td colspan="6"></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="3">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="3" style="background: #f2f2fe; padding:20px;">
            <table width="100%">
                <tr>
                    <td colspan="4"><b>Credit Request</b></td>
                </tr>
                <tr>
                    <td colspan="4">Expected Quarterly Volume with</td>
                </tr>
                <tr>
                    <td width="60%">Syscom Distribution LLC: ({{ $po->currency_name->code }})</td><td width="40%" class="tdd">{{ $formInfo["amountaed"] }}</td>
                </tr>
                <tr>
                    <td width="60%">Mode Of Payment</td><td width="40%" class="tdd">{{ $formInfo["amountmode"] }}</td>
                </tr>
                <tr>
                    <td colspan="4"></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="3">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="3"><b>Credit Policies</b></td>
    </tr>
    <tr>
        <td colspan="3">1. In consideration of the extension of credit and establishment of a credit account, applicant acknowledges liability for payment of amounts due to SYSCOM for purchase of products or services.</td>
    </tr>
    <tr>
        <td colspan="3">2. If SYSCOM must take action to collect any outstanding amount, applicant agrees to pay all reasonable costs and expenses incurred in collection including attorney's fees, court fees, court costs and interest thereon at maximum legal rate.</td>
    </tr>
    <tr>
        <td colspan="3">3. In case of any change in the constitution of the applicant's organization, the applicant should inform SYSCOM immediately with appropriate documents.</td>
    </tr>
    <tr>
        <td colspan="3">4. SYSCOM reserves the right to change its policies, terms and conditions and the applicant agrees to abide by the same.</td>
    </tr>
    <tr>
        <td colspan="3">5. The applicant does not have any objection in case SYSCOM contacts their bankers or any other trade references for information.</td>
    </tr>
    <tr>
        <td colspan="3"><p></p></td>
    </tr>
    <tr>
        <td colspan="3"><b>Declaration</b></td>
    </tr>
    <tr>
        <td colspan="3">I Mr./Mrs./Ms. ............................................. (Owner/Director/Partner) Of ............................................. Hereby Declare that the particulars furnished herein are true and correct. And also agrees to abide by the terms and conditions of Syscom Distribution LLC.</td>
    </tr>
    <tr>
        <td colspan="2"><b>Signature</b></td>
        <td style="text-align: right;"><b>Company stamp</b></td>
    </tr>
  </table> --}}

    <footer>
     <img  src="{!! asset('public/'.$company->pdf_footer) !!}" width="100%">
    </footer>

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
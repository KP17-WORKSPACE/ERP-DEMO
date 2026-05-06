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
    @page { margin: 70px 70px; }
    header { position: fixed; left: 20px; top: -50px; right: 20px; height: 80px; background-color: white; text-align: center; border-bottom:solid 0px #808080; color:#555555;  }
    footer {  position: fixed; left: 0px; bottom: 0px; right: 0px; height: 100px; background-color: white;  background-image: url('{!! asset("public/".$company->pdf_watermark."") !!}'); }
    footer .page:after { content: counter(page, upper-roman); }

    body{font-family: Verdana, sans-serif;  text-align: left; font-size:12px; color:#555555;}
    /* background-image: url('{!! asset("public/".$company->pdf_watermark."") !!}'); */
    th, td {padding: 5px 5px;}
    .tdd{border:dashed 1px #9e9e9e; border-width:0 0 1px 0;}
    b{font-size:11px;}
    main{margin:0px 0px 0px 0px; text-align: left;}
    .m1 table { border: 0px solid #9e9e9e;  text-align: left;}
    .m1 td { border: 1px solid #9e9e9e;  text-align: left;}
    .tmc ol {padding: 0px; margin: 0px;}
    .bottom_b {font-size:12px; }
    .page-break { page-break-after: always; }
    .m-0{margin: 0px;}
    .p-0{padding: 0px;}
    .item-head-row {border: solid 1px #000000; border-width:1px 1px 1px 1px; text-align:left;}
    .item-row {border: solid 1px #000000; border-width:0px 1px 1px 1px;  text-align:left;}
    .first_set{line-height: 20px; padding-bottom: 10px; text-align:left; }
    .second_set{line-height: 20px; padding-left: 20px; padding-bottom: 10px; text-align:left;}
    .third_set{line-height: 20px; padding-left: 40px; padding-bottom: 10px; text-align:left;}
    .forth_set{line-height: 20px; padding-left: 60px; padding-bottom: 10px; text-align:left;}
    .footer-text{position: fixed; left: 0px; bottom: 0px; right: 0px; height: 20px; text-align: center; color: #a3a3a3;}
    .footer2 {
        position: fixed;
        bottom: 10px;
        left: 0;
        width: 100%;
        text-align: center;
        font-size: 10px;
    }
    
    .internal-note {
        text-align: center;
        font-weight: bold;
        margin-top: 100px; /* adjust as needed */
    }
</style>


</head>
<body>
  <style>
    .pagenum:before {
         content: counter(page);
     }
 </style>


  

  <main>
    <br />
    <br />
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td><br /><br /><br /><br />
          {{-- <td align="left"><img  src="{{asset(@$company->company_logo)}}" width="200px"/></td> --}}
          {{-- <td align="right"><b style="font-size: 30px; font-weight: 400;">Short Term Loan (STL)</b></td> --}}
      </tr>
  </table>
    <br />
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td valign="top" style="line-height: 20px;">To,<br />
          <div style="float: right;">Date: {{date('d/m/Y', strtotime(@$stl->submition_date))}}</div>
              <b style="font-size: 15px;">{{@$stl->bank_name->stl_dept}}</b><br />
              {{@$stl->bank_name->bank_name}}<br />
              {{@$stl->bank_name->branch}}<br />
              {{@$stl->bank_name->branch_location}}<br /><br />
        </td>
      </tr>
      <tr>
        <td><u style="font-weight: bold; font-size: 12px;">Subject: Application for Supplier Payment Under Mudaraba Facility</u><br /></td>
      </tr>
      <tr>
        <td style="line-height: 20px;">
          This letter is a request for transferring the payment against {{ $tax_invoices }} {{ $invoices_no }} to {{ @$stl->vendor_name->account_name }} for {{ @$stl->currency_name_m->code }} {{ @App\SysHelper::com_curr_format(@$stl->amount_usd,'2','.',',') }} from our Mudaraba Financing facility. We are authorizing to deduct the bank charges related to this transaction from our {{ @$stl->bank_name->bank_name }} account {{ @$stl->bank_name->acc_no }}.<br />
        </td>
      </tr>
      
      @if ($stl->payment_type == "Partial")
      <tr>
        <td style="line-height: 20px;">
          {!! nl2br($stl->partial_remarks) !!}<br /><br /><br />
        </td>
      </tr>
      @endif

      <tr>
        <td><b style="font-size: 12px;">FUNDS TRANSFER REQUEST</b><br /></td>
      </tr>
      <tr>
        <td>
          <table border="1" cellspacing="0" cellpadding="0">
            <tr style="color: #000000;">
              <td style="width: 30%;">Beneficiary Name</td>
              <td style="width: 70%;">{{ @$vendor_det->vendor_name }}</td>
            </tr>
            <tr>
              <td>Beneficiary Bank Name</td>
              <td>{{ @$vendor_det->beneficiary_name }}</td>
            </tr>
            <tr>
              <td>Account No / IBAN</td>
              <td>{{ @$vendor_det->iban }}</td>
            </tr>
            <tr>
              <td>City and Country</td>
              <td>{{ @$vendor_det->city_country }}</td>
            </tr>
            <tr>
              <td>Bank Swift Code</td>
              <td>{{ @$vendor_det->swift_code }}</td>
            </tr>
            <tr>
              <td>Payment Details / Invoice No</td>
              <td>{{ @$stl->currency_name->code }} {{ @App\SysHelper::com_curr_format(@$stl->amount_aed,'2','.',',') }} / {{ $tax_invoices }}: {{ $invoices_no }}</td>
            </tr>
            <tr>
              <td>Purpose of Payment</td>
              <td>Goods Import - GDI</td>
            </tr>
            <tr>
              <td>Charges</td>
              <td>Debit our Account no {{ @$stl->bank_name->acc_no }}</td>
            </tr>
          </table>
        </td>
      </tr>


      <tr>
        <td><br /><br /><br /><b style="font-size: 12px;">For, {{ $company->company_name }}</b><br /><br /><br /><br /><br /></td>
      </tr>
      <tr>
        <td><br /><br /><b style="font-size: 12px;">{{ $stl->owner_name }}</b><br /></td>
      </tr>
  </table>

  {{-- <div class="footer2">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td width="50px" align="left">Document:- 1</td>
            <td width="50px" align="right">P.T.O</td>
        </tr>
    </table>
</div> --}}

  <div class="page-break"></div>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td valign="top" style="line-height: 20px; text-align: center;"><b style="font-size: 12px;">SCHEDULE 7<br />REQUEST FOR CAPITAL CONTRIBUTION</b><br /><br /></td>
  </tr>
  <tr>
    <td valign="top" style="line-height: 20px;">From:		{{ $company->company_name }}, PO Box: 124402, {{ $company->city }}, UAE (as Mudarib)<br />
      To:	{{ @$stl->bank_name->bank_name }} (as Rab-ul-Maal)<br />
      Date: {{date('d/m/Y', strtotime(@$stl->submition_date))}}</td>
  </tr>
  <tr>
    <td valign="top" style="line-height: 20px;"><br />Dear Sirs,<br />
      <b style="font-size: 12px;">Re:	Request For Capital Contribution</b><br />
      1)    Unless the context does not so admit, terms defined, and the construction given to them, in the Master Mudaraba Agreement between Rab-ul-Maal and the Mudarib dated 27.09.2022 (the "Master Mudaraba Agreement") shall have the same meaning and construction when used herein.<br /><br />
      2)		This is the Request For Capital Contribution, as envisaged under Clause 6.1 of the Master Mudaraba Agreement and shall be construed an integral part of the Master Mudaraba Agreement.<br /><br />
      3)		Subject to Clause 6 of the Master Mudaraba Agreement and pursuant to the entry into the Relevant Investment Plan, we request you to make a contribution of {{ @$stl->currency_name->code }} {{ @App\SysHelper::com_curr_format(@$stl->amount_aed,'2','.',',') }} into the Relevant Mudaraba Account {{ @$stl->bank_name->acc_no }}
      </td>
  </tr>
  <tr>
    <td><br /><br /><b style="font-size: 12px;">By:<br /><br />{{ $stl->owner_name }} (as Mudarib)</b><br /></td>
  </tr>
</table>



{{-- <div class="footer2">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
          <td width="50px" align="left">Document:- 2</td>
          <td width="50px" align="right">P.T.O</td>
      </tr>
  </table>
</div> --}}

<div class="page-break"></div>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td valign="top" style="line-height: 20px; text-align: center;"><b style="font-size: 12px;">SCHEDULE 1<br />RELEVANT INVESTMENT PLAN</b><br /></td>
  </tr>
  <tr>
    <td valign="top" style="line-height: 20px;">The Parties to the Agreement agree to the following Relevant Investment Plan:</td>
  </tr>
  <tr>
    <td valign="top" style="line-height: 20px;"><br /><b style="font-size: 12px;">1.	RELEVANT ACTIVITY</b><br />
      <div class="first_set">1.1.	Under the Relevant Mudaraba, the Mudarib undertakes to invest the Relevant Mudaraba Capital solely in the following Sharia <br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;compliant activity (the "Relevant Activity"):</div>
      <div class="second_set">1.1.1	The Relevant Mudaraba Capital shall be invested for general corporate purposes of the Mudarib (including but not limited towards <br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;working capital requirements). </div>
      
      <div class="first_set">1.2.	The Relevant Mudaraba is expected to generate the Net Operating Profit of at least 15% to 18% per annum on the Relevant Mudaraba <br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Capital (the "Anticipated Relevant Mudaraba Profit"). </div>
      <div class="first_set">1.3.	The Parties agree that for the threshold for the purpose of the Relevant Mudaraba shall be as follows (the “Threshold”):</div>
      <div class="second_set"><img width="130px" src="https://erp.venushrms.com/public/uploads/crm_pdf_img/stl_pdf.png" /></div>

      <div class="second_set">R	means an amount equal to the Relevant Mudaraba Capital (or the relevant Remaining Mudaraba Capital as the case may be, as <br />&nbsp;&nbsp;&nbsp;&nbsp;appearing in column 4 in the table appearing under paragraph 2.9 of this Relevant Investment Plan).</div>

        <div class="second_set">P	means the aggregate of 8% per annum.</div>

          <div class="second_set">n	number of days determined on the following basis:</div>

          <div class="third_set">(a)	with regard to a distribution of the Relevant Mudaraba Profit on the Profit Distribution Date, the number of days in the relevant <br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Profit Distribution Period; or</div>

            <div class="third_set">(b)	with regard to a distribution of the Relevant Mudaraba Profit on the Relevant Mudaraba End Date:</div>
            <div class="forth_set">(i)	if the Mudarib has made the payment on such previous Profit Distribution Date, the number of days from the previous Profit <br />&nbsp;&nbsp;&nbsp;&nbsp;Distribution Date until the Relevant Mudaraba End Date; or </div>
              <div class="forth_set">(ii)	if the Mudarib has not made any payment on the previous Profit Distribution Date, the number of days from the Relevant <br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Mudaraba Commencement Date until the Relevant Mudaraba End Date.  </div>
            
      <br /><b style="font-size: 12px;">2.	RELEVANT MUDARABA DETAILS</b><br />
      <div class="first_set">This Relevant Mudaraba shall be subject to the terms, conditions and provisions of the Agreement and the following:</div>
      <div class="first_set">2.1.	Relevant Mudaraba Commencement Date:	________</div>
      
      <div class="first_set">2.2.	Relevant Mudaraba Completion Date:	________,</div>
    </td></tr>
  </table>
  <div class="internal-note" style="margin-top: 20px;">
        Internal use only
    </div>

      <div class="page-break"></div>

      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td>
      <div class="first_set">Subject to Clause 5.2 of theAgreement.</div>
      <div class="first_set">2.3.	Relevant Mudaraba Capital:			<b style="font-size: 12px;">{{ @$stl->currency_name->code }} {{ @App\SysHelper::com_curr_format(@$stl->amount_aed,'2','.',',') }}</b></div>

      <div class="first_set">2.4.	Relevant Company Contribution:			<b style="font-size: 12px;">AED 0</b></div>

        <div class="first_set">2.5.	Joint Contribution:				<b style="font-size: 12px;">{{ @$stl->currency_name->code }} {{ @App\SysHelper::com_curr_format(@$stl->amount_aed,'2','.',',') }}</b></div>
        <div class="first_set">2.6.	Relevant Contribution Ratio</div>

        <div class="second_set">2.6.1.	Relevant Mudaraba Capital:	100% </div>
          <div class="second_set">2.6.2.	Relevant Company Contribution:	 0% </div>
      
            <div class="first_set">2.7.	Profit Distribution</div>
            <div class="second_set">2.7.1.	First Level Distribution  </div>

            <div class="third_set">The First Level Distribution shall be as follows:</div>
              <div class="third_set">a)	Company share in the Relevant Activity Profit:  		 0% </div>
                <div class="third_set">b)	Relevant Mudaraba share in the Relevant Activity Profit: 	 100%.</div>


                <div class="second_set">2.7.2.	Relevant Mudaraba Profit Ratios</div>
                <div class="third_set">The entitlement of the Parties with regard to the Relevant Mudaraba Profit shall be on the following profit ratios:</div>
    
                <div class="third_set">a)	Rab-ul-Maal share in the Relevant Mudaraba Profit: 		90%</div>
                  <div class="third_set">b)	Mudarib share in the Relevant Mudaraba Profit: 		10 %</div>

                  
            <div class="first_set">2.8.	Profit Distribution Period and the Profit Distribution Date</div>
            <div class="second_set">The Profit Distribution Period and the Profit Distribution Date with regard to the Relevant Mudaraba shall be as follows:</div>

            <table width="100%" border="1" cellspacing="0" cellpadding="0">
              <tr style="color: #000000;">
                <td style="width: 20%; text-align: center;">Sl. No. </td>
                <td style="width: 40%; text-align: center;">Profit Distribution Period</td>
                <td style="width: 40%; text-align: center;">Profit Distribution Date</td>
              </tr>
              <tr>
                <td style=" text-align: center;">1</td>
                <td style=" text-align: center;">From ________ to ________</td>
                <td style=" text-align: center;"></td>
              </tr>
            </table>

            
            <div class="first_set">2.9.	Partial Liquidation</div>
            <div class="second_set">Further to Clause 4.6 of the Agreement, the Parties agree that the Partial Liquidation shall be on a constructive liquidation basis in the manner and on dates as mentioned in the table below (the “Partial Liquidation Date”).</div>

            <div class="first_set"><br /><br /><br /><br /><br /><br />____________________________________________</div>
            <div class="first_set">This should directly proportional to the extent of the relevant contributions. </div>
            
  <div class="internal-note" style="margin-top: 70px;">
        Internal use only
    </div>
            
            
            
      <div class="page-break"></div>

      
            <div class="second_set">On each Partial Liquidation Date, the Mudarib shall return an amount out of the Relevant Mudaraba Capital (to the extent intact) to the Rab-ul-Maal as mentioned in the table below and the remaining Relevant Mudaraba Capital shall be reinvested in the Relevant Activity (the <u>“Partial Liquidation Amount”</u>).</div>
            <div class="second_set">For avoidance of doubt the provisions relating to the Relevant Mudaraba Capital shall mutatis mutandis apply to the remaining Relevant Mudaraba Capital and the Agreement and the Relevant Investment Plan shall be construed accordingly.</div>

            <table width="100%" border="1" cellspacing="0" cellpadding="0">
              <tr style="color: #000000;">
                <td style="width: 10%; text-align: center;">Sl. No. </td>
                <td style="width: 30%; text-align: center;">Partial Liquidation Date</td>
                <td style="width: 30%; text-align: center;">Partial Liquidation Amount</td>
                <td style="width: 30%; text-align: center;">Remaining Mudaraba Capital</td>
              </tr>
              <tr>
                <td style=" text-align: center;">1</td>
                <td style=" text-align: center;"></td>
                <td style=" text-align: center;"></td>
                <td style=" text-align: center;"></td>
              </tr>
            </table>

            <div class="first_set">2.10.	Relevant Mudaraba End Date</div>
            <div class="second_set">The Relevant Mudaraba End Date shall be determined as per Clause 5 of the Agreement.</div>
            
          </td></tr>
        </table>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr><td>
            <div class="first_set">1.	MUDARIB REPRESENTATIONS AND WARRANTIES</div>
            <div class="second_set">The Mudarib irrevocably and unconditionally represents to the Rab-ul-Maal that the representations and warranties provided in Clause 14 of the Agreement are current and that it shall be bound by the provisions stated therein.</div>
            
            <div class="first_set">2.	MUDARIB UNDERTAKINGS </div>
            <div class="second_set">The Mudarib irrevocably and unconditionally undertakes to the Rab-ul-Maal that it shall comply with its undertakings provided in Clause 15 of the Agreement.</div>
            <div class="first_set">3.	GOVERNING LAW AND DISPUTE RESOLUTION</div>
            <div class="second_set">The Governing Law and Disputes and Jurisdiction, as provided in Clause 25 of the Agreement shall be applicable for this Relevant Investment Plan.</div>
            
          </td></tr>
        </table>
        <div class="first_set">IN WITNESS WHEREOF this Relevant Investment Plan has been executed by the Parties on the date specified above.</div>
            <div class="first_set"><br /><br /><br />_______________________________________</div>
            <div class="first_set">(Signature)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(Stamp)</div>
            
            <div class="first_set"><b style="font-size: 12px;">{{@$stl->bank_name->bank_name}}</b></div>
            <div class="first_set">Represented by: <b style="font-size: 12px;">{{@$stl->bank_representative}}</b></div>


            <div class="first_set"><br /><br /><br />_______________________________________</div>
            <div class="first_set">(Signature)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(Stamp)</div>
            <div class="first_set"><b style="font-size: 12px;">(as Mudarib)</b></div>
            <div class="first_set">Represented by : <b style="font-size: 12px;">{{@$stl->owner_name}}</b></div>
        

  <div class="internal-note" style="margin-top: 40px;">
        Internal use only
    </div>
  {{-- <div class="footer2">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td width="50px" align="left">Document:- 4</td>
            <td width="50px" align="right">P.T.O</td>
        </tr>
    </table>
  </div> --}}
<div class="page-break"></div>
    

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td valign="top" style="line-height: 20px; text-align: center;"><b style="font-size: 12px;">SCHEDULE 1<br />SUPPLEMENTAL PURCHASE UNDERTAKING</b></td>
  </tr>
  <tr>
    <td>

      <div class="first_set">This Supplemental Purchase Undertaking Deed (the <u>“Supplemental Purchase Undertaking”</u>) is issued on the {{date('d/m/Y', strtotime(@$stl->submition_date))}} BY:</div>
      <div class="first_set">(1)	{{ $company->company_name }}, a <u>Limited Liability company</u>, established and existing under the laws of the Emirate of Dubai and having its registered office in PO Box: 124402, {{ $company->city }}, UAE, represented by Mr. {{ $stl->owner_name }}  (the <u>"Company"</u> or the <u>"Promissor"</u>, which expression shall be deemed to include its respective successors, transferees and assigns);</div>
      
      
      <div class="first_set"><b style="font-size: 12px;">IN FAVOUR OF:</b><br >
      (2)	{{ @$stl->bank_name->bank_name }}, a company established and existing under the laws of the Emirate of Dubai and having its registered office in {{ @$stl->bank_name->branch_location }}, represented by its authorized signatory, {{ @$stl->owner_name }},  (the <u>"Promissee"</u> or the <u>"{{ @$stl->bank_name->account_name }}"</u>, which expression shall be deemed to include its respective successors, transferees and assigns).</div>

      <div class="first_set"><b style="font-size: 12px;">NOW THEREFORE THE PROMISSOR PROVIDES AS FOLLOWS:</b><br >
      1.	DEFINITIONS AND INTERPRETATIONS<br >
      1.1	Except as otherwise expressly provided in this Supplemental Purchase Undertaking, capitalised terms used in this Supplemental Purchase Undertaking and not otherwise defined herein shall have the meanings given to such terms in the Master Mudaraba Agreement and the Master Purchase Undertaking.  In addition, the following terms have the meanings given below:</div>
      <div class="second_set"><u>“Relevant Mudaraba”</u> means the relevant Mudaraba entered into pursuant to the Master Mudaraba Agreement and the Relevant Investment Plan dated {{date('d/m/Y', strtotime(@$stl->submition_date))}}.</div>
      <div class="second_set"><u>“Relevant Mudaraba Assets”</u> in respect of each Relevant Mudaraba, mean the Relevant Mudaraba Assets belonging to the Rab-ul-Maal.</div>

      <div class="first_set">1.2	In this Supplemental Purchase Undertaking, Clause 1.2 of the Master Purchase Undertaking shall mutatis mutandis apply hereto.</div>
      
      <div class="first_set">2.	SUPPLEMENTAL PURCHASE UNDERTAKING<br >
      The Promissor hereby irrevocably undertakes, in relation to the subject Relevant Mudaraba, to purchase the rights, title, interests, benefits and entitlements in and to the Relevant Mudaraba Assets at the Relevant Exercise Price on the occurrence of the Exercise Event and upon the exercise of its right granted hereto to the Promissee.</div>
         
      <div class="first_set">3.	APPLICABILITY OF PROVISIONS<br >
        This Supplemental Purchase Undertaking is supplemental to, and should be read and construed as one document with, the Master Purchase Undertaking.  The provisions of Clauses 3 to 15 (inclusive) of the Master Purchase Undertaking shall apply mutatis mutandis to this Supplemental Purchase Undertaking.</div>
      
        <div class="first_set" style="text-align: center;"><b style="font-size: 12px;">THIS SUPPLEMENTAL PURCHASE UNDERTAKING HAS BEEN EXECUTED AS A DEED</b></div>
      
        
        <div class="first_set"><b style="font-size: 12px;">EXECUTED as a DEED by the authorized</b></div>
        <div class="first_set">Signatory(ies) of <b style="font-size: 12px;">{{ $company->company_name }}</b>  (the "Promissor"): </div>
            <div class="first_set"><br /><br />_______________________________________ Customer sign</div>
            
            <div class="first_set">Name of Signatory : <b style="font-size: 13x;">{{@$stl->owner_name}}</b></div>
    </td>
  </tr>
</table>
  <div class="internal-note" style="margin-top: 0px;">
        Confidential
    </div>

<div class="page-break"></div>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr><td>
      
      <div class="first_set"><b style="font-size: 15px;">Product Description</b></div>

      @php
  $inv = "";
  $stl_items_h = 0;
  $stl_items_l = 0;
  $invoice_total = 0;  // Variable to hold the total for each invoice
  $i = 1;
@endphp

@if (count($stl_items) > 0)
  @foreach ($stl_items as $index => $item)
    @if ($inv != $item->pi_inv_no)
      <!-- Display total before moving to the next invoice -->
      @if ($with_with_out == 1)
      @if ($inv != "") <!-- Check if this is not the first invoice -->
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td class="item-row" style="text-align: right; font-weight: bold;">Total</td>
          <td class="item-row" style="text-align: right; width: 13%; font-weight: bold;">{{ @App\SysHelper::com_curr_format($invoice_total, 2, '.', ',') }}</td>
        </tr>
      </table>
      @endif
      @endif

      <!-- New invoice section -->
      <br />
      <div><b style="font-size: 12px;">{{ $tax_invoices }} {{ $item->pi_inv_no }}</b></div>
      <br />
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td class="item-head-row" style="width: 7%; font-weight: bold; text-align: center;">Sl.No</td>
          <td class="item-head-row" style="width: 30%; font-weight: bold; text-align: center;">Item Part Number</td>
          <td class="item-head-row" style="width: 50%; font-weight: bold; text-align: center;">Description of Goods</td>
          @if ($with_with_out == 1)
          <td class="item-head-row" style="width: 13%; text-align: right; font-weight: bold;">Amount</td>
          @endif
        </tr>
      </table>

      @php
        // Reset total for the new invoice
        $i=1;
        $invoice_total = 0;
      @endphp
    @endif

    @php
      // Update the total for the current invoice
      $invoice_total += $item->amount;

      // Categorize the amounts into license and non-license
      if (str_contains(strtolower($item->description), 'license') || str_contains(strtolower($item->description), 'licence')) {
        $stl_items_l += $item->amount;
      } else {
        $stl_items_h += $item->amount;
      }
      
      // Update the current invoice number
      $inv = $item->pi_inv_no;
    @endphp

    <!-- Item row -->
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="item-row" style="width: 7%; text-align: center;">{{ $i }}</td> <!-- Show 1-based index -->
        <td class="item-row" style="width: 30%;">{{ $item->part_no }}</td>
        <td class="item-row" style="width: 50%;">{{ $item->description }}</td>
        @if ($with_with_out == 1)
        <td class="item-row" style="width: 13%; text-align: right;">{{ @App\SysHelper::com_curr_format(@$item->amount,'2','.',',') }}</td>
        @endif
      </tr>
    </table>
    <?php $i++; ?>
  @endforeach

  <!-- Final total for the last invoice -->
  @if ($with_with_out == 1)
  @if ($inv != "") <!-- Ensure we display the total for the last invoice -->
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td class="item-row" colspan="3" style="text-align: right; font-weight: bold;">Total</td>
      <td class="item-row" style="text-align: right; width: 13%; font-weight: bold;">{{ @App\SysHelper::com_curr_format($invoice_total, 2, '.', ',') }}</td>
    </tr>
  </table>
  @endif
  @endif
@endif




    </td>
  </tr>
</table>

<br /><br />
@if ($with_with_out == 1)
<table width="50%" border="1" cellspacing="0" cellpadding="0">
  <tr style="color: #000000;">
    <td style="width: 70%;">Value of Hardware</td>
    <td style="width: 30%; text-align: right;">{{ @App\SysHelper::com_curr_format(@$stl_items_h,'2','.',',') }}</td>
  </tr>
  <tr style="color: #000000;">
    <td style="width: 70%;">Value of License</td>
    <td style="width: 30%; text-align: right;">{{ @App\SysHelper::com_curr_format(@$stl_items_l,'2','.',',') }}</td>
  </tr>
  <tr style="color: #000000;">
    <td style="width: 70%;">Total</td>
    <td style="width: 30%; text-align: right;">{{ @App\SysHelper::com_curr_format(@$stl_items->sum('amount'),'2','.',',') }}</td>
  </tr>
</table>
@endif
{{-- <div class="footer2">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
          <td width="50px" align="left">Document:- 7</td>
          <td width="50px" align="right">P.T.O</td>
      </tr>
  </table>
</div> --}}

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
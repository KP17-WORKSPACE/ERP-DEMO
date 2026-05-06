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
    @page { margin: 100px 0px; }
    header { position: fixed; left: 0px; top: -100px; right: 0px; height: 100px; background-color: white; text-align: center; }
    footer { position: fixed; left: 0px; bottom: -100px; right: 0px; height: 55px; background-color: white; }
    footer .page:after { content: counter(page, upper-roman); }

    body{font-family: Verdana, sans-serif; font-size:13px; color:#000000;}
    th, td {padding: 5px 5px;}
    .tdd{border:dashed 1px #b2b2b2; border-width:0 0 1px 0;}
    span{font-size:17px; font-weight: bold;}
    main{margin:0px 50px;}

    .dttable {}
    .dttable th, .dttable td {padding: 5px 5px; text-align:left; border:solid 1px #b2b2b2; border-width:1px; font-size: 12px;}
    .dttable th{background: #2b2a6c;}
    .algc{text-align: left !important; font-weight: bold; background: #9e9e9e; color: #ffffff;}
    .subhd{font-weight: bold;font-size: 13px;}
</style>


</head>
<body>
  <header><img  src="{!! asset('public/backEnd/img/pdf-header.jpg') !!}" width="100%"></header>
  <footer><img  src="{!! asset('public/backEnd/img/pdf-footer.jpg') !!}" width="100%"></footer>
  <main>
  <table width="100%">
    <tr>
        <td colspan="2" align="center"><br /><br /><span><u>Transfer of Ownership of Free Zone Goods</u></span><br /><br /><br /></td>
    </tr>
    <tr>
        <td colspan="2">
            This is to certify that I/We have this day transferred the ownership of the under
            <br />Mentioned goods to:<br /><br />
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <b>{{ $cl->ship_to}}</b>
        </td>
    </tr>
    <tr>
        <td width="200px">Invoice No.</td>
        <td> : {{ $cl->invoice_no}}</td>
    </tr>
    <tr>
        <td>Free Zone Bill of Entry No.</td>
        <td> : {{ $cl->free_zone_bill_no}}</td>
    </tr>
    <tr>
        <td>Description of Goods</td>
        <td> : {{ $cl->goods_description}}</td>
    </tr>
    <tr>
        <td>Quantity</td>
        <td> : {{ $total[0]->qty1 }} PCS</td>
    </tr>
    <tr>
        <td>Gross Weight</td>
        <td> : {{ $total[0]->weight1 }}</td>
    </tr>
    <tr>
        <td>Country of Origin</td>
        <td> : <?php if(count($coos) >0){ foreach($coos as $val) { $arr1[] = $val->coo;} echo implode(', ', $arr1); }?></td>
    </tr>
    <tr>
        <td>BOE NO</td>
        <td> : {{ $cl->boe_no}}</td>
    </tr>
    <tr>
        <td colspan="2">
            <br /><br />
            <br /><br />
            <br /><br />
        </td>
    </tr>
    
    <tr>
        <td>Authorized Signature & Stamp</td>
        <td align="right">Date: {{date('d/m/Y', strtotime($cl->invoice_date))}}</td>
    </tr>
    <tr>
        <td colspan="2">
            <hr/><br/>
            I/We SYSCOM FZE hereby certify that as from this date, am/are the owners of the above-mentioned goods and that I/we undertake to pay when called upon to do so, all portage and other charges due and occurring thereon.
            <br/><br/>
            Transferee's Stamp & Authorized Signature
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <div style="page-break-before:always">&nbsp;</div>
        </td>
    </tr>
    
</table>

{{-- Not Free Zone Internal Transfer --}}
@if($cl->customer_bill_type !="Free Zone Internal Transfer")
@if(count($cl_item) >0)

<table width="100%" cellpadding="0" cellspacing="0">
<tr>
    <td colspan="2" align="center"><br /><span><u>Invoice</u></span><br /><br /><br /></td>
</tr>
<tr>
    <td align="left">From: SYSCOM FZE<br />
        RA08FD03<br />
        Jebel Ali Freezone, PO Box 124402<br />
        Dubai, UAE
    </td>
    <td align="right">
        Invoice No: {{$cl->invoice_no}}<br />
        Date: {{date('d/m/Y', strtotime($cl->invoice_date))}}


    </td>
</tr>
<tr>
    <td colspan="2"><br /><br /></td>
</tr>
<tr>
    <td colspan="2">Bill To: {{ $cl->bill_to}}</td>
</tr>
<tr>
    <td colspan="2">Ship To: {{ $cl->ship_to}}</td>
</tr>
<tr>
    <td colspan="2"><br /></td>
</tr>
</table>

            <table width="100%" cellpadding="0" cellspacing="0" border="1" class="dttable">
                <tr>
                    <td width="5%" class="subhd">Item</td>
                    <td width="15%" class="subhd">Part No</td>
                    <td width="30%" class="subhd">Description</td>
                    <td width="5%" class="subhd">Qty</td>
                    <td width="10%" class="subhd">Unit Price</td>
                    <td width="10%" class="subhd">Amount</td>
                </tr>
                <?php $i=1; foreach($cl_item as $val){?><tr>
                    <td>{{ $i }}</td>
                    <td>{{ $val["partno"] }}</td>
                    <td>{{ $val["description"] }}</td>
                    <td>{{ $val["qty"] }}</td>
                    <td>{{ @App\SysHelper::com_curr_format($val["price"], 2, '.', ',') }}</td>
                    <td>{{ @App\SysHelper::com_curr_format($val["totalprice"], 2, '.', ',') }}</td>
                </tr><?php $i++; } ?>
                <tr>
                    <td colspan="3" align="right">Total</td>
                    <td>{{ $total[0]->qty1 }}</td>
                    <td>{{ @App\SysHelper::com_curr_format($total[0]->price1, 2, '.', ',') }}</td>
                    <td>{{ @App\SysHelper::com_curr_format($total[0]->totalprice1, 2, '.', ',') }}</td>
                </tr>
            </table>
            <br />
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td colspan="2">Country of Origin: <b><?php if(count($coos) >0){ foreach($coos as $val) { $arr2[] = $val->coo;} echo implode(', ', $arr2); }?></b></td>
                </tr>
                <tr>
                    <td colspan="2">Total Gross Weight: <b>{{ $total[0]->weight1 }}</b></td>
                </tr>
                <tr>
                    <td colspan="2">No of PCS: <b>{{ $total[0]->qty1 }}</b></td>
                </tr>
                <tr>
                    <td colspan="2"><br /></td>
                </tr>                
                <tr>
                    <td colspan="2">
                        <div style="page-break-before:always">&nbsp;</div>
                    </td>
                </tr>
                </table>
            @endif
            @endif
{{-- Not Free Zone Internal Transfer --}}

{{-- Free Zone Internal Transfer --}}
@if(count($cl_item) >0)

<table width="100%" cellpadding="0" cellspacing="0">
<tr>
    <td colspan="2" align"center"><span><u>@if($cl->customer_bill_type=="Free Zone Internal Transfer") Invoice @if($cl->invoice_no !="SYZ-60112")/ Packing List @endif @else Packing List @endif</u></span><br /><br /></td>
</tr>
<tr>
    <td align="left">From: SYSCOM FZE<br />
        RA08FD03<br />
        Jebel Ali Freezone, PO Box 124402<br />
        Dubai, UAE
    </td>
    <td align="right">
        Invoice No: {{$cl->invoice_no}}<br />
        Date: {{date('d/m/Y', strtotime($cl->invoice_date))}}


    </td>
</tr>
<tr>
    <td colspan="2"><br /><br /></td>
</tr>
<tr>
    <td colspan="2">Bill To: {{ $cl->bill_to}}</td>
</tr>
<tr>
    <td colspan="2">Ship To: {{ $cl->ship_to}}</td>
</tr>
<tr>
    <td colspan="2"><br /></td>
</tr>
</table>

            <table width="100%" cellpadding="0" cellspacing="0" border="1" class="dttable">
                <tr>
                    <td width="5%" class="subhd">Item</td>
                    <td width="13%" class="subhd">Part No</td>
                    <td width="20%" class="subhd">Description</td>
                    <td width="7%" class="subhd">COO</td>
                    <td width="10%" class="subhd">HS Code</td>
                    <td width="5%" class="subhd">Weight</td>
                    <td width="5%" class="subhd">Qty</td>
                    <td width="10%" class="subhd">Unit Price</td>
                    <td width="10%" class="subhd">Amount</td>
                </tr>
                <?php $i=1; foreach($cl_item as $val){?><tr>
                    <td>{{ $i }}</td>
                    <td>{{ $val["partno"] }}</td>
                    <td>{{ $val["description"] }}</td>
                    <td>{{ $val["coo"] }}</td>
                    <td>{{ $val["hscode"] }}</td>
                    <td>{{ $val["weight"] }}</td>
                    <td>{{ $val["qty"] }}</td>
                    <td>{{ @App\SysHelper::com_curr_format($val["price"], 2, '.', ',') }}</td>
                    <td>{{ @App\SysHelper::com_curr_format($val["totalprice"], 2, '.', ',') }}</td>
                </tr><?php $i++; } ?>
                <tr>
                    <td colspan="5" align="right">Total</td>
                    <td>{{ $total[0]->weight1 }}</td>
                    <td>{{ $total[0]->qty1 }}</td>
                    <td>{{ @App\SysHelper::com_curr_format($total[0]->price1, 2, '.', ',') }}</td>
                    <td>{{ @App\SysHelper::com_curr_format($total[0]->totalprice1, 2, '.', ',') }}</td>
                </tr>
            </table>
            <br />
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td colspan="2">Country of Origin: <b><?php if(count($coos) >0){ foreach($coos as $val) { $arr2[] = $val->coo;} echo implode(', ', $arr2); }?></b></td>
                </tr>
                <tr>
                    <td colspan="2">Total Gross Weight: <b>{{ $total[0]->weight1 }}</b></td>
                </tr>
                <tr>
                    <td colspan="2">No of PCS: <b>{{ $total[0]->qty1 }}</b></td>
                </tr>
                <tr>
                    <td colspan="2"><br /></td>
                </tr>                
                <tr>
                    <td colspan="2">
                        <div style="page-break-before:always">&nbsp;</div>
                    </td>
                </tr>
                </table>
            @endif
{{-- Free Zone Internal Transfer --}}

<table width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td colspan="2" align="center"><br /><span><u>Delivery Advice</u></span><br /><br /><br /></td>
    </tr>
    <tr>
        <td>Date: {{date('d/m/Y', strtotime($cl->invoice_date))}}<br/><br/>
            Dept. of Ports & Customs,<br/>
            Please authorize release of the below mentioned goods from our warehouse to  Jebel Ali
        </td>
        <td>
            <table width="100%" cellpadding="0" cellspacing="0" border="1" class="dttable">
                <tr>
                    <td colspan="3">{{ $cl->invoice_no }}</td></tr>
                <tr>
                    <td width="35%" class="subhd">Importer Code</td>
                    <td width="35%" class="subhd">Agent Code</td>
                    <td width="30%" class="subhd">Rep.Card No.</td>
                </tr>
                <tr>
                    <td>AE-1158539</td>
                    <td>F-23000</td>
                    <td></td>
                </tr>
            </table>

        </td>
    </tr>
    <tr>
        <td colspan="2"><br />
            <table width="100%" cellpadding="0" cellspacing="0" border="1" class="dttable">
                <tr>
                    <td width="10%"><b>CNTR No</b></td>
                    <td width="10%"><b>Type</b></td>
                    <td width="10%"><b>Quantity</b></td>
                    <td width="10%"><b>Weight Kgs/ M.T</b></td>
                    <td width="10%"><b>Volume CBM</b></td>
                    <td colspan="2" width="50%"><b>Description of Goods</b></td>
                </tr>
                <tr>
                    <td></td>
                    <td>{{ $cl->box_type }}</td>
                    <td>{{ $cl->box_qty }}</td>
                    <td>{{ $total[0]->weight1 }} Kgs</td>
                    <td>{{ $cl->cbm }}</td>
                    <td colspan="2" rowspan="3">AS PER ATTACHED INVOICE NO: {{$cl->invoice_no}}</td>
                </tr>
                <tr>
                    <td colspan="5"><b>Paymet Method</b></td>
                </tr>
                <tr>
                    <td colspan="5">{{-- CDR Cash, CDR Bank, Deposit, Credit A/C *, Stan. G*, Bank G*, FTT, Alcohol, Other --}}
                        {{ $cl->payment_method }}</td>
                </tr>
				<tr>
                    <td colspan="5">Ref A/C No. *</td>
                    <td><b>Exit Point</b></td>
                    <td><b>Destination</b></td>
                </tr>
				<tr>
                    <td colspan="5"> </td>
                    <td>{{ $cl->exit_point }}</td>
                    <td>{{ $cl->destination }}</td>
                </tr>                
				<tr>
                    <td colspan="5" rowspan="4"><b>B/E Ref Nos:</b><br /><?php echo str_replace(',', '<br />', $cl->boe_no); ?></td>
                    <td colspan="2">Carrier's Agent</td>
                </tr>
				<tr>
                    <td colspan="2"></td>
                </tr>
				<tr>
                    <td><b>Country of Origin</b></td>
                    <td><b>Value</b></td>
                </tr>
				<tr>
                    <td><b><?php if(count($coos) >0){ foreach($coos as $val) { $arr3[] = $val->coo;} echo implode(', ', $arr3); }?></b></td>
                    <td><b>
                        {{$currency->code}} {{ @App\SysHelper::com_curr_format($total[0]->totalprice1, 2, '.', ',') }}</b></td>
                </tr>

                <tr>
                    <td colspan="5"><b>Customs Bill Type</b></td>
                    <td colspan="2">I/We declare the details given herein to be true and complete</td>
                </tr>
                <tr>
                    <td colspan="5">
                        {{-- Import, Import for Re-Export, Temporary Exit, Free Zone Internal Transfer, BILL OF ENTRY --}}
                        {{ $cl->customer_bill_type }}<br /><br /><br /><br />
                    </td>
                    <td colspan="2"><br /><br /><br /><br /><b>Licensee/Agent Stamp & Signature</b></td>
                </tr>
                <tr>
                    <td colspan="5">For Custom Use<br /><br /><br /><br /></td>
                    <td colspan="2">I/We declare the details given herein to be true and complete<br /><br /><br />
                        Importer's Stamp & Signature*<br />
                        * Applicable only incase of imports
                    </td>
                </tr>
                
            </table>

        </td>
    </tr>
</table>

  </main>
</body>
</html>
<style>
    .pdfarea header {
        position: fixed;
        left: 20px;
        top: -50px;
        right: 20px;
        height: 80px;
        background-color: white;
        text-align: center;
        border-bottom: solid 0px #808080;
        color: #555555;
    }



    .pdfarea footer .page:after {
        content: counter(page, upper-roman);
    }

    .pdfarea {
        font-family: Verdana, sans-serif;
        font-size: 12px;
        color: #555555;
       
    }

    .pdfarea th,
    .pdfarea td {
        padding: 5px 5px;
    }

    .tdd {
        border: dashed 1px #9e9e9e;
        border-width: 0 0 1px 0;
    }

    b {
        font-size: 14px;
    }

    .m1 table {
        border: 0px solid #9e9e9e;
    }

    .m1 td {
        border: 1px solid #9e9e9e;
    }

    .tmc ol {
        padding: 0px;
        margin: 0px;
    }

    .bottom_b {
        font-size: 12px;
    }

    .page-break {
        page-break-after: always;
    }

    .m-0 {
        margin: 0px;
    }

    .p-0 {
        padding: 0px;
    }

    .item-head-row {
        background: #2c2b6d;
        color: #ffffff;
    }

    .item-row {
        border-bottom: solid 1px #2c2b6d;
    }
</style>


 <style>
   
 
   th, td {padding: 5px 5px;}
    .tdd{border:dashed 1px #b2b2b2; border-width:0 0 1px 0;}

    .pdfarea{
        font-size: 13px
    }


    .dttable {}
    .dttable th, .dttable td {padding: 5px 5px; text-align:left; border:solid 1px #b2b2b2; border-width:1px; font-size: 12px;}
    .dttable th{background: #2b2a6c;}
    .algc{text-align: left !important; font-weight: bold; background: #9e9e9e; color: #ffffff;}
    .subhd{font-weight: bold;font-size: 13px;}
</style>

<?php try { ?>





<div class="purchase-order-content-header d-flex align-items-center justify-content-between mb-1 gap-2">
    <h4 class="purchase-order-content-header-left">
        {{$cl->invoice_no}}
    </h4>
    <div class="purchase-order-content-header-right d-flex align-items-center justify-content-end gap-2">


        <form method="GET" action="{{ url('clearance',$cl->id) }}">
         
            <button type="submit" name="clr_action" value="edit" class="btn btn-light">
                <i class="ico icon-outline-pen-2 text-success"></i> Edit
            </button>
        </form>

        <form method="GET" action="{{ url('clearance') }}">
            <button type="submit" name="clr_action" value="add" class="btn btn-light">
                <i class="ico icon-outline-add-square text-success"></i> Add
            </button>
        </form>



        <div class="dropdown">
            <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="ico icon-outline-hamburger-menu"></i>
            </button>
            <ul class="dropdown-menu" style="">
                <li><a class="dropdown-item" href="{{ url('clearance/' . $cl->id . '/download') }}">
                        <i class="ico icon-outline-import text-success"></i>
                        Download</a></li>
              

            </ul>
        </div>
    </div>
</div>
<div class="card mb-3">
    <div class="card-body">
        <div class="" id="deal-info" role="tabpanel" aria-labelledby="deal-info-tab">

            <div class="row">
                <div class="col-2 mb-2">&nbsp;</div>
                <div class="col-8 mb-2 pdfarea">






                    <style>
                        .pagenum:before {
                            content: counter(page);
                        }
                    </style>

    <?php
        $total_weight=0;
        foreach($cl_item as $val) {
            $total_weight += $val["weight"];
        }
    ?>
  <table width="100%">
    <tr>
        <td colspan="2"  align="center"><br /><br /><span ><u class="title-15 fw-bold">Transfer of Ownership of Free Zone Goods</u></span><br /><br /></td>
    </tr>
    <tr>
        <td colspan="2" class="font-size-14 fw-semibold">
            This is to certify that I/We have this day transferred the ownership of the under
            <br />Mentioned goods to:<br />
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
        @if($cl->id > 45)
        <td> : {{ $total_weight }}</td>
        @else
        <td> : {{ $total_weight }}</td>
        @endif
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
            <br />
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
            Transferee&#39;s Stamp & Authorized Signature
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <div style="page-break-before:always">&nbsp;</div>
        </td>
    </tr>
    
</table>
            
{{-- firewall --}}
@if(count($cl_item) >0)

<table width="100%" cellpadding="0" cellspacing="0">
<tr>
    <td colspan="2" align="center"><br /><span><u class="title-15 fw-bold">Invoice / Packing List</u></span><br /><br /><br /></td>
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
                    <td width="10%" class="subhd">COO</td>
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
                    @if($cl->id>45)
                    <td>{{ $val["weight"]*$val["qty"] }}</td>
                    @else
                    <td>{{ $val["weight"] }}</td>
                    @endif
                    <td>{{ $val["qty"] }}</td>
                    <td>{{ $val["price"] }}</td>
                    <td>{{ $val["totalprice"] }}</td>
                </tr><?php $i++; } ?>
                <tr>
                    <td colspan="5" align="right" style="text-align: right;"><b>Total Amount ({{$currency->code}})</b></td>
                    @if($cl->id>45)
                    <td>{{ $total_weight }}</td>
                    @else
                    <td>{{ $total_weight }}</td>
                    @endif
                    <td>{{ $total[0]->qty1 }}</td>
                    <td>{{ $total[0]->price1 }}</td>
                    <td>{{ $total[0]->totalprice1 }}</td>
                </tr>
            </table>
            <br />
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td colspan="2"><br /><br /></td>
                </tr>
                <tr>
                    <td colspan="2">Country of Origin: <b><?php if(count($coos) >0){ foreach($coos as $val) { $arr2[] = $val->coo;} echo implode(', ', $arr2); }?></b></td>
                </tr>
                <tr>
                    @if($cl->id>45)
                    <td colspan="2">Total Gross Weight: <b>{{ $total_weight }}</b></td>
                    @else
                    <td colspan="2">Total Gross Weight: <b>{{ $total_weight }}</b></td>
                    @endif
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
{{-- firewall --}}

<?php if(count($coos) >0){ foreach($coos as $val) { $arr2[] = $val->coo;}
if (count(array_unique($arr2)) > 1) {?>

<table width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td colspan="2" align="center"><span><u>DETAILED COMMODITY CLASSIFICATION FORM<br />(FOR STATISTICAL USE)</u></span><br /><br /></td>
    </tr>    
<tr>
    <td colspan="2"><br /></td>
</tr>

<tr>
    <td colspan="2">Ports, Customs & Free Zone Corporation</td>
</tr>
</table>
<table width="100%" cellpadding="0" cellspacing="0"  class="dttable">
    <tr>
        <td width="10%" class="subhd">SL.NO</td>
        <td width="10%" class="subhd">HS Code</td>
        <td width="40%" class="subhd">Goods Description</td>
        <td width="10%" class="subhd">COO</td>
        <td width="10%" class="subhd">Weight</td>
        <td width="5%" class="subhd">Qty</td>
        <td width="15%" class="subhd">Amount</td>
    </tr>
</table>
    
    <?php $i=1; foreach($cl_item as $val){?>
    <table width="100%" cellpadding="0" cellspacing="0" class="dttable2">
        <tr>
            <td width="10%">{{ $i }}</td>
            <td width="10%">{{ $val["hscode"] }}</td>
            <td width="40%">{{ $val["description"] }}</td>
            <td width="10%">{{ $val["coo"] }}</td>
            @if($cl->id>45)
            <td width="10%">{{ $val["weight"] * $val["qty"] }}</td>
            @else
            <td width="10%">{{ $val["weight"] }}</td>
            @endif
            <td width="5%">{{ $val["qty"] }}</td>
            <td width="15%">{{ @App\SysHelper::com_curr_format($val["totalprice"], 2, '.', ',') }}</td>
        </tr>
    </table>

    <?php $i++; } ?>
    <table width="100%" cellpadding="0" cellspacing="0" class="dttable2">
        <tr>
        <td width="10%" class="subhd"></td>
        <td width="10%" class="subhd"></td>
        <td width="40%" class="subhd"></td>
        <td width="10%" class="subhd">Total</td>
        @if($cl->id>45)
        <td width="10%" class="subhd">{{ $total_weight }}</td>
        @else
        <td width="10%" class="subhd">{{ $total_weight }}</td>
        @endif
        <td width="5%" class="subhd">{{ $total[0]->qty1 }}</td>
        <td width="15%" class="subhd">{{ @App\SysHelper::com_curr_format($total[0]->totalprice1, 2, '.', ',') }}</td>
        </tr>
    </table>
    
    <table width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td colspan="2"><br /><br />
            We certify that the above information is correct.
            <br /><br /><br /><br /><br />
            Signature & Stamp</td>
    </tr>
    </table>

<div style="page-break-before:always">&nbsp;</div>
<?php } } ?>

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
                    @if($cl->id>45)
                    <td>{{ $total_weight }} Kgs</td>
                    @else
                    <td>{{ $total_weight }} Kgs</td>
                    @endif
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


                </div>
                <div class="col-2 mb-2">&nbsp;</div>
            </div>
        </div>
    </div>
</div>





<?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

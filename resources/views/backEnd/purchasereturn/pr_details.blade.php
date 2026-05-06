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
        background-image: url('{!! asset("public/" . $company->pdf_watermark . "") !!}');
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
    <?php try { ?>
    @php
        $pri_adjestment = \App\SysPurchaseInvoice::select('sys_purchase_invoice.doc_number as piv_no', 'sys_purchase_invoice.pi_date as doc_date', 'cat.credit_amount as total_amount', \DB::raw('sum(adj.paid_amount) as paid_amount'))
            ->join('sys_chartofaccounts_transaction as cat', 'cat.transaction_no', 'sys_purchase_invoice.doc_number')
            ->leftJoin('sys_purchase_return_adjestment as adj', 'adj.piv_no', 'sys_purchase_invoice.doc_number')
            ->where('sys_purchase_invoice.vendors', $pr->vendors)
            ->where('account_id', $pr->vendors)
            ->where('cat.company_id', session('logged_session_data.company_id'))
            ->groupBy('sys_purchase_invoice.doc_number', 'sys_purchase_invoice.pi_date', 'cat.credit_amount')
            ->get();

        $invoice_amount = \App\SysChartofAccountsTransaction::where('transaction_no', $pr->pi_number)
            ->where('transaction_type', 'purchaseinvoice')
            ->where('account_id', $pr->vendors)
            ->sum('credit_amount');
    @endphp

        



    <div class="purchase-order-content-header sticky-top d-flex align-items-center justify-content-between gap-2" style="background-color: #f7f8fd">
        <div class="d-flex align-items-center gap-2">
            <h4 class="purchase-order-content-header-left">
                {{ $pr->doc_number }}
            </h4>
            @if(isset($pr->deal_id) && $pr->deal_id)
                {!! App\SysHelper::deal_pipeline_purchase($pr->deal_id) !!}
            @endif
        </div>
        <div class="purchase-order-content-header-right">

            <a class="btn btn-light text-dark" href="{{url('purchase-return/'.$pr->id.'/?pr_action=edit')}}">
                <i class="ico icon-outline-pen-2 text-success"></i> Edit
            </a>
            <a class="btn btn-light text-dark" href="{{url('purchase-return/'.$pr->id.'/?pr_action=add')}}">
                <i class="ico icon-outline-add-square text-success"></i> Add
            </a> 
          
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ico icon-outline-hamburger-menu"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{url('purchase-return/'.$pr->id.'/delete')}}"><i class="ico icon-outline-trash-bin-minimalistic text-danger"></i> Cancel Return</a></li>
                    <li><a class="dropdown-item" href="{{url('purchase-return/'.$pr->id.'/download')}}"><i class="ico icon-outline-document-medicine text-success"></i> Download</a></li>
                    <li><button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#adjustmentModal"><i class="ico icon-outline-calculator-minimalistic text-warning"></i> Adjustment</button></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="card mb-3 card-min-height">
        <div class="card-body">
            <div class="" id="deal-info" role="tabpanel" aria-labelledby="deal-info-tab">
                {{-- <div class="d-flex align-items-center gap-3 mt-3 mb-2">
                    <h5 class="m-0 text-green">heading</h5>
                </div> --}}
                <div class="row">
                    <div class="col-2 mb-2">&nbsp;</div>
                    <div class="col-8 mb-2 pdfarea" >
                        
                        {{-- ************* --}}
                            
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
          <td align="left"><img style="margin-left:-12px" src="{{asset('public/'.@$company->company_logo)}}" width="200px"/></td>
          <td align="right"><b style="font-size: 30px; font-weight: 400;">
          
          @if ($pr->debit_note == "PR")
Purchase Return
          @elseif($pr->debit_note == "DN")
              Debit Note
        @else
              Purchase Return
          @endif
          


          </b></td>
      </tr>
  </table>
  
  <br />

    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="55%" valign="top" style="line-height: 17px;">
            <b>From,</b><br>
            <b>{{@$company->company_name}}</b> <br>
              @if ($bill_contact_name != '')
                                        {{ $bill_contact_name }}<br />
                                    @endif
            <div>{{ @$company->stateRelation->name }}, {{ @$company->countryname->name }}</div>
            T: {{@$company->telephone}}, M: {{@$company->mobile}}<br />
            E: {{@$company->email}}<br />
            TRN No: {{@$company->vat_number}}
          </td>
          <td valign="top" style="line-height: 18px;">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td style="padding: 0px; margin: 0px; width: 150px;">Invoice No:</td>
                    <td style="padding: 0px; margin: 0px">: {{@$pr->doc_number}}</td>
                  </tr>
                  <tr>
                    <td style="padding: 0px; margin: 0px">Date:</td>
                    <td style="padding: 0px; margin: 0px">: {{date('d/m/Y', strtotime(@$pr->pi_date))}}</td>
                  </tr>
                  <tr>
                    <td style="padding: 0px; margin: 0px">Ref No:</td>
                    <td style="padding: 0px; margin: 0px">: {{@$pr->lpo_number}}</td>
                  </tr>
                  <tr>
                    <td style="padding: 0px; margin: 0px">Ref Date:</td>
                    <td style="padding: 0px; margin: 0px">: {{date('d/m/Y', strtotime(@$pr->lpo_date))}}</td>
                  </tr>
                  <tr>
                    <td style="padding: 0px; margin: 0px; vertical-align: top; white-space: nowrap;">Payment Terms:</td>
                    <td style="padding:0; margin:0; white-space:nowrap; max-width:190px; overflow:hidden; text-overflow:ellipsis;">: {{ $pr->paymentterms->title }} {{ $pr->payment_terms2 }}</td>
                  </tr>
            </table>
          </td>
        </tr>
    </table>
    <br />
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="55%" valign="top" style="line-height: 18px;">To,<br />
                <b style="font-size: 90%;">{{@$pr->accountname->account_name}}</b><br>
                {{@$contact_name}}<br />
               
                {{@$state}}, {{@$country}}<br>
                T: {{@$tel}}, M: {{@$mobile}}<br/>
                E: {{@$email}}<br/>
                @if($cust_trn_no!="") TRN No: {{@$cust_trn_no}} @endif
          </td>
          <td valign="top" style="line-height: 18px;">Ship To,<br />
            <b style="font-size: 90%;">{{@$pr->accountname->account_name}}</b><br>
            {{@$ship_contact_name}}<br />
           
            {{@$delivery_state}}, {{@$delivery_country}}<br>
            T: {{@$ship_tel}}, M: {{@$ship_mob}}<br/>
            E: {{@$ship_email}}<br/>
            @if($cust_trn_no!="") TRN No: {{@$cust_trn_no}} @endif
          </td>
        </tr>
    </table>

    <br />
    
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr class="item-head-row">
          <td style="width: 20px; text-align: center;">No</td>
          <td>Part No</td>
          <td style="width: 20px; text-align: center;">Qty</td>
          <td style="width: 70px; text-align: right;">Rate</td>
          <td style="width: 70px; text-align: right;">Value</td>
          <td style="width: 30px; text-align: right;">VAT%</td>
          <td style="width: 80px; text-align: right;">VAT Amount</td>
          <td style="width: 80px; text-align: right;">Amount</td>
        </tr>
    </table>
        <?php
            $i=1;
            $sub_total=0;
            $discount=0; $deal_discount=0; $deal_discount_vat=0; $deal_discount_vat_amount=0; $deal_discount_amount=0;
            $taxable_amt=0;
            $customs_charges=0;
            $vat_amount=0;
            $total_amount=0;
        ?>
        @if(count($pi_item)>0)
        @foreach ($pi_item as $item)
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td class="item-row" style="width: 20px;">{{$i}} <?php $i++;?></td>
            <td class="item-row" >
              <span style="font-weight:bold;">{{ $item->partnumber->part_number }}</span><br />
              {!! nl2br(@$item->description) !!}
            <br />  <span style="font-weight:bold;">{{ @$item->serialno }}</span>
            </td>
            <td class="item-row" style="width: 20px; text-align: center;">{{ $item->qty }}</td>
            <td class="item-row" style="width: 70px; text-align: right;">{{ @App\SysHelper::com_curr_format($item->unitprice,2,'.',',') }}</td>
            <td class="item-row" style="width: 70px; text-align: right;">{{ @App\SysHelper::com_curr_format($item->unitprice*$item->qty,2,'.',',') }}</td>
            <td class="item-row" style="width: 30px; text-align: right;">{{ @App\SysHelper::com_curr_format($item->tax,2,'.',',') }}</td>
            <td class="item-row" style="width: 80px; text-align: right;">{{ @App\SysHelper::com_curr_format($item->vatamount,2,'.',',') }}</td>
            <td class="item-row" style="width: 80px; text-align: right;">{{ @App\SysHelper::com_curr_format($item->taxableamount+$item->vatamount,2,'.',',') }}</td>
            <?php
            
            $sub_total += $item->unitprice*$item->qty;
            $discount += $item->discount;
            $taxable_amt += $item->taxableamount;
            $customs_charges += $item->customcharges;
            $vat_amount += $item->vatamount;
            $total_amount += $item->taxableamount + $item->vatamount;

            ?>


        </tr>
        </table>
        @endforeach

        <?php
        
        $deal_discount += $pr->deal_discount;
        $deal_discount_vat=$pi_item->max('tax');
        $deal_discount_vat_amount= $deal_discount * $deal_discount_vat/100;
        $deal_discount_amount= $deal_discount + $deal_discount_vat_amount;
        ?>
        @endif

        <div class="row">

    {{-- LEFT SIDE : Amount in Words + Terms --}}
    <div class="col-8">

        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td class="font-weight-600">
                    {{ $pr->currency_name->code }}
                    <?php echo ucwords(
                        @App\SysHelper::convertAmountToWords(
                            @App\SysHelper::com_curr_format($total_amount - $deal_discount_amount, 2, '.', ''),
                            $pr->currency_name->r_code,
                            $pr->currency_name->p_code
                        )
                    ); ?>
                </td>
            </tr>

            <tr>
                <td>
                    <b>Terms & Conditions</b><br />

                    <ol style="padding: 10px 0px 0px 15px; margin: 0px; font-size: 9px; text-align: justify;">
                        <li>The ownership of goods will remain with us until full payment is received.</li>
<li>Open box items are non-returnable, and all sales of such items are final.</li>
<li>Items without serial numbers are not covered under the warranty.</li>
<li>Damage caused by power fluctuations is not covered under the warranty.</li>
<li>To make a warranty claim, please contact the relevant vendor&#39;s service center.</li>
<li>Bank details:- Bank Name: {{@$company->bank_name}}, Account Number: {{@$company->account_number}}</li>
                    </ol>

                </td>
            </tr>
        </table>

    </div>

    {{-- RIGHT SIDE : Amount Table --}}
    <div class="col-4">

        {{-- SUB TOTAL --}}
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td></td>
                <td style="width:160px;text-align:left;font-weight:bold;border-bottom:1px solid #2c2b6d;">
                    Sub Total {{ $pr->currency_name->code }}
                </td>
                <td style="width:80px;text-align:right;font-weight:bold;border-bottom:1px solid #2c2b6d;">
                    {{ @App\SysHelper::com_curr_format($sub_total, 2, '.', ',') }}
                </td>
            </tr>
        </table>

        {{-- DISCOUNT --}}
        @if(($discount + $deal_discount) != 0)
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td></td>
                <td style="width:160px;text-align:left;font-weight:bold;border-bottom:1px solid #2c2b6d;">
                    Discount {{ $pr->currency_name->code }}
                </td>
                <td style="width:80px;text-align:right;font-weight:bold;border-bottom:1px solid #2c2b6d;">
                    {{ @App\SysHelper::com_curr_format($discount + $deal_discount, 2, '.', ',') }}
                </td>
            </tr>
        </table>
        @endif

        {{-- TAXABLE AMOUNT --}}
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td></td>
                <td style="width:160px;text-align:left;font-weight:bold;border-bottom:1px solid #2c2b6d;">
                    Taxable Amt. {{ $pr->currency_name->code }}
                </td>
                <td style="width:80px;text-align:right;font-weight:bold;border-bottom:1px solid #2c2b6d;">
                    {{ @App\SysHelper::com_curr_format($taxable_amt - $deal_discount, 2, '.', ',') }}
                </td>
            </tr>
        </table>

        {{-- VAT AMOUNT --}}
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td></td>
                <td style="width:160px;text-align:left;font-weight:bold;border-bottom:1px solid #2c2b6d;">
                    VAT Amount {{ $pr->currency_name->code }}
                </td>
                <td style="width:80px;text-align:right;font-weight:bold;border-bottom:1px solid #2c2b6d;">
                    {{ @App\SysHelper::com_curr_format($vat_amount - $deal_discount_vat_amount, 2, '.', ',') }}
                </td>
            </tr>
        </table>

        {{-- TOTAL AMOUNT --}}
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td></td>
                <td style="width:160px;text-align:left;font-weight:bold;border-bottom:1px solid #2c2b6d;">
                    Total Amount {{ $pr->currency_name->code }}
                </td>
                <td style="width:80px;text-align:right;font-weight:bold;border-bottom:1px solid #2c2b6d;">
                    {{ @App\SysHelper::com_curr_format($total_amount - $deal_discount_amount, 2, '.', ',') }}
                </td>
            </tr>
        </table>

    </div>

</div>
 


<div>
        {{-- <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
              <b>Terms & Conditions</b>
              <ol style="padding: 10px 0px 0px 15px; margin: 0px;">

<li>The ownership of goods will remain with us until full payment is received.</li>
<li>Open box items are non-returnable, and all sales of such items are final.</li>
<li>Items without serial numbers are not covered under the warranty.</li>
<li>Damage caused by power fluctuations is not covered under the warranty.</li>
<li>To make a warranty claim, please contact the relevant vendor&#39;s service center.</li>
<li>Bank details:- Bank Name: {{@$company->bank_name}}, Account Number: {{@$company->account_number}}</li>
            </ol>          
          </td>
          </tr>
      </table> --}}
      <br ><br ><br >
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td style="border: none; width:200px;" align="left" valign="top"><b class="bottom_b">Received By:</b><br ><br ></td>
          <td rowspan="4" style="border: none; width:200px;" align="center" valign="bottom">{{@$pr->createdby->full_name}}<br /><b class="bottom_b" style="font-size: 10px;">Prepared By</b></td>
          <td rowspan="4" style="border: none; width:200px;" align="right" valign="bottom"><b class="bottom_b" style="font-size: 10px;">For {!! str_replace('SYSCOM DISTRIBUTIONS LLC BRANCH ABU DHABI 1','SYSCOM DISTRIBUTIONS LLC<br />BRANCH ABU DHABI 1',$company->company_name) !!}</b></td>
        </tr>
        <tr>
          <td style="border: none;" align="left" valign="top"><b class="bottom_b">Name:</b><br ><br ></td>
        </tr>
        <tr>
          <td style="border: none;" align="left" valign="top"><b class="bottom_b">Phone:</b><br ><br ></td>
        </tr>
        <tr>
          <td style="border: none;" align="left" valign="top"><b class="bottom_b">Signature and stamp:</b></td>
        </tr>
      </table>
      <footer>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
           </tr>
        <tr>
          <td colspan="3" style="border: none; font-size: 10px;" align="right" valign="top">
            {{-- Page No <span style="" class="pagenum"></span> of {{@$po->doc_number}}</td> --}}
        </tr>
    </table>
    <img  src="{!! asset('public/'.$company->pdf_footer.'') !!}"  width="100%"/></td>
  </footer>
 
                        {{-- ************* --}}
                    </div>
                    <div class="col-2 mb-2">&nbsp;</div>
                </div>
            </div>
        </div>
    </div>

    <?php /*
    <div class="purchase-order-content-header">
        <h4 class="purchase-order-content-header-left">
        </h4>
        <div class="purchase-order-content-header-right">&nbsp;
            {{-- <button class="btn btn-light">
                <i class="ico icon-outline-add-square text-success"></i> Add
            </button>
            <button class="btn btn-light">
                <i class="ico icon-outline-pen-2 text-success"></i> Edit
            </button>
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ico icon-outline-hamburger-menu"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#dealcancelModal"><i class="ico icon-outline-trash-bin-minimalistic text-danger"></i> Cancel Deal</a></li>
                    <li><a class="dropdown-item" href="quote.html"><i class="ico icon-outline-document-medicine text-success"></i> Generate Quote</a></li>
                    <li><a class="dropdown-item" href="#"><i class="ico icon-outline-pen-2 text-warning"></i> Add Pre-Sales Request</a></li>
                    <li><a class="dropdown-item" href="#"> <i class="ico icon-outline-pen-2 text-warning"></i> Add Collaboration</a></li>
                    <li><a class="dropdown-item" href="#"> <i class="ico icon-outline-pen-2 text-warning"></i> End User Details</a></li>
                </ul>
            </div> --}}
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <div class="tab-pane fade show active" id="deal-info" role="tabpanel" aria-labelledby="deal-info-tab">
                <div class="d-flex align-items-center gap-3 mt-3 mb-2">
                    <h5 class="m-0 text-green">No details found</h5>
                </div>
                <div class="row">
                    <div class="col-12 mb-2">
                        No details found
                    </div>
                </div>
            </div>
        </div>
    </div> */ ?>

        <div class="modal side-panel fade" id="adjustmentModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" style="height: 500px !important;"> 
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="editModalLabel">Bill Wise Adjustments</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'purchase-return-add-adjestment', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'purchase-return-add-adjestment']) }}
                    <input type="hidden" value="{{ $pr->doc_number }}" name="adj_pri_no">
                    <input type="hidden" value="{{ $pr->lpo_number }}" name="edit_adj_lpo_no">
                    <input type="hidden" value="{{ $pr->doc_date }}" name="edit_adj_doc_date">
                    <div class="modal-body m-0 p-0">
                        <div class="card mb-0 mt-0">
                            <div class="card-body" style="height: 420px; overflow-y: scroll;">
                                <div class="row">
                                    <div class="col-12 mb-2">
                                        <input type="text" id="act_pri_adj_amount" value="{{ ($pri_adjestment->sum('total_amount') - $pri_adjestment->sum('paid_amount') + $invoice_amount) }}" hidden/>
                                        <input type="text" id="pri_adj_amount" value="{{ ($pri_adjestment->sum('total_amount') - $pri_adjestment->sum('paid_amount') + $invoice_amount) }}"  hidden/>
                                        <table class="table table-hover form-item-table" id="table_adjestment">
                                            <thead>
                                                <tr>
                                                    <th>Doc Date</th>
                                                    <th>PIV No</th>
                                                    <th class="text-end">Total</th>
                                                    <th class="text-end">Paid</th>
                                                    <th class="text-end">Balance</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $i=0; @endphp
                                                @if (count($pri_adjestment)>0)
                                                    @foreach ($pri_adjestment as $dt)
                                                        @php
                                                            $paid_amount = $dt->paid_amount ?: 0;
                                                            $balance_amount = abs($dt->total_amount - $paid_amount);
                                                        @endphp
                                                        <tr>
                                                            <td style="width:100px;"><input type="text" class="form-control" name="adj_doc_date[]" id="adj_doc_date_{{ $i }}" value="{{ date('d/m/Y', strtotime($dt->doc_date)) }}" readonly /></td>
                                                            <td style="width:100px;"><input type="text" class="form-control" name="adj_pi_no[]" id="adj_pi_no_{{ $i }}" value="{{ $dt->piv_no }}" readonly /></td>
                                                            <td style="width:100px;"><input type="text" class="form-control text-end" name="adj_total[]" id="adj_total_{{ $i }}" value="{{ number_format($dt->total_amount, 2, '.', '') }}" readonly /></td>
                                                            <td style="width:100px;"><input type="text" class="form-control text-end class_adj_paid" name="adj_paid[]" id="adj_paid_{{ $i }}" value="{{ number_format($paid_amount, 2, '.', '') }}" onchange="get_set_amount({{ $i }})" onclick="set_adjestment({{ $i }})" required /></td>
                                                            <td style="width:100px;"><input type="text" class="form-control text-end" name="adj_balance[]" id="adj_balance_{{ $i }}" value="{{ number_format($balance_amount, 2, '.', '') }}" readonly /></td>
                                                        </tr>
                                                        @php $i++; @endphp
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td style="width:100px;"><input type="text" class="form-control" name="adj_doc_date" value="{{ date('d/m/Y', strtotime($pr->doc_date)) }}" readonly /></td>
                                                        <td style="width:100px;"><input type="text" class="form-control" name="adj_pi_no" value="{{ $pr->pi_number }}" readonly /></td>
                                                        <td style="width:100px;"><input type="text" class="form-control" name="adj_lpo_no" value="{{ $pr->lpo_number }}" readonly /></td>
                                                        <td style="width:100px;"><input type="text" class="form-control text-end" name="adj_total" id="adj_total" value="{{ number_format($invoice_amount, 2, '.', '') }}" readonly /></td>
                                                        <td style="width:100px;"><input type="text" class="form-control text-end class_adj_paid" name="adj_paid" id="adj_paid" value="" onchange="get_set_amount(0)" required /></td>
                                                        <td style="width:100px;"><input type="text" class="form-control text-end" name="adj_balance" id="adj_balance" value="" readonly /></td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th></th>
                                                    <th></th>
                                                    <th class="text-end"><label id="footer_total"></label></th>
                                                    <th class="text-end"><label id="footer_paid"></label></th>
                                                    <th class="text-end"><label id="footer_balance"></label></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-light add-btn ms-2" id="discount_add_btn">
                            <i class="ico icon-outline-bookmark-opened text-success"></i> Adjust
                        </button>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>

        <script>
            function parseCurrency(value) {
                if (value === undefined || value === null || value === "") return 0;
                var cleaned = String(value).replace(/,/g, '').trim();
                return Number(cleaned) || 0;
            }

            function formatCurrency(value) {
                return Number(value).toFixed(2);
            }

            function get_set_amount(id) {
                set_adjestment(id);
                var adj_total = parseCurrency($('#adj_total_' + id).val());
                var adj_paid = parseCurrency($('#adj_paid_' + id).val());
                $('#adj_balance_' + id).val(formatCurrency(adj_total - adj_paid));

                updateAdjustmentTotals();
            }

            function set_adjestment(id) {
                var sum = parseCurrency($('#act_pri_adj_amount').val());
                var numItems = $('.class_adj_paid').length;
                var adj = 0;
                for (var i = 0; i < numItems; i++) {
                    if (i != id) {
                        adj += parseCurrency($('#adj_paid_' + i).val());
                    }
                }

                var adj2 = sum - adj;
                $('#pri_adj_amount').val(formatCurrency(Math.max(adj2, 0)));

                var adj3 = parseCurrency($('#pri_adj_amount').val());
                if (adj3 > 0) {
                    var adj_balance = parseCurrency($('#adj_balance_' + id).val());
                    if (adj3 >= adj_balance) {
                        $('#adj_paid_' + id).val(formatCurrency(adj_balance));
                    } else {
                        $('#adj_paid_' + id).val(formatCurrency(adj3));
                    }
                }

                updateAdjustmentTotals();
            }

            function updateAdjustmentTotals() {
                var total = 0;
                var paid = 0;
                var balance = 0;

                $('#table_adjestment tbody tr').each(function() {
                    var rowTotal = parseCurrency($(this).find('input[name="adj_total[]"]').val() || $(this).find('input[name="adj_total"]').val());
                    var rowPaid = parseCurrency($(this).find('input[name="adj_paid[]"]').val() || $(this).find('input[name="adj_paid"]').val());
                    var rowBalance = parseCurrency($(this).find('input[name="adj_balance[]"]').val() || $(this).find('input[name="adj_balance"]').val());

                    total += rowTotal;
                    paid += rowPaid;
                    balance += rowBalance;
                });

                $('#footer_total').text(formatCurrency(total));
                $('#footer_paid').text(formatCurrency(paid));
                $('#footer_balance').text(formatCurrency(balance));
            }

            $(document).on('blur', '.class_adj_paid', function() {
                updateAdjustmentTotals();
            });

            $(document).ready(function() {
                updateAdjustmentTotals();
            });
        </script>

    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
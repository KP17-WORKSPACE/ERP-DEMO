
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

        



    <div class="purchase-order-content-header sticky-top d-flex align-items-center justify-content-between gap-2" style="background-color: #f7f8fd">
        <div class="d-flex align-items-center gap-2">
            <h4 class="purchase-order-content-header-left">
                {{ $pi->doc_number }}
            </h4>
            @if(isset($pi->deal_id) && $pi->deal_id)
                {!! App\SysHelper::deal_pipeline_purchase($pi->deal_id) !!}
            @endif
        </div>
        <div class="purchase-order-content-header-right">
          <a class="btn btn-light text-dark" href="{{url('purchase-invoice/'.$pi->id.'?pi_action=edit')}}">
                <i class="ico icon-outline-pen-2 text-success"></i> Edit
            </a>
            <a class="btn btn-light text-dark" href="{{url('purchase-invoice/'.$pi->id.'?pi_action=add')}}">
                <i class="ico icon-outline-add-square text-success"></i> Add
            </a>
            
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ico icon-outline-hamburger-menu"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{url('purchase-invoice/'.$pi->id.'/delete')}}"><i class="ico icon-outline-trash-bin-minimalistic text-danger"></i> Cancel GRN</a></li>
                    <li><a class="dropdown-item" href="{{url('purchase-invoice/'.$pi->id.'/download')}}"><i class="ico icon-outline-document-medicine text-success"></i> Download</a></li>
                    <li><button type="button" class="dropdown-item" data-modal-size="modal-md" data-bs-target="#attachment_popup_win" data-bs-toggle="modal" class="btn btn-primary" onclick="view_attachment()"><i class="ico icon-outline-calculator-minimalistic text-warning"></i> Attachment</button></li>
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
          <td align="right"><b style="font-size: 30px; font-weight: 400;">PURCHASE INVOICE</b></td>
      </tr>
  </table>
  
  <br />

    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
           <td width="60%" valign="top" style="line-height: 18px;">
                                    <b>To,</b><br>

                <b>{{@$pi->accountname->account_name}}</b><br>
              Attn.  {{@$contact_name}}<br />
             
                {{@$state}}, {{@$country}}<br>
                T: {{@$tel}}, M: {{@$mobile}}<br/>
                E: {{@$email}}<br/>
                @if($cust_trn_no!="") TRN No: {{@$cust_trn_no}} @endif
          </td>
          <td valign="top" style="line-height: 18px;">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td  style="padding: 0px; margin: 0px; width: 150px;">Invoice No:</td>
                    <td style="padding: 0px; margin: 0px">: {{@$pi->doc_number}}</td>
                  </tr>
                  
                  <tr>
                    <td style="padding: 0px; margin: 0px">Date:</td>
                    <td style="padding: 0px; margin: 0px">: {{date('d/m/Y', strtotime(@$pi->pi_date))}}</td>
                  </tr>
                  <tr>
                    <td style="padding: 0px; margin: 0px">Ref No:</td>
                    <td style="padding: 0px; margin: 0px">: {{@$pi->lpo_number}}</td>
                  </tr>
                  <tr>
                    <td style="padding: 0px; margin: 0px">Ref Date:</td>
                    <td style="padding: 0px; margin: 0px">: {{date('d/m/Y', strtotime(@$pi->lpo_date))}}</td>
                  </tr>
                  <tr>
                    <td style="padding: 0px; margin: 0px; vertical-align: top; white-space: nowrap;">Payment Terms:</td>
                    <td style="padding:0; margin:0; white-space:nowrap; max-width:190px; overflow:hidden; text-overflow:ellipsis;">: {{ $pi->paymentterms->title }} {{ $pi->payment_terms2 }}</td>
                  </tr>
            </table>
          </td>
        </tr>
    </table>
    <br />
    
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="60%" valign="top" style="line-height: 18px;">Bill To,<br />
                <b style="font-size: 90%;">{{@$company->company_name}}</b><br>
                <div>{!! nl2br($company->company_address) !!}</div>
                 @if ($bill_contact_name != '')
                                        {{ $bill_contact_name }}<br />
                                    @endif
                {{-- {{@$contact_name}}<br />
                {{@$address}}<br>
                {{@$address2}}, {{@$city}}<br>
                {{@$state}}, {{@$country}}<br> --}}
            <div>{{ optional($company->stateRelation)->name }}, {{ optional($company->countryname)->name }}</div>

                T: {{@$company->telephone}}, M: {{@$company->mobile}}<br/>
                E: {{@$company->email}}<br/>
                TRN No: {{@$company->vat_number}}
          </td>
          <td valign="top" style="line-height: 18px;">Ship To,<br />
            <b style="font-size: 90%;">{{@$pi->shippingSupplierName->account_name}}</b><br>
            {{@$pi->shipping_name}}<br />
            {{@$ship_address1}}<br>
            T: {{@$pi->shipping_contact_no}}, M: {{ @$ship_mob }}<br/>
            E: {{@$pi->shipping_email}}<br/>
            TRN: {{ $ship_trnno }}

            {{-- @if($cust_trn_no!="") TRN No: {{@$cust_trn_no}} @endif --}}
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
              <span style="font-weight:bold;">{{ $item->productname->part_number }}</span><br />
              {!! nl2br($item->description) !!}</td>
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
        
        $deal_discount += $pi->deal_discount;
        $deal_discount_vat=$pi_item->max('tax');
        $deal_discount_vat_amount= $deal_discount * $deal_discount_vat/100;
        $deal_discount_amount= $deal_discount + $deal_discount_vat_amount;
        ?>
        @endif

 <div class="row">
    <div class="col-8">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td class="font-weight-600">
                    {{ $pi->currency_name->code }}
                    <?php 
                        echo ucwords(
                            @App\SysHelper::convertAmountToWords(
                                @App\SysHelper::com_curr_format($total_amount - ($deal_discount_amount ?? 0), 2, '.', ''),
                                $pi->currency_name->r_code,
                                $pi->currency_name->p_code
                            )
                        );
                    ?>
                </td>
            </tr>

            <tr>
                <td>
                    <b>Terms & Conditions</b><br />

                    @if ($pi->terms_and_condition == '')
                        <ol style="padding-left: 15px; font-size: 11px">
                          <li>The ownership of goods will remain with us until full payment is received.</li>
<li>Open box items are non-returnable, and all sales of such items are final.</li>
<li>Items without serial numbers are not covered under the warranty.</li>
<li>Damage caused by power fluctuations is not covered under the warranty.</li>
<li>To make a warranty claim, please contact the relevant vendor&#39;s service center.</li>
<li>Bank details:- Bank Name: {{@$company->bank_name}}, Account Number: {{@$company->account_number}}</li>
                        </ol>
                    @else
                        <div style="padding-top: 2px; font-size: 11px">
                            {!! nl2br($pi->terms_and_condition) !!}
                        </div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <div class="col-4">
        {{-- SUB TOTAL --}}
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td></td>
                <td style="width: 160px; text-align: left; font-weight: bold; border-bottom:1px solid #2c2b6d;">
                    Sub Total {{ $pi->currency_name->code }}
                </td>
                <td style="width: 80px; text-align: right; font-weight: bold; border-bottom:1px solid #2c2b6d;">
                    {{ App\SysHelper::com_curr_format($sub_total, 2, '.', ',') }}
                </td>
            </tr>
        </table>

        {{-- DISCOUNT (show only if any discount exists) --}}
        @if( ($discount ?? 0) + ($deal_discount ?? 0) > 0 )
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td></td>
                    <td style="width: 160px; text-align: left; font-weight: bold; border-bottom:1px solid #2c2b6d;">
                        Discount {{ $pi->currency_name->code }}
                    </td>
                    <td style="width: 80px; text-align: right; font-weight: bold; border-bottom:1px solid #2c2b6d;">
                        {{ App\SysHelper::com_curr_format( ($discount ?? 0) + ($deal_discount ?? 0), 2, '.', ',') }}
                    </td>
                </tr>
            </table>
        @endif

        {{-- TAXABLE AMOUNT --}}
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td></td>
                <td style="width: 160px; text-align: left; font-weight: bold; border-bottom:1px solid #2c2b6d;">
                    Taxable Amt. {{ $pi->currency_name->code }}
                </td>
                <td style="width: 80px; text-align: right; font-weight: bold; border-bottom:1px solid #2c2b6d;">
                    {{ App\SysHelper::com_curr_format( ($taxable_amt ?? 0) - ($deal_discount ?? 0), 2, '.', ',') }}
                </td>
            </tr>
        </table>

        {{-- VAT AMOUNT --}}
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td></td>
                <td style="width: 160px; text-align: left; font-weight: bold; border-bottom:1px solid #2c2b6d;">
                    VAT Amount {{ $pi->currency_name->code }}
                </td>
                <td style="width: 80px; text-align: right; font-weight: bold; border-bottom:1px solid #2c2b6d;">
                    {{ App\SysHelper::com_curr_format( ($vat_amount ?? 0) - ($deal_discount_vat_amount ?? 0), 2, '.', ',') }}
                </td>
            </tr>
        </table>

        {{-- TOTAL AMOUNT --}}
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td></td>
                <td style="width: 160px; text-align: left; font-weight: bold; border-bottom:1px solid #2c2b6d;">
                    Total Amount {{ $pi->currency_name->code }}
                </td>
                <td style="width: 80px; text-align: right; font-weight: bold; border-bottom:1px solid #2c2b6d;">
                    {{ App\SysHelper::com_curr_format( ($total_amount ?? 0) - ($deal_discount_amount ?? 0), 2, '.', ',') }}
                </td>
            </tr>
        </table>
    </div>
</div>



<div>
    <br>
        
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td style="border: none; width:200px;" align="left" valign="top"><b class="bottom_b">Received By:</b><br ><br ></td>
          <td rowspan="4" style="border: none; width:200px;" align="center" valign="bottom">{{@$pi->createdby->full_name}}<br /><b class="bottom_b" style="font-size: 10px;">Prepared By</b></td>
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
        {{-- <tr>
          <td style="width: 30%; border: none; font-size: 10px;" align="left" valign="bottom">{{@$po->createdby->full_name}}<br/><b class="bottom_b">Prepared By</b></td>
          <td style="width: 40%; border: none; font-size: 10px;" align="center" valign="bottom"><br/><br/>This document is computer generated Signature is not required</td>
          <td style="width: 30%; border: none; font-size: 10px;" align="right" valign="bottom">{{@$company->company_name}} <br /><br /><br /><br /><b class="bottom_b">Authorised Signature</b></td>
        </tr> --}}
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

    
{{-- attachment start--}}
        <div class="modal side-panel fade" id="attachment_popup_win" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header m-0 p-3">
                    <h4 class="modal-title">Attachments</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            <div class="modal-body m-0 p-3">
                <div class="container-fluid">
                    
                <div class="row">
                    <div class="col-md-12 mt-2">
                        <table id="att-table" class="table table-hover" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="width: 10%;">No</th>
                                    <th style="width: 30%;">Date</th>
                                    <th style="width: 50%;">Attachment</th>
                                    <th style="width: 10%;"></th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>

                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function add_attachment(){
        $("#loading_bg").css("display", "block");

        if($('#att_file').val()==""){ $('#att_file').focus(); $("#loading_bg").css("display", "none"); return false; }

        var action = "{{ URL::to('add-purchase-invoice-attachment') }}";
        
        var formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');  // Append CSRF token
        formData.append('doc_id', $('#pi_id').val());
        formData.append('att_date', $('#att_date').val()); // Append other form data
        formData.append('att_file', $('#att_file')[0].files[0]); 
        formData.append('doc_name', $('#doc_name').val());


        $.ajax({
            url: action,
            type: "POST",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(dataResult) {
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                var getSelectedRows="";
                    if(dataResult['data'] != null){
                        len = dataResult['data'].length;
                    }
                    if(len > 0){
                        for(var i=0; i<len; i++){
                            getSelectedRows +="<tr>\
                                <td>"+ Number(i+1) +"</td>\
                                <td>"+get_format_date(dataResult['data'][i].doc_date)+"</td>\
                                <td><a href='../../"+dataResult['data'][i].doc_file+"' target='_blank'>"+dataResult['data'][i].doc_name+"</a></td>\
                                <td><a onclick='delete_attachment("+dataResult['data'][i].id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
                                </tr>";
                        }
                        $('#att_file').val('');
                        $('#doc_name').val('');
                        $('#att-table tbody').empty();
                        $("#att-table tbody").append(getSelectedRows); 
                    }
                    else{
                        $('#att-table tbody').empty();
                    }
            }
        });
        $("#loading_bg").css("display", "none");
    }
    function view_attachment(){
        $("#loading_bg").css("display", "block");
        $('#att_cust_name').text($('#vendors :selected').text() + " " + $('#doc_number').val());

        var action = "{{ URL::to('view-purchase-invoice-attachment') }}";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                doc_id : $('#pi_id').val(),
            },
            cache: false,
            success: function(dataResult) {
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                var getSelectedRows="";
                    if(dataResult['data'] != null){
                        len = dataResult['data'].length;
                    }
                    if(len > 0){
                        for(var i=0; i<len; i++){
                            getSelectedRows +="<tr>\
                                <td>"+ Number(i+1) +"</td>\
                                <td>"+get_format_date(dataResult['data'][i].doc_date)+"</td>\
                                <td><a href='../../"+dataResult['data'][i].doc_file+"' target='_blank'>"+dataResult['data'][i].doc_name+"</a></td>\
                                <td><a onclick='delete_attachment("+dataResult['data'][i].id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
                                </tr>";
                        }
                        $('#att_file').val('');
                        $('#doc_name').val('');
                        $('#att-table tbody').empty();
                        $("#att-table tbody").append(getSelectedRows); 
                    }
                    else{
                        $('#att-table tbody').empty();
                    }
            }
        });
        $("#loading_bg").css("display", "none");
    }
    function delete_attachment(id){
        $("#loading_bg").css("display", "block");
        var action = "{{ URL::to('delete-purchase-invoice-attachment') }}";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                id : id,
                doc_id : $('#pi_id').val(),
            },
            cache: false,
            success: function(dataResult) {
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                var getSelectedRows="";
                    if(dataResult['data'] != null){
                        len = dataResult['data'].length;
                    }
                    if(len > 0){
                        for(var i=0; i<len; i++){
                            getSelectedRows +="<tr>\
                                <td>"+ Number(i+1) +"</td>\
                                <td>"+get_format_date(dataResult['data'][i].doc_date)+"</td>\
                                <td><a href='../../"+dataResult['data'][i].doc_file+"' target='_blank'>"+dataResult['data'][i].doc_name+"</a></td>\
                                <td><a onclick='delete_attachment("+dataResult['data'][i].id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
                                </tr>";
                        }
                        $('#att_file').val('');
                        $('#doc_name').val('');
                        $('#att-table tbody').empty();
                        $("#att-table tbody").append(getSelectedRows); 
                    }
                    else{
                        $('#att-table tbody').empty();
                    }
            }
        });
        $("#loading_bg").css("display", "none");
    }
    </script>

{{-- attachment end--}}

    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
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

        



    <div class="purchase-order-content-header d-flex align-items-center justify-content-between gap-2">
        <div class="d-flex align-items-center gap-2">
            <h4 class="purchase-order-content-header-left">
                {{ $sr->doc_number }}
            </h4>
            @if(isset($sr->deal_id))
                {!! App\SysHelper::deal_pipeline($sr->deal_id) !!}
            @endif
        </div>
        <div class="purchase-order-content-header-right">

           <a class="btn btn-light text-dark" href="{{url('sales-return/'.$sr->id.'?sr_action=edit')}}">
                <i class="ico icon-outline-pen-2 text-success"></i> Edit
            </a>
            <a class="btn btn-light  text-dark" href="{{url('sales-return/'.$sr->id.'?sr_action=add')}}">
                <i class="ico icon-outline-add-square text-success"></i> Add
            </a>
           
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ico icon-outline-hamburger-menu"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{url('sales-return/'.$sr->id.'/delete')}}"><i class="ico icon-outline-trash-bin-minimalistic text-danger"></i> Cancel SR</a></li>
                    <li><a class="dropdown-item" href="{{url('sales-return/'.$sr->id.'/download')}}"><i class="ico icon-outline-document-medicine text-success"></i> Download</a></li>
                    <li><button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#adj_popup_win"><i class="ico icon-outline-calculator-minimalistic text-danger"></i> Adjustment</button></li>
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
            <td align="left"><img style="margin-left: -12px;"  src="{{asset('public/'.@$company->company_logo)}}" width="200px"/></td>
            <td align="right"><b style="font-size: 30px; font-weight: 400;">
            
            @if (@$sr->credit_note == 'CN')
                Credit Note
            @elseif(@$sr->credit_note == 'SR')
                Sales Return
            @else
                 Sales Return
            @endif
            


            </b></td>
        </tr>
    </table>
    <br />

    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
         <td  width="60%"  valign="top" style="line-height: 18px;">

          
          To,<br />
            
          <b>{{@$sr->accountname->account_name}}</b><br>
   
           @if ($state !="") {{@$state}}, @endif {{ $country }}<br>
          T: {{@$tel}}, M: {{@$mobile}}<br>
          E: {{@$email}} <br>
          TRN: {{ @$trn_no }}
        </td>
        <td>

          <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                  <td style="padding: 0px; margin: 0px; width: 150px;">Sales Return No</td>
                  <td style="padding: 0px; margin: 0px">: {{@$sr->doc_number}}</td>
                </tr>
                <tr>
                  <td style="padding: 0px; margin: 0px">Sales Return Date</td>
                  <td style="padding: 0px; margin: 0px">: {{date('d/m/Y', strtotime(@$sr->doc_date))}}</td>
                </tr>
                <tr>
                  <td style="padding: 0px; margin: 0px">DO No</td>
                  <td style="padding: 0px; margin: 0px">: {{@$sr->dn_doc_number}}</td>
                </tr>
                <tr>
                  <td style="padding: 0px; margin: 0px">Invoice No</td>
                  <td style="padding: 0px; margin: 0px">: {{ @$sr->si_doc_number }}</td>
                </tr>
                <tr>
                  <td style="padding: 0px; margin: 0px; vertical-align: top; white-space: nowrap;">Payment Terms:</td>
                  <td style="padding:0; margin:0; white-space:nowrap; max-width:250px; overflow:hidden; text-overflow:ellipsis;">: {{ $sr->paymentterms->title }} {{ $sr->payment_terms2 }}</td>
                </tr>
          </table>
        </td>
      </tr>
        
  </table>
  <br />

  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr class="item-head-row">
        <td style="width: 20px;">No</td>
        <td>Part No</td>
        <td style="width: 30px; text-align: center;">Qty</td>
        <td style="width: 70px; text-align: right;">Rate</td>
        <td style="width: 80px; text-align: right;">Taxable</td>
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
      @if(count($sr_item)>0)
      @foreach ($sr_item as $item)
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
      <td class="item-row" style="width: 20px;">{{$i}} <?php $i++;?></td>
      <td class="item-row">
         <strong> {{ $item->product->part_number }}</strong><br />
        <span style="font-size: 10px;">  {!! nl2br($item->description) !!}</span><br />
        <span style="font-weight: bold;">  {{ $item->serial_no  }}</span></td>
          <td class="item-row" style="width: 30px; text-align: center;">{{ $item->qty }}</td>
          <td class="item-row" style="width: 70px; text-align: right;">{{ @App\SysHelper::com_curr_format($item->unitprice, 2, '.', ',') }}</td>
          <td class="item-row" style="width: 80px; text-align: right;">{{ @App\SysHelper::com_curr_format($item->unitprice*$item->qty, 2, '.', ',') }}</td>
          <td class="item-row" style="width: 30px; text-align: right;">{{ @App\SysHelper::com_curr_format($item->tax, 2, '.', ',') }}</td>
          <td class="item-row" style="width: 80px; text-align: right;">{{ @App\SysHelper::com_curr_format($item->vatamount, 2, '.', ',') }}</td>
          <td class="item-row" style="width: 80px; text-align: right;">{{ @App\SysHelper::com_curr_format($item->taxableamount+$item->vatamount, 2, '.', ',') }}</td>
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
      @endforeach
      @endif


        <table width="100%" border="0" cellspacing="0" cellpadding="0">

        <tr>
            <td valign="top">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr><td>
                {{ $sr->currency_name->code }}  <?php echo ucwords(@App\SysHelper::convertAmountToWords(@App\SysHelper::com_curr_format($total_amount, 2, '.', ''), $sr->currency_name->r_code, $sr->currency_name->p_code));?>
              </td>
              </tr>
            </table>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                  <td>
                  <b style="font-size: 12px;">Terms & Conditions</b>
                  <ol style="padding: 10px 0px 0px 15px; margin: 0px; font-size: 9px;">
                    <li>The ownership of goods will remain with us until full payment is received.</li>
                    <li>Open box items are non-returnable, and all sales of such items are final.</li>
                    <li>Items without serial numbers are not covered under the warranty.</li>
                    <li>Damage caused by power fluctuations is not covered under the warranty.</li>
                    <li>To make a warranty claim, please contact the relevant vendor's service center.</li>
                    <li>Bank details:- Bank Name: {{@$company->bank_name}}, Account Number: {{@$company->account_number}}</li>
                  </ol>
                </td>
                </tr>
            </table>
          </td>
          <td valign="top" width="250px"><table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td style="width: 110px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">Sub Total {{ $sr->currency_name->code }}</td>
              <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">{{ @App\SysHelper::com_curr_format($sub_total, 2, '.', ',') }}</td>
            </tr>
          </table>
          @if(($discount) > 0)
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td style="width: 110px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">Discount {{ $sr->currency_name->code }}</td>
              <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">{{ @App\SysHelper::com_curr_format($discount, 2, '.', ',') }}</td>
            </tr>
          </table>
          @endif
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td style="width: 110px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">Taxable Amt. {{ $sr->currency_name->code }}</td>
              <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">{{ @App\SysHelper::com_curr_format($taxable_amt, 2, '.', ',') }}</td>
            </tr>
          </table>
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td style="width: 110px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">VAT Amount {{ $sr->currency_name->code }}</td>
              <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">{{ @App\SysHelper::com_curr_format($vat_amount, 2, '.', ',') }}</td>
            </tr>
          </table>
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td style="width: 110px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">Total Amount {{ $sr->currency_name->code }}</td>
              <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">{{ @App\SysHelper::com_curr_format($total_amount, 2, '.', ',') }}</td>
            </tr>
          </table></td>
        </tr>
        </table>

  

      
      
     
                    
      <footer>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td style="width: 30%; border: none; font-size: 10px;" align="left" valign="bottom">{{@$po->createdby->full_name}}<br/><b class="bottom_b">Prepared By</b></td>
          <td style="width: 40%; border: none; font-size: 10px;" align="center" valign="bottom"><br/><br/>This document is computer generated Signature is not required</td>
          <td style="width: 30%; border: none; font-size: 10px;" align="right" valign="bottom">{{@$company->company_name}} <br /><br /><br /><br /><b class="bottom_b">Authorised Signature</b></td>
        </tr>
        <tr>
          <td colspan="3" style="border: none; font-size: 10px;" align="right" valign="top">
            {{-- Page No <span style="" class="pagenum"></span> of {{@$po->doc_number}}</td> --}}
        </tr>
    </table>
        <img  src="{!! asset('public/'.$company->pdf_footer.'') !!}"  width="100%"/></td>

  </footer>{{-- ************* --}}
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

    <div class="modal side-panel fade" id="adj_popup_win" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" style="height: 500px !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Bill Wise Adjustment</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-3">
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'sales-return-add-adjestment', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'sales-return-add-adjestment']) }}
                    <input type="hidden" value="{{ $sr->doc_number }}" name="adj_srn_no">
                    <input type="hidden" value="{{ $sr->dn_doc_number }}" name="adj_dn_doc_number">
                    <input type="hidden" value="{{ $sr->doc_date }}" name="edit_adj_doc_date">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="equipment comon-status row mt-40 d-block">
                                    <input type="text" id="act_srn_adj_amount" value="{{ $srn_adj_amount }}" hidden/>
                                    <input type="text" id="srn_adj_amount" value="{{ $srn_adj_amount }}" hidden />

                                    <table class="table table-hover form-item-table" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th style="width:100px;">@lang('Doc No')</th>
                                                <th style="width:100px;">@lang('Doc Date')</th>
                                                <th style="width:100px;">@lang('LPO NO')</th>
                                                <th style="width:100px;" class="text-end">@lang('Total')</th>
                                                <th style="width:100px;" class="text-end">@lang('Balance')</th>
                                                <th style="width:100px;" class="text-end">@lang('Paid')</th>
                                                <th style="width:200px;" class="text-center">@lang('Narration')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $i=0; @endphp
                                            @if (count($srn_adjestment)>0)
                                                @foreach ($srn_adjestment as $dt)
                                                    @php
                                                        $paid_amount = empty($dt->total_paid_amount) ? 0 : $dt->total_paid_amount;
                                                        $balance_amount = abs($dt->total_amount - $paid_amount);
                                                    @endphp
                                                    @if($balance_amount > 0)
                                                        <tr>
                                                            <td style="width:100px;"><input type="text" class="form-control" name="adj_siv_no[]" id="adj_siv_no_{{ $i }}" value="{{ $dt->doc_number }}" readonly /></td>
                                                            <td style="width:100px;"><input type="text" class="form-control" name="adj_doc_date[]" id="adj_doc_date_{{ $i }}" value="{{ date('d/m/Y', strtotime($dt->doc_date)) }}" readonly /></td>
                                                            <td style="width:100px;"><input type="text" class="form-control" name="lpo_number[]" id="lpo_number_{{ $i }}" value="{{ $dt->lpo_number }}" readonly /></td>
                                                            <td style="width:100px;"><input type="text" class="form-control text-end" name="adj_total[]" id="adj_total_{{ $i }}" value="{{ $dt->total_amount }}" readonly /></td>
                                                            <td style="width:100px;"><input type="text" class="form-control text-end" name="adj_balance[]" id="adj_balance_{{ $i }}" value="{{ $balance_amount }}" readonly /></td>
                                                            <td style="width:100px;"><input type="text" class="form-control text-end class_adj_paid" name="adj_paid[]" id="adj_paid_{{ $i }}" value="" onchange="get_set_amount({{ $i }})" onclick="set_adjestment({{ $i }})" /></td>
                                                            <td style="width:100px;"><input type="text" class="form-control" name="narration[]" id="narration_{{ $i }}" value="{{ $dt->narration }}" /></td>
                                                        </tr>
                                                        @php $i++; @endphp
                                                    @endif
                                                @endforeach
                                            @endif
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th><label id="footer_total" /></th>
                                                <th><label id="footer_balance" /></th>
                                                <th><label id="footer_paid" /></th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <script>
                            function get_set_amount(id) {
                                set_adjestment(id);
                                var adj_total = Number($('#adj_total_'+id).val());
                                var adj_paid = Number($('#adj_paid_'+id).val());
                                $('#adj_balance_'+id).val(adj_total - adj_paid);
                            }

                            function set_adjestment(id){
                                var sum = Number($('#act_srn_adj_amount').val());
                                var numItems = $('.class_adj_paid').length;
                                var adj=0;
                                for(var i=0; i < numItems; i++){
                                    if(i!=id){
                                        adj +=  Number($('#adj_paid_'+i).val());
                                    }
                                }
                                var adj2 = sum - adj;

                                if(adj2 > 0){
                                    $('#srn_adj_amount').val(adj2);
                                }
                                else { $('#srn_adj_amount').val(0); }

                                var adj3 = Number($('#srn_adj_amount').val());

                                if(adj3 > 0){
                                    var adj_total = Number($('#adj_balance_'+id).val());
                                    if(adj3 >= adj_total){
                                        $('#adj_paid_'+id).val(adj_total);
                                    }
                                    else{
                                        $('#adj_paid_'+id).val(adj3);
                                    }
                                }
                            }
                        </script>

                        <div class="modal-footer justify-content-center">
                            <button type="submit" class="btn btn-light add-btn">
                                <i class="ico icon-outline-bookmark-opened text-success"></i> Adjust
                            </button>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

    <script>
    function set_adjust(amt,id) {
        let maxAdjustable = parseFloat($("#srn_adj_amount").val());
        let currentAdjusted = 0;

        $("input[id^='set_amt_']").each(function () {
            let val = parseFloat($(this).val());
            if (!isNaN(val)) {
                currentAdjusted += val;
            }
        });

        let remaining = maxAdjustable - currentAdjusted;
        if (remaining <= 0) {
            alert("No more amount left to adjust.");
            return;
        }

        let adjustAmount = parseFloat(amt);
        if (adjustAmount > remaining) {
            adjustAmount = remaining;
        }

        $('#set_amt_' + id).val(adjustAmount);
        $("input[name='adj_siv_amount_adjusted']").val(currentAdjusted + adjustAmount);
    }
    </script>

    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
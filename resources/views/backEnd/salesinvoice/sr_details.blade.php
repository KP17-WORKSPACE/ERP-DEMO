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

        



    <div class="purchase-order-content-header">
        <h4 class="purchase-order-content-header-left">
            {{ $sr->doc_number }}
        </h4>
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
            <td align="left"><img  src="{{asset('public/'.@$company->company_logo)}}" width="200px"/></td>
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
        <td width="55%" valign="top" style="line-height: 18px;" colspan="2">
          <b>{{@$company->company_name}}</b>
          <div>{!! nl2br($company->company_address) !!}</div>
          P: {{@$company->telephone}}, M: {{@$company->mobile}}<br />
          E: {{@$company->email}}<br />
          TRN No: {{@$company->vat_number}}
          
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
        <tr>
        <td  width="60%"  valign="top" style="line-height: 18px;">

          
          Bill To,<br />
            
          <b>{{@$sr->accountname->account_name}}</b><br>
   
           @if ($state !="") {{@$state}}, @endif {{ $country }}<br>
          T: {{@$tel}}, M: {{@$mobile}}<br>
          E: {{@$email}}
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
          {!! nl2br($item->description) !!}<br />
          {{ $item->serial_no  }}</td>
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

    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
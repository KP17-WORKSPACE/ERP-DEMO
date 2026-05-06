<div class="modal-header">
    <h4 class="modal-title" id="pay-doc-number">{{ $payment->doc_number }}</h4>

    <div class="d-flex align-items-center gap-2">
         <a class="btn btn-light text-dark" href="{{url('payment/'.$payment->id.'/?pr_action=edit')}}" style="font-size:12px;padding:0px 7px !important;min-height:19px">
                <i class="ico icon-outline-pen-2 text-success" style="font-size:15px"></i> Edit
            </a>
            <a class="btn btn-light text-dark" href="{{url('payment/'.$payment->id.'/?pr_action=add')}}" style="font-size:12px;padding:0px 7px !important;min-height:19px">
                <i class="ico icon-outline-add-square text-success" style="font-size:15px"></i> Add
            </a>
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size:12px;padding:0px 7px !important;min-height:19px">
                    <i class="ico icon-outline-hamburger-menu" style="font-size:15px"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{url('payment/'.$payment->id.'/delete')}}"><i class="ico icon-outline-trash-bin-minimalistic text-danger"></i> Cancel</a></li>
                    <li><a class="dropdown-item" href="{{url('payment/'.$payment->id.'/download')}}"><i class="ico icon-outline-document-medicine text-success"></i> Download</a></li>
                    <li><a class="dropdown-item" href="{{url('stl')}}"><i class="ico icon-outline-add-square text-success"></i> STL</a></li>
                    <li><a class="dropdown-item" href="{{url('payment-cheque-list')}}"><i class="ico icon-outline-add-square text-success"></i> Cheque</a></li>
                </ul>
            </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
</div>

<div class="modal-body m-0 p-0">



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
            background-image: url('{!! asset('public/' . $company->pdf_watermark . '') !!}');
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





    <div class="card mb-3">
        <div class="card-body">
            <div class="" id="deal-info" role="tabpanel" aria-labelledby="deal-info-tab">

                <div class="row">

                     <div class="col-12 mb-2 pdfarea" >
                        
                        {{-- ************* --}}
                            
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
          <td align="left"><img  src="{{asset('public/'.@$company->company_logo)}}" width="200px"/></td>
          <td align="right"><b style="font-size: 30px; font-weight: 400;">Payment Voucher</b></td>
      </tr>
  </table>
  <br />

    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="55%" valign="top" style="line-height: 18px;">
            <b>{{@$company->company_name}}</b>
            <div>{!! nl2br($company->company_address) !!}</div>
            {{ @$company->stateRelation->name }}, {{ @$company->countryRelation->name }}<br />
            T: {{@$company->telephone}}, M: {{@$company->mobile}}<br />
            E: {{@$company->email}}<br />
            TRN No: {{@$company->vat_number}}
          </td>
          <td>
          </td>
        </tr>
    </table>
    <br />
      @if($payment->mode==1)
      <table width="100%" border="0" cellspacing="0" cellpadding="0" class="m1" style="text-align: center;">
      <tr>
        <td width="25%" style="line-height: 18px; font-weight:bold;">Doc Date</td>
        <td width="25%" style="line-height: 18px; font-weight:bold;">Doc Number</td>
        <td width="25%" style="line-height: 18px; font-weight:bold;">Payment Mode</td>
        <td width="25%" style="line-height: 18px; font-weight:bold;">Payment Date</td>
      </tr>
      <tr>
        <td style="line-height: 18px;">{{ date('d/m/Y', strtotime(@$payment->doc_date)) }}</td>
        <td style="line-height: 18px;">{{ $payment->doc_number }}</td>
        <td style="line-height: 18px;">Cash</td>
        <td style="line-height: 18px;">{{ date('d/m/Y', strtotime(@$payment->payment_date)) }}</td>
      </tr>
    </table>
      @else
      <table width="100%" border="0" cellspacing="0" cellpadding="0" class="m1" style="text-align: center;">
      <tr>
        <td width="25%" style="line-height: 18px; font-weight:bold;">Doc Date</td>
        <td width="25%" style="line-height: 18px; font-weight:bold;">Doc Number</td>
        <td width="25%" style="line-height: 18px; font-weight:bold;">Payment Mode</td>
        <td width="25%" style="line-height: 18px; font-weight:bold;">Payment Through</td>
      </tr>
      <tr>
        <td style="line-height: 18px;">{{ date('d/m/Y', strtotime(@$payment->doc_date)) }}</td>
        <td style="line-height: 18px;">{{ $payment->doc_number }}</td>
        <td style="line-height: 18px;">Bank</td>
        <td style="line-height: 18px;">
          @if($payment->payment_through == 1) Bank Transfer @endif
          @if($payment->payment_through == 2) CDC Cheque @endif
          @if($payment->payment_through == 3) PDC Cheque @endif
        </td>
      </tr>
      </table><br />
      <table width="100%" border="0" cellspacing="0" cellpadding="0" class="m1" style="text-align: center;">
      <tr>
        <td style="line-height: 18px; font-weight:bold;">Payment Date</td>
        <td style="line-height: 18px; font-weight:bold;">Cheque Date</td>
        <td style="line-height: 18px; font-weight:bold;">Cheque Number</td>
        <td style="line-height: 18px; font-weight:bold;">Bank Name</td>
      </tr>
      <tr>
        <td style="line-height: 18px;">{{ date('d/m/Y', strtotime(@$payment->payment_date)) }}</td>
        <td style="line-height: 18px;">{{ date('d/m/Y', strtotime(@$payment->cheque_date)) }}</td>
        <td style="line-height: 18px;">{{ $payment->cheque_number }}</td>
        <td style="line-height: 18px;">{{ $payment->account->account_name }}</td>
      </tr>
      </table>
      @endif
    <br />
    
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr style="background: #2c2b6d; color: #ffffff;">
          <td style="width: 20px;">S.No</td>
          <td style="width: 530px;">Particulars</td>
          <td style="width: 50px; text-align: center;">Amount</td>
        </tr>
    </table>
        <?php
            $i=1;
            $sum=0;
        ?>
        @if(count($payment_item)>0)
        @foreach ($payment_item as $item)
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
        <td style="width: 20px; border-bottom: solid 1px #2c2b6d;">{{$i}}. <?php $i++;?></td>
        <td style="width: 530px; border-bottom: solid 1px #2c2b6d; font-size: 10px;">{{ $item->accounts->account_name }}</td>
          <td style="width: 50px; border-bottom: solid 1px #2c2b6d; text-align: center;">{{ @App\SysHelper::com_curr_format(abs($item->debit_amount - $item->credit_amount),2,'.',',') }}</td>
            <?php            
            $sum += abs($item->debit_amount - $item->credit_amount);
            ?>
        </tr>
        </table>
        @endforeach
        @endif
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td style="border-bottom: solid 1px #2c2b6d; text-align: left; width: 550px; font-weight: bold;">
            <?php echo ucwords(@App\SysHelper::convertAmountToWords($sum,$payment->currency_name->r_code,$payment->currency_name->p_code));?></td>
          <td style="border-bottom: solid 1px #2c2b6d; text-align: center; font-weight: bold; width: 50px;">{{ @App\SysHelper::com_curr_format($sum,2,'.',',') }}</td>
        </tr>
      </table>
      <br ><br ><br ><br />
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td style="border: none; font-weight: bold;" align="left" valign="bottom">
        
            </td>
          <td style="border: none; font-weight: bold;" align="center" valign="bottom"></td>
          <td style="border: none; font-weight: bold;" align="right" valign="bottom">Authorised Signature<br /><br /><br />{{@$company->company_name}}
          <br /><br />Printed on {{ $print }}</td>
        </tr>
      </table>

      <footer>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
       
        <tr>
          <td colspan="3" style="border: none; font-size: 10px;" align="right" valign="top">
            {{-- Page No <span style="" class="pagenum"></span> of {{@$po->doc_number}}</td> --}}
        </tr>
    </table>
    <img  src="{!! asset('public/'.$company->pdf_footer.'') !!}"  width="100%"/></td>
  </footer>
                        {{-- ************* --}}
                    </div>

                </div>
            </div>
        </div>
    </div>


    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
</div>

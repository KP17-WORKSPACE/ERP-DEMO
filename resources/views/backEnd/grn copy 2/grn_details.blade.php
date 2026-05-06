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
        background-image: url('{!! asset("public/backEnd/img/" . $company->pdf_watermark . "") !!}');
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
            {{ $grn->doc_number }}
        </h4>
        <div class="purchase-order-content-header-right">
            <a class="btn btn-light" href="{{url('goods-receipt-note/create')}}">
                <i class="ico icon-outline-add-square text-success"></i> Add
            </a>
            <a class="btn btn-light" href="{{url('goods-receipt-note/'.$grn->id.'/edit')}}">
                <i class="ico icon-outline-pen-2 text-success"></i> Edit
            </a>
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ico icon-outline-hamburger-menu"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{url('goods-receipt-note/'.$grn->id.'/delete')}}"><i class="ico icon-outline-trash-bin-minimalistic text-danger"></i> Cancel GRN</a></li>
                    <li><a class="dropdown-item" href="{{url('goods-receipt-note/'.$grn->id.'/download')}}"><i class="ico icon-outline-document-medicine text-success"></i> Download</a></li>
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
                                <td align="left"><img  src="{{asset(@$company->company_logo)}}" width="200px"/></td>
                                <td align="right"><b style="font-size: 30px; font-weight: 400;">Goods Receipt Note</b></td>
                            </tr>
                        </table>
  <br />

    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="60%" valign="top" style="line-height: 18px;">
            <b>{{@$company->company_name}}</b>
            <div>{!! nl2br($company->company_address) !!}</div>
            Phone: {{@$company->telephone}}<br />
            Email: {{@$company->email}}<br />
            TRN No: {{@$company->vat_number}}
          </td>
          <td>
            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="line-height: 18px;" >
              <tr>
                  <td style="padding: 0px; margin: 0px; width: 150px;">Delivery Note No</td>
                  <td style="padding: 0px; margin: 0px;">: {{@$grn->doc_number}}</td>
                </tr>
                <tr>
                  <td style="padding: 0px; margin: 0px;">Delivery Note Date</td>
                  <td style="padding: 0px; margin: 0px;">: {{date('d/m/Y', strtotime(@$grn->grn_date))}}</td>
                </tr>
                <tr>
                  <td style="padding: 0px; margin: 0px;">LPO Number</td>
                  <td style="padding: 0px; margin: 0px;">: {{@$grn->lpo_number}}</td>
                </tr>
                <tr>
                  <td style="padding: 0px; margin: 0px;">LPO Date</td>
                  <td style="padding: 0px; margin: 0px;">: {{date('d/m/Y', strtotime(@$grn->lpo_date))}}</td>
                </tr>
                <tr>
                  <td style="padding: 0px; margin: 0px;padding: 0px; margin: 0px; vertical-align: top; white-space: nowrap;">Payment Terms</td>
                  <td style="padding: 0px; margin: 0px;">: {{ $grn->paymentterms->title }} {{ $grn->payment_terms2 }}</td>
                </tr>
          </table>
          </td>
        </tr>
    </table>
    <br />
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="60%" valign="top" style="line-height: 18px;">Bill From,<br />
              <b style="font-size: 90%;">{{@$grn->accountname->account_name}}</b><br>
              {{@$contact_name}}<br />
              {{@$address}}, @if(@$address2 != ""){{@$address2}}, @endif @if(@$city != ""){{@$city}}@endif<br>
              {{@$state}}, {{@$country}}<br>
              T: {{@$tel}}<br/>
              E: {{@$email}}
        </td>
        <td valign="top" style="line-height: 18px;">Ship From,<br />
          <b style="font-size: 90%;">{{@$grn->accountname->account_name}}</b><br>
          {{@$ship_contact_name}}<br />
          {{@$ship_address1}}, @if(@$ship_address2 != ""){{@$ship_address2}}, @endif @if(@$delivery_city != ""){{@$delivery_city}} @endif<br>
          {{@$delivery_state}}, {{@$delivery_country}}<br>
          T: {{@$ship_tel}}<br/>
          E: {{@$ship_email}}
        </td>
      </tr>
  </table>
  <br />
    
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr style="background: #2c2b6d; color: #ffffff;">
          <td style="width: 20px;">No</td>
          <td style="width: 530px;">Description</td>
          <td style="width: 50px; text-align: center;">Qty</td>
        </tr>
    </table>
        <?php
            $i=1;
            $qty=0;
        ?>
        @if(count($grn_item)>0)
        @foreach ($grn_item as $item)
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
        <td style="width: 20px; border-bottom: solid 1px #2c2b6d; vertical-align: top;">{{$i}} <?php $i++;?></td>
        <td style="width: 530px; border-bottom: solid 1px #2c2b6d; font-size: 10px; vertical-align: top;">
            <b style="font-size: 11px; vertical-align: top;">{{ $item->part_number }}</b><br />
            <?php
                $srl = $grn_item_srl->where('part_no',$item->part_no)->pluck('srl_no');
            ?>
            @if(count($srl)>0)
            @foreach ($srl as $sr)
            {{ $sr }}, 
            @endforeach
            @endif
            {!! nl2br($item->description) !!}
            <span style="width:auto; font-size: 12px; background: #ffffff; padding: 2px; margin-top:5px; font-weight: bold; font-style: italic; display: none;">{{ str_replace(',',', ',$item->serial_no)  }}</span></td>
          <td style="width: 50px; border-bottom: solid 1px #2c2b6d; text-align: center; vertical-align: top;">{{ $item->qty }}</td>
            <?php            
            $qty += $item->qty;
            ?>
        </tr>
        </table>
        @endforeach
        @endif
         <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
              <td style="border-bottom: solid 1px #2c2b6d; font-weight: bold;">
                Note: Goods Received in Good Condition
            </td>
            <td class="text-end" style="border-bottom: solid 1px #2c2b6d; font-weight: bold">
                Total
            </td>
          <td style="border-bottom: solid 1px #2c2b6d; text-align: center; font-weight: bold;">{{ $qty }}</td>
        </tr>
      </table>
      <br ><br ><br ><br />
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td style="border: none; font-weight: bold;" align="left" valign="bottom">
                Received By: <br /><br />
                Name: <br /><br />
                Phone: <br /><br /><br /><br />
                Signature & Stamp
        
            </td>
          <td style="border: none; font-weight: bold;" align="center" valign="bottom">Approved By</td>
          <td style="border: none; font-weight: bold;" align="right" valign="bottom">For {{@$company->company_name}}</td>
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
    <img  src="{!! asset('public/uploads/crm_pdf_img/new-'.$company->pdf_footer.'') !!}"  width="100%"/></td>
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

    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
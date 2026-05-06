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





<div class="purchase-order-content-header d-flex align-items-center justify-content-between mb-1 gap-2">
    <h4 class="purchase-order-content-header-left">
{{$po->doc_number}}
    </h4>
   <div class="purchase-order-content-header-right d-flex align-items-center justify-content-end gap-2">
                           

                         <form method="GET" action="{{ url('purchase-order',@$po->id) }}">
                              {{-- <input hidden type="text" value="{{@$po->id}}" name="id"> --}}
                            <button type="submit" name="po_action" value="edit" class="btn btn-light">
                               <i class="ico icon-outline-pen-2 text-success"></i> Edit
                            </button>
                            </form> 
                            
                            <form method="GET" action="{{ url('purchase-order') }}">
                            <button type="submit" name="po_action" value="add" class="btn btn-light">
                                 <i class="ico icon-outline-bookmark-opened text-success"></i>  Add
                            </button>
                            </form>

                           
                            
                            <div class="dropdown">
                                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="ico icon-outline-hamburger-menu"></i>
                                </button>
                                <ul class="dropdown-menu" style="">
                                    <li><a class="dropdown-item" href="{{url('purchase-order/'.@$po->id.'/print')}}">
                                            <i class="ico icon-outline-import text-success"></i>
                                            Download</a></li>
                                    {{-- <li><a class="dropdown-item" href="#">
                                            <i class="ico icon-outline-import text-success"></i>
                                            Import</a></li> --}}
                                    <li><a class="dropdown-item" href="{{url('purchase-order/'.@$po->id.'/delete')}}"><i class="ico icon-outline-trash-bin-minimalistic text-danger"></i>
                                            Delete</a></li>

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
    
  


    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
          <td align="left"><img  src="{!! asset($company->company_logo) !!}" width="150px"/></td>
          <td align="right"><b style="font-size: 30px; font-weight: 400;">Purchase Order</b></td>
      </tr>
  </table>
  <br />

    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="60%" valign="top" style="line-height: 17px;">
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
          <td style="line-height: 18px;" valign="top">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td style="padding: 0px; margin: 0px; width: 150px;">Purchase Order No</td>
                  <td style="padding: 0px; margin: 0px">: {{@$po->doc_number}}</td>
                </tr>
                <tr>
                  <td style="padding: 0px; margin: 0px">Purchase Order Date</td>
                  <td style="padding: 0px; margin: 0px">: {{date('d/m/Y', strtotime(@$po->po_date))}}</td>
                </tr>
                <tr>
                  <td style="padding: 0px; margin: 0px">Delivery Date</td>
                  <td style="padding: 0px; margin: 0px">
                    @if($po->delivery_date == "1970-01-01")
                    --
                    @else
                    : {{date('d/m/Y', strtotime(@$po->delivery_date))}}
                    @endif
                
                </td>
                </tr>
                <tr>
                  <td style="padding: 0px; margin: 0px; vertical-align: top; white-space: nowrap;">Payment Terms</td>
                  <td style="padding: 0px; margin: 0px">: {{ @$des=App\SysPaymentTerms::getPaymentTermsName($po->payment_terms)}}</td>
                </tr>
            </table>
          </td>
        </tr>
    </table>
    <br />
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="60%" valign="top" style="line-height: 18px;">Bill To,<br />
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
              <span style="font-size:11px;">{!! nl2br($item->description) !!}</span></td>
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
              <td style="width: 130px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">Sub Total {{ $po->currency_name->code }}</td>
              <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">{{ @App\SysHelper::com_curr_format($sub_total, 2, '.', ',') }}</td>
            </tr>
          {{-- </table> --}}
          @if($discount != 0)
          {{-- <table width="100%" border="0" cellspacing="0" cellpadding="0"> --}}
            <tr>
              <td></td>
              <td style="width: 130px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">Discount {{ $po->currency_name->code }}</td>
              <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">{{ @App\SysHelper::com_curr_format($discount, 2, '.', ',') }}</td>
            </tr>
          {{-- </table> --}}
          @endif
          {{-- <table width="100%" border="0" cellspacing="0" cellpadding="0"> --}}
            <tr>
              <td></td>
              <td style="width: 130px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">Taxable Amt. {{ $po->currency_name->code }}</td>
              <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">{{ @App\SysHelper::com_curr_format($taxable_amt, 2, '.', ',') }}</td>
            </tr>
          {{-- </table> --}}
          @if($customs_charges != 0)
          {{-- <table width="100%" border="0" cellspacing="0" cellpadding="0"> --}}
            <tr>
              <td></td>
              <td style="width: 130px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">Customs Charges</td>
              <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">{{ @App\SysHelper::com_curr_format($customs_charges, 2, '.', ',') }}</td>
            </tr>
          {{-- </table> --}}
          @endif
          @if ($vat_amount != 0)
          {{-- <table width="100%" border="0" cellspacing="0" cellpadding="0"> --}}
            <tr>
              <td></td>
              <td style="width: 130px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">VAT Amount {{ $po->currency_name->code }}</td>
              <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">{{ @App\SysHelper::com_curr_format($vat_amount, 2, '.', ',') }}</td>
            </tr>
          {{-- </table> --}}
          @endif
          {{-- <table width="100%" border="0" cellspacing="0" cellpadding="0"> --}}
            <tr>
              <td></td>
              <td style="width: 130px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">Total Amount {{ $po->currency_name->code }}</td>
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
    <img  src="{!! asset('public/uploads/crm_pdf_img/new-'.$company->pdf_footer.'') !!}"  width="100%"/></td>
  </footer>




 

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

                    
                </div>
                <div class="col-2 mb-2">&nbsp;</div>
            </div>
        </div>
    </div>
</div>


<?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
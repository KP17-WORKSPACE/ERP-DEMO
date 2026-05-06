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





<div class="purchase-order-content-header d-flex align-items-center justify-content-between mb-1 gap-2">
    <h4 class="purchase-order-content-header-left">
{{$edit->doc_number}}
    </h4>
   <div class="purchase-order-content-header-right d-flex align-items-center justify-content-end gap-2">
                           

                         <form method="GET" action="{{ url('stock-out',@$edit->id) }}">
                              {{-- <input hidden type="text" value="{{@$po->id}}" name="id"> --}}
                            <button type="submit" name="stockout_action" value="edit" class="btn btn-light">
                               <i class="ico icon-outline-pen-2 text-success"></i> Edit
                            </button>
                            </form> 
                            
                            <form method="GET" action="{{ url('stock-out') }}">
                            <button type="submit" name="stockout_action" value="add" class="btn btn-light">
                                 <i class="ico icon-outline-add-square text-success"></i>  Add
                            </button>
                            </form>

                          <div class="dropdown">
            <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="ico icon-outline-hamburger-menu"></i>
            </button>
            <ul class="dropdown-menu" style="">
               


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
          <td align="left"><img  src="{!! asset('public/'.$company->company_logo) !!}" width="150px"/></td>
          <td align="right"><b style="font-size: 30px; font-weight: 400;">Shortage Stock</b></td>
      </tr>
  </table>
  <br />

    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="60%" valign="top" style="line-height: 17px;">
            <b>To,</b><br>
            <b>{{ @$company->company_name }}</b><br>
              <div>{!! nl2br($company->company_address) !!}</div>
            Phone: {{@$company->telephone}}<br />
            Email: {{@$company->email}}<br />
            TRN No: {{@$company->vat_number}}
            {{-- Attn. {{ $m_contact_name }}<br>
          
            {{@$m_state}}, {{ $m_country }}<br>
            T: {{@$m_tel}}, M: {{@$m_mob}}<br>
           
            @if($m_trnno != "" )TRN No: {{@$m_trnno}}<br>@endif --}}
          </td>
          <td style="line-height: 18px;" valign="top">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td style="padding: 0px; margin: 0px; width: 150px;">Shortage Stock No</td>
                  <td style="padding: 0px; margin: 0px">: {{@$edit->doc_number}}</td>
                </tr>
                <tr>
                  <td style="padding: 0px; margin: 0px">Shortage Stock Date</td>
                  <td style="padding: 0px; margin: 0px">: {{date('d/m/Y', strtotime(@$edit->date))}}</td>
                </tr>
            
            </table>
          </td>
        </tr>
    </table>
    <br />
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr class="item-head-row">
          <td style="width: 20px;">No</td>
          <td >Description</td>
          <td style="width: 20px; text-align: center;">Qty</td>
          <td style="width: 70px; text-align: right;">Rate</td>
          <td style="width: 80px; text-align: right;">Amount</td>
        </tr>
    </table>
        <?php
            $i=1;
            $total_amount=0;
        ?>
        @if(count($edit_items)>0)
        @foreach ($edit_items as $dt)

        

        <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td class="item-row" style="width: 20px;">{{$i}} <?php $i++;?>  <span id="spn_"{{ $i }}></span></td>
            <td class="item-row" >
              <span style="font-weight:bold;">{{ $dt->partno }}</span><br />
              <span style="font-size:11px;">{!! nl2br($dt->serialno) !!}</span> <br>
              <span style="font-size:11px;">{!! nl2br($dt->description) !!}</span></td>
            <td class="item-row" style="width:20px; text-align: center;">{{$dt->qty}}</td>
            <td class="item-row" style="width:70px; text-align: right;">{{@App\SysHelper::com_curr_format($dt->unitprice,2,'.',',')}}</td>
            <td class="item-row" style="width:80px; text-align: right;">{{@App\SysHelper::com_curr_format($dt->value,2,'.',',')}}</td>

            <?php
            $total_amount += $dt->value;
            ?>
        </tr>
    </table>
    
        @endforeach
        @endif

        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td>{{ $edit->currency_name->code }}  <?php echo ucwords(@App\SysHelper::convertAmountToWords($total_amount,$edit->currency_name->code,$edit->currency_name->code));?></td>
            <td style="width: 130px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">Total Amount {{ $edit->currency_name->code }}</td>
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
    <img  src="{!! asset('public/'.$company->pdf_footer.'') !!}"  width="100%"/></td>
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
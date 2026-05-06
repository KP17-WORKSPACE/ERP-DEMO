<html>
<head>
  <?php
   $bi_amountt = 0;
   $paidt=0;
   $check_if_show=0;
   $data_adj='';
   $ii=0;
   ?>
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
    @page { margin: 20px 20px; }
    header { position: fixed; left: 20px; top: -50px; right: 20px; height: 80px; background-color: white; text-align: center; border-bottom:solid 0px #808080; color:#555555;  }
    footer { position: fixed; left: 0px; bottom: -100px; right: 0px; height: 100px; background-color: white; }
    footer .page:after { content: counter(page, upper-roman); }

    body{font-family: Verdana, sans-serif; font-size:12px; color:#555555; background-image: url('{!! asset("public/backEnd/img/".$company->pdf_watermark."") !!}');}

    th, td {padding: 5px 5px;}
    .tdd{border:dashed 1px #9e9e9e; border-width:0 0 1px 0;}
    b{font-size:14px;}
    main{margin:0px 0px;}
    .m1 table { border: 0px solid #9e9e9e; }
    .m1 td { border: 1px solid #9e9e9e; }
    .tmc ol {padding: 0px; margin: 0px;}
    .bottom_b {font-size:12px; }
    .page-break { page-break-after: always; }
    .m-0{margin: 0px;}
    .p-0{padding: 0px;}
    .item-head-row {background: #2c2b6d; color: #ffffff; }
    .item-row {border-bottom: solid 1px #2c2b6d;}
</style>

</head>
<body>
  <?php /*
    <header>
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td align="left"><img  src="{{asset(@$company->company_logo)}}" width="200px"/></td>
            <td align="right"><b style="font-size: 30px; font-weight: 400;">Sales Invoice</b></td>
        </tr>
    </table>
    </header>
    <footer>
      {{-- <img  src="{!! asset('admin_assets/dist/img/pdf-footer.jpg') !!}" width="100%"> --}}
    </footer>
     */ ?>
  <main class="m2">
    <?php try { ?>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
          <td align="left"><img  src="{{asset(@$company->company_logo)}}" width="200px"/></td>
          <td align="right"><b style="font-size: 30px; font-weight: 400;">Statement of Outstanding</b></td>
      </tr>
  </table>
  <br />
  <br />
  <br />

    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="50%" valign="top" style="line-height: 18px;"><br />
            <b style="font-size: 100%;">{{@$company->company_name}}</b>
            <div>{!! nl2br($company->company_address) !!}</div>
            Phone: {{@$company->telephone}}<br />
            Email: {{@$company->email}}<br />
            TRN No: {{@$company->vat_number}}
          </td>
          <td width="50%" valign="top" style="line-height: 18px;">
            <b>To,</b><br />
            <b style="font-size: 100%;">{{@$cust_detail->customer_name_display}}</b><br />
            {{ $cust_detail->customer_salutation }} {{ $cust_detail->first_name }} {{ $cust_detail->last_name }}<br />
            {{ $cust_address->address }}<br />
            {{ $cust_address->address2 }}, {{ $cust_address->city }}<br />
            {{ $cust_address->statename->name }}, {{ $cust_address->countryname->name }}<br />
            Phone: {{@$cust_detail->contcat_number}}<br />
            Email: {{@$cust_detail->email}}<br />
            TRN No: {{@$cust_detail->vat_number}}
          </td>
          
        </tr>
    </table>

    <br />
    <br />

    <?php 
    $bi_amountt = 0;
    $paidt=0;
    //$check_if_show=0;
    $data_adj='';
    $ii=0;

    foreach ($payable as $dt){
  
      $adjustmentst = $data_adjestment->where('piv_no',$dt->transaction_no)->max('paid_amount');
      $paymentt = $data_payment->where('bi_doc_no',$dt->transaction_no);
    if(count($paymentt)>0){
        foreach($paymentt as $p){
          $bi_amountt += $p->bi_amount;
        }
    }
    $paidt += $adjustmentst+$bi_amountt;
  
      if($dt->credit_amount != $paidt){
  
        if ($paidt != 0){
          $check_if_show=1;
        }
      }
    }

    ?>

    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td style="text-align: center;"><b>Statement of Outstanding Balance As of {{ date('d/m/Y', strtotime(@$date)) }}</b></td>
      </tr>
    </table>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr class="item-head-row">
        <td style="width:80; text-align: center;">Doc Date</td>
        <td style="width:80; text-align: center;">Doc No</td>
        <td style="width:80; text-align: center;">LPO No</td>
        <td style="width:80; text-align: center;">Amount</td>
        <!-- @if($check_if_show == 0) -->
        <?php if ($check_if_show == 1) { ?>
        <td style=" text-align: center;">Adjestments</td>
        <td style=" text-align: center;">Balance</td>
        <?php } ?>
        <!-- @endif -->

        <!-- <td style="width: 100px; text-align: center;">Adjestments</td>	
        <td style="width: 100px; text-align: center;">Total Balance</td> -->
   <td style="width:80; text-align: center;">Due Date</td>	
   <td style="width:80; text-align: center;">Over Due</td>	


        <!-- <td style="width: 100px; text-align: center;">Total Balance</td>
        <td style="text-align: center;">Due Date</td> -->
      </tr>
  </table>
  @php
  $adjustments = 0;
  $b=0;
  @endphp
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
  @foreach ($payable as $dt)
  <?php
  try{ ?>
    @php
    $adjustments = 0; $receipt_date=''; $doc_number=''; $cheque_number=''; $bank_name=''; $bi_amount=0; $paid=0;
                        @endphp
  @php
      $adjustments = $data_adjestment->where('piv_no',$dt->transaction_no)->max('paid_amount');
      $payment = $data_payment->where('bi_doc_no',$dt->transaction_no);
      if(count($payment)>0){
          foreach($payment as $p){
              $receipt_date .= date('d/m/Y', strtotime($p->payment_date)).',';
              $doc_number .= $p->doc_number.',';
              if ($p->cheque_number != ""){
                  $cheque_number .= $p->cheque_number.',';
              }                                
              if ($p->cheque_bank_name != ""){
                  $bank_name .= $p->cheque_bank_name.',';
              }
              $bi_amount += $p->bi_amount;
          }
      }
      $paid += $adjustments+$bi_amount;
        
    @endphp
  <?php } catch (\Exception $e) { } ?>
  <?php 
if(isset($data_adjestment[$ii]->paid_amount) )
	   	$data_adj=$data_adjestment[$ii]->paid_amount;
else
   $data_adj=' ';
?>

  @if($dt->credit_amount != $paid)
 
    <tr>
      <td class="item-row" style="width:80; text-align: center;">{{ date('d/m/Y', strtotime($dt->transaction_date)) }}</td>
      <td class="item-row" style="width:80; text-align: center;">{{ $dt->transaction_no }}</td>
      @php $lpono =  @App\SysHelper::get_purchase_invoice_details($dt->transaction_no); @endphp
      
      <td class="item-row" style="width:80; text-align: center;">{{ @$lpono->lpo_number }}</td>
      <!-- <td class="item-row" style="width: 100px; text-align: center;">{{ $dt->credit_amount }}</td> -->
      <?php /* if ($check_if_show == 1) { ?>
          <td class="item-row" style=" text-align: center;"><?php if ($paid == 0) echo '--';
                                                                          else echo  @App\SysHelper::com_curr_format($paid, 2, '.', '')  ?></td>
          <td class="item-row" style=" text-align: center;"><?php if ($paid == 0) echo '--';
                                                                          else echo  @App\SysHelper::com_curr_format($dt->credit_amount - abs($paid), 2, '.', '')  ?></td>
         <?php }*/ ?>
         <?php /*
      <!-- @if($check_if_show == 0)
      <td class="item-row" style="width: 100px; text-align: center;">@if($paid==0) -- @else {{ @App\SysHelper::com_curr_format($paid,2,'.','') }} @endif</td>
      <td class="item-row" style="width: 100px; text-align: center;">@if($paid==0) -- @else {{ @App\SysHelper::com_curr_format($dt->credit_amount-abs($paid),2,'.','') }} @endif</td>
      @endif -->
       */ ?>
      <!-- <td class="item-row" style="width: 100px; text-align: center;"><?php  echo $data_adj  ?></td> -->
      
      @php $b += $dt->credit_amount-abs($paid); @endphp
      @php $DueData =  @App\SysHelper::get_due_date_purchase_invoice($dt->transaction_no,$dt->transaction_date); @endphp
	
  <td class="item-row" style="width:80; text-align: center;">{{ @App\SysHelper::com_curr_format($dt->credit_amount-abs($paid),2,'.','') }}</td>
<td class="item-row" style="width:80; text-align: center;">{{ $DueData[0] }}</td>
                        <td class="item-row" style="width:80; text-align: center;">{{ $DueData[1] }}</td>	


      <!-- <td class="item-row" style="width: 100px; text-align: center;">{{ @App\SysHelper::com_curr_format($b,2,'.','') }}</td>
      <td class="item-row" style="text-align: center;"> -- </td> -->
    </tr>

@endif
<?php $ii++; ?>
@endforeach
<tr>
<td colspan="3" class="item-row" style="width: 100px; text-align: center;">Grand total amount</td>

<td class="item-row" style="width: 100px; text-align: center;">{{ @App\SysHelper::com_curr_format($b,2,'.','') }}</td>
<td colspan="2" class="item-row" style="width: 100px; text-align: center;">&nbsp;</td>   
</tr>
</table>
@if(count($payment)==0)
<script>
    $('.hidecol').css('display','none');
</script>
@endif

{{--  <br /><br />
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
      <td>
        <b>Terms & Condition:- </b>
        <ol style="padding: 10px 0px 0px 15px; margin: 0px;">
          <li>All payments must be made in accordance with the due dates specified on the invoices</li>
          <li>All cheques are subject to realization.</li>
          <li>Any discrepancies or errors in the statement of accounts must be reported within 7 days of receiving the statement.</li>
          <li>Ownership of goods remains with the company until full payment is received.</li>
      </ol>          
    </td>
    </tr>
</table>  --}}

<br /><br />
<div style="width: 100%; text-align: right;">Printed on :- {{ $generate_date }}</div>

    <?php /*
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
        @if(count($si_item)>0)
        @foreach ($si_item as $item)
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td class="item-row" style="width: 20px;">{{$i}} <?php $i++;?></td>
        <?php @$des=App\SmItem::getItemDes($item->part_number); ?>
            <td class="item-row" >{!! nl2br($des) !!}</td>
            <td class="item-row" style="width: 20px; text-align: center;">{{ $item->qty }}</td>
            <td class="item-row" style="width: 70px; text-align: right;">{{ @App\SysHelper::com_curr_format($item->unitprice,2,'.','') }}</td>
            <td class="item-row" style="width: 70px; text-align: right;">{{ @App\SysHelper::com_curr_format($item->unitprice*$item->qty,2,'.','') }}</td>
            <td class="item-row" style="width: 30px; text-align: right;">{{ @App\SysHelper::com_curr_format($item->tax,2,'.','') }}</td>
            <td class="item-row" style="width: 80px; text-align: right;">{{ @App\SysHelper::com_curr_format($item->vatamount,2,'.','') }}</td>
            <td class="item-row" style="width: 80px; text-align: right;">{{ @App\SysHelper::com_curr_format($item->taxableamount+$item->vatamount,2,'.','') }}</td>
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
        
        $deal_discount += $si->deal_discount;
        $deal_discount_vat=$si_item->max('tax');
        $deal_discount_vat_amount= $deal_discount * $deal_discount_vat/100;
        $deal_discount_amount= $deal_discount + $deal_discount_vat_amount;
        ?>
        @endif

        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td>
              {{ $si->currency_name->code }}  <?php echo ucwords(getIndianCurrency($total_amount,$si->currency_name->r_code,$si->currency_name->p_code));?>
            </td>
            <td style="width: 110px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">Sub Total {{ $si->currency_name->code }}</td>
            <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">{{ @App\SysHelper::com_curr_format($sub_total, 2, '.', ',') }}</td>
          </tr>
        </table>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td></td>
            <td style="width: 110px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">Discount {{ $si->currency_name->code }}</td>
            <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">{{ @App\SysHelper::com_curr_format($discount+$deal_discount, 2, '.', ',') }}</td>
          </tr>
        </table>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td></td>
            <td style="width: 110px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">Taxable Amt. {{ $si->currency_name->code }}</td>
            <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">{{ @App\SysHelper::com_curr_format($taxable_amt-$deal_discount, 2, '.', ',') }}</td>
          </tr>
        </table>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td></td>
            <td style="width: 110px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">VAT Amount {{ $si->currency_name->code }}</td>
            <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">{{ @App\SysHelper::com_curr_format($vat_amount-$deal_discount_vat_amount, 2, '.', ',') }}</td>
          </tr>
        </table>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td></td>
            <td style="width: 110px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">Total Amount {{ $si->currency_name->code }}</td>
            <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">{{ @App\SysHelper::com_curr_format($total_amount-$deal_discount_amount, 2, '.', ',') }}</td>
          </tr>
        </table>

*/ ?>
  
  </main>
  <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
</body>

</html>

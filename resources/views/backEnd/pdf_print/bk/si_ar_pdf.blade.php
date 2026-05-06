<!DOCTYPE html>
<html  lang="ar" id="content">
  
<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
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
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <style>
      .amiri-regular {
        font-family: "Amiri", serif;
        font-weight: 400;
        font-style: normal; font-size: 13px;
        text-align: right; direction: rtl; line-height: 15px;
      }
      
      .amiri-bold {
        font-family: "Amiri", serif;
        font-weight: 700;
        font-style: bold; font-size: 15px;
        text-align: right; direction: rtl; line-height: 14px;
      }
      
      .amiri-regular-italic {
        font-family: "Amiri", serif;
        font-weight: 400;
        font-style: italic;
      }
      
      .amiri-bold-italic {
        font-family: "Amiri", serif;
        font-weight: 700;
        font-style: italic;
      }
    </style>


  <style>

    * {
      -webkit-print-color-adjust: exact !important;   /* Chrome, Safari 6 – 15.3, Edge */
      color-adjust: exact !important;                 /* Firefox 48 – 96 */
      print-color-adjust: exact !important;           /* Firefox 97+, Safari 15.4+ */
  }
    
    @page { margin: 20px 20px; }
    header { position: fixed; left: 20px; top: -50px; right: 20px; height: 80px; background-color: white; text-align: center; border-bottom:solid 0px #808080; color:#555555;  }
    footer { position: fixed; left: 0px; bottom: -100px; right: 0px; height: 100px; background-color: white; }
    footer .page:after { content: counter(page, upper-roman); }

    body{font-family: Verdana, sans-serif; width: 815px; margin: 0 auto; font-size:12px; color:#555555; background-repeat: no-repeat; background-position: center center; background-image: url('{!! asset("public/backEnd/img/".$company->pdf_watermark."") !!}');}

    th, td {padding: 5px 5px;}
    .tdd{border:dashed 1px #9e9e9e; border-width:0 0 1px 0;}
    b{font-size:12px;}
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
    .border{ border: solid 1px #9a9a9a;}
    .border-l{ border: solid 1px #9a9a9a; border-width: 1px 1px 1px 0px; }
    .border-r{ border: solid 1px #9a9a9a; border-width: 1px 0px 1px 1px;}
    .border-b{ border: solid 1px #9a9a9a; border-width: 0px 0px 1px 0px;}
</style>

 {{--  onload="abc()" onload="window.print()"  --}}
 {{--  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>  --}}

</head>
<body onload="window.print()">
  <a id="download"></a>
  <script>
    function abc(){
      document.getElementById('download').click();
    }
    const options = {
      margin: 0,
      filename: '{{ $si->doc_number }}.pdf', // Specify the file name here
      image: { type: 'jpeg', quality: 1 },
      html2canvas: { scale: 2 },
      jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
  };
  
  document.getElementById('download').addEventListener('click', () => {
      const element = document.getElementById('content');
      html2pdf()
          .from(element)
          .set(options)
          .save();
  });
  </script>
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
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
          <td align="left" width="350px;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td class="m-0 p-0"><b>{{@$company->company_name}}</b></td>
            </tr>
            <tr>
              <td class="m-0 p-0">{!! nl2br($company->company_address) !!}</td>
            </tr>
            <tr>
              <td style="width:50px;" class="m-0 p-0">Phone: {{@$company->telephone}}</td>
            </tr>
            <tr>
              <td class="m-0 p-0">Email: {{@$company->email}}</td>
            </tr>
            <tr>
              <td class="m-0 p-0">TRN No: {{@$company->vat_number}}</td>
            </tr>
          </table></td>
          <td align="center">
            <img  src="{{asset(@$company->company_logo)}}" width="150px"/><br />
            <b style="font-size: 18px; font-weight: 400;">TAX INVOICE</b>
          </td>
          <td align="right" width="350px;" style="padding-right: 10px;">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td>
                @if($company->id==8)
                <p class="m-0 p-0 amiri-bold">سوبريم سيستم تردينج إسطبلشمنة</p>
                <p class="m-0 p-0 amiri-regular">٦٥٤۹,  وادي الشعراء - </p>
                <p class="m-0 p-0 amiri-regular">حي العليا وحدة رقم ٥٥٦۹,</p>
                <p class="m-0 p-0 amiri-regular">الرياض ۱۲۲۱۱ - ۳۸۰٥</p>
                <p class="m-0 p-0 amiri-regular">الهاتف: +۹٦٦-۱۱-۲۱۰-۹٦٦۸</p>
                <p class="m-0 p-0 amiri-regular">الهاتف: +۹٦٦-٥٥-٤۹۰-۹۳۲۷</p>
                <p class="m-0 p-0 amiri-regular">ترن رقم: ۳۰۲۰۷۱۰۲۷٤۰۰۰۰۳</p>
                @endif
                
                @if ($company->id==10)
                <p class="m-0 p-0 amiri-bold">سپریم سسٹم ڈسٹریبیوٹرز ایس پی سی</p>
                <p class="m-0 p-0 amiri-regular">فلور ٦، الشمور بلڈنگ،</p>
                <p class="m-0 p-0 amiri-regular">چیمبر آف کامرس کے مقابل، سی بی ڈی،</p>
                <p class="m-0 p-0 amiri-regular">مسقط، سلطنت عمان</p>
                <p class="m-0 p-0 amiri-regular">الهاتف: +۹٦۸ ۲٤۷۸۱۸۳٦</p>
                <p class="m-0 p-0 amiri-regular">الهاتف: +۹٦٦-٥٥-٤۹۰-۹۳۲۷</p>
                <p class="m-0 p-0 amiri-regular">ترن رقم: OM۱۱۰۰۲۳۸۱۹۱</p>
                @endif
              </td>
            </tr>            
          </table>

        </td>
      </tr>
  </table>
  <br />

    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="60%" valign="top" style="line-height: 18px;" class="border-r">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="m-0 p-0"><b style="font-size: 90%;">{{@$si->accountname->account_name}}</b>
                  @if(count($ar_data)>0)<b style="font-size: 100%; float: right;" class="amiri-regular">{{@$ar_data[0]->company_name_ar}}</b>@endif
                </td>
              </tr>
              <tr>
                <td class="m-0 p-0">{{@$contact_name}}
                  @if(count($ar_data)>0)<span style="float: right;" class="amiri-regular">{{@$ar_data[0]->contact_person_ar}}</span>@endif<br />
                  {{@$address}}@if(count($ar_data)>0)<span class="amiri-regular" style="float: right;">{!! nl2br($ar_data[0]->address_ar) !!}</span>@endif<br>
                  {{@$address2}}, {{@$city}}<br>
                  {{@$state}}, {{@$country}}<br>
                  Tel: {{@$tel}}<span class="amiri-regular" style="float: right;">الهاتف: <?php echo set_en_to_ar($tel); ?></span><br>
                  Email: {{@$email}}<span class="amiri-regular" style="float: right;">البريد الإلكتروني: {{@$email}}</span><br>
                  @if($cust_trn_no!="")
                    TRN No: {{@$cust_trn_no}}<span class="amiri-regular" style="float: right;">ترن رقم: <?php echo set_en_to_ar($cust_trn_no); ?></span>
                  @endif
                </td>
              </tr>
            </table>
          </td>
          <td valign="top" style="line-height: 18px; padding: 0px;" class="border">
            <table width="100%" border="0" cellspacing="0" cellpadding="5">
                <tr>
                    <td class="m-0 p-0 border-b" style="font-size: 11px; padding: 2px 0px;">Invoice No: {{@$si->doc_number}} <span class="amiri-regular" style="float: right;">رقم الفاتورة: <?php echo set_en_to_ar($si->doc_number); ?></span></td>
                </tr>
                <tr>
                    <td class="m-0 p-0 border-b" style="font-size: 11px; padding: 2px 0px;">Date: {{date('d/m/Y', strtotime(@$si->doc_date))}} <span class="amiri-regular" style="float: right;">التاريخ: <?php echo set_en_to_ar(date('d/m/Y', strtotime(@$si->doc_date))); ?></span></td>
                </tr>
                <tr>
                    <td class="m-0 p-0 border-b" style="font-size: 11px; padding: 2px 0px;">Ref No: {{@$si->lpo_number}} <span class="amiri-regular" style="float: right;">رقم المرجع: {{@$si->lpo_number}}</span></td>
                </tr>
                <tr>
                  <td class="m-0 p-0 border-b" style="font-size: 11px; padding: 2px 0px;">Ref Date: {{date('d/m/Y', strtotime(@$si->lpo_date))}} <span class="amiri-regular" style="float: right;">التاريخ المرجعي: <?php echo set_en_to_ar(date('d/m/Y', strtotime(@$si->lpo_date))); ?></span></td>
                </tr>
                <tr>
                  <td class="m-0 p-0 border-b" style="font-size: 11px; padding: 2px 0px;">Payment Terms: {{ $si->paymentterms->title }} {{ $si->payment_terms2 }} <span class="amiri-regular" style="float: right;">شروط الدفع: <?php echo set_en_to_ar($si->paymentterms->title); ?> <?php echo set_en_to_ar($si->payment_terms2); ?></span></td>
                </tr>
            </table>
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
        <tr class="item-head-row">
          <td style="width: 20px;">الأب لا</td>
          <td>رقم الجزء</td>
          <td style="width: 20px; text-align: center;">الكمية</td>
          <td style="width: 70px; text-align: right;">السعر</td>
          <td style="width: 70px; text-align: right;">قيمة</td>
          <td style="width: 30px; text-align: right;">ضريبة القيمة المضافة %</td>
          <td style="width: 80px; text-align: right;">مبلغ ضريبة القيمة المضافة</td>
          <td style="width: 80px; text-align: right;">المبلغ الإجمالي</td>
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
            <td class="item-row" >
              <span style="font-weight:bold;">{{ $item->productname->part_number }}</span><br />
              {!! nl2br($item->description) !!}</td>
            <td class="item-row" style="width: 20px; text-align: center;">{{ $item->qty }} <br /><?php echo set_en_to_ar($item->qty); ?></td>
            <td class="item-row" style="width: 70px; text-align: right;">{{ @App\SysHelper::com_curr_format($item->unitprice,2,'.',',') }}<br /><?php echo set_en_to_ar(@App\SysHelper::com_curr_format($item->unitprice,2,'.',',')); ?></td>
            <td class="item-row" style="width: 70px; text-align: right;">{{ @App\SysHelper::com_curr_format($item->unitprice*$item->qty,2,'.',',') }}<br /><?php echo set_en_to_ar(@App\SysHelper::com_curr_format($item->unitprice*$item->qty,2,'.',',')); ?></td>
            <td class="item-row" style="width: 30px; text-align: right;">{{ @App\SysHelper::com_curr_format($item->tax,2,'.',',') }}<br /><?php echo set_en_to_ar(@App\SysHelper::com_curr_format($item->tax,2,'.',',')); ?></td>
            <td class="item-row" style="width: 80px; text-align: right;">{{ @App\SysHelper::com_curr_format($item->vatamount,2,'.',',') }}<br /><?php echo set_en_to_ar(@App\SysHelper::com_curr_format($item->vatamount,2,'.',',')); ?></td>
            <td class="item-row" style="width: 80px; text-align: right;">{{ @App\SysHelper::com_curr_format($item->taxableamount+$item->vatamount,2,'.',',') }}<br /><?php echo set_en_to_ar(@App\SysHelper::com_curr_format($item->taxableamount+$item->vatamount,2,'.',',')); ?></td>
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
            <td>Amount chargeable in words: {{ $si->currency_name->code }}  <?php echo ucwords(@App\SysHelper::convertAmountToWords(@App\SysHelper::com_curr_format($total_amount-$deal_discount_amount, 2, '.', ''),$si->currency_name->r_code,$si->currency_name->p_code));?>
              <br />
              المبلغ المحمل (بالكلمات): {{ $si->currency_name->code }}  <?php echo ucwords(@App\SysHelper::convertAmountToWordsArabic(@App\SysHelper::com_curr_format($total_amount-$deal_discount_amount, 2, '.', ''),$si->currency_name->r_code,$si->currency_name->p_code));?>
            </td>
            <td style="width: 150px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">Sub Total {{ $si->currency_name->code }}
              <br />المجموع الفرعي
            </td>
            <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">{{ @App\SysHelper::com_curr_format($sub_total, 2, '.', ',') }}<br /><?php echo set_en_to_ar(@App\SysHelper::com_curr_format($sub_total, 2, '.', ',')); ?></td>
          </tr>
        </table>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td>
              VAT amount in words:{{ $si->currency_name->code }}  <?php echo ucwords(@App\SysHelper::convertAmountToWords(@App\SysHelper::com_curr_format($vat_amount-$deal_discount_vat_amount, 2, '.', ''),$si->currency_name->r_code,$si->currency_name->p_code));?>
              <br >
              مبلغ ضريبة القيمة المضافة (بالكلمات): {{ $si->currency_name->code }}  <?php echo ucwords(@App\SysHelper::convertAmountToWordsArabic(@App\SysHelper::com_curr_format($vat_amount-$deal_discount_vat_amount, 2, '.', ''),$si->currency_name->r_code,$si->currency_name->p_code));?>
            </td>
            <td style="width: 150px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">Discount {{ $si->currency_name->code }}
              <br/>الخصم
            </td>
            <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">{{ @App\SysHelper::com_curr_format($discount+$deal_discount, 2, '.', ',') }}<br /><?php echo set_en_to_ar(@App\SysHelper::com_curr_format($discount+$deal_discount, 2, '.', ',')); ?></td>
          </tr>
        </table>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td></td>
            <td style="width: 150px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">Taxable Amt. {{ $si->currency_name->code }}
              <br />أمت الخاضع للضريبة
            </td>
            <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">{{ @App\SysHelper::com_curr_format($taxable_amt-$deal_discount, 2, '.', ',') }}<br /><?php echo set_en_to_ar(@App\SysHelper::com_curr_format($taxable_amt-$deal_discount, 2, '.', ',')); ?></td>
          </tr>
        </table>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td></td>
            <td style="width: 150px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">VAT Amount {{ $si->currency_name->code }}
              <br />مبلغ ضريبة القيمة المضافة
            </td>
            <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">{{ @App\SysHelper::com_curr_format($vat_amount-$deal_discount_vat_amount, 2, '.', ',') }}<br /><?php echo set_en_to_ar(@App\SysHelper::com_curr_format($vat_amount-$deal_discount_vat_amount, 2, '.', ',')); ?></td>
          </tr>
        </table>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td></td>
            <td style="width: 150px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">Total Amount {{ $si->currency_name->code }}
              <br />المبلغ الإجمالي
            </td>
            <td style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">{{ @App\SysHelper::com_curr_format($total_amount-$deal_discount_amount, 2, '.', ',') }}<br /><?php echo set_en_to_ar(@App\SysHelper::com_curr_format($total_amount-$deal_discount_amount, 2, '.', ',')); ?></td>
          </tr>
        </table>


<div>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td>
            <b style="font-size: 12px;">Terms & Conditions</b>
                    <ol style="padding: 10px 0px 0px 15px; margin: 0px; font-size: 10px;">
      
      <li>The ownership of goods will remain with us until full payment is received.</li>
      <li>Open box items are non-returnable, and all sales of such items are final.</li>
      <li>Items without serial numbers are not covered under the warranty.</li>
      <li>Damage caused by power fluctuations is not covered under the warranty.</li>
      <li>To make a warranty claim, please contact the relevant vendor&#39;s service center.</li>
      <li>Bank details:- Bank Name: {{@$company->bank_name}}, Account Number: {{@$company->account_number}}</li>
                  </ol>
          </td>
            <td>
              <b>الشروط والأحكام</b>
              <ul style="padding: 10px 0px 0px 10px; margin: 0px; list-style: none; font-size: 10px; direction: rtl; text-align: right;">
              @if($company->id==8)
              <li> ستبقى ملكية البضائع معنا حتى يتم استلام الدفعة الكاملة.	۱</li>
              <li> عناصر الصندوق المفتوح غير قابلة للإرجاع ، وجميع مبيعات هذه العناصر نهائية.	۲</li>
              <li> لا يغطي الضمان العناصر التي لا تحتوي على أرقام تسلسلية.	۳</li>
              <li> لا يغطي الضمان الأضرار الناجمة عن تقلبات الطاقة.	٤</li>
              <li> لتقديم مطالبة بالضمان ، يرجى الاتصال بمركز خدمة البائع ذي الصلة.	٥</li>
              @endif
              @if($company->id==10)
              <li> ستبقى ملكية البضائع معنا حتى يتم استلام الدفعة الكاملة.	۱</li>
              <li> عناصر الصندوق المفتوح غير قابلة للإرجاع ، وجميع مبيعات هذه العناصر نهائية.	۲</li>
              <li> لا يغطي الضمان العناصر التي لا تحتوي على أرقام تسلسلية.	۳</li>
              <li> لا يغطي الضمان الأضرار الناجمة عن تقلبات الطاقة.	٤</li>
              <li> لتقديم مطالبة بالضمان ، يرجى الاتصال بمركز خدمة البائع ذي الصلة.	٥</li>
              @endif
            </ul>
          </td>
          </tr>
      </table>
      <br ><br ><br >
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td style="border: none;" align="left" valign="top"><b class="bottom_b">Received By: <br />تلقى بواسطة</b><br ><br ></td>
          <td rowspan="3" valign="top" align="left" style="width:345px;">
            <b>Bank Details</b><br />
            A/C Holder's Name: {{@$company->company_name}}<br />
            Bank Name: {{@$company->bank_name}}<br />
            Account Number: {{@$company->account_number}}<br />
            IBAN: {{@$company->iban_no}}<br />
            Finance Code: {{@$company->finance_code}}<br />
            Branch & SWIFT Code: {{@$company->branch_swift_code}}<br /></td>
        </tr>
        <tr>
          <td style="border: none;" align="left" valign="top"><b class="bottom_b">Name:<br />الاسم</b><br ><br ></td>
        </tr>
        <tr>
          <td style="border: none;" align="left" valign="top"><b class="bottom_b">Phone:<br />الهاتف</b><br ><br ></td>
        </tr>
      </table>
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="35%" style="border: none;" align="left" valign="top"><b class="bottom_b">Signature and stamp:<br />التوقيع والختم</b></td>
          <td width="30%" style="border: none;" align="center" valign="top">{{@$si->createdby->full_name}}<br /><b class="bottom_b" style="font-size: 10px;">Prepared By <br />من إعداد</b></td>
          <td width="35%" style="border: none;" align="right" valign="top"><b class="bottom_b" style="font-size: 10px;">For {!! str_replace('SYSCOM DISTRIBUTIONS LLC BRANCH ABU DHABI 1','SYSCOM DISTRIBUTIONS LLC<br />BRANCH ABU DHABI 1',$company->company_name) !!}</b></td>
        </tr>
      </table>
</div>
  
  </main>
</body>

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

function getIndianCurrencyAr(float $number, int $company_id)
{
  if($company_id==8){
    $r1 = "ريال"; $r2 = "هالاس";
  }
  if($company_id==10){
    $r1 = "ريال"; $r2 = "بيزة";
  }
  


    $decimal = round($number - ($no = floor($number)), 2) * 100;
    $hundred = null;
    $digits_length = strlen($no);
    $i = 0;
    $str = array();
    $words = array(0 => '', 1 => 'واحد', 2 => 'اثنان',
        3 => 'ثلاثة', 4 => 'أربعة', 5 => 'خمسة', 6 => 'ستة',
        7 => 'سبعة', 8 => 'ثمانية', 9 => 'تاسعًا',
        10 => 'عشرة', 11 => 'أحد عشر', 12 => 'اثنا عشر',
        13 => 'ثلاثة عشر', 14 => 'أربعة عشر', 15 => 'خمسة عشر',
        16 => 'ستة عشر', 17 => 'سبعة عشر', 18 => 'ثمانية عشر',
        19 => 'تسعة عشر', 20 => 'عشرون', 30 => 'الثلاثون',
        40 => 'أربعون', 50 => 'خمسون', 60 => 'ستون',
        70 => 'سبعون', 80 => 'ثمانون', 90 => 'تسعون');
    $digits = array('', 'مائة','ألف','', '');
    while( $i < $digits_length ) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += $divider == 10 ? 1 : 2;
        if ($number) {
            $plural = (($counter = count($str)) && $number > 9) ? '' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' و ' : null;
            $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
        } else $str[] = null;
    }
    $Rupees = implode('', array_reverse($str));
    $paise = ($decimal > 0) ? "." . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . " " .$r2 : '';
    return ($Rupees ? $Rupees . $r1 : ' ') . $paise;
}

function set_en_to_ar(string $str){  
  $arr = str_split($str);
  $f='';
  foreach($arr as $r){
    $f .= get_ar_num($r);
  }
  return $f;
  
}

function get_ar_num(string $num){
  if($num=="0"){return "۰";}
  if($num=="1"){return "۱";}
  if($num=="2"){return "۲";}
  if($num=="3"){return "۳";}
  if($num=="4"){return "٤";}
  if($num=="5"){return "٥";}
  if($num=="6"){return "٦";}
  if($num=="7"){return "۷";}
  if($num=="8"){return "۸";}
  if($num=="9"){return "۹";}
  if($num=="."){return ".";}
  if($num==","){return ",";}
  if($num=="+"){return "+";}
  if($num=="-"){return "-";}
  return $num;
}
?>
</html>
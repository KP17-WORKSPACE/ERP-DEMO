<html>
<head>
 


  <style>
    @page { margin: 0px; }
    header { position: fixed; left: 20px; top: -50px; right: 20px; height: 80px; background-color: white; text-align: center; border-bottom:solid 0px #808080; color:#555555;  }
    footer { position: fixed; left: 0px; bottom: -100px; right: 0px; height: 100px; background-color: white; }
    footer .page:after { content: counter(page, upper-roman); }

    body{font-family: Verdana, sans-serif; font-size:{{ @$font_size }}; font-weight: bold; color:#555555; margin: 0;}

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

  .div_company {
      position: absolute; text-align: left;
      z-index: 9;
      top: {{ @$company_top }};
      left: {{ @$company_left }};
  }

  .div_date {
      position: absolute; text-align: left;
      z-index: 9;
      top: {{ @$date_top }};
      left: {{ @$date_left }};
  }

  .div_amount_w {
      position: absolute; text-align: left;
      z-index: 9; width: 378px; line-height: 28px;
      top: {{ @$amount_w_top }};
      left: {{ @$amount_w_left }};
  }

  .div_amount {
      position: absolute; text-align: left;
      z-index: 9;
      top: {{ @$amount_top }};
      left: {{ @$amount_left }};
  }
</style>

</head>
<body>
  <main>
        <div class="div_company">{{ $supplier_name }}</div>
        <div class="div_date">{{ date('d/m/Y', strtotime(@$cheque_date)) }}</div>
        <div class="div_amount_w">{{ $amount_words }} Only</div>
        <div class="div_amount">{{ @App\SysHelper::com_curr_format(@$amount,2,'.',',') }}</div>
  </main>
</body>

</html>

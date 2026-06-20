<html>

<head>

<?php 
 $bi_amountt = 0;
 $bi_amountt2 = 0;
 $bi_amountt3 = 0;
 $bi_return1 = 0;
 $paidt = 0;
 $check_if_show = 0;
 $ii = 0;
 $data_adj = '';
 $sum_b=0;
 $all_total=0;
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
    @page {
      margin: 20px 20px 120px 20px;
    }

    header {
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

    footer {
      position: fixed;
      left: 0px;
      bottom: 0px;
      right: 0px;
      height: 100px;
      background-color: white;
      background-image: url('{!! asset("public/".$company->pdf_watermark."") !!}');
    }

    .pagenum:before {
      content: counter(page);
    }

    footer .page:after {
      content: counter(page, upper-roman);
    }

    body {
      font-family: Verdana, sans-serif;
      font-size: 12px;
      color: #555555;
      background-image: url('{!! asset("public/".$company->pdf_watermark."") !!}');
    }

    th,
    td {
      padding: 5px 5px;
    }

    .tdd {
      border: dashed 1px #9e9e9e;
      border-width: 0 0 1px 0;
    }

    b {
      font-size: 14px;
    }

    main {
      margin: 0px 0px 100px 0px;
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
      font-size: 11px;
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
    <footer>
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td style="border: none; line-height: 20px;" align="left" valign="top"><b class="bottom_b">Received By:</b><br >
            <b class="bottom_b">Name:</b><br >
            <b class="bottom_b">Phone:</b><br >
            <b class="bottom_b">Signature and stamp:</b>
          </td>
          <td style="border: none; line-height: 20px;" align="right" valign="top"><b class="bottom_b" style="font-size: 10px;">For {!! str_replace('SYSCOM DISTRIBUTIONS LLC BRANCH ABU DHABI 1','SYSCOM DISTRIBUTIONS LLC<br />BRANCH ABU DHABI 1',$company->company_name) !!}</b>
            <br />{{ auth()->check() ? auth()->user()->full_name : '' }}<br />
            Page No <span style="" class="pagenum"></span> of Statement of Outstanding
          </td>
        </tr>
      </table>

    </footer>
  <main class="m2">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left"><img src="{{asset('public/'.$company->company_logo)}}" width="200px" /></td>
        <td align="right"><b style="font-size: 30px; font-weight: 400;">Statement of Outstanding</b></td>
      </tr>
    </table>
    <br />
    <br />
    <br />

    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>

        <td width="50%" valign="top" style="line-height: 18px;">
          <b style="font-size: 100%;">{{@$cust_detail->customer_name_display}}</b><br />
          {{ $cust_detail->customer_salutation }} {{ $cust_detail->first_name }} {{ $cust_detail->last_name }}<br />
          {{ $cust_address->statename->name }}, {{ $cust_address->countryname->name }}<br />
          Phone: {{@$cust_detail->contcat_number}}<br />
          Email: {{@$cust_detail->email}}<br />
        </td>

         @php
             $customerCurrency = !empty($cust_detail->currency_id)
                 ? \App\SysCurrencySettings::select('code')->find($cust_detail->currency_id)
                 : null;
         @endphp
         <td width="50%" valign="top" style="line-height: 18px;">
          Credit Limit: {{@$cust_detail->contcat_number}}<br />
          Payment Terms: {{@$cust_detail->email}}<br />
          Currency: {{ @$customerCurrency->code }}<br />
          TRN No: {{@$cust_detail->vat_number}}
        </td>

      </tr>
    </table>

    <br />
    <br />

    <?php
   

    foreach ($receivable as $dt) {

      $adjustmentst = $data_adjestment->where('srn_no', $dt->transaction_no)->max('paid_amount');
      $receiptt = $data_receipt->where('bi_doc_no', $dt->transaction_no);
      if (count($receiptt) > 0) {
        foreach ($receiptt as $p) {
          $bi_amountt += $p->bi_amount;
        }
      }
      $receiptt2 = $data_receipt2->where('bi_doc_no', $dt->transaction_no);
      if (count($receiptt2) > 0) {
        foreach ($receiptt2 as $p) {
          $bi_amountt2 += $p->bi_amount;
        }
      }
      $receiptt3 = $data_receipt3->where('bi_doc_no', $dt->transaction_no);
      if (count($receiptt3) > 0) {
        foreach ($receiptt3 as $p) {
          $bi_amountt3 += $p->bi_amount;
        }
      }
      $return1 = $data_return->where('siv_no', $dt->transaction_no);
      if (count($return1) > 0) {
        foreach ($return1 as $p) {
          $bi_return1 += $p->paid_amount;
        }
      }
      $paidt += $adjustmentst + $bi_amountt-$bi_amountt2+$bi_amountt3+$bi_return1;

      if ($dt->debit_amount != $paidt) {

        if ($paidt != 0) {
          $check_if_show = 1;
        }
      }
    }

    //  foreach ($data_adjestment as $dadj){

    //}	

    ?>
<?php
$date = @$date; // Suppressing errors in case $date is undefined

// Convert the date if it's set and valid
$formattedDate = (!empty($date) && date('d/m/Y', strtotime($date)) !== '01/01/1970') 
    ? date('d/m/Y', strtotime($date)) 
    : date('d/m/Y'); // Default to today's date if null or 01/01/1970

?>

    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td style="text-align: center;"><b>Statement of Outstanding Balance As of {{ @$formattedDate }}</b></td>
      </tr>
    </table>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr class="item-head-row">
        <td style="width: 100px; text-align: center;">Doc Date</td>
        <td style="width: 100px; text-align: center;">Doc No</td>
        <td style="width: 100px; text-align: center;">LPO No</td>
        <td style="width: 100px; text-align: center;">Balance</td>
        <td style="width: 100px; text-align: center;">Over Due</td>
      </tr>
    </table>
    @php
    $adjustments = 0;
    $b=0;
    $dueNotDue=0;
    $due0To30=0;
    $due31To60=0;
    $due61To90=0;
    $dueOver90=0;
    $dueRows = collect();
    $mainSumOutstandingBalance = 0;
    @endphp
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      @foreach (($receivable ?? collect([])) as $dt)
      <?php
      try { ?>
        @php
        $adjustments = 0;
        $receipt_date='';
        $doc_number='';
        $cheque_number='';
        $bank_name='';
        $bi_amount=0;
        $bi_amount2=0;
        $bi_amount3=0;
        $bi_return1=0;
        $paid=0;
        @endphp
        @php
        $adjustments = $data_adjestment->where('srn_no',$dt->transaction_no)->max('paid_amount');
        $receipt = $data_receipt->where('bi_doc_no',$dt->transaction_no);
        if(count($receipt)>0){
            foreach($receipt as $p){
            $receipt_date .= date('d/m/Y', strtotime($p->receipt_date)).',';
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
        
        $receipt2 = $data_receipt2->where('bi_doc_no',$dt->transaction_no);
        if(count($receipt2)>0){
            foreach($receipt2 as $p){
            $receipt_date .= date('d/m/Y', strtotime($p->doc_date)).',';
            $doc_number .= $p->doc_number.',';
            
            $bi_amount2 += $p->bi_amount;
            }
        }
        
        $receipt3 = $data_receipt3->where('bi_doc_no',$dt->transaction_no);
        if(count($receipt3)>0){
            foreach($receipt3 as $p){
            $receipt_date .= date('d/m/Y', strtotime($p->doc_date)).',';
            $doc_number .= $p->doc_number.',';
            
            $bi_amount3 += $p->bi_amount;
            }
        }
        $return1 = $data_return->where('siv_no',$dt->transaction_no);
        if(count($return1)>0){
            foreach($return1 as $p){
            $receipt_date .= date('d/m/Y', strtotime($p->doc_date)).',';
            $doc_number .= $p->doc_number.',';
            
            $bi_return1 += $p->paid_amount;
            }
        }

        $paid += $adjustments+$bi_amount-$bi_amount2+$bi_amount3+$bi_return1;

        @endphp
      <?php



      } catch (\Exception $e) {
      } ?>

      <?php
      if (isset($data_adjestment[$ii]->paid_amount))
        $data_adj = $data_adjestment[$ii]->paid_amount;
      else
        $data_adj = ' ';
      ?>

      @if($dt->debit_amount != $paid)


      <tr>
        <td class="item-row" style="width: 100px; text-align: center;">{{ date('d/m/Y', strtotime($dt->transaction_date)) }}</td>
        <td class="item-row" style="width: 100px; text-align: center;">{{ $dt->transaction_no }}</td>
        @php $lpono = @App\SysHelper::get_sales_invoice_details($dt->transaction_no); @endphp
        <td class="item-row" style="width: 100px; text-align: center;">{{ @$lpono->lpo_number }}</td>

        @php $DueData = @App\SysHelper::get_due_date_sales_invoice($dt->transaction_no,$dt->transaction_date,$date); @endphp
        @php
            $rowDueAmount = str_contains($dt->transaction_no,'SR')
                ? ((float) $dt->credit_amount - abs((float) $paid))
                : ((float) $dt->debit_amount - abs((float) $paid));
            $mainSumOutstandingBalance += $rowDueAmount;
            $invoiceDate = $dt->transaction_date;
            $effectivePaymentTerm = null;
            $paymentTermsMapPdf = $payment_terms_map ?? collect();

            if (($dt->transaction_type ?? '') == 'opbinvoice') {
                $opbDet = isset($opbinvoice_map) ? $opbinvoice_map->get($dt->transaction_no) : null;
                $effectivePaymentTerm = App\SysPaymentTerms::resolveOpbPaymentTerm(
                    $opbDet->payment_terms ?? '',
                    $invoiceDate,
                    $opbDet->due_date ?? '',
                    $paymentTermsMapPdf
                );
            } else {
                $siRow = isset($sales_invoice_map) ? $sales_invoice_map->get($dt->transaction_no) : null;
                if ($siRow) {
                    $invoiceDate = $siRow->doc_date;
                    $effectivePaymentTerm = $paymentTermsMapPdf->get($siRow->payment_terms);
                }
            }

            $breakdown = App\SysPaymentTerms::buildOutstandingBreakdown(
                $invoiceDate,
                $rowDueAmount,
                $effectivePaymentTerm,
                $receivable_finance_rate ?? 0,
                $date
            );
            $ageingRow = App\SysPaymentTerms::buildOsListAgeingBuckets(
                $invoiceDate,
                $rowDueAmount,
                $effectivePaymentTerm,
                $date,
                $breakdown['max_overdue_days'] ?? null
            );

            if (($breakdown['max_overdue_days'] ?? 0) < 0) {
                $dueNotDue += $rowDueAmount;
                $dueRows->push([
                    'not_due' => $rowDueAmount,
                    '0_30' => 0,
                    '31_60' => 0,
                    '61_90' => 0,
                    '90_plus' => 0,
                ]);
            } else {
                $due0To30 += $ageingRow['0_30'];
                $due31To60 += $ageingRow['31_60'];
                $due61To90 += $ageingRow['61_90'];
                $dueOver90 += $ageingRow['90_plus'];
                $dueRows->push([
                    'not_due' => 0,
                    '0_30' => $ageingRow['0_30'],
                    '31_60' => $ageingRow['31_60'],
                    '61_90' => $ageingRow['61_90'],
                    '90_plus' => $ageingRow['90_plus'],
                ]);
            }
        @endphp


        @if (str_contains($dt->transaction_no,'SR'))
        <td class="item-row" style="width: 100px; text-align: center;"> {{ @App\SysHelper::com_curr_format($b - $dt->debit_amount,2,'.',',') }}  @php if($b - $dt->debit_amount <= 0) {$b = ($b - $dt->debit_amount);} else{ $b += ($b - $dt->debit_amount); } @endphp</td>
        @else
        <td class="item-row" style="width: 100px; text-align: center;">{{ @App\SysHelper::com_curr_format($dt->debit_amount-abs($paid),2,'.',',') }} @php $b += $dt->debit_amount-abs($paid); @endphp </td>
        @endif


        <td class="item-row" style="width: 100px; text-align: center;">{{ $DueData[1] }}</td>

        <!-- <td class="item-row" style="text-align: center;"> -- </td> -->
      </tr>

      @endif
      <?php $ii++; ?>
      @endforeach

      <tr>
        <td colspan="3" class="item-row" style="width: 100px; text-align: center;">Grand total amount</td>

        <td class="item-row" style="width: 100px; text-align: center;">{{ @App\SysHelper::com_curr_format($b,2,'.','') }}</td>
        <td class="item-row" style="width: 100px; text-align: center;">&nbsp;</td>
      </tr>
    </table>


                  @php
                      $unadjustedBalanceTotal = collect($list_of_unadjusted)->sum(function ($p) {
                          return (float) ($p->amount ?? 0) - (float) ($p->adj_amount ?? 0);
                      }) + collect($list_of_unadjusted_jv_to_jv)->sum(function ($p) {
                          return (float) ($p->amount ?? 0) - (float) ($p->adj_amount ?? 0);
                      });
                      $mainSumUnadjustedRows = collect($list_of_unadjusted ?? [])
                          ->filter(function ($row) {
                              $remaining = (float) ($row->amount ?? 0) - (float) ($row->adj_amount ?? 0);
                              $isOpeningBalanceCredit = ($row->transaction_type ?? '') === 'openingbalance'
                                  && (float) ($row->amount ?? 0) < 0;

                              if ($isOpeningBalanceCredit) {
                                  return round(abs($remaining), 2) > 0.00;
                              }

                              return round($remaining, 2) > 0.00;
                          })
                          ->values();
                      $existingUnadjustedDocNos = $mainSumUnadjustedRows->pluck('doc_number')->filter()->unique();
                      $mainSumUnadjustedJvRows = collect($list_of_unadjusted_jv_to_jv ?? [])
                          ->groupBy('doc_number')
                          ->map(function ($rows) {
                              $first = $rows->first();
                              $first->amount = collect($rows)->sum(function ($row) {
                                  return (float) ($row->amount ?? 0);
                              });
                              $first->amount2 = collect($rows)->sum(function ($row) {
                                  return (float) ($row->amount2 ?? 0);
                              });
                              $first->adj_amount = collect($rows)->sum(function ($row) {
                                  return (float) ($row->adj_amount ?? 0);
                              });
                              return $first;
                          })
                          ->filter(function ($row) use ($existingUnadjustedDocNos) {
                              if ($existingUnadjustedDocNos->contains($row->doc_number ?? null)) {
                                  return false;
                              }

                              return round((float) (($row->amount ?? 0) - ($row->amount2 ?? 0) - ($row->adj_amount ?? 0)), 2) > 0.00;
                          })
                          ->values();
                      $mainSumUnadjustedBalanceTotal = $mainSumUnadjustedRows->sum(function ($p) {
                          $unadjustedAmount = (float) ($p->amount ?? 0);
                          $unadjustedAdjustment = (float) ($p->adj_amount ?? 0);
                          $isReceivableOpeningDebit = ($p->transaction_type ?? '') === 'openingbalance'
                              && preg_match('/^OPB-\d+$/', (string) ($p->doc_number ?? ''))
                              && (float) ($p->amount ?? 0) > 0
                              && (float) ($p->debit_amount ?? 0) > (float) ($p->credit_amount ?? 0);

                          if (!$isReceivableOpeningDebit && (float) ($p->credit_amount ?? 0) > (float) ($p->debit_amount ?? 0)) {
                              $unadjustedAmount = -abs($unadjustedAmount);
                              $unadjustedAdjustment = -abs($unadjustedAdjustment);
                          }

                          return $unadjustedAmount - $unadjustedAdjustment;
                      }) + $mainSumUnadjustedJvRows->sum(function ($p) {
                          return (float) ($p->amount ?? 0) - ((float) ($p->amount2 ?? 0) + (float) ($p->adj_amount ?? 0));
                      });
                      $normalizePdcRowsForPdf = function ($rows) {
                          return collect($rows)
                              ->groupBy('doc_number')
                              ->map(function ($group) {
                                  $first = $group->first();
                                  $first->amount = (float) collect($group)->max(function ($row) {
                                      return (float) ($row->amount ?? 0);
                                  });
                                  $first->adj_amount = (float) collect($group)->max(function ($row) {
                                      return (float) ($row->adj_amount ?? 0);
                                  });
                                  $first->debit_amount = (float) collect($group)->max(function ($row) {
                                      return (float) ($row->debit_amount ?? 0);
                                  });
                                  $first->credit_amount = (float) collect($group)->max(function ($row) {
                                      return (float) ($row->credit_amount ?? 0);
                                  });
                                  return $first;
                              })
                              ->values();
                      };
                      $pdcRowsForPdf = $normalizePdcRowsForPdf($list_of_adjusted_pdc ?? [])
                          ->merge($normalizePdcRowsForPdf($list_of_unadjusted_pdc ?? []))
                          ->groupBy('doc_number')
                          ->map(function ($group) {
                              $first = $group->first();
                              $first->amount = (float) collect($group)->max(function ($row) {
                                  return (float) ($row->amount ?? 0);
                              });
                              $first->adj_amount = (float) collect($group)->max(function ($row) {
                                  return (float) ($row->adj_amount ?? 0);
                              });
                              $first->debit_amount = (float) collect($group)->max(function ($row) {
                                  return (float) ($row->debit_amount ?? 0);
                              });
                              $first->credit_amount = (float) collect($group)->max(function ($row) {
                                  return (float) ($row->credit_amount ?? 0);
                              });
                              return $first;
                          })
                          ->values();
                      $pdcInHandTotal = abs($pdcRowsForPdf->sum(function ($row) {
                          $sign = ((float) ($row->credit_amount ?? 0) > (float) ($row->debit_amount ?? 0)) ? -1 : 1;
                          return $sign * abs((float) ($row->amount ?? 0));
                      }));
                      $accountPdcAdjustedTotal = $pdcRowsForPdf->sum(function ($row) {
                          return abs((float) ($row->adj_amount ?? 0));
                      });
                      $mainSumTotal = (float) $mainSumOutstandingBalance + (float) $mainSumUnadjustedBalanceTotal + (float) $accountPdcAdjustedTotal;
                  @endphp
                  <br />
                  <table border="0" cellspacing="0" cellpadding="0" style="width: 320px;">
                    <tr>
                      <td style="width: 190px;"><b>Total Excluding PDC</b></td>
                      <td style="width: 10px;"><b>:</b></td>
                      <td style="width: 140px; text-align: right;"><b>{{ @App\SysHelper::com_curr_format($b,2,'.',',') }}</b></td>
                    </tr>
                    <tr>
                      <td><b>PDC in Hand</b></td>
                      <td><b>:</b></td>
                      <td style="text-align: right;"><b>{{ @App\SysHelper::com_curr_format($pdcInHandTotal,2,'.',',') }}</b></td>
                    </tr>
                    @if (count($list_of_unadjusted)>0 || count($list_of_unadjusted_jv_to_jv)>0)
                    <tr>
                      <td><b>Unadjusted balance</b></td>
                      <td><b>:</b></td>
                      <td style="text-align: right;"><b>{{ @App\SysHelper::com_curr_format($unadjustedBalanceTotal,2,'.',',') }}</b></td>
                    </tr>
                    @endif
                    <tr>
                      <td><b>Closing Balance</b></td>
                      <td><b>:</b></td>
                      <td style="text-align: right;"><b>{{ @App\SysHelper::com_curr_format($mainSumTotal,2,'.',',') }}</b></td>
                    </tr>
                  </table>

                  @php
                      $companyIdForBanks = session('logged_session_data.company_id') ?? ($company->id ?? null);
                      $bankSubgroupId = \App\SysAccountGroupSub2::whereRaw('LOWER(title) = ?', ['bank'])
                          ->where('status', 1)
                          ->value('id');
                      $companyBanks = collect();

                      if (!empty($bankSubgroupId) && !empty($companyIdForBanks)) {
                          $companyBanks = \App\SysChartofAccounts::select(
                                  'beneficiary_name',
                                  'bank_name',
                                  'acc_no',
                                  'iban',
                                  'swift_code',
                                  'branch',
                                  'account_name'
                              )
                              ->where('subgroup2', $bankSubgroupId)
                              ->where('status', 1)
                              ->where(function ($query) use ($companyIdForBanks) {
                                  $query->where('company_id', $companyIdForBanks)
                                      ->orWhereRaw('find_in_set(?, company_access)', [$companyIdForBanks]);
                              })
                              ->orderBy('bank_name', 'asc')
                              ->orderBy('account_name', 'asc')
                              ->get();
                      }
                  @endphp




     
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td style="text-align: center;"><b>Amount Due Excluding PDC</b></td>
      </tr>
    </table>
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr class="item-head-row">
                            <td style="width: 80px; text-align: center;">> 0</td>
                            <td style="width: 80px; text-align: center;">0-30</td>
                            <td style="width: 80px; text-align: center;">31-60</td>
                            <td style="width: 80px; text-align: center;">61-90</td>
                            <td style="width: 80px; text-align: center;">>90</td>
                        </tr>
                        </table>
                        
                        @forelse (($dueRows ?? collect([])) as $dueRow)
                        @php
                            $dueNotDueValue = (float) data_get($dueRow, 'not_due', 0);
                            $due0To30Value = (float) data_get($dueRow, '0_30', 0);
                            $due31To60Value = (float) data_get($dueRow, '31_60', 0);
                            $due61To90Value = (float) data_get($dueRow, '61_90', 0);
                            $dueOver90Value = (float) data_get($dueRow, '90_plus', 0);
                        @endphp
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                          <tr>
                              <td class="item-row" style="width: 80px; text-align: right;">{{ abs($dueNotDueValue) >= 0.01 ? App\SysHelper::com_curr_format($dueNotDueValue,2,'.',',') : '' }}</td>
                              <td class="item-row" style="width: 80px; text-align: right;">{{ abs($due0To30Value) >= 0.01 ? App\SysHelper::com_curr_format($due0To30Value,2,'.',',') : '' }}</td>
                              <td class="item-row" style="width: 80px; text-align: right;">{{ abs($due31To60Value) >= 0.01 ? App\SysHelper::com_curr_format($due31To60Value,2,'.',',') : '' }}</td>
                              <td class="item-row" style="width: 80px; text-align: right;">{{ abs($due61To90Value) >= 0.01 ? App\SysHelper::com_curr_format($due61To90Value,2,'.',',') : '' }}</td>
                              <td class="item-row" style="width: 80px; text-align: right;">{{ abs($dueOver90Value) >= 0.01 ? App\SysHelper::com_curr_format($dueOver90Value,2,'.',',') : '' }}</td>
                          </tr>
                        </table>
                        @empty
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                          <tr>
                              <td colspan="5" class="item-row" style="text-align: center;">&nbsp;</td>
                          </tr>
                        </table>
                        @endforelse
            




                  <!-- @if (count($list_of_unadjusted_pdc)>0)<br />
                  <b>List of Unadjusted PDC:-</b>
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr class="item-head-row">
                            <td style="width: 100px; text-align: center;">Doc Date</td>
                            <td style="width: 100px; text-align: center;">Receipt No</td>
                            <td style="width: 100px; text-align: right;">Amount</td>
                            <td style="width: 100px; text-align: center;">Cheque Date</td>
                            <td style="width: 100px; text-align: center;">Cheque No</td>
                            <td style="width: 100px; text-align: center;">Receipt Date</td>
                        </tr>
                    </table>
                        @foreach ($list_of_unadjusted_pdc as $p)
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td class="item-row" style="width: 100px; text-align: center;">{{ date('d/m/Y', strtotime($p->doc_date)) }}</td>
                            <td class="item-row" style="width: 100px; text-align: center;">{{ $p->doc_number }}</td>
                            <td class="item-row" style="width: 100px; text-align: right;">{{ @App\SysHelper::com_curr_format($p->amount - $p->adj_amount,2,'.',',') }}</td>
                            <td class="item-row" style="width: 100px; text-align: center;">{{ date('d/m/Y', strtotime($p->cheque_date)) }}</td>
                            <td class="item-row" style="width: 100px; text-align: center;">{{ $p->cheque_number }}</td>
                            <td class="item-row" style="width: 100px; text-align: center;">{{ date('d/m/Y', strtotime($p->receipt_date)) }}</td>
                        </tr>
                        </table>
                        @endforeach
                  @endif

                  @if (count($list_of_adjusted_pdc)>0)<br />
                  <b>List of PDC:-</b>
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr class="item-head-row">
                            <td style="width: 80px; text-align: center;">Doc Date</td>
                            <td style="width: 80px; text-align: center;">Receipt No</td>
                            <td style="width: 80px; text-align: right;">Amount</td>
                            <td style="width: 80px; text-align: center;">Cheque Date</td>
                            <td style="width: 80px; text-align: center;">Cheque No</td>
                            <td style="width: 80px; text-align: center;">Receipt Date</td>
                            <td style="width: 80px; text-align: center;">Invoice</td>
                            <td style="width: 80px; text-align: right;">Adjusted</td>
                        </tr>
                        </table>
                        
                        @foreach ($list_of_adjusted_pdc as $p)
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td class="item-row" style="width: 80px; text-align: center;">{{ date('d/m/Y', strtotime($p->doc_date)) }}</td>
                            <td class="item-row" style="width: 80px; text-align: center;">{{ $p->doc_number }}</td>
                            <td class="item-row" style="width: 80px; text-align: right;">{{ @App\SysHelper::com_curr_format($p->amount,2,'.',',') }}</td>
                            <td class="item-row" style="width: 80px; text-align: center;">{{ date('d/m/Y', strtotime($p->cheque_date)) }}</td>
                            <td class="item-row" style="width: 80px; text-align: center;">{{ $p->cheque_number }}</td>
                            <td class="item-row" style="width: 80px; text-align: center;">{{ date('d/m/Y', strtotime($p->receipt_date)) }}</td>
                            <td class="item-row" style="width: 80px; text-align: center;">{{ $p->bi_doc_no }}</td>
                            <td class="item-row" style="width: 80px; text-align: right;">
                                {{ @App\SysHelper::com_curr_format(@$p->adj_amount,2,'.',',') }}
                            </td>
                        </tr>
                        </table>
                        
                        <?php $adjusted = $receivable->where('transaction_no',$p->bi_doc_no);
                        if(count($adjusted)>0){
                          foreach ($adjusted as $q){ ?>
                          <b>PDC Adjusted:-</b>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                              <tr class="item-head-row">
                                  <td class="item-row" style="width: 80px; text-align: center;">Doc Date</td>
                                  <td class="item-row" style="width: 80px; text-align: center;">Doc No</td>
                                  <td class="item-row" style="width: 80px; text-align: center;">LPO No</td>
                                  <td class="item-row" style="width: 80px; text-align: right;">Amount</td>
                              </tr>
                              </table> <?php
                            $lpono2 = @App\SysHelper::get_sales_invoice_details($q->transaction_no);
                            ?>
                              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                              <tr>
                                  <td class="item-row" style="width: 80px; text-align: center;">{{ date('d/m/Y', strtotime($q->transaction_date)) }}</td>
                                  <td class="item-row" style="width: 80px; text-align: center;">{{ $q->transaction_no }}</td>
                                  <td class="item-row" style="width: 80px; text-align: center;">{{ $lpono2->lpo_number }}</td>
                                  <td class="item-row" style="width: 80px; text-align: right;">{{ @App\SysHelper::com_curr_format($q->credit_amount,2,'.',',') }}</td>
                              </tr>
                              </table>
                        <?php
                          }
                        }
                        ?>
                        @endforeach
                  @endif -->


  @if (count($companyBanks)>0)
                  <br />
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td style="text-align: center;"><b>Bank Details</b></td>
                    </tr>
                  </table>
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr class="item-head-row">
                      <td style="width: 18%; text-align: center;">Beneficiary Name</td>
                      <td style="width: 18%; text-align: center;">Bank Name</td>
                      <td style="width: 15%; text-align: center;">Acc No</td>
                      <td style="width: 22%; text-align: center;">IBAN No</td>
                      <td style="width: 13%; text-align: center;">SWIFT Code</td>
                      <td style="width: 14%; text-align: center;">Branch</td>
                    </tr>
                  </table>
                  @foreach ($companyBanks as $bank)
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td class="item-row" style="width: 18%; text-align: left; word-break: break-word;">{{ $bank->beneficiary_name ?: '-' }}</td>
                      <td class="item-row" style="width: 18%; text-align: left; word-break: break-word;">{{ $bank->bank_name ?: '-' }}</td>
                      <td class="item-row" style="width: 15%; text-align: center; word-break: break-word;">{{ $bank->acc_no ?: '-' }}</td>
                      <td class="item-row" style="width: 22%; text-align: center; word-break: break-word;">{{ $bank->iban ?: '-' }}</td>
                      <td class="item-row" style="width: 13%; text-align: center; word-break: break-word;">{{ $bank->swift_code ?: '-' }}</td>
                      <td class="item-row" style="width: 14%; text-align: left; word-break: break-word;">{{ $bank->branch ?: '-' }}</td>
                    </tr>
                  </table>
                  @endforeach
                  @endif
    {{-- <br /><br />
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
</body>

</html>

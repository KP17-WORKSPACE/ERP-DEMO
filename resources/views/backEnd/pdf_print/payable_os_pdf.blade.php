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
    @page { margin: 20px 20px 120px 20px; }
    header { position: fixed; left: 20px; top: -50px; right: 20px; height: 80px; background-color: white; text-align: center; border-bottom:solid 0px #808080; color:#555555;  }
    footer { position: fixed; left: 0px; bottom: 0px; right: 0px; height: 100px; background-color: white; background-image: url('{!! asset("public/".$company->pdf_watermark."") !!}'); }
    .pagenum:before { content: counter(page); }
    footer .page:after { content: counter(page, upper-roman); }

    body{font-family: Verdana, sans-serif; font-size:12px; color:#555555; background-image: url('{!! asset("public/".$company->pdf_watermark."") !!}');}

    th, td {padding: 5px 5px;}
    .tdd{border:dashed 1px #9e9e9e; border-width:0 0 1px 0;}
    b{font-size:12px;}
    main{margin:0px 0px 100px 0px;}
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
  <footer>
    <img src="{!! asset('public/' . $company->pdf_footer . '') !!}" width="100%" />
  </footer>
  <main class="m2">
    <?php try { ?>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
          <td align="left"><img  src="{{asset('public/'.$company->company_logo)}}" width="200px"/></td>
          <td align="right"><b style="font-size: 12px; font-weight: 400;">Statement of Outstanding</b></td>
      </tr>
  </table>
  <br />
  <br />
  <br />

    @php
      $supplierCurrency = !empty($cust_detail->currency_id)
          ? \App\SysCurrencySettings::select('code')->find($cust_detail->currency_id)
          : null;
      $paymentTerms = optional($cust_detail->paymentterms)->title;
      $creditLimit = $cust_detail->credit_limit !== null && $cust_detail->credit_limit !== ''
          ? number_format((float) $cust_detail->credit_limit, 2, '.', ',')
          : '';
    @endphp
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="50%" valign="top">
          <table width="100%" border="0" cellspacing="0" cellpadding="0" style="line-height: 18px;">
            <tr><td width="135px" style="padding:0;">Supplier Name</td><td width="8" style="padding:0;">:</td><td style="padding:0;"><b style="font-size:100%;">{{ @$cust_detail->customer_name_display }}</b></td></tr>
            <tr><td style="padding:0;">Contact Person</td><td style="padding:0;">:</td><td style="padding:0;">{{ $cust_detail->customer_salutation }} {{ $cust_detail->first_name }} {{ $cust_detail->last_name }}</td></tr>
            <tr><td style="padding:0;">Address</td><td style="padding:0;">:</td><td style="padding:0;">{{ optional(optional($cust_address)->statename)->name }}, {{ optional(optional($cust_address)->countryname)->name }}</td></tr>
            <tr><td style="padding:0;">Phone</td><td style="padding:0;">:</td><td style="padding:0;">{{ @$cust_detail->contcat_number }}</td></tr>
            <tr><td style="padding:0;">Email</td><td style="padding:0;">:</td><td style="padding:0;">{{ @$cust_detail->email }}</td></tr>
          </table>
        </td>
        <td width="50%" valign="top">
          <table width="100%" border="0" cellspacing="0" cellpadding="0" style="line-height: 18px;">
            <tr><td width="135px" style="padding:0;">Credit Limit</td><td width="8" style="padding:0;">:</td><td style="padding:0;">{{ $creditLimit }}</td></tr>
            <tr><td style="padding:0;">Payment Terms</td><td style="padding:0;">:</td><td style="padding:0;">{{ $paymentTerms }}</td></tr>
            <tr><td style="padding:0;">Currency</td><td style="padding:0;">:</td><td style="padding:0;">{{ optional($supplierCurrency)->code }}</td></tr>
            <tr><td style="padding:0;">TRN No</td><td style="padding:0;">:</td><td style="padding:0;">{{ @$cust_detail->vat_number }}</td></tr>
          </table>
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
        <td style="width: 100px; text-align: center;">Doc Date</td>
        <td style="width: 100px; text-align: center;">Doc No</td>
        <td style="width: 100px; text-align: center;">LPO No</td>
        <td style="width: 100px; text-align: center;">Balance</td>
        <td style="width: 100px; text-align: center;">Over Due</td>
      </tr>
    </table>
    @php
      $b = 0;
      $dueRows = collect();
      $mainSumOutstandingBalance = 0;
      $paymentTermsMapPdf = $payment_terms_map ?? collect();
      $purchaseInvoiceMapPdf = $purchase_invoice_map ?? collect();
      $salesInvoiceMapPdf = $sales_invoice_map ?? collect();
      $opbInvoiceMapPdf = $opbinvoice_map ?? collect();
    @endphp
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      @foreach (($payable ?? collect()) as $dt)
        @php
          $adjustments = (float) $data_adjestment->where('piv_no', $dt->transaction_no)->max('paid_amount');
          $biAmount = (float) $data_payment->where('bi_doc_no', $dt->transaction_no)->sum('bi_amount');
          $biAmount2 = (float) $data_payment2->where('bi_doc_no', $dt->transaction_no)->sum('bi_amount');
          $biAmount3 = (float) $data_payment3->where('bi_doc_no', $dt->transaction_no)->sum('bi_amount');
          $biAmount4 = (float) $data_return->where('piv_no', $dt->transaction_no)->sum('paid_amount');
          $opbImportPaid = ($dt->transaction_type ?? '') === 'opbinvoice'
              ? (float) ($dt->debit_amount ?? 0)
              : 0;
          $paid = ($adjustments + $biAmount + $biAmount2 + $opbImportPaid) - ($biAmount3 - $biAmount4);
          $isPurchaseReturnRow = str_contains((string) $dt->transaction_no, 'PR');
          $hidePurchaseReturn = $isPurchaseReturnRow && (float) $dt->debit_amount >= $paid;
          $showRow = (round((float) $dt->credit_amount, 2) !== round($paid, 2)
              || round((float) $dt->debit_amount, 2) > 0) && !$hidePurchaseReturn;
        @endphp

        @if ($showRow)
          @php
            $isSalesInvoiceRow = \Illuminate\Support\Str::contains($dt->transaction_no, ['SI']);
            $lpono = $isSalesInvoiceRow
                ? App\SysHelper::get_sales_invoice_details($dt->transaction_no)
                : App\SysHelper::get_purchase_invoice_details($dt->transaction_no);
            $opbDet = ($dt->transaction_type ?? '') === 'opbinvoice'
                ? $opbInvoiceMapPdf->get($dt->transaction_no)
                : null;
            $lpoNumber = $opbDet->po_no ?? ($lpono->lpo_number ?? '');
            $rowBalance = $isPurchaseReturnRow
                ? ((float) $dt->debit_amount - abs($paid))
                : ((float) $dt->credit_amount - abs($paid));
            $b += $rowBalance;
            $mainSumOutstandingBalance += $rowBalance;
            $invoiceDate = $dt->transaction_date;
            $effectivePaymentTerm = null;

            if (($dt->transaction_type ?? '') === 'opbinvoice') {
                $effectivePaymentTerm = App\SysPaymentTerms::resolveOpbPaymentTerm(
                    $opbDet->payment_terms ?? '',
                    $invoiceDate,
                    $opbDet->due_date ?? '',
                    $paymentTermsMapPdf
                );
                $DueData = App\SysHelper::get_due_date_invoice_opbinvoice(
                    $dt->transaction_no,
                    $opbDet->due_date ?? '',
                    $opbDet->payment_terms ?? '',
                    $date
                );
            } elseif ($isSalesInvoiceRow) {
                $invoiceRow = $salesInvoiceMapPdf->get($dt->transaction_no);
                if ($invoiceRow) {
                    $invoiceDate = $invoiceRow->doc_date;
                    $effectivePaymentTerm = $paymentTermsMapPdf->get($invoiceRow->payment_terms);
                }
                $DueData = App\SysHelper::get_due_date_sales_invoice($dt->transaction_no, $dt->transaction_date, $date);
            } else {
                $invoiceRow = $purchaseInvoiceMapPdf->get($dt->transaction_no);
                if ($invoiceRow) {
                    $invoiceDate = $invoiceRow->pi_date ?? $dt->transaction_date;
                    $effectivePaymentTerm = $paymentTermsMapPdf->get($invoiceRow->payment_terms);
                }
                $DueData = App\SysHelper::get_due_date_purchase_invoice($dt->transaction_no, $dt->transaction_date, $date);
            }

            $breakdown = App\SysPaymentTerms::buildOutstandingBreakdown(
                $invoiceDate,
                $rowBalance,
                $effectivePaymentTerm,
                $payable_finance_rate ?? 0,
                $date
            );
            $ageingRow = App\SysPaymentTerms::buildOsListAgeingBuckets(
                $invoiceDate,
                $rowBalance,
                $effectivePaymentTerm,
                $date,
                $breakdown['max_overdue_days'] ?? null
            );
            if (($breakdown['max_overdue_days'] ?? 0) < 0) {
                $dueRows->push([
                    'not_due' => $rowBalance,
                    '0_30' => 0,
                    '31_60' => 0,
                    '61_90' => 0,
                    '90_plus' => 0,
                ]);
            } else {
                $dueRows->push([
                    'not_due' => 0,
                    '0_30' => $ageingRow['0_30'],
                    '31_60' => $ageingRow['31_60'],
                    '61_90' => $ageingRow['61_90'],
                    '90_plus' => $ageingRow['90_plus'],
                ]);
            }
          @endphp
          <tr>
            <td class="item-row" style="width: 100px; text-align: center;">{{ date('d/m/Y', strtotime($dt->transaction_date)) }}</td>
            <td class="item-row" style="width: 100px; text-align: center;">{{ $dt->transaction_no }}</td>
            <td class="item-row" style="width: 100px; text-align: center;">{{ $lpoNumber }}</td>
            <td class="item-row" style="width: 100px; text-align: center;">{{ App\SysHelper::com_curr_format($rowBalance, 2, '.', ',') }}</td>
            <td class="item-row" style="width: 100px; text-align: center;">{{ $DueData[1] }}</td>
          </tr>
        @endif
      @endforeach
      <tr>
        <td colspan="3" class="item-row" style="width: 100px; text-align: center;">Grand total amount</td>
        <td class="item-row" style="width: 100px; text-align: center;">{{ App\SysHelper::com_curr_format($b, 2, '.', ',') }}</td>
        <td class="item-row" style="width: 100px; text-align: center;">&nbsp;</td>
      </tr>
    </table>

    @php
      $resolvePayableUnadjustedRow = function ($row) {
          $amount = (float) ($row->amount ?? 0);
          $adjustment = (float) ($row->adj_amount ?? 0);
          $docNumber = (string) ($row->doc_number ?? '');
          $isPayableCreditDoc = in_array(($row->transaction_type ?? ''), ['bankpayment', 'cashpayment', 'purchasereturn'], true)
              || \Illuminate\Support\Str::contains($docNumber, ['BP', 'CP', 'PR']);
          $isOpeningBalance = ($row->transaction_type ?? '') === 'openingbalance'
              && preg_match('/^OPB-\d+$/', $docNumber);

          if ($isPayableCreditDoc) {
              $amount = -abs($amount);
              $adjustment = -abs($adjustment);
          }

          return [
              'amount' => $amount,
              'adjustment' => $adjustment,
              'balance' => $amount - $adjustment,
              'adds_to_closing' => $isPayableCreditDoc || $isOpeningBalance,
          ];
      };
      $resolvePayableUnadjustedJvRow = function ($row) {
          $amount = (float) ($row->amount ?? 0);
          $adjustment = (float) ($row->amount2 ?? 0) + (float) ($row->adj_amount ?? 0);

          return [
              'amount' => $amount,
              'adjustment' => $adjustment,
              'balance' => $amount - $adjustment,
          ];
      };
      $unadjustedBalanceTotal = collect($list_of_unadjusted ?? [])->sum(function ($row) use ($resolvePayableUnadjustedRow) {
          return $resolvePayableUnadjustedRow($row)['balance'];
      }) + collect($list_of_unadjusted_jv_to_jv ?? [])->sum(function ($row) use ($resolvePayableUnadjustedJvRow) {
          return $resolvePayableUnadjustedJvRow($row)['balance'];
      });
      $unadjustedClosingContribution = collect($list_of_unadjusted ?? [])->sum(function ($row) use ($resolvePayableUnadjustedRow) {
          $values = $resolvePayableUnadjustedRow($row);
          return $values['adds_to_closing'] ? $values['balance'] : -$values['balance'];
      }) + collect($list_of_unadjusted_jv_to_jv ?? [])->sum(function ($row) use ($resolvePayableUnadjustedJvRow) {
          return -$resolvePayableUnadjustedJvRow($row)['balance'];
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
              return $first;
          })
          ->values();
      $pdcInHandTotal = abs($pdcRowsForPdf->sum('amount'));
      $accountPdcAdjustedTotal = $pdcRowsForPdf->sum(function ($row) {
          return abs((float) ($row->adj_amount ?? 0));
      });
      $mainSumTotal = (float) $mainSumOutstandingBalance
          + (float) $unadjustedClosingContribution
          + (float) $accountPdcAdjustedTotal;
    @endphp
    <br />
    <table border="0" cellspacing="0" cellpadding="0" style="width: 320px;">
      <tr>
        <td style="width: 200px;">Total Excluding PDC</td>
        <td style="width: 10px;">:</td>
        <td style="width: 140px; text-align: right;">{{ App\SysHelper::com_curr_format($b, 2, '.', ',') }}</td>
      </tr>
      <tr>
        <td>PDC in Hand</td>
        <td>:</td>
        <td style="text-align: right;">{{ App\SysHelper::com_curr_format($pdcInHandTotal, 2, '.', ',') }}</td>
      </tr>
      @if (count($list_of_unadjusted ?? []) > 0 || count($list_of_unadjusted_jv_to_jv ?? []) > 0)
      <tr>
        <td>Unadjusted balance</td>
        <td>:</td>
        <td style="text-align: right;">{{ App\SysHelper::com_curr_format(abs($unadjustedBalanceTotal), 2, '.', ',') }}</td>
      </tr>
      @endif
      <tr>
        <td>Closing Balance</td>
        <td>:</td>
        <td style="text-align: right;">{{ App\SysHelper::com_curr_format($mainSumTotal, 2, '.', ',') }}</td>
      </tr>
    </table>

    <br />
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td style="text-align: center;"><b>Amount Due Excluding PDC</b></td>
      </tr>
    </table>
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout: fixed;">
      <tr class="item-head-row">
        <td style="width: 20%; text-align: right;">&gt; 0</td>
        <td style="width: 20%; text-align: right;">0-30</td>
        <td style="width: 20%; text-align: right;">31-60</td>
        <td style="width: 20%; text-align: right;">61-90</td>
        <td style="width: 20%; text-align: right;">&gt;90</td>
      </tr>
      @forelse ($dueRows as $dueRow)
      <tr>
        <td class="item-row" style="width: 20%; text-align: right;">{{ abs($dueRow['not_due']) >= 0.01 ? App\SysHelper::com_curr_format($dueRow['not_due'], 2, '.', ',') : '0.00' }}</td>
        <td class="item-row" style="width: 20%; text-align: right;">{{ abs($dueRow['0_30']) >= 0.01 ? App\SysHelper::com_curr_format($dueRow['0_30'], 2, '.', ',') : '0.00' }}</td>
        <td class="item-row" style="width: 20%; text-align: right;">{{ abs($dueRow['31_60']) >= 0.01 ? App\SysHelper::com_curr_format($dueRow['31_60'], 2, '.', ',') : '0.00' }}</td>
        <td class="item-row" style="width: 20%; text-align: right;">{{ abs($dueRow['61_90']) >= 0.01 ? App\SysHelper::com_curr_format($dueRow['61_90'], 2, '.', ',') : '0.00' }}</td>
        <td class="item-row" style="width: 20%; text-align: right;">{{ abs($dueRow['90_plus']) >= 0.01 ? App\SysHelper::com_curr_format($dueRow['90_plus'], 2, '.', ',') : '0.00' }}</td>
      </tr>
      @empty
      <tr>
        <td class="item-row" style="width: 20%; text-align: right;">0.00</td>
        <td class="item-row" style="width: 20%; text-align: right;">0.00</td>
        <td class="item-row" style="width: 20%; text-align: right;">0.00</td>
        <td class="item-row" style="width: 20%; text-align: right;">0.00</td>
        <td class="item-row" style="width: 20%; text-align: right;">0.00</td>
      </tr>
      @endforelse
    </table>




                  @if (count($pdcRowsForPdf)>0)<br />
                  <b>List of PDC:-</b>
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr class="item-head-row">
                            <td style="width: 10%; text-align: center;">Doc Date</td>
                            <td style="width: 11%; text-align: center;">Payment No</td>
                            <td style="width: 10%; text-align: right;">Amount</td>
                            <td style="width: 10%; text-align: right;">Adjusted</td>
                            <td style="width: 10%; text-align: right;">Balance</td>
                            <td style="width: 10%; text-align: center;">Cheque Date</td>
                            <td style="width: 10%; text-align: center;">Cheque No</td>
                            <td style="width: 10%; text-align: center;">Payment Date</td>
                            <td style="width: 9%; text-align: center;">Invoice</td>
                            <td style="width: 10%; text-align: left;">Remarks</td>
                        </tr>
                        </table>
                        
                        @foreach ($pdcRowsForPdf as $p)
                        @php
                          $pdcAmount = abs((float) ($p->amount ?? 0));
                          $pdcAdjusted = abs((float) ($p->adj_amount ?? 0));
                          $pdcBalance = abs($pdcAmount - $pdcAdjusted);
                        @endphp
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td class="item-row" style="width: 10%; text-align: center;">{{ date('d/m/Y', strtotime($p->doc_date)) }}</td>
                            <td class="item-row" style="width: 11%; text-align: center;">{{ $p->doc_number }}</td>
                            <td class="item-row" style="width: 10%; text-align: right;">{{ App\SysHelper::com_curr_format($pdcAmount, 2, '.', ',') }}</td>
                            <td class="item-row" style="width: 10%; text-align: right;">{{ App\SysHelper::com_curr_format($pdcAdjusted, 2, '.', ',') }}</td>
                            <td class="item-row" style="width: 10%; text-align: right;">{{ App\SysHelper::com_curr_format($pdcBalance, 2, '.', ',') }}</td>
                            <td class="item-row" style="width: 10%; text-align: center;">{{ date('d/m/Y', strtotime($p->cheque_date)) }}</td>
                            <td class="item-row" style="width: 10%; text-align: center;">{{ $p->cheque_number }}</td>
                            <td class="item-row" style="width: 10%; text-align: center;">{{ date('d/m/Y', strtotime($p->payment_date)) }}</td>
                            <td class="item-row" style="width: 9%; text-align: center;">{{ $p->bi_doc_no ?? '-' }}</td>
                            <td class="item-row" style="width: 10%; text-align: left;">{{ $p->remarks ?? '' }}</td>
                        </tr>
                        </table>
                        
                        @endforeach
                        @endif

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

                  @if (count($companyBanks) > 0)
                  <br />
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td style="text-align: start;"><b>Bank Details</b></td>
                    </tr>
                  </table>
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    @foreach ($companyBanks->chunk(2) as $bankRow)
                    <tr>
                      @foreach ($bankRow as $bank)
                      <td width="50%" valign="top" style="padding: 4px 8px 4px 0;">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="font-size: 12px; line-height: 18px;">
                          <tr><td width="155px">Beneficiary Name</td><td width="8px">:</td><td style="word-break: break-word;">{{ $bank->beneficiary_name ?: '-' }}</td></tr>
                          <tr><td>Bank Name</td><td>:</td><td style="word-break: break-word;">{{ $bank->bank_name ?: '-' }}</td></tr>
                          <tr><td>Acc No</td><td>:</td><td style="word-break: break-word;">{{ $bank->acc_no ?: '-' }}</td></tr>
                          <tr><td>IBAN No</td><td>:</td><td style="word-break: break-word;">{{ $bank->iban ?: '-' }}</td></tr>
                          <tr><td>SWIFT Code</td><td>:</td><td style="word-break: break-word;">{{ $bank->swift_code ?: '-' }}</td></tr>
                          <tr><td>Branch</td><td>:</td><td style="word-break: break-word;">{{ $bank->branch ?: '-' }}</td></tr>
                        </table>
                      </td>
                      @endforeach
                      @if ($bankRow->count() < 2)
                      <td width="50%"></td>
                      @endif
                    </tr>
                    @endforeach
                  </table>
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

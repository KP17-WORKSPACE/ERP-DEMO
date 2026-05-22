@extends('backEnd.newmasterpage')
@section('mainContent')

    @php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp

<?php try { ?>
    <style>
        .ageing-grn-popover { max-width: 320px; text-align: left; }
        .ageing-grn-popover .popover-body { padding: 0.5rem 0.65rem; }
        .ageing-grn-tip {
            cursor: help;
            border-bottom: 1px dotted #adb5bd;
        }
        .recv-sched-col {
            font-size: 11px;
            line-height: 1.35;
            overflow: visible;
            border: 1px solid #dee2e6 !important;
        }
        #dataTable td.recv-sched-col + td.recv-sched-col + td.recv-sched-col {
            text-align: end !important;
        }
        .recv-sched-list { display: inline; word-break: break-word; }
        .recv-sched-item {
            cursor: help;
            border-bottom: 1px dotted #adb5bd;
            white-space: nowrap;
        }
        .recv-sched-sep { color: #868e96; }
        .recv-sched-od-late { color: #c92a2a; font-weight: 600; }
        .recv-sched-od-soon { color: #2b8a3e; font-weight: 600; }
        .recv-sched-od-today { color: #495057; font-weight: 600; }
    </style>

    <script>
function set_total(id,at){
    $('#sum_'+id).text(at.toFixed(@json(session('logged_session_data.decimal_point'))).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
    $('#collapse'+id).css('display','');
    $('#account_table'+id).css('display','');
}
function formatAmountToNumber(input) {
    if (!input) return 0;

    let inputStr = String(input).replace(/,/g, '').trim();
    let number = parseFloat(inputStr);
    return isNaN(number) ? 0 : number;
}
function set_total_addmore(id, amount) {
    let totText = $('#sum_' + id).text();
    let currentTotal = formatAmountToNumber(totText);
    let additionalAmount = formatAmountToNumber(amount);
    let newTotal = currentTotal + additionalAmount;
    $('#sum_' + id).text(newTotal.toLocaleString('en-US', { minimumFractionDigits: @json(session('logged_session_data.decimal_point')), maximumFractionDigits: @json(session('logged_session_data.decimal_point')) }));
}
function set_total_lessmore(id, amount) {
    let totText = $('#sum_' + id).text();
    let currentTotal = formatAmountToNumber(totText);
    let additionalAmount = formatAmountToNumber(amount);
    let newTotal = currentTotal - additionalAmount;
    $('#sum_' + id).text(newTotal.toLocaleString('en-US', { minimumFractionDigits: @json(session('logged_session_data.decimal_point')), maximumFractionDigits: @json(session('logged_session_data.decimal_point')) }));
}
function check_total(id, amount) {
    let totText = $('#sum_' + id).text();
    let currentTotal = formatAmountToNumber(totText);
    let additionalAmount = formatAmountToNumber(amount);
    if(currentTotal != additionalAmount){
        $('#sum_' + id).css('color', 'red');
    }
}
</script>

    <div class="content-container col-12">
    <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
        <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
            <div class="purchase-order-content-header">
                <h4 class="purchase-order-content-header-left">
                    PI Adjustment Report
                </h4>
                <div class="purchase-order-content-header-right">
                    {{-- <a class="btn btn-light" href="{{url('payment-add')}}">
                        <i class="ico icon-outline-add-square text-success"></i> Add Payment
                    </a> --}}
                    {{-- <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addChequeModal">
                        <i class="ico icon-outline-add-square text-success"></i> Search
                    </button> --}}
                </div>
            </div>

            <div class="card mb-3">
                                <div class="card-body">
{{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'pi-adjustment-report', 'method' => 'post', 'id' => 'pi-adjustment-report']) }}

                                    <div class="row gap-rows">
                <div class="col-md-5 mb-2">
                    <div class="no-gutters input-right-icon">
                        <div class="col">
                            <div class="form-check-label">
                                <label>@lang('Supplier Name')</label>
                                <select class="form-control js-account-select" name="supplier" id="supplier">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 mb-2">
                    <div class="no-gutters input-right-icon">
                        <div class="col">
                            <div class="form-check-label">
                                <label>@lang('From Date')</label>
                                <input class="form-control date-picker " id="from_date" type="text" name="from_date" value="{{ !empty($from_date) ? date('d/m/Y', strtotime($from_date)) : '' }}" autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 mb-2">
                    <div class="no-gutters input-right-icon">
                        <div class="col">
                            <div class="form-check-label">
                                <label>@lang('To date ')</label>
                                <input class="form-control date-picker" id="to_date" type="text" name="to_date" value="{{!empty($to_date) ? date('d/m/Y', strtotime($to_date)) : ''}}" autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">Filter By</label>
                    <select class="form-control" name="filter_by" id="filter_by">
                        <option value="" @if(@$filter_by == "") selected @endif>-Select-</option>
                        <option value="this_month" @if(@$filter_by == "this_month") selected @endif>This Month</option>
                        <option value="today" @if(@$filter_by == "today") selected @endif>Today</option>
                        <option value="this_week" @if(@$filter_by == "this_week") selected @endif>This Week</option>
                        <option value="last_week" @if(@$filter_by == "last_week") selected @endif>Last Week</option>                                    
                        <option value="last_month" @if(@$filter_by == "last_month") selected @endif>Last Month</option>
                        <option value="this_quarter" @if(@$filter_by == "this_quarter") selected @endif>This Quarter</option>
                        <option value="pre_quarter" @if(@$filter_by == "pre_quarter") selected @endif>Previous Quarter</option>
                        <option value="this_year" @if(@$filter_by == "this_year") selected @endif>This Year</option>
                        <option value="last_year" @if(@$filter_by == "last_year") selected @endif>Last Year</option>
                    </select>
                </div>
                <script>
                    function set_filter(){
                    if($('#from_date').val()!="" || $('#to_date').val() != "")
                    {
                        $('#filter_by').val('')
                    }
                    }
                </script>
                <div class="col-1">&nbsp;<br />
                    <button class="btn btn-light" type="submit">
                        <i class="ico icon-outline-minimalistic-magnifer text-success"></i> Search
                    </button>
                </div>
                                    </div>
            {{ Form::close() }}
                                </div>
                            </div>

                            
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row gap-rows">

                        <table class="table table-hover" id="long-list" style="border: solid 1px #e3e6f0; width:100%; table-layout:fixed;">
                            @php
                                $grouped = $purchase_invoice->groupBy('vendors');
                            @endphp
                            @foreach($grouped as $customer_id => $invoices)
                            @php $total_balance=0;
                            $total_invoice_amount = 0;
                            $total_adjustments = 0;
                            $total_balance = 0;
                            $total_total_balance = 0;
                            $total_cheque_amount = 0;
                            @endphp
    

                                <thead>
                                    <tr>
                                        <td style="cursor: pointer; padding: 5px 10px !important;" onclick="show_details({{ $invoices->first()->account_id }})" colspan="19"><h7>{{ $invoices->first()->account_code }} - {{ $invoices->first()->account_name }}</h7> <label style="float: right;" class="main_sum" id="sum_{{ $invoices->first()->account_id }}"></label></td>
                                    </tr>
                                    <tr class="{{ $invoices->first()->account_id }}" style="display: none;">
                                        <th class="border text-center">Doc Number</th>
                                        <th class="border text-center">Doc Date</th>
                                        <th class="border text-center">LPO Number</th>
                                        <th class="border text-center">Deal ID</th>
                                        <th class="border text-end">Amount</th>
                                        <th class="border text-end">Adjustments</th>
                                        <th class="border text-end">Balance</th>
                                        <th class="border text-end">Total Balance</th>

                                        <th class="border text-center">Doc Date</th>
                                        <th class="border text-center">Payment No</th>
                                        <th class="border text-end">Amount</th>
                                        <th class="border text-center">Cheque Date</th>
                                        <th class="border text-center">Cheque No</th>
                                        <th class="border text-center">Payment Date</th>


                                        <th class="border text-center">Due Date</th>
                                        <th class="border text-center">Over Due Days</th>
                                        <th class="border text-center">Due Amount</th>
                                        <th class="border text-center">Sales Person</th>
                                        <th class="border text-center">Payment Terms</th>
                                    </tr>
                                </thead>
                                <tbody class="{{ $invoices->first()->account_id }}" style="display: none;">
                                    @foreach($invoices as $invoice)
                                    
                                            @php
                                                $isImportedInvoice = ($invoice->transaction_type ?? '') === 'opbinvoice';
                                                $importedPaid = $isImportedInvoice ? (float) ($invoice->imported_paid ?? 0) : 0;
                                                $legacyAdjustments = 0;
                                                $p_doc_date = '';
                                                $bi_doc_numbers = '';
                                                $cheque_amount = '';
                                                $p_cheque_date = '';
                                                $p_cheque_number = '';
                                                $displayDate = '';
                                                $adjustment_data = $sys_adjustment_list->where('bi_doc_no', $invoice->doc_number);
                                                if (count($adjustment_data) > 0) {
                                                    foreach ($adjustment_data as $item) {
                                                        $legacyAdjustments = (float) $item['total_paid'];
                                                        $p_doc_date = $item['p_doc_date'];
                                                        $bi_doc_numbers = $item['bi_doc_numbers'];
                                                        $cheque_amount = $item['cheque_amount'];
                                                        $p_cheque_date = $item['p_cheque_date'];
                                                        $p_cheque_number = $item['p_cheque_number'];
                                                        if (!empty($item['p_doc_date'])) {
                                                            $displayDate = $item['p_doc_date'];
                                                        } elseif (!empty($item['j_doc_date'])) {
                                                            $displayDate = $item['j_doc_date'];
                                                        } elseif (!empty($item['s_doc_date'])) {
                                                            $displayDate = $item['s_doc_date'];
                                                        }
                                                    }
                                                }
                                                $displayAmount = isset($invoice->payable_credit_amount) ? (float) $invoice->payable_credit_amount : (float) $invoice->amount;
                                                $debitAmount = isset($invoice->payable_debit_amount) ? (float) $invoice->payable_debit_amount : 0;
                                                $adjustments = isset($invoice->payable_adjustments) ? (float) $invoice->payable_adjustments : ($legacyAdjustments + $importedPaid);
                                                $balance = isset($invoice->payable_balance) ? (float) $invoice->payable_balance : ($displayAmount - abs($adjustments));
                                                $ageingBalance = isset($invoice->payable_ageing_balance) ? (float) $invoice->payable_ageing_balance : $balance;
                                                $rowVisible = isset($invoice->payable_visible) ? (bool) $invoice->payable_visible : (abs($balance) >= 0.01 || abs($debitAmount) >= 0.01);
                                                $reportAsOfDate = $as_of_date ?? date('Y-m-d');
                                            @endphp
                                            @continue(!$rowVisible)
                                        <tr>
                                            <td class="border text-center">
                                                @if($isImportedInvoice)
                                                    {{ $invoice->doc_number }}
                                                @else
                                                    <a href="{{url('get-url-purchase-invoice/'.$invoice->doc_number)}}" target="_blank">{{ $invoice->doc_number }}</a>
                                                @endif
                                            </td>
                                            <td class="border text-center">{{ date('d/m/Y', strtotime(@$invoice->doc_date)) }}</td>
                                            <td class="border text-center">{{ $invoice->lpo_number }}</td>
                                            <td class="border text-center">
                                                @if($isImportedInvoice)
                                                    {{ @$invoice->deal_id }}
                                                @else
                                                    <a href="{{url('get-url-deal-track/'.@$invoice->deal_code->code)}}" target="_blank">{{ @$invoice->deal_code->code }}</a>
                                                @endif
                                            </td>

                                            <td class="border text-end">{{ App\SysHelper::com_curr_format($displayAmount, 2, '.', ',') }} <?php $total_invoice_amount += @$displayAmount; ?></td>
                                            <td class="border text-end">{{ App\SysHelper::com_curr_format($adjustments, 2, '.', ',') }}  <?php $total_adjustments += @$adjustments; ?></td>
                                            <td class="border text-end">{{ App\SysHelper::com_curr_format($balance, 2, '.', ',') }}  <?php $total_balance += @$balance; ?></td>
                                            <td class="border text-end">{{ App\SysHelper::com_curr_format($total_balance, 2, '.', ',') }} <?php $total_total_balance += @$balance; ?></td>
                                            
                                            <td class="border text-center">{{ @$displayDate ? date('d/m/Y', strtotime($displayDate)) : '' }}</td> 
                                            
                                                @if(Illuminate\Support\Str::contains(@$bi_doc_numbers, ['BP', 'CP']))
                                <td class="border text-center">
                                    <a href="{{ url('get-url-payment/' . @$bi_doc_numbers) }}" target="_blank">{{ @$bi_doc_numbers }}</a>
                                </td>
                            @elseif(Illuminate\Support\Str::contains(@$bi_doc_numbers, ['JV']))
                                <td class="border text-center">
                                    <a href="{{ url('get-url-journalvoucher/' . @$bi_doc_numbers) }}" target="_blank">{{ @$bi_doc_numbers }}</a>
                                </td>
                            @elseif(Illuminate\Support\Str::contains(@$bi_doc_numbers, ['PR']))
                                <td class="border text-center">
                                    <a href="{{ url('get-url-purchase-return/' . @$bi_doc_numbers) }}" target="_blank">{{ @$bi_doc_numbers }}</a>
                                </td>
                            @else
                            <td class="border text-center">
                                    {{ @$bi_doc_numbers }}
                                </td>
                            @endif
                                            <td class="border text-end">{{ $cheque_amount !== '' && $cheque_amount !== null ? App\SysHelper::com_curr_format($cheque_amount, 2, '.', ',') : '' }}<?php /*$total_cheque_amount += @$cheque_amount;*/ ?></td>
                                            <td class="border text-center">{{ @$p_cheque_date ? date('d/m/Y', strtotime($p_cheque_date)) : '' }}</td>
                                            <td class="border text-center">{{ @$p_cheque_number }}</td>
                                            <td class="border text-center">{{ @$displayDate ? date('d/m/Y', strtotime($displayDate)) : '' }}</td> 

                                            @php
                                                if ($isImportedInvoice) {
                                                    $paymentTermRow = App\SysPaymentTerms::resolveOpbPaymentTerm(
                                                        $invoice->payment_terms ?? '',
                                                        $invoice->doc_date,
                                                        $invoice->due_date ?? '',
                                                        $payment_terms_map ?? collect([])
                                                    );
                                                } else {
                                                    $paymentTermRow = isset($payment_terms_map) ? $payment_terms_map->get($invoice->payment_terms) : null;
                                                }
                                                $paymentTermsTitle = $paymentTermRow ? ($paymentTermRow->title ?? '') : '';
                                                $breakdown = abs($ageingBalance) >= 0.01
                                                    ? App\SysPaymentTerms::buildOutstandingBreakdown(
                                                        $invoice->doc_date,
                                                        $ageingBalance,
                                                        $paymentTermRow,
                                                        $payable_finance_rate ?? 0,
                                                        $reportAsOfDate
                                                    )
                                                    : ['installments' => [], 'max_overdue_days' => 0];
                                                if (abs($ageingBalance) >= 0.01) {
                                                    $paymentTermsTitle = $breakdown['payment_terms_title'] ?? $paymentTermsTitle;
                                                }
                                            @endphp
                                            @if(($breakdown['max_overdue_days'] ?? 0) > 0 && abs($ageingBalance) >= 0.01)
                                            <script>
                                                $('#sum_{{ $invoices->first()->account_id }}').css('color', 'red');
                                            </script>
                                            @endif
                                            @if(abs($ageingBalance) >= 0.01)
                                                @include('backEnd.outstanding.partials.receivable_due_columns', ['breakdown' => $breakdown])
                                            @else
                                            <td class="border"></td>
                                            <td class="border"></td>
                                            <td class="border"></td>
                                            @endif
                                            <td class="border text-center">{{ $isImportedInvoice ? (@$invoice->imported_sales_person ?? '') : @$invoice->salesman->full_name }}</td>
                                            <td class="border text-center">{{ $paymentTermsTitle }}</td>
                                        </tr>

                            


                                    @endforeach
                                    <tr>
                                        <th class="border text-end" colspan="4">Total</th>
                                        <th class="border text-end">{{ App\SysHelper::com_curr_format($total_invoice_amount, 2, '.', ',') }}</th>
                                        <th class="border text-end">{{ App\SysHelper::com_curr_format($total_adjustments, 2, '.', ',') }}</th>
                                        <th class="border text-end">{{ App\SysHelper::com_curr_format($total_balance, 2, '.', ',') }}</th>
                                        <th class="border text-end">{{ App\SysHelper::com_curr_format($total_total_balance, 2, '.', ',') }}</th>
                                        <th class="border" colspan="2"></th>
                                        <th class="border">
                                            {{-- {{ $total_cheque_amount }} --}}
                                        </td>
                                        <th class="border" style="margin-bottom: 40px;" colspan="8"></th>
                                    </tr>
                                    <tr><td colspan="19">

                            <script>
                                set_total({{ $invoices->first()->account_id }},{{ $total_total_balance }});
                            </script>


                                    <?php
                                        $account_id = $invoices->first()->account_id;
                                        $unadj_list = $list_of_unadjusted->where('account_id',$account_id);
                                        $unadj_list_jv_to_jv = $list_of_unadjusted_jv_to_jv->where('account_id',$account_id); ?>
                  
                  @if (count($unadj_list)>0 || count($unadj_list_jv_to_jv)>0)
                  <b>List of Unadjusted balance:-</b>
                  <table class="table" style="border: solid 1px #e3e6f0; width:auto; width:100%;">
                    <thead>
                        <tr>
                            <th class="border">Doc Date</th>
                            <th class="border">Payment No</th>
                            <th class="border text-end">Amount</th>
                            <th class="border">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($unadj_list)>0)
                        @foreach ($unadj_list as $p)
                        <tr>
                            <td class="border">{{ date('d/m/Y', strtotime($p->doc_date)) }}</td>
                            @php
                                $docNumber = $p->doc_number;
                            @endphp
                            @if(Illuminate\Support\Str::contains($docNumber, ['BP', 'CP']))
                                <td class="border">
                                    <a href="{{ url('get-url-payment/' . $docNumber) }}" target="_blank">{{ $docNumber }}</a>
                                </td>
                            @elseif(Illuminate\Support\Str::contains($docNumber, ['JV']))
                                <td class="border">
                                    <a href="{{ url('get-url-journalvoucher/' . $docNumber) }}" target="_blank">{{ $docNumber }}</a>
                                </td>
                            @elseif(Illuminate\Support\Str::contains($docNumber, ['PR']))
                                <td class="border">
                                    <a href="{{ url('get-url-purchase-return/' . $docNumber) }}" target="_blank">{{ $docNumber }}</a>
                                </td>
                            @else
                            <td class="border">
                                    {{ $docNumber }}
                                </td>
                            @endif
                            <td class="border text-end">{{ @App\SysHelper::com_curr_format($p->amount - $p->adj_amount,2,'.',',') }}</td>
                            <td class="border">{{ $p->remarks }}</td>
                        </tr>
                        <script>
                            set_total_lessmore({{ $invoices->first()->account_id }},{{ $p->amount - $p->adj_amount }})
                        </script>
                        @endforeach
                        @endif
                        
                        @if (count($unadj_list_jv_to_jv)>0)
                        @foreach ($unadj_list_jv_to_jv as $p)
                        <tr>
                            <td class="border">{{ date('d/m/Y', strtotime($p->doc_date)) }}</td>
                            @php
                                $docNumber = $p->doc_number;
                            @endphp
                            @if(Illuminate\Support\Str::contains($docNumber, ['JV']))
                                <td class="border">
                                    <a href="{{ url('get-url-journalvoucher/' . $docNumber) }}" target="_blank">{{ $docNumber }}</a>
                                </td>
                            @else
                            <td class="border">
                                    {{ $docNumber }}
                                </td>
                            @endif
                            <td class="border text-end">{{ @App\SysHelper::com_curr_format($p->amount - $p->amount2,2,'.',',') }}</td>
                            <td class="border">{{ $p->remarks }}</td>
                        </tr>
                        <script>
                            set_total_lessmore({{ $invoices->first()->account_id }},{{ $p->amount - $p->amount2 }})
                        </script>
                        @endforeach
                        @endif                       

                    </tbody>
                  </table>
                  @endif

                  <?php $pdc = $list_of_unadjusted_pdc->where('account_id',$account_id); ?>
                  @if (count($pdc)>0)
                  <b>List of Unadjusted PDC:-</b>
                  <table class="table" style="border: solid 1px #e3e6f0; width:auto; width:100%;">
                    <thead>
                        <tr>
                            <th class="border">Doc Date</th>
                            <th class="border">Payment No</th>
                            <th class="border text-end">Amount</th>
                            <th class="border">Cheque Date</th>
                            <th class="border">Cheque No</th>
                            <th class="border">Payment Date</th>
                            <th class="border">Remarks</th>
                        </tr>
                    </thead>:
                    <tbody>
                        @foreach ($pdc as $p)
                        <tr id="row_pdc_received_{{ $p->doc_number }}">
                            <td class="border">{{ date('d/m/Y', strtotime($p->doc_date)) }}</td>
                            <td class="border"><a href="{{url('get-url-payment/' . $p->doc_number)}}" target="_blank">{{ $p->doc_number }}</a></td>
                            <td class="border text-end">{{ @App\SysHelper::com_curr_format($p->amount - $p->adj_amount,2,'.',',') }}</td>
                            <td class="border">{{ date('d/m/Y', strtotime($p->cheque_date)) }}</td>
                            <td class="border">{{ $p->cheque_number }}</td>
                            <td class="border">{{ date('d/m/Y', strtotime($p->payment_date)) }}</td>
                            <td class="border">{{ $p->remarks }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                  </table>
                  @endif

                  <?php $pdc = $list_of_adjusted_pdc->where('account_id',$account_id); ?>
                  @if (count($pdc)>0)
                  <b>List of PDC:-</b>
                  <table class="table" style="border: solid 1px #e3e6f0; width:auto; width:100%;">
                    <thead>
                        <tr>
                            <th class="border">Doc Date</th>
                            <th class="border">Payment No</th>
                            <th class="border text-end">Amount</th>
                            <th class="border">Cheque Date</th>
                            <th class="border">Cheque No</th>
                            <th class="border">Payment Date</th>
                            <th class="border">Invoice Adjusted</th>
                            <th class="border text-end">Adjusted</th>
                            <th class="border">Remarks</th>
                        </tr>
                    </thead>:
                    <tbody>
                        @foreach ($pdc as $p)
                        <tr id="row_pdc_received_{{ $p->doc_number }}">
                            <td class="border">{{ date('d/m/Y', strtotime($p->doc_date)) }}</td>
                            <td class="border"><a href="{{url('get-url-payment/' . $p->doc_number)}}" target="_blank">{{ $p->doc_number }}</a></td>
                            <td class="border text-end">{{ @App\SysHelper::com_curr_format($p->amount,2,'.',',') }}</td>
                            <td class="border">{{ date('d/m/Y', strtotime($p->cheque_date)) }}</td>
                            <td class="border">{{ $p->cheque_number }}</td>
                            <td class="border">{{ date('d/m/Y', strtotime($p->payment_date)) }}</td>
                            <td class="border">
                                <a style="cursor: pointer;" onclick="row_det_fun('{{ $p->doc_number }}','{{ $p->bi_doc_no }}')">{{ $p->bi_doc_no }}</a>
                            </td>
                            <td class="border text-end">
                                {{ @App\SysHelper::com_curr_format(@$p->adj_amount,2,'.',',') }}
                            </td>
                            <td class="border">{{ $p->remarks }}</td>
                            
                        </tr>
                            <script>
                                set_total_addmore({{ $invoices->first()->account_id }},{{ $p->adj_amount }})
                            </script>
                        <tr style="display: none;" id="row_det_{{ $p->doc_number }}">
                            <td></td>
                            <td colspan="9">
                                    <table class="table" style="border: solid 1px #e3e6f0; width:auto; width:100%;" id="row_det_table_{{ $p->doc_number }}">
                                        <thead>
                                            <tr>
                                                <th class="border">Doc Date</th>
                                                <th class="border">Doc No</th>
                                                <th class="border">LPO No</th>
                                                <th class="border">Deal ID</th>
                                                <th class="border text-end">Amount</th>
                                                <th class="border text-end">Adjustments</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                  </table>
                  @endif    




                                    </td></tr>
                                </tbody>
                        @endforeach
                            
                    </table>

                    </div>
                </div>
            </div>



        </div>
    </div>
    </div>
    
<script>
    function show_details(account_code) {
        $('.' + account_code).toggle(); // Toggle visibility
        if (typeof window.initPiAdjDuePopovers === 'function') {
            window.initPiAdjDuePopovers(document);
        }
    }
</script>

<script>
$(document).ready(function () {
    function initAccountSelect2(selector) {
        $(selector).select2({
            ajax: {
                url: '{{ route("autocomplete.get_supp_account_list_ajax") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        search_text: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.map(function (item) {
                            return {
                                id: item.id,
                                text: item.account_code + ' - ' + item.account_name
                            };
                        })
                    };
                },
                cache: true
            },
            placeholder: 'Select Account',
            minimumInputLength: 2
        });
    }

    // Initial init
    initAccountSelect2('.js-account-select');

    // Re-initialize on focus (if needed for dynamically added fields)
    $(document).on('focus', '.js-account-select', function () {
        if (!$(this).hasClass("select2-hidden-accessible")) {
            initAccountSelect2(this);
        }
    });

    // Open dropdown and focus search box on click
    $(document).on('click', '.js-account-select', function () {
        $(this).select2('open');
    });

    // Focus the search input inside the opened Select2 dropdown
    $(document).on('select2:open', function () {
        setTimeout(function () {
            const searchInput = document.querySelector('.select2-container--open .select2-search__field');
            if (searchInput) {
                searchInput.focus();
            }
        }, 0);
    });
});
</script>

@push('scripts')
<script>
(function () {
    window.initPiAdjDuePopovers = function initAgeingGrnPopovers(root) {
        var scope = root && root.querySelectorAll ? root : document;
        var nodes = scope.querySelectorAll ? scope.querySelectorAll('.ageing-grn-pop') : document.querySelectorAll('.ageing-grn-pop');
        nodes.forEach(function (el) {
            if (typeof bootstrap === 'undefined' || !bootstrap.Popover) {
                return;
            }
            if (bootstrap.Popover.getInstance(el)) {
                return;
            }
            var raw = el.getAttribute('data-bs-content');
            if (!raw) {
                return;
            }
            new bootstrap.Popover(el, {
                container: 'body',
                html: true,
                sanitize: false,
                trigger: 'hover focus',
                placement: 'auto',
                delay: { show: 120, hide: 60 }
            });
        });
    };

    $(function () {
        window.initPiAdjDuePopovers(document);
        setTimeout(function () {
            window.initPiAdjDuePopovers(document);
        }, 600);
    });
})();
</script>
@endpush
        
    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

@endsection

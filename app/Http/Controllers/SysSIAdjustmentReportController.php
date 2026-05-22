<?php

namespace App\Http\Controllers;

use App\SysChartofAccounts;
use App\SysAccountType;
use App\ApiBaseMethod;
use App\SmSupplier;
use App\SysAccountGroup;
use App\SysAccountGroupSub;
use App\SysChartofaccountsOpeningBalanceInvoice;
use App\SysChartofAccountsTransaction;
use App\SysCompany;
use App\SysCustSuppl;
use App\SysCustSupplAddressbook;
use App\SysHelper;
use App\SysPaymentTerms;
use App\SysJournalVoucher;
use App\SysLedgerEntries;
use App\SysReceipt;
use App\SysReceiptAdjustments;
use App\SysReceiptAdjustmentsTemp;
use App\SysSalesInvoice;
use App\SysSalesReturn;
use App\SysSalesReturnAdjestment;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use hisorange\BrowserDetect\Result;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\Else_;
use Barryvdh\DomPDF\Facade as PDF;
use DateTime;

class SysSIAdjustmentReportController extends Controller
{
    public function __construct(){
        $this->middleware('PM');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        try{

            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $data = [];
            $data_all = [];
            $accounts = SysHelper::get_customer_list($company_id);
            $sales_person_list = SysHelper::get_sales_persons();
            $com_id = session('logged_session_data.company_id');
            $account_id = "";
            $till_date = date('Y-m-d');
            //$data_query->wherein('created_by',$r[1]);
            $pdc_list = [];
            $unadjested_list = [];
            $data_adjestment = [];
            $data_receipt = [];
            $transaction_no1 = [];
            $list_option = "";

               $from_date = "";
                $to_date = "";

$deal_id = '';
$amount = 0;
$overdue = -999999;
$ageing = -999999;




if(!$_POST){
    
    $sales_invoice = SysSalesInvoice::select(        
        'sys_sales_invoice.id',
        'doc_number',
        'doc_date',
        'lpo_number',
        'deal_id',
        db::raw('sum(taxableamount) + sum(vatamount) as amount'),
        'sales_man',
        'payment_terms',
        'customer',
        'ca.account_code',
        'ca.account_name',
        'ca.id as account_id'
    )
    ->join('sys_sales_invoice_items as si_items', 'si_items.si_id', 'sys_sales_invoice.id')
    ->join('sys_chartofaccounts as ca', 'ca.id', 'sys_sales_invoice.customer')
    ->where('sys_sales_invoice.company_id', $company_id)
    ->where('sys_sales_invoice.status', 1)
    ->groupBy('sys_sales_invoice.id','doc_number', 'doc_date', 'lpo_number', 'deal_id', 'sales_man', 'payment_terms', 'customer','ca.id')
    ->orderby('ca.account_name', 'asc')->orderby('doc_date', 'asc')
    ->get();
    $sales_invoice = $this->appendReceivableImportedInvoices($sales_invoice, $company_id);
    $as_of_date = $this->siAdjustmentAsOfDate($till_date);
    $sales_invoice = $this->applyReceivableOutstandingValues($sales_invoice, $company_id, $as_of_date);
    $sys_payment_terms_list = DB::table('sys_payment_terms')->get();
    $candidateAccountIds = $accounts->pluck('id');

    $list_of_unadjusted = SysHelper::get_list_of_unadjusted($candidateAccountIds,$company_id,$as_of_date);
    $list_of_unadjusted_jv_to_jv = SysHelper::get_list_of_unadjusted_jv_to_jv($candidateAccountIds,$company_id);
    $list_of_unadjusted_pdc = SysHelper::get_list_of_unadjusted_pdc($candidateAccountIds,$company_id);
    $list_of_adjusted_pdc = SysHelper::get_list_of_adjusted_pdc($candidateAccountIds,$company_id);
    $opb_balance_amount = SysHelper::get_customer_opening_balance($candidateAccountIds,$as_of_date,$company_id);
    $sales_invoice = $this->appendUnadjustedOnlySalesInvoiceRows($sales_invoice, $accounts, $list_of_unadjusted, $list_of_unadjusted_jv_to_jv, $as_of_date);

    $sys_adjustment_list = SysReceiptAdjustments::select(
        'bi_doc_number',
        'bi_doc_no',
        db::raw('sum(bi_paid) as total_paid'),
        db::raw('sum(bi_cheque_amount) as cheque_amount'),
        db::raw('IFNULL(max(r.doc_date), "") as r_doc_date'),
db::raw('IFNULL(max(r.cheque_date), "") as r_cheque_date'),
db::raw('IFNULL(max(r.cheque_number), "") as r_cheque_number'),
db::raw('IFNULL(max(j.doc_date), "") as j_doc_date'),
db::raw('IFNULL(max(s.doc_date), "") as s_doc_date')
    )
    ->leftjoin('sys_receipt as r','r.doc_number','sys_receipt_adjustments.bi_doc_number')
    ->leftjoin('sys_journalvoucher as j','j.doc_number','sys_receipt_adjustments.bi_doc_number')
    ->leftjoin('sys_sales_return as s','s.doc_number','sys_receipt_adjustments.bi_doc_number')
    ->where('sys_receipt_adjustments.status', 1)
    ->where('sys_receipt_adjustments.company_id', $company_id)
    ->wherein('sys_receipt_adjustments.account_id', $sales_invoice->pluck('customer'))
    ->groupBy('bi_doc_number', 'bi_doc_no')
    ->orderby('r_doc_date', 'asc')->orderby('s_doc_date', 'asc')->orderby('j_doc_date', 'asc')
    ->get()
    ->groupBy('bi_doc_no') // group by bi_doc_no
    ->map(function ($group) {
    $first = $group->first();
    return [
        'bi_doc_no' => $first->bi_doc_no,
        'bi_doc_numbers' => $group->pluck('bi_doc_number')->unique()->implode(', '),
        'total_paid' => $group->sum('total_paid'),
        'cheque_amount' => $group->sum('cheque_amount'), // or $first->cheque_amount if only max expected
        'r_doc_date' => $first->r_doc_date,
        'r_cheque_date' => $first->r_cheque_date,
        'r_cheque_number' => $first->r_cheque_number,
        'j_doc_date' => $first->j_doc_date,
        's_doc_date' => $first->s_doc_date,
    ];
})
    ->values();   

        $viewSupport = $this->loadSiAdjustmentViewData();
        extract($viewSupport);

return view('backEnd.outstanding.si_adjustment_report', compact('sales_invoice','sys_payment_terms_list','sys_adjustment_list','list_of_unadjusted','list_of_unadjusted_jv_to_jv','list_of_unadjusted_pdc','list_of_adjusted_pdc','opb_balance_amount','payment_terms_map','receivable_finance_rate','as_of_date'));

}

        
            if($_POST){
                $account_id = "";
             
                $filter_by = "";

                if ($request->customer != "") {
                    $account_id=$request->customer;
                }
                if ($request->from_date != "" && $request->filter_by == "") {
                    $from_date= Carbon::createFromFormat('d/m/Y', $request->from_date)->format('Y-m-d');
                }
                if ($request->from_date != "" && $request->filter_by == "") {
                    $from_date=Carbon::createFromFormat('d/m/Y', $request->from_date)->format('Y-m-d');
                    if ($request->to_date != "") {
                        $to_date=Carbon::createFromFormat('d/m/Y', $request->to_date)->format('Y-m-d');
                    }
                }
                if ($request->filter_by == "this_month") {
                    $from_date=date('Y-m-01');
                    $to_date=date("Y-m-t", strtotime($from_date));
                    $filter_by='this_month';               
                }
                if ($request->filter_by == "today") {
                    $from_date=date('Y-m-d');
                    $to_date=date('Y-m-d');
                    $filter_by='today';
                }
                if ($request->filter_by == "this_week") {
                    $from_date = date('Y-m-d', strtotime('-1 week sunday 00:00:00'));
                    $to_date = date('Y-m-d', strtotime('saturday 23:59:59'));
                    $filter_by='this_week';
                }
                if ($request->filter_by == "last_week") {
                    $from_date = date('Y-m-d', strtotime('-2 week sunday 00:00:00'));
                    $to_date = date('Y-m-d', strtotime('-1 week saturday 23:59:59'));
                    $filter_by='last_week';
                }
                if ($request->filter_by == "last_month") {
                    $from_date = date('Y-m-d', strtotime('first day of previous month'));
                    $to_date = date('Y-m-d', strtotime('last day of previous month'));
                    $filter_by='last_month';
                }
                if ($request->filter_by == "this_quarter") {
                    $q_date = SysHelper::get_quarter(date('m'));
                    $from_date = $q_date[0];
                    $to_date = $q_date[1];
                    $filter_by='this_quarter';
                }
                if ($request->filter_by == "pre_quarter") {
                    $q_date = SysHelper::get_pre_quarter(date('m'));
                    $from_date = $q_date[0];
                    $to_date = $q_date[1];
                    $filter_by='pre_quarter';
                }
                if ($request->filter_by == "this_year") {
                    $from_date = date('Y-01-01');
                    $to_date = date('Y-12-31');
                    $filter_by='this_year';
                }
                if ($request->filter_by == "last_year") {
                    $from_date = date("Y-01-01",strtotime("-1 year"));
                    $to_date = date("Y-12-31",strtotime("-1 year"));
                    $filter_by='last_year';
                }

                
                
                $query = SysSalesInvoice::select(        
        'sys_sales_invoice.id',
        'doc_number',
        'doc_date',
        'lpo_number',
        'deal_id',
        db::raw('sum(taxableamount) + sum(vatamount) as amount'),
        'sales_man',
        'payment_terms',
        'customer',
        'ca.account_code',
        'ca.account_name',
        'ca.id as account_id'
    )
    ->join('sys_sales_invoice_items as si_items', 'si_items.si_id', 'sys_sales_invoice.id')
    ->join('sys_chartofaccounts as ca', 'ca.id', 'sys_sales_invoice.customer')
    ->where('sys_sales_invoice.company_id', $company_id);
    if($account_id !=""){
        $query->where('sys_sales_invoice.customer',$account_id);
    }
    if (!empty($from_date) && !empty($to_date)) {
        $query->whereBetween('sys_sales_invoice.doc_date', [$from_date, $to_date]);
    } elseif (!empty($from_date)) {
        $query->whereDate('sys_sales_invoice.doc_date', '>=', $from_date);
    } elseif (!empty($to_date)) {
        $query->whereDate('sys_sales_invoice.doc_date', '<=', $to_date);
    }

$sales_invoice = $query->where('sys_sales_invoice.status', 1)
    ->groupBy('sys_sales_invoice.id','doc_number', 'doc_date', 'lpo_number', 'deal_id', 'sales_man', 'payment_terms', 'customer','ca.id')
    ->orderby('ca.account_name', 'asc')->orderby('doc_date', 'asc')
    ->get();
    $sales_invoice = $this->appendReceivableImportedInvoices($sales_invoice, $company_id, $account_id, $from_date, $to_date);
    $as_of_date = $this->siAdjustmentAsOfDate($to_date);
    $sales_invoice = $this->applyReceivableOutstandingValues($sales_invoice, $company_id, $as_of_date);
    $sys_payment_terms_list = DB::table('sys_payment_terms')->get();
    $candidateAccountIds = $account_id != "" ? collect([$account_id]) : $accounts->pluck('id');

    $list_of_unadjusted = SysHelper::get_list_of_unadjusted($candidateAccountIds,$company_id,$as_of_date);
    $list_of_unadjusted_jv_to_jv = SysHelper::get_list_of_unadjusted_jv_to_jv($candidateAccountIds,$company_id);
    $list_of_unadjusted_pdc = SysHelper::get_list_of_unadjusted_pdc($candidateAccountIds,$company_id);
    $list_of_adjusted_pdc = SysHelper::get_list_of_adjusted_pdc($candidateAccountIds,$company_id);
    $opb_balance_amount = SysHelper::get_customer_opening_balance($candidateAccountIds,$as_of_date,$company_id);
    $sales_invoice = $this->appendUnadjustedOnlySalesInvoiceRows($sales_invoice, $accounts, $list_of_unadjusted, $list_of_unadjusted_jv_to_jv, $as_of_date);

    $sys_adjustment_list = SysReceiptAdjustments::select(
        'bi_doc_number',
        'bi_doc_no',
        db::raw('sum(bi_paid) as total_paid'),
        db::raw('sum(bi_cheque_amount) as cheque_amount'),
        db::raw('IFNULL(max(r.doc_date), "") as r_doc_date'),
db::raw('IFNULL(max(r.cheque_date), "") as r_cheque_date'),
db::raw('IFNULL(max(r.cheque_number), "") as r_cheque_number'),
db::raw('IFNULL(max(j.doc_date), "") as j_doc_date'),
db::raw('IFNULL(max(s.doc_date), "") as s_doc_date')
    )
    ->leftjoin('sys_receipt as r','r.doc_number','sys_receipt_adjustments.bi_doc_number')
    ->leftjoin('sys_journalvoucher as j','j.doc_number','sys_receipt_adjustments.bi_doc_number')
    ->leftjoin('sys_sales_return as s','s.doc_number','sys_receipt_adjustments.bi_doc_number')
    ->where('sys_receipt_adjustments.status', 1)
    ->where('sys_receipt_adjustments.company_id', $company_id)
    ->wherein('sys_receipt_adjustments.account_id', $sales_invoice->pluck('customer'))
    ->groupBy('bi_doc_number', 'bi_doc_no')
    ->orderby('r_doc_date', 'asc')->orderby('s_doc_date', 'asc')->orderby('j_doc_date', 'asc')
    ->get()
    ->groupBy('bi_doc_no') // group by bi_doc_no
    ->map(function ($group) {
    $first = $group->first();
    return [
        'bi_doc_no' => $first->bi_doc_no,
        'bi_doc_numbers' => $group->pluck('bi_doc_number')->unique()->implode(', '),
        'total_paid' => $group->sum('total_paid'),
        'cheque_amount' => $group->sum('cheque_amount'), // or $first->cheque_amount if only max expected
        'r_doc_date' => $first->r_doc_date,
        'r_cheque_date' => $first->r_cheque_date,
        'r_cheque_number' => $first->r_cheque_number,
        'j_doc_date' => $first->j_doc_date,
        's_doc_date' => $first->s_doc_date,
    ];
})
    ->values();   

        $viewSupport = $this->loadSiAdjustmentViewData();
        extract($viewSupport);

return view('backEnd.outstanding.si_adjustment_report', compact('sales_invoice','sys_payment_terms_list','sys_adjustment_list','list_of_unadjusted','list_of_unadjusted_jv_to_jv','list_of_unadjusted_pdc','list_of_adjusted_pdc','opb_balance_amount','from_date','to_date','payment_terms_map','receivable_finance_rate','as_of_date'));
            }

        }catch (\Exception $e) {
            return $e;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    private function loadSiAdjustmentViewData()
    {
        $companyId = session('logged_session_data.company_id');
        $payment_terms_map = SysPaymentTerms::where('active_status', 1)->get()->keyBy('id');
        $company_row = SysCompany::find($companyId);
        $receivable_finance_rate = 0;
        if ($company_row) {
            $receivable_finance_rate = (float) ($company_row->receivables_finance_cost_percentage ?? 0);
        }

        return compact('payment_terms_map', 'receivable_finance_rate');
    }

    private function siAdjustmentAsOfDate($date = '')
    {
        if (empty($date)) {
            return date('Y-m-d');
        }

        return SysHelper::normalizeToYmd($date) ?: $date;
    }

    private function siAdjustmentMapKey($accountId, $docNo)
    {
        return (string) $accountId . '|' . (string) $docNo;
    }

    private function appendUnadjustedOnlySalesInvoiceRows($salesInvoice, $accounts, $listOfUnadjusted, $listOfUnadjustedJvToJv, $asOfDate)
    {
        $salesInvoice = collect($salesInvoice);
        $renderedAccountIds = $salesInvoice->pluck('customer')->filter()->unique()->values();
        $unadjustedAccountIds = collect($listOfUnadjusted)->pluck('account_id')
            ->merge(collect($listOfUnadjustedJvToJv)->pluck('account_id'))
            ->filter()
            ->unique()
            ->values();

        $missingAccounts = collect($accounts)
            ->whereIn('id', $unadjustedAccountIds)
            ->whereNotIn('id', $renderedAccountIds);

        foreach ($missingAccounts as $account) {
            $salesInvoice->push((object) [
                'id' => 0,
                'doc_number' => '__unadjusted_only_' . $account->id,
                'doc_date' => $asOfDate ?: date('Y-m-d'),
                'lpo_number' => '',
                'deal_id' => '',
                'amount' => 0,
                'sales_man' => null,
                'payment_terms' => null,
                'customer' => $account->id,
                'account_code' => $account->account_code,
                'account_name' => $account->account_name,
                'account_id' => $account->id,
                'transaction_type' => 'unadjusted_placeholder',
                'receivable_debit_amount' => 0,
                'receivable_credit_amount' => 0,
                'receivable_adjustments' => 0,
                'receivable_balance' => 0,
                'receivable_ageing_balance' => 0,
                'receivable_visible' => false,
            ]);
        }

        return $salesInvoice->sortBy(function ($row) {
            return strtolower((string) ($row->account_name ?? '')) . '|' . (string) ($row->doc_date ?? '');
        })->values();
    }

    private function applyReceivableOutstandingValues($salesInvoice, $companyId, $asOfDate)
    {
        $salesInvoice = collect($salesInvoice);
        if ($salesInvoice->isEmpty()) {
            return $salesInvoice;
        }

        $companyIds = collect(is_array($companyId) ? $companyId : [$companyId])->filter()->values();
        $accountIds = $salesInvoice->pluck('customer')->filter()->unique()->values();
        $docNos = $salesInvoice->pluck('doc_number')->filter()->unique()->values();

        if ($companyIds->isEmpty() || $accountIds->isEmpty() || $docNos->isEmpty()) {
            return $salesInvoice;
        }

        $ledgerRows = DB::table('sys_chartofaccounts_transaction')
            ->select(
                'account_id',
                'transaction_no',
                DB::raw('MIN(transaction_date) as transaction_date'),
                DB::raw('MAX(transaction_type) as transaction_type'),
                DB::raw('SUM(debit_amount) as debit_amount'),
                DB::raw('SUM(credit_amount) as credit_amount')
            )
            ->whereIn('company_id', $companyIds)
            ->whereIn('account_id', $accountIds)
            ->whereIn('transaction_no', $docNos)
            ->where('status', 1)
            ->whereIn('transaction_type', ['salesinvoice', 'salesreturn', 'opbinvoice', 'openingbalance111'])
            ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') <= ?", [$asOfDate])
            ->groupBy('account_id', 'transaction_no')
            ->get()
            ->keyBy(function ($row) {
                return $this->siAdjustmentMapKey($row->account_id, $row->transaction_no);
            });

        if ($ledgerRows->isEmpty()) {
            return $salesInvoice;
        }

        $trnNos = $ledgerRows->pluck('transaction_no')->unique()->values();

        $srnPaid = DB::table('sys_sales_return_adjestment')
            ->select('srn_no', DB::raw('SUM(paid_amount) as paid_amount'))
            ->whereIn('srn_no', $trnNos)
            ->groupBy('srn_no')
            ->pluck('paid_amount', 'srn_no');

        $receiptPaid = DB::table('sys_receipt as r')
            ->join('sys_receipt_adjustments as ra', 'ra.bi_doc_number', '=', 'r.doc_number')
            ->whereIn('ra.account_id', $accountIds)
            ->whereIn('ra.bi_doc_no', $trnNos)
            ->whereIn('r.company_id', $companyIds)
            ->where('r.status', 1)
            ->select('ra.account_id', 'ra.bi_doc_no', DB::raw('SUM(ra.bi_amount) as bi_amount'))
            ->groupBy('ra.account_id', 'ra.bi_doc_no')
            ->get()
            ->mapWithKeys(function ($row) {
                return [$this->siAdjustmentMapKey($row->account_id, $row->bi_doc_no) => (float) $row->bi_amount];
            });

        $jvReceiptPaid = DB::table('sys_journalvoucher as j')
            ->join('sys_receipt_adjustments as ra', 'ra.bi_doc_number', '=', 'j.doc_number')
            ->whereIn('ra.account_id', $accountIds)
            ->whereIn('ra.bi_doc_no', $trnNos)
            ->whereIn('j.company_id', $companyIds)
            ->where('j.status', 1)
            ->select('ra.account_id', 'ra.bi_doc_no', DB::raw('SUM(ra.bi_amount) as bi_amount'))
            ->groupBy('ra.account_id', 'ra.bi_doc_no')
            ->get()
            ->mapWithKeys(function ($row) {
                return [$this->siAdjustmentMapKey($row->account_id, $row->bi_doc_no) => (float) $row->bi_amount];
            });

        $jvPaymentPaid = DB::table('sys_journalvoucher as j')
            ->join('sys_payment_adjustments as pa', 'pa.bi_doc_number', '=', 'j.doc_number')
            ->whereIn('pa.account_id', $accountIds)
            ->whereIn('pa.bi_doc_no', $trnNos)
            ->whereIn('j.company_id', $companyIds)
            ->where('j.status', 1)
            ->select('pa.account_id', 'pa.bi_doc_no', DB::raw('SUM(pa.bi_amount) as bi_amount'))
            ->groupBy('pa.account_id', 'pa.bi_doc_no')
            ->get()
            ->mapWithKeys(function ($row) {
                return [$this->siAdjustmentMapKey($row->account_id, $row->bi_doc_no) => (float) $row->bi_amount];
            });

        $returnPaid = DB::table('sys_sales_return as r')
            ->join('sys_sales_return_adjestment as ra', 'ra.srn_no', '=', 'r.doc_number')
            ->whereIn('r.customer', $accountIds)
            ->whereIn('ra.siv_no', $trnNos)
            ->whereIn('r.company_id', $companyIds)
            ->where('r.status', 1)
            ->whereRaw("DATE_FORMAT(r.doc_date, '%Y-%m-%d') <= ?", [$asOfDate])
            ->select('r.customer', 'ra.siv_no', DB::raw('SUM(ra.paid_amount) as paid_amount'))
            ->groupBy('r.customer', 'ra.siv_no')
            ->get()
            ->mapWithKeys(function ($row) {
                return [$this->siAdjustmentMapKey($row->customer, $row->siv_no) => (float) $row->paid_amount];
            });

        $opbReceiptPaid = DB::table('sys_receipt_adjustments as ra')
            ->where('ra.transaction_type', 'openingbalance')
            ->whereIn('ra.company_id', $companyIds)
            ->where('ra.status', 1)
            ->whereIn('ra.account_id', $accountIds)
            ->whereIn('ra.bi_doc_no', $trnNos)
            ->select('ra.account_id', 'ra.bi_doc_no', DB::raw('SUM(ra.bi_amount) as bi_amount'))
            ->groupBy('ra.account_id', 'ra.bi_doc_no')
            ->get()
            ->mapWithKeys(function ($row) {
                return [$this->siAdjustmentMapKey($row->account_id, $row->bi_doc_no) => (float) $row->bi_amount];
            });

        return $salesInvoice->map(function ($invoice) use ($ledgerRows, $srnPaid, $receiptPaid, $jvReceiptPaid, $jvPaymentPaid, $returnPaid, $opbReceiptPaid) {
            $key = $this->siAdjustmentMapKey($invoice->customer ?? '', $invoice->doc_number ?? '');
            $ledger = $ledgerRows->get($key);

            if (!$ledger) {
                $paid = (float) ($invoice->imported_paid ?? 0);
                $amount = (float) ($invoice->amount ?? 0);
                $invoice->receivable_debit_amount = $amount;
                $invoice->receivable_credit_amount = 0;
                $invoice->receivable_adjustments = $paid;
                $invoice->receivable_balance = $amount - abs($paid);
                $invoice->receivable_ageing_balance = $invoice->receivable_balance;
                $invoice->receivable_visible = abs($invoice->receivable_balance) >= 0.01;
                return $invoice;
            }

            $docNo = (string) $ledger->transaction_no;
            $paid = (float) ($srnPaid[$docNo] ?? 0)
                + (float) ($receiptPaid[$key] ?? 0)
                + (float) ($jvReceiptPaid[$key] ?? 0)
                + (float) ($opbReceiptPaid[$key] ?? 0)
                - (float) ($jvPaymentPaid[$key] ?? 0)
                - (float) ($returnPaid[$key] ?? 0);

            if (($ledger->transaction_type ?? '') === 'opbinvoice') {
                $paid += (float) ($ledger->credit_amount ?? 0);
            }

            $debit = (float) ($ledger->debit_amount ?? 0);
            $credit = (float) ($ledger->credit_amount ?? 0);
            $isHidden = false;
            if (strpos($docNo, 'SR') !== false && round($credit, 2) >= round($paid, 2)) {
                $isHidden = true;
            }
            if (strpos($docNo, 'SI') !== false && round(abs($debit), 2) == round(abs($paid), 2)) {
                $isHidden = true;
            }

            $invoice->amount = $debit;
            $invoice->imported_paid = ($ledger->transaction_type ?? '') === 'opbinvoice' ? $credit : ($invoice->imported_paid ?? 0);
            $invoice->transaction_type = $ledger->transaction_type ?: ($invoice->transaction_type ?? '');
            $invoice->receivable_debit_amount = $debit;
            $invoice->receivable_credit_amount = $credit;
            $invoice->receivable_adjustments = $paid;
            $invoice->receivable_balance = $debit - abs($paid);
            $invoice->receivable_ageing_balance = strpos($docNo, 'SR') !== false ? $credit - abs($paid) : $invoice->receivable_balance;
            $invoice->receivable_visible = ((number_format($debit, 2, '.', '') != number_format($paid, 2, '.', '')) || $credit > 0) && !$isHidden;

            if (!empty($ledger->transaction_date)) {
                $invoice->doc_date = $ledger->transaction_date;
            }

            return $invoice;
        });
    }

    private function appendReceivableImportedInvoices($salesInvoice, $companyId, $accountId = '', $fromDate = '', $toDate = '')
    {
        $accountIds = SysHelper::get_customer_list(is_array($companyId) ? $companyId : [$companyId])->pluck('id');
        $invoiceDetails = DB::table('sys_chartofaccounts_transaction_invoice_detail')
            ->select(
                'trn_id',
                DB::raw('MAX(po_no) as po_no'),
                DB::raw('MAX(deal_id) as deal_id'),
                DB::raw('MAX(payment_terms) as payment_terms'),
                DB::raw('MAX(due_date) as due_date'),
                DB::raw('MAX(sales_person) as sales_person')
            )
            ->groupBy('trn_id');

        $query = DB::table('sys_chartofaccounts_transaction as t')
            ->join('sys_chartofaccounts as ca', 'ca.id', '=', 't.account_id')
            ->leftJoinSub($invoiceDetails, 'd', function ($join) {
                $join->on('d.trn_id', '=', 't.id');
            })
            ->where('t.status', 1)
            ->whereIn('t.company_id', is_array($companyId) ? $companyId : [$companyId])
            ->where('t.transaction_type', 'opbinvoice')
            ->whereIn('t.account_id', $accountIds)
            ->select(
                DB::raw('MIN(t.id) as id'),
                DB::raw('t.transaction_no as doc_number'),
                DB::raw('MIN(t.transaction_date) as doc_date'),
                DB::raw('MAX(d.po_no) as lpo_number'),
                DB::raw('MAX(d.deal_id) as deal_id'),
                DB::raw('SUM(t.debit_amount) as amount'),
                DB::raw('SUM(t.credit_amount) as imported_paid'),
                DB::raw('NULL as sales_man'),
                DB::raw('MAX(d.payment_terms) as payment_terms'),
                DB::raw('t.account_id as customer'),
                'ca.account_code',
                'ca.account_name',
                DB::raw('ca.id as account_id'),
                DB::raw("'opbinvoice' as transaction_type"),
                DB::raw('MAX(d.due_date) as due_date'),
                DB::raw('MAX(d.sales_person) as imported_sales_person')
            )
            ->groupBy(
                't.transaction_no',
                't.account_id',
                'ca.account_code',
                'ca.account_name',
                'ca.id'
            );

        if ($accountId !== '') {
            $query->where('t.account_id', $accountId);
        }
        if (!empty($fromDate) && !empty($toDate)) {
            $query->whereBetween('t.transaction_date', [$fromDate, $toDate]);
        } elseif (!empty($fromDate)) {
            $query->whereDate('t.transaction_date', '>=', $fromDate);
        } elseif (!empty($toDate)) {
            $query->whereDate('t.transaction_date', '<=', $toDate);
        }

        return collect($salesInvoice->all())
            ->merge($query->get())
            ->sortBy(function ($row) {
                return strtolower((string) ($row->account_name ?? '')) . '|' . (string) ($row->doc_date ?? '');
            })
            ->values();
    }

}

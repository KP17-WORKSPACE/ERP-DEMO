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
use App\SysJournalVoucher;
use App\SysLedgerEntries;
use App\SysPaymentAdjustments;
use App\SysPaymentTerms;
use App\SysPurchaseInvoice;
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

class SysPIAdjustmentReportController extends Controller
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
            $filter_by = "";

$deal_id = '';
$amount = 0;
$overdue = -999999;
$ageing = -999999;




if(!$_POST){
    
    $as_of_date = $this->piAdjustmentAsOfDate($till_date);
    $purchase_invoice = $this->buildPayableAdjustmentTransactions($company_id, $as_of_date);
    $purchase_invoice = $this->applyPayableOutstandingValues($purchase_invoice, $company_id, $as_of_date);
    
    $sys_payment_terms_list = DB::table('sys_payment_terms')->get();

    $sys_adjustment_list = SysPaymentAdjustments::select(
        'bi_doc_number',
        'bi_doc_no',
        db::raw('sum(bi_paid) as total_paid'),
        db::raw('sum(bi_cheque_amount) as cheque_amount'),
        db::raw('IFNULL(max(p.doc_date), "") as p_doc_date'),
db::raw('IFNULL(max(p.cheque_date), "") as p_cheque_date'),
db::raw('IFNULL(max(p.cheque_number), "") as p_cheque_number'),
db::raw('IFNULL(max(j.doc_date), "") as j_doc_date'),
db::raw('IFNULL(max(s.doc_date), "") as s_doc_date')
    )
    ->leftjoin('sys_payment as p','p.doc_number','sys_payment_adjustments.bi_doc_number')
    ->leftjoin('sys_journalvoucher as j','j.doc_number','sys_payment_adjustments.bi_doc_number')
    ->leftjoin('sys_purchase_return as s','s.doc_number','sys_payment_adjustments.bi_doc_number')
    ->where('sys_payment_adjustments.status', 1)
    ->where('sys_payment_adjustments.company_id', $company_id)
    ->wherein('sys_payment_adjustments.account_id', $purchase_invoice->pluck('vendors'))
    ->groupBy('bi_doc_number', 'bi_doc_no')
    ->orderby('p_doc_date', 'asc')->orderby('s_doc_date', 'asc')->orderby('j_doc_date', 'asc')
    ->get()
    ->groupBy('bi_doc_no') // group by bi_doc_no
    ->map(function ($group) {
    $first = $group->first();
    return [
        'bi_doc_no' => $first->bi_doc_no,
        'bi_doc_numbers' => $group->pluck('bi_doc_number')->unique()->implode(', '),
        'total_paid' => $group->sum('total_paid'),
        'cheque_amount' => $group->sum('cheque_amount'), // or $first->cheque_amount if only max expected
        'p_doc_date' => $first->p_doc_date,
        'p_cheque_date' => $first->p_cheque_date,
        'p_cheque_number' => $first->p_cheque_number,
        'j_doc_date' => $first->j_doc_date,
        's_doc_date' => $first->s_doc_date,
    ];
})
    ->values();   

    
        $list_of_unadjusted = SysHelper::get_list_of_payable_unadjusted($purchase_invoice->pluck('vendors'),$company_id,$as_of_date);
        $list_of_unadjusted_jv_to_jv = SysHelper::get_list_of_payable_unadjusted_jv_to_jv($purchase_invoice->pluck('vendors'),$company_id);
        $list_of_unadjusted_pdc = SysHelper::get_list_of_payable_unadjusted_pdc($purchase_invoice->pluck('vendors'),$company_id);
        $list_of_adjusted_pdc = SysHelper::get_list_of_payable_adjusted_pdc($purchase_invoice->pluck('vendors'),$company_id);        
        $opb_balance_amount = SysHelper::get_supplier_opening_balance($purchase_invoice->pluck('vendors'),$as_of_date,$company_id);
        $viewSupport = $this->loadPiAdjustmentViewData();
        extract($viewSupport);

return view('backEnd.outstanding.pi_adjustment_report', compact('purchase_invoice','sys_payment_terms_list','sys_adjustment_list','list_of_unadjusted','list_of_unadjusted_jv_to_jv','list_of_unadjusted_pdc','list_of_adjusted_pdc','opb_balance_amount','from_date','to_date','filter_by','payment_terms_map','payable_finance_rate','as_of_date'));

}

        
            if($_POST){
                $account_id = "";
                

                if ($request->supplier != "") {
                    $account_id=$request->supplier;
                }
                if ($request->from_date != "" && $request->filter_by == "") {
                    
                    $from_date = Carbon::createFromFormat('d/m/Y', $request->from_date)->format('Y-m-d');

                }
                if ($request->from_date != "" && $request->filter_by == "") {
                    $from_date= Carbon::createFromFormat('d/m/Y', $request->from_date)->format('Y-m-d');
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

                
                $as_of_date = $this->piAdjustmentAsOfDate($to_date);
                $purchase_invoice = $this->buildPayableAdjustmentTransactions($company_id, $as_of_date, $account_id, $from_date, $to_date);
                $purchase_invoice = $this->applyPayableOutstandingValues($purchase_invoice, $company_id, $as_of_date);
                
                $sys_payment_terms_list = DB::table('sys_payment_terms')->get();

        $sys_adjustment_list = SysPaymentAdjustments::select(
        'bi_doc_number',
        'bi_doc_no',
        db::raw('sum(bi_paid) as total_paid'),
        db::raw('sum(bi_cheque_amount) as cheque_amount'),
        db::raw('IFNULL(max(p.doc_date), "") as p_doc_date'),
db::raw('IFNULL(max(p.cheque_date), "") as p_cheque_date'),
db::raw('IFNULL(max(p.cheque_number), "") as p_cheque_number'),
db::raw('IFNULL(max(j.doc_date), "") as j_doc_date'),
db::raw('IFNULL(max(s.doc_date), "") as s_doc_date')
    )
    ->leftjoin('sys_payment as p','p.doc_number','sys_payment_adjustments.bi_doc_number')
    ->leftjoin('sys_journalvoucher as j','j.doc_number','sys_payment_adjustments.bi_doc_number')
    ->leftjoin('sys_purchase_return as s','s.doc_number','sys_payment_adjustments.bi_doc_number')
    ->where('sys_payment_adjustments.status', 1)
    ->where('sys_payment_adjustments.company_id', $company_id)
    ->wherein('sys_payment_adjustments.account_id', $purchase_invoice->pluck('vendors'))
    ->groupBy('bi_doc_number', 'bi_doc_no')
    ->orderby('p_doc_date', 'asc')->orderby('s_doc_date', 'asc')->orderby('j_doc_date', 'asc')
    ->get()
    ->groupBy('bi_doc_no') // group by bi_doc_no
    ->map(function ($group) {
    $first = $group->first();
    return [
        'bi_doc_no' => $first->bi_doc_no,
        'bi_doc_numbers' => $group->pluck('bi_doc_number')->unique()->implode(', '),
        'total_paid' => $group->sum('total_paid'),
        'cheque_amount' => $group->sum('cheque_amount'), // or $first->cheque_amount if only max expected
        'p_doc_date' => $first->p_doc_date,
        'p_cheque_date' => $first->p_cheque_date,
        'p_cheque_number' => $first->p_cheque_number,
        'j_doc_date' => $first->j_doc_date,
        's_doc_date' => $first->s_doc_date,
    ];
})
    ->values();   

    
        $list_of_unadjusted = SysHelper::get_list_of_payable_unadjusted($purchase_invoice->pluck('vendors'),$company_id,$as_of_date);
        $list_of_unadjusted_jv_to_jv = SysHelper::get_list_of_payable_unadjusted_jv_to_jv($purchase_invoice->pluck('vendors'),$company_id);
        $list_of_unadjusted_pdc = SysHelper::get_list_of_payable_unadjusted_pdc($purchase_invoice->pluck('vendors'),$company_id);
        $list_of_adjusted_pdc = SysHelper::get_list_of_payable_adjusted_pdc($purchase_invoice->pluck('vendors'),$company_id);        
        $opb_balance_amount = SysHelper::get_supplier_opening_balance($purchase_invoice->pluck('vendors'),$as_of_date,$company_id);
        $viewSupport = $this->loadPiAdjustmentViewData();
        extract($viewSupport);

return view('backEnd.outstanding.pi_adjustment_report', compact('purchase_invoice','sys_payment_terms_list','sys_adjustment_list','list_of_unadjusted','list_of_unadjusted_jv_to_jv','list_of_unadjusted_pdc','list_of_adjusted_pdc','opb_balance_amount','from_date','to_date','filter_by','payment_terms_map','payable_finance_rate','as_of_date'));
            }

        }catch (\Exception $e) {
            return $e;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    private function loadPiAdjustmentViewData()
    {
        $companyId = session('logged_session_data.company_id');
        $payment_terms_map = SysPaymentTerms::where('active_status', 1)->get()->keyBy('id');
        $company_row = SysCompany::find($companyId);
        $payable_finance_rate = 0;
        if ($company_row) {
            $payable_finance_rate = (float) ($company_row->finance_cost_percentage ?? 0);
        }

        return compact('payment_terms_map', 'payable_finance_rate');
    }

    private function piAdjustmentAsOfDate($date = '')
    {
        if (empty($date)) {
            return date('Y-m-d');
        }

        return SysHelper::normalizeToYmd($date) ?: $date;
    }

    private function piAdjustmentMapKey($accountId, $docNo)
    {
        return (string) $accountId . '|' . (string) $docNo;
    }

    private function buildPayableAdjustmentTransactions($companyId, $asOfDate, $accountId = '', $fromDate = '', $toDate = '')
    {
        $companyIds = collect(is_array($companyId) ? $companyId : [$companyId])->filter()->values();
        if ($companyIds->isEmpty()) {
            return collect([]);
        }

        $accountIds = $accountId !== '' ? collect([$accountId]) : SysHelper::get_supplier_list($companyIds->all())->pluck('id');
        $accountIds = collect($accountIds)->filter()->values();
        if ($accountIds->isEmpty()) {
            return collect([]);
        }

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
            ->leftJoin('sys_purchase_invoice as pi', function ($join) {
                $join->on('pi.doc_number', '=', 't.transaction_no')
                    ->on('pi.company_id', '=', 't.company_id');
            })
            ->leftJoin('sys_sales_invoice as si', function ($join) {
                $join->on('si.doc_number', '=', 't.transaction_no')
                    ->on('si.company_id', '=', 't.company_id');
            })
            ->leftJoinSub($invoiceDetails, 'd', function ($join) {
                $join->on('d.trn_id', '=', 't.id');
            })
            ->whereIn('t.company_id', $companyIds)
            ->whereIn('t.account_id', $accountIds)
            ->where('t.status', 1)
            ->whereIn('t.transaction_type', ['purchaseinvoice', 'purchasereturn', 'opbinvoice', 'openingbalance111', 'salesinvoice'])
            ->whereRaw("DATE_FORMAT(t.transaction_date, '%Y-%m-%d') <= ?", [$asOfDate]);

        if (!empty($fromDate) && !empty($toDate)) {
            $query->whereBetween('t.transaction_date', [$fromDate, $toDate]);
        } elseif (!empty($fromDate)) {
            $query->whereDate('t.transaction_date', '>=', $fromDate);
        } elseif (!empty($toDate)) {
            $query->whereDate('t.transaction_date', '<=', $toDate);
        }

        return $query
            ->select(
                DB::raw('MIN(t.id) as id'),
                DB::raw('t.transaction_no as doc_number'),
                DB::raw('MIN(t.transaction_date) as doc_date'),
                DB::raw('MAX(COALESCE(pi.lpo_number, d.po_no)) as lpo_number'),
                DB::raw('MAX(COALESCE(pi.deal_id, d.deal_id)) as deal_id'),
                DB::raw('SUM(t.credit_amount) as amount'),
                DB::raw('SUM(t.debit_amount) as imported_paid'),
                DB::raw('MAX(pi.salesman_name) as salesman_name'),
                DB::raw('MAX(COALESCE(pi.payment_terms, si.payment_terms, d.payment_terms)) as payment_terms'),
                DB::raw('t.account_id as vendors'),
                'ca.account_code',
                'ca.account_name',
                DB::raw('ca.id as account_id'),
                DB::raw('MAX(t.transaction_type) as transaction_type'),
                DB::raw('MAX(d.due_date) as due_date'),
                DB::raw('MAX(d.sales_person) as imported_sales_person')
            )
            ->groupBy('t.transaction_no', 't.account_id', 'ca.account_code', 'ca.account_name', 'ca.id')
            ->orderBy('ca.account_name', 'asc')
            ->orderBy('doc_date', 'asc')
            ->get();
    }

    private function applyPayableOutstandingValues($purchaseInvoice, $companyId, $asOfDate)
    {
        $purchaseInvoice = collect($purchaseInvoice);
        if ($purchaseInvoice->isEmpty()) {
            return $purchaseInvoice;
        }

        $companyIds = collect(is_array($companyId) ? $companyId : [$companyId])->filter()->values();
        $accountIds = $purchaseInvoice->pluck('vendors')->filter()->unique()->values();
        $docNos = $purchaseInvoice->pluck('doc_number')->filter()->unique()->values();

        if ($companyIds->isEmpty() || $accountIds->isEmpty() || $docNos->isEmpty()) {
            return $purchaseInvoice;
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
            ->whereIn('transaction_type', ['purchaseinvoice', 'purchasereturn', 'opbinvoice', 'openingbalance111', 'salesinvoice'])
            ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') <= ?", [$asOfDate])
            ->groupBy('account_id', 'transaction_no')
            ->get()
            ->keyBy(function ($row) {
                return $this->piAdjustmentMapKey($row->account_id, $row->transaction_no);
            });

        if ($ledgerRows->isEmpty()) {
            return $purchaseInvoice;
        }

        $trnNos = $ledgerRows->pluck('transaction_no')->unique()->values();

        $prPaid = DB::table('sys_purchase_return_adjestment')
            ->select('piv_no', DB::raw('SUM(paid_amount) as paid_amount'))
            ->whereIn('piv_no', $trnNos)
            ->groupBy('piv_no')
            ->pluck('paid_amount', 'piv_no');

        $paymentPaid = DB::table('sys_payment as p')
            ->join('sys_payment_adjustments as pa', 'pa.bi_doc_number', '=', 'p.doc_number')
            ->whereIn('pa.account_id', $accountIds)
            ->whereIn('pa.bi_doc_no', $trnNos)
            ->whereIn('p.company_id', $companyIds)
            ->where('p.status', 1)
            ->select('pa.account_id', 'pa.bi_doc_no', DB::raw('SUM(pa.bi_amount) as bi_amount'))
            ->groupBy('pa.account_id', 'pa.bi_doc_no')
            ->get()
            ->mapWithKeys(function ($row) {
                return [$this->piAdjustmentMapKey($row->account_id, $row->bi_doc_no) => (float) $row->bi_amount];
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
                return [$this->piAdjustmentMapKey($row->account_id, $row->bi_doc_no) => (float) $row->bi_amount];
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
                return [$this->piAdjustmentMapKey($row->account_id, $row->bi_doc_no) => (float) $row->bi_amount];
            });

        $returnPaid = DB::table('sys_purchase_return as r')
            ->join('sys_purchase_return_adjestment as ra', 'ra.pri_no', '=', 'r.doc_number')
            ->whereIn('r.vendors', $accountIds)
            ->whereIn('ra.piv_no', $trnNos)
            ->whereIn('r.company_id', $companyIds)
            ->where('r.status', 1)
            ->whereRaw("DATE_FORMAT(r.doc_date, '%Y-%m-%d') <= ?", [$asOfDate])
            ->select('r.vendors', 'ra.piv_no', DB::raw('SUM(ra.paid_amount) as paid_amount'))
            ->groupBy('r.vendors', 'ra.piv_no')
            ->get()
            ->mapWithKeys(function ($row) {
                return [$this->piAdjustmentMapKey($row->vendors, $row->piv_no) => (float) $row->paid_amount];
            });

        return $purchaseInvoice->map(function ($invoice) use ($ledgerRows, $prPaid, $paymentPaid, $jvPaymentPaid, $jvReceiptPaid, $returnPaid) {
            $key = $this->piAdjustmentMapKey($invoice->vendors ?? '', $invoice->doc_number ?? '');
            $ledger = $ledgerRows->get($key);

            if (!$ledger) {
                $paid = (float) ($invoice->imported_paid ?? 0);
                $amount = (float) ($invoice->amount ?? 0);
                $invoice->payable_credit_amount = $amount;
                $invoice->payable_debit_amount = 0;
                $invoice->payable_adjustments = $paid;
                $invoice->payable_balance = $amount - abs($paid);
                $invoice->payable_ageing_balance = $invoice->payable_balance;
                $invoice->payable_visible = abs($invoice->payable_balance) >= 0.01;
                return $invoice;
            }

            $docNo = (string) $ledger->transaction_no;
            $credit = (float) ($ledger->credit_amount ?? 0);
            $debit = (float) ($ledger->debit_amount ?? 0);

            $opbImportPaid = 0.0;
            if (($ledger->transaction_type ?? '') === 'opbinvoice') {
                $opbImportPaid = $debit;
            }

            $paid = (float) ($prPaid[$docNo] ?? 0)
                + (float) ($paymentPaid[$key] ?? 0)
                + (float) ($jvPaymentPaid[$key] ?? 0)
                + $opbImportPaid
                - ((float) ($jvReceiptPaid[$key] ?? 0) - (float) ($returnPaid[$key] ?? 0));

            $isHidden = false;
            if (strpos($docNo, 'PR') !== false && round($debit, 2) >= round($paid, 2)) {
                $isHidden = true;
            }

            $rowBalance = $credit - abs($paid);
            if (strpos($docNo, 'PR') !== false) {
                $rowBalance = $debit - abs($paid);
            }

            $invoice->amount = $credit;
            $invoice->imported_paid = ($ledger->transaction_type ?? '') === 'opbinvoice' ? $debit : ($invoice->imported_paid ?? 0);
            $invoice->transaction_type = $ledger->transaction_type ?: ($invoice->transaction_type ?? '');
            $invoice->payable_credit_amount = $credit;
            $invoice->payable_debit_amount = $debit;
            $invoice->payable_adjustments = $paid;
            $invoice->payable_balance = $rowBalance;
            $invoice->payable_ageing_balance = $rowBalance;
            $invoice->payable_visible = ((number_format($credit, 2, '.', '') != number_format($paid, 2, '.', '')) || $debit > 0) && !$isHidden;

            if (!empty($ledger->transaction_date)) {
                $invoice->doc_date = $ledger->transaction_date;
            }

            return $invoice;
        });
    }

    private function appendPayableLedgerTransactions($purchaseInvoice, $companyId, $asOfDate, $accountId = '', $fromDate = '', $toDate = '')
    {
        $purchaseInvoice = collect($purchaseInvoice);

        $companyIds = collect(is_array($companyId) ? $companyId : [$companyId])->filter()->values();
        if ($companyIds->isEmpty()) {
            return $purchaseInvoice;
        }

        $accountIds = $purchaseInvoice->pluck('vendors')->filter()->unique()->values();
        if ($accountId !== '') {
            $accountIds = collect([$accountId]);
        }
        if ($accountIds->isEmpty()) {
            return $purchaseInvoice;
        }

        $query = DB::table('sys_chartofaccounts_transaction as t')
            ->join('sys_chartofaccounts as ca', 'ca.id', '=', 't.account_id')
            ->whereIn('t.company_id', $companyIds)
            ->whereIn('t.account_id', $accountIds)
            ->where('t.status', 1)
            ->whereIn('t.transaction_type', ['purchasereturn', 'salesinvoice', 'openingbalance111'])
            ->whereRaw("DATE_FORMAT(t.transaction_date, '%Y-%m-%d') <= ?", [$asOfDate]);

        if (!empty($fromDate) && !empty($toDate)) {
            $query->whereBetween('t.transaction_date', [$fromDate, $toDate]);
        } elseif (!empty($fromDate)) {
            $query->whereDate('t.transaction_date', '>=', $fromDate);
        } elseif (!empty($toDate)) {
            $query->whereDate('t.transaction_date', '<=', $toDate);
        }

        $rows = $query
            ->select(
                DB::raw('MIN(t.id) as id'),
                DB::raw('t.transaction_no as doc_number'),
                DB::raw('MIN(t.transaction_date) as doc_date'),
                DB::raw('NULL as lpo_number'),
                DB::raw('NULL as deal_id'),
                DB::raw('SUM(t.credit_amount) as amount'),
                DB::raw('0 as imported_paid'),
                DB::raw('NULL as salesman_name'),
                DB::raw('NULL as payment_terms'),
                DB::raw('t.account_id as vendors'),
                'ca.account_code',
                'ca.account_name',
                DB::raw('ca.id as account_id'),
                DB::raw('MAX(t.transaction_type) as transaction_type'),
                DB::raw('NULL as due_date'),
                DB::raw('NULL as imported_sales_person')
            )
            ->groupBy('t.transaction_no', 't.account_id', 'ca.account_code', 'ca.account_name', 'ca.id')
            ->get();

        if ($rows->isEmpty()) {
            return $purchaseInvoice;
        }

        $existingKeys = $purchaseInvoice->map(function ($row) {
            return $this->piAdjustmentMapKey($row->vendors ?? '', $row->doc_number ?? '');
        })->filter()->flip();

        $merged = $purchaseInvoice;
        foreach ($rows as $row) {
            $key = $this->piAdjustmentMapKey($row->vendors ?? '', $row->doc_number ?? '');
            if (!$existingKeys->has($key)) {
                $merged->push($row);
            }
        }

        return $merged
            ->sortBy(function ($row) {
                return strtolower((string) ($row->account_name ?? '')) . '|' . (string) ($row->doc_date ?? '');
            })
            ->values();
    }

    private function appendPayableImportedInvoices($purchaseInvoice, $companyId, $accountId = '', $fromDate = '', $toDate = '')
    {
        $accountIds = SysHelper::get_supplier_list(is_array($companyId) ? $companyId : [$companyId])->pluck('id');
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
                DB::raw('SUM(t.credit_amount) as amount'),
                DB::raw('SUM(t.debit_amount) as imported_paid'),
                DB::raw('NULL as salesman_name'),
                DB::raw('MAX(d.payment_terms) as payment_terms'),
                DB::raw('t.account_id as vendors'),
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

        return collect($purchaseInvoice->all())
            ->merge($query->get())
            ->sortBy(function ($row) {
                return strtolower((string) ($row->account_name ?? '')) . '|' . (string) ($row->doc_date ?? '');
            })
            ->values();
    }

}

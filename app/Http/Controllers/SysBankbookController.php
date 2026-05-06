<?php

namespace App\Http\Controllers;

use App\SysChartofAccounts;
use App\SysAccountType;
use App\ApiBaseMethod;
use App\Helper;
use App\Role;
use App\SmStaff;
use App\SmSupplier;
use App\SysAccountGroup;
use App\SysAccountGroupSub;
use App\SysAccountGroupSub2;
use App\SysChartofAccountsTransaction;
use App\SysCountries;
use App\SysCountryCode;
use App\SysCustSuppl;
use App\SysHelper;
use App\SysPayment;
use App\SysPaymentTerms;
use App\SysReceipt;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\Else_;
use Carbon\Carbon;
class SysBankbookController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
    }

    public function index(Request $request)
    {
        try {
            $accounts = SysHelper::get_bank_account();
            if ($accounts === 0 || $accounts === null || !is_countable($accounts)) {
                $accounts = collect();
            }
            session()->forget('gl_data');

            if (session('bank_book_session_data')) {
                $from_date = session('bank_book_session_data.from_date');
                $to_date = session('bank_book_session_data.to_date');
                $account_id = session('bank_book_session_data.account_id');
            } else {
                $from_date = date('Y-m-01');
                $to_date = date('Y-m-d');
                if (is_countable($accounts) && count($accounts) > 0) {
                    $account_id = $accounts[0]->id;
                } else {
                    $account_id = "";
                }
            }
            $filter_by="";
            $pdc_filter="with_pdc";
            $data = [];
            if ($_POST) {

                if ($request->from_date != "" && $request->filter_by == "") {
                    $from_date= SysHelper::normalizeToYmd($request->from_date);
                }
                if ($request->to_date != "" && $request->filter_by == "") {
                    $to_date= SysHelper::normalizeToYmd($request->to_date); 
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

                if($request->pdc_filter == "with_pdc"){
                    $pdc_filter = 'with_pdc';
                }elseif($request->pdc_filter == "without_pdc"){
                    $pdc_filter = 'without_pdc';
                }elseif($request->pdc_filter == "hide_pdc"){
                    $pdc_filter = 'hide_pdc';
                }else{
                    $pdc_filter = '';
                }


                // $from_date = $from_date
                //     ? Carbon::createFromFormat('d/m/Y', $from_date)->format('Y-m-d')
                //     : null;
                // $to_date = $to_date
                //     ? Carbon::createFromFormat('d/m/Y', $to_date)->format('Y-m-d')
                //     : null;
                $account_id = $request->account_id;

                $data1 = [
                    'from_date' => $from_date,
                    'to_date' => $to_date,
                    'account_id' => $account_id,
                ];
                session()->put('bank_book_session_data', $data1);
            }

            $queryra1 = "SELECT cat.transaction_no, cat.entry_no FROM sys_chartofaccounts_transaction AS cat
                    JOIN sys_chartofaccounts AS ca ON ca.id=cat.account_id
                    WHERE cat.account_id = '" . $account_id . "'
                    and DATE_FORMAT(cat.transaction_date, '%Y-%m-%d') >= '" . $from_date . "'
                    and DATE_FORMAT(cat.transaction_date, '%Y-%m-%d') <= '" . $to_date . "' order by cat.transaction_date asc";
            $resultsra1 = DB::select($queryra1);

            $resultopb2 = DB::table('sys_chartofaccounts_transaction AS cat')
                ->select('cat.account_id', 'ca.account_name', DB::raw('SUM(cat.debit_amount) as debit_total'), DB::raw('SUM(cat.credit_amount) as credit_total'))
                ->join('sys_chartofaccounts AS ca', 'ca.id', 'cat.account_id')
                ->where('cat.account_id', $account_id)
                //->wherein('transaction_type',['openingbalance','openingstock'])
                ->whereRaw("DATE_FORMAT(cat.transaction_date, '%Y-%m-%d') < '" . $from_date . "'")
                ->where('cat.status', 1)
                ->groupby('cat.account_id', 'ca.account_name')
                ->get();

            // $resultopb_test = DB::table('sys_chartofaccounts_transaction AS cat')
            // ->select('cat.account_id','ca.account_name','cat.debit_amount','cat.credit_amount','cat.transaction_no')
            // ->join('sys_chartofaccounts AS ca', 'ca.id', 'cat.account_id')
            // ->where('cat.account_id', $account_id)
            // //->wherein('transaction_type',['openingbalance','openingstock'])
            // ->whereRaw("DATE_FORMAT(cat.transaction_date, '%Y-%m-%d') < '" . $from_date . "'")
            // ->where('cat.status',1)
            // //->groupby('cat.account_id','ca.account_name')
            // ->get();
            // return $resultopb_test;

            if (count($resultopb2) > 0) {
                foreach ($resultopb2 as $resopb) {
                    $data[] = [
                        'account_id' => $resopb->account_id,
                        'account_name' => $resopb->account_name,
                        'transaction_no' => 'OPB',
                        'transaction_date' => $from_date,
                        'debit_amount' => $resopb->debit_total,
                        'credit_amount' => $resopb->credit_total,
                        'entry_no' => 1,
                        'remarks' => 'Openning Balance',
                        'transaction_id' => '0',
                    ];
                }
            }

            $resultopb3 = DB::table('sys_chartofaccounts_transaction AS cat')
                ->select('cat.account_id', 'ca.account_name', 'cat.transaction_no', 'cat.transaction_date', 'cat.debit_amount', 'cat.credit_amount', 'cat.entry_no', 'cat.remarks')
                ->join('sys_chartofaccounts AS ca', 'ca.id', 'cat.account_id')
                ->where('cat.account_id', $account_id)
                ->wherein('transaction_type', ['openingbalance', 'openingstock'])
                ->whereRaw("DATE_FORMAT(cat.transaction_date, '%Y-%m-%d') >= '" . $from_date . "'")
                ->whereRaw("DATE_FORMAT(cat.transaction_date, '%Y-%m-%d') <= '" . $to_date . "'")
                ->where('cat.status', 1)
                ->get();

            if (count($resultopb3) > 0) {
                foreach ($resultopb3 as $resopb) {
                    $data[] = [
                        'account_id' => $resopb->account_id,
                        'account_name' => $resopb->account_name,
                        'transaction_no' => $resopb->transaction_no,
                        'transaction_date' => $from_date,
                        'debit_amount' => $resopb->debit_amount,
                        'credit_amount' => $resopb->credit_amount,
                        'entry_no' => 1,
                        'remarks' => $resopb->remarks,
                        'transaction_id' => '0',
                    ];
                }
            }


            if (count($resultsra1) > 0) {
                foreach ($resultsra1 as $res1) {
                    $resultsra2 = DB::table('sys_chartofaccounts_transaction AS cat')
                        ->select('cat.id', 'cat.account_id', 'ca.account_name', 'cat.transaction_no', 'cat.transaction_id', 'cat.transaction_date', 'cat.debit_amount', 'cat.credit_amount', 'cat.entry_no', 'cat.remarks', 'cat.is_main_account')
                        ->join('sys_chartofaccounts AS ca', 'ca.id', 'cat.account_id')
                        ->where('cat.transaction_no', $res1->transaction_no)
                        ->where('entry_no', $res1->entry_no)
                        ->where('cat.status', 1)
                        ->wherenotin('transaction_type', ['openingbalance', 'openingstock'])
                        ->orderby('cat.transaction_date', 'asc')
                        ->get();


                    if (count($resultsra2) > 0) {
                        $dt = $resultsra2->where('account_id', $account_id)->first();
                        if (isset($dt)) {
                            if ($dt->is_main_account == 1) {
                                $bdata = $resultsra2->where('account_id', '!=', $account_id);
                                foreach ($bdata as $dt) {
                                    $data[] = [
                                        'account_id' => $account_id,
                                        'account_name' => $dt->account_name,
                                        'transaction_no' => $dt->transaction_no,
                                        'transaction_date' => $dt->transaction_date,
                                        'debit_amount' => $dt->credit_amount,
                                        'credit_amount' => $dt->debit_amount,
                                        'entry_no' => $dt->entry_no,
                                        'remarks' => $dt->remarks,
                                        'transaction_id' => $dt->transaction_id,
                                    ];
                                }
                            } else {
                                //$data[] = SysHelper::get_ledger_data($resultsra2, $account_id); 
                                //return $resultsra2[0]->transaction_no;
                                $ret = SysHelper::get_ledger_data_formated_bank_book($resultsra2[0]->transaction_no, $resultsra2[0]->account_id, $resultsra2);
                                $data[] = [
                                    'account_id' => $account_id,
                                    'account_name' => $ret[2],
                                    'transaction_no' => $resultsra2[0]->transaction_no,
                                    'transaction_date' => $resultsra2[0]->transaction_date,
                                    'debit_amount' => $ret[0],
                                    'credit_amount' => '0.00',
                                    'entry_no' => $resultsra2[0]->entry_no,
                                    'remarks' => $ret[1],
                                    'transaction_id' => $resultsra2[0]->transaction_id,
                                ];
                            }
                        } else {
                            $data[] = SysHelper::get_ledger_data($resultsra2, $account_id);
                        }
                    }
                }
            }

            $com_id = session('logged_session_data.company_id');
            $receipt_pdc_list = SysReceipt::select('sys_receipt.id','doc_date', 'doc_number', 'receipt_mode', 'cat.account_id as account_id', 'cat.debit_amount', 'cat.credit_amount', 'cheque_date', 'cheque_number', 'receipt_date', 'cat.remarks', DB::raw('GROUP_CONCAT(adj.bi_doc_no) as bi_doc_no'), 'ca.account_name')
                ->join('sys_chartofaccounts_transaction as cat', 'cat.transaction_no', 'sys_receipt.doc_number')
                ->join('sys_chartofaccounts as ca', 'ca.id', 'cat.account_id')
                ->leftjoin('sys_receipt_adjustments as adj', 'adj.bi_doc_number', 'sys_receipt.doc_number')
                ->where('sys_receipt.pdc_removed_os', 1)
                ->where('sys_receipt.status', 1)->where('sys_receipt.mode', 2)->where('sys_receipt.receipt_through', 3)->where('sys_receipt.company_id', $com_id)
                ->where('sys_receipt.receipt_mode', $account_id)
                ->whereRaw("DATE_FORMAT(sys_receipt.cheque_date, '%Y-%m-%d') >= ?", [$from_date])
                ->whereRaw("DATE_FORMAT(sys_receipt.cheque_date, '%Y-%m-%d') <= ?", [$to_date])
                ->wherenotin('cat.account_id', [$account_id])
                ->groupby('doc_date', 'doc_number', 'receipt_mode', 'cat.account_id', 'cat.debit_amount', 'cat.credit_amount', 'cheque_date', 'cheque_number', 'receipt_date', 'cat.remarks')
                ->orderBy('cheque_date', 'asc')
                ->get();

            $payment_pdc_list = SysPayment::select('sys_payment.id','doc_date', 'doc_number', 'payment_mode', 'cat.account_id', 'cat.debit_amount', 'cat.credit_amount', 'cheque_date', 'cheque_number', 'payment_date', 'cat.remarks', DB::raw('GROUP_CONCAT(adj.bi_doc_no) as bi_doc_no'), 'ca.account_name')
                ->join('sys_chartofaccounts_transaction as cat', 'cat.transaction_no', 'sys_payment.doc_number')
                ->join('sys_chartofaccounts as ca', 'ca.id', 'cat.account_id')
                ->leftjoin('sys_payment_adjustments as adj', 'adj.bi_doc_number', 'sys_payment.doc_number')
                ->where('sys_payment.pdc_removed_os', 1)
                ->where('sys_payment.status', 1)->where('sys_payment.mode', 2)->where('sys_payment.payment_through', 3)
                ->where('sys_payment.company_id', $com_id)
                ->where('sys_payment.payment_mode', $account_id)
                ->whereRaw("DATE_FORMAT(sys_payment.cheque_date, '%Y-%m-%d') >= ?", [$from_date])
                ->whereRaw("DATE_FORMAT(sys_payment.cheque_date, '%Y-%m-%d') <= ?", [$to_date])
                ->wherenotin('cat.account_id', [$account_id])
                ->groupby('doc_date', 'doc_number', 'payment_mode', 'cat.account_id', 'cat.debit_amount', 'cat.credit_amount', 'cheque_date', 'cheque_number', 'payment_date', 'cat.remarks')
                ->orderBy('cheque_date', 'asc')
                ->get();
            //return $payment_pdc_list;


            return view('backEnd.bankbook.view', compact('data', 'accounts', 'account_id', 'from_date', 'to_date', 'filter_by', 'receipt_pdc_list', 'payment_pdc_list','pdc_filter'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
}
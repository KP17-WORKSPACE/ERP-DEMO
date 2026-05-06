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
    $sys_payment_terms_list = DB::table('sys_payment_terms')->get();

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

    
        $list_of_unadjusted = SysHelper::get_list_of_unadjusted($sales_invoice->pluck('customer'),$company_id);
        $list_of_unadjusted_jv_to_jv = SysHelper::get_list_of_unadjusted_jv_to_jv($sales_invoice->pluck('customer'),$company_id);
        $list_of_unadjusted_pdc = SysHelper::get_list_of_unadjusted_pdc($sales_invoice->pluck('customer'),$company_id);
        $list_of_adjusted_pdc = SysHelper::get_list_of_adjusted_pdc($sales_invoice->pluck('customer'),$company_id);        
        $opb_balance_amount = SysHelper::get_customer_opening_balance($sales_invoice->pluck('customer'),date('Y-m-d'),$company_id);

return view('backEnd.outstanding.si_adjustment_report', compact('sales_invoice','sys_payment_terms_list','sys_adjustment_list','list_of_unadjusted','list_of_unadjusted_jv_to_jv','list_of_unadjusted_pdc','list_of_adjusted_pdc','opb_balance_amount'));

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
    $sys_payment_terms_list = DB::table('sys_payment_terms')->get();

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

    
        $list_of_unadjusted = SysHelper::get_list_of_unadjusted($sales_invoice->pluck('customer'),$company_id);
        $list_of_unadjusted_jv_to_jv = SysHelper::get_list_of_unadjusted_jv_to_jv($sales_invoice->pluck('customer'),$company_id);
        $list_of_unadjusted_pdc = SysHelper::get_list_of_unadjusted_pdc($sales_invoice->pluck('customer'),$company_id);
        $list_of_adjusted_pdc = SysHelper::get_list_of_adjusted_pdc($sales_invoice->pluck('customer'),$company_id);        
        $opb_balance_amount = SysHelper::get_customer_opening_balance($sales_invoice->pluck('customer'),date('Y-m-d'),$company_id);

return view('backEnd.outstanding.si_adjustment_report', compact('sales_invoice','sys_payment_terms_list','sys_adjustment_list','list_of_unadjusted','list_of_unadjusted_jv_to_jv','list_of_unadjusted_pdc','list_of_adjusted_pdc','opb_balance_amount','from_date','to_date'));
            }

        }catch (\Exception $e) {
            return $e;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

}
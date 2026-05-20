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
use App\SysPaymentTerms;
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

class SysReceivableOutstandingController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function summary(Request $request)
    {
        try {
            $company_id = session('logged_session_data.company_id');
            $user_id = SysHelper::get_sales_persons();


            //$os = [$adj_sum,$adj_count, $adj_due_sum,$adj_due_count, $adj_over_due_sum,$adj_over_due_count];                    
            //$due_by_days=[$due_30_amount,$due_30_count, $due_60_amount,$due_60_count, $due_90_amount,$due_90_count, $due_91_amount,$due_91_count];

            $receivable = SysCrmReportController::get_receivable_os_report_summary($company_id);
            $os_det = $receivable[0];
            $due_by_det = $receivable[3];
            //return $os_det;
            //return $due_by_det;
            return view('backEnd.outstanding.receivableoutstanding_summary', compact('os_det', 'due_by_det'));

        } catch (\Throwable $th) {
            return $th;
        }

    }

    public function index(Request $request)
    {
        try {

            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $data = [];
            $data_all = [];
            $accounts_select = SysHelper::get_customer_list($company_id);
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

            $deal_id = '';
            $amount = 0;
            $overdue = -999999;
            $ageing = -999999;
            $followup_from = "";     // new filter range
            $followup_to = "";
            $ctrl_account_id = "";
            $ctrl_asofdate = "";
            $ctrl_doc_no = "";
            $ctrl_deal_id = "";
            $ctrl_amount = "";
            $ctrl_sales_person = "";
            $ctrl_overdue = "";
            $ctrl_ageing = "";
            $ctrl_followup_from = "";
            $ctrl_followup_to = "";
            $ctrl_list_option = "";
            $ctrl_intext = "";
            $ctrl_basic_search = "1";
            $accounts = $accounts_select;
            $list_of_unadjusted = collect([]);
            $list_of_unadjusted_jv_to_jv = collect([]);
            $list_of_unadjusted_pdc = collect([]);
            $list_of_adjusted_pdc = collect([]);
            $opb_balance_amount = 0;
            $is_view_all_cust = false;
            $first_load = true;



            if (!$_POST) {

            $first_load = true;

                if (SysHelper::get_pagination_post($request)) {
                    $com_id = $request->com;
                    $company_id = [$request->com];
                }

                $query1 = SysChartofAccountsTransaction::select('transaction_no', 'account_id')->where('status', 1)->where('company_id', $com_id)->wherein('transaction_type', ['salesinvoice', 'salesreturn', 'opbinvoice', 'openingbalance111']);

                if (SysHelper::get_pagination_post($request)) {
                    if ($request->sales != "") {
                        $inv_trn_no1 = SysSalesInvoice::where('sales_man', $request->sales)->where('status', 1)->where('company_id', $com_id)->pluck('doc_number');
                        $inv_trn_no2 = SysSalesReturn::where('sales_man', $request->sales)->where('status', 1)->where('company_id', $com_id)->pluck('doc_number');
                        $inv_trn_no = $inv_trn_no2->merge($inv_trn_no1);
                        if (count($inv_trn_no) > 0) {
                            $query1->wherein('transaction_no', $inv_trn_no);
                        } else {
                            $query1->where('transaction_no', '0');
                        }

                    }
                }

                $BigData1 = $query1->distinct()->get();
                $BigData = SysChartofAccountsTransaction::where('status', 1)->where('company_id', $com_id)->wherein('transaction_type', ['salesinvoice', 'salesreturn', 'opbinvoice', 'openingbalance111']);


                if (count($accounts) > 0) {
                    foreach ($accounts as $a) {
                        $abc = clone $BigData1;
                        $transaction_no = $abc->where('account_id', $a->id)->pluck('transaction_no');
                        if (count($transaction_no) > 0) {
                            $selectBigData = clone $BigData;
                            $data_query = $selectBigData->select(...$this->receivableOsAggregateSelect((string) $a->id))->wherein('company_id', $company_id);
                            $data_query->where("account_id", $a->id)->where('status', 1);
                            $data_query->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') <= '" . $till_date . "'");
                            $data_query->wherein('transaction_type', ['salesinvoice', 'salesreturn', 'opbinvoice', 'openingbalance111']);

                            if (SysHelper::get_pagination_post($request)) {
                                if ($request->over != "") {
                                    $overdue_list = SysHelper::get_receivable_os_by_overdue($request->over, $a->id, SysHelper::normalizeToYmd($till_date));
                                    if (count($overdue_list) > 0) {
                                        $data_query->wherein('transaction_no', $overdue_list);
                                    } else {
                                        $query1->where('transaction_no', '0');
                                    }
                                }
                            }


                            $dq = $data_query->groupby('transaction_date', 'transaction_id', 'transaction_no')->orderby('transaction_date', 'asc')->get();
                            if (count($dq) > 0) {
                                $data_all[] = $dq;
                            }
                        }
                    }


                    $list_of_unadjusted = SysHelper::get_list_of_unadjusted($accounts->pluck('id'), $com_id);
                    $list_of_unadjusted_jv_to_jv = SysHelper::get_list_of_unadjusted_jv_to_jv($accounts->pluck('id'), $com_id);
                    $list_of_unadjusted_pdc = SysHelper::get_list_of_unadjusted_pdc($accounts->pluck('id'), $com_id);
                    $list_of_adjusted_pdc = SysHelper::get_list_of_adjusted_pdc($accounts->pluck('id'), $com_id);

                    $opb_balance_amount = SysHelper::get_customer_opening_balance($accounts->pluck('id'), date('Y-m-d', strtotime('+1 day')), $com_id);
                }

                
                
                $data_all = [];
            }


            if ($_POST) {
                $first_load = false;
          


                $account_id = $request->account_id;

                if(!$request->exists('account_id')){
                    // then make it view all customer
                    $account_id = ["view_all_cust"];
                }
                

                $till_date = SysHelper::normalizeToYmd($request->till_date);
                $ctrl_account_id = $request->account_id;
                $ctrl_asofdate = $request->till_date;




                $deal_id = $request->deal_id;

                $amount = $request->amount;

                $overdue = $request->overdue;

                $ageing = $request->ageing;

                // follow-up date normalization for filter
                $followup_from = SysHelper::normalizeToYmd($request->followup_from);
                $followup_to = SysHelper::normalizeToYmd($request->followup_to);
                $ctrl_followup_from = $request->followup_from;
                $ctrl_followup_to = $request->followup_to;

                if ($account_id != 0) {

                    if (is_array($account_id) && in_array("view_all_cust", $account_id)) {   
                            $is_view_all_cust = true;
                        $account_id = $accounts_select->pluck('id');
                    }
                    $accounts = SysChartofAccounts::select('sys_chartofaccounts.id', 'sys_chartofaccounts.account_name', 'sys_chartofaccounts.account_code','sys_chartofaccounts.grn_select')
                        ->wherein('sys_chartofaccounts.id', $account_id)->get();

                }

                

               

                if ($request->list_in_ex != "") {
                    $accounts = SysChartofAccounts::select('sys_chartofaccounts.id', 'sys_chartofaccounts.account_name', 'sys_chartofaccounts.account_code','sys_chartofaccounts.grn_select')
                        ->where('sys_chartofaccounts.internal', $request->list_in_ex)->get();
                    $ctrl_intext = $request->list_in_ex;
                }

                if($request->list_in_basic != ""){
                    $ctrl_basic_search = $request->list_in_basic;
                }


                if($request->list_option == "grn"){

                   $accounts = $accounts->where('grn_select', "yes");
                   $ctrl_list_option = $request->list_option;

                            
                }

                // filter accounts by comment follow-up date range if provided
                if ($request->followup_from || $request->followup_to) {
                    $ctrl_followup_from = $request->followup_from;
                    $ctrl_followup_to = $request->followup_to;
                    $queryComments = DB::table('outstand_comments')->where('company_id', $com_id);
                    if ($request->followup_from) {
                        $ff = SysHelper::normalizeToYmd($request->followup_from);
                        $queryComments->whereRaw("DATE_FORMAT(followup_date,'%Y-%m-%d') >= '" . $ff . "'");
                    }
                    if ($request->followup_to) {
                        $tt = SysHelper::normalizeToYmd($request->followup_to);
                        $queryComments->whereRaw("DATE_FORMAT(followup_date,'%Y-%m-%d') <= '" . $tt . "'");
                    }
                    $acctIds = $queryComments->pluck('comment_id')->unique()->toArray();
                    if (count($acctIds) > 0) {
                        $accounts = $accounts->whereIn('id', $acctIds);
                    } else {
                        $accounts = collect([]);
                    }
                }

                $bigData = SysChartofAccountsTransaction::select(...$this->receivableOsAggregateSelect('account_id'))->wherein('company_id', $company_id)->where('status', 1)->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') <= '" . $till_date . "'")->wherein('transaction_type', ['salesinvoice', 'salesreturn', 'opbinvoice', 'openingbalance111'])->groupby('transaction_date', 'transaction_id', 'transaction_no', 'account_id');

                foreach ($accounts as $a) {
                    $bdata = clone $bigData;
                    $data_query = $bdata->where("account_id", $a->id);

                    if ($request->transaction_no != "") {
                        $data_query->where("transaction_no", $request->transaction_no);
                        $ctrl_doc_no = $request->transaction_no;
                    }

                    if ($request->deal_id != "") {
                        $deal_id = SysHelper::get_dealid_from_code($request->deal_id);
                        $deal_trn_no = SysSalesInvoice::where('deal_id', $deal_id)->where('status', 1)->where('company_id', $com_id)->where("customer", $a->id)->pluck('doc_number');
                        $deal_trn_no = $deal_trn_no->merge($this->opbTransactionNosByDeal($com_id, $a->id, $request->deal_id, $deal_id))->unique()->values();
                        if (count($deal_trn_no) > 0) {
                            $data_query->wherein('transaction_no', $deal_trn_no);
                        } else {
                            $data_query->where('transaction_no', '0');
                        }
                        $ctrl_deal_id = $request->deal_id;
                    }

                    if ($request->amount != "") {
                        $amount_d_trn_no = SysChartofAccountsTransaction::where("account_id", $a->id)->where('status', 1)
                            ->whereRaw("debit_amount = " . $request->amount . "")->where('company_id', $com_id)->pluck('transaction_no');

                        if (count($amount_d_trn_no) > 0) {
                            $data_query->wherein('transaction_no', $amount_d_trn_no);
                        } else {
                            $data_query->where('transaction_no', '0');
                        }
                        $ctrl_amount = $request->amount;
                    }

                    if ($request->sales_person != "") {
                        $inv_trn_no1 = SysSalesInvoice::wherein('sales_man', $request->sales_person)->where('status', 1)->where('company_id', $com_id)->where("customer", $a->id)->pluck('doc_number');
                        $inv_trn_no2 = SysSalesReturn::wherein('sales_man', $request->sales_person)->where('status', 1)->where('company_id', $com_id)->where("customer", $a->id)->pluck('doc_number');
                        $inv_trn_no = $inv_trn_no2->merge($inv_trn_no1)->merge($this->opbTransactionNosBySalesPerson($com_id, $a->id, (array) $request->sales_person))->unique()->values();
                        if (count($inv_trn_no) > 0) {
                            $data_query->wherein('transaction_no', $inv_trn_no);
                        } else {
                            $data_query->where('transaction_no', '0');
                        }
                        $ctrl_sales_person = $request->sales_person;
                    }

                    if ($overdue != "") {
                        $overdue_list = SysHelper::get_receivable_os_by_overdue($overdue, $a->id, SysHelper::normalizeToYmd($till_date));
                        if (count($overdue_list) > 0) {
                            $data_query->wherein('transaction_no', $overdue_list);
                        } else {
                            $data_query->where('transaction_no', '0');
                        }
                        $ctrl_overdue = $request->overdue;
                    }

                    if ($ageing != "") {
                        $ageing_list = SysHelper::get_receivable_os_by_ageing($ageing, $a->id, SysHelper::normalizeToYmd($till_date));
                        if (count($ageing_list) > 0) {
                            $data_query->wherein('transaction_no', $ageing_list);
                        } else {
                            $data_query->where('transaction_no', '0');
                        }
                        $ctrl_ageing = $request->ageing;
                    }
                    if ($request->list_option != "") {
                        $list_option = $request->list_option;
                        if ($list_option == "consolidated") {
                            $list_option = "show";
                        }
                        $ctrl_list_option = $request->list_option;
                    }

                    $dq = $data_query->orderby('transaction_date', 'asc')->get();
                    if (count($dq) > 0) {
                        $data_all[] = $dq;
                    }
                }

                $list_of_unadjusted = SysHelper::get_list_of_unadjusted($account_id, $com_id);
                $list_of_unadjusted_jv_to_jv = SysHelper::get_list_of_unadjusted_jv_to_jv($account_id, $com_id);
                $list_of_unadjusted_pdc = SysHelper::get_list_of_unadjusted_pdc($account_id, $com_id);
                $list_of_adjusted_pdc = SysHelper::get_list_of_adjusted_pdc($account_id, $com_id);




                if ($request->amount != "") {
                    $amount = $request->amount;
                } else {
                    $amount = 0;
                }
                if ($request->overdue != "") {
                    $overdue = $request->overdue;
                } else {
                    $overdue = -999999;
                }
                if ($request->ageing != "") {
                    $ageing = $request->ageing;
                } else {
                    $ageing = -999999;
                }

                $opb_balance_amount = SysHelper::get_customer_opening_balance($accounts->pluck('id'), date('Y-m-d', strtotime('+1 day')), $com_id);
            }

            $data_adjestment_all = DB::table('sys_sales_return_adjestment')->select('srn_no', DB::raw('sum(paid_amount) as paid_amount'))->groupby('srn_no');

            $data_receipt_all = DB::table('sys_receipt as r')->select('ra.bi_doc_no', 'r.doc_number', 'ra.bi_amount', 'r.receipt_through', 'r.receipt_date', 'r.cheque_number', 'r.cheque_bank_name', 'ra.account_id')->join('sys_receipt_adjustments as ra', 'ra.bi_doc_number', 'r.doc_number')->where('r.company_id', $com_id)->where('r.status', 1);
            //return $data_receipt_all->get();

            $data_receipt_opb = DB::table('sys_receipt_adjustments as ra')->select('ra.bi_doc_no', 'ra.bi_doc_number as doc_number', 'ra.bi_amount', 'ra.transaction_type as receipt_through', 'ra.bi_doc_date as receipt_date', 'ra.bi_doc_number as cheque_number', 'ra.bi_doc_number as cheque_bank_name', 'ra.account_id')->where('ra.transaction_type', 'openingbalance')->where('ra.company_id', $com_id)->where('ra.status', 1);

            $data_receipt2_all = DB::table('sys_journalvoucher as j')->select('ra.bi_doc_no', 'j.doc_number', 'ra.bi_amount', 'j.doc_date', 'ra.account_id')
                ->join('sys_receipt_adjustments as ra', 'ra.bi_doc_number', 'j.doc_number')->where('j.company_id', $com_id)->where('j.status', 1);
            //return $data_receipt2_all->get();

            $data_receipt3_all = DB::table('sys_journalvoucher as j')->select('pa.bi_doc_no', 'j.doc_number', 'pa.bi_amount', 'j.doc_date', 'pa.account_id')
                ->join('sys_payment_adjustments as pa', 'pa.bi_doc_number', 'j.doc_number')->where('j.company_id', $com_id)->where('j.status', 1);

            $data_return_all = DB::table('sys_sales_return as r')->select('ra.siv_no', 'r.doc_number', 'ra.paid_amount', 'r.doc_date', 'r.customer', 'ra.srn_no')
                ->join('sys_sales_return_adjestment as ra', 'ra.srn_no', 'r.doc_number')->where('r.company_id', $com_id)->where('r.status', 1);

            $viewSupport = $this->loadReceivableOutstandingViewData($com_id);
            extract($viewSupport);

            return view('backEnd.outstanding.receivableoutstanding', compact('data','is_view_all_cust', 'accounts', 'account_id', 'till_date', 'data_adjestment', 'data_all', 'com_id', 'overdue', 'ageing', 'sales_person_list', 'data_adjestment_all', 'data_receipt_all', 'data_receipt_opb', 'data_receipt2_all', 'data_receipt3_all', 'data_return_all', 'list_option', 'opbinvoice', 'opbinvoice_map', 'opb_balance_amount', 'list_of_unadjusted', 'list_of_unadjusted_jv_to_jv', 'list_of_unadjusted_pdc', 'list_of_adjusted_pdc', 'ctrl_account_id', 'ctrl_asofdate', 'ctrl_doc_no', 'ctrl_deal_id', 'ctrl_amount', 'ctrl_sales_person', 'ctrl_overdue', 'ctrl_ageing', 'ctrl_followup_from', 'ctrl_followup_to', 'ctrl_list_option', 'ctrl_intext','accounts_select','first_load','ctrl_basic_search', 'payment_terms_map', 'max_installments', 'sales_invoice_map', 'receivable_finance_rate'));

        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function update_receivable_pdc(Request $request)
    {
        try {
            $com_id = session('logged_session_data.company_id');

            $status = $request->status;
            $pdc_removed_os = 1;
            if ($status == 2) {
                $status = 1;
                $pdc_removed_os = $request->pdc_status;
            }

            $dateInput = $request->doc_date;
            $mysqlDate = null;
            if ($dateInput) {
                try {
                    if (Carbon::hasFormat($dateInput, 'Y-m-d')) {
                        // Already in MySQL format
                        $mysqlDate = $dateInput;
                    } elseif (Carbon::hasFormat($dateInput, 'd/m/Y')) {
                        // Convert DMY to YMD
                        $mysqlDate = Carbon::createFromFormat('d/m/Y', $dateInput)->format('Y-m-d');
                    }
                } catch (\Exception $e) {
                    $mysqlDate = null; // invalid date → set null
                }
            }

            SysChartofAccountsTransaction::where('transaction_no', $request->doc_id)->update([
                'transaction_date' => $mysqlDate,
                'status' => $status,
            ]);
            SysReceipt::where('doc_number', $request->doc_id)->update([
                'receipt_date' => $mysqlDate,
                'pdc_removed_os' => $pdc_removed_os,
            ]);



            $data = 'SUCCESS';
            return json_encode(array('data' => $data));

        } catch (\Exception $e) {
            $data = 'ERROR';
            return json_encode(array('data' => $data));
        }
    }

    public function index_old(Request $request)
    {
        try {

            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $data = [];
            $data_all = [];
            $accounts = SysHelper::get_customer_list($company_id);
            $com_id = session('logged_session_data.company_id');
            $account_id = "";
            $till_date = date('Y-m-d');
            //$data_query->wherein('created_by',$r[1]);

            $data_adjestment = [];
            $data_receipt = [];
            if ($_POST) {
                $account_id = $request->account_id;
                $till_date = $request->till_date;

                if ($request->btn_submit == 1 || $account_id == 0) {
                    if (count($accounts) > 0) {
                        foreach ($accounts as $a) {
                            $transaction_no = SysChartofAccountsTransaction::where('account_id', $a->id)->where('status', 1)->where('company_id', $com_id)->pluck('transaction_no');

                            if (count($transaction_no) > 0) {
                                $data_query = SysChartofAccountsTransaction::select('transaction_date', 'transaction_id', 'transaction_no', DB::raw('sum(debit_amount) as debit_amount'), DB::raw('sum(credit_amount) as credit_amount'), DB::raw($a->id . ' as account_id'))->wherein('company_id', $company_id);
                                $data_query->wherein('transaction_no', $transaction_no)->where('status', 1);
                                $data_query->wherein('transaction_type', ['salesinvoice', 'salesreturn', 'opbinvoice', 'openingbalance111']);
                                $data_query->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') <= '" . $till_date . "'");
                                $data_all[] = $data_query->groupby('transaction_date', 'transaction_id', 'transaction_no')->orderby('transaction_date', 'asc')->get();
                            }
                        }
                    }
                } else {
                    if ($account_id != 0) {
                        foreach ($account_id as $id) {
                            $transaction_no = SysChartofAccountsTransaction::where('account_id', $id)->where('status', 1)->where('company_id', $com_id)->pluck('transaction_no');

                            if (count($transaction_no) > 0) {
                                $data_query = SysChartofAccountsTransaction::select('transaction_date', 'transaction_id', 'transaction_no', DB::raw('sum(debit_amount) as debit_amount'), DB::raw('sum(credit_amount) as credit_amount'), DB::raw($id . ' as account_id'))->wherein('company_id', $company_id);
                                $data_query->wherein('transaction_no', $transaction_no)->where('status', 1);
                                $data_query->wherein('transaction_type', ['salesinvoice', 'salesreturn', 'opbinvoice', 'openingbalance111']);
                                $data_query->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') <= '" . $till_date . "'");
                                $data_all[] = $data_query->groupby('transaction_date', 'transaction_id', 'transaction_no')->orderby('transaction_date', 'asc')->get();
                            }
                        }
                    }
                }
            }
            if (count($data) > 0) {
                $data_adjestment = SysSalesReturnAdjestment::select('srn_no', DB::raw('sum(paid_amount) as paid_amount'))->wherein('srn_no', $data->pluck("transaction_no"))->groupby('srn_no')->get();

                $data_receipt = DB::table('sys_receipt as r')->select('ra.bi_doc_no', 'r.doc_number', 'ra.bi_amount', 'r.receipt_through', 'r.receipt_date', 'r.cheque_number', 'r.cheque_bank_name')
                    ->join('sys_receipt_adjustments as ra', 'ra.bi_doc_number', 'r.doc_number')->where('ra.status', 1)->where('ra.account_id', $account_id)->wherein('bi_doc_no', $data->pluck("transaction_no"))->where('r.status', 1)->get();
            }

            return view('backEnd.outstanding.receivableoutstanding', compact('data', 'accounts', 'account_id', 'till_date', 'data_adjestment', 'data_receipt', 'data_all', 'com_id'));

        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function download($account, $date)
    {
        try {
            $date = SysHelper::normalizeToYmd($date);
            $com_id = session('logged_session_data.company_id');
            $transaction_no = SysChartofAccountsTransaction::where('account_id', $account)->where('status', 1)->where('company_id', $com_id)->pluck('transaction_no');

            $company = SysCompany::find($com_id);

            $cust_detail = SysCustSuppl::select('sys_cust_suppl.*')->join('sys_chartofaccounts', 'sys_chartofaccounts.account_code', 'sys_cust_suppl.code')->where('sys_chartofaccounts.id', $account)->first();
            $cust_address = SysCustSupplAddressbook::where('cust_suppl_id', $cust_detail->id)->orderby('id', 'desc')->first();

            if (count($transaction_no) > 0) {
                $data_query = SysChartofAccountsTransaction::select('transaction_date', 'transaction_id', 'transaction_no', DB::raw('sum(debit_amount) as debit_amount'), DB::raw('sum(credit_amount) as credit_amount'), DB::raw($account . ' as account_id'))->where('company_id', $com_id);
                $data_query->wherein('transaction_no', $transaction_no)->where('status', 1);
                $data_query->wherein('transaction_type', ['salesinvoice', 'opbinvoice', 'openingbalance111']);
                $data_query->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') <= '" . $date . "'");
                $receivable = $data_query->groupby('transaction_date', 'transaction_id', 'transaction_no')->orderby('transaction_date', 'asc')->get();


                // $data_adjestment = SysSalesReturnAdjestment::select('srn_no',DB::raw('sum(paid_amount) as paid_amount'))->wherein('srn_no',$receivable->pluck("transaction_no"))->groupby('srn_no')->get();
                // $data_receipt = DB::table('sys_receipt as r')->select('ra.bi_doc_no','r.doc_number','ra.bi_amount','r.receipt_through','r.receipt_date','r.cheque_number','r.cheque_bank_name')
                // ->join('sys_receipt_adjustments as ra','ra.bi_doc_number','r.doc_number')->where('ra.account_id',$account)->wherein('bi_doc_no',$receivable->pluck("transaction_no"))->where('r.status',1)->get();

                // $data_adjestment = SysSalesReturnAdjestment::select('srn_no',DB::raw('sum(paid_amount) as paid_amount'))->wherein('srn_no',$receivable->pluck("transaction_no"))->groupby('srn_no')->get();

                // $data_receipt = DB::table('sys_receipt as r')->select('ra.bi_doc_no','r.doc_number','ra.bi_amount','r.receipt_through','r.receipt_date','r.cheque_number','r.cheque_bank_name')
                // ->join('sys_receipt_adjustments as ra','ra.bi_doc_number','r.doc_number')->where('ra.account_id',$account)->wherein('bi_doc_no',$receivable->pluck("transaction_no"))->where('r.status',1)->get();

                // $data_receipt2 = DB::table('sys_journalvoucher as j')->select('ra.bi_doc_no','j.doc_number','ra.bi_amount','j.doc_date')
                // ->join('sys_receipt_adjustments as ra','ra.bi_doc_number','j.doc_number')->where('ra.account_id',$account)->wherein('bi_doc_no',$receivable->pluck("transaction_no"))->where('j.status',1)->get();

                // $data_receipt3 = DB::table('sys_journalvoucher as j')->select('pa.bi_doc_no','j.doc_number','pa.bi_amount','j.doc_date')
                // ->join('sys_payment_adjustments as pa','pa.bi_doc_number','j.doc_number')->where('pa.account_id',$account)->wherein('bi_doc_no',$receivable->pluck("transaction_no"))->where('j.status',1)->get();


                $data_adjestment = SysSalesReturnAdjestment::select('srn_no', DB::raw('sum(paid_amount) as paid_amount'))->wherein('srn_no', $receivable->pluck("transaction_no"))->groupby('srn_no')->get();

                $data_receipt = DB::table('sys_receipt as r')->select('ra.bi_doc_no', 'r.doc_number', 'ra.bi_amount', 'r.receipt_through', 'r.receipt_date', 'r.cheque_number', 'r.cheque_bank_name')
                    ->join('sys_receipt_adjustments as ra', 'ra.bi_doc_number', 'r.doc_number')->where('ra.account_id', $account)->wherein('bi_doc_no', $receivable->pluck("transaction_no"))->where('r.status', 1)->get();

                $data_receipt2 = DB::table('sys_journalvoucher as j')->select('ra.bi_doc_no', 'j.doc_number', 'ra.bi_amount', 'j.doc_date')
                    ->join('sys_receipt_adjustments as ra', 'ra.bi_doc_number', 'j.doc_number')->where('ra.account_id', $account)->wherein('bi_doc_no', $receivable->pluck("transaction_no"))->where('j.status', 1)->get();

                $data_receipt3 = DB::table('sys_journalvoucher as j')->select('pa.bi_doc_no', 'j.doc_number', 'pa.bi_amount', 'j.doc_date')
                    ->join('sys_payment_adjustments as pa', 'pa.bi_doc_number', 'j.doc_number')->where('pa.account_id', $account)->wherein('bi_doc_no', $receivable->pluck("transaction_no"))->where('j.status', 1)->get();

                $data_return = DB::table('sys_sales_return as r')->select('ra.siv_no', 'r.doc_number', 'ra.paid_amount', 'r.doc_date')
                    ->join('sys_sales_return_adjestment as ra', 'ra.srn_no', 'r.doc_number')->where('r.customer', $account)->wherein('siv_no', $receivable->pluck("transaction_no"))->where('r.status', 1)->get();



                $list_of_unadjusted = SysHelper::get_list_of_unadjusted([$account], $com_id);
                $list_of_unadjusted_jv_to_jv = SysHelper::get_list_of_unadjusted_jv_to_jv([$account], $com_id);
                $list_of_unadjusted_pdc = SysHelper::get_list_of_unadjusted_pdc([$account], $com_id);
                $list_of_adjusted_pdc = SysHelper::get_list_of_adjusted_pdc([$account], $com_id);

                //var inv_e_doc_date = $('#inv_e_doc_date_'+doc[i]).val();
                //var inv_e_doc_no = $('#inv_e_doc_no_'+doc[i]).val();
                //var inv_e_lpo_no = $('#inv_e_lpo_no_'+doc[i]).val();
                //var inv_e_deal_code = $('#inv_e_deal_code_'+doc[i]).val();
                //var inv_e_amount = $('#inv_e_amount_'+doc[i]).val();
                //var inv_e_adjustment = $('#inv_e_adjustment_'+doc[i]).val();


            }
            $generate_date = Carbon::now('+04:00')->format('d/m/Y H:i a');

            $data = [
                'company' => $company,
                'cust_detail' => $cust_detail,
                'cust_address' => $cust_address,
                'receivable' => $receivable,
                'data_adjestment' => $data_adjestment,
                'data_receipt' => $data_receipt,
                'data_receipt2' => $data_receipt2,
                'data_receipt3' => $data_receipt3,
                'data_return' => $data_return,
                'date' => $date,
                'generate_date' => $generate_date,
                'list_of_unadjusted' => $list_of_unadjusted,
                'list_of_unadjusted_jv_to_jv' => $list_of_unadjusted_jv_to_jv,
                'list_of_unadjusted_pdc' => $list_of_unadjusted_pdc,
                'list_of_adjusted_pdc' => $list_of_adjusted_pdc,
            ];

            //return view('backEnd.pdf_print.receivable_os_pdf',$data);
            $pdf = PDF::loadView('backEnd.pdf_print.receivable_os_pdf', $data);
            $pdf->setPaper('A4', 'portrait');
            return $pdf->download($cust_detail->name . " - receivable_outstanding.pdf");
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function store_temp(Request $request)
    {
        try {
            $data = [
                'bi_lpo_no' => $request->bi_lpo_no,
                'bi_total' => $request->bi_total,
                'bi_paid' => $request->bi_amount,
                'bi_balance' => $request->bi_balance,
                'bi_amount' => $request->bi_amount,
            ];
            $check = SysReceiptAdjustmentsTemp::where($data)->count();
            if ($check == 0) {
                $sra = new SysReceiptAdjustmentsTemp();
                $sra->transaction_type = $request->transaction_type;
                $sra->bi_cheque_amount = str_replace(',', '', $request->bi_cheque_amount);
                $sra->bi_amount_adjusted = str_replace(',', '', $request->bi_amount_adjusted);
                $sra->bi_balance_to_adjust = str_replace(',', '', $request->bi_balance_to_adjust);
                $sra->bi_extra_amount = str_replace(',', '', $request->bi_extra_amount);
                $sra->bi_doc_no = $request->bi_doc_no;
                $sra->bi_doc_number = $request->bi_doc_number;
                $sra->bi_doc_date = date('Y-m-d', strtotime($request->bi_doc_date));
                $sra->bi_lpo_no = $request->bi_lpo_no;
                $sra->bi_total = str_replace(',', '', $request->bi_total);
                $sra->bi_paid = str_replace(',', '', $request->bi_amount);
                $sra->bi_balance = str_replace(',', '', $request->bi_balance);
                $sra->bi_amount = str_replace(',', '', $request->bi_amount);
                $sra->bi_currency = $request->bi_currency;
                $sra->bi_narration = $request->bi_narration;
                $sra->account_id = $request->account_id;
                $sra->status = 1;
                $sra->created_by = Auth::user()->id;
                $sra->process_id = $request->process_id;
                $sra->process_status = 1;
                $results = $sra->save();
                $sra->toArray();
            }

            $ret = 'SUCCESS';
            return json_encode(array('data' => $ret));
        } catch (\Exception $e) {
            return $e;
            return json_encode(array('data' => $ret));
        }
    }

    public function store_temp_delete(Request $request)
    {
        try {
            SysReceiptAdjustmentsTemp::where('bi_doc_number', $request->doc_number)->where('account_id', $request->account_id)->delete();
            $ret = 'SUCCESS';
            return json_encode(array('data' => $ret));
        } catch (\Exception $e) {
            $ret = $e;
            return json_encode(array('data' => $ret));
        }
    }

    public function store_update(Request $request)
    {
        try {
            DB::beginTransaction();
            SysReceiptAdjustments::where('bi_doc_number', $request->doc_number2)->where('account_id', $request->br_account_id)->delete();

            $transaction = SysChartofAccountsTransaction::where('transaction_no', $request->doc_number2)->get();
            if ($transaction->where('account_id', $request->br_account_id)->count() == 0) {
                SysHelper::trn_chartof_accounts_transaction_with_main($request->br_account_id, $transaction[0]->transaction_id, $request->doc_number2, $transaction[0]->transaction_date, $request->transaction_type2, '0.00', $request->br_account_id_amount, '', 1, 0, "", 1, 0);
            }

            $amount = SysChartofAccountsTransaction::where(['transaction_no' => $request->doc_number2])->sum('credit_amount');
            SysChartofAccountsTransaction::where(['transaction_no' => $request->doc_number2])
                ->where(['is_main_account' => 1])->where(['credit_amount' => '0.00'])->update(['debit_amount' => $amount]);

            $temp_data = [];
            for ($i = 0; $i < count($request->bi_amount); $i++) {
                if ($request->bi_amount[$i] != 0) {
                    $temp_data[] = [
                        'transaction_type' => $request->transaction_type2,
                        'bi_cheque_amount' => str_replace(',', '', $request->bi_cheque_amount),
                        'bi_amount_adjusted' => str_replace(',', '', $request->bi_amount_adjusted),
                        'bi_balance_to_adjust' => str_replace(',', '', $request->bi_balance_to_adjust),
                        'bi_extra_amount' => str_replace(',', '', $request->bi_extra_amount),
                        'bi_currency' => $request->bi_currency2,
                        'bi_doc_number' => $request->doc_number2,
                        'bi_contains' => '',
                        'bi_doc_no' => $request->bi_doc_no[$i],
                        'bi_lpo_no' => $request->bi_lpo_no[$i],
                        'bi_doc_date' => $request->bi_doc_date[$i],
                        'bi_total' => str_replace(',', '', $request->bi_total[$i]),
                        'bi_paid' => str_replace(',', '', $request->bi_amount[$i]),
                        'bi_balance' => str_replace(',', '', $request->bi_balance[$i]),
                        'bi_amount' => str_replace(',', '', $request->bi_amount[$i]),
                        'bi_narration' => $request->bi_narration[$i],
                        'account_id' => $request->br_account_id,
                        'status' => 1,
                        'created_by' => Auth::user()->id,
                        'created_at' => Carbon::now('+04:00'),
                        'company_id' => session('logged_session_data.company_id'),
                    ];
                }
            }
            if (count($temp_data) > 0) {
                SysReceiptAdjustments::insert($temp_data);
            }

            $ret = SysReceiptAdjustments::where('bi_doc_number', $request->doc_number2)->where('status', 1)->get();

            DB::commit();
            if (count($ret) > 0) {
                return json_encode(array('data' => $ret));
            } else {
                $ret = [];
                return json_encode(array('data' => $ret));
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            //$ret = 'ERROR';
            $ret = $th;
            return json_encode(array('data' => $ret));
        }
    }

    //execlude
    public function store_update_execlude(Request $request)
    {
        try {
            //SysReceiptAdjustments::where('bi_doc_number',$request->doc_number)->delete();
            $temp_data[] = [
                'transaction_type' => $request->transaction_type,
                'bi_cheque_amount' => $request->bi_cheque_amount,
                'bi_amount_adjusted' => $request->bi_amount_adjusted,
                'bi_balance_to_adjust' => $request->bi_balance_to_adjust,
                'bi_extra_amount' => $request->bi_extra_amount,
                'bi_currency' => $request->bi_currency,
                'bi_doc_number' => $request->doc_number,
                'bi_contains' => '',
                'bi_doc_no' => $request->bi_doc_no,
                'bi_lpo_no' => $request->bi_lpo_no,
                'bi_doc_date' => $request->bi_doc_date,
                'bi_total' => $request->bi_total,
                'bi_paid' => $request->bi_amount,
                'bi_balance' => $request->bi_balance,
                'bi_amount' => $request->bi_amount,
                'bi_narration' => $request->bi_narration,
                'account_id' => $request->account_id,
                'status' => 1,
                'created_by' => Auth::user()->id,
                'created_at' => Carbon::now('+04:00'),
                'company_id' => session('logged_session_data.company_id'),
            ];
            SysReceiptAdjustments::insert($temp_data);

            $ret = 'SUCCESS';
            return json_encode(array('data' => $ret));
        } catch (\Exception $e) {
            $ret = $e;
            return json_encode(array('data' => $ret));
        }
    }
    //execlude


    // public function store_delete_before_update(Request $request)
    // {
    //     try{    
    //         SysReceiptAdjustments::where('bi_doc_number',$request->doc_number)->where('account_id',$request->account_id)->delete();        
    //     $ret = 'SUCCESS';
    //         return json_encode(array('data'=>$ret));
    //     }catch (\Exception $e) {
    //         $ret = $e;
    //         return json_encode(array('data'=>$ret));
    //     }
    // }

    public function supplieroutstanding(Request $request)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $account_id = "";
            $accounts = SysChartofAccounts::select('id', 'account_name')->where('subgroup', 3)->wherein('company_id', $company_id)->where('status', 1)->orderby('account_name', 'asc')->get();
            $data_query = SysChartofAccountsTransaction::where('status', 1);
            if ($_POST) {
                if ($request->account_id != "") {
                    $data_query->where('account_id', $request->account_id);
                    $account_id = $request->account_id;
                }
            } else {
                $data_query->where('transaction_no', '0');
            }
            $data = $data_query->get();
            return view('backEnd.supplier-ledger.supplieroutstanding', compact('data', 'accounts', 'account_id'));

        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function supplieroutstandingpdc(Request $request)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $account_id = "";
            $accounts = SysChartofAccounts::select('id', 'account_name')->where('subgroup', 3)->wherein('company_id', $company_id)->where('status', 1)->orderby('account_name', 'asc')->get();
            $data_query = SysChartofAccountsTransaction::where('status', 1);
            if ($_POST) {
                if ($request->account_id != "") {
                    $data_query->where('account_id', $request->account_id);
                    $account_id = $request->account_id;
                }
            } else {
                $data_query->where('transaction_no', '0');
            }
            $data = $data_query->get();
            return view('backEnd.supplier-ledger.supplieroutstandingpdc', compact('data', 'accounts', 'account_id'));

        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function supplierageing(Request $request)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $account_id = "";
            $accounts = SysChartofAccounts::select('id', 'account_name')->where('subgroup', 3)->wherein('company_id', $company_id)->where('status', 1)->orderby('account_name', 'asc')->get();
            $data_query = SysChartofAccountsTransaction::where('status', 1);
            if ($_POST) {
                if ($request->account_id != "") {
                    $data_query->where('account_id', $request->account_id);
                    $account_id = $request->account_id;
                }
            } else {
                $data_query->where('transaction_no', '0');
            }
            $data = $data_query->get();
            return view('backEnd.supplier-ledger.supplierageing', compact('data', 'accounts', 'account_id'));

        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    // start geo

    public function outstanding_comment(Request $request)
    {
        try {

            $id = $request->id_deal;



            $val = DB::table('outstand_comments')
                ->where('comment_id', $id)
                ->where('company_id', session('logged_session_data.company_id'))
                ->orderBy('created_at', 'asc')
                ->get();
            return json_encode($val);
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function outstanding_comment_save(Request $request)
    {
        try {
            // save a comment attached to the specified customer/account id
            $id = Auth::user()->id;
            $username = DB::table('users')->where('id', $id)->first();

            // ensure we received the account id
            $accountId = $request->id_deal;

            $attachment = null;
            if ($request->hasFile('remark_file')) {
                $file = $request->file('remark_file');
                $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
                $file->move(public_path('uploads/outstand_comments_doc'), $filename);
                $attachment = $filename;
            }

            DB::table('outstand_comments')->insert(
                [
                    'comment_id' => $accountId,
                    'comment' => $request->comment,
                    'status' => 0,
                    'company_id' => session('logged_session_data.company_id'),
                    'username' => $username->full_name,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                    'followup_date' => SysHelper::normalizeToYmd($request->remark_date) ?: null,
                    'file' => $attachment,
                ]
            );

            return json_encode(['data' => 'SUCCESS']);
        } catch (\Throwable $th) {
            Toastr::error('Something went wrong, please try again', 'Failed');
            return json_encode(['data' => 'ERROR', 'message' => $th->getMessage()]);
        }
    }

    public function outstanding_comment_delete(Request $request)
    {
        try {
            $commentId = $request->comment_id;
            DB::table('outstand_comments')
                ->where('id', $commentId)
                ->where('created_by', Auth::user()->id)
                ->update(['is_deleted' => 1]);
            return json_encode(['data' => 'SUCCESS']);
        } catch (\Throwable $th) {
            return json_encode(['data' => 'ERROR']);
        }
    }


    //end geo



    public function CustomerAgeingReport(Request $request)
    {
        try {

            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $data_all = [];
            $accounts = SysHelper::get_customer_list($company_id);
            $com_id = session('logged_session_data.company_id');
            $till_date = date('Y-m-d');
            $ctrl_intext = "";



            if (!$_POST) {

                if (SysHelper::get_pagination_post($request)) {
                    $com_id = $request->com;
                    $company_id = [$request->com];
                }

                $query1 = SysChartofAccountsTransaction::select('transaction_no', 'account_id')->where('status', 1)->where('company_id', $com_id)->wherein('transaction_type', ['salesinvoice', 'salesreturn', 'opbinvoice', 'openingbalance111']);

                $BigData1 = $query1->distinct()->get();
                $BigData = SysChartofAccountsTransaction::where('status', 1)->where('company_id', $com_id)->wherein('transaction_type', ['salesinvoice', 'salesreturn', 'opbinvoice', 'openingbalance111']);


                if (count($accounts) > 0) {
                    foreach ($accounts as $a) {
                        $abc = clone $BigData1;
                        $transaction_no = $abc->where('account_id', $a->id)->pluck('transaction_no');
                        if (count($transaction_no) > 0) {
                            $selectBigData = clone $BigData;
                            $data_query = $selectBigData->select(...$this->receivableOsAggregateSelect((string) $a->id))->wherein('company_id', $company_id);
                            $data_query->where("account_id", $a->id)->where('status', 1);
                            $data_query->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') <= '" . $till_date . "'");
                            $data_query->wherein('transaction_type', ['salesinvoice', 'salesreturn', 'opbinvoice', 'openingbalance111']);

                            $dq = $data_query->groupby('transaction_date', 'transaction_id', 'transaction_no')->orderby('transaction_date', 'asc')->get();
                            if (count($dq) > 0) {
                                $data_all[] = $dq;
                            }
                        }
                    }
                }
            }


            if ($_POST) {
                $till_date = SysHelper::normalizeToYmd($request->till_date);

                if ($request->list_in_ex != "") {
                    $accounts = SysChartofAccounts::select('sys_chartofaccounts.id', 'sys_chartofaccounts.account_name', 'sys_chartofaccounts.account_code')
                        ->where('sys_chartofaccounts.internal', $request->list_in_ex)->get();
                    $ctrl_intext = $request->list_in_ex;
                }

                $bigData = SysChartofAccountsTransaction::select(...$this->receivableOsAggregateSelect('account_id'))->wherein('company_id', $company_id)->where('status', 1)->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') <= '" . $till_date . "'")->wherein('transaction_type', ['salesinvoice', 'salesreturn', 'opbinvoice', 'openingbalance111'])->groupby('transaction_date', 'transaction_id', 'transaction_no', 'account_id');

                foreach ($accounts as $a) {
                    $bdata = clone $bigData;
                    $data_query = $bdata->where("account_id", $a->id);

                    $dq = $data_query->orderby('transaction_date', 'asc')->get();
                    if (count($dq) > 0) {
                        $data_all[] = $dq;
                    }
                }
            }

            $data_adjestment_all = DB::table('sys_sales_return_adjestment')->select('srn_no', DB::raw('sum(paid_amount) as paid_amount'))->groupby('srn_no');

            $data_receipt_all = DB::table('sys_receipt as r')->select('ra.bi_doc_no', 'r.doc_number', 'ra.bi_amount', 'r.receipt_through', 'r.receipt_date', 'r.cheque_number', 'r.cheque_bank_name', 'ra.account_id')->join('sys_receipt_adjustments as ra', 'ra.bi_doc_number', 'r.doc_number')->where('r.company_id', $com_id)->where('r.status', 1);
            //return $data_receipt_all->get();

            $data_receipt_opb = DB::table('sys_receipt_adjustments as ra')->select('ra.bi_doc_no', 'ra.bi_doc_number as doc_number', 'ra.bi_amount', 'ra.transaction_type as receipt_through', 'ra.bi_doc_date as receipt_date', 'ra.bi_doc_number as cheque_number', 'ra.bi_doc_number as cheque_bank_name', 'ra.account_id')->where('ra.transaction_type', 'openingbalance')->where('ra.company_id', $com_id)->where('ra.status', 1);

            $data_receipt2_all = DB::table('sys_journalvoucher as j')->select('ra.bi_doc_no', 'j.doc_number', 'ra.bi_amount', 'j.doc_date', 'ra.account_id')
                ->join('sys_receipt_adjustments as ra', 'ra.bi_doc_number', 'j.doc_number')->where('j.company_id', $com_id)->where('j.status', 1);
            //return $data_receipt2_all->get();

            $data_receipt3_all = DB::table('sys_journalvoucher as j')->select('pa.bi_doc_no', 'j.doc_number', 'pa.bi_amount', 'j.doc_date', 'pa.account_id')
                ->join('sys_payment_adjustments as pa', 'pa.bi_doc_number', 'j.doc_number')->where('j.company_id', $com_id)->where('j.status', 1);

            $data_return_all = DB::table('sys_sales_return as r')->select('ra.siv_no', 'r.doc_number', 'ra.paid_amount', 'r.doc_date', 'r.customer', 'ra.srn_no')
                ->join('sys_sales_return_adjestment as ra', 'ra.srn_no', 'r.doc_number')->where('r.company_id', $com_id)->where('r.status', 1);

            return view('backEnd.outstanding.customerageingreport', compact('accounts', 'till_date', 'data_all', 'data_adjestment_all', 'data_receipt_all', 'data_receipt_opb', 'data_receipt2_all', 'data_receipt3_all', 'data_return_all', 'ctrl_intext'));

        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function indexModal(Request $request)
    {
        try {

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

            $deal_id = '';
            $amount = 0;
            $overdue = -999999;
            $ageing = -999999;
            $ctrl_account_id = "";
            $ctrl_asofdate = "";
            $ctrl_doc_no = "";
            $ctrl_deal_id = "";
            $ctrl_amount = "";
            $ctrl_sales_person = "";
            $ctrl_overdue = "";
            $ctrl_ageing = "";
            $ctrl_list_option = "";
            $ctrl_intext = "";



            if (!$_POST) {


                if (SysHelper::get_pagination_post($request)) {
                    $com_id = $request->com;
                    $company_id = [$request->com];
                }

                $query1 = SysChartofAccountsTransaction::select('transaction_no', 'account_id')->where('status', 1)->where('company_id', $com_id)->wherein('transaction_type', ['salesinvoice', 'salesreturn', 'opbinvoice', 'openingbalance111']);

                if (SysHelper::get_pagination_post($request)) {
                    if ($request->sales != "") {
                        $inv_trn_no1 = SysSalesInvoice::where('sales_man', $request->sales)->where('status', 1)->where('company_id', $com_id)->pluck('doc_number');
                        $inv_trn_no2 = SysSalesReturn::where('sales_man', $request->sales)->where('status', 1)->where('company_id', $com_id)->pluck('doc_number');
                        $inv_trn_no = $inv_trn_no2->merge($inv_trn_no1);
                        if (count($inv_trn_no) > 0) {
                            $query1->wherein('transaction_no', $inv_trn_no);
                        } else {
                            $query1->where('transaction_no', '0');
                        }

                    }
                }

                $BigData1 = $query1->distinct()->get();
                $BigData = SysChartofAccountsTransaction::where('status', 1)->where('company_id', $com_id)->wherein('transaction_type', ['salesinvoice', 'salesreturn', 'opbinvoice', 'openingbalance111']);


                if (count($accounts) > 0) {
                    foreach ($accounts as $a) {
                        $abc = clone $BigData1;
                        $transaction_no = $abc->where('account_id', $a->id)->pluck('transaction_no');
                        if (count($transaction_no) > 0) {
                            $selectBigData = clone $BigData;
                            $data_query = $selectBigData->select(...$this->receivableOsAggregateSelect((string) $a->id))->wherein('company_id', $company_id);
                            $data_query->where("account_id", $a->id)->where('status', 1);
                            $data_query->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') <= '" . $till_date . "'");
                            $data_query->wherein('transaction_type', ['salesinvoice', 'salesreturn', 'opbinvoice', 'openingbalance111']);

                            if (SysHelper::get_pagination_post($request)) {
                                if ($request->over != "") {
                                    $overdue_list = SysHelper::get_receivable_os_by_overdue($request->over, $a->id, SysHelper::normalizeToYmd($till_date));
                                    if (count($overdue_list) > 0) {
                                        $data_query->wherein('transaction_no', $overdue_list);
                                    } else {
                                        $query1->where('transaction_no', '0');
                                    }
                                }
                            }


                            $dq = $data_query->groupby('transaction_date', 'transaction_id', 'transaction_no')->orderby('transaction_date', 'asc')->get();
                            if (count($dq) > 0) {
                                $data_all[] = $dq;
                            }
                        }
                    }


                    $list_of_unadjusted = SysHelper::get_list_of_unadjusted($accounts->pluck('id'), $com_id);
                    $list_of_unadjusted_jv_to_jv = SysHelper::get_list_of_unadjusted_jv_to_jv($accounts->pluck('id'), $com_id);
                    $list_of_unadjusted_pdc = SysHelper::get_list_of_unadjusted_pdc($accounts->pluck('id'), $com_id);
                    $list_of_adjusted_pdc = SysHelper::get_list_of_adjusted_pdc($accounts->pluck('id'), $com_id);

                    $opb_balance_amount = SysHelper::get_customer_opening_balance($accounts->pluck('id'), date('Y-m-d', strtotime('+1 day')), $com_id);
                }
            }


            if ($_POST) {
                $account_id = $request->account_id;
                $till_date = SysHelper::normalizeToYmd($request->till_date);
                $ctrl_account_id = $request->account_id;
                $ctrl_asofdate = $request->till_date;




                $deal_id = $request->deal_id;

                $amount = $request->amount;

                $overdue = $request->overdue;

                $ageing = $request->ageing;


                if ($account_id != 0) {
                    $accounts = SysChartofAccounts::select('sys_chartofaccounts.id', 'sys_chartofaccounts.account_name', 'sys_chartofaccounts.account_code')
                        ->wherein('sys_chartofaccounts.id', $account_id)->get();
                }

                if ($request->list_in_ex != "") {
                    $accounts = SysChartofAccounts::select('sys_chartofaccounts.id', 'sys_chartofaccounts.account_name', 'sys_chartofaccounts.account_code')
                        ->where('sys_chartofaccounts.internal', $request->list_in_ex)->get();
                    $ctrl_intext = $request->list_in_ex;
                }

                $bigData = SysChartofAccountsTransaction::select(...$this->receivableOsAggregateSelect('account_id'))->wherein('company_id', $company_id)->where('status', 1)->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') <= '" . $till_date . "'")->wherein('transaction_type', ['salesinvoice', 'salesreturn', 'opbinvoice', 'openingbalance111'])->groupby('transaction_date', 'transaction_id', 'transaction_no', 'account_id');

                foreach ($accounts as $a) {
                    $bdata = clone $bigData;
                    $data_query = $bdata->where("account_id", $a->id);

                    if ($request->transaction_no != "") {
                        $data_query->where("transaction_no", $request->transaction_no);
                        $ctrl_doc_no = $request->transaction_no;
                    }

                    if ($request->deal_id != "") {
                        $deal_id = SysHelper::get_dealid_from_code($request->deal_id);
                        $deal_trn_no = SysSalesInvoice::where('deal_id', $deal_id)->where('status', 1)->where('company_id', $com_id)->where("customer", $a->id)->pluck('doc_number');
                        $deal_trn_no = $deal_trn_no->merge($this->opbTransactionNosByDeal($com_id, $a->id, $request->deal_id, $deal_id))->unique()->values();
                        if (count($deal_trn_no) > 0) {
                            $data_query->wherein('transaction_no', $deal_trn_no);
                        } else {
                            $data_query->where('transaction_no', '0');
                        }
                        $ctrl_deal_id = $request->deal_id;
                    }

                    if ($request->amount != "") {
                        $amount_d_trn_no = SysChartofAccountsTransaction::where("account_id", $a->id)->where('status', 1)
                            ->whereRaw("debit_amount = " . $request->amount . "")->where('company_id', $com_id)->pluck('transaction_no');

                        if (count($amount_d_trn_no) > 0) {
                            $data_query->wherein('transaction_no', $amount_d_trn_no);
                        } else {
                            $data_query->where('transaction_no', '0');
                        }
                        $ctrl_amount = $request->amount;
                    }

                    if ($request->sales_person != "") {
                        $inv_trn_no1 = SysSalesInvoice::wherein('sales_man', $request->sales_person)->where('status', 1)->where('company_id', $com_id)->where("customer", $a->id)->pluck('doc_number');
                        $inv_trn_no2 = SysSalesReturn::wherein('sales_man', $request->sales_person)->where('status', 1)->where('company_id', $com_id)->where("customer", $a->id)->pluck('doc_number');
                        $inv_trn_no = $inv_trn_no2->merge($inv_trn_no1)->merge($this->opbTransactionNosBySalesPerson($com_id, $a->id, (array) $request->sales_person))->unique()->values();
                        if (count($inv_trn_no) > 0) {
                            $data_query->wherein('transaction_no', $inv_trn_no);
                        } else {
                            $data_query->where('transaction_no', '0');
                        }
                        $ctrl_sales_person = $request->sales_person;
                    }

                    if ($overdue != "") {
                        $overdue_list = SysHelper::get_receivable_os_by_overdue($overdue, $a->id, SysHelper::normalizeToYmd($till_date));
                        if (count($overdue_list) > 0) {
                            $data_query->wherein('transaction_no', $overdue_list);
                        } else {
                            $data_query->where('transaction_no', '0');
                        }
                        $ctrl_overdue = $request->overdue;
                    }

                    if ($ageing != "") {
                        $ageing_list = SysHelper::get_receivable_os_by_ageing($ageing, $a->id, SysHelper::normalizeToYmd($till_date));
                        if (count($ageing_list) > 0) {
                            $data_query->wherein('transaction_no', $ageing_list);
                        } else {
                            $data_query->where('transaction_no', '0');
                        }
                        $ctrl_ageing = $request->ageing;
                    }
                    if ($request->list_option != "") {
                        $list_option = $request->list_option;
                        if ($list_option == "consolidated") {
                            $list_option = "show";
                        }

                        $ctrl_list_option = $request->list_option;


                    }

                    $dq = $data_query->orderby('transaction_date', 'asc')->get();
                    if (count($dq) > 0) {
                        $data_all[] = $dq;
                    }
                }

                $list_of_unadjusted = SysHelper::get_list_of_unadjusted($account_id, $com_id);
                $list_of_unadjusted_jv_to_jv = SysHelper::get_list_of_unadjusted_jv_to_jv($account_id, $com_id);
                $list_of_unadjusted_pdc = SysHelper::get_list_of_unadjusted_pdc($account_id, $com_id);
                $list_of_adjusted_pdc = SysHelper::get_list_of_adjusted_pdc($account_id, $com_id);




                if ($request->amount != "") {
                    $amount = $request->amount;
                } else {
                    $amount = 0;
                }
                if ($request->overdue != "") {
                    $overdue = $request->overdue;
                } else {
                    $overdue = -999999;
                }
                if ($request->ageing != "") {
                    $ageing = $request->ageing;
                } else {
                    $ageing = -999999;
                }

                $opb_balance_amount = SysHelper::get_customer_opening_balance($accounts->pluck('id'), date('Y-m-d', strtotime('+1 day')), $com_id);
            }

            $data_adjestment_all = DB::table('sys_sales_return_adjestment')->select('srn_no', DB::raw('sum(paid_amount) as paid_amount'))->groupby('srn_no');

            $data_receipt_all = DB::table('sys_receipt as r')->select('ra.bi_doc_no', 'r.doc_number', 'ra.bi_amount', 'r.receipt_through', 'r.receipt_date', 'r.cheque_number', 'r.cheque_bank_name', 'ra.account_id')->join('sys_receipt_adjustments as ra', 'ra.bi_doc_number', 'r.doc_number')->where('r.company_id', $com_id)->where('r.status', 1);
            //return $data_receipt_all->get();

            $data_receipt_opb = DB::table('sys_receipt_adjustments as ra')->select('ra.bi_doc_no', 'ra.bi_doc_number as doc_number', 'ra.bi_amount', 'ra.transaction_type as receipt_through', 'ra.bi_doc_date as receipt_date', 'ra.bi_doc_number as cheque_number', 'ra.bi_doc_number as cheque_bank_name', 'ra.account_id')->where('ra.transaction_type', 'openingbalance')->where('ra.company_id', $com_id)->where('ra.status', 1);

            $data_receipt2_all = DB::table('sys_journalvoucher as j')->select('ra.bi_doc_no', 'j.doc_number', 'ra.bi_amount', 'j.doc_date', 'ra.account_id')
                ->join('sys_receipt_adjustments as ra', 'ra.bi_doc_number', 'j.doc_number')->where('j.company_id', $com_id)->where('j.status', 1);
            //return $data_receipt2_all->get();

            $data_receipt3_all = DB::table('sys_journalvoucher as j')->select('pa.bi_doc_no', 'j.doc_number', 'pa.bi_amount', 'j.doc_date', 'pa.account_id')
                ->join('sys_payment_adjustments as pa', 'pa.bi_doc_number', 'j.doc_number')->where('j.company_id', $com_id)->where('j.status', 1);

            $data_return_all = DB::table('sys_sales_return as r')->select('ra.siv_no', 'r.doc_number', 'ra.paid_amount', 'r.doc_date', 'r.customer', 'ra.srn_no')
                ->join('sys_sales_return_adjestment as ra', 'ra.srn_no', 'r.doc_number')->where('r.company_id', $com_id)->where('r.status', 1);

            $viewSupport = $this->loadReceivableOutstandingViewData($com_id);
            extract($viewSupport);

            return view('backEnd.outstanding.receivableoutstanding-modal', compact('data', 'accounts', 'account_id', 'till_date', 'data_adjestment', 'data_all', 'com_id', 'overdue', 'ageing', 'sales_person_list', 'data_adjestment_all', 'data_receipt_all', 'data_receipt_opb', 'data_receipt2_all', 'data_receipt3_all', 'data_return_all', 'list_option', 'opbinvoice', 'opbinvoice_map', 'opb_balance_amount', 'list_of_unadjusted', 'list_of_unadjusted_jv_to_jv', 'list_of_unadjusted_pdc', 'list_of_adjusted_pdc', 'ctrl_account_id', 'ctrl_asofdate', 'ctrl_doc_no', 'ctrl_deal_id', 'ctrl_amount', 'ctrl_sales_person', 'ctrl_overdue', 'ctrl_ageing', 'ctrl_list_option', 'ctrl_intext', 'payment_terms_map', 'max_installments', 'sales_invoice_map', 'receivable_finance_rate'));
            // }

            // return view('backEnd.outstanding.receivableoutstanding', compact('data', 'accounts', 'account_id', 'till_date', 'data_adjestment', 'data_all', 'com_id', 'overdue', 'ageing', 'sales_person_list', 'data_adjestment_all', 'data_receipt_all', 'data_receipt_opb', 'data_receipt2_all', 'data_receipt3_all', 'data_return_all', 'list_option', 'opbinvoice', 'opb_balance_amount', 'list_of_unadjusted', 'list_of_unadjusted_jv_to_jv', 'list_of_unadjusted_pdc', 'list_of_adjusted_pdc',  'ctrl_account_id', 'ctrl_asofdate', 'ctrl_doc_no', 'ctrl_deal_id', 'ctrl_amount', 'ctrl_sales_person', 'ctrl_overdue', 'ctrl_ageing', 'ctrl_list_option', 'ctrl_intext'));

        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    private function receivableOsAggregateSelect($accountIdExpression)
    {
        return [
            'transaction_date',
            'transaction_id',
            'transaction_no',
            DB::raw('sum(debit_amount) as debit_amount'),
            DB::raw('sum(credit_amount) as credit_amount'),
            DB::raw("{$accountIdExpression} as account_id"),
            DB::raw('MAX(transaction_type) as transaction_type'),
        ];
    }

    private function loadOpbInvoiceDetailMap($companyId)
    {
        return DB::table('sys_chartofaccounts_transaction_invoice_detail as d')
            ->join('sys_chartofaccounts_transaction as t', 't.id', '=', 'd.trn_id')
            ->where('t.company_id', $companyId)
            ->where('t.transaction_type', 'opbinvoice')
            ->where('t.status', 1)
            ->select('d.*')
            ->get()
            ->keyBy('transaction_no');
    }

    private function loadReceivableOutstandingViewData($companyId)
    {
        $payment_terms_map = SysPaymentTerms::where('active_status', 1)->get()->keyBy('id');
        $sales_invoice_map = DB::table('sys_sales_invoice')
            ->where('company_id', $companyId)
            ->where('status', 1)
            ->select('doc_number', 'payment_terms', 'doc_date')
            ->get()
            ->keyBy('doc_number');
        $opbinvoice_map = $this->loadOpbInvoiceDetailMap($companyId);
        $opbinvoice = $opbinvoice_map->values();
        $max_installments = max(
            SysPaymentTerms::resolveMaxInstallmentsFromMaps($sales_invoice_map, $payment_terms_map),
            SysPaymentTerms::resolveMaxInstallmentsFromOpbMap($opbinvoice_map, $payment_terms_map)
        );
        $company_row = SysCompany::find($companyId);
        $receivable_finance_rate = (float) ($company_row->receivables_finance_cost_percentage ?? 0);

        return compact(
            'payment_terms_map',
            'sales_invoice_map',
            'opbinvoice_map',
            'opbinvoice',
            'max_installments',
            'receivable_finance_rate'
        );
    }

    private function opbTransactionNosByDeal($companyId, $accountId, $dealCode, $dealId = null)
    {
        return DB::table('sys_chartofaccounts_transaction_invoice_detail as d')
            ->join('sys_chartofaccounts_transaction as t', 't.id', '=', 'd.trn_id')
            ->where('t.company_id', $companyId)
            ->where('t.account_id', $accountId)
            ->where('t.transaction_type', 'opbinvoice')
            ->where('t.status', 1)
            ->where(function ($query) use ($dealCode, $dealId) {
                $query->where('d.deal_id', $dealCode);
                if ($dealId) {
                    $query->orWhere('d.deal_id', (string) $dealId);
                }
            })
            ->pluck('d.transaction_no');
    }

    private function opbTransactionNosBySalesPerson($companyId, $accountId, array $salesPersons)
    {
        if (empty($salesPersons)) {
            return collect();
        }

        return DB::table('sys_chartofaccounts_transaction_invoice_detail as d')
            ->join('sys_chartofaccounts_transaction as t', 't.id', '=', 'd.trn_id')
            ->where('t.company_id', $companyId)
            ->where('t.account_id', $accountId)
            ->where('t.transaction_type', 'opbinvoice')
            ->where('t.status', 1)
            ->whereIn('d.sales_person', $salesPersons)
            ->pluck('d.transaction_no');
    }


}
<?php

namespace App\Http\Controllers;

use App\SysChartofAccounts;
use App\SysAccountType;
use App\ApiBaseMethod;
use App\SmSupplier;
use App\SysAccountGroup;
use App\SysAccountGroupSub;
use App\SysChartofAccountsTransaction;
use App\SysCompany;
use App\SysPaymentTerms;
use App\SysCustSuppl;
use App\SysCustSupplAddressbook;
use App\SysHelper;
use App\SysLedgerEntries;
use App\SysPayment;
use App\SysPaymentAdjustments;
use App\SysPaymentAdjustmentsTemp;
use App\SysPurchaseInvoice;
use App\SysPurchaseReturnAdjestment;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use hisorange\BrowserDetect\Result;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\Else_;
use Barryvdh\DomPDF\Facade as PDF;
use Symfony\Component\CssSelector\Parser\Shortcut\ElementParser;

class SysPayablesOutstandingController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
    }

    private function payableOsSalesInvoiceRowGroup()
    {
        return DB::raw("CASE WHEN transaction_type = 'salesinvoice' THEN id ELSE 0 END");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $com_id = session('logged_session_data.company_id');

            $data = [];
            $data_all = [];
            $accounts = SysHelper::get_supplier_list($company_id);
            $accounts_select = SysHelper::get_supplier_list($company_id);

            $sales_person_list = SysHelper::get_sales_persons3();
            $com_id = session('logged_session_data.company_id');
            $account_id = "";
            $till_date = date('Y-m-d');
            $pdc_list = [];
            $data_adjestment = [];
            $data_payment = [];
            $transaction_no1 = [];
            $doc_date = '';
            $deal_id = '';
            $amount = 0;
            $overdue = -999999;
            $ageing = -999999;

            $ctrl_overdue = "";
            $ctrl_ageing = "";
            $ctrl_intext = "";
            $ctrl_basic_search = "1";
            $ctrl_followup_from = "";
            $ctrl_followup_to = "";
            $is_view_all_supp = false;
            $ctrl_sales_person = [];

            $list_of_unadjusted = [];
            $list_of_unadjusted_jv_to_jv = [];
            $list_of_unadjusted_pdc = [];
            $list_of_adjusted_pdc = [];
            $opb_balance_amount = 0;

            $list_option = "";
            $ctrl_list_option = "";
            $first_load = true;



            if (!$_POST) {
                $first_load = true;

                //$accounts = $accounts->where('id',7529);
                if (count($accounts) > 0) {
                    foreach ($accounts as $a) {
                        $transaction_no = SysChartofAccountsTransaction::where('account_id', $a->id)->where('status', 1)->where('company_id', $com_id)->pluck('transaction_no');

                        if (count($transaction_no) > 0) {

                            $data_query = SysChartofAccountsTransaction::select('transaction_date', 'transaction_id', 'transaction_no', DB::raw('sum(debit_amount) as debit_amount'), DB::raw('sum(credit_amount) as credit_amount'), DB::raw($a->id . ' as account_id'), 'transaction_type')->where('account_id', $a->id)->wherein('company_id', $company_id)->where('status', 1);

                            if ($request->transaction_no != "") {
                                $data_query->wherein('transaction_no', $request->transaction_no)->where('account_id', $a->id);
                            }

                            if ($a->id == 7642) {
                                $cash_supplier_list = SysHelper::get_cash_supplier($a->id);
                                if (count($cash_supplier_list) > 0) {
                                    $data_query->wherein('transaction_no', $cash_supplier_list)->where('account_id', $a->id);
                                } else {
                                    $data_query->where('transaction_no', '0')->where('account_id', $a->id);
                                }
                                $data_query->wherein('transaction_type', ['purchaseinvoice', 'purchasereturn', 'opbinvoice', 'openingbalance111', 'salesinvoice']);
                            } else {
                                $data_query->wherein('transaction_type', ['purchaseinvoice', 'purchasereturn', 'opbinvoice', 'openingbalance111', 'salesinvoice']);
                            }

                            $data_query->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') <= '" . $till_date . "'");
                            $data_all[] = $data_query->groupby('transaction_date', 'transaction_id', 'transaction_no', 'transaction_type', $this->payableOsSalesInvoiceRowGroup())->orderby('transaction_date', 'asc')->get();

                            //return $data_all;

                            $list_of_unadjusted = SysHelper::get_list_of_payable_unadjusted($accounts->pluck('id'), $com_id);
                            $list_of_unadjusted_jv_to_jv = SysHelper::get_list_of_payable_unadjusted_jv_to_jv($accounts->pluck('id'), $com_id);
                            $list_of_unadjusted_pdc = SysHelper::get_list_of_payable_unadjusted_pdc($accounts->pluck('id'), $com_id);
                            $list_of_adjusted_pdc = SysHelper::get_list_of_payable_adjusted_pdc($accounts->pluck('id'), $com_id);

                            $opb_balance_amount = SysHelper::get_supplier_opening_balance($accounts->pluck('id'), date('Y-m-d'), $com_id);

                            /*$pdc_list = SysPayment::select('doc_date','doc_number','payment_mode','cat.account_id','cat.debit_amount','cat.credit_amount','cheque_date','cheque_number','payment_date','cat.remarks', DB::raw('GROUP_CONCAT(adj.bi_doc_no) as bi_doc_no'))
                            ->join('sys_chartofaccounts_transaction as cat','cat.transaction_no','sys_payment.doc_number')
                            ->leftjoin('sys_payment_adjustments as adj' ,'adj.bi_doc_number','sys_payment.doc_number')
                            ->where('sys_payment.pdc_removed_os',1)
                            ->where('sys_payment.status', 1)->where('sys_payment.mode', 2)->where('sys_payment.payment_through', 3)->where('sys_payment.company_id',$com_id)
                            ->groupby('doc_date','doc_number','payment_mode','cat.account_id','cat.debit_amount','cat.credit_amount','cheque_date','cheque_number','payment_date','cat.remarks')->get();*/

                            //$pdc_list = SysPayment::select('doc_date','doc_number','payment_mode','cat.account_id','cat.debit_amount','cat.credit_amount','cheque_date','cheque_number','payment_date','cat.remarks','adj.bi_doc_no')->join('sys_chartofaccounts_transaction as cat','cat.transaction_no','sys_payment.doc_number')
                            //->leftjoin('sys_payment_adjustments as adj' ,'adj.bi_doc_number','sys_payment.doc_number')
                            //->where('sys_payment.status', 1)->where('sys_payment.mode', 2)->where('sys_payment.payment_through', 3)->where('sys_payment.company_id',$com_id)->get();
                        }
                    }
                }
                $data_all = [];

            }




            if ($_POST) {
                $first_load = false;

                $till_date = SysHelper::normalizeToYmd($request->till_date);
                $transaction_no1[0] = $request->transaction_no;

                $doc_date = $request->doc_date;
                $deal_id = $request->deal_id;
                $amount = $request->amount;
                $overdue = $request->overdue;
                $ageing = $request->ageing;
                $account_id = $request->account_id;


                if ($request->account_id) {
                    if (count($request->account_id) > 0) {


                        if (is_array($account_id) && in_array("view_all_supp", $account_id)) {
                            $is_view_all_supp = true;

                            $account_id = $accounts_select->pluck('id');
                        }

                        $accounts = SysChartofAccounts::select('sys_chartofaccounts.id', 'sys_chartofaccounts.account_name', 'sys_chartofaccounts.account_code')
                            ->where('sys_chartofaccounts.status', 1)->wherein('sys_chartofaccounts.id', $account_id)->orderby('sys_chartofaccounts.account_name', 'asc')->get();
                    }
                }

                // filter accounts by comment follow-up date range if provided
                if ($request->followup_from || $request->followup_to) {
                    $ctrl_followup_from = $request->followup_from;
                    $ctrl_followup_to = $request->followup_to;
                    $queryComments = DB::table('outstand_comments_payable')->where('company_id', $com_id);
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

                if (count($accounts) > 0) {
                    foreach ($accounts as $a) {
                        $transaction_no = SysChartofAccountsTransaction::where('account_id', $a->id)->where('status', 1)->where('company_id', $com_id)->pluck('transaction_no');
                        if (count($transaction_no) > 0) {
                            $data_query = SysChartofAccountsTransaction::select('transaction_date', 'transaction_id', 'transaction_no', DB::raw('sum(debit_amount) as debit_amount'), DB::raw('sum(credit_amount) as credit_amount'), DB::raw($a->id . ' as account_id'), 'transaction_type')->where('account_id', $a->id)->wherein('company_id', $company_id)->where('status', 1);

                            if ($request->transaction_no != "") {
                                $data_query->where('transaction_no', $request->transaction_no)->where('account_id', $a->id);
                            }

                            if ($request->deal_id != "") {
                                $deal_id = SysHelper::get_dealid_from_code($request->deal_id);
                                $deal_trn_no = SysPurchaseInvoice::where('deal_id', $deal_id)->where('status', 1)->where('company_id', $com_id)->where("vendors", $a->id)->pluck('doc_number');
                                if (count($deal_trn_no) > 0) {
                                    $data_query->wherein('transaction_no', $deal_trn_no)->where('account_id', $a->id);
                                } else {
                                    $data_query->where('transaction_no', '0')->where('account_id', $a->id);
                                }
                            }
                            if ($request->sales_person != "") {
                                $sp_trn_no = SysPurchaseInvoice::wherein('created_by', $request->sales_person)->where('status', 1)->where('company_id', $com_id)->where("vendors", $a->id)->pluck('doc_number');
                                if (count($sp_trn_no) > 0) {
                                    $data_query->wherein('transaction_no', $sp_trn_no)->where('account_id', $a->id);
                                } else {
                                    $data_query->where('transaction_no', '0')->where('account_id', $a->id);
                                }
                                $ctrl_sales_person = $request->sales_person;
                            }

                            if ($request->amount != "") {
                                $amount_d_trn_no = SysChartofAccountsTransaction::where("account_id", $a->id)->where('status', 1)
                                    ->whereRaw("credit_amount = " . $request->amount . "")->where('company_id', $com_id)->pluck('transaction_no');
                                if (count($amount_d_trn_no) > 0) {
                                    $data_query->wherein('transaction_no', $amount_d_trn_no)->where('account_id', $a->id);
                                } else {
                                    $data_query->where('transaction_no', '0')->where('account_id', $a->id);
                                }
                            }

                            if ($overdue != "") {
                                $overdue_list = SysHelper::get_payable_os_by_overdue($overdue, $a->id, $till_date);
                                if (count($overdue_list) > 0) {
                                    $data_query->wherein('transaction_no', $overdue_list)->where('account_id', $a->id);
                                } else {
                                    $data_query->where('transaction_no', '0')->where('account_id', $a->id);
                                }
                                $ctrl_overdue = $request->overdue;

                            }

                            if ($ageing != "") {
                                $ageing_list = SysHelper::get_payable_os_by_ageing($ageing, $a->id, $till_date);
                                if (count($ageing_list) > 0) {
                                    $data_query->wherein('transaction_no', $ageing_list)->where('account_id', $a->id);
                                } else {
                                    $data_query->where('transaction_no', '0')->where('account_id', $a->id);
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

                            if ($request->list_in_ex != "") {
                                $ctrl_intext = $request->list_in_ex;
                            }

                            if ($request->list_in_basic != "") {
                                $ctrl_basic_search = $request->list_in_basic;
                            }

                            if ($request->followup_from || $request->followup_to) {
                                $ctrl_followup_from = $request->followup_from;
                                $ctrl_followup_to = $request->followup_to;
                            }

                            if ($a->id == 7642) {
                                $cash_supplier_list = SysHelper::get_cash_supplier($a->id);
                                if (count($cash_supplier_list) > 0) {
                                    $data_query->wherein('transaction_no', $cash_supplier_list)->where('account_id', $a->id);
                                } else {
                                    $data_query->where('transaction_no', '0')->where('account_id', $a->id);
                                }
                                $data_query->wherein('transaction_type', ['purchaseinvoice', 'purchasereturn', 'opbinvoice', 'openingbalance111', 'salesinvoice']);
                            } else {
                                $data_query->wherein('transaction_type', ['purchaseinvoice', 'purchasereturn', 'opbinvoice', 'openingbalance111', 'salesinvoice']);
                            }



                            $data_query->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') <= '" . $till_date . "'");
                            $data_all[] = $data_query->groupby('transaction_date', 'transaction_id', 'transaction_no', 'transaction_type', $this->payableOsSalesInvoiceRowGroup())->orderby('transaction_date', 'asc')->get();
                        }
                    }
                }

                //return $data_all;

                /*$pdc_list = SysPayment::select('doc_date','doc_number','payment_mode','cat.account_id','cat.debit_amount','cat.credit_amount','cheque_date','cheque_number','payment_date','cat.remarks', DB::raw('GROUP_CONCAT(adj.bi_doc_no) as bi_doc_no'))
                ->join('sys_chartofaccounts_transaction as cat','cat.transaction_no','sys_payment.doc_number')
                ->leftjoin('sys_payment_adjustments as adj' ,'adj.bi_doc_number','sys_payment.doc_number')
                ->where('sys_payment.pdc_removed_os',1)
                ->where('sys_payment.status', 1)->where('sys_payment.mode', 2)->where('sys_payment.payment_through', 3)->where('sys_payment.company_id',$com_id)
                ->groupby('doc_date','doc_number','payment_mode','cat.account_id','cat.debit_amount','cat.credit_amount','cheque_date','cheque_number','payment_date','cat.remarks')->get();*/

                // $account_id = [$request->account_id];
                $list_of_unadjusted = SysHelper::get_list_of_payable_unadjusted($account_id, $com_id);
                $list_of_unadjusted_jv_to_jv = SysHelper::get_list_of_payable_unadjusted_jv_to_jv($account_id, $com_id);
                $list_of_unadjusted_pdc = SysHelper::get_list_of_payable_unadjusted_pdc($account_id, $com_id);
                $list_of_adjusted_pdc = SysHelper::get_list_of_payable_adjusted_pdc($account_id, $com_id);
                $opb_balance_amount = SysHelper::get_supplier_opening_balance($account_id, date('Y-m-d'), $com_id);


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
            }

            $viewSupport = $this->loadPayableOutstandingViewData($com_id);
            extract($viewSupport);

            return view('backEnd.outstanding.payableoutstanding', compact('data', 'accounts', 'account_id', 'till_date', 'data_adjestment', 'data_payment', 'data_all', 'overdue', 'ageing', 'doc_date', 'sales_person_list', 'opbinvoice', 'opbinvoice_map', 'list_of_unadjusted', 'list_of_unadjusted_jv_to_jv', 'list_of_unadjusted_pdc', 'list_of_adjusted_pdc', 'opb_balance_amount', 'ctrl_overdue', 'ctrl_ageing', 'is_view_all_supp', 'accounts_select', 'ctrl_sales_person', 'list_option', 'ctrl_list_option', 'first_load', 'ctrl_basic_search', 'ctrl_intext', 'ctrl_followup_from', 'ctrl_followup_to', 'payment_terms_map', 'max_installments', 'purchase_invoice_map', 'sales_invoice_map', 'payable_finance_rate'));

        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function update_payable_pdc(Request $request)
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

            if ($status != 3) {
                SysPayment::where('doc_number', $request->doc_id)->update([
                    'payment_date' => $mysqlDate,
                    'pdc_removed_os' => $pdc_removed_os,
                    'cheque_status' => 2
                ]);
            } else {
                $p_status = SysPayment::where('doc_number', $request->doc_id)->first();
                if ($p_status->cheque_status == 2) {
                    SysPayment::where('doc_number', $request->doc_id)->update([
                        'payment_date' => $mysqlDate,
                        'pdc_removed_os' => $pdc_removed_os,
                        'cheque_status' => 4
                    ]);
                }else{
                    SysPayment::where('doc_number', $request->doc_id)->update([
                        'payment_date' => $mysqlDate,
                        'pdc_removed_os' => $pdc_removed_os,
                    ]);
                }

            }


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
            $com_id = session('logged_session_data.company_id');

            $data = [];
            $data_all = [];
            $accounts = SysHelper::get_supplier_list($company_id);
            $com_id = session('logged_session_data.company_id');
            $account_id = "";
            $till_date = date('Y-m-d');

            $data_adjestment = [];
            $data_payment = [];
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
                                $data_query->wherein('transaction_type', ['purchaseinvoice', 'purchasereturn', 'opbinvoice', 'openingbalance111', 'salesinvoice']);
                                $data_query->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') < '" . $till_date . "'");
                                $data_all[] = $data_query->groupby('transaction_date', 'transaction_id', 'transaction_no', $this->payableOsSalesInvoiceRowGroup())->orderby('transaction_date', 'asc')->get();
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
                                $data_query->wherein('transaction_type', ['purchaseinvoice', 'purchasereturn', 'opbinvoice', 'openingbalance111', 'salesinvoice']);
                                $data_query->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') < '" . $till_date . "'");
                                $data_all[] = $data_query->groupby('transaction_date', 'transaction_id', 'transaction_no', $this->payableOsSalesInvoiceRowGroup())->orderby('transaction_date', 'asc')->get();
                            }
                        }
                    }
                }
            }

            if (count($data) > 0) {
                $data_adjestment = SysPurchaseReturnAdjestment::select('piv_no', DB::raw('sum(paid_amount) as paid_amount'))->wherein('piv_no', $data->pluck("transaction_no"))->groupby('piv_no')->get();

                $data_payment = DB::table('sys_payment as p')->select('pa.bi_doc_no', 'p.doc_number', 'pa.bi_amount', 'p.payment_through', 'p.payment_date', 'p.cheque_number', 'p.cheque_bank_name')
                    ->join('sys_payment_adjustments as pa', 'pa.bi_doc_number', 'p.doc_number')->where('pa.account_id', $account_id)->wherein('bi_doc_no', $data->pluck("transaction_no"))->where('p.status', 1)->get();
            }

            return view('backEnd.outstanding.payableoutstanding', compact('data', 'accounts', 'account_id', 'till_date', 'data_adjestment', 'data_payment', 'data_all'));

        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function download($account, $date)
    {
        $com_id = session('logged_session_data.company_id');
        $transaction_no = SysChartofAccountsTransaction::where('account_id', $account)->where('status', 1)->where('company_id', $com_id)->pluck('transaction_no');

        $company = SysCompany::find($com_id);

        $cust_detail = SysCustSuppl::select('sys_cust_suppl.*')->join('sys_chartofaccounts', 'sys_chartofaccounts.account_code', 'sys_cust_suppl.code')->where('sys_chartofaccounts.id', $account)->first();
        $cust_address = SysCustSupplAddressbook::where('cust_suppl_id', $cust_detail->id)->orderby('id', 'desc')->first();

        if (count($transaction_no) > 0) {
            $data_query = SysChartofAccountsTransaction::select('transaction_date', 'transaction_id', 'transaction_no', DB::raw('sum(debit_amount) as debit_amount'), DB::raw('sum(credit_amount) as credit_amount'), DB::raw($account . ' as account_id'))->where('company_id', $com_id);
            $data_query->wherein('transaction_no', $transaction_no)->where('status', 1);
            $data_query->wherein('transaction_type', ['purchaseinvoice', 'purchasereturn', 'opbinvoice', 'openingbalance111', 'salesinvoice']);
            $data_query->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') <= '" . $date . "'");
            $payable = $data_query->groupby('transaction_date', 'transaction_id', 'transaction_no', $this->payableOsSalesInvoiceRowGroup())->orderby('transaction_date', 'asc')->get();


            $data_adjestment = SysPurchaseReturnAdjestment::select('piv_no', DB::raw('sum(paid_amount) as paid_amount'))->wherein('piv_no', $payable->pluck("transaction_no"))->groupby('piv_no')->get();

            $data_payment = DB::table('sys_payment as p')->select('pa.bi_doc_no', 'p.doc_number', 'pa.bi_amount', 'p.payment_through', 'p.payment_date', 'p.cheque_number', 'p.cheque_bank_name')
                ->join('sys_payment_adjustments as pa', 'pa.bi_doc_number', 'p.doc_number')->where('pa.account_id', $payable[0]->account_id)->wherein('bi_doc_no', $payable->pluck("transaction_no"))->where('p.status', 1)->get();


            $list_of_unadjusted = SysHelper::get_list_of_payable_unadjusted([$account], $com_id);
            $list_of_unadjusted_jv_to_jv = SysHelper::get_list_of_payable_unadjusted_jv_to_jv([$account], $com_id);
            $list_of_unadjusted_pdc = SysHelper::get_list_of_payable_unadjusted_pdc([$account], $com_id);
            $list_of_adjusted_pdc = SysHelper::get_list_of_payable_adjusted_pdc([$account], $com_id);

        }

        $generate_date = Carbon::now('+04:00')->format('d/m/Y H:i a');

        $data = [
            'company' => $company,
            'cust_detail' => $cust_detail,
            'cust_address' => $cust_address,
            'payable' => $payable,
            'data_adjestment' => $data_adjestment,
            'data_payment' => $data_payment,
            'date' => $date,
            'generate_date' => $generate_date,
            'list_of_unadjusted' => $list_of_unadjusted,
            'list_of_unadjusted_jv_to_jv' => $list_of_unadjusted_jv_to_jv,
            'list_of_unadjusted_pdc' => $list_of_unadjusted_pdc,
            'list_of_adjusted_pdc' => $list_of_adjusted_pdc,
        ];

        //return  view('backEnd.pdf_print.payable_os_pdf',$data);
        $pdf = PDF::loadView('backEnd.pdf_print.payable_os_pdf', $data);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download($cust_detail->name . " - payable_outstanding.pdf");
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
            $check = SysPaymentAdjustmentsTemp::where($data)->count();
            if ($check == 0) {
                $sra = new SysPaymentAdjustmentsTemp();
                $sra->transaction_type = $request->transaction_type;
                $sra->bi_cheque_amount = str_replace(',', '', $request->bi_cheque_amount);
                $sra->bi_amount_adjusted = str_replace(',', '', $request->bi_amount_adjusted);
                $sra->bi_balance_to_adjust = str_replace(',', '', $request->bi_balance_to_adjust);
                $sra->bi_extra_amount = str_replace(',', '', $request->bi_extra_amount);
                $sra->bi_doc_no = $request->bi_doc_no;
                $sra->bi_doc_number = $request->bi_doc_number;
                $sra->bi_doc_date = date('Y-m-d', strtotime($request->bi_doc_date));
                $sra->bi_lpo_no = $request->bi_lpo_no;
                $sra->bi_bill_number = $request->bi_bill_number;
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
            $ret = $e;
            return json_encode(array('data' => $ret));
        }
    }

    public function store_temp_delete(Request $request)
    {
        try {
            SysPaymentAdjustmentsTemp::where('bi_doc_number', $request->doc_number)->where('account_id', $request->account_id)->delete();
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
            SysPaymentAdjustments::where('bi_doc_number', $request->doc_number2)->where('account_id', $request->br_account_id)->delete();

            $transaction = SysChartofAccountsTransaction::where('transaction_no', $request->doc_number2)->get();
            if ($transaction->where('account_id', $request->br_account_id)->count() == 0) {
                SysHelper::trn_chartof_accounts_transaction_with_main($request->br_account_id, $transaction[0]->transaction_id, $request->doc_number2, $transaction[0]->transaction_date, $request->transaction_type2, $request->br_account_id_amount, '0.00', '', 1, 0, "", 1, 0);
            }

            $amount = SysChartofAccountsTransaction::where(['transaction_no' => $request->doc_number2])->sum('debit_amount');
            SysChartofAccountsTransaction::where(['transaction_no' => $request->doc_number2])
                ->where(['is_main_account' => 1])->where(['debit_amount' => '0.00'])->update(['credit_amount' => $amount]);

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
                        'bi_bill_number' => $request->bi_bill_number[$i],
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
                SysPaymentAdjustments::insert($temp_data);
            }
            DB::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();

        }
    }
    // public function store_delete_before_update(Request $request)
    // {
    //     try{
    //         SysPaymentAdjustments::where('bi_doc_number',$request->doc_number)->delete();        
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

            $val = DB::table('outstand_comments_payable')->where('comment_id', $id)->get();
            // use Laravel's response helper to send proper JSON content-type
            return response()->json($val);
        } catch (\Throwable $th) {
            // on error we still return JSON so the client can handle it gracefully
            Toastr::error('Something went wrong, please try again', 'Failed');
            return response()->json(['error' => $th->getMessage()]);
        }
    }

    public function outstanding_comment_save(Request $request)
    {
        try {

            //   $id = $request->id;
            $id = Auth::user()->id;
            $username = DB::table('users')->where('id', $id)->first();

            $attachment = null;
            if ($request->hasFile('remark_file')) {
                $file = $request->file('remark_file');
                $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
                $file->move(public_path('uploads/outstand_comments_doc'), $filename);
                $attachment = $filename;
            }


            // prepare data with optional columns
            $data = [
                'comment_id' => $request->id_deal,
                'comment' => $request->comment,
                'status' => 0,
                'company_id' => session('logged_session_data.company_id'),
                'username' => $username->full_name,
                'created_by' => Auth::user()->id,
                'created_at' => Carbon::now('+04:00'),
            ];

            $data['followup_date'] = SysHelper::normalizeToYmd($request->remark_date) ?: null;


            $data['file'] = $attachment;


            $data['is_deleted'] = 0;

            DB::table('outstand_comments_payable')->insert($data);
            return response()->json(['status' => 'SUCCESS']);
            //return redirect()->back();
        } catch (\Throwable $th) {
            return response()->json(['status' => 'ERROR', 'message' => $th->getMessage()]);
        }
    }

    // end geo

    public function outstanding_comment_delete_payable(Request $request)
    {
        try {
            $commentId = $request->comment_id;

            DB::table('outstand_comments_payable')
                ->where('id', $commentId)
                ->where('created_by', Auth::user()->id)
                ->update(['is_deleted' => 1]);



            return json_encode(['data' => 'SUCCESS']);
        } catch (\Throwable $th) {
            return json_encode(['data' => 'ERROR']);
        }
    }

    public function SupplierAgeingReport(Request $request)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $com_id = session('logged_session_data.company_id');

            $data_all = [];
            $accounts = SysHelper::get_supplier_list($company_id);
            $till_date = date('Y-m-d');
            $ctrl_intext = '';

            if (!$_POST) {
                if (SysHelper::get_pagination_post($request)) {
                    $com_id = $request->com;
                    $company_id = [$request->com];
                }

                if (count($accounts) > 0) {
                    foreach ($accounts as $a) {
                        $dq = $this->buildSupplierAgeingDataQuery($a, $company_id, $com_id, $till_date);
                        if ($dq !== null && count($dq) > 0) {
                            $data_all[] = $dq;
                        }
                    }
                }
            }

            if ($_POST) {
                $till_date = SysHelper::normalizeToYmd($request->till_date);

                if ($request->list_in_ex != '') {
                    $accounts = SysChartofAccounts::select('sys_chartofaccounts.id', 'sys_chartofaccounts.account_name', 'sys_chartofaccounts.account_code')
                        ->whereIn('sys_chartofaccounts.id', $accounts->pluck('id'))
                        ->where('sys_chartofaccounts.internal', $request->list_in_ex)
                        ->where('sys_chartofaccounts.status', 1)
                        ->orderby('sys_chartofaccounts.account_name', 'asc')
                        ->get();
                    $ctrl_intext = $request->list_in_ex;
                }

                if (count($accounts) > 0) {
                    foreach ($accounts as $a) {
                        $dq = $this->buildSupplierAgeingDataQuery($a, $company_id, $com_id, $till_date);
                        if ($dq !== null && count($dq) > 0) {
                            $data_all[] = $dq;
                        }
                    }
                }
            }

            $accountIdsForUnadj = collect($data_all)->map(function ($chunk) {
                return count($chunk) > 0 ? $chunk[0]->account_id : null;
            })->filter()->unique()->values();

            $list_of_unadjusted = $accountIdsForUnadj->isNotEmpty()
                ? SysHelper::get_list_of_payable_unadjusted($accountIdsForUnadj, $com_id)
                : collect([]);
            $list_of_unadjusted_jv_to_jv = $accountIdsForUnadj->isNotEmpty()
                ? SysHelper::get_list_of_payable_unadjusted_jv_to_jv($accountIdsForUnadj, $com_id)
                : collect([]);
            $opb_balance_amount = $accountIdsForUnadj->isNotEmpty()
                ? SysHelper::get_supplier_opening_balance($accountIdsForUnadj, date('Y-m-d'), $com_id)
                : collect([]);

            $osViewData = $this->loadPayableOutstandingViewData($com_id);

            return view('backEnd.outstanding.supplierageingreport', array_merge(
                compact(
                    'accounts',
                    'till_date',
                    'data_all',
                    'ctrl_intext',
                    'com_id',
                    'list_of_unadjusted',
                    'list_of_unadjusted_jv_to_jv',
                    'opb_balance_amount'
                ),
                $osViewData
            ));

        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    private function payableOsAggregateSelect($accountIdExpression)
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

    private function buildSupplierAgeingDataQuery($account, $companyIds, $comId, $tillDate)
    {
        $transactionNo = SysChartofAccountsTransaction::where('account_id', $account->id)
            ->where('status', 1)
            ->where('company_id', $comId)
            ->pluck('transaction_no');

        if (count($transactionNo) === 0) {
            return null;
        }

        $dataQuery = SysChartofAccountsTransaction::select(...$this->payableOsAggregateSelect((string) $account->id))
            ->where('account_id', $account->id)
            ->whereIn('company_id', $companyIds)
            ->where('status', 1);

        if ($account->id == 7642) {
            $cashSupplierList = SysHelper::get_cash_supplier($account->id);
            if (count($cashSupplierList) > 0) {
                $dataQuery->whereIn('transaction_no', $cashSupplierList);
            } else {
                $dataQuery->where('transaction_no', '0');
            }
            $dataQuery->whereIn('transaction_type', ['purchaseinvoice', 'purchasereturn', 'opbinvoice', 'openingbalance111', 'salesinvoice']);
        } else {
            $dataQuery->whereIn('transaction_type', ['purchaseinvoice', 'purchasereturn', 'opbinvoice', 'openingbalance111', 'salesinvoice']);
        }

        $dataQuery->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') <= '" . $tillDate . "'");

        return $dataQuery
            ->groupBy('transaction_date', 'transaction_id', 'transaction_no', 'transaction_type', $this->payableOsSalesInvoiceRowGroup())
            ->orderBy('transaction_date', 'asc')
            ->get();
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

    private function loadPayableOutstandingViewData($companyId)
    {
        $payment_terms_map = SysPaymentTerms::where('active_status', 1)->get()->keyBy('id');
        $purchase_invoice_map = DB::table('sys_purchase_invoice')
            ->where('company_id', $companyId)
            ->where('status', 1)
            ->select('doc_number', 'payment_terms', 'pi_date')
            ->get()
            ->keyBy('doc_number');
        $sales_invoice_map = DB::table('sys_sales_invoice')
            ->where('company_id', $companyId)
            ->where('status', 1)
            ->select('doc_number', 'payment_terms', 'doc_date')
            ->get()
            ->keyBy('doc_number');
        $opbinvoice_map = $this->loadOpbInvoiceDetailMap($companyId);
        $opbinvoice = $opbinvoice_map->values();
        $max_installments = max(
            SysPaymentTerms::resolveMaxInstallmentsFromMaps($purchase_invoice_map, $payment_terms_map),
            SysPaymentTerms::resolveMaxInstallmentsFromMaps($sales_invoice_map, $payment_terms_map),
            SysPaymentTerms::resolveMaxInstallmentsFromOpbMap($opbinvoice_map, $payment_terms_map)
        );
        $company_row = SysCompany::find($companyId);
        $payable_finance_rate = (float) ($company_row->finance_cost_percentage ?? 0);

        return compact(
            'payment_terms_map',
            'purchase_invoice_map',
            'sales_invoice_map',
            'opbinvoice_map',
            'opbinvoice',
            'max_installments',
            'payable_finance_rate'
        );
    }

}

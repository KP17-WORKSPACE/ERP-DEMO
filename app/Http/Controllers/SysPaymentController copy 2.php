<?php

namespace App\Http\Controllers;

use App\SysChartofAccounts;
use App\SysAccountType;
use App\ApiBaseMethod;
use App\SysBankPayment;
use App\SysCashReceiptList;
use App\SysCustSuppl;
use App\SysAccountGroup;
use App\SysChartofAccountsDetails;
use App\SysChartofAccountsTransaction;
use App\SysCompany;
use App\SysCurrencySettings;
use App\SysHelper;
use App\SysLedgerEntries;
use App\SysLedgerEntriesTemp;
use App\SysPayment;
use App\SysPaymentAdjustments;
use App\SysPaymentAdjustmentsTemp;
use App\SysPaymentCheque;
use App\SysPurchaseInvoice;
use App\SysReceipt;
use App\SysReceiptAdjustments;
use App\SysReceiptAdjustmentsTemp;
use App\SysReceiptMode;
use App\SysSalesInvoice;
use App\SysSalesInvoiceCFCharges;
use App\SysTransactions;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade as PDF;
use PayPal\Api\Currency;
use App\DealTrackPoPaymentCart;
use App\SysPurchaseOrder;
use App\SysSTL;
use App\SysSTLItems;
use App\Chequebook;
use App\SysPaymentChequeDetail;

class SysPaymentController extends Controller
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
    public function paymentList(Request $request, $id = null)
    {


        try {
            //return SysHelper::get_list_of_payable_unadjusted([7515],2);
            //return SysHelper::get_list_of_payable_unadjusted_jv_to_jv([7515],1);
            //return SysHelper::get_list_of_payable_unadjusted_include_removed_jv([7515],1);
            //return SysHelper::get_list_of_payable_unadjusted_pdc([7515],1);
            //return SysHelper::get_list_of_payable_adjusted_pdc([7515],1);

        } catch (\Throwable $th) {
            //return $th;
        }


        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];

            $ctrl_doc_number = "";
            $ctrl_payment_mode = "";
            $ctrl_payment_through = "";
            $ctrl_account_name = "";
            $ctrl_amount = "";
            $ctrl_doc_date = "";
            $ctrl_payment_date = "";
            $ctrl_cheque_date = "";
            $ctrl_cheque_number = "";
            $ctrl_deal_id = "";
            $ctrl_created_by = "";

            $paymentmode_cash = SysHelper::get_cash_account();
            $paymentmode_bank = SysHelper::get_bank_account();

            $data1 = $paymentmode_cash->pluck('id');
            $data2 = $paymentmode_bank->pluck('id');
            $data3 = array_merge($data1->toArray(), $data2->toArray());
            $accounts = SysHelper::get_supplier_list($company_id);
            $payment_mode_list = array_merge($paymentmode_cash->toArray(), $paymentmode_bank->toArray());

            $staff_list = SysHelper::get_staff_list();

            $query = SysPayment::select('sys_payment.id', 'sys_payment.doc_number', 'sys_payment.mode', 'sys_payment.payment_mode', 'sys_payment.payment_through', 'a.account_name', 'c.debit_amount', 'c.credit_amount', 'sys_payment.doc_date', 'sys_payment.payment_date', 'sys_payment.cheque_date', 'sys_payment.cheque_number', 'u.full_name', 'sys_payment.narration', 'sys_payment.status', 'sys_payment.deal_id')->selectRaw('1 as type')
                ->leftjoin('sys_chartofaccounts_transaction as c', 'c.transaction_no', 'sys_payment.doc_number')
                ->leftjoin('sys_chartofaccounts as a', 'a.id', 'c.account_id')
                ->leftjoin('users as u', 'u.id', 'sys_payment.created_by');

            $doc = SysChartofAccountsTransaction::wherein('transaction_type', ['bankpayment', 'cashpayment'])->wherein('company_id', $company_id)->wherenotin('account_id', $data3)->pluck('transaction_no');
            $query2 = SysPayment::select('sys_payment.id', 'sys_payment.doc_number', 'sys_payment.mode', 'sys_payment.payment_mode', 'sys_payment.payment_through', 'sys_payment.doc_date', 'sys_payment.payment_date', 'sys_payment.cheque_date', 'sys_payment.cheque_number', 'u.full_name', 'sys_payment.narration', 'sys_payment.status', 'sys_payment.deal_id')->selectRaw('2 as type')
                ->leftjoin('users as u', 'u.id', 'sys_payment.created_by')
                ->wherenotin('doc_number', $doc)->wherein('sys_payment.company_id', $company_id);

            if (SysHelper::get_pagination_post($request)) {

                if ($request->doc_number != "") {
                    $query->where('sys_payment.doc_number', $request->doc_number);
                    $query2->where('sys_payment.doc_number', $request->doc_number);
                    $ctrl_doc_number = $request->doc_number;
                }
                if ($request->payment_mode != "") {
                    $query->where('sys_payment.payment_mode', $request->payment_mode);
                    $ctrl_payment_mode = $request->payment_mode;
                }
                if ($request->payment_through != "") {
                    if ($request->payment_through == 0) {
                        $query->where('sys_payment.mode', 1);
                    } else {
                        $query->where('sys_payment.payment_through', $request->payment_through);
                    }
                    $ctrl_payment_through = $request->payment_through;
                }
                if ($request->account_name != "") {
                    $query->where('c.account_id', $request->account_name);
                    $ctrl_account_name = $request->account_name;
                }
                if ($request->amount != "") {
                    $query->where('c.debit_amount', $request->amount);
                    $ctrl_amount = $request->amount;
                }
                if ($request->doc_date != "") {
                    $query->where('sys_payment.doc_date', SysHelper::normalizeToYmd($request->doc_date));
                    $ctrl_doc_date = $request->doc_date;
                }
                if ($request->payment_date != "") {
                    $query->where('sys_payment.payment_date', SysHelper::normalizeToYmd($request->payment_date));
                    $ctrl_payment_date = $request->payment_date;
                }
                if ($request->cheque_date != "") {
                    $query->where('sys_payment.cheque_date', SysHelper::normalizeToYmd($request->cheque_date));
                    $ctrl_cheque_date = $request->cheque_date;
                }
                if ($request->cheque_number != "") {
                    $query->where('sys_payment.cheque_number', $request->cheque_number);
                    $ctrl_cheque_number = $request->cheque_number;
                }
                if ($request->deal_id != "") {
                    $dealid = SysHelper::get_dealid_from_code($request->deal_id);
                    $query->whereRaw("find_in_set($dealid,sys_payment.deal_id)");
                    $ctrl_deal_id = $request->deal_id;
                }
                if ($request->created_by != "") {
                    $query->where('sys_payment.created_by', $request->created_by);
                    $ctrl_created_by = $request->created_by;
                }
            }

            $query->wherenotin('c.account_id', $data3);
            $query->wherein('sys_payment.company_id', $company_id);
            $query->orderby('sys_payment.id', 'desc');
            $payment = $query->orderby('sys_payment.payment_date', 'desc')->orderby('sys_payment.payment_date', 'desc')->get();
            //return $payment;


            $payment2 = $query2->orderby('sys_payment.doc_number', 'desc')->orderby('sys_payment.payment_date', 'desc')->get();
            $payment = $payment->merge($payment2);


            $active_id = $id;
            $data = [];
            $action = false;
            $editData = [];
            $addData = [];


            if ($request->has('pr_action')) {
                $poAction = $request->input('pr_action');

                if ($poAction === 'add') {
                    $action = 'add';
                    $addData = $this->paymentAddData(); // Get all data for adding
                } elseif ($poAction === 'edit') {
                    $action = 'edit';
                    $editData = $this->edit($active_id); // Get all data for editing
                } elseif ($poAction === 'dealtrack') {
                    $action = 'dealtrack';
                    $supp_id = $request->input('supplier_id');
                    $deal_id = $request->input('deal_id');
                    $dealtrack_id = $request->input('dealtrack_id');
                    $addData = $this->paymentAddData_Dealtrack($supp_id, $deal_id, $dealtrack_id); // Get all data for adding
                }
            } else {


                if ($id != null) {
                    $data = $this->get_payment_pdf_data($id);
                } else {

                    $firstRecord = $payment->first();
                    if ($firstRecord) {
                        $active_id = $firstRecord->id;
                        $data = $this->get_payment_pdf_data($active_id);
                    }
                }
            }



            // $data = $this->get_payment_pdf_data($id);

            return view('backEnd.payment.paymentlist', compact('payment', 'accounts', 'payment_mode_list', 'staff_list', 'ctrl_doc_number', 'ctrl_payment_mode', 'ctrl_payment_through', 'ctrl_account_name', 'ctrl_amount', 'ctrl_doc_date', 'ctrl_payment_date', 'ctrl_cheque_date', 'ctrl_cheque_number', 'ctrl_deal_id', 'ctrl_created_by', 'data', 'active_id', 'action', 'editData', 'addData'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function getDetails($id)
    {
        $data = $this->get_payment_pdf_data($id);
        if (count($data) > 0) {
            return view('backEnd.payment.p_details', $data);
        } else {
            return "error!!";
        }
    }

    public function get_payment_pdf_data($id)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $payment = SysPayment::find($id);
            $paymentList = SysChartofAccountsTransaction::where('transaction_id', $id)->wherein('transaction_type', ['cashpayment', 'bankpayment'])->where('is_main_account', 0)->get();
            $company = SysCompany::find($payment->company_id);
            $print = date('d/m/Y h:i A', strtotime(Carbon::now('+04:00')));

            $data = [
                'company' => $company,
                'payment' => $payment,
                'payment_item' => $paymentList,
                'print' => $print,
            ];
            return $data;
        } catch (\Throwable $th) {
            return [];
        }
    }

    public function paymentAddData()
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $accounts = SysHelper::get_supplier_list($company_id);
            $company = SysCompany::find(session('logged_session_data.company_id'));
            $paymentmode_cash = SysHelper::get_cash_account();
            $paymentmode_bank = SysHelper::get_bank_account();
            $currency = SysCurrencySettings::select('id', 'code')->get();
            $curr = SysHelper::cheque_print_currancy_code(session('logged_session_data.company_id'));
            $currency1 = $curr[0];
            $currency2 = $curr[1];


            $paymentmode_cash = SysHelper::get_cash_account();
            $paymentmode_bank = SysHelper::get_bank_account();

            $data1 = $paymentmode_cash->pluck('id');
            $data2 = $paymentmode_bank->pluck('id');
            $data3 = array_merge($data1->toArray(), $data2->toArray());
            $query = SysPayment::select('sys_payment.id', 'sys_payment.doc_number', 'sys_payment.mode', 'sys_payment.payment_mode', 'sys_payment.payment_through', 'a.account_name', 'c.debit_amount', 'c.credit_amount', 'sys_payment.doc_date', 'sys_payment.payment_date', 'sys_payment.cheque_date', 'sys_payment.cheque_number', 'u.full_name', 'sys_payment.narration', 'sys_payment.status', 'sys_payment.deal_id')->selectRaw('1 as type')
                ->leftjoin('sys_chartofaccounts_transaction as c', 'c.transaction_no', 'sys_payment.doc_number')
                ->leftjoin('sys_chartofaccounts as a', 'a.id', 'c.account_id')
                ->leftjoin('users as u', 'u.id', 'sys_payment.created_by');

            $doc = SysChartofAccountsTransaction::wherein('transaction_type', ['bankpayment', 'cashpayment'])->wherein('company_id', $company_id)->wherenotin('account_id', $data3)->pluck('transaction_no');
            $query2 = SysPayment::select('sys_payment.id', 'sys_payment.doc_number', 'sys_payment.mode', 'sys_payment.payment_mode', 'sys_payment.payment_through', 'sys_payment.doc_date', 'sys_payment.payment_date', 'sys_payment.cheque_date', 'sys_payment.cheque_number', 'u.full_name', 'sys_payment.narration', 'sys_payment.status', 'sys_payment.deal_id')->selectRaw('2 as type')
                ->leftjoin('users as u', 'u.id', 'sys_payment.created_by')
                ->wherenotin('doc_number', $doc)->wherein('sys_payment.company_id', $company_id);
            $query->wherenotin('c.account_id', $data3);
            $query->wherein('sys_payment.company_id', $company_id);
            $query->orderby('sys_payment.id', 'desc');
            $payment = $query->orderby('sys_payment.payment_date', 'desc')->orderby('sys_payment.payment_date', 'desc')->get();
            $payment2 = $query2->orderby('sys_payment.doc_number', 'desc')->orderby('sys_payment.payment_date', 'desc')->get();
            $payment = $payment->merge($payment2);


            return compact('payment', 'accounts', 'paymentmode_cash', 'paymentmode_bank', 'currency', 'company', 'currency1', 'currency2');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function paymentAddData_Dealtrack($supp_id, $deal_id, $dealtrack_id)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $accounts = SysHelper::get_supplier_list($company_id);
            $company = SysCompany::find(session('logged_session_data.company_id'));
            $paymentmode_cash = SysHelper::get_cash_account();
            $paymentmode_bank = SysHelper::get_bank_account();
            $currency = SysCurrencySettings::select('id', 'code')->get();
            $curr = SysHelper::cheque_print_currancy_code(session('logged_session_data.company_id'));
            $currency1 = $curr[0];
            $currency2 = $curr[1];


            $paymentmode_cash = SysHelper::get_cash_account();
            $paymentmode_bank = SysHelper::get_bank_account();

            $data1 = $paymentmode_cash->pluck('id');
            $data2 = $paymentmode_bank->pluck('id');
            $data3 = array_merge($data1->toArray(), $data2->toArray());
            $query = SysPayment::select('sys_payment.id', 'sys_payment.doc_number', 'sys_payment.mode', 'sys_payment.payment_mode', 'sys_payment.payment_through', 'a.account_name', 'c.debit_amount', 'c.credit_amount', 'sys_payment.doc_date', 'sys_payment.payment_date', 'sys_payment.cheque_date', 'sys_payment.cheque_number', 'u.full_name', 'sys_payment.narration', 'sys_payment.status', 'sys_payment.deal_id')->selectRaw('1 as type')
                ->leftjoin('sys_chartofaccounts_transaction as c', 'c.transaction_no', 'sys_payment.doc_number')
                ->leftjoin('sys_chartofaccounts as a', 'a.id', 'c.account_id')
                ->leftjoin('users as u', 'u.id', 'sys_payment.created_by');

            $doc = SysChartofAccountsTransaction::wherein('transaction_type', ['bankpayment', 'cashpayment'])->wherein('company_id', $company_id)->wherenotin('account_id', $data3)->pluck('transaction_no');
            $query2 = SysPayment::select('sys_payment.id', 'sys_payment.doc_number', 'sys_payment.mode', 'sys_payment.payment_mode', 'sys_payment.payment_through', 'sys_payment.doc_date', 'sys_payment.payment_date', 'sys_payment.cheque_date', 'sys_payment.cheque_number', 'u.full_name', 'sys_payment.narration', 'sys_payment.status', 'sys_payment.deal_id')->selectRaw('2 as type')
                ->leftjoin('users as u', 'u.id', 'sys_payment.created_by')
                ->wherenotin('doc_number', $doc)->wherein('sys_payment.company_id', $company_id);
            $query->wherenotin('c.account_id', $data3);
            $query->wherein('sys_payment.company_id', $company_id);
            $query->orderby('sys_payment.id', 'desc');
            $payment = $query->orderby('sys_payment.payment_date', 'desc')->orderby('sys_payment.payment_date', 'desc')->get();
            $payment2 = $query2->orderby('sys_payment.doc_number', 'desc')->orderby('sys_payment.payment_date', 'desc')->get();
            $payment = $payment->merge($payment2);

            //fetch dealtrack po payment cart data
            $dealtrack_po_payments = DealTrackPoPaymentCart::where('supplier_id', $supp_id)
                ->where('deal_id', $deal_id)
                ->where('deal_track_id', $dealtrack_id)
                ->get();

            $supplier_details = SysChartofAccounts::with('cust_suppl')->find($supp_id);






            return compact('payment', 'accounts', 'paymentmode_cash', 'paymentmode_bank', 'currency', 'company', 'currency1', 'currency2', 'dealtrack_po_payments', 'supplier_details', 'supp_id', 'deal_id', 'dealtrack_id');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function paymentAdd(Request $request, $page_id = null)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $accounts = SysHelper::get_supplier_list($company_id);
            $company = SysCompany::find(session('logged_session_data.company_id'));
            $paymentmode_cash = SysHelper::get_cash_account();
            $paymentmode_bank = SysHelper::get_bank_account();
            $currency = SysCurrencySettings::select('id', 'code')->get();
            $curr = SysHelper::cheque_print_currancy_code(session('logged_session_data.company_id'));
            $currency1 = $curr[0];
            $currency2 = $curr[1];


            $paymentmode_cash = SysHelper::get_cash_account();
            $paymentmode_bank = SysHelper::get_bank_account();

            $data1 = $paymentmode_cash->pluck('id');
            $data2 = $paymentmode_bank->pluck('id');
            $data3 = array_merge($data1->toArray(), $data2->toArray());
            $query = SysPayment::select('sys_payment.id', 'sys_payment.doc_number', 'sys_payment.mode', 'sys_payment.payment_mode', 'sys_payment.payment_through', 'a.account_name', 'c.debit_amount', 'c.credit_amount', 'sys_payment.doc_date', 'sys_payment.payment_date', 'sys_payment.cheque_date', 'sys_payment.cheque_number', 'u.full_name', 'sys_payment.narration', 'sys_payment.status', 'sys_payment.deal_id')->selectRaw('1 as type')
                ->leftjoin('sys_chartofaccounts_transaction as c', 'c.transaction_no', 'sys_payment.doc_number')
                ->leftjoin('sys_chartofaccounts as a', 'a.id', 'c.account_id')
                ->leftjoin('users as u', 'u.id', 'sys_payment.created_by');

            $doc = SysChartofAccountsTransaction::wherein('transaction_type', ['bankpayment', 'cashpayment'])->wherein('company_id', $company_id)->wherenotin('account_id', $data3)->pluck('transaction_no');
            $query2 = SysPayment::select('sys_payment.id', 'sys_payment.doc_number', 'sys_payment.mode', 'sys_payment.payment_mode', 'sys_payment.payment_through', 'sys_payment.doc_date', 'sys_payment.payment_date', 'sys_payment.cheque_date', 'sys_payment.cheque_number', 'u.full_name', 'sys_payment.narration', 'sys_payment.status', 'sys_payment.deal_id')->selectRaw('2 as type')
                ->leftjoin('users as u', 'u.id', 'sys_payment.created_by')
                ->wherenotin('doc_number', $doc)->wherein('sys_payment.company_id', $company_id);
            $query->wherenotin('c.account_id', $data3);
            $query->wherein('sys_payment.company_id', $company_id);
            $query->orderby('sys_payment.id', 'desc');
            $payment = $query->orderby('sys_payment.payment_date', 'desc')->orderby('sys_payment.payment_date', 'desc')->get();
            $payment2 = $query2->orderby('sys_payment.doc_number', 'desc')->orderby('sys_payment.payment_date', 'desc')->get();
            $payment = $payment->merge($payment2);


            return view('backEnd.payment.paymentadd', compact('payment', 'accounts', 'paymentmode_cash', 'paymentmode_bank', 'currency', 'page_id', 'company', 'currency1', 'currency2'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    public function paymentAddFromCheque($id)
    {
        try {
            $page_id = null;
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $accounts = SysHelper::get_supplier_list($company_id);
            $company = SysCompany::find(session('logged_session_data.company_id'));
            $paymentmode_cash = SysHelper::get_cash_account();
            $paymentmode_bank = SysHelper::get_bank_account();
            $currency = SysCurrencySettings::select('id', 'code')->get();

            $cheque_detail = SysPaymentCheque::where('id', $id)->first();

            $curr = SysHelper::cheque_print_currancy_code(session('logged_session_data.company_id'));
            $currency1 = $curr[0];
            $currency2 = $curr[1];

            return view('backEnd.payment.paymentadd', compact('accounts', 'paymentmode_cash', 'paymentmode_bank', 'currency', 'page_id', 'company', 'cheque_detail', 'currency1', 'currency2'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function getpycustlist()
    {
        $r = SysHelper::get_data_by_role();
        $company_id = $r[0];
        $accounts = SysHelper::get_supplier_list($company_id);
        $searchData = [];
        foreach ($accounts as $item) {
            $searchData[] = ['id' => $item->id, 'name' => $item->account_name];
        }

        // Always return JSON (empty array when no data) to avoid client-side parse errors
        return response()->json($searchData);
    }

    public function store(Request $request)
    {

        try {

            if ($request->mode == 3) {

                try {

                    DB::beginTransaction();

                    $stl = new SysSTL();
                    $stl->doc_number = SysHelper::get_new_code('sys_stl', 'STL', 'doc_number');
                    $stl->doc_date = SysHelper::normalizeToYmd($request->doc_date);

                    $stl->bank = $request->bank;
                    $stl->exchange_rate = $request->exchange_rate;
                    $stl->amount_usd = floatval(str_replace(',', '', $request->amount_usd));
                    $stl->amount_aed = floatval(str_replace(',', '', $request->amount_aed));
                    $stl->currency = $request->currency_stl;
                    $stl->currency_m = $request->currency_m;
                    $stl->owner_name = $request->owner_name;
                    $stl->bank_representative = $request->bank_representative;
                    $stl->vendor = $request->vendor;
                    $stl->payment_type = $request->payment_type;
                    $stl->pi_no = $request->pi_no;
                    $stl->submition_date = SysHelper::normalizeToYmd($request->submition_date);
                    $stl->narration = $request->narration_stl;
                    $stl->partial_remarks = $request->partial_remarks;
                    $stl->with_amount = $request->with_amount;
                    $stl->status = 1;
                    $stl->created_by = Auth::user()->id;
                    $stl->created_at = Carbon::now('+04:00');
                    $stl->company_id = session('logged_session_data.company_id');
                    $results = $stl->save();
                    $stl->toArray();

                    for ($i = 0; $i < count($request->partno); $i++) {
                        if ($request->pi_no == 1) {
                            $pi_no = $request->purchase_inv[$i];
                        } else {
                            $pi_no = 0;
                        }

                        if ($request->partno[$i] == 0) {
                            $partno = db::table('sm_items')->select('id')->where('part_number', $request->part_number[$i])->where('status', 1)->first();
                            if (isset($partno)) {
                                $partno = $partno->id;
                            } else {
                                $partno = 0;
                            }
                        } else {
                            $partno = $request->partno[$i];
                        }

                        $temp_data[] = [
                            'stl_id' => $stl->id,
                            'pi_no' => $pi_no,
                            'part_id' => $request->partno[$i],
                            'part_no' => $request->part_number[$i],
                            'description' => $request->description[$i],
                            'amount' => floatval(str_replace(',', '', $request->amount[$i])),
                            'status' => 1,
                            'pi_inv_no' => $request->pi_inv_no[$i],
                            'awbno' => $request->awbno[$i],
                            'boeno' => $request->boeno[$i],
                            //'bill_no' => $request->table_id_stl_billno_.''.$request->purchase_inv[$i],
                        ];
                    }
                    SysSTLItems::insert($temp_data);

                    DB::commit();
                    Toastr::success('STL Added Successful', 'Success');
                    return redirect('stl/' . $stl->id);


                } catch (\Exception $e) {
                    DB::rollback();
                    return $e;
                    Toastr::error('Operation Failed', 'Failed');
                    return redirect()->back();
                }

            }




            DB::beginTransaction();
            if (count(array_filter($request->account_id)) > 0 && count(array_filter($request->amount)) > 0) {
                $py = new SysPayment();
                if ($request->mode == 1) { //mode 1 cash, mode 2 bank
                    $py->doc_number = SysHelper::get_new_code('sys_payment', 'CP', 'doc_number');
                    ;
                } else {
                    $py->doc_number = SysHelper::get_new_code_err('sys_payment', 'BP', 'doc_number');
                    ;
                }
                $py->doc_date = Carbon::createFromFormat('d/m/Y', $request->doc_date)->format('Y-m-d');
                $py->mode = $request->mode;
                if ($request->mode == 1) {
                    $py->payment_mode = $request->payment_mode_cash;
                    $py->payment_through = 1;
                } else {
                    $py->payment_mode = $request->payment_mode_bank;
                    $py->payment_through = $request->payment_through;
                }



                // $py->no_days = $request->payment_days;
                $py->currency = $request->currency;
                // $py->payment_date = Carbon::createFromFormat('d/m/Y', $request->payment_date)->format('Y-m-d');
                // $py->narration = $request->narration;
                // $py->cheque_id = $request->cheque_id;
                $py->status = 1;
                $py->created_by = Auth::user()->id;
                $py->created_at = Carbon::now('+04:00');
                $py->company_id = session('logged_session_data.company_id');

                // if (!empty($request->deal_id)) {

                //     // deal is comma seperated and also ciontains spaces

                //     $deal_id = str_replace(' ', '', $request->deal_id);
                //     $deal_id = explode(',', $deal_id);





                //     foreach ($deal_id as $d_id) {
                //         $dl[] = SysHelper::get_dealid_from_code($d_id);
                //     }




                //     $py->deal_id = implode(',', $dl);

                // }

                // $py->deal_id = SysHelper::get_dealid_from_code($request->deal_id);
                $results = $py->save();
                $py->toArray();



                if ($request->mode == 1) { //mode 1 cash, mode 2 bank
                    $account_id = $request->payment_mode_cash;
                    $transaction_type = "cashpayment";
                } else {
                    $account_id = $request->payment_mode_bank;
                    $transaction_type = "bankpayment";
                }

                $status = 1;
                if ($request->payment_through == 3 && $request->mode == 2) {
                    $status = 3;
                }

                $array_sum_amount = array_sum(
                    array_map(function ($value) {
                        return (float) str_replace(',', '', $value);
                    }, $request->amount)
                );

                SysHelper::trn_chartof_accounts_transaction_with_main($account_id, $py->id, $py->doc_number, date('Y-m-d'), $transaction_type, '0.00', $array_sum_amount, $request->narration, $status, 0, "", 1, 1);
                //return $request->all();
                for ($i = 0; $i < count($request->account_id); $i++) {
                    $transaction_id = null;
                    if ($request->account_id[$i] != "" && $request->amount[$i] != "") {
                        $transaction_id = SysHelper::trn_chartof_accounts_transaction_with_main_return_ID($request->account_id[$i], $py->id, $py->doc_number, $py->payment_date, $transaction_type, str_replace(',', '', $request->amount[$i]), '0.00', $request->remarks[$i], $status, 0, "", 1, 0);
                    }

                    // Add cheque details only when the row has valid cheque-related data
                    if (intval($request->payment_through) === 3
                        && !empty($request->account_id[$i])
                        && !empty($request->amount[$i])
                        && !empty($request->cheque_number_grid[$i])
                    ) {
                        $cheque_details = new SysPaymentChequeDetail();
                        $cheque_details->payment_id = $py->id;
                        $cheque_details->transaction_id = $transaction_id;
                        $cheque_details->account_id = intval($request->account_id[$i]);
                        $cheque_details->amount = str_replace(',', '', $request->amount[$i]);
                        $cheque_details->no_of_days = $request->no_of_days_grid[$i] ?? null;
                        $cheque_details->cheque_number = $request->cheque_number_grid[$i] ?? null;
                        $cheque_details->chequebook_id = $request->chequebook_id_grid[$i] ?? null;
                        $cheque_details->cheque_date = SysHelper::normalizeToYmd($request->cheque_date_grid[$i] ?? null);
                        $cheque_details->status = $request->status_grid[$i] ?? null;
                        $cheque_details->deal_id = SysHelper::get_dealid_from_code($request->deal_id_grid[$i]);
                        $cheque_details->payment_date = SysHelper::normalizeToYmd($request->payment_date_grid[$i]);
                        $cheque_details->narration = $request->remarks[$i] ?? null;
                        $cheque_details->created_by = Auth::user()->id;
                        $cheque_details->created_at = Carbon::now('+04:00');
                        $cheque_details->company_id = session('logged_session_data.company_id');
                        $cheque_details->save();


                       



                        $isdone = DB::table('sys_payment_cheque')->where([
                            'bank_name' => $request->payment_mode_bank,
                            'cheque_number' => $request->cheque_number_grid[$i],
                            'cheque_date' => SysHelper::normalizeToYmd($request->cheque_date_grid[$i]),
                            'supplier_name' => $request->account_id[$i] ?? null,
                        ])->get();



                        $array_sum_amount = isset($array_sum_amount) ? $array_sum_amount : 0;
                        $chequeData = [
                            'doc_number' => SysHelper::get_new_code('sys_payment_cheque', 'CH', 'doc_number'),
                            'doc_date' => Carbon::now('+04:00'),
                            'bank_name' => $request->payment_mode_bank ?? null,
                            'cheque_number' => $request->cheque_number_grid[$i] ?? null,
                            'cheque_date' => $request->cheque_date_grid[$i] ? SysHelper::normalizeToYmd($request->cheque_date_grid[$i]) : null,
                            'supplier_name' => $request->account_id[$i] ?? null,
                            'other_supplier_name' => $request->other_supplier_name[$i] ?? null,
                            'amount' => str_replace(',', '', $request->amount[$i]) ?? 0,
                            'amount_words' => trim(SysHelper::convertAmountToWords(str_replace(',', '', $request->amount[$i]) ?? 0, 'Dirham', 'Fils')),
                            'deal_id' => SysHelper::get_dealid_from_code($request->deal_id_grid[$i]),
                            'reference' => $request->remarks[$i]?? null,
                            'status' => 1,
                            'created_by' => Auth::user()->id,
                            'created_at' => Carbon::now('+04:00'),
                            'company_id' => session('logged_session_data.company_id'),
                            'sys_payment_id' => $py->id,
                        ];

                        if ($isdone->count() > 0) {
                            $existingCheque = SysPaymentCheque::find($isdone[0]->id);
                            if ($existingCheque) {
                                $existingCheque->update($chequeData);
                            }
                        } else {
                            $createdCheque = SysPaymentCheque::create($chequeData);
                        }

                    
                    }


                }





                $temp = SysPaymentAdjustmentsTemp::where('process_id', $request->process_id)->get();
                if (count($temp) > 0) {
                    foreach ($temp as $te) {
                        $temp_data[] = [
                            'transaction_type' => $te->transaction_type,
                            'bi_cheque_amount' => $te->bi_cheque_amount,
                            'bi_amount_adjusted' => $te->bi_amount_adjusted,
                            'bi_balance_to_adjust' => $te->bi_balance_to_adjust,
                            'bi_extra_amount' => $te->bi_extra_amount,
                            'bi_currency' => $te->bi_currency,
                            'bi_doc_number' => $py->doc_number,
                            'bi_contains' => $te->bi_contains,
                            'bi_doc_no' => $te->bi_doc_no,
                            'bi_lpo_no' => $te->bi_lpo_no,
                            'bi_doc_date' => $te->bi_doc_date,
                            'bi_total' => $te->bi_total,
                            'bi_paid' => $te->bi_paid,
                            'bi_balance' => $te->bi_balance,
                            'bi_amount' => $te->bi_amount,
                            'bi_narration' => $te->bi_narration,
                            'account_id' => $te->account_id,
                            'deal_id' => $te->deal_id,
                            'deal_code' => $te->deal_code,
                            'bi_bill_number' => $te->bi_bill_number,
                            'status' => 1,
                            'created_by' => Auth::user()->id,
                            'created_at' => Carbon::now('+04:00'),
                            'company_id' => session('logged_session_data.company_id'),
                        ];
                    }
                    SysPaymentAdjustments::insert($temp_data);
                    SysReceiptAdjustmentsTemp::where('process_id', $request->process_id)->delete();
                    SysReceiptAdjustmentsTemp::where('created_by', Auth::user()->id)->delete();
                }






                DB::commit();
                Toastr::success('Operation successful', 'Success');
                if ($request->page_id == "cashbook") {
                    return redirect('cashbook');
                } elseif ($request->page_id == "bankbook") {
                    return redirect('bankbook');
                } else {

                    return redirect('payment/' . $py->id);
                }
            } else {
                Toastr::error('Operation Failed. please enter valid data', 'Failed');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function edit($id)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $accounts = SysHelper::get_supplier_list_all($company_id);
            $paymentmode_cash = SysHelper::get_cash_account('all');
            $paymentmode_bank = SysHelper::get_bank_account('all');
            $currency = SysCurrencySettings::select('id', 'code')->get();

            $editData = SysPayment::find($id);
            $editDataList = SysChartofAccountsTransaction::where('transaction_id', $id)->wherein('transaction_type', ['cashpayment', 'bankpayment'])->where('is_main_account', 0)->get();

            if ($editData->cheque_id != 0) {
                $cheque_detail = SysPaymentCheque::where('id', $editData->cheque_id)->first();
            } else {
                $cheque_detail = null;
            }

            $curr = SysHelper::cheque_print_currancy_code(session('logged_session_data.company_id'));
            $currency1 = $curr[0];
            $currency2 = $curr[1];
            $editDataAdjustments = SysPaymentAdjustments::where('bi_doc_number', $editData->doc_number)->where('status', 1)->get();

            $paymentmode_cash = SysHelper::get_cash_account();
            $paymentmode_bank = SysHelper::get_bank_account();

            $data1 = $paymentmode_cash->pluck('id');
            $data2 = $paymentmode_bank->pluck('id');
            $data3 = array_merge($data1->toArray(), $data2->toArray());
            $query = SysPayment::select('sys_payment.id', 'sys_payment.doc_number', 'sys_payment.mode', 'sys_payment.payment_mode', 'sys_payment.payment_through', 'a.account_name', 'c.debit_amount', 'c.credit_amount', 'sys_payment.doc_date', 'sys_payment.payment_date', 'sys_payment.cheque_date', 'sys_payment.cheque_number', 'u.full_name', 'sys_payment.narration', 'sys_payment.status', 'sys_payment.deal_id')->selectRaw('1 as type')
                ->leftjoin('sys_chartofaccounts_transaction as c', 'c.transaction_no', 'sys_payment.doc_number')
                ->leftjoin('sys_chartofaccounts as a', 'a.id', 'c.account_id')
                ->leftjoin('users as u', 'u.id', 'sys_payment.created_by');

            $doc = SysChartofAccountsTransaction::wherein('transaction_type', ['bankpayment', 'cashpayment'])->wherein('company_id', $company_id)->wherenotin('account_id', $data3)->pluck('transaction_no');
            $query2 = SysPayment::select('sys_payment.id', 'sys_payment.doc_number', 'sys_payment.mode', 'sys_payment.payment_mode', 'sys_payment.payment_through', 'sys_payment.doc_date', 'sys_payment.payment_date', 'sys_payment.cheque_date', 'sys_payment.cheque_number', 'u.full_name', 'sys_payment.narration', 'sys_payment.status', 'sys_payment.deal_id')->selectRaw('2 as type')
                ->leftjoin('users as u', 'u.id', 'sys_payment.created_by')
                ->wherenotin('doc_number', $doc)->wherein('sys_payment.company_id', $company_id);
            $query->wherenotin('c.account_id', $data3);
            $query->wherein('sys_payment.company_id', $company_id);
            $query->orderby('sys_payment.id', 'desc');
            $payment = $query->orderby('sys_payment.payment_date', 'desc')->orderby('sys_payment.payment_date', 'desc')->get();
            $payment2 = $query2->orderby('sys_payment.doc_number', 'desc')->orderby('sys_payment.payment_date', 'desc')->get();
            $payment = $payment->merge($payment2);
            $payment_id = $id;

            return compact('payment', 'payment_id', 'accounts', 'paymentmode_cash', 'paymentmode_bank', 'currency', 'editData', 'editDataList', 'cheque_detail', 'currency1', 'currency2', 'editDataAdjustments');
            // return view('backEnd.payment.paymentedit', compact('payment', 'payment_id', 'accounts', 'paymentmode_cash', 'paymentmode_bank', 'currency', 'editData', 'editDataList', 'cheque_detail', 'currency1', 'currency2', 'editDataAdjustments'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function view(Request $request, $id)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $accounts = SysHelper::get_supplier_list_all($company_id);
            $paymentmode_cash = SysHelper::get_cash_account('all');
            $paymentmode_bank = SysHelper::get_bank_account('all');
            $currency = SysCurrencySettings::select('id', 'code')->get();

            $editData = SysPayment::find($id);
            $editDataList = SysChartofAccountsTransaction::where('transaction_id', $id)->wherein('transaction_type', ['cashpayment', 'bankpayment'])->where('is_main_account', 0)->get();
            $editDataAdjustments = SysPaymentAdjustments::where('bi_doc_number', $editData->doc_number)->where('status', 1)->get();

            return view('backEnd.payment.paymentview', compact('accounts', 'paymentmode_cash', 'paymentmode_bank', 'currency', 'editData', 'editDataList', 'editDataAdjustments'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function download(Request $request, $id)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $payment = SysPayment::find($id);
            $paymentList = SysChartofAccountsTransaction::where('transaction_id', $id)->wherein('transaction_type', ['cashpayment', 'bankpayment'])->where('is_main_account', 0)->get();
            $company = SysCompany::find($payment->company_id);
            $print = date('d/m/Y h:i A', strtotime(Carbon::now('+04:00')));

            $data = [
                'company' => $company,
                'payment' => $payment,
                'payment_item' => $paymentList,
                'print' => $print,
            ];


            $pdf = PDF::loadView('backEnd.pdf_print.payment_pdf', $data);
            $pdf->setPaper('A4', 'portrait');
            return $pdf->download($payment->doc_number . ".pdf");
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function update(Request $request, $id)
    {
        try {
            if ($request->payment_through == 3 && $request->chequebook && $request->cheque_number) {
                if ($this->isChequeNumberAlreadyUsed($request->chequebook, $request->cheque_number, $id)) {
                    Toastr::error('This cheque number is already used. Please select another number.', 'Validation Failed');
                    return redirect()->back()->withInput();
                }
            }

            DB::beginTransaction();
            $py = SysPayment::find($id);
            $py->doc_date = SysHelper::normalizeToYmd($request->doc_date);
            $py->mode = $request->mode;
            if ($request->mode == 1) {
                if ($request->mode != $request->actual_mode) {
                    $doc = SysHelper::get_new_code('sys_payment', 'CP', 'doc_number');
                    $py->edit_note = $py->doc_number . " change to " . $doc;
                    $py->doc_number = $doc;
                    SysPaymentAdjustments::where('bi_doc_number', $py->doc_number)->delete();
                }
                $py->payment_mode = $request->payment_mode_cash;
                $py->payment_through = 1;
            } else {
                if ($request->mode != $request->actual_mode) {
                    $doc = SysHelper::get_new_code('sys_payment', 'BP', 'doc_number');
                    $py->edit_note = $py->doc_number . " change to " . $doc;
                    $py->doc_number = $doc;
                    SysPaymentAdjustments::where('bi_doc_number', $py->doc_number)->delete();
                }
                $py->payment_mode = $request->payment_mode_bank;
                $py->payment_through = $request->payment_through;
            }
            $py->payment_through = $request->payment_through;
            if ($request->filled('cheque_date')) {
                $py->cheque_date = SysHelper::normalizeToYmd($request->cheque_date);
            }
            $py->cheque_number = $request->cheque_number;
            $py->chequebook_id = $request->chequebook;
            $py->no_days = $request->payment_days;
            $py->currency = $request->currency;
            if ($py->payment_date != SysHelper::normalizeToYmd($request->payment_date)) {
                $py->pdc_removed_os = 1;
            }
            $py->payment_date = SysHelper::normalizeToYmd($request->payment_date);
            $py->narration = $request->narration;
            $py->status = 1;
            $py->updated_by = Auth::user()->id;
            $py->updated_at = Carbon::now('+04:00');
            //$py->company_id = session('logged_session_data.company_id');

            if ($request->filled('deal_id')) {
                // comma seperated deal ids in request
                $deal_ids = explode(',', $request->deal_id);

                // remove spaces from deal ids
                $deal_ids = array_map('trim', $deal_ids);

                // get deal ids from db for the payment
                foreach ($deal_ids as $deal_id) {
                    $db_deal_ids[] = SysHelper::get_dealid_from_code($deal_id);
                }


                // check if there is any change in deal ids
                $py->deal_id = implode(',', $db_deal_ids);
            }


            $results = $py->save();
            $py->toArray();
            if (intval($request->payment_through) === 3) {



                $isdone = DB::table('sys_payment_cheque')->where('id', $py->cheque_id)->get();


                $array_sum_amount = isset($array_sum_amount) ? $array_sum_amount : 0;
                $chequeData = [
                    'doc_number' => SysHelper::get_new_code('sys_payment_cheque', 'CH', 'doc_number'),
                    'doc_date' => Carbon::now('+04:00'),
                    'bank_name' => $request->payment_mode_bank ?? null,
                    'cheque_number' => $request->cheque_number ?? null,
                    'cheque_date' => $request->cheque_date ? SysHelper::normalizeToYmd($request->cheque_date) : null,
                    'supplier_name' => $request->account_id[0] ?? null,
                    'other_supplier_name' => $request->other_supplier_name ?? null,
                    'amount' => str_replace(',', '', $request->amount[0]) ?? 0,
                    'amount_words' => trim(SysHelper::convertAmountToWords(str_replace(',', '', $request->amount[0]) ?? 0, 'Dirham', 'Fils')),
                    'deal_id' => !empty($request->deal_id) ? SysHelper::get_dealid_from_code($request->deal_id) : null,
                    'reference' => $request->narration ?? null,
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                    'company_id' => session('logged_session_data.company_id'),
                    'cheque_id' => $request->chequebook,
                ];

                if ($isdone->count() > 0) {
                    $existingCheque = SysPaymentCheque::find($isdone[0]->id);
                    if ($existingCheque) {
                        $existingCheque->update($chequeData);
                        $py->cheque_id = $existingCheque->id;
                    }
                } else {
                    $createdCheque = SysPaymentCheque::create($chequeData);
                    $py->cheque_id = $createdCheque->id;
                }

                $py->save();
            }

            if ($request->mode == 1) { //mode 1 cash, mode 2 bank
                $account_id = $request->payment_mode_cash;
                $transaction_type = "cashpayment";
            } else {
                $account_id = $request->payment_mode_bank;
                $transaction_type = "bankpayment";
            }

            SysChartofAccountsTransaction::query()
                ->where('transaction_id', $id)->wherein('transaction_type', ['cashpayment', 'bankpayment'])
                ->each(function ($oldRecord) {
                    $newRecord = $oldRecord->replicate();
                    $newRecord->setTable('sys_chartofaccounts_transaction_history');
                    $newRecord->save();
                    $oldRecord->delete();
                });

            $status = 1;
            if ($request->payment_through == 3 && $request->mode == 2) {
                $status = 3;
            }
            $array_sum_amount = array_sum(
                array_map(function ($value) {
                    return (float) str_replace(',', '', $value);
                }, $request->amount)
            );
            SysHelper::trn_chartof_accounts_transaction_with_main($account_id, $py->id, $py->doc_number, $py->payment_date, $transaction_type, '0.00', $array_sum_amount, $request->narration, $status, 0, "", 1, 1);
            //return $request->all();
            for ($i = 0; $i < count($request->account_id); $i++) {
                if ($request->account_id[$i] != "" && $request->amount[$i] != "") {
                    SysHelper::trn_chartof_accounts_transaction_with_main($request->account_id[$i], $py->id, $py->doc_number, $py->payment_date, $transaction_type, str_replace(',', '', $request->amount[$i]), '0.00', $request->remarks[$i], $status, 0, "", 1, 0);
                }
            }

            DB::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect('payment/' . $id);
        } catch (\Exception $e) {
            DB::rollback();
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    // public function receiptadjustmentsstore(Request $request)
    // {
    //     try{
    //     $sra = new SysReceiptAdjustments();
    //     $sra->bi_new_reference = $request->bi_new_reference;
    //     $sra->bi_amount_to_adjust = $request->bi_amount_to_adjust;
    //     $sra->bi_adjusted_amount = $request->bi_adjusted_amount;
    //     $sra->bi_currency = $request->bi_currency;
    //     $sra->bi_doc_number = $request->bi_doc_number;
    //     $sra->bi_contains = $request->bi_contains;
    //     $sra->bi_doc_no = $request->bi_doc_no;
    //     $sra->bi_doc_date = date('Y-m-d', strtotime($request->bi_doc_date));
    //     $sra->bi_lpo_no = $request->bi_lpo_no;
    //     $sra->bi_due_date = date('Y-m-d', strtotime($request->bi_due_date));
    //     $sra->bi_total = $request->bi_total;
    //     $sra->bi_paid = $request->bi_amount;
    //     $sra->bi_balance = $request->bi_balance;
    //     $sra->bi_amount = $request->bi_amount;
    //     $sra->status = 1;
    //     $sra->created_by = Auth::user()->id;
    //     $results = $sra->save();
    //     $sra->toArray();

    //     $sle = new SysLedgerEntries();
    //     $sle->transaction_id = $request->bi_doc_number;
    //     $sle->transaction_type = "bankreceipt";
    //     $sle->account_id = $request->account_id;
    //     $sle->entry_date = date('Y-m-d', strtotime($request->entry_date));
    //     $sle->entry_type = 1; //Debit
    //     $sle->amount = $request->bi_amount;
    //     $sle->status = 1;
    //     $sle->created_by = Auth::user()->id;
    //     $sle->save();

    //     $ret = 'SUCCESS';
    //         return json_encode(array('data'=>$ret));
    //     }catch (\Exception $e) {
    //         $ret = $e;
    //         return json_encode(array('data'=>$ret));
    //     }
    // }

    public function getpybalancelist(Request $request)
    {
        $company_id = session('logged_session_data.company_id');
        $opb = SysChartofAccountsTransaction::wherein('transaction_type', ['openingbalance', 'opbinvoice'])->where('account_id', $request->account_id)->where('status', 1)->where('company_id', $company_id)->get();
        $items = DB::select("CALL get_bank_payment_adjestments($request->account_id,$company_id)");



        //$siv_charges = SysSalesInvoiceCFCharges::where('');

        // $items = SysSalesInvoice::select('sys_sales_invoice.doc_number', 'sys_sales_invoice.si_date', 'sys_sales_invoice.lpo_number','sys_sales_invoice.lpo_date', DB::raw('SUM(sys_sales_invoice_items.taxableamount) as amount'))
        // ->join('sys_sales_invoice_items', 'sys_sales_invoice.id', '=', 'sys_sales_invoice_items.si_id')
        // ->where('sys_sales_invoice.customer',$request->cr_account_id)
        // ->groupBy('sys_sales_invoice.id')
        // ->groupBy('sys_sales_invoice.doc_number')
        // ->groupBy('sys_sales_invoice.si_date')
        // ->groupBy('sys_sales_invoice.lpo_number')
        // ->groupBy('sys_sales_invoice.lpo_date')
        // ->get();

        //$items = SysCustSuppl::select('id','name')->where('catid',1)->get();



        $searchData = [];

        if (count($opb) > 0) {
            foreach ($opb as $dt) {
                $paid = SysPaymentAdjustments::where('bi_doc_no', $dt->transaction_no)->sum('bi_paid');
                $searchData[] = [
                    'deal_code' => null,
                    'deal_id' => null,
                    'doc_number' => $dt->transaction_no,
                    'doc_date' => $dt->transaction_date,
                    'lpo_number' => '',
                    'bill_number' => '',
                    'lpo_date' => '',
                    'total' => abs($dt->debit_amount - $dt->credit_amount),
                    'paid' => $paid,
                    'balance' => abs($dt->debit_amount - $dt->credit_amount) - $paid,
                ];
            }
        }

        foreach ($items as $item) {
            $searchData[] = [
                'deal_id' => $item->deal_id,
                'deal_code' => $item->deal_code,
                'doc_number' => $item->doc_number,
                'doc_date' => $item->doc_date,
                'lpo_number' => $item->lpo_number,
                'bill_number' => $item->bill_number,
                'lpo_date' => $item->lpo_date,
                'total' => $item->total,
                'paid' => $item->paid,
                'balance' => $item->balance,
            ];
        }

        // Always return JSON (empty array when no data) to avoid client-side parse errors
        return response()->json($searchData);
    }
    public function getpybalancelistedit(Request $request)
    {
        $company_id = session('logged_session_data.company_id');
        $opb = SysChartofAccountsTransaction::wherein('transaction_type', ['openingbalance', 'opbinvoice'])->where('account_id', $request->account_id)->where('status', 1)->where('company_id', $company_id)->get();
        $items = DB::select("CALL get_bank_payment_adjestments_edit($request->account_id,$company_id)");


        $searchData = [];

        $adjestData = SysPaymentAdjustments::where('bi_doc_number', $request->doc_number)->get();

        if (count($opb) > 0) {
            foreach ($opb as $dt) {
                $paid = SysPaymentAdjustments::where('bi_doc_no', $dt->transaction_no)->sum('bi_paid');
                $bi_amount = $adjestData->where('bi_doc_no', $dt->transaction_no)->sum('bi_paid');
                if ($bi_amount != 0) {
                    $paid = 0;
                }
                $searchData[] = [
                    'deal_code' => null,
                    'deal_id' => null,
                    'doc_number' => $dt->transaction_no,
                    'doc_date' => $dt->transaction_date,
                    'lpo_number' => '',
                    'bill_number' => '',
                    'lpo_date' => '',
                    'total' => abs($dt->debit_amount - $dt->credit_amount),
                    'paid' => $paid,
                    'bi_amount' => $bi_amount,
                    'balance' => abs($dt->debit_amount - $dt->credit_amount) - $paid,
                ];
            }
        }

        foreach ($items as $item) {
            $paid = $item->paid;
            $bi_amount = $adjestData->where('bi_doc_no', $item->doc_number)->sum('bi_paid');
            if ($bi_amount != 0) {
                $paid = 0;
            }
            $searchData[] = [
                'deal_id' => $item->deal_id,
                'deal_code' => $item->deal_code,
                'doc_number' => $item->doc_number,
                'doc_date' => $item->doc_date,
                'lpo_number' => $item->lpo_number,
                'bill_number' => $item->bill_number,
                'lpo_date' => $item->lpo_date,
                'total' => $item->total,
                'paid' => $item->paid,
                'bi_amount' => $bi_amount,
                'balance' => $item->balance,
            ];
        }

        // Always return JSON (empty array when no data) to avoid client-side parse errors
        return response()->json($searchData);
    }

    public function delete($id)
    {
        try {
            DB::table('sys_payment')->where('id', $id)->update(['status' => 2]);
            DB::table('sys_chartofaccounts_transaction')->where('transaction_id', $id)->wherein('transaction_type', ['bankpayment', 'cashpayment'])->update(['status' => 2]);

            $dt = DB::table('sys_payment')->where('id', $id)->first();
            //DB::table('sys_payment_adjustments')->where('bi_doc_number',$dt->doc_number)->update(['status' => 1]);
            DB::table('sys_payment_adjustments')->where('bi_doc_number', $dt->doc_number)->delete();

            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function restore($id)
    {
        try {
            DB::table('sys_payment')->where('id', $id)->update(['status' => 1]);
            DB::table('sys_chartofaccounts_transaction')->where('transaction_id', $id)->wherein('transaction_type', ['bankpayment', 'cashpayment'])->update(['status' => 1]);

            $dt = DB::table('sys_payment')->where('id', $id)->first();
            //DB::table('sys_payment_adjustments')->where('bi_doc_number',$dt->doc_number)->update(['status' => 1]);

            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function delete_payment_items(Request $request)
    {
        try {
            db::beginTransaction();
            SysChartofAccountsTransaction::where(['id' => $request->id])->delete();
            SysPaymentAdjustments::where(['account_id' => $request->account_id])->where('bi_doc_number', $request->transaction_no)->delete();

            $amount = SysChartofAccountsTransaction::where(['transaction_no' => $request->transaction_no])->sum('debit_amount');
            SysChartofAccountsTransaction::where(['transaction_no' => $request->transaction_no])
                ->where(['is_main_account' => 1])->where(['debit_amount' => '0.00'])->update(['credit_amount' => $amount]);

            db::commit();

            $ret = 'SUCCESS';
            return json_encode(array('data' => $ret));
        } catch (\Exception $e) {
            db::rollBack();
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }

    public function delete_adjustment($id)
    {
        try {
            DB::table('sys_payment_adjustments')->where('id', $id)->delete();

            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function delete_adjustment_json(Request $request)
    {
        try {
            DB::table('sys_payment_adjustments')->where('id', $request->id)->delete();
            $ret = SysPaymentAdjustments::where('bi_doc_number', $request->doc_number)->where('status', 1)->get();
            if (count($ret) > 0) {
                return json_encode(array('data' => $ret));
            } else {
                $ret = [];
                return json_encode(array('data' => $ret));
            }
        } catch (\Exception $e) {
            $ret = $e;
            return json_encode(array('data' => $ret));
        }
    }

    public function payment_cheque_list(Request $request)
    {
        try {
            $com_id = session('logged_session_data.company_id');
            $bank = SysChartofAccounts::select('id', 'account_name', 'account_code')->where('subgroup2', 6)->where('company_id', $com_id)->orderby('id', 'asc')->get();
            $supplier = SysChartofAccounts::select('id', 'account_name', 'account_code')->where('subgroup2', 19)->where('company_id', $com_id)->orderby('account_name', 'asc')->get();

            $selectedBankId = $request->input('bank_name');
            $selectedSupplierId = $request->input('supplier_id');
            $from_date = $request->input('from_date');
            $to_date = $request->input('to_date');

            $query = SysPaymentCheque::with('payment')->where('company_id', $com_id);

            if ($selectedBankId) {
                $query->where('bank_name', $selectedBankId);
            }

            if ($selectedSupplierId) {
                $query->where('supplier_name', $selectedSupplierId);
            }

            if ($from_date) {
                $query->whereDate('cheque_date', '>=', SysHelper::normalizeToYmd($from_date));
            }

            if ($to_date) {
                $query->whereDate('cheque_date', '<=', SysHelper::normalizeToYmd($to_date));
            }

            $data = $query->orderby('id', 'desc')->get();

            $curr = SysHelper::cheque_print_currancy_code($com_id);
            $currency1 = $curr[0];
            $currency2 = $curr[1];

            return view('backEnd.payment.payment_cheque_list', compact('data', 'bank', 'supplier', 'currency1', 'currency2', 'selectedBankId', 'selectedSupplierId', 'from_date', 'to_date'));
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function getChequebooksByBank($bankId)
    {
        try {
            $chequeBooks = Chequebook::where('bank_id', $bankId)->whereNull('deleted_at')->get();
            return response()->json(['success' => true, 'data' => $chequeBooks]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Returns all active chequebooks for the given bank along with their
     * already-used cheque numbers so the front-end can determine the next
     * available number without additional round-trips.
     */
    public function getChequebookDataByBank($bankId)
    {
        try {
            $bankId = (int) $bankId;
            if (!$bankId) {
                return response()->json(['success' => false, 'message' => 'Invalid bank ID'], 422);
            }

            $chequebooks = Chequebook::where('bank_id', $bankId)
                ->whereNull('deleted_at')
                ->orderBy('id', 'asc')
                ->get(['id', 'doc_number', 'start_no', 'end_no', 'no_of_cheques']);

            $result = $chequebooks->map(function ($cb) {
                $used = SysPayment::where('chequebook_id', $cb->id)
                    ->where('payment_through', 3)
                    ->where('status', 1)
                    ->whereNotNull('cheque_number')
                    ->pluck('cheque_number')
                    ->map(function ($v) {
                        return trim((string) $v);
                    })
                    ->unique()
                    ->values()
                    ->all();

                return [
                    'id' => $cb->id,
                    'doc_number' => $cb->doc_number,
                    'start_no' => (int) $cb->start_no,
                    'end_no' => (int) $cb->end_no,
                    'no_of_cheques' => (int) $cb->no_of_cheques,
                    'used_numbers' => $used,
                ];
            });

            return response()->json(['success' => true, 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getUsedChequeNumbersByChequebook($chequebookId)
    {
        try {
            $used = SysPayment::where('chequebook_id', $chequebookId)
                ->where('payment_through', 3)
                ->where('status', 1)
                ->whereNotNull('cheque_number')
                ->pluck('cheque_number')
                ->map(function ($value) {
                    return trim((string) $value);
                })
                ->unique()
                ->values();

            return response()->json(['success' => true, 'used' => $used]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    protected function isChequeNumberAlreadyUsed($chequebookId, $chequeNumber, $excludePaymentId = null)
    {
        if (!$chequebookId || !$chequeNumber) {
            return false;
        }

        $query = SysPayment::where('chequebook_id', $chequebookId)
            ->where('cheque_number', $chequeNumber)
            ->where('payment_through', 3)
            ->where('status', 1);

        if ($excludePaymentId) {
            $query->where('id', '!=', $excludePaymentId);
        }

        return $query->exists();
    }

    public function payment_cheque_store(Request $request)
    {
        try {

            db::beginTransaction();
            $attachment = "";
            if ($request->file('attachment') != "") {
                $file = $request->file('attachment');
                $attachment = md5(time()) . "." . $file->getclientoriginalextension();
                $file->move('public/uploads/payment_cheque/', $attachment);
                $attachment = $attachment;
            }

            $isdone = DB::table('sys_payment_cheque')->where([
                'bank_name' => $request->bank_name,
                'cheque_number' => $request->cheque_number,
                'cheque_date' => SysHelper::normalizeToYmd($request->cheque_date),
                'supplier_name' => $request->supplier_name,
            ])->get();
            if (count($isdone) == 0) {
                $id = DB::table('sys_payment_cheque')->insertGetId(
                    [
                        'doc_number' => SysHelper::get_new_code('sys_payment_cheque', 'CH', 'doc_number'),
                        'doc_date' => Carbon::now('+04:00'),
                        'bank_name' => $request->bank_name,
                        'cheque_number' => $request->cheque_number,
                        'cheque_date' => SysHelper::normalizeToYmd($request->cheque_date),
                        'supplier_name' => $request->supplier_name,
                        'other_supplier_name' => $request->other_supplier_name,
                        'amount' => str_replace(',', '', $request->amount),
                        'amount_words' => $request->amount_words,
                        'deal_id' => SysHelper::get_dealid_from_code($request->deal_id),
                        'attachment' => $attachment,
                        'reference' => $request->reference,
                        'status' => 1,
                        'created_by' => Auth::user()->id,
                        'created_at' => Carbon::now('+04:00'),
                        'company_id' => session('logged_session_data.company_id'),
                    ]
                );
            } else {
                DB::table('sys_payment_cheque')->where('id', $isdone[0]->id)->update(
                    [
                        'bank_name' => $request->bank_name,
                        'cheque_number' => $request->cheque_number,
                        'cheque_date' => SysHelper::normalizeToYmd($request->cheque_date),
                        'supplier_name' => $request->supplier_name,
                        'other_supplier_name' => $request->other_supplier_name,
                        'amount' => str_replace(',', '', $request->amount),
                        'amount_words' => $request->amount_words,
                        'deal_id' => SysHelper::get_dealid_from_code($request->deal_id),
                        'attachment' => $attachment,
                        'reference' => $request->reference,
                        'status' => 1,
                        'updated_by' => Auth::user()->id,
                        'updated_at' => Carbon::now('+04:00'),
                    ]
                );
            }

            db::commit();

            if ($request->submit_btn == "pr") {

                if (count($isdone) == 0) {
                    $pdata = SysPaymentCheque::where('id', $id)->first();
                } else {
                    $pdata = SysPaymentCheque::where('id', $isdone[0]->id)->first();
                }

                if ($pdata->supplier_name == 0) {
                    $supplier_name = $pdata->other_supplier_name;
                } else {
                    $supplier_name = $pdata->suppliername->account_name;
                }
                $company_top = '285px';
                $company_left = '425px';
                $date_top = '220px';
                $date_left = '836px';
                $amount_w_top = '316px';
                $amount_w_left = '326px';
                $amount_top = '355px';
                $amount_left = '834px';
                $font_size = '13px';
                $temp_data = DB::table('sys_payment_cheque_template')->where('bank_id', $pdata->bank_name)->orderby('id', 'desc')->first();
                if (isset($temp_data)) {
                    $company_top = $temp_data->company_top;
                    $company_left = $temp_data->company_left;
                    $date_top = $temp_data->date_top;
                    $date_left = $temp_data->date_left;
                    $amount_w_top = $temp_data->amount_w_top;
                    $amount_w_left = $temp_data->amount_w_left;
                    $amount_top = $temp_data->amount_top;
                    $amount_left = $temp_data->amount_left;
                    $font_size = $temp_data->font_size;
                }
                $data = [
                    'cheque_date' => $pdata->cheque_date,
                    'supplier_name' => $supplier_name,
                    'amount' => $pdata->amount,
                    'amount_words' => $pdata->amount_words,
                    'company_top' => $company_top,
                    'company_left' => $company_left,
                    'date_top' => $date_top,
                    'date_left' => $date_left,
                    'amount_w_top' => $amount_w_top,
                    'amount_w_left' => $amount_w_left,
                    'amount_top' => $amount_top,
                    'amount_left' => $amount_left,
                    'font_size' => $font_size,
                ];
                $pdf = PDF::loadView('backEnd.pdf_print.cheque_pdf', $data);
                $paper_size = array(0, 0, 750, 500);
                $pdf->setPaper($paper_size);
                //$pdf->setPaper('A4', 'portrait');
                //return redirect()->back()->$pdf->download('cheque-print.pdf');
                return $pdf->download("cheque-print.pdf");
            }

            if ($request->submit_btn == "jv") {
                return redirect('journalvoucheradd/' . $request->cheque_date);
            }
            if ($request->submit_btn == "py") {
                return redirect('payment-add-from-cheque/' . $id);
            }

            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            db::rollBack();
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function payment_cheque_update(Request $request)
    {
        try {
            db::beginTransaction();
            $attachment = $request->edit_attachment;
            if ($request->file('attachment') != "") {
                $file = $request->file('attachment');
                $attachment = md5(time()) . "." . $file->getclientoriginalextension();
                $file->move('public/uploads/payment_cheque/', $attachment);
                $attachment = $attachment;
            }

            DB::table('sys_payment_cheque')->where('id', $request->cid)->update(
                [
                    'bank_name' => $request->bank_name,
                    'cheque_number' => $request->cheque_number,
                    'cheque_date' => $request->cheque_date,
                    'supplier_name' => $request->supplier_name,
                    'other_supplier_name' => $request->other_supplier_name,
                    'amount' => str_replace(',', '', $request->amount),
                    'amount_words' => $request->amount_words,
                    'deal_id' => SysHelper::get_dealid_from_code($request->deal_id),
                    'attachment' => $attachment,
                    'reference' => $request->reference,
                    'status' => 1,
                    'updated_by' => Auth::user()->id,
                    'updated_at' => Carbon::now('+04:00'),
                ]
            );

            db::commit();

            if ($request->submit_btn == "pr") {
                $pdata = SysPaymentCheque::where('id', $request->cid)->first();
                if ($pdata->supplier_name == 0) {
                    $supplier_name = $pdata->other_supplier_name;
                } else {
                    $supplier_name = $pdata->suppliername->account_name;
                }
                $company_top = '285px';
                $company_left = '425px';
                $date_top = '220px';
                $date_left = '836px';
                $amount_w_top = '316px';
                $amount_w_left = '326px';
                $amount_top = '355px';
                $amount_left = '834px';
                $font_size = '13px';
                $temp_data = DB::table('sys_payment_cheque_template')->where('bank_id', $pdata->bank_name)->orderby('id', 'desc')->first();
                if (isset($temp_data)) {
                    $company_top = $temp_data->company_top;
                    $company_left = $temp_data->company_left;
                    $date_top = $temp_data->date_top;
                    $date_left = $temp_data->date_left;
                    $amount_w_top = $temp_data->amount_w_top;
                    $amount_w_left = $temp_data->amount_w_left;
                    $amount_top = $temp_data->amount_top;
                    $amount_left = $temp_data->amount_left;
                    $font_size = $temp_data->font_size;
                }
                $data = [
                    'cheque_date' => $pdata->cheque_date,
                    'supplier_name' => $supplier_name,
                    'amount' => $pdata->amount,
                    'amount_words' => $pdata->amount_words,
                    'company_top' => $company_top,
                    'company_left' => $company_left,
                    'date_top' => $date_top,
                    'date_left' => $date_left,
                    'amount_w_top' => $amount_w_top,
                    'amount_w_left' => $amount_w_left,
                    'amount_top' => $amount_top,
                    'amount_left' => $amount_left,
                    'font_size' => $font_size,
                ];
                $pdf = PDF::loadView('backEnd.pdf_print.cheque_pdf', $data);
                $paper_size = array(0, 0, 750, 500);
                $pdf->setPaper($paper_size);
                //$pdf->setPaper('A4', 'portrait');
                return $pdf->download("cheque-print.pdf");
            }
            if ($request->submit_btn == "jv") {
                return redirect('journalvoucheradd/' . $request->cheque_date);
            }
            if ($request->submit_btn == "py") {
                return redirect('payment-add-from-cheque/' . $request->cid);
            }

            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            db::rollBack();
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function payment_cheque_print($id)
    {
        try {

            $pdata = SysPaymentCheque::where('id', $id)->first();
            $company_top = '285px';
            $company_left = '425px';
            $date_top = '220px';
            $date_left = '836px';
            $amount_w_top = '316px';
            $amount_w_left = '326px';
            $amount_top = '355px';
            $amount_left = '834px';
            $font_size = '13px';
            $temp_data = DB::table('sys_payment_cheque_template')->where('bank_id', $pdata->bank_name)->orderby('id', 'desc')->first();
            if (isset($temp_data)) {
                $company_top = $temp_data->company_top;
                $company_left = $temp_data->company_left;
                $date_top = $temp_data->date_top;
                $date_left = $temp_data->date_left;
                $amount_w_top = $temp_data->amount_w_top;
                $amount_w_left = $temp_data->amount_w_left;
                $amount_top = $temp_data->amount_top;
                $amount_left = $temp_data->amount_left;
                $font_size = $temp_data->font_size;
            }

            if ($pdata->supplier_name == 0) {
                $supplier_name = $pdata->other_supplier_name;
            } else {
                $supplier_name = $pdata->suppliername->account_name;
            }
            $data = [
                'cheque_date' => $pdata->cheque_date,
                'supplier_name' => $supplier_name,
                'amount' => $pdata->amount,
                'amount_words' => $pdata->amount_words,
                'company_top' => $company_top,
                'company_left' => $company_left,
                'date_top' => $date_top,
                'date_left' => $date_left,
                'amount_w_top' => $amount_w_top,
                'amount_w_left' => $amount_w_left,
                'amount_top' => $amount_top,
                'amount_left' => $amount_left,
                'font_size' => $font_size,
            ];
            $pdf = PDF::loadView('backEnd.pdf_print.cheque_pdf', $data);
            $paper_size = array(0, 0, 750, 500);
            $pdf->setPaper($paper_size);
            //$pdf->setPaper('A4', 'portrait');
            return $pdf->download("cheque-print.pdf");
        } catch (\Throwable $th) {
            return $th;
        }
    }
    public function payment_cheque_print_template(Request $request)
    {
        try {
            $bankid = 0;
            $temp_data = null;
            $bank = SysChartofAccounts::select('id', 'account_name', 'account_code')->where('subgroup2', 6)->where('status', '!=', 2)->where('company_id', session('logged_session_data.company_id'))->orderby('id', 'asc')->get();
            if ($_POST) {
                $bankid = $request->bank_id;
                if ($request->btn_submit == "save") {
                    $check = DB::table('sys_payment_cheque_template')->where('bank_id', $request->bank_id)->orderby('id', 'desc')->first();
                    if (isset($check)) {
                        DB::table('sys_payment_cheque_template')->where('bank_id', $request->bank_id)->update([
                            'company_top' => $request->company_top,
                            'company_left' => $request->company_left,
                            'date_top' => $request->date_top,
                            'date_left' => $request->date_left,
                            'amount_w_top' => $request->amount_w_top,
                            'amount_w_left' => $request->amount_w_left,
                            'amount_top' => $request->amount_top,
                            'amount_left' => $request->amount_left,
                            'font_size' => $request->font_size,
                            'status' => $request->status,
                            'updated_by' => Auth::user()->id,
                            'updated_at' => Carbon::now('+04:00'),
                            'company_id' => session('logged_session_data.company_id'),
                        ]);
                    } else {
                        DB::table('sys_payment_cheque_template')->insert([
                            'bank_id' => $request->bank_id,
                            'company_top' => $request->company_top,
                            'company_left' => $request->company_left,
                            'date_top' => $request->date_top,
                            'date_left' => $request->date_left,
                            'amount_w_top' => $request->amount_w_top,
                            'amount_w_left' => $request->amount_w_left,
                            'amount_top' => $request->amount_top,
                            'amount_left' => $request->amount_left,
                            'font_size' => $request->font_size,
                            'status' => $request->status,
                            'created_by' => Auth::user()->id,
                            'created_at' => Carbon::now('+04:00'),
                            'company_id' => session('logged_session_data.company_id'),
                        ]);
                    }
                    Toastr::success('Updated Successfully', 'Success');
                }
            }
            $company_top = '285px';
            $company_left = '425px';
            $date_top = '220px';
            $date_left = '836px';
            $amount_w_top = '316px';
            $amount_w_left = '326px';
            $amount_top = '355px';
            $amount_left = '834px';
            $font_size = '13px';
            if ($_POST) {
                if (isset($request->bank_id))
                    $temp_data = DB::table('sys_payment_cheque_template')->where('bank_id', $request->bank_id)->orderby('id', 'desc')->first();
            } else {
                if (count($bank) > 0)
                    $temp_data = DB::table('sys_payment_cheque_template')->where('bank_id', $bank[0]->id)->orderby('id', 'desc')->first();
            }
            if (isset($temp_data)) {
                $company_top = $temp_data->company_top;
                $company_left = $temp_data->company_left;
                $date_top = $temp_data->date_top;
                $date_left = $temp_data->date_left;
                $amount_w_top = $temp_data->amount_w_top;
                $amount_w_left = $temp_data->amount_w_left;
                $amount_top = $temp_data->amount_top;
                $amount_left = $temp_data->amount_left;
                $font_size = $temp_data->font_size;
            }
            $company = SysCompany::select('company_name')->where('id', session('logged_session_data.company_id'))->first();
            $company = $company->company_name;
            $cheque_date = "15/01/2025";
            $cheque_amount_w = "Sixty Thousand Eight Hundred Sixty Thousand Eight Hundred Only";
            $cheque_amount = "60,800.00";



            return view('backEnd.payment.payment_cheque_template', compact('temp_data', 'company', 'cheque_date', 'cheque_amount', 'cheque_amount_w', 'company_top', 'company_left', 'date_top', 'date_left', 'amount_w_top', 'amount_w_left', 'amount_top', 'amount_left', 'font_size', 'bank', 'bankid'));
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function payment_cheque_delete($id)
    {
        try {
            db::beginTransaction();
            DB::table('sys_payment_cheque')->where('id', $id)->update(
                [
                    'status' => 0,
                    'updated_by' => Auth::user()->id,
                    'updated_at' => Carbon::now('+04:00'),
                ]
            );
            db::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            db::rollBack();
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function payment_cheque_restore($id)
    {
        try {
            db::beginTransaction();
            DB::table('sys_payment_cheque')->where('id', $id)->update(
                [
                    'status' => 1,
                    'updated_by' => Auth::user()->id,
                    'updated_at' => Carbon::now('+04:00'),
                ]
            );
            db::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            db::rollBack();
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    // public function deleteSupplier(Request $request,$id){

    //     try{
    //         $result = SmSupplier::destroy($id);

    //         if (ApiBaseMethod::checkUrl($request->fullUrl())) {
    //             if ($result) {
    //                 return ApiBaseMethod::sendResponse(null, 'Supplier has been deleted successfully');
    //             } else {
    //                 return ApiBaseMethod::sendError('Something went wrong, please try again.');
    //             }
    //         } else {
    //             if ($result) {
    //                 Toastr::success('Operation successful', 'Success');
    //                 return redirect('suppliers');
    //             } else {
    //                 Toastr::error('Operation Failed', 'Failed');
    //                 return redirect()->back();
    //             }
    //         }
    //     }catch (\Exception $e) {
    //        Toastr::error('Operation Failed', 'Failed');
    //        return redirect()->back(); 
    //     }
    // }



    public function search(Request $request)
    {
        $q = $request->get('query');
        $formattedDate = null;
        if (preg_match('/\d{2}[\/\-]\d{2}[\/\-]\d{4}/', $q)) {
            $normalized = str_replace('/', '-', $q);
            $formattedDate = date('Y-m-d', strtotime($normalized));
        }

        $r = SysHelper::get_data_by_role();
        $company_id = $r[0];

        $paymentmode_cash = SysHelper::get_cash_account();
        $paymentmode_bank = SysHelper::get_bank_account();

        $data1 = $paymentmode_cash->pluck('id');
        $data2 = $paymentmode_bank->pluck('id');
        $data3 = array_merge($data1->toArray(), $data2->toArray());


        $query = SysPayment::with(['account:id,account_name,account_code', 'deal_code:id,code', 'currency_name:id,code'])->select('sys_payment.id', 'sys_payment.doc_number', 'sys_payment.mode', 'sys_payment.payment_mode', 'sys_payment.payment_through', 'a.account_name', 'c.debit_amount', 'c.credit_amount', 'sys_payment.doc_date', 'sys_payment.payment_date', 'sys_payment.cheque_date', 'sys_payment.cheque_number', 'u.full_name', 'sys_payment.narration', 'sys_payment.status', 'sys_payment.deal_id')->selectRaw('1 as type')
            ->leftjoin('sys_chartofaccounts_transaction as c', 'c.transaction_no', 'sys_payment.doc_number')
            ->leftjoin('sys_chartofaccounts as a', 'a.id', 'c.account_id')
            ->leftjoin('users as u', 'u.id', 'sys_payment.created_by');

        $doc = SysChartofAccountsTransaction::wherein('transaction_type', ['bankpayment', 'cashpayment'])->wherein('company_id', $company_id)->wherenotin('account_id', $data3)->pluck('transaction_no');

        $query2 = SysPayment::with(['account:id,account_name,account_code', 'deal_code:id,code', 'currency_name:id,code'])->select('sys_payment.id', 'sys_payment.doc_number', 'sys_payment.mode', 'sys_payment.payment_mode', 'sys_payment.payment_through', 'sys_payment.doc_date', 'sys_payment.payment_date', 'sys_payment.cheque_date', 'sys_payment.cheque_number', 'u.full_name', 'sys_payment.narration', 'sys_payment.status', 'sys_payment.deal_id')->selectRaw('2 as type')
            ->leftjoin('users as u', 'u.id', 'sys_payment.created_by')
            ->wherenotin('doc_number', $doc)->wherein('sys_payment.company_id', $company_id);

        $query->where(function ($query) use ($q, $formattedDate) {
            $query->where(function ($qsub) use ($q) {
                $dealId = SysHelper::get_dealid_from_code($q);


                if ($q) {
                    $qsub->where('doc_number', 'like', "%{$q}%")

                        ->orWhere('full_name', 'like', "%{$q}%")
                        ->orWhereHas('deal_code', function ($q1) use ($q) {
                            $q1->where('code', 'like', "%{$q}%");
                        })
                        ->orWhereHas('account', function ($q1) use ($q) {
                            $q1->where('account_name', 'like', "%{$q}%");
                        });
                }

                if (!empty($dealId) && $dealId != "0") {
                    $qsub->orWhere('deal_id', 'like', "%{$dealId}%");
                }
            });

            if ($formattedDate) {
                // Combine inside same group
                $query->orWhere(function ($q2) use ($formattedDate) {
                    $q2->whereDate('doc_date', $formattedDate)
                        ->orWhereDate('payment_date', $formattedDate);
                });
            }
        });
        $query->wherenotin('c.account_id', $data3);
        $query->wherein('sys_payment.company_id', $company_id);
        $query->orderby('sys_payment.id', 'desc');
        $amc_list = $query->orderby('sys_payment.payment_date', 'desc')->orderby('sys_payment.payment_date', 'desc')->get();



        $query2->where(function ($query) use ($q, $formattedDate) {
            $query->where(function ($qsub) use ($q) {
                $dealId = SysHelper::get_dealid_from_code($q);


                if ($q) {
                    $qsub->where('doc_number', 'like', "%{$q}%")

                        ->orWhere('full_name', 'like', "%{$q}%")
                        ->orWhereHas('deal_code', function ($q1) use ($q) {
                            $q1->where('code', 'like', "%{$q}%");
                        })
                        ->orWhereHas('account', function ($q1) use ($q) {
                            $q1->where('account_name', 'like', "%{$q}%");
                        });
                }

                if (!empty($dealId) && $dealId != "0") {
                    $qsub->orWhere('deal_id', 'like', "%{$dealId}%");
                }
            });

            if ($formattedDate) {
                // Combine inside same group
                $query->orWhere(function ($q2) use ($formattedDate) {
                    $q2->whereDate('doc_date', $formattedDate)
                        ->orWhereDate('payment_date', $formattedDate);
                });
            }
        });


        $payment2 = $query2->orderby('sys_payment.doc_number', 'desc')->orderby('sys_payment.payment_date', 'desc')->get();
        $amc_list = $amc_list->merge($payment2);




        // 🔹 Map additional formatted fields
        $amc_list = $amc_list->map(function ($item) {
            // Compute amount if not present
            $calculatedAmount = abs(($item->debit_amount ?? 0) - ($item->credit_amount ?? 0));

            // Format using helper
            $item->formatted_amount = \App\SysHelper::com_curr_format($calculatedAmount, 2, '.', ',');



            return $item;
        });


        return response()->json($amc_list);
    }

    public function storePaymentsFromPurchaseOrder(Request $request)
    {


        //delete already existing payment cart for this deal track and supplier
        DealTrackPoPaymentCart::where('supplier_id', $request->supplier_id)
            ->where('deal_track_id', $request->dealtrack_id)
            ->where('deal_id', $request->po_sup_deal_id)
            ->delete();

        foreach ($request->payment_value as $poId => $amount) {

            // remove commas & convert to decimal
            $payment = str_replace(',', '', $amount);

            $payment_cart = new DealTrackPoPaymentCart();

            $payment_cart->supplier_id = $request->supplier_id;
            $payment_cart->deal_track_id = $request->dealtrack_id;
            $payment_cart->deal_id = $request->po_sup_deal_id;
            $payment_cart->po_id = $poId;
            $payment_cart->payment = $payment;
            $payment_cart->company_id = session('logged_session_data.company_id');
            $payment_cart->created_by = Auth::user()->id;
            $payment_cart->save();
        }

        return redirect('payment?pr_action=dealtrack&dealtrack_id=' . $request->dealtrack_id . '&deal_id=' . $request->po_sup_deal_id . '&supplier_id=' . $request->supplier_id);

    }

    public function storePaymentDeal(Request $request)
    {




        try {
            DB::beginTransaction();
            if (count(array_filter($request->account_id)) > 0 && count(array_filter($request->amount)) > 0) {
                $py = new SysPayment();
                if ($request->mode == 1) { //mode 1 cash, mode 2 bank
                    $py->doc_number = SysHelper::get_new_code('sys_payment', 'CP', 'doc_number');

                } else {
                    $py->doc_number = SysHelper::get_new_code_err('sys_payment', 'BP', 'doc_number');

                }
                $py->doc_date = Carbon::createFromFormat('d/m/Y', $request->doc_date)->format('Y-m-d');
                $py->mode = $request->mode;
                if ($request->mode == 1) {
                    $py->payment_mode = $request->payment_mode_cash;
                    $py->payment_through = 1;
                } else {
                    $py->payment_mode = $request->payment_mode_bank;
                    $py->payment_through = $request->payment_through;
                }
                $py->cheque_date = Carbon::createFromFormat('d/m/Y', $request->cheque_date)->format('Y-m-d');
                $py->cheque_number = $request->cheque_number;
                $py->currency = $request->currency;
                $py->payment_date = Carbon::createFromFormat('d/m/Y', $request->payment_date)->format('Y-m-d');
                $py->narration = $request->narration;
                $py->no_days = $request->payment_days;
                $py->cheque_id = $request->cheque_id;
                $py->status = 1;
                $py->created_by = Auth::user()->id;
                $py->created_at = Carbon::now('+04:00');
                $py->company_id = session('logged_session_data.company_id');
                if (!empty($request->deal_code)) {
                    // deal is comma seperated and also ciontains spaces

                    $deal_id = str_replace(' ', '', $request->deal_code);
                    $deal_id = explode(',', $deal_id);



                    foreach ($deal_id as $d_id) {
                        $dl[] = SysHelper::get_dealid_from_code($d_id);
                    }


                    $py->deal_id = implode(',', $dl);

                }
                // $py->deal_id = $request->deal_id;
                $results = $py->save();
                $py->toArray();

                if ($request->mode == 1) { //mode 1 cash, mode 2 bank
                    $account_id = $request->payment_mode_cash;
                    $transaction_type = "cashpayment";
                } else {
                    $account_id = $request->payment_mode_bank;
                    $transaction_type = "bankpayment";
                }

                $status = 1;
                if ($request->payment_through == 3 && $request->mode == 2) {
                    $status = 3;
                }

                $array_sum_amount = array_sum(
                    array_map(function ($value) {
                        return (float) str_replace(',', '', $value);
                    }, $request->amount)
                );
                SysHelper::trn_chartof_accounts_transaction_with_main($account_id, $py->id, $py->doc_number, $py->payment_date, $transaction_type, '0.00', $array_sum_amount, $request->narration, $status, 0, "", 1, 1);
                //return $request->all();
                for ($i = 0; $i < count($request->account_id); $i++) {
                    if ($request->account_id[$i] != "" && $request->amount[$i] != "") {
                        SysHelper::trn_chartof_accounts_transaction_with_main($request->account_id[$i], $py->id, $py->doc_number, $py->payment_date, $transaction_type, str_replace(',', '', $request->amount[$i]), '0.00', $request->remarks[$i], $status, 0, "", 1, 0);
                    }
                }

                $temp = SysPaymentAdjustmentsTemp::where('process_id', $request->process_id)->get();
                if (count($temp) > 0) {
                    foreach ($temp as $te) {
                        $temp_data[] = [
                            'transaction_type' => $te->transaction_type,
                            'bi_cheque_amount' => $te->bi_cheque_amount,
                            'bi_amount_adjusted' => $te->bi_amount_adjusted,
                            'bi_balance_to_adjust' => $te->bi_balance_to_adjust,
                            'bi_extra_amount' => $te->bi_extra_amount,
                            'bi_currency' => $te->bi_currency,
                            'bi_doc_number' => $py->doc_number,
                            'bi_contains' => $te->bi_contains,
                            'bi_doc_no' => $te->bi_doc_no,
                            'bi_lpo_no' => $te->bi_lpo_no,
                            'bi_doc_date' => $te->bi_doc_date,
                            'bi_total' => $te->bi_total,
                            'bi_paid' => $te->bi_paid,
                            'bi_balance' => $te->bi_balance,
                            'bi_amount' => $te->bi_amount,
                            'bi_narration' => $te->bi_narration,
                            'account_id' => $te->account_id,
                            'deal_id' => $te->deal_id,
                            'deal_code' => $te->deal_code,
                            'bi_bill_number' => $te->bi_bill_number,
                            'status' => 1,
                            'created_by' => Auth::user()->id,
                            'created_at' => Carbon::now('+04:00'),
                            'company_id' => session('logged_session_data.company_id'),
                        ];
                    }
                    SysPaymentAdjustments::insert($temp_data);
                    SysReceiptAdjustmentsTemp::where('process_id', $request->process_id)->delete();
                    SysReceiptAdjustmentsTemp::where('created_by', Auth::user()->id)->delete();
                }

                DB::commit();


                $cart_pos = DealTrackPoPaymentCart::where('supplier_id', $request->supplier_id)
                    ->where('deal_track_id', $request->dealtrack_id)
                    ->where('deal_id', $request->deal_id)
                    ->get();



                // foreach ($cart_pos as $cart) {
                //     $purchase_order = SysPurchaseOrder::where('id', $cart->po_id)->first();
                //     if ($purchase_order) {
                //         $purchase_order->payment_id = $py->id;
                //         $purchase_order->save();
                //     }
                // }

                foreach ($cart_pos as $cart) {

                    $purchase_order = SysPurchaseOrder::find($cart->po_id);

                    if (!$purchase_order) {
                        continue;
                    }

                    $newPaymentId = (string) $py->id;

                    if (!empty($purchase_order->payment_id)) {

                        // Convert existing IDs to array
                        $existingIds = explode(',', $purchase_order->payment_id);

                        // Trim spaces
                        $existingIds = array_map('trim', $existingIds);

                        // Add only if not already present
                        if (!in_array($newPaymentId, $existingIds)) {
                            $existingIds[] = $newPaymentId;
                        }

                        // Save back as comma-separated string
                        $purchase_order->payment_id = implode(',', $existingIds);

                    } else {
                        // No existing value
                        $purchase_order->payment_id = $newPaymentId;
                    }

                    $purchase_order->save();
                }


                //delete the payment cart after processing
                DealTrackPoPaymentCart::where('supplier_id', $request->supplier_id)
                    ->where('deal_track_id', $request->dealtrack_id)
                    ->where('deal_id', $request->deal_id)
                    ->delete();

                Toastr::success('Operation successful', 'Success');
                return redirect('crm-deal-track-approval-list/' . $request->dealtrack_id);
            } else {
                Toastr::error('Operation Failed. please enter valid data', 'Failed');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    public function getDetailsPDF($id)
    {
        $data = $this->get_payment_pdf_data($id);

        if (!empty($data) && is_array($data)) {
            return view('backEnd.payment.payment-pdf-view', $data);
        } else {
            return response("Error loading details!", 404);
        }
    }




}

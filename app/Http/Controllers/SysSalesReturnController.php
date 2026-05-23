<?php

namespace App\Http\Controllers;

use App\SysChartofAccounts;
use App\SysAccountType;
use App\ApiBaseMethod;
use App\SmItem;
use App\SmStaff;
use App\SysCashReceipt;
use App\SysCashReceiptList;
use App\SysCustSuppl;
use App\SysAccountGroup;
use App\SysChartofAccountsTransaction;
use App\SysCompany;
use App\SysCountries;
use App\SysCrmDeals;
use App\SysCurrency;
use App\SysCurrencyRate;
use App\SysCurrencySettings;
use App\SysCustomerType;
use App\SysCustSupplAddressbook;
use App\SysDeliveryNote;
use App\SysDeliveryNoteItems;
use App\SysHelper;
use App\SysItemStock;
use App\SysPaymentTerms;
use App\SysProformaInvoiceItems;
use App\SysPurchaseDlnLicenseKey;
use App\SysPurchaseGrnLicenseKey;
use App\SysReceiptAdjustments;
use App\SysSalesInvoice;
use App\SysSalesInvoiceItems;
use App\SysSalesReturn;
use App\SysSalesReturnList;
use App\SysReceiptMode;
use App\SysSalesReturnAdjestment;
use App\SysSalesReturnListCart;
use App\SysSaleType;
use App\SysStates;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade as PDF;

class SysSalesReturnController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
    }

    /**
     * Finalize staged sales return license keys for this session/cart.
     * Keys are staged as sales_return_id = -1 and must be bound to the saved SR id.
     */
    private function finalizeSalesReturnLicenseKeys($sr)
    {
        $cartId = session('logged_session_data.cart_id');
        $companyId = session('logged_session_data.company_id');

        $keyItems = SysPurchaseGrnLicenseKey::where('sales_return_id', -1)
            ->where('cart_id', $cartId)
            ->where('company_id', $companyId)
            ->get();

        if ($keyItems->isEmpty()) {
            return;
        }

        foreach ($keyItems as $k) {
            SysHelper::set_license_key_trn(3, $sr->id, $sr->doc_date, $sr->doc_number, $k->id, $k->item_id, $k->license_key, $k->exp_date);
        }

        SysPurchaseGrnLicenseKey::whereIn('id', $keyItems->pluck('id')->all())
            ->update([
                'status' => 1,
                'sales_return_id' => $sr->id,
                'cart_id' => '',
                'updated_by' => Auth::user()->id,
                'updated_at' => Carbon::now('+04:00'),
            ]);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function salesreturnList(Request $request, $id = null)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $customer_list = SysHelper::get_customer_list($company_id);

            $ctrl_doc_number = "";
            $ctrl_customer = "";
            $ctrl_supplier = "";
            $ctrl_deal_number = "";
            $ctrl_sales_invoice_number = "";
            $ctrl_dln_number = "";
            $ctrl_amount = "";
            $ctrl_date = "";

            $query = SysSalesReturn::select(DB::raw('sys_sales_return.*, (SELECT sum(debit_amount) FROM sys_chartofaccounts_transaction WHERE transaction_type="salesreturn" and transaction_no=sys_sales_return.doc_number) AS amount, (SELECT GROUP_CONCAT(code) FROM sys_crm_deals WHERE id=sys_sales_return.deal_id) AS code'));
            if (SysHelper::get_pagination_post($request)) {
                if ($request->documents_number != "") {
                    $query->where('doc_number', 'like', '%' . $request->documents_number . '%');
                    $ctrl_doc_number = $request->documents_number;
                }
                if ($request->customer != "") {
                    $query->where('customer', $request->customer);
                    $ctrl_customer = $request->customer;
                }
                if ($request->supplier != "") {
                    $query->where('supplier_name', 'like', '%' . $request->supplier . '%');
                    $ctrl_supplier = $request->supplier;
                }
                if ($request->deal_number != "") {
                    $query->where('deal_id', 'like', '%' . SysHelper::get_dealid_from_code($request->deal_number) . '%');
                    $ctrl_deal_number = $request->deal_number;
                }
                if ($request->sales_invoice_number != "") {
                    $query->where('si_doc_number', 'like', '%' . $request->sales_invoice_number . '%');
                    $ctrl_sales_invoice_number = $request->sales_invoice_number;
                }
                if ($request->dln_number != "") {
                    $query->where('dn_doc_number', 'like', '%' . $request->dln_number . '%');
                    $ctrl_dln_number = $request->dln_number;
                }
                if ($request->amount != "") {
                    $amt_nos = SysChartofAccountsTransaction::where('transaction_type', 'salesreturn')->where('debit_amount', $request->amount)->pluck('transaction_no');
                    $query->wherein('doc_number', $amt_nos);
                    $ctrl_amount = $request->amount;
                }
                if ($request->date != "") {
                    $query->where('doc_date', SysHelper::normalizeToYmd($request->date));
                    $ctrl_date = $request->date;
                }
            } else {

            }

            $query->wherein('company_id', $company_id);
            //$query->wherein('created_by',$r[1]);
            $query->orderby('doc_number', 'desc');
            $salesreturn = $query->paginate(50);


            $active_id = $id;
            $data = [];
            $action = false;
            $editData = [];
            $addData = [];


            if ($request->has('sr_action')) {
                $poAction = $request->input('sr_action');

                if ($poAction === 'add') {
                    $action = 'add';
                    $addData = $this->salesreturnAdd(); // Get all data for adding
                } elseif ($poAction === 'edit') {
                    $action = 'edit';
                    $editData = $this->edit($active_id); // Get all data for editing
                }
            } else {


                if ($id != null) {
                    $data = $this->get_sr_pdf_data($id);
                } else {

                    $firstRecord = $salesreturn->first();
                    if ($firstRecord) {
                        $active_id = $firstRecord->id;
                        $data = $this->get_sr_pdf_data($active_id);
                    }
                }
            }


            // if($sr_id!=null){
            //     $id = $sr_id;
            // } else {
            //     $id = $salesreturn->first()->id;
            // }

            // $data = $this->get_sr_pdf_data($id);
            //return $salesreturn;
            return view('backEnd.salesreturn.salesreturnlist', compact('salesreturn', 'customer_list', 'data', 'id', 'active_id', 'action', 'editData', 'addData', 'ctrl_doc_number', 'ctrl_customer', 'ctrl_supplier', 'ctrl_deal_number', 'ctrl_sales_invoice_number', 'ctrl_dln_number', 'ctrl_amount', 'ctrl_date'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function search(Request $request)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $query = $request->get('query');

            $invoices = SysSalesReturn::select(
                'sys_sales_return.id',
                'sys_sales_return.doc_number',
                'sys_sales_return.doc_date',
                DB::raw('(SELECT sum(debit_amount) FROM sys_chartofaccounts_transaction WHERE transaction_type="salesreturn" and transaction_no=sys_sales_return.doc_number) AS amount'),
                'sys_chartofaccounts.account_code',
                'sys_chartofaccounts.account_name',
                'sys_currency.code as currency_code'
            )
                ->join('sys_chartofaccounts', 'sys_chartofaccounts.id', '=', 'sys_sales_return.customer')
                ->join('sys_currency', 'sys_currency.id', '=', 'sys_sales_return.currency')
                ->whereIn('sys_sales_return.company_id', $company_id)
                ->where('sys_sales_return.doc_number', 'LIKE', "%{$query}%")
                ->orderBy('sys_sales_return.doc_number', 'desc')
                ->limit(20)
                ->get();
            return response()->json($invoices);
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function getDetails($id)
    {
        $data = $this->get_sr_pdf_data($id);
        if (count($data) > 0) {
            return view('backEnd.salesreturn.sr_details', $data);
        } else {
            return "error!!";
        }

    }

    public function get_sr_pdf_data($id)
    {
        try {
            $sr = SysSalesReturn::find($id);
            if (!empty($sr)) {
                $company = SysCompany::find($sr->company_id);
                $sr_item = SysSalesReturnList::where('sr_id', '=', $sr->id)->orderBy('sort_id')->get();
                $chartAccount = SysChartofAccounts::find($sr->customer);
                $account_code = $chartAccount ? $chartAccount->account_code : null;

                $sup_email = SysCustSuppl::select('sys_cust_suppl.*')
                    ->join('sys_chartofaccounts', 'sys_chartofaccounts.account_code', 'sys_cust_suppl.code')
                    ->where('sys_chartofaccounts.id', $sr->customer)
                    ->first();

                $trn_no = $sup_email ? $sup_email->vat_number : null;

                if (!empty($sup_email)) {
                    $add = SysCustSupplAddressbook::where('cust_suppl_id', $sup_email->id)->first();
                }

                if (!empty($add)) {
                    $address = $add->address;
                    $address2 = $add->address2;
                    $city = $add->city;
                    $state = $add->statename->name;
                    $country = $add->countryname->name;
                    $email = $sup_email->email;
                    $tel = $sup_email->contcat_number;
                    $mob = $sup_email->mobile;
                } else {
                    $address = "";
                    $address2 = "";
                    $city = "";
                    $state = "";
                    $country = "";
                    $email = "";
                    $tel = "";
                    $mob = "";
                }
                $srn_adjestment_main = SysSalesInvoice::select(
                    'sys_sales_invoice.doc_number',
                    'sys_sales_invoice.doc_date',
                    'sys_sales_invoice.lpo_number',
                    'cat.debit_amount as total_amount',
                    DB::raw('(
                        SELECT IFNULL(SUM(paid_amount), 0)
                        FROM sys_sales_return_adjestment
                        WHERE siv_no = sys_sales_invoice.doc_number
                    ) + (
                        SELECT IFNULL(SUM(bi_paid), 0)
                        FROM sys_receipt_adjustments
                        WHERE bi_doc_no = sys_sales_invoice.doc_number
                    ) AS total_paid_amount'),
                    'dn.doc_number as dn_doc_number',
                    DB::raw("COALESCE((SELECT narration FROM sys_sales_return_adjestment WHERE siv_no = sys_sales_invoice.doc_number LIMIT 1), '') as narration")
                )
                    ->join('sys_chartofaccounts_transaction as cat', 'cat.transaction_no', 'sys_sales_invoice.doc_number')
                    ->leftJoin('sys_delivery_note as dn', 'dn.id', 'sys_sales_invoice.dn_id')
                    ->where('cat.account_id', $sr->customer)
                    ->where('sys_sales_invoice.customer', $sr->customer)
                    ->where('cat.company_id', session('logged_session_data.company_id'))
                    ->groupBy(
                        'sys_sales_invoice.doc_number',
                        'sys_sales_invoice.doc_date',
                        'cat.debit_amount',
                        'dn.doc_number',
                        'sys_sales_invoice.lpo_number'
                    )
                    ->get();

                $opb = SysChartofAccountsTransaction::whereIn('transaction_type', ['opbinvoice'])
                    ->where('status', 1)
                    ->where('account_id', $sr->customer)
                    ->where('company_id', $company->id)
                    ->get();

                $srn_adjestment = [];
                foreach ($opb as $dt) {
                    $paid = SysReceiptAdjustments::where('bi_doc_no', $dt->transaction_no)->sum('bi_paid');
                    $srn_adjestment[] = [
                        'doc_number' => $dt->transaction_no,
                        'doc_date' => $dt->transaction_date,
                        'lpo_number' => '',
                        'total_amount' => abs($dt->debit_amount - $dt->credit_amount),
                        'total_paid_amount' => $paid,
                        'dn_doc_number' => '',
                        'narration' => '',
                    ];
                }

                $srn_adjestment = collect($srn_adjestment)->map(function ($item) {
                    return (object) $item;
                });
                $srn_adjestment = $srn_adjestment->merge($srn_adjestment_main);
                $srn_adjestment = $srn_adjestment->sortByDesc('doc_date')->values()->all();

                $srn_adj_amount = 0;
                if (count($sr_item) > 0) {
                    $srn_adj_amount = $sr_item->sum('taxableamount') + $sr_item->sum('vatamount');
                }

                $data = [
                    'sr' => $sr,
                    'company' => $company,
                    'sr_item' => $sr_item,
                    'email' => $email,
                    'tel' => $tel,
                    'address' => $address,
                    'address2' => $address2,
                    'city' => $city,
                    'state' => $state,
                    'account_code' => $account_code,
                    'trn_no' => $trn_no,
                    'country' => $country,
                    'mobile' => $mob,
                    'srn_adjestment' => $srn_adjestment,
                    'srn_adj_amount' => $srn_adj_amount,
                ];
                return $data;
            }
            return [];
        } catch (\Throwable $th) {

            return [];
        }
    }

    public function salesreturnAdd()
    {
        try {
            $currency = SysCurrencySettings::select('id', 'code')->get();
            $company = SysCompany::find(session('logged_session_data.company_id'));
            $paymentterms = SysPaymentTerms::select('id', 'title')->orderby('title', 'asc')->get();

            $staff = SysHelper::get_sales_persons();

            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $customer = SysHelper::get_customer_list($company_id);
            $customs_freight_account = SysHelper::get_customs_freight_accounts_for_sales($company_id);
            $supplier = SysHelper::get_supplier_list($company_id);
            $company = SysCompany::find(session('logged_session_data.company_id'));

            $countries = SysCountries::orderby('name', 'asc')->get();
            $states = SysStates::orderby('name', 'asc')->get();
            $items = SysHelper::get_product_list($company_id);
            $customertype = SysCustomerType::orderby('title', 'asc')->get();
            $saletype = SysSaleType::orderby('title', 'asc')->get();

            $cart = SysSalesReturnListCart::select('sys_sales_return_list_cart.*', 'sm_items.part_number AS partno', 'sm_items.description')
                ->join('sm_items', 'sm_items.id', 'sys_sales_return_list_cart.part_number')
                ->where('cart_id', session('logged_session_data.cart_id'))->get();

            $query = SysSalesReturn::select(DB::raw('sys_sales_return.*, (SELECT sum(debit_amount) FROM sys_chartofaccounts_transaction WHERE transaction_type="salesreturn" and transaction_no=sys_sales_return.doc_number) AS amount, (SELECT GROUP_CONCAT(code) FROM sys_crm_deals WHERE id=sys_sales_return.deal_id) AS code'));
            $query->wherein('company_id', $company_id);
            //$query->wherein('created_by',$r[1]);
            $query->orderby('doc_number', 'desc');
            $salesreturn = $query->paginate(50);


            //return compact('currency', 'customer', 'customs_freight_account', 'items', 'paymentterms','company','customertype','saletype','staff','countries','states','supplier','cart','company','salesreturn');
            return view('backEnd.salesreturn.salesreturnadd', compact('currency', 'customer', 'customs_freight_account', 'items', 'paymentterms', 'company', 'customertype', 'saletype', 'staff', 'countries', 'states', 'supplier', 'cart', 'company', 'salesreturn'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function adjestment_list_add(Request $request)
    {
        try {
            // $srn_adjestment = SysSalesInvoice::select('sys_sales_invoice.doc_number','sys_sales_invoice.doc_date','sys_sales_invoice.lpo_number','cat.debit_amount as total_amount',DB::raw('sum(adj.paid_amount) as paid_amount'),'dn.doc_number as dn_doc_number',DB::raw("COALESCE(MAX(adj.narration), '') as narration"))
            // ->join('sys_chartofaccounts_transaction as cat','cat.transaction_no','sys_sales_invoice.doc_number')
            // ->leftjoin('sys_sales_return_adjestment as adj','adj.siv_no','sys_sales_invoice.doc_number')
            // ->leftjoin('sys_delivery_note as dn','dn.id','sys_sales_invoice.dn_id')
            // ->where('customer',$request->id)
            // ->where('account_id',$request->id)
            // ->groupby('sys_sales_invoice.doc_number','sys_sales_invoice.doc_date','cat.debit_amount','dn.doc_number','sys_sales_invoice.lpo_number')
            // ->orderby('sys_sales_invoice.doc_number','asc')
            // ->get();


            $srn_adjestment_main = SysSalesInvoice::select(
                'sys_sales_invoice.doc_number',
                'sys_sales_invoice.doc_date',
                'sys_sales_invoice.lpo_number',
                'cat.debit_amount as total_amount',
                DB::raw('
            (SELECT IFNULL(SUM(paid_amount), 0) FROM sys_sales_return_adjestment WHERE siv_no = sys_sales_invoice.doc_number) +
            (SELECT IFNULL(SUM(bi_paid), 0) FROM sys_receipt_adjustments WHERE bi_doc_no = sys_sales_invoice.doc_number) 
            AS total_paid_amount'),
                'dn.doc_number as dn_doc_number',
                DB::raw("COALESCE((SELECT narration FROM sys_sales_return_adjestment WHERE siv_no = sys_sales_invoice.doc_number LIMIT 1), '') as narration")
            )
                ->join('sys_chartofaccounts_transaction as cat', 'cat.transaction_no', 'sys_sales_invoice.doc_number')
                ->leftJoin('sys_delivery_note as dn', 'dn.id', 'sys_sales_invoice.dn_id')
                ->where('cat.account_id', $request->id)
                ->where('sys_sales_invoice.customer', $request->id)
                ->where('cat.company_id', session('logged_session_data.company_id'))
                ->groupBy(
                    'sys_sales_invoice.doc_number',
                    'sys_sales_invoice.doc_date',
                    'cat.debit_amount',
                    'dn.doc_number',
                    'sys_sales_invoice.lpo_number'
                )
                ->get();

            $opb = SysChartofAccountsTransaction::wherein('transaction_type', ['opbinvoice', ''])->where('status', 1)->where('account_id', $request->id)->where('company_id', session('logged_session_data.company_id'))->get();
            $srn_adjestment = [];

            if (count($opb) > 0) {
                foreach ($opb as $dt) {
                    $paid = SysReceiptAdjustments::where('bi_doc_no', $dt->transaction_no)->sum('bi_paid');
                    //if(abs($dt->debit_amount - $dt->credit_amount)-$paid>0){
                    $srn_adjestment[] = [
                        'doc_number' => $dt->transaction_no,
                        'doc_date' => $dt->transaction_date,
                        'lpo_number' => '',
                        'total_amount' => abs($dt->debit_amount - $dt->credit_amount),
                        'total_paid_amount' => $paid,
                        'dn_doc_number' => '',
                        'narration' => '',
                    ];
                    //}
                }
            }


            $srn_adjestment = collect($srn_adjestment)->map(function ($item) {
                return (object) $item;
            });
            $srn_adjestment = $srn_adjestment->merge($srn_adjestment_main);
            $srn_adjestment = $srn_adjestment->sortByDesc('doc_date');
            $srn_adjestment = $srn_adjestment->values()->all(); // Optional: make it a plain array


            //$docno = SysDeliveryNote::select('doc_number')->where('id',$request->id)->first();
            //$ret = SysSalesReturnAdjestment::select('srn_no','dln_no','siv_no','doc_date','total_amount','paid_amount','balance_amount')->where('dln_no',$docno->doc_number)->get();
            return json_encode(array('data' => $srn_adjestment));

        } catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }

    public function salesreturnadd_adjestment(Request $request)
    {
        try {
            if (count($request->adj_paid) > 0) {
                for ($i = 0; $i < count($request->adj_paid); $i++) {
                    SysSalesReturnAdjestment::where(['srn_no' => $request->adj_srn_no])->where('status', 5)->delete(); //, 'siv_no' => $request->adj_siv_no[$i]
                }
                for ($i = 0; $i < count($request->adj_paid); $i++) {
                    if ($request->adj_paid[$i] != "" && $request->adj_paid[$i] != 0) {
                        $data = [
                            'srn_no' => $request->adj_srn_no,
                            'dln_no' => $request->adj_dn_doc_number,
                            'siv_no' => $request->adj_siv_no[$i],
                            'lpo_number' => $request->lpo_number[$i],
                            'doc_date' => date('Y-m-d', strtotime($request->edit_adj_doc_date)),
                            'total_amount' => str_replace(',', '', $request->adj_total[$i]),
                            'paid_amount' => str_replace(',', '', $request->adj_paid[$i]),
                            'balance_amount' => max((float) str_replace(',', '', $request->adj_balance[$i]), 0),
                            'narration' => $request->narration[$i],
                            'status' => 1,
                            'created_by' => Auth::user()->id,
                        ];
                        SysSalesReturnAdjestment::insert($data);
                    }
                }
            }
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function salesreturnadd_adjestment3(Request $request)
    {
        try {
            if (count($request->adj_paid) > 0) {
                for ($i = 0; $i < count($request->adj_paid); $i++) {
                    SysSalesReturnAdjestment::where(['srn_no' => $request->adj_srn_no])->delete(); //, 'siv_no' => $request->adj_siv_no[$i]
                }

                $adj_doc_date = $request->adj_doc_date;
                $adj_siv_no = $request->adj_siv_no;
                $adj_lpo_number = $request->adj_lpo_number;
                $adj_total = $request->adj_total;
                $adj_paid = $request->adj_paid;
                $adj_balance = $request->adj_balance;
                $adj_narration = $request->adj_narration;

                for ($i = 0; $i < count($request->adj_paid); $i++) {
                    if ($adj_paid[$i] != "" && $adj_paid[$i] != 0) {
                        $data = [
                            'srn_no' => $request->adj_srn_no,
                            'dln_no' => $request->dn_doc_number,
                            'siv_no' => $adj_siv_no[$i],
                            'lpo_number' => $adj_lpo_number[$i],
                            'doc_date' => date('Y-m-d', strtotime($request->doc_date)),
                            'total_amount' => str_replace(',', '', $adj_total[$i]),
                            'paid_amount' => str_replace(',', '', $adj_paid[$i]),
                            'balance_amount' => max((float) str_replace(',', '', $adj_balance[$i]), 0),
                            'narration' => $adj_narration[$i],
                            'status' => 5,
                            'created_by' => Auth::user()->id,
                        ];
                        SysSalesReturnAdjestment::insert($data);
                    }
                }
            }

            $ret = SysSalesInvoice::select('sys_sales_invoice.doc_number', 'sys_sales_invoice.doc_date', 'sys_sales_invoice.lpo_number', 'cat.debit_amount as total_amount', DB::raw('sum(adj.paid_amount) as paid_amount'), 'dn.doc_number as dn_doc_number', DB::raw("COALESCE(MAX(adj.narration), '') as narration"))
                ->join('sys_chartofaccounts_transaction as cat', 'cat.transaction_no', 'sys_sales_invoice.doc_number')
                ->leftjoin('sys_sales_return_adjestment as adj', 'adj.siv_no', 'sys_sales_invoice.doc_number')
                ->leftjoin('sys_delivery_note as dn', 'dn.id', 'sys_sales_invoice.dn_id')
                ->where('customer', $request->id)
                ->where('account_id', $request->id)
                ->groupby('sys_sales_invoice.doc_number', 'sys_sales_invoice.doc_date', 'cat.debit_amount', 'dn.doc_number', 'sys_sales_invoice.lpo_number')
                ->get();

            return json_encode(array('data' => $ret));

        } catch (\Exception $e) {
            return $e;
            $ret = "ERROR";
            return json_encode(array('data' => $ret));
        }
    }

    public function adjestment_list(Request $request)
    {
        try {
            $srn_adjestment = SysSalesInvoice::select('sys_sales_invoice.doc_number', 'sys_sales_invoice.doc_date', 'cat.debit_amount as total_amount', DB::raw('sum(adj.paid_amount) as paid_amount'), 'dn.doc_number as dn_doc_number', 'adj.status as adj_status')
                ->join('sys_chartofaccounts_transaction as cat', 'cat.transaction_no', 'sys_sales_invoice.doc_number')
                ->leftjoin('sys_sales_return_adjestment as adj', 'adj.siv_no', 'sys_sales_invoice.doc_number')
                ->leftjoin('sys_delivery_note as dn', 'dn.id', 'sys_sales_invoice.dn_id')
                ->where('customer', $request->id)
                ->where('account_id', $request->id)
                ->groupby('sys_sales_invoice.doc_number', 'sys_sales_invoice.doc_date', 'cat.debit_amount', 'dn.doc_number', 'adj.status')
                ->orderby('sys_sales_invoice.doc_number', 'asc')
                ->get();

            //$docno = SysDeliveryNote::select('doc_number')->where('id',$request->id)->first();
            //$ret = SysSalesReturnAdjestment::select('srn_no','dln_no','siv_no','doc_date','total_amount','paid_amount','balance_amount')->where('dln_no',$docno->doc_number)->get();
            return json_encode(array('data' => $srn_adjestment));

        } catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }
    public function salesreturnadd_adjestment2(Request $request)
    {
        try {
            if (count($request->adj_paid) > 0) {
                for ($i = 0; $i < count($request->adj_paid); $i++) {
                    if ($request->adj_paid[$i] != "" && $request->adj_paid[$i] != 0) {
                        $data = [
                            'srn_no' => $request->adj_srn_no,
                            'siv_no' => $request->adj_siv_no[$i],
                            'doc_date' => $request->adj_doc_date[$i],
                            'total_amount' => str_replace(',', '', $request->adj_total[$i]),
                            'paid_amount' => str_replace(',', '', $request->adj_paid[$i]),
                            'balance_amount' => max((float) str_replace(',', '', $request->adj_balance[$i]), 0),
                            'status' => 1,
                            'created_by' => Auth::user()->id,
                        ];
                        $check = SysSalesReturnAdjestment::where($data)->count();
                        if ($check == 0) {
                            SysSalesReturnAdjestment::insert($data);
                        }
                    }
                }
            }
            Toastr::error('Adjestment added Successfully', 'Success');
            return redirect()->back();
            //$ret = SysSalesReturnAdjestment::select('srn_no','dln_no','siv_no','doc_date','total_amount','paid_amount','balance_amount')->where('dln_no',$request->dln_no)->get();
            //return json_encode(array('data'=>$ret));

        } catch (\Exception $e) {
            return $e;
            Toastr::error('Error', 'Failed');
            return redirect()->back();
            //$ret = 'ERROR';   
            //return json_encode(array('data'=>$ret));
        }
    }

    public function get_dn_list(Request $request)
    {
        try {
            $ret = SysDeliveryNote::select('id', 'doc_number')->where('customer_id', $request->id)->where('return_status', 0)->where('company_id', session('logged_session_data.company_id'))->get();
            return json_encode(array('data' => $ret));

        } catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }
    public function get_dn_list_for_si_return(Request $request)
    {
        try {

            $ret = SysDeliveryNoteItems::select('sm_items.part_number as partnumber', 'sm_items.description', 'sys_delivery_note_items.*', 'sys_delivery_note.currency', 'sys_delivery_note.doc_number', 'sys_delivery_note.doc_date', 'sys_delivery_note.supplier_name', 'sys_delivery_note.deal_id', 'sys_delivery_note.lpo_no', 'sys_delivery_note.lpo_date', 'sys_delivery_note.paymentterms', 'sys_delivery_note.invoice_no', 'sys_delivery_note.invoice_date')
                ->join('sys_delivery_note', 'sys_delivery_note.id', 'sys_delivery_note_items.dn_id')
                ->join('sm_items', 'sm_items.id', 'sys_delivery_note_items.part_number')
                ->where('dn_id', $request->id)->get();

            /*$ret = SysDeliveryNoteItems::select('sm_items.part_number as partnumber','sm_items.description','sys_delivery_note_items.*','sys_delivery_note.payment_terms','sys_delivery_note.sales_man','sys_delivery_note.currency','sys_delivery_note.delivery_terms','sys_delivery_note.shipping_name','sys_delivery_note.shipping_address','sys_delivery_note.customer_country','sys_delivery_note.customer_state','sys_delivery_note.customer_type','sys_delivery_note.sale_type','sys_delivery_note.end_user_name','sys_delivery_note.contact_person_name','sys_delivery_note.contact_person_email','sys_delivery_note.contact_person_no','sys_delivery_note.doc_number','sys_delivery_note.doc_date','sys_delivery_note.lpo_number','sys_delivery_note.lpo_date','sys_delivery_note.printed_invoice_number','sys_delivery_note.supplier_name','sys_delivery_note.deal_id')
            ->join('sys_delivery_note','sys_delivery_note.id','sys_delivery_note_items.si_id')
            ->join('sm_items','sm_items.id','sys_delivery_note_items.part_number')
            ->where('si_id',$request->id)->get();*/

            return response()->json([$ret]);

        } catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }

    public function get_si_list(Request $request)
    {
        try {
            $ret = SysSalesInvoice::select('id', 'doc_number')->where('customer', $request->id)->where('return_status', 0)->where('status', 1)->where('company_id', session('logged_session_data.company_id'))->get();
            return json_encode(array('data' => $ret));

        } catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }
    public function get_si_list_for_si_return(Request $request)
    {
        try {
            // $ret = SysSalesInvoiceItems::select('sm_items.part_number as partnumber','sm_items.description','sys_sales_invoice_items.*','sys_sales_invoice.currency','sys_sales_invoice.doc_number','sys_sales_invoice.doc_date','sys_sales_invoice.supplier_name','sys_sales_invoice.deal_id','sys_sales_invoice.lpo_no','sys_sales_invoice.lpo_date','sys_sales_invoice.paymentterms','sys_sales_invoice.invoice_no','sys_sales_invoice.invoice_date')
            // ->join('sys_sales_invoice','sys_sales_invoice.id','sys_sales_invoice_items.dn_id')
            // ->join('sm_items','sm_items.id','sys_sales_invoice_items.part_number')

            $ret = SysSalesInvoiceItems::select(
                'sm_items.part_number as partnumber',
                'sm_items.product_type',
                'sm_items.description',
                'sys_sales_invoice_items.*',
                'sys_sales_invoice.currency',
                'sys_sales_invoice.doc_number',
                'sys_sales_invoice.doc_date',
                'sys_sales_invoice.supplier_name',
                'sys_sales_invoice.deal_id',
                'sys_sales_invoice.lpo_number as lpo_no',
                'sys_sales_invoice.lpo_date',
                'sys_sales_invoice.payment_terms as paymentterms',
                'sys_delivery_note.doc_number as dn_doc_number',
                'sys_delivery_note.doc_date as dn_doc_date',
                'sys_sales_invoice.printed_invoice_number',
                'sys_crm_deals.code as deal_code',
                'sys_sales_invoice.end_user_name',
                'sys_sales_invoice.contact_person_name',
                'sys_sales_invoice.contact_person_email',
                'sys_sales_invoice.contact_person_no',
                'sys_sales_invoice.ref_supplier_id',
                'sys_sales_invoice.sales_man'


            )
                ->join('sys_sales_invoice', 'sys_sales_invoice.id', 'sys_sales_invoice_items.si_id')
                ->join('sm_items', 'sm_items.id', 'sys_sales_invoice_items.part_number')
                ->leftjoin('sys_delivery_note', 'sys_delivery_note.invoice_no', 'sys_sales_invoice.doc_number')
                ->leftjoin('sys_crm_deals', 'sys_crm_deals.id', 'sys_sales_invoice.deal_id')
                ->where('si_id', $request->id)->where('sys_sales_invoice.status', 1)->get();

            return response()->json([$ret]);

        } catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }

    public function store(Request $request)
    {

        //return $request->all();
        if ($request->customer == "") {
            Toastr::error('Customer not found', 'Failed');
            return redirect()->back();
        }
        // if($request->currency ==""){Toastr::error('Currency not found', 'Failed'); return redirect()->back();}
        // if($request->sales_man ==""){Toastr::error('Sales Man not found', 'Failed'); return redirect()->back();}
        // if($request->delivery_terms ==""){Toastr::error('Delivery Terms not found', 'Failed'); return redirect()->back();}
        // if($request->payment_terms ==""){Toastr::error('Payment Terms not found', 'Failed'); return redirect()->back();}

        $cart = SysSalesReturnListCart::select('sys_sales_return_list_cart.*', 'sm_items.part_number AS partno', 'sm_items.description')
            ->join('sm_items', 'sm_items.id', 'sys_sales_return_list_cart.part_number')
            ->where('cart_id', session('logged_session_data.cart_id'))->get();

        if ($request->part_number != "") {


        } elseif (count($cart) > 0) {

        } else {
            Toastr::error('Items not found', 'Failed');
            return redirect()->back();
        }

        try {
            DB::beginTransaction();


            //if((isset($request->part_number) && (count(array_filter($request->part_number))>0 && count(array_filter($request->qty))>0 && count(array_filter($request->unitprice))>0)) || count($cart)>0){
            if ($request->part_number != "" || count($cart) > 0) {

                $doc_file = "";
                if ($request->file('doc') != "") {
                    $file = $request->file('doc');
                    $doc_file = md5(time()) . "." . $file->getClientOriginalExtension();
                    $file->move('public/uploads/sales_return_doc/', $doc_file);
                    $doc_file = $doc_file;
                }

                $sr = new SysSalesReturn();
                $sr->doc_number = SysHelper::get_new_code('sys_sales_return', 'SR', 'doc_number');
                $sr->doc_date = Carbon::createFromFormat('d/m/Y', $request->doc_date)->format('Y-m-d');
                $sr->dn_doc_number = $request->dn_doc_number;
                $sr->dn_doc_date = Carbon::createFromFormat('d/m/Y', $request->dn_doc_date)->format('Y-m-d');
                $sr->si_doc_number = $request->si_doc_number;
                $sr->si_doc_date = Carbon::createFromFormat('d/m/Y', $request->si_doc_date)->format('Y-m-d');
                $sr->customer = $request->customer;
                $sr->currency = $request->currency;
                $sr->printed_invoice_number = $request->printed_invoice_number;
                $sr->lpo_number = $request->reference_no;
                $sr->lpo_date = Carbon::createFromFormat('d/m/Y', $request->reference_date)->format('Y-m-d');
                $sr->payment_terms = $request->payment_terms;
                $sr->payment_terms2 = $request->payment_terms2;
                $sr->delivery_terms = $request->delivery_terms;
                $sr->sales_man = $request->sales_man;
                $sr->narration = $request->narration;

                $sr->ref_supplier_id = $request->ref_supplier_id ? implode(',', $request->ref_supplier_id) : null;


                // $sr->shipping_name= $request->shipping_name;
                // $sr->shipping_address= $request->shipping_address;

                $sr->shipping_address = $request->shipping_address_1;
                $sr->shipping_name = $request->shipping_name;
                $sr->shipping_supplier = $request->shipping_supplier;
                $sr->shipping_contact_no = $request->shipping_contact_no;
                $sr->shipping_email = $request->shipping_email;



                $sr->customer_type = $request->customer_type;
                $sr->sale_type = $request->sale_type;
                $sr->customer_country = $request->customer_country;
                $sr->customer_state = $request->customer_state;
                $sr->end_user_name = $request->end_user_name;
                $sr->contact_person_name = $request->contact_person_name;
                $sr->contact_person_email = $request->contact_person_email;
                $sr->contact_person_no = $request->contact_person_no;

                $sr->credit_note = $request->credit_note;

                $sr->net_vat = $request->net_vat ?: 0;
                $sr->deal_id = SysHelper::get_dealid_from_code($request->deal_id);

                $sr->narration = $request->narration;
                $sr->supplier_name = $request->supplier_name;
                $sr->company_id = session('logged_session_data.company_id');
                $sr->created_by = Auth::user()->id;
                $sr->attachment = $doc_file;
                $sr->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
                $sr->status = 1;
                $sr->save();
                $sr->toArray();


                if ($request->part_number != "") {
                    if ((count(array_filter($request->part_number)) > 0 && count(array_filter($request->qty)) > 0 && count(array_filter($request->unitprice)) > 0)) {

                        $total_tax_amount = array_sum(array_map(function ($value) {
                            return (float) str_replace(',', '', $value);
                        }, $request->taxableamount));

                        $total_vat_amount = array_sum(array_map(function ($value) {
                            return (float) str_replace(',', '', $value);
                        }, $request->vatamount));

                        $total_amount = $total_tax_amount + $total_vat_amount;
                    }
                }


                if (count($cart) > 0) {
                    $total_tax_amount = $cart->sum(function ($item) {
                        return (float) str_replace(',', '', $item->taxableamount);
                    });

                    $total_vat_amount = $cart->sum(function ($item) {
                        return (float) str_replace(',', '', $item->vatamount);
                    });

                    $total_amount = $total_tax_amount + $total_vat_amount;
                }


                //customer account cr
                SysHelper::trn_chartof_accounts_transaction($request->customer, $sr->id, $sr->doc_number, $sr->doc_date, 'salesreturn', '0.00', $total_amount, '', 1, 0, "", 1);

                //sales account dr
                $sales_return_account_id = SysHelper::get_sales_return_account_id();
                SysHelper::trn_chartof_accounts_transaction($sales_return_account_id, $sr->id, $sr->doc_number, $sr->doc_date, 'salesreturn', $total_tax_amount, '0.00', '', 1, 0, "", 1);

                //vat on sales account dr
                $sales_vat_account_id = SysHelper::get_sales_vat_account_id();
                SysHelper::trn_chartof_accounts_transaction($sales_vat_account_id, $sr->id, $sr->doc_number, $sr->doc_date, 'salesreturn', $total_vat_amount, '0.00', '', 1, 0, "", 1);

                if ($request->part_number != "") {
                    if (count($request->part_number) > 0) {
                        for ($i = 0; $i < count($request->part_number); $i++) {
                            if ($request->part_number[$i] != "" && $request->qty[$i] != "" && $request->unitprice[$i] != "") {
                                $sii = new SysSalesReturnList();
                                $sii->sr_id = $sr->id;
                                $sii->part_number = $request->part_number[$i];
                                $sii->serial_no = $request->serial_no[$i];
                                $sii->tax = $request->tax[$i] ?: 0;
                                $sii->qty = $request->qty[$i];
                                $sii->description = $request->description[$i] ?? '';
                                $sii->unitprice = (float) str_replace(',', '', $request->unitprice[$i] ?? 0);
                                $value = (float) str_replace(',', '', $request->value[$i] ?? 0);
                                $sii->value = $value;
                                $sii->discount = (float) str_replace(',', '', $request->discount[$i] ?? 0);
                                $sii->taxableamount = (float) str_replace(',', '', $request->taxableamount[$i] ?? 0);
                                $sii->vatamount = (float) str_replace(',', '', $request->vatamount[$i] ?? 0);
                                $sii->status = 1;
                                $sii->sort_id = $request->sort_id[$i];
                                $sii->created_by = Auth::user()->id;
                                $sii->save();

                                $str_arr = explode(",", $request->serial_no[$i]);
                                /*$str_arr = collect(preg_split('/[\s,]+/', $request->srl[$i], -1, PREG_SPLIT_NO_EMPTY))
                                ->map(fn($s) => strtoupper(trim($s)))->unique()->values()->toArray();*/
                                foreach ($str_arr as $srl) {
                                    $values = array('sr_id' => $sr->id, 'part_number' => $request->part_number[$i], 'srl_no' => $srl);
                                    DB::table('sys_sales_return_list_srl')->insert($values);
                                }

                                $discount = (float) str_replace(',', '', $request->discount[$i] ?? 0);
                                $istock = new SysItemStock();
                                $istock->slr_id = $sr->id;
                                $istock->account_id = $request->customer;
                                $istock->partno = $request->part_number[$i];
                                $istock->qty_in = $request->qty[$i];
                                $istock->description = $request->description[$i] ?? '';
                                $istock->price_in = ($value - $discount) / $request->qty[$i];
                                $istock->refno = $request->dn_doc_number;
                                $istock->doc_number = $sr->doc_number;
                                $istock->doc_date = $sr->doc_date;
                                $istock->deal_id = $sr->deal_id;
                                $istock->slno = $request->serial_no[$i];
                                $istock->status = 1;
                                $istock->created_by = Auth::user()->id;
                                $istock->company_id = session('logged_session_data.company_id');
                                $istock->currency_id = $request->currency;
                                $istock->sales_person = $request->sales_man;
                                $istock->save();

                            }
                        }
                    }
                }

                if (count($cart) > 0) {
                    //for($i = 0; $i < count($cart); $i++) {
                    foreach ($cart as $dt) {
                        if ($dt->part_number != "" && $dt->qty != "" && $dt->unitprice != "") {
                            $sii = new SysSalesReturnList();
                            $sii->sr_id = $sr->id;
                            $sii->part_number = $dt->part_number;
                            $sii->serial_no = $dt->serial_no;
                            $sii->tax = $dt->tax;
                            $sii->qty = $dt->qty;
                            $sii->unitprice = $dt->unitprice;
                            $sii->value = $dt->value;
                            $sii->discount = $dt->discount;
                            $sii->taxableamount = $dt->taxableamount;
                            $sii->vatamount = $dt->vatamount;
                            $sii->status = 1;
                            $sii->created_by = Auth::user()->id;
                            $sii->save();

                            $str_arr = explode(",", $dt->serial_no);
                            foreach ($str_arr as $srl) {
                                $values = array('sr_id' => $sr->id, 'part_number' => $dt->part_number, 'srl_no' => $srl);
                                DB::table('sys_sales_return_list_srl')->insert($values);
                            }

                            $discount = $dt->discount;
                            $istock = new SysItemStock();
                            $istock->slr_id = $sr->id;
                            $istock->account_id = $request->customer;
                            $istock->partno = $dt->part_number;
                            $istock->qty_in = $dt->qty;
                            $istock->price_in = ($dt->value - $discount) / $dt->qty;
                            $istock->refno = $request->dn_doc_number;
                            $istock->doc_number = $sr->doc_number;
                            $istock->doc_date = $sr->doc_date;
                            $istock->deal_id = $sr->deal_id;
                            $istock->slno = $dt->serial_no;
                            $istock->status = 1;
                            $istock->created_by = Auth::user()->id;
                            $istock->company_id = session('logged_session_data.company_id');
                            $istock->currency_id = $request->currency;
                            $istock->sales_person = $request->sales_man;
                            $istock->save();

                        }
                    }
                    SysSalesReturnListCart::where('cart_id', session('logged_session_data.cart_id'))->delete();
                }

                // Always finalize staged SR license keys, regardless of source (table rows or cart rows).
                $this->finalizeSalesReturnLicenseKeys($sr);



                SysCrmDeals::where('id', $sr->deal_id)->update(['sales_return_id' => $sr->id]);

                if ($request->part_number != "") {
                    if ((count(array_filter($request->part_number)) > 0 && count(array_filter($request->qty)) > 0 && count(array_filter($request->unitprice)) > 0)) {
                        $ret_qty = array_sum($request->qty);
                    }
                }
                if (count($cart) > 0) {
                    $ret_qty = $cart->sum('qty');
                }
                $dn_qty = SysSalesInvoiceItems::where('si_id', $request->dn_id)->sum('qty');

                if ($request->dn_id != "") {
                    if ($ret_qty == $dn_qty) {
                        SysSalesInvoice::where('id', $request->dn_id)->update(['return_status' => 1]);
                    } else {
                        SysSalesInvoice::where('id', $request->dn_id)->update(['return_status' => 2]);
                    }
                }



                SysSalesReturnAdjestment::where('srn_no', $sr->doc_number)->where('status', 5)->update(['status' => 1]);

                DB::commit();
                if ($request->btnSubmit == 1) {
                    Toastr::success('Sales Return Successfully Saved', 'Success');
                    return redirect('sales-return/' . $sr->id . '/download');
                    //return redirect('/delivery-note');
                } else {
                    Toastr::success('Sales Return Successfully Saved', 'Success');
                    return redirect('sales-return/' . $sr->id);
                }
            } else {
                Toastr::error('Operation Failed. please enter valid data', 'Failed');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            return $e;
            DB::rollback();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function download(Request $request, $id)
    {
        try {
            $sr = SysSalesReturn::find($id);
            if (!empty($sr)) {
                $company = SysCompany::find($sr->company_id);
                $sr_item = SysSalesReturnList::where('sr_id', '=', $sr->id)->get();
                $sup_email = SysCustSuppl::select('sys_cust_suppl.*')->join('sys_chartofaccounts', 'sys_chartofaccounts.account_code', 'sys_cust_suppl.code')->where('sys_chartofaccounts.id', $sr->customer)->first();
                if (!empty($sup_email)) {
                    $add = SysCustSupplAddressbook::where('cust_suppl_id', $sup_email->id)->first();
                }

                if (!empty($add)) {
                    $address = $add->address;
                    $address2 = $add->address2;
                    $city = $add->city;
                    $state = $add->statename->name;
                    $country = $add->countryname->name;
                    $email = $sup_email->email;
                    $tel = $sup_email->contcat_number;
                    $mobile = $sup_email->mobile;
                } else {
                    $address = "";
                    $address2 = "";
                    $city = "";
                    $state = "";
                    $country = "";
                    $email = "";
                    $tel = "";
                    $mobile = "";
                }
                $data = [
                    'sr' => $sr,
                    'company' => $company,
                    'sr_item' => $sr_item,
                    'email' => $email,
                    'tel' => $tel,
                    'address' => $address,
                    'address2' => $address2,
                    'city' => $city,
                    'state' => $state,
                    'country' => $country,
                    'mobile' => $mobile,
                ];

                $pdf = PDF::loadView('backEnd.pdf_print.srn_pdf', $data);
                $pdf->setPaper('A4', 'portrait');
                return $pdf->download($sr->doc_number . '-' . $sr->accountname->account_name . ".pdf");
            } else {
                return "error!!";
            }
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function edit($id)
    {
        try {
            $currency = SysCurrencySettings::select('id', 'code', 'ex_rate')->get();
            $company = SysCompany::find(session('logged_session_data.company_id'));
            $paymentterms = SysPaymentTerms::select('id', 'title')->orderby('title', 'asc')->get();

            $staff = SysHelper::get_sales_persons();

            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];

            $customs_freight_account = SysHelper::get_customs_freight_accounts_for_sales($company_id);
            $supplier = SysHelper::get_supplier_list_all($company_id);

            $countries = SysCountries::orderby('name', 'asc')->get();
            $states = SysStates::orderby('name', 'asc')->get();
            $items = SysHelper::get_product_list($company_id);
            $customertype = SysCustomerType::orderby('title', 'asc')->get();
            $saletype = SysSaleType::orderby('title', 'asc')->get();

            $edit = SysSalesReturn::find($id);
            $currencylist2 = DB::table('sys_currency_rate as r')->select('r.id', 'r.from_currency', 'r.to_currency', 'c.code', 'r.rate')
                ->join('sys_currency as c', 'c.id', 'r.to_currency')
                ->where('r.status', 1)->where('r.from_currency', $edit->currency)
                ->orderBy('c.code', 'ASC')->get();

            $edit_list = SysSalesReturnList::select('sys_sales_return_list.*', 'sm_items.part_number as partno', 'sm_items.description as description2', 'sm_items.product_type')
                ->join('sm_items', 'sm_items.id', 'sys_sales_return_list.part_number')
                ->where('sys_sales_return_list.sr_id', $id)->orderBy('sys_sales_return_list.sort_id')->get();

            $edit_list_srl = DB::table('sys_sales_return_list_srl')->selectRaw('part_number, GROUP_CONCAT(srl_no) as srl_no')->where('sr_id', $id)->groupby('part_number')->get();

            $customer = SysChartofAccounts::select('id', 'account_name')->where('id', $edit->customer)->get();

            $dn = DB::table('sys_delivery_note as dn')->select(DB::raw('sum(taxableamount) + sum(vatamount) as amount'))
                ->join('sys_delivery_note_items as dni', 'dni.dn_id', 'dn.id')
                ->where('dn.doc_number', $edit->dn_doc_number)->get();
            if (count($dn) > 0) {
                $invoice_amount = $dn[0]->amount;
            } else {
                $invoice_amount = 0;
            }

            //$srn_adjestment = SysSalesReturnAdjestment::where('dln_no',$edit->dn_doc_number)->get();

            $srn_adjestment_main = SysSalesInvoice::select(
                'sys_sales_invoice.doc_number',
                'sys_sales_invoice.doc_date',
                'sys_sales_invoice.lpo_number',
                'sys_sales_invoice.deal_id',
                'cat.debit_amount as total_amount',
                DB::raw('
            (SELECT IFNULL(SUM(paid_amount), 0) FROM sys_sales_return_adjestment WHERE siv_no = sys_sales_invoice.doc_number) +
            (SELECT IFNULL(SUM(bi_paid), 0) FROM sys_receipt_adjustments WHERE bi_doc_no = sys_sales_invoice.doc_number) 
            AS total_paid_amount'),
                'dn.doc_number as dn_doc_number',
                DB::raw("COALESCE((SELECT narration FROM sys_sales_return_adjestment WHERE siv_no = sys_sales_invoice.doc_number LIMIT 1), '') as narration")
            )
                ->join('sys_chartofaccounts_transaction as cat', 'cat.transaction_no', 'sys_sales_invoice.doc_number')
                ->leftJoin('sys_delivery_note as dn', 'dn.id', 'sys_sales_invoice.dn_id')
                ->where('cat.account_id', $edit->customer)
                //->where('sys_sales_invoice.doc_number', $edit->doc_number)
                ->where('sys_sales_invoice.customer', $edit->customer)
                ->where('cat.company_id', session('logged_session_data.company_id'))
                ->groupBy(
                    'sys_sales_invoice.doc_number',
                    'sys_sales_invoice.doc_date',
                    'cat.debit_amount',
                    'dn.doc_number',
                    'sys_sales_invoice.deal_id',
                    'sys_sales_invoice.lpo_number'
                )
                ->get();


            $opb = SysChartofAccountsTransaction::wherein('transaction_type', ['opbinvoice'])->where('status', 1)->where('account_id', $edit->customer)->where('company_id', $company_id)->get();
            $srn_adjestment = [];

            if (count($opb) > 0) {
                foreach ($opb as $dt) {
                    $paid = SysReceiptAdjustments::where('bi_doc_no', $dt->transaction_no)->sum('bi_paid');
                    //if(abs($dt->debit_amount - $dt->credit_amount)-$paid>0){
                    $srn_adjestment[] = [
                        'doc_number' => $dt->transaction_no,
                        'doc_date' => $dt->transaction_date,
                        'lpo_number' => '',
                        'deal_id' => '',
                        'total_amount' => abs($dt->debit_amount - $dt->credit_amount),
                        'total_paid_amount' => $paid,
                        'dn_doc_number' => '',
                        'narration' => '',
                    ];
                    //}
                }
            }


            $srn_adjestment = collect($srn_adjestment)->map(function ($item) {
                return (object) $item;
            });
            $srn_adjestment = $srn_adjestment->merge($srn_adjestment_main);
            $srn_adjestment = $srn_adjestment->sortByDesc('doc_date');
            $srn_adjestment = $srn_adjestment->values()->all(); // Optional: make it a plain array




            $editDataAdjustments = SysSalesReturnAdjestment::where('srn_no', $edit->doc_number)->where('status', 1)->get();

            //return $srn_adjestment;

            //return $srn_adjestment;
            //$srn_adjestment = SysSalesReturn::where('customer',$edit->customer)->get();
            //return  $srn_adjestment;

            $query = SysSalesReturn::select(DB::raw('sys_sales_return.*, (SELECT sum(debit_amount) FROM sys_chartofaccounts_transaction WHERE transaction_type="salesreturn" and transaction_no=sys_sales_return.doc_number) AS amount, (SELECT GROUP_CONCAT(code) FROM sys_crm_deals WHERE id=sys_sales_return.deal_id) AS code'));
            $query->wherein('company_id', $company_id);
            //$query->wherein('created_by',$r[1]);
            $query->orderby('doc_number', 'desc');
            $salesreturn = $query->paginate(50);

            return compact('currency', 'currencylist2', 'customer', 'customs_freight_account', 'items', 'paymentterms', 'company', 'customertype', 'saletype', 'staff', 'countries', 'states', 'supplier', 'edit', 'edit_list', 'invoice_amount', 'srn_adjestment', 'edit_list_srl', 'editDataAdjustments', 'salesreturn');
            // return view('backEnd.salesreturn.salesreturnedit', compact('currency','currencylist2', 'customer', 'customs_freight_account', 'items', 'paymentterms','company','customertype','saletype','staff','countries','states','supplier','edit','edit_list','invoice_amount','srn_adjestment','edit_list_srl','editDataAdjustments','salesreturn'));

        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function view(Request $request, $id)
    {
        try {
            $currency = SysCurrencySettings::select('id', 'code')->get();
            $company = SysCompany::find(session('logged_session_data.company_id'));
            $paymentterms = SysPaymentTerms::select('id', 'title')->orderby('title', 'asc')->get();

            $staff = SysHelper::get_sales_persons();

            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];

            $customer = SysHelper::get_customer_list_all($company_id);
            $customs_freight_account = SysHelper::get_customs_freight_accounts_for_sales($company_id);
            $supplier = SysHelper::get_supplier_list_all($company_id);

            $countries = SysCountries::orderby('name', 'asc')->get();
            $states = SysStates::orderby('name', 'asc')->get();
            $items = SysHelper::get_product_list($company_id);
            $customertype = SysCustomerType::orderby('title', 'asc')->get();
            $saletype = SysSaleType::orderby('title', 'asc')->get();

            $edit = SysSalesReturn::find($id);
            $edit_list = SysSalesReturnList::where('sr_id', $id)->get();
            $edit_list_srl = DB::table('sys_sales_return_list_srl')->selectRaw('part_number, GROUP_CONCAT(srl_no) as srl_no')->where('sr_id', $id)->groupby('part_number')->get();

            $dn = DB::table('sys_delivery_note as dn')->select(DB::raw('sum(taxableamount) + sum(vatamount) as amount'))
                ->join('sys_delivery_note_items as dni', 'dni.dn_id', 'dn.id')
                ->where('dn.doc_number', $edit->dn_doc_number)->get();
            if (count($dn) > 0) {
                $invoice_amount = $dn[0]->amount;
            } else {
                $invoice_amount = 0;
            }
            $srn_adjestment = SysSalesReturnAdjestment::where('dln_no', $edit->dn_doc_number)->get();
            $editDataAdjustments = SysSalesReturnAdjestment::where('srn_no', $edit->doc_number)->where('status', 1)->get();


            return view('backEnd.salesreturn.salesreturnview', compact('currency', 'customer', 'customs_freight_account', 'items', 'paymentterms', 'company', 'customertype', 'saletype', 'staff', 'countries', 'states', 'supplier', 'edit', 'edit_list', 'invoice_amount', 'srn_adjestment', 'edit_list_srl', 'editDataAdjustments'));

        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();
        //return $input;

        DB::beginTransaction();
        try {

            $doc_file = "";
            if ($request->file('doc') != "") {
                $file = $request->file('doc');
                $doc_file = md5(time()) . "." . $file->getClientOriginalExtension();
                $file->move('public/uploads/sales_return_doc/', $doc_file);
                $doc_file = $doc_file;
            }

            $sr = SysSalesReturn::find($id);
            $sr->doc_date = Carbon::createFromFormat('d/m/Y', $request->doc_date)->format('Y-m-d');
            $sr->dn_doc_number = $request->dn_doc_number;
            $sr->dn_doc_date = Carbon::createFromFormat('d/m/Y', $request->dn_doc_date)->format('Y-m-d');
            $sr->si_doc_number = $request->si_doc_number;
            $sr->si_doc_date = Carbon::createFromFormat('d/m/Y', $request->si_doc_date)->format('Y-m-d');
            $sr->customer = $request->customer;
            $sr->currency = $request->currency;
            $sr->printed_invoice_number = $request->printed_invoice_number;
            $sr->lpo_number = $request->reference_no;
            $sr->lpo_date = Carbon::createFromFormat('d/m/Y', $request->reference_date)->format('Y-m-d');
            $sr->payment_terms = $request->payment_terms;
            $sr->payment_terms2 = $request->payment_terms2;
            $sr->delivery_terms = $request->delivery_terms;
            $sr->sales_man = $request->sales_man;

            $sr->ref_supplier_id = $request->ref_supplier_id ? implode(',', $request->ref_supplier_id) : null;

            $sr->narration = $request->narration;

            // $sr->shipping_name= $request->shipping_name;
            // $sr->shipping_address= $request->shipping_address;

            $sr->shipping_address = $request->shipping_address_1;
            $sr->shipping_name = $request->shipping_name;
            $sr->shipping_supplier = $request->shipping_supplier;
            $sr->shipping_contact_no = $request->shipping_contact_no;
            $sr->shipping_email = $request->shipping_email;

            $sr->customer_type = $request->customer_type;
            $sr->sale_type = $request->sale_type;
            $sr->customer_country = $request->customer_country;
            $sr->customer_state = $request->customer_state;
            $sr->end_user_name = $request->end_user_name;
            $sr->contact_person_name = $request->contact_person_name;
            $sr->contact_person_email = $request->contact_person_email;
            $sr->contact_person_no = $request->contact_person_no;
            $sr->credit_note = $request->credit_note;

            if ($doc_file != "") {
                $sr->attachment = $doc_file;
            }
            //$sr->net_vat= $request->net_vat;

            $sr->deal_id = SysHelper::get_dealid_from_code($request->deal_id);

            $sr->narration = $request->narration;
            $sr->supplier_name = $request->supplier_name;
            $sr->ref_supplier_id = $request->ref_supplier_id ? implode(',', $request->ref_supplier_id) : null;
            $sr->company_id = session('logged_session_data.company_id');
            $sr->updated_by = Auth::user()->id;
            $sr->updated_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
            $sr->status = 1;
            $sr->save();
            $sr->toArray();

$total_tax_amount = array_sum(
    array_map(function ($value) {
        return (float) str_replace(',', '', $value);
    }, $request->taxableamount)
);

$total_vat_amount = array_sum(
    array_map(function ($value) {
        return (float) str_replace(',', '', $value);
    }, $request->vatamount)
);
            $total_amount = $total_tax_amount + $total_vat_amount;


            DB::table('sys_chartofaccounts_transaction')->where('transaction_type', 'salesreturn')->where('transaction_id', $request->id)->delete();
            DB::table('sys_sales_return_list')->where('sr_id', $request->id)->delete();
            DB::table('sys_item_stock')->where('slr_id', $request->id)->delete();
            DB::table('sys_sales_return_list_srl')->where('sr_id', $request->id)->delete();

            //customer account cr
            SysHelper::trn_chartof_accounts_transaction($request->customer, $sr->id, $sr->doc_number, $sr->doc_date, 'salesreturn', '0.00', $total_amount, '', 1, 0, "", 1);

            //sales account dr
            $sales_return_account_id = SysHelper::get_sales_return_account_id();
            SysHelper::trn_chartof_accounts_transaction($sales_return_account_id, $sr->id, $sr->doc_number, $sr->doc_date, 'salesreturn', $total_tax_amount, '0.00', '', 1, 0, "", 1);

            //vat on sales account dr
            $sales_vat_account_id = SysHelper::get_sales_vat_account_id();
            SysHelper::trn_chartof_accounts_transaction($sales_vat_account_id, $sr->id, $sr->doc_number, $sr->doc_date, 'salesreturn', $total_vat_amount, '0.00', '', 1, 0, "", 1);


            for ($i = 0; $i < count($request->part_number); $i++) {
                if ($request->part_number[$i] != "" && $request->qty[$i] != "" && $request->unitprice[$i] != "") {
                    $sii = new SysSalesReturnList();
                    $sii->sr_id = $sr->id;
                    $sii->part_number = $request->part_number[$i];
                    if (isset($request->serial_no)) {
                        $sii->serial_no = $request->serial_no[$i];
                    }

                    $sii->tax = filled($request->tax[$i]) ? $request->tax[$i] : 0;
                    $sii->qty = $request->qty[$i];
                    $sii->description = $request->description[$i] ?? '';
                    $sii->unitprice = (float) str_replace(',', '', $request->unitprice[$i] ?? 0);
                    $value = (float) str_replace(',', '', $request->value[$i] ?? 0);
                    $sii->value = $value;
                    $sii->discount = (float) str_replace(',', '', $request->discount[$i] ?? 0);
                    $sii->taxableamount = (float) str_replace(',', '', $request->taxableamount[$i] ?? 0);
                    $sii->vatamount = (float) str_replace(',', '', $request->vatamount[$i] ?? 0);
                    $sii->sort_id = $request->sort_id[$i];
                    $sii->status = 1;
                    $sii->created_by = Auth::user()->id;
                    $sii->save();

                    if (isset($request->serial_no)) {
                        $str_arr = explode(",", $request->serial_no[$i]);
                        foreach ($str_arr as $srl) {
                            $values = array('sr_id' => $sr->id, 'part_number' => $request->part_number[$i], 'srl_no' => $srl);
                            DB::table('sys_sales_return_list_srl')->insert($values);
                        }
                    }


                    $discount = (float) str_replace(',', '', $request->discount[$i] ?? 0);
                    $istock = new SysItemStock();
                    $istock->slr_id = $sr->id;
                    $istock->account_id = $request->customer;
                    $istock->partno = $request->part_number[$i];
                    $istock->qty_in = $request->qty[$i];
                    $istock->price_in = ($value - $discount) / $request->qty[$i];
                    $istock->refno = $request->dn_doc_number;
                    $istock->doc_number = $sr->doc_number;
                    $istock->doc_date = $sr->doc_date;
                    $istock->description = $request->description[$i] ?? '';
                    $istock->deal_id = SysHelper::get_dealid_from_code($sr->deal_id);
                    if (isset($request->serial_no[$i])) {
                        $istock->slno = $request->serial_no[$i];
                    } else {
                        $istock->slno = '';
                    }
                    $istock->status = 1;
                    $istock->created_by = Auth::user()->id;
                    $istock->company_id = session('logged_session_data.company_id');
                    $istock->currency_id = $request->currency;
                    $istock->sales_person = $request->sales_man;
                    $istock->save();

                }
            }

            // Finalize current staged keys for this SR on update as well.
            $this->finalizeSalesReturnLicenseKeys($sr);

            DB::commit();

            SysCrmDeals::where('id', SysHelper::get_dealid_from_code($request->deal_id))->update(['sales_return_id' => $sr->id]);

            $ret_qty = array_sum($request->qty);
            $dn_qty = SysDeliveryNoteItems::where('dn_id', $sr->id)->sum('qty');
            if ($ret_qty == $dn_qty) {
                SysDeliveryNote::where('id', $sr->id)->update(['return_status' => 1]);
            } else {
                SysDeliveryNote::where('id', $sr->id)->update(['return_status' => 2]);
            }

            if ($request->btnSubmit == 1) {
                Toastr::success('Sales Return Successfully Updated', 'Success');
                return redirect('sales-return/' . $id . '/download');
                //return redirect('/delivery-note');
            } else {
                Toastr::success('Sales Return Successfully Updated', 'Success');
                return redirect('sales-return/' . $id);
            }

        } catch (\Exception $e) {
            DB::rollback();
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function delete_adjustment($id)
    {
        try {
            DB::table('sys_sales_return_adjestment')->where('id', $id)->delete();

            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();
            $no = DB::table('sys_sales_return')->where('id', $id)->first();
            SysSalesReturn::where('id', $id)->update(['status' => 2]);
            SysSalesReturnList::where('sr_id', $id)->update(['status' => 2]);
            DB::table('sys_sales_return_list_srl')->where('sr_id', $id)->update(['status' => 2]);
            DB::table('sys_sales_return_adjestment')->where('srn_no', $no->doc_number)->update(['status' => 2]);
            SysItemStock::where('slr_id', $id)->update(['status' => 2]);
            //SysCrmDeals::where('sales_return_id',$id)->update([ 'sales_return_id' => 0]);
            DB::table('sys_chartofaccounts_transaction')->where('transaction_id', $id)->where('transaction_type', 'salesreturn')->update(['status' => 2]);
            DB::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();

        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function restore($id)
    {
        try {
            DB::beginTransaction();
            $no = DB::table('sys_sales_return')->where('id', $id)->first();
            SysSalesReturn::where('id', $id)->update(['status' => 1]);
            SysSalesReturnList::where('sr_id', $id)->update(['status' => 1]);
            DB::table('sys_sales_return_list_srl')->where('sr_id', $id)->update(['status' => 1]);
            DB::table('sys_sales_return_adjestment')->where('srn_no', $no->doc_number)->update(['status' => 1]);
            SysItemStock::where('slr_id', $id)->update(['status' => 1]);
            //SysCrmDeals::where('sales_return_id',$id)->update([ 'sales_return_id' => 0]);
            DB::table('sys_chartofaccounts_transaction')->where('transaction_id', $id)->where('transaction_type', 'salesreturn')->update(['status' => 1]);
            DB::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();

        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    //normal sr cart
    function addsalesreturnitemscart(Request $request)
    {
        try {
            DB::table('sys_sales_return_list_cart')->insert(
                [
                    'cart_id' => session('logged_session_data.cart_id'),
                    'part_number' => $request->part_number,
                    'tax' => $request->tax,
                    'qty' => $request->qty,
                    'unitprice' => $request->unitprice,
                    'value' => $request->value,
                    'discount' => $request->discount,
                    'taxableamount' => $request->taxableamount,
                    'vatamount' => $request->vatamount,
                    'serial_no' => $request->serial_no,
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                ]
            );
            $ret = SysSalesReturnListCart::select('sys_sales_return_list_cart.*', 'sm_items.part_number AS partno', 'sm_items.description')
                ->join('sm_items', 'sm_items.id', 'sys_sales_return_list_cart.part_number')
                ->where('cart_id', session('logged_session_data.cart_id'))->get();
            if (count($ret) > 0) {
                return json_encode(array('data' => $ret));
            } else {
                $ret = [];
                return json_encode(array('data' => $ret));
            }
        } catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }

    function updatesalesreturnitemscart(Request $request)
    {
        try {
            DB::table('sys_sales_return_list_cart')->where('id', $request->itm_id)->update([
                'part_number' => $request->part_number,
                'tax' => $request->tax,
                'qty' => $request->qty,
                'unitprice' => $request->unitprice,
                'value' => $request->value,
                'discount' => $request->discount,
                'taxableamount' => $request->taxableamount,
                'vatamount' => $request->vatamount,
                'serial_no' => $request->serial_no,
            ]);

            $ret = SysSalesReturnListCart::select('sys_sales_return_list_cart.*', 'sm_items.part_number AS partno', 'sm_items.description')
                ->join('sm_items', 'sm_items.id', 'sys_sales_return_list_cart.part_number')
                ->where('cart_id', session('logged_session_data.cart_id'))->get();

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

    function deletesalesreturnitemscart(Request $request)
    {
        try {
            DB::table('sys_sales_return_list_cart')->where('id', $request->id)->delete();
            $ret = SysSalesReturnListCart::select('sys_sales_return_list_cart.*', 'sm_items.part_number AS partno', 'sm_items.description')
                ->join('sm_items', 'sm_items.id', 'sys_sales_return_list_cart.part_number')
                ->where('cart_id', session('logged_session_data.cart_id'))->get();

            if (count($ret) > 0) {
                return json_encode(array('data' => $ret));
            } else {
                $ret = [];
                return json_encode(array('data' => $ret));
            }
        } catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }

    //edit page
    function addsalesreturnitems(Request $request)
    {
        try {
            if ($request->discount == "") {
                $discount = 0;
            } else {
                $discount = $request->discount;
            }
            DB::table('sys_sales_return_list')->insert(
                [
                    'sr_id' => $request->sr_id,
                    'part_number' => $request->part_number,
                    'tax' => $request->tax,
                    'qty' => $request->qty,
                    'unitprice' => $request->unitprice,
                    'value' => $request->value,
                    'discount' => $discount,
                    'taxableamount' => $request->taxableamount,
                    'vatamount' => $request->vatamount,
                    'serial_no' => $request->serial_no,
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                ]
            );

            $sr = SysSalesReturn::where('id', $request->sr_id)->first();
            $sr_li = SysSalesReturnList::where('sr_id', $request->sr_id)->get();
            DB::table('sys_chartofaccounts_transaction')->where('transaction_type', 'salesreturn')->where('transaction_id', $request->sr_id)->delete();
            $total_amount = $sr_li->sum('taxableamount');
            $total_vat_amount = $sr_li->sum('vatamount');
            $total_tax_amount = $total_amount + $total_vat_amount;
            //customer account cr
            SysHelper::trn_chartof_accounts_transaction($request->customer, $sr->id, $sr->doc_number, $sr->doc_date, 'salesreturn', '0.00', $total_amount, '', 1, 0, "", 1);
            //sales account dr
            $sales_return_account_id = SysHelper::get_sales_return_account_id();
            SysHelper::trn_chartof_accounts_transaction($sales_return_account_id, $sr->id, $sr->doc_number, $sr->doc_date, 'salesreturn', $total_tax_amount, '0.00', '', 1, 0, "", 1);
            //vat on sales account dr
            $sales_vat_account_id = SysHelper::get_sales_vat_account_id();
            SysHelper::trn_chartof_accounts_transaction($sales_vat_account_id, $sr->id, $sr->doc_number, $sr->doc_date, 'salesreturn', $total_vat_amount, '0.00', '', 1, 0, "", 1);


            $ret = SysSalesReturnList::select('sys_sales_return_list.*', 'sm_items.part_number AS partno', 'sm_items.description')
                ->join('sm_items', 'sm_items.id', 'sys_sales_return_list.part_number')
                ->where('sr_id', $request->sr_id)->get();

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

    function updatesalesreturnitems(Request $request)
    {
        try {
            if ($request->discount == "") {
                $discount = 0;
            } else {
                $discount = $request->discount;
            }
            DB::table('sys_sales_return_list')->where('id', $request->itm_id)->update([
                'part_number' => $request->part_number,
                'tax' => $request->tax,
                'qty' => $request->qty,
                'unitprice' => $request->unitprice,
                'value' => $request->value,
                'discount' => $discount,
                'taxableamount' => $request->taxableamount,
                'vatamount' => $request->vatamount,
                'serial_no' => $request->serial_no,
            ]);


            $sr = SysSalesReturn::where('id', $request->sr_id)->first();
            $sr_li = SysSalesReturnList::where('sr_id', $request->sr_id)->get();
            DB::table('sys_chartofaccounts_transaction')->where('transaction_type', 'salesreturn')->where('transaction_id', $request->sr_id)->delete();

            $total_amount = $sr_li->sum('taxableamount');
            $total_vat_amount = $sr_li->sum('vatamount');
            $total_tax_amount = $total_amount + $total_vat_amount;
            //customer account cr
            SysHelper::trn_chartof_accounts_transaction($request->customer, $sr->id, $sr->doc_number, $sr->doc_date, 'salesreturn', '0.00', $total_amount, '', 1, 0, "", 1);
            //sales account dr
            $sales_return_account_id = SysHelper::get_sales_return_account_id();
            SysHelper::trn_chartof_accounts_transaction($sales_return_account_id, $sr->id, $sr->doc_number, $sr->doc_date, 'salesreturn', $total_tax_amount, '0.00', '', 1, 0, "", 1);
            //vat on sales account dr
            $sales_vat_account_id = SysHelper::get_sales_vat_account_id();
            SysHelper::trn_chartof_accounts_transaction($sales_vat_account_id, $sr->id, $sr->doc_number, $sr->doc_date, 'salesreturn', $total_vat_amount, '0.00', '', 1, 0, "", 1);

            $ret = SysSalesReturnList::select('sys_sales_return_list.*', 'sm_items.part_number AS partno', 'sm_items.description')
                ->join('sm_items', 'sm_items.id', 'sys_sales_return_list.part_number')
                ->where('sr_id', $request->sr_id)->get();

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

    function deletesalesreturnitems(Request $request)
    {
        try {
            $sr_id = DB::table('sys_sales_return_list')->where('id', $request->id)->first();

            DB::table('sys_sales_return_list')->where('id', $request->id)->delete();

            $sr_li = SysSalesReturnList::where('sr_id', $sr_id->sr_id)->get();
            $sr = SysSalesReturn::where('id', $sr_id->sr_id)->first();
            DB::table('sys_chartofaccounts_transaction')->where('transaction_type', 'salesreturn')->where('transaction_id', $sr_id->sr_id)->delete();

            $total_amount = $sr_li->sum('taxableamount');
            $total_vat_amount = $sr_li->sum('vatamount');
            $total_tax_amount = $total_amount + $total_vat_amount;
            //customer account cr
            SysHelper::trn_chartof_accounts_transaction($sr->customer, $sr->id, $sr->doc_number, $sr->doc_date, 'salesreturn', '0.00', $total_amount, '', 1, 0, "", 1);
            //sales account dr
            $sales_return_account_id = SysHelper::get_sales_return_account_id();
            SysHelper::trn_chartof_accounts_transaction($sales_return_account_id, $sr->id, $sr->doc_number, $sr->doc_date, 'salesreturn', $total_tax_amount, '0.00', '', 1, 0, "", 1);
            //vat on sales account dr
            $sales_vat_account_id = SysHelper::get_sales_vat_account_id();
            SysHelper::trn_chartof_accounts_transaction($sales_vat_account_id, $sr->id, $sr->doc_number, $sr->doc_date, 'salesreturn', $total_vat_amount, '0.00', '', 1, 0, "", 1);

            $ret = SysSalesReturnList::select('sys_sales_return_list.*', 'sm_items.part_number AS partno', 'sm_items.description')
                ->join('sm_items', 'sm_items.id', 'sys_sales_return_list.part_number')
                ->where('sr_id', $request->sr_id)->get();

            if (count($ret) > 0) {
                return json_encode(array('data' => $ret));
            } else {
                $ret = [];
                return json_encode(array('data' => $ret));
            }
        } catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }

    function salesreturnupdate_currency(Request $request)
    {
        try {
            if ($request->to_currency_id != $request->from_currency_id) {

                $to_currency = SysCurrencyRate::where('id', $request->to_currency_id)->value('to_currency');
                SysSalesReturn::where('id', $request->cur_sr_id)->update(['currency' => $to_currency]);
                $qt = SysSalesReturnList::where('sr_id', $request->cur_sr_id)->get();
                $ca = SysChartofAccountsTransaction::where('transaction_id', $request->cur_sr_id)->where('transaction_type', 'salesreturn')->get();
                foreach ($qt as $t) {
                    $new_price = $t->unitprice * $request->to_currency_rate;

                    $new_discount = $t->discount * $request->to_currency_rate;

                    SysSalesReturnList::where('id', $t->id)->update(
                        [
                            'unitprice' => $new_price,
                            'value' => $new_price * $t->qty,
                            'discount' => $new_discount,
                            'taxableamount' => ($new_price * $t->qty) - $new_discount,
                            'vatamount' => (($new_price * $t->qty) - $new_discount) * $t->tax / 100,
                        ]
                    );

                    SysItemStock::where('doc_number', $request->cur_sr_doc_no)->where('partno', $t->part_number)->update(
                        ['price_in' => ($new_price * $t->qty) - $new_discount, 'currency_id' => $to_currency]
                    );
                }
                foreach ($ca as $t) {
                    $new_debit_amount = $t->debit_amount * $request->to_currency_rate;
                    $new_credit_amount = $t->credit_amount * $request->to_currency_rate;

                    SysChartofAccountsTransaction::where('id', $t->id)->update(
                        [
                            'debit_amount' => $new_debit_amount,
                            'credit_amount' => $new_credit_amount,
                        ]
                    );
                }
            }

            Toastr::success('Currency Updated Successfully. Please Update Sales Return', 'Success');
            return redirect()->back();

        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

}

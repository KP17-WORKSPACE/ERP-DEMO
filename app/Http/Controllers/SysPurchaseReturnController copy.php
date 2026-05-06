<?php

namespace App\Http\Controllers;

use App\SysChartofAccounts;
use App\SysAccountType;
use App\ApiBaseMethod;
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
use App\SysCustSupplAddressbook;
use App\SysHelper;
use App\SysItemStock;
use App\SysPaymentTerms;
use App\SysPurchaseGRNItemsSrlnoCart;
use App\SysPurchaseGrnLicenseKey;
use App\SysPurchaseInvoice;
use App\SysPurchaseInvoiceItems;
use App\SysPurchaseReturn;
use App\SysPurchaseReturnAdjestment;
use App\SysPurchaseReturnItemsSrlnoCart;
use App\SysPurchaseReturnList;
use App\SysPurchaseReturnListCart;
use App\SysPurchaseType;
use App\SysReceiptMode;
use App\SysSalesInvoice;
use App\SysStates;
use App\SysSupplierType;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SysPurchaseReturnController extends Controller
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
    public function purchasereturnList(Request $request)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $supplier_list = SysHelper::get_supplier_list($company_id);

            $query = SysPurchaseReturn::select(DB::raw('sys_purchase_return.*, (SELECT GROUP_CONCAT(doc_number) as doc_number FROM sys_purchase_grn WHERE lpo_number = sys_purchase_return.lpo_number) AS grnno, (SELECT GROUP_CONCAT(doc_number) FROM sys_purchase_order WHERE id = sys_purchase_return.ref_po_id) AS po_no, (SELECT SUM(taxableamount)+SUM(vatamount) AS amount FROM sys_purchase_return_list WHERE pr_id = sys_purchase_return.id) AS amount, (SELECT GROUP_CONCAT(code) FROM sys_crm_deals WHERE id=sys_purchase_return.deal_id) AS code'));
            if (SysHelper::get_pagination_post($request)) {
                if ($request->documents_number != "") {
                    $query->where('doc_number', 'like', '%' . $request->documents_number . '%');
                }
                if ($request->supplier != "") {
                    $query->where('vendors', $request->supplier);
                }
                if ($request->customer != "") {
                    $query->where('narration', 'like', '%' . $request->customer . '%');
                }
                if ($request->purchase_order_number != "") {
                    $po_nos = SysPurchaseReturn::join('sys_purchase_order', 'sys_purchase_order.id', 'sys_purchase_return.ref_po_id')
                        ->where('sys_purchase_order.doc_number', 'like', '%' . $request->purchase_order_number . '%')->pluck('sys_purchase_return.doc_number');
                    $query->wherein('doc_number', $po_nos);
                }
                if ($request->grn_number != "") {
                    $grn_nos = SysPurchaseReturn::join('sys_purchase_grn', 'sys_purchase_grn.id', 'sys_purchase_return.ref_grn_id')
                        ->where('sys_purchase_grn.doc_number', 'like', '%' . $request->grn_number . '%')->pluck('sys_purchase_return.doc_number');
                    $query->wherein('doc_number', $grn_nos);
                }
                if ($request->purchase_invoice_number != "") {
                    $query->where('pi_number', 'like', '%' . $request->purchase_invoice_number . '%');
                }
                // if ($request->amount != "") {                    
                //     $amt_nos = SysChartofAccountsTransaction::where('transaction_type', 'salesreturn')->where('debit_amount',$request->amount)->pluck('transaction_no');
                //     $query->wherein('doc_number',$amt_nos);
                // }
                if ($request->date != "") {
                    $query->where('doc_date', $request->date);
                }
            } else {

            }
            $query->wherein('company_id', $company_id);
            $query->orderby('doc_number', 'desc');
            $purchasereturn = $query->paginate(50);
            $id = $purchasereturn->first()->id;
            $data = $this->get_pr_pdf_data($id);


            return view('backEnd.purchasereturn.purchasereturnlist', compact('purchasereturn', 'supplier_list', 'data'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function getDetails($id)
    {
        $data = $this->get_pr_pdf_data($id);
        if (count($data) > 0) {
            return view('backEnd.purchasereturn.pr_details', $data);
        } else {
            return "error!!";
        }

    }

    public function get_pr_pdf_data($id)
    {
        try {
            $address = "";
            $address2 = "";
            $city = "";
            $state = "";
            $country = "";
            $contact_name = "";
            $email = "";
            $tel = "";
            $ship_company_name = "";
            $delivery_city = "";
            $delivery_zip_code = "";
            $delivery_country = "";
            $delivery_state = "";
            $cust_trn_no = "";

            $pr = SysPurchaseReturn::find($id);
            if (!empty($pr)) {
                $company = SysCompany::find($pr->company_id);
                $pi_item = SysPurchaseReturnList::where('pr_id', '=', $pr->id)->get();
                $sup_email = SysCustSuppl::select('sys_cust_suppl.*')->join('sys_chartofaccounts', 'sys_chartofaccounts.account_code', 'sys_cust_suppl.code')->where('sys_chartofaccounts.id', $pr->vendors)->first();

                if (!empty($sup_email)) {
                    $add = SysCustSupplAddressbook::where('cust_suppl_id', $sup_email->id)->first();
                }

                $contact_name = $sup_email->customer_salutation . ' ' . $sup_email->first_name . ' ' . $sup_email->last_name;
                $email = $sup_email->email;
                $tel = $sup_email->contcat_number;
                $cust_trn_no = $sup_email->vat_number;

                if (!empty($add)) {
                    $address = $add->address;
                    $address2 = $add->address2;
                    $city = $add->city . ', PB No: ' . $add->zip_code;
                    $state = $add->statename->name;
                    $country = $add->countryname->name;
                }

                if ($pr->deal_id != 0 && $pr->deal_id != "") {
                    $deal_details = SysCrmDeals::where('id', $pr->deal_id)->first();

                    if (isset($deal_details)) {
                        if ($deal_details->delivery_company != "") {
                            $ship_company_name = $deal_details->delivery_company;
                        } else {
                            $ship_company_name = $deal_details->customername->name;
                        }

                        if ($deal_details->delivery_address1 != "") {
                            $ship_address1 = $deal_details->delivery_address1;
                        } else {
                            $ship_address1 = $deal_details->address;
                        }
                        if ($deal_details->delivery_address2 != "") {
                            $ship_address2 = $deal_details->delivery_address2;
                        } else {
                            $ship_address2 = "";
                        }
                        if ($deal_details->delivery_city != "") {
                            $delivery_city = $deal_details->delivery_city;
                        } else {
                            $delivery_city = $add->city;
                        }
                        if ($deal_details->delivery_zip_code != "") {
                            $delivery_zip_code = $deal_details->delivery_zip_code;
                        } else {
                            $delivery_zip_code = $add->zip_code;
                        }
                        if ($deal_details->delivery_country != "") {
                            $delivery_country = $deal_details->country->name;
                        } else {
                            $delivery_country = $add->countryname->name;
                        }
                        if ($deal_details->delivery_state != "") {
                            $delivery_state = $deal_details->state->name;
                        } else {
                            $delivery_state = $add->statename->name;
                        }


                        if ($deal_details->delivery_name != "") {
                            $ship_contact_name = $deal_details->delivery_name;
                        } else {
                            $ship_contact_name = $deal_details->cust_name;
                        }
                        if ($deal_details->delivery_number != "") {
                            $ship_tel = $deal_details->delivery_number;
                        } else {
                            $ship_tel = $deal_details->cust_no;
                        }
                        if ($deal_details->delivery_email != "") {
                            $ship_email = $deal_details->delivery_email;
                        } else {
                            $ship_email = $deal_details->cust_email;
                        }
                    }
                } else {
                    $ship_company_name = "";
                    $ship_contact_name = $contact_name;
                    $ship_email = $email;
                    $ship_tel = $tel;
                    $ship_address1 = $add->city . ', PB No: ' . $add->zip_code;
                    $ship_address2 = "";
                    $delivery_city = $add->city . ', PB No: ' . $add->zip_code;
                    $delivery_zip_code = $add->zip_code;
                    $delivery_country = $add->countryname->name;
                    $delivery_state = $add->statename->name;
                }


                $data = [
                    'pr' => $pr,
                    'company' => $company,
                    'pi_item' => $pi_item,
                    'email' => $email,
                    'tel' => $tel,
                    'address' => $address,
                    'address2' => $address2,
                    'city' => $city,
                    'state' => $state,
                    'country' => $country,
                    'contact_name' => $contact_name,

                    'ship_company_name' => $ship_company_name,
                    'ship_address1' => $ship_address1,
                    'ship_address2' => $ship_address2,
                    'delivery_city' => $delivery_city,
                    'delivery_zip_code' => $delivery_zip_code,
                    'delivery_country' => $delivery_country,
                    'delivery_state' => $delivery_state,

                    'ship_contact_name' => $ship_contact_name,
                    'ship_tel' => $ship_tel,
                    'ship_email' => $ship_email,
                    'cust_trn_no' => $cust_trn_no,
                ];
                return $data;
            }
            return [];
        } catch (\Throwable $th) {
            return [];
        }
    }

    public function purchasereturnAdd(Request $request)
    {
        // $items = SysPurchaseInvoiceItems::select('sys_purchase_invoice_items.*', 'sm_items.part_number','sm_items.description')->join('sm_items','sys_purchase_invoice_items.part_number','sm_items.id')->whereIn('pi_id',[100,101])->get();
        // return $items;

        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];

            $currency = SysCurrencySettings::select('id', 'code')->get();
            $paymentterms = SysPaymentTerms::select('id', 'title')->orderby('title', 'asc')->get();

            $vendors = SysHelper::get_supplier_list($company_id);
            $company = SysCompany::find(session('logged_session_data.company_id'));

            $customs_freight_account = SysHelper::get_customs_freight_accounts_for_purchase($company_id);
            $salesman = SysHelper::get_sales_persons();

            $suppliertype = SysSupplierType::orderby('title', 'asc')->get();
            $purchasetype = SysPurchaseType::orderby('title', 'asc')->get();
            $countries = SysCountries::orderby('name', 'asc')->get();
            $states = SysStates::orderby('name', 'asc')->get();
            $items = SysHelper::get_product_list($company_id);

            $cart = SysPurchaseReturnListCart::select('sys_purchase_return_list_cart.*', 'sm_items.part_number AS partno', 'sm_items.description')
                ->join('sm_items', 'sm_items.id', 'sys_purchase_return_list_cart.part_number')
                ->where('cart_id', session('logged_session_data.cart_id'))->get();
            //return $cart;

            // $accounts = SysChartofAccounts::all();
            // $currency = SysCurrencySettings::all();            
            // $supplier = SysChartofAccounts::select('id','account_name')->where('subgroup',3)->get(); //1cust, 3supp
            // if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            //     return ApiBaseMethod::sendResponse($accounts, null);
            // }
            $query = SysPurchaseReturn::select(DB::raw('sys_purchase_return.*, (SELECT GROUP_CONCAT(doc_number) as doc_number FROM sys_purchase_grn WHERE lpo_number = sys_purchase_return.lpo_number) AS grnno, (SELECT GROUP_CONCAT(doc_number) FROM sys_purchase_order WHERE id = sys_purchase_return.ref_po_id) AS po_no, (SELECT SUM(taxableamount)+SUM(vatamount) AS amount FROM sys_purchase_return_list WHERE pr_id = sys_purchase_return.id) AS amount, (SELECT GROUP_CONCAT(code) FROM sys_crm_deals WHERE id=sys_purchase_return.deal_id) AS code'));
            $query->wherein('company_id', $company_id);
            $query->orderby('doc_number', 'desc');
            $purchasereturn = $query->paginate(50);

            return view('backEnd.purchasereturn.purchasereturnadd', compact('purchasereturn', 'currency', 'vendors', 'paymentterms', 'suppliertype', 'purchasetype', 'countries', 'states', 'customs_freight_account', 'items', 'cart', 'company', 'salesman'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function purchasereturnadd_adjestment(Request $request)
    {
        //return $request->all();
        try {
            if (count($request->adj_paid) > 0) {
                for ($i = 0; $i < count($request->adj_paid); $i++) {
                    SysPurchaseReturnAdjestment::where(['pri_no' => $request->adj_pri_no])->delete(); //, 'piv_no' => $request->adj_pi_no[$i]
                }
                for ($i = 0; $i < count($request->adj_paid); $i++) {
                    if ($request->adj_paid[$i] != "" && $request->adj_paid[$i] != 0) {
                        $data = [
                            'pri_no' => $request->adj_pri_no,
                            'lpo_no' => $request->edit_adj_lpo_no,
                            'piv_no' => $request->adj_pi_no[$i],
                            'doc_date' => date('Y-m-d', strtotime($request->edit_adj_doc_date)),
                            'total_amount' => $request->adj_total[$i],
                            'paid_amount' => $request->adj_paid[$i],
                            'balance_amount' => $request->adj_total[$i] - $request->adj_paid[$i],
                            'status' => 1,
                            'created_by' => Auth::user()->id,
                        ];

                        SysPurchaseReturnAdjestment::insert($data);
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

    public function adjestment_list(Request $request)
    {
        try {
            $docno = SysPurchaseInvoice::select('doc_number')->where('id', $request->id)->first();
            $ret = SysPurchaseReturnAdjestment::select('pri_no', 'piv_no', 'lpo_no', 'doc_date', 'total_amount', 'paid_amount', 'balance_amount')->where('piv_no', $docno->doc_number)->get();
            return json_encode(array('data' => $ret));

        } catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }
    public function adjestment_list_add(Request $request)
    {
        try {
            $pri_adjestment = SysPurchaseInvoice::select('sys_purchase_invoice.doc_number as doc_number', 'sys_purchase_invoice.pi_date as doc_date', 'cat.credit_amount as total_amount', DB::raw('sum(adj.paid_amount) as paid_amount'), 'adj.status as adj_status')
                ->join('sys_chartofaccounts_transaction as cat', 'cat.transaction_no', 'sys_purchase_invoice.doc_number')
                ->leftjoin('sys_purchase_return_adjestment as adj', 'adj.piv_no', 'sys_purchase_invoice.doc_number')
                ->where('sys_purchase_invoice.vendors', $request->id)
                ->where('account_id', $request->id)->where('cat.company_id', session('logged_session_data.company_id'))
                ->groupby('sys_purchase_invoice.doc_number', 'sys_purchase_invoice.pi_date', 'cat.credit_amount', 'adj.status')
                ->orderby('sys_purchase_invoice.doc_number', 'asc')
                ->get();
            return json_encode(array('data' => $pri_adjestment));

        } catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }
    public function purchasereturnadd_adjestment2(Request $request)
    {
        try {
            $data = [
                'pri_no' => $request->pri_no,
                'piv_no' => $request->piv_no,
                'lpo_no' => $request->lpo_no,
                'doc_date' => $request->doc_date,
                'total_amount' => $request->total_amount,
                'paid_amount' => $request->paid_amount,
                'balance_amount' => $request->balance_amount,
                'status' => 1,
                'created_by' => Auth::user()->id,
            ];
            $check = SysPurchaseReturnAdjestment::where($data)->count();
            if ($check == 0) {
                SysPurchaseReturnAdjestment::insert($data);
            }
            $ret = SysPurchaseReturnAdjestment::select('pri_no', 'piv_no', 'lpo_no', 'doc_date', 'total_amount', 'paid_amount', 'balance_amount')->where('piv_no', $request->piv_no)->get();
            return json_encode(array('data' => $ret));

        } catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }
    public function purchasereturnadd_adjestment3(Request $request)
    {
        try {

            if (count($request->adj_paid) > 0) {
                for ($i = 0; $i < count($request->adj_paid); $i++) {
                    SysPurchaseReturnAdjestment::where(['pri_no' => $request->adj_pri_no])->delete();
                }
                $adj_doc_date = $request->adj_doc_date;
                $adj_pi_no = $request->adj_pi_no;
                $adj_total = $request->adj_total;
                $adj_paid = $request->adj_paid;
                $adj_balance = $request->adj_balance;

                for ($i = 0; $i < count($request->adj_paid); $i++) {
                    if ($adj_paid[$i] != "" && $adj_paid[$i] != 0) {
                        $data = [
                            'pri_no' => $request->adj_pri_no,
                            'lpo_no' => $request->adj_lpo_no,
                            'piv_no' => $adj_pi_no[$i],
                            'doc_date' => date('Y-m-d', strtotime($request->doc_date)),
                            'total_amount' => $adj_total[$i],
                            'paid_amount' => $adj_paid[$i],
                            'balance_amount' => $adj_total[$i] - $adj_paid[$i],
                            'status' => 5,
                            'created_by' => Auth::user()->id,
                        ];
                        SysPurchaseReturnAdjestment::insert($data);
                    }
                }
            }

            $pri_adjestment = SysPurchaseInvoice::select('sys_purchase_invoice.doc_number as doc_number', 'sys_purchase_invoice.pi_date as doc_date', 'cat.credit_amount as total_amount', DB::raw('sum(adj.paid_amount) as paid_amount'))
                ->join('sys_chartofaccounts_transaction as cat', 'cat.transaction_no', 'sys_purchase_invoice.doc_number')
                ->leftjoin('sys_purchase_return_adjestment as adj', 'adj.piv_no', 'sys_purchase_invoice.doc_number')
                ->where('sys_purchase_invoice.vendors', $request->id)
                ->where('account_id', $request->id)->where('cat.company_id', session('logged_session_data.company_id'))
                ->groupby('sys_purchase_invoice.doc_number', 'sys_purchase_invoice.pi_date', 'cat.credit_amount')
                ->orderby('sys_purchase_invoice.doc_number', 'asc')
                ->get();
            return json_encode(array('data' => $pri_adjestment));

        } catch (\Exception $e) {
            //$ret = 'ERROR';
            $ret = $e;
            return json_encode(array('data' => $ret));
        }
    }

    public function get_pi_list(Request $request)
    {
        try {
            $ret = SysPurchaseInvoice::select('id', 'doc_number')->where('vendors', $request->id)->wherein('return_status', [0, 2])->where('status', 1)->where('company_id', session('logged_session_data.company_id'))->get();
            return json_encode(array('data' => $ret));

        } catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }

    public function get_pi_list_for_pi_return(Request $request)
    {
        try {
            $ret = DB::table('sys_purchase_invoice_items as pi_items')->select('pi_items.*', 'pi.doc_number', 'pi.pi_date', 'pi.currency', 'pi.awbno', 'pi.lpo_number', 'pi.lpo_date', 'pi.bill_number', 'pi.bill_date', 'pi.payment_terms', 'pi.payment_terms2', 'pi.reference', 'pi.location', 'pi.warehouse', 'pi.salesman_name', 'pi.narration', 'deals.code', 'pi.ref_po_id', 'pi.ref_grn_id', 'items.part_number as part_number_txt', 'items.description', 'items.product_type', 'pi_items.sort_id')
                ->join('sys_purchase_invoice as pi', 'pi.id', 'pi_items.pi_id')
                ->join('sm_items as items', 'items.id', 'pi_items.part_number')
                ->leftjoin('sys_crm_deals as deals', 'deals.id', 'pi.deal_id')
                ->where('pi_items.qty', '!=', DB::raw('pi_items.return_qty'))
                ->where('pi_items.pi_id', $request->pi_id)->where('pi.status', 1)->orderby('pi_items.sort_id')->get();
            return response()->json([$ret]);
        } catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }

    public function store(Request $request)
    {
        
       
        $input = $request->all();
        //return $input;

        $cart = SysPurchaseReturnListCart::select('sys_purchase_return_list_cart.*', 'sm_items.part_number AS partno', 'sm_items.description')
            ->join('sm_items', 'sm_items.id', 'sys_purchase_return_list_cart.part_number')
            ->where('cart_id', session('logged_session_data.cart_id'))->get();

        if ($request->part_number[0] != "none" && $request->qty[0] != "" && $request->unitprice[0] != "") {

        } elseif (count($cart) > 0) {

        } else {
            Toastr::error('Items not found', 'Failed');
            return redirect()->back();
        }

        try {
            DB::beginTransaction();
            $pr = new SysPurchaseReturn();
            $pr->doc_number = SysHelper::get_new_code('sys_purchase_return', 'PR', 'doc_number');
            $pr->doc_date = $request->filled('doc_date')
                ? Carbon::createFromFormat('d/m/Y', $request->doc_date)->format('Y-m-d')
                : null;

            if ($request->po_id != "") {
                $pr->ref_po_id = $request->po_id;
            }
            if ($request->grn_id != "") {
                $pr->ref_grn_id = $request->grn_id;
            }
            if ($request->pi_id != "") {
                $pr->pi_id = $request->pi_id;
            }
            $pr->pi_number = $request->pi_number;
            $pr->pi_date = Carbon::createFromFormat('d/m/Y', $request->pi_date)->format('Y-m-d');
            $pr->vendors = $request->vendors;
            $pr->currency = $request->currency;
            $pr->lpo_number = $request->lpo_number;
            $pr->lpo_date = Carbon::createFromFormat('d/m/Y', $request->lpo_date)->format('Y-m-d');
            $pr->bill_number = $request->bill_number;
            $pr->bill_date = Carbon::createFromFormat('d/m/Y', $request->bill_date)->format('Y-m-d');
            $pr->payment_terms = $request->payment_terms;
            $pr->payment_terms2 = $request->payment_terms2;
            $pr->awbno = $request->awbno;
            $pr->reference = $request->reference;
            $pr->warehouse = $request->warehouse;
            $pr->grn_no = $request->grn_no;
            $pr->grn_date = $request->grn_date;
            $pr->sales_person = $request->sales_person;
            $pr->narration = $request->narration;
            $pr->deal_id = SysHelper::get_dealid_from_code($request->deal_id);
            $pr->status = 1;
            $pr->created_by = Auth::user()->id;
            $pr->company_id = session('logged_session_data.company_id');
            $results = $pr->save();
            $pr->toArray();

            if ((count(array_filter($request->part_number)) > 0 && count(array_filter($request->qty)) > 0 && count(array_filter($request->unitprice)) > 0)) {
                $total_tax_amount = array_sum($request->taxableamount);
                $total_vat_amount = array_sum($request->vatamount);
            }
            if (count($cart) > 0) {
                $total_tax_amount = $cart->sum('taxableamount');
                $total_vat_amount = $cart->sum('vatamount');
            }

            if ((count(array_filter($request->part_number)) > 0 && count(array_filter($request->qty)) > 0 && count(array_filter($request->unitprice)) > 0)) {
                for ($i = 0; $i < count($request->part_number); $i++) {
                    if ($request->part_number[$i] != "") {
                        $prl = new SysPurchaseReturnList();
                        $prl->pr_id = $pr->id;
                        $prl->pi_id_ref = $request->pi_id;
                        $prl->partno = $request->part_number[$i];
                        $prl->qty = $request->qty[$i];
                        $prl->unitprice = $request->unitprice[$i];
                        $prl->value = $request->value[$i];
                        $prl->discount = $request->discount[$i];
                        $prl->taxableamount = $request->taxableamount[$i];
                        $prl->vat = $request->tax[$i];
                        $prl->vatamount = $request->vatamount[$i];
                        $prl->sort_id = $request->sort_id[$i];
                        $prl->remarks = "";
                        $prl->serialno = "";
                        $prl->status = 1;
                        $prl->created_by = Auth::user()->id;
                        $prl->save();

                        $str_arr = explode(",", $request->serial_no[$i]);
                        /*$str_arr = collect(preg_split('/[\s,]+/', $request->srl[$i], -1, PREG_SPLIT_NO_EMPTY))
                        ->map(fn($s) => strtoupper(trim($s)))->unique()->values()->toArray();*/
                        if ($str_arr[0] != "") {
                            foreach ($str_arr as $srl) {
                                $values = array('prt_id' => $pr->id, 'part_no' => $request->part_number[$i], 'srl_no' => $srl);
                                DB::table('sys_purchase_return_items_srlno')->insert($values);
                            }
                        }

                        // $ret = SysPurchaseReturnItemsSrlnoCart::select('srl_no')->where('session_id',session('logged_session_data.cart_id'))->where('part_no',$request->part_id[$i])->get();
                        // if(count($ret)>0){
                        //     foreach($ret as $sl){
                        //         DB::table('sys_purchase_return_items_srlno')->insert(
                        //             [
                        //                 'prt_id' => $pr->id,
                        //                 'piv_id' => $pr->pi_id,
                        //                 'part_no' => $request->part_id[$i],
                        //                 'srl_no' => $sl->srl_no,
                        //             ]
                        //             );
                        //             SysPurchaseReturnItemsSrlnoCart::where('session_id',session('logged_session_data.cart_id'))->where('part_no',$request->part_id[$i])->delete();
                        //     }
                        // }

                        db::table('sys_purchase_invoice_items')->where('pi_id', $request->pi_id)->where('part_number', $request->part_number[$i])->update(['return_qty' => $request->qty[$i]]);

                        $discount = ($request->discount[$i] === '' ? '0.00' : $request->discount[$i]);
                        $istock = new SysItemStock();
                        $istock->pri_id = $pr->id;
                        $istock->account_id = $request->vendors;
                        $istock->partno = $request->part_number[$i];
                        $istock->qty_out = $request->qty[$i];
                        $istock->price_out = ($request->value[$i] - $discount) / $request->qty[$i];
                        $istock->refno = $pr->pi_number;
                        $istock->doc_number = $pr->doc_number;
                        $istock->doc_date = $pr->doc_date;
                        $istock->deal_id = $pr->deal_id;
                        $istock->slno = $request->serial_no[$i];
                        $istock->status = 1;
                        $istock->created_by = Auth::user()->id;
                        $istock->company_id = session('logged_session_data.company_id');
                        $istock->currency_id = $request->currency;
                        $istock->sales_person = $request->sales_person;
                        $istock->save();
                        $key_item = SysPurchaseGrnLicenseKey::where('item_id', $request->part_number[$i])->where('purchase_return_id', -1)->where('cart_id', session('logged_session_data.cart_id'))->where('company_id', session('logged_session_data.company_id'))->get();


                        if (count($key_item) > 0) {
                            foreach ($key_item as $k) {
                                SysHelper::set_license_key_trn(5, $pr->id, $pr->doc_date, $pr->doc_number, $k->id, $k->item_id, $k->license_key, $k->exp_date);
                                SysPurchaseGrnLicenseKey::where('item_id', $request->part_number[$i])->where('license_key', $k->license_key)->where('status', 1)->where('purchase_return_id', -1)->where('company_id', session('logged_session_data.company_id'))->update(['status' => 2, 'purchase_return_id' => $pr->id, 'updated_by' => Auth::user()->id, 'updated_at' => Carbon::now('+04:00')]);
                            }
                        }

                    }
                }
            }
            if (count($cart) > 0) {
                //for($i = 0; $i < count($cart); $i++) {
                foreach ($cart as $dt) {
                    if ($dt->part_number != "" && $dt->qty != "" && $dt->unitprice != "") {
                        $sii = new SysPurchaseReturnList();
                        $sii->pr_id = $pr->id;
                        $sii->partno = $dt->part_number;
                        $sii->serialno = $dt->serial_no;
                        $sii->vat = $dt->tax;
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
                            $values = array('prt_id' => $pr->id, 'part_no' => $dt->part_number, 'srl_no' => $srl);
                            DB::table('sys_purchase_return_items_srlno')->insert($values);
                        }

                        $discount = $dt->discount;
                        $istock = new SysItemStock();
                        $istock->slr_id = $pr->id;
                        $istock->account_id = $request->vendors;
                        $istock->partno = $dt->part_number;
                        $istock->qty_out = $dt->qty;
                        $istock->price_out = ($dt->value - $discount) / $dt->qty;
                        $istock->refno = $request->dn_doc_number;
                        $istock->doc_number = $pr->doc_number;
                        $istock->doc_date = $pr->doc_date;
                        $istock->deal_id = $pr->deal_id;
                        $istock->slno = $dt->serial_no;
                        $istock->status = 1;
                        $istock->created_by = Auth::user()->id;
                        $istock->company_id = session('logged_session_data.company_id');
                        $istock->currency_id = $request->currency;
                        $istock->sales_person = $request->sales_person;
                        $istock->save();

                    }
                }
                SysPurchaseReturnListCart::where('cart_id', session('logged_session_data.cart_id'))->delete();
            }

            //Supplier account cr
            SysHelper::trn_chartof_accounts_transaction($request->vendors, $pr->id, $pr->doc_number, $pr->doc_date, 'purchasereturn', ($total_tax_amount + $total_vat_amount), '0.00', '', 1, 0, $request->pi_number, 1);

            //Purchase account dr 
            $purchase_account_id = SysHelper::get_purchase_return_account_id();
            SysHelper::trn_chartof_accounts_transaction($purchase_account_id, $pr->id, $pr->doc_number, $pr->doc_date, 'purchasereturn', '0.00', ($total_tax_amount), '', 1, 0, $request->pi_number, 1);

            //vat account dr 
            $purchase_vat_account_id = SysHelper::get_purchase_vat_account_id();
            SysHelper::trn_chartof_accounts_transaction($purchase_vat_account_id, $pr->id, $pr->doc_number, $pr->doc_date, 'purchasereturn', '0.00', ($total_vat_amount), '', 1, 0, $request->pi_number, 1);

            $return_status = db::table('sys_purchase_invoice_items')->select(DB::raw('sum(qty) as pi_qty'), DB::raw('sum(return_qty) as pr_qty'))->where('pi_id', $request->pi_id)->first();

            if ($request->pi_id != "") {
                if ($return_status->pi_qty == $return_status->pr_qty) {
                    db::table('sys_purchase_invoice')->where('id', $request->pi_id)->update(['return_status' => 1]); // pi fully return
                } else {
                    db::table('sys_purchase_invoice')->where('id', $request->pi_id)->update(['return_status' => 2]); // pi half return
                }
            }

            SysPurchaseReturnAdjestment::where('pri_no', $pr->doc_number)->where('status', 5)->update(['status' => 1]);

            DB::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();

        } catch (\Exception $e) {
            DB::rollBack();
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function edit(Request $request, $id)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];

            $currency = SysCurrencySettings::select('id', 'code', 'ex_rate')->get();
            $paymentterms = SysPaymentTerms::select('id', 'title')->orderby('title', 'asc')->get();


            $customs_freight_account = SysHelper::get_customs_freight_accounts_for_purchase($company_id);

            $suppliertype = SysSupplierType::orderby('title', 'asc')->get();
            $purchasetype = SysPurchaseType::orderby('title', 'asc')->get();
            $countries = SysCountries::orderby('name', 'asc')->get();
            $states = SysStates::orderby('name', 'asc')->get();
            $salesman = SysHelper::get_sales_persons();

            $edit = SysPurchaseReturn::find($id);

            $vendors = SysChartofAccounts::select('id', 'account_name')->where('id', $edit->vendors)->get();

            $currencylist2 = DB::table('sys_currency_rate as r')->select('r.id', 'r.from_currency', 'r.to_currency', 'c.code', 'r.rate')
                ->join('sys_currency as c', 'c.id', 'r.to_currency')
                ->where('r.status', 1)->where('r.from_currency', $edit->currency)
                ->orderBy('c.code', 'ASC')->get();

            $editList = SysPurchaseReturnList::where('pr_id', $id)->orderby('sort_id')->get();
            $invoice_amount = SysChartofAccountsTransaction::where('transaction_no', $edit->pi_number)->where('transaction_type', 'purchaseinvoice')->where('account_id', $edit->vendors)->sum('credit_amount');
            //$pri_adjestment = SysPurchaseReturnAdjestment::where('piv_no',$edit->pi_number)->get();

            $pri_adjestment = SysPurchaseInvoice::select('sys_purchase_invoice.doc_number as piv_no', 'sys_purchase_invoice.pi_date as doc_date', 'cat.credit_amount as total_amount', DB::raw('sum(adj.paid_amount) as paid_amount'))
                ->join('sys_chartofaccounts_transaction as cat', 'cat.transaction_no', 'sys_purchase_invoice.doc_number')
                ->leftjoin('sys_purchase_return_adjestment as adj', 'adj.piv_no', 'sys_purchase_invoice.doc_number')
                ->where('sys_purchase_invoice.vendors', $edit->vendors)
                ->where('account_id', $edit->vendors)->where('cat.company_id', session('logged_session_data.company_id'))
                ->groupby('sys_purchase_invoice.doc_number', 'sys_purchase_invoice.pi_date', 'cat.credit_amount')
                ->get();

            $query = SysPurchaseReturn::select(DB::raw('sys_purchase_return.*, (SELECT GROUP_CONCAT(doc_number) as doc_number FROM sys_purchase_grn WHERE lpo_number = sys_purchase_return.lpo_number) AS grnno, (SELECT GROUP_CONCAT(doc_number) FROM sys_purchase_order WHERE id = sys_purchase_return.ref_po_id) AS po_no, (SELECT SUM(taxableamount)+SUM(vatamount) AS amount FROM sys_purchase_return_list WHERE pr_id = sys_purchase_return.id) AS amount, (SELECT GROUP_CONCAT(code) FROM sys_crm_deals WHERE id=sys_purchase_return.deal_id) AS code'));
            $query->wherein('company_id', $company_id);
            $query->orderby('doc_number', 'desc');
            $purchasereturn = $query->paginate(50);

            //return $pri_adjestment;

            return view('backEnd.purchasereturn.purchasereturnedit', compact('purchasereturn', 'currency', 'currencylist2', 'vendors', 'paymentterms', 'suppliertype', 'purchasetype', 'countries', 'states', 'customs_freight_account', 'edit', 'editList', 'invoice_amount', 'pri_adjestment', 'salesman'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function view(Request $request, $id)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];

            $currency = SysCurrencySettings::select('id', 'code')->get();
            $paymentterms = SysPaymentTerms::select('id', 'title')->orderby('title', 'asc')->get();

            $vendors = SysHelper::get_supplier_list_all($company_id);

            $customs_freight_account = SysHelper::get_customs_freight_accounts_for_purchase($company_id);

            $suppliertype = SysSupplierType::orderby('title', 'asc')->get();
            $purchasetype = SysPurchaseType::orderby('title', 'asc')->get();
            $countries = SysCountries::orderby('name', 'asc')->get();
            $states = SysStates::orderby('name', 'asc')->get();
            $salesman = SysHelper::get_sales_persons();

            $edit = SysPurchaseReturn::find($id);
            $editList = SysPurchaseReturnList::where('pr_id', $id)->get();
            $invoice_amount = SysChartofAccountsTransaction::where('transaction_no', $edit->pi_number)->where('transaction_type', 'purchaseinvoice')->where('account_id', $edit->vendors)->sum('credit_amount');
            $pri_adjestment = SysPurchaseReturnAdjestment::where('piv_no', $edit->pi_number)->get();

            return view('backEnd.purchasereturn.purchasereturnview', compact('currency', 'vendors', 'paymentterms', 'suppliertype', 'purchasetype', 'countries', 'states', 'customs_freight_account', 'edit', 'editList', 'invoice_amount', 'pri_adjestment', 'salesman'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function update(Request $request, $id)
    {
       
        try {
          
            DB::beginTransaction();
            $pr = SysPurchaseReturn::find($id);

           
            $pr->doc_date = $request->filled('doc_date')
                ? Carbon::createFromFormat('d/m/Y', $request->doc_date)->format('Y-m-d')
                : null;
  dd($request->all());
            $pr->pi_id = $request->pi_id;
            $pr->pi_number = $request->pi_number;
            $pr->pi_date = $request->pi_date;
            $pr->vendors = $request->vendors;
            $pr->currency = $request->currency;
            $pr->lpo_number = $request->lpo_number;
            $pr->lpo_date = Carbon::createFromFormat('d/m/Y', $request->lpo_date)->format('Y-m-d');
            $pr->bill_number = $request->bill_number;
            $pr->bill_date =  Carbon::createFromFormat('d/m/Y', $request->bill_date)->format('Y-m-d');
            $pr->payment_terms = $request->payment_terms;
            $pr->payment_terms2 = $request->payment_terms2;
            $pr->awbno = $request->awbno;
            $pr->reference = $request->reference;
            $pr->warehouse = $request->warehouse;
            $pr->grn_no = $request->grn_no;
            //$pr->grn_date = $request->grn_date;
            $pr->sales_person = $request->sales_person;
            $pr->narration = $request->narration;
            $pr->deal_id = SysHelper::get_dealid_from_code($request->deal_id);
            $pr->status = 1;
            $pr->updated_by = Auth::user()->id;
            $pr->company_id = session('logged_session_data.company_id');
            $results = $pr->save();
            $pr->toArray();

            $total_tax_amount = 0;
            $total_vat_amount = 0;

            DB::table('sys_purchase_return_list')->where('pr_id', $id)->delete();
            DB::table('sys_item_stock')->where('pri_id', $id)->delete();

            for ($i = 0; $i < count($request->part_number); $i++) {

                if ($request->part_number[$i] != "" && $request->qty[$i] != "" && $request->unitprice[$i] != "") {
                    $sii = new SysPurchaseReturnList();
                    $sii->pr_id = $pr->id;
                    $sii->partno = $request->part_number[$i];
                    $sii->serialno = $request->serial_no[$i];
                    $sii->vat = $request->tax[$i];
                    $sii->qty = $request->qty[$i];
                    $sii->unitprice = $request->unitprice[$i];
                    $sii->value = $request->value[$i];
                    $sii->discount = $request->discount[$i];
                    $sii->taxableamount = $request->taxableamount[$i];
                    $sii->vatamount = $request->vatamount[$i];
                    $sii->status = 1;
                    $sii->created_by = Auth::user()->id;
                    $sii->save();

                    $str_arr = explode(",", $request->serial_no[$i]);
                    foreach ($str_arr as $srl) {
                        $values = array('prt_id' => $pr->id, 'part_no' => $request->part_number[$i], 'srl_no' => $srl);
                        DB::table('sys_purchase_return_items_srlno')->insert($values);
                    }

                    $discount = $request->discount[$i];
                    $istock = new SysItemStock();
                    $istock->slr_id = $pr->id;
                    $istock->account_id = $request->vendors;
                    $istock->partno = $request->part_number[$i];
                    $istock->qty_out = $request->qty[$i];
                    $istock->price_out = ($request->value[$i] - $discount) / $request->qty[$i];
                    //$istock->refno = $request->dn_doc_number[$i];
                    $istock->doc_number = $pr->doc_number;
                    $istock->doc_date = $pr->doc_date;
                    $istock->deal_id = $pr->deal_id;
                    $istock->slno = $request->serial_no[$i];
                    $istock->status = 1;
                    $istock->created_by = Auth::user()->id;
                    $istock->company_id = session('logged_session_data.company_id');
                    $istock->currency_id = $request->currency;
                    $istock->sales_person = $request->sales_person;
                    $istock->save();

                }

                $taxableamount = (($request->unitprice[$i] * $request->qty[$i]) - $request->discount[$i]);
                $vatamount = (($request->unitprice[$i] * $request->qty[$i]) - $request->discount[$i]) * $request->tax[$i] / 100;

                $total_tax_amount += $taxableamount;
                $total_vat_amount += $vatamount;
            }

            DB::table('sys_chartofaccounts_transaction')->where('transaction_type', 'purchasereturn')->where('transaction_id', $request->id)->delete();

            //Supplier account cr
            SysHelper::trn_chartof_accounts_transaction($request->vendors, $pr->id, $pr->doc_number, $pr->doc_date, 'purchasereturn', ($total_tax_amount + $total_vat_amount), '0.00', '', 1, 0, "", 1);

            //Purchase account dr 
            $purchase_account_id = SysHelper::get_purchase_return_account_id();
            SysHelper::trn_chartof_accounts_transaction($purchase_account_id, $pr->id, $pr->doc_number, $pr->doc_date, 'purchasereturn', '0.00', ($total_tax_amount), '', 1, 0, "", 1);

            //vat account dr 
            $purchase_vat_account_id = SysHelper::get_purchase_vat_account_id();
            SysHelper::trn_chartof_accounts_transaction($purchase_vat_account_id, $pr->id, $pr->doc_number, $pr->doc_date, 'purchasereturn', '0.00', ($total_vat_amount), '', 1, 0, "", 1);


            $return_status = db::table('sys_purchase_invoice_items')->select(DB::raw('sum(qty) as pi_qty'), DB::raw('sum(return_qty) as pr_qty'))->where('pi_id', $request->pi_id)->first();

            if ($return_status->pi_qty == $return_status->pr_qty) {
                db::table('sys_purchase_invoice')->where('id', $request->pi_id)->update(['return_status' => 1]); // pi fully return
            } else {
                db::table('sys_purchase_invoice')->where('id', $request->pi_id)->update(['return_status' => 2]); // pi half return
            }


            DB::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect('purchase-return/' . $id . '/edit');

        } catch (\Exception $e) {
            return $e;
            DB::rollback();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    function addserialno(Request $request)
    {
        try {
            $check = SysPurchaseReturnItemsSrlnoCart::where('session_id', session('logged_session_data.cart_id'))->where('part_no', $request->part_no)->get();
            if (count($check) >= $request->qty) {
                $ret = 'QTYERROR';
                return json_encode(array('data' => $ret));
            }

            DB::table('sys_purchase_return_items_srlno_cart')->insert(
                [
                    'session_id' => session('logged_session_data.cart_id'),
                    'piv_id' => $request->piv_id,
                    'part_no' => $request->part_no,
                    'part_number' => $request->part_number,
                    'srl_no' => $request->srl_no,
                ]
            );
            $ret = SysPurchaseReturnItemsSrlnoCart::where('session_id', session('logged_session_data.cart_id'))->where('part_no', $request->part_no)->get();
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
    function getserialno(Request $request)
    {
        try {
            $ret = SysPurchaseReturnItemsSrlnoCart::where('session_id', session('logged_session_data.cart_id'))->where('part_no', $request->part_no)->get();
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

    //normal pr cart
    function addpurchasereturnitemscart(Request $request)
    {
        try {
            DB::table('sys_purchase_return_list_cart')->insert(
                [
                    'cart_id' => session('logged_session_data.cart_id'),
                    'part_number' => $request->part_number,
                    'vat' => $request->tax,
                    'qty' => $request->qty,
                    'unitprice' => $request->unitprice,
                    'value' => $request->value,
                    'discount' => $request->discount,
                    'taxableamount' => $request->taxableamount,
                    'vatamount' => $request->vatamount,
                    'serialno' => $request->serial_no,
                    'sort_id' => $request->sort_id,
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                ]
            );
            $ret = SysPurchaseReturnListCart::select('sys_purchase_return_list_cart.*', 'sm_items.part_number AS partno', 'sm_items.description')
                ->join('sm_items', 'sm_items.id', 'sys_purchase_return_list_cart.part_number')
                ->where('cart_id', session('logged_session_data.cart_id'))->orderby('sort_id')->get();
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

    function updatepurchasereturnitemscart(Request $request)
    {
        try {
            DB::table('sys_purchase_return_list_cart')->where('id', $request->itm_id)->update([
                'part_number' => $request->part_number,
                'vat' => $request->tax,
                'qty' => $request->qty,
                'unitprice' => $request->unitprice,
                'value' => $request->value,
                'discount' => $request->discount,
                'taxableamount' => $request->taxableamount,
                'vatamount' => $request->vatamount,
                'serialno' => $request->serial_no,
                'sort_id' => $request->sort_id,
            ]);

            $ret = SysPurchaseReturnListCart::select('sys_purchase_return_list_cart.*', 'sm_items.part_number AS partno', 'sm_items.description')
                ->join('sm_items', 'sm_items.id', 'sys_purchase_return_list_cart.part_number')
                ->where('cart_id', session('logged_session_data.cart_id'))->orderby('sort_id')->get();

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

    function deletepurchasereturnitemscart(Request $request)
    {
        try {
            DB::table('sys_purchase_return_list_cart')->where('id', $request->id)->delete();
            $ret = SysPurchaseReturnListCart::select('sys_purchase_return_list_cart.*', 'sm_items.part_number AS partno', 'sm_items.description')
                ->join('sm_items', 'sm_items.id', 'sys_purchase_return_list_cart.part_number')
                ->where('cart_id', session('logged_session_data.cart_id'))->orderby('sort_id')->get();

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

    public function delete(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $no = DB::table('sys_purchase_return')->where('id', $id)->first();
            DB::table('sys_purchase_return')->where('id', $id)->update(['status' => 2]);
            DB::table('sys_purchase_return_list')->where('pr_id', $id)->update(['status' => 2]);
            DB::table('sys_purchase_return_adjestment')->where('pri_no', $no->doc_number)->update(['status' => 2]);
            DB::table('sys_purchase_return_items_srlno')->where('prt_id', $id)->update(['status' => 2]);
            DB::table('sys_item_stock')->where('pri_id', $id)->update(['status' => 2]);
            DB::table('sys_chartofaccounts_transaction')->where('transaction_type', 'purchasereturn')->where('transaction_id', $id)->update(['status' => 2]);
            DB::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();

        } catch (\Throwable $th) {
            DB::rollback();
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function restore(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $no = DB::table('sys_purchase_return')->where('id', $id)->first();
            DB::table('sys_purchase_return')->where('id', $id)->update(['status' => 1]);
            DB::table('sys_purchase_return_list')->where('pr_id', $id)->update(['status' => 1]);
            DB::table('sys_purchase_return_adjestment')->where('pri_no', $no->doc_number)->update(['status' => 1]);
            DB::table('sys_purchase_return_items_srlno')->where('prt_id', $id)->update(['status' => 1]);
            DB::table('sys_item_stock')->where('pri_id', $id)->update(['status' => 1]);
            DB::table('sys_chartofaccounts_transaction')->where('transaction_type', 'purchasereturn')->where('transaction_id', $id)->update(['status' => 1]);
            DB::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();

        } catch (\Throwable $th) {
            DB::rollback();
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    function purchasereturnupdate_currency(Request $request)
    {
        try {
            if ($request->to_currency_id != $request->from_currency_id) {

                $to_currency = SysCurrencyRate::where('id', $request->to_currency_id)->value('to_currency');
                SysPurchaseReturn::where('id', $request->cur_pr_id)->update(['currency' => $to_currency]);
                $ca = SysChartofAccountsTransaction::where('transaction_id', $request->cur_pr_id)->where('transaction_type', 'purchasereturn')->get();
                $qt = SysPurchaseReturnList::where('pr_id', $request->cur_pr_id)->get();
                foreach ($qt as $t) {
                    $new_price = $t->unitprice * $request->to_currency_rate;
                    $new_discount = $t->discount * $request->to_currency_rate;

                    SysPurchaseReturnList::where('id', $t->id)->update(
                        [
                            'unitprice' => $new_price,
                            'value' => $new_price * $t->qty,
                            'discount' => $new_discount,
                            'taxableamount' => ($new_price * $t->qty) - $new_discount,
                            'vatamount' => (($new_price * $t->qty) - $new_discount) * $t->tax / 100,
                        ]
                    );

                    SysItemStock::where('doc_number', $request->cur_pr_doc_no)->where('partno', $t->partno)->update(
                        ['price_out' => ($new_price * $t->qty) - $new_discount, 'currency_id' => $to_currency]
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
    public function delete_adjustment($id)
    {
        try {
            DB::table('sys_purchase_return_adjestment')->where('id', $id)->delete();

            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
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
}
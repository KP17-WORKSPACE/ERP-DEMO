<?php

namespace App\Http\Controllers;

use App\SmItem;
use App\SmStaff;
use App\SmSupplier;
use App\SysCompany;
use App\SysPurchaseOrder;
use App\SysPurchaseOrderItems;
use App\SysPurchaseOrderAttachment;
use App\SmQuotation;
use App\SysCurrencySettings;
use App\SysPaymentTerms;
use App\SysShipping;
use App\SmGeneralSettings;
use App\SmQuotationProducts;
use App\ApiBaseMethod;
use App\SysAppTabs;
use App\SmInspectingDepartment;
use App\SysCustomer;
use App\SysSupplierType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Brian2694\Toastr\Facades\Toastr;
//use Barryvdh\DomPDF\PDF;
use Barryvdh\DomPDF\Facade as PDF;


use App\Role;
use App\SysChartofAccounts;
use App\SysChartofAccountsTransaction;
use App\SysCountries;
use App\SysCrmDeals;
use App\SysCrmDealTrack;
use App\SysCrmQuoteItems;
use App\SysCurrency;
use App\SysCurrencyRate;
use App\SysCustSuppl;
use App\SysCustSupplAddressbook;
use App\SysDealPurchaseOrderCount;
use App\SysDealPurchaseOrderItems;
use App\SysDealPurchaseOrderItemsCart;
use App\SysHelper;
use App\SysItemStock;
use App\SysPayment;
use App\SysPurchaseAuto;
use App\SysPurchaseGRN;
use App\SysPurchaseGRNItems;
use App\SysPurchaseInvoice;
use App\SysPurchaseInvoiceItems;
use App\SysPurchaseOrderItemsCart;
use App\SysPurchaseReturn;
use App\SysPurchaseReturnAdjestment;
use App\SysPurchaseType;
use App\SysSalesInvoice;
use App\SysStates;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;


use function GuzzleHttp\Promise\exception_for;


class SysPurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $id = null)
    {

        //return session_id();
        try {
            $filter_by = "";
            $ctrl_date = "";
            $ctrl_date2 = "";
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $supplier_list = SysHelper::get_supplier_list($company_id);
            $pending_grn = 0;
            $pending_pi = 0;
            $pending_pr = 0;
            $currency = SysCurrencySettings::select('id', 'code', 'ex_rate')->get();
            $flt_documents_number = "";
            $flt_supplier = "";
            $flt_customer = "";
            $flt_dealno = "";
            $flt_currency = "";
            $flt_grnno = "";
            $flt_purchase_invoice_no = "";
            $flt_purchase_return_no = "";
            $flt_attachments = "";

            DB::statement("UPDATE sys_purchase_grn g JOIN sys_purchase_order p ON p.doc_number = g.lpo_number SET g.po_id = p.id WHERE g.po_id = 0");
            DB::statement("UPDATE sys_purchase_invoice i JOIN sys_purchase_order p ON p.doc_number = i.lpo_number SET i.ref_po_id = p.id WHERE i.ref_po_id = 0");

            // $query = SysPurchaseOrder::select(DB::raw('sys_purchase_order.*, (SELECT max(doc_number) FROM sys_purchase_grn WHERE po_id=sys_purchase_order.id) AS grn_no, (SELECT max(doc_number) FROM sys_purchase_invoice WHERE ref_po_id=sys_purchase_order.id) AS piv_no, (SELECT max(doc_number) FROM sys_purchase_return WHERE ref_po_id=sys_purchase_order.id) AS prt_no, (SELECT SUM(taxableamount)+SUM(vatamount) AS amount FROM sys_purchase_order_items where po_id = sys_purchase_order.id) AS amount, (SELECT max(code) FROM sys_crm_deals WHERE id=sys_purchase_order.deal_id) AS code'));
            $query = SysPurchaseOrder::select(DB::raw('
    sys_purchase_order.*, (SELECT GROUP_CONCAT(doc_file) FROM sys_purchase_order_att WHERE doc_id = sys_purchase_order.id) AS attach,
    (SELECT GROUP_CONCAT(doc_number) FROM sys_purchase_grn WHERE po_id=sys_purchase_order.id) AS grn_no,
    (SELECT GROUP_CONCAT(doc_number) FROM sys_purchase_invoice WHERE ref_po_id=sys_purchase_order.id) AS piv_no,
    (SELECT GROUP_CONCAT(doc_number) FROM sys_purchase_return WHERE ref_po_id=sys_purchase_order.id) AS prt_no,
    (SELECT SUM(taxableamount) + SUM(vatamount) FROM sys_purchase_order_items WHERE po_id = sys_purchase_order.id) AS amount,
    (SELECT GROUP_CONCAT(code) FROM sys_crm_deals WHERE id = sys_purchase_order.deal_id) AS code
'));




            if (SysHelper::get_pagination_post($request)) {
                if ($request->documents_number != "") {
                    $query->where('doc_number', 'like', '%' . $request->documents_number . '%');
                    $flt_documents_number = $request->documents_number;
                }
                if ($request->supplier != "") {
                    $query->where('vendors', $request->supplier);
                    $flt_supplier = $request->supplier;
                }
                if ($request->customer != "") {
                    $query->where('narration', 'like', '%' . $request->customer . '%');
                    $flt_customer = $request->customer;
                }
                if ($request->deal_number != "") {
                    $query->where('deal_id', 'like', '%' . SysHelper::get_dealid_from_code($request->deal_number) . '%');
                    $flt_dealno = $request->deal_number;
                }
                if ($request->currency != "") {
                    $query->where('currency', $request->currency);
                    $flt_currency = $request->currency;
                }
                if ($request->grn_number != "") {
                    if (strtolower($request->grn_number) == "pending") {
                        $pending_grn = 1;
                        $flt_grnno = "pending";
                    } else {
                        $grn_nos = SysPurchaseOrder::join('sys_purchase_grn', 'sys_purchase_grn.po_id', 'sys_purchase_order.id')
                            ->where('sys_purchase_grn.doc_number', 'like', '%' . $request->grn_number . '%')->pluck('sys_purchase_order.doc_number');
                        $query->wherein('doc_number', $grn_nos);
                        $flt_grnno = $request->grn_number;
                    }
                }
                if ($request->purchase_invoice_number != "") {
                    if (strtolower($request->purchase_invoice_number) == "pending") {
                        $pending_pi = 1;
                        $flt_purchase_invoice_no = "pending";
                    } else {
                        $inv_nos = SysPurchaseOrder::join('sys_purchase_invoice', 'sys_purchase_invoice.ref_po_id', 'sys_purchase_order.id')
                            ->where('sys_purchase_invoice.doc_number', 'like', '%' . $request->purchase_invoice_number . '%')->pluck('sys_purchase_order.doc_number');
                        $query->wherein('doc_number', $inv_nos);
                        $flt_purchase_invoice_no = $request->purchase_invoice_number;
                    }
                }
                if ($request->purchase_return_number != "") {
                    if (strtolower($request->purchase_return_number) == "pending") {
                        $pending_pr = 1;
                        $flt_purchase_return_no = "pending";
                    } else {
                        $prt_nos = SysPurchaseOrder::join('sys_purchase_return', 'sys_purchase_return.ref_po_id', 'sys_purchase_order.id')
                            ->where('sys_purchase_return.doc_number', 'like', '%' . $request->purchase_return_number . '%')->pluck('sys_purchase_order.doc_number');
                        $query->wherein('doc_number', $prt_nos);
                        $flt_purchase_return_no = $request->purchase_return_number;

                    }
                }
                // if ($request->amount != "") {                    
                //     $amt_nos = SysChartofAccountsTransaction::where('transaction_type', 'salesreturn')->where('debit_amount',$request->amount)->pluck('transaction_no');
                //     $query->wherein('doc_number',$amt_nos);
                // }
                if ($request->from_date != "" && $request->filter_by == "") {
                    $ctrl_date = $request->from_date;
                }
                if ($request->to_date != "" && $request->filter_by == "") {
                    $ctrl_date2 = $request->to_date;
                }
                if ($request->filter_by == "this_month") {
                    $ctrl_date = date('Y-m-01');
                    $ctrl_date2 = date("Y-m-t", strtotime($ctrl_date));
                    $filter_by = 'this_month';
                }
                if ($request->filter_by == "today") {
                    $ctrl_date = date('Y-m-d');
                    $ctrl_date2 = date('Y-m-d');
                    $filter_by = 'today';
                }
                if ($request->filter_by == "this_week") {
                    $ctrl_date = date('Y-m-d', strtotime('-1 week sunday 00:00:00'));
                    $ctrl_date2 = date('Y-m-d', strtotime('saturday 23:59:59'));
                    $filter_by = 'this_week';
                }
                if ($request->filter_by == "last_week") {
                    $ctrl_date = date('Y-m-d', strtotime('-2 week sunday 00:00:00'));
                    $ctrl_date2 = date('Y-m-d', strtotime('-1 week saturday 23:59:59'));
                    $filter_by = 'last_week';
                }
                if ($request->filter_by == "last_month") {
                    $ctrl_date = date('Y-m-d', strtotime('first day of previous month'));
                    $ctrl_date2 = date('Y-m-d', strtotime('last day of previous month'));
                    $filter_by = 'last_month';
                }
                if ($request->filter_by == "this_quarter") {
                    $q_date = SysHelper::get_quarter(date('m'));
                    $ctrl_date = $q_date[0];
                    $ctrl_date2 = $q_date[1];
                    $filter_by = 'this_quarter';
                }
                if ($request->filter_by == "pre_quarter") {
                    $q_date = SysHelper::get_pre_quarter(date('m'));
                    $ctrl_date = $q_date[0];
                    $ctrl_date2 = $q_date[1];
                    $filter_by = 'pre_quarter';
                }
                if ($request->filter_by == "this_year") {
                    $ctrl_date = date('Y-01-01');
                    $ctrl_date2 = date('Y-12-31');
                    $filter_by = 'this_year';
                }
                if ($request->filter_by == "last_year") {
                    $ctrl_date = date("Y-01-01", strtotime("-1 year"));
                    $ctrl_date2 = date("Y-12-31", strtotime("-1 year"));
                    $filter_by = 'last_year';
                }



                if ($ctrl_date != "" && $ctrl_date2 != "") {
                    $query->whereBetween('po_date', [$ctrl_date, $ctrl_date2]);
                }
                if ($ctrl_date != "" && $ctrl_date2 == "") {
                    $query->where('po_date', $ctrl_date);
                }
                if ($ctrl_date == "" && $ctrl_date2 != "") {
                    $query->where('po_date', $ctrl_date2);
                }
                if ($request->attachments == 1) {
                    $att_nos = DB::table('sys_purchase_order_att')->wherein('company_id', $company_id)->pluck('doc_id');
                    $query->wherein('id', $att_nos);
                    $flt_attachments = 1;
                }
                if ($request->attachments == 2) {
                    $att_nos = DB::table('sys_purchase_order_att')->wherein('company_id', $company_id)->pluck('doc_id');
                    $query->wherenotin('id', $att_nos);
                    $flt_attachments = 2;
                }
            } else {

            }
            $query->wherein('company_id', $company_id);
            $query->orderby('doc_number', 'desc');
            $purchaseorder = $query->paginate(50);


            $vendors = $customer = $salesman = $currency = $company = $paymentterms = $items = $quotations = $departments = $shipping = $suppliertype = $purchasetype = $countries = $states = $cart = collect();



            $active_id = $id;
            $selectedPO = [];


            $action = false;
            $editData = [];


            if ($request->has('po_action')) {
                $poAction = $request->input('po_action');

                if ($poAction === 'add') {
                    $action = 'add';

                    $vendors = SysHelper::get_supplier_list($company_id);
                    $customer = SysHelper::get_customer_list($company_id);
                    $salesman = SysHelper::get_sales_persons();

                    $currency = SysCurrencySettings::select('id', 'code')->get();
                    $company = SysCompany::find(session('logged_session_data.company_id'));
                    $paymentterms = SysPaymentTerms::select('id', 'title')->orderby('title', 'asc')->get();

                    $items = SysHelper::get_product_list($company_id);
                    $quotations = SmQuotation::all();
                    $departments = SmInspectingDepartment::all();
                    $shipping = SysShipping::all();
                    $suppliertype = SysSupplierType::orderby('title', 'asc')->get();
                    $purchasetype = SysPurchaseType::orderby('title', 'asc')->get();
                    $countries = SysCountries::orderby('name', 'asc')->get();
                    $states = SysStates::orderby('name', 'asc')->get();

                    $cart = SysPurchaseOrderItemsCart::select('sys_purchase_order_items_cart.*', 'sm_items.part_number AS partno', 'sm_items.description')
                        ->join('sm_items', 'sm_items.id', 'sys_purchase_order_items_cart.part_number')
                        ->where('cart_id', session('logged_session_data.cart_id'))->get();

                } elseif ($poAction === 'edit') {
                    $action = 'edit';
                    $editData = $this->edit($active_id); // Get all data for editing
                }
            } else {
                if ($id) {
                    $selectedPO = $this->get_print_data($id);
                } else {
                    $firstRecord = $purchaseorder->first();
                    if ($firstRecord) {
                        $active_id = $firstRecord->id;
                        $selectedPO = $this->get_print_data($firstRecord->id);
                    }
                }
            }

            return view('backEnd/purchaseorder/purchase_order_list', compact('purchaseorder', 'supplier_list', 'pending_grn', 'pending_pi', 'pending_pr', 'currency', 'selectedPO', 'flt_documents_number', 'flt_supplier', 'flt_customer', 'flt_dealno', 'flt_currency', 'flt_grnno', 'flt_purchase_invoice_no', 'flt_purchase_return_no', 'flt_attachments', 'ctrl_date', 'ctrl_date2', 'filter_by', 'quotations', 'currency', 'vendors', 'items', 'departments', 'paymentterms', 'company', 'shipping', 'suppliertype', 'purchasetype', 'countries', 'states', 'cart', 'customer', 'salesman', 'action', 'active_id', 'editData'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function get_print_data($id)
    {
        try {
            $po = SysPurchaseOrder::find($id);
            //return $po;

            $m_company_name = "";
            $m_contact_name = "";
            $m_address1 = "";
            $m_address2 = "";
            $m_city = "";
            $m_state = "";
            $m_country = "";
            $m_tel = "";
            $m_mob = "";
            $m_emali = "";
            $m_trnno = "";

            $bill_company_name = "";
            $bill_contact_name = "";
            $bill_address1 = "";
            $bill_address2 = "";
            $bill_city = "";
            $bill_state = "";
            $bill_country = "";
            $bill_tel = "";
            $bill_mob = "";
            $bill_emali = "";

            $ship_company_name = "";
            $ship_contact_name = "";
            $ship_address1 = "";
            $ship_address2 = "";
            $ship_city = "";
            $ship_state = "";
            $ship_country = "";
            $ship_tel = "";
            $ship_mob = "";
            $ship_emali = "";



            if (!empty($po)) {
                $company = SysCompany::find($po->company_id);

                $bill_contact_name = $po->createdby->full_name;
                $bill_company_name = $company->company_name;
                $bill_tel = $company->telephone;
                $bill_mob = $company->mobile;
                $bill_emali = $company->email;
                $bill_trnno = $company->vat_number;
                $bill_address1 = $company->company_address;
                $bill_address2 = "";
                $bill_city = "";
                $bill_state = $company->city;
                $bill_country = $company->countryname->name;
                $ship_mob = SmStaff::select('mobile')->where('user_id', $po->created_by)->first();
                $ship_mob = $ship_mob->mobile;




                $po_item = SysPurchaseOrderItems::where('po_id', '=', $po->id)->get();

                $main_data_list = SysCustSuppl::select('sys_cust_suppl.*')->join('sys_chartofaccounts', 'sys_chartofaccounts.account_code', 'sys_cust_suppl.code')->where('sys_chartofaccounts.id', $po->vendors)->first();

                $main_address = SysCustSupplAddressbook::where('cust_suppl_id', $main_data_list->id)->orderby('set_default', 'desc')->first();


                if (isset($main_address)) {
                    $m_company_name = $main_data_list->name;
                    $m_contact_name = $main_data_list->customer_salutation . ' ' . $main_data_list->first_name . ' ' . $main_data_list->last_name;
                    $m_address1 = $main_address->address;
                    $m_address2 = $main_address->address2;
                    $m_city = $main_address->city;
                    $m_state = $main_address->statename->name;
                    $m_country = $main_address->countryname->name;
                    $m_tel = $main_data_list->contcat_number;
                    $m_mob = $main_data_list->mobile;
                    $m_emali = $main_data_list->email;
                    $m_trnno = $main_data_list->vat_number;
                }

                $sub_data_list = SysCustSuppl::select('sys_cust_suppl.*')->join('sys_chartofaccounts', 'sys_chartofaccounts.account_code', 'sys_cust_suppl.code')->where('sys_chartofaccounts.id', $po->shipping_supplier)->first();

                if (isset($sub_data_list)) {

                    $sub_address = SysCustSupplAddressbook::where('cust_suppl_id', $sub_data_list->id)->orderby('set_default', 'desc')->first();

                    //$bill_contact_name= $sub_data_list->customer_salutation.' '.$sub_data_list->first_name.' '.$sub_data_list->last_name;


                    $ship_company_name = $sub_data_list->name;
                    $ship_contact_name = $sub_data_list->customer_salutation . ' ' . $sub_data_list->first_name . ' ' . $sub_data_list->last_name;
                    $ship_address1 = $sub_address->address;
                    $ship_address2 = $sub_address->address2;
                    $ship_city = $sub_address->city;
                    try {
                        $ship_state = $sub_address->statename->name;
                    } catch (\Throwable $th) {
                        $ship_state = "";
                    }

                    $ship_country = $sub_address->countryname->name;
                    $ship_tel = $sub_data_list->contcat_number;
                    //$ship_mob= $sub_data_list->mobile;
                    $ship_emali = $sub_data_list->email;
                    $ship_trnno = $sub_data_list->vat_number;
                }



                //if($po->contact_person_name!=""){ $m_contact_name = $po->contact_person_name; }
                if ($po->contact_person_email != "") {
                    $m_emali = $po->contact_person_email;
                }
                //if($po->contact_person_telephone!=""){ $m_tel = $po->contact_person_telephone; }

                if ($po->shipping_name != "") {
                    $ship_contact_name = $po->shipping_name;
                }
                if ($po->shipping_address_1 != "") {
                    $ship_address1 = $po->shipping_address_1;
                    $ship_address2 = "";
                }
                if ($po->shipping_email != "") {
                    $ship_emali = $po->shipping_email;
                }
                if ($po->shipping_contact_no != "") {
                    $ship_tel = $po->shipping_contact_no;
                }


                $data = [
                    'po' => $po,
                    'company' => $company,
                    'po_item' => $po_item,
                    'm_company_name' => $m_company_name,
                    'm_contact_name' => $m_contact_name,
                    'm_address1' => $m_address1,
                    'm_address2' => $m_address2,
                    'm_city' => $m_city,
                    'm_state' => $m_state,
                    'm_country' => $m_country,
                    'm_tel' => $m_tel,
                    'm_mob' => $m_mob,
                    'm_emali' => $m_emali,
                    'm_trnno' => $m_trnno,

                    'bill_company_name' => $bill_company_name,
                    'bill_contact_name' => $bill_contact_name,
                    'bill_address1' => $bill_address1,
                    'bill_address2' => $bill_address2,
                    'bill_city' => $bill_city,
                    'bill_state' => $bill_state,
                    'bill_country' => $bill_country,
                    'bill_tel' => $bill_tel,
                    'bill_mob' => $bill_mob,
                    'bill_emali' => $bill_emali,
                    'bill_trnno' => $bill_trnno,

                    'ship_company_name' => $ship_company_name,
                    'ship_contact_name' => $ship_contact_name,
                    'ship_address1' => $ship_address1,
                    'ship_address2' => $ship_address2,
                    'ship_city' => $ship_city,
                    'ship_state' => $ship_state,
                    'ship_country' => $ship_country,
                    'ship_tel' => $ship_tel,
                    'ship_mob' => $ship_mob,
                    'ship_emali' => $ship_emali,
                    'ship_trnno' => $ship_trnno,

                ];


                return $data;


                //return view('backEnd.pdf_print.po_pdf', $data);
                // $pdf = PDF::loadView('backEnd.pdf_print.po_pdf', $data);
                // $pdf->setPaper('A4', 'portrait');
                // return $pdf->download($po->doc_number.'-'.$po->accountname->account_name.".pdf");
            } else {
                return [];
                //return view('web.syscom_credit_application_form');
            }

        } catch (\Throwable $th) {
            return [];
        }
    }

    public function getDetails($id)
    {
        $data = $this->get_print_data($id);

        if (!empty($data) && is_array($data)) {
            return view('backEnd/purchaseorder/po-pdf-html', $data);
        } else {
            return response("Error loading details!", 404);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create_gen(Request $request)
    {
        try {

            if (isset($request->product_id)) {
                if (count($request->product_id) > 0) {
                    foreach ($request->product_id as $pid) {
                        $qt = SysCrmQuoteItems::where('product_id', $pid)->where('deal_id', $request->req_deal_id)->first();
                        if (isset($qt)) {
                            $val = ($qt->price * $qt->qty) - $qt->discount;
                            DB::table('sys_purchase_order_items_cart')->insert(
                                [
                                    'cart_id' => session('logged_session_data.cart_id'),
                                    'part_number' => $qt->product_id,
                                    'description' => $qt->description,
                                    'tax' => ($qt->vat === '' ? '0.00' : $qt->vat),
                                    'qty' => $qt->qty,
                                    'unitprice' => $qt->price,
                                    'value' => $val,
                                    'discount' => $qt->discount,
                                    'fright' => 0,
                                    'customcharges' => 0,
                                    'taxableamount' => $val + ($val * $qt->vatamount / 100),
                                    'vatamount' => $val * $qt->vatamount / 100,
                                    'serialno' => '',
                                    'status' => 1,
                                    'created_by' => Auth::user()->id,
                                    'created_at' => Carbon::now('+04:00'),
                                ]
                            );
                        }
                    }
                }
            }

            $req_po = 0;
            $req_grn = 0;
            $req_pi = 0;
            $req_pay = 0;
            $req_mode_acc = 0;
            $req_cost = 0;
            if ($request->req_po == "") {
                $req_po = 0;
            } else {
                $req_po = 1;
            }
            if ($request->req_grn == "") {
                $req_grn = 0;
            } else {
                $req_grn = 1;
            }
            if ($request->req_pi == "") {
                $req_pi = 0;
            } else {
                $req_pi = 1;
            }
            if ($request->req_pay == "") {
                $req_pay = 0;
                $req_mode_acc = 0;
            } else {
                $req_pay = 1;
                $req_mode_acc = $request->req_mode_acc;
            }
            if ($request->req_cost == "") {
                $req_cost = 0;
            } else {
                $req_cost = 1;
            }

            $check = SysPurchaseAuto::where([
                'cart_id' => session('logged_session_data.cart_id'),
                'deal_id' => $request->req_deal_id,
                'status' => 1,
                'company_id' => session('logged_session_data.company_id'),
                'created_by' => Auth::user()->id,
            ])->get();
            if (count($check) > 0) {
                SysPurchaseAuto::where([
                    'cart_id' => session('logged_session_data.cart_id'),
                    'deal_id' => $request->req_deal_id,
                    'status' => 1,
                    'company_id' => session('logged_session_data.company_id'),
                    'created_by' => Auth::user()->id,
                ])->delete();
            }

            SysPurchaseAuto::insert(
                [
                    'cart_id' => session('logged_session_data.cart_id'),
                    'deal_id' => $request->req_deal_id,
                    'req_po' => $req_po,
                    'req_grn' => $req_grn,
                    'req_pi' => $req_pi,
                    'req_pay' => $req_pay,
                    'req_mode_acc' => $req_mode_acc,
                    'req_cost' => $req_cost,
                    'status' => 1,
                    'company_id' => session('logged_session_data.company_id'),
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                ]
            );
            return redirect('purchase-order-create-all/' . $request->req_deal_id);

        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function create_all($deal_id)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $vendors = SysHelper::get_supplier_list($company_id);
            $customer = SysHelper::get_customer_list($company_id);

            $currency = SysCurrencySettings::select('id', 'code')->get();
            $company = SysCompany::find(session('logged_session_data.company_id'));
            $paymentterms = SysPaymentTerms::select('id', 'title')->orderby('title', 'asc')->get();

            $items = SysHelper::get_product_list($company_id);
            $quotations = SmQuotation::all();
            $departments = SmInspectingDepartment::all();
            $shipping = SysShipping::all();
            $suppliertype = SysSupplierType::orderby('title', 'asc')->get();
            $purchasetype = SysPurchaseType::orderby('title', 'asc')->get();
            $countries = SysCountries::orderby('name', 'asc')->get();
            $states = SysStates::orderby('name', 'asc')->get();
            $salesman = SysHelper::get_sales_persons();

            $deal = SysCrmDeals::where('id', $deal_id)->first();
            $deal_track = SysCrmDealTrack::where('deal_id', $deal_id)->first();
            $deal_items = SysCrmQuoteItems::where('deal_id', $deal_id)->where('quote_id', $deal->quote_id)->first();

            if (isset($deal)) {
                $customer_det = SysChartofAccounts::select('sys_chartofaccounts.id')->join('sys_cust_suppl', 'sys_cust_suppl.code', 'sys_chartofaccounts.account_code')->where('sys_cust_suppl.id', $deal->cust_id)->first();
                $narration = @$deal_track->reference_no;
                $payment_terms = @$deal_track->payment_terms;
                $sales_man = @$deal->owner;

            } else {
                $narration = "";
                $payment_terms = "";
                $sales_man = "";
            }



            return view('backEnd.purchaseorder.manage_purchase_order_auto', compact('quotations', 'currency', 'vendors', 'items', 'departments', 'paymentterms', 'company', 'shipping', 'suppliertype', 'purchasetype', 'countries', 'states', 'deal_id', 'customer', 'salesman', 'payment_terms', 'sales_man', 'narration'));

        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function create2($customer_reference = null, $salesman_name = null, $deal_id = null, $deal_code = null)
    {

        try {
            $cart_qty = SysDealPurchaseOrderItems::where('created_by', Auth::user()->id)->where('deal_id', $deal_id)->where('cart_id', session('logged_session_data.cart_id'))->sum('qty');
            $po_qty = SysDealPurchaseOrderItems::where('created_by', Auth::user()->id)->where('deal_id', $deal_id)->where('cart_id', session('logged_session_data.cart_id'))->sum('po_qty');

            if ($cart_qty == $po_qty) {
                Toastr::error('Pending Items Not Found!', 'Failed');
                return redirect('purchase-order/create');
            }

            $select_cart = SysDealPurchaseOrderItems::where('created_by', Auth::user()->id)->where('deal_id', $deal_id)->where('status', 1)->where('cart_id', session('logged_session_data.cart_id'))->get();
            $data = [];
            $check_cart = SysDealPurchaseOrderItemsCart::where(['created_by' => Auth::user()->id, 'status' => 1,])->where('cart_id', session('logged_session_data.cart_id'))->count();

            if ($check_cart == 0) {
                foreach ($select_cart as $items) {
                    $qty = abs($items->qty - $items->po_qty);
                    $data[] = [
                        'cart_id' => session('logged_session_data.cart_id'),
                        'quote_item_id' => $items->quote_item_id,
                        'part_number' => $items->part_number,
                        'part_number_txt' => $items->part_number_txt,
                        'description' => $items->description,
                        'tax' => ($items->tax === '' ? '0.00' : $items->tax),
                        'qty' => $qty,
                        'deal_qty' => $items->deal_qty,
                        'po_qty' => $items->po_qty,
                        'unitprice' => $items->unitprice,
                        'value' => $items->value,
                        'discount' => $items->discount,
                        'fright' => 0,
                        'customcharges' => 0,
                        'taxableamount' => (($items->unitprice * $qty) - $items->discount),
                        'vatamount' => (($items->unitprice * $qty) - $items->discount) * $items->tax / 100,
                        'status' => 1,
                        'created_by' => Auth::user()->id,
                        'created_at' => Carbon::now('+04:00'),
                        'refid' => $items->item_id,
                        'deal_id' => $items->deal_id,
                    ];
                }
            }

            if (count($data) > 0) {
                SysDealPurchaseOrderItemsCart::where('deal_id', $items->deal_id)->delete();
                SysDealPurchaseOrderItemsCart::insert($data);
            }

            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];

            $vendors = SysHelper::get_supplier_list($company_id);

            $customer = SysHelper::get_customer_list($company_id);

            $currency = SysCurrencySettings::select('id', 'code')->get();
            $company = SysCompany::find(session('logged_session_data.company_id'));
            $paymentterms = SysPaymentTerms::select('id', 'title')->orderby('title', 'asc')->get();

            $items = SysHelper::get_product_list($company_id);
            $quotations = SmQuotation::all();
            $departments = SmInspectingDepartment::all();
            $shipping = SysShipping::all();
            $suppliertype = SysSupplierType::orderby('title', 'asc')->get();
            $purchasetype = SysPurchaseType::orderby('title', 'asc')->get();
            $countries = SysCountries::orderby('name', 'asc')->get();
            $states = SysStates::orderby('name', 'asc')->get();

            $deal = SysCrmDeals::where('id', $deal_id)->first();
            if (isset($deal)) {
                $deal_currency = $deal->deal_currency;
                $delivery_date = SysCrmDealTrack::where('deal_id', $deal->id)->value('delivery_date');
            } else {
                $deal_currency = "";
                $delivery_date = "";
            }

            $cart = SysDealPurchaseOrderItemsCart::select('sys_deal_purchase_order_items_cart.*', 'sm_items.part_number AS partno')
                ->join('sm_items', 'sm_items.id', 'sys_deal_purchase_order_items_cart.part_number')
                ->where('sys_deal_purchase_order_items_cart.created_by', Auth::user()->id)
                ->where('cart_id', session('logged_session_data.cart_id'))->get();

            $salesman = SysHelper::get_sales_persons();

            return view('backEnd.purchaseorder.manage_purchase_order_2', compact('quotations', 'currency', 'vendors', 'items', 'departments', 'paymentterms', 'company', 'shipping', 'suppliertype', 'purchasetype', 'countries', 'states', 'cart', 'customer_reference', 'salesman', 'salesman_name', 'deal_id', 'deal_code', 'customer', 'deal_currency', 'delivery_date'));

        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function create()
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $vendors = SysHelper::get_supplier_list($company_id);
            $customer = SysHelper::get_customer_list($company_id);
            $salesman = SysHelper::get_sales_persons();

            $currency = SysCurrencySettings::select('id', 'code')->get();
            $company = SysCompany::find(session('logged_session_data.company_id'));
            $paymentterms = SysPaymentTerms::select('id', 'title')->orderby('title', 'asc')->get();

            $items = SysHelper::get_product_list($company_id);
            $quotations = SmQuotation::all();
            $departments = SmInspectingDepartment::all();
            $shipping = SysShipping::all();
            $suppliertype = SysSupplierType::orderby('title', 'asc')->get();
            $purchasetype = SysPurchaseType::orderby('title', 'asc')->get();
            $countries = SysCountries::orderby('name', 'asc')->get();
            $states = SysStates::orderby('name', 'asc')->get();

            $cart = SysPurchaseOrderItemsCart::select('sys_purchase_order_items_cart.*', 'sm_items.part_number AS partno', 'sm_items.description')
                ->join('sm_items', 'sm_items.id', 'sys_purchase_order_items_cart.part_number')
                ->where('cart_id', session('logged_session_data.cart_id'))->get();

            return view('backEnd.purchaseorder.manage_purchase_order', compact('quotations', 'currency', 'vendors', 'items', 'departments', 'paymentterms', 'company', 'shipping', 'suppliertype', 'purchasetype', 'countries', 'states', 'cart', 'customer', 'salesman'));

        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }



    // create po from deal - old code not using
    function adddealitemstopurchaseordercart(Request $request)
    {
        try {
            $deal = SysCrmDeals::where('id', $request->po_deal_id)->first();
            $customer_reference = $deal->customername->name;
            $salesman_name = $deal->owner;
            $deal_id = $deal->id;
            $deal_code = $deal->code;
            $deal_items = DB::table('sys_crm_quote_items')->where('deal_id', $request->po_deal_id)->get();

            foreach ($deal_items as $items) {
                $check_cart = SysPurchaseOrderItemsCart::where([
                    'cart_id' => session('logged_session_data.cart_id'),
                    'part_number' => $items->product_id,
                ])->count();
                if ($check_cart == 0) {
                    DB::table('sys_purchase_order_items_cart')->insert([
                        'cart_id' => session('logged_session_data.cart_id'),
                        'part_number' => $items->product_id,
                        'description' => $items->description,
                        'tax' => ($items->tax === '' ? '0.00' : $items->tax),
                        'qty' => $items->qty,
                        'unitprice' => $items->price,
                        'value' => $items->price * $items->qty,
                        'discount' => $items->discount,
                        'fright' => 0.00,
                        'customcharges' => 0.00,
                        'taxableamount' => ($items->price * $items->qty) - $items->discount,
                        'vatamount' => (($items->price * $items->qty) - $items->discount) * 5 / 100,
                        'status' => 1,
                        'created_by' => Auth::user()->id,
                        'created_at' => Carbon::now('+04:00'),
                        'refid' => $items->id,
                    ]);
                }
            }
            $ret = SysPurchaseOrderItemsCart::select('sys_purchase_order_items_cart.*', 'sm_items.part_number AS partno')
                ->join('sm_items', 'sm_items.id', 'sys_purchase_order_items_cart.part_number')
                ->where('cart_id', session('logged_session_data.cart_id'))->get();

            return redirect('purchase-order/create/' . $customer_reference . '/' . $salesman_name . '/' . $deal_id . '/' . $deal_code);

        } catch (\Exception $e) {
            return $e;
        }
    }


    function add_purchase_order_items_excel_cart(Request $request)
    {
        try {

            DB::beginTransaction();
            $lastSortId = DB::table('sys_purchase_order_items_cart')->where('cart_id', session('logged_session_data.cart_id'))->max('sort_id');
            $nextSortId = $lastSortId ? $lastSortId + 1 : 1;

            $selected_file = "";
            if (!isset($request->excel_part_no) || count($request->excel_part_no) == 0) {
                Toastr::error('No Data found in excel', 'Failed');
                return redirect()->back();
            }
            if (count($request->excel_part_no) > 0) {
                for ($i = 0; $i < count($request->excel_part_no); $i++) {
                    if ($request->excel_part_no[$i] != "") {
                        $pid = SmItem::where('part_number', $request->excel_part_no[$i])->where('status', 1)->max('id');
                        if ($pid != "") {
                            $description = $request->excel_description[$i];
                            if ($description == false) { //check null value
                                $description = SmItem::where('part_number', $request->excel_part_no[$i])->where('status', 1)->max('description');
                            }

                            $val = ($request->excel_unit_price[$i] * $request->excel_qty[$i]) - $request->excel_discount[$i];
                            $data[] = [
                                'cart_id' => session('logged_session_data.cart_id'),
                                'part_number' => $pid,
                                'description' => $description,
                                'tax' => $request->vat_excel[$i],
                                'qty' => $request->excel_qty[$i],
                                'unitprice' => $request->excel_unit_price[$i],
                                'value' => $request->excel_unit_price[$i] * $request->excel_qty[$i],
                                'discount' => $request->excel_discount[$i],
                                'fright' => 0,
                                'customcharges' => 0,
                                'taxableamount' => $val,
                                'vatamount' => $val * $request->vat_excel[$i] / 100,
                                'serialno' => '',
                                'status' => 1,
                                'created_by' => Auth::user()->id,
                                'sort_id' => $nextSortId,
                            ];
                            $nextSortId++;
                        } else {
                            DB::rollBack();
                            Toastr::error('Item not found in System ' . $request->excel_part_no[$i], 'Failed');
                            return redirect()->back();
                        }
                    }
                }
            }
            if (count($data) > 0) {
                DB::table('sys_purchase_order_items_cart')->insert($data);
            }

            DB::commit();
            Toastr::success('Item Imported Successfully', 'Success');
            return redirect()->back();

            /*$ret = SysPurchaseOrderItemsCart::select('sys_purchase_order_items_cart.*','sm_items.part_number AS partno')
            ->join('sm_items','sm_items.id','sys_purchase_order_items_cart.part_number')
            ->where('cart_id',session('logged_session_data.cart_id'))->get();
            if(count($ret)>0){
                return json_encode(array('data'=>$ret));
            }else{
                $ret=[];
                return json_encode(array('data'=>$ret));
            }*/
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    // normal po cart start
    // function add_purchase_order_items_excel_cart(Request $request)
    // {
    //     try {
    //         DB::beginTransaction();
    //         $lastSortId = DB::table('sys_purchase_order_items_cart')->where('cart_id', session('logged_session_data.cart_id'))->max('sort_id');
    //         $nextSortId = $lastSortId ? $lastSortId + 1 : 1;

    //         $selected_file = "";
    //         if (!isset($request->excel_part_no) || count($request->excel_part_no) == 0) {
    //             Toastr::error('No Data found in excel', 'Failed');
    //             return redirect()->back();
    //         }
    //         if (count($request->excel_part_no) > 0) {
    //             for ($i = 0; $i < count($request->excel_part_no); $i++) {
    //                 if ($request->excel_part_no[$i] != "") {
    //                     $pid = SmItem::where('part_number', $request->excel_part_no[$i])->where('status', 1)->max('id');
    //                     if ($pid != "") {
    //                         $description = $request->excel_description[$i];
    //                         if ($description == false) { //check null value
    //                             $description = SmItem::where('part_number', $request->excel_part_no[$i])->where('status', 1)->max('description');
    //                         }

    //                         $val = ($request->excel_unit_price[$i] * $request->excel_qty[$i]) - $request->excel_discount[$i];
    //                         $data[] = [
    //                             'cart_id' => session('logged_session_data.cart_id'),
    //                             'part_number' => $pid,
    //                             'description' => $description,
    //                             'tax' => $request->vat_excel[$i],
    //                             'qty' => $request->excel_qty[$i],
    //                             'unitprice' => $request->excel_unit_price[$i],
    //                             'value' => $request->excel_unit_price[$i] * $request->excel_qty[$i],
    //                             'discount' => $request->excel_discount[$i],
    //                             'fright' => 0,
    //                             'customcharges' => 0,
    //                             'taxableamount' => $val,
    //                             'vatamount' => $val * $request->vat_excel[$i] / 100,
    //                             'serialno' => '',
    //                             'status' => 1,
    //                             'created_by' => Auth::user()->id,
    //                             'sort_id' => $nextSortId,
    //                         ];
    //                         $nextSortId++;
    //                     } else {
    //                         DB::rollBack();
    //                         Toastr::error('Item not found in System ' . $request->excel_part_no[$i], 'Failed');
    //                         return redirect()->back();
    //                     }
    //                 }
    //             }
    //         }
    //         if (count($data) > 0) {
    //             DB::table('sys_purchase_order_items_cart')->insert($data);
    //         }
    //         DB::commit();
    //         Toastr::success('Item Imported Successfully', 'Success');
    //         return redirect()->back();

    //         /*$ret = SysPurchaseOrderItemsCart::select('sys_purchase_order_items_cart.*','sm_items.part_number AS partno')
    //         ->join('sm_items','sm_items.id','sys_purchase_order_items_cart.part_number')
    //         ->where('cart_id',session('logged_session_data.cart_id'))->get();
    //         if(count($ret)>0){
    //             return json_encode(array('data'=>$ret));
    //         }else{
    //             $ret=[];
    //             return json_encode(array('data'=>$ret));
    //         }*/
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Toastr::error('Operation Failed', 'Failed');
    //         return redirect()->back();
    //     }
    // }
    function add_purchase_order_items_cart(Request $request)
    {
        try {
            $lastSortId = DB::table('sys_purchase_order_items_cart')->where('cart_id', session('logged_session_data.cart_id'))->max('sort_id');
            $nextSortId = $lastSortId ? $lastSortId + 1 : 1;

            // $check = DB::table('sys_purchase_order_items_cart')->where(['cart_id' => session('logged_session_data.cart_id'),
            //         'part_number' => $request->part_number,
            //         'description' => $request->description,
            //         'tax' => ($request->tax === '' ? '0.00' : $request->tax),
            //         'qty' => $request->qty,
            //         'unitprice' => $request->unitprice,
            //         'value' => $request->value,
            //         'discount' => $request->discount,])->count();
            // if($check == 0){
            DB::table('sys_purchase_order_items_cart')->insert(
                [
                    'cart_id' => session('logged_session_data.cart_id'),
                    'part_number' => $request->part_number,
                    'description' => $request->description,
                    'tax' => ($request->tax === '' ? '0.00' : $request->tax),
                    'qty' => $request->qty,
                    'unitprice' => $request->unitprice,
                    'value' => $request->value,
                    'discount' => $request->discount,
                    'fright' => $request->fright,
                    'customcharges' => $request->customcharges,
                    'taxableamount' => $request->taxableamount,
                    'vatamount' => $request->vatamount,
                    'serialno' => $request->serialno,
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                    'sort_id' => $nextSortId,
                ]
            );
            //}
            $ret = SysPurchaseOrderItemsCart::select('sys_purchase_order_items_cart.*', 'sm_items.part_number AS partno')
                ->join('sm_items', 'sm_items.id', 'sys_purchase_order_items_cart.part_number')
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
    function delete_purchase_order_items_cart(Request $request)
    {
        try {
            DB::table('sys_purchase_order_items_cart')->where('id', $request->id)->delete();
            $ret = SysPurchaseOrderItemsCart::select('sys_purchase_order_items_cart.*', 'sm_items.part_number AS partno')
                ->join('sm_items', 'sm_items.id', 'sys_purchase_order_items_cart.part_number')
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
    function view_purchase_order_items_cart(Request $request)
    {
        try {
            $ret = SysPurchaseOrderItemsCart::select('sys_purchase_order_items_cart.*', 'sm_items.part_number AS partno')
                ->join('sm_items', 'sm_items.id', 'sys_purchase_order_items_cart.part_number')
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

    function update_purchase_order_items_cart(Request $request)
    {
        try {
            try {
                SysCrmQuoteItems::where('id', $request->deal_ref_id)->update(['product_id' => $request->part_number]);
            } catch (\Throwable $th) { /*throw $th;*/
            }

            DB::table('sys_purchase_order_items_cart')->where('id', $request->itm_id)->update([
                'part_number' => $request->part_number,
                'description' => $request->description,
                'tax' => ($request->tax === '' ? '0.00' : $request->tax),
                'qty' => $request->qty,
                'unitprice' => $request->unitprice,
                'value' => $request->value,
                'discount' => $request->discount,
                'fright' => $request->fright,
                'customcharges' => $request->customcharges,
                'taxableamount' => $request->taxableamount,
                'vatamount' => $request->vatamount,
                'serialno' => $request->serialno,
            ]);

            $ret = SysPurchaseOrderItemsCart::select('sys_purchase_order_items_cart.*', 'sm_items.part_number AS partno')
                ->join('sm_items', 'sm_items.id', 'sys_purchase_order_items_cart.part_number')
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
    function add_purchase_order_deal_items_cart(Request $request)
    {
        try {
            $check = DB::table('sys_deal_purchase_order_items_cart')->where([
                'cart_id' => session('logged_session_data.cart_id'),
                'part_number' => $request->part_number,
                'description' => $request->description,
                'tax' => ($request->tax === '' ? '0.00' : $request->tax),
                'qty' => $request->qty,
                'unitprice' => $request->unitprice,
                'value' => $request->value,
                'discount' => $request->discount,
            ])->count();
            if ($check == 0) {
                DB::table('sys_deal_purchase_order_items_cart')->insert(
                    [
                        'cart_id' => session('logged_session_data.cart_id'),
                        'part_number' => $request->part_number,
                        'description' => $request->description,
                        'tax' => ($request->tax === '' ? '0.00' : $request->tax),
                        'qty' => $request->qty,
                        'unitprice' => $request->unitprice,
                        'value' => $request->value,
                        'discount' => $request->discount,
                        'fright' => $request->fright,
                        'customcharges' => $request->customcharges,
                        'taxableamount' => ((($request->unitprice * $request->qty) - $request->discount) + ($request->fright + $request->customcharges)),
                        'vatamount' => ((($request->unitprice * $request->qty) - $request->discount) + ($request->fright + $request->customcharges)) * $request->tax / 100,
                        'status' => 1,
                        'created_by' => Auth::user()->id,
                        'created_at' => Carbon::now('+04:00'),
                    ]
                );
            }
            $ret = SysDealPurchaseOrderItemsCart::select('sys_deal_purchase_order_items_cart.*', 'sm_items.part_number AS partno')
                ->join('sm_items', 'sm_items.id', 'sys_deal_purchase_order_items_cart.part_number')
                ->where('sys_deal_purchase_order_items_cart.created_by', Auth::user()->id)->get();
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
    // normal po cart start

    // create po cart from deal
    function deal_add_selected_deal_items_to_purchase_order_cart(Request $request)
    {


        try {
            if (!isset($request->selected_item_id)) {
                Toastr::error('Operation Failed! No Items Selected', 'Failed');
                return redirect()->back();
            }
            DB::beginTransaction();
            $deal = SysCrmDeals::where('id', $request->deal_id)->first();
            $customer_reference = $deal->customername->name;
            $salesman_name = $deal->owner;
            $deal_id = $deal->id;
            $deal_code = $request->deal_code;

            for ($i = 0; $i < count($request->item_id); $i++) {
                for ($j = 0; $j < count($request->selected_item_id); $j++) {
                    if ($request->selected_item_id[$j] == $request->roids[$i]) {
                        $data[] = [
                            'cart_id' => session('logged_session_data.cart_id'),
                            'quote_item_id' => $request->selected_item_id[$j],
                            'part_number' => $request->product_id[$i],
                            'part_number_txt' => $request->product_id[$i],
                            'description' => $request->description[$i],
                            'tax' => ($request->tax[$i] === '' ? '0.00' : $request->tax[$i]),
                            'qty' => $request->qty[$i],
                            'unitprice' => $request->unitprice[$i],
                            'value' => $request->unitprice[$i] * $request->qty[$i],
                            //'discount' => $request->discount[$i],
                            'fright' => 0,
                            'customcharges' => 0,
                            'taxableamount' => ($request->unitprice[$i] * $request->qty[$i]) - $request->discount[$i],
                            'vatamount' => (($request->unitprice[$i] * $request->qty[$i]) - $request->discount[$i]) * $request->tax[$i] / 100,
                            'status' => 1,
                            'created_by' => Auth::user()->id,
                            'created_at' => Carbon::now('+04:00'),
                            'refid' => $request->item_id[$i],
                            'deal_id' => $request->deal_id[$i],
                            'deal_qty' => $request->deal_qty[$i],
                        ];
                    }
                }
            }
            SysDealPurchaseOrderItemsCart::where('cart_id', session('logged_session_data.cart_id'))->delete();
            SysDealPurchaseOrderItems::where('cart_id', session('logged_session_data.cart_id'))->delete();
            SysDealPurchaseOrderItems::insert($data);
            DB::commit();
            Toastr::success('Items added to cart successfully', 'Success');
            return redirect('purchase-order/create/' . $customer_reference . '/' . $salesman_name . '/' . $deal_id . '/' . $deal_code);

        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    function deal_update_purchase_order_items_cart(Request $request)
    {
        try {
            try {
                SysCrmQuoteItems::where('id', $request->deal_ref_id)->update(['product_id' => $request->part_number]);
            } catch (\Throwable $th) { /*throw $th;*/
            }

            DB::table('sys_deal_purchase_order_items_cart')->where('id', $request->cart_item_id)->update([
                'part_number' => $request->pid,
                'description' => $request->description,
                'tax' => ($request->tax === '' ? '0.00' : $request->tax),
                'qty' => $request->qty,
                'unitprice' => $request->unitprice,
                'value' => $request->unitprice * $request->qty,
                'discount' => $request->discount,
                'fright' => $request->fright,
                'customcharges' => $request->customcharges,
                'taxableamount' => ((($request->unitprice * $request->qty) - $request->discount) + ($request->fright + $request->customcharges)),
                'vatamount' => ((($request->unitprice * $request->qty) - $request->discount) + ($request->fright + $request->customcharges)) * $request->tax / 100,
            ]);

            $ret = SysDealPurchaseOrderItemsCart::select('sys_deal_purchase_order_items_cart.*', 'sm_items.part_number AS partno')
                ->join('sm_items', 'sm_items.id', 'sys_deal_purchase_order_items_cart.part_number')
                ->where('sys_deal_purchase_order_items_cart.created_by', Auth::user()->id)->get();

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
    function deal_delete_purchase_order_items_cart(Request $request)
    {
        try {
            DB::table('sys_deal_purchase_order_items_cart')->where('id', $request->cart_item_id)->delete();
            $ret = SysDealPurchaseOrderItemsCart::select('sys_deal_purchase_order_items_cart.*', 'sm_items.part_number AS partno')
                ->join('sm_items', 'sm_items.id', 'sys_deal_purchase_order_items_cart.part_number')
                ->where('sys_deal_purchase_order_items_cart.created_by', Auth::user()->id)->get();

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

    public function deal_purchase_store(Request $request)
    {
        //return $request->all();
        if ($request->vendors == "") {
            Toastr::error('Vendors not found', 'Failed');
            return redirect()->back();
        }
        if ($request->currency == "") {
            Toastr::error('Currency not found', 'Failed');
            return redirect()->back();
        }
        if ($request->delivery_date == "") {
            Toastr::error('Delivery Date Man not found', 'Failed');
            return redirect()->back();
        }
        if ($request->payment_terms == "") {
            Toastr::error('Payment Terms Terms not found', 'Failed');
            return redirect()->back();
        }
        if ($request->supplier_type == "") {
            Toastr::error('Supplier Type not found', 'Failed');
            return redirect()->back();
        }
        if ($request->purchase_type == "") {
            Toastr::error('Purchase Type not found', 'Failed');
            return redirect()->back();
        }

        $cart = SysDealPurchaseOrderItemsCart::where('created_by', Auth::user()->id)->where('cart_id', session('logged_session_data.cart_id'))->get();
        if (count($cart) > 0) {
        } else {
            Toastr::error('Items not found', 'Failed');
            return redirect()->back();
        }
        DB::beginTransaction();
        try {
            $po = new SysPurchaseOrder();
            $po->doc_number = SysHelper::get_new_code('sys_purchase_order', 'PO', 'doc_number');
            $po->po_date = date('Y-m-d', strtotime($request->po_date));
            $po->vendors = $request->vendors;
            $po->currency = $request->currency;
            $po->narration = $request->narration;
            $po->delivery_date = date('Y-m-d', strtotime($request->delivery_date));
            $po->payment_terms = $request->payment_terms;
            $po->payment_terms2 = $request->payment_terms2;
            $po->supplier_remarks = $request->supplier_remarks;
            $po->shipping_supplier = $request->shipping_supplier;
            $po->shipping_address_1 = $request->shipping_address_1;
            //$po->shipping_address_2 = $request->shipping_address_2;
            $po->shipping_name = $request->shipping_name;
            $po->shipping_contact_no = $request->shipping_contact_no;
            $po->shipping_email = $request->shipping_email;
            $po->supplier_type = $request->supplier_type;
            $po->purchase_type = $request->purchase_type;
            $po->supplier_country = $request->supplier_country;
            $po->supplier_state = $request->supplier_state;
            $po->note = $request->note;
            $po->reference = $request->reference;
            $po->status = 1;
            $po->company_id = session('logged_session_data.company_id');
            $po->created_by = Auth::user()->id;
            $po->sales_person = $request->sales_person;
            $po->deal_id = $request->deal_id;
            $po->contact_person_name = $request->contact_person_name;
            $po->contact_person_email = $request->contact_person_email;
            $po->contact_person_telephone = $request->contact_person_telephone;
            if ($request->internal_transfer != "") {
                $po->internal_transfer = $request->internal_transfer;
            }
            $po->save();
            $po->toArray();

            if (count($cart) > 0) {
                foreach ($cart as $dt) {
                    $poi = new SysPurchaseOrderItems();
                    $poi->po_id = $po->id;
                    $poi->part_number = $dt->part_number;
                    $poi->description = $dt->description;
                    $poi->tax = ($dt->tax === '' ? '0.00' : $dt->tax);
                    $poi->qty = $dt->qty;
                    $poi->unitprice = $dt->unitprice;
                    $poi->value = $dt->value;
                    $poi->discount = ($dt->discount === '' ? '0.00' : $dt->discount);
                    $poi->fright = ($dt->fright === '' ? '0.00' : $dt->fright);
                    $poi->customcharges = ($dt->customcharges === '' ? '0.00' : $dt->customcharges);
                    $poi->taxableamount = $dt->taxableamount;
                    $poi->vatamount = $dt->vatamount;
                    $poi->status = 1;
                    $poi->created_by = Auth::user()->id;
                    //$poi->sort_id = $dt->sort_id;
                    $poi->save();

                    $crm_quote_item = SysCrmQuoteItems::find($dt->quote_item_id);
                    if ($crm_quote_item != "") {
                        $crm_quote_item->po_qty = $crm_quote_item->po_qty + $dt->qty;
                        $crm_quote_item->save();
                    }

                    try {
                        $items = SysDealPurchaseOrderItems::where('created_by', Auth::user()->id)->where('deal_id', $request->deal_id)
                            ->where('part_number', $dt->part_number)->first();
                        $items->po_qty = ($items->po_qty + $dt->qty);
                        $items->po_id = $po->id;
                        $items->save();

                        DB::table('sys_deal_purchase_order_items')->where('created_by', Auth::user()->id)->where('deal_id', $request->deal_id)
                            ->where('qty', $items->po_qty)
                            ->update([
                                'status' => 0,
                            ]);
                    } catch (\Throwable $th) {

                    }
                }
                DB::table('sys_deal_purchase_order_items_cart')->where('created_by', Auth::user()->id)->delete();
            }

            $deal_track_id = SysCrmDealTrack::select('id')->where('deal_id', $request->deal_id)->first();

            if ($request->internal_transfer == 1) {
                $retDeal = $this->Internal_Transfer_Deal($request->deal_id, $po->id, $request->vendors);
                if ($retDeal == "ERROR") {
                    DB::rollback();
                    return $retDeal;
                }

                $retTrack = $this->Internal_Transfer_Deal_Track($retDeal, $po->id);
                if ($retTrack != "TRACK") {
                    DB::rollback();
                    return $retTrack;
                }
            }


            DB::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect('crm-deal-track-approval/' . $deal_track_id->id);
        } catch (\Exception $e) {
            return $e;
            DB::rollback();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    // create po cart from deal
    function addpurchaseorderitems(Request $request)
    {
        try {
            DB::table('sys_purchase_order_items')->insert(
                [
                    'po_id' => $request->po_id,
                    'part_number' => $request->part_number,
                    'description' => $request->description,
                    'tax' => ($request->tax === '' ? '0.00' : $request->tax),
                    'qty' => $request->qty,
                    'unitprice' => $request->unitprice,
                    'value' => $request->value,
                    'discount' => $request->discount,
                    'fright' => $request->fright,
                    'customcharges' => $request->customcharges,
                    'taxableamount' => $request->taxableamount,
                    'vatamount' => $request->vatamount,
                    'sort_id' => $request->sort_id,
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                ]
            );
            $ret = SysPurchaseOrderItems::select('sys_purchase_order_items.*', 'sm_items.part_number AS partno')
                ->join('sm_items', 'sm_items.id', 'sys_purchase_order_items.part_number')
                ->where('po_id', $request->po_id)->orderby('sort_id', 'asc')->get();
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
    function updatepurchaseorderitems(Request $request)
    {
        try {
            DB::table('sys_purchase_order_items')->where('id', $request->id)->update(
                [
                    'part_number' => $request->part_number,
                    'description' => $request->description,
                    'tax' => ($request->tax === '' ? '0.00' : $request->tax),
                    'qty' => $request->qty,
                    'unitprice' => $request->unitprice,
                    'value' => $request->value,
                    'discount' => $request->discount,
                    'fright' => $request->fright,
                    'customcharges' => $request->customcharges,
                    'taxableamount' => $request->taxableamount,
                    'vatamount' => $request->vatamount,
                    'sort_id' => $request->sort_id,
                    'status' => 1,
                    'updated_by' => Auth::user()->id,
                    'updated_at' => Carbon::now('+04:00'),
                ]
            );
            $ret = SysPurchaseOrderItems::select('sys_purchase_order_items.*', 'sm_items.part_number AS partno')
                ->join('sm_items', 'sm_items.id', 'sys_purchase_order_items.part_number')
                ->where('po_id', $request->po_id)->orderby('sort_id', 'asc')->get();
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

    function deletepurchaseorderitems(Request $request)
    {
        try {
            DB::table('sys_purchase_order_items')->where('id', $request->id)->delete();

            $ret = SysPurchaseOrderItems::select('sys_purchase_order_items.*', 'sm_items.part_number AS partno')
                ->join('sm_items', 'sm_items.id', 'sys_purchase_order_items.part_number')
                ->where('po_id', $request->po_id)->orderby('sort_id', 'asc')->get();
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

    public function store(Request $request)
    {


        if (!isset($request->part_number)) {
            Toastr::error('Operation Failed. please enter valid data', 'Failed');
            return redirect()->back();
        }

        if ($request->vendors == "") {
            Toastr::error('Vendors not found', 'Failed');
            return redirect()->back();
        }
        if ($request->currency == "") {
            Toastr::error('Currency not found', 'Failed');
            return redirect()->back();
        }
        if ($request->delivery_date == "") {
            Toastr::error('Delivery Date Man not found', 'Failed');
            return redirect()->back();
        }
        if ($request->payment_terms == "") {
            Toastr::error('Payment Terms Terms not found', 'Failed');
            return redirect()->back();
        }
        if ($request->supplier_type == "") {
            Toastr::error('Supplier Type not found', 'Failed');
            return redirect()->back();
        }
        if ($request->purchase_type == "") {
            Toastr::error('Purchase Type not found', 'Failed');
            return redirect()->back();
        }


        // $cart = SysPurchaseOrderItemsCart::where('cart_id', session('logged_session_data.cart_id'))->get();
        // if (count($cart) > 0) {
        // } else {
        //     dd($request->all());
        //     Toastr::error('Items not found', 'Failed');
        //     return redirect()->back();
        // }


        DB::beginTransaction();
        try {
            $po = new SysPurchaseOrder();
            $po->doc_number = SysHelper::get_new_code('sys_purchase_order', 'PO', 'doc_number');
            $po->po_date = date('Y-m-d', strtotime($request->po_date));
            $po->vendors = $request->vendors;
            $po->currency = $request->currency;
            $po->narration = $request->narration;
            $po->delivery_date = date('Y-m-d', strtotime($request->delivery_date));
            $po->payment_terms = $request->payment_terms;
            $po->payment_terms2 = $request->payment_terms2;


            $po->supplier_remarks = $request->supplier_remarks;
            $po->shipping_supplier = $request->shipping_supplier;
            $po->shipping_address_1 = $request->shipping_address_1;

            //$po->shipping_address_2 = $request->shipping_address_2;
            $po->shipping_name = $request->shipping_name;
            $po->shipping_contact_no = $request->shipping_contact_no;
            $po->shipping_email = $request->shipping_email;
            $po->supplier_type = $request->supplier_type;
            $po->purchase_type = $request->purchase_type;
            $po->supplier_country = $request->supplier_country;
            $po->supplier_state = $request->supplier_state;
            $po->note = $request->note;
            $po->status = 1;
            $po->company_id = session('logged_session_data.company_id');
            $po->created_by = Auth::user()->id;
            $po->sales_person = $request->sales_person;
            $po->deal_id = SysHelper::get_dealid_from_code_list($request->deal_id);
            $po->contact_person_name = $request->contact_person_name;
            $po->contact_person_email = $request->contact_person_email;
            $po->contact_person_telephone = $request->contact_person_telephone;



            if ($request->create_grn == 1 || $request->create_pi == 1) {
                $po->bill_number = $request->bill_number;

                $po->bill_date = $request->bill_date;

                $po->awbno = $request->awbno;
                $po->boeno = $request->boeno;
                $po->reference = $request->reference;
            }


            if ($request->internal_transfer != "") {
                $po->internal_transfer = $request->internal_transfer;
            }

            $po->save();
            $po->toArray();



            for ($i = 0; $i < count($request->part_number); $i++) {
                if ($request->qty[$i] != "" && $request->qty[$i] > 0) {
                    $poi = new SysPurchaseOrderItems();
                    $poi->po_id = $po->id;
                    $poi->part_number = $request->part_number[$i] ?? null;
                    $poi->description = $request->description[$i] ?? null;
                    $poi->tax = $request->tax[$i] !== '' ? $request->tax[$i] : '0.00';
                    $poi->qty = $request->qty[$i];
                    $poi->unitprice = $request->unitprice[$i] ?? 0;
                    $poi->value = $request->value[$i] ?? 0;
                    $poi->discount = $request->discount[$i] !== '' ? $request->discount[$i] : '0.00';
                    $poi->fright = $request->fright[$i] !== '' ? $request->fright[$i] : '0.00';
                    $poi->customcharges = $request->customcharges[$i] !== '' ? $request->customcharges[$i] : '0.00';
                    $poi->taxableamount = $request->taxableamount[$i] ?? 0;
                    $poi->vatamount = $request->vatamount[$i] ?? 0;
                    $poi->serialno = $request->serial_no[$i] ?? '';
                    $poi->status = 1;
                    $poi->created_by = Auth::user()->id;
                    $poi->sort_id = $i + 1; // or use another field like $request->sort_id[$i] if available
                    $poi->save();
                    $str_arr = explode(",", $request->serial_no[$i]);
                    /*$str_arr = collect(preg_split('/[\s,]+/', $dt->serialno, -1, PREG_SPLIT_NO_EMPTY))
                    ->map(fn($s) => strtoupper(trim($s)))->unique()->values()->toArray();*/
                    foreach ($str_arr as $srl) {
                        $values = array('po_id' => $po->id, 'part_number' => $request->part_number[$i], 'srl_no' => $srl);
                        DB::table('sys_purchase_order_items_srl')->insert($values);
                    }
                }
            }





            // if (count($cart) > 0) {
            //     foreach ($cart as $dt) {
            //         $poi = new SysPurchaseOrderItems();
            //         $poi->po_id = $po->id;
            //         $poi->part_number = $dt->part_number;
            //         $poi->description = $dt->description;
            //         $poi->tax = ($dt->tax === '' ? '0.00' : $dt->tax);
            //         $poi->qty = $dt->qty;
            //         $poi->unitprice = $dt->unitprice;
            //         $poi->value = $dt->value;
            //         $poi->discount = ($dt->discount === '' ? '0.00' : $dt->discount);
            //         $poi->fright = ($dt->fright === '' ? '0.00' : $dt->fright);
            //         $poi->customcharges = ($dt->customcharges === '' ? '0.00' : $dt->customcharges);
            //         $poi->taxableamount = $dt->taxableamount;
            //         $poi->vatamount = $dt->vatamount;
            //         $poi->serialno = $dt->serialno;
            //         $poi->status = 1;
            //         $poi->created_by = Auth::user()->id;
            //         $poi->sort_id = $dt->sort_id;
            //         $poi->save();

            //         $str_arr = explode(",", $dt->serialno);
            //         /*$str_arr = collect(preg_split('/[\s,]+/', $dt->serialno, -1, PREG_SPLIT_NO_EMPTY))
            //         ->map(fn($s) => strtoupper(trim($s)))->unique()->values()->toArray();*/
            //         foreach ($str_arr as $srl) {
            //             $values = array('po_id' => $po->id, 'part_number' => $dt->part_number, 'srl_no' => $srl);
            //             DB::table('sys_purchase_order_items_srl')->insert($values);
            //         }
            //     }
            //     DB::table('sys_purchase_order_items_cart')->where('cart_id', session('logged_session_data.cart_id'))->delete();
            // }



            if (isset($request->create_deal)) {
                if ($request->create_deal == 1) {

                    $ret = $this->generateDeal($po->id);



                    if ($ret[0] == "OK") {
                        $retTrack = $this->generateDealTrack($ret[1]);

                        if ($retTrack != "TRACK") {

                            return $retTrack;

                        }
                    }

                    if ($ret[0] != "OK") {
                        DB::rollback();
                        return $ret[1];
                    }
                }
            }


            if (isset($request->create_grn)) {
                if ($request->create_grn == 1) {
                    $retGRN = $this->generateGRN($po->id);
                    if ($retGRN != "GRN") {
                        DB::rollback();
                        return $retGRN;
                    }

                }
            }
            if (isset($request->create_pi)) {
                if ($request->create_pi == 1) {
                    $retPI = $this->generatePI($po->id);
                    //return $retPI;
                    if ($retPI != "PI") {
                        DB::rollback();
                        return $retPI;
                    }
                }
            }

            if ($request->internal_transfer == 1) {
                if ($request->deal_id == "Without Deal" || $request->deal_id == "" || $request->deal_id == 0) {
                    $new_dealid = SysPurchaseOrder::select('deal_id')->where('id', $po->id)->first();
                    $retDeal = $this->Internal_Transfer_Deal($new_dealid->deal_id, $po->id, $request->vendors);
                    //return $retDeal;
                } else {
                    $retDeal = $this->Internal_Transfer_Deal($po->deal_id, $po->id, $request->vendors);
                    //return $retDeal;
                }
                if ($retDeal == "ERROR") {
                    DB::rollback();
                    return $retDeal;
                }
                if ($retDeal != "No Account" && $retDeal != "ERROR") {
                    $retTrack = $this->Internal_Transfer_Deal_Track($retDeal, $po->id);
                    if ($retTrack != "TRACK") {
                        DB::rollback();
                        return $retTrack;
                    }
                }

            }

            DB::table('sys_purchase_order_att')->where('cart_id', session('logged_session_data.cart_id'))->where('doc_id', 0)->where('company_id', session('logged_session_data.company_id'))->update(['doc_id' => $po->id]);

            //@if(someCondition) output this text @endif
            // if ($request->quotation_type == "equipment") {
            //     foreach ($request->Eproducts as $product) {
            //         $quotation_product                      = new SmQuotationProducts();
            //         $quotation_product->quotation_id        = $quotation->id;
            //         $quotation_product->product_id          = $product;
            //         $quotation_product->product_model       = $request->Eproduct_model[$i];
            //         $quotation_product->qnt                 = $request->Equantity[$i];
            //         $quotation_product->unit_price          = $request->Eunit_price[$i];
            //         $quotation_product->save();
            //         $data               = SmQuotationProducts::find($quotation_product->id);
            //         $data['note']       = '"quotation No' . $request->quotation_no . ' & Product Id ' . $data->product_id . '" has been added.';
            //         $data['model_name'] = 'SmQuotationProducts';
            //         $data['old_data']   = $data->toJson();
            //         $data['new_data']   = '';
            //         $data['action']     = 'Insert';
            //         $data['action_id']  = $data->id;
            //         $result             = SmGeneralSettings::StoreAllActivities($data);
            //         $i++;
            //     }
            // } else {
            //     foreach ($request->products as $product) {
            //         $quotation_product                  = new SmQuotationProducts();
            //         $quotation_product->quotation_id    = $quotation->id;
            //         $quotation_product->product_id      = $product;
            //         $quotation_product->qnt             = $request->quantity[$i];
            //         $quotation_product->unit_price      = $request->unit_price[$i];
            //         $result                             = $quotation_product->save();
            //         $data               = SmQuotationProducts::find($quotation_product->id);
            //         $data['note']       = '"quotation No' . $request->quotation_no . ' & Product Id ' . $data->product_id . '" has been added.';
            //         $data['model_name'] = 'SmQuotationProducts';
            //         $data['old_data']   = $data->toJson();
            //         $data['new_data']   = '';
            //         $data['action']     = 'Insert';
            //         $data['action_id']  = $data->id;
            //         $result             = SmGeneralSettings::StoreAllActivities($data);

            //         $i++;
            //     }
            // }

            DB::commit();

            if ($request->btnSubmit == 1) {
                Toastr::success('Purchase Order Successfully Created', 'Success');
                //print($po->id);                
                //return response('<script> alert(123); </script>');
                return redirect('purchase-order/' . $po->id . '/print');
                return redirect('purchase-order/create');
            } else {
                return redirect('purchase-order/' . $po->id);

                // Toastr::success('Purchase Order Successfully Created', 'Success');
                return redirect('purchase-order/create');
            }

        } catch (\Exception $e) {
            dd($e);
            return $e;
            DB::rollback();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    //end store method 

    public function edit($id)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $customer = SysHelper::get_customer_list($company_id);
            $salesman = SysHelper::get_sales_persons();

            $currency = SysCurrencySettings::select('id', 'code', 'ex_rate')->get();
            $company = SysCompany::find(session('logged_session_data.company_id'));
            $paymentterms = SysPaymentTerms::select('id', 'title')->orderby('title', 'asc')->get();

            $items = SysHelper::get_product_list_edit($company_id);

            $quotations = SmQuotation::all();
            $departments = SmInspectingDepartment::all();
            $shipping = SysShipping::all();
            $suppliertype = SysSupplierType::orderby('title', 'asc')->get();
            $purchasetype = SysPurchaseType::orderby('title', 'asc')->get();
            $countries = SysCountries::orderby('name', 'asc')->get();
            $states = SysStates::orderby('name', 'asc')->get();

            $po = SysPurchaseOrder::find($id);
            $po_items = SysPurchaseOrderItems::select('sys_purchase_order_items.*', 'sm_items.part_number AS partno')
                ->join('sm_items', 'sm_items.id', 'sys_purchase_order_items.part_number')
                ->where('po_id', $po->id)->orderby('sort_id', 'asc')->get();
            $vendors = SysChartofAccounts::select('id', 'account_name')->where('id', $po->vendors)->get();

            $currencylist2 = DB::table('sys_currency_rate as r')->select('r.id', 'r.from_currency', 'r.to_currency', 'c.code', 'r.rate')
                ->join('sys_currency as c', 'c.id', 'r.to_currency')
                ->where('r.status', 1)->where('r.from_currency', $po->currency)
                ->orderBy('c.code', 'ASC')->get();

            $net_vat = SysCustSuppl::select('vat_percentage')->join('sys_chartofaccounts', 'sys_chartofaccounts.account_code', 'sys_cust_suppl.code')
                ->where('sys_chartofaccounts.id', $po->vendors)->sum('vat_percentage');

            // foreach ($str_arr as $srl) {
            //         $values = array('po_id' => $po->id, 'part_number' => $request->part_number[$i], 'srl_no' => $srl);
            //         DB::table('sys_purchase_order_items_srl')->insert($values);
            //     }
            $edit_list_srl = DB::table('sys_purchase_order_items_srl')
                ->where('po_id', $id)
                ->get();

            return compact('quotations', 'currency', 'currencylist2', 'vendors', 'items', 'departments', 'paymentterms', 'company', 'shipping', 'suppliertype', 'purchasetype', 'countries', 'states', 'po', 'po_items', 'net_vat', 'customer', 'salesman', 'edit_list_srl');
            // return view('backEnd.purchaseorder.manage_purchase_order_edit', compact('quotations', 'currency', 'currencylist2', 'vendors', 'items', 'departments', 'paymentterms', 'company', 'shipping', 'suppliertype', 'purchasetype', 'countries', 'states', 'po', 'po_items', 'net_vat', 'customer', 'salesman'));
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
            $vendors = SysHelper::get_supplier_list_all($company_id);

            $currency = SysCurrencySettings::select('id', 'code')->get();
            $company = SysCompany::find(session('logged_session_data.company_id'));
            $paymentterms = SysPaymentTerms::select('id', 'title')->orderby('title', 'asc')->get();

            $items = SysHelper::get_product_list($company_id);

            $quotations = SmQuotation::all();
            $departments = SmInspectingDepartment::all();
            $shipping = SysShipping::all();
            $suppliertype = SysSupplierType::orderby('title', 'asc')->get();
            $purchasetype = SysPurchaseType::orderby('title', 'asc')->get();
            $countries = SysCountries::orderby('name', 'asc')->get();
            $states = SysStates::orderby('name', 'asc')->get();

            $salesman = SysHelper::get_sales_persons();
            $po = SysPurchaseOrder::find($id);
            $po_items = SysPurchaseOrderItems::select('sys_purchase_order_items.*', 'sm_items.part_number AS partno')
                ->join('sm_items', 'sm_items.id', 'sys_purchase_order_items.part_number')
                ->where('po_id', $po->id)->get();

            $net_vat = SysCustSuppl::select('vat_percentage')->join('sys_chartofaccounts', 'sys_chartofaccounts.account_code', 'sys_cust_suppl.code')
                ->where('sys_chartofaccounts.id', $po->vendors)->sum('vat_percentage');

            return view('backEnd.purchaseorder.manage_purchase_order_view', compact('quotations', 'currency', 'salesman', 'vendors', 'items', 'departments', 'paymentterms', 'company', 'shipping', 'suppliertype', 'purchasetype', 'countries', 'states', 'po', 'po_items', 'net_vat'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    function purchaseorderpending(Request $request)
    {
        try {

            $ppo = SysPurchaseOrder::where('vendors', $request->id)->get();
            // $shipping = new SysShipping();
            // $shipping->shipping_name = $request->shipping_name;
            // $shipping->contact_name = $request->contact_name;
            // $shipping->contact_no = $request->contact_no;
            // $shipping->address1 = $request->address1;
            // $shipping->address2 = $request->address2;
            // $shipping->status = 1;
            // $shipping->created_by = Auth::user()->id;            
            // $results = $shipping->save();

            $ret = $ppo; // SysShipping::all();
            return json_encode(array('data' => $ret));

        } catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }

    public function find(Request $request)
    {
        try {
            $po = SysPurchaseOrder::where('doc_number', 'like', '%' . $request->po_number . '%')->first();
            if ($po != '') {
                $po_items = SysPurchaseOrderItems::where('po_id', '=', $po->id)->get();
                $po_att = SysPurchaseOrderAttachment::where('po_id', '=', $po->id)->get();
                $company = SysCompany::find($po->company_id);
                return view('backEnd/purchaseorder/purchase_order_view', compact('po', 'po_items', 'po_att', 'company'));
            } else {
                Toastr::error('Invalid PO Number', 'Failed');
                return redirect('purchase-order');
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    public function print($id)
    {
        try {
            $po = SysPurchaseOrder::find($id);
            //return $po;

            $m_company_name = "";
            $m_contact_name = "";
            $m_address1 = "";
            $m_address2 = "";
            $m_city = "";
            $m_state = "";
            $m_country = "";
            $m_tel = "";
            $m_mob = "";
            $m_emali = "";
            $m_trnno = "";

            $bill_company_name = "";
            $bill_contact_name = "";
            $bill_address1 = "";
            $bill_address2 = "";
            $bill_city = "";
            $bill_state = "";
            $bill_country = "";
            $bill_tel = "";
            $bill_mob = "";
            $bill_emali = "";

            $ship_company_name = "";
            $ship_contact_name = "";
            $ship_address1 = "";
            $ship_address2 = "";
            $ship_city = "";
            $ship_state = "";
            $ship_country = "";
            $ship_tel = "";
            $ship_mob = "";
            $ship_emali = "";

            if (!empty($po)) {
                $company = SysCompany::find($po->company_id);
                $bill_contact_name = $po->createdby->full_name;
                $bill_company_name = $company->company_name;
                $bill_tel = $company->telephone;
                $bill_mob = $company->mobile;
                $bill_emali = $company->email;
                $bill_trnno = $company->vat_number;
                $bill_address1 = $company->company_address;
                $bill_address2 = "";
                $bill_city = "";
                $bill_state = $company->city;
                $bill_country = $company->countryname->name;
                $ship_mob = SmStaff::select('mobile')->where('user_id', $po->created_by)->first();
                $ship_mob = $ship_mob->mobile;

                $po_item = SysPurchaseOrderItems::where('po_id', '=', $po->id)->get();

                $main_data_list = SysCustSuppl::select('sys_cust_suppl.*')->join('sys_chartofaccounts', 'sys_chartofaccounts.account_code', 'sys_cust_suppl.code')->where('sys_chartofaccounts.id', $po->vendors)->first();
                $main_address = SysCustSupplAddressbook::where('cust_suppl_id', $main_data_list->id)->orderby('set_default', 'desc')->first();
                if (isset($main_address)) {
                    $m_company_name = $main_data_list->name;
                    $m_contact_name = $main_data_list->customer_salutation . ' ' . $main_data_list->first_name . ' ' . $main_data_list->last_name;
                    $m_address1 = $main_address->address;
                    $m_address2 = $main_address->address2;
                    $m_city = $main_address->city;
                    $m_state = $main_address->statename->name;
                    $m_country = $main_address->countryname->name;
                    $m_tel = $main_data_list->contcat_number;
                    $m_mob = $main_data_list->mobile;
                    $m_emali = $main_data_list->email;
                    $m_trnno = $main_data_list->vat_number;
                }

                $sub_data_list = SysCustSuppl::select('sys_cust_suppl.*')->join('sys_chartofaccounts', 'sys_chartofaccounts.account_code', 'sys_cust_suppl.code')->where('sys_chartofaccounts.id', $po->shipping_supplier)->first();
                if (isset($sub_data_list)) {

                    $sub_address = SysCustSupplAddressbook::where('cust_suppl_id', $sub_data_list->id)->orderby('set_default', 'desc')->first();

                    //$bill_contact_name= $sub_data_list->customer_salutation.' '.$sub_data_list->first_name.' '.$sub_data_list->last_name;


                    $ship_company_name = $sub_data_list->name;
                    $ship_contact_name = $sub_data_list->customer_salutation . ' ' . $sub_data_list->first_name . ' ' . $sub_data_list->last_name;
                    $ship_address1 = $sub_address->address;
                    $ship_address2 = $sub_address->address2;
                    $ship_city = $sub_address->city;
                    try {
                        $ship_state = $sub_address->statename->name;
                    } catch (\Throwable $th) {
                        $ship_state = "";
                    }

                    $ship_country = $sub_address->countryname->name;
                    $ship_tel = $sub_data_list->contcat_number;
                    //$ship_mob= $sub_data_list->mobile;
                    $ship_emali = $sub_data_list->email;
                    $ship_trnno = $sub_data_list->vat_number;
                }



                //if($po->contact_person_name!=""){ $m_contact_name = $po->contact_person_name; }
                if ($po->contact_person_email != "") {
                    $m_emali = $po->contact_person_email;
                }
                //if($po->contact_person_telephone!=""){ $m_tel = $po->contact_person_telephone; }

                if ($po->shipping_name != "") {
                    $ship_contact_name = $po->shipping_name;
                }
                if ($po->shipping_address_1 != "") {
                    $ship_address1 = $po->shipping_address_1;
                    $ship_address2 = "";
                }
                if ($po->shipping_email != "") {
                    $ship_emali = $po->shipping_email;
                }
                if ($po->shipping_contact_no != "") {
                    $ship_tel = $po->shipping_contact_no;
                }

                $data = [
                    'po' => $po,
                    'company' => $company,
                    'po_item' => $po_item,
                    'm_company_name' => $m_company_name,
                    'm_contact_name' => $m_contact_name,
                    'm_address1' => $m_address1,
                    'm_address2' => $m_address2,
                    'm_city' => $m_city,
                    'm_state' => $m_state,
                    'm_country' => $m_country,
                    'm_tel' => $m_tel,
                    'm_mob' => $m_mob,
                    'm_emali' => $m_emali,
                    'm_trnno' => $m_trnno,

                    'bill_company_name' => $bill_company_name,
                    'bill_contact_name' => $bill_contact_name,
                    'bill_address1' => $bill_address1,
                    'bill_address2' => $bill_address2,
                    'bill_city' => $bill_city,
                    'bill_state' => $bill_state,
                    'bill_country' => $bill_country,
                    'bill_tel' => $bill_tel,
                    'bill_mob' => $bill_mob,
                    'bill_emali' => $bill_emali,
                    'bill_trnno' => $bill_trnno,

                    'ship_company_name' => $ship_company_name,
                    'ship_contact_name' => $ship_contact_name,
                    'ship_address1' => $ship_address1,
                    'ship_address2' => $ship_address2,
                    'ship_city' => $ship_city,
                    'ship_state' => $ship_state,
                    'ship_country' => $ship_country,
                    'ship_tel' => $ship_tel,
                    'ship_mob' => $ship_mob,
                    'ship_emali' => $ship_emali,
                    'ship_trnno' => $ship_trnno,
                ];

                //return $data;
                //return view('backEnd.pdf_print.po_pdf', $data);
                $pdf = PDF::loadView('backEnd.pdf_print.po_pdf', $data);
                $pdf->setPaper('A4', 'portrait');
                return $pdf->download($po->doc_number . '-' . $po->accountname->account_name . ".pdf");
            } else {
                return "error!!";
                //return view('web.syscom_credit_application_form');
            }

        } catch (\Throwable $th) {
            return $th;
        }
    }
    public function printexcel($id)
    {
        try {

            $po = SysPurchaseOrder::find($id);
            //return $po;
            if (!empty($po)) {
                $company = SysCompany::find($po->company_id);
                $po_item = SysPurchaseOrderItems::where('po_id', '=', $po->id)->get();
                //return $po_item;

                $data = [
                    'po' => $po,
                    'company' => $company,
                    'po_item' => $po_item,
                ];

                return Excel::download(new SysPurchaseOrderItems, 'students.xlsx');

                $pdf = Excel::download($data, 'test.xlsx');
                $pdf->setPaper('A4', 'portrait');
                return $pdf->download("purchase_order_" . $po->doc_number . ".xls");
            } else {
                return "error!!";
                //return view('web.syscom_credit_application_form');
            }

        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function printpreview($id)
    {
        $po = SysPurchaseOrder::find($id);
        //return $po;
        if (!empty($po)) {
            $company = SysCompany::find($po->company_id);
            $po_item = SysPurchaseOrderItems::where('po_id', '=', $po->id)->get();
            //return $po_item;

            $data = [
                'po' => $po,
                'company' => $company,
                'po_item' => $po_item,
            ];


            $pdf = PDF::loadView('backEnd.pdf_print.po_pdf', $data);
            //$pdf->setPaper('A4', 'portrait');
            //return $pdf->download("purchase_order_".$po->doc_number.".pdf");
            return $pdf->stream("purchase_order_" . $po->doc_number . ".pdf");
        } else {
            return "error!!";
            //return view('web.syscom_credit_application_form');
        }
    }

    public function addattachment(Request $request)
    {
        //return $request;

        $po_attach_file = "";
        if ($request->file('po_attach_file') != "") {
            $file = $request->file('po_attach_file');
            $po_attach_file = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
            $file->move('public/uploads/po_attachment/', $po_attach_file);
            $po_attach_file = 'public/uploads/po_attachment/' . $po_attach_file;
        }

        try {
            $po_att = new SysPurchaseOrderAttachment();
            $po_att->po_id = $request->po_id;
            $po_att->file_name = $request->file_name;
            $po_att->description = $request->description;
            $po_att->validtill = date('Y-m-d', strtotime($request->validtill));
            $po_att->po_attach_file = $po_attach_file;
            $po_att->status = 1;
            $po_att->created_by = Auth::user()->id;
            $results = $po_att->save();

            Toastr::success('Operation successful', 'Success');
            return redirect('purchase-order/' . $po_att->po_id);
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function update(Request $request, SmQuotation $smQuotation)
    {

        $input = $request->all();


        DB::beginTransaction();
        try {
            $po = SysPurchaseOrder::find($request->id);
            $po->po_date = date('Y-m-d', strtotime($request->po_date));
            if ($request->doc_number != $request->doc_number_main) {
                $exists = SysPurchaseOrder::where('doc_number', $request->doc_number)->exists();
                if ($exists) {
                    DB::rollback();
                    Toastr::error('Operation Failed. Document number already exists', 'Failed');
                    return redirect()->back();
                }
                $po->doc_number = $request->doc_number;
            }

            $po->vendors = $request->vendors;
            $po->currency = $request->currency;
            $po->narration = $request->narration;
            $po->delivery_date = date('Y-m-d', strtotime($request->delivery_date));
            $po->payment_terms = $request->payment_terms;
            $po->payment_terms2 = $request->payment_terms2;
            $po->supplier_remarks = $request->supplier_remarks;
            $po->shipping_supplier = $request->shipping_supplier;
            $po->shipping_address_1 = $request->shipping_address_1;
            //$po->shipping_address_2 = $request->shipping_address_2;
            $po->shipping_name = $request->shipping_name;
            $po->shipping_contact_no = $request->shipping_contact_no;
            $po->shipping_email = $request->shipping_email;
            $po->supplier_type = $request->supplier_type;
            $po->purchase_type = $request->purchase_type;
            $po->supplier_country = $request->supplier_country;
            $po->supplier_state = $request->supplier_state;
            $po->note = $request->note;
            $po->reference = $request->reference;
            $po->status = 1;
            $po->company_id = session('logged_session_data.company_id');
            $po->updated_by = Auth::user()->id;
            $po->sales_person = $request->sales_person;
            $po->deal_id = SysHelper::get_dealid_from_code_list($request->deal_id);
            $po->contact_person_name = $request->contact_person_name;
            $po->contact_person_email = $request->contact_person_email;
            $po->contact_person_telephone = $request->contact_person_telephone;

            if ($request->internal_transfer != "") {
                $po->internal_transfer = $request->internal_transfer;
            }

            $po->save();
            $po->toArray();



            SysPurchaseOrderItems::where('po_id', $po->id)->delete();
            DB::table('sys_purchase_order_items_srl')->where('po_id', $po->id)->delete();

            



            for ($i = 0; $i < count($request->part_number); $i++) {
                if ($request->qty[$i] != "" && $request->qty[$i] > 0) {

                    $poi = new SysPurchaseOrderItems();
                    $poi->po_id = $po->id;
                    $poi->part_number = $request->part_number[$i] ?? null;
                    $poi->description = $request->description[$i] ?? null;
                    $poi->tax = $request->tax[$i] !== '' ? $request->tax[$i] : '0.00';
                    $poi->qty = $request->qty[$i];
                    $poi->unitprice = $request->unitprice[$i] ?? 0;
                    $poi->value = $request->value[$i] ?? 0;
                    $poi->discount = $request->discount[$i] !== '' ? $request->discount[$i] : '0.00';
                    $poi->fright = $request->fright[$i] !== '' ? $request->fright[$i] : '0.00';
                    $poi->customcharges = $request->customcharges[$i] !== '' ? $request->customcharges[$i] : '0.00';
                    $poi->taxableamount = $request->taxableamount[$i] ?? 0;
                    $poi->vatamount = $request->vatamount[$i] ?? 0;
                    $poi->serialno = $request->serial_no[$i] ?? '';
                    $poi->status = 1;
                    $poi->created_by = Auth::user()->id;
                    $poi->sort_id = $i + 1; // or use another field like $request->sort_id[$i] if available
                    $poi->save();
                    $str_arr = explode(",", $request->serial_no[$i]);
                    /*$str_arr = collect(preg_split('/[\s,]+/', $dt->serialno, -1, PREG_SPLIT_NO_EMPTY))
                    ->map(fn($s) => strtoupper(trim($s)))->unique()->values()->toArray();*/
                    foreach ($str_arr as $srl) {
                        $values = array('po_id' => $po->id, 'part_number' => $request->part_number[$i], 'srl_no' => $srl);
                        DB::table('sys_purchase_order_items_srl')->insert($values);
                    }
                }
            }


            if ($po->internal_transfer == 0 && $request->internal_transfer == 1) {
                $retDeal = $this->Internal_Transfer_Deal($po->deal_id, $po->id, $request->vendors);
                if ($retDeal == "ERROR") {
                    DB::rollback();
                    return $retDeal;
                }

                $retTrack = $this->Internal_Transfer_Deal_Track($retDeal, $po->id);
                if ($retTrack != "TRACK") {
                    DB::rollback();
                    return $retTrack;
                }
            }



            // --------------------------------------

            if (isset($request->create_deal)) {
                if ($request->create_deal == 1) {
                    $ret = $this->generateDeal_update($po->id);
                    if ($ret[0] != "OK") {
                        DB::rollback();
                        return $ret[1];
                    }
                }
            }
            if (isset($request->create_grn)) {
                if ($request->create_grn == 1) {
                    $retGRN = $this->generateGRN_update($po->id);
                    if ($retGRN != "GRN") {
                        DB::rollback();
                        return $retGRN;
                    }
                }
            }
            if (isset($request->create_pi)) {
                if ($request->create_pi == 1) {
                    $retPI = $this->generatePI_update($po->id);
                    if ($retPI != "PI") {
                        DB::rollback();
                        return $retPI;
                    }
                }
            }

            // --------------------------------------





            DB::commit();
            if ($request->btnSubmit == 1) {
                Toastr::success('Purchase Order Successfully Updated', 'Success');
                return redirect('purchase-order/' . $po->id . '/print');
            } else {
                Toastr::success('Purchase Order Successfully Updated', 'Success');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function destroy(Request $request, SmQuotation $smQuotation)
    {

        try {
            $result = SmQuotation::destroy($request->id);

            if ($result) {
                Toastr::success('Operation successful', 'Success');
                return redirect('quotations');
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect('quotations');
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();

            DB::table('sys_purchase_order')->where('id', $id)->update(['status' => 2]);
            DB::table('sys_purchase_order_items')->where('po_id', $id)->update(['status' => 2]);
            DB::table('sys_purchase_order_attachment')->where('po_id', $id)->update(['status' => 2]);


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

            DB::table('sys_purchase_order')->where('id', $id)->update(['status' => 1]);
            DB::table('sys_purchase_order_items')->where('po_id', $id)->update(['status' => 1]);
            DB::table('sys_purchase_order_attachment')->where('po_id', $id)->update(['status' => 1]);

            DB::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    function purchaseorderupdate_currency(Request $request)
    {
        try {
            if ($request->to_currency_id != $request->from_currency_id) {

                $to_currency = SysCurrencyRate::where('id', $request->to_currency_id)->value('to_currency');
                $qt = SysPurchaseOrderItems::where('po_id', $request->cur_po_id)->get();
                SysPurchaseOrder::where('id', $request->cur_po_id)->update(['currency' => $to_currency]);
                foreach ($qt as $t) {
                    //$old_price = $t->unitprice / $old_currancy->ex_rate;
                    $new_price = $t->unitprice * $request->to_currency_rate;

                    //$old_discount = $t->discount / $old_currancy->ex_rate;
                    $new_discount = $t->discount * $request->to_currency_rate;

                    SysPurchaseOrderItems::where('id', $t->id)->update(
                        [
                            'unitprice' => $new_price,
                            'value' => $new_price * $t->qty,
                            'discount' => $new_discount,
                            'taxableamount' => ($new_price * $t->qty) - $new_discount + ($t->fright + $t->customcharges),
                            'vatamount' => (($new_price * $t->qty) - $new_discount + ($t->fright + $t->customcharges)) * $t->tax / 100,
                        ]
                    );

                    // SysItemStock::where('doc_number',$request->cur_sr_doc_no)->where('partno',$t->part_number)->update(
                    //     ['price_in' => ($new_price*$t->qty) - $new_discount,]);
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

    // discount
    function purchaseorderupdate_discount(Request $request)
    {
        try {
            if ($request->discount_amount != "") {
                $qt = SysPurchaseOrderItems::where('po_id', $request->discount_amount_po_id)->get();
                $discount_amount = $request->discount_amount;
                $total = $qt->sum('value');
                foreach ($qt as $t) {
                    $new_discount = ($t->value / $total) * $discount_amount;
                    SysPurchaseOrderItems::where('id', $t->id)->update(
                        [
                            'discount' => $new_discount,
                            'taxableamount' => ($t->unitprice * $t->qty) - $new_discount + ($t->fright + $t->customcharges),
                            'vatamount' => (($t->unitprice * $t->qty) - $new_discount + ($t->fright + $t->customcharges)) * $t->tax / 100,
                        ]
                    );
                }
            }
            Toastr::success('Discount Updated Successfully.', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    function add_purchase_order_items_cart_discount(Request $request)
    {
        try {
            if ($request->discount_amount != "") {
                $qt = SysPurchaseOrderItemsCart::where('cart_id', session('logged_session_data.cart_id'))->get();
                $discount_amount = $request->discount_amount;
                $total = $qt->sum('value');
                foreach ($qt as $t) {
                    $new_discount = ($t->value / $total) * $discount_amount;
                    SysPurchaseOrderItemsCart::where('id', $t->id)->update(
                        [
                            'discount' => $new_discount,
                            'taxableamount' => ($t->unitprice * $t->qty) - $new_discount + ($t->fright + $t->customcharges),
                            'vatamount' => (($t->unitprice * $t->qty) - $new_discount + ($t->fright + $t->customcharges)) * $t->tax / 100,
                        ]
                    );
                }
            }
            Toastr::success('Discount Updated Successfully.', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    function add_purchase_order_deal_items_cart_discount(Request $request)
    {
        try {
            if ($request->discount_amount != "") {
                $qt = SysDealPurchaseOrderItemsCart::where('cart_id', session('logged_session_data.cart_id'))->get();
                $discount_amount = $request->discount_amount;
                $total = $qt->sum('value');
                foreach ($qt as $t) {
                    $new_discount = ($t->value / $total) * $discount_amount;
                    SysDealPurchaseOrderItemsCart::where('id', $t->id)->update(
                        [
                            'discount' => $new_discount,
                            'taxableamount' => ($t->unitprice * $t->qty) - $new_discount + ($t->fright + $t->customcharges),
                            'vatamount' => (($t->unitprice * $t->qty) - $new_discount + ($t->fright + $t->customcharges)) * $t->tax / 100,
                        ]
                    );
                }
            }
            Toastr::success('Discount Updated Successfully.', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    // discount

    // freight
    function purchaseorderupdate_freight(Request $request)
    {
        try {
            if ($request->freight_amount != "") {
                $qt = SysPurchaseOrderItems::where('po_id', $request->freight_amount_po_id)->get();
                $freight_amount = $request->freight_amount;
                $total = $qt->sum('value');
                foreach ($qt as $t) {
                    $new_freight = ($t->value / $total) * $freight_amount;
                    SysPurchaseOrderItems::where('id', $t->id)->update(
                        [
                            'fright' => $new_freight,
                            'taxableamount' => ($t->unitprice * $t->qty) - $t->discount + $new_freight + $t->customcharges,
                            'vatamount' => (($t->unitprice * $t->qty) - $t->discount + $new_freight + $t->customcharges) * $t->tax / 100,
                        ]
                    );
                }
            }
            Toastr::success('Freight Updated Successfully.', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    function add_purchase_order_items_cart_freight(Request $request)
    {
        try {
            if ($request->freight_amount != "") {
                $qt = SysPurchaseOrderItemsCart::where('cart_id', session('logged_session_data.cart_id'))->get();
                $freight_amount = $request->freight_amount;
                $total = $qt->sum('value');
                foreach ($qt as $t) {
                    $new_freight = ($t->value / $total) * $freight_amount;
                    SysPurchaseOrderItemsCart::where('id', $t->id)->update(
                        [
                            'fright' => $new_freight,
                            'taxableamount' => ($t->unitprice * $t->qty) - $t->discount + $new_freight + $t->customcharges,
                            'vatamount' => (($t->unitprice * $t->qty) - $t->discount + $new_freight + $t->customcharges) * $t->tax / 100,
                        ]
                    );
                }
            }
            Toastr::success('Freight Updated Successfully.', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    function add_purchase_order_deal_items_cart_freight(Request $request)
    {
        try {
            if ($request->freight_amount != "") {
                $qt = SysDealPurchaseOrderItemsCart::where('cart_id', session('logged_session_data.cart_id'))->get();
                $freight_amount = $request->freight_amount;
                $total = $qt->sum('value');
                foreach ($qt as $t) {
                    $new_freight = ($t->value / $total) * $freight_amount;
                    SysDealPurchaseOrderItemsCart::where('id', $t->id)->update(
                        [
                            'fright' => $new_freight,
                            'taxableamount' => ($t->unitprice * $t->qty) - $t->discount + $new_freight + $t->customcharges,
                            'vatamount' => (($t->unitprice * $t->qty) - $t->discount + $new_freight + $t->customcharges) * $t->tax / 100,
                        ]
                    );
                }
            }
            Toastr::success('Freight Updated Successfully.', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    // freight

    // custom
    function purchaseorderupdate_custom(Request $request)
    {
        try {
            if ($request->custom_amount != "") {
                $qt = SysPurchaseOrderItems::where('po_id', $request->custom_amount_po_id)->get();
                $custom_amount = $request->custom_amount;
                $total = $qt->sum('value');
                foreach ($qt as $t) {
                    $new_custom = ($t->value / $total) * $custom_amount;
                    SysPurchaseOrderItems::where('id', $t->id)->update(
                        [
                            'customcharges' => $new_custom,
                            'taxableamount' => ($t->unitprice * $t->qty) - $t->discount + $new_custom + $t->fright,
                            'vatamount' => (($t->unitprice * $t->qty) - $t->discount + $new_custom + $t->fright) * $t->tax / 100,
                        ]
                    );
                }
            }
            Toastr::success('Custom Charges Updated Successfully.', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    function add_purchase_order_items_cart_custom(Request $request)
    {
        try {
            if ($request->custom_amount != "") {
                $qt = SysPurchaseOrderItemsCart::where('cart_id', session('logged_session_data.cart_id'))->get();
                $custom_amount = $request->custom_amount;
                $total = $qt->sum('value');
                foreach ($qt as $t) {
                    $new_custom = ($t->value / $total) * $custom_amount;
                    SysPurchaseOrderItemsCart::where('id', $t->id)->update(
                        [
                            'customcharges' => $new_custom,
                            'taxableamount' => ($t->unitprice * $t->qty) - $t->discount + $new_custom + $t->fright,
                            'vatamount' => (($t->unitprice * $t->qty) - $t->discount + $new_custom + $t->fright) * $t->tax / 100,
                        ]
                    );
                }
            }
            Toastr::success('Custom Charges Updated Successfully.', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    function add_purchase_order_deal_items_cart_custom(Request $request)
    {
        try {
            if ($request->custom_amount != "") {
                $qt = SysDealPurchaseOrderItemsCart::where('cart_id', session('logged_session_data.cart_id'))->get();
                $custom_amount = $request->custom_amount;
                $total = $qt->sum('value');
                foreach ($qt as $t) {
                    $new_custom = ($t->value / $total) * $custom_amount;
                    SysDealPurchaseOrderItemsCart::where('id', $t->id)->update(
                        [
                            'customcharges' => $new_custom,
                            'taxableamount' => ($t->unitprice * $t->qty) - $t->discount + $new_custom + $t->fright,
                            'vatamount' => (($t->unitprice * $t->qty) - $t->discount + $new_custom + $t->fright) * $t->tax / 100,
                        ]
                    );
                }
            }
            Toastr::success('Custom Charges Updated Successfully.', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    // custom

    //store all
    public function store_all(Request $request)
    {
        if ($request->vendors == "") {
            Toastr::error('Vendors not found', 'Failed');
            return redirect()->back();
        }
        if ($request->currency == "") {
            Toastr::error('Currency not found', 'Failed');
            return redirect()->back();
        }
        if ($request->delivery_date == "") {
            Toastr::error('Delivery Date Man not found', 'Failed');
            return redirect()->back();
        }
        if ($request->payment_terms == "") {
            Toastr::error('Payment Terms Terms not found', 'Failed');
            return redirect()->back();
        }
        if ($request->supplier_type == "") {
            Toastr::error('Supplier Type not found', 'Failed');
            return redirect()->back();
        }
        if ($request->purchase_type == "") {
            Toastr::error('Purchase Type not found', 'Failed');
            return redirect()->back();
        }

        $cart = SysPurchaseOrderItemsCart::where('cart_id', session('logged_session_data.cart_id'))->get();
        if (count($cart) > 0) {
        } else {
            Toastr::error('Items not found', 'Failed');
            return redirect()->back();
        }
        DB::beginTransaction();
        try {
            $po = new SysPurchaseOrder();
            $po->doc_number = SysHelper::get_new_code('sys_purchase_order', 'PO', 'doc_number');
            $po->po_date = date('Y-m-d', strtotime($request->po_date));
            $po->vendors = $request->vendors;
            $po->currency = $request->currency;
            $po->narration = $request->narration;
            $po->delivery_date = date('Y-m-d', strtotime($request->delivery_date));
            $po->payment_terms = $request->payment_terms;
            $po->payment_terms2 = $request->payment_terms2;
            $po->supplier_remarks = $request->supplier_remarks;
            $po->shipping_supplier = $request->shipping_supplier;
            $po->shipping_address_1 = $request->shipping_address_1;
            //$po->shipping_address_2 = $request->shipping_address_2;
            $po->shipping_name = $request->shipping_name;
            $po->shipping_contact_no = $request->shipping_contact_no;
            $po->shipping_email = $request->shipping_email;
            $po->supplier_type = $request->supplier_type;
            $po->purchase_type = $request->purchase_type;
            $po->supplier_country = $request->supplier_country;
            $po->supplier_state = $request->supplier_state;
            $po->note = $request->note;
            $po->status = 1;
            $po->company_id = session('logged_session_data.company_id');
            $po->created_by = Auth::user()->id;
            $po->sales_person = $request->sales_person;
            $po->deal_id = SysHelper::get_dealid_from_code($request->deal_id);
            $po->contact_person_name = $request->contact_person_name;
            $po->contact_person_email = $request->contact_person_email;
            $po->contact_person_telephone = $request->contact_person_telephone;
            if ($request->internal_transfer != "") {
                $po->internal_transfer = $request->internal_transfer;
            }
            $po->save();
            $po->toArray();

            if (count($cart) > 0) {
                foreach ($cart as $dt) {
                    $poi = new SysPurchaseOrderItems();
                    $poi->po_id = $po->id;
                    $poi->part_number = $dt->part_number;
                    $poi->description = $dt->description;
                    $poi->tax = ($dt->tax === '' ? '0.00' : $dt->tax);
                    $poi->qty = $dt->qty;
                    $poi->unitprice = $dt->unitprice;
                    $poi->value = $dt->value;
                    $poi->discount = ($dt->discount === '' ? '0.00' : $dt->discount);
                    $poi->fright = ($dt->fright === '' ? '0.00' : $dt->fright);
                    $poi->customcharges = ($dt->customcharges === '' ? '0.00' : $dt->customcharges);
                    $poi->taxableamount = $dt->taxableamount;
                    $poi->vatamount = $dt->vatamount;
                    $poi->status = 1;
                    $poi->created_by = Auth::user()->id;
                    $poi->save();
                }
                DB::table('sys_purchase_order_items_cart')->where('cart_id', session('logged_session_data.cart_id'))->delete();
            }

            $PurchaseAuto = SysPurchaseAuto::where('cart_id', session('logged_session_data.cart_id'))->where('created_by', Auth::user()->id)->where('company_id', session('logged_session_data.company_id'))->where('status', 1)->orderby('id', 'desc')->get();
            //return  $PurchaseAuto;

            $grnID = 0;
            if (count($PurchaseAuto) > 0) {
                if ($PurchaseAuto[0]->req_grn == 1) {
                    $grnID = $this->grn_auto($po->id);
                    //return $grnID;
                }
                if ($PurchaseAuto[0]->req_pi == 1) {
                    $piID = $this->pi_auto($po->id, $grnID);
                    //return $piID;
                }
                if ($PurchaseAuto[0]->req_pay == 1) {
                    $pyID = $this->payment_auto($po->id, $PurchaseAuto[0]->req_mode_acc);
                    //return $pyID;
                }
            }
            if ($request->internal_transfer == 1) {
                $retDeal = $this->Internal_Transfer_Deal($po->deal_id, $po->id, $request->vendors);
                if ($retDeal == "ERROR") {
                    DB::rollback();
                    return $retDeal;
                }
                if ($retDeal != "No Account" && $retDeal != "ERROR") {
                    $retTrack = $this->Internal_Transfer_Deal_Track($retDeal, $po->id);
                    if ($retTrack != "TRACK") {
                        DB::rollback();
                        return $retTrack;
                    }
                }
            }

            SysPurchaseAuto::where('cart_id', session('logged_session_data.cart_id'))->where('created_by', Auth::user()->id)->where('company_id', session('logged_session_data.company_id'))->where('status', 1)->update(['po_no' => $po->doc_number, 'po_id' => $po->id, 'status' => 2]);

            $trackid = SysCrmDealTrack::where('deal_id', $PurchaseAuto[0]->deal_id)->first();
            DB::commit();
            Toastr::success('Purchase Successfully Created', 'Success');
            return redirect('crm-deal-track-approval/' . $trackid->id);

        } catch (\Exception $e) {
            return $e;
            DB::rollback();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function grn_auto($po_id)
    {
        DB::beginTransaction();
        $po = SysPurchaseOrder::where('id', $po_id)->first();
        $po_item = SysPurchaseOrderItems::where('po_id', $po_id)->get();

        try {
            $grn = new SysPurchaseGRN();
            $grn->doc_number = SysHelper::get_new_code('sys_purchase_grn', 'GR', 'doc_number');
            $grn->grn_date = date('Y-m-d', strtotime($po->po_date));
            $grn->po_id = $po->id;
            $grn->vendors = $po->vendors;
            $grn->currency = $po->currency;
            $grn->lpo_number = $po->doc_number;
            $grn->lpo_date = $po->po_date;
            $grn->payment_terms = $po->payment_terms;
            $grn->bill_number = '';
            $grn->bill_date = $po->po_date;
            $grn->awbno = '';
            $grn->warehouse = '';
            $grn->reference = '';
            $grn->narration = $po->narration;
            $grn->deal_id = $po->deal_id;
            $grn->sales_person = $po->sales_person;
            $grn->status = 1;
            $grn->created_by = Auth::user()->id;
            $grn->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
            $grn->company_id = session('logged_session_data.company_id');
            $grn->save();

            for ($i = 0; $i < count($po_item); $i++) {
                $part_no = SmItem::where('id', $po_item[$i]->part_number)->max('part_number');

                $grnitms = new SysPurchaseGRNItems();
                $grnitms->grn_id = $grn->id;
                $grnitms->po_id = $po_id;
                $grnitms->part_no = $po_item[$i]->part_number;
                $grnitms->part_number = $part_no;
                $grnitms->tax = $po_item[$i]->tax;
                $grnitms->qty = $po_item[$i]->qty;
                //$grnitms->issue_qty = $po_item[$i]->qty;
                //$grnitms->grn_qty = $po_item[$i]->qty;
                $grnitms->unitprice = $po_item[$i]->unitprice;
                $grnitms->value = $po_item[$i]->value;
                $grnitms->discount = $po_item[$i]->discount;
                //$grnitms->customcharges = $request->customcharges[$i];
                $grnitms->taxableamount = $po_item[$i]->taxableamount;
                $grnitms->vatamount = $po_item[$i]->vatamount;
                $grnitms->status = 1;
                $grnitms->save();

                // $str_arr = explode (",", $request->srl[$i]);
                // foreach($str_arr as $srl){
                //     $values = array('grn_id' => $grn->id,'part_no' => $request->part_id[$i],'srl_no' => $srl);
                //     DB::table('sys_purchase_grn_items_srlno')->insert($values);
                // }                    

                // SysPurchaseGrnLicenseKey::where('item_id',$request->part_id[$i])->where('grn_id',0)->where('cart_id',session('logged_session_data.cart_id'))->where('company_id',session('logged_session_data.company_id'))->update(['grn_id' => $grn->id]);


                // $grn_quantity = DB::table('sys_purchase_order_items')->where('po_id',$request->po_id)->where('part_number',$request->part_id[$i])->sum('grn_qty');

                // DB::table('sys_purchase_order_items')->where('po_id',$request->po_id)->where('part_number',$request->part_id[$i])
                // ->update(['grn_qty' => $grn_quantity+$request->qty[$i]]);

                $discount = ($po_item[$i]->discount === '' ? '0.00' : $po_item[$i]->discount);
                $istock = new SysItemStock();
                $istock->grn_id = $grn->id;
                $istock->account_id = $po->vendors;
                $istock->partno = $po_item[$i]->part_number;
                $istock->qty_in = $po_item[$i]->qty;
                $istock->price_in = ($po_item[$i]->value - $discount) / $po_item[$i]->qty;
                $istock->refno = $grn->lpo_number;
                $istock->doc_number = $grn->doc_number;
                $istock->doc_date = $grn->grn_date;
                $istock->deal_id = $grn->deal_id;
                $istock->slno = '';
                $istock->status = 1;
                $istock->created_by = Auth::user()->id;
                $istock->company_id = session('logged_session_data.company_id');
                $istock->currency_id = $po->currency;
                $istock->save();
            }

            //SysPurchaseGrnLicenseKey::where('grn_id',0)->where('cart_id',session('logged_session_data.cart_id'))
            //->where('company_id',session('logged_session_data.company_id'))->delete();

            $po = SysPurchaseOrderItems::where('po_id', $po_id)->sum('qty');
            $gr = SysPurchaseGRNItems::where('po_id', $po_id)->where('po_id', '!=', 0)->sum('qty');

            if ($po <= $gr) {
                DB::table('sys_purchase_order')->where('id', $po_id)->update(['grn_status' => 1]);
            }

            SysPurchaseAuto::where('cart_id', session('logged_session_data.cart_id'))->where('created_by', Auth::user()->id)->where('company_id', session('logged_session_data.company_id'))->where('status', 1)->update(['grn_no' => $grn->doc_number, 'grn_id' => $grn->id]);

            DB::commit();
            return $grn->id;

        } catch (\Exception $e) {
            return $e;
            DB::rollback();
        }
    }
    public function pi_auto($po_id, $grnID)
    {
        try {
            $po = SysPurchaseOrder::where('id', $po_id)->first();
            $po_item = SysPurchaseOrderItems::where('po_id', $po_id)->get();

            if ($grnID != 0) {
                $grn = SysPurchaseGRN::where('id', $grnID)->first();
                $grn_item = SysPurchaseGRNItems::where('grn_id', $grnID)->get();
            }
            DB::beginTransaction();

            $pi = new SysPurchaseInvoice();
            $pi->doc_number = SysHelper::get_new_code('sys_purchase_invoice', 'PI', 'doc_number');
            $pi->pi_date = date('Y-m-d', strtotime($po->po_date));
            $pi->ref_po_id = $po_id;
            $pi->vendors = $po->vendors;
            $pi->currency = $po->currency;
            // $pi->narration = $request->narration;
            $pi->lpo_number = $po->doc_number;
            $pi->lpo_date = date('Y-m-d', strtotime($po->po_date));
            $pi->bill_number = '';
            $pi->bill_date = date('Y-m-d', strtotime($po->po_date));
            $pi->payment_terms = $po->payment_terms;
            $pi->payment_terms2 = $po->payment_terms2;

            $pi->awbno = '';
            $pi->reference = $po->narration;
            $pi->warehouse = '';

            if (isset($grn)) {
                $pi->ref_grn_id = $grn->id;
                $pi->grn_no = $grn->doc_number;
                $pi->grn_date = $grn->grn_date;
            }

            $pi->sales_person = $po->sales_person;
            $pi->narration = $po->narration;
            $pi->deal_id = $po->deal_id;

            $pi->shipping_name = $po->shipping_name;
            $pi->shipping_address_1 = $po->shipping_address_1;
            $pi->shipping_address_2 = $po->shipping_address_2;
            $pi->shipping_contact_no = $po->shipping_contact_no;

            $pi->supplier_type = $po->supplier_type;
            $pi->purchase_type = $po->purchase_type;
            $pi->supplier_country = $po->supplier_country;
            $pi->supplier_state = $po->supplier_state;
            $pi->status = 1;
            $pi->company_id = session('logged_session_data.company_id');
            $pi->created_by = Auth::user()->id;
            $pi->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
            $pi->save();
            $pi->toArray();

            if (isset($grn)) {
                $total_tax_amount = $grn_item->sum('taxableamount');
                $total_vat_amount = $grn_item->sum('vatamount');

            } else {
                $total_tax_amount = $po_item->sum('taxableamount');
                $total_vat_amount = $po_item->sum('vatamount');

            }

            //Supplier account cr
            SysHelper::trn_chartof_accounts_transaction($po->vendors, $pi->id, $pi->doc_number, $pi->pi_date, 'purchaseinvoice', '0.00', ($total_tax_amount + $total_vat_amount), '', 1, 0, "", 1);

            //Purchase account dr 
            $purchase_account_id = SysHelper::get_purchase_account_id();
            SysHelper::trn_chartof_accounts_transaction($purchase_account_id, $pi->id, $pi->doc_number, $pi->pi_date, 'purchaseinvoice', ($total_tax_amount), '0.00', '', 1, 0, "", 1);

            //vat account dr 
            $purchase_vat_account_id = SysHelper::get_purchase_vat_account_id();
            SysHelper::trn_chartof_accounts_transaction($purchase_vat_account_id, $pi->id, $pi->doc_number, $pi->pi_date, 'purchaseinvoice', ($total_vat_amount), '0.00', '', 1, 0, "", 1);

            for ($i = 0; $i < count($po_item); $i++) {
                $pii = new SysPurchaseInvoiceItems();
                $pii->pi_id = $pi->id;
                $pii->ref_po_id = $po->id;
                $pii->part_number = $po_item[$i]->part_number;
                $pii->tax = $po_item[$i]->tax;
                $pii->qty = $po_item[$i]->qty;
                $pii->unitprice = $po_item[$i]->unitprice;
                $pii->value = $po_item[$i]->value;
                $pii->discount = $po_item[$i]->discount;
                $pii->taxableamount = $po_item[$i]->taxableamount;
                $pii->vatamount = $po_item[$i]->vatamount;
                $pii->status = 1;
                $pii->created_by = Auth::user()->id;
                $pii->save();

            }

            if (count($grn_item) > 0) {
                $po = SysPurchaseInvoiceItems::where('pi_id', $pi->id)->sum('qty');
                $gr = SysPurchaseGRNItems::where('grn_id', $pi->ref_grn_id)->sum('qty');
                if ($po == $gr) {
                    DB::table('sys_purchase_grn')->where('id', $pi->ref_grn_id)->update(['grn_status' => 0]);
                }
            }

            SysPurchaseAuto::where('cart_id', session('logged_session_data.cart_id'))->where('created_by', Auth::user()->id)->where('company_id', session('logged_session_data.company_id'))->where('status', 1)->update(['pi_no' => $pi->doc_number, 'pi_id' => $pi->id]);

            DB::commit();
            return $pi->id;

        } catch (\Exception $e) {
            DB::rollback();
            return $e;
        }
    }

    public function payment_auto($po_id, $account_id)
    {
        try {
            DB::beginTransaction();

            $po = SysPurchaseOrder::where('id', $po_id)->first();
            $po_item = SysPurchaseOrderItems::where('po_id', $po_id)->get();

            $p = SysChartofAccounts::select('subgroup2')->where('id', $account_id)->first();

            $py = new SysPayment();
            if ($p->subgroup2 == 5) { //mode 1 cash, mode 2 bank
                $py->doc_number = SysHelper::get_new_code('sys_payment', 'CP', 'doc_number');
                ;
            } else {
                $py->doc_number = SysHelper::get_new_code('sys_payment', 'BP', 'doc_number');
                ;
            }
            $py->doc_date = date('Y-m-d', strtotime($po->po_date));
            if ($p->subgroup2 == 5) {
                $py->mode = 1;
                $py->payment_mode = $account_id;
                $py->payment_through = 1;
            } else {
                $py->mode = 2;
                $py->payment_mode = $account_id;
                $py->payment_through = 1;
            }
            $py->cheque_date = date('Y-m-d', strtotime($po->po_date));
            $py->cheque_number = '';
            $py->cheque_bank_name = '';
            $py->currency = $po->currency;
            $py->payment_date = $po->po_date;
            $py->narration = $po->narration;
            $py->status = 1;
            $py->created_by = Auth::user()->id;
            $py->created_at = Carbon::now('+04:00');
            $py->company_id = session('logged_session_data.company_id');
            $py->deal_id = $po->deal_id;
            $results = $py->save();
            $py->toArray();

            if ($p->subgroup2 == 5) { //mode 1 cash, mode 2 bank
                $transaction_type = "cashpayment";
            } else {
                $transaction_type = "bankpayment";
            }

            $status = 1;
            SysHelper::trn_chartof_accounts_transaction_with_main($account_id, $py->id, $py->doc_number, $py->payment_date, $transaction_type, '0.00', $po_item->sum('taxableamount'), $po->narration, $status, 0, "", 1, 1);
            SysHelper::trn_chartof_accounts_transaction_with_main($po->vendors, $py->id, $py->doc_number, $py->payment_date, $transaction_type, $po_item->sum('taxableamount'), '0.00', $po->narration, $status, 0, "", 1, 0);


            SysPurchaseAuto::where('cart_id', session('logged_session_data.cart_id'))->where('created_by', Auth::user()->id)->where('company_id', session('logged_session_data.company_id'))->where('status', 1)->update(['pay_no' => $py->doc_number, 'pay_id' => $py->id]);

            //return $request->all();
            // for($i = 0; $i < count($request->account_id); $i++) {
            //     if($request->account_id[$i] !="" && $request->amount[$i] !=""){
            //         SysHelper::trn_chartof_accounts_transaction_with_main($request->account_id[$i],$py->id,$py->doc_number,$py->payment_date,$transaction_type,$request->amount[$i],'0.00',$request->remarks[$i],$status,0,"",1,0);
            //     }
            // }

            /*$temp = SysPaymentAdjustmentsTemp::where('process_id',$request->process_id)->get();
            if(count($temp)>0){
                foreach ($temp as $te) {
                    $temp_data[]=[
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
                        'status' => 1,
                        'created_by' => Auth::user()->id,
                        'created_at' => Carbon::now('+04:00'),
                        'company_id' => session('logged_session_data.company_id'),
                    ];
                }*/
            //SysPaymentAdjustments::insert($temp_data);
            //SysReceiptAdjustmentsTemp::where('process_id',$request->process_id)->delete();
            //SysReceiptAdjustmentsTemp::where('created_by',Auth::user()->id)->delete();


            DB::commit();
            return $py->id;

        } catch (\Exception $e) {
            DB::rollback();
            return $e;
        }
    }

    public function generateDeal($po_id)
    {
        try {

            $com = session('logged_session_data.company_id');
            $po = SysPurchaseOrder::where('id', $po_id)->first();
            $po_item = SysPurchaseOrderItems::where('po_id', $po_id)->get();

            $cust = SysCustSuppl::select('sys_cust_suppl.*')
                ->join('sys_chartofaccounts as ca', 'ca.account_code', 'sys_cust_suppl.code')
                ->where('ca.id', $po->shipping_supplier)->first();
            //return $cust;

            $scd = new SysCrmDeals();
            $scd->code = SysHelper::get_new_lead_deal_code('sys_crm_deals', 'code', $com);
            $scd->date = date('Y-m-d', strtotime($po->po_date));
            $scd->deal_name = $po->doc_number;
            $scd->cust_id = $cust->id;
            $scd->cust_name = $cust->first_name . ' ' . $cust->last_name;
            $scd->cust_no = $cust->contcat_number;
            $scd->cust_email = $cust->email;
            $scd->deal_value = $po_item->sum('taxableamount');
            $scd->deal_currency = $po->currency;
            $scd->source = 'Mail';
            $scd->source_o = '';
            $scd->tags = '';
            $scd->stage = 4;
            $scd->owner = $po->created_by;
            $scd->doc = '';
            $scd->isproject = 2;
            $scd->designation = $cust->designation;
            $scd->address = $cust->address;

            $scd->delivery_company = $cust->name;
            $scd->delivery_name = $po->shipping_name;
            $scd->delivery_number = $po->shipping_contact_no;
            $scd->delivery_email = $cust->email;
            $scd->delivery_address = $po->shipping_address;

            $scd->note = 'Deal Created from Purchase Order No: ' . $po->doc_number;
            $scd->status = 1;
            $scd->estimated_close_date = date('Y-m-d', strtotime($po->delivery_date));
            $scd->created_by = Auth::user()->id;
            $scd->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
            $scd->company_id = $com;
            $scd->is_professional_service = 0;
            //$scd->sales_invoice_id = $siv->id;
            $scd->save();
            $scd->toArray();

            $i = 1;
            foreach ($po_item as $items) {
                DB::table('sys_crm_quote_items')->insert([
                    'user_id' => $scd->owner,
                    'deal_id' => $scd->id,
                    'company_id' => $scd->company_id,
                    'currency_id' => $po->currency,
                    'customer_type' => $cust->account_type,
                    'quote_validity' => '3 Weeks',
                    'payment_terms' => $po->payment_terms,
                    'delivery_date' => $po->delivery_date,
                    'payment_terms_txt' => $po->payment_terms2,
                    'delivery_time' => '3 Weeks',
                    'product_id' => $items->part_number,
                    'qty' => $items->qty,
                    'price' => $items->unitprice,
                    'description' => $items->description,
                    'discount' => $items->discount,
                    'vat' => $items->tax,
                    'cost' => $items->unitprice,
                    'status' => $items->status,
                    'sort_id' => $i++,
                    'created_by' => Auth::user()->id,
                    'quote_id' => 1,
                ]);
            }

            SysPurchaseOrder::where('id', $po_id)->update(['deal_id' => $scd->id]);

            return ["OK", $scd->id];
        } catch (\Throwable $th) {

            return ["ERROR", $th];
        }
    }
    public function generateDealTrack($deal_id)
    {
        try {

            $deal = SysCrmDeals::where('id', $deal_id)->first();
            $deal_items = SysCrmQuoteItems::where('deal_id', $deal_id)->first();
            $po = SysPurchaseOrder::where('deal_id', $deal_id)->first();


            $track = new SysCrmDealTrack();
            $track->deal_id = $deal_id;
            $track->delivery_date = date('Y-m-d', strtotime($deal->estimated_close_date));

            $track->payment_terms = $deal_items->payment_terms;

            if ($deal_items->payment_terms == 1) {
                $track->payment_mode = 1;
            } else {
                $track->payment_mode = 2;
            }

            //$track->payment_mode_sec = $request->payment_mode_sec;

            $track->lpo = "";
            $track->purchease_quote = "";
            $track->cheque_copy = "";
            $track->technical = 0;
            $track->technical_detail = "";
            $track->remarks = "Deal Track created from Purchase Order";
            $track->reference_no = $po->doc_number;
            $track->reference_date = $deal->date;

            $track->purchease_approval = 1;
            $track->invoice_approval = 0;
            $track->delivery_approval = 0;
            $track->receivables_approval = 0;
            //$track->start_date = $request->start_date;
            //$track->end_date = $request->end_date;

            $track->accounts = 1;
            $track->sales = 1;
            $track->purchease = 1;
            $track->invoice = 1;
            $track->delivery = 1;
            $track->receivables = 1;
            $track->tech = 0;
            $track->created_by = Auth::user()->id;
            $track->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
            $track->company_id = session('logged_session_data.company_id');
            $track->save();
            $track->toArray();

            DB::table('sys_crm_deal_track_approval_accounts')->insert(
                [
                    'deal_track_id' => $track->id,
                    'deal_id' => $deal_id,
                    'customer_status' => 1,
                    'credit_limit' => 1,
                    'payment_terms' => 1,
                    'pending_payment' => 1,
                    'other' => 1,
                    'remarks' => 'ADVANCE PAYMENT',
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00')->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now('+04:00')->format('Y-m-d H:i:s'),
                ]
            );

            DB::table('sys_crm_deal_track_approval_sales')->insert(
                [
                    'deal_track_id' => $track->id,
                    'deal_id' => $deal_id,
                    'margin' => 1,
                    'stock' => 1,
                    'purcease_quote' => 1,
                    'other' => 1,

                    'purchase_approval' => 2,
                    'invoice_approval' => 0,
                    'delivery_approval' => 0,
                    'receivables_approval' => 0,

                    'remarks' => $deal->deal_name,
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00')->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now('+04:00')->format('Y-m-d H:i:s'),
                ]
            );

            DB::table('sys_crm_deal_track_approval_purchease')->insert(
                [
                    'deal_track_id' => $track->id,
                    'deal_id' => $deal_id,
                    'purchease_quote' => 1,
                    'three_quote_request' => 1,
                    'validation' => 1,
                    'other' => 1,
                    'remarks' => "",
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00')->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now('+04:00')->format('Y-m-d H:i:s'),
                    'fileone' => "",
                    'filetwo' => "",
                    'filethree' => "",
                    'lpo_no' => $po->doc_number,
                    'supplier_name' => $po->accountname->account_name,
                    'part_no' => "",
                    'cost_of_purchase' => $deal->deal_value,
                    'cost_of_purchase_currency' => $po->currency,
                    'delivery_date' => $deal_items->delivery_date,
                    'partial_delivery_note' => "",
                ]
            );

            return "TRACK";

        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function generateGRN($po_id)
    {
        try {
            $po = SysPurchaseOrder::where('id', $po_id)->first();
            $po_item = SysPurchaseOrderItems::where('po_id', $po_id)->get();
            $deal_track = SysCrmDealTrack::where('deal_id', $po->deal_id)->first();

            $grn = new SysPurchaseGRN();
            $grn->doc_number = SysHelper::get_new_code('sys_purchase_grn', 'GR', 'doc_number');
            $grn->grn_date = date('Y-m-d', strtotime($po->po_date));
            $grn->po_id = $po_id;
            $grn->vendors = $po->vendors;
            $grn->currency = $po->currency;
            $grn->lpo_number = $po->doc_number;
            $grn->lpo_date = $po->po_date;
            $grn->payment_terms = $po->payment_terms;

            $grn->bill_number = $po->bill_number;
            $grn->bill_date = $po->bill_date;
            $grn->awbno = $po->awbno;
            $grn->boeno = $po->boeno;
            $grn->reference = $po->narration;

            $grn->warehouse = "";
            $grn->narration = $po->reference;
            $grn->deal_id = $po->deal_id;
            $grn->sales_person = $po->sales_person;
            $grn->status = 1;
            $grn->created_by = Auth::user()->id;
            $grn->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
            $grn->company_id = session('logged_session_data.company_id');
            $grn->save();

            for ($i = 0; $i < count($po_item); $i++) {

                $item = SmItem::select('part_number')->where('id', $po_item[$i]->part_number)->first();
                $grnitms = new SysPurchaseGRNItems();
                $grnitms->grn_id = $grn->id;
                $grnitms->po_id = $po_id;
                $grnitms->part_no = $po_item[$i]->part_number;
                $grnitms->part_number = $item->part_number;
                $grnitms->tax = $po_item[$i]->tax;
                $grnitms->qty = $po_item[$i]->qty;
                $grnitms->unitprice = $po_item[$i]->unitprice;
                $grnitms->value = $po_item[$i]->value;
                $grnitms->discount = $po_item[$i]->discount;
                $grnitms->fright = $po_item[$i]->fright;
                $grnitms->customcharges = $po_item[$i]->customcharges;
                //$grnitms->customcharges = $request->customcharges[$i];
                $grnitms->taxableamount = $po_item[$i]->taxableamount;
                $grnitms->vatamount = $po_item[$i]->vatamount;
                $grnitms->status = 1;
                $grnitms->save();


                $str_arr = explode(",", $po_item[$i]->serialno);
                foreach ($str_arr as $srl) {
                    $values = array('grn_id' => $grn->id, 'part_no' => $po_item[$i]->part_number, 'srl_no' => $srl);
                    DB::table('sys_purchase_grn_items_srlno')->insert($values);
                }

                DB::table('sys_purchase_order_items')->where('po_id', $po_id)->where('part_number', $po_item[$i]->part_number)
                    ->update(['grn_qty' => $po_item[$i]->qty]);

                $discount = ($po_item[$i]->discount === '' ? '0.00' : $po_item[$i]->discount);
                $istock = new SysItemStock();
                $istock->grn_id = $grn->id;
                $istock->account_id = $po->vendors;
                $istock->partno = $po_item[$i]->part_number;
                $istock->qty_in = $po_item[$i]->qty;
                $istock->price_in = ($po_item[$i]->value - $discount) / $po_item[$i]->qty;
                $istock->refno = $grn->lpo_number;
                $istock->doc_number = $grn->doc_number;
                $istock->doc_date = $grn->grn_date;
                $istock->deal_id = $grn->deal_id;
                $istock->slno = $po_item[$i]->serialno;
                $istock->status = 1;
                $istock->created_by = Auth::user()->id;
                $istock->company_id = session('logged_session_data.company_id');
                $istock->currency_id = $po->currency;
                $istock->save();

            }

            DB::table('sys_purchase_order')->where('id', $po_id)->update(['grn_status' => 1]);

            return "GRN";

        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function generatePI($po_id)
    {
        try {
            $po = SysPurchaseOrder::where('id', $po_id)->first();
            $po_item = SysPurchaseOrderItems::where('po_id', $po_id)->get();
            $deal_track = SysCrmDealTrack::where('deal_id', $po->deal_id)->first();
            $grn = SysPurchaseGRN::where('po_id', $po_id)->first();

            $pi = new SysPurchaseInvoice();
            $pi->doc_number = SysHelper::get_new_code('sys_purchase_invoice', 'PI', 'doc_number');
            $pi->pi_date = date('Y-m-d', strtotime($po->po_date));
            $pi->ref_po_id = $po_id;

            if (isset($grn)) {
                $pi->ref_grn_id = $grn->id;
                $pi->grn_no = $grn->doc_number;
                $pi->grn_date = $grn->grn_date;
                $pi->bill_number = $grn->bill_number;
                $pi->bill_date = date('Y-m-d', strtotime($grn->bill_date));
                $pi->awbno = $grn->awbno;
                $pi->reference = $grn->reference;
                $pi->warehouse = $grn->warehouse;
            }

            $pi->vendors = $po->vendors;
            $pi->currency = $po->currency;
            $pi->lpo_number = $po->doc_number;
            $pi->lpo_date = date('Y-m-d', strtotime($po->po_date));
            $pi->payment_terms = $po->payment_terms;
            $pi->payment_terms2 = $po->payment_terms2;


            $pi->sales_person = $po->sales_person;
            $pi->narration = $po->reference;
            $pi->deal_id = $po->deal_id;

            $pi->shipping_name = $po->shipping_name;
            $pi->shipping_address_1 = $po->shipping_address_1;
            $pi->shipping_address_2 = $po->shipping_address_2;
            $pi->shipping_contact_no = $po->shipping_contact_no;

            $pi->supplier_type = $po->supplier_type;
            $pi->purchase_type = $po->purchase_type;
            $pi->supplier_country = $po->supplier_country;
            $pi->supplier_state = $po->supplier_state;
            $pi->status = 1;

            $pi->bill_number = $po->bill_number;
            $pi->bill_date = $po->bill_date;
            $pi->awbno = $po->awbno;
            $pi->boeno = $po->boeno;
            $pi->reference = $po->narration;

            $pi->company_id = session('logged_session_data.company_id');
            $pi->created_by = Auth::user()->id;
            $pi->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
            $pi->save();
            $pi->toArray();


            $total_tax_amount = $po_item->sum('taxableamount');
            $total_vat_amount = $po_item->sum('vatamount');

            //Supplier account cr
            SysHelper::trn_chartof_accounts_transaction($pi->vendors, $pi->id, $pi->doc_number, $pi->pi_date, 'purchaseinvoice', '0.00', ($total_tax_amount + $total_vat_amount), '', 1, 0, "", 1);

            //Purchase account dr 
            $purchase_account_id = SysHelper::get_purchase_account_id();
            SysHelper::trn_chartof_accounts_transaction($purchase_account_id, $pi->id, $pi->doc_number, $pi->pi_date, 'purchaseinvoice', ($total_tax_amount), '0.00', '', 1, 0, "", 1);

            //vat account dr 
            $purchase_vat_account_id = SysHelper::get_purchase_vat_account_id();
            SysHelper::trn_chartof_accounts_transaction($purchase_vat_account_id, $pi->id, $pi->doc_number, $pi->pi_date, 'purchaseinvoice', ($total_vat_amount), '0.00', '', 1, 0, "", 1);

            for ($i = 0; $i < count($po_item); $i++) {
                if ($po_item[$i]->part_number != "" && $po_item[$i]->qty != "" && $po_item[$i]->unitprice != "") {
                    $pii = new SysPurchaseInvoiceItems();
                    $pii->pi_id = $pi->id;
                    $pii->ref_po_id = $po_id;
                    $pii->part_number = $po_item[$i]->part_number;
                    $pii->tax = $po_item[$i]->tax;
                    $pii->qty = $po_item[$i]->qty;
                    $pii->unitprice = $po_item[$i]->unitprice;
                    $pii->value = $po_item[$i]->value;
                    $pii->discount = ($po_item[$i]->discount === '' ? '0.00' : $po_item[$i]->discount);
                    $pii->fright = $po_item[$i]->fright;
                    $pii->customcharges = $po_item[$i]->customcharges;
                    $pii->taxableamount = ($po_item[$i]->taxableamount === '' ? '0.00' : $po_item[$i]->taxableamount);
                    $pii->vatamount = ($po_item[$i]->vatamount === '' ? '0.00' : $po_item[$i]->vatamount);
                    $pii->status = 1;
                    $pii->created_by = Auth::user()->id;
                    $pii->save();

                }
            }

            $po = SysPurchaseInvoiceItems::where('pi_id', $pi->id)->sum('qty');
            $gr = SysPurchaseGRNItems::where('grn_id', $pi->ref_grn_id)->sum('qty');
            if ($po == $gr) {
                DB::table('sys_purchase_grn')->where('id', $pi->ref_grn_id)->update(['grn_status' => 0]);
            }


            return "PI";

        } catch (\Throwable $th) {
            return $th;
        }
    }
    public function Internal_Transfer_Deal($deal_id, $po_id, $sup_id)
    {
        try {

            $com_id = SysHelper::get_internal_transfer_company_id($sup_id);
            if ($com_id == 0) {
                return "No Account";
            }

            $com_sess = session('logged_session_data.company_id');
            $cust_id = SysHelper::get_internal_transfer_customer_id($com_sess);
            if ($cust_id == 0) {
                return "No Account";
            }

            $po = SysPurchaseOrder::where('id', $po_id)->first();
            $po_item = SysPurchaseOrderItems::where('po_id', $po_id)->first();
            $deal = SysCrmDeals::where('id', $deal_id)->first();
            $deal_items = SysCrmQuoteItems::select('sys_crm_quote_items.*', 'po_itm.tax', 'po_itm.qty as p_qty', 'po_itm.unitprice', 'po_itm.discount as p_discount', 'po_itm.description as p_description')
                ->join('sys_purchase_order_items as po_itm', 'po_itm.part_number', 'sys_crm_quote_items.product_id')
                ->where('sys_crm_quote_items.deal_id', $deal_id)->where('po_itm.po_id', $po_id)->get();


            $cust = SysCustSuppl::where('id', $cust_id)->first();
            //$cust_address = SysCustSupplAddressbook::where('cust_suppl_id',$cust_id)->orderby('id','desc')->first();
            if (isset($deal)) {
                $scd = new SysCrmDeals();
                $scd->code = SysHelper::get_new_lead_deal_code('sys_crm_deals', 'code', $com_id);
                $scd->date = date('Y-m-d', strtotime($deal->date));
                $scd->deal_name = 'Internal Transfer PO Number: ' . $po->doc_number . ' & Deal ID: ' . $deal->code . '';
                $scd->cust_id = $cust_id;
                $scd->cust_name = $cust->name;
                $scd->cust_no = $cust->contcat_number;
                $scd->cust_email = $cust->email;
                $scd->deal_value = $deal->deal_value;
                $scd->source = $deal->source;
                $scd->source_o = $deal->source_o;
                $scd->tags = $deal->tags;
                $scd->stage = $deal->stage;
                $scd->owner = 3;
                $scd->doc = $deal->doc;
                $scd->isproject = $deal->isproject;
                $scd->designation = $deal->designation;
                $scd->address = $deal->address;

                $scd->delivery_company = $deal->delivery_company;
                $scd->delivery_name = $deal->delivery_name;
                $scd->delivery_number = $deal->delivery_number;
                $scd->delivery_email = $deal->delivery_email;
                $scd->delivery_address = $deal->delivery_address;

                $scd->note = $deal->note . ' (Created From Deal ID :' . $deal->code . ' & PO: ' . $po->doc_number . ')';
                $scd->status = $deal->status;
                $scd->estimated_close_date = $deal->estimated_close_date;
                $scd->created_by = Auth::user()->id;
                $scd->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
                //$scd->company_id = session('logged_session_data.company_id');
                $scd->company_id = $com_id;
                $scd->is_professional_service = $deal->is_professional_service;
                $scd->save();
                $scd->toArray();

                $i = 1;
                foreach ($deal_items as $items) {
                    DB::table('sys_crm_quote_items')->insert([
                        'user_id' => $scd->owner,
                        'deal_id' => $scd->id,
                        'company_id' => $scd->company_id,
                        'currency_id' => $items->currency_id,
                        'customer_type' => $cust->account_type,
                        'quote_validity' => '3 Weeks',
                        'payment_terms' => $po->payment_terms,
                        'delivery_date' => $po->delivery_date,
                        'payment_terms_txt' => $po->payment_terms2,
                        'delivery_time' => '3 Weeks',
                        'product_id' => $items->product_id,
                        'qty' => $items->p_qty,
                        'price' => $items->unitprice,
                        'description' => $items->p_description,
                        'discount' => $items->p_discount,
                        'vat' => $items->tax,
                        'cost' => $items->cost,
                        'status' => $items->status,
                        'sort_id' => $i++,
                        'created_by' => Auth::user()->id,
                        'quote_id' => 1,
                    ]);
                }
            }
            return $scd->id;

        } catch (\Throwable $th) {

            return $th;
            return "ERROR";
        }

    }
    public function Internal_Transfer_Deal_Track($deal_id, $po_id)
    {
        try {
            $deal = SysCrmDeals::where('id', $deal_id)->first();
            $deal_items = SysCrmQuoteItems::where('deal_id', $deal_id)->first();
            if (!isset($deal_items)) {
                return "TRACK";
            }
            $po = SysPurchaseOrder::where('id', $po_id)->first();

            $track = new SysCrmDealTrack();
            $track->deal_id = $deal_id;
            $track->delivery_date = date('Y-m-d', strtotime($deal->estimated_close_date));
            $track->payment_terms = $deal_items->payment_terms;

            if ($deal_items->payment_terms == 1) {
                $track->payment_mode = 1;
            } else {
                $track->payment_mode = 2;
            }

            //$track->payment_mode_sec = $request->payment_mode_sec;

            $track->lpo = "";
            $track->purchease_quote = "";
            $track->cheque_copy = "";
            $track->technical = 0;
            $track->technical_detail = "";
            $track->remarks = "Deal Track created from Purchase Order (Internal Transfer)";
            $track->reference_no = $po->doc_number;
            $track->reference_date = $deal->date;

            $track->purchease_approval = 1;
            $track->invoice_approval = 1;
            $track->delivery_approval = 1;
            $track->receivables_approval = 1;
            //$track->start_date = $request->start_date;
            //$track->end_date = $request->end_date;

            $track->accounts = 1;
            $track->sales = 1;
            $track->purchease = 0;
            $track->invoice = 0;
            $track->delivery = 0;
            $track->receivables = 0;
            $track->tech = 0;
            $track->created_by = Auth::user()->id;
            $track->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
            $track->company_id = $deal->company_id;
            $track->save();
            $track->toArray();

            DB::table('sys_crm_deal_track_approval_accounts')->insert(
                [
                    'deal_track_id' => $track->id,
                    'deal_id' => $deal_id,
                    'customer_status' => 1,
                    'credit_limit' => 1,
                    'payment_terms' => $deal_items->payment_terms,
                    'pending_payment' => 1,
                    'other' => 1,
                    'remarks' => 'ADVANCE PAYMENT',
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00')->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now('+04:00')->format('Y-m-d H:i:s'),
                ]
            );

            DB::table('sys_crm_deal_track_approval_sales')->insert(
                [
                    'deal_track_id' => $track->id,
                    'deal_id' => $deal_id,
                    'margin' => 1,
                    'stock' => 1,
                    'purcease_quote' => 1,
                    'other' => 1,

                    'purchase_approval' => 1,
                    'invoice_approval' => 1,
                    'delivery_approval' => 1,
                    'receivables_approval' => 1,

                    'remarks' => $deal->deal_name,
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00')->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now('+04:00')->format('Y-m-d H:i:s'),
                ]
            );


            return "TRACK";

        } catch (\Throwable $th) {
            return $th;
        }
    }


    public function generateDeal_update($po_id)
    {
        try {
            $com = session('logged_session_data.company_id');
            $po = SysPurchaseOrder::where('id', $po_id)->first();
            $po_item = SysPurchaseOrderItems::where('po_id', $po_id)->get();

            $cust = SysCustSuppl::select('sys_cust_suppl.*')
                ->join('sys_chartofaccounts as ca', 'ca.account_code', 'sys_cust_suppl.code')
                ->where('ca.id', $po->shipping_supplier)->first();
            //return $cust;

            $scd = SysCrmDeals::find($po->deal_id);
            $scd->date = date('Y-m-d', strtotime($po->po_date));
            $scd->deal_name = $po->doc_number;
            $scd->cust_id = $cust->id;
            $scd->cust_name = $cust->first_name . ' ' . $cust->last_name;
            $scd->cust_no = $cust->contcat_number;
            $scd->cust_email = $cust->email;
            $scd->deal_value = $po_item->sum('taxableamount');
            $scd->deal_currency = $po->currency;
            $scd->source = 'Mail';
            $scd->source_o = '';
            $scd->tags = '';
            $scd->stage = 4;
            $scd->owner = $po->created_by;
            $scd->doc = '';
            $scd->isproject = 2;
            $scd->designation = $cust->designation;
            $scd->address = $cust->address;

            $scd->delivery_company = $cust->name;
            $scd->delivery_name = $po->shipping_name;
            $scd->delivery_number = $po->shipping_contact_no;
            $scd->delivery_email = $cust->email;
            $scd->delivery_address = $po->shipping_address;

            $scd->note = 'Deal Created from Purchase Order No: ' . $po->doc_number;
            $scd->status = 1;
            $scd->estimated_close_date = date('Y-m-d', strtotime($po->delivery_date));
            $scd->created_by = Auth::user()->id;
            $scd->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
            $scd->company_id = $com;
            $scd->is_professional_service = 0;
            //$scd->sales_invoice_id = $siv->id;
            $scd->save();
            $scd->toArray();

            $i = 1;
            DB::table('sys_crm_quote_items')->where('deal_id', $po->deal_id)->delete();
            foreach ($po_item as $items) {
                DB::table('sys_crm_quote_items')->insert([
                    'user_id' => $scd->owner,
                    'deal_id' => $scd->id,
                    'company_id' => $scd->company_id,
                    'currency_id' => $po->currency,
                    'customer_type' => $cust->account_type,
                    'quote_validity' => '3 Weeks',
                    'payment_terms' => $po->payment_terms,
                    'delivery_date' => $po->delivery_date,
                    'payment_terms_txt' => $po->payment_terms2,
                    'delivery_time' => '3 Weeks',
                    'product_id' => $items->part_number,
                    'qty' => $items->qty,
                    'price' => $items->unitprice,
                    'description' => $items->description,
                    'discount' => $items->discount,
                    'vat' => $items->tax,
                    'cost' => $items->unitprice,
                    'status' => $items->status,
                    'sort_id' => $i++,
                    'created_by' => Auth::user()->id,
                    'quote_id' => 1,
                ]);
            }

            SysPurchaseOrder::where('id', $po_id)->update(['deal_id' => $scd->id]);

            return ["OK", $scd->id];
        } catch (\Throwable $th) {
            return ["ERROR", $th];
        }
    }
    public function generateGRN_update($po_id)
    {
        try {
            $po = SysPurchaseOrder::where('id', $po_id)->first();
            $po_item = SysPurchaseOrderItems::where('po_id', $po_id)->get();
            $deal_track = SysCrmDealTrack::where('deal_id', $po->deal_id)->first();

            $grn_id = SysPurchaseGRN::where('po_id', $po_id)->first();
            if (!isset($grn_id)) {
                return "NO GRN";
            }

            $grn = SysPurchaseGRN::find($grn_id->id);
            $grn->grn_date = date('Y-m-d', strtotime($po->po_date));

            $grn->po_id = $po_id;
            $grn->vendors = $po->vendors;
            $grn->currency = $po->currency;
            $grn->lpo_number = $po->doc_number;
            $grn->lpo_date = $po->po_date;
            $grn->payment_terms = $po->payment_terms;

            $grn->bill_number = $po->bill_number;
            $grn->bill_date = $po->bill_date;
            $grn->awbno = $po->awbno;
            $grn->boeno = $po->boeno;
            $grn->reference = $po->narration;

            $grn->warehouse = "";
            $grn->narration = $po->reference;
            $grn->deal_id = $po->deal_id;
            $grn->sales_person = $po->sales_person;

            $grn->status = 1;
            $grn->created_by = Auth::user()->id;
            $grn->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
            $grn->company_id = session('logged_session_data.company_id');
            $grn->save();

            SysPurchaseGRNItems::where('grn_id', $grn_id->id)->delete();
            SysItemStock::where('grn_id', $grn_id->id)->delete();
            for ($i = 0; $i < count($po_item); $i++) {

                $item = SmItem::select('part_number')->where('id', $po_item[$i]->part_number)->first();
                $grnitms = new SysPurchaseGRNItems();
                $grnitms->grn_id = $grn->id;
                $grnitms->po_id = $po_id;
                $grnitms->part_no = $po_item[$i]->part_number;
                $grnitms->part_number = $item->part_number;
                $grnitms->tax = $po_item[$i]->tax;
                $grnitms->qty = $po_item[$i]->qty;
                $grnitms->unitprice = $po_item[$i]->unitprice;
                $grnitms->value = $po_item[$i]->value;
                $grnitms->discount = $po_item[$i]->discount;
                $grnitms->fright = $po_item[$i]->fright;
                $grnitms->customcharges = $po_item[$i]->customcharges;
                //$grnitms->customcharges = $request->customcharges[$i];
                $grnitms->taxableamount = $po_item[$i]->taxableamount;
                $grnitms->vatamount = $po_item[$i]->vatamount;
                $grnitms->status = 1;
                $grnitms->save();


                $str_arr = explode(",", $po_item[$i]->serialno);
                foreach ($str_arr as $srl) {
                    $values = array('grn_id' => $grn->id, 'part_no' => $po_item[$i]->part_number, 'srl_no' => $srl);
                    DB::table('sys_purchase_grn_items_srlno')->insert($values);
                }

                DB::table('sys_purchase_order_items')->where('po_id', $po_id)->where('part_number', $po_item[$i]->part_number)
                    ->update(['grn_qty' => $po_item[$i]->qty]);

                $discount = ($po_item[$i]->discount === '' ? '0.00' : $po_item[$i]->discount);
                $istock = new SysItemStock();
                $istock->grn_id = $grn->id;
                $istock->account_id = $po->vendors;
                $istock->partno = $po_item[$i]->part_number;
                $istock->qty_in = $po_item[$i]->qty;
                $istock->price_in = ($po_item[$i]->value - $discount) / $po_item[$i]->qty;
                $istock->refno = $grn->lpo_number;
                $istock->doc_number = $grn->doc_number;
                $istock->doc_date = $grn->grn_date;
                $istock->deal_id = $grn->deal_id;
                $istock->slno = $po_item[$i]->serialno;
                $istock->status = 1;
                $istock->created_by = Auth::user()->id;
                $istock->company_id = session('logged_session_data.company_id');
                $istock->currency_id = $po->currency;
                $istock->save();

            }

            DB::table('sys_purchase_order')->where('id', $po_id)->update(['grn_status' => 1]);

            return "GRN";

        } catch (\Throwable $th) {
            return $th;
        }
    }
    public function generatePI_update($po_id)
    {
        try {
            $po = SysPurchaseOrder::where('id', $po_id)->first();
            $po_item = SysPurchaseOrderItems::where('po_id', $po_id)->get();
            $deal_track = SysCrmDealTrack::where('deal_id', $po->deal_id)->first();
            $grn = SysPurchaseGRN::where('po_id', $po_id)->first();

            $pi_id = SysPurchaseInvoice::where('ref_po_id', $po_id)->first();
            if (!isset($pi_id)) {
                return "NO PI";
            }

            $pi = SysPurchaseInvoice::find($pi_id->id);
            $pi->pi_date = date('Y-m-d', strtotime($po->po_date));
            $pi->ref_po_id = $po_id;

            if (isset($grn)) {
                $pi->ref_grn_id = $grn->id;
                $pi->grn_no = $grn->doc_number;
                $pi->grn_date = $grn->grn_date;
                $pi->bill_number = $grn->bill_number;
                $pi->bill_date = date('Y-m-d', strtotime($grn->bill_date));
                $pi->awbno = $grn->awbno;
                $pi->reference = $grn->narration;
                $pi->warehouse = $grn->warehouse;
            }

            $pi->vendors = $po->vendors;
            $pi->currency = $po->currency;
            $pi->lpo_number = $po->doc_number;
            $pi->lpo_date = date('Y-m-d', strtotime($po->po_date));
            $pi->payment_terms = $po->payment_terms;
            $pi->payment_terms2 = $po->payment_terms2;


            $pi->sales_person = $po->sales_person;
            $pi->narration = $po->reference;
            $pi->deal_id = $po->deal_id;

            $pi->shipping_name = $po->shipping_name;
            $pi->shipping_address_1 = $po->shipping_address_1;
            $pi->shipping_address_2 = $po->shipping_address_2;
            $pi->shipping_contact_no = $po->shipping_contact_no;

            $pi->supplier_type = $po->supplier_type;
            $pi->purchase_type = $po->purchase_type;
            $pi->supplier_country = $po->supplier_country;
            $pi->supplier_state = $po->supplier_state;
            $pi->status = 1;
            $pi->company_id = session('logged_session_data.company_id');
            $pi->created_by = Auth::user()->id;
            $pi->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
            $pi->save();
            $pi->toArray();


            $total_tax_amount = $po_item->sum('taxableamount');
            $total_vat_amount = $po_item->sum('vatamount');


            SysPurchaseInvoiceItems::where('pi_id', $pi->id)->delete();
            DB::table('sys_purchase_invoice_cf_charges')->where('pi_id', $pi->id)->delete();
            DB::table('sys_chartofaccounts_transaction')->where('transaction_type', 'purchaseinvoice')->where('transaction_id', $pi->id)->delete();



            //Supplier account cr
            SysHelper::trn_chartof_accounts_transaction($pi->vendors, $pi->id, $pi->doc_number, $pi->pi_date, 'purchaseinvoice', '0.00', ($total_tax_amount + $total_vat_amount), '', 1, 0, "", 1);

            //Purchase account dr 
            $purchase_account_id = SysHelper::get_purchase_account_id();
            SysHelper::trn_chartof_accounts_transaction($purchase_account_id, $pi->id, $pi->doc_number, $pi->pi_date, 'purchaseinvoice', ($total_tax_amount), '0.00', '', 1, 0, "", 1);

            //vat account dr 
            $purchase_vat_account_id = SysHelper::get_purchase_vat_account_id();
            SysHelper::trn_chartof_accounts_transaction($purchase_vat_account_id, $pi->id, $pi->doc_number, $pi->pi_date, 'purchaseinvoice', ($total_vat_amount), '0.00', '', 1, 0, "", 1);

            for ($i = 0; $i < count($po_item); $i++) {
                if ($po_item[$i]->part_number != "" && $po_item[$i]->qty != "" && $po_item[$i]->unitprice != "") {
                    $pii = new SysPurchaseInvoiceItems();
                    $pii->pi_id = $pi->id;
                    $pii->ref_po_id = $po_id;
                    $pii->part_number = $po_item[$i]->part_number;
                    $pii->tax = $po_item[$i]->tax;
                    $pii->qty = $po_item[$i]->qty;
                    $pii->unitprice = $po_item[$i]->unitprice;
                    $pii->value = $po_item[$i]->value;
                    $pii->discount = ($po_item[$i]->discount === '' ? '0.00' : $po_item[$i]->discount);
                    $pii->fright = $po_item[$i]->fright;
                    $pii->customcharges = $po_item[$i]->customcharges;
                    $pii->taxableamount = ($po_item[$i]->taxableamount === '' ? '0.00' : $po_item[$i]->taxableamount);
                    $pii->vatamount = ($po_item[$i]->vatamount === '' ? '0.00' : $po_item[$i]->vatamount);
                    $pii->status = 1;
                    $pii->created_by = Auth::user()->id;
                    $pii->save();

                }
            }

            $po = SysPurchaseInvoiceItems::where('pi_id', $pi->id)->sum('qty');
            $gr = SysPurchaseGRNItems::where('grn_id', $pi->ref_grn_id)->sum('qty');
            if ($po == $gr) {
                DB::table('sys_purchase_grn')->where('id', $pi->ref_grn_id)->update(['grn_status' => 0]);
            }


            return "PI";

        } catch (\Throwable $th) {
            return $th;
        }
    }

    function add_attachment(Request $request)
    {
        try {
            $selected_file = "";
            if ($request->hasFile('att_file') && $request->file('att_file')->isValid()) {
                // Store the file (e.g., in the 'uploads' folder)
                if ($request->file('att_file') != "") {
                    $file = $request->file('att_file');
                    $selected_file = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                    $file->move('public/uploads/product_upload/', $selected_file);
                    $selected_file = 'public/uploads/product_upload/' . $selected_file;
                }
            }


            $data[] = [
                'cart_id' => session('logged_session_data.cart_id'),
                'doc_id' => $request->doc_id,
                'doc_file' => $selected_file,
                'doc_date' => date('Y-m-d', strtotime($request->att_date)),
                'doc_name' => $request->doc_name,
                'status' => 1,
                'created_by' => Auth::user()->id,
                'created_at' => Carbon::now('+04:00'),
                'company_id' => session('logged_session_data.company_id'),
            ];

            DB::table('sys_purchase_order_att')->insert($data);


            if ($request->doc_id == 0) {
                $ret = DB::table('sys_purchase_order_att')->where('doc_id', $request->doc_id)->where('cart_id', session('logged_session_data.cart_id'))->where('company_id', session('logged_session_data.company_id'))->get();
            } else {
                $ret = DB::table('sys_purchase_order_att')->where('doc_id', $request->doc_id)->where('company_id', session('logged_session_data.company_id'))->get();
            }
            if (count($ret) > 0) {
                return json_encode(array('data' => $ret));
            } else {
                $ret = [];
                return json_encode(array('data' => $ret));
            }
        } catch (\Exception $e) {
            //$ret = 'ERROR';
            $ret = $e;
            return json_encode(array('data' => $ret));
        }
    }
    function view_attachment(Request $request)
    {
        try {

            if ($request->doc_id == 0) {
                $ret = DB::table('sys_purchase_order_att')->where('doc_id', $request->doc_id)->where('cart_id', session('logged_session_data.cart_id'))->where('company_id', session('logged_session_data.company_id'))->get();
            } else {
                $ret = DB::table('sys_purchase_order_att')->where('doc_id', $request->doc_id)->where('company_id', session('logged_session_data.company_id'))->get();
            }
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
    function delete_attachment(Request $request)
    {
        try {
            DB::table('sys_purchase_order_att')->where('id', $request->id)->delete();

            if ($request->doc_id == 0) {
                $ret = DB::table('sys_purchase_order_att')->where('doc_id', $request->doc_id)->where('cart_id', session('logged_session_data.cart_id'))->where('company_id', session('logged_session_data.company_id'))->get();
            } else {
                $ret = DB::table('sys_purchase_order_att')->where('doc_id', $request->doc_id)->where('company_id', session('logged_session_data.company_id'))->get();
            }

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
}
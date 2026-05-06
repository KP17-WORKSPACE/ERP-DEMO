<?php

namespace App\Http\Controllers;

use App\SmItem;
use App\SmStaff;
use App\SysCompany;
use App\SysPurchaseInvoice;
use App\SysPurchaseInvoiceItems;
use App\SysPurchaseInvoiceAttachment;
use App\SysPurchaseInvoiceCFCharges;
use App\SysPurchaseGrnCfCharges;
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
use App\SmSupplier;
use App\SysChartofAccounts;
use App\SysChartofAccountsTransaction;
use App\SysCountries;
use App\SysCrmDeals;
use App\SysCurrency;
use App\SysCurrencyRate;
use App\SysCustSuppl;
use App\SysCustSupplAddressbook;
use App\SysHelper;
use App\SysItemStock;
use App\SysLedgerEntries;
use App\SysPayment;
use App\SysPaymentAdjustments;
use App\SysPurchaseGRNItems;
use App\SysPurchaseOrder;
use App\SysPurchaseOrderItems;
use App\SysPurchaseReturn;
use App\SysPurchaseReturnAdjestment;
use App\SysPurchaseType;
use App\SysStates;
use App\User;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Hash;

use function GuzzleHttp\Promise\exception_for;

class SysPurchaseInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $id = null)
    {

        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $supplier_list = SysHelper::get_supplier_list($company_id);
            $sales_person_list = SysHelper::get_sales_persons();
            $currency = SysCurrencySettings::select('id', 'code', 'ex_rate')->get();

            $ctrl_doc_no = "";
            $ctrl_supplier = "";
            $ctrl_customer = "";
            $ctrl_grn_no = "";
            $ctrl_po_no = "";
            $ctrl_prt_no = "";
            $ctrl_date = "";
            $ctrl_date2 = "";
            $ctrl_sales_person = "";
            $ctrl_attachments = "";
            $ctrl_currency = "";



            $adj_list = SysPaymentAdjustments::select('bi_doc_number', 'bi_doc_no', 'bi_total', 'bi_paid', 'bi_balance', 'bi_amount')->wherein('company_id', $company_id)->get();

            $query = SysPurchaseInvoice::select(DB::raw('sys_purchase_invoice.*, (SELECT GROUP_CONCAT(doc_file) FROM sys_purchase_invoice_att WHERE doc_id = sys_purchase_invoice.id) AS attach, (SELECT GROUP_CONCAT(doc_number) FROM sys_purchase_grn WHERE id=sys_purchase_invoice.ref_grn_id) AS grn_no, (SELECT GROUP_CONCAT(doc_number) FROM sys_purchase_return WHERE pi_id=sys_purchase_invoice.id) AS prt_no, (SELECT SUM(taxableamount)+SUM(vatamount) AS amount FROM sys_purchase_invoice_items WHERE pi_id=sys_purchase_invoice.id) AS amount, sys_purchase_invoice.deal_id AS code'), DB::raw('(SELECT SUM(vatamount) FROM sys_purchase_invoice_items WHERE pi_id = sys_purchase_invoice.id) AS total_vatamount'), DB::raw('(SELECT SUM(taxableamount) FROM sys_purchase_invoice_items WHERE pi_id = sys_purchase_invoice.id) AS total_taxableamount'));
            if (SysHelper::get_pagination_post($request)) {
                if ($request->documents_number != "") {
                    $query->where('doc_number', 'like', '%' . $request->documents_number . '%');
                    $ctrl_doc_no = $request->documents_number;
                }
                if ($request->supplier != "") {
                    $query->where('vendors', $request->supplier);
                    $ctrl_supplier = $request->supplier;
                }
                if ($request->customer != "") {
                    $query->where('narration', 'like', '%' . $request->customer . '%');
                    $ctrl_customer = $request->customer;
                }
                if ($request->currency != "") {
                    $query->where('currency', $request->currency);
                    $ctrl_currency = $request->currency;
                }
                if ($request->purchase_order_number != "") {
                    $po_nos = SysPurchaseInvoice::join('sys_purchase_order', 'sys_purchase_order.id', 'sys_purchase_invoice.ref_po_id')
                        ->where('sys_purchase_order.doc_number', 'like', '%' . $request->purchase_order_number . '%')->pluck('sys_purchase_invoice.doc_number');
                    $query->wherein('doc_number', $po_nos);
                    $ctrl_po_no = $request->purchase_order_number;
                }
                if ($request->grn_number != "") {
                    $grn_nos = SysPurchaseInvoice::join('sys_purchase_grn', 'sys_purchase_grn.id', 'sys_purchase_invoice.ref_grn_id')
                        ->where('sys_purchase_grn.doc_number', 'like', '%' . $request->grn_number . '%')->pluck('sys_purchase_invoice.doc_number');
                    $query->wherein('doc_number', $grn_nos);
                    $ctrl_grn_no = $request->grn_number;
                }
                if ($request->purchase_return_number != "") {
                    $prt_nos = SysPurchaseInvoice::join('sys_purchase_return', 'sys_purchase_return.pi_id', 'sys_purchase_invoice.id')
                        ->where('sys_purchase_return.doc_number', 'like', '%' . $request->purchase_return_number . '%')->pluck('sys_purchase_invoice.doc_number');
                    $query->wherein('doc_number', $prt_nos);
                    $ctrl_prt_no = $request->purchase_return_number;
                }
                // if ($request->amount != "") {                    
                //     $amt_nos = SysChartofAccountsTransaction::where('transaction_type', 'salesreturn')->where('debit_amount',$request->amount)->pluck('transaction_no');
                //     $query->wherein('doc_number',$amt_nos);
                // }                
                if ($request->from_date != "" && $request->to_date != "") {
                    $request->from_date = SysHelper::normalizeToYmd($request->from_date);
                    $request->to_date = SysHelper::normalizeToYmd($request->to_date);
                    $query->whereBetween('pi_date', [$request->from_date, $request->to_date]);
                    $ctrl_date = $request->from_date;
                    $ctrl_date2 = $request->to_date;
                }
                if ($request->from_date != "" && $request->to_date == "") {
                    $request->from_date = SysHelper::normalizeToYmd($request->from_date);
                    $query->where('pi_date', '>=', $request->from_date);
                    $ctrl_date = $request->from_date;
                }
                if ($request->from_date == "" && $request->to_date != "") {
                    $request->to_date = SysHelper::normalizeToYmd($request->to_date);
                    $query->where('pi_date', '<=', $request->to_date);
                    $ctrl_date2 = $request->to_date;
                }
                if ($request->sales_person != "") {
                    $query->where('sales_person', $request->sales_person);
                    $ctrl_sales_person = $request->sales_person;
                }
                if ($request->attachments == 1) {
                    $att_nos = DB::table('sys_purchase_invoice_att')->wherein('company_id', $company_id)->pluck('doc_id');
                    $query->wherein('id', $att_nos);
                    $ctrl_attachments = 1;
                }
                if ($request->attachments == 2) {
                    $att_nos = DB::table('sys_purchase_invoice_att')->wherein('company_id', $company_id)->pluck('doc_id');
                    $query->wherenotin('id', $att_nos);
                    $ctrl_attachments = 2;
                }
            } else {

            }
            $query->wherein('company_id', $company_id);
            $query->orderby('id', 'desc');
            $purchaseinvoice = $query->paginate(50);

            $active_id = $id;
            $data = [];


            $action = false;
            $editData = [];

            $addData = [];




            if ($request->has('pi_action')) {
                $poAction = $request->input('pi_action');

                if ($poAction === 'add') {
                    $action = 'add';

                    $addData = $this->create(); // Get all data for adding


                } elseif ($poAction === 'edit') {
                    $action = 'edit';
                    $editData = $this->edit($active_id); // Get all data for editing
                }
            } else {
                if ($id) {
                    $data = $this->get_pi_pdf_data($id);
                } else {
                    $firstRecord = $purchaseinvoice->first();
                    if ($firstRecord) {
                        $active_id = $firstRecord->id;
                        $data = $this->get_pi_pdf_data($firstRecord->id);
                    }
                }
            }



            //return $purchaseorder;
            return view('backEnd/purchaseinvoice/purchase_invoice_list', compact('purchaseinvoice', 'supplier_list', 'adj_list', 'sales_person_list', 'currency', 'data', 'active_id', 'action', 'editData', 'ctrl_date', 'ctrl_date2', 'ctrl_sales_person', 'ctrl_attachments', 'ctrl_doc_no', 'ctrl_supplier', 'ctrl_customer', 'ctrl_grn_no', 'ctrl_po_no', 'ctrl_prt_no', 'ctrl_currency', 'addData'));
        } catch (\Exception $e) {
            dd($e);
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];

            $currency = SysCurrencySettings::select('id', 'code')->get();
            $paymentterms = SysPaymentTerms::select('id', 'title')->orderby('title', 'asc')->get();
            $company = SysCompany::find(session('logged_session_data.company_id'));
            $vendors = SysHelper::get_supplier_list($company_id);
            $salesman = SysHelper::get_sales_persons();

            $customs_freight_account = SysHelper::get_customs_freight_accounts_for_purchase($company_id);

            $suppliertype = SysSupplierType::orderby('title', 'asc')->get();
            $purchasetype = SysPurchaseType::orderby('title', 'asc')->get();
            $countries = SysCountries::orderby('name', 'asc')->get();
            $states = SysStates::orderby('name', 'asc')->get();

            $adj_list = SysPaymentAdjustments::select('bi_doc_number', 'bi_doc_no', 'bi_total', 'bi_paid', 'bi_balance', 'bi_amount')->wherein('company_id', $company_id)->get();
            $query = SysPurchaseInvoice::select(DB::raw('sys_purchase_invoice.*, (SELECT GROUP_CONCAT(doc_file) FROM sys_purchase_invoice_att WHERE doc_id = sys_purchase_invoice.id) AS attach, (SELECT GROUP_CONCAT(doc_number) FROM sys_purchase_grn WHERE id=sys_purchase_invoice.ref_grn_id) AS grn_no, (SELECT GROUP_CONCAT(doc_number) FROM sys_purchase_return WHERE pi_id=sys_purchase_invoice.id) AS prt_no, (SELECT SUM(taxableamount)+SUM(vatamount) AS amount FROM sys_purchase_invoice_items WHERE pi_id=sys_purchase_invoice.id) AS amount, sys_purchase_invoice.deal_id AS code'), DB::raw('(SELECT SUM(vatamount) FROM sys_purchase_invoice_items WHERE pi_id = sys_purchase_invoice.id) AS total_vatamount'), DB::raw('(SELECT SUM(taxableamount) FROM sys_purchase_invoice_items WHERE pi_id = sys_purchase_invoice.id) AS total_taxableamount'));
            $query->wherein('company_id', $company_id);
            $query->orderby('id', 'desc');
            $purchaseinvoice = $query->get();

            $customer_reference_list = SysHelper::get_customer_list_deal_lead_all_role();
            $customer = SysHelper::get_customer_list($company_id);



            return compact('purchaseinvoice', 'adj_list', 'currency', 'vendors', 'paymentterms', 'suppliertype', 'purchasetype', 'countries', 'states', 'customs_freight_account', 'company', 'salesman', 'customer_reference_list', 'customer');
            // return view('backEnd.purchaseinvoice.manage_purchase_invoice', compact('purchaseinvoice','adj_list','currency', 'vendors', 'paymentterms','suppliertype','purchasetype','countries','states','customs_freight_account','company','salesman'));
        } catch (\Exception $e) {

            Toastr::error('Operation Failed', 'Failed');
            return [];
        }
    }

    function purchaseorderpending(Request $request)
    {
        try {
            $ret = SysPurchaseOrder::where('vendors', $request->id)->get();
            return json_encode(array('data' => $ret));

        } catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }
    function purchaseorderpendingitemlist(Request $request)
    {
        try {
            $ret = DB::select("SELECT po.id,itm.id AS part_id,itm.part_number,po.qty AS po_qty,itmstock.qty AS os_qty,
            (SELECT sum(qty) FROM sys_purchase_invoice_items WHERE ref_po_id = po.po_id AND part_number = po.part_number) pro_qty
            FROM sys_purchase_order_items po
            INNER JOIN sm_items itm ON itm.id=po.part_number
            INNER JOIN sys_item_stock itmstock ON itmstock.partno=po.part_number
            WHERE po_id = '" . $request->po_id . "'");

            /*$ret = SysQuotationsItems::select('sys_quotations_items.id','sm_items.part_number','sys_quotations_items.qty as qt_qty','sys_item_stock.qty as os_qty','sys_proforma_invoice_items.qty as pro_qty')
                    ->join('sm_items','sm_items.id','sys_quotations_items.part_number')
                    ->join('sys_item_stock','sys_item_stock.partno','sys_quotations_items.part_number')
                    ->leftjoin('sys_proforma_invoice_items','sys_proforma_invoice_items.ref_qt_id','sys_quotations_items.qt_id')
                    ->where('qt_id',$request->qt_id)->get();*/

            return response()->json([$ret]);

        } catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }

    public function store(Request $request)
    {
        try {

        
       
            DB::beginTransaction();
            if (count(array_filter($request->part_number)) > 0 && count(array_filter($request->qty)) > 0 && count(array_filter($request->unitprice)) > 0) {

                $pi = new SysPurchaseInvoice();
                $pi->doc_number = SysHelper::get_new_code('sys_purchase_invoice', 'PI', 'doc_number');

                $pi->pi_date = Carbon::createFromFormat('d/m/Y', $request->pi_date)->format('Y-m-d');


                if($request->ref_po_id){
                    $poArray = array_map('trim', explode(',', $request->ref_po_id));
                    $pi->ref_po_id = implode(',', $poArray);
                }

                // $pi->ref_po_id = $request->po_id ?: 0;

                if($request->grn_id){
                    $grnArray = array_map('trim', explode(',', $request->grn_id));
                    $pi->ref_grn_id = implode(',', $grnArray);
                }

                // $pi->ref_grn_id = $request->grn_id ?: 0;


                $pi->vendors = $request->vendors;
                $pi->currency = $request->currency;
                // $pi->narration = $request->narration;

                if ($request->lpo_number) {
                    $lpoArray = array_map('trim', explode(',', $request->lpo_number));
                    $pi->lpo_number = implode(',', $lpoArray);
                }
                // $pi->lpo_number = $request->lpo_number;

                $pi->lpo_date = Carbon::createFromFormat('d/m/Y', $request->lpo_date)->format('Y-m-d');

                if ($request->bill_number) {
                    $billArray = array_map('trim', explode(',', $request->bill_number));
                    $pi->bill_number = implode(',', $billArray);
                }




                $pi->bill_date = Carbon::createFromFormat('d/m/Y', $request->bill_date)->format('Y-m-d');
                $pi->payment_terms = $request->payment_terms;
                $pi->payment_terms2 = $request->payment_terms2;

                $pi->awbno = $request->awbno;
                $pi->boeno = $request->boeno;
                $pi->reference = $request->reference;
                $pi->warehouse = $request->warehouse;

                $pi->vat_percent = $request->vat_percent ?: null;
                $pi->vat_number = $request->vat_number;

                if ($request->has('ref_company_id')) {
                    $pi->ref_company_id = implode(',', $request->ref_company_id);
                } else {
                    $pi->ref_company_id = null;
                }

                //  $pi->ref_company_id = $request->ref_company_id;

                if ($request->grn_no) {
                    $grnArray = array_map('trim', explode(',', $request->grn_no));
                    $pi->grn_no = implode(',', $grnArray);

                }

              

                $pi->grn_date = Carbon::createFromFormat('d/m/Y', $request->grn_date)->format('Y-m-d');

                // $pi->sales_person = $request->sales_person;

                $sales_person = $request->sales_person;

                if (!is_null($sales_person) && $sales_person !== '') {

                    if (is_numeric($sales_person)) {
                        // Selected from dropdown (user ID)
                        $pi->sales_person = (int) $sales_person;
                        $pi->sales_person_name = null;
                    } else {
                        // Manually entered name
                        $pi->sales_person = null;
                        $pi->sales_person_name = trim($sales_person);
                    }
                }


                $pi->narration = $request->narration;
                $pi->deal_id = SysHelper::get_dealid_from_code_list($request->deal_id);

                // $pi->shipping_name = $request->shipping_name;
                // $pi->shipping_address_1 = $request->shipping_address_1;
                // $pi->shipping_address_2 = $request->shipping_address_2;
                // $pi->shipping_contact_no = $request->shipping_contact_no;
  
                $pi->shipping_supplier = $request->shipping_supplier;
                $pi->shipping_name = $request->shipping_name;
                $pi->shipping_email = $request->shipping_email;
                $pi->shipping_contact_no = $request->shipping_contact_no;
                $pi->shipping_address_1 = $request->shipping_address_1;


                $pi->supplier_type = $request->supplier_type;
                $pi->purchase_type = $request->purchase_type;
                $pi->supplier_country = $request->supplier_country;
                $pi->supplier_state = $request->supplier_state;
                $pi->status = 1;
                $pi->company_id = session('logged_session_data.company_id');
                $pi->created_by = Auth::user()->id;
                $pi->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
                $pi->save();
                $pi->toArray();



                $total_tax_amount = array_sum(array_map(function ($value) {
                    return (float) str_replace(',', '', $value);
                }, $request->taxableamount ?? []));
                $total_vat_amount = array_sum(array_map(function ($value) {
                    return (float) str_replace(',', '', $value);
                }, $request->vatamount ?? []));
                //$total_cfc_amount = array_sum($request->cfc_amount);

                // continue processing without debugging dump

          

      

                //Supplier account cr
                SysHelper::trn_chartof_accounts_transaction($request->vendors, $pi->id, $pi->doc_number, $pi->pi_date, 'purchaseinvoice', '0.00', ($total_tax_amount + $total_vat_amount), '', 1, 0, "", 1);

                //Purchase account dr 
                $purchase_account_id = SysHelper::get_purchase_account_id();
                SysHelper::trn_chartof_accounts_transaction($purchase_account_id, $pi->id, $pi->doc_number, $pi->pi_date, 'purchaseinvoice', ($total_tax_amount), '0.00', '', 1, 0, "", 1);

                //vat account dr 
                $purchase_vat_account_id = SysHelper::get_purchase_vat_account_id();
                SysHelper::trn_chartof_accounts_transaction($purchase_vat_account_id, $pi->id, $pi->doc_number, $pi->pi_date, 'purchaseinvoice', ($total_vat_amount), '0.00', '', 1, 0, "", 1);



                for ($i = 0; $i < count($request->part_number); $i++) {
                    if ($request->part_number[$i] != "" && $request->qty[$i] != "" && $request->unitprice[$i] != "") {
                        $pii = new SysPurchaseInvoiceItems();
                        $pii->pi_id = $pi->id;
                        
                        $pii->ref_po_id = $request->po_id ?: 0;


                         if (isset($request->grn_id_main[$i])) {
                                $pii->grn_id = $request->grn_id_main[$i] ?: 0;
                            } else {
                                $pii->grn_id = null;
                            }

                        $pii->part_number = $request->part_number[$i];
                        $pii->tax = $request->tax[$i];
                  
                        $pii->qty = (float) str_replace(',', '', $request->qty[$i] ?? 0);
                        $pii->unitprice = (float) str_replace(',', '', $request->unitprice[$i] ?? 0);
                        $pii->value = (float) str_replace(',', '', $request->value[$i] ?? 0);
                        $pii->discount = (float) str_replace(',', '', $request->discount[$i] ?? 0);
                        $pii->fright = (float) str_replace(',', '', $request->fright[$i] ?? 0);
                        $pii->customcharges = (float) str_replace(',', '', $request->customcharges[$i] ?? 0);
                        $pii->taxableamount = (float) str_replace(',', '', $request->taxableamount[$i] ?? 0);
                        $pii->vatamount = (float) str_replace(',', '', $request->vatamount[$i] ?? 0);
                        $pii->description = $request->description[$i];
                        $pii->status = 1;
                        $pii->sort_id = $request->sort_id[$i];
                        $pii->created_by = Auth::user()->id;
                        $pii->save();



                        if (isset($request->grn_id_main[$i])) {

                        $grn_main_id = $request->grn_id_main[$i];
                        $grn_item_id = $request->grn_item_id[$i];
                       
                        $pi_quantity = DB::table('sys_purchase_grn_items')->where('id', $grn_item_id)->where('part_number', $request->part_number[$i])->sum('pi_qty');

                        //  dd($request->list_po_id,$request->po_itm_id);

                        // dd($request->part_number[$i], $grn_item_id, $pi_quantity, (float) str_replace(',', '', $request->qty[$i] ?? 0));

                        DB::table('sys_purchase_grn_items')->where('part_no', $request->part_number[$i])->where('id', $grn_item_id)
                            ->update(['pi_qty' => $pi_quantity + (float) str_replace(',', '', $request->qty[$i] ?? 0)]);
                            
                    }
            


                        //$product = SysPurchaseOrderItems::where(['po_id' => $request->po_id, 'part_number' => $request->part_id[$i]])->first();

                        /*$issue_qty = $product['issue_qty'];
                        $issue_qty = $issue_qty + 1;
                        $product->issue_qty = $issue_qty;
                        $product->save();*/

                        //SysPurchaseOrderItems::where(['po_id' => $request->po_id, 'part_number' => $request->part_id[$i]])->update(['issue_qty' => ($product->issue_qty + $request->qty[$i])]);

                    }

                }

             
   
                for($i = 0; $i < count($request->cfc_name); $i++) {

                            
                        if($request->grn_id){
                            // delete all cfc for grn it can be multiple comma seperated values
                            $grn_array = array_map('trim', explode(',', $request->grn_id));
                            foreach($grn_array as $grn_id) {
                                SysPurchaseGrnCfCharges::where('grn_id', $grn_id)->delete();
                            }
                        }

                     
                    if($request->cfc_name[$i] !="" && $request->cfc_credit_account[$i] !="" && $request->cfc_amount[$i] !=""){
                        $cfc = new SysPurchaseInvoiceCFCharges();
                        $cfc->pi_id = $pi->id;
                        $cfc->pi_doc_number = $pi->doc_number;
                        $cfc->date = $request->cfc_date[$i] ? SysHelper::normalizeToYmd($request->cfc_date[$i]) : null;
                        $cfc->bill_number = $request->cfc_bill_no[$i];
                        $cfc->cfc_name = $request->cfc_name[$i];
                        $cfc->cfc_credit_account = $request->cfc_credit_account[$i];
                        // remove comma from amount before saving
                        $cfc->cfc_amount = str_replace(',', '', $request->cfc_amount[$i]);
                        $cfc->cfc_remarks = $request->cfc_remarks[$i];
                        $cfc->status = 1;
                        $cfc->created_by = Auth::user()->id;
                        $cfc->save();

                    //Supplier account cr
                    SysHelper::trn_chartof_accounts_transaction($request->cfc_credit_account[$i],$pi->id,$pi->doc_number,$cfc->date,'purchaseinvoice','0.00',str_replace(',', '', $request->cfc_amount[$i]),$request->cfc_remarks[$i],1,0,"",$i+2);

                    //Direct Exp account dr Customs Fright
                    SysHelper::trn_chartof_accounts_transaction($request->cfc_name[$i],$pi->id,$pi->doc_number,$cfc->date,'purchaseinvoice',str_replace(',', '', $request->cfc_amount[$i]),'0.00',$request->cfc_remarks[$i],1,0,"",$i+2);

                    }
                }

                // Keep linked GRN charge table synchronized with PI create/save as well.
                $linkedGrnIds = collect(explode(',', (string) ($pi->ref_grn_id ?? '')))
                    ->map(function ($id) {
                        return (int) trim($id);
                    })
                    ->filter(function ($id) {
                        return $id > 0;
                    })
                    ->unique()
                    ->values();

                if ($linkedGrnIds->count() > 0) {
                    $piCharges = SysPurchaseInvoiceCFCharges::where('pi_id', $pi->id)->get();
                    foreach ($linkedGrnIds as $grnId) {
                        DB::table('sys_purchase_grn_cf_charges')->where('grn_id', $grnId)->delete();
                        foreach ($piCharges as $piCharge) {
                            $grnCharge = new SysPurchaseGrnCfCharges();
                            $grnCharge->grn_id = $grnId;
                            $grnCharge->date = $piCharge->date;
                            $grnCharge->bill_number = $piCharge->bill_number;
                            $grnCharge->cfc_name = $piCharge->cfc_name;
                            $grnCharge->cfc_credit_account = $piCharge->cfc_credit_account;
                            $grnCharge->cfc_amount = $piCharge->cfc_amount;
                            $grnCharge->cfc_remarks = $piCharge->cfc_remarks;
                            $grnCharge->status = 1;
                            $grnCharge->created_by = Auth::user()->id;
                            $grnCharge->save();
                        }
                    }
                }

                $adjData = db::table('sys_purchase_invoice_adjustment_temp')->where('cart_id', session('logged_session_data.cart_id'))
                    ->where('company_id', session('logged_session_data.company_id'))
                    ->where('user_id', Auth::user()->id)
                    ->where('status', 1)
                    ->get();
                if (count($adjData) > 0) {

                    $adj_temp_data = [];
                    $adj_ret_data = [];
                    for ($i = 0; $i < count($adjData); $i++) {
                        if ($adjData[$i]->set_amt != 0) {

                            $rec = SysPayment::where('doc_number', $adjData[$i]->paymentno)->first();
                            if (isset($rec)) {
                                $adjusted_amt = SysPaymentAdjustments::select(db::raw('COALESCE(max(bi_cheque_amount)-sum(bi_paid),0) as adjusted_amt'))->where('bi_doc_number', $adjData[$i]->paymentno)->value('adjusted_amt');
                                if ($rec->mode == 1) {
                                    $transaction_type = "cashpayment";
                                } else {
                                    $transaction_type = "bankpayment";
                                }
                                $currency = $rec->currency;
                                $exe_type = "payment";
                            } else {
                                $adjusted_amt = 0;
                                $rec = SysPurchaseReturn::where('doc_number', $adjData[$i]->paymentno)->first();

                                $currency = $rec->currency;
                                $transaction_type = "";
                                $exe_type = "return";
                            }

                            if ($adjData[$i]->set_amt_act == $adjData[$i]->set_amt) {
                                $bi_balance_to_adjust = 0;
                                $bi_extra_amount = 0;
                            }
                            if ($adjData[$i]->set_amt_act > $adjData[$i]->set_amt) {
                                $bi_balance_to_adjust = 0;
                                $bi_extra_amount = $adjData[$i]->set_amt_act - $adjData[$i]->set_amt;
                            }
                            if ($adjData[$i]->set_amt_act < $adjData[$i]->set_amt) {
                                $bi_balance_to_adjust = $adjData[$i]->set_amt - $adjData[$i]->set_amt_act;
                                $bi_extra_amount = 0;
                            }

                            if ($exe_type == "payment") {
                                $amt_bi_balance_to_adjust = 0;
                                if ($adjData[$i]->set_amt == $adjusted_amt) {
                                    $amt_bi_balance_to_adjust = 0;
                                }
                                if ($adjData[$i]->set_amt > $adjusted_amt) {
                                    $amt_bi_balance_to_adjust = $adjData[$i]->set_amt - $adjusted_amt;
                                }
                                if ($adjData[$i]->set_amt < $adjusted_amt) {
                                    $amt_bi_balance_to_adjust = $adjusted_amt - $adjData[$i]->set_amt;
                                }
                                $bi_total = SysPurchaseInvoiceItems::where('pi_id', $pi->id)->sum('taxableamount') + syspurchaseinvoiceitems::where('pi_id', $pi->id)->sum('vatamount');
                                $adj_temp_data[] = [
                                    'transaction_type' => $transaction_type,
                                    'bi_cheque_amount' => $adjData[$i]->set_amt_act,
                                    'bi_amount_adjusted' => $adjData[$i]->set_amt,
                                    'bi_balance_to_adjust' => $amt_bi_balance_to_adjust,
                                    'bi_extra_amount' => $bi_extra_amount,
                                    'bi_currency' => @$currency,
                                    'bi_doc_number' => $adjData[$i]->paymentno,
                                    'bi_contains' => '',
                                    'bi_doc_no' => $pi->doc_number,
                                    'bi_lpo_no' => '',
                                    'bi_doc_date' => $pi->pi_date,
                                    'bi_total' => $bi_total,
                                    'bi_paid' => $adjData[$i]->set_amt,
                                    'bi_balance' => $amt_bi_balance_to_adjust,
                                    'bi_amount' => $adjData[$i]->set_amt,
                                    'bi_narration' => "Adjusted from PI No: " . $pi->doc_number,
                                    'account_id' => $pi->vendors,
                                    'status' => 1,
                                    'created_by' => Auth::user()->id,
                                    'created_at' => Carbon::now('+04:00'),
                                    'company_id' => session('logged_session_data.company_id'),
                                ];
                            }
                            if ($exe_type == "return") {
                                $adj_ret_data = [
                                    'pri_no' => $adjData[$i]->paymentno,
                                    'piv_no' => $pi->doc_number,
                                    'lpo_no' => @$rec->lpo_number,
                                    'doc_date' => $pi->pi_date,
                                    'total_amount' => $adjData[$i]->set_amt_act,
                                    'paid_amount' => $adjData[$i]->set_amt,
                                    'balance_amount' => $adjData[$i]->set_amt_act - $adjData[$i]->set_amt,
                                    'narration' => 'Adjusted from PI No: ' . $pi->doc_number,
                                    'status' => 1,
                                    'created_by' => Auth::user()->id,
                                ];
                            }
                        }
                    }
                    if (count($adj_temp_data) > 0) {
                        SysPaymentAdjustments::insert($adj_temp_data);
                    }
                    if (count($adj_ret_data) > 0) {
                        SysPurchaseReturnAdjestment::insert($adj_ret_data);
                    }

                    $adjData = db::table('sys_purchase_invoice_adjustment_temp')->where('cart_id', session('logged_session_data.cart_id'))
                        ->where('company_id', session('logged_session_data.company_id'))
                        ->where('user_id', Auth::user()->id)
                        ->where('status', 1)
                        ->delete();


                }


                DB::table('sys_purchase_invoice_att')->where('cart_id', session('logged_session_data.cart_id'))->where('doc_id', 0)->where('company_id', session('logged_session_data.company_id'))->update(['doc_id' => $pi->id]);


                if (isset($request->grn_id_main)) {
                for ($a = 0; $a < count($request->grn_id_main); $a++) {
                     
                    $grn_main_id = $request->grn_id_main[$a] ?: 0;
                    $gr = SysPurchaseGRNItems::where('grn_id', $grn_main_id)->sum('qty');
                    $pi_sum = SysPurchaseInvoiceItems::where('grn_id', $grn_main_id)->where('grn_id', '!=', 0)->sum('qty');

                    if ($gr <= $pi_sum) {
                        DB::table('sys_purchase_grn')->where('id', $grn_main_id)->update(['pi_status' => 0]);
                    }
                }
            }

                // $po = SysPurchaseInvoiceItems::where('pi_id', $pi->id)->sum('qty');
                // $gr = SysPurchaseGRNItems::where('grn_id', $pi->ref_grn_id)->sum('qty');
                // if ($po == $gr) {
                //     DB::table('sys_purchase_grn')->where('id', $pi->ref_grn_id)->update(['grn_status' => 0]);
                // }


                DB::commit();
                Toastr::success('Operation successful', 'Success');
                return redirect('purchase-invoice/' . $pi->id);
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
    //end store method

    public function calcTotal($amt, $tax, $cfc)
    {
        return ($amt / $tax * $cfc) + $amt;
    }

    public function getChargesByGrn(Request $request)
    {
        try {
            $rawIds = $request->input('grn_id', '');
            $grnIds = collect(explode(',', (string) $rawIds))
                ->map(function ($id) {
                    return (int) trim($id);
                })
                ->filter(function ($id) {
                    return $id > 0;
                })
                ->unique()
                ->values();

            if ($grnIds->isEmpty()) {
                return response()->json(['data' => []]);
            }

            $charges = collect();
            foreach ($grnIds as $grnId) {
                $pi = SysPurchaseInvoice::whereRaw('FIND_IN_SET(?, ref_grn_id)', [$grnId])->first();

                if (!is_null($pi)) {
                    $piCharges = SysPurchaseInvoiceCFCharges::where('pi_id', $pi->id)->get();
                    if ($piCharges->count() > 0) {
                        $charges = $charges->merge($piCharges->map(function ($row) {
                            return [
                                'date' => $row->date,
                                'bill_number' => $row->bill_number,
                                'cfc_name' => $row->cfc_name,
                                'cfc_credit_account' => $row->cfc_credit_account,
                                'cfc_amount' => $row->cfc_amount,
                                'cfc_remarks' => $row->cfc_remarks,
                            ];
                        }));
                        continue;
                    }
                }

                $grnCharges = SysPurchaseGrnCfCharges::where('grn_id', $grnId)->get();
                $charges = $charges->merge($grnCharges->map(function ($row) {
                    return [
                        'date' => $row->date,
                        'bill_number' => $row->bill_number,
                        'cfc_name' => $row->cfc_name,
                        'cfc_credit_account' => $row->cfc_credit_account,
                        'cfc_amount' => $row->cfc_amount,
                        'cfc_remarks' => $row->cfc_remarks,
                    ];
                }));
            }

            return response()->json(['data' => $charges->values()]);
        } catch (\Exception $e) {
            return response()->json(['data' => [], 'error' => $e->getMessage()], 500);
        }
    }


    public function show(Request $request, $id)
    {
        try {
            $pi = SysPurchaseInvoice::find($id);
            $pi_items = SysPurchaseInvoiceItems::where('pi_id', '=', $pi->id)->get();
            $pi_att = SysPurchaseInvoiceAttachment::where('pi_id', '=', $pi->id)->get();
            $company = SysCompany::find($pi->company_id);
            $cfcharges = SysPurchaseInvoiceCFCharges::where('pi_id', '=', $pi->id)->get();

            return view('backEnd/purchaseinvoice/purchase_invoice_view', compact('pi', 'pi_items', 'pi_att', 'company', 'cfcharges'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function find(Request $request)
    {
        try {
            $pi = SysPurchaseInvoice::where('doc_number', 'like', '%' . $request->pi_number . '%')->first();

            if ($pi != '') {
                $pi_items = SysPurchaseInvoiceItems::where('pi_id', '=', $pi->id)->get();
                $pi_att = SysPurchaseInvoiceAttachment::where('pi_id', '=', $pi->id)->get();
                $company = SysCompany::find($pi->company_id);
                $cfcharges = SysPurchaseInvoiceCFCharges::where('pi_id', '=', $pi->id)->get();
                return view('backEnd/purchaseinvoice/purchase_invoice_view', compact('pi', 'pi_items', 'pi_att', 'company', 'cfcharges'));
            } else {
                Toastr::error('Invalid PO Number', 'Failed');
                return redirect('purchase-invoice');
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function print($id)
    {
        $pi = SysPurchaseInvoice::find($id);
        if (!empty($pi)) {
            $company = SysCompany::find($pi->company_id);
            $pi_item = SysPurchaseInvoiceItems::where('pi_id', '=', $pi->id)->get();
            //return $pi_item;
            $data = [
                'pi' => $pi,
                'company' => $company,
                'pi_item' => $pi_item,
            ];
            $pdf = PDF::loadView('backEnd.pdf_print.pi_pdf', $data);
            $pdf->setPaper('A4', 'portrait');
            return $pdf->download("purchase_invoice_" . $pi->doc_number . ".pdf");
        } else {
            return "error!!";
            //return view('web.syscom_credit_application_form');
        }
    }
    public function printpreview($id)
    {
        $pi = SysPurchaseInvoice::find($id);
        if (!empty($pi)) {
            $company = SysCompany::find($pi->company_id);
            $pi_item = SysPurchaseInvoiceItems::where('pi_id', '=', $pi->id)->get();
            //return $po_item;
            $data = [
                'pi' => $pi,
                'company' => $company,
                'pi_item' => $pi_item,
            ];
            $pdf = PDF::loadView('backEnd.pdf_print.pi_pdf', $data);
            //$pdf->setPaper('A4', 'portrait');
            // //return $pdf->download("purchase_invoice_".$pi->doc_number.".pdf");
            return $pdf->stream("purchase_invoice_" . $pi->doc_number . ".pdf");
            //return view('backEnd/pdf_print/pi_pdf', compact('pi','pi_item','company'));

        } else {
            return "error!!";
            //return view('web.syscom_credit_application_form');
        }
    }

    public function addattachment(Request $request)
    {
        //return $request;

        $pi_attach_file = "";
        if ($request->file('pi_attach_file') != "") {
            $file = $request->file('pi_attach_file');
            $pi_attach_file = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
            $file->move('public/uploads/pi_attachment/', $pi_attach_file);
            $pi_attach_file = 'public/uploads/pi_attachment/' . $pi_attach_file;
        }

        try {
            $pi_att = new SysPurchaseInvoiceAttachment();
            $pi_att->pi_id = $request->pi_id;
            $pi_att->file_name = $request->file_name;
            $pi_att->description = $request->description;
            $pi_att->validtill = date('Y-m-d', strtotime($request->validtill));
            $pi_att->pi_attach_file = $pi_attach_file;
            $pi_att->status = 1;
            $pi_att->created_by = Auth::user()->id;
            $results = $pi_att->save();

            Toastr::success('Operation successful', 'Success');
            return redirect('purchase-invoice/' . $pi_att->pi_id);
        } catch (\Exception $e) {
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

            $currency = SysCurrencySettings::select('id', 'code', 'ex_rate')->get();
            $paymentterms = SysPaymentTerms::select('id', 'title')->orderby('title', 'asc')->get();

            $query = SysPurchaseInvoice::select(DB::raw('sys_purchase_invoice.*, (SELECT GROUP_CONCAT(doc_file) FROM sys_purchase_invoice_att WHERE doc_id = sys_purchase_invoice.id) AS attach, (SELECT GROUP_CONCAT(doc_number) FROM sys_purchase_grn WHERE id=sys_purchase_invoice.ref_grn_id) AS grn_no, (SELECT GROUP_CONCAT(doc_number) FROM sys_purchase_return WHERE pi_id=sys_purchase_invoice.id) AS prt_no, (SELECT SUM(taxableamount)+SUM(vatamount) AS amount FROM sys_purchase_invoice_items WHERE pi_id=sys_purchase_invoice.id) AS amount, sys_purchase_invoice.deal_id AS code'), DB::raw('(SELECT SUM(vatamount) FROM sys_purchase_invoice_items WHERE pi_id = sys_purchase_invoice.id) AS total_vatamount'), DB::raw('(SELECT SUM(taxableamount) FROM sys_purchase_invoice_items WHERE pi_id = sys_purchase_invoice.id) AS total_taxableamount'));
            $query->wherein('company_id', $company_id);
            $query->orderby('id', 'desc');
            $purchaseinvoice = $query->get();



            $salesman = SysHelper::get_sales_persons();

            $customs_freight_account = SysHelper::get_customs_freight_accounts_for_purchase($company_id);

            $suppliertype = SysSupplierType::orderby('title', 'asc')->get();
            $purchasetype = SysPurchaseType::orderby('title', 'asc')->get();
            $countries = SysCountries::orderby('name', 'asc')->get();
            $states = SysStates::orderby('name', 'asc')->get();

            $items = SysHelper::get_product_list($company_id);

            $edit_pi = SysPurchaseInvoice::find($id);

            $adj_list = SysPaymentAdjustments::select('bi_doc_number', 'bi_doc_no', 'bi_doc_date', 'bi_lpo_no', 'bi_bill_number', 'bi_total', 'bi_paid', 'bi_balance', 'bi_amount', 'bi_balance_to_adjust', 'bi_amount_adjusted', 'bi_cheque_amount')->wherein('company_id', $company_id)->where('bi_doc_no', $edit_pi->doc_number)->get();

            $vendors = SysChartofAccounts::select('id', 'account_name', 'account_code')->where('id', $edit_pi->vendors)->get();
            $vendors2 = SysHelper::get_supplier_list_all($company_id);

            $currencylist2 = DB::table('sys_currency_rate as r')->select('r.id', 'r.from_currency', 'r.to_currency', 'c.code', 'r.rate')
                ->join('sys_currency as c', 'c.id', 'r.to_currency')
                ->where('r.status', 1)->where('r.from_currency', $edit_pi->currency)
                ->orderBy('c.code', 'ASC')->get();

            //$edit_pi_items = SysPurchaseInvoiceItems::where('pi_id','=',$edit_pi->id)->get();

            $edit_pi_items = SysPurchaseInvoiceItems::select('sys_purchase_invoice_items.*', 'sm_items.part_number AS partno', 'sm_items.description')
                ->join('sm_items', 'sm_items.id', 'sys_purchase_invoice_items.part_number')
                ->where('pi_id', $edit_pi->id)->orderby('sort_id')->get();


            $edit_pi_att = SysPurchaseInvoiceAttachment::where('pi_id', '=', $edit_pi->id)->get();
            $edit_company = SysCompany::find($edit_pi->company_id);
            $edit_cfc = SysPurchaseInvoiceCFCharges::where('pi_id', '=', $edit_pi->id)->get();


            $paymentAdjustments = SysPaymentAdjustments::where('bi_doc_no', $edit_pi->doc_number)->where('status', 1)->get();
            $returnAdjustments = SysPurchaseReturnAdjestment::where('piv_no', $edit_pi->doc_number)->where('status', 1)->get();


            $adjusted_amt = SysPaymentAdjustments::where('bi_doc_no', $edit_pi->doc_number)->sum('bi_amount_adjusted');
            $adjusted_amt_actual = $edit_pi_items->sum('taxableamount') + $edit_pi_items->sum('vatamount');
            $adjusted_amt = $adjusted_amt_actual - $adjusted_amt;

            $list_of_unadjusted = SysHelper::get_list_of_payable_unadjusted([$edit_pi->vendors], $company_id);
            $list_of_unadjusted_pdc = SysHelper::get_list_of_payable_unadjusted_pdc([$edit_pi->vendors], $company_id);

            $customer_reference_list = SysHelper::get_customer_list_deal_lead_all_role();
            $customer = SysHelper::get_customer_list($company_id);


            return compact('currency', 'currencylist2', 'vendors', 'vendors2', 'paymentterms', 'suppliertype', 'purchasetype', 'countries', 'states', 'edit_pi', 'edit_pi_items', 'edit_cfc', 'customs_freight_account', 'items', 'salesman', 'paymentAdjustments', 'returnAdjustments', 'list_of_unadjusted', 'list_of_unadjusted_pdc', 'adjusted_amt', 'adjusted_amt_actual', 'adj_list', 'purchaseinvoice', 'customer_reference_list', 'customer');
            // return view('backEnd.purchaseinvoice.manage_purchase_invoice_edit', compact('currency','currencylist2','vendors','vendors2','paymentterms','suppliertype','purchasetype','countries','states','edit_pi','edit_pi_items','edit_cfc','customs_freight_account','items','salesman','paymentAdjustments','returnAdjustments','list_of_unadjusted','list_of_unadjusted_pdc','adjusted_amt','adjusted_amt_actual','adj_list','purchaseinvoice'));


        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return [];
        }
    }

    public function view($id)
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

            $edit_pi = SysPurchaseInvoice::find($id);
            $edit_pi_items = SysPurchaseInvoiceItems::where('pi_id', '=', $edit_pi->id)->get();
            $edit_pi_att = SysPurchaseInvoiceAttachment::where('pi_id', '=', $edit_pi->id)->get();
            $edit_company = SysCompany::find($edit_pi->company_id);
            $edit_cfc = SysPurchaseInvoiceCFCharges::where('pi_id', '=', $edit_pi->id)->get();

            $paymentAdjustments = SysPaymentAdjustments::where('bi_doc_no', $edit_pi->doc_number)->where('status', 1)->get();
            $returnAdjustments = SysPurchaseReturnAdjestment::where('piv_no', $edit_pi->doc_number)->where('status', 1)->get();

            $salesman = SysHelper::get_sales_persons();
            return view('backEnd.purchaseinvoice.manage_purchase_invoice_view', compact('currency', 'vendors', 'salesman', 'paymentterms', 'suppliertype', 'purchasetype', 'countries', 'states', 'edit_pi', 'edit_pi_items', 'edit_cfc', 'customs_freight_account', 'paymentAdjustments', 'returnAdjustments'));


        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function update(Request $request)
    {


        $total_tax_amount = 0;
        $total_vat_amount = 0;
        try {
            DB::beginTransaction();

            $pi = SysPurchaseInvoice::find($request->id);
            $pi->pi_date = $request->filled('pi_date')
                ? Carbon::createFromFormat('d/m/Y', $request->pi_date)->format('Y-m-d')
                : null;

            if ($request->doc_number != $request->doc_number_main) {
                $exists = SysPurchaseInvoice::where('doc_number', $request->doc_number)->exists();
                if ($exists) {
                    DB::rollback();
                    Toastr::error('Operation Failed. Document number already exists', 'Failed');
                    return redirect()->back();
                }
                $pi->doc_number = $request->doc_number;
            }
            $pi->vendors = $request->vendors;
            $pi->currency = $request->currency;
            // $pi->narration = $request->narration;
            // $pi->lpo_number = $request->lpo_number;

            
                if ($request->lpo_number) {
                    $lpoArray = array_map('trim', explode(',', $request->lpo_number));
                    $pi->lpo_number = implode(',', $lpoArray);
                }

            $pi->lpo_date = Carbon::createFromFormat('d/m/Y', $request->lpo_date)->format('Y-m-d');
            // $pi->bill_number = $request->bill_number;

            
                if ($request->bill_number) {
                    $billArray = array_map('trim', explode(',', $request->bill_number));
                    $pi->bill_number = implode(',', $billArray);
                }

            $pi->bill_date = Carbon::createFromFormat('d/m/Y', $request->bill_date)->format('Y-m-d');
            $pi->payment_terms = $request->payment_terms;
            $pi->payment_terms2 = $request->payment_terms2;

            $pi->awbno = $request->awbno;
            $pi->boeno = $request->boeno;
            $pi->reference = $request->reference;
            $pi->warehouse = $request->warehouse;


            $pi->vat_percent = $request->vat_percent ?: null;
            $pi->vat_number = $request->vat_number;

            if ($request->has('ref_company_id')) {
                $pi->ref_company_id = implode(',', $request->ref_company_id);
            } else {
                $pi->ref_company_id = null;
            }

            // $pi->ref_company_id = $request->ref_company_id;



            // $pi->grn_no = $request->grn_no;

             if ($request->grn_no) {
                    $grnArray = array_map('trim', explode(',', $request->grn_no));
                    $pi->grn_no = implode(',', $grnArray);

                }

            $pi->grn_date = Carbon::createFromFormat('d/m/Y', $request->grn_date)->format('Y-m-d');

            // $pi->sales_person = $request->sales_person;

            $sales_person = $request->sales_person;

            if (!is_null($sales_person) && $sales_person !== '') {

                if (is_numeric($sales_person)) {
                    // Selected from dropdown (user ID)
                    $pi->sales_person = (int) $sales_person;
                    $pi->sales_person_name = null;
                } else {
                    // Manually entered name
                    $pi->sales_person = null;
                    $pi->sales_person_name = trim($sales_person);
                }
            }

            $pi->narration = $request->narration;
            $pi->deal_id = SysHelper::get_dealid_from_code_list($request->deal_id);

            $pi->shipping_supplier = $request->shipping_supplier;
            $pi->shipping_name = $request->shipping_name;
            $pi->shipping_email = $request->shipping_email;
            $pi->shipping_contact_no = $request->shipping_contact_no;
            $pi->shipping_address_1 = $request->shipping_address_1;

            $pi->supplier_type = $request->supplier_type;
            $pi->purchase_type = $request->purchase_type;
            $pi->supplier_country = $request->supplier_country;
            $pi->supplier_state = $request->supplier_state;
            $pi->status = 1;
            // $pi->company_id = session('logged_session_data.company_id');
            $pi->updated_by = Auth::user()->id;
            $pi->updated_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
            $pi->save();
            $pi->toArray();


            DB::table('sys_purchase_invoice_items')->where('pi_id', $pi->id)->delete();
            for ($i = 0; $i < count($request->part_number); $i++) {
                if ($request->part_number[$i] != "" && $request->qty[$i] != "" && $request->unitprice[$i] != "") {
                    $pii = new SysPurchaseInvoiceItems();
                    $pii->pi_id = $pi->id;
                    $pii->ref_po_id = $pi->ref_po_id;
                    $pii->part_number = $request->part_number[$i];
                    $pii->tax = $request->tax[$i];
                    $pii->qty = $request->qty[$i];
                    $pii->unitprice = (float) str_replace(',', '', $request->unitprice[$i] ?? 0);
                    $pii->value = (float) str_replace(',', '', $request->value[$i] ?? 0);
                    $pii->discount = (float) str_replace(',', '', $request->discount[$i] ?? 0);
                    $pii->fright = (float) str_replace(',', '', $request->fright[$i] ?? 0);
                    $pii->customcharges = (float) str_replace(',', '', $request->customcharges[$i] ?? 0);
                    $pii->taxableamount = (float) str_replace(',', '', $request->taxableamount[$i] ?? 0);
                    $pii->vatamount = (float) str_replace(',', '', $request->vatamount[$i] ?? 0);
                    $pii->description = $request->description[$i];
                    $pii->status = 1;
                    $pii->sort_id = $request->sort_id[$i];
                    $pii->created_by = Auth::user()->id;
                    $pii->save();

                }
            }

            $tamount = SysPurchaseInvoiceItems::where('pi_id', $request->id)->get();
            $total_tax_amount += $tamount->sum('taxableamount');
            $total_vat_amount += $tamount->sum('vatamount');


            DB::table('sys_purchase_invoice_cf_charges')->where('pi_id', $request->id)->delete();
            DB::table('sys_chartofaccounts_transaction')->where('transaction_type', 'purchaseinvoice')->where('transaction_id', $request->id)->delete();

            //Supplier account cr
            SysHelper::trn_chartof_accounts_transaction($request->vendors, $request->id, $pi->doc_number, $pi->pi_date, 'purchaseinvoice', '0.00', ($total_tax_amount + $total_vat_amount), '', 1, 0, "", 1);

            //Purchase account dr 
            $purchase_account_id = SysHelper::get_purchase_account_id();
            SysHelper::trn_chartof_accounts_transaction($purchase_account_id, $request->id, $pi->doc_number, $pi->pi_date, 'purchaseinvoice', ($total_tax_amount), '0.00', '', 1, 0, "", 1);

            //vat account dr 
            $purchase_vat_account_id = SysHelper::get_purchase_vat_account_id();
            SysHelper::trn_chartof_accounts_transaction($purchase_vat_account_id, $request->id, $pi->doc_number, $pi->pi_date, 'purchaseinvoice', ($total_vat_amount), '0.00', '', 1, 0, "", 1);

            for($i = 0; $i < count($request->cfc_name); $i++) {
                if($request->cfc_name[$i] !="" && $request->cfc_credit_account[$i] !="" && $request->cfc_amount[$i] !=""){
                    $cfc = new SysPurchaseInvoiceCFCharges();
                    $cfc->pi_id = $pi->id;
                    $cfc->pi_doc_number = $pi->doc_number;
                    $cfc->date = $request->cfc_date[$i] ? SysHelper::normalizeToYmd($request->cfc_date[$i]) : null;

                    $cfc->bill_number = $request->cfc_bill_no[$i];
                    $cfc->cfc_name = $request->cfc_name[$i];
                    $cfc->cfc_credit_account = $request->cfc_credit_account[$i];
                         $cfc->cfc_amount = str_replace(',', '', $request->cfc_amount[$i]);
                    $cfc->cfc_remarks = $request->cfc_remarks[$i];
                    $cfc->status = 1;
                    $cfc->created_by = Auth::user()->id;
                    $cfc->save();

                //Supplier account cr
                SysHelper::trn_chartof_accounts_transaction($request->cfc_credit_account[$i],$request->id,$pi->doc_number,$cfc->date,'purchaseinvoice','0.00',str_replace(',', '', $request->cfc_amount[$i]),$request->cfc_remarks[$i],1,0,"",$i+2);

                //Direct Exp account dr Customs Fright
                SysHelper::trn_chartof_accounts_transaction($request->cfc_name[$i],$request->id,$pi->doc_number,$cfc->date,'purchaseinvoice',str_replace(',', '', $request->cfc_amount[$i]),'0.00',$request->cfc_remarks[$i],1,0,"",$i+2);

                }
            }

            // Keep linked GRN charge table synchronized with PI charge updates.
            $linkedGrnIds = collect(explode(',', (string) ($pi->ref_grn_id ?? '')))
                ->map(function ($id) {
                    return (int) trim($id);
                })
                ->filter(function ($id) {
                    return $id > 0;
                })
                ->unique()
                ->values();

            if ($linkedGrnIds->count() > 0) {
                $piCharges = SysPurchaseInvoiceCFCharges::where('pi_id', $pi->id)->get();
                foreach ($linkedGrnIds as $grnId) {
                    DB::table('sys_purchase_grn_cf_charges')->where('grn_id', $grnId)->delete();
                    foreach ($piCharges as $piCharge) {
                        $grnCharge = new SysPurchaseGrnCfCharges();
                        $grnCharge->grn_id = $grnId;
                        $grnCharge->date = $piCharge->date;
                        $grnCharge->bill_number = $piCharge->bill_number;
                        $grnCharge->cfc_name = $piCharge->cfc_name;
                        $grnCharge->cfc_credit_account = $piCharge->cfc_credit_account;
                        $grnCharge->cfc_amount = $piCharge->cfc_amount;
                        $grnCharge->cfc_remarks = $piCharge->cfc_remarks;
                        $grnCharge->status = 1;
                        $grnCharge->created_by = Auth::user()->id;
                        $grnCharge->save();
                    }
                }
            }


            DB::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect('purchase-invoice/' . $pi->id);

        } catch (\Exception $e) {
            return $e;
            DB::rollback();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            DB::table('sys_purchase_invoice')->where('id', $id)->update(['status' => 2]);
            DB::table('sys_purchase_invoice_items')->where('pi_id', $id)->update(['status' => 2]);
            DB::table('sys_purchase_invoice_cf_charges')->where('pi_id', $id)->update(['status' => 2]);
            DB::table('sys_purchase_invoice_attachment')->where('pi_id', $id)->update(['status' => 2]);
            DB::table('sys_chartofaccounts_transaction')->where('transaction_id', $id)->where('transaction_type', 'purchaseinvoice')->update(['status' => 2]);
            DB::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
            DB::rollback();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function restore($id)
    {
        DB::beginTransaction();
        try {
            DB::table('sys_purchase_invoice')->where('id', $id)->update(['status' => 1]);
            DB::table('sys_purchase_invoice_items')->where('pi_id', $id)->update(['status' => 1]);
            DB::table('sys_purchase_invoice_cf_charges')->where('pi_id', $id)->update(['status' => 1]);
            DB::table('sys_purchase_invoice_attachment')->where('pi_id', $id)->update(['status' => 1]);
            DB::table('sys_chartofaccounts_transaction')->where('transaction_id', $id)->where('transaction_type', 'purchaseinvoice')->update(['status' => 1]);
            DB::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
            DB::rollback();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    function addpurchaseinvoiceitems(Request $request)
    {
        try {
            // $check = DB::table('sys_purchase_invoice_items')->where(
            //     [
            //         'pi_id' => $request->pi_id,
            //         'part_number' => $request->part_number,
            //         'tax' => $request->tax,
            //         'qty' => $request->qty,
            //         'unitprice' => $request->unitprice,
            //         'value' => $request->value,
            //         'discount' => $request->discount,
            //     ])->count();

            // if($check==0){
            DB::table('sys_purchase_invoice_items')->insert(
                [
                    'pi_id' => $request->pi_id,
                    'part_number' => $request->part_number,
                    'tax' => $request->tax,
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
            // }
            $ret = SysPurchaseInvoiceItems::select('sys_purchase_invoice_items.*', 'sm_items.part_number AS partno', 'sm_items.description')
                ->join('sm_items', 'sm_items.id', 'sys_purchase_invoice_items.part_number')
                ->where('pi_id', $request->pi_id)->orderby('sort_id')->get();
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
    function updatepurchaseinvoiceitems(Request $request)
    {
        try {
            DB::table('sys_purchase_invoice_items')->where('id', $request->id)->update(
                [
                    'part_number' => $request->part_number,
                    'tax' => $request->tax,
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
            $ret = SysPurchaseInvoiceItems::select('sys_purchase_invoice_items.*', 'sm_items.part_number AS partno', 'sm_items.description')
                ->join('sm_items', 'sm_items.id', 'sys_purchase_invoice_items.part_number')
                ->where('pi_id', $request->pi_id)->orderby('sort_id')->get();
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

    function deletepurchaseinvoiceitems(Request $request)
    {
        try {
            DB::table('sys_purchase_invoice_items')->where('id', $request->id)->delete();

            $ret = SysPurchaseInvoiceItems::select('sys_purchase_invoice_items.*', 'sm_items.part_number AS partno', 'sm_items.description')
                ->join('sm_items', 'sm_items.id', 'sys_purchase_invoice_items.part_number')
                ->where('pi_id', $request->pi_id)->orderby('sort_id')->get();
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

    public function getDetails($id)
    {
        $data = $this->get_pi_pdf_data($id);
        if (count($data) > 0) {
            return view('backEnd.purchaseinvoice.pi_details', $data);
        } else {
            return "error!!";
        }

    }

    public function get_pi_pdf_data($id)
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
            $bill_contact_name = "";
            $ship_trnno = "";
            $ship_address1 = "";


            $pi = SysPurchaseInvoice::find($id);
            $bill_contact_name = $pi->createdby->full_name;
             $ship_mob = SmStaff::select('mobile')->where('user_id', $pi->created_by)->first();
                $ship_mob = $ship_mob->mobile;
            if (!empty($pi)) {
                $company = SysCompany::find($pi->company_id);
                $pi_item = SysPurchaseInvoiceItems::where('pi_id', '=', $pi->id)->orderBy('sort_id')->get();
                $sup_email = SysCustSuppl::select('sys_cust_suppl.*')->join('sys_chartofaccounts', 'sys_chartofaccounts.account_code', 'sys_cust_suppl.code')->where('sys_chartofaccounts.id', $pi->vendors)->first();

                if (!empty($sup_email)) {
                    $add = SysCustSupplAddressbook::where('cust_suppl_id', $sup_email->id)->first();
                }

                $sub_data_list = SysCustSuppl::select('sys_cust_suppl.*')->join('sys_chartofaccounts', 'sys_chartofaccounts.account_code', 'sys_cust_suppl.code')->where('sys_chartofaccounts.id', $pi->shipping_supplier)->first();

                if (isset($sub_data_list)) {

             
                    $ship_trnno = $sub_data_list->vat_number;
                
                  
                }
                
                $contact_name = $sup_email->customer_salutation . ' ' . $sup_email->first_name . ' ' . $sup_email->last_name;
                $email = $sup_email->email;
                $tel = $sup_email->contcat_number;
                $mobile = $sup_email->mobile;
                $cust_trn_no = $sup_email->vat_number;

                if (!empty($add)) {
                    $address = $add->address;
                    $address2 = $add->address2;
                    $city = $add->city . ', PB No: ' . $add->zip_code;
                    $state = $add->statename->name;
                    $country = $add->countryname->name;
                }

                if ($pi->deal_id != 0 && $pi->deal_id != "") {
                    $deal_details = SysCrmDeals::where('id', $pi->deal_id)->first();

                    if (isset($deal_details)) {
                        if ($deal_details->delivery_company != "") {
                            $ship_company_name = $deal_details->delivery_company;
                        } else {
                            $ship_company_name = $deal_details->customername->name;
                        }

                        if ($deal_details->delivery_country != "" || $deal_details->delivery_state != null) {
                     $ship_address1 = trim(optional($deal_details->state)->name . (optional($deal_details->state)->name && optional($deal_details->country)->name ? ', ' : '') . optional($deal_details->country)->name);
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
                    'pi' => $pi,
                    'company' => $company,
                    'pi_item' => $pi_item,
                    'email' => $email,
                    'tel' => $tel,
                    'mobile' => $mobile,
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
                    'bill_contact_name' => $bill_contact_name,
                    'ship_mob' => $ship_mob,
                    'ship_trnno' => $ship_trnno,
                ];
                return $data;
            }
            return [];
        } catch (\Throwable $th) {
        
            return [];
        }
    }

    public function download($id)
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
            $mobile = "";
            $bill_contact_name = "";
            $ship_trnno = "";

            $pi = SysPurchaseInvoice::find($id);
            $bill_contact_name = $pi->createdby->full_name;
                $ship_mob = SmStaff::select('mobile')->where('user_id', $pi->created_by)->first();
                    $ship_mob = $ship_mob->mobile;
            if (!empty($pi)) {
                $company = SysCompany::find($pi->company_id);
                $pi_item = SysPurchaseInvoiceItems::where('pi_id', '=', $pi->id)->get();
                $sup_email = SysCustSuppl::select('sys_cust_suppl.*')->join('sys_chartofaccounts', 'sys_chartofaccounts.account_code', 'sys_cust_suppl.code')->where('sys_chartofaccounts.id', $pi->vendors)->first();

                if (!empty($sup_email)) {
                    $add = SysCustSupplAddressbook::where('cust_suppl_id', $sup_email->id)->first();
                }

                $contact_name = $sup_email->customer_salutation . ' ' . $sup_email->first_name . ' ' . $sup_email->last_name;
                $email = $sup_email->email;
                $tel = $sup_email->contcat_number;
                $cust_trn_no = $sup_email->vat_number;
                $mobile = $sup_email->mobile;

                 $sub_data_list = SysCustSuppl::select('sys_cust_suppl.*')->join('sys_chartofaccounts', 'sys_chartofaccounts.account_code', 'sys_cust_suppl.code')->where('sys_chartofaccounts.id', $pi->shipping_supplier)->first();

                if (isset($sub_data_list)) {

             
                    $ship_trnno = $sub_data_list->vat_number;
                
                  
                }
                


                if (!empty($add)) {
                    $address = $add->address;
                    $address2 = $add->address2;
                    $city = $add->city . ', PB No: ' . $add->zip_code;
                    $state = $add->statename->name;
                    $country = $add->countryname->name;
                }

                if ($pi->deal_id != 0 && $pi->deal_id != "") {
                    $deal_details = SysCrmDeals::where('id', $pi->deal_id)->first();

                    if (isset($deal_details)) {
                        if ($deal_details->delivery_company != "") {
                            $ship_company_name = $deal_details->delivery_company;
                        } else {
                            $ship_company_name = $deal_details->customername->name;
                        }

                        if ($deal_details->delivery_country != "" || $deal_details->delivery_state != null) {
                          $ship_address1 = trim(optional($deal_details->state)->name . (optional($deal_details->state)->name && optional($deal_details->country)->name ? ', ' : '') . optional($deal_details->country)->name);
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
                    'pi' => $pi,
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
                    'mobile' => $mobile,
                    'ship_trnno' => $ship_trnno,

                    'ship_company_name' => $ship_company_name,
                    'ship_address1' => $ship_address1,
                    'ship_address2' => $ship_address2,
                    'delivery_city' => $delivery_city,
                    'delivery_zip_code' => $delivery_zip_code,
                    'delivery_country' => $delivery_country,
                    'delivery_state' => $delivery_state,
                    'bill_contact_name' => $bill_contact_name,
                     'ship_mob' => $ship_mob,

                    'ship_contact_name' => $ship_contact_name,
                    'ship_tel' => $ship_tel,
                    'ship_email' => $ship_email,
                    'cust_trn_no' => $cust_trn_no,
                ];

                // return view('backEnd.pdf_print.pi_pdf', $data);

                $pdf = PDF::loadView('backEnd.pdf_print.pi_pdf', $data);
                $pdf->setPaper('A4', 'portrait');
                return $pdf->download($pi->doc_number . '-' . $pi->accountname->account_name . ".pdf");

            } else {
                return "error!!";
                //return view('web.syscom_credit_application_form');
            }
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    function purchaseinvoiceupdate_currency(Request $request)
    {
        try {
            if ($request->to_currency_id != $request->from_currency_id) {

                $to_currency = SysCurrencyRate::where('id', $request->to_currency_id)->value('to_currency');
                SysPurchaseInvoice::where('id', $request->cur_pi_id)->update(['currency' => $to_currency]);
                $qt = SysPurchaseInvoiceItems::where('pi_id', $request->cur_pi_id)->get();
                $ca = SysChartofAccountsTransaction::where('transaction_id', $request->cur_pi_id)->where('transaction_type', 'purchaseinvoice')->get();
                foreach ($qt as $t) {
                    //$old_price = $t->unitprice / $old_currancy->ex_rate;
                    $new_price = $t->unitprice * $request->to_currency_rate;

                    //$old_discount = $t->discount / $old_currancy->ex_rate;
                    $new_discount = $t->discount * $request->to_currency_rate;

                    SysPurchaseInvoiceItems::where('id', $t->id)->update(
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

            DB::table('sys_purchase_invoice_att')->insert($data);

            if ($request->doc_id == 0) {
                $ret = DB::table('sys_purchase_invoice_att')->where('doc_id', $request->doc_id)->where('cart_id', session('logged_session_data.cart_id'))->where('company_id', session('logged_session_data.company_id'))->get();
            } else {
                $ret = DB::table('sys_purchase_invoice_att')->where('doc_id', $request->doc_id)->where('company_id', session('logged_session_data.company_id'))->get();
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
                $ret = DB::table('sys_purchase_invoice_att')->where('doc_id', $request->doc_id)->where('cart_id', session('logged_session_data.cart_id'))->where('company_id', session('logged_session_data.company_id'))->get();
            } else {
                $ret = DB::table('sys_purchase_invoice_att')->where('doc_id', $request->doc_id)->where('company_id', session('logged_session_data.company_id'))->get();
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
            DB::table('sys_purchase_invoice_att')->where('id', $request->id)->delete();

            if ($request->doc_id == 0) {
                $ret = DB::table('sys_purchase_invoice_att')->where('doc_id', $request->doc_id)->where('cart_id', session('logged_session_data.cart_id'))->where('company_id', session('logged_session_data.company_id'))->get();
            } else {
                $ret = DB::table('sys_purchase_invoice_att')->where('doc_id', $request->doc_id)->where('company_id', session('logged_session_data.company_id'))->get();
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

    // discount
    function purchaseinvoiceupdate_discount(Request $request)
    {
        try {
            if ($request->discount_amount != "") {
                $qt = SysPurchaseInvoiceItems::where('pi_id', $request->discount_amount_pi_id)->get();
                $discount_amount = $request->discount_amount;
                $total = $qt->sum('value');
                foreach ($qt as $t) {
                    $new_discount = ($t->value / $total) * $discount_amount;
                    SysPurchaseInvoiceItems::where('id', $t->id)->update(
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
    function addpurchaseinvoiceitemscart_discount(Request $request)
    {
        try {
            if ($request->discount_amount != "") {
                $qt = SysPurchaseInvoiceItems::where('cart_id', session('logged_session_data.cart_id'))->get();
                $discount_amount = $request->discount_amount;
                $total = $qt->sum('value');
                foreach ($qt as $t) {
                    $new_discount = ($t->value / $total) * $discount_amount;
                    SysPurchaseInvoiceItems::where('id', $t->id)->update(
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
    function purchaseinvoiceupdate_freight(Request $request)
    {
        try {
            if ($request->freight_amount != "") {
                $qt = SysPurchaseInvoiceItems::where('pi_id', $request->freight_amount_pi_id)->get();
                $freight_amount = $request->freight_amount;
                $total = $qt->sum('value');
                foreach ($qt as $t) {
                    $new_freight = ($t->value / $total) * $freight_amount;
                    SysPurchaseInvoiceItems::where('id', $t->id)->update(
                        [
                            'fright' => $new_freight,
                            'taxableamount' => ($t->unitprice * $t->qty) - $t->discount + $new_freight,
                            'vatamount' => (($t->unitprice * $t->qty) - $t->discount + $new_freight) * $t->tax / 100,
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
    function addpurchaseinvoiceitemscart_freight(Request $request)
    {
        try {
            if ($request->freight_amount != "") {
                $qt = SysPurchaseInvoiceItems::where('cart_id', session('logged_session_data.cart_id'))->get();
                $freight_amount = $request->freight_amount;
                $total = $qt->sum('value');
                foreach ($qt as $t) {
                    $new_freight = ($t->value / $total) * $freight_amount;
                    SysPurchaseInvoiceItems::where('id', $t->id)->update(
                        [
                            'fright' => $new_freight,
                            'taxableamount' => ($t->unitprice * $t->qty) - $t->discount + $new_freight,
                            'vatamount' => (($t->unitprice * $t->qty) - $t->discount + $new_freight) * $t->tax / 100,
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
    function purchaseinvoiceupdate_custom(Request $request)
    {
        try {
            if ($request->custom_amount != "") {
                $qt = SysPurchaseInvoiceItems::where('pi_id', $request->custom_amount_pi_id)->get();
                $custom_amount = $request->custom_amount;
                $total = $qt->sum('value');
                foreach ($qt as $t) {
                    $new_custom = ($t->value / $total) * $custom_amount;
                    SysPurchaseInvoiceItems::where('id', $t->id)->update(
                        [
                            'customcharges' => $new_custom,
                            'taxableamount' => ($t->unitprice * $t->qty) - $t->discount + $new_custom,
                            'vatamount' => (($t->unitprice * $t->qty) - $t->discount + $new_custom) * $t->tax / 100,
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
    function addpurchaseinvoiceitemscart_custom(Request $request)
    {
        try {
            if ($request->custom_amount != "") {
                $qt = SysPurchaseInvoiceItems::where('cart_id', session('logged_session_data.cart_id'))->get();
                $custom_amount = $request->custom_amount;
                $total = $qt->sum('value');
                foreach ($qt as $t) {
                    $new_custom = ($t->value / $total) * $custom_amount;
                    SysPurchaseInvoiceItems::where('id', $t->id)->update(
                        [
                            'customcharges' => $new_custom,
                            'taxableamount' => ($t->unitprice * $t->qty) - $t->discount + $new_custom,
                            'vatamount' => (($t->unitprice * $t->qty) - $t->discount + $new_custom) * $t->tax / 100,
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


    function purchaseinvoiceupdate_adjustment(Request $request)
    {
        try {
            $temp_data = [];
            $ret_data = [];
            for ($i = 0; $i < count($request->set_amt); $i++) {
                if ($request->set_amt[$i] != 0) {

                    $rec = SysPayment::where('doc_number', $request->paymentno[$i])->first();

                    if (isset($rec)) {
                        $adjusted_amt = SysPaymentAdjustments::where('bi_doc_no', $request->adj_siv_no)->sum('bi_amount_adjusted');
                        if ($rec->mode == 1) {
                            $transaction_type = "cashpayment";
                        } else {
                            $transaction_type = "bankpayment";
                        }
                        $currency = $rec->currency;
                        $exe_type = "receipt";
                    } else {
                        $adjusted_amt = 0;
                        $rec = SysPurchaseReturn::where('doc_number', $request->paymentno[$i])->first();

                        $currency = $rec->currency;
                        $transaction_type = "";
                        $exe_type = "return";
                    }

                    if ($request->set_amt_act[$i] == $request->set_amt[$i]) {
                        $bi_balance_to_adjust = 0;
                        $bi_extra_amount = 0;
                    }
                    if ($request->set_amt_act[$i] > $request->set_amt[$i]) {
                        $bi_balance_to_adjust = 0;
                        $bi_extra_amount = $request->set_amt_act[$i] - $request->set_amt[$i];
                    }
                    if ($request->set_amt_act[$i] < $request->set_amt[$i]) {
                        $bi_balance_to_adjust = $request->set_amt[$i] - $request->set_amt_act[$i];
                        $bi_extra_amount = 0;
                    }

                    if ($exe_type == "receipt") {
                        $temp_data[] = [
                            'transaction_type' => $transaction_type,
                            'bi_cheque_amount' => $request->set_amt_act[$i],
                            'bi_amount_adjusted' => $request->set_amt[$i],
                            'bi_balance_to_adjust' => $request->adj_piv_amount_actual - ($request->set_amt[$i] + $adjusted_amt),
                            'bi_extra_amount' => $bi_extra_amount,
                            'bi_currency' => @$currency,
                            'bi_doc_number' => $request->paymentno[$i],
                            'bi_contains' => '',
                            'bi_doc_no' => $request->adj_piv_no,
                            'bi_lpo_no' => '',
                            'bi_doc_date' => $request->adj_piv_date,
                            'bi_total' => $request->adj_piv_amount,
                            'bi_paid' => $request->set_amt[$i],
                            'bi_balance' => $request->adj_piv_amount_actual - ($request->set_amt[$i] + $adjusted_amt),
                            'bi_extra_amount' => $bi_extra_amount,
                            'bi_amount' => $request->set_amt[$i],
                            'bi_narration' => "Adjusted from SI No: " . $request->adj_piv_no,
                            'account_id' => $request->adj_sup_id,
                            'status' => 1,
                            'created_by' => Auth::user()->id,
                            'created_at' => Carbon::now('+04:00'),
                            'company_id' => session('logged_session_data.company_id'),
                        ];
                    }
                    if ($exe_type == "return") {
                        $ret_data = [
                            'pri_no' => $request->paymentno[$i],
                            'piv_no' => $request->adj_piv_no,
                            'lpo_number' => @$rec->lpo_number,
                            'doc_date' => date('Y-m-d', strtotime($request->adj_piv_date)),
                            'total_amount' => $request->set_amt_act[$i],
                            'paid_amount' => $request->set_amt[$i],
                            'balance_amount' => $request->set_amt_act[$i] - $request->set_amt[$i],
                            'narration' => 'Adjusted from PI No: ' . $request->adj_piv_no,
                            'status' => 1,
                            'created_by' => Auth::user()->id,
                        ];
                    }
                }
            }
            if (count($temp_data) > 0) {
                SysPaymentAdjustments::insert($temp_data);
            }
            if (count($ret_data) > 0) {
                SysPurchaseReturnAdjestment::insert($ret_data);
            }
            // db::commit();


            Toastr::success('Adjustment Updated Successfully.', 'Success');
            return redirect()->back();

        } catch (\Exception $e) {
            //db::rollBack();
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    function purchaseinvoice_get_adjustment(Request $request)
    {
        try {
            $company_id = session('logged_session_data.company_id');

            $unadjusted = SysHelper::get_list_of_payable_unadjusted([$request->vendors], $company_id);
            $unadjusted_pdc = SysHelper::get_list_of_payable_unadjusted_pdc([$request->vendors], $company_id);

            return json_encode([
                'unadjusted' => $unadjusted,
                'unadjusted_pdc' => $unadjusted_pdc
            ]);

        } catch (\Exception $e) {
            return json_encode([
                'error' => true,
                'message' => $e->getMessage()
            ]);
        }
    }
    function purchaseinvoice_add_adjustment_cart(Request $request)
    {
        try {

            $company_id = session('logged_session_data.company_id');
            DB::table('sys_purchase_invoice_adjustment_temp')->where([
                'company_id' => $company_id,
                'user_id' => Auth::user()->id,
                'cart_id' => session('logged_session_data.cart_id')
            ])->delete();

            $adj_sup_id = $request->input('adj_sup_id');
            $adj_piv_amount_actual = $request->input('adj_piv_amount_actual');

            $paymentNos = $request->input('paymentno');
            $setAmts = $request->input('set_amt');
            $setAmtActs = $request->input('set_amt_act');

            foreach ($paymentNos as $index => $paymentNo) {
                $data[] = [
                    'cart_id' => session('logged_session_data.cart_id'),
                    'paymentno' => $paymentNos[$index],
                    'set_amt_act' => str_replace(',', '', $setAmtActs[$index]),
                    'set_amt' => $setAmts[$index],
                    'adj_sup_id' => $adj_sup_id,
                    'adj_piv_amount_actual' => $adj_piv_amount_actual,
                    'status' => 1,
                    'company_id' => $company_id,
                    'user_id' => Auth::user()->id,
                ];
            }

            if (count($data) > 0) {
                DB::table('sys_purchase_invoice_adjustment_temp')->insert($data);
            }
            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            return $e;
            return json_encode([
                'error' => true,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function search(Request $request)
    {
        try {
            $companyId = session('logged_session_data.company_id');
            $q = $request->get('query');
            $formattedDate = null;

            if ($q && preg_match('/\d{2}[\/\-]\d{2}[\/\-]\d{4}/', $q)) {
                $normalized = str_replace('/', '-', $q);
                $formattedDate = date('Y-m-d', strtotime($normalized));
            }

            $purchase = SysPurchaseInvoice::select(DB::raw('sys_purchase_invoice.*, (SELECT GROUP_CONCAT(doc_file) FROM sys_purchase_invoice_att WHERE doc_id = sys_purchase_invoice.id) AS attach, (SELECT GROUP_CONCAT(doc_number) FROM sys_purchase_grn WHERE id=sys_purchase_invoice.ref_grn_id) AS grn_no, (SELECT GROUP_CONCAT(doc_number) FROM sys_purchase_return WHERE pi_id=sys_purchase_invoice.id) AS prt_no, (SELECT SUM(taxableamount)+SUM(vatamount) AS amount FROM sys_purchase_invoice_items WHERE pi_id=sys_purchase_invoice.id) AS amount, sys_purchase_invoice.deal_id AS code'), DB::raw('(SELECT SUM(vatamount) FROM sys_purchase_invoice_items WHERE pi_id = sys_purchase_invoice.id) AS total_vatamount'), DB::raw('(SELECT SUM(taxableamount) FROM sys_purchase_invoice_items WHERE pi_id = sys_purchase_invoice.id) AS total_taxableamount'))
                ->with(['currency_name', 'accountname'])
                ->where('sys_purchase_invoice.company_id', $companyId)
                ->where(function ($query) use ($q, $formattedDate) {
                    if ($q) {
                        $query->where('sys_purchase_invoice.doc_number', 'like', "%{$q}%");


                        if ($formattedDate) {
                            $query->orWhereDate('sys_purchase_invoice.pi_date', $formattedDate);
                        }



                        $query->orWhereHas('accountname', function ($q2) use ($q) {
                            $q2->where('account_code', 'like', "%{$q}%");
                            $q2->orWhere('account_name', 'like', "%{$q}%");
                        });


                        // Search by related currency code
                        $query->orWhereHas('currency_name', function ($q3) use ($q) {
                            $q3->where('code', 'like', "%{$q}%");
                        });
                    }
                })
                ->orderBy('sys_purchase_invoice.id', 'desc')
                ->get();

            return response()->json($purchase);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

}
<?php

namespace App\Http\Controllers;

use App\SysChartofAccounts;
use App\SysAccountType;
use App\ApiBaseMethod;
use App\SmStaff;
use App\SysCashReceipt;
use App\SysCashReceiptList;
use App\SysCustSuppl;
use App\SysAccountGroup;
use App\SysChartofAccountsTransaction;
use App\SysCompany;
use App\SysCountries;
use App\SysCrmDeals;
use App\SysCrmDealTrack;
use App\SysCrmDealTrackApprovalPurchease;
use App\SysCrmEndUser;
use App\SysCrmQuoteItems;
use App\SysCurrency;
use App\SysCurrencyRate;
use App\SysCurrencySettings;
use App\SysCustomerType;
use App\SysCustSupplAddressbook;
use App\SysDealDlnItemsCart;
use App\SysDealSalesInvoiceItemsCart;
use App\SysDeliveryNote;
use App\SysDeliveryNoteItems;
use App\SysSalesInvoice;
use App\SysSalesInvoiceCFCharges;
use App\SysSalesInvoiceItems;
use App\SysDeliveryNoteList;
use App\SysHelper;
use App\SysItemStock;
use App\SysPaymentTerms;
use App\SysPurchaseDlnLicenseKey;
use App\SysPurchaseGrnLicenseKey;
use App\SysPurchaseOrder;
use App\SysPurchaseOrderItems;
use App\SysReceiptMode;
use App\SysSalesReturn;
use App\SysSaleType;
use App\SysStates;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade as PDF;
use setasign\Fpdi\Fpdi;

class SysDeliveryNoteController extends Controller
{
    public function __construct(){
        $this->middleware('PM');
    }
    

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function deliverynoteList(Request $request, $id = null)
    {
        try{
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $pending_si=0;
            $customer_list = SysHelper::get_customer_list($company_id);
            $ctrl_doc_number = "";
            $ctrl_customer = "";
            $ctrl_supplier = "";
            $ctrl_deal_number = "";
            $ctrl_sales_invoice_number = "";
            $ctrl_srt = "";
            $ctrl_date ="";
            
            $query = SysDeliveryNote::select(DB::raw('sys_delivery_note.*, ( SELECT GROUP_CONCAT(sia.doc_file) FROM sys_sales_invoice_att sia JOIN ( SELECT MAX(id) AS max_id FROM sys_sales_invoice WHERE doc_number = sys_delivery_note.invoice_no ) si ON sia.siv_id = si.max_id ) AS attach,(SELECT GROUP_CONCAT(doc_number) 
 FROM sys_sales_return 
 WHERE dn_doc_number COLLATE utf8mb4_unicode_ci = sys_delivery_note.doc_number COLLATE utf8mb4_unicode_ci) AS srtno, (SELECT SUM(taxableamount)+SUM(vatamount) FROM sys_delivery_note_items WHERE dn_id=sys_delivery_note.id) as amount, (SELECT GROUP_CONCAT(code) FROM sys_crm_deals WHERE id=sys_delivery_note.deal_id) AS code'));
            if(SysHelper::get_pagination_post($request)){
                if ($request->documents_number != "") {
                    $query->where('doc_number','like','%'.$request->documents_number.'%');
                    $ctrl_doc_number = $request->documents_number;
                }
                if ($request->customer != "") {
                    $query->where('customer_id',$request->customer);
                    $ctrl_customer = $request->customer;
                }
                if ($request->supplier != "") {
                    $query->where('supplier_name','like','%'.$request->supplier.'%');
                    $ctrl_supplier = $request->supplier;
                }
                if ($request->deal_number != "") {
                    $query->where('deal_id','like','%'.SysHelper::get_dealid_from_code($request->deal_number).'%');
                    $ctrl_deal_number = $request->deal_number;
                }
                if ($request->sales_invoice_number != "") {
                    if(strtolower($request->delivery_note) == "pending"){
                        $pending_si=1;
                    } else {
                        $query->where('invoice_no','like','%'.$request->sales_invoice_number.'%');
                    }
                    $ctrl_sales_invoice_number = $request->sales_invoice_number;
                }
                if ($request->srt != "") {
                    $srt_nos = SysSalesReturn::where('doc_number',$request->srt)->pluck('dn_doc_number');
                    $query->wherein('doc_number',$srt_nos);
                    $ctrl_srt = $request->srt;
                }
                if ($request->date != "") {
                    $query->where('doc_date',SysHelper::normalizeToYmd($request->date));
                    $ctrl_date = $request->date;
                }
            }
            else{

            }
            
            $query->wherein('company_id',$company_id);
            //$query->wherein('created_by',$r[1]);
            
            $query->orderby('doc_number','desc');
            $deliverynote = $query->paginate(50);



            $active_id = $id;
            $data = [];


            $action = false;
            $editData = [];

            $addData = [];




            if ($request->has('di_action')) {
                $poAction = $request->input('di_action');

                if ($poAction === 'add') {
                    $action = 'add';

                    $addData = $this->deliverynoteAdd(); // Get all data for adding
                  

                } elseif ($poAction === 'edit') {
                    $action = 'edit';
                    $editData = $this->edit($active_id); // Get all data for editing
                }
            } else {
                if ($id) {
                    $data = $this->get_si_pdf_data($id);
                } else {
                    $firstRecord = $deliverynote->first();
                    if ($firstRecord) {
                        $active_id = $firstRecord->id;
                        $data = $this->get_si_pdf_data($firstRecord->id);
                    }
                }
            }


           
            //$cashreceipt_list = SysCashReceiptList::all();
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse($deliverynote, null);
            }
            return view('backEnd.deliverynote.deliverynotelist', compact('deliverynote','customer_list','pending_si','data','active_id','action','editData','addData','ctrl_customer','ctrl_supplier','ctrl_deal_number','ctrl_sales_invoice_number','ctrl_srt','ctrl_date','ctrl_doc_number'));
        }catch (\Exception $e) {
            return $e;
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


            $invoices = SysDeliveryNote::select(
        'sys_delivery_note.id',
        'sys_delivery_note.doc_number',
        'sys_delivery_note.doc_date',
        DB::raw('(SELECT SUM(taxableamount)+SUM(vatamount) FROM sys_delivery_note_items WHERE dn_id=sys_delivery_note.id) as amount'),
        'sys_chartofaccounts.account_code',
        'sys_chartofaccounts.account_name',
        'sys_currency.code as currency_code'
    )
    ->join('sys_chartofaccounts', 'sys_chartofaccounts.id', '=', 'sys_delivery_note.customer_id')
    ->join('sys_currency', 'sys_currency.id', '=', 'sys_delivery_note.currency')
    ->whereIn('sys_delivery_note.company_id', $company_id)
    ->where('sys_delivery_note.doc_number', 'LIKE', "%{$query}%")
    ->orderBy('sys_delivery_note.doc_number','desc')
    ->limit(20)
    ->get();
            return response()->json($invoices);            
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function getDetails($id)
    {
         $data = $this->get_si_pdf_data($id);
         if(count($data)>0){
                return view('backEnd.deliverynote.dn_details', $data);
            }
            else {
                return "error!!";
            }
    }

     public function getDetailsPDF($id)
    {
         $data = $this->get_si_pdf_data($id);
         if(count($data)>0){
                return view('backEnd.deliverynote.dn_details-pdf', $data);
            }
            else {
                return "error!!";
            }
    }

    public function get_si_pdf_data($id)
    {
        try {
            $address = ""; $address2 = ""; $city = ""; $state = ""; $country = ""; $contact_name = ""; $email = ""; $tel = ""; $mob = "";
        $ship_company_name = ""; $delivery_city = ""; $delivery_zip_code = ""; $delivery_country = ""; $delivery_state="";

            $dn = SysDeliveryNote::find($id);
            if(!empty($dn)){
                $company = SysCompany::find($dn->company_id);
                $dn_item = SysDeliveryNoteItems::where('dn_id','=',$dn->id)->orderBy('sort_id')->get();
                $sup_email = SysCustSuppl::select('sys_cust_suppl.*')->join('sys_chartofaccounts','sys_chartofaccounts.account_code','sys_cust_suppl.code')->where('sys_chartofaccounts.id',$dn->customer_id)->first();
                if(!empty($sup_email)){
                    $add = SysCustSupplAddressbook::where('cust_suppl_id',$sup_email->id)->first();
                }
                
                $contact_name = $sup_email->customer_salutation.' '.$sup_email->first_name.' '.$sup_email->last_name;
                $email = $sup_email->email;
                $tel = $sup_email->contcat_number;
                $mob = $sup_email->mobile;
                $ship_tel=$tel;
                $ship_mob=$mob;
 
                if(!empty($add)){
                    $address = $add->address;
                    $address2 = $add->address2;
                    $city = $add->city;
                    $state = @$add->statename->name;
                    $country = @$add->countryname->name;
                }
            
                if($dn->deal_id != 0 && $dn->deal_id != "") {
                    $deal_details = SysCrmDeals::where('id',$dn->deal_id)->first();
    
                    if(isset($deal_details)){
                        if($deal_details->delivery_company != "") { $ship_company_name = $deal_details->delivery_company; } else { $ship_company_name = $deal_details->customername->name; }
                        
                        if($deal_details->delivery_address1 != "") { $ship_address1 = $deal_details->delivery_address1; } else { $ship_address1 = ""; }
                        if($deal_details->delivery_address2 != "") { $ship_address2 = $deal_details->delivery_address2; } else { $ship_address2 = ""; }
                        if($deal_details->delivery_city != "") { $delivery_city = $deal_details->delivery_city; } else { $delivery_city = $add->city; }
                        if($deal_details->delivery_zip_code != "") { $delivery_zip_code = $deal_details->delivery_zip_code; } else { $delivery_zip_code = ""; }
                        if($deal_details->delivery_country != "") { $delivery_country = $deal_details->country->name; } else { $delivery_country = $add->countryname->name; }
                        if($deal_details->delivery_state != "") { $delivery_state = $deal_details->state->name; } else { $delivery_state = $add->statename->name; }
                        
    
                        if($deal_details->delivery_name != "") { $ship_contact_name = $deal_details->delivery_name; } else { $ship_contact_name = $deal_details->cust_name; }
                        //if($deal_details->delivery_number != "") { $ship_tel = $deal_details->delivery_number; } else { $ship_tel = $deal_details->cust_no; }
                        if($deal_details->delivery_email != "") { $ship_email = $deal_details->delivery_email; } else { $ship_email = $deal_details->cust_email; }
                    }
                }
                else{
                    $ship_company_name="";
                    $ship_contact_name = $contact_name;
                    $ship_email = $email ;
                    //$ship_tel = $tel;
                    $ship_address1 = $add->city;
                    $ship_address2 = "";
                    $delivery_city = $add->city;
                    $delivery_zip_code = "";
                    $delivery_country = $add->countryname->name;
                    $delivery_state = $add->statename->name;
                }
                $data = [
                    'dn' => $dn,
                    'company' => $company,
                    'dn_item' => $dn_item,
                    
                    'email' => $email,
                    'tel' => $tel,
                    'mob' => $mob,
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
                    'ship_mob' => $ship_mob,
                    'ship_email' => $ship_email,

                    // 'email' => $email,
                    // 'tel' => $tel,
                    // 'address' => $address,
                    // 'address2' => $address2,
                    // 'city' => $city,
                    // 'state' => $state,
                    // 'country' => $country,
                ];
                return $data;
            }       
            return [];
        } catch (\Throwable $th) {
            return [];
        }
    }

    public function deliverynoteAdd()
    {
        try{
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $customer = SysHelper::get_customer_list($company_id);
            $currency = SysCurrencySettings::all();
            $company = SysCompany::find(session('logged_session_data.company_id'));
            
            $staff = SysHelper::get_sales_persons();
            $items = SysHelper::get_product_list($company_id);

            $paymentterms = SysPaymentTerms::select('id','title')->orderby('title','asc')->get();
            $select_cart=[];
            
            $countries = SysCountries::orderby('name','asc')->get();
            $states = SysStates::orderby('name','asc')->get();
            $items = SysHelper::get_product_list($company_id);
            $customertype = SysCustomerType::orderby('title','asc')->get();
            $saletype = SysSaleType::orderby('title','asc')->get();

            $query = SysDeliveryNote::select(DB::raw('sys_delivery_note.*, ( SELECT GROUP_CONCAT(sia.doc_file) FROM sys_sales_invoice_att sia JOIN ( SELECT MAX(id) AS max_id FROM sys_sales_invoice WHERE doc_number = sys_delivery_note.invoice_no ) si ON sia.siv_id = si.max_id ) AS attach,(SELECT GROUP_CONCAT(doc_number) 
 FROM sys_sales_return 
 WHERE dn_doc_number COLLATE utf8mb4_unicode_ci = sys_delivery_note.doc_number COLLATE utf8mb4_unicode_ci) AS srtno, (SELECT SUM(taxableamount)+SUM(vatamount) FROM sys_delivery_note_items WHERE dn_id=sys_delivery_note.id) as amount, (SELECT GROUP_CONCAT(code) FROM sys_crm_deals WHERE id=sys_delivery_note.deal_id) AS code'));
            $query->wherein('company_id',$company_id);            
            $query->orderby('doc_number','desc');
            $deliverynote = $query->paginate(50);
            $pending_si=0;

            $deal_det = [];
            
            //return  compact('currency','customer','paymentterms','company','staff','select_cart','items','deliverynote','pending_si','countries','states','customertype','saletype');
            return view('backEnd.deliverynote.deliverynoteadd', compact('currency','customer','paymentterms','company','staff','select_cart','items','deliverynote','pending_si','countries','states','customertype','saletype'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function deliverynoteAdd2($deal_id = null, Request $request)
    {
        try{
             $customer_reference = $request->input('customer_reference', null);
    $salesman_name = $request->input('salesman_name', null);
    $account_id = $request->input('account_id', null);
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            
            $cart_qty = SysDealDlnItemsCart::where('created_by',Auth::user()->id)->where('deal_id',$deal_id)->where('cart_id',session('logged_session_data.cart_id'))->sum('qty');
            $dln_qty = SysDealDlnItemsCart::where('created_by',Auth::user()->id)->where('deal_id',$deal_id)->where('cart_id',session('logged_session_data.cart_id'))->sum('dln_qty');
            
            $deal_acc = SysChartofAccounts::where('account_name',$customer_reference)->where('subgroup2',7)->where('status',1)->first();
            $deal_det = SysCrmDeals::where('id',$deal_id)->first();
            $deal_cust = SysCustSuppl::where('name',$customer_reference)->where('catid',1)->where('status',1)->first();
            $deal_enduser = SysCrmEndUser::where('deal_id',$deal_id)->where('status',1)->first();

            if($cart_qty == $dln_qty){
                Toastr::error('Pending Items Not Found!', 'Failed');
                return redirect('purchase-order?po_action=add');
            }
            
            $select_cart = SysDealDlnItemsCart::select('sys_deal_dln_items_cart.*','sys_deal_dln_items_cart.part_number_txt as partno','sys_crm_deal_track.payment_terms','sys_crm_deal_track.reference_no','sys_crm_deal_track.reference_date')
            ->join('sys_crm_deal_track','sys_crm_deal_track.deal_id','sys_deal_dln_items_cart.deal_id')
            ->where('cart_id',session('logged_session_data.cart_id'))
            ->where('sys_deal_dln_items_cart.created_by',Auth::user()->id)
            ->where('sys_deal_dln_items_cart.deal_id',$deal_id)
            ->where('sys_deal_dln_items_cart.status',1)->orderby('sys_deal_dln_items_cart.id','asc')->get();


            //$deal_det = SysCrmDeals::select('sales_invoice_id','dln_id','owner')->where('id',$deal_id)->first();
            $siv_det = SysSalesInvoice::select('doc_number','doc_date')->where('id',$deal_det->sales_invoice_id)->first();
            $sup_name = SysCrmDealTrackApprovalPurchease::select('supplier_name')->where('deal_id',$deal_id)->first();

            $customer_det = SysCustSuppl::select('sys_cust_suppl.*')
            ->join('sys_crm_deals','sys_crm_deals.cust_id','sys_cust_suppl.id')
            ->where('sys_crm_deals.id',$select_cart[0]->deal_id)->first();
            $items = SysHelper::get_product_list($company_id);

            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            //$customer = SysHelper::get_customer_list($company_id);
            $customer = SysChartofAccounts::select('id','account_name','account_code')
                ->where('account_name',$customer_reference)->where('subgroup2',7)->where('status',1)->orderby('account_name','asc')->get();
            $currency = SysCurrencySettings::all();
            $company        = SysCompany::find(session('logged_session_data.company_id'));
            
            $staff = SysHelper::get_sales_persons();
            $paymentterms = SysPaymentTerms::select('id','title')->orderby('title','asc')->get();
            
            $countries = SysCountries::orderby('name','asc')->get();
            $states = SysStates::orderby('name','asc')->get();
            $customertype = SysCustomerType::orderby('title','asc')->get();
            $saletype = SysSaleType::orderby('title','asc')->get();
            
            $query = SysDeliveryNote::select(DB::raw('sys_delivery_note.*, ( SELECT GROUP_CONCAT(sia.doc_file) FROM sys_sales_invoice_att sia JOIN ( SELECT MAX(id) AS max_id FROM sys_sales_invoice WHERE doc_number = sys_delivery_note.invoice_no ) si ON sia.siv_id = si.max_id ) AS attach,(SELECT GROUP_CONCAT(doc_number) 
 FROM sys_sales_return 
 WHERE dn_doc_number COLLATE utf8mb4_unicode_ci = sys_delivery_note.doc_number COLLATE utf8mb4_unicode_ci) AS srtno, (SELECT SUM(taxableamount)+SUM(vatamount) FROM sys_delivery_note_items WHERE dn_id=sys_delivery_note.id) as amount, (SELECT GROUP_CONCAT(code) FROM sys_crm_deals WHERE id=sys_delivery_note.deal_id) AS code'));
            $query->wherein('company_id',$company_id);            
            $query->orderby('doc_number','desc');
            $deliverynote = $query->paginate(50);
            $pending_si=0;


            return view('backEnd.deliverynote.deliverynoteadd', compact('currency','customer','paymentterms','company','staff','select_cart','customer_det','account_id','siv_det','sup_name','deal_det','items','deliverynote','pending_si','deal_cust','deal_acc','countries','states','customertype','saletype','deal_enduser'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function deliverynoteAdd3($deal_id)
    {
        try{
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $customer = SysHelper::get_customer_list($company_id);
            $currency = SysCurrencySettings::all();
            $company = SysCompany::find(session('logged_session_data.company_id'));
            
            $staff = SysHelper::get_sales_persons();
            $from_deal = 1;
            $items = SysHelper::get_product_list($company_id);

            $deal = SysCrmDeals::where('id',$deal_id)->first();
            $deal_items = SysCrmQuoteItems::where('deal_id',$deal_id)->where('quote_id',$deal->quote_id)->first();

            

            if(isset($deal)){
                $customer_det = SysChartofAccounts::select('sys_chartofaccounts.id')->join('sys_cust_suppl','sys_cust_suppl.code','sys_chartofaccounts.account_code')->where('sys_cust_suppl.id',$deal->cust_id)->first();
                $customer_id = @$customer_det->id;

                $po = SysPurchaseOrder::where('deal_id',$deal_id)->first();
                $lpo_no = @$po->doc_number;
                $lpo_date = @$po->po_date;
                $payment_terms = @$deal_items->payment_terms;

                $si = SysSalesInvoice::where('deal_id',$deal_id)->first();
                $si_no = @$si->doc_number;
                $si_date = @$si->doc_date;
                $sales_man = @$deal->owner;
                $supp_name = @$po->accountname->account_name;

                
            $deal_acc = SysChartofAccounts::where('id',$customer_id)->where('subgroup2',7)->where('status',1)->first();
            $deal_det = SysCrmDeals::where('id',$deal_id)->first();
            $deal_cust = SysCustSuppl::where('id',$deal->cust_id)->where('catid',1)->where('status',1)->first();
            $deal_enduser = SysCrmEndUser::where('deal_id',$deal_id)->where('status',1)->first();

            } else {
                $customer_id = "";
                $lpo_no = "";
                $lpo_date = "";
                $payment_terms = "";
                $si_no = "";
                $si_date = "";
                $sales_man = "";
                $supp_name = "";
            }

            $paymentterms = SysPaymentTerms::select('id','title')->orderby('title','asc')->get();
            
            $select_cart = SysDealDlnItemsCart::select('sys_deal_dln_items_cart.*','sm_items.part_number as partno')
            ->join('sm_items','sm_items.id','sys_deal_dln_items_cart.part_number')
            ->where('cart_id',session('logged_session_data.cart_id'))
            ->where('sys_deal_dln_items_cart.created_by',Auth::user()->id)
            //->where('sys_deal_dln_items_cart.deal_id',$deal_id)
            ->where('sys_deal_dln_items_cart.status',1)->orderby('sys_deal_dln_items_cart.id','asc')->get();


            
            $query = SysDeliveryNote::select(DB::raw('sys_delivery_note.*, ( SELECT GROUP_CONCAT(sia.doc_file) FROM sys_sales_invoice_att sia JOIN ( SELECT MAX(id) AS max_id FROM sys_sales_invoice WHERE doc_number = sys_delivery_note.invoice_no ) si ON sia.siv_id = si.max_id ) AS attach,(SELECT GROUP_CONCAT(doc_number) 
 FROM sys_sales_return 
 WHERE dn_doc_number COLLATE utf8mb4_unicode_ci = sys_delivery_note.doc_number COLLATE utf8mb4_unicode_ci) AS srtno, (SELECT SUM(taxableamount)+SUM(vatamount) FROM sys_delivery_note_items WHERE dn_id=sys_delivery_note.id) as amount, (SELECT GROUP_CONCAT(code) FROM sys_crm_deals WHERE id=sys_delivery_note.deal_id) AS code'));
            $query->wherein('company_id',$company_id);            
            $query->orderby('doc_number','desc');
            $deliverynote = $query->paginate(50);
            $pending_si=0;
            
            $countries = SysCountries::orderby('name','asc')->get();
            $states = SysStates::orderby('name','asc')->get();
            $customertype = SysCustomerType::orderby('title','asc')->get();
            $saletype = SysSaleType::orderby('title','asc')->get();

            
            return view('backEnd.deliverynote.deliverynoteadd', compact('currency','customer','paymentterms','company','staff','select_cart','deal_id','from_deal','items','customer_id','lpo_no','lpo_date','payment_terms','si_no','si_date','sales_man','supp_name','deliverynote','pending_si','countries','states','customertype','saletype','deal_cust','deal_acc','deal_det','deal_enduser'));




        }catch (\Exception $e) {
            return $e;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    // public function get_si_list_delivery_note(Request $request){     
    //     //$select_sub_category = SysSalesInvoice::all();
    //     $select_sub_category = SysSalesInvoice::select('id','doc_number')->where('customer',$request->cus_id)->get();
    //     return response()->json([$select_sub_category]);
    // }

    public function get_si_list_for_delivery_note(Request $request){
        //$ids[] = $request->pi_ids;
        $explode_id = array_map('intval', explode(',', $request->si_ids));
        
        $items = DB::select("CALL set_delivery_note($request->si_ids)");
        
        //$items = SysSalesInvoiceItems::select('sys_sales_invoice_items.*', 'sm_items.part_number','sm_items.description')->join('sm_items','sys_sales_invoice_items.part_number','sm_items.id')->whereIn('si_id',$explode_id)->where('delivery_status',0)->get();

		if(!empty($items)){
			return json_encode($items);
        }        
    }

    function adddealitemstodlncart(Request $request)
    {
        //return $request->all();
        try{
            if(!isset($request->selected_item_id)){
                Toastr::error('Operation Failed! No Items Selected', 'Failed');
                return redirect()->back();
            }
            $deal = SysCrmDeals::where('id',$request->deal_id)->first();
            $customer_reference = $deal->customername->name;
            $salesman_name = optional($deal->ownername)->full_name ?? '';

            $deal_id = $deal->id;
            //$tax = SysHelper::get_company_tax($deal->company_id);
            $account_id = SysHelper::get_company_account_id($deal->cust_id);
            //$account_id = $deal->cust_id;
            //return $account_id;
            
            

            
            /*$deal_items = SysCrmQuoteItems::select('sys_crm_quote_items.*','sm_items.part_number')
            ->join('sm_items','sm_items.id','sys_crm_quote_items.product_id')
            ->where('deal_id',$request->dln_deal_id)->where('quote_id',$deal->quote_id)->orderby('sys_crm_quote_items.id','desc')->get();*/
            //return $deal_items;

            // dd($request->all());
            
            for($i=0; $i < count($request->item_id); $i++){
                for($j=0; $j < count($request->selected_item_id); $j++){
                    if($request->selected_item_id[$j]==$request->roids[$i]){
                        if($request->qty[$i] !=0){
                            //$description = DB::table('sys_crm_quote_items')->where('product_id',$request->product_id[$i])->where('deal_id',$request->deal_id)->value('description');
                            $data[]=[
                                'cart_id' => session('logged_session_data.cart_id'),
                                'part_number' => $request->product_id[$i],
                                'part_number_txt' => $request->part_no_text[$i],
                                'description' => $request->description[$i],
                                'tax' => $request->tax[$i],
                                'qty' => $request->qty[$i],
                                'unitprice' => str_replace(',', '', $request->unitprice[$i]),
                                'value' => str_replace(',', '', $request->unitprice[$i]) * $request->qty[$i],
                                'discount' => str_replace(',', '', $request->discount[$i]),
                                'fright' => 0.00,
                                'customcharges' => 0.00,
                                'taxableamount' => (str_replace(',', '', $request->unitprice[$i]) * $request->qty[$i]) - str_replace(',', '', $request->discount[$i]),
                                'vatamount' => ((str_replace(',', '', $request->unitprice[$i]) * $request->qty[$i]) - str_replace(',', '', $request->discount[$i])) * $request->tax[$i]/100,
                                'status' => 1,
                                'created_by' => Auth::user()->id,
                                'created_at' => Carbon::now('+04:00'),
                                'refid' => $request->item_id[$i],
                                'deal_id' => $request->deal_id,
                                'deal_qty' => $request->deal_qty[$i],
                            ];

                            // if(str_contains($request->roids[$i],'a_')){
                            //     SysPurchaseOrderItems::where('id',$request->item_id[$i])->update(['dn_qty' => $request->qty[$i]]);
                            // }
                        }

                    }
                }
            }
            //return $data;
            SysDealDlnItemsCart::where('cart_id', session('logged_session_data.cart_id'))->delete();
            SysDealDlnItemsCart::insert($data);

            // return redirect('delivery-note-add/'.$customer_reference.'/'.$salesman_name.'/'.$account_id.'/'. $deal_id);
        return redirect()->route('deliverynote.add', [
    'deal_id' => $deal_id,
    'customer_reference' => $customer_reference,
    'salesman_name' => $salesman_name,
    'account_id' => $account_id,
]);



            

        }catch (\Exception $e) {
            return $e;
        }
    }
    //add delivery note items from deal track item
    /*function deal_add_selected_deal_items_to_delivery_note_cart(Request $request) {
        //return $request;
        
        try {
            if(!isset($request->selected_item_id)){
                Toastr::error('Operation Failed! No Items Selected', 'Failed');
                return redirect()->back();
            }
            DB::beginTransaction();            
            $deal = SysCrmDeals::where('id',$request->deal_id)->first();
            $customer_reference = $deal->customername->name;
            $salesman_name = $deal->ownername->full_name;
            $deal_id = $deal->id;
            $deal_code = $request->deal_code;

            for($i=0; $i < count($request->item_id); $i++){
                for($j=0; $j < count($request->selected_item_id); $j++){
                    if($request->selected_item_id[$j]==$request->roids[$i]){
                        $data[]=[
                            'cart_id' => session('logged_session_data.cart_id'),
                            'quote_item_id' => $request->selected_item_id[$j],
                            'part_number' => $request->product_id[$i],
                            'part_number_txt' => $request->product_id[$i],
                            'description' => $request->description[$i],
                            'tax' => $request->tax[$i],
                            'qty' => $request->qty[$i],
                            'unitprice' => $request->unitprice[$i],
                            'value' => $request->unitprice[$i]*$request->qty[$i],
                            'discount' => $request->discount[$i],
                            'fright' => 0,
                            'customcharges' => 0,
                            'taxableamount' => ($request->unitprice[$i] * $request->qty[$i]) - $request->discount[$i],
                            'vatamount' => (($request->unitprice[$i] * $request->qty[$i]) - $request->discount[$i]) * $request->tax[$i]/100,
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
            //return $data;
            SysDealPurchaseOrderItems::where('deal_id',$deal->id)->delete();
            SysDealPurchaseOrderItems::insert($data);
            DB::commit();
            Toastr::success('Items added to cart successfully', 'Success');
            return redirect('purchase-order?po_action=add/'.$customer_reference.'/'.$salesman_name .'/'. $deal_id .'/'. $deal_code);

        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }*/




    function salesinvoicepending(Request $request)
    {
        try{            
            $ret = SysSalesInvoice::where('customer',$request->cus_id)->where('company_id',session('logged_session_data.company_id'))->where('dn_id',0)->get();
            return json_encode(array('data'=>$ret));

        }catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data'=>$ret));
        }
    }
    function salesinvoicependingitemlist(Request $request)
    {
        try{
            $ret = SysSalesInvoiceItems::select('sm_items.part_number as partnumber','sm_items.description as description2','sys_sales_invoice_items.*','sys_sales_invoice.lpo_number','sys_sales_invoice.lpo_date','sys_sales_invoice.payment_terms','sys_sales_invoice.currency','sys_sales_invoice.doc_number','sys_sales_invoice.doc_date','sys_sales_invoice.sales_man','sys_sales_invoice.deal_id','sys_sales_invoice.supplier_name','sm_items.product_type','sys_crm_deals.code as deal_code','sys_sales_invoice.device_serial','sys_sales_invoice.end_user_name','sys_sales_invoice.contact_person_name','sys_sales_invoice.contact_person_email','sys_sales_invoice.contact_person_no')
            ->join('sys_sales_invoice','sys_sales_invoice.id','sys_sales_invoice_items.si_id')
            ->join('sm_items','sm_items.id','sys_sales_invoice_items.part_number')
            ->join('sys_crm_deals','sys_crm_deals.id','sys_sales_invoice.deal_id') 
            ->where('si_id',$request->si_id)->get();
            return response()->json([$ret]);

        }catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data'=>$ret));
        }
    }

    public function getChargesBySalesInvoice(Request $request)
    {
        try {
            $rawIds = $request->input('si_id', '');
            $siIds = collect(explode(',', (string) $rawIds))
                ->map(function ($id) {
                    return (int) trim($id);
                })
                ->filter(function ($id) {
                    return $id > 0;
                })
                ->unique()
                ->values();

            $invoiceNo = trim((string) $request->input('invoice_no', ''));
            if ($siIds->isEmpty() && $invoiceNo !== '') {
                $byInvoice = SysSalesInvoice::where('doc_number', $invoiceNo)
                    ->where('status', 1)
                    ->pluck('id');
                if ($byInvoice->count() > 0) {
                    $siIds = $byInvoice
                        ->map(function ($id) {
                            return (int) $id;
                        })
                        ->filter(function ($id) {
                            return $id > 0;
                        })
                        ->unique()
                        ->values();
                }
            }

            $dnId = (int) $request->input('dn_id', 0);
            $dnLocalCharges = collect();
            if ($dnId > 0) {
                $dnLocalCharges = SysSalesInvoiceCFCharges::where('si_id', 0)
                    ->where('si_doc_number', 'DN-' . $dnId)
                    ->where('status', 1)
                    ->get()
                    ->map(function ($row) {
                        return [
                            'date' => $row->date,
                            'bill_number' => $row->bill_number,
                            'cfc_name' => $row->cfc_name,
                            'cfc_credit_account' => $row->cfc_credit_account,
                            'cfc_amount' => $row->cfc_amount,
                            'cfc_remarks' => $row->cfc_remarks,
                        ];
                    });
            }

            if ($siIds->isEmpty()) {
                return response()->json(['data' => $dnLocalCharges->values()]);
            }

            $charges = collect();
            foreach ($siIds as $siId) {
                $siCharges = SysSalesInvoiceCFCharges::where('si_id', $siId)->where('status', 1)->get();
                if ($siCharges->count() > 0) {
                    $charges = $charges->merge($siCharges->map(function ($row) {
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

                // Sales invoice CFC table is source-of-truth for DN as well.
            }
            $charges = $charges
                ->unique(function ($row) {
                    return implode('|', [
                        (string) ($row['date'] ?? ''),
                        (string) ($row['bill_number'] ?? ''),
                        (string) ($row['cfc_name'] ?? ''),
                        (string) ($row['cfc_credit_account'] ?? ''),
                        (string) ($row['cfc_amount'] ?? ''),
                        (string) ($row['cfc_remarks'] ?? ''),
                    ]);
                })
                ->values();

            if ($charges->isEmpty() && $dnLocalCharges->count() > 0) {
                $charges = $dnLocalCharges->values();
            }

            return response()->json(['data' => $charges]);
        } catch (\Exception $e) {
            return response()->json(['data' => [], 'error' => $e->getMessage()], 500);
        }
    }




    public function store(Request $request)
    {
        if($request->customer_id ==""){Toastr::error('Customer not found', 'Failed'); return redirect()->back();}

        if($request->part_number[0] !="none" && $request->qty[0] !="" && $request->unitprice[0] !="") {
            
        } else {
            Toastr::error('Items not found', 'Failed');
            return redirect()->back();
        }

        try{            
        DB::beginTransaction();

            $dn = new SysDeliveryNote();
            $dn->doc_number = SysHelper::get_new_code('sys_delivery_note','DN', 'doc_number');
            $dn->doc_date =Carbon::createFromFormat('d/m/Y', $request->doc_date)->format('Y-m-d');
            $siIdsForDn = $this->resolveSiIdsFromRequest($request);
            if ($siIdsForDn->count() > 0) {
                $dn->ref_si_id = $siIdsForDn->implode(',');
            }
            $dn->customer_id = $request->customer_id;
            $dn->narration = $request->narration;
            $dn->currency = $request->currency;
            $dn->salesman = $request->sales_man;
            $dn->lpo_no = $request->lpo_no;
            $dn->lpo_date = Carbon::createFromFormat('d/m/Y', $request->lpo_date)->format('Y-m-d');
            $dn->issued_by = $request->issued_by;
            $dn->received_by = $request->received_by;
            $dn->supplier_name = $request->supplier_name;
            $dn->deal_id = SysHelper::get_dealid_from_code($request->deal_id);
            $dn->warehouse = $request->warehouse;
            $dn->driver = $request->driver;
            $dn->vehicleno = $request->vehicleno;
            $dn->paymentterms = $request->payment_terms;
            $dn->invoice_no = $request->invoice_no;
            $dn->invoice_date = Carbon::createFromFormat('d/m/Y', $request->invoice_date)->format('Y-m-d');
            $dn->serial_no = $request->device_serial;
          
            // $dn->shipping_name= $request->shipping_name;
            // $dn->shipping_address= $request->shipping_address;

            
            $dn->shipping_address = $request->shipping_address_1;
            $dn->shipping_name = $request->shipping_name;
            $dn->shipping_supplier = $request->shipping_supplier;
            $dn->shipping_contact_no = $request->shipping_contact_no;
            $dn->shipping_email = $request->shipping_email;




          
          
            $dn->customer_type= $request->customer_type;
            $dn->sale_type= $request->sale_type;
            $dn->customer_country= $request->customer_country;
            $dn->customer_state= $request->customer_state;
            $dn->end_user_name= $request->end_user_name;
            $dn->contact_person_name= $request->contact_person_name;
            $dn->contact_person_email= $request->contact_person_email;
            $dn->contact_person_no= $request->contact_person_no;
            $dn->vat_percent= $request->vat_percent?:0;
            $dn->vat_number= $request->vat_number?:null;
            $dn->status = 1;
            $dn->company_id = session('logged_session_data.company_id');
            $dn->created_by = Auth::user()->id;
            $dn->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
            
            $results = $dn->save();
            $dn->toArray();
            
            //return count($request->part_number);
            for($i = 0; $i < count($request->part_number); $i++) {
                if($request->part_number[$i] !="" && $request->qty[$i] !="" && $request->unitprice[$i] !=""){
                    $dnl = new SysDeliveryNoteItems();
                    $dnl->dn_id = $dn->id;
                    $dnl->ref_si_id = $dn->ref_si_id;
                    $dnl->part_number = $request->part_number[$i];
                    $dnl->description = $request->description[$i];
                    $dnl->serial_no = $request->serial_no[$i];
                    $dnl->qty = $request->qty[$i];
                    $dnl->tax = $request->tax[$i];
                    $dnl->unitprice = (float) str_replace(',', '', $request->unitprice[$i] ?? 0);
                    $value = (float) str_replace(',', '', $request->value[$i] ?? 0);
                    $dnl->value = $value;
                    $dnl->discount = (float) str_replace(',', '', $request->discount[$i] ?? 0);
                    $dnl->taxableamount = (float) str_replace(',', '', $request->taxableamount[$i] ?? 0);
                    $dnl->vatamount = (float) str_replace(',', '', $request->vatamount[$i] ?? 0);
                    $dnl->status = 1;
                    $dnl->sort_id = $request->sort_id[$i];

                    try {
                        $dnl->refid = $request->row_id[$i];
                    } catch (\Throwable $th) {
                    }

                    $dnl->created_by = Auth::user()->id;
                    if($request->btnSubmit==2){
                        $dnl->is_deal_aditional = 1;
                    }
                    $dnl->save();
                    
                    if(isset($request->row_id)){
                        if(count($request->row_id)){
                            $crm_quote_item = SysCrmQuoteItems::find($request->row_id[$i]);
                            if($crm_quote_item!=""){
                                $crm_quote_item->dn_qty = $crm_quote_item->dn_qty + $dnl->qty;
                                $crm_quote_item->save();
                            }
                        }
                    }

                    $str_arr = explode (",", $request->serial_no[$i]);
                    /*$str_arr = collect(preg_split('/[\s,]+/', $request->srl[$i], -1, 'PREG_SPLIT_NO_EMPTY'))
                    ->map(fn($s) => strtoupper(trim($s)))->unique()->values()->toArray();*/

                    foreach($str_arr as $srl){
                        $values = array('dn_id' => $dn->id,'part_number' => $request->part_number[$i],'srl_no' => $srl,'item_id' => $dnl->id);
                        DB::table('sys_delivery_note_items_srl')->insert($values);
                    }

                    


                    $key_item = SysPurchaseGrnLicenseKey::where('item_id',$request->part_number[$i])->where('dn_id',-1)->where('cart_id',session('logged_session_data.cart_id'))->where('company_id',session('logged_session_data.company_id'))->get();
                    

                    if(count($key_item)>0){
                        foreach($key_item as $k){
                            SysHelper::set_license_key_trn(4,$dn->id,$dn->doc_date,$dn->doc_number,$k->id,$k->item_id,$k->license_key,$k->exp_date);
                            SysPurchaseGrnLicenseKey::where('item_id',$request->part_number[$i])->where('license_key',$k->license_key)->where('status',1)->where('dn_id',-1)->where('company_id',session('logged_session_data.company_id'))->update(['status' => 2, 'dn_id' => $dn->id, 'updated_by' => Auth::user()->id, 'updated_at' => Carbon::now('+04:00')]);
                        }
                    }

                    /*$total_tax_amount = array_sum($request->taxableamount);
                    $total_cfc_amount1 = DB::select("SELECT SUM(cfc_amount) cfc_amount FROM sys_sales_invoice_cf_charges WHERE si_id=".$request->si_id."");
                    if($total_cfc_amount1[0]->cfc_amount=="")
                    {
                        $total_cfc_amount = 0;
                    }
                    else{ $total_cfc_amount = $total_cfc_amount1[0]->cfc_amount; }*/
                    //return $request->all();
                    //if($request->qty[$i] != 0 && $request->qty[$i] != ""){
                    $discount = ($request->discount[$i] === '' ? '0.00' : $request->discount[$i]);
                    $istock = new SysItemStock();
                    $istock->dln_id = $dn->id;
                    $istock->account_id = $request->customer_id;
                    $istock->partno = $request->part_number[$i];
                    $istock->qty_out = $request->qty[$i];
                    

                    $s_price_out =   ($value - $dnl->discount) / $dnl->qty;


                    $istock->price_out = $s_price_out;
                    $istock->refno = $dn->invoice_no;
                    $istock->doc_number = $dn->doc_number;
                    $istock->doc_date = $dn->doc_date;
                    $istock->deal_id = $dn->deal_id;
                    $istock->slno = $request->serial_no[$i];
                    $istock->status = 1;
                    $istock->created_by = Auth::user()->id;
                    $istock->company_id = session('logged_session_data.company_id');
                    $istock->currency_id = $request->currency;
                    $istock->sales_person = $request->sales_man;
                    $istock->item_id = $dnl->id;
                    $istock->save();
                    //}
                }
            }
            
            if ($siIdsForDn->count() > 0) {
                SysSalesInvoice::whereIn('id', $siIdsForDn->all())->update(['dn_id' => $dn->id]);
            }
            $this->syncSalesInvoiceChargesFromDeliveryNote($request, $dn->ref_si_id, $request->customer_id, $dn->id, $dn->doc_number);

            SysCrmDeals::where('id',$dn->deal_id)->update([
                'dln_id' => $dn->id,
            ]);

            $deal_item = SysDealDlnItemsCart::where('cart_id', session('logged_session_data.cart_id'))->where('deal_id',$dn->deal_id)->get();
            
               
            foreach ($deal_item as $ditm) {
                DB::table('sys_crm_quote_items')
                    ->where('deal_id', $dn->deal_id)
                    ->where('id', $ditm->refid)
                    ->increment('dn_qty', $ditm->qty);
            }
            SysDealDlnItemsCart::where('cart_id', session('logged_session_data.cart_id'))->delete();

//return $deal_item;

            if($request->store_id == "cart"){
                SysDealDlnItemsCart::where('cart_id',session('logged_session_data.cart_id'))->delete();
            }
            
            DB::commit();
            
            if($request->store_id == "cart"){
                Toastr::success('Delivery Note Successfully Created', 'Success');
                $trackid = SysCrmDealTrack::select('id')->where('deal_id',$dn->deal_id)->first();
                if($trackid==""){return redirect('delivery-note');}
                return redirect('crm-deal-track-approval-list/'.$trackid->id);
            }

            
            if($request->btnSubmit==2){
                Toastr::success('Delivery Note Successfully Created', 'Success');
                return redirect('crm-deal-track-approval-list/'.$dn->deal_id);
            }
            else if($request->btnSubmit==1){
                Toastr::success('Delivery Note Successfully Created', 'Success');
                return redirect('delivery-note/'.$dn->id.'/download');
                //return redirect('/delivery-note');
            } else { 
                Toastr::success('Delivery Note Successfully Created', 'Success');
                return redirect('delivery-note');
            }

        }catch (\Exception $e) {
            DB::rollback();
            return $e;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function calcTotal($amt,$tax,$cfc)
    {
        return ($amt/$tax*$cfc) + $amt;
    }
    
    public static function get_srl_no($dln,$partno,$item_id)
    {
        try {
            $dt="";
            $data = DB::table('sys_delivery_note_items_srl')->select('srl_no')->where('dn_id',$dln)->where('item_id',$item_id)->where('part_number',$partno)->pluck('srl_no');
            foreach ($data as $key) {
                if($dt=="") { $dt=$key; }                
                else { $dt .= ','.$key; }
            }
            return $dt;
        } catch (\Throwable $th) {
            return $th;
        }
    }

    


    public function edit($id)
    {
       try{
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $currency = SysCurrencySettings::all();
            $company        = SysCompany::find(session('logged_session_data.company_id'));
            $items = SysHelper::get_product_list($company_id);
            
            $staff = SysHelper::get_sales_persons();
            $paymentterms = SysPaymentTerms::select('id','title')->orderby('title','asc')->get();
            
            $edit=SysDeliveryNote::where('id',$id)->first();
            
            $currencylist2 = DB::table('sys_currency_rate as r')->select('r.id','r.from_currency','r.to_currency','c.code','r.rate')
            ->join('sys_currency as c','c.id','r.to_currency')
            ->where('r.status',1)->where('r.from_currency',$edit->currency)
            ->orderBy('c.code','ASC')->get();
            $select_cart=SysDeliveryNoteItems::where('dn_id',$id)->get();
            $customer = SysChartofAccounts::select('id','account_name')->where('id',$edit->customer_id)->get();
            
            $select_cart = SysDeliveryNoteItems::select('sys_delivery_note_items.*','sm_items.part_number as partno','sm_items.description as description2','sm_items.product_type')
            ->join('sm_items','sm_items.id','sys_delivery_note_items.part_number')
            ->where('sys_delivery_note_items.dn_id',$id)->orderBy('sys_delivery_note_items.sort_id')->get();
            
            $query = SysDeliveryNote::select(DB::raw('sys_delivery_note.*, ( SELECT GROUP_CONCAT(sia.doc_file) FROM sys_sales_invoice_att sia JOIN ( SELECT MAX(id) AS max_id FROM sys_sales_invoice WHERE doc_number = sys_delivery_note.invoice_no ) si ON sia.siv_id = si.max_id ) AS attach,(SELECT GROUP_CONCAT(doc_number) 
 FROM sys_sales_return 
 WHERE dn_doc_number COLLATE utf8mb4_unicode_ci = sys_delivery_note.doc_number COLLATE utf8mb4_unicode_ci) AS srtno, (SELECT SUM(taxableamount)+SUM(vatamount) FROM sys_delivery_note_items WHERE dn_id=sys_delivery_note.id) as amount, (SELECT GROUP_CONCAT(code) FROM sys_crm_deals WHERE id=sys_delivery_note.deal_id) AS code'));
            $query->wherein('company_id',$company_id);            
            $query->orderby('doc_number','desc');
            $deliverynote = $query->paginate(50);
            $pending_si=0;
            
            $countries = SysCountries::orderby('name','asc')->get();
            $states = SysStates::orderby('name','asc')->get();
            $customertype = SysCustomerType::orderby('title','asc')->get();
            $saletype = SysSaleType::orderby('title','asc')->get();


            return  compact('currency','currencylist2','customer','paymentterms','company','staff','edit','select_cart','items','deliverynote','pending_si','countries','states','customertype','saletype');
            // return view('backEnd.deliverynote.deliverynoteedit', compact('currency','currencylist2','customer','paymentterms','company','staff','edit','select_cart','items','deliverynote','pending_si','countries','states','customertype','saletype'));
        }catch (\Exception $e) {
        Toastr::error('Operation Failed', 'Failed');
        return redirect()->back(); 
        }
    }

    public function download(Request $request,$id,$type=null)
    {
       try{
        
        $address = ""; $address2 = ""; $city = ""; $state = ""; $country = ""; $contact_name = ""; $email = ""; $tel = ""; $mob = "";
        $ship_company_name = ""; $delivery_city = ""; $delivery_zip_code = ""; $delivery_country = ""; $delivery_state="";

            $dn = SysDeliveryNote::find($id);
            $dn->ref_si_id = $request->si_id ?: $dn->ref_si_id;
            if(!empty($dn)){
                $company = SysCompany::find($dn->company_id);
                $dn_item = SysDeliveryNoteItems::where('dn_id','=',$dn->id)->get();
                $sup_email = SysCustSuppl::select('sys_cust_suppl.*')->join('sys_chartofaccounts','sys_chartofaccounts.account_code','sys_cust_suppl.code')->where('sys_chartofaccounts.id',$dn->customer_id)->first();
                if(!empty($sup_email)){
                    $add = SysCustSupplAddressbook::where('cust_suppl_id',$sup_email->id)->first();
                }
                
                $contact_name = $sup_email->customer_salutation.' '.$sup_email->first_name.' '.$sup_email->last_name;
                $email = $sup_email->email;
                $tel = $sup_email->contcat_number;
                $mob = $sup_email->mobile;
                $ship_tel=$tel;
                $ship_mob=$mob;
 
                if(!empty($add)){
                    $address = $add->address;
                    $address2 = $add->address2;
                    $city = $add->city;
                    $state = @$add->statename->name;
                    $country = @$add->countryname->name;
                }
            
                if($dn->deal_id != 0 && $dn->deal_id != "") {
                    $deal_details = SysCrmDeals::where('id',$dn->deal_id)->first();
    
                    if(isset($deal_details)){
                        if($deal_details->delivery_company != "") { $ship_company_name = $deal_details->delivery_company; } else { $ship_company_name = $deal_details->customername->name; }
                        
                        if($deal_details->delivery_address1 != "") { $ship_address1 = $deal_details->delivery_address1; } else { $ship_address1 = ""; }
                        if($deal_details->delivery_address2 != "") { $ship_address2 = $deal_details->delivery_address2; } else { $ship_address2 = ""; }
                        if($deal_details->delivery_city != "") { $delivery_city = $deal_details->delivery_city; } else { $delivery_city = $add->city; }
                        if($deal_details->delivery_zip_code != "") { $delivery_zip_code = $deal_details->delivery_zip_code; } else { $delivery_zip_code = ""; }
                        if($deal_details->delivery_country != "") { $delivery_country = $deal_details->country->name; } else { $delivery_country = $add->countryname->name; }
                        if($deal_details->delivery_state != "") { $delivery_state = $deal_details->state->name; } else { $delivery_state = $add->statename->name; }
                        
    
                        if($deal_details->delivery_name != "") { $ship_contact_name = $deal_details->delivery_name; } else { $ship_contact_name = $deal_details->cust_name; }
                        //if($deal_details->delivery_number != "") { $ship_tel = $deal_details->delivery_number; } else { $ship_tel = $deal_details->cust_no; }
                        if($deal_details->delivery_email != "") { $ship_email = $deal_details->delivery_email; } else { $ship_email = $deal_details->cust_email; }
                    }
                }
                else{
                    $ship_company_name="";
                    $ship_contact_name = $contact_name;
                    $ship_email = $email ;
                    //$ship_tel = $tel;
                    $ship_address1 = $add->city;
                    $ship_address2 = "";
                    $delivery_city = $add->city;
                    $delivery_zip_code = "";
                    $delivery_country = $add->countryname->name;
                    $delivery_state = $add->statename->name;
                }
                $data = [
                    'dn' => $dn,
                    'company' => $company,
                    'dn_item' => $dn_item,
                    
                    'email' => $email,
                    'tel' => $tel,
                    'mob' => $mob,
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
                    'ship_mob' => $ship_mob,
                    'ship_email' => $ship_email,

                    // 'email' => $email,
                    // 'tel' => $tel,
                    // 'address' => $address,
                    // 'address2' => $address2,
                    // 'city' => $city,
                    // 'state' => $state,
                    // 'country' => $country,
                ];

                if($dn->company_id == 4){
                    $pdf = PDF::loadView('backEnd.pdf_print.dln_co_uk_pdf', $data);
                }
                else if($dn->company_id == 7 || $dn->company_id == 9 || $dn->company_id == 12){
                    $pdf = PDF::loadView('backEnd.pdf_print.dln_uk_pdf', $data);
                }
                else{
                    //return view('backEnd.pdf_print.dln_pdf', $data);
                    if($type=="t"){
                        $pdf = PDF::loadView('backEnd.pdf_print.dln_normal_pdf', $data);
                    }
                    else {
                         try {                            
                        $pdf = PDF::loadView('backEnd.pdf_print.dln_pdf', $data);
                        $originalPath = storage_path('app/public/original_dn.pdf');
                        $pdf->save($originalPath);

                        require_once base_path('vendor/setasign/fpdi/autoload.php');

                        $sourcePath = storage_path('app/public/original_dn.pdf'); // your original PDF path
                        $pdf = new Fpdi();

                        // Load the source PDF
                        $pageCount = $pdf->setSourceFile($sourcePath);

                        for ($i = 1; $i <= $pageCount; $i++) {
                            // Import current page once
                            $templateId = $pdf->importPage($i);

                            // Add the same page twice
                            for ($j = 0; $j < 2; $j++) {
                                $pdf->AddPage();
                                $pdf->useTemplate($templateId);

                                // Optional: Mark the copy for debugging or info
                                $pdf->SetFont('Arial', '', 10);
                                $pdf->SetTextColor(150, 150, 150);
                                //$pdf->Text(10, 10, "Page {$i} copy " . ($j + 1));
                            }
                        }

                        $filename = $dn->doc_number.'-'.$dn->accountname->account_name.'.pdf';
                        $outputPath = public_path($filename);

                        $pdf->Output('F', $outputPath);
                        return response()->download($outputPath);

                    } catch (\Throwable $th) {
                        return $th;
                    }

                    }
                }

                $pdf->setPaper('A4', 'portrait');
                return $pdf->download($dn->doc_number.'-'.$dn->accountname->account_name.".pdf");
            }
            else {
                return "error!!";
            }
       }catch (\Exception $e) {
        return $e;
        Toastr::error('Operation Failed', 'Failed');
        return redirect()->back(); 
        }
    }
    
    public function view(Request $request,$id)
    {
        try{
             $r = SysHelper::get_data_by_role();
             $company_id = $r[0];
             $customer = SysHelper::get_customer_list_all($company_id);
             $currency = SysCurrencySettings::all();
             $company        = SysCompany::find(session('logged_session_data.company_id'));
             
             $staff = SysHelper::get_sales_persons();
             $paymentterms = SysPaymentTerms::select('id','title')->orderby('title','asc')->get();
             
             $edit=SysDeliveryNote::where('id',$id)->first();
             $select_cart=SysDeliveryNoteItems::where('dn_id',$id)->get();
             
             $select_cart = SysDeliveryNoteItems::select('sys_delivery_note_items.*','sm_items.part_number as partno','sm_items.description as description2')
             ->join('sm_items','sm_items.id','sys_delivery_note_items.part_number')
             ->where('sys_delivery_note_items.dn_id',$id)->get();
 
 
             return view('backEnd.deliverynote.deliverynoteview', compact('currency','customer','paymentterms','company','staff','edit','select_cart'));
         }catch (\Exception $e) {
         Toastr::error('Operation Failed', 'Failed');
         return redirect()->back(); 
         }
    }

    public function update(Request $request, $id)
    {
        //return $request->all();        
        if($request->customer_id ==""){Toastr::error('Customer not found', 'Failed'); return redirect()->back();}
        if($request->part_number[0] !="none" && $request->qty[0] !="" && $request->unitprice[0] !="") {            
        } else {
            Toastr::error('Items not found', 'Failed');
            return redirect()->back();
        }

        try {
            DB::beginTransaction();
            
            $dn = SysDeliveryNote::find($id);
            $dn->doc_date = Carbon::createFromFormat('d/m/Y', $request->doc_date)->format('Y-m-d');
            $siIdsForDn = $this->resolveSiIdsFromRequest($request);
            if ($siIdsForDn->count() > 0) {
                $dn->ref_si_id = $siIdsForDn->implode(',');
            } else {
                $dn->ref_si_id = null;
            }
            if($request->doc_number != $request->doc_number_main){
                $exists = SysDeliveryNote::where('doc_number', $request->doc_number)->exists();
                if ($exists) {                    
                    DB::rollback();                    
                    Toastr::error('Operation Failed. Document number already exists', 'Failed');
                    return redirect()->back();
                }
                $dn->doc_number = $request->doc_number;
            }
            $dn->customer_id = $request->customer_id;
            $dn->narration = $request->narration;
            $dn->currency = $request->currency;
            $dn->salesman = $request->sales_man;
            $dn->lpo_no = $request->lpo_no;
            $dn->lpo_date = Carbon::createFromFormat('d/m/Y', $request->lpo_date)->format('Y-m-d');
            $dn->issued_by = $request->issued_by;
            $dn->received_by = $request->received_by;
            $dn->supplier_name = $request->supplier_name;
            $dn->deal_id = SysHelper::get_dealid_from_code($request->deal_id);
            $dn->warehouse = $request->warehouse;
            $dn->driver = $request->driver;
            $dn->vehicleno = $request->vehicleno;
            $dn->paymentterms = $request->payment_terms;
            $dn->invoice_no = $request->invoice_no;
            $dn->invoice_date = Carbon::createFromFormat('d/m/Y', $request->invoice_date)->format('Y-m-d');
      
            $dn->serial_no = $request->device_serial;


            // $dn->shipping_name= $request->shipping_name;
            // $dn->shipping_address= $request->shipping_address;


            $dn->shipping_address = $request->shipping_address_1;
            $dn->shipping_name = $request->shipping_name;
            $dn->shipping_supplier = $request->shipping_supplier;
            $dn->shipping_contact_no = $request->shipping_contact_no;
            $dn->shipping_email = $request->shipping_email;


            $dn->customer_type= $request->customer_type;
            $dn->sale_type= $request->sale_type;
            $dn->customer_country= $request->customer_country;
            $dn->customer_state= $request->customer_state;
            $dn->end_user_name= $request->end_user_name;
            $dn->contact_person_name= $request->contact_person_name;
            $dn->contact_person_email= $request->contact_person_email;
            $dn->vat_percent= $request->vat_percent?:0;
            $dn->vat_number= $request->vat_number?:null;
            $dn->contact_person_no= $request->contact_person_no;
            $dn->status = 1;
            $dn->company_id = session('logged_session_data.company_id');
            $dn->updated_by = Auth::user()->id;
            $dn->updated_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
            $results = $dn->save();
            $dn->toArray();
            
            SysDeliveryNoteItems::where('dn_id',$id)->delete();
            DB::table('sys_delivery_note_items_srl')->where('dn_id',$id)->delete();
            SysItemStock::where('dln_id',$id)->delete();

            for($i = 0; $i < count($request->part_number); $i++) {
                if($request->part_number[$i] !="" && $request->qty[$i] !="" && $request->unitprice[$i] !=""){
                    $dnl = new SysDeliveryNoteItems();
                    $dnl->dn_id = $dn->id;
                    $dnl->ref_si_id = $dn->ref_si_id;
                    $dnl->part_number = $request->part_number[$i];
                    $dnl->description = $request->description[$i];
                    $dnl->serial_no = $request->serial_no[$i];
                    $dnl->qty = $request->qty[$i];
                    $dnl->tax = $request->tax[$i];
                    $dnl->unitprice = (float) str_replace(',', '', $request->unitprice[$i] ?? 0);
                    $value = (float) str_replace(',', '', $request->value[$i] ?? 0);
                    $dnl->value = $value;
                    $dnl->discount = (float) str_replace(',', '', $request->discount[$i] ?? 0);
                    $dnl->taxableamount = (float) str_replace(',', '', $request->taxableamount[$i] ?? 0);
                    $dnl->vatamount = (float) str_replace(',', '', $request->vatamount[$i] ?? 0);
                    $dnl->status = 1;
                    $dnl->created_by = Auth::user()->id;
                    $dnl->sort_id = $request->sort_id[$i];
                    $dnl->save();
                    $dnl->toArray();

                    $str_arr = explode (",", $request->serial_no[$i]);
                    /*$str_arr = collect(preg_split('/[\s,]+/', $request->srl[$i], -1, 'PREG_SPLIT_NO_EMPTY'))
                    ->map(fn($s) => strtoupper(trim($s)))->unique()->values()->toArray();*/
                    foreach($str_arr as $srl){
                        $values = array('dn_id' => $dn->id,'part_number' => $request->part_number[$i],'srl_no' => $srl,'item_id' => $dnl->id);
                        DB::table('sys_delivery_note_items_srl')->insert($values);
                    }

                    /*$total_tax_amount = array_sum($request->taxableamount);
                    $total_cfc_amount1 = DB::select("SELECT SUM(cfc_amount) cfc_amount FROM sys_sales_invoice_cf_charges WHERE si_id=".$request->si_id."");
                    if($total_cfc_amount1[0]->cfc_amount=="")
                    {
                        $total_cfc_amount = 0;
                    }
                    else{ $total_cfc_amount = $total_cfc_amount1[0]->cfc_amount; }*/

                    // Same as store(): staged license keys (dn_id -1 + cart) must be committed on save.
                    $key_item = SysPurchaseGrnLicenseKey::where('item_id',$request->part_number[$i])->where('dn_id',-1)->where('cart_id',session('logged_session_data.cart_id'))->where('company_id',session('logged_session_data.company_id'))->get();
                    if(count($key_item)>0){
                        foreach($key_item as $k){
                            SysHelper::set_license_key_trn(4,$dn->id,$dn->doc_date,$dn->doc_number,$k->id,$k->item_id,$k->license_key,$k->exp_date);
                            SysPurchaseGrnLicenseKey::where('item_id',$request->part_number[$i])->where('license_key',$k->license_key)->where('status',1)->where('dn_id',-1)->where('company_id',session('logged_session_data.company_id'))->update(['status' => 2, 'dn_id' => $dn->id, 'updated_by' => Auth::user()->id, 'updated_at' => Carbon::now('+04:00')]);
                        }
                    }
                    
                    $discount = ($request->discount[$i] === '' ? '0.00' : $request->discount[$i]);
                    $istock = new SysItemStock();
                    $istock->dln_id = $dn->id;
                    $istock->account_id = $request->customer_id;
                    $istock->partno = $request->part_number[$i];
                    $istock->qty_out = $request->qty[$i];

                    $s_price_out =   ($value - $dnl->discount) / $dnl->qty;

                    $istock->price_out = $s_price_out;
                    $istock->refno = $dn->invoice_no;
                    $istock->doc_number = $dn->doc_number;
                    $istock->doc_date = $dn->doc_date;
                    $istock->deal_id = $dn->deal_id;
                    $istock->item_id = $dnl->id;
                    $istock->status = 1;
                    $istock->created_by = Auth::user()->id;
                    $istock->company_id = session('logged_session_data.company_id');
                    $istock->currency_id = $request->currency;
                    $istock->sales_person = $request->sales_man;
                    $istock->item_id = $dnl->id;
                    $istock->save();
                }
            }

            if ($siIdsForDn->count() > 0) {
                SysSalesInvoice::whereIn('id', $siIdsForDn->all())->update(['dn_id' => $dn->id]);
            }
            $this->syncSalesInvoiceChargesFromDeliveryNote($request, $dn->ref_si_id, $request->customer_id, $dn->id, $dn->doc_number);
            DB::commit();
            if($request->btnSubmit==1){
                Toastr::success('Delivery Note Successfully Updated', 'Success');
                return redirect('delivery-note/'.$dn->id.'/download');
                //return redirect('/delivery-note');
            } else { 
                Toastr::success('Delivery Note Successfully Updated', 'Success');
                return redirect('delivery-note/'.$dn->id);
            }
            
        } catch (\Exception $e) {
            DB::rollback();
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    private function syncSalesInvoiceChargesFromDeliveryNote(Request $request, $siId, $defaultCreditAccount = null, $dnId = null, $dnDocNumber = null)
    {
        $dnLocalDocKey = !empty($dnId) ? ('DN-' . $dnId) : null;
        $normalizedRows = $this->normalizeCfcRowsFromRequest($request, $defaultCreditAccount);
        $siIds = collect(explode(',', (string) ($siId ?? '')))
            ->map(function ($id) {
                return (int) trim($id);
            })
            ->filter(function ($id) {
                return $id > 0;
            })
            ->unique()
            ->values();

        // Always keep a DN-local snapshot of CFC rows (source for DN edit screen).
        if (!empty($dnLocalDocKey)) {
            DB::table('sys_sales_invoice_cf_charges')->where('si_id', 0)->where('si_doc_number', $dnLocalDocKey)->delete();
            foreach ($normalizedRows as $row) {
                $cfc = new SysSalesInvoiceCFCharges();
                $cfc->si_id = 0;
                $cfc->si_doc_number = $dnLocalDocKey;
                $cfc->date = $row['date'];
                $cfc->bill_number = $row['bill_number'];
                $cfc->cfc_name = $row['cfc_name'];
                $cfc->cfc_credit_account = $row['cfc_credit_account'];
                $cfc->cfc_amount = $row['cfc_amount'];
                $cfc->cfc_remarks = $row['cfc_remarks'];
                $cfc->status = 1;
                $cfc->created_by = Auth::user()->id;
                $cfc->save();
            }
        }

        if ($siIds->count() === 0) {
            // No SI linked: DN-local snapshot above is enough.
            return;
        }

        if (count($normalizedRows) === 0) {
            DB::table('sys_sales_invoice_cf_charges')->whereIn('si_id', $siIds->all())->delete();
            return;
        }

        foreach ($siIds as $singleSiId) {
            DB::table('sys_sales_invoice_cf_charges')->where('si_id', $singleSiId)->delete();

            $si = SysSalesInvoice::find($singleSiId);
            $siDocNumber = !empty($si) ? $si->doc_number : null;

            foreach ($normalizedRows as $row) {
                $cfc = new SysSalesInvoiceCFCharges();
                $cfc->si_id = $singleSiId;
                $cfc->si_doc_number = $siDocNumber;
                $cfc->date = $row['date'];
                $cfc->bill_number = $row['bill_number'];
                $cfc->cfc_name = $row['cfc_name'];
                $cfc->cfc_credit_account = $row['cfc_credit_account'];
                $cfc->cfc_amount = $row['cfc_amount'];
                $cfc->cfc_remarks = $row['cfc_remarks'];
                $cfc->status = 1;
                $cfc->created_by = Auth::user()->id;
                $cfc->save();
            }
        }
    }

    private function normalizeCfcRowsFromRequest(Request $request, $defaultCreditAccount = null)
    {
        $rows = [];
        $count = is_array($request->cfc_name ?? null) ? count($request->cfc_name) : 0;
        for ($i = 0; $i < $count; $i++) {
            $name = $request->cfc_name[$i] ?? null;
            $amountRaw = $request->cfc_amount[$i] ?? '0';
            $bill = $request->cfc_bill_no[$i] ?? null;
            $remarks = $request->cfc_remarks[$i] ?? null;
            $date = !empty($request->cfc_date[$i]) ? SysHelper::normalizeToYmd($request->cfc_date[$i]) : null;
            $creditAccountId = !empty($request->cfc_credit_account[$i]) ? $request->cfc_credit_account[$i] : $defaultCreditAccount;

            $hasAnyChargeValue = !empty($name) || !empty($amountRaw) || !empty($bill) || !empty($remarks) || !empty($date) || !empty($creditAccountId);
            if (!$hasAnyChargeValue) {
                continue;
            }

            $amount = (float) str_replace(',', '', (string) $amountRaw);
            $rows[] = [
                'date' => $date,
                'bill_number' => $bill,
                'cfc_name' => $name,
                'cfc_credit_account' => $creditAccountId,
                'cfc_amount' => $amount,
                'cfc_remarks' => $remarks,
            ];
        }

        return collect($rows)->unique(function ($r) {
            return implode('|', [
                (string) ($r['date'] ?? ''),
                (string) ($r['bill_number'] ?? ''),
                (string) ($r['cfc_name'] ?? ''),
                (string) ($r['cfc_credit_account'] ?? ''),
                (string) ($r['cfc_amount'] ?? ''),
                (string) ($r['cfc_remarks'] ?? ''),
            ]);
        })->values()->all();
    }

    private function resolveSiIdsFromRequest(Request $request)
    {
        $siIds = collect(explode(',', (string) ($request->si_id ?? '')))
            ->map(function ($id) {
                return (int) trim($id);
            })
            ->filter(function ($id) {
                return $id > 0;
            })
            ->unique()
            ->values();

        $invoiceNo = trim((string) ($request->invoice_no ?? ''));
        if ($invoiceNo !== '') {
            $byInvoice = SysSalesInvoice::where('doc_number', $invoiceNo)
                ->where('status', 1)
                ->pluck('id')
                ->map(function ($id) {
                    return (int) $id;
                })
                ->filter(function ($id) {
                    return $id > 0;
                });
            if ($byInvoice->count() > 0) {
                $siIds = $siIds->merge($byInvoice)->unique()->values();
            }
        }

        return $siIds;
    }


    public function delete($id)
    {
        try{
            DB::beginTransaction();
            SysDeliveryNote::where('id',$id)->update(['status' => 2]);
            SysDeliveryNoteItems::where('dn_id',$id)->update(['status' => 2]);
            DB::table('sys_delivery_note_items_srl')->where('dn_id',$id)->update(['status' => 2]);
            SysItemStock::where('dln_id',$id)->update(['status' => 2]);
            //SysCrmDeals::where('dln_id',$id)->update([ 'dln_id' => 0]);
            DB::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();

        }catch (\Exception $e) {
            DB::rollBack();
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back();
        }
    }
    public function restore($id)
    { 
        try{
            DB::beginTransaction();
            SysDeliveryNote::where('id',$id)->update(['status' => 1]);
            SysDeliveryNoteItems::where('dn_id',$id)->update(['status' => 1]);
            DB::table('sys_delivery_note_items_srl')->where('dn_id',$id)->update(['status' => 1]);
            SysItemStock::where('dln_id',$id)->update(['status' => 1]);
            //SysCrmDeals::where('dln_id',$id)->update([ 'dln_id' => 0]);
            DB::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();

        }catch (\Exception $e) {
            DB::rollBack();
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back();
        }
    }

    public function item_delete(Request $request)
    {
        try{            
            SysDeliveryNoteItems::where('id',$request->id)->delete();
            SysItemStock::where('dln_id',$request->dln_id)->where('partno',$request->partno)->delete();
            
            $ret = 'SUCCESS';
            return json_encode(array('data'=>$ret));
        }catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data'=>$ret));
        }
    }
    
    function item_update(Request $request)
    {
        try{
            db::beginTransaction();
            DB::table('sys_delivery_note_items')->where('id',$request->id)->update(
                [
                    'part_number' => $request->part_number,
                    'description' => $request->description,
                    'tax' => $request->tax,
                    'qty' => $request->qty,
                    'unitprice' => $request->unitprice,
                    'value' => $request->value,
                    'discount' => $request->discount,
                    'taxableamount' => $request->taxableamount,
                    'vatamount' => $request->vatamount,
                    'serial_no' => $request->serial_no,
                    'status' => 1,
                    'updated_by' => Auth::user()->id,
                    'updated_at' => Carbon::now('+04:00'),
                ]
                );
                DB::table('sys_item_stock')->where('dln_id',$request->dln_id)->where('partno',$request->part_number)->update(
                    [
                        'qty_out' => $request->qty,
                        'price_out' => ($request->value-$request->discount)/$request->qty,
                        'status' => 1,
                        'updated_by' => Auth::user()->id,
                        'updated_at' => Carbon::now('+04:00'),
                    ]
                );
                
                DB::table('sys_delivery_note_items_srl')->where('dn_id',$request->dln_id)->where('item_id',$request->id)->where('part_number',$request->part_number)->delete();
                $str_arr = explode (",", $request->serial_no);
                    foreach($str_arr as $srl){
                        $values = array('dn_id' => $request->dln_id,'part_number' => $request->part_number,'srl_no' => $srl, 'item_id' => $request->id);
                        DB::table('sys_delivery_note_items_srl')->insert($values);
                    }
                    db::commit();
                    
            $ret="SUCCESS";
            return json_encode(array('data'=>$ret));
        }catch (\Exception $e) {
            db::rollBack();
            $ret = $e;
            return json_encode(array('data'=>$ret));
        }
    }
    function item_add(Request $request)
    {
        try{
            db::beginTransaction();
            $item_id = DB::table('sys_delivery_note_items')->insertGetId(
                [
                    'dn_id' => $request->dln_id,
                    'part_number' => $request->part_number,
                    'description' => $request->description,
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
                DB::table('sys_item_stock')->insert(
                    [
                        'dln_id' => $request->dln_id,                        
                        'account_id' => $request->account_id,
                        'partno' => $request->part_number,
                        'refno' => $request->refno,
                        'doc_number' => $request->doc_number,
                        'doc_date' => $request->doc_date,
                        'deal_id' => SysHelper::get_dealid_from_code($request->deal_id),
                        'slno' => $request->serial_no,
                        'qty_out' => $request->qty,
                        'price_out' => ($request->value-$request->discount)/$request->qty,
                        'status' => 1,
                        'created_by' => Auth::user()->id,
                        'created_at' => Carbon::now('+04:00'),
                        'company_id' => session('logged_session_data.company_id'),
                        'sales_person' => $request->sales_man,
                    ]
                );
                
                $str_arr = explode (",", $request->serial_no);
                foreach($str_arr as $srl){
                    $values = array('dn_id' => $request->dln_id,'part_number' => $request->part_number,'srl_no' => $srl,'item_id' => $item_id);
                    DB::table('sys_delivery_note_items_srl')->insert($values);
                }
                db::commit();
                    
            $ret="SUCCESS";
            return json_encode(array('data'=>$ret));
        }catch (\Exception $e) {
            db::rollBack();
            $ret = $e;
            return json_encode(array('data'=>$ret));
        }
    }

    function item_add_cart_discount(Request $request)
    {
        try{
            if($request->discount_amount != ""){                
                $qt = SysDeliveryNoteItems::where('dn_id',$request->discount_amount_dn_id)->get();
                $discount_amount = $request->discount_amount;
                $total = $qt->sum('value');                
                foreach($qt as $t){
                    $new_discount = ($t->value / $total) * $discount_amount;
                    SysDeliveryNoteItems::where('id',$t->id)->update(
                        [
                            'discount' => $new_discount,
                            'taxableamount' => ($t->unitprice * $t->qty)-$new_discount,
                            'vatamount' => (($t->unitprice * $t->qty)-$new_discount)*$t->tax/100,
                        ]
                    );
                }
            }
            Toastr::success('Discount Updated Successfully.', 'Success');
            return redirect()->back(); 
        }catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back(); 
        }
    }

    function item_add_cart(Request $request)
    {
        try{
            db::beginTransaction();
            

                $data[]=[
                    'cart_id' => session('logged_session_data.cart_id'),
                    'part_number' => $request->part_number,
                    'part_number_txt' => $request->part_number,
                    'description' => $request->description,
                    'tax' => $request->tax,
                    'qty' => $request->qty,
                    'unitprice' => $request->unitprice,
                    'value' => $request->unitprice*$request->qty,
                    'discount' => $request->discount,
                    'fright' => 0.00,
                    'customcharges' => 0.00,
                    'taxableamount' => ($request->unitprice * $request->qty) - $request->discount,
                    'vatamount' => (($request->unitprice * $request->qty) - $request->discount) * $request->tax/100,
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                    'deal_id' => 0,
                    'deal_qty' => 0,
                ];
                
                SysDealDlnItemsCart::insert($data);
                // $str_arr = explode (",", $request->serial_no);
                // foreach($str_arr as $srl){
                //     $values = array('dn_id' => $request->dln_id,'part_number' => $request->part_number,'srl_no' => $srl);
                //     DB::table('sys_delivery_note_items_srl')->insert($values);
                // }

                db::commit();
                    
            $ret="SUCCESS";
            return json_encode(array('data'=>$ret));
        }catch (\Exception $e) {
            db::rollBack();
            $ret = $e;
            return json_encode(array('data'=>$ret));
        }
    }

    function deliverynoteupdate_currency(Request $request)
    {
        try{
            if($request->to_currency_id != $request->from_currency_id){
                
                $to_currency = SysCurrencyRate::where('id', $request->to_currency_id)->value('to_currency');
                SysDeliveryNote::where('id',$request->cur_dn_id)->update(['currency' => $to_currency]);
                $qt = SysDeliveryNoteItems::where('dn_id',$request->cur_dn_id)->get();
                foreach($qt as $t){
                    $new_price = $t->unitprice * $request->to_currency_rate;

                    $new_discount = $t->discount * $request->to_currency_rate;

                    SysDeliveryNoteItems::where('id',$t->id)->update(
                        [
                            'unitprice' => $new_price,
                            'value' => $new_price*$t->qty,
                            'discount' => $new_discount,
                            'taxableamount' => ($new_price*$t->qty) - $new_discount,
                            'vatamount' => (($new_price*$t->qty) - $new_discount)*$t->tax/100,
                        ]
                    );

                    SysItemStock::where('doc_number',$request->cur_dn_doc_no)->where('partno',$t->part_number)->update(
                        ['price_out' => ($new_price*$t->qty) - $new_discount,'currency_id' => $to_currency]);
                }
            }

            Toastr::success('Currency Updated Successfully. Please Update Delivery Note', 'Success');
            return redirect()->back(); 

        }catch (\Exception $e) {
            return $e;
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
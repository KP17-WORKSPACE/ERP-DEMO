<?php

namespace App\Http\Controllers;

use App\ApiBaseMethod;
use App\SmDesignation;
use App\SmInspectingDepartment;
use App\SmItem;
use Illuminate\Http\Request;
use App\SmItemStore;
use App\SmStaff;
use App\SysBrand;
use App\SysChartofAccounts;
use App\SysCompany;
use App\SysCountries;
use App\SysCrmDeals;
use App\SysCrmDealsCollaboration;
use App\SysCrmDealsComments;
use App\SysCrmDealTrack;
use App\SysCrmDealTrackTemp;
use App\SysCrmEndUser;
use App\SysCrmLeads;
use App\SysCrmQuoteCharges;
use App\SysCrmQuoteCSItems;
use App\SysCrmQuoteItems;
use App\SysCrmService;
use App\SysCrmServiceComments;
use App\SysCrmSupport;
use App\SysCrmSupportActivity;
use App\SysCurrencySettings;
use App\SysCustSuppl;
use App\SysCustSupplAddressbook;
use App\SysHelper;
use App\SysItemOpeningStock;
use App\SysItemStock;
use App\SysPaymentTerms;
use App\SysPurchaseAuto;
use App\SysPurchaseOrderItems;
use App\SysShipping;
use App\SysStates;
use App\SysStockIn;
use App\SysStockInSerialNo;
use App\SysSupplierType;
use App\SysVat;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Return_;
use Validator;
use App\SysCrmQuoteCart;
use App\SysCrmQuoteCartEdit;
use App\SysCurrency;
use App\SysCustomerType;
use App\ReserveStock;
use App\SysCurrencyRate;

class SysCrmDealsController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
    }

    public function company_error(Request $request)
    {
        //return view('backEnd.crm.CompanyError');
        return redirect('crm-dashboard');
    }

    public function add()
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $currency = SysCurrencySettings::select('id', 'code')->get();
            $company = SysCompany::orderby('sort_id', 'asc');
            if (Auth::user()->role_id != 1) {
                $companyAccess = SysHelper::get_company_access();
                if (is_array($companyAccess) && count($companyAccess) > 0) {
                    $company->whereIn('id', $companyAccess);
                } else {
                    $company->whereRaw('0 = 1');
                }
            }
            $company = $company->get();
            $product = SysHelper::get_product_list($company_id);

            $staff_query = SmStaff::select('user_id', 'full_name');
            if (Auth::user()->role_id != 1 && Auth::user()->role_id != 35) {
                if (Auth::user()->role_id == 3) { //Department Head
                    $users = SmStaff::select('user_id')->where('department_id', session('logged_session_data.department_id'))->get();
                    foreach ($users as $value) {
                        $userid[] = $value->user_id;
                    }
                    $staff_query->wherein('user_id', $userid);
                } else {
                    $staff_query->where('user_id', Auth::user()->id);
                }
            }
            $staff_query->wherein('company_id', $company_id);
            $staff_query->where('active_status', 1);
            $staff = $staff_query->get();

            $items = SysHelper::get_product_list($company_id);


            $brand = SysBrand::select('title')->orderby('title', 'asc')->get();
            $country = SysCountries::select('id', 'name', 'iso2')->get();

            $query = SysCrmDeals::select('id', 'code', 'deal_name', 'estimated_close_date', 'date', 'stage', 'deal_currency', 'company_id', 'deal_value', 'deal_profit', 'created_at', 'updated_at', 'cust_id', 'owner', 'deleted_at', 'quote_id')->where('stage', '!=', 0);


            if (session('logged_session_data.company_id') != 1) {
                $query->wherein('company_id', $company_id);
            }

            $companylist = SysCompany::orderby('sort_id', 'asc');
            if (Auth::user()->role_id != 1) {
                $companyAccess = SysHelper::get_company_access();
                if (is_array($companyAccess) && count($companyAccess) > 0) {
                    $companylist->whereIn('id', $companyAccess);
                } else {
                    $companylist->whereRaw('0 = 1');
                }
            }
            $companylist = $companylist->get();
            $currencylist = SysCurrencySettings::select('id', 'code')->where('status', 1)->orderBy('code', 'ASC')->get();
            $paymentterms = SysPaymentTerms::all();

            $deals = $query->orderby('id', 'desc')->paginate(200);

            $rolearray = [1, 28, 27, 10, 3, 2, 4, 29, 26, 9, 30, 8, 32];
            if (in_array(Auth::user()->role_id, $rolearray)) {
                $sales_person = SysHelper::get_only_sales_persons();
                $vendors = SysHelper::get_customer_list_deal_lead_all_role();

            } else {
                $vendors = SysHelper::get_customer_list_deal_lead();
                $sales_person = SysHelper::get_only_sales_persons();
            }


            $deal_company = SysCompany::find(session('logged_session_data.company_id'));




            $designation = SmDesignation::select('title')->where('active_status', 1)->get();



            $customs_freight_account = SysHelper::get_customs_freight_accounts_for_sales($company_id);

            $supplier = SysHelper::get_supplier_list($company_id);



            $cart = SysCrmQuoteCart::where(['cart_id' => session('logged_session_data.cart_id'), 'user_id' => Auth::user()->id])->get();

            // dd($cart);



            return compact('currency', 'vendors', 'company', 'staff', 'brand', 'country', 'product', 'deals', 'currencylist', 'paymentterms', 'companylist', 'deal_company', 'designation', 'sales_person', 'customs_freight_account', 'supplier', 'items', 'cart');
            // return view('backEnd.crm.DealForm', compact('currency', 'vendors', 'company', 'staff', 'brand', 'country', 'product', 'deals', 'currencylist', 'paymentterms', 'companylist', 'deal_company', 'designation', 'sales_person', 'customs_freight_account', 'supplier', 'items', 'cart'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function store(Request $request) //kunal modified
    {



        $tags = "";
        if ($request->tags != "") {
            $tags = implode(",", $request->tags);
        }
        $doc_file = "";
        if ($request->file('doc') != "") {
            $file = $request->file('doc');
            $doc_file = md5(time()) . "." . $file->getclientoriginalextension();
            $file->move('public/uploads/crm_deal_doc/', $doc_file);
            $doc_file = $doc_file;
        }
        if ($request->is_professional_service == 1) {
            $is_professional_service = 1;
        } else {
            $is_professional_service = 0;
        }

        DB::beginTransaction();
        try {
            $flag = SysCrmDeals::where([
                ['date', SysHelper::normalizeToYmd($request->date)],
                ['deal_name', $request->deal_name],
                ['cust_id', $request->cust_id],
                ['source', $request->source],
                ['owner', $request->owner],
                ['tags', $tags],
                ['created_by', Auth::user()->id],
                ['company_id', $request->company]
            ])->first();
            if ($flag) {
                Toastr::success('Deal has been added successfully', 'Success');
                return redirect('crm-deals/show');
            } else {
                if ($request->deal_value == "") {
                    $deal_value = "0.00";
                } else {
                    $deal_value = $request->deal_value;
                }

                $delivery_company = DB::table('sys_cust_suppl')->where('id', $request->cust_id)->first();

                $scd = new SysCrmDeals();
                $scd->code = SysHelper::get_new_code_lead('sys_crm_deals', 'DL', 'code', $request->company);
                $scd->date = SysHelper::normalizeToYmd($request->date);
                $scd->deal_name = $request->deal_name;
                $scd->cust_id = $request->cust_id;
                $scd->cust_name = $request->cust_name;
                $scd->cust_no = $request->cust_no;
                $scd->cust_email = $request->cust_email;
                $scd->deal_value = $deal_value;
                $scd->source = $request->source;
                $scd->source_o = $request->source_o;
                $scd->tags = $request->tags ? implode(',', $request->tags) : null;
                $scd->stage = $request->stage;
                $scd->owner = $request->owner;
                $scd->doc = $doc_file;
                $scd->isproject = $request->isproject;
                $scd->designation = $request->designation;
                $scd->address = $request->address;

                $scd->delivery_company = $delivery_company->id;
                $scd->delivery_name = $delivery_company->customer_salutation . ' ' . $delivery_company->first_name . ' ' . $delivery_company->last_name;
                $scd->delivery_number = $delivery_company->contcat_number;
                $scd->delivery_email = $delivery_company->email;

                $scd->delivery_country = $delivery_company->vat_country;
                $scd->delivery_state = $delivery_company->vat_state;
                $scd->delivery_city = $delivery_company->city;
                $scd->delivery_area = $delivery_company->area;
                $scd->delivery_building = $delivery_company->building_name;
                $scd->delivery_flat_office_no = $delivery_company->flat_office_no;
                $scd->delivery_zip_code = $delivery_company->zip_code;

                $address_id = SysCustSupplAddressbook::where('cust_suppl_id', $request->cust_id)->where('is_shipping', 0)->first();
                if ($address_id) {
                    $scd->delivery_address_select = $address_id->id ?? null;
                }




                $scd->followup_date = Carbon::now()
                    ->addDays(3)
                    ->setTime(11, 0, 0)
                    ->format('Y-m-d H:i:s');

                $scd->note = $request->note;
                $scd->status = $request->status;
                $scd->estimated_close_date = SysHelper::normalizeToYmd($request->estimated_close_date);
                $scd->created_by = Auth::user()->id;
                $scd->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
                //$scd->company_id = session('logged_session_data.company_id');
                $scd->company_id = $request->company;
                $scd->is_professional_service = $is_professional_service;
                $scd->save();
                $scd->toArray();

            



              


               

                //return $request->all();
                if ($request->quotation_generated == 1) {
              

                    $quote_id = SysCrmQuoteItems::where('deal_id', $scd->id)->max('quote_id');
                    $customer = SysCustSuppl::where('id', $request->cust_id)->first();
                    $quote_doc_number = SysHelper::getNextDealQuoteDocNo();

                    if ($request->part_number != null && count($request->part_number) > 0) {


                        for ($i = 0; $i < count($request->part_number); $i++) {

                            if($request->qty[$i] == ""){
                                continue;
                            }

                            if($request->unitprice[$i] == ""){
                                continue;
                            }

                            if($request->part_number[$i] == ""){
                                continue;
                            }
                            
                            
                            $data[] = [
                                'user_id' => $request->owner,
                                'deal_id' => $scd->id,
                                'company_id' => $request->company,
                                'currency_id' => $request->currency_id !== '' ? $request->currency_id : null,

                                'customer_type' => $customer->customer_type,
                                'quote_validity' => $request->quote_validity,
                                'payment_terms' => $request->payment_terms,
                                'delivery_date' => SysHelper::normalizeToYmd($request->estimated_close_date),
                                'payment_terms_txt' => $request->payment_terms_txt,
                                'delivery_time' => $request->delivery_time,
                                'product_id' => $request->part_number[$i],
                                'qty' => $request->qty[$i],
                                'price' => (float) str_replace(',', '', $request->unitprice[$i] ?? 0),
                                'description' => $request->description[$i],
                                'discount' => (float) str_replace(',', '', $request->discount[$i] ?? 0),
                                'vat' => $request->tax[$i],
                                'cost' => (float) str_replace(',', '', $request->cost[$i] ?? 0),
                                'status' => 1,
                                'sort_id' => $i + 1,
                                'created_by' => Auth::user()->id,

                                'product_type' => !empty($request->product_type[$i])
                                    ? $request->product_type[$i]
                                    : null,
                                'quote_id' => $quote_id + 1,
                                'document_number' => $quote_doc_number,
                            ];


                        }

                       

                   
                      

                        SysCrmQuoteItems::insert($data);
                      
                        DB::table('sys_crm_deals')->where('id', $scd->id)
                            ->update(['estimated_close_date' => SysHelper::normalizeToYmd($request->estimated_close_date), 'quote_id' => $quote_id + 1, 'deal_discount' => (float) str_replace(',', '', $request->deal_discount ?? 0), 'deal_discount_vat' => ($request->deal_discount_vat ?? 0), 'terms_and_condition' => $request->terms_and_condition]);
                        SysHelper::deal_updated_at($scd->id);
                      

                        if (!isset($request->tags) || $request->tags == '') {

                            $item = SmItem::find($request->part_number[0]);
                            if ($item) {
                                $brandName = SmItem::getBrandName($item->brand);
                                $scd->tags = $brandName;
                            } else {
                                $scd->tags = '';
                            }

                            $scd->save();
                        }
                    }

                    //sys_crm_quote_charges
                    for ($i = 0; $i < count($request->cfc_name); $i++) {
                        if ($request->cfc_name[$i] != "" && $request->cfc_credit_account[$i] != "" && $request->cfc_amount[$i] != "") {
                            $cfc = new SysCrmQuoteCharges();
                            // Use the newly created deal id (not the request, which may be empty on create)
                            $cfc->deal_id = $scd->id;
                            $cfc->quote_id = $quote_id + 1;
                            $cfc->selling_exp_account = $request->cfc_name[$i];
                            $cfc->credit_account = $request->cfc_credit_account[$i];
                            $cfc->amount = $request->cfc_amount[$i];
                            $cfc->remarks = $request->cfc_remarks[$i];
                            $cfc->status = 1;
                            $cfc->created_by = Auth::user()->id;
                            $cfc->save();
                        }
                    }

                    $scd->stage = 2; //Quotation Generated
                    $scd->save();

                    SysHelper::set_deal_profit($scd->id);
                }

                SysHelper::set_user_custsupp($request->owner, $request->cust_id);

                $results = 0;
                DB::commit();



                if ($results == 0) {

                    if ($request->source == "Gitex") {
                        if ($request->cust_email != "") {
                            SysHelper::notificationMailGitexMail($request->cust_name, $request->cust_email, $request->owner);
                        }
                    }

                    SysCrmQuoteCart::where([
                        'cart_id' => session('logged_session_data.cart_id'),
                        'user_id' => Auth::user()->id,
                    ])->delete();





                    if ($request->btnSubmit == 2) {
                        Toastr::success('Deal has been added successfully. Please Create Quote', 'Success');
                        return redirect('crm-quote/' . $scd->id . '/create');
                    } else {
                        Toastr::success('Deal has been added successfully', 'Success');
                        //return redirect('crm-deals/'.$scd->id.'/view');
                        return redirect('crm-deals/show');
                    }
                } else {
                    Toastr::error('Operation Failed', 'Failed');
                    return redirect()->back();
                }
            }
        } catch (\Exception $e) {
            return $e;
            DB::rollback();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    /*public function store(Request $request)
    {
        $tags = "";
        if($request->tags!="") { $tags =implode(",",$request->tags); }
        $doc_file = "";
        if ($request->file('doc') != "") { 
            $file = $request->file('doc');
            $doc_file = md5(time()) . "." . $file->getclientoriginalextension();
            $file->move('public/uploads/crm_deal_doc/', $doc_file);
            $doc_file = $doc_file;
        }
        if($request->is_professional_service==1){
            $is_professional_service=1;
        } else { $is_professional_service=0; }

        DB::beginTransaction();
        try {
            $flag = SysCrmDeals::where([
                ['date',date('Y-m-d', strtotime($request->date))],
                ['deal_name',$request->deal_name],
                ['cust_id',$request->cust_id],
                ['source',$request->source],
                ['owner',$request->owner],
                ['tags',$tags],
                ['created_by',Auth::user()->id],
                ['company_id',$request->company]
                ])->first();
            if($flag)
            {
                Toastr::success('Deal has been added successfully', 'Success');
                return redirect('crm-deals/show');
            }
            else{
            if($request->deal_value==""){$deal_value="0.00";}else{$deal_value = $request->deal_value;}

            $delivery_company = DB::table('sys_cust_suppl')->select('name')->where('id',$request->cust_id)->first();

            $scd = new SysCrmDeals();
            $scd->code = SysHelper::get_new_lead_deal_code('sys_crm_deals','code',$request->company);
            $scd->date = date('Y-m-d', strtotime($request->date));
            $scd->deal_name = $request->deal_name;
            $scd->cust_id = $request->cust_id;
            $scd->cust_name = $request->cust_name;
            $scd->cust_no = $request->cust_no;
            $scd->cust_email = $request->cust_email;
            $scd->deal_value = $deal_value;
            $scd->source = $request->source;
            $scd->source_o = $request->source_o;
            $scd->tags = $tags;
            $scd->stage = $request->stage;
            $scd->owner = $request->owner;
            $scd->doc = $doc_file;
            $scd->isproject = $request->isproject;
            $scd->designation = $request->designation;
            $scd->address = $request->address;

            $scd->delivery_company = $delivery_company->name;
            $scd->delivery_name = $request->cust_name;
            $scd->delivery_number = $request->cust_no;
            $scd->delivery_email = $request->cust_email;
            $scd->delivery_address = $request->address;

            $scd->note = $request->note;
            $scd->status = $request->status;
            $scd->estimated_close_date = date('Y-m-d', strtotime($request->estimated_close_date));
            $scd->created_by = Auth::user()->id;
            $scd->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
            //$scd->company_id = session('logged_session_data.company_id');
            $scd->company_id = $request->company;
            $scd->is_professional_service = $is_professional_service;
            $scd->save();
            $scd->toArray();

            SysHelper::set_user_custsupp($request->owner,$request->cust_id);

           $results=0;
           DB::commit();

        if ($results==0) {

            if($request->source == "Gitex"){
                if($request->cust_email != ""){
                    SysHelper::notificationMailGitexMail($request->cust_name,$request->cust_email,$request->owner);
                }
            }

            if($request->btnSubmit==2){
                Toastr::success('Deal has been added successfully. Please Create Quote', 'Success');
                return redirect('crm-quote/'.$scd->id.'/create');
            } else { 
                Toastr::success('Deal has been added successfully', 'Success');
                return redirect('crm-deals/'.$scd->id.'/view'); }

        } else {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
        }
       } catch (\Exception $e) {
           return $e;
           DB::rollback();
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back();
       }       
    }

    /*public function showList()
    {
        try {      
            $deals = session('deal_list_query.deals');
            $vendors = session('deal_list_query.vendors');
            $staff = session('deal_list_query.staff');
            $ctrl_cust_id = session('deal_list_query.ctrl_cust_id');
            $ctrl_stage = session('deal_list_query.ctrl_stage');
            $ctrl_source = session('deal_list_query.ctrl_source');
            $ctrl_owner = session('deal_list_query.ctrl_owner');
            $ctrl_date = session('deal_list_query.ctrl_date');
            $ctrl_date2 = session('deal_list_query.ctrl_date2');
            $ctrl_deal_id = session('deal_list_query.ctrl_deal_id');
            $ctrl_isproject = session('deal_list_query.ctrl_isproject');
            $brand = session('deal_list_query.brand');
            $ctrl_brand = session('deal_list_query.ctrl_brand');
            $filter_by = session('deal_list_query.filter_by');
            $country = session('deal_list_query.country');            

            return view('backEnd.crm.DealList', compact('deals','vendors','staff','ctrl_cust_id','ctrl_stage','ctrl_source','ctrl_owner','ctrl_date','ctrl_date2','ctrl_deal_id','ctrl_isproject','brand','ctrl_brand','filter_by','country'));

        } catch (\Throwable $th) {
            return $th;
        }
    }*/

    public function show(Request $request, $id = null) //kunal modified
    {
        try {

            // if(Auth::user()->id == 1) {
            //     $d = SysCrmDeals::select('id')->get();
            //     foreach ($d as $d1) {
            //         SysHelper::set_deal_profit($d1->id);
            //     }
            // }

            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];

            $collaboration = SysCrmDealsCollaboration::select('deal_id')->where('user_id', Auth::user()->id)->get();
            if (count($collaboration) > 0) {
                foreach ($collaboration as $collab) {
                    $coll[] = $collab->deal_id;
                }
            }
            $company = SysCompany::orderby('sort_id', 'asc');
            if (Auth::user()->role_id != 1) {
                $companyAccess = SysHelper::get_company_access();
                if (is_array($companyAccess) && count($companyAccess) > 0) {
                    $company->whereIn('id', $companyAccess);
                } else {
                    $company->whereRaw('0 = 1');
                }
            }
            $company = $company->get();
            $product = SysHelper::get_product_list($company_id);
            $designation = SmDesignation::select('title')->where('active_status', 1)->get();
            $country = SysCountries::select('id', 'name')->get();

            $com_id = session('logged_session_data.company_id');
            if ($com_id == 1) {
                $staff = SmStaff::where('active_status', 1)->orderby('first_name', 'asc')->get();
            } else {
                $staff = SmStaff::select('user_id', 'full_name')->whereRaw("find_in_set($com_id,company_access)")->get();
            }

            $rolearray = [1, 28, 27, 10, 3, 2, 4, 29, 26, 9, 30, 8, 32];
            if (in_array(Auth::user()->role_id, $rolearray)) {
                $sales_person = SmStaff::select('user_id', 'full_name')->where('active_status', 1)->orderby('full_name', 'asc')->get();
                $vendors = SysHelper::get_customer_list_deal_lead_all_role();
            } else {
                $vendors = SysHelper::get_customer_list_deal_lead();
                $sales_person = SmStaff::select('user_id', 'full_name')->where('user_id', Auth::user()->id)->orderby('full_name', 'asc')->get();
            }

            $brand = SysBrand::select('id', 'title')->orderby('title', 'asc')->get();



            $paymentterms = SysPaymentTerms::where('active_status', '=', '1')->get();

            $ctrl_deal_id = '';
            $ctrl_cust_id = '';
            $ctrl_stage = '';
            $ctrl_source = '';
            $ctrl_owner = '';
            $ctrl_date = '';
            $ctrl_date2 = '';
            $ctrl_isproject = '';
            $ctrl_brand = '';
            $filter_by = '';
            $ctrl_followup = '';
            $ctrl_sort_id = '';

            //if($_POST){
            if (SysHelper::get_pagination_post($request)) {
                $ctrl_sort_id = $request->sort_id;
                $query = SysCrmDeals::select('id', 'code', 'deal_name', 'estimated_close_date', 'date', 'stage', 'deal_currency', 'company_id', 'deal_value', 'deal_profit', 'created_at', 'updated_at', 'cust_id', 'owner', 'deleted_at', 'quote_id', 'followup_date')->where('stage', '!=', 0);

                if ($request->deal_id != "") {
                    $query->where('code', $request->deal_id);
                    $ctrl_deal_id = $request->deal_id;
                }
                if ($request->company_id != "") {
                    $query->where('cust_id', $request->company_id);
                    $ctrl_cust_id = $request->company_id;
                }
                if ($request->stage_id != "") {
                    if ($request->stage_id == 6) {
                        $did = SysCrmDealTrack::select('deal_id')->where([['accounts', '=', 1], ['sales', '=', 1], ['purchease', '=', 1], ['invoice', '=', 1], ['delivery', '=', 1], ['receivables', '=', 1]])->get();
                        if (count($did) > 0) {
                            foreach ($did as $d) {
                                $dd[] = $d->deal_id;
                            }
                        }
                        $query->wherein('id', $dd);
                    } else if ($request->stage_id == 7) {
                        $did = SysCrmDealTrack::select('deal_id')->where('accounts', '!=', 1)->orwhere('sales', '!=', 1)->orwhere('purchease', '!=', 1)->orwhere('invoice', '!=', 1)->orwhere('delivery', '!=', 1)->orwhere('receivables', '!=', 1)->get();
                        if (count($did) > 0) {
                            foreach ($did as $d) {
                                $dd[] = $d->deal_id;
                            }
                        }
                        $query->wherein('id', $dd);
                    } else {
                        $query->where('stage', $request->stage_id);
                    }
                    $ctrl_stage = $request->stage_id;
                }
                if ($request->isproject_id != "") {
                    $query->where('isproject', $request->isproject_id);
                    $ctrl_isproject = $request->isproject_id;
                }
                if ($request->source_id != "") {
                    $query->where('source', $request->source_id);
                    $ctrl_source = $request->source_id;
                }
                if ($request->owner_id != "") {
                    $query->where('owner', $request->owner_id);
                    $ctrl_owner = $request->owner_id;
                }

                // If a filter-by preset is selected, give it priority over manual date inputs.
                if ($request->sort_id == "" || $request->sort_id == null) {
                    if ($request->date != "") {
                        $ctrl_date = SysHelper::normalizeToYmd($request->date);
                        if ($request->date2 != "") {
                            $ctrl_date2 = SysHelper::normalizeToYmd($request->date2);
                            $query->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') >= '" . $ctrl_date . "' and DATE_FORMAT(created_at, '%Y-%m-%d') <= '" . $ctrl_date2 . "'");
                        } else {
                            $query->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') >= '" . $ctrl_date . "'");
                        }
                    }
                }

                if ($request->followup != "") {
                    $followup = Carbon::createFromFormat('d/m/Y', $request->followup);
                    //    $query->where('followup_date', '=', $followup->format('Y-m-d'));
                    $query->whereRaw("DATE_FORMAT(followup_date, '%Y-%m-%d') = '" . $followup->format('Y-m-d') . "'");

                    $query->orderby("followup_date", "ASC");

                    $ctrl_followup = $request->followup;
                }

                if ($request->brand_id != "") {
                    $brnd = SysCrmQuoteItems::select('deal_id')->join('sm_items', 'sm_items.id', 'sys_crm_quote_items.product_id')->where('sm_items.brand', $request->brand_id)->distinct()->get();

                    if (count($brnd) > 0) {
                        foreach ($brnd as $br) {
                            $b_ids[] = $br->deal_id;
                        }
                        $query->wherein('id', $b_ids);
                    } else {
                        $query->wherein('id', [0]);
                    }
                    $ctrl_brand = $request->brand_id;
                }

                if ($request->sort_id == 1) { //Today
                    $query->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') = '" . date('Y-m-d') . "'");
                    $filter_by = ": Today";
                    $ctrl_date = date('Y-m-d');
                    $ctrl_date2 = date('Y-m-d');
                }
                if ($request->sort_id == 2) { //This Week
                    $start_date = date('Y-m-d', strtotime('-1 week sunday 00:00:00'));
                    $end_date = date('Y-m-d', strtotime('saturday 23:59:59'));
                    $query->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') >= '" . $start_date . "' and DATE_FORMAT(created_at, '%Y-%m-%d') <= '" . $end_date . "'");
                    $filter_by = ": This Week";
                    $ctrl_date = $start_date;
                    $ctrl_date2 = $end_date;
                }
                if ($request->sort_id == 3) { //Last Week
                    $start_date = date('Y-m-d', strtotime('-2 week sunday 00:00:00'));
                    $end_date = date('Y-m-d', strtotime('-1 week saturday 23:59:59'));
                    $query->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') >= '" . $start_date . "' and DATE_FORMAT(created_at, '%Y-%m-%d') <= '" . $end_date . "'");
                    $filter_by = ": Last Week";
                    $ctrl_date = $start_date;
                    $ctrl_date2 = $end_date;
                }
                if ($request->sort_id == 4) { //This Month
                    $query->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '" . date('Y-m') . "'");
                    $filter_by = ": This Month";
                    $ctrl_date = date('Y-m') . '-01';
                    $ctrl_date2 = date('Y-m') . '-' . date('t');
                }
                if ($request->sort_id == 5) { //Last Month
                    $start_date = date('Y-m-d', strtotime('first day of previous month'));
                    $end_date = date('Y-m-d', strtotime('last day of previous month'));
                    $query->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') >= '" . $start_date . "' and DATE_FORMAT(created_at, '%Y-%m-%d') <= '" . $end_date . "'");
                    $filter_by = ": Last Month";
                    $ctrl_date = $start_date;
                    $ctrl_date2 = $end_date;

                }
                if ($request->sort_id == 6) { //Last 6 Month
                    $start_date = date('Y-m-d', strtotime('first day of this month - 6 months'));
                    $end_date = date('Y-m-d', strtotime('last day of this month'));
                    $query->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') >= '" . $start_date . "' and DATE_FORMAT(created_at, '%Y-%m-%d') <= '" . $end_date . "'");
                    $filter_by = ": Last 6 Month";
                    $ctrl_date = $start_date;
                    $ctrl_date2 = $end_date;
                }
                if ($request->sort_id == 7) { //This Year
                    $query->whereRaw("DATE_FORMAT(created_at, '%Y') = '" . date('Y') . "'");
                    $filter_by = ": This Year";
                    $ctrl_date = date('Y') . '-01-01';
                    $ctrl_date2 = date('Y') . '-12-31';
                }
                if ($request->sort_id == 8) { //Last Year
                    $query->whereRaw("DATE_FORMAT(created_at, '%Y') = '" . date("Y", strtotime("-1 year")) . "'");
                    $filter_by = ": Last Year";
                    $ctrl_date = date("Y", strtotime("-1 year")) . '-01-01';
                    $ctrl_date2 = date("Y", strtotime("-1 year")) . '-12-31';
                }



                if (Auth::user()->role_id != 1 && Auth::user()->role_id != 2 && Auth::user()->role_id != 32 && Auth::user()->role_id != 35 && Auth::user()->role_id != 35) {
                    $query->where('owner', Auth::user()->id);
                    if (count($collaboration) > 0) {
                        $query->orwherein('id', $coll);
                    }
                }



                // if(Auth::user()->role_id == 1)
                // {
                //     session('logged_session_data.company_id') = $request->main_filter_company;
                //     if(isset(session('logged_session_data.company_id'))){
                //         if(session('logged_session_data.company_id')!=0){
                //         $query->where("company_id",session('logged_session_data.company_id'));}
                //     }
                // }

                // if(Auth::user()->role_id == 35){
                //     $query->wherein('created_by',[17,24]);
                // }

                if ($request->sort_id == 9) { //By Deal Value
                    $query->wherein('stage', [1, 2, 3]);
                    if (session('logged_session_data.company_id') != 1) {
                        $query->wherein('company_id', $company_id);
                    }
                    $deals = $query->orderByRaw('CAST(deal_value AS UNSIGNED) DESC')->paginate(50);
                    $filter_by = ": By Deal Value";
                } else if ($request->sort_id == 10) { //By Date
                    $query->wherein('stage', [1, 2, 3]);
                    if (session('logged_session_data.company_id') != 1) {
                        $query->wherein('company_id', $company_id);
                    }
                    $deals = $query->orderby('updated_at', 'desc')->paginate(50);
                    $filter_by = ": By Date";
                } else if ($request->sort_id == 11) { //By latest
                    $query->wherein('stage', [1, 2, 3]);
                    if (session('logged_session_data.company_id') != 1) {
                        $query->wherein('company_id', $company_id);
                    }
                    $deals = $query->orderby('updated_at', 'desc')->paginate(50);
                    $filter_by = "Latest";
                } else if ($request->sort_id == 12) { //By expired
                    $query->wherein('stage', [1, 2, 3]);
                    if (session('logged_session_data.company_id') != 1) {
                        $query->wherein('company_id', $company_id);
                    }
                    $deals = $query->orderby('estimated_close_date', 'asc')->orderby('stage', 'asc')->get();
                    $filter_by = "Expired";
                } else {
                    if (session('logged_session_data.company_id') != 1) {
                        $query->wherein('company_id', $company_id);
                    }
                    $deals = $query->orderby('id', 'desc')->paginate(500);
                    //$deals = $query->orderby('estimated_close_date','asc')->orderby('stage','asc')->get();
                }
            } else {
                $query = SysCrmDeals::select('id', 'code', 'deal_name', 'estimated_close_date', 'date', 'stage', 'deal_currency', 'company_id', 'deal_value', 'deal_profit', 'created_at', 'updated_at', 'cust_id', 'owner', 'deleted_at', 'quote_id', 'followup_date')->where('stage', '!=', 0);


                if (Auth::user()->role_id != 1 && Auth::user()->role_id != 2 && Auth::user()->role_id != 32 && Auth::user()->role_id != 35) {
                    $query->where('owner', Auth::user()->id);
                    if (count($collaboration) > 0) {
                        $query->orwherein('id', $coll);
                    }
                }


                // if(Auth::user()->role_id == 1)
                // {
                //     if(isset(session('logged_session_data.company_id'))){
                //         if(session('logged_session_data.company_id')!=0){
                //         $query->where("company_id",session('logged_session_data.company_id'));}
                //     }
                // }
                //This Year
                // $query->whereRaw("DATE_FORMAT(created_at, '%Y') = '" . date('Y') . "'");
                $filter_by = ": This Year";

                if (session('logged_session_data.company_id') != 1) {
                    $query->wherein('company_id', $company_id);
                }

                // if(Auth::user()->role_id == 35){
                //     $query->wherein('created_by',[17,24]);
                // }



                if ($_POST) {
                    $deals = $query->orderby('id', 'desc')->paginate(500000);
                } else {
                    $deals = $query->orderby('id', 'desc')->paginate(200);
                }


                //$deals = $query->orderby('estimated_close_date','asc')->orderby('stage','asc')->get();
            }


            $active_id = $id;
            $selectedDeal = [];


            $action = false;
            $editData = [];
            $addData = [];



            // dd($request->all());


            if ($request->has('deal_action')) {
                $poAction = $request->input('deal_action');


                if ($poAction === 'add') {
                    $action = 'add';
                    $addData = $this->add(); // Get all data for adding
                } elseif ($poAction === 'edit') {
                    $action = 'edit';

                    if ($request->has('quote')) {
                        $editData = $this->edit($active_id, $request->quote);
                    } else {
                        $editData = $this->edit($active_id);
                    }
                }
            } else {
                if ($id != "show" && $id != null) {
                    $selectedDeal = $this->get_deal_pdf_data($id);
                } else {
                    $firstRecord = $deals->first();

                    if ($firstRecord) {
                        $active_id = $firstRecord->id;
                        $selectedDeal = $this->get_deal_pdf_data($firstRecord->id);
                    }
                }
            }


            // $pdfdata = [];
            // if ($id == null || $id == 'show') {
            //     if ($deals->first() != null) {
            //         $id = $deals->first()->id;
            //         $pdfdata = $this->get_deal_pdf_data($id);
            //     }
            // } else {
            //     $pdfdata = $this->get_deal_pdf_data($id);
            // }



            // $active_id =  $id;



            //return $pdfdata;


            return view('backEnd.crm.DealList', compact('deals', 'vendors', 'staff', 'ctrl_cust_id', 'ctrl_stage', 'ctrl_source', 'ctrl_owner', 'ctrl_date', 'ctrl_date2', 'ctrl_deal_id', 'ctrl_isproject', 'brand', 'ctrl_brand', 'filter_by', 'product', 'designation', 'country', 'company', 'paymentterms', 'sales_person', 'active_id', 'ctrl_followup', 'selectedDeal', 'action', 'editData', 'addData'));

            /*$form_data = [
            'deals' => $deals,
            'vendors' => $vendors,
            'staff' => $staff,
            'ctrl_cust_id' => $ctrl_cust_id,
            'ctrl_stage' => $ctrl_stage,
            'ctrl_source' => $ctrl_source,
            'ctrl_owner' => $ctrl_owner,
            'ctrl_date' => $ctrl_date,
            'ctrl_date2' => $ctrl_date2,
            'ctrl_deal_id' => $ctrl_deal_id,
            'ctrl_isproject' => $ctrl_isproject,
            'brand' => $brand,
            'ctrl_brand' => $ctrl_brand,
            'filter_by' => $filter_by,
            'country' => $country,
        ];
        session()->put('deal_list_query', $form_data);
        return redirect('crm-deals-showlist');*/
            //return view('backEnd.crm.DealList', compact('deals','vendors','staff','ctrl_cust_id','ctrl_stage','ctrl_source','ctrl_owner','ctrl_date','ctrl_date2','ctrl_deal_id','ctrl_isproject','brand','ctrl_brand','filter_by','country));

        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function search(Request $request)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $query = $request->get('query');

            $invoices = DB::table('sys_crm_deals as d')->select('d.id', 'd.code', 'd.deal_name', 'd.date', 'd.deal_currency', 'd.company_id', 'd.deal_value', 'd.deal_profit', 'd.quote_id', 'sys_currency.code as currency_code', 'sys_cust_suppl.code as account_code', 'sys_cust_suppl.name as account_name')->where('d.stage', '!=', 0)
                ->join('sys_currency', 'sys_currency.id', '=', 'd.deal_currency')
                ->join('sys_cust_suppl', 'sys_cust_suppl.id', '=', 'd.cust_id')

                ->when(session('logged_session_data.company_id') != 1, function ($q) use ($company_id) {
                    $q->where('d.company_id', $company_id);
                })
                ->where(function ($q) use ($query) {
                    $q->where('d.code', 'LIKE', "%{$query}%")
                        ->orWhere('d.deal_name', 'LIKE', "%{$query}%")
                        ->orWhere('sys_cust_suppl.name', 'LIKE', "%{$query}%")
                        ->orWhere('sys_cust_suppl.code', 'LIKE', "%{$query}%");
                })
                ->orderBy('d.id', 'desc')
                ->limit(30)
                ->get();
            return response()->json($invoices);
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function getDetails($id)
    {
        $data = $this->get_deal_pdf_data($id);
        if (count($data) > 0) {
            return view('backEnd.crm.DealList_details', $data);
        } else {
            return "error!!";
        }
    }
    public function get_deal_pdf_data($id)
    {
        try {
            $quotation = SysCrmDeals::where('id', $id)->first();
            $company = SysCompany::find($quotation->company_id);




            $qid = $quotation->quote_id;
            $quotationitems = SysCrmQuoteItems::where('deal_id', $id)->where('quote_id', $qid)->orderby('sort_id', 'ASC')->get();


            if (count($quotationitems) > 0) {
                $currency_modal = SysCurrency::find($quotationitems[0]->currency_id);
                $currency = optional($quotationitems[0]->currency)->code;
                $paymentterms = optional($quotationitems[0]->paymentterms)->title;
                if (strtolower(optional($quotationitems[0]->paymentterms)->title) == "other") {
                    $paymentterms = $quotationitems[0]->payment_terms_txt;
                }
                $deliverydate = $quotationitems[0]->delivery_date;
                $deliverytime = $quotationitems[0]->delivery_time;

                $pdfheader = optional($quotationitems[0]->company)->pdf_header;
                $pdffooter = optional($quotationitems[0]->company)->pdf_footer;
                $pdfwatermark = optional($quotationitems[0]->company)->pdf_watermark;
                $pdffirstpage = optional($quotationitems[0]->company)->pdf_first_page;
                /*if($quotationitems[0]->currency_id==1){ $net_vat=5; }
                else if($quotationitems[0]->currency_id==2){ $net_vat=5; }
                else if($quotationitems[0]->currency_id==3){ $net_vat=5; }
                else if($quotationitems[0]->currency_id==4){ $net_vat=15; }
                else if($quotationitems[0]->currency_id==5){ $net_vat=5; }
                else if($quotationitems[0]->currency_id==6){ $net_vat=18; }
                else if($quotationitems[0]->currency_id==7){ $net_vat=5; }
                else if($quotationitems[0]->currency_id==8){ $net_vat=5; }
                else if($quotationitems[0]->currency_id==9){ $net_vat=20; }*/
                $net_vat = $quotationitems[0]->vat;
            } else {
                $currency_modal = "";
                $currency = "";
                $paymentterms = "";
                $deliverydate = "";
                $deliverytime = "";
                $pdfheader = "syscom-pdf-header.jpg";
                $pdffooter = "syscom-pdf-footer.jpg";
                $pdfwatermark = "syscom-watermark-sm.png";
                $pdffirstpage = "syscom-pdf-first-page.jpg";
                $net_vat = 5;
            }


            $wp = 0;
            $wt = 0;
            $wv = 0;

            $enduser = SysCrmEndUser::where('deal_id', $id)->first();

            $support = SysCrmSupport::where('deal_id', $id)->get();
            $edit = SysCrmDeals::where('id', $id)->first();
            $collaboration = SysCrmDealsCollaboration::where('deal_id', $id)->get();
            $service = SysCrmService::where('deal_id', $id)->get();

            // $net_vat = $quotationitems[0]->company->net_vat;
            // if($net_vat==""){$net_vat=5;}

            //return $quotation; 


            $data = [
                'quotation' => $quotation,
                'quotationitems' => $quotationitems,
                'currency' => $currency,
                'currency_modal' => $currency_modal,
                'company' => $company,
                'paymentterms' => $paymentterms,
                'deliverydate' => $deliverydate,
                'deliverytime' => $deliverytime,
                'wp' => $wp,
                'wt' => $wt,
                'wv' => $wv,
                'pdfheader' => $pdfheader,
                'pdffooter' => $pdffooter,
                'pdfwatermark' => $pdfwatermark,
                'pdffirstpage' => $pdffirstpage,
                'net_vat' => $net_vat,
                'enduser' => $enduser,
                'support' => $support,
                'edit' => $edit,
                'service' => $service,
                'collaboration' => $collaboration,
            ];
            return $data;
        } catch (\Throwable $th) {
            dd($th);
            return [];
        }
    }

    /*

    public function show(Request $request)
    {
        try {

            // if(Auth::user()->id == 1) {
            //     $d = SysCrmDeals::select('id')->get();
            //     foreach ($d as $d1) {
            //         SysHelper::set_deal_profit($d1->id);
            //     }
            // }

            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];

        $collaboration = SysCrmDealsCollaboration::select('deal_id')->where('user_id',Auth::user()->id)->get();
        if(count($collaboration)>0){
            foreach($collaboration as $collab)
            {
                $coll[]=$collab->deal_id;
            }
        }
        $company = SysCompany::orderby('sort_id','asc')->get();
        $product = SysHelper::get_product_list($company_id);
        $designation = SmDesignation::select('title')->where('active_status',1)->get();
        $country = SysCountries::select('id','name')->get();
        $staff      = SysHelper::get_sales_persons4();

        $rolearray=[1,28,27,10,3,2,4,29,26,9,30,8,32];
        if(in_array(Auth::user()->role_id, $rolearray)) {
            $sales_person      = SmStaff::select('user_id','full_name')->where('active_status',1)->orderby('full_name','asc')->get();
            $vendors = SysHelper::get_customer_list_deal_lead_all_role();
        } else{
            $vendors = SysHelper::get_customer_list_deal_lead();
            $sales_person      = SmStaff::select('user_id','full_name')->where('user_id',Auth::user()->id)->orderby('full_name','asc')->get();
        }

        $brand      = SysBrand::select('id','title')->orderby('title','asc')->get();



        $paymentterms = SysPaymentTerms::where('active_status', '=', '1')->get();

$ctrl_deal_id='';
$ctrl_cust_id='';
$ctrl_stage='';
$ctrl_source='';
$ctrl_owner='';
$ctrl_date='';
$ctrl_date2='';
$ctrl_isproject='';
$ctrl_brand='';
$filter_by='';

        //if($_POST){
        if(SysHelper::get_pagination_post($request)){
            $query = SysCrmDeals::select('id','deal_name','estimated_close_date','date','stage','deal_currency','company_id','deal_value','deal_profit','created_at','updated_at','cust_id','owner')->where('stage','!=',0);
            if ($request->deal_id != "") {
                $query->where('code', $request->deal_id);
                $ctrl_deal_id=$request->deal_id;
            }
            if ($request->company_id != "") {
                $query->where('cust_id', $request->company_id);
                $ctrl_cust_id=$request->company_id;
            }
            if ($request->stage_id != "") {
                if($request->stage_id==6){
                    $did = SysCrmDealTrack::select('deal_id')->where([['accounts','=',1],['sales','=',1],['purchease','=',1],['invoice','=',1],['delivery','=',1],['receivables','=',1]])->get();
                    if(count($did)>0){
                        foreach ($did as $d) {
                            $dd[]=$d->deal_id;
                        }
                    }
                    $query->wherein('id', $dd);
                }
                else if($request->stage_id==7){
                    $did = SysCrmDealTrack::select('deal_id')->where('accounts','!=',1)->orwhere('sales','!=',1)->orwhere('purchease','!=',1)->orwhere('invoice','!=',1)->orwhere('delivery','!=',1)->orwhere('receivables','!=',1)->get();
                    if(count($did)>0){
                        foreach ($did as $d) {
                            $dd[]=$d->deal_id;
                        }
                    }
                    $query->wherein('id', $dd);
                }
                else{
                    $query->where('stage', $request->stage_id);
                }
                $ctrl_stage=$request->stage_id;
            }
            if ($request->isproject_id != "") {
                $query->where('isproject', $request->isproject_id);
                $ctrl_isproject=$request->isproject_id;
            }
            if ($request->source_id != "") {
                $query->where('source', $request->source_id);
                $ctrl_source=$request->source_id;
            }
            if ($request->owner_id != "") {
                $query->where('owner', $request->owner_id);
                $ctrl_owner=$request->owner_id;
            }
            if ($request->date != "") {
                $ctrl_date=$request->date;
                if ($request->date2 != "") {
                    $query->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') >= '".date('Y-m-d', strtotime($request->date))."' and DATE_FORMAT(created_at, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($request->date2))."'");
                    $ctrl_date2=$request->date2;
                }
                else{
                    $query->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') = '".date('Y-m-d', strtotime($request->date))."'");
                }
            }
            if ($request->brand_id != "") {
                $brnd= SysCrmQuoteItems::select('deal_id')->join('sm_items','sm_items.id','sys_crm_quote_items.product_id')->where('sm_items.brand',$request->brand_id)->distinct()->get();

                if(count($brnd)>0){
                    foreach($brnd as $br)
                    {
                        $b_ids[]=$br->deal_id;
                    }
                    $query->wherein('id', $b_ids);
                }
                else{
                    $query->wherein('id', [0]);
                }
                $ctrl_brand=$request->brand_id;
            }

            if($request->sort_id==1){//Today
                $query->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') = '".date('Y-m-d')."'");
                $filter_by=": Today";
            }
            if($request->sort_id==2){//This Week
                $start_date = date('Y-m-d', strtotime('-1 week sunday 00:00:00'));
                $end_date = date('Y-m-d', strtotime('saturday 23:59:59'));
                $query->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(created_at, '%Y-%m-%d') <= '".$end_date."'");
                $filter_by=": This Week";
            }
            if($request->sort_id==3){//Last Week
                $start_date = date('Y-m-d', strtotime('-2 week sunday 00:00:00'));
                $end_date = date('Y-m-d', strtotime('-1 week saturday 23:59:59'));
                $query->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(created_at, '%Y-%m-%d') <= '".$end_date."'");
                $filter_by=": Last Week";
            }
            if($request->sort_id==4){//This Month
                $query->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '".date('Y-m')."'");
                $filter_by=": This Month";
            }
            if($request->sort_id==5){//Last Month
                $start_date = date('Y-m-d', strtotime('first day of previous month'));
                $end_date = date('Y-m-d', strtotime('last day of previous month'));
                $query->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(created_at, '%Y-%m-%d') <= '".$end_date."'");
                $filter_by=": Last Month";
            }
            if($request->sort_id==6){//Last 6 Month
                $start_date = date('Y-m-d', strtotime('first day of this month - 6 months'));
                $end_date = date('Y-m-d', strtotime('last day of this month'));
                $query->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(created_at, '%Y-%m-%d') <= '".$end_date."'");
                $filter_by=": Last 6 Month";
            }
            if($request->sort_id==7){//This Year
                $query->whereRaw("DATE_FORMAT(created_at, '%Y') = '".date('Y')."'");
                $filter_by=": This Year";
            }
            if($request->sort_id==8){//Last Year
                $query->whereRaw("DATE_FORMAT(created_at, '%Y') = '".date("Y",strtotime("-1 year"))."'");
                $filter_by=": Last Year";
            }

            if(Auth::user()->role_id != 1 && Auth::user()->role_id != 2 && Auth::user()->role_id != 32 && Auth::user()->role_id != 35)
            {
                $query->where('owner', Auth::user()->id);
                if(count($collaboration)>0){ $query->orwherein('id',$coll); }                
            }



            // if(Auth::user()->role_id == 1)
            // {
            //     session('logged_session_data.company_id') = $request->main_filter_company;
            //     if(isset(session('logged_session_data.company_id'))){
            //         if(session('logged_session_data.company_id')!=0){
            //         $query->where("company_id",session('logged_session_data.company_id'));}
            //     }
            // }

            if(Auth::user()->role_id == 35){
                $query->wherein('created_by',[17,24]);
            }

            if($request->sort_id==9){//By Deal Value
                $query->wherein('stage', [1,2,3]); 
                if(session('logged_session_data.company_id') != 1){ $query->wherein('company_id',$company_id); }
                $deals = $query->orderByRaw('CAST(deal_value AS UNSIGNED) DESC')->paginate(50);
                $filter_by=": By Deal Value";
            }
            else if($request->sort_id==10){//By Date
                $query->wherein('stage', [1,2,3]); 
                if(session('logged_session_data.company_id') != 1){ $query->wherein('company_id',$company_id); }
                $deals = $query->orderby('updated_at','desc')->paginate(50);
                $filter_by=": By Date";
            }
            else if($request->sort_id==11){//By latest
                $query->wherein('stage', [1,2,3]); 
                if(session('logged_session_data.company_id') != 1){ $query->wherein('company_id',$company_id); }
                $deals = $query->orderby('updated_at','desc')->paginate(50);
                $filter_by="Latest";
            }
            else if($request->sort_id==12){//By expired
                $query->wherein('stage', [1,2,3]); 
                if(session('logged_session_data.company_id') != 1){ $query->wherein('company_id',$company_id); }
                $deals = $query->orderby('estimated_close_date','asc')->orderby('stage','asc')->get();
                $filter_by="Expired";
            }
            else{
                if(session('logged_session_data.company_id') != 1){ $query->wherein('company_id',$company_id); }
                $deals = $query->orderby('updated_at','desc')->paginate(50);
                //$deals = $query->orderby('estimated_close_date','asc')->orderby('stage','asc')->get();
            }


        }
        else{
            $query = SysCrmDeals::select('id','deal_name','estimated_close_date','date','stage','deal_currency','company_id','deal_value','deal_profit','created_at','updated_at','cust_id','owner')->where('stage','!=',0);            

            if (Auth::user()->role_id != 1 && Auth::user()->role_id != 2 && Auth::user()->role_id != 32 && Auth::user()->role_id != 35) {
                $query->where('owner', Auth::user()->id);
                if(count($collaboration)>0){ $query->orwherein('id',$coll); }    
            }


            // if(Auth::user()->role_id == 1)
            // {
            //     if(isset(session('logged_session_data.company_id'))){
            //         if(session('logged_session_data.company_id')!=0){
            //         $query->where("company_id",session('logged_session_data.company_id'));}
            //     }
            // }
            //This Year
            $query->whereRaw("DATE_FORMAT(created_at, '%Y') = '".date('Y')."'");
            $filter_by=": This Year";

            if(session('logged_session_data.company_id') != 1){ $query->wherein('company_id',$company_id); }

            if(Auth::user()->role_id == 35){
                $query->wherein('created_by',[17,24]);
            }

            if($_POST){
                $deals = $query->orderby('updated_at','desc')->paginate(500000);
            } else {
                $deals = $query->orderby('updated_at','desc')->paginate(100);
            }
            //$deals = $query->orderby('estimated_close_date','asc')->orderby('stage','asc')->get();
        }

        return view('backEnd.crm.DealList', compact('deals','vendors','staff','ctrl_cust_id','ctrl_stage','ctrl_source','ctrl_owner','ctrl_date','ctrl_date2','ctrl_deal_id','ctrl_isproject','brand','ctrl_brand','filter_by','product','designation','country','company','paymentterms','sales_person'));

        /*$form_data = [
            'deals' => $deals,
            'vendors' => $vendors,
            'staff' => $staff,
            'ctrl_cust_id' => $ctrl_cust_id,
            'ctrl_stage' => $ctrl_stage,
            'ctrl_source' => $ctrl_source,
            'ctrl_owner' => $ctrl_owner,
            'ctrl_date' => $ctrl_date,
            'ctrl_date2' => $ctrl_date2,
            'ctrl_deal_id' => $ctrl_deal_id,
            'ctrl_isproject' => $ctrl_isproject,
            'brand' => $brand,
            'ctrl_brand' => $ctrl_brand,
            'filter_by' => $filter_by,
            'country' => $country,
        ];
        session()->put('deal_list_query', $form_data);
        return redirect('crm-deals-showlist');*/
    //return view('backEnd.crm.DealList', compact('deals','vendors','staff','ctrl_cust_id','ctrl_stage','ctrl_source','ctrl_owner','ctrl_date','ctrl_date2','ctrl_deal_id','ctrl_isproject','brand','ctrl_brand','filter_by','country));

    /*} catch (\Throwable $th) {
        return $th;
    }
    }
    */
    public function edit($id, $qid = 0)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];

            $edit = SysCrmDeals::where('id', $id)->first();



            if ($qid == 0) {
                $quote_id = $edit->quote_id;
            } else {
                $quote_id = $qid;
            }
            $quotationitems = SysCrmQuoteItems::where('deal_id', $id)->where('quote_id', $quote_id)->orderby('sort_id', 'ASC')->get();



            $edit_cfc = SysCrmQuoteCharges::where('deal_id', $id)->where('quote_id', $quote_id)->get();

            // $query = SysCrmDeals::select('id', 'code', 'deal_name', 'estimated_close_date', 'date', 'stage', 'deal_currency', 'company_id', 'deal_value', 'deal_profit', 'created_at', 'updated_at', 'cust_id', 'owner', 'deleted_at', 'quote_id')->where('stage', '!=', 0);

            // if (session('logged_session_data.company_id') != 1) {
            //     $query->wherein('company_id', $company_id);
            // }

            // $deals = $query->orderby('id', 'desc')->paginate(200);


            $companylist = SysCompany::orderby('sort_id', 'asc');
            if (Auth::user()->role_id != 1) {
                $companyAccess = SysHelper::get_company_access();
                if (is_array($companyAccess) && count($companyAccess) > 0) {
                    $companylist->whereIn('id', $companyAccess);
                } else {
                    $companylist->whereRaw('0 = 1');
                }
            }
            $companylist = $companylist->get();
            $paymentterms = SysPaymentTerms::all();

            $currencylist = SysCurrencySettings::select('id', 'code', 'ex_rate')->where('status', 1)->orderBy('code', 'ASC')->get();
            $currencylist2 = DB::table('sys_currency_rate as r')->select('r.id', 'r.from_currency', 'r.to_currency', 'c.code', 'r.rate')
                ->join('sys_currency as c', 'c.id', 'r.to_currency')
                ->where('r.status', 1)->where('r.from_currency', $edit->deal_currency)
                ->orderBy('c.code', 'ASC')->get();

            $currency_id = $edit->deal_currency;

            $addressbook = SysCustSupplAddressbook::where('cust_suppl_id', $edit->id)->orderBy('id', 'desc')->first();

            $currency = SysCurrencySettings::select('id', 'code')->get();
            $product = SysHelper::get_product_list($company_id);
            $company = SysCompany::orderby('sort_id', 'asc');
            if (Auth::user()->role_id != 1) {
                $companyAccess = SysHelper::get_company_access();
                if (is_array($companyAccess) && count($companyAccess) > 0) {
                    $company->whereIn('id', $companyAccess);
                } else {
                    $company->whereRaw('0 = 1');
                }
            }
            $company = $company->get();

            // $staff_query = SmStaff::select('user_id','full_name');
            // if(Auth::user()->role_id != 1){
            //     if(Auth::user()->role_id == 3){ //Department Head
            //         $users = SmStaff::select('user_id')->where('department_id',session('logged_session_data.department_id'))->get();
            //         foreach ($users as $value) {
            //             $userid[]=$value->user_id;
            //         }
            //         $staff_query->wherein('user_id', $userid);
            //     }
            //     else{
            //         $staff_query->where('user_id', Auth::user()->id);
            //     }
            // }

            // $staff_query->wherein('company_id', $company_id);
            // $staff_query->where('active_status', 1);
            // $staff = $staff_query->get();

            $com_id = session('logged_session_data.company_id');
            // if(Auth::user()->role_id == 5){
            // $staff = SmStaff::select('sm_staffs.user_id as id','sm_staffs.full_name')
            // ->join('sys_cust_suppl_assign','sys_cust_suppl_assign.user_id','sm_staffs.id')
            // ->where('sys_cust_suppl_assign.cust_supp_id',$edit->cust_id)->groupby('sm_staffs.id','sm_staffs.full_name')->get();
            // }
            // else{
            //     $staff = SmStaff::select('user_id as id','full_name')->where('active_status', '=', '1')
            //     ->wherein('role_id',[1,2,5,8])
            //     ->whereRaw("find_in_set($com_id,company_access)")->orderby('full_name','asc')->get();
            // }
            $staff = SysHelper::get_sales_persons2();

            $rolearray = [1, 28, 27, 10, 3, 2, 4, 29, 26, 9, 30, 8, 32];
            if (in_array(Auth::user()->role_id, $rolearray)) {
                $sales_person = SysHelper::get_sales_persons();
                $vendors = SysHelper::get_customer_list_deal_lead_all_role();
            } else {
                $vendors = SysHelper::get_customer_list_deal_lead();
                $sales_person = SysHelper::get_sales_persons();
            }

            $brand = SysBrand::select('title')->orderby('title', 'asc')->get();
            $country = SysCountries::select('id', 'name')->get();

            if (session('logged_session_data.company_id') == 1) {
                $deal_company = "";
            } else {
                $deal_company = SysCompany::find(session('logged_session_data.company_id'))->company_name;
            }
            $comments = SysCrmDealsComments::with('createdby:id,user_id,first_name,last_name')->where('deal_id', $id)->orderBy('id', 'DESC')->get();
            $cust_supp = SysHelper::get_customer_list_deal_lead_all_role();
            $countries = SysCountries::all();
            $states = SysStates::all();

            $deal_track = SysCrmDealTrack::where('deal_id', $id)->orderby('id', 'desc')->first();
            $check_edit_fullfill = 0;
            if (isset($deal_track)) {
                if ($deal_track->invoice == 1) {
                    $check_invoice_approved = 1;
                }
                if ($deal_track->accounts == 1 && $deal_track->sales == 1) {
                    $check_edit_fullfill = 1;
                }
            }
            $is_amc_item = SysCrmQuoteItems::wherein('product_id', [35657])->where('deal_id', $id)->where('quote_id', $quote_id)->count();

            $to_date = Carbon::now()->format('Y-m-d');
            $customs_freight_account = SysHelper::get_customs_freight_accounts_for_sales($company_id);
            $supplier = SysHelper::get_supplier_list($company_id);
            $deal_track_temp = SysCrmDealTrackTemp::where('deal_id', $id)->orderby('id', 'desc')->first();
            $support = SysCrmSupport::where('deal_id', $id)->get();
            $enduser = SysCrmEndUser::where('deal_id', $id)->first();
            $items = SysHelper::get_product_list($company_id);




            $cart = SysCrmQuoteCartEdit::where(['cart_id' => session('logged_session_data.cart_id'), 'user_id' => Auth::user()->id, 'deal_id' => $id])->get();



            $designation = SmDesignation::select('title')->where('active_status', 1)->get();
            $country = SysCountries::select('id', 'name')->get();

            $customer_type = SysCustomerType::where('status', '=', '1')->get();



            $reserve_stock = ReserveStock::where('deal_id', $id)->whereNull('deleted_at')->get();










            return compact('currency', 'vendors', 'company', 'staff', 'brand', 'country', 'edit', 'product', 'company', 'deal_company', 'paymentterms', 'quotationitems', 'quote_id', 'comments', 'cust_supp', 'countries', 'states', 'check_edit_fullfill', 'is_amc_item', 'addressbook', 'currencylist', 'currencylist2', 'currency_id', 'customs_freight_account', 'supplier', 'edit_cfc', 'deal_track_temp', 'deal_track', 'support', 'enduser', 'items', 'company_id', 'cart', 'sales_person', 'designation', 'country', 'customer_type', 'reserve_stock');
            // return view('backEnd.crm.DealFormEdit', compact('currency', 'vendors', 'company', 'staff', 'brand', 'country', 'edit', 'product', 'company', 'deals', 'deal_company', 'paymentterms', 'quotationitems', 'quote_id', 'comments', 'cust_supp', 'countries', 'states', 'check_edit_fullfill', 'is_amc_item', 'addressbook', 'currencylist', 'currencylist2', 'currency_id', 'customs_freight_account', 'supplier', 'edit_cfc', 'deal_track_temp', 'deal_track', 'support', 'enduser', 'items', 'company_id', 'cart','sales_person','designation','country','customer_type'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function view($id) //kunal modified
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $queryra = "UPDATE sys_crm_deals SET company_id = (SELECT company_id FROM sm_staffs WHERE user_id=sys_crm_deals.owner ) WHERE company_id IS NULL";
            DB::select($queryra);

            $currency = SysCurrencySettings::select('id', 'code')->get();
            $company = SysCompany::find(session('logged_session_data.company_id'));
            $staff = SmStaff::select('user_id', 'full_name')->get();

            if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2) {
                $leads = SysCrmDeals::where('id', $id)->first();
                $edit = SysCrmDeals::where('id', $id)->first();
            } else {
                $leads = SysCrmDeals::where('id', $id)->where('company_id', session('logged_session_data.company_id'))->first();
                $edit = SysCrmDeals::where('id', $id)->where('company_id', session('logged_session_data.company_id'))->first();
            }

            //return $edit;
            if (!isset($edit)) {
                return redirect('crm-deals/show');
            }

            $sales_person = SmStaff::select('user_id', 'full_name')->where('active_status', 1)->wherein('user_id', [$edit->owner])->orderby('full_name', 'asc')->get();
            $support_person = SysHelper::get_engineer_list();

            // $vendors_query = SysCustSuppl::select('id','code','name')->where('catid',1); // 1 customers, 2 suppliers
            // if(Auth::user()->role_id != 1){
            //     $vendors_query->where('sales_person', Auth::user()->id);
            // }
            // $vendors = $vendors_query->get();

            //$product_list = SmItem::select('sm_items.id','sm_items.part_number','sm_items.description')->wherein('company_id',$company_id)->get();
            $product_list = SysHelper::get_product_list($company_id);

            $comments = SysCrmDealsComments::where('deal_id', $id)->orderBy('id', 'DESC')->get();
            $paymentterms = SysPaymentTerms::all();
            $collaboration = SysCrmDealsCollaboration::where('deal_id', $id)->get();
            $service = SysCrmService::where('deal_id', $id)->get();
            $enduser = SysCrmEndUser::where('deal_id', $id)->first();
            $support = SysCrmSupport::where('deal_id', $id)->get();
            if (count($support) > 0) {
                $support_work = DB::table('sys_crm_support_work')->where('support_id', $support[0]->id)->get();
            } else {
                $support_work = [];
            }

            $countries = SysCountries::all();
            $states = SysStates::all();


            if (count($service) > 0) {
                $servicecomments = SysCrmServiceComments::where('service_id', $service[0]->id)->get();
            } else {
                $servicecomments = [];
            }
            if (count($support) > 0) {
                $supportcomments = SysCrmSupportActivity::where('support_id', $support[0]->id)->get();
            } else {
                $supportcomments = [];
            }

            $quoteitems = SysCrmQuoteItems::select('sys_crm_quote_items.*', 'sm_items.part_number', 'sys_brand.title')
                ->leftjoin('sm_items', 'sm_items.id', 'sys_crm_quote_items.product_id')
                ->leftjoin('sys_brand', 'sys_brand.id', 'sm_items.brand')
                ->where('deal_id', $id)->where('quote_id', $leads->quote_id)->orderby('sort_id', 'ASC')->get();

            $quotecsitems = SysCrmQuoteCSItems::where('deal_id', $id)->get();

            $extra_charges = 0;
            $quote_charges = SysCrmQuoteCharges::where('deal_id', $id)->where('quote_id', $leads->quote_id)->get();
            if (count($quote_charges) > 0) {
                $extra_charges = $quote_charges->sum('amount');
            }
            //return $extra_charges;

            $purchase_auto = SysPurchaseAuto::where(['deal_id' => $id, 'status' => 2, 'req_cost' => 1])->pluck('po_id');
            $purchase_cost = 0;
            if (count($purchase_auto) > 0) {
                $purchase_cost = SysPurchaseOrderItems::select(DB::raw('sum(taxableamount) as total_cost'))->wherein('po_id', $purchase_auto)->get();
                $purchase_cost = $purchase_cost[0]->total_cost;
            }

            $deal_track = SysCrmDealTrack::where('deal_id', $id)->orderby('id', 'desc')->first();


            $net = 0;
            $vat = 0;
            $curr = 1;
            $delivery_date = '';
            $deal_profit = 0;
            $deal_cost = 0;
            $check_edit_fullfill = 0;

            $first_item_brand = 0;

            if (count($quoteitems) > 0) {
                $first_item_brand = $quoteitems[0]->title;
                foreach ($quoteitems as $itms) {
                    $qty = $itms->qty;
                    $price = $itms->price;
                    $discount = $itms->discount;
                    $vat = $itms->vat;
                    $net += (($price * $qty) + (($price * $qty) * $vat / 100)) - ($discount + ($discount * $vat / 100));
                    $curr = $itms->currency_id;
                    $delivery_date = $itms->delivery_date;
                    $deal_cost += ($itms->cost * $itms->qty);
                    $deal_profit += (($price * $qty) - ($discount)) - ($itms->cost * $itms->qty);
                }
                $deal_value = $net - ($leads->deal_discount + (($leads->deal_discount * $vat) / 100));
                //return $deal_value;
                $cost = $deal_cost + $extra_charges;

                $check_invoice_approved = 0;
                if (isset($deal_track)) {
                    if ($deal_track->invoice == 1) {
                        $check_invoice_approved = 1;
                    }
                    if ($deal_track->accounts == 1 && $deal_track->sales == 1) {
                        $check_edit_fullfill = 1;
                    }
                }

                DB::table('sys_crm_deals')->where('id', $id)
                    ->update([
                        'tags' => $first_item_brand,
                    ]);

                //&& $check_invoice_approved != 1
                // if($net > 0){
                //     DB::table('sys_crm_deals')->where('id',$id)
                //     ->update([
                //         'deal_value' => $deal_value,
                //         'deal_currency' => $curr,
                //         'deal_profit' => $deal_profit-$extra_charges-$purchase_cost,
                //     ]);
                // }
                //if($net > 0 && $check_invoice_approved == 1){                
                SysHelper::set_deal_profit($id);
                //}
            }



            $leads = SysCrmDeals::where('id', $id)->first();

            $companylist = SysCompany::select('id', 'company_name', 'city')->where('status', 1)->orderBy('company_name', 'ASC')->get();
            $company_query = SysCompany::select('id', 'company_name', 'city')->where('status', 1);
            if (Auth::user()->role_id != 1) {
                $company_query->where('id', session('logged_session_data.company_id'));
            }
            $companylist = $company_query->orderBy('company_name', 'ASC')->get();

            $currencylist = SysCurrencySettings::select('id', 'code')->where('status', 1)->orderBy('code', 'ASC')->get();

            $addressbook = SysCustSupplAddressbook::where('cust_suppl_id', $leads->cust_id)->orderBy('id', 'desc')->first();

            $deal_track_temp = SysCrmDealTrackTemp::where('deal_id', $id)->orderby('id', 'desc')->first();

            $cust_supp = SysHelper::get_customer_list_deal_lead_all_role();

            // AMC product id = 35657
            $is_amc_item = SysCrmQuoteItems::wherein('product_id', [35657])->where('deal_id', $id)->where('quote_id', $leads->quote_id)->count();



            $rolearray = [1, 28, 27, 10, 3, 2, 4, 29, 26, 9, 30, 8, 32];
            if (in_array(Auth::user()->role_id, $rolearray)) {
                $sales_person = SmStaff::select('user_id', 'full_name')->where('active_status', 1)->orderby('full_name', 'asc')->get();
                $vendors = SysHelper::get_customer_list_deal_lead_all_role();
            } else {
                $vendors = SysHelper::get_customer_list_deal_lead();
                $sales_person = SmStaff::select('user_id', 'full_name')->where('user_id', Auth::user()->id)->orderby('full_name', 'asc')->get();
            }

            $brand = SysBrand::select('id', 'title')->orderby('title', 'asc')->get();
            $paymentterms = SysPaymentTerms::where('active_status', '=', '1')->get();
            $designation = SmDesignation::select('title')->where('active_status', 1)->get();
            $country = SysCountries::select('id', 'name')->get();

            return view('backEnd.crm.DealView', compact('currency', 'company', 'staff', 'edit', 'leads', 'comments', 'companylist', 'currencylist', 'quoteitems', 'quotecsitems', 'paymentterms', 'collaboration', 'addressbook', 'service', 'servicecomments', 'enduser', 'product_list', 'sales_person', 'support_person', 'support', 'support_work', 'supportcomments', 'deal_track_temp', 'deal_track', 'countries', 'states', 'quote_charges', 'is_amc_item', 'check_edit_fullfill', 'cust_supp', 'vendors', 'brand', 'paymentterms', 'designation', 'country'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    /*public function view($id)
    {
        try{
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];            
            $queryra = "UPDATE sys_crm_deals SET company_id = (SELECT company_id FROM sm_staffs WHERE user_id=sys_crm_deals.owner ) WHERE company_id IS NULL";
            DB::select($queryra);

            $currency       = SysCurrencySettings::select('id','code')->get();
            $company        = SysCompany::find(session('logged_session_data.company_id'));
            $staff      = SmStaff::select('user_id','full_name')->get();

            if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2) {
                $leads = SysCrmDeals::where('id',$id)->first();
                $edit = SysCrmDeals::where('id',$id)->first();
            } else {
                $leads = SysCrmDeals::where('id',$id)->where('company_id',session('logged_session_data.company_id'))->first();
                $edit = SysCrmDeals::where('id',$id)->where('company_id',session('logged_session_data.company_id'))->first();
            }

            //return $edit;
            if(!isset($edit)){
                return redirect('crm-deals/show');
            }

            $sales_person = SmStaff::select('user_id','full_name')->where('active_status', 1)->wherein('user_id',[$edit->owner])->orderby('full_name','asc')->get();
            $support_person = SysHelper::get_engineer_list();

            // $vendors_query = SysCustSuppl::select('id','code','name')->where('catid',1); // 1 customers, 2 suppliers
            // if(Auth::user()->role_id != 1){
            //     $vendors_query->where('sales_person', Auth::user()->id);
            // }
            // $vendors = $vendors_query->get();

            //$product_list = SmItem::select('sm_items.id','sm_items.part_number','sm_items.description')->wherein('company_id',$company_id)->get();
            $product_list = SysHelper::get_product_list($company_id);

            $comments = SysCrmDealsComments::where('deal_id',$id)->orderBy('id','DESC')->get();
            $paymentterms = SysPaymentTerms::all();
            $collaboration = SysCrmDealsCollaboration::where('deal_id',$id)->get();
            $service = SysCrmService::where('deal_id',$id)->get();
            $enduser = SysCrmEndUser::where('deal_id',$id)->first();
            $support = SysCrmSupport::where('deal_id',$id)->get();
            if(count($support)>0){
                $support_work = DB::table('sys_crm_support_work')->where('support_id',$support[0]->id)->get();
            } else { $support_work = []; }

            $countries = SysCountries::all();
            $states = SysStates::all();


            if(count($service)>0){
                $servicecomments = SysCrmServiceComments::where('service_id',$service[0]->id)->get();
            }
            else{
                $servicecomments = [];
            }
            if(count($support)>0){
                $supportcomments = SysCrmSupportActivity::where('support_id',$support[0]->id)->get();
            }
            else{
                $supportcomments = [];
            }

            $quoteitems = SysCrmQuoteItems::select('sys_crm_quote_items.*','sm_items.part_number','sys_brand.title')
            ->leftjoin('sm_items','sm_items.id','sys_crm_quote_items.product_id')
            ->leftjoin('sys_brand','sys_brand.id','sm_items.brand')
            ->where('deal_id',$id)->where('quote_id',$leads->quote_id)->orderby('sort_id','ASC')->get();

            $quotecsitems = SysCrmQuoteCSItems::where('deal_id',$id)->get();

            $extra_charges=0;
            $quote_charges = SysCrmQuoteCharges::where('deal_id',$id)->where('quote_id',$leads->quote_id)->get();
            if(count($quote_charges)>0){
            $extra_charges=$quote_charges->sum('amount');}
            //return $extra_charges;

            $purchase_auto = SysPurchaseAuto::where(['deal_id' => $id, 'status' => 2, 'req_cost' => 1])->pluck('po_id');
            $purchase_cost=0;
            if(count($purchase_auto)>0){
                $purchase_cost = SysPurchaseOrderItems::select(DB::raw('sum(taxableamount) as total_cost'))->wherein('po_id',$purchase_auto)->get();
                $purchase_cost = $purchase_cost[0]->total_cost;
            }

            $deal_track = SysCrmDealTrack::where('deal_id',$id)->orderby('id','desc')->first();


            $net=0;
            $vat=0;
            $curr=1;
            $delivery_date='';
            $deal_profit=0;
            $deal_cost=0;
            $check_edit_fullfill=0;

            $first_item_brand=0;

            if(count($quoteitems)>0){
                $first_item_brand = $quoteitems[0]->title;
                foreach($quoteitems as $itms){
                    $qty = $itms->qty;
                    $price = $itms->price;
                    $discount = $itms->discount;
                    $vat = $itms->vat;
                    $net += (($price * $qty)+(($price * $qty)*$vat/100)) - ($discount+($discount*$vat/100));
                    $curr = $itms->currency_id;
                    $delivery_date = $itms->delivery_date;
                    $deal_cost += ($itms->cost*$itms->qty);
                    $deal_profit += (($price * $qty) - ($discount)) - ($itms->cost*$itms->qty);
                }
                $deal_value = $net - ($leads->deal_discount+(($leads->deal_discount*$vat)/100));
                //return $deal_value;
                $cost = $deal_cost+$extra_charges;

                $check_invoice_approved=0;
                if(isset($deal_track)){
                    if($deal_track->invoice == 1){
                        $check_invoice_approved=1;
                    }
                    if($deal_track->accounts == 1 && $deal_track->sales == 1){
                        $check_edit_fullfill=1;
                    }
                }

                DB::table('sys_crm_deals')->where('id',$id)
                    ->update([
                        'tags' => $first_item_brand,
                    ]);

                    //&& $check_invoice_approved != 1
                // if($net > 0){
                //     DB::table('sys_crm_deals')->where('id',$id)
                //     ->update([
                //         'deal_value' => $deal_value,
                //         'deal_currency' => $curr,
                //         'deal_profit' => $deal_profit-$extra_charges-$purchase_cost,
                //     ]);
                // }
                //if($net > 0 && $check_invoice_approved == 1){                
                    SysHelper::set_deal_profit($id);
                //}
            }



            $leads = SysCrmDeals::where('id',$id)->first();

            $companylist = SysCompany::select('id','company_name','city')->where('status',1)->orderBy('company_name','ASC')->get();
            $company_query = SysCompany::select('id','company_name','city')->where('status',1);
            if(Auth::user()->role_id != 1){
                $company_query->where('id', session('logged_session_data.company_id'));
            }
            $companylist = $company_query->orderBy('company_name','ASC')->get();

            $currencylist = SysCurrencySettings::select('id','code')->where('status',1)->orderBy('code','ASC')->get();

            $addressbook = SysCustSupplAddressbook::where('cust_suppl_id',$leads->cust_id)->orderBy('id','desc')->first();

            $deal_track_temp = SysCrmDealTrackTemp::where('deal_id',$id)->orderby('id','desc')->first();

            $cust_supp = SysHelper::get_customer_list_deal_lead_all_role();

            // AMC product id = 35657
            $is_amc_item = SysCrmQuoteItems::wherein('product_id',[35657])->where('deal_id',$id)->where('quote_id',$leads->quote_id)->count();

            return view('backEnd.crm.DealView', compact('currency','company','staff','edit','leads','comments','companylist','currencylist','quoteitems','quotecsitems','paymentterms','collaboration','addressbook','service','servicecomments','enduser','product_list','sales_person','support_person','support','support_work','supportcomments','deal_track_temp','deal_track','countries','states','quote_charges','is_amc_item','check_edit_fullfill','cust_supp'));
        }catch (\Exception $e) {
            return $e;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }*/


    public function createquote($id)
    {

        try {
            $maxQuoteId = SysCrmQuoteItems::where('deal_id', $id)->max('quote_id');
            $newQuoteId = $maxQuoteId + 1;

            return redirect('crm-deals/show/' . $id . '?deal_action=edit&quote=' . $newQuoteId . '&new=yes');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }

    }

    public function update(Request $request, $id)
    {


        try {

            DB::beginTransaction();
            $tags = "";
            if ($request->tags != "") {
                $tags = implode(",", $request->tags);
            }
            $doc_file = $request->file_name;
            if ($request->file('doc') != "") {
                $file = $request->file('doc');
                $doc_file = md5(time()) . "." . $file->getclientoriginalextension();
                $file->move('public/uploads/crm_deal_doc/', $doc_file);
                $doc_file = $doc_file;
            }
            if ($request->is_professional_service == 1) {
                $is_professional_service = 1;
            } else {
                $is_professional_service = 0;
            }

            $scd = SysCrmDeals::find($id);
            $current_company_id = $scd->company_id;
            $old_code = $scd->code;
            if ($request->deal_value == "") {
                $deal_value = "0.00";
            } else {
                $deal_value = $request->deal_value;
            }
            $scd->date = SysHelper::normalizeToYmd($request->date);
            $scd->deal_name = $request->deal_name;
            $scd->cust_id = $request->cust_id;
            $scd->cust_name = $request->cust_name;
            $scd->cust_no = $request->cust_no;
            $scd->cust_email = $request->cust_email;
            $scd->deal_value = $deal_value;
            $scd->source = $request->source;
            $scd->source_o = $request->source_o;
            $scd->tags = $request->tags ? implode(',', $request->tags) : null;
            $scd->stage = $request->stage;
            $scd->owner = $request->owner;
            $scd->doc = $doc_file;
            $scd->isproject = $request->isproject;
            $scd->designation = $request->designation;
            $scd->address = $request->address;
            $scd->note = $request->note;
            $scd->status = $request->status;
            $scd->estimated_close_date = SysHelper::normalizeToYmd($request->estimated_close_date);
            $scd->updated_by = Auth::user()->id;
            $scd->updated_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
            $scd->is_professional_service = $is_professional_service;
            if ($request->delivery_address_select != '' && $request->delivery_address_select != null) {
                $scd->delivery_address_select = $request->delivery_address_select;
            }


            if (!empty($request->followup_date)) {
                try {
                    // Parse from your input format
                    $date = Carbon::createFromFormat('d/m/Y h:i A', $request->followup_date); // Store in UTC (best practice)

                    // Save formatted for database
                    $scd->followup_date = $date->format('Y-m-d H:i:s');
                } catch (\Exception $e) {
                    $scd->followup_date = null;
                }
            } else {
                $scd->followup_date = null;
            }

            // dd($request->all());


            $scd->delivery_company = $request->delivery_company ?? '';
            $scd->delivery_name = $request->delivery_name ?? '';
            $scd->delivery_number = $request->delivery_number ?? '';
            $scd->delivery_email = $request->delivery_email ?? '';
            $scd->delivery_address = $request->delivery_address ?? '';
            $scd->delivery_area = $request->delivery_area1 ?? '';
            $scd->delivery_building = $request->delivery_building ?? '';
            $scd->delivery_flat_office_no = $request->delivery_flat_office_no ?? '';
            // $scd->delivery_address1 = $request->delivery_address1 ?? '';
            // $scd->delivery_address2 = $request->delivery_address2 ?? '';
            $scd->delivery_city = $request->delivery_city ?? '';
            $scd->delivery_zip_code = $request->delivery_zip_code ?? '';
            $scd->delivery_country = $request->delivery_country ?? '';
            $scd->delivery_state = $request->delivery_state ?? '';

            if ($current_company_id != $request->company) {
                $new_code = SysHelper::get_new_code_lead('sys_crm_deals', 'DL', 'code', $request->company);
                $scd->code = $new_code;
                $scd->company_id = $request->company;
            }
            $results = $scd->update();

            DB::commit();


            // **************




            if ($request->quotation_generated == 1) {

                $get_existing_doc_no = "";

                DB::beginTransaction();

                $get_existing_doc = SysCrmQuoteItems::where('deal_id', $scd->id)->first(['document_number']);
                if ($get_existing_doc) {
                    if ($request->quote_id > 1) {
                        $get_existing_doc_no = $get_existing_doc->document_number . '-' . ($request->quote_id - 1);
                    } else {
                        $get_existing_doc_no = $get_existing_doc->document_number;
                    }
                } else {
                    // First quote for this deal (common after lead conversion): generate a new doc number.
                    $get_existing_doc_no = SysHelper::getNextDealQuoteDocNo();
                }

                $deleted = SysCrmQuoteItems::where('deal_id', $scd->id)->where('quote_id', $request->quote_id)->delete();
                $customer = SysCustSuppl::where('id', $request->cust_id)->first();











                if ($request->part_number != null && count($request->part_number) > 0) {



                    for ($i = 0; $i < count($request->part_number); $i++) {
                        // dd($request->all());

                        if($request->part_number[$i] == ""){
                            continue;
                        }
                        if($request->qty[$i] == ""){
                            continue;
                        }

                        if($request->unitprice[$i] == ""){
                            continue;
                        }
                       

                        $data[] = [
                            'user_id' => $request->owner,
                            'deal_id' => $scd->id,
                            'company_id' => $request->company,
                            'currency_id' => $request->currency_id,
                            'customer_type' => $customer->customer_type,
                            'quote_validity' => $request->quote_validity,
                            'payment_terms' => $request->payment_terms,
                            'delivery_date' => SysHelper::normalizeToYmd($request->estimated_close_date),
                            'payment_terms_txt' => $request->payment_terms_txt,
                            'delivery_time' => $request->delivery_time,
                            'product_id' => $request->part_number[$i],
                            'qty' => $request->qty[$i],
                            'price' => (float) str_replace(',', '', $request->unitprice[$i] ?? 0),
                            'description' => $request->description[$i],
                            'discount' => (float) str_replace(',', '', $request->discount[$i] ?? 0),
                            'vat' => $request->tax[$i],
                            'cost' => (float) str_replace(',', '', $request->cost[$i] ?? 0),
                            'status' => 1,
                            'sort_id' => $i + 1,
                            'created_by' => Auth::user()->id,
                            'product_type' => !empty($request->product_type[$i])
                                ? $request->product_type[$i]
                                : null,
                            'quote_id' => $request->quote_id,
                            'document_number' => $get_existing_doc_no,
                        ];
                    }

                    SysCrmQuoteItems::insert($data);


                    DB::table('sys_crm_deals')->where('id', $scd->id)
                        ->update(['estimated_close_date' => SysHelper::normalizeToYmd($request->estimated_close_date), 'quote_id' => $request->quote_id, 'deal_discount' => (float) str_replace(',', '', $request->deal_discount ?? 0), 'deal_discount_vat' => ($request->deal_discount_vat ?? 0), 'terms_and_condition' => $request->terms_and_condition]);
                    SysHelper::deal_updated_at($scd->id);

                    if ($scd->tags == "" || $scd->tags == null) {

                        $item = SmItem::find($request->part_number[0]);
                        if ($item) {
                            $brandName = SmItem::getBrandName($item->brand);
                            $scd->tags = $brandName;
                        } else {
                            $scd->tags = '';
                        }

                        $scd->update();
                    }
                }


                if($scd->stage == 1){
                    DB::table('sys_crm_deals')->where('id', $scd->id)
                    ->update([
                        'stage' => 2,
                    ]);
                }

                //sys_crm_quote_charges
                SysCrmQuoteCharges::where('deal_id', $scd->id)->delete();
                for ($i = 0; $i < count($request->cfc_name); $i++) {
                    if ($request->cfc_name[$i] != "" && $request->cfc_credit_account[$i] != "" && $request->cfc_amount[$i] != "") {
                        $cfc = new SysCrmQuoteCharges();
                        $cfc->deal_id = $scd->id;
                        $cfc->quote_id = $request->quote_id;
                        $cfc->selling_exp_account = $request->cfc_name[$i];
                        $cfc->credit_account = $request->cfc_credit_account[$i];
                        $cfc->amount = $request->cfc_amount[$i];
                        $cfc->remarks = $request->cfc_remarks[$i];
                        $cfc->status = 1;
                        $cfc->created_by = Auth::user()->id;
                        $cfc->save();
                    }
                }


                DB::commit();



            }

            // **************

            DB::beginTransaction();
            if ($current_company_id != $request->company) {
                DB::table('sys_crm_deal_track')->where('deal_id', $id)->delete();
                DB::table('sys_crm_deal_track_approval_accounts_pending')->where('deal_id', $id)->delete();
                DB::table('sys_crm_deal_track_approval_delivery')->where('deal_id', $id)->delete();
                DB::table('sys_crm_deal_track_approval_invoice')->where('deal_id', $id)->delete();
                DB::table('sys_crm_deal_track_approval_purchease')->where('deal_id', $id)->delete();
                DB::table('sys_crm_deal_track_approval_receivables')->where('deal_id', $id)->delete();
                DB::table('sys_crm_deal_track_approval_sales')->where('deal_id', $id)->delete();
                DB::table('sys_crm_deal_track_approval_technical')->where('deal_id', $id)->delete();

                DB::table('sys_crm_deals_company_change')->insert([
                    'deal_id' => $id,
                    'old_code' => $old_code,
                    'new_code' => $new_code,
                    'old_company' => $current_company_id,
                    'new_company' => $request->company,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00')->format('Y-m-d H:i:s'),
                ]);

                $cc = SysCompany::where('id', $current_company_id)->value('company_name');
                $nc = SysCompany::where('id', $request->company)->value('company_name');
                DB::table('sys_crm_deals_comments')->insert(
                    [
                        'deal_id' => $id,
                        'comments' => '<span class=text-danger>Company Changed from ' . $cc . ' to ' . $nc . ' and code changed from ' . $old_code . ' to ' . $new_code . '</span>',
                        'status' => 1,
                        'created_by' => Auth::user()->id,
                        'created_at' => Carbon::now('+04:00'),
                    ]
                );
            }



            SysHelper::set_deal_profit($id);

            try {
                DB::table('sys_crm_quote_items')->where('deal_id', $id)
                    ->update([
                        'delivery_date' => SysHelper::normalizeToYmd($request->estimated_close_date),
                    ]);
            } catch (\Throwable $th) {
                //throw $th;
            }

            if ($request->stage == 5) {
                DB::table('sys_crm_deals_comments')->insert(
                    [
                        'deal_id' => $id,
                        'comments' => '<span class=text-danger>' . $request->lost_comments . '</span>',
                        'status' => 1,
                        'created_by' => Auth::user()->id,
                        'created_at' => Carbon::now('+04:00'),
                    ]
                );
            }

            SysHelper::set_user_custsupp($request->owner, $request->cust_id);

            if ($results) {

                SysCrmQuoteCartEdit::where([
                    'cart_id' => session('logged_session_data.cart_id'),
                    'user_id' => Auth::user()->id,
                    'deal_id' => $id
                ])->delete();


                DB::commit();




                if (
                    $request->filled('end_user_company_name') && $request->end_user_company_name != '' && $request->end_user_company_name != null

                    && $request->filled('serial_numbers_by_row') && $request->filled('part_number_by_row')
                ) {

                    $deviceSerial = null;

                    $serialsByRow = $request->input('serial_numbers_by_row', []);
                    $partByRow = $request->input('part_number_by_row', []);

                    $result_end = [];

                    foreach ($serialsByRow as $rowIndex => $serialJson) {

                        // Row must have a part number
                        if (!isset($partByRow[$rowIndex])) {
                            continue;
                        }

                        $partNumber = $partByRow[$rowIndex];

                        // Decode serial JSON ( ["1","2"] )
                        $serials = json_decode($serialJson, true);

                        if (!is_array($serials) || empty($serials)) {
                            continue;
                        }

                        // Initialize part if not exists
                        if (!isset($result_end[$partNumber])) {
                            $result_end[$partNumber] = [];
                        }

                        // Merge serials
                        $result_end[$partNumber] = array_merge(
                            $result_end[$partNumber],
                            $serials
                        );
                    }

                    // Cleanup: remove duplicates + empty
                    foreach ($result_end as $partNumber => $serials) {
                        $serials = array_values(array_unique(array_filter($serials)));

                        if (empty($serials)) {
                            unset($result_end[$partNumber]);
                        } else {
                            $result_end[$partNumber] = $serials;
                        }
                    }

                    // Final JSON
                    if (!empty($result_end)) {
                        $deviceSerial = json_encode($result_end);
                    }




                    DB::table('sys_crm_end_user')->insert(
                        [
                            'deal_id' => $request->deal_id,
                            'end_user_company_name' => $request->end_user_company_name,
                            'device_serial' => $request->filled('end_user_device_serial')
                                ? $deviceSerial
                                : null,
                            // 'address_line_a' => $request->address_line_a,
                            //'address_line_b' => $request->address_line_b,
                            //'city' => $request->city,
                            //'po_box' => $request->po_box,
                            'end_user_contact_person' => $request->end_user_contact_person,
                            //'job_title' => $request->job_title,
                            'mobile_no' => $request->end_user_mobile_no,
                            'email' => $request->end_user_email,
                            // 'project_name' => $request->project_name,
                            // 'project_description' => $request->project_description,
                            // 'expected_close_date' => SysHelper::normalizeToYmd($request->expected_close_date),
                            'status' => 1,
                            'created_by' => Auth::user()->id,
                            'created_at' => Carbon::now('+04:00'),

                        ]
                    );
                }


                // $currency_deal = SysCrmDeals::find($id);

                // if($request->currency_id != $currency_deal->deal_currency){

                //     $from_currency = $currency_deal->deal_currency;
                //     $to_currency = $request->currency_id;

                //     // Try exact from->to rate first
                //     $rateRow = SysCurrencyRate::where('from_currency', $from_currency)
                //         ->where('to_currency', $to_currency)
                //         ->where('status', 1)
                //         ->orderBy('id', 'desc')
                //         ->first();

                //     // dd($rateRow);


                //     $currency_updated = $this->crmquoteupdate_currency($rateRow->from_currency, $rateRow->rate, $rateRow->id, $currency_deal->quote_id, $id);
                // }




                if ($request->btnSubmit == 2) {
                    Toastr::success('Deal has been updated successfully. Please Create Quote', 'Success');
                    return redirect('crm-quote/' . $id . '/create');
                } else {
                    Toastr::success('Deal has been updated successfully', 'Success');
                    return redirect('crm-deals/show/' . $id);
                }
            } else {
                Toastr::error('Something went wrong, please try again', 'Failed');
                return redirect()->back();
            }
        } catch (\Throwable $th) {
            dd($th);
            DB::rollBack();
            return $th;
        }
    }



    function crmquoteupdate_currency($from_currency_id, $to_currency_rate, $to_currency_id, $cur_quote_id, $cur_deal_id)
    {
        try {
            if ($to_currency_id != $from_currency_id) {

                // dd($from_currency_id, $to_currency_rate, $to_currency_id, $cur_quote_id, $cur_deal_id);

                //$old_currancy = SysCurrency::where('id',$request->from_currency_id)->first();

                $to_currency = SysCurrencyRate::where('id', $to_currency_id)->value('to_currency');
                $qt = SysCrmQuoteItems::where('quote_id', $cur_quote_id)->where('deal_id', $cur_deal_id)->get();


                // dd($to_currency_rate, $qt);
                foreach ($qt as $t) {
                    //$old_price = $t->price / $old_currancy->ex_rate;
                    $new_price = $t->price * $to_currency_rate;

                    $new_discount = $t->discount * $to_currency_rate;
                    //$old_cost = $t->cost / $old_currancy->ex_rate;
                    $new_cost = $t->cost * $to_currency_rate;

                    // dd( $new_price, $t->price, $to_currency_rate);

                    DB::table('sys_crm_quote_items')->where('id', $t->id)->update(
                        [
                            'currency_id' => $to_currency,
                            'price' => $new_price,
                            'cost' => $new_cost,
                            'discount' => $new_discount,
                        ]
                    );
                }
                $deal_discount = DB::table('sys_crm_deals')->where('id', $cur_deal_id)->value('deal_discount');
                $new_deal_discount = $deal_discount * $to_currency_rate;

                DB::table('sys_crm_deals')->where('id', $cur_deal_id)
                    ->update(['deal_currency' => $to_currency, 'deal_discount' => $new_deal_discount]);
            }

            return true;

        } catch (\Exception $e) {
            return false;
        }
    }

    public function crmdealscommentsadd(Request $request)
    {

        try {
            $doc_file = null;

            if ($request->hasFile('commentsdoc')) {
                $file = $request->file('commentsdoc');
                $doc_file = md5(time()) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/crm_deal_doc/'), $doc_file);
            }

            DB::table('sys_crm_deals_comments')->insert([
                'deal_id' => $request->commentsid,
                'comments' => $request->comments,
                'commentsdoc' => $doc_file,
                'status' => 1,
                'created_by' => Auth::id(),
                'created_at' => Carbon::now('+04:00'),
            ]);

            SysHelper::deal_updated_at($request->commentsid);

            // Return JSON response for AJAX
            return response()->json([
                'status' => true,
                'message' => 'Comment has been added successfully',
                'data' => [
                    'deal_id' => $request->commentsid,
                    'comments' => $request->comments,
                    'file' => $doc_file ? asset("uploads/crm_deal_doc/$doc_file") : null,
                    'created_by' => Auth::user()->name ?? 'You',
                    'created_at' => Carbon::now('+04:00')->toDateTimeString(),
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong, please try again',
                'error' => $th->getMessage()
            ], 500);
        }
    }


    // public function crmdealscommentsadd(Request $request)
    // {
    //     try {
    //         $doc_file = "";
    //         if ($request->file('commentsdoc') != "") { 
    //             $file = $request->file('commentsdoc');
    //             $doc_file = md5(time()) . "." . $file->getclientoriginalextension();
    //             $file->move('public/uploads/crm_deal_doc/', $doc_file);
    //             $doc_file = $doc_file;
    //         }
    //         DB::table('sys_crm_deals_comments')->insert(
    //             [
    //                 'deal_id' => $request->commentsid,
    //                 'comments' => $request->comments,
    //                 'commentsdoc' => $doc_file,
    //                 'status' => 1,
    //                 'created_by' => Auth::user()->id,
    //                 'created_at' => Carbon::now('+04:00'),
    //             ]
    //             );

    //         SysHelper::deal_updated_at($request->commentsid);

    //         Toastr::success('Comments has been added successfully', 'Success');
    //         return redirect()->back();

    //     } catch (\Throwable $th) {
    //         return $th;
    //         Toastr::error('Something went wrong, please try again', 'Failed');
    //         return redirect()->back();
    //     }
    // }
    public function crmdealscommentsdelete($id)
    {
        // try {
        //     DB::table('sys_crm_deals_comments')->where('id',$id)->delete();
        //     Toastr::success('Comments has been deleted successfully', 'Success');
        //     return redirect()->back();

        // } catch (\Throwable $th) {
        //     return $th;
        //     Toastr::error('Something went wrong, please try again', 'Failed');
        //     return redirect()->back();
        // }

        try {
            // DB::table('sys_crm_leads_comments')->where('id', $id)->delete();
            $comment = SysCrmDealsComments::find($id);
            $comment->softDelete(auth()->id()); // marks deleted_by + deleted_at

            Toastr::success('Comments has been deleted successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }
    public function crmdealsupdatestage(Request $request)
    {
        try {
            DB::table('sys_crm_deals')->where('id', $request->id)
                ->update([
                    'stage' => $request->stage,
                ]);

            if ($request->stage == 5) {
                DB::table('sys_crm_deals_comments')->insert(
                    [
                        'deal_id' => $request->deal_id,
                        'comments' => '<span class=text-danger>' . $request->comments . '</span>',
                        'status' => 1,
                        'created_by' => Auth::user()->id,
                        'created_at' => Carbon::now('+04:00'),
                    ]
                );

                $check_process = SysCrmDealTrack::where('deal_id', $request->deal_id)->count();
                $body = "<br />";
                $body .= "Deal ID " . $request->deal_id . " has been cancelled by " . session('logged_session_data.full_name');
                $body .= "<br />Reason : " . $request->comments;
                $body .= "<br /><br />";

                if ($check_process != 0) {
                    SysHelper::notificationMail('Accounts', $body, 'accounts@sysllc.com', 'Deal ID ' . $request->deal_id . ' has been cancelled by ' . session('logged_session_data.full_name') . '');
                    SysHelper::notificationMail('Accounts', $body, 'accounts1@sysllc.com', 'Deal ID ' . $request->deal_id . ' has been cancelled by ' . session('logged_session_data.full_name') . '');
                    SysHelper::notificationMail('Hennie', $body, 'hennie@sysllc.com', 'Deal ID ' . $request->deal_id . ' has been cancelled by ' . session('logged_session_data.full_name') . '');
                }
                //AMC Cancel;
                SysHelper::cancel_amc($request->deal_id);
            }

            SysHelper::deal_updated_at($request->deal_id);

            $bug = 0;
        } catch (\Throwable $th) {
            return $th;
        }
        if ($bug == 0) {
            $retData = "OK";
            return json_encode(array('data' => $retData));
        } else {
            $retData = 'ERROR';
            return json_encode(array('data' => $retData));
        }
    }

    public function collaboration(Request $request)
    {
        try {
            DB::table('sys_crm_deals_collaboration')->where('deal_id', $request->collaboration_deal_id)->delete();

            for ($i = 0; $i < count($request->user_id); $i++) {
                DB::table('sys_crm_deals_collaboration')->insert(
                    [
                        'deal_id' => $request->collaboration_deal_id,
                        'user_id' => $request->user_id[$i],
                        'status' => 1,
                    ]
                );
                SysHelper::set_user_custsupp($request->user_id[$i], $request->collaboration_cust_id);
            }

            SysHelper::deal_updated_at($request->collaboration_deal_id);

            Toastr::success('Collaboration Added Successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function profitupdate(Request $request)
    {
        try {
            DB::table('sys_crm_deals')->where('id', $request->profit_deal_id)->update(['deal_profit' => $request->deal_profit]);
            SysHelper::deal_updated_at($request->profit_deal_id);

            Toastr::success('Deal Profit Updated Successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function dealcancel(Request $request)
    {
        try {
            $check_process = SysCrmDealTrack::where('deal_id', $request->cancel_deal_id)->count();
            DB::table('sys_crm_deals')->where('id', $request->cancel_deal_id)->update(['stage' => 6]); //6 cancelled
            DB::table('sys_crm_service')->where('deal_id', $request->cancel_deal_id)->update(['status' => 5]); //5 cancelled
            DB::table('sys_crm_support')->where('deal_id', $request->cancel_deal_id)->update(['status' => 4]); //4 cancelled
            SysHelper::cancel_amc($request->cancel_deal_id); //AMC Cancel;

            DB::table('sys_crm_deals_comments')->insert(
                [
                    'deal_id' => $request->cancel_deal_id,
                    'comments' => '<span class="text-white bg-danger">&nbsp;&nbsp;Deal Cancelled : ' . $request->reason . '&nbsp;&nbsp;</span>',
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                ]
            );
            SysHelper::deal_updated_at($request->cancel_deal_id);

            $body = "<br />";
            $body .= "Deal ID " . $request->cancel_deal_id . " has been cancelled by " . session('logged_session_data.full_name');
            $body .= "<br />Reason : " . $request->reason;
            $body .= "<br />";
            $body .= "<br />";

            if ($check_process != 0) {
                SysHelper::notificationMail('Accounts', $body, 'accounts@sysllc.com', 'Deal ID ' . $request->cancel_deal_id . ' has been cancelled by ' . session('logged_session_data.full_name') . '');
                SysHelper::notificationMail('Accounts', $body, 'accounts1@sysllc.com', 'Deal ID ' . $request->cancel_deal_id . ' has been cancelled by ' . session('logged_session_data.full_name') . '');
                SysHelper::notificationMail('Hennie', $body, 'hennie@sysllc.com', 'Deal ID ' . $request->cancel_deal_id . ' has been cancelled by ' . session('logged_session_data.full_name') . '');
            }

            Toastr::success('Deal Cancelled Successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function recallDealTrack(Request $request)
    {
        $dealId = (int) $request->deal_id;
        if ($dealId <= 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid deal id.',
            ], 422);
        }

        $deal = SysCrmDeals::select('id')->where('id', $dealId)->first();
        if (!$deal) {
            return response()->json([
                'status' => 'error',
                'message' => 'Deal not found.',
            ], 404);
        }

        $dealTrack = SysCrmDealTrack::where('deal_id', $dealId)->orderBy('id', 'desc')->first();
        if (!$dealTrack) {
            return response()->json([
                'status' => 'error',
                'message' => 'Deal track not found.',
            ], 404);
        }

        if ((int) $dealTrack->accounts === 1) {
            return response()->json([
                'status' => 'error',
                'message' => 'Recall is not allowed after accounts approval.',
            ], 422);
        }

        DB::beginTransaction();
        try {
            DB::table('sys_crm_deal_track')->where('deal_id', $dealId)->delete();
            DB::table('sys_crm_deal_track_approval_accounts')->where('deal_id', $dealId)->delete();
            DB::table('sys_crm_deal_track_approval_accounts_pending')->where('deal_id', $dealId)->delete();
            DB::table('sys_crm_deal_track_approval_delivery')->where('deal_id', $dealId)->delete();
            DB::table('sys_crm_deal_track_approval_invoice')->where('deal_id', $dealId)->delete();
            DB::table('sys_crm_deal_track_approval_purchease')->where('deal_id', $dealId)->delete();
            DB::table('sys_crm_deal_track_approval_receivables')->where('deal_id', $dealId)->delete();
            DB::table('sys_crm_deal_track_approval_sales')->where('deal_id', $dealId)->delete();
            DB::table('sys_crm_deal_track_approval_technical')->where('deal_id', $dealId)->delete();
            DB::table('sys_crm_deals_comments')->insert([
                'deal_id' => $dealId,
                'comments' => "Deal Track Recalled By " . Auth::user()->full_name . " on " . Carbon::now('+04:00')->format('d/m/Y h:i A'),
                'status' => 1,
                'created_by' => Auth::user()->id,
                'created_at' => Carbon::now('+04:00'),
            ]);

            SysHelper::deal_updated_at($dealId);
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Deal track recalled successfully.',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to recall deal track. Please try again.',
            ], 500);
        }
    }
    public function dealpercent(Request $request)
    {
        try {
            DB::table('sys_crm_deals')->where('id', $request->deal_percent_id)->update(['deal_percent' => $request->deal_percent]);
            Toastr::success('Deal Percent Updated Successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function adddeliveryaddress(Request $request)
    {
        try {
            DB::table('sys_cust_suppl_addressbook')->where('cust_suppl_id', $request->cust_id)->update(['set_default' => 0]);
            DB::table('sys_cust_suppl_addressbook')->insert(
                [
                    'cust_suppl_id' => $request->cust_id,
                    'address' => $request->address,
                    'contact_person' => $request->contact_person,
                    'contact_number' => $request->contact_number,
                    'contact_email' => $request->contact_email,
                    'set_default' => 1,
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                    'company_id' => session('logged_session_data.company_id'),

                ]
            );
            Toastr::success('Address Added Successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }
    public function changedeliveryaddress(Request $request)
    {
        try {
            DB::table('sys_crm_deals')->where('id', $request->cust_deal_id)->update(
                [
                    'delivery_company' => $request->delivery_company,
                    'delivery_name' => $request->delivery_name,
                    'delivery_number' => $request->delivery_number,
                    'delivery_email' => $request->delivery_email,
                    'delivery_address' => $request->delivery_address,

                    'delivery_address1' => $request->delivery_address1,
                    'delivery_address2' => $request->delivery_address2,
                    'delivery_city' => $request->delivery_city,
                    'delivery_zip_code' => $request->delivery_zip_code,
                    'delivery_country' => $request->delivery_country,
                    'delivery_state' => $request->delivery_state,
                    //'updated_by' => Auth::user()->id,
                    //'updated_at' => Carbon::now('+04:00'),
                    //'company_id' => session('logged_session_data.company_id'),
                ]
            );
            Toastr::success('Address Changed Successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function dealpagefilterlist($id, $mo, $co)
    {

        $r = SysHelper::get_data_by_role();
        $company_id = $r[0];
        $country = SysCountries::select('id', 'name')->get();
        $product = SysHelper::get_product_list($company_id);
        $staff = SmStaff::select('user_id', 'full_name')->wherein('company_id', $company_id)->orderby('full_name', 'asc')->get();

        $brand = SysBrand::select('id', 'title')->orderby('title', 'asc')->get();
        $vendors = SysHelper::get_customer_list_deal_lead();

        try {
            $collaboration = SysCrmDealsCollaboration::select('deal_id')->where('user_id', Auth::user()->id)->get();
            if (count($collaboration) > 0) {
                foreach ($collaboration as $collab) {
                    $coll[] = $collab->deal_id;
                }
            }

            $staff = SmStaff::select('user_id', 'full_name')->orderby('full_name', 'asc')->get();
            $brand = SysBrand::select('id', 'title')->orderby('title', 'asc')->get();

            if (Auth::user()->role_id != 1) {
                $sales_person = DB::table('sys_cust_suppl_assign')->select('cust_supp_id')->where('user_id', Auth::user()->id)->get();
                if (count($sales_person) > 0) {
                    foreach ($sales_person as $spid) {
                        $sp[] = $spid->cust_supp_id;
                    }
                    //$vendors_query->wherein('id', $sp);
                } else {/*$vendors_query->where('id', 0);*/
                }
            }


            $ctrl_deal_id = '';
            $ctrl_cust_id = '';
            $ctrl_stage = '';
            $ctrl_source = '';
            $ctrl_owner = '';
            $ctrl_date = '';
            $ctrl_date2 = '';
            $ctrl_isproject = '';
            $ctrl_brand = '';
            $filter_by = '';

            $query = SysCrmDeals::where('stage', '!=', 0);

            $query->where('company_id', $co);

            if ($mo == "m") {
                $query->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '" . date('Y-m') . "'");
            }
            if ($mo == "d") {
                $query->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') = '" . date('Y-m-d') . "'");
            }
            if ($mo == "q") {
                $start_date = date('Y-m-d', strtotime('first day of this month - 3 months'));
                $end_date = date('Y-m-d', strtotime('last day of this month'));
                $query->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') >= '" . $start_date . "' and DATE_FORMAT(created_at, '%Y-%m-%d') <= '" . $end_date . "'");
            }
            if ($mo == "y") {
                $query->whereRaw("DATE_FORMAT(created_at, '%Y') = '" . date('Y') . "'");
            }

            if ($id == "prospecting") {
                $ctrl_stage = 1;
                $query->where('stage', $ctrl_stage);
            }
            if ($id == "quote") {
                $ctrl_stage = 2;
                $query->where('stage', $ctrl_stage);
            }
            if ($id == "closure") {
                $ctrl_stage = 3;
                $query->where('stage', $ctrl_stage);
            }
            if ($id == "won") {
                $ctrl_stage = 4;
                $query->where('stage', $ctrl_stage);
            }
            if ($id == "lost") {
                $ctrl_stage = 5;
                $query->where('stage', $ctrl_stage);
            }
            if ($id == "project") {
                $ctrl_isproject = 1;
                $query->where('isproject', $ctrl_isproject);
            }
            if ($id == "channel") {
                $ctrl_isproject = 2;
                $query->where('isproject', $ctrl_isproject);
            }
            if ($id == "corporate") {
                $ctrl_isproject = 3;
                $query->where('isproject', $ctrl_isproject);
            }
            if (Auth::user()->role_id != 1) {
                $query->where('owner', Auth::user()->id);
                if (count($collaboration) > 0) {
                    $query->orwherein('id', $coll);
                }
            }
            $deals = $query->orderby('id', 'desc')->get();
            return view('backEnd.crm.DealList', compact('deals', 'vendors', 'staff', 'ctrl_cust_id', 'ctrl_stage', 'ctrl_source', 'ctrl_owner', 'ctrl_date', 'ctrl_date2', 'ctrl_deal_id', 'ctrl_isproject', 'brand', 'ctrl_brand', 'filter_by', 'country', 'product'));
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function leadpagefilterlist($id, $mo, $co)
    {
        try {
            $staff = SmStaff::select('user_id', 'full_name')->orderby('full_name', 'asc')->get();
            $brand = SysBrand::select('id', 'title')->orderby('title', 'asc')->get();
            $vendors = SysHelper::get_customer_list_deal_lead();

            $ctrl_lead_id = '';
            $ctrl_cust_id = '';
            $ctrl_status = '';
            $ctrl_source = '';
            $ctrl_owner = '';
            $ctrl_date = '';
            $ctrl_date2 = '';
            $ctrl_isproject = '';
            $ctrl_brand = '';
            $filter_by = '';

            $query = SysCrmLeads::where('status', '!=', 0);

            $query->where('company_id', $co);

            if ($mo == "m") {
                $query->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '" . date('Y-m') . "'");
            }
            if ($mo == "d") {
                $query->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') = '" . date('Y-m-d') . "'");
            }
            if ($mo == "q") {
                $start_date = date('Y-m-d', strtotime('first day of this month - 3 months'));
                $end_date = date('Y-m-d', strtotime('last day of this month'));
                $query->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') >= '" . $start_date . "' and DATE_FORMAT(created_at, '%Y-%m-%d') <= '" . $end_date . "'");
            }
            if ($mo == "y") {
                $query->whereRaw("DATE_FORMAT(created_at, '%Y') = '" . date('Y') . "'");
            }

            if ($id == "new") {
                $ctrl_status = 1;
                $query->where('status', $ctrl_status);
            }
            if ($id == "qualified") {
                $ctrl_status = 2;
                $query->where('status', $ctrl_status);
            }
            if ($id == "unqualified") {
                $ctrl_status = 3;
                $query->where('status', $ctrl_status);
            }
            if ($id == "pendingresponse") {
                $ctrl_status = 4;
                $query->where('status', $ctrl_status);
            }
            if ($id == "closed") {
                $ctrl_status = 10;
                $query->where('status', $ctrl_status);
            }
            if ($id == "project") {
                $ctrl_isproject = 1;
                $query->where('isproject', $ctrl_isproject);
            }
            if ($id == "channel") {
                $ctrl_isproject = 2;
                $query->where('isproject', $ctrl_isproject);
            }
            if ($id == "corporate") {
                $ctrl_isproject = 3;
                $query->where('isproject', $ctrl_isproject);
            }

            if (Auth::user()->role_id != 1) {
                $query->where('owner', Auth::user()->id);
            }
            $leads = $query->orderby('id', 'desc')->get();
            return view('backEnd.crm.LeadList', compact('leads', 'vendors', 'staff', 'ctrl_cust_id', 'ctrl_status', 'ctrl_source', 'ctrl_owner', 'ctrl_date', 'ctrl_date2', 'ctrl_lead_id', 'ctrl_isproject', 'brand', 'ctrl_brand', 'filter_by'));
        } catch (\Throwable $th) {
            return $th;
        }
    }
    public function delete(Request $request, $id) //kunal modified
    {
        try {

            // DB::table('sys_crm_deals')->where('id', $id)->delete();
            // DB::table('sys_crm_deals_comments')->where('deal_id', $id)->delete();
            // DB::table('sys_crm_quote_items')->where('deal_id', $id)->delete();

            $request->validate([
                'delete_reason' => 'required|string|max:255',
            ]);

            // Build the formatted comment
            $formattedComment = '<span class="text-danger">[Deleted] ' . e($request->delete_reason) . '</span>';

            DB::beginTransaction();

            DB::table('sys_crm_deals_comments')->insert(
                [
                    'deal_id' => $id,
                    'comments' => $formattedComment,
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                ]
            );


            DB::table('sys_crm_deals')
                ->where('id', $id)
                ->update(['deleted_at' => Carbon::now()]);

            DB::commit();

            Toastr::success('Deal Deleted Successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }
    // public function delete($id)
    // {
    //     try {

    //         DB::table('sys_crm_deals')->where('id', $id)->delete();
    //         DB::table('sys_crm_deals_comments')->where('deal_id', $id)->delete();
    //         DB::table('sys_crm_quote_items')->where('deal_id', $id)->delete();

    //         Toastr::success('Deal Deleted Successfully', 'Success');
    //         return redirect()->back();

    //     } catch (\Throwable $th) {
    //         Toastr::error('Something went wrong, please try again', 'Failed');
    //         return redirect()->back();
    //     }
    // }

    public function addenduser(Request $request)
    {
        try {
            DB::table('sys_crm_end_user')->insert(
                [
                    'deal_id' => $request->end_user_deal_id,
                    'end_user_company_name' => $request->end_user_company_name,
                    'device_serial' => $request->filled('device_serial')
                        ? $request->device_serial
                        : null,
                    // 'address_line_a' => $request->address_line_a,
                    //'address_line_b' => $request->address_line_b,
                    //'city' => $request->city,
                    //'po_box' => $request->po_box,
                    'end_user_contact_person' => $request->end_user_contact_person,
                    //'job_title' => $request->job_title,
                    'mobile_no' => $request->mobile_no,
                    'email' => $request->email,
                    // 'project_name' => $request->project_name,
                    // 'project_description' => $request->project_description,
                    // 'expected_close_date' => SysHelper::normalizeToYmd($request->expected_close_date),
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),

                ]
            );
            Toastr::success('End User Details Added Successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function destroy($id)
    {
        //
    }

    public function deleteStoreView(Request $request, $id)
    {

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            return ApiBaseMethod::sendResponse($id, null);
        }
        return view('backEnd.inventory.deleteItemStoreView', compact('id'));
    }

    public function deleteStore(Request $request, $id)
    {
        $result = SmItemStore::destroy($id);

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            if ($result) {
                return ApiBaseMethod::sendResponse(null, 'Store  has been deleted successfully');
            } else {
                return ApiBaseMethod::sendError('Something went wrong, please try again');
            }
        } else {
            if ($result) {
                return redirect('item-store')->with('message-success-delete', 'Store  has been deleted successfully');
            } else {
                return redirect()->back()->with('message-danger-delete', 'Something went wrong, please try again');
            }
        }
    }

    public function dashboarddealfilter(Request $request)
    {
        try {
            $ret = SysHelper::get_deal_filter($request->date, $request->company);
            return json_encode(array('data' => $ret));
        } catch (\Exception $e) {
            return $e;
            $retData = $e;
            return json_encode(array('data' => $retData));
        }
    }
    public function dashboardservicefilter(Request $request)
    {
        try {
            $company_id = $request->company;
            $date_id = $request->date;
            if ($company_id == 0) {
                $service = SysHelper::get_total_service_revenue_all($date_id, $company_id);
            } else {
                $service = SysHelper::get_total_service_revenue($date_id, $company_id);
            }
            $ret = [$service[0], $service[1]];
            return json_encode(array('data' => $ret));
        } catch (\Exception $e) {
            return $e;
            $retData = $e;
            return json_encode(array('data' => $retData));
        }
    }
    public function dashboardamcfilter(Request $request)
    {
        try {
            $company_id = $request->company;
            $date_id = $request->date;
            if ($company_id == 0) {
                $service = SysHelper::get_total_amc_revenue_all($date_id, $company_id);
            } else {
                $service = SysHelper::get_total_amc_revenue($date_id, $company_id);
            }
            $ret = [$service[0], $service[1]];
            return json_encode(array('data' => $ret));
        } catch (\Exception $e) {
            return $e;
            $retData = $e;
            return json_encode(array('data' => $retData));
        }
    }
    public function dashboardprojectfilter(Request $request)
    {
        try {
            $company_id = $request->company;
            $date_id = $request->date;
            if ($company_id == 0) {
                $service = SysHelper::get_total_project_revenue_all($date_id, $company_id);
            } else {
                $service = SysHelper::get_total_project_revenue($date_id, $company_id);
            }
            $ret = [$service[0], $service[1]];
            return json_encode(array('data' => $ret));
        } catch (\Exception $e) {
            return $e;
            $retData = $e;
            return json_encode(array('data' => $retData));
        }
    }
    public function dashboardsalesfilter(Request $request)
    {
        try {
            $company_id = $request->company;
            $date_id = $request->date;
            $_SESSION["page_date_id"] = $date_id;

            $from_date = date('Y-m-01');
            $to_date = date('Y-m-t');
            if ($date_id == 'd') {
                $from_date = date('Y-m-d');
                $to_date = date('Y-m-d');
            } elseif ($date_id == 'm') {
                $from_date = date('Y-m-01');
                $to_date = date('Y-m-t');
            } elseif ($date_id == 'y') {
                $from_date = date('Y-01-01');
                $to_date = date('Y-12-31');
            } elseif ($date_id == 'q') {
                $quarter = SysHelper::get_quarter(date('m'));
                $from_date = $quarter[0];
                $to_date = $quarter[1];
            } elseif ($date_id == 'pm') {
                $c_date = Carbon::createFromFormat('Y-m', date('Y-m'))->subMonth();
                $from_date = $c_date->format('Y-m-01');
                $to_date = $c_date->format('Y-m-t');
            } elseif ($date_id == 'pq') {
                $quarter = SysHelper::get_pre_quarter(date('m'));
                $from_date = $quarter[0];
                $to_date = $quarter[1];
            }

            $sales_revenue_report = SysHelper::get_total_revenue_all_by_company($from_date, $to_date, $company_id, []);
            $sales_forecast_report = SysHelper::get_total_forcast_all_by_company($from_date, $to_date, $company_id, []);
            $ret = [$sales_revenue_report[0], $sales_forecast_report[0], 0];
            return json_encode(array('data' => $ret));
        } catch (\Exception $e) {
            return $e;
            $retData = $e;
            return json_encode(array('data' => $retData));
        }
    }

    public function getComments(Request $request, $id) //kunal new added
    {
        try {
            $comments = SysCrmDealsComments::with('createdby:id,user_id,first_name,last_name')->where('deal_id', $id)->orderBy('id', 'DESC')->get();
            $bug = 0;
        } catch (\Exception $e) {
            return $e;
            $bug = $e->errorInfo[1];
        }
        if ($bug == 0) {
            return json_encode(array('data' => $comments));
        } else {
            $retData = 'ERROR';
            return json_encode(array('data' => $retData));
        }
    }

    public function restoreDeal(Request $request, $id) //kunal new added
    {
        try {
            $request->validate([
                'restore_reason' => 'required|string|max:255',
            ]);

            // Build the formatted comment
            $formattedComment = '<span class="text-success">[Restored] ' . e($request->restore_reason) . '</span>';

            DB::beginTransaction();
            DB::table('sys_crm_deals')->where('id', $id)->update(['deleted_at' => null]);
            DB::table('sys_crm_deals_comments')->insert(
                [
                    'deal_id' => $id,
                    'comments' => $formattedComment,
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                ]
            );
            DB::commit();
            Toastr::success('Deal Restored Successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function crmdealscommentsrestore($id)
    {
        try {
            // DB::table('sys_crm_leads_comments')->where('id', $id)->delete();
            $comment = SysCrmDealsComments::find($id);
            $comment->restoreComment();


            Toastr::success('Comments has been deleted successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }




    public function storeReserveStockT(Request $request)
    {




        try {

            dd($request->all());

            DB::beginTransaction();

            // Convert date from d/m/Y to Y-m-d format
            $reserveDate = Carbon::createFromFormat('d/m/Y', $request->reserve_date)->format('Y-m-d');

            // Check if there's enough stock available
            $stockId = $request->reserve_stock_id;
            $requestedQty = $request->reserve_qty;




            // Create reserve stock record
            $reserveStock = new ReserveStock();
            $reserveStock->stock_id = $stockId;
            $reserveStock->deal_id = $request->reserve_deal_id ?: null;
            $reserveStock->part_number = $request->reserve_part_number;
            $reserveStock->customer_id = $request->reserve_customer_id;
            $reserveStock->customer_name = SysCustSuppl::find($request->reserve_customer_id)->customer_name_display ?? 'N/A';
            $reserveStock->sales_person_id = $request->reserve_sales_person ?: null;
            $reserveStock->reserve_qty = $requestedQty;
            $reserveStock->reserve_date = $reserveDate;
            $reserveStock->company_id = session('logged_session_data.company_id');
            $reserveStock->created_by = Auth::id();
            $reserveStock->save();

            DB::commit();

            Toastr::success('Stock reserved successfully!', 'Success');
            return redirect()->back();

        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Failed to reserve stock: ' . $e->getMessage(), 'Error');
            return redirect()->back()->withInput();
        }
    }

    public function storeReserveStock(Request $request)
    {
        try {
            DB::beginTransaction();

            $customer = SysCustSuppl::find($request->reserve_customer_id);
            $now = now();

            foreach ($request->qty as $index => $qty) {

                // Skip empty or zero qty
                if (empty($qty) || $qty <= 0) {
                    continue;
                }

                $reserveDate = isset($request->reserve_date[$index])
                    ? Carbon::createFromFormat('d/m/Y', $request->reserve_date[$index])->format('Y-m-d')
                    : now()->format('Y-m-d');

                // 🔹 Check if record already exists
                $existing = ReserveStock::where('deal_id', $request->req_deal_id)
                    ->where('stock_id', $request->reserve_stock_id[$index])
                    ->first();

                if ($existing) {
                    // dd($existing);
                    // 🔹 Update qty
                    $existing->update([
                        'reserve_qty' => $qty,
                        'reserve_date' => $reserveDate,
                        'updated_at' => $now,
                    ]);
                } else {
                    // 🔹 Create new record
                    ReserveStock::create([
                        'stock_id' => $request->reserve_stock_id[$index],
                        'deal_id' => $request->req_deal_id,
                        'part_number' => $request->reserve_part_number[$index],
                        'customer_id' => $request->reserve_customer_id,
                        'customer_name' => $customer->customer_name_display ?? 'N/A',
                        'sales_person_id' => $request->reserve_sales_person_id,
                        'reserve_qty' => $qty,
                        'reserve_date' => $reserveDate,
                        'company_id' => session('logged_session_data.company_id'),
                        'created_by' => Auth::id(),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }

            DB::commit();

            Toastr::success('Stock reserved successfully!', 'Success');
            return back();

        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error('Failed to reserve stock: ' . $e->getMessage(), 'Error');
            return back()->withInput();
        }
    }





}

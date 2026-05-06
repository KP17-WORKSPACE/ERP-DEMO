<?php

namespace App\Http\Controllers;

use App\SmItem;
use App\SmStaff;
use App\SysHelper;
use App\SmSupplier;
use App\SysCompany;
use App\SysVatType;
use App\SmQuotation;
use App\SysCrmDeals;
use App\SysCountries;
use App\SysCustSuppl;
use App\SysQuotations;
use App\SysCrmDealTrack;
use App\SysPaymentTerms;
use App\SysCrmQuoteItems;
use App\SmGeneralSettings;
use App\SysChartofAccounts;
use App\SysQuotationsItems;
use App\SmQuotationProducts;
use App\SysCurrencySettings;
use Illuminate\Http\Request;
use App\SmInspectingDepartment;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\SmDesignation;
use App\SysBrand;
use App\SysCrmQuoteCart;
use App\SysCrmQuotation;
use Carbon\Carbon;
use App\SysCrmQuotationItem;
use App\SysStates;
use App\SysCrmQuoteCharges;
use App\SysCustSupplAddressbook;
use App\SysCrmDealsComments;
use App\SysCrmDealTrackTemp;
use App\SysCrmSupport;
use App\SysCrmEndUser;
use App\SysCrmQuoteCartEdit;
use App\SysCustomerType;
use App\ReserveStock;
use App\SysCrmDealsCollaboration;
use App\SysCrmService;
use App\SysCurrency;

class SysQuotationController extends Controller
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
            $query = SysCrmDeals::with('track:id,deal_id')->select('id', 'quote_id', 'cust_id', 'deal_value', 'company_id', 'owner', 'date', 'code', 'deal_currency')->where('stage', 4);
            $query->wherein('company_id', $company_id);
            //$query->wherein('created_by',$r[1]);

            $quotations = $query->orderby('id', 'desc')->get();



            $selectedQuote = [];


            $active_id = $id;
            $action = false;
            $editData = [];
            $addData = [];




            if ($request->has('qn_action')) {
                $poAction = $request->input('qn_action');

                if ($poAction === 'add') {
                    $action = 'add';

            



                    $addData = $this->add(); // Get all data for adding

                } elseif ($poAction === 'edit') {
                    $action = 'edit';
                    if ($request->has('quote')) {
                        $editData = $this->getQuoteEdit($active_id, $request->quote);
                    } else {
                        $editData = $this->getQuoteEdit($active_id);
                    }

                }
            } else {
                if ($id) {
                    $active_id = $id;
                    if($active_id != 'show'){
                    $record = SysCrmDeals::with('track:id,deal_id')->select('id', 'quote_id', 'cust_id', 'deal_value', 'company_id', 'owner', 'date', 'code', 'deal_currency')->where('id', $id)->where('stage', 4)->first();
                    $selectedQuote = $this->get_deal_pdf_data($record->id);

                    }
                   
                  
                } else {
                    $firstRecord = $quotations->first();
                    if ($firstRecord) {
                        $active_id = $quotations->first()->id;
                        $selectedQuote = $this->get_deal_pdf_data($active_id);
                    }
                }
            }

            //return $quotations;
            return view('backEnd.quotations.quotations_list', compact('quotations', 'selectedQuote', 'active_id', 'action', 'editData', 'addData'));
        } catch (\Exception $e) {
            dd($e);
           
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function getDetails($id)
    {
        $data = $this->get_deal_pdf_data($id);

        if (!empty($data) && is_array($data)) {
            return view('backEnd/quotations/quotation_pdf', $data);
        } else {
            return response("Error loading details!", 404);
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
            $currency = SysCurrencySettings::all();
            $items = SysHelper::get_product_list($company_id);
            $company = SysCompany::find(session('logged_session_data.company_id'));
            $sales_man = SmStaff::where('role_id', 2)->get();
            $vendors = SmSupplier::all();
            $custsuppl = SysCustSuppl::where('catid', 1)->get(); //1 customers, 2 suppliers
            $items = SmItem::where('status', 1)->get();
            $quotations = SmQuotation::all();
            $departments = SmInspectingDepartment::all();
            $countries = SysCountries::All();
            $vattype = SysVatType::all();
            $paymentterms = SysPaymentTerms::all();

            return view('backEnd/quotations/manage_quotations', compact('quotations', 'sales_man', 'vendors', 'items', 'departments', 'currency', 'items', 'company', 'custsuppl', 'countries', 'vattype', 'paymentterms'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function add()
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $currency = SysCurrencySettings::select('id', 'code')->get();
            $company = SysCompany::orderby('sort_id', 'asc')->get();
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

            $companylist = SysCompany::wherein('id', $company_id)->orderby('sort_id', 'asc')->get();
            $currencylist = SysCurrencySettings::select('id', 'code')->where('status', 1)->orderBy('code', 'ASC')->get();
            $paymentterms = SysPaymentTerms::all();

            $deals = $query->orderby('id', 'desc')->paginate(200);

            $rolearray = [1, 28, 27, 10, 3, 2, 4, 29, 26, 9, 30, 8, 32];
            if (in_array(Auth::user()->role_id, $rolearray)) {
                $sales_person = SmStaff::select('user_id', 'full_name')->where('active_status', 1)->orderby('full_name', 'asc')->get();
                $vendors = SysHelper::get_customer_list_deal_lead_all_role();
            } else {
                $vendors = SysHelper::get_customer_list_deal_lead();
                $sales_person = SmStaff::select('user_id', 'full_name')->where('user_id', Auth::user()->id)->orderby('full_name', 'asc')->get();
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

    public function store(Request $request)
    {
        if ($request->customer == "") {
            Toastr::error('Customer not found', 'Failed');
            return redirect()->back();
        }
        if ($request->currency == "") {
            Toastr::error('Currency not found', 'Failed');
            return redirect()->back();
        }
        if ($request->sales_man == "") {
            Toastr::error('Sales Man not found', 'Failed');
            return redirect()->back();
        }
        if ($request->delivery_terms == "") {
            Toastr::error('Delivery Terms not found', 'Failed');
            return redirect()->back();
        }
        if ($request->payment_terms == "") {
            Toastr::error('Payment Terms not found', 'Failed');
            return redirect()->back();
        }
        if ($request->quote_validity == "") {
            Toastr::error('Quote Validity not found', 'Failed');
            return redirect()->back();
        }

        if ($request->part_number[0] != "none" && $request->qty[0] != "" && $request->unitprice[0] != "") {
        } else {
            Toastr::error('Items not found', 'Failed');
            return redirect()->back();
        }

        DB::beginTransaction();
        try {
            $quotation = new SysQuotations();
            $quotation->doc_number = 'QTO-' . SysHelper::get_new_maxid('sys_quotations', 'id');
            $quotation->qt_date = date('Y-m-d');
            $quotation->customer = $request->customer;
            $quotation->currency = $request->currency;
            $quotation->narration = $request->narration;
            $quotation->sales_man = $request->sales_man;
            $quotation->customer_ref_no = $request->customer_ref_no;
            $quotation->customer_ref_date = date('Y-m-d', strtotime($request->customer_ref_date));
            $quotation->delivery_terms = $request->delivery_terms;
            $quotation->payment_terms = $request->payment_terms;
            $quotation->payment_terms2 = $request->payment_terms2;
            $quotation->quote_validity = $request->quote_validity;
            $quotation->vat_type = $request->vat_type;
            $quotation->vat_country = $request->vat_country;
            $quotation->vat_state = $request->vat_state;
            $quotation->vat_percentage = $request->vat_percentage;
            $quotation->vat_number = $request->vat_number;
            $quotation->created_by = Auth::user()->id;
            $quotation->status = 1;
            $quotation->save();
            $quotation->toArray();

            for ($i = 0; $i < count($request->part_number); $i++) {
                if ($request->part_number[$i] != "" && $request->qty[$i] != "" && $request->unitprice[$i] != "") {
                    $pii = new SysQuotationsItems();
                    $pii->qt_id = $quotation->id;
                    $pii->part_number = $request->part_number[$i];
                    $pii->tax = $request->net_vat;
                    $pii->qty = $request->qty[$i];
                    $pii->unitprice = $request->unitprice[$i];
                    $pii->value = $request->value[$i];
                    $pii->discount = ($request->discount[$i] === '' ? '0.00' : $request->discount[$i]);
                    $pii->taxableamount = ($request->taxableamount[$i] === '' ? '0.00' : $request->taxableamount[$i]);
                    $pii->vatamount = ($request->vatamount[$i] === '' ? '0.00' : $request->vatamount[$i]);
                    $pii->status = 1;
                    $pii->created_by = Auth::user()->id;
                    $pii->save();
                }
            }


            DB::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect('quotations');
        } catch (\Exception $e) {
            return $e;
            DB::rollback();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    //end store method 

    public function show(Request $request, $id)
    {
        // Forward to index and include the original Request object to match the signature.
        return $this->index($request, $id);
    }


    public function edit(Request $request, $id)
    {
        $des = SmItem::select('description')->where('id', 1001)->first();
        //return $des->description;

        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $editData = SysQuotations::find($id);
            $editDataList = SysQuotationsItems::where('qt_id', $id)->get();

            $currency = SysCurrencySettings::all();
            $items = SysHelper::get_product_list($company_id);
            $company = SysCompany::find(session('logged_session_data.company_id'));
            $sales_man = SmStaff::where('role_id', 2)->get();
            $vendors = SmSupplier::all();
            $custsuppl = SysCustSuppl::where('catid', 1)->get(); //1 customers, 2 suppliers
            $items = SmItem::where('status', 1)->get();
            $quotations = SmQuotation::all();
            $departments = SmInspectingDepartment::all();
            $countries = SysCountries::All();
            $vattype = SysVatType::all();
            $paymentterms = SysPaymentTerms::all();

            return view('backEnd/quotations/manage_quotations_edit', compact('quotations', 'sales_man', 'vendors', 'items', 'departments', 'currency', 'items', 'company', 'custsuppl', 'countries', 'vattype', 'paymentterms', 'editData', 'editDataList'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function update(Request $request, SmQuotation $smQuotation)
    {
        $input = $request->all();
        if ($request->customer == "") {
            Toastr::error('Customer not found', 'Failed');
            return redirect()->back();
        }
        if ($request->currency == "") {
            Toastr::error('Currency not found', 'Failed');
            return redirect()->back();
        }
        if ($request->sales_man == "") {
            Toastr::error('Sales Man not found', 'Failed');
            return redirect()->back();
        }
        if ($request->delivery_terms == "") {
            Toastr::error('Delivery Terms not found', 'Failed');
            return redirect()->back();
        }
        if ($request->payment_terms == "") {
            Toastr::error('Payment Terms not found', 'Failed');
            return redirect()->back();
        }
        if ($request->quote_validity == "") {
            Toastr::error('Quote Validity not found', 'Failed');
            return redirect()->back();
        }

        if ($request->part_number[0] != "none" && $request->qty[0] != "" && $request->unitprice[0] != "") {
        } else {
            Toastr::error('Items not found', 'Failed');
            return redirect()->back();
        }

        DB::beginTransaction();
        try {
            $quotation = SysQuotations::find($request->id);

            $quotation->customer = $request->customer;
            $quotation->currency = $request->currency;
            $quotation->narration = $request->narration;
            $quotation->sales_man = $request->sales_man;
            $quotation->customer_ref_no = $request->customer_ref_no;
            $quotation->customer_ref_date = date('Y-m-d', strtotime($request->customer_ref_date));
            $quotation->delivery_terms = $request->delivery_terms;
            $quotation->payment_terms = $request->payment_terms;
            $quotation->payment_terms2 = $request->payment_terms2;
            $quotation->quote_validity = $request->quote_validity;
            $quotation->vat_type = $request->vat_type;
            $quotation->vat_country = $request->vat_country;
            $quotation->vat_state = $request->vat_state;
            $quotation->vat_percentage = $request->vat_percentage;
            $quotation->vat_number = $request->vat_number;

            $quotation->updated_by = Auth::user()->id;
            $quotation->save();
            $quotation->toArray();

            SysQuotationsItems::query()
                ->where('qt_id', '=', $request->id)
                ->each(function ($oldRecord) {
                    $newRecord = $oldRecord->replicate();
                    $newRecord->setTable('sys_quotations_items_history');
                    $newRecord->save();
                    $oldRecord->delete();
                });

            for ($i = 0; $i < count($request->part_number); $i++) {
                if ($request->part_number[$i] != "" && $request->qty[$i] != "" && $request->unitprice[$i] != "") {
                    $pii = new SysQuotationsItems();
                    $pii->qt_id = $quotation->id;
                    $pii->part_number = $request->part_number[$i];
                    $pii->tax = $request->net_vat;
                    $pii->qty = $request->qty[$i];
                    $pii->unitprice = $request->unitprice[$i];
                    $pii->value = $request->value[$i];
                    $pii->discount = ($request->discount[$i] === '' ? '0.00' : $request->discount[$i]);
                    $pii->taxableamount = ($request->taxableamount[$i] === '' ? '0.00' : $request->taxableamount[$i]);
                    $pii->vatamount = ($request->vatamount[$i] === '' ? '0.00' : $request->vatamount[$i]);
                    $pii->status = 1;
                    $pii->created_by = Auth::user()->id;
                    $pii->save();
                }
            }

            DB::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect('quotations');
        } catch (\Exception $e) {
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
            $result = SmQuotation::destroy($id);

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


    public function get_print_data($id, $qid)
    {
        try {
            $quotation = SysCrmDeals::where('id', $id)->first();
            $quotationitems = SysCrmQuoteItems::where('deal_id', $id)->where('quote_id', $qid)->orderby('sort_id', 'ASC')->get();

            $currency = $quotationitems[0]->currency->code;
            $paymentterms = $quotationitems[0]->paymentterms->title;
            if (strtolower($quotationitems[0]->paymentterms->title) == "other") {
                $paymentterms = $quotationitems[0]->payment_terms_txt;
            }
            $deliverydate = $quotationitems[0]->delivery_date;
            $deliverytime = $quotationitems[0]->delivery_time;

            $pdfheader = $quotationitems[0]->company->pdf_header ?? '';
            $pdffooter = $quotationitems[0]->company->pdf_footer ?? '';
            $pdfwatermark = $quotationitems[0]->company->pdf_watermark ?? '';
            $pdffirstpage = $quotationitems[0]->company->pdf_first_page ?? '';

            $net_vat = $quotationitems[0]->vat;

            $wp = null;
            $wt = null;
            $wv = null;
            $net_vat = null;
            $document_number = $quotationitems[0]->document_number;




            return ['quotation' => $quotation, 'document_number' => $document_number, 'quotationitems' => $quotationitems, 'currency' => $currency, 'paymentterms' => $paymentterms, 'deliverydate' => $deliverydate, 'deliverytime' => $deliverytime, 'wp' => $wp, 'wt' => $wt, 'wv' => $wv, 'pdfheader' => $pdfheader, 'pdffooter' => $pdffooter, 'pdfwatermark' => $pdfwatermark, 'pdffirstpage' => $pdffirstpage, 'net_vat' => $net_vat];
        } catch (\Throwable $th) {
            return $th;
        }
    }


    public function search(Request $request)
    {

        $q = $request->get('query');
        $formattedDate = null;
        if (preg_match('/\d{2}[\/\-]\d{2}[\/\-]\d{4}/', $q)) {
            $normalized = str_replace('/', '-', $q);
            $formattedDate = date('Y-m-d', strtotime($normalized));
        }


        $company_id = session('logged_session_data.company_id');


        $query = SysCrmDeals::with(['customername:id,name', 'ownername:id,full_name', 'dealcurrency:id,code'])->select('id', 'quote_id', 'cust_id', 'deal_value', 'company_id', 'owner', 'date', 'code', 'deal_currency')->where('stage', 4);
        $query->where('company_id', $company_id);






        $query->where(function ($query) use ($q, $formattedDate) {
            $query->where(function ($qsub) use ($q) {



                if ($q) {
                    $qsub->where('code', 'like', "%{$q}%")
                        ->orWhere('deal_value', 'like', "%{$q}%");
                }
            });

            if ($formattedDate) {
                // Combine inside same group
                $query->orWhere(function ($q2) use ($formattedDate) {
                    $q2->whereDate('date', $formattedDate);
                });
            }
        });




        $amc_list = $query->orderby('id', 'desc')->get();

        $amc_list->transform(function ($item) {
            $item->formatted_deal_value = \App\SysHelper::currancy_format_deal($item->deal_value, $item->company_id);
            return $item;
        });




        return response()->json($amc_list);
    }


    public function storeQuote(Request $request) //kunal modified
    {

        if ($request->is_professional_service == 1) {
            $is_professional_service = 1;
        } else {
            $is_professional_service = 0;
        }

        DB::beginTransaction();
        try {

            if ($request->deal_value == "") {
                $deal_value = "0.00";
            } else {
                $deal_value = $request->deal_value;
            }




            $delivery_company = SysCustSuppl::select('sys_cust_suppl.*')
                ->join('sys_chartofaccounts as ca', 'ca.account_code', 'sys_cust_suppl.code')
                ->where('sys_cust_suppl.id', $request->cust_id)->first();




            $scd = new SysCrmDeals();
            $scd->code = SysHelper::get_new_code_lead('sys_crm_deals', 'DL', 'code', session('logged_session_data.company_id'));
            $scd->date = Carbon::now()->format('Y-m-d');
            $scd->deal_name = $request->deal_name;
            $scd->cust_id = $request->cust_id;


            $scd->cust_name = $delivery_company->cust_name;
            $scd->cust_no = $delivery_company->cust_no;

            $scd->deal_value = $deal_value;
            $scd->tags = null;
            $scd->stage = 4;
            $scd->owner = $request->owner;


            $scd->isproject = null;
            $scd->designation = $delivery_company->designation;

            $state = SysStates::find($delivery_company->vat_state);
            $country = SysCountries::find($delivery_company->vat_country);

            $scd->address = $delivery_company->flat_office_no . ' ' . $delivery_company->building_name . ' ' . $delivery_company->area . ' ' . $delivery_company->city . ' ' . $state->name . ' ' . $country->name;

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

            $scd->followup_date = Carbon::now()
                ->addDays(3)
                ->setTime(11, 0, 0)
                ->format('Y-m-d H:i:s');

            $scd->note = 'Deal created from quotation. Quotation ID: ' . $request->quote_id;
            $scd->status = 1;
            $scd->estimated_close_date = SysHelper::normalizeToYmd($request->estimated_close_date);
            $scd->created_by = Auth::user()->id;
            $scd->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
            $scd->company_id = session('logged_session_data.company_id');
            $scd->is_professional_service = $is_professional_service;
            $scd->save();
            $scd->toArray();


            $quote_id = SysCrmQuoteItems::where('deal_id', $scd->id)->max('quote_id');
            $customer = SysCustSuppl::where('id', $request->cust_id)->first();
            $document_number = SysHelper::getNextDealQuoteDocNo();

            if ($request->part_number != null && count($request->part_number) > 0) {


                for ($i = 0; $i < count($request->part_number); $i++) {
                    $data[] = [
                        'user_id' => $request->owner,
                        'deal_id' => $scd->id,
                        'company_id' => session('logged_session_data.company_id'),
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
                        'document_number' => $document_number,
                    ];


                }


                SysCrmQuoteItems::insert($data);
                DB::table('sys_crm_deals')->where('id', $scd->id)
                    ->update(['estimated_close_date' => SysHelper::normalizeToYmd($request->estimated_close_date), 'quote_id' => $quote_id + 1, 'deal_discount' => (float) str_replace(',', '', $request->deal_discount ?? 0), 'deal_discount_vat' => ($request->deal_discount_vat ?? 0), 'terms_and_condition' => $request->terms_and_condition]);
                SysHelper::deal_updated_at($scd->id);

                $scd->note = 'Deal created from quotation. Quotation ID: ' . $document_number;
                $scd->save();



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



                //sys_crm_quote_charges
                for ($i = 0; $i < count($request->cfc_name); $i++) {
                    if ($request->cfc_name[$i] != "" && $request->cfc_credit_account[$i] != "" && $request->cfc_amount[$i] != "") {
                    
                        $cfc = new SysCrmQuoteCharges();
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
                SysProformaInvoiceController::re_generate($scd->id, '', '');

                SysHelper::set_deal_profit($scd->id);
            }






            SysHelper::set_user_custsupp($request->owner, $request->cust_id);

            $results = 0;
            DB::commit();



            if ($results == 0) {

                SysCrmQuoteCart::where([
                    'cart_id' => session('logged_session_data.cart_id'),
                    'user_id' => Auth::user()->id,
                ])->delete();



                Toastr::success('Quote has been added successfully', 'Success');
                return redirect('quotations/' . $scd->id);

            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }

        } catch (\Exception $e) {
            return $e;
            DB::rollback();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function getQuoteEdit($id, $qid = 0)
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


            $companylist = SysCompany::wherein('id', $company_id)->orderby('sort_id', 'asc')->get();
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
            $company = SysCompany::orderby('sort_id', 'asc')->get();

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
                $sales_person = SmStaff::select('user_id', 'full_name')->where('active_status', 1)->orderby('full_name', 'asc')->get();
                $vendors = SysHelper::get_customer_list_deal_lead_all_role();
            } else {
                $vendors = SysHelper::get_customer_list_deal_lead();
                $sales_person = SmStaff::select('user_id', 'full_name')->where('user_id', Auth::user()->id)->orderby('full_name', 'asc')->get();
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


    public function updateQuote(Request $request, $id)
    {
        try {


            DB::beginTransaction();


            $scd = SysCrmDeals::find($id);
            $scd->deal_name = $request->deal_name;
            $scd->cust_id = $request->cust_id;
            $scd->owner = $request->owner;
            $scd->note = 'Deal updated from quotation. Quotation ID: ' . $request->document_number;
            $scd->estimated_close_date = SysHelper::normalizeToYmd($request->estimated_close_date);
            $scd->updated_by = Auth::user()->id;
            $scd->updated_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');


            $delivery_company = SysCustSuppl::select('sys_cust_suppl.*')
                ->join('sys_chartofaccounts as ca', 'ca.account_code', 'sys_cust_suppl.code')
                ->where('sys_cust_suppl.id', $request->cust_id)->first();

            $scd->cust_name = $delivery_company->customer_salutation . ' ' . $delivery_company->first_name . ' ' . $delivery_company->last_name;

            $scd->cust_no = $delivery_company->cust_no;
            $scd->cust_email = $delivery_company->email;
            $scd->designation = $delivery_company->designation;

            $state = SysStates::find($delivery_company->vat_state);
            $country = SysCountries::find($delivery_company->vat_country);

            $scd->address = $delivery_company->flat_office_no . ' ' . $delivery_company->building_name . ' ' . $delivery_company->area . ' ' . $delivery_company->city . ' ' . $state->name . ' ' . $country->name;

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

            $results = $scd->update();
            DB::commit();


            // **************






            $get_existing_doc_no = "";

            DB::beginTransaction();

            $get_existing_doc = SysCrmQuoteItems::where('deal_id', $scd->id)->first(['document_number']);
            if ($get_existing_doc) {
                if ($request->quote_id > 1) {
                    $get_existing_doc_no = $get_existing_doc->document_number . '-' . ($request->quote_id - 1);
                } else {
                    $get_existing_doc_no = $get_existing_doc->document_number;
                }
            }



            $deleted = SysCrmQuoteItems::where('deal_id', $scd->id)->where('quote_id', $request->quote_id)->delete();
            $customer = SysCustSuppl::where('id', $request->cust_id)->first();



            // if ($deleted == 0) {
            //     $get_existing_doc_no = SysHelper::getNextDealQuoteDocNo();
            // }







            if ($request->part_number != null && count($request->part_number) > 0) {



                for ($i = 0; $i < count($request->part_number); $i++) {
                    // dd($request->all());

                    $data[] = [
                        'user_id' => $request->owner,
                        'deal_id' => $scd->id,
                        'currency_id' => $request->currency_id,
                        'company_id' => $scd->company_id,
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
                    ->update(['estimated_close_date' => SysHelper::normalizeToYmd($request->estimated_close_date), 'quote_id' => $request->quote_id, 'deal_discount' => (float) str_replace(',', '', $request->deal_discount ?? 0),'deal_discount_vat' => ($request->deal_discount_vat ?? 0), 'terms_and_condition' => $request->terms_and_condition]);
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
            }



            DB::commit();



            SysProformaInvoiceController::re_generate($scd->id, '', '');


            // **************

            DB::beginTransaction();

            SysHelper::set_deal_profit($id);

            try {
                DB::table('sys_crm_quote_items')->where('deal_id', $id)
                    ->update([
                        'delivery_date' => SysHelper::normalizeToYmd($request->estimated_close_date),
                    ]);
            } catch (\Throwable $th) {
                //throw $th;
            }



            SysHelper::set_user_custsupp($request->owner, $request->cust_id);

            if ($results) {

                SysCrmQuoteCartEdit::where([
                    'cart_id' => session('logged_session_data.cart_id'),
                    'user_id' => Auth::user()->id,
                    'deal_id' => $id
                ])->delete();


                DB::commit();

                Toastr::success('Quotation has been updated successfully', 'Success');
                return redirect('quotations/' . $id);

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


    public function createquote($id)
    {

        try {
            $maxQuoteId = SysCrmQuoteItems::where('deal_id', $id)->max('quote_id');
            $newQuoteId = $maxQuoteId + 1;

            return redirect('quotations/' . $id . '?qn_action=edit&quote=' . $newQuoteId . '&new=yes');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
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



}

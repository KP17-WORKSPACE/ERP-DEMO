<?php

namespace App\Http\Controllers;

use App\Role;
use App\User;
use App\SysCustomer;
use App\SysCompany;
use App\SysPaymentTerms;
use App\SmBaseSetup;
use App\ApiBaseMethod;
use App\SmDesignation;
use App\SmLeaveRequest;
use App\SmHumanDepartment;
use App\SmStudentDocument;
use App\SmStudentTimeline;
use App\SmHrPayrollGenerate;
use App\SysChartofAccounts;
use App\SysAccountType;
use App\SmStaff;
use App\SysAccountGroupSub2;
use App\SysCountries;
use App\SysCountryCode;
use App\SysCrmAmcTable;
use App\SysCrmDeals;
use App\SysCrmLeads;
use App\SysCrmPSServiceTable;
use App\SysCrmQuoteItems;
use App\SysCustomerType;
use App\SysCustSuppl;
use App\SysCustSupplAddressbook;
use App\SysCustSupplAddressbookCart;
use App\SysCustSupplAddressbookForm;
use App\SysCustSupplAssign;
use App\SysCustSupplContact;
use App\SysCustSupplContactForm;
use App\SysCustSupplDoc;
use App\SysCustSupplDocForm;
use App\SysCustSupplForm;
use App\SysCustSupplImport;
use App\SysHelper;
use App\SysItemStockImport;
use App\SysPurchaseType;
use App\SysSaleType;
use App\SysStates;
use App\SysVat;
use App\SysVatType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PHPExcel;
use PHPExcel_IOFactory;
use App\SysChartofAccountsTransaction;
use Illuminate\Support\Facades\Storage;





class SysCustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
    }

    public function getViewCustomer($id)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];

            $custDetails = SysCustSuppl::find($id);
            $custAddress = SysCustSupplAddressbook::where('cust_suppl_id', $id)->get();
            $custContact = SysCustSupplContact::where('cust_suppl_id', $id)->get();
            $custDoc = SysCustSupplDoc::where('cust_suppl_id', $id)->get();

            $salespersons = SmStaff::select('sm_staffs.full_name', 'sm_staffs.user_id')->join('sys_cust_suppl_assign', 'sys_cust_suppl_assign.user_id', 'sm_staffs.user_id')->where('sys_cust_suppl_assign.cust_supp_id', $id)->get();


            $pending = SysCrmDeals::select('sys_crm_deals.id', 'code', 'deal_name', 'estimated_close_date', 'stage', 'deal_currency', 'sys_crm_deals.company_id', 'deal_value', 'sys_crm_deals.created_at', 'sys_crm_deals.updated_at', 'cust_id', 'owner', 'receivables', 'delivery', 'invoice', 'purchease', 'sales', 'accounts')
                ->join('sys_crm_deal_track', 'sys_crm_deal_track.deal_id', 'sys_crm_deals.id')->where('stage', '=', 4)->where('cust_id', $id)
                ->where('purchease', 0)->get();

            $invoiced = SysCrmDeals::select('sys_crm_deals.id', 'code', 'deal_name', 'estimated_close_date', 'stage', 'deal_currency', 'sys_crm_deals.company_id', 'deal_value', 'sys_crm_deals.created_at', 'sys_crm_deals.updated_at', 'cust_id', 'owner', 'receivables', 'delivery', 'invoice', 'purchease', 'sales', 'accounts')
                ->join('sys_crm_deal_track', 'sys_crm_deal_track.deal_id', 'sys_crm_deals.id')->where('stage', '=', 4)->where('cust_id', $id)
                ->where('invoice', 1)->where('delivery', '!=', 1)->get();

            $delivery = SysCrmDeals::select('sys_crm_deals.id', 'code', 'quote_id', 'deal_name', 'estimated_close_date', 'stage', 'deal_currency', 'sys_crm_deals.company_id', 'deal_value', 'sys_crm_deals.created_at', 'sys_crm_deals.updated_at', 'cust_id', 'owner', 'receivables', 'delivery', 'invoice', 'purchease', 'sales', 'accounts')
                ->join('sys_crm_deal_track', 'sys_crm_deal_track.deal_id', 'sys_crm_deals.id')->where('stage', '=', 4)->where('cust_id', $id)
                ->where('invoice', 1)->where('delivery', 1)->where('receivables', '!=', 1)->get();

            $receivables = SysCrmDeals::select('sys_crm_deals.id', 'code', 'deal_name', 'estimated_close_date', 'stage', 'deal_currency', 'sys_crm_deals.company_id', 'deal_value', 'sys_crm_deals.created_at', 'sys_crm_deals.updated_at', 'cust_id', 'owner', 'receivables', 'delivery', 'invoice', 'purchease', 'sales', 'accounts')
                ->join('sys_crm_deal_track', 'sys_crm_deal_track.deal_id', 'sys_crm_deals.id')->where('stage', '=', 4)->where('cust_id', $id)
                ->where('invoice', 1)->where('delivery', 1)->where('receivables', 1)->get();

            $editAssign = DB::table('sys_cust_suppl_assign')->select('full_name')->join('users', 'users.id', 'sys_cust_suppl_assign.user_id')->where('cust_supp_id', $id)->distinct()->get();

            $amcdata = SysCrmAmcTable::where('company_id', session('logged_session_data.company_id'))->where('cust_name', $id)->orderby('date', 'desc')->get();
            $support = SysCrmPSServiceTable::where('status', 0)->where('cust_name', $id)->orderby('id', 'desc')->get();

            // Outstanding data - similar to supplier view
            $data_all = [];
            $amount = 0;
            $overdue = 999999;
            $ageing = 99999;

            $com_id = session('logged_session_data.company_id');
            $acc_id = SysChartofAccounts::where('account_code', $custDetails->code)->first();

            if ($acc_id) {
                $account_id = $acc_id->id;
                $accounts = SysChartofAccounts::where('account_code', $custDetails->code)->get();
                $transaction_no = SysChartofAccountsTransaction::where('account_id', $account_id)->where('status', 1)->where('company_id', $com_id)->pluck('transaction_no');

                if (count($transaction_no) > 0) {
                    $data_query = SysChartofAccountsTransaction::select('transaction_date', 'transaction_id', 'transaction_no', DB::raw('sum(debit_amount) as debit_amount'), DB::raw('sum(credit_amount) as credit_amount'), DB::raw($account_id . ' as account_id'))->where('company_id', $com_id);
                    $data_query->wherein('transaction_no', $transaction_no)->where('status', 1);
                    $data_query->wherein('transaction_type', ['salesinvoice', 'salesreturn', 'opbinvoice', 'openingbalance111']);
                    $data_query->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') <= '" . date('Y-m-d') . "'");
                    $data_all[] = $data_query->groupby('transaction_date', 'transaction_id', 'transaction_no')->orderby('transaction_date', 'asc')->get();
                }
            } else {
                $accounts = collect([]);
            }

            if (!empty($custDetails)) {
                return compact('custDetails', 'custAddress', 'custContact', 'custDoc', 'salespersons', 'pending', 'invoiced', 'delivery', 'receivables', 'editAssign', 'amcdata', 'support', 'data_all', 'accounts', 'amount', 'overdue', 'ageing');
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function getCustomerDetails($id)
    {
        $data = $this->getViewCustomer($id);

        if (!empty($data) && is_array($data)) {
            return view('backEnd.cust-suppl.viewCustomer', $data);
        } else {
            return response("Error loading details!", 404);
        }
    }










    public function getViewCustomerForm($id)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $com_id = session('logged_session_data.company_id');
            $countries = SysCountries::all();
            $states = SysStates::all();
            $vattype = SysVatType::all();
            $vat = SysVat::select('sys_vat.*', 'sys_countries.name')->join('sys_countries', 'sys_countries.id', 'sys_vat.vat_country')->wherein('company_id', $company_id)->where('status', 1)->get();
            $accounts = SysChartofAccounts::where('status', 1)->wherein('company_id', $company_id)->get();
            $accounttype = SysAccountType::all();
            $roles = Role::where('active_status', '=', '1')->where('id', 2)->get();
            $paymentterms = SysPaymentTerms::where('active_status', '=', '1')->get();

            $customer_type = SysCustomerType::where('status', '=', '1')->get();
            $sale_type = SysSaleType::where('status', '=', '1')->get();

            $staffs = SysHelper::get_sales_persons2();
            $company = SysHelper::get_company_names();

            $designation = SmDesignation::select('id', 'title')->where('active_status', 1)->orderby('title', 'asc')->get();
            $department = SmHumanDepartment::select('id', 'name')->where('active_status', 1)->orderby('name', 'asc')->get();

            $editData = SysCustSupplForm::where('id', $id)->first();
            $editAddressbook = SysCustSupplAddressbookForm::where('cust_suppl_id', $id)->get();
            $editContact = SysCustSupplContactForm::where('cust_suppl_id', $id)->get();
            $editDoc = SysCustSupplDocForm::where('cust_suppl_id', $id)->get();
            $editAssign = DB::table('sys_cust_suppl_assign')->where('cust_supp_id', $id)->get();
            $row_id = $id;

            $a = $editData->name;
            $b = $editData->email;
            $excisting_list = SysCustSuppl::select('id', 'code', 'name', 'email', 'first_name', 'mobile', 'contcat_number')->where(function ($query) use ($a, $b) {
                $query->where('name', '=', $a)
                    ->orWhere('email', '=', $b);
            })->where('catid', 1)->get();


            $custDetails = $editData;


            if (!empty($custDetails)) {
                return compact('custDetails', 'countries', 'states', 'vattype', 'vat', 'accounts', 'accounttype', 'roles', 'paymentterms', 'customer_type', 'sale_type', 'staffs', 'company', 'designation', 'department', 'editData', 'editAddressbook', 'editContact', 'editDoc', 'editAssign', 'row_id', 'excisting_list');
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function getCustomerFormDetails($id)
    {
        $data = $this->getViewCustomerForm($id);

        if (!empty($data) && is_array($data)) {
            return view('backEnd.cust-suppl.viewCustomerForm', $data);
        } else {
            return response("Error loading details!", 404);
        }
    }


    // public function viewCustomer($id)
    // {
    //     try{
    //         $r = SysHelper::get_data_by_role();
    //         $company_id = $r[0];

    //         $custDetails = SysCustSuppl::find($id);
    //         $custAddress = SysCustSupplAddressbook::where('cust_suppl_id',$id)->get();
    //         $custContact = SysCustSupplContact::where('cust_suppl_id',$id)->get();
    //         $custDoc = SysCustSupplDoc::where('cust_suppl_id',$id)->get();

    //         $salespersons = SmStaff::select('sm_staffs.full_name','sm_staffs.user_id')->join('sys_cust_suppl_assign','sys_cust_suppl_assign.user_id','sm_staffs.user_id')->where('sys_cust_suppl_assign.cust_supp_id',$id)->get();


    //         $pending = SysCrmDeals::select('sys_crm_deals.id','code','deal_name','estimated_close_date','stage','deal_currency','sys_crm_deals.company_id','deal_value','sys_crm_deals.created_at','sys_crm_deals.updated_at','cust_id','owner','receivables','delivery','invoice','purchease','sales','accounts')
    //         ->join('sys_crm_deal_track','sys_crm_deal_track.deal_id','sys_crm_deals.id')->where('stage','=',4)->where('cust_id',$id)
    //         ->where('purchease',0)->get();

    //         $invoiced = SysCrmDeals::select('sys_crm_deals.id','code','deal_name','estimated_close_date','stage','deal_currency','sys_crm_deals.company_id','deal_value','sys_crm_deals.created_at','sys_crm_deals.updated_at','cust_id','owner','receivables','delivery','invoice','purchease','sales','accounts')
    //         ->join('sys_crm_deal_track','sys_crm_deal_track.deal_id','sys_crm_deals.id')->where('stage','=',4)->where('cust_id',$id)
    //         ->where('invoice',1)->where('delivery','!=',1)->get();

    //         $delivery = SysCrmDeals::select('sys_crm_deals.id','code','quote_id','deal_name','estimated_close_date','stage','deal_currency','sys_crm_deals.company_id','deal_value','sys_crm_deals.created_at','sys_crm_deals.updated_at','cust_id','owner','receivables','delivery','invoice','purchease','sales','accounts')
    //         ->join('sys_crm_deal_track','sys_crm_deal_track.deal_id','sys_crm_deals.id')->where('stage','=',4)->where('cust_id',$id)
    //         ->where('invoice',1)->where('delivery',1)->where('receivables','!=',1)->get();

    //         $receivables = SysCrmDeals::select('sys_crm_deals.id','code','deal_name','estimated_close_date','stage','deal_currency','sys_crm_deals.company_id','deal_value','sys_crm_deals.created_at','sys_crm_deals.updated_at','cust_id','owner','receivables','delivery','invoice','purchease','sales','accounts')
    //         ->join('sys_crm_deal_track','sys_crm_deal_track.deal_id','sys_crm_deals.id')->where('stage','=',4)->where('cust_id',$id)
    //         ->where('invoice',1)->where('delivery',1)->where('receivables',1)->get();

    //         $editAssign = DB::table('sys_cust_suppl_assign')->select('full_name')->join('users', 'users.id','sys_cust_suppl_assign.user_id')->where('cust_supp_id',$id)->distinct()->get();

    //         $amcdata = SysCrmAmcTable::where('company_id',session('logged_session_data.company_id'))->where('cust_name',$id)->orderby('date','desc')->get();
    //         $support = SysCrmPSServiceTable::where('status', 0)->where('cust_name',$id)->orderby('id', 'desc')->get();

    //         if (!empty($custDetails)) {
    //             return view('backEnd.cust-suppl.viewCustomer', compact('custDetails','custAddress','custContact','custDoc','salespersons','pending','invoiced','delivery','receivables','editAssign','amcdata','support'));
    //         } else {
    //             Toastr::error('Operation Failed', 'Failed');
    //             return redirect()->back();
    //         }
    //     }catch (\Exception $e) {
    //         return $e;
    //        Toastr::error('Operation Failed', 'Failed');
    //        return redirect()->back(); 
    //     }
    // }

    public function customer(Request $request, $id = null)
    {

        $r = SysHelper::get_data_by_role();
        $company_id = $r[0];
        $com_id = session('logged_session_data.company_id');

        $staff = SysHelper::get_sales_persons2();
        $countries = SysCountries::orderby('name', 'asc')->get();
        $customer_list = SysHelper::get_customer_list_all($com_id);
        $states = SysStates::select('id', 'name')->orderby('name', 'asc')->get();
        $ctrl_company_name = "";
        $ctrl_contact_name = "";
        $ctrl_email = "";
        $ctrl_vat_country = "";
        $ctrl_vat_state = "";
        $ctrl_sales_person = "";
        $ctrl_sales = "";
        $status_filter = "";
        $information_filter = "";
        $filter_by = "";
        $ctrl_date = "";
        $ctrl_date2 = "";
        $assigned_filter = [];
        try {
            //if($_POST){
            $customer_query = SysCustSuppl::select('id', 'updated_by', 'created_by', 'name', 'code', 'contcat_person', 'mobile', 'email', 'vat_country', 'vat_percentage', 'vat_number', 'credit_limit', 'credit_days', 'payment_terms', 'transaction_type', 'customer_type', 'first_name', 'last_name', 'contcat_number', 'customer_salutation', 'status', 'is_file', 'internal', 'created_at', 'updated_at', 'customer_name_display')
                ->where('catid', 1);

            if (SysHelper::get_pagination_post($request)) {
                if ($request->company_name != "") {
                    $customer_query->where('name', 'like', '%' . $request->company_name . '%');
                    $ctrl_company_name = $request->company_name;
                }
                if ($request->contact_name != "") {
                    $customer_query->where('contcat_person', 'like', '%' . $request->contact_name . '%');
                    $ctrl_contact_name = $request->contact_name;
                }
                if ($request->email != "") {
                    $customer_query->where('email', 'like', '%' . $request->email . '%');
                    $ctrl_email = $request->email;
                }
                if ($request->vat_country != "") {
                    $customer_query->where('vat_country', $request->vat_country);
                    $ctrl_vat_country = $request->vat_country;
                }
                if ($request->vat_state != "") {
                    $customer_query->where('vat_state', $request->vat_state);
                    $ctrl_vat_state = $request->vat_state;
                }
                if ($request->sales_person != "") {
                    $sales_person = DB::table('sys_cust_suppl_assign')->select('cust_supp_id')->where('user_id', $request->sales_person)->get();
                    $ctrl_sales_person = $request->sales_person;
                    if (count($sales_person) > 0) {
                        foreach ($sales_person as $spid) {
                            $sp[] = $spid->cust_supp_id;
                        }
                        $customer_query->wherein('id', $sp);
                    } else {
                        $customer_query->where('id', 0);
                    }
                }

                if ($request->assigned_filter != "" && !empty($request->assigned_filter)) {
                    // Handle multiple user selection
                    if (is_array($request->assigned_filter)) {
                        $customer_query->whereHas('createdBy', function ($q) use ($request) {
                            $q->whereIn('users.id', $request->assigned_filter);
                        });
                    } else {
                        // Single selection fallback
                        if ($request->assigned_filter == 'none') {
                            $customer_query->whereDoesntHave('createdBy');
                        } else {
                            $customer_query->whereHas('createdBy', function ($q) use ($request) {
                                $q->where('users.id', $request->assigned_filter);
                            });
                        }
                    }
                    $assigned_filter = $request->assigned_filter;
                }



                if ($request->status_filter != "") {
                    $customer_query->where('status', $request->status_filter);
                    $status_filter = $request->status_filter;
                }

                if ($request->information_filter != "") {


                    if ($request->information_filter == 'complete') {
                        $customer_query->where(function ($q) {
                            $q->where('status', '!=', 3)
                                ->where('vat_country', '!=', '')
                                ->where('vat_percentage', '!=', '')
                                ->whereRaw('LENGTH(mobile) >= 9')
                                ->whereRaw('LENGTH(email) >= 5')
                                ->whereRaw('LENGTH(first_name) >= 3')
                                ->whereRaw('LENGTH(contcat_number) >= 8')
                                ->where('transaction_type', '!=', '')
                                ->where('customer_salutation', '!=', '')
                                ->where('customer_salutation', '!=', '.')
                                ->where('is_file', '!=', 0)
                                ->where(function ($sub) {
                                    $sub->where('transaction_type', '!=', 'Credit')
                                        ->orWhere(function ($sub2) {
                                            $sub2->whereNotIn('credit_limit', ['', '0.00', '0'])
                                                ->whereNotIn('credit_days', ['', '0']);
                                        });
                                })
                                ->where(function ($sub3) {
                                    $sub3->where('customer_type', 7)
                                        ->orWhereRaw('LENGTH(vat_number) >= 5');
                                });
                        });
                    } elseif ($request->information_filter == 'incomplete') {
                        // Opposite: incomplete if any required field missing
                        $customer_query->where(function ($q) {
                            $q->where('status', 3)
                                ->orWhere('vat_country', '')
                                ->orWhere('vat_percentage', '')
                                ->orWhereRaw('LENGTH(mobile) < 9')
                                ->orWhereRaw('LENGTH(email) < 5')
                                ->orWhereRaw('LENGTH(first_name) < 3')
                                ->orWhereRaw('LENGTH(contcat_number) < 8')
                                ->orWhere('transaction_type', '')
                                ->orWhere('customer_salutation', '')
                                ->orWhere('customer_salutation', '.')
                                ->orWhere('is_file', 0)
                                ->orWhere(function ($sub) {
                                    $sub->where('transaction_type', 'Credit')
                                        ->where(function ($sub2) {
                                            $sub2->whereIn('credit_limit', ['', '0.00', '0'])
                                                ->orWhereIn('credit_days', ['', '0']);
                                        });
                                })
                                ->orWhere(function ($sub3) {
                                    $sub3->where('customer_type', '!=', 7)
                                        ->whereRaw('LENGTH(vat_number) < 5');
                                });
                        });
                    }




                    $information_filter = $request->information_filter;
                }

                if ($request->sales != "") {
                    $total_customers = SysCustSuppl::select('sys_cust_suppl.id')
                        ->join('sys_cust_suppl_assign', 'sys_cust_suppl_assign.cust_supp_id', 'sys_cust_suppl.id')
                        ->where('catid', 1)->where('user_id', $request->sales)->pluck('id');

                    $customer_query->wherein('id', $total_customers);

                    if ($request->status != "") {
                        $threeMonthsAgo = Carbon::now()->subMonths(3)->format('Y-m-d');
                        $oneMonthsAgo = Carbon::now()->subMonths(1)->format('Y-m-d');

                        $dealIdsFromInvoice = SysCrmDeals::join('sys_crm_deal_track_approval_invoice as i', 'i.deal_id', '=', 'sys_crm_deals.id')
                            ->where('sys_crm_deals.stage', 4)
                            ->where('sys_crm_deals.owner', $request->sales)
                            ->where('i.status', 1)
                            ->where('i.created_at', '>=', $oneMonthsAgo)
                            ->pluck('sys_crm_deals.cust_id');

                        // From deals with estimated close date
                        $dealIdsFromEstimatedClose = SysCrmDeals::where('stage', 4)
                            ->where('owner', $request->sales)
                            ->where('estimated_close_date', '>=', $oneMonthsAgo)
                            ->pluck('cust_id');

                        // From leads
                        $leadIds = SysCrmLeads::whereIn('status', [0, 1, 2])
                            ->where('owner', $request->sales)
                            ->where('updated_at', '>=', $oneMonthsAgo)
                            ->pluck('cust_id');

                        // Merge all and count unique customer IDs
                        $active_customers = $dealIdsFromInvoice
                            ->merge($dealIdsFromEstimatedClose)
                            ->merge($leadIds);

                        /*$active_customers = SysCrmDeals::join('sys_crm_deal_track_approval_invoice as i', 'i.deal_id', 'sys_crm_deals.id')->where('stage', 4)->where('i.status', 1)
                        ->where('owner', $request->sales)->where('i.created_at', '>=', $threeMonthsAgo)->pluck('cust_id');*/




                        $potential_customers = SysCrmDeals::join('sys_crm_deal_track as t', 't.deal_id', 'sys_crm_deals.id')->where('owner', $request->sales)->where(['accounts' => 1, 'sales' => 1, 'purchease' => 1])->where('invoice', '!=', 1)->where('stage', 4)->pluck('cust_id');

                        $inactive_customers = SysCrmDeals::join('sys_crm_deal_track_approval_invoice as i', 'i.deal_id', 'sys_crm_deals.id')->where('stage', 4)->where('i.status', 1)->where('owner', $request->sales)
                            ->wherenotin('cust_id', $active_customers)
                            ->pluck('cust_id');

                        if ($request->status == "active") {
                            $customer_query->wherein('id', $active_customers);
                        }
                        if ($request->status == "inactive") {
                            $customer_query->wherenotin('id', $active_customers);
                        }
                        if ($request->status == "potential") {
                            $customer_query->wherein('id', $potential_customers);
                        }
                        if ($request->status == "open") {
                        }
                    }
                } else {
                    $customer_query->whereRaw("find_in_set($com_id,company_access)");
                }

                // Priority 1: Manual date range input
                if (!empty($request->date)) {
                    $ctrl_date = SysHelper::normalizeToYmd($request->date);
                    $ctrl_date2 = !empty($request->date2)
                        ? SysHelper::normalizeToYmd($request->date2)
                        : $ctrl_date;
                }

                // Priority 2: Predefined filters (only if manual dates are not used)
                if (!empty($request->filter_by)) {
                    switch ($request->filter_by) {
                        case "today":
                            $ctrl_date = date('Y-m-d');
                            $ctrl_date2 = date('Y-m-d');
                            $filter_by = 'today';
                            break;

                        case "this_week":
                            $ctrl_date = date('Y-m-d', strtotime('last sunday'));
                            $ctrl_date2 = date('Y-m-d', strtotime('this saturday'));
                            $filter_by = 'this_week';
                            break;

                        case "last_week":
                            $ctrl_date = date('Y-m-d', strtotime('last sunday -7 days'));
                            $ctrl_date2 = date('Y-m-d', strtotime('last saturday'));
                            $filter_by = 'last_week';
                            break;

                        case "this_month":
                            $ctrl_date = date('Y-m-01');
                            $ctrl_date2 = date('Y-m-t');
                            $filter_by = 'this_month';
                            break;

                        case "last_month":
                            $ctrl_date = date('Y-m-d', strtotime('first day of previous month'));
                            $ctrl_date2 = date('Y-m-d', strtotime('last day of previous month'));
                            $filter_by = 'last_month';
                            break;

                        case "last_6_months":
                            $ctrl_date = date('Y-m-d', strtotime('first day of this month - 6 months'));
                            $ctrl_date2 = date('Y-m-d', strtotime("last day of this month"));
                            $filter_by = 'last_6_months';
                            break;

                        case "this_year":
                            $ctrl_date = date('Y-01-01');
                            $ctrl_date2 = date('Y-12-31');
                            $filter_by = 'this_year';
                            break;

                        case "last_year":
                            $ctrl_date = date('Y-01-01', strtotime('-1 year'));
                            $ctrl_date2 = date('Y-12-31', strtotime('-1 year'));
                            $filter_by = 'last_year';
                            break;
                    }
                }

                // Apply filter only if both dates are set
                if (!empty($ctrl_date) && !empty($ctrl_date2)) {
                    $customer_query->whereBetween(DB::raw("DATE(created_at)"), [$ctrl_date, $ctrl_date2]);

                }

            } else {
                if (Auth::user()->role_id != 1 && Auth::user()->role_id != 2 && Auth::user()->role_id != 3 && Auth::user()->role_id != 27 && Auth::user()->role_id != 28 && Auth::user()->role_id != 35) {
                    if (Auth::user()->role_id == 5 || Auth::user()->role_id == 8) {
                        $users = SmStaff::select('user_id')->where('company_id', $company_id)->get();
                        foreach ($users as $value) {
                            $userid[] = $value->user_id;
                        }
                        $customer_query->wherein('sales_person', $userid);
                    } else {
                        $customer_query->where('sales_person', Auth::user()->id);
                    }
                }
                $customer_query->whereRaw("find_in_set($com_id,company_access)");
            }
            //$customer_query->wherein('r.created_by',$r[1]);


            // $customer = $customer_query->orderby('name','asc')->paginate(50);
            $customer = $customer_query->orderBy('created_at', 'desc')->paginate(100);


            $duplicateNames = DB::table('sys_chartofaccounts')
                ->select(DB::raw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TRIM(account_name)), ' ', ''), '@', ''), '#', ''), '.', ''), '-', ''), '_', ''), '(', ''), ')', '') AS duplicate_name"))->where('subgroup2', 7)->where('status', 1)
                ->groupBy(DB::raw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TRIM(account_name)), ' ', ''), '@', ''), '#', ''), '.', ''), '-', ''), '_', ''), '(', ''), ')', '')"))
                ->havingRaw('COUNT(*) > 1')
                ->get();

            if (count($duplicateNames) > 0) {
                $dn = [];
                foreach ($duplicateNames as $key) {
                    $dn[] = $key->duplicate_name;
                }
                $placeholders = implode(',', array_fill(0, count($dn), '?'));

                $duplicate_customer = DB::table('sys_chartofaccounts')
                    ->select('id', 'account_name', 'account_code', DB::raw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TRIM(account_name)), ' ', ''), '@', ''), '#', ''), '.', ''), '-', ''), '_', ''), '(', ''), ')', '') AS duplicate_name"))
                    ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TRIM(account_name)), ' ', ''), '@', ''), '#', ''), '.', ''), '-', ''), '_', ''), '(', ''), ')', '') IN ($placeholders)", $dn)->where('subgroup2', 7)->where('status', 1)
                    ->orderByRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TRIM(account_name)), ' ', ''), '@', ''), '#', ''), '.', ''), '-', ''), '_', ''), '(', ''), ')', '') ASC")
                    ->get();
            } else {
                $duplicate_customer = [];
            }




            $active_id = $id;
            $selectedCus = [];
            $addCustomer = [];
            $action = false;
            $editData = [];

            if ($request->has('customer_action')) {
                $poAction = $request->input('customer_action');

                if ($poAction === 'add') {
                    $action = 'add';
                    $addCustomer = $this->getAddCustomer(); // Get data for adding supplier
                } elseif ($poAction === 'edit') {
                    $action = 'edit';
                    $editData = $this->getCustomerEdit($active_id); // Get all data for editing

                } elseif ($poAction === 'createcustomer') {
                    $action = 'createcustomer';
                    $supplier_id = $request->input('supplier_id');
                    $editData = $this->getCustomerEdit($supplier_id); // Get all data for viewing

                }
            } else {
                if ($id) {
                    $selectedCus = $this->getViewCustomer($id);
                } else {

                    $firstRecord = $customer->first();
                    if ($firstRecord) {
                        $active_id = $firstRecord->id;
                        $selectedCus = $this->getViewCustomer($firstRecord->id);
                    }
                }
            }



            //return $duplicate_customer;

            return view('backEnd.cust-suppl.customer_list', compact('customer', 'staff', 'countries', 'states', 'customer_list', 'duplicate_customer', 'ctrl_company_name', 'ctrl_contact_name', 'ctrl_email', 'ctrl_vat_country', 'ctrl_vat_state', 'ctrl_sales_person', 'active_id', 'selectedCus', 'addCustomer', 'action', 'editData', 'status_filter', 'information_filter', 'assigned_filter', 'filter_by', 'ctrl_date', 'ctrl_date2'));

            /*$form_data = [
                'customer' => $customer,
                'staff' => $staff,
                'countries' => $countries,
                'states' => $states,
            ];
            session()->put('customer_list_query', $form_data);
            return redirect('customer');*/

            //return view('backEnd.cust-suppl.customer_list', compact('customer','staff','countries','states'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function customer_pending(Request $request, $id = null)
    {
        $r = SysHelper::get_data_by_role();
        $company_id = $r[0];
        $com_id = session('logged_session_data.company_id');
        $staff = SysHelper::get_sales_persons2();
        $countries = SysCountries::all();
        $states = SysStates::all();
        $ctrl_company_name = "";
        $ctrl_contact_name = "";
        $ctrl_email = "";
        $ctrl_vat_country = "";
        $ctrl_vat_state = "";
        $ctrl_sales_person = "";
        $ctrl_sales = "";
        $status_filter = "";
        $information_filter = "";
        $assigned_filter = [];
        try {
            //if($_POST){
            if (SysHelper::get_pagination_post($request)) {
                $customer_query = SysCustSuppl::whereRaw("find_in_set($com_id,company_access)")
                    ->where('catid', 1);
                if ($request->company_name != "") {
                    $customer_query->where('name', 'like', '%' . $request->company_name . '%');
                }
                if ($request->contact_name != "") {
                    $customer_query->where('contcat_person', 'like', '%' . $request->contact_name . '%');
                }
                if ($request->email != "") {
                    $customer_query->where('email', 'like', '%' . $request->email . '%');
                }
                if ($request->vat_country != "") {
                    $customer_query->where('vat_country', $request->vat_country);
                }
                if ($request->sales_person != "") {
                    $sales_person = DB::table('sys_cust_suppl_assign')->select('cust_supp_id')->where('user_id', $request->sales_person)->get();
                    if (count($sales_person) > 0) {
                        foreach ($sales_person as $spid) {
                            $sp[] = $spid->cust_supp_id;
                        }
                        $customer_query->wherein('id', $sp);
                    } else {
                        $customer_query->where('id', 0);
                    }
                }
            } else {
                $customer_query = SysCustSuppl::whereRaw("find_in_set($com_id,company_access)")
                    ->where('catid', 1);
                if (Auth::user()->role_id != 1 && Auth::user()->role_id != 2 && Auth::user()->role_id != 3 && Auth::user()->role_id != 27 && Auth::user()->role_id != 28) {
                    if (Auth::user()->role_id == 5 || Auth::user()->role_id == 8) {
                        $users = SmStaff::select('user_id')->where('company_id', $company_id)->get();
                        foreach ($users as $value) {
                            $userid[] = $value->user_id;
                        }
                        $customer_query->wherein('sales_person', $userid);
                    } else {
                        $customer_query->where('sales_person', Auth::user()->id);
                    }
                }
            }
            //$customer_query->wherein('r.created_by',$r[1]);


            $customer = $customer_query->where('status', 3)->orderby('name', 'asc')->paginate(100);


            $active_id = $id;
            $selectedCus = [];
            $addCustomer = [];
            $action = false;
            $editData = [];

            if ($request->has('customer_action')) {
                $poAction = $request->input('customer_action');

                if ($poAction === 'add') {
                    $action = 'add';
                    $addCustomer = $this->getAddCustomer(); // Get data for adding supplier
                } elseif ($poAction === 'edit') {
                    $action = 'edit';
                    $editData = $this->getCustomerEdit($active_id); // Get all data for editing

                }
            } else {
                if ($id) {
                    $selectedCus = $this->getViewCustomer($id);
                } else {
                    $firstRecord = $customer->first();
                    if ($firstRecord) {
                        $active_id = $firstRecord->id;
                        $selectedCus = $this->getViewCustomer($firstRecord->id);
                    }
                }
            }


            return view('backEnd.cust-suppl.customer_list_pending', compact('customer', 'staff', 'countries', 'states', 'active_id', 'selectedCus', 'addCustomer', 'action', 'editData', 'ctrl_company_name', 'ctrl_contact_name', 'ctrl_email', 'ctrl_vat_country', 'ctrl_vat_state', 'ctrl_sales_person', 'ctrl_sales', 'status_filter', 'information_filter', 'assigned_filter'));

            /*$form_data = [
                'customer' => $customer,
                'staff' => $staff,
                'countries' => $countries,
                'states' => $states,
            ];
            session()->put('customer_list_query', $form_data);
            return redirect('customer');*/

            //return view('backEnd.cust-suppl.customer_list', compact('customer','staff','countries','states'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function customerlist()
    {
        try {
            $customer = session('customer_list_query.customer');
            $staff = session('customer_list_query.staff');
            $countries = session('customer_list_query.countries');
            $states = session('customer_list_query.states');

            return view('backEnd.cust-suppl.customer_list', compact('customer', 'staff', 'countries', 'states'));
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function getAddCustomer()
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $com_id = session('logged_session_data.company_id');
            $countries = SysCountries::all();
            $vattype = SysVatType::all();
            $vat = SysVat::select('sys_vat.*', 'sys_countries.name')->join('sys_countries', 'sys_countries.id', 'sys_vat.vat_country')->wherein('company_id', $company_id)->where('status', 1)->get();
            $accounts = SysChartofAccounts::where('status', 1)->wherein('company_id', $company_id)->get();
            $accounttype = SysAccountType::all();
            $roles = Role::where('active_status', '=', '1')->where('id', 2)->get();
            $paymentterms = SysPaymentTerms::where('active_status', '=', '1')->get();

            $designation = SmDesignation::select('id', 'title')->where('active_status', 1)->orderby('title', 'asc')->get();
            $department = SmHumanDepartment::select('id', 'name')->where('active_status', 1)->orderby('name', 'asc')->get();

            $customer_type = SysCustomerType::where('status', '=', '1')->get();
            $sale_type = SysSaleType::where('status', '=', '1')->get();
            //$staffs = SmStaff::select('id','full_name')->where('active_status', '=', '1')->whereIn('designation_id', array(9,1,2,3))->get();
            $staffs = SysHelper::get_sales_persons2();

 

            $company = SysHelper::get_company_names();

            $address_cart = SysCustSupplAddressbookCart::select('sys_cust_suppl_addressbook_cart.*', 'sys_countries.name as c_name', 'sys_states.name as s_name')
                ->join('sys_countries', 'sys_countries.id', 'sys_cust_suppl_addressbook_cart.country')
                ->join('sys_states', 'sys_states.id', 'sys_cust_suppl_addressbook_cart.state')
                ->where('cart_id', session('logged_session_data.cart_id'))->get();

            return compact('roles', 'paymentterms', 'staffs', 'accounts', 'accounttype', 'countries', 'vattype', 'customer_type', 'sale_type', 'vat', 'address_cart', 'designation', 'department', 'company');
        } catch (\Throwable $th) {
            return $th;
        }
    }

    // public function addCustomer()
    // {
    //     try {
    //         $r = SysHelper::get_data_by_role();
    //         $company_id = $r[0];
    //         $com_id = session('logged_session_data.company_id');
    //         $countries = SysCountries::all();
    //         $vattype = SysVatType::all();
    //         $vat = SysVat::select('sys_vat.*','sys_countries.name')->join('sys_countries','sys_countries.id','sys_vat.vat_country')->wherein('company_id',$company_id)->where('status',1)->get();
    //         $accounts = SysChartofAccounts::where('status',1)->wherein('company_id',$company_id)->get();
    //         $accounttype = SysAccountType::all();
    //         $roles = Role::where('active_status', '=', '1')->where('id',2)->get();
    //         $paymentterms = SysPaymentTerms::where('active_status', '=', '1')->get();

    //         $designation = SmDesignation::select('id','title')->where('active_status',1)->orderby('title','asc')->get();
    //         $department = SmHumanDepartment::select('id','name')->where('active_status',1)->orderby('name','asc')->get();

    //         $customer_type = SysCustomerType::where('status', '=', '1')->get();
    //         $sale_type = SysSaleType::where('status', '=', '1')->get();
    //         //$staffs = SmStaff::select('id','full_name')->where('active_status', '=', '1')->whereIn('designation_id', array(9,1,2,3))->get();
    //         $staffs = SysHelper::get_sales_persons2();
    //         $company = SysHelper::get_company_names();

    //         $address_cart = SysCustSupplAddressbookCart::select('sys_cust_suppl_addressbook_cart.*','sys_countries.name as c_name','sys_states.name as s_name')
    //         ->join('sys_countries','sys_countries.id','sys_cust_suppl_addressbook_cart.country')
    //         ->join('sys_states','sys_states.id','sys_cust_suppl_addressbook_cart.state')
    //         ->where('cart_id',session('logged_session_data.cart_id'))->get();

    //         return view('backEnd.cust-suppl.addCustomer', compact('roles', 'paymentterms','staffs','accounts','accounttype','countries','vattype','customer_type','sale_type','vat','address_cart','designation','department','company'));
    //     } catch (\Throwable $th) {
    //         return $th;
    //     }
    // }

    public function addCustomerStore(Request $request)
    {



        $input = $request->all();

        //return $request->address;
        //    return $request->address2;

        //return $input;
        $dom = explode("@", $request->email);
        //$check = SysCustSuppl::select('id','code','name')->where('email', $request->email)->wherenotin('email', ['x','xx','xxx','xxxx'])->first();

        if (SysHelper::check_customer_is_added($request->customer_name) > 0) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }


        try {
            DB::beginTransaction();

            $company_access = "";
            if ($request->company_access != "") {
                $company_access = implode(",", $request->company_access);
            }
            if (!in_array(1, $request->company_access)) {
                $company_access = '1,' . $company_access;
            }


            $new_customer = new SysCustSuppl();
            $new_customer->group = SysHelper::get_customer_group('group');
            $new_customer->catid = 1;  // 1 customers, 2 suppliers
            $new_customer->account_type = $request->account_type;
            $new_customer->customer_salutation = $request->customer_salutation ?: 'Mr.';
            $new_customer->first_name = $request->first_name;
            $new_customer->designation = $request->designation;
            $new_customer->last_name = $request->last_name;
            $new_customer->name = $request->customer_name;
            $new_customer->customer_name_display = $request->customer_name_display;
            $new_customer->code = SysHelper::get_new_customer_code();
            $new_customer->grn_select = $request->grn_select;
            // $new_customer->address = preg_replace('/[^A-Za-z0-9\-]/', '', $request->address);
            // $new_customer->address2 = preg_replace('/[^A-Za-z0-9\-]/', '', $request->address2);
            $new_customer->contcat_person = $request->e_first_name[0];
            $new_customer->contcat_number = $request->mobile_code;
            $new_customer->mobile = $request->mobile;
            $new_customer->email = $request->email;
            $new_customer->sales_person = Auth::user()->id;
            //$new_customer->vat_type = $request->vat_type;
            $new_customer->customer_type = $request->customer_type;
            $new_customer->sale_type = $request->sale_type;
            $new_customer->vat_country = $request->country_vat;
            $new_customer->vat_state = $request->vat_state;

            $new_customer->country_telephone = $request->country_telephone ?: null;

            $new_customer->city = $request->city;
            $new_customer->area = $request->billing_area;
            $new_customer->building_name = $request->billing_building_name;
            $new_customer->flat_office_no = $request->billing_flat_office_shop_no;
            $new_customer->zip_code = $request->zip_code;
            $new_customer->vat_percentage = $request->vat_percentage;
            $new_customer->vat_number = $request->vat_number;

            if ($request->credit_limit == "") {
                $new_customer->credit_limit = 0;
            } else {
                $new_customer->credit_limit = str_replace(',', '', $request->credit_limit);
            }
            if ($request->credit_days == "") {
                $new_customer->credit_days = 0;
            } else {
                $new_customer->credit_days = $request->credit_days;
            }
            if ($request->payment_terms == "") {
                $new_customer->payment_terms = 0;
            } else {
                $new_customer->payment_terms = $request->payment_terms;
            }
            if ($request->transaction_type == "") {
                $new_customer->transaction_type = 0;
            } else {
                $new_customer->transaction_type = $request->transaction_type;
            }
            $new_customer->payment_terms_txt = $request->payment_terms_txt;

            //$new_customer->customer_documents = $customer_documents;
            $new_customer->status = 1;
            $new_customer->internal = $request->internal;
            if ($request->vat_percentage_fixed) {
                $new_customer->vat_is_fixed = 1;
            }
            $new_customer->created_by = Auth::user()->id;
            $new_customer->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
            $new_customer->type = $request->type;
            $new_customer->company_access = $company_access;
            $new_customer->company_id = session('logged_session_data.company_id');

            $new_customer->website = $request->customer_website;
            $new_customer->maps_location = $request->maps_location;
            $new_customer->place_id = $request->place_id;

        

            $results1 = $new_customer->save();

            if ($request->filled('supplier_id')) {
                $new_customer->supplier_id = $request->supplier_id;
                $new_customer->save();
                $supplier = SysCustSuppl::find($request->supplier_id);
                if ($supplier) {
                    $supplier->customer_id = $new_customer->id;
                    $supplier->save();
                }

                // move supplier existing_doc_id to customer_documents

                if ($request->existing_doc_id) {

                


                    foreach ($request->existing_doc_id as $key => $value) {

                        $oldDoc = SysCustSupplDoc::find($value);


                        // Save NEW record
                        DB::table('sys_cust_suppl_doc')->insert([
                            'cust_suppl_id' => $new_customer->id,
                            'doc_name' => $oldDoc->doc_name,
                            'doc_file' => $oldDoc->doc_file,   // IMPORTANT: new file name
                            'doc_exp_date' => $oldDoc->doc_exp_date,
                            'status' => 1,
                            'created_by' => Auth::id(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }



            }



            $accounts = new SysChartofAccounts();
            $accounts->account_code = $new_customer->code;
            $accounts->account_name = $new_customer->name;
            $accounts->group = SysHelper::get_customer_group('group');
            $accounts->subgroup = SysHelper::get_customer_group('subgroup');
            $accounts->subgroup2 = SysHelper::get_customer_group('subgroup2');
            $accounts->status = 1;
            $accounts->company_id = session('logged_session_data.company_id');
            $accounts->company_access = $company_access;
            $accounts->grn_select = $request->grn_select;
            $accounts->created_by = Auth::user()->id;
            $accounts->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
            $accounts->internal = $request->internal;
            $results = $accounts->save();

            for ($i = 0; $i < count($request->sales_person); $i++) {
                DB::table('sys_cust_suppl_assign')->insert(
                    [
                        'cust_supp_id' => $new_customer->id,
                        'user_id' => $request->sales_person[$i],
                        'type' => 1, //1 customers, 2 suppliers
                    ]
                );
            }

            DB::table('sys_cust_suppl_addressbook')->where('cust_suppl_id', $new_customer->id)->update(['set_default' => 0]);
            DB::table('sys_cust_suppl_contact')->where('cust_suppl_id', $new_customer->id)->update(['set_default' => 0]);

            $address = new SysCustSupplAddressbook();
            $address->cust_suppl_id = $new_customer->id;
            // $address->address = $request->address;
            // $address->address2 = $request->address2;

            $address->area = $request->billing_area;
            $address->building_name = $request->billing_building_name;
            $address->flat_office_no = $request->billing_flat_office_shop_no;

            $address->city = $request->city;
            $address->country = $request->country;
            if ($request->state == "") {
                $address->state = 0;
            } else {
                $address->state = $request->state;
            }
            $address->zip_code = $request->zip_code;
            $address->set_default = 1;
            $address->company_id = session('logged_session_data.company_id');
            $address->is_shipping = 0;
            $address->status = 1;
            $address->created_by = Auth::user()->id;
            $results = $address->save();

            if ($request->same_billing_address) {
                $address = new SysCustSupplAddressbook();
                $address->cust_suppl_id = $new_customer->id;
                // $address->address = $request->address;
                // $address->address2 = $request->address2;
                $address->area = $request->billing_area;
                $address->building_name = $request->billing_building_name;
                $address->flat_office_no = $request->billing_flat_office_shop_no;
                $address->city = $request->city;
                $address->country = $request->country;
                if ($request->state == "") {
                    $address->state = 0;
                } else {
                    $address->state = $request->state;
                }
                $address->zip_code = $request->zip_code;
                $address->set_default = 1;
                $address->company_id = session('logged_session_data.company_id');
                $address->is_shipping = 1;
                $address->status = 1;
                $address->created_by = Auth::user()->id;
                $results = $address->save();
            } else {
                $address = new SysCustSupplAddressbook();
                $address->cust_suppl_id = $new_customer->id;
                // $address->address = $request->address_ship;
                // $address->address2 = $request->address2_ship;
                $address->area = $request->shipping_area;
                $address->building_name = $request->shipping_building_name;
                $address->flat_office_no = $request->shipping_flat_office_shop_no;
                $address->city = $request->city_ship;
                $address->country = $request->country_ship;
                if ($request->state_ship == "") {
                    $address->state = 0;
                } else {
                    $address->state = $request->state_ship;
                }
                $address->zip_code = $request->zip_code_ship;
                $address->set_default = 1;
                $address->company_id = session('logged_session_data.company_id');
                $address->is_shipping = 1;
                $address->status = 1;
                $address->created_by = Auth::user()->id;
                $results = $address->save();
            }

            $cart_address = SysCustSupplAddressbookCart::where('cart_id', session('logged_session_data.cart_id'))->get();
            if (count($cart_address) > 0) {
                foreach ($cart_address as $key) {
                    $address = new SysCustSupplAddressbook();
                    $address->cust_suppl_id = $new_customer->id;
                    $address->area = $key->area;
                    $address->building_name = $key->building_name;
                    $address->flat_office_no = $key->flat_office_no;
                    // $address->address = $key->address;
                    // $address->address2 = $key->address2;
                    $address->city = $key->city;
                    $address->country = $key->country;
                    $address->state = $key->state;
                    // if ($request->state == "") {
                    //     $address->state = 0;
                    // } else {
                    //     $address->state = $request->state;
                    // }
                    $address->zip_code = $key->zip_code;
                    $address->set_default = $key->set_default;
                    $address->company_id = session('logged_session_data.company_id');
                    $address->is_shipping = $key->is_shipping;
                    $address->status = 1;
                    $address->created_by = Auth::user()->id;
                    $results = $address->save();
                }
            }
            SysCustSupplAddressbookCart::where('cart_id', session('logged_session_data.cart_id'))->delete();

            $eFirstNames = (array) $request->input('e_first_name', []);
            $eEmails = (array) $request->input('e_email_address', []);
            $eWorkPhones = (array) $request->input('e_work_phone', []);
            $eMobiles = (array) $request->input('e_mobile', []);
            $eSalutations = (array) $request->input('e_salutation', []);
            $eLastNames = (array) $request->input('e_last_name', []);
            $eDesignations = (array) $request->input('e_designation', []);
            $eDepartments = (array) $request->input('e_department', []);

            foreach ($eFirstNames as $i => $firstName) {
                $firstName = trim((string) $firstName);
                $email = trim((string) ($eEmails[$i] ?? ''));
                $workPhone = trim((string) ($eWorkPhones[$i] ?? ''));
                $mobile = trim((string) ($eMobiles[$i] ?? ''));

                if ($firstName !== '' && $email !== '' && ($workPhone !== '' || $mobile !== '')) {
                    $contact = new SysCustSupplContact();
                    $contact->cust_suppl_id = $new_customer->id;
                    $contact->salutation = (string) ($eSalutations[$i] ?? '');
                    $contact->first_name = $firstName;
                    $contact->last_name = (string) ($eLastNames[$i] ?? '');
                    $contact->email_address = $email;
                    $contact->work_phone = $workPhone;
                    $contact->mobile = $mobile;
                    $contact->designation = (string) ($eDesignations[$i] ?? '');
                    $contact->department = (string) ($eDepartments[$i] ?? '');
                    $contact->status = 1;
                    $contact->set_default = 1;
                    $contact->company_id = session('logged_session_data.company_id');
                    $contact->created_by = Auth::user()->id;
                    $results = $contact->save();
                }
            }

            for ($i = 1; $i <= count($request->doc_name); $i++) {
                if ($request->file('customer_documents_' . $i) != "") {
                    $doc_exp_date = date('Y-m-d');
                    if ($i == 1) {
                        $doc_exp_date = SysHelper::normalizeToYmd($request->doc_exp_date[$i - 1]);
                        DB::table('sys_cust_suppl')->where('id', $new_customer->id)->update(['is_file' => 1]);
                    }
                    if ($i == 2) {
                        DB::table('sys_cust_suppl')->where('id', $new_customer->id)->update(['is_file' => 2]);
                    }
                    $file = $request->file('customer_documents_' . $i);
                    $company_doc = md5($file->getClientOriginalName() . time()) . "_customer_doc_" . $i . "." . $file->getClientOriginalExtension();
                    $file->move('public/uploads/cust-suppl/', $company_doc);
                    DB::table('sys_cust_suppl_doc')->insert([
                        'cust_suppl_id' => $new_customer->id,
                        'doc_name' => $request->doc_name[$i - 1],
                        'doc_file' => $company_doc,
                        'doc_exp_date' => $doc_exp_date,
                        'status' => 1,
                        'created_by' => Auth::user()->id,
                    ]);
                }
            }


            session()->forget('subgroup2');
            DB::commit();

            if ($request->btnSubmit == 'createsupplier') {
                Toastr::success('Customer Created Successfully ! Please Create Supplier', 'Success');
                 return redirect('suppliers?supplier_action=createsupplier&customer_id=' . $new_customer->id);
            }


            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($results) {
                    return ApiBaseMethod::sendResponse(null, 'New Customers has been added successfully');
                } else {
                    return ApiBaseMethod::sendError('Something went wrong, please try again');
                }
            } else {
                if ($results) {
                    Toastr::success('Operation successful', 'Success');
                    return redirect('customers/' . $new_customer->id);
                } else {
                    Toastr::error('Operation Failed', 'Failed');
                    return redirect()->back();
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    public function getCustomerEdit($id)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $com_id = session('logged_session_data.company_id');
            $countries = SysCountries::all();
            $states = SysStates::all();
            $vattype = SysVatType::all();
            $vat = SysVat::select('sys_vat.*', 'sys_countries.name')->join('sys_countries', 'sys_countries.id', 'sys_vat.vat_country')->wherein('company_id', $company_id)->where('status', 1)->get();
            $accounts = SysChartofAccounts::where('status', 1)->wherein('company_id', $company_id)->get();
            $accounttype = SysAccountType::all();
            $roles = Role::where('active_status', '=', '1')->where('id', 2)->get();
            $paymentterms = SysPaymentTerms::where('active_status', '=', '1')->get();

            $customer_type = SysCustomerType::where('status', '=', '1')->get();
            $sale_type = SysSaleType::where('status', '=', '1')->get();

            $designation = SmDesignation::select('id', 'title')->where('active_status', 1)->orderby('title', 'asc')->get();
            $department = SmHumanDepartment::select('id', 'name')->where('active_status', 1)->orderby('name', 'asc')->get();

            // $staffs = SysHelper::get_sales_persons2();

            $company = SysHelper::get_company_names();

            $editData = SysCustSuppl::where('id', $id)->first();
            $editAddressbook = SysCustSupplAddressbook::where('cust_suppl_id', $id)->get();
            $editContact = SysCustSupplContact::where('cust_suppl_id', $id)->get();
            $editDoc = SysCustSupplDoc::where('cust_suppl_id', $id)
                ->orderByRaw('CASE WHEN deleted_at IS NULL THEN 0 ELSE 1 END')
                ->orderBy('id', 'DESC')
                ->get();

            $companyIds = $editData->company_access
                ? array_filter(array_map('intval', explode(',', $editData->company_access)))
                : [];
                
            $staffs = SmStaff::select(
                'user_id',
                DB::raw("TRIM(CONCAT(first_name, ' ', COALESCE(last_name, ''))) as full_name")
            )
                ->where('active_status', 1)
            ->wherein('role_id',[5,33,30,8,32])
                ->whereIn('company_id', $companyIds)
                ->orderBy('first_name', 'asc')
                ->get();



            $editAssign = DB::table('sys_cust_suppl_assign')->where('cust_supp_id', $id)->get();

            return compact('roles', 'paymentterms', 'staffs', 'accounts', 'accounttype', 'countries', 'vattype', 'customer_type', 'sale_type', 'vat', 'editData', 'editAddressbook', 'editContact', 'editDoc', 'editAssign', 'states', 'designation', 'department', 'company');
        } catch (\Throwable $th) {
            return $th;
        }
    }

    // public function customerEdit($id)
    // {
    //     try {
    //         $r = SysHelper::get_data_by_role();
    //         $company_id = $r[0];
    //         $com_id = session('logged_session_data.company_id');
    //         $countries = SysCountries::all();
    //         $states = SysStates::all();
    //         $vattype = SysVatType::all();
    //         $vat = SysVat::select('sys_vat.*','sys_countries.name')->join('sys_countries','sys_countries.id','sys_vat.vat_country')->wherein('company_id',$company_id)->where('status',1)->get();
    //         $accounts = SysChartofAccounts::where('status',1)->wherein('company_id',$company_id)->get();
    //         $accounttype = SysAccountType::all();
    //         $roles = Role::where('active_status', '=', '1')->where('id',2)->get();
    //         $paymentterms = SysPaymentTerms::where('active_status', '=', '1')->get();

    //         $customer_type = SysCustomerType::where('status', '=', '1')->get();
    //         $sale_type = SysSaleType::where('status', '=', '1')->get();

    //         $designation = SmDesignation::select('id','title')->where('active_status',1)->orderby('title','asc')->get();
    //         $department = SmHumanDepartment::select('id','name')->where('active_status',1)->orderby('name','asc')->get();

    //         $staffs = SysHelper::get_sales_persons2();
    //         $company = SysHelper::get_company_names();

    //         $editData = SysCustSuppl::where('id',$id)->first();
    //         $editAddressbook = SysCustSupplAddressbook::where('cust_suppl_id',$id)->get();
    //         $editContact = SysCustSupplContact::where('cust_suppl_id',$id)->get();
    //         $editDoc = SysCustSupplDoc::where('cust_suppl_id',$id)->get();
    //         $editAssign = DB::table('sys_cust_suppl_assign')->where('cust_supp_id',$id)->get();

    //         return view('backEnd.cust-suppl.editCustomer', compact('roles', 'paymentterms','staffs','accounts','accounttype','countries','vattype','customer_type','sale_type','vat','editData','editAddressbook','editContact','editDoc','editAssign','states','designation','department','company'));
    //     } catch (\Throwable $th) {
    //         return $th;
    //     }
    // }
    public function customerInactive($id)
    {
        try {
            DB::beginTransaction();
            $account_code = SysCustSuppl::select('code')->where('id', $id)->first();
            SysCustSuppl::where('id', $id)->update(['status' => 2]);
            SysChartofAccounts::where('account_code', $account_code->code)->update(['status' => 2]);
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
    public function customerRestore($id)
    {
        try {
            DB::beginTransaction();
            $account_code = SysCustSuppl::select('code')->where('id', $id)->first();
            SysCustSuppl::where('id', $id)->update(['status' => 1]);
            SysChartofAccounts::where('account_code', $account_code->code)->update(['status' => 1]);
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

   public function customerUpdate(Request $request)
{
    $input = $request->all();
    $dom = explode("@", $request->email);

    try {
        DB::beginTransaction();
        $company_access = "";
        if ($request->company_access != "") {
            $company_access = implode(",", $request->company_access);
        }
        if (!in_array(1, $request->company_access)) {
            $company_access = '1,' . $company_access;
        }
        $new_customer = SysCustSuppl::find($request->cust_id);
        $new_customer->account_type = $request->account_type;
        $new_customer->customer_salutation = $request->customer_salutation;
        $new_customer->first_name = $request->first_name;
        $new_customer->designation = $request->designation;
        $new_customer->last_name = $request->last_name;
        $new_customer->grn_select = $request->grn_select;

        if (SysHelper::check_customer_is_added($request->customer_name) == 0) {
            $new_customer->name = $request->customer_name;
            $new_customer->customer_name_display = $request->customer_name_display;
            SysChartofAccounts::where('account_code', $new_customer->code)->update([
                'account_name' => $request->customer_name,
                'company_access' => $company_access,
                'internal' => $request->internal,
                'grn_select' => $request->grn_select
            ]);
        }

        // FIX 1: null-safe access on e_first_name
        $eFirstNames = $request->e_first_name ?? [];
        $new_customer->contcat_person = $eFirstNames[0] ?? null;

        $new_customer->contcat_number = $request->mobile_code;
        $new_customer->mobile = $request->mobile;
        $new_customer->email = $request->email;

        // FIX 2: null-safe access on sales_person
        $salesPersons = $request->sales_person ?? [];
        if (count($salesPersons) > 0) {
            $new_customer->sales_person = $salesPersons[0];
        }

        $new_customer->country_telephone = $request->country_telephone ?: null;

        $new_customer->customer_type = $request->customer_type;
        $new_customer->sale_type = $request->sale_type;
        $new_customer->vat_country = $request->country_vat;
        $new_customer->vat_state = $request->vat_state;
        $new_customer->city = $request->city;
        $new_customer->zip_code = $request->zip_code;
        $new_customer->vat_percentage = $request->vat_percentage;
        $new_customer->vat_number = $request->vat_number;

        if ($request->credit_limit == "") {
            $new_customer->credit_limit = 0;
        } else {
            $new_customer->credit_limit = str_replace(',', '', $request->credit_limit);
        }

        if ($request->credit_days == "") {
            $new_customer->credit_days = 0;
        } else {
            $new_customer->credit_days = $request->credit_days;
        }

        if ($request->transaction_type == "Cash") {
            $new_customer->payment_terms = $request->payment_terms_cash;
        } else {
            $new_customer->payment_terms = $request->payment_terms;
        }

        $new_customer->payment_terms_txt = $request->payment_terms_txt;
        $new_customer->transaction_type = $request->transaction_type;
        $new_customer->status = 1;
        $new_customer->internal = $request->internal;
        if ($request->vat_percentage_fixed) {
            $new_customer->vat_is_fixed = 1;
        }
        $new_customer->updated_by = Auth::user()->id;
        $new_customer->updated_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
        $new_customer->type = $request->type;
        $new_customer->company_access = $company_access;
        $new_customer->website = $request->customer_website;
        $new_customer->maps_location = $request->maps_location;
        $new_customer->created_at = $new_customer->created_at;

        $results1 = $new_customer->save();

        // FIX 3: null-safe sales_person loop
        DB::table('sys_cust_suppl_assign')->where(['cust_supp_id' => $request->cust_id])->delete();
        for ($i = 0; $i < count($salesPersons); $i++) {
            DB::table('sys_cust_suppl_assign')->insert([
                'cust_supp_id' => $request->cust_id,
                'user_id' => $salesPersons[$i],
                'type' => 1,
            ]);
        }

        DB::table('sys_cust_suppl_addressbook')->where('cust_suppl_id', $request->cust_id)->update(['set_default' => 0]);
        DB::table('sys_cust_suppl_contact')->where('cust_suppl_id', $request->cust_id)->update(['set_default' => 0]);

        // FIX 4: null-safe e_first_name loop
        SysCustSupplContact::where('cust_suppl_id', $request->cust_id)->delete();
        if (count($eFirstNames) > 0) {
            for ($i = 0; $i < count($eFirstNames); $i++) {
                if (
                    !empty($eFirstNames[$i]) &&
                    !empty($request->e_email_address[$i]) &&
                    (!empty($request->e_work_phone[$i]) || !empty($request->e_mobile[$i]))
                ) {
                    $contact = new SysCustSupplContact();
                    $contact->cust_suppl_id = $request->cust_id;
                    $contact->salutation = $request->e_salutation[$i] ?? null;
                    $contact->first_name = $request->e_first_name[$i];
                    $contact->last_name = $request->e_last_name[$i] ?? null;
                    $contact->email_address = $request->e_email_address[$i];
                    $contact->work_phone = $request->e_work_phone[$i] ?? null;
                    $contact->mobile = $request->e_mobile[$i] ?? null;
                    $contact->designation = $request->e_designation[$i] ?? null;
                    $contact->department = $request->e_department[$i] ?? null;
                    $contact->status = 1;
                    $contact->set_default = 1;
                    $contact->company_id = session('logged_session_data.company_id');
                    $contact->updated_by = Auth::user()->id;
                    $results = $contact->save();
                }
            }
        }

        // FIX 5: null-safe doc_name loop
        $docNames = $request->doc_name ?? [];
        for ($i = 1; $i <= count($docNames); $i++) {
            if ($request->file('customer_documents_' . $i) != "") {
                $doc_exp_date = date('Y-m-d');
                if ($i == 1) {
                    $doc_exp_date = SysHelper::normalizeToYmd($request->doc_exp_date[$i - 1]);
                    DB::table('sys_cust_suppl')->where('id', $new_customer->id)->update(['is_file' => 1]);
                }
                if ($i == 2) {
                    DB::table('sys_cust_suppl')->where('id', $new_customer->id)->update(['is_file' => 2]);
                }
                $file = $request->file('customer_documents_' . $i);
                $company_doc = md5($file->getClientOriginalName() . time()) . "_customer_doc_" . $i . "." . $file->getClientOriginalExtension();
                $file->move('public/uploads/cust-suppl/', $company_doc);
                DB::table('sys_cust_suppl_doc')->insert([
                    'cust_suppl_id' => $new_customer->id,
                    'doc_name' => $docNames[$i - 1],
                    'doc_file' => $company_doc,
                    'doc_exp_date' => $doc_exp_date,
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                ]);
            }
        }

        $hasDocs = SysCustSupplDoc::where('cust_suppl_id', $new_customer->id)->count();
        DB::table('sys_cust_suppl')
            ->where('id', $new_customer->id)
            ->update(['is_file' => $hasDocs > 0 ? 1 : 0]);

        session()->forget('subgroup2');

        $address = SysCustSupplAddressbook::find($request->billing_address_id);
        if (!$address) {
            $address = new SysCustSupplAddressbook();
        }
        $address->cust_suppl_id = $new_customer->id;
        $address->area = $request->billing_area;
        $address->building_name = $request->billing_building_name;
        $address->flat_office_no = $request->billing_flat_office_shop_no;
        $address->city = $request->city;
        $address->country = $request->country ?? null;
        if ($request->state == "") {
            $address->state = 0;
        } else {
            $address->state = $request->state;
        }
        $address->zip_code = $request->zip_code;
        $address->set_default = 1;
        $address->company_id = session('logged_session_data.company_id');
        $address->is_shipping = 0;
        $address->status = 1;
        $address->created_by = Auth::user()->id;
        $results = $address->save();

        $address_s = SysCustSupplAddressbook::find($request->shipping_address_id);
        if (!$address_s) {
            $address_s = new SysCustSupplAddressbook();
        }
        $address_s->cust_suppl_id = $new_customer->id;
        $address_s->area = $request->shipping_area;
        $address_s->building_name = $request->shipping_building_name;
        $address_s->flat_office_no = $request->shipping_flat_office_shop_no;
        $address_s->city = $request->city_ship;
        if ($request->country_ship == "") {
            $address_s->country = null;
        } else {
            $address_s->country = $request->country_ship;
        }
        if ($request->state_ship == "") {
            $address_s->state = 0;
        } else {
            $address_s->state = $request->state_ship;
        }
        $address_s->zip_code = $request->zip_code_ship;
        $address_s->set_default = 1;
        $address_s->company_id = session('logged_session_data.company_id');
        $address_s->is_shipping = 1;
        $address_s->status = 1;
        $address_s->created_by = Auth::user()->id;
        $results = $address_s->save();

        DB::commit();

        Toastr::success('Operation successful', 'Success');
        return redirect('customers/' . $new_customer->id);

    } catch (\Exception $e) {
        DB::rollBack();
        Toastr::error('Operation Failed', 'Failed');
        return redirect()->back()->withErrors(['error' => $e->getMessage()]);
    }
}
    //end customer update 

    public function customerUpdateDealTrack(Request $request)
    {
        try {
            DB::beginTransaction();

            $customer = SysCustSuppl::findOrFail($request->cust_id);



            if ($request->filled('first_name')) {

                $fullName = trim($request->first_name);
                $parts = preg_split('/\s+/', $fullName);

                if (count($parts) == 1) {

                    // Only one word → update ONLY first_name
                    $customer->first_name = $parts[0];
                    // DO NOT update $customer->last_name

                } elseif (count($parts) == 2) {

                    // Two words
                    $customer->first_name = $parts[0];
                    $customer->last_name = $parts[1];

                } else {

                    // Three or more words
                    $customer->first_name = array_shift($parts);
                    $customer->last_name = implode(' ', $parts);
                }
            }




            if ($request->filled('mobile_code'))
                $customer->contcat_number = $request->mobile_code;

            if ($request->filled('mobile'))
                $customer->mobile = $request->mobile;

            if ($request->filled('email'))
                $customer->email = $request->email;

            if ($request->filled('customer_type'))
                $customer->customer_type = $request->customer_type;

            if ($request->filled('vat_number'))
                $customer->vat_number = $request->vat_number;

            $customer->updated_by = Auth::id();
            $customer->updated_at = now('+04:00');
            $customer->save();


            // Process documents only if they exist
            if ($request->has('doc_name')) {
                foreach ($request->doc_name as $index => $docName) {
                    $i = $index + 1;

                    if ($request->hasFile("customer_documents_$i")) {

                        $file = $request->file("customer_documents_$i");

                        $fileName = md5($file->getClientOriginalName() . time())
                            . "_customer_doc_$i."
                            . $file->getClientOriginalExtension();

                        $file->move('public/uploads/cust-suppl/', $fileName);

                        // Expiry date
                        $expDate = ($i == 1 && isset($request->doc_exp_date[$index]))
                            ? SysHelper::normalizeToYmd($request->doc_exp_date[$index])
                            : date('Y-m-d');

                        SysCustSupplDoc::create([
                            'cust_suppl_id' => $customer->id,
                            'doc_name' => $docName,
                            'doc_file' => $fileName,
                            'doc_exp_date' => $expDate,
                            'status' => 1,
                            'created_by' => Auth::id(),
                        ]);


                    }
                }
            }

            $hasDocs = SysCustSupplDoc::where('cust_suppl_id', $customer->id)->count();

            DB::table('sys_cust_suppl')
                ->where('id', $customer->id)
                ->update(['is_file' => $hasDocs > 0 ? 1 : 0]);

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Customer updated successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }


    function add_customer_script(Request $request)
    {
        try {
            $address = new SysCustSupplAddressbookCart();
            $address->cart_id = session('logged_session_data.cart_id');
            $address->cust_suppl_id = 0;
            // $address->address = $request->address;
            // $address->address2 = $request->address2;
            $address->area = $request->area;
            $address->building_name = $request->building_name;
            $address->flat_office_no = $request->flat_office_shop_no;
            $address->city = $request->city;
            $address->country = $request->country;
            $address->state = $request->state;
            $address->zip_code = $request->zip_code;
            $address->set_default = $request->set_default;
            $address->company_id = session('logged_session_data.company_id');
            $address->is_shipping = $request->address_type;
            $address->status = 1;
            $address->created_by = Auth::user()->id;
            $results = $address->save();

            $ret = SysCustSupplAddressbookCart::select('sys_cust_suppl_addressbook_cart.*', 'sys_countries.name as c_name', 'sys_states.name as s_name')
                ->join('sys_countries', 'sys_countries.id', 'sys_cust_suppl_addressbook_cart.country')
                ->join('sys_states', 'sys_states.id', 'sys_cust_suppl_addressbook_cart.state')
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

    function delete_customer_script(Request $request)
    {
        try {

            SysCustSupplAddressbookCart::where('id', $request->id)->delete();

            $ret = SysCustSupplAddressbookCart::select('sys_cust_suppl_addressbook_cart.*', 'sys_countries.name as c_name', 'sys_states.name as s_name')
                ->join('sys_countries', 'sys_countries.id', 'sys_cust_suppl_addressbook_cart.country')
                ->join('sys_states', 'sys_states.id', 'sys_cust_suppl_addressbook_cart.state')
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

    // public function delete_cust_suppl_doc($id)
    // {
    //     try {
    //        $doc = DB::table('sys_cust_suppl_doc')->where('id', $id)->first();

    //     if ($doc) {

    //         $custSupplId = $doc->cust_suppl_id;  


    //         DB::table('sys_cust_suppl_doc')->where('id', $id)->delete();

    //           $hasDocs = SysCustSupplDoc::where('cust_suppl_id', $custSupplId)->count();

    //         DB::table('sys_cust_suppl')
    //         ->where('id', $custSupplId)
    //         ->update(['is_file' => $hasDocs > 0 ? 1 : 0]);
    //     }

    //         Toastr::success('Deleted Successfully', 'Success');
    //         return redirect()->back();
    //     } catch (\Exception $e) {
    //         return $e;
    //         Toastr::error('Operation Failed', 'Failed');
    //         return redirect()->back();
    //     }
    // }



    public function delete_cust_suppl_doc($id)
    {
        try {

            $doc = SysCustSupplDoc::where('id', $id)
                ->whereNull('deleted_at')   // Only active docs
                ->first();

            if ($doc) {

                $custSupplId = $doc->cust_suppl_id;

                // Soft Delete (NOT HARD DELETE)
                SysCustSupplDoc::where('id', $id)->update([
                    'deleted_by' => auth()->id(),
                    'deleted_at' => now()
                ]);

                // Check if any active docs remain
                $hasDocs = SysCustSupplDoc::where('cust_suppl_id', $custSupplId)
                    ->whereNull('deleted_at')
                    ->count();

                DB::table('sys_cust_suppl')
                    ->where('id', $custSupplId)
                    ->update([
                        'is_file' => $hasDocs > 0 ? 1 : 0
                    ]);
            }

            Toastr::success('Deleted Successfully', 'Success');
            return redirect()->back();

        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function restore_cust_suppl_doc($id)
    {
        try {

            // Fetch only DELETED documents
            $doc = SysCustSupplDoc::where('id', $id)
                ->whereNotNull('deleted_at')
                ->first();

            if ($doc) {

                $custSupplId = $doc->cust_suppl_id;

                // Restore the soft-deleted document
                SysCustSupplDoc::where('id', $id)->update([
                    'deleted_by' => null,
                    'deleted_at' => null
                ]);

                // Check if any active docs exist for the supplier
                $hasDocs = SysCustSupplDoc::where('cust_suppl_id', $custSupplId)
                    ->whereNull('deleted_at')
                    ->count();

                // Update parent is_file flag
                DB::table('sys_cust_suppl')
                    ->where('id', $custSupplId)
                    ->update([
                        'is_file' => $hasDocs > 0 ? 1 : 0
                    ]);
            }

            Toastr::success('Document Restored Successfully', 'Success');
            return redirect()->back();

        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }



    public function delete_cust_suppl_address($id)
    {
        try {
            DB::table('sys_cust_suppl_addressbook')->where('id', $id)->delete();
            Toastr::success('Deleted Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    function add_cust_suppl_address(Request $request)
    {
        try {
            $address = new SysCustSupplAddressbook();
            $address->cust_suppl_id = $request->cust_suppl_id;
            $address->area = $request->area_n;
            $address->building_name = $request->building_name_n;
            $address->flat_office_no = $request->flat_office_shop_no_n;
            // $address->address = $request->address_n;
            // $address->address2 = $request->address2_n;
            $address->city = $request->city_n;
            $address->country = $request->country_n;
            $address->state = $request->state_n;
            $address->zip_code = $request->zip_code_n;
            $address->set_default = $request->set_default_n;
            $address->company_id = session('logged_session_data.company_id');
            $address->is_shipping = $request->address_type_n;
            $address->status = 1;
            $address->created_by = Auth::user()->id;
            $results = $address->save();

            if ($request->address_type_n == 0 && $request->set_default_n == 1) {
                DB::table('sys_cust_suppl')->where('id', $request->cust_suppl_id)->update([
                    'address' => $request->address_n,
                    'address2' => $request->address2_n,
                ]);
            }

            Toastr::success('Added Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    function update_cust_suppl_address(Request $request)
    {
        //return $request->all();
        try {
            $address = SysCustSupplAddressbook::find($request->cust_suppl_edit_id);
            $address->area = $request->area_n_e;
            $address->building_name = $request->building_name_n_e;
            $address->flat_office_no = $request->flat_office_shop_no_n_e;
            // $address->address = $request->address_n_e;
            // $address->address2 = $request->address2_n_e;
            $address->city = $request->city_n_e;
            $address->country = $request->country_n_e;
            $address->state = $request->state_n_e;
            $address->zip_code = $request->zip_code_n_e;
            $address->set_default = $request->set_default_n_e;
            $address->company_id = session('logged_session_data.company_id');
            $address->is_shipping = $request->address_type_n_e;
            $address->status = 1;
            $address->updated_by = Auth::user()->id;
            $results = $address->save();

            if ($request->address_type_n_e == 0 && $request->set_default_n_e == 1) {
                DB::table('sys_cust_suppl')->where('id', $request->cust_suppl_edit)->update([
                    'address' => $request->address_n_e,
                    'address2' => $request->address2_n_e,
                ]);
            }

            //return $address->address.", ".$address->address2.", ".$address->city.", ".$address->zip_code;

            $address_new = SysCustSupplAddressbook::find($request->cust_suppl_edit_id);
            if ($request->address_type_n_e == 0) {
                SysCrmDeals::where('cust_id', $request->cust_suppl_edit)->update([
                    'delivery_address1' => $address_new->address,
                    'delivery_address2' => $address_new->address2,
                    'delivery_city' => $address_new->city,
                    'delivery_zip_code' => $address_new->zip_code,
                    'delivery_country' => $address_new->country,
                    'delivery_state' => $address_new->state,
                    'address' => $address_new->address . ", " . $address_new->address2 . ", " . $address_new->city . ", " . $address_new->countryname->name . ", " . $address_new->statename->name . ", PB No: " . $address_new->zip_code,
                ]);
            }
            if ($request->address_type_n_e == 1) {
                SysCrmDeals::where('cust_id', $request->cust_suppl_edit)->update([
                    'address' => $address_new->address . ", " . $address_new->address2 . ", " . $address_new->city . ", " . $address_new->countryname->name . ", " . $address_new->statename->name . ", PB No: " . $address_new->zip_code,
                ]);
            }
            if ($request->address_type_n_e == 2) {
                SysCrmDeals::where('cust_id', $request->cust_suppl_edit)->update([
                    'delivery_address1' => $address_new->address,
                    'delivery_address2' => $address_new->address2,
                    'delivery_city' => $address_new->city,
                    'delivery_zip_code' => $address_new->zip_code,
                    'delivery_country' => $address_new->country,
                    'delivery_state' => $address_new->state,
                ]);
            }

            Toastr::success('Updated Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }



    public function deleteSCustomer($id)
    {
        // try{
        //     $staffs = SmStaff::find($id);
        //     $staffs->active_status = 0;
        //     $result = $staffs->update();
        //     if ($result) {
        //         $users = User::find($staffs->user_id);
        //         $users->active_status = 0;
        //         $results = $users->update();
        //     }
        //     Toastr::success('Operation successful', 'Success');
        //     return redirect()->back();
        // }catch (\Exception $e) {
        //    Toastr::error('Operation Failed', 'Failed');
        //    return redirect()->back(); 
        // }
    }

    public function loginAccessPermission(Request $request)
    {

        // if($request->status == 'on'){
        //     $status = 1;
        // }else{
        //     $status = 0;
        // }

        // $staff = SmStaff::find($request->id);
        // $user_id = $staff->user_id;
        // $staff->active_status = $status;
        // $staff->save();
        // $user = User::find($user_id);
        // $user->access_status = $status;
        // $user->save();
        // return response()->json($request->id);
    }

    // import

    public function customer_import(Request $request)
    {
        try {
            $data = DB::table('sys_cust_suppl_import as i')->select('i.*')->where('i.status', 1)->where('i.company_id', session('logged_session_data.company_id'))->get();

            $customer = DB::table('sys_cust_suppl')->select('name')->where('company_id', session('logged_session_data.company_id'))->where('catid', 1)->get(); // cat - 1 customers, 2 suppliers
            //$sales_person = DB::table('sm_staffs')->select('user_id','first_name')->where('company_id',session('logged_session_data.company_id'))->get();
            $sales_person = SysHelper::get_sales_persons2();
            $paymentterms = SysPaymentTerms::where('active_status', '=', '1')->get();

            $customer_type = SysCustomerType::where('status', '=', '1')->get();
            $sale_type = SysSaleType::where('status', '=', '1')->get();
            $country = SysCountries::get();
            $state = SysStates::get();

            return view('backEnd.cust-suppl.importcustomer', compact('data', 'customer', 'sales_person', 'paymentterms', 'customer_type', 'sale_type', 'country', 'state'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function customer_import_list(Request $request)
    {
        try {
            DB::beginTransaction();
            $selected_file = "";
            if ($request->file('import_file') != "") {
                $file = $request->file('import_file');
                $selected_file = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('public/uploads/product_upload/', $selected_file);
                $selected_file = 'public/uploads/product_upload/' . $selected_file;
                //return  $selected_file;
            }

            $objPHPExcel = PHPExcel_IOFactory::load($selected_file);
            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $objWorksheet->getHighestRow();

            $dataArray = $objPHPExcel->getActiveSheet()->toArray();

            for ($i = 1; $i < count($dataArray); $i++) {

                //for($j=0; $j < count($dataArray[0]); $j++){
                $data[] = [
                    $dataArray[0][0] => $dataArray[$i][0],
                    $dataArray[0][1] => $dataArray[$i][1],
                    $dataArray[0][2] => $dataArray[$i][2],
                    $dataArray[0][3] => $dataArray[$i][3],
                    $dataArray[0][4] => $dataArray[$i][4],
                    $dataArray[0][5] => $dataArray[$i][5],
                    $dataArray[0][6] => $dataArray[$i][6],
                    $dataArray[0][7] => $dataArray[$i][7],
                    $dataArray[0][8] => $dataArray[$i][8],
                    $dataArray[0][9] => $dataArray[$i][9],
                    $dataArray[0][10] => $dataArray[$i][10],
                    $dataArray[0][11] => $dataArray[$i][11],
                    $dataArray[0][12] => $dataArray[$i][12],
                    $dataArray[0][13] => $dataArray[$i][13],
                    $dataArray[0][14] => $dataArray[$i][14],
                    $dataArray[0][15] => $dataArray[$i][15],
                    $dataArray[0][16] => $dataArray[$i][16],
                    $dataArray[0][17] => $dataArray[$i][17],
                    $dataArray[0][18] => $dataArray[$i][18],
                    $dataArray[0][19] => $dataArray[$i][19],
                    $dataArray[0][20] => $dataArray[$i][20],
                    $dataArray[0][21] => $dataArray[$i][21],
                    $dataArray[0][22] => $dataArray[$i][22],
                    $dataArray[0][23] => $dataArray[$i][23],
                    $dataArray[0][24] => $dataArray[$i][24],
                    $dataArray[0][25] => $dataArray[$i][25],
                    $dataArray[0][26] => $dataArray[$i][26],
                    $dataArray[0][27] => $dataArray[$i][27],
                    $dataArray[0][28] => $dataArray[$i][28],
                    $dataArray[0][29] => $dataArray[$i][29],
                    'created_by' => Auth::user()->id,
                    'company_id' => session('logged_session_data.company_id'),
                ];
                //}
                //$data2[]=$data;

            }

            foreach (array_chunk($data, 1000) as $dt) {
                SysCustSupplImport::insert($dt);
            }

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

    public function customer_import_clear(Request $request)
    {
        try {
            SysCustSupplImport::where('company_id', session('logged_session_data.company_id'))->delete();
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function customer_import_data(Request $request)
    {
        try {
            DB::beginTransaction();
            $customer = DB::table('sys_cust_suppl')->where('company_id', session('logged_session_data.company_id'))->where('catid', 1)->pluck('name');

            $sales_person = SysHelper::get_sales_persons2();
            $paymentterms = SysPaymentTerms::where('active_status', '=', '1')->get();

            $customer_type = SysCustomerType::where('status', '=', '1')->get();
            $sale_type = SysSaleType::where('status', '=', '1')->get();
            $country = SysCountries::get();
            $state = SysStates::get();

            $data = DB::table('sys_cust_suppl_import as i')->select('i.*')->where('i.status', 1)->where('i.company_id', session('logged_session_data.company_id'))
                ->whereNotIn('i.name', $customer)->get();

            $group = SysHelper::get_customer_group('group');
            $subgroup = SysHelper::get_customer_group('subgroup');
            $subgroup2 = SysHelper::get_customer_group('subgroup2');

            if (count($data) > 0) {
                foreach ($data as $dt) {

                    $sales_person_id = $sales_person->where('full_name', $dt->sales_person)->max('user_id');
                    $customer_type_id = $customer_type->where('title', $dt->customer_type)->max('id');
                    $sale_type_id = $sale_type->where('title', $dt->sale_type)->max('id');
                    $payment_terms_id = $paymentterms->where('title', $dt->payment_terms)->max('id');
                    $country_id = $country->where('name', $dt->country)->max('id');
                    $state_id = $state->where('name', $dt->state)->max('id');
                    $vat_country = $country->where('name', $dt->vat_country)->max('id');

                    if ($dt->account_type == "Reseller") {
                        $account_type = 1;
                    } elseif ($dt->account_type == "Enduser") {
                        $account_type = 2;
                    } elseif ($dt->account_type == "Ecommerce") {
                        $account_type = 3;
                    } else {
                        $account_type = 1;
                    }

                    $new_customer = new SysCustSuppl();
                    $new_customer->group = $group;
                    $new_customer->catid = 1;  // 1 customers, 2 suppliers
                    $new_customer->account_type = $account_type;
                    $new_customer->customer_salutation = $dt->customer_salutation;
                    $new_customer->first_name = $dt->first_name;
                    $new_customer->designation = $dt->designation;
                    $new_customer->last_name = $dt->last_name;
                    $new_customer->name = $dt->name;
                    $new_customer->customer_name_display = $dt->customer_name_display;
                    $new_customer->code = SysHelper::get_new_customer_code();
                    $new_customer->address = $dt->address;
                    $new_customer->address2 = $dt->address2;
                    $new_customer->contcat_person = $dt->contcat_person_first_name . ' ' . $dt->contcat_person_last_name;
                    $new_customer->contcat_number = $dt->contcat_number;
                    $new_customer->mobile = $dt->mobile;
                    $new_customer->email = $dt->email;
                    $new_customer->sales_person = $sales_person_id;
                    $new_customer->customer_type = $customer_type_id;
                    $new_customer->sale_type = $sale_type_id;
                    $new_customer->vat_country = $vat_country;
                    $new_customer->city = $dt->city;
                    $new_customer->zip_code = $dt->zip_code;
                    $new_customer->vat_percentage = $dt->vat_percentage;
                    $new_customer->vat_number = $dt->vat_number;
                    $new_customer->credit_limit = str_replace(',', '', $dt->credit_limit);
                    $new_customer->credit_days = $dt->credit_days;
                    $new_customer->payment_terms = $payment_terms_id;
                    $new_customer->transaction_type = $dt->transaction_type;
                    //$new_customer->customer_documents = $customer_documents;
                    $new_customer->status = 1;
                    $new_customer->vat_is_fixed = $dt->vat_is_fixed;
                    $new_customer->created_by = Auth::user()->id;
                    $new_customer->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
                    //$new_customer->created_at = '';
                    $new_customer->type = 1;
                    $new_customer->company_id = $dt->company_id;
                    $new_customer->company_access = '1,' . $dt->company_id;
                    $results1 = $new_customer->save();

                    $accounts = new SysChartofAccounts();
                    $accounts->account_code = $new_customer->code;
                    $accounts->account_name = $new_customer->name;
                    $accounts->group = $group;
                    $accounts->subgroup = $subgroup;
                    $accounts->subgroup2 = $subgroup2;
                    $accounts->status = 1;
                    $accounts->company_id = $dt->company_id;
                    $accounts->company_access = '1,' . $dt->company_id;
                    $accounts->created_by = Auth::user()->id;
                    $accounts->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
                    $results = $accounts->save();

                    $address = new SysCustSupplAddressbook();
                    $address->cust_suppl_id = $new_customer->id;
                    $address->address = $dt->address;
                    $address->address2 = $dt->address2;
                    $address->city = $dt->city;
                    $address->country = $country_id;
                    $address->state = $state_id;
                    $address->zip_code = $dt->zip_code;
                    $address->set_default = 1;
                    $address->company_id = $dt->company_id;
                    $address->is_shipping = 1;
                    $address->status = 1;
                    $address->created_by = Auth::user()->id;
                    $results = $address->save();

                    $contact = new SysCustSupplContact();
                    $contact->cust_suppl_id = $new_customer->id;
                    $contact->salutation = $dt->contcat_person_salutation;
                    $contact->first_name = $dt->contcat_person_first_name;
                    $contact->last_name = $dt->contcat_person_last_name;
                    $contact->email_address = $dt->email;
                    $contact->work_phone = $dt->contcat_number;
                    $contact->mobile = $dt->mobile;
                    $contact->designation = $dt->designation;
                    //$contact->department = $dt->e_department[$i];
                    $contact->status = 1;
                    $contact->set_default = 1;
                    $contact->company_id = $dt->company_id;
                    $contact->created_by = Auth::user()->id;
                    $results = $contact->save();
                }

                SysCustSupplImport::where('company_id', session('logged_session_data.company_id'))->delete();
                DB::commit();
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
            Toastr::success('Customer Imported Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    // import

    public function customer_from_list(Request $request, $id = null)
    {
        $r = SysHelper::get_data_by_role();
        $company_id = $r[0];
        $staff = SysHelper::get_sales_persons2();
        $countries = SysCountries::all();
        $states = SysStates::all();
        try {
            //if($_POST){
            if (SysHelper::get_pagination_post($request)) {
                $customer_query = SysCustSupplForm::with('createdby')->wherein('company_id', $company_id)->where('status', 1)->where('catid', 1);
                if ($request->company_name != "") {
                    $customer_query->where('name', 'like', '%' . $request->company_name . '%');
                }
                if ($request->contact_name != "") {
                    $customer_query->where('contcat_person', 'like', '%' . $request->contact_name . '%');
                }
                if ($request->email != "") {
                    $customer_query->where('email', 'like', '%' . $request->email . '%');
                }
                if ($request->vat_country != "") {
                    $customer_query->where('vat_country', $request->vat_country);
                }
                if ($request->vat_state != "") {
                    $customer_query->where('vat_state', $request->vat_state);
                }
            } else {
                $customer_query = SysCustSupplForm::with('createdby')->wherein('company_id', $company_id)->where('status', 1)->where('catid', 1);
            }
            //$customer_query->wherein('r.created_by',$r[1]);



            $customer = $customer_query->paginate(30);




            $active_id = $id;
            $selectedCus = [];
            $addCustomer = [];
            $action = false;
            $editData = [];

            if ($request->has('customerform_action')) {
                $poAction = $request->input('customerform_action');

                if ($poAction === 'add') {
                    $action = 'add';
                    $addCustomer = $this->getAddCustomer(); // Get data for adding supplier
                } elseif ($poAction === 'edit') {
                    $action = 'edit';
                    $editData = $this->customer_form_edit($active_id); // Get all data for editing

                }
            } else {
                if ($id) {
                    $selectedCus = $this->getViewCustomerForm($id);
                } else {
                    $firstRecord = $customer->first();
                    if ($firstRecord) {
                        $active_id = $firstRecord->id;
                        $selectedCus = $this->getViewCustomerForm($firstRecord->id);
                    }
                }
            }



            return view('backEnd.cust-suppl.customer_list_form', compact('customer', 'staff', 'countries', 'states', 'active_id', 'selectedCus', 'addCustomer', 'editData', 'action'));

            /*$form_data = [
                'customer' => $customer,
                'staff' => $staff,
                'countries' => $countries,
                'states' => $states,
            ];
            session()->put('customer_list_query', $form_data);
            return redirect('customer');*/

            //return view('backEnd.cust-suppl.customer_list', compact('customer','staff','countries','states'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function customer_form_edit($id)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $com_id = session('logged_session_data.company_id');
            $countries = SysCountries::all();
            $states = SysStates::all();
            $vattype = SysVatType::all();
            $vat = SysVat::select('sys_vat.*', 'sys_countries.name')->join('sys_countries', 'sys_countries.id', 'sys_vat.vat_country')->wherein('company_id', $company_id)->where('status', 1)->get();
            $accounts = SysChartofAccounts::where('status', 1)->wherein('company_id', $company_id)->get();
            $accounttype = SysAccountType::all();
            $roles = Role::where('active_status', '=', '1')->where('id', 2)->get();
            $paymentterms = SysPaymentTerms::where('active_status', '=', '1')->get();

            $customer_type = SysCustomerType::where('status', '=', '1')->get();
            $sale_type = SysSaleType::where('status', '=', '1')->get();

            $staffs = SysHelper::get_sales_persons2();
            $company = SysHelper::get_company_names();

            $designation = SmDesignation::select('id', 'title')->where('active_status', 1)->orderby('title', 'asc')->get();
            $department = SmHumanDepartment::select('id', 'name')->where('active_status', 1)->orderby('name', 'asc')->get();

            $editData = SysCustSupplForm::where('id', $id)->first();
            $editAddressbook = SysCustSupplAddressbookForm::where('cust_suppl_id', $id)->get();
            $editContact = SysCustSupplContactForm::where('cust_suppl_id', $id)->get();
            $editDoc = SysCustSupplDocForm::where('cust_suppl_id', $id)->get();
            $editAssign = DB::table('sys_cust_suppl_assign')->where('cust_supp_id', $id)->get();
            $row_id = $id;

            $a = $editData->name;
            $b = $editData->email;
            $excisting_list = SysCustSuppl::select('id', 'code', 'name', 'email', 'first_name', 'mobile', 'contcat_number')->where(function ($query) use ($a, $b) {
                $query->where('name', '=', $a)
                    ->orWhere('email', '=', $b);
            })->where('catid', 1)->get();

            return compact('roles', 'paymentterms', 'staffs', 'accounts', 'accounttype', 'countries', 'vattype', 'customer_type', 'sale_type', 'vat', 'editData', 'editAddressbook', 'editContact', 'editDoc', 'editAssign', 'row_id', 'excisting_list', 'states', 'designation', 'department', 'company');
            // return view('backEnd.cust-suppl.editCustomerForm', compact('roles', 'paymentterms', 'staffs', 'accounts', 'accounttype', 'countries', 'vattype', 'customer_type', 'sale_type', 'vat', 'editData', 'editAddressbook', 'editContact', 'editDoc', 'editAssign', 'row_id', 'excisting_list', 'states', 'designation', 'department', 'company'));
        } catch (\Throwable $th) {
            return $th;
        }
    }

    function customer_form_address_update(Request $request)
    {
        try {
            $address = SysCustSupplAddressbookForm::find($request->cust_suppl_edit_id);
            $address->address = $request->address_n_e;
            $address->address2 = $request->address2_n_e;
            $address->city = $request->city_n_e;
            $address->country = $request->country_n_e;
            $address->state = $request->state_n_e;
            $address->zip_code = $request->zip_code_n_e;
            $address->set_default = $request->set_default_n_e;
            $address->company_id = session('logged_session_data.company_id');
            $address->is_shipping = $request->address_type_n_e;
            $address->status = 1;
            $address->updated_by = Auth::user()->id;
            $results = $address->save();

            if ($request->address_type_n_e == 0 && $request->set_default_n_e == 1) {
                DB::table('sys_cust_suppl_form')->where('id', $request->cust_suppl_edit)->update([
                    'address' => $request->address_n_e,
                    'address2' => $request->address2_n_e,
                ]);
            }

            Toastr::success('Updated Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function customer_form_approve(Request $request)
    {
        $input = $request->all();

        //return $input;
        $dom = explode("@", $request->email);
        //$check = SysCustSuppl::select('id','code','name')->where('email', $request->email)->wherenotin('email', ['x','xx','xxx','xxxx'])->first();
        //$check = SysCustSuppl::select('id','code','name')->where('name', $request->customer_name)->where('catid',1)->first();
        //$check2 = SysCustSuppl::select('id','code','name')->where('name', $request->email)->where('catid',1)->first();
        if (SysHelper::check_customer_is_added($request->customer_name) > 0) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }

        try {
            $company_access = "";
            if ($request->company_access != "") {
                $company_access = implode(",", $request->company_access);
            }
            if (!in_array(1, $request->company_access)) {
                $company_access = '1,' . $company_access;
            }

            DB::beginTransaction();
            $new_customer = new SysCustSuppl();
            $new_customer->group = SysHelper::get_customer_group('group');
            $new_customer->catid = 1;  // 1 customers, 2 suppliers
            $new_customer->account_type = $request->account_type;
            $new_customer->customer_salutation = $request->customer_salutation;
            $new_customer->first_name = $request->first_name;
            $new_customer->designation = $request->designation;
            $new_customer->last_name = $request->last_name;
            $new_customer->name = $request->customer_name;
            $new_customer->customer_name_display = $request->customer_name_display;
            $new_customer->code = SysHelper::get_new_customer_code();
            $new_customer->address = $request->address;
            $new_customer->address2 = $request->address2;
            $new_customer->contcat_person = $request->e_first_name[0];
            $new_customer->contcat_number = $request->mobile_code;
            $new_customer->mobile = $request->mobile;
            $new_customer->email = $request->email;
            $new_customer->sales_person = Auth::user()->id;
            //$new_customer->vat_type = $request->vat_type;
            $new_customer->customer_type = $request->customer_type;
            $new_customer->sale_type = $request->sale_type;
            $new_customer->vat_country = $request->country_vat;
            //$new_customer->vat_state = $request->state_vat;
            $new_customer->city = $request->city;
            $new_customer->zip_code = $request->zip_code;
            $new_customer->vat_percentage = $request->vat_percentage;
            $new_customer->vat_number = $request->vat_number;
            $new_customer->credit_limit = str_replace(',', '', $request->credit_limit);
            $new_customer->credit_days = $request->credit_days;
            $new_customer->payment_terms = $request->payment_terms;
            $new_customer->transaction_type = $request->transaction_type;
            //$new_customer->customer_documents = $customer_documents;
            $new_customer->status = 1;
            if ($request->vat_percentage_fixed) {
                $new_customer->vat_is_fixed = 1;
            }
            $new_customer->created_by = Auth::user()->id;
            $new_customer->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
            //$new_customer->created_at = '';
            $new_customer->type = $request->type;
            $new_customer->company_id = session('logged_session_data.company_id');
            $new_customer->company_access = $company_access;
            $results1 = $new_customer->save();

            $accounts = new SysChartofAccounts();
            $accounts->account_code = $new_customer->code;
            $accounts->account_name = $request->customer_name;
            $accounts->group = SysHelper::get_customer_group('group');
            $accounts->subgroup = SysHelper::get_customer_group('subgroup');
            $accounts->subgroup2 = SysHelper::get_customer_group('subgroup2');
            $accounts->status = 1;
            $accounts->company_id = session('logged_session_data.company_id');
            $accounts->company_access = $company_access;
            $accounts->created_by = Auth::user()->id;
            $accounts->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
            $results = $accounts->save();

            for ($i = 0; $i < count($request->sales_person); $i++) {
                DB::table('sys_cust_suppl_assign')->insert(
                    [
                        'cust_supp_id' => $new_customer->id,
                        'user_id' => $request->sales_person[$i],
                        'type' => 1, //1 customers, 2 suppliers
                    ]
                );
            }

            $cart_address = SysCustSupplAddressbookForm::where('cust_suppl_id', $request->row_id)->get();
            if (count($cart_address) > 0) {
                foreach ($cart_address as $key) {
                    $address = new SysCustSupplAddressbook();
                    $address->cust_suppl_id = $new_customer->id;
                    $address->address = $key->address;
                    $address->address2 = $key->address2;
                    $address->city = $key->city;
                    $address->country = $key->country;
                    $address->state = $key->state;
                    $address->zip_code = $key->zip_code;
                    $address->set_default = $key->set_default;
                    $address->company_id = session('logged_session_data.company_id');
                    $address->is_shipping = $key->is_shipping;
                    $address->status = 1;
                    $address->created_by = Auth::user()->id;
                    $results = $address->save();
                }
            }

            for ($i = 0; $i < count($request->e_first_name); $i++) {
                if ($request->e_first_name[$i] != "" && $request->e_email_address[$i] != "" && ($request->e_work_phone[$i] != "" || $request->e_mobile[$i] != "")) {
                    $contact = new SysCustSupplContact();
                    $contact->cust_suppl_id = $new_customer->id;
                    $contact->salutation = $request->e_salutation[$i];
                    $contact->first_name = $request->e_first_name[$i];
                    $contact->last_name = $request->e_last_name[$i];
                    $contact->email_address = $request->e_email_address[$i];
                    $contact->work_phone = $request->e_work_phone[$i];
                    $contact->mobile = $request->e_mobile[$i];
                    $contact->designation = $request->e_designation[$i];
                    $contact->department = $request->e_department[$i];
                    $contact->status = 1;
                    $contact->set_default = 1;
                    $contact->company_id = session('logged_session_data.company_id');
                    $contact->created_by = Auth::user()->id;
                    $results = $contact->save();
                }
            }

            $cart_doc = SysCustSupplDocForm::where('cust_suppl_id', $request->row_id)->get();
            if (count($cart_doc) > 0) {
                foreach ($cart_doc as $key) {
                    DB::table('sys_cust_suppl_doc')->insert([
                        'cust_suppl_id' => $new_customer->id,
                        'doc_name' => $key->doc_name,
                        'doc_file' => $key->doc_file,
                        'doc_exp_date' => $key->doc_exp_date,
                        'status' => 1,
                        'created_by' => Auth::user()->id,
                    ]);
                }
            }



            SysCustSupplAddressbookForm::where('cust_suppl_id', $request->row_id)->delete();
            SysCustSupplContactForm::where('cust_suppl_id', $request->row_id)->delete();
            SysCustSupplDocForm::where('cust_suppl_id', $request->row_id)->delete();
            SysCustSupplForm::where('id', $request->row_id)->delete();


            session()->forget('subgroup2');
            DB::commit();

            if ($results) {
                Toastr::success('Operation successful', 'Success');
                return redirect('view-customer/' . $new_customer->id);
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function customer_form_delete($id)
    {
        try {
            SysCustSupplAddressbookForm::where('cust_suppl_id', $id)->delete();
            SysCustSupplContactForm::where('cust_suppl_id', $id)->delete();
            SysCustSupplDocForm::where('cust_suppl_id', $id)->delete();
            SysCustSupplForm::where('id', $id)->delete();
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function customer_form_merge($cust_id, $sub_id)
    {
        try {

            DB::beginTransaction();

            $subdata = SysCustSupplForm::where('id', $sub_id)->first();
            $subdata_address = SysCustSupplAddressbookForm::where('cust_suppl_id', $sub_id)->get();
            $subdata_contact = SysCustSupplContactForm::where('cust_suppl_id', $sub_id)->get();
            $subdata_doc = SysCustSupplDocForm::where('cust_suppl_id', $sub_id)->get();


            DB::table('sys_cust_suppl_addressbook')->where('cust_suppl_id', $cust_id)->update(['set_default' => 0]);
            DB::table('sys_cust_suppl_contact')->where('cust_suppl_id', $cust_id)->update(['set_default' => 0]);

            $new_customer = SysCustSuppl::find($cust_id);
            $new_customer->customer_salutation = $subdata->customer_salutation;
            $new_customer->first_name = $subdata->first_name;
            $new_customer->designation = $subdata->designation;
            $new_customer->last_name = $subdata->last_name;
            $new_customer->name = $subdata->name;
            $new_customer->customer_name_display = $subdata->customer_name_display;
            if (count($subdata_contact) > 0) {
                $new_customer->contcat_person = $subdata_contact[0]->first_name;
            }
            $new_customer->contcat_number = $subdata->mobile_code;
            $new_customer->mobile = $subdata->mobile;
            $new_customer->email = $subdata->email;
            $new_customer->sales_person = $new_customer->sales_person;
            //$new_customer->vat_type = $request->vat_type;
            $new_customer->customer_type = $subdata->customer_type;
            $new_customer->sale_type = $subdata->sale_type;
            $new_customer->vat_country = $subdata->country_vat;
            //$new_customer->vat_state = $request->state_vat;
            $new_customer->city = $subdata->city;
            $new_customer->zip_code = $subdata->zip_code;
            $new_customer->vat_percentage = $subdata->vat_percentage;
            $new_customer->vat_number = $subdata->vat_number;

            $new_customer->credit_limit = str_replace(',', '', $new_customer->credit_limit);
            $new_customer->credit_days = $new_customer->credit_days;
            $new_customer->payment_terms = $new_customer->payment_terms;
            $new_customer->payment_terms_txt = $new_customer->payment_terms_txt;
            $new_customer->transaction_type = $new_customer->transaction_type;

            //$new_customer->customer_documents = $customer_documents;
            $new_customer->status = 1;
            if ($subdata->vat_percentage_fixed) {
                $new_customer->vat_is_fixed = 1;
            }
            $new_customer->updated_by = Auth::user()->id;
            $new_customer->updated_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
            //$new_customer->created_at = '';
            $new_customer->type = $subdata->type;
            // $new_customer->company_id = session('logged_session_data.company_id');
            $results1 = $new_customer->save();

            if (count($subdata_address) > 0) {
                foreach ($subdata_address as $key) {
                    $address = new SysCustSupplAddressbook();
                    $address->cust_suppl_id = $new_customer->id;
                    $address->address = $key->address;
                    $address->address2 = $key->address2;
                    $address->city = $key->city;
                    $address->country = $key->country;
                    $address->state = $key->state;
                    $address->zip_code = $key->zip_code;
                    $address->set_default = $key->set_default;
                    $address->company_id = $key->company_id;
                    $address->is_shipping = $key->is_shipping;
                    $address->status = 1;
                    $address->created_by = Auth::user()->id;
                    $results = $address->save();
                }
            }


            if (count($subdata_contact) > 0) {
                foreach ($subdata_contact as $key) {
                    $contact = new SysCustSupplContact();
                    $contact->cust_suppl_id = $new_customer->id;
                    $contact->salutation = $key->salutation;
                    $contact->first_name = $key->first_name;
                    $contact->last_name = $key->last_name;
                    $contact->email_address = $key->email_address;
                    $contact->work_phone = $key->work_phone;
                    $contact->mobile = $key->mobile;
                    $contact->designation = $key->designation;
                    $contact->department = $key->department;
                    $contact->status = 1;
                    $contact->set_default = 1;
                    $contact->company_id = $key->company_id;
                    $contact->created_by = Auth::user()->id;
                    $results = $contact->save();
                }
            }

            if (count($subdata_doc) > 0) {
                foreach ($subdata_doc as $key) {
                    DB::table('sys_cust_suppl_doc')->insert([
                        'cust_suppl_id' => $new_customer->id,
                        'doc_name' => $key->doc_name,
                        'doc_file' => $key->doc_file,
                        'doc_exp_date' => $key->doc_exp_date,
                        'status' => 1,
                        'created_by' => Auth::user()->id,
                    ]);
                }
            }

            SysChartofAccounts::where('account_code', $new_customer->code)->update([
                'account_name' => $new_customer->name,
                'updated_at' => Carbon::now('+04:00')->format('Y-m-d H:i:s')
            ]);


            SysCustSupplAddressbookForm::where('cust_suppl_id', $sub_id)->delete();
            SysCustSupplContactForm::where('cust_suppl_id', $sub_id)->delete();
            SysCustSupplDocForm::where('cust_suppl_id', $sub_id)->delete();
            SysCustSupplForm::where('id', $sub_id)->delete();

            DB::commit();

            Toastr::success('Customer updated successfully', 'Success');
            return redirect('view-customer/' . $new_customer->id);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function add_customer_detail_popup(Request $request) //kunal modified
    {
        try {
            
            DB::beginTransaction();
                                                                                                                                                                                                                                                                                   
            //$check = SysCustSuppl::select('id','code','name')->where('email', $request->cust_email_add)->wherenotin('email', ['x','xx','xxx','xxxx'])->first();
            $company_name = $request->company_name_add;
            if (SysHelper::check_customer_is_added($company_name) > 0) {
                $retData = 'ERROR2';
                return json_encode(array('data' => $retData));
            }

            if (session('logged_session_data.company_id') != 1) {
                $company_access = '1,' . session('logged_session_data.company_id') . ',' . $request->company_id;
            } else {
                $company_access = session('logged_session_data.company_id') . ',' . $request->company_id;
            }

            $vat_percent = "";
            $vat_data = SysVat::select('vat_percentage')->where('company_id', session('logged_session_data.company_id'))
                ->where('vat_country', $request->vat_country)->where('status', 1)
                ->whereRaw("DATE_FORMAT(vat_from, '%Y-%m-%d') < '" . date('Y-m-d') . "'")
                ->orderby('vat_from', 'desc')->first();
            if (isset($vat_data->vat_percentage)) {
                $vat_percent = $vat_data->vat_percentage;
            }

            $fname = "";
            $lname = "";
            $strname = $request->cust_name_add;
            $strname_array = explode(" ", $strname);
            $fname = $strname_array[0];
            if (count($strname_array) > 1) {
                for ($i = 1; $i < count($strname_array); $i++) {
                    $lname .= $strname_array[$i] . " ";
                }
            }

            $new_customer = new SysCustSuppl();
            $new_customer->group = SysHelper::get_customer_group('group');
            $new_customer->customer_salutation = $request->customer_salutation_add ?: 'Mr.';
            $new_customer->catid = 1;  // 1 customers, 2 suppliers
            $new_customer->account_type = $request->account_type;
            $new_customer->name = $request->company_name_add;
            $new_customer->customer_name_display = strtoupper($request->company_name_add);
            $new_customer->code = SysHelper::get_new_customer_code();
            $new_customer->address = $request->cust_address_add;
            $new_customer->address2 = $request->cust_address_add2;
            $new_customer->first_name = $fname;
            $new_customer->last_name = trim($lname);
            $new_customer->designation = $request->designation_add;
            $new_customer->contcat_number = $request->cust_no_add;
            $new_customer->mobile = $request->cust_no_add;

            $new_customer->email = $request->cust_email_add;
            $new_customer->sales_person = $request->sales_person;
            $new_customer->vat_number = "";
            $new_customer->vat_country = $request->vat_country;
            $new_customer->vat_state = $request->vat_state;
            $new_customer->vat_type = "";

            $new_customer->country_telephone = $request->country_telephone ?: null;

            $new_customer->customer_type = 5;
            $new_customer->sale_type = 5;
            $new_customer->credit_limit = 1;
            $new_customer->credit_days = 1;

            $new_customer->vat_percentage = $vat_percent;
            $new_customer->payment_terms = $request->payment_terms;
            $new_customer->status = 3;
            $new_customer->type = 1;
            $new_customer->zip_code = $request->zip_code;
            $new_customer->city = $request->city;
            $new_customer->company_access = $company_access;
            $new_customer->company_id = session('logged_session_data.company_id');
            $new_customer->created_by = Auth::user()->id;
            $new_customer->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');

            if ($request->filled('customer_website')) {
                $new_customer->website = $request->customer_website;
            }

            if ($request->filled('maps_location')) {
                $new_customer->maps_location = $request->maps_location;
            }

            if ($request->filled('places_id')) {
                $new_customer->place_id = $request->places_id;
            }

            if ($request->filled('area')) {
                $new_customer->area = $request->area;
            }

            if ($request->filled('building_name')) {
                $new_customer->building_name = $request->building_name;
            }

            if ($request->filled('flat_no')) {
                $new_customer->flat_office_no = $request->flat_no;
            }


            $results1 = $new_customer->save();

            $accounts = new SysChartofAccounts();
            $accounts->account_code = $new_customer->code;
            $accounts->account_name = $request->company_name_add;
            $accounts->group = SysHelper::get_customer_group('group');
            $accounts->subgroup = SysHelper::get_customer_group('subgroup');
            $accounts->subgroup2 = SysHelper::get_customer_group('subgroup2');
            $accounts->status = 1;
            $accounts->company_id = session('logged_session_data.company_id');
            $accounts->company_access = $company_access;
            $accounts->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
            $accounts->created_by = Auth::user()->id;
            $results = $accounts->save();

            DB::table('sys_cust_suppl_assign')->insert([
                'cust_supp_id' => $new_customer->id,
                'user_id' => $request->sales_person,
                'type' => 1, //1 customers, 2 suppliers
            ]);

            $address = new SysCustSupplAddressbook();
            $address->cust_suppl_id = $new_customer->id;
            // $address->address = $request->cust_address_add;
            // $address->address2 = $request->cust_address_add2;

            if ($request->filled('area')) {
                $address->area = $request->area;
            }

            if ($request->filled('building_name')) {
                $address->building_name = $request->building_name;
            }

            if ($request->filled('flat_no')) {
                $address->flat_office_no = $request->flat_no;
            }



            $address->country = $request->vat_country;
            $address->state = $request->vat_state;
            $address->city = $request->city;
            $address->zip_code = $request->zip_code;
            $address->set_default = 1;
            $address->company_id = session('logged_session_data.company_id');
            $address->is_shipping = 1;
            $address->status = 1;
            $address->created_by = Auth::user()->id;
            $results = $address->save();


            $address = new SysCustSupplAddressbook();
            $address->cust_suppl_id = $new_customer->id;
            // $address->address = $request->cust_address_add;
            // $address->address2 = $request->cust_address_add2;

            if ($request->filled('area')) {
                $address->area = $request->area;
            }

            if ($request->filled('building_name')) {
                $address->building_name = $request->building_name;
            }

            if ($request->filled('flat_no')) {
                $address->flat_office_no = $request->flat_no;
            }



            $address->country = $request->vat_country;
            $address->state = $request->vat_state;
            $address->city = $request->city;
            $address->zip_code = $request->zip_code;
            $address->set_default = 1;
            $address->company_id = session('logged_session_data.company_id');
            $address->is_shipping = 0;
            $address->status = 1;
            $address->created_by = Auth::user()->id;
            $results = $address->save();

            $contact = new SysCustSupplContact();
            $contact->cust_suppl_id = $new_customer->id;
            $contact->salutation = 'Mr';
            $contact->first_name = $fname;
            $contact->last_name = trim($lname);
            $contact->email_address = $request->cust_email_add;
            $contact->work_phone = $request->cust_no_add;
            $contact->mobile = $request->cust_no_add;
            $contact->designation = $request->designation_add;
            $contact->status = 1;
            $contact->set_default = 1;
            $contact->company_id = session('logged_session_data.company_id');
            $contact->created_by = Auth::user()->id;
            $results = $contact->save();


            $vendors = SysHelper::get_customer_list_deal_lead();
            DB::commit();
            return json_encode(['data' => $vendors, 'new_company_id' => $new_customer->id]);
        } catch (\Exception $e) {
            $retData = $e;
            DB::rollBack();
            //return $e;
            return json_encode(array('data' => $retData));
            //Toastr::error('Operation Failed', 'Failed');
            //return redirect()->back(); 
        }
    }

    function customer_name(Request $request)
    {
        try {
            if ($request->get('query')) {
                $company_name = $request->get('query');
                $data = DB::table('sys_cust_suppl')->select('name')->where('catid', 1)
                    ->where(function ($query) use ($company_name) {
                        $query->orwhere('name', 'like', '%' . $company_name . '%')
                            ->orwhere('name', 'like', '%' . str_replace(',', '', $company_name) . '%')
                            ->orwhere('name', 'like', '%' . str_replace(',', ' ', $company_name) . '%')
                            ->orwhere('name', 'like', '%' . str_replace('.', '', $company_name) . '%')
                            ->orwhere('name', 'like', '%' . str_replace('.', ' ', $company_name) . '%');
                    })->get();

                if ($data->isEmpty()) {
                    return '';
                }
                $output = '<ul class="form-control" style="list-style: none; height: auto; position: absolute; z-index: 999; line-height: 25px;">';
                foreach ($data as $row) {
                    $output .= '<li><a href="#">' . $row->name . '</a></li>';
                }
                $output .= '</ul>';
                echo $output;
            }
        } catch (\Throwable $th) {
            //return $th;
        }
    }

    function customerMerge(Request $request)
    {
        try {
            DB::beginTransaction();

            /*$duplicates = DB::table('sys_cust_suppl')
            ->select('id', 'name', 'code')
            ->whereIn('code', function ($q){
                        $q->select('code')
                        ->from('sys_cust_suppl')
                        ->groupBy('code')
                        ->havingRaw('COUNT(*) > 1');
            })->where('company_id',1)->get();
            //return  $duplicates;
            if(count($duplicates)){
                foreach($duplicates as $dup){
                    $code = SysHelper::get_new_customer_code();
                    DB::table('sys_cust_suppl')->where('code',$dup->code)->where('company_id',1)->update(['code' => $code]);
                    DB::table('sys_chartofaccounts')->where('account_code',$dup->code)->where('company_id',1)->update(['account_code' => $code, 'account_name' => $dup->name]);
                }
            }*/

            $from_account = $request->from_account;
            $to_account = $request->to_account;

            if (count($from_account) > 0) {
                foreach ($from_account as $f_account) {
                    $from_cust = DB::table('sys_cust_suppl as c')->select('c.id', 'c.sales_person', 'c.company_access')->join('sys_chartofaccounts as a', 'a.account_code', 'c.code')->where('a.id', $f_account)->get();
                    $to_cust = DB::table('sys_cust_suppl as c')->select('c.id', 'c.sales_person', 'c.company_access')->join('sys_chartofaccounts as a', 'a.account_code', 'c.code')->where('a.id', $to_account)->get();
                    if (count($from_cust) > 1) {
                        DB::rollBack();
                        Toastr::error('Operation Failed. Multipple Account Code', 'Failed');
                        return redirect()->back();
                    }
                    if (count($to_cust) > 1) {
                        DB::rollBack();
                        Toastr::error('Operation Failed. Multipple Account Code', 'Failed');
                        return redirect()->back();
                    }

                    DB::table('sys_crm_deals')->where('cust_id', $from_cust[0]->id)->update(['cust_id' => $to_cust[0]->id]);
                    DB::table('sys_crm_leads')->where('cust_id', $from_cust[0]->id)->update(['cust_id' => $to_cust[0]->id]);

                    DB::table('sys_item_stock')->where('account_id', $f_account)->update(['account_id' => $to_account]);
                    DB::table('sys_chartofaccounts_transaction')->where('account_id', $f_account)->update(['account_id' => $to_account]);

                    DB::table('sys_proforma_invoice')->where('customer', $f_account)->update(['customer' => $to_account]);
                    DB::table('sys_sales_invoice')->where('customer', $f_account)->update(['customer' => $to_account]);
                    DB::table('sys_delivery_note')->where('customer_id', $f_account)->update(['customer_id' => $to_account]);
                    DB::table('sys_sales_return')->where('customer', $f_account)->update(['customer' => $to_account]);
                    DB::table('sys_receipt_adjustments')->where('account_id', $f_account)->update(['account_id' => $to_account]);

                    DB::table('sys_purchase_order')->where('vendors', $f_account)->update(['vendors' => $to_account]);
                    DB::table('sys_purchase_grn')->where('vendors', $f_account)->update(['vendors' => $to_account]);
                    DB::table('sys_purchase_invoice')->where('vendors', $f_account)->update(['vendors' => $to_account]);
                    DB::table('sys_purchase_return')->where('vendors', $f_account)->update(['vendors' => $to_account]);
                    DB::table('sys_payment_adjustments')->where('account_id', $f_account)->update(['account_id' => $to_account]);

                    DB::table('sys_crm_amc_table')->where('cust_name', $from_cust[0]->id)->update(['cust_name' => $to_cust[0]->id]);
                    DB::table('sys_crm_ps_service_table')->where('cust_name', $from_cust[0]->id)->update(['cust_name' => $to_cust[0]->id]);

                    DB::table('sys_cust_suppl')->where('id', $from_cust[0]->id)->update([
                        'status' => 2,
                        'updated_at' => Carbon::now('+04:00')->format('Y-m-d H:i:s')
                    ]);
                    DB::table('sys_chartofaccounts')->where('id', $f_account)->update([
                        'status' => 2,
                        'updated_at' => Carbon::now('+04:00')->format('Y-m-d H:i:s')
                    ]);

                    DB::table('sys_cust_suppl_assign')->insert(['cust_supp_id' => $to_cust[0]->id, 'user_id' => $to_cust[0]->sales_person, 'type' => 1]);

                    $str = $from_cust[0]->company_access . ',' . $to_cust[0]->company_access;
                    $exploded = explode(',', $str);
                    $unique = array_unique($exploded);
                    $company_access = implode(',', $unique);

                    DB::table('sys_cust_suppl')->where('id', $to_account)->update(['company_access' => $company_access, 'status' => 1]);
                    DB::table('sys_chartofaccounts')->where('id', $to_account)->update(['company_access' => $company_access, 'status' => 1]);
                }
            }
            DB::commit();
            Toastr::success('Customer Merged Successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    function customerMergeDuplicate(Request $request)
    {
        try {
            $er = "";
            if (count($request->duplicate_name) > 0) {
                DB::beginTransaction();
                foreach ($request->duplicate_name as $dup_code) {

                    $duplicate_customer = DB::table('sys_chartofaccounts')
                        ->select('id', 'account_name', 'account_code')
                        ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TRIM(account_name)), ' ', ''), '@', ''), '#', ''), '.', ''), '-', ''), '_', ''), '(', ''), ')', '') = ?", [$dup_code])
                        ->where('subgroup2', 7)->where('status', 1)->orderby('company_id', 'asc')
                        ->get();
                    //return $duplicate_customer;
                    if (count($duplicate_customer) > 0) {
                        $to_account = $duplicate_customer[0]->id;
                        for ($i = 1; $i < count($duplicate_customer); $i++) {
                            $f_account = $duplicate_customer[$i]->id;

                            $from_cust = DB::table('sys_cust_suppl as c')->select('c.id', 'c.sales_person', 'c.company_access')->join('sys_chartofaccounts as a', 'a.account_code', 'c.code')->where('a.id', $f_account)
                                ->get();

                            $to_cust = DB::table('sys_cust_suppl as c')->select('c.id', 'c.sales_person', 'c.company_access')->join('sys_chartofaccounts as a', 'a.account_code', 'c.code')->where('a.id', $to_account)->get();


                            if (count($from_cust) == 0 || count($to_cust) == 0) {
                                $er .= $dup_code . ", ";
                                continue;
                                //DB::rollBack();
                                //Toastr::error('Operation Failed. Account Issue Found', 'Failed');
                                //return redirect()->back();
                            }
                            if (count($from_cust) > 1) {
                                $er .= $dup_code . ", ";
                                continue;
                                //DB::rollBack();
                                //Toastr::error('Operation Failed. Multipple Account Code', 'Failed');
                                //return redirect()->back();
                            }
                            if (count($to_cust) > 1) {
                                $er .= $dup_code . ", ";
                                continue;
                                //DB::rollBack();
                                //Toastr::error('Operation Failed. Multipple Account Code', 'Failed');
                                //return redirect()->back();                        
                            }

                            DB::table('sys_crm_deals')->where('cust_id', $from_cust[0]->id)->update(['cust_id' => $to_cust[0]->id]);
                            SysHelper::cust_suppl_merge(1, 'sys_crm_deals', $from_cust[0]->id, $to_cust[0]->id);

                            DB::table('sys_crm_leads')->where('cust_id', $from_cust[0]->id)->update(['cust_id' => $to_cust[0]->id]);
                            SysHelper::cust_suppl_merge(1, 'sys_crm_leads', $from_cust[0]->id, $to_cust[0]->id);

                            DB::table('sys_item_stock')->where('account_id', $f_account)->update(['account_id' => $to_account]);
                            SysHelper::cust_suppl_merge(1, 'sys_item_stock', $f_account, $to_account);

                            DB::table('sys_chartofaccounts_transaction')->where('account_id', $f_account)->update(['account_id' => $to_account]);
                            SysHelper::cust_suppl_merge(1, 'sys_chartofaccounts_transaction', $f_account, $to_account);

                            DB::table('sys_proforma_invoice')->where('customer', $f_account)->update(['customer' => $to_account]);
                            SysHelper::cust_suppl_merge(1, 'sys_proforma_invoice', $f_account, $to_account);

                            DB::table('sys_sales_invoice')->where('customer', $f_account)->update(['customer' => $to_account]);
                            SysHelper::cust_suppl_merge(1, 'sys_sales_invoice', $f_account, $to_account);

                            DB::table('sys_delivery_note')->where('customer_id', $f_account)->update(['customer_id' => $to_account]);
                            SysHelper::cust_suppl_merge(1, 'sys_delivery_note', $f_account, $to_account);

                            DB::table('sys_sales_return')->where('customer', $f_account)->update(['customer' => $to_account]);
                            SysHelper::cust_suppl_merge(1, 'sys_sales_return', $f_account, $to_account);

                            DB::table('sys_receipt_adjustments')->where('account_id', $f_account)->update(['account_id' => $to_account]);
                            SysHelper::cust_suppl_merge(1, 'sys_receipt_adjustments', $f_account, $to_account);

                            DB::table('sys_purchase_order')->where('vendors', $f_account)->update(['vendors' => $to_account]);
                            SysHelper::cust_suppl_merge(1, 'sys_purchase_order', $f_account, $to_account);

                            DB::table('sys_purchase_grn')->where('vendors', $f_account)->update(['vendors' => $to_account]);
                            SysHelper::cust_suppl_merge(1, 'sys_purchase_grn', $f_account, $to_account);

                            DB::table('sys_purchase_invoice')->where('vendors', $f_account)->update(['vendors' => $to_account]);
                            SysHelper::cust_suppl_merge(1, 'sys_purchase_invoice', $f_account, $to_account);

                            DB::table('sys_purchase_return')->where('vendors', $f_account)->update(['vendors' => $to_account]);
                            SysHelper::cust_suppl_merge(1, 'sys_purchase_return', $f_account, $to_account);

                            DB::table('sys_payment_adjustments')->where('account_id', $f_account)->update(['account_id' => $to_account]);
                            SysHelper::cust_suppl_merge(1, 'sys_payment_adjustments', $f_account, $to_account);

                            DB::table('sys_crm_amc_table')->where('cust_name', $from_cust[0]->id)->update(['cust_name' => $to_cust[0]->id]);
                            SysHelper::cust_suppl_merge(1, 'sys_crm_amc_table', $from_cust[0]->id, $to_cust[0]->id);

                            DB::table('sys_crm_ps_service_table')->where('cust_name', $from_cust[0]->id)->update(['cust_name' => $to_cust[0]->id]);
                            SysHelper::cust_suppl_merge(1, 'sys_crm_ps_service_table', $from_cust[0]->id, $to_cust[0]->id);

                            DB::table('sys_cust_suppl')->where('id', $from_cust[0]->id)->update([
                                'status' => 2,
                                'updated_at' => Carbon::now('+04:00')->format('Y-m-d H:i:s')
                            ]);
                            DB::table('sys_chartofaccounts')->where('id', $f_account)->update([
                                'status' => 2,
                                'updated_at' => Carbon::now('+04:00')->format('Y-m-d H:i:s')
                            ]);

                            DB::table('sys_cust_suppl_assign')->insert(['cust_supp_id' => $to_cust[0]->id, 'user_id' => $to_cust[0]->sales_person, 'type' => 1]);

                            $str = $from_cust[0]->company_access . ',' . $to_cust[0]->company_access;
                            $exploded = explode(',', $str);
                            $unique = array_unique($exploded);
                            $company_access = implode(',', $unique);

                            DB::table('sys_cust_suppl')->where('id', $to_cust[0]->id)->update(['company_access' => $company_access, 'status' => 1]);
                            DB::table('sys_chartofaccounts')->where('id', $to_account)->update(['company_access' => $company_access, 'status' => 1]);
                        }
                    }
                }
                DB::commit();
                if ($er != "") {
                    $er = " And Skiped " . $er;
                    Toastr::warning('Customer Merged Successfully' . $er, 'Success');
                } else {
                    Toastr::success('Customer Merged Successfully' . $er, 'Success');
                }
                return redirect()->back();
            } else {
                Toastr::error('No Customer Selected', 'Failed');
                return redirect()->back();
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
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


        $com_id = session('logged_session_data.company_id');


        $query = SysCustSuppl::select('id', 'name', 'code', 'contcat_person', 'mobile', 'email', 'vat_country', 'vat_percentage', 'vat_number', 'credit_limit', 'credit_days', 'payment_terms', 'transaction_type', 'customer_type', 'first_name', 'last_name', 'contcat_number', 'customer_salutation', 'status', 'is_file', 'internal', 'created_at')
            ->where('catid', 1);

        if (Auth::user()->role_id != 1 && Auth::user()->role_id != 2 && Auth::user()->role_id != 3 && Auth::user()->role_id != 27 && Auth::user()->role_id != 28 && Auth::user()->role_id != 35) {
            if (Auth::user()->role_id == 5 || Auth::user()->role_id == 8) {
                $users = SmStaff::select('user_id')->where('company_id', $com_id)->get();
                foreach ($users as $value) {
                    $userid[] = $value->user_id;
                }
                $query->wherein('sales_person', $userid);
            } else {
                $query->where('sales_person', Auth::user()->id);
            }
        }
        $query->whereRaw("find_in_set($com_id,company_access)");



        $query->where(function ($query) use ($q, $formattedDate) {
            $query->where(function ($qsub) use ($q) {
                $dealId = SysHelper::get_dealid_from_code($q);


                if ($q) {
                    $qsub->where('code', 'like', "%{$q}%")
                        ->orWhere('contcat_person', 'like', "%{$q}%")
                        ->orWhere('mobile', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")

                        ->orWhere('name', 'like', "%{$q}%");
                }

                if (!empty($dealId) && $dealId != "0") {
                    $qsub->orWhere('deal_id', 'like', "%{$dealId}%");
                }
            });

            if ($formattedDate) {
                // Combine inside same group
                $query->orWhere(function ($q2) use ($formattedDate) {
                    $q2->whereDate('created_at', $formattedDate);
                });
            }
        });




        $amc_list = $query->orderBy('created_at', 'desc')->paginate(100);




        return response()->json($amc_list);
    }


    public function searchCustomerList(Request $request)
    {

        $q = $request->get('query');
        $formattedDate = null;
        if (preg_match('/\d{2}[\/\-]\d{2}[\/\-]\d{4}/', $q)) {
            $normalized = str_replace('/', '-', $q);
            $formattedDate = date('Y-m-d', strtotime($normalized));
        }


        $com_id = session('logged_session_data.company_id');


        $query = SysCustSupplForm::with('createdby')->where('company_id', $com_id)->where('status', 1)->where('catid', 1);



        $query->where(function ($query) use ($q, $formattedDate) {
            $query->where(function ($qsub) use ($q) {
                if ($q) {
                    $qsub->where('code', 'like', "%{$q}%")
                        ->orWhere('contcat_person', 'like', "%{$q}%")
                        ->orWhere('mobile', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")

                        ->orWhere('name', 'like', "%{$q}%");
                }
            });

            if ($formattedDate) {
                // Combine inside same group
                $query->orWhere(function ($q2) use ($formattedDate) {
                    $q2->whereDate('created_at', $formattedDate);
                });
            }
        });




        $amc_list = $query->orderBy('created_at', 'desc')->paginate(100);




        return response()->json($amc_list);
    }


    public function searchCustomerPending(Request $request)
    {

        $q = $request->get('query');
        $formattedDate = null;
        if (preg_match('/\d{2}[\/\-]\d{2}[\/\-]\d{4}/', $q)) {
            $normalized = str_replace('/', '-', $q);
            $formattedDate = date('Y-m-d', strtotime($normalized));
        }


        $com_id = session('logged_session_data.company_id');


        $query = SysCustSuppl::select('id', 'name', 'code', 'contcat_person', 'mobile', 'email', 'vat_country', 'vat_percentage', 'vat_number', 'credit_limit', 'credit_days', 'payment_terms', 'transaction_type', 'customer_type', 'first_name', 'last_name', 'contcat_number', 'customer_salutation', 'status', 'is_file', 'internal', 'created_at')
            ->where('catid', 1);

        if (Auth::user()->role_id != 1 && Auth::user()->role_id != 2 && Auth::user()->role_id != 3 && Auth::user()->role_id != 27 && Auth::user()->role_id != 28 && Auth::user()->role_id != 35) {
            if (Auth::user()->role_id == 5 || Auth::user()->role_id == 8) {
                $users = SmStaff::select('user_id')->where('company_id', $com_id)->get();
                foreach ($users as $value) {
                    $userid[] = $value->user_id;
                }
                $query->wherein('sales_person', $userid);
            } else {
                $query->where('sales_person', Auth::user()->id);
            }
        }
        $query->whereRaw("find_in_set($com_id,company_access)");



        $query->where(function ($query) use ($q, $formattedDate) {
            $query->where(function ($qsub) use ($q) {
                $dealId = SysHelper::get_dealid_from_code($q);


                if ($q) {
                    $qsub->where('code', 'like', "%{$q}%")
                        ->orWhere('contcat_person', 'like', "%{$q}%")
                        ->orWhere('mobile', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")

                        ->orWhere('name', 'like', "%{$q}%");
                }

                if (!empty($dealId) && $dealId != "0") {
                    $qsub->orWhere('deal_id', 'like', "%{$dealId}%");
                }
            });

            if ($formattedDate) {
                // Combine inside same group
                $query->orWhere(function ($q2) use ($formattedDate) {
                    $q2->whereDate('created_at', $formattedDate);
                });
            }
        });




        $amc_list = $query->orderBy('created_at', 'desc')->paginate(100);




        return response()->json($amc_list);
    }

    // Customer Search Page - Returns the view kp
    public function customerSearch(Request $request)
    {
        try {
            $cust_name = "";

            // If AJAX request with cust_name parameter, return the partial view with data
            if ($request->ajax() || $request->has('cust_name')) {
                $cust_name = $request->get('cust_name', '');

                if (strlen($cust_name) >= 5) {
                    $com_id = session('logged_session_data.company_id');

                    $query = SysCustSuppl::select('id', 'name', 'code', 'contcat_person', 'mobile', 'email', 'created_at')
                        ->where('catid', 1);

                    // Apply role-based filtering
                    if (Auth::user()->role_id != 1 && Auth::user()->role_id != 2 && Auth::user()->role_id != 3 && Auth::user()->role_id != 27 && Auth::user()->role_id != 28 && Auth::user()->role_id != 35) {
                        if (Auth::user()->role_id == 5 || Auth::user()->role_id == 8) {
                            $users = SmStaff::select('user_id')->where('company_id', $com_id)->get();
                            $userid = [];
                            foreach ($users as $value) {
                                $userid[] = $value->user_id;
                            }
                            $query->whereIn('sales_person', $userid);
                        } else {
                            $query->where('sales_person', Auth::user()->id);
                        }
                    }

                    $query->whereRaw("find_in_set($com_id,company_access)");

                    // Search filter
                    $query->where(function ($q) use ($cust_name) {
                        $q->where('code', 'like', "%{$cust_name}%")
                            ->orWhere('name', 'like', "%{$cust_name}%")
                            ->orWhere('contcat_person', 'like', "%{$cust_name}%")
                            ->orWhere('mobile', 'like', "%{$cust_name}%")
                            ->orWhere('email', 'like', "%{$cust_name}%");
                    });

                    $data_list = $query->orderBy('created_at', 'desc')->paginate(50);
                } else {
                    $data_list = collect();
                }

                return view('backEnd.cust-suppl.CustomerSearchPage', compact('data_list', 'cust_name'));
            }

            // Initial page load - return the main view
            return view('backEnd.cust-suppl.CustomerSearch', compact('cust_name'));

        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    // Customer Search Data - For AJAX POST requests kp
    public function customerSearchData(Request $request)
    {
        try {
            $cust_name = $request->get('cust_name', '');

            if (strlen($cust_name) >= 5) {
                $com_id = session('logged_session_data.company_id');

                $query = SysCustSuppl::select('id', 'name', 'code', 'contcat_person', 'mobile', 'email', 'created_at')
                    ->where('catid', 1);

                // Apply role-based filtering
                if (Auth::user()->role_id != 1 && Auth::user()->role_id != 2 && Auth::user()->role_id != 3 && Auth::user()->role_id != 27 && Auth::user()->role_id != 28 && Auth::user()->role_id != 35) {
                    if (Auth::user()->role_id == 5 || Auth::user()->role_id == 8) {
                        $users = SmStaff::select('user_id')->where('company_id', $com_id)->get();
                        $userid = [];
                        foreach ($users as $value) {
                            $userid[] = $value->user_id;
                        }
                        $query->whereIn('sales_person', $userid);
                    } else {
                        $query->where('sales_person', Auth::user()->id);
                    }
                }

                $query->whereRaw("find_in_set($com_id,company_access)");

                // Search filter
                $query->where(function ($q) use ($cust_name) {
                    $q->where('code', 'like', "%{$cust_name}%")
                        ->orWhere('name', 'like', "%{$cust_name}%")
                        ->orWhere('contcat_person', 'like', "%{$cust_name}%")
                        ->orWhere('mobile', 'like', "%{$cust_name}%")
                        ->orWhere('email', 'like', "%{$cust_name}%");
                });

                $data_list = $query->orderBy('created_at', 'desc')->paginate(50);
            } else {
                $data_list = collect();
            }

            return view('backEnd.cust-suppl.CustomerSearchPage', compact('data_list', 'cust_name'));

        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


}

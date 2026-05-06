<?php

namespace App\Http\Controllers;

use App\ApiBaseMethod;
use App\SmDesignation;
use App\SmInspectingDepartment;
use App\SmItem;
use Illuminate\Http\Request;
use App\SmItemStore;
use App\SmStaff;
use App\SysAccountGroupSub2;
use App\SysBrand;
use App\SysChartofAccounts;
use App\SysCompany;
use App\SysCountries;
use App\SysCrmDeals;
use App\SysCrmDealTrack;
use App\SysCrmDealTrackApprovalInvoice;
use App\SysCrmDealTrackApprovalPurchease;
use App\SysCrmDealTrackApprovalReceivables;
use App\SysCrmLeads;
use App\SysCrmLeadsComments;
use App\SysCrmDealsComments;
use App\SysCrmQuoteCSItems;
use App\SysCrmQuoteItems;
use App\SysCrmSalesTarget;
use App\SysCrmService;
use App\SysCrmServiceAssign;
use App\SysCrmSupport;
use App\SysCurrencySettings;
use App\SysCustSuppl;
use App\SysHelper;
use App\SysItemOpeningStock;
use App\SysItemStock;
use App\SysPaymentTerms;
use App\SysShipping;
use App\SysStockIn;
use App\SysStockInSerialNo;
use App\SysSupplierType;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Return_;
use Illuminate\Support\Facades\Validator;
use App\SysCompanyPeopleDocument;
use App\SysCompanySetting;
use App\SysCompanyHrPayrollSetting;
use App\SysCompanyCompliance;
use App\SysCompanyPolicy;
use App\SysCompanyHoliday;
use App\SysCompanyPeople;
use App\SysCompanyBanking;
use App\CompanyWarehouse;

class SysCrmLeadsController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
    }

    public function index(Request $request)
    {
        return redirect('crm-leads/show');
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $currency = SysCurrencySettings::select('id', 'code')->get();
            $product = SysHelper::get_product_list($company_id);
            $vendors = SysHelper::get_customer_list_deal_lead();
            $brand = SysBrand::select('title')->orderby('title', 'asc')->get();
            return view('backEnd.crm.LeadForm', compact('currency', 'vendors', 'brand', 'product'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    //modified vb kp
    public function store(Request $request)
    {

        $tags = "";
        if ($request->tags != "") {
            $tags = implode(",", $request->tags);
        }

        $doc_file = "";

        if ($request->file('doc') != "") {
            $files = $request->file('doc');
            for ($i = 0; $i < count($files); $i++) {
                $file1 = $files[$i];
                $doc_file = md5(time()) . "_lead_" . $i . "." . $file1->getclientoriginalextension();
                $file1->move('public/uploads/crm_lead_doc/', $doc_file);
                $lpo[] = $doc_file;
            }
            $doc_file = implode("|", $lpo);
        }


        DB::beginTransaction();
        try {
            $flag = SysCrmLeads::where([
                ['date', Carbon::createFromFormat('d/m/Y', $request->date)->format('Y-m-d')],
                ['lead_name', $request->lead_name],
                ['cust_id', $request->company_name],
                ['source', $request->source],
                ['owner', $request->owner],
                ['tags', $tags],
                ['created_by', Auth::user()->id],
                ['company_id', $request->company]
            ])->first();
            if ($flag) {
                Toastr::success('Lead has been added successfully', 'Success');
                return redirect()->back();
            } else {
                $ssi = new SysCrmLeads();
                $ssi->code = SysHelper::get_new_code_lead('sys_crm_leads', 'LD', 'code', $request->company);
                $ssi->date = Carbon::createFromFormat('d/m/Y', $request->date)->format('Y-m-d');
                $ssi->lead_name = $request->lead_name;
                $ssi->cust_id = $request->company_name;
                $ssi->cust_name = $request->cust_name;
                $ssi->cust_no = $request->cust_no;
                $ssi->company_name = $request->company_name;
                $ssi->cust_email = $request->cust_email;
                $ssi->cust_designation = $request->cust_designation;
                $ssi->address = $request->address;
                //$ssi->country = $request->country;
                $ssi->source = $request->source;
                $ssi->source_o = $request->source_o;
                $ssi->owner = $request->owner;
                $ssi->doc = $doc_file;
                $ssi->tags = $tags;
                $ssi->note = $request->note;
                $ssi->status = $request->status;

                if ($request->status == 1) {
                    $ssi->sub_status = 1;
                } elseif ($request->status == 2) {
                    $ssi->sub_status = 2;
                }

                $ssi->isproject = $request->isproject;
                $ssi->created_by = Auth::user()->id;
                $ssi->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
                $ssi->updated_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
                //$ssi->company_id = session('logged_session_data.company_id');
                $ssi->company_id = $request->company;
                $ssi->last_updated = Carbon::now('+04:00')->format('Y-m-d H:i:s');
                $ssi->save();
                $ssi->toArray();

                SysHelper::set_user_custsupp($request->owner, $request->company_name);

                $results = 0;
                DB::commit();

                if ($results == 0) {

                    if ($request->source == "Gitex") {
                        if ($request->cust_email != "") {
                            SysHelper::notificationMailGitexMail($request->cust_name, $request->cust_email, $request->owner);
                        }
                    }

                    if ($request->owner != Auth::user()->id) {
                        $owner = SmStaff::select('first_name', 'email')->where('user_id', $request->owner)->first();
                        $comp = SysCustSuppl::select('name')->where('id', $request->company_name)->first();

                        $body = "<br />";
                        $body .= "A new lead has been assigned to you.";
                        $body .= "<br />Customer Name : " . $request->cust_name;
                        $body .= "<br />Company Name : " . $comp->name;
                        $body .= "<br />";
                        $body .= "<a href='http://erp.venushrms.com/crm-leads/" . $ssi->id . "/view'> View Lead : " . $ssi->id . " </a>";
                        $body .= "<br />";
                        $body .= "<br />";
                        SysHelper::notificationMail($owner->first_name, $body, $owner->email, 'A new lead has been assigned');
                    }

                    Toastr::success('Lead has been added successfully', 'Success');
                    return redirect('crm-leads/show/' . $ssi->id);
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

    public function storeLeadZapier(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'mobile' => 'nullable|string|max:20',
            'tags' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:255',
        ]);


        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }
        try {
            DB::beginTransaction();


            $lead = new SysCrmLeads();
            $lead->code = SysHelper::get_new_code_lead('sys_crm_leads', 'LD', 'code', 1);
            $lead->date = Carbon::now('+04:00')->format('Y-m-d');
            $lead->lead_name = $request->name;
            $lead->cust_email = $request->email;
            $lead->cust_no = $request->mobile;
            $lead->note = $request->note;
            $lead->tags = $request->tags;
            $lead->source = 'Chat';
            $lead->status = 1;
            $lead->owner = 17;
            $lead->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
            $lead->company_id = 1;
            $lead->last_updated = Carbon::now('+04:00')->format('Y-m-d H:i:s');
            $lead->save();

            DB::commit();


            return response()->json(['status' => true, 'message' => 'Lead has been added successfully.'], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'error' => 'Server Error', 'message' => $e->getMessage()], 500);
        }
    }

    // public function showList()
    // {
    //     return 2;
    //     try {
    //         $leads=session('lead_list_query.leads');
    //         $vendors=session('lead_list_query.vendors');
    //         $staff=session('lead_list_query.staff');
    //         $ctrl_cust_id=session('lead_list_query.ctrl_cust_id');
    //         $ctrl_status=session('lead_list_query.ctrl_status');
    //         $ctrl_source=session('lead_list_query.ctrl_source');
    //         $ctrl_owner=session('lead_list_query.ctrl_owner');
    //         $ctrl_date=session('lead_list_query.ctrl_date');
    //         $ctrl_date2=session('lead_list_query.ctrl_date2');
    //         $ctrl_lead_id=session('lead_list_query.ctrl_lead_id');
    //         $ctrl_isproject=session('lead_list_query.ctrl_isproject');
    //         $brand=session('lead_list_query.brand');
    //         $ctrl_brand=session('lead_list_query.ctrl_brand');
    //         $filter_by=session('lead_list_query.filter_by');

    //         return view('backEnd.crm.LeadList', compact('leads','vendors','staff','ctrl_cust_id','ctrl_status','ctrl_source','ctrl_owner','ctrl_date','ctrl_date2','ctrl_lead_id','ctrl_isproject','brand','ctrl_brand','filter_by'));

    //     } catch (\Throwable $th) {
    //         return $th;
    //     }
    // }

    public function show(Request $request, $id = null)
    {


        $com_id = session('logged_session_data.company_id');
        //return 1;
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $currency = SysCurrencySettings::select('id', 'code')->get();
            $company = SysCompany::orderby('sort_id', 'asc')->get();
            $product = SysHelper::get_product_list($company_id);
            $designation = SmDesignation::select('title')->where('active_status', 1)->get();
            $country = SysCountries::select('id', 'name', 'iso3')->get();

            $vendors = SysHelper::get_customer_list_deal_lead();

            $brand = SysBrand::select('id', 'title')->orderby('title', 'asc')->get();

            if ($com_id == 1) {
                $staff = SmStaff::where('active_status', 1)->orderby('first_name', 'asc')->get();
            } else {
                $staff = SmStaff::select('user_id', 'full_name')->whereRaw("find_in_set($com_id,company_access)")->get();
            }

            if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 35) {
                $sales_person = SmStaff::select('user_id', 'full_name')->where('active_status', 1)->orderby('full_name', 'asc')->get();
            } else {
                $sales_person = SmStaff::select('user_id', 'full_name')->where('user_id', Auth::user()->id)->orderby('full_name', 'asc')->get();
            }

            $paymentterms = SysPaymentTerms::where('active_status', '=', '1')->get();
            $ctrl_lead_id = '';
            $ctrl_cust_id = '';
            $ctrl_status = '';
            $ctrl_source = '';
            $ctrl_owner = '';
            $ctrl_date = null;
            $ctrl_date2 = null;
            $ctrl_isproject = '';
            $ctrl_brand = '';
            $filter_by = '';
            $ctrl_sub_status = '';
            $ctrl_region_id = '';
            $statusCounts = [];
            $total_leads = 0;
            $ctrl_followupdt_filter = '';



            //if($_POST){
            // if (SysHelper::get_pagination_post($request)) {
            if (
                $request->has('date') ||
                $request->has('date2') ||
                $request->has('filter_by') ||
                $request->has('lead_id') ||
                $request->has('company_id') ||
                $request->has('status_id') ||
                $request->has('sub_status') ||
                $request->has('region_id') ||
                $request->has('isproject_id') ||
                $request->has('source_id') ||
                $request->has('owner_id') ||
                $request->has('followupdt_filter') ||
                $request->has('brand_id') ||
                $request->has('sales')
            ) {





                $query = SysCrmLeads::wherein('status', [0, 1, 2, 3, 4, 10]);

                $r = SysHelper::get_data_by_role();
                if (session('logged_session_data.company_id') != 1) {
                    $query->wherein('company_id', $r[0]);
                }
                $statsQuery = (clone $query);



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
                    $query->whereBetween(DB::raw("DATE(created_at)"), [$ctrl_date, $ctrl_date2]);
                    $statsQuery = (clone $query);
                }

                if (!empty($request->lead_id)) {
                    $leadInput = trim($request->lead_id); // user input like 1005 or LDS-1005

                    $query->where(function ($q) use ($leadInput) {


                        $q->where('code', 'like', "%{$leadInput}%");
                    });

                    $ctrl_lead_id = $leadInput;
                }

                if ($request->company_id != "") {
                    $query->where('cust_id', $request->company_id);
                    $ctrl_cust_id = $request->company_id;
                    $statsQuery = (clone $query);
                }
                if ($request->status_id != "") {
                    if ($request->status_id == 5 || $request->status_id == 2) {
                        $query->whereIn('status', [0, 2]);
                    } else {
                        $query->where('status', $request->status_id);
                    }
                    $ctrl_status = $request->status_id;
                }

                if ($request->sub_status != "") {
                    if (in_array($request->sub_status, ['d1', 'd2', 'd3', 'd4', 'd5'])) {
                        $dealstatusId = preg_replace('/\D/', '', $request->sub_status);
                        $matchingDealIds = SysCrmDeals::where('stage', $dealstatusId)->pluck('id')->toArray();
                        // Use whereIn to filter leads by matching deal IDs
                        $query->whereIn('deal_id', $matchingDealIds);
                    } else {
                        $query->where('sub_status', $request->sub_status);
                    }

                    $ctrl_sub_status = $request->sub_status;
                }

                if ($request->region_id != "") {
                    $query->whereHas('customername.vatcountry', function ($q) use ($request) {
                        $q->where('vat_country', $request->region_id);
                    });
                    $ctrl_region_id = $request->region_id;
                    $statsQuery = (clone $query);
                }

                if ($request->isproject_id != "") {
                    $query->where('isproject', $request->isproject_id);
                    $ctrl_isproject = $request->isproject_id;
                    $statsQuery = (clone $query);
                }
                if ($request->source_id != "") {
                    $query->where('source', $request->source_id);
                    $ctrl_source = $request->source_id;
                    $statsQuery = (clone $query);
                }
                if ($request->owner_id != "") {
                    $query->where('owner', $request->owner_id);
                    $ctrl_owner = $request->owner_id;
                    $statsQuery = (clone $query);
                }


                if ($request->followupdt_filter != "") {
                    $query->where('follow_up_date', date('Y-m-d', strtotime($request->followupdt_filter)));
                    $ctrl_followupdt_filter = $request->followupdt_filter;
                    $statsQuery = (clone $query);
                }
                if ($request->brand_id != "") {
                    $query->where('tags', 'like', '%' . $request->brand_id . '%');
                    $ctrl_brand = $request->brand_id;
                    $statsQuery = (clone $query);
                }




                if ($request->sales != "") {
                    $total_lead = DB::table('sys_crm_leads')->where('owner', $request->sales)->wherein('status', [1, 2, 0])->where('company_id', $request->com)->pluck('id');
                    $query->wherein('id', $total_lead);
                    if ($request->status != "") {
                        if ($request->status == "new") {
                            $oneMonthsAgo = Carbon::now()->subMonths(1)->format('Y-m-d');
                            $query->where('date', '>=', $oneMonthsAgo);
                        }
                        if ($request->status == "unqualified") {
                            $query->wherein('status', [3]);
                        }
                        if ($request->status == "qualified") {
                            $query->wherein('status', [2, 0]);
                        }
                        if ($request->status == "quote") {
                            $query->where('status', 0);
                        }
                        if ($request->status == "win") {
                            $converted_deal = SysCrmLeads::select('deal_id')->where('company_id', $request->com)->where('owner', $request->sales)->where('status', 0)->get();
                            $total_win = SysCrmDeals::where('stage', 4)->wherein('id', $converted_deal->pluck('deal_id'))->pluck('id');
                            $query->wherein('deal_id', $total_win);
                        }
                        if ($request->status == "invoice") {
                            $converted_deal = SysCrmLeads::select('deal_id')->where('company_id', $request->com)->where('owner', $request->sales)->where('status', 0)->get();
                            $total_invoice = SysCrmDealTrackApprovalInvoice::where('status', 1)->wherein('deal_id', $converted_deal->pluck('deal_id'));
                            $query->wherein('deal_id', $total_invoice);
                        }
                        if ($request->status == "closed") {
                            $converted_deal = SysCrmLeads::select('deal_id')->where('company_id', $request->com)->where('owner', $request->sales)->where('status', 0)->get();
                            $total_deal_closed = SysCrmDealTrackApprovalReceivables::where('status', 1)->wherein('deal_id', $converted_deal->pluck('deal_id'));
                            $query->wherein('deal_id', $total_deal_closed);
                        }
                    }
                }


                if (Auth::user()->role_id != 1 && Auth::user()->role_id != 2 && Auth::user()->role_id != 35) {
                    $query->where('owner', Auth::user()->id);
                    $statsQuery = (clone $query);
                }




                // Get total count for filtered leads
                $lead_stats['total_leads'] = $statsQuery->count();

                $lead_stats['statusCounts'] = (clone $statsQuery)
                    ->select('status', DB::raw('count(*) as count'))
                    ->groupBy('status')
                    ->pluck('count', 'status')
                    ->toArray();


                // Average aging days calculation
                $avgAgingDays = (clone $statsQuery)
                    ->select(DB::raw("
        AVG(
            TIMESTAMPDIFF(DAY, created_at, last_updated) / NULLIF(lead_update_count, 0)
        ) as avg_aging_days
    "))
                    ->value('avg_aging_days');

                // Store in $lead_stats
                $lead_stats['avg_aging_days'] = round($avgAgingDays, 2);

                $lead_stats['sub_statusCounts'] = (clone $statsQuery)
                    ->select('sub_status', DB::raw('count(*) as count'))
                    ->where('status', '<>', 0)
                    ->groupBy('sub_status')
                    ->pluck('count', 'sub_status')
                    ->toArray();



                $subQuery = (clone $statsQuery)->getQuery(); // converts Eloquent to Query\Builder

                // Step 3: Wrap as subquery with alias
                $lead_stats['deals_statusCounts'] = DB::table(DB::raw("({$subQuery->toSql()}) as leads"))
                    ->mergeBindings($subQuery) // Critical: to bind whereIn bindings properly
                    ->join('sys_crm_deals as deals', 'leads.deal_id', '=', 'deals.id')
                    ->select('deals.stage', DB::raw('COUNT(*) as count'))
                    ->where('leads.status', 0)
                    ->groupBy('deals.stage')
                    ->pluck('count', 'deals.stage')
                    ->toArray();



                $leads = $query->orderby('id', 'desc')->paginate(200);

                if (session('logged_session_data.company_id') != 1) {
                    $con_lead = SysCrmLeads::where('status', 0)->wherein('company_id', $r[0])->pluck('deal_id');
                } else {
                    $con_lead = SysCrmLeads::where('status', 0)->pluck('deal_id');
                }

                $deal_det = SysCrmDeals::select('sys_crm_deals.id', 'sys_crm_deals.stage', 't.accounts', 't.sales', 't.purchease', 't.invoice', 't.delivery', 't.receivables')->leftjoin('sys_crm_deal_track as t', 't.deal_id', 'sys_crm_deals.id')->wherein('sys_crm_deals.id', $con_lead)->get();
            } else {

                $query = SysCrmLeads::wherein('status', [0, 1, 2, 3, 4, 10]);


                $r = SysHelper::get_data_by_role();
                if (session('logged_session_data.company_id') != 1) {
                    $query->wherein('company_id', $r[0]);
                }


                $statsQuery = (clone $query);

                if (Auth::user()->role_id != 1 && Auth::user()->role_id != 2 && Auth::user()->role_id != 35) {
                    $query->where('owner', Auth::user()->id);

                    $statsQuery = (clone $query);
                }


                $lead_stats['total_leads'] = $statsQuery->count();

                $lead_stats['statusCounts'] = (clone $statsQuery)
                    ->select('status', DB::raw('count(*) as count'))
                    ->groupBy('status')
                    ->pluck('count', 'status')
                    ->toArray();


                // Average aging days calculation
                $avgAgingDays = (clone $statsQuery)
                    ->select(DB::raw("
        AVG(
            TIMESTAMPDIFF(DAY, created_at, last_updated) / NULLIF(lead_update_count, 0)
        ) as avg_aging_days
    "))
                    ->value('avg_aging_days');

                // Store in $lead_stats
                $lead_stats['avg_aging_days'] = round($avgAgingDays, 2);


                $lead_stats['sub_statusCounts'] = (clone $statsQuery)
                    ->select('sub_status', DB::raw('count(*) as count'))
                    ->where('status', '<>', 0)
                    ->groupBy('sub_status')
                    ->pluck('count', 'sub_status')
                    ->toArray();


                $subQuery = (clone $statsQuery)->getQuery(); // converts Eloquent to Query\Builder

                $lead_stats['deals_statusCounts'] = DB::table(DB::raw("({$subQuery->toSql()}) as leads"))
                    ->mergeBindings($subQuery) // Critical: to bind whereIn bindings properly
                    ->join('sys_crm_deals as deals', 'leads.deal_id', '=', 'deals.id')
                    ->select('deals.stage', DB::raw('COUNT(*) as count'))
                    ->where('leads.status', 0)
                    ->groupBy('deals.stage')
                    ->pluck('count', 'deals.stage')
                    ->toArray();


                //$leads = $query->orderby('id','desc')->get();
                $leads = $query->orderby('id', 'desc')->paginate(200);

                if (session('logged_session_data.company_id') != 1) {
                    $con_lead = SysCrmLeads::where('status', 0)->wherein('company_id', $r[0])->pluck('deal_id');
                } else {
                    $con_lead = SysCrmLeads::where('status', 0)->pluck('deal_id');
                }
                $deal_det = SysCrmDeals::select('sys_crm_deals.id', 'sys_crm_deals.stage', 't.accounts', 't.sales', 't.purchease', 't.invoice', 't.delivery', 't.receivables')->leftjoin('sys_crm_deal_track as t', 't.deal_id', 'sys_crm_deals.id')->wherein('sys_crm_deals.id', $con_lead)->get();
            }

            $active_id = $id;
            $selectedLead = [];


            $action = false;
            $editData = [];


            if ($request->has('lead_action')) {
                $poAction = $request->input('lead_action');
             

                if ($poAction === 'add') {
                    $action = 'add';
                } elseif ($poAction === 'edit') {
                    $action = 'edit';
                    $editData = $this->edit($active_id); // Get all data for editing
                }
            } else {
                if ($id != "show" && $id != null) {
                    $selectedLead = $this->getleadData($id);
                } else {
                    $firstRecord = $leads->first();

                    if ($firstRecord) {
                        $active_id = $firstRecord->id;
                        $selectedLead = $this->getleadData($firstRecord->id);
                    }
                }
            }



            return view('backEnd.crm.LeadList', compact('leads', 'vendors', 'staff', 'ctrl_cust_id', 'ctrl_status', 'ctrl_source', 'ctrl_owner', 'ctrl_date', 'ctrl_date2', 'ctrl_lead_id', 'ctrl_isproject', 'brand', 'ctrl_brand', 'filter_by', 'currency', 'product', 'designation', 'country', 'company', 'paymentterms', 'sales_person', 'deal_det', 'ctrl_sub_status', 'ctrl_region_id', 'lead_stats', 'ctrl_followupdt_filter', 'selectedLead', 'active_id', 'action', 'editData'));
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function edit($id)
    {
        try {
            $edit = SysCrmLeads::where('id', $id)->first();
            return compact('edit');
            // return view('backEnd.crm.LeadForm', compact('currency', 'vendors', 'staff', 'edit', 'brand', 'product', 'company'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    public function getleadData($id)
    {
        try {
            $currency = SysCurrencySettings::select('id', 'code')->get();
            $company = SysCompany::orderby('sort_id', 'asc')->get();

            $staff = SmStaff::select('user_id', 'full_name')->get();

            $leads = SysCrmLeads::where('id', $id)->first();
            $edit = SysCrmLeads::where('id', $id)->first();
            $vendors = SysHelper::get_customer_list_deal_lead();
            $brand = SysBrand::select('id', 'title')->orderby('title', 'asc')->get();

            $designation = SmDesignation::select('title')->where('active_status', 1)->get();
            $country = SysCountries::select('id', 'name', 'iso3')->get();


            $comments = SysCrmLeadsComments::where('lead_id', $id)->orderBy('id', 'DESC')->get();
            $paymentterms = SysPaymentTerms::where('active_status', '=', '1')->get();
            if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 35) {
                $sales_person = SmStaff::select('user_id', 'full_name')->where('active_status', 1)->orderby('full_name', 'asc')->get();
            } else {
                $sales_person = SmStaff::select('user_id', 'full_name')->where('user_id', Auth::user()->id)->orderby('full_name', 'asc')->get();
            }

            return compact('currency', 'company', 'staff', 'edit', 'leads', 'comments', 'vendors', 'brand', 'designation', 'country', 'paymentterms', 'sales_person');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function view($id)
    {
        try {
            $currency = SysCurrencySettings::select('id', 'code')->get();
            $company = SysCompany::orderby('sort_id', 'asc')->get();

            $staff = SmStaff::select('user_id', 'full_name')->get();

            $leads = SysCrmLeads::where('id', $id)->first();
            $edit = SysCrmLeads::where('id', $id)->first();
            $vendors = SysHelper::get_customer_list_deal_lead();
            $brand = SysBrand::select('id', 'title')->orderby('title', 'asc')->get();

            $designation = SmDesignation::select('title')->where('active_status', 1)->get();
            $country = SysCountries::select('id', 'name', 'iso3')->get();


            $comments = SysCrmLeadsComments::where('lead_id', $id)->orderBy('id', 'DESC')->get();
            $paymentterms = SysPaymentTerms::where('active_status', '=', '1')->get();
            if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 35) {
                $sales_person = SmStaff::select('user_id', 'full_name')->where('active_status', 1)->orderby('full_name', 'asc')->get();
            } else {
                $sales_person = SmStaff::select('user_id', 'full_name')->where('user_id', Auth::user()->id)->orderby('full_name', 'asc')->get();
            }

            return view('backEnd.crm.LeadView', compact('currency', 'company', 'staff', 'edit', 'leads', 'comments', 'vendors', 'brand', 'designation', 'country', 'paymentterms', 'sales_person'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function update(Request $request, $id)
    {

        $tags = "";
        if ($request->tags != "") {
            $tags = implode(",", $request->tags);
        }
        //$doc_file = $request->file_name;
        // if ($request->file('doc') != "") {
        //     $file = $request->file('doc');
        //     $doc_file = md5(time()) . "." . $file->getclientoriginalextension();
        //     $file->move('public/uploads/crm_lead_doc/', $doc_file);
        //     $doc_file = $doc_file;
        // }
        $doc_file = "";
        if ($request->file('doc') != "") {
            $files = $request->file('doc');
            for ($i = 0; $i < count($files); $i++) {
                $file1 = $files[$i];
                $doc_file = md5(time()) . "_lead_" . $i . "." . $file1->getclientoriginalextension();
                $file1->move('public/uploads/crm_lead_doc/', $doc_file);
                $lpo[] = $doc_file;
            }
            $doc_file = implode("|", $lpo);
        }

        $ssi = SysCrmLeads::find($id);
        $ssi->date = Carbon::createFromFormat('d/m/Y', $request->date)->format('Y-m-d');
        $ssi->lead_name = $request->lead_name;
        $ssi->cust_id = $request->company_name;
        $ssi->cust_name = $request->cust_name;
        $ssi->cust_no = $request->cust_no;
        $ssi->cust_email = $request->cust_email;
        $ssi->cust_designation = $request->cust_designation;
        $ssi->address = $request->address;
        $ssi->country = $request->country;
        $ssi->source = $request->source;
        $ssi->source_o = $request->source_o;
        $ssi->owner = $request->owner;
        if ($doc_file != "") {
            $ssi->doc = $doc_file;
        }
        //$ssi->doc = $doc_file;
        $ssi->tags = $tags;
        $ssi->note = $request->note;
        $ssi->status = $request->status;

        if ($request->status == 1) {
            $ssi->sub_status = 1;
        } else if ($request->status == 2) {
            $ssi->sub_status = 2;
        } else {
            $ssi->sub_status = 0;
        }

        $ssi->isproject = $request->isproject;
        $ssi->updated_by = Auth::user()->id;
        $ssi->updated_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');


        if ($ssi->company_id != $request->company) {
            if ($ssi->deal_id == 0 || $ssi->deal_id == null) {

                $new_code =  SysHelper::get_new_code_lead('sys_crm_leads', 'LD', 'code', $request->company);
                $cc = SysCompany::where('id', $ssi->company_id)->value('company_name');
                $nc = SysCompany::where('id', $request->company)->value('company_name');
                DB::table('sys_crm_leads_comments')->insert(
                    [
                        'lead_id' => $id,
                        'comments' => '<span class=text-danger>Company Changed from ' . $cc . ' to ' . $nc . ' and code changed from ' . $ssi->code . ' to ' . $new_code . '</span>',
                        'status' => 1,
                        'created_by' => Auth::user()->id,
                        'created_at' => Carbon::now('+04:00'),
                    ]
                );

                $ssi->code = $new_code;
                $ssi->company_id = $request->company;
            }else{
                Toastr::warning('Company cannot be changed because a deal has already been created.', 'Action Failed');
                return redirect()->back();
            }
        }

        //$ssi->company_id = $request->company;        
        $results = $ssi->update();


        SysHelper::set_user_custsupp($request->owner, $request->company_name);

        if ($results) {
            Toastr::success('Lead has been updated successfully', 'Success');
            return redirect('crm-leads/show/' . $ssi->id);
        } else {
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function getcustomername(Request $request)
    {
        $input = $request->all();

        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            // $vendors_query = SysCustSuppl::select('id','code','name')->where('catid',1); // 1 customers, 2 suppliers
            // if(Auth::user()->role_id != 1){
            //     $vendors_query->where('created_by', Auth::user()->id);
            // }
            // $vendors = $vendors_query->get();
            //$customers = SysCustSuppl::select('sys_cust_suppl.id','sys_cust_suppl.customer_salutation','sys_cust_suppl.first_name','sys_cust_suppl.last_name','sys_cust_suppl.mobile',//'sys_cust_suppl.email','sys_cust_suppl.address','sys_cust_suppl.address2','sys_cust_suppl.designation','sys_countries.name','sys_states.name as statename','sys_cust_suppl.city',//'sys_cust_suppl.zip_code','sys_cust_suppl.account_type')
            //->leftjoin('sys_countries','sys_countries.id','sys_cust_suppl.vat_country')
            //->leftjoin('sys_states','sys_states.id','sys_cust_suppl.vat_state')
            //->where('sys_cust_suppl.id',$request->id)->where('company_id',session('logged_session_data.company_id'))->get();
            $customers = DB::table('sys_cust_suppl as cs')->select('cs.id', 'cs.customer_salutation', 'cs.first_name', 'cs.last_name', 'cs.mobile', 'cs.email', 'csa.address', 'csa.address2', 'cs.designation', 'c.name', 's.name as statename', 'csa.city', 'csa.zip_code', 'cs.account_type', 'cs.payment_terms')
                ->leftjoin('sys_cust_suppl_addressbook as csa', 'csa.cust_suppl_id', 'cs.id')
                ->leftjoin('sys_countries as c', 'c.id', 'csa.country')
                ->leftjoin('sys_states as s', 's.id', 'csa.state')
                ->where('cs.id', $request->id)
                //->whereRaw("find_in_set(session('logged_session_data.company_id'),cs.company_access)")
                ->limit(1)->orderby('csa.id', 'desc')->get();
            $bug = 0;
        } catch (\Exception $e) {
            return $e;
            $bug = $e->errorInfo[1];
        }
        if ($bug == 0) {
            return json_encode(array('data' => $customers));
        } else {
            $retData = 'ERROR';
            return json_encode(array('data' => $retData));
        }
    }
    public function getcustomername_deal(Request $request)
    {
        $input = $request->all();

        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $customers = DB::table('sys_cust_suppl as cs')->select('cs.id', 'cs.customer_salutation', 'cs.first_name', 'cs.last_name', 'cs.mobile', 'cs.email', 'csa.address', 'csa.address2', 'cs.designation', 'c.name', 's.name as statename', 'csa.city', 'csa.zip_code', 'cs.account_type')
                ->leftjoin('sys_cust_suppl_addressbook as csa', 'csa.cust_suppl_id', 'cs.id')
                ->leftjoin('sys_countries as c', 'c.id', 'csa.country')
                ->leftjoin('sys_states as s', 's.id', 'csa.state')
                ->where('cs.name', $request->name)
                ->limit(1)->orderby('csa.id', 'desc')->get();
            $bug = 0;
        } catch (\Exception $e) {
            return $e;
            $bug = $e->errorInfo[1];
        }
        if ($bug == 0) {
            return json_encode(array('data' => $customers));
        } else {
            $retData = 'ERROR';
            return json_encode(array('data' => $retData));
        }
    }

    public function crmleadscommentsadd(Request $request)
    {
        try {
            $doc_file = "";
            if ($request->file('commentsdoc') != "") {
                $file = $request->file('commentsdoc');
                $doc_file = md5(time()) . "." . $file->getclientoriginalextension();
                $file->move('public/uploads/crm_lead_doc/', $doc_file);
            }


            DB::table('sys_crm_leads_comments')->insert(
                [
                    'lead_id' => $request->commentsid,
                    'comments' => $request->comments,
                    'commentsdoc' => $doc_file,
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                ]
            );

            SysHelper::lead_updated_at($request->commentsid);

            Toastr::success('Comments has been added successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }


    public function crmleadscommentsdelete($id)
    {
        try {
            // DB::table('sys_crm_leads_comments')->where('id', $id)->delete();
            $comment = SysCrmLeadsComments::find($id);
            $comment->softDelete(auth()->id()); // marks deleted_by + deleted_at

            Toastr::success('Comments has been deleted successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function crmleadscommentsrestore($id)
    {
        try {
            // DB::table('sys_crm_leads_comments')->where('id', $id)->delete();
            $comment = SysCrmLeadsComments::find($id);
            $comment->restoreComment();


            Toastr::success('Comments has been deleted successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }
    public function crmleadsupdatestatus(Request $request)
    {

        try {

            DB::beginTransaction(); // Start transaction
            $updateData = [
                'status' => $request->status,
                'sub_status' => $request->sub_status,
            ];

            // Add sub_status_comment only for specific sub_status values
            if (in_array($request->sub_status, [8, 12, 14])) {
                $updateData['sub_status_comment'] = $request->comments;
            }

            // Add follow_up_date only when status is 4
            if ($request->status == 4) {
                $updateData['follow_up_date'] = SysHelper::normalizeToYmd($request->follow_up_date);
            }

            $flag = false;

            if ($request->status == 2 && $request->qualified_deal_no != "") {
                $updateData['deal_id'] = SysHelper::get_dealid_from_code($request->qualified_deal_no);
                $flag = true;
            }


            // Single DB update call
            DB::table('sys_crm_leads')
                ->where('id', $request->id)
                ->update($updateData);


            $status = (int) $request->status;
            $commentText = trim($request->comments);

            $statusMap = [
                1 => ['label' => 'New', 'color' => 'text-primary'],
                2 => ['label' => 'Qualified', 'color' => 'text-success'],
                3 => ['label' => 'Unqualified', 'color' => 'text-danger'],
                4 => ['label' => 'Pending Response', 'color' => 'text-warning'],
                10 => ['label' => 'Closed', 'color' => 'text-danger'],
            ];


            // Use defaults if status not matched
            $label = isset($statusMap[$status]) ? $statusMap[$status]['label'] : '';
            $colorClass = isset($statusMap[$status]) ? $statusMap[$status]['color'] : '';

            // Build the formatted comment
            $formattedComment = '<span class="' . $colorClass . '">[' . $label . '] ' . e($commentText) . '</span>';


            DB::table('sys_crm_leads_comments')->insert(
                [
                    'lead_id' => $request->lead_id,
                    'comments' => $formattedComment,
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                ]
            );


            SysHelper::lead_updated_at($request->id);

            if ($request->status == 2 && !$flag) {

                // Call convert() and get URL
                $dealUrl = $this->convert($request->id, true); // ✅ new version that returns URL
                DB::commit();

                return response()->json(['data' => 'REDIRECT', 'url' => $dealUrl]);
            }

            $bug = 0;
        } catch (\Throwable $th) {
            return $th;
        }
        if ($bug == 0) {

            if ($request->status == 1) {

                $dealId = DB::table('sys_crm_leads')
                    ->where('id', $request->lead_id)
                    ->value('deal_id');

                if ($dealId) {

                    $exists = SysCrmQuoteItems::where('deal_id', $dealId)->exists();

                    if ($exists) {


                        return response()->json(['data' => 'QC']);
                    } else {

                        // Delete the corresponding deal from crm_deals table
                        DB::table('sys_crm_deals')->where('id', $dealId)->delete();

                        // Optionally, set deal_id to null in the lead
                        DB::table('sys_crm_leads')->where('id', $request->lead_id)->update(['deal_id' => 0]);
                    }
                }
            }

            DB::commit();
            return response()->json(['data' => 'OK']);
        } else {
            return response()->json(['data' => 'ERROR']);
        }
    }

    public function convert($id, $returnUrlOnly = false)
    {
        try {
            $leads = SysCrmLeads::where('id', $id)->wherenotin('status', [0])->get();
            $comments = SysCrmLeadsComments::where('lead_id', $id)->orderBy('id', 'DESC')->get();


            if (count($leads) > 0) {
                DB::beginTransaction();
                foreach ($leads as $val) {
                    $deal_id = DB::table('sys_crm_deals')->insertGetId([
                        'code' => SysHelper::get_new_code_lead('sys_crm_deals', 'DL', 'code', $val->company_id),
                        'date' => $val->date,
                        'deal_name' => $val->lead_name,
                        'cust_id' => $val->cust_id,
                        'cust_name' => $val->cust_name,
                        'cust_no' => $val->cust_no,
                        'cust_email' => $val->cust_email,
                        'company_name' => $val->company_name,
                        'deal_value' => '0.00',
                        'source' => $val->source,
                        'source_o' => $val->source_o,
                        'tags' => $val->tags,
                        'stage' => 1,
                        'owner' => $val->owner,
                        'doc' => $val->doc,
                        'note' => $val->note . " (Converted from Lead #" . $val->code . ") ",
                        'isproject' => $val->isproject,
                        'estimated_close_date' => date('Y-m-d'),
                        'created_by' => Auth::user()->id,
                        'created_at' => Carbon::now('+04:00')->format('Y-m-d H:i:s'),
                        'lead_id' => $val->id,
                        'designation' => $val->cust_designation,
                        'address' => $val->address,
                        'status' => 1,
                        'company_id' => $val->company_id,
                    ]);
                }
                foreach ($comments as $val) {
                    DB::table('sys_crm_deals_comments')->insert([
                        'deal_id' => $deal_id,
                        'comments' => $val->comments,
                        'status' => $val->status,
                        'created_by' => Auth::user()->id,
                    ]);
                }
                DB::table('sys_crm_leads')->where('id', $id)
                    ->update([
                        'status' => 0,
                        'sub_status' => 2,
                        'deal_id' => $deal_id,
                        'updated_by' => Auth::user()->id,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                DB::commit();
                SysHelper::lead_updated_at($deal_id);


                if ($returnUrlOnly) {
                    return url('crm-deals/show' . $deal_id); // ✅ return URL
                }


                return redirect('crm-deals/show/' . $deal_id);
            } else {
                return redirect('crm-deals/show');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function delete(Request $request, $id)
    {

        try {

            $request->validate([
                'delete_reason' => 'required|string|max:255',
            ]);

            // Build the formatted comment
            $formattedComment = '<span class="text-danger">[Deleted] ' . e($request->delete_reason) . '</span>';


            DB::table('sys_crm_leads_comments')->insert(
                [
                    'lead_id' => $id,
                    'comments' => $formattedComment,
                    'commentsdoc' => null,
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                ]
            );

            DB::table('sys_crm_leads')
                ->where('id', $id)
                ->update(['deleted_at' => Carbon::now()]);

            // DB::table('sys_crm_leads')->where('id', $id)->delete();
            // DB::table('sys_crm_leads_comments')->where('lead_id', $id)->delete();

            Toastr::success('Lead Deleted Successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function getleadnametobrand(Request $request)
    {
        try {
            $data = SmItem::select('sys_brand.title')
                ->join('sys_brand', 'sys_brand.id', 'sm_items.brand')
                ->where('sm_items.part_number', $request->id)->get();
            if (count($data) > 0) {
                return json_encode(array('data' => $data));
            } else {
                $data = 'ERROR';
                return json_encode(array('data' => $data));
            }
        } catch (\Exception $e) {
            $data = 'ERROR';
            return json_encode(array('data' => $data));
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

    public function dashboardsalesfilter_EXCLUDE(Request $request)
    {
        $input = $request->all();

        try {

            $company_id = $request->company;
            $date_id = $request->date;

            if ($date_id == "d") {
                $revenue1 = DB::table('sys_crm_deals')->select('sys_crm_deals.id as dealid', 'deal_value', 'deal_currency', 'source', 'cust_id', 'deal_percent');
                if ($request->company == 1 || $request->company == 3) {
                    $revenue1->leftjoin('sys_crm_deal_track_approval_invoice', 'sys_crm_deal_track_approval_invoice.deal_id', 'sys_crm_deals.id')
                        ->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') = '" . date('Y-m-d') . "'")
                        ->where('sys_crm_deal_track_approval_invoice.status', 1)->where('sys_crm_deals.is_partial_invoice', 0);
                } else {
                    $revenue1->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') = '" . date('Y-m-d') . "'");
                }
                $revenue1->where('sys_crm_deals.stage', 4)->where('sys_crm_deals.company_id', $request->company);

                $forecast1 = DB::table('sys_crm_deals')->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') = '" . date('Y-m-d') . "'")->wherein('stage', [3])->where('sys_crm_deals.company_id', $request->company);
            }
            if ($date_id == "m") {
                $revenue1 = DB::table('sys_crm_deals')->select('deal_value', 'deal_currency', 'source', 'cust_id');
                if ($request->company == 1 || $request->company == 3) {
                    $revenue1->leftjoin('sys_crm_deal_track_approval_invoice', 'sys_crm_deal_track_approval_invoice.deal_id', 'sys_crm_deals.id')
                        ->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m') = '" . date('Y-m') . "'")
                        ->where('sys_crm_deal_track_approval_invoice.status', 1)->where('sys_crm_deals.is_partial_invoice', 0);
                } else {
                    $revenue1->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m') = '" . date('Y-m') . "'");
                }
                $revenue1->where('sys_crm_deals.stage', 4)->where('sys_crm_deals.company_id', $request->company);

                $forecast1 = DB::table('sys_crm_deals')->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '" . date('Y-m') . "'")->wherein('stage', [3])->where('sys_crm_deals.company_id', $request->company);
            }
            if ($date_id == "y") {
                $revenue1 = DB::table('sys_crm_deals')->select('deal_value', 'deal_currency', 'source', 'cust_id');
                if ($request->company == 1 || $request->company == 3) {
                    $revenue1->leftjoin('sys_crm_deal_track_approval_invoice', 'sys_crm_deal_track_approval_invoice.deal_id', 'sys_crm_deals.id')
                        ->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y') = '" . date('Y') . "'")
                        ->where('sys_crm_deal_track_approval_invoice.status', 1)->where('sys_crm_deals.is_partial_invoice', 0);
                } else {
                    $revenue1->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y') = '" . date('Y') . "'");
                }
                $revenue1->where('sys_crm_deals.stage', 4)->where('sys_crm_deals.company_id', $request->company);

                $forecast1 = DB::table('sys_crm_deals')->whereRaw("DATE_FORMAT(created_at, '%Y') = '" . date('Y') . "'")->wherein('stage', [3])->where('sys_crm_deals.company_id', $request->company);
            }
            if ($date_id == "q") {
                $start_date = date('Y-m-d', strtotime('first day of this month - 3 months'));
                $end_date = date('Y-m-d', strtotime('last day of this month'));

                $revenue1 = DB::table('sys_crm_deals')->select('deal_value', 'deal_currency', 'source', 'cust_id');
                if ($request->company == 1 || $request->company == 3) {
                    $revenue1->leftjoin('sys_crm_deal_track_approval_invoice', 'sys_crm_deal_track_approval_invoice.deal_id', 'sys_crm_deals.id')
                        ->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '" . $start_date . "' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '" . $end_date . "'")
                        ->where('sys_crm_deal_track_approval_invoice.status', 1)->where('sys_crm_deals.is_partial_invoice', 0);
                } else {
                    $revenue1->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') >= '" . $start_date . "' and DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') <= '" . $end_date . "'");
                }
                $revenue1->where('sys_crm_deals.stage', 4)->where('sys_crm_deals.company_id', $request->company);

                $forecast1 = DB::table('sys_crm_deals')->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') >= '" . $start_date . "' and DATE_FORMAT(created_at, '%Y-%m-%d') <= '" . $end_date . "'")->wherein('stage', [3])->where('sys_crm_deals.company_id', $request->company);
            }

            if (Auth::user()->role_id == 1) { //admin
                $revenue = $revenue1->get();
                $forecast = $forecast1->get();
            } else if (Auth::user()->id == 33) { //jacob
                $teams = array(33, 31, 59);
                $revenue = $revenue1->wherein('sys_crm_deals.owner', $teams)->get();
                $forecast = $forecast1->wherein('sys_crm_deals.owner', $teams)->get();
            } else if (Auth::user()->id == 27) { //monica
                //$teams= array(27,30,54,62);
                $teams = array(27);
                $revenue = $revenue1->wherein('sys_crm_deals.owner', $teams)->get();
                $forecast = $forecast1->wherein('sys_crm_deals.owner', $teams)->get();
            } else if (Auth::user()->id == 44) { //rajiv
                $teams = array(44, 45, 34, 32);
                $revenue = $revenue1->wherein('sys_crm_deals.owner', $teams)->get();
                $forecast = $forecast1->wherein('sys_crm_deals.owner', $teams)->get();
            } else if (Auth::user()->id == 26 || Auth::user()->id == 36 || Auth::user()->id == 112 || Auth::user()->id == 111) { //26 Naeem & 36 Arianne
                $teams = array(26, 36, 112, 111);
                $revenue = $revenue1->wherein('sys_crm_deals.owner', $teams)->get();
                $forecast = $forecast1->wherein('sys_crm_deals.owner', $teams)->get();
            } else {
                $revenue = $revenue1->where('sys_crm_deals.owner', Auth::user()->id)->get();
                $forecast = $forecast1->where('sys_crm_deals.owner', Auth::user()->id)->get();
            }

            $retrevenue = 0;
            $retforecast = 0;
            if (count($revenue) > 0) {
                foreach ($revenue as $dt) {
                    //$retrevenue+= SysHelper::get_aed_amount($dt->deal_currency,$dt->deal_value);
                    if (in_array($dt->dealid, [8690, 8660])) {
                        $retrevenue += SysHelper::get_aed_amount($dt->deal_currency, $dt->deal_value);
                    } else {
                        if ($dt->source == "Fulfillment") {
                            $retrevenue += SysHelper::get_aed_amount($dt->deal_currency, ($dt->deal_value * 20 / 100));
                        } else if (in_array($dt->cust_id, [2568, 4258, 4382, 5322, 7347, 8144, 8145, 8146, 3711, 4089, 8142])) {
                            $retrevenue += SysHelper::get_aed_amount($dt->deal_currency, ($dt->deal_value * 20 / 100));
                        } else if (in_array($dt->cust_id, [8866])) {
                            $retrevenue += SysHelper::get_aed_amount($dt->deal_currency, ($dt->deal_value * 30 / 100));
                        } else {
                            $retrevenue += SysHelper::get_aed_amount($dt->deal_currency, $dt->deal_value);
                        }
                    }
                }
            }
            if (count($forecast) > 0) {
                foreach ($forecast as $dt) {
                    $retforecast += SysHelper::get_aed_amount($dt->deal_currency, $dt->deal_value);
                }
            }

            //$sf = $revenue->where('source','Fulfillment')->sum('deal_value')*20/100;       

            $ret = [SysHelper::com_curr_format($retrevenue, 2, '.', ','), SysHelper::com_curr_format($retforecast, 2, '.', ',')];
            return json_encode(array('data' => $ret));
        } catch (\Exception $e) {
            return $e;
            $retData = $e;
            return json_encode(array('data' => $retData));
            //Toastr::error('Operation Failed', 'Failed');
            //return redirect()->back(); 
        }
    }

    public function dashboardleadfilter(Request $request)
    {
        try {
            $ret = SysHelper::get_lead_filter($request->date, $request->company);
            return json_encode(array('data' => $ret));
        } catch (\Exception $e) {
            return $e;
            $retData = $e;
            return json_encode(array('data' => $retData));
        }
    }

    public function getComments(Request $request, $id)
    {
        try {
            // Get lead with deal_id loaded
            $lead = SysCrmLeads::findOrFail($id);
            $leadComments = SysCrmLeadsComments::with('createdby:id,user_id,first_name,last_name')->where('lead_id', $id)
                ->get()
                ->map(function ($comment) {
                    $comment->source = 'lead'; // Tag source
                    return $comment;
                });
            $dealComments = collect();

            if ($lead->deal_id) {
                // If deal_id exists, fetch comments from sys_crm_deals_comments
                $dealComments = SysCrmDealsComments::with('createdby:id,user_id,first_name,last_name')
                    ->where('deal_id', $lead->deal_id)
                    ->get()
                    ->map(function ($comment) {
                        $comment->source = 'deal'; // Tag source
                        return $comment;
                    });
            }

            // Merge lead comments and deal comments
            $comments = $leadComments->merge($dealComments)->sortBy('created_at')->values()->all();

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

    public function restoreLead(Request $request, $id)
    {
        try {
            $request->validate([
                'restore_reason' => 'required|string|max:255',
            ]);

            // Build the formatted comment
            $formattedComment = '<span class="text-success">[Restored] ' . e($request->restore_reason) . '</span>';


            DB::table('sys_crm_leads_comments')->insert(
                [
                    'lead_id' => $id,
                    'comments' => $formattedComment,
                    'commentsdoc' => null,
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                ]
            );
            DB::table('sys_crm_leads')
                ->where('id', $id)
                ->update(['deleted_at' => null]);

            Toastr::success('Lead restored successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function crmleadscommentsaddAPI(Request $request)
    {
        try {
            $doc_file = null;
            if ($request->file('commentsdoc') != "") {
                $file = $request->file('commentsdoc');
                $doc_file = md5(time()) . "." . $file->getclientoriginalextension();
                $file->move('public/uploads/crm_lead_doc/', $doc_file);
            }

            $comment = SysCrmLeadsComments::create([
                'lead_id' => $request->current_lead_id,
                'comments' => $request->comment,
                'commentsdoc' => $doc_file,
                'status' => 1,
                'created_by' => Auth::user()->id,
                'created_at' => Carbon::now('+04:00'),
            ]);


            SysHelper::lead_updated_at($request->current_lead_id);




            $comment = SysCrmLeadsComments::where('id', $comment->id)->first();

            $comment->load('createdby:id,user_id,first_name,last_name'); // Load user relation

            return response()->json($comment, 201);
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
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

        $leads = SysCrmLeads::with([
            'customername:id,code,name',
            'lead_deal_code:id,code'
        ])
            ->whereIn('status', [0, 1, 2, 3, 4, 10])
            ->where(function ($query) use ($q, $formattedDate) {

                // Company restriction
                if (session('logged_session_data.company_id') != 1) {
                    $r = SysHelper::get_data_by_role();
                    $query->whereIn('company_id', $r[0]);
                }

                // Owner restriction
                if (Auth::user()->role_id != 1 && Auth::user()->role_id != 2 && Auth::user()->role_id != 35) {
                    $query->where('owner', Auth::user()->id);
                }

                // Search query
                if ($q) {
                    $query->where(function ($qsub) use ($q) {
                        $qsub->where('code', 'like', "%{$q}%")
                            ->orWhere('lead_name', 'like', "%{$q}%")
                            ->orWhere('deal_id', 'like', "%{$q}%")
                            ->orWhere('tags', 'like', "%{$q}%")
                            ->orWhere('cust_name', 'like', "%{$q}%")
                             ->orWhereHas('customername', function ($qrel) use ($q) {
                                    $qrel->where('name', 'like', "%{$q}%")
                                        ->orWhere('code', 'like', "%{$q}%");
                    });
                    });
                }

                // Date filter
                if ($formattedDate) {
                    $query->orWhereDate('created_at', $formattedDate);
                }
            })
            ->orderBy('created_at', 'desc')
            ->limit(100) // example
            ->get();


        return response()->json($leads);
    }

    public function storeBasic(Request $request)
    {
        DB::beginTransaction();
        try {
            // =====================================================
            // 1. CREATE MAIN COMPANY RECORD
            // =====================================================
            $company = new SysCompany();
            
            // Basic Company Information
            $company->company_name = $request->company_name;
            $company->trade_name = $request->trade_name;
            $company->company_code = $request->company_code;
            $company->business_entity_type_id = $request->business_entity_type_id;
            $company->industry_type_id = $request->industry_type_id;
            $company->business_sector_id = $request->business_sector_id;
            $company->date_of_incorporation = $request->date_of_incorporation ? 
                Carbon::createFromFormat('d/m/Y', $request->date_of_incorporation)->format('Y-m-d') : null;
            $company->company_type = $request->company_type;
            
            // Parent Company Logic
            if ($request->company_type === 'parent') {
                $company->parent_company = $request->parent_company;
            } elseif (in_array($request->company_type, ['subsidiary', 'branch', 'sub_branch'])) {
                $company->parent_company_id = $request->parent_company_id;
            }
            
            // Address Information
            $company->country = $request->country;
            $company->state = $request->state;
            $company->city = $request->city;
            $company->area = $request->area;
            $company->building_no = $request->building_no;
            $company->floor_shop_no = $request->floor_shop_no;
            $company->company_address = $request->company_address;
            
            // Contact Information
            $company->email = $request->email;
            $company->website = $request->website;
            $company->telephone = $request->telephone;
            $company->mobile_code = $request->mobile_code;
            $company->mobile = $request->mobile;
            $company->fax = $request->fax;
            
            // Social Media
            $company->linkedin = $request->linkedin;
            $company->facebook = $request->facebook;
            $company->instagram = $request->instagram;
            $company->twitter_x = $request->twitter_x;
            $company->youtube = $request->youtube;
            $company->other_social = $request->other_social;
            
            // File Uploads (Logo, Stamp, Profile)
            $company->company_logo = $this->uploadSingleFile($request, 'company_logo', 'uploads/company/logos');
            $company->digital_stamp = $this->uploadSingleFile($request, 'digital_stamp', 'uploads/company/stamps');
            $company->company_profile = $this->uploadSingleFile($request, 'company_profile', 'uploads/company/profiles');
            
            $company->shift_id = $request->shift_id;
            $company->created_by = Auth::id();
            $company->save();
            
            $companyId = $company->id;
            
            // =====================================================
            // 2. COMPANY SETTINGS
            // =====================================================
            $setting = new SysCompanySetting();
            $setting->company_id = $companyId;
            $setting->currency = $request->currency;
            $setting->currency_symbol = $request->currency_symbol;
            $setting->currency_digit = $request->currency_digit;
            $setting->r_code = $request->r_code;
            $setting->p_code = $request->p_code;
            $setting->book_closed = $request->book_closed;
            $setting->sales_code = $request->sales_code;
            $setting->other_code = $request->other_code;
            $setting->is_customer_code = $request->has('is_customer_code') ? 1 : 0;
            $setting->is_supplier_code = $request->has('is_supplier_code') ? 1 : 0;
            $setting->is_account_code = $request->has('is_account_code') ? 1 : 0;
            $setting->is_subaccount_code = $request->has('is_subaccount_code') ? 1 : 0;
            $setting->save();
            
            // =====================================================
            // 3. UAE COMPLIANCE
            // =====================================================
            if ($request->country == 'UAE' || $request->country == '231') { // 231 is UAE ID
                $compliance = new SysCompanyCompliance();
                $compliance->company_id = $companyId;
                $compliance->trade_license_no = $request->trade_license_no;
                $compliance->license_issue_date = $this->parseDate($request->license_issue_date);
                $compliance->license_expiry_date = $this->parseDate($request->license_expiry_date);
                $compliance->issuing_authority = $request->issuing_authority;
                $compliance->business_license_upload = $this->uploadSingleFile($request, 'business_license_upload', 'uploads/company/compliance');
                
                // Tax Information
                $compliance->tax_applicable = $request->tax_applicable;
                $compliance->vat_registration_number = $request->vat_registration_number;
                $compliance->vat_percentage = $request->vat_percentage;
                $compliance->vat_date = $this->parseDate($request->vat_date);
                $compliance->vat_issuing_authority = $request->vat_issuing_authority;
                $compliance->vat_certificate = $this->uploadSingleFile($request, 'vat_certificate', 'uploads/company/compliance');
                
                // Corporate Tax
                $compliance->corporate_tax_number = $request->corporate_tax_number;
                $compliance->corporate_tax_vat = $request->corporate_tax_vat;
                $compliance->corporate_tax_date = $this->parseDate($request->corporate_tax_date);
                $compliance->ct_issuing_authority = $request->ct_issuing_authority;
                $compliance->corporate_tax_certificate = $this->uploadSingleFile($request, 'corporate_tax_certificate', 'uploads/company/compliance');
                
                // Establishment Card
                $compliance->establishment_number = $request->establishment_number;
                $compliance->establishment_date = $this->parseDate($request->establishment_date);
                $compliance->establishment_expiry = $this->parseDate($request->establishment_expiry);
                $compliance->establishment_file = $this->uploadSingleFile($request, 'establishment_file', 'uploads/company/compliance');
                
                // Immigration Card
                $compliance->immigration_number = $request->immigration_number;
                $compliance->immigration_date = $this->parseDate($request->immigration_date);
                $compliance->immigration_expiry = $this->parseDate($request->immigration_expiry);
                $compliance->immigration_file = $this->uploadSingleFile($request, 'immigration_file', 'uploads/company/compliance');
                
                // Labour Card
                $compliance->labour_number = $request->labour_number;
                $compliance->labour_date = $this->parseDate($request->labour_date);
                $compliance->labour_expiry = $this->parseDate($request->labour_expiry);
                $compliance->labour_file = $this->uploadSingleFile($request, 'labour_file', 'uploads/company/compliance');
                
                // Chamber of Commerce
                $compliance->chamber_number = $request->chamber_number;
                $compliance->chamber_date = $this->parseDate($request->chamber_date);
                $compliance->chamber_expiry = $this->parseDate($request->chamber_expiry);
                $compliance->chamber_file = $this->uploadSingleFile($request, 'chamber_file', 'uploads/company/compliance');
                
                // Insurance Certificate
                $compliance->insurance_certificate_number = $request->insurance_certificate_number;
                $compliance->insurance_certificate_date = $this->parseDate($request->insurance_certificate_date);
                $compliance->insurance_certificate_expiry = $this->parseDate($request->insurance_certificate_expiry);
                $compliance->insurance_file = $this->uploadSingleFile($request, 'insurance_file', 'uploads/company/compliance');
                
                // MOA/AOA
                $compliance->moa_aoa_number = $request->moa_aoa_number;
                $compliance->moa_aoa_expiry = $this->parseDate($request->moa_aoa_expiry);
                $compliance->moa_aoa_file = $this->uploadSingleFile($request, 'moa_aoa_file', 'uploads/company/compliance');
                
                // Board Resolution
                $compliance->board_resolution_number = $request->board_resolution_number;
                $compliance->board_resolution_expiry = $this->parseDate($request->board_resolution_expiry);
                $compliance->board_resolution_file = $this->uploadSingleFile($request, 'board_resolution_file', 'uploads/company/compliance');
                
                // POA
                $compliance->poa_number = $request->poa_number;
                $compliance->poa_expiry = $this->parseDate($request->poa_expiry);
                $compliance->poa_file = $this->uploadSingleFile($request, 'poa_file', 'uploads/company/compliance');
                
                $compliance->save();
            }
            
            // =====================================================
            // 4. HR PAYROLL SETTINGS
            // =====================================================
            $hrPayroll = new SysCompanyHrPayrollSetting();
            $hrPayroll->company_id = $companyId;
            
            // Leave Policy
            $hrPayroll->leave_policy_type = $request->leave_policy_type;
            $hrPayroll->annual_leave = $request->annual_leave;
            $hrPayroll->sick_leave = $request->sick_leave;
            $hrPayroll->casual_leave = $request->casual_leave;
            $hrPayroll->comp_off_allowed = $request->has('comp_off_allowed') ? 1 : 0;
            $hrPayroll->carry_forward = $request->has('carry_forward') ? 1 : 0;
            $hrPayroll->max_carry_forward = $request->max_carry_forward;
            $hrPayroll->leave_encashment = $request->has('leave_encashment') ? 1 : 0;
            
            // Attendance Policy
            $hrPayroll->attendance_policy = $request->attendance_policy;
            $hrPayroll->min_working_hours = $request->min_working_hours;
            $hrPayroll->grace_period = $request->grace_period;
            $hrPayroll->half_day_after = $request->half_day_after;
            $hrPayroll->absent_below_hours = $request->absent_below_hours;
            $hrPayroll->late_mark_allowed = $request->late_mark_allowed;
            $hrPayroll->late_mark_halfday = $request->late_mark_halfday;
            $hrPayroll->auto_absent_after = $request->auto_absent_after;
            
            // Working Days
            $hrPayroll->hr_weekly_off = $request->has('hr_weekly_off') ? json_encode($request->hr_weekly_off) : null;
            
            // WPS Settings
            $hrPayroll->hr_wps_establishment_id = $request->hr_wps_establishment_id;
            $hrPayroll->hr_wps_bank = $request->hr_wps_bank;
            $hrPayroll->hr_wps_salary_file_code = $request->hr_wps_salary_file_code;
            
            // Payroll Cycle
            $hrPayroll->hr_payroll_cycle = $request->hr_payroll_cycle;
            $hrPayroll->hr_payroll_start = $request->hr_payroll_start;
            $hrPayroll->hr_payroll_end = $request->hr_payroll_end;
            
            // Gratuity
            $hrPayroll->hr_gratuity_method = $request->hr_gratuity_method;
            
            $hrPayroll->save();
            
            // =====================================================
            // 5. OWNERS (Dynamic)
            // =====================================================
            if ($request->has('owners') && is_array($request->owners)) {
                foreach ($request->owners as $index => $owner) {
                    if (empty($owner['first_name'])) continue;
                    
                    $person = new SysCompanyPeople();
                    $person->company_id = $companyId;
                    $person->type = 'owner';
                    $person->salutation = $owner['salutation'] ?? null;
                    $person->first_name = $owner['first_name'];
                    $person->last_name = $owner['last_name'] ?? null;
                    $person->mobile = $owner['mobile'] ?? null;
                    $person->email = $owner['email'] ?? null;
                    $person->designation_id = $owner['designation_id'] ?? null;
                    $person->share_percentage = $owner['share_percentage'] ?? 0;
                    $person->save();
                    
                    // Save owner documents if any
                    $this->savePersonDocumentsArray($person->id, $request, "owners.$index", 'owner');
                }
            }
            
            // =====================================================
            // 6. SPONSORS (Dynamic)
            // =====================================================
            if ($request->has('sponsors') && is_array($request->sponsors)) {
                foreach ($request->sponsors as $index => $sponsor) {
                    if (empty($sponsor['first_name'])) continue;
                    
                    $person = new SysCompanyPeople();
                    $person->company_id = $companyId;
                    $person->type = 'sponsor';
                    $person->salutation = $sponsor['salutation'] ?? null;
                    $person->first_name = $sponsor['first_name'];
                    $person->last_name = $sponsor['last_name'] ?? null;
                    $person->mobile = $sponsor['mobile'] ?? null;
                    $person->email = $sponsor['email'] ?? null;
                    $person->nationality_id = $sponsor['nationality_id'] ?? null;
                    $person->save();
                    
                    // Save sponsor documents
                    $this->savePersonDocumentsArray($person->id, $request, "sponsors.$index", 'sponsor');
                }
            }
            
            // =====================================================
            // 7. CONTACTS (Dynamic)
            // =====================================================
            if ($request->has('contacts') && is_array($request->contacts)) {
                foreach ($request->contacts as $index => $contact) {
                    if (empty($contact['first_name'])) continue;
                    
                    $person = new SysCompanyPeople();
                    $person->company_id = $companyId;
                    $person->type = 'contact';
                    $person->salutation = $contact['salutation'] ?? null;
                    $person->first_name = $contact['first_name'];
                    $person->last_name = $contact['last_name'] ?? null;
                    $person->mobile = $contact['mobile'] ?? null;
                    $person->email = $contact['email'] ?? null;
                    $person->designation = $contact['designation'] ?? null;
                    $person->save();
                    
                    // Save contact documents
                    $this->savePersonDocumentsArray($person->id, $request, "contacts.$index", 'contact');
                }
            }
            
            // =====================================================
            // 8. BANKING (Dynamic) - from hidden inputs
            // =====================================================
            $bankCount = 0;
            while ($request->has("banks.$bankCount.bank_name")) {
                $banking = new SysCompanyBanking();
                $banking->company_id = $companyId;
                $banking->bank_name = $request->input("banks.$bankCount.bank_name");
                $banking->branch_name = $request->input("banks.$bankCount.branch_name");
                $banking->account_number = $request->input("banks.$bankCount.account_number");
                $banking->iban_number = $request->input("banks.$bankCount.iban_number");
                $banking->swift_code = $request->input("banks.$bankCount.swift_code");
                $banking->finance_code = $request->input("banks.$bankCount.finance_code");
                $banking->currency = $request->input("banks.$bankCount.currency");
                
                // Bank letter file
                if ($request->hasFile("banks.$bankCount.bank_letter")) {
                    $banking->bank_letter = $this->uploadSingleFile($request, "banks.$bankCount.bank_letter", 'uploads/company/banking');
                }
                
                $banking->save();
                $bankCount++;
            }
            
            // =====================================================
            // 9. WAREHOUSES (Dynamic)
            // =====================================================
            $warehouseCount = 0;
            while ($request->has("warehouses.$warehouseCount.warehouse_name")) {
                $warehouse = new CompanyWarehouse();
                $warehouse->company_id = $companyId;
                $warehouse->warehouse_code = $request->input("warehouses.$warehouseCount.warehouse_code");
                $warehouse->warehouse_name = $request->input("warehouses.$warehouseCount.warehouse_name");
                $warehouse->country = $request->input("warehouses.$warehouseCount.warehouse_country");
                $warehouse->state = $request->input("warehouses.$warehouseCount.warehouse_state");
                $warehouse->city = $request->input("warehouses.$warehouseCount.warehouse_city");
                $warehouse->area = $request->input("warehouses.$warehouseCount.warehouse_area");
                $warehouse->building_name = $request->input("warehouses.$warehouseCount.warehouse_building_name");
                $warehouse->flat_office_no = $request->input("warehouses.$warehouseCount.warehouse_flat_office_no");
                
                // Contact Person
                $warehouse->contact_salutation = $request->input("warehouses.$warehouseCount.contact_salutation");
                $warehouse->contact_first_name = $request->input("warehouses.$warehouseCount.contact_first_name");
                $warehouse->contact_last_name = $request->input("warehouses.$warehouseCount.contact_last_name");
                $warehouse->contact_mobile = $request->input("warehouses.$warehouseCount.contact_mobile");
                $warehouse->contact_email = $request->input("warehouses.$warehouseCount.contact_email");
                $warehouse->contact_designation = $request->input("warehouses.$warehouseCount.contact_designation");
                
                // Fire Safety
                $warehouse->fire_safety_compliance_status = $request->input("warehouses.$warehouseCount.fire_safety_compliance_status");
                $warehouse->fire_noc_certificate_number = $request->input("warehouses.$warehouseCount.fire_noc_certificate_number");
                $warehouse->safety_equipment_available = $request->input("warehouses.$warehouseCount.safety_equipment_available");
                $warehouse->fire_noc_expiry_date = $this->parseDate($request->input("warehouses.$warehouseCount.fire_noc_expiry_date"));
                $warehouse->last_safety_inspection_date = $this->parseDate($request->input("warehouses.$warehouseCount.last_safety_inspection_date"));
                
                // Documents
                if ($request->hasFile("warehouses.$warehouseCount.contact_documents")) {
                    $warehouse->contact_documents = $this->uploadMultipleFiles($request, "warehouses.$warehouseCount.contact_documents", 'uploads/company/warehouse_docs');
                }
                
                $warehouse->save();
                $warehouseCount++;
            }
            
            // =====================================================
            // 10. COMPANY POLICIES (Dynamic)
            // =====================================================
            $policyCount = 0;
            while ($request->has("policies.$policyCount.policy_name")) {
                $policy = new SysCompanyHrPolicy();
                $policy->company_id = $companyId;
                $policy->policy_date = $this->parseDate($request->input("policies.$policyCount.policy_date"));
                $policy->policy_name = $request->input("policies.$policyCount.policy_name");
                $policy->policy_category = $request->input("policies.$policyCount.policy_category");
                $policy->policy_valid = $this->parseDate($request->input("policies.$policyCount.policy_valid"));
                $policy->view_to_employees = $request->input("policies.$policyCount.view_to_employees");
                $policy->policy_details = $request->input("policies.$policyCount.policy_details");
                
                // Policy file
                if ($request->hasFile("policies.$policyCount.policy_file")) {
                    $policy->policy_file = $this->uploadSingleFile($request, "policies.$policyCount.policy_file", 'uploads/company/policies');
                }
                
                $policy->save();
                $policyCount++;
            }
            
            DB::commit();
            
            Toastr::success('Company created successfully with all details!', 'Success');
            return redirect()->route('company-details', $companyId);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error('Operation Failed: ' . $e->getMessage(), 'Failed');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Helper: Upload single file with original name
     */
    private function uploadSingleFile($request, $fieldName, $directory)
    {
        if (!$request->hasFile($fieldName)) {
            return null;
        }
        
        $file = $request->file($fieldName);
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
        $uniqueName = $nameWithoutExt . '_' . time() . '_' . uniqid() . '.' . $extension;
        
        $path = public_path($directory);
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        
        $file->move($path, $uniqueName);
        return $directory . '/' . $uniqueName;
    }

    /**
     * Helper: Upload multiple files and return pipe-separated string
     */
    private function uploadMultipleFiles($request, $fieldName, $directory)
    {
        if (!$request->hasFile($fieldName)) {
            return null;
        }
        
        $files = $request->file($fieldName);
        if (!is_array($files)) {
            $files = [$files];
        }
        
        $uploadedFiles = [];
        $path = public_path($directory);
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        
        foreach ($files as $i => $file) {
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
            $uniqueName = $nameWithoutExt . '_' . time() . '_' . $i . '.' . $extension;
            
            $file->move($path, $uniqueName);
            $uploadedFiles[] = $uniqueName;
        }
        
        return implode('|', $uploadedFiles);
    }

    /**
     * Helper: Parse date from d/m/Y or other formats
     */
    private function parseDate($dateString)
    {
        if (empty($dateString)) return null;
        
        try {
            if (strpos($dateString, '/') !== false) {
                return Carbon::createFromFormat('d/m/Y', $dateString)->format('Y-m-d');
            }
            return Carbon::parse($dateString)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Helper: Save person documents (owners/sponsors/contacts)
     */
    private function savePersonDocumentsArray($peopleId, $request, $baseKey, $type)
    {
        $docDir = public_path('uploads/company/people_docs');
        if (!file_exists($docDir)) mkdir($docDir, 0777, true);
        
        // Check for documents array
        $docIndex = 0;
        while ($request->hasFile("$baseKey.documents.$docIndex.attachment")) {
            $file = $request->file("$baseKey.documents.$docIndex.attachment");
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $uniqueName = $type . '_doc_' . time() . '_' . uniqid() . '.' . $extension;
            
            $file->move($docDir, $uniqueName);
            
            SysCompanyPeopleDocument::create([
                'people_id'     => $peopleId,
                'document_name' => $request->input("$baseKey.documents.$docIndex.document_name") ?? 'Document',
                'document_number' => $request->input("$baseKey.documents.$docIndex.document_number"),
                'document_date' => $this->parseDate($request->input("$baseKey.documents.$docIndex.document_date")),
                'expiry_date'   => $this->parseDate($request->input("$baseKey.documents.$docIndex.expiry_date")),
                'file_path'     => 'uploads/company/people_docs/' . $uniqueName,
            ]);
            
            $docIndex++;
        }
    }
}

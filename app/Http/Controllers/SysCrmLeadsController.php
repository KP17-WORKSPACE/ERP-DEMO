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
use Validator;
use App\SysCustSupplAddressbook;

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
            $lpo = [];
            $uploadPath = 'public/uploads/crm_lead_doc/';

            for ($i = 0; $i < count($files); $i++) {
                $file1 = $files[$i];
                $originalName = $file1->getClientOriginalName();
                $extension = $file1->getClientOriginalExtension();
                $filenameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);

                // Check if file exists and add suffix if needed
                $finalFilename = $originalName;
                $counter = 1;
                while (file_exists($uploadPath . $finalFilename)) {
                    $finalFilename = $filenameWithoutExt . '-' . $counter . '.' . $extension;
                    $counter++;
                }

                $file1->move($uploadPath, $finalFilename);
                $lpo[] = $finalFilename;
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
            $country = SysCountries::select('id', 'name', 'iso3', 'iso2')->get();

            $vendors = SysHelper::get_customer_list_deal_lead();

            $brand = SysBrand::select('id', 'title')->orderby('title', 'asc')->get();

            
                $staff = SysHelper::get_sales_persons();
        

            if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 35) {
                $sales_person = SysHelper::get_only_sales_persons();
            } else {
                $sales_person = SysHelper::get_only_sales_persons();
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
                if (!empty($request->date) || !empty($request->date2)) {
                    if (!empty($request->date)) {
                        $ctrl_date = SysHelper::normalizeToYmd($request->date);
                    }
                    if (!empty($request->date2)) {
                        $ctrl_date2 = SysHelper::normalizeToYmd($request->date2);
                    }


                    
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


                // Apply date filter if at least one manual date is set
                if (!empty($ctrl_date) && !empty($ctrl_date2)) {
                    $query->whereBetween(DB::raw("DATE(created_at)"), [$ctrl_date, $ctrl_date2]);
                    $statsQuery = (clone $query);
                }

                //if only from date is set
                elseif (!empty($ctrl_date)) {
                    $query->whereDate('created_at', '>=', $ctrl_date);
                    $statsQuery = (clone $query);
                }

                //if only to date is set
                elseif (!empty($ctrl_date2)) {
                    $query->whereDate('created_at', '<=', $ctrl_date2);
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
            $comments = SysCrmLeadsComments::where('lead_id', $id)->orderBy('id', 'DESC')->get();
            return compact('edit', 'comments');
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

            $staff = SmStaff::select('user_id', 'full_name')->get();

            $leads = SysCrmLeads::where('id', $id)->first();
            $edit = SysCrmLeads::where('id', $id)->first();
            $vendors = SysHelper::get_customer_list_deal_lead();
            $brand = SysBrand::select('id', 'title')->orderby('title', 'asc')->get();

            $designation = SmDesignation::select('title')->where('active_status', 1)->get();
            $country = SysCountries::select('id', 'name', 'iso3', 'iso2')->get();


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

            $staff = SmStaff::select('user_id', 'full_name')->get();

            $leads = SysCrmLeads::where('id', $id)->first();
            $edit = SysCrmLeads::where('id', $id)->first();
            $vendors = SysHelper::get_customer_list_deal_lead();
            $brand = SysBrand::select('id', 'title')->orderby('title', 'asc')->get();

            $designation = SmDesignation::select('title')->where('active_status', 1)->get();
            $country = SysCountries::select('id', 'name', 'iso3', 'iso2')->get();


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
            $lpo = [];
            $uploadPath = 'public/uploads/crm_lead_doc/';

            for ($i = 0; $i < count($files); $i++) {
                $file1 = $files[$i];
                $originalName = $file1->getClientOriginalName();
                $extension = $file1->getClientOriginalExtension();
                $filenameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);

                // Check if file exists and add suffix if needed
                $finalFilename = $originalName;
                $counter = 1;
                while (file_exists($uploadPath . $finalFilename)) {
                    $finalFilename = $filenameWithoutExt . '-' . $counter . '.' . $extension;
                    $counter++;
                }

                $file1->move($uploadPath, $finalFilename);
                $lpo[] = $finalFilename;
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

                $new_code = SysHelper::get_new_code_lead('sys_crm_leads', 'LD', 'code', $request->company);
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
            } else {
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
            $customers = DB::table('sys_cust_suppl as cs')->select('cs.id', 'cs.customer_salutation', 'cs.first_name', 'cs.last_name', 'cs.mobile', 'cs.email', 'csa.flat_office_no', 'csa.area', 'csa.building_name', 'cs.designation', 'c.name', 's.name as statename', 'csa.city', 'csa.zip_code', 'cs.account_type', 'cs.payment_terms')
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


    public function getcustomername_deal_controller($customername)
    {

        try {
            $r = SysHelper::get_data_by_role();
            $customer = DB::table('sys_cust_suppl as cs')->select('cs.id', 'cs.customer_salutation', 'cs.first_name', 'cs.last_name', 'cs.mobile', 'cs.email', 'csa.address', 'csa.address2', 'cs.designation', 'c.id as country_id', 'c.name', 's.id as state_id', 's.name as statename', 'csa.city', 'csa.zip_code', 'cs.account_type')
                ->leftjoin('sys_cust_suppl_addressbook as csa', 'csa.cust_suppl_id', 'cs.id')
                ->leftjoin('sys_countries as c', 'c.id', 'csa.country')
                ->leftjoin('sys_states as s', 's.id', 'csa.state')
                ->where('cs.id', $customername)
                ->limit(1)->orderby('csa.id', 'desc')->first();
            $bug = 0;
        } catch (\Exception $e) {
            return $e;
        }

        return $customer ?: null; // return as object
    }
    public function getCustomerAddresses(Request $request, $custId)
    {
        try {
            $addresses = DB::table('sys_cust_suppl_addressbook as csa')
                ->select(
                    'csa.id',
                    'csa.address',
                    'csa.address2',
                    'csa.city',
                    'csa.area',
                    'csa.building_name',
                    'csa.flat_office_no',
                    'csa.zip_code',
                    'csa.country',
                    'csa.state',
                    'c.name as country_name',
                    's.name as state_name',
                    'csa.is_shipping'
                )
                ->leftJoin('sys_countries as c', 'c.id', 'csa.country')
                ->leftJoin('sys_states as s', 's.id', 'csa.state')
                ->where('csa.cust_suppl_id', (int) $custId)
                ->orderBy('csa.set_default', 'desc')
                ->orderBy('csa.id', 'asc')
                ->get();
            return response()->json($addresses);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    public function getcustomername_deal(Request $request)
    {
        $input = $request->all();

        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $customers = DB::table('sys_cust_suppl as cs')->select('cs.id', 'cs.customer_salutation', 'cs.first_name', 'cs.last_name', 'cs.mobile', 'cs.email', 'csa.address', 'csa.address2', 'cs.designation', 'c.id as country_id', 'c.name', 's.id as state_id', 's.name as statename', 'csa.city', 'csa.zip_code', 'cs.account_type', 'csa.area', 'csa.flat_office_no', 'csa.building_name')
                ->leftjoin('sys_cust_suppl_addressbook as csa', 'csa.cust_suppl_id', 'cs.id')
                ->leftjoin('sys_countries as c', 'c.id', 'csa.country')
                ->leftjoin('sys_states as s', 's.id', 'csa.state')
                ->where('cs.id', $request->name)
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
                    $delivery_company = DB::table('sys_cust_suppl')->where('id', $val->cust_id)->first();
                $address_id = SysCustSupplAddressbook::where('cust_suppl_id', $val->cust_id)->where('is_shipping', 0)->first();

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
                        'note' => $val->note . " Converted from Lead #" . $val->code . " ",
                        'isproject' => $val->isproject,
                        'estimated_close_date' => date('Y-m-d'),
                        'created_by' => Auth::user()->id,
                        'created_at' => Carbon::now('+04:00')->format('Y-m-d H:i:s'),
                        'lead_id' => $val->id,
                        'designation' => $val->cust_designation,
                        'address' => $val->address,

                        'delivery_company' => $delivery_company->id ?? '',
                        'delivery_name' => $delivery_company->customer_salutation . ' ' . $delivery_company->first_name . ' ' . $delivery_company->last_name ?? '',
                        'delivery_number' => $delivery_company->contcat_number ?? '',
                        'delivery_email' => $delivery_company->email ?? '',
                        'delivery_country' => $delivery_company->vat_country ?? '',
                        'delivery_state' => $delivery_company->vat_state ?? '',
                        'delivery_city' => $delivery_company->city ?? '',
                        'delivery_area' => $delivery_company->area ?? '',
                        'delivery_flat_office_no' => $delivery_company->flat_office_no ?? '',
                        'delivery_building' => $delivery_company->building_name ?? '',
                        'delivery_zip_code' => $delivery_company->zip_code ?? '',
                        'delivery_address_select' => $address_id->id ?? '',

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
                    return response()->json([
                        'data' => 'REDIRECT',
                        'url' => url('crm-deals/show/' . $deal_id . '?deal_action=edit&new=yes'),
                    ]);
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
}

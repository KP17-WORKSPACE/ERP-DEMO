<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\SysCompany;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\SysHelper;
use App\SysCrmLeads;
use App\SysCrmDeals;
use App\SysCrmDealTrackApprovalInvoice;
use App\SmStaff;
use App\SysBrand;
use App\SysCountries;
use App\SysCrmDealTrackApprovalReceivables;






class SysCrmLeadsReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
    }

    public function leadsreportcompany(Request $request)
    {

        try {

            $ctrl_date = date('Y-m-01'); // First day of the current month
            $ctrl_date2 = date('Y-m-t'); // Last day of the current month

            if (session('logged_session_data.company_id') != 1) {
                return redirect('crm-leads-report/' . session('logged_session_data.company_id') . '/' . $ctrl_date . '/' . $ctrl_date2);
            }


            $filter_by = 'this_month';
            $ctrl_company_id = session('logged_session_data.company_id');



            $company = SysCompany::select('id', 'company_name')->orderby('sort_id', 'asc')->get();
            if (Auth::user()->id == 36) {
                $company = SysCompany::select('id', 'company_name')->where('id', 5)->orderby('sort_id', 'asc')->get();
            }

            $query = SysCrmLeads::wherein('status', [0, 1, 2, 3, 4, 10]);

            if ($_POST) {

                // Priority 1: Manual date range input
                if (!empty($request->date)) {
                    $ctrl_date =  SysHelper::normalizeToYmd($request->date);
                    $ctrl_date2 = !empty($request->date2)
                        ?  SysHelper::normalizeToYmd($request->date2)
                        : $ctrl_date;
                    $filter_by = '';
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

            }



            $query->whereBetween(DB::raw("DATE(created_at)"), [$ctrl_date, $ctrl_date2]);

            $base_total_leads = $query->count();

            $base_statusCounts = (clone $query)
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            $base_substatusCounts = (clone $query)
                ->select('sub_status', DB::raw('count(*) as count'))
                ->where('status', '!=', 0)
                ->groupBy('sub_status')
                ->pluck('count', 'sub_status')
                ->toArray();



            $subQuery = (clone $query)->getQuery(); // converts Eloquent to Query\Builder

            // Step 3: Wrap as subquery with alias
            $base_dealstatusCounts = DB::table(DB::raw("({$subQuery->toSql()}) as leads"))
                ->mergeBindings($subQuery) // Critical: to bind whereIn bindings properly
                ->join('sys_crm_deals as deals', 'leads.deal_id', '=', 'deals.id')
                ->select('deals.stage', DB::raw('COUNT(*) as count'))
                ->where('leads.status', 0)
                ->groupBy('deals.stage')
                ->pluck('count', 'deals.stage')
                ->toArray();




            $base_avgAgingDays = (clone $query)
                ->select(DB::raw("
                    AVG(
                        TIMESTAMPDIFF(DAY, created_at, last_updated) / NULLIF(lead_update_count, 0)
                    ) as avg_aging_days
                "))
                ->value('avg_aging_days');

            $company_stats = [];

            if (count($company) > 0) {
                foreach ($company as $comp) {
                    $company_id = $comp->id;
                    // Call your helper function
                    $leadStats = SysHelper::get_lead_count_by_company($ctrl_date, $ctrl_date2, $company_id);


                    // Step 2: Get average aging days
                    $avgAgingDays = DB::table('sys_crm_leads')
                        ->where('company_id', $company_id)
                        ->whereBetween('date', [$ctrl_date, $ctrl_date2])
                        ->whereNotNull('last_updated')
                        ->where('lead_update_count', '>', 0)
                        ->select(DB::raw("
                AVG(
                    TIMESTAMPDIFF(DAY, created_at, last_updated) / NULLIF(lead_update_count, 0)
                ) as avg_aging_days
            "))
                        ->value('avg_aging_days');

                    // Organize result per company
                    $company_stats[] = [
                        'company_id' => $company_id,
                        'company_name' => $comp->company_name ?? '',
                        'new' => $leadStats['new'] ?? 0,
                        'qualified' => $leadStats['qualified'] ?? 0,
                        'unqualified' => $leadStats['unqualified'] ?? 0,
                        'pending_response' => $leadStats['pending_response'] ?? 0,
                        'converted' => $leadStats['converted'] ?? 0,
                        'closed' => $leadStats['closed'] ?? 0,
                        'total' => $leadStats['total'] ?? 0,
                        'avg_aging_days' => $avgAgingDays,
                        'just_received_uncontacted' => $leadStats['just_received_uncontacted'] ?? 0,
                        'sent_to_sales' => $leadStats['sent_to_sales'] ?? 0,
                        'budget_issue' => $leadStats['budget_issue'] ?? 0,
                        'not_interested' => $leadStats['not_interested'] ?? 0,
                        'wrong_contact' => $leadStats['wrong_contact'] ?? 0,
                        'timeline_not_matching' => $leadStats['timeline_not_matching'] ?? 0,
                        'product_service_mismatch' => $leadStats['product_service_mismatch'] ?? 0,
                        'unqualified_other' => $leadStats['unqualified_other'] ?? 0,
                        'waiting_for_eud' => $leadStats['waiting_for_eud'] ?? 0,
                        'waiting_for_vendor_price' => $leadStats['waiting_for_vendor_price'] ?? 0,
                        'quoted_waiting_response' => $leadStats['quoted_waiting_response'] ?? 0,
                        'pending_response_other' => $leadStats['pending_response_other'] ?? 0,
                        'no_response' => $leadStats['no_response'] ?? 0,
                        'closed_other' => $leadStats['closed_other'] ?? 0,
                        'deal_prospecting' => $leadStats['deal_prospecting'] ?? 0,
                        'deal_quote' => $leadStats['deal_quote'] ?? 0,
                        'deal_closure' => $leadStats['deal_closure'] ?? 0,
                        'deal_won' => $leadStats['deal_won'] ?? 0,
                        'deal_lost' => $leadStats['deal_lost'] ?? 0,
                    ];


                    //   1: 'Just received, uncontacted',
                    //   2: 'Sent to Sales',
                    //   3: 'Budget Issue',
                    //   4: 'Not Interseted',
                    //   5: 'Wrong Contact',
                    //   6: 'Timeline not matching',
                    //   7: 'Product/Service mismatch',
                    //   8: 'Other',
                    //   9: 'Waiting for EUD',
                    //   10: 'Waiting for Vendor Price',
                    //   11: 'Quoted - Waiting for Response',
                    //   12: 'Other',
                    //   13: 'No Response',
                    //   14: 'Other'

                }
            } else {
                $company_stats = [];
            }



            return view('backEnd.crm.LeadsReportCompany', compact(
                'ctrl_date',
                'ctrl_date2',
                'filter_by',
                'company_stats',
                'base_total_leads',
                'base_statusCounts',
                'base_avgAgingDays',
                'base_substatusCounts',
                'base_dealstatusCounts'
            ));


        } catch (\Throwable $th) {
            return $th;
        }
    }

    //  public function leadsreport(Request $request, $company_id = null, $from_date = null, $to_date = null)
    // {

    //     try {

    //         $ctrl_date = date('Y-m-01');
    //         $ctrl_date2 = date("Y-m-t", strtotime($ctrl_date));
    //         $filter_by = '';
    //         $ctrl_company_id = session('logged_session_data.company_id');

    //         $ctrl_owner = '';



    //         if ($company_id != null && $from_date != null && $to_date != null) {
    //             $ctrl_company_id = $company_id;
    //             $ctrl_date = $from_date;
    //             $ctrl_date2 = $to_date;
    //         }

    //         $base_company = SysCompany::select('id', 'company_name')->where('id', $ctrl_company_id)->first();


    //         $filter_company_id = '';

    //         $company = SysCompany::select('id', 'company_name')->where('id', '!=', 1)->orderby('sort_id', 'asc')->get();
    //         if (Auth::user()->id == 36) {
    //             $company = SysCompany::select('id', 'company_name')->where('id', 5)->orderby('sort_id', 'asc')->get();
    //         }

    //         if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 35) {
    //             if ($ctrl_company_id == 1) {
    //                 $staff = SmStaff::select('user_id', 'full_name')->where('active_status', 1)->orderby('full_name', 'asc')->get();
    //             } else {
    //                 $staff = SmStaff::select('user_id', 'full_name')
    //                     ->whereRaw("find_in_set($ctrl_company_id,company_access)")
    //                     ->where('active_status', 1)->orderby('full_name', 'asc')->get();
    //             }
    //         } else {
    //             $staff = SmStaff::select('user_id', 'full_name')->where('user_id', Auth::user()->id)->get();
    //         }


    //         $query = SmStaff::select('user_id', 'full_name', 'role_id');


    //         if (Auth::user()->role_id != 1 && Auth::user()->role_id != 2 && Auth::user()->role_id != 8 && Auth::user()->role_id != 35) {
    //             $query->where('user_id', Auth::user()->id);
    //         }
    //         if (Auth::user()->role_id == 8) {
    //             $teams = DB::table('users')->where('role_id', 5)->pluck('id');
    //             $query->wherein('user_id', $teams);
    //         }




    //         if ($_POST) {

    //             if (!empty($request->company_id)) {
    //                 $query->where('main_company', $request->company_id);
    //                 $filter_company_id = $request->company_id;
    //             }

    //             if (!empty($request->owner_id)) {
    //                 $query->where('user_id', $request->owner_id);
    //                 $ctrl_owner = $request->owner_id;
    //             }

    //             // Priority 1: Manual date range input
    //             if (!empty($request->date)) {
    //                 $ctrl_date = date('Y-m-d', strtotime($request->date));
    //                 $ctrl_date2 = !empty($request->date2)
    //                     ? date('Y-m-d', strtotime($request->date2))
    //                     : $ctrl_date;
    //             }

    //             // Priority 2: Predefined filters (only if manual dates are not used)
    //             if (!empty($request->filter_by)) {
    //                 switch ($request->filter_by) {
    //                     case "today":
    //                         $ctrl_date = date('Y-m-d');
    //                         $ctrl_date2 = date('Y-m-d');
    //                         $filter_by = 'today';
    //                         break;

    //                     case "this_week":
    //                         $ctrl_date = date('Y-m-d', strtotime('last sunday'));
    //                         $ctrl_date2 = date('Y-m-d', strtotime('this saturday'));
    //                         $filter_by = 'this_week';
    //                         break;

    //                     case "last_week":
    //                         $ctrl_date = date('Y-m-d', strtotime('last sunday -7 days'));
    //                         $ctrl_date2 = date('Y-m-d', strtotime('last saturday'));
    //                         $filter_by = 'last_week';
    //                         break;

    //                     case "this_month":
    //                         $ctrl_date = date('Y-m-01');
    //                         $ctrl_date2 = date('Y-m-t');
    //                         $filter_by = 'this_month';
    //                         break;

    //                     case "last_month":
    //                         $ctrl_date = date('Y-m-d', strtotime('first day of previous month'));
    //                         $ctrl_date2 = date('Y-m-d', strtotime('last day of previous month'));
    //                         $filter_by = 'last_month';
    //                         break;

    //                     case "last_6_months":
    //                         $ctrl_date = date('Y-m-d', strtotime('first day of this month - 6 months'));
    //                         $ctrl_date2 = date('Y-m-d', strtotime("last day of this month"));
    //                         $filter_by = 'last_6_months';
    //                         break;

    //                     case "this_year":
    //                         $ctrl_date = date('Y-01-01');
    //                         $ctrl_date2 = date('Y-12-31');
    //                         $filter_by = 'this_year';
    //                         break;

    //                     case "last_year":
    //                         $ctrl_date = date('Y-01-01', strtotime('-1 year'));
    //                         $ctrl_date2 = date('Y-12-31', strtotime('-1 year'));
    //                         $filter_by = 'last_year';
    //                         break;
    //                 }
    //             }

    //         }


    //         $query->where(function ($q) use ($ctrl_date2) {
    //             $q->whereRaw("DATE_FORMAT(date_of_resign, '%Y-%m-%d') > ?", [date('Y-m-d', strtotime($ctrl_date2))])
    //                 ->orWhereNull('date_of_resign');
    //         });

    //         $persons = $query->orderby('full_name', 'asc')->get();

    //         $sales_persons = [];

    //         $base_total_leads['total'] = 0;
    //         $base_total_leads['new'] = 0;
    //         $base_total_leads['qualified'] = 0;
    //         $base_total_leads['unqualified'] = 0;
    //         $base_total_leads['pending_response'] = 0;
    //         $base_total_leads['converted'] = 0;
    //         $base_total_leads['closed'] = 0;
    //         $base_total_leads['just_received_uncontacted'] = 0;
    //         $base_total_leads['sent_to_sales'] = 0;
    //         $base_total_leads['budget_issue'] = 0;
    //         $base_total_leads['not_interested'] = 0;
    //         $base_total_leads['wrong_contact'] = 0;
    //         $base_total_leads['timeline_not_matching'] = 0;
    //         $base_total_leads['product_service_mismatch'] = 0;
    //         $base_total_leads['unqualified_other'] = 0;
    //         $base_total_leads['waiting_for_eud'] = 0;
    //         $base_total_leads['waiting_for_vendor_price'] = 0;
    //         $base_total_leads['quoted_waiting_response'] = 0;
    //         $base_total_leads['pending_response_other'] = 0;
    //         $base_total_leads['no_response'] = 0;
    //         $base_total_leads['closed_other'] = 0;
    //         $base_total_leads['total_followups'] = 0;
    //         $base_total_leads['avg_followups'] = 0;




    //         if (count($persons) > 0) {
    //             foreach ($persons as $value) {
    //                 $user = [$value->user_id];


    //                 $leadStats = SysHelper::get_lead_count_by_sales_person($ctrl_date, $ctrl_date2, $user, $ctrl_company_id);

    //                 // Step 2: Get average aging days
    //                 $avgAgingDays = DB::table('sys_crm_leads')
    //                     ->where('owner', $user)
    //                     ->whereBetween('date', [$ctrl_date, $ctrl_date2])
    //                     ->whereNotNull('last_updated')
    //                     ->where('lead_update_count', '>', 0)
    //                     ->select(DB::raw("
    //             AVG(
    //                 TIMESTAMPDIFF(DAY, created_at, last_updated) / NULLIF(lead_update_count, 0)
    //             ) as avg_aging_days
    //         "))
    //                     ->value('avg_aging_days');


    //                 $sales_persons[] = [
    //                     'user_id' => $value->user_id,
    //                     'full_name' => $value->full_name,
    //                     'role_id' => $value->role_id,
    //                     'new' => $leadStats['new'] ?? 0,
    //                     'qualified' => $leadStats['qualified'] ?? 0,
    //                     'unqualified' => $leadStats['unqualified'] ?? 0,
    //                     'pending_response' => $leadStats['pending_response'] ?? 0,
    //                     'converted' => $leadStats['converted'] ?? 0,
    //                     'closed' => $leadStats['closed'] ?? 0,
    //                     'total' => $leadStats['total'] ?? 0,
    //                     'avg_aging_days' => $avgAgingDays,
    //                     'just_received_uncontacted' => $leadStats['just_received_uncontacted'] ?? 0,
    //                     'sent_to_sales' => $leadStats['sent_to_sales'] ?? 0,
    //                     'budget_issue' => $leadStats['budget_issue'] ?? 0,
    //                     'not_interested' => $leadStats['not_interested'] ?? 0,
    //                     'wrong_contact' => $leadStats['wrong_contact'] ?? 0,
    //                     'timeline_not_matching' => $leadStats['timeline_not_matching'] ?? 0,
    //                     'product_service_mismatch' => $leadStats['product_service_mismatch'] ?? 0,
    //                     'unqualified_other' => $leadStats['unqualified_other'] ?? 0,
    //                     'waiting_for_eud' => $leadStats['waiting_for_eud'] ?? 0,
    //                     'waiting_for_vendor_price' => $leadStats['waiting_for_vendor_price'] ?? 0,
    //                     'quoted_waiting_response' => $leadStats['quoted_waiting_response'] ?? 0,
    //                     'pending_response_other' => $leadStats['pending_response_other'] ?? 0,
    //                     'no_response' => $leadStats['no_response'] ?? 0,
    //                     'closed_other' => $leadStats['closed_other'] ?? 0,
    //                     'total_followups' => $leadStats['total_followups'] ?? 0,
    //                     'avg_followups' => $leadStats['avg_followups'] ?? 0,
    //                     'deal_prospecting' => $leadStats['deal_prospecting'] ?? 0,
    //                     'deal_quote' => $leadStats['deal_quote'] ?? 0,
    //                     'deal_closure' => $leadStats['deal_closure'] ?? 0,
    //                     'deal_won' => $leadStats['deal_won'] ?? 0,
    //                     'deal_lost' => $leadStats['deal_lost'] ?? 0,
    //                 ];



    //                 $base_total_leads['total'] += $leadStats['total'] ?? 0;
    //                 $base_total_leads['new'] += $leadStats['new'] ?? 0;
    //                 $base_total_leads['qualified'] += (($leadStats['qualified'] ?? 0) + ($leadStats['converted'] ?? 0));
    //                 $base_total_leads['unqualified'] += $leadStats['unqualified'] ?? 0;
    //                 $base_total_leads['pending_response'] += $leadStats['pending_response'] ?? 0;
    //                 $base_total_leads['converted'] += $leadStats['converted'] ?? 0;
    //                 $base_total_leads['closed'] += $leadStats['closed'] ?? 0;
    //                 $base_total_leads['just_received_uncontacted'] += $leadStats['just_received_uncontacted'] ?? 0;
    //                 $base_total_leads['sent_to_sales'] += $leadStats['sent_to_sales'] ?? 0;
    //                 $base_total_leads['budget_issue'] += $leadStats['budget_issue'] ?? 0;
    //                 $base_total_leads['not_interested'] += $leadStats['not_interested'] ?? 0;
    //                 $base_total_leads['wrong_contact'] += $leadStats['wrong_contact'] ?? 0;
    //                 $base_total_leads['timeline_not_matching'] += $leadStats['timeline_not_matching'] ?? 0;
    //                 $base_total_leads['product_service_mismatch'] += $leadStats['product_service_mismatch'] ?? 0;
    //                 $base_total_leads['unqualified_other'] += $leadStats['unqualified_other'] ?? 0;
    //                 $base_total_leads['waiting_for_eud'] += $leadStats['waiting_for_eud'] ?? 0;
    //                 $base_total_leads['waiting_for_vendor_price'] += $leadStats['waiting_for_vendor_price'] ?? 0;
    //                 $base_total_leads['quoted_waiting_response'] += $leadStats['quoted_waiting_response'] ?? 0;
    //                 $base_total_leads['pending_response_other'] += $leadStats['pending_response_other'] ?? 0;
    //                 $base_total_leads['no_response'] += $leadStats['no_response'] ?? 0;
    //                 $base_total_leads['closed_other'] += $leadStats['closed_other'] ?? 0;
    //                 $base_total_leads['total_followups'] += $leadStats['total_followups'] ?? 0;
    //                 $base_total_leads['avg_followups'] += $leadStats['avg_followups'] ?? 0;
    //             }
    //         } else {
    //             $sales_persons = [];
    //         }

    //         return view('backEnd.crm.LeadsReport', compact(
    //             'ctrl_date',
    //             'ctrl_date2',
    //             'filter_by',
    //             'sales_persons',
    //             'company',
    //             'ctrl_company_id',
    //             'base_total_leads',
    //             'filter_company_id',
    //             'staff',
    //             'ctrl_owner',
    //             'base_company'
    //         ));




    //     } catch (\Throwable $th) {
    //         return $th;
    //     }

    // }

    public function leadsreport(Request $request, $company_id = null, $from_date = null, $to_date = null)
    {

        try {

            $ctrl_date = date('Y-m-01');
            $ctrl_date2 = date("Y-m-t");
            $filter_by = 'this_month';
            $ctrl_company_id = session('logged_session_data.company_id');

            $ctrl_owner = '';

            if ($company_id != null && $from_date != null && $to_date != null) {
                $ctrl_company_id = $company_id;
                $ctrl_date = $from_date;
                $ctrl_date2 = $to_date;
            }


            $company = SysCompany::select('id', 'company_name')->where('id', '!=', 1)->orderby('sort_id', 'asc')->get();
            if (Auth::user()->id == 36) {
                $company = SysCompany::select('id', 'company_name')->where('id', 5)->orderby('sort_id', 'asc')->get();
            }


            $query = SmStaff::select('user_id', 'full_name', 'role_id')->where('active_status', 1)->wherein('role_id', [2, 35, 26, 5, 33, 30, 8, 1]);


            if (Auth::user()->role_id != 1 && Auth::user()->role_id != 2 && Auth::user()->role_id != 8 && Auth::user()->role_id != 35) {
                $query->where('user_id', Auth::user()->id);
            }
            if (Auth::user()->role_id == 8) {
                $teams = DB::table('users')->where('role_id', 5)->pluck('id');
                $query->wherein('user_id', $teams);
            }


            if ($_POST) {

                if (!empty($request->company_id)) {
                    $ctrl_company_id = $request->company_id;
                }

                if (!empty($request->owner_id)) {
                    $query->where('user_id', $request->owner_id);
                    $ctrl_owner = $request->owner_id;
                }

                // Priority 1: Manual date range input
                if (!empty($request->date)) {
                        $ctrl_date = SysHelper::normalizeToYmd($request->date);
                    $ctrl_date2 = !empty($request->date2)
                        ?  SysHelper::normalizeToYmd($request->date2)
                        : $ctrl_date;
                    $filter_by = '';
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

            }

            $query->where('main_company', $ctrl_company_id);


            $query->where(function ($q) use ($ctrl_date2) {
                $q->whereRaw("DATE_FORMAT(date_of_resign, '%Y-%m-%d') > ?", [date('Y-m-d', strtotime($ctrl_date2))])
                    ->orWhereNull('date_of_resign');
            });

            $staff = SmStaff::select('user_id', 'full_name', 'role_id')->where('main_company', $ctrl_company_id)->where('active_status', 1)->wherein('role_id', [2, 35, 26, 5, 33, 30, 8, 1])
                ->where(function ($q) use ($ctrl_date2) {
                    $q->whereRaw("DATE_FORMAT(date_of_resign, '%Y-%m-%d') > ?", [date('Y-m-d', strtotime($ctrl_date2))])
                        ->orWhereNull('date_of_resign');
                })
                ->orderby('full_name', 'asc')
                ->get();

            $base_company = SysCompany::select('id', 'company_name')->where('id', $ctrl_company_id)->first();



            $persons = $query->orderby('full_name', 'asc')->get();

            $sales_persons = [];

            $base_total_leads['total'] = 0;
            $base_total_leads['new'] = 0;
            $base_total_leads['qualified'] = 0;
            $base_total_leads['unqualified'] = 0;
            $base_total_leads['pending_response'] = 0;
            $base_total_leads['converted'] = 0;
            $base_total_leads['closed'] = 0;
            $base_total_leads['just_received_uncontacted'] = 0;
            $base_total_leads['sent_to_sales'] = 0;
            $base_total_leads['budget_issue'] = 0;
            $base_total_leads['not_interested'] = 0;
            $base_total_leads['wrong_contact'] = 0;
            $base_total_leads['timeline_not_matching'] = 0;
            $base_total_leads['product_service_mismatch'] = 0;
            $base_total_leads['unqualified_other'] = 0;
            $base_total_leads['waiting_for_eud'] = 0;
            $base_total_leads['waiting_for_vendor_price'] = 0;
            $base_total_leads['quoted_waiting_response'] = 0;
            $base_total_leads['pending_response_other'] = 0;
            $base_total_leads['no_response'] = 0;
            $base_total_leads['closed_other'] = 0;
            $base_total_leads['total_followups'] = 0;
            $base_total_leads['avg_followups'] = 0;
            $base_total_leads['avg_aging_days'] = 0;
            $base_total_leads['deal_prospecting'] = 0;
            $base_total_leads['deal_quote'] = 0;
            $base_total_leads['deal_closure'] = 0;
            $base_total_leads['deal_won'] = 0;
            $base_total_leads['deal_lost'] = 0;
            $counter = 0;




            if (count($persons) > 0) {
                foreach ($persons as $value) {
                    $user = [$value->user_id];
                    $counter++;


                    $leadStats = SysHelper::get_lead_count_by_sales_person($ctrl_date, $ctrl_date2, $user, $ctrl_company_id);

                    // Step 2: Get average aging days
                    $avgAgingDays = DB::table('sys_crm_leads')
                        ->where('owner', $user)
                        ->whereBetween('date', [$ctrl_date, $ctrl_date2])
                        ->whereNotNull('last_updated')
                        ->where('lead_update_count', '>', 0)
                        ->select(DB::raw("
                AVG(
                    TIMESTAMPDIFF(DAY, created_at, last_updated) / NULLIF(lead_update_count, 0)
                ) as avg_aging_days
            "))
                        ->value('avg_aging_days');


                    $sales_persons[] = [
                        'user_id' => $value->user_id,
                        'full_name' => $value->full_name,
                        'role_id' => $value->role_id,
                        'new' => $leadStats['new'] ?? 0,
                        'qualified' => $leadStats['qualified'] ?? 0,
                        'unqualified' => $leadStats['unqualified'] ?? 0,
                        'pending_response' => $leadStats['pending_response'] ?? 0,
                        'converted' => $leadStats['converted'] ?? 0,
                        'closed' => $leadStats['closed'] ?? 0,
                        'total' => $leadStats['total'] ?? 0,
                        'avg_aging_days' => $avgAgingDays,
                        'just_received_uncontacted' => $leadStats['just_received_uncontacted'] ?? 0,
                        'sent_to_sales' => $leadStats['sent_to_sales'] ?? 0,
                        'budget_issue' => $leadStats['budget_issue'] ?? 0,
                        'not_interested' => $leadStats['not_interested'] ?? 0,
                        'wrong_contact' => $leadStats['wrong_contact'] ?? 0,
                        'timeline_not_matching' => $leadStats['timeline_not_matching'] ?? 0,
                        'product_service_mismatch' => $leadStats['product_service_mismatch'] ?? 0,
                        'unqualified_other' => $leadStats['unqualified_other'] ?? 0,
                        'waiting_for_eud' => $leadStats['waiting_for_eud'] ?? 0,
                        'waiting_for_vendor_price' => $leadStats['waiting_for_vendor_price'] ?? 0,
                        'quoted_waiting_response' => $leadStats['quoted_waiting_response'] ?? 0,
                        'pending_response_other' => $leadStats['pending_response_other'] ?? 0,
                        'no_response' => $leadStats['no_response'] ?? 0,
                        'closed_other' => $leadStats['closed_other'] ?? 0,
                        'total_followups' => $leadStats['total_followups'] ?? 0,
                        'avg_followups' => $leadStats['avg_followups'] ?? 0,
                        'deal_prospecting' => $leadStats['deal_prospecting'] ?? 0,
                        'deal_quote' => $leadStats['deal_quote'] ?? 0,
                        'deal_closure' => $leadStats['deal_closure'] ?? 0,
                        'deal_won' => $leadStats['deal_won'] ?? 0,
                        'deal_lost' => $leadStats['deal_lost'] ?? 0,
                    ];


                    $base_total_leads['total'] += $leadStats['total'] ?? 0;
                    $base_total_leads['new'] += $leadStats['new'] ?? 0;
                    $base_total_leads['qualified'] += (($leadStats['qualified'] ?? 0) + ($leadStats['converted'] ?? 0));
                    $base_total_leads['unqualified'] += $leadStats['unqualified'] ?? 0;
                    $base_total_leads['pending_response'] += $leadStats['pending_response'] ?? 0;
                    $base_total_leads['converted'] += $leadStats['converted'] ?? 0;
                    $base_total_leads['closed'] += $leadStats['closed'] ?? 0;
                    $base_total_leads['just_received_uncontacted'] += $leadStats['just_received_uncontacted'] ?? 0;
                    $base_total_leads['sent_to_sales'] += $leadStats['sent_to_sales'] ?? 0;
                    $base_total_leads['budget_issue'] += $leadStats['budget_issue'] ?? 0;
                    $base_total_leads['not_interested'] += $leadStats['not_interested'] ?? 0;
                    $base_total_leads['wrong_contact'] += $leadStats['wrong_contact'] ?? 0;
                    $base_total_leads['timeline_not_matching'] += $leadStats['timeline_not_matching'] ?? 0;
                    $base_total_leads['product_service_mismatch'] += $leadStats['product_service_mismatch'] ?? 0;
                    $base_total_leads['unqualified_other'] += $leadStats['unqualified_other'] ?? 0;
                    $base_total_leads['waiting_for_eud'] += $leadStats['waiting_for_eud'] ?? 0;
                    $base_total_leads['waiting_for_vendor_price'] += $leadStats['waiting_for_vendor_price'] ?? 0;
                    $base_total_leads['quoted_waiting_response'] += $leadStats['quoted_waiting_response'] ?? 0;
                    $base_total_leads['pending_response_other'] += $leadStats['pending_response_other'] ?? 0;
                    $base_total_leads['no_response'] += $leadStats['no_response'] ?? 0;
                    $base_total_leads['closed_other'] += $leadStats['closed_other'] ?? 0;
                    $base_total_leads['total_followups'] += $leadStats['total_followups'] ?? 0;
                    $base_total_leads['avg_followups'] += $leadStats['avg_followups'] ?? 0;
                    $base_total_leads['avg_aging_days'] += $avgAgingDays;

                    $base_total_leads['deal_prospecting'] += $leadStats['deal_prospecting'] ?? 0;
                    $base_total_leads['deal_quote'] += $leadStats['deal_quote'] ?? 0;
                    $base_total_leads['deal_closure'] += $leadStats['deal_closure'] ?? 0;
                    $base_total_leads['deal_won'] += $leadStats['deal_won'] ?? 0;
                    $base_total_leads['deal_lost'] += $leadStats['deal_lost'] ?? 0;

                }
            } else {
                $sales_persons = [];
            }



            if ($base_total_leads['avg_aging_days'] > 0) {
                $base_total_leads['avg_aging_days'] = $base_total_leads['avg_aging_days'] / $counter;
            }


            return view('backEnd.crm.LeadsReport', compact(
                'ctrl_date',
                'ctrl_date2',
                'filter_by',
                'sales_persons',
                'company',
                'ctrl_company_id',
                'base_total_leads',
                'staff',
                'ctrl_owner',
                'base_company'
            ));




        } catch (\Throwable $th) {
            return $th;
        }

    }

    public function leadsreportsalesperson(Request $request, $staff_id)
    {

        try {

            $ctrl_date = date('Y-m-01');
            $ctrl_date2 = date("Y-m-t", strtotime($ctrl_date));
            $filter_by = '';
            $ctrl_company_id = '';
            $ctrl_owner = $staff_id;
            $ctrl_lead_id = '';
            $ctrl_cust_id = '';
            $ctrl_brand = '';
            $ctrl_status = '';
            $ctrl_source = '';
            $ctrl_followupdt_filter = '';
            $ctrl_region_id = '';
            $ctrl_isproject = '';
            $ctrl_sub_status = '';
            $statusCounts = [];



            $url_company_id = $request->get('base_company_id');
            $url_from_date = $request->get('date');
            $url_to_date = $request->get('date2');


            if ($url_company_id != null) {
                $ctrl_company_id = $url_company_id;
            }
            if ($url_from_date != null) {
                $ctrl_date = $url_from_date;
            }
            if ($url_to_date != null) {
                $ctrl_date2 = $url_to_date;
            }



            $staff = SysHelper::get_sales_persons();

            $brand = SysBrand::select('id', 'title')->orderby('title', 'asc')->get();

            $country = SysCountries::select('id', 'name')->get();

            $base_company = SysCompany::select('id', 'company_name')->where('id', $ctrl_company_id)->first();



            $sales_person = SmStaff::select('user_id', 'full_name')->where('user_id', $staff_id)->first();

            $company = SysCompany::orderby('sort_id', 'asc')->get();

            if (Auth::user()->id == 36) {
                $company = SysCompany::select('id', 'company_name')->where('id', 5)->orderby('sort_id', 'asc')->get();
            }

            $query = SysCrmLeads::where('owner', $ctrl_owner)->wherein('status', [0, 1, 2, 3, 4, 10]);


            $vendors = SysHelper::get_customer_list_deal_lead();



            if (SysHelper::get_pagination_post($request)) {

                $r = SysHelper::get_data_by_role();

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
                if ($request->lead_id != "") {
                    $query->where('code', $request->lead_id);
                    $ctrl_lead_id = $request->lead_id;
                }
                if ($request->base_company_id != "") {
                    $ctrl_company_id = $request->base_company_id;
                    $statsQuery = (clone $query);

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

            }


            // Apply filter only if both dates are set
            if (!empty($ctrl_date) && !empty($ctrl_date2)) {
                $query->whereBetween(DB::raw("DATE(created_at)"), [$ctrl_date, $ctrl_date2]);
                $statsQuery = (clone $query);
            }

            if (!empty($ctrl_company_id)) {
                $query->where('company_id', $ctrl_company_id);
                $base_company = SysCompany::select('id', 'company_name')->where('id', $ctrl_company_id)->first();
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
                ->where('status', '!=', 0)
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

            $leads = $query->orderby('updated_at', 'desc')->paginate(50);


            if (session('logged_session_data.company_id') != 1) {
                $con_lead = SysCrmLeads::where('status', 0)->wherein('company_id', $r[0])->pluck('deal_id');
            } else {
                $con_lead = SysCrmLeads::where('status', 0)->pluck('deal_id');
            }

            $deal_det = SysCrmDeals::select('sys_crm_deals.id', 'sys_crm_deals.stage', 't.accounts', 't.sales', 't.purchease', 't.invoice', 't.delivery', 't.receivables')->leftjoin('sys_crm_deal_track as t', 't.deal_id', 'sys_crm_deals.id')->wherein('sys_crm_deals.id', $con_lead)->get();


            return view(
                'backEnd.crm.LeadsReportSalesPerson',
                compact(
                    'leads',
                    'deal_det',
                    'base_company',
                    'ctrl_date',
                    'ctrl_date2',
                    'ctrl_company_id',
                    'sales_person',
                    'vendors',
                    'ctrl_lead_id',
                    'ctrl_cust_id',
                    'staff',
                    'ctrl_owner',
                    'brand',
                    'ctrl_brand',
                    'ctrl_status',
                    'ctrl_source',
                    'ctrl_followupdt_filter',
                    'country',
                    'ctrl_region_id',
                    'ctrl_isproject',
                    'filter_by',
                    'ctrl_sub_status',
                    'company',
                    'lead_stats'
                )
            );

        } catch (\Throwable $th) {
            return $th;
        }
    }


}
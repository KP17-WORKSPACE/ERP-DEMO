<?php

namespace App\Http\Controllers;

use App\ApiBaseMethod;
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
use App\SysCrmLeads;
use App\SysCrmQuoteCSItems;
use App\SysCrmQuoteItems;
use App\SysCrmSalesTarget;
use App\SysCurrencySettings;
use App\SysCustSuppl;
use App\SysCustSupplAddressbook;
use App\SysHelper;
use App\SysItemOpeningStock;
use App\SysItemStock;
use App\SysPaymentTerms;
use App\SysSalesInvoice;
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

class SysCrmDealsReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
    }

    //modified kp
    public function salesreportcompany(Request $request)
    {
        try {
            if (session('logged_session_data.company_id') != 1) {
                return redirect('crm-deals-sales-report'); // Redirect if company_id is not 1
            }
            $ctrl_owner = '';
            $filter_by = '';
            $ctrl_company = session('logged_session_data.company_id');
            $ctrl_company_list = [];
            $ctrl_date = date('Y-m-01');
            $ctrl_date2 = date("Y-m-t", strtotime($ctrl_date));
            $ps_amc = "";
            $ps_amc_deal_id = [];

            $company = SysCompany::select('id', 'company_name')->orderby('sort_id', 'asc')->get();
            if (Auth::user()->id == 36) {
                $company = SysCompany::select('id', 'company_name')->where('id', 5)->orderby('sort_id', 'asc')->get();
            }

            if ($_POST) {
                if ($request->company_id != 1) {
                    //$query->where('company_id', $request->company_id);
                    $ctrl_company_list = [$request->company_id];
                    $ctrl_company = $request->company_id;
                } else {
                    if ($ctrl_company == 1 && (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 35)) {
                        $ctrl_company_list = SysCompany::pluck('id');
                    }
                }

                if ($request->date != "" && $request->filter_by == "") {
                    $ctrl_date = SysHelper::normalizeToYmd($request->date);
                }
                if ($request->date != "" && $request->filter_by == "") {
                    $ctrl_date = SysHelper::normalizeToYmd($request->date);
                    if ($request->date2 != "") {
                        $ctrl_date2 = SysHelper::normalizeToYmd($request->date2);
                    }
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
                if ($request->ps_amc == "ps") {
                    $ps_amc = "ps";
                    $ps_amc_deal_id = DB::table('sys_crm_deals')->select('sys_crm_deals.id')
                        ->leftjoin('sys_crm_deal_track_approval_invoice', 'sys_crm_deal_track_approval_invoice.deal_id', 'sys_crm_deals.id')
                        ->join('sys_crm_quote_items', 'sys_crm_quote_items.deal_id', 'sys_crm_deals.id')
                        ->wherein('sys_crm_quote_items.product_id', [26328, 35710, 36223])
                        ->where('sys_crm_deals.stage', 4)->pluck('sys_crm_deals.id');
                }
                if ($request->ps_amc == "amc") {
                    $ps_amc = "amc";
                    $ps_amc_deal_id = DB::table('sys_crm_deals')->select('sys_crm_deals.id')
                        ->leftjoin('sys_crm_deal_track_approval_invoice', 'sys_crm_deal_track_approval_invoice.deal_id', 'sys_crm_deals.id')
                        ->join('sys_crm_quote_items', 'sys_crm_quote_items.deal_id', 'sys_crm_deals.id')
                        ->wherein('sys_crm_quote_items.product_id', [35657, 35716, 37892])
                        ->where('sys_crm_deals.stage', 4)->pluck('sys_crm_deals.id');
                }
                if ($request->ps_amc == "ps_amc") {
                    $ps_amc = "ps_amc";
                    $ps_amc_deal_id = DB::table('sys_crm_deals')->select('sys_crm_deals.id')
                        ->leftjoin('sys_crm_deal_track_approval_invoice', 'sys_crm_deal_track_approval_invoice.deal_id', 'sys_crm_deals.id')
                        ->join('sys_crm_quote_items', 'sys_crm_quote_items.deal_id', 'sys_crm_deals.id')
                        ->wherein('sys_crm_quote_items.product_id', [26328, 35710, 36223, 35657, 35716, 37892])
                        ->where('sys_crm_deals.stage', 4)->pluck('sys_crm_deals.id');
                }
            } else {

                if ($ctrl_company == 1 && (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 35)) {
                    $ctrl_company_list = SysCompany::pluck('id');
                } else {
                    $ctrl_company_list = [$ctrl_company];
                }
            }

            if (count($company) > 0) {
                foreach ($company as $value) {
                    $company = $value->id;
                    $revenue = SysHelper::get_total_revenue_all_by_company($ctrl_date, $ctrl_date2, $company, $ps_amc_deal_id);
                    if ($value->combind_user_id == "") {
                        $target = SysHelper::calculateSalesTarget([0], $ctrl_date, $ctrl_date2, $filter_by, $company);
                    } else {
                        $target = SysHelper::calculateSalesTarget([0], $ctrl_date, $ctrl_date2, $filter_by, $company);
                    }
                    //return SysHelper::calculateSalesTarget([5800], '2025-01-01', '2025-02-01');

                    if ($target['rev_amount'] == 0) {
                        $tp = 0;
                    } else {
                        $tp1 = ($revenue[0] / $target['rev_amount']) * 100;
                        $tp2 = ($revenue[1] / $target['gp_amount']) * 100;
                        $tp = round(($tp1 + $tp2) / 2, 2);
                    }
                    $arrayVariable = [
                        'company_id' => $value->id,
                        'full_name' => $value->company_name,
                        'role_id' => 1,
                        'combind_user_id' => 0,
                        'revenue' => $revenue,
                        'forcast' => SysHelper::get_total_forcast_all_by_company($ctrl_date, $ctrl_date2, $company, $ps_amc_deal_id),
                        'revenue_actual' => SysHelper::get_total_revenue_actual_all_by_company($ctrl_date, $ctrl_date2, $company, $ps_amc_deal_id),
                        'on_process' => SysHelper::get_total_on_process_all_by_company($ctrl_date, $ctrl_date2, $company, $ps_amc_deal_id),
                        'target' => $target,
                        'dealcount' => SysHelper::get_deal_count_by_company($ctrl_date, $ctrl_date2, $company, $ps_amc_deal_id),
                        'tp' => $tp,
                    ];
                    $data[] = $arrayVariable;
                }
                $data = collect($data);
                //$data = $data->sortByDesc('tp');
                $rev_sum = SysHelper::get_total_revenue_all_by_company_sum($ctrl_date, $ctrl_date2, $company, $ps_amc_deal_id);
            } else {
                $data = '0';
                $rev_sum = [0.00, 0.00, 0.00];
            }

            $form_data = [
                'data' => $data,
                'ctrl_company' => $ctrl_company,
                'ctrl_date' => $ctrl_date,
                'ctrl_date2' => $ctrl_date2,
                'filter_by' => $filter_by,
                'rev_sum' => $rev_sum,
            ];

            return view('backEnd.crm.DealSaleReportCompany', compact('data', 'ctrl_date', 'ctrl_date2', 'filter_by', 'ps_amc', 'company', 'rev_sum'));

            session()->put('sale_report_list_query', $form_data);
            return redirect('crm-deals-sales-reports');
            //return $data;

        } catch (\Throwable $th) {
            return $th;
        }
    }

    //modified kp
    // public function salesreport(Request $request, $cid = null, $m1 = null, $m2 = null)
    // {
    //     try {

    //         $ctrl_owner = '';
    //         $filter_by = '';
    //         $ctrl_company = session('logged_session_data.company_id');
    //         $ctrl_company_list = [];
    //         $ctrl_date = date('Y-m-01');
    //         $ctrl_date2 = date("Y-m-t", strtotime($ctrl_date));
    //         $ps_amc = "";
    //         $ps_amc_deal_id = [];

    //         if ($cid != null && $m1 != null && $m2 != null) {
    //             $ctrl_company = $cid;
    //             $ctrl_date = $m1;
    //             $ctrl_date2 = $m2;
    //         }

    //         $company = SysCompany::select('id', 'company_name')->orderby('sort_id', 'asc')->get();
    //         if (Auth::user()->id == 36) {
    //             $company = SysCompany::select('id', 'company_name')->where('id', 5)->orderby('sort_id', 'asc')->get();
    //         }

    //         if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 35) {
    //             if ($ctrl_company == 1) {
    //                 $staff = SmStaff::select('user_id', 'full_name')->where('active_status', 1)->orderby('full_name', 'asc')->get();
    //             } else {
    //                 $staff = SmStaff::select('user_id', 'full_name')
    //                     ->whereRaw("find_in_set($ctrl_company,company_access)")
    //                     //->where('main_company',$ctrl_company)
    //                     ->where('active_status', 1)->orderby('full_name', 'asc')->get();
    //             }
    //         } else {
    //             $staff = SmStaff::select('user_id', 'full_name')->where('user_id', Auth::user()->id)->get();
    //         }

    //         $query = SmStaff::select('user_id', 'full_name', 'role_id', 'combind_user_id');

    //         if (isset($request->company_id)) {
    //             if ($request->company_id != 1) {
    //                 //$query->where('main_company',$request->company_id);
    //                 //$query->whereRaw("find_in_set($request->company_id,company_access)");    
    //                 //return $request->company_id;
    //             }
    //         } else {
    //             if ($ctrl_company != 1 && (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 35)) {
    //                 //$query->where('main_company',$ctrl_company);
    //                 $query->whereRaw("find_in_set($ctrl_company,company_access)");
    //                 //$query->whereRaw("find_in_set($ctrl_company,company_access)");

    //             }
    //         }

    //         // if(Auth::user()->id == 36){ //Jacob George
    //         //     $query->wherein('user_id',[36,25,51,31,27]);
    //         // }
    //         // if(Auth::user()->id == 58) { //Thaiab Mohammed
    //         //     $query->wherein('user_id',[58,59,60,62]);
    //         // }
    //         // if(Auth::user()->id == 18) { //Prajeesh Prabhakar
    //         //     $query->wherein('user_id',[18,20,19]);
    //         // }
    //         // if(Auth::user()->id == 48) { //Parveen Sheik Asif
    //         //     $query->wherein('user_id',[48,39,71]);
    //         // }

    //         //on 27/03/2025 commented mismatch issue in total
    //         //$query->wherein('role_id', [1,2,5,8,32]);

    //         //$query->wherenotin('user_id', [51,28]);

    //         //->where('active_status',1);
    //         //$query->wherenotin('user_id', [48,82,21,49,71,28,55,56,35,80,75,30,18,61,3,72,37,78,4,60,73,22,51,31,23,1,59,89,76,24,102]);
    //         if ($_POST) {
    //             if ($request->company_id != 1) {
    //                 //$query->where('company_id', $request->company_id);
    //                 $ctrl_company_list = [$request->company_id];
    //                 $ctrl_company = $request->company_id;
    //             } else {
    //                 if ($ctrl_company == 1 && (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 35)) {
    //                     $ctrl_company_list = SysCompany::pluck('id');
    //                 }
    //             }
    //             if ($request->owner_id != "") {
    //                 $query->where('user_id', $request->owner_id);
    //                 $ctrl_owner = $request->owner_id;
    //             }
    //             if ($request->date != "" && $request->filter_by == "") {
    //                 $ctrl_date = SysHelper::normalizeToYmd($request->date);
    //             }
    //             if ($request->date != "" && $request->filter_by == "") {
    //                 $ctrl_date = SysHelper::normalizeToYmd($request->date);
    //                 if ($request->date2 != "") {
    //                     $ctrl_date2 = SysHelper::normalizeToYmd($request->date2);
    //                 }
    //             }
    //             if ($request->filter_by == "this_month") {
    //                 $ctrl_date = date('Y-m-01');
    //                 $ctrl_date2 = date("Y-m-t", strtotime($ctrl_date));
    //                 $filter_by = 'this_month';
    //             }
    //             if ($request->filter_by == "today") {
    //                 $ctrl_date = date('Y-m-d');
    //                 $ctrl_date2 = date('Y-m-d');
    //                 $filter_by = 'today';
    //             }
    //             if ($request->filter_by == "this_week") {
    //                 $ctrl_date = date('Y-m-d', strtotime('-1 week sunday 00:00:00'));
    //                 $ctrl_date2 = date('Y-m-d', strtotime('saturday 23:59:59'));
    //                 $filter_by = 'this_week';
    //             }
    //             if ($request->filter_by == "last_week") {
    //                 $ctrl_date = date('Y-m-d', strtotime('-2 week sunday 00:00:00'));
    //                 $ctrl_date2 = date('Y-m-d', strtotime('-1 week saturday 23:59:59'));
    //                 $filter_by = 'last_week';
    //             }
    //             if ($request->filter_by == "last_month") {
    //                 $ctrl_date = date('Y-m-d', strtotime('first day of previous month'));
    //                 $ctrl_date2 = date('Y-m-d', strtotime('last day of previous month'));
    //                 $filter_by = 'last_month';
    //             }
    //             if ($request->filter_by == "this_quarter") {
    //                 $q_date = SysHelper::get_quarter(date('m'));
    //                 $ctrl_date = $q_date[0];
    //                 $ctrl_date2 = $q_date[1];
    //                 $filter_by = 'this_quarter';
    //             }
    //             if ($request->filter_by == "pre_quarter") {
    //                 $q_date = SysHelper::get_pre_quarter(date('m'));
    //                 $ctrl_date = $q_date[0];
    //                 $ctrl_date2 = $q_date[1];
    //                 $filter_by = 'pre_quarter';
    //             }
    //             if ($request->filter_by == "this_year") {
    //                 $ctrl_date = date('Y-01-01');
    //                 $ctrl_date2 = date('Y-12-31');
    //                 $filter_by = 'this_year';
    //             }
    //             if ($request->filter_by == "last_year") {
    //                 $ctrl_date = date("Y-01-01", strtotime("-1 year"));
    //                 $ctrl_date2 = date("Y-12-31", strtotime("-1 year"));
    //                 $filter_by = 'last_year';
    //             }

    //             if ($request->ps_amc == "ps") {
    //                 $ps_amc = "ps";
    //                 $ps_amc_deal_id = DB::table('sys_crm_deals')->select('sys_crm_deals.id')
    //                     ->leftjoin('sys_crm_deal_track_approval_invoice', 'sys_crm_deal_track_approval_invoice.deal_id', 'sys_crm_deals.id')
    //                     ->join('sys_crm_quote_items', 'sys_crm_quote_items.deal_id', 'sys_crm_deals.id')
    //                     ->wherein('sys_crm_quote_items.product_id', [26328, 35710, 36223])
    //                     ->where('sys_crm_deals.stage', 4)->pluck('sys_crm_deals.id');
    //             }
    //             if ($request->ps_amc == "amc") {
    //                 $ps_amc = "amc";
    //                 $ps_amc_deal_id = DB::table('sys_crm_deals')->select('sys_crm_deals.id')
    //                     ->leftjoin('sys_crm_deal_track_approval_invoice', 'sys_crm_deal_track_approval_invoice.deal_id', 'sys_crm_deals.id')
    //                     ->join('sys_crm_quote_items', 'sys_crm_quote_items.deal_id', 'sys_crm_deals.id')
    //                     ->wherein('sys_crm_quote_items.product_id', [35657, 35716, 37892])
    //                     ->where('sys_crm_deals.stage', 4)->pluck('sys_crm_deals.id');
    //             }
    //             if ($request->ps_amc == "ps_amc") {
    //                 $ps_amc = "ps_amc";
    //                 $ps_amc_deal_id = DB::table('sys_crm_deals')->select('sys_crm_deals.id')
    //                     ->leftjoin('sys_crm_deal_track_approval_invoice', 'sys_crm_deal_track_approval_invoice.deal_id', 'sys_crm_deals.id')
    //                     ->join('sys_crm_quote_items', 'sys_crm_quote_items.deal_id', 'sys_crm_deals.id')
    //                     ->wherein('sys_crm_quote_items.product_id', [26328, 35710, 36223, 35657, 35716, 37892])
    //                     ->where('sys_crm_deals.stage', 4)->pluck('sys_crm_deals.id');
    //             }

    //             $query->where(function ($q) use ($ctrl_date2) {
    //                 $q->whereRaw("DATE_FORMAT(date_of_resign, '%Y-%m-%d') > ?", [date('Y-m-d', strtotime($ctrl_date2))])
    //                     ->orWhereNull('date_of_resign');
    //             });

    //             if (Auth::user()->role_id != 1 && Auth::user()->role_id != 2) {
    //                 $query->where('user_id', Auth::user()->id);
    //             }
    //             $deals = $query->orderby('full_name', 'asc')->get();
    //             $ctrl_currancy = $request->currancy;
    //         } else {
    //             $ctrl_currancy = 0;
    //             if ($ctrl_company == 1 && (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 35)) {
    //                 $ctrl_company_list = SysCompany::pluck('id');
    //             } else {
    //                 $ctrl_company_list = [$ctrl_company];
    //             }


    //             //$query->where('company_id', $ctrl_company);
    //             if (Auth::user()->role_id != 1 && Auth::user()->role_id != 2 && Auth::user()->role_id != 8 && Auth::user()->role_id != 35) {
    //                 $query->where('user_id', Auth::user()->id);
    //             }
    //             if (Auth::user()->role_id == 8) {
    //                 $teams = DB::table('users')->wherein('role_id', [5, 33])->pluck('id');
    //                 $query->wherein('user_id', $teams);
    //             }

    //             $query->where(function ($q) use ($ctrl_date2) {
    //                 $q->whereRaw("DATE_FORMAT(date_of_resign, '%Y-%m-%d') > ?", [date('Y-m-d', strtotime($ctrl_date2))])
    //                     ->orWhereNull('date_of_resign');
    //             });
    //             $deals = $query->orderby('full_name', 'asc')->get();
    //         }
    //         if (count($deals) > 0) {
    //             foreach ($deals as $value) {
    //                 $user = [$value->user_id];
    //                 $revenue = SysHelper::get_total_revenue_all_by_user($user, $ctrl_date, $ctrl_date2, $ctrl_company_list, $ps_amc_deal_id, $ctrl_currancy);
    //                 //return $revenue;
    //                 //return SysHelper::get_internal_external_sales_report($user, $ctrl_date, $ctrl_date2, $ctrl_company_list);

    //                 /*$target = SysCrmSalesTarget::select(db::raw('sum(target) as amount'),db::raw('max(type) as type'))->where('user_id',$value->user_id)
    //                 ->whereRaw("DATE_FORMAT(target_month, '%Y-%m') >= '".date('Y-m', strtotime($ctrl_date))."' and DATE_FORMAT(target_month, '%Y-%m') <= '".date('Y-m', strtotime($ctrl_date2))."'")->first();*/
    //                 if ($value->combind_user_id == "") {
    //                     //$target = SysCrmSalesTarget::select(db::raw('COALESCE(sum(revenue_target_monthly), 0) as rev_amount'),
    //                     //db::raw('COALESCE(sum(gp_target_monthly), 0) as gp_amount'))->where('user_id',$value->user_id)
    //                     //->whereRaw("DATE_FORMAT(target_month_from, '%Y-%m') <= '".date('Y-m', strtotime($ctrl_date))."'")->orderby('target_month_from','desc')->first();
    //                     $target = SysHelper::calculateSalesTarget([$value->user_id], $ctrl_date, $ctrl_date2, $filter_by, $ctrl_company);

    //                 } else {
    //                     $userIds = array_map('intval', explode(',', $value->combind_user_id));
    //                     /*$target = SysCrmSalesTarget::select(
    //                         DB::raw('COALESCE(sum(revenue_target_monthly), 0) as rev_amount'),
    //                         DB::raw('COALESCE(sum(gp_target_monthly), 0) as gp_amount')
    //                     )
    //                     ->wherein("user_id", $userIds)
    //                     ->whereRaw("DATE_FORMAT(target_month_from, '%Y-%m') <= ?", [date('Y-m', strtotime($ctrl_date))]) // Using parameter binding for safety
    //                     ->orderBy('target_month_from', 'desc')
    //                     ->first();*/
    //                     $target = SysHelper::calculateSalesTarget($userIds, $ctrl_date, $ctrl_date2, $filter_by, $ctrl_company);
    //                 }

    //                 //return SysHelper::calculateSalesTarget([5800], '2025-01-01', '2025-02-01');

    //                 if ($target['rev_amount'] == 0) {
    //                     $tp = 0;
    //                 } else {
    //                     $tp1 = ($revenue[0] / $target['rev_amount']) * 100;
    //                     $tp2 = ($revenue[1] / $target['gp_amount']) * 100;
    //                     $tp = round(($tp1 + $tp2) / 2, 2);
    //                 }
    //                 $arrayVariable = [
    //                     'user_id' => $value->user_id,
    //                     'full_name' => $value->full_name,
    //                     'role_id' => $value->role_id,
    //                     'combind_user_id' => $value->combind_user_id,
    //                     'revenue' => $revenue,
    //                     'revenue_nocombind' => SysHelper::get_total_revenue_all_by_user_nocombind($user, $ctrl_date, $ctrl_date2, $ctrl_company_list, $ps_amc_deal_id, $ctrl_currancy),
    //                     'forcast' => SysHelper::get_total_forcast_all_by_user($user, $ctrl_date, $ctrl_date2, $ctrl_company_list, $ps_amc_deal_id, $ctrl_currancy),
    //                     'revenue_actual' => SysHelper::get_total_revenue_actual_all_by_user($user, $ctrl_date, $ctrl_date2, $ctrl_company_list, $ps_amc_deal_id, $ctrl_currancy),
    //                     'on_process' => SysHelper::get_total_on_process_all_by_user($user, $ctrl_date, $ctrl_date2, $ctrl_company_list, $ps_amc_deal_id, $ctrl_currancy),
    //                     'target' => $target,
    //                     'dealcount' => SysHelper::get_deal_count_by_user($user, $ctrl_date, $ctrl_date2, $ctrl_company_list, $ps_amc_deal_id),
    //                     'tp' => $tp,
    //                 ];
    //                 $data[] = $arrayVariable;
    //             }
    //             $data = collect($data);
    //             $data = $data->sortByDesc('tp');
    //             $rev_sum = SysHelper::get_total_revenue_all_by_user_sum($deals->pluck('user_id'), $ctrl_date, $ctrl_date2, $ctrl_company_list, $ps_amc_deal_id, $ctrl_currancy);
    //         } else {
    //             $data = '0';
    //             $rev_sum = [0.00, 0.00, 0.00];
    //         }

    //         $form_data = [
    //             'data' => $data,
    //             'staff' => $staff,
    //             'ctrl_owner' => $ctrl_owner,
    //             'ctrl_company' => $ctrl_company,
    //             'ctrl_date' => $ctrl_date,
    //             'ctrl_date2' => $ctrl_date2,
    //             'filter_by' => $filter_by,
    //             'rev_sum' => $rev_sum,
    //         ];

    //         return view('backEnd.crm.DealSaleReport', compact('data', 'staff', 'ctrl_owner', 'ctrl_company', 'ctrl_date', 'ctrl_date2', 'filter_by', 'company', 'rev_sum', 'ps_amc', 'ctrl_currancy'));

    //         session()->put('sale_report_list_query', $form_data);
    //         return redirect('crm-deals-sales-reports');
    //         //return $data;

    //     } catch (\Throwable $th) {
    //         return $th;
    //     }
    // }


     public function salesreport(Request $request,$cid=null, $m1=null, $m2=null)
    {
        try {
            
            
            $ctrl_owner='';
            $filter_by='';
            $ctrl_company=session('logged_session_data.company_id');
            $ctrl_company_list=[];
            $ctrl_date=date('Y-m-01');
            $ctrl_date2=date("Y-m-t", strtotime($ctrl_date));
            $ps_amc="";
            $ps_amc_deal_id=[];

            if($cid != null && $m1 != null && $m2 != null){                
                $ctrl_company = $cid;
                $ctrl_date=$m1;
                $ctrl_date2=$m2;
            }
            
            $company = SysCompany::select('id','company_name')->orderby('sort_id','asc')->get();
            if(Auth::user()->id == 36){
                $company = SysCompany::select('id','company_name')->where('id',5)->orderby('sort_id','asc')->get();
            }
            
            if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 35){
                if($ctrl_company == 1){
                    $staff = SmStaff::select('user_id','full_name')->where('active_status',1)->orderby('full_name','asc')->get();
                }
                else{
                    $staff = SmStaff::select('user_id','full_name')
                    ->whereRaw("find_in_set($ctrl_company,company_access)")
                    //->where('main_company',$ctrl_company)
                    ->where('active_status',1)->orderby('full_name','asc')->get();
                }
            }
            else{
                $staff = SmStaff::select('user_id','full_name')->where('user_id',Auth::user()->id)->get();
            }

            $query = SmStaff::select('user_id','full_name','role_id','combind_user_id');
            
            if(isset($request->company_id)){
                if($request->company_id != 1){
                    $query->where('main_company',$request->company_id);
                    //$query->whereRaw("find_in_set($request->company_id,company_access)");    
                }
            }
            else{
                if($ctrl_company!=1 && (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 35)){
                    //$query->where('main_company',$ctrl_company);
                    $query->whereRaw("find_in_set($ctrl_company,company_access)");
                    //$query->whereRaw("find_in_set($ctrl_company,company_access)");

                }
            }

            // if(Auth::user()->id == 36){ //Jacob George
            //     $query->wherein('user_id',[36,25,51,31,27]);
            // }
            // if(Auth::user()->id == 58) { //Thaiab Mohammed
            //     $query->wherein('user_id',[58,59,60,62]);
            // }
            // if(Auth::user()->id == 18) { //Prajeesh Prabhakar
            //     $query->wherein('user_id',[18,20,19]);
            // }
            // if(Auth::user()->id == 48) { //Parveen Sheik Asif
            //     $query->wherein('user_id',[48,39,71]);
            // }

            //on 27/03/2025 commented mismatch issue in total
            //$query->wherein('role_id', [1,2,5,8,32]);

            //$query->wherenotin('user_id', [51,28]);
            
            //->where('active_status',1);
            //$query->wherenotin('user_id', [48,82,21,49,71,28,55,56,35,80,75,30,18,61,3,72,37,78,4,60,73,22,51,31,23,1,59,89,76,24,102]);
            if($_POST){
                if ($request->company_id != 1) {
                    //$query->where('company_id', $request->company_id);
                    $ctrl_company_list=[$request->company_id];
                    $ctrl_company = $request->company_id;
                } else {
                    if($ctrl_company == 1 && (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 35)){
                        $ctrl_company_list= SysCompany::pluck('id');
                    }
                }
                if ($request->owner_id != "") {
                    $query->where('user_id', $request->owner_id);
                    $ctrl_owner=$request->owner_id;
                }
                if ($request->date != "" && $request->filter_by == "") {
                    $ctrl_date= SysHelper::normalizeToYmd($request->date);
                }
                if ($request->date != "" && $request->filter_by == "") {
                    $ctrl_date= SysHelper::normalizeToYmd($request->date);
                    if ($request->date2 != "") {
                        $ctrl_date2= SysHelper::normalizeToYmd($request->date2);
                    }
                }
                if ($request->filter_by == "this_month") {
                    $ctrl_date=date('Y-m-01');
                    $ctrl_date2=date("Y-m-t", strtotime($ctrl_date));
                    $filter_by='this_month';               
                }
                if ($request->filter_by == "today") {
                    $ctrl_date=date('Y-m-d');
                    $ctrl_date2=date('Y-m-d');
                    $filter_by='today';
                }
                if ($request->filter_by == "this_week") {
                    $ctrl_date = date('Y-m-d', strtotime('-1 week sunday 00:00:00'));
                    $ctrl_date2 = date('Y-m-d', strtotime('saturday 23:59:59'));
                    $filter_by='this_week';
                }
                if ($request->filter_by == "last_week") {
                    $ctrl_date = date('Y-m-d', strtotime('-2 week sunday 00:00:00'));
                    $ctrl_date2 = date('Y-m-d', strtotime('-1 week saturday 23:59:59'));
                    $filter_by='last_week';
                }
                if ($request->filter_by == "last_month") {
                    $ctrl_date = date('Y-m-d', strtotime('first day of previous month'));
                    $ctrl_date2 = date('Y-m-d', strtotime('last day of previous month'));
                    $filter_by='last_month';
                }
                if ($request->filter_by == "this_quarter") {
                    $q_date = SysHelper::get_quarter(date('m'));
                    $ctrl_date = $q_date[0];
                    $ctrl_date2 = $q_date[1];
                    $filter_by='this_quarter';
                }
                if ($request->filter_by == "pre_quarter") {
                    $q_date = SysHelper::get_pre_quarter(date('m'));
                    $ctrl_date = $q_date[0];
                    $ctrl_date2 = $q_date[1];
                    $filter_by='pre_quarter';
                }
                if ($request->filter_by == "this_year") {
                    $ctrl_date = date('Y-01-01');
                    $ctrl_date2 = date('Y-12-31');
                    $filter_by='this_year';
                }
                if ($request->filter_by == "last_year") {
                    $ctrl_date = date("Y-01-01",strtotime("-1 year"));
                    $ctrl_date2 = date("Y-12-31",strtotime("-1 year"));
                    $filter_by='last_year';
                }
                
                if($request->ps_amc == "ps")
                {
                    $ps_amc="ps";
                    $ps_amc_deal_id = DB::table('sys_crm_deals')->select('sys_crm_deals.id')
                    ->leftjoin('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_id','sys_crm_deals.id')
                    ->join('sys_crm_quote_items','sys_crm_quote_items.deal_id','sys_crm_deals.id')
                    ->wherein('sys_crm_quote_items.product_id',[26328,35710,36223])
                    ->where('sys_crm_deals.stage',4)->pluck('sys_crm_deals.id');
                }
                if($request->ps_amc == "amc")
                {
                    $ps_amc="amc";
                    $ps_amc_deal_id = DB::table('sys_crm_deals')->select('sys_crm_deals.id')
                    ->leftjoin('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_id','sys_crm_deals.id')
                    ->join('sys_crm_quote_items','sys_crm_quote_items.deal_id','sys_crm_deals.id')
                    ->wherein('sys_crm_quote_items.product_id',[35657,35716,37892])
                    ->where('sys_crm_deals.stage',4)->pluck('sys_crm_deals.id');
                }
                if($request->ps_amc == "ps_amc")
                {
                    $ps_amc="ps_amc";
                    $ps_amc_deal_id = DB::table('sys_crm_deals')->select('sys_crm_deals.id')
                    ->leftjoin('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_id','sys_crm_deals.id')
                    ->join('sys_crm_quote_items','sys_crm_quote_items.deal_id','sys_crm_deals.id')
                    ->wherein('sys_crm_quote_items.product_id',[26328,35710,36223,35657,35716,37892])
                    ->where('sys_crm_deals.stage',4)->pluck('sys_crm_deals.id');
                }

                $query->where(function($q) use ($ctrl_date2) {
                    $q->whereRaw("DATE_FORMAT(date_of_resign, '%Y-%m-%d') > ?", [date('Y-m-d', strtotime($ctrl_date2))])
                      ->orWhereNull('date_of_resign');
                });

                if(Auth::user()->role_id != 1 && Auth::user()->role_id != 2){
                        $query->where('user_id', Auth::user()->id);
                }
                $deals = $query->orderby('full_name','asc')->get();
            }
            else{

                if($ctrl_company == 1 && (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 35)){
                    $ctrl_company_list= SysCompany::pluck('id');
                } else{$ctrl_company_list= [$ctrl_company];}


                //$query->where('company_id', $ctrl_company);
                if(Auth::user()->role_id != 1 && Auth::user()->role_id != 2 && Auth::user()->role_id != 8 && Auth::user()->role_id != 35){
                    $query->where('user_id', Auth::user()->id);
                }
                if(Auth::user()->role_id == 8){
                    $teams = DB::table('users')->wherein('role_id',[5,33])->pluck('id');
                    $query->wherein('user_id', $teams);
                }
                
                $query->where(function($q) use ($ctrl_date2) {
                    $q->whereRaw("DATE_FORMAT(date_of_resign, '%Y-%m-%d') > ?", [date('Y-m-d', strtotime($ctrl_date2))])
                      ->orWhereNull('date_of_resign');
                });
                $deals = $query->orderby('full_name','asc')->get();
            }
            
            if(count($deals)>0){
                foreach ($deals as $value) {
                    $user=[$value->user_id];
                    $revenue = SysHelper::get_total_revenue_all_by_user($user,$ctrl_date,$ctrl_date2,$ctrl_company_list,$ps_amc_deal_id);
                    //return $revenue;
                    
                    /*$target = SysCrmSalesTarget::select(db::raw('sum(target) as amount'),db::raw('max(type) as type'))->where('user_id',$value->user_id)
                    ->whereRaw("DATE_FORMAT(target_month, '%Y-%m') >= '".date('Y-m', strtotime($ctrl_date))."' and DATE_FORMAT(target_month, '%Y-%m') <= '".date('Y-m', strtotime($ctrl_date2))."'")->first();*/
                    if($value->combind_user_id==""){
                        //$target = SysCrmSalesTarget::select(db::raw('COALESCE(sum(revenue_target_monthly), 0) as rev_amount'),
                        //db::raw('COALESCE(sum(gp_target_monthly), 0) as gp_amount'))->where('user_id',$value->user_id)
                        //->whereRaw("DATE_FORMAT(target_month_from, '%Y-%m') <= '".date('Y-m', strtotime($ctrl_date))."'")->orderby('target_month_from','desc')->first();
                        $target = SysHelper::calculateSalesTarget([$value->user_id], $ctrl_date, $ctrl_date2,$filter_by,$ctrl_company);
                    } else {
                        $userIds = array_map('intval', explode(',', $value->combind_user_id));
                        /*$target = SysCrmSalesTarget::select(
                            DB::raw('COALESCE(sum(revenue_target_monthly), 0) as rev_amount'),
                            DB::raw('COALESCE(sum(gp_target_monthly), 0) as gp_amount')
                        )
                        ->wherein("user_id", $userIds)
                        ->whereRaw("DATE_FORMAT(target_month_from, '%Y-%m') <= ?", [date('Y-m', strtotime($ctrl_date))]) // Using parameter binding for safety
                        ->orderBy('target_month_from', 'desc')
                        ->first();*/
                        $target = SysHelper::calculateSalesTarget($userIds, $ctrl_date, $ctrl_date2,$filter_by,$ctrl_company);
                    }

                    //return SysHelper::calculateSalesTarget([5800], '2025-01-01', '2025-02-01');

                    if($target['rev_amount']==0){
                        $tp = 0;
                    } else{
                            $tp1 = ($revenue[0] / $target['rev_amount']) * 100;
                            $tp2 = ($revenue[1] / $target['gp_amount']) * 100;
                            $tp = round(($tp1+$tp2)/2,2);
                    }
                    $arrayVariable = [
                        'user_id'  => $value->user_id,
                        'full_name' => $value->full_name,
                        'role_id' => $value->role_id,
                        'combind_user_id' => $value->combind_user_id,
                        'revenue' => $revenue,
                        'revenue_nocombind' => SysHelper::get_total_revenue_all_by_user_nocombind($user,$ctrl_date,$ctrl_date2,$ctrl_company_list,$ps_amc_deal_id),
                        'forcast' => SysHelper::get_total_forcast_all_by_user($user,$ctrl_date,$ctrl_date2,$ctrl_company_list,$ps_amc_deal_id),
                        'revenue_actual' => SysHelper::get_total_revenue_actual_all_by_user($user,$ctrl_date,$ctrl_date2,$ctrl_company_list,$ps_amc_deal_id),
                        'on_process' => SysHelper::get_total_on_process_all_by_user($user,$ctrl_date,$ctrl_date2,$ctrl_company_list,$ps_amc_deal_id),
                        'target' => $target,
                        'dealcount' => SysHelper::get_deal_count_by_user($user,$ctrl_date,$ctrl_date2,$ctrl_company_list,$ps_amc_deal_id),
                        'tp' => $tp,
                    ];
                    $data[]=$arrayVariable;
                }
                $data =  collect($data)->unique('user_id')->values();
                $data = $data->sortByDesc('tp');
                $rev_sum = SysHelper::get_total_revenue_all_by_user_sum($deals->pluck('user_id'),$ctrl_date,$ctrl_date2,$ctrl_company_list,$ps_amc_deal_id);
            }
            else{
                $data='0';
                $rev_sum=[0.00,0.00,0.00];
            }
            
            $form_data = [
                'data' => $data,
                'staff' => $staff,
                'ctrl_owner' => $ctrl_owner,
                'ctrl_company' => $ctrl_company,
                'ctrl_date' => $ctrl_date,
                'ctrl_date2' => $ctrl_date2,
                'filter_by' => $filter_by,
                'rev_sum' => $rev_sum,
            ];

            return view('backEnd.crm.DealSaleReport', compact('data','staff','ctrl_owner','ctrl_company','ctrl_date','ctrl_date2','filter_by','company','rev_sum','ps_amc'));

            session()->put('sale_report_list_query', $form_data);
            return redirect('crm-deals-sales-reports');
            //return $data;

        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function salesreportgp(Request $request)
    {
        try {

            $ctrl_owner = '';
            $filter_by = 'this_month';
            $ctrl_company = session('logged_session_data.company_id');
            $ctrl_date = date('Y-m-01');
            $ctrl_date2 = date("Y-m-t", strtotime($ctrl_date));

            if (Auth::user()->role_id == 1) {
                $staff = SmStaff::select('user_id', 'full_name')->whereRaw("find_in_set($ctrl_company,company_access)")->where('active_status', 1)->orderby('full_name', 'asc')->get();
            } elseif (Auth::user()->role_id == 2) {
                $staff = SmStaff::select('user_id', 'full_name')->where('active_status', 1)->whereRaw("find_in_set($ctrl_company,company_access)")->orderby('full_name', 'asc')->get();
            } else {
                $staff = SmStaff::select('user_id', 'full_name')->where('user_id', Auth::user()->id)->get();
            }

            $query = SmStaff::select('user_id', 'full_name', 'role_id')->whereRaw("find_in_set($ctrl_company,company_access)");
            $query->wherein('role_id', [1, 2, 5, 8]);
            $query->wherenotin('user_id', [51, 28]);

            //->where('active_status',1);
            //$query->wherenotin('user_id', [48,82,21,49,71,28,55,56,35,80,75,30,18,61,3,72,37,78,4,60,73,22,51,31,23,1,59,89,76,24,102]);
            if ($_POST) {
                if ($request->company_id != "") {
                    //$query->where('company_id', $request->company_id);
                    $ctrl_company = $request->company_id;
                }
                if ($request->owner_id != "") {
                    $query->where('user_id', $request->owner_id);
                    $ctrl_owner = $request->owner_id;
                }
                if ($request->date != "" && $request->filter_by == "") {
                    $ctrl_date = $request->date;
                }
                if ($request->date != "" && $request->filter_by == "") {
                    $ctrl_date = $request->date;
                    if ($request->date2 != "") {
                        $ctrl_date2 = $request->date2;
                    }
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

                if (Auth::user()->role_id != 1 && Auth::user()->role_id != 2) {
                    $query->where('user_id', Auth::user()->id);
                }
                $deals = $query->orderby('full_name', 'asc')->get();
            } else {
                //$query->where('company_id', $ctrl_company);
                if (Auth::user()->role_id != 1 && Auth::user()->role_id != 2 && Auth::user()->role_id != 8) {
                    $query->where('user_id', Auth::user()->id);
                }
                if (Auth::user()->role_id == 8) {
                    $teams = DB::table('users')->where('role_id', 5)->pluck('id');
                    $query->wherein('user_id', $teams);
                }
                $deals = $query->orderby('full_name', 'asc')->get();
            }

            if (count($deals) > 0) {
                foreach ($deals as $value) {
                    $user = [$value->user_id];

                    $arrayVariable = [
                        'user_id' => $value->user_id,
                        'full_name' => $value->full_name,
                        'role_id' => $value->role_id,
                        'revenue' => SysHelper::get_total_revenue_all_by_user($user, $ctrl_date, $ctrl_date2, $ctrl_company, []),
                        'forcast' => SysHelper::get_total_forcast_all_by_user($user, $ctrl_date, $ctrl_date2, $ctrl_company, []),
                        'revenue_actual' => SysHelper::get_total_revenue_actual_all_by_user($user, $ctrl_date, $ctrl_date2, $ctrl_company, []),
                        'on_process' => SysHelper::get_total_on_process_all_by_user($user, $ctrl_date, $ctrl_date2, $ctrl_company, []),
                        'target' => SysCrmSalesTarget::where('user_id', $value->user_id)
                            ->whereRaw("DATE_FORMAT(target_month, '%Y-%m') >= '" . date('Y-m', strtotime($ctrl_date)) . "' and DATE_FORMAT(target_month, '%Y-%m') <= '" . date('Y-m', strtotime($ctrl_date2)) . "'")->sum('target'),
                        'dealcount' => SysHelper::get_deal_count_by_user($user, $ctrl_date, $ctrl_date2, [], []),
                    ];
                    $data[] = $arrayVariable;
                }
            } else {
                $data = '0';
            }
            $form_data = [
                'data' => $data,
                'staff' => $staff,
                'ctrl_owner' => $ctrl_owner,
                'ctrl_company' => $ctrl_company,
                'ctrl_date' => $ctrl_date,
                'ctrl_date2' => $ctrl_date2,
                'filter_by' => $filter_by,
            ];

            return view('backEnd.crm.DealSaleReportGP', compact('data', 'staff', 'ctrl_owner', 'ctrl_company', 'ctrl_date', 'ctrl_date2', 'filter_by'));

        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function salesreports()
    {
        try {
            $data = session('sale_report_list_query.data');
            $staff = session('sale_report_list_query.staff');
            $company = session('sale_report_list_query.company');
            $ctrl_owner = session('sale_report_list_query.ctrl_owner');
            $ctrl_company = session('sale_report_list_query.ctrl_company');
            $ctrl_date = session('sale_report_list_query.ctrl_date');
            $ctrl_date2 = session('sale_report_list_query.ctrl_date2');
            $filter_by = session('sale_report_list_query.filter_by');

            return view('backEnd.crm.DealSaleReport', compact('data', 'staff', 'company', 'ctrl_owner', 'ctrl_company', 'ctrl_date', 'ctrl_date2', 'filter_by'));

        } catch (\Throwable $th) {
            return $th;
        }
    }
    public function salesreportlist(Request $request, $uid = null, $cid = null, $m1 = null, $m2 = null)
    {
        $filter_by = '';
        $ctrl_date = $m1;
        $ctrl_date2 = $m2;
        if (empty($uid)) {
            $uid = Auth::user()->id;
        }
        if (empty($cid)) {
            $cid = session('logged_session_data.company_id');
        }
        if ($_POST) {
            $uid = $request->owner_id != "" ? $request->owner_id : $uid;
            $cid = session('logged_session_data.company_id');
            $ctrl_date = SysHelper::normalizeToYmd($request->date);
            $ctrl_date2 = SysHelper::normalizeToYmd($request->date2);
        }


        $combinedUserId = DB::table('sm_staffs')->where('user_id', $uid)->value('combind_user_id');
        if (empty($combinedUserId)) {
            $user = [$uid];
        } else {
            $user = array_values(array_filter(array_map('intval', array_map('trim', explode(',', $combinedUserId)))));
            if (count($user) == 0) {
                $user = [$uid];
            }
        }

        // else if($uid==25 || $uid==41){
        //     $user=[25,41];
        // }
        // else if($uid==44){
        //     $user=[44,45,34,32,79];
        // }
        // else if($uid==104){
        //     $user=[38,39,40,50,63,77,104];
        // }

        try {
            if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2) {
                if ($cid == 1) {
                    $staff = SmStaff::select('user_id', 'full_name')->where('active_status', 1)->orderby('full_name', 'asc')->get();
                    $company = SysCompany::select('id', 'company_name', 'city')->orderby('company_name', 'asc')->get();
                } else {
                    $staff = SmStaff::select('user_id', 'full_name')
                        ->whereRaw("find_in_set($cid,company_access)")
                        //->where('main_company',$ctrl_company)
                        ->where('active_status', 1)->orderby('full_name', 'asc')->get();
                }
            } else {
                $staff = SmStaff::select('user_id', 'full_name')->where('user_id', Auth::user()->id)->get();
            }
            $ctrl_owner = $uid;
            $ctrl_company = $cid;

            if ($request->date != "" && $request->filter_by == "") {
                $ctrl_date = SysHelper::normalizeToYmd($request->date);
            }
            if ($request->date != "" && $request->filter_by == "") {
                $ctrl_date = SysHelper::normalizeToYmd($request->date);
                if ($request->date2 != "") {
                    $ctrl_date2 = SysHelper::normalizeToYmd($request->date2);
                }
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


            // $query = SysSalesInvoice::select(DB::raw('sys_sales_invoice.*, (SELECT max(debit_amount) FROM sys_chartofaccounts_transaction WHERE transaction_type="salesinvoice" and transaction_no=sys_sales_invoice.doc_number and account_id=sys_sales_invoice.customer) AS amount, (SELECT max(code) FROM sys_crm_deals WHERE id=sys_sales_invoice.deal_id) AS code, (SELECT max(deal_profit) FROM sys_crm_deals WHERE id=sys_sales_invoice.deal_id) AS deal_profit, (SELECT max(deal_value) FROM sys_crm_deals WHERE id=sys_sales_invoice.deal_id) AS deal_value, (SELECT max(deal_currency) FROM sys_crm_deals WHERE id=sys_sales_invoice.deal_id) AS deal_currency'),DB::raw('(SELECT SUM(vatamount) FROM sys_sales_invoice_items WHERE si_id = sys_sales_invoice.id) AS total_vatamount'),DB::raw('(SELECT SUM(taxableamount) FROM sys_sales_invoice_items WHERE si_id = sys_sales_invoice.id) AS total_taxableamount'),DB::raw('(SELECT SUM(value) FROM sys_sales_invoice_items WHERE si_id = sys_sales_invoice.id) AS value'),DB::raw('(SELECT SUM(discount) FROM sys_sales_invoice_items WHERE si_id = sys_sales_invoice.id) AS discount'));


            /*$data1 = SysCrmDeals::select('sys_crm_deals.*','sys_crm_deals.id as dealid','dt.invoice','dt.delivery','dt.receivables','sys_crm_deal_track_approval_invoice.created_at as inv_date','deal_percent','pt.title','dt.accounts','dt.sales','dt.purchease_approval','dt.purchease','dt.invoice_approval','dt.invoice','dt.delivery_approval','dt.delivery','dt.receivables_approval','dt.receivables','dt.id as trackid','sys_crm_deal_track_approval_invoice.partial_invoice_amount')
            ->leftjoin('sys_crm_deal_track as dt','dt.deal_id','sys_crm_deals.id')
            ->leftjoin('sys_payment_terms as pt','pt.id','dt.payment_terms')
            ->leftjoin('sys_sales_invoice','sys_sales_invoice.deal_id','sys_crm_deals.id')
            ->leftjoin('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_id','sys_crm_deals.id')
            ->leftjoin('sys_crm_deal_track_approval_receivables','sys_crm_deal_track_approval_receivables.deal_id','sys_crm_deals.id')
            ->where('stage',4);*/


            $query = SysSalesInvoice::select(DB::raw('sys_sales_invoice.*, (SELECT max(debit_amount) FROM sys_chartofaccounts_transaction WHERE transaction_type="salesinvoice" and transaction_no=sys_sales_invoice.doc_number and account_id=sys_sales_invoice.customer) AS amount, (SELECT max(code) FROM sys_crm_deals WHERE id=sys_sales_invoice.deal_id) AS code, (SELECT max(deal_profit) FROM sys_crm_deals WHERE id=sys_sales_invoice.deal_id) AS deal_profit, (SELECT max(deal_value) FROM sys_crm_deals WHERE id=sys_sales_invoice.deal_id) AS deal_value, (SELECT max(deal_currency) FROM sys_crm_deals WHERE id=sys_sales_invoice.deal_id) AS deal_currency'), DB::raw('(SELECT SUM(vatamount) FROM sys_sales_invoice_items WHERE si_id = sys_sales_invoice.id) AS total_vatamount'), DB::raw('(SELECT SUM(taxableamount) FROM sys_sales_invoice_items WHERE si_id = sys_sales_invoice.id) AS total_taxableamount'), DB::raw('(SELECT SUM(value) FROM sys_sales_invoice_items WHERE si_id = sys_sales_invoice.id) AS value'), DB::raw('(SELECT SUM(discount) FROM sys_sales_invoice_items WHERE si_id = sys_sales_invoice.id) AS discount'), DB::raw('dt.accounts'), DB::raw('dt.sales'), DB::raw('dt.purchease_approval'), DB::raw('dt.purchease'), DB::raw('dt.invoice_approval'), DB::raw('dt.invoice'), DB::raw('dt.delivery_approval'), DB::raw('dt.delivery'), DB::raw('dt.receivables_approval'), DB::raw('dt.receivables'), DB::raw('dt.id as trackid'));

            $query->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_sales_invoice.deal_id')
                ->leftjoin('sys_crm_deal_track as dt', 'dt.deal_id', 'sys_crm_deals.id')
                ->leftjoin('sys_crm_deal_track_approval_invoice', 'sys_crm_deal_track_approval_invoice.deal_id', 'sys_crm_deals.id')
                ->leftjoin('sys_crm_deal_track_approval_receivables', 'sys_crm_deal_track_approval_receivables.deal_id', 'sys_crm_deals.id')
                ->leftjoin('sys_payment_terms as pt', 'pt.id', 'dt.payment_terms')
                ->where('stage', 4);
            if ($ctrl_date != "" && $ctrl_date2 != "") {
                $query->whereRaw("DATE_FORMAT(doc_date, '%Y-%m-%d') >= '" . date('Y-m-d', strtotime($ctrl_date)) . "' and DATE_FORMAT(doc_date, '%Y-%m-%d') <= '" . date('Y-m-d', strtotime($ctrl_date2)) . "'");
            }
            if ($ctrl_date != "" && $ctrl_date2 == "") {
                $query->whereRaw("DATE_FORMAT(doc_date, '%Y-%m-%d') = '" . date('Y-m-d', strtotime($ctrl_date)) . "'");
            }
            $query->where('sys_sales_invoice.status', 1);
            // if(count($ps_amc_deal_id)>0){                
            //     $query->wherein('sys_sales_invoice.deal_id',$ps_amc_deal_id);
            // }
            if ($cid != 1) {
                $query->wherein('sys_sales_invoice.company_id', [$ctrl_company]);
            }
            $query->wherein('sales_man', $user);

            $ret = $query->get();
            //return $ret;



            $data1 = SysCrmDeals::select('sys_crm_deals.*', 'sys_crm_deals.id as dealid', 'dt.invoice', 'dt.delivery', 'dt.receivables', 'sys_crm_deal_track_approval_invoice.created_at as inv_date', 'deal_percent', 'pt.title', 'dt.accounts', 'dt.sales', 'dt.purchease_approval', 'dt.purchease', 'dt.invoice_approval', 'dt.invoice', 'dt.delivery_approval', 'dt.delivery', 'dt.receivables_approval', 'dt.receivables', 'dt.id as trackid', 'sys_crm_deal_track_approval_invoice.partial_invoice_amount', DB::raw('(SELECT SUM(taxableamount) FROM sys_sales_invoice_items WHERE si_id = sys_sales_invoice.id) AS total_taxableamount'), 'sys_sales_invoice.deal_discount', 'sys_sales_invoice.doc_number', 'sys_sales_invoice.doc_date', 'sys_sales_invoice.currency')
                ->join('sys_sales_invoice', 'sys_sales_invoice.deal_id', 'sys_crm_deals.id')
                ->leftjoin('sys_crm_deal_track as dt', 'dt.deal_id', 'sys_crm_deals.id')
                ->leftjoin('sys_crm_deal_track_approval_invoice', 'sys_crm_deal_track_approval_invoice.deal_id', 'sys_crm_deals.id')
                ->leftjoin('sys_crm_deal_track_approval_receivables', 'sys_crm_deal_track_approval_receivables.deal_id', 'sys_crm_deals.id')
                ->leftjoin('sys_payment_terms as pt', 'pt.id', 'dt.payment_terms')
                ->where('stage', 4);

            if ($ctrl_date != "" && $ctrl_date2 != "") {
                $data1->whereRaw("DATE_FORMAT(sys_sales_invoice.doc_date, '%Y-%m-%d') >= '" . date('Y-m-d', strtotime($ctrl_date)) . "' and DATE_FORMAT(sys_sales_invoice.doc_date, '%Y-%m-%d') <= '" . date('Y-m-d', strtotime($ctrl_date2)) . "'");
            }
            if ($ctrl_date != "" && $ctrl_date2 == "") {
                $data1->whereRaw("DATE_FORMAT(sys_sales_invoice.doc_date, '%Y-%m-%d') = '" . date('Y-m-d', strtotime($ctrl_date)) . "'");
            }
            $data1->where('sys_crm_deals.stage', 4);
            //->where('sys_crm_deal_track_approval_invoice.status',1);
            /*$data1->where(function ($query) {
                $query->orwhere('sys_crm_deal_track_approval_invoice.status',1)
                ->orwhere('sys_crm_deal_track_approval_receivables.status',1);
            });*/

            if ($cid != 1) {
                $data1->where('sys_crm_deals.company_id', $cid);
            }

            //->where('sys_crm_deals.is_partial_invoice',0);
            $data1->wherein('sys_crm_deals.owner', $user);

            $deals1 = $query->get();
            //return $deals1;

            // Fetch Sales Return data for the same user and date range
            $salesReturnQuery = DB::table('sys_sales_return')
                ->select(
                    'sys_sales_return.id',
                    'sys_sales_return.doc_number',
                    'sys_sales_return.doc_date',
                    'sys_sales_return.customer',
                    'sys_sales_return.company_id',
                    'sys_sales_return.deal_id',
                    'sys_sales_return.currency',
                    DB::raw('(SELECT sum(debit_amount) FROM sys_chartofaccounts_transaction WHERE transaction_type="salesreturn" and transaction_no=sys_sales_return.doc_number) AS amount'),
                    DB::raw('(SELECT code FROM sys_crm_deals WHERE id = sys_sales_return.deal_id LIMIT 1) AS deal_code'),
                    DB::raw('(SELECT deal_name FROM sys_crm_deals WHERE id = sys_sales_return.deal_id LIMIT 1) AS deal_name'),
                    DB::raw('(SELECT deal_value FROM sys_crm_deals WHERE id = sys_sales_return.deal_id LIMIT 1) AS deal_value'),
                    DB::raw('(SELECT deal_profit FROM sys_crm_deals WHERE id = sys_sales_return.deal_id LIMIT 1) AS deal_profit'),
                    DB::raw('(SELECT deal_currency FROM sys_crm_deals WHERE id = sys_sales_return.deal_id LIMIT 1) AS deal_currency')
                )
                ->where('sys_sales_return.status', 1);

            if ($ctrl_date != "" && $ctrl_date2 != "") {
                $salesReturnQuery->whereRaw("DATE_FORMAT(sys_sales_return.doc_date, '%Y-%m-%d') >= '" . date('Y-m-d', strtotime($ctrl_date)) . "' and DATE_FORMAT(sys_sales_return.doc_date, '%Y-%m-%d') <= '" . date('Y-m-d', strtotime($ctrl_date2)) . "'");
            }
            if ($ctrl_date != "" && $ctrl_date2 == "") {
                $salesReturnQuery->whereRaw("DATE_FORMAT(sys_sales_return.doc_date, '%Y-%m-%d') = '" . date('Y-m-d', strtotime($ctrl_date)) . "'");
            }
            if ($cid != 1) {
                $salesReturnQuery->whereIn('sys_sales_return.company_id', [$ctrl_company]);
            }
            $salesReturnQuery->whereIn('sys_sales_return.sales_man', $user);

            $salesReturns = $salesReturnQuery->get();

            // if(Auth::user()->role_id != 1 && Auth::user()->role_id != 9){
            //     $query->where('owner', Auth::user()->id);
            //     if(count($collaboration)>0){ $query->orwherein('sys_crm_deals.id',$coll); }
            // }
            return view('backEnd.crm.DealSaleReportList', compact('deals1', 'salesReturns', 'staff', 'ctrl_owner', 'ctrl_company', 'ctrl_date', 'ctrl_date2', 'filter_by'));
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function gitex2023salesreport()
    {
        try {
            $data = SysCrmLeads::select('owner', 'full_name', DB::raw('COUNT(*) as lead_count'))
                ->join('users', 'users.id', '=', 'sys_crm_leads.owner')
                ->where('source', 'Gitex 2023')->groupBy('owner')->orderby('full_name', 'asc')->get();

            $converted = SysCrmDeals::select('owner', DB::raw('COUNT(*) as deal_count'))
                ->where('source', 'Gitex 2023')->groupBy('owner')->get();

            $won = SysCrmDeals::select('owner', DB::raw('COUNT(*) as won_count'))
                ->where('source', 'Gitex 2023')->where('stage', 4)->groupBy('owner')->get();

            $dealvalue = SysCrmDeals::select('owner', DB::raw('sum(deal_value) as deal_value'))
                ->where('source', 'Gitex 2023')->where('stage', 4)->groupBy('owner')->get();

            $invoiced = SysCrmDeals::select('owner', DB::raw('COUNT(*) as invoice_count'))
                ->join('users', 'users.id', '=', 'sys_crm_deals.owner')
                ->join('sys_crm_deal_track', 'sys_crm_deal_track.deal_id', '=', 'sys_crm_deals.id')
                ->where('sys_crm_deal_track.invoice', 1)
                ->where('source', 'Gitex 2023')->where('stage', 4)->groupBy('owner')->get();

            return view('backEnd.crm.DealSaleReportGitex2023', compact('data', 'converted', 'won', 'dealvalue', 'invoiced'));
        } catch (\Throwable $th) {
            return $th;
        }
    }
    public function gitex2023salesreportlist($uid, $sid)
    {
        try {
            $deals = SysCrmDeals::select('id', 'deal_name', 'estimated_close_date', 'stage', 'deal_currency', 'company_id', 'deal_value', 'created_at', 'cust_id', 'owner')
                ->where('source', 'Gitex 2023')->where('owner', $uid)->get();

            return view('backEnd.crm.DealListGitex2023', compact('deals'));
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function forecastreport(Request $request)
    {
        try {
            if (Auth::user()->role_id == 1 || Auth::user()->role_id == 9) {
                $staff = SmStaff::select('user_id', 'full_name')->wherein('department_id', [2, 5])->where('active_status', 1)->orderby('full_name', 'asc')->get();
                $company = SysCompany::select('id', 'company_name', 'city')->orderby('company_name', 'asc')->get();
            } else {
                if (Auth::user()->id == 44) { //rajiv
                    $teams = array(44, 45, 34, 32, 79);
                    $staff = SmStaff::select('user_id', 'full_name')->wherein('user_id', $teams)->where('active_status', 1)->get();
                } else if (Auth::user()->id == 26) { //Sayed Naeem
                    $teams = array(26, 53, 88, 25, 41, 27, 62, 94, 91, 36, 112);
                    $staff = SmStaff::select('user_id', 'full_name')->wherein('user_id', $teams)->where('active_status', 1)->get();
                } else {
                    $staff = SmStaff::select('user_id', 'full_name')->where('user_id', Auth::user()->id)->where('active_status', 1)->get();
                }
                $company = SysCompany::select('id', 'company_name', 'city')->where('id', session('logged_session_data.company_id'))->orderby('company_name', 'asc')->get();
            }
            $ctrl_owner = '';
            $ctrl_company = session('logged_session_data.company_id');
            $ctrl_date = date('Y-m-01');
            $ctrl_date2 = date("Y-m-t", strtotime($ctrl_date));

            $query = SmStaff::select('user_id', 'full_name')->where('active_status', 1);
            $query->wherenotin('user_id', [48, 82, 71, 28, 55, 56, 35, 80, 75, 30, 61, 3, 72, 37, 78, 4, 60, 73, 22, 51, 31, 23, 1, 59, 89, 76, 24, 102, 100, 21]);

            if ($_POST) {
                if ($request->company_id != "") {
                    $query->where('company_id', $request->company_id);
                    $ctrl_company = $request->company_id;
                }
                if ($request->owner_id != "") {
                    $query->where('user_id', $request->owner_id);
                    $ctrl_owner = $request->owner_id;
                }
                if ($request->date != "") {

                }
                if ($request->date != "") {
                    $ctrl_date = $request->date;
                    if ($request->date2 != "") {
                        $ctrl_date2 = $request->date2;
                    }
                }
                if (Auth::user()->role_id != 1 && Auth::user()->role_id != 9) {
                    if (Auth::user()->id == 44) { //rajiv
                        $teams = array(44, 34, 32, 79);
                        $query->wherein('user_id', $teams);
                    } else {
                        $query->where('user_id', Auth::user()->id);
                    }
                }
                $deal = $query->orderby('full_name', 'asc')->get();
            } else {
                $query->where('company_id', $ctrl_company);
                if (Auth::user()->role_id != 1 && Auth::user()->role_id != 9) {
                    if (Auth::user()->id == 44) { //rajiv
                        $teams = array(44, 34, 32, 79);
                        $query->wherein('user_id', $teams);
                    } else {
                        $query->where('user_id', Auth::user()->id);
                    }
                }
                $deal = $query->orderby('full_name', 'asc')->get();
            }


            if (count($deal) > 0) {
                foreach ($deal as $value) {
                    if ($value->user_id == 26 || $value->user_id == 36 || $value->user_id == 112) {
                        $user = [26, 36, 112];
                    } else {
                        $user = [$value->user_id];
                    }

                    $arrayVariable = [
                        'user_id' => $value->user_id,
                        'full_name' => $value->full_name,
                        'forcast' => SysHelper::get_total_forcast_all_by_user($user, $ctrl_date, $ctrl_date2, $ctrl_company),
                    ];
                    $data[] = $arrayVariable;
                }
            } else {
                $data = '0';
            }
            $form_data = [
                'data' => $data,
                'staff' => $staff,
                'company' => $company,
                'ctrl_owner' => $ctrl_owner,
                'ctrl_company' => $ctrl_company,
                'ctrl_date' => $ctrl_date,
                'ctrl_date2' => $ctrl_date2,
            ];
            session()->put('forecast_report_list_query', $form_data);
            return redirect('crm-deals-forecast-reports');
            //return $data;

        } catch (\Throwable $th) {
            return $th;
        }
    }


    //modified by kp
    public function brandsalesreportnew(Request $request)
    {
        try {

            $ctrl_owner = '';
            $filter_by = 'this_month';
            $ctrl_company = session('logged_session_data.company_id');
            $ctrl_company_list = [];
            $ctrl_date = date('Y-m-01');
            $ctrl_date2 = date("Y-m-t", strtotime($ctrl_date));
            $company = SysCompany::select('id', 'company_name')->orderby('sort_id', 'asc')->get();

            if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 35) {
                if ($ctrl_company == 1) {
                    $staff = SmStaff::select('user_id', 'full_name')->where('active_status', 1)->orderby('full_name', 'asc')->get();
                } else {
                    $staff = SmStaff::select('user_id', 'full_name')->whereRaw("find_in_set($ctrl_company,company_access)")->where('active_status', 1)->orderby('full_name', 'asc')->get();
                }
            } else {
                $staff = SmStaff::select('user_id', 'full_name')->where('user_id', Auth::user()->id)->get();
            }

            $bid = SysCrmQuoteItems::select('itm.brand')->join('sm_items as itm', 'itm.id', 'sys_crm_quote_items.product_id')->distinct()->pluck('itm.brand');
            $query = SysBrand::select('id', 'title')->wherein('id', $bid);

            //->where('active_status',1);
            //$query->wherenotin('user_id', [48,82,21,49,71,28,55,56,35,80,75,30,18,61,3,72,37,78,4,60,73,22,51,31,23,1,59,89,76,24,102]);
            if ($_POST) {
                if ($request->company_id != 1) {
                    //$query->where('company_id', $request->company_id);
                    $ctrl_company_list = [$request->company_id];
                    $ctrl_company = $request->company_id;
                } else {
                    if ($ctrl_company == 1 && (Auth::user()->role_id == 1 || Auth::user()->role_id == 2)) {
                        $ctrl_company_list = SysCompany::pluck('id');
                    }
                }
                if ($request->owner_id != "") {
                    $query->where('user_id', $request->owner_id);
                    $ctrl_owner = $request->owner_id;
                }
                if ($request->date != "" && $request->filter_by == "") {
                    $ctrl_date = SysHelper::normalizeToYmd($request->date);
                }
                if ($request->date != "" && $request->filter_by == "") {
                    $ctrl_date = SysHelper::normalizeToYmd($request->date);

                    if ($request->date2 != "") {
                        $ctrl_date2 = SysHelper::normalizeToYmd($request->date2);
                    }
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

                if (Auth::user()->role_id != 1 && Auth::user()->role_id != 2 && Auth::user()->role_id != 35) {
                    $query->where('user_id', Auth::user()->id);
                }
                $deals = $query->orderby('title', 'asc')->get();
            } else {

                if ($ctrl_company == 1 && (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 35)) {
                    $ctrl_company_list = SysCompany::pluck('id');
                } else {
                    $ctrl_company_list = [$ctrl_company];
                }


                $deals = $query->orderby('title', 'asc')->get();
            }


            if (count($deals) > 0) {
                foreach ($deals as $value) {
                    $brand = [$value->id];



                    $arrayVariable = [
                        'brand_id' => $value->id,
                        'title' => $value->title,
                        'role_id' => $value->role_id,
                        'revenue' => SysHelper::get_total_revenue_all_by_brand($brand, $ctrl_date, $ctrl_date2, $ctrl_company_list),
                        'forcast' => SysHelper::get_total_forcast_all_by_brand($brand, $ctrl_date, $ctrl_date2, $ctrl_company_list),
                        'revenue_actual' => SysHelper::get_total_revenue_actual_all_by_brand($brand, $ctrl_date, $ctrl_date2, $ctrl_company_list),
                        'on_process' => SysHelper::get_total_on_process_all_by_brand($brand, $ctrl_date, $ctrl_date2, $ctrl_company_list),
                        'target' => SysCrmSalesTarget::where('user_id', $value->user_id)
                            ->whereRaw("DATE_FORMAT(target_month, '%Y-%m') >= '" . date('Y-m', strtotime($ctrl_date)) . "' and DATE_FORMAT(target_month, '%Y-%m') <= '" . date('Y-m', strtotime($ctrl_date2)) . "'")->sum('target'),
                        'dealcount' => SysHelper::get_deal_count_by_brand($brand, $ctrl_date, $ctrl_date2),
                    ];
                    $data[] = $arrayVariable;
                }
            } else {
                $data = '0';
            }
            $form_data = [
                'data' => $data,
                'staff' => $staff,
                'ctrl_owner' => $ctrl_owner,
                'ctrl_company' => $ctrl_company,
                'ctrl_date' => $ctrl_date,
                'ctrl_date2' => $ctrl_date2,
                'filter_by' => $filter_by,
            ];
            return view('backEnd.crm.DealSaleReportBrand', compact('data', 'staff', 'ctrl_owner', 'ctrl_company', 'ctrl_date', 'ctrl_date2', 'filter_by', 'company'));

            session()->put('sale_report_list_query', $form_data);
            return redirect('crm-deals-sales-reports');
            //return $data;

        } catch (\Throwable $th) {
            return $th;
        }
    }
    public function forecastreportlistbrand($bid, $cid, $m1, $m2)
    {
        try {

            $ctrl_company = $cid;
            if ($cid == 1) {
                $cid = SysCompany::pluck('id');
            } else {
                $cid = [$cid];
            }

            if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2) {
                $staff = SmStaff::select('user_id', 'full_name')->where('active_status', 1)->orderby('full_name', 'asc')->get();
                $company = SysCompany::select('id', 'company_name', 'city')->orderby('company_name', 'asc')->get();
            } else {
                $staff = SmStaff::select('user_id', 'full_name')->where('user_id', Auth::user()->id)->get();
                $company = SysCompany::select('id', 'company_name', 'city')->where('id', session('logged_session_data.company_id'))->orderby('company_name', 'asc')->get();
            }
            $ctrl_owner = $bid;
            $ctrl_date = $m1;
            $ctrl_date2 = $m2;
            $query = SysCrmDeals::wherein('stage', [1, 2, 3])

                ->wherein('sys_crm_deals.company_id', $cid)
                ->whereNotIn('sys_crm_deals.id', function ($query) {
                    $query->select('deal_id')->from('sys_crm_deal_track_approval_invoice')->where('status', 1);
                });

            $query->where('owner', $bid);

            $ctrl_owner = $bid;

            if ($m1 != "") {
                $ctrl_date = $m1;
                if ($m2 != "") {
                    $query->whereRaw("DATE_FORMAT(estimated_close_date, '%Y-%m-%d') >= '" . date('Y-m-d', strtotime($m1)) . "' and DATE_FORMAT(estimated_close_date, '%Y-%m-%d') <= '" . date('Y-m-d', strtotime($m2)) . "'");
                    $ctrl_date2 = $m2;
                } else {
                    $query->whereRaw("DATE_FORMAT(estimated_close_date, '%Y-%m-%d') = '" . date('Y-m-d', strtotime($m1)) . "'");
                }
            }

            $deals = $query->orderby('id', 'desc')->get();

            return view('backEnd.crm.DealForecastReportList', compact('deals', 'staff', 'company', 'ctrl_owner', 'ctrl_company', 'ctrl_date', 'ctrl_date2'));
        } catch (\Throwable $th) {
            return $th;
        }
    }


    public function forecastreports()
    {
        try {
            $data = session('forecast_report_list_query.data');
            $staff = session('forecast_report_list_query.staff');
            $company = session('forecast_report_list_query.company');
            $ctrl_owner = session('forecast_report_list_query.ctrl_owner');
            $ctrl_company = session('forecast_report_list_query.ctrl_company');
            $ctrl_date = session('forecast_report_list_query.ctrl_date');
            $ctrl_date2 = session('forecast_report_list_query.ctrl_date2');
            return view('backEnd.crm.DealForecastReport', compact('data', 'staff', 'company', 'ctrl_owner', 'ctrl_company', 'ctrl_date', 'ctrl_date2'));

        } catch (\Throwable $th) {
            return $th;
        }
    }
    public function forecastreportlist($uid, $cid, $m1, $m2)
    {
        try {



return redirect('crm-deals/show?stage_id=3&owner_id='.$uid.'&date='.$m1.'&date2='.$m2);
            
        
        } catch (\Throwable $th) {
            return $th;
        }
    }
    public function onprocessreportlist($uid, $cid, $m1, $m2)
    {
        try {
      

            return redirect('crm-deals/show?stage_id=4&owner_id='.$uid.'&date='.$m1.'&date2='.$m2);
        } catch (\Throwable $th) {
            return $th;
        }

    }


    public function leadconvertionreport(Request $request)
    {
        try {
            $ctrl_date = date('Y-m');
            if ($_POST) {
                if ($request->date != "") {
                    $ctrl_date = $request->date;
                    $query = SysCrmLeads::select('created_by');
                    $query->whereRaw("DATE_FORMAT(created_at, '%Y-%m') >= '" . date('Y-m', strtotime($ctrl_date)) . "'");
                }
            } else {
                $query = SysCrmLeads::select('created_by');
                $query->whereRaw("DATE_FORMAT(created_at, '%Y-%m') >= '" . date('Y-m', strtotime($ctrl_date)) . "'");
            }

            $data = $query->distinct()->get();

            //return $data;
            /*if(count($leads)>0){
                foreach ($leads as $value) {
                    $arrayVariable = [
                        'user_id'  => $value->user_id,
                        'full_name' => $value->full_name,
                        'forcast' => SysHelper::get_total_forcast_all_by_user($value->user_id,$ctrl_date,$ctrl_date2),
                    ];
                    $data[]=$arrayVariable;
                }
            }
            else{
                $data='0';
            }*/

            return view('backEnd.crm.LeadConvertionReport', compact('data', 'ctrl_date'));
            //return $data;

        } catch (\Throwable $th) {
            return $th;
        }
    }




    public function dealpageservicefilterlist($id, $date, $company)
    {
        return redirect()->back();
        try {
            $ctrl_date = date('Y-m-01');
            $ctrl_date2 = date('Y-m-d');
            $type = "service";

            if ($company == 1 || $company == 3 || $company == 13) {
                $data1 = SysCrmDeals::select('deal_name', 'deal_value', 'qty', 'price', 'discount', 'deal_currency', 'source', 'cust_id', 'sys_crm_quote_items.deal_id', 'estimated_close_date', 'owner')
                    ->leftjoin('sys_crm_deal_track_approval_invoice', 'sys_crm_deal_track_approval_invoice.deal_id', 'sys_crm_deals.id')
                    ->leftjoin('sys_crm_quote_items', 'sys_crm_quote_items.deal_id', 'sys_crm_deals.id')
                    ->wherein('sys_crm_quote_items.product_id', [1417, 1427, 4714, 8408, 8490, 8493, 8729, 8806, 9319, 10247, 8544, 9726, 9728, 9780, 10294])
                    ->where('sys_crm_deals.stage', 4)->where('sys_crm_deals.is_partial_invoice', 0)
                    ->where('sys_crm_deal_track_approval_invoice.status', 1)->where('sys_crm_deals.company_id', $company);
            } else {
                $data1 = SysCrmDeals::select('deal_name', 'deal_value', 'qty', 'price', 'discount', 'deal_currency', 'source', 'cust_id', 'sys_crm_quote_items.deal_id', 'estimated_close_date', 'owner')
                    ->join('sys_crm_quote_items', 'sys_crm_quote_items.deal_id', 'sys_crm_deals.id')
                    ->wherein('sys_crm_quote_items.product_id', [1417, 1427, 4714, 8408, 8490, 8493, 8729, 8806, 9319, 10247, 8544, 9726, 9728, 9780, 10294])
                    ->where('sys_crm_deals.stage', 4)->where('sys_crm_deals.company_id', $company);
            }

            $data2 = SysCrmDeals::select('deal_name', 'deal_value', 'qty', 'price', 'discount', 'deal_currency', 'source', 'cust_id', 'sys_crm_quote_items.deal_id', 'estimated_close_date', 'owner')
                ->leftjoin('sys_crm_quote_items', 'sys_crm_quote_items.deal_id', 'sys_crm_deals.id')
                ->wherein('sys_crm_quote_items.product_id', [1417, 1427, 4714, 8408, 8490, 8493, 8729, 8806, 9319, 10247, 8544, 9726, 9728, 9780, 10294])
                ->where('sys_crm_deals.company_id', $company)->wherein('stage', [1, 2, 3])
                ->whereNotIn('sys_crm_deals.id', function ($query) {
                    $query->select('deal_id')->from('sys_crm_deal_track_approval_invoice')->where('status', 1);
                });

            if ($date == "d") {
                if ($company == 1 || $company == 3 || $company == 13) {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') = '" . date('Y-m-d') . "'");
                } else {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') = '" . date('Y-m-d') . "'");
                }
                $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') = '" . date('Y-m-d') . "'");
            }
            if ($date == "m") {
                if ($company == 1 || $company == 3 || $company == 13) {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m') = '" . date('Y-m') . "'");
                } else {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m') = '" . date('Y-m') . "'");
                }
                $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m') = '" . date('Y-m') . "'");
            }
            if ($date == "y") {
                if ($company == 1 || $company == 3 || $company == 13) {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y') = '" . date('Y') . "'");
                } else {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y') = '" . date('Y') . "'");
                }
                $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y') = '" . date('Y') . "'");
            }
            if ($date == "q") {
                $quarter = SysHelper::get_quarter(date('m'));
                $start_date = $quarter[0];
                $end_date = $quarter[1];

                if ($company == 1 || $company == 3 || $company == 13) {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '" . $start_date . "' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '" . $end_date . "'");
                } else {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') >= '" . $start_date . "' and DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') <= '" . $end_date . "'");
                }
                $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') >= '" . $start_date . "' and DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') <= '" . $end_date . "'");
            }
            if ($date == "pm") {
                $c_date = Carbon::createFromFormat('Y-m', date('Y-m'))->subMonth();
                $pm_date = $c_date->format('Y-m');

                if ($company == 1 || $company == 3 || $company == 13) {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m') = '" . $pm_date . "'");
                } else {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m') = '" . $pm_date . "'");
                }
                $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m') = '" . $pm_date . "'");
            }
            if ($date == "pq") {
                $quarter = SysHelper::get_pre_quarter(date('m'));
                $start_date = $quarter[0];
                $end_date = $quarter[1];

                if ($company == 1 || $company == 3 || $company == 13) {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '" . $start_date . "' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '" . $end_date . "'");
                } else {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') >= '" . $start_date . "' and DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') <= '" . $end_date . "'");
                }
                $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') >= '" . $start_date . "' and DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') <= '" . $end_date . "'");
            }

            if (Auth::user()->role_id == 1 || Auth::user()->id == 33) { //admin
                // $data1->where('sys_crm_deals.company_id',1);
                // $data2->where('sys_crm_deals.company_id',1);
            } else {
                if (Auth::user()->id == 26 || Auth::user()->id == 36 || Auth::user()->id == 112) {//26 Naeem & 36 Arianne
                    $teams = array(26, 36, 112);
                    $data1->wherein('sys_crm_deals.owner', $teams);
                    $data2->wherein('sys_crm_deals.owner', $teams);
                } else {
                    $data1->where('sys_crm_deals.owner', Auth::user()->id);
                    $data2->where('sys_crm_deals.owner', Auth::user()->id);
                }
            }



            if ($id == "service_revenue") {
                $data = $data1->get();
                return view('backEnd.crm.DealServiceReport', compact('data', 'id', 'type'));
            }
            if ($id == "service_forcast") {
                $data = $data2->get();
                return view('backEnd.crm.DealServiceReport', compact('data', 'id', 'type'));
            }


        } catch (\Throwable $th) {
            return $th;
        }
    }
    public function dealpageamcfilterlist($id, $date, $company)
    {
        return redirect()->back();
        try {
            $ctrl_date = date('Y-m-01');
            $ctrl_date2 = date('Y-m-d');
            $type = "amc";

            if ($company == 1 || $company == 3 || $company == 13) {
                $data1 = SysCrmDeals::select('deal_name', 'deal_value', 'qty', 'price', 'discount', 'deal_currency', 'source', 'cust_id', 'sys_crm_quote_items.deal_id', 'estimated_close_date', 'owner')
                    ->leftjoin('sys_crm_deal_track_approval_invoice', 'sys_crm_deal_track_approval_invoice.deal_id', 'sys_crm_deals.id')
                    ->join('sys_crm_quote_items', 'sys_crm_quote_items.deal_id', 'sys_crm_deals.id')
                    ->wherein('sys_crm_quote_items.product_id', [9976, 10465, 10497])
                    ->where('sys_crm_deals.stage', 4)->where('sys_crm_deals.is_partial_invoice', 0)
                    ->where('sys_crm_deal_track_approval_invoice.status', 1)->where('sys_crm_deals.company_id', $company);
            } else {
                $data1 = SysCrmDeals::select('deal_name', 'deal_value', 'qty', 'price', 'discount', 'deal_currency', 'source', 'cust_id', 'sys_crm_quote_items.deal_id', 'estimated_close_date', 'owner')
                    ->join('sys_crm_quote_items', 'sys_crm_quote_items.deal_id', 'sys_crm_deals.id')
                    ->wherein('sys_crm_quote_items.product_id', [9976, 10465, 10497])
                    ->where('sys_crm_deals.stage', 4)->where('sys_crm_deals.company_id', $company);
            }

            $data2 = SysCrmDeals::select('deal_name', 'deal_value', 'qty', 'price', 'discount', 'deal_currency', 'source', 'cust_id', 'sys_crm_quote_items.deal_id', 'estimated_close_date', 'owner')
                ->join('sys_crm_quote_items', 'sys_crm_quote_items.deal_id', 'sys_crm_deals.id')
                ->wherein('sys_crm_quote_items.product_id', [9976, 10465, 10497])
                ->where('sys_crm_deals.company_id', $company)
                ->wherein('stage', [1, 2, 3])
                ->whereNotIn('sys_crm_deals.id', function ($query) {
                    $query->select('deal_id')->from('sys_crm_deal_track_approval_invoice')->where('status', 1);
                });

            if ($date == "d") {
                if ($company == 1 || $company == 3 || $company == 13) {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') = '" . date('Y-m-d') . "'");
                } else {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') = '" . date('Y-m-d') . "'");
                }
                $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') = '" . date('Y-m-d') . "'");
            }
            if ($date == "m") {
                if ($company == 1 || $company == 3 || $company == 13) {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m') = '" . date('Y-m') . "'");
                } else {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m') = '" . date('Y-m') . "'");
                }
                $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m') = '" . date('Y-m') . "'");
            }
            if ($date == "y") {
                if ($company == 1 || $company == 3 || $company == 13) {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y') = '" . date('Y') . "'");
                } else {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y') = '" . date('Y') . "'");
                }
                $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y') = '" . date('Y') . "'");
            }
            if ($date == "q") {
                $quarter = SysHelper::get_quarter(date('m'));
                $start_date = $quarter[0];
                $end_date = $quarter[1];

                if ($company == 1 || $company == 3 || $company == 13) {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '" . $start_date . "' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '" . $end_date . "'");
                } else {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') >= '" . $start_date . "' and DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') <= '" . $end_date . "'");
                }
                $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') >= '" . $start_date . "' and DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') <= '" . $end_date . "'");
            }
            if ($date == "pm") {
                $c_date = Carbon::createFromFormat('Y-m', date('Y-m'))->subMonth();
                $pm_date = $c_date->format('Y-m');

                if ($company == 1 || $company == 3 || $company == 13) {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m') = '" . $pm_date . "'");
                } else {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m') = '" . $pm_date . "'");
                }
                $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m') = '" . $pm_date . "'");
            }
            if ($date == "pq") {
                $quarter = SysHelper::get_pre_quarter(date('m'));
                $start_date = $quarter[0];
                $end_date = $quarter[1];

                if ($company == 1 || $company == 3 || $company == 13) {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '" . $start_date . "' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '" . $end_date . "'");
                } else {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') >= '" . $start_date . "' and DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') <= '" . $end_date . "'");
                }
                $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') >= '" . $start_date . "' and DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') <= '" . $end_date . "'");
            }

            if (Auth::user()->role_id == 1 || Auth::user()->id == 33) { //admin
                // $data1->where('sys_crm_deals.company_id',1);
                // $data2->where('sys_crm_deals.company_id',1);
            } else {
                if (Auth::user()->id == 26 || Auth::user()->id == 36 || Auth::user()->id == 112) {//26 Naeem & 36 Arianne
                    $teams = array(26, 36, 112);
                    $data1->wherein('sys_crm_deals.owner', $teams);
                    $data2->wherein('sys_crm_deals.owner', $teams);
                } else {
                    $data1->where('sys_crm_deals.owner', Auth::user()->id);
                    $data2->where('sys_crm_deals.owner', Auth::user()->id);
                }
            }



            if ($id == "amc_revenue") {
                $data = $data1->get();
                return view('backEnd.crm.DealServiceReport', compact('data', 'id', 'type'));
            }
            if ($id == "amc_forcast") {
                $data = $data2->get();
                return view('backEnd.crm.DealServiceReport', compact('data', 'id', 'type'));
            }


        } catch (\Throwable $th) {
            return $th;
        }
    }
    public function dealpageprojectfilterlist($id, $date, $company)
    {
        return redirect()->back();

        try {
            $ctrl_date = date('Y-m-01');
            $ctrl_date2 = date('Y-m-d');
            $type = "project";

            if ($company == 1 || $company == 3 || $company == 13) {
                $data1 = SysCrmDeals::select('deal_value', 'deal_currency', 'source', 'cust_id', 'sys_crm_deals.id', 'deal_name', 'estimated_close_date', 'owner')
                    ->leftjoin('sys_crm_deal_track_approval_invoice', 'sys_crm_deal_track_approval_invoice.deal_id', 'sys_crm_deals.id')
                    ->where('sys_crm_deals.isproject', 1)
                    ->where('sys_crm_deals.stage', 4)->where('sys_crm_deals.is_partial_invoice', 0)
                    ->where('sys_crm_deal_track_approval_invoice.status', 1)->where('sys_crm_deals.company_id', $company);
            } else {
                $data1 = SysCrmDeals::select('deal_value', 'deal_currency', 'source', 'cust_id', 'sys_crm_deals.id', 'deal_name', 'estimated_close_date', 'owner')
                    ->where('sys_crm_deals.isproject', 1)
                    ->where('sys_crm_deals.stage', 4)->where('sys_crm_deals.company_id', $company);
            }

            $data2 = SysCrmDeals::select('deal_value', 'deal_currency', 'source', 'cust_id', 'sys_crm_deals.id', 'deal_name', 'estimated_close_date', 'owner')
                ->where('sys_crm_deals.isproject', 1)
                ->where('sys_crm_deals.company_id', $company)
                ->wherein('stage', [1, 2, 3])
                ->whereNotIn('sys_crm_deals.id', function ($query) {
                    $query->select('deal_id')->from('sys_crm_deal_track_approval_invoice')->where('status', 1);
                });

            if ($date == "d") {
                if ($company == 1 || $company == 3 || $company == 13) {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') = '" . date('Y-m-d') . "'");
                } else {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') = '" . date('Y-m-d') . "'");
                }
                $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') = '" . date('Y-m-d') . "'");
            }
            if ($date == "m") {
                if ($company == 1 || $company == 3 || $company == 13) {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m') = '" . date('Y-m') . "'");
                } else {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m') = '" . date('Y-m') . "'");
                }
                $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m') = '" . date('Y-m') . "'");
            }
            if ($date == "y") {
                if ($company == 1 || $company == 3 || $company == 13) {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y') = '" . date('Y') . "'");
                } else {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y') = '" . date('Y') . "'");
                }
                $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y') = '" . date('Y') . "'");
            }
            if ($date == "q") {
                $quarter = SysHelper::get_quarter(date('m'));
                $start_date = $quarter[0];
                $end_date = $quarter[1];

                if ($company == 1 || $company == 3 || $company == 13) {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '" . $start_date . "' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '" . $end_date . "'");
                } else {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') >= '" . $start_date . "' and DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') <= '" . $end_date . "'");
                }
                $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') >= '" . $start_date . "' and DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') <= '" . $end_date . "'");
            }
            if ($date == "pm") {
                $c_date = Carbon::createFromFormat('Y-m', date('Y-m'))->subMonth();
                $pm_date = $c_date->format('Y-m');

                if ($company == 1 || $company == 3 || $company == 13) {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m') = '" . $pm_date . "'");
                } else {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m') = '" . $pm_date . "'");
                }
                $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m') = '" . $pm_date . "'");
            }
            if ($date == "pq") {
                $quarter = SysHelper::get_pre_quarter(date('m'));
                $start_date = $quarter[0];
                $end_date = $quarter[1];

                if ($company == 1 || $company == 3 || $company == 13) {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '" . $start_date . "' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '" . $end_date . "'");
                } else {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') >= '" . $start_date . "' and DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') <= '" . $end_date . "'");
                }
                $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') >= '" . $start_date . "' and DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') <= '" . $end_date . "'");
            }

            if (Auth::user()->role_id == 1 || Auth::user()->id == 33) { //admin
                // $data1->where('sys_crm_deals.company_id',1);
                // $data2->where('sys_crm_deals.company_id',1);
            } else {
                if (Auth::user()->id == 26 || Auth::user()->id == 36 || Auth::user()->id == 112) {//26 Naeem & 36 Arianne
                    $teams = array(26, 36, 112);
                    $data1->wherein('sys_crm_deals.owner', $teams);
                    $data2->wherein('sys_crm_deals.owner', $teams);
                } else {
                    $data1->where('sys_crm_deals.owner', Auth::user()->id);
                    $data2->where('sys_crm_deals.owner', Auth::user()->id);
                }
            }



            if ($id == "project_revenue") {
                $data = $data1->get();
                return view('backEnd.crm.DealProjectReport', compact('data', 'id', 'type'));
            }
            if ($id == "project_forcast") {
                $data = $data2->get();
                return view('backEnd.crm.DealProjectReport', compact('data', 'id', 'type'));
            }


        } catch (\Throwable $th) {
            return $th;
        }
    }


    //from admin
    public function dealpagesalesperformancefilterlist($id, $date, $company)
    {
        return redirect()->back();
        try {
            $ctrl_date = date('Y-m-01');
            $ctrl_date2 = date('Y-m-d');
            $type = "salesperformance";

            if ($company == 1 || $company == 3 || $company == 13) {
                $data1 = SysCrmDeals::select('sys_crm_deals.id as dealid', 'deal_value', 'deal_currency', 'source', 'cust_id', 'sys_crm_deals.id', 'deal_name', 'estimated_close_date', 'owner', 'deal_percent')
                    ->leftjoin('sys_crm_deal_track_approval_invoice', 'sys_crm_deal_track_approval_invoice.deal_id', 'sys_crm_deals.id')
                    ->where('sys_crm_deals.stage', 4)->where('sys_crm_deals.is_partial_invoice', 0)
                    ->where('sys_crm_deal_track_approval_invoice.status', 1)->where('sys_crm_deals.company_id', $company);
            } else {
                $data1 = SysCrmDeals::select('sys_crm_deals.id as dealid', 'deal_value', 'deal_currency', 'source', 'cust_id', 'sys_crm_deals.id', 'deal_name', 'estimated_close_date', 'owner', 'deal_percent')
                    ->where('sys_crm_deals.stage', 4)->where('sys_crm_deals.company_id', $company);
            }

            $data2 = SysCrmDeals::select('deal_value', 'deal_currency', 'source', 'cust_id', 'sys_crm_deals.id', 'deal_name', 'estimated_close_date', 'owner')
                ->where('sys_crm_deals.company_id', $company)
                ->wherein('stage', [1, 2, 3])
                ->whereNotIn('id', function ($query) {
                    $query->select('deal_id')->from('sys_crm_deal_track_approval_invoice')->where('status', 1);
                });

            if ($date == "d") {
                if ($company == 1 || $company == 3 || $company == 13) {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') = '" . date('Y-m-d') . "'");
                } else {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') = '" . date('Y-m-d') . "'");
                }
                $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') = '" . date('Y-m-d') . "'");
            }
            if ($date == "m") {
                if ($company == 1 || $company == 3 || $company == 13) {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m') = '" . date('Y-m') . "'");
                } else {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m') = '" . date('Y-m') . "'");
                }
                $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m') = '" . date('Y-m') . "'");
            }
            if ($date == "y") {
                if ($company == 1 || $company == 3 || $company == 13) {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y') = '" . date('Y') . "'");
                } else {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y') = '" . date('Y') . "'");
                }
                $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y') = '" . date('Y') . "'");
            }
            if ($date == "q") {
                $quarter = SysHelper::get_quarter(date('m'));
                $start_date = $quarter[0];
                $end_date = $quarter[1];

                if ($company == 1 || $company == 3 || $company == 13) {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '" . $start_date . "' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '" . $end_date . "'");
                } else {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') >= '" . $start_date . "' and DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') <= '" . $end_date . "'");
                }
                $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') >= '" . $start_date . "' and DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') <= '" . $end_date . "'");
            }
            if ($date == "pm") {
                $c_date = Carbon::createFromFormat('Y-m', date('Y-m'))->subMonth();
                $pm_date = $c_date->format('Y-m');

                if ($company == 1 || $company == 3 || $company == 13) {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m') = '" . $pm_date . "'");
                } else {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m') = '" . $pm_date . "'");
                }
                $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m') = '" . $pm_date . "'");
            }
            if ($date == "pq") {
                $quarter = SysHelper::get_pre_quarter(date('m'));
                $start_date = $quarter[0];
                $end_date = $quarter[1];

                if ($company == 1 || $company == 3 || $company == 13) {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '" . $start_date . "' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '" . $end_date . "'");
                } else {
                    $data1->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') >= '" . $start_date . "' and DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') <= '" . $end_date . "'");
                }
                $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') >= '" . $start_date . "' and DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') <= '" . $end_date . "'");
            }

            if (Auth::user()->role_id == 1 || Auth::user()->id == 33) { //admin
                // $data1->where('sys_crm_deals.company_id',1);
                // $data2->where('sys_crm_deals.company_id',1);
            } else {
                if (Auth::user()->id == 26 || Auth::user()->id == 36 || Auth::user()->id == 112) {//26 Naeem & 36 Arianne
                    $teams = array(26, 36, 112);
                    $data1->wherein('sys_crm_deals.owner', $teams);
                    $data2->wherein('sys_crm_deals.owner', $teams);
                } else if (Auth::user()->id == 44) {//44 rajiv
                    $teams = array(44, 34, 32, 79);
                    $data1->wherein('sys_crm_deals.owner', $teams);
                    $data2->wherein('sys_crm_deals.owner', $teams);
                } else {
                    $data1->where('sys_crm_deals.owner', Auth::user()->id);
                    $data2->where('sys_crm_deals.owner', Auth::user()->id);
                }
            }



            if ($id == "revenue") {
                $data = $data1->get();
                return view('backEnd.crm.DealSalesPerformanceReport', compact('data', 'id', 'type'));
            }
            if ($id == "forcast") {
                $data = $data2->get();
                return view('backEnd.crm.DealSalesPerformanceReport', compact('data', 'id', 'type'));
            }


        } catch (\Throwable $th) {
            return $th;
        }
    }

}
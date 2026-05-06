<?php

namespace App\Http\Controllers;

use App\ApiBaseMethod;
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
use App\SysCrmAmcServiceTable;
use App\SysCrmAmcTable;
use App\SysCrmAmcTableServiceComments;
use App\SysCrmDeals;
use App\SysCrmDealTrack;
use App\SysCrmDealTrackApprovalInvoice;
use App\SysCrmDealTrackApprovalPurchease;
use App\SysCrmDealTrackApprovalReceivables;
use App\SysCrmLeads;
use App\SysCrmLeadsComments;
use App\SysCrmPSServiceTable;
use App\SysCrmQuoteCSItems;
use App\SysCrmQuoteItems;
use App\SysCrmSalesTarget;
use App\SysCrmService;
use App\SysCrmServiceAssign;
use App\SysCrmSupport;
use App\SysCurrencySettings;
use App\SysCustSuppl;
use App\SysDailyQuotes;
use App\SysHelper;
use App\SysItemOpeningStock;
use App\SysItemStock;
use App\SysPaymentTerms;
use App\SysSalesInvoice;
use App\SysShipping;
use App\SysStockIn;
use App\SysStockInItems;
use App\SysStockInSerialNo;
use App\SysSupplierType;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Return_;
use Validator;

class SysCrmDashboardFunctionController extends Controller
{
    public function get_user_company(Request $request)
    {
        try {
            $id = SmStaff::where('user_id', $request->user_id)->pluck('company_access')->first();
            $id2 = explode(',', $id);
            $ret = SysCompany::whereIn('id', $id2)->select('id', 'company_name')->get();
            return json_encode(array('data' => $ret));
        } catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }

    public function dashboard_views(Request $request, $comid, $user_id)
    {
        $role = SmStaff::select('role_id', 'full_name', 'staff_photo', 'ds.title')->join('sm_designations as ds', 'ds.id', 'sm_staffs.designation_id')
            ->where('user_id', $user_id)->first();
        $role_id = $role->role_id;
        $full_name = $role->full_name;
        $staff_photo = $role->staff_photo;
        $designation = $role->title;

        if (Auth::user()->role_id != 1) {
            return "You do not have permission to view this option.";
        }
        try {
            if ($role_id == 5) {
                return $this->sales($request, $user_id, $comid, $full_name, $staff_photo, $designation);
            }
            if ($role_id == 3) {
                return $this->accounts_receivable($user_id, $comid, $full_name, $staff_photo, $designation);
            }
            if ($role_id == 27) {
                return $this->accounts_head($user_id, $comid, $full_name, $staff_photo, $designation);
            }
            if ($role_id == 4) {
                return $this->billing($user_id, $comid, $full_name, $staff_photo, $designation);
            }
            if ($role_id == 6) {
                return $this->logistic_dept($user_id, $comid, $full_name, $staff_photo, $designation);
            }
            if ($role_id == 29) {
                return $this->logistic_dept_head($user_id, $comid, $full_name, $staff_photo, $designation);
            }
            if ($role_id == 8) {
                return $this->sales_department_head($user_id, $comid, $full_name, $staff_photo, $designation);
            }
            if ($role_id == 9) {
                return $this->procurement_dept_head($user_id, $comid, $full_name, $staff_photo, $designation);
            }
            if ($role_id == 10) {
                return $this->accounts_payable_procurement_dept($user_id, $comid, $full_name, $staff_photo, $designation);
            }
            if ($role_id == 26) {
                return $this->marketing_dept($user_id, $comid, $full_name, $staff_photo, $designation);
            }
            if ($role_id == 28) {
                return $this->accounts($user_id, $comid, $full_name, $staff_photo, $designation);
            }
            if ($role_id == 31) {
                return $this->support_engineer($user_id, $comid, $full_name, $staff_photo, $designation);
            }
            if ($role_id == 32) {
                return $this->support_co_ordinator($user_id, $comid, $full_name, $staff_photo, $designation);
            }
        } catch (\Throwable $th) {
            return $th;
        }
    }


    //sales Role 5 start
    public static function sales($request, $user_id, $company_id, $d_full_name, $d_staff_photo, $d_designation)
    {

        try {
            
            $teams = array($user_id);

            /*
            Exclude codes start
            $lead_ret = SysHelper::get_lead_filter('m', $company_id);
            $deal_ret = SysHelper::get_deal_filter('m', $company_id);

            //1-New, 2-Qualified, 3-Unqualified, 0-converted            
            $total_leads_new            = $lead_ret[0];
            $total_leads_qualified      = $lead_ret[1];
            $total_leads_unqualified    = $lead_ret[2];
            $leads_type_reseller         = $lead_ret[3];
            $leads_type_enduser         = $lead_ret[4];
            $leads_type_ecommerce       = $lead_ret[5];

            //1-Prospecting, 2-Quote, 3-Closure, 4-Won, 5-Lost

            $total_deals_prospecting    = $deal_ret[0];
            $total_deals_quote          = $deal_ret[1];
            $total_deals_closure        = $deal_ret[2];
            $total_deals_won            = $deal_ret[3];
            $total_deals_lost           = $deal_ret[4];
            $deals_type_project         = $deal_ret[5];
            $deals_type_channel         = $deal_ret[6];
            $deals_type_corporate       = $deal_ret[7];
            
            $sales_target_indu = SysCrmSalesTarget::whereRaw("DATE_FORMAT(target_month, '%Y-%m') = '" . date('Y-m') . "'")->where('user_id', $user_id)->where('company_id', $company_id)->orderby('id', 'asc')->get();
            
            $order_in_process_all = SysCrmDealTrack::select()->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '" . date('Y-m') . "'")
                ->wherein('sys_crm_deals.owner', $teams)
                ->where('sys_crm_deal_track.receivables', '!=', 1)
                ->where('sys_crm_deal_track.company_id', $company_id)
                ->orderby('sys_crm_deal_track.id', 'desc')->get();

                
            $sales =  SysCrmReportController::get_total_sales_revenue($teams, 'm', $company_id);
            Exclude codes end
            */


            $leads_type = SysCrmLeads::select('isproject')->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '" . date('Y-m') . "'")->where('company_id', $company_id)->wherein('owner', $teams)->get();

            $deals_type = SysCrmDeals::select('isproject')->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '" . date('Y-m') . "'")->where('company_id', $company_id)->wherein('owner', $teams)->get();

            //$sales_target = SysCrmSalesTarget::whereRaw("DATE_FORMAT(target_month, '%Y-%m') = '" . date('Y-m') . "'")->where('target_month_from',null)->wherein('user_id', $teams)->orderby('id', 'asc')->get();
            $teams2 = array($user_id);
            if($user_id=="40"){
                $teams2 = array(34,40);
            }
            $sales_target = SysCrmSalesTarget::select('revenue_target_monthly', 'type','user_id')->whereRaw("DATE_FORMAT(target_month_from, '%Y-%m') <= '" . date('Y-m') . "'")->wherein('user_id', $teams2)->orderby('target_month_from','desc')->get();

            $order_in_process = SysCrmDealTrack::select('sys_crm_deal_track.*', 'sys_crm_deals.*')
                ->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')
                ->wherein('sys_crm_deals.owner', $teams)
                ->where('sys_crm_deal_track.receivables', '!=', 1)
                ->where('sys_crm_deals.stage', '!=', 6)
                ->where('sys_crm_deal_track.company_id', $company_id)
                ->orderby('sys_crm_deal_track.id', 'desc')->get();


            $dealsbyclosedate =  SysHelper::get_deals_close_date($teams);
            //$sales =  SysHelper::get_total_sales_revenue('m',$company_id);

            //new dashboard
            $ctrl_date=date('Y-m-01');
            $ctrl_date2=date("Y-m-t", strtotime($ctrl_date));
            //$sales_revenue =  SysCrmReportController::get_total_sales_revenue($teams, 'm', $company_id);
            $sales_revenue =  SysHelper::get_total_revenue_all_by_user($teams,$ctrl_date,$ctrl_date2,[$company_id],[]); //[219519.81,53631.286,219519.81]

            //$on_process = SysCrmReportController::get_total_on_process($teams, 'm', $company_id);
            $on_process = SysHelper::get_total_on_process_all_by_user($teams,$ctrl_date,$ctrl_date2,[$company_id],[]);

            //$target_gp = SysCrmReportController::get_total_target_gp($teams, 'm', $company_id); //["1,831,068.89","192,250.16"]
            //return $target_gp;
            $target = SysHelper::calculateSalesTarget($teams, $ctrl_date, $ctrl_date2,'this_month',[$company_id]); //{"rev_amount":"500000.00","gp_amount":"39000.00"}
            

            if($target['rev_amount']==0){
                $target_gp = 0;
                $target_amt = 0;
            } else{
                    $target_gp = ($sales_revenue[0] / $target['rev_amount']) * 100;
                    $target_amt = $target['rev_amount'];
            }

            //$forcast = SysCrmReportController::get_total_forcast($teams, 'm', $company_id);
            $forcast = SysHelper::get_total_forcast_all_by_user($teams,$ctrl_date,$ctrl_date2,[$company_id],[]);

            //new dashboard 

            //$targets = SysCrmSalesTarget::select('target', 'type')->whereRaw("DATE_FORMAT(target_month, '%Y-%m') = '" . date('Y-m') . "'")->where('user_id', $user_id)->first();
            $targets = SysCrmSalesTarget::select('revenue_target_monthly', 'type')->whereRaw("DATE_FORMAT(target_month_from, '%Y-%m') <= '" . date('Y-m') . "'")->wherein('user_id', $teams2)->orderby('target_month_from','desc')->first();

            $pending_payments = SysCrmDeals::select()->join('sys_crm_deal_track_approval_receivables', 'sys_crm_deals.id', 'sys_crm_deal_track_approval_receivables.deal_id')
                ->join('sys_crm_deal_track', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_receivables.created_at, '%Y-%m') = '" . date('Y-m') . "'")->where('sys_crm_deal_track.receivables', '!=', 1)->where('sys_crm_deal_track.delivery', 1)->wherein('owner', $teams)->where('sys_crm_deal_track.company_id', $company_id)->orderby('sys_crm_deal_track_approval_receivables.id', 'asc')->get();

            $url_user_id = $user_id;
            $url_com_id = $company_id;
            $url_from = date('Y-m-01');
            $url_to = Carbon::now()->endOfMonth()->toDateString();
            $url_array = array($url_user_id, $url_com_id, $url_from, $url_to);


            // customer database
            $threeMonthsAgo = Carbon::now()->subMonths(3)->format('Y-m-d');
            $oneMonthsAgo = Carbon::now()->subMonths(1)->format('Y-m-d');

            $total_customers = SysCustSuppl::select('sys_cust_suppl.id')
            ->join('sys_cust_suppl_assign','sys_cust_suppl_assign.cust_supp_id','sys_cust_suppl.id')
            ->where('catid',1)->where('user_id', $user_id)->pluck('id')->unique()->count();

            //$total_customers = DB::table('sys_cust_suppl_assign')->where('user_id', $user_id)->where('type',1)->pluck('cust_supp_id');
            
            /*$active_customers = SysCrmDeals::join('sys_crm_deal_track_approval_invoice as i', 'i.deal_id', 'sys_crm_deals.id')->where('stage', 4)->where('i.status', 1)
            //->where('sys_crm_deals.company_id', $company_id)
            ->where('owner', $user_id)->where('i.created_at', '>=', $threeMonthsAgo)->pluck('cust_id')->unique()->count();*/
            
 $dealIdsFromInvoice = SysCrmDeals::join('sys_crm_deal_track_approval_invoice as i', 'i.deal_id', '=', 'sys_crm_deals.id')
    ->where('sys_crm_deals.stage', 4)
    ->where('sys_crm_deals.owner', $user_id)
    ->where('i.status', 1)
    ->where('i.created_at', '>=', $oneMonthsAgo)
    ->pluck('sys_crm_deals.cust_id');

// From deals with estimated close date
$dealIdsFromEstimatedClose = SysCrmDeals::where('stage', 4)
    ->where('owner', $user_id)
    ->where('estimated_close_date', '>=', $oneMonthsAgo)
    ->pluck('cust_id');

// From leads
$leadIds = SysCrmLeads::whereIn('status', [0, 1, 2])
    ->where('owner', $user_id)
    ->where('updated_at', '>=', $oneMonthsAgo)
    ->pluck('cust_id');

// Merge all and count unique customer IDs
$active_customers = $dealIdsFromInvoice
    ->merge($dealIdsFromEstimatedClose)
    ->merge($leadIds)
    ->unique()
    ->count();

            $potential_customers =  SysCrmDeals::join('sys_crm_deal_track as t', 't.deal_id', 'sys_crm_deals.id')
            //->where('sys_crm_deals.company_id', $company_id)
            ->where('owner', $user_id)->where(['accounts' => 1, 'sales' => 1, 'purchease' => 1])->where('invoice', '!=', 1)->where('stage', 4)->distinct()->count();
            
            $inactive_customers = $total_customers - $active_customers - $potential_customers;
            $open_customers = 0;
            // customer database
            
            // Lead
            if($_POST && $request->show_deals==1){
                if($request->lead_filter_by != ""){
                    if ($request->lead_filter_by == "today") {
                        $lead_start_date=date('Y-m-d');
                        $lead_end_date=date('Y-m-d');
                    }
                    elseif ($request->lead_filter_by == "yesterday") {
                        $lead_start_date=date('Y-m-d', strtotime('-1 day'));
                        $lead_end_date=date('Y-m-d', strtotime('-1 day'));
                    }
                    elseif ($request->lead_filter_by == "this_week") {
                        $lead_start_date = date('Y-m-d', strtotime('-1 week sunday 00:00:00'));
                        $lead_end_date = date('Y-m-d', strtotime('saturday 23:59:59'));
                    }
                    elseif ($request->lead_filter_by == "last_week") {
                        $lead_start_date = date('Y-m-d', strtotime('-2 week sunday 00:00:00'));
                        $lead_end_date = date('Y-m-d', strtotime('-1 week saturday 23:59:59'));
                    } else {
                        $lead_start_date=date('Y-m-d');
                        $lead_end_date=date('Y-m-d');
                    }

                } elseif($request->lead_from_date !="" && $request->lead_to_date !=""){
                    $lead_start_date = $request->lead_from_date;
                    $lead_end_date = $request->lead_to_date;
                    
                } elseif($request->lead_from_date !="" && $request->lead_to_date ==""){
                    $lead_start_date = $request->lead_from_date;
                    $lead_end_date = date('Y-m-d');
                    
                } elseif($request->lead_from_date =="" && $request->lead_to_date !=""){
                    $lead_start_date = '2000-01-01';
                    $lead_end_date = $request->lead_to_date;
                } else{
                    $lead_start_date = date('Y-m-d');
                    $lead_end_date = date('Y-m-d');
                }

                $oneMonthsAgo = Carbon::now()->subMonths(1)->format('Y-m-d');
                
                $new_deal = DB::table('sys_crm_deals')->where('owner', $user_id)->where('date', '>=', $oneMonthsAgo)->where('status', 1)->where('company_id', $company_id)
                ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') >= '".$lead_start_date."' and DATE_FORMAT(date, '%Y-%m-%d') <= '".$lead_end_date."'")
                ->select('code')->distinct()->count();
                
                $total_deal = DB::table('sys_crm_deals')->where('owner', $user_id)->where('company_id', $company_id)
                ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') >= '".$lead_start_date."' and DATE_FORMAT(date, '%Y-%m-%d') <= '".$lead_end_date."'")->select('id')->count();
                
                $converted_deal = SysCrmDeals::select('id')->where('company_id', $company_id)->where('owner', $user_id)->where('stage', 4)
                ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') >= '".$lead_start_date."' and DATE_FORMAT(date, '%Y-%m-%d') <= '".$lead_end_date."'")->get();

                $total_quote = DB::table('sys_crm_quote_items')->wherein('deal_id', $converted_deal->pluck('id'))->select('deal_id')->distinct()->count();
                
                $unqualified_deal = DB::table('sys_crm_deals')->where('owner', $user_id)->wherein('status', [3])->where('company_id', $company_id)
                ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') >= '".$lead_start_date."' and DATE_FORMAT(date, '%Y-%m-%d') <= '".$lead_end_date."'")->select('code')->distinct()->count();

                $qualified_deal = DB::table('sys_crm_deals')->where('owner', $user_id)->wherein('status', [1,2])->where('company_id', $company_id)
                ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') >= '".$lead_start_date."' and DATE_FORMAT(date, '%Y-%m-%d') <= '".$lead_end_date."'")->select('code')->distinct()->count();

                $total_win = $converted_deal->count();
                /*$total_win = SysCrmDeals::where('stage', 4)->wherein('id', $converted_deal->pluck('deal_id'))
                ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') >= '".$lead_start_date."' and DATE_FORMAT(date, '%Y-%m-%d') <= '".$lead_end_date."'")->count();*/

                $total_in_progress = SysCrmDealTrackApprovalPurchease::where('status', 1)->wherein('deal_id', $converted_deal->pluck('deal_id'))
                ->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') >= '".$lead_start_date."' and DATE_FORMAT(created_at, '%Y-%m-%d') <= '".$lead_end_date."'")->count();

                $total_invoice = SysCrmDealTrackApprovalInvoice::where('status', 1)->wherein('deal_id', $converted_deal->pluck('deal_id'))
                ->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') >= '".$lead_start_date."' and DATE_FORMAT(created_at, '%Y-%m-%d') <= '".$lead_end_date."'")->count();

                $total_deal_closed = SysCrmDealTrackApprovalReceivables::where('status', 1)->wherein('deal_id', $converted_deal->pluck('deal_id'))
                ->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') >= '".$lead_start_date."' and DATE_FORMAT(created_at, '%Y-%m-%d') <= '".$lead_end_date."'")->count();

            } else {
                $oneMonthsAgo = Carbon::now()->subMonths(1)->format('Y-m-d');
                

                $new_deal = DB::table('sys_crm_deals')->where('owner', $user_id)->where('date', '>=', $oneMonthsAgo)->where('status', 1)->where('company_id', $company_id)->select('code')->distinct()->count();

                $total_deal = DB::table('sys_crm_deals')->where('owner', $user_id)->where('company_id', $company_id)->select('id')->count();

                $converted_deal = SysCrmDeals::select('id')->where('company_id', $company_id)->where('owner', $user_id)->where('stage', 4)->get();
                
                $total_quote = DB::table('sys_crm_quote_items')->wherein('deal_id', $converted_deal->pluck('id'))->select('deal_id')->distinct()->count();

                $unqualified_deal = DB::table('sys_crm_deals')->where('owner', $user_id)->wherein('status', [3])->where('company_id', $company_id)->select('code')->distinct()->count();

                $qualified_deal = DB::table('sys_crm_deals')->where('owner', $user_id)->wherein('status', [1,2])->where('company_id', $company_id)->select('code')->distinct()->count();

                //$total_win = SysCrmDeals::where('stage', 4)->where('owner', $user_id)->count();
                $total_win = $converted_deal->count();
                $total_in_progress = SysCrmDealTrackApprovalPurchease::where('status', 1)->wherein('deal_id', $converted_deal->pluck('id'))->count();
                $total_invoice = SysCrmDealTrackApprovalInvoice::where('status', 1)->wherein('deal_id', $converted_deal->pluck('id'))->count();
                $total_deal_closed = SysCrmDealTrackApprovalReceivables::where('status', 1)->wherein('deal_id', $converted_deal->pluck('id'))->count();
            }
            // Lead

            //sales
            $s_amount=0; $s_gp=0; $s_no_deals=0; 
            $s_in_amount=0; $s_ex_amount=0;
            $s_in_gp=0; $s_ex_gp=0;

            $s_data_query = SysSalesInvoice::select(DB::raw('sys_sales_invoice.*, (SELECT max(debit_amount) FROM sys_chartofaccounts_transaction WHERE transaction_type="salesinvoice" and transaction_no=sys_sales_invoice.doc_number and account_id=sys_sales_invoice.customer) AS amount, (SELECT max(code) FROM sys_crm_deals WHERE id=sys_sales_invoice.deal_id) AS code, (SELECT max(deal_profit) FROM sys_crm_deals WHERE id=sys_sales_invoice.deal_id) AS deal_profit, (SELECT max(deal_value) FROM sys_crm_deals WHERE id=sys_sales_invoice.deal_id) AS deal_value, (SELECT max(deal_currency) FROM sys_crm_deals WHERE id=sys_sales_invoice.deal_id) AS deal_currency'),DB::raw('(SELECT SUM(vatamount) FROM sys_sales_invoice_items WHERE si_id = sys_sales_invoice.id) AS total_vatamount'),DB::raw('(SELECT SUM(taxableamount) FROM sys_sales_invoice_items WHERE si_id = sys_sales_invoice.id) AS total_taxableamount'),DB::raw('(SELECT SUM(value) FROM sys_sales_invoice_items WHERE si_id = sys_sales_invoice.id) AS value'),DB::raw('(SELECT SUM(discount) FROM sys_sales_invoice_items WHERE si_id = sys_sales_invoice.id) AS discount'),DB::raw('(SELECT internal FROM sys_chartofaccounts WHERE id = sys_sales_invoice.customer) AS customer_internal'))->where('company_id',$company_id)->where('sales_man',$user_id);
            
            $sales_start_date="";
            $sales_end_date="";

            if($_POST && $request->show_sales==1){
                if($request->sales_filter_by != ""){
                    if ($request->sales_filter_by == "today") {
                        $sales_start_date=date('Y-m-d');
                        $sales_end_date=date('Y-m-d');
                    }
                    elseif ($request->sales_filter_by == "yesterday") {
                        $sales_start_date=date('Y-m-d', strtotime('-1 day'));
                        $sales_end_date=date('Y-m-d', strtotime('-1 day'));
                    }
                    elseif ($request->sales_filter_by == "this_week") {
                        $sales_start_date = date('Y-m-d', strtotime('-1 week sunday 00:00:00'));
                        $sales_end_date = date('Y-m-d', strtotime('saturday 23:59:59'));
                    }
                    elseif ($request->sales_filter_by == "last_week") {
                        $sales_start_date = date('Y-m-d', strtotime('-2 week sunday 00:00:00'));
                        $sales_end_date = date('Y-m-d', strtotime('-1 week saturday 23:59:59'));
                    } else {
                        $sales_start_date=date('Y-m-d');
                        $sales_end_date=date('Y-m-d');
                    }

                } elseif($request->sales_from_date !="" && $request->sales_to_date !=""){
                    $sales_start_date = $request->sales_from_date;
                    $sales_end_date = $request->sales_to_date;
                    
                } elseif($request->sales_from_date !="" && $request->sales_to_date ==""){
                    $sales_start_date = $request->sales_from_date;
                    $sales_end_date = date('Y-m-d');
                    
                } elseif($request->sales_from_date =="" && $request->sales_to_date !=""){
                    $sales_start_date = '2000-01-01';
                    $sales_end_date = $request->sales_to_date;
                } else{
                    $sales_start_date = date('Y-m-d');
                    $sales_end_date = date('Y-m-d');
                }
                $s_data_query->whereRaw("DATE_FORMAT(sys_sales_invoice.doc_date, '%Y-%m-%d') >= '".$sales_start_date."' and DATE_FORMAT(sys_sales_invoice.doc_date, '%Y-%m-%d') <= '".$sales_end_date."'");
            }
            $s_data = $s_data_query->get();

            $customer_ids = $s_data->pluck('customer')->toArray();
            $customer_count = array_count_values($customer_ids);
            $new_customers = collect($customer_count)->filter(function ($count) { return $count === 1; })->keys()->toArray();
            $old_customers = collect($customer_count)->filter(function ($count) { return $count > 1; })->flatMap(function ($count, $customer) { return array_fill(0, $count, $customer);})->toArray();
            $int_customers = SysCustSuppl::where('internal',1)->wherein('id',$customer_ids)->count();

            if(count($s_data)>0){
                $nc=[];
                foreach ($s_data as $value) {
                    $s_amount += SysHelper::get_aed_amount($value->currency,$value->total_taxableamount);
                    $s_gp += SysHelper::get_aed_amount($value->currency,$value->deal_profit);

                    if($value->customer_internal == 1){
                        $s_in_amount += SysHelper::get_aed_amount($value->currency,$value->total_taxableamount);
                        $s_in_gp += SysHelper::get_aed_amount($value->currency,$value->deal_profit);
                    } else {
                        $s_ex_amount += SysHelper::get_aed_amount($value->currency,$value->total_taxableamount);
                        $s_ex_gp += SysHelper::get_aed_amount($value->currency,$value->deal_profit);
                    }


                    if($value->code !=0 && $value->code != ""){
                        $s_no_deals += 1;
                    }
                    if(!in_array($value->customer, $nc))
                    {
                        $nc[]=$value->customer;
                    }
                }
            }


            if($s_amount != 0){
                $s_gp_p = $s_gp/$s_amount*100;
            } else { $s_gp_p=0; }

if($s_in_amount != 0){
    $s_in_gp_p = $s_in_gp/$s_in_amount*100;
} else { $s_in_gp_p=0; }

if($s_ex_amount != 0){
    $s_ex_gp_p = $s_ex_gp/$s_ex_amount*100;
} else { $s_ex_gp_p=0; }

            $s_nc=count($new_customers);
            $s_oc=count($old_customers);;
            $s_ic=$int_customers;
            
            $get_on_process = SysCrmReportController::get_total_on_process_dashboard_report($teams, $company_id, $sales_start_date, $sales_end_date);
            //$d_data = SysCrmDeals::
            $s_on_amount = $get_on_process[0];
            $s_on_in_amount=$get_on_process[6];
            $s_on_ex_amount=$get_on_process[7];
            $s_on_gp=$get_on_process[1];
            if($get_on_process[0] != 0 && $get_on_process[0] != ""){
                $s_on_gp_p = $get_on_process[1]/$get_on_process[0]*100;
            } else { $s_on_gp_p = 0; }
            $s_on_in_gp=$get_on_process[8];
            $s_on_ex_gp=$get_on_process[9];
            $s_on_no_deals=$get_on_process[2];
            $s_on_nc=$get_on_process[3];
            $s_on_oc=$get_on_process[4];
            $s_on_ic=$get_on_process[5];
            
            $get_fo_amount = SysCrmReportController::get_total_forcast_dashboard_report($teams, $company_id, $sales_start_date, $sales_end_date);
            $s_fo_amount = $get_fo_amount[0];
            $s_fo_in_amount=$get_fo_amount[6];
            $s_fo_ex_amount=$get_fo_amount[7];
            $s_fo_gp=$get_fo_amount[1];
            if($get_fo_amount[0] != 0 && $get_fo_amount[0] != ""){
                $s_fo_gp_p = $get_fo_amount[1]/$get_fo_amount[0]*100;
            } else { $s_fo_gp_p = 0; }            
            $s_fo_in_gp=$get_fo_amount[8];
            $s_fo_ex_gp=$get_fo_amount[9];
            $s_fo_no_deals=$get_fo_amount[2];
            $s_fo_nc=$get_fo_amount[3];
            $s_fo_oc=$get_fo_amount[4];
            $s_fo_ic=$get_fo_amount[5];
            //sales
            
            $os = [0,0, 0,0, 0,0];
            $os_in = [0,0, 0,0, 0,0];
            $os_ex = [0,0, 0,0, 0,0];
            $due_by_days=[0,0, 0,0, 0,0, 0,0];
            $due_by_days_in=[0,0, 0,0, 0,0, 0,0];
            $due_by_days_ex=[0,0, 0,0, 0,0, 0,0];
             $receivable = [$os,$os_in,$os_ex,$due_by_days,$due_by_days_in,$due_by_days_ex];
             
            if($_POST && $request->show_receivable==1){
                $receivable = SysCrmReportController::get_receivable_os_report_dashboard($user_id,$company_id);
            }
            
            $os_det = $receivable[0];
            $os_det_in = $receivable[1];
            $os_det_ex = $receivable[2];
            $due_by_det = $receivable[3];
            $due_by_det_in = $receivable[4];
            $due_by_det_ex = $receivable[5];


            $topBrands = SysItemStock::select('i.brand','b.title', DB::raw('sum(sys_item_stock.qty_out) as qty_out'), DB::raw('sum(sys_item_stock.qty_out * sys_item_stock.price_out) as price_out'), DB::raw('COUNT(*) as count'))
            ->join('sm_items as i', 'i.id', '=', 'sys_item_stock.partno')
            ->join('sys_brand as b', 'b.id', '=', 'i.brand')
            ->where('sys_item_stock.sales_person', $user_id)
            ->where('sys_item_stock.company_id', $company_id)
            ->groupBy('i.brand','b.title')
            ->orderByDesc('count')
            ->limit(5)
            ->get();


            // stock

            if(Auth::user()->role_id == 1 && $_POST && $request->show_brand_stock==1){
            $stocklist = DB::table('sys_item_stock as stock')
                ->select(DB::raw('max(item.part_number) as part_number'))
                ->selectRaw('2 as type')
                ->join('sm_items as item', 'item.id','stock.partno')
                ->whereRaw("DATE_FORMAT(stock.doc_date, '%Y-%m-%d') <= '" . date('Y-m-d') . "'")
                ->where('stock.company_id',$company_id)->where('stock.sales_person',$user_id)->where('stock.status',1)
                ->where('stock.doc_number', 'not like', 'SRN%')
                ->wherein('item.product_type',[1,2])
                ->groupby('item.part_number')
                ->get();  
                //return $user_id;
                $part_number = $stocklist->pluck('part_number');
                if(count($part_number)>0){
                    $stockledgerlist = SysHelper::get_inventory_report($part_number,date('Y-m-d'),[$company_id],"","","",$user_id,121,$user_id);
                    
                    $stock_data = DB::table('sys_inventory_report as stock')->select('brand.title','brand.id')->selectRaw('SUM(qty_out) as qty')->selectRaw('SUM(qty_out * price_out) as total')
                    ->join('sm_items as item', 'item.id','stock.partno')
                    ->join('sys_brand as brand','brand.id','item.brand')
                    ->leftjoin('sm_item_categories as cat','cat.id','item.category_name')
                    ->leftjoin('sm_item_subcategories as subcat','subcat.id','item.subcategory_name')
                    ->where(['cart_id' => session('logged_session_data.cart_id'), 'stock.user_id' => Auth::user()->id, 'stock.company_id' => session('logged_session_data.company_id')])->where('stock.sales_person_id',$user_id)->where('stock.days_m121','!=',0)->groupby('brand.title','brand.id');
                    $brand_list_data = $stock_data->get();

                    $stock_data_list = DB::table('sys_inventory_report as stock')->select('stock.*','item.description','brand.title as bname','brand.id as bid','cat.category_name','subcat.sub_category_name')
                    ->join('sm_items as item', 'item.id','stock.partno')
                    ->join('sys_brand as brand','brand.id','item.brand')
                    ->leftjoin('sm_item_categories as cat','cat.id','item.category_name')
                    ->leftjoin('sm_item_subcategories as subcat','subcat.id','item.subcategory_name')
                    ->where(['cart_id' => session('logged_session_data.cart_id'), 'stock.user_id' => Auth::user()->id, 'stock.company_id' => session('logged_session_data.company_id')])->where('stock.sales_person_id',$user_id)->where('stock.days_m121','!=',0)->get();
                    

                } else { $brand_list_data = []; $stock_data_list=[]; }
            } else { $brand_list_data = []; $stock_data_list=[]; }

            // stock end
        
        // Step 2: Get all related part numbers for the top 5 brands
       /* $brandWithParts = SysItemStock::select('i.brand', 'b.title', 'sys_item_stock.partno')
            ->join('sm_items as i', 'i.id', '=', 'sys_item_stock.partno')
            ->join('sys_brand as b', 'b.id', '=', 'i.brand')
            ->whereIn('i.brand', $topBrands) // Filter by top 5 brands
            ->where('sys_item_stock.sales_person', $user_id)
            ->get()
            ->groupBy('brand'); // Group by brand*/
            

            return view('backEnd.crm.DashboardSales', compact('sales_revenue', 'on_process', 'target_gp','target_amt', 'forcast', 'targets','order_in_process', 'sales_target', 'pending_payments', 'leads_type', 'deals_type', 'dealsbyclosedate', 'url_array', 'total_customers', 'active_customers', 'inactive_customers', 'potential_customers', 'open_customers', 'new_deal', 'total_deal','unqualified_deal','qualified_deal', 'total_quote', 'total_win', 'total_in_progress', 'total_invoice', 'total_deal_closed', 'd_full_name', 'd_staff_photo', 'd_designation','s_amount','s_gp','s_gp_p','s_no_deals','s_nc','s_oc','s_on_amount','s_fo_amount','s_on_gp','s_fo_gp','s_on_gp_p','s_fo_gp_p','s_on_no_deals','s_fo_no_deals','s_on_nc','s_on_oc','s_fo_nc','s_fo_oc','s_on_ic','s_fo_ic','s_ic','s_in_gp','s_ex_gp','s_in_amount','s_ex_amount','s_on_in_amount','s_on_ex_amount','s_on_in_gp','s_on_ex_gp','s_fo_in_amount','s_fo_ex_amount','s_fo_in_gp','s_fo_ex_gp','os_det','os_det_in','os_det_ex','due_by_det','due_by_det_in','due_by_det_ex','ctrl_date','ctrl_date2','company_id','user_id','topBrands','brand_list_data','stock_data_list'));

        } catch (\Throwable $th) {
            return $th;
        }
    }
    //sales Role 5 end

    //accounts receivable 3 start
    public static function accounts_receivable($user_id, $company_id, $d_full_name, $d_staff_photo, $d_designation)
    {
        try {
            $receivables_pending = SysCrmDealTrack::select('sys_crm_deal_track.id', 'sys_crm_deals.deal_name', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner', 'sys_crm_deal_track.deal_id', 'sys_crm_deal_track.created_at')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')->where([['sys_crm_deal_track.receivables', 0], ['sys_crm_deal_track.delivery', 1]])->where('stage', 4)
                ->where(function ($query) {
                    $query->where('sys_crm_deal_track.technical', 0)->orwhere('sys_crm_deal_track.tech', 1);
                })
                ->where('sys_crm_deals.stage', 4)->where('sys_crm_deal_track.company_id', $company_id)
                ->orderby('sys_crm_deal_track.id', 'asc')->get();

            $payment_reminder = SysCrmDealTrack::select('sys_crm_deal_track.id', 'sys_crm_deals.deal_name', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner', 'sys_crm_deal_track.deal_id', 'sys_crm_deal_track.created_at', 'reminder_date')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')
                ->join('sys_crm_deal_track_approval_receivables', 'sys_crm_deal_track_approval_receivables.deal_id', 'sys_crm_deal_track.deal_id')
                ->where([['sys_crm_deal_track.receivables', 3]])->whereRaw("DATE_FORMAT(reminder_date, '%Y-%m-%d') >= '" . date('Y-m-d') . "'")
                ->where('sys_crm_deals.stage', 4)->where('sys_crm_deal_track.company_id', $company_id)
                ->orderby('reminder_date', 'asc')->get();

            $payment_pending = SysCrmDealTrack::select('sys_crm_deal_track.id', 'sys_crm_deals.deal_name', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner', 'sys_crm_deal_track.deal_id', 'sys_crm_deal_track.created_at', 'reminder_date')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')
                ->join('sys_crm_deal_track_approval_receivables', 'sys_crm_deal_track_approval_receivables.deal_id', 'sys_crm_deal_track.deal_id')
                ->where([['sys_crm_deal_track.receivables', 3]])->whereRaw("DATE_FORMAT(reminder_date, '%Y-%m-%d') < '" . date('Y-m-d') . "'")
                ->where('sys_crm_deals.stage', 4)->where('sys_crm_deal_track.company_id', $company_id)
                ->orderby('reminder_date', 'asc')->get();

            return view('backEnd.crm.DashboardReceivables', compact('receivables_pending', 'payment_reminder', 'payment_pending', 'd_full_name', 'd_staff_photo', 'd_designation'));
        } catch (\Throwable $th) {
            return $th;
        }
    }
    //accounts receivable 3 end

    //accounts head 27 start
    public static function accounts_head($user_id, $company_id, $d_full_name, $d_staff_photo, $d_designation)
    {
        try {
            $pending_deals = SysCrmDealTrack::select('sys_crm_deal_track.*', 'sys_crm_deals.deal_name', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')
                ->where('sys_crm_deals.stage', 4)
                ->where('sys_crm_deal_track.accounts', 0)
                ->where('sys_crm_deal_track.company_id', $company_id)
                ->orderby('sys_crm_deal_track.id', 'asc')->get();

            $pending_payments = SysCrmDeals::select('sys_crm_deals.*')
                ->join('sys_crm_deal_track_approval_receivables', 'sys_crm_deals.id', 'sys_crm_deal_track_approval_receivables.deal_id')
                ->join('sys_crm_deal_track', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')
                ->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_receivables.created_at, '%Y-%m') = '" . date('Y-m') . "'")->where('sys_crm_deal_track.receivables', '!=', 1)
                ->where('sys_crm_deal_track.delivery', 1)
                ->where('sys_crm_deal_track.company_id', $company_id)
                ->orderby('sys_crm_deal_track_approval_receivables.id', 'asc')->get();

            $sales_target = SysCrmSalesTarget::whereRaw("DATE_FORMAT(target_month, '%Y-%m') = '" . date('Y-m') . "'")->where('company_id', $company_id)->orderby('id', 'asc')->get();

            $payment_reminder = SysCrmDealTrack::select('sys_crm_deal_track.id', 'sys_crm_deals.deal_name', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner', 'sys_crm_deal_track.deal_id', 'sys_crm_deal_track.created_at', 'reminder_date')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')
                ->join('sys_crm_deal_track_approval_receivables', 'sys_crm_deal_track_approval_receivables.deal_id', 'sys_crm_deal_track.deal_id')
                ->where([['sys_crm_deal_track.receivables', 3]])->whereRaw("DATE_FORMAT(reminder_date, '%Y-%m-%d') >= '" . date('Y-m-d') . "'")
                ->where('sys_crm_deal_track.company_id', $company_id)
                ->orderby('reminder_date', 'asc')->get();

            $payment_pending = SysCrmDealTrack::select('sys_crm_deal_track.id', 'sys_crm_deals.deal_name', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner', 'sys_crm_deal_track.deal_id', 'sys_crm_deal_track.created_at', 'reminder_date')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')
                ->join('sys_crm_deal_track_approval_receivables', 'sys_crm_deal_track_approval_receivables.deal_id', 'sys_crm_deal_track.deal_id')
                ->where([['sys_crm_deal_track.receivables', 3]])->whereRaw("DATE_FORMAT(reminder_date, '%Y-%m-%d') < '" . date('Y-m-d') . "'")
                ->where('sys_crm_deal_track.company_id', $company_id)
                ->orderby('reminder_date', 'asc')->get();

            return view('backEnd.crm.DashboardAccounts', compact('pending_deals', 'pending_payments', 'sales_target', 'payment_reminder', 'payment_pending', 'd_full_name', 'd_staff_photo', 'd_designation'));
        } catch (\Throwable $th) {
            return $th;
        }
    }
    //accounts head 27 end

    //billing 4 start
    public static function billing($user_id, $company_id, $d_full_name, $d_staff_photo, $d_designation)
    {
        try {
            $invoice_pending = SysCrmDealTrack::select('sys_crm_deal_track.id', 'sys_crm_deals.deal_name', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner', 'sys_crm_deal_track.deal_id', 'sys_crm_deal_track.created_at', 'sys_crm_deals.date')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')->where('sys_crm_deal_track.sales', 1)->wherein('sys_crm_deal_track.purchease', [1, 4])->where(
                function ($q) {
                    $q->where('sys_crm_deal_track.invoice', 0)
                        ->orWhere('sys_crm_deal_track.invoice', 3);
                }
            )->where('sys_crm_deals.stage', 4)->where('sys_crm_deal_track.company_id', $company_id)->orderby('sys_crm_deal_track.id', 'asc')->get();

            $partial_invoice = SysCrmDealTrack::select('sys_crm_deal_track.id', 'sys_crm_deals.deal_name', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner', 'sys_crm_deal_track.deal_id', 'sys_crm_deal_track.created_at', 'sys_crm_deals.date')
                ->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')
                ->join('sys_crm_deal_track_approval_invoice', 'sys_crm_deal_track_approval_invoice.deal_track_id', 'sys_crm_deal_track.id')
                ->where([['sys_crm_deal_track.invoice', 1], ['sys_crm_deal_track.sales', 1]])->wherein('sys_crm_deal_track.purchease', [1, 4])
                ->where('sys_crm_deal_track_approval_invoice.partial_invoice', 1)
                ->where('sys_crm_deal_track.company_id', $company_id)
                ->orderby('sys_crm_deal_track.id', 'asc')->get();

            $do_pending = SysCrmDealTrack::select('sys_crm_deal_track.id', 'sys_crm_deals.deal_name', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner', 'sys_crm_deal_track.deal_id', 'sys_crm_deal_track.created_at', 'sys_crm_deals.date')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '" . date('Y-m') . "'")->where('sys_crm_deal_track.delivery', 0)->where('sys_crm_deals.stage', 4)->where('sys_crm_deal_track.company_id', $company_id)->orderby('sys_crm_deal_track.id', 'asc')->get();

            return view('backEnd.crm.DashboardInvoice', compact('invoice_pending', 'do_pending', 'partial_invoice', 'd_full_name', 'd_staff_photo', 'd_designation'));
        } catch (\Throwable $th) {
            return $th;
        }
    }
    //billing 4 end

    //logistic dept 6 start
    public static function logistic_dept($user_id, $company_id, $d_full_name, $d_staff_photo, $d_designation)
    {
        try {
            $do_pending = SysCrmDealTrack::select('sys_crm_deal_track.id', 'sys_crm_deals.deal_name', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner', 'sys_crm_deal_track.deal_id', 'sys_crm_deal_track.created_at', 'sys_crm_deals.date')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')->where([['sys_crm_deal_track.delivery', 0], ['sys_crm_deal_track.invoice', 1]])->wherenotin('sys_crm_deal_track.purchease', [4])->where('sys_crm_deal_track.company_id', $company_id)->orderby('sys_crm_deal_track.id', 'desc')->get();;

            $do_onprocess = SysCrmDealTrack::select('sys_crm_deal_track.id', 'sys_crm_deals.deal_name', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner', 'sys_crm_deal_track.deal_id', 'sys_crm_deal_track.created_at', 'sys_crm_deal_track.delivery')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')
                //->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '".date('Y-m')."'")
                ->where('stage', 4)->where('sys_crm_deal_track.company_id', $company_id)
                ->where([['sys_crm_deal_track.delivery', '=', 3], ['sys_crm_deal_track.delivery', '=', 4], ['sys_crm_deal_track.invoice', 1]])->orderby('sys_crm_deal_track.id', 'desc')->get();

            $pending_for_delivery = SysCrmDealTrack::select('sys_crm_deal_track.id', 'sys_crm_deals.deal_name', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner', 'sys_crm_deal_track.deal_id', 'sys_crm_deal_track.created_at', 'sys_crm_deal_track.delivery')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')
                //->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '".date('Y-m')."'")
                ->where('stage', 4)->where('sys_crm_deal_track.company_id', $company_id)
                ->where('sys_crm_deal_track.delivery', 4)->orderby('sys_crm_deal_track.id', 'desc')->get();

            $out_for_delivery = SysCrmDealTrack::select('sys_crm_deal_track.id', 'sys_crm_deals.deal_name', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner', 'sys_crm_deal_track.deal_id', 'sys_crm_deal_track.created_at', 'sys_crm_deal_track.delivery')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')
                //->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '".date('Y-m')."'")
                ->where('stage', 4)->where('sys_crm_deal_track.company_id', $company_id)
                ->where('sys_crm_deal_track.delivery', 3)->orderby('sys_crm_deal_track.id', 'desc')->get();

            $ready_for_delivery = SysCrmDealTrack::select('sys_crm_deal_track.id', 'sys_crm_deals.deal_name', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner', 'sys_crm_deal_track.deal_id', 'sys_crm_deal_track.created_at', 'sys_crm_deal_track.delivery')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')
                //->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '".date('Y-m')."'")
                ->where('stage', 4)->where('sys_crm_deal_track.company_id', $company_id)
                ->where('sys_crm_deal_track.delivery', 5)->orderby('sys_crm_deal_track.id', 'desc')->get();

            return view('backEnd.crm.DashboardDelivery', compact('do_pending', 'do_onprocess', 'pending_for_delivery', 'out_for_delivery', 'ready_for_delivery', 'd_full_name', 'd_staff_photo', 'd_designation'));
        } catch (\Throwable $th) {
            return $th;
        }
    }
    //logistic dept 6 end

    //logistic dept head 29 start
    public static function logistic_dept_head($user_id, $company_id, $d_full_name, $d_staff_photo, $d_designation)
    {
        try {
            $do_pending = SysCrmDealTrack::select('sys_crm_deal_track.id', 'sys_crm_deals.deal_name', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner', 'sys_crm_deal_track.deal_id', 'sys_crm_deal_track.created_at', 'sys_crm_deals.date')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')->where([['sys_crm_deal_track.delivery', 0], ['sys_crm_deal_track.invoice', 1]])->wherenotin('sys_crm_deal_track.purchease', [4])->where('sys_crm_deal_track.company_id', $company_id)->orderby('sys_crm_deal_track.id', 'desc')->get();

            //$do_onprocess = SysCrmDealTrack::select('sys_crm_deal_track.id','sys_crm_deals.deal_name','sys_crm_deals.cust_id','sys_crm_deals.owner','sys_crm_deal_track.deal_id','sys_crm_deal_track.created_at','sys_crm_deal_track.delivery')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')
            //->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '".date('Y-m')."'")
            //->where('stage',4)->where('sys_crm_deal_track.company_id',$company_id)
            //->where([['sys_crm_deal_track.delivery', '=', 3],['sys_crm_deal_track.delivery', '=', 4],['sys_crm_deal_track.invoice', 1]])->orderby('sys_crm_deal_track.id','desc')->get();

            $pending_for_delivery = SysCrmDealTrack::select('sys_crm_deal_track.id', 'sys_crm_deals.deal_name', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner', 'sys_crm_deal_track.deal_id', 'sys_crm_deal_track.created_at', 'sys_crm_deal_track.delivery', 'sys_crm_deals.date')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')
                //->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '".date('Y-m')."'")
                ->where('stage', 4)->where('sys_crm_deal_track.company_id', $company_id)
                ->where('sys_crm_deal_track.delivery', 4)->orderby('sys_crm_deal_track.id', 'desc')->get();

            $out_for_delivery = SysCrmDealTrack::select('sys_crm_deal_track.id', 'sys_crm_deals.deal_name', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner', 'sys_crm_deal_track.deal_id', 'sys_crm_deal_track.created_at', 'sys_crm_deal_track.delivery', 'sys_crm_deals.date')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')
                //->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '".date('Y-m')."'")
                ->where('stage', 4)->where('sys_crm_deal_track.company_id', $company_id)
                ->where('sys_crm_deal_track.delivery', 3)->orderby('sys_crm_deal_track.id', 'desc')->get();

            $ready_for_delivery = SysCrmDealTrack::select('sys_crm_deal_track.id', 'sys_crm_deals.deal_name', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner', 'sys_crm_deal_track.deal_id', 'sys_crm_deal_track.created_at', 'sys_crm_deal_track.delivery', 'sys_crm_deals.date')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')
                //->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '".date('Y-m')."'")
                ->where('stage', 4)->where('sys_crm_deal_track.company_id', $company_id)
                ->where('sys_crm_deal_track.delivery', 5)->orderby('sys_crm_deal_track.id', 'desc')->get();;

            $partial_delivery = SysCrmDealTrack::select('sys_crm_deal_track.id', 'sys_crm_deal_track_approval_purchease.remarks', 'sys_crm_deals.deal_name', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner', 'sys_crm_deal_track.deal_id', 'sys_crm_deal_track.created_at', 'sys_crm_deals.date')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')
                ->join('sys_crm_deal_track_approval_purchease', 'sys_crm_deal_track_approval_purchease.deal_id', 'sys_crm_deal_track.deal_id')
                ->where('sys_crm_deal_track.purchease', 4)->where('sys_crm_deal_track.company_id', $company_id)->orderby('sys_crm_deal_track.id', 'desc')->get();

            $grn_pending = SysCrmDealTrack::select('sys_crm_deal_track_approval_purchease_grn.id', 'sys_crm_deal_track.id AS trackid', 'sys_crm_deals.deal_name', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner', 'sys_crm_deal_track.deal_id', 'sys_crm_deal_track.created_at', 'sys_crm_deal_track_approval_purchease.supplier_name', 'sys_crm_deal_track_approval_purchease.delivery_date', 'sys_crm_deal_track_approval_purchease.lpo_no')
                ->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')
                ->join('sys_crm_deal_track_approval_purchease', 'sys_crm_deal_track_approval_purchease.deal_track_id', 'sys_crm_deal_track.id')
                ->join('sys_crm_deal_track_approval_purchease_grn', 'sys_crm_deal_track_approval_purchease_grn.deal_id', 'sys_crm_deal_track.deal_id')
                ->wherein('sys_crm_deal_track_approval_purchease_grn.status', [0])->wherein('sys_crm_deal_track.purchease', [3])->where('sys_crm_deals.stage', 4)->where('sys_crm_deal_track.company_id', $company_id)
                ->orderby('sys_crm_deal_track_approval_purchease_grn.id', 'desc')->get();

            return view('backEnd.crm.DashboardDelivery', compact('do_pending', 'pending_for_delivery', 'out_for_delivery', 'ready_for_delivery', 'partial_delivery', 'grn_pending', 'd_full_name', 'd_staff_photo', 'd_designation'));
        } catch (\Throwable $th) {
            return $th;
        }
    }
    //logistic dept head 29 end

    //sales department head 8 start
    public static function sales_department_head($user_id, $company_id, $d_full_name, $d_staff_photo, $d_designation)
    {
        try {
            $teams = DB::table('users')->where('role_id', 5)->pluck('id');
            $approved_list = SysCrmDealTrack::select()->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '" . date('Y-m') . "'")->where('sys_crm_deal_track.sales', 1)
                ->where('sys_crm_deals.company_id', $company_id)->orderby('sys_crm_deal_track.id', 'desc')->get();

            $pending_approval = SysCrmDealTrack::select('sys_crm_deal_track.*', 'sys_crm_deals.owner', 'sys_crm_deals.cust_id')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')
                ->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '" . date('Y-m') . "'")
                ->where([['sys_crm_deal_track.accounts', 1], ['sys_crm_deal_track.sales', 0]])
                ->where('sys_crm_deal_track.company_id', $company_id)->orderby('sys_crm_deal_track.id', 'desc')->get();

            $order_in_process = SysCrmDealTrack::select()->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '" . date('Y-m') . "'")->where('sys_crm_deal_track.receivables', '!=', 1)->wherein('owner', $teams)
                ->where('sys_crm_deal_track.company_id', $company_id)->orderby('sys_crm_deal_track.id', 'desc')->get();

            $sales_target_indu = SysCrmSalesTarget::whereRaw("DATE_FORMAT(target_month, '%Y-%m') = '" . date('Y-m') . "'")->where('user_id', $user_id)
                ->where('company_id', $company_id)->orderby('id', 'asc')->get();

            $dealsbyclosedate   = SysHelper::get_deals_close_date($teams);
            $sales  =   SysHelper::get_total_sales_revenue('m', $company_id);


            $sales_revenue =  SysCrmReportController::get_total_sales_revenue($teams, 'm', $company_id);
            $on_process =  SysCrmReportController::get_total_on_process($teams, 'm', $company_id);
            $target_gp = SysCrmReportController::get_total_target_gp($teams, 'm', $company_id);
            $forcast = SysCrmReportController::get_total_forcast($teams, 'm', $company_id);
            $targets = SysCrmSalesTarget::select('target')->whereRaw("DATE_FORMAT(target_month, '%Y-%m') = '" . date('Y-m') . "'")->wherein('user_id', $teams)->sum('target');


            return view('backEnd.crm.DashboardSalesDep', compact('order_in_process', 'approved_list', 'pending_approval', 'sales_target_indu', 'dealsbyclosedate', 'sales', 'sales_revenue', 'on_process', 'target_gp', 'forcast', 'targets', 'd_full_name', 'd_staff_photo', 'd_designation'));
            //return view('backEnd.crm.DashboardSalesDep', compact('total_revenue_won','total_revenue_quote','order_in_process','approved_list','pending_approval','sales_target_indu','dealsbyclosedate','sales'));
        } catch (\Throwable $th) {
            return $th;
        }
    }
    //sales department head 8 end

    //procurement dept head 9 start
    public static function procurement_dept_head($user_id, $company_id, $d_full_name, $d_staff_photo, $d_designation)
    {
        try {
            $purchease_pending = SysCrmDealTrack::select('sys_crm_deal_track.id', 'sys_crm_deals.deal_name', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner', 'sys_crm_deal_track.deal_id', 'sys_crm_deal_track.created_at', 'sys_crm_deals.date')
                ->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')
                ->where('sys_crm_deal_track.sales', 1)->wherein('sys_crm_deal_track.purchease', [0, 4])->wherenotin('sys_crm_deal_track.delivery', [1])->where('sys_crm_deals.stage', 4)
                ->wherenotin('sys_crm_deal_track.purchease', [4])->where('sys_crm_deal_track.company_id', $company_id)->orderby('sys_crm_deal_track.id', 'desc')->get();

            // $invoice_pending = SysCrmDealTrack::select('sys_crm_deal_track.id','sys_crm_deals.deal_name','sys_crm_deals.cust_id','sys_crm_deals.owner','sys_crm_deal_track.deal_id','sys_crm_deal_track.created_at')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '".date('Y-m')."'")->where('sys_crm_deal_track.invoice', 0)->orderby('sys_crm_deal_track.id','desc')->get();

            $under_purchase = SysCrmDealTrack::select('sys_crm_deal_track.id', 'sys_crm_deals.deal_name', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner', 'sys_crm_deal_track.deal_id', 'sys_crm_deal_track.created_at', 'sys_crm_deal_track_approval_purchease.lpo_no', 'sys_crm_deal_track_approval_purchease.delivery_date', 'sys_crm_deal_track_approval_purchease.remarks', 'part_no', 'supplier_name', 'sys_crm_deals.date')
                ->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')
                ->join('sys_crm_deal_track_approval_purchease', 'sys_crm_deal_track_approval_purchease.deal_id', 'sys_crm_deal_track.deal_id')->where('sys_crm_deals.stage', 4)
                ->where('sys_crm_deal_track.purchease', 3)->where('sys_crm_deal_track.company_id', $company_id)->orderby('sys_crm_deal_track.id', 'desc')->get();

            $partial_delivery = SysCrmDealTrack::select('sys_crm_deal_track.id', 'sys_crm_deal_track_approval_purchease.remarks', 'sys_crm_deals.deal_name', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner', 'sys_crm_deal_track.deal_id', 'sys_crm_deal_track.created_at', 'sys_crm_deals.date')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')
                ->join('sys_crm_deal_track_approval_purchease', 'sys_crm_deal_track_approval_purchease.deal_id', 'sys_crm_deal_track.deal_id')
                ->where('sys_crm_deal_track.purchease', 4)->where('sys_crm_deal_track.company_id', $company_id)->orderby('sys_crm_deal_track.id', 'desc')->get();

            $purchase_completed = SysCrmDealTrack::select('sys_crm_deal_track.id', 'sys_crm_deals.deal_name', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner', 'sys_crm_deal_track.deal_id', 'sys_crm_deal_track.created_at', 'sys_crm_deals.date')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')
                ->join('sys_crm_deal_track_approval_purchease', 'sys_crm_deal_track_approval_purchease.deal_id', 'sys_crm_deal_track.deal_id')
                ->where('sys_crm_deal_track_approval_purchease.validation', 1)->where('sys_crm_deal_track.company_id', $company_id)->orderby('sys_crm_deal_track.id', 'desc')->get();

            return view('backEnd.crm.DashboardPurchease', compact('purchease_pending', 'under_purchase', 'partial_delivery', 'purchase_completed', 'd_full_name', 'd_staff_photo', 'd_designation'));
        } catch (\Throwable $th) {
            return $th;
        }
    }
    //procurement dept head 9 end

    //accounts payable procurement dept 10 start
    public static function accounts_payable_procurement_dept($user_id, $company_id, $d_full_name, $d_staff_photo, $d_designation)
    {
        try {
            $purchease_pending = SysCrmDealTrack::select('sys_crm_deal_track.id', 'sys_crm_deals.deal_name', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner', 'sys_crm_deal_track.deal_id', 'sys_crm_deal_track.created_at')
                ->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')
                ->where('sys_crm_deal_track.sales', 1)->wherein('sys_crm_deal_track.purchease', [0, 4])->wherenotin('sys_crm_deal_track.delivery', [1])->where('sys_crm_deals.stage', 4)
                ->wherenotin('sys_crm_deal_track.purchease', [4])->where('sys_crm_deal_track.company_id', $company_id)->orderby('sys_crm_deal_track.id', 'desc')->get();

            // $invoice_pending = SysCrmDealTrack::select('sys_crm_deal_track.id','sys_crm_deals.deal_name','sys_crm_deals.cust_id','sys_crm_deals.owner','sys_crm_deal_track.deal_id','sys_crm_deal_track.created_at')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '".date('Y-m')."'")->where('sys_crm_deal_track.invoice', 0)->orderby('sys_crm_deal_track.id','desc')->get();

            $under_purchase = SysCrmDealTrack::select('sys_crm_deal_track.id', 'sys_crm_deals.deal_name', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner', 'sys_crm_deal_track.deal_id', 'sys_crm_deal_track.created_at', 'sys_crm_deal_track_approval_purchease.lpo_no', 'sys_crm_deal_track_approval_purchease.delivery_date', 'sys_crm_deal_track_approval_purchease.remarks', 'part_no', 'supplier_name')
                ->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')
                ->join('sys_crm_deal_track_approval_purchease', 'sys_crm_deal_track_approval_purchease.deal_id', 'sys_crm_deal_track.deal_id')
                ->where('sys_crm_deal_track.purchease', 3)->where('sys_crm_deal_track.company_id', $company_id)->orderby('sys_crm_deal_track.id', 'desc')->get();

            $partial_delivery = SysCrmDealTrack::select('sys_crm_deal_track.id', 'sys_crm_deal_track_approval_purchease.remarks', 'sys_crm_deals.deal_name', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner', 'sys_crm_deal_track.deal_id', 'sys_crm_deal_track.created_at')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')
                ->join('sys_crm_deal_track_approval_purchease', 'sys_crm_deal_track_approval_purchease.deal_id', 'sys_crm_deal_track.deal_id')
                ->where('sys_crm_deal_track.purchease', 4)->where('sys_crm_deal_track.company_id', $company_id)->orderby('sys_crm_deal_track.id', 'desc')->get();

            $purchase_completed = SysCrmDealTrack::select('sys_crm_deal_track.id', 'sys_crm_deals.deal_name', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner', 'sys_crm_deal_track.deal_id', 'sys_crm_deal_track.created_at')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')
                ->join('sys_crm_deal_track_approval_purchease', 'sys_crm_deal_track_approval_purchease.deal_id', 'sys_crm_deal_track.deal_id')
                ->where('sys_crm_deal_track_approval_purchease.validation', 1)->where('sys_crm_deal_track.company_id', $company_id)->orderby('sys_crm_deal_track.id', 'desc')->get();

            return view('backEnd.crm.DashboardPurchease', compact('purchease_pending', 'under_purchase', 'partial_delivery', 'purchase_completed', 'd_full_name', 'd_staff_photo', 'd_designation'));
        } catch (\Throwable $th) {
            return $th;
        }
    }
    //accounts payable procurement dept 10 end

    //marketing dept 26 start
    public static function marketing_dept($user_id, $company_id, $d_full_name, $d_staff_photo, $d_designation)
    {
        try {

            $sales =  SysHelper::get_total_sales_revenue_all($_SESSION["page_date_id"], $company_id);
            $service =  SysHelper::get_total_service_revenue_all($_SESSION["page_date_id"], $company_id);
            $amc =  SysHelper::get_total_amc_revenue_all($_SESSION["page_date_id"], $company_id);
            $project =  SysHelper::get_total_project_revenue_all($_SESSION["page_date_id"], $company_id);
            $total_leads_new =null;
            $total_leads_qualified =null;
            $total_leads_unqualified = null;
            $total_deals_prospecting  = null;
            $total_deals_quote  = null;
            $total_deals_closure  = null;
            $total_deals_won  = null;
            $total_deals_lost  = null;
            $deals_type_project  = null;
            $deals_type_channel  = null;
            $deals_type_corporate  = null;
            $leads_type_project  = null;
            $leads_type_channel   = null;
$leads_type_corporate = null;
$order_in_process     = [];
$sales_target         = [];
$pending_payments     = [];
$payment_reminder     = [];
$payment_pending      = [];
$partial_invoice      = [];


$dealsbyclosedate     = [];
$d_full_name          = null;
$d_staff_photo        = null;
$d_designation        = null;

            return view('backEnd.crm.Dashboard', compact('total_leads_new', 'total_leads_qualified', 'total_leads_unqualified', 'total_deals_prospecting', 'total_deals_quote', 'total_deals_closure', 'total_deals_won', 'total_deals_lost', 'deals_type_project', 'deals_type_channel', 'deals_type_corporate', 'leads_type_project', 'leads_type_channel', 'leads_type_corporate', 'order_in_process', 'sales_target', 'pending_payments', 'payment_reminder', 'payment_pending', 'partial_invoice', 'service', 'amc', 'project', 'sales', 'dealsbyclosedate', 'd_full_name', 'd_staff_photo', 'd_designation'));
        } catch (\Throwable $th) {
            return $th;
        }
    }
    //marketing dept 26 end

    //accounts 28 start
    public static function accounts($user_id, $company_id, $d_full_name, $d_staff_photo, $d_designation)
    {
        try {
            $purchease_pending = SysCrmDealTrack::select('sys_crm_deal_track.id', 'sys_crm_deals.deal_name', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner', 'sys_crm_deal_track.deal_id', 'sys_crm_deal_track.created_at', 'sys_crm_deals.date')
                ->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')
                ->where('sys_crm_deal_track.sales', 1)->wherein('sys_crm_deal_track.purchease', [0, 4])->wherenotin('sys_crm_deal_track.delivery', [1])->where('sys_crm_deals.stage', 4)
                ->wherenotin('sys_crm_deal_track.purchease', [4])->where('sys_crm_deal_track.company_id', $company_id)->orderby('sys_crm_deal_track.id', 'desc')->get();

            // $invoice_pending = SysCrmDealTrack::select('sys_crm_deal_track.id','sys_crm_deals.deal_name','sys_crm_deals.cust_id','sys_crm_deals.owner','sys_crm_deal_track.deal_id','sys_crm_deal_track.created_at')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '".date('Y-m')."'")->where('sys_crm_deal_track.invoice', 0)->orderby('sys_crm_deal_track.id','desc')->get();

            $under_purchase = SysCrmDealTrack::select('sys_crm_deal_track.id', 'sys_crm_deals.deal_name', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner', 'sys_crm_deal_track.deal_id', 'sys_crm_deal_track.created_at', 'sys_crm_deal_track_approval_purchease.lpo_no', 'sys_crm_deal_track_approval_purchease.delivery_date', 'sys_crm_deal_track_approval_purchease.remarks', 'part_no', 'supplier_name', 'sys_crm_deals.date')
                ->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')
                ->join('sys_crm_deal_track_approval_purchease', 'sys_crm_deal_track_approval_purchease.deal_id', 'sys_crm_deal_track.deal_id')
                ->where('sys_crm_deal_track.purchease', 3)->where('sys_crm_deal_track.company_id', $company_id)->orderby('sys_crm_deal_track.id', 'desc')->get();

            $partial_delivery = SysCrmDealTrack::select('sys_crm_deal_track.id', 'sys_crm_deal_track_approval_purchease.remarks', 'sys_crm_deals.deal_name', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner', 'sys_crm_deal_track.deal_id', 'sys_crm_deal_track.created_at', 'sys_crm_deals.date')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')
                ->join('sys_crm_deal_track_approval_purchease', 'sys_crm_deal_track_approval_purchease.deal_id', 'sys_crm_deal_track.deal_id')
                ->where('sys_crm_deal_track.purchease', 4)->where('sys_crm_deal_track.company_id', $company_id)->orderby('sys_crm_deal_track.id', 'desc')->get();

            $purchase_completed = SysCrmDealTrack::select('sys_crm_deal_track.id', 'sys_crm_deals.deal_name', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner', 'sys_crm_deal_track.deal_id', 'sys_crm_deal_track.created_at', 'sys_crm_deals.date')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')
                ->join('sys_crm_deal_track_approval_purchease', 'sys_crm_deal_track_approval_purchease.deal_id', 'sys_crm_deal_track.deal_id')
                ->where('sys_crm_deal_track_approval_purchease.validation', 1)->where('sys_crm_deal_track.company_id', $company_id)->orderby('sys_crm_deal_track.id', 'desc')->get();

            //Purchase

            //Invoice

            $invoice_pending = SysCrmDealTrack::select('sys_crm_deal_track.id', 'sys_crm_deals.deal_name', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner', 'sys_crm_deal_track.deal_id', 'sys_crm_deal_track.created_at', 'sys_crm_deals.date')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')->where('sys_crm_deal_track.sales', 1)->wherein('sys_crm_deal_track.purchease', [1, 4])->where(
                function ($q) {
                    $q->where('sys_crm_deal_track.invoice', 0)
                        ->orWhere('sys_crm_deal_track.invoice', 3);
                }
            )->where('sys_crm_deals.stage', 4)->where('sys_crm_deal_track.company_id', $company_id)->orderby('sys_crm_deal_track.id', 'asc')->get();

            $partial_invoice = SysCrmDealTrack::select('sys_crm_deal_track.id', 'sys_crm_deals.deal_name', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner', 'sys_crm_deal_track.deal_id', 'sys_crm_deal_track.created_at', 'sys_crm_deals.date')
                ->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')
                ->join('sys_crm_deal_track_approval_invoice', 'sys_crm_deal_track_approval_invoice.deal_track_id', 'sys_crm_deal_track.id')
                ->where([['sys_crm_deal_track.invoice', 1], ['sys_crm_deal_track.sales', 1]])->wherein('sys_crm_deal_track.purchease', [1, 4])
                ->where('sys_crm_deal_track_approval_invoice.partial_invoice', 1)
                ->where('sys_crm_deal_track.company_id', $company_id)
                ->orderby('sys_crm_deal_track.id', 'asc')->get();


            $do_pending = SysCrmDealTrack::select('sys_crm_deal_track.id', 'sys_crm_deals.deal_name', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner', 'sys_crm_deal_track.deal_id', 'sys_crm_deal_track.created_at', 'sys_crm_deals.date')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '" . date('Y-m') . "'")->where('sys_crm_deal_track.delivery', 0)->where('sys_crm_deals.stage', 4)->where('sys_crm_deal_track.company_id', $company_id)->orderby('sys_crm_deal_track.id', 'asc')->get();

            //Invoice

            //Delivery

            // $do_pending = SysCrmDealTrack::select('sys_crm_deal_track.id','sys_crm_deals.deal_name','sys_crm_deals.cust_id','sys_crm_deals.owner','sys_crm_deal_track.deal_id','sys_crm_deal_track.created_at')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')->where([['sys_crm_deal_track.delivery', 0],['sys_crm_deal_track.invoice', 1]])->wherenotin('sys_crm_deal_track.purchease',[4])->where('sys_crm_deal_track.company_id',$company_id)->orderby('sys_crm_deal_track.id','desc')->get();

            $do_onprocess = SysCrmDealTrack::select('sys_crm_deal_track.id', 'sys_crm_deals.deal_name', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner', 'sys_crm_deal_track.deal_id', 'sys_crm_deal_track.created_at', 'sys_crm_deal_track.delivery', 'sys_crm_deals.date')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')
                //->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '".date('Y-m')."'")
                ->where('stage', 4)->where('sys_crm_deal_track.company_id', $company_id)
                ->where([['sys_crm_deal_track.delivery', '=', 3], ['sys_crm_deal_track.delivery', '=', 4], ['sys_crm_deal_track.invoice', 1]])->orderby('sys_crm_deal_track.id', 'desc')->get();

            $pending_for_delivery = SysCrmDealTrack::select('sys_crm_deal_track.id', 'sys_crm_deals.deal_name', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner', 'sys_crm_deal_track.deal_id', 'sys_crm_deal_track.created_at', 'sys_crm_deal_track.delivery', 'sys_crm_deals.date')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')
                //->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '".date('Y-m')."'")
                ->where('stage', 4)->where('sys_crm_deal_track.company_id', $company_id)
                ->where('sys_crm_deal_track.delivery', 4)->orderby('sys_crm_deal_track.id', 'desc')->get();

            $out_for_delivery = SysCrmDealTrack::select('sys_crm_deal_track.id', 'sys_crm_deals.deal_name', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner', 'sys_crm_deal_track.deal_id', 'sys_crm_deal_track.created_at', 'sys_crm_deal_track.delivery', 'sys_crm_deals.date')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')
                //->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '".date('Y-m')."'")
                ->where('stage', 4)->where('sys_crm_deal_track.company_id', $company_id)
                ->where('sys_crm_deal_track.delivery', 3)->orderby('sys_crm_deal_track.id', 'desc')->get();

            $ready_for_delivery = SysCrmDealTrack::select('sys_crm_deal_track.id', 'sys_crm_deals.deal_name', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner', 'sys_crm_deal_track.deal_id', 'sys_crm_deal_track.created_at', 'sys_crm_deal_track.delivery', 'sys_crm_deals.date')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')
                //->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '".date('Y-m')."'")
                ->where('stage', 4)->where('sys_crm_deal_track.company_id', $company_id)
                ->where('sys_crm_deal_track.delivery', 5)->orderby('sys_crm_deal_track.id', 'desc')->get();

            //Delivery

            //Receivables

            $receivables_pending = SysCrmDealTrack::select('sys_crm_deal_track.id', 'sys_crm_deals.deal_name', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner', 'sys_crm_deal_track.deal_id', 'sys_crm_deal_track.created_at', 'sys_crm_deals.date')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')->where([['sys_crm_deal_track.receivables', 0], ['sys_crm_deal_track.delivery', 1]])->where('stage', 4)
                ->where(function ($query) {
                    $query->where('sys_crm_deal_track.technical', 0)->orwhere('sys_crm_deal_track.tech', 1);
                })
                ->where('sys_crm_deals.stage', 4)
                ->where('sys_crm_deals.company_id', $company_id)
                ->orderby('sys_crm_deal_track.id', 'asc')->get();

            $payment_reminder = SysCrmDealTrack::select('sys_crm_deal_track.id', 'sys_crm_deals.deal_name', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner', 'sys_crm_deal_track.deal_id', 'sys_crm_deal_track.created_at', 'reminder_date', 'sys_crm_deals.date')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')
                ->join('sys_crm_deal_track_approval_receivables', 'sys_crm_deal_track_approval_receivables.deal_id', 'sys_crm_deal_track.deal_id')
                ->where([['sys_crm_deal_track.receivables', 3]])->whereRaw("DATE_FORMAT(reminder_date, '%Y-%m-%d') >= '" . date('Y-m-d') . "'")
                ->where('sys_crm_deals.stage', 4)
                ->where('sys_crm_deals.company_id', $company_id)
                ->orderby('reminder_date', 'asc')->get();

            $payment_pending = SysCrmDealTrack::select('sys_crm_deal_track.id', 'sys_crm_deals.deal_name', 'sys_crm_deals.cust_id', 'sys_crm_deals.owner', 'sys_crm_deal_track.deal_id', 'sys_crm_deal_track.created_at', 'reminder_date', 'sys_crm_deals.date')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')
                ->join('sys_crm_deal_track_approval_receivables', 'sys_crm_deal_track_approval_receivables.deal_id', 'sys_crm_deal_track.deal_id')
                ->where([['sys_crm_deal_track.receivables', 3]])->whereRaw("DATE_FORMAT(reminder_date, '%Y-%m-%d') < '" . date('Y-m-d') . "'")
                ->where('sys_crm_deals.stage', 4)
                ->where('sys_crm_deals.company_id', $company_id)
                ->orderby('reminder_date', 'asc')->get();

            //Receivables            
            return view('backEnd.crm.DashboardPurchaseInvoiceDeliveryReceivable', compact('purchease_pending', 'under_purchase', 'partial_delivery', 'purchase_completed', 'invoice_pending', 'do_pending', 'partial_invoice', 'do_onprocess', 'pending_for_delivery', 'out_for_delivery', 'ready_for_delivery', 'receivables_pending', 'payment_reminder', 'payment_pending', 'd_full_name', 'd_staff_photo', 'd_designation'));
        } catch (\Throwable $th) {
            return $th;
        }
    }
    //accounts 28 end

    //support engineer 31 start
    public static function support_engineer($user_id, $company_id, $d_full_name, $d_staff_photo, $d_designation)
    {
        try {
            SysHelper::amc_set_completed();

            // $ret = SysCrmAmcTableServiceComments::select('sys_crm_amc_table_service_comments.*','st.full_name','w.work')
            // ->join('sys_crm_amc_table_service_scope_of_work as w' ,'w.id','sys_crm_amc_table_service_comments.work_id')
            // ->leftjoin('sm_staffs as st','st.user_id','sys_crm_amc_table_service_comments.engineer_id')
            // ->where('sys_crm_amc_table_service_comments.amc_id', 1036)->get();
            // return $ret;
            $user_id = $user_id;

            $amc_request = SysCrmAmcTable::select('sys_crm_amc_table.id', 'sys_crm_amc_table.doc_number', 'sys_crm_amc_table.cust_name', 'sys_crm_amc_table.contact_person', 'sys_crm_amc_table.mobile_no', 'sys_crm_amc_table.date', 'ser.service_engineer', 'ser.location_of_work', 'ser.scope_of_work', 'ser.service_date', 'ser.service_time', 'ser.source', 'ser.attachment', 'sys_crm_amc_table.status', 'is_delete', 'ser.id as service_id', 'sys_crm_amc_table.is_auto')
                ->leftjoin('sys_crm_amc_table_service_request as ser', 'ser.amc_id', 'sys_crm_amc_table.id')
                //->where('sys_crm_amc_table.company_id',$company_id)
                ->wherein('sys_crm_amc_table.status', [2])
                ->where('ser.status', 1)
                //->where('sys_crm_amc_table.is_auto',0)
                ->whereRaw("find_in_set($user_id,ser.service_engineer)")
                ->orderby('date', 'desc')->get();

            $amc_request_work = DB::table('sys_crm_amc_table_service_request_scope_of_work')->wherein('service_id', $amc_request->pluck('service_id'))->get();
            $amc_work = DB::table('sys_crm_amc_table_service_scope_of_work')->wherein('amc_id', $amc_request->pluck('id'))->get();

            $amc_request_completed = SysCrmAmcTable::select('sys_crm_amc_table.id', 'sys_crm_amc_table.doc_number', 'sys_crm_amc_table.cust_name', 'sys_crm_amc_table.contact_person', 'sys_crm_amc_table.mobile_no', 'sys_crm_amc_table.date', 'ser.service_engineer', 'ser.location_of_work', 'ser.scope_of_work', 'ser.service_date', 'ser.service_time', 'ser.source', 'ser.attachment', 'sys_crm_amc_table.status', 'is_delete')
                ->join('sys_crm_amc_table_service_request as ser', 'ser.amc_id', 'sys_crm_amc_table.id')
                //->where('sys_crm_amc_table.company_id',$company_id)
                ->where('ser.status', 2)
                //->wherein('sys_crm_amc_table.status',[5])->where('sys_crm_amc_table.is_auto',0)
                //->where('cmt.created_by',$user_id)
                ->whereRaw("find_in_set($user_id,service_engineer)")
                ->orderby('date', 'desc')->get();

            $professional_services = SysCrmPSServiceTable::whereRaw("find_in_set($user_id,engineer)")
                //->where('company_id',$company_id)
                ->where('status', 1)->get();

            if (count($professional_services) > 0) {
                $professional_services_work = db::table('sys_crm_ps_service_table_scope_of_work')->wherein('service_id', $professional_services->pluck('id'))->get();
            } else {
                $professional_services_work = [];
            }

            //return $professional_services;
            $professional_services_completed = SysCrmPSServiceTable::select('sys_crm_ps_service_table.*')
                ->join('sys_crm_ps_table_service_comments as cmt', 'cmt.ps_id', 'sys_crm_ps_service_table.id')
                ->where('cmt.created_by', $user_id)
                //->whereRaw("find_in_set($user_id,engineer)")
                //where('company_id',$company_id)                
                ->where('cmt.status', 2)->get();

            $pre_sales_support_new = SysCrmSupport::select('sys_crm_support.*', 'c.name')
                ->leftjoin('sys_crm_deals as d', 'd.id', 'sys_crm_support.deal_id')
                ->leftjoin('sys_cust_suppl as c', 'c.id', 'd.cust_id')
                //->where('sys_crm_support.company_id',$company_id)
                ->whereRaw("find_in_set('" . $user_id . "',support_person_id)")
                ->wherein('sys_crm_support.status', [2])->get();
            //return $pre_sales_support_new;

            if (count($pre_sales_support_new) > 0) {
                $pre_sales_support_work = db::table('sys_crm_support_work')->wherein('support_id', $pre_sales_support_new->pluck('id'))->get();
            } else {
                $pre_sales_support_work = [];
            }

            $pre_sales_support_completed = SysCrmSupport::select('sys_crm_support.*', 'c.name')
                ->join('sys_crm_support_comments as cmt', 'cmt.support_id', 'sys_crm_support.id')
                ->leftjoin('sys_crm_deals as d', 'd.id', 'sys_crm_support.deal_id')
                ->leftjoin('sys_cust_suppl as c', 'c.id', 'd.cust_id')
                //->where('sys_crm_support.company_id',$company_id)
                //->whereRaw("find_in_set('".$user_id."',support_person_id)")
                ->where('cmt.created_by', $user_id)
                //->wherein('sys_crm_support.status',[3])
                ->where('cmt.status', 2)->get();

            return view('backEnd.crm.DashboardSupportEngineer', compact('amc_request', 'amc_request_completed', 'professional_services', 'professional_services_completed', 'pre_sales_support_new', 'pre_sales_support_completed', 'professional_services_work', 'pre_sales_support_work', 'amc_request_work', 'amc_work', 'd_full_name', 'd_staff_photo', 'd_designation'));
        } catch (\Throwable $th) {
            return $th;
        }
    }
    //support engineer 31 end

    //support co-ordinator 32 start
    public static function support_co_ordinator($user_id, $company_id, $d_full_name, $d_staff_photo, $d_designation)
    {
        try {
            SysHelper::amc_set_completed();
            $user_id = $user_id;

            //1 from deal, 2 generated and created directly in request, 3 customer submited, 4 - , 5 compleated, 6 reassign

            $amc_new = SysCrmAmcTable::select('sys_crm_amc_table.id', 'sys_crm_amc_table.doc_number', 'sys_crm_amc_table.cust_name', 'sys_crm_amc_table.contact_person', 'sys_crm_amc_table.mobile_no', 'sys_crm_amc_table.date', 'ser.service_engineer', 'ser.location_of_work', 'ser.scope_of_work', 'ser.service_date', 'ser.service_time', 'ser.source', 'ser.attachment', 'sys_crm_amc_table.status', 'is_delete')
                ->leftjoin('sys_crm_amc_table_service_request as ser', 'ser.amc_id', 'sys_crm_amc_table.id')
                ->where('sys_crm_amc_table.company_id', $company_id)->wherein('sys_crm_amc_table.status', [1, 3])
                //->where('service_engineer',$user_id)
                ->orderby('date', 'desc')->get();

            $amc_pending = SysCrmAmcTable::select('sys_crm_amc_table.id', 'sys_crm_amc_table.doc_number', 'sys_crm_amc_table.cust_name', 'sys_crm_amc_table.contact_person', 'sys_crm_amc_table.mobile_no', 'sys_crm_amc_table.date', 'ser.service_engineer', 'ser.location_of_work', 'ser.scope_of_work', 'ser.service_date', 'ser.service_time', 'ser.source', 'ser.attachment', 'sys_crm_amc_table.status', 'is_delete')
                ->join('sys_crm_amc_table_service_request as ser', 'ser.amc_id', 'sys_crm_amc_table.id')
                ->where('sys_crm_amc_table.company_id', $company_id)->wherein('sys_crm_amc_table.status', [2])->where('sys_crm_amc_table.is_auto', 0)
                //->where('service_engineer',$user_id)
                ->orderby('date', 'desc')->get();

            $amc_completed = SysCrmAmcTable::select('sys_crm_amc_table.id', 'sys_crm_amc_table.doc_number', 'sys_crm_amc_table.cust_name', 'sys_crm_amc_table.contact_person', 'sys_crm_amc_table.mobile_no', 'sys_crm_amc_table.date', 'ser.service_engineer', 'ser.location_of_work', 'ser.scope_of_work', 'ser.service_date', 'ser.service_time', 'ser.source', 'ser.attachment', 'sys_crm_amc_table.status', 'is_delete')
                ->join('sys_crm_amc_table_service_request as ser', 'ser.amc_id', 'sys_crm_amc_table.id')
                ->where('sys_crm_amc_table.company_id', $company_id)->wherein('sys_crm_amc_table.status', [5])->where('sys_crm_amc_table.is_auto', 0)
                //->where('service_engineer',$user_id)
                ->orderby('date', 'desc')->get();

            $project_new = SysCrmPSServiceTable::where('company_id', $company_id)->where('status', 0)->get();
            //return $professional_services;
            $project_pending = SysCrmPSServiceTable::where('company_id', $company_id)->where('status', 1)->get();

            $project_completed = SysCrmPSServiceTable::where('company_id', $company_id)->where('status', 2)->get();

            $pending_approval = SysCrmDealTrack::select('sys_crm_deal_track.*', 'sys_crm_deals.owner', 'sys_crm_deals.cust_id', 'sys_crm_deals.date')->join('sys_crm_deals', 'sys_crm_deals.id', 'sys_crm_deal_track.deal_id')
                ->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '" . date('Y-m') . "'")
                ->where([['sys_crm_deal_track.accounts', 1], ['sys_crm_deal_track.sales', 1], ['sys_crm_deal_track.purchease', 1], ['sys_crm_deal_track.invoice', 1], ['sys_crm_deal_track.delivery', 1], ['sys_crm_deal_track.tech', 0], ['sys_crm_deal_track.technical', 1]])
                ->where('sys_crm_deal_track.company_id', $company_id)->orderby('sys_crm_deal_track.id', 'desc')->get();

            $pre_sales_support_new = SysCrmSupport::select('sys_crm_support.*', 'c.name')
                ->leftjoin('sys_crm_deals as d', 'd.id', 'sys_crm_support.deal_id')
                ->leftjoin('sys_cust_suppl as c', 'c.id', 'd.cust_id')
                ->where('sys_crm_support.company_id', $company_id)
                ->wherein('sys_crm_support.status', [1])->get();

            $pre_sales_support_pending = SysCrmSupport::select('sys_crm_support.*', 'c.name')
                ->leftjoin('sys_crm_deals as d', 'd.id', 'sys_crm_support.deal_id')
                ->leftjoin('sys_cust_suppl as c', 'c.id', 'd.cust_id')
                ->where('sys_crm_support.company_id', $company_id)
                ->wherein('sys_crm_support.status', [2])->get();

            $pre_sales_support_completed = SysCrmSupport::select('sys_crm_support.*', 'c.name')
                ->leftjoin('sys_crm_deals as d', 'd.id', 'sys_crm_support.deal_id')
                ->leftjoin('sys_cust_suppl as c', 'c.id', 'd.cust_id')
                ->where('sys_crm_support.company_id', $company_id)
                ->wherein('sys_crm_support.status', [3])->get();

            $pre_sales_support = SysCrmSupport::select('sys_crm_support.*', 'c.name')
                ->leftjoin('sys_crm_deals as d', 'd.id', 'sys_crm_support.deal_id')
                ->leftjoin('sys_cust_suppl as c', 'c.id', 'd.cust_id')
                ->where('sys_crm_support.company_id', $company_id)
                ->wherein('sys_crm_support.status', [1, 2, 3])->get();

            return view('backEnd.crm.DashboardSupportCoOrdinator', compact('amc_new', 'amc_pending', 'amc_completed', 'project_new', 'project_pending', 'project_completed', 'pending_approval', 'pre_sales_support', 'pre_sales_support_new', 'pre_sales_support_pending', 'pre_sales_support_completed', 'd_full_name', 'd_staff_photo', 'd_designation'));
        } catch (\Throwable $th) {
            return $th;
        }
    }
    //support co-ordinator 32 end
}
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

class SysCrmDashboardController extends Controller
{
    public function __construct(){
        $this->middleware('PM');
    }
    
    public function crmtest()
    {    
        return view('backEnd.crm.test');
    }


    public function check_auth(){
        return view('auth.login_auth');
    }
    
    public function check_auth_update(Request $request){
        
        $chk = DB::table('sm_staffs')->where('user_id',Auth::user()->id)->where('auth_code',$request->code)->get();
        if(count($chk)>0){
            DB::table('sm_staffs')->where('user_id',Auth::user()->id)->where('auth_status',0)->update(
                [
                    'auth_date' => Carbon::now('+04:00'),
                    'auth_status' => 1,
                ]
            );
            session()->put('logged_session_data.auth_status',1);
            Toastr::success('Authentication Successful', 'Success');
            return redirect('crm-dashboard');
        } else {
            Toastr::error('Invalid Code', 'Failed');
            return redirect('crm-auth');
        }        
    }
    


    public function dashboard(Request $request)
    {
        try{
           
                // DB::enableQueryLog(); // Start logging queries
            $queryra = "UPDATE sys_crm_deals SET company_id = (SELECT company_id FROM sm_staffs WHERE user_id=sys_crm_deals.owner ) WHERE company_id IS NULL";
            DB::select($queryra);

            $total_leads_new=[];
            $total_leads_qualified =[];
            $total_leads_unqualified =[];
            $total_leads_pending   =[];
            $total_leads_closed   =[];
            $total_deals_prospecting =[];
            $total_deals_quote =[];
            $total_deals_closure  =[];
            $total_deals_won  =[];
            $total_deals_lost   =[];
            $deals_type_project   =[];
            $deals_type_channel   =[];
            $deals_type_corporate   =[];
            $leads_type_project    =[];
            $leads_type_channel    =[];
            $leads_type_corporate    =[];
            $order_in_process     =[];
            $sales_target     =[];
            $pending_payments     =[];
            $payment_reminder     =[];
            $payment_pending     =[];
            $partial_invoice      =[];
            $service      =[];
            $amc      =[];
            $project      =[];
            $sales      =[];
            $dealsbyclosedate      =[];


            if(!isset($_SESSION["page_date_id"])){
                $_SESSION["page_date_id"] = 'm';
            }
        

        //return session('logged_session_data.company_id');
// 1 SUPER ADMIN START
            if(Auth::user()->role_id == 1){
                

                //return SysHelper::get_total_revenue_all_by_user([63],date('Y-m-01'),date('Y-m-t'),1,[]);

            $lead_ret = SysHelper::get_lead_filter($_SESSION["page_date_id"],session('logged_session_data.company_id'));
            $deal_ret = SysHelper::get_deal_filter($_SESSION["page_date_id"],session('logged_session_data.company_id'));

            $t_from_date = date('Y-01-01');
            $t_to_date = date('Y-m-d');
            $t_filter_by = 'this_year';

                if ($request->from_date != "" && $request->filter_by == "") {
                    $t_from_date=Carbon::createFromFormat('d/m/Y', $request->from_date)->format('Y-m-d');
                }
                if ($request->to_date != "" && $request->filter_by == "") {
                    $t_to_date=Carbon::createFromFormat('d/m/Y', $request->to_date)->format('Y-m-d');
                }

                if ($request->filter_by == "this_month") {
                    $t_from_date=date('Y-m-01');
                    $t_to_date=date("Y-m-t", strtotime($t_from_date));
                    $t_filter_by='this_month';               
                }
                if ($request->filter_by == "today") {
                    $t_from_date=date('Y-m-d');
                    $t_to_date=date('Y-m-d');
                    $t_filter_by='today';
                }
                if ($request->filter_by == "this_week") {
                    $t_from_date = date('Y-m-d', strtotime('-1 week sunday 00:00:00'));
                    $t_to_date = date('Y-m-d', strtotime('saturday 23:59:59'));
                    $t_filter_by='this_week';
                }
                if ($request->filter_by == "last_week") {
                    $t_from_date = date('Y-m-d', strtotime('-2 week sunday 00:00:00'));
                    $t_to_date = date('Y-m-d', strtotime('-1 week saturday 23:59:59'));
                    $t_filter_by='last_week';
                }
                if ($request->filter_by == "last_month") {
                    $t_from_date = date('Y-m-d', strtotime('first day of previous month'));
                    $t_to_date = date('Y-m-d', strtotime('last day of previous month'));
                    $t_filter_by='last_month';
                }
                if ($request->filter_by == "this_quarter") {
                    $q_date = SysHelper::get_quarter(date('m'));
                    $t_from_date = $q_date[0];
                    $t_to_date = $q_date[1];
                    $t_filter_by='this_quarter';
                }
                if ($request->filter_by == "pre_quarter") {
                    $q_date = SysHelper::get_pre_quarter(date('m'));
                    $t_from_date = $q_date[0];
                    $t_to_date = $q_date[1];
                    $t_filter_by='pre_quarter';
                }
                if ($request->filter_by == "this_year") {
                    $t_from_date = date('Y-01-01');
                    $t_to_date = date('Y-12-31');
                    $t_filter_by='this_year';
                }
                if ($request->filter_by == "last_year") {
                    $t_from_date = date("Y-01-01",strtotime("-1 year"));
                    $t_to_date = date("Y-12-31",strtotime("-1 year"));
                    $t_filter_by='last_year';
                }

            
            
            $total_leads_new            = $lead_ret[0];
            $total_leads_qualified      = $lead_ret[1];
            $total_leads_unqualified    = $lead_ret[2];
            $total_leads_pending        = $lead_ret[3];
            $total_leads_closed         = $lead_ret[4];
            $leads_type_project         = $lead_ret[5];
            $leads_type_channel         = $lead_ret[6];
            $leads_type_corporate       = $lead_ret[7];

            // $order_in_process = SysCrmDealTrack::select()->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '".date('Y-m')."'")->where('sys_crm_deal_track.receivables','!=', 1)->orderby('sys_crm_deal_track.id','desc')->get();
            $order_in_process = []; //done by kunal
            
            $total_deals_prospecting    = $deal_ret[0];
            $total_deals_quote          = $deal_ret[1];
            $total_deals_closure        = $deal_ret[2];
            $total_deals_won            = $deal_ret[3];
            $total_deals_lost           = $deal_ret[4];
            $deals_type_project         = $deal_ret[5];
            $deals_type_channel         = $deal_ret[6];
            $deals_type_corporate       = $deal_ret[7];


            
            $pending_payments = SysCrmDeals::select()->join('sys_crm_deal_track_approval_receivables','sys_crm_deals.id','sys_crm_deal_track_approval_receivables.deal_id')
            ->join('sys_crm_deal_track','sys_crm_deals.id','sys_crm_deal_track.deal_id')->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_receivables.created_at, '%Y-%m') = '".date('Y-m')."'")->where('sys_crm_deal_track.receivables','!=', 1)->where('sys_crm_deal_track.delivery', 1)
            ->where('sys_crm_deals.company_id',session('logged_session_data.company_id'))
            ->orderby('sys_crm_deal_track_approval_receivables.id','asc')->get();
            
            
            if(session('logged_session_data.company_id') == 0){ $sales_target = SysCrmSalesTarget::whereRaw("DATE_FORMAT(target_month_from, '%Y-%m') <= '" . date('Y-m') . "'")
                ->orderby('target_month_from','desc')->get(); }
            else{$sales_target = SysCrmSalesTarget::whereRaw("DATE_FORMAT(target_month_from, '%Y-%m') <= '" . date('Y-m') . "'")->orderby('target_month_from','desc')->get();}




            $payment_reminder = SysCrmDealTrack::select('sys_crm_deal_track.id','sys_crm_deals.deal_name','sys_crm_deals.cust_id','sys_crm_deals.owner','sys_crm_deal_track.deal_id','sys_crm_deal_track.created_at','reminder_date')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')
            ->join('sys_crm_deal_track_approval_receivables','sys_crm_deal_track_approval_receivables.deal_id','sys_crm_deal_track.deal_id')
            ->where([['sys_crm_deal_track.receivables', 3]])->whereRaw("DATE_FORMAT(reminder_date, '%Y-%m-%d') >= '".date('Y-m-d')."'")
            ->where('sys_crm_deal_track.company_id',session('logged_session_data.company_id'))
            ->orderby('reminder_date','asc')->get();

            $payment_pending = SysCrmDealTrack::select('sys_crm_deal_track.id','sys_crm_deals.deal_name','sys_crm_deals.cust_id','sys_crm_deals.owner','sys_crm_deal_track.deal_id','sys_crm_deal_track.created_at','reminder_date')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')
            ->join('sys_crm_deal_track_approval_receivables','sys_crm_deal_track_approval_receivables.deal_id','sys_crm_deal_track.deal_id')
            ->where([['sys_crm_deal_track.receivables', 3]])->whereRaw("DATE_FORMAT(reminder_date, '%Y-%m-%d') < '".date('Y-m-d')."'")
            ->where('sys_crm_deal_track.company_id',session('logged_session_data.company_id'))
            ->orderby('reminder_date','asc')->get();
              
            $partial_invoice = SysCrmDealTrack::select('sys_crm_deal_track.id','sys_crm_deals.deal_name','sys_crm_deals.cust_id','sys_crm_deals.owner','sys_crm_deal_track.deal_id','sys_crm_deal_track.created_at')
            ->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')
            ->join('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_track_id','sys_crm_deal_track.id')
            ->where([['sys_crm_deal_track.invoice', 1],['sys_crm_deal_track.sales', 1]])->wherein('sys_crm_deal_track.purchease', [1,4])
            ->where('sys_crm_deal_track_approval_invoice.partial_invoice',1)
            ->where('sys_crm_deal_track.company_id',session('logged_session_data.company_id'))
            ->orderby('sys_crm_deal_track.id','asc')->get();


            $sales_revenue_report = SysHelper::get_total_revenue_all_by_company($t_from_date, $t_to_date, session('logged_session_data.company_id'), []);
            $sales_forecast_report = SysHelper::get_total_forcast_all_by_company($t_from_date, $t_to_date, session('logged_session_data.company_id'), []);
            $sales = [
                $sales_revenue_report[0],
                $sales_forecast_report[0],
                0
            ];
            $service = [0,0];
            $amc = [0,0];
            $project = [0,0];

            // $dealsbyclosedate =  SysHelper::get_deals_close_date();
            $dealsbyclosedate = []; //done by kunal
            $performance = [];
            //$company_list = SysCompany::select('id','company_name')->orderby('sort_id','asc')->get();
                        
           /* if(count($company_list)>0){
                foreach($company_list as $list){
                    $rev = [0,0];//SysCrmReportController::get_total_sales_revenue_by_company('m',$list->id);
                    $performance[] = [
                        'company_name' => $list->company_name,
                        'target_gp' => $rev[1],
                        'revenue' => $rev[0],
                        'on_process_deal' => 0,//SysCrmReportController::get_total_on_process_by_company('m',$list->id),
                        'forcast' => 0, //SysCrmReportController::get_total_forcast_by_company('m',$list->id),
                    ];
                }
            }*/
            //new dashboard
            $sales_revenue = 0; // SysCrmReportController::get_total_sales_revenue_by_company('m',session('logged_session_data.company_id'));
            $sales_on_process_report = SysHelper::get_total_on_process_all_by_company($t_from_date, $t_to_date, session('logged_session_data.company_id'), []);
            $on_process = $sales_on_process_report[0];
            $target_gp = 0; //SysCrmReportController::get_total_target_gp([0],'m',session('logged_session_data.company_id'));
            //return $target_gp;
            $forcast = 0; //SysCrmReportController::get_total_forcast_by_company('m',session('logged_session_data.company_id'));

    // Example: after running some queries, get the log
    // $queries = DB::getQueryLog();

    // dd($queries); // Dump the queries to see them


                return view('backEnd.crm.Dashboard', compact('total_leads_new', 'total_leads_qualified','total_leads_unqualified','total_leads_pending','total_leads_closed','total_deals_prospecting','total_deals_quote','total_deals_closure','total_deals_won','total_deals_lost','deals_type_project','deals_type_channel','deals_type_corporate','leads_type_project','leads_type_channel','leads_type_corporate','order_in_process','sales_target','pending_payments','payment_reminder','payment_pending','partial_invoice','service','amc','project','sales','dealsbyclosedate','performance','sales_revenue','on_process','target_gp','forcast','t_filter_by','t_from_date','t_to_date'));
            }
// 1 SUPER ADMIN END

// 2 ADMIN START
            if(Auth::user()->role_id == 2){
                
                $lead_ret = SysHelper::get_lead_filter($_SESSION["page_date_id"],session('logged_session_data.company_id'));
                $deal_ret = SysHelper::get_deal_filter($_SESSION["page_date_id"],session('logged_session_data.company_id'));
    
                $teams = SysHelper::get_staff_list()->pluck('user_id')->toArray();

                $com_id=session('logged_session_data.company_id');
                
                $sales_person = SmStaff::select('user_id','full_name')->whereRaw("FIND_IN_SET(?, company_access)", [$com_id])->get();
               
                $total_leads_new            = $lead_ret[0];
                $total_leads_qualified      = $lead_ret[1];
                $total_leads_unqualified    = $lead_ret[2];
                $total_leads_pending        = $lead_ret[3];
                $total_leads_closed         = $lead_ret[4];
                $leads_type_project         = $lead_ret[5];
                $leads_type_channel         = $lead_ret[6];
                $leads_type_corporate       = $lead_ret[7];

                $total_deals_prospecting    = $deal_ret[0];
                $total_deals_quote          = $deal_ret[1];
                $total_deals_closure        = $deal_ret[2];
                $total_deals_won            = $deal_ret[3];
                $total_deals_lost           = $deal_ret[4];
                $deals_type_project         = $deal_ret[5];
                $deals_type_channel         = $deal_ret[6];
                $deals_type_corporate       = $deal_ret[7];

                $order_in_process = SysCrmDealTrack::select()->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '".date('Y-m')."'")->where('sys_crm_deal_track.receivables','!=', 1)
                ->where('sys_crm_deal_track.company_id',session('logged_session_data.company_id'))->orderby('sys_crm_deal_track.id','desc')->get();

                $sales =  SysHelper::get_total_sales_revenue_all($_SESSION["page_date_id"],session('logged_session_data.company_id'));
                $service =  SysHelper::get_total_service_revenue_all($_SESSION["page_date_id"],session('logged_session_data.company_id'));
                $amc =  SysHelper::get_total_amc_revenue_all($_SESSION["page_date_id"],session('logged_session_data.company_id'));
                $project =  SysHelper::get_total_project_revenue_all($_SESSION["page_date_id"],session('logged_session_data.company_id'));


                $performance = [];
                $company_list = SysCompany::select('id','company_name')->orderby('sort_id','asc')->get();
                if(count($company_list)>0){
                    foreach($company_list as $list){
                        $performance[] = [
                            'company_name' => $list->company_name,
                            'target_gp' => SysCrmReportController::get_total_target_gp($teams,'m',$list->id),
                            'revenue' => SysCrmReportController::get_total_sales_revenue($teams,'m',$list->id),
                            'on_process_deal' => SysCrmReportController::get_total_on_process($teams,'m',$list->id),
                            'forcast' => SysCrmReportController::get_total_forcast($teams,'m',$list->id),
                        ];
                    }
                }

                //new dashboard
                //$sales_revenue =  SysCrmReportController::get_total_sales_revenue($teams,'m',session('logged_session_data.company_id'));
                $sales_revenue =  SysCrmReportController::get_total_sales_revenue_by_company('m',session('logged_session_data.company_id'));
                $on_process =  SysCrmReportController::get_total_on_process($teams,'m',session('logged_session_data.company_id'));
                $target_gp = SysCrmReportController::get_total_target_gp($teams,'m',session('logged_session_data.company_id'));
                //return $target_gp;
                $forcast = SysCrmReportController::get_total_forcast($teams,'m',session('logged_session_data.company_id'));

                $onprocess_service_list = SysCrmPSServiceTable::where('status',1)->orderby('service_date','desc')->get();
                $engi = DB::table('sm_staffs')->select('user_id','full_name')->wherein('user_id',$onprocess_service_list->pluck('engineer'))->get();

                if(Auth::user()->id == 36) { //Jacob George
                    $sales_target = SysCrmSalesTarget::whereRaw("DATE_FORMAT(target_month_from, '%Y-%m') <= '" . date('Y-m') . "'")
                    ->wherein('user_id',[25,36,51,31,27]) ->orderby('target_month_from','desc')->get();
                } else if(Auth::user()->id == 58) { //Thaiab Mohammed
                    $sales_target = SysCrmSalesTarget::whereRaw("DATE_FORMAT(target_month_from, '%Y-%m') <= '" . date('Y-m') . "'")
                    ->wherein('user_id',[58,59,60,62]) ->orderby('target_month_from','desc')->get();
                } else if(Auth::user()->id == 18) { //Prajeesh Prabhakar
                    $sales_target = SysCrmSalesTarget::whereRaw("DATE_FORMAT(target_month_from, '%Y-%m') <= '" . date('Y-m') . "'")
                    ->wherein('user_id',[18,20,19]) ->orderby('target_month_from','desc')->get();
                } else if(Auth::user()->id == 48) { //Parveen Sheik Asif
                    $sales_target = SysCrmSalesTarget::whereRaw("DATE_FORMAT(target_month_from, '%Y-%m') <= '" . date('Y-m') . "'")
                    ->wherein('user_id',[48,39,71]) ->orderby('target_month_from','desc')->get();
                } else {
                    $sales_target = SysCrmSalesTarget::whereRaw("DATE_FORMAT(target_month_from, '%Y-%m') <= '" . date('Y-m') . "'")->orderby('target_month_from','desc')->get();
                }
                

                return view('backEnd.crm.DashboardAdmin', compact('total_leads_new', 'total_leads_qualified','total_leads_unqualified','total_leads_pending','total_leads_closed','total_deals_prospecting','total_deals_quote','total_deals_closure','total_deals_won','total_deals_lost','deals_type_project','deals_type_channel','deals_type_corporate','leads_type_project','leads_type_channel','leads_type_corporate','order_in_process','sales_target','pending_payments','payment_reminder','payment_pending','partial_invoice','service','amc','project','sales','dealsbyclosedate','performance','sales_revenue','on_process','target_gp','forcast','sales_person','onprocess_service_list','engi'));
            }
// 2 ADMIN END
// 3 ACCOUNTS RECEIVABLE START
            if(Auth::user()->role_id == 3){
                return SysCrmDashboardFunctionController::accounts_receivable(Auth::user()->id,session('logged_session_data.company_id'),"","","");
            }
// 3 ACCOUNTS RECEIVABLE END
// 27 ACCOUNTS HEAD START
            if(Auth::user()->role_id == 27){
                return SysCrmDashboardFunctionController::accounts_head(Auth::user()->id,session('logged_session_data.company_id'),"","","");
            }
// 27 ACCOUNTS HEAD END
// 4 BILLING START
            if(Auth::user()->role_id == 4){
                return SysCrmDashboardFunctionController::billing(Auth::user()->id,session('logged_session_data.company_id'),"","","");
            }
// 4 BILLING END
// 5 SALES START
            if(Auth::user()->role_id == 5){
                return SysCrmDashboardFunctionController::sales($request, Auth::user()->id,session('logged_session_data.company_id'),"","","");
            }
// 5 SALES END
// 30 SALES CO-ORDINATOR START
            if(Auth::user()->role_id == 30){
                return SysCrmDashboardFunctionController::sales($request, Auth::user()->id,session('logged_session_data.company_id'),"","","");
            }
// 30 SALES CO-ORDINATOR END
// 6 DELIVERY (LOGISTIC) DEPT START
            if(Auth::user()->role_id == 6){
                return SysCrmDashboardFunctionController::logistic_dept(Auth::user()->id,session('logged_session_data.company_id'),"","","");
            }
// 6 DELIVERY (LOGISTIC) DEPT END
// 29 DELIVERY (LOGISTIC) DEPT HEAD START
            if(Auth::user()->role_id == 29){
                return SysCrmDashboardFunctionController::logistic_dept_head(Auth::user()->id,session('logged_session_data.company_id'),"","","");
            }
// 29 DELIVERY (LOGISTIC) DEPT HEAD END
// 8 SALES DEPARTMENT HEAD START
            if(Auth::user()->role_id == 8){
                return SysCrmDashboardFunctionController::sales_department_head(Auth::user()->id,session('logged_session_data.company_id'),"","","");
            }
// 8 SALES DEPARTMENT HEAD END
// 9 PROCUREMENT DEPT HEAD START
            if(Auth::user()->role_id == 9){
                return SysCrmDashboardFunctionController::procurement_dept_head(Auth::user()->id,session('logged_session_data.company_id'),"","","");
            }
// 9 PROCUREMENT DEPT HEAD END
// 10 PROCUREMENT & RECEIVABLE DEPT START
            if(Auth::user()->role_id == 10){
                return SysCrmDashboardFunctionController::accounts_payable_procurement_dept(Auth::user()->id,session('logged_session_data.company_id'),"","","");
            }
// 10 PROCUREMENT & RECEIVABLE DEPT END
// 26 Marketing DEPT START
            if(Auth::user()->role_id == 26){
                return SysCrmDashboardFunctionController::marketing_dept(Auth::user()->id,session('logged_session_data.company_id'),"","","");
            }
// 26 Marketing DEPT END
// 28 ACCOUNTS, BILLING, LOGISTIC DEPT DEPT START
            if(Auth::user()->role_id == 28){
                return SysCrmDashboardFunctionController::accounts(Auth::user()->id,session('logged_session_data.company_id'),"","","");
            }
// 28 ACCOUNTS, BILLING, LOGISTIC DEPT END
// 31 SUPPORT ENGINEER START
            if(Auth::user()->role_id == 31){
                return SysCrmDashboardFunctionController::support_engineer(Auth::user()->id,session('logged_session_data.company_id'),"","","");
            }
// 31 SUPPORT ENGINEER END
// 32 SUPPORT CO-ORDINATOR START
            if(Auth::user()->role_id == 32){
                return SysCrmDashboardFunctionController::support_co_ordinator(Auth::user()->id,session('logged_session_data.company_id'),"","","");
            }
// 32 SUPPORT CO-ORDINATOR END
// 35 SUPPORT CO-ORDINATOR START
            if(Auth::user()->role_id == 35){
                return SysCrmDashboardFunctionController::sales($request, Auth::user()->id,session('logged_session_data.company_id'),"","","");
            }
// 35 SUPPORT CO-ORDINATOR END
// 34 HUMAN RESOURCES MANAGER (HR) START
            if(Auth::user()->role_id == 34){
                return SysCrmDashboardFunctionController::sales($request, Auth::user()->id,session('logged_session_data.company_id'),"","","");
            }
// 34 HUMAN RESOURCES MANAGER (HR) END
























             

        if(Auth::user()->role_id != 1) {


        //Biju
        if(Auth::user()->id==56){
            $order_in_process = SysCrmDealTrack::select('sys_crm_deal_track.*','sys_crm_deals.*')
            ->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')
            ->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '".date('Y-m')."'")
            ->where('sys_crm_deal_track.receivables','!=', 1)
            ->orderby('sys_crm_deal_track.id','desc')->get();
            $new_support = SysCrmSupport::where('status',1)->whereRaw("find_in_set('".Auth::user()->id."',support_person_id)")->orderby('support_date','asc')->get();

            return view('backEnd.crm.DashboardSalesHead', compact('order_in_process','new_support'));
        }

        
        //grn
        if(Auth::user()->id == 74){
            
            $do_pending = SysCrmDealTrack::select('sys_crm_deal_track.id','sys_crm_deals.deal_name','sys_crm_deals.cust_id','sys_crm_deals.owner','sys_crm_deal_track.deal_id','sys_crm_deal_track.created_at')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')->where([['sys_crm_deal_track.delivery', 0],['sys_crm_deal_track.invoice', 1]])->wherenotin('sys_crm_deal_track.purchease',[4])->orderby('sys_crm_deal_track.id','desc')->get();
            
            $grn_pending = SysCrmDealTrack::select('sys_crm_deal_track_approval_purchease_grn.id','sys_crm_deal_track.id AS trackid','sys_crm_deals.deal_name','sys_crm_deals.cust_id','sys_crm_deals.owner','sys_crm_deal_track.deal_id','sys_crm_deal_track.created_at','sys_crm_deal_track_approval_purchease.supplier_name','sys_crm_deal_track_approval_purchease.delivery_date','sys_crm_deal_track_approval_purchease.lpo_no')
            ->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')
            ->join('sys_crm_deal_track_approval_purchease','sys_crm_deal_track_approval_purchease.deal_track_id','sys_crm_deal_track.id')
            ->join('sys_crm_deal_track_approval_purchease_grn','sys_crm_deal_track_approval_purchease_grn.deal_id','sys_crm_deal_track.deal_id')
            ->wherein('sys_crm_deal_track_approval_purchease_grn.status', [0])->wherein('sys_crm_deal_track.purchease', [3])->where('sys_crm_deals.stage', 4)
            ->orderby('sys_crm_deal_track_approval_purchease_grn.id','desc')->get();

            return view('backEnd.crm.DashboardGRN', compact('do_pending','grn_pending'));
        }


        //sales
        if(((session('logged_session_data.department_id')==2 || session('logged_session_data.department_id')==5) && ( Auth::user()->id==34 || session('logged_session_data.designation_id')!=27 && session('logged_session_data.designation_id')!=20 && session('logged_session_data.designation_id')!=35 && session('logged_session_data.designation_id')!=34))){
          
            if(Auth::user()->id==27){//monica
                //$teams= array(27,30,54,62);
                $teams= array(27);
            }
            else if(Auth::user()->id==33){ //jacob
                $teams= array(33,24);
            }
            else if(Auth::user()->id==26 || Auth::user()->id==36 || Auth::user()->id==112 || Auth::user()->id==111){ //26 Naeem & 36 Arianne & 112 Bushra Khot , 111 Muskan
                $teams= array(26,36,112,111);
            }
            else if(Auth::user()->id==44){ // Rajiv R 44, Stephen F Mendonsa 34, Irshaad Aklekar 32, Shamshad Ahmed 79
                $teams= array(44,34,32,79);
            }
            else{
                $teams= array(Auth::user()->id);
            }
            
            $teams2= array(Auth::user()->id);

            //$new_service=[];
            //$new_service_pending=[];
            //if(Auth::user()->id==90){ //jacob
                //$new_service = SysCrmService::select('sys_crm_service.*','sys_crm_deals.deal_name','sys_crm_deals.cust_id','sys_crm_deals.owner')->leftjoin('sys_crm_deals','sys_crm_deals.id','sys_crm_service.deal_id')->where('sys_crm_service.status',1)->orderby('sys_crm_service.id','desc')->get();
                //$new_service_pending = SysCrmService::select('sys_crm_service.*','sys_crm_deals.deal_name','sys_crm_deals.cust_id','sys_crm_deals.owner')->leftjoin('sys_crm_deals','sys_crm_deals.id','sys_crm_service.deal_id')->where('sys_crm_service.status',2)->orderby('sys_crm_service.id','desc')->get();
            //}
            //$total_leads = SysCrmLeads::where('status','!=',0)->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '".date('Y-m')."'")->wherein('owner', $teams)->count();
            //$total_deals                = SysCrmDeals::where('stage','!=',0)->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '".date('Y-m')."'")->wherein('owner', $teams)->count();
            //$onprocess_deals = SysCrmDeals::where('stage','!=',0)->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '".date('Y-m')."'")->wherein('owner', $teams)->get();
            
            $lead_ret = SysHelper::get_lead_filter('m',session('logged_session_data.company_id'));
            $deal_ret = SysHelper::get_deal_filter('m',session('logged_session_data.company_id'));
            
            //lead[$new,$qualified,$unqualified,$project,$channel,$corporate];
            //deal[$prospecting,$quote,$closure,$won,$lost,$project,$channel,$corporate];

            //1-New, 0/2-Qualified, 3-Unqualified, 4-Pending Response, 10-Closed
            $total_leads_new            = $lead_ret[0];
            $total_leads_qualified      = $lead_ret[1];
            $total_leads_unqualified    = $lead_ret[2];
            $total_leads_pending        = $lead_ret[3];
            $total_leads_closed         = $lead_ret[4];
            $leads_type_project         = $lead_ret[5];
            $leads_type_channel         = $lead_ret[6];
            $leads_type_corporate       = $lead_ret[7];

            //1-Prospecting, 2-Quote, 3-Closure, 4-Won, 5-Lost
            $total_deals_prospecting    = $deal_ret[0];
            $total_deals_quote          = $deal_ret[1];
            $total_deals_closure        = $deal_ret[2];
            $total_deals_won            = $deal_ret[3];
            $total_deals_lost           = $deal_ret[4];
            $deals_type_project         = $deal_ret[5];
            $deals_type_channel         = $deal_ret[6];
            $deals_type_corporate       = $deal_ret[7];

            //1-New, 2-Qualified, 3-Unqualified, 0-converted            
            //$total_leads_new            = SysCrmLeads::where('status',1)->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '".date('Y-m')."'")->wherein('owner', $teams)->count();
            //$total_leads_qualified      = SysCrmLeads::where('status',2)->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '".date('Y-m')."'")->wherein('owner', $teams)->count();
            //$total_leads_unqualified    = SysCrmLeads::where('status',3)->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '".date('Y-m')."'")->wherein('owner', $teams)->count();
            //1-Prospecting, 2-Quote, 3-Closure, 4-Won, 5-Lost
            //$total_deals_prospecting    = SysCrmDeals::where('stage',1)->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '".date('Y-m')."'")->wherein('owner', $teams)->count();
            //$total_deals_quote          = SysCrmDeals::where('stage',2)->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '".date('Y-m')."'")->wherein('owner', $teams)->count();
            //$total_deals_closure        = SysCrmDeals::where('stage',3)->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '".date('Y-m')."'")->wherein('owner', $teams)->count();
            //$total_deals_won            = SysCrmDeals::where('stage',4)->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '".date('Y-m')."'")->wherein('owner', $teams)->count();
            //$total_deals_lost           = SysCrmDeals::where('stage',5)->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '".date('Y-m')."'")->wherein('owner', $teams)->count();

            
            $leads_type = SysCrmLeads::select('isproject')->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '".date('Y-m')."'")->wherein('owner', $teams)->get();

            $deals_type = SysCrmDeals::select('isproject')->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '".date('Y-m')."'")->wherein('owner', $teams)->get();

            
            $sales_target_indu = SysCrmSalesTarget::whereRaw("DATE_FORMAT(target_month, '%Y-%m') = '".date('Y-m')."'")->where('user_id', Auth::user()->id)->orderby('id','asc')->get();
            $sales_target = SysCrmSalesTarget::whereRaw("DATE_FORMAT(target_month, '%Y-%m') = '".date('Y-m')."'")->where('user_id', Auth::user()->id)->orderby('id','asc')->get();
                       

            $order_in_process = SysCrmDealTrack::select('sys_crm_deal_track.*','sys_crm_deals.*')
            ->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')
            ->wherein('sys_crm_deals.owner', $teams)
            ->where('sys_crm_deal_track.receivables','!=', 1)
            ->where('sys_crm_deals.stage','!=', 6)
            ->orderby('sys_crm_deal_track.id','desc')->get();

            $order_in_process_all = SysCrmDealTrack::select()->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '".date('Y-m')."'")
            ->wherein('sys_crm_deals.owner', $teams)
            ->where('sys_crm_deal_track.receivables','!=', 1)
            ->orderby('sys_crm_deal_track.id','desc')->get();

            $dealsbyclosedate =  SysHelper::get_deals_close_date($teams2);
            $sales =  SysHelper::get_total_sales_revenue('m',session('logged_session_data.company_id'));

            $pending_payments = SysCrmDeals::select()->join('sys_crm_deal_track_approval_receivables','sys_crm_deals.id','sys_crm_deal_track_approval_receivables.deal_id')
            ->join('sys_crm_deal_track','sys_crm_deals.id','sys_crm_deal_track.deal_id')->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_receivables.created_at, '%Y-%m') = '".date('Y-m')."'")->where('sys_crm_deal_track.receivables','!=', 1)->where('sys_crm_deal_track.delivery', 1)->wherein('owner', $teams)->orderby('sys_crm_deal_track_approval_receivables.id','asc')->get();

            //'total_leads','total_deals','onprocess_deals','new_service','new_service_pending',


            return view('backEnd.crm.DashboardSales', compact('total_leads_new', 'total_leads_qualified','total_leads_unqualified','total_deals_prospecting','total_deals_quote','total_deals_closure','total_deals_won','total_deals_lost','order_in_process','sales_target','pending_payments','sales_target_indu','leads_type','deals_type','dealsbyclosedate','sales'));
        }
        //Technical
        if(session('logged_session_data.department_id')==3){

            if(Auth::user()->id==33 || Auth::user()->id==90){ //jacob
                $teams= array("33","31","59");
            }
            else{
                $teams= array(Auth::user()->id);
            }
            
            $teams2= array(Auth::user()->id);
            
            $new_service=[];
            $new_service_pending=[];
            if(Auth::user()->id==33 || Auth::user()->id==90){ //jacob
                $new_service = SysCrmService::select('sys_crm_service.*','sys_crm_deals.deal_name','sys_crm_deals.cust_id','sys_crm_deals.owner')
                ->leftjoin('sys_crm_deals','sys_crm_deals.id','sys_crm_service.deal_id')->where('sys_crm_service.status',1)->orderby('sys_crm_service.id','desc')->get();
                $new_service_pending = SysCrmService::select('sys_crm_service.*','sys_crm_deals.deal_name','sys_crm_deals.cust_id','sys_crm_deals.owner')
                ->leftjoin('sys_crm_deals','sys_crm_deals.id','sys_crm_service.deal_id')
                ->where('sys_crm_service.status',2)->orderby('sys_crm_service.id','desc')->get();
            }
            else{
                $ids= SysCrmServiceAssign::select('service_id')->where('user_id', Auth::user()->id)->get();
                if(count($ids)>0){
                    foreach($ids as $id){
                        $d[]=$id->service_id;
                    }

                $new_service = SysCrmService::select('sys_crm_service.*','sys_crm_deals.deal_name','sys_crm_deals.cust_id','sys_crm_deals.owner')
                ->leftjoin('sys_crm_deals','sys_crm_deals.id','sys_crm_service.deal_id')->where('sys_crm_service.status',1)->wherein('sys_crm_service.id',$d)->orderby('sys_crm_service.id','desc')->get();
                $new_service_pending = SysCrmService::select('sys_crm_service.*','sys_crm_deals.deal_name','sys_crm_deals.cust_id','sys_crm_deals.owner')
                ->leftjoin('sys_crm_deals','sys_crm_deals.id','sys_crm_service.deal_id')
                ->where('sys_crm_service.status',2)->wherein('sys_crm_service.id',$d)->orderby('sys_crm_service.id','desc')->get();
                
                }
            }
            
            $new_support = SysCrmSupport::where('status',1)->whereRaw("find_in_set('".Auth::user()->id."',support_person_id)")->orderby('support_date','asc')->get();
            
            $order_in_process = SysCrmDealTrack::select('sys_crm_deal_track.*','sys_crm_deals.*')
            ->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')
            ->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '".date('Y-m')."'")
            ->where('sys_crm_deals.owner', Auth::user()->id)
            ->where('sys_crm_deal_track.receivables','!=', 1)
            ->orderby('sys_crm_deal_track.id','desc')->get();
            
            $collaboration= SysCrmDeals::select('sys_crm_deals.*','users.full_name')->join('sys_crm_deals_collaboration','sys_crm_deals_collaboration.deal_id','sys_crm_deals.id')
            ->join('users','users.id','sys_crm_deals_collaboration.user_id')
            ->whereRaw("DATE_FORMAT(sys_crm_deals.created_at, '%Y-%m') = '".date('Y-m')."'")
            ->wherein('sys_crm_deals_collaboration.user_id',$teams)->get();

            $service =  SysHelper::get_total_service_revenue('q',session('logged_session_data.company_id'));
            $amc =  SysHelper::get_total_amc_revenue('q',session('logged_session_data.company_id'));
            $sales =  SysHelper::get_total_sales_revenue('q',session('logged_session_data.company_id'));            
            $gp =  SysHelper::get_total_sales_gp('q',session('logged_session_data.company_id'));

            $amc_exp = SysCrmDeals::select('sys_crm_deals.*','sys_crm_amc.from_date','sys_crm_amc.to_date')
            ->join('sys_crm_quote_items','sys_crm_quote_items.deal_id','sys_crm_deals.id')
            ->leftjoin('sys_crm_amc','sys_crm_amc.deal_id','sys_crm_deals.id')
            ->wherein('sys_crm_deals.stage',[4])
            //->wherein('sys_crm_quote_items.product_id',[9976,10465,10497])
            ->orderby('sys_crm_deals.id','desc')->get();

            $amc_exp_m = SysCrmDeals::select('sys_crm_deals.*','sys_crm_amc.from_date','sys_crm_amc.to_date')
            ->join('sys_crm_quote_items','sys_crm_quote_items.deal_id','sys_crm_deals.id')
            ->leftjoin('sys_crm_amc','sys_crm_amc.deal_id','sys_crm_deals.id')
            ->wherein('sys_crm_deals.stage',[4])
            //->wherein('sys_crm_quote_items.product_id',[9976,10465,10497])
            ->whereRaw("DATE_FORMAT(sys_crm_amc.to_date, '%Y-%m') = '".date('Y-m')."'")
            ->orderby('sys_crm_deals.id','desc')->get();
            
            $sales_target = SysCrmSalesTarget::whereRaw("DATE_FORMAT(target_month, '%Y-%m') = '".date('Y-m')."'")
            ->where('company_id',13)->orderby('id','asc')->get();

            return view('backEnd.crm.DashboardTechnical', compact('order_in_process','collaboration','new_service','new_service_pending','service','amc','sales','new_support','amc_exp','amc_exp_m','sales_target','gp'));
        }

        // accounts
        if(session('logged_session_data.designation_id')==8){
                
            $pending_deals = SysCrmDealTrack::select('sys_crm_deal_track.*','sys_crm_deals.deal_name','sys_crm_deals.cust_id','sys_crm_deals.owner')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')
            ->where('sys_crm_deals.stage',4)
            ->where('sys_crm_deal_track.accounts', 0)->orderby('sys_crm_deal_track.id','asc')->get();

            // $received_payments = SysCrmDeals::select()->join('sys_crm_deal_track_approval_receivables','sys_crm_deals.id','sys_crm_deal_track_approval_receivables.deal_id')
            // ->join('sys_crm_deal_track','sys_crm_deals.id','sys_crm_deal_track.deal_id')->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_receivables.created_at, '%Y-%m') = '".date('Y-m')."'")->where('sys_crm_deal_track_approval_receivables.payment_status', 1)->orderby('sys_crm_deal_track_approval_receivables.id','asc')->get();

            $pending_payments = SysCrmDeals::select()->join('sys_crm_deal_track_approval_receivables','sys_crm_deals.id','sys_crm_deal_track_approval_receivables.deal_id')
            ->join('sys_crm_deal_track','sys_crm_deals.id','sys_crm_deal_track.deal_id')->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_receivables.created_at, '%Y-%m') = '".date('Y-m')."'")->where('sys_crm_deal_track.receivables','!=', 1)->where('sys_crm_deal_track.delivery', 1)->orderby('sys_crm_deal_track_approval_receivables.id','asc')->get();
                      
            $sales_target = SysCrmSalesTarget::whereRaw("DATE_FORMAT(target_month, '%Y-%m') = '".date('Y-m')."'")->orderby('id','asc')->get();

            /*$total_revenue_won        = SysHelper::get_total_revenue_all();
            $total_revenue_quote      = SysHelper::get_total_forcast_all();
            $total_deals                = SysCrmDeals::where('stage','!=',0)->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '".date('Y-m')."'")->count();
            $total_deals_prospecting    = SysCrmDeals::where('stage',1)->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '".date('Y-m')."'")->count();
            $total_deals_quote          = SysCrmDeals::where('stage',2)->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '".date('Y-m')."'")->count();
            $total_deals_closure        = SysCrmDeals::where('stage',3)->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '".date('Y-m')."'")->count();            
            $total_deals_won            = SysCrmDeals::where('stage',4)->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '".date('Y-m')."'")->count();
            $total_deals_lost           = SysCrmDeals::where('stage',5)->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '".date('Y-m')."'")->count();*/

            $payment_reminder = SysCrmDealTrack::select('sys_crm_deal_track.id','sys_crm_deals.deal_name','sys_crm_deals.cust_id','sys_crm_deals.owner','sys_crm_deal_track.deal_id','sys_crm_deal_track.created_at','reminder_date')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')
            ->join('sys_crm_deal_track_approval_receivables','sys_crm_deal_track_approval_receivables.deal_id','sys_crm_deal_track.deal_id')
            ->where([['sys_crm_deal_track.receivables', 3]])->whereRaw("DATE_FORMAT(reminder_date, '%Y-%m-%d') >= '".date('Y-m-d')."'")
            ->orderby('reminder_date','asc')->get();

            $payment_pending = SysCrmDealTrack::select('sys_crm_deal_track.id','sys_crm_deals.deal_name','sys_crm_deals.cust_id','sys_crm_deals.owner','sys_crm_deal_track.deal_id','sys_crm_deal_track.created_at','reminder_date')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')
            ->join('sys_crm_deal_track_approval_receivables','sys_crm_deal_track_approval_receivables.deal_id','sys_crm_deal_track.deal_id')
            ->where([['sys_crm_deal_track.receivables', 3]])->whereRaw("DATE_FORMAT(reminder_date, '%Y-%m-%d') < '".date('Y-m-d')."'")
            ->orderby('reminder_date','asc')->get();
            
            return view('backEnd.crm.DashboardAccounts', compact('pending_deals','pending_payments','sales_target','payment_reminder','payment_pending'));
    
        }

        //purchease
        if(session('logged_session_data.designation_id')==20){
            
            $purchease_pending = SysCrmDealTrack::select('sys_crm_deal_track.id','sys_crm_deals.deal_name','sys_crm_deals.cust_id','sys_crm_deals.owner','sys_crm_deal_track.deal_id','sys_crm_deal_track.created_at')
            ->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')
            ->where('sys_crm_deal_track.sales', 1)->wherein('sys_crm_deal_track.purchease', [0,4])->wherenotin('sys_crm_deal_track.delivery', [1])->where('sys_crm_deals.stage', 4)
            ->wherenotin('sys_crm_deal_track.purchease',[4])->orderby('sys_crm_deal_track.id','desc')->get();

            // $invoice_pending = SysCrmDealTrack::select('sys_crm_deal_track.id','sys_crm_deals.deal_name','sys_crm_deals.cust_id','sys_crm_deals.owner','sys_crm_deal_track.deal_id','sys_crm_deal_track.created_at')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '".date('Y-m')."'")->where('sys_crm_deal_track.invoice', 0)->orderby('sys_crm_deal_track.id','desc')->get();
            
            $under_purchase = SysCrmDealTrack::select('sys_crm_deal_track.id','sys_crm_deals.deal_name','sys_crm_deals.cust_id','sys_crm_deals.owner','sys_crm_deal_track.deal_id','sys_crm_deal_track.created_at','sys_crm_deal_track_approval_purchease.lpo_no','sys_crm_deal_track_approval_purchease.delivery_date','sys_crm_deal_track_approval_purchease.remarks','part_no','supplier_name')
            ->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')
            ->join('sys_crm_deal_track_approval_purchease','sys_crm_deal_track_approval_purchease.deal_id','sys_crm_deal_track.deal_id')
            ->where('sys_crm_deal_track.purchease', 3)->orderby('sys_crm_deal_track.id','desc')->get();

            $partial_delivery = SysCrmDealTrack::select('sys_crm_deal_track.id','sys_crm_deal_track_approval_purchease.remarks','sys_crm_deals.deal_name','sys_crm_deals.cust_id','sys_crm_deals.owner','sys_crm_deal_track.deal_id','sys_crm_deal_track.created_at')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')
            ->join('sys_crm_deal_track_approval_purchease','sys_crm_deal_track_approval_purchease.deal_id','sys_crm_deal_track.deal_id')
            ->where('sys_crm_deal_track.purchease', 4)->orderby('sys_crm_deal_track.id','desc')->get();

            $purchase_completed = SysCrmDealTrack::select('sys_crm_deal_track.id','sys_crm_deals.deal_name','sys_crm_deals.cust_id','sys_crm_deals.owner','sys_crm_deal_track.deal_id','sys_crm_deal_track.created_at')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')
            ->join('sys_crm_deal_track_approval_purchease','sys_crm_deal_track_approval_purchease.deal_id','sys_crm_deal_track.deal_id')
            ->where('sys_crm_deal_track_approval_purchease.validation', 1)->orderby('sys_crm_deal_track.id','desc')->get();
            
            return view('backEnd.crm.DashboardPurchease', compact('purchease_pending', 'under_purchase','partial_delivery','purchase_completed'));
        }

        //invoice
        if(session('logged_session_data.designation_id')==35){
            
            $invoice_pending = SysCrmDealTrack::select('sys_crm_deal_track.id','sys_crm_deals.deal_name','sys_crm_deals.cust_id','sys_crm_deals.owner','sys_crm_deal_track.deal_id','sys_crm_deal_track.created_at')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')->where('sys_crm_deal_track.sales', 1)->wherein('sys_crm_deal_track.purchease',[1,4])->where(
                function($q){
                    $q->where('sys_crm_deal_track.invoice', 0)
                      ->orWhere('sys_crm_deal_track.invoice', 3);
               }
            )->where('sys_crm_deals.stage', 4)->orderby('sys_crm_deal_track.id','asc')->get();
            
            $partial_invoice = SysCrmDealTrack::select('sys_crm_deal_track.id','sys_crm_deals.deal_name','sys_crm_deals.cust_id','sys_crm_deals.owner','sys_crm_deal_track.deal_id','sys_crm_deal_track.created_at')
            ->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')
            ->join('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_track_id','sys_crm_deal_track.id')
            ->where([['sys_crm_deal_track.invoice', 1],['sys_crm_deal_track.sales', 1]])->wherein('sys_crm_deal_track.purchease', [1,4])
            ->where('sys_crm_deal_track_approval_invoice.partial_invoice',1)
            ->orderby('sys_crm_deal_track.id','asc')->get();
            
            $do_pending = SysCrmDealTrack::select('sys_crm_deal_track.id','sys_crm_deals.deal_name','sys_crm_deals.cust_id','sys_crm_deals.owner','sys_crm_deal_track.deal_id','sys_crm_deal_track.created_at')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '".date('Y-m')."'")->where('sys_crm_deal_track.delivery', 0)->where('sys_crm_deals.stage', 4)->orderby('sys_crm_deal_track.id','asc')->get();
            
            return view('backEnd.crm.DashboardInvoice', compact('invoice_pending','do_pending','partial_invoice'));
        }

        //delivery
        if(session('logged_session_data.designation_id')==34){
            
            $do_pending = SysCrmDealTrack::select('sys_crm_deal_track.id','sys_crm_deals.deal_name','sys_crm_deals.cust_id','sys_crm_deals.owner','sys_crm_deal_track.deal_id','sys_crm_deal_track.created_at')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')->where([['sys_crm_deal_track.delivery', 0],['sys_crm_deal_track.invoice', 1]])->wherenotin('sys_crm_deal_track.purchease',[4])->orderby('sys_crm_deal_track.id','desc')->get();

            $do_onprocess = SysCrmDealTrack::select('sys_crm_deal_track.id','sys_crm_deals.deal_name','sys_crm_deals.cust_id','sys_crm_deals.owner','sys_crm_deal_track.deal_id','sys_crm_deal_track.created_at','sys_crm_deal_track.delivery')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')
            //->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '".date('Y-m')."'")
            ->where('stage',4)
            ->where([['sys_crm_deal_track.delivery', '=', 3],['sys_crm_deal_track.delivery', '=', 4],['sys_crm_deal_track.invoice', 1]])->orderby('sys_crm_deal_track.id','desc')->get();
            
            $pending_for_delivery = SysCrmDealTrack::select('sys_crm_deal_track.id','sys_crm_deals.deal_name','sys_crm_deals.cust_id','sys_crm_deals.owner','sys_crm_deal_track.deal_id','sys_crm_deal_track.created_at','sys_crm_deal_track.delivery')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')
            //->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '".date('Y-m')."'")
            ->where('stage',4)
            ->where('sys_crm_deal_track.delivery',4)->orderby('sys_crm_deal_track.id','desc')->get();
            
            $out_for_delivery = SysCrmDealTrack::select('sys_crm_deal_track.id','sys_crm_deals.deal_name','sys_crm_deals.cust_id','sys_crm_deals.owner','sys_crm_deal_track.deal_id','sys_crm_deal_track.created_at','sys_crm_deal_track.delivery')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')
            //->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '".date('Y-m')."'")
            ->where('stage',4)
            ->where('sys_crm_deal_track.delivery',3)->orderby('sys_crm_deal_track.id','desc')->get();
            
            $ready_for_delivery = SysCrmDealTrack::select('sys_crm_deal_track.id','sys_crm_deals.deal_name','sys_crm_deals.cust_id','sys_crm_deals.owner','sys_crm_deal_track.deal_id','sys_crm_deal_track.created_at','sys_crm_deal_track.delivery')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')
            //->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '".date('Y-m')."'")
            ->where('stage',4)
            ->where('sys_crm_deal_track.delivery',5)->orderby('sys_crm_deal_track.id','desc')->get();

            //return $ready_for_delivery;
            
            return view('backEnd.crm.DashboardDelivery', compact('do_pending','do_onprocess','pending_for_delivery','out_for_delivery','ready_for_delivery'));
        }
        //receivables
        if(session('logged_session_data.designation_id')==2){
            
            $receivables_pending = SysCrmDealTrack::select('sys_crm_deal_track.id','sys_crm_deals.deal_name','sys_crm_deals.cust_id','sys_crm_deals.owner','sys_crm_deal_track.deal_id','sys_crm_deal_track.created_at')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')->where([['sys_crm_deal_track.receivables', 0],['sys_crm_deal_track.delivery', 1]])->where('stage',4)
            ->where(function($query) {$query->where('sys_crm_deal_track.technical', 0)->orwhere('sys_crm_deal_track.tech',1);})
            ->where('sys_crm_deals.stage',4)
            ->orderby('sys_crm_deal_track.id','asc')->get();

            $payment_reminder = SysCrmDealTrack::select('sys_crm_deal_track.id','sys_crm_deals.deal_name','sys_crm_deals.cust_id','sys_crm_deals.owner','sys_crm_deal_track.deal_id','sys_crm_deal_track.created_at','reminder_date')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')
            ->join('sys_crm_deal_track_approval_receivables','sys_crm_deal_track_approval_receivables.deal_id','sys_crm_deal_track.deal_id')
            ->where([['sys_crm_deal_track.receivables', 3]])->whereRaw("DATE_FORMAT(reminder_date, '%Y-%m-%d') >= '".date('Y-m-d')."'")
            ->where('sys_crm_deals.stage',4)
            ->orderby('reminder_date','asc')->get();

            $payment_pending = SysCrmDealTrack::select('sys_crm_deal_track.id','sys_crm_deals.deal_name','sys_crm_deals.cust_id','sys_crm_deals.owner','sys_crm_deal_track.deal_id','sys_crm_deal_track.created_at','reminder_date')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')
            ->join('sys_crm_deal_track_approval_receivables','sys_crm_deal_track_approval_receivables.deal_id','sys_crm_deal_track.deal_id')
            ->where([['sys_crm_deal_track.receivables', 3]])->whereRaw("DATE_FORMAT(reminder_date, '%Y-%m-%d') < '".date('Y-m-d')."'")
            ->where('sys_crm_deals.stage',4)
            ->orderby('reminder_date','asc')->get();
            
            return view('backEnd.crm.DashboardReceivables', compact('receivables_pending','payment_reminder','payment_pending'));
        }

        //sales dep manager
        if((session('logged_session_data.designation_id')==27)){

            $teams= array(Auth::user()->id);
            
            $approved_list = SysCrmDealTrack::select()->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '".date('Y-m')."'")->where('sys_crm_deal_track.sales', 1)->orderby('sys_crm_deal_track.id','desc')->get();
           
            $pending_approval = SysCrmDealTrack::select('sys_crm_deal_track.*','sys_crm_deals.owner','sys_crm_deals.cust_id')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')
            ->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '".date('Y-m')."'")
            ->where([['sys_crm_deal_track.accounts', 1],['sys_crm_deal_track.sales', 0]])->orderby('sys_crm_deal_track.id','desc')->get();

            $order_in_process = SysCrmDealTrack::select()->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '".date('Y-m')."'")->where('sys_crm_deal_track.receivables','!=', 1)->wherein('owner', $teams)->orderby('sys_crm_deal_track.id','desc')->get();

            $sales_target_indu = SysCrmSalesTarget::whereRaw("DATE_FORMAT(target_month, '%Y-%m') = '".date('Y-m')."'")->where('user_id', Auth::user()->id)->orderby('id','asc')->get();
            
            $total_revenue_won        = SysHelper::get_total_revenue_all_by_user($teams,date('Y-m-01'),date('Y-m-d'),session('logged_session_data.company_id'));
            $total_revenue_quote      = SysHelper::get_total_forcast($teams);
            $dealsbyclosedate =  SysHelper::get_deals_close_date($teams);
            $sales =  SysHelper::get_total_sales_revenue('m',session('logged_session_data.company_id'));

            return view('backEnd.crm.DashboardSalesDep', compact('total_revenue_won','total_revenue_quote','order_in_process','approved_list','pending_approval','sales_target_indu','dealsbyclosedate','sales'));
        }
    }
            /*return view('backEnd.crm.Dashboard', compact('total_leads_new', 'total_leads_qualified','total_leads_unqualified','total_deals_prospecting','total_deals_quote','total_deals_closure','total_deals_won','total_deals_lost','deals_type_project','deals_type_channel','deals_type_corporate','leads_type_project','leads_type_channel','leads_type_corporate','order_in_process','sales_target','pending_payments','payment_reminder','payment_pending','partial_invoice','service','amc','project','sales','dealsbyclosedate'));*/

        }catch (\Exception $e) {
            
            return $e;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function crmdashboardview($id){
        return $id;
    }
}
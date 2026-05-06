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
use App\SysCrmAmc;
use App\SysCrmAmcComments;
use App\SysCrmAmcTable;
use App\SysCrmAmcTableServiceComments;
use App\SysCrmDeals;
use App\SysCrmDealsCollaboration;
use App\SysCrmDealsComments;
use App\SysCrmDealTrack;
use App\SysCrmLeads;
use App\SysCrmPSServiceTable;
use App\SysCrmPSTableServiceComments;
use App\SysCrmQuoteCSItems;
use App\SysCrmQuoteItems;
use App\SysCrmService;
use App\SysCrmServiceAssign;
use App\SysCrmServiceComments;
use App\SysCrmSupport;
use App\SysCrmSupportActivity;
use App\SysCrmSupportComments;
use App\SysCurrencySettings;
use App\SysCustSuppl;
use App\SysCustSupplAddressbook;
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
use App\SysCrmAmcTableServiceRequest;
use App\SysCrmAmcTableServiceScopeofWork;


class SysCrmEngineerTrackingController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
    }

    public function index(Request $request, $type = null, $id = null)
    {
  
        try {

            $ctrl_track_id = "";
            $ctrl_deal_id = "";
            $ctrl_customer = "";
            $ctrl_engineer = "";
            $ctrl_from_date = null;
            $ctrl_to_date = null;
            $ctrl_status = "";


            //id doc_number, date, cust_name, service_engineer, scope_of_work, service_date, service_time, status

            $amc_query = DB::table('sys_crm_amc_table')->select('sys_crm_amc_table.id', 'sys_crm_amc_table.status as amc_status', 'sys_crm_amc_table.doc_number', 'deal.code as deal_code', 'deal.id as deal_id', 'sys_crm_amc_table.date', 'c.name as cust_name', 'ser.service_engineer', 'ser.scope_of_work', 'ser.service_date', 'ser.service_time', 'cmt.status', 'cmt.work_date', 'cmt.work_time_from', 'cmt.work_time_to', DB::raw("'AMC' as type"), DB::raw('TIMESTAMPDIFF(MINUTE, cmt.work_time_from, cmt.work_time_to) as tim'), 'cmt.created_by as comment_by')
                ->join('sys_crm_amc_table_service_request as ser', 'ser.amc_id', 'sys_crm_amc_table.id')
                ->leftjoin('sys_crm_amc_table_service_comments as cmt', 'cmt.amc_id', 'sys_crm_amc_table.id')
                ->leftjoin('sm_staffs as st', 'st.user_id', 'ser.service_engineer')
                ->leftjoin('sys_cust_suppl as c', 'c.id', 'sys_crm_amc_table.cust_name')
                ->leftjoin('sys_crm_deals as deal', 'deal.id', 'sys_crm_amc_table.deal_id')

                ->when(session('logged_session_data.company_id') != 1, function ($query) {
                    $query->where('sys_crm_amc_table.company_id', session('logged_session_data.company_id'));
                })


                ->wherein('sys_crm_amc_table.status', [2, 3, 5])->where('sys_crm_amc_table.is_auto', 0);

            $ps_query = DB::table('sys_crm_ps_service_table')
                ->select(
                    'sys_crm_ps_service_table.id',
                    'sys_crm_ps_service_table.doc_number',
                    'deal.code as deal_code',
                    'deal.id as deal_id',
                    'sys_crm_ps_service_table.date',
                    'c.name as cust_name',
                    'sys_crm_ps_service_table.engineer as service_engineer',
                    'sys_crm_ps_service_table.scope_of_work',
                    'sys_crm_ps_service_table.service_date',
                    'sys_crm_ps_service_table.service_time',
                    'sys_crm_ps_service_table.status as ps_status',
                    DB::raw("MAX(cmt.status) as status"),                 // ✅ take latest or any status
                    DB::raw("MAX(cmt.work_date) as work_date"),           // ✅ avoid duplicates
                    DB::raw("MAX(cmt.work_time_from) as work_time_from"),
                    DB::raw("MAX(cmt.work_time_to) as work_time_to"),
                    DB::raw("'PS' as type"),
                    DB::raw("MAX(TIMESTAMPDIFF(MINUTE, cmt.work_time_from, cmt.work_time_to)) as tim"),
                    DB::raw("MAX(cmt.created_by) as comment_by")
                )
                ->leftJoin('sys_crm_ps_table_service_comments as cmt', 'cmt.ps_id', '=', 'sys_crm_ps_service_table.id')
                ->leftJoin('sys_cust_suppl as c', 'c.id', '=', 'sys_crm_ps_service_table.cust_name')
                ->leftJoin('sys_crm_deals as deal', 'deal.id', '=', 'sys_crm_ps_service_table.deal_id')
                ->when(session('logged_session_data.company_id') != 1, function ($query) {
                    $query->where('sys_crm_ps_service_table.company_id', session('logged_session_data.company_id'));
                })



                ->whereIn('sys_crm_ps_service_table.status', [1, 2])
                ->groupBy(
                    'sys_crm_ps_service_table.id',
                ); // ✅ group by all non-aggregated columns




            // $ps_query2 = DB::table('sys_crm_ps_service_table')->select('sys_crm_ps_service_table.id','sys_crm_ps_service_table.doc_number','deal.code as deal_code','sys_crm_ps_service_table.date', 'c.name as cust_name', 'sys_crm_ps_service_table.engineer as service_engineer', 'sys_crm_ps_service_table.scope_of_work', 'sys_crm_ps_service_table.service_date', 'sys_crm_ps_service_table.service_time', 'sys_crm_ps_service_table.status',DB::raw("'PS' as type"))
            // ->leftjoin('sys_crm_ps_table_service_comments as ps','ps.ps_id','sys_crm_ps_service_table.id')
            // ->leftjoin('sys_cust_suppl as c','c.id','sys_crm_ps_service_table.cust_name')
            // ->leftjoin('sys_crm_deals as deal','deal.id','sys_crm_ps_service_table.deal_id')
            // ->where('sys_crm_ps_service_table.company_id',session('logged_session_data.company_id'))->wherein('sys_crm_ps_service_table.status', [1,2])->get();
            // return $ps_query2;



            $presales_query = DB::table('sys_crm_support')->select('sys_crm_support.id', 'sys_crm_support.status as scs_status', 'sys_crm_support.customer_id', 'sys_crm_support.created_at', 'sys_crm_support.contact_person', 'sys_crm_support.mobile', 'sys_crm_support.site_name', 'sys_crm_support.support_date', 'sys_crm_support.time_from', 'sys_crm_support.remarks', 'sys_crm_support.support_person_id', 'sys_crm_support.doc_number', 'deal.code as deal_code', 'deal.id as deal_id', 'sys_crm_support.created_at as date', 'c.name as cust_name', 'sys_crm_support.support_person_id as service_engineer', 'sys_crm_support.remarks as scope_of_work', 'sys_crm_support.support_date as service_date', 'sys_crm_support.time_from as service_time', 'cmt.status', 'cmt.work_date', 'cmt.work_time_from', 'cmt.work_time_to', DB::raw("'PRESALES' as type"), DB::raw('TIMESTAMPDIFF(MINUTE, cmt.work_time_from, cmt.work_time_to) as tim'), 'cmt.created_by as comment_by')
                ->leftjoin('sys_crm_support_comments as cmt', 'cmt.support_id', 'sys_crm_support.id')
                ->leftjoin('sys_crm_deals as d', 'd.id', 'sys_crm_support.deal_id')
                ->leftjoin('sys_cust_suppl as c', 'c.id', 'd.cust_id')
                ->leftjoin('sys_crm_deals as deal', 'deal.id', 'sys_crm_support.deal_id')
                ->when(session('logged_session_data.company_id') != 1, function ($query) {
                    $query->where('sys_crm_support.company_id', session('logged_session_data.company_id'));
                })
                ->wherein('sys_crm_support.status', [2, 3]);

            if (Auth::user()->role_id != 1 && Auth::user()->role_id != 2 && Auth::user()->role_id != 32) {
                $amc_query->whereRaw("find_in_set('" . Auth::user()->id . "',service_engineer)");
                $ps_query->whereRaw("find_in_set('" . Auth::user()->id . "',engineer)");
                $presales_query->whereRaw("find_in_set('" . Auth::user()->id . "',support_person_id)");
            }

            if ($_POST) {
                if ($request->search_track_id != "") {
                    $amc_query->where('doc_number', $request->search_track_id);
                    $ps_query->where('doc_number', $request->search_track_id);
                    $presales_query->where('doc_number', $request->search_track_id);
                    $ctrl_track_id = $request->search_track_id;
                }
                if ($request->search_deal_id != "") {
                    $dealid = SysHelper::get_dealid_from_code($request->search_deal_id);
                    $amc_query->where('deal_id', $dealid);
                    $ps_query->where('deal_id', $dealid);
                    $presales_query->where('deal_id', $dealid);
                    $ctrl_deal_id = $request->search_deal_id;
                }
                if ($request->search_customer_name != "") {
                    $amc_query->where('sys_crm_amc_table.cust_name', $request->search_customer_name);
                    $ps_query->where('sys_crm_ps_service_table.cust_name', $request->search_customer_name);
                    $presales_query->where('customer_id', $request->search_customer_name);
                    $ctrl_customer = $request->search_customer_name;
                }
                if ($request->search_engineer != "") {
                    $amc_query->whereRaw("find_in_set('" . $request->search_engineer . "',service_engineer)");
                    $ps_query->whereRaw("find_in_set('" . $request->search_engineer . "',engineer)");
                    $presales_query->whereRaw("find_in_set('" . $request->search_engineer . "',support_person_id)");
                    $ctrl_engineer = $request->search_engineer;
                }

                $fromDate = $request->search_from_date
                    ? Carbon::createFromFormat('d/m/Y', $request->search_from_date)->format('Y-m-d')
                    : null;

                $toDate = $request->search_to_date
                    ? Carbon::createFromFormat('d/m/Y', $request->search_to_date)->format('Y-m-d')
                    : null;

                if ($fromDate && !$toDate) {
                    $amc_query->whereDate('cmt.work_date', $fromDate);
                    $ps_query->whereDate('cmt.work_date', $fromDate);
                    $presales_query->whereDate('cmt.work_date', $fromDate);
                    $ctrl_from_date = $fromDate;
                }

                if ($toDate && !$fromDate) {
                    $amc_query->whereDate('cmt.work_date', $toDate);
                    $ps_query->whereDate('cmt.work_date', $toDate);
                    $presales_query->whereDate('cmt.work_date', $toDate);
                    $ctrl_to_date = $toDate;
                }

                if ($fromDate && $toDate) {
                    $amc_query->whereBetween('cmt.work_date', [$fromDate, $toDate]);
                    $ps_query->whereBetween('cmt.work_date', [$fromDate, $toDate]);
                    $presales_query->whereBetween('cmt.work_date', [$fromDate, $toDate]);
                    $ctrl_from_date = $fromDate;
                    $ctrl_to_date = $toDate;
                }

                /*if ($request->search_from_date != "" && $request->search_to_date == "") {
                    return $request->search_from_date;
                    $amc_query->whereRaw("DATE_FORMAT(ser.service_date, '%Y-%m-%d') = '".date('Y-m-d', strtotime($request->search_from_date))."'");
                    $ps_query->whereRaw("DATE_FORMAT(sys_crm_ps_service_table.service_date, '%Y-%m-%d') = '".date('Y-m-d', strtotime($request->search_from_date))."'");
                    $presales_query->whereRaw("DATE_FORMAT(sys_crm_support.support_date, '%Y-%m-%d') = '".date('Y-m-d', strtotime($request->search_from_date))."'");
                }
                if ($request->search_to_date != "" && $request->search_from_date == "") {
                    $amc_query->whereRaw("DATE_FORMAT(ser.service_date, '%Y-%m-%d') = '".date('Y-m-d', strtotime($request->search_to_date))."'");
                    $ps_query->whereRaw("DATE_FORMAT(sys_crm_ps_service_table.service_date, '%Y-%m-%d') = '".date('Y-m-d', strtotime($request->search_to_date))."'");
                    $presales_query->whereRaw("DATE_FORMAT(sys_crm_support.support_date, '%Y-%m-%d') = '".date('Y-m-d', strtotime($request->search_to_date))."'");
                }
                if ($request->search_from_date != "" && $request->search_to_date != "") {
                    $amc_query->whereRaw("DATE_FORMAT(ser.service_date, '%Y-%m-%d') >= '".date('Y-m-d', strtotime($request->search_from_date))."'");
                    $amc_query->whereRaw("DATE_FORMAT(ser.service_date, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($request->search_to_date))."'");
                    $ps_query->whereRaw("DATE_FORMAT(sys_crm_ps_service_table.service_date, '%Y-%m-%d') >= '".date('Y-m-d', strtotime($request->search_from_date))."'");
                    $ps_query->whereRaw("DATE_FORMAT(sys_crm_ps_service_table.service_date, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($request->search_to_date))."'");
                    $presales_query->whereRaw("DATE_FORMAT(sys_crm_support.support_date, '%Y-%m-%d') >= '".date('Y-m-d', strtotime($request->search_from_date))."'");
                    $presales_query->whereRaw("DATE_FORMAT(sys_crm_support.support_date, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($request->search_to_date))."'");
                }*/
                if ($request->search_status == 1) {
                    /*$amc_query->wherein('sys_crm_amc_table.status',[2,3]);
                    $ps_query->where('sys_crm_ps_service_table.status',1);
                    $presales_query->wherein('sys_crm_support.status',2);*/
                    $amc_query->where('cmt.status', 1);
                    $ps_query->where('cmt.status', 1);
                    $presales_query->where('cmt.status', 1);
                    $ctrl_status = $request->search_status;
                }
                if ($request->search_status == 2) {
                    /*$amc_query->where('sys_crm_amc_table.status',5);
                    $ps_query->where('sys_crm_ps_service_table.status',2);
                    $presales_query->where('sys_crm_support.status',3);*/
                    $amc_query->where('cmt.status', 2);
                    $ps_query->where('cmt.status', 2);
                    $presales_query->where('cmt.status', 2);
                    $ctrl_status = $request->search_status;
                }
            }

            $amc_list = $amc_query->orderby('sys_crm_amc_table.status', 'asc')->orderby('date', 'desc')->get();

            $ps_list = $ps_query->orderBy(DB::raw("DATE_FORMAT(sys_crm_ps_service_table.service_date, '%H:%i')"), 'asc')->orderBy(DB::raw("DATE_FORMAT(sys_crm_ps_service_table.service_date, '%d-%m-%Y')"), 'asc')
                ->orderby('sys_crm_ps_service_table.status', 'desc')->get();

            $presales_list = $presales_query->orderby('sys_crm_support.id', 'desc')->get();
            //return $ps_list;

            $amc_comments = SysCrmAmcTableServiceComments::wherein('amc_id', $amc_list->pluck('id'))->get();
            $ps_comments = SysCrmPSTableServiceComments::wherein('ps_id', $ps_list->pluck('id'))->get();
            $presales_comments = SysCrmSupportComments::wherein('support_id', $presales_list->pluck('id'))->get();

            $amc_work = DB::table('sys_crm_amc_table_service_scope_of_work')->wherein('amc_id', $amc_list->pluck('id'))->get();
            $ps_work = DB::table('sys_crm_ps_service_table_scope_of_work')->wherein('service_id', $ps_list->pluck('id'))->get();
            $presales_work = DB::table('sys_crm_support_work')->wherein('support_id', $presales_list->pluck('id'))->get();



            $data_list = array_merge($amc_list->toArray(), $ps_list->toArray(), $presales_list->toArray());
            $data = collect($data_list);
            $data = $data->sortBy('status')->sortByDesc('service_date');






            $staff = SmStaff::select('user_id', 'full_name')->get();
            $salesperson = SysHelper::get_engineer_list();
            $salespersonamc = SysCrmAmcTable::select('cust.id', 'cust.code', 'cust.name')
                ->join('sys_cust_suppl as cust', 'cust.id', 'sys_crm_amc_table.cust_name')
                ->when(session('logged_session_data.company_id') != 1, function ($query) {
                    $query->where('sys_crm_amc_table.company_id', session('logged_session_data.company_id'));
                })
                
                ->distinct()->get();

            $customer = SysHelper::get_customer_list_deal_lead();


            $customers_AddRequest = SysCrmPSServiceTable::select('cust.id', 'cust.code', 'cust.name')
                ->join('sys_cust_suppl as cust', 'cust.id', 'sys_crm_ps_service_table.cust_name')
                ->when(session('logged_session_data.company_id') != 1, function ($query) {
                    $query->where('sys_crm_ps_service_table.company_id', session('logged_session_data.company_id'));
                })
                
                ->distinct()->get();


            $deal_track = SysCrmDealTrack::select('deal_id', 'accounts', 'sales', 'purchease', 'invoice', 'delivery', 'receivables')->wherein('deal_id', $data->pluck('deal_id'))->get();

            $deals = SysCrmDeals::select('id', 'stage')->wherein('id', $data->pluck('deal_id'))->get();



            $customer_salesreq = SysCrmSupport::select('cust.id', 'cust.code', 'cust.name')
                ->join('sys_cust_suppl as cust', 'cust.id', 'sys_crm_support.customer_id')
                ->when(session('logged_session_data.company_id') != 1, function ($query) {
                    $query->where('sys_crm_support.company_id', session('logged_session_data.company_id'));
                })
                
                ->distinct()->get();




            $active_id = $id;
            $selectedRecord = [];
            $firstRecord_type = "";



            if ($id) {
               
                if($type == 'amc'){
                    $selectedRecord = $this->crmamcdata($id);
                    $firstRecord_type = 'AMC';
                } else if($type == 'ps'){  
                    $selectedRecord = $this->pstrackservicedata($id);
                    $firstRecord_type = 'PS';
                } else if($type == 'presales'){
                    $selectedRecord = $this->supportlistdata($id);
                    $firstRecord_type = 'PRESALES';
                }
            } else {
                $firstRecord = $data->first();

                if ($firstRecord) {
                    $active_id = $firstRecord->id;
                    if ($firstRecord->type == 'AMC') {
                        $selectedRecord = $this->crmamcdata($firstRecord->id);
                        $firstRecord_type = 'AMC';
                    } else if ($firstRecord->type == 'PS') {
                        $selectedRecord = $this->pstrackservicedata($firstRecord->id);
                        $firstRecord_type = 'PS';
                    } else if ($firstRecord->type == 'PRESALES') {
                        $selectedRecord = $this->supportlistdata($firstRecord->id);
                        $firstRecord_type = 'PRESALES';
                    }
                }
            }


            return view('backEnd.amc.EngineerTracking', compact('data', 'amc_list', 'ps_list', 'presales_list', 'staff', 'salesperson', 'customer', 'amc_comments', 'ps_comments', 'presales_comments', 'amc_work', 'ps_work', 'presales_work', 'deals', 'deal_track', 'salespersonamc', 'customer_salesreq', 'customers_AddRequest', 'ctrl_status', 'ctrl_to_date', 'ctrl_from_date', 'ctrl_engineer', 'ctrl_customer', 'ctrl_deal_id', 'ctrl_track_id', 'active_id', 'selectedRecord', 'firstRecord_type'));
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }


    public function supportlistdata($id)
    {
        try {
            $support = SysCrmSupport::query()
                ->leftJoin('sys_cust_suppl as c', 'c.id', '=', 'sys_crm_support.customer_id')
                ->where('sys_crm_support.id', $id)
                ->select('sys_crm_support.*', 'c.name as customer_name') // add required columns
                ->first();


            $support_activity = SysCrmSupportActivity::where('support_id', $id)->get();
            $support_work = DB::table('sys_crm_support_work')->where('support_id', $id)->get();
            $eng = array_map('intval', explode(',', $support->support_person_id));
            return compact('support', 'support_activity', 'support_work', 'eng');
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }


    public function crmamcdata($id)
    {


        try {
            $amcdata = SysCrmAmcTable::select('sys_crm_amc_table.id', 'sys_crm_amc_table.doc_number', 'sys_crm_amc_table.cust_name', 'sys_crm_amc_table.contact_person', 'sys_crm_amc_table.mobile_no', 'sys_crm_amc_table.date', 'ser.service_engineer', 'ser.location_of_work', 'ser.scope_of_work', 'ser.service_date', 'ser.service_time', 'ser.source', 'st.full_name', 'ser.attachment', 'sys_crm_amc_table.status', 'is_delete')
                ->join('sys_crm_amc_table_service_request as ser', 'ser.amc_id', 'sys_crm_amc_table.id')
                ->leftjoin('sm_staffs as st', 'st.user_id', 'ser.service_engineer')
                ->wherein('sys_crm_amc_table.status', [2, 3, 5])->where('sys_crm_amc_table.is_auto', 0)
                ->where('sys_crm_amc_table.id', $id)
                ->orderby('date', 'desc')->orderby('status', 'asc')->first();

            $amc_work = SysCrmAmcTableServiceScopeofWork::wherein('amc_id', $amcdata->pluck('id'))->get();

            $staff = SysHelper::get_engineer_list();

            $amcdata_id = $amcdata->pluck('id');
            $amc_comments = SysCrmAmcTableServiceComments::select('sys_crm_amc_table_service_comments.*', 'st.full_name', 'w.work')
                ->join('sys_crm_amc_table_service_scope_of_work as w', 'w.id', 'sys_crm_amc_table_service_comments.work_id')
                ->leftjoin('sm_staffs as st', 'st.user_id', 'sys_crm_amc_table_service_comments.engineer_id')
                ->where('sys_crm_amc_table_service_comments.amc_id', $id)
                ->get();

            return compact('amcdata', 'amc_comments', 'amc_work', 'staff');
            // return view('backEnd.amc.DealAmcServiceRequestDetail', compact('amcdata', 'amc_comments', 'amc_work', 'staff'));

        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function pstrackservicedata($id)
    {
        try {
            $psdata = SysCrmPSServiceTable::where('id', $id)->first();
            $service_request = SysCrmPSTableServiceComments::where('ps_id', $psdata->id)->get();
            if (isset($service_request)) {
                $service_request_work = DB::table('sys_crm_ps_service_table_scope_of_work')->where('service_id', $psdata->id)->get();
            } else {
                $service_request_work = [];
            }
            return compact('psdata', 'service_request', 'service_request_work');
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function search(Request $request)
    {
        $q = $request->get('query');
        $formattedDate = null;

        // Detect if query is a date
        if (preg_match('/\d{2}[\/\-]\d{2}[\/\-]\d{4}/', $q)) {
            $normalized = str_replace('/', '-', $q);
            $formattedDate = date('Y-m-d', strtotime($normalized));
        }

        $company_id = session('logged_session_data.company_id');
        $user_id = Auth::user()->id;
        $role_id = Auth::user()->role_id;
        $department_id = session('logged_session_data.department_id');

        // 🔹 AMC Query
        $amc_query = DB::table('sys_crm_amc_table')
            ->select(
                'sys_crm_amc_table.id',
                DB::raw("ANY_VALUE(sys_crm_amc_table.doc_number) as doc_number"),
                DB::raw("ANY_VALUE(deal.code) as deal_code"),
                DB::raw("ANY_VALUE(sys_crm_amc_table.date) as date"),
                DB::raw("ANY_VALUE(c.name) as cust_name"),
                DB::raw("ANY_VALUE(ser.service_engineer) as service_engineer"),
                DB::raw("ANY_VALUE(ser.scope_of_work) as scope_of_work"),
                DB::raw("ANY_VALUE(ser.service_date) as service_date"),
                DB::raw("ANY_VALUE(ser.service_time) as service_time"),
                DB::raw("MAX(cmt.status) as status"),
                DB::raw("MAX(cmt.work_date) as work_date"),
                DB::raw("MAX(cmt.work_time_from) as work_time_from"),
                DB::raw("MAX(cmt.work_time_to) as work_time_to"),
                DB::raw("'AMC' as type"),
                DB::raw("MAX(TIMESTAMPDIFF(MINUTE, cmt.work_time_from, cmt.work_time_to)) as tim"),
                DB::raw("MAX(cmt.created_by) as comment_by")
            )
            ->join('sys_crm_amc_table_service_request as ser', 'ser.amc_id', 'sys_crm_amc_table.id')
            ->leftJoin('sys_crm_amc_table_service_comments as cmt', 'cmt.amc_id', 'sys_crm_amc_table.id')
            ->leftJoin('sys_cust_suppl as c', 'c.id', 'sys_crm_amc_table.cust_name')
            ->leftJoin('sys_crm_deals as deal', 'deal.id', 'sys_crm_amc_table.deal_id')
              ->when(session('logged_session_data.company_id') != 1, function ($query) {
                    $query->where('sys_crm_amc_table.company_id', session('logged_session_data.company_id'));
                })
            
            ->whereIn('sys_crm_amc_table.status', [2, 3, 5])
            ->where('sys_crm_amc_table.is_auto', 0)
            ->groupBy('sys_crm_amc_table.id');

        // 🔹 PS Query
        $ps_query = DB::table('sys_crm_ps_service_table')
            ->select(
                'sys_crm_ps_service_table.id',
                DB::raw("ANY_VALUE(sys_crm_ps_service_table.doc_number) as doc_number"),
                DB::raw("ANY_VALUE(deal.code) as deal_code"),
                DB::raw("ANY_VALUE(sys_crm_ps_service_table.date) as date"),
                DB::raw("ANY_VALUE(c.name) as cust_name"),
                DB::raw("ANY_VALUE(sys_crm_ps_service_table.engineer) as service_engineer"),
                DB::raw("ANY_VALUE(sys_crm_ps_service_table.scope_of_work) as scope_of_work"),
                DB::raw("ANY_VALUE(sys_crm_ps_service_table.service_date) as service_date"),
                DB::raw("ANY_VALUE(sys_crm_ps_service_table.service_time) as service_time"),
                DB::raw("MAX(cmt.status) as status"),
                DB::raw("MAX(cmt.work_date) as work_date"),
                DB::raw("MAX(cmt.work_time_from) as work_time_from"),
                DB::raw("MAX(cmt.work_time_to) as work_time_to"),
                DB::raw("'PS' as type"),
                DB::raw("MAX(TIMESTAMPDIFF(MINUTE, cmt.work_time_from, cmt.work_time_to)) as tim"),
                DB::raw("MAX(cmt.created_by) as comment_by")
            )
            ->leftJoin('sys_crm_ps_table_service_comments as cmt', 'cmt.ps_id', '=', 'sys_crm_ps_service_table.id')
            ->leftJoin('sys_cust_suppl as c', 'c.id', '=', 'sys_crm_ps_service_table.cust_name')
            ->leftJoin('sys_crm_deals as deal', 'deal.id', '=', 'sys_crm_ps_service_table.deal_id')
               ->when(session('logged_session_data.company_id') != 1, function ($query) {
                    $query->where('sys_crm_ps_service_table.company_id', session('logged_session_data.company_id'));
                })
            
            ->whereIn('sys_crm_ps_service_table.status', [1, 2])
            ->groupBy('sys_crm_ps_service_table.id');

        // 🔹 PRESALES Query
        $presales_query = DB::table('sys_crm_support')
            ->select(
                'sys_crm_support.id',
                DB::raw("ANY_VALUE(sys_crm_support.doc_number) as doc_number"),
                DB::raw("ANY_VALUE(deal.code) as deal_code"),
                DB::raw("ANY_VALUE(sys_crm_support.created_at) as date"),
                DB::raw("ANY_VALUE(c.name) as cust_name"),
                DB::raw("ANY_VALUE(sys_crm_support.support_person_id) as service_engineer"),
                DB::raw("ANY_VALUE(sys_crm_support.remarks) as scope_of_work"),
                DB::raw("ANY_VALUE(sys_crm_support.support_date) as service_date"),
                DB::raw("ANY_VALUE(sys_crm_support.time_from) as service_time"),
                DB::raw("MAX(cmt.status) as status"),
                DB::raw("MAX(cmt.work_date) as work_date"),
                DB::raw("MAX(cmt.work_time_from) as work_time_from"),
                DB::raw("MAX(cmt.work_time_to) as work_time_to"),
                DB::raw("'PRESALES' as type"),
                DB::raw("MAX(TIMESTAMPDIFF(MINUTE, cmt.work_time_from, cmt.work_time_to)) as tim"),
                DB::raw("MAX(cmt.created_by) as comment_by")
            )
            ->leftJoin('sys_crm_support_comments as cmt', 'cmt.support_id', '=', 'sys_crm_support.id')
            ->leftJoin('sys_crm_deals as deal', 'deal.id', '=', 'sys_crm_support.deal_id')
            ->leftJoin('sys_cust_suppl as c', 'c.id', '=', 'sys_crm_support.customer_id')
             ->when(session('logged_session_data.company_id') != 1, function ($query) {
                    $query->where('sys_crm_support.company_id', session('logged_session_data.company_id'));
                })
            
            ->whereIn('sys_crm_support.status', [2, 3])
            ->groupBy('sys_crm_support.id');

        // 🔹 Role-based filters
        if (!in_array($role_id, [1, 2, 32])) {
            if (in_array($department_id, [2, 3])) { // Sales or Technical
                $amc_query->whereRaw("find_in_set('$user_id', ser.service_engineer)");
                $ps_query->whereRaw("find_in_set('$user_id', sys_crm_ps_service_table.engineer)");
                $presales_query->whereRaw("find_in_set('$user_id', sys_crm_support.support_person_id)");
            }
        }

        // 🔹 Apply search filters
        if ($q) {
            $like = "%{$q}%";
            foreach ([$amc_query, $ps_query, $presales_query] as $sub) {
                $sub->where(function ($inner) use ($like) {
                    $inner->where('doc_number', 'like', $like)
                        ->orWhere('deal.code', 'like', $like)
                        ->orWhere('c.name', 'like', $like);
                });
            }
        }

        // 🔹 Date filter
        if ($formattedDate) {
            $amc_query->whereDate('ser.service_date', $formattedDate);
            $ps_query->whereDate('sys_crm_ps_service_table.service_date', $formattedDate);
            $presales_query->whereDate('sys_crm_support.support_date', $formattedDate);
        }

        // 🔹 Fetch results
        $amc_list = $amc_query->get();
        $ps_list = $ps_query->get();
        $presales_list = $presales_query->get();

        // Merge all data and sort
        $data_list = array_merge(
            $amc_list->toArray(),
            $ps_list->toArray(),
            $presales_list->toArray()
        );

        $data = collect($data_list)
            ->sortBy('status')
            ->sortByDesc('service_date')
            ->values();

        return response()->json($data);
    }
}

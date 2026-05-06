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
use App\SysCrmAmcTable;
use App\SysCrmAmcServiceTable;
use App\SysCrmAmcAsign;
use App\SysCrmAmcComments;
use App\SysCrmAmcTableServiceComments;
use App\SysCrmAmcTableServiceRequest;
use App\SysCrmAmcTableServiceScopeofWork;
use App\SysCrmAmcUpdates;
use App\SysCrmDeals;
use App\SysCrmDealsCollaboration;
use App\SysCrmDealsComments;
use App\SysCrmDealTrack;
use App\SysCrmLeads;
use App\SysCrmLeadsComments;
use App\SysCrmPSServiceTable;
use App\SysCrmPSTableServiceComments;
use App\SysCrmQuoteCSItems;
use App\SysCrmQuoteItems;
use App\SysCrmService;
use App\SysCrmServiceAssign;
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

class SysCrmPSController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
    }


    /* service part     */



    public function pstrackservicelist(Request $request, $id = null)
    {
        try {
            $ctrl_ps_id = "";
            $ctrl_deal_id = "";
            $ctrl_customer_name = "";
            $ctrl_sales_person = "";
            $ctrl_from_date = "";
            $ctrl_to_date = "";
            $ctrl_search_status = "";

            $salesperson = SysHelper::get_sales_persons();
            $customer = SysHelper::get_customer_list_deal_lead();

            if (session('logged_session_data.company_id') == 1) {
                $amc_query = SysCrmPSServiceTable::select('sys_crm_ps_service_table.*');
            } else {

                $amc_query = SysCrmPSServiceTable::select('sys_crm_ps_service_table.*')->where('company_id', session('logged_session_data.company_id'));
            }

            $service_person = db::table('sm_staffs')->select('user_id', 'full_name')->where('department_id',16)->get();


            if ($_POST) {
                if ($request->search_ps_id != "") {
                    $amc_query->where('doc_number', $request->search_ps_id);
                    $ctrl_ps_id = $request->search_ps_id;
                }
                if ($request->search_deal_id != "") {
                    $amc_query->where('deal_id', SysHelper::get_dealid_from_code($request->search_deal_id));
                    $ctrl_deal_id = $request->search_deal_id;
                }
                if ($request->search_customer_name != "") {
                    $amc_query->where('cust_name', $request->search_customer_name);
                    $ctrl_customer_name = $request->search_customer_name;
                }
                if ($request->search_sales_person != "") {
                    $amc_query->where('sales_person', $request->search_sales_person);
                    $ctrl_sales_person = $request->search_sales_person;
                }
                if ($request->search_from_date != "" && $request->search_to_date == "") {
                    $request->search_from_date = Carbon::createFromFormat('d/m/Y', $request->search_from_date)->format('Y-m-d');
                    $amc_query->whereRaw("DATE_FORMAT(service_date, '%Y-%m-%d') = '" . $request->search_from_date . "'");
                    $ctrl_from_date = $request->search_from_date;
                }
                if ($request->search_to_date != "" && $request->search_from_date == "") {
                    $request->search_to_date = Carbon::createFromFormat('d/m/Y', $request->search_to_date)->format('Y-m-d');
                    $amc_query->whereRaw("DATE_FORMAT(service_date, '%Y-%m-%d') = '" . $request->search_to_date . "'");
                    $ctrl_to_date = $request->search_to_date;
                }
                if ($request->search_from_date != "" && $request->search_to_date != "") {
                    $request->search_to_date = Carbon::createFromFormat('d/m/Y', $request->search_to_date)->format('Y-m-d');
                    $request->search_from_date = Carbon::createFromFormat('d/m/Y', $request->search_from_date)->format('Y-m-d');

                    $amc_query->whereRaw("DATE_FORMAT(service_date, '%Y-%m-%d') >= '" . $request->search_from_date . "'");
                    $amc_query->whereRaw("DATE_FORMAT(service_date, '%Y-%m-%d') <= '" . $request->search_to_date . "'");
                    $ctrl_from_date = $request->search_from_date;
                    $ctrl_to_date = $request->search_to_date;
                }
                if ($request->search_status != "") {
                    $amc_query->where('status', $request->search_status);
                    $ctrl_search_status = $request->search_status;
                } else {
                    $amc_query->where('status', 0);
                }
            } else {
                $amc_query->where('status', 0);
            }
            $support = $amc_query->orderby('id', 'desc')->get();



            $active_id = $id;
            $selectedProj = [];





            if ($id) {
                $selectedProj = $this->pstrackservicedata($id);
            } else {
                $firstRecord = $support->first();
                if ($firstRecord) {
                    $active_id = $firstRecord->id;
                    $selectedProj = $this->pstrackservicedata($firstRecord->id);
                }
            }

            return view('backEnd.amc.DealAmcTrackServiceList', compact('support', 'salesperson', 'customer', 'ctrl_ps_id', 'ctrl_deal_id', 'ctrl_customer_name', 'ctrl_sales_person', 'ctrl_from_date', 'ctrl_to_date', 'ctrl_search_status', 'active_id', 'selectedProj','service_person'));
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function pstrackservicedata($id)
    {
        try {
            $psdata = SysCrmPSServiceTable::where('id', $id)->first();
            $service_request = SysCrmPSTableServiceComments::where('ps_id', $psdata->id)->get();
            $deal = SysCrmDeals::where('id', $psdata->deal_id)->first();
            if(!$deal){
                $deal = null;
                $quotationitems = [];
            }else{
                $quotationitems = SysCrmQuoteItems::where('deal_id', $deal->id)->where('quote_id', $deal->quote_id)->orderby('sort_id', 'ASC')->get();
            }

           
            if (isset($service_request)) {
                $service_request_work = DB::table('sys_crm_ps_service_table_scope_of_work')->where('service_id', $psdata->id)->get();
            } else {
                $service_request_work = [];
            }
            return compact('psdata', 'service_request', 'service_request_work','deal','quotationitems');
        } catch (\Throwable $th) {
            return $th;
        }
    }
    public function pstrackservicedetail($id)
    {
        try {
            $psdata = SysCrmPSServiceTable::where('id', $id)->first();
            $service_request = SysCrmPSTableServiceComments::where('ps_id', $psdata->id)->get();
              $service_request = SysCrmPSTableServiceComments::where('ps_id', $psdata->id)->get();
            $deal = SysCrmDeals::where('id', $psdata->deal_id)->first();
            if(!$deal){
                $deal = null;
                $quotationitems = [];
            }else{
                $quotationitems = SysCrmQuoteItems::where('deal_id', $deal->id)->where('quote_id', $deal->quote_id)->orderby('sort_id', 'ASC')->get();
            }

            if (isset($service_request)) {
                $service_request_work = DB::table('sys_crm_ps_service_table_scope_of_work')->where('service_id', $psdata->id)->get();
            } else {
                $service_request_work = [];
            }
            return view('backEnd.amc.DealAmcTrackServiceDetail', compact('psdata', 'service_request', 'service_request_work','deal','quotationitems'));
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function pstrackservicereqlist(Request $request, $id = null)
    {
        try {
            $ctrl_ps_id = "";
            $ctrl_deal_id = "";
            $ctrl_customer_name = "";
            $ctrl_sales_person = "";
            $ctrl_from_date = "";
            $ctrl_to_date = "";
            $ctrl_search_status = "";

            $salesperson = SysHelper::get_sales_persons();
            $customer = SysHelper::get_customer_list_deal_lead();

            $customers_AddRequest = SysCrmPSServiceTable::select('cust.id', 'cust.code', 'cust.name')
                ->join('sys_cust_suppl as cust', 'cust.id', 'sys_crm_ps_service_table.cust_name')
                 ->when(session('logged_session_data.company_id') != 1, function ($query) {
                    $query->where('sys_crm_ps_service_table.company_id', session('logged_session_data.company_id'));
                })
                
                ->distinct()->get();



            if (session('logged_session_data.company_id') == 1) {
                $ps_query = SysCrmPSServiceTable::wherein('status', [1, 2]);
            } else {
                $ps_query = SysCrmPSServiceTable::where('company_id', session('logged_session_data.company_id'))->wherein('status', [1, 2]);
            }


            if ($_POST) {
                if ($request->search_ps_id != "") {
                    $ps_query->where('doc_number', $request->search_ps_id);
                    $ctrl_ps_id = $request->search_ps_id;
                }
                if ($request->search_deal_id != "") {
                    $ps_query->where('deal_id', SysHelper::get_dealid_from_code($request->search_deal_id));
                    $ctrl_deal_id = $request->search_deal_id;
                }
                if ($request->search_customer_name != "") {
                    $ps_query->where('cust_name', $request->search_customer_name);
                    $ctrl_customer_name = $request->search_customer_name;
                }
                if ($request->search_sales_person != "") {
                    $ps_query->where('sales_person', $request->search_sales_person);
                    $ctrl_sales_person = $request->search_sales_person;
                }
                if ($request->search_from_date != "" && $request->search_to_date == "") {
                    $ps_query->whereRaw("DATE_FORMAT(service_date, '%Y-%m-%d') = '" . date('Y-m-d', strtotime($request->search_from_date)) . "'");
                    $ctrl_from_date = $request->search_from_date;
                }
                if ($request->search_to_date != "" && $request->search_from_date == "") {
                    $ps_query->whereRaw("DATE_FORMAT(service_date, '%Y-%m-%d') = '" . date('Y-m-d', strtotime($request->search_to_date)) . "'");
                    $ctrl_to_date = $request->search_to_date;
                }
                if ($request->search_from_date != "" && $request->search_to_date != "") {
                    $ps_query->whereRaw("DATE_FORMAT(service_date, '%Y-%m-%d') >= '" . date('Y-m-d', strtotime($request->search_from_date)) . "'");
                    $ps_query->whereRaw("DATE_FORMAT(service_date, '%Y-%m-%d') <= '" . date('Y-m-d', strtotime($request->search_to_date)) . "'");
                    $ctrl_from_date = $request->search_from_date;
                    $ctrl_to_date = $request->search_to_date;
                }
                if ($request->search_status != "") {
                    $ps_query->where('status', $request->search_status);
                    $ctrl_search_status = $request->search_status;
                }
            }

            $psData = $ps_query->orderByRaw("FIELD(status, 1, 2)")->orderBy(DB::raw("DATE_FORMAT(service_date, '%H:%i')"), 'asc')->orderBy(DB::raw("DATE_FORMAT(service_date, '%d-%m-%Y')"), 'asc')->get();


            $staff = SmStaff::select('user_id', 'full_name')->get();
            $work = DB::table('sys_crm_ps_service_table_scope_of_work')->get();

            $ps_comments = SysCrmPSTableServiceComments::get();

            $active_id = $id;
            $selectedProj = [];




            if ($id) {
                $selectedProj = $this->pstrackservicereqdata($id);
            } else {
                $firstRecord = $psData->first();
                if ($firstRecord) {
                    $active_id = $firstRecord->id;
                    $selectedProj = $this->pstrackservicereqdata($firstRecord->id);
                }
            }


            return view('backEnd.amc.DealAmcTrackServiceReqList', compact('psData', 'ps_comments', 'staff', 'salesperson', 'customer', 'ctrl_ps_id', 'ctrl_deal_id', 'ctrl_customer_name', 'ctrl_sales_person', 'ctrl_from_date', 'ctrl_to_date', 'ctrl_search_status', 'work', 'customers_AddRequest', 'selectedProj', 'active_id'));
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function pstrackservicereqdata($id)
    {
        try {
            $psdata = SysCrmPSServiceTable::where('id', $id)->first();
            $service_request = SysCrmPSTableServiceComments::where('ps_id', $psdata->id)->get();

            if (isset($psdata)) {
                $service_request_work = DB::table('sys_crm_ps_service_table_scope_of_work')->where('service_id', $psdata->id)->get();
            } else {
                $service_request_work = [];
            }

            $staff = SmStaff::select('user_id', 'full_name')->get();

            return compact('psdata', 'service_request', 'service_request_work', 'staff');
        } catch (\Throwable $th) {
            return $th;
        }
    }
    public function pstrackservicereqdetail($id)
    {
        try {
            $psdata = SysCrmPSServiceTable::where('id', $id)->first();
            $service_request = SysCrmPSTableServiceComments::where('ps_id', $psdata->id)->get();

            if (isset($psdata)) {
                $service_request_work = DB::table('sys_crm_ps_service_table_scope_of_work')->where('service_id', $psdata->id)->get();
            } else {
                $service_request_work = [];
            }

            $staff = SmStaff::select('user_id', 'full_name')->get();

            return view('backEnd.amc.DealAmcTrackServiceReqDetail', compact('psdata', 'service_request', 'service_request_work', 'staff'));
        } catch (\Throwable $th) {
            return $th;
        }
    }


    public function psservicerequestedit(Request $request)
    {
        try {
            $ret = SysCrmPSServiceTable::with('deal_code:id,code')->select('sys_crm_ps_service_table.*', 'u.name')->join('sys_cust_suppl as u', 'u.id', 'sys_crm_ps_service_table.cust_name')->where('sys_crm_ps_service_table.id', $request->id)->get();
            return json_encode(array('data' => $ret));
        } catch (\Throwable $th) {
            $ret = $th;
            return json_encode(array('data' => $ret));
        }
    }
    public function psservicerequestwork(Request $request)
    {
        try {
            $ret = DB::table('sys_crm_ps_service_table_scope_of_work')->where('service_id', $request->id)->get();
            return json_encode(array('data' => $ret));
        } catch (\Throwable $th) {
            $ret = $th;
            return json_encode(array('data' => $ret));
        }
    }

    public function psservicerequestupdate(Request $request)
    {

        try {
            $attachment = "";
            if ($request->file('attachment') != "") {
                $file1 = $request->file('attachment');
                $attachment = md5(time()) . "attachment." . $file1->getclientoriginalextension();
                $file1->move('public/uploads/crm_amc_doc/', $attachment);
                $attachment = $attachment;
            }


            DB::table('sys_crm_ps_service_table')->where('id', $request->amc_id)->update(
                [
                    'deal_id' => SysHelper::get_dealid_from_code($request->deal_id),
                    'contact_person' => $request->contact_person,
                    'mobile' => $request->mobile,
                    'location_of_work' => $request->location_of_work,
                    'scope_of_work' => '',
                    'service_date' => Carbon::createFromFormat('d/m/Y', $request->service_date)->format('Y-m-d'),
                    'service_time' => $request->service_time,
                    'engineer' => implode(',', $request->engineer),
                    'updated_by' => Auth::user()->id,
                    'updated_at' => Carbon::now('+04:00'),
                    'status' => $request->status_edit, // Added status update
                ]
            );

            DB::table('sys_crm_ps_service_table_scope_of_work')->where('service_id', $request->amc_id)->delete();
            if (count($request->scope_of_work) > 0) {
                for ($i = 0; $i < count($request->scope_of_work); $i++) {
                    if ($request->scope_of_work[$i] != "") {
                        DB::table('sys_crm_ps_service_table_scope_of_work')->insert([
                            'work' => $request->scope_of_work[$i],
                            'service_id' => $request->amc_id,
                            'updated_at' => Carbon::now('+04:00'),
                        ]);
                    }
                }
            }

            if ($attachment != "") {
                DB::table('sys_crm_ps_service_table')->where('id', $request->amc_id)->update(
                    ['attachment' => $attachment,]
                );
            }
            Toastr::success('PS Request has been updated successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function psservicerequestupdate2(Request $request)
    {
        try {
            //return $request->all();
            db::beginTransaction();
            $attachment = "";
            if ($request->file('attachment') != "") {
                $file1 = $request->file('attachment');
                $attachment = md5(time()) . "attachment." . $file1->getclientoriginalextension();
                $file1->move('public/uploads/crm_amc_doc/', $attachment);
                $attachment = $attachment;
            }

            //$ps_det = DB::table('sys_crm_ps_service_table')->where('id',$request->amc_id)->first();
            $task = syscrmpsservicetable::find($request->amc_id);
            $new = $task->replicate();
            $new->save();
            $newID = $new->id;

            DB::table('sys_crm_ps_service_table')->where('id', $newID)->update(
                [
                    'contact_person' => $request->contact_person,
                    'mobile' => $request->mobile,
                    'location_of_work' => $request->location_of_work,
                    'scope_of_work' => '',
                    'service_date' => $request->service_date,
                    'service_time' => $request->service_time,
                    'engineer' => implode(',', $request->engineer),
                    'updated_by' => Auth::user()->id,
                    'updated_at' => Carbon::now('+04:00'),
                ]
            );

            if (count($request->scope_of_work) > 0) {
                for ($i = 0; $i < count($request->scope_of_work); $i++) {
                    if ($request->scope_of_work[$i] != "") {
                        DB::table('sys_crm_ps_service_table_scope_of_work')->insert([
                            'work' => $request->scope_of_work[$i],
                            'service_id' => $newID,
                            'updated_at' => Carbon::now('+04:00'),
                        ]);
                    }
                }
            }

            if ($attachment != "") {
                DB::table('sys_crm_ps_service_table')->where('id', $request->amcid_edit)->update(
                    ['attachment' => $attachment,]
                );
            }
            DB::commit();
            Toastr::success('PS Request has been updated successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    public function psservicerequestdeactivate($id)
    {
        try {
            SysCrmPSServiceTable::where('id', $id)->update(['is_delete' => 1]);
            Toastr::success('PS has been Deactivated successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function psservicerequestactivate($id)
    {
        try {
            SysCrmPSServiceTable::where('id', $id)->update(['is_delete' => 0]);
            Toastr::success('PS has been Deactivated successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    //Dashboard Codes
    public function ps_servicerequestcomments(Request $request)
    {
        try {
            SysCrmPSTableServiceComments::insert([
                'ps_id' => $request->ps_id,
                'comments' => $request->comments,
                'engineer_id' => Auth::user()->id,
                'work_date' => $request->work_date,
                'work_time_from' => $request->work_time_from,
                'work_time_to' => $request->work_time_to,
                'status' => $request->status,
                'created_by' => Auth::user()->id,
                'created_at' => Carbon::now('+04:00'),
            ]);


            SysCrmPSServiceTable::where('id', $request->ps_id)->update(['status' => $request->status]);

            //$ret = SysCrmAmcTableServiceComments::select('sys_crm_amc_table_service_comments.*','st.full_name','w.work')
            //->join('sys_crm_amc_table_service_scope_of_work as w' ,'w.id','sys_crm_amc_table_service_comments.work_id')
            //->leftjoin('sm_staffs as st','st.user_id','sys_crm_amc_table_service_comments.engineer_id')
            //->where('sys_crm_amc_table_service_comments.amc_id', $request->amc_id)->get();

            $ret = SysCrmPSTableServiceComments::select('sys_crm_ps_table_service_comments.*', 'st.full_name')
                ->leftjoin('sm_staffs as st', 'st.user_id', 'sys_crm_ps_table_service_comments.engineer_id')
                ->where('sys_crm_ps_table_service_comments.ps_id', $request->ps_id)->get();

            return json_encode(array('data' => $ret));
        } catch (\Throwable $th) {
            $ret = $th;
            return json_encode(array('data' => $ret));
        }
    }

    public function ps_servicerequest_get_comments(Request $request)
    {
        try {
            $ret = SysCrmPSTableServiceComments::select('sys_crm_ps_table_service_comments.*', 'st.full_name')
                ->leftjoin('sm_staffs as st', 'st.user_id', 'sys_crm_ps_table_service_comments.engineer_id')
                ->where('sys_crm_ps_table_service_comments.ps_id', $request->id)->get();
            return json_encode(array('data' => $ret));
        } catch (\Throwable $th) {
            $ret = $th;
            return json_encode(array('data' => $ret));
        }
    }

    //Dashboard Codes



    public function amc_engineerservicelist(Request $request)
    {
        $id = $request->id;
        $englist = SysHelper::get_engineer_list();

        foreach ($englist as $list) {
            if ($list->user_id == $id)
                echo '<option  select value="' . $list->user_id . '" >' . $list->full_name . '</option>';
        }
    }

    public function amctrackserviceid(Request $request)
    {
        try {

            $id = $request->id;





            $val = DB::table('sys_crm_ps_service_table')->where('id', $id)->first();
            return json_encode($val);
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function pstrackservicesubmit(Request $request)
    {
        try {

            DB::beginTransaction();
            $attachment = "";
            if ($request->file('attachment') != "") {
                $file1 = $request->file('attachment');
                $attachment = md5(time()) . "attachment." . $file1->getclientoriginalextension();
                $file1->move('public/uploads/crm_amc_doc/', $attachment);
            }

            DB::table('sys_crm_ps_service_table')->where('id', $request->amc_id)->update(
                [
                    'date' => Carbon::createFromFormat('d/m/Y', $request->date)->format('Y-m-d'),
                    'contact_person' => $request->contact_person,
                    'mobile' => $request->mobile,
                    'location_of_work' => $request->location_of_work,
                    'scope_of_work' => '',
                    'service_date' => Carbon::createFromFormat('d/m/Y', $request->service_date)->format('Y-m-d'),
                    'service_time' => $request->service_time,
                    'engineer' => implode(',', $request->engineer),
                    'status' => 1,
                    'updated_by' => Auth::user()->id,
                    'updated_at' => Carbon::now('+04:00'),
                ]
            );




            $scope_of_work = "";
            $work = [];
            foreach ($request->scope_of_work as $sw) {
                if ($sw != "") {
                    if ($scope_of_work == "") {
                        $scope_of_work = $sw;
                    } else {
                        $scope_of_work .= '$' . $sw;
                    }
                    $work[] = [
                        'service_id' => $request->amc_id,
                        'work' => $sw,
                    ];
                }
            }

            if (count($work)) {
                DB::table('sys_crm_ps_service_table_scope_of_work')->insert($work);
            }

            if ($attachment != "") {
                DB::table('sys_crm_ps_service_table')->where('id', $request->amc_id)->update(
                    ['attachment' => $attachment,]
                );
            }

            DB::commit();
            Toastr::success('PS Updated Successfully', 'Success');
            //return redirect()->back();     
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }


    public function amctrackserviceedit(Request $request)
    {
        try {
            DB::table('sys_crm_ps_service_table')->where('id', $request->amcid1)->update(
                [

                    'cust_name' => $request->cust_name,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'contact_person' => $request->contact_person,
                    'amount' => $request->amount,
                    'sales_person' => $request->sales_person,
                    'mobile' => $request->mobile,
                    'scope_work' => $request->scope_work,
                    'service_date_time' => $request->service_date_time,
                    'source' => $request->source,
                    'engineer' => $request->engineer,
                    'status' => 1,

                ]
            );



            Toastr::success('AMC Updated Successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }


    public function deleteservice($id)
    {

        try {

            DB::table('sys_crm_ps_service_table')->where('id', $id)->delete();

            Toastr::success('Deleted Successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }





    public function amctrackadd(Request $request)
    {
        try {


            DB::table('sys_crm_amc_table')->insert(
                [
                    'contact_person' => $request->contact_person,
                    'invoice' => $request->invoice,
                    'date' => $request->date,
                    'deal_id' => $request->deal_id,
                    'description' => $request->description,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'amount' => $request->amount,
                    'sales_person' => $request->sales_person,
                    'cust_name' => $request->cust_name,
                    'status' => 1,
                    'company_id' => session('logged_session_data.company_id'),




                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                ]
            );



            Toastr::success('AMC Added Successfully', 'Success');
            //return redirect()->back();     
            return redirect('crm-amc-list-req');
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }



    /* End  Geo     */

    public function amclist(Request $request)
    {
        try {
            $ctrl_amc_id = "";
            $ctrl_date = "";

            // $amc_query = SysCrmDeals::select('sys_crm_deals.*','sys_crm_amc.from_date','sys_crm_amc.to_date','sys_crm_amc.remarks','sys_crm_amc.id as amcid')
            // ->join('sys_crm_quote_items','sys_crm_quote_items.deal_id','sys_crm_deals.id')
            // ->leftjoin('sys_crm_amc','sys_crm_amc.deal_id','sys_crm_deals.id')
            // ->wherein('sys_crm_deals.stage',[4])
            // ->wherein('sys_crm_quote_items.product_id',[9976,10465,10497]);

            $amc_query = SysCrmAmc::select('sys_crm_amc.*');

            if (Auth::user()->role_id == 1) {
            } else {
                $amc_query->where('owner', Auth::user()->id);
            }
            if ($_POST) {
                if ($request->amc_id != "") {
                    $amc_query->where('id', $request->amc_id);
                    $ctrl_amc_id = $request->amc_id;
                }
                if ($request->to_date != "") {
                    $amc_query->where('to_date', $request->to_date);
                    $ctrl_date = $request->to_date;
                }
            }
            $support = $amc_query->orderby('sys_crm_amc.id', 'desc')->get();

            return view('backEnd.amc.DealAmcList', compact('support', 'ctrl_amc_id', 'ctrl_date'));
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }
    public function amcedit(Request $request)
    {
        try {
            DB::table('sys_crm_amc')->where('id', $request->amcid)->update(
                [
                    'deal_id' => $request->deal_name,
                    'from_date' => $request->from_date,
                    'to_date' => $request->to_date,
                    'remarks' => $request->remarks,
                    'file' => $request->file,
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                ]
            );

            Toastr::success('AMC Updated Successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function supportview($id)
    {
        try {
            $support = SysCrmSupport::where('id', $id)->first();
            $support_activity = SysCrmSupportActivity::where('support_id', $id)->get();

            return view('backEnd.amc.DealSupport', compact('support', 'support_activity'));
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }
    public function supportactivity(Request $request)
    {
        try {
            $doc_file = "";
            if ($request->file('activitydoc') != "") {
                $file = $request->file('activitydoc');
                $doc_file = md5(time()) . "." . $file->getclientoriginalextension();
                $file->move('public/uploads/crm_deal_support_doc/', $doc_file);
                $doc_file = $doc_file;
            }
            DB::table('sys_crm_support_activity')->insert(
                [
                    'support_id' => $request->support_id,
                    'activity_date' => $request->activity_date,
                    'activity_from' => $request->activity_from,
                    'activity_to' => $request->activity_to,
                    'remarks' => $request->remarks,
                    'file' => $doc_file,
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                ]
            );

            /*SysCrmSupport::where('id',$request->support_id)
                ->update([
                    'status' => $request->status,
                    'updated_by' => Auth::user()->id,
                    'updated_at' => Carbon::now('+04:00'),
                ]);*/

            Toastr::success('Activity has been added successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function supportactivityclose(Request $request)
    {
        try {

            $supportdet = SysCrmSupport::where('id', $request->support_id)->first();

            $doc_file = "";
            if ($request->file('closingdoc') != "") {
                $file = $request->file('closingdoc');
                $doc_file = md5(time()) . "." . $file->getclientoriginalextension();
                $file->move('public/uploads/crm_deal_support_doc/', $doc_file);
                $doc_file = $doc_file;
            }

            DB::table('sys_crm_support')->where('id', $request->support_id)->update(
                [
                    'close_remarks' => $request->remarks,
                    'closingdoc' => $doc_file,
                    'status' => 3,
                    'close_by' => Auth::user()->id,
                    'close_at' => Carbon::now('+04:00'),
                ]
            );

            $sp = explode(",", $supportdet->support_person_id);
            foreach ($sp as $s) {
                $support = SysHelper::get_user_detail($s);
                $body = "<br />";
                $body .= "The service task '.$request->support_id.' status has been updated as closed.<br />";
                $body .= "<a href='http://erp.venushrms.com/crm-deal-support/" . $request->support_id . "/view' target='_blank'><b>View Service Task</b></a><br /><br />";
                SysHelper::notificationMail($support->full_name, $body, $support->email, 'Service task ' . $request->support_id . ' has been updated');
            }

            $support2 = SysHelper::get_user_detail($sp[0]);
            $sales = SysHelper::get_user_detail($supportdet->sales_person_id);
            $body = "<br />";
            $body .= "The service task '.$request->support_id.' status has been updated as closed.<br />";
            $body .= "<a href='http://erp.venushrms.com/crm-deal-support/" . $request->support_id . "/view' target='_blank'><b>View Service Task</b></a><br /><br />";
            SysHelper::notificationMail($sales->full_name, $body, $sales->email, 'Service task ' . $request->support_id . ' has been updated');

            Toastr::success('Service Clossed Successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function supportdelete($id)
    {
        try {
            DB::table('sys_crm_support')->where('id', $id)->delete();
            DB::table('sys_crm_support_activity')->where('support_id', $id)->delete();
            Toastr::success('Service Deleted Successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }


    public function psservicerequestadd(Request $request)
    {


        try {
            $attachment = "";
            if ($request->file('attachment') != "") {
                $file1 = $request->file('attachment');
                $attachment = md5(time()) . "attachment." . $file1->getclientoriginalextension();
                $file1->move('public/uploads/crm_amc_doc/', $attachment);
            }

            $deal_det_for_serv = SysCrmDeals::where('code', $request->add_deal_id)->where('company_id', session('logged_session_data.company_id'))->first();


            if ($deal_det_for_serv) {
                $sales_person = $deal_det_for_serv->owner;
            } else {
                Toastr::error('Please Enter valid deal', 'Failed');
                return redirect()->back();
            }




            $record_id = DB::table('sys_crm_ps_service_table')->insertGetId([
                'doc_number' => SysHelper::get_new_code('sys_crm_ps_service_table', 'PR', 'doc_number'),
                'deal_id' => SysHelper::get_dealid_from_code($request->add_deal_id),
                'date' => Carbon::createFromFormat('d/m/Y', $request->date)->format('Y-m-d'),
                'cust_name' => $request->add_cust_name,
                'contact_person' => $request->contact_person ?? null,
                'mobile' => $request->mobile ?? null,
                'location_of_work' => $request->location_of_work ?? null,
                'status' => 1,
                'service_date' => Carbon::createFromFormat('d/m/Y', $request->service_date)->format('Y-m-d'),
                'service_time' => $request->service_time,
                'engineer' => implode(',', $request->add_engineer),
                'sales_person' => $sales_person,
                'company_id' => session('logged_session_data.company_id'),
                'created_by' => Auth::user()->id,
                'created_at' => Carbon::now('+04:00'),
            ]);




            $scope_of_work = "";
            $work = [];
            foreach ($request->scope_of_work as $sw) {
                if ($sw != "") {
                    if ($scope_of_work == "") {
                        $scope_of_work = $sw;
                    } else {
                        $scope_of_work .= '$' . $sw;
                    }
                    $work[] = [
                        'service_id' => $record_id,
                        'work' => $sw,
                    ];
                }
            }

            if (count($work)) {
                DB::table('sys_crm_ps_service_table_scope_of_work')->insert($work);
            }

            if ($attachment != "") {
                DB::table('sys_crm_ps_service_table')->where('id', $record_id)->update(
                    ['attachment' => $attachment,]
                );
            }

            Toastr::success('PS Request Added Successfully', 'Success');

            return redirect('crm-ps-service-list-req/' . $record_id);
        } catch (\Throwable $th) {
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

        $amc_list = SysCrmPSServiceTable::with(['custname', 'deal_code', 'ownername'])
            ->where('status', 0)
            ->when(session('logged_session_data.company_id') != 1, function ($query) {
                $query->where('company_id', session('logged_session_data.company_id'));
            })

            ->where(function ($query) use ($q, $formattedDate) {

                // Search query
                if ($q) {
                    $query->where(function ($qsub) use ($q) {
                        $qsub->where('doc_number', 'like', "%{$q}%")
                            ->orWhereHas('custname', function ($q1) use ($q) {
                                $q1->where('name', 'like', "%{$q}%");
                            })
                            ->orWhereHas('deal_code', function ($q2) use ($q) {
                                $q2->where('code', 'like', "%{$q}%");
                            })
                            ->orWhereHas('ownername', function ($q3) use ($q) {
                                $q3->where('full_name', 'like', "%{$q}%");
                            });
                    });
                }

                // Date filter
                if ($formattedDate) {
                    $query->orWhereDate('date', $formattedDate);
                }
            })
            ->orderby('id', 'desc')->get();




        return response()->json($amc_list);
    }


    public function searchReq(Request $request)
    {
        $q = $request->get('query');
        $formattedDate = null;
        if (preg_match('/\d{2}[\/\-]\d{2}[\/\-]\d{4}/', $q)) {
            $normalized = str_replace('/', '-', $q);
            $formattedDate = date('Y-m-d', strtotime($normalized));
        }

        $amc_list = SysCrmPSServiceTable::with(['custname', 'deal_code', 'ownername'])
            ->wherein('status', [1, 2])
             ->when(session('logged_session_data.company_id') != 1, function ($query) {
                    $query->where('company_id', session('logged_session_data.company_id'));
            })
            ->where(function ($query) use ($q, $formattedDate) {

                // Search query
                if ($q) {
                    $query->where(function ($qsub) use ($q) {
                        $qsub->where('doc_number', 'like', "%{$q}%")
                            ->orWhereHas('custname', function ($q1) use ($q) {
                                $q1->where('name', 'like', "%{$q}%");
                            })
                            ->orWhereHas('deal_code', function ($q2) use ($q) {
                                $q2->where('code', 'like', "%{$q}%");
                            })
                            ->orWhereHas('ownername', function ($q3) use ($q) {
                                $q3->where('full_name', 'like', "%{$q}%");
                            });
                    });
                }

                // Date filter
                if ($formattedDate) {
                    $query->orWhereDate('date', $formattedDate);
                }
            })
            ->orderByRaw("FIELD(status, 1, 2)")->orderBy(DB::raw("DATE_FORMAT(service_date, '%H:%i')"), 'asc')->orderBy(DB::raw("DATE_FORMAT(service_date, '%d-%m-%Y')"), 'asc')->get();




        return response()->json($amc_list);
    }
}

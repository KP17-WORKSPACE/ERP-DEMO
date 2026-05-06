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

class SysCrmAmcController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
    }

    public function crmamclist(Request $request, $id = null)
    {
        try {
            $ctrl_amc_id = "";
            $ctrl_date = "";
            $ctrl_date2 = "";
            $ctrl_customer_name = "";
            $ctrl_validity = "";
            $today = now()->toDateString();

            $customer = SysHelper::get_customer_list_deal_lead();
            $salesperson = SysHelper::get_sales_persons();
            $engineer_list = SysHelper::get_engineer_list();
            if ($_POST) {

                // $amcdata_query = SysCrmAmcTable::where('company_id', session('logged_session_data.company_id'))->where('is_auto', 1);
                if (session('logged_session_data.company_id') == 1) {
                    $amcdata_query = SysCrmAmcTable::where('is_auto', 1);
                } else {
                    $amcdata_query = SysCrmAmcTable::where('company_id', session('logged_session_data.company_id'))->where('is_auto', 1);
                }


                if ($request->search_amc_id != "") {
                    $amcdata_query->where('sys_crm_amc_table.doc_number', $request->search_amc_id);
                    $ctrl_amc_id = $request->search_amc_id;
                }
                if ($request->search_from_date != "") {
                    //$amcdata_query->whereRaw("DATE_FORMAT(sys_crm_amc_table.start_date, '%Y-%m-%d') < '" . date('Y-m-d', strtotime($request->search_from_date)) . "'");
                    $amcdata_query->whereRaw("DATE_FORMAT(sys_crm_amc_table.end_date, '%Y-%m-%d') > '" . date('Y-m-d', strtotime($request->search_from_date)) . "'");
                    //$amcdata_query->where('sys_crm_amc_table.end_date',$request->search_from_date);
                    $ctrl_date = Carbon::createFromFormat('d/m/Y', $request->search_from_date)->format('Y-m-d');
                }
                if ($request->search_to_date != "") {
                    $amcdata_query->whereRaw("DATE_FORMAT(sys_crm_amc_table.end_date, '%Y-%m-%d') < '" . date('Y-m-d', strtotime($request->search_to_date)) . "'");
                    //$amcdata_query->where('sys_crm_amc_table.end_date',$request->search_to_date);
                    $ctrl_date2 = Carbon::createFromFormat('d/m/Y', $request->search_to_date)->format('Y-m-d');
                }
                if ($request->search_customer_name != "") {
                    $amcdata_query->where('sys_crm_amc_table.cust_name', $request->search_customer_name);
                    $ctrl_customer_name = $request->search_customer_name;
                }

                if ($request->filled('validity')) {
                    $ctrl_validity = (int) $request->validity;

                    $amcdata_query->where(function ($query) use ($ctrl_validity) {
                        if ($ctrl_validity === 0) {
                            // ACTIVE
                            $query->where(function ($sub) {
                                $sub->where('sys_crm_amc_table.is_expired', 0)
                                    ->whereDate('sys_crm_amc_table.end_date', '>=', now()->toDateString());
                            });
                        } elseif ($ctrl_validity === 1) {
                            // EXPIRED
                            $query->where(function ($sub) {
                                $sub->where('sys_crm_amc_table.is_expired', 1)
                                    ->orWhereDate('sys_crm_amc_table.end_date', '<', now()->toDateString());
                            });
                        }
                    });
                } else {
                    $ctrl_validity = '';
                }



                $amcdata = $amcdata_query->orderby('end_date', 'asc')->orderByRaw("CASE WHEN is_expired = 1 OR end_date < ? THEN 0 ELSE 1 END", [$today])
                    ->orderBy('end_date', 'desc')->get();

                $custid = $amcdata->pluck('cust_name');
            } else {

                if (session('logged_session_data.company_id') == 1) {
                    $amcdata = SysCrmAmcTable::where('is_auto', 1)
                        ->orderByRaw("CASE WHEN is_expired = 1 OR end_date < ? THEN 0 ELSE 1 END", [$today])
                        ->orderBy('end_date', 'desc')
                        ->get();
                } else {
                    $amcdata = SysCrmAmcTable::where('company_id', session('logged_session_data.company_id'))
                        ->where('is_auto', 1)
                        ->orderByRaw("CASE WHEN is_expired = 1 OR end_date < ? THEN 0 ELSE 1 END", [$today])
                        ->orderBy('end_date', 'desc')
                        ->get();
                }




                $custid = $amcdata->pluck('cust_name');
            }

            $active_id = $id;
            $selectedAMC = [];



            $base_company_list = SysCompany::orderby('sort_id', 'asc')->get();

            if ($id) {
                $selectedAMC = $this->crmamcdata($id);
            } else {
                $firstRecord = $amcdata->first();
                if ($firstRecord) {
                    $active_id = $firstRecord->id;
                    $selectedAMC = $this->crmamcdata($firstRecord->id);
                }
            }


            $location_list = SysCustSuppl::select('id', 'address', 'address2')->wherein('id', $custid)->get();
            return view('backEnd.amc.DealAmcList', compact('amcdata', 'customer', 'salesperson', 'engineer_list', 'location_list', 'ctrl_amc_id', 'ctrl_date', 'ctrl_customer_name', 'ctrl_date2', 'ctrl_validity', 'selectedAMC', 'active_id', 'base_company_list'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    public function crmamcdata($id)
    {


        try {

            $amcdata = SysCrmAmcTable::where('id', $id)->first();
            $service_request = SysCrmAmcTableServiceRequest::where('amc_id', $amcdata->id)->get();
            if (count($service_request) > 0) {
                $service_request_work = DB::table('sys_crm_amc_table_service_request_scope_of_work')->wherein('service_id', $service_request->pluck('id'))->get();
            } else {
                $service_request_work = [];
            }

            return compact('amcdata', 'service_request', 'service_request_work');
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function crmamcdetail($id)
    {


        try {
            $amcdata = SysCrmAmcTable::where('id', $id)->first();
            $service_request = SysCrmAmcTableServiceRequest::where('amc_id', $amcdata->id)->get();
            if (count($service_request) > 0) {
                $service_request_work = DB::table('sys_crm_amc_table_service_request_scope_of_work')->wherein('service_id', $service_request->pluck('id'))->get();
            } else {
                $service_request_work = [];
            }

            return view('backEnd.amc.DealAmcDetail', compact('amcdata', 'service_request', 'service_request_work'));
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function amccustomerdetails(Request $request)
    {
        try {
            $ret = SysCustSuppl::select('id', 'customer_salutation', 'first_name', 'last_name', 'code', 'name', 'address', 'address2', 'city', 'mobile')->where('id', $request->id)->get();
            return json_encode(array('data' => $ret));
        } catch (\Throwable $th) {
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }


    public function addservicerequest(Request $request)
    {
        try {
            DB::beginTransaction();

            DB::table('sys_crm_amc_table')->where('id', $request->amc_id)
                ->update([
                    'status' => 2,
                ]);

            $record = SysCrmAmcTable::find($request->amc_id);


            $amcid = DB::table('sys_crm_amc_table')->insertGetId(
                [
                    'doc_number' => SysHelper::get_new_code('sys_crm_amc_table', 'AM', 'doc_number'),
                    'deal_id' => $record->deal_id ?? null,
                    'date' => $record->date,
                    'cust_name' => $record->cust_name,
                    'contact_person' => $record->contact_person,
                    'mobile_no' => $record->mobile_no,
                    'start_date' => $record->start_date,
                    'end_date' => $record->end_date,
                    //'invoice' => $request->invoice,
                    'amount' => $record->amount,
                    'sales_person' => $record->sales_person,
                    //'description' => $request->description,
                    'status' => 2,
                    'is_auto' => 0,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00')->format('Y-m-d H:i:s'),
                    'company_id' => session('logged_session_data.company_id'),
                ]
            );


            $scope_of_work2 = "";
            $work2 = [];
            foreach ($request->scope_of_work as $sw2) {
                if ($sw2 != "") {
                    if ($scope_of_work2 == "") {
                        $scope_of_work2 = $sw2;
                    } else {
                        $scope_of_work2 .= '$' . $sw2;
                    }
                    $work2[] = [
                        'amc_id' => $amcid,
                        'work' => $sw2,
                    ];
                }
            }

            if (count($work2)) {
                DB::table('sys_crm_amc_table_service_scope_of_work')->insert($work2);
            }


            $service_date = Carbon::createFromFormat('d/m/Y', $request->service_date)->format('Y-m-d');

            DB::table('sys_crm_amc_table_service_request')->insertGetId(
                [
                    'amc_id' => $amcid,
                    'location_of_work' => $request->location_of_work,
                    'scope_of_work' => $scope_of_work2,
                    'service_date' => $service_date,
                    'service_time' => $request->service_time,
                    'source' => $request->source,
                    'service_engineer' => implode(',', $request->service_engineer),
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                ]
            );

            $service_id = DB::table('sys_crm_amc_table_service_request')->insertGetId(
                [
                    'amc_id' => $request->amc_id,
                    'location_of_work' => $request->location_of_work,
                    'scope_of_work' => "",
                    'service_date' => $service_date,
                    'service_time' => $request->service_time,
                    'source' => $request->source,
                    'service_engineer' => implode(',', $request->service_engineer),
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
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
                        'service_id' => $service_id,
                        'work' => $sw,
                    ];
                }
            }

            if (count($work)) {
                DB::table('sys_crm_amc_table_service_request_scope_of_work')->insert($work);
            }
            Toastr::success('AMC Request has been added successfully', 'Success');
            DB::commit();
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function addamc(Request $request)
    {


        if ($request->has('base_company')) {
            $base_company = $request->base_company;
        } else {
            $base_company = session('logged_session_data.company_id');
        }


        try {
            $id = DB::table('sys_crm_amc_table')->insertGetId(
                [
                    'doc_number' => SysHelper::get_new_code_lead('sys_crm_amc_table', 'AM', 'doc_number', $base_company),
                    'deal_id' => SysHelper::get_dealid_from_code($request->deal_id),
                    'date' => Carbon::createFromFormat('d/m/Y', $request->date)->format('Y-m-d'),
                    'cust_name' => $request->cust_name,
                    'contact_person' => $request->contact_person,
                    'mobile_no' => $request->mobile_no,
                    'start_date' => Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d'),
                    'end_date' => Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d'),
                    'invoice' => $request->invoice,
                    'amount' => $request->amount,
                    'sales_person' => $request->sales_person,
                    'description' => $request->description,
                    'status' => 1,
                    'is_auto' => 1,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00')->format('Y-m-d H:i:s'),
                    'company_id' => $base_company,
                ]
            );
            Toastr::success('AMC has been added successfully', 'Success');
            return redirect('crm-amc-list/' . $id);
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function crmamcedit(Request $request)
    {
        try {
            $ret = SysCrmAmcTable::select('sys_crm_amc_table.*', 'd.code')
                ->leftjoin('sys_crm_deals as d', 'd.id', 'sys_crm_amc_table.deal_id')
                ->where('sys_crm_amc_table.id', $request->id)->get();
            return json_encode(array('data' => $ret));
        } catch (\Throwable $th) {
            $ret = $th;
            return json_encode(array('data' => $ret));
        }
    }

    public function crmamcupdate(Request $request)
    {


        try {


            $is_expired = null;
            $reason = "";


            if ($request->filled('amc_status')) {
                // Field exists and is not empty
                if ($request->amc_status === 'expired') {
                    $is_expired = 1;
                    $reason = $request->expired_comment ?? "";
                } elseif ($request->amc_status === 'renew') {
                    $is_expired = 0;
                    $reason = $request->renewal_comment ?? "";
                }
            } else {
                // Field missing or empty
                $is_expired = null; // or keep previous value
            }





            DB::table('sys_crm_amc_table')->where('id', $request->amcid_edit)->update(
                [
                    'deal_id' => SysHelper::get_dealid_from_code_without_company($request->deal_id),
                    'date' => Carbon::createFromFormat('d/m/Y', $request->date)->format('Y-m-d'),
                    'cust_name' => $request->cust_name,
                    'contact_person' => $request->contact_person,
                    'mobile_no' => $request->mobile_no,
                    'start_date' => Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d'),
                    'end_date' => Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d'),
                    'invoice' => $request->invoice,
                    'amount' => $request->amount,
                    'sales_person' => $request->sales_person,
                    'description' => $request->description,
                    'updated_by' => Auth::user()->id,
                    'is_expired' => $is_expired,
                    'comment' => $reason,
                    'updated_at' => Carbon::now('+04:00')->format('Y-m-d H:i:s'),
                ]
            );

            if ($request->filled('base_company')) {

                $base_company = $request->base_company;
                $amc_record = SysCrmAmcTable::find($request->amcid_edit);

                if ($amc_record->company_id != $base_company) {
                    $amc_record->doc_number =  SysHelper::get_new_code_lead('sys_crm_amc_table', 'AM', 'doc_number', $base_company);
                    $amc_record->company_id = $base_company;
                    $amc_record->save();
                }
            }



            Toastr::success('AMC has been updated successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function crmamcdeactivate($id)
    {
        try {
            SysCrmAmcTable::where('id', $id)->update(['is_delete' => 1]);
            Toastr::success('AMC has been Deactivated successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function crmamcactivate($id)
    {
        try {
            SysCrmAmcTable::where('id', $id)->update(['is_delete' => 0]);
            Toastr::success('AMC has been Deactivated successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function servicerequestlist(Request $request, $id = null)
    {
        try {
            $ctrl_amc_id = "";
            $ctrl_from_date = "";
            $ctrl_to_date = "";
            $ctrl_customer_name = "";
            $ctrl_service_enginer = "";
            $ctrl_search_status = "";

            SysHelper::amc_set_completed();


            $customer = SysCrmAmcTable::select('cust.id', 'cust.code', 'cust.name')
                ->join('sys_cust_suppl as cust', 'cust.id', 'sys_crm_amc_table.cust_name')
                ->when(session('logged_session_data.company_id') != 1, function ($query) {
                    $query->where('sys_crm_amc_table.company_id', session('logged_session_data.company_id'));
                })
                ->distinct()->get();

            $customer2 = SysHelper::get_customer_list_deal_lead();

            $salesperson = SysHelper::get_sales_persons();
            $engineer_list = SysHelper::get_engineer_list();


            if ($_POST) {
                $amcdata_query = SysCrmAmcTable::select('sys_crm_amc_table.id', 'sys_crm_amc_table.doc_number', 'sys_crm_amc_table.cust_name', 'sys_crm_amc_table.contact_person', 'sys_crm_amc_table.mobile_no', 'sys_crm_amc_table.date', 'ser.service_engineer', 'ser.location_of_work', 'ser.scope_of_work', 'ser.service_date', 'ser.service_time', 'ser.source', 'st.full_name', 'ser.attachment', 'sys_crm_amc_table.status', 'is_delete', 'de.code')
                    ->join('sys_crm_amc_table_service_request as ser', 'ser.amc_id', 'sys_crm_amc_table.id')
                    ->leftjoin('sys_crm_deals as de', 'de.id', 'sys_crm_amc_table.deal_id')
                    ->leftjoin('sm_staffs as st', 'st.user_id', 'ser.service_engineer')
                    ->when(session('logged_session_data.company_id') != 1, function ($query) {
                        $query->where('sys_crm_amc_table.company_id', session('logged_session_data.company_id'));
                    })
                    ->wherein('sys_crm_amc_table.status', [2, 3, 5])->where('sys_crm_amc_table.is_auto', 0);

                if ($request->search_amc_id != "") {
                    $amcdata_query->where('sys_crm_amc_table.doc_number', $request->search_amc_id);
                    $ctrl_amc_id = $request->search_amc_id;
                }
                if ($request->search_customer_name != "") {
                    $amcdata_query->where('sys_crm_amc_table.cust_name', $request->search_customer_name);
                    $ctrl_customer_name = $request->search_customer_name;
                }
                if ($request->search_service_enginer != "") {

                    if ($request->search_service_enginer == "NA") {
                        $amcdata_query->whereNull('ser.service_engineer');
                    } else {
                        $amcdata_query->where('ser.service_engineer', $request->search_service_enginer);
                    }
                    $ctrl_service_enginer = $request->search_service_enginer;
                }
                if ($request->search_from_date != "") {
                    $amcdata_query->whereRaw("DATE_FORMAT(ser.service_date, '%Y-%m-%d') >= '" . date('Y-m-d', strtotime($request->search_from_date)) . "'");
                    $ctrl_from_date = Carbon::createFromFormat('d/m/Y', $request->search_from_date)->format('Y-m-d');
                }
                if ($request->search_to_date != "") {
                    $amcdata_query->whereRaw("DATE_FORMAT(ser.service_date, '%Y-%m-%d') <= '" . date('Y-m-d', strtotime($request->search_to_date)) . "'");
                    $ctrl_to_date = Carbon::createFromFormat('d/m/Y', $request->search_to_date)->format('Y-m-d');
                }
                if ($request->search_status != "") {
                    $amcdata_query->wherein('sys_crm_amc_table.status', [$request->search_status]);
                    $ctrl_search_status = $request->search_status;
                }


                $amcdata = $amcdata_query->orderby('status', 'asc')->orderby('date', 'desc')->get();

                $amc_work = SysCrmAmcTableServiceScopeofWork::wherein('amc_id', $amcdata->pluck('id'))->get();

                $amcdata_id = $amcdata->pluck('id');
                $amc_comments = SysCrmAmcTableServiceComments::select('sys_crm_amc_table_service_comments.*', 'st.full_name', 'w.work')
                    ->join('sys_crm_amc_table_service_scope_of_work as w', 'w.id', 'sys_crm_amc_table_service_comments.work_id')
                    ->leftjoin('sm_staffs as st', 'st.user_id', 'sys_crm_amc_table_service_comments.engineer_id')
                    ->wherein('sys_crm_amc_table_service_comments.amc_id', $amcdata_id)
                    ->get();
                $custid = $amcdata->pluck('cust_name');
            } else {
                $amcdata = SysCrmAmcTable::select('sys_crm_amc_table.id', 'sys_crm_amc_table.doc_number', 'sys_crm_amc_table.cust_name', 'sys_crm_amc_table.contact_person', 'sys_crm_amc_table.mobile_no', 'sys_crm_amc_table.date', 'ser.service_engineer', 'ser.location_of_work', 'ser.scope_of_work', 'ser.service_date', 'ser.service_time', 'ser.source', 'st.full_name', 'ser.attachment', 'sys_crm_amc_table.status', 'is_delete', 'de.code')
                    ->join('sys_crm_amc_table_service_request as ser', 'ser.amc_id', 'sys_crm_amc_table.id')
                    ->leftjoin('sys_crm_deals as de', 'de.id', 'sys_crm_amc_table.deal_id')
                    ->leftjoin('sm_staffs as st', 'st.user_id', 'ser.service_engineer')
                    ->when(session('logged_session_data.company_id') != 1, function ($query) {
                        $query->where('sys_crm_amc_table.company_id', session('logged_session_data.company_id'));
                    })
                    ->wherein('sys_crm_amc_table.status', [2, 3, 5])->where('sys_crm_amc_table.is_auto', 0)
                    ->orderby('sys_crm_amc_table.status', 'asc')->orderby('sys_crm_amc_table.date', 'desc')->get();



                $amc_work = SysCrmAmcTableServiceScopeofWork::wherein('amc_id', $amcdata->pluck('id'))->get();

                $amcdata_id = $amcdata->pluck('id');
                $amc_comments = SysCrmAmcTableServiceComments::select('sys_crm_amc_table_service_comments.*', 'st.full_name', 'w.work')
                    ->join('sys_crm_amc_table_service_scope_of_work as w', 'w.id', 'sys_crm_amc_table_service_comments.work_id')
                    ->leftjoin('sm_staffs as st', 'st.user_id', 'sys_crm_amc_table_service_comments.engineer_id')
                    ->wherein('sys_crm_amc_table_service_comments.amc_id', $amcdata_id)
                    ->get();
                $custid = $amcdata->pluck('cust_name');
            }

            $staff = SmStaff::select('user_id', 'full_name')->get();
            $location_list = SysCustSuppl::select('id', 'address', 'address2')->wherein('id', $custid)->get();

            $active_id = $id;
            $selectedAMC = [];




            if ($id) {
                $selectedAMC = $this->servicerequestdata($id);
            } else {
                $firstRecord = $amcdata->first();
                if ($firstRecord) {
                    $active_id = $firstRecord->id;
                    $selectedAMC = $this->servicerequestdata($firstRecord->id);
                }
            }




            //return $location_list;
            return view('backEnd.amc.DealAmcServiceRequestList', compact('amcdata', 'customer', 'customer2', 'salesperson', 'engineer_list', 'location_list', 'ctrl_amc_id', 'ctrl_from_date', 'ctrl_to_date', 'ctrl_customer_name', 'ctrl_service_enginer', 'amc_comments', 'staff', 'ctrl_search_status', 'amc_work', 'selectedAMC', 'active_id'));
        } catch (\Throwable $th) {
            return $th;
        }
    }


    public function servicerequestdata($id)
    {
        try {
            $amcdata = SysCrmAmcTable::select('sys_crm_amc_table.id', 'sys_crm_amc_table.deal_id', 'sys_crm_amc_table.doc_number', 'sys_crm_amc_table.cust_name', 'sys_crm_amc_table.contact_person', 'sys_crm_amc_table.mobile_no', 'sys_crm_amc_table.date', 'ser.service_engineer', 'ser.location_of_work', 'ser.scope_of_work', 'ser.service_date', 'ser.service_time', 'ser.source', 'st.full_name', 'ser.attachment', 'sys_crm_amc_table.status', 'is_delete')
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
    public function servicerequestdetail($id)
    {
        try {
            $amcdata = SysCrmAmcTable::select('sys_crm_amc_table.id', 'sys_crm_amc_table.deal_id', 'sys_crm_amc_table.doc_number', 'sys_crm_amc_table.cust_name', 'sys_crm_amc_table.contact_person', 'sys_crm_amc_table.mobile_no', 'sys_crm_amc_table.date', 'ser.service_engineer', 'ser.location_of_work', 'ser.scope_of_work', 'ser.service_date', 'ser.service_time', 'ser.source', 'st.full_name', 'ser.attachment', 'sys_crm_amc_table.status', 'is_delete')
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

            // return compact('amcdata', 'amc_comments', 'amc_work', 'staff');
            return view('backEnd.amc.DealAmcServiceRequestDetail', compact('amcdata', 'amc_comments', 'amc_work', 'staff'));
        } catch (\Throwable $th) {
            return $th;
        }
    }


    public function servicerequestlistadd(Request $request)
    {


        try {
            DB::beginTransaction();
            $attachment = "";
            if ($request->file('attachment') != "") {
                $file1 = $request->file('attachment');
                $attachment = md5(time()) . "attachment." . $file1->getclientoriginalextension();
                $file1->move('public/uploads/crm_amc_doc/', $attachment);
                $attachment = $attachment;
            }

            $amcid = DB::table('sys_crm_amc_table')->insertGetId(
                [
                    'doc_number' => SysHelper::get_new_code('sys_crm_amc_table', 'AM', 'doc_number'),
                    'deal_id' => SysHelper::get_dealid_from_code($request->deal_id),
                    'date' => Carbon::createFromFormat('d/m/Y', $request->date)->format('Y-m-d'),
                    'cust_name' => $request->cust_name,
                    'contact_person' => $request->contact_person,
                    'mobile_no' => $request->mobile_no,
                    'start_date' => date('Y-m-d'),
                    'end_date' => date('Y-m-d'),
                    //'invoice' => $request->invoice,
                    'amount' => 0,
                    'sales_person' => 0,
                    //'description' => $request->description,
                    'status' => 2,
                    'is_auto' => 0,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00')->format('Y-m-d H:i:s'),
                    'company_id' => session('logged_session_data.company_id'),
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
                        'amc_id' => $amcid,
                        'work' => $sw,
                    ];
                }
            }

            if (count($work)) {
                DB::table('sys_crm_amc_table_service_scope_of_work')->insert($work);
            }

            DB::table('sys_crm_amc_table_service_request')->insert(
                [
                    'amc_id' => $amcid,
                    'location_of_work' => $request->location_of_work,
                    'scope_of_work' => $scope_of_work,
                    'service_date' => Carbon::createFromFormat('d/m/Y', $request->service_date)->format('Y-m-d'),
                    'service_time' => $request->service_time,
                    'source' => $request->source,
                    'service_engineer' => implode(',', $request->service_engineer),
                    'attachment' => $attachment,
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                ]
            );
            DB::commit();
            Toastr::success('AMC Service Request has been added successfully', 'Success');
            return redirect('crm-amc-service-request-list/' . $amcid);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function servicerequestedit(Request $request)
    {
        try {
            $ret = SysCrmAmcTable::with('deal_code:id,code')->select('sys_crm_amc_table.*', 'ser.location_of_work', 'ser.scope_of_work', 'ser.service_date', 'ser.service_time', 'ser.source', 'ser.service_engineer')
                ->leftjoin('sys_crm_amc_table_service_request as ser', 'ser.amc_id', 'sys_crm_amc_table.id')
                ->where('sys_crm_amc_table.id', $request->id)->get();
            return json_encode(array('data' => $ret));
        } catch (\Throwable $th) {
            $ret = $th;
            return json_encode(array('data' => $ret));
        }
    }

    public function servicerequestwork(Request $request)
    {
        try {
            $ret = SysCrmAmcTableServiceScopeofWork::where('amc_id', $request->id)->get();
            return json_encode(array('data' => $ret));
        } catch (\Throwable $th) {
            $ret = $th;
            return json_encode(array('data' => $ret));
        }
    }

    public function servicerequestupdate(Request $request)
    {


        try {
            DB::beginTransaction();
            $attachment = "";
            if ($request->file('attachment') != "") {
                $file1 = $request->file('attachment');
                $attachment = md5(time()) . "attachment." . $file1->getclientoriginalextension();
                $file1->move('public/uploads/crm_amc_doc/', $attachment);
                $attachment = $attachment;
            }
            DB::table('sys_crm_amc_table')->where('id', $request->amcid_edit)->update(
                [
                    'deal_id' => SysHelper::get_dealid_from_code($request->deal_id),
                    'date' => Carbon::createFromFormat('d/m/Y', $request->date)->format('Y-m-d'),
                    'cust_name' => $request->cust_name,
                    'contact_person' => $request->contact_person,
                    'mobile_no' => $request->mobile_no,
                    'status' => $request->status_edit,
                    'updated_by' => Auth::user()->id,
                    'updated_at' => Carbon::now('+04:00')->format('Y-m-d H:i:s'),
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
                }
            }


            SysCrmAmcTableServiceScopeofWork::where('amc_id', $request->amcid_edit)->delete();

            if (count($request->scope_of_work) > 0) {
                for ($i = 0; $i < count($request->scope_of_work); $i++) {
                    if ($request->scope_of_work[$i] != "") {
                        SysCrmAmcTableServiceScopeofWork::insert([
                            'work' => $request->scope_of_work[$i],
                            'amc_id' => $request->amcid_edit,
                            'updated_at' => Carbon::now('+04:00'),
                        ]);
                    }
                }
            }

            DB::table('sys_crm_amc_table_service_request')->where('amc_id', $request->amcid_edit)->update(
                [
                    'location_of_work' => $request->location_of_work,
                    'scope_of_work' => $scope_of_work,
                    'service_date' => Carbon::createFromFormat('d/m/Y', $request->service_date)->format('Y-m-d'),
                    'service_time' => $request->service_time,
                    'source' => $request->source,
                    'service_engineer' => implode(',', $request->service_engineer),
                    'updated_by' => Auth::user()->id,
                    'updated_at' => Carbon::now('+04:00'),
                ]
            );
            if ($attachment != "") {
                DB::table('sys_crm_amc_table_service_request')->where('amc_id', $request->amcid_edit)->update(
                    ['attachment' => $attachment,]
                );
            }
            DB::commit();
            Toastr::success('AMC Service Request has been updated successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    public function servicerequestdeactivate($id)
    {
        try {
            SysCrmAmcTable::where('id', $id)->update(['is_delete' => 1]);
            Toastr::success('AMC has been Deactivated successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function servicerequestactivate($id)
    {
        try {
            SysCrmAmcTable::where('id', $id)->update(['is_delete' => 0]);
            Toastr::success('AMC has been Deactivated successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    //Dashboard Codes
    public function servicerequestcomments(Request $request)
    {
        try {
            SysCrmAmcTableServiceComments::insert([
                'amc_id' => $request->amc_id,
                'comments' => $request->comments,
                'engineer_id' => Auth::user()->id,
                'work_date' => $request->work_date,
                'work_time_from' => $request->work_time_from,
                'work_time_to' => $request->work_time_to,
                'status' => $request->status,
                'work_id' => $request->work_id,
                'created_by' => Auth::user()->id,
                'created_at' => Carbon::now('+04:00'),
            ]);

            if ($request->status == 2) {
                SysCrmAmcTableServiceRequest::where('id', $request->work_id)->update(['status' => $request->status]);
            }
            //SysCrmAmcTableServiceScopeofWork::where('id',$request->work_id)->update(['status' => $request->status]);

            //$ret = SysCrmAmcTableServiceComments::select('sys_crm_amc_table_service_comments.*','st.full_name','w.work')
            //->join('sys_crm_amc_table_service_scope_of_work as w' ,'w.id','sys_crm_amc_table_service_comments.work_id')
            //->leftjoin('sm_staffs as st','st.user_id','sys_crm_amc_table_service_comments.engineer_id')
            //->where('sys_crm_amc_table_service_comments.amc_id', $request->amc_id)->get();


            $ret = SysCrmAmcTableServiceComments::select('sys_crm_amc_table_service_comments.*', 'st.full_name', 'w.work')
                ->join('sys_crm_amc_table_service_scope_of_work as w', 'w.id', 'sys_crm_amc_table_service_comments.work_id')
                ->leftjoin('sm_staffs as st', 'st.user_id', 'sys_crm_amc_table_service_comments.engineer_id')
                ->where('sys_crm_amc_table_service_comments.amc_id', $request->amc_id)->get();

            return json_encode(array('data' => $ret));
        } catch (\Throwable $th) {
            $ret = $th;
            return json_encode(array('data' => $ret));
        }
    }

    public function servicerequest_get_comments(Request $request)
    {
        try {
            $ret = SysCrmAmcTableServiceComments::select('sys_crm_amc_table_service_comments.*', 'st.full_name', 'w.work')
                ->join('sys_crm_amc_table_service_scope_of_work as w', 'w.id', 'sys_crm_amc_table_service_comments.work_id')
                ->leftjoin('sm_staffs as st', 'st.user_id', 'sys_crm_amc_table_service_comments.engineer_id')
                ->where('sys_crm_amc_table_service_comments.amc_id', $request->id)->get();
            return json_encode(array('data' => $ret));
        } catch (\Throwable $th) {
            $ret = $th;
            return json_encode(array('data' => $ret));
        }
    }

    //Dashboard Codes






    //GEO
    public function form()
    {
        try {
            $currency = SysCurrencySettings::select('id', 'code')->get();
            $company = SysCompany::find(session('logged_session_data.company_id'));

            $staff_query = SmStaff::select('user_id', 'full_name');
            if (Auth::user()->role_id != 1) {
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
            $staff_query->where('active_status', 1);
            $staff = $staff_query->get();

            $vendors_query = SysCustSuppl::select('id', 'code', 'name')->where('catid', 1); // 1 customers, 2 suppliers
            if (session('logged_session_data.company_id') == 3) { //magnus
                $vendors_query->where('company_id', 3);
            } else if (session('logged_session_data.company_id') == 11) { //USA
                $vendors_query->where('company_id', 11);
            } else {
                $vendors_query->where('company_id', '!=', 3);
                $vendors_query->where('company_id', '!=', 11);
            }

            if (Auth::user()->role_id != 1) {
                $sales_person = DB::table('sys_cust_suppl_assign')->select('cust_supp_id')->where('user_id', Auth::user()->id)->get();
                if (count($sales_person) > 0) {
                    foreach ($sales_person as $spid) {
                        $sp[] = $spid->cust_supp_id;
                    }
                    $vendors_query->wherein('id', $sp);
                } else {
                    $vendors_query->where('id', 0);
                }
            }

            $vendors = $vendors_query->orderby('name', 'asc')->get();
            $leads = SysCrmLeads::where('status', '!=', 0)->get();
            $brand = SysBrand::all();
            $country = SysCountries::select('id', 'name')->get();

            return view('backEnd.crm.AMCForm', compact('currency', 'vendors', 'company', 'staff', 'leads', 'brand', 'country'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function add2(Request $request)
    {
        $tags = "";
        if ($request->tags != "") {
            $tags = implode(",", $request->tags);
        }

        $doc_file = "";
        if ($request->file('file') != "") {
            $files = $request->file('file');
            for ($i = 0; $i < count($files); $i++) {
                $file1 = $files[$i];
                $doc_file = md5(time()) . "_amc_" . $i . "." . $file1->getclientoriginalextension();
                $file1->move('public/uploads/crm_amc_doc/', $doc_file);
                $lpo[] = $doc_file;
            }
            $doc_file = implode("|", $lpo);
        }
        if ($request->deal_id == '') {
            $deal_id = 0;
        }

        DB::beginTransaction();
        try {
            $flag = SysCrmAmc::where([
                ['from_date', date('Y-m-d', strtotime($request->from_date))],
                ['to_date', date('Y-m-d', strtotime($request->to_date))],
                ['deal_id', $request->deal_id],
                ['cust_id', $request->cust_id],
                ['owner', $request->owner],
                ['tags', $tags],
                ['created_by', Auth::user()->id],
                ['company_id', session('logged_session_data.company_id')]
            ])->first();
            if ($flag) {
                Toastr::success('AMC has been added successfully', 'Success');
                return redirect('crm-amc/show');
            } else {
                $ssi = new SysCrmAmc();
                $ssi->from_date = date('Y-m-d', strtotime($request->from_date));
                $ssi->to_date = date('Y-m-d', strtotime($request->to_date));
                $ssi->deal_id = $deal_id;
                $ssi->cust_id = $request->company_name;
                $ssi->cust_name = $request->cust_name;
                $ssi->cust_no = $request->cust_no;
                $ssi->cust_email = $request->cust_email;
                $ssi->address = $request->address;
                $ssi->country = $request->country;
                $ssi->owner = $request->owner;
                $ssi->file = $doc_file;
                $ssi->tags = $tags;
                $ssi->remarks = $request->remarks;
                $ssi->amc_value = $request->amc_value;
                $ssi->status = $request->status;
                $ssi->created_by = Auth::user()->id;
                $ssi->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
                $ssi->company_id = session('logged_session_data.company_id');
                $ssi->save();
                $ssi->toArray();

                //SysHelper::set_user_custsupp($request->owner,$request->company_name);

                $results = 0;
                DB::commit();

                if ($results == 0) {
                    Toastr::success('AMC has been added successfully', 'Success');
                    return redirect('crm-amc/' . $ssi->id . '/view');
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
    public function view($id)
    {
        try {
            $currency = SysCurrencySettings::select('id', 'code')->get();
            $company = SysCompany::find(session('logged_session_data.company_id'));
            $staff = SmStaff::select('user_id', 'full_name')->get();
            // $vendors_query = SysCustSuppl::select('id','code','name')->where('catid',1); // 1 customers, 2 suppliers
            // if(Auth::user()->role_id != 1){
            //     $vendors_query->where('created_by', Auth::user()->id);
            // }
            // $vendors = $vendors_query->get();
            $amc = SysCrmAmc::where('id', $id)->first();

            $comments = SysCrmAmcComments::where('amc_id', $id)->orderby('id', 'desc')->get();
            $support = SysCrmAmcUpdates::where('amc_id', $id)->orderby('id', 'desc')->get();
            $asign = SysCrmAmcAsign::where('amc_id', $id)->get();
            $support_staff_query = SmStaff::select('user_id', 'full_name')->where('active_status', 1)->wherein('department_id', [3]);
            if (Auth::user()->role_id != 1) {
                $support_staff_query->where('user_id', Auth::user()->id);
            }
            $support_staff = $support_staff_query->get();

            return view('backEnd.crm.AmcView', compact('currency', 'company', 'staff', 'amc', 'comments', 'support', 'support_staff', 'asign'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function edit($id)
    {
        try {
            $currency = SysCurrencySettings::select('id', 'code')->get();
            $company = SysCompany::find(session('logged_session_data.company_id'));

            $staff_query = SmStaff::select('user_id', 'full_name');
            $staff = $staff_query->get();

            $brand = SysBrand::all();
            $edit = SysCrmAmc::where('id', $id)->first();
            $country = SysCountries::select('id', 'name')->get();

            $vendors_query = SysCustSuppl::select('id', 'code', 'name')->where('catid', 1); // 1 customers, 2 suppliers
            if (session('logged_session_data.company_id') == 3) { //magnus
                $vendors_query->where('company_id', 3);
            } else if (session('logged_session_data.company_id') == 11) { //USA
                $vendors_query->where('company_id', 11);
            } else {
                $vendors_query->where('company_id', '!=', 3);
                $vendors_query->where('company_id', '!=', 11);
            }
            if (Auth::user()->role_id != 1) {
                /*$sales_person = DB::table('sys_cust_suppl_assign')->select('cust_supp_id')->where('user_id',Auth::user()->id)->get();
                 if(count($sales_person)>0){
                     foreach($sales_person as $spid){
                         $sp[]=$spid->cust_supp_id;
                     }
                     $vendors_query->wherein('id', $sp);
                 }else{$vendors_query->where('id', 0);}*/

                $vendors_query->where('id', $edit->cust_id);
            }

            $vendors = $vendors_query->orderby('name', 'asc')->get();


            return view('backEnd.crm.AMCForm', compact('currency', 'vendors', 'company', 'staff', 'edit', 'brand', 'country'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function update(Request $request, $id)
    {
        try {
            $tags = "";
            if ($request->tags != "") {
                $tags = implode(",", $request->tags);
            }
            $doc_file = "";
            if ($request->file('file') != "") {
                $files = $request->file('file');
                for ($i = 0; $i < count($files); $i++) {
                    $file1 = $files[$i];
                    $doc_file = md5(time()) . "_amc_" . $i . "." . $file1->getclientoriginalextension();
                    $file1->move('public/uploads/crm_amc_doc/', $doc_file);
                    $lpo[] = $doc_file;
                }
                $doc_file = implode("|", $lpo);
            }
            if ($request->deal_id == '') {
                $deal_id = 0;
            }

            $ssi = SysCrmAmc::find($id);
            $ssi->from_date = date('Y-m-d', strtotime($request->from_date));
            $ssi->to_date = date('Y-m-d', strtotime($request->to_date));
            $ssi->deal_id = $deal_id;
            $ssi->cust_id = $request->company_name;
            $ssi->cust_name = $request->cust_name;
            $ssi->cust_no = $request->cust_no;
            $ssi->cust_email = $request->cust_email;
            $ssi->address = $request->address;
            $ssi->country = $request->country;
            $ssi->owner = $request->owner;
            if ($doc_file != "") {
                $ssi->file = $doc_file;
            }
            $ssi->tags = $tags;
            $ssi->remarks = $request->remarks;
            $ssi->amc_value = $request->amc_value;
            $ssi->status = $request->status;
            $ssi->updated_by = Auth::user()->id;
            $ssi->updated_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
            $ssi->company_id = session('logged_session_data.company_id');
            $results = $ssi->update();

            //SysHelper::set_user_custsupp($request->owner,$request->company_name);

            if ($results) {
                Toastr::success('AMC has been updated successfully', 'Success');
                return redirect('crm-amc/' . $id . '/view');
            } else {
                Toastr::error('Something went wrong, please try again', 'Failed');
                return redirect()->back();
            }
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }



    public function commentsadd(Request $request)
    {
        try {
            $doc_file = "";
            if ($request->file('commentsdoc') != "") {
                $file = $request->file('commentsdoc');
                $doc_file = md5(time()) . "." . $file->getclientoriginalextension();
                $file->move('public/uploads/crm_amc_doc/', $doc_file);
                $doc_file = $doc_file;
            }
            DB::table('sys_crm_amc_comments')->insert(
                [
                    'amc_id' => $request->commentsid,
                    'comments' => $request->comments,
                    'commentsdoc' => $doc_file,
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                ]
            );

            Toastr::success('Comments has been added successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function commentsdelete($id)
    {
        try {
            DB::table('sys_crm_amc_comments')->where('id', $id)->delete();
            Toastr::success('Comments has been deleted successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function supportupdate(Request $request)
    {
        try {
            $doc_file = "";
            if ($request->file('commentsdoc') != "") {
                $file = $request->file('commentsdoc');
                $doc_file = md5(time()) . "." . $file->getclientoriginalextension();
                $file->move('public/uploads/crm_amc_doc/', $doc_file);
                $doc_file = $doc_file;
            }

            for ($i = 0; $i < count($request->support_person_id); $i++) {
                $supportperson[] = $request->support_person_id[$i];
            }

            DB::table('sys_crm_amc_updates')->insert(
                [
                    'amc_id' => $request->amc_id,
                    'support_type' => $request->support_type,
                    'support_date' => $request->support_date,
                    'support_time_from' => $request->support_time_from,
                    'support_time_to' => $request->support_time_to,
                    'support_person_id' => implode(",", $supportperson),
                    'comments' => $request->comments,
                    'commentsdoc' => $doc_file,
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                ]
            );

            Toastr::success('Support has been added successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function asignstaff(Request $request)
    {
        try {
            DB::table('sys_crm_amc_asign')->where('amc_id', $request->staff_amc_id)->delete();

            for ($i = 0; $i < count($request->user_id); $i++) {
                DB::table('sys_crm_amc_asign')->insert(
                    [
                        'amc_id' => $request->staff_amc_id,
                        'user_id' => $request->user_id[$i],
                        'status' => 1,
                    ]
                );
            }
            Toastr::success('Staff Assigned Successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function amc(Request $request)
    {
        try {
            $check = SysCrmAmc::where('deal_id', $request->deal_name)->where('created_by', Auth::user()->id)->count();
            if ($check == 0) {
                $ret_support_id = DB::table('sys_crm_amc')->insertGetId(
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
            } else {
                Toastr::error('AMC date already added, please check', 'Failed');
                return redirect()->back();
            }
            Toastr::success('AMC Added Successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }



    /* Start Geo     */









    public function amctracklist(Request $request)
    {
        try {
            $ctrl_amc_id = "";
            $ctrl_date = "";
            $sales_person = "";

            // $amc_query = SysCrmDeals::select('sys_crm_deals.*','sys_crm_amc.from_date','sys_crm_amc.to_date','sys_crm_amc.remarks','sys_crm_amc.id as amcid')
            // ->join('sys_crm_quote_items','sys_crm_quote_items.deal_id','sys_crm_deals.id')
            // ->leftjoin('sys_crm_amc','sys_crm_amc.deal_id','sys_crm_deals.id')
            // ->wherein('sys_crm_deals.stage',[4])
            // ->wherein('sys_crm_quote_items.product_id',[9976,10465,10497]);

            $amc_query = SysCrmAmcTable::select('sys_crm_amc_table.*')->where('status', 0);

            if (Auth::user()->role_id == 1) {
            } else {
                $amc_query->where('owner', Auth::user()->id);
            }
            if ($_POST) {
                if ($request->sales_person != "") {
                    $amc_query->where('sales_person', $request->sales_person);
                    $sales_person = $request->sales_person;
                }
                // if ($request->to_date != "") {
                //     $amc_query->where('to_date', $request->to_date);
                //     $ctrl_date=$request->to_date;
                // }
            }
            $support = $amc_query->orderby('sys_crm_amc_table.id', 'desc')->get();

            return view('backEnd.crm.DealAmcTrackList', compact('support', 'sales_person', 'ctrl_amc_id', 'ctrl_date'));
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }


    public function amctrackreqlist(Request $request)
    {
        try {
            $ctrl_amc_id = "";
            $ctrl_date = "";
            $sales_person = "";

            // $amc_query = SysCrmDeals::select('sys_crm_deals.*','sys_crm_amc.from_date','sys_crm_amc.to_date','sys_crm_amc.remarks','sys_crm_amc.id as amcid')
            // ->join('sys_crm_quote_items','sys_crm_quote_items.deal_id','sys_crm_deals.id')
            // ->leftjoin('sys_crm_amc','sys_crm_amc.deal_id','sys_crm_deals.id')
            // ->wherein('sys_crm_deals.stage',[4])
            // ->wherein('sys_crm_quote_items.product_id',[9976,10465,10497]);

            $amc_query = SysCrmAmcTable::select('sys_crm_amc_table.*')->where('status', 1);

            if (Auth::user()->role_id == 1) {
            } else {
                $amc_query->where('owner', Auth::user()->id);
            }
            if ($_POST) {
                if ($request->sales_person != "") {
                    $amc_query->where('sales_person', $request->sales_person);
                    $sales_person = $request->sales_person;
                }
                // if ($request->to_date != "") {
                //     $amc_query->where('to_date', $request->to_date);
                //     $ctrl_date=$request->to_date;
                // }
            }
            $support = $amc_query->orderby('sys_crm_amc_table.id', 'desc')->get();

            return view('backEnd.crm.DealAmcTrackReqList', compact('support', 'sales_person', 'ctrl_amc_id', 'ctrl_date'));
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function amc_engineerlist(Request $request)
    {
        $id = $request->id;
        $englist = SysHelper::get_engineer_list();

        foreach ($englist as $list) {
            if ($list->user_id == $id)
                echo '<option  select value="' . $list->user_id . '" >' . $list->full_name . '</option>';
        }
    }

    public function amctrackid(Request $request)
    {
        try {

            $id = $request->id;
            $val = SysCrmAmcTable::select('sys_crm_amc_table.*', 'cu.name as cu_name')->join('sys_cust_suppl as cu', 'cu.id', 'sys_crm_amc_table.cust_name')->where('sys_crm_amc_table.id', $id)->first();
            return json_encode($val);
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function amctracksubmit(Request $request)
    {
        try {


            DB::table('sys_crm_amc_table')->where('id', $request->amcid)->update(
                [
                    'contact_person' => $request->contact_person,
                    'mobile' => $request->mobile,
                    'scope_work' => $request->scope_work,
                    'service_date_time' => $request->service_date_time,
                    'source' => $request->source,
                    'status' => 1,
                    'req' => 1,
                    'engineer' => $request->engineer,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                ]
            );



            Toastr::success('AMC Updated Successfully', 'Success');
            //return redirect()->back();     
            return redirect('crm-amc-list-req');
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }


    public function amctrackedit(Request $request)
    {




        try {
            DB::table('sys_crm_amc_table')->where('id', $request->amcid1)->update(
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





    public function delete($id)
    {

        try {

            DB::table('sys_crm_amc_table')->where('id', $id)->delete();

            Toastr::success('Deleted Successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }





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





            $val = DB::table('sys_crm_amc_service_table')->where('id', $id)->first();
            return json_encode($val);
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

        $amc_list = SysCrmAmcTable::with(['custname', 'deal_code', 'salesperson'])
            ->where('is_auto', 1)
             ->when(session('logged_session_data.company_id') != 1, function ($query) {
                    $query->where('sys_crm_amc_table.company_id', session('logged_session_data.company_id'));
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
                            ->orWhereHas('salesperson', function ($q3) use ($q) {
                                $q3->where('full_name', 'like', "%{$q}%");
                            });
                    });
                }

                // Date filter
                if ($formattedDate) {
                    $query->orWhereDate('date', $formattedDate);
                }
            })
            ->get()
            ->map(function ($item) {
                // Format currency using your helper
                $item->formatted_amount = \App\SysHelper::com_curr_format($item->amount, 2, '.', ',');

                // Optional: You can also add readable customer name etc.
                $item->cu_name = $item->custname->name ?? '';

                return $item;
            });



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

        $amc_list = SysCrmAmcTable::with(['custname', 'deal_code', 'salesperson'])
            ->where('is_auto', 0)
              ->when(session('logged_session_data.company_id') != 1, function ($query) {
                    $query->where('sys_crm_amc_table.company_id', session('logged_session_data.company_id'));
                })
            
            ->wherein('sys_crm_amc_table.status', [2, 3, 5])
            ->orderby('sys_crm_amc_table.status', 'asc')->orderby('sys_crm_amc_table.date', 'desc')
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
                            ->orWhereHas('salesperson', function ($q3) use ($q) {
                                $q3->where('full_name', 'like', "%{$q}%");
                            });
                    });
                }

                // Date filter
                if ($formattedDate) {
                    $query->orWhereDate('date', '=', $formattedDate);
                }
            })
            ->get();



        return response()->json($amc_list);
    }
}

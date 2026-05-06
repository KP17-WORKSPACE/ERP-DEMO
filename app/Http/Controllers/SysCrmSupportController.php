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

class SysCrmSupportController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
    }

    public function support(Request $request)
    {
        try {
            DB::beginTransaction();
            if ($request->deal_id == "") {
                $deal_id = 0;
            } else {
                $deal_id = $request->deal_id;
            }
            $check = SysCrmSupport::where('deal_id', $deal_id)->where('sales_person_id', $request->sales_person_id)
                ->where('support_person_id', $request->support_person_id)->where('support_date', SysHelper::normalizeToYmd($request->support_date))
                ->where('site_name', $request->site_name)->where('remarks', $request->remarks)->where('created_by', Auth::user()->id)->count();
            if ($check == 0) {

                if ($request->support_id == 0) {
                    $ret_support_id = DB::table('sys_crm_support')->insertGetId(
                        [
                            'doc_number' => SysHelper::get_new_code('sys_crm_support', 'PS', 'doc_number'),
                            'deal_id' => $deal_id,
                            'customer_id' => $request->customer_id,
                            'sales_person_id' => $request->sales_person_id,
                            'support_person_id' => '',
                            'support_date' => SysHelper::normalizeToYmd($request->support_date),
                            'time_from' => $request->time_from,
                            'time_to' => $request->time_to,
                            'site_name' => $request->site_name,
                            'file' => $request->file,
                            'status' => 1,
                            'created_by' => Auth::user()->id,
                            'created_at' => Carbon::now('+04:00'),
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
                                'support_id' => $ret_support_id,
                                'work' => $sw,
                            ];
                        }
                    }
                    if (count($work)) {
                        DB::table('sys_crm_support_work')->insert($work);
                    }
                    DB::table('sys_crm_support')->where('id', $ret_support_id)->update(['remarks' => $scope_of_work]);
                } else {
                    $ret_support_id = DB::table('sys_crm_support')->insertGetId(
                        [
                            'doc_number' => SysHelper::get_new_code('sys_crm_support', 'PS', 'doc_number'),
                            'deal_id' => $request->deal_id,
                            'customer_id' => $request->customer_id,
                            'sales_person_id' => $request->sales_person_id,
                            'support_person_id' => '',
                            'support_date' => SysHelper::normalizeToYmd($request->support_date),
                            'time_from' => $request->time_from,
                            'time_to' => $request->time_to,
                            'site_name' => $request->site_name,
                            'remarks' => $request->remarks,
                            'file' => $request->file,
                            'status' => 1,
                            'created_by' => Auth::user()->id,
                            'created_at' => Carbon::now('+04:00'),
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
                                'support_id' => $ret_support_id,
                                'work' => $sw,
                            ];
                        }
                    }
                    if (count($work)) {
                        DB::table('sys_crm_support_work')->insert($work);
                    }
                    DB::table('sys_crm_support')->where('id', $ret_support_id)->update(['remarks' => $scope_of_work]);
                }
            }
            DB::commit();
            Toastr::success('Service Added Successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function supportlist(Request $request, $id = null)
    {
        try {
            $ctrl_support_id = "";
            $ctrl_deal_id = "";
            $ctrl_customer_name = "";
            $ctrl_sales_person = "";
            $ctrl_from_date = "";
            $ctrl_to_date = "";
            $ctrl_status = "";
            //$sales_array=[87,83,62,27,105,26,36,53,88,85,41,101,25,91,32,34,1,33,103,94];
            //$support_array=[33,59,31,106,95,97,90,98,96,77,109];

            $sales_person = SmStaff::select('user_id', 'full_name')->where('active_status', 1)->orderby('full_name', 'asc')->get();
            $support_person = SmStaff::select('user_id', 'full_name')->where('active_status', 1)->orderby('full_name', 'asc')->get();

            $support_query = SysCrmSupport::select('sys_crm_support.*', 'c.name')
                ->leftjoin('sys_crm_deals as d', 'd.id', 'sys_crm_support.deal_id')
                ->leftjoin('sys_cust_suppl as c', 'c.id', 'sys_crm_support.customer_id')
                ->wherein('sys_crm_support.status', [1, 2, 3]);


            $staff_sales_query = SmStaff::select('user_id', 'full_name')->where('active_status', 1);
            $staff_support_query = SmStaff::select('user_id', 'full_name')->where('active_status', 1);
            if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 32) {
            } else {
                if (session('logged_session_data.department_id') == 2) { //2 Sales
                    $support_query->where('sales_person_id', Auth::user()->id);
                    $staff_sales_query->where('user_id', Auth::user()->id);
                }
                if (session('logged_session_data.department_id') == 3) { //3 Technical
                    $support_query->whereRaw("find_in_set('" . Auth::user()->id . "',support_person_id)");
                    //$staff_support_query->where('sys_crm_support.user_id',Auth::user()->id);
                }
            }
            if ($_POST) {
                if ($request->search_support_id != "") {
                    $support_query->where('sys_crm_support.doc_number', $request->search_support_id);
                    $ctrl_support_id = $request->search_support_id;
                }
                if ($request->search_deal_id != "") {
                    $support_query->where('sys_crm_support.deal_id', SysHelper::get_dealid_from_code($request->search_deal_id));
                    $ctrl_deal_id = $request->search_deal_id;
                }
                if ($request->search_customer_name != "") {
                    $support_query->where('sys_crm_support.customer_id', $request->search_customer_name);
                    $ctrl_customer_name = $request->search_customer_name;
                }
                if ($request->search_sales_person != "") {
                    $support_query->whereRaw("find_in_set($request->search_sales_person,sys_crm_support.support_person_id)");
                    $ctrl_sales_person = $request->search_sales_person;
                }
                if ($request->search_from_date != "" && $request->search_to_date == "") {
                    $request->search_from_date = Carbon::createFromFormat('d/m/Y', $request->search_from_date)->format('Y-m-d');
                    $support_query->whereRaw("DATE_FORMAT(sys_crm_support.support_date, '%Y-%m-%d') = '" . $request->search_from_date . "'");
                    $ctrl_from_date = $request->search_from_date;
                }
                if ($request->search_to_date != "" && $request->search_from_date == "") {
                    $request->search_to_date = Carbon::createFromFormat('d/m/Y', $request->search_to_date)->format('Y-m-d');
                    $support_query->whereRaw("DATE_FORMAT(sys_crm_support.support_date, '%Y-%m-%d') = '" . $request->search_to_date . "'");
                    $ctrl_to_date = $request->search_to_date;
                }
                if ($request->search_from_date != "" && $request->search_to_date != "") {
                    $request->search_to_date = Carbon::createFromFormat('d/m/Y', $request->search_to_date)->format('Y-m-d');
                    $request->search_from_date = Carbon::createFromFormat('d/m/Y', $request->search_from_date)->format('Y-m-d');

                    $support_query->whereRaw("DATE_FORMAT(sys_crm_support.support_date, '%Y-%m-%d') >= '" . $request->search_from_date . "'");
                    $support_query->whereRaw("DATE_FORMAT(sys_crm_support.support_date, '%Y-%m-%d') <= '" . $request->search_to_date . "'");
                    $ctrl_from_date = $request->search_from_date;
                    $ctrl_to_date = $request->search_to_date;
                }
                if ($request->search_status != "") {
                    $support_query->where('sys_crm_support.status', $request->search_status);
                    $ctrl_status = $request->search_status;
                }
            } else {
                //$support_query->where('sys_crm_support.status',1);
            }
            $staff = SmStaff::select('user_id', 'full_name')->get();

            if (session('logged_session_data.company_id') == 1) {
                $support = $support_query->orderby('sys_crm_support.id', 'desc')->get();
            } else {
                $support = $support_query->where('sys_crm_support.company_id', session('logged_session_data.company_id'))->orderby('sys_crm_support.id', 'desc')->get();
            }

            $staff_sales = $staff_sales_query->orderby('full_name', 'asc')->get();
            $staff_support = $staff_support_query->orderby('full_name', 'asc')->get();

            $salesperson = SysHelper::get_engineer_list();
            $customer = SysHelper::get_customer_list_deal_lead();





            $active_id = $id;
            $selectedSales = [];




            if ($id) {
                $selectedSales = $this->supportlistdata($id);
            } else {
                $firstRecord = $support->first();
                if ($firstRecord) {
                    $active_id = $firstRecord->id;
                    $selectedSales = $this->supportlistdata($firstRecord->id);
                }
            }

            return view('backEnd.crm.DealSupportList', compact('support', 'staff_sales', 'staff_support', 'sales_person', 'support_person', 'staff', 'salesperson', 'customer', 'ctrl_support_id', 'ctrl_deal_id', 'ctrl_customer_name', 'ctrl_sales_person', 'ctrl_from_date', 'ctrl_to_date', 'ctrl_status', 'active_id', 'selectedSales'));
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function supportupdate(Request $request)
    {

        try {

            DB::table('sys_crm_support')->where('id', $request->pre_sales_id)->update(
                [
                    'deal_id' => SysHelper::get_dealid_from_code($request->deal_id),
                    'contact_person' => $request->contact_person,
                    'mobile' => $request->mobile,
                    'support_person_id' => implode(',', $request->engineer),
                    'support_date' => Carbon::createFromFormat('d/m/Y', $request->service_date)->format('Y-m-d'),
                    'time_from' => $request->service_time,
                    'site_name' => $request->location_of_work,
                    'remarks' => implode(',', $request->scope_of_work),
                    'status' => 2,
                    'updated_by' => Auth::user()->id,
                    'updated_at' => Carbon::now('+04:00'),
                ]
            );



            $work = [];
            $scope_of_work = "";
            foreach ($request->scope_of_work as $sw) {
                if ($sw != "") {
                    if ($scope_of_work == "") {
                        $scope_of_work = $sw;
                    } else {
                        $scope_of_work .= '$' . $sw;
                    }
                    $work[] = [
                        'support_id' => $request->pre_sales_id,
                        'work' => $sw,
                    ];
                }
            }



            DB::table('sys_crm_support')->where('id', $request->pre_sales_id)->update(['remarks' => $scope_of_work]);

            if (count($work) > 0) {
                DB::table('sys_crm_support_work')->where('support_id', $request->pre_sales_id)->delete();
                DB::table('sys_crm_support_work')->insert($work);
            }



            Toastr::success('Service Updated Successfully', 'Success');
            return redirect('crm-deal-support-list/' . $request->pre_sales_id);
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
    public function supportlistview($id)
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
            return view('backEnd.crm.DealSupportDetail', compact('support', 'support_activity', 'support_work', 'eng'));
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }
    public function supportlistdelete($id)
    {
        try {
            db::beginTransaction();
            SysCrmSupport::where('id', $id)->update(['is_delete' => 1,]);
            db::commit();
            Toastr::success('Deleted successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            db::rollBack();
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }
    public function supportlistrestore($id)
    {
        try {
            db::beginTransaction();
            SysCrmSupport::where('id', $id)->update(['is_delete' => 0,]);
            db::commit();
            Toastr::success('Restore successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            db::rollBack();
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function supportlistrequestsubmit(Request $request)
    {

        try {
            DB::table('sys_crm_support')->where('id', $request->pre_sales_id)->update(
                [
                    'contact_person' => $request->contact_person,
                    'mobile' => $request->mobile,
                    'support_person_id' => implode(',', $request->engineer),
                    'support_date' => Carbon::createFromFormat('d/m/Y', $request->service_date)->format('Y-m-d'),
                    'time_from' => $request->service_time,
                    'site_name' => $request->location_of_work,
                    'remarks' => implode(',', $request->scope_of_work),
                    'status' => 2,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                    'company_id' => session('logged_session_data.company_id'),
                ]
            );

            $work = [];
            $scope_of_work = "";
            foreach ($request->scope_of_work as $sw) {
                if ($sw != "") {
                    if ($scope_of_work == "") {
                        $scope_of_work = $sw;
                    } else {
                        $scope_of_work .= '$' . $sw;
                    }
                    $work[] = [
                        'support_id' => $request->pre_sales_id,
                        'work' => $sw,
                    ];
                }
            }
            DB::table('sys_crm_support')->where('id', $request->pre_sales_id)->update(['remarks' => $scope_of_work]);

            if (count($work) > 0) {
                DB::table('sys_crm_support_work')->where('support_id', $request->pre_sales_id)->delete();
                DB::table('sys_crm_support_work')->insert($work);
            }

            Toastr::success('Pre-Sales Request Added successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function supportlistrequestsubmit2(Request $request)
    {
        try {
            $task = SysCrmSupport::find($request->pre_sales_id);
            $new = $task->replicate();
            $new->save();
            $newID = $new->id;

            $doc_file = "";
            if ($request->file('attachment') != "") {
                $file = $request->file('attachment');
                $doc_file = md5(time()) . "." . $file->getclientoriginalextension();
                $file->move('public/uploads/crm_deal_support_doc/', $doc_file);
                $doc_file = $doc_file;
            } else {
                $doc_file = $new->file;
            }

            DB::table('sys_crm_support')->where('id', $newID)->update(
                [
                    'contact_person' => $request->contact_person,
                    'mobile' => $request->mobile,
                    'support_person_id' => implode(',', $request->engineer),
                    'support_date' => $request->service_date,
                    'time_from' => $request->service_time,
                    'site_name' => $request->location_of_work,
                    'remarks' => implode(',', $request->scope_of_work),
                    'file' => $doc_file,
                    'status' => 2,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                    'company_id' => session('logged_session_data.company_id'),
                ]
            );

            $work = [];
            $scope_of_work = "";
            foreach ($request->scope_of_work as $sw) {
                if ($sw != "") {
                    if ($scope_of_work == "") {
                        $scope_of_work = $sw;
                    } else {
                        $scope_of_work .= '$' . $sw;
                    }
                    $work[] = [
                        'support_id' => $newID,
                        'work' => $sw,
                    ];
                }
            }
            DB::table('sys_crm_support')->where('id', $newID)->update(['remarks' => $scope_of_work]);

            if (count($work) > 0) {
                DB::table('sys_crm_support_work')->where('support_id', $newID)->delete();
                DB::table('sys_crm_support_work')->insert($work);
            }

            Toastr::success('Pre-Sales Request Added successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function supportlistrequestupdate(Request $request)
    {



        try {
            DB::table('sys_crm_support')->where('id', $request->pre_sales_id)->update(
                [
                    'contact_person' => $request->contact_person,
                    'mobile' => $request->mobile,
                    'support_person_id' => implode(',', $request->engineer),
                    'support_date' => Carbon::createFromFormat('d/m/Y', $request->service_date)->format('Y-m-d'),
                    'time_from' => $request->service_time,
                    'site_name' => $request->location_of_work,
                    'remarks' => implode(',', $request->scope_of_work),
                    'status' => $request->presales_status ?? 2,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                    'company_id' => session('logged_session_data.company_id'),
                ]
            );

            $work = [];
            $scope_of_work = "";
            foreach ($request->scope_of_work as $sw) {
                if ($sw != "") {
                    if ($scope_of_work == "") {
                        $scope_of_work = $sw;
                    } else {
                        $scope_of_work .= '$' . $sw;
                    }
                    $work[] = [
                        'support_id' => $request->pre_sales_id,
                        'work' => $sw,
                    ];
                }
            }
            DB::table('sys_crm_support')->where('id', $request->pre_sales_id)->update(['remarks' => $scope_of_work]);

            if (count($work) > 0) {
                DB::table('sys_crm_support_work')->where('support_id', $request->pre_sales_id)->delete();
                DB::table('sys_crm_support_work')->insert($work);
            }

            Toastr::success('Pre-Sales Request Updated successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function supportrequestedlist(Request $request, $id = null)
    {
        try {
            $ctrl_support_id = "";
            $ctrl_deal_id = "";
            $ctrl_customer_name = "";
            $ctrl_sales_person = "";
            $ctrl_from_date = "";
            $ctrl_to_date = "";
            $ctrl_status = "";
            //$sales_array=[87,83,62,27,105,26,36,53,88,85,41,101,25,91,32,34,1,33,103,94];
            //$support_array=[33,59,31,106,95,97,90,98,96,77,109];

            $sales_person = SmStaff::select('user_id', 'full_name')->where('active_status', 1)->orderby('full_name', 'asc')->get();
            $support_person = SmStaff::select('user_id', 'full_name')->where('active_status', 1)->orderby('full_name', 'asc')->get();

            $support_query = SysCrmSupport::select('sys_crm_support.*', 'c.name')
                ->leftjoin('sys_crm_deals as d', 'd.id', 'sys_crm_support.deal_id')
                ->leftjoin('sys_cust_suppl as c', 'c.id', 'sys_crm_support.customer_id')
                ->wherein('sys_crm_support.status', [1, 2, 3]);


            $customer_salesreq = SysCrmSupport::select('cust.id', 'cust.code', 'cust.name')
                ->join('sys_cust_suppl as cust', 'cust.id', 'sys_crm_support.customer_id')
                ->when(session('logged_session_data.company_id') != 1, function ($query) {
                    $query->where('sys_crm_support.company_id', session('logged_session_data.company_id'));
                })
                ->distinct()->get();



            $staff_sales_query = SmStaff::select('user_id', 'full_name')->where('active_status', 1);
            $staff_support_query = SmStaff::select('user_id', 'full_name')->where('active_status', 1);
            if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 32) {
            } else {
                if (session('logged_session_data.department_id') == 2) { //2 Sales
                    $support_query->where('sales_person_id', Auth::user()->id);
                    $staff_sales_query->where('user_id', Auth::user()->id);
                }
                if (session('logged_session_data.department_id') == 3) { //3 Technical
                    $support_query->whereRaw("find_in_set('" . Auth::user()->id . "',support_person_id)");
                    //$staff_support_query->where('sys_crm_support.user_id',Auth::user()->id);
                }
            }
            if ($_POST) {
                if ($request->search_support_id != "") {
                    $support_query->where('sys_crm_support.doc_number', $request->search_support_id);
                    $ctrl_support_id = $request->search_support_id;
                }
                if ($request->search_deal_id != "") {
                    $support_query->where('sys_crm_support.deal_id', SysHelper::get_dealid_from_code($request->search_deal_id));
                    $ctrl_deal_id = $request->search_deal_id;
                }
                if ($request->search_customer_name != "") {
                    $support_query->where('sys_crm_support.customer_id', $request->search_customer_name);
                    $ctrl_customer_name = $request->search_customer_name;
                }
                if ($request->search_sales_person != "") {
                    $support_query->whereRaw("find_in_set($request->search_sales_person,sys_crm_support.support_person_id)");
                    $ctrl_sales_person = $request->search_sales_person;
                }
                if ($request->search_from_date != "" && $request->search_to_date == "") {
                    $support_query->whereRaw("DATE_FORMAT(sys_crm_support.support_date, '%Y-%m-%d') = '" . $request->search_from_date . "'");
                    $ctrl_from_date = $request->search_from_date;
                }
                if ($request->search_to_date != "" && $request->search_from_date == "") {
                    $support_query->whereRaw("DATE_FORMAT(sys_crm_support.support_date, '%Y-%m-%d') = '" . $request->search_to_date . "'");
                    $ctrl_to_date = $request->search_to_date;
                }
                if ($request->search_from_date != "" && $request->search_to_date != "") {
                    $support_query->whereRaw("DATE_FORMAT(sys_crm_support.support_date, '%Y-%m-%d') >= '" . $request->search_from_date . "'");
                    $support_query->whereRaw("DATE_FORMAT(sys_crm_support.support_date, '%Y-%m-%d') <= '" . $request->search_to_date . "'");
                    $ctrl_from_date = $request->search_from_date;
                    $ctrl_to_date = $request->search_to_date;
                }
                if ($request->search_status != "") {
                    $support_query->where('sys_crm_support.status', $request->search_status);
                    $ctrl_status = $request->search_status;
                }
            } else {
                // $support_query->where('sys_crm_support.status', 2);
            }
            $staff = SmStaff::select('user_id', 'full_name')->get();
            if (session('logged_session_data.company_id') == 1) {
                $support = $support_query->orderby('sys_crm_support.id', 'desc')->get();
            } else {
                $support = $support_query->where('sys_crm_support.company_id', session('logged_session_data.company_id'))->orderby('sys_crm_support.id', 'desc')->get();
            }

            $staff_sales = $staff_sales_query->orderby('full_name', 'asc')->get();
            $staff_support = $staff_support_query->orderby('full_name', 'asc')->get();


            $salesperson = SysHelper::get_engineer_list();
            $customer = SysHelper::get_customer_list_deal_lead();
            $supportactivity = SysCrmSupportActivity::get();



            $active_id = $id;
            $selectedSales = [];




            if ($id) {
                $selectedSales = $this->supportlistdata($id);
            } else {
                $firstRecord = $support->first();
                if ($firstRecord) {
                    $active_id = $firstRecord->id;
                    $selectedSales = $this->supportlistdata($firstRecord->id);
                }
            }



            return view('backEnd.crm.DealSupportRequestList', compact('support', 'staff_sales', 'staff_support', 'sales_person', 'support_person', 'staff', 'salesperson', 'customer', 'ctrl_support_id', 'ctrl_deal_id', 'ctrl_customer_name', 'ctrl_sales_person', 'ctrl_from_date', 'ctrl_to_date', 'ctrl_status', 'supportactivity', 'customer_salesreq', 'active_id', 'selectedSales'));
        } catch (\Throwable $th) {
            return $th;
        }
    }



    public function supportviewdata($id)
    {
        try {

            $support = SysCrmSupport::query()
                ->leftJoin('sys_cust_suppl as c', 'c.id', '=', 'sys_crm_support.customer_id')
                ->where('sys_crm_support.id', $id)
                ->select('sys_crm_support.*', 'c.name as customer_name') // add required columns
                ->first();
            $support_activity = SysCrmSupportActivity::where('support_id', $id)->get();

            return compact('support', 'support_activity');
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function supportview($id)
    {
        try {
            $support = SysCrmSupport::query()
                ->leftJoin('sys_cust_suppl as c', 'c.id', '=', 'sys_crm_support.customer_id')
                ->where('sys_crm_support.id', $id)
                ->select('sys_crm_support.*', 'c.name as customer_name') // add required columns
                ->first();
            $support_activity = SysCrmSupportActivity::where('support_id', $id)->get();

            return view('backEnd.crm.DealSupport', compact('support', 'support_activity'));
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

    public function supportactivitycomments(Request $request)
    {
        try {
            $doc_file = "";
            DB::table('sys_crm_support_activity')->insert(
                [
                    'support_id' => $request->support_id,
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

            Toastr::success('Comments has been added successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function supportactivitycomments_add(Request $request)
    {
        try {
            SysCrmSupportComments::insert([
                'support_id' => $request->support_id,
                'comments' => $request->comments,
                'engineer_id' => Auth::user()->id,
                'work_date' => $request->work_date,
                'work_time_from' => $request->work_time_from,
                'work_time_to' => $request->work_time_to,
                'status' => $request->status,
                'created_by' => Auth::user()->id,
                'created_at' => Carbon::now('+04:00'),
            ]);

            if ($request->status == 2) {
                SysCrmSupport::where('id', $request->support_id)->update([
                    'status' => 3,
                    'close_by' => Auth::user()->id,
                    'close_at' => Carbon::now('+04:00'),
                    'close_remarks' => $request->comments,
                ]);
            }

            //$ret = SysCrmAmcTableServiceComments::select('sys_crm_amc_table_service_comments.*','st.full_name','w.work')
            //->join('sys_crm_amc_table_service_scope_of_work as w' ,'w.id','sys_crm_amc_table_service_comments.work_id')
            //->leftjoin('sm_staffs as st','st.user_id','sys_crm_amc_table_service_comments.engineer_id')
            //->where('sys_crm_amc_table_service_comments.amc_id', $request->amc_id)->get();

            $ret = DB::table('sys_crm_support_comments')->select('sys_crm_support_comments.*', 'st.full_name')
                ->leftjoin('sm_staffs as st', 'st.user_id', 'sys_crm_support_comments.engineer_id')
                ->where('sys_crm_support_comments.support_id', $request->support_id)->get();

            return json_encode(array('data' => $ret));
        } catch (\Throwable $th) {
            $ret = $th;
            return json_encode(array('data' => $ret));
        }
    }

    public function supportactivitycomments_view(Request $request)
    {
        try {
            $ret = DB::table('sys_crm_support_comments')->select('sys_crm_support_comments.*', 'st.full_name')
                ->leftjoin('sm_staffs as st', 'st.user_id', 'sys_crm_support_comments.engineer_id')
                ->where('sys_crm_support_comments.support_id', $request->id)->get();
            return json_encode(array('data' => $ret));
        } catch (\Throwable $th) {
            $ret = $th;
            return json_encode(array('data' => $ret));
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

    public function salerequestsubmit(Request $request)
    {

        try {


            $attachment = "";
            if ($request->file('attachment') != "") {
                $file1 = $request->file('attachment');
                $attachment = md5(time()) . "attachment." . $file1->getclientoriginalextension();
                $file1->move('public/uploads/crm_amc_doc/', $attachment);
            }



            $deal_det_for_serv = SysCrmDeals::where('code', $request->deal_id)->where('company_id', session('logged_session_data.company_id'))->first();

            if ($deal_det_for_serv) {
                $sales_person = $deal_det_for_serv->owner;
            } else {
                Toastr::error('Please Enter valid deal', 'Failed');
                return redirect()->back();
            }

            $ret_support_id = DB::table('sys_crm_support')->insertGetId(
                [
                    'doc_number' => SysHelper::get_new_code('sys_crm_support', 'PS', 'doc_number'),
                    'deal_id' => SysHelper::get_dealid_from_code($request->deal_id),
                    'customer_id' => $request->add_cust_name,
                    'sales_person_id' => $sales_person,
                    'support_person_id' => implode(',', $request->add_engineer),
                    'support_date' => Carbon::createFromFormat('d/m/Y', $request->service_date)->format('Y-m-d'),
                    'time_from' => $request->service_time ?? 0,
                    'site_name' => $request->add_site_name ?? 0,
                    'contact_person' => $request->contact_person ?? 0,
                    'mobile' => $request->mobile ?? 0,
                    'file' => $attachment,
                    'status' => 2,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                    'company_id' => session('logged_session_data.company_id'),
                ]
            );

            //    DB::table('sys_crm_support')->where('id',$request->pre_sales_id)->update(
            //     [
            //         'contact_person' => $request->contact_person,
            //         'mobile' => $request->mobile,
            //         'support_person_id' => implode(',', $request->engineer),
            //         'support_date' => $request->service_date,
            //         'time_from' => $request->service_time,
            //         'site_name' => $request->location_of_work,
            //         'remarks' => implode(',', $request->scope_of_work),
            //         'status' => 2,
            //         'created_by' => Auth::user()->id,
            //         'created_at' => Carbon::now('+04:00'),
            //         'company_id' => session('logged_session_data.company_id'),
            //     ]
            // );

            $work = [];
            $scope_of_work = "";
            foreach ($request->scope_of_work as $sw) {
                if ($sw != "") {
                    if ($scope_of_work == "") {
                        $scope_of_work = $sw;
                    } else {
                        $scope_of_work .= '$' . $sw;
                    }
                    $work[] = [
                        'support_id' => $ret_support_id,
                        'work' => $sw,
                    ];
                }
            }
            DB::table('sys_crm_support')->where('id', $ret_support_id)->update(['remarks' => $scope_of_work]);

            if (count($work) > 0) {
                DB::table('sys_crm_support_work')->where('support_id', $ret_support_id)->delete();
                DB::table('sys_crm_support_work')->insert($work);
            }

            Toastr::success('Pre-Sales Request Added successfully', 'Success');

            return redirect('crm-deal-support-requested-list/' . $ret_support_id);
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

        $amc_list = SysCrmSupport::select('sys_crm_support.*', 'c.name', 'd.code as deal_code')
            ->leftjoin('sys_crm_deals as d', 'd.id', 'sys_crm_support.deal_id')
            ->leftjoin('sys_cust_suppl as c', 'c.id', 'sys_crm_support.customer_id')
            ->wherein('sys_crm_support.status', [1, 2, 3])

            ->where(function ($query) use ($q, $formattedDate) {


                if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 32) {
                } else {
                    if (session('logged_session_data.department_id') == 2) { //2 Sales
                        $query->where('sales_person_id', Auth::user()->id);
                    }
                    if (session('logged_session_data.department_id') == 3) { //3 Technical
                        $query->whereRaw("find_in_set('" . Auth::user()->id . "',support_person_id)");
                    }
                }


                // Search query
                if ($q) {
                    $query->where(function ($qsub) use ($q) {
                        $qsub->where('sys_crm_support.doc_number', 'like', "%{$q}%")
                            ->orWhere('c.name', 'like', "%{$q}%")
                            ->orWhere('d.code', 'like', "%{$q}%")
                            ->orWhereHas('salesperson', function ($q3) use ($q) {
                                $q3->where('full_name', 'like', "%{$q}%");
                            });
                    });
                }

                // Date filter
                if ($formattedDate) {
                    $query->orWhereDate('support_date', $formattedDate);
                }
            })
            ->when(session('logged_session_data.company_id') != 1, function ($query) {
                $query->where('sys_crm_support.company_id', session('logged_session_data.company_id'));
            })
            ->orderby('sys_crm_support.id', 'desc')
            ->get();



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

        $amc_list = SysCrmSupport::select('sys_crm_support.*', 'c.name', 'd.code as deal_code')
            ->leftjoin('sys_crm_deals as d', 'd.id', 'sys_crm_support.deal_id')
            ->leftjoin('sys_cust_suppl as c', 'c.id', 'sys_crm_support.customer_id')
            ->wherein('sys_crm_support.status', [1, 2, 3])

            ->where(function ($query) use ($q, $formattedDate) {


                if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 32) {
                } else {
                    if (session('logged_session_data.department_id') == 2) { //2 Sales
                        $query->where('sales_person_id', Auth::user()->id);
                    }
                    if (session('logged_session_data.department_id') == 3) { //3 Technical
                        $query->whereRaw("find_in_set('" . Auth::user()->id . "',support_person_id)");
                    }
                }


                // Search query
                if ($q) {
                    $query->where(function ($qsub) use ($q) {
                        $qsub->where('sys_crm_support.doc_number', 'like', "%{$q}%")
                            ->orWhere('c.name', 'like', "%{$q}%")
                            ->orWhere('d.code', 'like', "%{$q}%")
                            ->orWhereHas('salesperson', function ($q3) use ($q) {
                                $q3->where('full_name', 'like', "%{$q}%");
                            });
                    });
                }

                // Date filter
                if ($formattedDate) {
                    $query->orWhereDate('support_date', $formattedDate);
                }
            })
            ->when(session('logged_session_data.company_id') != 1, function ($query) {
                $query->where('sys_crm_support.company_id', session('logged_session_data.company_id'));
            })
            ->orderby('sys_crm_support.id', 'desc')
            ->get();



        return response()->json($amc_list);
    }
}

<?php

namespace App\Http\Controllers;

use App\Role;
use App\User;
use App\SmStaff;
use App\SysCompany;
use App\SmBaseSetup;
use App\ApiBaseMethod;
use App\SmDesignation;
use App\SmLeaveRequest;
use App\SmHumanDepartment;
use App\SmStudentDocument;
use App\SmStudentTimeline;
use App\SmHrPayrollGenerate;
use App\SmItem;
use App\SysBrand;
use App\SysCrmSalesTarget;
use App\SysHelper;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\SmStaffJobDetail;
use App\SmStaffAttendanceLeaveConfiguration;
use App\SmStaffBankDetail;
use App\SmStaffEducationQualification;
use App\SmStaffProfessionalExperience;
use Illuminate\Support\Facades\Storage;
use App\SmStaffDocument;
use App\SysCountries;
use App\EndOfService;
use App\EndOfServiceNotice;
use App\EndOfServiceHandover;
use App\EndOfServiceAssetClearance;
use App\EndOfServiceAsset;
use App\EndOfServiceFinance;
use App\EndOfServiceFinalSettlement;
use App\EndOfServiceExitInterview;
use App\EndOfServiceApproval;
use App\EndOfServiceDocument;
use App\SysStates;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Schema;

class SmStaffController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
    }

    public function staffList(Request $request, $id = null)
    {

        try {

            $companyId = session()->get('logged_session_data.company_id');
            $q = SmStaff::with(['roles', 'maincompany', 'departments', 'designations', 'jobDetail'])

                ->where('delete_status', 1)
                ->when(Auth::check() && Auth::user()->role_id != 1, function ($x) {
                    $x->where('role_id', '!=', 1);
                });

            if (session('logged_session_data.company_id') != 1) {
                $q->where('company_id', session('logged_session_data.company_id'));
            }

            if ($request->filled('staff_no')) {
                $term = trim($request->input('staff_no'));
                $q->where(function ($x) use ($term) {
                    $x->where('staff_no', 'like', "%{$term}%")
                        ->orWhere('first_name', 'like', "%{$term}%")
                        ->orWhere('last_name', 'like', "%{$term}%")
                        ->orWhereRaw("CONCAT_WS(' ', first_name, last_name) LIKE ?", ["%{$term}%"])
                        ->orWhere('email', 'like', "%{$term}%")
                        ->orWhere('mobile', 'like', "%{$term}%");
                });
            }

            $staffs = $q->orderBy('id', 'desc')->get();
            $roles = Role::where('active_status', 1)->get();
            $company = SysCompany::select('id', 'company_name')->get();
            $active_id = null;

            if ($id) {
                $firstStaff = SmStaff::with(['roles', 'maincompany', 'departments', 'designations', 'jobDetail'])
                    ->where('company_id', $companyId)
                    ->where('id', $id)
                    ->first();
                $active_id = $id;
            } else if ($staffs->count() > 0) {
                $firstStaff = $staffs->first();
                $active_id = $firstStaff->id;
            } else {
                $firstStaff = null;
            }


            return view('backEnd.humanResource.staff_list', compact('staffs', 'roles', 'company', 'firstStaff', 'active_id'));
        } catch (\Throwable $e) {
            Toastr::error('Operation Failed', 'Failed');
            return back();
        }
    }

    public function staffAuth(Request $request)
    {
        try {
            $data = SmStaff::where('auth_status', 0)->orderby('auth_date', 'desc')->get();
            return view('backEnd.humanResource.staff_auth_list', compact('data'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function staffAuthApprove($id)
    {
        try {
            DB::table('sm_staffs')->where('user_id', Auth::user()->id)->where('auth_status', 0)->update(
                [
                    'auth_date' => Carbon::now('+04:00'),
                    'auth_status' => 1,
                ]
            );
            Toastr::success('Authentication Approved Successful', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function addStaff()
    {
        try {
            // Clear session data for fresh staff addition
            session()->forget(['staff_educations', 'staff_experiences', 'staff_banks']);

            $roles = Role::where('active_status', '=', '1')->orderBy('name', 'asc')->get();
            $company = SysCompany::select('id', 'company_name')->where('status', '=', '1')->get();
            $departments = SmHumanDepartment::where('active_status', '=', '1')->get();
            $designations = SmDesignation::where('active_status', '=', '1')->orderBy('title', 'asc')->get();

            $genders = SmBaseSetup::where('active_status', '=', '1')->where('base_group_id', '=', '1')->get();
            $staff = SmStaff::select('user_id', 'full_name')->where('active_status', 1)->get();

            $brand_list = SysBrand::select('id', 'title')->where('active_status', 1)->orderby('title')->get();
            $countries = SysCountries::all();
            $states = SysStates::all();

            return view('backEnd.humanResource.addStaff', compact('roles', 'departments', 'designations', 'genders', 'company', 'staff', 'brand_list', 'countries', 'states'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function staffStore(Request $request)
    {
        try {


            DB::beginTransaction();

            $login_check = DB::table('users')->where('username', $request->email)->get();

            if (count($login_check) > 0) {
                Toastr::success('Username / Email is already excist!', 'Warning');
                return redirect()->back();
            }
            $companyAccess = $request->input('company_access');
            if (is_string($companyAccess)) {
                $companyAccess = array_filter(array_map('trim', explode(',', $companyAccess)));
            }
            $job->company_access = $companyAccess ? array_values((array) $companyAccess) : null;

            // brands may come as array OR comma string
            $brands = $request->input('brands');
            if (is_string($brands)) {
                $brands = array_filter(array_map('trim', explode(',', $brands)));
            }
            $job->brand_ids = $brands ? array_values((array) $brands) : null;

            $user = new User();
            $user->role_id = $request->role_id;
            $user->username = $request->email;
            $user->email = $request->email;
            $user->full_name = $request->first_name . ' ' . $request->last_name;
            $user->password = Hash::make($request->password);
            $user->company_id = $request->company_id;
            $user->save();
            $user->toArray();

            // for upload staff photo
            $staff_photo = "";
            if ($request->file('staff_photo') != "") {
                $file = $request->file('staff_photo');
                $staff_photo = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('public/uploads/staff/', $staff_photo);
                $staff_photo = 'public/uploads/staff/' . $staff_photo;
            }

            $staff = new SmStaff();
            $staff->staff_no = SysHelper::get_new_staff_code();
            $staff->role_id = $request->role_id;
            $staff->company_id = 1; //$request->company_id
            $staff->company_access = $company_access;
            $staff->main_company = $request->main_company;
            $staff->type = $request->type;
            $staff->department_id = $request->department_id;
            $staff->designation_id = $request->designation_id;
            $staff->first_name = $request->first_name;
            $staff->last_name = $request->last_name;
            $staff->full_name = $request->first_name . ' ' . $request->last_name;
            $staff->email = $request->email;
            $staff->mobile = $request->mobile;
            $staff->emergency_mobile = $request->emergency_mobile;
            $staff->ext_no = $request->ext_no;
            $staff->gender_id = $request->gender_id;
            $staff->date_of_joining = SysHelper::normalizeToYmd($request->date_of_joining);
            $staff->staff_photo = $staff_photo;
            $staff->is_target = $request->is_target;
            $staff->brands = $brands;

            if ($request->is_target == 1) {
                $staff->revenue_target_weekly = !empty($request->revenue_target_weekly) ? $request->revenue_target_weekly : 0;
                $staff->revenue_target_monthly = !empty($request->revenue_target_monthly) ? $request->revenue_target_monthly : 0;
                $staff->revenue_target_quaterly = !empty($request->revenue_target_quaterly) ? $request->revenue_target_quaterly : 0;
                $staff->revenue_target_yearly = !empty($request->revenue_target_yearly) ? $request->revenue_target_yearly : 0;
                $staff->gp_target_weekly = !empty($request->gp_target_weekly) ? $request->gp_target_weekly : 0;
                $staff->gp_target_monthly = !empty($request->gp_target_monthly) ? $request->gp_target_monthly : 0;
                $staff->gp_target_quaterly = !empty($request->gp_target_quaterly) ? $request->gp_target_quaterly : 0;
                $staff->gp_target_yearly = !empty($request->gp_target_yearly) ? $request->gp_target_yearly : 0;
                $staff->target_month_from = !empty($request->target_month_from) ? $request->target_month_from . '-01' : null;

                if ($request->combind_user_id != "") {
                    $combind_user_id = implode(",", $request->combind_user_id) . ',' . $user->id;
                }
                $staff->combind_user_id = $combind_user_id;
            }

            $staff->user_id = $user->id;

            $staff->created_by = Auth()->user()->id;

            $results = $staff->save();

            if ($staff->is_target == 1) {
                SysCrmSalesTarget::insert([
                    'user_id' => $staff->user_id,
                    'target' => $request->revenue_target_monthly,
                    'target_month' => date('Y-m-01'),
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                    'company_id' => $request->main_company,
                    'company_access' => $company_access,
                    'type' => 1,
                    'revenue_target_weekly' => $request->revenue_target_weekly,
                    'revenue_target_monthly' => $request->revenue_target_monthly,
                    'revenue_target_quaterly' => $request->revenue_target_quaterly,
                    'revenue_target_yearly' => $request->revenue_target_yearly,
                    'gp_target_weekly' => $request->gp_target_weekly,
                    'gp_target_monthly' => $request->gp_target_monthly,
                    'gp_target_quaterly' => $request->gp_target_quaterly,
                    'gp_target_yearly' => $request->gp_target_yearly,
                    'target_month_from' => $request->target_month_from . '-01',
                    'combind_user_id' => $combind_user_id,
                ]);
            }

            DB::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }

        if ($results) {
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } else {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function editStaff($id)
    {
        try {
            $editData = SmStaff::find($id);
            $roles = Role::where('active_status', '=', '1')->orderBy('name', 'asc')->get();

            if ($editData->company_access == "") {
                $companyIds = [0];
            } else {
                $companyIds = explode(',', $editData->company_access);
            }

            $company = SysCompany::select('id', 'company_name')->where('status', '=', '1')
                ->orderByRaw("FIELD(id, " . implode(',', $companyIds) . ")")->get();
            $company1 = SysCompany::select('id', 'company_name')->where('status', '=', '1')->orderby('sort_id', 'asc')->get();

            $departments = SmHumanDepartment::where('active_status', '=', '1')->get();
            $designations = SmDesignation::where('active_status', '=', '1')->get();
            $marital_ststus = SmBaseSetup::where('active_status', '=', '1')->where('base_group_id', '=', '4')->get();
            $genders = SmBaseSetup::where('active_status', '=', '1')->where('base_group_id', '=', '1')->get();
            $staff = SmStaff::select('user_id', 'full_name')->where('active_status', 1)->get();
            $target = SysCrmSalesTarget::where('user_id', $editData->user_id)->where('target_month_from', '!=', null)->orderby('target_month_from', 'asc')->get();
            $brand_list = SysBrand::select('id', 'title')->where('active_status', 1)->orderby('title')->get();
            if ($editData->brands != "") {
                $selected_brands = array_map('intval', explode(',', $editData->brands));
            } else {
                $selected_brands = [0];
            }

            return view('backEnd.humanResource.editStaff', compact('editData', 'roles', 'departments', 'designations', 'marital_ststus', 'genders', 'company', 'company1', 'staff', 'target', 'brand_list', 'selected_brands'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function staffUpdate(Request $request)
    {
        try {
            DB::beginTransaction();
            $company_access = "";
            if ($request->company_access != "") {
                $company_access = implode(",", $request->company_access);
            }
            $combind_user_id = "";

            $brands = "";
            if ($request->brands != "") {
                $brands = implode(",", $request->brands);
            }
            // for update staff photo
            $staff_photos = "";
            if ($request->file('staff_photo') != "") {
                $photos = SmStaff::find($request->staff_id);
                if ($photos->staff_photo != '' && file_exists($photos->staff_photo)) {
                    unlink($photos->staff_photo);
                }
                $file = $request->file('staff_photo');
                $staff_photos = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('public/uploads/staff/', $staff_photos);
                $staff_photo = 'public/uploads/staff/' . $staff_photos;
            } else {
                $photos = SmStaff::find($request->staff_id);
                $staff_photo = $photos->staff_photo;
            }

            $staff = SmStaff::find($request->staff_id);
            $staff->role_id = $request->role_id;
            $staff->company_id = 1; //$request->company_id
            $staff->company_access = $company_access;
            $staff->main_company = $request->main_company;
            $staff->type = $request->type;
            $staff->department_id = $request->department_id;
            $staff->designation_id = $request->designation_id;
            $staff->first_name = $request->first_name;
            $staff->last_name = $request->last_name;
            $staff->full_name = $request->first_name . ' ' . $request->last_name;
            $staff->email = $request->email;
            $staff->mobile = $request->mobile;
            $staff->emergency_mobile = $request->emergency_mobile;
            $staff->ext_no = $request->ext_no;
            $staff->gender_id = $request->gender_id;
            $staff->date_of_joining = date('Y-m-d', strtotime($request->date_of_joining));

            if ($request->date_of_resign == "") {
                $staff->date_of_resign = NULL;
                $staff->active_status = 1;
            } else {
                $staff->date_of_resign = date('Y-m-d', strtotime($request->date_of_resign));
                $staff->active_status = 0;
            }

            $staff->staff_photo = $staff_photo;
            $staff->is_target = $request->is_target;
            $staff->brands = $brands;

            if ($request->is_target == 1) {
                $staff->revenue_target_weekly = !empty($request->revenue_target_weekly) ? $request->revenue_target_weekly : 0;
                $staff->revenue_target_monthly = !empty($request->revenue_target_monthly) ? $request->revenue_target_monthly : 0;
                $staff->revenue_target_quaterly = !empty($request->revenue_target_quaterly) ? $request->revenue_target_quaterly : 0;
                $staff->revenue_target_yearly = !empty($request->revenue_target_yearly) ? $request->revenue_target_yearly : 0;
                $staff->gp_target_weekly = !empty($request->gp_target_weekly) ? $request->gp_target_weekly : 0;
                $staff->gp_target_monthly = !empty($request->gp_target_monthly) ? $request->gp_target_monthly : 0;
                $staff->gp_target_quaterly = !empty($request->gp_target_quaterly) ? $request->gp_target_quaterly : 0;
                $staff->gp_target_yearly = !empty($request->gp_target_yearly) ? $request->gp_target_yearly : 0;
                $staff->target_month_from = !empty($request->target_month_from) ? $request->target_month_from . '-01' : null;


                if ($request->combind_user_id != "") {
                    $combind_user_id = implode(",", $request->combind_user_id) . ',' . $staff->user_id;
                }

                $staff->combind_user_id = $combind_user_id;
            } else {
                $staff->revenue_target_weekly = 0;
                $staff->revenue_target_monthly = 0;
                $staff->revenue_target_quaterly = 0;
                $staff->revenue_target_yearly = 0;
                $staff->gp_target_weekly = 0;
                $staff->gp_target_monthly = 0;
                $staff->gp_target_quaterly = 0;
                $staff->gp_target_yearly = 0;
                $staff->target_month_from = null;
                $staff->combind_user_id = "";
            }

            $staff->user_id = $staff->user_id;
            $staff->created_by = Auth()->user()->id;
            ;

            $result = $staff->update();

            if ($staff->is_target == 1) {
                $check = SysCrmSalesTarget::where('user_id', $staff->user_id)->where('target_month_from', $staff->target_month_from)->get();
                if (count($check) == 0) {
                    SysCrmSalesTarget::insert([
                        'user_id' => $staff->user_id,
                        'target' => $request->revenue_target_monthly,
                        'target_month' => date('Y-m-01'),
                        'status' => 1,
                        'created_by' => Auth::user()->id,
                        'created_at' => Carbon::now('+04:00'),
                        'company_id' => $request->main_company,
                        'company_access' => $company_access,
                        'type' => 1,
                        'revenue_target_weekly' => $request->revenue_target_weekly,
                        'revenue_target_monthly' => $request->revenue_target_monthly,
                        'revenue_target_quaterly' => $request->revenue_target_quaterly,
                        'revenue_target_yearly' => $request->revenue_target_yearly,
                        'gp_target_weekly' => $request->gp_target_weekly,
                        'gp_target_monthly' => $request->gp_target_monthly,
                        'gp_target_quaterly' => $request->gp_target_quaterly,
                        'gp_target_yearly' => $request->gp_target_yearly,
                        'target_month_from' => $request->target_month_from . '-01',
                        'combind_user_id' => $combind_user_id,
                    ]);
                } else {
                    SysCrmSalesTarget::where('id', $check->max('id'))->update([
                        'user_id' => $staff->user_id,
                        'target' => $request->revenue_target_monthly,
                        'target_month' => date('Y-m-01'),
                        'status' => 1,
                        'created_by' => Auth::user()->id,
                        'created_at' => Carbon::now('+04:00'),
                        'company_id' => $request->main_company,
                        'company_access' => $company_access,
                        'type' => 1,
                        'revenue_target_weekly' => $request->revenue_target_weekly,
                        'revenue_target_monthly' => $request->revenue_target_monthly,
                        'revenue_target_quaterly' => $request->revenue_target_quaterly,
                        'revenue_target_yearly' => $request->revenue_target_yearly,
                        'gp_target_weekly' => $request->gp_target_weekly,
                        'gp_target_monthly' => $request->gp_target_monthly,
                        'gp_target_quaterly' => $request->gp_target_quaterly,
                        'gp_target_yearly' => $request->gp_target_yearly,
                        'target_month_from' => $request->target_month_from . '-01',
                        'combind_user_id' => $combind_user_id,
                    ]);
                }
            }

            $user = User::find($staff->user_id);
            //$user->username = $request->email;
            if ($request->password != "") {
                $user->password = Hash::make($request->password);
            }
            $user->role_id = $request->role_id;
            $user->email = $request->email;
            $user->full_name = $request->first_name . ' ' . $request->last_name;
            $user->company_id = $request->company_id;

            //return $request->date_of_resign;

            if ($request->date_of_resign == "") {
                $user->access_status = 1;
                $user->active_status = 1;
            } else {
                $user->access_status = 0;
                $user->active_status = 0;
            }

            $user->update();

            DB::commit();
            //if($request->role_id ==2){
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();

            // }else{
            //     Toastr::error('Operation Failed', 'Failed');
            //     return redirect()->back();
            // }
        } catch (\Exception $e) {
            DB::rollBack();
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function viewStaff($id)
    {
        try {
            $staffDetails = SmStaff::find($id);
            $comid = json_decode('[' . $staffDetails->company_access . ']', true);
            $company_access_list = SysCompany::select('company_name')->wherein('id', $comid)->get();

            if (!empty($staffDetails)) {
                $staffPayrollDetails = SmHrPayrollGenerate::where('staff_id', $id)->where('payroll_status', '!=', 'NG')->get();
                $staffLeaveDetails = SmLeaveRequest::where('staff_id', $id)->get();
                $staffDocumentsDetails = SmStudentDocument::where('student_staff_id', $id)->where('type', '=', 'stf')->get();
                $timelines = SmStudentTimeline::where('staff_student_id', $id)->where('type', '=', 'stf')->get();
                return view('backEnd.humanResource.viewStaff', compact('staffDetails', 'staffPayrollDetails', 'staffLeaveDetails', 'staffDocumentsDetails', 'timelines', 'company_access_list'));
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            dd($e);
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function searchStaff(Request $request)
    {
        try {
            $staff = SmStaff::query();
            $staff->where('active_status', 1);
            if ($request->role_id != "") {
                $staff->where('role_id', $request->role_id);
            }
            if ($request->staff_no != "") {
                $staff->where('staff_no', $request->staff_no);
            }

            if ($request->staff_name != "") {
                $staff->where('full_name', 'like', '%' . $request->staff_name . '%');
            }
            $staffs = $staff->get();
            $roles = Role::where('active_status', '=', '1')->where('id', '!=', 2)->where('id', '!=', 3)->get();
            $company = SysCompany::select('id', 'company_name')->get();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['staffs'] = $staffs->toArray();
                $data['roles'] = $roles->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.humanResource.staff_list', compact('staffs', 'roles', 'company'));
        } catch (\Exception $e) {

            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function getsalespersonlist(Request $request)
    {
        try {
            $com_id = session('logged_session_data.company_id');
            if (Auth::user()->role_id == 5) {
                $data = SmStaff::select('sm_staffs.user_id as id', 'sm_staffs.full_name')
                    ->join('sys_cust_suppl_assign', 'sys_cust_suppl_assign.user_id', 'sm_staffs.id')
                    ->where('sm_staffs.active_status', 1)
                    ->where('sys_cust_suppl_assign.cust_supp_id', $request->id)->groupby('sm_staffs.id', 'sm_staffs.full_name')->get();
            } elseif ($com_id == 1) {
                $data = SmStaff::select('sm_staffs.user_id as id', 'sm_staffs.full_name')
                    ->join('sys_cust_suppl_assign', 'sys_cust_suppl_assign.user_id', 'sm_staffs.id')
                    ->where('sm_staffs.active_status', 1)
                    ->where('sys_cust_suppl_assign.cust_supp_id', $request->id)->groupby('sm_staffs.id', 'sm_staffs.full_name')->get();

                //$data = SmStaff::select('user_id as id','full_name')->where('active_status', '=', '1')
                //->wherein('role_id',[1,2,5,8])->orderby('full_name','asc')->get();
            } else {
                $data = SmStaff::select('sm_staffs.user_id as id', 'sm_staffs.full_name')
                    ->join('sys_cust_suppl_assign', 'sys_cust_suppl_assign.user_id', 'sm_staffs.id')
                    ->where('sm_staffs.active_status', 1)
                    ->where('sys_cust_suppl_assign.cust_supp_id', $request->id)->groupby('sm_staffs.id', 'sm_staffs.full_name')->get();

                //$data = SmStaff::select('user_id as id','full_name')->where('active_status', '=', '1')
                //->wherein('role_id',[1,2,5,8])
                //->whereRaw("find_in_set($com_id,company_access)")->orderby('full_name','asc')->get();
            }

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

    public function viewCustomer($id)
    {
        try {
            $staffDetails = SmStaff::find($id);
            if (!empty($staffDetails)) {
                $staffPayrollDetails = SmHrPayrollGenerate::where('staff_id', $id)->where('payroll_status', '!=', 'NG')->get();
                $staffLeaveDetails = SmLeaveRequest::where('staff_id', $id)->get();
                $staffDocumentsDetails = SmStudentDocument::where('student_staff_id', $id)->where('type', '=', 'stf')->get();
                $timelines = SmStudentTimeline::where('staff_student_id', $id)->where('type', '=', 'stf')->get();
                return view('backEnd.humanResource.viewCustomer', compact('staffDetails', 'staffPayrollDetails', 'staffLeaveDetails', 'staffDocumentsDetails', 'timelines'));
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function customer(Request $request)
    {

        try {
            $staffs = SmStaff::where('active_status', 1)->where('role_id', 2)->get();
            $roles = Role::where('active_status', '=', '1')->where('id', 2)->get();
            return view('backEnd.humanResource.customer_list', compact('staffs', 'roles'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function addCustomer()
    {
        // try{
        $max_staff_no = SmStaff::max('staff_no');
        $roles = Role::where('active_status', '=', '1')->where('id', 2)->get();
        $departments = SmHumanDepartment::where('active_status', '=', '1')->get();
        $designations = SmDesignation::where('active_status', '=', '1')->orderBy('title', 'asc')->get();
        $marital_ststus = SmBaseSetup::where('active_status', '=', '1')->where('base_group_id', '=', '4')->get();
        $genders = SmBaseSetup::where('active_status', '=', '1')->where('base_group_id', '=', '1')->get();

        return view('backEnd.humanResource.addCustomer', compact('roles', 'departments', 'designations', 'marital_ststus', 'max_staff_no', 'genders', 'company'));
        // }catch (\Exception $e) {
        //    Toastr::error('Operation Failed', 'Failed');
        //    return redirect()->back(); 
        // }
    }


    public function addCustomerStore(Request $request)
    {
        //return $request;
        $request->validate([
            'customer_Name' => "required",
            'customer_code' => "required",
            'contcat_person' => "required",
            'mobile' => "required",
            'current_address' => "required",
            'email' => "required",
            'vat_number' => "required",
            'sales_person_name' => "required",
            'credit_limit' => "required",
            'credit_days' => "required",
            'payment_terms' => "required",
            'accountant_name' => "required",
            'accountant_email' => "required",
            'accountant_number' => "required",

        ]);
        // dd($request->input());
        try {


            $inserted_cols = ['first_name', 'last_name', 'email', 'mobile', 'company_name', 'current_address', 'permanent_address', 'bank_account_name', 'bank_account_no', 'bank_name', 'bank_brach', 'paypal_account', 'payoneer_account', 'skrill_account', 'stripe_account', 'wepay_account', 'amazon_account', 'facebook_url', 'twiteer_url', 'linkedin_url', 'instragram_url', 'date_of_joining'];

            $staff_photo = "";
            if ($request->file('staff_photo') != "") {
                $file = $request->file('staff_photo');
                $staff_photo = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('public/uploads/staff/', $staff_photo);
                $staff_photo = 'public/uploads/staff/' . $staff_photo;
            }

            DB::beginTransaction();
            try {
                $user = new User();
                $user->role_id = 2;
                $user->username = $request->email;
                $user->email = $request->email;
                $user->full_name = $request->first_name . ' ' . $request->last_name;
                $user->password = Hash::make(123456);
                $user->save();
                $user->toArray();

                $new_customer = new SmStaff();
                $new_customer->staff_no = $request->staff_no;
                $new_customer->user_id = $user->id;
                $new_customer->role_id = 2;
                if ($request->designation_id != '') {
                    $new_customer->designation_id = $request->designation_id;
                }
                foreach ($inserted_cols as $col) {
                    if (isset($request->$col)) {
                        $new_customer->$col = $request->$col;
                    }
                }
                $new_customer->full_name = $request->first_name . ' ' . $request->last_name;
                $new_customer->staff_photo = $staff_photo;
                $results = $new_customer->save();
                DB::commit();
                return redirect('customers')->with('message-success', 'Operation successfully');
            } catch (\Exception $e) {
                dd($e);
                DB::rollback();
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }

            if ($results) {
                return redirect('customers')->with('message-success', 'Operation successfully');
            } else {
                return redirect()->back()->with('message-danger', 'Something went wrong, please try again');
            }
        } catch (\Exception $e) {
            dd($e);
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    public function customerEdit($id)
    {
        try {
            $editData = SmStaff::find($id);
            $max_staff_no = SmStaff::max('staff_no');
            $roles = Role::where('active_status', '=', '1')->get();
            $departments = SmHumanDepartment::where('active_status', '=', '1')->get();
            $designations = SmDesignation::where('active_status', '=', '1')->get();
            $marital_ststus = SmBaseSetup::where('active_status', '=', '1')->where('base_group_id', '=', '4')->get();
            $genders = SmBaseSetup::where('active_status', '=', '1')->where('base_group_id', '=', '1')->get();

            return view('backEnd.humanResource.addCustomer', compact('editData', 'roles', 'departments', 'designations', 'marital_ststus', 'max_staff_no', 'genders'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }



    public function customerUpdate(Request $request)
    {
        $request->validate([
            'first_name' => "required",
            'email' => "required",
            'mobile' => "required",
        ]);

        try {
            $customer = SmStaff::find($request->staff_id);
            // for update staff photo
            $staff_photos = "";
            if ($request->file('staff_photo') != "") {
                if ($customer->staff_photo != '' && file_exists($customer->staff_photo)) {
                    unlink($customer->staff_photo);
                }
                $file = $request->file('staff_photo');
                $staff_photos = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('public/uploads/staff/', $staff_photos);
                $staff_photo = 'public/uploads/staff/' . $staff_photos;
            } else {
                $staff_photo = $customer->staff_photo;
            }
            //update from user table
            $user = User::find($customer->user_id);
            $user->username = $request->email;
            $user->email = $request->email;
            $user->full_name = $request->first_name . ' ' . $request->last_name;
            $user->save();

            //update from customer table
            $update_customer = SmStaff::find($request->staff_id);

            $inserted_cols = ['first_name', 'last_name', 'email', 'mobile', 'company_name', 'designation_id', 'current_address', 'permanent_address', 'bank_account_name', 'bank_account_no', 'bank_name', 'bank_brach', 'paypal_account', 'payoneer_account', 'skrill_account', 'stripe_account', 'wepay_account', 'amazon_account', 'facebook_url', 'twiteer_url', 'linkedin_url', 'instragram_url', 'date_of_joining'];
            foreach ($inserted_cols as $col) {
                if (isset($request->$col)) {
                    $update_customer->$col = $request->$col;
                }
            }
            $update_customer->full_name = $request->first_name . ' ' . $request->last_name;
            $update_customer->staff_photo = $staff_photo;
            $result = $update_customer->update();

            if ($result) {
                Toastr::success('Operation successful', 'Success');
                return redirect('customers');
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect('staff-directory');
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    //end customer update 






    public function uploadStaffDocuments($staff_id)
    {

        try {
            return view('backEnd.humanResource.uploadStaffDocuments', compact('staff_id'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function saveUploadDocument(Request $request)
    {
        try {
            if ($request->file('staff_upload_document') != "" && $request->title != "") {
                $document_photo = "";
                if ($request->file('staff_upload_document') != "") {
                    $file = $request->file('staff_upload_document');
                    $document_photo = 'staff-' . md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                    $file->move('public/uploads/staff/document/', $document_photo);
                    $document_photo = 'public/uploads/staff/document/' . $document_photo;
                }

                $document = new SmStudentDocument();
                $document->title = $request->title;
                $document->student_staff_id = $request->staff_id;
                $document->type = 'stf';
                $document->file = $document_photo;
                $document->created_by = Auth()->user()->id;
                $results = $document->save();
            }

            if ($results) {
                Toastr::success('Operation successful', 'Success');
                return redirect()->back();
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function deleteStaffDocumentView(Request $request, $id)
    {
        try {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse($id, null);
            }
            return view('backEnd.humanResource.deleteStaffDocumentView', compact('id'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function deleteStaffDocument($id)
    {

        try {
            $result = SmStudentDocument::destroy($id);
            if ($result) {
                Toastr::success('Operation successful', 'Success');
                return redirect()->back();
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function addStaffTimeline($id)
    {
        try {
            return view('backEnd.humanResource.addStaffTimeline', compact('id'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function storeStaffTimeline(Request $request)
    {

        try {
            if ($request->title != "") {

                $document_photo = "";
                if ($request->file('document_file_4') != "") {
                    $file = $request->file('document_file_4');
                    $document_photo = 'stu-' . md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                    $file->move('public/uploads/staff/timeline/', $document_photo);
                    $document_photo = 'public/uploads/staff/timeline/' . $document_photo;
                }

                $timeline = new SmStudentTimeline();
                $timeline->staff_student_id = $request->staff_student_id;
                $timeline->title = $request->title;
                $timeline->type = 'stf';
                $timeline->date = date('Y-m-d', strtotime($request->date));
                $timeline->description = $request->description;
                if (isset($request->visible_to_student)) {
                    $timeline->visible_to_student = $request->visible_to_student;
                }
                $timeline->file = $document_photo;
                $timeline->save();
            }
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function deleteStaffTimelineView($id)
    {

        try {
            return view('backEnd.humanResource.deleteStaffTimelineView', compact('id'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function deleteStaffTimeline($id)
    {

        try {
            $result = SmStudentTimeline::destroy($id);
            if ($result) {
                Toastr::success('Operation successful', 'Success');
                return redirect()->back();
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function deleteStaffView($id)
    {
        try {
            SmStaff::where('user_id', $id)->update(['active_status' => 0]);
            DB::table('users')->where('id', $id)->update([
                'access_status' => 0,
                'active_status' => 0,
            ]);
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function deleteStaff($id)
    {
        try {
            $staff = SmStaff::find($id);
            $user_id = $staff->user_id;
            $staff->active_status = 0;
            $staff->delete_status = 0;
            $staff->save();
            $user = User::find($user_id);
            $user->access_status = 0;
            $result = $user->save();
            if ($result) {
                Toastr::success('Operation successful', 'Success');
                return redirect()->back();
            }
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }


    }

    public function deleteSCustomer($id)
    {
        try {
            $staffs = SmStaff::find($id);
            $staffs->active_status = 0;
            $result = $staffs->update();
            if ($result) {
                $users = User::find($staffs->user_id);
                $users->active_status = 0;
                $results = $users->update();
            }
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function loginAccessPermission(Request $request)
    {

        if ($request->status == 'on') {
            $status = 1;
        } else {
            $status = 0;
        }

        $staff = SmStaff::find($request->id);
        $user_id = $staff->user_id;
        $staff->active_status = $status;
        $staff->save();
        $user = User::find($user_id);
        $user->access_status = $status;
        $user->save();
        return response()->json($request->id);
    }

    public function details($id)
    {
        $employee = SmStaff::with([
            'roles',
            'departments',
            'designations',
            'maincompany',
            'jobDetail',
            'bankDetail',
            'bankDetails',
            'educationQualifications',
            'professionalExperiences',
            'documents',
            'genders',
            'nationalityCountry'
        ])->findOrFail($id);

        // ye ek partial blade return karega jisme sirf detail ka HTML hoga
        return view('backEnd.humanResource.staff_details', compact('employee'));
    }

    // StaffController.php
// app/Http/Controllers/StaffController.php

    public function index($id = null)
    {
        try {
            // Sidebar list: latest first, same company, eager-load role
            $staffsQuery = SmStaff::with(['roles'])
                ->where('company_id', session('logged_session_data.company_id'));

            // Non-admin users ko admin (role_id = 1) staff mat dikhana
            if (Auth::user()->role_id != 1) {
                $staffsQuery->where('role_id', '!=', 1);
            }

            $staffs = $staffsQuery
                ->orderBy('id', 'desc')   // 🔹 latest first
                ->get();

            // filters/dropdowns ke liye
            $roles = Role::where('active_status', 1)->get();
            $company = SysCompany::select('id', 'company_name')->get();

            // Right-pane: selected staff (agar URL me id aaya ho)
            $selectedStaff = null;
            if (!empty($id)) {
                $selectedStaff = SmStaff::with(['roles', 'departments', 'designations', 'maincompany'])
                    ->where('company_id', session('logged_session_data.company_id'))
                    ->when(Auth::user()->role_id != 1, function ($q) {
                        $q->where('role_id', '!=', 1);
                    })
                    ->find($id); // null bhi ho sakta hai; view me handle ho jayega
            }

            return view('backEnd.humanResource.staff_list', compact(
                'staffs',
                'roles',
                'company',
                'selectedStaff',
                'id'
            ));
        } catch (\Exception $e) {
            // optional: Toastr ya fallback
            // Toastr::error('Operation Failed', 'Failed');
            return redirect()->back()->with('error', $e->getMessage());
        }
    }



    /**
     * Helper: parse d/m/Y to Y-m-d
     */
    private function parseDateDmy($str)
    {
        $str = trim($str ?: '');
        if (!$str)
            return null;
        $p = \DateTime::createFromFormat('d/m/Y', $str);
        return $p ? $p->format('Y-m-d') : null;
    }

    private function dec($v): ?string
    {
        if ($v === null)
            return null;
        $v = trim((string) $v);
        if ($v === '')
            return null;
        $v = preg_replace('/[^\d\.\-]/', '', $v);  // remove commas, currency, spaces
        if (!is_numeric($v))
            return null;
        return number_format((float) $v, 2, '.', '');
    }

    // public function storeBasic(Request $req)
    // {
    //     $staffId = $req->input('staff_id');   // may be null on first save
    //     $isUpdate = !empty($staffId);


    //     // Validation
    //     $data = $req->validate([
    //         'staff_code' => ['required', 'string', 'max:50'],
    //         'first_name' => ['required', 'string', 'max:100'],
    //         // 'middle_name' => ['required', 'string', 'max:100'],
    //         'last_name' => ['required', 'string', 'max:100'],
    //         'reporting_manager' => ['required', 'array'],
    //         'employment_type' => ['required', Rule::in(['full_time', 'part_time', 'contract', 'intern'])],


    //         'date_of_birth' => ['required', 'string'], // dd/mm/YYYY from UI
    //         'religion' => ['required', 'string', 'max:50'],
    //         'gender_id' => ['required', 'integer'],
    //         'mobile' => ['required', 'string', 'max:20'],

    //         'email' => [
    //             'required',
    //             'email',
    //             'max:191',
    //             Rule::unique('sm_staffs', 'email')->ignore($staffId),
    //         ],

    //         'marital_status' => ['required', 'string', 'in:single,married,divorced,widowed'],
    //         // 'nationality'    => ['required', 'string', 'max:80'],

    //         'emergency_contact_name' => ['required', 'string', 'max:150'],
    //         'emergency_contact_relationship' => ['required', 'string', 'max:100'],
    //         'emergency_contact_number' => ['required', 'string', 'max:20'],

    //         // password required on create, optional on update
    //         'password' => [$isUpdate ? 'nullable' : 'required', 'string', 'min:6'],
    //         'staff_photo' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],

    //         // Permanent Address Fields
    //         'permanent_country' => ['nullable', 'integer'],
    //         'permanent_state' => ['nullable', 'integer'],
    //         'permanent_city' => ['nullable', 'string', 'max:200'],
    //         'permanent_area' => ['nullable', 'string', 'max:200'],
    //         'permanent_building_no' => ['nullable', 'string', 'max:100'],
    //         'permanent_flat_no' => ['nullable', 'string', 'max:100'],

    //         // Current Address Fields
    //         'current_country' => ['nullable', 'integer'],
    //         'current_state' => ['nullable', 'integer'],
    //         'current_city' => ['nullable', 'string', 'max:200'],
    //         'current_area' => ['nullable', 'string', 'max:200'],
    //         'current_building_no' => ['nullable', 'string', 'max:100'],
    //         'current_flat_no' => ['nullable', 'string', 'max:100'],
    //     ], [], [
    //         'staff_code' => 'User No',
    //         'date_of_birth' => 'Date of Birth',
    //         'gender_id' => 'Gender',
    //         'emergency_contact_number' => 'Emergency Contact Number',
    //     ]);

    //     // Parse DOB: d/m/Y -> Y-m-d
    //     $dob = null;
    //     if ($req->filled('date_of_birth')) {
    //         try {
    //             $dob = Carbon::createFromFormat('d/m/Y', $req->input('date_of_birth'))->format('Y-m-d');
    //         } catch (\Exception $e) {
    //             $dob = Carbon::parse($req->input('date_of_birth'))->format('Y-m-d');
    //         }
    //     }

    //     // Find or create staff
    //     $staff = $isUpdate ? SmStaff::find($staffId) : new SmStaff;
    //     if (!$staff) {
    //         $staff = new SmStaff;
    //         $isUpdate = false;
    //     }

    //     // Map staff payload
    //     $staff->staff_no = $req->input('staff_code');
    //     $staff->first_name = $req->input('first_name');
    //     $staff->middle_name = $req->input('middle_name');
    //     $staff->last_name = $req->input('last_name');
    //     $staff->fathers_name = $req->input('fathers_name');
    //     $staff->mothers_name = $req->input('mothers_name');
    //     $staff->date_of_birth = $dob;
    //     $staff->religion = $req->input('religion');
    //     $staff->gender_id = (int) $req->input('gender_id');
    //     $staff->mobile = $req->input('mobile');
    //     $staff->role_id = (int) $req->input('role_id');
    //     $staff->designation_id = (int) $req->input('designation_id');
    //     $staff->department_id = (int) $req->input('department_id');
    //     $staff->company_id = (int) $req->input('visa_company_name');
    //     $staff->main_company = (int) $req->input('working_company_name');
    //     $staff->company_access = implode(',', $req->input('company_access', []));
    //     $staff->ext_no = $req->input('ext_no_2');
    //     $staff->email = $req->input('email');
    //     $staff->marital_status = $req->input('marital_status');
    //     $staff->nationality = $req->input('nationality');

    //     // Permanent Address
    //     $staff->permanent_country = $req->input('permanent_country');
    //     $staff->permanent_state = $req->input('permanent_state');
    //     $staff->permanent_city = $req->input('permanent_city');
    //     $staff->permanent_area = $req->input('permanent_area');
    //     $staff->permanent_building_no = $req->input('permanent_building_no');
    //     $staff->permanent_flat_no = $req->input('permanent_flat_no');

    //     // Current Address
    //     $staff->current_country = $req->input('current_country');
    //     $staff->current_state = $req->input('current_state');
    //     $staff->current_city = $req->input('current_city');
    //     $staff->current_area = $req->input('current_area');
    //     $staff->current_building_no = $req->input('current_building_no');
    //     $staff->current_flat_no = $req->input('current_flat_no');

    //     $staff->emergency_contact_name = $req->input('emergency_contact_name');
    //     $staff->emergency_contact_relationship = $req->input('emergency_contact_relationship');
    //     $staff->emergency_mobile = $req->input('emergency_contact_number');

    //     $staff->reporting_manager = implode(',', $req->input('reporting_manager', []));
    //     $staff->employment_type = $req->input('employment_type');

    //     if ($req->filled('password')) {
    //         $staff->password = Hash::make($req->input('password'));
    //     } elseif (!$isUpdate) {
    //         $staff->password = Hash::make($req->input('password'));
    //     }

    //     // Photo upload
    //     if ($req->hasFile('staff_photo')) {
    //         $file = $req->file('staff_photo');
    //         $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
    //         $destination = public_path('uploads/staff_photos');
    //         if (!is_dir($destination)) {
    //             mkdir($destination, 0777, true);
    //         }
    //         $file->move($destination, $filename);
    //         $staff->staff_photo = 'uploads/staff_photos/' . $filename;
    //     }

    //     $staff->save();

    //     // Persist documentation if any files/fields were submitted with the request
    //     if ($req->files->count() > 0 || $req->has('docs')) {
    //         // ensure storeDocs knows which staff we're operating on
    //         $req->merge(['staff_id' => $staff->id]);
    //         try {
    //             $this->storeDocs($req);
    //         } catch (ValidationException $ve) {
    //             // Bubble validation errors so the client receives 422 with field errors
    //             throw $ve;
    //         }
    //     }

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Also save/update User
    //     |--------------------------------------------------------------------------
    //     | Fields required: role_id, full_name, username, email, password, usertype, access_status=1
    //     | Assumptions:
    //     | - sm_staffs has a nullable user_id column to link to users.id
    //     | - users table has columns: role_id, full_name, username, email, password, usertype, access_status
    //     | - We derive sensible defaults for username/usertype if not provided.
    //     */
    //     $fullName = trim(collect([$staff->first_name, $staff->middle_name, $staff->last_name])
    //         ->filter()->implode(' '));

    //     // prefer request username if provided, else email local-part, else slug of name + staff_no
    //     $username = $req->input('username')
    //         ?? (strpos($staff->email, '@') !== false ? strstr($staff->email, '@', true) : null)
    //         ?? Str::slug(($staff->first_name . '-' . $staff->last_name . '-' . $staff->staff_no), '_');

    //     // Try to find an existing user: priority = linked user_id -> email
    //     $user = null;
    //     if (!empty($staff->user_id)) {
    //         $user = User::find($staff->user_id);
    //     }
    //     if (!$user) {
    //         $user = User::where('email', $staff->email)->first();
    //     }
    //     if (!$user) {
    //         $user = new User;
    //     }

    //     $user->role_id = (int) $req->input('role_id');
    //     $user->full_name = $fullName;
    //     $user->username = $username;
    //     $user->email = $staff->email;
    //     $user->usertype = $req->input('usertype', 'staff'); // default 'staff' if not sent
    //     $user->access_status = 1;

    //     // Only (re)set password when provided, but ensure create has it
    //     if ($req->filled('password')) {
    //         $user->password = Hash::make($req->input('password'));
    //     } elseif (!$user->exists) {
    //         // On create, password must exist already due to validation
    //         $user->password = Hash::make($req->input('password'));
    //     }

    //     $user->save();

    //     // Link back to staff if not linked
    //     if (empty($staff->user_id) || $staff->user_id !== $user->id) {
    //         $staff->user_id = $user->id;
    //         $staff->save();
    //     }

    //     // Save Education, Experience, and Bank data from session to database
    //     $this->saveSessionDataToDatabase($staff->id);

    //     return response()->json([
    //         'ok' => true,
    //         'message' => $isUpdate ? 'Basic details updated.' : 'Basic details saved.',
    //         'staff_id' => $staff->id,
    //         'user_id' => $user->id,
    //     ]);
    // }


    public function storeBasic(Request $request)
    {
        // Validate core fields (dynamic arrays optional)
        $rules = [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'salutation' => ['nullable', 'string', 'max:10'],
            'date_of_birth' => ['nullable', 'string'],
            'place_of_birth' => ['nullable', 'string', 'max:191'],
            'religion' => ['nullable', 'string', 'max:80'],
            'gender_id' => ['nullable', 'integer'],
            'mobile' => ['nullable', 'string', 'max:40'],
            'email' => ['required', 'email', 'max:191', 'unique:sm_staffs,email'],
            'marital_status' => ['nullable', 'string', 'in:single,married,divorced,widowed'],
            'blood_group' => ['nullable', 'string', 'max:10'],
            'password' => ['required', 'string', 'min:6'],

            'role_id' => ['nullable', 'integer'],
            'department_id' => ['nullable', 'integer'],
            'designation_id' => ['nullable', 'integer'],
            'reporting_manager' => ['nullable'],

            // job/company
            'employment_type' => ['nullable', 'string'],
            'date_of_joining_2' => ['nullable', 'string'],
            'probation_end_date' => ['nullable', 'string'],
            'visa_company_name' => ['nullable', 'integer'],
            'working_company_name' => ['nullable', 'integer'],
            'company_access' => ['nullable', 'array'],
            'ext_no_2' => ['nullable', 'string', 'max:50'],
            'company_email' => ['nullable', 'email', 'max:191'],
            'company_mobile' => ['nullable', 'string', 'max:30'],

            // salary
            'salary_basic' => ['nullable', 'numeric'],
            'salary_allowances' => ['nullable', 'numeric'],
            'salary_other_allowances' => ['nullable', 'numeric'],
            'transport_allowance' => ['nullable', 'numeric'],
            'other_benefits' => ['nullable', 'numeric'],
            'salary_gross' => ['nullable', 'numeric'],

            // arrays and files
            'docs' => ['nullable', 'array'],
            'educations' => ['nullable', 'array'],
            'experiences' => ['nullable', 'array'],
            'banks' => ['nullable', 'array'],
            'staff_photo' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png'],
            // family details
            'father_first_name' => ['nullable', 'string', 'max:100'],
            'father_last_name' => ['nullable', 'string', 'max:100'],
            'father_mobile' => ['nullable', 'string', 'max:40'],
            'father_email' => ['nullable', 'email', 'max:191'],
            'mother_first_name' => ['nullable', 'string', 'max:100'],
            'mother_last_name' => ['nullable', 'string', 'max:100'],
            'mother_mobile' => ['nullable', 'string', 'max:40'],
            'mother_email' => ['nullable', 'email', 'max:191'],
            'spouse_first_name' => ['nullable', 'string', 'max:100'],
            'spouse_last_name' => ['nullable', 'string', 'max:100'],
            'spouse_mobile' => ['nullable', 'string', 'max:40'],
            'spouse_email' => ['nullable', 'email', 'max:191'],
            'emergency1_salutation' => ['nullable', 'string', 'max:10'],
            'emergency1_name' => ['nullable', 'string', 'max:150'],
            'emergency1_mobile' => ['nullable', 'string', 'max:40'],
            'emergency1_email' => ['nullable', 'email', 'max:191'],
            'emergency1_relationship' => ['nullable', 'string', 'max:100'],
            'emergency2_salutation' => ['nullable', 'string', 'max:10'],
            'emergency2_name' => ['nullable', 'string', 'max:150'],
            'emergency2_mobile' => ['nullable', 'string', 'max:40'],
            'emergency2_email' => ['nullable', 'email', 'max:191'],
            'emergency2_relationship' => ['nullable', 'string', 'max:100'],
            'father_attachment' => ['nullable', 'file', 'mimes:pdf,jpeg,png,gif,webp', 'max:5000'],
            'mother_attachment' => ['nullable', 'file', 'mimes:pdf,jpeg,png,gif,webp', 'max:5000'],
            'spouse_attachment' => ['nullable', 'file', 'mimes:pdf,jpeg,png,gif,webp', 'max:5000'],

            // resignation fields (optional on create)
            'resignation_type' => ['nullable', 'string', 'max:40'],
            'resignation_reason' => ['nullable', 'string', 'max:80'],
            'resignation_remarks' => ['nullable', 'string'],
            'resignation_submitted_date' => ['nullable', 'string'],
            'notice_period_days' => ['nullable', 'integer'],
            'last_working_day' => ['nullable', 'string'],
            'relieving_date' => ['nullable', 'string'],
            'knowledge_transfer_completed' => ['nullable', 'string', 'max:8'],
            'handover_to' => ['nullable', 'string', 'max:255'],
            'handover_notes' => ['nullable', 'string'],
            'resignation_status' => ['nullable', 'string', 'max:40'],

            // resignation extras
            'assets_returned' => ['nullable', 'string', 'max:8'],
            'other_resignation_docs' => ['nullable', 'array'],
            'other_resignation_docs.*' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png'],
            'other_resignation_docs_remarks' => ['nullable', 'string'],
            'resignation_letter' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png'],
            'resignation_letter_remarks' => ['nullable', 'string'],
            'exit_interview_conducted' => ['nullable', 'string', 'max:8'],
            'exit_interview_feedback' => ['nullable', 'string'],
            'settlement_amount' => ['nullable', 'numeric'],
        ];


        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $first = $validator->errors()->first();
            Toastr::error($first ?: 'Validation failed. Please check the inputs.');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // small helpers contained in this function only
        $parseDate = function ($val) {
            $val = trim((string) ($val ?? ''));
            if ($val === '')
                return null;
            try {
                $d = \DateTime::createFromFormat('d/m/Y', $val);
                if ($d)
                    return $d->format('Y-m-d');
            } catch (\Exception $e) {
            }
            try {
                return (new \Carbon\Carbon($val))->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        };

        $storeFile = function ($file, $folder) {
            if (!$file)
                return null;
            $orig = $file->getClientOriginalName();
            $ext = $file->getClientOriginalExtension();
            $base = pathinfo($orig, PATHINFO_FILENAME);
            $safeBase = preg_replace('/[^A-Za-z0-9_\-]/', '_', $base);
            $candidate = $safeBase . '.' . $ext;
            $n = 0;
            while (Storage::disk('public')->exists($folder . '/' . $candidate)) {
                $n++;
                $candidate = $safeBase . '_' . $n . '.' . $ext;
            }
            $path = $file->storeAs($folder, $candidate, 'public');
            return $path; // relative path on public disk
        };

        DB::beginTransaction();
        try {
            // 1) Create staff base record
            $staff = new SmStaff();
            $staff->staff_no = SysHelper::get_new_staff_code();
            $staff->employee_salutation = $request->input('salutation');
            $staff->first_name = $request->input('first_name');
            $staff->last_name = $request->input('last_name');
            $staff->place_of_birth = $request->input('place_of_birth');
            $staff->religion = $request->input('religion');
            if ($request->gender_id != "" && $request->gender_id != null) {
                $staff->gender_id = $request->input('gender_id');
            }
            $staff->ext_no = $request->input('ext_no_2');
            $staff->department_id = (int) ($request->input('department_id') ?: 0);
            $staff->designation_id = (int) ($request->input('designation_id') ?: 0);

            $staff->role_id = (int) ($request->input('role_id') ?: 2);
            $staff->company_id = (int) (session('logged_session_data.company_id'));
            $staff->mobile = $request->input('mobile');
            $staff->email = $request->input('email');
            $staff->marital_status = $request->input('marital_status');
            $staff->blood_group = $request->input('blood_group');

            // DOB parsing
            $dob = $parseDate($request->input('date_of_birth'));
            if ($dob)
                $staff->date_of_birth = $dob;

            if ($request->perm_country != "" && $request->perm_country != null) {
                $staff->permanent_country = $request->input('perm_country');
            }

            // address fields (form uses perm_ and curr_ prefixes)

            if ($request->perm_state != "" && $request->perm_state != null) {
                $staff->permanent_state = $request->input('perm_state');
            }




            $staff->permanent_city = $request->input('perm_city');
            $staff->permanent_area = $request->input('perm_area');
            $staff->permanent_building_no = $request->input('perm_building_name');
            $staff->permanent_flat_no = $request->input('perm_flat_office_no');

            if ($request->curr_country != "" && $request->curr_country != null) {
                $staff->current_country = $request->input('curr_country');
            }



            if ($request->curr_state != "" && $request->curr_state != null) {
                $staff->current_state = $request->input('curr_state');
            }



            $staff->current_city = $request->input('curr_city');
            $staff->current_area = $request->input('curr_area');
            $staff->current_building_no = $request->input('curr_building_name');
            $staff->current_flat_no = $request->input('curr_flat_office_no');

            $staff->emergency_contact_name = $request->input('emergency1_name');
            $staff->emergency_contact_relationship = $request->input('emergency1_relationship');
            $staff->emergency_mobile = $request->input('emergency1_mobile');
            $staff->emergency_email = $request->input('emergency1_email');
            $staff->em1_salutation = $request->input('emergency1_salutation') ?? $request->input('em1_salutation');

            // emergency contact 2
            $staff->emergency2_contact_name = $request->input('emergency2_name');
            $staff->emergency2_mobile = $request->input('emergency2_mobile');
            $staff->emergency2_email = $request->input('emergency2_email');
            $staff->emergency2_contact_relationship = $request->input('emergency2_relationship');
            $staff->em2_salutation = $request->input('emergency2_salutation') ?? $request->input('em2_salutation');



            // family fields
            $staff->fathers_first_name = $request->input('father_first_name');
            $staff->fathers_last_name = $request->input('father_last_name');
            $staff->father_mobile = $request->input('father_mobile');
            $staff->father_email = $request->input('father_email');

            $staff->mothers_first_name = $request->input('mother_first_name');
            $staff->mothers_last_name = $request->input('mother_last_name');
            $staff->mother_mobile = $request->input('mother_mobile');
            $staff->mother_email = $request->input('mother_email');

            $staff->spouse_first_name = $request->input('spouse_first_name');
            $staff->spouse_last_name = $request->input('spouse_last_name');
            $staff->spouse_mobile = $request->input('spouse_mobile');
            $staff->spouse_email = $request->input('spouse_email');



            // staff photo
            if ($request->hasFile('staff_photo')) {
                $staff->staff_photo = $storeFile($request->file('staff_photo'), 'uploads/staff_photos');
            }

            $staff->save();

            // Save family attachments as documents (group: family)
            if ($request->hasFile('father_attachment')) {
                $fa = $request->file('father_attachment');
                $orig = $fa->getClientOriginalName();
                $path = $storeFile($fa, 'employee/family_attachments');
                SmStaffDocument::create([
                    'staff_id' => $staff->id,
                    'group' => 'family',
                    'key' => 'father_attachment',
                    'name' => $orig,
                    'path' => $path,
                ]);
            }

            if ($request->hasFile('mother_attachment')) {
                $ma = $request->file('mother_attachment');
                $orig = $ma->getClientOriginalName();
                $path = $storeFile($ma, 'employee/family_attachments');
                SmStaffDocument::create([
                    'staff_id' => $staff->id,
                    'group' => 'family',
                    'key' => 'mother_attachment',
                    'name' => $orig,
                    'path' => $path,
                ]);
            }

            if ($request->hasFile('spouse_attachment')) {
                $sa = $request->file('spouse_attachment');
                $orig = $sa->getClientOriginalName();
                $path = $storeFile($sa, 'employee/family_attachments');
                SmStaffDocument::create([
                    'staff_id' => $staff->id,
                    'group' => 'family',
                    'key' => 'spouse_attachment',
                    'name' => $orig,
                    'path' => $path,
                ]);
                // also keep legacy spouse_attachment column
                $staff->spouse_attachment = $path;
                $staff->save();
            }

            // 2) Create or update user and link
            $user = User::where('email', $staff->email)->first();
            if (!$user)
                $user = new User();
            $fullName = trim(implode(' ', array_filter([$staff->first_name, $staff->last_name])));
            $username = $request->input('username') ?: (strpos($staff->email, '@') !== false ? strstr($staff->email, '@', true) : Str::slug($fullName, '_'));

            $user->role_id = (int) ($request->input('role_id') ?: 2);
            $user->full_name = $fullName;
            $user->username = $username;
            $user->email = $staff->email;
            $user->usertype = $request->input('usertype', 'staff');
            $user->access_status = 1;
            $user->password = Hash::make($request->input('password'));
            $user->save();

            $staff->user_id = $user->id;
            $staff->save();

            // 3) Job details
            $jobPayload = [
                'staff_id' => $staff->id,
                'date_of_joining' => $parseDate($request->input('date_of_joining_2')),
                'probation_end_date' => $parseDate($request->input('probation_end_date')),
                'department_id' => $request->input('department_id'),
                'designation_id' => $request->input('designation_id'),
                'grade' => $request->input('grade'),
                // SmStaffJobDetail casts these to arrays, prefer passing arrays when available
                'reporting_manager' =>  $request->input('reporting_manager'),
                'employment_type' => $request->input('employment_type'),
                'visa_company_name' => $request->input('visa_company_name'),
                'working_company_name' => $request->input('working_company_name'),
                'company_access' => is_array($request->input('company_access')) ? implode(',', $request->input('company_access')) : $request->input('company_access'),
                'ext_no' => $request->input('ext_no_2'),
                'company_email' => $request->input('company_email'),
                'company_mobile' => $request->input('company_mobile'),
                'work_location' => $request->input('work_location'),
                'work_hours' => $request->input('work_hours'),
                'week_off' => is_array($request->input('hr_weekly_off')) ? implode(',', $request->input('hr_weekly_off')) : $request->input('hr_weekly_off'),
                'salary_basic' => $this->numOrNull($request->input('salary_basic')),
                'salary_allowances' => $this->numOrNull($request->input('salary_allowances')),
                'salary_other_allowances' => $this->numOrNull($request->input('salary_other_allowances')),
                'transport_allowance' => $this->numOrNull($request->input('transport_allowance')),
                'other_benefits' => $this->numOrNull($request->input('other_benefits')),
                'salary_gross' => $this->numOrNull($request->input('salary_gross')),
                'is_target' => (int) ($request->input('is_target') ?: 0),
                'target_month_from' => $parseDate($request->input('target_month_from')),
                'brand_ids' => $request->input('brands', []),
                'role_id' => $request->input('role_id'),
            ];

            // Compute gross if missing but parts exist
            if (is_null($jobPayload['salary_gross'])) {
                $b = (float) ($jobPayload['salary_basic'] ?? 0);
                $a = (float) ($jobPayload['salary_allowances'] ?? 0);
                $sum = $b + $a;
                $jobPayload['salary_gross'] = $sum > 0 ? $sum : null;
            }

            // Map revenue/gp target into period-specific columns (weekly/monthly/quaterly/yearly)
            $revenueTarget = $this->numOrNull($request->input('revenue_target'));
            $gpTarget = $this->numOrNull($request->input('gp_target'));
            $period = $request->input('target_period'); // expected: weekly|monthly|quaterly|yearly
            $validPeriods = ['weekly', 'monthly', 'quaterly', 'yearly'];
            if (in_array($period, $validPeriods, true)) {
                if ($revenueTarget !== null) {
                    $col = 'revenue_target_' . $period; // matches fillable: revenue_target_weekly/monthly/quaterly/yearly
                    $jobPayload[$col] = $revenueTarget;
                }
                if ($gpTarget !== null) {
                    $col2 = 'gp_target_' . $period;
                    $jobPayload[$col2] = $gpTarget;
                }
            }

            $job = SmStaffJobDetail::create($jobPayload);

            // store job attachments
            if ($request->hasFile('att_resume'))
                $job->att_resume = $storeFile($request->file('att_resume'), 'employee/job');
            if ($request->hasFile('att_offer_letter'))
                $job->att_offer_letter = $storeFile($request->file('att_offer_letter'), 'employee/job');
            if ($request->hasFile('att_signed_contract'))
                $job->att_signed_contract = $storeFile($request->file('att_signed_contract'), 'employee/job');
            $job->save();

            // 4) Resignation (if provided in Add Staff form)
            // If the form included resignation details, persist them to EndOfService tables
            if (
                $request->filled('resignation_type') ||
                $request->filled('resignation_submitted_date') ||
                $request->filled('resignation_status') ||
                $request->filled('assets_returned') ||
                $request->filled('exit_interview_conducted') ||
                $request->filled('settlement_amount') ||
                $request->hasFile('resignation_letter') ||
                $request->hasFile('other_resignation_docs')
            ) {
                // Master record
                $eosData = [
                    'employee_id' => $staff->id,
                    'department_id' => $staff->department_id ?: 0,
                    'designation_id' => $staff->designation_id ?: 0,
                    'reporting_manager_id' => $request->input('reporting_manager'),
                    'separation_type' => 1,
                    'resignation_type' => $this->getResignationTypeValue($request->resignation_type),
                    'initiated_by' => $this->getInitiatedByValue($request->initiated_by),
                    'reason_category' => $this->getReasonCategoryValue($request->resignation_reason),
                    'detailed_reason' => $request->resignation_remarks ?: 'N/A',
                    'status' => $this->getStatusValue($request->resignation_status),
                    'created_by' => Auth::id() ?: 1,
                    'updated_by' => Auth::id() ?: 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $endOfService = EndOfService::updateOrCreate(['employee_id' => $staff->id], $eosData);
                $endOfServiceId = $endOfService->id;

                // Notice/period details
                EndOfServiceNotice::updateOrCreate(
                    ['end_of_service_id' => $endOfServiceId],
                    [
                        'end_of_service_id' => $endOfServiceId,
                        'notice_waiver' => $this->getYesNoValue($request->notice_waiver),
                        'resignation_submitted_date' => $this->formatDateOrNull($request->resignation_submitted_date) ?: now()->format('Y-m-d'),
                        'notice_period_days' => $request->notice_period_days && is_numeric($request->notice_period_days) ? $request->notice_period_days : null,
                        'last_working_day' => $this->formatDateOrNull($request->last_working_day),
                        'garden_leave_applicable' => $this->getYesNoValue($request->garden_leave_applicable),
                        'garden_leave_start_date' => $this->formatDateOrNull($request->garden_leave_start_date),
                        'garden_leave_end_date' => $this->formatDateOrNull($request->garden_leave_end_date),
                        'relieving_date' => $this->formatDateOrNull($request->relieving_date),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );

                // Handover details
                EndOfServiceHandover::updateOrCreate(
                    ['end_of_service_id' => $endOfServiceId],
                    [
                        'end_of_service_id' => $endOfServiceId,
                        'knowledge_transfer_required' => $this->getYesNoValue($request->knowledge_transfer_completed),
                        // Add free-text handover_to if provided (form uses free-text name)
                        'handover_to' => $request->handover_to ?: null,
                        'handover_notes' => $request->handover_notes ?: null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );

                // ----- Asset clearance (map simple yes/no to clearance_status) -----
                if ($request->filled('assets_returned') || $request->filled('other_resignation_docs_remarks')) {
                    $clearanceValue = $request->assets_returned === 'yes' ? 'completed' : 'pending';
                    $assetClearance = EndOfServiceAssetClearance::updateOrCreate(
                        ['end_of_service_id' => $endOfServiceId],
                        [
                            'end_of_service_id' => $endOfServiceId,
                            'clearance_status' => $this->getClearanceStatusValue($clearanceValue),
                            'remarks' => $request->other_resignation_docs_remarks ?: null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }

                // ----- Exit interview -----
                if ($request->filled('exit_interview_conducted') || $request->filled('exit_interview_feedback') || $request->filled('exit_interview_date')) {
                    EndOfServiceExitInterview::updateOrCreate(
                        ['end_of_service_id' => $endOfServiceId],
                        [
                            'end_of_service_id' => $endOfServiceId,
                            'exit_interview_conducted' => $this->getYesNoValue($request->exit_interview_conducted),
                            'exit_interview_date' => $this->formatDateOrNull($request->exit_interview_date),
                            'hr_feedback' => $request->exit_interview_feedback ?: null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }

                // ----- Finance / Settlement amount -----
                if ($request->filled('settlement_amount')) {
                    EndOfServiceFinance::updateOrCreate(
                        ['end_of_service_id' => $endOfServiceId],
                        [
                            'end_of_service_id' => $endOfServiceId,
                            'net_eos_payable' => $this->getDecimalValue($request->settlement_amount),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }

                // ----- Documents: resignation_letter + other_resignation_docs -----
                if ($request->hasFile('resignation_letter') || $request->hasFile('other_resignation_docs')) {
                    $documents = [];
                    if ($request->hasFile('resignation_letter')) {
                        $documents[] = [
                            'end_of_service_id' => $endOfServiceId,
                            'document_name' => 'Resignation Letter',
                            'document_date' => now()->toDateString(),
                            'attachment' => $request->file('resignation_letter')->store('eos/documents'),
                            'remarks' => $request->resignation_letter_remarks ?: null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }

                    if ($request->hasFile('other_resignation_docs')) {
                        foreach ($request->file('other_resignation_docs') as $f) {
                            if ($f && $f->isValid()) {
                                $documents[] = [
                                    'end_of_service_id' => $endOfServiceId,
                                    'document_name' => 'Other Resignation Document',
                                    'document_date' => now()->toDateString(),
                                    'attachment' => $f->store('eos/documents'),
                                    'remarks' => $request->other_resignation_docs_remarks ?: null,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ];
                            }
                        }
                    }

                    foreach ($documents as $document) {
                        EndOfServiceDocument::create($document);
                    }
                }
            }

            // 4) Banks
            foreach ($request->input('banks', []) as $i => $b) {
                $bankData = [
                    'staff_id' => $staff->id,
                    'bank_name' => $b['bank_name'] ?? null,
                    'bank_branch' => $b['branch_name'] ?? null,
                    'bank_ac_holder' => $b['account_holder'] ?? null,
                    'bank_ac_number' => $b['account_number'] ?? null,
                    'iban_number' => isset($b['iban_number']) ? strtoupper(preg_replace('/\s+/', '', $b['iban_number'])) : null,
                    'swift_code' => isset($b['swift_code']) ? strtoupper(trim($b['swift_code'])) : null,
                    'bank_currency' => isset($b['currency']) ? strtoupper(trim($b['currency'])) : null,
                ];
                if ($request->hasFile("banks.$i.iban_letter")) {
                    $bankData['att_iban_letter'] = $storeFile($request->file("banks.$i.iban_letter"), 'employee/banks');
                }
                SmStaffBankDetail::create($bankData);
            }

            // 5) Educations
            foreach ($request->input('educations', []) as $i => $ed) {
                $eduData = [
                    'staff_id' => $staff->id,
                    'qualification' => $ed['qualification'] ?? null,
                    'university' => $ed['university'] ?? $ed['institution'] ?? null,
                    'specialization' => $ed['specialization'] ?? null,
                    'year' => $ed['year'] ?? null,
                    'result' => $ed['result'] ?? null,
                    'gpa' => $ed['gpa'] ?? null,
                    'mode' => $ed['mode'] ?? null,
                    'country' => $ed['country'] ?? null,
                    'duration_years' => $ed['duration'] ?? $ed['duration_years'] ?? null,
                ];
                if ($request->hasFile("educations.$i.certificate")) {
                    $eduData['certificate_path'] = $storeFile($request->file("educations.$i.certificate"), 'employee/education');
                }
                SmStaffEducationQualification::create($eduData);
            }

            // 6) Experiences
            foreach ($request->input('experiences', []) as $i => $ex) {
                $expData = [
                    'staff_id' => $staff->id,
                    'organization' => $ex['organization'] ?? null,
                    'designation' => $ex['designation'] ?? null,
                    'years' => isset($ex['years']) ? (int) $ex['years'] : null,
                    'months' => isset($ex['months']) ? (int) $ex['months'] : null,
                    'responsibilities' => $ex['responsibilities'] ?? null,
                ];
                if ($request->hasFile("experiences.$i.certificate")) {
                    $expData['certificate_path'] = $storeFile($request->file("experiences.$i.certificate"), 'employee/experience');
                }
                SmStaffProfessionalExperience::create($expData);
            }

            // 7) Documents (joining, employment, others)
            $docs = $request->input('docs', []);

            // joining:
            foreach ($docs['joining'] ?? [] as $key => $row) {
                $remarks = $row['remarks'] ?? null;
                $number = $row['number'] ?? null;
                $expiry = $parseDate($row['expiry'] ?? null);
                $path = null;
                $origName = $row['label'] ?? ucfirst(str_replace('_', ' ', $key));
                if ($request->hasFile("docs.joining.$key.file")) {
                    $file = $request->file("docs.joining.$key.file");
                    $origName = $file->getClientOriginalName();
                    $path = $storeFile($file, 'employee/docs/joining');
                } elseif (!empty($row['existing'])) {
                    $path = $row['existing'];
                }
                if (!$path && !$remarks)
                    continue;
                SmStaffDocument::create([
                    'staff_id' => $staff->id,
                    'group' => 'joining',
                    'key' => $key,
                    'name' => $origName,
                    'path' => $path,
                    'remarks' => $remarks,
                    'expiry_date' => $expiry,
                    'document_number' => $number,
                ]);
            }

            // employment:
            foreach ($docs['employment'] ?? [] as $key => $row) {
                $remarks = $row['remarks'] ?? null;
                $number = $row['number'] ?? null;
                $path = null;
                $origName = $row['label'] ?? ucfirst(str_replace('_', ' ', $key));
                if ($request->hasFile("docs.employment.$key.file")) {
                    $file = $request->file("docs.employment.$key.file");
                    $origName = $file->getClientOriginalName();
                    $path = $storeFile($file, 'employee/docs/employment');
                } elseif (!empty($row['existing'])) {
                    $path = $row['existing'];
                }
                if (!$path && !$remarks && !$number)
                    continue;
                SmStaffDocument::create([
                    'staff_id' => $staff->id,
                    'group' => 'employment',
                    'key' => $key,
                    'name' => $origName,
                    'path' => $path,
                    'remarks' => $remarks,
                    'document_number' => $number,
                ]);
            }

            // others:
            foreach ($docs['others'] ?? [] as $i => $row) {
                $name = trim((string) ($row['name'] ?? ''));
                $number = $row['number'] ?? null;
                $remarks = $row['remarks'] ?? null;
                $path = null;
                if ($request->hasFile("docs.others.$i.file")) {
                    $file = $request->file("docs.others.$i.file");
                    $origName = $file->getClientOriginalName();
                    $name = $name ?: $origName;
                    $path = $storeFile($file, 'employee/docs/others');
                } elseif (!empty($row['existing'])) {
                    $path = $row['existing'];
                }
                if (!$name && !$path && !$remarks)
                    continue;
                SmStaffDocument::create([
                    'staff_id' => $staff->id,
                    'group' => 'others',
                    'key' => 'other_' . ($i + 1),
                    'name' => $name ?: 'Other Document',
                    'path' => $path,
                    'remarks' => $remarks,
                    'document_number' => $number,
                ]);
            }

            DB::commit();

            Toastr::success('Staff saved successfully', 'Success');
            return redirect()->back();

        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            Toastr::error('Operation Failed. Please try again.', 'Failed');
            return redirect()->back()->withInput();
        }
    }


    public function updateBasic(Request $request)
    {
        $rules = [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'password' => ['nullable', 'string', 'min:6'],
            'staff_photo' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png'],
            'father_attachment' => ['nullable', 'file', 'mimes:pdf,jpeg,png,gif,webp', 'max:5000'],
            'mother_attachment' => ['nullable', 'file', 'mimes:pdf,jpeg,png,gif,webp', 'max:5000'],
            'spouse_attachment' => ['nullable', 'file', 'mimes:pdf,jpeg,png,gif,webp', 'max:5000'],
            'docs' => ['nullable', 'array'],
            'educations' => ['nullable', 'array'],
            'experiences' => ['nullable', 'array'],
            'banks' => ['nullable', 'array'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            dd($validator->errors());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // helpers
        $parseDate = function ($val) {
            $val = trim((string) ($val ?? ''));
            if ($val === '')
                return null;
            try {
                $d = \DateTime::createFromFormat('d/m/Y', $val);
                if ($d)
                    return $d->format('Y-m-d');
            } catch (\Exception $e) {
            }
            try {
                return (new \Carbon\Carbon($val))->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        };

        $storeFile = function ($file, $folder) {
            if (!$file)
                return null;
            $orig = $file->getClientOriginalName();
            $ext = $file->getClientOriginalExtension();
            $base = pathinfo($orig, PATHINFO_FILENAME);
            $safeBase = preg_replace('/[^A-Za-z0-9_\-]/', '_', $base);
            $candidate = $safeBase . '.' . $ext;
            $n = 0;
            while (Storage::disk('public')->exists($folder . '/' . $candidate)) {
                $n++;
                $candidate = $safeBase . '_' . $n . '.' . $ext;
            }
            $path = $file->storeAs($folder, $candidate, 'public');
            return $path; // relative path on public disk
        };

        DB::beginTransaction();
        try {
            $staffId = (int) $request->input('staff_id');

            $staff = SmStaff::findOrFail($staffId);

            // Basic fields
            $staff->employee_salutation = $request->input('salutation');
            $staff->first_name = $request->input('first_name');
            $staff->last_name = $request->input('last_name');
            $staff->place_of_birth = $request->input('place_of_birth');
            $staff->religion = $request->input('religion');
            if ($request->filled('gender_id'))
                $staff->gender_id = $request->input('gender_id');
            $staff->ext_no = $request->input('ext_no_2');
            $staff->department_id = (int) ($request->input('department_id') ?: 0);
            $staff->designation_id = (int) ($request->input('designation_id') ?: 0);
            $staff->role_id = (int) ($request->input('role_id') ?: $staff->role_id ?: 2);
            $staff->mobile = $request->input('mobile');

            // Only update email if changed (validation already checked uniqueness)
            if ($request->filled('email'))
                $staff->email = $request->input('email');

            $staff->marital_status = $request->input('marital_status');
            $staff->blood_group = $request->input('blood_group');

            // DOB
            $dob = $parseDate($request->input('date_of_birth'));
            if ($dob)
                $staff->date_of_birth = $dob;

            // Addresses
            if ($request->filled('perm_country'))
                $staff->permanent_country = $request->input('perm_country');
            if ($request->filled('perm_state'))
                $staff->permanent_state = $request->input('perm_state');
            $staff->permanent_city = $request->input('perm_city');
            $staff->permanent_area = $request->input('perm_area');
            $staff->permanent_building_no = $request->input('perm_building_name');
            $staff->permanent_flat_no = $request->input('perm_flat_office_no');

            if ($request->filled('curr_country'))
                $staff->current_country = $request->input('curr_country');
            if ($request->filled('curr_state'))
                $staff->current_state = $request->input('curr_state');
            $staff->current_city = $request->input('curr_city');
            $staff->current_area = $request->input('curr_area');
            $staff->current_building_no = $request->input('curr_building_name');
            $staff->current_flat_no = $request->input('curr_flat_office_no');

            // Emergency contacts
            $staff->emergency_contact_name = $request->input('emergency1_name');
            $staff->emergency_contact_relationship = $request->input('emergency1_relationship');
            $staff->emergency_mobile = $request->input('emergency1_mobile');
            $staff->emergency_email = $request->input('emergency1_email');
            $staff->em1_salutation = $request->input('emergency1_salutation') ?? $request->input('em1_salutation');

            $staff->emergency2_contact_name = $request->input('emergency2_name');
            $staff->emergency2_mobile = $request->input('emergency2_mobile');
            $staff->emergency2_email = $request->input('emergency2_email');
            $staff->emergency2_contact_relationship = $request->input('emergency2_relationship');
            $staff->em2_salutation = $request->input('emergency2_salutation') ?? $request->input('em2_salutation');

            // Family
            $staff->fathers_first_name = $request->input('father_first_name');
            $staff->fathers_last_name = $request->input('father_last_name');
            $staff->father_mobile = $request->input('father_mobile');
            $staff->father_email = $request->input('father_email');

            $staff->mothers_first_name = $request->input('mother_first_name');
            $staff->mothers_last_name = $request->input('mother_last_name');
            $staff->mother_mobile = $request->input('mother_mobile');
            $staff->mother_email = $request->input('mother_email');

            $staff->spouse_first_name = $request->input('spouse_first_name');
            $staff->spouse_last_name = $request->input('spouse_last_name');
            $staff->spouse_mobile = $request->input('spouse_mobile');
            $staff->spouse_email = $request->input('spouse_email');

            // Photo: replace only if new uploaded
            if ($request->hasFile('staff_photo')) {
                // delete old file if exists
                if ($staff->staff_photo && Storage::disk('public')->exists($staff->staff_photo)) {
                    Storage::disk('public')->delete($staff->staff_photo);
                }
                $staff->staff_photo = $storeFile($request->file('staff_photo'), 'uploads/staff_photos');
            }

            $staff->save();

            // Family attachments: update existing or create
            $updateOrCreateFamilyDoc = function ($key, $file) use ($staff, $storeFile) {
                if (!$file)
                    return;
                $existing = SmStaffDocument::where('staff_id', $staff->id)->where('group', 'family')->where('key', $key)->first();
                $path = $storeFile($file, 'employee/family_attachments');
                $orig = $file->getClientOriginalName();
                if ($existing) {
                    // remove old path
                    if ($existing->path && Storage::disk('public')->exists($existing->path)) {
                        Storage::disk('public')->delete($existing->path);
                    }
                    $existing->update(['name' => $orig, 'path' => $path]);
                } else {
                    SmStaffDocument::create([
                        'staff_id' => $staff->id,
                        'group' => 'family',
                        'key' => $key,
                        'name' => $orig,
                        'path' => $path,
                    ]);
                }
            };

            if ($request->hasFile('father_attachment')) {
                $updateOrCreateFamilyDoc('father_attachment', $request->file('father_attachment'));
            }
            if ($request->hasFile('mother_attachment')) {
                $updateOrCreateFamilyDoc('mother_attachment', $request->file('mother_attachment'));
            }
            if ($request->hasFile('spouse_attachment')) {
                $updateOrCreateFamilyDoc('spouse_attachment', $request->file('spouse_attachment'));
                // keep legacy column
                $saPath = SmStaffDocument::where('staff_id', $staff->id)->where('group', 'family')->where('key', 'spouse_attachment')->value('path');
                if ($saPath) {
                    $staff->spouse_attachment = $saPath;
                    $staff->save();
                }
            }

            // Update or create linked User
            $user = null;
            if (!empty($staff->user_id)) {
                $user = User::find($staff->user_id);
            }
            if (!$user) {
                $user = User::where('email', $staff->email)->first();
            }
            if (!$user)
                $user = new User();

            $fullName = trim(implode(' ', array_filter([$staff->first_name, $staff->last_name])));
            $username = $request->input('username') ?: (strpos($staff->email, '@') !== false ? strstr($staff->email, '@', true) : Str::slug($fullName, '_'));

            $user->role_id = (int) ($request->input('role_id') ?: $user->role_id ?: 2);
            $user->full_name = $fullName;
            $user->username = $username;
            $user->email = $staff->email;
            $user->usertype = $request->input('usertype', $user->usertype ?: 'staff');
            $user->access_status = 1;
            if ($request->filled('password')) {
                $user->password = Hash::make($request->input('password'));
            }
            $user->save();

            // Ensure staff.user_id is set
            if (empty($staff->user_id) || $staff->user_id !== $user->id) {
                $staff->user_id = $user->id;
                $staff->save();
            }

            // Job details: update existing or create
            $job = SmStaffJobDetail::where('staff_id', $staff->id)->first();
            $jobPayload = [
                'staff_id' => $staff->id,
                'date_of_joining' => $parseDate($request->input('date_of_joining_2')),
                'probation_end_date' => $parseDate($request->input('probation_end_date')),
                'department_id' => $request->input('department_id'),
                'designation_id' => $request->input('designation_id'),
                'reporting_manager' => $request->input('reporting_manager'),
                'employment_type' => $request->input('employment_type'),
                'visa_company_name' => $request->input('visa_company_name'),
                'working_company_name' => $request->input('working_company_name'),
                'company_access' => is_array($request->input('company_access')) ? implode(',', $request->input('company_access')) : $request->input('company_access'),
                'ext_no' => $request->input('ext_no_2'),
                'company_email' => $request->input('company_email'),
                'company_mobile' => $request->input('company_mobile'),
                'work_location' => $request->input('work_location'),
                'work_hours' => $request->input('work_hours'),
                'week_off' => is_array($request->input('hr_weekly_off')) ? implode(',', $request->input('hr_weekly_off')) : $request->input('hr_weekly_off'),
                'salary_basic' => $this->numOrNull($request->input('salary_basic')),
                'salary_allowances' => $this->numOrNull($request->input('salary_allowances')),
                'salary_other_allowances' => $this->numOrNull($request->input('salary_other_allowances')),
                'transport_allowance' => $this->numOrNull($request->input('transport_allowance')),
                'other_benefits' => $this->numOrNull($request->input('other_benefits')),
                'salary_gross' => $this->numOrNull($request->input('salary_gross')),
                'is_target' => (int) ($request->input('is_target') ?: 0),
                'target_month_from' => $parseDate($request->input('target_month_from')),
                'brand_ids' => $request->input('brands', []),
                'role_id' => $request->input('role_id'),
                'grade' => $request->input('grade')
            ];

            if ($job) {
                $job->update($jobPayload);
            } else {
                $job = SmStaffJobDetail::create($jobPayload);
            }

            // Job attachments: replace only when new files are uploaded; otherwise keep existing
            if ($request->hasFile('att_resume')) {
                if ($job->att_resume && Storage::disk('public')->exists($job->att_resume)) {
                    Storage::disk('public')->delete($job->att_resume);
                }
                $job->att_resume = $storeFile($request->file('att_resume'), 'employee/job');
            }
            if ($request->hasFile('att_offer_letter')) {
                if ($job->att_offer_letter && Storage::disk('public')->exists($job->att_offer_letter)) {
                    Storage::disk('public')->delete($job->att_offer_letter);
                }
                $job->att_offer_letter = $storeFile($request->file('att_offer_letter'), 'employee/job');
            }
            if ($request->hasFile('att_signed_contract')) {
                if ($job->att_signed_contract && Storage::disk('public')->exists($job->att_signed_contract)) {
                    Storage::disk('public')->delete($job->att_signed_contract);
                }
                $job->att_signed_contract = $storeFile($request->file('att_signed_contract'), 'employee/job');
            }
            $job->save();

            // Resignation: same handling as storeBasic (update or create)
            if (
                $request->filled('resignation_type') ||
                $request->filled('resignation_submitted_date') ||
                $request->filled('resignation_status') ||
                $request->filled('assets_returned') ||
                $request->filled('exit_interview_conducted') ||
                $request->filled('settlement_amount') ||
                $request->hasFile('resignation_letter') ||
                $request->hasFile('other_resignation_docs')
            ) {
                $eosData = [
                    'employee_id' => $staff->id,
                    'department_id' => $staff->department_id ?: 0,
                    'designation_id' => $staff->designation_id ?: 0,
                    'reporting_manager_id' => $request->input('reporting_manager'),
                    'separation_type' => 1,
                    'resignation_type' => $this->getResignationTypeValue($request->resignation_type),
                    'initiated_by' => $this->getInitiatedByValue($request->initiated_by),
                    'reason_category' => $this->getReasonCategoryValue($request->resignation_reason),
                    'detailed_reason' => $request->resignation_remarks ?: 'N/A',
                    'status' => $this->getStatusValue($request->resignation_status),
                    'created_by' => Auth::id() ?: 1,
                    'updated_by' => Auth::id() ?: 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $endOfService = EndOfService::updateOrCreate(['employee_id' => $staff->id], $eosData);
                $endOfServiceId = $endOfService->id;

                EndOfServiceNotice::updateOrCreate(
                    ['end_of_service_id' => $endOfServiceId],
                    [
                        'end_of_service_id' => $endOfServiceId,
                        'notice_waiver' => $this->getYesNoValue($request->notice_waiver),
                        'resignation_submitted_date' => $this->formatDateOrNull($request->resignation_submitted_date) ?: now()->format('Y-m-d'),
                        'notice_period_days' => $request->notice_period_days && is_numeric($request->notice_period_days) ? $request->notice_period_days : null,
                        'last_working_day' => $this->formatDateOrNull($request->last_working_day),
                        'garden_leave_applicable' => $this->getYesNoValue($request->garden_leave_applicable),
                        'garden_leave_start_date' => $this->formatDateOrNull($request->garden_leave_start_date),
                        'garden_leave_end_date' => $this->formatDateOrNull($request->garden_leave_end_date),
                        'relieving_date' => $this->formatDateOrNull($request->relieving_date),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );

                EndOfServiceHandover::updateOrCreate(
                    ['end_of_service_id' => $endOfServiceId],
                    [
                        'end_of_service_id' => $endOfServiceId,
                        'knowledge_transfer_required' => $this->getYesNoValue($request->knowledge_transfer_completed),
                        'handover_to' => $request->handover_to ?: null,
                        'handover_notes' => $request->handover_notes ?: null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );

                if ($request->filled('assets_returned') || $request->filled('other_resignation_docs_remarks')) {
                    $clearanceValue = $request->assets_returned === 'yes' ? 'completed' : 'pending';
                    EndOfServiceAssetClearance::updateOrCreate(
                        ['end_of_service_id' => $endOfServiceId],
                        [
                            'end_of_service_id' => $endOfServiceId,
                            'clearance_status' => $this->getClearanceStatusValue($clearanceValue),
                            'remarks' => $request->other_resignation_docs_remarks ?: null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }

                if ($request->hasFile('resignation_letter') || $request->hasFile('other_resignation_docs')) {
                    $documents = [];
                    if ($request->hasFile('resignation_letter')) {
                        $documents[] = [
                            'end_of_service_id' => $endOfServiceId,
                            'document_name' => 'Resignation Letter',
                            'document_date' => now()->toDateString(),
                            'attachment' => $request->file('resignation_letter')->store('eos/documents'),
                            'remarks' => $request->resignation_letter_remarks ?: null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                    if ($request->hasFile('other_resignation_docs')) {
                        foreach ($request->file('other_resignation_docs') as $f) {
                            if ($f && $f->isValid()) {
                                $documents[] = [
                                    'end_of_service_id' => $endOfServiceId,
                                    'document_name' => 'Other Resignation Document',
                                    'document_date' => now()->toDateString(),
                                    'attachment' => $f->store('eos/documents'),
                                    'remarks' => $request->other_resignation_docs_remarks ?: null,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ];
                            }
                        }
                    }
                    foreach ($documents as $document) {
                        EndOfServiceDocument::create($document);
                    }
                }
            }

            // 4) Banks: replace existing banks with provided ones
            SmStaffBankDetail::where('staff_id', $staff->id)->delete();
            foreach ($request->input('banks', []) as $i => $b) {
                $bankData = [
                    'staff_id' => $staff->id,
                    'bank_name' => $b['bank_name'] ?? null,
                    'bank_branch' => $b['branch_name'] ?? null,
                    'bank_ac_holder' => $b['account_holder'] ?? null,
                    'bank_ac_number' => $b['account_number'] ?? null,
                    'iban_number' => isset($b['iban_number']) ? strtoupper(preg_replace('/\s+/', '', $b['iban_number'])) : null,
                    'swift_code' => isset($b['swift_code']) ? strtoupper(trim($b['swift_code'])) : null,
                    'bank_currency' => isset($b['currency']) ? strtoupper(trim($b['currency'])) : null,
                ];
                if (!empty($b['iban_letter_existing'])) {
                    $bankData['att_iban_letter'] = $b['iban_letter_existing'];
                }
                if ($request->hasFile("banks.$i.iban_letter")) {
                    $bankData['att_iban_letter'] = $storeFile($request->file("banks.$i.iban_letter"), 'employee/banks');
                }
                SmStaffBankDetail::create($bankData);
            }

            // 5) Educations: replace
            SmStaffEducationQualification::where('staff_id', $staff->id)->delete();
            foreach ($request->input('educations', []) as $i => $ed) {
                $eduData = [
                    'staff_id' => $staff->id,
                    'qualification' => $ed['qualification'] ?? null,
                    'university' => $ed['university'] ?? $ed['institution'] ?? null,
                    'specialization' => $ed['specialization'] ?? null,
                    'year' => $ed['year'] ?? null,
                    'result' => $ed['result'] ?? null,
                    'gpa' => $ed['gpa'] ?? null,
                    'mode' => $ed['mode'] ?? null,
                    'country' => $ed['country'] ?? null,
                    'duration_years' => $ed['duration'] ?? $ed['duration_years'] ?? null,
                ];
                if ($request->hasFile("educations.$i.certificate")) {
                    $eduData['certificate_path'] = $storeFile($request->file("educations.$i.certificate"), 'employee/education');
                } elseif (!empty($ed['certificate_existing'])) {
                    $eduData['certificate_path'] = $ed['certificate_existing'];
                }
                SmStaffEducationQualification::create($eduData);
            }

            // 6) Experiences: replace
            SmStaffProfessionalExperience::where('staff_id', $staff->id)->delete();
            foreach ($request->input('experiences', []) as $i => $ex) {
                $expData = [
                    'staff_id' => $staff->id,
                    'organization' => $ex['organization'] ?? null,
                    'designation' => $ex['designation'] ?? null,
                    'years' => isset($ex['years']) ? (int) $ex['years'] : null,
                    'months' => isset($ex['months']) ? (int) $ex['months'] : null,
                    'responsibilities' => $ex['responsibilities'] ?? null,
                ];
                if ($request->hasFile("experiences.$i.certificate")) {
                    $expData['certificate_path'] = $storeFile($request->file("experiences.$i.certificate"), 'employee/experience');
                } elseif (!empty($ex['certificate_existing'])) {
                    $expData['certificate_path'] = $ex['certificate_existing'];
                }
                SmStaffProfessionalExperience::create($expData);
            }

            // 7) Documents (joining, employment, others) - remove existing groups and recreate
            SmStaffDocument::where('staff_id', $staff->id)->whereIn('group', ['joining', 'employment', 'others'])->delete();
            $docs = $request->input('docs', []);

            // joining
            foreach ($docs['joining'] ?? [] as $key => $row) {
                $remarks = $row['remarks'] ?? null;
                $number = $row['number'] ?? null;
                $expiry = $parseDate($row['expiry'] ?? null);
                $path = null;
                $origName = $row['label'] ?? ucfirst(str_replace('_', ' ', $key));
                if ($request->hasFile("docs.joining.$key.file")) {
                    $file = $request->file("docs.joining.$key.file");
                    $origName = $file->getClientOriginalName();
                    $path = $storeFile($file, 'employee/docs/joining');
                } elseif (!empty($row['existing'])) {
                    $path = $row['existing'];
                }
                if (!$path && !$remarks)
                    continue;
                SmStaffDocument::create([
                    'staff_id' => $staff->id,
                    'group' => 'joining',
                    'key' => $key,
                    'name' => $origName,
                    'path' => $path,
                    'remarks' => $remarks,
                    'expiry_date' => $expiry,
                    'document_number' => $number,
                ]);
            }

            // employment
            foreach ($docs['employment'] ?? [] as $key => $row) {
                $remarks = $row['remarks'] ?? null;
                $number = $row['number'] ?? null;
                $path = null;
                $origName = $row['label'] ?? ucfirst(str_replace('_', ' ', $key));
                if ($request->hasFile("docs.employment.$key.file")) {
                    $file = $request->file("docs.employment.$key.file");
                    $origName = $file->getClientOriginalName();
                    $path = $storeFile($file, 'employee/docs/employment');
                } elseif (!empty($row['existing'])) {
                    $path = $row['existing'];
                }
                if (!$path && !$remarks && !$number)
                    continue;
                SmStaffDocument::create([
                    'staff_id' => $staff->id,
                    'group' => 'employment',
                    'key' => $key,
                    'name' => $origName,
                    'path' => $path,
                    'remarks' => $remarks,
                    'document_number' => $number,
                ]);
            }

            // others
            $otherIndex = 0;

            foreach ($docs['others'] ?? [] as $i => $row) {

                $otherIndex++;
                $name = trim((string) ($row['name'] ?? ''));
                $number = $row['number'] ?? null;
                $remarks = $row['remarks'] ?? null;
                $path = null;
                if ($request->hasFile("docs.others.$i.file")) {
                    $file = $request->file("docs.others.$i.file");
                    $origName = $file->getClientOriginalName();
                    $name = $name ?: $origName;
                    $path = $storeFile($file, 'employee/docs/others');
                } elseif (!empty($row['existing'])) {
                    $path = $row['existing'];
                }
                if (!$path)
                    continue;
                SmStaffDocument::create([
                    'staff_id' => $staff->id,
                    'group' => 'others',
                    'key' => 'other_' . $otherIndex,
                    'name' => $name ?: 'Other Document',
                    'path' => $path,
                    'remarks' => $remarks,
                    'document_number' => $number,
                ]);
            }

            DB::commit();

            Toastr::success('Staff updated successfully', 'Success');
            return redirect('staff-directory/' . $staff->id);
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            \Log::error('Failed to update staff: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            Toastr::error('Operation Failed. Please try again.', 'Failed');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Save session data (education, experience, banks) to database
     */
    private function saveSessionDataToDatabase($staffId)
    {
        // Save Education from session
        $educations = session('staff_educations', []);
        if (!empty($educations)) {
            foreach ($educations as $edu) {
                SmStaffEducationQualification::updateOrCreate(
                    [
                        'staff_id' => $staffId,
                        'qualification' => $edu['qualification'] ?? null,
                        'university' => $edu['university'] ?? null,
                        'year' => $edu['year'] ?? null,
                    ],
                    [
                        'specialization' => $edu['specialization'] ?? null,
                        'result' => $edu['result'] ?? null,
                        'gpa' => $edu['gpa'] ?? null,
                        'mode' => $edu['mode'] ?? null,
                        'country' => $edu['country'] ?? null,
                        'duration_years' => $edu['duration'] ?? $edu['duration_years'] ?? null,
                        'certificate_path' => $edu['certificate_path'] ?? null,
                    ]
                );
            }
        }

        // Save Experience from session
        $experiences = session('staff_experiences', []);
        if (!empty($experiences)) {
            foreach ($experiences as $exp) {
                SmStaffProfessionalExperience::updateOrCreate(
                    [
                        'staff_id' => $staffId,
                        'organization' => $exp['organization'] ?? null,
                    ],
                    [
                        'designation' => $exp['designation'] ?? null,
                        'years' => $exp['years'] ?? 0,
                        'months' => $exp['months'] ?? 0,
                        'responsibilities' => $exp['responsibilities'] ?? null,
                        'certificate_path' => $exp['certificate_path'] ?? null,
                    ]
                );
            }
        }

        // Save Banks from session
        $banks = session('staff_banks', []);
        if (!empty($banks)) {
            foreach ($banks as $bank) {
                SmStaffBankDetail::updateOrCreate(
                    [
                        'staff_id' => $staffId,
                        'bank_ac_number' => $bank['account_number'] ?? null,
                    ],
                    [
                        'bank_name' => $bank['bank_name'] ?? null,
                        'bank_branch' => $bank['branch_name'] ?? null,
                        'bank_ac_holder' => $bank['account_holder'] ?? null,
                        'iban_number' => $bank['iban_number'] ?? null,
                        'swift_code' => $bank['swift_code'] ?? null,
                        'bank_currency' => $bank['currency'] ?? null,
                        'att_iban_letter' => $bank['iban_letter'] ?? null,
                    ]
                );
            }
        }
    }

    /**
     * Create/Update Job Details for a staff
     */

    private function numOrNull($v)
    {
        return ($v === null || $v === '') ? null : (float) str_replace(',', '', $v);
    }

    public function storeJob(Request $req)
    {

        // Validate
        $req->validate([
            'staff_id' => ['required', 'integer', 'exists:sm_staffs,id'],

            'date_of_joining_2' => ['required', 'string'], // dd/mm/YYYY from UI
            'role_id' => ['required', 'integer'],          // adjust table if yours differs
            'designation_id' => ['required', 'integer'],   // adjust table if yours differs
            'department_id' => ['required', 'integer'],    // adjust table if yours differs
            'employment_type' => ['required', Rule::in(['full_time', 'part_time', 'contract', 'intern'])],


            'reporting_manager' => ['required', 'array'],
            'reporting_manager.*' => ['integer'],
            'work_location' => ['nullable', 'string', 'max:150'],
            'work_hours' => ['nullable', 'string', 'max:100'],
            'ext_no_2' => ['nullable', 'string', 'max:50'],

            'salary_basic' => ['nullable', 'numeric'],
            'salary_allowances' => ['nullable', 'numeric'],
            'salary_other_allowances' => ['nullable', 'numeric'],
            'salary_gross' => ['nullable', 'numeric'],

            // 'visa_company_name'    => ['required','integer'], // you submit company id
            'working_company_name' => ['required', 'integer'],

            'company_access' => ['required', 'array'],
            'company_access.*' => ['integer'],

            'is_target' => ['nullable', 'integer', 'in:0,1'],
            'revenue_target_weekly' => ['required_if:is_target,1', 'nullable', 'numeric'],
            'revenue_target_monthly' => ['required_if:is_target,1', 'nullable', 'numeric'],
            'revenue_target_quaterly' => ['required_if:is_target,1', 'nullable', 'numeric'],
            'revenue_target_yearly' => ['required_if:is_target,1', 'nullable', 'numeric'],
            'gp_target_weekly' => ['required_if:is_target,1', 'nullable', 'numeric'],
            'gp_target_monthly' => ['required_if:is_target,1', 'nullable', 'numeric'],
            'gp_target_quaterly' => ['required_if:is_target,1', 'nullable', 'numeric'],
            'gp_target_yearly' => ['required_if:is_target,1', 'nullable', 'numeric'],
            'target_month_from' => ['nullable', 'date_format:Y-m'],

            'brands' => ['nullable', 'array'],
            'brands.*' => ['integer'],
            'combind_user_id' => ['nullable', 'array'],
            'combind_user_id.*' => ['integer'],

            'att_resume' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:4096'],
            'att_offer_letter' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:4096'],
            'att_signed_contract' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:4096'],
        ], [], [
            'date_of_joining_2' => 'Date of Joining',
            'week_off' => 'Week Off',
            'ext_no_2' => 'Ext No',
            'company_access' => 'Company Access',
            'working_company_name' => 'Main Company',
            'visa_company_name' => 'Company',
        ]);




        // Convert Date of Joining to Y-m-d
        $doj = null;
        if ($req->filled('date_of_joining_2')) {
            try {
                $doj = Carbon::createFromFormat('d/m/Y', $req->input('date_of_joining_2'))->format('Y-m-d');
            } catch (\Exception $e) {
                $doj = Carbon::parse($req->input('date_of_joining_2'))->format('Y-m-d');
            }
        }

        // Build payload
        $payload = [
            'staff_id' => (int) $req->input('staff_id'),
            'date_of_joining' => $doj,
            'role_id' => (int) $req->input('role_id'),
            'designation_id' => (int) $req->input('designation_id'),
            'department_id' => (int) $req->input('department_id'),
            'employment_type' => $req->input('employment_type'),
            'reporting_manager' => $req->input('reporting_manager', []),
            'work_location' => $req->input('work_location'),
            'work_hours' => $req->input('work_hours'),
            'ext_no' => $req->input('ext_no_2'),
            'visa_company_name' => (int) $req->input('visa_company_name'),
            'working_company_name' => (int) $req->input('working_company_name'),
            'company_access' => $req->input('company_access', []),
            'is_target' => (int) $req->input('is_target', 0),
            'target_month_from' => $req->input('target_month_from'),
            'brand_ids' => $req->input('brands', []),
            'combind_user_ids' => $req->input('combind_user_id', []),
            'week_off' => $req->input('week_off'),
        ];

        // Normalize ALL numeric fields to null or float
        foreach ([
            'salary_basic',
            'salary_allowances',
            'salary_other_allowances',
            'salary_gross',
            'revenue_target_weekly',
            'revenue_target_monthly',
            'revenue_target_quaterly',
            'revenue_target_yearly',
            'gp_target_weekly',
            'gp_target_monthly',
            'gp_target_quaterly',
            'gp_target_yearly',
        ] as $key) {
            $payload[$key] = $this->numOrNull($req->input($key));
        }

        // Auto compute gross if it's null but parts exist
        if (is_null($payload['salary_gross'])) {
            $b = (float) ($payload['salary_basic'] ?? 0);
            $a = (float) ($payload['salary_allowances'] ?? 0);
            $o = (float) ($payload['salary_other_allowances'] ?? 0);
            $sum = $b + $a + $o;
            $payload['salary_gross'] = $sum > 0 ? $sum : null;
        }

        // Upload docs
        foreach (['att_resume', 'att_offer_letter', 'att_signed_contract'] as $field) {
            if ($req->hasFile($field)) {
                $path = $req->file($field)->store('staff_docs', 'public');
                $payload[$field] = 'storage/' . $path;
            }
        }

        $job = SmStaffJobDetail::updateOrCreate(
            ['staff_id' => (int) $req->input('staff_id')],
            $payload
        );

        return response()->json([
            'ok' => true,
            'message' => 'Job details saved.',
            'job_id' => $job->id,
        ]);
    }

    public function storeBank(Request $req)
    {
        $req->validate([
            'staff_id' => ['required', 'integer', 'exists:sm_staffs,id'],
            'bank_name' => ['required', 'string', 'max:150'],
            'bank_branch' => ['nullable', 'string', 'max:150'],
            'bank_ac_holder' => ['required', 'string', 'max:150'],
            'bank_ac_number' => ['required', 'string', 'max:50'],
            'iban_number' => ['required'],
            'swift_code' => ['nullable', 'string', 'max:20'],
            'bank_currency' => ['nullable', 'string', 'max:10'],
            'att_iban_letter' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
        ], [], [
            'iban_number' => 'IBAN Number',
            'bank_ac_holder' => 'Account Holder Name',
        ]);

        // Clean up IBAN (uppercase, remove spaces)
        $iban = strtoupper(preg_replace('/\s+/', '', $req->input('iban_number')));

        $payload = [
            'staff_id' => (int) $req->input('staff_id'),
            'bank_name' => $req->input('bank_name'),
            'bank_branch' => $req->input('bank_branch'),
            'bank_ac_holder' => $req->input('bank_ac_holder'),
            'bank_ac_number' => $req->input('bank_ac_number'),
            'iban_number' => $iban,
            'swift_code' => strtoupper($req->input('swift_code', '')),
            'bank_currency' => strtoupper($req->input('bank_currency', '')),
        ];

        if ($req->hasFile('att_iban_letter')) {
            $path = $req->file('att_iban_letter')->store('bank_docs', 'public');
            $payload['att_iban_letter'] = 'storage/' . $path;
        }

        $bank = SmStaffBankDetail::updateOrCreate(
            ['staff_id' => (int) $req->input('staff_id')],
            $payload
        );

        return response()->json([
            'ok' => true,
            'message' => 'Bank details saved.',
            'bank_id' => $bank->id,
        ]);
    }

    // Session-based Education Management (for Add Staff form)
    public function storeEducationSession(Request $request)
    {
        $request->validate([
            'qualification' => 'required|string|max:150',
            'university' => 'required|string|max:200',
            'specialization' => 'nullable|string|max:150',
            'year' => 'nullable',
            'result' => 'nullable|string|max:100',
            'gpa' => 'nullable|numeric',
            'mode' => 'nullable|string',
            'country' => 'nullable|string|max:100',
            'duration' => 'nullable|numeric',
            'certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ]);

        $educations = session('staff_educations', []);
        $educationId = $request->input('education_id');

        // Handle file upload
        $certificatePath = null;
        if ($request->hasFile('certificate')) {
            $file = $request->file('certificate');
            $path = $file->store('temp_education_certs', 'public');
            $certificatePath = 'storage/' . $path;
        }

        $educationData = [
            'qualification' => $request->input('qualification'),
            'university' => $request->input('university'),
            'specialization' => $request->input('specialization'),
            'year' => $request->input('year'),
            'result' => $request->input('result'),
            'gpa' => $request->input('gpa'),
            'mode' => $request->input('mode'),
            'country' => $request->input('country'),
            'duration' => $request->input('duration'),
            'certificate' => $certificatePath,
        ];

        if ($educationId && isset($educations[$educationId])) {
            // Edit existing
            if (!$certificatePath && isset($educations[$educationId]['certificate'])) {
                $educationData['certificate'] = $educations[$educationId]['certificate'];
            }
            $educations[$educationId] = array_merge($educations[$educationId], $educationData);
        } else {
            // Add new
            $newId = count($educations) > 0 ? max(array_keys($educations)) + 1 : 1;
            $educationData['id'] = $newId;
            $educations[$newId] = $educationData;
        }

        session(['staff_educations' => $educations]);

        return response()->json([
            'success' => true,
            'message' => 'Education saved to session',
            'educations' => array_values($educations)
        ]);
    }

    public function deleteEducationSession(Request $request)
    {
        $educations = session('staff_educations', []);
        $id = $request->input('id');

        if (isset($educations[$id])) {
            // Delete uploaded file if exists
            if (isset($educations[$id]['certificate']) && $educations[$id]['certificate']) {
                $filePath = str_replace('storage/', '', $educations[$id]['certificate']);
                Storage::disk('public')->delete($filePath);
            }
            unset($educations[$id]);
        }

        session(['staff_educations' => $educations]);

        return response()->json([
            'success' => true,
            'message' => 'Education deleted from session',
            'educations' => array_values($educations)
        ]);
    }

    public function saveAllEducations(Request $request)
    {
        // This will be called when main staff form is saved
        // Move education from session to database
        $staffId = $request->input('staff_id');
        $educations = session('staff_educations', []);

        if (empty($educations)) {
            return response()->json(['success' => true, 'message' => 'No educations to save']);
        }

        foreach ($educations as $edu) {
            // Move file from temp to permanent storage if needed
            $certificatePath = $edu['certificate'] ?? null;
            if ($certificatePath && strpos($certificatePath, 'temp_') !== false) {
                $oldPath = str_replace('storage/', '', $certificatePath);
                $newPath = str_replace('temp_education_certs/', 'education_certs/', $oldPath);
                Storage::disk('public')->move($oldPath, $newPath);
                $certificatePath = 'storage/' . $newPath;
            }

            SmStaffEducationQualification::create([
                'staff_id' => $staffId,
                'qualification' => $edu['qualification'] ?? null,
                'university' => $edu['university'] ?? null,
                'specialization' => $edu['specialization'] ?? null,
                'year' => $edu['year'] ?? null,
                'result' => $edu['result'] ?? null,
                'gpa' => $edu['gpa'] ?? null,
                'mode' => $edu['mode'] ?? null,
                'country' => $edu['country'] ?? null,
                'duration_years' => $edu['duration'] ?? null,
                'certificate_path' => $certificatePath,
            ]);
        }

        // Clear session
        session()->forget('staff_educations');

        return response()->json(['success' => true, 'message' => 'All educations saved to database']);
    }

    public function storeEducation(Request $req)
    {
        // Base rules (all nullable since the whole tab is optional)
        $req->validate([
            'staff_id' => ['required', 'integer', 'exists:sm_staffs,id'],
            'education' => ['nullable', 'array'],
            // keep everything nullable here
            'education.*.qualification' => ['nullable', 'string', 'max:150'],
            'education.*.university' => ['nullable', 'string', 'max:200'],
            'education.*.specialization' => ['nullable', 'string', 'max:150'],
            'education.*.year' => ['nullable', 'digits:4'],
            'education.*.result' => ['nullable', 'string', 'max:100'],
            'education.*.gpa' => ['nullable', 'numeric'],
            'education.*.mode' => ['nullable', 'string', 'in:Full-Time,Part-Time,Distance,Online'],
            'education.*.country' => ['nullable', 'string', 'max:100'],
            'education.*.duration' => ['nullable', 'numeric'],
            'education.*.certificate' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:5120'],
            'education.*.certificate_existing' => ['nullable', 'string'],
        ]);

        $staffId = (int) $req->input('staff_id');
        $rows = $req->input('education', []);

        // Helper: decide if a row is "filled" (i.e., user actually entered something or uploaded a file)
        $isFilled = function (array $row, int $i) use ($req): bool {
            $texty = ['qualification', 'university', 'specialization', 'year', 'result', 'gpa', 'mode', 'country', 'duration'];
            foreach ($texty as $k) {
                if (isset($row[$k]) && trim((string) $row[$k]) !== '')
                    return true;
            }
            if ($req->hasFile("education.$i.certificate"))
                return true;
            if (!empty($row['certificate_existing']))
                return true;
            return false;
        };

        // Collect only rows the user actually filled
        $active = [];
        foreach ($rows as $i => $row) {
            if ($isFilled((array) $row, (int) $i)) {
                $active[(int) $i] = (array) $row;
            }
        }

        // If nothing filled, SKIP silently (no changes, no errors)
        if (empty($active)) {
            return response()->json(['ok' => true, 'message' => 'Education skipped (no entries).']);
        }

        // Per-row conditional requirements ONLY for filled rows
        $errors = [];
        foreach ($active as $i => $row) {
            if (empty($row['qualification'])) {
                $errors["education.$i.qualification"][] = 'Highest Qualification is required.';
            }
            if (empty($row['university'])) {
                $errors["education.$i.university"][] = 'Board / University is required.';
            }
            $hasNewFile = $req->hasFile("education.$i.certificate");
            $hasExisting = !empty($row['certificate_existing']);
            if (!$hasNewFile && !$hasExisting) {
                $errors["education.$i.certificate"][] = 'Certificate is required.';
            }
        }
        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }

        // Persist: replace all rows for this staff with only the active ones
        DB::transaction(function () use ($req, $staffId, $active) {
            SmStaffEducationQualification::where('staff_id', $staffId)->delete();

            foreach ($active as $i => $row) {
                // handle certificate path
                $certPath = $row['certificate_existing'] ?? null;
                if ($req->hasFile("education.$i.certificate")) {
                    $path = $req->file("education.$i.certificate")->store('education_certs', 'public');
                    $certPath = 'storage/' . $path;
                }

                SmStaffEducationQualification::create([
                    'staff_id' => $staffId,
                    'qualification' => $row['qualification'] ?? null,
                    'university' => $row['university'] ?? null,
                    'specialization' => $row['specialization'] ?? null,
                    'year' => $row['year'] ?? null,
                    'result' => $row['result'] ?? null,
                    'gpa' => ($row['gpa'] === '' ? null : $row['gpa']),
                    'mode' => $row['mode'] ?? null,
                    'country' => $row['country'] ?? null,
                    'duration_years' => ($row['duration'] === '' ? null : $row['duration']),
                    'certificate_path' => $certPath,
                ]);
            }
        });

        return response()->json(['ok' => true, 'message' => 'Educational qualifications saved.']);
    }

    // Session-based Experience Management (for Add Staff form)
    public function storeExperienceSession(Request $request)
    {
        $request->validate([
            'organization' => 'required|string|max:200',
            'designation' => 'nullable|string|max:150',
            'years' => 'nullable|integer|min:0',
            'months' => 'nullable|integer|min:0|max:11',
            'responsibilities' => 'nullable|string|max:1000',
            'certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ]);

        $experiences = session('staff_experiences', []);
        $experienceId = $request->input('experience_id');

        // Handle file upload
        $certificatePath = null;
        if ($request->hasFile('certificate')) {
            $file = $request->file('certificate');
            $path = $file->store('temp_experience_certs', 'public');
            $certificatePath = 'storage/' . $path;
        }

        $experienceData = [
            'organization' => $request->input('organization'),
            'designation' => $request->input('designation'),
            'years' => $request->input('years'),
            'months' => $request->input('months'),
            'responsibilities' => $request->input('responsibilities'),
            'certificate' => $certificatePath,
        ];

        if ($experienceId && isset($experiences[$experienceId])) {
            // Edit existing
            if (!$certificatePath && isset($experiences[$experienceId]['certificate'])) {
                $experienceData['certificate'] = $experiences[$experienceId]['certificate'];
            }
            $experiences[$experienceId] = array_merge($experiences[$experienceId], $experienceData);
        } else {
            // Add new
            $newId = count($experiences) > 0 ? max(array_keys($experiences)) + 1 : 1;
            $experienceData['id'] = $newId;
            $experiences[$newId] = $experienceData;
        }

        session(['staff_experiences' => $experiences]);

        return response()->json([
            'success' => true,
            'message' => 'Experience saved to session',
            'experiences' => array_values($experiences)
        ]);
    }

    public function deleteExperienceSession(Request $request)
    {
        $experiences = session('staff_experiences', []);
        $id = $request->input('id');

        if (isset($experiences[$id])) {
            // Delete uploaded file if exists
            if (isset($experiences[$id]['certificate']) && $experiences[$id]['certificate']) {
                $filePath = str_replace('storage/', '', $experiences[$id]['certificate']);
                Storage::disk('public')->delete($filePath);
            }
            unset($experiences[$id]);
        }

        session(['staff_experiences' => $experiences]);

        return response()->json([
            'success' => true,
            'message' => 'Experience deleted from session',
            'experiences' => array_values($experiences)
        ]);
    }

    public function saveAllExperiences(Request $request)
    {
        // This will be called when main staff form is saved
        // Move experience from session to database
        $staffId = $request->input('staff_id');
        $experiences = session('staff_experiences', []);

        if (empty($experiences)) {
            return response()->json(['success' => true, 'message' => 'No experiences to save']);
        }

        foreach ($experiences as $exp) {
            // Move file from temp to permanent storage if needed
            $certificatePath = $exp['certificate'] ?? null;
            if ($certificatePath && strpos($certificatePath, 'temp_') !== false) {
                $oldPath = str_replace('storage/', '', $certificatePath);
                $newPath = str_replace('temp_experience_certs/', 'experience_certs/', $oldPath);
                Storage::disk('public')->move($oldPath, $newPath);
                $certificatePath = 'storage/' . $newPath;
            }

            SmStaffProfessionalExperience::create([
                'staff_id' => $staffId,
                'organization' => $exp['organization'] ?? null,
                'designation' => $exp['designation'] ?? null,
                'years' => $exp['years'] ?? null,
                'months' => $exp['months'] ?? null,
                'responsibilities' => $exp['responsibilities'] ?? null,
                'certificate_path' => $certificatePath,
            ]);
        }

        // Clear session
        session()->forget('staff_experiences');

        return response()->json(['success' => true, 'message' => 'All experiences saved to database']);
    }

    // Session-based Bank Management (for Add Staff form)
    public function storeBankSession(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string|max:200',
            'branch_name' => 'nullable|string|max:200',
            'account_holder' => 'required|string|max:200',
            'account_number' => 'nullable|string|max:100',
            'iban_number' => 'required|string|max:100',
            'swift_code' => 'nullable|string|max:50',
            'currency' => 'nullable|string|max:10',
            'iban_letter' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ]);

        $banks = session('staff_banks', []);
        $bankId = $request->input('bank_id');

        // Handle file upload
        $ibanLetterPath = null;
        if ($request->hasFile('iban_letter')) {
            $file = $request->file('iban_letter');
            $path = $file->store('temp_iban_letters', 'public');
            $ibanLetterPath = 'storage/' . $path;
        }

        $bankData = [
            'bank_name' => $request->input('bank_name'),
            'branch_name' => $request->input('branch_name'),
            'account_holder' => $request->input('account_holder'),
            'account_number' => $request->input('account_number'),
            'iban_number' => $request->input('iban_number'),
            'swift_code' => $request->input('swift_code'),
            'currency' => $request->input('currency'),
            'iban_letter' => $ibanLetterPath,
        ];

        if ($bankId && isset($banks[$bankId])) {
            // Edit existing
            if (!$ibanLetterPath && isset($banks[$bankId]['iban_letter'])) {
                $bankData['iban_letter'] = $banks[$bankId]['iban_letter'];
            }
            $banks[$bankId] = array_merge($banks[$bankId], $bankData);
        } else {
            // Add new
            $newId = count($banks) > 0 ? max(array_keys($banks)) + 1 : 1;
            $bankData['id'] = $newId;
            $banks[$newId] = $bankData;
        }

        session(['staff_banks' => $banks]);

        return response()->json([
            'success' => true,
            'message' => 'Bank account saved to session',
            'banks' => array_values($banks)
        ]);
    }

    public function deleteBankSession(Request $request)
    {
        $banks = session('staff_banks', []);
        $id = $request->input('bank_id');

        if (isset($banks[$id])) {
            // Delete uploaded file if exists
            if (isset($banks[$id]['iban_letter']) && $banks[$id]['iban_letter']) {
                $filePath = str_replace('storage/', '', $banks[$id]['iban_letter']);
                Storage::disk('public')->delete($filePath);
            }
            unset($banks[$id]);
        }

        session(['staff_banks' => $banks]);

        return response()->json([
            'success' => true,
            'message' => 'Bank account deleted from session',
            'banks' => array_values($banks)
        ]);
    }

    public function saveAllBanks(Request $request)
    {
        // This will be called when main staff form is saved
        // Move banks from session to database
        $staffId = $request->input('staff_id');
        $banks = session('staff_banks', []);

        if (empty($banks)) {
            return response()->json(['success' => true, 'message' => 'No bank accounts to save']);
        }

        foreach ($banks as $bank) {
            // Move file from temp to permanent storage if needed
            $ibanLetterPath = $bank['iban_letter'] ?? null;
            if ($ibanLetterPath && strpos($ibanLetterPath, 'temp_') !== false) {
                $oldPath = str_replace('storage/', '', $ibanLetterPath);
                $newPath = str_replace('temp_iban_letters/', 'iban_letters/', $oldPath);
                Storage::disk('public')->move($oldPath, $newPath);
                $ibanLetterPath = 'storage/' . $newPath;
            }

            SmStaffBankDetail::create([
                'staff_id' => $staffId,
                'bank_name' => $bank['bank_name'] ?? null,
                'branch_name' => $bank['branch_name'] ?? null,
                'account_holder' => $bank['account_holder'] ?? null,
                'account_number' => $bank['account_number'] ?? null,
                'iban_number' => $bank['iban_number'] ?? null,
                'swift_code' => $bank['swift_code'] ?? null,
                'currency' => $bank['currency'] ?? null,
                'iban_letter_path' => $ibanLetterPath,
            ]);
        }

        // Clear session
        session()->forget('staff_banks');

        return response()->json(['success' => true, 'message' => 'All bank accounts saved to database']);
    }

    public function storeExperience(Request $req)
    {
        // Tab is optional; validate shape only
        $req->validate([
            'staff_id' => ['required', 'integer', 'exists:sm_staffs,id'],
            'experience' => ['nullable', 'array'],

            'experience.*.organization' => ['nullable', 'string', 'max:200'],
            'experience.*.designation' => ['nullable', 'string', 'max:150'],
            'experience.*.years' => ['nullable', 'integer', 'min:0'],
            'experience.*.months' => ['nullable', 'integer', 'min:0', 'max:11'],
            'experience.*.responsibilities' => ['nullable', 'string', 'max:1000'],

            'experience.*.certificate' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:5120'],
            'experience.*.certificate_existing' => ['nullable', 'string'],
        ]);

        $staffId = (int) $req->input('staff_id');
        $rows = $req->input('experience', []);

        // consider a row "filled" if any field has a value or a file/existing cert present
        $isFilled = function (array $row, int $i) use ($req): bool {
            foreach (['organization', 'designation', 'years', 'months', 'responsibilities'] as $k) {
                if (isset($row[$k]) && trim((string) $row[$k]) !== '')
                    return true;
            }
            if ($req->hasFile("experience.$i.certificate"))
                return true;
            if (!empty($row['certificate_existing']))
                return true;
            return false;
        };

        $active = [];
        foreach ($rows as $i => $row) {
            if ($isFilled((array) $row, (int) $i)) {
                $active[(int) $i] = (array) $row;
            }
        }

        // if nothing filled, skip silently
        if (empty($active)) {
            return response()->json(['ok' => true, 'message' => 'Experience skipped (no entries).']);
        }

        // per-row conditional rules for filled rows
        $errors = [];
        foreach ($active as $i => $row) {
            if (empty($row['organization'])) {
                $errors["experience.$i.organization"][] = 'Previous Organization is required.';
            }
            // months range already validated above; years non-negative already validated
            // certificate is optional for experience; keep existing if provided
        }
        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }

        DB::transaction(function () use ($req, $staffId, $active) {
            // replace all rows for this staff (keeps code simple)
            SmStaffProfessionalExperience::where('staff_id', $staffId)->delete();

            foreach ($active as $i => $row) {
                $certPath = $row['certificate_existing'] ?? null;
                if ($req->hasFile("experience.$i.certificate")) {
                    $path = $req->file("experience.$i.certificate")->store('experience_certs', 'public');
                    $certPath = 'storage/' . $path;
                }

                SmStaffProfessionalExperience::create([
                    'staff_id' => $staffId,
                    'organization' => $row['organization'] ?? null,
                    'designation' => $row['designation'] ?? null,
                    'years' => ($row['years'] === '' ? null : (int) $row['years']),
                    'months' => ($row['months'] === '' ? null : (int) $row['months']),
                    'responsibilities' => $row['responsibilities'] ?? null,
                    'certificate_path' => $certPath,
                ]);
            }
        });

        return response()->json(['ok' => true, 'message' => 'Professional experience saved.']);
    }

    // public function storeDocs(Request $req)
    // {
    //     $req->validate([
    //         'staff_id' => ['required', 'integer', 'exists:sm_staffs,id'],
    //         'docs' => ['nullable', 'array'],
    //         // no hard required here; we'll enforce per-row below
    //     ]);

    //     $staffId = (int) $req->input('staff_id');
    //     $docs = $req->input('docs', []);

    //     // Helper to parse dd/mm/YYYY -> Y-m-d
    //     $parseDate = function ($val) {
    //         if (!$val)
    //             return null;
    //         try {
    //             return Carbon::createFromFormat('d/m/Y', $val)->format('Y-m-d');
    //         } catch (\Throwable $e) {
    //             try {
    //                 return Carbon::parse($val)->format('Y-m-d');
    //             } catch (\Throwable $e2) {
    //                 return null;
    //             }
    //         }
    //     };

    //     DB::transaction(function () use ($req, $staffId, $docs, $parseDate) {

    //         // Remove existing docs for idempotency (optional: only remove groups we’re writing)
    //         SmStaffDocument::where('staff_id', $staffId)->delete();

    //         // 1) JOINING DOCS
    //         foreach (($docs['joining'] ?? []) as $key => $row) {
    //             $remarks = $row['remarks'] ?? null;
    //             $expiry = $parseDate($row['expiry'] ?? null);

    //             // accept either uploaded file, or existing path from hidden inputs
    //             $path = null;

    //             if ($key === 'prof_certs') {
    //                 // May have multiple existing[]
    //                 $existing = (array) ($row['existing'] ?? []);
    //                 foreach ($existing as $ex) {
    //                     if ($ex) {
    //                         SmStaffDocument::create([
    //                             'staff_id' => $staffId,
    //                             'group' => 'joining',
    //                             'key' => 'prof_certs',
    //                             'name' => 'Professional Certificate',
    //                             'path' => $ex,
    //                             'remarks' => $remarks,
    //                             'expiry_date' => null,
    //                         ]);
    //                     }
    //                 }
    //                 if ($req->hasFile('docs.joining.prof_certs.file')) {
    //                     $upload = $req->file('docs.joining.prof_certs.file')->store('docs/joining', 'public');
    //                     SmStaffDocument::create([
    //                         'staff_id' => $staffId,
    //                         'group' => 'joining',
    //                         'key' => 'prof_certs',
    //                         'name' => 'Professional Certificate',
    //                         'path' => 'storage/' . $upload,
    //                         'remarks' => $remarks,
    //                         'expiry_date' => null,
    //                     ]);
    //                 }
    //                 continue;
    //             }

    //             // Generic single-file keys
    //             if ($req->hasFile("docs.joining.$key.file")) {
    //                 $upload = $req->file("docs.joining.$key.file")->store('docs/joining', 'public');
    //                 $path = 'storage/' . $upload;
    //             } elseif (!empty($row['existing'])) {
    //                 $path = $row['existing'];
    //             }

    //             // Enforce required for passport_visa & emirates_id
    //             if (in_array($key, ['passport_visa']) && !$path) {
    //                 return abort(response()->json([
    //                     'message' => 'The given data was invalid.',
    //                     'errors' => ["docs.joining.$key.file" => ["$key is required."]],
    //                 ], 422));
    //             }

    //             // Optional rows without path can be skipped
    //             if (!$path)
    //                 continue;

    //             SmStaffDocument::create([
    //                 'staff_id' => $staffId,
    //                 'group' => 'joining',
    //                 'key' => $key,
    //                 'name' => ucfirst(str_replace('_', ' ', $key)),
    //                 'path' => $path,
    //                 'remarks' => $remarks,
    //                 'expiry_date' => $expiry,
    //             ]);
    //         }

    //         // 2) EMPLOYMENT DOCS
    //         foreach (($docs['employment'] ?? []) as $key => $row) {
    //             $remarks = $row['remarks'] ?? null;
    //             $path = null;
    //             if ($req->hasFile("docs.employment.$key.file")) {
    //                 $upload = $req->file("docs.employment.$key.file")->store('docs/employment', 'public');
    //                 $path = 'storage/' . $upload;
    //             } elseif (!empty($row['existing'])) {
    //                 $path = $row['existing'];
    //             }
    //             if (!$path)
    //                 continue;
    //             SmStaffDocument::create([
    //                 'staff_id' => $staffId,
    //                 'group' => 'employment',
    //                 'key' => $key,
    //                 'name' => ucfirst(str_replace('_', ' ', $key)),
    //                 'path' => $path,
    //                 'remarks' => $remarks,
    //                 'expiry_date' => null,
    //             ]);
    //         }

    //         // 3) OTHERS (dynamic)
    //         foreach (($docs['others'] ?? []) as $i => $row) {
    //             $name = trim((string) ($row['name'] ?? ''));
    //             $remarks = $row['remarks'] ?? null;
    //             $path = null;
    //             if ($req->hasFile("docs.others.$i.file")) {
    //                 $upload = $req->file("docs.others.$i.file")->store('docs/others', 'public');
    //                 $path = 'storage/' . $upload;
    //             } elseif (!empty($row['existing'])) {
    //                 $path = $row['existing'];
    //             }
    //             // Skip blank rows
    //             if (!$name && !$path && !$remarks)
    //                 continue;

    //             // If user typed a name but no file, you can allow or enforce file; here we allow.
    //             SmStaffDocument::create([
    //                 'staff_id' => $staffId,
    //                 'group' => 'others',
    //                 'key' => null,
    //                 'name' => $name ?: 'Other Document',
    //                 'path' => $path,
    //                 'remarks' => $remarks,
    //                 'expiry_date' => null,
    //             ]);
    //         }
    //     });

    //     return response()->json(['ok' => true, 'message' => 'Documentation saved.']);
    // }

    public function storeDocs(Request $req)
    {
        $req->validate([
            'staff_id' => ['required', 'integer', 'exists:sm_staffs,id'],
            'docs' => ['nullable', 'array'],
        ]);

        $staffId = (int) $req->input('staff_id');
        $docs = $req->input('docs', []);

        $parseDate = function ($val) {
            if (!$val)
                return null;
            try {
                return Carbon::createFromFormat('d/m/Y', $val)->format('Y-m-d');
            } catch (\Throwable $e) {
                return null;
            }
        };

        DB::transaction(function () use ($req, $staffId, $docs, $parseDate) {

            SmStaffDocument::where('staff_id', $staffId)->delete();

            /* =======================
             * 1) JOINING DOCS
             * ======================= */
            foreach (($docs['joining'] ?? []) as $key => $row) {

                $remarks = $row['remarks'] ?? null;
                $expiry = $parseDate($row['expiry'] ?? null);
                $path = null;

                // Special case: professional certificates (multiple)
                if ($key === 'prof_certs') {

                    foreach ((array) ($row['existing'] ?? []) as $ex) {
                        if ($ex) {
                            SmStaffDocument::create([
                                'staff_id' => $staffId,
                                'group' => 'joining',
                                'key' => 'prof_certs',
                                'name' => 'Professional Certificate',
                                'path' => $ex,
                                'remarks' => $remarks,
                                'expiry_date' => null,
                            ]);
                        }
                    }

                    if ($req->hasFile('docs.joining.prof_certs.file')) {
                        $file = $req->file('docs.joining.prof_certs.file');
                        $path = $this->uploadFile($file, 'staff_docs/joining');

                        SmStaffDocument::create([
                            'staff_id' => $staffId,
                            'group' => 'joining',
                            'key' => 'prof_certs',
                            'name' => 'Professional Certificate',
                            'path' => $path,
                            'remarks' => $remarks,
                            'expiry_date' => null,
                        ]);
                    }
                    continue;
                }

                // Generic single-file docs
                if ($req->hasFile("docs.joining.$key.file")) {
                    $file = $req->file("docs.joining.$key.file");
                    $path = $this->uploadFile($file, 'staff_docs/joining');
                } elseif (!empty($row['existing'])) {
                    $path = $row['existing'];
                }

                // Required docs validation
                if (in_array($key, ['passport_visa']) && !$path) {
                    abort(response()->json([
                        'message' => 'The given data was invalid.',
                        'errors' => ["docs.joining.$key.file" => ["$key is required."]],
                    ], 422));
                }

                if (!$path)
                    continue;

                SmStaffDocument::create([
                    'staff_id' => $staffId,
                    'group' => 'joining',
                    'key' => $key,
                    'name' => ucfirst(str_replace('_', ' ', $key)),
                    'path' => $path,
                    'remarks' => $remarks,
                    'expiry_date' => $expiry,
                ]);
            }

            /* =======================
             * 2) EMPLOYMENT DOCS
             * ======================= */
            foreach (($docs['employment'] ?? []) as $key => $row) {

                $remarks = $row['remarks'] ?? null;
                $path = null;

                if ($req->hasFile("docs.employment.$key.file")) {
                    $file = $req->file("docs.employment.$key.file");
                    $path = $this->uploadFile($file, 'staff_docs/employment');
                } elseif (!empty($row['existing'])) {
                    $path = $row['existing'];
                }

                if (!$path)
                    continue;

                SmStaffDocument::create([
                    'staff_id' => $staffId,
                    'group' => 'employment',
                    'key' => $key,
                    'name' => ucfirst(str_replace('_', ' ', $key)),
                    'path' => $path,
                    'remarks' => $remarks,
                    'expiry_date' => null,
                ]);
            }

            /* =======================
             * 3) OTHER DOCS
             * ======================= */
            foreach (($docs['others'] ?? []) as $i => $row) {

                $name = trim($row['name'] ?? '');
                $remarks = $row['remarks'] ?? null;
                $path = null;

                if ($req->hasFile("docs.others.$i.file")) {
                    $file = $req->file("docs.others.$i.file");
                    $path = $this->uploadFile($file, 'staff_docs/others');
                } elseif (!empty($row['existing'])) {
                    $path = $row['existing'];
                }

                if (!$name && !$path && !$remarks)
                    continue;

                SmStaffDocument::create([
                    'staff_id' => $staffId,
                    'group' => 'others',
                    'key' => null,
                    'name' => $name ?: 'Other Document',
                    'path' => $path,
                    'remarks' => $remarks,
                    'expiry_date' => null,
                ]);
            }
        });

        return response()->json([
            'ok' => true,
            'message' => 'Documentation saved successfully.'
        ]);
    }

    private function uploadFile($file, $folder)
    {
        $filename = md5(uniqid() . time()) . '.' . $file->getClientOriginalExtension();
        $path = public_path("uploads/$folder");

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $file->move($path, $filename);

        return "public/uploads/$folder/$filename";
    }


    public function docsPeek(Request $req)
    {
        $req->validate([
            'staff_id' => ['required', 'integer', 'exists:sm_staffs,id'],
        ]);

        $sid = (int) $req->staff_id;
        $staff = SmStaff::find($sid);
        $job = SmStaffJobDetail::where('staff_id', $sid)->first();
        $bank = SmStaffBankDetail::where('staff_id', $sid)->first();

        // Collection of relative/legacy paths for experience certs
        $exps = SmStaffProfessionalExperience::where('staff_id', $sid)
            ->pluck('certificate_path')
            ->filter()
            ->values(); // Collection of strings


        $normalize = function ($p) {
            if (!$p)
                return null;
            $p = str_replace('\\', '/', $p);        // Windows -> web slashes
            $p = ltrim($p, '/');                    // trim leading slash
            // strip accidental prefixes (public/, storage/, public/storage/)
            $p = preg_replace('#^(public/|storage/)+#', '', $p);
            return $p;
        };

        // 2) Builder: { path, url } using the public disk
        $make = function ($p) use ($normalize) {
            $p = $normalize($p);
            return $p ? ['path' => $p, 'url' => Storage::disk('public')->url($p)] : null;
        };

        // 3) Map experience certs -> [{path, url}, ...]
        $prof = $exps->map(function ($p) use ($make) {
            return $make($p);
        })->filter()->values();

        return response()->json([
            'ok' => true,
            'photo' => $make(optional($staff)->staff_photo),          // e.g. 'staff_photos/...'
            'cv' => $make(optional($job)->att_resume),             // e.g. 'staff_docs/resume/...'
            'offer' => $make(optional($job)->att_offer_letter),
            'iban' => $make(optional($bank)->att_iban_letter),
            'prof' => $prof,                                         // [{path, url}, ...]
        ]);
    }



    public function edit($id)
    {
        try {
            $staffRow = SmStaff::findOrFail($id);
            $job = SmStaffJobDetail::where('staff_id', $id)->first();
            $bankRows = SmStaffBankDetail::where('staff_id', $id)->get();
            $eduRows = SmStaffEducationQualification::where('staff_id', $id)->get();
            $expRows = SmStaffProfessionalExperience::where('staff_id', $id)->get();
            $docRows = SmStaffDocument::where('staff_id', $id)->get();



            // Get all lookups same as addStaff
            $roles = Role::where('active_status', '=', '1')->orderBy('name', 'asc')->get();
            $company = SysCompany::select('id', 'company_name')->where('status', '=', '1')->get();
            $departments = SmHumanDepartment::where('active_status', '=', '1')->get();
            $designations = SmDesignation::where('active_status', '=', '1')->orderBy('title', 'asc')->get();
            $marital_ststus = SmBaseSetup::where('active_status', '=', '1')->where('base_group_id', '=', '4')->get();
            $genders = SmBaseSetup::where('active_status', '=', '1')->where('base_group_id', '=', '1')->get();
            $staff = SmStaff::select('user_id', 'full_name')->where('active_status', 1)->get();
            $brand_list = SysBrand::select('id', 'title')->where('active_status', 1)->orderby('title')->get();
            $countries = SysCountries::all();
            $states = SysStates::all();

            // Load EOS data for this employee if present
            $eosData = null;
            try {
                $eos = EndOfService::where('employee_id', $id)->first();
                if ($eos) {
                    $eosData = $this->getEosDataForEdit($eos->id);
                }
            } catch (\Exception $e) {
                $eosData = null;
            }

            return view('backEnd.humanResource.edit-Staff', compact(
                'roles',
                'departments',
                'designations',
                'marital_ststus',
                'genders',
                'company',
                'staff',
                'brand_list',
                'countries',
                'eosData',
                'staffRow',
                'job',
                'bankRows',
                'eduRows',
                'expRows',
                'docRows',
                'states'
            ));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed: ' . $e->getMessage(), 'Failed');
            return redirect()->back();
        }
    }

    public function attendenceLeave(Request $req)
    {
        // Staff ID must already be set by your Save All flow
        $staffId = $req->input('staff_id');
        if (!$staffId) {
            return response()->json([
                'ok' => false,
                'message' => 'Staff ID missing. Please save Basic Info first.'
            ], 422);
        }

        // Laravel 5.7 validation (nullable + in + required_if supported)
        $data = $this->validate($req, [
            'attendance_policy' => 'required',
            'min_working_hours' => 'nullable|numeric|min:0',
            'grace_period' => 'nullable|integer|min:0',
            'half_day_after' => 'nullable|numeric|min:0',
            'absent_below_hours' => 'nullable|numeric|min:0',

            'late_mark_allowed' => 'nullable|integer|min:0',
            'late_mark_halfday' => 'nullable|integer|min:0',
            'auto_absent_after' => 'nullable|integer|min:0',

            'leave_policy_type' => 'nullable|in:default,custom',
            'annual_leave' => 'nullable|integer|min:0',
            'sick_leave' => 'nullable|integer|min:0',
            'casual_leave' => 'nullable|integer|min:0',

            'comp_off_allowed' => 'nullable|in:yes,no',

            'carry_forward' => 'nullable|in:yes,no',
            'max_carry_forward' => 'required_if:carry_forward,yes|nullable|integer|min:0',

            'leave_encashment' => 'nullable|in:yes,no',

            // ✅ New fields added
            'shift_start_time' => 'nullable|date_format:H:i',
            'shift_end_time' => 'nullable|date_format:H:i|after:shift_start_time',
            'weekly_off_days' => 'nullable|array',
            'weekly_off_days.*' => 'in:sunday_all,saturday_all,1_3_saturday,2_4_saturday,friday_all',
        ]);

        // Convert weekly_off_days array → JSON (for DB JSON or TEXT column)
        if ($req->filled('weekly_off_days')) {
            $data['weekly_off_days'] = $req->input('weekly_off_days');
        } else {
            $data['weekly_off_days'] = [];
        }

        SmStaffAttendanceLeaveConfiguration::updateOrCreate(
            ['staff_id' => $staffId],
            array_merge($data, ['staff_id' => $staffId])
        );

        return response()->json([
            'ok' => true,
            'staff_id' => $staffId,
            'message' => 'Attendance & Leave configuration saved.'
        ]);
    }



    private function getLookups(): array
    {
        return [
            'roles' => Role::where('active_status', 1)->orderBy('name')->get(['id', 'name']),
            'company' => SysCompany::where('status', 1)->orderBy('company_name')->get(['id', 'company_name']),
            'departments' => SmHumanDepartment::where('active_status', 1)->orderBy('name')->get(['id', 'name']),
            'designations' => SmDesignation::where('active_status', 1)->orderBy('title')->get(['id', 'title']),
            'marital_status' => SmBaseSetup::where('active_status', 1)->where('base_group_id', 4)->orderBy('base_setup_name')->get(),
            'genders' => SmBaseSetup::where('active_status', 1)->where('base_group_id', 1)->orderBy('base_setup_name')->get(),
            'staff' => SmStaff::where('active_status', 1)->orderBy('first_name')->get(['user_id', 'first_name']),
            'brand_list' => SysBrand::where('active_status', 1)->orderBy('title')->get(['id', 'title']),
            'countries' => SysCountries::orderBy('name')->get(['id', 'name', 'iso2']),
        ];
    }


    public function createResignation($id = null)
    {
        $editMode = false;
        $staffData = null;
        $job = null;
        $eosData = null;
        $staffs = SmStaff::where('active_status', 1)
            ->orderBy('full_name', 'asc')
            ->get();
        $departments = SmHumanDepartment::where('active_status', 1)
            ->orderBy('name', 'asc')
            ->get();

        // If ID is provided, load existing EOS record for editing
        if ($id) {
            $eosData = $this->getEosDataForEdit($id);
            if ($eosData) {
                $editMode = true;
                $staffData = SmStaff::find($eosData['main']->employee_id);
                if ($staffData) {
                    $job = $staffData->job ?? null;
                }
            }
        }

        return view('backEnd.humanResource.resignation.index', compact('editMode', 'staffData', 'job', 'staffs', 'departments', 'eosData'));
    }

    /**
     * Edit existing EOS record
     */
    public function editResignation($id)
    {
        return $this->createResignation($id);
    }

    /**
     * List all resignation records
     */
    public function resignationList(Request $request)
    {
        try {
            $companyId = session()->get('logged_session_data.company_id');

            $q = EndOfService::with(['employee', 'employee.departments', 'employee.designations'])
                ->when($companyId, function ($query) use ($companyId) {
                    $query->whereHas('employee', function ($empQuery) use ($companyId) {
                        $empQuery->where('company_id', $companyId);
                    });
                });

            // Filter by employee name/staff no
            if ($request->filled('staff_name')) {
                $term = trim($request->input('staff_name'));
                $q->whereHas('employee', function ($empQuery) use ($term) {
                    $empQuery->where('staff_no', 'like', "%{$term}%")
                        ->orWhere('first_name', 'like', "%{$term}%")
                        ->orWhere('last_name', 'like', "%{$term}%")
                        ->orWhereRaw("CONCAT_WS(' ', first_name, last_name) LIKE ?", ["%{$term}%"]);
                });
            }

            // Filter by status
            if ($request->filled('status')) {
                $q->where('status', $request->status);
            }

            // Filter by separation type
            if ($request->filled('separation_type')) {
                $q->where('separation_type', $request->separation_type);
            }

            $resignations = $q->orderBy('created_at', 'desc')->get();
            $departments = SmHumanDepartment::where('active_status', 1)->get();

            return view('backEnd.humanResource.resignation.list', compact('resignations', 'departments'));
        } catch (\Exception $e) {
            return redirect()->back()->with('message-danger', 'Error loading resignation list: ' . $e->getMessage());
        }
    }

    /**
     * Get EOS data for editing
     */
    private function getEosDataForEdit($id)
    {
        try {
            $main = EndOfService::find($id);
            if (!$main) {
                return null;
            }

            return [
                'main' => $main,
                'notice' => EndOfServiceNotice::where('end_of_service_id', $id)->first(),
                'handover' => EndOfServiceHandover::where('end_of_service_id', $id)->first(),
                'asset_clearance' => EndOfServiceAssetClearance::where('end_of_service_id', $id)->first(),
                'assets' => EndOfServiceAsset::where('asset_clearance_id', function ($query) use ($id) {
                    $query->select('id')
                        ->from('sm_end_of_service_asset_clearance')
                        ->where('end_of_service_id', $id)
                        ->limit(1);
                })->get(),
                'finance' => EndOfServiceFinance::where('end_of_service_id', $id)->first(),
                'final_settlement' => EndOfServiceFinalSettlement::where('end_of_service_id', $id)->first(),
                'exit_interview' => EndOfServiceExitInterview::where('end_of_service_id', $id)->first(),
                'documents' => EndOfServiceDocument::where('end_of_service_id', $id)->first(),
                'approvals' => EndOfServiceApproval::where('end_of_service_id', $id)->get()
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getDesignationsByDepartment(Request $request)
    {
        try {
            $designations = SmDesignation::where('department_id', $request->department_id)
                // ->where('active_status', 1)
                ->orderBy('title', 'asc')
                ->get();

            return response()->json(['status' => 'success', 'data' => $designations]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function storeResignation(Request $request)
    {
        try {
            DB::beginTransaction();

            // Step 1: Create or Update Master Record (sm_end_of_service)
            $endOfServiceData = [
                'employee_id' => $request->employee_id,
                'department_id' => $request->department_id ?: 0,
                'designation_id' => $request->designation_id ?: 0,
                'reporting_manager_id' => $request->reporting_manager ?: 0,
                'separation_type' => $this->getSeparationTypeValue($request->separation_type),
                'resignation_type' => $this->getResignationTypeValue($request->resignation_type),
                'initiated_by' => $this->getInitiatedByValue($request->initiated_by),
                'reason_category' => $this->getReasonCategoryValue($request->reason_category),
                'detailed_reason' => $request->detailed_reason ?: 'N/A',
                'status' => $this->getStatusValue($request->status),
                'created_by' => Auth::id() ?: 1,
                'updated_by' => Auth::id() ?: 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $endOfService = EndOfService::updateOrCreate(
                ['employee_id' => $request->employee_id],
                $endOfServiceData
            );

            $endOfServiceId = $endOfService->id;

            // Step 2: Notice & Period Data (sm_end_of_service_notice)
            EndOfServiceNotice::updateOrCreate(
                ['end_of_service_id' => $endOfServiceId],
                [
                    'end_of_service_id' => $endOfServiceId,
                    'notice_waiver' => $this->getYesNoValue($request->notice_waiver),
                    'notice_waiver_approved_by' => $this->getNoticeWaiverApprovedByValue($request->notice_waiver_approved_by),
                    'notice_period_served' => $this->getNoticePeriodServedValue($request->notice_period_served),
                    'resignation_submitted_date' => $this->formatDateOrNull($request->resignation_submitted_date) ?: now()->format('Y-m-d'),
                    'notice_period_days' => $request->notice_period_days && is_numeric($request->notice_period_days) ? $request->notice_period_days : 30,
                    'last_working_day' => $this->formatDateOrNull($request->last_working_day),
                    'garden_leave_applicable' => $this->getYesNoValue($request->garden_leave_applicable),
                    'garden_leave_start_date' => $this->formatDateOrNull($request->garden_leave_start_date),
                    'garden_leave_end_date' => $this->formatDateOrNull($request->garden_leave_end_date),
                    'relieving_date' => $this->formatDateOrNull($request->relieving_date),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // Step 3: Handover Data (sm_end_of_service_handover)
            EndOfServiceHandover::updateOrCreate(
                ['end_of_service_id' => $endOfServiceId],
                [
                    'end_of_service_id' => $endOfServiceId,
                    'knowledge_transfer_required' => $this->getYesNoValue($request->knowledge_transfer_required),
                    'handover_start_date' => $this->formatDateOrNull($request->handover_start_date),
                    'handover_end_date' => $this->formatDateOrNull($request->handover_end_date),
                    'handover_to_employee_id' => $request->handover_to_employee && is_numeric($request->handover_to_employee) ? $request->handover_to_employee : null,
                    'successor_assigned' => $this->getYesNoValue($request->successor_assigned),
                    'successor_employee_id' => $request->successor_name && is_numeric($request->successor_name) ? $request->successor_name : null,
                    'client_project_handover_completed' => $this->getYesNoValue($request->client_handover_completed),
                    'sop_documentation_shared' => $this->getYesNoValue($request->sop_shared),
                    'handover_checklist_completed' => $this->getYesNoValue($request->handover_checklist_completed),
                    'handover_notes' => $request->handover_notes ?: null,
                    'manager_handover_approval' => $this->getManagerHandoverApprovalValue($request->manager_handover_approval),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // Step 4: Asset Clearance Data (sm_end_of_service_asset_clearance)
            $assetClearance = EndOfServiceAssetClearance::updateOrCreate(
                ['end_of_service_id' => $endOfServiceId],
                [
                    'end_of_service_id' => $endOfServiceId,
                    'clearance_status' => $this->getClearanceStatusValue($request->clearance_status),
                    'remarks' => $request->clearance_remarks ?: null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            $assetClearanceId = $assetClearance->id;

            // Step 5: Asset Items (sm_end_of_service_assets)
            if ($request->has('assets') && is_array($request->assets)) {
                // Delete existing assets for this clearance
                EndOfServiceAsset::where('asset_clearance_id', $assetClearanceId)->delete();

                foreach ($request->assets as $asset) {
                    if (isset($asset['applicable']) && $asset['applicable']) {
                        EndOfServiceAsset::create([
                            'asset_clearance_id' => $assetClearanceId,
                            'asset_name' => $asset['name'] ?? null,
                            'applicable' => $this->getAssetApplicableValue($asset['applicable']),
                            'serial_number' => !empty($asset['serial_number']) ? $asset['serial_number'] : null,
                            'asset_return_date' => $this->formatDateOrNull($asset['return_date']),
                            'asset_condition' => $this->getAssetConditionValue($asset['condition']),
                            'asset_recovery_amount' => isset($asset['recovery_amount']) && is_numeric($asset['recovery_amount']) ? $asset['recovery_amount'] : 0.00,
                            'verified_by_employee_id' => $asset['verified_by'] && is_numeric($asset['verified_by']) ? $asset['verified_by'] : null,
                            'damage_remarks' => !empty($asset['damage_remarks']) ? $asset['damage_remarks'] : null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            // Step 6: Finance Data (sm_end_of_service_finance)
            EndOfServiceFinance::updateOrCreate(
                ['end_of_service_id' => $endOfServiceId],
                [
                    'end_of_service_id' => $endOfServiceId,
                    'leave_balance_at_exit' => $this->getDecimalValue($request->leave_balance_at_exit),
                    'leave_encashment_eligible' => $this->getYesNoValue($request->leave_encashment_eligible),
                    'leave_encashment_days' => $this->getDecimalValue($request->leave_encashment_days),
                    'leave_encashment_amount' => $this->getDecimalValue($request->leave_encashment_amount),
                    'eos_eligibility' => $this->getYesNoValue($request->eos_eligibility),
                    'eos_calculation_method' => $request->eos_calculation_method ?: null,
                    'basic_salary_for_eos' => $this->getDecimalValue($request->basic_salary_for_eos),
                    'gratuity_amount' => $this->getDecimalValue($request->gratuity_amount),
                    'other_allowances_payable' => $this->getDecimalValue($request->other_allowances_payable),
                    'loan_advance_outstanding' => $this->getDecimalValueWithDefault($request->loan_advance_outstanding, 0.00),
                    'deductions_amount' => $this->getDecimalValueWithDefault($request->deductions_total, 0.00),
                    'total_deductions' => $this->getDecimalValueWithDefault($request->total_deductions, 0.00),
                    'net_eos_payable' => $this->getDecimalValue($request->net_eos_payable),
                    'payroll_closure_status' => $this->getPayrollClosureStatusValue($request->payroll_closure_status),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // Step 7: Final Settlement Data (sm_end_of_service_final_settlement)
            EndOfServiceFinalSettlement::updateOrCreate(
                ['end_of_service_id' => $endOfServiceId],
                [
                    'end_of_service_id' => $endOfServiceId,
                    'visa_type' => $request->visa_type ?: null,
                    'visa_cancellation_required' => $request->visa_cancellation_required ? 1 : 0,
                    'visa_cancellation_date' => $this->formatDateOrNull($request->visa_cancellation_date),
                    'labour_card_cancellation_date' => $this->formatDateOrNull($request->labour_card_cancellation_date),
                    'immigration_clearance_status' => $this->getImmigrationClearanceStatusValue($request->immigration_clearance_status),
                    'exit_permit_issued' => $request->exit_permit_issued ? 1 : 0,
                    'mohre_clearance_document' => $request->hasFile('mohre_clearance_document') ?
                        $request->file('mohre_clearance_document')->store('eos/documents') : null,
                    'visa_cancellation_document' => $request->hasFile('visa_cancellation_document') ?
                        $request->file('visa_cancellation_document')->store('eos/documents') : null,
                    'labour_cancellation_document' => $request->hasFile('labour_cancellation_document') ?
                        $request->file('labour_cancellation_document')->store('eos/documents') : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // Step 8: Exit Interview Data (sm_end_of_service_exit_interview)
            EndOfServiceExitInterview::updateOrCreate(
                ['end_of_service_id' => $endOfServiceId],
                [
                    'end_of_service_id' => $endOfServiceId,
                    'exit_interview_conducted' => $this->getYesNoValue($request->exit_interview_conducted),
                    'exit_interview_date' => $this->formatDateOrNull($request->exit_interview_date),
                    'interview_mode' => $this->getInterviewModeValue($request->interview_mode),
                    'overall_satisfaction_rating' => $this->getRatingValue($request->satisfaction_rating),
                    'manager_feedback' => $request->manager_feedback ?: null,
                    'hr_feedback' => $request->hr_feedback ?: null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // Step 9: Documents Data (sm_end_of_service_documents)
            if (
                $request->hasFile('final_settlement_sheet') || $request->hasFile('final_payslip') ||
                $request->filled('document_name') || $request->filled('document_date')
            ) {

                // Handle additional document uploads
                $documents = [];

                if ($request->hasFile('final_settlement_sheet')) {
                    $documents[] = [
                        'end_of_service_id' => $endOfServiceId,
                        'document_name' => 'Final Settlement Sheet',
                        'document_date' => now()->toDateString(),
                        'attachment' => $request->file('final_settlement_sheet')->store('eos/documents'),
                        'remarks' => 'Final settlement sheet upload',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                if ($request->hasFile('final_payslip')) {
                    $documents[] = [
                        'end_of_service_id' => $endOfServiceId,
                        'document_name' => 'Final Payslip',
                        'document_date' => now()->toDateString(),
                        'attachment' => $request->file('final_payslip')->store('eos/documents'),
                        'remarks' => 'Final payslip upload',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                foreach ($documents as $document) {
                    EndOfServiceDocument::create($document);
                }
            }

            // Step 10: Approvals Data (sm_end_of_service_approvals) - Optional
            if ($request->filled('approval_level') || $request->filled('approver_id')) {
                EndOfServiceApproval::updateOrCreate(
                    [
                        'end_of_service_id' => $endOfServiceId,
                        'approval_level' => $this->getApprovalLevelValue($request->approval_level)
                    ],
                    [
                        'end_of_service_id' => $endOfServiceId,
                        'approval_level' => $this->getApprovalLevelValue($request->approval_level),
                        'approver_id' => $request->approver_id ?: Auth::id(),
                        'approval_status' => $this->getApprovalStatusValue($request->approval_status),
                        'approval_date' => null,
                        'remarks' => $request->approval_remarks ?: null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }

            DB::commit();

            return redirect()->route('staff.resignation.edit', $endOfServiceId)->with('message-success', 'End of Service record saved successfully! You can now edit the saved data.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('message-danger', 'Error saving record: ' . $e->getMessage());
        }
    }

    /**
     * Helper method to get proper ENUM value for separation_type
     */
    private function getSeparationTypeValue($value)
    {
        // Map form values to database ENUM values
        $valueMap = [
            'resignation' => 'Resignation',
            'termination' => 'Termination',
            'end_of_contract' => 'End of Contract',
            'retirement' => 'Retirement',
            'absconding' => 'Absconding',
            'death' => 'Death'
        ];

        return isset($valueMap[strtolower($value)]) ? $valueMap[strtolower($value)] : 'Resignation';
    }

    /**
     * Helper method to get proper ENUM value for resignation_type
     */
    private function getResignationTypeValue($value)
    {
        // Map form values to database ENUM values
        $valueMap = [
            'voluntary' => 'Voluntary',
            'involuntary' => 'Involuntary',
            'mutual_separation' => 'Mutual Separation'
        ];

        return isset($valueMap[strtolower($value)]) ? $valueMap[strtolower($value)] : 'Voluntary';
    }

    /**
     * Helper method to get proper ENUM value for initiated_by
     */
    private function getInitiatedByValue($value)
    {
        $validValues = ['Employee', 'Company', 'Management'];
        if (in_array($value, $validValues)) {
            return $value;
        }
        return 'Employee'; // Default fallback
    }

    /**
     * Helper method to get proper ENUM value for reason_category
     */
    private function getReasonCategoryValue($value)
    {
        $validValues = ['Personal', 'Performance', 'Misconduct', 'Redundancy', 'Health', 'Relocation', 'Better Opportunity', 'Other'];
        if (in_array($value, $validValues)) {
            return $value;
        }
        return 'Other'; // Default fallback
    }

    /**
     * Helper method to get proper ENUM value for status
     */
    private function getStatusValue($value)
    {
        // Return ENUM string values for status column: 'draft','submitted','approved','rejected','completed'
        if ($value === 'submitted' || $value === 'S' || $value === 's') {
            return 'submitted';
        }
        if ($value === 'approved' || $value === 'A' || $value === 'a' || $value === 1 || $value === '1') {
            return 'approved';
        }
        if ($value === 'rejected' || $value === 'R' || $value === 'r' || $value === 2 || $value === '2') {
            return 'rejected';
        }
        if ($value === 'completed' || $value === 'C' || $value === 'c') {
            return 'completed';
        }
        return 'draft'; // Default fallback to 'draft'
    }

    /**
     * Helper method to get proper ENUM value for notice_waiver_approved_by
     */
    private function getNoticeWaiverApprovedByValue($value)
    {
        // ENUM('manager','hr','management')
        $valueMap = [
            'manager' => 'manager',
            'hr' => 'hr',
            'management' => 'management'
        ];

        return isset($valueMap[strtolower($value)]) ? $valueMap[strtolower($value)] : null;
    }

    /**
     * Helper method to get proper ENUM value for notice_period_served
     */
    private function getNoticePeriodServedValue($value)
    {
        // ENUM('full','partial','not_served')
        $valueMap = [
            'full' => 'full',
            'partial' => 'partial',
            'not_served' => 'not_served'
        ];

        return isset($valueMap[strtolower($value)]) ? $valueMap[strtolower($value)] : null;
    }

    /**
     * Helper method to get proper ENUM value for manager_handover_approval
     */
    private function getManagerHandoverApprovalValue($value)
    {
        // ENUM('approved','pending') [default 'pending']
        $valueMap = [
            'approved' => 'approved',
            'pending' => 'pending'
        ];

        return isset($valueMap[strtolower($value)]) ? $valueMap[strtolower($value)] : 'pending';
    }

    /**
     * Helper method to get proper ENUM value for asset applicable
     */
    private function getAssetApplicableValue($value)
    {
        // ENUM('yes','no','na') [default 'yes']
        $valueMap = [
            'yes' => 'yes',
            'no' => 'no',
            'na' => 'na',
            'n/a' => 'na'
        ];

        return isset($valueMap[strtolower($value)]) ? $valueMap[strtolower($value)] : 'yes';
    }

    /**
     * Helper method to get proper ENUM value for asset_condition
     */
    private function getAssetConditionValue($value)
    {
        // ENUM('good','damaged','missing')
        $valueMap = [
            'good' => 'good',
            'damaged' => 'damaged',
            'missing' => 'missing'
        ];

        return isset($valueMap[strtolower($value)]) ? $valueMap[strtolower($value)] : null;
    }

    /**
     * Helper method to get proper ENUM value for payroll_closure_status
     */
    private function getPayrollClosureStatusValue($value)
    {
        // ENUM('pending','completed') [default 'pending']
        $valueMap = [
            'pending' => 'pending',
            'completed' => 'completed',
            'open' => 'pending', // Map legacy value
            'closed' => 'completed' // Map legacy value
        ];

        return isset($valueMap[strtolower($value)]) ? $valueMap[strtolower($value)] : 'pending';
    }

    /**
     * Helper method to get proper ENUM value for immigration_clearance_status
     */
    private function getImmigrationClearanceStatusValue($value)
    {
        $validValues = ['Cleared', 'Pending', 'Rejected'];
        if (in_array($value, $validValues)) {
            return $value;
        }
        return 'Pending'; // Default fallback
    }

    /**
     * Helper method to get proper ENUM value for interview_mode
     */
    private function getInterviewModeValue($value)
    {
        // ENUM('in_person','online','telephonic') NULL
        $valueMap = [
            'in_person' => 'in_person',
            'online' => 'online',
            'telephonic' => 'telephonic',
            'face to face' => 'in_person', // Map legacy value
            'phone' => 'telephonic', // Map legacy value
            'video' => 'online' // Map legacy value
        ];

        return isset($valueMap[strtolower($value)]) ? $valueMap[strtolower($value)] : null;
    }

    /**
     * Helper method to get proper ENUM value for approval_status
     */
    private function getApprovalStatusValue($value)
    {
        $validValues = ['Approved', 'Pending', 'Rejected'];
        if (in_array($value, $validValues)) {
            return $value;
        }
        return 'Pending'; // Default fallback
    }

    /**
     * Helper method to get proper ENUM value for approval_level
     */
    private function getApprovalLevelValue($value)
    {
        $validValues = ['Level 1', 'Level 2', 'Level 3'];
        if (in_array($value, $validValues)) {
            return $value;
        }
        return 'Level 1'; // Default fallback
    }

    /**
     * Helper method to get numeric value or 0
     */
    private function getNumericValue($value)
    {
        if (empty($value) || !is_numeric($value)) {
            return 0;
        }
        return $value;
    }

    /**
     * Helper method to get decimal value or null for nullable decimal fields
     */
    private function getDecimalValue($value)
    {
        if (empty($value) || !is_numeric($value)) {
            return null;
        }
        return $value;
    }

    /**
     * Helper method to get decimal value with specific default for fields with defaults
     */
    private function getDecimalValueWithDefault($value, $default = 0.00)
    {
        if (empty($value) || !is_numeric($value)) {
            return $default;
        }
        return $value;
    }

    /**
     * Helper method to validate rating value (1-5) for tinyint rating fields
     */
    private function getRatingValue($value)
    {
        if (empty($value) || !is_numeric($value)) {
            return null;
        }

        $rating = intval($value);
        if ($rating >= 1 && $rating <= 5) {
            return $rating;
        }

        return null; // Invalid rating outside 1-5 range
    }

    /**
     * Helper method to format date for MySQL or return default
     */
    private function formatDateOrDefault($date)
    {
        if (empty($date)) {
            return now()->toDateString();
        }

        try {
            return \Carbon\Carbon::parse($date)->toDateString();
        } catch (\Exception $e) {
            return now()->toDateString();
        }
    }

    /**
     * Helper method to format date for MySQL or return NULL
     */
    private function formatDateOrNull($date)
    {
        if (empty($date)) {
            return null;
        }

        try {
            return SysHelper::normalizeToYmd($date);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Helper method to get proper ENUM value for yes/no fields
     */
    private function getYesNoValue($value)
    {
        // ENUM('yes','no') - return 'yes' or 'no' based on input
        if ($value === 'yes' || $value === 'y' || $value === 'Y' || $value === '1' || $value === 1 || $value === true) {
            return 'yes';
        }
        return 'no'; // Default to 'no'
    }

    /**
     * Helper method to get proper ENUM value for clearance_status
     */
    private function getClearanceStatusValue($value)
    {
        // ENUM('pending','completed') [default 'pending']
        $valueMap = [
            'completed' => 'completed',
            'pending' => 'pending'
        ];

        return isset($valueMap[strtolower($value)]) ? $valueMap[strtolower($value)] : 'pending';
    }


    // ============= COMPENSATION & ROLE CHANGES =============

    /**
     * Show compensation & role change form
     */
    public function compensationCreate($id = null)
    {
        $editMode = false;
        $staffData = null;
        $compensationData = null;

        // Fetch all required data for dropdowns
        $staffs = SmStaff::where('active_status', 1)
            ->orderBy('full_name', 'asc')
            ->get();

        $departments = SmHumanDepartment::where('active_status', 1)
            ->orderBy('name', 'asc')
            ->get();

        $designations = SmDesignation::where('active_status', 1)
            ->orderBy('title', 'asc')
            ->get();

        // If ID is provided, load existing compensation record for editing
        if ($id) {
            // You would fetch the compensation data here once the model is created
            // $compensationData = $this->getCompensationDataForEdit($id);
            // if ($compensationData) {
            //     $editMode = true;
            //     $staffData = SmStaff::find($compensationData['main']->employee_id);
            // }
        }

        return view('backEnd.humanResource.compansation_roles.index', compact(
            'editMode',
            'staffData',
            'staffs',
            'departments',
            'designations',
            'compensationData'
        ));
    }

    /**
     * List all compensation & role changes
     */
    public function compensationList(Request $request)
    {
        try {
            $companyId = session()->get('logged_session_data.company_id');

            // This will fetch from compensation table once created
            // For now, return empty view
            $compensations = collect();
            $departments = SmHumanDepartment::where('active_status', 1)->get();

            return view('backEnd.humanResource.compansation_roles.list', compact('compensations', 'departments'));
        } catch (\Exception $e) {
            return redirect()->back()->with('message-danger', 'Error loading compensation list: ' . $e->getMessage());
        }
    }

    /**
     * Store compensation & role change data
     */
    public function compensationStore(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validate request
            $validated = $request->validate([
                'employee_id' => 'required|exists:sm_staff,id',
                'doc_date' => 'required|date',
                'transaction_type' => 'required|in:increment,promotion,demotion,increment_promotion,decrement_demotion',
                'effective_date' => 'required|date',
                'current_status' => 'required|in:draft,pending,approved,rejected',
                'new_basic_salary' => 'required|numeric|min:0',
            ]);

            // Create or update compensation record
            // This will be replaced once the model is created
            // For now, just store in a temporary location or redirect back

            DB::commit();

            return redirect()->route('staff.compensation.list')->with('message-success', 'Compensation & role change saved successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('message-danger', 'Error saving record: ' . $e->getMessage());
        }
    }

    /**
     * View compensation & role change details
     */
    public function compensationView($id)
    {
        try {
            // Fetch compensation data once model is created
            // return view with details
            return redirect()->route('staff.compensation.list')->with('message-danger', 'Record not found.');
        } catch (\Exception $e) {
            return redirect()->back()->with('message-danger', 'Error: ' . $e->getMessage());
        }
    }

 public function getReportingManagersByGrade(Request $request)
{
    try {
        $gradeRaw = (string) $request->input('grade', '');

        // Normalize grade: 'g4' -> 4
        $gradeInt = (int) preg_replace('/\D+/', '', $gradeRaw);

        if ($gradeInt <= 0) {
            return response()->json([
                'status' => 'success',
                'data' => []
            ]);
        }



        $staff = SmStaff::select('id', 'user_id', 'full_name')
            ->where('active_status', 1)
            ->whereHas('jobDetail', function ($q) use ($gradeInt) {
                $q->whereRaw(
                    "CAST(SUBSTRING(grade, 2) AS UNSIGNED) < ?",
                    [$gradeInt]
                );
            })
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $staff
        ]);

    } catch (\Throwable $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
}


}



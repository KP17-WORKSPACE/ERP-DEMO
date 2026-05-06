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
use App\SmStaffBankDetail;
use App\SmStaffEducationQualification;
use App\SmStaffProfessionalExperience;
use Illuminate\Support\Facades\Storage;
use App\SmStaffDocument;
use App\SysCountries;

use Illuminate\Support\Facades\Schema;

class SmStaffController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
    }

   public function staffList(Request $request)
    {
        try {
            $companyId = session('logged_session_data.company_id');

            $q = SmStaff::with(['roles','maincompany','departments','designations'])
                ->where('company_id', $companyId)
                ->where('delete_status', 1)
                ->when(Auth::user()->role_id != 1, fn($x) => $x->where('role_id','!=',1));

            if ($request->filled('staff_no')) {
                $term = trim($request->input('staff_no'));
                $q->where(function ($x) use ($term) {
                    $x->where('staff_no', 'like', "%{$term}%")
                      ->orWhere('first_name','like',"%{$term}%")
                      ->orWhere('last_name','like',"%{$term}%")
                      ->orWhereRaw("CONCAT_WS(' ', first_name, last_name) LIKE ?", ["%{$term}%"])
                      ->orWhere('email','like',"%{$term}%")
                      ->orWhere('mobile','like',"%{$term}%");
                });
            }

            $staffs  = $q->orderBy('first_name')->orderBy('last_name')->get();
            $roles   = Role::where('active_status',1)->get();
            $company = SysCompany::select('id','company_name')->get();

            return view('backEnd.humanResource.staff_list', compact('staffs','roles','company'));
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
            $roles = Role::where('active_status', '=', '1')->orderBy('name', 'asc')->get();
            $company = SysCompany::select('id', 'company_name')->where('status', '=', '1')->get();
            $departments = SmHumanDepartment::where('active_status', '=', '1')->get();
            $designations = SmDesignation::where('active_status', '=', '1')->orderBy('title', 'asc')->get();
            $marital_ststus = SmBaseSetup::where('active_status', '=', '1')->where('base_group_id', '=', '4')->get();
            $genders = SmBaseSetup::where('active_status', '=', '1')->where('base_group_id', '=', '1')->get();
            $staff = SmStaff::select('user_id', 'full_name')->where('active_status', 1)->get();
            $brand_list = SysBrand::select('id', 'title')->where('active_status', 1)->orderby('title')->get();
            $countries = SysCountries::all();

            return view('backEnd.humanResource.addStaff', compact('roles', 'departments', 'designations', 'marital_ststus', 'genders', 'company', 'staff', 'brand_list', 'countries'));
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
                    ->where('sys_cust_suppl_assign.cust_supp_id', $request->id)->groupby('sm_staffs.id', 'sm_staffs.full_name')->get();
            } elseif ($com_id == 1) {
                $data = SmStaff::select('sm_staffs.user_id as id', 'sm_staffs.full_name')
                    ->join('sys_cust_suppl_assign', 'sys_cust_suppl_assign.user_id', 'sm_staffs.id')
                    ->where('sys_cust_suppl_assign.cust_supp_id', $request->id)->groupby('sm_staffs.id', 'sm_staffs.full_name')->get();

                //$data = SmStaff::select('user_id as id','full_name')->where('active_status', '=', '1')
                //->wherein('role_id',[1,2,5,8])->orderby('full_name','asc')->get();
            } else {
                $data = SmStaff::select('sm_staffs.user_id as id', 'sm_staffs.full_name')
                    ->join('sys_cust_suppl_assign', 'sys_cust_suppl_assign.user_id', 'sm_staffs.id')
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
        $staff = SmStaff::with([
            'roles',
            'departments',
            'designations',
            'maincompany',
            'jobDetail',
            'bankDetail',
            'educationQualifications',
            'professionalExperiences',
            'documents',
            'genders',
            'nationalityCountry'
        ])->findOrFail($id);

        // ye ek partial blade return karega jisme sirf detail ka HTML hoga
        return view('backEnd.humanResource.staff_details', compact('staff'));
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

    public function storeBasic(Request $req)
    {
        $staffId = $req->input('staff_id');            // may be null on first save
        $isUpdate = !empty($staffId);

        // Validation
        $data = $req->validate([
            'staff_code' => ['required', 'string', 'max:50'],
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'fathers_name' => ['required', 'string', 'max:150'],
            'mothers_name' => ['required', 'string', 'max:150'],
            'date_of_birth' => ['required', 'string'], // dd/mm/YYYY from UI
            'religion' => ['required', 'string', 'max:50'],
            'gender_id' => ['required', 'integer'],
            'mobile' => ['required', 'string', 'max:20'],
            'email' => [
                'required',
                'email',
                'max:191',
                Rule::unique('sm_staffs', 'email')->ignore($staffId)
            ],
            'marital_status' => ['required', 'string', 'in:single,married,divorced,widowed'],
            'nationality' => ['required', 'string', 'max:80'],

            'emergency_contact_name' => ['required', 'string', 'max:150'],
            'emergency_contact_relationship' => ['required', 'string', 'max:100'],
            'emergency_contact_number' => ['required', 'string', 'max:20'],

            // password required on create, optional on update
            'password' => [$isUpdate ? 'nullable' : 'required', 'string', 'min:6'],
            'staff_photo' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],

            'permanent_address' => ['nullable', 'string', 'max:1000'],
            'uae_address' => ['nullable', 'string', 'max:1000'],
        ], [], [
            'staff_code' => 'User No',
            'date_of_birth' => 'Date of Birth',
            'gender_id' => 'Gender',
            'emergency_contact_number' => 'Emergency Contact Number',
        ]);

        // Parse date: d/m/Y -> Y-m-d
        $dob = null;
        if ($req->filled('date_of_birth')) {
            try {
                $dob = Carbon::createFromFormat('d/m/Y', $req->input('date_of_birth'))->format('Y-m-d');
            } catch (\Exception $e) {
                $dob = Carbon::parse($req->input('date_of_birth'))->format('Y-m-d');
            }
        }

        // Find or make new
        $staff = $isUpdate ? SmStaff::find($staffId) : new SmStaff;
        if (!$staff) { // if id sent but not found, treat as create
            $staff = new SmStaff;
            $isUpdate = false;
        }

        // Map payload
        $staff->staff_no = $req->input('staff_code');
        $staff->first_name = $req->input('first_name');
        $staff->middle_name = $req->input('middle_name');
        $staff->last_name = $req->input('last_name');
        $staff->fathers_name = $req->input('fathers_name');
        $staff->mothers_name = $req->input('mothers_name');
        $staff->date_of_birth = $dob;
        $staff->religion = $req->input('religion');
        $staff->gender_id = (int) $req->input('gender_id');
        $staff->mobile = $req->input('mobile');
        $staff->role_id = (int) $req->input('role_id');
        $staff->designation_id = (int) $req->input('designation_id');
        $staff->department_id = (int) $req->input('department_id');
        $staff->company_id = (int) $req->input('visa_company_name');
        $staff->main_company = (int) $req->input('working_company_name');
        $staff->company_access = implode(',', $req->input('company_access', []));
        $staff->ext_no = $req->input('ext_no_2');
        $staff->email = $req->input('email');
        $staff->marital_status = $req->input('marital_status');
        $staff->nationality = $req->input('nationality');
        $staff->permanent_address = $req->input('permanent_address');
        $staff->current_address = $req->input('uae_address');
        $staff->emergency_contact_name = $req->input('emergency_contact_name');
        $staff->emergency_contact_relationship = $req->input('emergency_contact_relationship');
        $staff->emergency_mobile = $req->input('emergency_contact_number');

        // Only change password if provided on update
        if ($req->filled('password')) {
            $staff->password = Hash::make($req->input('password'));
        } elseif (!$isUpdate) {
            // just to be explicit—on create it's required so this branch won't hit
            $staff->password = Hash::make($req->input('password'));
        }

        // Photo upload
        if ($req->hasFile('staff_photo')) {
            $file = $req->file('staff_photo');

            // unique file name
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // destination path
            $destination = public_path('uploads/staff_photos');

            // create folder if not exists
            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }

            // move file
            $file->move($destination, $filename);

            // save clean relative path in DB (forward slash only ✅)
            $staff->staff_photo = 'uploads/staff_photos/' . $filename;
        }



        $staff->save();

        return response()->json([
            'ok' => true,
            'message' => $isUpdate ? 'Basic details updated.' : 'Basic details saved.',
            'staff_id' => $staff->id,
        ]);
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
            'week_off' => ['required', Rule::in(['sat_sun', 'sunday', 'fri_sat', 'friday'])],

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
            'iban_number' => [
                'required',
                'string',
                'max:34',
                'regex:/^[A-Z]{2}[0-9]{2}[0-9A-Z]{11,30}$/i'
            ],
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

    public function storeDocs(Request $req)
    {
        $req->validate([
            'staff_id' => ['required', 'integer', 'exists:sm_staffs,id'],
            'docs' => ['nullable', 'array'],
            // no hard required here; we'll enforce per-row below
        ]);

        $staffId = (int) $req->input('staff_id');
        $docs = $req->input('docs', []);

        // Helper to parse dd/mm/YYYY -> Y-m-d
        $parseDate = function ($val) {
            if (!$val)
                return null;
            try {
                return Carbon::createFromFormat('d/m/Y', $val)->format('Y-m-d');
            } catch (\Throwable $e) {
                try {
                    return Carbon::parse($val)->format('Y-m-d');
                } catch (\Throwable $e2) {
                    return null;
                }
            }
        };

        DB::transaction(function () use ($req, $staffId, $docs, $parseDate) {

            // Remove existing docs for idempotency (optional: only remove groups we’re writing)
            SmStaffDocument::where('staff_id', $staffId)->delete();

            // 1) JOINING DOCS
            foreach (($docs['joining'] ?? []) as $key => $row) {
                $remarks = $row['remarks'] ?? null;
                $expiry = $parseDate($row['expiry'] ?? null);

                // accept either uploaded file, or existing path from hidden inputs
                $path = null;

                if ($key === 'prof_certs') {
                    // May have multiple existing[]
                    $existing = (array) ($row['existing'] ?? []);
                    foreach ($existing as $ex) {
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
                        $upload = $req->file('docs.joining.prof_certs.file')->store('docs/joining', 'public');
                        SmStaffDocument::create([
                            'staff_id' => $staffId,
                            'group' => 'joining',
                            'key' => 'prof_certs',
                            'name' => 'Professional Certificate',
                            'path' => 'storage/' . $upload,
                            'remarks' => $remarks,
                            'expiry_date' => null,
                        ]);
                    }
                    continue;
                }

                // Generic single-file keys
                if ($req->hasFile("docs.joining.$key.file")) {
                    $upload = $req->file("docs.joining.$key.file")->store('docs/joining', 'public');
                    $path = 'storage/' . $upload;
                } elseif (!empty($row['existing'])) {
                    $path = $row['existing'];
                }

                // Enforce required for passport_visa & emirates_id
                if (in_array($key, ['passport_visa', 'emirates_id']) && !$path) {
                    return abort(response()->json([
                        'message' => 'The given data was invalid.',
                        'errors' => ["docs.joining.$key.file" => ["$key is required."]],
                    ], 422));
                }

                // Optional rows without path can be skipped
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

            // 2) EMPLOYMENT DOCS
            foreach (($docs['employment'] ?? []) as $key => $row) {
                $remarks = $row['remarks'] ?? null;
                $path = null;
                if ($req->hasFile("docs.employment.$key.file")) {
                    $upload = $req->file("docs.employment.$key.file")->store('docs/employment', 'public');
                    $path = 'storage/' . $upload;
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

            // 3) OTHERS (dynamic)
            foreach (($docs['others'] ?? []) as $i => $row) {
                $name = trim((string) ($row['name'] ?? ''));
                $remarks = $row['remarks'] ?? null;
                $path = null;
                if ($req->hasFile("docs.others.$i.file")) {
                    $upload = $req->file("docs.others.$i.file")->store('docs/others', 'public');
                    $path = 'storage/' . $upload;
                } elseif (!empty($row['existing'])) {
                    $path = $row['existing'];
                }
                // Skip blank rows
                if (!$name && !$path && !$remarks)
                    continue;

                // If user typed a name but no file, you can allow or enforce file; here we allow.
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

        return response()->json(['ok' => true, 'message' => 'Documentation saved.']);
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
        $staffRow = SmStaff::findOrFail($id);
        $jobRow = SmStaffJobDetail::where('staff_id', $id)->first();
        $bankRow = SmStaffBankDetail::where('staff_id', $id)->first();
        $eduRows = SmStaffEducationQualification::where('staff_id', $id)->get();
        $expRows = SmStaffProfessionalExperience::where('staff_id', $id)->get();

        $lookups = $this->getLookups(); // your roles, genders, etc.

        $editData = $staffRow; // ← alias for backward-compat

        return view(
            'backEnd.humanResource.updateStaff',
            $lookups + compact('staffRow', 'editData', 'jobRow', 'bankRow', 'eduRows', 'expRows')
        );
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




}

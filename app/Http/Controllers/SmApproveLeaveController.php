<?php

namespace App\Http\Controllers;

use DB;
use App\Role;
use App\SmStaff;
use App\SmParent;
use App\SmLeaveType;
use App\ApiBaseMethod;
use App\SmLeaveDefine;
use App\SmLeaveRequest;
use App\SmNotification;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SmApproveLeaveController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
    }

    public function index(Request $request)
    {
        
        try{
            if (Auth::user()->role_id != 1) {
                $staff = SmStaff::where('user_id', '=', Auth::user()->id)->first();
                $apply_leaves = SmLeaveRequest::where('active_status', 1)->where('staff_id', '=', $staff->id)->get();
                $leave_types = SmLeaveType::where('active_status', 1)->get();
                $roles = Role::where('id', '!=', 1)->where('id', '!=', 2)->where('id', '!=', 3)->get();
            } else {
                $apply_leaves = SmLeaveRequest::where('active_status', 1)->get();
                $leave_types = SmLeaveType::where('active_status', 1)->get();
                $roles = Role::where('id', '!=', 1)->where('id', '!=', 2)->where('id', '!=', 3)->get();
            }
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['apply_leaves'] = $apply_leaves->toArray();
                $data['apply_leaves'] = $leave_types->toArray();
                $data['roles'] = $roles->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.humanResource.approveLeaveRequest', compact('apply_leaves', 'leave_types', 'roles'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function store(Request $request)
    {
        $input = $request->all();
        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            $validator = Validator::make($input, [
                'apply_date' => "required",
                'leave_type' => "required",
                'leave_from' => "required",
                'leave_to' => "required",
                'reason' => "required",
                'login_id' => "required",
                'role_id' => "required"
            ]);
        } else {
            $validator = Validator::make($input, [
                'staff_id' => "required",
                'apply_date' => "required",
                'leave_type' => "required",
                'leave_from' => "required",
                'leave_to' => "required",
                'reason' => "required"
            ]);
        }
        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        try{
            $fileName = "";
            if ($request->file('attach_file') != "") {
                $file = $request->file('attach_file');
                $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('public/uploads/leave_request/', $fileName);
                $fileName =  'public/uploads/leave_request/' . $fileName;
            }
    
            $user = Auth()->user();
    
            if ($user) {
                $login_id = $user->id;
                $role_id = $user->role_id;
            } else {
                $login_id = $request->login_id;
                $role_id = $request->role_id;
            }
            $leave_request_data = new SmLeaveRequest();
            $leave_request_data->staff_id = $login_id;
            $leave_request_data->role_id =  $role_id;
            $leave_request_data->apply_date = date('Y-m-d', strtotime($request->apply_date));
            $leave_request_data->type_id = $request->leave_type;
            $leave_request_data->leave_from = date('Y-m-d', strtotime($request->leave_from));
            $leave_request_data->leave_to = date('Y-m-d', strtotime($request->leave_to));
            $leave_request_data->approve_status = $request->approve_status;
            $leave_request_data->reason = $request->reason;
            $leave_request_data->file = $fileName;
            $result = $leave_request_data->save();
    
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($result) {
                    return ApiBaseMethod::sendResponse(null, 'Leave Request has been created successfully.');
                } else {
                    return ApiBaseMethod::sendError('Something went wrong, please try again.');
                }
            } else {
                if ($result) {
                    Toastr::success('Operation successful', 'Success');
                    return redirect()->back();
                } else {
                    Toastr::error('Operation Failed', 'Failed');
                    return redirect()->back();
                }
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function edit(Request $request, $id)
    {
        try{
            $editData = SmLeaveRequest::find($id);
            $staffsByRole = SmStaff::where('role_id', '=', $editData->role_id)->get();
            $roles = Role::all();
            $apply_leaves = SmLeaveRequest::where('active_status', 1)->get();
            $leave_types = SmLeaveType::where('active_status', 1)->get();
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['editData'] = $editData->toArray();
                $data['staffsByRole'] = $staffsByRole->toArray();
                $data['apply_leaves'] = $apply_leaves->toArray();
                $data['leave_types'] = $leave_types->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.humanResource.approveLeaveRequest', compact('editData', 'staffsByRole', 'apply_leaves', 'leave_types', 'roles'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function staffNameByRole(Request $request)
    {

        if ($request->id != 3) {
            $allStaffs = SmStaff::where('role_id', '=', $request->id)->get();
            $staffs = [];
            foreach ($allStaffs as $staffsvalue) {
                $staffs[] = SmStaff::find($staffsvalue->id);
            }
        } else {
            $staffs = SmParent::where('active_status', 1)->get();
        }

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            return ApiBaseMethod::sendResponse($staffs, null);
        }

        return response()->json([$staffs]);
    }
    public function staffNameByRoleDev(Request $request)
    {

       
            $allStaffs = DB::table('sm_staffs')->where('role_id',$request->id)->get();
            $staffs = [];
            foreach ($allStaffs as $staffsvalue) {
                $staffs[] = SmStaff::find($staffsvalue->id);
            }
// return $allStaffs;
        // return response()->json($allStaffs);
        return response()->json([$staffs]);
    }

    public function updateApproveLeave(Request $request)
    {
        try{
            $leave_request_data = SmLeaveRequest::find($request->id);
            $staff_id = $leave_request_data->staff_id;
            $role_id = $leave_request_data->role_id;
            $leave_request_data->approve_status = $request->approve_status;
            $result = $leave_request_data->save();

            $notification = new SmNotification;
            $notification->user_id = $staff_id;
            $notification->role_id = $role_id;
            $notification->date = date('Y-m-d');
            $notification->message = 'Leave status updated';
            $notification->save();
    
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($result) {
                    return ApiBaseMethod::sendResponse(null, 'Leave Request has been updates successfully.');
                } else {
                    return ApiBaseMethod::sendError('Something went wrong, please try again.');
                }
            } else {
                if ($result) {
                    Toastr::success('Operation successful', 'Success');
                    return redirect('approve-leave');
                } else {
                    Toastr::error('Operation Failed', 'Failed');
                    return redirect()->back();
                }
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function viewLeaveDetails(Request $request, $id)
    {
        try{
            $leaveDetails = SmLeaveRequest::find($id);
            $staff_leaves = SmLeaveDefine::where('role_id', $leaveDetails->role_id)->get();
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['leaveDetails'] = $leaveDetails->toArray();
                $data['staff_leaves'] = $staff_leaves->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.humanResource.viewLeaveDetails', compact('leaveDetails', 'staff_leaves'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
}

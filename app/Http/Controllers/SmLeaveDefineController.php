<?php

namespace App\Http\Controllers;

use App\Role;
use App\SmLeaveType;
use App\ApiBaseMethod;
use App\SmLeaveDefine;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Validator;

class SmLeaveDefineController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
    }

    public function index(Request $request)
    {
        try{
            $leave_types = SmLeaveType::where('active_status', 1)->get();
            $roles = Role::where('active_status', 1)->where('id', '!=', 2)->get();
            $leave_defines = SmLeaveDefine::where('sm_leave_defines.active_status', 1)
                ->join('sm_leave_types', 'sm_leave_types.id', '=', 'sm_leave_defines.type_id')
                ->where('sm_leave_types.active_status', '=', 1)
                ->select('sm_leave_defines.*')
                ->get();
                // return $leave_defines;
            return view('backEnd.humanResource.leave_define', compact('leave_types', 'roles', 'leave_defines'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'role' => "required",
            'leave_type' => 'required',
            'days' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        try{
            $leaves = SmLeaveDefine::where('role_id', $request->role)->where('type_id', $request->leave_type)->first();

            if ($leaves == "") {
                $leave_define = new SmLeaveDefine();
                $leave_define->role_id = $request->role;
                $leave_define->type_id = $request->leave_type;
                $leave_define->days = $request->days;
                $results = $leave_define->save();
    
                if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                    if ($results) {
                        return ApiBaseMethod::sendResponse(null, 'Visitor has been created successfully.');
                    }
                    return ApiBaseMethod::sendError('Something went wrong, please try again.');
                } else {
                    if ($results) {
                        Toastr::success('Operation successful', 'Success');
                        return redirect()->back();
                    }
                    Toastr::error('Operation Failed', 'Failed');
                    return redirect()->back();
                }
            } else {
                if (ApiBaseMethod::checkUrl($request->fullUrl())) {
    
                    return ApiBaseMethod::sendError('The type already assigned for the role');
                } else {
                    Toastr::error('The type already assigned for the role', 'Failed');
                    return redirect()->back();
                }
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function show(Request $request, $id)
    {
       
        try{
            $leave_types = SmLeaveType::where('active_status', 1)->get();
            $roles = Role::where('active_status', 1)->get();
            $leave_defines = SmLeaveDefine::where('sm_leave_defines.active_status', 1)
                ->join('sm_leave_types', 'sm_leave_types.id', '=', 'sm_leave_defines.type_id')
                ->where('sm_leave_types.active_status', '=', 1)
                ->get();
            $leave_define = SmLeaveDefine::find($id);
    
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['leave_types'] = $leave_types->toArray();
                $data['roles'] = $roles->toArray();
                $data['leave_defines'] = $leave_defines->toArray();
                $data['leave_define'] = $leave_define->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }
    
            return view('backEnd.humanResource.leave_define', compact('leave_types', 'holidays', 'roles', 'leave_defines', 'leave_define'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();
        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            $validator = Validator::make($input, [
                'role' => "required",
                'leave_type' => 'required',
                'days' => 'required|numeric',
                'id' => "required"
            ]);
        } else {
            $validator = Validator::make($input, [
                'role' => "required",
                'leave_type' => 'required',
                'days' => 'required|numeric'
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
            $leave_define = SmLeaveDefine::find($request->id);
            $leave_define->role_id = $request->role;
            $leave_define->type_id = $request->leave_type;
            $leave_define->days = $request->days;
            $results = $leave_define->save();
    
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($results) {
                    return ApiBaseMethod::sendResponse(null, 'Leave Define has been Updated successfully');
                } else {
                    return ApiBaseMethod::sendError('Something went wrong, please try again.');
                }
            } else {
                if ($results) {
                    Toastr::success('Operation successful', 'Success');
                    return redirect('leave-define');
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

    public function destroy(Request $request, $id)
    {
        
        try{
            $result = SmLeaveDefine::findOrfail($id);
            $result->active_status = 0;
            $result->save();
    
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($result) {
                    return ApiBaseMethod::sendResponse(null, 'Leave Define has been deleted successfully');
                } else {
                    return ApiBaseMethod::sendError('Something went wrong, please try again.');
                }
            } else {
                if ($result) {
                    Toastr::success('Operation successful', 'Success');
                    return redirect('leave-define');
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
}

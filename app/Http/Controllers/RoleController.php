<?php

namespace App\Http\Controllers;

use App\Role;
use App\ApiBaseMethod;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function __construct()
    {
        $roles=Role::all();
        // $roles->truncate();
    }

    public function index(Request $request){
    	
        try{
            $roles = Role::where('active_status', '=', 1)->orderBy('name', 'asc')->get();
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse($roles, null);
            }
            return view('backEnd.systemSettings.role.role', compact('roles'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    
    public function store(Request $request){
        $input = $request->all();
        $validator = Validator::make($input, [
    		'name' => "required"
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
            $role = new Role();
            $role->name = $request->name;
            $role->type = 'User Defined';
            $result = $role->save();
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($result) {
                    return ApiBaseMethod::sendResponse(null, 'Role has been created successfully');
                } else {
                    return ApiBaseMethod::sendError('Something went wrong, please try again');
                }
            } else {
                if ($result) {
                    return redirect()->back()->with('message-success', 'Role has been created successfully');
                } else {
                    return redirect()->back()->with('message-danger', 'Something went wrong, please try again');
                }
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function edit(Request $request,$id){
    	
         try{
            $role = Role::find($id);
            $roles = Role::where('active_status', '=', 1)->orderBy('name', 'asc')->get();
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['role'] = $role;
                $data['roles'] = $roles->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }
             return view('backEnd.systemSettings.role.role', compact('role', 'roles'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function update(Request $request){
        $input = $request->all();
        $validator = Validator::make($input, [
    		'name' => "required"
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
            $role = Role::find($request->id);
            $role->name = $request->name;
            $result = $role->save();
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($result) {
                    return ApiBaseMethod::sendResponse(null, 'Role has been updated successfully');
                } else {
                    return ApiBaseMethod::sendError('Something went wrong, please try again');
                }
            } else {
                if ($result) {
                    return redirect()->back()->with('message-success', 'Role has been updated successfully');
                } else {
                    return redirect()->back()->with('message-danger', 'Something went wrong, please try again');
                }
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function delete(Request $request){
    	
        try{
            $role = Role::destroy($request->id);
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($role) {
                    return ApiBaseMethod::sendResponse(null, 'Role has been deleted successfully');
                } else {
                    return ApiBaseMethod::sendError('Something went wrong, please try again');
                }
            } else {
                if ($role) {
                    return redirect()->back()->with('message-success-delete', 'Role has been deleted successfully');
                } else {
                    return redirect()->back()->with('message-danger-delete', 'Something went wrong, please try again');
                }
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

}

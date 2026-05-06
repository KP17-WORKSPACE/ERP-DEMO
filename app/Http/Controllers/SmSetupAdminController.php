<?php

namespace App\Http\Controllers;

use App\SmSetupAdmin;
use App\ApiBaseMethod;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Validator;

class SmSetupAdminController extends Controller
{
    public function __construct(){
        $this->middleware('PM');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            $admin_setups = SmSetupAdmin::where('active_status', '=', 1)->get();
            $admin_setups = $admin_setups->groupBy('type');
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['admin_setups'] = $admin_setups->toArray();
                $data['admin_setups'] = $admin_setups->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.admin.setup_admin', compact('admin_setups'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'type' => 'required',
            'name' => 'required'
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
            $setup = new SmSetupAdmin();
            $setup->type = $request->type;
            $setup->name = $request->name;
            $setup->description = $request->description;
            $result = $setup->save();
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($result) {
                    return ApiBaseMethod::sendResponse(null, 'Admin  Setup has been created successfully');
                } else {
                    return ApiBaseMethod::sendError('Something went wrong, please try again');
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

    public function show(Request $request,$id)
    {
        try{
            $admin_setup = SmSetupAdmin::find($id);
            $admin_setups = SmSetupAdmin::where('active_status', '=', 1)->get();
            $admin_setups = $admin_setups->groupBy('type');
    
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['admin_setup'] = $admin_setup->toArray();
                $data['admin_setups'] = $admin_setups->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.admin.setup_admin', compact('admin_setups', 'admin_setup'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'type' => 'required',
            'name' => 'required'
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
            $setup = SmSetupAdmin::find($id);
            $setup->type = $request->type;
            $setup->name = $request->name;
            $setup->description = $request->description;
            $result = $setup->save();
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($result) {
                    return ApiBaseMethod::sendResponse(null, 'Admin Setup has been updated successfully');
                } else {
                    return ApiBaseMethod::sendError('Something went wrong, please try again');
                }
            } else {
                if ($result) {
                    Toastr::success('Operation successful', 'Success');
                    return redirect('setup-admin');
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

    public function destroy(Request $request,$id)
    {
        try{
            $result = SmSetupAdmin::destroy($id);
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($result) {
                    return ApiBaseMethod::sendResponse(null, 'Admin Setup has been deleted successfully');
                } else {
                    return ApiBaseMethod::sendError('Something went wrong, please try again');
                }
            } else {
                if ($result) {
                    Toastr::success('Operation successful', 'Success');
                    return redirect('setup-admin');
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

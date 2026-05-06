<?php

namespace App\Http\Controllers;

use App\ApiBaseMethod;
use App\SmModule;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class SysModuleController extends Controller
{

    public function __construct()
    {
        $this->middleware('PM');
    }

    public function index(Request $request)
    {
        try{
            $modulelist = SmModule::where('active_status', 1)->get();
            if(ApiBaseMethod::checkUrl($request->fullUrl())){
                return ApiBaseMethod::sendResponse($modulelist, null);
            }
            return view('backEnd.systemSettings.modules', compact('modulelist'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function store(Request $request)
    {        
        try{
            $modulelist = new SmModule();
            $modulelist->name = $request->name;
            $modulelist->created_by = Auth::user()->id;
            $result = $modulelist->save();
            if(ApiBaseMethod::checkUrl($request->fullUrl())){
                if($result){
                    Toastr::success('Operation successful', 'Success');
                    return redirect()->back();
                }else{
                    return ApiBaseMethod::sendError('Something went wrong, please try again.');
                }
            }else{
                if($result){
                    Toastr::success('Operation successful', 'Success');
                    return redirect()->back();
                }else{
                    Toastr::error('Operation Failed', 'Failed');
                    return redirect()->back();
                }
            }
        }catch (\Exception $e) {
            return $e;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function show(Request $request, $id)
    {
        try{
            $editmode = SmModule::find($id);
            $modulelist = SmModule::all();

            if(ApiBaseMethod::checkUrl($request->fullUrl())){
                $data=[];
                $data['modulelist']= $modulelist->toArray();
                $data['editmode']= $editmode->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.systemSettings.modules', compact('modulelist', 'editmode'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function update(Request $request, $id)
    {
        try{
            $modulelist = SmModule::find($request->id);
            $modulelist->name = $request->name;
            $modulelist->updated_by = Auth::user()->id;
            $result = $modulelist->save();
            if(ApiBaseMethod::checkUrl($request->fullUrl())){
                if($result){
                    return ApiBaseMethod::sendResponse(null, 'Module has been updated successfully');
                }else{
                    return ApiBaseMethod::sendError('Something went wrong, please try again.');
                }
            }else{
                if($result){
                    Toastr::success('Operation successful', 'Success');
                    return redirect('module');
                }else{
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
            $brands = SmModule::destroy($id);
            if(ApiBaseMethod::checkUrl($request->fullUrl())){
                if($brands){
                    return ApiBaseMethod::sendResponse(null, 'Brand has been deleted successfully');
                }else{
                    return ApiBaseMethod::sendError('Something went wrong, please try again.');
                }
            }else{
                if($brands){
                    Toastr::success('Operation successful', 'Success');
                    return redirect('module');
                }else{
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
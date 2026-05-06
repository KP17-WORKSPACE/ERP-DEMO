<?php

namespace App\Http\Controllers;

use App\ApiBaseMethod;
use App\SmModule;
use App\SmModuleLink;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class SysModulePagesController extends Controller
{

    public function __construct()
    {
        $this->middleware('PM');
    }

    public function show(Request $request, $id)
    {
        try{
            $editmode = SmModule::find($id);
            $pagelist = SmModuleLink::where('module_id','=',$id)->get();

            if(ApiBaseMethod::checkUrl($request->fullUrl())){
                $data=[];
                $data['pagelist']= $pagelist->toArray();
                $data['editmode']= $editmode->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.systemSettings.modulepages', compact('pagelist', 'editmode'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function edit(Request $request, $id)
    {
        try{
            $editpage = SmModuleLink::where('id','=',$id)->first();
            $editmode = SmModule::find($editpage->module_id);
            $pagelist = SmModuleLink::where('module_id','=',$editpage->module_id)->get();

            if(ApiBaseMethod::checkUrl($request->fullUrl())){
                $data=[];
                $data['pagelist']= $pagelist->toArray();
                $data['editmode']= $editmode->toArray();
                $data['editpage']= $editpage->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.systemSettings.modulepages', compact('pagelist', 'editmode','editpage'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    // public function index(Request $request)
    // {
    //     return "11";
    //     try{
    //         $modulelist = SmModule::where('active_status', 1)->get();
    //         if(ApiBaseMethod::checkUrl($request->fullUrl())){
    //             return ApiBaseMethod::sendResponse($modulelist, null);
    //         }
    //         return view('backEnd.systemSettings.modules', compact('modulelist'));
    //     }catch (\Exception $e) {
    //        Toastr::error('Operation Failed', 'Failed');
    //        return redirect()->back(); 
    //     }
    // }
    public function store(Request $request)
    {
        $input = $request->all();
        
        try{
            $modulelist = new SmModuleLink();
            $modulelist->name = $request->name;
            $modulelist->page_name = $request->page_name;
            $modulelist->module_id = $request->mid;
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
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();
        try{
            $modulelist = SmModuleLink::find($request->id);
            $modulelist->name = $request->name;
            $modulelist->page_name = $request->page_name;
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
                    return redirect('module-pages/'.$request->mid);
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

    // public function destroy(Request $request,$id)
    // {
    //     try{
    //         $brands = SmModule::destroy($id);
    //         if(ApiBaseMethod::checkUrl($request->fullUrl())){
    //             if($brands){
    //                 return ApiBaseMethod::sendResponse(null, 'Brand has been deleted successfully');
    //             }else{
    //                 return ApiBaseMethod::sendError('Something went wrong, please try again.');
    //             }
    //         }else{
    //             if($brands){
    //                 Toastr::success('Operation successful', 'Success');
    //                 return redirect('module');
    //             }else{
    //                 Toastr::error('Operation Failed', 'Failed');
    //                 return redirect()->back();
    //             }
    //         }
    //     }catch (\Exception $e) {
    //        Toastr::error('Operation Failed', 'Failed');
    //        return redirect()->back(); 
    //     }
    // }
}
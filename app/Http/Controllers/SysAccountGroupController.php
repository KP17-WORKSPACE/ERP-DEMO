<?php

namespace App\Http\Controllers;

use App\SysChartofAccounts;
use App\ApiBaseMethod;
use App\SmSupplier;
use App\SysAccountGroup;
use App\SysAccountGroupSub;
use App\SysAccountGroupSub2;
use App\SysAccountType;
use App\SysCustSuppl;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SysAccountGroupController extends Controller
{
    public function __construct(){
        $this->middleware('PM');
    }
    

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function accountgroupAdd(Request $request)
    {
        try{
            $accountgroup = SysAccountGroup::where('status',1)->get();
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse($accountgroup, null);
            }
            return view('backEnd.accounts.accountgroupadd', compact('accountgroup'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }


    public function store(Request $request)
    {
        // return $request;
        $input = $request->all();
        $validator = Validator::make($input, [
            'title'=> "required",
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
            $accountgroup = new SysAccountGroup();
            $accountgroup->title = $request->title;
            $accountgroup->status = 1;
            $accountgroup->created_by = Auth::user()->id;
            
            $results = $accountgroup->save();
     
             if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                 if ($results) {
                     return ApiBaseMethod::sendResponse(null, 'Account Group has been added successfully');
                 } else {
                     return ApiBaseMethod::sendError('Something went wrong, please try again');
                 }
             } else {
                 if ($results) {
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

    public function store2(Request $request)
    {        

        try{
            $accountgroup = new SysAccountGroup();
            $accountgroup->title = $request->title;
            $accountgroup->status = 1;
            $accountgroup->created_by = Auth::user()->id;
            $results = $accountgroup->save();
                        
            $ret = SysAccountGroup::all();
            return json_encode(array('data'=>$ret));

        }catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data'=>$ret));
        }
    }

    public function edit(Request $request,$id)
    {
       try{           
        $accountgroup = SysAccountGroup::where('status',1)->get();
        $editData = SysAccountGroup::find($id);
        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
             $data = [];
             $data['editData'] = $accountgroup->toArray();
             $data['editData'] = $editData->toArray();
             return ApiBaseMethod::sendResponse($data, null);
        }
        return view('backEnd.accounts.accountgroupadd', compact('editData', 'accountgroup'));
        }catch (\Exception $e) {
        Toastr::error('Operation Failed', 'Failed');
        return redirect()->back(); 
        }
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'title'=> "required",
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
            $accountgroup = SysAccountGroup::find($id);            
            $accountgroup->title = $request->title;
            $accountgroup->status = 1;            
            $accountgroup->updated_by = Auth()->user()->id;
            $results = $accountgroup->update();

             if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                 if ($results) {
                     return ApiBaseMethod::sendResponse(null,  'Account Group has been updated successfully');
                 } else {
                     return ApiBaseMethod::sendError('Something went wrong, please try again');
                 }
             } else {
                 if ($results) {
                    Toastr::success('Operation successful', 'Success');
                    return redirect('accountgroup-add');
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


    public function delete($id){
          try{
            $accountgroupsub = SysAccountGroupSub::where('group_id',$id)->get();
            $accountgroupsub2 = SysAccountGroupSub2::where('group_id',$id)->get();
            $chartofaccounts = SysChartofAccounts::where('group',$id)->get();
            $custsuppl = SysCustSuppl::where('group',$id)->get();

            if(count($accountgroupsub)>0){
                Toastr::error('This Group Has Sub Group', 'Failed');
                return redirect()->back();
            }
            if(count($accountgroupsub2)>0){
                Toastr::error('This Group Has Sub Group', 'Failed');
                return redirect()->back();                
            }

            if(count($chartofaccounts)>0){
                SysAccountGroup::where('id',$id)->update(['status' => 0]);
                Toastr::success('Deleted successful', 'Success');
                return redirect()->back();                
            }
            if(count($custsuppl)>0){
                SysAccountGroup::where('id',$id)->update(['status' => 0]);
                Toastr::success('Deleted successful', 'Success');
                return redirect()->back();                
            }

            SysAccountGroup::where('id',$id)->delete();

            
            Toastr::success('Deleted successful', 'Success');
            return redirect()->back();  
         }catch (\Exception $e) {
            return $e;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
}
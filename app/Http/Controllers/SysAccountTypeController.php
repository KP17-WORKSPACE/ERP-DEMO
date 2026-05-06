<?php

namespace App\Http\Controllers;

use App\SysChartofAccounts;
use App\ApiBaseMethod;
use App\SmSupplier;
use App\SysAccountType;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SysAccountTypeController extends Controller
{
    public function __construct(){
        $this->middleware('PM');
    }
    

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function accounttypeAdd(Request $request)
    {
        try{
            $accounttype = SysAccountType::all();
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse($accounttype, null);
            }
            return view('backEnd.accounts.accounttypeadd', compact('accounttype'));
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
            $accounttype = new SysAccountType();
            $accounttype->title = $request->title;
            $accounttype->status = 1;
            $accounttype->created_by = Auth::user()->id;
            
            $results = $accounttype->save();
     
             if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                 if ($results) {
                     return ApiBaseMethod::sendResponse(null, 'Account Type has been added successfully');
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
            $accounttype = new SysAccountType();
            $accounttype->title = $request->title;
            $accounttype->status = 1;
            $accounttype->created_by = Auth::user()->id;            
            $results = $accounttype->save();
                        
            $ret = SysAccountType::all();
            return json_encode(array('data'=>$ret));

        }catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data'=>$ret));
        }
    }

    public function edit(Request $request,$id)
    {
       try{           
        $accounttype = SysAccountType::all();
        $editData = SysAccountType::find($id);
        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
             $data = [];
             $data['editData'] = $accounttype->toArray();
             $data['editData'] = $editData->toArray();
             return ApiBaseMethod::sendResponse($data, null);
        }
        return view('backEnd.accounts.accounttypeadd', compact('editData', 'accounttype'));
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
            $accounttype = SysAccountType::find($id);            
            $accounttype->title = $request->title;
            $accounttype->status = 1;            
            $accounttype->updated_by = Auth()->user()->id;
            $results = $accounttype->update();

             if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                 if ($results) {
                     return ApiBaseMethod::sendResponse(null,  'Account Type has been updated successfully');
                 } else {
                     return ApiBaseMethod::sendError('Something went wrong, please try again');
                 }
             } else {
                 if ($results) {
                    Toastr::success('Operation successful', 'Success');
                    return redirect('accounttype-add');
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


    public function delete(Request $request,$id){
        
        //  try{
        //     if (ApiBaseMethod::checkUrl($request->fullUrl())) {
        //         return ApiBaseMethod::sendResponse($id, null);
        //     }
        //      return view('backEnd.inventory.deleteSupportView', compact('id'));
        // }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        //}
    }

    // public function deleteSupplier(Request $request,$id){
        
    //     try{
    //         $result = SmSupplier::destroy($id);

    //         if (ApiBaseMethod::checkUrl($request->fullUrl())) {
    //             if ($result) {
    //                 return ApiBaseMethod::sendResponse(null, 'Supplier has been deleted successfully');
    //             } else {
    //                 return ApiBaseMethod::sendError('Something went wrong, please try again.');
    //             }
    //         } else {
    //             if ($result) {
    //                 Toastr::success('Operation successful', 'Success');
    //                 return redirect('suppliers');
    //             } else {
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
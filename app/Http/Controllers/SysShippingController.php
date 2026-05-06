<?php

namespace App\Http\Controllers;

use App\SysChartofAccounts;
use App\SysAccountType;
use App\ApiBaseMethod;
use App\SmSupplier;
use App\SysShipping;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SysShippingController extends Controller
{
    public function __construct(){
        $this->middleware('PM');
    }
    

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function shippingAdd(Request $request)
    {
        try{
            $shipping = SysShipping::all();
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse($shipping, null);
            }
            return view('backEnd.shipping.shippingadd', compact('shipping'));
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
            'shipping_name'=> "required",
            'contact_name'=> "required",
            'contact_no'=> "required",
            'address1'=> "required",
            'address2'=> "required",
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
            $shipping = new SysShipping();
            $shipping->shipping_name = $request->shipping_name;
            $shipping->contact_name = $request->contact_name;
            $shipping->contact_no = $request->contact_no;
            $shipping->address1 = $request->address1;
            $shipping->address2 = $request->address2;
            $shipping->status = 1;
            $shipping->created_by = Auth::user()->id;
            
            $results = $shipping->save();
     
             if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                 if ($results) {
                     return ApiBaseMethod::sendResponse(null, 'New Company has been added successfully');
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
            $shipping = new SysShipping();
            $shipping->shipping_name = $request->shipping_name;
            $shipping->contact_name = $request->contact_name;
            $shipping->contact_no = $request->contact_no;
            $shipping->address1 = $request->address1;
            $shipping->address2 = $request->address2;
            $shipping->status = 1;
            $shipping->created_by = Auth::user()->id;            
            $results = $shipping->save();
                        
            $ret = SysShipping::all();
            return json_encode(array('data'=>$ret));

        }catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data'=>$ret));
        }
    }

    public function edit(Request $request,$id)
    {
       try{           
        $shipping = SysShipping::all();
        $editData = SysShipping::find($id);
        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
             $data = [];
             $data['editData'] = $shipping->toArray();
             $data['editData'] = $editData->toArray();
             return ApiBaseMethod::sendResponse($data, null);
        }
        return view('backEnd.shipping.shippingadd', compact('editData', 'shipping'));
        }catch (\Exception $e) {
        Toastr::error('Operation Failed', 'Failed');
        return redirect()->back(); 
        }
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'shipping_name'=> "required",
            'contact_name'=> "required",
            'contact_no'=> "required",
            'address1'=> "required",
            'address2'=> "required",
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
            $shipping = SysShipping::find($id);            
            $shipping->shipping_name = $request->shipping_name;
            $shipping->contact_name = $request->contact_name;
            $shipping->contact_no = $request->contact_no;
            $shipping->address1 = $request->address1;
            $shipping->address2 = $request->address2;
            $shipping->status = 1;            
            $shipping->updated_by = Auth()->user()->id;
            $results = $shipping->update();

             if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                 if ($results) {
                     return ApiBaseMethod::sendResponse(null,  'Company has been updated successfully');
                 } else {
                     return ApiBaseMethod::sendError('Something went wrong, please try again');
                 }
             } else {
                 if ($results) {
                    Toastr::success('Operation successful', 'Success');
                    return redirect('shipping-add');
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
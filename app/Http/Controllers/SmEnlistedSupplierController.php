<?php

namespace App\Http\Controllers;

use App\ApiBaseMethod;
use App\SmEnlistedSupplier;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Validator;

class SmEnlistedSupplierController extends Controller
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
            $suppliers = SmEnlistedSupplier::where('active_status',1)->orderBy('created_at','desc')->get();
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse($suppliers, null);
            }
            return view('backEnd.EnlistedSuppliers.EnlistedSuppliersList', compact('suppliers'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }


    public function ajaxSearchEnlistedSupplier(){
        $suppliers = SmEnlistedSupplier::where('active_status',1)->orderBy('created_at','desc')->get();

        $searchData = [];
        foreach($suppliers as $item){
            $searchData[] =  ['id' => $item->id, 'name' => $item->company_name]; 
        }

        if(!empty($searchData)){
            return json_encode($searchData);
        }
        
    }
 
    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
           'company_name' => "required",
           'company_address' => "required",
           'contact_person_name' => "required",
           'contact_person_mobile' => "required"
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
            $suppliers = new SmEnlistedSupplier();
            $suppliers->company_name = $request->company_name;
            $suppliers->company_address = $request->company_address;
            $suppliers->contact_person_name = $request->contact_person_name;
            $suppliers->contact_person_mobile = $request->contact_person_mobile;
            $suppliers->contact_person_email = $request->contact_person_email;
            $suppliers->description = $request->description;
            // $suppliers->created_by = Auth()->user()->id;
            $results = $suppliers->save();
     
             if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                 if ($results) {
                     return ApiBaseMethod::sendResponse(null, 'New Enlisted Supplier has been added successfully');
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

    public function edit(Request $request,$id)
    {
       
       try{
        $editData = SmEnlistedSupplier::find($id);
        $suppliers = SmEnlistedSupplier::all();
         if (ApiBaseMethod::checkUrl($request->fullUrl())) {
             $data = [];
             $data['editData'] = $editData->toArray();
             $data['suppliers'] = $suppliers->toArray();
             return ApiBaseMethod::sendResponse($data, null);
         }
        return view('backEnd.EnlistedSuppliers.EnlistedSuppliersList', compact('editData', 'suppliers'));
        }catch (\Exception $e) {
        Toastr::error('Operation Failed', 'Failed');
        return redirect()->back(); 
        }
    }


    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
           'company_name' => "required",
           'company_address' => "required",
           'contact_person_name' => "required",
           'contact_person_mobile' => "required"
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
            $suppliers = SmEnlistedSupplier::find($id);
            $suppliers->company_name = $request->company_name;
            $suppliers->company_address = $request->company_address;
            $suppliers->contact_person_name = $request->contact_person_name;
            $suppliers->contact_person_mobile = $request->contact_person_mobile;
            $suppliers->contact_person_email = $request->contact_person_email;
            $suppliers->description = $request->description;
            $suppliers->updated_by = Auth()->user()->id;
            $results = $suppliers->update();
     
             if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                 if ($results) {
                     return ApiBaseMethod::sendResponse(null,  'Enlisted Supplier has been updated successfully');
                 } else {
                     return ApiBaseMethod::sendError('Something went wrong, please try again');
                 }
             } else {
                 if ($results) {
                    Toastr::success('Operation successful', 'Success');
                     return redirect('enlisted-suppliers');
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


    public function deleteSupplierView(Request $request,$id){

         try{
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse($id, null);
            }
             return view('backEnd.EnlistedSuppliers.deleteEnSupplierView', compact('id'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function deleteSupplier(Request $request,$id){
        
        try{
            $result = SmEnlistedSupplier::destroy($id);

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($result) {
                    return ApiBaseMethod::sendResponse(null, 'Enlisted Supplier has been deleted successfully');
                } else {
                    return ApiBaseMethod::sendError('Something went wrong, please try again.');
                }
            } else {
                if ($result) {
                    Toastr::success('Operation successful', 'Success');
                    return redirect('enlisted-suppliers');
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

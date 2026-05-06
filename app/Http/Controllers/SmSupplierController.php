<?php

namespace App\Http\Controllers;

use App\SmSupplier;
use App\SysPaymentTerms;
use App\ApiBaseMethod;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SmSupplierController extends Controller
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

            $suppliers = SmSupplier::all();
            $paymentterms = SysPaymentTerms::where('active_status', '=', '1')->get();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse($suppliers, null);
            }
            return view('backEnd.inventory.supplierList', compact('suppliers','paymentterms'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function addsupplier(Request $request)
    {
        
        try{

            $suppliers = SmSupplier::all();
            $paymentterms = SysPaymentTerms::where('active_status', '=', '1')->get();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse($suppliers, null);
            }
            return view('backEnd.inventory.supplierAdd', compact('suppliers','paymentterms'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function store(Request $request)
    {
        //return $request;
        $input = $request->all();
        $validator = Validator::make($input, [
           'supplier_code' => "required",
           'supplier_name' => "required",
           'supplier_address' => "required",
           'contact_person_name' => "required",
           'contact_person_mobile' => "required",
           'contact_person_email' => "required",
           'vat_number' => "required",
           'payment_terms' => "required",
           'credit_days' => "required",
           'accountant_name' => "required",
           'accountant_email' => "required",
           'accountant_number' => "required",
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
            $suppliers = new SmSupplier();
            $suppliers->supplier_code = $request->supplier_code;
            $suppliers->supplier_name = $request->supplier_name;
            $suppliers->supplier_address = $request->supplier_address;
            $suppliers->contact_person_name = $request->contact_person_name;
            $suppliers->contact_person_mobile = $request->contact_person_mobile;
            $suppliers->contact_person_email = $request->contact_person_email;            
            $suppliers->vat_number = $request->vat_number;
            $suppliers->payment_terms = $request->payment_terms;
            $suppliers->credit_days = $request->credit_days;
            $suppliers->accountant_name = $request->accountant_name;
            $suppliers->accountant_email = $request->accountant_email;
            $suppliers->accountant_number = $request->accountant_number;
            $suppliers->status = 1;
            $suppliers->created_by = Auth::user()->id;
            $results = $suppliers->save();
     
             if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                 if ($results) {
                     return ApiBaseMethod::sendResponse(null, 'New Supplier has been added successfully');
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
        $editData = SmSupplier::find($id);
        $suppliers = SmSupplier::all();
        $paymentterms = SysPaymentTerms::where('active_status', '=', '1')->get();
 
         if (ApiBaseMethod::checkUrl($request->fullUrl())) {
             $data = [];
             $data['editData'] = $editData->toArray();
             $data['suppliers'] = $suppliers->toArray();
             return ApiBaseMethod::sendResponse($data, null);
         }
        return view('backEnd.inventory.supplierAdd', compact('editData', 'suppliers','paymentterms'));
        }catch (\Exception $e) {
        Toastr::error('Operation Failed', 'Failed');
        return redirect()->back(); 
        }
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'supplier_code' => "required",
            'supplier_name' => "required",
            'supplier_address' => "required",
            'contact_person_name' => "required",
            'contact_person_mobile' => "required",
            'contact_person_email' => "required",
            'vat_number' => "required",
            'payment_terms' => "required",
            'credit_days' => "required",
            'accountant_name' => "required",
            'accountant_email' => "required",
            'accountant_number' => "required",
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
            $suppliers = SmSupplier::find($id);
            $suppliers->supplier_code = $request->supplier_code;
            $suppliers->supplier_name = $request->supplier_name;
            $suppliers->supplier_address = $request->supplier_address;
            $suppliers->contact_person_name = $request->contact_person_name;
            $suppliers->contact_person_mobile = $request->contact_person_mobile;
            $suppliers->contact_person_email = $request->contact_person_email;            
            $suppliers->vat_number = $request->vat_number;
            $suppliers->payment_terms = $request->payment_terms;
            $suppliers->credit_days = $request->credit_days;
            $suppliers->accountant_name = $request->accountant_name;
            $suppliers->accountant_email = $request->accountant_email;
            $suppliers->accountant_number = $request->accountant_number;
            $suppliers->updated_by = Auth()->user()->id;
            $results = $suppliers->update();
     
             if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                 if ($results) {
                     return ApiBaseMethod::sendResponse(null,  'Supplier has been updated successfully');
                 } else {
                     return ApiBaseMethod::sendError('Something went wrong, please try again');
                 }
             } else {
                 if ($results) {
                    Toastr::success('Operation successful', 'Success');
                    return redirect('suppliers');
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
             return view('backEnd.inventory.deleteSupportView', compact('id'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function deleteSupplier(Request $request,$id){
        
        try{
            $result = SmSupplier::destroy($id);

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($result) {
                    return ApiBaseMethod::sendResponse(null, 'Supplier has been deleted successfully');
                } else {
                    return ApiBaseMethod::sendError('Something went wrong, please try again.');
                }
            } else {
                if ($result) {
                    Toastr::success('Operation successful', 'Success');
                    return redirect('suppliers');
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

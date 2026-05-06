<?php

namespace App\Http\Controllers;

use App\SysChartofAccounts;
use App\ApiBaseMethod;
use App\SmSupplier;
use App\SysAccountGroup;
use App\SysAccountType;
use App\SysCountries;
use App\SysCustSuppl;
use App\SysHelper;
use App\SysStates;
use App\SysVat;
use App\SysVatType;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SysVatController extends Controller
{
    public function __construct(){
        $this->middleware('PM');
    }
    

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function vatadd(Request $request)
    {
        try{
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $countries = SysCountries::all();
            $vattype = SysVatType::all();
            $vat = SysVat::wherein('company_id',$company_id)->get();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse($vat, null);
            }
            return view('backEnd.accounts.vatadd', compact('vat','countries','vattype'));
        }catch (\Exception $e) {
            return $e;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }


    public function store(Request $request)
    {
        try{
            $vat = new SysVat();
            $vat->vat_country = $request->country;
            //$vat->vat_state = $request->state;
            //$vat->vat_type = $request->vat_type;
            $vat->vat_percentage = $request->vat_percentage;
          
            $vat->vat_from = SysHelper::normalizeToYmd($request->vat_from);
            $vat->status = 1;
            $vat->created_by = Auth::user()->id;
            $vat->company_id = session('logged_session_data.company_id');
            $results = $vat->save();
     
    
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
                    
        }catch (\Exception $e) {
            return $e;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function edit(Request $request,$id)
    {
       try{
        $r = SysHelper::get_data_by_role();
        $company_id = $r[0];
           
        $countries = SysCountries::get();
        $states = SysStates::get();
        $vattype = SysVatType::get();
        $vat = SysVat::wherein('company_id',$company_id)->get();
        $editData = SysVat::where('id',$id)->where('company_id', session('logged_session_data.company_id'))->first();
        return view('backEnd.accounts.vatadd', compact('editData', 'vat', 'vattype', 'countries','states'));
        }catch (\Exception $e) {
        Toastr::error('Operation Failed', 'Failed');
        return redirect()->back(); 
        }
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();
        
        try{
            $vat = SysVat::find($id);
            $vat->vat_country = $request->country;
            //$vat->vat_state = $request->state;
            //$vat->vat_type = $request->vat_type;
            $vat->vat_percentage = $request->vat_percentage;
            $vat->vat_from = SysHelper::normalizeToYmd($request->vat_from); 
            $vat->status = $request->status;
            $vat->company_id = session('logged_session_data.company_id');
            $vat->updated_by = Auth()->user()->id;
            $results = $vat->update();

             if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                 if ($results) {
                     return ApiBaseMethod::sendResponse(null,  'VAT has been updated successfully');
                 } else {
                     return ApiBaseMethod::sendError('Something went wrong, please try again');
                 }
             } else {
                 if ($results) {
                    Toastr::success('Operation successful', 'Success');
                    return redirect('vat-settings');
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
        try{
            SysVat::where('id',$id)->delete();
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back();
        }
    }

    public function apply(Request $request,$id){        
        try{
            $vat = SysVat::select('vat_country','vat_percentage')->where('id',$id)->first();
            SysCustSuppl::where('vat_country',$vat->vat_country)->where('vat_is_fixed',0)->update(['vat_percentage' => $vat->vat_percentage]);
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        }catch (\Exception $e) {
            return $e;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back();
        }
    }

    public function getvatdetails(Request $request){
        $data = SysVat::select('id','vat_country','vat_percentage')->where('company_id',session('logged_session_data.company_id'))
        ->where('vat_country',$request->vat_id)
        ->where('status',1)
        ->whereRaw("DATE_FORMAT(vat_from, '%Y-%m-%d') < '" . date('Y-m-d') . "'")
        ->orderby('vat_from','desc')->first();
        return json_encode(array('data'=>$data));
        //return response()->json([$data]);
    }

    public function get_cust_supp_vat(Request $request){
        try{            
            $data = SysCustSuppl::select('vat_percentage')->where('id',$request->id)->get();
            return json_encode(array('data'=>$data));
        }catch (\Exception $e) {
            $data = 'ERROR';
            return json_encode(array('data'=>$data));
        }
    }

    public function get_cust_supp_vat_by_ca(Request $request){
        try{
        
            $data = SysCustSuppl::select('vat_percentage')
            ->join('sys_chartofaccounts as ca','ca.account_code','sys_cust_suppl.code')
            ->where('ca.id',$request->id)->first();
            return json_encode(array('data'=>$data));
        }catch (\Exception $e) {
            $data = 'ERROR';
            return json_encode(array('data'=>$data));
        }
        //return response()->json([$data]);
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
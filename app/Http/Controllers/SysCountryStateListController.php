<?php

namespace App\Http\Controllers;

use App\SysChartofAccounts;
use App\SysAccountType;
use App\ApiBaseMethod;
use App\Helper;
use App\Role;
use App\SmStaff;
use App\SmSupplier;
use App\SysAccountGroup;
use App\SysAccountGroupSub;
use App\SysAccountGroupSub2;
use App\SysCountries;
use App\SysCountryCode;
use App\SysCustSuppl;
use App\SysHelper;
use App\SysPaymentTerms;
use App\SysStates;
use App\SysCities;
use App\SysVat;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\Else_;

class SysCountryStateListController extends Controller
{
    public function __construct(){
        $this->middleware('PM');
    }
    

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function get_country(Request $request){    
        $select_country = SysCountries::select('id','name')->get();
        return response()->json([$select_country]);
    }
    public function get_state(Request $request){
        $select_state = SysStates::select('id','name')->where('country_id',$request->country_id)->get();
        return response()->json([$select_state]);
    }
     public function get_state_company(Request $request){
        $select_state = SysStates::select('id','name')->where('country_id',$request->country_id)->get();
        return response()->json([$select_state]);
    }

    // Return cities for a given state id
    public function get_city(Request $request){
        $select_city = SysCities::select('id','name')->where('state_id',$request->state_id)->get();
        return response()->json([$select_city]);
    }
    public function getstatewithvat(Request $request){
        $select_state = SysStates::select('sys_states.id as id','sys_states.name as name','sys_vat.vat_percentage as vat_percentage')
        ->leftjoin('sys_vat', 'sys_vat.vat_country','sys_states.country_id')
        ->where('sys_states.country_id',$request->country_id)->orderby('sys_states.name','asc')->get();
        return response()->json([$select_state]);
    }
    public function get_vat_state(Request $request){    
        $select_vat = SysVat::select('id','vat_type','vat_percentage')->where('vat_state',$request->state_vat)->first();
        return response()->json([$select_vat]);
    }
    public function get_customer_vat(Request $request){    
        //$select_vat_by_company = SysCustSuppl::select('id','vat_number','vat_country','vat_state','vat_type','vat_percentage')->where('id',$request->customer_with_vat)->get();        
        $select_vat_by_company = DB::table('sys_cust_suppl')
        ->join('sys_vat_type','sys_vat_type.id','sys_cust_suppl.vat_type')
        ->join('sys_countries','sys_countries.id','sys_cust_suppl.vat_country')
        ->join('sys_states','sys_states.id','sys_cust_suppl.vat_state')
        ->select('sys_cust_suppl.id','sys_cust_suppl.vat_number','sys_countries.name as cname','sys_states.name as sname','sys_vat_type.type','sys_cust_suppl.vat_percentage')        
        ->where('sys_cust_suppl.id',$request->customer_with_vat)->get();
        return response()->json([$select_vat_by_company]);
    }


    public function addNewCity(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'modal_city' => 'required|string|max:255',
            'modal_state' => 'required|integer|exists:sys_states,id',
            'modal_country' => 'required|integer|exists:sys_countries,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        dd($request->all());

        $city = new SysCities();
        $city->name = $request->modal_city;
        $city->state_id = $request->modal_state;
        $city->country_id = $request->modal_country;
        $city->flag = 1; // Assuming new cities are active by default
        $city->save();

        return response()->json(['success' => 'City added successfully', 'city_id' => $city->id, 'city_name' => $city->name]);
    }


}
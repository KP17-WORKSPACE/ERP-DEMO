<?php

namespace App\Http\Controllers;

use App\ApiBaseMethod;
use App\SysCurrencySettings;
use App\SysHelper;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SysCurrencySettingsController extends Controller
{

    public function __construct()
    {
        $this->middleware('PM');
    }

    public function index(Request $request)
    {
        try{
            $currencysettings = SysCurrencySettings::where('active_status', 1)->get();
            if(ApiBaseMethod::checkUrl($request->fullUrl())){
                return ApiBaseMethod::sendResponse($currencysettings, null);
            }
            return view('backEnd.humanResource.currency_settings', compact('currencysettings'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => "required",
            'code' => "required",
            'symbol' => "required",
            'rate' => "required",
        ]);

        if($validator->fails()){
            if(ApiBaseMethod::checkUrl($request->fullUrl())){
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        try{
            $currencysettings = new SysCurrencySettings();
            $currencysettings->name = $request->name;
            $currencysettings->code = $request->code;
            $currencysettings->symbol = $request->symbol;
            $currencysettings->rate = $request->rate;
            $currencysettings->created_by = Auth::user()->id;
            $result = $currencysettings->save();
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

    public function show(Request $request, $id)
    {
        try{
            $editmode = SysCurrencySettings::find($id);
            $currencysettings = SysCurrencySettings::all();

            if(ApiBaseMethod::checkUrl($request->fullUrl())){
                $data=[];
                $data['currencysettings']= $currencysettings->toArray();
                $data['editmode']= $editmode->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.humanResource.currency_settings', compact('currencysettings', 'editmode'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => "required",
            'code' => "required",
            'symbol' => "required",
            'rate' => "required",
        ]);

        if($validator->fails()){
            if(ApiBaseMethod::checkUrl($request->fullUrl())){
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        try{
            $currencysettings = SysCurrencySettings::find($request->id);
            $currencysettings->name = $request->name;
            $currencysettings->code = $request->code;
            $currencysettings->symbol = $request->symbol;
            $currencysettings->rate = $request->rate;
            $currencysettings->updated_by = Auth::user()->id;
            $result = $currencysettings->save();
            if(ApiBaseMethod::checkUrl($request->fullUrl())){
                if($result){
                    return ApiBaseMethod::sendResponse(null, 'Currency has been updated successfully');
                }else{
                    return ApiBaseMethod::sendError('Something went wrong, please try again.');
                }
            }else{
                if($result){
                    Toastr::success('Operation successful', 'Success');
                    return redirect('currency-settings');
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
            $currencysettings = SysCurrencySettings::destroy($id);
            if(ApiBaseMethod::checkUrl($request->fullUrl())){
                if($currencysettings){
                    return ApiBaseMethod::sendResponse(null, 'Currency has been deleted successfully');
                }else{
                    return ApiBaseMethod::sendError('Something went wrong, please try again.');
                }
            }else{
                if($currencysettings){
                    Toastr::success('Operation successful', 'Success');
                    return redirect('payment-terms');
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

    function add_currency_rate(Request $request)
    {
        try{
                $data[] = [
                    'from_currency' => $request->from_currency,
                    'to_currency' => $request->to_currency,
                    'from_date' => SysHelper::normalizeToYmd($request->from_date),
                    'rate' => $request->rate,
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                ];

            DB::table('sys_currency_rate')->insert($data);
            
            
            $ret = DB::table('sys_currency_rate as r')->select('r.*','c.code')
            ->join('sys_currency as c','c.id','r.to_currency')
            ->where('from_currency',$request->from_currency)->get();
            if(count($ret)>0){
                return json_encode(array('data'=>$ret));
            }else{
                $ret=[];
                return json_encode(array('data'=>$ret));
            }
        }catch (\Exception $e) {
            //$ret = 'ERROR';
            $ret = $e;
            return json_encode(array('data'=>$ret));
        }
    }
    function update_currency_rate(Request $request)
    {
        try{
            DB::table('sys_currency_rate')->where('id',$request->rate_id)->update([
                'from_currency' => $request->from_currency,
                'to_currency' => $request->to_currency,
                'from_date' => SysHelper::normalizeToYmd($request->from_date),
                'rate' => $request->rate,
                'status' => 1,
                'updated_by' => Auth::user()->id,
                'updated_at' => Carbon::now('+04:00'),
            ]);
            
            
            $ret = DB::table('sys_currency_rate as r')->select('r.*','c.code')
            ->join('sys_currency as c','c.id','r.to_currency')
            ->where('from_currency',$request->from_currency)->get();
            if(count($ret)>0){
                return json_encode(array('data'=>$ret));
            }else{
                $ret=[];
                return json_encode(array('data'=>$ret));
            }
        }catch (\Exception $e) {
            //$ret = 'ERROR';
            $ret = $e;
            return json_encode(array('data'=>$ret));
        }
    }
    function view_currency_rate(Request $request)
    {
        try{
            $ret = DB::table('sys_currency_rate as r')->select('r.*','c.code')
            ->join('sys_currency as c','c.id','r.to_currency')
            ->where('from_currency',$request->from_currency)->get();
            if(count($ret)>0){
                return json_encode(array('data'=>$ret));
            }else{
                $ret=[];
                return json_encode(array('data'=>$ret));
            }
        }catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data'=>$ret));
        }
    }
    function delete_currency_rate(Request $request)
    {
        try{
            DB::table('sys_currency_rate')->where('id',$request->id)->delete();
            $ret = DB::table('sys_currency_rate as r')->select('r.*','c.code')
            ->join('sys_currency as c','c.id','r.to_currency')
            ->where('from_currency',$request->from_currency)->get();
            
            if(count($ret)>0){
                return json_encode(array('data'=>$ret));
            }else{
                $ret=[];
                return json_encode(array('data'=>$ret));
            }
        }catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data'=>$ret));
        }
    }
}

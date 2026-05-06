<?php

namespace App\Http\Controllers;
use App\SmAddExpense;
use App\SmCostCenter;
use App\SmSubAccount; 
use App\SmChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Brian2694\Toastr\Facades\Toastr;
use Modules\Project\Entities\InfixProject;

class SmChartOfAccountController extends Controller
{

    public function __construct()
    {
        $this->middleware('PM');
    }
    
    public function index()
    {
        try{
            $chart_of_accounts = SmChartOfAccount::all();
            return view('backEnd.accounts.chart_of_account', compact('chart_of_accounts'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function costCenter() {
        
        try{
            $costCenters=  SmCostCenter::all();
            $product_list=  InfixProject::all();
            return view('backEnd.accounts.costCenter', compact('costCenters','product_list'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function costCenterEdit($id) {
        
        try{
            $singleData     =  SmCostCenter::find($id);
            $costCenters=  SmCostCenter::all();
            $product_list=  InfixProject::all();
            return view('backEnd.accounts.costCenter', compact('costCenters','singleData','product_list'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    /* ************************* Start cost Center Store ************************* */
    public function costCenterStore(Request $request) {
        $request->validate([
           'name' => "required",
        ]);
  
        try{
            $inputFields = [ 'name', 'description', 'active_status', 'is_existing_item', 'item_id'];
            $store = new SmCostCenter();
            foreach ($inputFields  as $inputField) {
                if(isset($request->$inputField)){
                    $store->$inputField = $request->$inputField;
                }
            }
            $isStore = $store->save();
            if($isStore){
                Toastr::success('Operation successful', 'Success');
                return redirect()->back();
            }else{
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            } 
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    /* ************************* end cost Center Store ************************* */

    /* ************************* Start cost Center Update ************************* */
    public function costCenterUpdate(Request $request) {
        $request->validate([
           'name' => "required",
        ]);
        
        try{
            $inputFields = [ 'name', 'description', 'active_status', 'is_existing_item', 'item_id'];
            $store =  SmCostCenter::find($request->id);
            foreach ($inputFields  as $inputField) {
                if(isset($request->$inputField)){
                    $store->$inputField = $request->$inputField;
                }
            }
            $isStore = $store->save();
            if($isStore){
                Toastr::success('Operation successful', 'Success');
                return redirect('cost-center');
            }else{
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back(); 
            } 
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
        
    }
    /* ************************* END cost Center Update ************************* */

    /* ************************* Start cost Center Delete ************************* */
    public function costCenterDelete($id) {
        DB::beginTransaction();
        try{
            $cost_center_delete=  SmCostCenter::find($id)->delete();

            
                $expense_delete = SmAddExpense::where('cost_center_id',$id)->delete();
                
           DB::commit();
                Toastr::success('Operation successful', 'Success');  
                return redirect('cost-center');
        } catch (\Exception $e) {
            DB::rollback(); 
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    /* ************************* End cost Center Delete ************************* */

    public function store(Request $request)
    {
        $request->validate([
            'head' => "required|unique:sm_chart_of_accounts,head",
            'type' => "required",
        ]);
       
        try{
            $chart_of_account = new SmChartOfAccount();
            $chart_of_account->head = $request->head;
            $chart_of_account->type = $request->type;
            $result = $chart_of_account->save();
            if($request->is_daily_expense_head ==1 && $request->type=='E'){
                $head_update = SmChartOfAccount::where('is_daily_expense_head',1)->update(['is_daily_expense_head'=> 0]);
                $head_update = SmChartOfAccount::where('id',$chart_of_account->id)->update(['is_daily_expense_head'=> 1]);
            } 
            if($result){
                Toastr::success('Operation successful', 'Success');  
                return redirect()->back();
            }else{
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
        
    }

    public function show($id)
    {
        try{
            $chart_of_account = SmChartOfAccount::find($id);
            $chart_of_accounts = SmChartOfAccount::all();
            return view('backEnd.accounts.chart_of_account', compact('chart_of_account', 'chart_of_accounts'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
        
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'head' => "required|unique:sm_chart_of_accounts,head,".$request->id,
            'type' => "required",
        ]);
        try{
            if($request->is_daily_expense_head ==1 && $request->type=='E'){
                $head_update = SmChartOfAccount::where('is_daily_expense_head',1)->update(['is_daily_expense_head'=> 0]);
                $head_update = SmChartOfAccount::where('id',$request->id)->update(['is_daily_expense_head'=> 1]);
            } 
            $chart_of_account = SmChartOfAccount::find($request->id);
            $chart_of_account->head = $request->head;
            $chart_of_account->type = $request->type; 
            $result = $chart_of_account->save();
    
            if($result){
                Toastr::success('Operation successful', 'Success');  
                return redirect('chart-of-account');
            }else{
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
        
    }


    public function destroy($id)
    {
        try{
            $chart_of_account = SmChartOfAccount::destroy($id);
            if($chart_of_account){
                Toastr::success('Operation successful', 'Success');  
                return redirect('chart-of-account');
            }else{
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
        
    }


/* ****************** subAccount ********************************** */
    public function subAccount() {
        
        try{
            $costCenters=  SmSubAccount::all();
            $SmChartOfAccount=  SmChartOfAccount::all();
            return view('backEnd.accounts.subAccount', compact('costCenters','SmChartOfAccount'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
        
    }

/* ****************** subAccount ********************************** */



/* ****************** subAccount ********************************** */
    public function subAccountEdit($id) {
        
        try{
            $singleData     =  SmSubAccount::find($id);
            $costCenters=  SmSubAccount::all(); 
            $SmChartOfAccount=  SmChartOfAccount::all();
            return view('backEnd.accounts.subAccount', compact('costCenters','singleData','SmChartOfAccount'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
        
    }
/* ****************** subAccount ********************************** */



    /* ************************* Start subAccount Store ************************* */
    public function subAccountStore(Request $request) {
        $request->validate([
           'head_id' => "required",
           'sub_head' => "required",
        ]);
        
        try{
            $inputFields = [ 'head_id', 'sub_head', 'description'];
            $store = new SmSubAccount();
            foreach ($inputFields  as $inputField) {
                if(isset($request->$inputField)){
                    $store->$inputField = $request->$inputField;
                }
            }
            $store->is_approved= 1;
            $isStore = $store->save();
            if($isStore){
                Toastr::success('Operation successful', 'Success');  
                return redirect()->back();
            }else{
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            } 
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
        
    }
    /* ************************* end subAccount Store ************************* */

    /* ************************* Start subAccount Update ************************* */
    public function subAccountUpdate(Request $request) {

        $request->validate([
           'head_id' => "required",
           'sub_head' => "required",
        ]);
        
        try{
            $inputFields = [ 'head_id', 'sub_head', 'description'];
            $store =  SmSubAccount::find($request->id);
            foreach ($inputFields  as $inputField) {
                if(isset($request->$inputField)){
                    $store->$inputField = $request->$inputField;
                }
            }
            $isStore = $store->save();
            if($isStore){
                Toastr::success('Operation successful', 'Success');  
                return redirect('sub-account');
            }else{
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            } 
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
        
    }
    /* ************************* END cost Center Update ************************* */




    /* ************************* StartsubAccount Delete ************************* */
    public function subAccountDelete($id) {
            
        try{
            $result=  SmSubAccount::find($id)->delete();
            if($result){
                Toastr::success('Operation successful', 'Success');  
                return redirect('sub-account');
            }else{
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        }catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back(); 
        }
            
    }
    /* ************************* End subAccount Delete ************************* */



}

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
use App\SysChartofAccountsTransaction;
use App\SysCountries;
use App\SysCountryCode;
use App\SysCustSuppl;
use App\SysHelper;
use App\SysPaymentTerms;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\Else_;

class SysBalancesheetController extends Controller
{
    public function __construct(){
        $this->middleware('PM');
    }
    
    public function index(Request $request)
    {
        try{
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $com_id = session('logged_session_data.company_id');
            $period="";
            $from_date="";
            $to_date="";
            $sub_group=[];
            $sub_group_2=[];
            $accounts=[];
            $net_profit = 0;
            $net_loss = 0;
            $net_profit_till = 0;
            $net_loss_till = 0;
            $asset_sum = 0;
            $liability_sum = 0;
            $isPL=0;
            $stock=0;

            if($_POST){
                if($request->period != ""){
                    if($request->period==2){ $from_date=date('Y-m-d'); $to_date=date('Y-m-d'); }
                    if($request->period==3){ $from_date=date('Y-m-d', strtotime('-1 week sunday 00:00:00')); $to_date=date('Y-m-d', strtotime('saturday 23:59:59')); }
                    if($request->period==4){ $from_date=date('Y-m-01'); $to_date=date('Y-m-t'); }
                    if($request->period==5){ 
                        if(date('m')==1 || date('m')==2 || date('m')==3){ $from_date=date('Y-01-01'); $to_date=date('Y-03-31'); }
                        if(date('m')==4 || date('m')==5 || date('m')==6){ $from_date=date('Y-04-01'); $to_date=date('Y-06-30'); }
                        if(date('m')==7 || date('m')==8 || date('m')==9){ $from_date=date('Y-07-01'); $to_date=date('Y-09-30'); }
                        if(date('m')==10 || date('m')==11 || date('m')==12){ $from_date=date('Y-10-01'); $to_date=date('Y-12-31'); }
                    }
                    if($request->period==6){ $from_date=date('Y-01-01'); $to_date=date('Y-12-31'); }
                    if($request->period==7){ $from_date=date('Y-m-d', strtotime('-1 day')); $to_date=date('Y-m-d', strtotime('-1 day')); }
                    if($request->period==8){ $from_date=date('Y-m-d', strtotime('first day of this month - 1 months')); $to_date=date('Y-m-d', strtotime('last day of this month - 1 months')); }
                    if($request->period==9){ $from_date=date('Y-m-d', strtotime('first day of this month - 1 months')); $to_date=date('Y-m-d'); }
                    if($request->period==10){ 
                        if(date('m')==1 || date('m')==2 || date('m')==3){ $from_date=date('Y-10-01', strtotime('-1 year')); $to_date=date('Y-12-31', strtotime('-1 year')); }
                        if(date('m')==4 || date('m')==5 || date('m')==6){ $from_date=date('Y-01-01'); $to_date=date('Y-03-31'); }
                        if(date('m')==7 || date('m')==8 || date('m')==9){ $from_date=date('Y-04-01'); $to_date=date('Y-06-30'); }
                        if(date('m')==10 || date('m')==11 || date('m')==12){ $from_date=date('Y-07-01'); $to_date=date('Y-09-30'); }
                    }
                    if($request->period==11 ){ $from_date=date('Y-01-01', strtotime('-1 year')); $to_date=date('Y-12-31', strtotime('-1 year')); }
                    if($request->period==12 ){ $from_date=date('Y-01-01', strtotime('-1 year')); $to_date=date('Y-m-d'); }
                    if($request->period==13 ){ $from_date=date('Y-m-01'); $to_date=date('Y-m-d'); }
                    if($request->period==14 ){ $from_date=date('Y-m-d'); $to_date=date('Y-m-t'); }
                    if($request->period==15 ){ $from_date=date('Y-01-01'); $to_date=date('Y-m-d'); }
                    if($request->period==16 ){ $from_date=date('Y-m-d'); $to_date=date('Y-12-31'); }

                     //$data_query->where('account_id',$request->account_id);
                     $period="";
                 }
                if($request->period == "" && $request->from_date !="" && $request->to_date !=""){
                    $from_date=$request->from_date;
                    $to_date=$request->to_date;
                }

                /*$users = User::select("*",
                \DB::raw('(CASE 
                    WHEN users.status = "0" THEN "User" 
                    WHEN users.status = "1" THEN "Admin" 
                    ELSE "SuperAdmin" 
                    END) AS status_lable'))
            ->get();*/
                $sub_group = DB::table('sys_account_group_sub as sub')
                ->select('sub.id','sub.group_id','sub.title',db::raw('
                (CASE WHEN ca.group = 1 THEN SUM(cat.debit_amount) - SUM(cat.credit_amount)
                WHEN ca.group = 2 THEN SUM(cat.credit_amount) - SUM(cat.debit_amount)
                WHEN ca.group = 5 THEN SUM(cat.credit_amount) - SUM(cat.debit_amount)
                ELSE SUM(cat.debit_amount) - SUM(cat.credit_amount) END) as amount'))
                ->join('sys_chartofaccounts as ca', 'ca.subgroup','sub.id')
                ->join('sys_chartofaccounts_transaction as cat','cat.account_id','ca.id')
                ->wherein('sub.group_id',[1,2,5])
                ->whereRaw("find_in_set($com_id,ca.company_access)")->where('cat.company_id', $com_id)
                //->wherein('ca.company_id',$company_id)
                //->whereRaw("DATE_FORMAT(cat.transaction_date, '%Y-%m-%d') >= '" . $from_date . "'")
                ->whereRaw("DATE_FORMAT(cat.transaction_date, '%Y-%m-%d') <= '" . $to_date . "'")
                ->where('cat.status',1)->wherenotin('cat.transaction_type',['opbinvoice'])
                ->groupby('sub.id','sub.title','ca.group')
                ->get();
                //return $sub_group;
                
                $sub_group_2 = DB::table('sys_account_group_sub2 as sub')
                ->select('sub.id','sub.sub_id','sub.title',db::raw('
                (CASE WHEN ca.group = 1 THEN SUM(cat.debit_amount) - SUM(cat.credit_amount)
                WHEN ca.group = 2 THEN SUM(cat.credit_amount) - SUM(cat.debit_amount)
                WHEN ca.group = 5 THEN SUM(cat.credit_amount) - SUM(cat.debit_amount)
                ELSE SUM(cat.debit_amount) - SUM(cat.credit_amount) END) as amount'))
                ->join('sys_chartofaccounts as ca', 'ca.subgroup2','sub.id')
                ->join('sys_chartofaccounts_transaction as cat','cat.account_id','ca.id')
                ->wherein('sub.group_id',[1,2,5])
                ->whereRaw("find_in_set($com_id,ca.company_access)")->where('cat.company_id', $com_id)
                //->wherein('ca.company_id',$company_id)
                //->whereRaw("DATE_FORMAT(cat.transaction_date, '%Y-%m-%d') >= '" . $from_date . "'")
                ->whereRaw("DATE_FORMAT(cat.transaction_date, '%Y-%m-%d') <= '" . $to_date . "'")
                ->where('cat.status',1)->wherenotin('cat.transaction_type',['opbinvoice'])
                ->groupby('sub.id','sub.title','ca.group')
                ->get();
                //return $sub_group_2;

                $accounts = DB::table('sys_chartofaccounts as ca')
                ->select('ca.subgroup2','ca.account_name',db::raw('
                (CASE WHEN ca.group = 1 THEN SUM(cat.debit_amount) - SUM(cat.credit_amount)
                WHEN ca.group = 2 THEN SUM(cat.credit_amount) - SUM(cat.debit_amount)
                WHEN ca.group = 5 THEN SUM(cat.credit_amount) - SUM(cat.debit_amount)
                ELSE SUM(cat.debit_amount) - SUM(cat.credit_amount) END) as amount'))
                ->join('sys_chartofaccounts_transaction as cat', 'cat.account_id','ca.id')
                ->wherein('ca.group',[1,2,5])
                ->whereRaw("find_in_set($com_id,ca.company_access)")->where('cat.company_id', $com_id)
                //->wherein('ca.company_id',$company_id)
                //->whereRaw("DATE_FORMAT(cat.transaction_date, '%Y-%m-%d') >= '" . $from_date . "'")
                ->whereRaw("DATE_FORMAT(cat.transaction_date, '%Y-%m-%d') <= '" . $to_date . "'")
                ->where('cat.status',1)->wherenotin('cat.transaction_type',['opbinvoice'])
                ->groupby('ca.subgroup2','ca.account_name','ca.group')
                ->get();

                //return $accounts;



                $chk_isPL=$accounts->where('account_name','Profit & Loss A/c (Reserve)');
                if(count($chk_isPL)>0){
                    $isPL=1;
                }

                $stock = SysProfitAndLossAccountController::get_closing_stock_update('1990-01-01',$to_date,$company_id);
                //$stock = SysProfitAndLossAccountController::get_item_stock('1990-01-01',$to_date,$company_id);
                

                $net_profit_loss = SysProfitAndLossAccountController::get_net_profit_loss($from_date,$to_date);
                $net_profit = $net_profit_loss['net-profit'];
                $net_loss = $net_profit_loss['net-loss'];
                
                $net_profit_loss_till = SysProfitAndLossAccountController::get_net_profit_loss_till_date($from_date);
                $net_profit_till = $net_profit_loss_till['net-profit'];
                $net_loss_till = $net_profit_loss_till['net-loss'];

                $general_reserve = SysProfitAndLossAccountController::get_general_reserve($from_date,$to_date,$company_id);
                //return $general_reserve;
                
                $asset_sum = abs($sub_group->wherein('group_id',[1])->sum('amount'));
                $asset_sum += $stock;

                $liability_sum = abs($sub_group->wherein('group_id',[2,5])->sum('amount'));
                $liability_sum += abs($net_profit);
                $liability_sum -= abs($net_loss);
                $liability_sum += abs($net_profit_till);
                $liability_sum -= abs($net_loss_till);
                

            }else{
                
            }
            

            return view('backEnd.balancesheet.view',compact('sub_group','sub_group_2','accounts','period','from_date','to_date','net_profit','net_loss','asset_sum','liability_sum','isPL','stock','net_profit_till','net_loss_till'));
        }catch (\Exception $e) {
            return $e;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function chartofaccountsAdd(Request $request)
    {
        try {
            $telcode = SysCountries::All();
            $accounts = SysChartofAccounts::where('status',1)->get();            
            $accountgroup = SysAccountGroup::all();
            $accounttype = SysAccountType::all();
            $accountgroupsub2 = SysAccountGroupSub2::all();

            $roles = Role::where('active_status', '=', '1')->where('id',2)->get();
            $paymentterms = SysPaymentTerms::where('active_status', '=', '1')->get();
            $staffs = SmStaff::select('id','full_name')->where('active_status', '=', '1')->whereIn('designation_id', array(9, 1, 2,3))->get();
            
            return view('backEnd.chart-of-accounts.chartofaccountsadd', compact('roles', 'paymentterms','staffs','accounts','accountgroup','accounttype','telcode','accountgroupsub2'));
            
        } catch (\Throwable $th) {
            return $th;
        }

            /*$accounts = SysChartofAccounts::all();
            $accountgroupsub = SysAccountGroupSub::all();
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse($accounts, null);
            }
            return view('backEnd.chart-of-accounts.chartofaccountsadd', compact('accounts','accountgroupsub'));
            */

    }

    public function get_subgroup2(Request $request){    
        $select_sub_group = SysAccountGroupSub2::select('id','title')->where('sub_id',$request->subgroup)->get();
        return response()->json([$select_sub_group]);
    }

    public function store(Request $request)
    {
        if($request->account_code==""){ Toastr::error('Account Code Missing', 'Failed'); return redirect()->back(); }
        if($request->account_name==""){ Toastr::error('Account Name Missing', 'Failed'); return redirect()->back(); }
        //if($request->group_id_sub==""){ Toastr::error('Account Type Missing', 'Failed'); return redirect()->back(); }
        //if($request->subgroup==""){ Toastr::error('Account Group Missing', 'Failed'); return redirect()->back(); }
        if($request->subgroup2==""){ Toastr::error('Account Sub Group Missing', 'Failed'); return redirect()->back(); }

        $input = $request->all();
        // $validator = Validator::make($input, [
        //     'subgroup'=> "required",
        //     'account_name'=> "required",
        // ]);

        // if ($validator->fails()) {
        //     if (ApiBaseMethod::checkUrl($request->fullUrl())) {
        //         return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
        //     }
        //     return redirect()->back()
        //         ->withErrors($validator)
        //         ->withInput();
        // }
        

        try{
            $accounts = new SysChartofAccounts();
            $accounts->account_code = $request->account_code;
            $accounts->account_name = $request->account_name;                        
            $groups = SysAccountGroupSub2::select('group_id','sub_id')->where('id',$request->subgroup2)->first();
            $accounts->group = $groups->group_id;
            $accounts->subgroup = $groups->sub_id;            
            $accounts->subgroup2 = $request->subgroup2;
            //$accounts->account_type = $request->account_type;
            //$accounts->billwise = $request->billwise;
            //$accounts->debitlimit = $request->debitlimit;
            $accounts->status = 1;
            $accounts->created_by = Auth::user()->id;            
            $results = $accounts->save();
     
             if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                 if ($results) {
                     return ApiBaseMethod::sendResponse(null, 'Account Info has been added successfully');
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

        $editData = SysChartofAccounts::find($id);
        $accounts = SysChartofAccounts::all();        
        $accountgroup = SysAccountGroup::all();
        $accountgroupsub = SysAccountGroupSub::all();
        $accountgroupsub2 = SysAccountGroupSub2::all();
        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
             $data = [];
             $data['editData'] = $editData->toArray();
             $data['accounts'] = $accounts->toArray();
             return ApiBaseMethod::sendResponse($data, null);
        }
        return view('backEnd.chart-of-accounts.chartofaccountsadd', compact('accounts', 'editData','accountgroup','accountgroupsub','accountgroupsub2'));
        }catch (\Exception $e) {
        Toastr::error('Operation Failed', 'Failed');
        return redirect()->back(); 
        }
    }

    public function update(Request $request, $id)
    {
        if($request->btnSubmit=="delete"){

            try {
                $accounts = SysChartofAccounts::find($id);
                $accounts->status = 0;
                $accounts->updated_by = Auth()->user()->id;
                $results = $accounts->update();
                
                Toastr::success('Operation successful', 'Success');
                return redirect('chartofaccounts-add');
    
            } catch (\Throwable $th) {
                return $th;
            }
            //DB::table('sys_chartofaccounts')->where('id', $id)->delete();
        }
        else if($request->btnSubmit=="update"){
        
            if($request->account_code==""){ Toastr::error('Account Code Missing', 'Failed'); return redirect()->back(); }
            if($request->account_name==""){ Toastr::error('Account Name Missing', 'Failed'); return redirect()->back(); }
            //if($request->group_id_sub==""){ Toastr::error('Account Type Missing', 'Failed'); return redirect()->back(); }
            //if($request->subgroup==""){ Toastr::error('Account Group Missing', 'Failed'); return redirect()->back(); }
            if($request->subgroup2==""){ Toastr::error('Account Sub Group Missing', 'Failed'); return redirect()->back(); }
            
            $input = $request->all();

            try{

                $accounts = SysChartofAccounts::find($id);

                $accounts->account_code = $request->account_code;
                $accounts->account_name = $request->account_name;                            
                $groups = SysAccountGroupSub2::select('group_id','sub_id')->where('id',$request->subgroup2)->first();
                $accounts->group = $groups->group_id;
                $accounts->subgroup = $groups->sub_id;                
                $accounts->subgroup2 = $request->subgroup2;
                $accounts->status = 1;
                $accounts->updated_by = Auth()->user()->id;
                $results = $accounts->update();
                //$accounts->account_type = $request->account_type;
                //$accounts->billwise = $request->billwise;
                //$accounts->debitlimit = $request->debitlimit;

                if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                    if ($results) {
                        return ApiBaseMethod::sendResponse(null,  'Account Info has been updated successfully');
                    } else {
                        return ApiBaseMethod::sendError('Something went wrong, please try again');
                    }
                } else {
                    if ($results) {
                        Toastr::success('Operation successful', 'Success');
                        return redirect('chartofaccounts-add');
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
        else{
            Toastr::error('Oops!! something went wrong', 'Failed');
            return redirect()->back(); 
        }
    }


    public function delete(Request $request,$id)
    {

        //return $id;
        
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
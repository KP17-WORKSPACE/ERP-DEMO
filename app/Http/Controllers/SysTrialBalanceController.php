<?php

namespace App\Http\Controllers;

use App\SysChartofAccounts;
use App\SysAccountType;
use App\ApiBaseMethod;
use App\SmSupplier;
use App\SysAccountGroup;
use App\SysAccountGroupSub;
use App\SysAccountGroupSub2;
use App\SysChartofAccountsTransaction;
use App\SysCustSuppl;
use App\SysHelper;
use App\SysLedgerEntries;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\Else_;

class SysTrialBalanceController extends Controller
{
    public function __construct(){
        $this->middleware('PM');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trialbalanceList(Request $request)
    {
        try{


            //return SysHelper::get_trial_balance_items_sub(10862,'2025-01-01','2025-09-24',"show");

            //return SysHelper::get_trial_balance_opening_by_group2_id(24,3,'2024-01-01','2024-11-28');
            //return SysHelper::get_trial_balance_opening_by_group2_id(24,3,'2024-04-01','2024-11-28');
            /*$trn1 = SysChartofAccountsTransaction::wherein('account_id',[1])->wherenotin('transaction_type',['openingbalance'])
            ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') < '2023-12-30'")->get();
            
            $trn2 = SysChartofAccountsTransaction::wherein('account_id',[1])->where('transaction_type','openingbalance')
            ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') < '2023-12-30'")->get();

            $trn = array_merge(array_merge($trn1->toArray(), $trn2->toArray()));
            return count($trn);*/
            
            //$dt_val= SysHelper::get_trial_balance_by_group2_id(15,1);
            //return $dt_val;
            
            //return SysHelper::get_trial_balance_opening_by_group_id(14, 3, '2024-01-01', '2024-07-04'); 
            //return SysHelper::get_trial_balance_opening_by_group_id(12,2,'2024-04-01','2024-07-07');
            //return SysHelper::get_trial_balance_opening_by_group2_id(40, 3, '2024-01-01', '2024-07-04');
            
            //return SysHelper::get_trial_balance_by_group_id(12, 2, '2024-04-01', '2024-07-05');
            //return SysHelper::get_trial_balance_by_group2_id(19, 2, '2024-04-01', '2024-07-05'); //22 20 19

            $period="";
            $from_date= date('Y-01-01');
            $to_date= date('Y-m-d');
            $filter_by="";
            $type="";
            $sub1="";
            $sub2="";
            $sub3="";
            $group = SysAccountGroupSub::where('status',1)->get();
            $data = SysAccountGroupSub2::where('status',1)->orderby('title','asc')->get();
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
                
                if ($request->from_date != "" && $request->filter_by == "") {
                    $from_date=Carbon::createFromFormat('d/m/Y', $request->from_date)->format('Y-m-d');
                }
                if ($request->to_date != "" && $request->filter_by == "") {
                    $to_date=Carbon::createFromFormat('d/m/Y', $request->to_date)->format('Y-m-d');
                }

                if ($request->filter_by == "this_month") {
                    $from_date=date('Y-m-01');
                    $to_date=date("Y-m-t", strtotime($from_date));
                    $filter_by='this_month';               
                }
                if ($request->filter_by == "today") {
                    $from_date=date('Y-m-d');
                    $to_date=date('Y-m-d');
                    $filter_by='today';
                }
                if ($request->filter_by == "this_week") {
                    $from_date = date('Y-m-d', strtotime('-1 week sunday 00:00:00'));
                    $to_date = date('Y-m-d', strtotime('saturday 23:59:59'));
                    $filter_by='this_week';
                }
                if ($request->filter_by == "last_week") {
                    $from_date = date('Y-m-d', strtotime('-2 week sunday 00:00:00'));
                    $to_date = date('Y-m-d', strtotime('-1 week saturday 23:59:59'));
                    $filter_by='last_week';
                }
                if ($request->filter_by == "last_month") {
                    $from_date = date('Y-m-d', strtotime('first day of previous month'));
                    $to_date = date('Y-m-d', strtotime('last day of previous month'));
                    $filter_by='last_month';
                }
                if ($request->filter_by == "this_quarter") {
                    $q_date = SysHelper::get_quarter(date('m'));
                    $from_date = $q_date[0];
                    $to_date = $q_date[1];
                    $filter_by='this_quarter';
                }
                if ($request->filter_by == "pre_quarter") {
                    $q_date = SysHelper::get_pre_quarter(date('m'));
                    $from_date = $q_date[0];
                    $to_date = $q_date[1];
                    $filter_by='pre_quarter';
                }
                if ($request->filter_by == "this_year") {
                    $from_date = date('Y-01-01');
                    $to_date = date('Y-12-31');
                    $filter_by='this_year';
                }
                if ($request->filter_by == "last_year") {
                    $from_date = date("Y-01-01",strtotime("-1 year"));
                    $to_date = date("Y-12-31",strtotime("-1 year"));
                    $filter_by='last_year';
                }

                if ($request->type == "1") {
                    $type = "1";
                    $sub1 ="";
                    $sub2 ="";
                    $sub3 ="";
                }
                if ($request->type == "2") {
                    $type = "2";
                    $sub1 ="show";
                    $sub2 ="";
                    $sub3 ="";
                }
                if ($request->type == "3") {
                    $type = "3";
                    $sub1 ="show";
                    $sub2 ="show";
                    $sub3 ="";
                }
                if ($request->type == "4") {
                    $type = "4";
                    $sub1 ="show";
                    $sub2 ="show";
                    $sub3 ="show";
                }
            }

            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $closing_stock = SysProfitAndLossAccountController::get_closing_stock_update($from_date,$to_date,$company_id);

            return view('backEnd.trial-balance.trialbalancelist', compact('group','data','period','from_date','to_date','filter_by','type','sub1','sub2','sub3','closing_stock'));
        }catch (\Exception $e) {
            return $e;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function search(Request $request)
    {
        //return $request;
        $from_date = ($request->from_date != "" ? date('Y-m-d', strtotime($request->from_date)) : '');
        $to_date = ($request->to_date != "" ? date('Y-m-d', strtotime($request->to_date)) : '');
        
        $from_date1 = $request->from_date;
        $to_date1 = $request->to_date;

        $period = $request->period;

// $date = new \Carbon\Carbon('-3 months');
// $firstOfQuarter = $date->firstOfQuarter();
// $lastOfQuarter = $date->lastOfQuarter();

//         if($period == 1){ $period = "All"; }
//         if($period == 2){ $period = date('Y-m-d'); }
//         if($period == 3){ $period = date('Y-m'); }
//         if($period == 4){'This Quarter'}
//         if($period == 5){'This Financial Year'}
//         if($period == 6){'Yesterday'}
//         if($period == 7){'Previous Month'}
//         if($period == 8){'Previous Quarter'}
//         if($period == 9){'Previous Financial Year'}
//         if($period == 10){'Previous Financial Year to Date'}
//         if($period == 11){'Month Start (to Date)'}
//         if($period == 12){'Month End (from Date)'}
//         if($period == 13){'Year Start (to Date)'}
//         if($period == 14){'Year End (from Date)'}

        if($from_date != "" && $to_date != "")
        {
            $search_result = DB::select("CALL get_trialbalance('$from_date','$to_date')");
            $search_result_openning_balance = DB::select("CALL get_opening_balance(2,'$from_date','$to_date',0)");

            $search_result_list = SysLedgerEntries::select('transaction_id','transaction_type','entry_date','entry_type','amount','account_id')
                                //->join('sys_chartofaccounts','sys_chartofaccounts.id','account_id')
                                ->whereBetween('entry_date', [$from_date, $to_date])->get();
                                // ->whereIn('transaction_type', ['postdatedreceipt','postdatedpayment'])
                                

            //return $search_result_list;
        }
        else if($period != "")
        {
            //return 'u2';
        }

        $accountgroup = SysAccountGroup::all();
        $accounts = SysChartofAccounts::all();
        // if (ApiBaseMethod::checkUrl($request->fullUrl())) {
        //     return ApiBaseMethod::sendResponse($accounts, null);
        // }
        
        //return $accountgroup;
        //return $editData;
        //return $search_result;

        return view('backEnd.trial-balance.trialbalancelist', compact('accountgroup','accounts','search_result','search_result_openning_balance','search_result_list','from_date1', 'to_date1', 'period'));

    }

    public function trialbalanceAdd(Request $request)
    {
        try{
            $accounts = SysChartofAccounts::all();
            $accountgroupsub = SysAccountGroupSub::all();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse($accounts, null);
            }
            return view('backEnd.trial-balance.trialbalanceadd', compact('accounts','accountgroupsub'));
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
            'subgroup'=> "required",
            'account_name'=> "required",
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
            $accounts = new SysChartofAccounts();
            //$accounts->account_code = $request->account_code;
            //$accounts->group = $request->group;
            $accounts->subgroup = $request->subgroup;
            //$accounts->account_type = $request->account_type;
            $accounts->account_name = $request->account_name;
            //$accounts->billwise = $request->billwise;
            //$accounts->debitlimit = $request->debitlimit;
            $accounts->status = 1;
            $accounts->created_by = Auth::user()->id;
            
            $results = $accounts->save();
     
             if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                 if ($results) {
                     return ApiBaseMethod::sendResponse(null, 'General Ledger Info has been added successfully');
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
        $accountgroupsub = SysAccountGroupSub::all();
        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
             $data = [];
             $data['editData'] = $editData->toArray();
             $data['accounts'] = $accounts->toArray();
             return ApiBaseMethod::sendResponse($data, null);
        }
        return view('backEnd.trial-balance.trialbalanceadd', compact('accounts', 'editData','accountgroupsub'));
        }catch (\Exception $e) {
        Toastr::error('Operation Failed', 'Failed');
        return redirect()->back(); 
        }
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'subgroup'=> "required",
            'account_name'=> "required",
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

            $accounts = SysChartofAccounts::find($id);
            //$accounts->account_code = $request->account_code;
            //$accounts->group = $request->group;
            $accounts->subgroup = $request->subgroup;
            //$accounts->account_type = $request->account_type;
            $accounts->account_name = $request->account_name;
            //$accounts->billwise = $request->billwise;
            //$accounts->debitlimit = $request->debitlimit;
            $accounts->status = 1;            
            $accounts->updated_by = Auth()->user()->id;
            $results = $accounts->update();

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
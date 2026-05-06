<?php

namespace App\Http\Controllers;

use App\SysChartofAccounts;
use App\SysAccountType;
use App\ApiBaseMethod;
use App\SmSupplier;
use App\SysAccountGroup;
use App\SysAccountGroupSub;
use App\SysChartofAccountsTransaction;
use App\SysCustSuppl;
use App\SysLedgerEntries;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\Else_;

class SysCustomerLedgerController extends Controller
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
            $account_id="";
            $accounts = SysChartofAccounts::select('id','account_name')->where('subgroup',1)->where('status',1)->orderby('account_name','asc')->get();
            $data_query = SysChartofAccountsTransaction::where('status',1);
            if($_POST){
                if($request->account_id != ""){
                    $data_query->where('account_id',$request->account_id);
                    $account_id=$request->account_id;
                }
            }
            else{
                $data_query->where('transaction_no','0');
            }
            $data = $data_query->get();
            return view('backEnd.customer-ledger.customerledgerlist',compact('data','accounts','account_id'));

        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function customeroutstanding(Request $request)
    {
        try{
            $account_id="";
            $accounts = SysChartofAccounts::select('id','account_name')->where('subgroup',1)->where('status',1)->orderby('account_name','asc')->get();
            $data_query = SysChartofAccountsTransaction::where('status',1);
            if($_POST){
                if($request->account_id != ""){
                    $data_query->where('account_id',$request->account_id);
                    $account_id=$request->account_id;
                }
            }
            else{
                $data_query->where('transaction_no','0');
            }
            $data = $data_query->get();
            return view('backEnd.customer-ledger.customeroutstanding',compact('data','accounts','account_id'));

        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function customeroutstandingpdc(Request $request)
    {
        try{
            $account_id="";
            $accounts = SysChartofAccounts::select('id','account_name')->where('subgroup',1)->where('status',1)->orderby('account_name','asc')->get();
            $data_query = SysChartofAccountsTransaction::where('status',1);
            if($_POST){
                if($request->account_id != ""){
                    $data_query->where('account_id',$request->account_id);
                    $account_id=$request->account_id;
                }
            }
            else{
                $data_query->where('transaction_no','0');
            }
            $data = $data_query->get();
            return view('backEnd.customer-ledger.customeroutstandingpdc',compact('data','accounts','account_id'));

        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function customerageing(Request $request)
    {
        try{
            $account_id="";
            $accounts = SysChartofAccounts::select('id','account_name')->where('subgroup',1)->where('status',1)->orderby('account_name','asc')->get();
            $data_query = SysChartofAccountsTransaction::where('status',1);
            if($_POST){
                if($request->account_id != ""){
                    $data_query->where('account_id',$request->account_id);
                    $account_id=$request->account_id;
                }
            }
            else{
                $data_query->where('transaction_no','0');
            }
            $data = $data_query->get();
            return view('backEnd.customer-ledger.customerageing',compact('data','accounts','account_id'));

        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
}
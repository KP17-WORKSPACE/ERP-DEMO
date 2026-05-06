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
use PhpParser\Node\Stmt\TryCatch;
use Carbon\Carbon;


class SysCashbookController extends Controller
{
    public function __construct(){
        $this->middleware('PM');
    }
    
    public function index(Request $request)
    {
        try{
            $accounts = SysHelper::get_cash_account();            
            $from_date = date('Y-m-01');
            $to_date = date('Y-m-d');
            $account_id = "";
            $filter_by="";
            $data = [];
            if($_POST){

          
                if ($request->from_date != "" && $request->filter_by == "") {
                    $from_date= SysHelper::normalizeToYmd($request->from_date);

                }
                if ($request->to_date != "" && $request->filter_by == "") {
                    $to_date=  SysHelper::normalizeToYmd($request->to_date); 
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

                // $from_date = $request->from_date
                //     ? Carbon::createFromFormat('d/m/Y', $request->from_date)->format('Y-m-d')
                //     : null;
                // $to_date =  $request->to_date
                //     ? Carbon::createFromFormat('d/m/Y', $request->to_date)->format('Y-m-d')
                //     : null;
                $account_id = $request->account_id;
            } else { 
                
                $from_date = $from_date;
                $to_date = $to_date;
                if(count($accounts)>0){
                    $account_id = $accounts[0]->id;
                } else { $account_id = 0; }
            }

            $queryra1 = "SELECT cat.transaction_no, cat.entry_no FROM sys_chartofaccounts_transaction AS cat
                JOIN sys_chartofaccounts AS ca ON ca.id=cat.account_id
                WHERE cat.account_id = '".$account_id."'
                and DATE_FORMAT(cat.transaction_date, '%Y-%m-%d') >= '" . $from_date . "'
                and DATE_FORMAT(cat.transaction_date, '%Y-%m-%d') <= '" . $to_date . "' order by cat.transaction_date asc";
                $resultsra1 = DB::select($queryra1); 
                
                $resultopb2 = DB::table('sys_chartofaccounts_transaction AS cat')
                ->select('cat.account_id','ca.account_name',DB::raw('SUM(cat.debit_amount) as debit_total'),DB::raw('SUM(cat.credit_amount) as credit_total'))
                ->join('sys_chartofaccounts AS ca', 'ca.id', 'cat.account_id')
                ->where('cat.account_id', $account_id)
                //->wherein('transaction_type',['openingbalance','openingstock'])
                ->whereRaw("DATE_FORMAT(cat.transaction_date, '%Y-%m-%d') < '" . $from_date . "'")
                ->where('cat.status',1)
                ->groupby('cat.account_id','ca.account_name')
                ->get();

                if(count($resultopb2)>0){
                    foreach ($resultopb2 as $resopb) {
                        $data[]=[
                            'account_id' => $resopb->account_id,
                            'account_name' => $resopb->account_name,
                            'transaction_no' => 'OPB',
                            'transaction_date' => $from_date,
                            'debit_amount' => $resopb->debit_total,
                            'credit_amount' => $resopb->credit_total,
                            'entry_no' => 1,
                            'remarks' => 'Openning Balance',
                            'transaction_id' => '0',
                        ];
                    }    
                }

                $resultopb3 = DB::table('sys_chartofaccounts_transaction AS cat')
                ->select('cat.account_id','ca.account_name','cat.transaction_no','cat.transaction_date','cat.debit_amount','cat.credit_amount','cat.entry_no','cat.remarks')
                ->join('sys_chartofaccounts AS ca', 'ca.id', 'cat.account_id')
                ->where('cat.account_id', $account_id)
                ->wherein('transaction_type',['openingbalance','openingstock'])
                ->whereRaw("DATE_FORMAT(cat.transaction_date, '%Y-%m-%d') >= '" . $from_date . "'")
                ->whereRaw("DATE_FORMAT(cat.transaction_date, '%Y-%m-%d') <= '" . $to_date . "'")
                ->where('cat.status',1)                      
                ->get();

                if(count($resultopb3)>0){
                    foreach ($resultopb3 as $resopb) {
                        $data[]=[
                            'account_id' => $resopb->account_id,
                            'account_name' => $resopb->account_name,
                            'transaction_no' => $resopb->transaction_no,
                            'transaction_date' => $from_date,
                            'debit_amount' => $resopb->debit_amount,
                            'credit_amount' => $resopb->credit_amount,
                            'entry_no' => 1,
                            'remarks' => $resopb->remarks,
                            'transaction_id' => '0',
                        ];
                    }    
                }
                
            if(count($resultsra1)>0){
                foreach ($resultsra1 as $res1) {
                $resultsra2 = DB::table('sys_chartofaccounts_transaction AS cat')
                ->select('cat.account_id','ca.account_name','cat.transaction_no','cat.transaction_id','cat.transaction_date','cat.debit_amount','cat.credit_amount','cat.entry_no','cat.remarks','cat.is_main_account')
                ->join('sys_chartofaccounts AS ca', 'ca.id', 'cat.account_id')
                ->where('cat.transaction_no', $res1->transaction_no)
                ->where('entry_no', $res1->entry_no)
                ->where('cat.status',1)
                ->orderby('cat.transaction_date','asc')
                ->get();

                    if(count($resultsra2)>0){
                        $dt = $resultsra2->where('account_id',$account_id)->first();
                        if($dt->is_main_account==1){
                            $bdata = $resultsra2->where('account_id','!=',$account_id);
                            foreach($bdata as $dt) {
                                $data[]=[
                                    'account_id' => $account_id,
                                    'account_name' => $dt->account_name,
                                    'transaction_no' => $dt->transaction_no,
                                    'transaction_date' => $dt->transaction_date,
                                    'debit_amount' => $dt->credit_amount,
                                    'credit_amount' => $dt->debit_amount,
                                    'entry_no' => $dt->entry_no,
                                    'remarks' => $dt->remarks,
                                    'transaction_id' => $dt->transaction_id,
                                ];
                            }
                        }
                        else{
                            $ret_data = SysHelper::get_ledger_data($resultsra2, $account_id);
                            //return SysHelper::get_ledger_data($resultsra2, $account_id);
                            
                            if($ret_data !=0 ){ $data[] = SysHelper::get_ledger_data($resultsra2, $account_id); }
                        }
                    }
                }
            }
            //return $data;
            return view('backEnd.cashbook.view',compact('data','accounts','account_id','from_date','to_date','filter_by'));

        }catch (\Exception $e) {
            return $e;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
}
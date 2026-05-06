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
use App\SysHelper;
use App\SysLedgerEntries;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\Else_;

class SysGeneralLedgerController extends Controller
{
    public function __construct(){
        $this->middleware('PM');
    }
    
    public function index(Request $request)
    {
        try{
            $com_ids = SysHelper::get_company_access();
            $account_id="";
            $from_date = "";
            $to_date = "";
            $accounts = SysChartofAccounts::select('id','account_name','group')->where('status',1)->orderby('account_name','asc')->get();
            $group=0;
            $data=[];

            $sales_account_id = SysHelper::get_sales_account_id();
            $sales_ret_account_id = SysHelper::get_sales_return_account_id();
            $sales_vat_account_id = SysHelper::get_sales_vat_account_id();
            
            $purchase_account_id = SysHelper::get_purchase_account_id();
            $purchase_ret_account_id = SysHelper::get_purchase_return_account_id();
            $purchase_vat_account_id = SysHelper::get_purchase_vat_account_id();

            if($_POST){
                if($request->account_id != ""){
                    $from_date = $request->from_date;
                    $to_date = $request->to_date;
                    $account_id=$request->account_id;
                    $group=$accounts->where('id',$account_id)->sum('group');
                    
                    $queryra1 = "SELECT cat.id, ca.account_name, cat.transaction_no, cat.transaction_date, cat.debit_amount, cat.credit_amount, cat.entry_no
                    FROM sys_chartofaccounts_transaction AS cat
                    JOIN sys_chartofaccounts AS ca ON ca.id=cat.account_id
                    WHERE cat.account_id = '".$account_id."'
                    and DATE_FORMAT(cat.transaction_date, '%Y-%m-%d') >= '" . $from_date . "'
                    and DATE_FORMAT(cat.transaction_date, '%Y-%m-%d') <= '" . $to_date . "' order by cat.transaction_date asc";
                    $resultsra1 = DB::select($queryra1);
                    //return $resultsra1;

                        $resultopb = DB::table('sys_chartofaccounts_transaction AS cat')
                        ->select('cat.account_id','ca.account_name',DB::raw('cat.debit_amount as debit_amount'),DB::raw('cat.credit_amount as credit_amount')
                        ,DB::raw("(SELECT 'Opening balance') as transaction_no"),DB::raw("(SELECT '".$from_date."') as transaction_date"),DB::raw("(SELECT '1') as entry_no"),DB::raw("(SELECT 'Opening balance b/d') as remarks"))
                        ->join('sys_chartofaccounts AS ca', 'ca.id', 'cat.account_id')
                        ->where('cat.account_id', $account_id)
                        //->wherein('transaction_type',['openingbalance','openingstock'])
                        ->whereRaw("DATE_FORMAT(cat.transaction_date, '%Y-%m-%d') < '" . $from_date . "'")
                        ->where('cat.status',1)
                        //->groupBy('cat.account_id','ca.account_name')                        
                        ->get();
                        //return $resultopb;
                        if(count($resultopb)>0){
                            foreach ($resultopb as $resopb) {
                                $data[]=[
                                    'account_id' => $resopb->account_id,
                                    'account_name' => $resopb->account_name,
                                    'transaction_no' => $resopb->transaction_no,
                                    'transaction_date' => $resopb->transaction_date,
                                    'debit_amount' => $resopb->debit_amount,
                                    'credit_amount' => $resopb->credit_amount,
                                    'entry_no' => $resopb->entry_no,
                                    'remarks' => $resopb->remarks,
                                ];
                            }    
                        } 
                        $resultopb2 = DB::table('sys_chartofaccounts_transaction AS cat')
                        ->select('cat.account_id','ca.account_name','cat.transaction_no','cat.transaction_date','cat.debit_amount','cat.credit_amount','cat.entry_no','cat.remarks')
                        ->join('sys_chartofaccounts AS ca', 'ca.id', 'cat.account_id')
                        ->where('cat.account_id', $account_id)
                        ->wherein('transaction_type',['openingbalance','openingstock'])
                        ->whereRaw("DATE_FORMAT(cat.transaction_date, '%Y-%m-%d') >= '" . $from_date . "'")
                        ->whereRaw("DATE_FORMAT(cat.transaction_date, '%Y-%m-%d') <= '" . $to_date . "'")
                        ->where('cat.status',1)                      
                        ->get();

                        if(count($resultopb2)>0){
                            foreach ($resultopb2 as $resopb) {
                                $data[]=[
                                    'account_id' => $resopb->account_id,
                                    'account_name' => $resopb->account_name,
                                    'transaction_no' => $resopb->transaction_no,
                                    'transaction_date' => $resopb->transaction_date,
                                    'debit_amount' => $resopb->debit_amount,
                                    'credit_amount' => $resopb->credit_amount,
                                    'entry_no' => $resopb->entry_no,
                                    'remarks' => $resopb->remarks,
                                ];
                            }    
                        }   

                        foreach ($resultsra1 as $res1) {
                            $resultsra2 = DB::table('sys_chartofaccounts_transaction AS cat')
                            ->select('cat.account_id','ca.account_name','cat.transaction_no','cat.transaction_date','cat.debit_amount','cat.credit_amount','cat.entry_no','cat.remarks','cat.is_main_account')
                            ->join('sys_chartofaccounts AS ca', 'ca.id', 'cat.account_id')
                            ->where('cat.transaction_no', $res1->transaction_no)
                            ->where('entry_no', $res1->entry_no)
                            ->where('transaction_type','!=','openingbalance')
                            ->where('cat.status',1)
                            ->orderby('cat.transaction_date','asc')
                            ->get();

                            //return $resultsra2;
                            
                            //sales account start
                            if($sales_account_id==$account_id) {
                                
                                if(count($resultsra2)>0){
                                    $sales_amount = 0;
                                    $sales_account = '';
                                    $account_id=0;
                                    $transaction_no=0;
                                    $transaction_date='';
                                    $entry_no='';
                                    $remarks='';
                                    foreach ($resultsra2 as $res2) {
                                        if($res2->account_id==$sales_account_id){
                                            $sales_amount=$res2->credit_amount;
                                            $account_id=$res2->account_id;
                                            $transaction_no=$res2->transaction_no;
                                            $transaction_date=$res2->transaction_date;
                                            $entry_no=$res2->entry_no;
                                            $remarks=$res2->remarks;
                                        }
                                        if($res2->account_id != $sales_account_id && $res2->account_id != $sales_vat_account_id && $res2->account_id != $sales_ret_account_id){
                                            $sales_account=$res2->account_name;
                                        }
                                    }
                                    if($sales_account_id==$account_id){
                                    $data[]=[
                                        'account_id' => $account_id,
                                        'account_name' => $sales_account,
                                        'transaction_no' => $transaction_no,
                                        'transaction_date' => $transaction_date,
                                        'debit_amount' => 0.00,
                                        'credit_amount' => $sales_amount,
                                        'entry_no' => $entry_no,
                                        'remarks' => $remarks,
                                    ];
                                    }
                                }

                            }
                            //sales account end

                            //sales Return start
                            else if($sales_ret_account_id==$account_id) {
                                if(count($resultsra2)>0){
                                    $sales_amount = 0;
                                    $sales_account = '';
                                    $account_id=0;
                                    $transaction_no=0;
                                    $transaction_date='';
                                    $entry_no='';
                                    $remarks='';
                                    foreach ($resultsra2 as $res2) {
                                        if($res2->account_id==$sales_ret_account_id){
                                            $sales_amount=$res2->debit_amount;
                                            $account_id=$res2->account_id;
                                            $transaction_no=$res2->transaction_no;
                                            $transaction_date=$res2->transaction_date;
                                            $entry_no=$res2->entry_no;
                                            $remarks=$res2->remarks;
                                        }
                                        if($res2->account_id != $sales_ret_account_id && $res2->account_id != $sales_vat_account_id){
                                            $sales_account=$res2->account_name;
                                        }
                                    }
                                    $data[]=[
                                        'account_id' => $account_id,
                                        'account_name' => $sales_account,
                                        'transaction_no' => $transaction_no,
                                        'transaction_date' => $transaction_date,
                                        'debit_amount' => $sales_amount,
                                        'credit_amount' => 0.00,
                                        'entry_no' => $entry_no,
                                        'remarks' => $remarks,
                                    ];
                                }

                            }
                            //sales Return end

                            //sales vat start
                            else if($sales_vat_account_id==$account_id) {
                                if(count($resultsra2)>0){
                                    $sales_amount = 0;
                                    $sales_account = '';
                                    $account_id=0;
                                    $transaction_no=0;
                                    $transaction_date='';
                                    $entry_no='';
                                    $remarks='';
                                    foreach ($resultsra2 as $res2) {
                                        if($res2->account_id==$sales_vat_account_id){
                                            $sales_amount=$res2->debit_amount;
                                            $sales_amount2=$res2->credit_amount;
                                            $account_id=$res2->account_id;
                                            $transaction_no=$res2->transaction_no;
                                            $transaction_date=$res2->transaction_date;
                                            $entry_no=$res2->entry_no;
                                            $remarks=$res2->remarks;
                                        }
                                        if($res2->account_id != $sales_ret_account_id && $res2->account_id != $sales_vat_account_id && $res2->account_id != $sales_account_id){
                                            $sales_account=$res2->account_name;
                                        }
                                    }
                                    $data[]=[
                                        'account_id' => $account_id,
                                        'account_name' => $sales_account,
                                        'transaction_no' => $transaction_no,
                                        'transaction_date' => $transaction_date,
                                        'debit_amount' => $sales_amount,
                                        'credit_amount' => $sales_amount2,
                                        'entry_no' => $entry_no,
                                        'remarks' => $remarks,
                                    ];
                                }
                            }
                            //sales vat end

                            //purchase account start
                            else if($purchase_account_id==$account_id) {
                                if(count($resultsra2)>0){
                                    $purchase_amount = 0;
                                    $purchase_account = '';
                                    $account_id=0;
                                    $transaction_no=0;
                                    $transaction_date='';
                                    $entry_no='';
                                    $remarks='';
                                    foreach ($resultsra2 as $res2) {
                                        if($res2->account_id==$purchase_account_id){
                                            $purchase_amount=$res2->debit_amount;
                                            $account_id=$res2->account_id;
                                            $transaction_no=$res2->transaction_no;
                                            $transaction_date=$res2->transaction_date;
                                            $entry_no=$res2->entry_no;
                                            $remarks=$res2->remarks;
                                        }
                                        if($res2->account_id != $purchase_account_id && $res2->account_id != $purchase_vat_account_id && $res2->account_id != $purchase_ret_account_id){
                                            $purchase_account=$res2->account_name;
                                        }
                                    }
                                    $data[]=[
                                        'account_id' => $account_id,
                                        'account_name' => $purchase_account,
                                        'transaction_no' => $transaction_no,
                                        'transaction_date' => $transaction_date,
                                        'debit_amount' => $purchase_amount,
                                        'credit_amount' => 0.00,
                                        'entry_no' => $entry_no,
                                        'remarks' => $remarks,
                                    ];
                                }

                            }
                            //purchase account end

                            //purchase Return start
                            else if($purchase_ret_account_id==$account_id) {
                                if(count($resultsra2)>0){
                                    $purchase_amount = 0;
                                    $purchase_account = '';
                                    $account_id=0;
                                    $transaction_no=0;
                                    $transaction_date='';
                                    $entry_no='';
                                    $remarks='';
                                    foreach ($resultsra2 as $res2) {
                                        if($res2->account_id==$purchase_ret_account_id){
                                            $purchase_amount=$res2->credit_amount;
                                            $account_id=$res2->account_id;
                                            $transaction_no=$res2->transaction_no;
                                            $transaction_date=$res2->transaction_date;
                                            $entry_no=$res2->entry_no;
                                            $remarks=$res2->remarks;
                                        }
                                        if($res2->account_id != $purchase_ret_account_id && $res2->account_id != $purchase_vat_account_id){
                                            $purchase_account=$res2->account_name;
                                        }
                                    }
                                    $data[]=[
                                        'account_id' => $account_id,
                                        'account_name' => $purchase_account,
                                        'transaction_no' => $transaction_no,
                                        'transaction_date' => $transaction_date,
                                        'debit_amount' => 0.00,
                                        'credit_amount' => $purchase_amount,
                                        'entry_no' => $entry_no,
                                        'remarks' => $remarks,
                                    ];
                                }

                            }
                            //purchase Return end

                            //purchase vat start
                            else if($purchase_vat_account_id==$account_id) {
                                if(count($resultsra2)>0){
                                    $purchase_amount = 0;
                                    $purchase_account = '';
                                    $account_id=0;
                                    $transaction_no=0;
                                    $transaction_date='';
                                    $entry_no='';
                                    $remarks='';
                                    foreach ($resultsra2 as $res2) {
                                        if($res2->account_id==$purchase_vat_account_id){
                                            $purchase_amount=$res2->debit_amount;
                                            $purchase_amount2=$res2->credit_amount;
                                            $account_id=$res2->account_id;
                                            $transaction_no=$res2->transaction_no;
                                            $transaction_date=$res2->transaction_date;
                                            $entry_no=$res2->entry_no;
                                            $remarks=$res2->remarks;
                                        }
                                        if($res2->account_id != $purchase_ret_account_id && $res2->account_id != $purchase_vat_account_id && $res2->account_id != $purchase_account_id){
                                            $purchase_account=$res2->account_name;
                                        }
                                    }
                                    $data[]=[
                                        'account_id' => $account_id,
                                        'account_name' => $purchase_account,
                                        'transaction_no' => $transaction_no,
                                        'transaction_date' => $transaction_date,
                                        'debit_amount' => $purchase_amount,
                                        'credit_amount' => $purchase_amount2,
                                        'entry_no' => $entry_no,
                                        'remarks' => $remarks,
                                    ];
                                }
                            }
                            //purchase vat end

                            else{
                                if(count($resultsra2)>0){
                                    foreach ($resultsra2 as $res2) {                                        
                                        
                                        if($res2->is_main_account==0 && $res2->account_id ==$account_id){
                                            $amt = $res2->debit_amount;
                                        }
                                        else if($res2->account_id==$account_id && $res2->debit_amount != '0.00'){
                                            $amt=$res2->debit_amount;
                                        }
                                        else{
                                            if($res2->debit_amount=="0.00"){
                                                $amt=$res2->credit_amount;
                                            }
                                            else {$amt=$res2->debit_amount;}
                                            
                                        }

                                        if($res2->account_id !=$account_id && $res2->credit_amount != '0.00'){
                                            $data[]=[
                                            'account_id' => $res2->account_id,
                                            'account_name' => $res2->account_name,
                                            'transaction_no' => $res2->transaction_no,
                                            'transaction_date' => $res2->transaction_date,
                                            'debit_amount' => $amt,
                                            'credit_amount' => '0.00',
                                            'entry_no' => $res2->entry_no,
                                            'remarks' => $res2->remarks,
                                            ];
                                        }
                                        if($res2->account_id==$account_id && $res2->credit_amount != '0.00'){
                                            $resultsra2_sub = $resultsra2->where('entry_no',$res2->entry_no)->wherenotin('account_id', $res2->account_id);
                                            if(count($resultsra2_sub)>0){
                                                foreach ($resultsra2_sub as $value) {
                                                    $data[]=[
                                                        'account_id' => $value->account_id,
                                                        'account_name' => $value->account_name,
                                                        'transaction_no' => $value->transaction_no,
                                                        'transaction_date' => $value->transaction_date,
                                                        'debit_amount' => $value->credit_amount,
                                                        'credit_amount' => $value->debit_amount,
                                                        'entry_no' => $value->entry_no,
                                                        'remarks' => $value->remarks,
                                                    ];   
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }

                    }
                }
            return view('backEnd.general-ledger.generalledgerlist',compact('data','accounts','account_id','from_date','to_date','group'));

        }catch (\Exception $e) {
            return $e;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }



}





/*

$data_query = SysChartofAccountsTransaction::select('sys_chartofaccounts_transaction.transaction_date','sys_chartofaccounts.account_name','sys_chartofaccounts_transaction.account_id',
            'sys_chartofaccounts_transaction.debit_amount','sys_chartofaccounts_transaction.credit_amount','sys_chartofaccounts_transaction.transaction_no','sys_chartofaccounts_transaction.remarks','sys_chartofaccounts_transaction.transaction_type')

            ->where('sys_chartofaccounts_transaction.status',1)->join('sys_chartofaccounts','sys_chartofaccounts.id','sys_chartofaccounts_transaction.account_id');

            if($_POST){
                if($request->account_id != ""){
                    $from_date = $request->from_date;
                    $to_date = $request->to_date;
                    $account_id=$request->account_id;
                    $trn_ids=SysChartofAccountsTransaction::select('id','account_id','transaction_no','debit_amount','credit_amount','transaction_type','entry_no')->where('account_id',$request->account_id)
                    ->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') >= '" . $from_date . "'")
                    ->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') <= '" . $to_date . "'")
                    ->wherein('company_id',$com_ids)->get();
                    
                    if(count($trn_ids)>0){
                        foreach($trn_ids as $receipt){

                            if($receipt->transaction_type=="    "){
                                $trn_id[]=$receipt->id;
                            }
                            else {

                                $dtids = SysChartofAccountsTransaction::select('id')->where('transaction_no',$receipt->transaction_no)
                                //->where('account_id',[$receipt->account_id])
                                ->wherenotin('account_id',[$request->account_id])
                                ->where('entry_no',$receipt->entry_no)
                                ->wherein('company_id',$com_ids)->get();
                            }

                            if(count($dtids)>0){
                                foreach($dtids as $ids){
                                    $trn_id[]=$ids->id;
                                }
                            }
                        }
                        $data_query->wherein('sys_chartofaccounts_transaction.id',$trn_id);
                        //$data_query->where('account_id',$request->account_id);
                    }else{
                        $data_query->where('sys_chartofaccounts_transaction.transaction_no','0');
                    }
                }
            }

*/
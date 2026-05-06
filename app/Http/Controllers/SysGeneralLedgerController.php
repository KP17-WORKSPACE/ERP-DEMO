<?php

namespace App\Http\Controllers;

use App\SysChartofAccounts;
use App\SysAccountType;
use App\ApiBaseMethod;
use App\SmSupplier;
use App\SysAccountGroup;
use App\SysAccountGroupSub;
use App\SysChartofAccountsTransaction;
use App\SysCompany;
use App\SysCustSuppl;
use App\SysHelper;
use App\SysLedgerEntries;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\Else_;
use Carbon\Carbon;

class SysGeneralLedgerController extends Controller
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
            session()->forget('gl_data');
            session()->forget('gl_data_jv');
            $account_id="";
            $ctrl_account_id="";
            $account_id_all=[];
            $from_date = "";
            $to_date = "";
            $filter_by="";
            //$accounts = SysChartofAccounts::select('id','account_name','group')->wherein('company_id',$company_id)->where('status',1)->orderby('account_name','asc')->get();
            $accounts = SysHelper::get_account_list_all_noget();
            $accounts_list = SysHelper::get_account_list_all();
            $group=0;
            $data=[];
            $data_all=[];
            $account_name=[];

            $sales_account_id = SysHelper::get_sales_account_id();
            $sales_ret_account_id = SysHelper::get_sales_return_account_id();
            $sales_vat_account_id = SysHelper::get_sales_vat_account_id();
            
            $purchase_account_id = SysHelper::get_purchase_account_id();
            $purchase_ret_account_id = SysHelper::get_purchase_return_account_id();
            $purchase_vat_account_id = SysHelper::get_purchase_vat_account_id();
            $sales_codes = SysCompany::select(DB::raw('substr(sales_code, 1, 2) as sales_code'))->get();
            $sales_code=[];
            foreach($sales_codes as $code){
                $sales_code[]=$code->sales_code;
            }

            if($_POST){
                 
                if($request->account_id != ""){
                    if(in_array("all",$request->account_id)){
                        $ctrl_account_id="all";
                        //$search = [7218,7219,7525,7413,9056,7222,9079,7455,7507,7422,7457,212,7452,7458]; //$accounts->pluck('id');
                        $search = $accounts->pluck('id');
                        //return $accounts;
                        //$account_id_all[] = $request->account_id;
                        $account_name = SysChartofAccounts::select('id','account_name','account_code')->wherein('id',$search)->orderby('account_name','asc')->get();
                    }
                    elseif(in_array("c",$request->account_id)){
                        $ctrl_account_id="c";
                        $search = $accounts->where('account_code','like','CUS%')->pluck('id');
                        $account_name = SysChartofAccounts::select('id','account_name','account_code')->wherein('id',$search)->orderby('account_name','asc')->get();
                    }
                    elseif(in_array("s",$request->account_id)){
                        $ctrl_account_id="s";
                        $search = $accounts->where('account_code','like','SUP%')->pluck('id');
                        $account_name = SysChartofAccounts::select('id','account_name','account_code')->wherein('id',$search)->orderby('account_name','asc')->get();
                    }
                    elseif(in_array("a",$request->account_id)){
                        $ctrl_account_id="a";
                        $search = $accounts->where('account_code','like','%ACC%')->pluck('id');
                        $account_name = SysChartofAccounts::select('id','account_name','account_code')->wherein('id',$search)->orderby('account_name','asc')->get();
                    }                    
                    else {
                        $search = $request->account_id;
                        $ctrl_account_id=$request->account_id;
                        //$account_id_all[] = $request->account_id;
                        $account_name = SysChartofAccounts::select('id','account_name','account_code')->wherein('id',$request->account_id)->orderby('account_name','asc')->get();
                    }
                    

                if ($request->from_date != "" && $request->filter_by == "") {
                   
                    $from_date= SysHelper::normalizeToYmd($request->from_date);
                }
                if ($request->to_date != "" && $request->filter_by == "") {
                    
                    $to_date= SysHelper::normalizeToYmd($request->to_date);
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

                    //START
                    for($i=0; $i < count($search); $i++)
                    {

                    

    
                    $account_id=$search[$i];
                    $group=$accounts->where('id',$account_id)->max('group');
                    
                    $queryra1 = "SELECT cat.id, ca.account_name, ca.account_code, cat.transaction_no, cat.transaction_date, cat.debit_amount, cat.credit_amount, cat.entry_no
                    FROM sys_chartofaccounts_transaction AS cat
                    JOIN sys_chartofaccounts AS ca ON ca.id=cat.account_id
                    WHERE cat.account_id = '".$account_id."' and cat.company_id = '".$com_id."' and cat.status=1 
                    and DATE_FORMAT(cat.transaction_date, '%Y-%m-%d') >= '" . $from_date . "'
                    and DATE_FORMAT(cat.transaction_date, '%Y-%m-%d') <= '" . $to_date . "' order by cat.transaction_date asc";
                    $resultsra1 = DB::select($queryra1);
                    //return $resultsra1;

                        $resultopb = DB::table('sys_chartofaccounts_transaction AS cat')
                        ->select('cat.account_id','ca.account_name','ca.account_code',DB::raw('sum(cat.debit_amount) as debit_amount'),DB::raw('sum(cat.credit_amount) as credit_amount')
                        ,DB::raw("(SELECT 'Opening balance') as transaction_no"),DB::raw("(SELECT '".$from_date."') as transaction_date"),DB::raw("(SELECT '1') as entry_no"),DB::raw("(SELECT 'Opening balance b/d') as remarks"))
                        ->join('sys_chartofaccounts AS ca', 'ca.id', 'cat.account_id')
                        ->where('cat.account_id', $account_id)->where('cat.company_id', $com_id)
                        //->wherein('transaction_type',['openingbalance','openingstock'])
                        ->wherenotin('transaction_type',['opbinvoice'])
                        ->whereRaw("DATE_FORMAT(cat.transaction_date, '%Y-%m-%d') < '" . $from_date . "'")
                        ->where('cat.status',1)
                        ->groupBy('cat.account_id','ca.account_name','ca.account_code')                        
                        ->get();
                        if(count($resultopb)>0){
                            foreach ($resultopb as $resopb) {
                                $data[]=[
                                    'account_id' => $resopb->account_id,
                                    'account_name' => $resopb->account_name,
                                    'account_code' => $resopb->account_code,
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
                        ->select('cat.account_id','ca.account_name','ca.account_code','cat.transaction_no','cat.transaction_date','cat.debit_amount','cat.credit_amount','cat.entry_no','cat.remarks')
                        ->join('sys_chartofaccounts AS ca', 'ca.id', 'cat.account_id')
                        ->where('cat.account_id', $account_id)->where('cat.company_id', $com_id)
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
                                    'account_code' => $resopb->account_code,
                                    'transaction_no' => $resopb->transaction_no,
                                    'transaction_date' => $resopb->transaction_date,
                                    'debit_amount' => $resopb->debit_amount,
                                    'credit_amount' => $resopb->credit_amount,
                                    'entry_no' => $resopb->entry_no,
                                    'remarks' => $resopb->remarks,
                                ];
                            }    
                        }

                        if(count($resultsra1)>0){
                        foreach ($resultsra1 as $res1) {
                            $resultsra2 = DB::table('sys_chartofaccounts_transaction AS cat')
                            ->select('cat.id','cat.account_id','ca.account_name','ca.account_code','cat.transaction_no','cat.transaction_id','cat.transaction_date','cat.debit_amount','cat.credit_amount','cat.entry_no','cat.remarks','cat.is_main_account')
                            ->join('sys_chartofaccounts AS ca', 'ca.id', 'cat.account_id')
                            ->where('cat.transaction_no', $res1->transaction_no)
                            ->where('entry_no', $res1->entry_no)
                            ->wherenotin('transaction_type',['openingbalance','opbinvoice'])
                            ->where('cat.status',1)
                            ->orderby('cat.transaction_date','asc')
                            ->get();
                            if(count($resultsra2)>0){
                                if(str_contains($resultsra2[0]->transaction_no,'JVR')||str_contains($resultsra2[0]->transaction_no,'BR-')||str_contains($resultsra2[0]->transaction_no,'CR-')||str_contains($resultsra2[0]->transaction_no,'BP-')||str_contains($resultsra2[0]->transaction_no,'CP-')){

                                    $dt = $resultsra2->where('account_id',$account_id)->first();
                                    if($dt->is_main_account==1){
                                        $bdata = $resultsra2->where('account_id','!=',$account_id);
                                        foreach($bdata as $dt) {
                                        $data[]=[
                                            'account_id' => $account_id,
                                            'account_name' => $dt->account_name,
                                            'account_name' => $dt->account_code,
                                            'transaction_no' => $dt->transaction_no,
                                            'transaction_date' => $dt->transaction_date,
                                            'debit_amount' => $dt->credit_amount,
                                            'credit_amount' => $dt->debit_amount,
                                            'entry_no' => $dt->entry_no,
                                            'remarks' => $dt->remarks,
                                        ];
                                    }
                                    }
                                    else{
                                        $ret_data = SysHelper::get_ledger_data($resultsra2, $account_id);
                                        if($ret_data !="" ){ $data[] = SysHelper::get_ledger_data($resultsra2, $account_id); }
                                    }

                                }
                                //$data[] =$resultsra2;
                                //$data[] =["sep" => "---------sep------------"];
                                //$data[] = SysHelper::get_ledger_data($resultsra2, $account_id);
                                //return SysHelper::get_ledger_data($resultsra2, $account_id);
                            }
                            //return $resultsra2;
                            
                            
                            //sales account start
                            if($sales_account_id==$account_id) {
                                
                                if(count($resultsra2)>0){
                                    $sales_amount = 0;
                                    $sales_account = '';
                                    $account_id=0;
                                    $account_code=0;
                                    $transaction_no=0;
                                    $transaction_date='';
                                    $entry_no='';
                                    $remarks='';
                                    foreach ($resultsra2 as $res2) {
                                        if($res2->account_id==$sales_account_id){
                                            $sales_amount=$res2->credit_amount;
                                            $account_id=$res2->account_id;
                                            $account_code=$res2->account_code;
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
                                        'account_code' => $account_code,
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
                                    $account_code=0;
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
                                        'account_code' => $account_code,
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
                                    $account_code =0;
                                    $transaction_no=0;
                                    $transaction_date='';
                                    $entry_no='';
                                    $remarks='';
                                    foreach ($resultsra2 as $res2) {
                                        if($res2->account_id==$sales_vat_account_id){
                                            $sales_amount=$res2->debit_amount;
                                            $sales_amount2=$res2->credit_amount;
                                            $account_id=$res2->account_id;
                                            $account_code=$res2->account_code;
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
                                        'account_code' => $account_code,
                                        'transaction_no' => $transaction_no,
                                        'transaction_date' => $transaction_date,
                                        'debit_amount' => $sales_amount,
                                        'credit_amount' => $sales_amount2,
                                        'entry_no' => $entry_no,
                                        'remarks' => $remarks,
                                    ];
                                }
                                //return $data;
                            }
                            //sales vat end

                            //purchase account start
                            else if($purchase_account_id==$account_id) {
                                if(count($resultsra2)>0){
                                    $purchase_amount = 0;
                                    $purchase_account = '';
                                    $account_id=0;
                                    $account_code=0;
                                    $transaction_no=0;
                                    $transaction_date='';
                                    $entry_no='';
                                    $remarks='';
                                    foreach ($resultsra2 as $res2) {
                                        if($res2->account_id==$purchase_account_id){
                                            $purchase_amount=$res2->debit_amount;
                                            $account_id=$res2->account_id;
                                            $account_code=$res2->account_code;
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
                                        'account_code' => $account_code,
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
                                    $account_code=0;
                                    $transaction_no=0;
                                    $transaction_date='';
                                    $entry_no='';
                                    $remarks='';
                                    foreach ($resultsra2 as $res2) {
                                        if($res2->account_id==$purchase_ret_account_id){
                                            $purchase_amount=$res2->credit_amount;
                                            $account_id=$res2->account_id;
                                            $account_code=$res2->account_code;
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
                                        'account_code' => $account_code,
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
                                    $account_code=0;
                                    $transaction_no=0;
                                    $transaction_date='';
                                    $entry_no='';
                                    $remarks='';
                                    foreach ($resultsra2 as $res2) {
                                        if($res2->account_id==$purchase_vat_account_id){
                                            $purchase_amount=$res2->debit_amount;
                                            $purchase_amount2=$res2->credit_amount;
                                            $account_id=$res2->account_id;
                                            $account_code=$res2->account_code;
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
                                        'account_code' => $account_code,
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
                                    if(!str_contains($resultsra2[0]->transaction_no,'JV') && !str_contains($resultsra2[0]->transaction_no,'BR') && !str_contains($resultsra2[0]->transaction_no,'CR') && !str_contains($resultsra2[0]->transaction_no,'BP') && !str_contains($resultsra2[0]->transaction_no,'CP')){
                                     
                                        foreach ($resultsra2 as $res2) {                                        
                                            
                                            if($res2->is_main_account==0 && $res2->account_id ==$account_id){
                                                $amt = $res2->debit_amount;
                                                $rem=$res2->remarks;
                                            }
                                            else if($res2->account_id==$account_id && $res2->debit_amount != '0.00'){
                                                $amt=$res2->debit_amount;
                                                $rem=$res2->remarks;
                                            }
                                            else{

                                                if($res2->debit_amount=="0.00"){
                                                    $ret=SysHelper::get_ledger_data_formated($res2->transaction_no,$account_id,$resultsra2);
                                                    //session('gl_data.remark');
                                                    //return $resultsra2;
                                                    //return $ret;
                                                    $amt = $ret[0];
                                                    $rem = $ret[1];
                                                    
                                                    //$amt=$res2->credit_amount;
                                                }
                                                else {$amt=$res2->debit_amount; $rem=$res2->remarks;}
                                                
                                            }

                                            if($res2->account_id !=$account_id && $res2->credit_amount != '0.00'){
                                                $data[]=[
                                                'account_id' => $res2->account_id,
                                                'account_name' => $res2->account_name,
                                                'account_code' => $res2->account_code,
                                                'transaction_no' => $res2->transaction_no,
                                                'transaction_date' => $res2->transaction_date,

                                                'debit_amount' => $amt,
                                                
                                                'credit_amount' => '0.00',
                                                'entry_no' => $res2->entry_no,
                                                'remarks' => $rem,
                                                ];
                                            }
                                            if($res2->account_id==$account_id && $res2->credit_amount != '0.00'){
                                                $resultsra2_sub = $resultsra2->where('entry_no',$res2->entry_no)->wherenotin('account_id', $res2->account_id);
                                                if(count($resultsra2_sub)>0){
                                                    foreach ($resultsra2_sub as $value) {
                                                        $data[]=[
                                                            'account_id' => $value->account_id,
                                                            'account_name' => $value->account_name,
                                                            'account_code' => $value->account_code,
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

                                    
                                    if(str_contains($resultsra2[0]->transaction_no,'JV') || str_contains($resultsra2[0]->transaction_no,'BR') || str_contains($resultsra2[0]->transaction_no,'CR') || str_contains($resultsra2[0]->transaction_no,'BP') || str_contains($resultsra2[0]->transaction_no,'CP')){

                                        $dt = $resultsra2->where('account_id',$account_id)->first();
                                        if($dt->is_main_account==1){
                                            $bdata = $resultsra2->where('account_id','!=',$account_id);
                                            foreach($bdata as $dt) {
                                                $data[]=[
                                                    'account_id' => $account_id,
                                                    'account_name' => $dt->account_name,
                                                    'account_code' => $dt->account_code,
                                                    'transaction_no' => $dt->transaction_no,
                                                    'transaction_date' => $dt->transaction_date,
                                                    'debit_amount' => $dt->credit_amount,
                                                    'credit_amount' => $dt->debit_amount,
                                                    'entry_no' => $dt->entry_no,
                                                    'remarks' => $dt->remarks,
                                                ];
                                            }
                                        }
                                        else{
                                            /*$ret_data = SysHelper::get_ledger_data($resultsra2, $account_id);
                                            return $ret_data;
                                            if($ret_data !="" ){ $data[] = SysHelper::get_ledger_data($resultsra2, $account_id); }*/

                                            $bdata = $resultsra2->where('account_id','=',$account_id);
                                            
                                            foreach($bdata as $dt) {
                                                $ret_data = SysHelper::get_ledger_data_formated_jv_receipt_payment($dt->transaction_no,$account_id, $resultsra2);
                                                //return $ret_data;
                                                //if($ret_data !="" ){ $data[] = SysHelper::get_ledger_data($resultsra2, $account_id); }
                                                if($ret_data[3] != "0"){
                                                    $data[]=[
                                                        'account_id' => $account_id,
                                                        'account_name' => $ret_data[3],
                                                        'account_code' => $dt->account_code,
                                                        'transaction_no' => $dt->transaction_no,
                                                        'transaction_date' => $dt->transaction_date,
                                                        'debit_amount' => $ret_data[0],
                                                        'credit_amount' => $ret_data[2],
                                                        'entry_no' => $dt->entry_no,
                                                        'remarks' => $ret_data[1],
                                                    ];
                                                }
                                            }



                                        }

                                    }


                                }
                            }                           
                        }
                     }
                     if(count($data)>0){
                        $data_all[] = $data;
                        $account_id_all[] = $search[$i];

                     }
                     $data=[];
                    }
                     //STOP


                    }
                }
                //return $data_all;
            
            // $isEmpty = !array_filter($data_all, function($item) {
            //     return !empty($item);
            // });
            // if ($isEmpty) {
            //     $data_all=[];
            // }
            //$filteredArray = array_filter($data_all, function($item) {
            //    return !empty($item);
            //});
            //$data_all=$filteredArray;
            //return $data_all;

            //return $data_all;
            //$data1 = $data_all[0];
            //return $data1[0]["account_id"];
            //return $account_id_all;
            if($ctrl_account_id != ""){
                $ctrl_account_id= $ctrl_account_id; }
            else{ $ctrl_account_id=""; }
                
            $account_name = $account_name = SysChartofAccounts::select('id','account_name','account_code')->wherein('id',$account_id_all)->orderby('account_name','asc')->get();

            //return $data_all;
            //return session('gl_data.id');


            if(isset($request->redirect_by_dealtrack) && $request->redirect_by_dealtrack == 1){

                if(isset($request->redirect_by_dealtrack_filter) && $request->redirect_by_dealtrack_filter == 1){
                    
                return view('backEnd.general-ledger.generalledgerlist-modal',compact('data_all','accounts','account_id','account_id_all','account_name','from_date','to_date','group','sales_code','ctrl_account_id','accounts_list','filter_by'));

                }
               
                return view('backEnd.general-ledger.generalledgerlist-modal',compact('data_all','accounts','account_id','account_id_all','account_name','from_date','to_date','group','sales_code','ctrl_account_id','accounts_list','filter_by'));
            }

            
            return view('backEnd.general-ledger.generalledgerlist',compact('data_all','accounts','account_id','account_id_all','account_name','from_date','to_date','group','sales_code','ctrl_account_id','accounts_list','filter_by'));

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
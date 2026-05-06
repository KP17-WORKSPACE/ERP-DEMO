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
use App\SysItemStock;
use App\SysPaymentTerms;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\Else_;

class SysProfitAndLossAccountController extends Controller
{
    public function __construct(){
        $this->middleware('PM');
    }
    
    public function index(Request $request)
    {
        try{
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $from_date = date('Y-01-01');
            $to_date =  date('Y-m-d');
            $filter_by="";
            
            $gross_profit=0;
            $gross_loss=0;

            $net_profit=0;
            $net_loss=0;

            $indirect_expenses=0;
            $indirect_income=0;
            $indirect_expenses_list=[];
            $indirect_income_list=[];
            $indirect_expenses_data=[];
            $indirect_income_data=[];
            $indirect_expenses_group=[];
            $indirect_income_group=[];
            $total_indirect_expenses = 0;
            $total_indirect_income = 0;

            if($_POST){
              
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

            }
            else{
                
            }


                $m = SysHelper::get_months_by_date($from_date,$to_date);

                $ie_acc_id = SysChartofAccounts::select('id','account_name')->where('subgroup',14)->wherein('company_id',$company_id)->get();
                
                $indirect_expenses = SysProfitAndLossAccountController::get_indirect_expenses($from_date,$to_date,$ie_acc_id);

                $indirect_expenses_group = DB::table('sys_account_group_sub2 as sub')
                ->select('sub.id','sub.title',DB::raw('SUM(debit_amount) as debit_amount'),DB::raw('SUM(credit_amount) as credit_amount'))
                ->join('sys_chartofaccounts as ca','ca.subgroup2','sub.id')
                ->join('sys_chartofaccounts_transaction as cat','cat.account_id','ca.id')
                ->where('sub.sub_id',14)                
                ->whereRaw("DATE_FORMAT(cat.transaction_date, '%Y-%m-%d') >= '" . $from_date . "'")
                ->whereRaw("DATE_FORMAT(cat.transaction_date, '%Y-%m-%d') <= '" . $to_date . "'")
                ->wherein('cat.company_id',$company_id)
                ->where('cat.status',1)
                ->wherenotin('cat.transaction_type',['opbinvoice'])
                ->groupby('sub.id','sub.title')->get();
                
                /*$indirect_expenses_data = DB::table('sys_chartofaccounts as c')->select('c.id','c.account_name','plan')->sum('debit_amount')
                ->join('sys_chartofaccounts_transaction as t','t.account_id','c.id')->where('c.subgroup',11)->where('c.status',1)
                ->whereRaw("DATE_FORMAT(t.transaction_date, '%Y-%m-%d') >= '".$from_date."' and DATE_FORMAT(t.transaction_date, '%Y-%m-%d') <= '".$to_date."'")
                ->where('debit_amount','>',0)->groupby('c.id','c.account_name','debit_amount','plan')->orderby('c.account_name','asc')->get();*/

                $indirect_expenses_data =  DB::table('sys_chartofaccounts as c')
                ->select('c.id', 'c.account_name', 'c.subgroup2', DB::raw('SUM(t.debit_amount) - SUM(t.credit_amount) as total_debit'))
                ->join('sys_chartofaccounts_transaction as t', 't.account_id', '=', 'c.id')
                ->where('c.subgroup', 14)
                ->where('c.status', 1)
                ->whereRaw("DATE_FORMAT(t.transaction_date, '%Y-%m-%d') >= '" . $from_date . "'")
                ->whereRaw("DATE_FORMAT(t.transaction_date, '%Y-%m-%d') <= '" . $to_date . "'")
                ->wherein('t.company_id',$company_id)
                //->where('t.debit_amount', '>', 0)
                ->where('t.status',1)
                ->wherenotin('t.transaction_type',['opbinvoice'])
                ->groupBy('c.id', 'c.account_name', 'c.subgroup2')
                ->orderBy('c.account_name', 'asc')
                ->get();
                
                if(count($indirect_expenses_data)>0){
                    foreach($indirect_expenses_data as $dt){
                        //if($dt->plan==0){
                            $indirect_expenses_list[] = ["subid" => $dt->subgroup2, "account_name" => $dt->account_name, "total_debit" => $dt->total_debit];
                        //}
                        //else{
                        //    $indirect_expenses_list[] = ["subid" => $dt->subgroup2, "account_name" => $dt->account_name, "total_debit" => SysHelper::get_amount_by_month($m,$dt->plan,$dt->total_debit)];
                        //}
                    }
                }

                $ii_acc_id = SysChartofAccounts::select('id','account_name')->where('subgroup',16)->wherein('company_id',$company_id)->get();
                $indirect_income = SysProfitAndLossAccountController::get_indirect_income($from_date,$to_date,$ii_acc_id);
                
                $indirect_income_group = DB::table('sys_account_group_sub2 as sub')
                ->select('sub.id','sub.title',DB::raw('SUM(debit_amount) as debit_amount'),DB::raw('SUM(credit_amount) as credit_amount'))
                ->join('sys_chartofaccounts as ca','ca.subgroup2','sub.id')
                ->join('sys_chartofaccounts_transaction as cat','cat.account_id','ca.id')
                ->where('sub.sub_id',16)
                ->whereRaw("DATE_FORMAT(cat.transaction_date, '%Y-%m-%d') >= '" . $from_date . "'")
                ->whereRaw("DATE_FORMAT(cat.transaction_date, '%Y-%m-%d') <= '" . $to_date . "'")
                ->wherein('cat.company_id',$company_id)
                ->where('cat.status',1)
                ->wherenotin('cat.transaction_type',['opbinvoice'])
                ->groupby('sub.id','sub.title')->get();

                /*$indirect_income_data = DB::table('sys_chartofaccounts as c')->select('c.id','c.account_name','credit_amount','plan')
                ->join('sys_chartofaccounts_transaction as t','t.account_id','c.id')->where('c.subgroup',13)->where('c.status',1)
                ->whereRaw("DATE_FORMAT(t.transaction_date, '%Y-%m-%d') >= '".$from_date."' and DATE_FORMAT(t.transaction_date, '%Y-%m-%d') <= '".$to_date."'")
                ->where('credit_amount','>',0)->orderby('c.account_name','asc')->get();*/

                $indirect_income_data =  DB::table('sys_chartofaccounts as c')
                ->select('c.id', 'c.account_name', 'c.subgroup2', DB::raw('SUM(t.credit_amount) - SUM(t.debit_amount) as total_credit'))
                ->join('sys_chartofaccounts_transaction as t', 't.account_id', '=', 'c.id')
                ->where('c.subgroup', 16)
                ->where('c.status', 1)
                ->whereRaw("DATE_FORMAT(t.transaction_date, '%Y-%m-%d') >= '" . $from_date . "'")
                ->whereRaw("DATE_FORMAT(t.transaction_date, '%Y-%m-%d') <= '" . $to_date . "'")
                ->wherein('t.company_id',$company_id)
                //->where('t.credit_amount', '>', 0)
                ->where('t.status',1)
                ->wherenotin('t.transaction_type',['opbinvoice'])
                ->groupBy('c.id', 'c.account_name', 'c.subgroup2')
                ->orderBy('c.account_name', 'asc')
                ->get();

                if(count($indirect_income_data)>0){
                    foreach($indirect_income_data as $dt){
                        //if($dt->plan==0){
                            $indirect_income_list[]= ["subid" => $dt->subgroup2, "account_name" => $dt->account_name, "total_credit" => $dt->total_credit];
                        //}
                        //else{
                        //    $indirect_income_list[] = ["subid" => $dt->subgroup2, "account_name" => $dt->account_name, "total_credit" => SysHelper::get_amount_by_month($m,$dt->plan,$dt->total_credit)];
                        //}
                    }
                }

                
                $ie_acc_id = SysChartofAccounts::select('id','account_name')->where('subgroup',13)->wherein('company_id',$company_id)->get();
                $direct_expenses = SysProfitAndLossAccountController::get_direct_expenses($from_date,$to_date,$ie_acc_id);
                $opening_stock = SysProfitAndLossAccountController::get_opening_stock_trading_new($from_date,$to_date,$company_id);
                $opening_stock_account = SysProfitAndLossAccountController::get_opening_stock_from_account($from_date,$to_date,$company_id);
                //return $opening_stock_account;
                $purchase = SysProfitAndLossAccountController::get_purchase($from_date,$to_date,$company_id);
                $purchase_return = SysProfitAndLossAccountController::get_purchase_return($from_date,$to_date,$company_id);

                $ie_acc_id = SysChartofAccounts::select('id','account_name')->where('subgroup',15)->wherein('company_id',$company_id)->where('status',1)->get();
                $direct_incomes = SysProfitAndLossAccountController::get_direct_income($from_date,$to_date,$ie_acc_id);
                $sales = SysProfitAndLossAccountController::get_sales($from_date,$to_date,$company_id);
                $sales_return = SysProfitAndLossAccountController::get_sales_return($from_date,$to_date,$company_id);
                $stock = SysProfitAndLossAccountController::get_item_stock($from_date,$to_date,$company_id);
                //$closing_stock = SysProfitAndLossAccountController::get_closing_stock($opening_stock,0,$purchase_return,$sales,$sales_return,$stock);
                $closing_stock = SysProfitAndLossAccountController::get_closing_stock_update($from_date,$to_date,$company_id);
                //$closing_stock_till = SysProfitAndLossAccountController::get_closing_stock_tilldate($from_date,$com_ids);
                //$opening_stock+=$closing_stock_till;


                //return [$direct_expenses,$opening_stock,$purchase,$purchase_return];

                //$total_direct_expences = ($direct_expenses + $opening_stock + abs($purchase - $purchase_return));  //subgroup 13 already taken purchase & purchase return account
                $total_direct_expences = ($direct_expenses + $opening_stock);
                //$total_direct_incomes = ($direct_incomes + $closing_stock + abs($sales - $sales_return));  // subgroup 15 already taken sales and sales return 
                $total_direct_incomes = ($direct_incomes + $closing_stock);
                
                //Direct Income - Direct Expence = Gross Profit
                if($total_direct_expences > $total_direct_incomes) {
                    $gross_loss = abs($total_direct_expences) - abs($total_direct_incomes) + $opening_stock_account;
                }
                else {
                    $gross_profit = abs($total_direct_incomes) - abs($total_direct_expences) + abs($opening_stock_account);
                }

                //Net Loss = Indirect Expenses  + Gross Loss - (Indirect Income)
                //Net Profit = Indirect Income + Gross Profit - (Indirect Expenses)
                //(Indirect income + Gross Profit) - Indirect Expence = Net Profit

                if($gross_profit > $gross_loss) {
                    if($indirect_expenses > ($indirect_income + $gross_profit)){
                        $net_loss = (abs($indirect_expenses) - abs($indirect_income)) - abs($gross_profit) ;   // need to check
                    } else {
                        $net_profit = (abs($indirect_income) - abs($indirect_expenses)) + abs($gross_profit);   // need to check
                    }
                } else {
                    if($indirect_expenses > ($indirect_income - $gross_loss)){
                        $net_loss =  (abs($indirect_expenses) - abs($indirect_income)) + abs($gross_loss);   // correct
                    } else {
                        $net_profit = (abs($indirect_income) - abs($indirect_expenses)) - abs($gross_loss);   // correct
                    }
                }
                //return $total_direct_expences .'/'. $total_direct_incomes .'/'. $indirect_expenses .'/'. $indirect_income .'/'. $gross_profit .'/'. $gross_loss;
                //return $direct_expenses .'/'. $opening_stock .'/'. $direct_incomes .'/'. $closing_stock;

                //return $net_profit;
                //return $indirect_expenses .'#'. $indirect_income .'#'. $gross_profit .'#'. $gross_loss;


                //$total_indirect_expenses = abs($indirect_expenses)+abs($net_profit)-abs($gross_loss);
                //$total_indirect_income = abs($indirect_income)+abs($gross_profit)-abs($net_loss);

                $total_indirect_expenses = abs($indirect_expenses)+abs($net_profit)+abs($gross_loss);
                $total_indirect_income = abs($indirect_income)+abs($gross_profit)+abs($net_loss);
                
                // delete
                /*$gp_p=$opening_stock+($purchase-$purchase_return)+$indirect_income;
                $gp_s=$closing_stock+($sales-$sales_return)+$indirect_expenses;

                if($gp_p > $gp_s){ $gprofit = SysHelper::com_curr_format($gp_p - $gp_s, 2, '.', ''); }
                if($gp_s > $gp_p){ $gloss = SysHelper::com_curr_format($gp_s - $gp_p, 2, '.', ''); }
                */
                


            return view('backEnd.profit-and-loss-account.view', compact('from_date','to_date','indirect_expenses','indirect_income','indirect_expenses_data','indirect_income_data','total_indirect_expenses','total_indirect_income','indirect_expenses_group','indirect_income_group','gross_profit','gross_loss','net_profit','net_loss','filter_by'));
        }catch (\Exception $e) {
            return $e;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public static function get_indirect_expenses($from_date,$to_date,$ie_acc_id)
    {
        $m = SysHelper::get_months_by_date($from_date,$to_date);
        $tot=0;
        if(count($ie_acc_id)>0){
            foreach ($ie_acc_id as $val) {
                $dt[]=$val->id;
            }
            $trn = SysChartofAccountsTransaction::select('plan','debit_amount','credit_amount')->wherein('account_id',$dt)->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') >= '".$from_date."' and DATE_FORMAT(transaction_date, '%Y-%m-%d') <= '".$to_date."'")->wherenotin('transaction_type',['opbinvoice','openingstock'])->where('status',1)->get();

            if(count($trn)>0){
                foreach($trn as $dt){
                    if($dt->plan==0){
                        $tot += $dt->debit_amount;
                        $tot -= $dt->credit_amount;
                    }
                    else{
                        $tot += SysHelper::get_amount_by_month($m,$dt->plan,$dt->debit_amount);
                    }
                }
                return SysHelper::com_curr_format($tot, 2, '.', '');
            } else{
                return SysHelper::com_curr_format($tot, 2, '.', '');
            }
        }
    }
    public static function get_indirect_income($from_date,$to_date,$ii_acc_id)
    {
        $m = SysHelper::get_months_by_date($from_date,$to_date);
        $tot=0;
        if(count($ii_acc_id)>0){
            foreach ($ii_acc_id as $val) {
                $dt[]=$val->id;
            }
            $trn = SysChartofAccountsTransaction::select('plan','debit_amount','credit_amount')->wherein('account_id',$dt)->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') >= '".$from_date."' and DATE_FORMAT(transaction_date, '%Y-%m-%d') <= '".$to_date."'")->wherenotin('transaction_type',['opbinvoice','openingstock'])->where('status',1)->get();

            if(count($trn)>0){
                foreach($trn as $dt){
                    if($dt->plan==0){
                        $tot += $dt->credit_amount;
                        $tot -= $dt->debit_amount;
                    }
                    else{
                        $tot += SysHelper::get_amount_by_month($m,$dt->plan,$dt->credit_amount);
                    }
                }
                return SysHelper::com_curr_format($tot, 2, '.', '');
            } else{
                return SysHelper::com_curr_format($tot, 2, '.', '');
            }
        }
    }

    public static function get_direct_expenses($from_date,$to_date,$ie_acc_id)
    {
        $m = SysHelper::get_months_by_date($from_date,$to_date);
        $tot=0;
        if(count($ie_acc_id)>0){
            foreach ($ie_acc_id as $val) {
                $dt[]=$val->id;
            }
            $trn = SysChartofAccountsTransaction::select('plan','debit_amount','credit_amount')->wherein('account_id',$dt)->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') >= '".$from_date."' and DATE_FORMAT(transaction_date, '%Y-%m-%d') <= '".$to_date."'")->wherenotin('transaction_type',['opbinvoice','openingstock'])->where('status',1)->get();

            if(count($trn)>0){
                foreach($trn as $dt){
                    if($dt->plan==0){
                        $tot += $dt->debit_amount;
                        $tot -= $dt->credit_amount;
                    }
                    else{
                        $tot += SysHelper::get_amount_by_month($m,$dt->plan,$dt->debit_amount);
                    }
                }
                return SysHelper::com_curr_format($tot, 2, '.', '');
            } else{
                return SysHelper::com_curr_format($tot, 2, '.', '');
            }
        }
    }
    public static function get_direct_income($from_date,$to_date,$ii_acc_id)
    {
        $m = SysHelper::get_months_by_date($from_date,$to_date);
        $tot=0;
        if(count($ii_acc_id)>0){
            foreach ($ii_acc_id as $val) {
                $dt[]=$val->id;
            }
            $trn = SysChartofAccountsTransaction::select('plan','debit_amount','credit_amount')->wherein('account_id',$dt)->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') >= '".$from_date."' and DATE_FORMAT(transaction_date, '%Y-%m-%d') <= '".$to_date."'")->wherenotin('transaction_type',['opbinvoice','openingstock'])->where('status',1)->get();

            if(count($trn)>0){
                foreach($trn as $dt){
                    if($dt->plan==0){
                        $tot += $dt->credit_amount;
                        $tot -= $dt->debit_amount;
                    }
                    else{
                        $tot += SysHelper::get_amount_by_month($m,$dt->plan,$dt->credit_amount);
                    }
                }
                return SysHelper::com_curr_format($tot, 2, '.', '');
            } else{
                return SysHelper::com_curr_format($tot, 2, '.', '');
            }
        }
    }

    //commented direct opb account 6/7/2024
    public static function get_opening_stock($from_date,$to_date,$company_id)
    {
        try {
            $opening_stock_account_id = SysHelper::get_opening_stock_account_id();
            // $trn = SysChartofAccountsTransaction::select(DB::raw('SUM(debit_amount) - SUM(credit_amount) as amount'))
            // ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') >= '".$from_date."'")
            // ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') <= '".$to_date."'")
            // ->where('account_id',$opening_stock_account_id)->where('status', 1)->wherenotin('transaction_type',['opbinvoice'])
            // ->wherein('company_id',$company_id)->first();

            $stocklist = DB::table('sys_item_stock as stock')
                ->select(DB::raw('max(stock.partno) as partno'),DB::raw('SUM(stock.qty_in) - SUM(stock.qty_out) as balance_qty')
                ,DB::raw('SUM(stock.qty_in * stock.price_in) / SUM(stock.qty_in) as avg_price'))
                ->join('sm_items as item', 'item.id','stock.partno')
                ->join('sys_brand as brand','brand.id','item.brand')
                ->whereRaw("DATE_FORMAT(stock.doc_date, '%Y-%m-%d') < '" . $from_date . "'")
                ->wherein('stock.company_id',$company_id)
                ->where('stock.doc_number', 'not like', 'SRN%')->where('stock.status', 1)
                ->groupby('item.part_number','item.description','brand.title')
                ->get();

            $stocklist_return = DB::table('sys_item_stock')->select(DB::raw('max(partno) as partno'),DB::raw('SUM(qty_in) as qty'))
                ->whereRaw("DATE_FORMAT(doc_date, '%Y-%m-%d') < '" . $from_date . "'")->wherein('company_id',$company_id)->where('doc_number', 'like', 'SRN%')->where('status', 1)
                ->groupby('partno')->get();

            $total_amount = 0;
            if(count($stocklist)>0){
                foreach($stocklist as $value){                        
                    $balance_qty = $value->balance_qty;
                    $balance_qty += $stocklist_return->where('partno',$value->partno)->sum('qty');;
                    $total_amount += ($value->avg_price * $balance_qty);
                }
            }
            //return $trn->amount+$total_amount;
            return $total_amount;

                //return $total_amount;
            // if($trn->amount==""){
            //     return '0.00';
            // }
        } catch (\Throwable $th) {
            return $th;
        }
    }
    
    public static function get_opening_stock_trading($from_date,$to_date,$company_id)
    {
        try {
            /*$opening_stock_account_id = SysHelper::get_opening_stock_account_id();
            $trn = SysChartofAccountsTransaction::select(DB::raw('SUM(debit_amount) - SUM(credit_amount) as amount'))
            ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') >= '".$from_date."'")
            ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') <= '".$to_date."'")
            ->where('account_id',$opening_stock_account_id)
            ->wherein('company_id',$company_id)->first();*/

            $stocklist = DB::table('sys_item_stock as stock')
                ->select(DB::raw('max(stock.partno) as partno'),DB::raw('SUM(stock.qty_in) - SUM(stock.qty_out) as balance_qty')
                ,DB::raw('SUM(stock.qty_in * stock.price_in) / SUM(stock.qty_in) as avg_price'))
                ->join('sm_items as item', 'item.id','stock.partno')
                ->join('sys_brand as brand','brand.id','item.brand')
                ->whereRaw("DATE_FORMAT(stock.doc_date, '%Y-%m-%d') < '" . $from_date . "'")
                ->wherein('stock.company_id',$company_id)->where('stock.status', 1)
                ->where('stock.doc_number', 'not like', 'SRN%')
                ->groupby('item.part_number','item.description','brand.title')
                ->get();

            $stocklist_return = DB::table('sys_item_stock')->select(DB::raw('max(partno) as partno'),DB::raw('SUM(qty_in) as qty'))
                ->whereRaw("DATE_FORMAT(doc_date, '%Y-%m-%d') < '" . $from_date . "'")->wherein('company_id',$company_id)->where('doc_number', 'like', 'SRN%')->where('status', 1)
                ->groupby('partno')->get();

            $total_amount = 0;
            if(count($stocklist)>0){
                foreach($stocklist as $value){                        
                    $balance_qty = $value->balance_qty;
                    $balance_qty += $stocklist_return->where('partno',$value->partno)->sum('qty');
                    $total_amount += ($value->avg_price * $balance_qty);
                }
            }
            return $total_amount;
            
        } catch (\Throwable $th) {
            return $th;
        }
    }
    public static function get_opening_stock_trading_new($from_date,$to_date,$company_id)
    {
        $from_date = Carbon::parse($from_date)->addDays(-1)->toDateString();
        try {
            // $stocklist = DB::table('sys_item_stock as stock')
            //     ->select(DB::raw('max(item.part_number) as part_number'),DB::raw('max(stock.partno) as partno'),DB::raw('max(item.description) as description')
            //     ,DB::raw('max(brand.title) as brand'),DB::raw('max(brand.id) as brandid'),DB::raw('SUM(stock.qty_in) - SUM(stock.qty_out) as balance_qty')
            //     ,DB::raw('SUM(stock.qty_in * stock.price_in) / SUM(stock.qty_in) as avg_price')
            //     ,DB::raw('max(cat.category_name) as categoryname'),DB::raw('max(subcat.sub_category_name) as subcategoryname'))
            //     ->selectRaw('2 as type')
            //     ->join('sm_items as item', 'item.id','stock.partno')
            //     ->join('sys_brand as brand','brand.id','item.brand')
            //     ->leftjoin('sm_item_categories as cat','cat.id','item.category_name')
            //     ->leftjoin('sm_item_subcategories as subcat','subcat.id','item.subcategory_name')
            //     ->whereRaw("DATE_FORMAT(stock.doc_date, '%Y-%m-%d') <= '" . $from_date . "'")
            //     ->wherein('stock.company_id',$company_id)->where('stock.status',1)->where('item.status',1)
            //     //->where('stock.doc_number', 'not like', 'SR%')
            //     ->wherein('item.product_type',[1,2])
            //     ->groupby('item.part_number','item.description','brand.title')
            //     ->get(); 
            
$stocklist = DB::table('sys_item_stock as stock')
->select(
    'item.id as partno',
    'item.description',
    'brand.title as brand',
    DB::raw('SUM(stock.qty_in) - SUM(stock.qty_out) as balance_qty'),
    DB::raw('SUM(stock.qty_in * stock.price_in) / SUM(stock.qty_in) as avg_price')
)
->join('sm_items as item','item.id','=','stock.partno')
->join('sys_brand as brand','brand.id','=','item.brand')
->where('stock.doc_date','<=',$from_date)
->whereIn('stock.company_id',$company_id)
->where('stock.status',1)
->groupBy('item.id','item.description','brand.title')
->get();

                $stocklist_return = DB::table('sys_item_stock')->select(DB::raw('max(partno) as partno'),DB::raw('SUM(qty_in) as qty'))
                ->whereRaw("DATE_FORMAT(doc_date, '%Y-%m-%d') <= '" . $from_date . "'")->wherein('company_id',$company_id)->where('doc_number', 'like', 'SR%')->where('status',1)
                ->groupby('partno')->get();

    // Apply filter based on r_qty

    $total_amount = 0;

    foreach ($stocklist as $value) {
        $group_qty = SysHelper::get_group_qty($value->partno);

            $balance_qty = $value->balance_qty;
            // + $stocklist_return->where('partno', $value->partno)->sum('qty');

            $avg = 0;
            $amount = 0;

                $avg = SysHelper::get_avg_price($value->partno, $from_date);
                $amount = $avg * max($balance_qty, 0);
                    $total_amount += $amount;
    }


                return $total_amount;

/*

            $stocklist = DB::table('sys_item_stock as stock')
                ->select(DB::raw('max(stock.partno) as partno'),DB::raw('SUM(stock.qty_in) - SUM(stock.qty_out) as balance_qty')
                ,DB::raw('SUM(stock.qty_in * stock.price_in) / SUM(stock.qty_in) as avg_price'))
                ->join('sm_items as item', 'item.id','stock.partno')
                ->join('sys_brand as brand','brand.id','item.brand')
                ->whereRaw("DATE_FORMAT(stock.doc_date, '%Y-%m-%d') < '" . $from_date . "'")
                ->wherein('stock.company_id',$company_id)->where('stock.status', 1)
                ->where('stock.doc_number', 'not like', 'SR%')
                ->groupby('item.part_number','item.description','brand.title')
                ->get();

            $stocklist_return = DB::table('sys_item_stock')->select(DB::raw('max(partno) as partno'),DB::raw('SUM(qty_in) as qty'))
                ->whereRaw("DATE_FORMAT(doc_date, '%Y-%m-%d') < '" . $from_date . "'")->wherein('company_id',$company_id)->where('doc_number', 'like', 'SR%')->where('status', 1)
                ->groupby('partno')->get();

            $total_amount = 0;
            if(count($stocklist)>0){
                foreach($stocklist as $value){                        
                    $balance_qty = $value->balance_qty;
                    $balance_qty += $stocklist_return->where('partno',$value->partno)->sum('qty');
                    $total_amount += ($value->avg_price * $balance_qty);
                }
            }
            return $total_amount;*/
            
        } catch (\Throwable $th) {
            return $th;
        }
    }
    
    public static function get_opening_stock_from_account($from_date,$to_date,$company_id)
    {
        try {
            $opening_stock_account_id = SysHelper::get_opening_stock_account_id();
            $trn = SysChartofAccountsTransaction::select(DB::raw('SUM(credit_amount)-SUM(debit_amount) as amount'))
            ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') >= '".$from_date."'")
            ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') <= '".$to_date."'")
            ->where('account_id',$opening_stock_account_id)->where('status', 1)->wherenotin('transaction_type',['opbinvoice','openingstock'])
            ->wherein('company_id',$company_id)->first();
            
            if($trn->amount==""){
                return '0.00';
            }

            return $trn->amount;
            
        } catch (\Throwable $th) {
            return $th;
        }
    }

    // public static function get_opening_stock_tilldate($till_date,$company_id)
    // {
    //     try {
    //         $opening_stock_amount = '0.00';
    //         $opening_stock_account_id = SysHelper::get_opening_stock_account_id();
    //         $opening_stock_trn = SysChartofAccountsTransaction::select(DB::raw('SUM(debit_amount) - SUM(credit_amount) as amount'))
    //         ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') < '".$till_date."'")
    //         ->where('account_id',$opening_stock_account_id)
    //         ->wherein('company_id',$company_id)->first();
    //         if($opening_stock_trn->amount != ""){
    //             $opening_stock_amount =  $opening_stock_trn->amount;
    //         }
    //         return $opening_stock_amount;
    //     } catch (\Throwable $th) {
    //         return $th;
    //     }
    // }
    
    public static function get_closing_stock_update($from_date,$to_date,$company_id)
    {
        try {
            // $stocklist = DB::table('sys_item_stock as stock')
            // ->select(DB::raw('max(stock.partno) as partno'),DB::raw('SUM(stock.qty_in) - SUM(stock.qty_out) as balance_qty')
            // ,DB::raw('SUM(stock.qty_in * stock.price_in) / SUM(stock.qty_in) as avg_price'))
            // ->join('sm_items as item', 'item.id','stock.partno')
            // ->join('sys_brand as brand','brand.id','item.brand')
            // ->whereRaw("DATE_FORMAT(stock.doc_date, '%Y-%m-%d') <= '" . $to_date . "'")
            // ->wherein('stock.company_id',$company_id)->where('stock.status', 1)
            // ->where('stock.doc_number', 'not like', 'SR%')
            // ->groupby('item.part_number','item.description','brand.title')
            // ->get();

$stocklist = DB::table('sys_item_stock as stock')
->select(
    'item.id as partno',
    'item.description',
    'brand.title as brand',
    DB::raw('SUM(stock.qty_in) - SUM(stock.qty_out) as balance_qty'),
    DB::raw('SUM(stock.qty_in * stock.price_in) / SUM(stock.qty_in) as avg_price')
)
->join('sm_items as item','item.id','=','stock.partno')
->join('sys_brand as brand','brand.id','=','item.brand')
->where('stock.doc_date','<=',$to_date)
->whereIn('stock.company_id',$company_id)
->where('stock.status',1)
->groupBy('item.id','item.description','brand.title')
->get();

        $stocklist_return = DB::table('sys_item_stock')->select(DB::raw('max(partno) as partno'),DB::raw('SUM(qty_in) as qty'))
            ->whereRaw("DATE_FORMAT(doc_date, '%Y-%m-%d') <= '" . $to_date . "'")->wherein('company_id',$company_id)->where('doc_number', 'like', 'SR%')->where('status',1)
            ->groupby('partno')->get();

        $total_amount = 0;

        
        $total_qty=0; $total_price=0; $total_value=0; $total_amount=0;


        if(count($stocklist)>0){
            foreach($stocklist as $value){            
                
                $balance_qty = $value->balance_qty;
                //$balance_qty += $stocklist_return->where('partno',$value->partno)->sum('qty');                

                $avg = SysHelper::get_avg_price($value->partno,$to_date);
                $total_qty += $balance_qty;
                $total_price += $avg;
                if($balance_qty > 0){
                    $total_amount += ($avg * $balance_qty);
                }

            }
        }
        return $total_amount;

        } catch (\Throwable $th) {
            return $th;
        }
    }

    // public static function get_closing_stock($opening_stock,$purchase,$purchase_return,$sales,$sales_return,$stock)
    // {
    //     try {

    //         $closing_stock = abs($opening_stock) + abs($stock) + abs(abs($purchase) - abs($purchase_return)) - abs(abs($sales) - abs($sales_return));
    //         return $closing_stock;

    //     } catch (\Throwable $th) {
    //         return $th;
    //     }
    // }
    // public static function get_closing_stock_tilldate($till_date,$company_id)
    // {
    //     try {
    //         $opening_stock = SysProfitAndLossAccountController::get_opening_stock_tilldate($till_date,$company_id);
    //         $purchase = SysProfitAndLossAccountController::get_purchase_tilldate($till_date,$company_id);
    //         $purchase_return = SysProfitAndLossAccountController::get_purchase_return_tilldate($till_date,$company_id);
    //         $sales = SysProfitAndLossAccountController::get_sales_tilldate($till_date,$company_id);
    //         $sales_return = SysProfitAndLossAccountController::get_sales_return_tilldate($till_date,$company_id);
    //         $stock = SysProfitAndLossAccountController::get_item_stock_tilldate($till_date,$company_id);

    //         $closing_stock = abs($opening_stock) + abs($stock) + abs(abs($purchase) - abs($purchase_return)) - abs(abs($sales) - abs($sales_return));
    //         return $closing_stock;

    //     } catch (\Throwable $th) {
    //         return $th;
    //     }
    // }
    
    public static function get_item_stock($from_date,$to_date,$company_id)
    {
        try {
            $stocklist = DB::table('sys_item_stock as stock')
                ->select(DB::raw('max(stock.partno) as partno'),DB::raw('SUM(stock.qty_in) - SUM(stock.qty_out) as balance_qty')
                ,DB::raw('SUM(stock.qty_in * stock.price_in) / SUM(stock.qty_in) as avg_price'))
                ->join('sm_items as item', 'item.id','stock.partno')
                ->join('sys_brand as brand','brand.id','item.brand')
                ->whereRaw("DATE_FORMAT(stock.doc_date, '%Y-%m-%d') >= '" . $from_date . "'")
                ->whereRaw("DATE_FORMAT(stock.doc_date, '%Y-%m-%d') <= '" . $to_date . "'")
                ->wherein('stock.company_id',$company_id)->where('stock.status', 1)
                ->where('stock.doc_number', 'not like', 'SRN%')
                ->groupby('item.part_number','item.description','brand.title')
                ->get();

            $stocklist_return = DB::table('sys_item_stock')->select(DB::raw('max(partno) as partno'),DB::raw('SUM(qty_in) as qty'))
                ->whereRaw("DATE_FORMAT(doc_date, '%Y-%m-%d') >= '" . $from_date . "'")->whereRaw("DATE_FORMAT(doc_date, '%Y-%m-%d') <= '" . $to_date . "'")
                ->wherein('company_id',$company_id)->where('doc_number', 'like', 'SRN%')
                ->groupby('partno')->get();

            $total_amount = 0;
            if(count($stocklist)>0){
                foreach($stocklist as $value){                        
                    $balance_qty = $value->balance_qty;
                    $balance_qty += $stocklist_return->where('partno',$value->partno)->sum('qty');;
                    $total_amount += ($value->avg_price * $balance_qty);
                }
            }
            return $total_amount;
            /*$trn = DB::table('sys_item_stock as stock')
            ->select(DB::raw('SUM(stock.qty_in * stock.price_in) / SUM(stock.qty_in) * SUM(stock.qty_in) - SUM(stock.qty_out) as avg_price'))
            ->join('sm_items as item', 'item.id','stock.partno')
            ->join('sys_brand as brand','brand.id','item.brand')
            ->whereRaw("DATE_FORMAT(stock.doc_date, '%Y-%m-%d') >= '".$from_date."'")
            ->whereRaw("DATE_FORMAT(stock.doc_date, '%Y-%m-%d') <= '".$to_date."'")
            ->wherein('stock.company_id',$company_id)->first();
            if($trn->avg_price==""){
                return '0.00';
            }
            return $trn->avg_price;*/
        } catch (\Throwable $th) {
            return $th;
        }
    }
    public static function get_item_stock_tilldate($till_date,$company_id)
    {
        try {
            $trn = DB::table('sys_item_stock as stock')
            ->select(DB::raw('SUM(stock.qty_in * stock.price_in) / SUM(stock.qty_in) * SUM(stock.qty_in) - SUM(stock.qty_out) as avg_price'))
            ->join('sm_items as item', 'item.id','stock.partno')
            ->join('sys_brand as brand','brand.id','item.brand')
            ->whereRaw("DATE_FORMAT(stock.doc_date, '%Y-%m-%d') < '".$till_date."'")
            ->wherein('stock.company_id',$company_id)->where('stock.status', 1)->first();
            if($trn->avg_price==""){
                return '0.00';
            }
            return $trn->avg_price;
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public static function get_purchase($from_date,$to_date,$company_id)
    {
        try {
            $purchase_account_id = SysHelper::get_purchase_account_id();
            $trn = SysChartofAccountsTransaction::select(DB::raw('SUM(debit_amount) - SUM(credit_amount) as amount'))
            ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') >= '".$from_date."'")
            ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') <= '".$to_date."'")
            ->where('account_id',$purchase_account_id)->where('status', 1)->wherenotin('transaction_type',['opbinvoice','openingstock'])
            ->wherein('company_id',$company_id)->first();
            if($trn->amount==""){
                return '0.00';
            }
            return $trn->amount;
        } catch (\Throwable $th) {
            return $th;
        }
    }
    public static function get_purchase_tilldate($till_date,$company_id)
    {
        try {
            $purchase_account_amount = '0.00';
            $purchase_account_id = SysHelper::get_purchase_account_id();
            $purchase_account_trn = SysChartofAccountsTransaction::select(DB::raw('SUM(debit_amount) - SUM(credit_amount) as amount'))
            ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') < '".$till_date."'")
            ->where('account_id',$purchase_account_id)->where('status', 1)->wherenotin('transaction_type',['opbinvoice','openingstock'])
            ->wherein('company_id',$company_id)->first();
            if($purchase_account_trn->amount != ""){
                $purchase_account_amount =  $purchase_account_trn->amount;
            }
            return $purchase_account_amount;
        } catch (\Throwable $th) {
            return $th;
        }
    }
    public static function get_purchase_return($from_date,$to_date,$company_id)
    {
        try {
            $purchase_return_account_id = SysHelper::get_purchase_return_account_id();
            $trn = SysChartofAccountsTransaction::select(DB::raw('SUM(credit_amount)-SUM(debit_amount) as amount'))
            ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') >= '".$from_date."'")
            ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') <= '".$to_date."'")
            ->where('account_id',$purchase_return_account_id)->where('status', 1)->wherenotin('transaction_type',['opbinvoice','openingstock'])
            ->wherein('company_id',$company_id)->first();
            if($trn->amount==""){
                return '0.00';
            }
            return $trn->amount;
        } catch (\Throwable $th) {
            return $th;
        }
    }
    public static function get_purchase_return_tilldate($till_date,$company_id)
    {
        try {
            $purchase_return_amount = '0.00';
            $purchase_return_account_id = SysHelper::get_purchase_return_account_id();
            $purchase_return_account_trn = SysChartofAccountsTransaction::select(DB::raw('SUM(credit_amount)-SUM(debit_amount) as amount'))
            ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') < '".$till_date."'")
            ->where('account_id',$purchase_return_account_id)->where('status', 1)->wherenotin('transaction_type',['opbinvoice','openingstock'])
            ->wherein('company_id',$company_id)->first();
            if($purchase_return_account_trn->amount != ""){
                $purchase_return_amount =  $purchase_return_account_trn->amount;
            }
            return $purchase_return_amount;
        } catch (\Throwable $th) {
            return $th;
        }
    }
    public static function get_sales($from_date,$to_date,$company_id)
    {
        try {
            $sales_account_id = SysHelper::get_sales_account_id();
            $trn = SysChartofAccountsTransaction::select(DB::raw('SUM(credit_amount) - SUM(debit_amount) as amount'))
            ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') >= '".$from_date."'")
            ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') <= '".$to_date."'")
            ->where('account_id',$sales_account_id)->where('status', 1)->wherenotin('transaction_type',['opbinvoice','openingstock'])
            ->wherein('company_id',$company_id)->first();
            if($trn->amount==""){
                return '0.00';
            }
            return $trn->amount;
        } catch (\Throwable $th) {
            return $th;
        }
    }
    public static function get_sales_tilldate($till_date,$company_id)
    {
        try {
            $sales_account_amount = '0.00';
            $sales_account_id = SysHelper::get_sales_account_id();
            $sales_account_trn = SysChartofAccountsTransaction::select(DB::raw('SUM(debit_amount)-SUM(credit_amount) as amount'))
            ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') < '".$till_date."'")
            ->where('account_id',$sales_account_id)->where('status', 1)->wherenotin('transaction_type',['opbinvoice','openingstock'])
            ->wherein('company_id',$company_id)->first();
            if($sales_account_trn->amount != ""){
                $sales_account_amount =  $sales_account_trn->amount;
            }
            return $sales_account_amount;       
        } catch (\Throwable $th) {
            return $th;
        }
    }
    public static function get_sales_return($from_date,$to_date,$company_id)
    {
        try {
            $sales_return_account_id = SysHelper::get_sales_return_account_id();
            $trn = SysChartofAccountsTransaction::select(DB::raw('SUM(debit_amount)-SUM(credit_amount) as amount'))
            ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') >= '".$from_date."'")
            ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') <= '".$to_date."'")
            ->where('account_id',$sales_return_account_id)->where('status', 1)->wherenotin('transaction_type',['opbinvoice','openingstock'])
            ->wherein('company_id',$company_id)->first();
            if($trn->amount==""){
                return '0.00';
            }
            return $trn->amount;
        } catch (\Throwable $th) {
            return $th;
        }
    }
    public static function get_sales_return_tilldate($till_date,$company_id)
    {
        try {
            $sales_return_amount = '0.00';
            $sales_return_account_id = SysHelper::get_sales_return_account_id();
            $sales_account_trn = SysChartofAccountsTransaction::select(DB::raw('SUM(credit_amount) - SUM(debit_amount) as amount'))
            ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') < '".$till_date."'")
            ->where('account_id',$sales_return_account_id)->where('status', 1)->wherenotin('transaction_type',['opbinvoice','openingstock'])
            ->wherein('company_id',$company_id)->first();
            if($sales_account_trn->amount != ""){
                $sales_return_amount =  $sales_account_trn->amount;
            }
            return $sales_return_amount;
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public static function get_general_reserve($from_date,$to_date,$com_ids)
    {
        try {
            $general_reserve_account_id = SysHelper::get_general_reserve_account_id();

            $accounts = DB::table('sys_chartofaccounts as ca')
            ->select('ca.subgroup2','ca.account_name',db::raw('
            (CASE WHEN ca.group = 1 THEN SUM(cat.debit_amount) - SUM(cat.credit_amount)
            WHEN ca.group = 2 THEN SUM(cat.credit_amount) - SUM(cat.debit_amount)
            WHEN ca.group = 5 THEN SUM(cat.credit_amount) - SUM(cat.debit_amount)
            ELSE SUM(cat.debit_amount) - SUM(cat.credit_amount) END) as amount'))
            ->join('sys_chartofaccounts_transaction as cat', 'cat.account_id','ca.id')
            ->wherein('ca.group',[1,2,5])
            ->wherein('ca.company_id',$com_ids)
            ->wherein('cat.account_id',[$general_reserve_account_id])
            ->whereRaw("DATE_FORMAT(cat.transaction_date, '%Y-%m-%d') < '" . $from_date . "'")
            ->where('cat.status',1)->wherenotin('cat.transaction_type',['opbinvoice','openingstock'])
            ->groupby('ca.subgroup2','ca.account_name','ca.group')
            ->get();

            if(count($accounts)>0) {
                return $accounts[0]->amount;
            } else {
                return '0.00';
            }

        } catch (\Throwable $th) {
            return $th;
        }
    }

    public static function get_net_profit_loss($from_date,$to_date)
    {
        $r = SysHelper::get_data_by_role();
        $company_id = $r[0];
        $gross_profit=0;
        $gross_loss=0;

        $net_profit=0;
        $net_loss=0;

        $indirect_expenses=0;
        $indirect_income=0;

        $ie_acc_id = SysChartofAccounts::select('id','account_name')->where('subgroup',14)->wherein('company_id',$company_id)->where('status',1)->get();
        $indirect_expenses = SysProfitAndLossAccountController::get_indirect_expenses($from_date,$to_date,$ie_acc_id);
        
        $ii_acc_id = SysChartofAccounts::select('id','account_name')->where('subgroup',16)->wherein('company_id',$company_id)->where('status',1)->get();
        $indirect_income = SysProfitAndLossAccountController::get_indirect_income($from_date,$to_date,$ii_acc_id);
        $ie_acc_id = SysChartofAccounts::select('id','account_name')->where('subgroup',13)->wherein('company_id',$company_id)->where('status',1)->get();
        $direct_expenses = SysProfitAndLossAccountController::get_direct_expenses($from_date,$to_date,$ie_acc_id);
        $opening_stock = SysProfitAndLossAccountController::get_opening_stock_trading_new($from_date,$to_date,$company_id);
        $opening_stock_account = SysProfitAndLossAccountController::get_opening_stock_from_account($from_date,$to_date,$company_id);
        $purchase = SysProfitAndLossAccountController::get_purchase($from_date,$to_date,$company_id);
        $purchase_return = SysProfitAndLossAccountController::get_purchase_return($from_date,$to_date,$company_id);
        
        $ie_acc_id = SysChartofAccounts::select('id','account_name')->where('subgroup',15)->wherein('company_id',$company_id)->where('status',1)->get();
        $direct_incomes = SysProfitAndLossAccountController::get_direct_income($from_date,$to_date,$ie_acc_id);                
        
        $sales = SysProfitAndLossAccountController::get_sales($from_date,$to_date,$company_id);
        $sales_return = SysProfitAndLossAccountController::get_sales_return($from_date,$to_date,$company_id);
        $stock = SysProfitAndLossAccountController::get_item_stock($from_date,$to_date,$company_id);
        $closing_stock = SysProfitAndLossAccountController::get_closing_stock_update($from_date,$to_date,$company_id);
        
        //ext
        //$closing_stock_till = SysProfitAndLossAccountController::get_closing_stock_tilldate($from_date,$com_ids);
        //$opening_stock += $closing_stock_till;
        //ext
        
        //$total_direct_expences = ($direct_expenses + $opening_stock + abs($purchase - $purchase_return));  //subgroup 13 already taken purchase & purchase return account
        $total_direct_expences = ($direct_expenses + $opening_stock);
        //$total_direct_incomes = ($direct_incomes + $closing_stock + abs($sales - $sales_return));  // subgroup 15 already taken sales and sales return 
        $total_direct_incomes = ($direct_incomes + $closing_stock);
        
        //Direct Income - Direct Expence = Gross Profit
        if($total_direct_expences > $total_direct_incomes) {
            $gross_loss = abs($total_direct_expences) - abs($total_direct_incomes) + abs($opening_stock_account);
        }
        else {
            $gross_profit = abs($total_direct_incomes) - abs($total_direct_expences) + abs($opening_stock_account);
        }
        
        //(Indirect income + Gross Profit) - Indirect Expence = Net Profit
        if($gross_profit > $gross_loss) {
            if($indirect_expenses > ($indirect_income + $gross_profit)){
                $net_loss = (abs($indirect_expenses) - abs($indirect_income)) - abs($gross_profit) ;
            } else {
                $net_profit = (abs($indirect_income) - abs($indirect_expenses)) + abs($gross_profit);
            }
        } else {
            if($indirect_expenses > ($indirect_income - $gross_loss)){
                $net_loss =  (abs($indirect_expenses) - abs($indirect_income)) + abs($gross_loss);
            } else {
                $net_profit = (abs($indirect_income) - abs($indirect_expenses)) - abs($gross_loss);
            }
        }
        return ['net-profit' => $net_profit, 'net-loss' => $net_loss];        
    }

    public static function get_net_profit_loss_till_date($till_date)
    {
        $from_date = "1990-01-01";
        $to_date = date('Y-m-d', strtotime($till_date . " -1 days"));
        
        $r = SysHelper::get_data_by_role();
        $company_id = $r[0];

        $gross_profit=0;
        $gross_loss=0;

        $net_profit=0;
        $net_loss=0;

        $indirect_expenses=0;
        $indirect_income=0;

        $ie_acc_id = SysChartofAccounts::select('id','account_name')->where('subgroup',14)->wherein('company_id',$company_id)->where('status',1)->get();
        $indirect_expenses = SysProfitAndLossAccountController::get_indirect_expenses($from_date,$to_date,$ie_acc_id);
        
        $ii_acc_id = SysChartofAccounts::select('id','account_name')->where('subgroup',16)->wherein('company_id',$company_id)->where('status',1)->get();
        $indirect_income = SysProfitAndLossAccountController::get_indirect_income($from_date,$to_date,$ii_acc_id);
        $ie_acc_id = SysChartofAccounts::select('id','account_name')->where('subgroup',13)->wherein('company_id',$company_id)->where('status',1)->get();
        $direct_expenses = SysProfitAndLossAccountController::get_direct_expenses($from_date,$to_date,$ie_acc_id);
        $opening_stock = SysProfitAndLossAccountController::get_opening_stock_trading($from_date,$to_date,$company_id);
        $purchase = SysProfitAndLossAccountController::get_purchase($from_date,$to_date,$company_id);
        $purchase_return = SysProfitAndLossAccountController::get_purchase_return($from_date,$to_date,$company_id);
        
        $ie_acc_id = SysChartofAccounts::select('id','account_name')->where('subgroup',15)->wherein('company_id',$company_id)->where('status',1)->get();
        $direct_incomes = SysProfitAndLossAccountController::get_direct_income($from_date,$to_date,$ie_acc_id);                
        
        $sales = SysProfitAndLossAccountController::get_sales($from_date,$to_date,$company_id);
        $sales_return = SysProfitAndLossAccountController::get_sales_return($from_date,$to_date,$company_id);
        $stock = SysProfitAndLossAccountController::get_item_stock($from_date,$to_date,$company_id);
        $closing_stock = SysProfitAndLossAccountController::get_closing_stock_update($from_date,$to_date,$company_id);
        
        //ext
        //$closing_stock_till = SysProfitAndLossAccountController::get_closing_stock_tilldate($from_date,$com_ids);
        //$opening_stock += $closing_stock_till;
        //ext
        
        //$total_direct_expences = ($direct_expenses + $opening_stock + abs($purchase - $purchase_return));  //subgroup 13 already taken purchase & purchase return account
        $total_direct_expences = ($direct_expenses + $opening_stock);
        //$total_direct_incomes = ($direct_incomes + $closing_stock + abs($sales - $sales_return));  // subgroup 15 already taken sales and sales return 
        $total_direct_incomes = ($direct_incomes + $closing_stock);
        
        //Direct Income - Direct Expence = Gross Profit
        if($total_direct_expences > $total_direct_incomes) {
            $gross_loss = abs($total_direct_expences) - abs($total_direct_incomes);
        }
        else {
            $gross_profit = abs($total_direct_incomes) - abs($total_direct_expences);
        }
        
        //(Indirect income + Gross Profit) - Indirect Expence = Net Profit
        if($gross_profit > $gross_loss) {
            if($indirect_expenses > ($indirect_income + $gross_profit)){
                $net_loss = (abs($indirect_expenses) - abs($indirect_income)) - abs($gross_profit) ;
            } else {
                $net_profit = (abs($indirect_income) - abs($indirect_expenses)) + abs($gross_profit);
            }
        } else {
            if($indirect_expenses > ($indirect_income - $gross_loss)){
                $net_loss =  (abs($indirect_expenses) - abs($indirect_income)) + abs($gross_loss);
            } else {
                $net_profit = (abs($indirect_income) - abs($indirect_expenses)) - abs($gross_loss);
            }
        }
        return ['net-profit' => $net_profit, 'net-loss' => $net_loss];        
    }
}




// IF Indirect Expenses > (Indirect income + Gross Profit)
// Net loss = Indirect Expenses - (Gross profit + Indirect Income)

// Else
// IF (Indirect Expenses + Gross Loss)  > Indirect income
// Net loss = (Indirect Expenses + Gross Loss)  - Indirect income

// Else
// IF Indirect Income > (Indirect Expenses + Gross Loss)
// Net Profit =  Indirect Income - (Indirect Expenses+ Gross Loss) 

// Else
// IF (Indirect Income + Gross profit) > Indirect Expenses 
// Net Profit =  (Indirect Income + Gross profit) - Indirect Expenses
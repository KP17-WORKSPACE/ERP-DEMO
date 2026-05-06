<?php

namespace App\Http\Controllers;

use App\ApiBaseMethod;
use App\SmInspectingDepartment;
use App\SmItem;
use Illuminate\Http\Request;
use App\SmItemStore;
use App\SmStaff;
use App\SysAccountGroupSub2;
use App\SysBrand;
use App\SysChartofAccounts;
use App\SysChartofAccountsTransaction;
use App\SysCompany;
use App\SysCountries;
use App\SysCrmDeals;
use App\SysCrmDealTrack;
use App\SysCrmDealTrackApprovalPurchease;
use App\SysCrmDealTrackApprovalReceivables;
use App\SysCrmLeads;
use App\SysCrmLeadsComments;
use App\SysCrmQuoteCSItems;
use App\SysCrmQuoteItems;
use App\SysCrmSalesTarget;
use App\SysCrmService;
use App\SysCrmServiceAssign;
use App\SysCrmSupport;
use App\SysCurrencySettings;
use App\SysCustSuppl;
use App\SysHelper;
use App\SysItemOpeningStock;
use App\SysItemStock;
use App\SysPaymentTerms;
use App\SysReceipt;
use App\SysReceiptAdjustments;
use App\SysShipping;
use App\SysStockIn;
use App\SysStockInSerialNo;
use App\SysSupplierType;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Return_;
use Validator;

class SysCrmReportController extends Controller
{
    public function __construct(){
        $this->middleware('PM');
    }
    public static function get_total_sales_revenue($users, $date, $company){
        try {        
            $data1 = DB::table('sys_crm_deals')->select('sys_crm_deals.id as dealid','deal_value','deal_profit','deal_currency','source','cust_id','deal_percent','owner')
            ->leftjoin('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_id','sys_crm_deals.id')
            ->leftjoin('sys_crm_deal_track_approval_receivables','sys_crm_deal_track_approval_receivables.deal_id','sys_crm_deals.id')
            ->where('sys_crm_deals.stage',4)//->where('sys_crm_deals.is_partial_invoice',0)
            //->where('sys_crm_deal_track_approval_invoice.status',1);
            ->where(function ($query) {
                $query->orwhere('sys_crm_deal_track_approval_invoice.status',1)
                ->orwhere('sys_crm_deal_track_approval_receivables.status',1);
            });
            //->where('sys_crm_deals.company_id',$company);
            
            if($date=="d"){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') = '".date('Y-m-d')."'");
            }
            if($date=="m"){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m') = '".date('Y-m')."'");
            }
            if($date=="y"){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y') = '".date('Y')."'");
            }
            if($date=="q"){
                $quarter = SysHelper::get_quarter(date('m'));
                $start_date = $quarter[0];
                $end_date = $quarter[1];            
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '".$end_date."'");
            }
            if($date=="pm"){
                $c_date = Carbon::createFromFormat('Y-m', date('Y-m'))->subMonth();  
                $pm_date = $c_date->format('Y-m');
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m') = '".$pm_date."'");
            }
            if($date=="pq"){
                $quarter = SysHelper::get_pre_quarter(date('m'));
                $start_date = $quarter[0];
                $end_date = $quarter[1];
                
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '".$end_date."'");
            }

            if(Auth::user()->role_id == 1){ //super admin
                
            }
            elseif(Auth::user()->role_id == 2){ //admin                
                if(in_array(36,$users)) { //jacob
                    $users=[36,25,51,68,64,31,65,27];
                    $data1->wherein('sys_crm_deals.owner',$users); 
                } else {
                    $data1->wherein('sys_crm_deals.owner',$users);
                }
            }
            else{
                $data1->wherein('sys_crm_deals.owner',$users);
            }
            
            $dataA = $data1->get();
            $retAmount=0; $retProfit=0; $retActual=0;
            if(count($dataA)>0){
                foreach($dataA as $dt){
                    $retAmount += SysHelper::get_deal_value($dt->deal_value,$dt->source,$dt->deal_currency,$dt->deal_percent,$dt->cust_id);
                    $retProfit += SysHelper::get_gp_value($dt->deal_profit,$dt->deal_currency);
                    $retActual += SysHelper::get_deal_value_actual($dt->deal_profit,$dt->deal_currency);
                }
            }
            $revenue = [SysHelper::com_curr_format($retAmount, 2, '.', ','),SysHelper::com_curr_format($retProfit, 2, '.', ','),SysHelper::com_curr_format($retActual, 2, '.', ',')];

            return $revenue;
                
        } catch (\Throwable $th) {
            return $th;
        }
    }
    public static function get_total_sales_revenue_by_company($date, $company){
        try {        
            $data1 = DB::table('sys_crm_deals')->select('sys_crm_deals.id as dealid','deal_value','deal_profit','deal_currency','source','cust_id','deal_percent','owner')
            ->leftjoin('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_id','sys_crm_deals.id')
            ->leftjoin('sys_crm_deal_track_approval_receivables','sys_crm_deal_track_approval_receivables.deal_id','sys_crm_deals.id')
            ->where('sys_crm_deals.stage',4)//->where('sys_crm_deals.is_partial_invoice',0)
            //->where('sys_crm_deal_track_approval_invoice.status',1)
            ->where(function ($query) {
                $query->orwhere('sys_crm_deal_track_approval_invoice.status',1)
                ->orwhere('sys_crm_deal_track_approval_receivables.status',1);
            })
            ->where('sys_crm_deals.company_id',$company);
            
            if($date=="d"){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') = '".date('Y-m-d')."'");
            }
            if($date=="m"){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m') = '".date('Y-m')."'");
            }
            if($date=="y"){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y') = '".date('Y')."'");
            }
            if($date=="q"){
                $quarter = SysHelper::get_quarter(date('m'));
                $start_date = $quarter[0];
                $end_date = $quarter[1];            
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '".$end_date."'");
            }
            if($date=="pm"){
                $c_date = Carbon::createFromFormat('Y-m', date('Y-m'))->subMonth();  
                $pm_date = $c_date->format('Y-m');
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m') = '".$pm_date."'");
            }
            if($date=="pq"){
                $quarter = SysHelper::get_pre_quarter(date('m'));
                $start_date = $quarter[0];
                $end_date = $quarter[1];
                
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '".$end_date."'");
            }

            /*if(in_array(Auth::user()->id,[36,25,51,31,27])) { //jacob
                    $users=[36,25,51,31,27];
                    $data1->wherein('sys_crm_deals.owner',$users); 
            }*/
            
            $dataA = $data1->get();
            $retAmount=0; $retProfit=0; $retActual=0;
            if(count($dataA)>0){
                foreach($dataA as $dt){
                    $retAmount += SysHelper::get_deal_value($dt->deal_value,$dt->source,$dt->deal_currency,$dt->deal_percent,$dt->cust_id);
                    $retProfit += SysHelper::get_gp_value($dt->deal_profit,$dt->deal_currency);
                    $retActual += SysHelper::get_deal_value_actual($dt->deal_profit,$dt->deal_currency);
                }
            }
            $revenue = [SysHelper::com_curr_format($retAmount, 2, '.', ','),SysHelper::com_curr_format($retProfit, 2, '.', ','),SysHelper::com_curr_format($retActual, 2, '.', ',')];

            return $revenue;
                
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public static function get_total_on_process($users, $date, $company){
        try {
         $data1 = DB::table('sys_crm_deals')->select('deal_value','deal_currency','source','cust_id')
        //->where('sys_crm_deals.company_id',$company)
        ->where('stage',4);
        /*->whereNotIn('id',function($query) use($company){
            $query->select('deal_id')->from('sys_crm_deal_track_approval_invoice')->where('status',1)->where('company_id',$company);
         });*/

            
            if($date=="d"){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') = '".date('Y-m-d')."'");
            }
            if($date=="m"){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m') = '".date('Y-m')."'");
            }
            if($date=="y"){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y') = '".date('Y')."'");
            }
            if($date=="q"){
                $quarter = SysHelper::get_quarter(date('m'));
                $start_date = $quarter[0];
                $end_date = $quarter[1];            
                $data1->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') <= '".$end_date."'");
            }
            if($date=="pm"){
                $c_date = Carbon::createFromFormat('Y-m', date('Y-m'))->subMonth();  
                $pm_date = $c_date->format('Y-m');
                $data1->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m') = '".$pm_date."'");
            }
            if($date=="pq"){
                $quarter = SysHelper::get_pre_quarter(date('m'));
                $start_date = $quarter[0];
                $end_date = $quarter[1];
                
                $data1->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') <= '".$end_date."'");
            }

            if(Auth::user()->role_id == 1){ //super admin
                
            }
            elseif(Auth::user()->role_id == 2){ //admin
                if(in_array(36,$users)) { //jacob
                    $users=[36,25,51,68,64,31,65,27];
                    //$data1->wherein('sys_crm_deals.owner',$users); 
                } else {
                    $data1->wherein('sys_crm_deals.owner',$users);
                }
            }
            else{
                $data1->wherein('sys_crm_deals.owner',$users);
            }
            
            $dataA = $data1->get();
            $retAmount=0;
            if(count($dataA)>0){
                foreach($dataA as $dt){
                    $retAmount+= SysHelper::get_aed_amount_new($dt->deal_currency,$dt->deal_value);
                }
            }
            $revenue = SysHelper::com_curr_format($retAmount, 2, '.', ',');

            return $revenue;
                
        } catch (\Throwable $th) {
            return $th;
        }
    }
    public static function get_total_on_process_dashboard_report($users, $company, $start_date, $end_date){
        try {
         $data1 = DB::table('sys_crm_deals')->select('sys_crm_deals.deal_value','sys_crm_deals.deal_currency','sys_crm_deals.deal_profit','sys_crm_deals.source','sys_crm_deals.cust_id','sys_cust_suppl.internal')   
         ->join('sys_cust_suppl', 'sys_cust_suppl.id','sys_crm_deals.cust_id')     
        ->where('sys_crm_deals.stage',4);

        if($start_date != "" && $end_date != ""){
            $data1->whereRaw("DATE_FORMAT(estimated_close_date, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(estimated_close_date, '%Y-%m-%d') <= '".$end_date."'");
        }

            $data1->wherein('sys_crm_deals.owner',$users);
            $dataA = $data1->get();
            
            $customer_ids = $dataA->pluck('cust_id')->toArray();
            $customer_count = array_count_values($customer_ids);
            $new_customers = collect($customer_count)->filter(function ($count) { return $count === 1; })->keys()->toArray();
            $old_customers = collect($customer_count)->filter(function ($count) { return $count > 1; })->flatMap(function ($count, $customer) { return array_fill(0, $count, $customer);})->toArray();
            $int_customers = SysCustSuppl::where('internal',1)->wherein('id',$customer_ids)->count();
            
            $retValue=0;
            $retGP=0;
            $retValue_in=0;
            $retGP_in=0;
            $retValue_ex=0;
            $retGP_ex=0;
            $retCount=0;
            if(count($dataA)>0){
                foreach($dataA as $dt){
                    $retValue+= SysHelper::get_aed_amount_new($dt->deal_currency,$dt->deal_value);
                    $retGP+= SysHelper::get_aed_amount_new($dt->deal_currency,$dt->deal_profit);
                    $retCount++;

                    if($dt->internal == 1){
                        $retValue_in+= SysHelper::get_aed_amount_new($dt->deal_currency,$dt->deal_value);
                        $retGP_in+= SysHelper::get_aed_amount_new($dt->deal_currency,$dt->deal_profit);
                    } else {
                        $retValue_ex+= SysHelper::get_aed_amount_new($dt->deal_currency,$dt->deal_value);
                        $retGP_ex+= SysHelper::get_aed_amount_new($dt->deal_currency,$dt->deal_profit);
                    }
                }
            }
            return [$retValue, $retGP,$retCount,count($new_customers),count($old_customers),$int_customers,$retValue_in,$retValue_ex,$retGP_in,$retGP_ex];
                
        } catch (\Throwable $th) {
            return $th;
        }
    }
    public static function get_total_forcast_dashboard_report($users, $company, $start_date, $end_date){
        try {
         $data1 = DB::table('sys_crm_deals')->select('sys_crm_deals.deal_value','sys_crm_deals.deal_profit','sys_crm_deals.deal_currency','sys_crm_deals.source','sys_crm_deals.cust_id','sys_cust_suppl.internal')
         ->join('sys_cust_suppl', 'sys_cust_suppl.id','sys_crm_deals.cust_id')
        //->where('sys_crm_deals.company_id',$company)
        ->wherein('sys_crm_deals.stage',[1,2,3]);            
           

        if($start_date != "" && $end_date != ""){
            $data1->whereRaw("DATE_FORMAT(estimated_close_date, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(estimated_close_date, '%Y-%m-%d') <= '".$end_date."'");
        }
        
            $data1->wherein('sys_crm_deals.owner',$users);
            
            $dataA = $data1->get();
            
            $customer_ids = $dataA->pluck('cust_id')->toArray();
            $customer_count = array_count_values($customer_ids);
            $new_customers = collect($customer_count)->filter(function ($count) { return $count === 1; })->keys()->toArray();
            $old_customers = collect($customer_count)->filter(function ($count) { return $count > 1; })->flatMap(function ($count, $customer) { return array_fill(0, $count, $customer);})->toArray();
            $int_customers = SysCustSuppl::where('internal',1)->wherein('id',$customer_ids)->count();

            $retValue=0;
            $retGP=0;
            $retValue_in=0;
            $retGP_in=0;
            $retValue_ex=0;
            $retGP_ex=0;
            $retCount=0;
            if(count($dataA)>0){
                foreach($dataA as $dt){
                    $retValue+= SysHelper::get_aed_amount_new($dt->deal_currency,$dt->deal_value);
                    $retGP+= SysHelper::get_aed_amount_new($dt->deal_currency,$dt->deal_profit);
                    $retCount++;

                    if($dt->internal == 1){
                        $retValue_in+= SysHelper::get_aed_amount_new($dt->deal_currency,$dt->deal_value);
                        $retGP_in+= SysHelper::get_aed_amount_new($dt->deal_currency,$dt->deal_profit);
                    } else {
                        $retValue_ex+= SysHelper::get_aed_amount_new($dt->deal_currency,$dt->deal_value);
                        $retGP_ex+= SysHelper::get_aed_amount_new($dt->deal_currency,$dt->deal_profit);
                    }
                }
            }
            return [$retValue,$retGP,$retCount,count($new_customers),count($old_customers),$int_customers,$retValue_in,$retValue_ex,$retGP_in,$retGP_ex];
                
        } catch (\Throwable $th) {
            return $th;
        }
    }
    public static function get_total_on_process_by_company($date, $company){
        try {
         $data1 = DB::table('sys_crm_deals')->select('deal_value','deal_currency','source','cust_id')
        ->where('sys_crm_deals.company_id',$company)
        ->where('stage',4)
        ->whereNotIn('id',function($query) use($company){
            $query->select('deal_id')->from('sys_crm_deal_track_approval_invoice')->where('status',1)->where('company_id',$company);
         });

            
            if($date=="d"){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') = '".date('Y-m-d')."'");
            }
            if($date=="m"){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m') = '".date('Y-m')."'");
            }
            if($date=="y"){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y') = '".date('Y')."'");
            }
            if($date=="q"){
                $quarter = SysHelper::get_quarter(date('m'));
                $start_date = $quarter[0];
                $end_date = $quarter[1];            
                $data1->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') <= '".$end_date."'");
            }
            if($date=="pm"){
                $c_date = Carbon::createFromFormat('Y-m', date('Y-m'))->subMonth();  
                $pm_date = $c_date->format('Y-m');
                $data1->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m') = '".$pm_date."'");
            }
            if($date=="pq"){
                $quarter = SysHelper::get_pre_quarter(date('m'));
                $start_date = $quarter[0];
                $end_date = $quarter[1];
                
                $data1->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') <= '".$end_date."'");
            }

            if(Auth::user()->role_id == 1){ //super admin
                
            }
            elseif(Auth::user()->role_id == 2){ //admin
                
            }
            else{
                
            }
            
            $dataA = $data1->get();
            $retAmount=0;
            if(count($dataA)>0){
                foreach($dataA as $dt){
                    $retAmount+= SysHelper::get_aed_amount_new($dt->deal_currency,$dt->deal_value);
                }
            }
            $revenue = SysHelper::com_curr_format($retAmount, 2, '.', ',');

            return $revenue;
                
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public static function get_receivable_os_report_dashboard($user_id, $company_id){
        try {


$count_os = DB::table('os_report')->where(['cart_id' => session('logged_session_data.cart_id'),'user_id' => $user_id])->count();
$count_os_received = DB::table('os_report_received')->where(['cart_id' => session('logged_session_data.cart_id'),'user_id' => $user_id])->count();

if($count_os==0 || $count_os_received==0){ 

            DB::table('os_report')->where(['cart_id' => session('logged_session_data.cart_id')])->delete();
            DB::table('os_report_received')->where(['cart_id' => session('logged_session_data.cart_id')])->delete();
                
                $data_query = DB::table('sys_chartofaccounts_transaction as t')->select('t.transaction_no',DB::raw('sum(t.debit_amount) as debit_amount'),'c.internal')
                ->join('sys_chartofaccounts as c','c.id','t.account_id')
                ->join('sys_sales_invoice as s','s.doc_number','t.transaction_no');
                    $data_query->where('t.company_id',$company_id);
                    if($user_id!=0){
                        $data_query->where('s.sales_man', $user_id);
                    }
                    $data_query->where('t.status', 1)->where('t.debit_amount','>',0);
                    $data_query->wherein('t.transaction_type',['salesinvoice','opbinvoice','openingbalance111']);                                
                    $data_all = $data_query->groupby('t.transaction_no','c.internal')->get();

                            if(count($data_all)>0){
                                foreach ($data_all as $key) {
                                    $indata[] = [
                                        'cart_id' => session('logged_session_data.cart_id'),
                                        'user_id' => $user_id,
                                        'doc_no' => $key->transaction_no,
                                        'amount' => $key->debit_amount,
                                        'internal' => $key->internal,
                                        'company_id' => $company_id,
                                    ];
                                }
                                DB::table('os_report')->insert($indata);
                            }

                        $pdc_list = SysReceipt::select(DB::raw('sum(cat.debit_amount) as debit_amount'),DB::raw('sum(cat.credit_amount) as credit_amount'), 'adj.bi_doc_no')
                        ->join('sys_chartofaccounts_transaction as cat','cat.transaction_no','sys_receipt.doc_number')
                        ->leftjoin('sys_receipt_adjustments as adj' ,'adj.bi_doc_number','sys_receipt.doc_number')
                        ->where('sys_receipt.pdc_removed_os',1)
                        ->where('sys_receipt.status', 1)->where('sys_receipt.mode', 2)->where('sys_receipt.receipt_through', 3)
                        ->where('sys_receipt.company_id',$company_id)
                        ->groupby('adj.bi_doc_no',)->get();
                        
                    if(count($pdc_list)>0){
                        foreach ($pdc_list as $key) {
                            $indata_received[] = [ 'cart_id' => session('logged_session_data.cart_id'),
                            'user_id' => $user_id,
                            'doc_no' => $key->bi_doc_no,
                            'amount' => $key->debit_amount,
                            'company_id' => $company_id, ];
                        }
                    }
                
                        /*$unadjested_list = SysReceiptAdjustments::select('bi_doc_number','bi_doc_no','account_id',db::raw('max(bi_doc_date) as bi_doc_date'),db::raw('max(bi_cheque_amount) as rec_amount'),db::raw('sum(bi_total) as rec_paid'),db::raw('max(bi_cheque_amount) - sum(bi_total) as rec_balance'),DB::raw('(SELECT MAX(sys_chartofaccounts.account_name) FROM sys_chartofaccounts_transaction INNER JOIN sys_chartofaccounts ON sys_chartofaccounts.id=sys_chartofaccounts_transaction.account_id  WHERE transaction_no = sys_receipt_adjustments.bi_doc_number AND is_main_account = 1) AS main_account_id'))
                        ->where('company_id',$company_id)
                        ->groupby('bi_doc_number','account_id','bi_doc_no')
                        ->having(db::raw('max(bi_cheque_amount)'), '>' ,db::raw('sum(bi_total)'))
                        ->get();*/

                    // $unadjested_list = SysReceiptAdjustments::select('bi_doc_no',db::raw('sum(bi_total) as rec_paid'))
                    // ->where('company_id',$company_id)
                    // ->groupby('bi_doc_no')
                    // //->having(db::raw('max(bi_cheque_amount)'), '>' ,db::raw('sum(bi_total)'))
                    // ->get();
                    //return $unadjested_list;
                    // if(count($unadjested_list)>0){
                    //     foreach ($unadjested_list as $key) {
                    //         $indata_received[] = [ 'cart_id' => session('logged_session_data.cart_id'),
                    //         'user_id' => $user_ids,
                    //         'doc_no' => $key->bi_doc_no,
                    //         'amount' => $key->rec_paid, ];
                    //     }
                    // }

                    $data_adjestment_all = DB::table('sys_sales_return_adjestment')->select('siv_no',DB::raw('sum(paid_amount) as paid_amount'))
                    ->groupby('siv_no')->get();
                    
                    if(count($data_adjestment_all)>0){
                        foreach ($data_adjestment_all as $key) {
                            $indata_received[] = [ 'cart_id' => session('logged_session_data.cart_id'),
                            'user_id' => $user_id,
                            'doc_no' => $key->siv_no,
                            'amount' => $key->paid_amount,
                            'company_id' => $company_id, ];
                        }
                    }
                    
                    $data_receipt_all = DB::table('sys_receipt as r')->select('ra.bi_doc_no',DB::raw('sum(ra.bi_amount) as bi_amount'))->join('sys_receipt_adjustments as ra','ra.bi_doc_number','r.doc_number')
                    ->where('r.company_id',$company_id)
                    ->where('r.status',1)->groupby('ra.bi_doc_no')->get();
                    
                    if(count($data_receipt_all)>0){
                        foreach ($data_receipt_all as $key) {
                            $indata_received[] = [ 'cart_id' => session('logged_session_data.cart_id'),
                            'user_id' => $user_id,
                            'doc_no' => $key->bi_doc_no,
                            'amount' => $key->bi_amount,
                            'company_id' => $company_id, ];
                        }
                    }

                    $data_receipt2_all = DB::table('sys_journalvoucher as j')->select('ra.bi_doc_no',DB::raw('sum(ra.bi_amount) as bi_amount'))
                    ->join('sys_receipt_adjustments as ra','ra.bi_doc_number','j.doc_number')->wherein('ra.transaction_type',['journalreceipt'])
                    ->where('j.status',1)->where('ra.company_id',$company_id)->groupby('ra.bi_doc_no')->get();
                    
                    if(count($data_receipt2_all)>0){
                        foreach ($data_receipt2_all as $key) {
                            $indata_received[] = [ 'cart_id' => session('logged_session_data.cart_id'),
                            'user_id' => $user_id,
                            'doc_no' => $key->bi_doc_no,
                            'amount' => $key->bi_amount,
                            'company_id' => $company_id, ];
                        }
                    }

                    // $data_receipt3_all = DB::table('sys_journalvoucher as j')->select('pa.bi_doc_no','j.doc_number','pa.bi_amount','j.doc_date','pa.account_id')
                    // ->join('sys_payment_adjustments as pa','pa.bi_doc_number','j.doc_number')->where('j.status',1)->get();
                    // $data_receipt3_all_amount = $data_receipt3_all->sum('bi_amount');
                    // $data_receipt3_all_count = $data_receipt3_all->count();
                    // return $data_receipt3_all;

                    $data_return_all = DB::table('sys_sales_return as r')->select('ra.siv_no',DB::raw('sum(ra.paid_amount) as paid_amount'))
                    ->join('sys_sales_return_adjestment as ra','ra.srn_no','r.doc_number')
                    ->where('r.company_id',$company_id)
                    ->where('r.status',1)->groupby('ra.siv_no')->get();

                    if(count($data_return_all)>0){
                        foreach ($data_return_all as $key) {
                            $indata_received[] = [ 'cart_id' => session('logged_session_data.cart_id'),
                            'user_id' => $user_id,
                            'doc_no' => $key->siv_no,
                            'amount' => $key->paid_amount,
                            'company_id' => $company_id, ];
                        }
                    }


                    if(count($indata_received)>0){
                        DB::table('os_report_received')->insert($indata_received);
                    }
          
// //////////////
// First, update os_report where amount <= total amount in os_report_received
DB::table('os_report as os')
    ->join(
        DB::raw('(SELECT doc_no, SUM(amount) as total_amount FROM os_report_received GROUP BY doc_no) as orr'),
        function ($join) {
            $join->on('os.doc_no', '=', 'orr.doc_no')
                 ->whereRaw('os.amount <= orr.total_amount');
        }
    )
    ->where(['os.cart_id' => session('logged_session_data.cart_id'), 'os.user_id' => $user_id, 'os.status' => 1])
    ->update(['os.status' => 2]);

// Second, update os_report_received without directly using it in a subquery
DB::table('os_report_received as orr')
    ->join('os_report as os', function ($join) {
        $join->on('orr.doc_no', '=', 'os.doc_no')
             ->whereRaw('os.amount <= orr.amount');
    })
    ->where(['os.cart_id' => session('logged_session_data.cart_id'), 'os.user_id' => $user_id, 'os.status' => 1])
    ->update(['orr.status' => 2]);
// ////////////////
}

$total_os = DB::table('os_report')->where(['cart_id' => session('logged_session_data.cart_id'),'user_id' => $user_id])->get();
$total_os_received = DB::table('os_report_received')->where(['cart_id' => session('logged_session_data.cart_id'),'user_id' => $user_id])->get();

                    $adj_sum = $total_os->sum('amount');
                    $adj_count = $total_os->count();                    
                    $adj_due_sum = $total_os->where('status',2)->sum('amount');
                    $adj_due_count = $total_os->where('status',2)->count();
                    $adj_over_due_sum = $total_os->where('status',1)->sum('amount');
                    $adj_over_due_count = $total_os->where('status',1)->count();                    
                    $os = [$adj_sum,$adj_count, $adj_due_sum,$adj_due_count, $adj_over_due_sum,$adj_over_due_count];
                    
                    $in_adj_sum = $total_os->where('internal',1)->sum('amount');
                    $in_adj_count = $total_os->where('internal',1)->count();                    
                    $in_adj_due_sum = $total_os->where('internal',1)->where('status',2)->sum('amount');
                    $in_adj_due_count = $total_os->where('internal',1)->where('status',2)->count();
                    $in_adj_over_due_sum = $total_os->where('internal',1)->where('status',1)->sum('amount');
                    $in_adj_over_due_count = $total_os->where('internal',1)->where('status',1)->count();                    
                    $os_in = [$in_adj_sum,$in_adj_count, $in_adj_due_sum,$in_adj_due_count, $in_adj_over_due_sum,$in_adj_over_due_count];

                    $ex_adj_sum = $total_os->where('internal',0)->sum('amount');
                    $ex_adj_count = $total_os->where('internal',0)->count();                    
                    $ex_adj_due_sum = $total_os->where('internal',0)->where('status',2)->sum('amount');
                    $ex_adj_due_count = $total_os->where('internal',0)->where('status',2)->count();
                    $ex_adj_over_due_sum = $total_os->where('internal',0)->where('status',1)->sum('amount');
                    $ex_adj_over_due_count = $total_os->where('internal',0)->where('status',1)->count();                    
                    $os_ex = [$ex_adj_sum,$ex_adj_count, $ex_adj_due_sum,$ex_adj_due_count, $ex_adj_over_due_sum,$ex_adj_over_due_count];

                    

                    $pending_doc = $total_os->where('status',1)->pluck('doc_no');

                    $due_30_count=0; $due_30_amount=0;
                    $due_60_count=0; $due_60_amount=0;
                    $due_90_count=0; $due_90_amount=0;
                    $due_91_count=0; $due_91_amount=0;
                    
                    $in_due_30_count=0; $in_due_30_amount=0;
                    $in_due_60_count=0; $in_due_60_amount=0;
                    $in_due_90_count=0; $in_due_90_amount=0;
                    $in_due_91_count=0; $in_due_91_amount=0;
                    
                    $ex_due_30_count=0; $ex_due_30_amount=0;
                    $ex_due_60_count=0; $ex_due_60_amount=0;
                    $ex_due_90_count=0; $ex_due_90_amount=0;
                    $ex_due_91_count=0; $ex_due_91_amount=0;

                    $invoice = DB::table('sys_sales_invoice as i')->select('i.doc_number','i.doc_date','i.payment_terms','t.debit_amount','c.internal')
                    ->join('sys_chartofaccounts_transaction as t','t.transaction_no','i.doc_number')
                    ->join('sys_chartofaccounts as c','c.id','t.account_id')
                    ->where('t.company_id',$company_id)
                    ->where('t.status', 1)->where('t.debit_amount','>',0)
                    ->wherein('i.doc_number',$pending_doc)->get();
                    if(count($invoice)>0){
                        foreach ($invoice as $value) {
                            $ret = SysHelper::get_due_date_sales_invoice_dashboard($value->doc_date,$value->payment_terms);

                            if($ret == 1 || $ret == 0){ // 0-30
                                $due_30_count++;
                                $due_30_amount += $value->debit_amount;
                                if($value->internal==1){
                                    $in_due_30_count++;
                                    $in_due_30_amount += $value->debit_amount;
                                } else {
                                    $ex_due_30_count++;
                                    $ex_due_30_amount += $value->debit_amount;
                                }
                            }
                            if($ret == 2){ // 31-60
                                $due_60_count++;
                                $due_60_amount += $value->debit_amount;
                                if($value->internal==1){
                                    $in_due_60_count++;
                                    $in_due_60_amount += $value->debit_amount;
                                } else {
                                    $ex_due_60_count++;
                                    $ex_due_60_amount += $value->debit_amount;                                    
                                }
                            }
                            if($ret == 3){ // 61-90
                                $due_90_count++;
                                $due_90_amount += $value->debit_amount;
                                if($value->internal==1){
                                    $in_due_90_count++;
                                    $in_due_90_amount += $value->debit_amount;
                                } else {
                                    $ex_due_90_count++;
                                    $ex_due_90_amount += $value->debit_amount;                                    
                                }
                            }
                            if($ret == 4){ // >90
                                $due_91_count++;
                                $due_91_amount += $value->debit_amount;
                                if($value->internal==1){
                                    $in_due_91_count++;
                                    $in_due_91_amount += $value->debit_amount;
                                } else {
                                    $ex_due_91_count++;
                                    $ex_due_91_amount += $value->debit_amount;                                    
                                }
                            }
                        }
                    }
                    $due_by_days=[$due_30_amount,$due_30_count, $due_60_amount,$due_60_count, $due_90_amount,$due_90_count, $due_91_amount,$due_91_count];
                    $due_by_days_in=[$in_due_30_amount,$in_due_30_count, $in_due_60_amount,$in_due_60_count, $in_due_90_amount,$in_due_90_count, $in_due_91_amount,$in_due_91_count];
                    $due_by_days_ex=[$ex_due_30_amount,$ex_due_30_count, $ex_due_60_amount,$ex_due_60_count, $ex_due_90_amount,$ex_due_90_count, $ex_due_91_amount,$ex_due_91_count];

                  return [$os,$os_in,$os_ex,$due_by_days,$due_by_days_in,$due_by_days_ex];

                
        } catch (\Throwable $th) {
            
            $os = [0,0, 0,0, 0,0];
            $os_in = [0,0, 0,0, 0,0];
            $os_ex = [0,0, 0,0, 0,0];
            $due_by_days=[0,0, 0,0, 0,0, 0,0];
            $due_by_days_in=[0,0, 0,0, 0,0, 0,0];
            $due_by_days_ex=[0,0, 0,0, 0,0, 0,0];
                  return [$os,$os_in,$os_ex,$due_by_days,$due_by_days_in,$due_by_days_ex];
            return $th;
        }


    }

//outstanding summary
public static function get_receivable_os_report_summary($company_id){
        try {


//$count_os = DB::table('os_report_summary')->where(['cart_id' => session('logged_session_data.cart_id'), 'company_id' => session('logged_session_data.company_id')])->count();
//$count_os_received = DB::table('os_report_summary_received')->where(['cart_id' => session('logged_session_data.cart_id'), 'company_id' => session('logged_session_data.company_id')])->count();

//if($count_os==0 || $count_os_received==0){ 

            DB::table('os_report_summary')->where(['cart_id' => session('logged_session_data.cart_id'), 'company_id' => session('logged_session_data.company_id')])->delete();
            DB::table('os_report_summary_received')->where(['cart_id' => session('logged_session_data.cart_id'), 'company_id' => session('logged_session_data.company_id')])->delete();
                
                $data_query = DB::table('sys_chartofaccounts_transaction as t')->select('t.transaction_no',DB::raw('sum(t.debit_amount) as debit_amount'),'c.internal','s.sales_man')
                ->join('sys_chartofaccounts as c','c.id','t.account_id')
                ->join('sys_sales_invoice as s','s.doc_number','t.transaction_no');
                    $data_query->where('t.company_id',$company_id);
                    /*if($user_id!=0){
                        $data_query->where('s.sales_man', $user_id);
                    }*/
                    $data_query->where('t.status', 1)->where('t.debit_amount','>',0);
                    $data_query->wherein('t.transaction_type',['salesinvoice','opbinvoice','openingbalance111']);                                
                    $data_all = $data_query->groupby('t.transaction_no','c.internal','s.sales_man')->get();

                            if(count($data_all)>0){
                                foreach ($data_all as $key) {
                                    $indata[] = [
                                        'cart_id' => session('logged_session_data.cart_id'),
                                        'user_id' => $key->sales_man,
                                        'doc_no' => $key->transaction_no,
                                        'amount' => $key->debit_amount,
                                        'internal' => $key->internal,
                                        'company_id' => $company_id,
                                    ];
                                }
                                DB::table('os_report_summary')->insert($indata);
                            }

                        $pdc_list = SysReceipt::select(DB::raw('sum(cat.debit_amount) as debit_amount'),DB::raw('sum(cat.credit_amount) as credit_amount'), 'adj.bi_doc_no')
                        ->join('sys_chartofaccounts_transaction as cat','cat.transaction_no','sys_receipt.doc_number')
                        ->join('sys_receipt_adjustments as adj' ,'adj.bi_doc_number','sys_receipt.doc_number')
                        ->where('sys_receipt.pdc_removed_os',1)
                        ->where('sys_receipt.status', 1)->where('sys_receipt.mode', 2)->where('sys_receipt.receipt_through', 3)
                        ->where('sys_receipt.company_id',$company_id)
                        ->groupby('adj.bi_doc_no',)->get();
                        
                    if(count($pdc_list)>0){
                        foreach ($pdc_list as $key) {
                            $indata_received[] = [ 'cart_id' => session('logged_session_data.cart_id'),
                            'user_id' => 0,
                            'doc_no' => $key->bi_doc_no,
                            'amount' => $key->debit_amount,
                            'company_id' => $company_id, ];
                        }
                    }

                    $data_adjestment_all = DB::table('sys_sales_return_adjestment')->select('siv_no',DB::raw('sum(paid_amount) as paid_amount'))
                    ->groupby('siv_no')->get();
                    
                    if(count($data_adjestment_all)>0){
                        foreach ($data_adjestment_all as $key) {
                            $indata_received[] = [ 'cart_id' => session('logged_session_data.cart_id'),
                            'user_id' => 0,
                            'doc_no' => $key->siv_no,
                            'amount' => $key->paid_amount,
                            'company_id' => $company_id, ];
                        }
                    }
                    
                    $data_receipt_all = DB::table('sys_receipt as r')->select('ra.bi_doc_no',DB::raw('sum(ra.bi_amount) as bi_amount'))->join('sys_receipt_adjustments as ra','ra.bi_doc_number','r.doc_number')
                    ->where('r.company_id',$company_id)
                    ->where('r.status',1)->groupby('ra.bi_doc_no')->get();
                    
                    if(count($data_receipt_all)>0){
                        foreach ($data_receipt_all as $key) {
                            $indata_received[] = [ 'cart_id' => session('logged_session_data.cart_id'),
                            'user_id' => 0,
                            'doc_no' => $key->bi_doc_no,
                            'amount' => $key->bi_amount,
                            'company_id' => $company_id, ];
                        }
                    }

                    $data_receipt2_all = DB::table('sys_journalvoucher as j')->select('ra.bi_doc_no',DB::raw('sum(ra.bi_amount) as bi_amount'))
                    ->join('sys_receipt_adjustments as ra','ra.bi_doc_number','j.doc_number')->wherein('ra.transaction_type',['journalreceipt'])
                    ->where('j.status',1)->where('ra.company_id',$company_id)->groupby('ra.bi_doc_no')->get();
                    
                    if(count($data_receipt2_all)>0){
                        foreach ($data_receipt2_all as $key) {
                            $indata_received[] = [ 'cart_id' => session('logged_session_data.cart_id'),
                            'user_id' => 0,
                            'doc_no' => $key->bi_doc_no,
                            'amount' => $key->bi_amount,
                            'company_id' => $company_id, ];
                        }
                    }

                    // $data_receipt3_all = DB::table('sys_journalvoucher as j')->select('pa.bi_doc_no','j.doc_number','pa.bi_amount','j.doc_date','pa.account_id')
                    // ->join('sys_payment_adjustments as pa','pa.bi_doc_number','j.doc_number')->where('j.status',1)->get();
                    // $data_receipt3_all_amount = $data_receipt3_all->sum('bi_amount');
                    // $data_receipt3_all_count = $data_receipt3_all->count();
                    // return $data_receipt3_all;

                    $data_return_all = DB::table('sys_sales_return as r')->select('ra.siv_no',DB::raw('sum(ra.paid_amount) as paid_amount'))
                    ->join('sys_sales_return_adjestment as ra','ra.srn_no','r.doc_number')
                    ->where('r.company_id',$company_id)
                    ->where('r.status',1)->groupby('ra.siv_no')->get();

                    if(count($data_return_all)>0){
                        foreach ($data_return_all as $key) {
                            $indata_received[] = [ 'cart_id' => session('logged_session_data.cart_id'),
                            'user_id' => 0,
                            'doc_no' => $key->siv_no,
                            'amount' => $key->paid_amount,
                            'company_id' => $company_id, ];
                        }
                    }


                    if(count($indata_received)>0){
                        DB::table('os_report_summary_received')->insert($indata_received);
                    }
          
// //////////////
// First, update os_report where amount <= total amount in os_report_received
DB::table('os_report_summary as os')
    ->join(
        DB::raw('(SELECT doc_no, SUM(amount) as total_amount FROM os_report_summary_received GROUP BY doc_no) as orr'),
        function ($join) {
            $join->on('os.doc_no', '=', 'orr.doc_no')
                 ->whereRaw('os.amount <= orr.total_amount');
        }
    )
    ->where(['os.cart_id' => session('logged_session_data.cart_id'), 'os.status' => 1])
    ->update(['os.status' => 2]);

// Second, update os_report_received without directly using it in a subquery
DB::table('os_report_summary_received as orr')
    ->join('os_report_summary as os', function ($join) {
        $join->on('orr.doc_no', '=', 'os.doc_no')
             ->whereRaw('os.amount <= orr.amount');
    })
    ->where(['os.cart_id' => session('logged_session_data.cart_id'), 'os.status' => 1])
    ->update(['orr.status' => 2]);
// ////////////////
//}

$total_os = DB::table('os_report_summary')->where(['cart_id' => session('logged_session_data.cart_id')])->get();
$total_os_received = DB::table('os_report_summary_received')->where(['cart_id' => session('logged_session_data.cart_id')])->get();

                    $adj_sum = $total_os->sum('amount');
                    $adj_count = $total_os->count();                    
                    $adj_due_sum = $total_os->where('status',2)->sum('amount');
                    $adj_due_count = $total_os->where('status',2)->count();
                    $adj_over_due_sum = $total_os->where('status',1)->sum('amount');
                    $adj_over_due_count = $total_os->where('status',1)->count();                    
                    $os = [$adj_sum,$adj_count, $adj_due_sum,$adj_due_count, $adj_over_due_sum,$adj_over_due_count];
                    
                    $in_adj_sum = $total_os->where('internal',1)->sum('amount');
                    $in_adj_count = $total_os->where('internal',1)->count();                    
                    $in_adj_due_sum = $total_os->where('internal',1)->where('status',2)->sum('amount');
                    $in_adj_due_count = $total_os->where('internal',1)->where('status',2)->count();
                    $in_adj_over_due_sum = $total_os->where('internal',1)->where('status',1)->sum('amount');
                    $in_adj_over_due_count = $total_os->where('internal',1)->where('status',1)->count();                    
                    $os_in = [$in_adj_sum,$in_adj_count, $in_adj_due_sum,$in_adj_due_count, $in_adj_over_due_sum,$in_adj_over_due_count];

                    $ex_adj_sum = $total_os->where('internal',0)->sum('amount');
                    $ex_adj_count = $total_os->where('internal',0)->count();                    
                    $ex_adj_due_sum = $total_os->where('internal',0)->where('status',2)->sum('amount');
                    $ex_adj_due_count = $total_os->where('internal',0)->where('status',2)->count();
                    $ex_adj_over_due_sum = $total_os->where('internal',0)->where('status',1)->sum('amount');
                    $ex_adj_over_due_count = $total_os->where('internal',0)->where('status',1)->count();                    
                    $os_ex = [$ex_adj_sum,$ex_adj_count, $ex_adj_due_sum,$ex_adj_due_count, $ex_adj_over_due_sum,$ex_adj_over_due_count];

                    

                    $pending_doc = $total_os->where('status',1)->pluck('doc_no');

                    $due_30_count=0; $due_30_amount=0;
                    $due_60_count=0; $due_60_amount=0;
                    $due_90_count=0; $due_90_amount=0;
                    $due_91_count=0; $due_91_amount=0;
                    
                    $in_due_30_count=0; $in_due_30_amount=0;
                    $in_due_60_count=0; $in_due_60_amount=0;
                    $in_due_90_count=0; $in_due_90_amount=0;
                    $in_due_91_count=0; $in_due_91_amount=0;
                    
                    $ex_due_30_count=0; $ex_due_30_amount=0;
                    $ex_due_60_count=0; $ex_due_60_amount=0;
                    $ex_due_90_count=0; $ex_due_90_amount=0;
                    $ex_due_91_count=0; $ex_due_91_amount=0;

                    $invoice = DB::table('sys_sales_invoice as i')->select('i.doc_number','i.doc_date','i.payment_terms','t.debit_amount','c.internal')
                    ->join('sys_chartofaccounts_transaction as t','t.transaction_no','i.doc_number')
                    ->join('sys_chartofaccounts as c','c.id','t.account_id')
                    ->where('t.company_id',$company_id)
                    ->where('t.status', 1)->where('t.debit_amount','>',0)
                    ->wherein('i.doc_number',$pending_doc)->get();
                    if(count($invoice)>0){
                        foreach ($invoice as $value) {
                            $ret = SysHelper::get_due_date_sales_invoice_dashboard($value->doc_date,$value->payment_terms);

                            if($ret == 1 || $ret == 0){ // 0-30
                                $due_30_count++;
                                $due_30_amount += $value->debit_amount;
                                if($value->internal==1){
                                    $in_due_30_count++;
                                    $in_due_30_amount += $value->debit_amount;
                                } else {
                                    $ex_due_30_count++;
                                    $ex_due_30_amount += $value->debit_amount;
                                }
                            }
                            if($ret == 2){ // 31-60
                                $due_60_count++;
                                $due_60_amount += $value->debit_amount;
                                if($value->internal==1){
                                    $in_due_60_count++;
                                    $in_due_60_amount += $value->debit_amount;
                                } else {
                                    $ex_due_60_count++;
                                    $ex_due_60_amount += $value->debit_amount;                                    
                                }
                            }
                            if($ret == 3){ // 61-90
                                $due_90_count++;
                                $due_90_amount += $value->debit_amount;
                                if($value->internal==1){
                                    $in_due_90_count++;
                                    $in_due_90_amount += $value->debit_amount;
                                } else {
                                    $ex_due_90_count++;
                                    $ex_due_90_amount += $value->debit_amount;                                    
                                }
                            }
                            if($ret == 4){ // >90
                                $due_91_count++;
                                $due_91_amount += $value->debit_amount;
                                if($value->internal==1){
                                    $in_due_91_count++;
                                    $in_due_91_amount += $value->debit_amount;
                                } else {
                                    $ex_due_91_count++;
                                    $ex_due_91_amount += $value->debit_amount;                                    
                                }
                            }
                        }
                    }
                    $due_by_days=[$due_30_amount,$due_30_count, $due_60_amount,$due_60_count, $due_90_amount,$due_90_count, $due_91_amount,$due_91_count];
                    $due_by_days_in=[$in_due_30_amount,$in_due_30_count, $in_due_60_amount,$in_due_60_count, $in_due_90_amount,$in_due_90_count, $in_due_91_amount,$in_due_91_count];
                    $due_by_days_ex=[$ex_due_30_amount,$ex_due_30_count, $ex_due_60_amount,$ex_due_60_count, $ex_due_90_amount,$ex_due_90_count, $ex_due_91_amount,$ex_due_91_count];

                  return [$os,$os_in,$os_ex,$due_by_days,$due_by_days_in,$due_by_days_ex];

                
        } catch (\Throwable $th) {
            
            $os = [0,0, 0,0, 0,0];
            $os_in = [0,0, 0,0, 0,0];
            $os_ex = [0,0, 0,0, 0,0];
            $due_by_days=[0,0, 0,0, 0,0, 0,0];
            $due_by_days_in=[0,0, 0,0, 0,0, 0,0];
            $due_by_days_ex=[0,0, 0,0, 0,0, 0,0];
            return [$os,$os_in,$os_ex,$due_by_days,$due_by_days_in,$due_by_days_ex];
            return $th;
        }
    }
//outstanding summary
    
    public static function get_total_target_gp($users, $date, $company){
        try {
            
            $data1 = DB::table('sys_crm_deals')->select('sys_crm_deals.id as dealid','deal_value','deal_currency','source','cust_id','deal_profit')
            ->leftjoin('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_id','sys_crm_deals.id')
            ->leftjoin('sys_crm_deal_track_approval_receivables','sys_crm_deal_track_approval_receivables.deal_id','sys_crm_deals.id')
            ->where('sys_crm_deals.stage',4)//->where('sys_crm_deals.is_partial_invoice',0)
            //->where('sys_crm_deal_track_approval_invoice.status',1);
            ->where(function ($query) {
                $query->orwhere('sys_crm_deal_track_approval_invoice.status',1)
                ->orwhere('sys_crm_deal_track_approval_receivables.status',1);
            });
            //->where('sys_crm_deals.company_id',$company);
            
            if($date=="d"){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') = '".date('Y-m-d')."'");
            }
            if($date=="m"){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m') = '".date('Y-m')."'");
            }
            if($date=="y"){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y') = '".date('Y')."'");
            }
            if($date=="q"){
                $quarter = SysHelper::get_quarter(date('m'));
                $start_date = $quarter[0];
                $end_date = $quarter[1];            
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '".$end_date."'");
            }
            if($date=="pm"){
                $c_date = Carbon::createFromFormat('Y-m', date('Y-m'))->subMonth();  
                $pm_date = $c_date->format('Y-m');
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m') = '".$pm_date."'");
            }
            if($date=="pq"){
                $quarter = SysHelper::get_pre_quarter(date('m'));
                $start_date = $quarter[0];
                $end_date = $quarter[1];
                
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '".$end_date."'");
            }

            if(Auth::user()->role_id == 1){ //super admin
                
            }
            elseif(Auth::user()->role_id == 2){ //admin
                if(in_array(36,$users)) { //jacob
                    $users=[36,25,51,31,27];
                    $data1->wherein('sys_crm_deals.owner',$users); 
                } else {
                    $data1->wherein('sys_crm_deals.owner',$users);
                }
                //$data1->wherein('sys_crm_deals.owner',$users);
            }
            else{
                $data1->wherein('sys_crm_deals.owner',$users);
            }
            $dataA = $data1->get();
            
            $retAmount=0;$retProfit=0;
            if(count($dataA)>0){
                foreach($dataA as $dt){
                    
                    $retAmount+= SysHelper::get_aed_amount_new($dt->deal_currency,$dt->deal_value);
                    
                    $retProfit+= SysHelper::get_aed_amount_new($dt->deal_currency,$dt->deal_profit);
                }
            }
            $revenue = [SysHelper::com_curr_format($retAmount, 2, '.', ','),SysHelper::com_curr_format($retProfit, 2, '.', ',')];

            return $revenue;
                
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public static function get_total_forcast($users, $date, $company){
        try {
         $data1 = DB::table('sys_crm_deals')->select('deal_value','deal_currency','source','cust_id')
        //->where('sys_crm_deals.company_id',$company)
        ->wherein('stage',[1,2,3]);            
            if($date=="d"){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') = '".date('Y-m-d')."'");
            }
            if($date=="m"){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m') = '".date('Y-m')."'");
            }
            if($date=="y"){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y') = '".date('Y')."'");
            }
            if($date=="q"){
                $quarter = SysHelper::get_quarter(date('m'));
                $start_date = $quarter[0];
                $end_date = $quarter[1];            
                $data1->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') <= '".$end_date."'");
            }
            if($date=="pm"){
                $c_date = Carbon::createFromFormat('Y-m', date('Y-m'))->subMonth();  
                $pm_date = $c_date->format('Y-m');
                $data1->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m') = '".$pm_date."'");
            }
            if($date=="pq"){
                $quarter = SysHelper::get_pre_quarter(date('m'));
                $start_date = $quarter[0];
                $end_date = $quarter[1];
                
                $data1->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') <= '".$end_date."'");
            }

            if(Auth::user()->role_id == 1){ //super admin
                
            }
            elseif(Auth::user()->role_id == 2){ //admin
                if(in_array(36,$users)) { //jacob
                    $users=[36,25,51,68,64,31,65,27];
                    $data1->wherein('sys_crm_deals.owner',$users); 
                } else {
                    $data1->wherein('sys_crm_deals.owner',$users);
                }
            }
            else{
                $data1->wherein('sys_crm_deals.owner',$users);
            }
            
            $dataA = $data1->get();
            $retAmount=0;
            if(count($dataA)>0){
                foreach($dataA as $dt){
                    $retAmount+= SysHelper::get_aed_amount_new($dt->deal_currency,$dt->deal_value);
                }
            }
            $revenue = SysHelper::com_curr_format($retAmount, 2, '.', ',');

            return $revenue;
                
        } catch (\Throwable $th) {
            return $th;
        }
    }
    public static function get_total_forcast_by_company($date, $company){
        try {
         $data1 = DB::table('sys_crm_deals')->select('deal_value','deal_currency','source','cust_id')
        ->where('sys_crm_deals.company_id',$company)
        ->wherein('stage',[1,2,3]);            
            if($date=="d"){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') = '".date('Y-m-d')."'");
            }
            if($date=="m"){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m') = '".date('Y-m')."'");
            }
            if($date=="y"){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y') = '".date('Y')."'");
            }
            if($date=="q"){
                $quarter = SysHelper::get_quarter(date('m'));
                $start_date = $quarter[0];
                $end_date = $quarter[1];            
                $data1->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') <= '".$end_date."'");
            }
            if($date=="pm"){
                $c_date = Carbon::createFromFormat('Y-m', date('Y-m'))->subMonth();  
                $pm_date = $c_date->format('Y-m');
                $data1->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m') = '".$pm_date."'");
            }
            if($date=="pq"){
                $quarter = SysHelper::get_pre_quarter(date('m'));
                $start_date = $quarter[0];
                $end_date = $quarter[1];
                
                $data1->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') <= '".$end_date."'");
            }

            if(Auth::user()->role_id == 1){ //super admin
                
            }
            elseif(Auth::user()->role_id == 2){ //admin
            }
            else{
            }
            
            $dataA = $data1->get();
            $retAmount=0;
            if(count($dataA)>0){
                foreach($dataA as $dt){
                    $retAmount+= SysHelper::get_aed_amount_new($dt->deal_currency,$dt->deal_value);
                }
            }
            $revenue = SysHelper::com_curr_format($retAmount, 2, '.', ',');

            return $revenue;
                
        } catch (\Throwable $th) {
            return $th;
        }
    }

    
    

}
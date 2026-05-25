<?php

namespace App;

use App\Http\Controllers\SysProfitAndLossAccountController;
use App\Models\PushSubscription;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use App\SystemNotification;
use App\StaffActivity;


class SysHelper extends Model
{
    public static function get_user_access()
    {
        //$role_id = Auth::user()->role_id;
        $role_id = 4;
        $data =  DB::table('sm_role_permissions')->select('module_link_id')->where('role_id',$role_id)->pluck('module_link_id');
        /*return response()->json([
            'status' => true,
            'data' => [
                "user_id" => $employee->user_id,
                "full_name" => $employee->full_name,
                "email" => $employee->email,
            ],
            'access_token' => 'none',
            'token_type' => 'Bearer'
        ]);*/
        return $data;
    }

    public static function user_wish_text()
    {
        //return strtotime('00:01', now());
        $time = Carbon::now('+04:00')->format('H:i');
        if($time >= '00:01' && $time <= '11:59'){
            return "Good Morning";
        }
        if($time >= '12:01' && $time <= '14:59'){
            return "Good Afternoon";
        }
        if($time >= '16:00' && $time <= '23:59'){
            return "Good Evening";
        }
        return "";
    }

    public static function get_quote_text()
    {
        $quote="";
        $daily_quote = SysDailyQuotes::where('date',date('Y-m-d'))->first();
        if(isset($daily_quote->quote)){
            $quote = $daily_quote->quote;
        } else{
            $daily_quote1 = SysDailyQuotes::where('date','<=',date('Y-m-d'))->orderby('date','desc')->first();
            if(isset($daily_quote1->quote)){
                $quote = $daily_quote1->quote;
            }
        }
        return $quote;
    }
    

    public static function get_data_by_role()
    {   //company_id $c1, created_by $c2

        $c1 = [0]; $c2 = [0];
        //1 SUPER ADMIN
        if(Auth::user()->role_id == 1){
            //$dt1=DB::table('sys_company')->pluck('id');
            $dt1=[session('logged_session_data.company_id')];
            $dt2=DB::table('sm_staffs')->pluck('user_id');
            $c1 = $dt1; $c2 = $dt2;
        }
        //2 ADMIN
        if(Auth::user()->role_id == 2){
            $dt1=[session('logged_session_data.company_id')];
            $dt2=DB::table('sm_staffs')->where('company_id',session('logged_session_data.company_id'))->pluck('user_id');
            $c1 = $dt1; $c2 = $dt2;
        }
        //3 Accounts
        if(Auth::user()->role_id == 3){
            $dt1=[session('logged_session_data.company_id')];
            $dt2=DB::table('sm_staffs')->where('company_id',session('logged_session_data.company_id'))->pluck('user_id');
            $c1 = $dt1; $c2 = $dt2;
        }
        //4 Billing
        if(Auth::user()->role_id == 4){
            $dt1=[session('logged_session_data.company_id')];
            $dt2=DB::table('sm_staffs')->where('company_id',session('logged_session_data.company_id'))->pluck('user_id');
            $c1 = $dt1; $c2 = $dt2;
        }
        //5 Sales
        if(Auth::user()->role_id == 5){
            $dt1=[session('logged_session_data.company_id')];
            $dt2=DB::table('sm_staffs')->where('company_id',session('logged_session_data.company_id'))->pluck('user_id');
            $c1 = $dt1; $c2 = $dt2;
        }
        //33 Sales and Support Engineer
        if(Auth::user()->role_id == 33){
            $dt1=[session('logged_session_data.company_id')];
            $dt2=DB::table('sm_staffs')->where('company_id',session('logged_session_data.company_id'))->pluck('user_id');
            $c1 = $dt1; $c2 = $dt2;
        }
        //6 Delivery Dept
        if(Auth::user()->role_id == 6){
            $dt1=[session('logged_session_data.company_id')];
            $dt2=DB::table('sm_staffs')->where('company_id',session('logged_session_data.company_id'))->pluck('user_id');
            $c1 = $dt1; $c2 = $dt2;
        }
        //8 Sales Department Head
        if(Auth::user()->role_id == 8){
            $dt1=[session('logged_session_data.company_id')];
            $dt2=DB::table('sm_staffs')->where('company_id',session('logged_session_data.company_id'))->pluck('user_id');
            $c1 = $dt1; $c2 = $dt2;
        }
        //9 Procurement Dept Head
        if(Auth::user()->role_id == 9){
            $dt1=[session('logged_session_data.company_id')];
            $dt2=DB::table('sm_staffs')->where('company_id',session('logged_session_data.company_id'))->pluck('user_id');
            $c1 = $dt1; $c2 = $dt2;
        }
        //10 Procurement & Receivable Dept
        if(Auth::user()->role_id == 10){
            $dt1=[session('logged_session_data.company_id')];
            $dt2=DB::table('sm_staffs')->where('company_id',session('logged_session_data.company_id'))->pluck('user_id');
            $c1 = $dt1; $c2 = $dt2;
        }
        
        //26 Marketing Dept
        if(Auth::user()->role_id == 26){
            $dt1=[session('logged_session_data.company_id')];
            $dt2=DB::table('sm_staffs')->where('company_id',session('logged_session_data.company_id'))->pluck('user_id');
            $c1 = $dt1; $c2 = $dt2;
        }
        //27 Accounts Dept Head
        if(Auth::user()->role_id == 27){
            $dt1=[session('logged_session_data.company_id')];
            $dt2=DB::table('sm_staffs')->where('company_id',session('logged_session_data.company_id'))->pluck('user_id');
            $c1 = $dt1; $c2 = $dt2;
        }
        //28 Accounts, Billing, Logistic Dept
        if(Auth::user()->role_id == 28){
            $dt1=[session('logged_session_data.company_id')];
            $dt2=DB::table('sm_staffs')->where('company_id',session('logged_session_data.company_id'))->pluck('user_id');
            $c1 = $dt1; $c2 = $dt2;
        }
        //29 Logistic Dept. Head
        if(Auth::user()->role_id == 29){
            $dt1=[session('logged_session_data.company_id')];
            $dt2=DB::table('sm_staffs')->where('company_id',session('logged_session_data.company_id'))->pluck('user_id');
            $c1 = $dt1; $c2 = $dt2;
        }
        //29 Logistic Dept. Head
        if(Auth::user()->role_id == 32){
            $dt1=[session('logged_session_data.company_id')];
            $dt2=DB::table('sm_staffs')->where('company_id',session('logged_session_data.company_id'))->pluck('user_id');
            $c1 = $dt1; $c2 = $dt2;
        }
        //29 Logistic Dept. Head
        if(Auth::user()->role_id == 31){
            $dt1=[session('logged_session_data.company_id')];
            $dt2=DB::table('sm_staffs')->where('company_id',session('logged_session_data.company_id'))->pluck('user_id');
            $c1 = $dt1; $c2 = $dt2;
        }
        //35 Lead conversion manger
        if(Auth::user()->role_id == 35){
            $dt1=[session('logged_session_data.company_id')];
            $dt2=DB::table('sm_staffs')->pluck('user_id');
            $c1 = $dt1; $c2 = $dt2;
        }
        
        
        $data = [$c1,$c2];
        return $data;
    }

    public static function get_company_access()
    {
        $role_id = Auth::user()->role_id;
        if($role_id == 1){
            $data = DB::table('sys_company')->pluck('id');
            return $data;
        }
        else{
            $data = explode(',', session('logged_session_data.company_access'));
            //$data = [Auth::user()->company_id];
        }
        return $data;
    }

    public static function get_staff_list()
    {
        $com_id=session('logged_session_data.company_id');
        $role_id = Auth::user()->role_id;
        if($role_id == 1){
            $com = DB::table('sys_company')->pluck('id');
            $staff= SmStaff::select('user_id','full_name')->wherein('company_id',$com)->get();
        }
        elseif($role_id == 2){         
            
      $staff = SmStaff::select('user_id', 'full_name')
    ->whereRaw("FIND_IN_SET(?, company_access)", [$com_id])
    ->get();

            
        }
        else{
           //$com = explode(',', session('logged_session_data.company_access'));
            //$staff= SmStaff::select('user_id','full_name')->wherein('company_id',$com)->get();
            $staff= SmStaff::select('user_id','full_name')->where('user_id',Auth::user()->id)->get();
        }
        return $staff;
    }

    //get sales person list for supplier customer
    public static function get_sales_persons()
    {
        $com_id=session('logged_session_data.company_id');
        if(Auth::user()->role_id == 5){
            $staffs = SmStaff::select('user_id','full_name')->where('active_status', '=', '1')
            ->wherein('role_id',[5,8,33])
            ->wherein('user_id',[Auth::user()->id])
            ->whereRaw("find_in_set($com_id,company_access)")->orderby('full_name','asc')->get();
        } else {
            if($com_id==1){
                $staffs = SmStaff::select('user_id','full_name')->where('active_status', '=', '1')
                ->wherein('role_id',[5,8,33])
                ->orderby('full_name','asc')->get();
            } else {
                $staffs = SmStaff::select('user_id','full_name')->where('active_status', '=', '1')
                ->wherein('role_id',[5,8,33])
                ->whereRaw("find_in_set($com_id,company_access)")->orderby('full_name','asc')->get();
            }
        }
        return $staffs;
    }
    public static function get_sales_persons4()
    {
        $com_id=session('logged_session_data.company_id');
        if(Auth::user()->role_id == 5){
            $staffs = SmStaff::select('user_id','full_name')->where('active_status', '=', '1')
            ->wherein('user_id',[Auth::user()->id])
            ->whereRaw("find_in_set($com_id,company_access)")->orderby('full_name','asc')->get();
        } else {
        $staffs = SmStaff::select('user_id','full_name')->where('active_status', '=', '1')
        ->wherein('role_id',[1,2,5,8])->orderby('full_name','asc')->get();
        }
        return $staffs;
    }
    //full staffs
    public static function get_sales_persons2()
    {
       
        return SmStaff::select(
            'user_id',
            DB::raw("CONCAT(first_name, ' ', last_name) as full_name")
        )
        ->where('active_status', '=', '1')
        ->wherein('role_id',[5,33,30,8,32])
        ->where('company_id', session('logged_session_data.company_id'))
        ->orderBy('first_name', 'asc')
        ->get();
    }
    //full staffs by company
    public static function get_sales_persons3()
    {
        return SmStaff::select(
            'user_id',
            DB::raw("CONCAT(first_name, ' ', last_name) as full_name")
        )
        ->where('active_status', '=', '1')
        ->wherein('role_id',[5,33,30,8,32])
        ->where('company_id', session('logged_session_data.company_id'))
        ->orderBy('first_name', 'asc')
        ->get();
    }
    //get sales person list for sales and purchase modules
    // public static function get_sales_persons_for_sales_purchase()
    // {
    //     $com_id=session('logged_session_data.company_id');
    //     $staffs = SmStaff::select('user_id','full_name')->where('active_status', '=', '1')
    //     ->wherenotin('role_id',[5,8])
    //     ->whereRaw("find_in_set($com_id,company_access)")->get();
    //     return $staffs;
    // }

    public static function get_company_names()
    {        
        if(!session('company_list.company')) {
            $list = DB::table('sys_company')->select('id','company_name')->orderby('sort_id', 'asc')->get();
            session()->put('company_list', ['company'=>$list]);
        }
        $list=session('company_list.company');
        if(count($list)) {
            $com_ids = SysHelper::get_company_access();
            return $list->wherein('id',$com_ids);
        }
    }
    
    public static function get_company_list($id)
    {        
        if(!session('company_list.company')) {
            $list = DB::table('sys_company')->select('id','company_name')->get();
            session()->put('company_list', ['company'=>$list]);
        }
        $list=session('company_list.company');
        if(count($list)) {
            return $list->wherein('id',$id)->pluck('company_name');
        }
    }
    
    public static function get_company_tax($id)
    {
        $vat = DB::table('sys_company')->select('net_vat')->where('id',$id)->first();
        if($vat->net_vat == ""){
            return 0;
        } else {
            return $vat->net_vat;
        }
    }
    public static function get_company_status($data)
    {
        $ret=1;
      
      
        if($data->customer_type != 7){
            if(strlen($data->vat_number) < 5){ $ret=0; }
        }

        if(strlen($data->mobile) < 9){ $ret=0; }
        if(strlen($data->email) < 5){ $ret=0; }
        if(strlen($data->first_name) < 1){ $ret=0; }
        if(strlen($data->contcat_number) < 8){ $ret=0; }

        
     

         $exists = SysCustSupplDoc::where('cust_suppl_id', $data->id)
                                                        ->where('doc_name', 'Trade License/Commercial Registration')
                                                              ->whereNull('deleted_at') // <-- only consider not deleted
                                                        ->exists();

       

    if(!$exists){
        $ret=0;
    }


        if($ret == 0){
            return 0;
        } else{
            return 1;
        }
    }


    public static function get_customer_incomplete_fields($data)
    {
        $errors = [];


        if ($data->customer_type != 7) {
            if (strlen($data->vat_number) < 5) {
                $errors[] = ['id' => 'vat_number', 'message' => 'VAT Number must be at least 5 characters'];
            }
        }

        if (strlen($data->mobile) < 9) {
            $errors[] = ['id' => 'mobile', 'message' => 'Mobile number is too short'];
        }
        if (strlen($data->email) < 5) {
            $errors[] = ['id' => 'email', 'message' => 'Email is invalid or too short'];
        }
        if (strlen($data->first_name) < 1) {
            $errors[] = ['id' => 'first_name', 'message' => 'Primary Contact First name must be at least 1 character'];
        }
        if (strlen($data->contcat_number) < 8) {
            $errors[] = ['id' => 'contact_number', 'message' => 'Customer Contact is too short'];
        }

        // ✅ Return structured result
        return [
            'status' => empty($errors) ? 1 : 0,
            'errors' => $errors
        ];
    }


    
    public static function get_company_account_id($id)
    {
        $vat = DB::table('sys_cust_suppl')->select('sys_chartofaccounts.id')
        ->join('sys_chartofaccounts','sys_chartofaccounts.account_code','sys_cust_suppl.code')
        ->where('sys_cust_suppl.id',$id)->first();
        if(isset($vat)){
            if($vat->id == ""){
                return 0;
            } else {
                return $vat->id;
            }
        }
        else {
            return 0;
        }
    }

    //for active account list
    public static function get_account_list()
    {
        $com_id = session('logged_session_data.company_id');
        $accounts = SysChartofAccounts::select('sys_chartofaccounts.id','sys_chartofaccounts.account_name','sys_chartofaccounts.group','sys_chartofaccounts.account_code')
        ->where('sys_chartofaccounts.status',1)
        ->whereRaw("find_in_set($com_id,sys_chartofaccounts.company_access)")->orderby('sys_chartofaccounts.account_name','asc')->get();
        return $accounts;
    }
    //for active account list if no main account if sub account excist
    public static function get_account_list2()
    {
        $com_id = session('logged_session_data.company_id');
        $accountslist = SysChartofAccounts::select('main_account_id')->where('main_account_id','!=',0)->whereRaw("find_in_set($com_id,sys_chartofaccounts.company_access)")->pluck('main_account_id');
        $accounts = SysChartofAccounts::select('sys_chartofaccounts.id','sys_chartofaccounts.account_name','sys_chartofaccounts.group','sys_chartofaccounts.account_code')
        ->where('sys_chartofaccounts.status',1)->wherenotin('id',$accountslist)
        ->whereRaw("find_in_set($com_id,sys_chartofaccounts.company_access)")->orderby('sys_chartofaccounts.account_name','asc')->get();
        return $accounts;
    }
    
    //for active and inactive account list
    public static function get_account_list_all()
    {
        $com_id = session('logged_session_data.company_id');
        $accounts = SysChartofAccounts::select('sys_chartofaccounts.id','sys_chartofaccounts.account_name','sys_chartofaccounts.group','sys_chartofaccounts.account_code')
        ->whereRaw("find_in_set($com_id,sys_chartofaccounts.company_access)")->where('status',1)->orderby('sys_chartofaccounts.account_name','asc')->get();
        return $accounts;
    }
    public static function get_account_list_all_noget()
    {
        $com_id = session('logged_session_data.company_id');
        $accounts = SysChartofAccounts::select('sys_chartofaccounts.id','sys_chartofaccounts.account_name','sys_chartofaccounts.group','sys_chartofaccounts.account_code')
        ->whereRaw("find_in_set($com_id,sys_chartofaccounts.company_access)")->orderby('sys_chartofaccounts.account_name','asc');
        return $accounts;
    }
    //for active and inactive account list if no main account if sub account excist
    public static function get_account_list_all2()
    {
        $com_id = session('logged_session_data.company_id');
        $accountslist = SysChartofAccounts::select('main_account_id')->where('main_account_id','!=',0)->whereRaw("find_in_set($com_id,sys_chartofaccounts.company_access)")->pluck('main_account_id');
        $accounts = SysChartofAccounts::select('sys_chartofaccounts.id','sys_chartofaccounts.account_name','sys_chartofaccounts.group','sys_chartofaccounts.account_code')
        ->whereRaw("find_in_set($com_id,sys_chartofaccounts.company_access)")->wherenotin('id',$accountslist)->orderby('sys_chartofaccounts.account_name','asc')->get();
        return $accounts;
    }
    
    //for active customer list
    public static function get_customer_list($com_ids)
    {
        $group = DB::table('sys_account_group_sub2')->select('id')->wherein('title',['Customers'])->first();
        if (!$group) {
            return collect();
        }

        $com_id = session('logged_session_data.company_id');
        $query = SysChartofAccounts::select('sys_chartofaccounts.id','sys_chartofaccounts.company_ship_to_id','sys_chartofaccounts.account_name','sys_chartofaccounts.account_code')
        ->where('sys_chartofaccounts.subgroup2',$group->id)->where('sys_chartofaccounts.status',1);
        $query->whereRaw("find_in_set($com_id,sys_chartofaccounts.company_access)");
        $customer = $query->orderby('sys_chartofaccounts.account_name','asc')->get();
        return $customer;
    }

    public static function get_customer_supplier_list($com_ids)
    {
        $group = DB::table('sys_account_group_sub2')->select('id')->wherein('title',['Customers'])->first();
        $group2 = DB::table('sys_account_group_sub2')->select('id')->wherein('title',['Suppliers'])->first();
        if (!$group || !$group2) {
            return collect();
        }

        $com_id = session('logged_session_data.company_id');
        $query = SysChartofAccounts::select('sys_chartofaccounts.id','sys_chartofaccounts.company_ship_to_id','sys_chartofaccounts.account_name','sys_chartofaccounts.account_code')
            ->wherein('sys_chartofaccounts.subgroup2',[$group->id,$group2->id])->where('sys_chartofaccounts.status',1);
        $query->whereRaw("find_in_set($com_id,sys_chartofaccounts.company_access)");
        $customer = $query->orderby('sys_chartofaccounts.account_name','asc')->get();
        return $customer;
    }

    //for active and inactive customer list
    public static function get_customer_list_all($com_ids)
    {
        $group = DB::table('sys_account_group_sub2')->select('id')->wherein('title',['Customers'])->first();
        $com_id = session('logged_session_data.company_id');
        if(!isset($group->id)){
            return collect();
        }
        $query = SysChartofAccounts::select('sys_chartofaccounts.id','sys_chartofaccounts.account_name','sys_chartofaccounts.account_code')
        ->where('sys_chartofaccounts.subgroup2',$group->id);
        $query->whereRaw("find_in_set($com_id,sys_chartofaccounts.company_access)");
        $customer = $query->orderby('sys_chartofaccounts.account_name','asc')->get();
        return $customer;
    }

    //for active supplier list
    public static function get_supplier_list($com_ids)
    {       
        $group = DB::table('sys_account_group_sub2')->select('id')->wherein('title',['Suppliers'])->first();
        $com_id = session('logged_session_data.company_id');

        if(!isset($group->id)){
            return [];
        }
        $query = SysChartofAccounts::select('sys_chartofaccounts.id','sys_chartofaccounts.account_name','sys_chartofaccounts.account_code')
        ->where('sys_chartofaccounts.subgroup2',$group->id)->where('sys_chartofaccounts.status',1);
        
        $query->whereRaw("find_in_set($com_id,sys_chartofaccounts.company_access)");
        $supplier = $query->orderby('sys_chartofaccounts.account_name','asc')->get();
        return $supplier;
    }
    public static function get_stl_supplier_list($com_ids)
    {       
        $group = DB::table('sys_account_group_sub2')->select('id')->wherein('title',['Suppliers'])->first();
        $com_id = session('logged_session_data.company_id');
        $query = SysChartofAccounts::select('sys_chartofaccounts.id','sys_chartofaccounts.account_name','sys_chartofaccounts.account_code')
        ->where('sys_chartofaccounts.subgroup2',$group->id)->where('sys_chartofaccounts.status',1)->where('sys_chartofaccounts.stl',1);
        $query->whereRaw("find_in_set($com_id,sys_chartofaccounts.company_access)");
        $supplier = $query->orderby('sys_chartofaccounts.account_name','asc')->get();
        return $supplier;
    }
    
    //for active and inactive supplier list
    public static function get_supplier_list_all($com_ids)
    {       
        $group = DB::table('sys_account_group_sub2')->select('id')->wherein('title',['Suppliers'])->first();
        $com_id = session('logged_session_data.company_id');
        if(!isset($group->id)){
            return [];
        }
        $query = SysChartofAccounts::select('sys_chartofaccounts.id','sys_chartofaccounts.account_name','sys_chartofaccounts.account_code')
        ->where('sys_chartofaccounts.subgroup2',$group->id);
        $query->whereRaw("find_in_set($com_id,sys_chartofaccounts.company_access)");
        $supplier = $query->orderby('sys_chartofaccounts.account_name','asc')->get();
        return $supplier;
    }
    //for active and inactive supplier list
    public static function get_customer_supplier_list_all($com_ids)
    {       
        $group = DB::table('sys_account_group_sub2')->select('id')->wherein('title',['Customers','Suppliers'])->first();
        $com_id = session('logged_session_data.company_id');
        $query = SysChartofAccounts::select('sys_chartofaccounts.id','sys_chartofaccounts.account_name','sys_chartofaccounts.account_code')
        ->where('sys_chartofaccounts.subgroup2',$group->id);
        $query->whereRaw("find_in_set($com_id,sys_chartofaccounts.company_access)")->where('sys_chartofaccounts.status',1);
        $supplier = $query->orderby('sys_chartofaccounts.account_name','asc')->get();
        return $supplier;
    }

    public static function get_account_id_from_cust_id($cust_id=0)
    {
        $query = DB::table('sys_cust_suppl as cs')->select('ca.id','ca.account_code','ca.account_name')
        ->join('sys_chartofaccounts as ca','ca.account_code','cs.code')->where('cs.id',$cust_id)->where('ca.status',1)->first();
        if(isset($query)){
            return $query->id;
        } else { return 0; }
    }

    //active cust_supplier list
    public static function get_customer_list_deal_lead()
    {
        $com_id = session('logged_session_data.company_id');
        $vendors_query = SysCustSuppl::select('id','code','name','customer_name_display')->where('catid',1); // 1 customers, 2 suppliers
        if(Auth::user()->role_id != 1 && Auth::user()->role_id != 2 && Auth::user()->role_id != 35){
            $sales_person = DB::table('sys_cust_suppl_assign')->select('cust_supp_id')->where('user_id',Auth::user()->id)->get();
            if(count($sales_person)>0){
                foreach($sales_person as $spid){
                    $sp[]=$spid->cust_supp_id;
                }
                $vendors_query->wherein('id', $sp);
            } else { $vendors_query->where('id', 0); }
        }
        $vendors_query->whereRaw("find_in_set($com_id,sys_cust_suppl.company_access)");
        $vendors = $vendors_query->wherein('status',[1,3])->orderby('name','asc')->get();
        return $vendors;
        // $com_id = session('logged_session_data.company_id');
        // $vendors_query = SysCustSuppl::select('id','code','name')->where('catid',1); // 1 customers, 2 suppliers
        // if(Auth::user()->role_id != 1 && Auth::user()->role_id != 2 && Auth::user()->role_id != 35){
        //     $sales_person = DB::table('sys_cust_suppl_assign')->select('cust_supp_id')->where('user_id',Auth::user()->id)->get();
        //     if(count($sales_person)>0){
        //         foreach($sales_person as $spid){
        //             $sp[]=$spid->cust_supp_id;
        //         }
        //         $vendors_query->wherein('id', $sp);
        //     } else { $vendors_query->where('id', 0); }
        // }
        // $vendors_query->whereRaw("find_in_set($com_id,sys_cust_suppl.company_access)");
        // $vendors = $vendors_query->wherein('status',[1,3])->orderby('name','asc')->get();
        // return $vendors;
    }



    
    public static function get_supplier_list_deal_lead()
    {
        $com_id = session('logged_session_data.company_id');
        $vendors_query = SysCustSuppl::select('id','code','name','customer_name_display')->where('catid',2); // 1 customers, 2 suppliers
        if(Auth::user()->role_id != 1 && Auth::user()->role_id != 2 && Auth::user()->role_id != 35){
            $sales_person = DB::table('sys_cust_suppl_assign')->select('cust_supp_id')->where('user_id',Auth::user()->id)->get();
            if(count($sales_person)>0){
                foreach($sales_person as $spid){
                    $sp[]=$spid->cust_supp_id;
                }
                $vendors_query->wherein('id', $sp);
            } else { $vendors_query->where('id', 0); }
        }
        $vendors_query->whereRaw("find_in_set($com_id,sys_cust_suppl.company_access)");
        $vendors = $vendors_query->wherein('status',[1,3])->orderby('name','asc')->get();
        return $vendors;
        // $com_id = session('logged_session_data.company_id');
        // $vendors_query = SysCustSuppl::select('id','code','name')->where('catid',1); // 1 customers, 2 suppliers
        // if(Auth::user()->role_id != 1 && Auth::user()->role_id != 2 && Auth::user()->role_id != 35){
        //     $sales_person = DB::table('sys_cust_suppl_assign')->select('cust_supp_id')->where('user_id',Auth::user()->id)->get();
        //     if(count($sales_person)>0){
        //         foreach($sales_person as $spid){
        //             $sp[]=$spid->cust_supp_id;
        //         }
        //         $vendors_query->wherein('id', $sp);
        //     } else { $vendors_query->where('id', 0); }
        // }
        // $vendors_query->whereRaw("find_in_set($com_id,sys_cust_suppl.company_access)");
        // $vendors = $vendors_query->wherein('status',[1,3])->orderby('name','asc')->get();
        // return $vendors;
    }


    

    public static function get_customer_list_deal_lead_all_role()
    {
        $com_id = session('logged_session_data.company_id');
        $vendors_query = SysCustSuppl::select('id','code','name','customer_name_display','taken_from_stock','stock_order')->where('catid',1)->whereRaw("find_in_set($com_id,sys_cust_suppl.company_access)"); // 1 customers, 2 suppliers
        //$vendors_query->whereRaw("find_in_set($com_id,sys_cust_suppl.company_access)");
        $vendors = $vendors_query->wherein('status',[1,3])->orderby('name','asc')->get();
    
        return $vendors;
        // $com_id = session('logged_session_data.company_id');
        // $vendors_query = SysCustSuppl::select('id','code','name')->where('catid',1); // 1 customers, 2 suppliers
        // //$vendors_query->whereRaw("find_in_set($com_id,sys_cust_suppl.company_access)");
        // $vendors = $vendors_query->wherein('status',[1,3])->orderby('name','asc')->get();
        // return $vendors;
    }
    // this is using view and edit pages (deal and lead)
    public static function get_customer_list_deal_lead_all()
    {
        $com_id = session('logged_session_data.company_id');
        $vendors_query = SysCustSuppl::select('id','code','name','customer_name_display')->where('catid',1); // 1 customers, 2 suppliers
        if(Auth::user()->role_id != 1 && Auth::user()->role_id != 2 && Auth::user()->role_id != 35){
            $sales_person = DB::table('sys_cust_suppl_assign')->select('cust_supp_id')->where('user_id',Auth::user()->id)->get();
            if(count($sales_person)>0){
                foreach($sales_person as $spid){
                    $sp[]=$spid->cust_supp_id;
                }
                $vendors_query->wherein('id', $sp);
            } else { $vendors_query->where('id', 0); }
        }
        $vendors_query->whereRaw("find_in_set($com_id,sys_cust_suppl.company_access)");
        $vendors = $vendors_query->wherein('status',[1,2,3])->orderby('name','asc')->get();
        return $vendors;
    }
    // this is using view and edit pages (deal and lead)
    public static function get_customer_list_deal_lead_no_company_access()
    {
        $com_id = session('logged_session_data.company_id');
        $vendors_query = SysCustSuppl::select('id','code','name')->where('catid',1); // 1 customers, 2 suppliers
        if(Auth::user()->role_id != 1 && Auth::user()->role_id != 2 && Auth::user()->role_id != 35){
            $sales_person = DB::table('sys_cust_suppl_assign')->select('cust_supp_id')->where('user_id',Auth::user()->id)->get();
            if(count($sales_person)>0){
                foreach($sales_person as $spid){
                    $sp[]=$spid->cust_supp_id;
                }
                $vendors_query->wherein('id', $sp);
            } else { $vendors_query->where('id', 0); }
        }
        //$vendors_query->whereRaw("find_in_set($com_id,sys_cust_suppl.company_access)");
        $vendors = $vendors_query->wherein('status',[1,2,3])->orderby('name','asc')->get();
        return $vendors;
    }

    //get customer supplier details from chart of accounts (accountant, contact, address)
 public static function get_customer_contact_detail($account_code)
{
    try {

        $cust = DB::table('sys_cust_suppl')
            ->where('code', $account_code)
            ->first();

        if(!$cust){
            return "Contact details not available";
        }

        $contacts = DB::table('sys_cust_suppl_contact')
            ->where('cust_suppl_id', $cust->id)
            ->get();

        if($contacts->count() == 0){
            return "Contact details not available";
        }

        // Prefer Accounts contact
        $contact = $contacts->first(function ($c) {
            return str_contains(strtolower($c->designation), 'account');
        });

        if(!$contact){
            $contact = $contacts->first();
        }

        $name = $contact->first_name.' '.$contact->last_name;
        $designation = $contact->designation ?? '';
        $mobile = $contact->mobile ?? '';
        $work = $contact->work_phone ?? '';
        $email = $contact->email_address ?? '';

        $phones = $mobile;
        if($work && $work != $mobile){
            $phones .= " | ".$work;
        }
$ret_val = '
<div style="background:#f8f9fc;
            padding:10px 14px;
            border-radius:6px;
            border:1px solid #e3e6f0;
            font-size:0.9rem;">

    <div style="font-weight:600;color:#2c3e50;margin-bottom:4px;">
        '.$name.' <span style="font-weight:400;color:#6c757d;">('.$designation.')</span>
    </div>

    <div style="display:flex;gap:15px;color:#444;font-size:0.85rem;flex-wrap:wrap;">

        <span>
            M: 
            <a href="tel:'.$mobile.'" style="text-decoration:none;color:#333;">
                '.$phones.'
            </a>
        </span>

        <span>
           E:
            <a href="mailto:'.$email.'" style="text-decoration:none;color:#333;">
                '.$email.'
            </a>
        </span>

    </div>

</div>';

        return $ret_val;

    } catch (\Throwable $th) {
        return "";
    }
}
    
    //get deal details for receivable outstanding
    public static function get_deal_detail_for_receivable_outstanding($invoice_doc_number)
    {
        try {
            $invoice = DB::table('sys_sales_invoice')->select('deal_id')->where('doc_number',$invoice_doc_number)->first();
            $deal = DB::table('sys_crm_deals as d')->select('d.id','d.code','s.full_name')
            ->join('sm_staffs as s','s.user_id','d.owner')
            ->where('d.id',$invoice->deal_id)->first();
            return $deal;
        } catch (\Throwable $th) {
            return "";
        }
    }
    public static function get_deal_track_detail_for_receivable_outstanding($invoice_doc_number)
    {
        try {
            $invoice = DB::table('sys_sales_invoice')->select('deal_id')->where('doc_number',$invoice_doc_number)->first();
            $deal = DB::table('sys_crm_deals as d')->select('d.id','d.code','s.full_name','t.id as track_id')
            ->join('sm_staffs as s','s.user_id','d.owner')
            ->leftjoin('sys_crm_deal_track as t','t.deal_id','d.id')
            ->where('d.id',$invoice->deal_id)->first();
            return $deal;
        } catch (\Throwable $th) {
            return "";
        }
    }
    public static function get_sales_invoice_details($invoice_doc_number)
    {
        try {
            $invoice = DB::table('sys_sales_invoice')->select('lpo_number')->where('doc_number',$invoice_doc_number)->first();
            return $invoice;
        } catch (\Throwable $th) {
            return "";
        }
    }
    //get deal details for payable outstanding
    public static function get_deal_detail_for_payable_outstanding($invoice_doc_number)
    {
        try {
            $invoice = DB::table('sys_purchase_invoice')->select('deal_id')->where('doc_number',$invoice_doc_number)->first();
            $deal = DB::table('sys_crm_deals as d')->select('d.id','d.code','s.full_name')
            ->join('sm_staffs as s','s.user_id','d.owner')
            ->where('d.id',$invoice->deal_id)->first();
            return $deal;
        } catch (\Throwable $th) {
            return "";
        }
    }
    public static function get_purchase_invoice_details($invoice_doc_number)
    {
        try {
            $invoice = DB::table('sys_purchase_invoice')->select('lpo_number','bill_number','bill_date')->where('doc_number',$invoice_doc_number)->first();
            return $invoice;
        } catch (\Throwable $th) {
            return "";
        }
    }

    public static function check_customer_is_added($company_name)
    {
        $check = DB::table('sys_cust_suppl')->select('id','code','name')->where('catid',1)
            ->where(function ($query) use ($company_name) {
                $query->orwhere('name','like','%'.$company_name.'%')
                ->orwhere('name','like','%'.str_replace( ',', '', $company_name).'%')
                ->orwhere('name','like','%'.str_replace( ',', ' ', $company_name).'%')
                ->orwhere('name','like','%'.str_replace( '.', '', $company_name).'%')
                ->orwhere('name','like','%'.str_replace( '.', ' ', $company_name).'%');
            })->count();
    }
    public static function check_supplier_is_added($company_name)
    {
        $check = DB::table('sys_cust_suppl')->select('id','code','name')->where('catid',2)
            ->where(function ($query) use ($company_name) {
                $query->orwhere('name','like','%'.$company_name.'%')
                ->orwhere('name','like','%'.str_replace( ',', '', $company_name).'%')
                ->orwhere('name','like','%'.str_replace( ',', ' ', $company_name).'%')
                ->orwhere('name','like','%'.str_replace( '.', '', $company_name).'%')
                ->orwhere('name','like','%'.str_replace( '.', ' ', $company_name).'%');                    
            })->count();
    }


    public static function check_customer_data_incompleate($id)
    {
        $data = DB::table('sys_cust_suppl')->where('id',$id)->first();
        if($data->vat_country == null || $data->vat_country == ''){ return false;}
        if($data->vat_percentage == null || $data->vat_percentage == ''){ return false;}
        if($data->customer_type == null || $data->customer_type == ''){ return false;}
        if($data->sale_type == null || $data->sale_type == ''){ return false;}
        if($data->vat_number == '.' || $data->vat_number == ''){ return false;}
        if($data->transaction_type == 0 || $data->transaction_type == '' || $data->transaction_type == '.'){ return false;}
        if($data->credit_limit == 0 || $data->credit_limit == '' || $data->credit_limit == '0.00'){ return false;}
        if($data->credit_days == 0 || $data->credit_days == ''){ return false;}
        if($data->payment_terms == 0 || $data->payment_terms == ''){ return false;}
        return true;
    }
    
    public static function check_supplier_data_incompleate($id)
    {
        $data = DB::table('sys_cust_suppl')->where('id',$id)->first();
        if($data->vat_country == null || $data->vat_country == ''){ return false;}
        if($data->vat_percentage == null || $data->vat_percentage == ''){ return false;}
        if($data->supplier_type == null || $data->supplier_type == ''){ return false;}
        if($data->purchase_type == null || $data->purchase_type == ''){ return false;}
        if($data->vat_number == '.' || $data->vat_number == ''){ return false;}
        if($data->transaction_type == 0 || $data->transaction_type == '' || $data->transaction_type == '.'){ return false;}
        if($data->credit_limit == 0 || $data->credit_limit == '' || $data->credit_limit == '0.00'){ return false;}
        if($data->credit_days == 0 || $data->credit_days == ''){ return false;}
        if($data->payment_terms == 0 || $data->payment_terms == ''){ return false;}
        return true;
    }
    
    public static function save_account_arabic_details($account_id, $company_name, $contact_person, $address)
    {
        $ar = SysCustSupDetailAr::where('account_id', $account_id)->count();
        if($ar == 0){
            SysCustSupDetailAr::insert([
                'account_id' => $account_id,
                'company_name_ar' => $company_name,
                'contact_person_ar' => $contact_person,
                'address_ar' => $address,
                'status' => 1,
                'created_by' => Auth::user()->id,
                'created_at' => Carbon::now('+04:00'),
                
            ]);
        } else{
            SysCustSupDetailAr::where('account_id', $account_id)->update([
                'account_id' => $account_id,
                'company_name_ar' => $company_name,
                'contact_person_ar' => $contact_person,
                'address_ar' => $address,
                'status' => 1,
                'updated_by' => Auth::user()->id,
                'updated_at' => Carbon::now('+04:00'),
            ]);
        }
    }
    
    public static function get_salesinvoice_deal_code($doc_no)
    {
        $data = DB::table('sys_sales_invoice as s')->select('d.code')
        ->leftjoin('sys_crm_deals as d','d.id','s.deal_id')->where('s.doc_number',$doc_no)->first();
        if(isset($data)){
            return $data->code;
        } else {
            return "";
        }
    }

    public static function get_product_list($company_id)
    {       
        $items = SmItem::select('id','part_number','description','coo','hscode','weight','product_type')->where('status',1)->where(
            function ($query) use ($company_id) {
                foreach ($company_id as $cid) {
                    $query->orWhereRaw("FIND_IN_SET($cid, company_id) > 0");
                }
            }
        )->orderby('part_number','ASC')->get();
        return $items;
    }
    public static function get_product_list_edit($company_id)
    {       
        $items = SmItem::select('id','part_number','description','coo','hscode','weight','product_type')->where('status',1)->where(
            function ($query) use ($company_id) {
                foreach ($company_id as $cid) {
                    $query->orWhereRaw("FIND_IN_SET($cid, company_id) > 0");
                }
            }
        )->orderby('part_number','ASC')->get();
        return $items;
    }
    public static function get_product_list_all_flelds($company_id)
    {       
        $items = SmItem::where('status',1)->where(
            function ($query) use ($company_id) {
                foreach ($company_id as $cid) {
                    $query->orWhereRaw("FIND_IN_SET($cid, company_id) > 0");
                }
            }
        )->orderby('part_number','ASC')->get();
        return $items;
    }

    

    public static function set_license_key_trn($type,$trn_id,$trm_date,$trn_doc_no,$key_id,$item_id,$license_key,$exp_date)
    {
        try {
            $exists = DB::table('sys_purchase_grn_license_key_trn')
            ->where('license_key', $license_key)
            ->where('key_id', $key_id)
            ->where('trn_id', $trn_id)
            ->where('item_id', $item_id)
            ->exists();
            if ($exists) { return ""; }

            DB::table('sys_purchase_grn_license_key_trn')->insert(
                [
                    'type' => $type,
                    'trn_id' => $trn_id,
                    'trm_date' => $trm_date,
                    'trn_doc_no' => $trn_doc_no,
                    'key_id' => $key_id,
                    'item_id' => $item_id,
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                    'company_id' => session('logged_session_data.company_id'),
                    'license_key' => $license_key,
                    'exp_date' => $exp_date,
                ]
            );            
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
    public static function get_user_detail($user_id)
    {
        return  DB::table('sm_staffs')->select('role_id','department_id','full_name','email','mobile')->where('user_id',$user_id)->first();
    }

    public static function minus_format($amt)
    {
        if(str_contains($amt,'-')){
            return "(".str_replace('-','',$amt).")";
        } else { return $amt; }
    }

    // get account chart of account ids start

    public static function get_opening_stock_account_id() {
        $opening_stock_account = SysChartofAccounts::select('id')->where('account_name','Opening Stock')->where('company_id',session('logged_session_data.company_id'))->first();
        if($opening_stock_account==""){
            return 0;
        } else{
            return $opening_stock_account->id;
        }
    }

    public static function get_purchase_account_id() {
        $purchase_account = SysChartofAccounts::select('id')->where('account_name','Purchase')->where('company_id',session('logged_session_data.company_id'))->first();
        if($purchase_account==""){
            return 0;
        } else{
            return $purchase_account->id;
        }
    }
    
    public static function get_purchase_return_account_id() {
        $purchase_account = SysChartofAccounts::select('id')->where('account_name','Purchase return')->where('company_id',session('logged_session_data.company_id'))->first();
        if($purchase_account==""){
            return 0;
        } else{
            return $purchase_account->id;
        }
    }

    public static function get_purchase_vat_account_id() {
        $purchase_vat_account = SysChartofAccounts::select('id')->where('account_name','VAT on Purchase')->where('company_id',session('logged_session_data.company_id'))->first();
        if($purchase_vat_account==""){
            return 0;
        } else{
            return $purchase_vat_account->id;
        }
    }

    public static function get_customs_freight_accounts_for_purchase($com_ids) {
        //Other Direct Expenses id 27
        $customs_freight_accounts = SysChartofAccounts::select('id','account_name','account_code')->wherein('company_id',$com_ids)->where('subgroup2',27)->where('status',1)->get();
        if(count($customs_freight_accounts) > 0){
            return $customs_freight_accounts;
        } else{
            return [];
        }
    }
    public static function get_customs_freight_accounts_for_sales($com_ids) {
     
        //Selling & Distributions Expenses id 29
        $customs_freight_accounts = SysChartofAccounts::select('id','account_name','account_code')->wherein('company_id',$com_ids)->where('subgroup2',29)->where('status',1)->get();
      
        if(count($customs_freight_accounts) > 0){
        
            return $customs_freight_accounts;
        } else{
            
            return [];
        }  
    }
    
    public static function get_sales_account_id() {
        $sales_account_id = SysChartofAccounts::select('id')->where('account_name','Sales')->where('company_id',session('logged_session_data.company_id'))->first();
        if($sales_account_id==""){
            return 0;
        } else{
            return $sales_account_id->id;
        }
    }

    public static function get_sales_return_account_id() {
        $sales_account_id = SysChartofAccounts::select('id')->where('account_name','Sales Return')->where('company_id',session('logged_session_data.company_id'))->first();
        if($sales_account_id==""){
            return 0;
        } else{
            return $sales_account_id->id;
        }
    }

    public static function get_sales_vat_account_id() {
        $sales_vat_account = SysChartofAccounts::select('id')->where('account_name','VAT on Sales')->where('company_id',session('logged_session_data.company_id'))->first();
        if($sales_vat_account==""){
            return 0;
        } else{
            return $sales_vat_account->id;
        }
    }

    public static function get_general_reserve_account_id() {
        $general_reserve_account = SysChartofAccounts::select('id')->where('account_name','General Reserve')->where('company_id',session('logged_session_data.company_id'))->first();
        if($general_reserve_account == ""){
            return 0;
        } else{
            return $general_reserve_account->id;
        }
    }
    
    public static function get_cash_account($status=null) {
        $cash_group_id = SysAccountGroupSub2::select('id')->where('title','cash')->where('status',1)->first();
        if($cash_group_id == ""){
            return [];
        } else{
            $query = SysChartofAccounts::select('id','account_name')->where('subgroup2',$cash_group_id->id)->where('company_id',session('logged_session_data.company_id'));
            if($status=="all"){ }
            else { $query->where('status',1); }
            $data = $query->get();
            return $data;
        }
    }
    
    public static function get_bank_account($status=null) {
        $bank_group_id = SysAccountGroupSub2::select('id')->where('title','bank')->where('status',1)->first();
        if($bank_group_id == ""){
            return collect();
        } else{
            $query = SysChartofAccounts::select('id','account_name')->where('subgroup2',$bank_group_id->id)->where('company_id',session('logged_session_data.company_id'));
            if($status=="all"){ }
            else { $query->where('status',1); }
            $data = $query->get();
            return $data;
        }
    }
    
    public static function get_stl_bank_account($status=null) {
        $bank_group_id = SysAccountGroupSub2::select('id')->where('title','bank')->where('status',1)->first();
        if($bank_group_id == ""){
            return collect();
        } else{
            $query = SysChartofAccounts::select('id','account_name')->where('subgroup2',$bank_group_id->id)->where('stl',1)->where('company_id',session('logged_session_data.company_id'));
            if($status=="all"){ }
            else { $query->where('status',1); }
            $data = $query->get();
            return $data;
        }
    }

    public static function get_avg_price($part_no, $to_date)
    {
        try {
            $stocklist = SysItemStock::select('sys_item_stock.doc_number','sys_item_stock.doc_date','sys_item_stock.refno','sys_item_stock.account_id','sys_item_stock.partno','sys_item_stock.description','sys_item_stock.qty_in','sys_item_stock.price_in','sys_item_stock.qty_out','sys_item_stock.price_out','sys_item_stock.deal_id','sys_item_stock.slno','sm_items.part_number')
                ->join('sm_items','sm_items.id','sys_item_stock.partno')
                ->whereRaw("DATE_FORMAT(sys_item_stock.doc_date, '%Y-%m-%d') <= '".$to_date."'")
                ->where('sm_items.id',$part_no)->where('sys_item_stock.status',1)
                ->where('sys_item_stock.company_id',session('logged_session_data.company_id'))
                ->orderby('sys_item_stock.doc_date','asc')
                ->get();
            //return $stocklist;

           $price_in_qty_in=0; $qty_in=0; $bal_qty=0; $avg_rate=0;

           if(count($stocklist)>0){
            
                foreach($stocklist as $value){
                    if($bal_qty <= 0){ $qty_in=0; $price_in_qty_in = 0; }                    
                    if(str_contains($value->doc_number,'SRT')) {
                        $qty_in += $value->qty_in;
                        $bal_qty += $value->qty_in;
                        $bal_qty -= $value->qty_out;
                    }
                    elseif(str_contains($value->doc_number,'OPS')) {
                        $qty_in += $value->qty_in;
                        $bal_qty += $value->qty_in;
                        $bal_qty -= $value->qty_out;
                    } else{
                        $price_in_qty_in += $value->price_in*$value->qty_in;
                        $qty_in += $value->qty_in;
                        $bal_qty += $value->qty_in;
                        $bal_qty -= $value->qty_out;
                        if($qty_in !=0){
                            $avg_rate = $price_in_qty_in/$qty_in;
                        }                        
                    }
                }
            }
            return $avg_rate;
                                    
        } catch (\Throwable $th) {
            return 0;
        }
    }

    /**
     * Compute avg_rate for a part to match the StockLedger footer exactly.
     *
     * Mirrors the StockLedger's two-phase logic:
     *   Phase 1 – Opening balance (pre-period, no CFC):
     *     All transactions up to Dec 31 of the year preceding $to_date are rolled
     *     up using the same simple-average algorithm used by get_stock_ledger_opening_stock().
     *   Phase 2 – In-period running WACC (with CFC):
     *     Transactions from Jan 1 of $to_date's year through $to_date use the full
     *     running WACC, with freight + custom charges added to GR/PI incoming rates,
     *     matching appendCfcToIncomingRate() in the StockLedger controller.
     */
    public static function get_stock_register_ledger_avg_rate($partNoId, $to_date, $companyId = null)
    {
        try {
            $partNoId = (int) $partNoId;
            $toCarbon = Carbon::parse($to_date);
            $toYmd    = $toCarbon->format('Y-m-d');
            $fromYmd  = $toCarbon->copy()->startOfYear()->format('Y-m-d');          // Jan 1 of $to_date's year
            $opbDate  = $toCarbon->copy()->startOfYear()->subDay()->format('Y-m-d'); // Dec 31 of prior year

      
            if ($companyId) {
                $companyIds = [$companyId];
            } else {
            
                $r = self::get_data_by_role();
            
                $companyIds = $r[0] ?? [];
            
                if (!is_array($companyIds)) {
                    $companyIds = [$companyIds];
                }
            }
                if (!is_array($companyIds)) {
                    $companyIds = [$companyIds];
                }
            $companyIds = array_values(array_filter($companyIds, function ($x) {
                return $x !== null && $x !== '' && $x !== 0;
            }));
            if (count($companyIds) === 0 && session()->has('logged_session_data.company_id')) {
                $companyIds = [session('logged_session_data.company_id')];
            }
            if (count($companyIds) === 0) {
                return 0.0;
            }

            $parseAmount = function ($v) {
                if ($v === null || $v === '') return 0.0;
                if (is_int($v) || is_float($v)) return (float) $v;
                if (is_numeric($v)) return (float) $v;
                $s = str_replace([',', ' '], '', trim((string) $v));
                return $s === '' ? 0.0 : (float) $s;
            };

            // ── Phase 1: Opening balance (same simple-average as get_stock_ledger_opening_stock) ──
            $opbList = SysItemStock::select(
                'sys_item_stock.doc_number',
                'sys_item_stock.qty_in',
                'sys_item_stock.price_in',
                'sys_item_stock.qty_out'
            )
                ->join('sm_items', 'sm_items.id', 'sys_item_stock.partno')
                ->whereRaw("DATE_FORMAT(sys_item_stock.doc_date, '%Y-%m-%d') <= ?", [$opbDate])
                ->where('sm_items.id', $partNoId)
                ->where('sys_item_stock.status', 1)
                ->whereIn('sys_item_stock.company_id', $companyIds)
                ->orderBy('sys_item_stock.doc_date', 'desc')
                ->get();

            $opbBalQty       = 0.0;
            $opbQtyIn        = 0.0;
            $opbPriceInQtyIn = 0.0;
            $opbAvgRate      = 0.0;
            foreach ($opbList as $row) {
                if ($opbBalQty <= 0) { $opbQtyIn = 0.0; $opbPriceInQtyIn = 0.0; }
                if (str_contains((string) ($row->doc_number ?? ''), 'SRT')) {
                    $opbQtyIn  += $parseAmount($row->qty_in);
                    $opbBalQty += $parseAmount($row->qty_in);
                    $opbBalQty -= $parseAmount($row->qty_out);
                } else {
                    $opbPriceInQtyIn += $parseAmount($row->price_in) * $parseAmount($row->qty_in);
                    $opbQtyIn        += $parseAmount($row->qty_in);
                    $opbBalQty       += $parseAmount($row->qty_in);
                    $opbBalQty       -= $parseAmount($row->qty_out);
                    if ($opbQtyIn != 0) {
                        $opbAvgRate = $opbPriceInQtyIn / $opbQtyIn;
                    }
                }
            }

            // Seed the running WACC from the opening balance
            $bal_qty             = (float) $opbBalQty;
            $avg_rate_value      = (float) $opbAvgRate;
            $running_stock_value = $bal_qty * $avg_rate_value;
            $displayAvgRateValue = $avg_rate_value;

            // ── Phase 2: CFC maps for in-period GR / PI lines ───────────────────
            $grnCfcMap = [];
            $grnCfcRows = DB::table('sys_purchase_grn_items as gi')
                ->join('sys_purchase_grn as grn', 'grn.id', '=', 'gi.grn_id')
                ->select(
                    'grn.doc_number',
                    'gi.id as line_item_id',
                    DB::raw('SUM(IFNULL(gi.qty,0)) as qty'),
                    DB::raw('SUM(IFNULL(gi.value, IFNULL(gi.unitprice,0)*IFNULL(gi.qty,0))) as line_value'),
                    DB::raw('SUM(IFNULL(gi.discount,0)) as discount'),
                    DB::raw('SUM(IFNULL(gi.fright,0)) as fright'),
                    DB::raw('SUM(IFNULL(gi.customcharges,0)) as customcharges')
                )
                ->where('gi.part_no', $partNoId)
                ->where('gi.status', 1)
                ->where('grn.status', 1)
                ->groupBy('grn.doc_number', 'gi.id')
                ->get();
            foreach ($grnCfcRows as $row) {
                $qty = (float) ($row->qty ?? 0);
                if ($qty <= 0) continue;
                $base   = (float) ($row->line_value ?? 0) - (float) ($row->discount ?? 0);
                $extras = (float) ($row->fright ?? 0) + (float) ($row->customcharges ?? 0);
                // Store under both lookup keys (by line-item id and by part id fallback)
                $grnCfcMap['li:' . (int) $row->line_item_id] = ($base + $extras) / $qty;
                $grnCfcMap[(string) $row->doc_number . '|li:' . (int) $row->line_item_id] = ($base + $extras) / $qty;
            }

            $piCfcMap = [];
            $piCfcRows = DB::table('sys_purchase_invoice_items as pii')
                ->join('sys_purchase_invoice as pi', 'pi.id', '=', 'pii.pi_id')
                ->select(
                    'pi.doc_number',
                    'pii.id as line_item_id',
                    DB::raw('SUM(IFNULL(pii.qty,0)) as qty'),
                    DB::raw('SUM(IFNULL(pii.value, IFNULL(pii.unitprice,0)*IFNULL(pii.qty,0))) as line_value'),
                    DB::raw('SUM(IFNULL(pii.discount,0)) as discount'),
                    DB::raw('SUM(IFNULL(pii.fright,0)) as fright'),
                    DB::raw('SUM(IFNULL(pii.customcharges,0)) as customcharges')
                )
                ->where('pii.part_number', $partNoId)
                ->where('pii.status', 1)
                ->where('pi.status', 1)
                ->groupBy('pi.doc_number', 'pii.id')
                ->get();
            foreach ($piCfcRows as $row) {
                $qty = (float) ($row->qty ?? 0);
                if ($qty <= 0) continue;
                $base   = (float) ($row->line_value ?? 0) - (float) ($row->discount ?? 0);
                $extras = (float) ($row->fright ?? 0) + (float) ($row->customcharges ?? 0);
                $piCfcMap['li:' . (int) $row->line_item_id] = ($base + $extras) / $qty;
                $piCfcMap[(string) $row->doc_number . '|li:' . (int) $row->line_item_id] = ($base + $extras) / $qty;
            }

            // ── Phase 2: In-period transactions (Jan 1 → to_date) with WACC + CFC ──
            $list = SysItemStock::select(
                'sys_item_stock.doc_number',
                'sys_item_stock.item_id',
                'sys_item_stock.qty_in',
                'sys_item_stock.price_in',
                'sys_item_stock.qty_out',
                'sys_item_stock.price_out',
                'prt.ref_company_id as prt_reference'
            )
                ->join('sm_items', 'sm_items.id', 'sys_item_stock.partno')
                ->leftJoin('sys_purchase_return as prt', DB::raw('prt.doc_number COLLATE utf8mb4_unicode_ci'), DB::raw('sys_item_stock.doc_number COLLATE utf8mb4_unicode_ci'))
                ->whereRaw("DATE_FORMAT(sys_item_stock.doc_date, '%Y-%m-%d') >= ?", [$fromYmd])
                ->whereRaw("DATE_FORMAT(sys_item_stock.doc_date, '%Y-%m-%d') <= ?", [$toYmd])
                ->where('sm_items.id', $partNoId)
                ->where('sys_item_stock.status', 1)
                ->where('sm_items.status', 1)
                ->whereIn('sys_item_stock.company_id', $companyIds)
                ->orderBy('sys_item_stock.doc_date', 'asc')
                ->orderByRaw('CASE WHEN IFNULL(sys_item_stock.qty_in,0) > 0 THEN 0 ELSE 1 END ASC')
                ->orderBy('sys_item_stock.slno', 'asc')
                ->orderBy('sys_item_stock.id', 'asc')
                ->get();

            foreach ($list as $value) {
                $previousAvgRateValue = $avg_rate_value;

                $docRaw     = strtoupper(trim((string) ($value->doc_number ?? '')));
                $docFirst   = trim(explode(',', $docRaw)[0]);
                $docPrefix2 = substr($docFirst, 0, 2);

                $lineQtyIn    = $parseAmount($value->qty_in    ?? 0);
                $lineQtyOut   = $parseAmount($value->qty_out   ?? 0);
                $linePriceIn  = $parseAmount($value->price_in  ?? 0);
                $linePriceOut = $parseAmount($value->price_out ?? 0);

                $hasPrtRef = isset($value->prt_reference)
                    && $value->prt_reference !== null
                    && trim((string) $value->prt_reference) !== '';

                $isSalesReturn    = ($docPrefix2 === 'SR') || str_contains($docFirst, 'SRT');
                $isPurchaseReturn = ($docPrefix2 === 'PR') || ($hasPrtRef && $lineQtyOut > 0);

                // Apply CFC landed-cost adjustment for GR / PI incoming lines.
                // Mirror appendCfcToIncomingRate(): try line-item key first, then doc+li key.
                if (!$isSalesReturn && !$isPurchaseReturn && $lineQtyIn > 0) {
                    $lineItemId = (int) ($value->item_id ?? 0);
                    $liKey      = 'li:' . $lineItemId;
                    $docLiKey   = $docFirst . '|li:' . $lineItemId;
                    if ($docPrefix2 === 'GR') {
                        if ($lineItemId > 0 && isset($grnCfcMap[$docLiKey])) {
                            $linePriceIn = (float) $grnCfcMap[$docLiKey];
                        } elseif ($lineItemId > 0 && isset($grnCfcMap[$liKey])) {
                            $linePriceIn = (float) $grnCfcMap[$liKey];
                        }
                    } elseif ($docPrefix2 === 'PI') {
                        if ($lineItemId > 0 && isset($piCfcMap[$docLiKey])) {
                            $linePriceIn = (float) $piCfcMap[$docLiKey];
                        } elseif ($lineItemId > 0 && isset($piCfcMap[$liKey])) {
                            $linePriceIn = (float) $piCfcMap[$liKey];
                        }
                    }
                }

                if ($isSalesReturn) {
                    $running_stock_value += $lineQtyIn  * $previousAvgRateValue;
                    $running_stock_value -= $lineQtyOut * $previousAvgRateValue;
                } elseif ($isPurchaseReturn) {
                    $returnCostOut = $linePriceOut > 0 ? $linePriceOut : $previousAvgRateValue;
                    $running_stock_value += $lineQtyIn  * $linePriceIn;
                    $running_stock_value -= $lineQtyOut * $returnCostOut;
                } else {
                    $running_stock_value += $lineQtyIn  * $linePriceIn;
                    $running_stock_value -= $lineQtyOut * $previousAvgRateValue;
                }

                $bal_qty += $lineQtyIn;
                $bal_qty -= $lineQtyOut;

                if ($bal_qty > 0) {
                    $avg_rate_value      = $running_stock_value / $bal_qty;
                    $displayAvgRateValue = $avg_rate_value;
                } elseif ($bal_qty == 0.0) {
                    $displayAvgRateValue = $previousAvgRateValue;
                    $avg_rate_value      = 0;
                    $running_stock_value = 0;
                } else {
                    $displayAvgRateValue = $previousAvgRateValue;
                    $avg_rate_value      = 0;
                    $running_stock_value = 0;
                }
            }

            return (float) $displayAvgRateValue;
        } catch (\Throwable $th) {
            return 0.0;
        }
    }

    public static function get_group_qty($part_no)
    {
        try {
            $balance_qty=0;
            $data = DB::table('sys_item_stock as stock')
            ->select(DB::raw('COALESCE(SUM(stock.qty_in) - SUM(stock.qty_out), 0) as balance_qty'))
            ->where('stock.status', 1)
            ->where('stock.doc_number', 'not like', 'SRN%')
            ->where('stock.partno', $part_no)
            ->where('stock.company_id', session('logged_session_data.company_id'))
            ->groupby('stock.company_id')
            ->get();
            if(count($data)){
                foreach($data as $dt){
                    if($dt->balance_qty > 0){
                        $balance_qty += $dt->balance_qty;
                    }
                }
            }
            return $balance_qty;
        } catch (\Throwable $th) {
            return 0;
        }
    }
    
    public static function get_stock_ledger_opening_stock($part_number,$opb_date,$company_id)
    {
        try {
            $opb_bal_qty = 0; $stocklist_opb=0;  $opb_qty_in=0; $opb_price_in_qty_in=0; $opb_avg_rate=0;
            $stocklist_opb = SysItemStock::select('sys_item_stock.doc_number','sys_item_stock.doc_date','sys_item_stock.refno','sys_item_stock.account_id','sys_item_stock.partno','sys_item_stock.description','sys_item_stock.qty_in','sys_item_stock.price_in','sys_item_stock.qty_out','sys_item_stock.price_out','sys_item_stock.deal_id','sys_item_stock.slno','sm_items.part_number')
            ->join('sm_items','sm_items.id','sys_item_stock.partno')
            ->whereRaw("DATE_FORMAT(sys_item_stock.doc_date, '%Y-%m-%d') <= ?", [$opb_date])
            ->where('sm_items.part_number',$part_number)->where('sys_item_stock.status',1)
            //->where('doc_number', 'not like', 'SRT%')
            ->wherein('sys_item_stock.company_id',$company_id)
            ->orderby('sys_item_stock.doc_date','desc')
            ->get();
            //return $opb_date;
            if(count($stocklist_opb)>0){
                foreach($stocklist_opb as $value){
                    if($opb_bal_qty <= 0){ $opb_qty_in=0; $opb_price_in_qty_in = 0; }
                    if(str_contains($value->doc_number,'SRT')){
                        $opb_qty_in += $value->qty_in;
                        $opb_bal_qty += $value->qty_in;
                        $opb_bal_qty -= $value->qty_out;
                    } else{                   

                        $opb_price_in_qty_in += $value->price_in*$value->qty_in;
                        $opb_qty_in += $value->qty_in;
                        $opb_bal_qty += $value->qty_in;
                        $opb_bal_qty -= $value->qty_out;
                        if($opb_qty_in !=0){
                            $opb_avg_rate = SysHelper::com_curr_format($opb_price_in_qty_in/$opb_qty_in,2,'.',',');
                        }
                    }
                }
            }
            return [$opb_bal_qty,$opb_avg_rate];
                
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public static function get_inventory_report($part_number, $to_date, $company_id, $doc_no, $deal_id, $acc_id, $sales_person,$ageing, $user_id=null)
    {
        try {
            $check = DB::table('sys_inventory_report')->where(['cart_id' => session('logged_session_data.cart_id'), 'user_id' => Auth::user()->id, 'company_id' => session('logged_session_data.company_id')])->count();
            if($check > 0){
                $ret_data = DB::table('sys_inventory_report')->where(['cart_id' => session('logged_session_data.cart_id'), 'user_id' => Auth::user()->id, 'company_id' => session('logged_session_data.company_id')]);
                if($doc_no != ""){
                    $ret_data->where('doc_number',$doc_no);
                }
                if($deal_id != ""){
                    $ret_data->where('deal_code',$deal_id);
                }
                if($acc_id != ""){
                    $ret_data->where('account_id',$acc_id);
                }
                if($sales_person != ""){
                    $ret_data->where('sales_person_id',$sales_person);
                }
                if($ageing != ""){
                    if($ageing==0){
                        $ret_data->where('days_m0','!=',0);
                    }
                    if($ageing==30){
                        $ret_data->where('days_m30','!=',0);
                    }
                    if($ageing==60){
                        $ret_data->where('days_m60','!=',0);
                    }
                    if($ageing==90){
                        $ret_data->where('days_m90','!=',0);
                    }
                    if($ageing==120){
                        $ret_data->where('days_m120','!=',0);
                    }
                    if($ageing==121){
                        $ret_data->where('days_m121','!=',0);
                    }
                }
            return $ret_data->get();
            }


            $stockledgerlist_query = SysItemStock::select('sys_item_stock.doc_number','sys_item_stock.doc_date','sys_item_stock.refno','sys_item_stock.account_id','sys_item_stock.partno','sys_item_stock.description','sys_item_stock.qty_in','sys_item_stock.price_in','sys_item_stock.qty_out','sys_item_stock.price_out','sys_item_stock.deal_id','sys_item_stock.slno','sm_items.part_number','sm_staffs.full_name','sys_item_stock.sales_person'
            /*'salesman1.full_name as delivery_salesman','salesman2.full_name as return_salesman',
            'salesman3.full_name as grn_salesman','salesman4.full_name as purchase_return_salesman'*/
            )
            ->join('sm_items','sm_items.id','sys_item_stock.partno')
            /*
            ->leftJoin('sys_delivery_note', 'sys_delivery_note.id', '=', 'sys_item_stock.dln_id')
            ->leftJoin('sys_purchase_grn', 'sys_purchase_grn.id', '=', 'sys_item_stock.grn_id')
            ->leftJoin('sys_purchase_return', 'sys_purchase_return.id', '=', 'sys_item_stock.pri_id')
            ->leftJoin('sys_sales_return', 'sys_sales_return.id', '=', 'sys_item_stock.slr_id')
            ->leftJoin('sm_staffs as salesman1', 'salesman1.user_id', '=', 'sys_delivery_note.salesman')
            ->leftJoin('sm_staffs as salesman2', 'salesman2.user_id', '=', 'sys_sales_return.sales_man')
            ->leftJoin('sm_staffs as salesman3', 'salesman3.user_id', '=', 'sys_purchase_grn.sales_person')
            ->leftJoin('sm_staffs as salesman4', 'salesman4.user_id', '=', 'sys_purchase_return.sales_person')
            */
            ->leftJoin('sm_staffs', 'sm_staffs.user_id', '=', 'sys_item_stock.sales_person');

            if($user_id != null)
            {
                $stockledgerlist_query->where('sys_item_stock.sales_person',$user_id);
            }
            $stockledgerlist = $stockledgerlist_query->whereRaw("DATE_FORMAT(sys_item_stock.doc_date, '%Y-%m-%d') <= '".$to_date."'")
            ->wherein('sm_items.part_number',$part_number)->where('sys_item_stock.status',1)
            ->wherein('sys_item_stock.company_id',$company_id)
            ->orderby('sys_item_stock.partno','asc')->orderby('sys_item_stock.doc_date','asc')
            ->get();
            //return  $stockledgerlist->count();

            DB::table('sys_inventory_report')->where('cart_id', session('logged_session_data.cart_id'))->delete();
            DB::table('sys_inventory_report_test')->where('cart_id', session('logged_session_data.cart_id'))->delete();
            
            if(count($stockledgerlist)>0){
                $lastPartNo = null;
                foreach ($stockledgerlist as $li) {

                    if ($li->partno !== $lastPartNo) {
                        $price_in_qty_in = 0;
                        $qty_in = 0;
                        $bal_qty = 0;
                        $avg_rate = 0;            
                        $lastPartNo = $li->partno;
                    }

                    if($bal_qty <= 0){ $qty_in=0; $price_in_qty_in = 0; }
                    $price_out = (!empty($li->price_out) || $li->price_out === 0) ? $li->price_out : 0;

                    if(str_contains($li->doc_number,'SRT')){
                        $qty_in += $li->qty_in;
                        $bal_qty += $li->qty_in;
                        $bal_qty -= $li->qty_out;
                    } else {
                        $price_in_qty_in += $li->price_in*$li->qty_in;
                        $qty_in += $li->qty_in;
                        $bal_qty += $li->qty_in;
                        $bal_qty -= $li->qty_out;
                        if($qty_in !=0){
                        $avg_rate = $price_in_qty_in/$qty_in;}
                    }
                    if($bal_qty>0 && $li->qty_in > 0){
                        $adj_in_qty=$bal_qty;

                    } else {
                        $adj_in_qty=0;
                    }

                    $full_name="";
                    if($li->full_name != ""){   
                        $full_name = $li->full_name;
                    }
                    /*if($li->delivery_salesman != ""){   
                        $full_name = $li->delivery_salesman;
                    }
                    if($li->return_salesman != ""){
                        $full_name = $li->return_salesman;
                    }
                    if($li->grn_salesman != ""){
                        $full_name = $li->grn_salesman;
                    }
                    if($li->purchase_return_salesman != ""){
                        $full_name = $li->purchase_return_salesman;
                    }*/
                    $data[]=[
                        'cart_id' => session('logged_session_data.cart_id'),
                        'user_id' => Auth::user()->id,
                        'partno' => $li->partno,
                        'part_number' => $li->part_number,
                        'doc_date' => $li->doc_date,
                        'doc_number' => $li->doc_number,
                        'refno' => $li->refno,
                        'deal_id' => @$li->deal_id,
                        'deal_code' => @$li->deal_code->code,
                        'account_name' => @$li->accountname->account_name,
                        'qty_in' => $li->qty_in,
                        'price_in' => $li->price_in,
                        'qty_out' => $li->qty_out,
                        'price_out' => $price_out,
                        'bal_qty' => $bal_qty,
                        'avg_rate' => $avg_rate,
                        'full_name' => $full_name,
                        'company_id' => session('logged_session_data.company_id'),
                        'status' => 1,
                        'profit' => 0.00,
                        'profit_p' => 0.00,
                        'adj_in_qty' => $adj_in_qty,
                        'adj_out_qty' => 0,
                        'adj_status' => 0,
                        'account_id' => $li->account_id,
                        'sales_person_id' => $li->sales_person,
                    ];
                }
                foreach (array_chunk($data,1000) as $dt) {
                    DB::table('sys_inventory_report')->insert($dt);
                }
                
                $setdata = DB::table('sys_inventory_report')->where(['cart_id' => session('logged_session_data.cart_id'), 'user_id' => Auth::user()->id, 'company_id' => session('logged_session_data.company_id')]);
                $data1 = $setdata;
                $partno = $data1->pluck('partno')->unique();
                $partno = array_values($partno->toArray());
               
                if(count($partno)>0){
                    for ($i=0; $i < count($partno); $i++) {
                        $out = DB::table('sys_inventory_report')->where(['cart_id' => session('logged_session_data.cart_id'), 'user_id' => Auth::user()->id, 'company_id' => session('logged_session_data.company_id')])->where('partno',$partno[$i])->where('qty_out','>',0)->get();
                        if(count($out)>0){
                            $out_id = $out[0]->id;
                            $out_rate = $out[0]->avg_rate;
                            $in = DB::table('sys_inventory_report')->where(['cart_id' => session('logged_session_data.cart_id'), 'user_id' => Auth::user()->id, 'company_id' => session('logged_session_data.company_id')])->where('partno', $partno[$i])->where('bal_qty', '>', 0)->where('adj_status', 0)->where('id', '<=', $out_id)->orderby('id','desc')->get();
                            if(count($in)>0){
                                for ($j=1; $j < count($in); $j++) {
                                    DB::table('sys_inventory_report')->where('id',$in[$j]->id)->where(['cart_id' => session('logged_session_data.cart_id'), 'user_id' => Auth::user()->id, 'company_id' => session('logged_session_data.company_id')])
                                    ->update(['avg_rate' => $out_rate,'adj_in_qty' => DB::raw('qty_in')]);
                                    //->update(['adj_status' => 1]);
                                }
                            }

                            $in2 = DB::table('sys_inventory_report')->where(['cart_id' => session('logged_session_data.cart_id'), 'user_id' => Auth::user()->id, 'company_id' => session('logged_session_data.company_id')])->where('partno', $partno[$i])->where('adj_status', 0)->where('id', '>', $out_id+1)->orderby('id','desc')->get();
                            if(count($in2)>0){
                                for ($k=0; $k < count($in2); $k++) {
                                    DB::table('sys_inventory_report')->where('id',$in2[$k]->id)->where(['cart_id' => session('logged_session_data.cart_id'), 'user_id' => Auth::user()->id, 'company_id' => session('logged_session_data.company_id')])->update(['adj_in_qty' => DB::raw('qty_in')]);
                                }
                            }
                        }
                    }
                }

                if(count($partno)>0){
                    for ($i=0; $i < count($partno); $i++) {

                        $out_data = DB::table('sys_inventory_report')->where(['cart_id' => session('logged_session_data.cart_id'), 'user_id' => Auth::user()->id, 'company_id' => session('logged_session_data.company_id')])->where('partno',$partno[$i])->where('qty_out','>',0)->where('adj_status',0)->get();
                        if(count($out_data)>0){
                            foreach ($out_data as $out_val) {
                                //return $out_val->id;
                                $ret = SysHelper::set_inventory_report_profit($out_val->id,$out_val->qty_out,$out_val->price_out,$partno[$i],$out_val->doc_number);
                                //if($ret != ""){ return $ret;}
                            }
                        }
                        
                    }
                }

                $setdaya = DB::table('sys_inventory_report')->select('id','doc_date')->where(['cart_id' => session('logged_session_data.cart_id'), 'user_id' => Auth::user()->id, 'company_id' => session('logged_session_data.company_id')])->get();
                if(count($setdaya)>0){
                    foreach($setdaya as $d){
                        $d_ret = SysHelper::set_inventory_report_moving_avg($d->id,$d->doc_date);
                        DB::table('sys_inventory_report')->where('id',$d->id)->update([
                            'days_m0' => $d_ret[0],
                            'days_m30' => $d_ret[1],
                            'days_m60' => $d_ret[2],
                            'days_m90' => $d_ret[3],
                            'days_m120' => $d_ret[4],
                            'days_m121' => $d_ret[5],
                    ]);
                    }
                }
                
                $ret_data = DB::table('sys_inventory_report')->where(['cart_id' => session('logged_session_data.cart_id'), 'user_id' => Auth::user()->id, 'company_id' => session('logged_session_data.company_id')]);
                    if($doc_no != ""){
                        $ret_data->where('doc_number',$doc_no);
                    }
                    if($deal_id != ""){
                        $ret_data->where('deal_code',$deal_id);
                    }
                    if($acc_id != ""){
                        $ret_data->where('account_id',$acc_id);
                    }
                    if($sales_person != ""){
                        $ret_data->where('sales_person_id',$sales_person);
                    }
                    if($ageing != ""){
                        if($ageing==0){
                            $ret_data->where('days_m0','!=',0);
                        }
                        if($ageing==30){
                            $ret_data->where('days_m30','!=',0);
                        }
                        if($ageing==60){
                            $ret_data->where('days_m60','!=',0);
                        }
                        if($ageing==90){
                            $ret_data->where('days_m90','!=',0);
                        }
                        if($ageing==120){
                            $ret_data->where('days_m120','!=',0);
                        }
                        if($ageing==121){
                            $ret_data->where('days_m121','!=',0);
                        }
                    }
                return $ret_data->get();

            } else {
                return "";
            }                
        } catch (\Throwable $th) {
            return $th;
        }
    }

    // public static function set_inventory_report_profit($id,$qty,$price,$partno)
    // {
    //     try {
    //         $in_data = DB::table('sys_inventory_report')->where(['cart_id' => session('logged_session_data.cart_id'), 'user_id' => Auth::user()->id, 'company_id' => session('logged_session_data.company_id')])->where('partno',$partno)->where('qty_in','>',0)->where('adj_status',0)->orderby('id','asc')->get();
    //         if(count($in_data)>0){
    //             foreach ($in_data as $in_val) {
    //                 $stock_qty = abs($in_val->adj_in_qty);
    //                 if($qty == $stock_qty){
    //                     $profit = ($price - $in_val->avg_rate) * $qty;
    //                     $profit_p = ($profit / ($price * $qty))*100;
    //                     //sys_inventory_report_test                     
    //                     DB::table('sys_inventory_report_test')->insert(['in_id' => $in_val->id, 'out_id' => $id, 'price' => $price, 'rate' => $in_val->avg_rate, 'qty' => $qty,'line_no' => 1096]);

    //                     DB::table('sys_inventory_report')->where('id',$id)->update(['adj_out_qty' => $qty, 'adj_status' => 1,'profit'=>$profit,'profit_p'=>$profit_p]);
    //                     DB::table('sys_inventory_report')->where('id',$in_val->id)->update(['adj_in_qty' => 0, 'adj_status' => 1]);
    //                     return 0;
    //                 } elseif($qty < $stock_qty){
    //                     $profit = ($price - $in_val->avg_rate) * $qty;
    //                     $profit_p = ($profit / ($price * $qty))*100;
    //                     //sys_inventory_report_test                     
    //                     DB::table('sys_inventory_report_test')->insert(['in_id' => $in_val->id, 'out_id' => $id, 'price' => $price, 'rate' => $in_val->avg_rate, 'qty' => $qty,'line_no' => 1105]);

    //                     DB::table('sys_inventory_report')->where('id',$id)->update(['adj_out_qty' => $qty, 'adj_status' => 1,'profit'=>$profit,'profit_p'=>$profit_p]);
    //                     DB::table('sys_inventory_report')->where('id',$in_val->id)->update(['adj_in_qty' => $stock_qty-$qty]);                    
    //                     DB::table('sys_inventory_report')->where('id',$in_val->id)->where('adj_in_qty', 0)->update(['adj_status' => 1]);
    //                     return "";
    //                 }
    //                 elseif($qty > $stock_qty){
    //                     $profit = ($price - $in_val->avg_rate) * $stock_qty;
    //                     $profit_p = ($profit / ($price * $stock_qty))*100;
    //                     //sys_inventory_report_test                     
    //                     DB::table('sys_inventory_report_test')->insert(['in_id' => $in_val->id, 'out_id' => $id, 'price' => $price, 'rate' => $in_val->avg_rate, 'qty' => $stock_qty,'line_no' => 1116]);

    //                     $profit2 = $profit;
    //                     $profit_p2 = $profit_p;
    //                     DB::table('sys_inventory_report')->where('id',$in_val->id)->update(['adj_in_qty' => 0, 'adj_status' => 1]);
                        
    //                     $balance_qty_to_sell = $qty - $stock_qty;
    //                     while ($balance_qty_to_sell > 0) {
    //                         $in_data2 = DB::table('sys_inventory_report')->where(['cart_id' => session('logged_session_data.cart_id'), 'user_id' => Auth::user()->id, 'company_id' => session('logged_session_data.company_id')])->where('partno',$partno)->where('qty_in','>',0)->where('adj_status',0)->orderby('id','asc')->get();
    //                         if(count($in_data2)>0){
    //                             foreach ($in_data2 as $in_val2) {
    //                                 $stock_qty2 = $in_val2->adj_in_qty;
    //                                 if($balance_qty_to_sell == $stock_qty2){
    //                                     $profit = ($price - $in_val2->avg_rate) * $balance_qty_to_sell;
    //                                     $profit_p = ($profit / ($price * $balance_qty_to_sell))*100;
    //                                     //sys_inventory_report_test                     
    //                                     DB::table('sys_inventory_report_test')->insert(['in_id' => $in_val2->id, 'out_id' => $id, 'price' => $price, 'rate' => $in_val2->avg_rate, 'qty' => $balance_qty_to_sell,'line_no' => 1132]);

    //                                     $profit2 += $profit;
    //                                     $profit_p2 += $profit_p;
    //                                     DB::table('sys_inventory_report')->where('id',$in_val2->id)->update(['adj_in_qty' => 0, 'adj_status' => 1]);
    //                                     $balance_qty_to_sell = $balance_qty_to_sell-$stock_qty2;
    //                                     if ($balance_qty_to_sell <= 0) {
    //                                         DB::table('sys_inventory_report')->where('id',$id)->update(['adj_out_qty' => $qty, 'adj_status' => 1,'profit'=>$profit2,'profit_p'=>$profit_p2]);
    //                                         DB::table('sys_inventory_report')->where('id',$in_val->id)->where('adj_in_qty', 0)->update(['adj_in_qty' => 0, 'adj_status' => 1]);
    //                                         break;
    //                                     }
    //                                 }
    //                                 elseif($balance_qty_to_sell < $stock_qty2){
    //                                     $profit = ($price - $in_val2->avg_rate) * $balance_qty_to_sell;
    //                                     $profit_p = ($profit / ($price * $balance_qty_to_sell))*100;
    //                                     //sys_inventory_report_test                     
    //                                     DB::table('sys_inventory_report_test')->insert(['in_id' => $in_val2->id, 'out_id' => $id, 'price' => $price, 'rate' => $in_val2->avg_rate, 'qty' => $balance_qty_to_sell,'line_no' => 1148]);

    //                                     $profit2 += $profit;
    //                                     $profit_p2 += $profit_p;
    //                                     DB::table('sys_inventory_report')->where('id',$in_val2->id)->update(['adj_in_qty' => $stock_qty2-$balance_qty_to_sell]);
    //                                     $balance_qty_to_sell = $balance_qty_to_sell-$balance_qty_to_sell;
    //                                     if ($balance_qty_to_sell <= 0) {
    //                                         DB::table('sys_inventory_report')->where('id',$id)->update(['adj_out_qty' => $qty, 'adj_status' => 1,'profit'=>$profit2,'profit_p'=>$profit_p2]);
    //                                         DB::table('sys_inventory_report')->where('id',$in_val->id)->where('adj_in_qty', 0)->update(['adj_in_qty' => 0, 'adj_status' => 1]);
    //                                         break;
    //                                     }
    //                                 }
    //                                 if ($balance_qty_to_sell <= 0) {
    //                                     DB::table('sys_inventory_report')->where('id',$id)->update(['adj_out_qty' => $qty, 'adj_status' => 1,'profit'=>$profit2,'profit_p'=>$profit_p2]);
    //                                     DB::table('sys_inventory_report')->where('id',$in_val->id)->where('adj_in_qty', 0)->update(['adj_in_qty' => 0, 'adj_status' => 1]);
    //                                     break;
    //                                 }
    //                             }
    //                         }
    //                     }
    //                     return "";                        
    //                 }
    //             }
    //         }
    //         return "";
    //     } catch (\Throwable $th) {
    //         return $th;
    //     }
    // }

    public static function set_inventory_report_profit($id, $qty, $price, $partno,$out_doc)
    {
        try {
            $cart_id = session('logged_session_data.cart_id');
            $user_id = Auth::user()->id;
            $company_id = session('logged_session_data.company_id');
    
            $in_data = DB::table('sys_inventory_report')
                ->where([
                    'cart_id' => $cart_id,
                    'user_id' => $user_id,
                    'company_id' => $company_id
                ])
                ->where('partno', $partno)
                ->where('qty_in', '>', 0)
                ->where('adj_status', 0)
                ->orderBy('id', 'asc')
                ->get();
    
            if ($in_data->isEmpty()) {
                return "No stock available";
            }
    
            $balance_qty_to_sell = $qty;
            $total_profit = 0;
            $total_profit_percentage = 0;
    
            foreach ($in_data as $in_val) {
                if ($balance_qty_to_sell <= 0) break;
    
                $stock_qty = abs($in_val->adj_in_qty);
                $sell_qty = min($balance_qty_to_sell, $stock_qty);
    
                // Calculate profit
                $profit = ($price - $in_val->avg_rate) * $sell_qty;
                $profit_p = ($price * $sell_qty) != 0 ? ($profit / ($price * $qty)) * 100 : 0;
    
                // Accumulate profits
                $total_profit += $profit;
                $total_profit_percentage += $profit_p;
    
                // Insert transaction record
                DB::table('sys_inventory_report_test')->insert([
                    'in_id' => $in_val->id,
                    'out_id' => $id,
                    'price' => $price,
                    'rate' => $in_val->avg_rate,
                    'qty' => $sell_qty,
                    'line_no' => 1000 + $in_val->id,
                    'doc_date' => $in_val->doc_date,
                    'cart_id' => $cart_id,
                    'user_id' => $user_id,
                    'in_doc' => $in_val->doc_number,
                    'out_doc' => $out_doc,
                ]);
    
                // Update inventory stock
                $new_stock_qty = $stock_qty - $sell_qty;
                DB::table('sys_inventory_report')->where('id', $in_val->id)
                    ->update(['adj_in_qty' => $new_stock_qty, 'adj_status' => ($new_stock_qty == 0 ? 1 : 0)]);
    
                // Reduce remaining balance
                $balance_qty_to_sell -= $sell_qty;
            }
    
            // Update outgoing inventory report
            DB::table('sys_inventory_report')->where('id', $id)
                ->update([
                    'adj_out_qty' => $qty,
                    'adj_status' => 1,
                    'profit' => $total_profit,
                    'profit_p' => ($total_profit/($price*$qty)*100)
                ]);
    
            return "Inventory adjusted successfully";
    
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public static function set_inventory_report_moving_avg($id, $out_date)
    {
        try {
            $val1 = 0;
            $val2 = 0;
            $ret = 0;
            $outDate = Carbon::parse($out_date);
            $data = DB::table('sys_inventory_report_test')->where('out_id', $id)->orderBy('id', 'asc')->get();
            if ($data->isNotEmpty()) {
                foreach ($data as $value) {
                    $qty = $value->qty;
                    $inDate = Carbon::parse($value->doc_date);
                    $days = $inDate->diff($outDate)->days * ($inDate->gt($outDate) ? -1 : 1);
                    $val1 += $qty*$days;
                    $val2 += $days;
                }
                if($val2 != 0){
                    $ret = round($val1/$val2,2);
                }
                if ($ret < 0) {
                    return [$ret,0,0,0,0,0];
                } elseif ($ret >= 0 && $days <= 30) {
                    return [0,$ret,0,0,0,0];
                } elseif ($ret >= 31 && $days <= 60) {
                    return [0,0,$ret,0,0,0];
                } elseif ($ret >= 61 && $days <= 90) {
                    return [0,0,0,$ret,0,0];
                } elseif ($ret >= 91 && $days <= 120) {
                    return [0,0,0,0,$ret,0];
                } else {
                    return [0,0,0,0,0,$ret];
                }

                
            } else {

                return [0,0,0,0,0,0];
            }
        } catch (\Throwable $th) {
            return [0,0,0,0,0,0];
        }
    }

    public static function get_ledger_data_formated($transaction_no, $account_id,$data)
    {
        $amt = "";
        $rem = "";
        $dt1 =  $data->where('transaction_no',$transaction_no)->where('account_id',$account_id);
        if(count($dt1)>1){
            $dt =  $data->where('transaction_no',$transaction_no)->where('account_id',$account_id)->wherenotin('id',session('gl_data.id'));
            foreach($dt as $d){
                if($amt ==""){
                    $amt = $d->debit_amount;
                    $rem = $d->remarks;
                    $existingIds = session('gl_data.id', []);
                    $existingIds[] = $d->id;
                    session()->put('gl_data.id', $existingIds);

                }
            }
            //return session('gl_data.remark');
            if($amt){
                return [$amt, $rem];
            }
            else{
                return [0,0];
            }

            //session()->put('gl_data', ['remark' => $from_date]);

            //if(session('gl_data')){
            //    $from_date = session('gl_data.remark');
            //}
            //remarks

        }else{        
            $amt =  $data->where('transaction_no',$transaction_no)->where('account_id',$account_id)->max('debit_amount');        
            $rem =  $data->where('transaction_no',$transaction_no)->where('account_id',$account_id)->max('remarks');
            if($amt){
                return [$amt, $rem];
            }
            else{
                return [0,0];
            }
        }        
    }
    public static function get_ledger_data_formated_jv_receipt_payment($transaction_no, $account_id,$data)
    {
        try {
        $amt = "";
        $amt2 = "";
        $rem = "";
        $acc = "";
        $dt1 =  $data->where('transaction_no',$transaction_no)->where('account_id',$account_id);        
        $acc =  $data->where('transaction_no',$transaction_no)->where('is_main_account',1)->max('account_name');
        if(count($dt1)>1){
            $dt =  $data->where('transaction_no',$transaction_no)->where('account_id',$account_id)->wherenotin('id',session('gl_data_jv.id'));
            foreach($dt as $d){
                if($amt ==""){
                    $amt = $d->debit_amount;
                    $amt2 = $d->credit_amount;
                    $rem = $d->remarks;
                    $existingIds = session('gl_data_jv.id', []);
                    $existingIds[] = $d->id;
                    session()->put('gl_data_jv.id', $existingIds);

                }
            }
            //return session('gl_data.remark');
            if($amt){
                return [$amt, $rem, $amt2, $acc];
            }
            else{
                return [0,0,0,0];
            }

            //session()->put('gl_data', ['remark' => $from_date]);

            //if(session('gl_data')){
            //    $from_date = session('gl_data.remark');
            //}
            //remarks

        }else{
            //$acc =  $data->where('transaction_no',$transaction_no)->where('account_id',$account_id)->max('account_name');
            $amt =  $data->where('transaction_no',$transaction_no)->where('account_id',$account_id)->max('debit_amount');
            $amt2 =  $data->where('transaction_no',$transaction_no)->where('account_id',$account_id)->max('credit_amount');
            $rem =  $data->where('transaction_no',$transaction_no)->where('account_id',$account_id)->max('remarks');
            if($amt){
                return [$amt, $rem, $amt2,$acc];
            }
            else{
                return [0,0,0,0];
            }
        }
        } catch (\Throwable $th) {
            return $th;
        }  
    }
    public static function get_ledger_data_formated_bank_book($transaction_no, $account_id,$data)
    {
        $amt = "";
        $rem = "";
        $acc = "";
        $dt1 =  $data->where('transaction_no',$transaction_no)->where('account_id',$account_id);
        if(count($dt1)>1){
            $dt =  $data->where('transaction_no',$transaction_no)->where('account_id',$account_id)->wherenotin('id',session('gl_data.id'));
            foreach($dt as $d){
                if($amt ==""){
                    $amt = $d->debit_amount;
                    $rem = $d->remarks;

                    if($d->is_main_account==0){
                        $acc = $data->where('transaction_no',$transaction_no)->where('is_main_account',1)->max('account_name');
                    }else {
                        $acc = $d->account_name;
                    }

                    $existingIds = session('gl_data.id', []);
                    $existingIds[] = $d->id;
                    session()->put('gl_data.id', $existingIds);

                }
            }
            //return session('gl_data.remark');
            if($amt){
                return [$amt, $rem, $acc];
            }
            else{
                return [0,0,0];
            }

            //session()->put('gl_data', ['remark' => $from_date]);

            //if(session('gl_data')){
            //    $from_date = session('gl_data.remark');
            //}
            //remarks

        }else{        
            $amt =  $data->where('transaction_no',$transaction_no)->where('account_id',$account_id)->max('debit_amount');        
            $rem =  $data->where('transaction_no',$transaction_no)->where('account_id',$account_id)->max('remarks');
            $acc =  $data->where('transaction_no',$transaction_no)->where('account_id',$account_id)->max('account_name');
            if($amt){
                return [$amt, $rem, $acc];
            }
            else{
                return [0,0,0];
            }
        }
        
    }

    public static function get_ledger_data($data, $account_id)
    {
        try {
            $process1 =$data;
            $process2 =$data;
            $process3 =$data;
            $acc_det = $process1->where('account_id',$account_id)->first();
            $is_main_account = $acc_det->is_main_account;
            $accountid = $acc_det->account_id;        

            if($is_main_account == 0 && $accountid == $account_id){
                $accountname = $process2->where('is_main_account',1)->first();
                $amt = $process3->where('account_id',$account_id)->first();
                $data=[
                    'account_id' => $account_id,
                    'account_name' => $accountname->account_name,
                    'transaction_no' => $data[0]->transaction_no,
                    'transaction_date' => $data[0]->transaction_date,
                    'debit_amount' => $amt->debit_amount,
                    'credit_amount' => $amt->credit_amount,
                    'entry_no' => $data[0]->entry_no,
                    'remarks' => $data[0]->remarks,
                    'transaction_id' => $data[0]->transaction_id,
                    ];
                return $data;
            }
            else if($is_main_account == 1 && $accountid == $account_id){
                $bdata = $process2->where('account_id','!=',$account_id);
                foreach($bdata as $dt) {
                    $data=array(
                        'account_id' => $account_id,
                        'account_name' => $dt->account_name,
                        'transaction_no' => $dt->transaction_no,
                        'transaction_date' => $dt->transaction_date,
                        'debit_amount' => $dt->debit_amount,
                        'credit_amount' => $dt->credit_amount,
                        'entry_no' => $dt->entry_no,
                        'remarks' => $dt->remarks,
                        'transaction_id' => $dt->transaction_id,
                    );
                }
                return $data;
            }
            
        } catch (\Throwable $th) {
            return 0;
        }
        /*$data[]=[
            'account_id' => $res2->account_id,
            'account_name' => $account_name,
            'transaction_no' => $res2->transaction_no,
            'transaction_date' => $res2->transaction_date,
            'debit_amount' => $amt_debit,
            'credit_amount' => $amt_credit,
            'entry_no' => $res2->entry_no,
            'remarks' => $res2->remarks,
            ];*/
    }

    public static function get_supplier_group($id) {
        //Group = Liabilities 2
        //Sub Group = Current liabilities 12
        //Sub Group 2 = Supplier 19
        if($id == 'group'){ return 2; }
        if($id == 'subgroup'){ return 12; }
        if($id == 'subgroup2'){ return 19; }        
    }

    public static function get_customer_group($id) {
        //Head = Assets 1
        //Group = Current Assets 2
        //Sub Group = Customer 7
        if($id == 'group'){ return 1; }
        if($id == 'subgroup'){ return 2; }
        if($id == 'subgroup2'){ return 7; }        
    }

    // get account chart of account ids end

    public static function ledger_merge_account($account_id) {
        $p_account_id = SysHelper::get_purchase_account_id();
        $p_ret_account_id = SysHelper::get_purchase_return_account_id();
        $p_vat_account_id = SysHelper::get_purchase_vat_account_id();
        $s_account_id = SysHelper::get_sales_account_id();
        $s_ret_account_id = SysHelper::get_sales_return_account_id();
        $s_vat_account_id = SysHelper::get_sales_vat_account_id();

        if(($account_id != $p_account_id) && ($account_id != $p_ret_account_id) && ($account_id != $s_account_id) && ($account_id != $s_ret_account_id) && ($account_id != $p_vat_account_id) && ($account_id != $s_vat_account_id)){
            return true;
        } else {
            return false;
        }
    }

    public static function ledger_merge_account_notvat($account_id) {
        $p_account_id = SysHelper::get_purchase_account_id();
        $p_ret_account_id = SysHelper::get_purchase_return_account_id();
        $s_account_id = SysHelper::get_sales_account_id();
        $s_ret_account_id = SysHelper::get_sales_return_account_id();

        if(($account_id == $p_account_id) || ($account_id == $p_ret_account_id) || ($account_id == $s_account_id) || ($account_id == $s_ret_account_id)){
            return true;
        } else {
            return false;
        }
    }
    public static function ledger_merge_account_vat($account_id) {
        $p_vat_account_id = SysHelper::get_purchase_vat_account_id();
        $s_vat_account_id = SysHelper::get_sales_vat_account_id();

        if(($account_id == $p_vat_account_id) || ($account_id == $s_vat_account_id)){
            return true;
        } else {
            return false;
        }
    }
    
    public static function get_deal_track_grn_status($deal_track_id)
    {
        $data = DB::table('sys_crm_deal_track_approval_purchease_grn')->select('id','status','grn_no')->where('deal_track_id',$deal_track_id)->orderby('id','desc')->first();
        if($data != ""){
            if($data->status == 1){ return "<p class='my-1 mb-1'><b>GRN No</b> : <b>".$data->grn_no."</b></p>";}
            if($data->status == 2){ return "<p class='my-1 mb-1'><b>GRN</b> : <span class=btn-danger btn-badge py-1 px-2>&nbsp;Disapproved&nbsp;</span></p>";}
            if($data->status == 3){ return "<p class='my-1 mb-1'><b>GRN</b> : <span class=btn-info btn-badge py-1 px-2>&nbsp;Partial Approved&nbsp;</span></p>";}
            if($data->status == 0){                
                if(Auth::user()->id == 74) {
                    return "<p class='my-1 mb-1'><b>GRN</b> : <a data-toggle='modal' data-target='#ModalGRN' onclick='set_no(".$data->id.")' class='btn btn-success btn-sm p-0 pl-1 pr-1'>Update</a></p>";}
                else {
                    return "<p class='my-1 mb-1'><b>GRN</b> : <span class=btn-warning btn-badge py-1 px-2>&nbsp;Pending&nbsp;</span></p>";}
            }
        } else {
            return "";
        }
    }

    public static function is_approval_access(){
        $userarray=[1,2];
        if(in_array(Auth::user()->id, $userarray))
        {
            return true;
        }
        return false;
    }
    
    public static function is_return_approval_access(){
        $userarray=[1,18,60,4];
        if(in_array(Auth::user()->id, $userarray))
        {
            return true;
        }
        return false;
    }

// deal track approval access start
// Accounts Status (Head of Account Dept) 27/ (Accounts, Billing, Logistic Dept)28
// Sales Status (Head of Sales Dept)8
// Purchase Status (Head of Procurement Dept)9 /(Accounts, Billing, Logistic Dept)28
// SIV (Billing)4  /(Accounts, Billing, Logistic Dept)28
// Delivery (Head of Logistic Dep)29  /(Accounts, Billing, Logistic Dept)28
// Receivables Status (Accounts)3 /(Accounts, Billing, Logistic Dept)28

public static function account_approval_access(){
    $userarray=[27,28,1,2];
    if(in_array(Auth::user()->role_id, $userarray))
    {
        return true;
    }
    return false;
}
public static function sales_approval_access(){
    $userarray=[8,1,2];
    if(in_array(Auth::user()->role_id, $userarray))
    {
        return true;
    }
    return false;
}
public static function purchase_approval_access(){
    $userarray=[9,28,1,2];
    if(in_array(Auth::user()->role_id, $userarray))
    {
        return true;
    }
    return false;
}
public static function invoice_approval_access(){
    $userarray=[4,28,1,2];
    if(in_array(Auth::user()->role_id, $userarray))
    {
        return true;
    }
    return false;
}
public static function delivery_approval_access(){
    $userarray=[29,28,1,2];
    if(in_array(Auth::user()->role_id, $userarray))
    {
        return true;
    }
    return false;
}
public static function receivables_approval_access(){
    $userarray=[27,3,28,1,2];
    if(in_array(Auth::user()->role_id, $userarray))
    {
        return true;
    }
    return false;
}
public static function professional_service_approval_access(){
    $userarray=[1,2,32];
    if(in_array(Auth::user()->role_id, $userarray))
    {
        return true;
    }
    return false;
}
// deal track approval access end

    // get deal vat amount
    public static function get_deal_vat_amount($deal_id, $quote_id){
        $t_vatamount=0;
        $quoteitems = DB::table('sys_crm_quote_items')->select('sys_crm_quote_items.*')
        ->where('deal_id',$deal_id)->where('quote_id',$quote_id)->orderby('sort_id','ASC')->get();
        if(count($quoteitems)>0){
            foreach($quoteitems as $Item){                    
                $value = $Item->price * $Item->qty;
                $taxableamount = $value - $Item->discount;
                $vatamount = $taxableamount * $Item->vat / 100;
                $t_vatamount += $vatamount;
            }
        }
        return $t_vatamount;
    }

    
    
    public static function get_quarter($month){
        if($month >= 1 && $month <= 3)
        {
            return [date('Y-01-01'),date('Y-03-31'),];
        }
        if($month >= 4 && $month <= 6)
        {
            return [date('Y-04-01'),date('Y-06-30'),];
        }
        if($month >= 7 && $month <= 9)
        {
            return [date('Y-07-01'),date('Y-09-30'),];
        }
        if($month >= 10 && $month <= 12)
        {
            return [date('Y-10-01'),date('Y-12-31'),];
        }
    }
    public static function get_pre_quarter($month){
        if($month >= 1 && $month <= 3)
        {
            $currentYear = date('Y');
            $previousYear = $currentYear - 1;
            return [date($previousYear.'-10-01'),date($previousYear.'-12-31'),];
        }
        if($month >= 4 && $month <= 6)
        {
            return [date('Y-01-01'),date('Y-03-31'),];
        }
        if($month >= 7 && $month <= 9)
        {
            return [date('Y-04-01'),date('Y-06-30'),];
        }
        if($month >= 10 && $month <= 12)
        {
            return [date('Y-07-01'),date('Y-09-30'),];
        }
    }

    public static function com_curr_format($amount, $decim , $dots, $separator){
        
        $decimal = session('logged_session_data.decimal_point');
        if($separator == ""){
            return number_format($amount, $decimal, '.', '');
        } else {
            return number_format($amount, $decimal, '.', ',');
        }
    }

    public static function currancy_format_deal($amount,$company_id){
        //if($company_id==6)
        //{
         //   return SysHelper::com_curr_format($amount, 3, '.', ',');
        //}
        return SysHelper::com_curr_format($amount,'','',',');
    }
    public static function currancy_format_deal_no($amount,$company_id){
        if($company_id==6)
        {
            return SysHelper::com_curr_format($amount, 3, '.', '');
        }
        return SysHelper::com_curr_format($amount, 2, '.', '');
    }
    public static function currancy_format($amount, $currancy){
        if($currancy==3)
        {
            return SysHelper::com_curr_format($amount, 3, '.', ',');
        }
        return SysHelper::com_curr_format($amount, 2, '.', ',');
    }
    public static function currancy_format_textbox($amount, $currancy){
        if($currancy==3)
        {
            return SysHelper::com_curr_format($amount, 3, '.', '');
        }
        return SysHelper::com_curr_format($amount, 2, '.', '');
    }
    public static function currancy_format_cart($amount){   
        if(session('form_session_data.currency_id')==3)
        {
            return SysHelper::com_curr_format($amount, 3, '.', '');
        }
        return SysHelper::com_curr_format($amount, 2, '.', '');
    }

    public static function convertAmountToWords($amount,$strRupee,$strPaise)
    {
        $decimal = session('logged_session_data.decimal_point');        
        $ones = [
            '', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine',
            'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen',
            'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'
        ];

        $tens = [
            '', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'
        ];

        $thousands = ['', 'Thousand', 'Million', 'Billion'];

        $numberToWords = function($num) use (&$ones, &$tens, &$thousands, &$numberToWords) {
            if ($num == 0) return 'Zero';

            $word = '';
            $i = 0;

            while ($num > 0) {
                $rem = $num % 1000;
                if ($rem > 0) {
                    $word = SysHelper::convertAmountToWords_helper($rem, $ones, $tens) . $thousands[$i] . ' ' . $word;
                }
                $num = floor($num / 1000);
                $i++;
            }

            return trim($word);
        };

        $amount = number_format((float)$amount, $decimal, '.', '');
        $parts = explode('.', $amount);
        $aed = (int)($parts[0] ?? 0);
        $fils = (int)($parts[1] ?? 0);

        $aedWords = $aed > 0 ? $numberToWords($aed) . ' '. $strRupee : '';
        $filsWords = $fils > 0 ? $numberToWords($fils) . ' '.$strPaise  : '';

        if ($aed > 0 && $fils > 0) {
            return $aedWords . ' and ' . $filsWords . ' only';
        } elseif ($aed > 0) {
            return $aedWords . ' only';
        } elseif ($fils > 0) {
            return $filsWords . ' only';
        } else {
            return 'Zero '.$strRupee.' only';
        }
    }

    private static function convertAmountToWords_helper($num, $ones, $tens)
    {
        $result = '';

        if ($num >= 100) {
            $result .= $ones[floor($num / 100)] . ' Hundred ';
            $num %= 100;
        }

        if ($num >= 20) {
            $result .= $tens[floor($num / 10)] . ' ';
            $num %= 10;
        }

        if ($num > 0 && $num < 20) {
            $result .= $ones[$num] . ' ';
        }

        return $result;
    }
    public static function convertAmountToWordsArabic($amount, $strDirham, $strFils)
    {
        $strDirham="ريال";
        if($strFils=="Baiza"){
            $strFils="بيزة";
        }
        

        $decimal = session('logged_session_data.decimal_point') ?? 2;

        $ones = [
            '', 'واحد', 'اثنان', 'ثلاثة', 'أربعة', 'خمسة', 'ستة', 'سبعة', 'ثمانية', 'تسعة',
            'عشرة', 'أحد عشر', 'اثنا عشر', 'ثلاثة عشر', 'أربعة عشر', 'خمسة عشر',
            'ستة عشر', 'سبعة عشر', 'ثمانية عشر', 'تسعة عشر'
        ];

        $tens = [
            '', '', 'عشرون', 'ثلاثون', 'أربعون', 'خمسون', 'ستون', 'سبعون', 'ثمانون', 'تسعون'
        ];

        $thousands = ['', 'ألف', 'مليون', 'مليار'];

        $numberToWords = function($num) use (&$ones, &$tens, &$thousands) {
            if ($num == 0) return 'صفر';

            $word = '';
            $i = 0;

            while ($num > 0) {
                $rem = $num % 1000;
                if ($rem > 0) {
                    $word = self::convertAmountToWordsArabicHelper($rem, $ones, $tens) . ' ' . $thousands[$i] . ' ' . $word;
                }
                $num = floor($num / 1000);
                $i++;
            }

            return trim($word);
        };

        $amount = number_format((float)$amount, $decimal, '.', '');
        $parts = explode('.', $amount);
        $aed = (int)($parts[0] ?? 0);
        $fils = (int)($parts[1] ?? 0);

        $aedWords = $aed > 0 ? $numberToWords($aed) . ' ' . $strDirham : '';
        $filsWords = $fils > 0 ? $numberToWords($fils) . ' ' . $strFils : '';

        if ($aed > 0 && $fils > 0) {
            return $aedWords . ' و ' . $filsWords;
        } elseif ($aed > 0) {
            return $aedWords;
        } elseif ($fils > 0) {
            return $filsWords;
        } else {
            return 'صفر ' . $strDirham;
        }
    }

    private static function convertAmountToWordsArabicHelper($num, $ones, $tens)
    {
        $result = '';

        if ($num >= 100) {
            $hundreds = [
                '', 'مائة', 'مائتان', 'ثلاثمائة', 'أربعمائة', 'خمسمائة',
                'ستمائة', 'سبعمائة', 'ثمانمائة', 'تسعمائة'
            ];
            $result .= $hundreds[floor($num / 100)] . ' ';
            $num %= 100;
        }

        if ($num >= 20) {
            $result .= $tens[floor($num / 10)] . ' ';
            $num %= 10;
        }

        if ($num > 0 && $num < 20) {
            $result .= $ones[$num] . ' ';
        }

        return trim($result);
    }

    public static function get_new_account_code(){
        $code = DB::table('sys_company')->select('other_code')->where('id',session('logged_session_data.company_id'))->max('other_code');
        $results =  DB::table('sys_chartofaccounts')->where('account_code','like','ACC'.$code.'-%')->where('company_id',session('logged_session_data.company_id'))->max('account_code');
        if($results=="") {
            return "ACC".$code."-1001";
        } else {
            $ret1 = preg_replace('~\D~', '', $results);
            $ret2 = sprintf('%03d',$ret1+1);
            return "ACC".$code."-".$ret2;
        }
    }
    public static function get_new_sub_account_code(){
        $code = DB::table('sys_company')->select('other_code')->where('id',session('logged_session_data.company_id'))->max('other_code');
        $results =  DB::table('sys_chartofaccounts')->where('account_code','like','SACC'.$code.'-%')->where('company_id',session('logged_session_data.company_id'))->max('account_code');
        if($results=="") {
            return "SACC".$code."-1001";
        } else {
            $ret1 = preg_replace('~\D~', '', $results);
            $ret2 = sprintf('%03d',$ret1+1);
            return "SACC".$code."-".$ret2;
        }
    }

    public static function get_new_customer_code(){
        $code = DB::table('sys_company')->select('other_code')->where('id',session('logged_session_data.company_id'))->max('other_code');
        //$results =  DB::table('sys_cust_suppl')->where('code','like','CUS'.$code.'%')->where('company_id',session('logged_session_data.company_id'))->max('code');
        $results =  DB::table('sys_cust_suppl')->where('code','like','CUS'.$code.'-%')->where('company_id',session('logged_session_data.company_id'))->max('code');
        if($results=="") {
            return "CUS".$code."-1001";
        } else {
            $ret1 = preg_replace('~\D~', '', $results);
            $ret2 = sprintf('%03d',$ret1+1);
            $nc = "CUS".$code."-".$ret2;
            $results2 =  DB::table('sys_cust_suppl')->where('code',$nc)->get();
            if(count($results2)==0){
                return $nc;
            } else {
                $results3 =  DB::table('sys_cust_suppl')->where('code','like','CUS'.$code.'-%')->max('code');
                $ret_1 = preg_replace('~\D~', '', $results3);
                $ret_2 = sprintf('%03d',$ret_1+1);
                return "CUS".$code."-".$ret_2;
            }
        }
    }
    public static function get_new_supplier_code(){
        $code = DB::table('sys_company')->select('other_code')->where('id',session('logged_session_data.company_id'))->max('other_code');
        $results =  DB::table('sys_cust_suppl')->where('code','like','SUP'.$code.'-%')->where('company_id',session('logged_session_data.company_id'))->max('code');
        if($results=="") {
            return "SUP".$code."-1001";
        } else {
            $ret1 = preg_replace('~\D~', '', $results);
            $ret2 = sprintf('%03d',$ret1+1);
            return "SUP".$code."-".$ret2;
        }
    }
    public static function get_new_sales_invoice_code(){
        $code = DB::table('sys_company')->select('sales_code')->where('id',session('logged_session_data.company_id'))->max('sales_code');
        $results =  DB::table('sys_sales_invoice')->where('doc_number','like',$code.'-%')->where('company_id',session('logged_session_data.company_id'))->max('doc_number');
        if($results=="") {
            return $code."-1001";
        } else {
            $ret1 = preg_replace('~\D~', '', $results);
            $ret2 = sprintf('%03d',$ret1+1);
            return $code."-".$ret2;
        }
    }
    public static function get_new_code($table_name,$code,$colum){
        $code2 = DB::table('sys_company')->select('other_code')->where('id',session('logged_session_data.company_id'))->max('other_code');
        $results =  DB::table($table_name)->where($colum,'like',$code.$code2.'-%')->where('company_id',session('logged_session_data.company_id'))->max($colum);
        
        if($results=="") {
            return $code.$code2."-1001";
        } else {
            $ret1 = preg_replace('~\D~', '', $results);
            $ret2 = sprintf('%03d',$ret1+1);
            return $code.$code2.'-'.$ret2;
        }
    }

    public static function get_new_chequebook_doc_number() {
        $companyCode = DB::table('sys_company')->where('id', session('logged_session_data.company_id'))->value('other_code');
        $prefix = 'CB'.$companyCode.'-';

        $maxDoc = DB::table('chequebooks')
            ->where('company_id', session('logged_session_data.company_id'))
            ->where('doc_number', 'like', $prefix . '%')
            ->max('doc_number');

        if (empty($maxDoc)) {
            return $prefix . '1001';
        }

        // extract numeric suffix (e.g. CBXX-1001 => 1001)
        preg_match('/(\d+)$/', $maxDoc, $matches);
        $number = isset($matches[1]) ? intval($matches[1]) : 1000;

        return $prefix . ($number + 1);
    }
    public static function get_new_product_code($table_name,$code,$colum){
        $code2 = DB::table('sys_company')->select('other_code')->where('id',session('logged_session_data.company_id'))->max('other_code');
        $results =  DB::table($table_name)->where($colum,'like',$code.$code2.'-%')->max($colum);
        if($results=="") {
            return $code.$code2."-1001";
        } else {
            $ret1 = preg_replace('~\D~', '', $results);
            $ret2 = sprintf('%03d',$ret1+1);
            return $code.$code2.'-'.$ret2;
        }
    }
    public static function get_new_code_err($table_name, $code, $colum) {
        // Get the other_code from sys_company
        $code2 = DB::table('sys_company')
            ->where('id', session('logged_session_data.company_id'))
            ->value('other_code');
    
        // Get the max numeric part of doc_number
        $maxNumber = DB::table($table_name)
            ->where($colum, 'like', $code.$code2.'-%')
            ->where('company_id', session('logged_session_data.company_id'))
            ->selectRaw("MAX(CAST(SUBSTRING_INDEX($colum, '-', -1) AS UNSIGNED)) as max_num")
            ->value('max_num');
    
        // If no previous record exists, start from 1001
        $newNumber = ($maxNumber === null) ? 1001 : $maxNumber + 1;
    
        return $code.$code2.'-'.$newNumber;
    }
    public static function get_new_code_normal($table_name,$code,$colum){
        $results =  DB::table($table_name)->where($colum,'like',$code.'-%')->where('company_id',session('logged_session_data.company_id'))->max($colum);
        if($results=="") {
            return $code."-1001";
        } else {
            $ret1 = preg_replace('~\D~', '', $results);
            $ret2 = sprintf('%03d',$ret1+1);
            return $code.'-'.$ret2;
        }
    }    
    public static function get_new_staff_code(){
        $code = DB::table('sys_company')->select('other_code')->where('id',session('logged_session_data.company_id'))->max('other_code');
        $results =  DB::table('sm_staffs')->where('staff_no','like','UI'.$code.'-%')->where('company_id',session('logged_session_data.company_id'))->max('staff_no');
        if($results=="") {
            return "UI".$code."-1001";
        } else {
            $ret1 = preg_replace('~\D~', '', $results);
            $ret2 = sprintf('%03d',$ret1+1);
            return "UI".$code."-".$ret2;
        }
    }

    /**
     * Generate staff code for a specific company without relying on session.
     * Format: UI{OTHER_CODE}-{SEQ}
     */
    public static function get_new_staff_code_for_company(int $companyId, ?string $otherCode = null): string
    {
        $code = strtoupper(trim((string) ($otherCode ?? DB::table('sys_company')->where('id', $companyId)->value('other_code'))));
        if ($code === '') {
            // Defensive fallback (other_code is expected to exist, but avoid returning invalid pattern)
            $code = '00';
        }

        $results = DB::table('sm_staffs')
            ->where('staff_no', 'like', 'UI' . $code . '-%')
            ->where('company_id', $companyId)
            ->max('staff_no');

        if ($results == "") {
            return "UI" . $code . "-1001";
        }

        $ret1 = preg_replace('~\D~', '', $results);
        $ret2 = sprintf('%03d', $ret1 + 1);
        return "UI" . $code . "-" . $ret2;
    }

    public static function get_new_lead_deal_code($table_name, $column, $company_id)
    {
        $latestCode = DB::table($table_name)
                        ->where('company_id', $company_id)
                        ->max($column);

        if (empty($latestCode)) {
            return '1001';
        }
        $numericPart = preg_replace('/\D/', '', $latestCode);
        if (empty($numericPart)) {
            return '1001';
        }
        return str_pad($numericPart + 1, 4, '0', STR_PAD_LEFT);
    }
    public static function get_dealid_from_code($deal_code){
    
        $deal_id =  DB::table('sys_crm_deals')
        //->where('company_id',session('logged_session_data.company_id'))
        ->where('code',$deal_code)->max('id');

        if($deal_id=="") {
            return "0";
        } else {
            return $deal_id;
        }
    }
    public static function get_code_from_dealid($deal_id){
        $deal_id =  DB::table('sys_crm_deals')
        //->where('company_id',session('logged_session_data.company_id'))
        ->where('id',$deal_id)->max('code');
        if($deal_id=="") {
            return "Without Deal";
        } else {
            return $deal_id;
        }
    }
    public static function get_dealid_from_code_list($deal_code){
        $dealid=[];
        $newArray =  array_map('trim', explode(',',$deal_code));
        if(count($newArray)>0){
            foreach ($newArray as $key) {
                $dealid[]=array($key);
            }
        }
 
        $deal_id =  DB::table('sys_crm_deals')
        //->where('company_id',session('logged_session_data.company_id'))
        ->wherein('code',$dealid)->pluck('id')->implode(',');
        if($deal_id=="") {
            return "0";
        } else {
            return $deal_id;
        }
    }
    public static function get_code_from_dealid_list($deal_id){
        $dealid=[];
        $newArray =  explode(',',$deal_id);
        if(count($newArray)>0){
            foreach ($newArray as $key) {
                $dealid[]=array($key);
            }
        }
        $deal_code =  DB::table('sys_crm_deals')->where('company_id',session('logged_session_data.company_id'))->wherein('id',$dealid)->pluck('code')->implode(',');
        if($deal_code=="") {
            return "Without Deal";
        } else {
            return $deal_code;
        }
    }
    

    public static function cheque_print_currancy_code($com_id){
        $currency1="Dirham"; $currency2="Fils";
        if($com_id == 1){ $currency1="Dirham"; $currency2="Fils"; }
        if($com_id == 2){ $currency1="Dirham"; $currency2="Fils"; }
        if($com_id == 3){ $currency1="Dirham"; $currency2="Fils"; }
        if($com_id == 4){ $currency1="Pound"; $currency2="Pences"; }
        if($com_id == 5){ $currency1="Dirham"; $currency2="Fils"; }
        if($com_id == 6){ $currency1="Dirham"; $currency2="Fils"; }
        if($com_id == 7){ $currency1="Pound"; $currency2="Pences"; }
        if($com_id == 8){ $currency1="Riyal"; $currency2="Halalas"; }
        if($com_id == 9){ $currency1="Riyal"; $currency2="Dirham"; }
        if($com_id == 10){ $currency1="Riyal"; $currency2="Baiza"; }
        return [$currency1,$currency2];
    }


    /*public static function get_new_maxid($table_name,$col){
        $results =  DB::table($table_name)->where('company_id',session('logged_session_data.company_id'))->max($col);
        if($results=="")
        {
            return "1001";
        }
        else{
            return $results+1;
        }
    }

    public static function get_new_maxid_2($table_name,$mode,$col){
        $mode_id=1;
        if($mode=='bank') { $mode_id=2; }
        $results =  DB::table($table_name)->where('mode',$mode_id)->where('company_id',session('logged_session_data.company_id'))->max($col);
        if($results=="")
        {
            return "1001";
        }
        else{
            return $results+1;
        }
    }*/

    public static function get_days_from_dates($startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $differenceInDays = $start->diffInDays($end);
        return $differenceInDays;
    }

    public static function lead_updated_at($lead_id){

        $today = Carbon::now('+04:00')->toDateString();

        $lead = DB::table('sys_crm_leads')
        ->select('last_updated', 'lead_update_count')
        ->where('id', $lead_id)
        ->first();
        
        if(!$lead){
            return false;
        }
        
        $today = Carbon::now('+04:00')->toDateString();
        $lastUpdatedDate = Carbon::parse($lead->last_updated)->toDateString();

        if($today !== $lastUpdatedDate){
            DB::table('sys_crm_leads')->where('id',$lead_id)->update(
                    [
                        'updated_by' => Auth::user()->id,
                        'updated_at' => Carbon::now('+04:00')->format('Y-m-d H:i:s'),
                        'last_updated' => Carbon::now('+04:00')->format('Y-m-d H:i:s'),
                        'lead_update_count' => DB::raw('lead_update_count + 1'),
                        'followup_count' => DB::raw('followup_count + 1')
                    ]
            );
        }else{                // Still update updated_by/updated_at if you want
                DB::table('sys_crm_leads')->where('id', $lead_id)->update([
                    'updated_by' => Auth::id(),
                    'updated_at' => Carbon::now('+04:00')->format('Y-m-d H:i:s'),
                    'last_updated' => Carbon::now('+04:00')->format('Y-m-d H:i:s'),
                    'followup_count' => DB::raw('followup_count + 1')
                ]);
        }
    }
    public static function deal_updated_at($deal_id){
        DB::table('sys_crm_deals')->where('id',$deal_id)->update(
            [
                'updated_by' => Auth::user()->id,
                'updated_at' => Carbon::now('+04:00')->format('Y-m-d H:i:s'),
            ]
        );
    }

    public static function get_pagination_post($request){
        if(count($request->all())==0){
            return false;
        }
        else{
            if(count($request->all())==1 && isset($request['page'])){
                return false;
            }
            else{
                return true;
            }                
        }
    }


    public static function get_status_icon($section,$status){
        
        if($section=="accounts"){
            if ($status==1){ return'<span class="border-success rounded-circle mr-1" style="padding:0px 7px 0px 6px; border-width: 3px; border: 2px solid #e3e6f0;" title="Accounts Approved"></span>'; }
            else if ($status==2){ return'<span class="border-danger rounded-circle mr-1" style="padding:0px 7px 0px 6px; border-width: 3px; border: 2px solid #e3e6f0;" title="Accounts Rejected"></span>'; }
            else if ($status==3){ return'<span class="border-warning rounded-circle mr-1" style="padding:0px 7px 0px 6px; border-width: 3px; border: 2px solid #e3e6f0;" title="Accounts On Process"></span>'; }
            else{ return'<span class="border-dark rounded-circle mr-1" style="padding:0px 7px 0px 6px; border-width: 3px; border: 2px solid #e3e6f0;" title="Accounts Pending"></span>'; }
        }
        if($section=="sales"){
            if ($status==1){ return'<span class="border-success rounded-circle mr-1" style="padding:0px 7px 0px 6px; border-width: 3px; border: 2px solid #e3e6f0;" title="Sales Approved"></span>'; }
            else if ($status==2){ return'<span class="border-danger rounded-circle mr-1" style="padding:0px 7px 0px 6px; border-width: 3px; border: 2px solid #e3e6f0;" title="Sales Rejected"></span>'; }
            else if ($status==3){ return'<span class="border-warning rounded-circle mr-1" style="padding:0px 7px 0px 6px; border-width: 3px; border: 2px solid #e3e6f0;" title="Sales On Process"></span>'; }
            else{ return'<span class="border-dark rounded-circle mr-1" style="padding:0px 7px 0px 6px; border-width: 3px; border: 2px solid #e3e6f0;" title="Sales Pending"></span>'; }
        }
        if($section=="purchease"){
            if ($status==1){ return'<span class="border-success rounded-circle mr-1" style="padding:0px 7px 0px 6px; border-width: 3px; border: 2px solid #e3e6f0;" title="Purchase Approved"></span>'; }
            else if ($status==2){ return'<span class="border-danger rounded-circle mr-1" style="padding:0px 7px 0px 6px; border-width: 3px; border: 2px solid #e3e6f0;" title="Purchase Rejected"></span>'; }
            else if ($status==3){ return'<span class="border-warning rounded-circle mr-1" style="padding:0px 7px 0px 6px; border-width: 3px; border: 2px solid #e3e6f0;" title="Purchase On Process"></span>'; }
            else{ return'<span class="border-dark rounded-circle mr-1" style="padding:0px 7px 0px 6px; border-width: 3px; border: 2px solid #e3e6f0;" title="Purchase Pending"></span>'; }
        }
        if($section=="invoice"){
            if ($status==1){ return'<span class="border-success rounded-circle mr-1" style="padding:0px 7px 0px 6px; border-width: 3px; border: 2px solid #e3e6f0;" title="Invoice Approved"></span>'; }
            else if ($status==2){ return'<span class="border-danger rounded-circle mr-1" style="padding:0px 7px 0px 6px; border-width: 3px; border: 2px solid #e3e6f0;" title="Invoice Rejected"></span>'; }
            else if ($status==3){ return'<span class="border-warning rounded-circle mr-1" style="padding:0px 7px 0px 6px; border-width: 3px; border: 2px solid #e3e6f0;" title="Invoice On Process"></span>'; }
            else{ return'<span class="border-dark rounded-circle mr-1" style="padding:0px 7px 0px 6px; border-width: 3px; border: 2px solid #e3e6f0;" title="Invoice Pending"></span>'; }
        }
        if($section=="delivery"){
            if ($status==1){ return'<span class="border-success rounded-circle mr-1" style="padding:0px 7px 0px 6px; border-width: 3px; border: 2px solid #e3e6f0;" title="Delivery Approved"></span>'; }
            else if ($status==2){ return'<span class="border-danger rounded-circle mr-1" style="padding:0px 7px 0px 6px; border-width: 3px; border: 2px solid #e3e6f0;" title="Delivery Rejected"></span>'; }
            else if ($status==3){ return'<span class="border-warning rounded-circle mr-1" style="padding:0px 7px 0px 6px; border-width: 3px; border: 2px solid #e3e6f0;" title="Delivery On Process"></span>'; }
            else{ return'<span class="border-dark rounded-circle mr-1" style="padding:0px 7px 0px 6px; border-width: 3px; border: 2px solid #e3e6f0;" title="Delivery Pending"></span>'; }
        }
        if($section=="receivables"){
            if ($status==1){ return'<span class="border-success rounded-circle" style="padding:0px 7px 0px 6px; border-width: 3px; border: 2px solid #e3e6f0;" title="Receivables Approved"></span>'; }
            else if ($status==2){ return'<span class="border-danger rounded-circle" style="padding:0px 7px 0px 6px; border-width: 3px; border: 2px solid #e3e6f0;" title="Receivables Rejected"></span>'; }
            else if ($status==3){ return'<span class="border-warning rounded-circle" style="padding:0px 7px 0px 6px; border-width: 3px; border: 2px solid #e3e6f0;" title="Receivables On Process"></span>'; }
            else{ return'<span class="border-dark rounded-circle" style="padding:0px 7px 0px 6px; border-width: 3px; border: 2px solid #e3e6f0;" title="Receivables Pending"></span>'; }
        }
        return'<span class="rounded-circle mr-1" style="padding:0px 7px 0px 6px; border-width: 3px; border: 2px solid #e3e6f0;" title="Pending"></span>';
    }
    
    public static function lead_type($id){
        if($id==1){
            return '<span class="btn-xs btn-success mt-2 pl-2 pr-2">Reseller</span>';}
        else if($id==2){
            return '<span class="btn-xs btn-info mt-2 pl-2 pr-2">Enduser</span>';}
        else if($id==3){
            return '<span class="btn-xs btn-primary mt-2 pl-2 pr-2">E-Commerce</span>';}
        else if($id==4){
            return '<span class="btn-xs btn-warning mt-2 pl-2 pr-2">Project</span>';}
        else{
            return '';}
    }
    public static function lead_type_new($id){
        if($id==1){
            return '<span class="badge bg-primary">Reseller</span>';}
        else if($id==2){
            return '<span class="badge bg-primary">Enduser</span>';}
        else if($id==3){
            return '<span class="badge bg-primary">E-Commerce</span>';}
        else if($id==4){
            return '<span class="badge bg-primary">Project</span>';}
        else{
            return '';}
    }
    public static function deal_type($id){
        if($id==1){
            return '<span class="btn-xs btn-success mt-2 pl-2 pr-2">Reseller</span>';}
        else if($id==2){
            return '<span class="btn-xs btn-info mt-2 pl-2 pr-2">Enduser</span>';}
        else if($id==3){
            return '<span class="btn-xs btn-primary mt-2 pl-2 pr-2">E-Commerece</span>';}
        else if($id==4){
            return '<span class="btn-xs btn-primary mt-2 pl-2 pr-2">Project</span>';}
        else{
            return '';}
    }
  

     public static function deal_type_new($id){
        if($id==1){
            return 'Reseller';}
        else if($id==2){
            return 'Enduser';}
        else if($id==3){
            return 'E-Commerece';}
        else if($id==4){
            return 'Project';}
        else{
            return '';}
    }

    public static function set_user_custsupp($user_d,$company_id){
        $data = DB::table('sys_cust_suppl_assign')->where('cust_supp_id', $company_id)->where('user_id', $user_d)->first();
        if(!isset($data)){
            DB::table('sys_cust_suppl_assign')->insert(
                [
                    'cust_supp_id' => $company_id,
                    'user_id' => $user_d,
                    'type' => 1, //1 customers, 2 suppliers
                ]);
        }
    }
    public static function get_deal_status_log($accounts,$sales,$purchease,$invoice,$delivery,$receivables){
        if($receivables==1){
            return '<span class="text-xs text-success">Payment Received</span>';}
        else if($receivables==2){
            return '<span class="text-xs text-danger">Rejected</span>';}
        else if($receivables==3){
            return '<span class="text-xs text-primary">Payment Pending</span>';}
        else if($delivery==1){
            return '<span class="text-xs text-success">Delivery Completed</span>';}
        else if($delivery==2){
            return '<span class="text-xs text-danger">Delivery Rejected</span>';}
        else if($delivery==3){
            return '<span class="text-xs text-primary">Out For Delivery</span>';}
        else if($delivery==4){
            return '<span class="text-xs text-primary">Pending For Delivery</span>';}
        else if($invoice==1){
            return '<span class="text-xs text-success">Invoice Approved</span>';}
        else if($invoice==2){
            return '<span class="text-xs text-danger">Invoice Disapproved</span>';}
        else if($invoice==3){
            return '<span class="text-xs text-primary">Invoice Pending</span>';}
        else if($purchease==1){
            return '<span class="text-xs text-success">Purchase Approved</span>';}
        else if($purchease==2){
            return '<span class="text-xs text-danger">Purchase Disapproved</span>';}
        else if($purchease==3){
            return '<span class="text-xs text-primary">Purchase Pending</span>';}
        else if($sales==1){
            return '<span class="text-xs text-success">Sales Approved</span>';}
        else if($sales==2){
            return '<span class="text-xs text-danger">Sales Disapproved</span>';}
        else if($sales==3){
            return '<span class="text-xs text-primary">Sales Pending</span>';}
        else if($accounts==1){
            return '<span class="text-xs text-success">Accounts Approved</span>';}
        else if($accounts==2){
            return '<span class="text-xs text-danger">Accounts Disapproved</span>';}
        else if($accounts==3){
            return '<span class="text-xs text-primary">Accounts Pending</span>';}
        else{
            return '<span class="text-xs text-warning">New</span>';
        }
    }
    public static function get_deal_status_db($deal_id){
        $value = DB::table('sys_crm_deal_track')->select('accounts','sales','purchease','invoice','delivery','receivables')->where('deal_id',$deal_id)->first();
        if(isset($value)){
            if($value->receivables==1){
                return '<span class="btn-xs btn-success text-white pl-1 pr-1">Payment Received</span>';}
            else if($value->receivables==2){
                return '<span class="btn-xs btn-danger text-white pl-1 pr-1">Rejected</span>';}
            else if($value->receivables==3){
                return '<span class="btn-xs btn-primary text-white pl-1 pr-1">Payment Pending</span>';}
            else if($value->delivery==1){
                return '<span class="btn-xs btn-success text-white pl-1 pr-1">Delivery Completed</span>';}
            else if($value->delivery==2){
                return '<span class="btn-xs btn-danger text-white pl-1 pr-1">Delivery Rejected</span>';}
            else if($value->delivery==3){
                return '<span class="btn-xs btn-primary text-white pl-1 pr-1">Out For Delivery</span>';}
            else if($value->delivery==4){
                return '<span class="btn-xs btn-primary text-white pl-1 pr-1">Pending For Delivery</span>';}
            else if($value->invoice==1){
                return '<span class="btn-xs btn-success text-white pl-1 pr-1">Invoice Approved</span>';}
            else if($value->invoice==2){
                return '<span class="btn-xs btn-danger text-white pl-1 pr-1">Invoice Disapproved</span>';}
            else if($value->invoice==3){
                return '<span class="btn-xs btn-primary text-white pl-1 pr-1">Invoice Pending</span>';}
            else if($value->purchease==1){
                return '<span class="btn-xs btn-success text-white pl-1 pr-1">Purchase Approved</span>';}
            else if($value->purchease==2){
                return '<span class="btn-xs btn-danger text-white pl-1 pr-1">Purchase Disapproved</span>';}
            else if($value->purchease==3){
                return '<span class="btn-xs btn-primary text-white pl-1 pr-1">Purchase Pending</span>';}
            else if($value->sales==1){
                return '<span class="btn-xs btn-success text-white pl-1 pr-1">Sales Approved</span>';}
            else if($value->sales==2){
                return '<span class="btn-xs btn-danger text-white pl-1 pr-1">Sales Disapproved</span>';}
            else if($value->sales==3){
                return '<span class="btn-xs btn-primary text-white pl-1 pr-1">Sales Pending</span>';}
            else if($value->accounts==1){
                return '<span class="btn-xs btn-success text-white pl-1 pr-1">Accounts Approved</span>';}
            else if($value->accounts==2){
                return '<span class="btn-xs btn-danger text-white pl-1 pr-1">Accounts Disapproved</span>';}
            else if($value->accounts==3){
                return '<span class="btn-xs btn-primary text-white pl-1 pr-1">Accounts Pending</span>';}
            else{
                return '<span class="btn-xs btn-warning text-white pl-1 pr-1">New</span>';
            }
        }
        else{
            return "New";
        }
    }
    public static function get_deal_status($track, $status){
        if($track=="receivables"){
            if($status==1){
                return '<span class="btn-xs btn-success text-white pl-1 pr-1">Payment Received</span>';}
            else if($status==2){
                return '<span class="btn-xs btn-danger text-white pl-1 pr-1">Rejected</span>';}
            else if($status==3){
                return '<span class="btn-xs btn-primary text-white pl-1 pr-1">Payment Pending</span>';}
            else{
                return '<span class="btn-xs btn-warning text-white pl-1 pr-1">New</span>';}
        }

        if($track=="delivery"){
            if($status==1){
                return '<span class="btn-xs btn-success text-white pl-1 pr-1">Delivery Completed</span>';}
            else if($status==2){
                return '<span class="btn-xs btn-danger text-white pl-1 pr-1">Delivery Rejected</span>';}
            else if($status==3){
                return '<span class="btn-xs btn-primary text-white pl-1 pr-1">Out For Delivery</span>';}
            else if($status==4){
                return '<span class="btn-xs btn-primary text-white pl-1 pr-1">Pending For Delivery</span>';}
            else{
                return '<span class="btn-xs btn-warning text-white pl-1 pr-1">New</span>';}
        }

        if($track=="invoice"){
            if($status==1){
                return '<span class="btn-xs btn-success text-white pl-1 pr-1">Invoice Approved</span>';}
            else if($status==2){
                return '<span class="btn-xs btn-danger text-white pl-1 pr-1">Invoice Disapproved</span>';}
            else if($status==3){
                return '<span class="btn-xs btn-primary text-white pl-1 pr-1">Invoice Pending</span>';}
            else{
                return '<span class="btn-xs btn-warning text-white pl-1 pr-1">New</span>';}
        }

        if($track=="purchease"){
            if($status==1){
                return '<span class="btn-xs btn-success text-white pl-1 pr-1">Purchase Approved</span>';}
            else if($status==2){
                return '<span class="btn-xs btn-danger text-white pl-1 pr-1">Purchase Disapproved</span>';}
            else if($status==3){
                return '<span class="btn-xs btn-primary text-white pl-1 pr-1">Purchase Pending</span>';}
            else{
                return '<span class="btn-xs btn-warning text-white pl-1 pr-1">New</span>';}
        }
        
        if($track=="sales"){
            if($status==1){
                return '<span class="btn-xs btn-success text-white pl-1 pr-1">Sales Approved</span>';}
            else if($status==2){
                return '<span class="btn-xs btn-danger text-white pl-1 pr-1">Sales Disapproved</span>';}
            else if($status==3){
                return '<span class="btn-xs btn-primary text-white pl-1 pr-1">Sales Pending</span>';}
            else{
                return '<span class="btn-xs btn-warning text-white pl-1 pr-1">New</span>';}
        }

        if($track=="accounts"){
            if($status==1){
                return '<span class="btn-xs btn-success text-white pl-1 pr-1">Accounts Approved</span>';}
            else if($status==2){
                return '<span class="btn-xs btn-danger text-white pl-1 pr-1">Accounts Disapproved</span>';}
            else if($status==3){
                return '<span class="btn-xs btn-primary text-white pl-1 pr-1">Accounts Pending</span>';}
            else{
                return '<span class="btn-xs btn-warning text-white pl-1 pr-1">New</span>';
            }
        }
        if($track=="new"){
            return '<span class="btn-xs btn-warning text-white pl-1 pr-1">New</span>';
        }
    }
    public static function deal_edit_disable($deal_id){
        if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 9){
            return 0;
        }
        $data = DB::table('sys_crm_deal_track')->select('accounts','sales','purchease','invoice','delivery','receivables')->where('deal_id',$deal_id)->first();
        if(isset($data)){
            if($data->accounts==1 && $data->sales==1 && $data->purchease==1 && $data->invoice==1 && $data->delivery==1 && $data->receivables==1){
                return 1;
            }
            elseif($data->accounts==2 || $data->sales==2 || $data->purchease==2 || $data->invoice==2 || $data->delivery==2 || $data->receivables==2){
                return 0;   
            }
            else{
                return 1;
            }
        }
        else{
            return 0;
        }
    }

    public static function check_grn($po_id, $part_no){    
        $po=SysPurchaseOrderItems::where('po_id',$po_id)->where('part_number',$part_no)->sum('qty');
        $gr=SysPurchaseGRNItems::where('po_id',$po_id)->where('part_no',$part_no)->sum('qty');
        if($po==$gr){return 0;}
        else{return abs($po-$gr);}
    }

    public static function get_total_sales($user_id){
        // $data = DB::table('sys_crm_deals')->where('stage',4)->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '".date('Y-m')."'")->where('owner',$user_id)->sum('deal_value');
        // return $data;

        $data = DB::table('sys_crm_deals')->join('sys_crm_deal_track','sys_crm_deal_track.deal_id','sys_crm_deals.id')
        ->where('sys_crm_deals.stage',4)->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '".date('Y-m')."'")
        ->where('sys_crm_deals.owner',$user_id)->where('sys_crm_deal_track.invoice',1)->sum('deal_value');
        return SysHelper::com_curr_format($data, 2, '.', '');
    }
    
    public static function get_total_sales_brand($user_id,$brand,$company){
        $brand = array_map('intval', explode(',', $brand));
        $start_date = date('Y-m', strtotime('first day of this month - 3 months'));
        $end_date = date('Y-m', strtotime('last day of this month'));

        
        $teams= array($user_id);

        try {
            if($brand[0]==0){
                if($company==13){
                    $data = DB::table('sys_crm_deals')->select('sys_crm_deals.id as dealid','deal_value','deal_profit','deal_currency','source','cust_id','deal_percent','deal_profit','sys_crm_deals.owner')
                    ->leftjoin('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_id','sys_crm_deals.id')
                    ->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m') = '".date('Y-m')."'")
                    ->where('sys_crm_deals.stage',4)->where('sys_crm_deal_track_approval_invoice.status',1)->wherein('sys_crm_deals.owner',$teams)->get();
                    
                    $data2 = DB::table('sys_crm_deals')->select('sys_crm_deals.id as dealid','deal_value','deal_profit','deal_currency','source','cust_id','deal_percent','deal_profit','qty','price','sys_crm_deals.owner')
                    ->leftjoin('sys_crm_quote_items','sys_crm_quote_items.deal_id','sys_crm_deals.id')
                    ->leftjoin('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_id','sys_crm_deals.id')
                    ->wherein('sys_crm_quote_items.product_id',[8490,8544,9726,9728,9780,10294,10624,10673,11722,12049,12487])
                    ->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m') = '".date('Y-m')."'")
                    ->where('sys_crm_deals.stage',4)->where('sys_crm_deal_track_approval_invoice.status',1)->wherein('sys_crm_deals.owner',$teams)->get();
                    
                    /*$data3 = DB::table('sys_crm_deals')->select('sys_crm_deals.id as dealid','deal_value','deal_currency','source','cust_id','deal_percent','deal_profit','qty','price')
                    ->leftjoin('sys_crm_quote_items','sys_crm_quote_items.deal_id','sys_crm_deals.id')
                    ->leftjoin('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_id','sys_crm_deals.id')
                    ->wherein('sys_crm_quote_items.product_id',[9976,10465,10497])
                    ->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m') = '".date('Y-m')."'")
                    ->where('sys_crm_deals.stage',4)->where('sys_crm_deal_track_approval_invoice.status',1)->wherein('sys_crm_deals.owner',$teams)->get();
                    
                    return $data3;*/

                }else{
                    $data = DB::table('sys_crm_deals')->select('sys_crm_deals.id as dealid','deal_value','deal_profit','deal_currency','source','cust_id','deal_percent','sys_crm_deals.owner')
                    ->leftjoin('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_id','sys_crm_deals.id')->where('sys_crm_deals.is_partial_invoice',0)                
                    ->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m') = '".date('Y-m')."'")
                    ->where('sys_crm_deals.stage',4)->wherein('sys_crm_deals.owner',$teams)->where('sys_crm_deal_track_approval_invoice.status',1)->get();
                    $data2 = [];
                }
        
                $retAmount=0;
                if(count($data)>0){
                    foreach($data as $dt){
                        
                    if(in_array($dt->owner,[25,3651,68,6431,65,27])){
                        $retAmount+= SysHelper::get_aed_amount_new($dt->deal_currency,$dt->deal_profit);
                    }
                    else{
                        $retAmount+= SysHelper::get_deal_value($dt->deal_value,$dt->source,$dt->deal_currency,$dt->deal_percent,$dt->cust_id);
                    }
                    }
                }
                if(count($data2)>0){
                    foreach($data2 as $dt){                        
                    if($company==13){
                        $retAmount+= SysHelper::get_aed_amount_new($dt->deal_currency,($dt->qty*$dt->price));
                    }
                    }
                }
                
                return SysHelper::com_curr_format($retAmount, 2, '.', '');
            }
            
        } catch (\Throwable $th) {
            return $th;
        }
    }
    public static function get_total_sales_brand_3months_profit($user_id,$brand,$company){
        $brand = array_map('intval', explode(',', $brand));
        $start_date = date('Y-m', strtotime('first day of this month - 3 months'));
        $end_date = date('Y-m', strtotime('last day of this month'));
        $quarter = SysHelper::get_quarter(date('m'));

        if($user_id==26 || $user_id==36 || $user_id==112){//26 Naeem & 36 Arianne
            $teams= array(26,36,112);
        }
        else{$teams= array($user_id);}
        try {
            if($brand[0]==0){
                if($company==13){
                    $data = DB::table('sys_crm_deals')->select('sys_crm_deals.id as dealid','deal_value','deal_currency','source','cust_id','deal_percent','deal_profit')
                    ->leftjoin('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_id','sys_crm_deals.id')
                    ->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '".$quarter[0]."' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '".$quarter[1]."'")
                    ->where('sys_crm_deals.stage',4)->where('sys_crm_deal_track_approval_invoice.status',1)->wherein('sys_crm_deals.owner',$teams)->get();
                    
                    $data2 = DB::table('sys_crm_deals')->select('sys_crm_deals.id as dealid','deal_value','deal_currency','source','cust_id','deal_percent','deal_profit','qty','price')
                    ->leftjoin('sys_crm_quote_items','sys_crm_quote_items.deal_id','sys_crm_deals.id')
                    ->leftjoin('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_id','sys_crm_deals.id')
                    ->wherein('sys_crm_quote_items.product_id',[8490,8544,9726,9728,9780,10294,10624,10673,11722,12049,12487])
                    ->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '".$quarter[0]."' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '".$quarter[1]."'")
                    ->where('sys_crm_deals.stage',4)->where('sys_crm_deal_track_approval_invoice.status',1)->wherein('sys_crm_deals.owner',$teams)->get();
                    
                    /*$data3 = DB::table('sys_crm_deals')->select('sys_crm_deals.id as dealid','deal_value','deal_currency','source','cust_id','deal_percent','deal_profit','qty','price')
                    ->leftjoin('sys_crm_quote_items','sys_crm_quote_items.deal_id','sys_crm_deals.id')
                    ->leftjoin('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_id','sys_crm_deals.id')
                    ->wherein('sys_crm_quote_items.product_id',[9976,10465,10497])
                    ->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m') = '".date('Y-m')."'")
                    ->where('sys_crm_deals.stage',4)->where('sys_crm_deal_track_approval_invoice.status',1)->wherein('sys_crm_deals.owner',$teams)->get();
                    
                    return $data3;*/

                }else{
                    $data = DB::table('sys_crm_deals')->select('sys_crm_deals.id as dealid','deal_value','deal_currency','source','cust_id','deal_percent')
                    ->leftjoin('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_id','sys_crm_deals.id')->where('sys_crm_deals.is_partial_invoice',0)
                    ->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '".$quarter[0]."' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '".$quarter[1]."'")
                    ->where('sys_crm_deals.stage',4)->wherein('sys_crm_deals.owner',$teams)->where('sys_crm_deal_track_approval_invoice.status',1)->get();
                    $data2 = [];
                }
        
                $retAmount=0;
                if(count($data)>0){
                    foreach($data as $dt){
                        
                    if($company==13){
                        $retAmount+= SysHelper::get_aed_amount($dt->deal_currency,$dt->deal_value);
                    }
                    else{
                        $retAmount+= SysHelper::get_deal_value($dt->deal_value,$dt->source,$dt->deal_currency,$dt->deal_percent,$dt->cust_id);
                    }
                    }
                }
                if(count($data2)>0){
                    foreach($data2 as $dt){                        
                    if($company==13){
                        $retAmount+= SysHelper::get_aed_amount($dt->deal_currency,($dt->qty*$dt->price));
                    }
                    }
                }
                
                return SysHelper::com_curr_format($retAmount, 2, '.', '');
            }
            
        } catch (\Throwable $th) {
            return $th;
        }
    }
    public static function get_total_sales_brand_3months($user_id,$brand){
        
        $quarter = SysHelper::get_quarter(date('m'));

        $brand = array_map('intval', explode(',', $brand));
        $start_date = date('Y-m', strtotime('first day of this month - 3 months'));
        $end_date = date('Y-m', strtotime('last day of this month'));

        if($user_id==26 || $user_id==36 || $user_id==112){//26 Naeem & 36 Arianne
            $teams= array(26,36,112);
        }
        else{$teams= array($user_id);}
        try {
            if($brand[0]==0){
                $data = DB::table('sys_crm_deals')->select('sys_crm_deals.id as dealid','deal_value','deal_currency','source','cust_id','deal_percent')->leftjoin('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_id','sys_crm_deals.id')->where('sys_crm_deals.is_partial_invoice',0)

                //->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m') = '".date('Y-m')."'")
                
                ->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '".$quarter[0]."' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '".$quarter[1]."'")
                ->where('sys_crm_deals.stage',4)->wherein('sys_crm_deals.owner',$teams)->where('sys_crm_deal_track_approval_invoice.status',1)->get();
        
                $retAmount=0;
                if(count($data)>0){
                    foreach($data as $dt){
                        $retAmount+= SysHelper::get_deal_value($dt->deal_value,$dt->source,$dt->deal_currency,$dt->deal_percent,$dt->cust_id);
                        /*if(in_array($dt->dealid, [8690,8660])){$retAmount+= SysHelper::get_aed_amount($dt->deal_currency,$dt->deal_value);}
                        else{
                                if($dt->source=="Fulfillment"){$retAmount+= SysHelper::get_aed_amount($dt->deal_currency,($dt->deal_value*20/100));}
                                else if(in_array($dt->cust_id, [2568,4258,4382,5322,7347,8144,8145,8146,3711,4089,8142])){$retAmount+= SysHelper::get_aed_amount($dt->deal_currency,($dt->deal_value*20/100));}
                                else if(in_array($dt->cust_id, [8866])){$retAmount+= SysHelper::get_aed_amount($dt->deal_currency,($dt->deal_value*30/100));}
                                else{$retAmount+= SysHelper::get_aed_amount($dt->deal_currency,$dt->deal_value);}
                        }*/
                    }
                }
                
                return SysHelper::com_curr_format($retAmount, 2, '.', '');
            }
            
        } catch (\Throwable $th) {
            return $th;
        }
    }
    public static function get_total_sales_brand_name($brand){
        try {
            
            $ret=0;
            $data = DB::table('sys_crm_deals')
            ->where('sys_crm_deals.stage',4)->whereRaw("DATE_FORMAT(sys_crm_deals.created_at, '%Y-%m') = '".date('Y-m')."'")
            //->where('sys_crm_deal_track.invoice',1)
            ->get();
            if(count($data)>0){
                foreach ($data as $id) {
                    $dataid[]=$id->id;
                }
                $dt=SysCrmQuoteItems::select('qty','price','discount','brand')
                ->join('sm_items','sm_items.id','sys_crm_quote_items.product_id')
                ->wherein('deal_id',$dataid)->where('brand',$brand)->get();
                foreach ($dt as $val) {
                    $ret += ($val->qty * $val->price) - ($val->qty * $val->discount);
                }
            }
            return SysHelper::com_curr_format($ret, 2, '.', '');            
            
        } catch (\Throwable $th) {
            return $th;
        }
    
    }
    
    public static function deal_won_date_update($dealid){
        DB::table('sys_crm_deals')->where('id', $dealid)->update(['won_date' => Carbon::now('+04:00')]);
    }
    public static function deal_invoice_date_update($dealid){
        DB::table('sys_crm_deals')->where('id', $dealid)->update(['invoice_date' => Carbon::now('+04:00')]);
    }

    public static function get_total_sales_brand_name_list_partnumber($brand){
        try {
            
            $ret=0;
            $retData=[];
            $data = DB::table('sys_crm_deals')
            ->where('sys_crm_deals.stage',4)->whereRaw("DATE_FORMAT(sys_crm_deals.created_at, '%Y-%m') = '".date('Y-m')."'")
            //->where('sys_crm_deal_track.invoice',1)
            ->get();
            if(count($data)>0){
                foreach ($data as $id) {
                    $dataid[]=$id->id;
                }
                $dt=SysCrmQuoteItems::select('qty','price','discount','brand','sm_items.part_number','deal_id','currency_id','sys_crm_quote_items.description')
                ->join('sm_items','sm_items.id','sys_crm_quote_items.product_id')
                ->wherein('deal_id',$dataid)->where('brand',$brand)->get();
                
                foreach ($dt as $val) {                    
                    $ret = ($val->qty * $val->price) - ($val->qty * $val->discount);                    
                    $amt = SysHelper::get_aed_amount($val->currency_id,$ret);
                    $retData[]=[
                        'pno' => $val->part_number,
                        'amount' => SysHelper::com_curr_format($amt, 2, '.', ''),
                        'deal_id' => $val->deal_id,
                        'description' => $val->description,
                    ];
                }
            }
            return $retData;
            
        } catch (\Throwable $th) {
            return $th;
        }
    }
    public static function get_total_sales_brand_name_3months($brand){
        try {
            
            $ret=0;
            $quarter = SysHelper::get_quarter(date('m'));
            
            $data = DB::table('sys_crm_deals')
            ->where('sys_crm_deals.stage',4)
            //->whereRaw("DATE_FORMAT(sys_crm_deals.created_at, '%Y-%m') = '".date('Y-m')."'")
            ->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') >= '".$quarter[0]."' and DATE_FORMAT(created_at, '%Y-%m-%d') <= '".$quarter[1]."'")
            //->where('sys_crm_deal_track.invoice',1)
            ->get();
            if(count($data)>0){
                foreach ($data as $id) {
                    $dataid[]=$id->id;
                }
                $dt=SysCrmQuoteItems::select('qty','price','discount','brand')
                ->join('sm_items','sm_items.id','sys_crm_quote_items.product_id')
                ->wherein('deal_id',$dataid)->where('brand',$brand)->get();
                foreach ($dt as $val) {
                    $ret += ($val->qty * $val->price) - ($val->qty * $val->discount);
                }
            }
            return SysHelper::com_curr_format($ret, 2, '.', '');            
            
        } catch (\Throwable $th) {
            return $th;
        }
    }
    public static function get_total_sales_brand_name_company($brand,$company){
        try {
            
            $ret=0;
            $data = DB::table('sys_crm_deals')
            ->where('sys_crm_deals.stage',4)->whereRaw("DATE_FORMAT(sys_crm_deals.created_at, '%Y-%m') = '".date('Y-m')."'")
            ->where('sys_crm_deals.company_id',$company)
            ->get();
            if(count($data)>0){
                foreach ($data as $id) {
                    $dataid[]=$id->id;
                }
                $dt=SysCrmQuoteItems::select('qty','price','discount','brand')
                ->join('sm_items','sm_items.id','sys_crm_quote_items.product_id')
                ->wherein('deal_id',$dataid)->where('brand',$brand)->get();
                foreach ($dt as $val) {
                    $ret += ($val->qty * $val->price) - ($val->qty * $val->discount);
                }
            }
            return SysHelper::com_curr_format($ret, 2, '.', ',');            
            
        } catch (\Throwable $th) {
            return $th;
        }
    }    

    
    public static function get_total_forcast($user_id){
        $data = DB::table('sys_crm_deals')->whereRaw("DATE_FORMAT(estimated_close_date, '%Y-%m') = '".date('Y-m')."'")->wherein('stage',[1,2,3])->wherein('owner', $user_id)->sum('deal_value');
        return SysHelper::com_curr_format($data, 2, '.', '');
    }

    
    public static function get_total_forcast_all(){
        $data2 = DB::table('sys_crm_deals')->whereRaw("DATE_FORMAT(estimated_close_date, '%Y-%m') = '".date('Y-m')."'")->wherein('stage',[1,2,3]);
        
        if(Auth::user()->role_id == 1){ //admin
            $data = $data2->get();
        }
        else if(Auth::user()->id==73){ //saleem
            $data = $data2->where('sys_crm_deals.company_id',3)->get();
        }
        else if(Auth::user()->id==33){ //jacob
            $teams= array(33,31,59);
            $data = $data2->wherein('sys_crm_deals.owner',$teams)->get();
        }                    
        else if(Auth::user()->id==27){//monica
            $teams= array(27,30,54,62);
            $data = $data2->wherein('sys_crm_deals.owner',$teams)->get();
        }
        else if(Auth::user()->id==44){ //rajiv
            $teams= array(44,45,34,32);
            $data = $data2->wherein('sys_crm_deals.owner',$teams)->get();
        }
        else{
            $data = $data2->where('sys_crm_deals.owner',Auth::user()->id)->get();
        }
        $retAmount=0;
        if(count($data)>0){
            foreach($data as $dt){
                $retAmount+= SysHelper::get_aed_amount($dt->deal_currency,$dt->deal_value);
            }
        }
        return SysHelper::com_curr_format($retAmount, 2, '.', ',');
    }
    public static function get_total_lost_all(){
        $data2 = DB::table('sys_crm_deals')->whereRaw("DATE_FORMAT(estimated_close_date, '%Y-%m') = '".date('Y-m')."'")->wherein('stage',[5,6]);
        
        if(Auth::user()->role_id == 1){ //admin
            $data = $data2->get();
        }
        else if(Auth::user()->id==73){ //saleem
            $data = $data2->where('sys_crm_deals.company_id',3)->get();
        }
        else if(Auth::user()->id==33){ //jacob
            $teams= array(33,31,59);
            $data = $data2->wherein('sys_crm_deals.owner',$teams)->get();
        }                    
        else if(Auth::user()->id==27){//monica
            $teams= array(27,30,54,62);
            $data = $data2->wherein('sys_crm_deals.owner',$teams)->get();
        }
        else if(Auth::user()->id==44){ //rajiv
            $teams= array(44,45,34,32);
            $data = $data2->wherein('sys_crm_deals.owner',$teams)->get();
        }
        else{
            $data = $data2->where('sys_crm_deals.owner',Auth::user()->id)->get();
        }
        $retAmount=0;
        if(count($data)>0){
            foreach($data as $dt){
                $retAmount+= SysHelper::get_aed_amount($dt->deal_currency,$dt->deal_value);
            }
        }
        return SysHelper::com_curr_format($retAmount, 2, '.', ',');
    }

    public static function get_total_revenue_all_by_company($from_month,$to_month,$company,$ps_amc_deal_id){
 


        // if(in_array(36,$user_id)){ //jacob
        //     //$user=[36,25,51,31,27];
        //     $user=DB::table('sm_staffs')->where('main_company',5)->pluck('user_id');
        // } elseif(in_array(15,$user_id)){ //subeesh
        //     $user=[15,40,34,46,44,63,33,30,45];
        // } elseif(in_array(58,$user_id)){ //Thaiab Mohammed
        //     $user=[58,59,60,62];
        // } elseif(in_array(18,$user_id)){ //Prajeesh Prabhakar
        //     $user=[18,19,20];
        // } elseif(in_array(48,$user_id)){ //Parveen Sheik Asif
        //     $user=[48,39,71];
        // } else { $user=$user_id; }

        $data1=[];
        $data2=[];

        //$pid = [35657,35716,26328,35710,36223,26324,35761,12587];
        //$extdeal_val = DB::table('sys_crm_quote_items')->select(db::raw('sum(price*qty-discount as amt)'))->wherein('product_id',$pid)->get();
        
            $data1 = DB::table('sys_crm_deals')->select('sys_crm_deals.id as dealid','deal_value','deal_currency','source','cust_id','deal_percent','deal_profit')
            ->leftjoin('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_id','sys_crm_deals.id')
            ->leftjoin('sys_crm_deal_track_approval_receivables','sys_crm_deal_track_approval_receivables.deal_id','sys_crm_deals.id');

            $query = SysSalesInvoice::select(DB::raw('sys_sales_invoice.*, (SELECT max(debit_amount) FROM sys_chartofaccounts_transaction WHERE transaction_type="salesinvoice" and transaction_no=sys_sales_invoice.doc_number and account_id=sys_sales_invoice.customer) AS amount, (SELECT max(code) FROM sys_crm_deals WHERE id=sys_sales_invoice.deal_id) AS code, (SELECT max(deal_profit) FROM sys_crm_deals WHERE id=sys_sales_invoice.deal_id) AS deal_profit, (SELECT max(deal_value) FROM sys_crm_deals WHERE id=sys_sales_invoice.deal_id) AS deal_value, (SELECT max(deal_currency) FROM sys_crm_deals WHERE id=sys_sales_invoice.deal_id) AS deal_currency'),DB::raw('(SELECT SUM(vatamount) FROM sys_sales_invoice_items WHERE si_id = sys_sales_invoice.id) AS total_vatamount'),DB::raw('(SELECT SUM(taxableamount) FROM sys_sales_invoice_items WHERE si_id = sys_sales_invoice.id) AS total_taxableamount'),DB::raw('(SELECT SUM(value) FROM sys_sales_invoice_items WHERE si_id = sys_sales_invoice.id) AS value'),DB::raw('(SELECT SUM(discount) FROM sys_sales_invoice_items WHERE si_id = sys_sales_invoice.id) AS discount'));

            


            //->where('sys_crm_deals.is_partial_invoice',0);
            if($from_month !="" && $to_month !=""){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '".date('Y-m-d', strtotime($from_month))."' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($to_month))."'");
                $query->whereRaw("DATE_FORMAT(doc_date, '%Y-%m-%d') >= '".date('Y-m-d', strtotime($from_month))."' and DATE_FORMAT(doc_date, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($to_month))."'");
            }
            if($from_month !="" && $to_month ==""){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') = '".date('Y-m-d', strtotime($from_month))."'");
                $query->whereRaw("DATE_FORMAT(doc_date, '%Y-%m-%d') = '".date('Y-m-d', strtotime($from_month))."'");
            }
            $data1->where('sys_crm_deals.stage',4)            
            //->where('sys_crm_deal_track_approval_invoice.status',1);
            ->where(function ($query) {
                $query->orwhere('sys_crm_deal_track_approval_invoice.status',1)
                ->orwhere('sys_crm_deal_track_approval_receivables.status',1);
            });
            
            $query->where('status',1);
            //->wherein('sys_crm_deals.company_id',$company);
            if($company != 1){
                $data1->where('sys_crm_deals.company_id',$company);
            }

            if(count($ps_amc_deal_id)>0){                
                $query->wherein('sys_sales_invoice.deal_id',$ps_amc_deal_id);
            }
            if($company != 1){
                $query->where('company_id',$company);
            }
        
               
        $retValue=0.00; $retProfit=0.00; $retActual=0.00;
        
            //$dataA = $data1->get();
            $dataA = $query->get();
            if(count($dataA)>0){
                foreach($dataA as $dt){
                    /* old
                    $retValue+= SysHelper::get_deal_value($dt->deal_value,$dt->source,$dt->deal_currency,$dt->deal_percent,$dt->cust_id);
                    $retProfit+= SysHelper::get_gp_value($dt->deal_profit,$dt->deal_currency);
                    $retActual+= SysHelper::get_deal_value_actual($dt->deal_value,$dt->deal_currency);
                    old */
                    $retValue+= SysHelper::get_gp_value($dt->total_taxableamount-$dt->deal_discount,$dt->deal_currency);
                    $retProfit+= SysHelper::get_gp_value($dt->deal_profit,$dt->deal_currency);
                    $retActual+= SysHelper::get_gp_value($dt->total_taxableamount-$dt->deal_discount,$dt->deal_currency);


                    /*if(in_array($dt->dealid, [8690,8660])){$retAmount+= SysHelper::get_aed_amount($dt->deal_currency,$dt->deal_value);}
                    else{
                    if($dt->source=="Fulfillment"){$retAmount+= SysHelper::get_aed_amount($dt->deal_currency,($dt->deal_value*20/100));}
                    else if(in_array($dt->cust_id, [2568,4258,4382,5322,7347,8144,8145,8146,3711,4089,8142])){$retAmount+= SysHelper::get_aed_amount($dt->deal_currency,($dt->deal_value*20/100));}
                    else if(in_array($dt->cust_id, [8866])){$retAmount+= SysHelper::get_aed_amount($dt->deal_currency,($dt->deal_value*30/100));}
                    else{$retAmount+= SysHelper::get_aed_amount($dt->deal_currency,$dt->deal_value);}                
                    }*/
                }
            }
        

        //$sf = $data->where('source','Fulfillment')->sum('deal_value')*20/100;
        //$data = $data1->sum('deal_value')+$data2->sum('deal_value');
        return [$retValue, $retProfit, $retActual];
    }

    public static function get_total_revenue_all_by_user($user_id,$from_month,$to_month,$company,$ps_amc_deal_id)
    {

        try {
 
        $user=DB::table('sm_staffs')->wherein('user_id',$user_id)->pluck('combind_user_id');
        if($user[0] == ""){
            $user = $user_id;
        }
        else {            
            $user = array_map('intval', explode(',', $user));
        }


        // if(in_array(36,$user_id)){ //jacob
        //     //$user=[36,25,51,31,27];
        //     $user=DB::table('sm_staffs')->where('main_company',5)->pluck('user_id');
        // } elseif(in_array(15,$user_id)){ //subeesh
        //     $user=[15,40,34,46,44,63,33,30,45];
        // } elseif(in_array(58,$user_id)){ //Thaiab Mohammed
        //     $user=[58,59,60,62];
        // } elseif(in_array(18,$user_id)){ //Prajeesh Prabhakar
        //     $user=[18,19,20];
        // } elseif(in_array(48,$user_id)){ //Parveen Sheik Asif
        //     $user=[48,39,71];
        // } else { $user=$user_id; }

        $data1=[];
        $data2=[];

        //$pid = [35657,35716,26328,35710,36223,26324,35761,12587];
        //$extdeal_val = DB::table('sys_crm_quote_items')->select(db::raw('sum(price*qty-discount as amt)'))->wherein('product_id',$pid)->get();
        
            $data1 = DB::table('sys_crm_deals')->select('sys_crm_deals.id as dealid','deal_value','deal_currency','source','cust_id','deal_percent','deal_profit')
            ->leftjoin('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_id','sys_crm_deals.id')
            ->leftjoin('sys_crm_deal_track_approval_receivables','sys_crm_deal_track_approval_receivables.deal_id','sys_crm_deals.id');

            $query = SysSalesInvoice::select(DB::raw('sys_sales_invoice.*, (SELECT max(debit_amount) FROM sys_chartofaccounts_transaction WHERE transaction_type="salesinvoice" and transaction_no=sys_sales_invoice.doc_number and account_id=sys_sales_invoice.customer) AS amount, (SELECT max(code) FROM sys_crm_deals WHERE id=sys_sales_invoice.deal_id) AS code, (SELECT max(deal_profit) FROM sys_crm_deals WHERE id=sys_sales_invoice.deal_id) AS deal_profit, (SELECT max(deal_value) FROM sys_crm_deals WHERE id=sys_sales_invoice.deal_id) AS deal_value, (SELECT max(deal_currency) FROM sys_crm_deals WHERE id=sys_sales_invoice.deal_id) AS deal_currency'),DB::raw('(SELECT SUM(vatamount) FROM sys_sales_invoice_items WHERE si_id = sys_sales_invoice.id) AS total_vatamount'),DB::raw('(SELECT SUM(taxableamount) FROM sys_sales_invoice_items WHERE si_id = sys_sales_invoice.id) AS total_taxableamount'),DB::raw('(SELECT SUM(value) FROM sys_sales_invoice_items WHERE si_id = sys_sales_invoice.id) AS value'),DB::raw('(SELECT SUM(discount) FROM sys_sales_invoice_items WHERE si_id = sys_sales_invoice.id) AS discount'));

            


            //->where('sys_crm_deals.is_partial_invoice',0);
            if($from_month !="" && $to_month !=""){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '".date('Y-m-d', strtotime($from_month))."' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($to_month))."'");
                $query->whereRaw("DATE_FORMAT(doc_date, '%Y-%m-%d') >= '".date('Y-m-d', strtotime($from_month))."' and DATE_FORMAT(doc_date, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($to_month))."'");
            }
            if($from_month !="" && $to_month ==""){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') = '".date('Y-m-d', strtotime($from_month))."'");
                $query->whereRaw("DATE_FORMAT(doc_date, '%Y-%m-%d') = '".date('Y-m-d', strtotime($from_month))."'");
            }
            $data1->where('sys_crm_deals.stage',4)            
            //->where('sys_crm_deal_track_approval_invoice.status',1);
            ->where(function ($query) {
                $query->orwhere('sys_crm_deal_track_approval_invoice.status',1)
                ->orwhere('sys_crm_deal_track_approval_receivables.status',1);
            });
            
            $query->where('status',1);
            if(count($ps_amc_deal_id)>0){                
                $query->wherein('sys_sales_invoice.deal_id',$ps_amc_deal_id);
            }
            $data1->wherein('sys_crm_deals.owner',$user);
            
            $query->wherein('company_id',$company);

            $query->wherein('sales_man',$user);
        
               
        $retValue=0.00; $retProfit=0.00; $retActual=0.00;
        
            //$dataA = $data1->get();
            $dataA = $query->get();
            //return $dataA;
            if(count($dataA)>0){
                foreach($dataA as $dt){
                    /* old
                    $retValue+= SysHelper::get_deal_value($dt->deal_value,$dt->source,$dt->deal_currency,$dt->deal_percent,$dt->cust_id);
                    $retProfit+= SysHelper::get_gp_value($dt->deal_profit,$dt->deal_currency);
                    $retActual+= SysHelper::get_deal_value_actual($dt->deal_value,$dt->deal_currency);
                    old */
                    $retValue+= SysHelper::get_gp_value($dt->total_taxableamount-$dt->deal_discount,$dt->deal_currency);
                    $retProfit+= SysHelper::get_gp_value($dt->deal_profit,$dt->deal_currency);
                    $retActual+= SysHelper::get_gp_value($dt->total_taxableamount-$dt->deal_discount,$dt->deal_currency);


                    /*if(in_array($dt->dealid, [8690,8660])){$retAmount+= SysHelper::get_aed_amount($dt->deal_currency,$dt->deal_value);}
                    else{
                    if($dt->source=="Fulfillment"){$retAmount+= SysHelper::get_aed_amount($dt->deal_currency,($dt->deal_value*20/100));}
                    else if(in_array($dt->cust_id, [2568,4258,4382,5322,7347,8144,8145,8146,3711,4089,8142])){$retAmount+= SysHelper::get_aed_amount($dt->deal_currency,($dt->deal_value*20/100));}
                    else if(in_array($dt->cust_id, [8866])){$retAmount+= SysHelper::get_aed_amount($dt->deal_currency,($dt->deal_value*30/100));}
                    else{$retAmount+= SysHelper::get_aed_amount($dt->deal_currency,$dt->deal_value);}                
                    }*/
                }
            }
        

        //$sf = $data->where('source','Fulfillment')->sum('deal_value')*20/100;
        //$data = $data1->sum('deal_value')+$data2->sum('deal_value');
        return [$retValue, $retProfit, $retActual];
        } catch (\Throwable $th) {
            return $th;
        }
    }
    public static function get_total_revenue_all_by_user_nocombind($user_id,$from_month,$to_month,$company_list,$ps_amc_deal_id){
 
        $user=$user_id;

        $data1=[];
        $data2=[];

        //$pid = [35657,35716,26328,35710,36223,26324,35761,12587];
        //$extdeal_val = DB::table('sys_crm_quote_items')->select(db::raw('sum(price*qty-discount as amt)'))->wherein('product_id',$pid)->get();
        
            $data1 = DB::table('sys_crm_deals')->select('sys_crm_deals.id as dealid','deal_value','deal_currency','source','cust_id','deal_percent','deal_profit')
            ->leftjoin('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_id','sys_crm_deals.id')
            ->leftjoin('sys_crm_deal_track_approval_receivables','sys_crm_deal_track_approval_receivables.deal_id','sys_crm_deals.id');
            //->where('sys_crm_deals.is_partial_invoice',0);
            
            $query = SysSalesInvoice::select(DB::raw('sys_sales_invoice.*, (SELECT max(debit_amount) FROM sys_chartofaccounts_transaction WHERE transaction_type="salesinvoice" and transaction_no=sys_sales_invoice.doc_number and account_id=sys_sales_invoice.customer) AS amount, (SELECT max(code) FROM sys_crm_deals WHERE id=sys_sales_invoice.deal_id) AS code, (SELECT max(deal_profit) FROM sys_crm_deals WHERE id=sys_sales_invoice.deal_id) AS deal_profit, (SELECT max(deal_value) FROM sys_crm_deals WHERE id=sys_sales_invoice.deal_id) AS deal_value, (SELECT max(deal_currency) FROM sys_crm_deals WHERE id=sys_sales_invoice.deal_id) AS deal_currency'),DB::raw('(SELECT SUM(vatamount) FROM sys_sales_invoice_items WHERE si_id = sys_sales_invoice.id) AS total_vatamount'),DB::raw('(SELECT SUM(taxableamount) FROM sys_sales_invoice_items WHERE si_id = sys_sales_invoice.id) AS total_taxableamount'),DB::raw('(SELECT SUM(value) FROM sys_sales_invoice_items WHERE si_id = sys_sales_invoice.id) AS value'),DB::raw('(SELECT SUM(discount) FROM sys_sales_invoice_items WHERE si_id = sys_sales_invoice.id) AS discount'));

            if($from_month !="" && $to_month !=""){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '".date('Y-m-d', strtotime($from_month))."' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($to_month))."'");
                $query->whereRaw("DATE_FORMAT(doc_date, '%Y-%m-%d') >= '".date('Y-m-d', strtotime($from_month))."' and DATE_FORMAT(doc_date, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($to_month))."'");
            }
            if($from_month !="" && $to_month ==""){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') = '".date('Y-m-d', strtotime($from_month))."'");
                $query->whereRaw("DATE_FORMAT(doc_date, '%Y-%m-%d') = '".date('Y-m-d', strtotime($from_month))."'");
            }
            $data1->where('sys_crm_deals.stage',4)
            
            //->where('sys_crm_deal_track_approval_invoice.status',1);
            ->where(function ($query) {
                $query->orwhere('sys_crm_deal_track_approval_invoice.status',1)
                ->orwhere('sys_crm_deal_track_approval_receivables.status',1);
            });


            $query->wherein('company_id',$company_list);

            $data1->wherein('sys_crm_deals.owner',$user);
            $query->wherein('sales_man',$user);
            if(count($ps_amc_deal_id)>0){                
                $query->wherein('sys_sales_invoice.deal_id',$ps_amc_deal_id);
            }
        
               
        $retValue=0.00; $retProfit=0.00; $retActual=0.00;
        
            //$dataA = $data1->get();
            $dataA = $query->get();
            if(count($dataA)>0){
                foreach($dataA as $dt){
                    /*$retValue+= SysHelper::get_deal_value($dt->deal_value,$dt->source,$dt->deal_currency,$dt->deal_percent,$dt->cust_id);
                    $retProfit+= SysHelper::get_gp_value($dt->deal_profit,$dt->deal_currency);
                    $retActual+= SysHelper::get_deal_value_actual($dt->deal_value,$dt->deal_currency);*/
                    $retValue+= SysHelper::get_gp_value($dt->total_taxableamount,$dt->deal_currency);
                    $retProfit+= SysHelper::get_gp_value($dt->deal_profit,$dt->deal_currency);
                    $retActual+= SysHelper::get_gp_value($dt->total_taxableamount,$dt->deal_currency);

                    /*if(in_array($dt->dealid, [8690,8660])){$retAmount+= SysHelper::get_aed_amount($dt->deal_currency,$dt->deal_value);}
                    else{
                    if($dt->source=="Fulfillment"){$retAmount+= SysHelper::get_aed_amount($dt->deal_currency,($dt->deal_value*20/100));}
                    else if(in_array($dt->cust_id, [2568,4258,4382,5322,7347,8144,8145,8146,3711,4089,8142])){$retAmount+= SysHelper::get_aed_amount($dt->deal_currency,($dt->deal_value*20/100));}
                    else if(in_array($dt->cust_id, [8866])){$retAmount+= SysHelper::get_aed_amount($dt->deal_currency,($dt->deal_value*30/100));}
                    else{$retAmount+= SysHelper::get_aed_amount($dt->deal_currency,$dt->deal_value);}                
                    }*/
                }
            }
        

        //$sf = $data->where('source','Fulfillment')->sum('deal_value')*20/100;
        //$data = $data1->sum('deal_value')+$data2->sum('deal_value');
        return [$retValue, $retProfit, $retActual];
    }

    public static function get_internal_external_sales_report($user_id, $from_month, $to_month, $company_id)
    {
        try {
            
            $in_value=0; $in_profit=0;
            $ex_value=0; $ex_profit=0;
            
            $query = SysSalesInvoice::select(DB::raw('sys_sales_invoice.*, (SELECT max(deal_profit) FROM sys_crm_deals WHERE id=sys_sales_invoice.deal_id) AS deal_profit, (SELECT max(deal_value) FROM sys_crm_deals WHERE id=sys_sales_invoice.deal_id) AS deal_value, (SELECT max(deal_currency) FROM sys_crm_deals WHERE id=sys_sales_invoice.deal_id) AS deal_currency'),DB::raw('(SELECT SUM(taxableamount) FROM sys_sales_invoice_items WHERE si_id = sys_sales_invoice.id) AS total_taxableamount'),DB::raw('(SELECT internal FROM sys_chartofaccounts WHERE id = sys_sales_invoice.customer) AS customer_internal'));
            
            if($company_id != 1){
                //$query->where('company_id',$company_id);   
            }
            if($from_month !="" && $to_month !=""){
                $query->whereRaw("DATE_FORMAT(doc_date, '%Y-%m-%d') >= '".date('Y-m-d', strtotime($from_month))."' and DATE_FORMAT(doc_date, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($to_month))."'");
            } 


            $s_data = $query->where('sales_man',$user_id)->get();
            
            if(count($s_data)>0){
                $nc=[];
                foreach ($s_data as $dt) {
                    if($dt->customer_internal == 1){
                        $in_value += SysHelper::get_gp_value($dt->total_taxableamount,$dt->deal_currency);
                        $in_profit += SysHelper::get_gp_value($dt->deal_profit,$dt->deal_currency);
                    } else {
                        $ex_value += SysHelper::get_gp_value($dt->total_taxableamount,$dt->deal_currency);
                        $ex_profit += SysHelper::get_gp_value($dt->deal_profit,$dt->deal_currency);
                    }
                }
            }
            return [$in_value, $in_profit, $ex_value, $ex_profit];            
        } catch (\Throwable $th) {            
            return [$in_value, $in_profit, $ex_value, $ex_profit];  
            //return $th;
        }

    }

    public static function internal_transfer_customer_id($customer_id, $company_id)
    {
        $mapping = [
            2 => 6262,   // SYSCOM FZE
            3 => 13100,  // SYSCOM DISTRIBUTIONS LLC BRANCH ABU DHABI 1
            4 => 6259,   // SYSCOM DISTRIBUTION LTD
            5 => 4105,   // SYSCOM IT SOLUTIONS LLC
            6 => 6261,   // SYSCOM DISTRIBUTIONS LLC
            7 => 6217,   // STACK LINK UK LTD
            8 => 6250,   // SUPREME SYSTEM TRADING ESTABLISHMENT
            9 => 8211,   // SYSCOM DISTRIBUTION WLL
            10 => 9373,   // SUPREME SYSTEM DISTRIBUTORS SPC
            // 11 => 9373, // SYSCOM DISTRIBUTION LIMITED (intentionally skipped)
            12 => 3868,   // TRIANGLE SYSTEMS LLC
        ];

        if (isset($mapping[$company_id]) && $mapping[$company_id] == $customer_id) {
            return 'selected';
        }
        return '';
    }
    public static function get_internal_transfer_customer_id($company_id)
    {
        $mapping = [
            2 => 7075,   // SYSCOM FZE
            3 => 12603,  // SYSCOM DISTRIBUTIONS LLC BRANCH ABU DHABI 1
            4 => 7072,   // SYSCOM DISTRIBUTION LTD
            5 => 4918,   // SYSCOM IT SOLUTIONS LLC
            6 => 7074,   // SYSCOM DISTRIBUTIONS LLC
            7 => 7030,   // STACK LINK UK LTD
            8 => 7063,   // SUPREME SYSTEM TRADING ESTABLISHMENT
            9 => 9084,   // SYSCOM DISTRIBUTION WLL
            10 => 10331,   // SUPREME SYSTEM DISTRIBUTORS SPC
            11 => 0, // SYSCOM DISTRIBUTION LIMITED (intentionally skipped)
            12 => 4681,   // TRIANGLE SYSTEMS LLC
        ];

        // Return the customer_id if company_id exists in the mapping
        return $mapping[$company_id] ?? '';
    }
    public static function get_internal_transfer_company_id($supplier_id)
    {
        $mapping = [
            8288 => 2,   // SYSCOM FZE
            7710 => 3,  // SYSCOM DISTRIBUTIONS LLC BRANCH ABU DHABI 1
            8287 => 4,   // SYSCOM DISTRIBUTION LTD
            8293 => 5,   // SYSCOM IT SOLUTIONS LLC
            229 => 6,   // SYSCOM DISTRIBUTIONS LLC
            8292 => 7,   // STACK LINK UK LTD
            8194 => 8,   // SUPREME SYSTEM TRADING ESTABLISHMENT
            8291 => 9,   // SYSCOM DISTRIBUTION WLL
            8290 => 10,   // SUPREME SYSTEM DISTRIBUTORS SPC
            0 => 11, // SYSCOM DISTRIBUTION LIMITED (intentionally skipped)
            12407 => 12,   // TRIANGLE SYSTEMS LLC
        ];

        // Return the customer_id if company_id exists in the mapping
        return $mapping[$supplier_id] ?? '';
    }

    public static function calculateSalesTarget($userId, $startDate, $endDate,$filter_by,$company_id)
    {
        try {
            $start_date = date('Y-m', strtotime($startDate));
            $end_date = date('Y-m', strtotime($endDate));
            // $target = SysCrmSalesTarget::select(DB::raw('COALESCE(sum(revenue_target_monthly), 0) as rev_amount'),DB::raw('COALESCE(sum(gp_target_monthly), 0) as gp_amount'))
            // ->wherein('user_id', $userId)->whereBetween('target_month_from', [$start_date, $end_date])->orderby('target_month_from', 'desc')->first();
            $target_query = SysCrmSalesTarget::select(DB::raw('COALESCE(sum(revenue_target_monthly), 0) as rev_amount'),DB::raw('COALESCE(sum(gp_target_monthly), 0) as gp_amount'));
            if($userId[0]!=0){
                $target_query->wherein('user_id', $userId);
            }
            $target = $target_query->whereRaw("DATE_FORMAT(target_month_from, '%Y-%m') <= '".date('Y-m', strtotime($end_date))."'")
            ->when($company_id != 1, function ($q) use ($company_id) {
                return $q->where('company_id', $company_id);
            })
            ->orderby('target_month_from','desc')->first();
            
            if($filter_by=="this_month"){
                return ['rev_amount' => SysHelper::com_curr_format($target['rev_amount'],'2','.',''), 'gp_amount' => SysHelper::com_curr_format($target['gp_amount'],'2','.','')];
            }
            if($filter_by=="today"){
                return ['rev_amount' => SysHelper::com_curr_format($target['rev_amount']/28,'2','.',''), 'gp_amount' => SysHelper::com_curr_format($target['gp_amount']/28,'2','.','')];
            }
            if($filter_by=="this_week"){
                return ['rev_amount' => SysHelper::com_curr_format($target['rev_amount']/28*7,'2','.',''), 'gp_amount' => SysHelper::com_curr_format($target['gp_amount']/28*7,'2','.','')];
            }
            if($filter_by=="last_week"){
                return ['rev_amount' => SysHelper::com_curr_format($target['rev_amount']/28*7,'2','.',''), 'gp_amount' => SysHelper::com_curr_format($target['gp_amount']/28*7,'2','.','')];
            }
            if($filter_by=="last_month"){
                return ['rev_amount' => SysHelper::com_curr_format($target['rev_amount'],'2','.',''), 'gp_amount' => SysHelper::com_curr_format($target['gp_amount'],'2','.','')];
            }
            if($filter_by=="this_quarter"){
                return ['rev_amount' => SysHelper::com_curr_format($target['rev_amount']*3,'2','.',''), 'gp_amount' => SysHelper::com_curr_format($target['gp_amount']*3,'2','.','')];
            }
            if($filter_by=="pre_quarter"){
                return ['rev_amount' => SysHelper::com_curr_format($target['rev_amount']*3,'2','.',''), 'gp_amount' => SysHelper::com_curr_format($target['gp_amount']*3,'2','.','')];
            }
            if($filter_by=="this_year"){
                return ['rev_amount' => SysHelper::com_curr_format($target['rev_amount']*12,'2','.',''), 'gp_amount' => SysHelper::com_curr_format($target['gp_amount']*12,'2','.','')];
            }
            if($filter_by=="last_year"){
                return ['rev_amount' => SysHelper::com_curr_format($target['rev_amount']*12,'2','.',''), 'gp_amount' => SysHelper::com_curr_format($target['gp_amount']*12,'2','.','')];
            }
            if($filter_by==""){
                $start_date = date('Y-m-d', strtotime($startDate));
                $end_date = date('Y-m-d', strtotime($endDate));
                $start = new DateTime($start_date);
                $end = new DateTime($end_date);
                $diff = $start->diff($end);
                $month_diff = ($diff->y * 12) + $diff->m;
                $include_end_month = true;
                $month_count = $include_end_month ? $month_diff + 1 : $month_diff;
                return ['rev_amount' => SysHelper::com_curr_format($target['rev_amount']*$month_count,'2','.',''), 'gp_amount' => SysHelper::com_curr_format($target['gp_amount']*$month_count,'2','.','')];
            }


            if(isset($target)){
                    return $target;
            } else {
                $target = SysCrmSalesTarget::select(DB::raw('COALESCE(sum(revenue_target_monthly), 0) as rev_amount'),DB::raw('COALESCE(sum(gp_target_monthly), 0) as gp_amount'))
                ->wherein('user_id', $userId)->where('company_id',$company_id)->whereRaw("DATE_FORMAT(target_month_from, '%Y-%m') <= '".date('Y-m', strtotime($end_date))."'")->orderby('target_month_from','desc')->first();
                return $target;
            }
        } catch (\Throwable $th) {
            return ["rev_amount"=>"0.00","gp_amount"=>"0.00"];
        }
    }


    public static function get_total_revenue_all_by_user_sum($user_id,$from_month,$to_month,$company_list,$ps_amc_deal_id){
        $user=$user_id;
        $data1=[];
        $data2=[];

        //$pid = [35657,35716,26328,35710,36223,26324,35761,12587];
        //$extdeal_val = DB::table('sys_crm_quote_items')->select(db::raw('sum(price*qty-discount as amt)'))->wherein('product_id',$pid)->get();
        
            $data1 = DB::table('sys_crm_deals')->select('sys_crm_deals.id as dealid','deal_value','deal_currency','source','cust_id','deal_percent','deal_profit')
            ->leftjoin('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_id','sys_crm_deals.id')
            ->leftjoin('sys_crm_deal_track_approval_receivables','sys_crm_deal_track_approval_receivables.deal_id','sys_crm_deals.id');
            //->where('sys_crm_deals.is_partial_invoice',0);
            if($from_month !="" && $to_month !=""){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '".date('Y-m-d', strtotime($from_month))."' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($to_month))."'");
            }
            if($from_month !="" && $to_month ==""){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') = '".date('Y-m-d', strtotime($from_month))."'");
            }
            $data1->where('sys_crm_deals.stage',4)
            //->where('sys_crm_deal_track_approval_invoice.status',1);
            ->where(function ($query) {
                $query->orwhere('sys_crm_deal_track_approval_invoice.status',1)
                ->orwhere('sys_crm_deal_track_approval_receivables.status',1);
            });
            $data1->wherein('sys_crm_deals.company_id',$company_list);
            $data1->wherein('sys_crm_deals.owner',$user);
            if(count($ps_amc_deal_id)>0){                
                $data1->wherein('sys_crm_deals.id',$ps_amc_deal_id);
            }
        
               
        $retValue=0.00; $retProfit=0.00; $retActual=0.00;
        
            $dataA = $data1->get();
            if(count($dataA)>0){
                foreach($dataA as $dt){
                    $retValue+= SysHelper::get_deal_value($dt->deal_value,$dt->source,$dt->deal_currency,$dt->deal_percent,$dt->cust_id);
                    $retProfit+= SysHelper::get_gp_value($dt->deal_profit,$dt->deal_currency);
                    $retActual+= SysHelper::get_deal_value_actual($dt->deal_value,$dt->deal_currency);
                    /*if(in_array($dt->dealid, [8690,8660])){$retAmount+= SysHelper::get_aed_amount($dt->deal_currency,$dt->deal_value);}
                    else{
                    if($dt->source=="Fulfillment"){$retAmount+= SysHelper::get_aed_amount($dt->deal_currency,($dt->deal_value*20/100));}
                    else if(in_array($dt->cust_id, [2568,4258,4382,5322,7347,8144,8145,8146,3711,4089,8142])){$retAmount+= SysHelper::get_aed_amount($dt->deal_currency,($dt->deal_value*20/100));}
                    else if(in_array($dt->cust_id, [8866])){$retAmount+= SysHelper::get_aed_amount($dt->deal_currency,($dt->deal_value*30/100));}
                    else{$retAmount+= SysHelper::get_aed_amount($dt->deal_currency,$dt->deal_value);}                
                    }*/
                }
            }
        

        //$sf = $data->where('source','Fulfillment')->sum('deal_value')*20/100;
        //$data = $data1->sum('deal_value')+$data2->sum('deal_value');
        return [$retValue, $retProfit, $retActual];
    }
    public static function get_total_revenue_all_by_company_sum($from_month,$to_month,$company,$ps_amc_deal_id){
        $data1=[];
        $data2=[];

        //$pid = [35657,35716,26328,35710,36223,26324,35761,12587];
        //$extdeal_val = DB::table('sys_crm_quote_items')->select(db::raw('sum(price*qty-discount as amt)'))->wherein('product_id',$pid)->get();
        
            $data1 = DB::table('sys_crm_deals')->select('sys_crm_deals.id as dealid','deal_value','deal_currency','source','cust_id','deal_percent','deal_profit')
            ->leftjoin('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_id','sys_crm_deals.id')
            ->leftjoin('sys_crm_deal_track_approval_receivables','sys_crm_deal_track_approval_receivables.deal_id','sys_crm_deals.id');
            //->where('sys_crm_deals.is_partial_invoice',0);
            if($from_month !="" && $to_month !=""){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '".date('Y-m-d', strtotime($from_month))."' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($to_month))."'");
            }
            if($from_month !="" && $to_month ==""){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') = '".date('Y-m-d', strtotime($from_month))."'");
            }
            $data1->where('sys_crm_deals.stage',4)
            //->where('sys_crm_deal_track_approval_invoice.status',1);
            ->where(function ($query) {
                $query->orwhere('sys_crm_deal_track_approval_invoice.status',1)
                ->orwhere('sys_crm_deal_track_approval_receivables.status',1);
            });
            //->wherein('sys_crm_deals.company_id',$company);
            if($company != 1){
                $data1->where('sys_crm_deals.company_id',$company);
            }
            if(count($ps_amc_deal_id)>0){                
                $data1->wherein('sys_crm_deals.id',$ps_amc_deal_id);
            }
        
               
        $retValue=0.00; $retProfit=0.00; $retActual=0.00;
        
            $dataA = $data1->get();
            if(count($dataA)>0){
                foreach($dataA as $dt){
                    $retValue+= SysHelper::get_deal_value($dt->deal_value,$dt->source,$dt->deal_currency,$dt->deal_percent,$dt->cust_id);
                    $retProfit+= SysHelper::get_gp_value($dt->deal_profit,$dt->deal_currency);
                    $retActual+= SysHelper::get_deal_value_actual($dt->deal_value,$dt->deal_currency);
                    /*if(in_array($dt->dealid, [8690,8660])){$retAmount+= SysHelper::get_aed_amount($dt->deal_currency,$dt->deal_value);}
                    else{
                    if($dt->source=="Fulfillment"){$retAmount+= SysHelper::get_aed_amount($dt->deal_currency,($dt->deal_value*20/100));}
                    else if(in_array($dt->cust_id, [2568,4258,4382,5322,7347,8144,8145,8146,3711,4089,8142])){$retAmount+= SysHelper::get_aed_amount($dt->deal_currency,($dt->deal_value*20/100));}
                    else if(in_array($dt->cust_id, [8866])){$retAmount+= SysHelper::get_aed_amount($dt->deal_currency,($dt->deal_value*30/100));}
                    else{$retAmount+= SysHelper::get_aed_amount($dt->deal_currency,$dt->deal_value);}                
                    }*/
                }
            }
        

        //$sf = $data->where('source','Fulfillment')->sum('deal_value')*20/100;
        //$data = $data1->sum('deal_value')+$data2->sum('deal_value');
        return [$retValue, $retProfit, $retActual];
    }

    public static function get_total_revenue_actual_all_by_user($user_id,$from_month,$to_month,$company_list,$ps_amc_deal_id){

            $user=$user_id;
        $data1=[];
        $data2=[];
        
            $data1 = DB::table('sys_crm_deals')->select('deal_value','deal_currency','source','cust_id')
            ->leftjoin('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_id','sys_crm_deals.id')
            ->leftjoin('sys_crm_deal_track_approval_receivables','sys_crm_deal_track_approval_receivables.deal_id','sys_crm_deals.id');
            //->where('sys_crm_deals.is_partial_invoice',0);
            if($from_month !="" && $to_month !=""){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '".date('Y-m-d', strtotime($from_month))."' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($to_month))."'");
            }
            if($from_month !="" && $to_month ==""){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') = '".date('Y-m-d', strtotime($from_month))."'");
            }
            $data1->where('sys_crm_deals.stage',4)
            //->where('sys_crm_deal_track_approval_invoice.status',1);
            ->where(function ($query) {
                $query->orwhere('sys_crm_deal_track_approval_invoice.status',1)
                ->orwhere('sys_crm_deal_track_approval_receivables.status',1);
            });
            $data1->wherein('sys_crm_deals.company_id',$company_list);
            $data1->wherein('sys_crm_deals.owner',$user);
            if(count($ps_amc_deal_id)>0){                
                $data1->wherein('sys_crm_deals.id',$ps_amc_deal_id);
            }

        $retAmount=0.00;
            $dataA = $data1->get();
            if(count($dataA)>0){
                foreach($dataA as $dt){
                    $retAmount+= SysHelper::get_aed_amount($dt->deal_currency,$dt->deal_value);
                }
            }

        //$sf = $data->where('source','Fulfillment')->sum('deal_value')*20/100;
        //$data = $data1->sum('deal_value')+$data2->sum('deal_value');
        return SysHelper::com_curr_format($retAmount, 2, '.', '');
    }

    public static function get_total_revenue_actual_all_by_company($from_month,$to_month,$company,$ps_amc_deal_id){

    $data1=[];
    $data2=[];
    
        $data1 = DB::table('sys_crm_deals')->select('deal_value','deal_currency','source','cust_id')
        ->leftjoin('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_id','sys_crm_deals.id')
        ->leftjoin('sys_crm_deal_track_approval_receivables','sys_crm_deal_track_approval_receivables.deal_id','sys_crm_deals.id');
        //->where('sys_crm_deals.is_partial_invoice',0);
        if($from_month !="" && $to_month !=""){
            $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '".date('Y-m-d', strtotime($from_month))."' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($to_month))."'");
        }
        if($from_month !="" && $to_month ==""){
            $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') = '".date('Y-m-d', strtotime($from_month))."'");
        }
        $data1->where('sys_crm_deals.stage',4)
        //->where('sys_crm_deal_track_approval_invoice.status',1);
        ->where(function ($query) {
            $query->orwhere('sys_crm_deal_track_approval_invoice.status',1)
            ->orwhere('sys_crm_deal_track_approval_receivables.status',1);
        });
        //->wherein('sys_crm_deals.company_id',$company);
        if($company != 1){
            $data1->where('sys_crm_deals.company_id',$company);
        }
        if(count($ps_amc_deal_id)>0){                
            $data1->wherein('sys_crm_deals.id',$ps_amc_deal_id);
        }
    

    $retAmount=0.00;
        $dataA = $data1->get();
        if(count($dataA)>0){
            foreach($dataA as $dt){
                $retAmount+= SysHelper::get_aed_amount_new($dt->deal_currency,$dt->deal_value);
            }
        }

    //$sf = $data->where('source','Fulfillment')->sum('deal_value')*20/100;
    //$data = $data1->sum('deal_value')+$data2->sum('deal_value');
    return SysHelper::com_curr_format($retAmount, 2, '.', '');
}

    public static function get_total_on_process_all_by_user($user_id,$from_month,$to_month,$company_list,$ps_amc_deal_id){

            $user=$user_id;
        $data1=[];
        $data2=[];

        $data1 = DB::table('sys_crm_deals')->select('deal_value','deal_currency','source','cust_id','cs.internal')
        ->join('sys_cust_suppl as cs','cs.id','sys_crm_deals.cust_id')
        //->wherein('sys_crm_deals.company_id',$company)
        ->where('stage',4)
        ->whereNotIn('sys_crm_deals.id',function($query){
            $query->select('deal_id')->from('sys_crm_deal_track_approval_invoice')->where('status',1);
         });
        /*->whereNotIn('id',function($query) use($company){
            $query->select('deal_id')->from('sys_crm_deal_track_approval_invoice')->where('status',1);//->wherein('company_id',$company);
         });*/
        
            if($from_month !="" && $to_month !=""){
                $data1->whereRaw("DATE_FORMAT(estimated_close_date, '%Y-%m-%d') >= '".date('Y-m-d', strtotime($from_month))."' and DATE_FORMAT(estimated_close_date, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($to_month))."'");
            }
            if($from_month !="" && $to_month ==""){
                $data1->whereRaw("DATE_FORMAT(estimated_close_date, '%Y-%m-%d') = '".date('Y-m-d', strtotime($from_month))."'");
            }
            
            $data1->wherein('sys_crm_deals.company_id',$company_list);
            $data1->wherein('sys_crm_deals.owner',$user);
            if(count($ps_amc_deal_id)>0){                
                $data1->wherein('sys_crm_deals.id',$ps_amc_deal_id);
            }

        $retAmount=0.00;
        $inAmount=0;
        $exAmount=0;
            $dataA = $data1->get();
            if(count($dataA)>0){
                foreach($dataA as $dt){
                    $retAmount+= SysHelper::get_aed_amount($dt->deal_currency,$dt->deal_value);
                    if($dt->internal == 1){
                        $inAmount+= SysHelper::get_aed_amount($dt->deal_currency,$dt->deal_value);
                    } else { 
                        $exAmount+= SysHelper::get_aed_amount($dt->deal_currency,$dt->deal_value);
                    }
                }
            }

        //$sf = $data->where('source','Fulfillment')->sum('deal_value')*20/100;
        //$data = $data1->sum('deal_value')+$data2->sum('deal_value');
        return [SysHelper::com_curr_format($retAmount, 2, '.', ''),SysHelper::com_curr_format($inAmount, 2, '.', ''),SysHelper::com_curr_format($exAmount, 2, '.', '')];
    }

    public static function get_total_on_process_all_by_company($from_month,$to_month,$company,$ps_amc_deal_id){

    $data1=[];
    $data2=[];

    $data1 = DB::table('sys_crm_deals')->select('deal_value','deal_currency','source','cust_id','cs.internal')
    ->join('sys_cust_suppl as cs','cs.id','sys_crm_deals.cust_id')
    //->wherein('sys_crm_deals.company_id',$company)
    ->where('stage',4)
    ->whereNotIn('sys_crm_deals.id',function($query){
        $query->select('deal_id')->from('sys_crm_deal_track_approval_invoice')->where('status',1);//->wherein('company_id',$company);
     });
    
        if($from_month !="" && $to_month !=""){
            $data1->whereRaw("DATE_FORMAT(estimated_close_date, '%Y-%m-%d') >= '".date('Y-m-d', strtotime($from_month))."' and DATE_FORMAT(estimated_close_date, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($to_month))."'");
        }
        if($from_month !="" && $to_month ==""){
            $data1->whereRaw("DATE_FORMAT(estimated_close_date, '%Y-%m-%d') = '".date('Y-m-d', strtotime($from_month))."'");
        }
        if($company != 1){
            $data1->where('sys_crm_deals.company_id',$company);
        }
        if(count($ps_amc_deal_id)>0){                
            $data1->wherein('sys_crm_deals.id',$ps_amc_deal_id);
        }

    $retAmount=0.00;
    $inAmount=0;
    $exAmount=0;
        $dataA = $data1->get();
        if(count($dataA)>0){
            foreach($dataA as $dt){
                $retAmount+= SysHelper::get_aed_amount($dt->deal_currency,$dt->deal_value);
                if($dt->internal == 1){
                    $inAmount+= SysHelper::get_aed_amount($dt->deal_currency,$dt->deal_value);
                } else { 
                    $exAmount+= SysHelper::get_aed_amount($dt->deal_currency,$dt->deal_value);
                }
            }
        }

    //$sf = $data->where('source','Fulfillment')->sum('deal_value')*20/100;
    //$data = $data1->sum('deal_value')+$data2->sum('deal_value');
    return [SysHelper::com_curr_format($retAmount, 2, '.', ''),SysHelper::com_curr_format($inAmount, 2, '.', ''),SysHelper::com_curr_format($exAmount, 2, '.', '')];
}

    public static function get_total_forcast_all_by_user($user_id,$from_month,$to_month,$company_list,$ps_amc_deal_id){
        try {
            
            /*$data2 = DB::table('sys_crm_deals')->select('deal_value','deal_currency','source','cust_id')
            ->where('sys_crm_deals.company_id',$company_id)
            ->wherein('stage',[1,2,3]);
            $data2->wherein('sys_crm_deals.owner',$user_id);*/

            $data2 = DB::table('sys_crm_deals')->select('deal_value','deal_currency','source','cust_id','cs.internal')
            ->join('sys_cust_suppl as cs','cs.id','sys_crm_deals.cust_id')
        //->wherein('sys_crm_deals.company_id',$company_id)
        ->wherein('stage',[1,2,3])
        ->whereNotIn('sys_crm_deals.id',function($query){
            $query->select('deal_id')->from('sys_crm_deal_track_approval_invoice')->where('sys_crm_deals.status',1);
         })
         ->whereNotIn('sys_crm_deals.id',function($query){
            $query->select('deal_id')->from('sys_crm_deal_track_approval_receivables')->where('sys_crm_deals.status',1);
         });
        $data2->wherein('sys_crm_deals.owner',$user_id);
        
        if($from_month !="" && $to_month !=""){
            $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') >= '".date('Y-m-d', strtotime($from_month))."' and DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($to_month))."'");
        }
        elseif($from_month !="" && $to_month ==""){
            $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') = '".date('Y-m-d', strtotime($from_month))."'");
        }
        else{
            $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m') = '".date('Y-m')."'");
        }
        
        if(count($ps_amc_deal_id)>0){                
            $data2->wherein('sys_crm_deals.id',$ps_amc_deal_id);
        }
               
        $data2->wherein('sys_crm_deals.company_id',$company_list);
        $data = $data2->get();

        $retAmount=0;
        $inAmount=0;
        $exAmount=0;
        $abc[]=0;
        if(count($data)>0){
            foreach($data as $dt){
                $retAmount+= SysHelper::get_aed_amount($dt->deal_currency,$dt->deal_value);
                if($dt->internal == 1){
                    $inAmount+= SysHelper::get_aed_amount($dt->deal_currency,$dt->deal_value);
                } else { 
                    $exAmount+= SysHelper::get_aed_amount($dt->deal_currency,$dt->deal_value);
                }
            }
        }
        return [SysHelper::com_curr_format($retAmount, 2, '.', ''),SysHelper::com_curr_format($inAmount, 2, '.', ''),SysHelper::com_curr_format($exAmount, 2, '.', '')];
        
        } catch (\Throwable $th) {
            return $th;
        }
    }
    public static function get_total_forcast_all_by_company($from_month,$to_month,$company_id,$ps_amc_deal_id){
        try {
            
            /*$data2 = DB::table('sys_crm_deals')->select('deal_value','deal_currency','source','cust_id')
            ->where('sys_crm_deals.company_id',$company_id)
            ->wherein('stage',[1,2,3]);
            $data2->wherein('sys_crm_deals.owner',$user_id);*/

            $data2 = DB::table('sys_crm_deals')->select('deal_value','deal_currency','source','cust_id','cs.internal')
            ->join('sys_cust_suppl as cs','cs.id','sys_crm_deals.cust_id')
        //->wherein('sys_crm_deals.company_id',$company_id)
        ->wherein('stage',[3])
        ->whereNotIn('sys_crm_deals.id',function($query){
            $query->select('deal_id')->from('sys_crm_deal_track_approval_invoice')->where('sys_crm_deals.status',1);
         })
         ->whereNotIn('sys_crm_deals.id',function($query){
            $query->select('deal_id')->from('sys_crm_deal_track_approval_receivables')->where('sys_crm_deals.status',1);
         });
         if($company_id != 1){
            $data2->where('sys_crm_deals.company_id',$company_id);
         }
        
        if($from_month !="" && $to_month !=""){
            $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') >= '".date('Y-m-d', strtotime($from_month))."' and DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($to_month))."'");
        }
        elseif($from_month !="" && $to_month ==""){
            $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') = '".date('Y-m-d', strtotime($from_month))."'");
        }
        else{
            $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m') = '".date('Y-m')."'");
        }
        
        if(count($ps_amc_deal_id)>0){                
            $data2->wherein('sys_crm_deals.id',$ps_amc_deal_id);
        }
        $data = $data2->get();

        $retAmount=0;
        $inAmount=0;
        $exAmount=0;
        $abc[]=0;
        if(count($data)>0){
            foreach($data as $dt){
                $retAmount+= SysHelper::get_aed_amount_new($dt->deal_currency,$dt->deal_value);
                if($dt->internal == 1){
                    $inAmount+= SysHelper::get_aed_amount_new($dt->deal_currency,$dt->deal_value);
                } else { 
                    $exAmount+= SysHelper::get_aed_amount_new($dt->deal_currency,$dt->deal_value);
                }
            }
        }
        return [SysHelper::com_curr_format($retAmount, 2, '.', ''),SysHelper::com_curr_format($inAmount, 2, '.', ''),SysHelper::com_curr_format($exAmount, 2, '.', '')];
        
        } catch (\Throwable $th) {
            return $th;
        }
    }

//added by kunal
    public static function normalizeToYmd($date)
    {
        if (empty($date)) {
            return null;
        }


        if (Carbon::hasFormat($date, 'Y-m-d')) {
            return Carbon::createFromFormat('Y-m-d', $date)->format('Y-m-d');
        }


        if (Carbon::hasFormat($date, 'd/m/Y')) {
            return Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
        }


        if (Carbon::hasFormat($date, 'd-m-Y')) { // extra support
            return Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');
        }


        if (Carbon::hasFormat($date, 'j/n/Y')) {
            return Carbon::createFromFormat('j/n/Y', $date)->format('Y-m-d');
        }

            // ✅ NEW: support single-digit day/month like 5/2/2024
        if (Carbon::hasFormat($date, 'j/n/Y')) {
            return Carbon::createFromFormat('j/n/Y', $date)->format('Y-m-d');
        }

        // ✅ NEW: support 2-digit year like 25/2/25 or 5/2/25
        if (Carbon::hasFormat($date, 'd/m/y')) {
            return Carbon::createFromFormat('d/m/y', $date)->format('Y-m-d');
        }



        return Carbon::parse($date)->format('Y-m-d'); // fallback
    }

      public static function normalizeToDmy($date)
    {
        if (empty($date)) {
            return null;
        }

        if (Carbon::hasFormat($date, 'Y-m-d')) {
            return Carbon::createFromFormat('Y-m-d', $date)->format('d/m/Y');
        }

        if (Carbon::hasFormat($date, 'd/m/Y')) {
            return Carbon::createFromFormat('d/m/Y', $date)->format('d/m/Y');
        }

        if (Carbon::hasFormat($date, 'd-m-Y')) { // extra support
            return Carbon::createFromFormat('d-m-Y', $date)->format('d/m/Y');
        }

     
         if (Carbon::hasFormat($date, 'j/n/Y')) {
            return Carbon::createFromFormat('j/n/Y', $date)->format('d/m/Y');
        }

        return Carbon::parse($date)->format('d/m/Y'); // fallback
    }
    
    //added by kunal
    public static function get_lead_count_by_company($date_from, $date_to, $company_id)
    {

        try {
            $statusResults = DB::table('sys_crm_leads')
                ->select('status', DB::raw('COUNT(*) as count'))
                ->whereBetween('date', [$date_from, $date_to])
                ->where('company_id', $company_id)
                ->groupBy('status')
                ->pluck('count', 'status') // returns associative array: [status => count]
                ->toArray();

            // Get counts grouped by sub_status
            $subStatusResults = DB::table('sys_crm_leads')
                ->select('sub_status', DB::raw('COUNT(*) as count'))
                ->whereBetween('date', [$date_from, $date_to])
                ->where('company_id', $company_id)
                ->where('status', '!=', 0)
                ->groupBy('sub_status')
                ->pluck('count', 'sub_status')
                ->toArray();

            $dealStatusResults = DB::table('sys_crm_leads as leads')
                ->join('sys_crm_deals as deals', 'leads.deal_id', '=', 'deals.id')
                ->select('deals.stage', DB::raw('COUNT(*) as count'))
                ->whereBetween('leads.date', [$date_from, $date_to])
                ->where('leads.company_id', $company_id)
                ->where('leads.status',0)
                ->groupBy('deals.stage')
                ->pluck('count', 'deals.stage')
                ->toArray();

            // Map results to specific status labels
            return [
                    'new' => $statusResults[1] ?? 0,
                    'qualified' => $statusResults[2] ?? 0,
                    'unqualified' => $statusResults[3] ?? 0,
                    'pending_response' => $statusResults[4] ?? 0,
                    'converted' => $statusResults[0] ?? 0,
                    'closed' => $statusResults[10] ?? 0,
                    'total' => array_sum($statusResults),
                    //sub status
                    'just_received_uncontacted' => $subStatusResults[1] ?? 0,
                    'sent_to_sales' => $subStatusResults[2] ?? 0,
                    'budget_issue' => $subStatusResults[3] ?? 0,
                    'not_interested' => $subStatusResults[4] ?? 0,
                    'wrong_contact' => $subStatusResults[5] ?? 0,
                    'timeline_not_matching' => $subStatusResults[6] ?? 0,
                    'product_service_mismatch' => $subStatusResults[7] ?? 0,
                    'unqualified_other' => $subStatusResults[8] ?? 0,
                    'waiting_for_eud' => $subStatusResults[9] ?? 0,
                    'waiting_for_vendor_price' => $subStatusResults[10] ?? 0,
                    'quoted_waiting_response' => $subStatusResults[11] ?? 0,
                    'pending_response_other' => $subStatusResults[12] ?? 0,
                    'no_response' => $subStatusResults[13] ?? 0,
                    'closed_other' => $subStatusResults[14] ?? 0,
            ];
        } catch (\Exception $e) {


            return [
                    'new' => 0,
                    'qualified' => 0,
                    'unqualified' => 0,
                    'pending_response' => 0,
                    'converted' => 0,
                    'closed' => 0,
                    'total' => 0,
                    'just_received_uncontacted' => 0,
                    'sent_to_sales' => 0,
                    'budget_issue' => 0,
                    'not_interested' => 0,
                    'wrong_contact' => 0,
                    'timeline_not_matching' => 0,
                    'product_service_mismatch' => 0,
                    'unqualified_other' => 0,
                    'waiting_for_eud' => 0,
                    'waiting_for_vendor_price' => 0,
                    'quoted_waiting_response' => 0,
                    'pending_response_other' => 0,
                    'no_response' => 0,
                    'closed_other' => 0,
                    'deal_prospecting' =>  0,
                    'deal_quote' =>  0,
                    'deal_closure' =>  0,
                    'deal_won' =>  0,
                    'deal_lost' =>  0,
            ];
        }
    }
    //added by kunal
    public static function get_lead_count_by_sales_person($date_from, $date_to, $person_id, $company_id)
    {

        try {
            $statusResults = DB::table('sys_crm_leads')
                ->select('status', DB::raw('COUNT(*) as count'))
                ->whereBetween('date', [$date_from, $date_to])
                ->where('company_id', $company_id)
                ->where('owner', $person_id)
                ->groupBy('status')
                ->pluck('count', 'status') // returns associative array: [status => count]
                ->toArray();

            // Get counts grouped by sub_status
            $subStatusResults = DB::table('sys_crm_leads')
                ->select('sub_status', DB::raw('COUNT(*) as count'))
                ->where('company_id', $company_id)
                ->whereBetween('date', [$date_from, $date_to])
                ->where('owner', $person_id)
                ->where('status', '!=', 0)
                ->groupBy('sub_status')
                ->pluck('count', 'sub_status')
                ->toArray();

            $followups = DB::table('sys_crm_leads')
                ->where('company_id', $company_id)
                ->where('owner', $person_id)
                ->select(
                    DB::raw('SUM(lead_update_count) as total_followups'),
                    DB::raw('AVG(lead_update_count) as avg_followups')
                )
                ->first();


            $dealStatusResults = DB::table('sys_crm_leads as leads')
                ->join('sys_crm_deals as deals', 'leads.deal_id', '=', 'deals.id')
                ->select('deals.stage', DB::raw('COUNT(*) as count'))
                ->whereBetween('leads.date', [$date_from, $date_to])
                ->where('leads.owner', $person_id)
                ->where('leads.company_id', $company_id)
                ->where('leads.status',0)
                ->groupBy('deals.stage')
                ->pluck('count', 'deals.stage')
                ->toArray();

                

            // Map results to specific status labels
            return [
                'new' => $statusResults[1] ?? 0,
                'qualified' => $statusResults[2] ?? 0,
                'unqualified' => $statusResults[3] ?? 0,
                'pending_response' => $statusResults[4] ?? 0,
                'converted' => $statusResults[0] ?? 0,
                'closed' => $statusResults[10] ?? 0,
                'total' => array_sum($statusResults),
                //sub status
                'just_received_uncontacted' => $subStatusResults[1] ?? 0,
                'sent_to_sales' => $subStatusResults[2] ?? 0,
                'budget_issue' => $subStatusResults[3] ?? 0,
                'not_interested' => $subStatusResults[4] ?? 0,
                'wrong_contact' => $subStatusResults[5] ?? 0,
                'timeline_not_matching' => $subStatusResults[6] ?? 0,
                'product_service_mismatch' => $subStatusResults[7] ?? 0,
                'unqualified_other' => $subStatusResults[8] ?? 0,
                'waiting_for_eud' => $subStatusResults[9] ?? 0,
                'waiting_for_vendor_price' => $subStatusResults[10] ?? 0,
                'quoted_waiting_response' => $subStatusResults[11] ?? 0,
                'pending_response_other' => $subStatusResults[12] ?? 0,
                'no_response' => $subStatusResults[13] ?? 0,
                'closed_other' => $subStatusResults[14] ?? 0,
                'total_followups' => $followups->total_followups ?? 0,
                'avg_followups' => $followups->avg_followups ?? 0,
                'deal_prospecting' => $dealStatusResults[1] ?? 0,
                'deal_quote' => $dealStatusResults[2] ?? 0,
                'deal_closure' => $dealStatusResults[3] ?? 0,
                'deal_won' => $dealStatusResults[4] ?? 0,
                'deal_lost' => $dealStatusResults[5] ?? 0,
            ];
        } catch (\Exception $e) {


            return [
                'new' => 0,
                'qualified' => 0,
                'unqualified' => 0,
                'pending_response' => 0,
                'converted' => 0,
                'closed' => 0,
                'total' => 0,
                'just_received_uncontacted' => 0,
                'sent_to_sales' => 0,
                'budget_issue' => 0,
                'not_interested' => 0,
                'wrong_contact' => 0,
                'timeline_not_matching' => 0,
                'product_service_mismatch' => 0,
                'unqualified_other' => 0,
                'waiting_for_eud' => 0,
                'waiting_for_vendor_price' => 0,
                'quoted_waiting_response' => 0,
                'pending_response_other' => 0,
                'no_response' => 0,
                'closed_other' => 0,
                'total_followups' => 0,
                'avg_followups' => 0,
            ];
        }
    }


    public static function get_total_leads_by_user($user_id,$month){
        $data = SysCrmLeads::where('created_by',$user_id)->whereRaw("DATE_FORMAT(created_at, '%Y-%m') >= '".date('Y-m', strtotime($month))."'")->count();
        return $data;
    }
    public static function get_total_leads_convert_by_user($user_id,$month){
        $data = SysCrmLeads::where('created_by',$user_id)->whereRaw("DATE_FORMAT(created_at, '%Y-%m') >= '".date('Y-m', strtotime($month))."'")->where('status',0)->count();
        return $data;
    }
    public static function get_total_leads_convert_won_by_user($user_id,$month){
        $data = SysCrmLeads::select('id')->where('created_by',$user_id)->whereRaw("DATE_FORMAT(created_at, '%Y-%m') >= '".date('Y-m', strtotime($month))."'")->where('status',0)->get();
        
        if (count($data)>0) {
            foreach ($data as $value) {
                $dt[]=$value->id;
            }
            $dt2=SysCrmDeals::wherein('lead_id',$dt)->wherein('stage',[4])->count();
            return $dt2;
        }
        else{return 0;}
    }
    public static function get_total_leads_convert_quote_by_user($user_id,$month){
        $data = SysCrmLeads::select('id')->where('created_by',$user_id)->whereRaw("DATE_FORMAT(created_at, '%Y-%m') >= '".date('Y-m', strtotime($month))."'")->where('status',0)->get();
        
        if (count($data)>0) {
            foreach ($data as $value) {
                $dt[]=$value->id;
            }
            $dt2=SysCrmDeals::wherein('lead_id',$dt)->wherein('stage',[2,3,4])->count();
            return $dt2;
        }
        else{return 0;}
    }
    public static function get_total_leads_convert_lost_by_user($user_id,$month){
        $data = SysCrmLeads::select('id')->where('created_by',$user_id)->whereRaw("DATE_FORMAT(created_at, '%Y-%m') >= '".date('Y-m', strtotime($month))."'")->where('status',0)->get();
        
        if (count($data)>0) {
            foreach ($data as $value) {
                $dt[]=$value->id;
            }
            $dt2=SysCrmDeals::wherein('lead_id',$dt)->wherein('stage',[5])->count();
            return $dt2;
        }
        else{return 0;}
    }

    public static function get_total_ecommerce_sale()
    {
        $start_date = date('Y-m-d', strtotime('first day of this month - 3 months'));
        $end_date = date('Y-m-d', strtotime('last day of this month'));

        $data1 = DB::table('sys_crm_deals')->select('deal_value','source')->leftjoin('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_id','sys_crm_deals.id')
        ->where('sys_crm_deals.stage',4)->where('sys_crm_deal_track_approval_invoice.status',1)->where('sys_crm_deals.source','Ecommerce')->where('sys_crm_deals.isproject',4);
        $dataD = $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') = '".date('Y-m-d')."'")->sum('deal_value');
        
        $data2 = DB::table('sys_crm_deals')->select('deal_value','source')->leftjoin('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_id','sys_crm_deals.id')
        ->where('sys_crm_deals.stage',4)->where('sys_crm_deal_track_approval_invoice.status',1)->where('sys_crm_deals.source','Ecommerce')->where('sys_crm_deals.isproject',4);
        $dataM = $data2->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m') = '".date('Y-m')."'")->sum('deal_value');
        
        $data3 = DB::table('sys_crm_deals')->select('deal_value','source')->leftjoin('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_id','sys_crm_deals.id')
        ->where('sys_crm_deals.stage',4)->where('sys_crm_deal_track_approval_invoice.status',1)->where('sys_crm_deals.source','Ecommerce')->where('sys_crm_deals.isproject',4);
        $dataY = $data3->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y') = '".date('Y')."'")->sum('deal_value');
        
        $data4 = DB::table('sys_crm_deals')->select('deal_value','source')->leftjoin('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_id','sys_crm_deals.id')
        ->where('sys_crm_deals.stage',4)->where('sys_crm_deal_track_approval_invoice.status',1)->where('sys_crm_deals.source','Ecommerce')->where('sys_crm_deals.isproject',4);
        $dataQ = $data4->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '".$end_date."'")->sum('deal_value');

        $ret[0] = SysHelper::com_curr_format($dataD, 2, '.', ',');
        $ret[1] = SysHelper::com_curr_format($dataM, 2, '.', ',');
        $ret[2] = SysHelper::com_curr_format($dataY, 2, '.', ',');
        $ret[3] = SysHelper::com_curr_format($dataQ, 2, '.', ',');

        return $ret;

    }
    public static function get_total_service_revenue($date, $company){

        
        $data1 = DB::table('sys_crm_deals')->select('sys_crm_deals.id as dealid','qty','price','discount','deal_currency','source','cust_id','sys_crm_quote_items.deal_id','deal_percent')
        ->leftjoin('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_id','sys_crm_deals.id')
        ->leftjoin('sys_crm_quote_items','sys_crm_quote_items.deal_id','sys_crm_deals.id')
        ->where('sys_crm_deals.stage',4)->where('sys_crm_deals.is_partial_invoice',0)
        ->where('sys_crm_deal_track_approval_invoice.status',1)->where('sys_crm_deals.company_id',$company);
        

        $data2 = DB::table('sys_crm_deals')->select('qty','price','discount','deal_currency','source','cust_id')
        ->leftjoin('sys_crm_quote_items','sys_crm_quote_items.deal_id','sys_crm_deals.id')
        ->where('sys_crm_deals.company_id',$company)->wherein('stage',[1,2,3])
        ->whereNotIn('sys_crm_deals.id',function($query){
            $query->select('deal_id')->from('sys_crm_deal_track_approval_invoice')->where('status',1);
         });

        if($date=="d"){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') = '".date('Y-m-d')."'");
            $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') = '".date('Y-m-d')."'");
        }
        if($date=="m"){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m') = '".date('Y-m')."'");
            $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m') = '".date('Y-m')."'");
        }
        if($date=="y"){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y') = '".date('Y')."'");
            $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y') = '".date('Y')."'");
        }
        if($date=="q"){
            $quarter = SysHelper::get_quarter(date('m'));
            $start_date = $quarter[0];
            $end_date = $quarter[1];
            
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '".$end_date."'");
            $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') <= '".$end_date."'");
        }
        if($date=="pm"){
            $c_date = Carbon::createFromFormat('Y-m', date('Y-m'))->subMonth();  
            $pm_date = $c_date->format('Y-m');

                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m') = '".$pm_date."'");
            $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m') = '".$pm_date."'");
        }
        if($date=="pq"){
            $quarter = SysHelper::get_pre_quarter(date('m'));
            $start_date = $quarter[0];
            $end_date = $quarter[1];
            
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '".$end_date."'");
            $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') <= '".$end_date."'");
        }
        
        if(Auth::user()->role_id == 1){ //admin
            // $data1->where('sys_crm_deals.company_id',1);
            // $data2->where('sys_crm_deals.company_id',1);
        }
        else{            
            $data1->where('sys_crm_deals.owner',Auth::user()->id);
            $data2->where('sys_crm_deals.owner',Auth::user()->id);                
        }
        
        $dataA = $data1->get();
        $dataB = $data2->get();
        $retAmount=0;
        if(count($dataA)>0){
            foreach($dataA as $dt){
                
                $deal_value= ($dt->qty*$dt->price) - ($dt->qty*$dt->discount);
                $retAmount+= SysHelper::get_deal_value($deal_value,$dt->source,$dt->deal_currency,$dt->deal_percent,$dt->cust_id);
                /*if(in_array($dt->dealid, [8690,8660])){$retAmount+= SysHelper::get_aed_amount($dt->deal_currency,$dt->deal_value);}
                else{
                if($dt->source=="Fulfillment"){$retAmount+= SysHelper::get_aed_amount($dt->deal_currency,($deal_value*20/100));}
                else if(in_array($dt->cust_id, [2568,4258,4382,5322,7347,8144,8145,8146,3711,4089,8142])){$retAmount+= SysHelper::get_aed_amount($dt->deal_currency,($deal_value*20/100));}
                else if(in_array($dt->cust_id, [8866])){$retAmount+= SysHelper::get_aed_amount($dt->deal_currency,($deal_value*30/100));}
                else{$retAmount+= SysHelper::get_aed_amount($dt->deal_currency,$deal_value);}                
                }*/
            }
        }
        $revenue = SysHelper::com_curr_format($retAmount, 2, '.', ',');

        $retAmount2=0;
        if(count($dataB)>0){
            foreach($dataB as $dt){
                $deal_value2= ($dt->qty*$dt->price) - ($dt->qty*$dt->discount);
                $retAmount2+= SysHelper::get_aed_amount($dt->deal_currency,$deal_value2);
            }
        }
        $forecast = SysHelper::com_curr_format($retAmount2, 2, '.', ',');

        return [$revenue,$forecast];
    }
    public static function get_total_service_revenue_all($date, $company){
        $revenue1 = DB::table('sys_crm_deals')->select('sys_crm_deals.id as dealid','qty','price','discount','deal_currency','source','cust_id','sys_crm_quote_items.deal_id','deal_percent')
        ->leftjoin('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_id','sys_crm_deals.id')
        ->leftjoin('sys_crm_deal_track_approval_receivables','sys_crm_deal_track_approval_receivables.deal_id','sys_crm_deals.id')
        ->leftjoin('sys_crm_quote_items','sys_crm_quote_items.deal_id','sys_crm_deals.id')
        //->wherein('sys_crm_quote_items.product_id',[1417,1427,4714,8408,8490,8493,8729,8806,9319,10247,8544,9726,9728,9780,10294])
        ->where('sys_crm_deals.stage',4)
        //->where('sys_crm_deals.is_partial_invoice',0)
        
        //->where('sys_crm_deal_track_approval_invoice.status',1);
        ->where(function ($query) {
            $query->orwhere('sys_crm_deal_track_approval_invoice.status',1)
            ->orwhere('sys_crm_deal_track_approval_receivables.status',1);
        });

        $revenue2 = DB::table('sys_crm_deals')->select('sys_crm_deals.id as dealid','qty','price','discount','deal_currency','source','cust_id','deal_percent')
        ->join('sys_crm_quote_items','sys_crm_quote_items.deal_id','sys_crm_deals.id')
        //->wherein('sys_crm_quote_items.product_id',[1417,1427,4714,8408,8490,8493,8729,8806,9319,10247,8544,9726,9728,9780,10294])
        ->where('sys_crm_deals.stage',4);

        $forcast = DB::table('sys_crm_deals')->select('qty','price','discount','deal_currency','source','cust_id')
        ->leftjoin('sys_crm_quote_items','sys_crm_quote_items.deal_id','sys_crm_deals.id')
        //->wherein('sys_crm_quote_items.product_id',[1417,1427,4714,8408,8490,8493,8729,8806,9319,10247,8544,9726,9728,9780,10294])
        ->wherein('stage',[1,2,3])
        ->whereNotIn('sys_crm_deals.id',function($query){
            $query->select('deal_id')->from('sys_crm_deal_track_approval_invoice')->where('status',1);
         });

        if($date=="d"){
                $revenue1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') = '".date('Y-m-d')."'");
                $revenue2->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') = '".date('Y-m-d')."'");
            $forcast->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') = '".date('Y-m-d')."'");
        }
        if($date=="m"){
                $revenue1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m') = '".date('Y-m')."'");
                $revenue2->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m') = '".date('Y-m')."'");
            $forcast->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m') = '".date('Y-m')."'");
        }
        if($date=="y"){
                $revenue1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y') = '".date('Y')."'");
                $revenue2->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y') = '".date('Y')."'");
            $forcast->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y') = '".date('Y')."'");
        }
        if($date=="q"){
            $quarter = SysHelper::get_quarter(date('m'));
            $start_date = $quarter[0];
            $end_date = $quarter[1];
            
                $revenue1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '".$end_date."'");
                $revenue2->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') <= '".$end_date."'");
            $forcast->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') <= '".$end_date."'");
        }
        if($date=="pm"){
            $c_date = Carbon::createFromFormat('Y-m', date('Y-m'))->subMonth();  
            $pm_date = $c_date->format('Y-m');

                $revenue1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m') = '".$pm_date."'");
                $revenue2->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m') = '".$pm_date."'");
            $forcast->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m') = '".$pm_date."'");
        }
        if($date=="pq"){
            $quarter = SysHelper::get_pre_quarter(date('m'));
            $start_date = $quarter[0];
            $end_date = $quarter[1];
            
                $revenue1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '".$end_date."'");
                $revenue2->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') <= '".$end_date."'");
            $forcast->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') <= '".$end_date."'");
        }
        
        if(Auth::user()->role_id == 1 || Auth::user()->id==33){ //admin
            // $data1->where('sys_crm_deals.company_id',1);
            // $data2->where('sys_crm_deals.company_id',1);
        }
        else{
            // if(Auth::user()->id==27){//monica
            //     //$teams= array(27,30,54,62);
            //     $teams= array(27);
            //     $revenue1->wherein('sys_crm_deals.owner',$teams);
            //     $revenue2->wherein('sys_crm_deals.owner',$teams);
            //     $forcast->wherein('sys_crm_deals.owner',$teams);
            // }               
            // else if(Auth::user()->id==44){ //rajiv
            //     $teams= array(44,45,34,32);
            //     $revenue1->wherein('sys_crm_deals.owner',$teams);
            //     $revenue2->wherein('sys_crm_deals.owner',$teams);
            //     $forcast->wherein('sys_crm_deals.owner',$teams);
            // }
            
            // else{
                $revenue1->where('sys_crm_deals.owner',Auth::user()->id);
                $revenue2->where('sys_crm_deals.owner',Auth::user()->id);
                $forcast->where('sys_crm_deals.owner',Auth::user()->id);
            //}
        }
        
        $dataA1 = $revenue1->get();
        $dataA2 = $revenue2->get();
        $dataB = $forcast->get();
        $retAmount=0;
        if(count($dataA1)>0){
            foreach($dataA1 as $dt){
                
                $deal_value= ($dt->qty*$dt->price) - ($dt->qty*$dt->discount);
                $retAmount+= SysHelper::get_deal_value($deal_value,$dt->source,$dt->deal_currency,$dt->deal_percent,$dt->cust_id);
            }
        }
        if(count($dataA2)>0){
            foreach($dataA2 as $dt){
                
                $deal_value= ($dt->qty*$dt->price) - ($dt->qty*$dt->discount);
                $retAmount+= SysHelper::get_deal_value($deal_value,$dt->source,$dt->deal_currency,$dt->deal_percent,$dt->cust_id);
            }
        }
        $revenue = SysHelper::com_curr_format($retAmount, 2, '.', ',');

        $retAmount2=0;
        if(count($dataB)>0){
            foreach($dataB as $dt){
                $deal_value2= ($dt->qty*$dt->price) - ($dt->qty*$dt->discount);
                $retAmount2+= SysHelper::get_aed_amount($dt->deal_currency,$deal_value2);
            }
        }
        $forecast = SysHelper::com_curr_format($retAmount2, 2, '.', ',');

        return [$revenue,$forecast];
    }

    public static function get_total_amc_revenue($date, $company){

        $data1 = DB::table('sys_crm_deals')->select('sys_crm_deals.id as dealid','qty','price','discount','deal_currency','source','cust_id','deal_percent')
        ->leftjoin('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_id','sys_crm_deals.id')
        ->join('sys_crm_quote_items','sys_crm_quote_items.deal_id','sys_crm_deals.id')
        ->wherein('sys_crm_quote_items.product_id',[9976,10465,10497])
        ->where('sys_crm_deals.stage',4)->where('sys_crm_deals.is_partial_invoice',0)
        ->where('sys_crm_deal_track_approval_invoice.status',1)->where('sys_crm_deals.company_id',$company);
        

        $data2 = DB::table('sys_crm_deals')->select('qty','price','discount','deal_currency','source','cust_id')
        ->join('sys_crm_quote_items','sys_crm_quote_items.deal_id','sys_crm_deals.id')
        ->wherein('sys_crm_quote_items.product_id',[9976,10465,10497])
        ->where('sys_crm_deals.company_id',$company)
        ->wherein('stage',[1,2,3])
        ->whereNotIn('sys_crm_deals.id',function($query){
			$query->select('deal_id')->from('sys_crm_deal_track_approval_invoice')->where('status',1);
		 });
        
        if($date=="d"){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') = '".date('Y-m-d')."'");
            $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') = '".date('Y-m-d')."'");
        }
        if($date=="m"){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m') = '".date('Y-m')."'");
            $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m') = '".date('Y-m')."'");
        }
        if($date=="y"){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y') = '".date('Y')."'");
            $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y') = '".date('Y')."'");
        }
        if($date=="q"){
            $quarter = SysHelper::get_quarter(date('m'));
            $start_date = $quarter[0];
            $end_date = $quarter[1];
            
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '".$end_date."'");
            $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') <= '".$end_date."'");
        }
        if($date=="pm"){
            $c_date = Carbon::createFromFormat('Y-m', date('Y-m'))->subMonth();  
            $pm_date = $c_date->format('Y-m');

                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m') = '".$pm_date."'");
            $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m') = '".$pm_date."'");
        }
        if($date=="pq"){
            $quarter = SysHelper::get_pre_quarter(date('m'));
            $start_date = $quarter[0];
            $end_date = $quarter[1];
            
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '".$end_date."'");
            $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') <= '".$end_date."'");
        }

        if(Auth::user()->role_id == 1){ //admin
            // $data1->where('sys_crm_deals.company_id',1);
            // $data2->where('sys_crm_deals.company_id',1);
        }
        else{
                $data1->where('sys_crm_deals.owner',Auth::user()->id);
                $data2->where('sys_crm_deals.owner',Auth::user()->id);
        }
        
        $dataA = $data1->get();
        $dataB = $data2->get();
        $retAmount=0;
        if(count($dataA)>0){
            foreach($dataA as $dt){
                
                $deal_value= ($dt->qty*$dt->price) - ($dt->qty*$dt->discount);
                $retAmount+= SysHelper::get_deal_value($deal_value,$dt->source,$dt->deal_currency,$dt->deal_percent,$dt->cust_id);
                /*if(in_array($dt->dealid, [8690,8660])){$retAmount+= SysHelper::get_aed_amount($dt->deal_currency,$dt->deal_value);}
                else{
                if($dt->source=="Fulfillment"){$retAmount+= SysHelper::get_aed_amount($dt->deal_currency,($deal_value*20/100));}
                else if(in_array($dt->cust_id, [2568,4258,4382,5322,7347,8144,8145,8146,3711,4089,8142])){$retAmount+= SysHelper::get_aed_amount($dt->deal_currency,($deal_value*20/100));}
                else if(in_array($dt->cust_id, [8866])){$retAmount+= SysHelper::get_aed_amount($dt->deal_currency,($deal_value*30/100));}
                else{$retAmount+= SysHelper::get_aed_amount($dt->deal_currency,$deal_value);}                
                }*/
            }
        }
        $revenue = SysHelper::com_curr_format($retAmount, 2, '.', ',');

        $retAmount2=0;
        if(count($dataB)>0){
            foreach($dataB as $dt){
                $deal_value2= ($dt->qty*$dt->price) - ($dt->qty*$dt->discount);
                $retAmount2+= SysHelper::get_aed_amount($dt->deal_currency,$deal_value2);
            }
        }
        $forecast = SysHelper::com_curr_format($retAmount2, 2, '.', ',');

        return [$revenue,$forecast];
    }
    public static function get_total_amc_revenue_all($date, $company){

        $revenue1 = DB::table('sys_crm_deals')->select('sys_crm_deals.id as dealid','deal_value','qty','price','discount','deal_currency','source','cust_id','deal_percent')
        ->leftjoin('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_id','sys_crm_deals.id')
        ->leftjoin('sys_crm_deal_track_approval_receivables','sys_crm_deal_track_approval_receivables.deal_id','sys_crm_deals.id')
        ->join('sys_crm_quote_items','sys_crm_quote_items.deal_id','sys_crm_deals.id')
        ->wherein('sys_crm_quote_items.product_id',[35657,35716])
        ->where('sys_crm_deals.stage',4)
        //->where('sys_crm_deals.is_partial_invoice',0)
        //->where('sys_crm_deal_track_approval_invoice.status',1);
        ->where(function ($query) {
            $query->orwhere('sys_crm_deal_track_approval_invoice.status',1)
            ->orwhere('sys_crm_deal_track_approval_receivables.status',1);
        });
        
        $revenue2 = DB::table('sys_crm_deals')->select('sys_crm_deals.id as dealid','deal_value','qty','price','discount','deal_currency','source','cust_id','deal_percent')
        ->join('sys_crm_quote_items','sys_crm_quote_items.deal_id','sys_crm_deals.id')
        ->wherein('sys_crm_quote_items.product_id',[35657,35716])
        ->where('sys_crm_deals.stage',4);
        

        $forcast = DB::table('sys_crm_deals')->select('qty','price','discount','deal_currency','source','cust_id','deal_value')
        ->join('sys_crm_quote_items','sys_crm_quote_items.deal_id','sys_crm_deals.id')
        ->wherein('sys_crm_quote_items.product_id',[35657,35716])
        ->wherein('stage',[1,2,3])
        ->whereNotIn('sys_crm_deals.id',function($query){
			$query->select('deal_id')->from('sys_crm_deal_track_approval_invoice')->where('status',1);
		 })
         ->whereNotIn('sys_crm_deals.id',function($query){
			$query->select('deal_id')->from('sys_crm_deal_track_approval_receivables')->where('status',1);
		 });
        
        if($date=="d"){
                $revenue1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') = '".date('Y-m-d')."'");
                $revenue2->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') = '".date('Y-m-d')."'");
            $forcast->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') = '".date('Y-m-d')."'");
        }
        if($date=="m"){
                $revenue1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m') = '".date('Y-m')."'");
                $revenue2->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m') = '".date('Y-m')."'");
            $forcast->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m') = '".date('Y-m')."'");
        }
        if($date=="y"){
                $revenue1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y') = '".date('Y')."'");
                $revenue2->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y') = '".date('Y')."'");
            $forcast->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y') = '".date('Y')."'");
        }
        if($date=="q"){
            $quarter = SysHelper::get_quarter(date('m'));
            $start_date = $quarter[0];
            $end_date = $quarter[1];
            
                $revenue1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '".$end_date."'");
                $revenue2->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') <= '".$end_date."'");
            $forcast->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') <= '".$end_date."'");
        }
        if($date=="pm"){
            $c_date = Carbon::createFromFormat('Y-m', date('Y-m'))->subMonth();  
            $pm_date = $c_date->format('Y-m');

                $revenue1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m') = '".$pm_date."'");
                $revenue2->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m') = '".$pm_date."'");
            $forcast->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m') = '".$pm_date."'");
        }
        if($date=="pq"){
            $quarter = SysHelper::get_pre_quarter(date('m'));
            $start_date = $quarter[0];
            $end_date = $quarter[1];
            
                $revenue1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '".$end_date."'");
                $revenue2->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') <= '".$end_date."'");
            $forcast->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') <= '".$end_date."'");
        }

        if(Auth::user()->role_id == 1 || Auth::user()->id==33){ //admin
            // $data1->where('sys_crm_deals.company_id',1);
            // $data2->where('sys_crm_deals.company_id',1);
        }
        else{
            if(Auth::user()->id==27){//monica
                //$teams= array(27,30,54,62);
                $teams= array(27);
                $revenue1->wherein('sys_crm_deals.owner',$teams);
                $revenue2->wherein('sys_crm_deals.owner',$teams);
                $forcast->wherein('sys_crm_deals.owner',$teams);
            }               
            else if(Auth::user()->id==44){ //rajiv
                $teams= array(44,45,34,32);
                $revenue1->wherein('sys_crm_deals.owner',$teams);
                $revenue2->wherein('sys_crm_deals.owner',$teams);
                $forcast->wherein('sys_crm_deals.owner',$teams);
            }
            else{
                $revenue1->where('sys_crm_deals.owner',Auth::user()->id);
                $revenue2->where('sys_crm_deals.owner',Auth::user()->id);
                $forcast->where('sys_crm_deals.owner',Auth::user()->id);
            }
        }
        
        $dataA1 = $revenue1->get();
        $dataA2 = $revenue2->get();
        $dataB = $forcast->get();
        $retAmount=0;
        if(count($dataA1)>0){
            foreach($dataA1 as $dt){
                
                $retAmount+= SysHelper::get_deal_value($dt->deal_value,$dt->source,$dt->deal_currency,$dt->deal_percent,$dt->cust_id);
            }
        }
        if(count($dataA2)>0){
            foreach($dataA2 as $dt){
                
                $retAmount+= SysHelper::get_deal_value($dt->deal_value,$dt->source,$dt->deal_currency,$dt->deal_percent,$dt->cust_id);
            }
        }
        $revenue = SysHelper::com_curr_format($retAmount, 2, '.', ',');

        $retAmount2=0;
        if(count($dataB)>0){
            foreach($dataB as $dt){
                $deal_value2= ($dt->qty*$dt->price) - ($dt->qty*$dt->discount);
                $retAmount2+= SysHelper::get_aed_amount($dt->deal_currency,$deal_value2);
            }
        }
        $forecast = SysHelper::com_curr_format($retAmount2, 2, '.', ',');

        return [$revenue,$forecast];
    }

    public static function get_total_project_revenue($date, $company){

        if($company==1 || $company==3 || $company==13){
        $data1 = DB::table('sys_crm_deals')->select('sys_crm_deals.id as dealid','deal_value','deal_currency','source','cust_id','deal_percent')
        ->leftjoin('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_id','sys_crm_deals.id')        
        ->where('sys_crm_deals.isproject',1)
        ->where('sys_crm_deals.stage',4)->where('sys_crm_deals.is_partial_invoice',0)
        ->where('sys_crm_deal_track_approval_invoice.status',1)->where('sys_crm_deals.company_id',$company);
        }
        else{
        $data1 = DB::table('sys_crm_deals')->select('sys_crm_deals.id as dealid','deal_value','deal_currency','source','cust_id','deal_percent')
        ->where('sys_crm_deals.isproject',1)        
        ->where('sys_crm_deals.stage',4)->where('sys_crm_deals.company_id',$company);
        }

        $data2 = DB::table('sys_crm_deals')->select('deal_value','deal_currency','source','cust_id')        
        ->where('sys_crm_deals.isproject',1)        
        ->where('sys_crm_deals.company_id',$company)
        ->wherein('stage',[1,2,3])
        ->whereNotIn('sys_crm_deals.id',function($query){
			$query->select('deal_id')->from('sys_crm_deal_track_approval_invoice')->where('status',1);
		 });
        
        if($date=="d"){
            if($company==1 || $company==3 || $company==13){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') = '".date('Y-m-d')."'");
            }else{
                $data1->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') = '".date('Y-m-d')."'");
            }
            $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') = '".date('Y-m-d')."'");
        }
        if($date=="m"){
            if($company==1 || $company==3 || $company==13){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m') = '".date('Y-m')."'");
            }else{
                $data1->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m') = '".date('Y-m')."'");
            }
            $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m') = '".date('Y-m')."'");
        }
        if($date=="y"){
            if($company==1 || $company==3 || $company==13){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y') = '".date('Y')."'");
            }else{
                $data1->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y') = '".date('Y')."'");
            }
            $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y') = '".date('Y')."'");
        }
        if($date=="q"){
            $quarter = SysHelper::get_quarter(date('m'));
            $start_date = $quarter[0];
            $end_date = $quarter[1];
            
            if($company==1 || $company==3 || $company==13){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '".$end_date."'");
            }else{
                $data1->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') <= '".$end_date."'");
            }
            $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') <= '".$end_date."'");
        }
        if($date=="pm"){
            $c_date = Carbon::createFromFormat('Y-m', date('Y-m'))->subMonth();  
            $pm_date = $c_date->format('Y-m');

            if($company==1 || $company==3 || $company==13){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m') = '".$pm_date."'");
            }else{
                $data1->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m') = '".$pm_date."'");
            }
            $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m') = '".$pm_date."'");
        }
        if($date=="pq"){
            $quarter = SysHelper::get_pre_quarter(date('m'));
            $start_date = $quarter[0];
            $end_date = $quarter[1];
            
            if($company==1 || $company==3 || $company==13){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '".$end_date."'");
            }else{
                $data1->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') <= '".$end_date."'");
            }
            $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') <= '".$end_date."'");
        }

        if(Auth::user()->role_id == 1 || Auth::user()->id==33){ //admin
            // $data1->where('sys_crm_deals.company_id',1);
            // $data2->where('sys_crm_deals.company_id',1);
        }
        else{
            if(Auth::user()->id==26 || Auth::user()->id==36 || Auth::user()->id==112){//26 Naeem & 36 Arianne
                $teams= array(26,36,112);
                $data1->wherein('sys_crm_deals.owner',$teams);
                $data2->wherein('sys_crm_deals.owner',$teams);
            }
            else{
                $data1->where('sys_crm_deals.owner',Auth::user()->id);
                $data2->where('sys_crm_deals.owner',Auth::user()->id);
            }
        }
        
        $dataA = $data1->get();
        $dataB = $data2->get();
        $retAmount=0;
        if(count($dataA)>0){
            foreach($dataA as $dt){
                $retAmount+= SysHelper::get_deal_value($dt->deal_value,$dt->source,$dt->deal_currency,$dt->deal_percent,$dt->cust_id);
                /*if(in_array($dt->dealid, [8690,8660])){$retAmount+= SysHelper::get_aed_amount($dt->deal_currency,$dt->deal_value);}
                else{
                if($dt->source=="Fulfillment"){$retAmount+= SysHelper::get_aed_amount($dt->deal_currency,($dt->deal_value*20/100));}
                else if(in_array($dt->cust_id, [2568,4258,4382,5322,7347,8144,8145,8146,3711,4089,8142])){$retAmount+= SysHelper::get_aed_amount($dt->deal_currency,($dt->deal_value*20/100));}
                else if(in_array($dt->cust_id, [8866])){$retAmount+= SysHelper::get_aed_amount($dt->deal_currency,($dt->deal_value*30/100));}
                else{$retAmount+= SysHelper::get_aed_amount($dt->deal_currency,$dt->deal_value);}
                }*/
            }
        }
        $revenue = SysHelper::com_curr_format($retAmount, 2, '.', ',');

        $retAmount2=0;
        if(count($dataB)>0){
            foreach($dataB as $dt){
                $retAmount2+= SysHelper::get_aed_amount($dt->deal_currency,$dt->deal_value);
            }
        }
        $forecast = SysHelper::com_curr_format($retAmount2, 2, '.', ',');

        return [$revenue,$forecast];
    }
    public static function get_total_project_revenue_all($date, $company){

        $revenue1 = DB::table('sys_crm_deals')->select('sys_crm_deals.id as dealid','deal_value','deal_currency','source','cust_id','deal_percent')
        ->leftjoin('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_id','sys_crm_deals.id')
        ->leftjoin('sys_crm_deal_track_approval_receivables','sys_crm_deal_track_approval_receivables.deal_id','sys_crm_deals.id')
        ->where('sys_crm_deals.isproject',1)
        ->where('sys_crm_deals.stage',4)
        //->where('sys_crm_deals.is_partial_invoice',0)
        //->where('sys_crm_deal_track_approval_invoice.status',1)
        ->where(function ($query) {
            $query->orwhere('sys_crm_deal_track_approval_invoice.status',1)
            ->orwhere('sys_crm_deal_track_approval_receivables.status',1);
        });
        //->wherein('sys_crm_deals.company_id',[1,3,13]);
        
        $revenue2 = DB::table('sys_crm_deals')->select('sys_crm_deals.id as dealid','deal_value','deal_currency','source','cust_id','deal_percent')
        ->where('sys_crm_deals.isproject',1)        
        ->where('sys_crm_deals.stage',4);
        //->wherenotin('sys_crm_deals.company_id',[1,3,13]);

        $forcast = DB::table('sys_crm_deals')->select('deal_value','deal_currency','source','cust_id')        
        ->where('sys_crm_deals.isproject',1)
        //->wherein('stage',[1,2,3])
        ->whereNotIn('sys_crm_deals.id',function($query){
			$query->select('deal_id')->from('sys_crm_deal_track_approval_invoice')->where('status',1);
		 });
        
        if($date=="d"){
                $revenue1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') = '".date('Y-m-d')."'");
                $revenue2->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') = '".date('Y-m-d')."'");
            $forcast->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') = '".date('Y-m-d')."'");
        }
        if($date=="m"){
                $revenue1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m') = '".date('Y-m')."'");
                $revenue2->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m') = '".date('Y-m')."'");
            $forcast->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m') = '".date('Y-m')."'");
        }
        if($date=="y"){
                $revenue1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y') = '".date('Y')."'");
                $revenue2->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y') = '".date('Y')."'");
            $forcast->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y') = '".date('Y')."'");
        }
        if($date=="q"){
            $quarter = SysHelper::get_quarter(date('m'));
            $start_date = $quarter[0];
            $end_date = $quarter[1];
            
                $revenue1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '".$end_date."'");
                $revenue2->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') <= '".$end_date."'");
            $forcast->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') <= '".$end_date."'");
        }
        if($date=="pm"){
            $c_date = Carbon::createFromFormat('Y-m', date('Y-m'))->subMonth();  
            $pm_date = $c_date->format('Y-m');

                $revenue1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m') = '".$pm_date."'");
                $revenue2->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m') = '".$pm_date."'");
            $forcast->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m') = '".$pm_date."'");
        }
        if($date=="pq"){
            $quarter = SysHelper::get_pre_quarter(date('m'));
            $start_date = $quarter[0];
            $end_date = $quarter[1];
            
                $revenue1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '".$end_date."'");
                $revenue2->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') <= '".$end_date."'");
            $forcast->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') <= '".$end_date."'");
        }

        if(Auth::user()->role_id == 1 || Auth::user()->id==33){ //admin
            // $data1->where('sys_crm_deals.company_id',1);
            // $data2->where('sys_crm_deals.company_id',1);
        }
        else{
            // if(Auth::user()->id==26 || Auth::user()->id==36 || Auth::user()->id==112){//26 Naeem & 36 Arianne
            //     $teams= array(26,36,112);
            //     $revenue1->wherein('sys_crm_deals.owner',$teams);
            //     $revenue2->wherein('sys_crm_deals.owner',$teams);
            //     $forcast->wherein('sys_crm_deals.owner',$teams);
            // }
            // else{
                $revenue1->where('sys_crm_deals.owner',Auth::user()->id);
                $revenue2->where('sys_crm_deals.owner',Auth::user()->id);
                $forcast->where('sys_crm_deals.owner',Auth::user()->id);
            //}
        }
        
        $dataA1 = $revenue1->get();
        $dataA2 = $revenue2->get();
        $dataB = $forcast->get();
        $retAmount=0;
        if(count($dataA1)>0){
            foreach($dataA1 as $dt){
                $retAmount+= SysHelper::get_deal_value($dt->deal_value,$dt->source,$dt->deal_currency,$dt->deal_percent,$dt->cust_id);
            }
        }
        if(count($dataA2)>0){
            foreach($dataA2 as $dt){
                $retAmount+= SysHelper::get_deal_value($dt->deal_value,$dt->source,$dt->deal_currency,$dt->deal_percent,$dt->cust_id);
            }
        }
        $revenue = SysHelper::com_curr_format($retAmount, 2, '.', ',');

        $retAmount2=0;
        if(count($dataB)>0){
            foreach($dataB as $dt){
                $retAmount2+= SysHelper::get_aed_amount($dt->deal_currency,$dt->deal_value);
            }
        }
        $forecast = SysHelper::com_curr_format($retAmount2, 2, '.', ',');

        return [$revenue,$forecast];
    }

    public static function get_total_sales_revenue($date, $company){

        
        $data1 = DB::table('sys_crm_deals')->select('sys_crm_deals.id as dealid','deal_value','deal_currency','source','cust_id','deal_percent')
        ->leftjoin('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_id','sys_crm_deals.id')
        ->where('sys_crm_deals.stage',4)->where('sys_crm_deals.is_partial_invoice',0)
        ->where('sys_crm_deal_track_approval_invoice.status',1)->where('sys_crm_deals.company_id',$company);
        

        $data2 = DB::table('sys_crm_deals')->select('deal_value','deal_currency','source','cust_id')
        ->where('sys_crm_deals.company_id',$company)
        ->wherein('stage',[1,2,3])
        ->whereNotIn('id',function($query){
            $query->select('deal_id')->from('sys_crm_deal_track_approval_invoice')->where('status',1);
         });

        $data3 = DB::table('sys_crm_deals')->select('deal_value','deal_currency','source','cust_id')
        ->where('sys_crm_deals.company_id',$company)
        ->wherein('stage',[5,6]);
        
        if($date=="d"){
            $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') = '".date('Y-m-d')."'");
            $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') = '".date('Y-m-d')."'");
            $data3->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') = '".date('Y-m-d')."'");
        }
        if($date=="m"){
            $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m') = '".date('Y-m')."'");
            $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m') = '".date('Y-m')."'");
            $data3->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m') = '".date('Y-m')."'");
        }
        if($date=="y"){
            $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y') = '".date('Y')."'");
            $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y') = '".date('Y')."'");
            $data3->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y') = '".date('Y')."'");
        }
        if($date=="q"){
            $quarter = SysHelper::get_quarter(date('m'));
            $start_date = $quarter[0];
            $end_date = $quarter[1];            
            $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '".$end_date."'");
            $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') <= '".$end_date."'");
            $data3->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') <= '".$end_date."'");
        }
        if($date=="pm"){
            $c_date = Carbon::createFromFormat('Y-m', date('Y-m'))->subMonth();  
            $pm_date = $c_date->format('Y-m');

            $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m') = '".$pm_date."'");
            $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m') = '".$pm_date."'");
            $data3->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m') = '".$pm_date."'");
        }
        if($date=="pq"){
            $quarter = SysHelper::get_pre_quarter(date('m'));
            $start_date = $quarter[0];
            $end_date = $quarter[1];
            
            $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '".$end_date."'");
            $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') <= '".$end_date."'");
            $data3->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') <= '".$end_date."'");
        }

        if(Auth::user()->role_id == 1 || Auth::user()->id==33){ //admin
            // $data1->where('sys_crm_deals.company_id',1);
            // $data2->where('sys_crm_deals.company_id',1);
        }
        else{
            $data1->where('sys_crm_deals.owner',Auth::user()->id);
            $data2->where('sys_crm_deals.owner',Auth::user()->id);
            $data3->where('sys_crm_deals.owner',Auth::user()->id);
        }
        
        $dataA = $data1->get();
        $dataB = $data2->get();
        $dataC = $data3->get();
        $retAmount=0;
        if(count($dataA)>0){
            foreach($dataA as $dt){
                $retAmount+= SysHelper::get_deal_value($dt->deal_value,$dt->source,$dt->deal_currency,$dt->deal_percent,$dt->cust_id);
            }
        }
        $revenue = SysHelper::com_curr_format($retAmount, 2, '.', ',');

        $retAmount2=0;
        if(count($dataB)>0){
            foreach($dataB as $dt){
                $retAmount2+= SysHelper::get_aed_amount($dt->deal_currency,$dt->deal_value);
            }
        }
        $forecast = SysHelper::com_curr_format($retAmount2, 2, '.', ',');

        $retAmount3=0;
        if(count($dataC)>0){
            foreach($dataC as $dt){
                $retAmount3+= SysHelper::get_aed_amount($dt->deal_currency,$dt->deal_value);
            }
        }
        $lost = SysHelper::com_curr_format($retAmount3, 2, '.', ',');

        return [0,0,0];
        return [$revenue,$forecast,$lost];
    }
    
    public static function get_total_sales_revenue_all($date, $company){

        $revenue1 = DB::table('sys_crm_deals')->select('sys_crm_deals.id as dealid','deal_value','deal_currency','source','cust_id','deal_percent')
        ->leftjoin('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_id','sys_crm_deals.id')
        ->leftjoin('sys_crm_deal_track_approval_receivables','sys_crm_deal_track_approval_receivables.deal_id','sys_crm_deals.id')
        ->where('sys_crm_deals.stage',4)
        //->where('sys_crm_deals.is_partial_invoice',0)
        //->where('sys_crm_deal_track_approval_invoice.status',1);
        ->where(function ($query) {
            $query->orwhere('sys_crm_deal_track_approval_invoice.status',1)
            ->orwhere('sys_crm_deal_track_approval_receivables.status',1);
        });
        
        

        $forcast = DB::table('sys_crm_deals')->select('deal_value','deal_currency','source','cust_id')
        ->wherein('stage',[3])        
        ->whereNotIn('id',function($query){
            $query->select('deal_id')->from('sys_crm_deal_track_approval_invoice')->where('status',1);
         });

        $lost = DB::table('sys_crm_deals')->select('deal_value','deal_currency','source','cust_id')
        ->wherein('stage',[5,6]);
        
        if($date=="d"){
                $revenue1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') = '".date('Y-m-d')."'");
                
            $forcast->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') = '".date('Y-m-d')."'");
            $lost->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') = '".date('Y-m-d')."'");
        }
        if($date=="m"){
                $revenue1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m') = '".date('Y-m')."'");
                
            $forcast->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m') = '".date('Y-m')."'");
            $lost->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m') = '".date('Y-m')."'");
        }
        if($date=="y"){
                $revenue1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y') = '".date('Y')."'");
               
            $forcast->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y') = '".date('Y')."'");
            $lost->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y') = '".date('Y')."'");
        }
        if($date=="q"){
            $quarter = SysHelper::get_quarter(date('m'));
            $start_date = $quarter[0];
            $end_date = $quarter[1];            
            
                $revenue1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '".$end_date."'");
                
            $forcast->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') <= '".$end_date."'");
            $lost->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') <= '".$end_date."'");
        }
        if($date=="pm"){
            $c_date = Carbon::createFromFormat('Y-m', date('Y-m'))->subMonth();  
            $pm_date = $c_date->format('Y-m');

                $revenue1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m') = '".$pm_date."'");
                
            $forcast->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m') = '".$pm_date."'");
            $lost->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m') = '".$pm_date."'");
        }
        if($date=="pq"){
            $quarter = SysHelper::get_pre_quarter(date('m'));
            $start_date = $quarter[0];
            $end_date = $quarter[1];
            
                $revenue1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '".$end_date."'");
               
            $forcast->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') <= '".$end_date."'");
            $lost->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') <= '".$end_date."'");
        }

        if(Auth::user()->role_id == 1 || Auth::user()->id==33){ //admin
            // $data1->where('sys_crm_deals.company_id',1);
            // $data2->where('sys_crm_deals.company_id',1);
        }
        else{
            if(Auth::user()->id==26 || Auth::user()->id==36 || Auth::user()->id==112){//26 Naeem & 36 Arianne
                $teams= array(26,36,112);
                $revenue1->wherein('sys_crm_deals.owner',$teams);
                
                $forcast->wherein('sys_crm_deals.owner',$teams);
                $lost->wherein('sys_crm_deals.owner',$teams);
            }
            else if(Auth::user()->id==44){//44 rajiv
                $teams= array(44,34,32,79);
                $revenue1->wherein('sys_crm_deals.owner',$teams);
                
                $forcast->wherein('sys_crm_deals.owner',$teams);
                $lost->wherein('sys_crm_deals.owner',$teams);
            }
            else{
                $revenue1->where('sys_crm_deals.owner',Auth::user()->id);
                
                $forcast->where('sys_crm_deals.owner',Auth::user()->id);
                $lost->where('sys_crm_deals.owner',Auth::user()->id);
            }
        }
        
        $dataA1 = $revenue1->get();
        $dataB = $forcast->get();
        $dataC = $lost->get();
        $retAmount=0;
        if(count($dataA1)>0){
            foreach($dataA1 as $dt){
                $retAmount+= SysHelper::get_deal_value($dt->deal_value,$dt->source,$dt->deal_currency,$dt->deal_percent,$dt->cust_id);
            }
        }
        $revenue = SysHelper::com_curr_format($retAmount, 2, '.', ',');

        $retAmount2=0;
        if(count($dataB)>0){
            foreach($dataB as $dt){
                $retAmount2+= SysHelper::get_aed_amount($dt->deal_currency,$dt->deal_value);
            }
        }
        $forecast = SysHelper::com_curr_format($retAmount2, 2, '.', ',');

        $retAmount3=0;
        if(count($dataC)>0){
            foreach($dataC as $dt){
                $retAmount3+= SysHelper::get_aed_amount($dt->deal_currency,$dt->deal_value);
            }
        }
        $lost = SysHelper::com_curr_format($retAmount3, 2, '.', ',');

        return [$revenue,$forecast,$lost];
    }

    public static function set_deal_profit($deal_id){
        $deal = SysCrmDeals::where('id',$deal_id)->first();
        $quoteitems = SysCrmQuoteItems::select('sys_crm_quote_items.*','sm_items.part_number','sys_brand.title')
            ->leftjoin('sm_items','sm_items.id','sys_crm_quote_items.product_id')
            ->leftjoin('sys_brand','sys_brand.id','sm_items.brand')
            ->where('deal_id',$deal->id)->where('quote_id',$deal->quote_id)->orderby('sort_id','ASC')->get();
            
            $extra_charges=0;
            $quote_charges = SysCrmQuoteCharges::where('deal_id',$deal->id)->where('quote_id',$deal->quote_id)->get();
            if(count($quote_charges)>0){
                $extra_charges=$quote_charges->sum('amount');
            }

            $purchase_auto = SysPurchaseAuto::where(['deal_id' => $deal->id, 'status' => 2, 'req_cost' => 1])->pluck('po_id');
            $purchase_cost=0;
            if(count($purchase_auto)>0){
                $purchase_cost_query = SysPurchaseOrderItems::select('unitprice as total_cost','qty as total_qty')->wherein('po_id',$purchase_auto)->get();
                if(count($purchase_cost_query)>0){
                    foreach($purchase_cost_query as $p){
                        $purchase_cost += $p->total_cost*$p->total_qty;
                    }
                }
            }

            $dln_cost = SysDeliveryNote::select(DB::raw('ifnull(sum(taxableamount),0) as total_cost'))
            ->join('sys_delivery_note_items as itm','itm.dn_id','sys_delivery_note.id')
            ->where('deal_id',$deal->id)->where('is_deal_aditional',1)->get();
            if(count($dln_cost)>0){
                $dln_cost = $dln_cost[0]->total_cost;
            } else {$dln_cost = 0;}

            //$jv = SysJournalVoucher::where('deal_id',$deal->id)->get();
            $jv = SysChartofAccountsTransaction::where('transaction_ref',$deal->id)->get();
            $jv_cost=0;
            if(count($jv)>0){

                $jv_cost = SysChartofAccountsTransaction::select(DB::raw('ifnull(sum(debit_amount),0) as total_cost'))
                ->where('transaction_ref',$deal->id)
                ->wherein('transaction_no',$jv->pluck('transaction_no'))->get();
                
                $jv_cost = $jv_cost[0]->total_cost;
            }

            


        if(count($quoteitems)>0){
            $net=0;
            $vat=0;
            $curr=1;
            $delivery_date='';
            $cost_profit=0;
            $deal_cost=0;
            foreach($quoteitems as $itms){

                // $grn = DB::table('sys_purchase_grn_items as grnit')->select('unitprice as lp_price')
                // ->join('sys_purchase_grn as grn','grn.id','grnit.grn_id')
                // ->where('grn.company_id',session('logged_session_data.company_id'))
                // //->where('part_no',$itms->product_id)
                // ->where('part_no',99999999999999999)
                // ->limit(1)
                // ->orderby('grn_id','desc')->first();

                $qty = $itms->qty;
            
                $price = $itms->price;
                $discount = $itms->discount;
                $net += ($price * $qty) - ($discount);
                $curr = $itms->currency_id;
                $delivery_date = $itms->delivery_date;
                $cost_profit += (($price * $qty) - ($discount)) - ($itms->cost*$itms->qty);
                
                $deal_cost += ($itms->cost * $qty);

                // if(isset($grn)){
                //     if($itms->cost < $grn->lp_price){
                //         $deal_cost += ($grn->lp_price * $qty) * 100/100;
                //     } else{
                //         $deal_cost += ($itms->cost * $qty) * 100/100;
                //     }
                // }
                //else{
                    //$deal_cost += ($itms->cost * $qty) * 100/100;
                //}                                
                
            }


            
            $deal_value = $net - $deal->deal_discount;
            $cost = $deal_cost+$extra_charges+$purchase_cost+$dln_cost+$jv_cost;
            $deal_profit = $deal_value-$cost;
            //return $net;

            
            
            if($net > 0){
                
                // dd($curr);
                DB::table('sys_crm_deals')->where('id',$deal_id)
                ->update([
                    'deal_value' => $deal_value,
                    // 'deal_currency' => $curr,
                    'deal_profit' => $deal_profit,
                ]);
            }
            
            
        }    
    }
    public static function get_total_sales_gp($date, $company){

        $revenue1 = DB::table('sys_crm_deals')->select('sys_crm_deals.id as dealid','deal_value','deal_currency','source','cust_id','deal_profit')
        ->leftjoin('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_id','sys_crm_deals.id')
        ->where('sys_crm_deals.stage',4)->where('sys_crm_deals.is_partial_invoice',0)
        ->where('sys_crm_deal_track_approval_invoice.status',1)->wherein('sys_crm_deals.company_id',[$company]);

        $revenue2 = DB::table('sys_crm_deals')->select('sys_crm_deals.id as dealid','deal_value','deal_currency','source','cust_id','price','qty')
        ->leftjoin('sys_crm_quote_items','sys_crm_quote_items.deal_id','sys_crm_deals.id')
        ->leftjoin('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_id','sys_crm_deals.id')
        ->wherein('sys_crm_quote_items.product_id',[8490,8544,9726,9728,9780,10294,10624,10673,11722,12049,12487])
        ->where('sys_crm_deals.stage',4)->where('sys_crm_deals.is_partial_invoice',0)
        ->where('sys_crm_deal_track_approval_invoice.status',1)->wherein('sys_crm_deals.company_id',[$company]);
        
        if($date=="d"){
                $revenue1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') = '".date('Y-m-d')."'");
                $revenue2->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') = '".date('Y-m-d')."'");
        }
        if($date=="m"){
                $revenue1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m') = '".date('Y-m')."'");
                $revenue2->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m') = '".date('Y-m')."'");
        }
        if($date=="y"){
                $revenue1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y') = '".date('Y')."'");
                $revenue2->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y') = '".date('Y')."'");
        }
        if($date=="q"){
            $quarter = SysHelper::get_quarter(date('m'));
            $start_date = $quarter[0];
            $end_date = $quarter[1];            
            
                $revenue1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '".$end_date."'");
                $revenue2->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') <= '".$end_date."'");
        }
        if($date=="pm"){
            $c_date = Carbon::createFromFormat('Y-m', date('Y-m'))->subMonth();  
            $pm_date = $c_date->format('Y-m');

                $revenue1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m') = '".$pm_date."'");
                $revenue2->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m') = '".$pm_date."'");
        }
        if($date=="pq"){
            $quarter = SysHelper::get_pre_quarter(date('m'));
            $start_date = $quarter[0];
            $end_date = $quarter[1];
            
                $revenue1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '".$end_date."'");
                $revenue2->whereRaw("DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(sys_crm_deals.date, '%Y-%m-%d') <= '".$end_date."'");
        }

        if(Auth::user()->role_id == 1 || Auth::user()->id==33){ //admin
            // $data1->where('sys_crm_deals.company_id',1);
            // $data2->where('sys_crm_deals.company_id',1);
        }
        else{
            $revenue1->where('sys_crm_deals.owner',Auth::user()->id);
            $revenue2->where('sys_crm_deals.owner',Auth::user()->id);
        }
        
        $dataA1 = $revenue1->get();
        $dataA2 = $revenue2->get();
        $retAmount=0;
        if(count($dataA1)>0){
            foreach($dataA1 as $dt){
                $retAmount+= SysHelper::get_aed_amount($dt->deal_currency,$dt->deal_value);
            }
        }
        if(count($dataA2)>0){
            foreach($dataA2 as $dt){
                $retAmount+= SysHelper::get_aed_amount($dt->deal_currency,($dt->qty*$dt->price));
            }
        }
        $revenue = SysHelper::com_curr_format($retAmount, 2, '.', ',');

        return [$revenue];
    }

    public static function get_deal_count_by_user($user,$m1,$m2,$company_list,$ps_amc_deal_id){
        $data1 = SysCrmDeals::select('sys_crm_deals.*','sys_crm_deal_track.invoice','sys_crm_deal_track.delivery','sys_crm_deal_track.receivables')
            ->join('sys_sales_invoice','sys_sales_invoice.deal_id','sys_crm_deals.id')
            ->leftjoin('sys_crm_deal_track','sys_crm_deal_track.deal_id','sys_crm_deals.id')
            ->leftjoin('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_id','sys_crm_deals.id')
            ->leftjoin('sys_crm_deal_track_approval_receivables','sys_crm_deal_track_approval_receivables.deal_id','sys_crm_deals.id')
            ->where('stage',4);     
            if($m1 !="" && $m2 !=""){
                $data1->whereRaw("DATE_FORMAT(sys_sales_invoice.doc_date, '%Y-%m-%d') >= '".date('Y-m-d', strtotime($m1))."' and DATE_FORMAT(sys_sales_invoice.doc_date, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($m2))."'");
            }
            if($m1 !="" && $m2 ==""){
                $data1->whereRaw("DATE_FORMAT(sys_sales_invoice.doc_date, '%Y-%m-%d') = '".date('Y-m-d', strtotime($m1))."'");
            }
            $data1->where('sys_crm_deals.stage',4)
            //->where('sys_crm_deal_track_approval_invoice.status',1)
            ->where(function ($query) {
                $query->orwhere('sys_crm_deal_track_approval_invoice.status',1)
                ->orwhere('sys_crm_deal_track_approval_receivables.status',1);
            });
            //->where('sys_crm_deals.company_id',session('logged_session_data.company_id'))
            //->where('sys_crm_deals.is_partial_invoice',0);
            $data1->wherein('sys_crm_deals.company_id',$company_list);
            
            $data1->wherein('sys_crm_deals.owner',$user);
            // if(count($ps_amc_deal_id)>0){                
            //     $data1->wherein('sys_crm_deals.id',$ps_amc_deal_id);
            // }

            $deals1 = $data1->count();
            return $deals1;
    }
    
    public static function get_deal_count_by_company($m1,$m2,$company_id,$ps_amc_deal_id) {
        $data1 = SysCrmDeals::select('sys_crm_deals.*','sys_crm_deal_track.invoice','sys_crm_deal_track.delivery','sys_crm_deal_track.receivables')
            ->join('sys_sales_invoice','sys_sales_invoice.deal_id','sys_crm_deals.id')
            ->leftjoin('sys_crm_deal_track','sys_crm_deal_track.deal_id','sys_crm_deals.id')
            ->leftjoin('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_id','sys_crm_deals.id')
            ->leftjoin('sys_crm_deal_track_approval_receivables','sys_crm_deal_track_approval_receivables.deal_id','sys_crm_deals.id')
            ->where('stage',4);     
            if($m1 !="" && $m2 !=""){
                $data1->whereRaw("DATE_FORMAT(sys_sales_invoice.doc_date, '%Y-%m-%d') >= '".date('Y-m-d', strtotime($m1))."' and DATE_FORMAT(sys_sales_invoice.doc_date, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($m2))."'");
            }
            if($m1 !="" && $m2 ==""){
                $data1->whereRaw("DATE_FORMAT(sys_sales_invoice.doc_date, '%Y-%m-%d') = '".date('Y-m-d', strtotime($m1))."'");
            }
            $data1->where('sys_crm_deals.stage',4)
            //->where('sys_crm_deal_track_approval_invoice.status',1)
            ->where(function ($query) {
                $query->orwhere('sys_crm_deal_track_approval_invoice.status',1)
                ->orwhere('sys_crm_deal_track_approval_receivables.status',1);
            });
            //->where('sys_crm_deals.company_id',session('logged_session_data.company_id'))
            //->where('sys_crm_deals.is_partial_invoice',0);
            if($company_id != 1){
                $data1->where('sys_crm_deals.company_id',$company_id);
            }
            // if(count($ps_amc_deal_id)>0){                
            //     $data1->wherein('sys_crm_deals.id',$ps_amc_deal_id);
            // }

            $deals1 = $data1->count();
            return $deals1;
    }

    public static function get_deals_close_date($uid=null){        
        $query = SysCrmDeals::select('id','deal_name','estimated_close_date','owner','stage','code')->wherein('stage',[1,2,3]);
        $query->whereRaw("DATE_FORMAT(estimated_close_date, '%Y-%m-%d') <= '".date('Y-m-d')."'");
        if($uid!=null){
            $query->wherein('owner', $uid);
        }
        $data = $query->orderby('estimated_close_date','asc')->orderby('estimated_close_date','asc')->get();
        return $data;        
    }

    public static function set_track($deal_id)
    {
        //return 1;
        $data = DB::table('sys_crm_quote_items')->select('company_id')->where('deal_id',$deal_id)->first();
        if(isset($data)){
                return 1;
        } else{
            return 0;
        }
    }

    public static function get_vat($currency_id){
        if($currency_id==1){ return 5; }
        else if($currency_id==2){ return 5; }
        else if($currency_id==3){ return 5; }
        else if($currency_id==4){ return 15; }
        else if($currency_id==5){ return 5; }
        else if($currency_id==6){ return 18; }
        else if($currency_id==7){ return 5; }
        else if($currency_id==8){ return 5; }
        else if($currency_id==9){ return 20; }
        else{ return 5; }
    }
    
    public static function deal_track_status($deal_id)
    {
        $data = SysCrmDealTrack::select('accounts','sales','purchease','invoice','delivery','receivables')->where('deal_id',$deal_id)->first();
        if(isset($data)){
            if($data->accounts==0 && $data->sales==0 && $data->purchease==0 && $data->invoice==0 && $data->delivery==0 && $data->receivables==0){
                return "Pending";
            }else if($data->accounts==1 && $data->sales==1 && $data->purchease==1 && $data->invoice==1 && $data->delivery==1 && $data->receivables==1){
                return "completed";
            } else{
                return "OnProcess";
            }
        } else{
            return "Fulfill";
        }
    }
    
    public static function deal_track_status2($deal_id)
    {
        $data = SysCrmDealTrack::select('accounts','sales','purchease','invoice','delivery','receivables')->where('deal_id',$deal_id)->first();
        if(isset($data)){
            if($data->invoice==0){
                return "Invoic Pending";
            }else if($data->invoice==1){
                return "Invoiced";
            } else{
                return "Invoic OnProcess";
            }
        } else{
            return "Fulfill";
        }
    }
    public static function deal_track_status3($receivables,$delivery,$invoice,$purchease,$sales,$accounts)
    {
        if($receivables==1){ return "<span class='badge bg-success'>Payment Received</span>";}
        else if($receivables==2){ 
            return "<span class='badge bg-danger'>Payment Rejected</span>";}
        else if($receivables==3){ 
            return "<span class='badge bg-primary'>Payment Pending</span>";}
        else if($receivables==4){ 
            return "<span class='badge bg-dark'>Order Cancelled</span>";}
        else if($delivery==1){ 
            return "<span class='badge bg-success '>Delivery Completed</span>";}
        else if($delivery==2){ 
            return "<span class='badge bg-danger'>Delivery Rejected</span>";}
        else if($delivery==3){ 
            return "<span class='badge bg-primary'>Out For Delivery</span>";}
        else if($delivery==4){ 
            return "<span class='badge bg-primary'>Pending For Delivery</span>";}
        else if($delivery==5){ 
            return "<span class='badge bg-primary'>Ready For Delivery</span>";}
        else if($invoice==1){ 
            return "<span class='badge bg-success'>Invoice Approved</span>";}
        else if($invoice==2){ 
            return "<span class='badge bg-danger'>Invoice Disapproved</span>";}
        else if($invoice==3){ 
            return "<span class='badge bg-primary'>Invoice Pending</span>";}
        else if($purchease==1){ 
            return "<span class='badge bg-success'>Purchase Approved</span>";}
        else if($purchease==2){ 
            return "<span class='badge bg-danger'>Purchase Disapproved</span>";}
        else if($purchease==3){ 
            return "<span class='badge bg-primary'>Purchase Pending</span>";}
        else if($purchease==4){ 
            return "<span class='badge bg-primary'>Partial Delivery</span>";}
        else if($sales==1){ 
            return "<span class='badge bg-success'>Sales Approved</span>";}
        else if($sales==2){ 
            return "<span class='badge bg-success'>Sales Disapproved</span>";}
        else if($sales==3){ 
            return "<span class='badge bg-primary'>Sales Pending</span>";}
        else if($accounts==1){ 
            return "<span class='badge bg-success'>Accounts Approved</span>";}
        else if($accounts==2){ 
            return "<span class='badge bg-danger'>Accounts Disapproved</span>";}
        else if($accounts==3){ 
            return "<span class='badge bg-primary'>Accounts Pending</span>";}
        else if($accounts==0){ 
            return "<span class='badge bg-success'>Accounts Pending</span>";}
        else if($sales==0){ 
            return "<span class='badge bg-success'>Sales Pending</span>";}
        else if($purchease==0){ 
            return "<span class='badge bg-success'>Purchase Pending</span>";}
        else if($invoice==0){ 
            return "<span class='badge bg-success'>Invoice Pending</span>";}
        else if($delivery==0){ 
            return "<span class='badge bg-success'>Delivery Pending</span>";}
        else if($receivables==0){ 
            return "<span class='badge bg-success'>Payment Pending</span>";}
        else { 
            return "<span class='badge bg-warning'>New</span>";}
    }
    public static function deal_stage($stage)
    {
        if($stage==1){ return "<span class='secondary btn-badge py-0 px-1'>Prospecting</span>";}
        else if($stage==2){ return "<span class='info btn-badge py-0 px-1'>Quote</span>";}
        else if($stage==3){ return "<span class='warning btn-badge py-0 px-1'>Closure</span>";}
        else if($stage==4){ return "<span class='success btn-badge py-0 px-1'>Won</span>";}
        else if($stage==5){ return "<span class='danger btn-badge py-0 px-1'>Lost</span>";}
        else { return "<span class='warning btn-badge py-0 px-1'>New</span>";}
    }

    public static function deal_pipeline($deal_id)
    {
        $deal = SysCrmDeals::select('id','stage','quote_id')->find($deal_id);

        if(!$deal){
            return '';
        }

        $quote_id = $deal->quote_id;
        $quote = SysCrmQuoteItems::where('deal_id', $deal_id)->where('quote_id', $quote_id)->first();

        $stageLabels = [
            1 => 'Prospecting',
            2 => 'Quote',
            3 => 'Closure',
            4 => 'Won',
            5 => 'Lost',
        ];

        $stageColors = [
            1 => 'secondary',
            2 => 'info',
            3 => 'warning',
            4 => 'success',
            5 => 'danger',
        ];

        $stage = intval($deal->stage);
        $stageLabel = $stageLabels[$stage] ?? 'Unknown';
        $stageColor = $stageColors[$stage] ?? 'secondary';

        $html = '<div class="pipeline-wrapper ms-2">';
       

        if ($stage === 4) {
            $data = self::deal_track_status($deal_id);
            $color = 'danger';
            if ($data == 'Pending') {
                $color = 'warning';
            } elseif ($data == 'completed') {
                $color = 'primary';
            } elseif ($data == 'OnProcess') {
                $color = 'info';
            }
            $html .= '<div class="pipeline-arrow deal-track-sales-person ' . $color . '" data-id="' . $deal_id . '">' . ucfirst($data) . '</div>';   
        }else{
            $html .= '<div class="pipeline-arrow ' . $stageColor . '">' . $stageLabel . '</div>';
        }

      


        if($quote){
            $html .= '<div class="pipeline-arrow bg-info text-white"><a class="text-white" href="' . url('crm-deals/show/'.$deal_id) . '">' . $quote->document_number . '</a></div>';
        }

        $proforma = SysProformaInvoice::where('deal_id', $deal_id)->first();
        if($proforma){
            $html .= '<div class="pipeline-arrow bg-primary text-white"><a class="text-white" href="' . url('proforma-invoice/'.$proforma->id) . '">' . $proforma->doc_number . '</a></div>';
        }

        $salesInvoices = SysSalesInvoice::where('deal_id', $deal_id)->get();
        if ($salesInvoices->isNotEmpty()) {
            $invoiceLinks = [];
            foreach ($salesInvoices as $salesInvoice) {
                $invoiceLinks[] = '<a class="text-white" href="' . url('sales-invoice/' . $salesInvoice->id) . '">' . $salesInvoice->doc_number . '</a>';
            }
            $html .= '<div class="pipeline-arrow bg-success text-white">' . implode(', ', $invoiceLinks) . '</div>';
        }

        $deliveryNotes = SysDeliveryNote::where('deal_id', $deal_id)->get();
        if ($deliveryNotes->isNotEmpty()) {
            $deliveryLinks = [];
            foreach ($deliveryNotes as $deliveryNote) {
                $deliveryLinks[] = '<a class="text-white" href="' . url('delivery-note/' . $deliveryNote->id) . '">' . $deliveryNote->doc_number . '</a>';
            }
            $html .= '<div class="pipeline-arrow bg-secondary text-white">' . implode(', ', $deliveryLinks) . '</div>';
        }

        $salesReturns = SysSalesReturn::where('deal_id', $deal_id)->get();
        if ($salesReturns->isNotEmpty()) {
            $returnLinks = [];
            foreach ($salesReturns as $salesReturn) {
                $returnLinks[] = '<a class="text-white" href="' . url('sales-return/' . $salesReturn->id) . '">' . $salesReturn->doc_number . '</a>';
            }
            $html .= '<div class="pipeline-arrow bg-danger text-white">' . implode(', ', $returnLinks) . '</div>';
        }

        $receipts = SysReceipt::where('deal_id', $deal_id)->get();
        if ($receipts->isNotEmpty()) {
            $receiptLinks = [];
            foreach ($receipts as $receipt) {
                $receiptLinks[] = '<a class="text-white" href="' . url('receipt/' . $receipt->id ) . '">' . $receipt->doc_number . '</a>';
            }
            $html .= '<div class="pipeline-arrow bg-dark text-white">' . implode(', ', $receiptLinks) . '</div>';
        }

        $html .= '</div>';
        return $html;
    }

     public static function deal_pipeline_purchase($deal_id)
    {
       
 $deal = SysCrmDeals::select('id','stage','quote_id')->find($deal_id);

        if(!$deal){
            return '';
        }

        $quote_id = $deal->quote_id;
        $quote = SysCrmQuoteItems::where('deal_id', $deal_id)->where('quote_id', $quote_id)->first();

        $stageLabels = [
            1 => 'Prospecting',
            2 => 'Quote',
            3 => 'Closure',
            4 => 'Won',
            5 => 'Lost',
        ];

        $stageColors = [
            1 => 'secondary',
            2 => 'info',
            3 => 'warning',
            4 => 'success',
            5 => 'danger',
        ];

        $stage = intval($deal->stage);
        $stageLabel = $stageLabels[$stage] ?? 'Unknown';
        $stageColor = $stageColors[$stage] ?? 'secondary';

        $html = '<div class="pipeline-wrapper ms-2">';
       

        if ($stage === 4) {
            $data = self::deal_track_status($deal_id);
            $color = 'danger';
            if ($data == 'Pending') {
                $color = 'warning';
            } elseif ($data == 'completed') {
                $color = 'primary';
            } elseif ($data == 'OnProcess') {
                $color = 'info';
            }
            $html .= '<div class="pipeline-arrow deal-track-sales-person ' . $color . '" data-id="' . $deal_id . '">' . ucfirst($data) . '</div>';   
        }else{
            $html .= '<div class="pipeline-arrow ' . $stageColor . '">' . $stageLabel . '</div>';
        }

      


        if($quote){
            $html .= '<div class="pipeline-arrow bg-info text-white"><a class="text-white" href="' . url('crm-deals/show/'.$deal_id) . '">' . $quote->document_number . '</a></div>';
        }

        $purchaseOrders = SysPurchaseOrder::where('deal_id', $deal_id)->get();
        if ($purchaseOrders->isNotEmpty()) {
            $poLinks = [];
            foreach ($purchaseOrders as $po) {
                $poLinks[] = '<a class="text-white" href="' . url('purchase-order/'.$po->id) . '">' . $po->doc_number . '</a>';
            }
            $html .= '<div class="pipeline-arrow bg-dark text-white">' . implode(', ', $poLinks) . '</div>';
        }

        $purchaseGrns = SysPurchaseGRN::where('deal_id', $deal_id)->get();
        if ($purchaseGrns->isNotEmpty()) {
            $grnLinks = [];
            foreach ($purchaseGrns as $grn) {
                $grnLinks[] = '<a class="text-white" href="' . url('goods-receipt-note-list/'.$grn->id) . '">' . $grn->doc_number . '</a>';
            }
            $html .= '<div class="pipeline-arrow bg-secondary text-white">' . implode(', ', $grnLinks) . '</div>';
        }

        $purchaseInvoices = SysPurchaseInvoice::where('deal_id', $deal_id)->get();
        if ($purchaseInvoices->isNotEmpty()) {
            $piLinks = [];
            foreach ($purchaseInvoices as $pi) {
                $piLinks[] = '<a class="text-white" href="' . url('purchase-invoice/'.$pi->id) . '">' . $pi->doc_number . '</a>';
            }
            $html .= '<div class="pipeline-arrow bg-success text-white">' . implode(', ', $piLinks) . '</div>';
        }

        $purchaseReturns = SysPurchaseReturn::where('deal_id', $deal_id)->get();
        if ($purchaseReturns->isNotEmpty()) {
            $prLinks = [];
            foreach ($purchaseReturns as $pr) {
                $prLinks[] = '<a class="text-white" href="' . url('purchase-return/'.$pr->id) . '">' . $pr->doc_number . '</a>';
            }
            $html .= '<div class="pipeline-arrow bg-danger text-white">' . implode(', ', $prLinks) . '</div>';
        }

        $payments = SysPayment::where('deal_id', $deal_id)->get();
        if ($payments->isNotEmpty()) {
            $paymentLinks = [];
            foreach ($payments as $payment) {
                $paymentLinks[] = '<a class="text-white" href="' . url('payment/'.$payment->id) . '">' . $payment->doc_number . '</a>';
            }
            $html .= '<div class="pipeline-arrow bg-warning text-white">' . implode(', ', $paymentLinks) . '</div>';
        }

        $html .= '</div>';
        return $html;
    }

    public static function gitex_lead_import()
    {
        try {
            
        $data = DB::table('gitex_lead_2024')->where('status',1)->get();
        return $data;

        foreach($data as $dt)
        {
            
            $cust = SysCustSuppl::select('id','name','vat_country')->Where('name', 'like', '%'.$dt->company_name.'%')->first();
            if(isset($cust)){
                $company_id= $cust->id;
                $cname= $cust->vat_country;
            }
            else{

                $new_customer = new SysCustSuppl();
            $new_customer->group=1;
            $new_customer->catid=1;  // 1 customers, 2 suppliers
            $new_customer->account_type = 1;
            $new_customer->customer_salutation = 'Mr';
            $new_customer->first_name = $dt->first_name;
            $new_customer->designation = $dt->designation;
            $new_customer->last_name = $dt->last_name;
            $new_customer->name = $dt->company_name;
            $new_customer->customer_name_display = strtoupper($dt->company_name);
            $new_customer->code = SysHelper::get_new_customer_code();
            $new_customer->address = '';
            $new_customer->address2 = '';
            $new_customer->contcat_person = $dt->first_name.' '.$dt->last_name;;
            $new_customer->contcat_number = $dt->contact_number;
            $new_customer->mobile = '';
            $new_customer->email = $dt->email;
            $new_customer->sales_person = $dt->sales_person;
            //$new_customer->vat_type = $request->vat_type;
            $new_customer->customer_type = 5;
            $new_customer->sale_type = 5;
            $new_customer->vat_country = $request->country_vat;
            //$new_customer->vat_state = $request->state_vat;
            $new_customer->city = $request->city;
            $new_customer->zip_code = '';
            $new_customer->vat_percentage = 5;
            $new_customer->vat_number = '';
            $new_customer->credit_limit = 0;
            $new_customer->credit_days = 0;
            $new_customer->payment_terms = 0;
            $new_customer->transaction_type = 0;
                
            //$new_customer->customer_documents = $customer_documents;
            $new_customer->status = 1;
            $new_customer->vat_is_fixed=0;
            $new_customer->created_by = Auth::user()->id;
            $new_customer->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
            $new_customer->type = 1;
            $new_customer->company_access = '1,6';
            $new_customer->company_id = 6;
            $results1 = $new_customer->save();

///////////////////////////////

            $new_customer = new SysCustSuppl();
            $new_customer->group=2;  // 1 accounts, 2 customers, 3 suppliers
            $new_customer->catid=1;  // 1 customers, 2 suppliers
            $new_customer->name = $dt->company_name;
            $new_customer->code = 'CUS' . sprintf('%03d', SysHelper::get_new_maxid('sys_cust_suppl', 'id'));
            $new_customer->address = "";
            $new_customer->address2 = "";
            $new_customer->contcat_person = $dt->first_name.' '.$dt->first_namelast_name;
            $new_customer->contcat_number = "";
            $new_customer->mobile_code = "";
            $new_customer->mobile = $dt->contact_number;
            $new_customer->email = $dt->email;
            $new_customer->sales_person = $dt->sales_person;
            $new_customer->vat_number = "";
            $new_customer->company_id = session('logged_session_data.company_id');
            
            $country = SysCountries::select('id','name')->Where('name', 'like', '%'.$dt->country.'%')->first();

            $cname=231;
            if(isset($country)){
                $new_customer->vat_country = $country->id;
                $cname=$country->id;
            }
            else{
                $new_customer->vat_country = 231;
                $cname=231;
            }

            $new_customer->vat_state = "";
            $new_customer->vat_type = "";
            $new_customer->vat_percentage = "";
            $new_customer->status=1;
            $new_customer->created_by = $dt->sales_person_id;
            $results1 = $new_customer->save();
            
            $company_id= $new_customer->id;
            } 

            $ssi = new SysCrmLeads();
            $ssi->date = date('Y-m-d');
            $ssi->lead_name = 'Gitex 2023 - '.$dt->company_name;
            $ssi->cust_id = $company_id;
            $ssi->cust_name = $dt->contact_person;
            $ssi->cust_no = $dt->contact_number;        
            $ssi->company_name = $company_id;
            $ssi->cust_email = $dt->email;
            $ssi->cust_designation = $dt->designation;
            $ssi->address = '';
            $ssi->country = $dt->country;
            $ssi->source = 'Gitex 2023';
            $ssi->source_o = '';
            $ssi->owner = $dt->sales_person_id;
            $ssi->doc = '';            
            $ssi->tags = '';
            $ssi->note = $dt->note;
            $ssi->status = 1;
            $ssi->created_by = $dt->sales_person_id;
            $ssi->company_id = session('logged_session_data.company_id');
            $ssi->save();
            $ssi->toArray();

            DB::table('gitex_leads_2023')->where('id',$dt->id)
            ->update([
                'status' => 0,
            ]);
            
            $accounts = new SysChartofAccounts();
            $accounts->account_code = "";
            $accounts->account_name = $dt->company_name;
            $groups = SysAccountGroupSub2::select('group_id','sub_id')->where('id',4)->first();  // 4 Sundry Debtors
            $accounts->group = $groups->group_id;
            $accounts->subgroup = $groups->sub_id;
            $accounts->subgroup2 = 4; // 4 Sundry Debtors
            $accounts->status = 1;
            $accounts->created_by = Auth::user()->id;
            $results = $accounts->save();
        }
        return "Success";
    } catch (\Throwable $th) {
        return $th;
    }

    }
    public static function Erp_Notify_in($user_id, $message, $received_id, $link)
    {
        try {            
            $notify = new SysNotifications();
            $notify->user_id = $user_id;
            $notify->date = date('Y-m-d');
            $notify->message = $message;
            $notify->received_id = $received_id;
            $notify->link = $link;
            $notify->is_read = 0;
            $notify->active_status = 1;
            $notify->created_by = Auth::user()->id;
            $notify->created_at = date('Y-m-d H:i:s');
            $results = $notify->save();        
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
    public static function Erp_Notify_re_submit($designation_id, $track_id, $deal_id)
    {
        try {
            $user = DB::table('sm_staffs')->select('user_id')->where('designation_id',$designation_id)->get();
            if(count($user)>0){
                foreach($user as $u){
                    SysHelper::Erp_Notify_in($u->user_id,'Deal '.$deal_id.' Resubmited',$u->user_id,'http://erp.venushrms.com/crm-deal-track-approval/'.$track_id.'');
                }
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
    public static function Erp_Notify_track_reject($deal_id, $owner_name, $owner_email, $dept, $reason)
    {
        try {            
            $body = "<br />";
            $body .= "Deal No ".$deal_id." has been Rejected by ".$dept." Department. Please review and Re-Submit.";
            $body .= "<br />";
            if($reason != ""){
                $body .= "Reason : ".$reason;
                $body .= "<br />";
            } 
            $body .= "<a href='http://erp.venushrms.com/crm-deal-track/".$deal_id."/view'> View Detail : ".$deal_id." </a>";
            $body .= "<br />";
            $body .= "<br />";
            SysHelper::notificationMail($owner_name,$body, $owner_email, 'Deal No '.$deal_id.' has been Rejected');
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
    
    public static function notificationMailGitexMail($name, $email, $sales){
    if(Auth::user()->id == 4){
        try {                
            $body = "Thank you for visiting us at GITEX Global!<br />";
            $body .= "We hope you had a great time visiting us and experiencing the latest products and solutions from our vendors.<br />";

            if($sales==23){
            $body .= "For further information and enquiries, you may contact Mr. Shoji.<br /><br />";
            $body .= "<b>Shoji George Thomas</b><br />";
            $body .= "Business Development Manager<br />";
            $body .= "Tel: +971 4 3522433 Ext: 311<br />";
            $body .= "Mobile: +971 52 735 4623<br />";
            $body .= "Email: shoji@sysllc.com<br />";
            }
            else if($sales==26){
            $body .= "For further information and enquiries, you may contact Mr. Sayed Naeem.<br /><br />";
            $body .= "<b>Sayed Naeem</b><br />";
            $body .= "Business Development Manager <br />";
            $body .= "Tel: +971 4 3522433 Ext : 324<br />";
            $body .= "Mobile : +971 52 541 7028<br />";
            $body .= "Email: naeem@sysllc.com<br />";
            }
            else if($sales==22){
            $body .= "For further information and enquiries, you may contact Mr. Sarath.<br /><br />";
            $body .= "<b>Sarath R</b><br />";
            $body .= "Business Development Manager<br />";
            $body .= "Tel: +971 4 3522433 Ext: 331<br />";
            $body .= "Mobile: +971 50 459 8916<br />";
            $body .= "Email: sarath@sysllc.com<br />";
            }
            else if($sales==48){
            $body .= "For further information and enquiries, you may contact Mr. Abdul Raheem.<br /><br />";
            $body .= "<b>Abdul Raheem</b><br />";
            $body .= "Tel: +971 4 3522433<br />";
            $body .= "Mobile: +971 56 408 6464<br />";
            $body .= "Email: raheem@sysllc.com<br />";
            }
            else if($sales==25){
            $body .= "For further information and enquiries, you may contact Mr. Imran Shaikh.<br /><br />";
            $body .= "<b>Imran Shaikh</b><br />";
            $body .= "AVAYA Product Manager<br />";
            $body .= "Tel: +971 4 3522433 Ext: 346<br />";
            $body .= "Mobile: +971 56 408 7373<br />";
            $body .= "Email: imran@sysllc.com<br />";
            }
            else if($sales==50){
            $body .= "For further information and enquiries, you may contact Mr. Faquih Abdul Sattar.<br /><br />";
            $body .= "<b>Faquih Abdul Sattar</b><br />";
            $body .= "Account Manager<br />";
            $body .= "Moons Nature Trading Establishment<br />";
            $body .= "Tel: +966 13 8642671<br />";
            $body .= "Mobile: +966 50 3137366<br />";
            $body .= "Email: abdul@sysllc.com<br />";
            }
            else if($sales==38){
            $body .= "For further information and enquiries, you may contact Mr. Tanzeel Ansari.<br /><br />";
            $body .= "<b>Tanzeel Ansari</b><br />";
            $body .= "Business Development Manager - Riyadh<br />";
            $body .= "Tel: +966 11 210 9668<br />";
            $body .= "Mobile: +966 55 490 9327<br />";
            $body .= "Email: tanzeel@supreme.sa<br />";
            }
            else if($sales==44){
            $body .= "For further information and enquiries, you may contact Mr. Rajiv R.<br />";
            $body .= "Rajiv R<br />";
            $body .= "Director Cybersecurity EMEA<br />";
            $body .= "Tel: +971 4 3522433 Ext : 355<br />";
            $body .= "Mobile : +971 50 480 8952<br />";
            $body .= "Email: rajiv.r@sysllc.com<br />";
            $body .= "Web: http://www.sysllc.com/cybersecurity-solutions/<br />";
            }
            else if($sales==34){
            $body .= "For further information and enquiries, you may contact Mr. Stephen F Mendonsa.<br />";
            $body .= "Stephen F Mendonsa<br />";
            $body .= "Cyber Security Manager EMEA<br />";
            $body .= "Tel: +971 4 3522433   Ext : 318<br />";
            $body .= "Mobile : +971 54 998 2878 / +971 58 883 2633<br />";
            $body .= "Email: stephen.m@sysllc.com<br />";
            $body .= "Web: www.sysllc.com/cybersecurity-solutions/<br />";
            }
            else if($sales==32){
            $body .= "For further information and enquiries, you may contact Mr. Irshaad Aklekar.<br />";
            $body .= "Irshaad Aklekar<br />";
            $body .= "Business Development Manager-ICT<br />";
            $body .= "Tel: +971 52 741 8616 Ext : 333<br />";
            $body .= "Mobile: +971 54 581 6852<br />";
            $body .= "Email: irshaad@sysllc.com<br />";
            }            
            else if($sales==29){
            $body .= "For further information and enquiries, you may contact Mr. Rahul Puliyasseri.<br />";
            $body .= "Rahul Puliyasseri<br />";
            $body .= "Senior Product Specialist<br />";
            $body .= "Tel: +971 4 874 7373<br />";
            $body .= "Mob: +971 56 418 0514<br />";
            $body .= "Email: rahul.p@magnusgulf.com<br />";
            $body .= "Web: www.magnusgulf.com<br />";
            }
            else if($sales==33){
            $body .= "For further information and enquiries, you may contact Mr. Jacob George.<br />";
            $body .= "Jacob George<br />";
            $body .= "Tel: +971 4 352 2433 Ext: 356<br />";
            $body .= "Mobile : +971 50 480 7852<br />";
            $body .= "Email: jacob@sysllc.com <br />";
            $body .= "Web: www.sysllc.com<br />";
            }
            else if($sales==27){
            $body .= "For further information and enquiries, you may contact Ms. Monica.<br />";
            $body .= "Monica<br />";
            $body .= "Tel: +971 4 352 2433 Ext: 317<br />";
            $body .= "Mobile : +971 56 408 7171<br />";
            $body .= "Email: monica@sysllc.com<br />";
            $body .= "Web: www.sysllc.com<br />";
            }
            else if($sales==46){
            $body .= "For further information and enquiries, you may contact Mr. Muhammed Junaid.<br />";
            $body .= "Muhammed Junaid<br />";
            $body .= "Senior Product Specialist<br />";
            $body .= "Tel: +971 4 874 7373<br />";
            $body .= "Mob: +971 56 415 5757<br />";
            $body .= "Email: junaid@magnusgulf.com<br />";
            $body .= "Web: www.magnusgulf.com<br />";
            }

            else {
                $body .= "For further information and enquiries, you may contact Ms. Amrutha.<br /><br />";
                $body .= "<b>Amrutha D K</b><br />";
                $body .= "Marketing Coordinator <br />";
                $body .= "Tel: +971 4 3522433<br />";
                $body .= "Mobile: +971 54 309 2141<br />";
                $body .= "Email: amrutha@sysllc.com<br />";
            }

            $body .= "Web: www.sysllc.com<br />";
            $body .= "Kindly <a href='https://www.sysllc.com/wp-content/uploads/2022/09/syscom-company-profile-10-2022.pdf'>download</a> our company profile here";
            SysHelper::notificationMailGitex($name,$body, $email, 'Thank you for Visiting Syscom at GITEX');
        } catch (\Throwable $th) {
            return $th;
        }
    }
    }

    public static function notificationMailGitex($name, $body, $to, $sub){
        $data =  array(
            'name' => $name,
            'body' => $body,
            'email' => $to,
            'subject' => $sub
        );
        try {            
            Mail::send('emails.mailTempGitex',$data, function ($msg) use ($data){
                $msg->from('marketing@wesyscom.com', 'Syscom');
                $msg->to($data['email'])->subject($data['subject']);
            });
        } catch (\Throwable $th) {
            //return $th;
        }
    }

    public static function notificationMail($name, $body, $to, $sub){
        $data =  array(
            'name' => $name,
            'body' => $body,
            'email' => $to,
            'subject' => $sub
        );
        try {            
            Mail::send('emails.mailTemp',$data, function ($msg) use ($data){
                $msg->from('marketing@wesyscom.com', 'Syscom');
                $msg->to($data['email'])->subject($data['subject']);
            });
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
    
    function notificationMailCc($name, $body, $to, $cc, $sub){
        $data =  array(
            'name' => $name,
            'body' => $body,
            'email' => $to,
            'emailcc' => $cc,
            'subject' => $sub
        );
    
        Mail::send('emails.mailTemp',$data, function ($msg) use ($data){
            $msg->from('noreply@venushrms.com', 'Venus HRMS');    
            $msg->to($data['email'])->cc($data['emailcc'])->subject($data['subject']);
        });
    }
    function notificationMailBcc($name, $body, $to, $bcc, $sub){
        $data =  array(
            'name' => $name,
            'body' => $body,
            'email' => $to,
            'emailbcc' => $bcc,
            'subject' => $sub
        );
    
        Mail::send('emails.mailTemp',$data, function ($msg) use ($data){
            $msg->from('noreply@venushrms.com', 'Venus HRMS');    
            $msg->to($data['email'])->bcc($data['emailbcc'])->subject($data['subject']);
        });
    }
    
    public static function get_stock_total_for_balance_sheet(){
        try {           
                $retData = 0;
                $data = DB::table('sys_item_stock')->select('price_in','qty_out')->get();
                if(count($data)>0){
                    foreach ($data as $dt) {
                        $retData += $dt->price_in * $dt->qty_out;
                    }
                }
                return SysHelper::com_curr_format($retData, 2, '.', '');
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public static function get_stock_for_balance_sheet(){
        try {           
                $retData ="";
                $data = DB::table('sys_item_stock')
                ->join('sm_items','sm_items.id','sys_item_stock.partno')
                ->select('sys_item_stock.description','sys_item_stock.price_in','sys_item_stock.qty_in','sm_items.part_number')->get();
                if(count($data)>0){
                    $retData .= "<tr class='collapse' id='collapsea_sto'><td class='border pl-4'>";
                    foreach ($data as $dt) {
                        $retData .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$dt->part_number." <span class='float-right'>".SysHelper::com_curr_format($dt->price_in * $dt->qty_in, 2, '.', '')."</span><hr class='p-0 m-0' />";
                    }
                    $retData .= "</td><td class='border'>&nbsp;</td></tr>";
                }
                return $retData;
        } catch (\Throwable $th) {
            return $th;
        }
    }
    
    public static function get_customer_total_for_balance_sheet(){
        try {           
                $retData = 0;
                $data = DB::table('sys_item_stock')->select('price_in','qty_in')->get();
                if(count($data)>0){
                    foreach ($data as $dt) {
                        $retData += $dt->price_in * $dt->qty_in;
                    }
                }
                return SysHelper::com_curr_format(0, 2, '.', '');
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public static function get_customer_for_balance_sheet(){
        try {           
                $retData ="";
                $data = DB::table('sys_cust_suppl')->select('name')->where('catid',1)->get();
                if(count($data)>0){
                    $retData .= "<tr class='collapse' id='collapsea_cust'><td class='border pl-4'>";
                    foreach ($data as $dt) {
                        $retData .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$dt->name." <span class='float-right'>".SysHelper::com_curr_format(0, 2, '.', '')."</span><hr class='p-0 m-0' />";
                    }
                    $retData .= "</td><td class='border'>&nbsp;</td></tr>";
                }
                return $retData;
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public static function get_supplier_total_for_balance_sheet(){
        try {           
                $retData = 0;
                $data = DB::table('sys_item_stock')->select('price_in','qty_in')->get();
                if(count($data)>0){
                    foreach ($data as $dt) {
                        $retData += $dt->price_in * $dt->qty_in;
                    }
                }
                return SysHelper::com_curr_format(0, 2, '.', '');
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public static function get_supplier_for_balance_sheet(){
        try {           
                $retData ="";
                $data = DB::table('sys_cust_suppl')->select('name')->where('catid',2)->get();
                if(count($data)>0){
                    $retData .= "<tr class='collapse' id='collapsea_supp'><td class='border pl-4'>";
                    foreach ($data as $dt) {
                        $retData .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$dt->name." <span class='float-right'>".SysHelper::com_curr_format(0, 2, '.', '')."</span><hr class='p-0 m-0' />";
                    }
                    $retData .= "</td><td class='border'>&nbsp;</td></tr>";
                }
                return $retData;
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public static function get_cash_in_hand_for_balance_sheet($from_date,$to_date){
        try {           
                $retData ="";
                $data = DB::table('sys_chartofaccounts')->select('id','account_name')->where('subgroup2',15)->get();
                if(count($data)>0){
                    $retData .= "<tr class='collapse' id='collapsea_cash_in_hand'><td class='border pl-4'>";
                    foreach ($data as $dt) {
                        $retData .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$dt->account_name." <span class='float-right'>".SysHelper::get_account_balance_by_account_id($dt->id,$from_date,$to_date)."</span><hr class='p-0 m-0' />";
                    }
                    $retData .= "</td><td class='border'>&nbsp;</td></tr>";
                }
                return $retData;
        } catch (\Throwable $th) {
            return $th;
        }
    }
    
    public static function get_cash_at_bank_for_balance_sheet($from_date,$to_date){
        try {           
                $retData ="";
                $data = DB::table('sys_chartofaccounts')->select('id','account_name')->where('subgroup2',16)->get();
                if(count($data)>0){
                    $retData .= "<tr class='collapse' id='collapsea_cash_at_bank'><td class='border pl-4'>";
                    foreach ($data as $dt) {
                        $retData .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$dt->account_name." <span class='float-right'>".SysHelper::get_account_balance_by_account_id($dt->id,$from_date,$to_date)."</span><hr class='p-0 m-0' />";
                    }
                    $retData .= "</td><td class='border'>&nbsp;</td></tr>";
                }
                return $retData;
        } catch (\Throwable $th) {
            return $th;
        }
    }
    
    
    public static function get_customer_opening_balance($accounts_id, $from_date, $com_id)
    {
        try {
            $resultopb = DB::table('sys_chartofaccounts_transaction AS cat')
            ->select('cat.account_id',DB::raw('sum(cat.debit_amount)-sum(cat.credit_amount) as opb_amount'))
            ->wherein('cat.account_id', $accounts_id)->where('cat.company_id', $com_id)
            ->wherenotin('transaction_type',['opbinvoice'])
            ->whereRaw("DATE_FORMAT(cat.transaction_date, '%Y-%m-%d') < '" . $from_date . "'")
            ->where('cat.status',1)
            ->groupBy('cat.account_id')
            ->get();
            return $resultopb;
        } catch (\Throwable $th) {
            return $th;
        }
    }
    public static function get_supplier_opening_balance($accounts_id, $from_date, $com_id)
    {
        try {
            $resultopb = DB::table('sys_chartofaccounts_transaction AS cat')
            ->select('cat.account_id',DB::raw('sum(cat.credit_amount)-sum(cat.debit_amount) as opb_amount'))
            ->wherein('cat.account_id', $accounts_id)->where('cat.company_id', $com_id)
            ->wherenotin('transaction_type',['opbinvoice'])
            ->whereRaw("DATE_FORMAT(cat.transaction_date, '%Y-%m-%d') < '" . $from_date . "'")
            ->where('cat.status',1)
            ->groupBy('cat.account_id')
            ->get();
            return $resultopb;
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public static function get_gross_profit_for_balance_sheet($from_date,$to_date){
        try {
            
            $retData =""; $gprofit=0; $gloss=0;
            
            $ie_acc_id = SysChartofAccounts::select('id','account_name')->where('subgroup',11)->where('status',1)->get();
            $indirect_expenses = SysProfitAndLossAccountController::get_indirect_expenses($from_date,$to_date,$ie_acc_id);
            
            $ii_acc_id = SysChartofAccounts::select('id','account_name')->where('subgroup',13)->where('status',1)->get();
            $indirect_income = SysProfitAndLossAccountController::get_indirect_income($from_date,$to_date,$ii_acc_id);

            $opening_stock = SysProfitAndLossAccountController::get_opening_stock($from_date,$to_date);
            $closing_stock = SysProfitAndLossAccountController::get_closing_stock($from_date,$to_date);
            $purchase = SysProfitAndLossAccountController::get_purchase($from_date,$to_date);
            $sales = SysProfitAndLossAccountController::get_sales($from_date,$to_date);
            
            $gp_p=$opening_stock+$purchase+$indirect_income;
            $gp_s=$closing_stock+$sales+$indirect_expenses;
            
            if($gp_p > $gp_s){ $gprofit = SysHelper::com_curr_format($gp_p - $gp_s, 2, '.', ''); }
            if($gp_s > $gp_p){ $gloss = SysHelper::com_curr_format($gp_s - $gp_p, 2, '.', ''); }

            if($gprofit !=0){
                $retData .= "<tr class='collapse' id='collapsel_gross_profit'><td class='border pl-4'>";                        
                $retData .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Gross Profit <span class='float-right'>".$gprofit."</span><hr class='p-0 m-0' />";
                $retData .= "</td><td class='border'>&nbsp;</td></tr>";

                $Data =['profit'=> $gprofit, 'data' => $retData];
            }
            else {
                $Data =['profit'=>$gprofit, 'data' => ''];
            }
            return $Data;

        } catch (\Throwable $th) {
            return $th;
        }
    }

    public static function get_gross_loss_for_balance_sheet($from_date,$to_date){
        try {
            
            $retData =""; $gprofit=0; $gloss=0;
            
            $ie_acc_id = SysChartofAccounts::select('id','account_name')->where('subgroup',11)->where('status',1)->get();
            $indirect_expenses = SysProfitAndLossAccountController::get_indirect_expenses($from_date,$to_date,$ie_acc_id);
            
            $ii_acc_id = SysChartofAccounts::select('id','account_name')->where('subgroup',13)->where('status',1)->get();
            $indirect_income = SysProfitAndLossAccountController::get_indirect_income($from_date,$to_date,$ii_acc_id);

            $opening_stock = SysProfitAndLossAccountController::get_opening_stock($from_date,$to_date);
            $closing_stock = SysProfitAndLossAccountController::get_closing_stock($from_date,$to_date);
            $purchase = SysProfitAndLossAccountController::get_purchase($from_date,$to_date);
            $sales = SysProfitAndLossAccountController::get_sales($from_date,$to_date);
            
            $gp_p=$opening_stock+$purchase+$indirect_income;
            $gp_s=$closing_stock+$sales+$indirect_expenses;
            
            if($gp_p > $gp_s){ $gprofit = SysHelper::com_curr_format($gp_p - $gp_s, 2, '.', ''); }
            if($gp_s > $gp_p){ $gloss = SysHelper::com_curr_format($gp_s - $gp_p, 2, '.', ''); }

            if($gloss !=0){
                $retData .= "<tr class='collapse' id='collapsel_gross_loss'><td class='border pl-4'>";                        
                $retData .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Gross Loss <span class='float-right'>".$gloss."</span><hr class='p-0 m-0' />";
                $retData .= "</td><td class='border'>&nbsp;</td></tr>";
                
                $Data =['loss'=>$gloss, 'data' => $retData];
            }
            else {
                $Data =['loss'=>$gloss, 'data' => ''];
            }
            return $Data;

        } catch (\Throwable $th) {
            return $th;
        }
    }

    public static function get_gross_profit_or_loss($from_date,$to_date){
        try {
            
            $retData =""; $gprofit=0; $gloss=0;
            
            $ie_acc_id = SysChartofAccounts::select('id','account_name')->where('subgroup',11)->where('status',1)->get();
            $indirect_expenses = SysProfitAndLossAccountController::get_indirect_expenses($from_date,$to_date,$ie_acc_id);
            
            $ii_acc_id = SysChartofAccounts::select('id','account_name')->where('subgroup',13)->where('status',1)->get();
            $indirect_income = SysProfitAndLossAccountController::get_indirect_income($from_date,$to_date,$ii_acc_id);

            $opening_stock = SysProfitAndLossAccountController::get_opening_stock($from_date,$to_date);
            $closing_stock = SysProfitAndLossAccountController::get_closing_stock($from_date,$to_date);
            $purchase = SysProfitAndLossAccountController::get_purchase($from_date,$to_date);
            $sales = SysProfitAndLossAccountController::get_sales($from_date,$to_date);
            
            $gp_p=$opening_stock+$purchase+$indirect_income;
            $gp_s=$closing_stock+$sales+$indirect_expenses;
            
            if($gp_p > $gp_s){ $gprofit = SysHelper::com_curr_format($gp_p - $gp_s, 2, '.', ''); }
            if($gp_s > $gp_p){ $gloss = SysHelper::com_curr_format($gp_s - $gp_p, 2, '.', ''); }

            if($gprofit !=0){
                $Data =['type' => 'profit', 'value' => $gprofit];
            }
            else {
                $Data =['type'=> 'loss', 'value' => $gloss];
            }
            return $Data;

        } catch (\Throwable $th) {
            return $th;
        }
    }

    public static function get_share_holder_fund_for_balance_sheet($from_date,$to_date){
        try {
                $retData ="";
                $data = DB::table('sys_chartofaccounts')->select('id','account_name')->where('subgroup',14)->get();
                if(count($data)>0){
                    $retData .= "<tr class='collapse' id='collapsel_partners_capital'><td class='border pl-4'>";
                    foreach ($data as $dt) {
                        $retData .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$dt->account_name." <span class='float-right'>".abs(SysHelper::get_account_balance_by_account_id($dt->id,$from_date,$to_date))."</span><hr class='p-0 m-0' />";
                    }
                    $retData .= "</td><td class='border'>&nbsp;</td></tr>";
                }
                return $retData;
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public static function get_partners_capital_for_balance_sheet($from_date,$to_date){
        try {           
                $retData ="";
                $data = DB::table('sys_chartofaccounts')->select('id','account_name')->where('subgroup2',7)->get();
                if(count($data)>0){
                    $retData .= "<tr class='collapse' id='collapsel_partners_capital'><td class='border pl-4'>";
                    foreach ($data as $dt) {
                        $retData .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$dt->account_name." <span class='float-right'>".abs(SysHelper::get_account_balance_by_account_id($dt->id,$from_date,$to_date))."</span><hr class='p-0 m-0' />";
                    }
                    $retData .= "</td><td class='border'>&nbsp;</td></tr>";
                }
                return $retData;
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public static function get_account_name_in_bank_book($doc_number,$account_id)
    {
        $account = DB::table('sys_journalvoucher_list')->select('sys_chartofaccounts.account_name')
            ->join('sys_chartofaccounts','sys_chartofaccounts.id','sys_journalvoucher_list.account_id')
            ->where('jv_id',$doc_number)->where('account_id','!=',$account_id)->first();
            return $account->account_name;
    }

    public static function get_account_details_for_employee_sub_add($com_id,$account_name)
    {
        try {
            $telephone = [1 => 11596, 2 => 10124, 3 => 11508, 4 => 11534, 5 => 10372, 6 => 11196, 7 => 11545, 8 => 10264, 9 => 11532, 10 => 10158, 11 => 11564, 12 => 11554];
            $airfare = [1 => 11597, 2 => 10125, 3 => 11509, 4 => 11535, 5 => 10371, 6 => 10860, 7 => 11546, 8 => 10263, 9 => 9479, 10 => 10154, 11 => 11565, 12 => 11555];
            $food = [1 => 11598, 2 => 10121, 3 => 11510, 4 => 11536, 5 => 10375, 6 => 10861, 7 => 11547, 8 => 10267, 9 => 10472, 10 => 10155, 11 => 11566, 12 => 11556];
            $salary = [1 => 11599, 2 => 9936, 3 => 11607, 4 => 11537, 5 => 10373, 6 => 10862, 7 => 8661, 8 => 10265, 9 => 9461, 10 => 10156, 11 => 11567, 12 => 11557];
            $gratuity = [1 => 11600, 2 => 10122, 3 => 11511, 4 => 11538, 5 => 10374, 6 => 10863, 7 => 11548, 8 => 10266, 9 => 10473, 10 => 10157, 11 => 11568, 12 => 11558];
            $visa = [1 => 65, 2 => 10088, 3 => 8927, 4 => 11539, 5 => 10376, 6 => 10865, 7 => 11549, 8 => 10268, 9 => 10475, 10 => 10159, 11 => 11569, 12 => 11559];
            $travelling = [1 => 11601, 2 => 10096, 3 => 11512, 4 => 11540, 5 => 11516, 6 => 10911, 7 => 11550, 8 => 10315, 9 => 9482, 10 => 10206, 11 => 11570, 12 => 11560];
            $parking = [1 => 11602, 2 => 10123, 3 => 11513, 4 => 11541, 5 => 11517, 6 => 11199, 7 => 11551, 8 => 11520, 9 => 11529, 10 => 11524, 11 => 11571, 12 => 11561];
            $petrol = [1 => 11603, 2 => 8976, 3 => 11514, 4 => 11542, 5 => 11518, 6 => 11198, 7 => 11552, 8 => 11521, 9 => 11530, 10 => 11525, 11 => 11572, 12 => 11562];
            $vehicle = [1 => 11604, 2 => 10087, 3 => 11515, 4 => 11543, 5 => 11519, 6 => 11200, 7 => 11553, 8 => 11522, 9 => 11531, 10 => 11526, 11 => 11573, 12 => 11563];
            
            if ($account_name == "employee_telephone_expenses") {
                if (array_key_exists($com_id, $telephone)) {
                    $account_id = $telephone[$com_id];
                    $data = DB::table('sys_chartofaccounts')->select('id','group','subgroup','subgroup2',DB::raw('CONCAT("Telephone Expenses") as sub_account_name'))->where('id',$account_id)->first();
                    if(isset($data)){
                        return $data;
                    } else { return 'no_data_found'; }
                } else {
                    return 'no_data_found';
                }
            }

            if($account_name=="employee_airfare_expenses"){
                if (array_key_exists($com_id, $airfare)) {
                    $account_id = $airfare[$com_id];
                    $data = DB::table('sys_chartofaccounts')->select('id','group','subgroup','subgroup2',DB::raw('CONCAT("Airfare Expenses") as sub_account_name'))->where('id',$account_id)->first();
                    if(isset($data)){
                        return $data;
                    } else { return 'no_data_found'; }
                } else {
                    return 'no_data_found';
                }                
            }
            if($account_name=="employee_food_expenses"){
                if (array_key_exists($com_id, $food)) {
                    $account_id = $food[$com_id];
                    $data = DB::table('sys_chartofaccounts')->select('id','group','subgroup','subgroup2',DB::raw('CONCAT("Food Expenses") as sub_account_name'))->where('id',$account_id)->first();
                    if(isset($data)){
                        return $data;
                    } else { return 'no_data_found'; }
                } else {
                    return 'no_data_found';
                }                
            }
            if($account_name=="employee_salary"){
                if (array_key_exists($com_id, $salary)) {
                    $account_id = $salary[$com_id];
                    $data = DB::table('sys_chartofaccounts')->select('id','group','subgroup','subgroup2',DB::raw('CONCAT("Salary") as sub_account_name'))->where('id',$account_id)->first();
                    if(isset($data)){
                        return $data;
                    } else { return 'no_data_found'; }
                } else {
                    return 'no_data_found';
                }                
            }
            if($account_name=="employee_gratuity"){
                if (array_key_exists($com_id, $gratuity)) {
                    $account_id = $gratuity[$com_id];
                    $data = DB::table('sys_chartofaccounts')->select('id','group','subgroup','subgroup2',DB::raw('CONCAT("Gratuity Expenses") as sub_account_name'))->where('id',$account_id)->first();
                    if(isset($data)){
                        return $data;
                    } else { return 'no_data_found'; }
                } else {
                    return 'no_data_found';
                }                
            }
            if($account_name=="employee_visa_expenses"){
                if (array_key_exists($com_id, $visa)) {
                    $account_id = $visa[$com_id];
                    $data = DB::table('sys_chartofaccounts')->select('id','group','subgroup','subgroup2',DB::raw('CONCAT("Visa Expenses") as sub_account_name'))->where('id',$account_id)->first();
                    if(isset($data)){
                        return $data;
                    } else { return 'no_data_found'; }
                } else {
                    return 'no_data_found';
                }
            }
            if($account_name=="employee_travelling_expenses"){
                if (array_key_exists($com_id, $travelling)) {
                    $account_id = $travelling[$com_id];
                    $data = DB::table('sys_chartofaccounts')->select('id','group','subgroup','subgroup2',DB::raw('CONCAT("Travelling Expenses") as sub_account_name'))->where('id',$account_id)->first();
                    if(isset($data)){
                        return $data;
                    } else { return 'no_data_found'; }
                } else {
                    return 'no_data_found';
                }
            }
            if($account_name=="employee_parking_expenses"){
                if (array_key_exists($com_id, $parking)) {
                    $account_id = $parking[$com_id];
                    $data = DB::table('sys_chartofaccounts')->select('id','group','subgroup','subgroup2',DB::raw('CONCAT("Parking Expenses") as sub_account_name'))->where('id',$account_id)->first();
                    if(isset($data)){
                        return $data;
                    } else { return 'no_data_found'; }
                } else {
                    return 'no_data_found';
                }
            }
            if($account_name=="employee_petrol_expenses"){
                if (array_key_exists($com_id, $petrol)) {
                    $account_id = $petrol[$com_id];
                    $data = DB::table('sys_chartofaccounts')->select('id','group','subgroup','subgroup2',DB::raw('CONCAT("Petrol Expenses") as sub_account_name'))->where('id',$account_id)->first();
                    if(isset($data)){
                        return $data;
                    } else { return 'no_data_found'; }
                } else {
                    return 'no_data_found';
                }
            }
            if($account_name=="employee_vehicle_maintenance"){
                if (array_key_exists($com_id, $vehicle)) {
                    $account_id = $vehicle[$com_id];
                    $data = DB::table('sys_chartofaccounts')->select('id','group','subgroup','subgroup2',DB::raw('CONCAT("Vehicle Maintenance") as sub_account_name'))->where('id',$account_id)->first();
                    if(isset($data)){
                        return $data;
                    } else { return 'no_data_found'; }
                } else {
                    return 'no_data_found';
                }
            }
        } catch (\Throwable $th) {
            return $th;
        }
    }
    
    public static function cust_suppl_merge($type, $process, $from_id, $to_id)
    {
        try {
            DB::table('sys_cust_suppl_merge')->insert([
                'type'=> $type,
                'process'=> $process,
                'from_id'=> $from_id,
                'to_id'=> $to_id,
                'status'=> 1,
                'created_by'=> Auth::user()->id,
                'created_at'=> Carbon::now('+04:00'),
            ]);
        } catch (\Throwable $th) {
            //return $th;
        }

    }

    public static function trn_chartof_accounts_transaction($account_id, $transaction_id, $transaction_no, $transaction_date, $transaction_type, $amount_dr, $amount_cr, $narration,$status,$plan=0, $transaction_ref="",$entry_no)
    {
        try {
            $cat = new SysChartofAccountsTransaction();
            $cat->account_id = $account_id;
            $cat->transaction_id = $transaction_id;
            $cat->transaction_no = $transaction_no;
            $cat->transaction_date = $transaction_date;
            $cat->transaction_type = $transaction_type;
            $cat->debit_amount = $amount_dr;
            $cat->credit_amount = $amount_cr;
            $cat->remarks = $narration;
            $cat->status = $status;
            $cat->plan = $plan;
            $cat->created_by = Auth::user()->id;
            $cat->created_at = Carbon::now('+04:00');
            $cat->company_id = session('logged_session_data.company_id');
            $cat->transaction_ref = $transaction_ref;
            $cat->entry_no = $entry_no;
            $cat->save();
        } catch (\Throwable $th) {
            return $th;
        }
    }
    public static function trn_chartof_accounts_transaction_with_main($account_id, $transaction_id, $transaction_no, $transaction_date, $transaction_type, $amount_dr, $amount_cr, $narration,$status,$plan=0, $transaction_ref="",$entry_no,$is_main)
    {
        try {
            $cat = new SysChartofAccountsTransaction();
            $cat->account_id = $account_id;
            $cat->transaction_id = $transaction_id;
            $cat->transaction_no = $transaction_no;
            $cat->transaction_date = $transaction_date;
            $cat->transaction_type = $transaction_type;
            $cat->debit_amount = $amount_dr;
            $cat->credit_amount = $amount_cr;
            $cat->remarks = $narration;
            $cat->status = $status;
            $cat->plan = $plan;
            $cat->created_by = Auth::user()->id;
            $cat->created_at = Carbon::now('+04:00');
            $cat->company_id = session('logged_session_data.company_id');
            $cat->transaction_ref = $transaction_ref;
            $cat->entry_no = $entry_no;
            $cat->is_main_account = $is_main;
            $cat->save();
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public static function trn_chartof_accounts_transaction_with_main_return_ID($account_id, $transaction_id, $transaction_no, $transaction_date, $transaction_type, $amount_dr, $amount_cr, $narration,$status,$plan=0, $transaction_ref="",$entry_no,$is_main)
    {
        try {
            $cat = new SysChartofAccountsTransaction();
            $cat->account_id = $account_id;
            $cat->transaction_id = $transaction_id;
            $cat->transaction_no = $transaction_no;
            $cat->transaction_date = $transaction_date;
            $cat->transaction_type = $transaction_type;
            $cat->debit_amount = $amount_dr;
            $cat->credit_amount = $amount_cr;
            $cat->remarks = $narration;
            $cat->status = $status;
            $cat->plan = $plan;
            $cat->created_by = Auth::user()->id;
            $cat->created_at = Carbon::now('+04:00');
            $cat->company_id = session('logged_session_data.company_id');
            $cat->transaction_ref = $transaction_ref;
            $cat->entry_no = $entry_no;
            $cat->is_main_account = $is_main;
            $cat->save();
            return $cat->id;
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public static function get_account_balance_by_account_id($account_id,$from_date,$to_date)
    {
        try {
            $tot=0;
            $trn = SysChartofAccountsTransaction::select('debit_amount','credit_amount')->where('account_id',$account_id)
            ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') >= '".$from_date."' and DATE_FORMAT(transaction_date, '%Y-%m-%d') <= '".$to_date."'")->get();
            if(count($trn)>0){
                foreach($trn as $dt){                
                    $tot += $dt->debit_amount;
                    $tot -= $dt->credit_amount;
                }
                return SysHelper::com_curr_format($tot, 2, '.', '');
            } else{
                return SysHelper::com_curr_format($tot, 2, '.', '');
            }        
        } catch (\Throwable $th) {
            return $th;
        }
    }
    public static function get_account_balance_by_group2_id($id,$from_date,$to_date)
    {
        try {
            $tot=0;
            $account = SysChartofAccounts::select('id')->where('subgroup2',$id)->get();
            if(count($account)>0){ foreach($account as $acc){ $account_id[]=$acc->id; } }

            $trn = SysChartofAccountsTransaction::select('debit_amount','credit_amount')
            ->wherein('account_id',$account_id)
            ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') >= '".$from_date."' and DATE_FORMAT(transaction_date, '%Y-%m-%d') <= '".$to_date."'")->get();
            if(count($trn)>0){
                foreach($trn as $dt){                
                    $tot += $dt->debit_amount;
                    $tot -= $dt->credit_amount;
                }
                return SysHelper::com_curr_format($tot, 2, '.', '');
            } else{
                return SysHelper::com_curr_format($tot, 2, '.', '');
            }
        } catch (\Throwable $th) {
            return $th;
        }
    }
    public static function get_trial_balance_by_group_id($id,$gid,$from_date,$to_date)
    {   //Assets-1 debit || Liabilities-2 credit || Expenses-3 debit || Incomes-4 credit
        $r = SysHelper::get_data_by_role();
        $company_id = $r[0];
        $com_id = session('logged_session_data.company_id');
        try {
            $tot=0;
            $subgroup2 = SysAccountGroupSub2::wherein('title',['Opening Stock','Closing Stock'])->pluck('id');
            $account = SysChartofAccounts::select('id')->where('subgroup',$id)->whereRaw("find_in_set($com_id,sys_chartofaccounts.company_access)")
            ->wherenotin('subgroup2',$subgroup2)->get();
            if(count($account)>0){ foreach($account as $acc){ $account_id[]=$acc->id; } }
            $trn = SysChartofAccountsTransaction::select('debit_amount','credit_amount')->where('status',1)
            ->wherein('account_id',$account_id)->wherenotin('transaction_type',['openingbalance','opbinvoice'])->where('company_id',$com_id)
            ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') >= '".$from_date."' and DATE_FORMAT(transaction_date, '%Y-%m-%d') <= '".$to_date."'")->get();
            
            /*$purchase_return_account_id = SysHelper::get_purchase_return_account_id();
            $trn3_return = SysChartofAccountsTransaction::wherein('transaction_type',['purchasereturn'])->where('account_id',$purchase_return_account_id)->where('status',1)
            ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') >= '".$from_date."' and DATE_FORMAT(transaction_date, '%Y-%m-%d') <= '".$to_date."'")->sum('credit_amount');
            $tot -= $trn3_return;*/

            if(count($trn)>0){                
                $tot = SysHelper::get_amount_by_group($gid,$trn->sum('debit_amount'),$trn->sum('credit_amount'));
                //  foreach($trn as $dt){
                //      $tot += $dt->debit_amount;
                //      $tot -= $dt->credit_amount;
                // }

                if($gid == 1 || $gid == 2){
                    return SysHelper::com_curr_format(($tot), 2, '.', '');
                } else { return SysHelper::com_curr_format(abs($tot), 2, '.', ''); }

            } else{
                return SysHelper::com_curr_format(0, 2, '.', '');
            }        
        } catch (\Throwable $th) {
            //return $th;
            return SysHelper::com_curr_format(0, 2, '.', '');
        }
    }
    public static function get_trial_balance_opening_by_group_id($id,$gid,$from_date,$to_date)
    {   //Assets-1 debit || Liabilities-2 credit || Expenses-3 debit || Incomes-4 credit                
        $r = SysHelper::get_data_by_role();
        $company_id = $r[0];
        $com_id = session('logged_session_data.company_id');

        if($from_date==""){return '0.00';}
        try {
            $tot=0;
            $subgroup2 = SysAccountGroupSub2::wherein('title',['Opening Stock','Closing Stock'])->pluck('id');
            
            $account = SysChartofAccounts::select('id')->where('subgroup',$id)->whereRaw("find_in_set($com_id,sys_chartofaccounts.company_access)")
            ->wherenotin('subgroup2',$subgroup2)->get();


            $account_id_no = SysChartofAccounts::wherein('account_name',['Opening Stock'])->where('company_id',session('logged_session_data.company_id'))->max('id');

            if(count($account)>0){ foreach($account as $acc){ $account_id[]=$acc->id; } if($id==13){ $account_id[]=$account_id_no; } }

            $trn1 = SysChartofAccountsTransaction::select('debit_amount','credit_amount')
            ->wherein('account_id',$account_id)->wherenotin('transaction_type',['openingbalance','opbinvoice','openingstock'])->where('status',1)->where('company_id',$com_id)
            ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') < '".$from_date."'")->get();

            /*$purchase_return_account_id = SysHelper::get_purchase_return_account_id();
            $trn3_return = SysChartofAccountsTransaction::wherein('transaction_type',['purchasereturn'])->where('account_id',$purchase_return_account_id)->where('status',1)
            ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') < '".$from_date."'")->sum('credit_amount');
            $tot -= $trn3_return;*/

            $trn2 = SysChartofAccountsTransaction::select('debit_amount','credit_amount')->where('status',1)
            ->wherein('account_id',$account_id)->where('transaction_type','openingbalance')->where('company_id',$com_id)->get();

            $trn = array_merge(array_merge($trn1->toArray(), $trn2->toArray()));

            if(count($trn)>0){

                //$tot = SysHelper::get_amount_by_group($gid,$trn->sum('debit_amount'),$trn->sum('credit_amount'));

                foreach($trn as $dt){
                    $tot += SysHelper::get_amount_by_group($gid,$dt["debit_amount"],$dt["credit_amount"]);
                    //$tot += $dt["debit_amount"];
                    //$tot -= $dt["credit_amount"];
                }
                return SysHelper::com_curr_format($tot, 2, '.', '');
            } else{
                return SysHelper::com_curr_format(0, 2, '.', '');
            }        
        } catch (\Throwable $th) {
            //return $th;
            return SysHelper::com_curr_format(0, 2, '.', '');
        }
    }


    public static function get_amount_by_group($gid,$debit_amount,$credit_amount){
        if($gid == 1 || $gid == 3){
            return $debit_amount-$credit_amount;
        }
        if($gid == 2 || $gid == 4 || $gid == 5){
            return $credit_amount-$debit_amount;
        }

    }
    

    public static function get_trial_balance_by_group2_id($id,$gid,$from_date,$to_date)
    {   //Assets-1 debit || Liabilities-2 credit || Expenses-3 debit || Incomes-4 credit
        $r = SysHelper::get_data_by_role();
        $company_id = $r[0];
        $com_id = session('logged_session_data.company_id');
        try {
            $tot=0;
            $subgroup2 = SysAccountGroupSub2::wherein('title',['Opening Stock','Closing Stock'])->pluck('id');
            $account = SysChartofAccounts::select('id')->where('subgroup2',$id)->whereRaw("find_in_set($com_id,sys_chartofaccounts.company_access)")
            ->wherenotin('subgroup2',$subgroup2)->get();
            if(count($account)>0){ foreach($account as $acc){ $account_id[]=$acc->id; } }
            
            $trn = SysChartofAccountsTransaction::select('debit_amount','credit_amount')->where('status',1)
            ->wherein('account_id',$account_id)->wherenotin('transaction_type',['openingbalance','opbinvoice'])->where('company_id',$com_id)
            ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') >= '".$from_date."' and DATE_FORMAT(transaction_date, '%Y-%m-%d') <= '".$to_date."'")->get();
            
            /*$purchase_return_account_id = SysHelper::get_purchase_return_account_id();
            $trn3_return = SysChartofAccountsTransaction::wherein('transaction_type',['purchasereturn'])->where('account_id',$purchase_return_account_id)->where('status',1)
            ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') >= '".$from_date."' and DATE_FORMAT(transaction_date, '%Y-%m-%d') <= '".$to_date."'")->sum('credit_amount');
            $tot -= $trn3_return;*/

            if(count($trn)>0){
                $tot = SysHelper::get_amount_by_group($gid,$trn->sum('debit_amount'),$trn->sum('credit_amount'));
                // foreach($trn as $dt){                
                //     $tot += $dt->debit_amount;
                //     $tot -= $dt->credit_amount;
                // }
                if($gid ==1 || $gid ==2){
                    return SysHelper::com_curr_format(($tot), 2, '.', ''); 
                } else {return SysHelper::com_curr_format(abs($tot), 2, '.', ''); }

            } else{
                return SysHelper::com_curr_format(0, 2, '.', '');
            }
        } catch (\Throwable $th) {
            //return $th;
            return SysHelper::com_curr_format(0, 2, '.', '');
        }
    }
    public static function get_trial_balance_opening_by_group2_id($id,$gid,$from_date,$to_date)
    {   //Assets-1 debit || Liabilities-2 credit || Expenses-3 debit || Incomes-4 credit
        $r = SysHelper::get_data_by_role();
        $company_id = $r[0];
        $com_id = session('logged_session_data.company_id');
        
        try {
            $tot=0;
            $subgroup2 = SysAccountGroupSub2::wherein('title',['Opening Stock','Closing Stock'])->pluck('id');
            
            if($id==24 && $gid==3){
                $account = SysChartofAccounts::select('id')->wherein('account_name',['Opening Stock'])->where('company_id',session('logged_session_data.company_id'))->get();
            } else{
                $account = SysChartofAccounts::select('id')->where('subgroup2',$id)
                //->wherein('sys_chartofaccounts.company_id',$company_id)
                ->whereRaw("find_in_set($com_id,sys_chartofaccounts.company_access)")
                ->wherenotin('subgroup2',$subgroup2)->get();
            }

            if(count($account)>0){ foreach($account as $acc){ $account_id[]=$acc->id; } }
            
            //old start
            //$trn1 = SysChartofAccountsTransaction::select('debit_amount','credit_amount')->where('status',1)
            //->wherein('account_id',$account_id)->wherenotin('transaction_type',['openingbalance','opbinvoice'])
            //->where('company_id',$com_id)
            //->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') < '".$from_date."'")->get();
            //return $trn1;
            
            /*$purchase_return_account_id = SysHelper::get_purchase_return_account_id();
            $trn3_return = SysChartofAccountsTransaction::wherein('transaction_type',['purchasereturn'])->where('account_id',$purchase_return_account_id)->where('status',1)
            ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') < '".$from_date."'")->sum('credit_amount');
            $tot -= $trn3_return;*/
            
            //$trn2 = SysChartofAccountsTransaction::select('debit_amount','credit_amount')->where('status',1)
            //->where('company_id',$com_id)
            //->wherein('account_id',$account_id)->wherein('transaction_type',['openingbalance','opbinvoice','openingstock'])->get();
            //return $trn2;
            //old end


            //new start

            $trn = SysChartofAccountsTransaction::select('debit_amount','credit_amount')->wherein('account_id',$account_id)->wherenotin('transaction_type',['openingbalance','opbinvoice'])
            ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') >= '".$from_date."' and DATE_FORMAT(transaction_date, '%Y-%m-%d') <= '".$to_date."'")->where('status',1)->where('company_id',$com_id)->get();
            
            $trn_opn1 = SysChartofAccountsTransaction::select('debit_amount','credit_amount')->wherein('account_id',$account_id)->wherenotin('transaction_type',['openingbalance','opbinvoice','openingstock'])
            ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') < '".$from_date."'")->where('status',1)->where('company_id',$com_id)->get();
            
            $trn_opn2 = SysChartofAccountsTransaction::select('debit_amount','credit_amount')->wherein('account_id',$account_id)->where('transaction_type','openingbalance')->where('status',1)->where('company_id',$com_id)->get();
            
            $trn_opn = array_merge(array_merge($trn_opn1->toArray(), $trn_opn2->toArray()));

            //new end
            /*-------------
            $trn = SysChartofAccountsTransaction::select('debit_amount','credit_amount')->wherein('account_id',$account_id_list)->wherenotin('transaction_type',['openingbalance','opbinvoice'])
            ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') >= '".$from_date."' and DATE_FORMAT(transaction_date, '%Y-%m-%d') <= '".$to_date."'")->where('status',1)->where('company_id',$com_id)->get();
            
            $trn_opn1 = SysChartofAccountsTransaction::select('debit_amount','credit_amount')->wherein('account_id',$account_id_list)->wherenotin('transaction_type',['openingbalance','opbinvoice','openingstock'])
            ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') < '".$from_date."'")->where('status',1)->where('company_id',$com_id)->get();
            
            $trn_opn2 = SysChartofAccountsTransaction::select('debit_amount','credit_amount')->wherein('account_id',$account_id_list)->where('transaction_type','openingbalance')->where('status',1)->where('company_id',$com_id)->get();
            
            $trn_opn = array_merge(array_merge($trn_opn1->toArray(), $trn_opn2->toArray()));
            --------------------*/


            //commenred and below changed $trn to $trn2   = if(count($trn2)>0){
            //$trn = array_merge(array_merge($trn1->toArray(), $trn2->toArray()));
            
            if(count($trn_opn)>0){
                
                //$tot = SysHelper::get_amount_by_group($gid,$trn->sum('debit_amount'),$trn->sum('credit_amount'));

                foreach($trn_opn as $dt){
                    $tot += SysHelper::get_amount_by_group($gid,$dt["debit_amount"],$dt["credit_amount"]);
                   //$tot += $dt["debit_amount"];
                   //$tot -= $dt["credit_amount"];
                }
                return SysHelper::com_curr_format($tot, 2, '.', '');
            } else{
                return SysHelper::com_curr_format(0, 2, '.', '');
            }        
        } catch (\Throwable $th) {
            //return $th;
            return SysHelper::com_curr_format(0, 2, '.', '');
        }
    }

    public static function get_trial_balance_items($gid,$from_date,$to_date,$sub2,$sub3)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $com_id = session('logged_session_data.company_id');
            $retData ="";
            $subgroup2 = SysAccountGroupSub2::wherein('title',['Opening Stock','Closing Stock'])->pluck('id');
            
            if($gid==24){
                $data = DB::table('sys_chartofaccounts')->select('id','account_name','group','subgroup2','status')->wherein('account_name',['Opening Stock'])
                ->where('company_id',session('logged_session_data.company_id'))
                ->where('main_account_id',0)->orderby('account_name','asc')->get();
            } else {

                if($gid == 7 || $gid == 19){
                $data = DB::table('sys_chartofaccounts')->select('id','account_name','group','subgroup2','status')->where('subgroup2',$gid)
                //->whereRaw("find_in_set($com_id,sys_chartofaccounts.company_access)")
                ->wherenotin('subgroup2',$subgroup2)->where('main_account_id',0)->orderby('account_name','asc')->get();
                } else {
                $data = DB::table('sys_chartofaccounts')->select('id','account_name','group','subgroup2','status')->where('subgroup2',$gid)
                ->whereRaw("find_in_set($com_id,sys_chartofaccounts.company_access)")
                ->wherenotin('subgroup2',$subgroup2)->where('main_account_id',0)->orderby('account_name','asc')->get();
                }
            }

            if(count($data)>0){
                foreach ($data as $dt) {

                    // if($dt->id==10)
                    // {
                    //     $dat = SysHelper::get_gross_profit_or_loss($from_date,$to_date);
                    //     if($dat['type']=="profit"){
                    //         $retData .= "<tr class='collapse' id='collapse_sub_".$gid."'><td class='border pl-4'>".$dt->account_name."</td><td class='pr-2 border text-right'>".$dat['value']."</td><td class='pr-2 border text-right'>0.00</td><td class='pr-2 border text-right'>".$dat['value']."</td><td class='pr-2 border text-right'>0.00</td><td class='pr-2 border text-right'>".SysHelper::com_curr_format($dat['value'], 2, '.', '')."</td><td class='pr-2 border text-right'>0.00</td></tr>";
                    //     }
                    //     else {
                    //         $retData .= "<tr class='collapse' id='collapse_sub_".$gid."'><td class='border pl-4'>".$dt->account_name."</td><td class='pr-2 border text-right'>0.00</td><td class='pr-2 border text-right'>".$dat['value']."</td><td class='pr-2 border text-right'>0.00</td><td class='pr-2 border text-right'>".$dat['value']."</td><td class='pr-2 border text-right'>0.00</td><td class='pr-2 border text-right'>".SysHelper::com_curr_format($dat['value'], 2, '.', '')."</td></tr>";
                    //     }
                    // }

                    
                    $purchase_return_account_id = SysHelper::get_purchase_return_account_id();

                    $sub_acc_det = SysHelper::get_trial_balance_items_sub($dt->id,$from_date,$to_date,$sub3);
                    //$sub2 = "1";

                    $total=SysHelper::get_account_balance_opening_by_account_id($dt->id,$from_date,$to_date,$dt->group);

                    if(($total[0] != "0.00" || $total[1] != "0.00") || $sub_acc_det != ""){
                        if($dt->group == 1 || $dt->group == 3){ // 1 Assets, 3 Expenses
                            if($dt->id == $purchase_return_account_id){
                                $sum=$total[0] + $total[1];
                                $retData .= "<tr class='collapse ".$sub2."' id='collapse_sub_".$gid."'><td class='border pl-4 text-primary ".($dt->status != 1 ? "bg-gray" : "")."'><a class='text-primary ".($dt->status != 1 ? "bg-gray" : "")."' data-bs-toggle='collapse' href='#collapse_sub2_".$dt->id."'>".$dt->account_name."</a> <a href=".url('get-url-generalledger'."/".$dt->id).">Ledger</a></td><td class=' border text-end text-primary ".($dt->status != 1 ? "bg-gray" : "")."'>0.00</td><td class=' border text-end text-primary ".($dt->status != 1 ? "bg-gray" : "")."'>".SysHelper::minus_format(SysHelper::com_curr_format($total[1], 2, '.', ','))."</td><td class=' border text-end text-primary ".($dt->status != 1 ? "bg-gray" : "")."'>0.00</td><td class=' border text-end text-primary ".($dt->status != 1 ? "bg-gray" : "")."'>".SysHelper::minus_format(SysHelper::com_curr_format($total[0], 2, '.', ','))."</td><td class=' border text-end text-primary ".($dt->status != 1 ? "bg-gray" : "")."'>0.00</td><td class=' border text-end text-primary ".($dt->status != 1 ? "bg-gray" : "")."'>".SysHelper::minus_format(SysHelper::com_curr_format($sum, 2, '.', ','))."</td></tr>";
                                //$retData .= $sub_acc_det;
                            } else {                                
                                $sum=$total[0] + $total[1];
                                $t=0; $c=0; $s=0;
                                if($dt->group==1){
                                    $t=SysHelper::minus_format(SysHelper::com_curr_format($total[1], 2, '.', ','));
                                    $c=SysHelper::minus_format(SysHelper::com_curr_format($total[0], 2, '.', ','));
                                    $s=SysHelper::minus_format(SysHelper::com_curr_format($sum, 2, '.', ','));
                                } else {
                                    $t=SysHelper::minus_format(SysHelper::com_curr_format($total[1], 2, '.', ','));
                                    $c=SysHelper::minus_format(SysHelper::com_curr_format($total[0], 2, '.', ','));
                                    $s=SysHelper::minus_format(SysHelper::com_curr_format($sum, 2, '.', ','));
                                }
                            $retData .= "<tr class='collapse ".$sub2."' id='collapse_sub_".$gid."'><td class='border pl-4 text-primary ".($dt->status != 1 ? "bg-gray" : "")."'><a class='text-primary ".($dt->status != 1 ? "bg-gray" : "")."' data-bs-toggle='collapse' href='#collapse_sub2_".$dt->id."'>".$dt->account_name."</a> <a href=".url('get-url-generalledger'."/".$dt->id).">Ledger</a></td><td class=' border text-end text-primary ".($dt->status != 1 ? "bg-gray" : "")."'>".$t."</td><td class=' border text-end text-primary ".($dt->status != 1 ? "bg-gray" : "")."'>0.00</td><td class=' border text-end text-primary ".($dt->status != 1 ? "bg-gray" : "")."'>".$c."</td><td class=' border text-end text-primary ".($dt->status != 1 ? "bg-gray" : "")."'>0.00</td><td class=' border text-end text-primary ".($dt->status != 1 ? "bg-gray" : "")."'>".$s."</td><td class=' border text-end text-primary ".($dt->status != 1 ? "bg-gray" : "")."'>0.00</td></tr>";
                            $retData .= $sub_acc_det;
                            }
                        }
                        if($dt->group == 2 || $dt->group == 4 || $dt->group == 5){ // 2 Liabilities, 4 Incomes, 5 Equity
                            $sum=$total[0] + $total[1];
                            $retData .= "<tr class='collapse ".$sub2."' id='collapse_sub_".$gid."'><td class='border pl-4 text-primary ".($dt->status != 1 ? "bg-gray" : "")."'><a class='text-primary ".($dt->status != 1 ? "bg-gray" : "")."' data-bs-toggle='collapse' href='#collapse_sub2_".$dt->id."'>".$dt->account_name."</a> <a href=".url('get-url-generalledger'."/".$dt->id).">Ledger</a></td><td class=' border text-end text-primary ".($dt->status != 1 ? "bg-gray" : "")."'>0.00</td><td class=' border text-end text-primary ".($dt->status != 1 ? "bg-gray" : "")."'>".SysHelper::minus_format(SysHelper::com_curr_format($total[1], 2, '.', ','))."</td><td class=' border text-end text-primary ".($dt->status != 1 ? "bg-gray" : "")."'>0.00</td><td class=' border text-end text-primary ".($dt->status != 1 ? "bg-gray" : "")."'>".SysHelper::minus_format(SysHelper::com_curr_format($total[0], 2, '.', ','))."</td><td class=' border text-end text-primary ".($dt->status != 1 ? "bg-gray" : "")."'>0.00</td><td class=' border text-end text-primary ".($dt->status != 1 ? "bg-gray" : "")."'>".SysHelper::minus_format(SysHelper::com_curr_format(($sum), 2, '.', ','))."</td></tr>";
                            //$retData .= $sub_acc_det;
                        }
                    }

                }
            }
            return $retData;
        } catch (\Throwable $th) {
            return $th;
        }
    }
    public static function get_trial_balance_items_sub($mid,$from_date,$to_date,$sub3)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $com_id = session('logged_session_data.company_id');
            $retData ="";
            $subgroup2 = SysAccountGroupSub2::wherein('title',['Opening Stock','Closing Stock'])->pluck('id');
            
                $data = DB::table('sys_chartofaccounts')->select('id','account_name','group','subgroup2','status')
                //->whereRaw("find_in_set($com_id,sys_chartofaccounts.company_access)")
                ->wherenotin('subgroup2',$subgroup2)->where('main_account_id',$mid)->orderby('account_name','asc')->get();

            if(count($data)>0){
                foreach ($data as $dt) {

                    // if($dt->id==10)
                    // {
                    //     $dat = SysHelper::get_gross_profit_or_loss($from_date,$to_date);
                    //     if($dat['type']=="profit"){
                    //         $retData .= "<tr class='collapse' id='collapse_sub_".$gid."'><td class='border pl-4'>".$dt->account_name."</td><td class='pr-2 border text-right'>".$dat['value']."</td><td class='pr-2 border text-right'>0.00</td><td class='pr-2 border text-right'>".$dat['value']."</td><td class='pr-2 border text-right'>0.00</td><td class='pr-2 border text-right'>".SysHelper::com_curr_format($dat['value'], 2, '.', '')."</td><td class='pr-2 border text-right'>0.00</td></tr>";
                    //     }
                    //     else {
                    //         $retData .= "<tr class='collapse' id='collapse_sub_".$gid."'><td class='border pl-4'>".$dt->account_name."</td><td class='pr-2 border text-right'>0.00</td><td class='pr-2 border text-right'>".$dat['value']."</td><td class='pr-2 border text-right'>0.00</td><td class='pr-2 border text-right'>".$dat['value']."</td><td class='pr-2 border text-right'>0.00</td><td class='pr-2 border text-right'>".SysHelper::com_curr_format($dat['value'], 2, '.', '')."</td></tr>";
                    //     }
                    // }

                    
                    $purchase_return_account_id = SysHelper::get_purchase_return_account_id();
                    

                    $total=SysHelper::get_account_balance_opening_by_account_id($dt->id,$from_date,$to_date,$dt->group);
                    
                        if($dt->group == 1 || $dt->group == 3){ // 1 Assets, 3 Expenses
                            if($dt->id == $purchase_return_account_id){
                                $sum=$total[0] + $total[1];
                                $retData .= "<tr class='collapse ".$sub3."' id='collapse_sub2_".$mid."'><td class='border pl-6 text-info ".($dt->status != 1 ? "bg-gray" : "")."'>".$dt->account_name." <a href=".url('get-url-generalledger'."/".$dt->id).">Ledger</a></td><td class=' border text-end text-info ".($dt->status != 1 ? "bg-gray" : "")."'>0.00</td><td class=' border text-end text-info ".($dt->status != 1 ? "bg-gray" : "")."'>".SysHelper::minus_format(SysHelper::com_curr_format($total[1], 2, '.', ','))."</td><td class=' border text-end text-info ".($dt->status != 1 ? "bg-gray" : "")."'>0.00</td><td class=' border text-end text-info ".($dt->status != 1 ? "bg-gray" : "")."'>".SysHelper::minus_format(SysHelper::com_curr_format($total[0], 2, '.', ','))."</td><td class=' border text-end text-info ".($dt->status != 1 ? "bg-gray" : "")."'>0.00</td><td class=' border text-end text-info ".($dt->status != 1 ? "bg-gray" : "")."'>".SysHelper::minus_format(SysHelper::com_curr_format($sum, 2, '.', ','))."</td></tr>";
                            } else {                                
                                $sum=$total[0] + $total[1];
                                $t=0; $c=0; $s=0;
                                if($dt->group==1){
                                    $t=SysHelper::minus_format(SysHelper::com_curr_format($total[1], 2, '.', ','));
                                    $c=SysHelper::minus_format(SysHelper::com_curr_format($total[0], 2, '.', ','));
                                    $s=SysHelper::minus_format(SysHelper::com_curr_format($sum, 2, '.', ','));
                                } else {
                                    $t=SysHelper::minus_format(SysHelper::com_curr_format($total[1], 2, '.', ','));
                                    $c=SysHelper::minus_format(SysHelper::com_curr_format($total[0], 2, '.', ','));
                                    $s=SysHelper::minus_format(SysHelper::com_curr_format($sum, 2, '.', ','));
                                }
                            $retData .= "<tr class='collapse ".$sub3."' id='collapse_sub2_".$mid."'><td class='border pl-6 text-info ".($dt->status != 1 ? "bg-gray" : "")."'>".$dt->account_name." <a href=".url('get-url-generalledger'."/".$dt->id).">Ledger</a></td><td class=' border text-end text-info ".($dt->status != 1 ? "bg-gray" : "")."'>".$t."</td><td class=' border text-end text-info ".($dt->status != 1 ? "bg-gray" : "")."'>0.00</td><td class=' border text-end text-info ".($dt->status != 1 ? "bg-gray" : "")."'>".$c."</td><td class=' border text-end text-info ".($dt->status != 1 ? "bg-gray" : "")."'>0.00</td><td class=' border text-end text-info ".($dt->status != 1 ? "bg-gray" : "")."'>".$s."</td><td class=' border text-end text-info ".($dt->status != 1 ? "bg-gray" : "")."'>0.00</td></tr>";
                            }
                        }
                        if($dt->group == 2 || $dt->group == 4 || $dt->group == 5){ // 2 Liabilities, 4 Incomes, 5 Equity
                            $sum=$total[0] + $total[1];
                            $retData .= "<tr class='collapse ".$sub3."' id='collapse_sub2_".$mid."'><td class='border pl-6 text-info ".($dt->status != 1 ? "bg-gray" : "")."'>".$dt->account_name." <a href=".url('get-url-generalledger'."/".$dt->id).">Ledger</a></td><td class=' border text-end text-info ".($dt->status != 1 ? "bg-gray" : "")."'>0.00</td><td class=' border text-end text-info ".($dt->status != 1 ? "bg-gray" : "")."'>".SysHelper::minus_format(SysHelper::com_curr_format($total[1], 2, '.', ','))."</td><td class=' border text-end text-info ".($dt->status != 1 ? "bg-gray" : "")."'>0.00</td><td class=' border text-end text-info ".($dt->status != 1 ? "bg-gray" : "")."'>".SysHelper::minus_format(SysHelper::com_curr_format($total[0], 2, '.', ','))."</td><td class=' border text-end text-info ".($dt->status != 1 ? "bg-gray" : "")."'>0.00</td><td class=' border text-end text-info ".($dt->status != 1 ? "bg-gray" : "")."'>".SysHelper::minus_format(SysHelper::com_curr_format(($sum), 2, '.', ','))."</td></tr>";
                        }

                }
            }
            return $retData;
        } catch (\Throwable $th) {
            return $th;
        }
    }
    public static function get_account_balance_opening_by_account_id($account_id,$from_date,$to_date,$group)
    {
        try {
            $com_id = session('logged_session_data.company_id');
            $tot=0;
            $tot_opn=0;

$account_id_list[] = $account_id;
$sub_acc = SysChartofAccounts::where('main_account_id', $account_id)->pluck('id')->toArray();
$account_id_list = array_merge($account_id_list, $sub_acc);


            $trn = SysChartofAccountsTransaction::select('debit_amount','credit_amount')->wherein('account_id',$account_id_list)->wherenotin('transaction_type',['openingbalance','opbinvoice'])
            ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') >= '".$from_date."' and DATE_FORMAT(transaction_date, '%Y-%m-%d') <= '".$to_date."'")->where('status',1)->where('company_id',$com_id)->get();
            
            $trn_opn1 = SysChartofAccountsTransaction::select('debit_amount','credit_amount')->wherein('account_id',$account_id_list)->wherenotin('transaction_type',['openingbalance','opbinvoice','openingstock'])
            ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') < '".$from_date."'")->where('status',1)->where('company_id',$com_id)->get();
            
            $trn_opn2 = SysChartofAccountsTransaction::select('debit_amount','credit_amount')->wherein('account_id',$account_id_list)->where('transaction_type','openingbalance')->where('status',1)->where('company_id',$com_id)->get();
            
            $trn_opn = array_merge(array_merge($trn_opn1->toArray(), $trn_opn2->toArray()));

            //$trn_opn = SysChartofAccountsTransaction::where('account_id',$account_id)->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') < '".$from_date."'")->get();
            $gid = SysChartofAccounts::select('group')->where('id',$account_id)->first();

            if(count($trn)>0){
                $tot = SysHelper::get_amount_by_group($gid->group,$trn->sum('debit_amount'),$trn->sum('credit_amount'));
                // foreach($trn as $dt){                
                //     $tot += $dt->debit_amount;
                //     $tot -= $dt->credit_amount;
                // }
            }
            if(count($trn_opn)>0){
                if($group ==1 || $group == 3){
                    foreach($trn_opn as $dt){                
                        $tot_opn += $dt["debit_amount"];
                        $tot_opn -= $dt["credit_amount"];
                    }
                }
                if($group ==2 || $group == 4 || $group == 5){
                    foreach($trn_opn as $dt){                
                        $tot_opn -= $dt["debit_amount"];
                        $tot_opn += $dt["credit_amount"];
                    }                   
                }
            }
            return [SysHelper::com_curr_format(($tot), 2, '.', ''),SysHelper::com_curr_format(($tot_opn), 2, '.', '')];
        } catch (\Throwable $th) {
            return $th;
        }
    }

//receivable outatsnding start
    /**
     * Same total as receivableoutstanding.blade.php footer "Amount" (grand_debit_amount) for invoice rows.
     */
    protected static function sumReceivableOutstandingInvoiceAmountTotal($accountId, $companyId, $tillDate = null)
    {
        $till = $tillDate ? (self::normalizeToYmd($tillDate) ?: $tillDate) : date('Y-m-d');

        $rows = DB::table('sys_chartofaccounts_transaction')
            ->select(
                'transaction_no',
                DB::raw('SUM(debit_amount) as debit_amount'),
                DB::raw('SUM(credit_amount) as credit_amount')
            )
            ->where('account_id', $accountId)
            ->where('company_id', $companyId)
            ->where('status', 1)
            ->whereIn('transaction_type', ['salesinvoice', 'salesreturn', 'opbinvoice', 'openingbalance111'])
            ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') <= ?", [$till])
            ->groupBy('transaction_date', 'transaction_id', 'transaction_no')
            ->get();

        if ($rows->isEmpty()) {
            return 0.0;
        }

        $trnNos = $rows->pluck('transaction_no')->unique()->values();

        $srnPaid = DB::table('sys_sales_return_adjestment')
            ->select('srn_no', DB::raw('SUM(paid_amount) as paid_amount'))
            ->whereIn('srn_no', $trnNos)
            ->groupBy('srn_no')
            ->pluck('paid_amount', 'srn_no');

        $receiptPaid = DB::table('sys_receipt as r')
            ->join('sys_receipt_adjustments as ra', 'ra.bi_doc_number', '=', 'r.doc_number')
            ->where('ra.account_id', $accountId)
            ->whereIn('ra.bi_doc_no', $trnNos)
            ->where('r.status', 1)
            ->select('ra.bi_doc_no', DB::raw('SUM(ra.bi_amount) as bi_amount'))
            ->groupBy('ra.bi_doc_no')
            ->pluck('bi_amount', 'bi_doc_no');

        $jvReceiptPaid = DB::table('sys_journalvoucher as j')
            ->join('sys_receipt_adjustments as ra', 'ra.bi_doc_number', '=', 'j.doc_number')
            ->where('ra.account_id', $accountId)
            ->whereIn('ra.bi_doc_no', $trnNos)
            ->where('j.status', 1)
            ->select('ra.bi_doc_no', DB::raw('SUM(ra.bi_amount) as bi_amount'))
            ->groupBy('ra.bi_doc_no')
            ->pluck('bi_amount', 'bi_doc_no');

        $jvPaymentPaid = DB::table('sys_journalvoucher as j')
            ->join('sys_payment_adjustments as pa', 'pa.bi_doc_number', '=', 'j.doc_number')
            ->where('pa.account_id', $accountId)
            ->whereIn('pa.bi_doc_no', $trnNos)
            ->where('j.status', 1)
            ->select('pa.bi_doc_no', DB::raw('SUM(pa.bi_amount) as bi_amount'))
            ->groupBy('pa.bi_doc_no')
            ->pluck('bi_amount', 'bi_doc_no');

        $returnPaid = DB::table('sys_sales_return as r')
            ->join('sys_sales_return_adjestment as ra', 'ra.srn_no', '=', 'r.doc_number')
            ->where('r.customer', $accountId)
            ->whereIn('ra.siv_no', $trnNos)
            ->where('r.status', 1)
            ->select('ra.siv_no', DB::raw('SUM(ra.paid_amount) as paid_amount'))
            ->groupBy('ra.siv_no')
            ->pluck('paid_amount', 'siv_no');

        $opbReceiptPaid = DB::table('sys_receipt_adjustments as ra')
            ->where('ra.transaction_type', 'openingbalance')
            ->where('ra.company_id', $companyId)
            ->where('ra.status', 1)
            ->where('ra.account_id', $accountId)
            ->whereIn('ra.bi_doc_no', $trnNos)
            ->select('ra.bi_doc_no', DB::raw('SUM(ra.bi_amount) as bi_amount'))
            ->groupBy('ra.bi_doc_no')
            ->pluck('bi_amount', 'bi_doc_no');

        $total = 0.0;
        foreach ($rows as $dt) {
            $paid = (float) ($srnPaid[$dt->transaction_no] ?? 0)
                + (float) ($receiptPaid[$dt->transaction_no] ?? 0)
                + (float) ($jvReceiptPaid[$dt->transaction_no] ?? 0)
                + (float) ($opbReceiptPaid[$dt->transaction_no] ?? 0)
                + (float) ($dt->credit_amount ?? 0)
                - (float) ($jvPaymentPaid[$dt->transaction_no] ?? 0)
                + (float) ($returnPaid[$dt->transaction_no] ?? 0);

            if ((float) $dt->debit_amount != $paid) {
                $total += (float) $dt->debit_amount;
            }
            if ((float) $dt->credit_amount > 0) {
                $total -= (float) $dt->credit_amount;
            }
        }

        return $total;
    }

    /**
     * Per-customer totals aligned with receivableoutstanding.blade.php (header #sum_{id} and gtot1–gtot4 columns).
     */
    public static function getReceivableOutstandingCustomerTotals(
        $accountId,
        $companyId,
        $tillDate = null,
        $rows = null,
        $unadjustedList = null,
        $unadjustedJvToJv = null,
        $paymentTermsMap = null,
        $salesInvoiceMap = null,
        $opbinvoiceMap = null,
        $receivableFinanceRate = 0,
        $adjustedPdcList = null
    ) {
        $till = $tillDate ? (self::normalizeToYmd($tillDate) ?: $tillDate) : date('Y-m-d');

        if ($rows === null) {
            $rows = DB::table('sys_chartofaccounts_transaction')
                ->select(
                    'transaction_no',
                    'transaction_type',
                    'transaction_date',
                    DB::raw('SUM(debit_amount) as debit_amount'),
                    DB::raw('SUM(credit_amount) as credit_amount')
                )
                ->where('account_id', $accountId)
                ->where('company_id', $companyId)
                ->where('status', 1)
                ->whereIn('transaction_type', ['salesinvoice', 'salesreturn', 'opbinvoice', 'openingbalance111'])
                ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') <= ?", [$till])
                ->groupBy('transaction_date', 'transaction_id', 'transaction_no', 'transaction_type')
                ->get();
        }

        $empty = [
            'net_invoice_amount' => 0.0,
            'net_balance' => 0.0,
            '0_30' => 0.0,
            '31_60' => 0.0,
            '61_90' => 0.0,
            '90_plus' => 0.0,
            'finance_cost' => 0.0,
            'has_overdue' => false,
        ];
        if ($rows->isEmpty()) {
            return $empty;
        }

        $paymentTermsMap = $paymentTermsMap ?? collect([]);
        $salesInvoiceMap = $salesInvoiceMap ?? collect([]);
        $opbinvoiceMap = $opbinvoiceMap ?? collect([]);

        $trnNos = $rows->pluck('transaction_no')->unique()->values();

        $srnPaid = DB::table('sys_sales_return_adjestment')
            ->select('srn_no', DB::raw('SUM(paid_amount) as paid_amount'))
            ->whereIn('srn_no', $trnNos)
            ->groupBy('srn_no')
            ->pluck('paid_amount', 'srn_no');

        $receiptPaid = DB::table('sys_receipt as r')
            ->join('sys_receipt_adjustments as ra', 'ra.bi_doc_number', '=', 'r.doc_number')
            ->where('ra.account_id', $accountId)
            ->whereIn('ra.bi_doc_no', $trnNos)
            ->where('r.company_id', $companyId)
            ->where('r.status', 1)
            ->select('ra.bi_doc_no', DB::raw('SUM(ra.bi_amount) as bi_amount'))
            ->groupBy('ra.bi_doc_no')
            ->pluck('bi_amount', 'bi_doc_no');

        $jvReceiptPaid = DB::table('sys_journalvoucher as j')
            ->join('sys_receipt_adjustments as ra', 'ra.bi_doc_number', '=', 'j.doc_number')
            ->where('ra.account_id', $accountId)
            ->whereIn('ra.bi_doc_no', $trnNos)
            ->where('j.company_id', $companyId)
            ->where('j.status', 1)
            ->select('ra.bi_doc_no', DB::raw('SUM(ra.bi_amount) as bi_amount'))
            ->groupBy('ra.bi_doc_no')
            ->pluck('bi_amount', 'bi_doc_no');

        $jvPaymentPaid = DB::table('sys_journalvoucher as j')
            ->join('sys_payment_adjustments as pa', 'pa.bi_doc_number', '=', 'j.doc_number')
            ->where('pa.account_id', $accountId)
            ->whereIn('pa.bi_doc_no', $trnNos)
            ->where('j.company_id', $companyId)
            ->where('j.status', 1)
            ->select('pa.bi_doc_no', DB::raw('SUM(pa.bi_amount) as bi_amount'))
            ->groupBy('pa.bi_doc_no')
            ->pluck('bi_amount', 'bi_doc_no');

        $returnPaid = DB::table('sys_sales_return as r')
            ->join('sys_sales_return_adjestment as ra', 'ra.srn_no', '=', 'r.doc_number')
            ->where('r.customer', $accountId)
            ->whereIn('ra.siv_no', $trnNos)
            ->where('r.company_id', $companyId)
            ->where('r.status', 1)
            ->select('ra.siv_no', DB::raw('SUM(ra.paid_amount) as paid_amount'))
            ->groupBy('ra.siv_no')
            ->pluck('paid_amount', 'siv_no');

        $opbReceiptPaid = DB::table('sys_receipt_adjustments as ra')
            ->where('ra.transaction_type', 'openingbalance')
            ->where('ra.company_id', $companyId)
            ->where('ra.status', 1)
            ->where('ra.account_id', $accountId)
            ->whereIn('ra.bi_doc_no', $trnNos)
            ->select('ra.bi_doc_no', DB::raw('SUM(ra.bi_amount) as bi_amount'))
            ->groupBy('ra.bi_doc_no')
            ->pluck('bi_amount', 'bi_doc_no');

        $sumB = 0.0;
        $ageing = ['0_30' => 0.0, '31_60' => 0.0, '61_90' => 0.0, '90_plus' => 0.0];
        $totalFinance = 0.0;
        $hasOverdue = false;

        foreach ($rows as $dt) {
            $paid = (float) ($srnPaid[$dt->transaction_no] ?? 0)
                + (float) ($receiptPaid[$dt->transaction_no] ?? 0)
                + (float) ($jvReceiptPaid[$dt->transaction_no] ?? 0)
                + (float) ($opbReceiptPaid[$dt->transaction_no] ?? 0)
                - (float) ($jvPaymentPaid[$dt->transaction_no] ?? 0)
                + (float) ($returnPaid[$dt->transaction_no] ?? 0);

            if (($dt->transaction_type ?? '') === 'opbinvoice') {
                $paid += (float) ($dt->credit_amount ?? 0);
            }

            $debit = (float) ($dt->debit_amount ?? 0);
            $credit = (float) ($dt->credit_amount ?? 0);
            $trnNo = (string) ($dt->transaction_no ?? '');

            $isHide2 = 0;
            if (str_contains($trnNo, 'SR') && round($credit, 2) >= round($paid, 2)) {
                $isHide2 = 1;
            }
            if (str_contains($trnNo, 'SI') && round(abs($debit), 2) == round(abs($paid), 2)) {
                $isHide2 = 1;
            }

            if (((round($debit, 2) != round($paid, 2)) || ($credit > 0)) && $isHide2 === 0) {
                $sumB += $debit - abs($paid);

                $rowBalance = $debit - abs($paid);
                if (str_contains($trnNo, 'SR')) {
                    $rowBalance = $credit - abs($paid);
                }

                $invoiceDate = $dt->transaction_date;
                $paymentTermRow = null;

                if (($dt->transaction_type ?? '') === 'opbinvoice') {
                    $opbDet = $opbinvoiceMap->get($dt->transaction_no);
                    $paymentTermRow = SysPaymentTerms::resolveOpbPaymentTerm(
                        $opbDet->payment_terms ?? '',
                        $invoiceDate,
                        $opbDet->due_date ?? '',
                        $paymentTermsMap
                    );
                } else {
                    $siRow = $salesInvoiceMap->get($dt->transaction_no);
                    if ($siRow) {
                        $invoiceDate = $siRow->doc_date;
                        $paymentTermRow = $paymentTermsMap->get($siRow->payment_terms);
                    }
                }

                $breakdown = SysPaymentTerms::buildOutstandingBreakdown(
                    $invoiceDate,
                    $rowBalance,
                    $paymentTermRow,
                    (float) $receivableFinanceRate,
                    $till
                );
                $ageingRow = SysPaymentTerms::buildOsListAgeingBuckets(
                    $invoiceDate,
                    $rowBalance,
                    $paymentTermRow,
                    $till,
                    $breakdown['max_overdue_days'] ?? null
                );
                $ageing['0_30'] += (float) ($ageingRow['0_30'] ?? 0);
                $ageing['31_60'] += (float) ($ageingRow['31_60'] ?? 0);
                $ageing['61_90'] += (float) ($ageingRow['61_90'] ?? 0);
                $ageing['90_plus'] += (float) ($ageingRow['90_plus'] ?? 0);
                $totalFinance += (float) ($breakdown['total_finance_cost'] ?? 0);
                if (($breakdown['max_overdue_days'] ?? 0) > 0) {
                    $hasOverdue = true;
                }
            }
        }

        $netInvoiceAmount = $sumB;

        if ($adjustedPdcList === null) {
            $adjustedPdcList = self::get_list_of_adjusted_pdc([$accountId], $companyId);
        }
        if ($adjustedPdcList) {
            foreach ($adjustedPdcList->where('account_id', $accountId) as $p) {
                $sumB += (float) ($p->adj_amount ?? 0);
            }
        }

        if ($unadjustedList === null) {
            $unadjustedList = self::get_list_of_unadjusted([$accountId], $companyId, $tillDate);
        }
        if ($unadjustedList) {
            foreach ($unadjustedList->where('account_id', $accountId) as $p) {
                $amt = (float) ($p->amount ?? 0);
                if (isset($p->adj_amount)) {
                    $amt -= (float) $p->adj_amount;
                }
                $sumB += ((float) ($p->credit_amount ?? 0) > (float) ($p->debit_amount ?? 0)) ? -abs($amt) : $amt;
            }
        }

        if ($unadjustedJvToJv === null) {
            $unadjustedJvToJv = self::get_list_of_unadjusted_jv_to_jv([$accountId], $companyId);
        }
        if ($unadjustedJvToJv) {
            foreach ($unadjustedJvToJv->where('account_id', $accountId) as $p) {
                $sumB += (float) ($p->amount2 ?? 0) - (float) ($p->amount ?? 0);
            }
        }

        return [
            'net_invoice_amount' => $netInvoiceAmount,
            'net_balance' => $sumB,
            '0_30' => $ageing['0_30'],
            '31_60' => $ageing['31_60'],
            '61_90' => $ageing['61_90'],
            '90_plus' => $ageing['90_plus'],
            'finance_cost' => $totalFinance,
            'has_overdue' => $hasOverdue,
        ];
    }

    /**
     * Per-supplier totals aligned with payableoutstanding.blade.php (header #sum_{id} and ageing columns).
     */
    public static function getPayableOutstandingSupplierTotals(
        $accountId,
        $companyId,
        $tillDate = null,
        $rows = null,
        $unadjustedList = null,
        $unadjustedJvToJv = null,
        $paymentTermsMap = null,
        $purchaseInvoiceMap = null,
        $salesInvoiceMap = null,
        $opbinvoiceMap = null,
        $payableFinanceRate = 0
    ) {
        $till = $tillDate ? (self::normalizeToYmd($tillDate) ?: $tillDate) : date('Y-m-d');

        if ($rows === null) {
            $rows = DB::table('sys_chartofaccounts_transaction')
                ->select(
                    'transaction_no',
                    'transaction_type',
                    'transaction_date',
                    DB::raw('SUM(debit_amount) as debit_amount'),
                    DB::raw('SUM(credit_amount) as credit_amount')
                )
                ->where('account_id', $accountId)
                ->where('company_id', $companyId)
                ->where('status', 1)
                ->whereIn('transaction_type', ['purchaseinvoice', 'purchasereturn', 'opbinvoice', 'openingbalance111', 'salesinvoice'])
                ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') <= ?", [$till])
                ->groupBy(
                    'transaction_date',
                    'transaction_id',
                    'transaction_no',
                    'transaction_type',
                    DB::raw("CASE WHEN transaction_type = 'salesinvoice' THEN id ELSE 0 END")
                )
                ->get();
        }

        $empty = [
            'net_invoice_amount' => 0.0,
            'net_balance' => 0.0,
            '0_30' => 0.0,
            '31_60' => 0.0,
            '61_90' => 0.0,
            '90_plus' => 0.0,
            'finance_cost' => 0.0,
            'has_overdue' => false,
        ];
        if ($rows->isEmpty()) {
            return $empty;
        }

        $paymentTermsMap = $paymentTermsMap ?? collect([]);
        $purchaseInvoiceMap = $purchaseInvoiceMap ?? collect([]);
        $salesInvoiceMap = $salesInvoiceMap ?? collect([]);
        $opbinvoiceMap = $opbinvoiceMap ?? collect([]);

        $trnNos = $rows->pluck('transaction_no')->unique()->values();

        $prPaid = DB::table('sys_purchase_return_adjestment')
            ->select('piv_no', DB::raw('SUM(paid_amount) as paid_amount'))
            ->whereIn('piv_no', $trnNos)
            ->groupBy('piv_no')
            ->pluck('paid_amount', 'piv_no');

        $paymentPaid = DB::table('sys_payment as p')
            ->join('sys_payment_adjustments as pa', 'pa.bi_doc_number', '=', 'p.doc_number')
            ->where('pa.account_id', $accountId)
            ->whereIn('pa.bi_doc_no', $trnNos)
            ->where('p.company_id', $companyId)
            ->where('p.status', 1)
            ->select('pa.bi_doc_no', DB::raw('SUM(pa.bi_amount) as bi_amount'))
            ->groupBy('pa.bi_doc_no')
            ->pluck('bi_amount', 'bi_doc_no');

        $jvPaymentPaid = DB::table('sys_journalvoucher as j')
            ->join('sys_payment_adjustments as pa', 'pa.bi_doc_number', '=', 'j.doc_number')
            ->where('pa.account_id', $accountId)
            ->whereIn('pa.bi_doc_no', $trnNos)
            ->where('j.company_id', $companyId)
            ->where('j.status', 1)
            ->select('pa.bi_doc_no', DB::raw('SUM(pa.bi_amount) as bi_amount'))
            ->groupBy('pa.bi_doc_no')
            ->pluck('bi_amount', 'bi_doc_no');

        $jvReceiptPaid = DB::table('sys_journalvoucher as j')
            ->join('sys_receipt_adjustments as ra', 'ra.bi_doc_number', '=', 'j.doc_number')
            ->where('ra.account_id', $accountId)
            ->whereIn('ra.bi_doc_no', $trnNos)
            ->where('j.company_id', $companyId)
            ->where('j.status', 1)
            ->select('ra.bi_doc_no', DB::raw('SUM(ra.bi_amount) as bi_amount'))
            ->groupBy('ra.bi_doc_no')
            ->pluck('bi_amount', 'bi_doc_no');

        $returnPaid = DB::table('sys_purchase_return as r')
            ->join('sys_purchase_return_adjestment as ra', 'ra.pri_no', '=', 'r.doc_number')
            ->where('r.vendors', $accountId)
            ->whereIn('ra.pri_no', $trnNos)
            ->where('r.company_id', $companyId)
            ->where('r.status', 1)
            ->select('ra.piv_no', DB::raw('SUM(ra.paid_amount) as paid_amount'))
            ->groupBy('ra.piv_no')
            ->pluck('paid_amount', 'piv_no');

        $sumB = 0.0;
        $ageing = ['0_30' => 0.0, '31_60' => 0.0, '61_90' => 0.0, '90_plus' => 0.0];
        $totalFinance = 0.0;
        $hasOverdue = false;

        foreach ($rows as $dt) {
            $opbImportPaid = 0.0;
            if (($dt->transaction_type ?? '') === 'opbinvoice') {
                $opbImportPaid = (float) ($dt->debit_amount ?? 0);
            }

            $paid = (float) ($prPaid[$dt->transaction_no] ?? 0)
                + (float) ($paymentPaid[$dt->transaction_no] ?? 0)
                + (float) ($jvPaymentPaid[$dt->transaction_no] ?? 0)
                + $opbImportPaid
                - ((float) ($jvReceiptPaid[$dt->transaction_no] ?? 0) - (float) ($returnPaid[$dt->transaction_no] ?? 0));

            $debit = (float) ($dt->debit_amount ?? 0);
            $credit = (float) ($dt->credit_amount ?? 0);
            $trnNo = (string) ($dt->transaction_no ?? '');

            $isHide2 = 0;
            if (str_contains($trnNo, 'PR') && round($debit, 2) >= round($paid, 2)) {
                $isHide2 = 1;
            }

            if (((round($credit, 2) != round($paid, 2)) || ($debit > 0)) && $isHide2 === 0) {
                $sumB += $credit - abs($paid);

                $rowBalance = $credit - abs($paid);
                if (str_contains($trnNo, 'PR')) {
                    $rowBalance = $debit - abs($paid);
                }

                $invoiceDate = $dt->transaction_date;
                $paymentTermRow = null;

                if (($dt->transaction_type ?? '') === 'opbinvoice') {
                    $opbDet = $opbinvoiceMap->get($dt->transaction_no);
                    $paymentTermRow = SysPaymentTerms::resolveOpbPaymentTerm(
                        $opbDet->payment_terms ?? '',
                        $invoiceDate,
                        $opbDet->due_date ?? '',
                        $paymentTermsMap
                    );
                    $breakdown = SysPaymentTerms::buildOutstandingBreakdown(
                        $invoiceDate,
                        $rowBalance,
                        $paymentTermRow,
                        (float) $payableFinanceRate,
                        $till
                    );
                } elseif (str_contains($trnNo, 'SI')) {
                    $siRow = $salesInvoiceMap->get($dt->transaction_no);
                    if ($siRow) {
                        $invoiceDate = $siRow->doc_date;
                        $paymentTermRow = $paymentTermsMap->get($siRow->payment_terms);
                    }
                    $breakdown = SysPaymentTerms::buildOutstandingBreakdown(
                        $invoiceDate,
                        $rowBalance,
                        $paymentTermRow,
                        (float) $payableFinanceRate,
                        $till
                    );
                } else {
                    $piRow = $purchaseInvoiceMap->get($dt->transaction_no);
                    if ($piRow) {
                        $invoiceDate = $piRow->pi_date ?? $dt->transaction_date;
                        $paymentTermRow = $paymentTermsMap->get($piRow->payment_terms);
                    }
                    $breakdown = SysPaymentTerms::buildOutstandingBreakdown(
                        $invoiceDate,
                        $rowBalance,
                        $paymentTermRow,
                        (float) $payableFinanceRate,
                        $till
                    );
                }

                $ageingRow = SysPaymentTerms::buildOsListAgeingBuckets(
                    $invoiceDate,
                    $rowBalance,
                    $paymentTermRow,
                    $till,
                    $breakdown['max_overdue_days'] ?? null
                );
                $ageing['0_30'] += (float) ($ageingRow['0_30'] ?? 0);
                $ageing['31_60'] += (float) ($ageingRow['31_60'] ?? 0);
                $ageing['61_90'] += (float) ($ageingRow['61_90'] ?? 0);
                $ageing['90_plus'] += (float) ($ageingRow['90_plus'] ?? 0);
                $totalFinance += (float) ($breakdown['total_finance_cost'] ?? 0);
                if (($breakdown['max_overdue_days'] ?? 0) > 0) {
                    $hasOverdue = true;
                }
            }
        }

        $netInvoiceAmount = $sumB;

        $adjustedPdcList = self::get_list_of_payable_adjusted_pdc([$accountId], $companyId);
        if ($adjustedPdcList) {
            foreach ($adjustedPdcList->where('account_id', $accountId) as $p) {
                $sumB += (float) ($p->adj_amount ?? 0);
            }
        }

        if ($unadjustedList === null) {
            $unadjustedList = self::get_list_of_payable_unadjusted([$accountId], $companyId);
        }
        if ($unadjustedList) {
            foreach ($unadjustedList->where('account_id', $accountId) as $p) {
                $amt = (float) ($p->amount ?? 0);
                if (isset($p->adj_amount)) {
                    $amt -= (float) $p->adj_amount;
                }
                $sumB -= $amt;
            }
        }

        if ($unadjustedJvToJv === null) {
            $unadjustedJvToJv = self::get_list_of_payable_unadjusted_jv_to_jv([$accountId], $companyId);
        }
        if ($unadjustedJvToJv) {
            foreach ($unadjustedJvToJv->where('account_id', $accountId) as $p) {
                $sumB -= (float) ($p->amount ?? 0) - (float) ($p->amount2 ?? 0);
            }
        }

        return [
            'net_invoice_amount' => $netInvoiceAmount,
            'net_balance' => $sumB,
            '0_30' => $ageing['0_30'],
            '31_60' => $ageing['31_60'],
            '61_90' => $ageing['61_90'],
            '90_plus' => $ageing['90_plus'],
            'finance_cost' => $totalFinance,
            'has_overdue' => $hasOverdue,
        ];
    }

    /**
     * Per-customer net balance shown on receivable outstanding header (#sum_{id}).
     */
    public static function getReceivableOutstandingCustomerNetBalance($accountId, $companyId, $tillDate = null, $rows = null, $unadjustedList = null, $unadjustedJvToJv = null)
    {
        $totals = self::getReceivableOutstandingCustomerTotals(
            $accountId,
            $companyId,
            $tillDate,
            $rows,
            $unadjustedList,
            $unadjustedJvToJv
        );

        return $totals['net_balance'];
    }

    /**
     * Split receivable OPB-{id} into separate credit and debit unadjusted lines.
     */
    protected static function expand_receivable_opb_unadjusted_rows($rows, array $invoiceAmountTotalsByAccount = [])
    {
        $out = collect();
        foreach ($rows as $r) {
            $docNo = $r->doc_number ?? '';
            $isReceivableOpb = ($r->transaction_type ?? '') === 'openingbalance'
                && in_array((int) ($r->account_group ?? 0), [1, 3], true)
                && preg_match('/^OPB-\d+$/', $docNo);

            if (!$isReceivableOpb) {
                $out->push($r);
                continue;
            }

            $invNet = (float) ($invoiceAmountTotalsByAccount[$r->account_id] ?? 0);
            $adj = (float) ($r->adj_amount ?? 0);
            $creditAmt = (float) ($r->credit_amount ?? 0);
            $debitAmt = (float) ($r->debit_amount ?? 0);

            if ($creditAmt > 0) {
                $creditRow = (object) (array) $r;
                $creditRow->amount = -$creditAmt;
                $creditRow->adj_amount = 0;
                $creditRow->remarks = 'Credit amount : ' . self::com_curr_format($creditAmt, 2, '.', ',');
                $out->push($creditRow);
            }

            if ($debitAmt > 0) {
                $netDebit = $debitAmt - $invNet;
                if ($netDebit < 0) {
                    $netDebit = $debitAmt;
                }
                $effectiveInvoiceAmount = $debitAmt - $netDebit;
                if (abs($netDebit - $adj) > 0.0001) {
                    $debitRow = (object) (array) $r;
                    $debitRow->amount = $netDebit;
                    $debitRow->adj_amount = $adj;
                    $debitRow->remarks = 'Debit amount : ' . self::com_curr_format($debitAmt, 2, '.', ',')
                        . ' (Invoices amount : ' . self::com_curr_format($effectiveInvoiceAmount, 2, '.', ',') . ')';
                    $out->push($debitRow);
                }
            }
        }

        return $out;
    }

    public static function get_list_of_unadjusted($account_ids,$company,$till_date = null)
    {
        try {  
             if($account_ids == null || empty($account_ids)){

                return null;
            }         
                $company = (int) $company;
                $jvAdjustedSql = "(SELECT COALESCE(SUM(jv.amount),0)
                    FROM sys_receipt_adjustments_jv jv
                    INNER JOIN sys_journalvoucher j ON j.doc_number = jv.jv_id AND j.company_id = jv.company_id
                    WHERE jv.company_id = {$company}
                      AND jv.status = 1
                      AND j.status = 1
                      AND jv.account_id = t.account_id
                      AND jv.receipt_no = t.transaction_no)";
                $totalAdjustedSql = "COALESCE(SUM(ra.bi_paid), 0) + COALESCE(SUM(sr.paid_amount), 0) + COALESCE({$jvAdjustedSql}, 0)";

                $unadjested_receipt = DB::table('sys_chartofaccounts_transaction as t')->select(
                    't.account_id',
                    'c.account_name',
                    't.transaction_no as doc_number',
                    't.transaction_date as doc_date',
                    't.remarks',
                    't.transaction_type',
                    't.debit_amount',
                    't.credit_amount',
                    'c.group as account_group',
                    DB::raw('t.credit_amount - t.debit_amount AS amount'),
                    DB::raw("{$totalAdjustedSql} AS adj_amount")
                )
                ->leftJoin('sys_chartofaccounts as c', 'c.id', '=', 't.account_id')
                ->leftJoin('sys_receipt_adjustments as ra', function ($join) {
                        $join->on('ra.bi_doc_number', '=', 't.transaction_no')
                            ->whereColumn('ra.account_id', '=', 't.account_id');
                    })
                ->leftJoin('sys_sales_return_adjestment as sr', function ($join) {
                    $join->on(DB::raw("sr.srn_no COLLATE utf8mb4_general_ci"), '=', DB::raw("t.transaction_no COLLATE utf8mb4_general_ci"));
                })
                ->leftJoin('sys_receipt', 'sys_receipt.doc_number', '=', 't.transaction_no')
                ->whereIn('t.account_id', $account_ids)
                ->where('t.company_id', $company)
                ->where('t.status', 1)
                ->wherein('t.transaction_type',['openingbalance','journalvoucher','bankreceipt','cashreceipt','salesreturn'])
                ->where(function ($query) {
                    $query->whereNull('sys_receipt.receipt_through')
                        ->orWhereNotIn('sys_receipt.receipt_through', [3]);
                })
                ->groupBy('t.account_id','t.transaction_no','t.transaction_date','t.remarks','t.debit_amount','t.credit_amount','t.transaction_type','c.group')
                ->havingRaw('(
                    (t.transaction_type = \'openingbalance\' AND c.group IN (1,3) AND t.transaction_no REGEXP \'^OPB-[0-9]+$\' AND (IFNULL(t.credit_amount,0) > 0 OR IFNULL(t.debit_amount,0) > 0))
                    OR
                    (NOT (t.transaction_type = \'openingbalance\' AND c.group IN (1,3) AND t.transaction_no REGEXP \'^OPB-[0-9]+$\') AND (t.credit_amount - t.debit_amount) > ('.$totalAdjustedSql.'))
                )')
                ->orderby('t.transaction_date','asc')
                ->get();

                $invoiceAmountTotalsByAccount = [];
                $accountIds = collect($account_ids)->merge($unadjested_receipt->pluck('account_id'))->unique()->filter();
                foreach ($accountIds as $accountId) {
                    $invoiceAmountTotalsByAccount[$accountId] = self::sumReceivableOutstandingInvoiceAmountTotal($accountId, $company, $till_date);
                }

                return self::expand_receivable_opb_unadjusted_rows($unadjested_receipt, $invoiceAmountTotalsByAccount);

                /*$unadjested_receipt = DB::table('sys_receipt as r')->select('t.account_id',
                    'r.doc_number','r.doc_date','t.credit_amount AS amount',
                    DB::raw('COALESCE(SUM(ra.bi_paid), 0) AS adj_amount'))
                ->join('sys_chartofaccounts_transaction as t', 't.transaction_no', '=', 'r.doc_number')
                ->leftJoin('sys_receipt_adjustments as ra', 'ra.bi_doc_number', '=', 'r.doc_number')
                ->whereIn('t.account_id', $account_ids)
                ->where('r.company_id', $company)
                ->where('r.status', 1)
                //->where('r.receipt_through',[3])
                ->whereNotIn('r.doc_number', $removed_receipt)
                ->groupBy('t.account_id','r.doc_number','t.credit_amount','r.doc_date')
                ->havingRaw('t.credit_amount > COALESCE(SUM(ra.bi_paid), 0)')
                ->get();

                return $unadjested_receipt;*/
                
/*

                $unadjested_list = SysReceiptAdjustments::select('bi_doc_number','account_id',db::raw('max(bi_doc_date) as bi_doc_date'),db::raw('max(bi_cheque_amount) as rec_amount'),db::raw('sum(bi_total) as rec_paid'),db::raw('max(bi_cheque_amount) - sum(bi_total) as rec_balance'),DB::raw('(SELECT MAX(sys_chartofaccounts.account_name) FROM sys_chartofaccounts_transaction INNER JOIN sys_chartofaccounts ON sys_chartofaccounts.id=sys_chartofaccounts_transaction.account_id  WHERE transaction_no = sys_receipt_adjustments.bi_doc_number AND is_main_account = 1) AS main_account_id'))
                ->where('company_id',$com_id)->where('status',1)->wherein('account_id',$account_id)
                ->wherenotin('bi_doc_number',$removed_receipt)
                ->groupby('bi_doc_number','account_id')
                ->having(db::raw('max(bi_cheque_amount)'), '>' ,db::raw('sum(bi_total)'))
                ->get();
                
                $unadjested_list2 = SysReceipt::select('doc_number',db::raw('max(doc_date) as doc_date'),DB::raw('(SELECT MAX(sys_chartofaccounts.account_name) FROM sys_chartofaccounts_transaction INNER JOIN sys_chartofaccounts ON sys_chartofaccounts.id=sys_chartofaccounts_transaction.account_id  WHERE transaction_no = sys_receipt.doc_number AND is_main_account = 1) AS main_account_id'),DB::raw('(SELECT MAX(account_id) FROM sys_chartofaccounts_transaction WHERE transaction_no = sys_receipt.doc_number AND is_main_account = 0) AS account_id'),DB::raw('(SELECT MAX(credit_amount-debit_amount) FROM sys_chartofaccounts_transaction WHERE transaction_no = sys_receipt.doc_number AND is_main_account = 0) AS amount'),DB::raw('(SELECT COALESCE(sum(bi_paid),0) FROM sys_receipt_adjustments WHERE bi_doc_number = sys_receipt.doc_number) AS adj_amount'))
                ->where('company_id',$com_id)->where('status',1)
                ->wherenotin('doc_number',$unadjested_list->pluck('bi_doc_number'))
                ->wherenotin('doc_number',$pdc_list->pluck('doc_number'))
                ->wherenotin('doc_number',$removed_receipt)
                ->groupby('doc_number')
                ->having(db::raw('max(amount)'), '>',DB::raw('SUM(adj_amount)'))
                ->get();

                
                //$opb_inv_list = SysChartofAccountsTransaction::where('company_id',$com_id)->wherein('account_id',$account_id)->where('transaction_type','opbinvoice')->where('status', 1)->get();
                $opb_inv_list = SysChartofAccountsTransaction::select('account_id','transaction_no','transaction_date','debit_amount','credit_amount')
                ->join('sys_chartofaccounts','sys_chartofaccounts.id','sys_chartofaccounts_transaction.account_id')
                ->where('transaction_type','openingbalance')->where('credit_amount', '>' ,'0')
                ->wherein('sys_chartofaccounts.id',$account_id)->where('sys_chartofaccounts_transaction.status',1)
                ->where('sys_chartofaccounts_transaction.company_id',$com_id)->orderby('account_code','asc')->orderby('transaction_date','desc')->get();


                $unadjested_list_return = SysSalesReturn::select('doc_number','customer',db::raw('max(doc_date) as doc_date'),DB::raw('(SELECT MAX(credit_amount-debit_amount) FROM sys_chartofaccounts_transaction WHERE transaction_no = sys_sales_return.doc_number AND is_main_account = 0) AS amount'))
                ->where('company_id',$com_id)->where('status',1)->wherein('customer',$account_id)
                ->wherenotin('doc_number',$unadjested_list->pluck('bi_doc_number'))
                ->wherenotin('doc_number',$pdc_list->pluck('doc_number'))
                ->wherenotin('doc_number',$removed_receipt)
                ->groupby('doc_number','customer')
                ->get();

                $unadjested_list_jv = SysChartofAccountsTransaction::select(['transaction_no as doc_number','transaction_date as doc_date','account_id',DB::raw('SUM(credit_amount) as amount')])
                ->where('company_id', $com_id)->where('status', 1)->where('transaction_type', 'journalvoucher')->wherein('account_id',$account_id)
                ->whereNotIn(
                    'transaction_no',
                    SysReceiptAdjustments::where('company_id', $com_id)
                        ->where('status', 1)
                        ->pluck('bi_doc_number')
                )
                ->groupBy('transaction_no', 'transaction_date', 'account_id')->get();*/

        } catch (\Throwable $th) {
            \Log::error('Receivable unadjusted list query failed', [
                'company_id' => $company,
                'account_ids' => $account_ids,
                'error' => $th->getMessage(),
            ]);
            return collect([]);
        }
    }

    public static function get_positive_receivable_unadjusted_for_billwise($accountId, $companyId, $currentAdjustedByDoc = [])
    {
        $accountId = (int) $accountId;
        $companyId = (int) $companyId;

        if ($accountId <= 0 || $companyId <= 0) {
            return collect([]);
        }

        $rows = self::get_list_of_unadjusted([$accountId], $companyId);
        if (!$rows) {
            return collect([]);
        }

        $rows = collect($rows);
        $currentAdjustedByDoc = collect($currentAdjustedByDoc)
            ->mapWithKeys(function ($amount, $docNo) {
                return [(string) $docNo => (float) $amount];
            });

        $docNumbers = $rows->pluck('doc_number')->filter()->unique()->values();
        $receiptDocs = [];
        $jvDocs = [];
        $srDocs = [];
        foreach ($docNumbers as $docNo) {
            $doc = (string) $docNo;
            if (preg_match('/^(BR|CR)/', $doc)) {
                $receiptDocs[] = $doc;
            } elseif (preg_match('/^JV/', $doc)) {
                $jvDocs[] = $doc;
            } elseif (preg_match('/^SR/', $doc)) {
                $srDocs[] = $doc;
            }
        }

        $dealMap = [];
        if (!empty($receiptDocs)) {
            $dealMap = array_merge($dealMap, SysReceipt::whereIn('doc_number', $receiptDocs)->pluck('deal_id', 'doc_number')->toArray());
        }
        if (!empty($jvDocs)) {
            $dealMap = array_merge($dealMap, SysJournalVoucher::whereIn('doc_number', $jvDocs)->pluck('deal_id', 'doc_number')->toArray());
        }
        if (!empty($srDocs)) {
            $dealMap = array_merge($dealMap, SysSalesReturn::whereIn('doc_number', $srDocs)->pluck('deal_id', 'doc_number')->toArray());
        }

        return $rows->map(function ($row) use ($dealMap, $currentAdjustedByDoc) {
            $docNumber = (string) ($row->doc_number ?? '');
            $amount = (float) ($row->amount ?? 0);
            $adjusted = (float) ($row->adj_amount ?? 0);
            $currentAdjusted = (float) ($currentAdjustedByDoc->get($docNumber, 0));

            $balance = $amount - $adjusted;
            if ((float) ($row->credit_amount ?? 0) > (float) ($row->debit_amount ?? 0)) {
                $balance = -abs($balance);
            }

            if ($balance <= 0 && $currentAdjusted <= 0) {
                return null;
            }

            $paidWithoutCurrent = max($adjusted - $currentAdjusted, 0);
            $displayBalance = max($balance, 0);
            $displayTotal = max(abs($amount), $displayBalance + $paidWithoutCurrent + $currentAdjusted);

            return (object) [
                'deal_id' => $dealMap[$docNumber] ?? null,
                'doc_number' => $docNumber,
                'doc_date' => $row->doc_date ?? null,
                'lpo_number' => '',
                'total' => $displayTotal,
                'paid' => $paidWithoutCurrent,
                'balance' => $displayBalance,
                'bi_amount' => $currentAdjusted,
                'remarks' => $row->remarks ?? '',
            ];
        })->filter()->values();
    }

    public static function get_list_of_unadjusted_jv_to_jv($account_ids,$company)
    {
        try {    
              if($account_ids == null || empty($account_ids)){

                return null;
            }       
                $removed_receipt = db::table('sys_receipt_adjustments_jv')->where('company_id',$company)->where('status',1)->pluck('receipt_no');

                $docNumbersWith2Lines = DB::table('sys_chartofaccounts_transaction')
                ->select('transaction_no')
                ->where('company_id', $company)
                ->whereIn('account_id', $account_ids)
                ->where('status', 1)
                ->whereIn('transaction_type', ['journalvoucher'])
                ->groupBy('transaction_no')
                ->havingRaw('COUNT(*) = 2')
                ->pluck('transaction_no');
                $unadjested_receipt = DB::table('sys_chartofaccounts_transaction as t')
                ->select(
                    't.account_id', 'c.account_name',
                    't.transaction_no as doc_number', 't.transaction_date as doc_date',
                    't.remarks', 't.credit_amount AS amount', 't.debit_amount AS amount2',
                    DB::raw('COALESCE(SUM(ra.bi_paid), 0) AS adj_amount')
                )
                ->leftJoin('sys_chartofaccounts as c', 'c.id', '=', 't.account_id')
                ->leftJoin('sys_receipt_adjustments as ra', function ($join) {
                    $join->on('ra.bi_doc_number', '=', 't.transaction_no')
                        ->whereColumn('ra.account_id', '=', 't.account_id');
                })
                ->leftJoin('sys_receipt', 'sys_receipt.doc_number', '=', 't.transaction_no')
                ->where('t.company_id', $company)
                ->where('t.status', 1)
                ->whereIn('t.transaction_type', ['journalvoucher'])
                ->where(function ($query) {
                    $query->whereNull('sys_receipt.receipt_through')
                        ->orWhereNotIn('sys_receipt.receipt_through', [3]);
                })
                ->whereNotIn('t.transaction_no', $removed_receipt)
                ->whereIn('t.transaction_no', $docNumbersWith2Lines)
                ->groupBy('t.account_id', 't.transaction_no', 't.transaction_date', 't.remarks', 't.credit_amount', 't.debit_amount')
                ->orderBy('t.credit_amount', 'desc')
                ->get();

            return $unadjested_receipt;

        } catch (\Throwable $th) {
            return $th;
        }
    }

    public static function get_list_of_unadjusted_include_removed_jv($account_ids,$company)
    {
        try {
                $company = (int) $company;
                $currentJvNo = request()->input('jv_id');
                $currentJvNo = $currentJvNo ? addslashes((string) $currentJvNo) : '';
                $otherJvFilter = $currentJvNo !== '' ? " AND jv.jv_id <> '{$currentJvNo}'" : '';
                $currentJvFilter = $currentJvNo !== '' ? " AND jv.jv_id = '{$currentJvNo}'" : " AND 1 = 0";

                $jvOtherAdjustedSql = "(SELECT COALESCE(SUM(jv.amount),0)
                    FROM sys_receipt_adjustments_jv jv
                    INNER JOIN sys_journalvoucher j ON j.doc_number = jv.jv_id AND j.company_id = jv.company_id
                    WHERE jv.company_id = {$company}
                      AND jv.status = 1
                      AND j.status = 1
                      AND jv.account_id = t.account_id
                      AND jv.receipt_no = t.transaction_no{$otherJvFilter})";
                $jvCurrentAdjustedSql = "(SELECT COALESCE(SUM(jv.amount),0)
                    FROM sys_receipt_adjustments_jv jv
                    INNER JOIN sys_journalvoucher j ON j.doc_number = jv.jv_id AND j.company_id = jv.company_id
                    WHERE jv.company_id = {$company}
                      AND jv.status = 1
                      AND j.status = 1
                      AND jv.account_id = t.account_id
                      AND jv.receipt_no = t.transaction_no{$currentJvFilter})";
                $totalAdjustedSql = "COALESCE(SUM(ra.bi_paid), 0) + COALESCE(SUM(sr.paid_amount), 0) + COALESCE({$jvOtherAdjustedSql}, 0)";

                $unadjested_receipt = DB::table('sys_chartofaccounts_transaction as t')->select(
                    't.account_id',
                    'c.account_name',
                    't.transaction_no as doc_number',
                    't.transaction_date as doc_date',
                    't.remarks',
                    't.debit_amount',
                    't.credit_amount',
                    DB::raw('t.credit_amount - t.debit_amount AS amount'),
                    DB::raw("{$totalAdjustedSql} AS adj_amount"),
                    DB::raw("COALESCE({$jvCurrentAdjustedSql}, 0) AS removed_amount")
                )
                ->leftJoin('sys_chartofaccounts as c', 'c.id', '=', 't.account_id')
                ->leftJoin('sys_receipt_adjustments as ra', function ($join) {
                        $join->on('ra.bi_doc_number', '=', 't.transaction_no')
                            ->whereColumn('ra.account_id', '=', 't.account_id');
                    })
                ->leftJoin('sys_sales_return_adjestment as sr', function ($join) {
                    $join->on(DB::raw("sr.srn_no COLLATE utf8mb4_general_ci"), '=', DB::raw("t.transaction_no COLLATE utf8mb4_general_ci"));
                })
                ->leftJoin('sys_receipt', 'sys_receipt.doc_number', '=', 't.transaction_no')
                ->whereIn('t.account_id', $account_ids)
                ->where('t.company_id', $company)
                ->where('t.status', 1)
                ->wherein('t.transaction_type',['openingbalance','journalvoucher','bankreceipt','cashreceipt','salesreturn'])
                ->where(function ($query) {
                    $query->whereNull('sys_receipt.receipt_through')
                        ->orWhereNotIn('sys_receipt.receipt_through', [3]);
                })
                ->groupBy('t.account_id','t.transaction_no','t.transaction_date','t.remarks','t.debit_amount','t.credit_amount','c.account_name')
                ->havingRaw('(t.credit_amount - t.debit_amount) > ('.$totalAdjustedSql.')')
                ->orderby('t.transaction_date','asc')
                ->get();
                return $unadjested_receipt;

        } catch (\Throwable $th) {
            \Log::error('Receivable unadjusted include-removed query failed', [
                'company_id' => $company,
                'account_ids' => $account_ids,
                'jv_id' => request()->input('jv_id'),
                'error' => $th->getMessage(),
            ]);
            return collect([]);
        }
    }

    public static function get_list_of_unadjusted_pdc($account_ids,$company)
    {
        try {

              if($account_ids == null || empty($account_ids)){

                return null;
            }

            $removed_receipt = db::table('sys_receipt_adjustments_jv')->where('company_id',$company)->where('status',1)->pluck('receipt_no');

            $unadjested_receipt = DB::table('sys_chartofaccounts_transaction as t')->select('t.account_id','c.account_name',
                't.transaction_no as doc_number','t.transaction_date as doc_date','t.remarks','sys_receipt.cheque_date','sys_receipt.cheque_number','sys_receipt.receipt_date','t.debit_amount','t.credit_amount','t.credit_amount AS amount',
                DB::raw('COALESCE(SUM(ra.bi_paid), 0) AS adj_amount'))
            ->leftJoin('sys_chartofaccounts as c', 'c.id', '=', 't.account_id')
            ->leftJoin('sys_receipt_adjustments as ra', function ($join) {
                    $join->on('ra.bi_doc_number', '=', 't.transaction_no')
                        ->whereColumn('ra.account_id', '=', 't.account_id');
                })
            ->leftJoin('sys_receipt', 'sys_receipt.doc_number', '=', 't.transaction_no')
            //->leftJoin('sys_receipt_adjustments as ra', 'ra.bi_doc_number', '=', 't.transaction_no')
            ->whereNotIn('sys_receipt.pdc_removed_os', [2])
            ->whereIn('t.account_id', $account_ids)
            ->where('t.company_id', $company)
            ->wherein('t.status', [1,3])
            ->wherein('t.transaction_type',['bankreceipt'])
            ->where(function ($query) {
                $query->whereNull('sys_receipt.receipt_through')
                    ->orWhereIn('sys_receipt.receipt_through', [3]);
            })
            ->whereNotIn('t.transaction_no', $removed_receipt)
            ->groupBy('t.account_id','t.transaction_no','t.debit_amount','t.credit_amount','t.transaction_date','t.remarks')
            ->havingRaw('t.credit_amount > COALESCE(SUM(ra.bi_paid), 0)')
            ->orderby('t.transaction_date','asc')
            ->get();
            return $unadjested_receipt;

        } catch (\Throwable $th) {
            return $th;
        }
    }

    public static function get_list_of_adjusted_pdc($account_ids,$company)
    {
        try {
              if($account_ids == null || empty($account_ids)){

                return null;
            }

            $removed_receipt = db::table('sys_receipt_adjustments_jv')->where('company_id',$company)->where('status',1)->pluck('receipt_no');

            $unadjested_receipt = DB::table('sys_chartofaccounts_transaction as t')->select('t.account_id','c.account_name',
                't.transaction_no as doc_number','t.transaction_date as doc_date','t.remarks','sys_receipt.cheque_date','sys_receipt.cheque_number','sys_receipt.receipt_date','t.debit_amount','t.credit_amount','t.credit_amount AS amount',
                DB::raw('COALESCE(SUM(ra.bi_paid), 0) AS adj_amount'),DB::raw('GROUP_CONCAT(ra.bi_doc_no) as bi_doc_no'))
            ->leftJoin('sys_chartofaccounts as c', 'c.id', '=', 't.account_id')
            ->leftJoin('sys_receipt_adjustments as ra', function ($join) {
                    $join->on('ra.bi_doc_number', '=', 't.transaction_no')
                        ->whereColumn('ra.account_id', '=', 't.account_id');
                })
            ->leftJoin('sys_receipt', 'sys_receipt.doc_number', '=', 't.transaction_no')
            //->leftJoin('sys_receipt_adjustments as ra', 'ra.bi_doc_number', '=', 't.transaction_no')
            ->whereNotIn('sys_receipt.pdc_removed_os', [3])
            ->whereIn('t.account_id', $account_ids)
            ->where('t.company_id', $company)
            ->wherein('t.status', [1,3])
            ->wherein('t.transaction_type',['bankreceipt'])
            ->where(function ($query) {
                $query->whereNull('sys_receipt.receipt_through')
                    ->orWhereIn('sys_receipt.receipt_through', [3]);
            })
            ->whereNotIn('t.transaction_no', $removed_receipt)
            ->groupBy('t.account_id','t.transaction_no','t.debit_amount','t.credit_amount','t.transaction_date','t.remarks')
            ->havingRaw('0 < COALESCE(SUM(ra.bi_paid), 0)')
            ->orderby('t.transaction_date','asc')
            ->get();
            return $unadjested_receipt;

        } catch (\Throwable $th) {
            return $th;
        }
    }

//receivable outatsnding end

//payable outatsnding start

    /**
     * Same total as payableoutstanding.blade.php footer "Amount" for invoice rows.
     */
    protected static function sumPayableOutstandingInvoiceAmountTotal($accountId, $companyId, $tillDate = null)
    {
        $till = $tillDate ? (self::normalizeToYmd($tillDate) ?: $tillDate) : date('Y-m-d');

        $rows = DB::table('sys_chartofaccounts_transaction')
            ->select(
                'transaction_no',
                'transaction_type',
                DB::raw('SUM(debit_amount) as debit_amount'),
                DB::raw('SUM(credit_amount) as credit_amount')
            )
            ->where('account_id', $accountId)
            ->where('company_id', $companyId)
            ->where('status', 1)
            ->whereIn('transaction_type', ['purchaseinvoice', 'purchasereturn', 'opbinvoice', 'openingbalance111', 'salesinvoice'])
            ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') <= ?", [$till])
            ->groupBy(
                'transaction_date',
                'transaction_id',
                'transaction_no',
                'transaction_type',
                DB::raw("CASE WHEN transaction_type = 'salesinvoice' THEN id ELSE 0 END")
            )
            ->get();

        if ($rows->isEmpty()) {
            return 0.0;
        }

        $total = 0.0;
        foreach ($rows as $dt) {
            $credit = (float) ($dt->credit_amount ?? 0);
            $debit = (float) ($dt->debit_amount ?? 0);
            if ($credit > 0) {
                $total += $credit;
            }
            if ($debit > 0) {
                $total -= $debit;
            }
        }

        return $total;
    }

    /**
     * Split supplier OPB-{id} into separate debit and credit unadjusted lines.
     */
    protected static function expand_payable_opb_unadjusted_rows($rows, array $invoiceAmountTotalsByAccount = [])
    {
        $out = collect();
        foreach ($rows as $r) {
            $docNo = $r->doc_number ?? '';
            // Suppliers are identified by subgroup2=Suppliers elsewhere; chartofaccounts.group is not consistent.
            // Treat any OPB-{n} openingbalance as expandable in payable context.
            $isPayableOpb = ($r->transaction_type ?? '') === 'openingbalance'
                && preg_match('/^OPB-\d+$/', $docNo);

            if (!$isPayableOpb) {
                $out->push($r);
                continue;
            }

            $invNet = (float) ($invoiceAmountTotalsByAccount[$r->account_id] ?? 0);
            $adj = (float) ($r->adj_amount ?? 0);
            $creditAmt = (float) ($r->credit_amount ?? 0);
            $debitAmt = (float) ($r->debit_amount ?? 0);

            if ($debitAmt > 0) {
                $debitRow = (object) (array) $r;
                $debitRow->amount = -$debitAmt;
                $debitRow->adj_amount = 0;
                $debitRow->remarks = 'Debit amount : ' . self::com_curr_format($debitAmt, 2, '.', ',');
                $out->push($debitRow);
            }

            if ($creditAmt > 0) {
                $netCredit = $creditAmt - $invNet;
                if ($netCredit > $adj + 0.0001) {
                    $creditRow = (object) (array) $r;
                    $creditRow->amount = $netCredit;
                    $creditRow->adj_amount = $adj;
                    $creditRow->remarks = 'Credit amount : ' . self::com_curr_format($creditAmt, 2, '.', ',')
                        . ' (Invoices amount : ' . self::com_curr_format($invNet, 2, '.', ',') . ')';
                    $out->push($creditRow);
                }
            }
        }

        return $out;
    }

    public static function get_list_of_payable_unadjusted($account_ids,$company,$till_date = null)
    {
        try {           
                $till = $till_date ? (self::normalizeToYmd($till_date) ?: $till_date) : date('Y-m-d');
                $removed_payment = db::table('sys_payment_adjustments_jv')->where('company_id',$company)->where('status',1)->pluck('payment_no');

                $unadjested_payment = DB::table('sys_chartofaccounts_transaction as t')->select('t.account_id','c.account_name',
                    't.transaction_no as doc_number','t.transaction_date as doc_date','t.remarks','t.transaction_type',
                    't.debit_amount','t.credit_amount','c.group as account_group',DB::raw('t.debit_amount-t.credit_amount AS amount'),
                    DB::raw('COALESCE(SUM(ra.bi_paid), 0) AS adj_amount'))
                ->leftJoin('sys_chartofaccounts as c', 'c.id', '=', 't.account_id')
                ->leftJoin('sys_payment_adjustments as ra', function ($join) {
                        $join->on('ra.bi_doc_number', '=', 't.transaction_no')
                            ->whereColumn('ra.account_id', '=', 't.account_id');
                    })
                ->leftJoin('sys_payment', 'sys_payment.doc_number', '=', 't.transaction_no')
                //->leftJoin('sys_receipt_adjustments as ra', 'ra.bi_doc_number', '=', 't.transaction_no')
                ->whereIn('t.account_id', $account_ids)
                ->where('t.company_id', $company)
                ->where('t.status', 1)
                ->wherein('t.transaction_type',['openingbalance','journalvoucher','bankpayment','cashpayment','purchasereturn'])
                ->whereRaw("DATE_FORMAT(t.transaction_date, '%Y-%m-%d') <= ?", [$till])
                ->where(function ($query) {
                    $query->whereNull('sys_payment.payment_through')
                        ->orWhereNotIn('sys_payment.payment_through', [3]);
                })
                ->whereNotIn('t.transaction_no', $removed_payment)
                ->groupBy('t.account_id','t.transaction_no','t.transaction_date','t.remarks','t.debit_amount','t.credit_amount','t.transaction_type','c.group')
                ->havingRaw('(
                    (t.transaction_type = \'openingbalance\' AND t.transaction_no REGEXP \'^OPB-[0-9]+$\' AND (IFNULL(t.credit_amount,0) > 0 OR IFNULL(t.debit_amount,0) > 0))
                    OR
                    (NOT (t.transaction_type = \'openingbalance\' AND t.transaction_no REGEXP \'^OPB-[0-9]+$\') AND (t.debit_amount - t.credit_amount) > COALESCE(SUM(ra.bi_paid), 0))
                )')
                ->orderby('t.transaction_date','asc')
                ->get();

                $invoiceAmountTotalsByAccount = [];
                $accountIds = collect($account_ids)->merge($unadjested_payment->pluck('account_id'))->unique()->filter();
                foreach ($accountIds as $accountId) {
                    $invoiceAmountTotalsByAccount[$accountId] = self::sumPayableOutstandingInvoiceAmountTotal($accountId, $company, $till_date);
                }

                return self::expand_payable_opb_unadjusted_rows($unadjested_payment, $invoiceAmountTotalsByAccount);

        } catch (\Throwable $th) {
            return $th;
        }
    }

    public static function get_list_of_payable_unadjusted_jv_to_jv($account_ids,$company)
    {
        try {           
                $removed_payment = db::table('sys_payment_adjustments_jv')->where('company_id',$company)->where('status',1)->pluck('payment_no');

                $docNumbersWith2Lines = DB::table('sys_chartofaccounts_transaction')
                ->select('transaction_no')
                ->where('company_id', $company)
                ->whereIn('account_id', $account_ids)
                ->where('status', 1)
                ->whereIn('transaction_type', ['journalvoucher'])
                ->groupBy('transaction_no')
                ->havingRaw('COUNT(*) = 2')
                ->pluck('transaction_no');
                $unadjested_payment = DB::table('sys_chartofaccounts_transaction as t')
                ->select(
                    't.account_id', 'c.account_name',
                    't.transaction_no as doc_number', 't.transaction_date as doc_date',
                    't.remarks', 't.credit_amount AS amount', 't.debit_amount AS amount2',
                    DB::raw('COALESCE(SUM(ra.bi_paid), 0) AS adj_amount')
                )
                ->leftJoin('sys_chartofaccounts as c', 'c.id', '=', 't.account_id')
                ->leftJoin('sys_payment_adjustments as ra', function ($join) {
                    $join->on('ra.bi_doc_number', '=', 't.transaction_no')
                        ->whereColumn('ra.account_id', '=', 't.account_id');
                })
                ->leftJoin('sys_payment', 'sys_payment.doc_number', '=', 't.transaction_no')
                ->where('t.company_id', $company)
                ->where('t.status', 1)
                ->whereIn('t.transaction_type', ['journalvoucher'])
                ->where(function ($query) {
                    $query->whereNull('sys_payment.payment_through')
                        ->orWhereNotIn('sys_payment.payment_through', [3]);
                })
                ->whereNotIn('t.transaction_no', $removed_payment)
                ->whereIn('t.transaction_no', $docNumbersWith2Lines)
                ->groupBy('t.account_id', 't.transaction_no', 't.transaction_date', 't.remarks', 't.credit_amount', 't.debit_amount')
                ->orderBy('t.credit_amount', 'desc')
                ->get();

            return $unadjested_payment;

        } catch (\Throwable $th) {
            return $th;
        }
    }

    public static function get_list_of_payable_unadjusted_include_removed_jv($account_ids,$company)
    {
        try {
                $unadjested_payment = DB::table('sys_chartofaccounts_transaction as t')->select('t.account_id','c.account_name',
                    't.transaction_no as doc_number','t.transaction_date as doc_date','t.remarks',DB::raw('sum(t.debit_amount-t.credit_amount) AS amount'),
                    DB::raw('COALESCE(SUM(ra.bi_paid), 0) AS adj_amount'),DB::raw('COALESCE(sum(jv.amount), 0) AS removed_amount'))
                ->leftJoin('sys_chartofaccounts as c', 'c.id', '=', 't.account_id')
                ->leftJoin('sys_payment_adjustments as ra', function ($join) {
                        $join->on('ra.bi_doc_number', '=', 't.transaction_no')
                            ->whereColumn('ra.account_id', '=', 't.account_id');
                    })
                ->leftJoin('sys_payment', 'sys_payment.doc_number', '=', 't.transaction_no')
                
                ->leftJoin('sys_payment_adjustments_jv as jv', function ($join) {
                    $join->on(DB::raw('jv.payment_no COLLATE utf8mb4_general_ci'), '=', DB::raw('t.transaction_no COLLATE utf8mb4_general_ci'));
                })
                
                //->leftJoin('sys_receipt_adjustments as ra', 'ra.bi_doc_number', '=', 't.transaction_no')
                ->whereIn('t.account_id', $account_ids)
                ->where('t.company_id', $company)
                ->where('t.status', 1)
                ->wherein('t.transaction_type',['openingbalance','journalvoucher','bankpayment','cashpayment','purchasereturn'])
                ->where(function ($query) {
                    $query->whereNull('sys_payment.payment_through')
                        ->orWhereNotIn('sys_payment.payment_through', [3]);
                })
                ->groupBy('t.account_id','t.transaction_no','t.transaction_date','t.remarks')
                ->havingRaw('amount > COALESCE(SUM(ra.bi_paid), 0)')
                ->orderby('t.transaction_date','asc')
                ->get();
                return $unadjested_payment;

        } catch (\Throwable $th) {
            return $th;
        }
    }

    public static function get_list_of_payable_unadjusted_pdc($account_ids,$company)
    {
        try {

            $removed_payment = db::table('sys_payment_adjustments_jv')->where('company_id',$company)->where('status',1)->pluck('payment_no');

            $unadjested_payment = DB::table('sys_chartofaccounts_transaction as t')->select('t.account_id','c.account_name',
                't.transaction_no as doc_number','t.transaction_date as doc_date','t.remarks','sys_payment.cheque_date','sys_payment.cheque_number','sys_payment.payment_date','t.debit_amount AS amount',
                DB::raw('COALESCE(SUM(ra.bi_paid), 0) AS adj_amount'))
            ->leftJoin('sys_chartofaccounts as c', 'c.id', '=', 't.account_id')
            ->leftJoin('sys_payment_adjustments as ra', function ($join) {
                    $join->on('ra.bi_doc_number', '=', 't.transaction_no')
                        ->whereColumn('ra.account_id', '=', 't.account_id');
                })
            ->leftJoin('sys_payment', 'sys_payment.doc_number', '=', 't.transaction_no')
            //->leftJoin('sys_receipt_adjustments as ra', 'ra.bi_doc_number', '=', 't.transaction_no')
            ->whereNotIn('sys_payment.pdc_removed_os', [2])
            ->whereIn('t.account_id', $account_ids)
            ->where('t.company_id', $company)
            ->wherein('t.status', [1,3])
            ->where('sys_payment.pdc_removed_os', '!=' ,2)
            ->wherein('t.transaction_type',['bankpayment'])
            ->where(function ($query) {
                $query->whereNull('sys_payment.payment_through')
                    ->orWhereIn('sys_payment.payment_through', [3]);
            })
            ->whereNotIn('t.transaction_no', $removed_payment)
            ->groupBy('t.account_id','t.transaction_no','t.debit_amount','t.transaction_date','t.remarks')
            ->havingRaw('t.debit_amount > COALESCE(SUM(ra.bi_paid), 0)')
            ->orderby('t.transaction_date','asc')
            ->get();
            return $unadjested_payment;

        } catch (\Throwable $th) {
            return $th;
        }
    }

    public static function get_list_of_payable_adjusted_pdc($account_ids,$company)
    {
        try {

            $removed_payment = db::table('sys_payment_adjustments_jv')->where('company_id',$company)->where('status',1)->pluck('payment_no');

            $unadjested_payment = DB::table('sys_chartofaccounts_transaction as t')->select('t.account_id','c.account_name',
                't.transaction_no as doc_number','t.transaction_date as doc_date','t.remarks','sys_payment.cheque_date','sys_payment.cheque_number','sys_payment.payment_date','t.credit_amount AS amount',
                DB::raw('COALESCE(SUM(ra.bi_paid), 0) AS adj_amount'),DB::raw('GROUP_CONCAT(ra.bi_doc_no) as bi_doc_no'))
            ->leftJoin('sys_chartofaccounts as c', 'c.id', '=', 't.account_id')
            ->leftJoin('sys_payment_adjustments as ra', function ($join) {
                    $join->on('ra.bi_doc_number', '=', 't.transaction_no')
                        ->whereColumn('ra.account_id', '=', 't.account_id');
                })
            ->leftJoin('sys_payment', 'sys_payment.doc_number', '=', 't.transaction_no')
            //->leftJoin('sys_receipt_adjustments as ra', 'ra.bi_doc_number', '=', 't.transaction_no')
            ->whereNotIn('sys_payment.pdc_removed_os', [3])
            ->where('sys_payment.pdc_removed_os',1)
            ->whereIn('t.account_id', $account_ids)
            ->where('t.company_id', $company)
            ->wherein('t.status', [1,3])
            ->wherein('t.transaction_type',['bankpayment'])
            ->where(function ($query) {
                $query->whereNull('sys_payment.payment_through')
                    ->orWhereIn('sys_payment.payment_through', [3]);
            })
            ->whereNotIn('t.transaction_no', $removed_payment)
            ->groupBy('t.account_id','t.transaction_no','t.credit_amount','t.transaction_date','t.remarks')
            ->havingRaw('0 < COALESCE(SUM(ra.bi_paid), 0)')
            ->orderby('t.transaction_date','asc')
            ->get();
            return $unadjested_payment;

        } catch (\Throwable $th) {
            return $th;
        }
    }

//payable outatsnding end

    public static function get_months_by_date($from_date,$to_date)
    {
        $date1 = strtotime($from_date);
        $date2 = strtotime($to_date);
        $months = 0;        
        while (($date1 = strtotime('+1 MONTH', $date1)) <= $date2){
            $months++;
        }        
        return $months;
    }
    public static function get_amount_by_month($month,$plan,$amount)
    {
        $year=12;
        if($plan==1){ $year=12; }
        if($plan==2){ $year=24; }
        if($plan==3){ $year=36; }
        if($plan==4){ $year=48; }
        if($plan==5){ $year=60; }
        return ($amount/$year)*$month;
    }
    public static function get_aed_amount_new($currency, $amount)
    {
            return $amount;
        if ($currency == 1) {
            return $amount; // AED
        } else if ($currency == 2) {
            return $amount * 1.01; // QAR
        } else if ($currency == 3) {
            return $amount * 9.55; // OMR
        } else if ($currency == 4) {
            return $amount * 0.98; // SAR
        } else if ($currency == 5) {
            return $amount * 3.67; // USD
        } else if ($currency == 6) {
            return $amount * 0.044; // INR
        } else if ($currency == 7) {
            return $amount * 9.74; // BHD
        } else if ($currency == 8) {
            return $amount * 11.98; // KWD
        } else if ($currency == 9) {
            return $amount * 4.66; // GBP
        } else if ($currency == 10) {
            return $amount * 4.00; // EURO
        } else {
            return $amount;
        }
    }
    /*public static function get_aed_amount_new($currency, $amount)
    {
        if($currency==1){return $amount;}
        else if($currency==2){return $amount*0.98;}
        else if($currency==3){return $amount*0.10;}
        else if($currency==4){return $amount*1.03;}
        else if($currency==5){return $amount*0.27;}
        else if($currency==6){return $amount*22.50;}
        else if($currency==7){return $amount*0.11;}
        else if($currency==8){return $amount*0.08;}
        else if($currency==9){return $amount*0.21;}
        else if($currency==10){return $amount*0.22;}
        else {return $amount;}
    }*/

    public static function get_aed_amount($currency, $amount)
    {
        if($currency==1){return $amount;}
        else if($currency==2){return $amount;}
        else if($currency==3){return $amount;}
        else if($currency==4){return $amount*0.975;}
        else if($currency==5){return $amount*3.68;}
        else if($currency==6){return $amount*0.045;}
        else if($currency==7){return $amount*9.75;}
        else if($currency==8){return $amount*12;}
        else if($currency==9){return $amount*0.22;}
        else if($currency==10){return $amount*0.22;}
        else {return $amount;}
    }
    public static function get_currency($currency)
    {
        if($currency==1){return "AED";}
        else if($currency==2){return "QAR";}
        else if($currency==3){return "OMR";}
        else if($currency==4){return "SAR";}
        else if($currency==5){return "USD";}
        else if($currency==6){return "INR";}
        else if($currency==7){return "BHD";}
        else if($currency==8){return "KWD";}
        else if($currency==9){return "GBP";}
        else {return "AED";}
    }
    
    public static function get_deal_value($deal_value, $source, $currency, $deal_percent, $cust_id)
    {
        return SysHelper::get_aed_amount($currency,$deal_value);
        //return SysHelper::get_aed_amount_new($currency,$deal_value);
        $RET=0;
        if($deal_percent != 0 || $deal_percent != null){
            $RET = SysHelper::get_aed_amount_new($currency,($deal_value*$deal_percent/100));
        }
        //else if(in_array($cust_id, [2568,4258,4382,5322,7347,8144,8145,8146,3711,4089,8142,1,2,4350,6957,8139])){
        //    $RET = SysHelper::get_aed_amount($currency,($deal_value*50/100));
        //}
        //else if(in_array($cust_id, [8866])){
        //    $RET = SysHelper::get_aed_amount($currency,($deal_value*30/100));
        //}
        else if($source=="Fulfillment"){
            $RET = SysHelper::get_aed_amount_new($currency,($deal_value*20/100));
        }
        else{
            $RET = SysHelper::get_aed_amount_new($currency,$deal_value);
        }
        return $RET;
    }
    public static function get_deal_value_actual($deal_value, $currency)
    {
        $RET=0;
        $RET = SysHelper::get_aed_amount($currency,$deal_value);
        //$RET = SysHelper::get_aed_amount_new($currency,$deal_value);
        return $RET;
    }
    public static function get_gp_value($deal_profit,$currency)
    {
        $RET=0;
        $RET = SysHelper::get_aed_amount_new($currency,$deal_profit);
        //$RET = SysHelper::get_aed_amount_new($currency,$deal_profit);
        return $RET;
    }
    public static function get_deal_delivery_qty($quote_item_id)
    {
        $ret = DB::table('sys_crm_deal_delivery_items')->where('quote_item_id',$quote_item_id)->sum('qty');
        return $ret;
    }

    public static function password_update(){
        $updated_at = date('Y-m-d', strtotime('- 1 months'));
        $updated_on = User::select('updated_at')->where('id',Auth::user()->id)->whereRaw("DATE_FORMAT(updated_at, '%Y-%m-%d') <= '".$updated_at."'")->count();
        return $updated_on;
    }
    function notificationLateLogin($data){
        $send_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
        $send_at1 = Carbon::now('+04:00')->format('Y-m-d');
        
        foreach($data as $dt)
        {
            if($dt->ifLate == 'Yes')
            {
                $Query="INSERT INTO mail_history (employee_id, mail_type, STATUS, send_at)
                        SELECT '".$dt->employee_id."', 'RECTIFICATIONMAIL','1','".$send_at."'
                        FROM mail_history WHERE (SELECT COUNT(id) FROM  mail_history 
                        WHERE employee_id='".$dt->employee_id."' AND mail_type='RECTIFICATIONMAIL' 
                        AND DATE_FORMAT(send_at,'%Y-%m-%d') = '".$send_at1."') = 0";
                DB::INSERT($Query);
            }
        }
    
        $employeeList = DB::table('mail_history')
        ->join('employee','mail_history.employee_id','employee.employee_id')
        ->select('employee.email','employee.first_name')
        ->where('mail_history.status','1')
        ->where('mail_history.mail_type','RECTIFICATIONMAIL')
        ->whereNotNull('employee.email')->distinct()->get();
         
        $body = "Late attendance punching found today, Login and Please rectify your attendance.";
        $body .= "<br />";
        $body .= "Date : " . date("d-M-Y", strtotime($send_at1));
    
        foreach ($employeeList AS $list)
        {
            notificationMail($list->first_name,$body,$list->email, 'Employee Attendance Rectification Warning');
            //notificationMail($list->first_name,$body,'sajeeshck@gmail.com', 'Employee Attendance Rectification Warning');
        }
        DB::table('mail_history')->where('mail_type','RECTIFICATIONMAIL')
        ->update([
            'status' => 2,
        ]);
    }
    
    function notificationNoLogOut(){
        //$send_at1 = Carbon::now('+04:00')->format('Y-m-d');
        $send_at1 = Carbon::now('+04:00')->addDays(-1)->format('Y-m-d');
    
        $Query="INSERT INTO mail_history (employee_id, mail_type, STATUS, send_at)
                SELECT employee_id, 'REMOTELOGOUTMAIL',1, '".$send_at1."' FROM(
                    SELECT employee_id, MAX(in_out_time)in_out_time, MAX(TYPE)TYPE FROM employee_remote_attendance 
                    WHERE DATE_FORMAT(in_out_time,'%Y-%m-%d')='".$send_at1."'
                    GROUP BY employee_id
                    ORDER BY in_out_time DESC)tbl WHERE TYPE=1 
                    AND (SELECT COUNT(id) FROM  mail_history 
                        WHERE employee_id=tbl.employee_id AND mail_type='REMOTELOGOUTMAIL' 
                        AND DATE_FORMAT(send_at,'%Y-%m-%d') = '".$send_at1."') = 0";
    
        DB::INSERT($Query);
    
        $employeeList = DB::table('mail_history')
        ->join('employee','mail_history.employee_id','employee.employee_id')
        ->select('employee.email','employee.first_name')
        ->where('mail_history.status','1')
        ->where('mail_history.mail_type','REMOTELOGOUTMAIL')
        ->whereNotNull('employee.email')->distinct()->get();
         
        $body = "Attendance logout not found on last day, Login and Please update your logout time.";
        $body .= "<br />";
        $body .= "Date : " . date("d-M-Y", strtotime($send_at1));
    
        foreach ($employeeList AS $list)
        {
            //notificationMail($list->first_name,$body,'sajeeshck@gmail.com', 'Employee Attendance Logout Warning');
            notificationMail($list->first_name,$body,$list->email, 'Employee Attendance Logout Warning');
        }
        DB::table('mail_history')->where('mail_type','REMOTELOGOUTMAIL')
        ->update([
            'status' => 2,
        ]);
    }


    public static function get_week_offs($dateDisplay,$weekly_offs){
        try {            
            $date = Carbon::createFromFormat('d/m/Y', $dateDisplay);

            $dayName = $date->format('l'); // Monday, Tuesday...
            $weekOfMonth = $date->weekOfMonth;

            $isWeeklyOff = false;

            foreach ($weekly_offs as $rule) {

                // Extract day from rule
                preg_match('/(Monday|Tuesday|Wednesday|Thursday|Friday|Saturday|Sunday)/', $rule, $dayMatch);

                if (empty($dayMatch)) continue;

                $ruleDay = $dayMatch[0];

                // Skip if day doesn't match
                if ($ruleDay !== $dayName) continue;

                // All weeks (e.g., Monday (All))
                if (str_contains($rule, '(All)')) {
                    $isWeeklyOff = true;
                    break;
                }

                // Specific weeks (1 & 3, 2 & 4)
                preg_match_all('/\d+/', $rule, $matches);
                $weeks = array_map('intval', $matches[0]);

                if (in_array($weekOfMonth, $weeks)) {
                    $isWeeklyOff = true;
                    break;
                }
            }
            return $isWeeklyOff;        
        } catch (\Throwable $th) {
            //return $th;
            return $isWeeklyOff;
        }
    }
    

    public static function notificationMailReport($name, $body, $to, $sub,$new,$qualified,$unqualified,$converted,$dealpend,$totdeal){
        $data =  array(
            'name' => $name,
            'body' => $body,
            'email' => $to,
            'subject' => $sub,
            'new' => $new,
            'qualified' => $qualified,
            'unqualified' => $unqualified,
            'converted' => $converted,
            'dealpend' => $dealpend,
            'totdeal' => $totdeal
        );
        try {            
            Mail::send('emails.mailTempReport',$data, function ($msg) use ($data){
                $msg->from('marketing@wesyscom.com', 'Syscom');
                $msg->to($data['email'])->subject($data['subject']);
            });
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
    public static function LeadWeeklyNotificationMail()
    {
        $start_date = date('Y-m-d', strtotime('-2 week monday 00:00:00'));
        $end_date = date('Y-m-d', strtotime('-1 week sunday 23:59:59'));
        
        $users = DB::table('users')->select('id','full_name','email')->where('active_status',1)->orderby('full_name','asc')->get();

        foreach ($users as $value) {

            $results = DB::table('sys_crm_leads')->select('status')
            ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(date, '%Y-%m-%d') <= '".$end_date."'")
            ->where('owner',$value->id)->get();

            $dealpend = SysCrmDeals::wherein('stage',[1,2,3])->where('owner',$value->id)
            ->whereRaw("DATE_FORMAT(estimated_close_date, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(estimated_close_date, '%Y-%m-%d') <= '".$end_date."'")->count();
            
            $totdeal = SysCrmDeals::wherein('stage',[1,2,3])->where('owner',$value->id)
            ->whereRaw("DATE_FORMAT(estimated_close_date, '%Y-%m-%d') <= '".$end_date."'")->count();


            if(count($results)>0){
                $total=count($results);
                $new = count($results->where('status',1));
                $qualified = count($results->where('status',2));
                $unqualified = count($results->where('status',3));
                $converted = count($results->where('status',0));

                $body = "You have ".$total." leads assigned to you this week. Kindly update the lead status.";
                $body .= "<br />A gentle reminder, ".$totdeal." deals are overdue as per the estimated closing date, please do the needful.";
                $body .= "<br />";

                //SysHelper::notificationMailReport($value->full_name,$body,'sajeesh@sysllc.com', 'Last Week Lead Report',$new,$qualified,$unqualified,$converted,$dealpend,$totdeal);
                SysHelper::notificationMailReport($value->full_name,$body,$value->email, 'Last Week Lead Report',$new,$qualified,$unqualified,$converted,$dealpend,$totdeal);
            }
            else{
                if($dealpend != 0 && $totdeal !=0){
                $body = "You have 0 leads assigned to you this week. Kindly update the lead status.";
                $body .= "A gentle reminder, ".$totdeal." deals are overdue as per the estimated closing date, please do the needful.";
                $body .= "<br />";

                SysHelper::notificationMailReport($value->full_name,$body,$value->email, 'Last Week Lead Report',0,0,0,0,$dealpend,$totdeal);
                }
            }
        }
    }



    //BOQ START
    
    public static function RemoveLastNullValue($arr)
    {
        unset($arr[count($arr)-1]);
        return $arr;
    }
    public static function AddLocationItems($qty,$customer_type,$con,$company)
    {
        $data =  ['700514867','700479702','700429202','700289747', '396445'];
        if($con=="Yes"){
            $data[] =  '205650';
        }
        foreach ($data as $dt) {
            //$pro = Products::select('id','price_re','price_en')->where('sku',$dt)->first();
            $pro = SysHelper::get_price_first($dt,$customer_type,$company);
            DB::table('product_cart')->insert(
                [
                    'user_id' => Auth::user()->id,
                    'cart_id' => session('logged_session_data.cart_id'),
                    'product_id' => $pro->id,
                    'qty' => $qty,
                    'price' => $pro->price,
                ]);
        }
    }
    public static function AddPRIItems($customer_type, $pri,$company)
    {
        try {
            if($pri >= 1 && $pri <=8){
                $data =  ['700417439','700504031'];
                $arr = array_count_values($data);
            }
            if($pri >= 9 && $pri <=10){
                $data =  ['700417439','700504031','383092'];
                $arr = array_count_values($data);
            }
            if($pri >= 11 && $pri <=12){
                $data =  ['700417439','700504031','383092','383092'];
                $arr = array_count_values($data);
            }
            if($pri >= 13 && $pri <=14){
                $data =  ['700417439','700504031','383092','383092','383092'];
                $arr = array_count_values($data);
            }
            if($pri >= 15 && $pri <=16){
                $data =  ['700417439','700504031','383093'];
                $arr = array_count_values($data);
            }
            if($pri >= 17 && $pri <=18){
                $data =  ['700417439','700504031','383093','383092'];
                $arr = array_count_values($data);
            }
            if($pri >= 19 && $pri <=20){
                $data =  ['700417439','700504031','383093','383092','383092'];
                $arr = array_count_values($data);
            }
            if($pri >= 21 && $pri <=22){
                $data =  ['700417439','700504031','383093','383092','383092','383092'];
                $arr = array_count_values($data);
            }
            if($pri >= 23 && $pri <=24){
                $data =  ['700417439','700504031','383093','383093'];
                $arr = array_count_values($data);
            }
            if($pri >= 25 && $pri <=26){
                $data =  ['700417439','700504031','383093','383093','383092'];
                $arr = array_count_values($data);
            }
            if($pri >= 27 && $pri <=28){
                $data =  ['700417439','700504031','383093','383093','383092','383092'];
                $arr = array_count_values($data);
            }
            if($pri >= 29 && $pri <=30){
                $data =  ['700417439','700504031','383094'];
                $arr = array_count_values($data);
            }
            if($pri >= 31 && $pri <=32){
                $data =  ['700417439','700504031','383094','383092'];
                $arr = array_count_values($data);
            }
            if($pri >= 33 && $pri <=34){
                $data =  ['700417439','700504031','383094','383092','383092'];
                $arr = array_count_values($data);
            }
            if($pri >= 35 && $pri <=36){
                $data =  ['700417439','700504031','383094','383092','383092','383092'];
                $arr = array_count_values($data);
            }
            if($pri >= 37 && $pri <=38){
                $data =  ['700417439','700504031','383094','383093'];
                $arr = array_count_values($data);
            }
            if($pri >= 39 && $pri <=40){
                $data =  ['700417439','700504031','383094','383093','383092'];
                $arr = array_count_values($data);
            }

            //$pro = Products::select('id','price_re','price_en','sku')->wherein('sku',$data)->get();
            $pro = SysHelper::get_price_get($data,$customer_type,$company);
            foreach ($pro as $p) {            
                $cart=SysCrmBoqProductCart::select('id','qty')
                    ->where(['cart_id' => session('logged_session_data.cart_id'),'user_id'=> Auth::user()->id,'product_id'=> $p->id])->first();
                if(isset($cart)){
                    DB::table('product_cart')->where('id',$cart->id)
                    ->update([
                        'qty' => $cart->qty + $arr[$p->part_number],
                        'price' => $p->price,
                    ]);
                }
                else{
                    DB::table('product_cart')->insert([
                        'user_id' => Auth::user()->id,
                        'cart_id' => session('logged_session_data.cart_id'),
                        'product_id' => $p->id,
                        'qty' => $arr[$p->part_number],
                        'price' => $p->price,
                    ]);
                }
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function PhoneLicense($customer_type,$company)
    {
        $data =  ['700505992','700512394','700513916','700513569','700512396','700514009','700504740','700514246','700501533','700501530','700508893','700514693','700513907','700513905'];
        $pro_qty = SysCrmBoqProductCart::join('sm_items','sm_items.id','product_cart.product_id')
        ->where(['cart_id' => session('logged_session_data.cart_id'),'user_id'=> Auth::user()->id])
        ->wherein('sm_items.part_number',$data)->sum('qty');
        
        if($pro_qty != 0){
            //$pro = Products::select('id','price_re','price_en','sku')->where('sku','383110')->first();
            $pro = SysHelper::get_price_383110_EL('383110',$customer_type,$company);
            $cart=SysCrmBoqProductCart::select('id','qty')
            ->where(['cart_id' => session('logged_session_data.cart_id'),'user_id'=> Auth::user()->id,'product_id'=> $pro->id])->first();
            if(isset($cart)){
                DB::table('product_cart')->where('id',$cart->id)
                ->update([
                    'qty' => $pro_qty,
                    'price' => $pro->price,
                ]);
            }
            else{
                DB::table('product_cart')->insert([
                    'user_id' => Auth::user()->id,
                    'cart_id' => session('logged_session_data.cart_id'),
                    'product_id' => $pro->id,
                    'qty' => $pro_qty,
                    'price' => $pro->price,
                ]);
            }
        }
    }

    public static function GetBasicPhones()
    {
        $data =  ['700505992','700512394','700513916','700513569','700512396'];
        return $data;
    }
    public static function GetConfrencePhone()
    {
        $data =  ['700513892','700514009','700504740','700514246','700501533','700501530','700508893','700514693'];
        return $data;
    }
    public static function GetManagerLevelPhones()
    {
        $data =  ['700513905','700512398','700515454','700513907','700512402'];
        return $data;
    }

    public static function AddAnalogItems($customer_type, $anlo,$company)
    {
        try {
            if($anlo >= 1 && $anlo <=4){
                $data =  ['700504556'];
                $arr = array_count_values($data);
            }
            if($anlo >= 5 && $anlo <=8){
                $data =  ['700504556','700504556'];
                $arr = array_count_values($data);
            }
            if($anlo >= 9 && $anlo <=12){
                $data =  ['700504556','700504556','700503164','700504031'];
                $arr = array_count_values($data);
            }
            if($anlo >= 11 && $anlo <=16){
                $data =  ['700504556','700504556','700503164','700503164','700504031','700504031'];
                $arr = array_count_values($data);
            }
            //$pro = Products::select('id','price_re','price_en','sku')->wherein('sku',$data)->get();
            $pro = SysHelper::get_price_get($data,$customer_type,$company);
            foreach ($pro as $p) {            
                $cart=SysCrmBoqProductCart::select('id','qty')
                    ->where(['cart_id' => session('logged_session_data.cart_id'),'user_id'=> Auth::user()->id,'product_id'=> $p->id])->first();
                if(isset($cart)){
                    DB::table('product_cart')->where('id',$cart->id)
                    ->update([
                        'qty' => $cart->qty + $arr[$p->part_number],
                        'price' => $p->price,
                    ]);
                }
                else{
                    DB::table('product_cart')->insert([
                        'user_id' => Auth::user()->id,
                        'cart_id' => session('logged_session_data.cart_id'),
                        'product_id' => $p->id,
                        'qty' => $arr[$p->part_number],
                        'price' => $p->price,
                    ]);
                }
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    public static function check_j189j179($sku)
    {
        if($sku == "700514337")
        {
            $phone=SysCrmBoqProductCart::where(['cart_id' => session('logged_session_data.cart_id'),'user_id'=> Auth::user()->id])
            ->wherein('product_id',[224,247])->sum('qty');
            
            $module=SysCrmBoqProductCart::where(['cart_id' => session('logged_session_data.cart_id'),'user_id'=> Auth::user()->id])
            ->where('product_id',[277])->sum('qty');

            if($phone > $module)
            {
                return $phone;
            }
            else
            {
                return "0";
            }
        }
        else {return "0";} 
    }
    //change
    public static function get_price_get($data, $cust_type, $company)
    {
        if($cust_type==1){
            return SmItem::select('sm_items.id','sm_items.part_number','sm_items.description','sys_price_book.r_price as price')
                ->join('sys_price_book','sys_price_book.pid','sm_items.id')
                ->where('sys_price_book.currency_id', session('form_session_data.currency_id'))->wherein('part_number',$data)->get();
        }
        else{
            return SmItem::select('sm_items.id','sm_items.part_number','sm_items.description','sys_price_book.e_price as price')
                ->join('sys_price_book','sys_price_book.pid','sm_items.id')
                ->where('sys_price_book.currency_id', session('form_session_data.currency_id'))->wherein('part_number',$data)->get();
        }
    }
    public static function get_price_first($dt, $cust_type, $company)
    {
        if($cust_type==1){
            return SmItem::select('sm_items.id','sm_items.part_number','sm_items.description','sys_price_book.r_price as price')
                ->join('sys_price_book','sys_price_book.pid','sm_items.id')
                ->where('sys_price_book.currency_id', session('form_session_data.currency_id'))->where('part_number',$dt)->first();
        }
        else{
            return SmItem::select('sm_items.id','sm_items.part_number','sm_items.description','sys_price_book.e_price as price')
                ->join('sys_price_book','sys_price_book.pid','sm_items.id')
                ->where('sys_price_book.currency_id', session('form_session_data.currency_id'))->where('part_number',$dt)->first();
        }
    }
    public static function get_price_383110_EL($el_sku, $cust_type, $company)//EndPoint License
    {
        if($cust_type==1){
            return SmItem::select('sm_items.id','sm_items.part_number','sm_items.description','sys_price_book.r_price as price')
                ->join('sys_price_book','sys_price_book.pid','sm_items.id')
                ->where('sys_price_book.currency_id', session('form_session_data.currency_id'))->where('part_number',$el_sku)->first();
        }
        else{
            return SmItem::select('sm_items.id','sm_items.part_number','sm_items.description','sys_price_book.e_price as price')
                ->join('sys_price_book','sys_price_book.pid','sm_items.id')
                ->where('sys_price_book.currency_id', session('form_session_data.currency_id'))->where('part_number',$el_sku)->first();
        }
    }
    public static function cart_sum()
    {
        $cartsku = DB::table('product_cart')
        ->where(['cart_id' => session('logged_session_data.cart_id'),'user_id'=> Auth::user()->id])
        ->sum(\DB::raw('qty * price'));
        return $cartsku;
    }
    //BOQ STOP


    public static function get_deal_filter($date_id, $company_id)
    {
        try {
    
            if($date_id=="d"){
                $deals_query = SysCrmDeals::whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') = '".date('Y-m-d')."'");
                $deals_type_query = SysCrmDeals::select('isproject')->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') = '".date('Y-m-d')."'");               
            }
            if($date_id=="m"){
                $deals_query = SysCrmDeals::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '".date('Y-m')."'");
                $deals_type_query = SysCrmDeals::select('isproject')->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '".date('Y-m')."'");
            }
            if($date_id=="y"){
                $deals_query = SysCrmDeals::whereRaw("DATE_FORMAT(created_at, '%Y') = '".date('Y')."'");
                $deals_type_query = SysCrmDeals::select('isproject')->whereRaw("DATE_FORMAT(created_at, '%Y') = '".date('Y')."'");
            }
            if($date_id=="q"){
                $quarter = SysHelper::get_quarter(date('m'));
                $start_date = $quarter[0];
                $end_date = $quarter[1];
                $deals_query = SysCrmDeals::whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(created_at, '%Y-%m-%d') <= '".$end_date."'");
                $deals_type_query = SysCrmDeals::select('isproject')->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(created_at, '%Y-%m-%d') <= '".$end_date."'");
            }
            if($date_id=="pm"){
                $c_date = Carbon::createFromFormat('Y-m', date('Y-m'))->subMonth();  
                $pm_date = $c_date->format('Y-m');

                $deals_query = SysCrmDeals::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '".$pm_date."'");
                $deals_type_query = SysCrmDeals::select('isproject')->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '".$pm_date."'"); 
            }
            if($date_id=="pq"){
                $quarter = SysHelper::get_pre_quarter(date('m'));
                $start_date = $quarter[0];
                $end_date = $quarter[1];
                
                $deals_query = SysCrmDeals::whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(created_at, '%Y-%m-%d') <= '".$end_date."'");
                $deals_type_query = SysCrmDeals::select('isproject')->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(created_at, '%Y-%m-%d') <= '".$end_date."'");
            }
            
            if($company_id != 0){
                $deals_query->where('company_id',$company_id);
                $deals_type_query->where('company_id',$company_id);
            }
            $total_deals1 = $deals_query->get();
            $deals_type1 = $deals_type_query->get(); 

            if(Auth::user()->role_id == 1){ //admin
                $total_deals=$total_deals1;
                $deals_type=$deals_type1;
            }
            else{
                $total_deals=$total_deals1->where('owner',Auth::user()->id);
                $deals_type=$deals_type1->where('owner',Auth::user()->id);
            }
            

            $prospecting = $total_deals->where('stage',1)->count();
            $quote = $total_deals->where('stage',2)->count();
            $closure = $total_deals->where('stage',3)->count();
            $won = $total_deals->where('stage',4)->count();
            $lost = $total_deals->where('stage',5)->count();

            $project = $deals_type->where('isproject',1)->count();
            $channel = $deals_type->where('isproject',2)->count();
            $corporate = $deals_type->where('isproject',3)->count();

            $ret = [$prospecting,$quote,$closure,$won,$lost,$project,$channel,$corporate];
            return $ret;
    
        }catch (\Exception $e) {
            return $e;
            $ret = [0,0,0,0,0,0,0,0];
            return $ret;
        }
    }

    public static function get_lead_filter($date_id, $company_id)
    {
        try {
    
            if($date_id=="d"){
                $leads_query = SysCrmLeads::whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') = '".date('Y-m-d')."'");
                $leads_type_query = SysCrmLeads::select('isproject')->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') = '".date('Y-m-d')."'");
            }
            if($date_id=="m"){
                $leads_query = SysCrmLeads::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '".date('Y-m')."'");
                $leads_type_query = SysCrmLeads::select('isproject')->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '".date('Y-m')."'");
            }
            if($date_id=="y"){
                $leads_query = SysCrmLeads::whereRaw("DATE_FORMAT(created_at, '%Y') = '".date('Y')."'");
                $leads_type_query = SysCrmLeads::select('isproject')->whereRaw("DATE_FORMAT(created_at, '%Y') = '".date('Y')."'");
            }
            if($date_id=="q"){
                $quarter = SysHelper::get_quarter(date('m'));
                $start_date = $quarter[0];
                $end_date = $quarter[1];

                $leads_query = SysCrmLeads::whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(created_at, '%Y-%m-%d') <= '".$end_date."'");
                $leads_type_query = SysCrmLeads::select('isproject')->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(created_at, '%Y-%m-%d') <= '".$end_date."'");
            }
            
            if($date_id=="pm"){
                $c_date = Carbon::createFromFormat('Y-m', date('Y-m'))->subMonth();  
                $pm_date = $c_date->format('Y-m');

                $leads_query = SysCrmLeads::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '".$pm_date."'");
                $leads_type_query = SysCrmLeads::select('isproject')->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '".$pm_date."'");
            }
            if($date_id=="pq"){
                $quarter = SysHelper::get_pre_quarter(date('m'));
                $start_date = $quarter[0];
                $end_date = $quarter[1];
                
                $leads_query = SysCrmLeads::whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(created_at, '%Y-%m-%d') <= '".$end_date."'");
                $leads_type_query = SysCrmLeads::select('isproject')->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') >= '".$start_date."' and DATE_FORMAT(created_at, '%Y-%m-%d') <= '".$end_date."'");
            }
            
            if($company_id!=0){
                $leads_query->where('company_id',$company_id);
                $leads_type_query->where('company_id',$company_id);
            }
            $total_leads1 = $leads_query->get();
            $leads_type1 = $leads_type_query->get(); 

            if(Auth::user()->role_id == 1){ //admin
                $total_leads=$total_leads1;
                $leads_type=$leads_type1;
            }
            else{
                $total_leads=$total_leads1->where('owner',Auth::user()->id);
                $leads_type=$leads_type1->where('owner',Auth::user()->id);
            }

            $new = $total_leads->where('status',1)->count();
            $qualified = $total_leads->whereIn('status',[0,2])->count();
            $unqualified = $total_leads->where('status',3)->count();
            $pending_response = $total_leads->where('status',4)->count();
            $closed = $total_leads->where('status',10)->count();
            $reseller = $leads_type->where('isproject',1)->count();
            $enduser = $leads_type->where('isproject',2)->count();
            $ecommerce = $leads_type->where('isproject',3)->count();
            
            $ret = [$new,$qualified,$unqualified,$pending_response,$closed,$reseller,$enduser,$ecommerce];
            
            return $ret;
    
        }catch (\Exception $e) {
            return $e;
            $ret = [0,0,0,0,0,0];
            return $ret;
        }
    }

    public static function set_amc_per_month($deal_id, $owner, $months)
    {
        try {
            
            $amc_amount=0;
            $amc_item = DB::table('sys_crm_deals')->select('qty','price')->leftjoin('sys_crm_quote_items','sys_crm_quote_items.deal_id','sys_crm_deals.id')
                    ->wherein('sys_crm_quote_items.product_id',[9976,10465,10497])->where('sys_crm_deals.id',$deal_id)->get();

            if(count($amc_item)>0){
                foreach($amc_item as $item){
                    $amc_amount += $item->price * $item->qty;
                }
                if($amc_amount>0){
                    //DB::table('sys_crm_amc_per_month')->where('deal_id', $deal_id)->delete();
                    for ($i = 0; $i < $months; $i++) {
                        /*DB::table('sys_crm_amc_per_month')->insert(
                            [
                                'deal_id' => $deal_id,
                                'amc_amount' => $amc_amount/$months,
                                'amc_date' => Carbon::now('+04:00')->addMonths($i)->format('Y-m-d'),
                                'owner' => $owner,
                                'created_by' => Auth::user()->id,
                                'created_at' => Carbon::now('+04:00'),
                            ]
                        );*/
                    }
                }
            }
        }catch (\Exception $e) {
            return $e;
        }
    }
    public static function cancel_amc($deal_id)
    {
        try {            
            /*DB::table('sys_crm_amc_per_month')->where('deal_id', $deal_id)->update(
                [
                    'status' => 0,
                ]
            );*/
        }catch (\Exception $e) {
            return $e;
        }
    }

    public static function add_to_price_book($items_id)
    {
SysPriceBook::insert([
    'pid' => $items_id,
    'currency_id' => 1,
    'r_price' => '0.00',
    'e_price' => '0.00',
    'status' => 1,
    'created_by' => Auth::user()->id,
    'company_id' => session('logged_session_data.company_id')
]);
SysPriceBook::insert([
    'pid' => $items_id,
    'currency_id' => 2,
    'r_price' => '0.00',
    'e_price' => '0.00',
    'status' => 1,
    'created_by' => Auth::user()->id,
    'company_id' => session('logged_session_data.company_id')
]);
SysPriceBook::insert([
    'pid' => $items_id,
    'currency_id' => 3,
    'r_price' => '0.00',
    'e_price' => '0.00',
    'status' => 1,
    'created_by' => Auth::user()->id,
    'company_id' => session('logged_session_data.company_id')
]);
SysPriceBook::insert([
    'pid' => $items_id,
    'currency_id' => 4,
    'r_price' => '0.00',
    'e_price' => '0.00',
    'status' => 1,
    'created_by' => Auth::user()->id,
    'company_id' => session('logged_session_data.company_id')
]);
SysPriceBook::insert([
    'pid' => $items_id,
    'currency_id' => 5,
    'r_price' => '0.00',
    'e_price' => '0.00',
    'status' => 1,
    'created_by' => Auth::user()->id,
    'company_id' => session('logged_session_data.company_id')
]);
SysPriceBook::insert([
    'pid' => $items_id,
    'currency_id' => 6,
    'r_price' => '0.00',
    'e_price' => '0.00',
    'status' => 1,
    'created_by' => Auth::user()->id,
    'company_id' => session('logged_session_data.company_id')
]);
SysPriceBook::insert([
    'pid' => $items_id,
    'currency_id' => 7,
    'r_price' => '0.00',
    'e_price' => '0.00',
    'status' => 1,
    'created_by' => Auth::user()->id,
    'company_id' => session('logged_session_data.company_id')
]);
SysPriceBook::insert([
    'pid' => $items_id,
    'currency_id' => 8,
    'r_price' => '0.00',
    'e_price' => '0.00',
    'status' => 1,
    'created_by' => Auth::user()->id,
    'company_id' => session('logged_session_data.company_id')
]);
SysPriceBook::insert([
    'pid' => $items_id,
    'currency_id' => 9,
    'r_price' => '0.00',
    'e_price' => '0.00',
    'status' => 1,
    'created_by' => Auth::user()->id,
    'company_id' => session('logged_session_data.company_id')
]);
}


    public static function exe_web_push($user_id,$title,$msg,$url)
    {
        $request =[
            'title' => $title,
            'body' => $msg,
            'url' => $url,
        ];
         
        $webPush = new WebPush([
            "VAPID" => [
                "publicKey" => "BOPWfY51U_FzhkN3YGiLoRpNwHEN7Q_R_2YSRgqijTn4VVb8aBy5YoEEoAbevT0hL74L91qig0-hTAW3xo1Eg6M",
                "privateKey" => "KsI8O6YzDK9unkbqlOWpeEaLnWWADw35lexaDx5jDxg",
                "subject" => "crm.venushrms.com"
            ]
        ]);

        $sub = PushSubscription::where('user_id',$user_id)->get();
        foreach ($sub as $value) {
            $result = $webPush->sendOneNotification(
                Subscription::create(json_decode($value->data ,true)),
                json_encode($request)
            );
        }
        
        $sub = PushSubscription::wherein('user_id',[4,1])->get();
        foreach ($sub as $value) {
            $result = $webPush->sendOneNotification(
                Subscription::create(json_decode($value->data ,true)),
                json_encode($request)
            );
        }
    }

    public static function get_total_revenue_all_by_brand($brand_id,$from_month,$to_month,$company){

        $brand=$brand_id;

        $data1=[];
        $data2=[];
        
            $data1 = DB::table('sys_crm_deals')->select('sys_crm_deals.id as dealid','deal_value','deal_currency','source','cust_id','deal_percent','deal_profit')->leftjoin('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_id','sys_crm_deals.id')->where('sys_crm_deals.is_partial_invoice',0);
            if($from_month !="" && $to_month !=""){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '".date('Y-m-d', strtotime($from_month))."' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($to_month))."'");
            }
            if($from_month !="" && $to_month ==""){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') = '".date('Y-m-d', strtotime($from_month))."'");
            }
            $data1->where('sys_crm_deals.stage',4)->where('sys_crm_deal_track_approval_invoice.status',1)->wherein('sys_crm_deals.company_id',$company);
               
            $dataA = $data1->get();
            $retAmount=0.00; $retProfit=0.00;

            $dataid=[];
            foreach ($dataA as $id) {
                $dataid[]=$id->dealid;
            }

            $dt_query=SysCrmQuoteItems::select('qty','price','discount','brand','sm_items.part_number','deal_id','currency_id','sys_crm_quote_items.description')
                ->join('sm_items','sm_items.id','sys_crm_quote_items.product_id')
                ->wherein('deal_id',$dataid)->where('brand',$brand);             
                $dt=$dt_query->get();

                foreach ($dt as $val) {                    
                    $ret = ($val->qty * $val->price) - ($val->qty * $val->discount);
                    $retAmount += $ret;
                    $retProfit=0;
                }
        
        return [$retAmount, $retProfit];
    }
	public static function get_total_forcast_all_by_brand($brand,$from_month,$to_month,$company_id){
        try {
            
            $data2 = DB::table('sys_crm_deals')->select('deal_value','deal_currency','source','cust_id','id')
        ->wherein('sys_crm_deals.company_id',$company_id)
        ->wherein('stage',[1,2,3])
        ->whereNotIn('id',function($query){
            $query->select('deal_id')->from('sys_crm_deal_track_approval_invoice')->where('status',1);
         });
        
        if($from_month !="" && $to_month !=""){
            $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') >= '".date('Y-m-d', strtotime($from_month))."' and DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($to_month))."'");
        }
        elseif($from_month !="" && $to_month ==""){
            $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m-%d') = '".date('Y-m-d', strtotime($from_month))."'");
        }
        else{
            $data2->whereRaw("DATE_FORMAT(sys_crm_deals.estimated_close_date, '%Y-%m') = '".date('Y-m')."'");
        }
        $data = $data2->get();

        $retAmount=0;
        $dataid=[];
        foreach ($data as $id) {
            $dataid[]=$id->id;
        }

        $dt_query=SysCrmQuoteItems::select('qty','price','discount','brand','sm_items.part_number','deal_id','currency_id','sys_crm_quote_items.description')
            ->join('sm_items','sm_items.id','sys_crm_quote_items.product_id')
            ->wherein('deal_id',$dataid)->where('brand',$brand);             
            $dt=$dt_query->get();

            foreach ($dt as $val) {                    
                $ret = ($val->qty * $val->price) - ($val->qty * $val->discount);                    
                $retAmount += $ret;
            }

        return SysHelper::com_curr_format($retAmount, 2, '.', '');
        
        } catch (\Throwable $th) {
            return $th;
        }
    }
	public static function get_total_revenue_actual_all_by_brand($brand_id,$from_month,$to_month,$company){

        $brand=$brand_id;
        $data1=[];
        $data2=[];
        
            $data1 = DB::table('sys_crm_deals')->select('deal_value','deal_currency','source','cust_id','sys_crm_deals.id')->leftjoin('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_id','sys_crm_deals.id')->where('sys_crm_deals.is_partial_invoice',0);
            if($from_month !="" && $to_month !=""){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '".date('Y-m-d', strtotime($from_month))."' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($to_month))."'");
            }
            if($from_month !="" && $to_month ==""){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') = '".date('Y-m-d', strtotime($from_month))."'");
            }
            $data1->where('sys_crm_deals.stage',4)->where('sys_crm_deal_track_approval_invoice.status',1)->wherein('sys_crm_deals.company_id',$company);
        

        $retAmount=0.00;
            $dataA = $data1->get();
            $retAmount=0;
            $dataid=[];
        foreach ($dataA as $id) {
            $dataid[]=$id->id;
        }

        $dt_query=SysCrmQuoteItems::select('qty','price','discount','brand','sm_items.part_number','deal_id','currency_id','sys_crm_quote_items.description')
            ->join('sm_items','sm_items.id','sys_crm_quote_items.product_id')
            ->wherein('deal_id',$dataid)->where('brand',$brand);             
            $dt=$dt_query->get();

            foreach ($dt as $val) {                    
                $ret = ($val->qty * $val->price) - ($val->qty * $val->discount);                    
                $retAmount += $ret;
            }

        return SysHelper::com_curr_format($retAmount, 2, '.', '');
    }
	public static function get_total_on_process_all_by_brand($brand_id,$from_month,$to_month,$company){

        $brand=$brand_id;
        $data1=[];
        $data2=[];

        $data1 = DB::table('sys_crm_deals')->select('id','deal_value','deal_currency','source','cust_id')
        ->wherein('sys_crm_deals.company_id',$company)
        ->where('stage',4)
        ->whereNotIn('id',function($query) use($company){
            $query->select('deal_id')->from('sys_crm_deal_track_approval_invoice')->where('status',1)->wherein('company_id',$company);
         });
        
            if($from_month !="" && $to_month !=""){
                $data1->whereRaw("DATE_FORMAT(estimated_close_date, '%Y-%m-%d') >= '".date('Y-m-d', strtotime($from_month))."' and DATE_FORMAT(estimated_close_date, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($to_month))."'");
            }
            if($from_month !="" && $to_month ==""){
                $data1->whereRaw("DATE_FORMAT(estimated_close_date, '%Y-%m-%d') = '".date('Y-m-d', strtotime($from_month))."'");
            }
        

        $retAmount=0.00;
            $dataA = $data1->get();
            $dataid=[];
            foreach ($dataA as $id) {
                $dataid[]=$id->id;
            }
    
            $dt_query=SysCrmQuoteItems::select('qty','price','discount','brand','sm_items.part_number','deal_id','currency_id','sys_crm_quote_items.description')
                ->join('sm_items','sm_items.id','sys_crm_quote_items.product_id')
                ->wherein('deal_id',$dataid)->where('brand',$brand);             
                $dt=$dt_query->get();
    
                foreach ($dt as $val) {                    
                    $ret = ($val->qty * $val->price) - ($val->qty * $val->discount);                    
                    $retAmount += $ret;
                }

        return SysHelper::com_curr_format($retAmount, 2, '.', '');
    }
    public static function get_deal_count_by_brand($brand,$m1,$m2){
    $data1 = SysCrmDeals::select('sys_crm_deals.*','sys_crm_deal_track.invoice','sys_crm_deal_track.delivery','sys_crm_deal_track.receivables')
            ->leftjoin('sys_crm_deal_track','sys_crm_deal_track.deal_id','sys_crm_deals.id')
            ->leftjoin('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_id','sys_crm_deals.id')->where('stage',4);     
            if($m1 !="" && $m2 !=""){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') >= '".date('Y-m-d', strtotime($m1))."' and DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($m2))."'");
            }
            if($m1 !="" && $m2 ==""){
                $data1->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_invoice.created_at, '%Y-%m-%d') = '".date('Y-m-d', strtotime($m1))."'");
            }
            $data1->where('sys_crm_deals.stage',4)->where('sys_crm_deal_track_approval_invoice.status',1)->where('sys_crm_deals.company_id',session('logged_session_data.company_id'))->where('sys_crm_deals.is_partial_invoice',0);

            $dataA = $data1->get();
            $dataid=[];
            foreach ($dataA as $id) {
                $dataid[]=$id->id;
            }
    
            $dt_query=SysCrmQuoteItems::distinct('deal_id')
                ->join('sm_items','sm_items.id','sys_crm_quote_items.product_id')
                ->wherein('deal_id',$dataid)->where('brand',$brand)->count();
    

            return $dt_query;
    }


    //GEO
    
    public static function amc_set_completed()
    {
        try {
            $pending_amc_id = DB::table('sys_crm_amc_table')->where('status','!=',5)->where('company_id',session('logged_session_data.company_id'))->pluck('id');
            if(count($pending_amc_id)>0){
                foreach($pending_amc_id as $amc_id){
                    $data = DB::table('sys_crm_amc_table_service_scope_of_work')->where('amc_id',$amc_id)->where('status',1)->get();
                    if(count($data)>0){
                        
                    } else {
                        if(DB::table('sys_crm_amc_table_service_scope_of_work')->where('amc_id',$amc_id)->count()>0){

                            
                            DB::table('sys_crm_amc_table')->where('id',$amc_id)->update(['status' => 5]);
                        }
                    }
                }
            }            
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
    
    public static function get_amc_status($status)
    {
        if($status == 5){ return "<span class='text-success'>Completed</span>"; }
        else{ return "<span class='text-warning'>Pending</span>"; }
    }
    
    public static function get_ps_status($status)
    {
        if($status == 2){ return "<span class='text-success'>Completed</span>"; }
        else{ return "<span class='text-warning'>Pending</span>"; }
    }
    public static function get_pre_sales_status($status)
    {
        if($status == 3){ return "<span class='text-success'>Completed</span>"; }
        else if($status == 2){ return "<span class='text-info'>Added</span>"; }
        else if($status == 4){ return "<span class='text-dark'>Cancel</span>"; }
        else{ return "<span class='text-warning'>Pending</span>"; }
    }
    public static function get_pre_sales_status_engineer_page($status)
    {
        if($status == 3){ return "<span class='text-success'>Completed</span>"; }
        else{ return "<span class='text-warning'>Pending</span>"; }
    }

    public static function get_amc_period($start_date, $end_date)
    {
        try {
            $startDate = Carbon::parse($start_date);
            $endDate = Carbon::parse($end_date);
            //$startDate = Carbon::parse('2024-01-01');
            //$endDate = Carbon::parse('2024-03-31');//->month(+4);            
            $diffInMonths = $endDate->diffInMonths($startDate);
            
            if($diffInMonths < 2){
                return "Monthly";
            }
            if($diffInMonths >= 2 && $diffInMonths < 5){
                return "Quarterly";
            }
            if($diffInMonths >= 5 && $diffInMonths < 11){
                return "Half Yearly";
            }
            if($diffInMonths >= 11){
                return "Yearly";
            }                
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public static function get_engineer_list()
    {
        $com_id=session('logged_session_data.company_id');
        $role_id = Auth::user()->role_id;
        if($role_id == 1){
            $com = DB::table('sys_company')->pluck('id');
            $staff= SmStaff::select('user_id','full_name')->wherein('role_id',[31,33])->get();
        }
        elseif($role_id == 2){            
            $staff= SmStaff::select('user_id','full_name')->wherein('role_id',[31,33])->get();
        }
        elseif($role_id == 32){            
            $staff= SmStaff::select('user_id','full_name')->wherein('role_id',[31,33])->get();
        }
        else{
            $staff= SmStaff::select('user_id','full_name')->where('user_id',Auth::user()->id)->get();
        }
        return $staff;
    }

    // public static function get_due_date_sales_invoice($transaction_no,$transaction_date)
    // {
    //     try {
    //         $payment_terms_id = DB::table('sys_sales_invoice')->select('payment_terms')->where('doc_number',$transaction_no)->first();
    //         $days = DB::table('sys_payment_terms')->select('days_calculation','title')->where('id',$payment_terms_id->payment_terms)->first();
    //         $get_date = Carbon::parse($transaction_date)->addDays($days->days_calculation);
    //         $due_date = date('d/m/Y', strtotime(@$get_date));
    //         $payment_terms= $days->title;

    //         if($get_date>now()){
    //             $due_days =  now()->diffInDays($get_date);
    //         } else {
    //             $due_days =  0;
    //         }

    //         return [$due_date,$due_days,$payment_terms];
    //     } catch (\Throwable $th) {
    //         return ['','',''];
    //     }
    // }
    public static function get_due_date_sales_invoice($transaction_no, $transaction_date, $asOfDate = null)
    {
        try {
            $si = DB::table('sys_sales_invoice')->select('payment_terms', 'doc_date')->where('doc_number', $transaction_no)->first();
            if (!$si) {
                return ['', '', '', '', ''];
            }

            $term = DB::table('sys_payment_terms')->where('id', $si->payment_terms)->first();
            $invoiceDate = $si->doc_date ?? $transaction_date;
            $due_days = SysPaymentTerms::computePrimaryOverdueDays($invoiceDate, $term, $asOfDate);
            $due_date = Carbon::parse($invoiceDate)->addDays(SysPaymentTerms::resolveCreditDays($term))->format('d/m/Y');
            $col = SysPaymentTerms::legacyColFromOverdueDays($due_days);
            $asOf = SysPaymentTerms::resolveAsOfDate($asOfDate);
            $dates = (int) round(($asOf->timestamp - Carbon::parse($invoiceDate)->startOfDay()->timestamp) / 86400);

            return [$due_date, $due_days, $term->title ?? '', $col, $dates];
        } catch (\Throwable $th) {
            return ['', '', '', '', ''];
        }
    }
    public static function get_due_date_sales_invoice_report($sys_payment_terms_list,$payment_terms_id,$transaction_date,$receipt_date)
    {
        try {
            $days = $sys_payment_terms_list->where('id',$payment_terms_id)->first();
            $get_date = Carbon::parse($transaction_date)->addDays($days->days_calculation);
            $due_date = date('d/m/Y', strtotime(@$get_date));
            $payment_terms= $days->title;

            $due_days= round ( (strtotime($receipt_date)-strtotime($get_date)) /(3600*24) );

            return [$due_date,$due_days];
            
        } catch (\Throwable $th) {
            return $th;
            return ['','','','',''];
        }
    }
    
    public static function get_due_date_sales_invoice_dashboard($transaction_date,$payment_terms_id)
    {
        try {           
          
            $days = DB::table('sys_payment_terms')->select('days_calculation','title')->where('id',$payment_terms_id)->first();
            $get_date = Carbon::parse($transaction_date)->addDays($days->days_calculation);
            $due_date = date('d/m/Y', strtotime(@$get_date));
            $payment_terms= $days->title;
            
            $dates= round ( (strtotime("now")-strtotime( $transaction_date)) /(3600*24) );
            $col=0;

            $due_days= round ( (strtotime("now")-strtotime( $get_date)) /(3600*24) ); 
            
            if( $due_days < 0 ){
                $col=0;
            }
            if($due_days >=0 && $due_days <31){
                $col=1;
            }
            else if($due_days >=31 &&  $due_days<=60 ){
                $col=2;
            }
            else if($due_days >=61 &&  $due_days<=90){
                $col=3;
            }
            else if($due_days >90){
                $col=4;
            }                   
            return $col;
            //return [$due_date,$due_days,$payment_terms,$col,$dates];
        } catch (\Throwable $th) {
            return ['','','','',''];
        }
    }

    public static function get_due_date_purchase_invoice($transaction_no, $transaction_date, $asOfDate = null)
    {
        try {
            $pi = DB::table('sys_purchase_invoice')->select('payment_terms', 'pi_date')->where('doc_number', $transaction_no)->first();
            if (!$pi) {
                return ['', '', '', '', ''];
            }

            $term = DB::table('sys_payment_terms')->where('id', $pi->payment_terms)->first();
            $invoiceDate = $pi->pi_date ?? $transaction_date;
            $due_days = SysPaymentTerms::computePrimaryOverdueDays($invoiceDate, $term, $asOfDate);
            $due_date = Carbon::parse($invoiceDate)->addDays(SysPaymentTerms::resolveCreditDays($term))->format('d/m/Y');
            $col = SysPaymentTerms::legacyColFromOverdueDays($due_days);
            $asOf = SysPaymentTerms::resolveAsOfDate($asOfDate);
            $dates = (int) round(($asOf->timestamp - Carbon::parse($invoiceDate)->startOfDay()->timestamp) / 86400);

            return [$due_date, $due_days, $term->title ?? '', $col, $dates];
        } catch (\Throwable $th) {
            return ['', '', '', '', ''];
        }
    }

    public static function get_due_date_invoice_opbinvoice($transaction_no, $duedate, $paymentterms, $asOfDate = null)
    {
        try {
            $term = SysPaymentTerms::resolveByIdOrTitle($paymentterms);
            $invoiceDate = $duedate;
            if (!$invoiceDate) {
                return ['', '', '', '', ''];
            }

            $due_days = SysPaymentTerms::computePrimaryOverdueDays($invoiceDate, $term, $asOfDate);
            $due_date = Carbon::parse($invoiceDate)->addDays(SysPaymentTerms::resolveCreditDays($term))->format('d/m/Y');
            $col = SysPaymentTerms::legacyColFromOverdueDays($due_days);
            $asOf = SysPaymentTerms::resolveAsOfDate($asOfDate);
            $dates = (int) round(($asOf->timestamp - Carbon::parse($invoiceDate)->startOfDay()->timestamp) / 86400);
            $title = is_array($term) ? ($term['title'] ?? $paymentterms) : ($term->title ?? $paymentterms);

            return [$due_date, $due_days, $title, $col, $dates];
        } catch (\Throwable $th) {
            return ['', '', '', '', ''];
        }
    }

    public static function get_receivable_os_by_overdue($overdue, $account_id, $asOfDate = null)
    {
        try {
            if($overdue == "0") { //0
                $df = 0;
                $dt = 100000;
                $overdue_date_f = date("Y-m-d");
                $overdue_date_t = date("2050-01-01");
            }
            if($overdue == "30") { //0-30
                $df = 0;
                $dt = 30;
                $overdue_date_f = date("Y-m-d");
                $overdue_date_t = date("Y-m-d", strtotime('+ 30 days' , strtotime(date('Y-m-d'))));
            }
            if($overdue == "60") { //31-60
                $df = 31;
                $dt = 60;
                $overdue_date_f = date("Y-m-d", strtotime('+ 31 days' , strtotime(date('Y-m-d'))));
                $overdue_date_t = date("Y-m-d", strtotime('+ 60 days' , strtotime(date('Y-m-d'))));
            }
            if($overdue == "90") { //61-90
                $df = 61;
                $dt = 90;
                $overdue_date_f = date("Y-m-d", strtotime('+ 61 days' , strtotime(date('Y-m-d'))));
                $overdue_date_t = date("Y-m-d", strtotime('+ 90 days' , strtotime(date('Y-m-d'))));
            
            }            
            if($overdue == "90+") { //>90
                $df = 91;
                $dt = 100000;
                $overdue_date_f = date("Y-m-d", strtotime('+ 91 days' , strtotime(date('Y-m-d'))));
                $overdue_date_t = date("2050-01-01");                
            }

            $list = DB::table('sys_sales_invoice as si')
                ->select('si.doc_number', 'si.doc_date', 'pt.payment_schedule', 'pt.days_calculation', 'pt.title')
                ->join('sys_payment_terms as pt', 'pt.id', 'si.payment_terms')
                ->where('si.company_id', session('logged_session_data.company_id'))
                ->where('customer', $account_id)
                ->get();

            $retlist = [];

            if (count($list) > 0) {
                foreach ($list as $li) {
                    $breakdown = SysPaymentTerms::buildOutstandingBreakdown($li->doc_date, 1, $li, 0, $asOfDate);
                    if (SysPaymentTerms::invoiceMatchesOverdueFilter($breakdown['installments'], $overdue)) {
                        $retlist[] = $li->doc_number;
                    }
                }
                return $retlist;
            }

            return [];
            
        } catch (\Throwable $th) {
            return [];
        }
    }

    public static function get_receivable_os_by_ageing($ageing, $account_id, $asOfDate = null)
    {
        try {
            if($ageing == "0") { //0-30
                $df = 0;
                $dt = 30;
                $overdue_date_f = date("Y-m-d");
                $overdue_date_t = date("Y-m-d", strtotime('+ 30 days' , strtotime(date('Y-m-d'))));
            }
            if($ageing == "30") { //31-60
                $df = 31;
                $dt = 60;
                $overdue_date_f = date("Y-m-d", strtotime('+ 31 days' , strtotime(date('Y-m-d'))));
                $overdue_date_t = date("Y-m-d", strtotime('+ 60 days' , strtotime(date('Y-m-d'))));
            }
            if($ageing == "60") { //61-90
                $df = 61;
                $dt = 90;
                $overdue_date_f = date("Y-m-d", strtotime('+ 61 days' , strtotime(date('Y-m-d'))));
                $overdue_date_t = date("Y-m-d", strtotime('+ 90 days' , strtotime(date('Y-m-d'))));
            
            }
            if($ageing == "90+") { //>90
                $df = 91;
                $dt = 100000;
                $overdue_date_f = date("Y-m-d", strtotime('+ 91 days' , strtotime(date('Y-m-d'))));
                $overdue_date_t = date("2050-01-01");                
            }

            $list = DB::table('sys_sales_invoice as si')
                ->select('si.doc_number', 'si.doc_date', 'pt.payment_schedule', 'pt.days_calculation', 'pt.title')
                ->join('sys_payment_terms as pt', 'pt.id', 'si.payment_terms')
                ->where('si.company_id', session('logged_session_data.company_id'))
                ->where('customer', $account_id)
                ->get();
            $retlist = [];

            if (count($list) > 0) {
                foreach ($list as $li) {
                    $breakdown = SysPaymentTerms::buildOutstandingBreakdown($li->doc_date, 1, $li, 0, $asOfDate);
                    if (SysPaymentTerms::invoiceMatchesAgeingFilter($breakdown['installments'], $ageing)) {
                        $retlist[] = $li->doc_number;
                    }
                }
                return $retlist;
            }
            return [];
            
        } catch (\Throwable $th) {
            return [];
        }
    }

    public static function get_payable_os_by_overdue($overdue, $account_id, $asOfDate = null)
    {
        try {
            $companyId = session('logged_session_data.company_id');
            $purchaseList = DB::table('sys_purchase_invoice as pi')
                ->select('pi.doc_number', 'pi.pi_date', 'pt.payment_schedule', 'pt.days_calculation', 'pt.title')
                ->join('sys_payment_terms as pt', 'pt.id', 'pi.payment_terms')
                ->where('pi.company_id', $companyId)
                ->where('vendors', $account_id)
                ->get();

            $salesCreditList = DB::table('sys_sales_invoice as si')
                ->select('si.doc_number', 'si.doc_date as pi_date', 'pt.payment_schedule', 'pt.days_calculation', 'pt.title')
                ->join('sys_payment_terms as pt', 'pt.id', 'si.payment_terms')
                ->join('sys_chartofaccounts_transaction as cat', function ($join) use ($account_id, $companyId) {
                    $join->on('cat.transaction_no', '=', 'si.doc_number')
                        ->where('cat.transaction_type', 'salesinvoice')
                        ->where('cat.account_id', $account_id)
                        ->where('cat.company_id', $companyId)
                        ->where('cat.status', 1);
                })
                ->where('si.company_id', $companyId)
                ->where('si.status', 1)
                ->groupBy('si.doc_number', 'si.doc_date', 'pt.payment_schedule', 'pt.days_calculation', 'pt.title')
                ->get();

            $list = $purchaseList->merge($salesCreditList);

            $retlist = [];

            if (count($list) > 0) {
                foreach ($list as $li) {
                    $breakdown = SysPaymentTerms::buildOutstandingBreakdown($li->pi_date, 1, $li, 0, $asOfDate);
                    if (SysPaymentTerms::invoiceMatchesOverdueFilter($breakdown['installments'], $overdue)) {
                        $retlist[] = $li->doc_number;
                    }
                }

                return $retlist;
            }

            return [];
        } catch (\Throwable $th) {
            return [];
        }
    }

    public static function get_payable_os_by_ageing($ageing, $account_id, $asOfDate = null)
    {
        try {
            $companyId = session('logged_session_data.company_id');
            $purchaseList = DB::table('sys_purchase_invoice as pi')
                ->select('pi.doc_number', 'pi.pi_date', 'pt.payment_schedule', 'pt.days_calculation', 'pt.title')
                ->join('sys_payment_terms as pt', 'pt.id', 'pi.payment_terms')
                ->where('pi.company_id', $companyId)
                ->where('vendors', $account_id)
                ->get();

            $salesCreditList = DB::table('sys_sales_invoice as si')
                ->select('si.doc_number', 'si.doc_date as pi_date', 'pt.payment_schedule', 'pt.days_calculation', 'pt.title')
                ->join('sys_payment_terms as pt', 'pt.id', 'si.payment_terms')
                ->join('sys_chartofaccounts_transaction as cat', function ($join) use ($account_id, $companyId) {
                    $join->on('cat.transaction_no', '=', 'si.doc_number')
                        ->where('cat.transaction_type', 'salesinvoice')
                        ->where('cat.account_id', $account_id)
                        ->where('cat.company_id', $companyId)
                        ->where('cat.status', 1);
                })
                ->where('si.company_id', $companyId)
                ->where('si.status', 1)
                ->groupBy('si.doc_number', 'si.doc_date', 'pt.payment_schedule', 'pt.days_calculation', 'pt.title')
                ->get();

            $list = $purchaseList->merge($salesCreditList);

            $retlist = [];

            if (count($list) > 0) {
                foreach ($list as $li) {
                    $breakdown = SysPaymentTerms::buildOutstandingBreakdown($li->pi_date, 1, $li, 0, $asOfDate);
                    if (SysPaymentTerms::invoiceMatchesAgeingFilter($breakdown['installments'], $ageing)) {
                        $retlist[] = $li->doc_number;
                    }
                }

                return $retlist;
            }

            return [];
        } catch (\Throwable $th) {
            return [];
        }
    }
    
    public static function get_cash_supplier($account_id){
        try {
            $list = DB::table('sys_sales_invoice as si')->select('si.doc_number')
            ->join('sys_sales_invoice_cf_charges as cf','cf.si_id','si.id')
            ->where('si.company_id',session('logged_session_data.company_id'))
            ->where("cfc_credit_account",$account_id)->pluck('doc_number');
            return $list;
            
        } catch (\Throwable $th) {
            return $th;
        }
    }
    
    //added by kunal
    public static function humanTimeDiff($startTime, $endTime = null)
    {
        if (!$startTime) return 'N/A';

        try {
            $start = Carbon::parse($startTime)->startOfMinute();
            $end = $endTime ? Carbon::parse($endTime)->startOfMinute() : Carbon::now()->startOfMinute();

            // Ensure $end is after $start
            if ($end < $start) {
                list($start, $end) = [$end, $start];
            }

            $diff = $start->diff($end);

            $parts = [];

            if ($diff->d > 0) {
                $parts[] = $diff->d . ' day' . ($diff->d > 1 ? 's' : '');
            }

            if ($diff->h > 0) {
                $parts[] = $diff->h . ' hr' . ($diff->h > 1 ? 's' : '');
            }

            if ($diff->i > 0) {
                $parts[] = $diff->i . ' min' . ($diff->i > 1 ? 's' : '');
            }

            return count($parts) ? implode(' ', $parts) : '0 mins';
        } catch (\Exception $e) {
            return 'Invalid date';
        }
    }
    public static function applyDateFilters($query, $ctrl_date_from, $ctrl_date_to, $table)
    {
        $createdAtColumn = "$table.created_date";

        if (!empty($ctrl_date_from) && !empty($ctrl_date_to)) {
            $query->whereRaw("
            DATE_FORMAT($createdAtColumn, '%Y-%m-%d') BETWEEN ? AND ?
        ", [date('Y-m-d', strtotime($ctrl_date_from)), date('Y-m-d', strtotime($ctrl_date_to))]);

        } elseif (!empty($ctrl_date_from)) {
            $query->whereDate($createdAtColumn, '>=', $ctrl_date_from);
        } elseif (!empty($ctrl_date_to)) {
            $query->whereDate($createdAtColumn, '<=', $ctrl_date_to);
        }

        return $query;
    }
    public static function applyDateFiltersBase($query, $ctrl_date_from, $ctrl_date_to, $relation)
    {


        if (!empty($ctrl_date_from) && !empty($ctrl_date_to)) {
            $query->whereHas($relation, function ($q) use ($ctrl_date_from, $ctrl_date_to) {
                $q->whereRaw("
            DATE_FORMAT(created_date, '%Y-%m-%d') BETWEEN ? AND ?
        ", [date('Y-m-d', strtotime($ctrl_date_from)), date('Y-m-d', strtotime($ctrl_date_to))]);
            });

        } elseif (!empty($ctrl_date_from)) {
            $query->whereHas($relation, function ($q) use ($ctrl_date_from, $ctrl_date_to) {
                $q->whereDate("created_date", '>=', $ctrl_date_from);
            });

        } elseif (!empty($ctrl_date_to)) {
            $query->whereHas($relation, function ($q) use ($ctrl_date_from, $ctrl_date_to) {
                $q->whereDate("created_date", '<=', $ctrl_date_to);
            });

        }

        return $query;
    }

    public function getAMCEngAndRequestCount($cust_name)
    {


        try {
            $companyId = session('logged_session_data.company_id');

            $amc_count = DB::table('sys_crm_amc_table')->where('cust_name', $cust_name)->where('company_id', $companyId)->wherein('sys_crm_amc_table.status', [2, 3, 5])->where('is_auto', 0)->count();



            // Join with service_request and get service_engineer fields
            $engineersRaw = DB::table('sys_crm_amc_table as amc')
                ->join('sys_crm_amc_table_service_request as ser', 'ser.amc_id', '=', 'amc.id')
                ->where('amc.cust_name', $cust_name)
                ->where('amc.company_id', $companyId)
                ->where('amc.is_auto', 0)
                ->select('ser.service_engineer')
                ->get();

            // 3. Collect all engineer IDs into one array
            $engineerIds = [];

            foreach ($engineersRaw as $row) {
                if (!empty($row->service_engineer)) {
                    $ids = explode(',', $row->service_engineer);
                    $engineerIds = array_merge($engineerIds, $ids);
                }
            }

            $uniqueEngineers = array_unique(array_filter($engineerIds));
            $eng_count = count($uniqueEngineers);

            return [
                'amc_count' => $amc_count,
                'eng_count' => $eng_count,
            ];




        } catch (\Throwable $th) {
            return ['amc_count' => 0, 'eng_count' => 0];
        }
    }
    
    public function getGRNID($grn_no)
    {
        try {
            return SysPurchaseGRN::where('doc_number', $grn_no)
                ->where('company_id', session('logged_session_data.company_id'))
                ->value('id');
        } catch (\Throwable $th) {
            return null;
        }
    }

    public function getPurchaseIvoiceID($pi_no)
    {
        try {
            return SysPurchaseInvoice::where('doc_number', $pi_no)
                ->where('company_id', session('logged_session_data.company_id'))
                ->value('id');
        } catch (\Throwable $th) {
            return null;
        }
    }

     public function getPurchaseOrderID($po_no)
    {
        try {
            return SysPurchaseOrder::where('doc_number', $po_no)
                ->where('company_id', session('logged_session_data.company_id'))
                ->value('id');
        } catch (\Throwable $th) {
            return null;
        }
    }


    public static function get_new_code_lead($table_name,$code,$colum,$company){
        $code2 = DB::table('sys_company')->select('other_code')->where('id',$company)->max('other_code');
        $results =  DB::table($table_name)->where($colum,'like',$code.$code2.'-%')->where('company_id',$company)->max($colum);
        if($results=="") {
            return $code.$code2."-1001";
        } else {
            $ret1 = preg_replace('~\D~', '', $results);
            $ret2 = sprintf('%03d',$ret1+1);
            return $code.$code2.'-'.$ret2;
        }
    }

     function isActiveRoute($patterns, $output = 'active') {
        foreach ((array) $patterns as $pattern) {
            if (\Request::is($pattern) || \Request::is($pattern . '/*')) return $output;
        }
        return '';
    }

    function isMenuOpen($patterns, $output = 'active') {
        foreach ((array) $patterns as $pattern) {
            if (\Request::is($pattern) || \Request::is($pattern . '/*')) return $output;
        }
        return '';
    }

        public static function get_dealid_from_code_without_company($deal_code){
        $deal_id =  DB::table('sys_crm_deals')->where('code',$deal_code)->max('id');
        if($deal_id=="") {
            return "0";
        } else {
            return $deal_id;
        }
    }

     public static function get_code_from_dealid_without_company($deal_id){
        $deal_id =  DB::table('sys_crm_deals')->where('id',$deal_id)->max('code');
        if($deal_id=="") {
            return "Without Deal";
        } else {
            return $deal_id;
        }
    }

       public static function get_reserved_qty($stock_id,$part_no, $company_id = null)
    {
        try {
            // Use session company_id if not provided
            if ($company_id === null) {
                $company_id = session('logged_session_data.company_id');
            }

            // dd($stock_id, $part_no, $company_id);

            $reserved_qty = DB::table('sys_reserve_stock as reserve')
                ->select(DB::raw('COALESCE(SUM(reserve.reserve_qty), 0) as total_reserved'))
                ->where('reserve.stock_id', $stock_id)
                ->where('reserve.company_id', $company_id)
                ->whereNull('reserve.deleted_at')
                ->where('reserve.reserve_date', '>=', DB::raw('CURDATE()')) // Only active reservations
                ->first();


           
                

            return $reserved_qty ? $reserved_qty->total_reserved : 0;
        } catch (\Throwable $th) {
            return 0;
        }
    }

        public static function get_supplierlist_charofaccounts()
    {
        try {
            $com_id = session('logged_session_data.company_id');
            $customer = SysChartofAccounts::select('sys_chartofaccounts.id', 'sys_chartofaccounts.account_name', 'sys_chartofaccounts.account_code')
                ->where('sys_chartofaccounts.subgroup2', 19)
                ->where('sys_chartofaccounts.status', 1)
                ->whereRaw("find_in_set(?, sys_chartofaccounts.company_access)", [$com_id])
                ->orderBy('sys_chartofaccounts.account_name', 'asc')
                ->get();

               
            return $customer;
        } catch (\Throwable $th) {
            return [];
        }
    }


    public static function getCompanyCodeSettings($companyId = null)
    {
        // If you have session-based company
        if (!$companyId && session()->has('logged_session_data.company_id')) {
            $companyId = session('logged_session_data.company_id');
        }

        $company = SysCompany::find($companyId);

        if (!$company) return ['is_customer_code' => 0, 'is_supplier_code' => 0];

        return [
            'is_customer_code' => (bool) $company->is_customer_code,
            'is_supplier_code' => (bool) $company->is_supplier_code,
            'is_account_code' => (bool) $company->is_account_code,
            'is_subaccount_code' => (bool) $company->is_subaccount_code,
        ];
    }

    public static function notify($data = [])
    {
        try {

          

        $notification = SystemNotification::create([
            'user_id' => $data['user_id'] ?? null,
            'role' => $data['role'] ?? null,
            'type' => $data['type'],
            'record_id' => $data['record_id'],

            'deal_id' => $data['deal_id'] ?? null,
            'company_id' => $data['company_id'] ?? null,
            'customer_name' => $data['customer_name'] ?? null,
            'sales_person' => $data['sales_person'] ?? null,
            'submitted_time' => $data['submitted_time'] ?? null,
            'value' => $data['value'] ?? null,

            'title' => $data['title'],
            'message' => $data['message'],

            'is_shown' => 0,
            'is_resolved' => 0,

            'is_account_rejected' => $data['is_account_rejected'] ?? 0,
            'is_sales_rejected' => $data['is_sales_rejected'] ?? 0,
            'is_purchase_rejected' => $data['is_purchase_rejected'] ?? 0,
            'is_invoice_rejected' => $data['is_invoice_rejected'] ?? 0,
            'is_delivery_rejected' => $data['is_delivery_rejected'] ?? 0,
            'is_receivables_rejected' => $data['is_receivables_rejected'] ?? 0,

        ]);

     
        // ✅ return the created notification record
        return $notification;

        } catch (\Throwable $e) {

            // ✅ Return false so your main flow continues
            return false;
        }
    }
    public static function getPartNumberById($id)
    {
        $item = DB::table('sm_items')->where('id', $id)->first();

        return $item ? $item->part_number : null;
    }

   public static function logStaffActivity($message, $type, $docNumber = null)
{
    StaffActivity::create([
        'user_id'    => Auth::id(),
        'doc_number' => $docNumber,
        'type'       => $type,
        'message'    => $message,
    ]);
}

 public static function getPartNumberDataByID($id)
    {
        $item = DB::table('sm_items')->where('id', $id)->first();

        return $item ? $item : null;
    }

    public static function CreateCompanyBank($account_id)
    {

    $bank = SysChartofAccounts::where('id', $account_id)->first();

     $banking = new SysCompanyBanking();
                $banking->company_id = session('logged_session_data.company_id');
                $banking->bank_name =  $bank->bank_name ?? '';
                $banking->account_name =  $bank->account_name ?? null;
                $banking->branch_name =  $bank->branch ?? null;
                $banking->account_number = $bank->acc_no ?? null;
                $banking->iban_number = $bank->iban ?? null;
                $banking->swift_code = $bank->swift_code ?? null;
            $banking->save();

        return $banking->id;
    }

      public static function UpdateCompanyBank($account_id)
    {

    $bank = SysChartofAccounts::where('id', $account_id)->first();

    if(!$bank->company_bank_id){
        return self::CreateCompanyBank($account_id);
    }

        $banking = SysCompanyBanking::where('id',$bank->company_bank_id)->first();
                    $banking->bank_name =  $bank->bank_name ?? '';
                    $banking->account_name =  $bank->account_name ?? null;
                    $banking->branch_name =  $bank->branch ?? null;
                    $banking->account_number = $bank->acc_no ?? null;
                    $banking->iban_number = $bank->iban ?? null;
                    $banking->swift_code = $bank->swift_code ?? null;
                $banking->save();

        return $banking->id;
    }

     public static function getPurchaseOrderCode($po_id)
    {
        try {
            return SysPurchaseOrder::where('id', $po_id)
                ->where('company_id', session('logged_session_data.company_id'))
                ->value('doc_number');
        } catch (\Throwable $th) {
            return null;
        }
    }


    public static function getCompanyWarehouses($company_id = null)
    {
        try {
            // Use session company_id if not provided
            if ($company_id === null) {
                $company_id = session('logged_session_data.company_id');
            }

            $warehouses = DB::table('company_warehouses')
                ->where('company_id', $company_id)
                ->get();

            return $warehouses;
        } catch (\Throwable $th) {
            return [];
        }
    }

    public static function getSuppliersVC($company_id = null)
    {
       $com_id = $company_id ?? session('logged_session_data.company_id');

            $vendors_query = SysCustSuppl::select(
                    'id',
                    'code',
                    'name',
                    'customer_name_display',
                    'taken_from_stock',
                    'stock_order'
                )
                ->where('company_id', $com_id)
                ->where(function ($query) {
                    $query->where(function ($q) {
                        $q->where('code', 'LIKE', 'CUS%')
                        ->whereIn('account_type', [1, 2, 3]);
                    })
                    ->orWhere(function ($q) {
                        $q->where('code', 'LIKE', 'SUP%')
                        ->whereIn('account_type', [2,3]);
                    });
                });

            $vendors = $vendors_query
                ->whereIn('status', [1, 3])
                ->orderBy('name', 'asc')
                ->get();

            return $vendors;

    }

    public static function getNextDealQuoteDocNo()
    {
        try {
            return DB::transaction(function () {

                $companyId = session('logged_session_data.company_id');

                // 1. Get company code
                $companyCode = DB::table('sys_company')
                    ->where('id', $companyId)
                    ->value('other_code');

                if (!$companyCode) {
                    throw new \Exception('Company code not found');
                }

                // 2. Lock rows to avoid duplicates
                $lastNumber = DB::table('sys_crm_quote_items')
                    ->where('company_id', $companyId)
                    ->where('document_number', 'like', 'QN' . $companyCode . '-%')
                    ->lockForUpdate()
                 
                      ->selectRaw("
                    MAX(
                        CAST(
                            SUBSTRING_INDEX(
                                SUBSTRING_INDEX(document_number, '-', 2),
                                '-',
                                -1
                            ) AS UNSIGNED
                        )
                    ) as max_no
                ")
                    ->value('max_no');

                // 3. Start from 1001
                $nextNumber = $lastNumber ? $lastNumber + 1 : 1001;

                // 4. Return formatted document number
                return 'QN' . $companyCode . '-' . $nextNumber;
            });

        } catch (\Throwable $e) {
            return null;
        }
    }

    public static function getQuoteDocNoByDeal($deal_id, $quote_id){
        try {
            $doc = DB::table('sys_crm_quote_items')
                ->where('deal_id', $deal_id)
                ->where('quote_id', $quote_id)
                ->first();

            return $doc ? $doc->document_number : null;

        } catch (\Throwable $e) {
            return null;
        }
    }


     public static function get_only_sales_persons()
    {
       
        $com_id=session('logged_session_data.company_id');
        if(Auth::user()->role_id == 5){
            $staffs = SmStaff::select('user_id','full_name')->where('active_status', '=', '1')
            ->wherein('user_id',[Auth::user()->id])
            ->whereRaw("find_in_set($com_id,company_access)")->orderby('full_name','asc')->get();
        } else {
            if($com_id==1){
                $staffs = SmStaff::select('user_id','full_name')->where('active_status', '=', '1')
                ->wherein('role_id',[5,8,33])
                ->orderby('full_name','asc')->get();
            } else {
                $staffs = SmStaff::select('user_id','full_name')->where('active_status', '=', '1')
                ->wherein('role_id',[5,8,33])
                ->whereRaw("find_in_set($com_id,company_access)")->orderby('full_name','asc')->get();
            }
        }
        return $staffs;
    }


    public static function get_product_description($id = null, $part_number = null)
    {
        if ($id) {
            $item = SmItem::select('description')->where('id', $id)->where('status', 1)->first();
            if ($item && $item->description) {
                return $item->description;
            }
        }

        if ($part_number) {
            $item = SmItem::select('description')->where('part_number', $part_number)->where('status', 1)->first();
            if ($item && $item->description) {
                return $item->description;
            }
        }

        return '';
    }
  

    public function getDepartmentByName($department){
        try {
            $normalized = strtolower(str_replace(' ', '', trim($department)));
            $dept = DB::table('sm_human_departments')
                ->whereRaw("REPLACE(LOWER(name), ' ', '') = ?", [$normalized])
                ->first();
            return $dept ? $dept->id : null;
        } catch (\Throwable $th) {
            return null;
        }

    }

    public function getDesignationByName($designation){
        try {
            $normalized = strtolower(str_replace(' ', '', trim($designation)));
            $desig = DB::table('sm_designations')
                ->whereRaw("REPLACE(LOWER(title), ' ', '') = ?", [$normalized])
                ->first();
            return $desig ? $desig->id : null;
        } catch (\Throwable $th) {
            return null;
        }
    }

    public static function getDepartmentID($department){
        try {
            $normalized = strtolower(str_replace(' ', '', trim($department)));
            $dept = DB::table('sm_human_departments')
                ->whereRaw("REPLACE(LOWER(name), ' ', '') = ?", [$normalized])
                ->first();
            return $dept ? $dept->id : null;
        } catch (\Throwable $th) {
            return null;
        }

    }


}
    




?>

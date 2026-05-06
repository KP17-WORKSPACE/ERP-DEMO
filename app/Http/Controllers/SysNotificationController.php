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
use App\SysCompany;
use App\SysCountries;
use App\SysCrmDeals;
use App\SysCrmDealTrack;
use App\SysCrmDealTrackApprovalPurchease;
use App\SysCrmDealTrackApprovalReceivables;
use App\SysCrmLeads;
use App\SysCrmLeadsComments;
use App\SysCrmSalesTarget;
use App\SysCurrencySettings;
use App\SysCustSuppl;
use App\SysHelper;
use App\SysItemOpeningStock;
use App\SysItemStock;
use App\SysNotifications;
use App\SysPaymentTerms;
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

class SysNotificationController extends Controller
{
    public function __construct(){
        $this->middleware('PM');
    }
    
    public function notificationread(Request $request)
    {
        $input = $request->all();        
        try{
            DB::table('sys_notifications')->where('received_id',$request->id)
                    ->update([
                        'is_read' => 1,
                    ]);
            $bug = 0;
        }
        
        catch(\Exception $e){
            return $e;
            $bug = $e->errorInfo[1];
        }
        if($bug==0){
            $retData='OK';
            return json_encode(array('data'=>$retData));
        }else {
            $retData='ERROR';
            return json_encode(array('data'=>$retData));
        }
    }
    public function notificationreadone(Request $request)
    {
        $input = $request->all();        
        try{
            DB::table('sys_notifications')->where('id',$request->id)
                    ->update([
                        'is_read' => 1,
                    ]);
            $bug = 0;
        }
        
        catch(\Exception $e){
            return $e;
            $bug = $e->errorInfo[1];
        }
        if($bug==0){
            $retData='OK';
            return json_encode(array('data'=>$retData));
        }else {
            $retData='ERROR';
            return json_encode(array('data'=>$retData));
        }
    }
}

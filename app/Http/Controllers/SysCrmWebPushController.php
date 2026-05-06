<?php

namespace App\Http\Controllers;

use App\ApiBaseMethod;
use App\Models\PushSubscription;
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

class SysCrmWebPushController extends Controller
{
    public function __construct(){
        $this->middleware('PM');
    }

    public function pushsubscribe(Request $request,$id) {
        try {

            $check = PushSubscription::where('data',$request->getContent())->where('user_id',$id)->count();
            if($check==0){
                PushSubscription::create([
                    'data' => $request->getContent(),
                    'user_id' => $id
                ]);
            }
        } catch (\Throwable $th) {
            return $th;
        }
    }
}
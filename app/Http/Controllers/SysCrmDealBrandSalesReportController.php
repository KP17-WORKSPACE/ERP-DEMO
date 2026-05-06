<?php

namespace App\Http\Controllers;

use App\ApiBaseMethod;
use App\SmInspectingDepartment;
use App\SmItem;
use Illuminate\Http\Request;
use App\SmItemStore;
use App\SmStaff;
use App\SysBrand;
use App\SysChartofAccounts;
use App\SysCompany;
use App\SysCountries;
use App\SysCrmDeals;
use App\SysCrmDealsCollaboration;
use App\SysCrmDealsComments;
use App\SysCrmDealTrack;
use App\SysCrmLeads;
use App\SysCrmQuoteCSItems;
use App\SysCrmQuoteItems;
use App\SysCrmSalesTarget;
use App\SysCurrencySettings;
use App\SysCustSuppl;
use App\SysCustSupplAddressbook;
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

class SysCrmDealBrandSalesReportController extends Controller
{
    public function __construct(){
        $this->middleware('PM');
    }

    public function brandsalesreport(Request $request)
    {
        try {

            if($_POST){
                
                $ctrl_part_number = $request->part_number;
                $ctrl_brand = $request->brand_id;
                $ctrl_date = $request->date;
                $ctrl_date2 = $request->date2;
                $data = SysCrmDealBrandSalesReportController::get_report($ctrl_brand,$ctrl_part_number,$ctrl_date,$ctrl_date2);
            }
            else{            
                $ctrl_part_number='';
                $ctrl_brand='';
                $ctrl_date=date('Y-m-01');
                $ctrl_date2=date('Y-m-d');
                $data=[];
            }

            $brand = SysBrand::select('id','title')->orderby('title','asc')->get();
            return view('backEnd.crm.DealBrandSaleReport', compact('data','brand','ctrl_part_number','ctrl_brand','ctrl_date','ctrl_date2'));            

        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function get_report($brand,$partno,$datefrom,$dateto)
    {
        try {
                
            $ret=0;
            $retData=[];
            $data_query = DB::table('sys_crm_deals')->select('id')
            ->where('sys_crm_deals.stage',4)
            ->whereRaw("DATE_FORMAT(sys_crm_deals.created_at, '%Y-%m-%d') >= '".$datefrom."'")
            ->whereRaw("DATE_FORMAT(sys_crm_deals.created_at, '%Y-%m-%d') <= '".$dateto."'");
            //->where('sys_crm_deal_track.invoice',1)
            $data = $data_query->get();
            if(count($data)>0){
                foreach ($data as $id) {
                    $dataid[]=$id->id;
                }
                $dt_query=SysCrmQuoteItems::select('qty','price','discount','brand','sm_items.part_number','deal_id','currency_id','sys_crm_quote_items.description')
                ->join('sm_items','sm_items.id','sys_crm_quote_items.product_id')
                ->wherein('deal_id',$dataid)->where('brand',$brand);
                if($partno != ''){
                    $dt_query->where('sm_items.part_number', 'like', '%'.$partno.'%');
                }                
                $dt=$dt_query->get();

                foreach ($dt as $val) {                    
                    $ret = ($val->qty * $val->price) - ($val->qty * $val->discount);                    
                    $amt = SysHelper::get_aed_amount($val->currency_id,$ret);
                    $retData[]=[
                        'pno' => $val->part_number,
                        'amount' => SysHelper::com_curr_format($amt, 2, '.', ''),
                        'deal_id' => $val->deal_id,
                        'description' => $val->description,
                        'qty' => $val->qty,
                    ];
                }
            }
            return $retData;
            
        } catch (\Throwable $th) {
            return $th;
        }
    }


}
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
use App\SysCrmDealsComments;
use App\SysCrmDealTrack;
use App\SysCrmDealTrackApprovalAccounts;
use App\SysCrmDealTrackApprovalDelivery;
use App\SysCrmDealTrackApprovalInvoice;
use App\SysCrmDealTrackApprovalPurchease;
use App\SysCrmDealTrackApprovalPurcheaseGrn;
use App\SysCrmDealTrackApprovalReceivables;
use App\SysCrmDealTrackApprovalSales;
use App\SysCrmDealTrackApprovalTechnical;
use App\SysCrmQuoteItems;
use App\SysCurrencySettings;
use App\SysCustSuppl;
use App\SysCustSupplAddressbook;
use App\SysDriver;
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

class SysCrmDealTrackGRNController extends Controller
{
    public function __construct(){
        $this->middleware('PM');
    }
    
    public function crmdealtrackgrn($id)
    {
        try{
            $grn = SysCrmDealTrackApprovalPurcheaseGrn::find($id);
            $deal = SysCrmDeals::where('id',$grn->deal_id)->first();
            $dealtrack = SysCrmDealTrack::where('id',$grn->deal_track_id)->first();
            $quoteitems = SysCrmQuoteItems::where('deal_id',$grn->deal_id)->where('quote_id',$deal->quote_id)->get();

            return view('backEnd.crm.DealTrackGrn', compact('grn','deal','dealtrack','quoteitems'));
        }catch (\Exception $e) {
            return $e;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function crmdealtrackgrnupdate(Request $request, $id)
    {
        $trn_time = Carbon::now('+04:00')->format('Y-m-d H:i:s');
        try {

            for($i = 1; $i <= $request->roid; $i++) {

                $chk = 'chk_'.$i;
                $txtqty = 'txtqty_'.$i;
                $txtsupplier = 'txtsupplier_'.$i;
                $txtdate = 'txtdate_'.$i;
                $partno = 'partno_'.$i;

                //return $request->$txtdate;

                if($request->$chk == "on")
                {
                    $values = array('grn_id' => $id,'partnumber' => $request->$partno,'supplier' => $request->$txtsupplier, 'expected_date' => $request->$txtdate, 'qty' => $request->$txtqty, 'status' => 1);
                    DB::table('sys_crm_deal_track_approval_purchease_grn_list')->insert($values);
                }
            }

            DB::table('sys_crm_deal_track_approval_purchease_grn')->where('id',$id)->update(['status' => $request->status]);

            if( $request->status==1){
                Toastr::success('Updated successfully', 'Approved');}
            else if ( $request->status==2){
                Toastr::error('Updated successfully', 'Disapproved');}
            else if ( $request->status==3){
                Toastr::success('Updated successfully', 'Partial Approved');}
            else{
                Toastr::warning('Updated successfully', 'Pending');}

            return redirect()->back(); 
        } catch (\Throwable $th) {
            return $th;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function crmdealtrackgrnnoupdate(Request $request)
    {
        $trn_time = Carbon::now('+04:00')->format('Y-m-d H:i:s');
        try {

            DB::table('sys_crm_deal_track_approval_purchease_grn')->where('id', $request->grn_id)->update(['status' => 1, 'grn_no' => $request->grn_no]);
            Toastr::success('GRN No Updated successfully', 'GRN No Updated');
            return redirect()->back();

        } catch (\Throwable $th) {
            return $th;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function crmdealsdeliveryupdateitems(Request $request)
    {
        try {
            foreach($request->checkbx as $chk)
            {
                $a='qty_'.$chk;
                $quote_item_id = $chk;
                $qty = $request->$a;

                $values = array('deal_id' => $request->update_item_deal_id,'quote_item_id' => $quote_item_id,'qty' => $qty, 'updated_on' => Carbon::now('+04:00')->format('Y-m-d H:i:00'));
                $chk = DB::table('sys_crm_deal_delivery_items')->where($values)->count();
                if($chk==0){
                    DB::table('sys_crm_deal_delivery_items')->insert($values);
                }
            }
            Toastr::success('Item QTY Updated Successfully', 'Success');
            return redirect()->back(); 
        
        } catch (\Throwable $th) {
            //return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }

    }

}
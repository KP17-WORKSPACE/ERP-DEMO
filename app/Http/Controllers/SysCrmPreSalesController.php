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
use App\SysCrmService;
use App\SysCrmServiceAssign;
use App\SysCrmServiceComments;
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

class SysCrmPreSalesController extends Controller
{
    public function __construct(){
        $this->middleware('PM');
    }

    public function presales(Request $request)
    {
        try {
            if($request->part_number != null){
                for($i = 0; $i < count($request->part_number); $i++) {
                    $part_number[] = $request->part_number[$i];
                }
            }else{$part_number="";}
            
            $check = SysCrmService::where('deal_id',$request->service_deal_id)->where('comments',$request->comments)->count();
            if($check==0){
                if($request->service_deal_id==0){
                    $ret_id = DB::table('sys_crm_presales')->insertGetId(
                        [
                            'deal_id' => $request->service_deal_id,
                            'subject' => $request->subject,
                            'comments' => $request->comments,
                            'part_number' => implode(",",$part_number),
                            'status' => 1,
                            'created_by' => Auth::user()->id,
                            'created_at' => Carbon::now('+04:00'),
                        ]
                    );
                }
                else{
                    $ret_id = DB::table('sys_crm_service')->insertGetId(
                        [
                            'deal_id' => $request->service_deal_id,
                            'comments' => $request->comments,
                            'part_number' => implode(",",$part_number),
                            'status' => 1,
                            'created_by' => Auth::user()->id,
                            'created_at' => Carbon::now('+04:00'),
                        ]
                    );
                }
                
                if($request->user_id != null){
                    for($i = 0; $i < count($request->user_id); $i++) {
                        DB::table('sys_crm_service_assign')->insert(
                            [
                                'service_id' => $ret_id,
                                'user_id' => $request->user_id[$i],
                                'created_by' => Auth::user()->id,
                                'created_at' => Carbon::now('+04:00'),
                            ]
                        );
                    }
                }

                $body = "<br />";
                $body .= "A new Deal ".$request->service_deal_id." has been added to service request <br />";
                $body .= $request->comments;
                $body .= "<br /><br />";
                SysHelper::notificationMail('Jacob George',$body, 'jacob@sysllc.com', 'A new service request has been added');
            }
            Toastr::success('Service Added Successfully', 'Success');
            return redirect()->back();            
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function presaleslist(Request $request)
    {
        try {
            if(Auth::user()->role_id == 1 || Auth::user()->id==33 || Auth::user()->id==20 || Auth::user()->id==90){ //jacob, roiden 
                $service = SysCrmService::select('sys_crm_service.*','sys_crm_deals.deal_name','sys_crm_deals.cust_id','sys_crm_deals.owner','sys_crm_deals.deal_value','sys_crm_deals.company_id')->leftjoin('sys_crm_deals','sys_crm_deals.id','sys_crm_service.deal_id')->orderby('sys_crm_service.id','desc')->get();
            }
            else{
                $ids= SysCrmServiceAssign::select('service_id')->where('user_id', Auth::user()->id)->get();
                $ids2= SysCrmService::select('id')->where('created_by', Auth::user()->id)->get();
                $d=[];
                if(count($ids)>0){
                    foreach($ids as $id){
                        $d[]=$id->service_id;
                    }
                }
                if(count($ids2)>0){
                    foreach($ids2 as $id){
                        $d[]=$id->id;
                    }
                }
                $service = SysCrmService::select('sys_crm_service.*','sys_crm_deals.deal_name','sys_crm_deals.cust_id','sys_crm_deals.owner')->leftjoin('sys_crm_deals','sys_crm_deals.id','sys_crm_service.deal_id')->wherein('sys_crm_service.id',$d)->orderby('sys_crm_service.id','desc')->get();
                
                
            }
            
            return view('backEnd.crm.DealServiceList', compact('service'));

        } catch (\Throwable $th) {
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function serviceview($id)
    {
        try {
            $service = SysCrmService::where('id',$id)->first();
            
            $service_assign = SysCrmServiceAssign::where('service_id',$id)->get();
            $service_comments = SysCrmServiceComments::where('service_id',$id)->get();
            $deal = SysCrmDeals::where('id',$service->deal_id)->first();
            $staff      = SmStaff::select('user_id','full_name')->wherein('department_id',[3])->get();
            
            $quoteitems = SysCrmQuoteItems::select('sys_crm_quote_items.*','sm_items.part_number','sys_brand.title')
            ->leftjoin('sm_items','sm_items.id','sys_crm_quote_items.product_id')
            ->leftjoin('sys_brand','sys_brand.id','sm_items.brand')
            ->where('deal_id',$service->deal_id)->get();
            
            return view('backEnd.crm.DealService', compact('service','service_assign','service_comments','deal','staff','quoteitems'));
                
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }
    public function serviceassign(Request $request)
    {
        try {
            SysCrmServiceAssign::where('service_id',$request->service_id)->delete();            
            $service = SysCrmService::where('id',$request->service_id)->first();
            for($i = 0; $i < count($request->user_id); $i++) {
                DB::table('sys_crm_service_assign')->insert(
                    [
                        'service_id' => $request->service_id,
                        'user_id' => $request->user_id[$i],
                        'created_by' => Auth::user()->id,
                        'created_at' => Carbon::now('+04:00'),
                    ]
                );

                try {
                    $user = SmStaff::select('first_name','email')->where('user_id',$request->user_id[$i])->first();
                    $body = "<br />";
                    $body .= "A new Deal ".$service->deal_id." has been added to service request <br />";
                    $body .= $service->comments;
                    $body .= "<br /><br />";
                    SysHelper::notificationMail($user->first_name,$body, $user->email, 'A new service request has been added');
                } catch (\Throwable $th) { }
            }
            Toastr::success('Service Assigned Successfully', 'Success');
            return redirect()->back();            
        } catch (\Throwable $th) {
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    
    public function servicecommentsadditional(Request $request)
    {
        try {
            $doc_file = "";
            if ($request->file('commentsdoc') != "") { 
                $file = $request->file('commentsdoc');
                $doc_file = md5(time()) . "." . $file->getclientoriginalextension();
                $file->move('public/uploads/crm_deal_doc/', $doc_file);
                $doc_file = $doc_file;
            }
            DB::table('sys_crm_service_comments')->insert(
                [
                    'service_id' => $request->service_id,
                    'comments' => $request->comments,
                    'commentsdoc' => $doc_file,
                    'status' => $request->status,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                ]
                );


            Toastr::success('Comments has been added successfully', 'Success');
            return redirect()->back();

        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function servicecomments(Request $request)
    {
        try {
            $doc_file = "";
            if ($request->file('commentsdoc') != "") { 
                $file = $request->file('commentsdoc');
                $doc_file = md5(time()) . "." . $file->getclientoriginalextension();
                $file->move('public/uploads/crm_deal_doc/', $doc_file);
                $doc_file = $doc_file;
            }
            DB::table('sys_crm_service_comments')->insert(
                [
                    'service_id' => $request->commentsid,
                    'comments' => $request->comments,
                    'commentsdoc' => $doc_file,
                    'status' => $request->status,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                ]
                );

                SysCrmService::where('id',$request->commentsid)
                ->update([
                    'status' => $request->status,
                    'updated_by' => Auth::user()->id,
                    'updated_at' => Carbon::now('+04:00'),
                ]);

            Toastr::success('Comments has been added successfully', 'Success');
            return redirect()->back();

        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function serviceviewedit($sid,$id)
    {
        try {
            $service = SysCrmService::where('id',$sid)->first();
            
            $service_assign = SysCrmServiceAssign::where('service_id',$sid)->get();
            $service_comments = SysCrmServiceComments::where('service_id',$sid)->get();
            $comments = SysCrmServiceComments::where('id',$id)->first();
            $deal = SysCrmDeals::where('id',$service->deal_id)->first();
            $staff      = SmStaff::select('user_id','full_name')->wherein('department_id',[3])->get();
            
            $quoteitems = SysCrmQuoteItems::select('sys_crm_quote_items.*','sm_items.part_number','sys_brand.title')
            ->leftjoin('sm_items','sm_items.id','sys_crm_quote_items.product_id')
            ->leftjoin('sys_brand','sys_brand.id','sm_items.brand')
            ->where('deal_id',$service->deal_id)->get();
            
            return view('backEnd.crm.DealService', compact('service','service_assign','service_comments','deal','staff','quoteitems','comments'));
                
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }
    public function servicecommentsupdate(Request $request)
    {
        try {
            $doc_file = "";
            if ($request->file('commentsdoc') != "") { 
                $file = $request->file('commentsdoc');
                $doc_file = md5(time()) . "." . $file->getclientoriginalextension();
                $file->move('public/uploads/crm_deal_doc/', $doc_file);
                $doc_file = $doc_file;
            }
            if($doc_file==""){ $doc_file=$request->doc; }

            SysCrmServiceComments::where('id',$request->id)->update(
                [
                    'comments' => $request->comments,
                    'commentsdoc' => $doc_file,
                    'status' => $request->status,
                    'updated_by' => Auth::user()->id,
                    'updated_at' => Carbon::now('+04:00'),
                ]
                );

                SysCrmService::where('id',$request->commentsid)
                ->update([
                    'status' => $request->status,
                    'updated_by' => Auth::user()->id,
                    'updated_at' => Carbon::now('+04:00'),
                ]);

            Toastr::success('Comments has been added successfully', 'Success');
            return redirect('crm-deal-service/'.$request->commentsid.'/view');

        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }
    public function servicedelete($id)
    {
        try {
            DB::table('sys_crm_service')->where('id', $id)->delete();
            DB::table('sys_crm_service_assign')->where('service_id', $id)->delete();
            DB::table('sys_crm_service_comments')->where('service_id', $id)->delete();
            Toastr::success('Service Deleted Successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }
}
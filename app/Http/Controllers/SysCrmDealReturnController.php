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

use App\SysCrmDealReturn;

use App\SysCrmDealReturnCollection;

use App\SysCrmDealReturnPayable;

use App\SysCrmDealReturnSales;

use App\SysCrmDeals;

use App\SysCrmDealsComments;

use App\SysCrmDealTrack;

use App\SysCrmDealTrackApprovalAccounts;

use App\SysCrmDealTrackApprovalDelivery;

use App\SysCrmDealTrackApprovalInvoice;

use App\SysCrmDealTrackApprovalPurchease;

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



class SysCrmDealReturnController extends Controller

{

    public function __construct(){

        $this->middleware('PM');

    }



    public function crmdealreturnsubmit(Request $request)

    {

        $trn_time = Carbon::now('+04:00')->format('Y-m-d H:i:s');

        

        DB::beginTransaction();

        try {

            $check = SysCrmDealReturn::select('id','deal_id')->where('deal_id',$request->return_deal_id)->first();

            if(isset($check)){



                Toastr::error('Already Submited in Return', 'Failed');

                return redirect()->back();

                // $scd = SysCrmDealReturn::find($check->id);

                // $scd->remarks = $request->return_remarks;

                // $scd->updated_by = Auth::user()->id;

                // $scd->updated_at = $trn_time;

                // $scd->save();

                // $return_id=$check->id;

            }

            else{

                $scd = new SysCrmDealReturn();

                $scd->deal_id = $request->return_deal_id;

                $scd->remarks = $request->return_remarks;

                $scd->status = 0;

                $scd->collection = 0;

                $scd->return = 0;

                $scd->payable = 0;

                $scd->created_by = Auth::user()->id;

                $scd->created_at = $trn_time;

                $scd->company_id = session('logged_session_data.company_id');

                $scd->save();

                $scd->toArray();

                $return_id=$scd->id;



                // $user = DB::table('sm_staffs')->select('user_id')->where('designation_id',8)->get();

                // if(count($user)>0){

                //     foreach($user as $u){

                //         SysHelper::Erp_Notify_in($u->user_id,'Deal '.$request->deal_id.' Track Received',$u->user_id,'http://erp.venushrms.com/crm-deal-track-approval/'.$scd->id.'');

                //     }

                // }

            }



            DB::table('sys_crm_deals_comments')->insert([

                'deal_id' => $request->return_deal_id,

                'comments' => $request->return_remarks,

                'status' => 1,

                'created_by' => Auth::user()->id,

                'created_at' => Carbon::now('+04:00'),

            ]);



           $results=0;

           DB::commit();

           

        if ($results==0) {

            Toastr::success('Deal Return has been added successfully', 'Success');

            return redirect('crm-deal-return/'.$return_id.'/view');

        } else {

            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();

        }



       } catch (\Exception $e) {

           return $e;

           DB::rollback();

           Toastr::error('Operation Failed', 'Failed');

           return redirect()->back();

       }       

    }



    public function view($id)

    {

        $check = SysCrmDealReturn::where('id',$id)->count();        

        if($check==0){

            Toastr::error('Deal Rewturn Not Found.', 'Failed');

            return redirect()->back();

        }

        try{

            $dealreturn = SysCrmDealReturn::where('id',$id)->first();

            $collection = SysCrmDealReturnCollection::where('deal_id',$dealreturn->deal_id)->orderBy('id','DESC')->get();

            $sales = SysCrmDealReturnSales::where('deal_id',$dealreturn->deal_id)->get();

            $payable = SysCrmDealReturnPayable::where('deal_id',$dealreturn->deal_id)->get();

            $edit = SysCrmDeals::where('id',$dealreturn->deal_id)->first();

            

            DB::table('sys_crm_deal_return')->where('id',$id)->where('collection',1)->where('return',1)->where('payable',1)->update(['status' => 1]);



            return view('backEnd.crm.DealReturn', compact('dealreturn','collection','sales','payable','edit'));

        }catch (\Exception $e) {

            return $e;

           Toastr::error('Operation Failed', 'Failed');

           return redirect()->back();

        }

    }



    public function crmdealreturncollection(Request $request)

    {

        $input = $request->all();

        $trn_time = Carbon::now('+04:00')->format('Y-m-d H:i:s');

        try {

            $status=1;

            $check=DB::table('sys_crm_deal_return_collection')->select('id','remarks')->where('ret_id',$request->ret_id)->first();



            if(isset($check)){

                DB::table('sys_crm_deal_return_collection')->where('id', $check->id)->delete();

                $data = [];

                if(isset($input['partno'])) {

                    for ($i = 0; $i < count($input['partno']); $i++) {

                        if($input['partno'][$i]!=""){

                        $data[$i] = [ 'ret_id' => $request->ret_id,

                        'deal_id'  => $request->deal_id,

                        'partno'   => $input['partno'][$i],

                        'qty' => $input['qty'][$i],

                        'ret_date'=> $input['ret_date'][$i],

                        'remarks'  => $request->remarks,

                        'status' => $status,

                        'updated_by' => Auth::user()->id,

                        'updated_at' => $trn_time,];

                        }

                    }

                    SysCrmDealReturnCollection::insert($data);

            }

        }

            else{

                $data = [];

                if(isset($input['partno'])) {

                    for ($i = 0; $i < count($input['partno']); $i++) {

                        if($input['partno'][$i]!=""){

                        $data[$i] = [ 'ret_id' => $request->ret_id,

                        'deal_id'  => $request->deal_id,

                        'partno'   => $input['partno'][$i],

                        'qty' => $input['qty'][$i],

                        'ret_date'=> $input['ret_date'][$i],

                        'remarks'  => $request->remarks,

                        'status' => $status,

                        'updated_by' => Auth::user()->id,

                        'updated_at' => $trn_time,];

                        }

                    }

                    SysCrmDealReturnCollection::insert($data);

                }

                // DB::table('sys_crm_deal_return_collection')->insert(

                //     [

                //         'ret_id' => $request->ret_id,

                //         'deal_id' => $request->deal_id,

                //         'partno' => $request->partno,

                //         'qty' => $request->qty,

                //         'ret_date' => date('Y-m-d', strtotime($request->ret_date)),

                //         'remarks' => $request->remarks,

                //         'status' => $status,

                //         'created_by' => Auth::user()->id,

                //         'created_at' => $trn_time,

                //     ]

                // );

            }



            DB::table('sys_crm_deal_return')->where('id',$request->ret_id)->update(['collection' => $status]);



            if( $status==1){

                Toastr::success('Approved successfully', 'Success');}

            else{

                Toastr::success('Rejected successfully', 'Success');}

            return redirect()->back(); 

        } catch (\Throwable $th) {

            return $th;

           Toastr::error('Operation Failed', 'Failed');

           return redirect()->back(); 

        }

    }



    public function crmdealreturnsales(Request $request)

    {

        $trn_time = Carbon::now('+04:00')->format('Y-m-d H:i:s');

        try {

            $status=1;

            $check=DB::table('sys_crm_deal_return_sales')->select('id','remarks')->where('ret_id',$request->ret_id)->first();



            if(isset($check)){

                DB::table('sys_crm_deal_return_sales')->where('id', $check->id)->update(

                    [

                        'ret_id' => $request->ret_id,

                        'deal_id' => $request->deal_id,

                        'sales_ret_no' => $request->sales_ret_no,

                        'amount' => $request->amount,

                        'amountvat' => $request->amountvat,

                        'remarks' => $request->remarks,

                        'status' => $status,

                        'updated_by' => Auth::user()->id,

                        'updated_at' => $trn_time,

                    ]

                );

            }

            else{

                DB::table('sys_crm_deal_return_sales')->insert(

                    [

                        'ret_id' => $request->ret_id,

                        'deal_id' => $request->deal_id,

                        'sales_ret_no' => $request->sales_ret_no,

                        'amount' => $request->amount,

                        'amountvat' => $request->amountvat,

                        'remarks' => $request->remarks,

                        'status' => $status,

                        'created_by' => Auth::user()->id,

                        'created_at' => $trn_time,

                    ]

                );

            }



            DB::table('sys_crm_deal_return')->where('id',$request->ret_id)->update(['return' => $status]);



            if( $status==1){

                Toastr::success('Approved successfully', 'Success');}

            else{

                Toastr::success('Rejected successfully', 'Success');}

            return redirect()->back(); 

        } catch (\Throwable $th) {

            return $th;

           Toastr::error('Operation Failed', 'Failed');

           return redirect()->back(); 

        }

    }

    public function crmdealreturnpayable(Request $request)

    {

        $trn_time = Carbon::now('+04:00')->format('Y-m-d H:i:s');

        try {

            $status=1;

            $check=DB::table('sys_crm_deal_return_payable')->select('id','remarks')->where('ret_id',$request->ret_id)->first();



            if(isset($check)){

                DB::table('sys_crm_deal_return_payable')->where('id', $check->id)->update(

                    [

                        'ret_id' => $request->ret_id,

                        'deal_id' => $request->deal_id,

                        'mode_of_pay' => $request->mode_of_pay,

                        'remarks' => $request->remarks,

                        'status' => $status,

                        'updated_by' => Auth::user()->id,

                        'updated_at' => $trn_time,

                    ]

                );

            }

            else{

                DB::table('sys_crm_deal_return_payable')->insert(

                    [

                        'ret_id' => $request->ret_id,

                        'deal_id' => $request->deal_id,

                        'mode_of_pay' => $request->mode_of_pay,

                        'remarks' => $request->remarks,

                        'status' => $status,

                        'created_by' => Auth::user()->id,

                        'created_at' => $trn_time,

                    ]

                );

            }



            DB::table('sys_crm_deal_return')->where('id',$request->ret_id)->update(['payable' => $status]);



            if( $status==1){

                Toastr::success('Approved successfully', 'Success');}

            else{

                Toastr::success('Rejected successfully', 'Success');}

            return redirect()->back(); 

        } catch (\Throwable $th) {

            return $th;

           Toastr::error('Operation Failed', 'Failed');

           return redirect()->back(); 

        }

    }





    public function crmdealtrackapprovallisting()

    {

        try {

            $dealtrack = session('deal_track_query.dealtrack');

            $vendors = session('deal_track_query.vendors');

            $staff = session('deal_track_query.staff');

            $ctrl_deal_id = session('deal_track_query.ctrl_deal_id');

            $ctrl_company_id = session('deal_track_query.ctrl_company_id');

            $ctrl_owner_id = session('deal_track_query.ctrl_owner_id');

            $ctrl_status_id = session('deal_track_query.ctrl_status_id');

            $ctrl_date = session('deal_track_query.ctrl_date');



            return view('backEnd.crm.DealReturnApprovalList', compact('dealtrack','vendors','staff','ctrl_deal_id','ctrl_company_id','ctrl_owner_id','ctrl_status_id','ctrl_date'));

        } catch (\Throwable $th) {

            return $th;

        }

    }



    public function crmdealreturnlist(Request $request)

    {

        $staff      = SmStaff::select('user_id','full_name')->orderby('full_name','asc')->get();

        $vendors = SysCustSuppl::select('id','code','name')->where('catid',1)->orderby('name','asc')->get(); // 1 customers, 2 suppliers

        

        $ctrl_deal_id="";

        $ctrl_company_id="";

        $ctrl_owner_id="";

        $ctrl_status_id="10";

        $ctrl_date='';

        try{

            $query = SysCrmDealReturn::select('sys_crm_deal_return.*','sys_crm_deals.cust_id','sys_crm_deals.owner','sys_crm_deals.deal_value','sys_crm_deals.deal_currency')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_return.deal_id');

            

            //collection

            if(session('logged_session_data.designation_id')==34){

                $query->where('status', 0)->where('collection', 0);

            }

            //return

            if(session('logged_session_data.designation_id')==35){

                $query->where('status', 0)->where('collection', 1)->where('return', 0);

            }

            //Payable

            if(session('logged_session_data.designation_id')==2){

                $query->where('status', 0)->where('collection', 1)->where('return', 1)->where('payable', 0);

            }



            if($_POST){

                if ($request->deal_id != "") {

                    $query->where('sys_crm_deals.id', $request->deal_id);

                    $ctrl_deal_id=$request->deal_id;

                }

                if ($request->company_id != "") {

                    $query->where('sys_crm_deals.cust_id', $request->company_id);

                    $ctrl_company_id=$request->company_id;

                }

                if ($request->owner_id != "") {

                    $query->where('sys_crm_deals.owner', $request->owner_id);

                    $ctrl_owner_id=$request->owner_id;

                }

                if ($request->date != "") {

                    $query->whereRaw("DATE_FORMAT(sys_crm_deals.created_at, '%Y-%m-%d') = '".date('Y-m-d', strtotime($request->date))."'");

                    $ctrl_date=$request->date;

                }

                if ($request->status_id != "10") {

                    $ctrl_status_id=$request->status_id;

                    

                    $track = str_split($request->status_id, 1)[0];

                    $status = str_split($request->status_id, 1)[1];

                    //accounts

                    if($track=="A"){

                        $query->where('sys_crm_deal_track.accounts', $status);

                    }

                    //sales

                    else if($track=="S"){

                        $query->where('sys_crm_deal_track.sales', $status);

                    }

                    //purchease

                    else if($track=="P"){

                        $query->where('sys_crm_deal_track.purchease', $status);

                    }

                    //invoice

                    else if($track=="I"){

                        $query->where('sys_crm_deal_track.invoice', $status);

                    }

                    //delivery

                    else if($track=="D"){

                        $query->where('sys_crm_deal_track.delivery', $status);

                    }

                    //receivables

                    else if($track=="R"){

                        $query->where('sys_crm_deal_track.receivables', $status);

                    }

                    else{

                        if($request->status_id==0){

                            $query->orwhere('sys_crm_deal_track.accounts', $request->status_id);

                        }else{

                            $query->orwhere('sys_crm_deal_track.accounts', $request->status_id);

                            $query->orwhere('sys_crm_deal_track.sales', $request->status_id);

                            $query->orwhere('sys_crm_deal_track.purchease', $request->status_id);

                            $query->orwhere('sys_crm_deal_track.invoice', $request->status_id);

                            $query->orwhere('sys_crm_deal_track.delivery', $request->status_id);

                            $query->orwhere('sys_crm_deal_track.receivables', $request->status_id);

                        }

                    }

                }

            }else{

                //$query->where('sys_crm_deal_track.receivables','!=', 1);

            }

            $query->get();





            if(Auth::user()->role_id != 1){

                if(session('logged_session_data.company_id')==3){

                    $query->where('sys_crm_deal_track.company_id', 3);

                }else{

                    $query->where('sys_crm_deal_track.company_id','!=', 3);

                }

            }





            if(session('logged_session_data.designation_id')==2){

                $query->where('sys_crm_deals.stage','=', 4);

                $dealtrack = $query->orderby('receivables','asc')->orderby('id','desc')->get();

            }else{

                $query->where('sys_crm_deals.stage','=', 4);

                $dealtrack = $query->orderby('id','desc')->get();

            }

            

            $form_data = [

                'dealtrack' => $dealtrack,

                'vendors' => $vendors,

                'staff' => $staff,

                'ctrl_deal_id' => $ctrl_deal_id,

                'ctrl_company_id' => $ctrl_company_id,

                'ctrl_owner_id' => $ctrl_owner_id,

                'ctrl_status_id' => $ctrl_status_id,

                'ctrl_date' => $ctrl_date,

            ];

            //session()->put('deal_track_query', $form_data);

            //return redirect('crm-deal-track-approval-listing');

            return view('backEnd.crm.DealReturnApprovalList', compact('dealtrack','vendors','staff','ctrl_deal_id','ctrl_company_id','ctrl_owner_id','ctrl_status_id','ctrl_date'));



        }catch (\Exception $e) {

            return $e;

           Toastr::error('Operation Failed', 'Failed');

           return redirect()->back(); 

        }

    }

    public function crmdealtrackapproval($id)

    {

        try{

            $deal = SysCrmDealTrack::find($id);

            $del = SysCrmDeals::where('id',$deal->deal_id)->first();

            $cust = SysCustSuppl::where('id',$del->cust_id)->first();

            

            $paymentterms = SysPaymentTerms::orderby('title','asc')->get();

            

            $comments = SysCrmDealsComments::where('deal_id',$deal->deal_id)->orderBy('id','DESC')->get();

            $quoteitems = SysCrmQuoteItems::where('deal_id',$deal->deal_id)->get();

            

            $accounts = SysCrmDealTrackApprovalAccounts::where('deal_track_id',$id)->get();

            $sales = SysCrmDealTrackApprovalSales::where('deal_track_id',$id)->get();

            $purchease = SysCrmDealTrackApprovalPurchease::where('deal_track_id',$id)->get();

            $invoice = SysCrmDealTrackApprovalInvoice::where('deal_track_id',$id)->get();

            $delivery = SysCrmDealTrackApprovalDelivery::where('deal_track_id',$id)->get();

            $receivables = SysCrmDealTrackApprovalReceivables::where('deal_track_id',$id)->get();

            $tech = SysCrmDealTrackApprovalTechnical::where('deal_track_id',$id)->get();



            $shipping = SysShipping::select('id','shipping_name')->where('status',1)->get();

            $driver = SysDriver::where('status',1)->get();

            $addressbook = SysCustSupplAddressbook::where('cust_suppl_id',$del->cust_id)->where('set_default',1)->orderBy('id','desc')->first();

            

            $currencylist = SysCurrencySettings::select('id','code')->where('status',1)->orderBy('code','ASC')->get();



            return view('backEnd.crm.DealTrackApproval', compact('deal','accounts','sales','purchease','invoice','delivery','receivables','tech','cust','del','comments','quoteitems','shipping','driver','addressbook','paymentterms','currencylist'));

        }catch (\Exception $e) {

            return $e;

           Toastr::error('Operation Failed', 'Failed');

           return redirect()->back(); 

        }

    }



    

    public function crmdealtrackapprovalsales(Request $request)

    {

        $trn_time = Carbon::now('+04:00')->format('Y-m-d H:i:s');

        try {

            $status=1;

            if($request->margin == 2 || $request->stock == 2 || $request->purcease_quote == 2 || $request->other == 2){

                $status=2;

            }

            $check=DB::table('sys_crm_deal_track_approval_sales')->select('id','remarks')->where(['deal_id' => $request->deal_id])->first();

            if(isset($check)){

                DB::table('sys_crm_deal_track_approval_sales')->where('id', $check->id)->update(

                    [

                        'margin' => $request->margin,

                        'stock' => $request->stock,

                        'purcease_quote' => $request->purcease_quote,

                        'other' => $request->other,

                        'remarks' => $request->remarks,

                        'status' => $status,

                        'created_by' => Auth::user()->id,

                        'updated_by' => Auth::user()->id,

                        'created_at' => $trn_time,

                        'updated_at' => $trn_time,

                    ]

                );

            }

            else{

                DB::table('sys_crm_deal_track_approval_sales')->insert(

                    [

                        'deal_track_id' => $request->deal_track_id,

                        'deal_id' => $request->deal_id,

                        'margin' => $request->margin,

                        'stock' => $request->stock,

                        'purcease_quote' => $request->purcease_quote,

                        'other' => $request->other,

                        'remarks' => $request->remarks,

                        'status' => $status,

                        'created_by' => Auth::user()->id,

                        'updated_by' => Auth::user()->id,

                        'created_at' => $trn_time,

                        'updated_at' => $trn_time,

                    ]

                );

            }

            

            DB::table('sys_crm_deal_track')->where('deal_id',$request->deal_id)->update(['sales' => $status]);



            if($request->purchease_approval==2){

                DB::table('sys_crm_deal_track')->where('deal_id',$request->deal_id)->update(['purchease' => 1]);

            }



            if($status==2){

                SysHelper::Erp_Notify_in($request->owner_id,'Deal'.$request->deal_id.' Rejected',$request->owner_id,'http://erp.venushrms.com/crm-deal-track/'.$request->deal_id.'/view');

                SysHelper::Erp_Notify_track_reject($request->deal_id,$request->owner_name, $request->owner_email, "Sales",$request->remarks);

            }

            if($status==1){

                $user = DB::table('sm_staffs')->select('user_id')->where('designation_id',20)->get();

                if(count($user)>0){

                    foreach($user as $u){

                        SysHelper::Erp_Notify_in($u->user_id,'Deal Track Received',$u->user_id,'http://erp.venushrms.com/crm-deal-track-approval/'.$request->deal_track_id.'');

                    }

                }

            }



            if( $status==1){

                Toastr::success('Approved successfully', 'Success');}

            else{

                Toastr::success('Rejected successfully', 'Success');}

            return redirect()->back(); 

        } catch (\Throwable $th) {

           Toastr::error('Operation Failed', 'Failed');

           return redirect()->back(); 

        }

    }

    public function crmdealtrackapprovalpurchease(Request $request)

    {

        $trn_time = Carbon::now('+04:00')->format('Y-m-d H:i:s');

        if($request->cost_of_purchase){

            $cost_of_purchase = $request->cost_of_purchase;

            $cost_of_purchase_currency = $request->cost_of_purchase_currency;

        }else{

            $cost_of_purchase = 0.00;

            $cost_of_purchase_currency=1;

        }



        $fileone = ""; $filetwo = ""; $filethree = "";

        if ($request->file('fileone') != "") {

            $file1 = $request->file('fileone');

            $fileone = md5(time()) . "fileone." . $file1->getclientoriginalextension();

            $file1->move('public/uploads/crm_deal_track_doc/', $fileone);

            $fileone = $fileone;

        }

        if ($request->file('filetwo') != "") { 

            $file2 = $request->file('filetwo');

            $filetwo = md5(time()) . "filetwo." . $file2->getclientoriginalextension();

            $file2->move('public/uploads/crm_deal_track_doc/', $filetwo);

            $filetwo = $filetwo;

        }

        if ($request->file('filethree') != "") { 

            $file3 = $request->file('filethree');

            $filethree = md5(time()) . "filethree." . $file3->getclientoriginalextension();

            $file3->move('public/uploads/crm_deal_track_doc/', $filethree);

            $filethree = $filethree;

        }



        try {

            $status=1;

            if($request->purchease_quote == 2 || $request->quote_request == 2 || $request->validation == 2 || $request->other == 2){

                $status=2;

            }

            if($request->validation == 4){

                $status=4;

            }

            if($request->validation == 3){

                $status=3;

            }

            $check=DB::table('sys_crm_deal_track_approval_purchease')->select('id','remarks','fileone','filetwo','filethree')->where(['deal_id' => $request->deal_id])->first();

            if(isset($check)){



                if( $fileone != "" ){ $fileone = $check->fileone; }

                if( $filetwo != "" ){ $filetwo = $check->filetwo; }

                if( $filethree != "" ){ $filethree = $check->filethree; }



                DB::table('sys_crm_deal_track_approval_purchease')->where('id', $check->id)->update(

                    [

                        'purchease_quote' => $request->purchease_quote,

                        'three_quote_request' => $request->quote_request,

                        'validation' => $request->validation,

                        'other' => $request->other,

                        'remarks' => $request->remarks,

                        'status' => $status,

                        'created_by' => Auth::user()->id,

                        'updated_by' => Auth::user()->id,

                        'created_at' => $trn_time,

                        'updated_at' => $trn_time,

                        'fileone' => $fileone,

                        'filetwo' => $filetwo,

                        'filethree' => $filethree,

                        'lpo_no' => $request->lpo_no,

                        'part_no' => $request->part_no,

                        'cost_of_purchase' => $cost_of_purchase,

                        'cost_of_purchase_currency' => $cost_of_purchase_currency,

                        'delivery_date' => date('Y-m-d', strtotime($request->delivery_date)),

                        'partial_delivery_note' => $request->partial_delivery_note,

                    ]

                );

            }

            else{

                DB::table('sys_crm_deal_track_approval_purchease')->insert(

                    [

                        'deal_track_id' => $request->deal_track_id,

                        'deal_id' => $request->deal_id,

                        'purchease_quote' => $request->purchease_quote,

                        'three_quote_request' => $request->quote_request,

                        'validation' => $request->validation,

                        'other' => $request->other,

                        'remarks' => $request->remarks,

                        'status' => $status,

                        'created_by' => Auth::user()->id,

                        'updated_by' => Auth::user()->id,

                        'created_at' => $trn_time,

                        'updated_at' => $trn_time,

                        'fileone' => $fileone,

                        'filetwo' => $filetwo,

                        'filethree' => $filethree,

                        'lpo_no' => $request->lpo_no,

                        'part_no' => $request->part_no,

                        'cost_of_purchase' => $cost_of_purchase,

                        'cost_of_purchase_currency' => $cost_of_purchase_currency,

                        'delivery_date' => date('Y-m-d', strtotime($request->delivery_date)),

                        'partial_delivery_note' => $request->partial_delivery_note,

                    ]

                );

            }



            

            if($request->validation==4){

                DB::table('sys_crm_deals')->where('id',$request->deal_id)->update(['is_partial_delivery' => 1]);                

            }

            else{

                DB::table('sys_crm_deals')->where('id',$request->deal_id)->update(['is_partial_delivery' => 0]);

            }



            DB::table('sys_crm_deal_track')->where('deal_id',$request->deal_id)->update(['purchease' => $status]);



            if($status==2){

                SysHelper::Erp_Notify_in($request->owner_id,'Deal'.$request->deal_id.' Rejected',$request->owner_id,'http://erp.venushrms.com/crm-deal-track/'.$request->deal_id.'/view');

                SysHelper::Erp_Notify_track_reject($request->deal_id,$request->owner_name, $request->owner_email, "Purchase",$request->remarks);

            }

            if($status==1){

                $user = DB::table('sm_staffs')->select('user_id')->where('designation_id',35)->get();

                if(count($user)>0){

                    foreach($user as $u){

                        SysHelper::Erp_Notify_in($u->user_id,'Deal Track Received',$u->user_id,'http://erp.venushrms.com/crm-deal-track-approval/'.$request->deal_track_id.'');

                    }

                }

            }



            if( $status==1){

                Toastr::success('Approved successfully', 'Success');}

            else{

                Toastr::success('Rejected successfully', 'Success');}

            return redirect()->back(); 

        } catch (\Throwable $th) {

            return $th;

           Toastr::error('Operation Failed', 'Failed');

           return redirect()->back(); 

        }

    }

    public function crmdealtrackapprovalinvoice(Request $request)

    {

        $trn_time = Carbon::now('+04:00')->format('Y-m-d H:i:s');

        try {

            $status=1;

            if($request->delivery_advice == 3 || $request->validation == 3 || $request->hold == 3 || $request->print == 3){

                $status=3;

            }

            elseif($request->delivery_advice == 2 || $request->validation == 2 || $request->hold == 2 || $request->print == 2){

                $status=2;

            }

            $partial_invoice_amount = $request->partial_invoice_amount;

            if($request->partial_invoice_amount=="")

            {

                $partial_invoice_amount = 0.00;

            }



            $check=DB::table('sys_crm_deal_track_approval_invoice')->select('id','remarks')->where(['deal_id' => $request->deal_id])->first();

            if(isset($check)){

                DB::table('sys_crm_deal_track_approval_invoice')->where('id', $check->id)->update(

                    [

                        'delivery_advice' => $request->delivery_advice,

                        'validation' => $request->validation,

                        'hold' => $request->hold,

                        'print' => $request->print,

                        'remarks' => $request->remarks,

                        'status' => $status,

                        'invoice_no' => $request->invoice_no,

                        'partial_invoice' => $request->partial_invoice,

                        'partial_invoice_amount' => $partial_invoice_amount,

                        'updated_by' => Auth::user()->id,

                        'updated_at' => $trn_time,

                    ]

                );

            }

            else{

                DB::table('sys_crm_deal_track_approval_invoice')->insert(

                    [

                        'deal_track_id' => $request->deal_track_id,

                        'deal_id' => $request->deal_id,

                        'delivery_advice' => $request->delivery_advice,

                        'validation' => $request->validation,

                        'hold' => $request->hold,

                        'print' => $request->print,

                        'remarks' => $request->remarks,

                        'status' => $status,

                        'invoice_no' => $request->invoice_no,

                        'partial_invoice' => $request->partial_invoice,

                        'partial_invoice_amount' => $partial_invoice_amount,

                        'created_by' => Auth::user()->id,

                        'created_at' => $trn_time,

                    ]

                );

            }

            if($request->partial_invoice==1){

                DB::table('sys_crm_deals')->where('id',$request->deal_id)->update(['is_partial_invoice' => 1]);                

            }

            else{

                DB::table('sys_crm_deals')->where('id',$request->deal_id)->update(['is_partial_invoice' => 0]);

            }



            DB::table('sys_crm_deal_track')->where('deal_id',$request->deal_id)->update(['invoice' => $status]);

            

            if($status==2){

                SysHelper::Erp_Notify_in($request->owner_id,'Deal'.$request->deal_id.' Rejected',$request->owner_id,'http://erp.venushrms.com/crm-deal-track/'.$request->deal_id.'/view');

                SysHelper::Erp_Notify_track_reject($request->deal_id,$request->owner_name, $request->owner_email, "Invoice",$request->remarks);

            }

            if($status==1){

                $user = DB::table('sm_staffs')->select('user_id')->where('designation_id',34)->get();

                if(count($user)>0){

                    foreach($user as $u){

                        SysHelper::Erp_Notify_in($u->user_id,'Deal Track Received',$u->user_id,'http://erp.venushrms.com/crm-deal-track-approval/'.$request->deal_track_id.'');

                    }

                }

            }



            if( $status==1){

                Toastr::success('Approved successfully', 'Success');}

            else{

                Toastr::success('Rejected successfully', 'Success');}

            return redirect()->back(); 

        } catch (\Throwable $th) {

           Toastr::error('Operation Failed', 'Failed');

           return redirect()->back(); 

        }

    }

    

    public function crmdealtrackapprovalinvoiceupdate(Request $request)

    {

        $trn_time = Carbon::now('+04:00')->format('Y-m-d H:i:s');

        try {

           

                DB::table('sys_crm_deal_track_approval_invoice')->where('id', $request->inv_id)->update(

                    [

                        'remarks' => $request->inv_remarks,

                        'invoice_no' => $request->inv_no,

                        'updated_by' => Auth::user()->id,

                        'updated_at' => $trn_time,

                    ]

                );

            Toastr::success('Invoice No Updated Successfully', 'Success');

            return redirect()->back(); 

        } catch (\Throwable $th) {

           Toastr::error('Operation Failed', 'Failed');

           return redirect()->back(); 

        }

    }

    public function crmdealtrackapprovalaccountsupdate(Request $request)

    {

        $trn_time = Carbon::now('+04:00')->format('Y-m-d H:i:s');

        try {

                DB::table('sys_crm_deal_track')->where('id', $request->acc_deal_id)->update(

                    [

                        'accounts' => $request->acc_status,

                        'updated_by' => Auth::user()->id,

                        'updated_at' => $trn_time,

                    ]

                );

                

                //'deal_id' => $request->acc_deal_id, this is teal track id

                DB::table('sys_crm_deal_track_approval_accounts_pending')->insert(

                    [

                        'deal_id' => $request->acc_deal_id,

                        'status' => $request->acc_status,

                        'remarks' => $request->acc_remarks,

                        'created_by' => Auth::user()->id,

                        'updated_by' => Auth::user()->id,

                        'created_at' => $trn_time,

                        'updated_at' => $trn_time,

                    ]

                );

            Toastr::success('Accounts Status Updated Successfully', 'Success');

            return redirect()->back(); 

        } catch (\Throwable $th) {

           Toastr::error('Operation Failed', 'Failed');

           return redirect()->back(); 

        }

    }

    



    public function crmdealtrackapprovaldelivery(Request $request)

    {

        $deliver_by=""; $driver="";

        if($request->deliver_by==1){$deliver_by="Courier"; $driver=$request->courier;}

        if($request->deliver_by==2){$deliver_by="Driver"; $driver=$request->driver;}

        if($request->deliver_by==3){$deliver_by="Local Delivery"; $driver=$request->localdelivery;}

        if($request->deliver_by==4){$deliver_by="Office Boy"; $driver=$request->officeboy;}

        if($request->deliver_by==5){$deliver_by="Collection by Client"; $driver=$request->collectionbyclient;}

        if($request->deliver_by==6){$deliver_by="By Email"; $driver=$request->byemail;}

        

        $trn_time = Carbon::now('+04:00')->format('Y-m-d H:i:s');

        $cheque_collection_file = "";

        if ($request->file('cheque_collection_file') != "") {

            $file1 = $request->file('cheque_collection_file');

            $cheque_collection_file = md5(time()) . "cheque." . $file1->getclientoriginalextension();

            $file1->move('public/uploads/crm_deal_track_doc/', $cheque_collection_file);

            $cheque_collection_file = $cheque_collection_file;

        }

        $attach_file = "";

        if ($request->file('attach_file') != "") {

            $file1 = $request->file('attach_file');

            $attach_file = md5(time()) . "awb." . $file1->getclientoriginalextension();

            $file1->move('public/uploads/crm_deal_track_doc/', $attach_file);

            $attach_file = $attach_file;

        }



        try {

            $status=1;

            if($request->do_status == 2 || $request->cheque_collection == 2){

                $status=2;

            }

            else if($request->delivery_status==3){

                $status=3;

            }

            else if($request->delivery_status==4){

                $status=5;

            }

            else if($request->delivery_status==2){

                $status=4;

            }

            else if($request->delivery_status==1){

                $status=1;

            }

            $check=DB::table('sys_crm_deal_track_approval_delivery')->select('id','remarks','cheque_collection_file','attach_file')->where(['deal_id' => $request->deal_id])->first();

            if(isset($check)){



                if( $cheque_collection_file == "" ){ $cheque_collection_file = $check->cheque_collection_file; }

                if( $attach_file == "" ){ $attach_file = $check->attach_file; }

                

                DB::table('sys_crm_deal_track_approval_delivery')->where('id', $check->id)->update(

                    [

                        'do_status' => $request->do_status,

                        'do_no' => $request->do_no,

                        'print_invoice_no' => $request->print_invoice_no,

                        'cheque_collection' => $request->cheque_collection,

                        'cheque_collection_file' => $cheque_collection_file,

                        'delivery_status' => $request->delivery_status,

                        'deliver_by' => $deliver_by,

                        'driver' => $driver,

                        'remarks' => $request->remarks,

                        'status' => $status,

                        'created_by' => Auth::user()->id,

                        'updated_by' => Auth::user()->id,

                        'created_at' => $trn_time,

                        'updated_at' => $trn_time,

                        'cash_collected' => $request->cash_collected,

                        'contact_no' => $request->contact_no,

                        'id_no' => $request->id_no,

                        'attach_file' => $attach_file,

                        'awb_no' => $request->awb_no,

                    ]

                );

            }

            else{

                DB::table('sys_crm_deal_track_approval_delivery')->insert(

                    [

                        'deal_track_id' => $request->deal_track_id,

                        'deal_id' => $request->deal_id,

                        'do_status' => $request->do_status,

                        'do_no' => $request->do_no,

                        'print_invoice_no' => $request->print_invoice_no,

                        'cheque_collection' => $request->cheque_collection,

                        'cheque_collection_file' => $cheque_collection_file,

                        'delivery_status' => $request->delivery_status,

                        'deliver_by' => $deliver_by,

                        'driver' => $driver,

                        'remarks' => $request->remarks,

                        'status' => $status,

                        'created_by' => Auth::user()->id,

                        'updated_by' => Auth::user()->id,

                        'created_at' => $trn_time,

                        'updated_at' => $trn_time,

                        'cash_collected' => $request->cash_collected,

                        'contact_no' => $request->contact_no,

                        'id_no' => $request->id_no,

                        'attach_file' => $attach_file,

                        'awb_no' => $request->awb_no,

                    ]

                );

            }



            DB::table('sys_crm_deal_track')->where('deal_id',$request->deal_id)->update(['delivery' => $status]);

            

            if($status==2){

                SysHelper::Erp_Notify_in($request->owner_id,'Deal'.$request->deal_id.' Rejected',$request->owner_id,'http://erp.venushrms.com/crm-deal-track/'.$request->deal_id.'/view');

                SysHelper::Erp_Notify_track_reject($request->deal_id,$request->owner_name, $request->owner_email, "Delivery",$request->remarks);

            }

            if($status==1){

                $user = DB::table('sm_staffs')->select('user_id')->where('designation_id',2)->get();

                if(count($user)>0){

                    foreach($user as $u){

                        SysHelper::Erp_Notify_in($u->user_id,'Deal Track Received',$u->user_id,'http://erp.venushrms.com/crm-deal-track-approval/'.$request->deal_track_id.'');

                    }

                }

            }            



            if( $status==1){

                Toastr::success('Approved successfully', 'Success');}

            else{

                Toastr::success('Rejected successfully', 'Success');}

            return redirect()->back(); 

        } catch (\Throwable $th) {

            return $th;

           Toastr::error('Operation Failed', 'Failed');

           return redirect()->back(); 

        }

    }

    

    public function crmdealtrackapprovalprofessionalservice(Request $request)

    {

        try {

            $trn_time = Carbon::now('+04:00')->format('Y-m-d H:i:s');

            $status=1;

            if($request->technical_approve == 2){

                $status=2;

            }

            $check=DB::table('sys_crm_deal_track_approval_technical')->select('id','remarks')->where(['deal_id' => $request->deal_id])->first();

            if(isset($check)){



                

                DB::table('sys_crm_deal_track_approval_technical')->where('id', $check->id)->update(

                    [

                        'technical_approve' => $request->technical_approve,

                        'remarks' => $request->remarks,

                        'status' => $status,

                        'created_by' => Auth::user()->id,

                        'updated_by' => Auth::user()->id,

                        'created_at' => $trn_time,

                        'updated_at' => $trn_time,

                    ]

                );

            }

            else{

                DB::table('sys_crm_deal_track_approval_technical')->insert(

                    [

                        'deal_track_id' => $request->deal_track_id,

                        'deal_id' => $request->deal_id,

                        'technical_approve' => $request->technical_approve,

                        'remarks' => $request->remarks,

                        'status' => $status,

                        'created_by' => Auth::user()->id,

                        'updated_by' => Auth::user()->id,

                        'created_at' => $trn_time,

                        'updated_at' => $trn_time,

                    ]

                );

            }

            

            DB::table('sys_crm_deal_track')->where('deal_id',$request->deal_id)->update(['tech' => $status]);

            

            if($status==2){

                SysHelper::Erp_Notify_in($request->owner_id,'Deal'.$request->deal_id.' Rejected',$request->owner_id,'http://erp.venushrms.com/crm-deal-track/'.$request->deal_id.'/view');

                SysHelper::Erp_Notify_track_reject($request->deal_id,$request->owner_name, $request->owner_email, "Professional Service",$request->remarks);

            }

            if($status==1){

                $user = DB::table('sm_staffs')->select('user_id')->where('designation_id',2)->get();

                if(count($user)>0){

                    foreach($user as $u){

                        SysHelper::Erp_Notify_in($u->user_id,'Deal Track Received',$u->user_id,'http://erp.venushrms.com/crm-deal-track-approval/'.$request->deal_track_id.'');

                    }

                }

            }            



            if( $status==1){

                Toastr::success('Approved successfully', 'Success');}

            else{

                Toastr::success('Rejected successfully', 'Success');}

            return redirect()->back(); 

        } catch (\Throwable $th) {

            return $th;

           Toastr::error('Operation Failed', 'Failed');

           return redirect()->back(); 

        }

    }



    public function crmdealtrackapprovalreceivables(Request $request)

    {

        $trn_time = Carbon::now('+04:00')->format('Y-m-d H:i:s');

        $cheque_copy = ""; $banktt_copy = "";

        if ($request->file('cheque_copy') != "") {

            $files = $request->file('cheque_copy');

            for ($i=0; $i<count($files); $i++) {

                $file1 = $files[$i];

                $cheque_copy = md5(time()) . "_cheque_".$i."." . $file1->getclientoriginalextension();

                $file1->move('public/uploads/crm_deal_track_doc/', $cheque_copy);

                $cheque[]=$cheque_copy;

            }

            $cheque_copy = implode("|",$cheque);

        }

        if ($request->file('banktt_copy') != "") {

            $files = $request->file('banktt_copy');

            for ($i=0; $i<count($files); $i++) {

                $file2 = $files[$i];

                $banktt_copy = md5(time()) . "_banktt_".$i."." . $file2->getclientoriginalextension();

                $file2->move('public/uploads/crm_deal_track_doc/', $banktt_copy);

                $banktt[]=$banktt_copy;

            }

            $banktt_copy = implode("|",$banktt);

        }



        // if ($request->file('cheque_copy') != "") {

        //     $file1 = $request->file('cheque_copy');

        //     $cheque_copy = md5(time()) . "cheque." . $file1->getclientoriginalextension();

        //     $file1->move('public/uploads/crm_deal_track_doc/', $cheque_copy);

        //     $cheque_copy = $cheque_copy;

        // }

        // if ($request->file('banktt_copy') != "") {

        //     $file2 = $request->file('banktt_copy');

        //     $banktt_copy = md5(time()) . "banktt." . $file2->getclientoriginalextension();

        //     $file2->move('public/uploads/crm_deal_track_doc/', $banktt_copy);

        //     $banktt_copy = $banktt_copy;

        // }



        try {

            $status=1;

            $payment_status=0;

            $amount=0;

            if($request->payment_collection == 2){

                $status=2;

            }

            if($request->payment_collection == 3){

                $status=4;

            }

            if($request->payment_collection != 3){

                $payment_status=$request->payment_status;

                $amount=$request->amount;

                if($request->payment_status == 2){

                    $status=3;

                }

            }

            $check=DB::table('sys_crm_deal_track_approval_receivables')->select('id','remarks')->where(['deal_id' => $request->deal_id])->first();

            if(isset($check)){



                if( $cheque_copy != "" ){ $cheque_copy = $check->cheque_copy; }

                if( $banktt_copy != "" ){ $banktt_copy = $check->banktt_copy; }

                

                $rec = SysCrmDealTrackApprovalReceivables::find($check->id);

                $rec->deal_track_id = $request->deal_track_id;

                $rec->deal_id = $request->deal_id;

                $rec->payment_collection = $request->payment_collection;

                $rec->payment_status = $payment_status;



                if($request->reminder_date != ""){

                    $rec->reminder_date = date('Y-m-d H:i:s', strtotime($request->reminder_date.''.$request->reminder_time));

                }





                $rec->remarks = $request->remarks;

                $rec->status = $status;

                $rec->created_by = Auth::user()->id;

                $rec->updated_by = Auth::user()->id;

                $rec->created_at = $trn_time;

                $rec->updated_at = $trn_time;

                $rec->paymenttype = $request->payment_mode;

                $rec->amount = $amount;



                if($request->amount2 !=''){$rec->amount2 = $request->amount2;}

                if($request->amount3 !=''){$rec->amount3 = $request->amount3;}



                if($request->balance_amount !=''){$rec->balance_amount = $request->balance_amount;}



                if($request->payment_mode==1){

                    if($request->thousand !=''){$rec->thousand = $request->thousand;}

                    if($request->fivehundred !=''){$rec->fivehundred = $request->fivehundred;}

                    if($request->hundred !=''){$rec->hundred = $request->hundred;}

                    if($request->fifty !=''){$rec->fifty = $request->fifty;}

                    if($request->twenty !=''){$rec->twenty = $request->twenty;}

                    if($request->ten !=''){$rec->ten = $request->ten;}

                    if($request->five !=''){$rec->five = $request->five;}

                    if($request->one !=''){$rec->one = $request->one;}

                    if($request->fiftyp !=''){$rec->fiftyp = $request->fiftyp;}

                    if($request->twentyfivep !=''){$rec->twentyfivep = $request->twentyfivep;}

                $rec->cash_date = date('Y-m-d', strtotime($request->cash_date));



                if($request->cash_date2 !=''){$rec->cash_date2 = date('Y-m-d', strtotime($request->cash_date2));}

                if($request->cash_date3 !=''){$rec->cash_date3 = date('Y-m-d', strtotime($request->cash_date3));}



                }

                if($request->payment_mode==2){

                $rec->cheque_no = $request->cheque_no;

                if($request->cheque_no2 !=''){$rec->cheque_no2 = $request->cheque_no2;}

                if($request->cheque_no3 !=''){$rec->cheque_no3 = $request->cheque_no3;}



                $rec->cheque_date = date('Y-m-d', strtotime($request->cheque_date));

                if($request->cheque_date2 !=''){$rec->cheque_date2 = date('Y-m-d', strtotime($request->cheque_date2));}

                if($request->cheque_date3 !=''){$rec->cheque_date3 = date('Y-m-d', strtotime($request->cheque_date3));}



                $rec->cheque_copy = $cheque_copy;

                }

                if($request->payment_mode==3){

                $rec->bank_name = $request->bank_name;

                $rec->deposit_date = date('Y-m-d', strtotime($request->deposit_date));

                $rec->deposit_date2 = date('Y-m-d', strtotime($request->deposit_date2));

                }

                if($request->payment_mode==4){

                $rec->open_credit_date = date('Y-m-d', strtotime($request->open_credit_date));

                }

                if($request->payment_mode==5){

                $rec->credit_card_type = $request->credit_card_type;

                $rec->payment_date = date('Y-m-d', strtotime($request->payment_date));

                $rec->credit_card_deposit_date = date('Y-m-d', strtotime($request->credit_card_deposit_date));

                }

                if($request->payment_mode==6){

                $rec->banktt_copy = $banktt_copy;

                $rec->banktt_date = date('Y-m-d', strtotime($request->banktt_date));

                if($request->banktt_date2 !=''){$rec->banktt_date2 = date('Y-m-d', strtotime($request->banktt_date2));}

                if($request->banktt_date3 !=''){$rec->banktt_date3 = date('Y-m-d', strtotime($request->banktt_date3));}

                }

                $rec->credit_note = $request->credit_note;

                $rec->update();

            }

            else{

                $rec = new SysCrmDealTrackApprovalReceivables();

                $rec->deal_track_id = $request->deal_track_id;

                $rec->deal_id = $request->deal_id;

                $rec->payment_collection = $request->payment_collection;

                $rec->payment_status = $payment_status;



                if($request->reminder_date != ""){

                    $rec->reminder_date = date('Y-m-d H:i:s', strtotime($request->reminder_date.''.$request->reminder_time));

                }



                $rec->remarks = $request->remarks;

                $rec->status = $status;

                $rec->created_by = Auth::user()->id;

                $rec->updated_by = Auth::user()->id;

                $rec->created_at = $trn_time;

                $rec->updated_at = $trn_time;

                $rec->paymenttype = $request->payment_mode;

                $rec->amount = $amount;

                

                if($request->amount2 !=''){$rec->amount2 = $request->amount2;}

                if($request->amount3 !=''){$rec->amount3 = $request->amount3;}



                if($request->balance_amount !=''){$rec->balance_amount = $request->balance_amount;}



                if($request->payment_mode==1){

                if($request->thousand !=''){$rec->thousand = $request->thousand;}

                if($request->fivehundred !=''){$rec->fivehundred = $request->fivehundred;}

                if($request->hundred !=''){$rec->hundred = $request->hundred;}

                if($request->fifty !=''){$rec->fifty = $request->fifty;}

                if($request->twenty !=''){$rec->twenty = $request->twenty;}

                if($request->ten !=''){$rec->ten = $request->ten;}

                if($request->five !=''){$rec->five = $request->five;}

                if($request->one !=''){$rec->one = $request->one;}

                if($request->fiftyp !=''){$rec->fiftyp = $request->fiftyp;}

                if($request->twentyfivep !=''){$rec->twentyfivep = $request->twentyfivep;}

                $rec->cash_date = date('Y-m-d', strtotime($request->cash_date));



                if($request->cash_date2 !=''){$rec->cash_date2 = date('Y-m-d', strtotime($request->cash_date2));}

                if($request->cash_date3 !=''){$rec->cash_date3 = date('Y-m-d', strtotime($request->cash_date3));}



                }

                if($request->payment_mode==2){

                $rec->cheque_no = $request->cheque_no;

                if($request->cheque_no2 !=''){$rec->cheque_no2 = $request->cheque_no2;}

                if($request->cheque_no3 !=''){$rec->cheque_no3 = $request->cheque_no3;}



                $rec->cheque_date = date('Y-m-d', strtotime($request->cheque_date));

                if($request->cheque_date2 !=''){$rec->cheque_date2 = date('Y-m-d', strtotime($request->cheque_date2));}

                if($request->cheque_date3 !=''){$rec->cheque_date3 = date('Y-m-d', strtotime($request->cheque_date3));}



                $rec->cheque_copy = $cheque_copy;

                }

                if($request->payment_mode==3){

                $rec->bank_name = $request->bank_name;

                $rec->deposit_date = date('Y-m-d', strtotime($request->deposit_date));

                $rec->deposit_date2 = date('Y-m-d', strtotime($request->deposit_date2));

                }

                if($request->payment_mode==4){

                $rec->open_credit_date = date('Y-m-d', strtotime($request->open_credit_date));

                }

                if($request->payment_mode==5){

                $rec->credit_card_type = $request->credit_card_type;

                $rec->payment_date = date('Y-m-d', strtotime($request->payment_date));

                $rec->credit_card_deposit_date = date('Y-m-d', strtotime($request->credit_card_deposit_date));

                }

                if($request->payment_mode==6){

                $rec->banktt_copy = $banktt_copy;

                $rec->banktt_date = date('Y-m-d', strtotime($request->banktt_date));

                if($request->banktt_date2 !=''){$rec->banktt_date2 = date('Y-m-d', strtotime($request->banktt_date2));}

                if($request->banktt_date3 !=''){$rec->banktt_date3 = date('Y-m-d', strtotime($request->banktt_date3));}

                }

                $rec->credit_note = $request->credit_note;

                $rec->save();

            }

            

            DB::table('sys_crm_deal_track')->where('deal_id',$request->deal_id)->update(['receivables' => $status]);



            if($status==2){

                SysHelper::Erp_Notify_in($request->owner_id,'Deal'.$request->deal_id.' Rejected',$request->owner_id,'http://erp.venushrms.com/crm-deal-track/'.$request->deal_id.'/view');

                SysHelper::Erp_Notify_track_reject($request->deal_id,$request->owner_name, $request->owner_email, "Receivables",$request->remarks);

            }

            if( $status==1){

                Toastr::success('Approved successfully', 'Success');}

            if( $status==2){

                    Toastr::success('Rejected successfully', 'Success');}

            else{

                Toastr::success('Updated successfully', 'Success');}

            return redirect()->back(); 

        } catch (\Throwable $th) {

            return $th;

           Toastr::error('Operation Failed', 'Failed');

           return redirect()->back(); 

        }

    }

    public function getdriverbyshipping(Request $request)

    {

        try {

            $shipping = SysShipping::select('id')->where('shipping_name',$request->deliver_by)->first();

            $driver = SysDriver::select('driver_name')->where('shipping_id',$shipping->id)->get();

            return json_encode(array('data'=>$driver));



       }catch (\Exception $e) {         

           $retData="ERROR";

           return json_encode(array('data'=>$retData));

       }

    }

    public function crmcustomercolor(Request $request)

    {

        try {

            DB::table('sys_cust_suppl')->where('id', $request->color_customer_id)->update(

                [

                    'type' => $request->edit_color,

                    'updated_by' => Auth::user()->id

                ]

            );

            Toastr::success('Color updated successfully', 'Success');

            return redirect()->back(); 

       }catch (\Exception $e) {

            Toastr::error('Color Updation Failed', 'Failed');

            return redirect()->back(); 

       }

    }

    public function crmdealtrackapprovalreceivablespaymentmode(Request $request)

    {

        try {

            DB::table('sys_crm_deal_track')->where('deal_id', $request->edit_payment_mode_id)->update(

                [

                    'payment_mode' => $request->edit_payment_mode,

                    'updated_by' => Auth::user()->id

                ]

            );

            Toastr::success('Color updated successfully', 'Success');

            return redirect()->back(); 

       }catch (\Exception $e) {

            Toastr::error('Color Updation Failed', 'Failed');

            return redirect()->back(); 

       }

    }

    public function crmdealtrackapprovalreceivablespaymenttermsmode(Request $request)

    {

        try {

            DB::table('sys_crm_deal_track')->where('deal_id', $request->edit_payment_mode_id)->update(

                [

                    'payment_mode' => $request->edit_payment_mode,

                    'payment_terms' => $request->edit_payment_terms,

                    'updated_by' => Auth::user()->id

                ]

            );

            Toastr::success('Color updated successfully', 'Success');

            return redirect()->back(); 

       }catch (\Exception $e) {

            Toastr::error('Color Updation Failed', 'Failed');

            return redirect()->back(); 

       }

    }



    public function trackpagefilter($track)

    {

        $staff      = SmStaff::select('user_id','full_name')->orderby('full_name','asc')->get();

        $vendors = SysCustSuppl::select('id','code','name')->where('catid',1)->orderby('name','asc')->get(); // 1 customers, 2 suppliers

        $ctrl_deal_id="";

        $ctrl_company_id="";

        $ctrl_owner_id="";

        $ctrl_status_id="10";

        $ctrl_date='';

        try{



            if($track=="pendingpayments"){

                

                $dealtrack = SysCrmDealTrack::select('sys_crm_deal_track.*','sys_crm_deals.cust_id','sys_crm_deals.owner')

                ->join('sys_crm_deals','sys_crm_deal_track.deal_id','sys_crm_deals.id')

                ->join('sys_crm_deal_track_approval_receivables','sys_crm_deals.id','sys_crm_deal_track_approval_receivables.deal_id')

                ->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_receivables.created_at, '%Y-%m') = '".date('Y-m')."'")->where('sys_crm_deal_track.receivables','!=', 1)->where('sys_crm_deal_track.delivery', 1);

                

                if(session('logged_session_data.company_id')==3){ //magnus

                    $dealtrack->where('sys_crm_deals.company_id',3);

                }

                $dealtrack->orderby('sys_crm_deal_track_approval_receivables.id','asc')->get();



                return view('backEnd.crm.DealTrackApprovalList', compact('dealtrack','vendors','staff','ctrl_deal_id','ctrl_company_id','ctrl_owner_id','ctrl_status_id','ctrl_date'));

            }



            if($track=="orderinprocess"){

            $dealtrack = SysCrmDealTrack::select('sys_crm_deal_track.*','sys_crm_deals.cust_id','sys_crm_deals.owner')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '".date('Y-m')."'")->where('sys_crm_deal_track.receivables','!=', 1);

            if(session('logged_session_data.company_id')==3){ //magnus

                $dealtrack->where('sys_crm_deals.company_id',3);

            }

            $dealtrack->orderby('sys_crm_deal_track.id','desc')->get();



                return view('backEnd.crm.DealTrackApprovalList', compact('dealtrack','vendors','staff','ctrl_deal_id','ctrl_company_id','ctrl_owner_id','ctrl_status_id','ctrl_date'));

            }



            if($track=="paymentreminder"){

                $dealtrack = SysCrmDealTrack::select('sys_crm_deal_track.*','sys_crm_deals.cust_id','sys_crm_deals.owner')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')

                ->join('sys_crm_deal_track_approval_receivables','sys_crm_deal_track_approval_receivables.deal_id','sys_crm_deal_track.deal_id')

                ->where([['sys_crm_deal_track.receivables', 3]])->whereRaw("DATE_FORMAT(reminder_date, '%Y-%m-%d') >= '".date('Y-m-d')."'");

                if(session('logged_session_data.company_id')==3){ //magnus

                    $dealtrack->where('sys_crm_deals.company_id',3);

                }

                $dealtrack->orderby('reminder_date','asc')->get();



                return view('backEnd.crm.DealTrackApprovalList', compact('dealtrack','vendors','staff','ctrl_deal_id','ctrl_company_id','ctrl_owner_id','ctrl_status_id','ctrl_date'));

            }



            if($track=="paymentpendingafterreminder"){

                $dealtrack = SysCrmDealTrack::select('sys_crm_deal_track.*','sys_crm_deals.cust_id','sys_crm_deals.owner')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')

                ->join('sys_crm_deal_track_approval_receivables','sys_crm_deal_track_approval_receivables.deal_id','sys_crm_deal_track.deal_id')

                ->where([['sys_crm_deal_track.receivables', 3]])->whereRaw("DATE_FORMAT(reminder_date, '%Y-%m-%d') < '".date('Y-m-d')."'");

                if(session('logged_session_data.company_id')==3){ //magnus

                    $dealtrack->where('sys_crm_deals.company_id',3);

                }

                $dealtrack->orderby('reminder_date','asc')->get();



                return view('backEnd.crm.DealTrackApprovalList', compact('dealtrack','vendors','staff','ctrl_deal_id','ctrl_company_id','ctrl_owner_id','ctrl_status_id','ctrl_date'));

            }



            if($track=="salesorderinprocess"){



                $dealtrack = SysCrmDealTrack::select('sys_crm_deal_track.*','sys_crm_deals.cust_id','sys_crm_deals.owner')

                ->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')

                ->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '".date('Y-m')."'")

                ->where('sys_crm_deals.owner', Auth::user()->id)->where('sys_crm_deal_track.receivables','!=', 1);

                if(session('logged_session_data.company_id')==3){ //magnus

                    $dealtrack->where('sys_crm_deals.company_id',3);

                }

                $dealtrack->orderby('sys_crm_deal_track.id','desc')->get();



                return view('backEnd.crm.DealTrackApprovalList', compact('dealtrack','vendors','staff','ctrl_deal_id','ctrl_company_id','ctrl_owner_id','ctrl_status_id','ctrl_date'));

            }



            if($track=="salesteamorderinprocess"){

                if(Auth::user()->id==27){//monica

                    $teams= array(27,28,30,54);

                }

                else if(Auth::user()->id==33){ //jacob

                    $teams= array(33,24);

                }

                else if(Auth::user()->id==44){ //rajiv

                    $teams= array(44,45,34,32);

                }

                $dealtrack = SysCrmDealTrack::select('sys_crm_deal_track.*','sys_crm_deals.cust_id','sys_crm_deals.owner')

                ->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')

                ->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '".date('Y-m')."'")

                ->wherein('sys_crm_deals.owner', $teams)->where('sys_crm_deal_track.receivables','!=', 1);

                if(session('logged_session_data.company_id')==3){ //magnus

                    $dealtrack->where('sys_crm_deals.company_id',3);

                }

                $dealtrack->orderby('sys_crm_deal_track.id','desc')->get();



                return view('backEnd.crm.DealTrackApprovalList', compact('dealtrack','vendors','staff','ctrl_deal_id','ctrl_company_id','ctrl_owner_id','ctrl_status_id','ctrl_date'));

            }



            if($track=="salespendingpayments"){

                $dealtrack = SysCrmDealTrack::select('sys_crm_deal_track.*','sys_crm_deals.cust_id','sys_crm_deals.owner')

                ->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')

                ->join('sys_crm_deal_track_approval_receivables','sys_crm_deals.id','sys_crm_deal_track_approval_receivables.deal_id')

                ->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_receivables.created_at, '%Y-%m') = '".date('Y-m')."'")->where('sys_crm_deal_track.receivables','!=', 1)->where('sys_crm_deal_track.delivery', 1)->where('owner', Auth::user()->id);

                if(session('logged_session_data.company_id')==3){ //magnus

                    $dealtrack->where('sys_crm_deals.company_id',3);

                }

                $dealtrack->orderby('sys_crm_deal_track_approval_receivables.id','asc')->get();



                return view('backEnd.crm.DealTrackApprovalList', compact('dealtrack','vendors','staff','ctrl_deal_id','ctrl_company_id','ctrl_owner_id','ctrl_status_id','ctrl_date'));

            }

            

            if($track=="salesteampendingpayments"){

                if(Auth::user()->id==27){ //monica

                    $teams= array(27,28,30,54);

                }

                else if(Auth::user()->id==33){ //jacob

                    $teams= array(33,24);

                }

                else if(Auth::user()->id==44){ //rajiv

                    $teams= array(44,45,34,32);

                }

                $dealtrack = SysCrmDealTrack::select('sys_crm_deal_track.*','sys_crm_deals.cust_id','sys_crm_deals.owner')

                ->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')

                ->join('sys_crm_deal_track_approval_receivables','sys_crm_deals.id','sys_crm_deal_track_approval_receivables.deal_id')

                ->whereRaw("DATE_FORMAT(sys_crm_deal_track_approval_receivables.created_at, '%Y-%m') = '".date('Y-m')."'")->where('sys_crm_deal_track.receivables','!=', 1)->where('sys_crm_deal_track.delivery', 1)->wherein('owner', $teams);

                if(session('logged_session_data.company_id')==3){ //magnus

                    $dealtrack->where('sys_crm_deals.company_id',3);

                }

                $dealtrack->orderby('sys_crm_deal_track_approval_receivables.id','asc')->get();



                return view('backEnd.crm.DealTrackApprovalList', compact('dealtrack','vendors','staff','ctrl_deal_id','ctrl_company_id','ctrl_owner_id','ctrl_status_id','ctrl_date'));

            }

            

            if($track=="partialdelivery"){

                $dealtrack = SysCrmDealTrack::select('sys_crm_deal_track.*','sys_crm_deals.cust_id','sys_crm_deals.owner')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')->where('sys_crm_deal_track.purchease', 4);

                if(session('logged_session_data.company_id')==3){ //magnus

                    $dealtrack->where('sys_crm_deals.company_id',3);

                }

                $dealtrack->orderby('sys_crm_deal_track.id','desc')->get();



                return view('backEnd.crm.DealTrackApprovalList', compact('dealtrack','vendors','staff','ctrl_deal_id','ctrl_company_id','ctrl_owner_id','ctrl_status_id','ctrl_date'));

            }

            

            if($track=="purchasecompleted"){

                $dealtrack = SysCrmDealTrack::select('sys_crm_deal_track.*','sys_crm_deals.cust_id','sys_crm_deals.owner')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')

                ->join('sys_crm_deal_track_approval_purchease','sys_crm_deal_track_approval_purchease.deal_id','sys_crm_deal_track.deal_id')

                ->where('sys_crm_deal_track_approval_purchease.validation', 1);

                if(session('logged_session_data.company_id')==3){ //magnus

                    $dealtrack->where('sys_crm_deals.company_id',3);

                }

                $dealtrack->orderby('sys_crm_deal_track.id','desc')->get();



                return view('backEnd.crm.DealTrackApprovalList', compact('dealtrack','vendors','staff','ctrl_deal_id','ctrl_company_id','ctrl_owner_id','ctrl_status_id','ctrl_date'));

            }

            

            if($track=="underpurchase"){

                $dealtrack = SysCrmDealTrack::select('sys_crm_deal_track.*','sys_crm_deals.cust_id','sys_crm_deals.owner')

                ->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')

                ->join('sys_crm_deal_track_approval_purchease','sys_crm_deal_track_approval_purchease.deal_id','sys_crm_deal_track.deal_id')

                ->where('sys_crm_deal_track.purchease', 3);

                if(session('logged_session_data.company_id')==3){ //magnus

                    $dealtrack->where('sys_crm_deals.company_id',3);

                }

                $dealtrack->orderby('sys_crm_deal_track.id','desc')->get();



                return view('backEnd.crm.DealTrackApprovalList', compact('dealtrack','vendors','staff','ctrl_deal_id','ctrl_company_id','ctrl_owner_id','ctrl_status_id','ctrl_date'));

            }

            

            if($track=="salesapprovedlist"){

                $dealtrack = SysCrmDealTrack::select('sys_crm_deal_track.*','sys_crm_deals.cust_id','sys_crm_deals.owner')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '".date('Y-m')."'")->where('sys_crm_deal_track.sales', 1);

                if(session('logged_session_data.company_id')==3){ //magnus

                    $dealtrack->where('sys_crm_deals.company_id',3);

                }

                $dealtrack->orderby('sys_crm_deal_track.id','desc')->get();



                return view('backEnd.crm.DealTrackApprovalList', compact('dealtrack','vendors','staff','ctrl_deal_id','ctrl_company_id','ctrl_owner_id','ctrl_status_id','ctrl_date'));

            }

            

            if($track=="doonprocess"){

                $dealtrack = SysCrmDealTrack::select('sys_crm_deal_track.*','sys_crm_deals.cust_id','sys_crm_deals.owner')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '".date('Y-m')."'")->where([['sys_crm_deal_track.delivery', '=', 3],['sys_crm_deal_track.delivery', '=', 4],['sys_crm_deal_track.invoice', 1]]);

                if(session('logged_session_data.company_id')==3){ //magnus

                    $dealtrack->where('sys_crm_deals.company_id',3);

                }

                $dealtrack->orderby('sys_crm_deal_track.id','desc')->get();



                return view('backEnd.crm.DealTrackApprovalList', compact('dealtrack','vendors','staff','ctrl_deal_id','ctrl_company_id','ctrl_owner_id','ctrl_status_id','ctrl_date'));

            }

            

            if($track=="dopending"){

                $dealtrack = SysCrmDealTrack::select('sys_crm_deal_track.*','sys_crm_deals.cust_id','sys_crm_deals.owner')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')->whereRaw("DATE_FORMAT(sys_crm_deal_track.created_at, '%Y-%m') = '".date('Y-m')."'")->where('sys_crm_deal_track.delivery', 0);

                if(session('logged_session_data.company_id')==3){ //magnus

                    $dealtrack->where('sys_crm_deals.company_id',3);

                }

                $dealtrack->orderby('sys_crm_deal_track.id','asc')->get();



                return view('backEnd.crm.DealTrackApprovalList', compact('dealtrack','vendors','staff','ctrl_deal_id','ctrl_company_id','ctrl_owner_id','ctrl_status_id','ctrl_date'));

            }



            if($track=="0"){



            $query = SysCrmDealTrack::select('sys_crm_deal_track.*','sys_crm_deals.cust_id','sys_crm_deals.owner')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id');

            //accounts

            if(session('logged_session_data.designation_id')==8){

                $query = SysCrmDealTrack::select('sys_crm_deal_track.*','sys_crm_deals.cust_id','sys_crm_deals.owner')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id');

            }

            //sales

            if(session('logged_session_data.designation_id')==27){

                $query = SysCrmDealTrack::select('sys_crm_deal_track.*','sys_crm_deals.cust_id','sys_crm_deals.owner')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')->where([['accounts','=',1]]);

            }

            //purchease

            if(session('logged_session_data.designation_id')==20){

                $query = SysCrmDealTrack::select('sys_crm_deal_track.*','sys_crm_deals.cust_id','sys_crm_deals.owner')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')->where([['accounts','=',1],['sales','=',1]]);

            }

            //invoice

            if(session('logged_session_data.designation_id')==35){

                $query = SysCrmDealTrack::select('sys_crm_deal_track.*','sys_crm_deals.cust_id','sys_crm_deals.owner','sys_crm_deal_track_approval_invoice.invoice_no','sys_crm_deals.company_id','sys_crm_deal_track.company_id')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')->leftjoin('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_track_id','sys_crm_deal_track.id')->where([['accounts','=',1],['sales','=',1]])->wherein('purchease',[1,4]);

            }

            //delivery

            if(session('logged_session_data.designation_id')==34){

                $query = SysCrmDealTrack::select('sys_crm_deal_track.*','sys_crm_deals.cust_id','sys_crm_deals.owner')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')->where([['accounts','=',1],['sales','=',1],['purchease','=',1],['invoice','=',1]]);

            }

            //receivables

            if(session('logged_session_data.designation_id')==2){

                $query = SysCrmDealTrack::select('sys_crm_deal_track.*','sys_crm_deals.cust_id','sys_crm_deals.owner')->join('sys_crm_deals','sys_crm_deals.id','sys_crm_deal_track.deal_id')->where([['accounts','=',1],['sales','=',1],['purchease','=',1],['invoice','=',1],['delivery','=',1]]);

            }

            

              

                    $ctrl_status_id=$track;

                    //accounts

                    if(session('logged_session_data.designation_id')==8){

                        $query->where('sys_crm_deal_track.accounts', $track);

                    }

                    //sales

                    else if(session('logged_session_data.designation_id')==27){

                        $query->where('sys_crm_deal_track.sales', $track);

                    }

                    //purchease

                    else if(session('logged_session_data.designation_id')==20){

                            $query->where('sys_crm_deal_track.purchease', $track);                        

                    }

                    //invoice

                    else if(session('logged_session_data.designation_id')==35){

                        $query->where('sys_crm_deal_track.invoice', $track);

                    }

                    //delivery

                    else if(session('logged_session_data.designation_id')==34){

                        $query->where('sys_crm_deal_track.delivery', $track);

                    }

                    //receivables

                    else if(session('logged_session_data.designation_id')==2){

                        $query->where('sys_crm_deal_track.receivables', $track);

                    }

                    else{

                        if($track==0){

                            $query->orwhere('sys_crm_deal_track.accounts', $track);

                        }else{

                            $query->orwhere('sys_crm_deal_track.accounts', $track);

                            $query->orwhere('sys_crm_deal_track.sales', $track);

                            $query->orwhere('sys_crm_deal_track.purchease', $track);

                            $query->orwhere('sys_crm_deal_track.invoice', $track);

                            $query->orwhere('sys_crm_deal_track.delivery', $track);

                            $query->orwhere('sys_crm_deal_track.receivables', $track);

                        }

                    }

                    if(session('logged_session_data.company_id')==3){ //magnus

                        $query->where('sys_crm_deals.company_id',3);

                    }

                    $dealtrack = $query->orderby('id','desc')->get();

                    // if(session('logged_session_data.designation_id')==2){

                    //     $query->where('sys_crm_deals.stage','=', 4);

                    //     $dealtrack = $query->orderby('receivables','asc')->orderby('id','desc')->get();

                    // }else{

                    //     $query->where('sys_crm_deals.stage','=', 4);

                    //     $dealtrack = $query->orderby('id','desc')->get();

                    // }

                }



            return view('backEnd.crm.DealTrackApprovalList', compact('dealtrack','vendors','staff','ctrl_deal_id','ctrl_company_id','ctrl_owner_id','ctrl_status_id','ctrl_date'));

        }catch (\Exception $e) {

            return $e;

           Toastr::error('Operation Failed', 'Failed');

           return redirect()->back(); 

        }

    }



    

    public function crmdealuploadimgview()

    {

        try{            

            return view('backEnd.crm.uploadForm');

        }catch (\Exception $e) {

            return $e;

        }

    }

    public function crmdealuploadimg(Request $request)

    {

        try {

            

        $lpo_file = "";



        if ($request->file('lpo') != "") {

            $files = $request->file('lpo');

            for ($i=0; $i<count($files); $i++) {

                $file1 = $files[$i];

                

                if(str_contains($file1->getClientOriginalName(),'dubai-uae'))

                {

                    $newname = str_replace('dubai-uae', 'georgia-us', $file1->getClientOriginalName());

                }

                else if(str_contains($file1->getClientOriginalName(),'dubai'))

                {

                    $newname = str_replace('dubai', 'georgia', $file1->getClientOriginalName());

                }

                else if(str_contains($file1->getClientOriginalName(),'Dubai'))

                {

                    $newname = str_replace('Dubai', 'georgia', $file1->getClientOriginalName());

                }

                else if(str_contains($file1->getClientOriginalName(),'UAE'))

                {

                    $newname = str_replace('UAE', 'us', $file1->getClientOriginalName());

                }

                else if(str_contains($file1->getClientOriginalName(),'uae'))

                {

                    $newname = str_replace('uae', 'us', $file1->getClientOriginalName());

                }

                else

                {

                    $newname = $file1->getClientOriginalName();

                }



                $lpo_file = $newname;

                $file1->move('public/uploads/syscom_us/', $lpo_file);

                $lpo[]=$lpo_file;

            }

        }

        return "success";

        

    } catch (\Throwable $th) {

        return $th;

    }



    }

    

}
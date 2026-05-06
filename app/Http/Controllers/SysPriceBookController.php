<?php

namespace App\Http\Controllers;

use App\ApiBaseMethod;
use App\SmInspectingDepartment;
use App\SmItem;
use Illuminate\Http\Request;
use App\SmItemStore;
use App\SmStaff;
use App\SysChartofAccounts;
use App\SysCompany;
use App\SysCrmLeads;
use App\SysCurrencySettings;
use App\SysCustSuppl;
use App\SysHelper;
use App\SysItemOpeningStock;
use App\SysItemStock;
use App\SysPaymentTerms;
use App\SysPriceBook;
use App\SysShipping;
use App\SysStockIn;
use App\SysStockInSerialNo;
use App\SysSupplierType;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Return_;
use Validator;

class SysPriceBookController extends Controller
{
    public function __construct(){
        $this->middleware('PM');
    }
    
    public function index(Request $request)
    {
        try{
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $currency   = SysCurrencySettings::select('id','code')->get();
            $company    = SysCompany::find(session('logged_session_data.company_id'));
            $product      = SysHelper::get_product_list($company_id);
            if($request->pid){
                $pid = $request->pid;
                $pricebook = SysPriceBook::where('pid',$request->pid)->get();
                $edit = SysPriceBook::where('pid',$request->pid)->get();
            }else{
                $pid = 0;
                $pricebook = [];
                $edit = [];
            }
            
            return view('backEnd.pricebook.Form', compact('currency', 'company','product','pid','pricebook','edit'));
        }catch (\Exception $e) {
            return $e;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    // public function getproduct(Request $request,$pro_id)
    // {
    //     try{
    //         $currency   = SysCurrencySettings::select('id','code')->get();
    //         $company    = SysCompany::find(session('logged_session_data.company_id'));
    //         $product      = SmItem::select('id','part_number','description')->get();
    //         $pid = $request->pid;
    //         $pricebook = SysPriceBook::where('pid',$request->pid)->get();
            
    //         return view('backEnd.pricebook.Form', compact('currency','company','product','pid','pricebook'));
    //     }catch (\Exception $e) {
    //        Toastr::error('Operation Failed', 'Failed');
    //        return redirect()->back(); 
    //     }
    // }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
        for($i = 0; $i < count($request->currency_id); $i++) {
           $pb = new SysPriceBook();
           $pb->pid = $request->pid;
           $pb->currency_id = $request->currency_id[$i];
           $pb->r_price = $request->r_price[$i];
           $pb->e_price = $request->e_price[$i];
           $pb->status = 1;
           $pb->created_by = Auth::user()->id;
           $pb->company_id = session('logged_session_data.company_id');
           $pb->save();
           $pb->toArray();
           $results=0;
           DB::commit();
        }
        if ($results==0) {
            Toastr::success('Price Book has been added successfully', 'Success');
            return redirect()->back();
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
    
    public function show(Request $request)
    {
        try{
            $pricebook = [];
            if($_POST){
            $pricebook = SmItem::select('sys_price_book.id','sm_items.part_number','sm_items.description','sys_price_book.r_price','sys_price_book.e_price','sys_price_book.status','sys_price_book.pid','sys_currency.code')
                    ->join('sys_price_book','sys_price_book.pid','sm_items.id')
                    ->join('sys_currency','sys_currency.id','sys_price_book.currency_id')
                    ->where(function($query) use ($request) {
                            $query->where('sm_items.part_number','like','%'.$request->part_number.'%')
                                ->orwhere('sm_items.description','like','%'.$request->part_number.'%');
                    })->get();

            }
            return view('backEnd.pricebook.List', compact('pricebook'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           //return redirect()->back();
           return $e;
        }
    }
    
    public function editproduct(Request $request,$pro_id,$book_id)
    {
        try{
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $currency   = SysCurrencySettings::select('id','code')->get();
            $company    = SysCompany::find(session('logged_session_data.company_id'));
            $product    = SysHelper::get_product_list($company_id);
            $pid = $pro_id;
            $pricebook = SysPriceBook::where('pid',$pro_id)->get();

            $edit = SysPriceBook::where('pid',$pro_id)->get();
            //return $edit;
            //$edit = SysPriceBook::where('id',$book_id)->first();

            return view('backEnd.pricebook.Form', compact('currency','company','product','pid','pricebook','edit'));

        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function update(Request $request, $id)
    {
        try {
            for($i = 0; $i < count($request->pbid); $i++) {
                $pb = SysPriceBook::find($request->pbid[$i]);
                $pb->pid = $request->pid;
                $pb->currency_id = $request->currency_id[$i];
                $pb->r_price = $request->r_price[$i];
                $pb->e_price = $request->e_price[$i];
                $pb->status = 1;
                $pb->updated_by = Auth::user()->id;
                $pb->company_id = session('logged_session_data.company_id');
                $results = $pb->update();
            }

        if ($results) {
            Toastr::success('Price Book has been updated successfully', 'Success');
            return redirect()->back();
        } else {
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function getcustomername(Request $request)
    {
        $input = $request->all();
        
        try{

            $customers      = SysCustSuppl::select('id','contcat_person','contcat_number','email')->where('id',$request->id)->get();            
            $bug = 0;
        }
        
        catch(\Exception $e){
            return $e;
            $bug = $e->errorInfo[1];
        }
        if($bug==0){
            return json_encode(array('data'=>$customers));
        }else {
            $retData='ERROR';
            return json_encode(array('data'=>$retData));
        }
    }

    public function destroy($id)
    {
        //
    }

    public function deleteStoreView(Request $request,$id){

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            return ApiBaseMethod::sendResponse($id, null);
        }
         return view('backEnd.inventory.deleteItemStoreView', compact('id'));
    }

    public function deleteStore(Request $request,$id){
        $result = SmItemStore::destroy($id);

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            if ($result) {
                return ApiBaseMethod::sendResponse(null, 'Store  has been deleted successfully');
            } else {
                return ApiBaseMethod::sendError('Something went wrong, please try again');
            }
        } else {
            if ($result) {
                return redirect('item-store')->with('message-success-delete', 'Store  has been deleted successfully');
            } else {
                return redirect()->back()->with('message-danger-delete', 'Something went wrong, please try again');
            }
        }
    }
}

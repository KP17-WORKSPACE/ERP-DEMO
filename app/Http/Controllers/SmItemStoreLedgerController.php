<?php

namespace App\Http\Controllers;

use App\ApiBaseMethod;
use App\SmInspectingDepartment;
use App\SmItem;
use Illuminate\Http\Request;
use App\SmItemStore;
use App\SysChartofAccounts;
use App\SysCompany;
use App\SysCurrencySettings;
use App\SysHelper;
use App\SysItemOpeningStock;
use App\SysItemStock;
use App\SysPaymentTerms;
use App\SysShipping;
use App\SysSupplierType;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;

class SmItemStoreLedgerController extends Controller
{
    public function __construct(){
        $this->middleware('PM');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            //$openingstock = DB::select('SELECT items.id,items.part_number, os.os_id FROM sys_item_stock os INNER JOIN sm_items items ON items.id=os.partno WHERE os_id IS NOT NULL');
            $stocklist = DB::select('SELECT itm.part_number, itm.description, sto.id, sto.ops_id, sto.pi_id, sto.partno, sto.qty, sto.price, sto.refno, pii.doc_number pi_docno, iop.doc_number os_docno,  pii.pi_date pi_date, iop.doc_date os_date, cs.name FROM sys_item_stock sto
            INNER JOIN sm_items itm ON itm.id=sto.partno
            LEFT JOIN sys_purchase_invoice pii ON pii.id = sto.pi_id
            LEFT JOIN sys_item_opening_stock iop ON iop.id = sto.os_id
            LEFT JOIN sys_cust_suppl cs ON cs.id = pii.vendors
            ORDER BY part_number ASC, sto.id ASC');
                
                //return $stocklist;



            return view('backEnd.inventory.itemStoreLedger', compact('stocklist'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           //return redirect()->back();
           return $e;
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->part_number[0] !="none" && $request->qty[0] !="" && $request->unitprice[0] !="")
        {
            
        }
        else
        {
            Toastr::error('Items not found', 'Failed');
            return redirect()->back();
        }

        DB::beginTransaction();
        try {
           $mxid = SysItemOpeningStock::max('id');
           $ios = new SysItemOpeningStock();
           $ios->doc_number = 'OPS-'.($mxid+1);
           $ios->doc_date = date('Y-m-d', strtotime($request->doc_date));
           $ios->bill_date = date('Y-m-d', strtotime($request->bill_date));
           $ios->currency = $request->currency;
           $ios->narration = $request->narration;
           $ios->status = 1;
           $ios->company_id = session('logged_session_data.company_id');
           $ios->created_by = Auth::user()->id;
           $ios->save();
           $ios->toArray();

           for($i = 0; $i < count($request->part_number); $i++) {
               if($request->part_number[$i] !="none" && $request->qty[$i] !="" && $request->unitprice[$i] !=""){
                   $ist = new SysItemStock();
                   $ist->ops_id = $ios->id;
                   $ist->groupname = $request->groupname[$i];
                   $ist->partno = $request->part_number[$i];
                   $ist->description = $request->description[$i];
                   $ist->slno = "";
                   $ist->qty = $request->qty[$i];
                   $ist->price = $request->unitprice[$i];
                   $ist->remarks = $request->remarks[$i];
                   $ist->refno = $request->refno[$i];
                   $ist->status = 1;
                   $ist->created_by = Auth::user()->id;
                   $ist->company_id = session('logged_session_data.company_id');
                   $ist->currency_id = $request->currency;
                   $ist->save();
               }
           }
           $results=0;
           DB::commit();
           
           if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            if ($results==0) {
                return ApiBaseMethod::sendResponse(null, 'Opening Stock has been added successfully');
            } else {
                return ApiBaseMethod::sendError('Something went wrong, please try again');
            }
        } else {
            if ($results==0) {
                Toastr::success('Opening Stock has been added successfully', 'Success');
                return redirect()->back();
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        }

       } catch (\Exception $e) {
           return $e;
           DB::rollback();
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back();
       }
       
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        try{
            $ios = SysItemOpeningStock::all();

            return view('backEnd.inventory.itemStoreList', compact('ios'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           //return redirect()->back();
           return $e;
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        try{
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $currency       = SysCurrencySettings::select('id','code')->get();
            $company        = SysCompany::find(session('logged_session_data.company_id'));
            $items = SysHelper::get_product_list($company_id);
            $openingstock = SysItemOpeningStock::where('id',$id)->first();
            $stocklist = SysItemStock::where('ops_id',$id)->get();

            return view('backEnd.inventory.itemStoreForm', compact('currency', 'items', 'company','openingstock','stocklist'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }


        $editData = SmItemStore::find($id);
        $itemstores = SmItemStore::all();

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            $data = [];
            $data['editData'] = $editData->toArray();
            $data['itemstores'] = $itemstores->toArray();
            return ApiBaseMethod::sendResponse($data, null);
        }
        return view('backEnd.inventory.itemStoreList', compact('editData','itemstores'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
           'store_name' => "required"
        ]);

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
       
       $stores = SmItemStore::find($id);
       $stores->store_name = $request->store_name;
       $stores->store_no = $request->store_no;
       $stores->description = $request->description;
       $results = $stores->update();

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            if ($results) {
                return ApiBaseMethod::sendResponse(null, 'Store has been updated successfully');
            } else {
                return ApiBaseMethod::sendError('Something went wrong, please try again');
            }
        } else {
            if ($results) {
                return redirect('item-store')->with('message-success', 'Store has been updated successfully');
            } else {
                return redirect()->back()->with('message-danger', 'Something went wrong, please try again');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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

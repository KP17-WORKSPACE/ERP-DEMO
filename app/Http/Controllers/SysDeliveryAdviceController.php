<?php

namespace App\Http\Controllers;

use App\SysChartofAccounts;
use App\SysAccountType;
use App\ApiBaseMethod;
use App\SysCashReceipt;
use App\SysCashReceiptList;
use App\SysCustSuppl;
use App\SysAccountGroup;
use App\SysCurrencySettings;
use App\SysDeliveryAdvice;
use App\SysDeliveryAdviceItems;
use App\SysSalesInvoice;
use App\SysSalesInvoiceItems;
use App\SysDeliveryNoteList;
use App\SysPaymentTerms;
use App\SysReceiptMode;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SysDeliveryAdviceController extends Controller
{
    public function __construct(){
        $this->middleware('PM');
    }
    

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function deliveryadviceList(Request $request)
    {
        try{
            $salesreturn = SysDeliveryAdvice::all();
            //$cashreceipt_list = SysCashReceiptList::all();
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse($salesreturn, null);
            }
            return view('backEnd.deliveryadvice.deliveryadvicelist', compact('salesreturn'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function deliveryadviceAdd(Request $request)
    {
        // $items = SysPurchaseInvoiceItems::select('sys_purchase_invoice_items.*', 'sm_items.part_number','sm_items.description')->join('sm_items','sys_purchase_invoice_items.part_number','sm_items.id')->whereIn('pi_id',[100,101])->get();
        // return $items;

        try{
            $accounts = SysChartofAccounts::all();
            $currency = SysCurrencySettings::all();            
            $customer = SysChartofAccounts::select('id','account_name')->where('subgroup',1)->get(); //1cust, 3supp
            $paymentterms = SysPaymentTerms::select('id','title')->get();
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse($accounts, null);
            }

            return view('backEnd.deliveryadvice.deliveryadviceadd', compact('accounts','currency','customer','paymentterms'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function get_si_list_delivery_advice(Request $request){     
        //$select_sub_category = SysSalesInvoice::all();
        $select_sub_category = SysSalesInvoice::select('id','doc_number')->where('customer',$request->cus_id)->get();
        return response()->json([$select_sub_category]);
    }

    public function get_si_list_for_delivery_advice(Request $request){
        //$ids[] = $request->pi_ids;
        $explode_id = array_map('intval', explode(',', $request->si_ids));
        
        $items = DB::select("CALL set_delivery_note($request->si_ids)");
        
        //$items = SysSalesInvoiceItems::select('sys_sales_invoice_items.*', 'sm_items.part_number','sm_items.description')->join('sm_items','sys_sales_invoice_items.part_number','sm_items.id')->whereIn('si_id',$explode_id)->where('delivery_status',0)->get();

		if(!empty($items)){
			return json_encode($items);
        }        
    }

    public function store(Request $request)
    {
        //return $request;
        $input = $request->all();
        $validator = Validator::make($input, [
            'doc_number'=> "required",
            'doc_date'=> "required",
            'currency'=> "required",

        ]);

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try{
            $mxid = SysDeliveryAdvice::max('id');
            $dn = new SysDeliveryAdvice();
            
            $dn->doc_number = 'DAV-'.($mxid+1);
            $dn->doc_date = date('Y-m-d', strtotime($request->doc_date));
            $dn->customer_id = $request->customer_id;
            $dn->narration = $request->narration;
            $dn->salesman = $request->salesman;
            $dn->contact_person = $request->contact_person;
            $dn->mobile_no = $request->mobile_no;
            $dn->landline_no = $request->landline_no;
            $dn->da_si_numbers = $request->da_si_numbers;
            $dn->invoice_date = date('Y-m-d', strtotime($request->invoice_date));
            $dn->vehicle_no = $request->vehicle_no;
            $dn->driver = $request->driver;
            $dn->do_no = $request->do_no;
            $dn->do_date = date('Y-m-d', strtotime($request->do_date));
            $dn->payment_terms = $request->payment_terms;
            $dn->delivery_date = date('Y-m-d', strtotime($request->delivery_date));
            $dn->delivery_time = $request->delivery_time;
            $dn->delivery_address = $request->delivery_address;
            $dn->invoice_amount = $request->invoice_amount;
            $dn->remarks = $request->remarks;
            $dn->status = 1;
            $dn->created_by = Auth::user()->id;
            $results = $dn->save();
            $dn->toArray();

            for($i = 0; $i < count($request->part_number); $i++) {
                if($request->part_number[$i] !=""){
                    $dnl = new SysDeliveryAdviceItems();
                    $dnl->da_id = $dn->id;
                    $dnl->part_number = $request->part_number[$i];
                    $dnl->qty = $request->qty[$i];
                    $dnl->unitprice = $request->unitprice[$i];
                    $dnl->da_value = $request->da_value[$i];
                    $dnl->status = 1;
                    $dnl->created_by = Auth::user()->id;
                    $dnl->save();
                }
            }

     
             if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                 if ($results) {
                     return ApiBaseMethod::sendResponse(null, 'Sales Return has been added successfully');
                 } else {
                     return ApiBaseMethod::sendError('Something went wrong, please try again');
                 }
             } else {
                 if ($results) {
                    Toastr::success('Operation successful', 'Success');
                    return redirect()->back();
                 } else {
                    Toastr::error('Operation Failed', 'Failed');
                    return redirect()->back(); 
                 }
             }
        }catch (\Exception $e) {
            return $e;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function edit(Request $request,$id)
    {
       try{
        $accounts = SysChartofAccounts::all();
        $currency = SysCurrencySettings::all();
        $editData = SysSalesReturn::find($id);
        $editDataList = SysSalesReturnList::where('sr_id',$id)->get();

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
             $data = [];
             $data['editData'] = $editData->toArray();
             $data['editDataList'] = $editDataList->toArray();
             return ApiBaseMethod::sendResponse($data, null);
        }
        return view('backEnd.salesreturn.salesreturnadd', compact('accounts', 'currency', 'editData','editDataList'));
        }catch (\Exception $e) {
        Toastr::error('Operation Failed', 'Failed');
        return redirect()->back(); 
        }
    }
    public function view(Request $request,$id)
    {
       try{
        $accounts = SysChartofAccounts::all();
        $currency = SysCurrencySettings::all();
        $editData = SysSalesReturn::find($id);
        $editDataList = SysSalesReturnList::where('jv_id',$id)->get();        
        $view = 'yes';

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
             $data = [];
             $data['editData'] = $editData->toArray();
             $data['editDataList'] = $editDataList->toArray();
             return ApiBaseMethod::sendResponse($data, null);
        }
        return view('backEnd.salesreturn.salesreturnadd', compact('accounts', 'currency', 'editData','editDataList','view'));
        }catch (\Exception $e) {
        Toastr::error('Operation Failed', 'Failed');
        return redirect()->back(); 
        }
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'doc_number'=> "required",
            'doc_date'=> "required",
            'currency'=> "required",
            'narration'=> "required",
        ]);

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        DB::beginTransaction();
        try {

            $pr = SysSalesReturn::find($id);
            $pr->doc_date = date('Y-m-d', strtotime($request->doc_date));
            $pr->currency = $request->currency;
            $pr->narration = $request->narration;
            $pr->supplier_id = $request->supplier_id;
            $pr->supplier_reference = $request->supplier_reference;
            $pr->supplier_country = $request->supplier_country;
            $pr->supplier_state = $request->supplier_state;
            $pr->purchase_type = $request->purchase_type;
            $pr->status = 1;
            $pr->updated_by = Auth::user()->id;
            $pr->update();

            SysSalesReturnList::query()
                ->where('sr_id','=', $id)
                ->each(function ($oldRecord) {
                $newRecord = $oldRecord->replicate();
                $newRecord->setTable('sys_sales_return_list_history');
                $newRecord->save();
                $oldRecord->delete();
                });

            for($i = 0; $i < count($request->account_id); $i++) {
                if($request->account_id[$i] !=""){
                    $prl = new SysSalesReturnList();
                    $prl->sr_id = $id;                    
                    $prl->si_id_ref = $request->si_id_ref[$i];
                    $prl->partno = $request->partno[$i];
                    $prl->qty = $request->qty[$i];
                    $prl->unitprice = $request->unitprice[$i];
                    $prl->value = $request->value[$i];
                    $prl->taxableamount = $request->taxableamount[$i];
                    $prl->vat = $request->vat[$i];
                    $prl->vatamount = $request->vatamount[$i];
                    $prl->remarks = $request->remarks[$i];
                    $prl->serialno = $request->serialno[$i];
                    $prl->status = 1;
                    $prl->created_by = Auth::user()->id;
                    $prl->save();
                }
            }

            DB::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect('salesreturn/'.$id.'/edit');
            
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    public function delete(Request $request,$id){
        
        //  try{
        //     if (ApiBaseMethod::checkUrl($request->fullUrl())) {
        //         return ApiBaseMethod::sendResponse($id, null);
        //     }
        //      return view('backEnd.inventory.deleteSupportView', compact('id'));
        // }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        //}
    }

    // public function deleteSupplier(Request $request,$id){
        
    //     try{
    //         $result = SmSupplier::destroy($id);

    //         if (ApiBaseMethod::checkUrl($request->fullUrl())) {
    //             if ($result) {
    //                 return ApiBaseMethod::sendResponse(null, 'Supplier has been deleted successfully');
    //             } else {
    //                 return ApiBaseMethod::sendError('Something went wrong, please try again.');
    //             }
    //         } else {
    //             if ($result) {
    //                 Toastr::success('Operation successful', 'Success');
    //                 return redirect('suppliers');
    //             } else {
    //                 Toastr::error('Operation Failed', 'Failed');
    //                 return redirect()->back();
    //             }
    //         }
    //     }catch (\Exception $e) {
    //        Toastr::error('Operation Failed', 'Failed');
    //        return redirect()->back(); 
    //     }
    // }
}
<?php

namespace App\Http\Controllers;

use App\SysChartofAccounts;
use App\SysAccountType;
use App\ApiBaseMethod;
use App\SysBankPayment;
use App\SysCashReceiptList;
use App\SysCustSuppl;
use App\SysAccountGroup;
use App\SysChartofAccountsDetails;
use App\SysChartofAccountsTransaction;
use App\SysCurrencySettings;
use App\SysHelper;
use App\SysLedgerEntries;
use App\SysLedgerEntriesTemp;
use App\SysPurchaseInvoice;
use App\SysReceiptAdjustments;
use App\SysReceiptAdjustmentsTemp;
use App\SysReceiptMode;
use App\SysSalesInvoice;
use App\SysTransactions;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SysBankPaymentController extends Controller
{
    public function __construct(){
        $this->middleware('PM');
    }
    

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function bankpaymentList(Request $request)
    {
        try{
            $com_ids = SysHelper::get_company_access();
            $bankpayment = SysBankPayment::wherein('company_id',$com_ids)->get();
            //$cashreceipt_list = SysCashReceiptList::all();
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse($bankpayment, null);
            }
            return view('backEnd.bank-payment.bankpaymentlist', compact('bankpayment'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function bankpaymentAdd(Request $request)
    {
        try{
            //$cashreceipt = SysCashReceipt::all();
            //$cashreceipt_list = SysCashReceiptList::all();
            //$cust = SysCustSuppl::where('catid',1)->get(); //1cust, 2supp
            
            $cust = SysChartofAccounts::select('id','account_name')->where('status',1)->orderby('account_name','asc')->get(); //1cust, 3supp
            $receiptmode = SysChartofAccounts::select('id','account_name')->where('subgroup2',16)->where('status',1)->orderby('account_name','asc')->get(); // 16 cash at bank
            $currency       = SysCurrencySettings::select('id','code')->get();
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse($cust, null);
            }
            return view('backEnd.bank-payment.bankpaymentadd', compact('cust','receiptmode','currency'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back();
        }
    }

    public function getbpcustlist(){
		$items = SysChartofAccounts::select('id','account_name')->where('status',1)->orderby('account_name','asc')->get();
		$searchData = [];
		foreach($items as $item){
			$searchData[] =  ['id' => $item->id, 'name' => $item->account_name];
		}

		if(!empty($searchData)){
			return json_encode($searchData);
		}
    }
    
    public function store(Request $request)
    {
        try{
            DB::beginTransaction();
            if(count(array_filter($request->account_id))>0 && count(array_filter($request->amount))>0){
                $mxid = SysBankPayment::max('id');
                $bp = new SysBankPayment();
                $bp->doc_number = 'BP-'.($mxid+1);
                $bp->doc_date = date('Y-m-d', strtotime($request->doc_date));
                $bp->payment_mode = $request->payment_mode;
                $bp->cheque_date = date('Y-m-d', strtotime($request->cheque_date));
                $bp->cheque_number = $request->cheque_number;
                $bp->cheque_bank_name = $request->cheque_bank_name;
                $bp->currency = $request->currency;
                $bp->narration = $request->narration;
                $bp->status = 1;
                $bp->created_by = Auth::user()->id;
                $bp->created_at = Carbon::now('+04:00');
                $bp->company_id = session('logged_session_data.company_id');
                $results = $bp->save();
                $bp->toArray();

                SysHelper::trn_chartof_accounts_transaction($request->payment_mode,$bp->id,$bp->doc_number,$bp->doc_date,'bankpayment','0.00',array_sum($request->amount),$request->narration,1);

                for($i = 0; $i < count($request->account_id); $i++) {
                    if($request->account_id[$i] !="" && $request->amount[$i] !=""){
                        SysHelper::trn_chartof_accounts_transaction($request->account_id[$i],$bp->id,$bp->doc_number,$bp->doc_date,'bankpayment',$request->amount[$i],'0.00',$request->remarks[$i],1);
                    }
                }
                DB::commit();
                Toastr::success('Operation successful', 'Success');
                return redirect()->back();
            }
            else{
                Toastr::error('Operation Failed. please enter valid data', 'Failed');
                return redirect()->back();
            }
        }catch (\Exception $e) {
            DB::rollback();
            return $e;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function edit(Request $request,$id)
    {
       try{
        $cust = SysChartofAccounts::select('id','account_name')->where('status',1)->orderby('account_name','asc')->get(); //1cust, 3supp
        $receiptmode = SysChartofAccounts::select('id','account_name')->where('subgroup2',16)->where('status',1)->orderby('account_name','asc')->get(); // 16 cash at bank
        foreach($receiptmode as $receipt){$acc_id[]=$receipt->id;}
        $currency       = SysCurrencySettings::select('id','code')->get();
        
        $editData = SysBankPayment::find($id);
        $editDataList = SysChartofAccountsTransaction::where(['transaction_id' => $id, 'transaction_type' => 'bankpayment'])->whereNotIn('account_id',$acc_id)->get();    

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
             $data = [];
             $data['editData'] = $editData->toArray();
             $data['editDataList'] = $editDataList->toArray();
             return ApiBaseMethod::sendResponse($data, null);
        }
        return view('backEnd.bank-payment.bankpaymentadd', compact('cust', 'receiptmode', 'editData','editDataList','currency'));
        }catch (\Exception $e) {
        Toastr::error('Operation Failed', 'Failed');
        return redirect()->back(); 
        }
    }
    public function view(Request $request,$id)
    {
       try{
        $cust = SysChartofAccounts::select('id','account_name')->where('subgroup',3)->where('status',1)->get(); //1cust, 3supp
        $receiptmode = SysChartofAccounts::select('id','account_name')->where('subgroup2',16)->where('status',1)->get(); // 16 cash at bank
        
        $editData = SysBankPayment::find($id);
        $editDataList = SysTransactions::where('transaction_id',$id)->get();
        $view = 'yes';
        
        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
             $data = [];
             $data['editData'] = $editData->toArray();
             $data['editDataList'] = $editDataList->toArray();
             return ApiBaseMethod::sendResponse($data, null);
        }
        return view('backEnd.bank-payment.bankpaymentadd', compact('cust', 'receiptmode', 'editData','editDataList','view'));
        }catch (\Exception $e) {
        Toastr::error('Operation Failed', 'Failed');
        return redirect()->back(); 
        }
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $bp = SysBankPayment::find($id);
            $bp->doc_date = date('Y-m-d', strtotime($request->doc_date));
            $bp->payment_mode = $request->payment_mode;
            $bp->cheque_date = date('Y-m-d', strtotime($request->cheque_date));
            $bp->cheque_number = $request->cheque_number;
            $bp->cheque_bank_name = $request->cheque_bank_name;
            $bp->currency = $request->currency;
            $bp->narration = $request->narration;
            $bp->status = 1;
            $bp->updated_by = Auth::user()->id;
            $bp->updated_at = Carbon::now('+04:00');
            $bp->company_id = session('logged_session_data.company_id');
            $bp->update();

            SysChartofAccountsTransaction::query()
                ->where(['transaction_id' => $id, 'transaction_type' => 'bankpayment'])
                ->each(function ($oldRecord) {
                $newRecord = $oldRecord->replicate();
                $newRecord->setTable('sys_chartofaccounts_transaction_history');
                $newRecord->save();
                $oldRecord->delete();
                });

                SysHelper::trn_chartof_accounts_transaction($request->payment_mode,$bp->id,$bp->doc_number,$bp->doc_date,'bankpayment','0.00',array_sum($request->amount),$request->narration,1);

                for($i = 0; $i < count($request->account_id); $i++) {
                    if($request->account_id[$i] !="" && $request->amount[$i] !=""){
                        SysHelper::trn_chartof_accounts_transaction($request->account_id[$i],$bp->id,$bp->doc_number,$bp->doc_date,'bankpayment',$request->amount[$i],'0.00',$request->remarks[$i],1);
                    }
                }

            DB::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect('bankpayment/'.$id.'/edit');
            
        } catch (\Exception $e) {
            DB::rollback();
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    // public function receiptadjustmentsstore(Request $request)
    // {
    //     try{
    //     $sra = new SysReceiptAdjustments();
    //     $sra->bi_new_reference = $request->bi_new_reference;
    //     $sra->bi_amount_to_adjust = $request->bi_amount_to_adjust;
    //     $sra->bi_adjusted_amount = $request->bi_adjusted_amount;
    //     $sra->bi_currency = $request->bi_currency;
    //     $sra->bi_doc_number = $request->bi_doc_number;
    //     $sra->bi_contains = $request->bi_contains;
    //     $sra->bi_doc_no = $request->bi_doc_no;
    //     $sra->bi_doc_date = date('Y-m-d', strtotime($request->bi_doc_date));
    //     $sra->bi_lpo_no = $request->bi_lpo_no;
    //     $sra->bi_due_date = date('Y-m-d', strtotime($request->bi_due_date));
    //     $sra->bi_total = $request->bi_total;
    //     $sra->bi_paid = $request->bi_amount;
    //     $sra->bi_balance = $request->bi_balance;
    //     $sra->bi_amount = $request->bi_amount;
    //     $sra->status = 1;
    //     $sra->created_by = Auth::user()->id;
    //     $results = $sra->save();
    //     $sra->toArray();
        
    //     $sle = new SysLedgerEntries();
    //     $sle->transaction_id = $request->bi_doc_number;
    //     $sle->transaction_type = "bankreceipt";
    //     $sle->account_id = $request->account_id;
    //     $sle->entry_date = date('Y-m-d', strtotime($request->entry_date));
    //     $sle->entry_type = 1; //Debit
    //     $sle->amount = $request->bi_amount;
    //     $sle->status = 1;
    //     $sle->created_by = Auth::user()->id;
    //     $sle->save();
        
    //     $ret = 'SUCCESS';
    //         return json_encode(array('data'=>$ret));
    //     }catch (\Exception $e) {
    //         $ret = $e;
    //         return json_encode(array('data'=>$ret));
    //     }
    // }

    public function getbpbalancelist(Request $request)
    {
        $items = DB::select("CALL get_bank_payment_adjestments($request->account_id)");

        // $items = SysSalesInvoice::select('sys_sales_invoice.doc_number', 'sys_sales_invoice.si_date', 'sys_sales_invoice.lpo_number','sys_sales_invoice.lpo_date', DB::raw('SUM(sys_sales_invoice_items.taxableamount) as amount'))
        // ->join('sys_sales_invoice_items', 'sys_sales_invoice.id', '=', 'sys_sales_invoice_items.si_id')
        // ->where('sys_sales_invoice.customer',$request->cr_account_id)
        // ->groupBy('sys_sales_invoice.id')
        // ->groupBy('sys_sales_invoice.doc_number')
        // ->groupBy('sys_sales_invoice.si_date')
        // ->groupBy('sys_sales_invoice.lpo_number')
        // ->groupBy('sys_sales_invoice.lpo_date')
        // ->get();

        //$items = SysCustSuppl::select('id','name')->where('catid',1)->get();

		$searchData = [];
		foreach($items as $item){
			$searchData[] =  [
                'doc_number' => $item->doc_number,
                'pi_date' => $item->pi_date,
                'lpo_number' => $item->lpo_number,
                'lpo_date' => $item->lpo_date,
                'total' => $item->total,
                'paid' => $item->paid,
                'balance' => $item->balance,
            ];
		}

		if(!empty($searchData)){
			return json_encode($searchData);
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
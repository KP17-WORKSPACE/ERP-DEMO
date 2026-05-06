<?php

namespace App\Http\Controllers;

use Auth;
use Validator;
use App\SmAddIncome;
use App\SmAddExpense;
use App\SmCostCenter;
use App\SmSubAccount;
use App\ApiBaseMethod;
use App\SmBankAccount;
use App\SmExpenseHead;
use App\SmFundTransfer;
use App\SmChartOfAccount;
use App\SmPaymentMethhod;
use App\SmGeneralSettings;
use App\SmHrPayrollGenerate;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;

class SmAddExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
    }

    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $add_expenses = SmAddExpense::where('active_status', 1)->get();
        $expense_heads = SmChartOfAccount::where('type', "E")->where('active_status', 1)->get();
        $bank_accounts = SmbankAccount::where('active_status', '=', 1)->get();
        $payment_methods = SmPaymentMethhod::where('active_status', '=', 1)->get();
        $cost_centers = SmCostCenter::where('active_status', '=', 1)->get();


        $opening_balance = SmBankAccount::find(1);
        $incomes = SmAddIncome::where('payment_method_id', 3)->where('account_id', 1)->sum('amount');
        $expenses = SmAddExpense::where('payment_method_id', 3)->where('account_id', 1)->where('status', 1)->sum('amount');

        if(ApiBaseMethod::checkUrl($request->fullUrl())){
            $data=[];
            $data['add_expenses']= $add_expenses->toArray();
            $data['expense_heads']= $expense_heads->toArray();
            $data['bank_accounts']= $bank_accounts->toArray();
            $data['payment_methods']= $payment_methods->toArray();
            return ApiBaseMethod::sendResponse($data, null);
        }
        return view('backEnd.accounts.add_expense', compact('add_expenses', 'expense_heads', 'bank_accounts', 'payment_methods', 'cost_centers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();

        if($request->payment_method == "3"){
            $validator = Validator::make($input, [
                'expense_head' => "required",
                'name' => "required",
                'date' => "required",
                'accounts' => "required",
                'payment_method' => "required",
                'amount' => "required"
            ]);
        }else{
            $validator = Validator::make($input, [
                'expense_head' => "required",
                'name' => "required",
                'date' => "required",
                'payment_method' => "required",
                'amount' => "required"
            ]);
        }

        if($validator->fails()){
            if(ApiBaseMethod::checkUrl($request->fullUrl())){
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
try{


        $fileName = ""; 
        if($request->file('file') != ""){
            $file = $request->file('file');
            $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
            $file->move('public/uploads/addExpense/', $fileName);
            $fileName =  'public/uploads/addExpense/'.$fileName;
        }

        $add_expense = new SmAddExpense();
        $add_expense->name = $request->name;
        
        $add_expense->expense_head_id = $request->expense_head;
        $add_expense->expense_sub_head_id = (int)$request->sub_head;

        $add_expense->date = date('Y-m-d',strtotime($request->date));
        $add_expense->payment_method_id = $request->payment_method;
        if($request->payment_method == "3"){
            $add_expense->account_id = $request->accounts;
        }
        $add_expense->amount = $request->amount;
        $add_expense->cost_center_id =(int) $request->cost_center;
        $add_expense->file = $fileName;
        $add_expense->description = $request->description;

        if(Auth::user()->role_id == 1){

            $add_expense->status = 1;
        }


        $add_expense->created_by =Auth::user()->id;
        $result = $add_expense->save();



// store user all activities 
      $data = SmAddExpense::find($add_expense->id);
      $data['note'] = '"Expense' . $request->expense_head. '" has been added.';
      $data['model_name'] = 'SmAddExpense';
      $data['old_data'] = $data->toJson();
      $data['new_data'] = '';
      $data['action'] = 'Insert';
      $data['action_id'] = $data->id;
      $result = SmGeneralSettings::StoreAllActivities($data);
// end store user all activities 


        if(ApiBaseMethod::checkUrl($request->fullUrl())){
            if($result){
                return ApiBaseMethod::sendResponse(null, 'Expense has been created successfully');
            }else{
                return ApiBaseMethod::sendError('Something went wrong, please try again.');
            }
        }else{
            if($result){
                return redirect()->back()->with('message-success', 'Expense has been created successfully');
            }else{
                return redirect()->back()->with('message-danger', 'Something went wrong, please try again');
            }
        }
    } catch (\Illuminate\Database\QueryException $q) {
        Toastr::error('Operation Failed For Query Exception', 'Failed');
        return redirect()->back();
    }catch (\Exception $e) {
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
    public function show(Request $request,$id)
    {
        $add_expense = SmAddExpense::find($id);
        $add_expenses = SmAddExpense::where('active_status', '=', 1)->get();
        $expense_heads = SmChartOfAccount::where('type', "E")->where('active_status', 1)->get();
        $bank_accounts = SmbankAccount::where('active_status', '=', 1)->get();
        $payment_methods = SmPaymentMethhod::where('active_status', '=', 1)->get();
        $cost_centers = SmCostCenter::where('active_status', '=', 1)->get();


        $opening_balance = SmBankAccount::find(1);

        $incomes = SmAddIncome::where('payment_method_id', 3)->where('account_id', 1)->sum('amount');

        $expenses = SmAddExpense::where('payment_method_id', 3)->where('account_id', 1)->where('status', 1)->sum('amount');

        $previous_amount = 0;
        if($add_expense->payment_method_id == 3 && $add_expense->account_id == 1 && $add_expense->status == 1){
          $previous_amount = $add_expense->amount;
        }





        $sub_heads = [];
        if($add_expense->expense_head_id != ""){
            $sub_heads = SmSubAccount::where('head_id', $add_expense->expense_head_id)->get();
        }

        if(ApiBaseMethod::checkUrl($request->fullUrl())){
            $data=[];
            $data['add_expenses']= $add_expenses->toArray();
            $data['add_expense']= $add_expense->toArray();
            $data['expense_heads']= $expense_heads->toArray();
            $data['bank_accounts']= $bank_accounts->toArray();
            $data['payment_methods']= $payment_methods->toArray();
            return ApiBaseMethod::sendResponse($data, null);
        }
        return view('backEnd.accounts.add_expense', compact('add_expenses', 'add_expense', 'expense_heads', 'bank_accounts', 'payment_methods', 'cost_centers', 'sub_heads'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        if($request->payment_method == "3"){
            $validator = Validator::make($input, [
                'expense_head' => "required",
                'name' => "required",
                'date' => "required",
                'accounts' => "required",
                'payment_method' => "required",
                'amount' => "required"
            ]);
        }else{
            $validator = Validator::make($input, [
                'expense_head' => "required",
                'name' => "required",
                'date' => "required",
                'payment_method' => "required",
                'amount' => "required"
            ]);
        }

        if($validator->fails()){
            if(ApiBaseMethod::checkUrl($request->fullUrl())){
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
try{


        $fileName = ""; 
        if($request->file('file') != ""){
            $add_expense = SmAddExpense::find($request->id);
            if(file_exists($add_expense->file)){
              unlink($add_expense->file);
            }
            

            $file = $request->file('file');
            $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
            $file->move('public/uploads/addExpense/', $fileName);
            $fileName =  'public/uploads/addExpense/'.$fileName;
        }


        $old_data1=$add_expense = SmAddExpense::find($request->id);
        $add_expense->name = $request->name;
        $add_expense->expense_head_id =(int) $request->expense_head;
        $add_expense->expense_sub_head_id =(int) $request->sub_head;
        $add_expense->date = date('Y-m-d',strtotime($request->date));
        $add_expense->payment_method_id = $request->payment_method;
        if($request->payment_method == "3"){
            $add_expense->account_id = $request->accounts;
        }
        $add_expense->amount = $request->amount;
        $add_expense->cost_center_id =(int) $request->cost_center;

        if($fileName != ""){
            $add_expense->file = $fileName;
        }

        if(isset($request->status)){
            $add_expense->status = $request->status;
        }

        $add_expense->description = $request->description;
        $add_expense->updated_by =Auth::user()->id;
        $result = $add_expense->save();



// store user all activities 
      $data1 = SmAddExpense::find($add_expense->id);
      $data['note'] = '"Expense ' . $request->expense_head. '" has been updated.';
      $data['model_name'] = 'SmAddExpense';
      $data['old_data'] = $old_data1->toJson();
      $data['new_data'] = $data1->toJson();
      $data['action'] = 'Edit';
      $data['action_id'] = $data1->id;
      $result = SmGeneralSettings::StoreAllActivities($data);
// end store user all activities 



        if(ApiBaseMethod::checkUrl($request->fullUrl())){
            if($result){
                return ApiBaseMethod::sendResponse(null, 'Expense has been updated successfully');
            }else{
                return ApiBaseMethod::sendError('Something went wrong, please try again.');
            }
        }else{
            if($result){
                return redirect('add-expense')->with('message-success', 'Expense has been updated successfully');
            }else{
                return redirect()->back()->with('message-danger', 'Something went wrong, please try again');
            }
        }
    } catch (\Illuminate\Database\QueryException $q) {
        Toastr::error('Operation Failed For Query Exception', 'Failed');
        return redirect()->back();
    }catch (\Exception $e) {
       Toastr::error('Operation Failed', 'Failed');
       return redirect()->back(); 
    }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        try{

       
        $add_expense = SmAddExpense::find($id);



        // store user all activities 
              $data = SmAddExpense::find($add_expense->id);
              $data['note'] = '"Expense ' . $data->expense_head. '" has been Deleted.';
              $data['model_name'] = 'SmAddExpense';
              $data['old_data'] = $data->toJson();
              $data['new_data'] = '';
              $data['action'] = 'Delete';
              $data['action_id'] = $data->id;
              $result = SmGeneralSettings::StoreAllActivities($data);
        // end store user all activities 


        if($add_expense->file != ""){
          if(file_exists($add_expense->file)){
            
            unlink($add_expense->file);
          }
        }

        
        $result = $add_expense->delete();

        if(ApiBaseMethod::checkUrl($request->fullUrl())){
            if($result){
                return ApiBaseMethod::sendResponse(null, 'Expense has been deleted successfully');
            }else{
                return ApiBaseMethod::sendError('Something went wrong, please try again.');
            }
        }else{
            if($result){
                return redirect('add-expense')->with('message-success-delete', 'Expense has been deleted successfully');
            }else{
                return redirect()->back()->with('message-danger-delete', 'Something went wrong, please try again');
            }
        }
    } catch (\Illuminate\Database\QueryException $q) {
        Toastr::error('Operation Failed For Query Exception', 'Failed');
        return redirect()->back();
    }catch (\Exception $e) {
       Toastr::error('Operation Failed', 'Failed');
       return redirect()->back(); 
    }
    }


    public function ajaxGetBankBalance(Request $request){


      $opening_balance = SmBankAccount::find($request->bank_id);

      $incomes = SmAddIncome::where('payment_method_id', 3)->where('account_id', $request->bank_id)->sum('amount');

      $expenses = SmAddExpense::where('payment_method_id', 3)->where('account_id', $request->bank_id)->where('status', 1)->sum('amount');

      $payroll_amount = SmHrPayrollGenerate::where('payment_mode', 3)->where('account_id', $request->bank_id)->sum('net_salary');


      $transfer_amount = SmFundTransfer::where('bank_account_id', $request->bank_id)->sum('amount');

      $total_balance = $opening_balance->opening_balance + $incomes + $transfer_amount - $expenses - $payroll_amount;



      // $balance = 0;
      // if($total_balance != ""){
      //   $balance = $total_balance;
      // }

      return response()->json($total_balance);
    }
}

<?php

namespace App\Http\Controllers;

use App\ApiBaseMethod;
use App\SmGeneralSettings;
use Illuminate\Http\Request;
use App\SmAddIncome;
use App\SmIncomeHead;
use App\SmChartOfAccount;
use App\SmBankAccount;
use App\SmPaymentMethhod;
use App\SmSubAccount;
use Validator;
use Auth;
class SmAddIncomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
    }


    public function index(Request $request){
    	$add_incomes = SmAddIncome::where('active_status', '=', 1)->get();
        $income_heads = SmChartOfAccount::where('type', "I")->where('active_status', '=', 1)->get();
        $bank_accounts = SmbankAccount::where('active_status', '=', 1)->get();
    	$payment_methods = SmPaymentMethhod::where('active_status', '=', 1)->get();

        if(ApiBaseMethod::checkUrl($request->fullUrl())){
            $data=[];
            $data['add_incomes']= $add_incomes->toArray();
            $data['income_heads']= $income_heads->toArray();
            $data['bank_accounts']= $bank_accounts->toArray();
            $data['payment_methods']= $payment_methods->toArray();
            return ApiBaseMethod::sendResponse($data, null);
        }

    	return view('backEnd.accounts.add_income', compact('add_incomes', 'income_heads', 'bank_accounts', 'payment_methods'));

    }
    public function store(Request $request){
        $input = $request->all();

        if($request->payment_method == "3"){
            $validator = Validator::make($input, [
                'income_head' => "required",
                'sub_head' => "required",
                'name' => "required",
                'date' => "required",
                'accounts' => "required",
                'payment_method' => "required",
                'amount' => "required"
            ]);
        }else{
            $validator = Validator::make($input, [
                'income_head' => "required",
                'sub_head' => "required",
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

    	$fileName = ""; 
    	if($request->file('file') != ""){
    		$file = $request->file('file');
	        $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
	        $file->move('public/uploads/add_income/', $fileName);
	        $fileName =  'public/uploads/add_income/'.$fileName;
    	}

    	$date = strtotime($request->date);
		$newformat = date('Y-m-d',$date);

    	$add_income = new SmAddIncome();
    	$add_income->name = $request->name;
    	$add_income->income_head_id = $request->income_head;
        $add_income->income_sub_head_id = $request->sub_head;
        $add_income->date = $newformat;
        $add_income->payment_method_id = $request->payment_method;
        if($request->payment_method == "3"){
        	$add_income->account_id = $request->accounts;
        }

    	$add_income->amount = $request->amount;
    	$add_income->file = $fileName;
    	$add_income->description = $request->description;
        $add_income->created_by =Auth::user()->id;
    	$result = $add_income->save();




// store user all activities 
      $data1 = SmAddIncome::find($add_income->id);
      $data['note'] = '"Income ' . $request->name. '" has been added.';
      $data['model_name'] = 'SmAddIncome';
      $data['old_data'] = $data1->toJson();
      $data['new_data'] = '';
      $data['action'] = 'Insert';
      $data['action_id'] = $data1->id;
      $result = SmGeneralSettings::StoreAllActivities($data);
// end store user all activities 



        if(ApiBaseMethod::checkUrl($request->fullUrl())){
            if($result){
                return ApiBaseMethod::sendResponse(null, 'Income has been created successfully');
            }else{
                return ApiBaseMethod::sendError('Something went wrong, please try again.');
            }
        }else{
            if($result){
                return redirect()->back()->with('message-success', 'Income has been created successfully');
            }else{
                return redirect()->back()->with('message-danger', 'Something went wrong, please try again');
            }
        }
    }

    public function edit(Request $request,$id){
    	$add_income = SmAddIncome::find($id);
      
    	$add_incomes = SmAddIncome::where('active_status', 1)->get();
    	$income_heads = SmChartOfAccount::where('active_status', '=', 1)->get();


        $sub_heads = SmSubAccount::where('head_id', $add_income->income_head_id)->get();

        $bank_accounts = SmbankAccount::where('active_status', '=', 1)->get();
        $payment_methods = SmPaymentMethhod::where('active_status', '=', 1)->get();

        if(ApiBaseMethod::checkUrl($request->fullUrl())){
            $data=[];
            $data['add_income']= $add_income->toArray();
            $data['add_incomes']= $add_incomes->toArray();
            $data['income_heads']= $income_heads->toArray();
            $data['bank_accounts']= $bank_accounts->toArray();
            $data['payment_methods']= $payment_methods->toArray();
            return ApiBaseMethod::sendResponse($data, null);
        }
     	return view('backEnd.accounts.add_income', compact('add_income', 'add_incomes', 'income_heads', 'bank_accounts', 'payment_methods', 'sub_heads'));
    }

    
    public function update(Request $request){
        $input = $request->all();
    	if($request->payment_method == "3"){
            $validator = Validator::make($input, [
                'sub_head' => "required",
                'income_head' => "required",
                'name' => "required",
                'date' => "required",
                'accounts' => "required",
                'payment_method' => "required",
                'amount' => "required"
            ]);
        }else{
            $validator = Validator::make($input, [
                'sub_head' => "required",
                'income_head' => "required",
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

    	$fileName = ""; 
    	if($request->file('file') != ""){

    		$add_income = SmAddIncome::find($request->id);
    		if($add_income->file != "" && file_exists($add_income->file)){

    			unlink($add_income->file);
    		}

    		$file = $request->file('file');
	        $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
	        $file->move('public/uploads/add_income/', $fileName);
	        $fileName =  'public/uploads/add_income/'.$fileName;
    	}

    	$date = strtotime($request->date);

		$newformat = date('Y-m-d',$date);

    	$old_data1=$add_income = SmAddIncome::find($request->id);
    	$add_income->name = $request->name;
        $add_income->income_head_id = $request->income_head;
        $add_income->income_sub_head_id = $request->sub_head;
        $add_income->date = $newformat;
        $add_income->payment_method_id = $request->payment_method;
        if($request->payment_method == "3"){
            $add_income->account_id = $request->accounts;
        }
        $add_income->amount = $request->amount;
    	if($request->file('file') != ""){
    		$add_income->file = $fileName;
    	}
    	$add_income->description = $request->description;
        $add_income->updated_by =Auth::user()->id; 
    	$result = $add_income->save();



// store user all activities 
      $data1 = SmAddIncome::find($request->id);
      $data['note'] = '"Income ' . $request->name. '" has been updated.';
      $data['model_name'] = 'SmAddIncome';
      $data['old_data'] = $old_data1->toJson();
      $data['new_data'] = $data1->toJson();
      $data['action'] = 'Edit';
      $data['action_id'] = $data1->id;
      $result = SmGeneralSettings::StoreAllActivities($data);
// end store user all activities 




        if(ApiBaseMethod::checkUrl($request->fullUrl())){
            if($result){
                return ApiBaseMethod::sendResponse(null, 'Income has been updated successfully');
            }else{
                return ApiBaseMethod::sendError('Something went wrong, please try again.');
            }
        }else{
            if($result){
                return redirect('add-income')->with('message-success', 'Income has been updated successfully');
            }else{
                return redirect()->back()->with('message-danger', 'Something went wrong, please try again');
            }
        }
    }
    public function delete(Request $request){
    	$add_income = SmAddIncome::find($request->id);




// store user all activities 
      $data1 = SmAddIncome::find($add_income->id);
      $data['note'] = '"Income ' . $data1->name. '" has been updated.';
      $data['model_name'] = 'SmAddIncome';
      $data['old_data'] = $data1->toJson();
      $data['new_data'] = '';
      $data['action'] = 'Delete';
      $data['action_id'] = $data1->id;
      $result = SmGeneralSettings::StoreAllActivities($data);
// end store user all activities 



        if($add_income->file != "" && file_exists($add_income->file)){
            unlink($add_income->file);
        }
        $result = $add_income->delete();

        if(ApiBaseMethod::checkUrl($request->fullUrl())){
            if($result){
                return ApiBaseMethod::sendResponse(null, 'Income has been deleted successfully');
            }else{
                return ApiBaseMethod::sendError('Something went wrong, please try again.');
            }
        }else{
            if($result){
                return redirect()->back()->with('message-success-delete', 'Income has been deleted successfully');
            }else{
                return redirect()->back()->with('message-danger-delete', 'Something went wrong, please try again');
            }
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\SmStaff;
use App\SmItemSell;
use App\SmAddIncome;
use App\SmAddExpense;
use App\SmCostCenter;
use App\SmSubAccount;
use App\ApiBaseMethod;
use App\SmDebitCredit;
use App\SmItemReceive;
use App\SmDailyExpense;
use App\SmChartOfAccount;
use App\SmGeneralSettings;
use App\SmHrPayrollGenerate;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;

class SmAccountsController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
    }

    public function searchAccount()
    {
        try{
            $income_heads = SmChartOfAccount::where('type', 'I')->get();
            $expense_heads = SmChartOfAccount::where('type', 'E')->get();
            return view('backEnd.accounts.search_income', compact('income_heads', 'expense_heads'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function searchAccountReportByDate(Request $request)
    {
        $request->validate([
            'type' => 'required'
        ]);
        
        try{
            $income_heads = SmChartOfAccount::where('type', 'I')->get();
            $expense_heads = SmChartOfAccount::where('type', 'E')->get();
            date_default_timezone_set("Asia/Dhaka");
            $date_from = date('Y-m-d', strtotime($request->date_from));
            $date_to = date('Y-m-d', strtotime($request->date_to));
            $date_time_from = date('Y-m-d H:i:s', strtotime($request->date_from));
            $date_time_to = date('Y-m-d H:i:s', strtotime($request->date_to . ' ' . '23:59:00'));
            $type_id = $request->type;
            $from_date = $request->date_from;
            $to_date = $request->date_to;
    
            if ($request->type == "In") {
                if ($request->filtering_income == "all") {
                    $dormitory = 0;
                    $transport = 0;
                    $fees_payments = null;
                    $add_incomes = SmAddIncome::where('date', '>=', $date_from)->where('date', '<=', $date_to)->where('active_status', 1)->get();
                    $item_sells = SmItemSell::where('updated_at', '>=', $date_time_from)->where('updated_at', '<=', $date_time_to)->where('active_status', 1)->sum('total_paid');
                } else {
                    $dormitory = 0;
                    $transport = 0;
                    $fees_payments = null;
                    $add_incomes = SmAddIncome::where('date', '>=', $date_from)->where('date', '<=', $date_to)->where('active_status', 1)->where('income_head_id', $request->filtering_income)->get();
                    $item_sells = SmItemSell::where('updated_at', '>=', $date_time_from)->where('updated_at', '<=', $date_time_to)->where('active_status', 1)->sum('total_paid');
                }
                return view('backEnd.accounts.search_income', compact('add_incomes', 'fees_payments', 'item_sells', 'dormitory', 'transport', 'type_id', 'from_date', 'to_date', 'income_heads', 'expense_heads'));
            } else {
                if ($request->filtering_expense == "all") {
                    $add_expenses = SmAddExpense::where('date', '>=', $date_from)->where('date', '<=', $date_to)->where('active_status', 1)->get();
                    $item_receives = SmItemReceive::where('updated_at', '>=', $date_time_from)->where('updated_at', '<=', $date_time_to)->where('active_status', 1)->sum('total_paid');
                    $payroll_payments = SmHrPayrollGenerate::where('updated_at', '>=', $date_time_from)->where('updated_at', '<=', $date_time_to)->where('active_status', 1)->where('payroll_status', 'P')->sum('net_salary');
                } else {
                    $add_expenses = SmAddExpense::where('date', '>=', $date_from)->where('date', '<=', $date_to)->where('active_status', 1)->where('expense_head_id', $request->filtering_expense)->get();
                    $daily_expenses = SmDailyExpense::where('date', '>=', $date_from)->where('date', '<=', $date_to)->where('active_status', 1)->where('head_id', $request->filtering_expense)->get();
                    $payroll_payments = '';
                    $item_receives = '';
                }
            }
            return view('backEnd.accounts.search_income', compact('add_expenses', 'item_receives', 'payroll_payments', 'type_id', 'from_date', 'to_date', 'income_heads', 'expense_heads'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function searchExpense()
    {
        try{
            return view('backEnd.accounts.search_expense');
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function searchExpenseReportByDate(Request $request)
    {
        try{
            date_default_timezone_set("Asia/Dhaka");
            $date_from = date('Y-m-d', strtotime($request->date_from));
            $date_to = date('Y-m-d', strtotime($request->date_to));
            $date_time_from = date('Y-m-d H:i:s', strtotime($request->date_from));
            $date_time_to = date('Y-m-d H:i:s', strtotime($request->date_to . ' ' . '23:59:00'));
            $add_expenses = SmAddExpense::where('date', '>=', $date_from)->where('date', '<=', $date_to)->where('active_status', 1)->get();
            $item_receives = SmItemReceive::where('updated_at', '>=', $date_time_from)->where('updated_at', '<=', $date_time_to)->where('active_status', 1)->sum('total_paid');
            $payroll_payments = SmHrPayrollGenerate::where('updated_at', '>=', $date_time_from)->where('updated_at', '<=', $date_time_to)->where('active_status', 1)->where('payroll_status', 'P')->sum('net_salary');
            return view('backEnd.accounts.search_expense', compact('add_expenses', 'item_receives', 'payroll_payments'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function profit(Request $request)
    {
        try{
            $add_incomes = SmAddIncome::where('active_status', 1)->sum('amount');
            $item_sells = SmItemSell::where('active_status', 1)->sum('total_paid');
            $total_income = $add_incomes + $item_sells;
            $add_expenses = SmAddExpense::where('active_status', 1)->sum('amount');
            $item_receives = SmItemReceive::where('active_status', 1)->sum('total_paid');
            $payroll_payments = SmHrPayrollGenerate::where('active_status', 1)->where('payroll_status', 'P')->sum('net_salary');
            $total_expense = $add_expenses + $item_receives + $payroll_payments;
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['total_income'] = $total_income;
                $data['total_expense'] = $total_expense;
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.accounts.profit', compact('total_income', 'total_expense'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function searchProfitByDate(Request $request)
    {
        try{
            date_default_timezone_set("Asia/Dhaka");
            $date_from = date('Y-m-d', strtotime($request->date_from));
            $date_to = date('Y-m-d', strtotime($request->date_to));
            $date_time_from = date('Y-m-d H:i:s', strtotime($request->date_from));
            $date_time_to = date('Y-m-d H:i:s', strtotime($request->date_to . ' ' . '23:59:00'));
            // Income
            $add_incomes = SmAddIncome::where('date', '>=', $date_from)->where('date', '<=', $date_to)->where('active_status', 1)->sum('amount');
            $item_sells = SmItemSell::where('updated_at', '>=', $date_time_from)->where('updated_at', '<=', $date_time_to)->where('active_status', 1)->sum('total_paid');
            $total_income = $add_incomes + $item_sells;
            // expense
            $add_expenses = SmAddExpense::where('date', '>=', $date_from)->where('date', '<=', $date_to)->where('active_status', 1)->sum('amount');
            $item_receives = SmItemReceive::where('updated_at', '>=', $date_time_from)->where('updated_at', '<=', $date_time_to)->where('active_status', 1)->sum('total_paid');
            $payroll_payments = SmHrPayrollGenerate::where('updated_at', '>=', $date_time_from)->where('updated_at', '<=', $date_time_to)->where('active_status', 1)->where('payroll_status', 'P')->sum('net_salary');
            // total profit
            $total_expense = $add_expenses + $item_receives + $payroll_payments;
    
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['total_income'] = $total_income;
                $data['total_expense'] = $total_expense;
                $data['date_from'] = $date_from;
                $data['date_to'] = $date_to;
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.accounts.profit', compact('total_income', 'total_expense', 'date_from', 'date_to'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }


    public function debitCreditVoucher()
    {
        try{
            $customers = SmStaff::where('role_id', 4)->get();
            $vouchers = SmDebitCredit::all();
            $max_voucher_no = SmDebitCredit::max('voucher_no');
            if ($max_voucher_no == "") {
                $max_voucher_no = 1001;
            } else {
                $max_voucher_no = $max_voucher_no + 1;
            }
            return view('backEnd.accounts.debitCreditVoucher', compact('vouchers', 'customers', 'max_voucher_no'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function debitCreditVoucherStore(Request $request)
    {
        $request->validate([
            'type' => 'required',
           
        ]);
        if ($request->type=='D') {
            $request->validate([
                'voucher_no' => 'required',
                'date' => 'required',
                'type' => 'required',
                'received_by' => 'required',
                'amount' => 'required',
            ]);
        }
        if ($request->type=='C') {
            $request->validate([
                'voucher_no' => 'required',
                'date' => 'required',
                'type' => 'required',
                'received_by' => 'required',
                'amount' => 'required',
                'customer' => 'required',
            ]);
        }
       
        try{
            $autho_fileName = "";
            if ($request->file('authorized_signature') != "") {
                $file = $request->file('authorized_signature');
                $autho_fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('public/uploads/voucher/', $autho_fileName);
                $autho_fileName =  'public/uploads/voucher/' . $autho_fileName;
            }
    
            $fileName = "";
            if ($request->file('accountant_signature') != "") {
                $file = $request->file('accountant_signature');
                $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('public/uploads/voucher/', $fileName);
                $fileName =  'public/uploads/voucher/' . $fileName;
            }
            $voucher = new SmDebitCredit();
            $voucher->voucher_no = $request->voucher_no;
            $voucher->date = date('Y-m-d', strtotime($request->date));
            $voucher->type = $request->type;
            $voucher->note = $request->note;
            if ($request->type == 'C') {
                $voucher->customer = $request->customer;
                $voucher->receiver = '';
            } else {
                $voucher->customer = '';
                $voucher->receiver = $request->received_by;
            }
    
            $voucher->company_or_address = $request->company_address;
            $voucher->amount = $request->amount;
            $voucher->authorised_signature = $autho_fileName;
            $voucher->accountant_signature = $fileName;
            $result = $voucher->save();
            $data = SmDebitCredit::find($voucher->id);
            $data['note'] = '"' . $data->voucher_no . '" has been added.';
            $data['model_name'] = 'SmDebitCredit';
            $data['old_data'] = $data->toJson();
            $data['new_data'] = '';
            $data['action'] = 'Insert';
            $data['action_id'] = $data->id;
            $result = SmGeneralSettings::StoreAllActivities($data);
            if ($result) {
                Toastr::success('Operation successful', 'Success');
                return redirect()->back();
            }
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function debitCreditVoucherEdit($id)
    {
        try{
            $voucher = SmDebitCredit::find($id);
            $customers = SmStaff::where('role_id', 4)->get();
            $vouchers = SmDebitCredit::all();
            return view('backEnd.accounts.debitCreditVoucher', compact('vouchers', 'customers', 'voucher'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function debitCreditVoucherUpdate(Request $request)
    {
        // return $request;
        $request->validate([
            'type' => 'required',
           
        ]);
        if ($request->type=='D') {
            $request->validate([
                'voucher_no' => 'required',
                'date' => 'required',
                'type' => 'required',
                'received_by' => 'required',
                'amount' => 'required',
            ]);
        }
        if ($request->type=='C') {
            $request->validate([
                'voucher_no' => 'required',
                'date' => 'required',
                'type' => 'required',
                'received_by' => 'required',
                'amount' => 'required',
                'customer' => 'required',
            ]);
        }

        try{
            $autho_fileName = "";
            if ($request->file('authorized_signature') != "") {
                $voucher = SmDebitCredit::find($request->id);
                if ($voucher->authorised_signature != "") {
                    if (file_exists($voucher->authorised_signature)) {
                        unlink($voucher->authorised_signature);
                    }
                }
                $file = $request->file('authorized_signature');
                $autho_fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('public/uploads/voucher/', $autho_fileName);
                $autho_fileName =  'public/uploads/voucher/' . $autho_fileName;
            }
    
            $fileName = "";
            if ($request->file('accountant_signature') != "") {
                $voucher = SmDebitCredit::find($request->id);
                if ($voucher->accountant_signature != "") {
                    if (file_exists($voucher->accountant_signature)) {
                        unlink($voucher->accountant_signature);
                    }
                }
                $file = $request->file('accountant_signature');
                $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('public/uploads/voucher/', $fileName);
                $fileName =  'public/uploads/voucher/' . $fileName;
            }
    
            $voucher = SmDebitCredit::findOrfail($request->id);
            $voucher->voucher_no = $request->voucher_no;
            $voucher->date = date('Y-m-d', strtotime($request->date));
            $voucher->type = $request->type;
            $voucher->note = $request->note;
            if ($request->type == 'C') {
                $voucher->customer = $request->customer;
                $voucher->receiver = '';
            } else {
                $voucher->customer = '';
                $voucher->receiver = $request->received_by;
            }
            $voucher->company_or_address = $request->company_address;
            $voucher->amount = $request->amount;
            $voucher->authorised_signature = $autho_fileName;
            $voucher->accountant_signature = $fileName;
            $result = $voucher->save();
            if ($result) {
                Toastr::success('Operation successful', 'Success');
                return redirect('debit-credit-voucher');
            }
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function debitCreditVoucherDelete($id)
    {
        try{
            $voucher = SmDebitCredit::find($id);
            if ($voucher->accountant_signature != "") {
                if (file_exists($voucher->accountant_signature)) {
                    unlink($voucher->accountant_signature);
                }
            }
            if ($voucher->accountant_signature != "") {
                if (file_exists($voucher->accountant_signature)) {
                    unlink($voucher->accountant_signature);
                }
            }
            $result = $voucher->delete();
            if ($result) {
                Toastr::success('Operation successful', 'Success');
                return redirect('debit-credit-voucher');
            }
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function debitCreditVoucherView($id)
    {
        try{
            $voucher = SmDebitCredit::find($id);
            return view('backEnd.accounts.viewVoucher', compact('voucher'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function dailyExpense()
    {
        try{
            $heads = SmChartOfAccount::where([['type', 'E'], ['is_daily_expense_head', 1]])->first();
            if ($heads == "") {
                $heads = SmChartOfAccount::where([['type', 'E']])->first();
            }
            $sub_heads = SmSubAccount::where('head_id', @$heads->id)->get();
            $expenses = SmDailyExpense::orderBy('created_at', 'DESC')->get();
            $cost_centers = SmCostCenter::where('active_status', '=', 1)->get();
            return view('backEnd.accounts.daily_expense', compact('heads', 'expenses', 'sub_heads', 'cost_centers'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function dailyExpenseStore(Request $request)
    {
        $request->validate([
            'expense_head' => 'required',
            'sub_head' => 'required',
            'amount' => 'required'
        ]);
        try{
            $date = strtotime($request->date);
            $newformat = date('Y-m-d', $date);
            $expense = new SmDailyExpense();
            $expense->head_id = $request->expense_head;
            $expense->sub_head_id = $request->sub_head;
            $expense->amount = $request->amount;
            $expense->cost_center_id = $request->cost_center;
            $expense->date = $newformat;
            $expense->description = $request->description;
            $expense->created_by = Auth::user()->id;
            $result = $expense->save();
    
            //Insert activity
            $data = SmDailyExpense::find($expense->id);
            $data['note'] = '"' . $data->head_id . "-" . $data->sub_head_id . '"Daily expense has been added.';
            $data['model_name'] = 'SmDailyExpense';
            $data['old_data'] = $data->toJson();
            $data['new_data'] = '';
            $data['action'] = 'Insert';
            $data['action_id'] = $data->id;
            $result = SmGeneralSettings::StoreAllActivities($data);
            if ($result) {
                Toastr::success('Operation successful', 'Success');
                return redirect()->back();
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function dailyExpenseEdit($id)
    {
        try{
            $heads = SmChartOfAccount::where([['type', 'E'], ['is_daily_expense_head', 1]])->first();
            if ($heads == "") {
                $heads = SmChartOfAccount::where([['type', 'E']])->first();
            }
            $sub_heads = SmSubAccount::where('head_id', $heads->id)->get();
            $expenses = SmDailyExpense::orderBy('created_at', 'DESC')->get();
            $expense = SmDailyExpense::find($id);
            $cost_centers = SmCostCenter::where('active_status', '=', 1)->get();
            return view('backEnd.accounts.daily_expense', compact('heads', 'expenses', 'expense', 'sub_heads', 'cost_centers'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function dailyExpenseUpdate(Request $request)
    {
        $request->validate([
            'expense_head' => 'required',
            'sub_head' => 'required',
            'amount' => 'required'
        ]);
        try{
            $date = strtotime($request->date);
            $newformat = date('Y-m-d', $date);
            $old_data = $expense = SmDailyExpense::find($request->id);
            $expense->head_id = $request->expense_head;
            $expense->sub_head_id = $request->sub_head;
            $expense->cost_center_id = $request->cost_center;
            $expense->amount = $request->amount;
            $expense->date = $newformat;
            $expense->is_approved = $request->is_approved;
            $expense->description = $request->description;
            $expense->updated_by = Auth::user()->id;
            $result = $expense->save();
            //Edit activity
            $new_data = SmDailyExpense::find($expense->id);
            $data['note'] = '"'  . $new_data->head_id . "-" . $new_data->sub_head_id . '"Daily expense  has been updated.';
            $data['model_name'] = 'SmDailyExpense';
            $data['old_data'] = $old_data->toJson();
            $data['new_data'] =  $new_data->toJson();
            $data['action'] = 'Edit';
            $data['action_id'] = $new_data->id;
            $result = SmGeneralSettings::StoreAllActivities($data);
    
            if ($result) {
                Toastr::success('Operation successful', 'Success');
                return redirect('daily-expense');
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function dailyExpenseDelete($id)
    {
        try{
            //Delete activity
            $data = SmDailyExpense::find($id);
            $data['note'] = '"'  . $data->head_id . "-" . $data->sub_head_id . '"Daily expense  has been deleted.';
            $data['model_name'] = 'SmDailyExpense';
            $data['old_data'] = $data->toJson();
            $data['new_data'] =  '';
            $data['action'] = 'Delete';
            $data['action_id'] = $data->id;
            $result = SmGeneralSettings::StoreAllActivities($data);
            $result = SmDailyExpense::destroy($id);

            if ($result) {
                Toastr::success('Operation successful', 'Success');
                return redirect('daily-expense');
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function getSubHead(Request $request)
    {
        $sub_accounts = SmSubAccount::where('head_id', $request->id)->get();
        // return response()->json($request);
        return response()->json([$sub_accounts]);
    }
}

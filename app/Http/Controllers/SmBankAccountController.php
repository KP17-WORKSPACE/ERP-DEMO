<?php

namespace App\Http\Controllers;

use App\SmAddIncome;
use App\SmAddExpense;
use App\SmBankAccount;
use App\SmFundTransfer;
use App\SmHrPayrollGenerate;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;

class SmBankAccountController extends Controller
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
    public function index()
    {
        try{
            $bank_accounts = SmbankAccount::all();
            return view('backEnd.accounts.bank_account', compact('bank_accounts'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'account_name' => "required|unique:sm_bank_accounts,account_name",
            'opening_balance' => "required"
        ]);
        try{
            $bank_account = new SmbankAccount();
            $bank_account->account_name = $request->account_name;
            $bank_account->account_no = $request->account_no;
            $bank_account->bank_name = $request->bank_name;
            $bank_account->opening_balance = $request->opening_balance;
            $bank_account->note = $request->note;
            $result = $bank_account->save();
            if($result){
                Toastr::success('Operation successful', 'Success');
                return redirect()->back();
            }else{
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function show($id)
    {
        try{
            $opening_balance = SmBankAccount::find($id);
            $incomes = SmAddIncome::where('payment_method_id', 3)->where('account_id', $id)->sum('amount');
            $expenses = SmAddExpense::where('payment_method_id', 3)->where('account_id', $id)->where('status', 1)->sum('amount');
            $total_balance = $opening_balance->opening_balance + $incomes - $expenses;
            $bank_account = SmbankAccount::find($id);
            $bank_accounts = SmbankAccount::all();
            return view('backEnd.accounts.bank_account', compact('bank_accounts', 'bank_account', 'total_balance'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'account_name' => "required|unique:sm_bank_accounts,account_name,".$id,
            'opening_balance' => "required"
        ]);
        try{
            $bank_account = SmbankAccount::find($id);
            $bank_account->account_name = $request->account_name;
            $bank_account->account_no = $request->account_no;
            $bank_account->bank_name = $request->bank_name;
            $bank_account->opening_balance = $request->opening_balance;
            $bank_account->note = $request->note;
            $result = $bank_account->save();
            if($result){
                Toastr::success('Operation successful', 'Success');
                return redirect('bank-account');
            }else{
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function destroy($id)
    {
        try{
            $bank_account = SmbankAccount::destroy($id);
            if($bank_account){
                Toastr::success('Operation successful', 'Success');
                return redirect('bank-account');
            }else{
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function pettyCashView($id){

        try{
            $opening_balance = SmBankAccount::find($id);
            $income_amount = SmAddIncome::where('payment_method_id', 3)->where('account_id', $id)->sum('amount');
            $incomes = SmAddIncome::where('payment_method_id', 3)->where('account_id', $id)->get();
            $expense_amount = SmAddExpense::where('payment_method_id', 3)->where('account_id', $id)->where('status', 1)->sum('amount');
            $transfer_amount = SmFundTransfer::where('bank_account_id', $id)->sum('amount');
            $transfers = SmFundTransfer::where('bank_account_id', $id)->get();
            $expenses = SmAddExpense::where('payment_method_id', 3)->where('account_id', $id)->where('status', 1)->get();
            $payroll_deducts = SmHrPayrollGenerate::where('payment_mode', 3)->where('account_id', $id)->get();
            $payroll_amount = SmHrPayrollGenerate::where('payment_mode', 3)->where('account_id', $id)->sum('net_salary');
            $expense_amount = $expense_amount + $payroll_amount;
            $total_balance = $opening_balance->opening_balance + $income_amount + $transfer_amount - $expense_amount;
            return view('backEnd.accounts.petty_cash_view', compact('total_balance', 'incomes', 'expenses', 'income_amount', 'expense_amount', 'opening_balance', 'payroll_deducts', 'transfers'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function bankLedger(){
        
        try{
            $bank_accounts = SmBankAccount::all();
            return view('backEnd.accounts.bank_ledger', compact('bank_accounts'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }


    public function bankLedgerSearch(Request $request){

        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from'
        ]);

        try{
            $bank_accounts = SmBankAccount::all();
            $account = $request->account;
            $dates = $this->dateRange($request->date_to, $request->date_from);
            return view('backEnd.accounts.bank_ledger', compact('dates', 'bank_accounts', 'account'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }


    public static function countDays($date1,$date2)
    {
        
        try{
            $date1 = strtotime($date1); // or your date as well
            $date2 = strtotime($date2);
            $datediff = $date1 - $date2;
            return floor($datediff/(60*60*24));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function dateRange($date1,$date2)
    {
        
        try{
            $count = static::countDays($date1,$date2) + 1;
            $dates = array();
            for($i=0;$i<$count;$i++)
            {
                $dates[] = date("Y-m-d",strtotime($date2.'+'.$i.' days'));
            }
            return $dates;
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
}

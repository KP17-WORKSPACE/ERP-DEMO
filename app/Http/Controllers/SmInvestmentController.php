<?php

namespace App\Http\Controllers;

use App\SmStaff;
use App\SmInvestment;
use App\SmBankAccount;
use App\SmFundTransfer;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;

class SmInvestmentController extends Controller
{
    public function index(){
    	
        try{
            $staffs = SmStaff::all();
            $investments = SmInvestment::all();
            return view('backEnd.accounts.investment', compact('staffs', 'investments'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function store(Request $request){
    	$request->validate([
    		'name' => 'required',
    		'staff_name' => 'required',
    		'amount' => 'required',
    		'date' => 'required'
    	]);
        try{
            $investment = new SmInvestment();
            $investment->name = $request->name;
            $investment->staff_id = $request->staff_name;
            $investment->amount = $request->amount;
            $investment->date = date('Y-m-d', strtotime($request->date));
            $result = $investment->save();
            if($result){
                Toastr::success('Operation successful', 'Success');
                return redirect('investment');
            }else{
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back(); 
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function edit($id){
        try{
            $staffs = SmStaff::all();
            $investment = SmInvestment::find($id);
            $investments = SmInvestment::all();
            return view('backEnd.accounts.investment', compact('staffs', 'investments', 'investment'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function update(Request $request){
    	$request->validate([
    		'name' => 'required',
    		'staff_name' => 'required',
    		'amount' => 'required',
    		'date' => 'required'
    	]);
        try{
            $investment = SmInvestment::find($request->id);
            $investment->name = $request->name;
            $investment->staff_id = $request->staff_name;
            $investment->amount = $request->amount;
            $investment->date = date('Y-m-d', strtotime($request->date));
            $result = $investment->save();
            if($result){
                Toastr::success('Operation successful', 'Success');
                return redirect('investment');
            }else{
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function delete($id){
        try{
            $result = SmInvestment::destroy($id);
            if($result){
                Toastr::success('Operation successful', 'Success');
                return redirect('investment');
            }else{
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function investmentReport(){
        
        try{
            return view('backEnd.accounts.investment_report');
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function transfer(){
        try{
            $banks = SmBankAccount::all();
            $investment_amount = SmInvestment::sum('amount');
            // return $investment_amount;
            $transfered = SmFundTransfer::sum('amount');
            $fund_transfers = SmFundTransfer::all();
            $investment_amount = $investment_amount - $transfered;
            
            return view('backEnd.accounts.fund_transfer', compact('investment_amount', 'banks', 'fund_transfers'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function transferStore(Request $request){
        $request->validate([
            'from' => 'required',
            'to_account' => 'required',
            'amount' => 'required',
            'date' => 'required'
        ]);
        try{
            $transfer = new SmFundTransfer();
            $transfer->bank_account_id = $request->to_account;
            $transfer->amount = $request->amount;
            $transfer->date = date('Y-m-d', strtotime($request->date));
            $result = $transfer->save();
            if($result){
                Toastr::success('Operation successful', 'Success');
                return redirect('transfer');
            }else{
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back(); 
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }

    }

    public function transferEdit($id){
        
        try{
            $banks = SmBankAccount::all();
            $investment_amount = SmInvestment::sum('amount');
            $fund_transfers = SmFundTransfer::all();
            $fund_transfer = SmFundTransfer::find($id);
            $transfered = SmFundTransfer::sum('amount');
            $investment_amount = $investment_amount - $transfered;
            return view('backEnd.accounts.fund_transfer', compact('investment_amount', 'banks', 'fund_transfers', 'fund_transfer'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function transferUpdate(Request $request){
        $request->validate([
            'from' => 'required',
            'to_account' => 'required',
            'amount' => 'required',
            'date' => 'required'
        ]);
        try{
            $transfer = SmFundTransfer::find($request->id);
            $transfer->bank_account_id = $request->to_account;
            $transfer->amount = $request->amount;
            $transfer->date = date('Y-m-d', strtotime($request->date));
            $result = $transfer->save();
            if($result){
                Toastr::success('Operation successful', 'Success');
                return redirect('transfer');
            }else{
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back(); 
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }

    }
    
    public function transferDelete($id){
        try{
            $result = SmFundTransfer::destroy($id);
            if($result){
                Toastr::success('Operation successful', 'Success');
                return redirect('transfer');
            }else{
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back(); 
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function invesmentSearch(Request $request){
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from'
        ]);

        try{
            $dates = $this->dateRange($request->date_to, $request->date_from);
            return view('backEnd.accounts.investment_report', compact('dates'));
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

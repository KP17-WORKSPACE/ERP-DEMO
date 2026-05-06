<?php

namespace App\Http\Controllers;

use App\SmStaff;
use App\SmCashIssue;
use App\SmItemCategory;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;

class SmCashIssueController extends Controller
{
    public function index(){
    	
		try{
			$staffs = SmStaff::where('active_status', 1)->get();
			// $classes = SmClass::all();
			$itemCat = SmItemCategory::all();
			$cash_issues = SmCashIssue::all();
			return view('backEnd.humanResource.cash_issue', compact('cash_issues', 'staffs', 'itemCat'));
		}catch (\Exception $e) {
		   Toastr::error('Operation Failed', 'Failed');
		   return redirect()->back(); 
		}
    }

    public function store(Request $request){
    	$request->validate([
    		'staff' => 'required',
    		'issue_date' => 'required',
    		'return_date' => 'required',
    		'amount' => 'required'
    	]);
    	
		try{
			$cash_issue = new SmCashIssue();
			$cash_issue->staff_id = $request->staff;
			$cash_issue->issue_date = $request->issue_date != ""? date('Y-m-d', strtotime($request->issue_date)):'';
			$cash_issue->return_date = $request->return_date != ""? date('Y-m-d', strtotime($request->return_date)):'';
			$cash_issue->amount = $request->amount;
			$cash_issue->note = $request->note;
			$result = $cash_issue->save();
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


    public function returnCashView($id){
		
		try{
			return view('backEnd.humanResource.return_cash_view', compact('id'));
		}catch (\Exception $e) {
		   Toastr::error('Operation Failed', 'Failed');
		   return redirect()->back(); 
		}
    }

    public function returnCash($id){

		try{
			$cash_issue = SmCashIssue::find($id);
			$cash_issue->is_return = '1';
			$result = $cash_issue->save();
	
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


}

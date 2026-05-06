<?php

namespace App\Http\Controllers;

use App\SmExam;
use App\SmItem;
use App\SmClass;
use App\SmStudent;
use Carbon\Carbon;
use App\SmExamType;
use App\SmAddIncome;
use App\SmExamSetup;
use App\SmMarkStore;
use App\SmAddExpense;
use App\ApiBaseMethod;
use App\SmBankAccount;
use App\SmItemReceive;
use App\SmResultStore;
use App\SmAssignSubject;
use App\SmTenderProduct;
use App\SmChartOfAccount;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Validator;

class SmReportController extends Controller
{
    public function __construct(){
        $this->middleware('PM');
    }

    public function tabulationSheetReport(Request $request){
    	
        try{
            $exam_types = SmExamType::where('active_status', 1)->get();
            $classes = SmClass::where('active_status', 1)->get();
    
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['exam_types'] = $exam_types->toArray();
                $data['classes'] = $classes->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.reports.tabulation_sheet_report', compact('exam_types', 'classes'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function tabulationSheetReportSearch(Request $request){
        $input = $request->all();
        $validator = Validator::make($input, [
    		'exam' => 'required',
    		'class' => 'required',
    		'section' => 'required'
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
            $exam_term_id   = $request->exam;
            $class_id       = $request->class;
            $section_id     = $request->section;
            $student_id     = $request->student;
    
            if(isset($request->student)){
                $marks      = SmMarkStore::where([
                                ['exam_term_id', $request->exam],
                                ['class_id', $request->class],
                                ['section_id', $request->section],
                                ['student_id', $request->student]
                            ])->get();
                $students   = SmStudent::where([
                                    ['class_id', $request->class],
                                    ['section_id', $request->section],
                                    ['id', $request->student]
                            ])->get();
            }else{            
                $marks = SmMarkStore::where([
                            ['exam_term_id', $request->exam],
                            ['class_id', $request->class],
                            ['section_id', $request->section]
                        ])->get();
            $students       = SmStudent::where([
                                    ['class_id', $request->class],
                                    ['section_id', $request->section]
                            ])->get();
            }
    
            $exam_types     = SmExamType::where('active_status', 1)->get();
            $classes        = SmClass::where('active_status', 1)->get();
            $subjects       = SmAssignSubject::where([
                                ['class_id', $request->class],
                                ['section_id', $request->section]
                            ])->get();
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['exam_types'] = $exam_types->toArray();
                $data['classes'] = $classes->toArray();
                $data['marks'] = $marks->toArray();
                $data['subjects'] = $subjects->toArray();
                $data['exam_term_id'] = $exam_term_id;
                $data['class_id'] = $class_id;
                $data['section_id'] = $section_id;
                $data['students'] = $students->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.reports.tabulation_sheet_report', compact('exam_types', 'classes','marks','subjects', 'exam_term_id','class_id','section_id','students'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }


    public function progressCardReport(Request $request){
        
        try{
            $exams = SmExam::where('active_status', 1)->get();
            $classes = SmClass::where('active_status', 1)->get();
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['routes'] = $exams->toArray();
                $data['assign_vehicles'] = $classes->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.reports.progress_card_report', compact('exams', 'classes'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    //student progress report search by rashed
    public function progressCardReportSearch(Request $request){

        //input validations, 3 input must be required
        $input = $request->all();
        $validator = Validator::make($input, [
            'class' => 'required',
            'section' => 'required',
            'student' => 'required'
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
            $exams = SmExam::where('active_status', 1)->get();
            $exam_types = SmExamType::where('active_status', 1)->get();
            $classes = SmClass::where('active_status', 1)->get();
            $studentDetails = SmStudent::find($request->student);
            $exam_setup = SmExamSetup::where([['class_id', $request->class],['section_id', $request->section]])->get();
            $class_id=$request->class;
            $section_id=$request->section;
            $student_id=$request->student;
            $subjects = SmAssignSubject::where([['class_id', $request->class],['section_id', $request->section]])->get();
            $is_result_available = SmResultStore::where([['class_id', $request->class],['section_id', $request->section],['student_id', $request->student]])->get();
            if($is_result_available->count()>0){
    
                if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                    $data = [];
                    $data['exams'] = $exams->toArray();
                    $data['classes'] = $classes->toArray();
                    $data['studentDetails'] = $studentDetails;
                    $data['is_result_available'] = $is_result_available;
                    $data['subjects'] = $subjects->toArray();
                    $data['class_id'] = $class_id;
                    $data['section_id'] = $section_id;
                    $data['student_id'] = $student_id;
                    $data['exam_types'] = $exam_types;
                    return ApiBaseMethod::sendResponse($data, null);
                }
                return view('backEnd.reports.progress_card_report', compact('exams', 'classes','studentDetails','is_result_available','subjects','class_id','section_id','student_id','exam_types'));
            }else{
                return redirect('progress-card-report')->with('message-danger', 'Ops! Your result is not found! Please check mark register.');
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }


    public function incomeStatement(){
        
        try{
            $income_heads = SmChartOfAccount::where('type', "I")->where('active_status', '=', 1)->get();
            return view('backEnd.reports.incomeStatement', compact('income_heads'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function incomeStatementSearch(Request $request){

        try{;
            $date_from =Carbon::createFromFormat('d/m/Y',$request->date_from)->format('Y-m-d');
            $date_to = Carbon::createFromFormat('d/m/Y',$request->date_to)->format('Y-m-d');
            $add_incomes = SmAddIncome::query();
            $add_incomes->where('date', '>=', $date_from);
            $add_incomes->where('date', '<=', $date_to);
            if($request->income_head != ""){
                $add_incomes->where('income_head_id', $request->income_head);
            }
             if($request->sub_head != ""){
                $add_incomes->where('income_sub_head_id', $request->sub_head);
            }
            $add_incomes = $add_incomes->get();
            $income_heads = SmChartOfAccount::where('type', "I")->where('active_status', '=', 1)->get();
            return view('backEnd.reports.incomeStatement', compact('income_heads', 'add_incomes'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function ledgerReport(){
        
        try{
            $heads = SmChartOfAccount::where('active_status', '=', 1)->get();
            return view('backEnd.reports.ledgerReport', compact('heads'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function ledgerReportSearch(Request $request){
        
        try{
            $date_from = date('Y-m-d', strtotime($request->date_from));
            $date_to = date('Y-m-d', strtotime($request->date_to));
            $add_incomes = SmAddIncome::query();
            $add_incomes->where('date', '>=', $date_from);
            $add_incomes->where('date', '<=', $date_to);
            if($request->head != ""){
                $add_incomes->where('income_head_id', $request->head);
            }
            $add_incomes = $add_incomes->sum('amount');
            $add_expenses = SmAddExpense::query();
            $add_expenses->where('date', '>=', $date_from);
            $add_expenses->where('date', '<=', $date_to);
            if($request->head != ""){
                $add_expenses->where('expense_head_id', $request->head);
            }
            $add_expenses = $add_expenses->sum('amount');
            $heads = SmChartOfAccount::where('active_status', '=', 1)->get();
            return view('backEnd.reports.ledgerReport', compact('income_heads', 'add_incomes', 'add_expenses', 'date_from', 'date_to', 'heads'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function bankBook()
    {
        try{
            $banks = SmBankAccount::where('active_status', '=', 1)->get();
            $date_from = date('Y-m-01');
            $date_to = date('Y-m-Y');
            $bank_accounts = SmBankAccount::query();
            $bank_accounts->where('id', $banks[0]->id);
            $bank_accounts = $bank_accounts->get();
            return view('backEnd.reports.bankBoork', compact('banks', 'bank_accounts', 'date_from', 'date_to'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function bankBookSearch(Request $request)
    {   
        try{
            $date_from = $request->date_from;
            $date_to = $request->date_to;
            $bank_accounts = SmBankAccount::query();
            if($request->bank != ""){
                $bank_accounts->where('id', $request->bank);
            }
            $bank_accounts = $bank_accounts->get();
            $banks = SmBankAccount::where('active_status', '=', 1)->get();
            return view('backEnd.reports.bankBoork', compact('banks', 'bank_accounts', 'date_from', 'date_to'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function purchaseReport(){
        try{
            $items = SmItem::all();
            return view('backEnd.reports.purchaseReport', compact('items'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function purchaseReportSearch(Request $request){

        try{
            $date_from = date('Y-m-d', strtotime($request->date_from));
            $date_to = date('Y-m-d', strtotime($request->date_to));
            $purchase_items = SmItemReceive::query();
            $purchase_items->where('received_date', '>=', $date_from);
            $purchase_items->where('received_date', '<=', $date_to);
            if($request->item != ""){
                $purchase_items->where('product_id', $request->item);
            }
            $purchase_items = $purchase_items->get();
            $items = SmItem::all();
            return view('backEnd.reports.purchaseReport', compact('purchase_items', 'items', 'date_from', 'date_to'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }


    public function salesReport(){
        
        try{
            $items = SmItem::all();
            return view('backEnd.reports.salesReport', compact('items'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }


    public function salesReportSearch(Request $request){
        try{
            $date_from = date('Y-m-d 00:00:00', strtotime($request->date_from));
            $date_to = date('Y-m-d 23:59:00', strtotime($request->date_to));
            $sales_items = SmTenderProduct::query();
            $sales_items->where('created_at', '>=', $date_from);
            $sales_items->where('created_at', '<=', $date_to);
            if($request->item != ""){
                $sales_items->where('product_id', $request->item);
            }
            $sales_items = $sales_items->get();
            $items = SmItem::all();
            return view('backEnd.reports.salesReport', compact('sales_items', 'items', 'date_from', 'date_to'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    

}

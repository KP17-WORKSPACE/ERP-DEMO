<?php

namespace App\Http\Controllers;

use App\ApiBaseMethod;
use Illuminate\Http\Request;
use App\SmStudentCategory;
use Validator;

class SmStudentCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
    }
    
     public function index(Request $request){
    	$student_types = SmStudentCategory::all();

         if (ApiBaseMethod::checkUrl($request->fullUrl())) {
             return ApiBaseMethod::sendResponse($student_types, null);
         }

    	return view('backEnd.studentInformation.student_category', compact('student_types'));

    }
    public function store(Request $request){
        $input = $request->all();
        $validator = Validator::make($input, [
    		'category' => 'required|unique:sm_student_categories,category_name',
    	]);

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

    	$student_type = new SmStudentCategory();
    	$student_type->category_name = $request->category;
    	$result = $student_type->save();

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            if ($result) {
                return ApiBaseMethod::sendResponse(null, 'Category been created successfully');
            } else {
                return ApiBaseMethod::sendError('Something went wrong, please try again');
            }
        } else {
            if ($result) {
                return redirect()->back()->with('message-success', 'Category been created successfully');
            } else {
                return redirect()->back()->with('message-danger', 'Something went wrong, please try again');
            }
        }
    }

    public function edit(Request $request,$id){
    	$student_type = SmStudentCategory::find($id);
    	$student_types = SmStudentCategory::all();


     	return view('backEnd.studentInformation.student_category', compact('student_types', 'student_type'));
    }
    public function update(Request $request){
        $input = $request->all();
        $validator = Validator::make($input, [
    		'category' => 'required|unique:sm_student_categories,category_name,'.$request->id,
    	]);

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
    	
    	$student_type = SmStudentCategory::find($request->id);
    	$student_type->category_name = $request->category;
    	$result = $student_type->save();

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            if ($result) {
                return ApiBaseMethod::sendResponse(null, 'Category been updated successfully');
            } else {
                return ApiBaseMethod::sendError('Something went wrong, please try again');
            }
        } else {
            if ($result) {
                return redirect('student-category')->with('message-success', 'Category been updated successfully');
            } else {
                return redirect()->back()->with('message-danger', 'Something went wrong, please try again');
            }
        }
    }
    public function delete(Request $request,$id){
    	$student_type = SmStudentCategory::destroy($id);

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            if ($student_type) {
                return ApiBaseMethod::sendResponse(null, 'Category has been deleted successfully');
            } else {
                return ApiBaseMethod::sendError('Something went wrong, please try again');
            }
        } else {
            if ($student_type) {
                return redirect('student-category')->with('message-success-delete', 'Category has been deleted successfully');
            } else {
                return redirect()->back()->with('message-danger-delete', 'Something went wrong, please try again');
            }
        }
    }
}

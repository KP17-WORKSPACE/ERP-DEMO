<?php

namespace App\Http\Controllers;

use App\SmSubject;
use App\ApiBaseMethod;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Validator;

class SmSubjectController extends Controller
{
    public function __construct(){
        $this->middleware('PM');
    }
    
    public function index(Request $request){
    	
        try{
            $subjects = SmSubject::where('active_status', 1)->orderBy('id', 'DESC')->get();
            if(ApiBaseMethod::checkUrl($request->fullUrl())){
                return ApiBaseMethod::sendResponse($subjects, null);
            }
            return view('backEnd.academics.subject', compact('subjects'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function store(Request $request){
        $input = $request->all();
        if(ApiBaseMethod::checkUrl($request->fullUrl())) {
            $validator = Validator::make($input, [
                'subject_name' => "required|unique:sm_subjects",
                'subject_type' => "required",
            ]);
        }else{
            $validator = Validator::make($input, [
                'subject_name' => "required|unique:sm_subjects"
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
            $subject = new SmSubject();
            $subject->subject_name = $request->subject_name;
            $subject->subject_type = $request->subject_type;
            $subject->subject_code = $request->subject_code;
            $result = $subject->save();
            if(ApiBaseMethod::checkUrl($request->fullUrl())){
                if($result){
                    return ApiBaseMethod::sendResponse(null, 'Subject has been created successfully');
                }else{
                    return ApiBaseMethod::sendError('Something went wrong, please try again.');
                }
            }else{
                if($result){
                    Toastr::success('Operation successful', 'Success');
                    return redirect()->back();
                }else{
                    Toastr::error('Operation Failed', 'Failed');
                    return redirect()->back();
                }
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function edit(Request $request,$id){
    	
         try{
            $subject = SmSubject::find($id);
            $subjects = SmSubject::where('active_status', 1)->orderBy('id', 'DESC')->get();
            if(ApiBaseMethod::checkUrl($request->fullUrl())){
                $data=[];
                $data['subject']= $subject->toArray();
                $data['subjects']= $subjects->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }
             return view('backEnd.academics.subject', compact('subject', 'subjects'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function update(Request $request){
        $input = $request->all();
        if(ApiBaseMethod::checkUrl($request->fullUrl())) {
            $validator = Validator::make($input, [
                'subject_name' => "required|unique:sm_subjects",
                'subject_type' => "required",
            ]);
        }else{
            $validator = Validator::make($input, [
                'subject_name' => "required|unique:sm_subjects"
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
            $subject = SmSubject::find($request->id);
            $subject->subject_name = $request->subject_name;
            $subject->subject_type = $request->subject_type;
            $subject->subject_code = $request->subject_code;
            $result = $subject->save();
            if(ApiBaseMethod::checkUrl($request->fullUrl())){
                if($result){
                    return ApiBaseMethod::sendResponse(null, 'Subject has been updated successfully');
                }else{
                    return ApiBaseMethod::sendError('Something went wrong, please try again.');
                }
            }else{
                if($result){
                    Toastr::success('Operation successful', 'Success');
                    return redirect()->back();
                }else{
                    Toastr::error('Operation Failed', 'Failed');
                    return redirect()->back();
                }
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }

    }
    public function delete(Request $request,$id){
    	
        try{
            $subject = SmSubject::destroy($id);
            if(ApiBaseMethod::checkUrl($request->fullUrl())){
                if($subject){
                    return ApiBaseMethod::sendResponse(null, 'Subject has been deleted successfully');
                }else{
                    return ApiBaseMethod::sendError('Something went wrong, please try again.');
                }
            }else{
                if($subject){
                    Toastr::success('Operation successful', 'Success');
                    return redirect()->back();
                }else{
                    Toastr::error('Operation Failed', 'Failed');
                    return redirect()->back();
                }
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
}

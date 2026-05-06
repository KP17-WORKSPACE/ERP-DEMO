<?php

namespace App\Http\Controllers;

use App\ApiBaseMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\SmClass;
use App\SmSection;
use App\SmClassSection;
use DB;
use Validator;

class SmClassController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
    }

    
    public function index(Request $request){
    	$sections = SmSection::where('active_status', '=', 1)->get();
    	$classes = SmClass::where('active_status', '=', 1)->get();

        if(ApiBaseMethod::checkUrl($request->fullUrl())){
            $data=[];
            $data['classes']= $sections->toArray();
            $data['sections']= $classes->toArray();
            return ApiBaseMethod::sendResponse($data, null);
        }

    	return view('backEnd.academics.class', compact('classes', 'sections'));
    }
    public function store(Request $request){

        $input = $request->all();
        $validator = Validator::make($input, [
    		'name' => "required|unique:sm_classes,class_name",
            'section' => 'required|array',
    	],
        [
            'section.required' => 'At least one checkbox required!'
        ]);

        if($validator->fails()){
            if(ApiBaseMethod::checkUrl($request->fullUrl())){
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try{
            $class = new SmClass();
            $class->class_name = $request->name;
            $class->save();
            $class->toArray();
            try{
                $sections = $request->section;

                foreach($sections as $section){
                    $smClassSection = new SmClassSection();
                    $smClassSection->class_id = $class->id;
                    $smClassSection->section_id = $section;
                    $smClassSection->save();
                }

                DB::commit();

                if(ApiBaseMethod::checkUrl($request->fullUrl())){
                    return ApiBaseMethod::sendResponse(null, 'Class has been created successfully');
                }
                return redirect()->back()->with('message-success', 'Class has been created successfully');

            }catch(Exception $e){
                DB::rollBack();
            }
        }catch(Exception $e){
            DB::rollBack();
        }
        if(ApiBaseMethod::checkUrl($request->fullUrl())){
            return ApiBaseMethod::sendError('Something went wrong, please try again.');
        }
		return redirect()->back()->with('message-danger', 'Something went wrong, please try again');

    }
    public function edit(Request $request,$id){

    	$classById = SmCLass::find($id);

    	$sectionByNames = SmClassSection::select('section_id')->where('class_id', '=', $classById->id)->get();

    	$sectionId = array();
    	foreach($sectionByNames as $sectionByName){
    		$sectionId[] = $sectionByName->section_id;
    	}

    	$sections = SmSection::where('active_status', '=', 1)->get();

    	$classes = SmClass::where('active_status', '=', 1)->orderBy('id', 'desc')->get();

        if(ApiBaseMethod::checkUrl($request->fullUrl())){
            $data=[];
            $data['sections']= $sections->toArray();
            $data['classes']= $classes->toArray();
            $data['classById']= $classById;
            $data['sectionId']= $sectionId;
            return ApiBaseMethod::sendResponse($data, null);
        }

     	return view('backEnd.academics.class', compact('classById', 'classes', 'sections', 'sectionId', 'className'));
    }
    public function update(Request $request){
        $input = $request->all();
        $validator = Validator::make($input, [
    		'name' => "required|unique:sm_classes,class_name,".$request->id,
            'section' => 'required|array',
        ],
        [
            'section.required' => 'At least one checkbox required!'
        ]);

        if($validator->fails()){
            if(ApiBaseMethod::checkUrl($request->fullUrl())){
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

    	SmCLassSection::where('class_id', $request->id)->delete();


    	DB::beginTransaction();

        try{
            $class = SmClass::find($request->id);
            $class->class_name = $request->name;
            $class->save();
            $class->toArray();
            try{
                $sections = $request->section;

                foreach($sections as $section){
                    $smClassSection = new SmClassSection();
                    $smClassSection->class_id = $class->id;
                    $smClassSection->section_id = $section;
                    $smClassSection->save();
                }

                DB::commit();

                if(ApiBaseMethod::checkUrl($request->fullUrl())){
                    return ApiBaseMethod::sendResponse(null, 'Class has been updated successfully');
                }

                return redirect('class')->with('message-success', 'Class has been updated successfully');

            }catch(Exception $e){
                DB::rollBack();
            }
        }catch(Exception $e){
            DB::rollBack();
        }

        if(ApiBaseMethod::checkUrl($request->fullUrl())){
            return ApiBaseMethod::sendError('Something went wrong, please try again.');
        }

        return redirect()->back()->with('message-danger', 'Something went wrong, please try again');

    	
    }
    public function delete(Request $request,$id){
    	$class = SmClass::find($id);
    	$result = SmClass::where('class_name', $class->class_name)->delete();

        if(ApiBaseMethod::checkUrl($request->fullUrl())){
            if($result){
                return ApiBaseMethod::sendResponse(null, 'Class has been deleted successfully');
            }else{
                return ApiBaseMethod::sendError('Something went wrong, please try again.');
            }
        }else{
            if($result){
                return redirect('class')->with('message-success-delete', 'Class has been deleted successfully');
            }else{
                return redirect()->back()->with('message-danger-delete', 'Something went wrong, please try again');
            }
        }
    }
}

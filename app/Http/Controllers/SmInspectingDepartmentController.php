<?php

namespace App\Http\Controllers;

use App\SmInspectingDepartment;
use Illuminate\Http\Request;

use App\ApiBaseMethod;
use Validator;
class SmInspectingDepartmentController extends Controller
{

    public function __construct(){
        $this->middleware('PM');
    }
    

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $InspectingDepartment = SmInspectingDepartment::where('active_status',1)->orderBy('created_at','desc')->get();

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            return ApiBaseMethod::sendResponse($suppliers, null);
        }
        return view('backEnd.InspectingDepartments.InspectingDepartmentsList', compact('InspectingDepartment'));
    }


    public function ajaxSearchInspectingDepartment(){
        $suppliers = SmInspectingDepartment::where('active_status',1)->orderBy('created_at','desc')->get();

        $searchData = [];
        foreach($suppliers as $item){
            $searchData[] =  ['id' => $item->id, 'name' => $item->company_name]; 
        }

        if(!empty($searchData)){
            return json_encode($searchData);
        }
        
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
        $validator = Validator::make($input, [
           'department_name' => "required", 
           'name' => "required", 
           'contact_person_email' => "required",
           'contact_person_mobile' => "required"
        ]);

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

       $suppliers = new SmInspectingDepartment();
       $suppliers->department_name = $request->department_name;  
       $suppliers->name = $request->name;  
       $suppliers->phone = $request->contact_person_mobile;
       $suppliers->email = $request->contact_person_email;
       $suppliers->description = $request->description;
       $suppliers->created_by = Auth()->user()->id;
       $results = $suppliers->save();

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            if ($results) {
                return ApiBaseMethod::sendResponse(null, 'Inspecting Department has been added successfully');
            } else {
                return ApiBaseMethod::sendError('Something went wrong, please try again');
            }
        } else {
            if ($results) {
                return redirect()->back()->with('message-success', 'Inspecting Department has been added successfully');
            } else {
                return redirect()->back()->with('message-danger', 'Something went wrong, please try again');
            }
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
       $editData = SmInspectingDepartment::find($id);
       $suppliers = SmInspectingDepartment::all();

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            $data = [];
            $data['editData'] = $editData->toArray();
            $data['suppliers'] = $suppliers->toArray();
            return ApiBaseMethod::sendResponse($data, null);
        }
       return view('backEnd.InspectingDepartments.InspectingDepartmentsList', compact('editData', 'suppliers'));
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
        $validator = Validator::make($input, [
           'department_name' => "required", 
           'name' => "required", 
           'contact_person_email' => "required",
           'contact_person_mobile' => "required"
        ]);

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
       
       $suppliers = SmInspectingDepartment::find($id);
       $suppliers->department_name = $request->department_name;  
       $suppliers->name = $request->name;  
       $suppliers->phone = $request->contact_person_mobile;
       $suppliers->email = $request->contact_person_email;
       $suppliers->description = $request->description;
       $suppliers->updated_by = Auth()->user()->id;
       $results = $suppliers->update();

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            if ($results) {
                return ApiBaseMethod::sendResponse(null,  'Inspecting Department has been updated successfully');
            } else {
                return ApiBaseMethod::sendError('Something went wrong, please try again');
            }
        } else {
            if ($results) {
                return redirect('inspecting-department')->with('message-success', 'Inspecting Department has been updated successfully');
            } else {
                return redirect()->back()->with('message-danger', 'Something went wrong, please try again');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function deleteInspectingDepartmentView(Request $request,$id){

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            return ApiBaseMethod::sendResponse($id, null);
        }
         return view('backEnd.InspectingDepartments.deleteInspectingDepartmentsView', compact('id'));
    }

    public function deleteInspectingDepartment(Request $request,$id){
        $result = SmInspectingDepartment::destroy($id);

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            if ($result) {
                return ApiBaseMethod::sendResponse(null, 'Inspecting Department has been deleted successfully');
            } else {
                return ApiBaseMethod::sendError('Something went wrong, please try again.');
            }
        } else {
            if ($result) {
                return redirect('inspecting-department')->with('message-success-delete', 'Inspecting Department has been deleted successfully');
            } else {
                return redirect()->back()->with('message-danger-delete', 'Something went wrong, please try again');
            }
        }
    }


}

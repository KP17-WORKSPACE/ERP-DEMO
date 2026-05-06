<?php

namespace App\Http\Controllers;

use App\ApiBaseMethod;
use Illuminate\Http\Request;
use App\SmLibraryMember;
use App\Role;
use App\SmClass;
use Validator;
class SmLibraryMemberController extends Controller
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
        $libraryMembers = SmLibraryMember::where('active_status', '=', 1)->get();
        $roles = Role::all();
        $classes = SmClass::all();

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            $data = [];
            $data['libraryMembers'] = $libraryMembers->toArray();
            $data['roles'] = $roles->toArray();
            $data['classes'] = $classes->toArray();
            return ApiBaseMethod::sendResponse($data, null);
        }
        return view('backEnd.library.members', compact('libraryMembers', 'roles', 'classes'));
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
        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            if ($request->member_type == "") {
                $validator = Validator::make($input, [
                    'member_type' => "required",
                    'user_id' => "required",
                    'member_ud_id' => "required|unique:sm_library_members,member_ud_id"
                ]);
            } elseif ($request->member_type == "2") {
                $validator = Validator::make($input, [
                    'member_type' => "required",
                    'student' => "required",
                    'user_id' => "required",
                    'member_ud_id' => "required|unique:sm_library_members,member_ud_id"
                ]);
            } else {
                $validator = Validator::make($input, [
                    'member_type' => "required",
                    'staff' => "required",
                    'user_id' => "required",
                    'member_ud_id' => "required|unique:sm_library_members,member_ud_id"
                ]);
            }
        }
        else{
            if ($request->member_type == "") {
                $validator = Validator::make($input, [
                    'member_type' => "required",
                    'member_ud_id' => "required|unique:sm_library_members,member_ud_id"
                ]);
            } elseif ($request->member_type == "2") {
                $validator = Validator::make($input, [
                    'member_type' => "required",
                    'student' => "required",
                    'member_ud_id' => "required|unique:sm_library_members,member_ud_id"
                ]);
            } else {
                $validator = Validator::make($input, [
                    'member_type' => "required",
                    'staff' => "required",
                    'member_ud_id' => "required|unique:sm_library_members,member_ud_id"
                ]);
            }
        }

        $student_staff_id = '';
        if(!empty($request->student)){
            $student_staff_id = $request->student;
            $isData = SmLibraryMember::where('student_staff_id', '=', $student_staff_id)->where('active_status', '=', 1)->first();
             if(!empty($isData)){
                return redirect()->back()->with('message-danger', 'This Member is already added in our library.');
             }
           
        }
        if(!empty($request->staff)){
            $student_staff_id = $request->staff;
            $isData = SmLibraryMember::where('student_staff_id', '=', $student_staff_id)->where('active_status', '=', 1)->first();
             if(!empty($isData)){
                return redirect()->back()->with('message-danger', 'This Member is already added in our library.');
             }
        }


        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }


        $user = Auth()->user();

        if ($user) {
            $user_id = $user->id;
        } else {
            $user_id = $request->user_id;
        }

       $members = new SmLibraryMember();
       $members->member_type = $request->member_type;
       $members->student_staff_id = $student_staff_id;
       $members->member_ud_id = $request->member_ud_id;
       $members->created_by = $user_id;
       $results = $members->save();

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            if ($results) {
                return ApiBaseMethod::sendResponse(null, 'New Member has been added successfully');
            } else {
                return ApiBaseMethod::sendError('Something went wrong, please try again.');
            }
        } else {
            if ($results) {
                return redirect()->back()->with('message-success', 'New Member has been added successfully');
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
    public function edit($id)
    {
        //
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
        //
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

    public function cancelMembership(Request $request,$id){
        $members = SmLibraryMember::find($id);
        $members->active_status = 0;
        $results = $members->update();

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            if ($results) {
                return ApiBaseMethod::sendResponse(null, 'Membership has been successfully cancelled');
            } else {
                return ApiBaseMethod::sendError('Something went wrong, please try again.');
            }
        } else {
            if ($results) {
                return redirect()->back()->with('message-success-delete', 'Membership has been successfully cancelled');
            } else {
                return redirect()->back()->with('message-danger-delete', 'Something went wrong, please try again');
            }
        }

    }
}

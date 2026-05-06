<?php

namespace App\Http\Controllers;

use App\ApiBaseMethod;
use Illuminate\Http\Request;
use App\SmDormitoryList;
use Validator;

class SmDormitoryListController extends Controller
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
    public function index(Request $request)
    {
        $dormitory_lists = SmDormitoryList::all();

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            return ApiBaseMethod::sendResponse($dormitory_lists, null);
        }
        return view('backEnd.dormitory.dormitory_list', compact('dormitory_lists'));
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
            'dormitory_name' => "required|unique:sm_dormitory_lists,dormitory_name",
            'type' => "required",
            'intake' => "required"
        ]);

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }


        $dormitory_list = new SmDormitoryList();
        $dormitory_list->dormitory_name = $request->dormitory_name;
        $dormitory_list->type = $request->type;
        $dormitory_list->address = $request->address;
        $dormitory_list->intake = $request->intake;
        $dormitory_list->description = $request->description;
        $result = $dormitory_list->save();

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            if ($result) {
                return ApiBaseMethod::sendResponse(null, 'Dormitory has been created successfully');
            } else {
                return ApiBaseMethod::sendError('Something went wrong, please try again');
            }
        } else {
            if ($result) {
                return redirect()->back()->with('message-success', 'Dormitory has been created successfully');
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
    public function show(Request $request, $id)
    {
        $dormitory_list = SmDormitoryList::find($id);
        $dormitory_lists = SmDormitoryList::all();

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            $data = [];
            $data['dormitory_list'] = $dormitory_list;
            $data['dormitory_lists'] = $dormitory_lists->toArray();
            return ApiBaseMethod::sendResponse($data, null);
        }
        return view('backEnd.dormitory.dormitory_list', compact('dormitory_lists', 'dormitory_list'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return 'dsfsd';
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
            'dormitory_name' => 'required|unique:sm_dormitory_lists,dormitory_name,'.$id,
            'type' => "required",
            'intake' => "required"
        ]);

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $dormitory_list = SmDormitoryList::find($request->id);
        $dormitory_list->dormitory_name = $request->dormitory_name;
        $dormitory_list->type = $request->type;
        $dormitory_list->address = $request->address;
        $dormitory_list->intake = $request->intake;
        $dormitory_list->description = $request->description;
        $result = $dormitory_list->save();

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            if ($result) {
                return ApiBaseMethod::sendResponse(null, 'Dormitory has been updated successfully');
            } else {
                return ApiBaseMethod::sendError('Something went wrong, please try again');
            }
        } else {
            if ($result) {
                return redirect('dormitory-list')->with('message-success', 'Dormitory has been updated successfully');
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
    public function destroy(Request $request,$id)
    {
        $dormitory_list = SmDormitoryList::destroy($id);

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            if ($dormitory_list) {
                return ApiBaseMethod::sendResponse(null, 'Room has been deleted successfully');
            } else {
                return ApiBaseMethod::sendError('Something went wrong, please try again');
            }
        } else {
            if ($dormitory_list) {
                return redirect('dormitory-list')->with('message-success-delete', 'Dormitory has been deleted successfully');
            } else {
                return redirect()->back()->with('message-danger-delete', 'Something went wrong, please try again');
            }
        }
    }
}

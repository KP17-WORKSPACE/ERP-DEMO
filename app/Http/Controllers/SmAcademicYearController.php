<?php

namespace App\Http\Controllers;

use App\ApiBaseMethod;
use Illuminate\Http\Request;
use App\SmAcademicYear;
use Validator;
class SmAcademicYearController extends Controller
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
        $academic_years = SmAcademicYear::all();
        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            return ApiBaseMethod::sendResponse($academic_years, null);
        }
        return view('backEnd.systemSettings.academic_year', compact('academic_years'));
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
            'year' => "required",
            'title' => "required",
            'starting_date' => "required",
            'ending_date' => "required"
        ]);

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }


        $academic_year = new SmAcademicYear();
        $academic_year->year = $request->year;
        $academic_year->title = $request->title;
        $academic_year->starting_date = date('Y-m-d', strtotime($request->starting_date));
        $academic_year->ending_date = date('Y-m-d', strtotime($request->ending_date));
        $result = $academic_year->save();

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            if ($result) {
                return ApiBaseMethod::sendResponse(null, 'Year has been created successfully');
            } else {
                return ApiBaseMethod::sendError('Something went wrong, please try again');
            }
        } else {
            if ($result) {
                return redirect()->back()->with('message-success', 'Year has been created successfully');
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
    public function show(Request $request,$id)
    {
        $academic_year = SmAcademicYear::find($id);
        $academic_years = SmAcademicYear::all();

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            $data = [];
            $data['academic_year'] = $academic_year->toArray();
            $data['academic_years'] = $academic_years->toArray();
            return ApiBaseMethod::sendResponse($data, null);
        }
        return view('backEnd.systemSettings.academic_year', compact('academic_year', 'academic_years'));
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
        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            $validator = Validator::make($input, [
                'year' => "required",
                'title' => "required",
                'starting_date' => "required",
                'ending_date' => "required",
                'id' => "required"
            ]);
        }else{
            $validator = Validator::make($input, [
                'year' => "required",
                'title' => "required",
                'starting_date' => "required",
                'ending_date' => "required"
            ]);
        }

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $academic_year = SmAcademicYear::find($request->id);
        $academic_year->year = $request->year;
        $academic_year->title = $request->title;
        $academic_year->starting_date = date('Y-m-d', strtotime($request->starting_date));
        $academic_year->ending_date = date('Y-m-d', strtotime($request->ending_date));
        $result = $academic_year->save();

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            if ($result) {
                return ApiBaseMethod::sendResponse(null, 'Year has been updated successfully');
            } else {
                return ApiBaseMethod::sendError('Something went wrong, please try again');
            }
        } else {
            if ($result) {
                return redirect('academic-year')->with('message-success', 'Year has been updated successfully');
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
        $academic_year = SmAcademicYear::destroy($id);

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            if ($academic_year) {
                return ApiBaseMethod::sendResponse(null, 'Year has been deleted successfully');
            } else {
                return ApiBaseMethod::sendError('Something went wrong, please try again');
            }
        } else {
            if ($academic_year) {
                return redirect()->back()->with('message-success-delete', 'Year has been deleted successfully');
            } else {
                return redirect()->back()->with('message-danger-delete', 'Something went wrong, please try again');
            }
        }
    }
}

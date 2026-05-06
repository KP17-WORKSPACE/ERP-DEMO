<?php

namespace App\Http\Controllers;

use App\SmBusinessActivity;
use App\SmIndustry;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;

class SmBusinessActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $activities = SmBusinessActivity::with('industry')->orderBy('id','DESC')->get();
        $industries = SmIndustry::orderBy('name')->get(); // for dropdown

        return view('backEnd.company.business-activity.index', compact('activities','industries'));
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
        $this->validate($request, [
            'industry_id' => 'required|integer',
            'name' => 'required|max:255'
        ]);

        $activity = new SmBusinessActivity();
        $activity->industry_id = $request->industry_id;
        $activity->name = $request->name;
        $activity->save();

        Toastr::success('Business Activity added successfully!', 'Success');
        return redirect()->back();
    }

    /**
     * Store a newly created Business Sector (activity) via AJAX and return JSON.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeAjax(Request $request)
    {
        $this->validate($request, [
            'industry_id' => 'required|integer',
            'name' => 'required|max:255'
        ]);

        $activity = new SmBusinessActivity();
        $activity->industry_id = $request->industry_id;
        $activity->name = $request->name;
        $activity->save();

        return response()->json([
            'status' => true,
            'message' => 'Business Sector added successfully!',
            'data' => [
                'id' => $activity->id,
                'name' => $activity->name
            ]
        ]);
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
        $this->validate($request, [
            'industry_id' => 'required|integer',
            'name' => 'required|max:255'
        ]);

        $activity = SmBusinessActivity::findOrFail($id);
        $activity->industry_id = $request->industry_id;
        $activity->name = $request->name;
        $activity->save();

        Toastr::success('Business Activity updated successfully!', 'Success');
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
   public function destroy($id)
    {
        $activity = SmBusinessActivity::findOrFail($id);
        $activity->delete();

        Toastr::success('Business Activity deleted successfully!', 'Success');
        return redirect()->back();
    }

    public function searchBusinessActivity(Request $request)
    {
        $query = $request->get('q', '');
        $activities = SmBusinessActivity::where('name', 'LIKE', '%' . $query . '%')
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($activities);
    }
}

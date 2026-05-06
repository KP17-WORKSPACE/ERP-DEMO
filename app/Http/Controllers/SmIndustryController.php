<?php

namespace App\Http\Controllers;

use App\SmBusinessActivity;
use App\SmIndustry;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;

class SmIndustryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $industries = SmIndustry::orderBy('id', 'DESC')->get();
        return view('backEnd.company.industry.index', compact('industries'));
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
            'name' => 'required|max:255'
        ]);

        $industry = new SmIndustry();
        $industry->name = $request->name;
        $industry->save();

        Toastr::success('Industry added successfully!', 'Success');
        return redirect()->back();
    }

    /**
     * Store a newly created resource in storage via AJAX.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeAjax(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255'
        ]);

        $industry = new SmIndustry();
        $industry->name = $request->name;
        $industry->save();

        return response()->json([
            'status' => true,
            'message' => 'Industry added successfully!',
            'data' => [
                'id' => $industry->id,
                'name' => $industry->name
            ]
        ]);
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
        $this->validate($request, [
            'name' => 'required|max:255'
        ]);

        $industry = SmIndustry::findOrFail($id);
        $industry->name = $request->name;
        $industry->save();

        Toastr::success('Industry updated successfully!', 'Success');
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
        $industry = SmIndustry::findOrFail($id);
        $industry->delete();

        Toastr::success('Industry deleted successfully!', 'Success');
        return redirect()->back();
    }


    public function search(Request $request)
    {
        $searchTerm = trim($request->input('q'));

        if (!$searchTerm) {
            return response()->json([]);
        }

        return SmIndustry::where('name', 'LIKE', "%{$searchTerm}%")
            ->orderBy('name')
            ->limit(8)
            ->get(['id', 'name']);
    }


}

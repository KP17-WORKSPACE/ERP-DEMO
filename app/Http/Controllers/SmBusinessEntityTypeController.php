<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SmBusinessEntityType;
use App\SmBusinessActivity;
use Illuminate\Support\Facades\Validator;
use Brian2694\Toastr\Facades\Toastr;


class SmBusinessEntityTypeController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('PM');
    // }

    // ==========================================
    // LIST PAGE
    // ==========================================
    public function index()
    {
        try {
            $entities = SmBusinessEntityType::all();

            return view('backEnd.company.business_entity_type.index', compact('entities'));

        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    // ==========================================
    // STORE (ADD)
    // ==========================================
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => "required|string|max:255"
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {

            SmBusinessEntityType::create([
                'name' => $request->name,
            ]);

            Toastr::success('Business Entity Type added successfully', 'Success');
            return redirect()->back();

        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    // ==========================================
    // EDIT PAGE (NOT USED - using modal)
    // ==========================================
    public function edit($id)
    {
        try {
            $entity = SmBusinessEntityType::findOrFail($id);
            return response()->json($entity);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Not Found'], 404);
        }
    }

    // ==========================================
    // UPDATE
    // ==========================================
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => "required|string|max:255"
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {

            $entity = SmBusinessEntityType::findOrFail($id);

            $entity->name = $request->name;
            $entity->save();

            Toastr::success('Business Entity Type updated successfully', 'Success');
            return redirect()->back();

        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    // ==========================================
    // DELETE
    // ==========================================
    public function destroy($id)
    {
        try {
            SmBusinessEntityType::destroy($id);

            Toastr::success('Deleted successfully', 'Success');
            return redirect()->back();

        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    public function getBusinessSector($industry_id)
    {
        $sectors = SmBusinessActivity::where('industry_id', $industry_id)->get();

        return response()->json($sectors);
    }


    public function storeAjax(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $entity = SmBusinessEntityType::create([
                'name' => $request->name,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Business Entity Type added successfully',
                'data' => $entity
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Operation Failed'
            ], 500);
        }
    }

    public function search(Request $request)
{
    $searchTerm = trim($request->input('q'));

    if (!$searchTerm) {
        return response()->json([]);
    }

    return SmBusinessEntityType::where('name', 'LIKE', "%{$searchTerm}%")
        ->orderBy('name')
        ->limit(8)
        ->get(['id', 'name']);
}




}

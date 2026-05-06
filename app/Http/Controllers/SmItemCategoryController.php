<?php

namespace App\Http\Controllers;

use App\ApiBaseMethod;
use App\SmItemCategory;
use App\SmItemCategoryCart;
use App\SmItemSubcategory;
use App\SmItemSubcategoryCart;
use App\SysHelper;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PHPExcel;
use PHPExcel_IOFactory;

class SmItemCategoryController extends Controller
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
        $com_ids = SysHelper::get_company_access();
        $itemCategories = SmItemCategory::wherein('company_id', $com_ids)->orderBy('created_at', 'desc')->get();
        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            return ApiBaseMethod::sendResponse($itemCategories, null);
        }
        return view('backEnd.inventory.itemCategoryList', compact('itemCategories'));
    }

    public function import(Request $request)
    {
        try {
            $com_ids = SysHelper::get_company_access();
            $itemCategories = SmItemCategory::wherein('company_id', $com_ids)->get();
            $data = SmItemCategoryCart::where('company_id', session('logged_session_data.company_id'))->where('status', 1)->get()->toArray();
            return view('backEnd.inventory.importcategory', compact('itemCategories', 'data'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function importList(Request $request)
    {
        try {
            $companyId = session('logged_session_data.company_id');
            SmItemCategoryCart::where('company_id', $companyId)->delete();

            $file = $request->file('import_file');
            $ext = $file->getClientOriginalExtension();
            $fileName = md5($file->getClientOriginalName() . time()) . "." . $ext;
            $path = 'public/uploads/category_upload/';
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
            $file->move($path, $fileName);
            $selected_file = $path . $fileName;

            $objPHPExcel = PHPExcel_IOFactory::load($selected_file);
            $dataArray = $objPHPExcel->getActiveSheet()->toArray();
            $data = [];
            if (count($dataArray) > 1) {
                for ($i = 1; $i < count($dataArray); $i++) {
                    $row = $dataArray[$i];
                    if (isset($row[0]) && trim($row[0]) != '') {
                        $data[] = [
                            'category_name' => trim($row[0]),
                            'company_id' => $companyId,
                            'created_by' => Auth::user()->id,
                            'status' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
            }

            if (!empty($data)) {
                SmItemCategoryCart::insert($data);
            }

            Toastr::success('Category file parsed successfully', 'Success');
            return redirect('category-import');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function importClear(Request $request)
    {
        SmItemCategoryCart::where('company_id', session('logged_session_data.company_id'))->delete();
        Toastr::success('Category upload data cleared', 'Success');
        return redirect('category-import');
    }

    public function importSubCategory(Request $request)
    {
        try {
            $com_ids = SysHelper::get_company_access();
            $itemCategories = SmItemCategory::wherein('company_id', $com_ids)->orderBy('created_at', 'desc')->get();
            $data = SmItemSubcategoryCart::where('company_id', session('logged_session_data.company_id'))->where('status', 1)->get();
            return view('backEnd.inventory.importsubcategory', compact('itemCategories', 'data'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function importSubCategoryList(Request $request)
    {
        try {
            $companyId = session('logged_session_data.company_id');
            SmItemSubcategoryCart::where('company_id', $companyId)->delete();

            $file = $request->file('import_file');
            $ext = $file->getClientOriginalExtension();
            $fileName = md5($file->getClientOriginalName() . time()) . "." . $ext;
            $path = 'public/uploads/subcategory_upload/';
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
            $file->move($path, $fileName);
            $selected_file = $path . $fileName;

            $objPHPExcel = PHPExcel_IOFactory::load($selected_file);
            $dataArray = $objPHPExcel->getActiveSheet()->toArray();

            $data = [];
            if (count($dataArray) > 1) {
                $firstRow = array_shift($dataArray);
                $header = array_map('strtolower', array_map('trim', $firstRow));

                $categoryIndex = array_search('category', $header);
                $subCategoryIndex = array_search('sub_category', $header);

                if ($categoryIndex === false) {
                    $categoryIndex = 0;
                }
                if ($subCategoryIndex === false) {
                    $subCategoryIndex = 1;
                }

                foreach ($dataArray as $row) {
                    $category = isset($row[$categoryIndex]) ? trim($row[$categoryIndex]) : '';
                    $subCategory = isset($row[$subCategoryIndex]) ? trim($row[$subCategoryIndex]) : '';

                    if ($category !== '' && $subCategory !== '') {
                        $data[] = [
                            'category_name' => $category,
                            'sub_category_name' => $subCategory,
                            'company_id' => $companyId,
                            'created_by' => Auth::user()->id,
                            'status' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
            }

            if (!empty($data)) {
                SmItemSubcategoryCart::insert($data);
            }

            Toastr::success('Subcategory file parsed successfully', 'Success');
            return redirect('subcategory-import');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function importSubCategoryClear(Request $request)
    {
        SmItemSubcategoryCart::where('company_id', session('logged_session_data.company_id'))->delete();
        Toastr::success('Subcategory upload data cleared', 'Success');
        return redirect('subcategory-import');
    }

    public function importSubCategoryData(Request $request)
    {
        try {
            $companyId = session('logged_session_data.company_id');
            $data = SmItemSubcategoryCart::where('company_id', $companyId)->where('status', 1)->get();
            if ($data->isEmpty()) {
                Toastr::error('No data to import', 'Failed');
                return redirect('subcategory-import');
            }

            $existingCategories = SmItemCategory::where('company_id', $companyId)->pluck('category_name', 'id')->toArray();
            $existingSubCategories = SmItemSubcategory::where('company_id', $companyId)->get()->groupBy('category_id');

            foreach ($data as $row) {
                $categoryName = trim($row->category_name);
                $subCategoryName = trim($row->sub_category_name);
                if ($categoryName === '' || $subCategoryName === '') {
                    continue;
                }

                $categoryId = array_search($categoryName, $existingCategories, true);
                if ($categoryId === false) {
                    $cat = new SmItemCategory();
                    $cat->category_name = $categoryName;
                    $cat->created_by = Auth::user()->id;
                    $cat->company_id = $companyId;
                    $cat->created_at = now();
                    $cat->updated_at = now();
                    $cat->save();
                    $categoryId = $cat->id;
                    $existingCategories[$categoryId] = $categoryName;
                }

                $subCatsForCategory = $existingSubCategories->get($categoryId, collect())->pluck('sub_category_name')->map(function ($v) {
                    return mb_strtolower(trim($v));
                })->toArray();

                if (!in_array(mb_strtolower($subCategoryName), $subCatsForCategory)) {
                    $subCat = new SmItemSubcategory();
                    $subCat->category_id = $categoryId;
                    $subCat->sub_category_name = $subCategoryName;
                    $subCat->created_by = Auth::user()->id;
                    $subCat->company_id = $companyId;
                    $subCat->created_at = now();
                    $subCat->updated_at = now();
                    $subCat->save();
                    $subCategoryCollection = $existingSubCategories->get($categoryId, collect());
                    $subCategoryCollection->push((object) ['sub_category_name' => $subCategoryName]);
                    $existingSubCategories->put($categoryId, $subCategoryCollection);
                }
            }

            SmItemSubcategoryCart::where('company_id', $companyId)->delete();

            Toastr::success('Subcategories imported successfully', 'Success');
            return redirect('subcategory-import');
        } catch (\Exception $e) {
            dd($e->getMessage());
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function importData(Request $request)
    {
        try {
            $companyId = session('logged_session_data.company_id');
            $data = SmItemCategoryCart::where('company_id', $companyId)->where('status', 1)->get();
            if ($data->isEmpty()) {
                Toastr::error('No data to import', 'Failed');
                return redirect('category-import');
            }

            $existing = SmItemCategory::where('company_id', $companyId)->pluck('category_name')->map(function ($v) {
                return mb_strtolower(trim($v));
            })->toArray();

            $insert = [];
            foreach ($data as $row) {
                $title = trim($row->category_name);
                if ($title == '') {
                    continue;
                }
                if (!in_array(mb_strtolower($title), $existing)) {
                    $insert[] = [
                        'category_name' => $title,
                        'created_by' => Auth::user()->id,
                        'company_id' => $companyId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if (!empty($insert)) {
                SmItemCategory::insert($insert);
            }

            SmItemCategoryCart::where('company_id', $companyId)->delete();

            Toastr::success('Category Imported successfully', 'Success');
            return redirect('category-import');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function SubCategory(Request $request)
    {
        $ctrl_category = "";
        $com_ids = SysHelper::get_company_access();
        $subCategories = SmItemSubcategory::query()
            ->whereIn('company_id', $com_ids);

        if ($request->filled('category')) {
            $subCategories->where('category_id', $request->category);
            $ctrl_category = $request->category;

        }
        $itemCategories = SmItemCategory::wherein('company_id', $com_ids)->orderBy('created_at', 'desc')->get();
        if ($ctrl_category != "") {
            $selectedCategory = SmItemCategory::where('id', $ctrl_category)->first();
        }else{
            $selectedCategory = null;
        }
        $subCategories = $subCategories->orderBy('created_at', 'desc')->get();

        return view('backEnd.inventory.createSubCategory', compact('itemCategories', 'subCategories', 'selectedCategory'));
    }
    public function createSubCategory($id)
    {
        $com_ids = SysHelper::get_company_access();
        $itemCategories = SmItemCategory::wherein('company_id', $com_ids)->get();
        $selectedCategory = SmItemCategory::find($id);
        $subCategories = SmItemSubcategory::where('category_id', $id)->wherein('company_id', $com_ids)->get();
        return view('backEnd.inventory.createSubCategory', compact('itemCategories', 'selectedCategory', 'subCategories'));
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'category_name' => "required|unique:sm_item_categories,category_name"
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                $message = $validator->errors()->first('category_name');
                return response()->json(['success' => false, 'type' => 'duplicate', 'message' => $message]);
            }
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $categories = new SmItemCategory();
        $categories->category_name = $request->category_name;
        $categories->created_by = Auth::user()->id;
        $categories->company_id = session('logged_session_data.company_id');
        $results = $categories->save();

        if ($request->ajax()) {
            if ($results) {
                return response()->json(['success' => true, 'id' => $categories->id, 'category_name' => $categories->category_name]);
            } else {
                return response()->json(['success' => false, 'message' => 'Something went wrong, please try again.']);
            }
        }

        // return $results;
        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            if ($results) {
                return ApiBaseMethod::sendResponse(null, 'Operation successfully');
            } else {
                return ApiBaseMethod::sendError('Something went wrong, please try again');
            }
        } else {
            if ($results) {
                return redirect()->back()->with('message-success', 'Operation successfully');
            } else {
                return redirect()->back()->with('message-danger', 'Something went wrong, please try again');
            }
        }
    }







    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $com_ids = SysHelper::get_company_access();
        $editData = SmItemCategory::find($id);
        $itemCategories = SmItemCategory::wherein('company_id', $com_ids)->get();

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            $data = [];
            $data['editData'] = $editData->toArray();
            $data['itemCategories'] = $itemCategories->toArray();
            return ApiBaseMethod::sendResponse($data, null);
        }
        return view('backEnd.inventory.itemCategoryList', compact('itemCategories', 'editData'));
    }



    public function editSubCategory(Request $request, $id)
    {
        $com_ids = SysHelper::get_company_access();
        $editData = SmItemSubcategory::find($id);
        $itemCategories = SmItemCategory::wherein('company_id', $com_ids)->get();

        $selectedCategory = SmItemCategory::find($editData->category_id);
        $subCategories = SmItemSubcategory::where('category_id', $editData->category_id)->wherein('company_id', $com_ids)->get();



        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            $data = [];
            $data['editData'] = $editData->toArray();
            $data['itemCategories'] = $itemCategories->toArray();
            return ApiBaseMethod::sendResponse($data, null);
        }
        return view('backEnd.inventory.createSubCategory', compact('itemCategories', 'editData', 'selectedCategory', 'subCategories'));
    }


    /************************* Start Store Sub Category ****************************** */

    public function StoreSubCategory(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'category' => "required",
            'sub_category_name' => "required|unique:sm_item_subcategories,sub_category_name"
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                $message = $validator->errors()->first(
                    $validator->errors()->has('category') ? 'category' : 'sub_category_name'
                );
                return response()->json(['success' => false, 'type' => 'duplicate', 'message' => $message]);
            }
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $s = new SmItemSubcategory();
        $s->category_id = $request->category;
        $s->sub_category_name = $request->sub_category_name;
        $s->created_by = Auth::user()->id;
        $s->company_id = session('logged_session_data.company_id');
        $results = $s->save();

        if ($request->ajax()) {
            if ($results) {
                return response()->json(['success' => true, 'id' => $s->id, 'sub_category_name' => $s->sub_category_name]);
            } else {
                return response()->json(['success' => false, 'message' => 'Something went wrong, please try again.']);
            }
        }

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            if ($results) {
                return ApiBaseMethod::sendResponse(null, 'Operation successfully');
            } else {
                return ApiBaseMethod::sendError('Something went wrong, please try again');
            }
        } else {
            if ($results) {
                return redirect()->route('createSubCategory', $request->category)->with('message-success', 'Operation successfully');
            } else {
                return redirect()->back()->with('message-danger', 'Something went wrong, please try again');
            }
        }
    }
    /************************* END Store Sub Category ****************************** */



    /************************* Start Update Sub Category ****************************** */

    public function updateSubCategory(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'category' => "required",
            'sub_category_name' => "required",
        ]);

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $s = SmItemSubcategory::find($request->id);
        $s->category_id = $request->category;
        $s->sub_category_name = $request->sub_category_name;
        $s->updated_by = Auth::user()->id;
        $s->company_id = session('logged_session_data.company_id');
        $results = $s->save();

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            if ($results) {
                return ApiBaseMethod::sendResponse(null, 'Operation successfully');
            } else {
                return ApiBaseMethod::sendError('Something went wrong, please try again');
            }
        } else {
            if ($results) {
                return redirect()->back()->with('message-success', 'Operation successfully');
            } else {
                return redirect()->back()->with('message-danger', 'Something went wrong, please try again');
            }
        }
    }
    /************************* End update Sub Category ****************************** */












    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'category_name' => "required|unique:sm_item_categories,category_name," . $id,
        ]);

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $categories = SmItemCategory::find($id);
        $categories->category_name = $request->category_name;
        $categories->updated_by = Auth::user()->id;
        $categories->company_id = session('logged_session_data.company_id');
        $results = $categories->update();

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            if ($results) {
                return ApiBaseMethod::sendResponse(null, 'Operation successfully');
            } else {
                return ApiBaseMethod::sendError('Something went wrong, please try again');
            }
        } else {
            if ($results) {
                return redirect('item-category')->with('message-success', 'Operation successfully');
            } else {
                return redirect()->back()->with('message-danger', 'Something went wrong, please try again');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function deleteItemCategoryView(Request $request, $id)
    {
        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            return ApiBaseMethod::sendResponse($id, null);
        }

        $result = SmItemCategory::destroy($id);

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            if ($result) {
                return ApiBaseMethod::sendResponse(null, 'Operation successfully');
            } else {
                return ApiBaseMethod::sendError('Something went wrong, please try again.');
            }
        } else {
            if ($result) {
                return redirect('item-category')->with('message-success-delete', 'Operation successfully');
            } else {
                return redirect()->back()->with('message-danger-delete', 'Something went wrong, please try again');
            }
        }

        // return view('backEnd.inventory.deleteItemCategoryView', compact('id'));
    }

    public function deleteItemCategory(Request $request, $id)
    {
        $result = SmItemCategory::destroy($id);

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            if ($result) {
                return ApiBaseMethod::sendResponse(null, 'Operation successfully');
            } else {
                return ApiBaseMethod::sendError('Something went wrong, please try again.');
            }
        } else {
            if ($result) {
                return redirect('item-category')->with('message-success-delete', 'Operation successfully');
            } else {
                return redirect()->back()->with('message-danger-delete', 'Something went wrong, please try again');
            }
        }
    }
    public function deleteSucategory(Request $request, $id)
    {


        $d = SmItemSubcategory::find($id);

        $selectedCategory = SmItemCategory::find($d->category_id);

        $result = SmItemSubcategory::destroy($id);

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            if ($result) {
                return ApiBaseMethod::sendResponse(null, 'Operation successfully');
            } else {
                return ApiBaseMethod::sendError('Something went wrong, please try again.');
            }
        } else {
            if ($result) {
                Toastr::success('Operation successful', 'Success');
                return redirect()->back()->with([['message-success', 'Operation successfully'], ['id', $selectedCategory->id]]);
            } else {
                return redirect()->back()->with('message-danger', 'Something went wrong, please try again');
            }
        }
    }
}

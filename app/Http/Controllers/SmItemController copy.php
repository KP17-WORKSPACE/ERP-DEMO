<?php

namespace App\Http\Controllers;

use App\ApiBaseMethod;
use Illuminate\Http\Request;
use App\SmItem;
use App\SmItemCategory;
use App\SmItemSubcategory;
use App\SmStaff;
use App\SysBrand;
use App\SysCompany;
use App\SysHelper;
use App\SysPriceBook;
use App\SysProductType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;
//use Validator;
class SmItemController extends Controller
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
        $r = SysHelper::get_data_by_role();
        $company_id = $r[0];
        $items = SmItem::wherein('company_id', $company_id)->get();
        $itemCategories = SmItemCategory::wherein('company_id', $company_id)->get();
        $SuCategories = SmItemSubcategory::wherein('company_id', $company_id)->get();
        $brands = SysBrand::wherein('company_id', $company_id)->get();
        $producttype = SysProductType::get();

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            $data = [];
            $data['items'] = $items->toArray();
            $data['itemCategories'] = $itemCategories->toArray();
            $data['brands'] = $brands->toArray();
            $data['producttype'] = $producttype->toArray();
            return ApiBaseMethod::sendResponse($data, null);
        }
        return view('backEnd.inventory.itemList', compact('items', 'itemCategories', 'SuCategories', 'brands', 'producttype'));
    }

    public function remove_duplicate(Request $request)
    {
        try {
            db::beginTransaction();

            $part_numbers = $request->item_partno;
            $partnos = explode(",", $part_numbers);

            if (count($partnos) > 0) {
                //return DB::table('sys_crm_quote_items')->wherein('product_id',$partnos)->get();
                DB::table('sys_crm_quote_items')->wherein('product_id', $partnos)->update(['product_id' => $partnos[0]]);
                DB::table('sys_item_stock')->wherein('partno', $partnos)->update(['partno' => $partnos[0]]);
                DB::table('sys_purchase_order_items')->wherein('part_number', $partnos)->update(['part_number' => $partnos[0]]);
                DB::table('sys_purchase_grn_items')->wherein('part_no', $partnos)->update(['part_no' => $partnos[0]]);
                DB::table('sys_purchase_invoice_items')->wherein('part_number', $partnos)->update(['part_number' => $partnos[0]]);
                DB::table('sys_purchase_return_list')->wherein('partno', $partnos)->update(['partno' => $partnos[0]]);
                DB::table('sys_sales_invoice_items')->wherein('part_number', $partnos)->update(['part_number' => $partnos[0]]);
                DB::table('sys_delivery_note_items')->wherein('part_number', $partnos)->update(['part_number' => $partnos[0]]);
                DB::table('sys_sales_return_list')->wherein('part_number', $partnos)->update(['part_number' => $partnos[0]]);
                DB::table('sys_delivery_note_items_srl')->wherein('part_number', $partnos)->update(['part_number' => $partnos[0]]);
                DB::table('sys_purchase_grn_items_srlno')->wherein('part_no', $partnos)->update(['part_no' => $partnos[0]]);
            }
            if (count($partnos) > 0) {
                for ($i = 1; $i < count($partnos); $i++) {

                    DB::table('sm_items')->where('id', $partnos[$i])->delete();
                }
            }
            db::commit();
            Toastr::success('Delete successful', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            db::rollBack();
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    public function itemadd(Request $request)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];

            $brand = SysBrand::select('id', 'title')->orderby('title', 'asc')->get();
            $category = DB::table('sm_item_categories')->select('id', 'category_name')->orderby('category_name', 'asc')->get();
            $sub_category = DB::table('sm_item_subcategories')->select('id', 'sub_category_name')->orderby('sub_category_name', 'asc')->get();


            $ctrl_part_number = "";
            $ctrl_brand = "";
            $ctrl_category = "";
            $ctrl_sub_category = "";
            // Start the base query
            $itemsQuery = SmItem::where(function ($query) use ($company_id) {
                foreach ($company_id as $cid) {
                    $query->orWhereRaw("FIND_IN_SET(?, company_id) > 0", [$cid]);
                }
            });


            if ($request->filled('part_number')) {
                $itemsQuery->where(function ($q) use ($request) {
                    $q->where('part_number', 'like', '%' . $request->part_number . '%')
                        ->orWhere('description', 'like', '%' . $request->part_number . '%');
                });

                $ctrl_part_number = $request->part_number;
            }

            if ($request->filled('brand')) {
                $itemsQuery->where('brand', $request->brand);
                $ctrl_brand = $request->brand;

            }

            if ($request->filled('category')) {
                $itemsQuery->where('category_name', $request->category);
                $ctrl_category = $request->category;

            }

            if ($request->filled('sub_category')) {
                $itemsQuery->where('subcategory_name', $request->sub_category);
                $ctrl_sub_category = $request->sub_category;

            }
            // if ($_POST) {





            //     // $pno = $request->part_number;
            //     // $items = SmItem::where(
            //     //     function ($query) use ($company_id) {
            //     //         foreach ($company_id as $cid) {
            //     //             $query->orWhereRaw("FIND_IN_SET($cid, company_id) > 0");
            //     //         }
            //     //     }
            //     // )->where(
            //     //         function ($q) use ($pno) {
            //     //             $q->orwhere('part_number', 'like', '%' . $pno . '%')
            //     //                 ->orwhere('description', 'like', '%' . $pno . '%');
            //     //         }
            //     //     )
            //     //     ->orderby('part_number', 'asc')->paginate(30);


            // }
            // // else {
            // //     $items = SmItem::where(
            // //         function ($query) use ($company_id) {
            // //             foreach ($company_id as $cid) {
            // //                 $query->orWhereRaw("FIND_IN_SET($cid, company_id) > 0");
            // //             }
            // //         }
            // //     )->orderby('part_number', 'asc')->paginate(30);
            // // }



            $hasFilters = $request->filled('part_number') || $request->filled('brand') || $request->filled('category') || $request->filled('sub_category');


            $itemsQuery = $itemsQuery
                ->orderByRaw("CASE WHEN status = 1 THEN 0 ELSE 1 END") // Active items first
                ->orderBy('id', 'desc'); // Newest first
               

            $items = $hasFilters ? $itemsQuery->get() : $itemsQuery->paginate(30);

            $itemCategories = SmItemCategory::orderby('category_name', 'asc')->get();
            $SuCategories = SmItemSubcategory::orderby('sub_category_name', 'asc')->get();
            $brands = SysBrand::orderby('title', 'asc')->get();
            $producttype = SysProductType::get();
            //$company = SysCompany::select('id','company_name')->wherein('id',$company_id)->get();
            $company = SysHelper::get_company_names();
            $item_list = SmItem::select('id', 'part_number')->where('status', 1)->orderby('part_number', 'asc')->get();

            $dup_item_list = SmItem::select('part_number')->where('status', 1)->groupBy('part_number')->orderby('part_number', 'asc')->havingRaw('COUNT(*) >= 2')->pluck('part_number');

            $user_list = SmStaff::select('user_id', 'full_name')->get();



            return view('backEnd.inventory.itemAdd', compact('items', 'itemCategories', 'SuCategories', 'brands', 'producttype', 'company', 'item_list', 'user_list', 'dup_item_list', 'brand', 'category', 'sub_category', 'ctrl_brand', 'ctrl_category', 'ctrl_sub_category', 'ctrl_part_number','hasFilters'));

        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function item_company_access_update()
    {
        try {
            db::beginTransaction();
            $commaSeparated = SysCompany::pluck('id')->implode(',');
            SmItem::where('status', 1)->update(['company_id' => $commaSeparated]);
            db::commit();
            Toastr::success('Items Company Updated Successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            //throw $th;
            db::rollBack();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
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


        if ($request->item_code == "") {
            Toastr::error('Product Code Missing', 'Failed');
            return redirect()->back();
        }
        if ($request->part_number == "") {
            Toastr::error('Part Number Missing', 'Failed');
            return redirect()->back();
        }
        if ($request->brand == "") {
            Toastr::error('Brand Missing', 'Failed');
            return redirect()->back();
        }
        if ($request->product_type == "") {
            Toastr::error('Product Type Missing', 'Failed');
            return redirect()->back();
        }
        if ($request->category_name == "") {
            Toastr::error('Category Name Missing', 'Failed');
            return redirect()->back();
        }
        if ($request->subcategory_name == "") {
            Toastr::error('Subcategory Name Missing', 'Failed');
            return redirect()->back();
        }
        if ($request->description == "") {
            Toastr::error('Description Missing', 'Failed');
            return redirect()->back();
        }

        if ($request->vat == "") {
            $vat = "0";
        } else {
            $vat = $request->vat;
        }
        if ($request->uom == "") {
            $uom = "0";
        } else {
            $uom = $request->uom;
        }
        if ($request->coo == "") {
            $coo = "0";
        } else {
            $coo = $request->coo;
        }
        if ($request->hscode == "") {
            $hscode = "0";
        } else {
            $hscode = $request->hscode;
        }


        $check_part_number = SmItem::select('id')->where('item_code', $request->part_number)->get();
        if (count($check_part_number) > 0) {
            return redirect()->back()->with('message-danger', 'Part Number Already Existing, please check try again');
        }

        try {
            $company_id = SysCompany::pluck('id')->implode(',');
            //if($request->company_id!="") { $company_id =implode(",",$request->company_id); }
            //if(!in_array(1,$request->company_id)){ $company_id='1,'.$company_id; }

            $items = new SmItem();
            //$items->item_name = $request->item_name;
            $items->item_code = SysHelper::get_new_code('sm_items', 'ITM', 'item_code');
            $items->part_number = $request->part_number;
            $items->brand = $request->brand;
            $items->product_type = $request->product_type;
            $items->category_name = $request->category_name;
            $items->subcategory_name = $request->subcategory_name;
            $items->description = $request->description;
            $items->vat = $vat;
            $items->uom = $uom;
            $items->coo = $coo;
            $items->hscode = $hscode;
            if ($request->weight != "") {
                $items->weight = $request->weight;
            }

            $items->status = $request->status;
            $items->created_by = Auth::user()->id;
            $items->company_id = $company_id;
            $results = $items->save();



            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($results) {
                    return ApiBaseMethod::sendResponse(null, 'New Category has been added successfully');
                } else {
                    return ApiBaseMethod::sendError('Something went wrong, please try again');
                }
            } else {
                if ($results) {
                    return redirect()->back()->with('message-success', 'New Item has been added successfully');
                } else {
                    return redirect()->back()->with('message-danger', 'Something went wrong, please try again');
                }
            }
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }

    }
    public function itemadd_modal(Request $request)
    {
        if ($request->vat == "") {
            $vat = "0";
        } else {
            $vat = $request->vat;
        }
        if ($request->uom == "") {
            $uom = "0";
        } else {
            $uom = $request->uom;
        }
        if ($request->coo == "") {
            $coo = "0";
        } else {
            $coo = $request->coo;
        }
        if ($request->hscode == "") {
            $hscode = "0";
        } else {
            $hscode = $request->hscode;
        }


        $check_part_number = SmItem::select('id')->where('item_code', $request->part_number)->get();
        if (count($check_part_number) > 0) {
            return response()->json(['warning' => true]);
        }

        try {
            $company_id = SysCompany::pluck('id')->implode(',');
            //if($request->company_id!="") { $company_id =implode(",",$request->company_id); }
            //if(!in_array(1,$request->company_id)){ $company_id='1,'.$company_id; }

            $items = new SmItem();
            $items->item_code = SysHelper::get_new_code('sm_items', 'ITM', 'item_code');
            $items->part_number = $request->part_number;
            $items->brand = $request->brand;
            $items->product_type = $request->product_type;
            $items->category_name = $request->category_name;
            $items->subcategory_name = $request->subcategory_name;
            $items->description = $request->description;
            $items->vat = $vat;
            $items->uom = $uom;
            $items->coo = $coo;
            $items->hscode = $hscode;
            if ($request->weight != "") {
                $items->weight = $request->weight;
            }

            $items->status = 1;
            $items->created_by = Auth::user()->id;
            $items->company_id = $company_id;
            $results = $items->save();


            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['error' => true]);
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
    public function itemedit(Request $request, $id)
    {
        try {
            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            $editData = SmItem::find($id);
            // $items = SmItem::all();
            if ($_POST) {
                $pno = $request->part_number;
                $items = SmItem::where(
                    function ($query) use ($company_id) {
                        foreach ($company_id as $cid) {
                            $query->orWhereRaw("FIND_IN_SET($cid, company_id) > 0");
                        }
                    }
                )->where(
                        function ($q) use ($pno) {
                            $q->orwhere('part_number', 'like', '%' . $pno . '%')
                                ->orwhere('description', 'like', '%' . $pno . '%');
                        }
                    )->paginate(30);
            } else {
                $items = SmItem::where(
                    function ($query) use ($company_id) {
                        foreach ($company_id as $cid) {
                            $query->orWhereRaw("FIND_IN_SET($cid, company_id) > 0");
                        }
                    }
                )->orderby('created_at', 'desc')->paginate(30);
            }
            /*$itemCategories = SmItemCategory::all();
            $SuCategories = SmItemSubcategory::all();
            $brands = SysBrand::all();
            $producttype = SysProductType::all();*/

            $itemCategories = SmItemCategory::orderby('category_name', 'asc')->get();
            $SuCategories = SmItemSubcategory::orderby('sub_category_name', 'asc')->get();
            $brands = SysBrand::orderby('title', 'asc')->get();
            $producttype = SysProductType::get();
            //$company = SysCompany::select('id','company_name')->wherein('id',$company_id)->get();
            $company = SysHelper::get_company_names();
            $user_list = SmStaff::select('user_id', 'full_name')->get();

            $item_list = SmItem::select('id', 'part_number')->where('status', 1)->orderby('part_number', 'asc')->get();
            $dup_item_list = SmItem::select('part_number')->where('status', 1)->groupBy('part_number')->orderby('part_number', 'asc')->havingRaw('COUNT(*) >= 2')->pluck('part_number');
            return view('backEnd.inventory.itemAdd', compact('editData', 'items', 'item_list', 'itemCategories', 'SuCategories', 'brands', 'producttype', 'company', 'user_list', 'dup_item_list'));
        } catch (\Throwable $th) {
            return $th;
        }
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
        // $validator = Validator::make($input, [
        //    //'item_name' => "required",
        //    'item_code' => "required",
        //    'part_number' => "required",
        //    'brand' => "required",
        //    'product_type' => "required",
        //    'category_name' => "required",
        //    'subcategory_name' => "required",
        //    'description' => "required",
        //    'vat' => "required",
        //    'uom' => "required",
        //    'status' => "required",

        // ]);

        // if ($validator->fails()) {
        //     if (ApiBaseMethod::checkUrl($request->fullUrl())) {
        //         return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
        //     }
        //     return redirect()->back()
        //         ->withErrors($validator)
        //         ->withInput();
        // }

        /*$check_item_code = SmItem::select('id')->where('item_code', $request->item_code)->get();
        $check_part_number = SmItem::select('id')->where('item_code', $request->part_number)->get();
        if(count($check_item_code)>0){
            return redirect()->back()->with('message-danger', 'Product Code Already Existing, please check try again');
        }
        if(count($check_part_number)>0){
            return redirect()->back()->with('message-danger', 'Part Number Already Existing, please check try again');
        }*/

        if ($request->vat == "") {
            $vat = "0";
        } else {
            $vat = $request->vat;
        }
        if ($request->uom == "") {
            $uom = "0";
        } else {
            $uom = $request->uom;
        }
        if ($request->coo == "") {
            $coo = "0";
        } else {
            $coo = $request->coo;
        }
        if ($request->hscode == "") {
            $hscode = "0";
        } else {
            $hscode = $request->hscode;
        }

        $company_id = "";
        if ($request->company_id != "") {
            $company_id = implode(",", $request->company_id);
        }
        if (!in_array(1, $request->company_id)) {
            $company_id = '1,' . $company_id;
        }

        $items = SmItem::find($id);
        //$items->item_code = $request->item_code;
        $items->part_number = $request->part_number;
        $items->brand = $request->brand;
        $items->product_type = $request->product_type;
        $items->category_name = $request->category_name;
        $items->subcategory_name = $request->subcategory_name;
        $items->description = $request->description;
        $items->vat = $vat;
        $items->uom = $uom;
        $items->coo = $coo;
        $items->hscode = $hscode;
        if ($request->weight != "") {
            $items->weight = $request->weight;
        }
        $items->status = $request->status;
        $items->updated_by = Auth::user()->id;
        $items->company_id = $company_id;
        $results = $items->update();

        if ($request->weight != "") {
            $cl_weight = $request->weight;
        } else {
            $cl_weight = 0;
        }

        $clearance = DB::table('sys_clearance_items')->where('pid', $id)->get();
        if (count($clearance) > 0) {
            foreach ($clearance as $key) {
                DB::table('sys_clearance_items')->where('id', $key->id)->update([
                    'partno' => $request->part_number,
                    'coo' => $coo,
                    'hscode' => $hscode,
                    'weight' => $cl_weight * $key->qty,
                ]);
            }
        }
        $clearance_cart = DB::table('sys_clearance_items_cart')->where('pid', $id)->get();
        if (count($clearance_cart) > 0) {
            foreach ($clearance_cart as $key) {
                DB::table('sys_clearance_items_cart')->where('id', $key->id)->update([
                    'partno' => $request->part_number,
                    'coo' => $coo,
                    'hscode' => $hscode,
                    'weight' => $cl_weight * $key->qty,
                ]);
            }
        }

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            if ($results) {
                return ApiBaseMethod::sendResponse(null, 'Item has been updated successfully');
            } else {
                return ApiBaseMethod::sendError('Something went wrong, please try again');
            }
        } else {
            if ($results) {
                return redirect('item-add')->with('message-success', 'Item has been updated successfully');
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

    public function deleteItemView(Request $request, $id)
    {


        //if (ApiBaseMethod::checkUrl($request->fullUrl())) {
        //return ApiBaseMethod::sendResponse($id, null);
        //}
        return view('backEnd.inventory.deleteItemView', compact('id'));
    }



    public function deleteSubItemView(Request $request, $id)
    {

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            return ApiBaseMethod::sendResponse($id, null);
        }
        return view('backEnd.inventory.delete_sub_category_view', compact('id'));
    }


    public function deleteItem(Request $request, $id)
    {

        try {
            $pro = SmItem::find($id);
            $pro->status = 0;
            $pro->updated_by = Auth()->user()->id;
            $results = $pro->update();

            Toastr::success('Delete successful', 'Success');
            return redirect('item-add');

        } catch (\Throwable $th) {
            return $th;
        }

        // $result = SmItem::destroy($id);

        // if (ApiBaseMethod::checkUrl($request->fullUrl())) {
        //     if ($result) {
        //         return ApiBaseMethod::sendResponse(null, 'Item  has been deleted successfully');
        //     } else {
        //         return ApiBaseMethod::sendError('Something went wrong, please try again.');
        //     }
        // } else {
        //     if ($result) {
        //         return redirect('item-list')->with('message-success-delete', 'Item  has been deleted successfully');
        //     } else {
        //         return redirect()->back()->with('message-danger-delete', 'Something went wrong, please try again');
        //     }
        // }

    }


    public function getProductDetails($id)
    {
        $product = SmItem::findOrFail($id);
        return response()->json($product);
    }

    public function update_itemModal(Request $request)
    {
        // Default fallback values
        $vat = $request->vat ?? '0';
        $uom = $request->uom ?? '0';
        $coo = $request->coo ?? '0';
        $hscode = $request->hscode ?? '0';
        $weight = $request->weight ?? null;

        // Ensure the product exists
        $product = SmItem::find($request->edit_product_id);
        if (!$product) {
            return response()->json(['error' => true, 'message' => 'Product not found.']);
        }

        // Optional: Check for duplicate part_number (but skip current one)
        $duplicate = SmItem::where('item_code', $request->part_number)
            ->where('id', '!=', $request->edit_product_id)
            ->exists();
        if ($duplicate) {
            return response()->json(['warning' => true, 'message' => 'Part number already exists.']);
        }

        try {
            $product->part_number = $request->part_number;
            $product->brand = $request->brand;
            $product->product_type = $request->product_type;
            $product->category_name = $request->category_name;
            $product->subcategory_name = $request->subcategory_name;
            $product->description = $request->description;
            $product->vat = $vat;
            $product->uom = $uom;
            $product->coo = $coo;
            $product->hscode = $hscode;
            $product->weight = $weight;

            $product->updated_by = Auth::id();
            $product->save();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'An unexpected error occurred.',
                'debug' => $e->getMessage()
            ]);
        }
    }

}
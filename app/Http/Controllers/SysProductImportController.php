<?php

namespace App\Http\Controllers;

use App\ApiBaseMethod;
use App\Imports\UsersImport;
use App\Imports\YourImportClass;
use App\SmInspectingDepartment;
use App\SmItem;
use Illuminate\Http\Request;
use App\SmItemStore;
use App\SysChartofAccounts;
use App\SysCompany;
use App\SysCurrencySettings;
use App\SysHelper;
use App\SysItemOpeningStock;
use App\SysItemStock;
use App\SysPaymentTerms;
use App\SysShipping;
use App\SmItemsCart;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
//use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Input;
//use Maatwebsite\Excel\Concerns\ToModel;
use Validator;
use PHPExcel; 
use PHPExcel_IOFactory;

class SysProductImportController extends Controller
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
        try{
            $data = DB::table('sm_items_cart as i')->select('i.*')->where('i.status',1)->where('i.company_id',session('logged_session_data.company_id'))->get();
            //return $data;

            $brand = DB::table('sys_brand')->select('id','title')->get();
            $cat = DB::table('sm_item_categories')->select('id','category_name')->get();
            $subcat = DB::table('sm_item_subcategories')->select('id','sub_category_name')->get();

            return view('backEnd.inventory.importproduct',compact('data','brand','cat','subcat'));
        }catch (\Exception $e) {
           return $e;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back();
        }
    }
    public function list(Request $request)
    {
        try{
            DB::beginTransaction();
            $selected_file = "";
            if ($request->file('import_file') != "") {
                $file = $request->file('import_file');
                $selected_file = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('public/uploads/product_upload/', $selected_file);
                $selected_file = 'public/uploads/product_upload/' . $selected_file;
                //return  $selected_file;
            }

            $objPHPExcel = PHPExcel_IOFactory::load($selected_file);
            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $objWorksheet->getHighestRow();

            $dataArray = $objPHPExcel->getActiveSheet()->toArray();
            //return count($dataArray[0]);
            /*->rangeToArray(
            'A1:C4',     // The worksheet range that we want to retrieve
            NULL,        // Value that should be returned for empty cells
            TRUE,        // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
            TRUE,        // Should values be formatted (the equivalent of getFormattedValue() for each cell)
            TRUE         // Should the array be indexed by cell row and cell column
            );*/

            for($i=1; $i < count($dataArray); $i++){
                
                    //for($j=0; $j < count($dataArray[0]); $j++){
                        $data [] = [
                            $dataArray[0][0] => $dataArray[$i][0],
                            $dataArray[0][1] => $dataArray[$i][1],
                            $dataArray[0][2] => $dataArray[$i][2],
                            $dataArray[0][3] => $dataArray[$i][3],
                            $dataArray[0][4] => $dataArray[$i][4],
                            $dataArray[0][5] => $dataArray[$i][5],
                            $dataArray[0][6] => $dataArray[$i][6],
                            $dataArray[0][7] => $dataArray[$i][7],
                            $dataArray[0][8] => $dataArray[$i][8],
                            $dataArray[0][9] => $dataArray[$i][9],
                            $dataArray[0][10] => $dataArray[$i][10],
                            'company_id' => session('logged_session_data.company_id'),
                            
                        ];
                    //}
                    //$data2[]=$data;

            }
            
            foreach (array_chunk($data,1000) as $dt) {
                SmItemsCart::insert($dt);
            }

            DB::commit();
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        }catch (\Exception $e) {
            DB::rollBack();            
           return $e;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back();
        }
    }

    public function list_clear(Request $request){
        try{
            SmItemsCart::where('company_id',session('logged_session_data.company_id'))->delete();
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        }catch (\Exception $e) {
           return $e;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back();
        }
    }
    public function import_data(Request $request)
    {
        try{
            DB::beginTransaction();
            $part_number = DB::table('sm_items')->pluck('part_number');
            
            $data = DB::table('sm_items_cart as i')->select('i.*')->where('i.status',1)->where('i.company_id',session('logged_session_data.company_id'))
            ->whereNotIn('i.part_number',$part_number)->get();

            $brand = DB::table('sys_brand')->select('id','title')->get();
            $cat = DB::table('sm_item_categories')->select('id','category_name')->get();
            $subcat = DB::table('sm_item_subcategories')->select('id','sub_category_name')->get();

            $company_id = SysCompany::pluck('id')->implode(',');

            $brand_id=0; $category_id=0; $subcategory_id=0; $product_type=0;
            foreach($data as $dt){
                $brand_id = $brand->where('title',$dt->brand)->max('id');
                $category_id = $cat->where('category_name',$dt->category_name)->max('id');
                $subcategory_id = $subcat->where('sub_category_name',$dt->subcategory_name)->max('id');
                if($brand_id=="") {$brand_id=0;}
                if($category_id=="") {$category_id=0;}
                if($subcategory_id=="") {$subcategory_id=0;}
                if($product_type=="General") {$product_type=1;} else {$product_type=2;}
                
                $inData[]=[
                    'item_name' => '',
                    'item_code' => SysHelper::get_new_code('sm_items','ITM','item_code'),
                    'part_number' => $dt->part_number,
                    'brand' => $brand_id,
                    'product_type' => $product_type,
                    'category_name' => $category_id,
                    'subcategory_name' => $subcategory_id,
                    'description' => $dt->description,
                    'vat' => $dt->vat,
                    'uom' => $dt->uom,
                    'coo' => $dt->coo,
                    'hscode' => $dt->hscode,
                    'weight' => $dt->weight,
                    'status' => 1,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now('+04:00'),
                    'company_id' => $company_id,
                ];
            }
            foreach (array_chunk($inData,1000) as $dt) {
                SmItem::insert($dt);
            }
            
            SmItemsCart::where('company_id',session('logged_session_data.company_id'))->delete();
            DB::commit();
            Toastr::success('Product Imported successfully', 'Success');
            return redirect()->back();
            
        }catch (\Exception $e) {
            DB::rollBack();
           return $e;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back();
        }
    }
    

}
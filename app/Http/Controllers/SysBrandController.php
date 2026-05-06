<?php

namespace App\Http\Controllers;

use App\ApiBaseMethod;
use App\SysBrand;
use App\SysBrandCart;
use App\SysHelper;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use PHPExcel_IOFactory;
class SysBrandController extends Controller
{

    public function __construct()
    {
        $this->middleware('PM');
    }

    public function index(Request $request)
    {
        try{
            $com_ids = SysHelper::get_company_access();
            $brands = SysBrand::where('active_status', 1)->wherein('company_id',$com_ids)->get();
            return view('backEnd.humanResource.brands', compact('brands'));
        }catch (\Exception $e) {
            return $e;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function import(Request $request)
    {
        try {
            $com_ids = SysHelper::get_company_access();
            $brands = SysBrand::where('active_status', 1)->wherein('company_id', $com_ids)->get();
            $data = SysBrandCart::where('company_id', session('logged_session_data.company_id'))->where('status', 1)->get();
            return view('backEnd.humanResource.importbrand', compact('brands', 'data'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function importList(Request $request)
    {
        try {
            $companyId = session('logged_session_data.company_id');
            SysBrandCart::where('company_id', $companyId)->delete();

            $file = $request->file('import_file');
            $ext = $file->getClientOriginalExtension();
            $fileName = md5($file->getClientOriginalName() . time()) . "." . $ext;
            $path = 'public/uploads/brand_upload/';
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
            $file->move($path, $fileName);
            $selected_file = $path . $fileName;

            $objPHPExcel = PHPExcel_IOFactory::load($selected_file);
            $dataArray = $objPHPExcel->getActiveSheet()->toArray();
            $data = [];
            if (count($dataArray) > 1) {
                $header = $dataArray[0];
                for ($i = 1; $i < count($dataArray); $i++) {
                    $row = $dataArray[$i];
                    if (isset($row[0]) && trim($row[0]) != '') {
                        $data[] = [
                            'title' => trim($row[0]),
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
                SysBrandCart::insert($data);
            }

            Toastr::success('Brand file parsed successfully', 'Success');
            return redirect('brand-import');
        } catch (\Exception $e) {
            dd($e);
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function importClear(Request $request)
    {
        SysBrandCart::where('company_id', session('logged_session_data.company_id'))->delete();
        Toastr::success('Brand upload data cleared', 'Success');
        return redirect('brand-import');
    }

    public function importData(Request $request)
    {
        try {
            $companyId = session('logged_session_data.company_id');
            $data = SysBrandCart::where('company_id', $companyId)->where('status', 1)->get();
            if ($data->isEmpty()) {
                Toastr::error('No data to import', 'Failed');
                return redirect('brand-import');
            }

            $existing = SysBrand::where('company_id', $companyId)->pluck('title')->toArray();

            $insert = [];
            foreach ($data as $row) {
                $title = trim($row->title);
                if ($title == '') {
                    continue;
                }
                if (!in_array($title, $existing)) {
                    $insert[] = [
                        'title' => $title,
                        'active_status' => 1,
                        'created_by' => Auth::user()->id,
                        'company_id' => $companyId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if (!empty($insert)) {
                SysBrand::insert($insert);
            }

            SysBrandCart::where('company_id', $companyId)->delete();

            Toastr::success('Brand Imported successfully', 'Success');
            return redirect('brand-import');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'title' => "required"
        ]);

        if($validator->fails()){
            if(ApiBaseMethod::checkUrl($request->fullUrl())){
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        try{
            $brands = new SysBrand();
            $brands->title = $request->title;
            $brands->created_by = Auth::user()->id;
            $brands->company_id = session('logged_session_data.company_id');
            $result = $brands->save();
            if ($request->ajax()) {
                if ($result) {
                    return response()->json(['success' => true, 'id' => $brands->id, 'title' => $brands->title]);
                } else {
                    return response()->json(['success' => false, 'message' => 'Something went wrong, please try again.']);
                }
            }

            if(ApiBaseMethod::checkUrl($request->fullUrl())){
                if($result){
                    Toastr::success('Operation successful', 'Success');
                    return redirect()->back();
                }else{
                    return ApiBaseMethod::sendError('Something went wrong, please try again.');
                }
            }else{
                if($result){
                    Toastr::success('Operation successful', 'Success');
                    return redirect()->back();
                }else{
                    Toastr::error('Operation Failed', 'Failed');
                    return redirect()->back();
                }
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function show(Request $request, $id)
    {
        try{
            $com_ids = SysHelper::get_company_access();
            $editmode = SysBrand::find($id);
            $brands = SysBrand::where('active_status', 1)->wherein('company_id',$com_ids)->get();

            if(ApiBaseMethod::checkUrl($request->fullUrl())){
                $data=[];
                $data['brands']= $brands->toArray();
                $data['editmode']= $editmode->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.humanResource.brands', compact('brands', 'editmode'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'title' => "required"
        ]);

        if($validator->fails()){
            if(ApiBaseMethod::checkUrl($request->fullUrl())){
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        try{
            $brands = SysBrand::find($request->id);
            $brands->title = $request->title;
            $brands->updated_by = Auth::user()->id;
            $brands->company_id = session('logged_session_data.company_id');
            $result = $brands->save();
            if(ApiBaseMethod::checkUrl($request->fullUrl())){
                if($result){
                    return ApiBaseMethod::sendResponse(null, 'Brand has been updated successfully');
                }else{
                    return ApiBaseMethod::sendError('Something went wrong, please try again.');
                }
            }else{
                if($result){
                    Toastr::success('Operation successful', 'Success');
                    return redirect('brand');
                }else{
                    Toastr::error('Operation Failed', 'Failed');
                    return redirect()->back();
                }
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function destroy(Request $request,$id)
    {
        try{
            $brands = SysBrand::destroy($id);
            if(ApiBaseMethod::checkUrl($request->fullUrl())){
                if($brands){
                    return ApiBaseMethod::sendResponse(null, 'Brand has been deleted successfully');
                }else{
                    return ApiBaseMethod::sendError('Something went wrong, please try again.');
                }
            }else{
                if($brands){
                    Toastr::success('Operation successful', 'Success');
                    return redirect('brand');
                }else{
                    Toastr::error('Operation Failed', 'Failed');
                    return redirect()->back();
                }
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function deleteBrand($id)
    {
        try{
            $brands = SysBrand::destroy($id);
            if($brands){
                Toastr::success('Operation successful', 'Success');
            }else{
                Toastr::error('Operation Failed', 'Failed');
            }
            return redirect('brand');
        }catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
}

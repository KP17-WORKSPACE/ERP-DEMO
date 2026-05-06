<?php

namespace App\Http\Controllers;

use App\SmUnitManage;
use App\SmBrandManage;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;

class SmUnitManageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        try{
            $data = SmUnitManage::all();
            return view('backEnd.systemSettings.unit_manage', compact('data'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function manageUnitModified(Request $request)
    {
        $request->validate([
           'name' => "required",
           'short_form' => "required",
        ]);
        try{
            if(isset($request->unit_id) && $request->unit_id != ""){
                $s = SmUnitManage::find($request->unit_id);
                $s->updated_by = Auth::user()->id;
            }else{
                $s = new SmUnitManage();
                $s->created_by = Auth::user()->id;
            }
            $s->name    =   $request->name;
            $s->short_form    =   $request->short_form;
            $s->description    =  $request->description ;
            $s->active_status    =  $request->active_status ;
            $isStore= $s->save();
            if($isStore){
                Toastr::success('Operation successful', 'Success');
                return redirect('manage-unit');
            }else{
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            } 
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function manageUnitEdit(SmUnitManage $smUnitManage, $id)
    {
        try{
            $data = SmUnitManage::all();
            $unit_manage = SmUnitManage::find($id);
            return view('backEnd.systemSettings.unit_manage', compact('data','unit_manage'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function unit_destroy($id){
        
        try{
            $singlaData = SmUnitManage::find($id)->delete();
            if($singlaData){
                Toastr::success('Operation successful', 'Success');
                return redirect('manage-unit');
            }else{
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
        //
    }
    public function brand_destroy( $id){
        
        try{
            $singlaData = SmBrandManage::find($id)->delete();
            if($singlaData){
                Toastr::success('Operation successful', 'Success');
                return redirect('manage-brand');
            }else{
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
        //
    }

    public function manageBrandModified(Request $request){
        $request->validate([           
            'name' => "required",
           'parent_id' => "required",
        ]);
        
        try{
            if(isset($request->unit_id) && $request->unit_id != ""){
                $s = SmBrandManage::find($request->unit_id);
                $s->updated_by = Auth::user()->id;
            }else{
                $s = new SmBrandManage();
                $s->created_by = Auth::user()->id;
            }
    
            $s->name            =   $request->name;
            $s->parent_id       =   $request->parent_id;
            $s->description     =   $request->description ;
            $s->active_status   =   $request->active_status ;
            $isStore            =   $s->save();
    
            if($isStore){
                Toastr::success('Operation successful', 'Success');
                return redirect('manage-brand');
            }else{
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        } 
    }

    public function ManageBrand()
    {
        try{
            $data = SmBrandManage::all();
            return view('backEnd.systemSettings.brand_manage', compact('data'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function manageBrandEdit(SmUnitManage $smUnitManage, $id)
    {
        
        try{
            $data = SmBrandManage::all();
            $brand_manage = SmBrandManage::find($id);
            return view('backEnd.systemSettings.brand_manage', compact('data','brand_manage'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }

    }


}

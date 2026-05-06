<?php

namespace App\Http\Controllers;

use App\SmBaseGroup;
use App\SmBaseSetup;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;


class SmBaseSetupController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
    }
    
    public function index(){
    	
		try{
			$base_groups = SmBaseGroup::where('active_status', '=', 1)->get();
			return view('backEnd.systemSettings.baseSetup.base_setup', compact('base_groups'));
		}catch (\Exception $e) {
		   Toastr::error('Operation Failed', 'Failed');
		   return redirect()->back(); 
		}
    }
    public function store(Request $request){
    	$request->validate([
    		'name' => "required",
    		'base_group' => "required"
    	]);
    	
		try{
			$base_setup = new SmBaseSetup();
			$base_setup->base_setup_name = $request->name;
			$base_setup->base_group_id = $request->base_group;
			$result = $base_setup->save();
			if($result){
				Toastr::success('Operation successful', 'Success');
				return redirect()->back();
			}else{
				return redirect()->back()->with('message-danger', 'Something went wrong, please try again');
			} 
		}catch (\Exception $e) {
		   Toastr::error('Operation Failed', 'Failed');
		   return redirect()->back(); 
		}
    }
    public function edit($id){
    	
		 try{
			$base_setup = SmBaseSetup::find($id);
			$base_groups = SmBaseGroup::where('active_status', '=', 1)->get();
			return view('backEnd.systemSettings.baseSetup.base_setup', compact('base_setup', 'base_groups'));
		}catch (\Exception $e) {
		   Toastr::error('Operation Failed', 'Failed');
		   return redirect()->back(); 
		}
    }
    
    public function update(Request $request){
    	$request->validate([
    		'name' => "required",
    		'base_group' => "required"
    	]);
    	
		try{
			$base_group = SmBaseSetup::find($request->id);
			$base_group->base_setup_name = $request->name;
			$base_group->base_group_id = $request->base_group;
			$result = $base_group->save();
			if($result){
				Toastr::success('Operation successful', 'Success');
				return redirect('base-setup');
			}else{
				Toastr::error('Operation Failed', 'Failed');
				return redirect()->back();
			} 
		}catch (\Exception $e) {
		   Toastr::error('Operation Failed', 'Failed');
		   return redirect()->back(); 
		}
    }
    public function delete(Request $request){
    	
		try{
			$base_setup = SmBaseSetup::destroy($request->id);
			if($base_setup){
				Toastr::success('Operation successful', 'Success');
				return redirect('base-setup');
			}else{
				Toastr::error('Operation Failed', 'Failed');
				return redirect()->back();
			}
		}catch (\Exception $e) {
		   Toastr::error('Operation Failed', 'Failed');
		   return redirect()->back(); 
		}
    }
}

<?php

namespace App\Http\Controllers;

use App\Role;
use App\SmModule;
use App\ApiBaseMethod;
use App\SmRolePermission;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;

class SmRolePermissionController extends Controller
{
    public function __construct(){
        $this->middleware('PM');
    }
    
    public function assignPermission(Request $request,$id)
	{
		try{
			$role = Role::find($id);
			$modules = SmModule::where('active_status', 1)->orderby('id','asc')->get();
			
			$role_permissions = SmRolePermission::where('role_id', $id)->orderby('id','asc')->get();

			$already_assigned	= [];
			$is_create 			= [];
			$is_read 			= [];
			$is_edit 			= [];
			$is_delete 			= [];
			$is_print 			= [];
			$is_copy 			= [];
			$is_recreate 		= [];
			$is_saveprint 		= [];
			$is_revice 			= [];
			$is_export 			= [];
			$is_editprinted 	= [];
			$is_attach 			= [];

			foreach($role_permissions as $role_permission){
				//$already_assigned[] = $role_permission->module_link_id;
				$is_create[] = $role_permission->is_create;
				$is_read[] = $role_permission->is_read;
				$is_edit[] = $role_permission->is_edit;
				$is_delete[] = $role_permission->is_delete;
				$is_print[] = $role_permission->is_print;
				$is_copy[] = $role_permission->is_copy;
				$is_recreate[] = $role_permission->is_recreate;
				$is_saveprint[] = $role_permission->is_saveprint;
				$is_revice[] = $role_permission->is_revice;
				$is_export[] = $role_permission->is_export;
				$is_editprinted[] = $role_permission->is_editprinted;
				$is_attach[] = $role_permission->is_attach;
			}

			// if (ApiBaseMethod::checkUrl($request->fullUrl())) {
			// 	$data['role'] = $role;
			// 	$data['modules'] = $modules->toArray();
			// 	$data['already_assigned'] = $already_assigned;
			// 	return ApiBaseMethod::sendResponse($data, null);
			// }

			return view('backEnd.systemSettings.role.assign_role_permission', compact('role', 'modules', 'already_assigned','is_create','is_read','is_edit','is_delete','is_print','is_copy','is_recreate','is_saveprint','is_revice','is_export','is_editprinted','is_attach'));
		}catch (\Exception $e) {
			return $e;
		   Toastr::error('Operation Failed', 'Failed');
		   return redirect()->back(); 
		}
    }
    public function rolePermissionStore(Request $request)
	{
		//return $request->all();
		try {
		$permissions_create = $request['permissions_create'];
		foreach($permissions_create AS $dat) { if($dat === '1'){ if(end($create)==0){ array_pop($create); } $create[]=1; } else{ $create[]=0; } }
		
		$permissions_read = $request['permissions_read'];
		foreach($permissions_read AS $dat) { if($dat === '1'){ if(end($read)==0){ array_pop($read); } $read[]=1; } else{ $read[]=0; } }

		$permissions_edit = $request['permissions_edit'];
		foreach($permissions_edit AS $dat) { if($dat === '1'){ if(end($edit)==0){ array_pop($edit); } $edit[]=1; } else{ $edit[]=0; } }

		$permissions_delete = $request['permissions_delete'];
		foreach($permissions_delete AS $dat) { if($dat === '1'){ if(end($delete)==0){ array_pop($delete); } $delete[]=1; } else{ $delete[]=0; } }

		$permissions_print = $request['permissions_print'];
		foreach($permissions_print AS $dat) { if($dat === '1'){ if(end($print)==0){ array_pop($print); } $print[]=1; } else{ $print[]=0; } }

		$permissions_copy = $request['permissions_copy'];
		foreach($permissions_copy AS $dat) { if($dat === '1'){ if(end($copy)==0){ array_pop($copy); } $copy[]=1; } else{ $copy[]=0; } }

		$permissions_recreate = $request['permissions_recreate'];
		foreach($permissions_recreate AS $dat) { if($dat === '1'){ if(end($recreate)==0){ array_pop($recreate); } $recreate[]=1; } else{ $recreate[]=0; } }

		$permissions_saveprint = $request['permissions_saveprint'];
		foreach($permissions_saveprint AS $dat) { if($dat === '1'){ if(end($saveprint)==0){ array_pop($saveprint); } $saveprint[]=1; } else{ $saveprint[]=0; } }

		$permissions_revice = $request['permissions_revice'];
		foreach($permissions_revice AS $dat) { if($dat === '1'){ if(end($revice)==0){ array_pop($revice); } $revice[]=1; } else{ $revice[]=0; } }

		$permissions_export = $request['permissions_export'];
		foreach($permissions_export AS $dat) { if($dat === '1'){ if(end($export)==0){ array_pop($export); } $export[]=1; } else{ $export[]=0; } }

		$permissions_editprinted = $request['permissions_editprinted'];
		foreach($permissions_editprinted AS $dat) { if($dat === '1'){ if(end($editprinted)==0){ array_pop($editprinted); } $editprinted[]=1; } else{ $editprinted[]=0; } }

		$permissions_attach = $request['permissions_attach'];
		foreach($permissions_attach AS $dat) { if($dat === '1'){ if(end($attach)==0){ array_pop($attach); } $attach[]=1; } else{ $attach[]=0; } }
		 
		} catch (\Throwable $th) {
			return $th;
		}


		//return $create;
		//return $request->permissions;
		//return $request->role_id;
		// $chk=0;
		// for ($i=0; $i < count($request->permissions); $i++) {

		// 	$dcomsub[]=$create[$i];
		// 	$dcomsub[]=$read[$i];

		// 	if($chk != $request->permissions) {
		// 		$chk = $request->permissions;
		// 		$dcom[]=$request->permissions[$i];
				
		// 		$dcom1['ss']=$dcomsub;
		// 	}
		// 	else {
		// 		$chk = $request->permissions;
		// 	}
		// }
 
		try{
			SmRolePermission::where('role_id', $request->role_id)->delete();
			if(isset($request->permissions)){
				for ($i=0; $i < count($request->permissions); $i++) {
					$role_permission = new SmRolePermission();
				 	$role_permission->role_id = $request->role_id;
				 	$role_permission->module_link_id = $request->permissions[$i];
				 	$role_permission->is_create = $create[$i];
				 	$role_permission->is_read = $read[$i];
				 	$role_permission->is_edit = $edit[$i];
				 	$role_permission->is_delete = $delete[$i];
				 	$role_permission->is_print = $print[$i];
				 	$role_permission->is_copy = $copy[$i];
				 	$role_permission->is_recreate = $recreate[$i];
				 	$role_permission->is_saveprint = $saveprint[$i];
				 	$role_permission->is_revice = $revice[$i];
				 	$role_permission->is_export = $export[$i];
				 	$role_permission->is_editprinted = $editprinted[$i];
				 	$role_permission->is_attach = $attach[$i];
				 	$role_permission->save();
				}
				// foreach($request->permissions as $permission){
				// 	$role_permission = new SmRolePermission();
				// 	$role_permission->role_id = $request->role_id;
				// 	$role_permission->module_link_id = $permission;
				// 	$role_permission->save();
				// }
			}
			if (ApiBaseMethod::checkUrl($request->fullUrl())) {
				return ApiBaseMethod::sendResponse(null, 'Role permission has been assigned successfully');
			}
			Toastr::success('Operation successful', 'Success');
			return redirect('role');
		}catch (\Exception $e) {
			return $e;
		   Toastr::error('Operation Failed', 'Failed');
		   return redirect()->back(); 
		}
	}
	
}

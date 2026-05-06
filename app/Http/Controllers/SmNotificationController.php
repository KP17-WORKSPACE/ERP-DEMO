<?php

namespace App\Http\Controllers;

use App\SmNotification;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;

class SmNotificationController extends Controller
{
    public function __construct(){
        $this->middleware('PM');
    }
    
    public function viewSingleNotification($id){
    	
		try{
			$notification = SmNotification::find($id);
			$notification->is_read = 1;
			$notification->save();
			return redirect()->back();
		}catch (\Exception $e) {
		   Toastr::error('Operation Failed', 'Failed');
		   return redirect()->back(); 
		}
    }

    public function viewAllNotification($id){
		
		try{
			$d=SmNotification::where('received_id',Auth::user()->id)->where('is_read',0)->get();
			foreach ($d as $key => $value) {
				$value->is_read=1;
				$value->save();
			}
			// $user = Auth()->user();
			// if(Auth()->user()->role_id != 1){
			// 	if($user->role_id == 2){
			// 		SmNotification::where('user_id', $user->student->id)->where('role_id', 2)->update(['is_read' => 1]);
			// 	}else{
			// 		SmNotification::where('user_id', $user->staff->id)->where('role_id', '!=', 2)->update(['is_read' => 1]);
			// 	}
			// }
			
			return redirect()->back();
		}catch (\Exception $e) {
		   Toastr::error('Operation Failed', 'Failed');
		   return redirect()->back(); 
		}
    }

    public function viewNotice($id){

	}
	
	function ticket_show(Request $r){
		$status=SmNotification::find($r->id);
		$d=SmNotification::where('ticket_id',$status->ticket_id)->where('received_id',$status->received_id)->get();
		foreach ($d as $key => $value) {
			$value->is_read=1;
            $value->save();
		}
        
        if (Auth::user()->role_id == 1) {
		if ($status->role_id == 7) {
		   $link=route('user.ticket_view',$status->ticket_id);
		}
		else {
			$link=route('admin.ticket_view',$status->ticket_id);
		}
            $data=new SmNotification();
            $data->user_id = Auth::user()->id;
            $data->role_id = Auth::user()->role_id;
            $data->message = 'View your noticifation';
            $data->link = $link;
            $data->received_id = $status->user_id;
            $data->ticket_id = $status->ticket_id;
            $data->save();
        }

        return response()->json(['success'=>'success']);
    }
}

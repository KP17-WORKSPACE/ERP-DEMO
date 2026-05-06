<?php

namespace App\Http\Controllers;

use App\SysHelper;
use App\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class SmAuthController extends Controller
{
    public function getLoginAccess(Request $request){
    	if($request->value == "Student"){
    		$user = User::where('role_id', 2)->first();
    	}elseif($request->value == "Parents"){
    		$user = User::where('role_id', 3)->first();
    	}elseif($request->value == "Super Admin"){
    		$user = User::where('role_id', 1)->first();
    	}elseif($request->value == "Admin"){
    		$user = User::where('role_id', 5)->first();
    	}elseif($request->value == "Teacher"){
    		$user = User::where('role_id', 4)->first();
    	}elseif($request->value == "Accountant"){
    		$user = User::where('role_id', 6)->first();
    	}elseif($request->value == "Receptionist"){
    		$user = User::where('role_id', 7)->first();
    	}elseif($request->value == "Librarian"){
    		$user = User::where('role_id', 8)->first();
    	}
        return response()->json($user);
    }

    public function recoveryPassord(){
		
		try{
			return view('auth.recovery_password');
		}catch (\Exception $e) {
		   Toastr::error('Operation Failed', 'Failed');
		   return redirect()->back(); 
		}
    }

    public function emailVerify(Request $request)
	{
    	$request->validate([
    		'email' => 'required'
    	]);
    	
		try{
			$emailCheck = User::select('*')->where('email', $request->email)->first();
			

			if($emailCheck == ""){
				return redirect()->back()->with('message-danger', "Invalid Email, Please try again");
			}else{
				$pwd = Str::random(10);
				$pwdHash = Hash::make($pwd);

				$user = User::where('email', $request->email)->first();
				$user->random_code = $pwdHash;
				$user->password = $pwdHash;
				$user->save();

				$body = "<br />Your account password has been changed to : <b>".$pwd."</b><br />You may log in using your email address and password in the following URL: <a href='https://crm.venushrms.com/login' target='_blank'>https://crm.venushrms.com</a>";

				dd($body);
				try {
					SysHelper::notificationMail($user->full_name,$body,$request->email,'Reset Password');
				} catch (\Throwable $th) {
					return $th;
				}

				// Mail::send('auth.confirmation_reset', compact('data'), function($message) use($request) {
				// 	$message->to($request->email, 'Tutorials Point')->subject
				// 		('Reset Password');
				// 	 $message->from('spn5@spondonit.com','Spondon IT');
				//   });
				return redirect('login')->with('message-success', 'Success ! Please check your email');
				return redirect()->back()->with('message-success', 'Success ! Please check your email');
			}
		}catch (\Exception $e) {
			return $e;
		   Toastr::error('Operation Failed', 'Failed');
		   return redirect()->back(); 
		}
    }

    public function resetEmailConfirtmation($email, $code){
    	
		try{
			$user = User::where('email', $email)->where('random_code', $code)->first();
			if($user != ""){
				$email = $user->email;
				return view('auth.new_password', compact('email'));
			}else{
				Toastr::error('You have clicked on a invalid link, please try again', 'Failed');
				return redirect('recovery/passord');
			}
		}catch (\Exception $e) {
		   Toastr::error('Operation Failed', 'Failed');
		   return redirect()->back(); 
		}
    }

    public function storeNewPassword(Request $request){
    	$request->validate([
    		'new_password' => 'required|same:confirm_password',
    		'confirm_password' => 'required'
    	]);
		try{
			$user = User::where('email', $request->email)->first();
			$user->password = Hash::make($request->new_password);
			$user->random_code = '';
			$result = $user->save();
			if($result){
				Toastr::success('Password has beed reset successfully', 'Success');
				return redirect('login');	
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

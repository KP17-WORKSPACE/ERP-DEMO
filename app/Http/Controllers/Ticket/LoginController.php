<?php

namespace App\Http\Controllers\Ticket;

use App\SmGeneralSettings; 
use App\SmStaffAttendence; 
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/after-login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function loginFormTwo(){
        try{
            if (Schema::hasTable('users')) {
                return view('auth.login_two');
            }else{
                return redirect('install');
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    function ticket_login(){
        
        try{
            $systemInformation = SmGeneralSettings::find(1);
            if($systemInformation == ""){
                return redirect('install');
            }
            return view('auth.ticket_login', compact('systemInformation'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    //user logout method
    public function logout(Request $request) {
        
        try{
            //attendance generate 
            $attendance = SmStaffAttendence::where([['attendence_date',date('Y-m-d')],['staff_id',Auth::user()->id]])->first();
            if($attendance != ""){
                $staff_attendance = SmStaffAttendence::find($attendance->id);
                $staff_attendance->notes =$staff_attendance->notes. ' & Office Out Time From Logout at '. date('H:i A');
                $staff_attendance->attendence_date = date('Y-m-d');
                $staff_attendance->out_time = date('H:i A');
                $staff_attendance->updated_by = Auth::user()->id;
                $staff_attendance->save();
            }  
            $request->session()->flush();
            /* Auth::logout();
            session_destroy();
            session(['role_id' => '']); */
            return redirect('ticket/login'); 
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }


}

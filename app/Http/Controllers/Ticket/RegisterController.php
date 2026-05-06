<?php

namespace App\Http\Controllers\Ticket;

use App\User;
use App\SmStaff;
use App\SmGeneralSettings; 
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }
    function ticket_register(){
        
        try{
            $systemInformation = SmGeneralSettings::find(1);
            if($systemInformation == ""){
                return redirect('install');
            }
            return view('auth.ticket_register', compact('systemInformation'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }


    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        try{
            $user=new User();
            $user->role_id =7;
            $user->full_name = $data['full_name'];
            $user->username = $data['username'];
            $user->email =$data['email'];
            $user->password = Hash::make($data['password']);
            $user->save();
    
            $staff=new SmStaff;
            $staff->user_id=$user->id;
            $staff->role_id=$user->role_id;
            $staff->email=$user->email;
            $staff->full_name=$user->full_name;
            $staff->first_name=$user->full_name;
            $staff->save();
            return $user;
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    protected function registered(Request $request, $user)
    {
        try{
            $request->session()->flush();
            Toastr::success('Registration successfull! You can login now', 'Success');
            return redirect()->route('ticket.ticket_login');
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
}

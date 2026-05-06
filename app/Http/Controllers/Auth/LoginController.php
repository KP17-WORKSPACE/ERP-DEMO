<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\SmUserLog;
use App\SmStaff;
use Soumen\Agent\Agent;
use App\SmGeneralSettings;
use App\SmStaffAttendence;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;
use Illuminate\Auth\Events\Lockout;
use App\Http\Controllers\Controller;
use App\Role;
use App\SmDesignation;
use App\SmHumanDepartment;
use App\SysAccountType;
use App\SysChartofAccounts;
use App\SysCompany;
use App\SysCountries;
use App\SysCustomerType;
use App\SysCustSupplAddressbook;
use App\SysCustSupplAddressbookCart;
use App\SysCustSupplAddressbookForm;
use App\SysCustSupplContact;
use App\SysCustSupplContactForm;
use App\SysCustSupplForm;
use App\SysHelper;
use App\SysPaymentTerms;
use App\SysPurchaseType;
use App\SysSaleType;
use App\SysStates;
use App\SysSupplierType;
use App\SysVat;
use App\SysVatType;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\DB;

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

    // public function loginFormTwo(){
    //     if (\Schema::hasTable('users')) {
    //         return view('auth.login_two');
    //     }else{
    //         return redirect('install');
    //     }
    // }
    function ticket_login()
    {
        $systemInformation = SmGeneralSettings::find(1);
        if ($systemInformation == "") {
            return redirect('install');
        }
        return view('auth.ticket_login', compact('systemInformation'));
    }

    //user logout method
    public function logout(Request $request)
    {
        //attendance generate 
        $attendance = SmStaffAttendence::where([['attendence_date', date('Y-m-d')], ['staff_id', Auth::user()->id]])->first();
        if ($attendance != "") {
            $staff_attendance = SmStaffAttendence::find($attendance->id);
            $staff_attendance->notes = $staff_attendance->notes . ' & Office Out Time From Logout at ' . date('H:i A');
            $staff_attendance->attendence_date = date('Y-m-d');
            $staff_attendance->out_time = date('H:i');
            $staff_attendance->updated_by = Auth::user()->id;
            // return $staff_attendance;
            $staff_attendance->save();
        }
        $request->session()->flush();


        /* Auth::logout();

        session_destroy();
        session(['role_id' => '']); */

        return redirect('/login');
    }


    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        if (Schema::hasTable('sm_general_settings')) {

            // $systemInformation = SmGeneralSettings::find(1);
            // if ($systemInformation == "") {
            //     return redirect('install');
            // }
            return view('auth.login_live');
        } else {
            return redirect('install');
        }
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {

        //return $request->all();
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {

            if (Auth::user()->access_status != 0) {

                $getuserlogdata=SmStaff::where('user_id',Auth::user()->id)->first();
                $co_access = explode(",",$getuserlogdata->company_access);


                session(['role_id' => Auth::user()->role_id]);
                
                if($getuserlogdata != ''){
                    $user_data =
                    [
                        'company_id' => $co_access[0],
                        'decimal_point' => DB::table('sys_company')->where('id',$co_access[0])->value('decimal_point'),
                        'company_access' => $getuserlogdata['company_access'],
                        'full_name' => $getuserlogdata['full_name'],
                        'email' => $getuserlogdata['email'],
                        'mobile' => $getuserlogdata['mobile'],
                        'staffid' => $getuserlogdata['staff_no'],
                        'cart_id' => Auth::user()->id.'_'.date('YmdHis'),
                        'designation_id' => $getuserlogdata['designation_id'],
                        'designation_name' => @$getuserlogdata->designations['title'],
                        'staff_photo' => @$getuserlogdata['staff_photo'],
                        'department_id' => $getuserlogdata['department_id'],
                        'department_name' => @$getuserlogdata->departments['name'],
                        'role_name' => @$getuserlogdata->roles['name'],
                        'joining_date' => $getuserlogdata['date_of_joining'],
                        'auth_status' => $getuserlogdata['auth_status'],
                        'company_name' => $getuserlogdata->maincompany['company_name'],
                    ];
                    session()->put('logged_session_data', $user_data);

                    if (Carbon::parse($getuserlogdata['auth_date'])->diffInDays(Carbon::now()) > 30 || $getuserlogdata['auth_date'] == null) {
                        $authCode = Str::random(6);
                        DB::table('sm_staffs')->where('user_id',Auth::user()->id)->update(
                            [
                                'auth_code' => $authCode,
                                'auth_date' => Carbon::now('+04:00'),
                                'auth_status' => 0,
                            ]
                        );
                    }

                    DB::table('user_log')->insert(
                        [
                            'user_id' => Auth::user()->id,
                            'full_name' => $getuserlogdata['full_name'],
                            'latitude' => $request->latitude,
                            'longitude' => $request->longitude,
                            'location' => $request->location,
                            'ipaddress' => Agent::ip(),
                            'browser' => Agent::browser()->name . ', ' . Agent::platform()->name,
                            'log_date' => Carbon::now('+04:00'),
                        ]
                    );
                }

                // User Log
                $user_log = new SmUserLog();
                $user_log->user_id = Auth::user()->id;
                $user_log->role_id = Auth::user()->role_id;
                $user_log->ip_address = Agent::ip();
                $user_log->user_agent = Agent::browser()->name . ', ' . Agent::platform()->name;
                $user_log->save();

                //attendance generate 
                $attendance = SmStaffAttendence::where([['attendence_date', date('Y-m-d')], ['staff_id', Auth::user()->id]])->first();
                if ($attendance == "") {
                    $staff_attendance = new SmStaffAttendence();
                    $staff_attendance->staff_id = Auth::user()->id;
                    $staff_attendance->finger_print_id = DB::table('sm_staffs')->where('user_id',Auth::user()->id)->value('finger_print_id');
                    $staff_attendance->attendence_type = 'P';
                    $staff_attendance->notes = 'Office In Time From Login at ' . date('H:i A');
                    $staff_attendance->attendence_date = date('Y-m-d');
                    $staff_attendance->in_time = date('H:i');
                    $staff_attendance->created_by = Auth::user()->id;
                    $staff_attendance->save();
                }
            } else {

                $this->guard()->logout();

                return redirect('login')->with('message-danger', 'You are not allowed, Please contact with administrator.');
            }

            return redirect('crm-dashboard');


            // User Log
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        return $this->guard()->attempt(
            $this->credentials($request),
            $request->filled('remember')
        );
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'password');
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        return $this->authenticated($request, $this->guard()->user())
            ?: redirect()->intended($this->redirectPath());
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        //
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $field = $this->username();
        $user = User::where($field, $request->{$field})->first();
        $message = $user ? 'Password is incorrect' : 'Username is incorrect';

        throw ValidationException::withMessages([
            $field => [$message],
        ]);
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'email';
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    /**
     * The user has logged out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    protected function loggedOut(Request $request)
    {
        //
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }

    public function redirectPath()
    {
        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo();
        }

        return property_exists($this, 'redirectTo') ? $this->redirectTo : '/home';
    }


    /**
     * Determine if the user has too many failed login attempts.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function hasTooManyLoginAttempts(Request $request)
    {
        return $this->limiter()->tooManyAttempts(
            $this->throttleKey($request),
            $this->maxAttempts()
        );
    }

    /**
     * Increment the login attempts for the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function incrementLoginAttempts(Request $request)
    {
        $this->limiter()->hit(
            $this->throttleKey($request),
            $this->decayMinutes()
        );
    }

    /**
     * Redirect the user after determining they are locked out.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendLockoutResponse(Request $request)
    {
        $seconds = $this->limiter()->availableIn(
            $this->throttleKey($request)
        );

        throw ValidationException::withMessages([
            $this->username() => [Lang::get('auth.throttle', ['seconds' => $seconds])],
        ])->status(429);
    }

    /**
     * Clear the login locks for the given user credentials.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function clearLoginAttempts(Request $request)
    {
        $this->limiter()->clear($this->throttleKey($request));
    }

    /**
     * Fire an event when a lockout occurs.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function fireLockoutEvent(Request $request)
    {
        event(new Lockout($request));
    }

    /**
     * Get the throttle key for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function throttleKey(Request $request)
    {
        return Str::lower($request->input($this->username())) . '|' . $request->ip();
    }

    /**
     * Get the rate limiter instance.
     *
     * @return \Illuminate\Cache\RateLimiter
     */
    protected function limiter()
    {
        return app(RateLimiter::class);
    }

    /**
     * Get the maximum number of attempts to allow.
     *
     * @return int
     */
    public function maxAttempts()
    {
        return property_exists($this, 'maxAttempts') ? $this->maxAttempts : 5;
    }

    /**
     * Get the number of minutes to throttle for.
     *
     * @return int
     */
    public function decayMinutes()
    {
        return property_exists($this, 'decayMinutes') ? $this->decayMinutes : 1;
    }


// CUSTOMER FORM START
    public function customer_form($company_id){
        try{
            $countries = SysCountries::all();
            $vattype = SysVatType::all();
            $vat = SysVat::select('sys_vat.*','sys_countries.name')->join('sys_countries','sys_countries.id','sys_vat.vat_country')->where('company_id',$company_id)->where('status',1)->get();
            $accounts = SysChartofAccounts::where('status',1)->where('company_id',$company_id)->get();
            $accounttype = SysAccountType::all();
            $roles = Role::where('active_status', '=', '1')->where('id',2)->get();
            $paymentterms = SysPaymentTerms::where('active_status', '=', '1')->get();

            $customer_type = SysCustomerType::where('status', '=', '1')->get();
            $sale_type = SysSaleType::where('status', '=', '1')->get();
            //$staffs = SmStaff::select('id','full_name')->where('active_status', '=', '1')->whereIn('designation_id', array(9,1,2,3))->get();
            $staffs = SmStaff::select('user_id','full_name')->where('active_status', '=', '1')->get();

            $designation = SmDesignation::select('id','title')->where('active_status',1)->orderby('title','asc')->get();
            $department = SmHumanDepartment::select('id','name')->where('active_status',1)->orderby('name','asc')->get();

            $company = SysCompany::where('id',$company_id)->first();
            
            $address_cart = SysCustSupplAddressbookCart::select('sys_cust_suppl_addressbook_cart.*','sys_countries.name as c_name','sys_states.name as s_name')
            ->join('sys_countries','sys_countries.id','sys_cust_suppl_addressbook_cart.country')
            ->join('sys_states','sys_states.id','sys_cust_suppl_addressbook_cart.state')->get();

            return view('auth.customer_form', compact('roles', 'paymentterms','staffs','accounts','accounttype','countries','vattype','customer_type','sale_type','vat','address_cart','company_id','designation','department','company'));
            
        }catch (\Exception $e) {
            return $e;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function customer_form_store(Request $request){
        $input = $request->all();                
        try {
            DB::beginTransaction();        
            $new_customer = new SysCustSupplForm();
            $new_customer->group=SysHelper::get_customer_group('group');
            $new_customer->catid=1;  // 1 customers, 2 suppliers
            $new_customer->account_type = $request->account_type;
            $new_customer->customer_salutation = $request->customer_salutation;
            $new_customer->first_name = $request->first_name;
            $new_customer->designation = $request->designation;
            $new_customer->last_name = $request->last_name;
            $new_customer->name = $request->customer_name;
            $new_customer->customer_name_display = $request->customer_name_display;
            //$new_customer->code = SysHelper::get_new_customer_code();
            $new_customer->address = $request->address;
            $new_customer->address2 = $request->address2;
            $new_customer->contcat_person = $request->e_first_name[0];
            $new_customer->contcat_number = $request->mobile_code;
            $new_customer->mobile = $request->mobile;
            $new_customer->email = $request->email;
            //$new_customer->sales_person = Auth::user()->id;
            //$new_customer->vat_type = $request->vat_type;
            $new_customer->customer_type = $request->customer_type;
            $new_customer->sale_type = $request->sale_type;
            $new_customer->vat_country = $request->country_vat;
            //$new_customer->vat_state = $request->state_vat;
            $new_customer->city = $request->city;
            $new_customer->zip_code = $request->zip_code;
            $new_customer->vat_percentage = $request->vat_percentage;
            $new_customer->vat_number = $request->vat_number;
            if($request->credit_limit==""){
                $new_customer->credit_limit = 0;
            } else{
                $new_customer->credit_limit = $request->credit_limit;
            }
            if($request->credit_days==""){
                $new_customer->credit_days = 0;
            } else{
                $new_customer->credit_days = $request->credit_days;
            }
            if($request->payment_terms==""){
                $new_customer->payment_terms = 0;
            } else{
                $new_customer->payment_terms = $request->payment_terms;
            }
            if($request->transaction_type==""){
                $new_customer->transaction_type = 0;
            } else{
                $new_customer->transaction_type = $request->transaction_type;
            }
            
            //$new_customer->customer_documents = $customer_documents;
            $new_customer->status = 1;
            if($request->vat_percentage_fixed){
                $new_customer->vat_is_fixed=1;
            }
            $new_customer->created_by = 0;
            //$new_customer->created_at = '';
            $new_customer->type = $request->type;
            $new_customer->company_id = $request->company_id;
            $results1 = $new_customer->save();
            

            $address = new SysCustSupplAddressbookForm();
            $address->cust_suppl_id = $new_customer->id;
            $address->address = $request->address;
            $address->address2 = $request->address2;
            $address->city = $request->city;
            $address->country = $request->country;
            if($request->state == ""){
                $address->state = 0;
            } else{
                $address->state = $request->state;
            }
            $address->zip_code = $request->zip_code;
            $address->set_default = 1;
            $address->company_id = $request->company_id;
            $address->is_shipping=0;
            $address->status = 1;
            $address->created_by = 0;
            $results = $address->save();

            if($request->same_billing_address){
                $address = new SysCustSupplAddressbookForm();
                $address->cust_suppl_id = $new_customer->id;
                $address->address = $request->address;
                $address->address2 = $request->address2;
                $address->city = $request->city;
                $address->country = $request->country;
                if($request->state == ""){
                    $address->state = 0;
                } else{
                    $address->state = $request->state;
                }
                $address->zip_code = $request->zip_code;
                $address->set_default = 1;
                $address->company_id = $request->company_id;
                $address->is_shipping=1;
                $address->status = 1;
                $address->created_by = 0;
                $results = $address->save();
            } else {
                $address = new SysCustSupplAddressbookForm();
                $address->cust_suppl_id = $new_customer->id;
                $address->address = $request->address_ship;
                $address->address2 = $request->address2_ship;
                $address->city = $request->city_ship;
                $address->country = $request->country_ship;
                if($request->state_ship == ""){
                    $address->state = 0;
                } else{
                    $address->state = $request->state_ship;
                }
                $address->zip_code = $request->zip_code_ship;
                $address->set_default = 1;
                $address->company_id = $request->company_id;
                $address->is_shipping=1;
                $address->status = 1;
                $address->created_by = 0;
                $results = $address->save();
            }


            for($i=0; $i < count($request->e_first_name); $i++){
                if($request->e_first_name[$i] != "" && $request->e_email_address[$i] != "" && ($request->e_work_phone[$i] != "" || $request->e_mobile[$i] != "")){
                    $contact = new SysCustSupplContactForm();
                    $contact->cust_suppl_id = $new_customer->id;
                    $contact->salutation = $request->e_salutation[$i];
                    $contact->first_name = $request->e_first_name[$i];
                    $contact->last_name = $request->e_last_name[$i];
                    $contact->email_address = $request->e_email_address[$i];
                    $contact->work_phone = $request->e_work_phone[$i];
                    $contact->mobile = $request->e_mobile[$i];
                    $contact->designation = $request->e_designation[$i];
                    $contact->department = $request->e_department[$i];
                    $contact->status = 1;
                    $contact->set_default = 1;
                    $contact->company_id = $request->company_id;
                    $contact->created_by = 0;
                    $results = $contact->save();
                }
            }

        // for ($i=1; $i <= count($request->doc_name); $i++) {             
        //     if ($request->file('customer_documents_'.$i) != "") { 
        //         $file = $request->file('customer_documents_'.$i);
        //         $company_doc = md5($file->getClientOriginalName() . time()) . "_customer_doc_". $i . "." . $file->getClientOriginalExtension();
        //         $file->move('public/uploads/cust-suppl/', $company_doc);

        //         if($request->doc_exp_date[$i-1]==""){
        //             $doc_exp_date = date('Y-m-d');
        //         } else {$doc_exp_date = $request->doc_exp_date[$i-1];}

        //         DB::table('sys_cust_suppl_doc_form')->insert([
        //             'cust_suppl_id' => $new_customer->id,
        //             'doc_name' => $request->doc_name[$i-1],
        //             'doc_file' => $company_doc,
        //             'doc_exp_date' => $doc_exp_date,
        //             'status' => 1,
        //             'created_by' => 0,
        //         ]);
        //     }
        // }

        for ($i=1; $i <= count($request->doc_name); $i++) {
            if ($request->file('customer_documents_'.$i) != "") {
                $doc_exp_date=date('Y-m-d');
                if($i==1){
                    $doc_exp_date = $request->doc_exp_date[$i-1];
                    DB::table('sys_cust_suppl_form')->where('id',$new_customer->id)->update(['is_file' => 1]);
                }
                if($i==2){
                    DB::table('sys_cust_suppl_form')->where('id',$new_customer->id)->update(['is_file' => 2]);
                }
                $file = $request->file('customer_documents_'.$i);
                $company_doc = md5($file->getClientOriginalName() . time()) . "_customer_doc_". $i . "." . $file->getClientOriginalExtension();
                $file->move('public/uploads/cust-suppl/', $company_doc);
                DB::table('sys_cust_suppl_doc_form')->insert([
                    'cust_suppl_id' => $new_customer->id,
                    'doc_name' => $request->doc_name[$i-1],
                    'doc_file' => $company_doc,
                    'doc_exp_date' => $doc_exp_date,
                    'status' => 1,
                    'created_by' => 0,
                ]);
            }
        }
        
        
            DB::commit();

            if ($results) {
                Toastr::success('Details submitted successfully', 'Success');
                return redirect('customer-from-submited');
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back(); 
            }
            
       }catch (\Exception $e) {
            DB::rollBack();
           return $e;
          Toastr::error('Operation Failed', 'Failed');
          return redirect()->back(); 
       }
    }
    public function customer_from_submited(Request $request){
        try{
            return view('auth.customer_from_submited');
            
        }catch (\Exception $e) {
            return $e;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
// CUSTOMER FORM END

// SUPPLIER FORM START
public function supplier_form($company_id){
    try {
        $countries = SysCountries::all();
        $vattype = SysVatType::all();
        $vat = SysVat::select('sys_vat.*','sys_countries.name')->join('sys_countries','sys_countries.id','sys_vat.vat_country')->where('company_id',$company_id)->where('status',1)->get();
        $accounts = SysChartofAccounts::where('status',1)->get();
        $accounttype = SysAccountType::all();
        $paymentterms = SysPaymentTerms::where('active_status', '=', '1')->get();

        $supplier_type = SysSupplierType::where('status', '=', '1')->get();
        $purchase_type = SysPurchaseType::where('status', '=', '1')->get();
        
        $company = SysCompany::where('id',$company_id)->first();
        
        $designation = SmDesignation::select('id','title')->where('active_status',1)->orderby('title','asc')->get();
        $department = SmHumanDepartment::select('id','name')->where('active_status',1)->orderby('name','asc')->get();
        
        return view('auth.supplier_form', compact('paymentterms','accounts','accounttype','countries','vattype','supplier_type','purchase_type','vat','company_id','designation','department','company'));
        
    } catch (\Throwable $th) {
        return $th;
    }
}
public function supplier_form_store(Request $request){
    $input = $request->all();                
        try {
            DB::beginTransaction();        
            $new_customer = new SysCustSupplForm();
            $new_customer->group=SysHelper::get_supplier_group('group');
            $new_customer->catid=2;  // 1 customers, 2 suppliers
            $new_customer->customer_salutation = $request->customer_salutation;
            $new_customer->first_name = $request->first_name;
            $new_customer->designation = $request->designation;
            $new_customer->last_name = $request->last_name;
            $new_customer->name = $request->customer_name;
            $new_customer->customer_name_display = $request->customer_name_display;
            //$new_customer->code = SysHelper::get_new_customer_code();
            $new_customer->address = $request->address;
            $new_customer->address2 = $request->address2;
            $new_customer->contcat_person = $request->e_first_name[0];
            $new_customer->contcat_number = $request->mobile_code;
            $new_customer->mobile = $request->mobile;
            $new_customer->email = $request->email;
            //$new_customer->sales_person = Auth::user()->id;
            //$new_customer->vat_type = $request->vat_type;
            $new_customer->supplier_type = $request->supplier_type;
            $new_customer->purchase_type = $request->purchase_type;
            $new_customer->vat_country = $request->country_vat;
            //$new_customer->vat_state = $request->state_vat;
            $new_customer->city = $request->city;
            $new_customer->zip_code = $request->zip_code;
            $new_customer->vat_percentage = $request->vat_percentage;
            $new_customer->vat_number = $request->vat_number;
            $new_customer->credit_limit = $request->credit_limit;
            $new_customer->credit_days = $request->credit_days;
            $new_customer->payment_terms = $request->payment_terms;
            $new_customer->transaction_type = $request->transaction_type;
            //$new_customer->customer_documents = $customer_documents;
            $new_customer->status = 1;
            if($request->vat_percentage_fixed){
                $new_customer->vat_is_fixed=1;
            }
            $new_customer->created_by = 0;
            //$new_customer->created_at = '';
            $new_customer->type = $request->type;
            $new_customer->company_id = $request->company_id;
            $results1 = $new_customer->save();
            

            $address = new SysCustSupplAddressbookForm();
            $address->cust_suppl_id = $new_customer->id;
            $address->address = $request->address;
            $address->address2 = $request->address2;
            $address->city = $request->city;
            $address->country = $request->country;
            $address->state = $request->state;
            $address->zip_code = $request->zip_code;
            $address->set_default = 1;
            $address->company_id = $request->company_id;
            $address->is_shipping=0;
            $address->status = 1;
            $address->created_by = 0;
            $results = $address->save();

            if($request->same_billing_address){
                $address = new SysCustSupplAddressbookForm();
                $address->cust_suppl_id = $new_customer->id;
                $address->address = $request->address;
                $address->address2 = $request->address2;
                $address->city = $request->city;
                $address->country = $request->country;
                $address->state = $request->state;
                $address->zip_code = $request->zip_code;
                $address->set_default = 1;
                $address->company_id = $request->company_id;
                $address->is_shipping=1;
                $address->status = 1;
                $address->created_by = 0;
                $results = $address->save();
            } else {
                $address = new SysCustSupplAddressbookForm();
                $address->cust_suppl_id = $new_customer->id;
                $address->address = $request->address_ship;
                $address->address2 = $request->address2_ship;
                $address->city = $request->city_ship;
                $address->country = $request->country_ship;
                $address->state = $request->state_ship;
                $address->zip_code = $request->zip_code_ship;
                $address->set_default = 1;
                $address->company_id = $request->company_id;
                $address->is_shipping=1;
                $address->status = 1;
                $address->created_by = 0;
                $results = $address->save();
            }


            for($i=0; $i < count($request->e_first_name); $i++){
                if($request->e_first_name[$i] != "" && $request->e_email_address[$i] != "" && ($request->e_work_phone[$i] != "" || $request->e_mobile[$i] != "")){
                    $contact = new SysCustSupplContactForm();
                    $contact->cust_suppl_id = $new_customer->id;
                    $contact->salutation = $request->e_salutation[$i];
                    $contact->first_name = $request->e_first_name[$i];
                    $contact->last_name = $request->e_last_name[$i];
                    $contact->email_address = $request->e_email_address[$i];
                    $contact->work_phone = $request->e_work_phone[$i];
                    $contact->mobile = $request->e_mobile[$i];
                    $contact->designation = $request->e_designation[$i];
                    $contact->department = $request->e_department[$i];
                    $contact->status = 1;
                    $contact->set_default = 1;
                    $contact->company_id = $request->company_id;
                    $contact->created_by = 0;
                    $results = $contact->save();
                }
            }

        for ($i=1; $i <= count($request->doc_name); $i++) {             
            if ($request->file('customer_documents_'.$i) != "") { 
                $file = $request->file('customer_documents_'.$i);
                $company_doc = md5($file->getClientOriginalName() . time()) . "_customer_doc_". $i . "." . $file->getClientOriginalExtension();
                $file->move('public/uploads/cust-suppl/', $company_doc);
                DB::table('sys_cust_suppl_doc_form')->insert([
                    'cust_suppl_id' => $new_customer->id,
                    'doc_name' => $request->doc_name[$i-1],
                    'doc_file' => $company_doc,
                    'doc_exp_date' => $request->doc_exp_date[$i-1],
                    'status' => 1,
                    'created_by' => 0,
                ]);
            }
        }
        
            DB::commit();

            if ($results) {
                Toastr::success('Details submitted successfully', 'Success');
                return redirect('supplier-from-submited');
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back(); 
            }
            
       }catch (\Exception $e) {
            DB::rollBack();
           return $e;
          Toastr::error('Operation Failed', 'Failed');
          return redirect()->back(); 
       }
}
public function supplier_from_submited(Request $request){
    try{
        return view('auth.supplier_from_submited');
        
    }catch (\Exception $e) {
        return $e;
       Toastr::error('Operation Failed', 'Failed');
       return redirect()->back(); 
    }
}
// SUPPLIER FORM END

    public function get_state(Request $request){
        $select_state = SysStates::select('id','name')->where('country_id',$request->country_id)->get();
        return response()->json([$select_state]);
    }
    public function getvatdetails(Request $request){
        $data = SysVat::select('id','vat_country','vat_percentage')
        ->where('vat_country',$request->vat_id)
        ->where('status',1)
        ->whereRaw("DATE_FORMAT(vat_from, '%Y-%m-%d') < '" . date('Y-m-d') . "'")
        ->orderby('vat_from','desc')->first();
        return json_encode(array('data'=>$data));
        //return response()->json([$data]);
    }

    public function amcnoteAdd($id)
    {
        try{
            return view('backEnd.crm.AmcNoteAdd', compact('id'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function amcnotesubmit(Request $request)
    {
        try {
            DB::table('sys_crm_amc_table')->where('id', $request->id)->update(
                [
                    'contact_person' => $request->contact_person,
                    'invoice' => $request->invoice,
                    'date' => $request->date,
                    'deal_id' => $request->deal_id,
                    'description' => $request->description,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'amount' => $request->amount,
		            'mobile' => $request->mobile,
                    'sales_person' => $request->sales_person,
                    'cust_name' => $request->cust_name,
                    'status' => 1,
                ]
            );
            return redirect('customer-from-submited');
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
           
        }
    }
    
    public function servicerequestcustomer($cusid,$comid)
    {
        try{
            $custdata = DB::table('sys_cust_suppl')->where('id',$cusid)->first();
            
            return view('backEnd.amc.CustomerAmcRequestForm', compact('cusid','comid','custdata'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
        
    public function servicerequestcustomeradd(Request $request)
    {
        try {
            $attachment = "";
            if ($request->file('attachment') != "") {
                $file1 = $request->file('attachment');
                $attachment = md5(time()) . "attachment." . $file1->getclientoriginalextension();
                $file1->move('public/uploads/crm_amc_doc/', $attachment);
                $attachment = $attachment;
            }
            $amc_id = DB::table('sys_crm_amc_table')->insertGetId(
                [
                    'deal_id' => 0,
                    'date' => date('Y-m-d'),
                    'cust_name' => $request->customer_id,
                    'contact_person' => $request->contact_person,
                    'mobile_no' => $request->mobile_no,
                    'start_date' => date('Y-m-d'),
                    'end_date' => date('Y-m-d'),
                    'amount' => 0,
                    'sales_person' => 0,
                    'status' => 3,
                    'is_auto' => 0,
                    'company_id' => $request->company_id,
                    ]
                );

                $scope_of_work="";
                $work=[];
                foreach($request->scope_of_work as $sw){
                    if($sw !=""){
                        if($scope_of_work==""){ $scope_of_work=$sw; }
                        else{ $scope_of_work .= '$'.$sw; }
                        $work[]=[
                            'amc_id' => $amc_id,
                            'work' => $sw,
                        ];
                    }
                }
                
                if(count($work)){
                    DB::table('sys_crm_amc_table_service_scope_of_work')->insert($work);
                }


                DB::table('sys_crm_amc_table_service_request')->insert(
                    [
                    'amc_id' => $amc_id,
                    'location_of_work' => $request->location_of_work,
                    'scope_of_work' => $scope_of_work,
                    'service_date' => $request->suggested_date,
                    'service_time' => $request->suggested_time,
                    'attachment' => $attachment,
                    'status' => 1,                    
                ]
            );
            return redirect('crm-amc-service-request-customer-success');
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
           
        }
    }

    public function servicerequestcustomersuccess()
    {
        try{
            return view('backEnd.amc.CustomerAmcRequestFormSuccess');
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
}

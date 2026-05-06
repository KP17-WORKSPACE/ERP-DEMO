<?php

namespace App\Http\Controllers\Ticket;

use App\Role;
use App\User;
use App\SmStaff;
use App\SmBaseSetup;
use App\SmDesignation;
use App\SmLeaveRequest;
use App\SmHumanDepartment;
use App\SmStudentDocument;
use App\SmStudentTimeline;
use App\SmHrPayrollGenerate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;

class SmTicketController extends Controller
{
    public function customerEdit($id)
    {
        try{
            $editData = SmStaff::find($id);
            $max_staff_no = SmStaff::max('staff_no');
            $roles = Role::where('active_status', '=', '1')->get();
            $departments = SmHumanDepartment::where('active_status', '=', '1')->get();
            $designations = SmDesignation::where('active_status', '=', '1')->get();
            $marital_ststus = SmBaseSetup::where('active_status', '=', '1')->where('base_group_id', '=', '4')->get();
            $genders = SmBaseSetup::where('active_status', '=', '1')->where('base_group_id', '=', '1')->get();
            return view('backEnd.user.addCustomer', compact('editData', 'roles', 'departments', 'designations', 'marital_ststus', 'max_staff_no', 'genders'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function customerUpdate(Request $request){
        $request->validate([   
            'first_name' => "required",
            'email' => "required", 
            'mobile' => "required", 
        ]);
        try{
            $customer = SmStaff::find($request->staff_id);
            // for update staff photo
            $staff_photos = "";
            if ($request->file('staff_photo') != "") { 
                if ($customer->staff_photo != '' && file_exists($customer->staff_photo)) {
                    unlink($customer->staff_photo);
                }
                $file = $request->file('staff_photo');
                $staff_photos = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('public/uploads/staff/', $staff_photos);
                $staff_photo = 'public/uploads/staff/' . $staff_photos;
            } else { 
                $staff_photo = $customer->staff_photo;
            }
            //update from user table
            $user = User::find($customer->user_id);  
            $user->username = $request->email;
            $user->email = $request->email;
            $user->full_name = $request->first_name . ' ' . $request->last_name; 
            $user->save();
            //update from customer table
            $update_customer = SmStaff::find($request->staff_id);
    
            $inserted_cols= ['first_name','last_name','email','mobile','company_name','department_id','designation_id','current_address','permanent_address','bank_account_name','bank_account_no','bank_name','bank_brach','paypal_account','payoneer_account','skrill_account','stripe_account','wepay_account','amazon_account','facebook_url','twiteer_url','linkedin_url','instragram_url','date_of_joining'];
            foreach ($inserted_cols as $col) {
                if(isset($request->$col)){
                    $update_customer->$col = $request->$col;
                }
            }
            $update_customer->staff_photo = $staff_photo;
            $result = $update_customer->update();
    
            if ($result) { 
                Toastr::success('Operation successful', 'Success');
                return redirect()->route('ticket.view_profile',$request->staff_id);
            }else{
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();                
            } 
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function viewCustomer($id)
    {
        try{
            $staffDetails = SmStaff::find($id);
            if (!empty($staffDetails)) {
                $staffPayrollDetails = SmHrPayrollGenerate::where('staff_id', $id)->where('payroll_status', '!=', 'NG')->get();
                $staffLeaveDetails = SmLeaveRequest::where('staff_id', $id)->get();
                $staffDocumentsDetails = SmStudentDocument::where('student_staff_id', $id)->where('type', '=', 'stf')->get();
                $timelines = SmStudentTimeline::where('staff_student_id', $id)->where('type', '=', 'stf')->get();
                return view('backEnd.user.viewCustomer', compact('staffDetails', 'staffPayrollDetails', 'staffLeaveDetails', 'staffDocumentsDetails', 'timelines'));
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

}

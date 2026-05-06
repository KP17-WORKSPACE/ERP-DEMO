<?php

namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\SmStudent;
use App\SmFeesAssign;
use App\SmFeesAssignDiscount;
use App\SmPaymentGatewaySetting;
use App\SmPaymentMethhod;


use App\Http\Requests;
use Paystack;
use Session;

class SmFeesController extends Controller
{
    public function studentFees(){
    	$id = Auth::user()->id;
    	$student = SmStudent::where('user_id', $id)->first();

        $payment_gateway = SmPaymentMethhod::where('active_status', 1)->first(); 

        $fees_assigneds = SmFeesAssign::where('student_id', $student->id)->get();
        $fees_discounts = SmFeesAssignDiscount::where('student_id', $student->id)->get();

        return view('backEnd.studentPanel.fees_pay', compact('student', 'fees_assigneds', 'fees_discounts', 'applied_discount', 'payment_gateway'));
    }



    /**
     * Redirect the User to Paystack Payment Page
     * @return Url
     */
    public function redirectToGateway(Request $request)
    {
        Session::put('fees_type_id', $request->fees_type_id);
        Session::put('amount', $request->amount);
        Session::put('payment_mode', $request->payment_mode);
        return Paystack::getAuthorizationUrl()->redirectNow();
    }

    /**
     * Obtain Paystack payment information
     * @return void
     */
    public function handleGatewayCallback()
    {
        $paymentDetails = Paystack::getPaymentData();

        $id = Auth::user()->id;
        $student = SmStudent::where('user_id', $id)->first();

        if($result){
            return redirect('student-fees')->with('message-success', 'Fees payment has been collected  successfully');
        }else{
            return redirect('student-fees')->with('message-danger', 'Something went wrong, please try again');
        }
        
    }
}

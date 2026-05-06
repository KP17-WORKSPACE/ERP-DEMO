<?php

namespace App\Http\Controllers\Parent;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\SmStudent;
use App\SmFeesAssign;
use App\SmFeesAssignDiscount;

class SmFeesController extends Controller
{
    public function childrenFees($id){
    	
    	$student = SmStudent::where('id', $id)->first();

        $fees_assigneds = SmFeesAssign::where('student_id', $student->id)->get();
        $fees_discounts = SmFeesAssignDiscount::where('student_id', $student->id)->get();     


        return view('backEnd.parentPanel.childrenFees', compact('student', 'fees_assigneds', 'fees_discounts'));
    }
}

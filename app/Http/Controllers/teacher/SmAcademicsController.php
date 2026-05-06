<?php

namespace App\Http\Controllers\teacher;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SmAssignSubject;
use App\SmClassRoutine;
use App\SmClassTime;
use App\SmWeekend;
use App\SmStaff;
use Auth;

class SmAcademicsController extends Controller
{
    public function viewTeacherRoutine(){

    	// $assinged_subjects = SmAssignSubject::where('active_status', 1)->where('teacher_id', 4)->distinct()->get(['subject_id']);

    	// $class_routines = [];
    	// foreach($assinged_subjects as $assinged_subject){
    	// 	$class_routines[] = SmClassRoutine::where('subject_id', $assinged_subject->subject_id)->first();
    	// }
        $user = Auth::user();

    	$class_times = SmClassTime::all();
    	$teacher_id = $user->staff->id;

        $sm_weekends = SmWeekend::orderBy('order', 'ASC')->where('active_status', 1)->get();
        $teachers = SmStaff::select('id', 'full_name')->where('active_status', 1)->get();

    	return view('backEnd.teacherPanel.view_class_routine', compact('class_times', 'teacher_id', 'sm_weekends', 'teachers'));

    }
}

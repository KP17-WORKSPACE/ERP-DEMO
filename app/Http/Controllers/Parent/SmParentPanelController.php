<?php

namespace App\Http\Controllers\Parent;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SmStudent;
use App\SmFeesAssign;
use App\SmFeesAssignDiscount;
use App\SmStudentDocument;
use App\SmStudentTimeline;
use App\SmExamSchedule;
use App\SmExamScheduleSubject;
use App\SmClass;
use App\SmSection;
use App\SmClassRoutine;
use App\SmStudentAttendance;
use App\SmAssignSubject;
use App\SmAssignVehicle;
use App\SmVehicle;
use App\SmRoute;
use App\SmRoomList;
use App\SmRoomType;
use App\SmDormitoryList;
use App\SmMarksGrade;
use App\SmParent;
use App\SmHomework;
use App\SmNoticeBoard;
use App\SmWeekend;
use App\SmClassTime;
use Auth;
use Session;

class SmParentPanelController extends Controller
{


    public function parentDashboard(){

        return view('backEnd.parentPanel.parent_dashboard');
    }
    public function myChildren($id){

    	$student_detail = SmStudent::where('id', $id)->first();

        $fees_assigneds = SmFeesAssign::where('student_id', $student_detail->id)->get();
        $fees_discounts = SmFeesAssignDiscount::where('student_id', $student_detail->id)->get();
        $documents = SmStudentDocument::where('student_staff_id', $student_detail->id)->where('type', 'stu')->get();
        $timelines = SmStudentTimeline::where('staff_student_id', $student_detail->id)->where('type', 'stu')->where('visible_to_student', 1)->get();
        $exams = SmExamSchedule::where('class_id', $student_detail->class_id)->where('section_id', $student_detail->section_id)->get();
        $grades = SmMarksGrade::where('active_status', 1)->get();
        return view('backEnd.parentPanel.my_children', compact('student_detail', 'fees_assigneds', 'fees_discounts', 'exams', 'documents', 'timelines', 'grades'));
    }

    public function classRoutine($id){
    	$student_detail = SmStudent::where('id', $id)->first();

    	//$classes = SmClass::where('active_status', 1)->get();

    	//$class_routines = SmClassRoutine::where('class_id', $student_detail->class_id)->where('section_id', $student_detail->section_id)->get();


    	//return view('backEnd.parentPanel.class_routine', compact('class_routines', 'classes', 'student_detail'));




        $class_id = $student_detail->class_id;
        $section_id = $student_detail->section_id;

        $sm_weekends = SmWeekend::orderBy('order', 'ASC')->where('active_status', 1)->get();
        $class_times = SmClassTime::where('type', 'class')->get();

        return view('backEnd.parentPanel.class_routine', compact('class_times', 'class_id', 'section_id', 'sm_weekends', 'student_detail'));



    }

    public function attendance($id){
    	$student_detail = SmStudent::where('id', $id)->first();
    	return view('backEnd.parentPanel.attendance', compact('student_detail'));

    }

    public function attendanceSearch(Request $request){
        $request->validate([
            'month' => 'required',
            'year' => 'required'
        ]);


        $student_detail = SmStudent::where('id', $request->student_id)->first();

        $year = $request->year;
        $month = $request->month;
        $current_day = date('d');

        $days=cal_days_in_month(CAL_GREGORIAN,$request->month,$request->year);
        //$students = SmStudent::where('class_id', $request->class)->where('section_id', $request->section)->get();
        

        $attendances = SmStudentAttendance::where('student_id', $student_detail->id)->where('attendance_date', 'like', $request->year.'-'.$request->month.'%')->get();

        return view('backEnd.parentPanel.attendance', compact('attendances', 'days', 'year', 'month', 'current_day', 'student_detail'));
    }

    public function examination($id){
 
        $student_detail = SmStudent::where('id', $id)->first();


        $exams = SmExamSchedule::where('class_id', $student_detail->class_id)->where('section_id', $student_detail->section_id)->get();
        $grades = SmMarksGrade::where('active_status', 1)->get();


        return view('backEnd.parentPanel.student_result', compact('student_detail', 'exams', 'grades'));
    }

    public function subjects($id){
        $student_detail = SmStudent::where('id', $id)->first();

        $assignSubjects = SmAssignSubject::where('class_id', $student_detail->class_id)->where('section_id', $student_detail->section_id)->get();


        return view('backEnd.parentPanel.subject', compact('assignSubjects', 'student_detail'));

    }

    public function teacherList($id){

        $student_detail = SmStudent::where('id', $id)->first();
        $teachers = SmAssignSubject::where('class_id', $student_detail->class_id)->where('section_id', $student_detail->section_id)->get()->unique('teacher_id');


        return view('backEnd.parentPanel.teacher_list', compact('teachers', 'student_detail'));

    }

    public function transport($id){
        $student_detail = SmStudent::where('id', $id)->first();

        $routes = SmAssignVehicle::where('active_status', 1)->get();

        return view('backEnd.parentPanel.transport', compact('routes', 'student_detail'));
    }

    public function dormitory($id){
        $student_detail = SmStudent::where('id', $id)->first();
        $room_lists = SmRoomList::where('active_status', 1)->get();
        $room_lists = $room_lists->groupBy('dormitory_id');
        $room_types = SmRoomType::where('active_status', 1)->get();
        $dormitory_lists = SmDormitoryList::where('active_status', 1)->get();
        return view('backEnd.parentPanel.dormitory', compact('room_lists', 'room_types', 'dormitory_lists', 'student_detail'));
    }

    public function homework($id){
        $student_detail = SmStudent::where('id', $id)->first();

        $homeworkLists = SmHomework::where('class_id', $student_detail->class_id)->where('section_id', $student_detail->section_id)->get();

        return view('backEnd.parentPanel.homework', compact('homeworkLists', 'student_detail'));
    }



    public function homeworkView($class_id, $section_id, $homework_id){
        $homeworkDetails = SmHomework::where('class_id', '=', $class_id)->where('section_id', '=', $section_id)->where('id', '=', $homework_id)->first();


        return view('backEnd.parentPanel.homeworkView', compact('homeworkDetails', 'homework_id'));
    }

    public function parentNoticeboard(){


    $allNotices = SmNoticeBoard::where('active_status', 1)->where('inform_to','LIKE','%3%')
            ->orderBy('id', 'DESC')
            ->get();

    return view('backEnd.parentPanel.parentNoticeboard', compact('allNotices'));
   }
    
}

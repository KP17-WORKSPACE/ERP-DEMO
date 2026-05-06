<?php

namespace App\Http\Controllers\Student;

use App\ApiBaseMethod;
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
use App\SmHomework;
use App\SmNoticeBoard;
use App\SmStaff;
use App\SmBook;
use App\SmSubject;
use App\SmTeacherUploadContent;
use App\SmLibraryMember;
use App\SmBookIssue;
use App\SmExamType;
use App\SmExam;
use App\SmClassTime;
use App\SmWeekend;
use DB;
use Auth;
use Validator;

class SmStudentPanelController extends Controller
{
    public function studentDashboard(Request $request){
        
    	$user = Auth::user();

        if ($user) {
            $user_id = $user->id;

        } else {
            $user_id = $request->user_id;
        }

    	$student_detail = SmStudent::where('user_id', $user_id)->first();
        $siblings = SmStudent::where('parent_id', $student_detail->parent_id)->where('active_status', 1)->get();
        $fees_assigneds = SmFeesAssign::where('student_id', $student_detail->id)->get();
        $fees_discounts = SmFeesAssignDiscount::where('student_id', $student_detail->id)->get();
        $documents = SmStudentDocument::where('student_staff_id', $student_detail->id)->where('type', 'stu')->get();
        $timelines = SmStudentTimeline::where('staff_student_id', $student_detail->id)->where('type', 'stu')->where('visible_to_student', 1)->get();
        $exams = SmExamSchedule::where('class_id', $student_detail->class_id)->where('section_id', $student_detail->section_id)->get();
        $grades = SmMarksGrade::where('active_status', 1)->get();

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            $data = [];
            $data['student_detail'] = $student_detail->toArray();
            $data['fees_assigneds'] = $fees_assigneds->toArray();
            $data['fees_discounts'] = $fees_discounts->toArray();
            $data['exams'] = $exams->toArray();
            $data['documents'] = $documents->toArray();
            $data['timelines'] = $timelines->toArray();
            $data['siblings'] = $siblings->toArray();
            $data['grades'] = $grades->toArray();
            return ApiBaseMethod::sendResponse($data, null);
        }
        return view('backEnd.studentPanel.my_profile', compact('student_detail', 'fees_assigneds', 'fees_discounts', 'exams', 'documents', 'timelines', 'siblings', 'grades'));
    }

    public function classRoutine(){
    	$user = Auth::user();
    	$student_detail = SmStudent::where('user_id', $user->id)->first();



        $class_id = $student_detail->class_id;
        $section_id = $student_detail->section_id;

        $sm_weekends = SmWeekend::orderBy('order', 'ASC')->where('active_status', 1)->get();
        $class_times = SmClassTime::where('type', 'class')->get();

        return view('backEnd.studentPanel.class_routine', compact('class_times', 'class_id', 'section_id', 'sm_weekends'));
    }

    public function studentResult(){
        $user = Auth::user();

 
        $student_detail = SmStudent::where('user_id', $user->id)->first();


        $exams = SmExamSchedule::where('class_id', $student_detail->class_id)->where('section_id', $student_detail->section_id)->get();
        $grades = SmMarksGrade::where('active_status', 1)->get();


        return view('backEnd.studentPanel.student_result', compact('student_detail', 'exams', 'grades'));
    }

    public function studentExamSchedule(){


        $user = Auth::user();
        $student_detail = SmStudent::where('user_id', $user->id)->first();


        $exam_types = SmExamType::all();



        return view('backEnd.studentPanel.exam_schedule', compact('exam_types'));
    }

    public function studentExamScheduleSearch(Request $request){

        $request->validate([
            'exam' => 'required'
        ]);

        $user = Auth::user();
        $student_detail = SmStudent::where('user_id', $user->id)->first();


        $assign_subjects = SmAssignSubject::where('class_id', $student_detail->class_id)->where('section_id', $student_detail->section_id)->get();

        if($assign_subjects->count() == 0){
            return redirect('student-exam-schedule')->with('message-danger', 'No Subject Assigned. Please assign subjects in this class.');
        }


        $assign_subjects = SmAssignSubject::where('class_id', $student_detail->class_id)->where('section_id', $student_detail->section_id)->get();


        $exams = SmExam::where('active_status', 1)->get();
        $class_id = $student_detail->class_id;
        $section_id = $student_detail->section_id;
        $exam_id = $request->exam;


        $exam_types = SmExamType::all();
        $exam_periods  = SmClassTime::where('type', 'exam')->get();

        return view('backEnd.studentPanel.exam_schedule', compact('classes', 'exams', 'assign_subjects', 'class_id', 'section_id', 'exam_id', 'exam_schedule_subjects', 'assign_subject_check','exam_types', 'exam_periods'));

    }

    public function studentViewExamSchedule($id){

        $user = Auth::user();
        $student_detail = SmStudent::where('user_id', $user->id)->first();
        $class = SmClass::find($student_detail->class_id);
        $section = SmSection::find($student_detail->section_id);
        $assign_subjects = SmExamScheduleSubject::where('exam_schedule_id', $id)->get();

        return view('backEnd.examination.view_exam_schedule_modal', compact('class', 'section', 'assign_subjects'));
    }

    public function studentMyAttendance(){
        return view('backEnd.studentPanel.student_attendance');
    }

    public function studentMyAttendanceSearch(Request $request){
        $request->validate([
            'month' => 'required',
            'year' => 'required'
        ]);

        $user = Auth::user();
        $student_detail = SmStudent::where('user_id', $user->id)->first();

        $year = $request->year;
        $month = $request->month;
        $current_day = date('d');

        $days=cal_days_in_month(CAL_GREGORIAN,$request->month,$request->year);
        

        $attendances = SmStudentAttendance::where('student_id', $student_detail->id)->where('attendance_date', 'like', $request->year.'-'.$request->month.'%')->get();
        return view('backEnd.studentPanel.student_attendance', compact('attendances', 'days', 'year', 'month', 'current_day'));
    }


    public function studentHomework(){
        $user = Auth::user();
        $student_detail = SmStudent::where('user_id', $user->id)->first();
        $homeworkLists = SmHomework::where('class_id', $student_detail->class_id)->where('section_id', $student_detail->section_id)->get();

        return view('backEnd.studentPanel.student_homework', compact('homeworkLists', 'student_detail'));
    }



    public function studentHomeworkView($class_id, $section_id, $homework_id){

    $homeworkDetails = SmHomework::where('class_id', '=', $class_id)->
    where('section_id', '=', $section_id)->where('id', '=', $homework_id)->first();



        return view('backEnd.studentPanel.studentHomeworkView', compact('homeworkDetails', 'homework_id'));
    }


    public function studentAssignment(){
        $user = Auth::user();
        $student_detail = SmStudent::where('user_id', $user->id)->first();

        $uploadContents = SmTeacherUploadContent::where('content_type', 'as')
        ->where(function ($query) use ($student_detail) {
                $query->where('available_for_all_classes', 1)
                    ->orWhere([['class', $student_detail->class_id], ['section', $student_detail->section_id]]);
        })->get();

        return view('backEnd.studentPanel.assignmentList', compact('uploadContents'));
    }

    public function studentStudyMaterial(){
        $user = Auth::user();
        $student_detail = SmStudent::where('user_id', $user->id)->first();

        $uploadContents = SmTeacherUploadContent::where('content_type', 'st')
        ->where(function ($query) use ($student_detail) {
                $query->where('available_for_all_classes', 1)
                    ->orWhere([['class', $student_detail->class_id], ['section', $student_detail->section_id]]);
        })->get();

        return view('backEnd.studentPanel.studyMetarialList', compact('uploadContents'));
    }

    public function studentSyllabus(){
        $user = Auth::user();
        $student_detail = SmStudent::where('user_id', $user->id)->first();

        $uploadContents = SmTeacherUploadContent::where('content_type', 'sy')
        ->where(function ($query) use ($student_detail) {
                $query->where('available_for_all_classes', 1)
                    ->orWhere([['class', $student_detail->class_id], ['section', $student_detail->section_id]]);
        })->get();

        return view('backEnd.studentPanel.studentSyllabus', compact('uploadContents'));
    }

    public function othersDownload(){
        $user = Auth::user();
        $student_detail = SmStudent::where('user_id', $user->id)->first();

        $uploadContents = SmTeacherUploadContent::where('content_type', 'ot')
        ->where(function ($query) use ($student_detail) {
                $query->where('available_for_all_classes', 1)
                    ->orWhere([['class', $student_detail->class_id], ['section', $student_detail->section_id]]);
        })->get();

        return view('backEnd.studentPanel.othersDownload', compact('uploadContents'));
    }
 
    public function studentSubject(){
        $user = Auth::user();
        $student_detail = SmStudent::where('user_id', $user->id)->first();
        $assignSubjects = SmAssignSubject::where('class_id', $student_detail->class_id)->where('section_id', $student_detail->section_id)->get();

        return view('backEnd.studentPanel.student_subject', compact('assignSubjects'));

    }
    

    //student panel Transport
    public function studentTransport(){
        $user = Auth::user();
        $student_detail = SmStudent::where('user_id', $user->id)->first();

        $routes = SmAssignVehicle::where('active_status', 1)->get();
        // dd( $routes );

        return view('backEnd.studentPanel.student_transport', compact('routes', 'student_detail'));
    }




    public function studentTransportViewModal($r_id, $v_id){
        $vehicle = SmVehicle::find($v_id);
        $route = SmRoute::find($r_id);
        
        return view('backEnd.studentPanel.student_transport_view_modal', compact('route', 'vehicle'));
    }

    public function studentDormitory(){
        $user = Auth::user();
        $student_detail = SmStudent::where('user_id', $user->id)->first();
        $room_lists = SmRoomList::where('active_status', 1)->get();

        $room_lists = $room_lists->groupBy('dormitory_id');



        $room_types = SmRoomType::where('active_status', 1)->get();
        $dormitory_lists = SmDormitoryList::where('active_status', 1)->get();
        return view('backEnd.studentPanel.student_dormitory', compact('room_lists', 'room_types', 'dormitory_lists', 'student_detail'));
    }



   public function studentBookList(){
    $books = SmBook::where('active_status', 1)
            ->orderBy('id', 'DESC')
            ->get();
    return view('backEnd.studentPanel.studentBookList', compact('books'));
   }

   public function studentBookIssue(){


      $user = Auth::user();
      $student_detail = SmStudent::where('user_id', $user->id)->first();

      $books = SmBook::select('id', 'book_title')->where('active_status',1)->get();
      $subjects = SmSubject::select('id', 'subject_name')->where('active_status',1)->get();


      // $issueBooks = DB::select(DB::raw("SELECT i.*, b.book_title, b.book_number, 
      //  b.isbn_no, b.author_name, m.member_type, m.student_staff_id, s.subject_name 
      //  FROM sm_book_issues i
      //  LEFT JOIN sm_books b ON i.book_id = b.id
      //  LEFT JOIN sm_library_members m ON i.member_id = m.student_staff_id
      //  LEFT JOIN sm_subjects s ON b.subject = s.id
      //  WHERE i.issue_status = 'I' and m.student_staff_id=$user->id"));

      $library_member = SmLibraryMember::where('member_type', 2)->where('student_staff_id', $student_detail->user_id)->first();
      if(empty($library_member)){
        return redirect()->back()->with('message-danger','You are not library member ! Please contact with librarian');
      }
       

      $issueBooks = SmBookIssue::where('member_id', $library_member->student_staff_id)->where('issue_status', 'I')->get();


      return view('backEnd.studentPanel.studentBookIssue', compact('books', 'subjects', 'issueBooks'));
   }

   public function studentNoticeboard(){
    $allNotices = SmNoticeBoard::where('active_status', 1)->where('inform_to','LIKE','%2%')
            ->orderBy('id', 'DESC')
            ->get();
    return view('backEnd.studentPanel.studentNoticeboard', compact('allNotices'));
   }

   public function studentTeacher(){
        $user = Auth::user();
        $student_detail = SmStudent::where('user_id', $user->id)->first();
        $teachers = SmAssignSubject::select('teacher_id')->where('class_id', $student_detail->class_id)
        ->where('section_id', $student_detail->section_id)->distinct('teacher_id')->get();


    return view('backEnd.studentPanel.studentTeacher', compact('teachers'));
   }


}

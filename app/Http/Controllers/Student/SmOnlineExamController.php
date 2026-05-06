<?php

namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SmOnlineExam;
use App\SmQuestionBank;
use App\SmQuestionBankMuOption;
use App\SmOnlineExamQuestionAssign;
use App\SmStudentTakeOnlineExam;
use App\SmStudentTakeOnlineExamQuestion;
use App\SmStudentTakeOnlnExQuesOption;
use Auth;
use DB;

class SmOnlineExamController extends Controller
{
    public function studentOnlineExam(){
    	date_default_timezone_set("Asia/Dhaka");
    	$student = Auth::user()->student;
    	$now = date('H:i:s');
    

    	$online_exams = SmOnlineExam::where('active_status', 1)->where('status', 1)->where('class_id', $student->class_id)->where('section_id', $student->section_id)->where('date', 'like', date('Y-m-d'))->where('start_time', '<', $now)->where('end_time', '>', $now)->get();
    	
    	$marks_assigned = [];
    	foreach($online_exams as $online_exam){
    		$exam = SmStudentTakeOnlineExam::where('online_exam_id', $online_exam->id)->where('student_id', $student->id)->where('status', 2)->first();
    		if($exam != ""){
    			$marks_assigned[] = $exam->online_exam_id;
    		}
    	}

    	return view('backEnd.studentPanel.online_exam', compact('online_exams', 'marks_assigned'));
    }

    public function takeOnlineExam($id){
    	$online_exam = SmOnlineExam::find($id);
    	$assigned_questions = SmOnlineExamQuestionAssign::where('online_exam_id', $online_exam->id)->get();

    	return view('backEnd.studentPanel.take_online_exam', compact('online_exam', 'assigned_questions'));
    }

    public function studentOnlineExamSubmit(Request $request){
    	// $question_option = 5;
    	

    	DB::beginTransaction();

        try{
	    	$student = Auth::user()->student;

	    	$take_online_exam = new SmStudentTakeOnlineExam();
	    	$take_online_exam->online_exam_id = $request->online_exam_id;
	    	$take_online_exam->student_id = $student->id;
	    	$take_online_exam->status = 1;
	    	$take_online_exam->save();
	    	$take_online_exam->toArray();

	    	foreach($request->question_ids as $question_id){
	    		$question_bank = SmQuestionBank::find($question_id);
	    		$trueFalse = 'trueOrFalse_'.$question_id;
	    		$trueFalse = $request->$trueFalse;

	    		$suitable_words = 'suitable_words_'.$question_id;
	    		$suitable_words = $request->$suitable_words;

	    		$exam_question = new SmStudentTakeOnlineExamQuestion();
	    		$exam_question->take_online_exam_id = $take_online_exam->id;
	    		$exam_question->question_bank_id = $question_id;
	    		$exam_question->trueFalse = $trueFalse;
	    		$exam_question->suitable_words = $suitable_words;
	    		$exam_question->save();
	    		$exam_question->toArray();
	    		if($question_bank->type == "M"){
	    			$question_options = SmQuestionBankMuOption::where('question_bank_id', $question_bank->id)->get();

	    			$i = 0;
	    			foreach($question_options as $question_option){
	    				$options = 'options_'.$question_id.'_'.$i++;
	    				// $options = $request->$options[$i++];
	    				// dd($request->$options);
	    				
	    				$exam_question_option = new SmStudentTakeOnlnExQuesOption();
	    				$exam_question_option->take_online_exam_question_id = $exam_question->id;
	    				$exam_question_option->title = $question_option->title;
	    				if(isset($request->$options)){
	    					$exam_question_option->status = $request->$options;
	    				}else{
	    					$exam_question_option->status = 0;
	    				}
	    				$exam_question_option->save();
	    				
	    			}
	    		}
	    	}

    	DB::commit();
        return redirect('student-online-exam')->with('message-success', 'Answer submitted successfully');

    } catch(Exception $e){
        DB::rollBack();
    }

    return redirect()->back()->with('message-danger', 'Something went wrong, please try again');
	}

	public function studentViewResult(){

        $result_views = SmStudentTakeOnlineExam::where('active_status', 1)->where('status', 2)->get();
        
        return view('backEnd.studentPanel.student_view_result', compact('result_views'));

    }

    public function studentAnswerScript($exam_id, $s_id){
    	$take_online_exam = SmStudentTakeOnlineExam::where('online_exam_id', $exam_id)->where('student_id', $s_id)->first();
        return view('backEnd.examination.online_answer_view_script_modal', compact('take_online_exam'));
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SmExam;
use App\SmSubject;
use App\SmClass;
use App\SmSection; 
use App\SmStudent; 
use App\SmExamSetup;
use App\SmExamType;
use App\SmMarkStore;
use App\SmResultStore;
use App\SmClassSection;
use App\SmAssignSubject;

class SmExamController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $exams = SmExam::all();
        $exams_types = SmExamType::all();
        $classes = SmClass::where('active_status', 1)->get();
        $subjects = SmSubject::where('active_status', 1)->get();
        $sections = SmSection::where('active_status',1)->get();
        return view('backEnd.examination.exam', compact('exams','classes','subjects','exams_types','sections'));
    }


    public function exam_setup($id)
    {
        $exams = SmExam::all();
        $exams_types = SmExamType::all();
        $classes = SmClass::where('active_status', 1)->get();
        $subjects = SmSubject::where('active_status', 1)->get();
        $sections = SmSection::where('active_status',1)->get();
        $selected_exam_type_id = $id;
       
        return view('backEnd.examination.exam', compact('exams','classes','subjects','exams_types','sections','selected_exam_type_id'));
    }

    

public function exam_reset(){

        $exams = SmExam::all();
        SmExam::query()->truncate();


        $exams_types = SmExamType::all();
        SmExamType::query()->truncate();

        $exam_mark_stores = SmMarkStore::all();
        SmMarkStore::query()->truncate();

        $exam_results_stores = SmResultStore::all();
        SmResultStore::query()->truncate();

        SmExamSetup::query()->truncate();



        $classes = SmClass::where('active_status', 1)->get();
        $subjects = SmSubject::where('active_status', 1)->get();
        $sections = SmSection::where('active_status',1)->get();
        return view('backEnd.examination.exam', compact('exams','classes','subjects','exams_types','sections'));


}
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){


        $request->validate([
            'class_ids' => 'required|array',
            'section_ids' => 'required|array',
            'subjects_ids' => 'required|array', 
            'exams_types' => 'required|array', 
        ],
        [
            'class_ids.required' => 'At least one checkbox required!',
            'section_ids.required' => 'At least one checkbox required!',
            'subjects_ids.required' => 'At least one checkbox required!', 
            'exams_types.required' => 'At least one checkbox required!', 
        ]
    );  

        if(!empty($request->class_ids)){
            foreach ($request->class_ids as $class_id) {
                if(!empty($request->section_ids)){
                    $section_eligilbe_id= SmClassSection::where('class_id',$class_id)->get()->toArray();
                    $SectionExistingArray =[];
                    foreach ($section_eligilbe_id as $r) {  
                        $SectionExistingArray[] =   $r['section_id'];
                    } 

                    foreach ($request->section_ids as $section_id) {
                        if(in_array($section_id, $SectionExistingArray)){

                            $Subject_eligilbe_id= SmAssignSubject::where('class_id',$class_id)->where('section_id',$section_id)->get()->toArray();
                            $SubjectExistingArray =[];
                            foreach ($Subject_eligilbe_id as $r) {  
                                $SubjectExistingArray[] =   $r['subject_id'];
                            }

                            if(!empty($request->subjects_ids)){
                                foreach ($request->subjects_ids as $subject_id) {
                                    if(in_array($subject_id, $SubjectExistingArray)){

                                        if(!empty($request->exams_types)){
                                            foreach ($request->exams_types as $exam_type_id) {
                                                $exam = new SmExam();
                                                $exam->exam_type_id = $exam_type_id;
                                                $exam->class_id = $class_id;
                                                $exam->section_id = $section_id;
                                                $exam->subject_id = $subject_id;
                                                $exam->exam_mark = $request->exam_marks;  
                                                $exam->save();
                                                $exam->toArray();


                                                $exam_term_id = $exam->id;
                                                
                                                    $exam_term_id = $exam->id;

                                                    $length= count($request->exam_title);
                                                    for($i=0; $i<$length; $i++){

                                                        $ex_title = $request->exam_title[$i];
                                                        $ex_mark = $request->exam_mark[$i];

                                                        $newSetupExam = new SmExamSetup();
                                                        $newSetupExam->class_id = $class_id ;
                                                        $newSetupExam->section_id = $section_id ;
                                                        $newSetupExam->subject_id = $subject_id ;
                                                        $newSetupExam->exam_term_id = $exam_type_id ;
                                                        $newSetupExam->exam_title = $ex_title ;
                                                        $newSetupExam->exam_mark = $ex_mark ;  
                                                        $result=$newSetupExam->save();


                                                    } //end loop exam setup loop

                                                

                                            }//end exam_type_ids loop
                                        }
                                    }
                                }//end subject_ids loop
                            }
                        }

                    }//end section_ids loop
                }
            }//end class_ids loop
        }
        if(isset($result)){
            return redirect()->back()->with('message-success', 'Exam has been created successfully');
        }else{
            return redirect()->back()->with('message-danger', 'Something went wrong, please try again');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $exam = SmExam::find($id);
        $exams = SmExam::all();
        $classes = SmClass::where('active_status', 1)->get();
        $subjects = SmSubject::where('active_status', 1)->get();
        $sections = SmSection::where('active_status', 1)->get();
        return view('backEnd.examination.exam', compact('exam', 'exams','classes','subjects','sections'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

 

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $request->validate([
            'name' => "required",
            'class' => "required",
            'section' => "required",
            'subject' => "required",
            'exam_mark' => "required",
        ]);

        $exam = SmExam::find($request->id);
        $exam->name = $request->name;

        $exam->class_id = $request->class;
        $exam->section_id = $request->section;
        $exam->subject_id = $request->subject;
        $exam->exam_mark = $request->exam_mark;
        $exam->exam_date = $request->exam_date;

        $exam->note = $request->note;
        $result = $exam->save();
        if($result){
            return redirect('exam')->with('message-success', 'Exam has been updated successfully');
        }else{
            return redirect()->back()->with('message-danger', 'Something went wrong, please try again');
        }
    }
    public function examSetup($id){
        $exam = SmExam::find($id);
    
        $exams = SmExam::all();
        $classes = SmClass::where('active_status', 1)->get();
        $subjects = SmSubject::where('active_status', 1)->get();
        $sections = SmSection::where('active_status', 1)->get();
        return view('backEnd.examination.exam_setup', compact('exam','exams','classes','subjects','sections'));
    }


    public function examSetupStore(Request $request){


    $class_id= $request->class;
    $section_id= $request->section;
    $subject_id= $request->subject;
    $exam_title= $request->name;
    $exam_term_id = $request->exam_term_id;

    $total_exam_mark= $request->total_exam_mark;
    $totalMark= $request->totalMark;
    
    if($total_exam_mark == $totalMark){
        $length= count($request->exam_title);
        for($i=0; $i<$length; $i++){
            $ex_title = $request->exam_title[$i];
            $ex_mark = $request->exam_mark[$i];

            $newSetupExam = new SmExamSetup();
            $newSetupExam->class_id = $class_id ;
            $newSetupExam->section_id = $section_id ;
            $newSetupExam->subject_id = $subject_id ;
            $newSetupExam->exam_term_id = $exam_term_id ;
            $newSetupExam->exam_title = $ex_title ;
            $newSetupExam->exam_mark = $ex_mark ;  
            $result=$newSetupExam->save();
            if($result){
                return redirect('exam')->with('message-success-delete', 'Exam Setup Successfully');
            }else{
                return redirect()->back()->with('message-danger-delete', 'Something went wrong, please try again');
            }  
        } 
    }else{
        return redirect()->back()->with('message-danger-delete', 'Something went wrong, please try again');
    }


        dd($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $exam = SmExam::destroy($id);
        if($exam){
            return redirect('exam')->with('message-success-delete', 'Exam has been deleted successfully');
        }else{
            return redirect()->back()->with('message-danger-delete', 'Something went wrong, please try again');
        }
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmAssignSubject extends Model
{
    public function subject(){
    	return $this->belongsTo('App\SmSubject', 'subject_id', 'id');
    }

    public function teacher(){
    	return $this->belongsTo('App\SmStaff', 'teacher_id', 'id');
    }

    public static function getNumberOfPart($subject_id, $class_id, $section_id, $exam_term_id){
    	$results= SmExamSetup::where([
    		['class_id',$class_id],
    		['subject_id',$subject_id],
    		['section_id',$section_id],
    		['exam_term_id',$exam_term_id],
    	])->get();
    	return $results;
    }

    public static function getMarksOfPart($student_id, $subject_id, $class_id, $section_id, $exam_term_id){
        $results= SmMarkStore::where([
            ['student_id',$student_id],
            ['class_id',$class_id],
            ['subject_id',$subject_id],
            ['section_id',$section_id],
            ['exam_term_id',$exam_term_id],
        ])->get();
        return $results;
    }
    public static function getSumMark($student_id, $subject_id, $class_id, $section_id, $exam_term_id){
        $results= SmMarkStore::where([
            ['student_id',$student_id],
            ['class_id',$class_id],
            ['subject_id',$subject_id],
            ['section_id',$section_id],
            ['exam_term_id',$exam_term_id],
        ])->sum('total_marks');
        return $results;
    }



}

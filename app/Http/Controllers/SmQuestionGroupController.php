<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SmQuestionGroup;

class SmQuestionGroupController extends Controller
{
    public function __construct(){
        $this->middleware('PM');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $groups = SmQuestionGroup::all();
        return view('backEnd.examination.question_group', compact('groups'));
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
    public function store(Request $request)
    {
        $request->validate([
            'title' => "required|unique:sm_question_groups"
        ]);

        $group = new SmQuestionGroup();
        $group->title = $request->title;
        $result = $group->save();
        if($result){
            return redirect()->back()->with('message-success', 'Group has been created successfully');
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
        $group = SmQuestionGroup::find($id);
        $groups = SmQuestionGroup::all();
        return view('backEnd.examination.question_group', compact('groups', 'group'));
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
            'title' => "required|unique:sm_question_groups,title".$request->id  
        ]);

        $group = SmQuestionGroup::find($request->id);
        $group->title = $request->title;
        $result = $group->save();
        if($result){
            return redirect('question-group')->with('message-success', 'Group has been updated successfully');
        }else{
            return redirect()->back()->with('message-danger', 'Something went wrong, please try again');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $group = SmQuestionGroup::destroy($id);
        if($group){
            return redirect('question-group')->with('message-success-delete', 'Group has been deleted successfully');
        }else{
            return redirect()->back()->with('message-danger-delete', 'Something went wrong, please try again');
        }
    }
}

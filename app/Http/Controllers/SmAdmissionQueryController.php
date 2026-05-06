<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SmStudent;
use App\SmSetupAdmin;
use App\SmClass;
use App\SmAdmissionQuery;
use App\SmAdmissionQueryFollowup;
use DB;
use Auth;

class SmAdmissionQueryController extends Controller
{

    public function __construct()
    {
        $this->middleware('PM');
    }


    public function index(){
        $admission_queries = SmAdmissionQuery::orderBy('id', 'DESC')->get();
    	$classes = SmClass::where('active_status', 1)->get();
    	$references = SmSetupAdmin::where('type', 4)->where('active_status', 1)->get();
    	$sources = SmSetupAdmin::where('type', 3)->where('active_status', 1)->get();
    	return view('backEnd.admin.admission_query', compact('admission_queries', 'references', 'classes', 'sources'));
    }

    public function admissionQueryStore(Request $request){
    	$user = Auth::user();
    	

    	$admission_query = new SmAdmissionQuery();
    	$admission_query->name = $request->name;
    	$admission_query->phone = $request->phone;
    	$admission_query->email = $request->email;
    	$admission_query->address = $request->address;
    	$admission_query->description = $request->description;
    	$admission_query->date = date('Y-m-d', strtotime($request->date));
    	$admission_query->next_follow_up_date = date('Y-m-d', strtotime($request->next_follow_up_date));
    	$admission_query->assigned = $request->assigned;
    	$admission_query->reference = $request->reference;
    	$admission_query->source = $request->source; 
    	$admission_query->created_by = $user->id;
    	$result = $admission_query->save();
    	if($result){
            return redirect()->back()->with('message-success', 'Query has been created successfully');
        }else{
            return redirect()->back()->with('message-danger', 'Something went wrong, please try again');
        }
    }

    public function admissionQueryEdit($id){
    	$admission_query = SmAdmissionQuery::find($id);
    	$classes = SmClass::where('active_status', 1)->get();
    	$references = SmSetupAdmin::where('type', 4)->where('active_status', 1)->get();
    	$sources = SmSetupAdmin::where('type', 3)->where('active_status', 1)->get();
    	return view('backEnd.admin.admission_query_edit', compact('admission_query', 'references', 'classes', 'sources'));
    }

    public function admissionQueryUpdate(Request $request){

    	$admission_query = SmAdmissionQuery::find($request->id);
    	$admission_query->name = $request->name;
    	$admission_query->phone = $request->phone;
    	$admission_query->email = $request->email;
    	$admission_query->address = $request->address;
    	$admission_query->description = $request->description;
    	$admission_query->date = date('Y-m-d', strtotime($request->date));
    	$admission_query->next_follow_up_date = date('Y-m-d', strtotime($request->next_follow_up_date));
    	$admission_query->assigned = $request->assigned;
    	$admission_query->reference = $request->reference;
    	$admission_query->source = $request->source; 
    	$result = $admission_query->save();
    	if($result){
            return redirect()->back()->with('message-success', 'Query has been updated successfully');
        }else{
            return redirect()->back()->with('message-danger', 'Something went wrong, please try again');
        }
    }

    public function addQuery($id){
    	$admission_query = SmAdmissionQuery::find($id);

    	$follow_up_lists = SmAdmissionQueryFollowup::where('admission_query_id', $id)->get();

    	$classes = SmClass::where('active_status', 1)->get();
    	$references = SmSetupAdmin::where('type', 4)->where('active_status', 1)->get();
    	$sources = SmSetupAdmin::where('type', 3)->where('active_status', 1)->get();
    	return view('backEnd.admin.add_query', compact('admission_query', 'follow_up_lists', 'references', 'classes', 'sources'));
    }

    public function queryFollowupStore(Request $request){
    	$request->validate([
    		'next_follow_up_date' => 'required',
    		'response' => 'required'
    	]);

    	$user = Auth::user();

    	DB::beginTransaction();
    	try{
	    	
    		$admission_query = SmAdmissionQuery::find($request->id);
    		$admission_query->follow_up_date = date('Y-m-d', strtotime($request->follow_up_date));
    		$admission_query->next_follow_up_date = date('Y-m-d', strtotime($request->next_follow_up_date));
    		$admission_query->active_status = $request->status;
    		$admission_query->save();
    		$admission_query->toArray();


    		$follow_up = new SmAdmissionQueryFollowup();
    		$follow_up->admission_query_id = $admission_query->id;
    		$follow_up->response = $request->response;
    		$follow_up->note = $request->note;
    		$follow_up->created_by = $user->id;
    		$follow_up->save();


	    	DB::commit();
	    	return redirect()->back()->with('message-success', 'Follow up report has beed forwarded successfully');
    	} catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('message-danger', 'Something went wrong, please try again');
        }
    }

    public function deleteFollowUp($id){
    	$result = SmAdmissionQueryFollowup::destroy($id);
    	if($result){
            return redirect()->back()->with('message-success-delete', 'Follow up query has been updated successfully');
        }else{
            return redirect()->back()->with('message-danger-delete', 'Something went wrong, please try again');
        }
    }

    public function admissionQueryDelete(Request $request){
    	DB::beginTransaction();
    	try{
	    	$admission_query = SmAdmissionQuery::find($request->id);

	    	SmAdmissionQueryFollowup::where('admission_query_id', $admission_query->id)->delete();

	    	$admission_query->delete();

	    	DB::commit();
	    	return redirect()->back()->with('message-success', 'Admission query has beed deleted successfully');
    	} catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('message-danger', 'Something went wrong, please try again');
        }
    }

    public function admissionQuerySearch(Request $request){



        $date_from = date('Y-m-d', strtotime($request->date_from));
        $date_to = date('Y-m-d', strtotime($request->date_to)); 

        $admission_queries = SmAdmissionQuery::query();

        if($request->date_from != "" && $request->date_to){
            $admission_queries->where('date', '>=', $date_from)->where('date', '<=', $date_to);
        }
         if($request->source != ""){
            $admission_queries->where('source', $request->source);
        }
        if($request->status != ""){
            $admission_queries->where('active_status', $request->status);
        }
        $admission_queries = $admission_queries->get();

        $date_from = $request->date_from;
        $date_to = $request->date_to;
        $source_id = $request->source;
        $status_id = $request->status;



        $classes = SmClass::where('active_status', 1)->get();
        $references = SmSetupAdmin::where('type', 4)->where('active_status', 1)->get();
        $sources = SmSetupAdmin::where('type', 3)->where('active_status', 1)->get();
        return view('backEnd.admin.admission_query', compact('admission_queries', 'references', 'classes', 'sources', 'date_from', 'date_to', 'source_id','status_id'));

    }


}

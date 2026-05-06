<?php

namespace Modules\Project\Http\Controllers;


use App\SmStaff;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Modules\Project\Entities\InfixTeam;
use Modules\Project\Entities\InfixProject;
use Modules\Project\Entities\InfixTeamMember;
use Modules\Project\Entities\InfixProjectTask;
use Modules\Project\Entities\InfixProjectTeam;
use Modules\Project\Entities\InfixProjectColor;
use Modules\Project\Entities\InfixProjectCategory;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function InfixProjectCategoryList()
    {
        try{
            $project_categories = InfixProjectCategory::where('active_status', 1)->get();
            return view('project::backend.category', compact('project_categories'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function InfixProjectCategoryEdit($id)
    {
        try{
            $project_categories = InfixProjectCategory::where('active_status', 1)->get();
            $category = InfixProjectCategory::findOrfail($id);
            return view('project::backend.category', compact('project_categories', 'category'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function InfixProjectCategoryStore(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100',
            'description' => 'required|max:200'
        ]);
        try {
            $category = new InfixProjectCategory();
            $category->name = $request->name;
            $category->description = $request->description;
            $category->save();
            Toastr::success('Operation successful', 'Success');
            return redirect()->route('InfixProjectCategoryList');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function InfixProjectCategoryUpdate(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100',
            'description' => 'required|max:200'
        ]);
        try {
            $category = InfixProjectCategory::find($request->id);
            $category->name = $request->name;
            $category->description = $request->description;
            $category->save();
            Toastr::success('Operation successful', 'Success');
            return redirect()->route('InfixProjectCategoryList');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    public function InfixProjectCategoryDelete(Request $request, $id)
    {
        try {
            $category = InfixProjectCategory::findOrfail($id);
            $category->delete();
            Toastr::success('Operation successful', 'Success');
            return redirect()->route('InfixProjectCategoryList');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
    //===================Start TEAM SECTION=====================
    public function InfixTeamList()
    {
        
        try{
            $staffs = SmStaff::where('active_status', 1)->get();
            $teams = InfixTeam::get();
            return view('project::backend.team', compact('teams', 'staffs'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function InfixTeamStore(Request $request)
    {

        $request->validate([
            'name' => 'required|max:100',
            'description' => 'required|max:200',
            'staff' => 'required|array',
        ]);
        try {
            $team = new InfixTeam();
            $team->name = $request->name;
            $team->description = $request->description;
            $result = $team->save();

            foreach ($request->staff as $member) {
                $team_member = new InfixTeamMember();
                $team_member->staff_id = $member;
                $team_member->team_id = $team->id;
                $team_member->save();
            }
            Toastr::success('Operation successful', 'Success');
            return redirect()->route('InfixTeamList');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->route('InfixTeamList');
        }
    }
    public function InfixTeamEdit($id)
    {
        try{
            $team = InfixTeam::findOrfail($id);
            $teamByNames = InfixTeamMember::select('staff_id')->where('team_id', '=', $team->id)->get();
            $memberId = array();
            foreach ($teamByNames as $teamByName) {
                $memberId[] = $teamByName->staff_id;
            }
            $staffs = SmStaff::where('active_status', 1)->get();
            $teams = InfixTeam::get();
            return view('project::backend.team', compact('teams', 'team', 'memberId', 'staffs'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function InfixTeamUpdate(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100',
            'description' => 'required|max:200',
            'staff' => 'required|array',
        ]);
        try {
            $team = InfixTeam::findOrfail($request->id);
            $team->name = $request->name;
            $team->description = $request->description;
            $result = $team->save();

            $exsiting_members = InfixTeamMember::where('team_id', $team->id)->get();

            foreach ($exsiting_members as $exsiting_member) {
                $member = InfixTeamMember::findOrfail($exsiting_member->id);
                $member->delete();
            }

            foreach ($request->staff as $member) {
                $team_member = new InfixTeamMember();
                $team_member->staff_id = $member;
                $team_member->team_id = $team->id;
                $team_member->save();
            }
            Toastr::success('Operation successful', 'Success');
            return redirect()->route('InfixTeamList');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->route('InfixTeamList');
        }
    }
    public function InfixTeamDelete($id)
    {
        try {
            $project_teams = InfixProject::where('team_id', '=', $id)->get();
            foreach ($project_teams as $project_team) {
                $project = InfixProject::findOrfail($project_team->id);
                $project->team_id = null;
                $project->save();
            }

            $team = InfixTeam::findOrfail($id);
            $team->delete();
            Toastr::success('Operation successful', 'Success');
            return redirect()->route('InfixTeamList');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->route('InfixTeamList');
        }
    }
    //===================END TEAM SECTION=====================

    //==============START PROJECT TASK===============
    public function InfixProjectTaskList($id)
    {
        try{
            $imcomplete_tasks = InfixProjectTask::where('project_id', $id)
            ->where('infix_project.is_complete', '=', 0)
            ->leftjoin('infix_project', 'infix_project.id', '=', 'infix_project_tasks.project_id')
            ->leftjoin('sm_staffs', 'sm_staffs.id', '=', 'infix_project_tasks.assigned_to')
            ->select('infix_project_tasks.id', 'sm_staffs.id as staff_id', 'sm_staffs.full_name', 'infix_project_tasks.title', 'infix_project_tasks.description', 'infix_project_tasks.is_complete', 'infix_project_tasks.image', 'infix_project_tasks.assigned_to')
            ->orderBy('infix_project_tasks.due_date', 'asc')
            ->get();
        $complete_tasks = InfixProjectTask::where('project_id', $id)
            ->where('infix_project.is_complete', '=', 1)
            ->leftjoin('infix_project', 'infix_project.id', '=', 'infix_project_tasks.project_id')
            ->leftjoin('sm_staffs', 'sm_staffs.id', '=', 'infix_project_tasks.assigned_to')
            ->select('infix_project_tasks.id', 'sm_staffs.id as staff_id', 'sm_staffs.full_name', 'infix_project_tasks.title', 'infix_project_tasks.description', 'infix_project_tasks.is_complete', 'infix_project_tasks.image', 'infix_project_tasks.assigned_to')
            ->get();
        $project = InfixProject::findOrfail($id);
        $team_members = InfixTeamMember::where('team_id', $project->team_id)
            ->leftjoin('sm_staffs', 'infix_team_member.staff_id', '=', 'sm_staffs.id')
            ->get();
        return view('project::backend.task', compact('imcomplete_tasks', 'complete_tasks', 'project', 'team_members'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function InfixMyTaskList()
    {
        try{
            $staff = SmStaff::where('user_id', '=', Auth::user()->id)->first();
            $incomplete_tasks = InfixProjectTask::where('assigned_to', '=', $staff->id)->where('is_complete', '=', 0)->get();
            $complete_tasks = InfixProjectTask::where('assigned_to', '=', $staff->id)->where('is_complete', '=', 1)->get();
            $projects = InfixProject::where('infix_project.active_status', '=', 1)
                ->join('infix_team', 'infix_team.id', '=', 'infix_project.team_id')
                ->join('infix_team_member', 'infix_team_member.team_id', '=', 'infix_team.id')
                ->where('infix_team_member.staff_id', '=', $staff->id)
                ->select('infix_project.*')
                ->orderBy('infix_project.due_date', 'asc')
                ->get();
            // return $projects;
            return view('project::backend.my_task', compact('staff', 'incomplete_tasks', 'complete_tasks', 'projects'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function InfixProjectTaskComplete(Request $request, $id)
    {
        $task = InfixProjectTask::findOrfail($id);
        $task->is_complete = 1;
        $task->save();

        $complete_tasks = InfixProjectTask::where('is_complete', '=', 1)->where('project_id', '=', $task->project_id)->where('assigned_to', '=', $task->assigned_to)->get();

        return response()->json($complete_tasks);
    }
    public function downloadTaskFile(Request $request)
    {
        $file = $request->file_name;
        if (file_exists($file)) {
            return Response()->download($file);
        }
        return redirect()->back();
    }
    public function InfixProjectTaskEdit($id)
    {
        try{
            $single_task = InfixProjectTask::findOrfail($id);
            $imcomplete_tasks = InfixProjectTask::where('project_id', $single_task->project_id)
                ->where('infix_project_tasks.is_complete', '=', 0)
                ->leftjoin('infix_project', 'infix_project.id', '=', 'infix_project_tasks.project_id')
                ->leftjoin('sm_staffs', 'sm_staffs.id', '=', 'infix_project_tasks.assigned_to')
                ->select('infix_project_tasks.id', 'sm_staffs.id as staff_id', 'sm_staffs.full_name', 'infix_project_tasks.title', 'infix_project_tasks.description', 'infix_project_tasks.is_complete', 'infix_project_tasks.image', 'infix_project_tasks.assigned_to')
                ->get();
            $complete_tasks = InfixProjectTask::where('project_id', $single_task->project_id)
                ->where('infix_project_tasks.is_complete', '=', 1)
                ->leftjoin('infix_project', 'infix_project.id', '=', 'infix_project_tasks.project_id')
                ->leftjoin('sm_staffs', 'sm_staffs.id', '=', 'infix_project_tasks.assigned_to')
                ->select('infix_project_tasks.id', 'sm_staffs.id as staff_id', 'sm_staffs.full_name', 'infix_project_tasks.title', 'infix_project_tasks.description', 'infix_project_tasks.is_complete', 'infix_project_tasks.image', 'infix_project_tasks.assigned_to')
                ->get();
            $project = InfixProject::findOrfail($single_task->project_id);
    
            $team_members = InfixTeamMember::where('team_id', $project->team_id)
                ->leftjoin('sm_staffs', 'infix_team_member.staff_id', '=', 'sm_staffs.id')
                ->get();
            return view('project::backend.task', compact('single_task', 'imcomplete_tasks', 'complete_tasks', 'project', 'team_members'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function InfixProjectTaskImcomplete()
    {
        $tasks = InfixProjectTask::where('is_complete', '=', 0)->get();
        return response()->json($tasks);
    }

    public function InfixProjectTaskStore(Request $request)
    {
        $project = InfixProject::findOrfail($request->project_id);
        $request->validate([
            'title' => 'required|max:200',
            'description' => 'max:300',
            'assign' => 'required',
            'due_date' => 'required|date|before_or_equal:' . $project->due_date
        ]);
        try {
            $task = new InfixProjectTask();
            $task->title = $request->title;
            $task->description = $request->description;
            $task->project_id = $request->project_id;
            $task->assigned_to = $request->assign;
            $task->due_date = $request->due_date;
            $task->created_by = Auth::user()->id;

            $fileName = "";
            if ($request->file('task_file') != "") {
                $file = $request->file('task_file');
                $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('public/uploads/tasks/', $fileName);
                $fileName = 'public/uploads/tasks/' . $fileName;
            }
            $task->image = $fileName;
            $result = $task->save();
            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();;
        }
    }
    public function InfixProjectComplete($id)
    {
        $project = InfixProject::findOrfail($id);
        $project->is_complete = 1;
        $project->save();
        return response()->json($id);
    }
    public function InfixProjectIncomplete($id)
    {
        $project = InfixProject::findOrfail($id);
        $project->is_complete = 0;
        $project->save();
        return response()->json(null);
    }
    public function InfixProjectTaskUpdate(Request $request)
    {
        $project = InfixProject::findOrfail($request->project_id);
        $request->validate([
            'title' => 'required|max:200',
            'description' => 'max:300',
            'assign' => 'required',
            'due_date' => 'required|date|before_or_equal:' . $project->due_date
        ]);

        try {
            $task = InfixProjectTask::findOrfail($request->id);
            $task->title = $request->title;
            $task->description = $request->description;
            $task->project_id = $request->project_id;
            $task->assigned_to = $request->assign;
            $task->due_date = $request->due_date;
            $task->created_by = Auth::user()->id;
            if (!empty($request->task_file)) {
                $fileName = "";
                if ($request->file('task_file') != "") {
                    $file = $request->file('task_file');
                    $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                    $file->move('public/uploads/tasks/', $fileName);
                    $fileName = 'public/uploads/tasks/' . $fileName;
                }
                $task->image = $fileName;
            } else {
                $task->image = $request->old_file;
            }

            $task->save();
            Toastr::success('Operation successful', 'Success');
            return redirect()->route('InfixProjectTaskList');
        } catch (\Exception $e) {
            // Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();;
        }
    }
    public function InfixProjectTaskDelete($id)
    {
        try {
            $task = InfixProjectTask::findOrfail($id);
            $task->delete();

            Toastr::success('Task Deleted successful', 'Success');
            return redirect()->route('InfixProjectTaskList');
        } catch (\Exception $e) {
            // Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();;
        }
    }
    //==============END PROJECT TASK===============
    public function InfixProjectTeamList()
    {
        $staffs = SmStaff::where('active_status', 1)->get();
        $colors = InfixProjectColor::where('is_active', 1)->get();
        $teams = InfixProjectTeam::where('is_active', 1)->get();
        return view('project::backend.team', compact('teams'));
        try{


        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function InfixProjectTeamStore(Request $request)
    {
        $request->validate([
            'title' => 'required|max:200',
            'description' => 'required|max:300',
            'assign' => 'required'
        ]);
        
        try{
        // try {
            $team = new InfixProjectTeam();
            $team->name = $request->name;
            $team->description = $request->description;
            $result = $team->save();

            if ($result) {
                Toastr::success('Operation successful', 'Success');
                return redirect()->route('InfixProjectCategoryList');
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->route('InfixProjectCategoryList');
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }




    public function InfixProjectList()
    {
        // $projects = InfixProject::where('active_status', 1)->orderBy('due_date', 'asc')->get();
        $projects = InfixProject::where('active_status', 1)->orderBy('due_date', 'asc')->paginate(15);
        $project_categories = InfixProjectCategory::where('active_status', 1)->get();
        $customers = SmStaff::where('active_status', 1)->where('role_id', 2)->get();
        $teams = InfixTeam::where('active_status', 1)->get();
        return view('project::backend.project', compact('project_categories', 'customers', 'teams', 'projects'));
    }
    public function InfixMyProjectList()
    {
        $staff = SmStaff::where('user_id', '=', Auth::user()->id)->first();
        $projects = InfixProject::where('infix_project.active_status', 1)
            ->join('infix_team_member', 'infix_team_member.team_id', '=', 'infix_project.team_id')
            ->where('infix_team_member.staff_id', '=', $staff->id)
            ->select('infix_project.*')
            ->orderBy('due_date', 'asc')->paginate(15);
        return view('project::backend.my_projects', compact('projects'));
    }



    public function InfixProjectStore(Request $request)
    {
        $request->validate([
            'customer' => 'required',
            'project_name' => 'required|max:200',
            'start_date' => 'required',
            'due_date' => 'required',
            'category' => 'required',
            'team' => 'required',
        ]);
        try{

        
        $fileName = "";
        if ($request->file('project_image') != "") {
            $file = $request->file('project_image');
            $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
            $file->move('public/uploads/projects/', $fileName);
            $fileName =  'public/uploads/projects/' . $fileName;
        }

        $data = new InfixProject();
        $data->code = Auth::user()->id . '-' . time();
        $data->name = $request->project_name;
        $data->description = $request->description;
        $data->start_date = date('Y-m-d', strtotime($request->start_date));
        $data->due_date = date('Y-m-d', strtotime($request->due_date));
        $data->category_id = $request->category;
        $data->customer_id = $request->customer;
        $data->team_id = $request->team;
        $data->photo = $fileName;
        $result = $data->save();


        
            Toastr::success('Operation successful', 'Success');
            return redirect()->route('InfixProjectList');
        
        }catch(\Exception $e){
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->route('InfixProjectList');
        }
    }
    public function InfixProjectUpdate(Request $request)
    {

        $request->validate([
            'customer' => 'required',
            'project_name' => 'required|max:200',
            'start_date' => 'required',
            'due_date' => 'required',
            'category' => 'required',
            'team' => 'required',
        ]);
        try {
            $fileName = "";
            if (!empty($request->project_image)) {
                if ($request->file('project_image') != "") {
                    $file = $request->file('project_image');
                    $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                    $file->move('public/uploads/projects/', $fileName);
                    $fileName =  'public/uploads/projects/' . $fileName;
                }
            } else {
                $fileName = $request->old_image;
            }

            $data = InfixProject::findOrfail($request->id);
            $data->code = Auth::user()->id . '-' . time();
            $data->name = $request->project_name;
            $data->description = $request->description;
            $data->start_date = date('Y-m-d', strtotime($request->start_date));
            $data->due_date = date('Y-m-d', strtotime($request->due_date));
            $data->category_id = $request->category;
            $data->customer_id = $request->customer;
            $data->team_id = $request->team;
            $data->photo = $fileName;
            $data->save();

            Toastr::success('Operation successful', 'Success');
            return redirect()->route('InfixProjectList');
        } catch (\Exception $e) {
            // return $e;
            // Toastr::error('Operation Failed', 'Failed');
            return redirect()->route('InfixProjectList');
        }
    }

    public function InfixProjectEdit($id)
    {
        $edit = InfixProject::findOrfail($id);
        $projects = InfixProject::where('active_status', 1)->orderBy('due_date', 'asc')->paginate(15);
        $project_categories = InfixProjectCategory::where('active_status', 1)->get();
        $customers = SmStaff::where('active_status', 1)->where('role_id', 2)->get();
        $teams = InfixTeam::where('active_status', 1)->get();
        return view('project::backend.project', compact('project_categories', 'customers', 'teams', 'projects', 'edit'));
    }
    public function InfixProjectDelete($id)
    {
        $project_tasks = InfixProjectTask::where('project_id', '=', $id)->get();
        DB::beginTransaction();
        try {
            foreach ($project_tasks as $project_task) {
                $delete_task = InfixProjectTask::where('id', '=', $project_task->id)->first();
                $delete_task->delete();
            }

            $project = InfixProject::findOrfail($id);
            $project->delete();
            DB::commit();

            Toastr::success('Operation successful', 'Success');
            return redirect()->route('InfixProjectList');
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
}

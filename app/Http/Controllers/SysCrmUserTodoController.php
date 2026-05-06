<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\SysCrmUserTodo;
use App\SysCrmUserTodoItems;
use App\SysCrmUserTodoComments;



class SysCrmUserTodoController extends Controller
{

    public function __construct()
    {
        $this->middleware('PM');
    }

    public function index(Request $request)
    {

        try {
            // Fetch all tasks
            $data['todos'] = SysCrmUserTodo::with('todoItems')->withCount('todoItems')
                ->where('user_id', Auth::id())
                ->orderByRaw("
        CASE
            WHEN deleted_at IS NULL THEN 0
            ELSE 1
        END
    ") // Active first
                ->orderByRaw("deleted_at DESC")
                ->orderByRaw("
        CASE
            WHEN status != 'completed' AND todo_due_date < NOW() THEN 0
            WHEN status != 'completed' AND todo_due_date >= NOW() THEN 1
            ELSE 2
        END
    ")
                ->orderByRaw("
        CASE priority
            WHEN 'critical' THEN 1
            WHEN 'high' THEN 2
            WHEN 'medium' THEN 3
            WHEN 'low' THEN 4
            ELSE 5
        END
    ")
                ->get();
            // $userId = Auth::id();
            // dd($data['todos']);

            $data['total_todo'] = SysCrmUserTodo::where('user_id', Auth::id())->count();
            $data['total_todo_completed'] = SysCrmUserTodo::where('user_id', Auth::id())->where('status', 'completed')->count();
            $data['total_todo_in_progress'] = SysCrmUserTodo::where('user_id', Auth::id())->where('status', 'in_progress')->count();
            $data['total_todo_not_started'] = SysCrmUserTodo::where('user_id', Auth::id())->where('status', 'not_started')->count();
            $data['total_todo_overdue'] = SysCrmUserTodo::where('user_id', Auth::id())->whereDate('todo_due_date', '<', Carbon::now('+04:00')->toDateString())->where('status','!=', 'completed')->count();
            $data['total_todo_due_today'] = SysCrmUserTodo::where('user_id', Auth::id())->whereDate('todo_due_date', Carbon::now('+04:00')->toDateString())->where('status','!=', 'completed')->count();







            return view('backEnd.execution-desk.todo-list.index', $data);

        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function create()
    {
        // Show form to create a new task   
    }

    public function store(Request $request)
    {

        // Handle POST: Save new task
        try {
            $request->validate([
                'todo_title' => 'required|string|max:255',
                'priority' => 'required|in:low,medium,high,critical',
                'todo_due_date' => 'required',
                'todo' => 'required|array|min:1',
                'todo.*' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'attachment' => 'nullable|file',
            ]);

            DB::beginTransaction();

            $attachment = "";
            if ($request->file('attachment') != "") {
                $file1 = $request->file('attachment');
                $attachment = md5(time()) . "attachment." . $file1->getclientoriginalextension();
                $file1->move('public/uploads/crm_user_todos/', $attachment);
            }


            // Insert main task
            $todoData = [
                'todo_title' => $request['todo_title'],
                'attachment' => $attachment,
                'todo_due_date' => $request['todo_due_date']
                    ? Carbon::createFromFormat('d/m/Y h:i A', $request['todo_due_date'])->format('Y-m-d H:i:s')
                    : null,
                'priority' => $request['priority'],
                'status' => 'not_started',
                'user_id' => Auth::id(),
                'description' => $request['description'],
                'created_at' => Carbon::now('+04:00'),
                'updated_at' => Carbon::now('+04:00'),
            ];



            $mainTodoId = DB::table('sys_crm_user_todos')->insertGetId($todoData);


            // Insert sub tasks
            $subTodos = [];
            foreach ($request['todo'] as $todoItem) {
                if (!empty($todoItem)) {
                    $subTodos[] = [
                        'todo_id' => $mainTodoId,
                        'todo' => $todoItem,
                        'status' => 'not_started',
                        'created_at' => Carbon::now('+04:00'),
                        'updated_at' => Carbon::now('+04:00'),
                    ];
                }
            }

            if (!empty($subTodos)) {
                DB::table('sys_crm_user_todo_items')->insert($subTodos);
            }


            DB::commit();

            Toastr::success('Todo created successfully', 'Success');
            return redirect()->back();

        } catch (\Throwable $th) {
            dd($th);
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function show($id)
    {
        // Show single task
    }

    public function edit($id)
    {

        // Show form to edit task
        $data['todo'] = SysCrmUserTodo::with('todoItems')->findOrFail($id);

        if (Auth::id() !== (int) $data['todo']->user_id) {
            Toastr::error('Unauthorized', 'Failed');
            return redirect()->back();
        }

        return response()->json($data);

    }

    public function update(Request $request)
    {
        // Handle PUT/PATCH: Update task

   

        try {
            $request->validate([
                'edit_todo_id' => 'required|exists:sys_crm_user_todos,id',
                'edit_todo_title' => 'required|string|max:255',
                'edit_priority' => 'required|in:low,medium,high,critical',
                'edit_todo_due_date' => 'required',
                'edit_todo' => 'required|array|min:1',
                'edit_todo.*' => 'nullable|string|max:255',
                'edit_description' => 'nullable|string',
                'edit_attachment' => 'nullable|file',
            ]);


            DB::beginTransaction();

            // Get current attachment value
            $todo = DB::table('sys_crm_user_todos')->where('id', $request->edit_todo_id)->first();

            $attachment = $todo->attachment;



            // Handle new attachment
            if ($request->hasFile('edit_attachment')) {
                $file = $request->file('edit_attachment');
                $attachment = md5(time()) . "attachment." . $file->getClientOriginalExtension();
                $file->move('public/uploads/crm_user_todos/', $attachment);
            }

            // Update the main task
            DB::table('sys_crm_user_todos')->where('id', $request->edit_todo_id)->update([
                'todo_title' => $request->edit_todo_title,
                'priority' => $request->edit_priority,
                'todo_due_date' => $request->edit_todo_due_date ? Carbon::createFromFormat('d/m/Y h:i A', $request->edit_todo_due_date)->format('Y-m-d H:i:s')
                    : null,
                'attachment' => $attachment,
                'description' => $request->edit_description,
                'user_id' => Auth::id(),
                'updated_at' => Carbon::now('+04:00'),
            ]);

            // Delete existing sub-items
            DB::table('sys_crm_user_todo_items')->where('todo_id', $request->edit_todo_id)->delete();

            // Insert updated sub-items
            $subTodos = [];
            foreach ($request->edit_todo as $item) {
                if (!empty($item)) {
                    $subTodos[] = [
                        'todo_id' => $request->edit_todo_id,
                        'todo' => $item,
                        'status' => 'not_started',
                        'created_at' => Carbon::now('+04:00'),
                        'updated_at' => Carbon::now('+04:00'),
                    ];
                }
            }

            if (!empty($subTodos)) {
                DB::table('sys_crm_user_todo_items')->insert($subTodos);
            }

            DB::commit();

            Toastr::success('Todo created successfully', 'Success');
            return redirect()->back();

        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th);
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }


    }

    public function todoProgressUpdate(Request $request)
    {

        // Handle PUT/PATCH: Update task progress
        try {

            $todo = SysCrmUserTodo::findOrFail($request->todo_id);


            if (Auth::id() !== (int) $todo->user_id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $request->validate([
                'todo_id' => 'required|exists:sys_crm_user_todos,id',
                'todo_item_id' => 'nullable|exists:sys_crm_user_todo_items,id',
                'status' => 'required|in:not_started,in_progress,completed',
                'comment' => 'nullable|string|max:1000',
            ]);


            $todo_item_id = is_numeric($request->todo_item_id) ? (int) $request->todo_item_id : null;
            $todo_id = $request->todo_id;

            DB::beginTransaction();

            if (empty($todo_item_id)) {
                SysCrmUserTodo::where('id', $todo_id)
                    ->update([
                        'status' => $request['status'],
                        'updated_at' => Carbon::now('+04:00'),
                    ]);
            } else {
                SysCrmUserTodoItems::where('id', $todo_item_id)
                    ->update([
                        'status' => $request['status'],
                        'updated_at' => Carbon::now('+04:00'),
                    ]);
            }


            // ✅ Check if all sub-tasks are completed
            $remaining = SysCrmUserTodoItems::where('todo_id', $todo_id)
                ->where('status', '!=', 'completed')
                ->count();





            // // If comment is provided, insert it
            $statusText = ucfirst(str_replace('_', ' ', $request->status)); // e.g., "in_progress" => "In progress"
            $commentText = trim($request->comment);
            $fullComment = "[Status: $statusText]";

            if (!empty($commentText)) {
                $fullComment .= " $commentText";
            }

           

            SysCrmUserTodoComments::create([
                'todo_id' => $todo_id,
                'todo_item_id' => $todo_item_id ?? null,
                'user_id' => Auth::id(),
                'comment' => $fullComment,
                'status' => $request->status,
            ]);


            // If all sub-tasks are completed, update the main task status
            if ($remaining === 0) {
                SysCrmUserTodo::where('id', $todo_id)
                    ->update([
                        'status' => 'completed',
                        'updated_at' => Carbon::now('+04:00'),
                    ]);


                if (empty($todo_item_id)) {
                    SysCrmUserTodo::where('id', $todo_id)
                        ->update([
                            'status' => $request['status'],
                            'updated_at' => Carbon::now('+04:00'),
                        ]);
                }
            } else if (empty($todo_item_id) && $request['status'] == 'completed') {
                SysCrmUserTodo::where('id', $todo_id)
                    ->update([
                        'status' => 'completed',
                        'updated_at' => Carbon::now('+04:00'),
                    ]);

                SysCrmUserTodoItems::where('todo_id', $todo_id)
                    ->update([
                        'status' => 'completed',
                        'updated_at' => Carbon::now('+04:00'),
                    ]);

            } else {
                SysCrmUserTodo::where('id', $todo_id)
                    ->update([
                        'status' => 'in_progress',
                        'updated_at' => Carbon::now('+04:00'),
                    ]);
            }

            DB::commit();

            Toastr::success('Todo status updated successfully', 'Success');
            return redirect()->back();

        } catch (\Throwable $th) {
            dd($th);
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function addComment(Request $request, $todo_id)
    {
        // dd($request->all());
        try {


            $todo = SysCrmUserTodo::findOrFail($todo_id);


            if (Auth::id() !== (int) $todo->user_id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $request->validate([
                'comment' => 'required|string|max:1000',
                'todo_item_id' => 'nullable|integer|exists:sys_crm_user_todo_items,id'
            ]);

            $todo_item_id = $request->todo_item_id;

            if (empty($todo_item_id)) {
                $todo_item_id = null;
            }


            $comment = SysCrmUserTodoComments::create([
                'todo_id' => $todo_id,
                'todo_item_id' => $todo_item_id, // Can be null
                'user_id' => Auth::id(), // Authenticated user
                'comment' => $request->comment,
                'status' => $todo->status // Default status
            ]);

            $comment->load('user:id,user_id,full_name'); // Load user relation

            return response()->json($comment, 201);

        } catch (\Throwable $th) {
            dd($th);
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }

    }

    public function getComments(Request $request, $todo_id)
    {
        try {
            // Fetch task
            $todo = SysCrmUserTodo::findOrFail($todo_id);


            if (Auth::id() !== (int) $todo->user_id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }


            // Optional sub-task (task_item) ID
            $todoItemId = $request->input('todo_item_id');

            $comments = SysCrmUserTodoComments::where('todo_id', $todo_id)
                // ->when($todoItemId !== null && $todoItemId !== '', function ($query) use ($todoItemId) {
                //     $query->where('todo_item_id', $todoItemId);
                // }, function ($query) {
                //     $query->whereNull('todo_item_id');
                // })
                // assuming relation with todo_items
                ->with(['user:id,user_id,full_name', 'todo:id,todo_title', 'todoItem:id,todo']) // assuming relation with user (sm_staffs)
                ->get();



            return response()->json($comments);

        } catch (\Throwable $th) {

            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function deleteMainTodo(Request $request, $todo_id)
    {
        try {
            $todo = SysCrmUserTodo::findOrFail($todo_id);
            if (Auth::id() !== (int) $todo->user_id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            DB::beginTransaction();
            // SysCrmUserTodoItems::where('todo_id', $todo->id)->delete();
            $todo->deleted_at = Carbon::now();
            $todo->save();

            $fullComment = "[Status: Deleted]";


            SysCrmUserTodoComments::create([
                'todo_id' => $todo_id,
                'todo_item_id' => null,
                'user_id' => Auth::id(),
                'comment' => $fullComment,
            ]);

            DB::commit();
            Toastr::success('Todo deleted successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function restoreMainTodo(Request $request, $todo_id)
    {
        try {
            $todo = SysCrmUserTodo::findOrFail($todo_id);
            if (Auth::id() !== (int) $todo->user_id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            DB::beginTransaction();
            $todo->deleted_at = null;
            $todo->save();

            $fullComment = "[Status: Restored]";


            SysCrmUserTodoComments::create([
                'todo_id' => $todo_id,
                'todo_item_id' => null,
                'user_id' => Auth::id(),
                'comment' => $fullComment,
            ]);

            DB::commit();
            Toastr::success('Todo restored successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function deleteSubTodo(Request $request, $todo_id, $todo_item_id)
    {
        try {
            $todo = SysCrmUserTodo::findOrFail($todo_id);
            if (Auth::id() !== (int) $todo->user_id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            DB::beginTransaction();
            SysCrmUserTodoItems::where('id', $todo_item_id)->delete();
            DB::commit();
            Toastr::success('Task deleted successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

}
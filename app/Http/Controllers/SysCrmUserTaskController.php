<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use App\SysHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\SysCrmUserTask;
use App\SmStaff;
use App\SysCrmUserTaskItems;

use App\SysCrmUserTaskComments;



class SysCrmUserTaskController extends Controller
{

    public function __construct()
    {
        $this->middleware('PM');
    }

    public function index(Request $request, $status = 'all', $id = null)
    {


        try {
            $userId = Auth::id();

            $data['staff_list'] = SmStaff::select('user_id', 'full_name')->where('active_status', 1)->orderby('first_name', 'asc')->get();


            $data['taskCounts'] = SysCrmUserTask::selectRaw("
        SUM(CASE WHEN status_s = 'approved' THEN 1 ELSE 0 END) as completed_count,
        SUM(CASE WHEN status_s != 'approved' THEN 1 ELSE 0 END) as pending_count")
                ->where('assigned_to', $userId)
                ->first();

            $data['taskCounts_assigned_by'] = SysCrmUserTask::selectRaw("
        SUM(CASE WHEN status_s = 'approved' THEN 1 ELSE 0 END) as completed_count_assigned_by,
        SUM(CASE WHEN status_s != 'approved' THEN 1 ELSE 0 END) as pending_count_assigned_by")
                ->where('assigned_by', $userId)
                ->first();


            $query = SysCrmUserTask::with([
                'assignedby:id,user_id,full_name',
                'taskitems' => function ($q) {
                    $q->withCount([
                        'comments as unread_comments_count' => function ($query) {
                            $query->where('is_read_receiver', 0);
                        }
                    ]);
                }
            ])
                ->withCount([
                    'comments as unread_main_comments_count' => function ($query) {
                        $query->where('is_read_receiver', 0);
                    }
                ])
                ->withCount('taskitems')
                ->where('assigned_to', $userId);


            // Apply status filter
            if ($status === 'pending') {
                $query->where('status_s', '!=', 'approved');
            } elseif ($status === 'completed') {
                $query->where('status_s', 'approved');
            }

            // Ordering logic preserved
            $query->orderByRaw("task_due_date IS NULL, task_due_date ASC")
                ->orderByRaw("
                CASE status_s
                    WHEN 'open' THEN 1
                    WHEN 'review' THEN 2
                    WHEN 'modification' THEN 3
                    WHEN 'approved' THEN 4
                    WHEN 'rejected' THEN 5
                    ELSE 6
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
                ->orderByRaw("
                CASE status_r
                    WHEN 'not_started' THEN 1
                    WHEN 'in_progress' THEN 2
                    WHEN 'blocked' THEN 3
                    WHEN 'cancelled' THEN 4
                    WHEN 'completed' THEN 5
                    ELSE 6
                END
            ");

            // Use pagination instead of get()
            $data['assigned_tasks'] = $query->get();

            // Pass current status to view for UI highlighting/filtering
            $data['current_status'] = $status;

            $data['active_id'] = $id;


            if ($id) {
                $data['selectedTask'] = $this->getTaskData($id);
            } else {
                $firstRecord = $data['assigned_tasks']->first();
                if ($firstRecord) {
                    $data['active_id'] = $firstRecord->id;
                    $data['selectedTask'] = $this->getTaskData($firstRecord->id);
                }
            }



            return view('backEnd.crm-tasks.index', $data);
        } catch (\Throwable $th) {
            dd($th);
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }




    public function store(Request $request)
    {
        try {
            $request->validate([
                'task_title' => 'required|string|max:255',
                'priority' => 'required|in:low,medium,high,critical',
                'assigned_to' => 'required',
                'task_due_date' => 'required',
                'task' => 'required|array|min:1',
                'task.*' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'attachment' => 'nullable|file',
            ]);

            DB::beginTransaction();

            $attachment = "";
            if ($request->file('attachment') != "") {
                $file1 = $request->file('attachment');
                $attachment = md5(time()) . "attachment." . $file1->getclientoriginalextension();
                $file1->move('public/uploads/crm_user_tasks/', $attachment);
            }

            $company_id = session('logged_session_data.company_id');

            $task_id = SysHelper::get_new_code('sys_crm_user_tasks', 'TA', 'task_id');



            // Insert main task
            $taskData = [
                'assigned_to' => $request['assigned_to'],
                'assigned_by' => Auth::id(),
                'company_id' => $company_id,
                'task_id' => $task_id,
                'task_title' => $request['task_title'],
                'attachment' => $attachment,
                'task_due_date' => $request->task_due_date
                    ? Carbon::createFromFormat('d/m/Y h:i A', $request->task_due_date)->format('Y-m-d H:i:s')
                    : null,
                'priority' => $request['priority'],
                'status_r' => 'not_started',
                'status_s' => 'open',
                'description' => $request['description'],
                'created_at' => Carbon::now('+04:00'),
                'updated_at' => Carbon::now('+04:00'),
            ];

            $mainTaskId = DB::table('sys_crm_user_tasks')->insertGetId($taskData);
            // Insert sub tasks
            $subTasks = [];
            foreach ($request['task'] as $taskItem) {
                if (!empty($taskItem)) {
                    $subTasks[] = [
                        'task_id' => $mainTaskId,
                        'task' => $taskItem,
                        'created_at' => Carbon::now('+04:00'),
                        'updated_at' => Carbon::now('+04:00'),
                    ];
                }
            }

            if (!empty($subTasks)) {
                DB::table('sys_crm_user_task_items')->insert($subTasks);
            }


            DB::commit();

            Toastr::success('Task created successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            dd($th);
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }


    public function getTaskData($id)
    {
        $task = SysCrmUserTask::with(['taskitems'])->findOrFail($id);

        if (Auth::id() !== (int) $task->assigned_to && Auth::id() !== (int) $task->assigned_by) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $data['task'] = $task;

        $data['staff_list'] = SmStaff::where('active_status', 1)
            ->orderby('first_name', 'asc')->get();

        return $data;
    }

    public function show($id)
    {

        $task = SysCrmUserTask::with(['taskitems'])->findOrFail($id);

        if (Auth::id() !== (int) $task->assigned_to && Auth::id() !== (int) $task->assigned_by) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $data['task'] = $task;

        $data['staff_list'] = SmStaff::where('active_status', 1)
            ->orderby('first_name', 'asc')->get();

        return view('backEnd.crm-tasks.viewtask', $data);
    }


    public function assignedByMe(Request $request, $status = 'all', $id = null)
    {



        try {

            $userId = Auth::id();
            $data['staff_list'] = SmStaff::where('active_status', 1)
                ->orderby('first_name', 'asc')->get();


            $data['taskCounts'] = SysCrmUserTask::selectRaw("
        SUM(CASE WHEN status_s = 'approved' THEN 1 ELSE 0 END) as completed_count,
        SUM(CASE WHEN status_s != 'approved' THEN 1 ELSE 0 END) as pending_count")
                ->where('assigned_by', $userId)
                ->first();

            $data['taskCounts_assigned_to'] = SysCrmUserTask::selectRaw("
        SUM(CASE WHEN status_s = 'approved' THEN 1 ELSE 0 END) as completed_count_assigned_to,
        SUM(CASE WHEN status_s != 'approved' THEN 1 ELSE 0 END) as pending_count_assigned_to")
                ->where('assigned_to', $userId)
                ->first();

            $query = SysCrmUserTask::with([
                'assignedto:id,user_id,full_name',
                'taskitems' => function ($q) {
                    $q->withCount([
                        'comments as unread_comments_count' => function ($query) {
                            $query->where('is_read_sender', 0);
                        }
                    ]);
                }
            ])
                ->withCount([
                    'comments as unread_main_comments_count' => function ($query) {
                        $query->where('is_read_sender', 0);
                    }
                ])
                ->withCount('taskitems')
                ->where('assigned_by', $userId);

            // Apply status filter
            if ($status === 'pending') {
                $query->where('status_s', '!=', 'approved');
            } elseif ($status === 'completed') {
                $query->where('status_s', 'approved');
            }

            // Ordering logic preserved
            $query->orderByRaw("task_due_date IS NULL, task_due_date ASC")
                ->orderByRaw("
                CASE status_s
                    WHEN 'open' THEN 1
                    WHEN 'review' THEN 2
                    WHEN 'modification' THEN 3
                    WHEN 'approved' THEN 4
                    WHEN 'rejected' THEN 5
                    ELSE 6
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
                ->orderByRaw("
                CASE status_r
                    WHEN 'not_started' THEN 1
                    WHEN 'in_progress' THEN 2
                    WHEN 'blocked' THEN 3
                    WHEN 'cancelled' THEN 4
                    WHEN 'completed' THEN 5
                    ELSE 6
                END
            ");

            // Use pagination instead of get()
            $data['assigned_tasks'] = $query->get();

            // Pass current status to view for UI highlighting/filtering
            $data['current_status'] = $status;

            //         $data['assigned_tasks'] = SysCrmUserTask::with(['assignedby:id,user_id,full_name', 'taskitems'])
            //             ->withCount('taskitems')
            //             ->where('assigned_by', Auth::id())
            //             ->orderByRaw("task_due_date IS NULL, task_due_date ASC")
            //             ->where('status_s', '!=', 'approved')
            //             ->orderByRaw("
            //     CASE status_s
            //         WHEN 'open' THEN 1
            //         WHEN 'review' THEN 2
            //         WHEN 'modification' THEN 3
            //         WHEN 'approved' THEN 4
            //         WHEN 'rejected' THEN 5
            //         ELSE 6
            //     END
            // ")
            //             ->orderByRaw("
            //     CASE priority
            //         WHEN 'critical' THEN 1
            //         WHEN 'high' THEN 2
            //         WHEN 'medium' THEN 3
            //         WHEN 'low' THEN 4
            //         ELSE 5
            //     END
            // ")
            //             ->orderByRaw("
            //     CASE status_r
            //         WHEN 'not_started' THEN 1
            //         WHEN 'in_progress' THEN 2
            //         WHEN 'blocked' THEN 3
            //         WHEN 'cancelled' THEN 4
            //         WHEN 'completed' THEN 5
            //         ELSE 6
            //     END
            // ")
            //             ->get();


            $data['active_id'] = $id;


            if ($id) {
                $data['selectedTask'] = $this->getTaskData($id);
            } else {
                $firstRecord = $data['assigned_tasks']->first();
                if ($firstRecord) {
                    $data['active_id'] = $firstRecord->id;
                    $data['selectedTask'] = $this->getTaskData($firstRecord->id);
                }
            }


            return view('backEnd.crm-tasks.assignedbyme', $data);
        } catch (\Throwable $th) {
            dd($th);
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function showassignedByme($id)
    {

        $task = SysCrmUserTask::with(['taskitems'])->findOrFail($id);

        if (Auth::id() !== (int) $task->assigned_to && Auth::id() !== (int) $task->assigned_by) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $data['task'] = $task;

        $data['staff_list'] = SmStaff::where('active_status', 1)
            ->orderby('first_name', 'asc')->get();

        return view('backEnd.crm-tasks.viewassignedtask', $data);
    }

    public function taskProgressUpdate(Request $request)
    {

        try {

            $task = SysCrmUserTask::findOrFail($request->task_id);


            if (Auth::id() !== (int) $task->assigned_to) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }



            $request->validate([
                'task_id' => 'required|exists:sys_crm_user_tasks,id',
                'task_item_id' => 'nullable|exists:sys_crm_user_task_items,id',
                'status_r' => 'required|in:not_started,in_progress,completed,blocked,cancelled',
                'comment' => 'nullable|string|max:1000',
            ]);

            $task_item_id = is_numeric($request->task_item_id) ? (int) $request->task_item_id : null;
            $task_id = $request->task_id;



            $attachment = "";
            if ($request->file('attachment') != "") {
                $file1 = $request->file('attachment');
                $attachment = md5(time()) . "attachment." . $file1->getclientoriginalextension();
                $file1->move('public/uploads/crm_user_tasks/', $attachment);
            }



            DB::beginTransaction();

            if (empty($task_item_id)) {
                SysCrmUserTask::where('id', $task_id)
                    ->update([
                        'status_r' => $request['status_r'],
                        'updated_at' => Carbon::now('+04:00'),
                    ]);
            } else {

                SysCrmUserTaskItems::where('id', $task_item_id)
                    ->update([
                        'status_r' => $request['status_r'],
                        'attachment' => $attachment != "" ? $attachment : DB::raw('attachment'),
                        'updated_at' => Carbon::now('+04:00'),
                    ]);
            }


            $remaining = SysCrmUserTaskItems::where('task_id', $task_id)
                ->where('status_r', '!=', 'completed')
                ->count();



            // If comment is provided, insert it

            $statusText = ucfirst(str_replace('_', ' ', $request->status_r)); // e.g., "in_progress" => "In progress"
            $commentText = trim($request->comment);
            $fullComment = "[Status: $statusText]";

            if (!empty($commentText)) {
                $fullComment .= " $commentText";
            }



            SysCrmUserTaskComments::create([
                'task_id' => $task_id,
                'task_item_id' => $task_item_id ?? null,
                'user_id' => Auth::id(),
                'comment' => $fullComment
            ]);


            if ($remaining == 0) {
                SysCrmUserTask::where('id', $task_id)
                    ->update([
                        'status_r' => 'completed',
                        'updated_at' => Carbon::now('+04:00'),
                    ]);
                if (empty($task_item_id)) {
                    SysCrmUserTask::where('id', $task_id)
                        ->update([
                            'status_r' => $request['status_r'],
                            'updated_at' => Carbon::now('+04:00'),
                        ]);
                }
            } else if (empty($task_item_id) && $request['status_r'] == 'completed') {
                SysCrmUserTask::where('id', $task_id)
                    ->update([
                        'status_r' => 'completed',
                        'updated_at' => Carbon::now('+04:00'),
                    ]);

                SysCrmUserTaskItems::where('task_id', $task_id)
                    ->update([
                        'status_r' => 'completed',
                        'updated_at' => Carbon::now('+04:00'),
                    ]);
            } else {
                SysCrmUserTask::where('id', $task_id)
                    ->update([
                        'status_r' => 'in_progress',
                        'updated_at' => Carbon::now('+04:00'),
                    ]);
            }

            DB::commit();

            Toastr::success('Task status updated successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            dd($th);
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function addComment(Request $request, $task_id)
    {
        // dd($request->all());
        try {

            // Fetch task
            $task = SysCrmUserTask::findOrFail($task_id);
            // Check if current user is authorized
            if (Auth::id() !== (int) $task->assigned_to && Auth::id() !== (int) $task->assigned_by) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $request->validate([
                'comment' => 'required|string|max:1000',
                'task_item_id' => 'nullable|integer|exists:sys_crm_user_task_items,id'
            ]);

            $task_item_id = $request->task_item_id;

            if (empty($task_item_id)) {
                $task_item_id = null;
            }

            $isSender = Auth::id() === $task->assigned_by;
            $isReceiver = Auth::id() === $task->assigned_to;


            $comment = SysCrmUserTaskComments::create([
                'task_id' => $task_id,
                'task_item_id' => $task_item_id, // Can be null
                'user_id' => Auth::id(), // Authenticated user
                'comment' => $request->comment,
                'is_read_sender' => $isSender ? 1 : 0,
                'is_read_receiver' => $isReceiver ? 1 : 0,
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

    public function getComments(Request $request, $task_id)
    {
        try {
            // Fetch task
            $task = SysCrmUserTask::findOrFail($task_id);

            // Check authorization
            if (Auth::id() !== (int) $task->assigned_to && Auth::id() !== (int) $task->assigned_by) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            // Optional sub-task (task_item) ID
            $taskItemId = $request->input('task_item_id');

            $comments = SysCrmUserTaskComments::where('task_id', $task_id)
                ->when($taskItemId !== null && $taskItemId !== '', function ($query) use ($taskItemId) {
                    $query->where('task_item_id', $taskItemId);
                })
                ->with('user:id,user_id,full_name') // assuming relation with user (sm_staffs)
                ->get();

            $isSender = Auth::id() === $task->assigned_by;
            $isReceiver = Auth::id() === $task->assigned_to;

            $updateQuery = SysCrmUserTaskComments::where('task_id', $task_id);

            if ($taskItemId !== null && $taskItemId !== '') {
                $updateQuery->where('task_item_id', $taskItemId);
            } else {
                $updateQuery->whereNull('task_item_id');
            }

            if ($isSender) {
                $updateQuery->where('is_read_sender', 0)->update(['is_read_sender' => 1]);
            }

            if ($isReceiver) {
                $updateQuery->where('is_read_receiver', 0)->update(['is_read_receiver' => 1]);
            }

            return response()->json($comments);
        } catch (\Throwable $th) {
            dd($th);
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }

    public function taskStatusUpdate(Request $request)
    {


        try {

            $task = SysCrmUserTask::findOrFail($request->task_id);


            if (Auth::id() !== (int) $task->assigned_by) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $request->validate([
                'task_id' => 'required|exists:sys_crm_user_tasks,id',
                'task_item_id' => 'nullable|exists:sys_crm_user_task_items,id',
                'status_s' => 'required|in:open,review,approved,modification,rejected',
                'comment' => 'nullable|string|max:1000',
            ]);

            $task_item_id = is_numeric($request->task_item_id) ? (int) $request->task_item_id : null;
            $task_id = $request->task_id;



            DB::beginTransaction();

            if (empty($task_item_id)) {
                SysCrmUserTask::where('id', $task_id)
                    ->update([
                        'status_s' => $request['status_s'],
                        'updated_at' => Carbon::now('+04:00'),
                    ]);
            } else {

                SysCrmUserTaskItems::where('id', $task_item_id)
                    ->update([
                        'status_s' => $request['status_s'],
                        'updated_at' => Carbon::now('+04:00'),
                    ]);
            }



            $statusText = ucfirst(str_replace('_', ' ', $request->status_s)); // e.g., "in_progress" => "In progress"
            $commentText = trim($request->comment);
            $fullComment = "[Status: $statusText]";

            if (!empty($commentText)) {
                $fullComment .= " $commentText";
            }

            SysCrmUserTaskComments::create([
                'task_id' => $task_id,
                'task_item_id' => $task_item_id ?? null,
                'user_id' => Auth::id(),
                'comment' => $fullComment
            ]);




            DB::commit();

            Toastr::success('Task status updated successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            dd($th);
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }



    public function search(Request $request)
    {
        $q = trim($request->get('query'));
        $formattedDate = null;

        if (preg_match('/\d{2}[\/\-]\d{2}[\/\-]\d{4}/', $q)) {
            $normalized = str_replace('/', '-', $q);
            $formattedDate = date('Y-m-d', strtotime($normalized));
        }

        $userId = Auth::id();

        $tasks = SysCrmUserTask::with([
            'assignedby:id,user_id,full_name',
            'taskitems' => function ($q) {
                $q->withCount([
                    'comments as unread_comments_count' => function ($query) {
                        $query->where('is_read_receiver', 0);
                    }
                ]);
            }
        ])
            ->withCount([
                'comments as unread_main_comments_count' => function ($query) {
                    $query->where('is_read_receiver', 0);
                }
            ])
            ->withCount('taskitems')
            ->where('assigned_to', $userId)
            ->when($q, function ($query) use ($q, $formattedDate) {
                $query->where(function ($qsub) use ($q) {
                    $qsub->where('task_id', 'like', "%{$q}%")
                        ->orWhere('task_title', 'like', "%{$q}%")
                        ->orWhere('priority', 'like', "%{$q}%")
                        ->orWhere('status_r', 'like', "%{$q}%")
                        ->orWhere('status_s', 'like', "%{$q}%")
                        ->orWhereHas('assignedby', function ($q1) use ($q) {
                            $q1->where('full_name', 'like', "%{$q}%");
                        });
                });

                if ($formattedDate) {
                    $query->orWhereDate('date', $formattedDate);
                }
            })
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($tasks);
    }


     public function searchMyTasks(Request $request)
    {
        $q = trim($request->get('query'));
        $formattedDate = null;

        if (preg_match('/\d{2}[\/\-]\d{2}[\/\-]\d{4}/', $q)) {
            $normalized = str_replace('/', '-', $q);
            $formattedDate = date('Y-m-d', strtotime($normalized));
        }

        $userId = Auth::id();

        $tasks = SysCrmUserTask::with([
            'assignedto:id,user_id,full_name',
            'taskitems' => function ($q) {
                $q->withCount([
                    'comments as unread_comments_count' => function ($query) {
                        $query->where('is_read_receiver', 0);
                    }
                ]);
            }
        ])
            ->withCount([
                'comments as unread_main_comments_count' => function ($query) {
                    $query->where('is_read_receiver', 0);
                }
            ])
            ->withCount('taskitems')
            ->where('assigned_by', $userId)
            ->when($q, function ($query) use ($q, $formattedDate) {
                $query->where(function ($qsub) use ($q) {
                    $qsub->where('task_id', 'like', "%{$q}%")
                        ->orWhere('task_title', 'like', "%{$q}%")
                        ->orWhere('priority', 'like', "%{$q}%")
                        ->orWhere('status_r', 'like', "%{$q}%")
                        ->orWhere('status_s', 'like', "%{$q}%")
                        ->orWhereHas('assignedto', function ($q1) use ($q) {
                            $q1->where('full_name', 'like', "%{$q}%");
                        });
                });

                if ($formattedDate) {
                    $query->orWhereDate('date', $formattedDate);
                }
            })
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($tasks);
    }

}

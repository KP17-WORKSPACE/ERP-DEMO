@extends('backEnd.masterpage')
@section('mainContent')

    <style>
        /* Chat container styles */
        #commentsContainer {
            max-height: 400px;
            overflow-y: auto;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #ddd;
            position: relative;
            word-wrap: break-word;
        }

        /* Common chat message style */
        .chat-msg {
            display: flex;
            margin-bottom: 15px;
            word-break: break-word;
        }

        /* Make sure message doesn’t overflow */
        .chat-msg .chat-msg-content {
            background: #ffffff;
            padding: 10px 15px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            max-width: calc(100% - 60px);
            position: relative;
            overflow-wrap: break-word;
            word-wrap: break-word;
            word-break: break-word;
        }

        /* Avatar */
        .chat-msg .avatar {
            width: 40px;
            height: 40px;
            background: #007bff;
            color: #fff;
            border-radius: 50%;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            line-height: 40px;
            margin-right: 10px;
            flex-shrink: 0;
        }

        /* Text styling */
        .chat-msg-content strong {
            display: block;
            font-size: 14px;
            color: #333;
        }

        .chat-msg-content small {
            font-size: 11px;
            color: #888;
        }

        .chat-msg-content p {
            margin: 5px 0 0;
            word-break: break-word;
        }

        .badge {
            border-radius: 0px;
            font-weight: normal;
        }

        .truncate-text {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        @media (max-width: 768px) {
            .truncate-text {
                max-width: 120px;
            }

            .table td,
            .table th {
                font-size: 12px;
                padding: 0.5rem;
            }
        }

        .comment-btn-wrapper {
            position: relative;
            display: inline-block;
        }

        .comment-badge {
            position: absolute;
            top: -5px;
            right: -8px;
            background: #d9534f;
            color: white;
            padding: 2px 6px;
            font-size: 10px;
            border-radius: 50%;
            font-weight: bold;
        }
    </style>


    <?php
    use Carbon\Carbon;
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>

    <?php try { ?>
    <div class="container-fluid">
        <div class="d-sm-flex justify-content-between">
            <div class="mb-3">

                <h2 class="page-heading m-0">
                    Todo List
                </h2>
                <span class="page-label">Home - Todo List</span>
            </div>
            <div>
                <a class="btn btn-primary" id="btn_add_new_todo" data-toggle="modal" data-target="#ModalAddNewTodo"
                    data-backdrop="static" data-keyboard="false"><i class="fa fa-plus"></i> Add Task</a>
                {{-- <a class="btn btn-danger" target="_blank" href="{{ url('crm-user-tasks/pending') }}">Pending Tasks
                    ({{ $taskCounts->pending_count ?? 0 }})</a>

                <a class="btn btn-success" target="_blank" href="{{ url('crm-user-tasks/completed') }}">Completed Tasks
                    ({{ $taskCounts->completed_count ?? 0 }})</a>

                <a class="btn btn-info" target="_blank" href="{{ url('crm-user-tasks/assigned-by-me/all') }}">Assigned by Me
                    ({{ $taskCounts_assigned_by->completed_count_assigned_by + $taskCounts_assigned_by->pending_count_assigned_by }})</a> --}}


            </div>
        </div>
        {{-- <div class="collapse" id="collapseExample">
            <div class="card shadow mb-4 p-4">

                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-amc-service-request-list', 'method' => 'POST', 'id' => 'crm-amc-search']) }}
                <div class="row">
                    <div class="col-md-1 mb-2">
                        <label for="" class="form-check-label">AMC ID</label>
                        <input class="form-control" id="search_amc_id" type="text" autocomplete="off"
                            name="search_amc_id">
                    </div>
                    <div class="col-md-2 mb-2">
                        <label for="" class="form-check-label">Customer Name</label>
                        <select class="form-control js-example-basic-single" name="search_customer_name"
                            id="search_customer_name">
                            <option value="">-Select-</option>
                            @foreach ($customer as $value)
        <option value="{{ @$value->id }}" @if ($ctrl_customer_name == $value->id) selected @endif>{{ @$value->name }}
        </option>
        @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label for="" class="form-check-label">Service Enginer</label>
                        <select class="form-control" name="search_service_enginer" id="search_service_enginer">
                            <option value="">Select</option>
                            @if (count($engineer_list) > 0)
        @foreach ($engineer_list as $list)
        <option value="{{ $list->user_id }}" @if ($ctrl_service_enginer == $list->user_id) selected @endif>{{
          $list->full_name }}</option>
        @endforeach
        @endif
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label for="" class="form-check-label">Service Date From</label>
                        <input class="form-control" id="search_from_date" type="date" autocomplete="off"
                            name="search_from_date" value="">
                    </div>
                    <div class="col-md-2 mb-2">
                        <label for="" class="form-check-label">Service Date To</label>
                        <input class="form-control" id="search_to_date" type="date" autocomplete="off"
                            name="search_to_date" value="">
                    </div>
                    <div class="col-md-2 mb-2">
                        <label for="" class="form-check-label">Status</label>
                        <select class="form-control" name="search_status" id="search_status">
                            <option value="">Select</option>
                            <option value="2,3" @if ($ctrl_search_status == '2,3') selected @endif>Pending</option>
        <option value="5" @if ($ctrl_search_status == '5') selected @endif>Completed</option>
                        </select>
                    </div>
                    <div class="col-md-1 mb-2">
                        <label for="" class="form-check-label">&nbsp;</label><br />
                        <button type="submit" class="btn btn-primary" id="btnSubmit">Search</button>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div> --}}

        <style>
            /* Card-like style for Bootstrap 3 */
            .task-card {
                border-radius: 6px;
                padding: 15px;
                text-align: center;
                margin-bottom: 15px;
                box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
            }

            .task-icon {
                margin-bottom: 8px;
                font-size: 24px;
                /* fa-lg equivalent */
            }

            .task-title {
                margin-bottom: 8px;
                font-weight: 600;
                font-size: 16px;
            }

            .task-count {
                font-weight: bold;
                font-size: 1.25em;
            }
        </style>

        <div class="row">
            <div class="cls-m-12 col-md-6 col-lg-3 col-xl-2">
                <div class="task-card bg-primary text-white">
                    <div><i class="fa fa-tasks task-icon"></i></div>
                    <h5 class="task-title">Total</h5>
                    <div id="totalTasks" class="task-count">{{ $total_todo }}</div>
                </div>
            </div>

            <div class="cls-m-12 col-md-6 col-lg-3 col-xl-2">

                <div class="task-card bg-warning text-dark">
                    <div><i class="fa fa-calendar task-icon"></i></div>
                    <h5 class="task-title">Due Today</h5>
                    <div id="dueToday" class="task-count">{{ $total_todo_due_today }}</div>
                </div>
            </div>

            <div class="cls-m-12 col-md-6 col-lg-3 col-xl-2">

                <div class="task-card bg-danger text-white">
                    <div><i class="fa fa-exclamation-triangle task-icon"></i></div>
                    <h5 class="task-title">Overdue</h5>
                    <div id="dueTasks" class="task-count">{{ $total_todo_overdue }}</div>
                </div>
            </div>

            <div class="cls-m-12 col-md-6 col-lg-3 col-xl-2">

                <div class="task-card bg-secondary text-white">
                    <div><i class="fa fa-clock task-icon"></i></div>
                    <h5 class="task-title">Not Started</h5>
                    <div id="notStartedCount" class="task-count">{{ $total_todo_not_started }}</div>
                </div>
            </div>

            <div class="cls-m-12 col-md-6 col-lg-3 col-xl-2">

                <div class="task-card bg-info text-white">
                    <div><i class="fa fa-spinner fa-spin task-icon"></i></div>
                    <h5 class="task-title">In Progress</h5>
                    <div id="inProgressCount" class="task-count">{{ $total_todo_in_progress }}</div>
                </div>
            </div>

            <div class="cls-m-12 col-md-6 col-lg-3 col-xl-2">

                <div class="task-card bg-success text-white">
                    <div><i class="fa fa-check-circle task-icon"></i></div>
                    <h5 class="task-title">Completed</h5>
                    <div id="completedCount" class="task-count">{{ $total_todo_completed }}</div>
                </div>
            </div>
        </div>



        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="dataTable1" width="100%" cellspacing="0">
                        <thead>
                            @if (session()->has('message-success') != '' || session()->get('message-danger') != '')
                                <tr>
                                    <td colspan="7">
                                        @if (session()->has('message-success'))
                                            <div class="alert alert-success">
                                                {{ session()->get('message-success') }}
                                            </div>
                                        @elseif(session()->has('message-danger'))
                                            <div class="alert alert-danger">
                                                {{ session()->get('message-danger') }}
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endif

                            <tr>
                                <th>#</th>
                                <th>@lang('Todo Title')</th>
                                <th>@lang('Tasks')</th>
                                <th>@lang('Created')</th>
                                <th>@lang('Deadline')</th>
                                <th>@lang('Priority')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Attachment')</th>
                                <th style="width:170px;">Action</th>
                            </tr>
                        </thead>


                        <tbody>
                            @foreach ($todos as $value)
                                <tr class="main-row  {{ $value->deleted_at ? 'bg-dark' : '' }} "
                                    onclick="toggleDetails({{ $value->id }})" style="cursor: pointer;">
                                    <td>{{ $loop->iteration }}</td>

                                    <td class="truncate-text" title="{{ $value->todo_title }}">{{ $value->todo_title }}
                                    </td>
                                    <td>
                                        <a href="#">
                                            View Tasks ({{ $value->todo_items_count }})
                                        </a>
                                    </td>



                                    <td>{{ date('d/m/Y h:i A', strtotime($value->created_at)) }}</td>
                                    <td class="{{ $value->is_overdue ? 'text-danger' : '' }}">
                                        {{ date('d/m/Y h:i A', strtotime($value->todo_due_date)) }}</td>


                                    <td>
                                        <span
                                            class="badge badge-{{ $value->priority == 'low' ? 'success' : ($value->priority == 'medium' ? 'warning' : ($value->priority == 'high' ? 'dark' : 'danger')) }}">
                                            {{ ucfirst($value->priority) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge badge-{{ $value->status === 'not_started'
                                                ? 'warning'
                                                : ($value->status === 'in_progress'
                                                    ? 'info'
                                                    : ($value->status === 'completed'
                                                        ? 'success'
                                                        : 'secondary')) }}">
                                            {{ ucwords(str_replace('_', ' ', $value->status)) }}
                                        </span>
                                    </td>

                                    <td onclick="event.stopPropagation();">
                                        @if (!empty($value->attachment))
                                            <a href="{{ asset('public/uploads/crm_user_todos/' . $value->attachment) }}"
                                                target="_blank" class="text-decoration-none text-primary"
                                                title="View Attachment">
                                                <i class="fa fa-paperclip me-1"></i> View
                                            </a>
                                        @endif
                                    </td>


                                    <td onclick="event.stopPropagation();">

                                        <a type="button" class="btn-sm btn-info update-progress-btn"
                                            data-id="{{ $value->id }}" data-status="{{ $value->status }}"
                                            data-title="{{ $value->todo_title }}"
                                            data-due="{{ date('d/m/Y h:i A', strtotime($value->todo_due_date)) }}"
                                            data-priority="{{ $value->priority }}">
                                            <i class="fa fa-edit"></i>
                                        </a>


                                        <a type="button" class="btn-sm btn-primary btn-comments comment-btn-wrapper"
                                            data-todo-id="{{ $value->id }}" data-task-title="{{ $value->todo_title }}">
                                            <i class="fa fa-comments"></i>
                                        </a>

                                        <a type="button" class="btn-sm btn-warning edit-task-btn"
                                            data-id="{{ $value->id }}">
                                            Edit
                                        </a>

                                        @if ($value->deleted_at)
                                            <form action="{{ url('user-todo-restore/' . $value->id) }}" method="POST"
                                                style="display:inline;"
                                                onsubmit="return confirm('Are you sure you want to restore this?');">
                                                {{ csrf_field() }}
                                                <button type="submit" class="btn-sm btn-success" title="Restore">
                                                    <i class="fa fa-undo"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ url('user-todo-delete/' . $value->id) }}" method="POST"
                                                style="display:inline;"
                                                onsubmit="return confirm('Are you sure you want to delete this?');">
                                                {{ csrf_field() }}
                                                {{ method_field('DELETE') }}
                                                <button type="submit" class="btn-sm btn-danger" title="Delete">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif



                                    </td>
                                </tr>

                                <!-- Expandable Row -->
                                <tr id="task-details-{{ $value->id }}" class="expand-row"
                                    style="display: none; background-color: #f9f9f9;">
                                    <td colspan="12" class="p-2">
                                        <div class="expandable-content" style="display: none;">
                                            <strong>Todo Title:</strong> {{ $value->todo_title }}<br>

                                            <strong>Task Items:</strong>
                                            <ol class="mb-1">
                                                @foreach ($value->todoItems as $task)
                                                    <li class="p-2">
                                                        {{ $task->todo }}


                                                        <span
                                                            class="badge badge-{{ $task->status === 'not_started'
                                                                ? 'warning'
                                                                : ($task->status === 'in_progress'
                                                                    ? 'info'
                                                                    : ($task->status === 'completed'
                                                                        ? 'success'
                                                                        : 'secondary')) }}">
                                                            {{ ucwords(str_replace('_', ' ', $task->status)) }}
                                                        </span>




                                                        <span type="button" class="badge badge-info update-progress-btn"
                                                            data-id="{{ $value->id }}" {{-- data-task-item-id="{{ $task->id }}" --}}
                                                            data-todo_item_id="{{ $task->id }}"
                                                            data-status="{{ $task->status }}"
                                                            data-title="{{ $task->todo }}"
                                                            data-due="{{ date('d/m/Y h:i A', strtotime($value->todo_due_date)) }}"
                                                            data-priority="{{ $value->priority }}">
                                                            <i class="fa fa-edit"></i>
                                                        </span>

                                                        <a type="button" class="badge badge-primary btn-comments"
                                                            data-todo-id="{{ $value->id }}"
                                                            data-todo-item-id="{{ $task->id }}"
                                                            data-task-title="{{ $task->todo }}">
                                                            <i class="fa fa-comments" aria-hidden="true"></i>
                                                        </a>

                                                        <a type="button" class="badge badge-danger"
                                                            onclick="return confirm('Are you sure?')"
                                                            href="{{ url('user-sub-todo-delete/' . $value->id . '/' . $task->id . '/') }}">
                                                            <i class="fa fa-trash"></i>
                                                        </a>



                                                    </li>
                                                @endforeach
                                            </ol>

                                            <strong>Description</strong>
                                            {{ $value->description }}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach



                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>






    {{-- <!-- Modal Add Task--> --}}
    <div class="modal fade" id="ModalAddNewTodo" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add a Task</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'user-todo-list', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="" class="form-label">Todo Title <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="todo_title" id="todo_title" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Todo Priority <span
                                        class="text-danger">*</span></label>
                                <select class="form-control js-example-basic-single" name="priority" id="priority"
                                    required>
                                    <option value="">-Select-</option>
                                    <option value="critical">Critical</option>
                                    <option value="high">High</option>
                                    <option value="medium">Medium</option>
                                    <option value="low">Low</option>
                                </select>
                            </div>
                        </div>




                        @php

                            $defaultDateTime = Carbon::now()->addHours(2)->format('Y-m-d\TH:i');
                        @endphp
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Due Date & Time <span
                                        class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" name="todo_due_date"
                                    id="todo_due_date" required min="{{ now()->format('Y-m-d\T00:00') }}"
                                    value="{{ $defaultDateTime }}" onchange="">
                            </div>
                        </div>


                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Attachment</label>
                                <input type="file" class="form-control" name="attachment" id="attachment">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Tasks <span class="text-danger">*</span></label>
                                {{-- <a onclick="add_task()" class="btn-sm btn-primary float-right"><i class="fa fa-plus-square"
            aria-hidden="true"></i></a> --}}

                                <button type="button" class="btn btn-sm btn-info btn-add-task  float-right"
                                    onclick="add_task()">
                                    <i class="fa fa-plus"></i> Add Task
                                </button>

                                <table width="100%">
                                    <tr>
                                        <td width="1%">1. </td>
                                        <td><input type="text" required class="form-control" name="todo[]"
                                                id="todo_1">
                                        </td>
                                    </tr>
                                    @for ($i = 2; $i <= 20; $i++)
                                        <tr id="row_{{ $i }}" style="display:none;">
                                            <td>{{ $i }}. </td>
                                            <td><input type="text" class="form-control" name="todo[]"
                                                    id="todo_{{ $i }}"></td>
                                        </tr>
                                    @endfor
                                </table>




                                <input type="hidden" id="todo_row_id" value="1" />
                                <script>
                                    function add_task() {
                                        var scope = $('#todo_row_id').val();

                                        scope++;
                                        $('#row_' + scope).css('display', '');
                                        $('#todo_row_id').val(scope);
                                        // $('#task_' + scope).prop("required", true);

                                    }
                                </script>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Description</label>
                                <textarea class="form-control" name="description" id="description" rows="4"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Task</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    {{-- <!-- Modal Add Task--> --}}

    {{-- <!-- Modal Edit Task--> --}}
    <div class="modal fade" id="ModalEditTodo" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Task</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-todo-update', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}

                <input type="hidden" name="edit_todo_id" id="edit_todo_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="" class="form-label">Todo Title <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="edit_todo_title" id="edit_todo_title"
                                    required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Todo Priority <span
                                        class="text-danger">*</span></label>
                                <select class="form-control js-example-basic-single" name="edit_priority"
                                    id="edit_priority" required>
                                    <option value="">-Select-</option>
                                    <option value="critical">Critical</option>
                                    <option value="high">High</option>
                                    <option value="medium">Medium</option>
                                    <option value="low">Low</option>
                                </select>
                            </div>
                        </div>





                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Due Date & Time <span
                                        class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" name="edit_todo_due_date"
                                    id="edit_todo_due_date" required min="{{ now()->format('Y-m-d\T00:00') }}"
                                    onchange="">
                            </div>
                        </div>


                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Attachment</label>
                                <input type="file" class="form-control" name="edit_attachment" id="edit_attachment">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Tasks <span class="text-danger">*</span></label>
                                {{-- <a onclick="add_task()" class="btn-sm btn-primary float-right"><i class="fa fa-plus-square"
            aria-hidden="true"></i></a> --}}

                                <button type="button" class="btn btn-sm btn-info btn-add-task  float-right"
                                    onclick="add_edit_task()">
                                    <i class="fa fa-plus"></i> Add Task
                                </button>

                                <table width="100%">
                                    <tr>
                                        <td width="1%">1. </td>
                                        <td><input type="text" required class="form-control" name="edit_todo[]"
                                                id="edit_todo_1">
                                        </td>
                                        <td width="5%">
                                            <button type="button" class="btn btn-danger btn-sm remove-edit-task"
                                                onclick="remove_edit_task(1)">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @for ($i = 2; $i <= 20; $i++)
                                        <tr id="edit_row_{{ $i }}" style="display:none;">
                                            <td>{{ $i }}. </td>
                                            <td><input type="text" class="form-control" name="edit_todo[]"
                                                    id="edit_todo_{{ $i }}"></td>
                                            <td width="5%">
                                                <button type="button" class="btn btn-danger btn-sm remove-edit-task"
                                                    onclick="remove_edit_task({{ $i }})">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endfor
                                </table>




                                <input type="hidden" id="edit_todo_row_id" value="1" />
                                <script>
                                    function add_edit_task() {
                                        var scope = $('#edit_todo_row_id').val();

                                        scope++;
                                        $('#edit_row_' + scope).css('display', '');
                                        $('#edit_todo_row_id').val(scope);
                                        // $('#task_' + scope).prop("required", true);

                                    }

                                    function remove_edit_task(index) {
                                        // Clear the input and hide the row
                                        $('#edit_todo_' + index).val('');
                                        $('#edit_row_' + index).hide();
                                    }
                                </script>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Description</label>
                                <textarea class="form-control" name="edit_description" id="edit_description" rows="4"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Task</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    {{-- <!-- Modal Add Task--> --}}




    {{-- <!-- Modal Update Task--> --}}
    <div class="modal fade" id="ModalTodoUpdate" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Update Task Progress</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-todo-progress-update', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}

                <input type="hidden" name="todo_id" id="todo_id">
                <input type="hidden" name="todo_item_id" id="todo_item_id">
                <div class="modal-body">
                    <!-- Task Summary -->
                    <div class="panel panel-default" style="border-radius: 8px;">

                        <div class="panel-body" style="padding: 15px;">
                            <div class="row">
                                <div class="col-md-6">

                                    <p><strong>Title:</strong> <span id="modal_todo_title"></span></p>

                                    <p><strong>Priority:</strong>
                                        <span id="modal_todo_priority" class="badge text-capitalize"></span>
                                    </p>

                                </div>
                                <div class="col-md-6">
                                    <p><strong>Due Date:</strong> <span id="modal_todo_due_date"
                                            class="text-danger"></span></p>
                                    <p><strong>Status:</strong>
                                        <span id="modal_todo_status" class="badge  text-capitalize"></span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Update Section -->
                    <div class="form-group">
                        <label for="status" class="control-label">
                            <i class="fa fa-line-chart text-success"></i> Update Progress <span
                                class="text-danger">*</span>
                        </label>
                        <select name="status" id="status" class="form-control input-sm" required>
                            <option value="">-- Select Status --</option>
                            <option value="not_started">Not Started</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>

                        </select>
                    </div>


                    <div class="form-group">
                        <label for="comment" class="control-label">
                            <i class="fa fa-line-chart text-success"></i> Comment
                        </label>
                        <textarea name="comment" class="form-control" cols="10" rows="3" placeholder="Write your comment..."></textarea>
                    </div>




                </div>

                <div class="modal-footer">

                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    {{-- <!-- Modal Update Task--> --}}

    <div class="modal fade" id="ModalTodoComments" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document" style="min-width:50% !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Task Comments</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                <style>
                    .custom-table th,
                    .custom-table td {
                        vertical-align: middle !important;
                        padding: 10px !important;
                    }
                </style>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 custom-table-container">
                            <div class="table-responsive">
                                <table class="table custom-table  table-striped table-bordered align-middle text-nowrap">
                                    <thead class="">
                                        <tr>
                                            <th style="width: 70%">
                                                <i class="fa fa-comment-alt text-primary"></i> Comment
                                            </th>
                                            <th>
                                                <i class="fa fa-tasks text-success"></i> Status
                                            </th>
                                            <th>
                                                <i class="fa fa-clock text-info"></i> Updated Time
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="commentsContainer">

                                        <tr id="no-comments">
                                            <td colspan="3" class="text-muted text-center">No comments yet.</td>
                                        </tr>


                                        <!-- Comment Row -->
                                        {{-- <tr>
                                            <td style="white-space: normal; word-break: break-word;">
                                                <span>
                                                    Lorem ipsum, dolor sit amet consectetur adipisicing elit. Dolor,
                                                    cupiditate! Doloribus, ullam.
                                                    Consequatur provident perferendis maiores tempora harum odit rem,
                                                    quibusdam minus illo qui
                                                    assumenda amet aliquid. Inventore, quasi illum!
                                                </span>
                                            </td>

                                            <td>
                                                <span class="badge badge-success">Completed</span>
                                            </td>
                                            <td>
                                                <span>20/05/2025 10:09 AM</span>
                                            </td>
                                        </tr> --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>



                    {{-- <div id="commentsContainer" style="max-height: 400px; overflow-y: auto;"></div> --}}
                    <textarea id="newComment" class="form-control mt-3" cols="10" rows="3"
                        placeholder="Write your comment..."></textarea>
                    <button id="submitComment" class="btn btn-primary mt-2 pull-right">Send</button>

                    <button class="btn btn-danger mt-2 pull-right" type="button" data-dismiss="modal">Cancel</button>

                    <input type="hidden" id="currentTodoId" />
                    <input type="hidden" id="todo_item_id" />
                    <input type="hidden" id="task_title" />


                </div>

                {{-- <div class="modal-footer">
                    <button class="btn-small" type="button" data-dismiss="modal">Cancel</button>
                    <button type="button" class=" btn-small" name="btnSubmit1" id="btnSubmit1">Add Remark</button>
                </div> --}}

                <div class="row">

                    <div id="mydiv" style="margin-left:30px;">
                    </div>
                </div>


            </div>
        </div>
    </div>
    {{-- <!-- Modal Deal Track--> --}}




    <script>
        $(document).ready(function() {
            $('.update-progress-btn').click(function() {
                $('#todo_id').val($(this).data('id'));
                $('#todo_item_id').val($(this).data('todo_item_id')) || null;
                $('#modal_todo_id_text').text($(this).data('task_id'));
                $('#modal_todo_title').text($(this).data('title'));
                $('#status').val($(this).data('status'));


                var priority = $(this).data('priority'); // e.g., 'low', 'medium', 'high', 'critical'
                var $prioritySpan = $('#modal_todo_priority');

                var status = $(this).data('status'); // e.g., 'not_started', 'in_progress', 'completed'
                var $statusSpan = $('#modal_todo_status');

                $statusSpan.removeClass('badge badge-warning badge-info badge-success');


                console.log('Status:', status);
                switch (status) {
                    case 'not_started':
                        $statusSpan.addClass('badge badge-warning').text('Not Started');
                        break;
                    case 'in_progress':
                        $statusSpan.addClass('badge badge-info').text('In Progress');
                        break;
                    case 'completed':
                        $statusSpan.addClass('badge badge-success').text('Completed');
                        break;
                    default:
                        $statusSpan.text(status); // Fallback text
                }

                // Clear previous badge classes
                $prioritySpan.removeClass('badge badge-success badge-warning badge-dark badge-danger');

                // Add new class and text based on priority
                switch (priority) {
                    case 'low':
                        $prioritySpan.addClass('badge badge-success').text('Low');
                        break;
                    case 'medium':
                        $prioritySpan.addClass('badge badge-warning').text('Medium');
                        break;
                    case 'high':
                        $prioritySpan.addClass('badge badge-dark').text('High');
                        break;
                    case 'critical':
                        $prioritySpan.addClass('badge badge-danger').text('Critical');
                        break;
                    default:
                        $prioritySpan.text(priority); // Fallback text
                }

                $('#modal_todo_due_date').text($(this).data('due'));
                $('#modal_todo_priority').text($(this).data('priority'));
                $('#ModalTodoUpdate').modal('show');
            });
        });


        $('.btn-comments').on('click', function() {
            var todoId = $(this).data('todo-id');
            var todoItemId = $(this).data('todo-item-id') || '';
            $('#todo_item_id').val(todoItemId);

            $('#currentTodoId').val(todoId);

            $('#task_title').val($(this).data('task-title'));

            $('#commentsContainer').html('<p>Loading comments...</p>');
            $('#ModalTodoComments').modal('show');

            $(this).find('.comment-badge').remove();


            $.ajax({
                url: '/crm-user-todo/' + todoId + '/comments',
                type: 'GET',
                data: {
                    todo_item_id: todoItemId
                },
                success: function(comments) {
                    $('#commentsContainer').html('');
                    if (comments.length === 0) {
                        $('#commentsContainer').html(
                            '<tr id="no-comments"><td colspan="3" class="text-center text-muted">No comments yet.</td></tr>'
                        );
                    } else {
                        comments.forEach(function(comment) {

                            console.log(comment);

                            const TodoTitle = comment.todo_item_id === null ?
                                `${comment.todo.todo_title}` :
                                `${comment.todo_item.todo}`;

                            const timeFormatted = new Date(comment.created_at).toLocaleString(
                                'en-US', {
                                    day: '2-digit',
                                    month: '2-digit',
                                    year: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    hour12: true
                                });


                            let badgeClass = '';
                            switch (comment.status) {
                                case 'not_started':
                                    badgeClass = 'warning';
                                    break;
                                case 'in_progress':
                                    badgeClass = 'info';
                                    break;
                                case 'completed':
                                    badgeClass = 'success';
                                    break;
                                default:
                                    badgeClass = 'secondary';
                            }

                            $('#commentsContainer').append(`
                        <tr>
                            <td style="white-space: normal; word-break: break-word;">
                                            <strong>Task Title:</strong> ${TodoTitle} <br>
                                
                                ${comment.comment}
                                </td>
                            <td>
               <span class="badge badge-${badgeClass} text-capitalize">${comment.status.replace('_', ' ')}</span>
                            </td>
                            <td>${timeFormatted}</td>
                        </tr>
                    `);
                        });


                    }
                }
            });


        });

        $('#submitComment').on('click', function() {
            var commentText = $('#newComment').val().trim();
            var todoId = $('#currentTodoId').val();
            var task_title = $('#task_title').val();
            var todoItemId = $('#todo_item_id').val() || null;

            if (!commentText) return alert("Comment cannot be empty.");

            $.ajax({
                url: '/crm-user-todo/' + todoId + '/comments',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    comment: commentText,
                    todo_item_id: todoItemId
                },

                success: function(newComment) {
                    $('#newComment').val('');
                    const timeFormatted = new Date(newComment.created_at).toLocaleString(
                        'en-US', {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: true
                        });


                    let badgeClass = '';
                    switch (newComment.status) {
                        case 'not_started':
                            badgeClass = 'warning';
                            break;
                        case 'in_progress':
                            badgeClass = 'info';
                            break;
                        case 'completed':
                            badgeClass = 'success';
                            break;
                        default:
                            badgeClass = 'secondary';
                    }
                    $('#commentsContainer').append(`
                        <tr>
                            <td style="white-space: normal; word-break: break-word;">
                                            <strong>Task Title:</strong> ${task_title} <br>
                                
                                ${newComment.comment}
                                </td>
                            <td>
               <span class="badge badge-${badgeClass} text-capitalize">${newComment.status.replace('_', ' ')}</span>
                            </td>
                            <td>${timeFormatted}</td>
                        </tr>
                    `);

                    $('#no-comments').remove();

                    // Scroll to bottom after appending
                    $('#commentsContainer').scrollTop($('#commentsContainer')[0].scrollHeight);
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", xhr);
                    alert("Error: " + xhr.responseJSON?.message || error);
                }
            });
        });

        $('.edit-task-btn').click(function() {
            $("#loading_bg").css("display", "block");

            var todoId = $(this).data('id');

            $.ajax({
                url: '/user-todo-list/' + todoId + '/edit',
                method: 'GET',
                success: function(res) {


                    console.log(res.todo.priority)
                    // Fill modal fields
                    $('#edit_todo_id').val(todoId);
                    $('#edit_todo_title').val(res.todo.todo_title);
                    $('#edit_priority').val(res.todo.priority).trigger('change');;
                    $('#edit_todo_due_date').val(res.todo.todo_due_date);
                    $('#edit_description').val(res.todo.description);

                    // Fill tasks
                    var todoArr = res.todo.todo_items || [];
                    for (var i = 1; i <= 20; i++) {
                        $('#edit_todo_' + i).val('');
                        $('#edit_row_' + i).hide();
                    }

                    todoArr.forEach((val, index) => {
                        console.log(val)
                        $('#edit_todo_' + (index + 1)).val(val.todo);
                        $('#edit_row_' + (index + 1)).show();
                        $('#edit_todo_row_id').val(index + 1);
                    });


                    // Show modal
                    $('#ModalEditTodo').modal('show');
                    $("#loading_bg").css("display", "none");

                }
            });
        });

        function toggleDetails(id) {
            const tr = $('#task-details-' + id);
            const content = tr.find('.expandable-content');

            if (tr.is(':visible')) {
                content.slideUp(200, function() {
                    tr.hide(); // hide the tr after sliding up the content
                });
            } else {
                tr.show(); // show the tr first
                content.slideDown(200);
            }
        }
    </script>



    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

@endsection

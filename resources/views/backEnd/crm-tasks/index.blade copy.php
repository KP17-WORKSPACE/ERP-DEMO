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
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>

    <?php try { ?>
    <div class="container-fluid">
        <div class="d-sm-flex justify-content-between">
            <div class="mb-3">

                <h2 class="page-heading m-0">
                    @if ($current_status == 'pending')
                        My Tasks - Pending ({{ $taskCounts->pending_count ?? 0 }})
                    @elseif ($current_status == 'completed')
                        My Tasks - Completed ({{ $taskCounts->completed_count ?? 0 }})
                    @else
                        My Tasks List ({{ $taskCounts->completed_count + $taskCounts->pending_count }})
                    @endif
                </h2>
                <span class="page-label">Home - Task List</span>
            </div>
            <div>
                <a class="btn btn-primary" id="btn_add_new_amc" data-toggle="modal" data-target="#ModalAddNewTask"
                    data-backdrop="static" data-keyboard="false"><i class="fa fa-plus"></i> New Task</a>
                <a class="btn btn-danger" target="_blank" href="{{ url('crm-user-tasks/pending') }}">Pending Tasks
                    ({{ $taskCounts->pending_count ?? 0 }})</a>

                <a class="btn btn-success" target="_blank" href="{{ url('crm-user-tasks/completed') }}">Completed Tasks
                    ({{ $taskCounts->completed_count ?? 0 }})</a>

                <a class="btn btn-info" target="_blank" href="{{ url('crm-user-tasks/assigned-by-me/all') }}">Assigned by Me
                    ({{ $taskCounts_assigned_by->completed_count_assigned_by + $taskCounts_assigned_by->pending_count_assigned_by }})</a>

                {{-- <button type="button" class="btn btn-warning" data-toggle="collapse" href="#collapseExample" role="button"
                    aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-filter mr-1"></i>Search</button> --}}
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
                                <th>@lang('Task ID')</th>
                                <th>@lang('Task Title')</th>
                                <th>@lang('Tasks')</th>
                                <th>@lang('Assigned By')</th>
                                <th>@lang('Assigned Date & Time')</th>
                                <th>@lang('Due Date & Time')</th>
                                <th>@lang('Attachment')</th>
                                <th>@lang('Priority')</th>
                                <th>@lang('Progress')</th>
                                <th>@lang('Status')</th>
                                <th style="width:150px;">Action</th>
                            </tr>
                        </thead>


                        <tbody>
                            @foreach ($assigned_tasks as $value)
                                <tr class="main-row" onclick="toggleDetails({{ $value->id }})" style="cursor: pointer;">
                                    <td>{{ $loop->iteration }}</td>
                                    <td onclick="event.stopPropagation();">
                                        <a target="_blank" href="{{ url('crm-user-tasks/' . $value->id . '/view') }}">
                                            {{ $value->task_id }}</a>
                                    </td>
                                    <td class="truncate-text" title="{{ $value->task_title }}">{{ $value->task_title }}
                                    </td>
                                    <td>
                                        <a href="#">
                                            View Tasks ({{ $value->taskitems_count }})
                                        </a>
                                    </td>
                                    <td>{{ $value->assignedby->full_name }}</td>
                                    <td>{{ date('d/m/Y h:i A', strtotime($value->created_at)) }}</td>
                                    <td class="{{ $value->is_overdue ? 'text-danger' : '' }}">
                                        {{ date('d/m/Y h:i A', strtotime($value->task_due_date)) }}</td>
                                    <td onclick="event.stopPropagation();">
                                        @if (!empty($value->attachment))
                                            <a href="{{ asset('public/uploads/crm_user_tasks/' . $value->attachment) }}"
                                                target="_blank" class="text-decoration-none text-primary"
                                                title="View Attachment">
                                                <i class="fa fa-paperclip me-1"></i> View
                                            </a>
                                        @endif
                                    </td>
                                    <td>
                                        <span
                                            class="badge badge-{{ $value->priority == 'low' ? 'success' : ($value->priority == 'medium' ? 'warning' : ($value->priority == 'high' ? 'dark' : 'danger')) }}">
                                            {{ ucfirst($value->priority) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge badge-{{ $value->status_r == 'not_started' ? 'warning' : ($value->status_r == 'in_progress' ? 'info' : ($value->status_r == 'completed' ? 'success' : ($value->status_r == 'blocked' ? 'dark' : 'info'))) }}">
                                            {{ ucwords(str_replace('_', ' ', $value->status_r)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge badge-{{ $value->status_s === 'open'
                                                ? 'warning'
                                                : ($value->status_s === 'review'
                                                    ? 'info'
                                                    : ($value->status_s === 'approved'
                                                        ? 'success'
                                                        : ($value->status_s === 'modification'
                                                            ? 'primary'
                                                            : ($value->status_s === 'rejected'
                                                                ? 'danger'
                                                                : 'secondary')))) }}">
                                            {{ ucwords(str_replace('_', ' ', $value->status_s)) }}
                                        </span>
                                    </td>


                                    <td onclick="event.stopPropagation();">
                                        <a type="button" class="btn-sm btn-info update-progress-btn"
                                            data-id="{{ $value->id }}" data-task_id="{{ $value->task_id }}"
                                            data-status="{{ $value->status_r }}" data-title="{{ $value->task_title }}"
                                            data-due="{{ date('d/m/Y h:i A', strtotime($value->task_due_date)) }}"
                                            data-priority="{{ $value->priority }}">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a type="button" class="btn-sm btn-primary btn-comments comment-btn-wrapper"
                                            data-task-id="{{ $value->id }}">
                                            <i class="fa fa-plus" aria-hidden="true"></i> Comments
                                            @if ($value->unread_main_comments_count > 0)
                                                <span class="comment-badge">
                                                    {{ $value->unread_main_comments_count }}</span>
                                            @endif
                                        </a>


                                    </td>
                                </tr>

                                <!-- Expandable Row -->
                                <tr id="task-details-{{ $value->id }}" class="expand-row"
                                    style="display: none; background-color: #f9f9f9;">
                                    <td colspan="12" class="p-2">
                                        <div class="expandable-content" style="display: none;">
                                            <strong>Task Title:</strong> {{ $value->task_title }}<br>

                                            <strong>Task Items:</strong>
                                            <ol class="mb-1">
                                                @foreach ($value->taskitems as $task)
                                                    <li class="p-2">
                                                        {{ $task->task }}


                                                        <span
                                                            class="badge badge-{{ $task->status_r == 'not_started' ? 'warning' : ($task->status_r == 'in_progress' ? 'info' : ($task->status_r == 'completed' ? 'success' : ($task->status_r == 'blocked' ? 'dark' : 'info'))) }}">
                                                            {{ ucwords(str_replace('_', ' ', $task->status_r)) }}
                                                        </span>

                                                        <span
                                                            class="badge badge-{{ $task->status_s === 'open'
                                                                ? 'warning'
                                                                : ($task->status_s === 'review'
                                                                    ? 'info'
                                                                    : ($task->status_s === 'approved'
                                                                        ? 'success'
                                                                        : ($task->status_s === 'modification'
                                                                            ? 'primary'
                                                                            : ($task->status_s === 'rejected'
                                                                                ? 'danger'
                                                                                : 'secondary')))) }}">
                                                            {{ ucwords(str_replace('_', ' ', $task->status_s)) }}
                                                        </span>


                                                        <span type="button" class="badge badge-info update-progress-btn"
                                                            data-id="{{ $value->id }}" {{-- data-task-item-id="{{ $task->id }}" --}}
                                                            data-task_id="{{ $value->task_id }}"
                                                            data-task_item_id="{{ $task->id }}"
                                                            data-status="{{ $task->status_r }}"
                                                            data-title="{{ $task->task }}"
                                                            data-due="{{ date('d/m/Y h:i A', strtotime($value->task_due_date)) }}"
                                                            data-priority="{{ $value->priority }}">
                                                            <i class="fa fa-edit"></i>
                                                        </span>

                                                        <a type="button"
                                                            class="badge badge-primary btn-comments comment-btn-wrapper"
                                                            data-task-id="{{ $value->id }}"
                                                            data-task-item-id="{{ $task->id }}">
                                                            <i class="fa fa-comments" aria-hidden="true"></i>
                                                            @if ($task->unread_comments_count > 0)
                                                                <span class="comment-badge">
                                                                    {{ $task->unread_comments_count }}</span>
                                                            @endif
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
    <div class="modal fade" id="ModalAddNewTask" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Assign New Task</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-user-tasks', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="" class="form-label">Task Title <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="task_title" id="task_title" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Task Priority <span
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


                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Assigned To <span
                                        class="text-danger">*</span></label>
                                <select class="form-control js-example-basic-single" name="assigned_to" id="assigned_to"
                                    required>
                                    <option value="">-Select-</option>
                                    @if (count($staff_list) > 0)
                                        @foreach ($staff_list as $value)
                                            <option value="{{ $value->user_id }}">{{ $value->full_name }}</option>
                                        @endforeach
                                    @endif

                                </select>
                            </div>
                        </div>


                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Due Date & Time <span
                                        class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" name="task_due_date"
                                    id="task_due_date" required min="{{ now()->format('Y-m-d\T00:00') }}" onchange="">
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
                                <label for="" class="form-label">Tasks <span
                                        class="text-danger">*</span></label>
                                {{-- <a onclick="add_task()" class="btn-sm btn-primary float-right"><i class="fa fa-plus-square"
            aria-hidden="true"></i></a> --}}

                                <button type="button" class="btn btn-sm btn-info btn-add-task  float-right"
                                    onclick="add_task()">
                                    <i class="fa fa-plus"></i> Add Task
                                </button>

                                <table width="100%">
                                    <tr>
                                        <td width="1%">1. </td>
                                        <td><input type="text" required class="form-control" name="task[]" id="task_1">
                                        </td>
                                    </tr>
                                    @for ($i = 2; $i <= 20; $i++)
                                        <tr id="row_{{ $i }}" style="display:none;">
                                            <td>{{ $i }}. </td>
                                            <td><input type="text" class="form-control" name="task[]"
                                                    id="task_{{ $i }}"></td>
                                        </tr>
                                    @endfor
                                </table>




                                <input type="hidden" id="task_row_id" value="1" />
                                <script>
                                    function add_task() {
                                        var scope = $('#task_row_id').val();

                                        scope++;
                                        $('#row_' + scope).css('display', '');
                                        $('#task_row_id').val(scope);
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
                    <button type="submit" class="btn btn-primary">Assign Task</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    {{-- <!-- Modal Add Task--> --}}

    {{-- <!-- Modal Update Task--> --}}
    <div class="modal fade" id="ModalTaskUpdate" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Update Task Progress</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-task-progress-update', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}

                <input type="hidden" name="task_id" id="task_id">
                <input type="hidden" name="task_item_id" id="task_item_id">
                <div class="modal-body">
                    <!-- Task Summary -->
                    <div class="panel panel-default" style="border-radius: 8px;">

                        <div class="panel-body" style="padding: 15px;">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Task ID:</strong> <span id="modal_task_id_text"></span>
                                    </p>
                                    <p><strong>Title:</strong> <span id="modal_task_title"></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Due Date:</strong> <span id="modal_task_due_date"
                                            class="text-danger"></span></p>
                                    <p><strong>Priority:</strong>
                                        <span id="modal_task_priority" class="badge text-capitalize"></span>

                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Update Section -->
                    <div class="form-group">
                        <label for="status_r" class="control-label">
                            <i class="fa fa-line-chart text-success"></i> Update Progress <span
                                class="text-danger">*</span>
                        </label>
                        <select name="status_r" id="status_r" class="form-control input-sm" required>
                            <option value="">-- Select Status --</option>
                            <option value="not_started">Not Started</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="blocked">Blocked</option>
                            <option value="cancelled">Cancelled</option>
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

    <div class="modal fade" id="ModalTaskComments" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document" style="min-width:50% !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Task Comments</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>


                <div class="modal-body">



                    <div id="commentsContainer" style="max-height: 400px; overflow-y: auto;"></div>
                    <textarea id="newComment" class="form-control mt-3" cols="10" rows="3"
                        placeholder="Write your comment..."></textarea>
                    <button id="submitComment" class="btn btn-primary mt-2 pull-right">Send</button>

                    <button class="btn btn-danger mt-2 pull-right" type="button" data-dismiss="modal">Cancel</button>

                    <input type="hidden" id="currentTaskId" />
                    <input type="hidden" id="task_item_id" />


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
                $('#task_id').val($(this).data('id'));
                $('#task_item_id').val($(this).data('task_item_id')) || null;
                $('#status_r').val($(this).data('status'));
                $('#modal_task_id_text').text($(this).data('task_id'));
                $('#modal_task_title').text($(this).data('title'));

                var priority = $(this).data('priority'); // e.g., 'low', 'medium', 'high', 'critical'
                var $prioritySpan = $('#modal_task_priority');

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

                $('#modal_task_due_date').text($(this).data('due'));
                $('#modal_task_priority').text($(this).data('priority'));
                $('#ModalTaskUpdate').modal('show');
            });
        });


        $('.btn-comments').on('click', function() {
            var taskId = $(this).data('task-id');
            var taskItemId = $(this).data('task-item-id') || '';
            $('#task_item_id').val(taskItemId);

            $('#currentTaskId').val(taskId);

            $('#commentsContainer').html('<p>Loading comments...</p>');
            $('#ModalTaskComments').modal('show');

            $(this).find('.comment-badge').remove();


            $.ajax({
                url: '/crm-user-task/' + taskId + '/comments',
                type: 'GET',
                data: {
                    task_item_id: taskItemId
                },
                success: function(comments) {
                    $('#commentsContainer').html('');
                    if (comments.length === 0) {
                        $('#commentsContainer').html(
                            '<p id="no-comments" class="text-muted">No comments yet.</p>');
                    } else {

                        comments.forEach(function(comment) {

                            var avatarInitial = comment.user.full_name.charAt(0).toUpperCase();

                            $('#commentsContainer').append(
                                '<div class="chat-msg"> <div class="avatar">' +
                                avatarInitial + '</div>' +
                                '<div class="chat-msg-content">' +
                                '<strong>' + comment.user.full_name + '</strong>' +
                                '<small>' + new Date(comment.created_at).toLocaleString(
                                    'en-US', {
                                        day: '2-digit',
                                        month: '2-digit',
                                        year: 'numeric',
                                        hour: '2-digit',
                                        minute: '2-digit',
                                        hour12: true
                                    }) +
                                '</small>' +
                                '<p>' + comment.comment + '</p>' +
                                '</div>' +
                                '</div>'
                            );
                        });

                        // Scroll to bottom to see the latest comment
                        $('#commentsContainer').scrollTop($('#commentsContainer')[0].scrollHeight);
                    }
                }
            });
        });

        $('#submitComment').on('click', function() {
            var commentText = $('#newComment').val().trim();
            var taskId = $('#currentTaskId').val();
            var taskItemId = $('#task_item_id').val() || null;

            if (!commentText) return alert("Comment cannot be empty.");

            $.ajax({
                url: '/crm-user-task/' + taskId + '/comments',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    comment: commentText,
                    task_item_id: taskItemId
                },

                success: function(newComment) {
                    $('#newComment').val('');
                    var avatarInitial = newComment.user.full_name.charAt(0).toUpperCase();
                    $('#commentsContainer').append(
                        '<div class="chat-msg ">' +
                        '<div class="avatar">' + avatarInitial + '</div>' +
                        '<div class="chat-msg-content">' +
                        '<strong>' + newComment.user.full_name + '</strong>' +
                        '<small>' + new Date(newComment.created_at).toLocaleString('en-US', {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: true
                        }) + '</small>' +
                        '<p>' + newComment.comment + '</p>' +
                        '</div>' +
                        '</div>'
                    );

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

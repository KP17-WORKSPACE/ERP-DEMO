@extends('backEnd.newmasterpage')
@section('mainContent')
    <script>
        let isFullList = false;

        function list_style_new() {
            const leftNav = document.querySelector('.left-nav');
            const content = document.querySelector('.content-container');

            if (!isFullList) {
                // Switch to FULL LIST VIEW
                isFullList = true;

                leftNav.classList.remove('col-3');
                leftNav.classList.add('col-12');
                leftNav.style.width = '100%';

                content.classList.add('d-none');

                $('#long-list').removeClass('d-none');
                $('#short-list').addClass('d-none');

                $('#filters-long').removeClass('d-none');
                $('#filters-short').addClass('d-none');
            } else {
                // Switch to COMPACT VIEW
                isFullList = false;

                leftNav.classList.remove('col-12');
                leftNav.classList.add('col-3');
                leftNav.style.width = '';

                content.classList.remove('d-none');

                $('#long-list').addClass('d-none');
                $('#short-list').removeClass('d-none');

                $('#filters-short').removeClass('d-none');
                $('#filters-long').addClass('d-none');
            }
        }


        //added ny kp
        function toggleLongFilters() {
            console.log("clicked");
            document.querySelectorAll('#filters-long .filter-field').forEach(el => {
                el.classList.toggle('d-none');
            });
        }
    </script>
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
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>

    <?php try { ?>

    <aside class="left-nav col-12" id="leftSidebar">


        <div class="long-list" id="filters-long">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="mb-2">
                    Todo List
                </h4>
                <div class="search-filter-container mb-0">


                    <input type="text" id="tableSearch" class="form-control"
                        style="font-size:13px; width: 350px; 
                        top: 12px;
                        right: 120px;"
                        placeholder="Search">

                    <button type="button" data-bs-toggle="modal" data-bs-target="#ModalAddNewTodo" class="btn btn-light">
                        <i class="ico icon-outline-add-square text-success"></i> Add
                    </button>

                    <button class="btn btn-light" onclick="toggleLongFilters()">
                        <i class="ico icon-outline-magnifer"></i>
                    </button>
                    <button class="btn btn-light" id="list_style_button" onclick="list_style_new()">
                        <i class="ico icon-outline-list-down"></i>
                    </button>
                </div>
            </div>


        </div>

        <!-- Advanced Dashboard Cards -->

        <div class="row g-4 mt-1">
            <!-- New -->
            <div class="col-md-6 col-lg-4 col-xl-2">
                <div class="card card-dashboard   shadow-sm border-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon me-3">
                            <i class="ico icon-bold-checklist-minimalistic fs-2"></i>
                        </div>
                        <div>
                            <h6 class="card-title mb-1">Total</h6>
                            <h3 class="fw-bold" id="count-new">{{ $total_todo }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending -->
            <div class="col-md-6 col-lg-4 col-xl-2">
                <div class="card card-dashboard  shadow-sm border-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon me-3">
                            <i class="ico icon-bold-calendar-mark fs-2"></i>
                        </div>
                        <div>
                            <h6 class="card-title mb-1">Due Today</h6>
                            <h3 class="fw-bold" id="count-pending">{{ $total_todo_due_today }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Due -->
            <div class="col-md-6 col-lg-4 col-xl-2">
                <div class="card card-dashboard  shadow-sm border-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon me-3">
                            <i class="ico icon-bold-alarm fs-2"></i>
                        </div>
                        <div>
                            <h6 class="card-title mb-1">Overdue</h6>
                            <h3 class="fw-bold" id="count-due">{{ $total_todo_overdue }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Overdue -->
            <div class="col-md-6 col-lg-4 col-xl-2">
                <div class="card card-dashboard   shadow-sm border-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon me-3">
                            <i class="ico icon-bold-stopwatch-pause fs-2"></i>
                        </div>
                        <div>
                            <h6 class="card-title mb-1">Not Started</h6>
                            <h3 class="fw-bold" id="count-overdue">{{ $total_todo_not_started }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Completed -->
            <div class="col-md-6 col-lg-4 col-xl-2">
                <div class="card card-dashboard   shadow-sm border-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon me-3">
                            <i class="ico icon-bold-stopwatch-play fs-2"></i>
                        </div>
                        <div>
                            <h6 class="card-title mb-1">In Progress</h6>
                            <h3 class="fw-bold" id="count-completed">{{ $total_todo_in_progress }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cancelled -->
            <div class="col-md-6 col-lg-4 col-xl-2">
                <div class="card card-dashboard  shadow-sm border-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon me-3">
                            <i class="ico icon-bold-check-circle fs-2"></i>
                        </div>
                        <div>
                            <h6 class="card-title mb-1">Completed</h6>
                            <h3 class="fw-bold" id="count-cancelled">{{ $total_todo_completed }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Custom CSS -->
        <style>
            .card-dashboard {
                border-radius: 12px;
                transition: transform 0.3s, box-shadow 0.3s;
                background-color: #deebe1;
            }

            .card-dashboard:hover {
                transform: translateY(-6px);
                box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
            }

            .card-dashboard .icon {
                width: 50px;
                height: 50px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
        </style>

        <!-- Include Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">


        <div class="left-nav-list">




            <div class="table-responsive mb-4 mt-4">
                <table id="long-list" class="table table-hover data-table" style="table-layout: fixed;width:100%">

                    <thead>
                        <tr>

                            <th style="width: 200px;">@lang('Todo Title')</th>
                            <th class="text-center" style="width: 80px;">@lang('Tasks')</th>
                            <th class="text-center" style="width: 100px;">@lang('Created Date & Time')
                            </th>
                            <th class="text-center" style="width: 100px;">@lang('Due Date & Time')
                            </th>
                            <th style="width: 60px;">@lang('Priority')</th>
                            <th style="width: 80px;">@lang('Status')</th>
                            <th class="text-center" style="width: 30px;"> <i class="ico icon-bold-paperclip"></i> </th>
                            <th class="text-center" style="width: 90px;">@lang('Action')

                            </th>
                        </tr>
                    </thead>


                    <tbody>
                        @foreach ($todos as $value)
                            <tr class="main-row  {{ $value->deleted_at ? 'bg-dark' : '' }} " style="cursor: pointer;">


                                <td title="{{ $value->todo_title }}">{{ $value->todo_title }}
                                </td>
                                <td class="text-center">
                                    <a data-bs-toggle="modal" data-bs-target="#ModalViewTodo{{ $value->id }}">View
                                        ({{ $value->todo_items_count }})
                                    </a>

                                </td>



                                <td class="text-center">{{ date('d/m/Y h:i A', strtotime($value->created_at)) }}</td>
                                <td class="{{ $value->is_overdue && $value->status !== 'completed' ? 'text-danger' : '' }} text-center">
                                    {{ date('d/m/Y h:i A', strtotime($value->todo_due_date)) }}</td>


                                <td>
                                    <span
                                        class="text-{{ $value->priority == 'low' ? 'success' : ($value->priority == 'medium' ? '' : ($value->priority == 'high' ? 'dark' : 'danger')) }}">
                                        {{ ucfirst($value->priority) }}
                                    </span>
                                </td>
                                <td>
                                    <span
                                        class="text-{{ $value->status === 'not_started'
                                            ? ''
                                            : ($value->status === 'in_progress'
                                                ? 'dark'
                                                : ($value->status === 'completed'
                                                    ? 'success'
                                                    : 'dark')) }}">
                                        {{ ucwords(str_replace('_', ' ', $value->status)) }}
                                    </span>
                                </td>

                                <td class="text-center" onclick="event.stopPropagation();">
                                    @if (!empty($value->attachment))
                                        <a href="{{ asset('public/uploads/crm_user_todos/' . $value->attachment) }}"
                                            target="_blank" class="text-decoration-none text-primary"
                                            title="View Attachment">
                                            <i class="ico icon-bold-paperclip"></i>
                                        </a>
                                    @endif
                                </td>


                                <td onclick="event.stopPropagation();">

                                    <div class="d-flex justify-content-center align-items-center">

                                        <a type="button" class="btn btn-sm btn-light update-progress-btn"
                                            data-id="{{ $value->id }}" data-status="{{ $value->status }}"
                                            data-title="{{ $value->todo_title }}"
                                            data-due="{{ date('d/m/Y h:i A', strtotime($value->todo_due_date)) }}"
                                            data-priority="{{ $value->priority }}">
                                            <i class="ico icon-outline-check-square text-dark"
                                                style="font-size: 16px;"></i>

                                        </a>


                                        <a type="button" class="btn-sm btn btn-light btn-comments comment-btn-wrapper"
                                            data-todo-id="{{ $value->id }}"
                                            data-task-title="{{ $value->todo_title }}">
                                            <i class="ico icon-outline-chat-round-dots" aria-hidden="true"
                                                style="font-size: 16px"></i>
                                        </a>

                                        <a type="button" class="btn-sm btn btn-light edit-task-btn"
                                            data-id="{{ $value->id }}">
                                            <i class="ico icon-outline-pen-2 text-dark" style="font-size: 16px;"></i>
                                        </a>

                                        @if ($value->deleted_at)
                                            <form action="{{ url('user-todo-restore/' . $value->id) }}" method="POST"
                                                style="display:inline;"
                                                onsubmit="return confirm('Are you sure you want to restore this?');">
                                                {{ csrf_field() }}
                                                <button type="submit" class="btn-sm btn btn-light" title="Restore">
                                                    <i class="ico icon-bold-restart text-dark"
                                                        style="font-size: 16px;"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ url('user-todo-delete/' . $value->id) }}" method="POST"
                                                style="display:inline;"
                                                onsubmit="return confirm('Are you sure you want to delete this?');">
                                                {{ csrf_field() }}
                                                {{ method_field('DELETE') }}
                                                <button type="submit" class="btn-sm btn btn-light" title="Delete">
                                                    <i class="ico icon-outline-trash-bin-minimalistic text-dark"
                                                        style="font-size: 16px;"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>




                            <!-- Expandable Row -->
                            {{-- <tr id="task-details-{{ $value->id }}" class="expand-row"
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
                                                        data-id="{{ $value->id }}" 
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
                            </tr> --}}
                        @endforeach



                    </tbody>
                </table>
            </div>
        </div>
    </aside>

    @foreach ($todos as $value)
        <div class="modal  fade" id="ModalViewTodo{{ $value->id }}" data-bs-backdrop="false" tabindex="-1"
            aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" style="top: 10%">
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'user-todo-list', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}

                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="editModalLabel">View Todo
                        </h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body m-0 p-0">
                        <div class="card mb-0 mt-0">
                            <div class="card-body">
                                <div class="row gap-rows">

                                    <div class="col-6">
                                        <p class="mb-1 text-muted"><strong>Title</strong></p>
                                        <p id="modal_task_title" class="fw-semibold text-dark">
                                            {{ $value->todo_title }}</p>
                                    </div>
                                    <div class="col-6">
                                        <p class="mb-1 text-muted"><strong>Description</strong></p>
                                        <p id="modal_task_due_date" class="fw-semibold text-dark">
                                            {{ $value->description }}</p>
                                    </div>

                                    <div class="col-12">
                                        <h6 class="text-muted mb-2">Tasks</h6>

                                          @foreach ($value->todoItems as $task)

                                        <table id="long-list" class="table table-hover"
                                            style="table-layout: fixed;width:100%">

                                            <thead>
                                                <tr>
                                                    <th class="text-center" style="width: 60px;">@lang('Task ID')</th>
                                                    <th style="width: 170px;">@lang('Task Title')</th>
                                                    <th style="width: 170px;">@lang('Status')</th>

                                                    <th class="text-center" style="width: 90px;">@lang('Action')

                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                <tr>
                                                    <td class="text-center">{{ $loop->iteration }}</td>
                                                    <td>{{ $task->todo }}</td>
                                                    <td class="text-center">
                                                        <div
                                                            class="col-2  text-{{ $task->status === 'not_started'
                                                                ? 'dark'
                                                                : ($task->status === 'in_progress'
                                                                    ? 'dark'
                                                                    : ($task->status === 'completed'
                                                                        ? 'success'
                                                                        : 'dark')) }}">
                                                            {{ ucwords(str_replace('_', ' ', $task->status)) }}
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <div class=" d-flex justify-content-center align-items-center">



                                                            <a type="button"
                                                                class="btn btn-sm btn-light update-progress-btn"
                                                                data-id="{{ $value->id }}" {{-- data-task-item-id="{{ $task->id }}" --}}
                                                                data-todo_item_id="{{ $task->id }}"
                                                                data-status="{{ $task->status }}"
                                                                data-title="{{ $task->todo }}"
                                                                data-due="{{ date('d/m/Y h:i A', strtotime($value->todo_due_date)) }}"
                                                                data-priority="{{ $value->priority }}">
                                                                <i class="ico ico icon-outline-check-square text-dark"
                                                                    style="font-size: 16px;"></i>

                                                            </a>


                                                            <a type="button"
                                                                class="btn-sm btn btn-light btn-comments comment-btn-wrapper"
                                                                data-todo-id="{{ $value->id }}"
                                                                data-todo-item-id="{{ $task->id }}"
                                                                data-task-title="{{ $task->todo }}">
                                                                <i class="ico icon-outline-chat-round-dots"
                                                                    aria-hidden="true" style="font-size: 16px"></i>
                                                            </a>


                                                            <a href="{{ url('user-sub-todo-delete/' . $value->id . '/' . $task->id . '/') }}"
                                                                class="btn-sm btn btn-light" title="Delete">
                                                                <i class="ico icon-bold-trash-bin-2 text-dark"
                                                                    style="font-size: 16px;"></i>
                                                            </a>


                                                        </div>

                                                    </td>
                                                </tr>

                                            </tbody>
                                        </table>

                                        @endforeach


                                        {{-- <ul class="list-group list-group-flush">
                                            @foreach ($value->todoItems as $task)
                                                <div class="row">


                                                    <div class="col-6">{{ $loop->iteration }}.
                                                        {{ $task->todo }}</div>


                                                    <div
                                                        class="col-2 text-start text-{{ $task->status === 'not_started'
                                                            ? 'dark'
                                                            : ($task->status === 'in_progress'
                                                                ? 'dark'
                                                                : ($task->status === 'completed'
                                                                    ? 'success'
                                                                    : 'dark')) }}">
                                                        {{ ucwords(str_replace('_', ' ', $task->status)) }}
                                                    </div>


                                                    <div class="col-2">
                                                        <div class=" d-flex justify-content-start align-items-center">



                                                            <a type="button"
                                                                class="btn btn-sm btn-light update-progress-btn"
                                                                data-id="{{ $value->id }}" 
                                                                data-todo_item_id="{{ $task->id }}"
                                                                data-status="{{ $task->status }}"
                                                                data-title="{{ $task->todo }}"
                                                                data-due="{{ date('d/m/Y h:i A', strtotime($value->todo_due_date)) }}"
                                                                data-priority="{{ $value->priority }}">
                                                                <i class="ico icon-outline-square-bottom-up text-dark"
                                                                    style="font-size: 16px;"></i>

                                                            </a>


                                                            <a type="button"
                                                                class="btn-sm btn btn-light btn-comments comment-btn-wrapper"
                                                                data-todo-id="{{ $value->id }}"
                                                                data-todo-item-id="{{ $task->id }}"
                                                                data-task-title="{{ $task->todo }}">
                                                                <i class="ico icon-outline-chat-round-dots"
                                                                    aria-hidden="true" style="font-size: 16px"></i>
                                                            </a>


                                                            <a href="{{ url('user-sub-todo-delete/' . $value->id . '/' . $task->id . '/') }}"
                                                                class="btn-sm btn btn-light" title="Delete">
                                                                <i class="ico icon-bold-trash-bin-2 text-dark"
                                                                    style="font-size: 16px;"></i>
                                                            </a>


                                                        </div>
                                                    </div>


                                                </div>
                                            @endforeach
                                        </ul> --}}
                                    </div>




                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-light add-btn ms-2">
                            <i class="ico icon-outline-bookmark-opened text-success"></i> Assign Task
                        </button>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    @endforeach




    <div class="modal  fade" id="ModalAddNewTodo" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="top: 10%">
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'user-todo-list', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel">Add Todo
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body">
                            <div class="row gap-rows">

                                <div class="col-12">
                                    <label for="" class="form-label">Todo Title <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="todo_title" id="todo_title"
                                        required>
                                </div>

                                <div class="col-4">
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



                                @php
                                    $defaultDateTime = \Carbon\Carbon::now()->addHours(2)->format('d/m/Y h:i A'); // e.g., 23/08/2025 03:45 PM

                                @endphp

                                <div class="col-4">
                                    <label for="" class="form-label">Due Date & Time <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control date-time-picker" name="todo_due_date"
                                        id="todo_due_date" required onchange="" value="{{ $defaultDateTime }}">
                                </div>

                                <div class="col-4">
                                    <label for="" class="form-label">Attachment</label>
                                    <input type="file" class="form-control" name="attachment" id="attachment">
                                </div>


                                <div class="col-12 mt-2">
                                    <label for="" class="form-label">Description</label>
                                    <textarea class="form-control" name="description" id="description" style="height:100px"></textarea>

                                </div>



                                <div class="col-12 mt-2">

                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label fw-semibold mb-0">Scope of Work</label>
                                        <button type="button" id="addRow" class="btn btn-light rounded-0"><i
                                                class="ico icon-outline-add-square text-success"></i> Add
                                        </button>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless align-middle mb-0" id="taskTable">

                                            <tbody>
                                                <tr>
                                                    <td width="5%"><input type="text"
                                                            class="form-control serial text-center" value="1"></td>
                                                    <td><input type="text" name="todo[]" class="form-control task"
                                                            placeholder="Enter task"></td>
                                                    <td width="5%">
                                                        <div class="d-flex justify-content-center">
                                                            <button type="button"
                                                                class="btn btn-light rounded-0 btn-sm deleteRow">
                                                                <i class="ico icon-outline-trash-bin-minimalistic"
                                                                    style="font-size: 16px"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>


                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-light add-btn ms-2">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Assign Task
                    </button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>

    <div class="modal  fade" id="ModalTodoUpdate" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="top: 10%">
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-todo-progress-update', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel">Update Task Progress
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body">



                            <input type="hidden" name="todo_id" id="todo_id">
                            <input type="hidden" name="todo_item_id" id="todo_item_id">

                            <div class="row gap-rows">


                                <div class="col-4">
                                    <p class="mb-1 text-muted"><strong>Title</strong></p>
                                    <p id="modal_todo_title" class="fw-semibold text-dark"></p>
                                </div>
                                <div class="col-3">
                                    <p class="mb-1 text-muted"><strong>Due Date</strong></p>
                                    <p id="modal_todo_due_date" class="fw-semibold text-dark"></p>
                                </div>
                                <div class="col-2">
                                    <p class="mb-1 text-muted"><strong>Priority</strong></p>
                                    <p id="modal_todo_priority" class="fw-semibold text-capitalize text-dark"></p>
                                </div>
                                <div class="col-2">
                                    <p class="mb-1 text-muted"><strong>Status</strong></p>
                                    <p id="modal_todo_status" class="fw-semibold text-capitalize text-dark"></p>
                                </div>


                                <div class="col-12 mb-2">

                                    <label for="status_r" class="form-label">
                                        Update Progress <span class="text-danger">*</span>
                                    </label>
                                    <select name="status" id="status" class="form-control input-sm" required>
                                        <option value="">-- Select Status --</option>
                                        <option value="not_started">Not Started</option>
                                        <option value="in_progress">In Progress</option>
                                        <option value="completed">Completed</option>

                                    </select>

                                </div>


                                <div class="col-12">
                                    <div class="form-group">

                                        <textarea name="comment" class="form-control" style="height: 100px" placeholder="Write your comment..."></textarea>
                                    </div>
                                </div>





                            </div>



                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-light add-btn ms-2">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Update
                    </button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>


    <div class="modal  fade" id="ModalTodoComments" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="top: 10%">
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-todo-progress-update', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel">Task Comments
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body">
                            <div class="row gap-rows">


                                <div class="col-12 custom-table-container">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="">
                                                <tr>
                                                    <th style="width: 250px">
                                                        Comment
                                                    </th>
                                                    <th class="text-center" style="width: 50px">
                                                        Status
                                                    </th>
                                                    <th style="width: 100px">
                                                        Updated Time
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody id="commentsContainer">

                                                <tr id="no-comments">
                                                    <td colspan="3" class="text-muted text-center">No comments yet.
                                                    </td>
                                                </tr>



                                            </tbody>
                                        </table>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>


            </div>
            {{ Form::close() }}
        </div>
    </div>

    <div class="modal  fade" id="ModalEditTodo" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="top: 10%">
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-todo-update', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel">Edit Todo
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body">
                            <div class="row gap-rows">
                                <input type="hidden" name="edit_todo_id" id="edit_todo_id">

                                <div class="col-12">
                                    <label for="" class="form-label">Todo Title <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="edit_todo_title"
                                        id="edit_todo_title" required>
                                </div>

                                <div class="col-4">
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



                                <div class="col-4">
                                    <label for="" class="form-label">Due Date & Time <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control date-time-picker" name="edit_todo_due_date"
                                        id="edit_todo_due_date" required onchange="" value="{{ $defaultDateTime }}">
                                </div>

                                <div class="col-4">
                                    <label for="" class="form-label">Attachment</label>
                                    <input type="file" class="form-control" name="edit_attachment"
                                        id="edit_attachment">
                                </div>

                                <div class="col-md-12 mt-2">

                                    <label for="" class="form-label">Description</label>
                                    <textarea class="form-control" name="edit_description" id="edit_description" rows="4"></textarea>

                                </div>


                                <div class="col-12 mt-2">

                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label fw-semibold mb-0">Scope of Work</label>
                                        <button type="button" id="addRowEdit" class="btn btn-light rounded-0"><i
                                                class="ico icon-outline-add-square text-success"></i> Add
                                        </button>
                                    </div>


                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless align-middle mb-0"
                                            id="taskTableEdit">

                                            <tbody>
                                                <tr>
                                                    <td width="5%"><input type="text"
                                                            class="form-control serial text-center" value="1"></td>
                                                    <td><input type="text" name="edit_todo[]"
                                                            class="form-control task" placeholder="Enter task"></td>
                                                    <td width="5%">
                                                        <div class="d-flex justify-content-center">
                                                            <button type="button"
                                                                class="btn btn-light rounded-0 btn-sm deleteRowEdit">
                                                                <i class="ico icon-outline-trash-bin-minimalistic"
                                                                    style="font-size: 16px"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>


                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-light add-btn ms-2">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Update
                    </button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>









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

                $statusSpan.removeClass('text-dark text-success');


                console.log('Status:', status);
                switch (status) {
                    case 'not_started':
                        $statusSpan.addClass('text-dark').text('Not Started');
                        break;
                    case 'in_progress':
                        $statusSpan.addClass('text-dark').text('In Progress');
                        break;
                    case 'completed':
                        $statusSpan.addClass('text-success').text('Completed');
                        break;
                    default:
                        $statusSpan.text(status); // Fallback text
                }

                // Clear previous badge classes
                $prioritySpan.removeClass('text-dark text-success text-danger');

                // Add new class and text based on priority
                switch (priority) {
                    case 'low':
                        $prioritySpan.addClass('text-success').text('Low');
                        break;
                    case 'medium':
                        $prioritySpan.addClass('text-dark').text('Medium');
                        break;
                    case 'high':
                        $prioritySpan.addClass('text-dark').text('High');
                        break;
                    case 'critical':
                        $prioritySpan.addClass('text-danger').text('Critical');
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

                            // ✅ Safe check for nested todo and todo_item objects
                            const TodoTitle = comment.todo_item_id === null ?
                                (comment.todo && comment.todo.todo_title ?
                                    comment.todo.todo_title :
                                    'Untitled Task') :
                                (comment.todo_item && comment.todo_item.todo ?
                                    comment.todo_item.todo :
                                    'Untitled Subtask');

                            // ✅ Format time
                            const timeFormatted = new Date(comment.created_at).toLocaleString(
                                'en-US', {
                                    day: '2-digit',
                                    month: '2-digit',
                                    year: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    hour12: true
                                });

                            // ✅ Badge color based on status
                            let badgeClass = '';
                            switch (comment.status) {
                                case 'not_started':
                                    badgeClass = 'secondary';
                                    break;
                                case 'in_progress':
                                    badgeClass = 'warning';
                                    break;
                                case 'blocked':
                                    badgeClass = 'danger';
                                    break;
                                case 'completed':
                                    badgeClass = 'success';
                                    break;
                                default:
                                    badgeClass = 'dark';
                            }

                            // ✅ Append formatted comment row
                            $('#commentsContainer').append(`
            <tr>
                <td>
                    <strong>Task Title:</strong> ${TodoTitle}<br>
                    ${comment.comment || ''}
                </td>
                <td class="text-center">
                    <span class="badge bg-${badgeClass} text-capitalize">
                        ${comment.status ? comment.status.replace('_', ' ') : 'N/A'}
                    </span>
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
            let tr = "";
            $.ajax({
                url: '/user-todo-list/' + todoId + '/edit',
                method: 'GET',
                success: function(res) {


                    console.log(res.todo.priority)
                    // Fill modal fields
                    $('#edit_todo_id').val(todoId);
                    $('#edit_todo_title').val(res.todo.todo_title);
                    $('#edit_priority').val(res.todo.priority).trigger('change');;
                    if (res.todo.todo_due_date) {
                        const dueDate = new Date(res.todo.todo_due_date); // parse MySQL datetime
                        const options = {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: true
                        };
                        const formattedDate = dueDate.toLocaleString('en-GB', options).replace(',', '');
                        $('#edit_todo_due_date').val(formattedDate); // e.g., 25/08/2025 08:43 PM
                    }
                    $('#edit_description').val(res.todo.description);

                    // Fill tasks
                    var todoArr = res.todo.todo_items || [];
                    for (var i = 1; i <= 20; i++) {
                        $('#edit_todo_' + i).val('');
                        $('#edit_row_' + i).hide();
                    }

                    todoArr.forEach((val, i) => {
                        let serial = i + 1;
                        tr += `
        <tr id="row_edit_${i}">
            <td width="5%">
              
                <input type="text" class="form-control serial text-center" value="${serial}" readonly>
            </td>
            <td>
                <input type="text" class="form-control task" 
                       value="${val.todo}" 
                       name="edit_todo[]" autocomplete="off">
            </td>
            <td width="5%">
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-light rounded-0 btn-sm deleteRowEdit">
                        <i class="ico icon-outline-trash-bin-minimalistic" style="font-size: 16px"></i>
                    </button>
                </div>
            </td>
        </tr>`;
                    });


                    // Show modal
                    $('#taskTableEdit tbody').empty();
                    $('#taskTableEdit tbody').html(tr);
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



    <script>
        $(document).ready(function() {
            // Function to update serial numbers
            function updateSerialNumbers() {
                $('#taskTable tbody tr').each(function(index) {
                    $(this).find('.serial').val(index + 1);
                });
            }

            // Add row
            $('#addRow').click(function() {
                let rowCount = $('#taskTable tbody tr').length + 1;
                let newRow = `
      <tr>
        <td width="5%"><input type="text" class="form-control serial text-center" value="${rowCount}" readonly></td>
        <td><input type="text" class="form-control task" name="todo[]" placeholder="Enter task"></td>
        <td width="5%"><div class="d-flex justify-content-center">
            <button type="button" class="btn btn-light rounded-0 btn-sm deleteRow"><i
                                                                class="ico icon-outline-trash-bin-minimalistic"
                                                                style="font-size: 16px"></i></button>
             </div></td>
      </tr>`;
                $('#taskTable tbody').append(newRow);
            });

            // Delete row
            $(document).on('click', '.deleteRow', function() {
                $(this).closest('tr').remove();
                updateSerialNumbers();
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            // Function to update serial numbers
            function updateSerialNumbersEdit() {
                $('#taskTableEdit tbody tr').each(function(index) {
                    $(this).find('.serial').val(index + 1);
                });
            }

            // Add row
            $('#addRowEdit').click(function() {
                let rowCount = $('#taskTableEdit tbody tr').length + 1;
                let newRow = `
      <tr>
        <td width="5%"><input type="text" class="form-control serial text-center" value="${rowCount}" readonly></td>
        <td><input type="text" class="form-control task" name="edit_todo[]" placeholder="Enter task"></td>
        <td width="5%"><div class="d-flex justify-content-center">
            <button type="button" class="btn btn-light rounded-0 btn-sm deleteRowEdit"><i
                                                                class="ico icon-outline-trash-bin-minimalistic"
                                                                style="font-size: 16px"></i></button>
             </div></td>
      </tr>`;
                $('#taskTableEdit tbody').append(newRow);
            });

            // Delete row
            $(document).on('click', '.deleteRowEdit', function() {
                $(this).closest('tr').remove();
                updateSerialNumbers();
            });
        });
    </script>

    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@endsection

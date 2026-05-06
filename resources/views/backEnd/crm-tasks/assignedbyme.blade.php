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

                localStorage.setItem('listViewTasksAssignedByMe', 'long');
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

                localStorage.setItem('listViewTasksAssignedByMe', 'short');

            }


        }


        //added ny kp
        function toggleLongFilters() {

            document.querySelectorAll('#filters-long .filter-field').forEach(el => {
                el.classList.toggle('d-none');
            });
        }




        // Initialize view from localStorage
        document.addEventListener('DOMContentLoaded', () => {
            const savedView = localStorage.getItem('listViewTasksAssignedByMe');
            if (savedView === 'long') {
                isFullList = false; // so that toggling once activates full view
                list_style_new();
            } else {
                // Default to short view
                isFullList = true; // so that toggling once activates short view
                list_style_new();
            }

            // Attach event to sidebar links to force short view on navigation
            document.querySelectorAll('.sub-nav-item').forEach(link => {
                link.addEventListener('click', () => {
                    localStorage.setItem('listViewTasksAssignedByMe', 'short');
                });
            });



        });
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
            background: #deebe1;
            color: #212529;
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




        .comment-btn-wrapper {
            position: relative;
            display: inline-block;
        }

        .comment-badge {
            position: absolute;
            top: 1px;
            right: -3px;
            background: #d9534f;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            content: "";
        }
    </style>

    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>

    <?php try { ?>

    <aside class="left-nav col-3" id="leftSidebar">
        <div class="resizer" id="sidebarResizer"></div>
        <div class="short-list" id="filters-short">
            <h4 class="mb-2">
                @if ($current_status == 'pending')
                    Tasks Assigned By Me - Pending ({{ $taskCounts->pending_count ?? 0 }})
                @elseif ($current_status == 'completed')
                    Tasks Assigned By Me - Completed ({{ $taskCounts->completed_count ?? 0 }})
                @else
                    Tasks Assigned By Me List ({{ $taskCounts->completed_count + $taskCounts->pending_count }})
                @endif
            </h4>

            {{-- {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'purchase-order', 'method' => 'get', 'id' => 'purchase-order-search']) }} --}}

            <div class="search-filter-container mb-4">
                <div class="input-group flex-nowrap">
                    <input type="text" name="documents_number" id="document_number" class="form-control"
                        placeholder="Document No" aria-label="Search" aria-describedby="addon-wrapping" value="">
                </div>



                {{-- {{ Form::close() }} --}}
                <button type="button" class="btn btn-light" id="list_style_button" onclick="list_style_new()">
                    <i class="ico icon-outline-list-down"></i>
                </button>
            </div>



        </div>

        <div class="long-list  d-none" id="filters-long">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="mb-2">
                    @if ($current_status == 'pending')
                        Tasks Assigned By Me - Pending ({{ $taskCounts->pending_count ?? 0 }})
                    @elseif ($current_status == 'completed')
                        Tasks Assigned By Me - Completed ({{ $taskCounts->completed_count ?? 0 }})
                    @else
                        Tasks Assigned By Me List ({{ $taskCounts->completed_count + $taskCounts->pending_count }})
                    @endif
                </h4>
                <div class="search-filter-container mb-0">

                    <input type="text" id="tableSearch" class="form-control"
                        style="font-size:13px; width: 350px; 
                        top: 12px;
                        right: 120px;"
                        placeholder="Search">

                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ico icon-outline-hamburger-menu"></i>
                        </button>

                        <ul class="dropdown-menu" style="">

                            <li>
                                <a href="{{ url('tasks-assigned-by-me/pending') }}"
                                    class="dropdown-item d-flex align-items-center"><i
                                        class="ico icon-outline-document-text text-success title-15 me-2"></i> Pending Tasks
                                    {{-- ({{ $taskCounts->pending_count ?? 0 }}) --}}
                                </a>
                            </li>

                            <li>
                                <a href="{{ url('tasks-assigned-by-me/completed') }}"
                                    class="dropdown-item d-flex align-items-center"><i
                                        class="ico icon-outline-document-text text-success title-15 me-2"></i> Completed
                                    Tasks
                                    {{-- ({{ $taskCounts->completed_count ?? 0 }}) --}}
                                </a>
                            </li>

                            <li>
                                <a href="{{ url('crm-user-tasks') }}" class="dropdown-item d-flex align-items-center"><i
                                        class="ico icon-outline-document-text text-success title-15 me-2"></i> My Tasks
                                    {{-- ({{ $taskCounts_assigned_by->completed_count_assigned_by + $taskCounts_assigned_by->pending_count_assigned_by }}) --}}
                                </a>
                            </li>

                            <li>
                                <button type="button" data-bs-toggle="modal" data-bs-target="#ModalAddNewTask"
                                    class="dropdown-item d-flex align-items-center text-success"><i
                                        class="ico icon-outline-add-square  text-success title-15 me-2"></i> Assign
                                    Task</button>
                            </li>
                        </ul>
                    </div>



                    <button class="btn btn-light" onclick="toggleLongFilters()">
                        <i class="ico icon-outline-magnifer"></i>
                    </button>
                    <button class="btn btn-light" id="list_style_button" onclick="list_style_new()">
                        <i class="ico icon-outline-list-down"></i>
                    </button>
                </div>
            </div>

            <div class="search-filter-container mt-1 mb-4 filter-field d-none border">

                <div class="card">
                    <div class="card-body">


                    </div>
                </div>
            </div>
        </div>

        <div class="left-nav-list">

            <ul id="short-list" class="nav flex-column nav-pills" id="companyTabNavs" role="tablist">
                @if (count($assigned_tasks) > 0)
                    @foreach ($assigned_tasks as $item)
                        <li class="nav-item w-100" role="presentation">
                            <button class="nav-link task-item {{ $active_id == $item->id ? 'active' : '' }}"
                                data-id="{{ $item->id }}" id="purchase-order-1-tab" data-bs-toggle="tab"
                                data-bs-target="#purchase-order-1" type="button" role="tab"
                                aria-controls="purchase-order-1" aria-selected="true">
                                <div class="row w-100">
                                    <div class="col-4">
                                        <div class="form-control-plaintext">{{ @$item->task_id }}</div>
                                    </div>
                                    <div class="col-4 text-center">
                                        <div class="form-control-plaintext">
                                            {{ date('d/m/Y', strtotime(@$item->task_due_date)) }}</div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="form-control-plaintext truncate-text">
                                            {{ ucfirst($item->priority) }}
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-control-plaintext truncate-text">
                                            {{ $item->task_title }}</label>
                                    </div>
                                </div>
                            </button>
                        </li>
                    @endforeach
                @else
                    No Records
                @endif
            </ul>

            <div class="table-responsive mb-4 mt-4">
                <table id="long-list" class="table table-hover d-none data-table" style="table-layout: fixed;width:100%">

                    <thead>
                        <tr>
                            <th class="text-center" style="width: 60px;">@lang('Task ID')</th>
                            <th style="width: 170px;">@lang('Task Title')</th>
                            <th class="text-center" style="width: 100px;">@lang('Tasks')</th>
                            <th style="width: 120px;">@lang('Assigned To')
                            </th>
                            <th class="text-center" style="width: 100px;">@lang('Assigned Date & Time')
                            </th>
                            <th class="text-center" style="width: 100px;">@lang('Due Date & Time')
                            </th>
                            <th style="width: 60px;">@lang('Priority')</th>
                            <th style="width: 80px;">@lang('Progress')</th>
                            <th style="width: 80px;">@lang('Status')</th>
                            <th class="text-center" style="width: 30px;"> <i class="ico icon-bold-paperclip"></i> </th>
                            <th class="text-center" style="width: 90px;">@lang('Action')

                            </th>
                        </tr>
                    </thead>


                    <tbody>
                        @foreach ($assigned_tasks as $value)
                            <tr class="main-row" style="cursor: pointer;">

                                <td class="text-center" onclick="event.stopPropagation();">
                                    <a
                                        href="{{ url('crm-user-tasks' . ($current_status ? '/' . $current_status : '') . '/' . $value->id) }}">
                                        {{ $value->task_id }}</a>
                                </td>
                                <td class="">{{ $value->task_title }}
                                </td>
                                <td class="text-center">

                                    {{ $value->taskitems_count }}

                                </td>
                                <td>{{ $value->assignedto->full_name }}</td>
                                <td class="text-center">{{ date('d/m/Y h:i A', strtotime($value->created_at)) }}</td>
                                <td class="{{ $value->is_overdue && $value->status_s !== 'approved' ? 'text-danger' : '' }} text-center">
                                    {{ date('d/m/Y h:i A', strtotime($value->task_due_date)) }}</td>

                                <td>
                                    <span
                                        class=" text-{{ $value->priority == 'low' ? '' : ($value->priority == 'medium' ? '' : ($value->priority == 'high' ? 'dark' : 'danger')) }}">
                                        {{ ucfirst($value->priority) }}
                                    </span>
                                </td>
                                <td>
                                    <span
                                        class=" text-{{ $value->status_r == 'not_started' ? '' : ($value->status_r == 'in_progress' ? '' : ($value->status_r == 'completed' ? 'success' : ($value->status_r == 'blocked' ? 'dark' : ''))) }}">
                                        {{ ucwords(str_replace('_', ' ', $value->status_r)) }}
                                    </span>
                                </td>
                                <td>
                                    <span
                                        class=" text-{{ $value->status_s === 'open'
                                            ? ''
                                            : ($value->status_s === 'review'
                                                ? ''
                                                : ($value->status_s === 'approved'
                                                    ? 'success'
                                                    : ($value->status_s === 'modification'
                                                        ? ''
                                                        : ($value->status_s === 'rejected'
                                                            ? 'danger'
                                                            : '')))) }}">
                                        {{ ucwords(str_replace('_', ' ', $value->status_s)) }}
                                    </span>
                                </td>

                                <td class="text-center" onclick="event.stopPropagation();">
                                    @if (!empty($value->attachment))
                                        <a href="{{ asset('public/uploads/crm_user_tasks/' . $value->attachment) }}"
                                            target="_blank" class="text-decoration-none text-primary"
                                            title="View Attachment">
                                            <i class="ico icon-bold-paperclip"></i>
                                        </a>
                                    @endif
                                </td>
                                <td class="text-center" onclick="event.stopPropagation();">

                                    <div class="d-flex justify-content-center align-items-center">
                                        <a type="button" class="btn btn-sm btn-light update-status-btn"
                                            data-id="{{ $value->id }}" data-task_id="{{ $value->task_id }}"
                                            data-status="{{ $value->status_s }}" data-title="{{ $value->task_title }}"
                                            data-due="{{ date('d/m/Y h:i A', strtotime($value->task_due_date)) }}"
                                            data-priority="{{ $value->priority }}">
                                            <i class="ico icon-outline-pen-2 text-dark" style="font-size: 16px;"></i>
                                        </a>
                                        <a type="button" class="btn-sm btn btn-light btn-comments comment-btn-wrapper"
                                            data-task-id="{{ $value->id }}"
                                            data-task-item-taskid="{{ $value->task_id }}">
                                            <i class="ico icon-outline-chat-round-dots" aria-hidden="true"
                                                style="font-size: 16px"></i>
                                            @if ($value->unread_main_comments_count > 0)
                                                <span class="comment-badge">

                                                </span>
                                            @endif
                                        </a>
                                    </div>



                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </aside>

    <div class="content-container col-9">
        <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">

            <script>
                $(document).ready(function() {
                    $('.task-item').on('click', function() {
                        var id = $(this).data('id');
                        console.log(id)
                        $('.task-item').removeClass('active');
                        $('.task-item[data-id="' + id + '"]').addClass('active');

                        // Update the browser URL to include selected ID (without reloading)
                        var newUrl =
                            "{{ url('tasks-assigned-by-me') }}@if ($current_status)/{{ $current_status }}@endif/" +
                            id;
                        window.history.pushState({
                            path: newUrl
                        }, '', newUrl);

                        var action = "{{ URL::to('crm-user-tasks-assigned-details') }}/" + id;
                        $('#loading_bg').show();

                        $.ajax({
                            url: action,
                            method: 'GET',
                            success: function(response) {
                                $('#po-details').html(response);
                            },
                            error: function(xhr, status, error) {
                                let message = "No Details Available.";

                                if (xhr.status === 404) {
                                    message = "Task not found. It may have been deleted.";
                                } else if (xhr.status === 500) {
                                    message = "Server error. Please try again later.";
                                } else if (xhr.responseText) {
                                    message = xhr.responseText;
                                }

                                $('#po-details').html('<p class="text-danger">' + message + '</p>');
                                console.error("Error: ", status, error, xhr.responseText);
                            },
                            complete: function() {
                                $('#loading_bg').hide(); // Always hide loader after request completes
                            }
                        });
                    });
                });
            </script>




            <div class="" role="tabpanel" aria-labelledby="po-tab" id="po-details">
                @if (!empty($selectedTask) && is_array($selectedTask))
                    @include('backEnd.crm-tasks.viewassignedtask', $selectedTask)
                @else
                    <p class="text-danger">No details available.</p>
                @endif
            </div>


        </div>
    </div>




    <div class="modal  fade" id="ModalAddNewTask" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="top: 10%">
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-user-tasks', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel">Assign Task
                        ({{ @App\SysHelper::get_new_code('sys_crm_user_tasks', 'TA', 'task_id') }}) </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body">
                            <div class="row gap-rows">


                                <div class="col-12">
                                    <label for="" class="form-label">Task Title <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="task_title" id="task_title"
                                        required>
                                </div>

                                <div class="col-3">
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

                                <div class="col-3">
                                    <label for="" class="form-label">Assigned To <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control js-example-basic-single" name="assigned_to"
                                        id="assigned_to" required>
                                        <option value="">-Select-</option>
                                        @if (count($staff_list) > 0)
                                            @foreach ($staff_list as $value)
                                                <option value="{{ $value->user_id }}">{{ $value->full_name }}</option>
                                            @endforeach
                                        @endif

                                    </select>
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Due Date & Time <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control date-time-picker" name="task_due_date"
                                        id="task_due_date" required min="{{ now()->format('Y-m-d\T00:00') }}"
                                        onchange="">
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Attachment</label>
                                    <input type="file" class="form-control" name="attachment" id="attachment">
                                </div>



                                <div class="col-12 mt-2">
                                    <label for="" class="form-label">Description</label>
                                    <textarea name="description" id="task_description" class="form-control" style="height: 80px"
                                        placeholder="Enter description..."></textarea>
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
                                                    <td><input type="text" name="task[]" class="form-control task"
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


    <div class="modal side-panel fade" id="ModalTaskUpdate" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="top: 10%">
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-task-status-update', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel">Update Status (<span id="modal_task_id_text"></span>)
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body">
                            <div class="row gap-rows">
                                <input type="hidden" name="task_id" id="task_id">
                                <input type="hidden" name="task_item_id" id="task_item_id">

                                <div class="col-4">
                                    <p class="mb-1 text-muted"><strong>Title</strong></p>
                                    <p id="modal_task_title" class="fw-semibold text-dark"></p>
                                </div>
                                <div class="col-4">
                                    <p class="mb-1 text-muted"><strong>Due Date</strong></p>
                                    <p id="modal_task_due_date" class="fw-semibold text-dark"></p>
                                </div>
                                <div class="col-4">
                                    <p class="mb-1 text-muted"><strong>Priority</strong></p>
                                    <p id="modal_task_priority" class="fw-semibold text-capitalize text-dark"></p>
                                </div>


                                <div class="col-12 mb-2">

                                    <label for="status_s" class="form-label">
                                        Update Status <span class="text-danger">*</span>
                                    </label>
                                    <select name="status_s" id="status_s" class="form-control input-sm" required>
                                        <option value="">-- Select Status --</option>
                                        <option value="open">Open</option>
                                        <option value="review">In Review</option>
                                        <option value="approved">Approved</option>
                                        <option value="modification">Sent To Modification</option>
                                        <option value="rejected">Rejected</option>
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


    <div class="modal  fade" id="ModalTaskComments" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="top: 10%">

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel">Task Comments (<span id="task_item_task_id"></span>)
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0" style="box-shadow: none;">
                        <div class="card-body">
                            <div id="commentsContainer" style="max-height: 400px; overflow-y: auto;"></div>
                            <textarea id="newComment" class="form-control mt-3" cols="10" rows="3"
                                placeholder="Write your comment..."></textarea>


                            <input type="hidden" id="currentTaskId" />
                            <input type="hidden" id="task_item_id" />
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button id="submitComment" class="btn btn-sm btn-light"> <i class="ico icon-bold-map-arrow-right"
                            style="font-size:16px"></i> Send</button>

                    {{-- <button class="btn btn-danger mt-2 pull-right" type="button" data-dismiss="modal">Cancel</button> --}}
                </div>
            </div>
        </div>
    </div>



    <script>
        $(document).ready(function() {
            $('.update-status-btn').click(function() {
                openUpdateProgressModal(
                    $(this).data('id'),
                    $(this).data('task_item_id'),
                    $(this).data('status'),
                    $(this).data('task_id'),
                    $(this).data('title'),
                    $(this).data('priority'),
                    $(this).data('due')
                );

            });



            $('.btn-comments').click(function() {
                openCommentsModal(
                    $(this).data('task-id'),
                    $(this).data('task-item-id'),
                    $(this).data('task-item-taskid')
                );
                // var taskId = $(this).data('task-id');
                // var taskItemId = $(this).data('task-item-id') || '';
                // var taskItemTaskId = $(this).data('task-item-taskid') || '';
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
                            '<small>' + new Date(newComment.created_at).toLocaleString(
                                'en-US', {
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
                        $('#commentsContainer').scrollTop($('#commentsContainer')[0]
                            .scrollHeight);
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", xhr);
                        alert("Error: " + xhr.responseJSON?.message || error);
                    }
                });
            });



        });


        function openUpdateProgressModal(taskId, taskItemId, status, maintaskid, title, priority, dueDate) {
            $('#task_id').val(taskId);
            $('#task_item_id').val(taskItemId || '');
            $('#status_s').val(status);

            // Fill text fields
            $('#modal_task_id_text').text(maintaskid);
            $('#modal_task_title').text(title);

            // Priority handling
            var $prioritySpan = $('#modal_task_priority');
            $prioritySpan.removeClass('text-success text-dark text-danger');

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
                    $prioritySpan.text(priority); // fallback
            }

            // Due date
            $('#modal_task_due_date').text(dueDate);

            // Show modal
            $('#ModalTaskUpdate').modal('show');
        }



        function openCommentsModal(taskId, taskItemId, taskItemTaskId) {
            $('#task_item_id').val(taskItemId);
            $('#task_item_task_id').text(taskItemTaskId);

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
                            '<p id="no-comments" class="text-muted">No comments yet.</p>'
                        );
                    } else {

                        comments.forEach(function(comment) {

                            var avatarInitial = comment.user.full_name.charAt(0)
                                .toUpperCase();

                            $('#commentsContainer').append(
                                '<div class="chat-msg"> <div class="avatar">' +
                                avatarInitial + '</div>' +
                                '<div class="chat-msg-content">' +
                                '<strong>' + comment.user.full_name +
                                '</strong>' +
                                '<small>' + new Date(comment.created_at)
                                .toLocaleString(
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
                        $('#commentsContainer').scrollTop($('#commentsContainer')[0]
                            .scrollHeight);
                    }
                }
            });
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
        <td><input type="text" class="form-control task" name="task[]" placeholder="Enter task"></td>
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

            $('#document_number').on('input', function() {

                var query = $(this).val();

                console.log(query);

                $.ajax({
                    url: "{{ route('crm-user-my-tasks.search') }}",
                    type: "GET",
                    data: {
                        query: query
                    },
                    success: function(data) {
                        $('#short-list').html('');



                        if (data.length > 0) {
                            $.each(data, function(index, record) {
                                console.log(record);

                                let ims = `  <li class="nav-item w-100" role="presentation">
                            <button class="nav-link task-item"
                                data-id="${record.id}" id="purchase-order-1-tab" data-bs-toggle="tab"
                                data-bs-target="#purchase-order-1" type="button" role="tab"
                                aria-controls="purchase-order-1" aria-selected="true">
                                <div class="row w-100">
                                    <div class="col-4">
                                        <div class="form-control-plaintext">${record.task_id}</div>
                                    </div>
                                    <div class="col-4 text-center">
                                        <div class="form-control-plaintext">
                                            ${get_format_date(record.task_due_date)}</div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="form-control-plaintext truncate-text">
                                          ${record.priority ? record.priority.charAt(0).toUpperCase() + record.priority.slice(1) : '-'}

                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-control-plaintext truncate-text">
                                            ${record.task_title}</label>
                                    </div>
                                </div>
                            </button>
                        </li>`;








                                $('#short-list').append(ims);
                            });
                        } else {
                            $('#short-list').html('<div class="p-2">No results found</div>');
                        }
                    }
                });
            });

        });
    </script>

    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>


@endsection

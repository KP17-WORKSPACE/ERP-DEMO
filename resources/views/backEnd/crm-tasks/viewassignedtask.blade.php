<?php
$module_links = [];
$permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
?>

<?php try { ?>

<div class="purchase-order-content-header">
    <h4 class="purchase-order-content-header-left">
        {{ $task->task_id }}
    </h4>
    <div class="purchase-order-content-header-right">

        <button type="button" class="btn btn-light btn-comments-view" data-task-id="{{ $task->id }}"
            data-task-item-taskid="{{ $task->task_id }}">
            <i class="ico icon-outline-chat-round-dots" aria-hidden="true" style="font-size: 16px"></i> Comment
        </button>

        <button type="button" data-bs-toggle="modal" data-bs-target="#ModalAddNewTask" class="btn btn-light">
            <i class="ico icon-outline-add-square text-success"></i> Add
        </button>




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


    </div>
</div>



<div class="card mb-3">
    <div class="card-body">
        <!-- <h4 class="mb-3 color-sub-head">Supplier Info (SUPS-1060)</h4> -->
        <div class="d-flex align-items-center mb-3">
            <div class="font-weight-600 title-15 me-3"> {{ $task->task_title }}    @if ($task->priority == 'critical')
                        <span class="badge bg-danger">Critical</span>
                    @elseif ($task->priority == 'high')
                        <span class="badge bg-warning">High</span>
                    @elseif ($task->priority == 'medium')
                        <span class="badge bg-info">Medium</span>
                    @elseif ($task->priority == 'low')
                        <span class="badge bg-success">Low</span>
                    @else
                        <span class="badge bg-secondary">N/A</span>
                    @endif
            </div>




        </div>
        <div class="row">


            <div class="col-2 mb-3">
                <label class="form-label">Assigned Date & Time:</label>
                <div class="form-control-plaintext"> {{ date('d/m/Y h:i A', strtotime($task->created_at)) }}
                </div>
            </div>

            <div class="col-2 mb-3">
                <label class="form-label">Due Date & Time:</label>
                <div class="form-control-plaintext {{ $task->is_overdue ? 'text-danger' : '' }}">
                    {{ date('d/m/Y h:i A', strtotime($task->task_due_date)) }}
                </div>
            </div>
            <div class="col-2 mb-3">
                <label class="form-label">Assigned To:</label>
                <div class="form-control-plaintext"> {{ $task->assignedto->full_name }}
                </div>
            </div>

            <div class="col-1-5 mb-3">
                <label class="form-label">Priority:</label>
                <div class="form-control-plaintext">
                    @if ($task->priority == 'critical')
                        <span class="text-danger">Critical</span>
                    @elseif ($task->priority == 'high')
                        <span class="">High</span>
                    @elseif ($task->priority == 'medium')
                        <span class="">Medium</span>
                    @elseif ($task->priority == 'low')
                        <span class=" text-success">Low</span>
                    @else
                        <span class="">N/A</span>
                    @endif
                </div>
            </div>

            <div class="col-1-5 mb-3">
                <label class="form-label">Progress:</label>
                <div class="form-control-plaintext"> {{ ucwords(str_replace('_', ' ', $task->status_r)) }}
                </div>
            </div>

            <div class="col-1-5 mb-3">
                <label class="form-label">Status:</label>
                <div class="form-control-plaintext">{{ ucwords(str_replace('_', ' ', $task->status_s)) }}
                </div>
            </div>






            <div class="col-1-5 mb-3">
                <label class="form-label">Attachment:</label>
                <div class="form-control-plaintext"> <a target="_blank" class="btn-sm btn-light text-dark"
                        href="{{ asset('public/uploads/crm_user_tasks/' . $task->attachment) }}"><i
                            class="ico icon-bold-download-minimalistic text-success fw-bold title-15"></i> Download</a>
                </div>
            </div>




            <div class="col-11">
                <label class="form-label">Description:</label>
                <div class="form-control-plaintext"> {{ $task->description }}
                </div>
            </div>

        </div>
    </div>
</div>

<div class="tab-wrap mb-3">
    <ul class="nav nav-tabs" id="dealTrackTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="task-info-tab" data-bs-toggle="tab" data-bs-target="#task-info"
                type="button" role="tab" aria-controls="task-info" aria-selected="true">Tasks</button>
        </li>


    </ul>
    <div class="tab-content mb-3" id="dealTrackTabsContent">
        <div class="tab-pane fade show active" id="task-info" role="tabpanel" aria-labelledby="task-info-tab">

            <div class="row">

                <div class="col-8">
                    <div class="table-responsive mb-4 mt-4">
                        <table id="long-list" class="table table-hover" style="table-layout: fixed;width:100%">

                            <thead>
                                <tr>

                                    <th style="width: 170px;">@lang('Task Title')</th>
                                    <th style="width: 50px;">@lang('Progress')</th>
                                    <th style="width: 50px;">@lang('Status')</th>
                                    <th class="text-center" style="width: 50px;">@lang('Action')

                                    </th>
                                </tr>
                            </thead>


                            <tbody>
                                @foreach ($task->taskitems as $value)
                                    <tr>

                                        <td class="">{{ $value->task }}
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

                                            <div class="d-flex justify-content-start align-items-center">

                                                
                                                @if ($value->attachment != '')
                                                    <a href="{{ asset('public/uploads/crm_user_tasks/' . $task->attachment) }}"
                                                        target="_blank" class="btn btn-sm btn-light">
                                                        <i class="ico icon-bold-paperclip text-dark"
                                                            style="font-size: 16px;"></i>
                                                    </a>
                                                @endif

                                                <a type="button"
                                                    class="btn btn-sm btn-light update-progress-btn-view"
                                                    data-id="{{ $task->id }}"
                                                    data-task_id="{{ $task->task_id }}"
                                                    data-task_item_id="{{ $value->id }}"
                                                    data-status="{{ $value->status_s }}"
                                                    data-title="{{ $value->task }}"
                                                    data-due="{{ date('d/m/Y h:i A', strtotime($task->task_due_date)) }}"
                                                    data-priority="{{ $task->priority }}">
                                                    <i class="ico icon-outline-pen-2 text-dark"
                                                        style="font-size: 16px;"></i>
                                                </a>
                                                <a type="button"
                                                    class="btn-sm btn btn-light btn-comments-view comment-btn-wrapper"
                                                    data-task-id="{{ $task->id }}"
                                                    data-task-item-id="{{ $value->id }}"
                                                    data-task-item-taskid="{{ $task->task_id }}">
                                                    <i class="ico icon-outline-chat-round-dots" aria-hidden="true"
                                                        style="font-size: 16px"></i>

                                                    @if ($value->unread_comments_count > 0)
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


            </div>


        </div>



    </div>
</div>

<script>
    $(document).ready(function() {

        $('.update-progress-btn-view').click(function() {
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

        $('.btn-comments-view').click(function() {
            openCommentsModal(
                $(this).data('task-id'),
                $(this).data('task-item-id'),
                $(this).data('task-item-taskid')
            );

        });
    });
</script>



<?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

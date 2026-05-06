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
    </style>


    <?php
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>

    <?php try { ?>
    <div class="container-fluid">
        <div class="d-sm-flex justify-content-between">
            <div class="mb-3">
                <h2 class="page-heading m-0">Task {{ $task->task_id }}</h2>
                <span class="page-label">Home - Task - {{ $task->task_id }}</span>
            </div>
            <div>
              

            </div>
        </div>


        <div class="card p-4 d-flex mb-3">
            <div class="row justify-content-center">
                <div class="col-md-10 p-4 border rounded">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="" class="form-label">Task Title</label>
                                <input type="text" class="form-control" name="task_title" id="task_title"
                                    value="{{ $task->task_title }}" disabled readonly>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Task Priority</label>
                                <select class="form-control js-example-basic-single" name="priority" id="priority" required
                                    disabled readonly>
                                    <option value="">-Select-</option>
                                    <option value="critical" {{ $task->priority == 'critical' ? 'selected' : '' }}>Critical
                                    </option>
                                    <option value="high" {{ $task->priority == 'high' ? 'selected' : '' }}>High</option>
                                    <option value="medium" {{ $task->priority == 'medium' ? 'selected' : '' }}>Medium
                                    </option>
                                    <option value="low" {{ $task->priority == 'low' ? 'selected' : '' }}>Low</option>
                                </select>
                            </div>
                        </div>


                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Assigned To</label>
                                <select class="form-control js-example-basic-single" name="assigned_to" id="assigned_to"
                                    required disabled readonly>
                                    <option value="">-Select-</option>
                                    @if (count($staff_list) > 0)
                                        @foreach ($staff_list as $value)
                                            <option value="{{ $value->user_id }}"
                                                {{ $task->assigned_to == $value->user_id ? 'selected' : '' }}>
                                                {{ $value->full_name }}</option>
                                        @endforeach
                                    @endif

                                </select>
                            </div>
                        </div>


                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Due Date & Time</label>
                                <input type="datetime-local" class="form-control" name="task_due_date" id="task_due_date"
                                    required disabled readonly min="{{ now()->format('Y-m-d\T00:00') }}"
                                    value="{{ $task->task_due_date }}" onchange="">
                            </div>
                        </div>


                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Attachment</label> <br>
                                @if (!empty($task->attachment))
                                    <a href="{{ asset('public/uploads/crm_user_tasks/' . $task->attachment) }}"
                                        target="_blank" class="btn btn-primary" title="View Attachment">
                                        <i class="fa fa-paperclip me-1"></i> View Attachment
                                    </a>
                                @else
                                    <span class="text-muted" title="No attachment">
                                        <i class="fa fa-ban me-1"></i>No Attachment
                                    </span>
                                @endif
                                {{-- <input type="file" class="form-control" name="attachment" id="attachment"> --}}
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Tasks</label>


                                <table width="100%">
                                    @foreach ($task->taskitems as $item)
                                        <tr>
                                            <td width="1%">{{ $loop->iteration }}. </td>
                                            <td><input type="text" class="form-control" name="task[]" id="task_1"
                                                    value="{{ $item->task }}" readonly disabled>
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>

                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Description</label>
                                <textarea class="form-control" name="description" id="description" rows="8" required disabled readonly>{{$task->description}}</textarea>
                            </div>
                        </div>
                    </div>
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
                                <label for="" class="form-label">Task Title</label>
                                <input type="text" class="form-control" name="task_title" id="task_title" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Task Priority</label>
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
                                <label for="" class="form-label">Assigned To</label>
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
                                <label for="" class="form-label">Due Date & Time</label>
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
                                <label for="" class="form-label">Tasks</label>
                                {{-- <a onclick="add_task()" class="btn-sm btn-primary float-right"><i class="fa fa-plus-square"
            aria-hidden="true"></i></a> --}}

                                <button type="button" class="btn btn-sm btn-info btn-add-task  float-right"
                                    onclick="add_task()">
                                    <i class="fa fa-plus"></i> Add Task
                                </button>

                                <table width="100%">
                                    <tr>
                                        <td width="1%">1. </td>
                                        <td><input type="text" class="form-control" name="task[]" id="task_1">
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
                                <textarea class="form-control" name="description" id="description" rows="4" required></textarea>
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





    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@endsection

<?php
$accountgroup = @App\SysAccountGroup::where('status',1)->get();
$accountgroupsub = App\SysAccountGroupSub::where('status', 1)->orderBy('group_id')->get();
?>
    
<div class="modal side-panel modal-draggable fade" id="groupModal" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editModalLabel">Group</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            @if (isset($editData))
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'accountgroupsub-update/' . @$editData->id, 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}
            @else
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'accountgroupsub-store', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
            @endif
                    <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
                    <input type="hidden" name="date_of_joining" id="date_of_joining" value="{{ date('Y-m-d') }}">
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body bg-white">
                        <div class="row">
                        <div class="col-lg-12 mb-4">
                            <div class="input-effect">
                                <label class="txtlbl"> @lang('Group Name') <span>*</span> </label>
                                <input class="form-control {{ $errors->has('title') ? 'is-invalid' : ' ' }}"
                                    type="text" id="title" name="title"
                                    value="{{ isset($editData) ? @$editData->title : old('title') }}">
                                <span class="focus-border"></span>

                                @if ($errors->has('title'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-12 mb-4">
                            <div class="input-effect">
                                <label class="txtlbl"> @lang('Select Heads') <span>*</span> </label>
                                <select class="form-control{{ $errors->has('group_id') ? ' is-invalid' : '' }} js-example-basic-single"
                                    name="group_id" id="group_id">
                                    <option value=""></option>
                                    @if (isset($accountgroup))
                                        @foreach ($accountgroup as $val)
                                            <option value="{{ @$val->id }}"
                                                @if (isset($editData)) @if (@$editData->group_id == @$val->id) selected @endif
                                                @endif
                                                {{-- {{ old('country') == @$countri->id ? 'selected' : '' }} --}}
                                                >{{ @$val->title }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <span class="focus-border"></span>
                                @if ($errors->has('group_id'))
                                    <span class="invalid-feedback invalid-select" role="alert">
                                        <strong>{{ $errors->first('group_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-light add-btn ms-2" id="btnSubmitGroup">
                    <i class="ico icon-outline-bookmark-opened text-success"></i>
                    @if (isset($editData)) @lang('lang.save')
                    @else
                        @lang('lang.save') @endif
                </button>
            </div>
                    {{ Form::close() }}
        </div>
    </div>
</div>

       <script>
$(document).on('click', '#groupModal .btn-close', function() {
    var modal = $('#groupModal');

    // Clear all text inputs inside the modal
    modal.find('input[type="text"]').val('');

    // Reset all select fields inside the modal
    modal.find('select').val('');

    console.log("Modal fields reset on close button click");
});
</script>

    
<script>
    $(document).ready(function() {
       

        // validation before submit
        $('#groupModal form').on('submit', function(e) {
            var title = $.trim($('#title').val());
            var head  = $('#group_id').val();
            if (title === '' || head === '' || head === null) {
                e.preventDefault();
                toastr.error('Please fill in all required fields.', 'Error');
                $('#title').focus();
            }
        });
    });
</script>

<div class="modal modal-draggable fade" id="GroupTableModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editGroupModalLabel"
    aria-hidden="true">
      <style>
        #table-head th {
            position: sticky;
            top: 0;
            z-index: 2;
        }
   
    </style>
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" style=" padding-left: 11px;" id="editGroupModalLabel">Groups</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card m-0 p-0">
                    <div class="card-body bg-white p-0">
                        <table class="table table-hover bordered-table" id="long-list" style="table-layout: fixed;width:100%">
                            <thead id="table-head">
                                @if (session()->has('message-success-delete') != '' || session()->get('message-danger-delete') != '')
                                    <tr>
                                        <td colspan="6">
                                            @if (session()->has('message-success-delete'))
                                                <div class="alert alert-success">
                                                    {{ session()->get('message-success-delete') }}
                                                </div>
                                            @elseif(session()->has('message-danger-delete'))
                                                <div class="alert alert-danger">
                                                    {{ session()->get('message-danger-delete') }}
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <th style="padding-left: 14px"> @lang('Main Heads')</th>
                                    <th > @lang('Group')</th>
                                    <th style="width:100px" class="text-center"> @lang('Status')</th>
                                    <th style="width:100px" class="text-center"> @lang('lang.action')</th>
                                </tr>
                            </thead>

                            <tbody>
                                @if (isset($accountgroupsub))
                                    @foreach ($accountgroupsub as $value)
                                        <tr >
                                            <td style="padding-left: 14px">
                                                {{ @$value->groupid->title }}
                                            </td>
                                            <td>
                                                {{ @$value->title }}
                                            </td>
                                            <td class="text-center">
                                                @if (@$value->status == 1)
                                                    <span class="text-success">Active</span>
                                                @else
                                                    <span class="text-danger">InActive</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center align-items-center">
                                                    @if (Auth::user()->role_id == 1)
                                                        <a class="btn btn-sm btn-light EditGroupBTN2"   data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                            data-bs-content="Edit Group"
                            data-bs-placement="top"
                                                            data-id="{{ $value->id }}" {{-- href="{{ url('accountgroupsub/' . @$value->id . '/edit') }}" --}}><i
                                                                style="font-size: 16px"
                                                                class="ico icon-outline-pen-2"></i></a>
                                                        <a class="btn btn-sm btn-light" data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                            data-bs-content="Delete Group"
                            data-bs-placement="top"
                                                            href="{{ url('accountgroupsub/' . @$value->id . '/delete') }}"
                                                            onclick="return confirm('Are you sure you want to delete this item?');"><i
                                                                style="font-size: 16px"
                                                                class="ico icon-outline-trash-bin-minimalistic"></i></a>
                                                    @endif
                                                </div>

                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>



        </div>
    </div>
</div>


<!-- Edit Group Modal -->
<div class="modal modal-draggable side-panel fade" id="editGroupModal2" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editGroupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editGroupModalLabel">Edit Group</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body bg-white">

                        {{ Form::open(['id' => 'editGroupForm2', 'class' => 'form-horizontal', 'files' => true, 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}

                        <input type="hidden" name="url" id="edit_url" value="{{ URL::to('/') }}">
                        <input type="hidden" name="date_of_joining" id="edit_date_of_joining"
                            value="{{ date('Y-m-d') }}">

                        <div class="row">
                            <div class="col-12 mb-4">
                                <div class="input-effect">
                                    <label class="txtlbl"> @lang('Group Name') <span>*</span> </label>
                                    <input class="form-control" type="text" id="edit_title" name="title"
                                        value="" required>
                                    <span class="focus-border"></span>
                                </div>
                            </div>

                            <div class="col-12 mb-4">
                                <div class="input-effect">
                                    <label class="txtlbl"> @lang('Select Main Heads') <span>*</span> </label>
                                    <select class="form-control js-example-basic-single" name="group_id" id="edit_group_id" required>
                                        <option value=""></option>
                                        @if (isset($accountgroup))
                                            @foreach ($accountgroup as $val)
                                                <option value="{{ $val->id }}">{{ $val->title }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <span class="focus-border"></span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-light" type="submit" id="edit_btnSubmit">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Update
                </button>
            </div>

            {{ Form::close() }}
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {

        $('.EditGroupBTN2').on('click', function() {
            let groupid = $(this).data('id');

            console.log("Group ID:", groupid);
            $("#loading_bg").show();

            $.ajax({
                url: '/accountgroupsub/' + groupid + '/get-edit',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.error) {
                        alert('Error: ' + response.message);
                        $("#loading_bg").hide();
                        return;
                    }

                    let editData = response.editData;

                    // Fill the form fields
                    $('#editGroupModal2 #edit_title').val(editData.title);
                    $('#editGroupModal2 #edit_group_id').val(editData.group_id).trigger(
                        'change');

                    // Set the form's action dynamically
                    $('#editGroupForm2').attr('action', '/accountgroupsub-update/' + editData
                        .id);

                    $("#loading_bg").hide();
                    $('#editGroupModal2').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    console.error('Response:', xhr.responseText);
                    alert('An error occurred while fetching data. Please try again later.');
                    $("#loading_bg").hide();
                }
            });
        });

    });
</script>
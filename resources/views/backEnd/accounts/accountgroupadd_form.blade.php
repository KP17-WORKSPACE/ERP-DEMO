<?php
$accountgroup = @App\SysAccountGroup::where('status',1)->orderBy('sort_id', 'asc')->get();
?>
    
<div class="modal side-panel modal-draggable fade" id="headModal" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editModalLabel">Main Head</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            @if (isset($editData))
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'accountgroup-update/' . @$editData->id, 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}
            @else
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'accountgroup-store', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
            @endif
                    <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
                    <input type="hidden" name="date_of_joining" id="date_of_joining" value="{{ date('Y-m-d') }}">
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body bg-white">
                        <div class="row">
                        <div class="col-lg-12 mb-4">
                            <div class="input-effect">
                                <label class="txtlbl"> @lang('Main Head Name') <span>*</span> </label>
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
                                <label class="txtlbl"> @lang('Group Code') </label>
                                    <input
    class="txtbx primary-input form-control {{ $errors->has('group_code') ? 'is-invalid' : ' ' }}"
    type="text" id="group_code" name="group_code"
    value="{{ isset($editData) ? $editData->group_code : (old('group_code') ?? @App\SysHelper::get_new_head_code()) }}" readonly>
                                <span class="focus-border"></span>

                                @if ($errors->has('group_code'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('group_code') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-12 mb-4">
                            <div class="input-effect">
                                <label class="txtlbl"> @lang('Sequence') <span>*</span> </label>
                                <input class="form-control {{ $errors->has('sort_id') ? 'is-invalid' : ' ' }}"
                                    type="number" id="sort_id" name="sort_id"
                                    value="{{ isset($editData) ? @$editData->sort_id : (old('sort_id') ?: App\SysHelper::get_next_sort_id('sys_account_group')) }}" required>
                                <span class="focus-border"></span>

                                @if ($errors->has('sort_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('sort_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-light add-btn ms-2" id="btnSubmitHead">
                    <i class="ico icon-outline-bookmark-opened text-success"></i>
                    @lang('lang.save')
                </button>
            </div>
                    {{ Form::close() }}
        </div>
    </div>
</div>

<script>
$(document).on('click', '#headModal .btn-close', function() {
    var modal = $('#headModal');
    modal.find('input[type="text"]').val('');
    console.log("Modal fields reset on close button click");
});
</script>
    
<script>
    $(document).ready(function() {
        // validation before submit
        $('#headModal form').on('submit', function(e) {
            var title = $.trim($('#title').val());
            var sort_id = $.trim($('#sort_id').val());
            if (title === '' || sort_id === '') {
                e.preventDefault();
                toastr.error('Please fill in all required fields.', 'Error');
                if (title === '') {
                    $('#title').focus();
                } else {
                    $('#sort_id').focus();
                }
            }
        });
    });
</script>

<div class="modal modal-draggable fade" id="HeadTableModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editHeadModalLabel"
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
                <h4 class="modal-title" style=" padding-left: 11px;" id="editHeadModalLabel">Main Heads</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card m-0 p-0">
                    <div class="card-body bg-white p-0">
                        <table class="table table-hover bordered-table" id="long-list" style="table-layout: fixed;width:100%">
                            <thead id="table-head">
                                <tr>
                                    <th style="padding-left: 14px"> @lang('Main Head')</th>
                                    <th > @lang('Group Code')</th>
                                    <th > @lang('Sequence')</th>
                                    <th style="width:100px" class="text-center"> @lang('Status')</th>
                                    <th style="width:100px" class="text-center"> @lang('lang.action')</th>
                                </tr>
                            </thead>

                            <tbody>
                                @if (isset($accountgroup))
                                    @foreach ($accountgroup as $value)
                                        <tr >
                                            <td style="padding-left: 14px">
                                                {{ @$value->title }}
                                            </td>
                                            <td>
                                                {{ @$value->group_code }}
                                            </td>
                                            <td>
                                                {{ @$value->sort_id }}
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
                                                        <a class="btn btn-sm btn-light EditHeadBTN2" data-bs-popover="popover"
                                                            data-bs-trigger="hover"
                                                            data-bs-delay="500"
                                                            data-bs-content="Edit Main Head"
                                                            data-bs-placement="top"
                                                            data-id="{{ $value->id }}"><i
                                                                style="font-size: 16px"
                                                                class="ico icon-outline-pen-2"></i></a>
                                                        <a class="btn btn-sm btn-light" data-bs-popover="popover"
                                                            data-bs-trigger="hover"
                                                            data-bs-delay="500"
                                                            data-bs-content="Delete Main Head"
                                                            data-bs-placement="top"
                                                            href="{{ url('accountgroup/' . @$value->id . '/delete') }}"
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

<!-- Edit Head Modal -->
<div class="modal modal-draggable side-panel fade" id="editHeadModal2" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editHeadModalLabel2" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editHeadModalLabel2">Edit Main Head</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body bg-white">

                        {{ Form::open(['id' => 'editHeadForm2', 'class' => 'form-horizontal', 'files' => true, 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}

                        <input type="hidden" name="url" id="edit_url" value="{{ URL::to('/') }}">
                        <input type="hidden" name="date_of_joining" id="edit_date_of_joining"
                            value="{{ date('Y-m-d') }}">

                        <div class="row">
                            <div class="col-12 mb-4">
                                <div class="input-effect">
                                    <label class="txtlbl"> @lang('Main Head Name') <span>*</span> </label>
                                    <input class="form-control" type="text" id="edit_title" name="title"
                                        value="" required>
                                    <span class="focus-border"></span>
                                </div>
                            </div>

                            <div class="col-12 mb-4">
                                <div class="input-effect">
                                    <label class="txtlbl"> @lang('Group Code') </label>
                                    <input class="form-control" type="text" id="edit_group_code" name="group_code"
                                        value="" readonly>
                                    <span class="focus-border"></span>
                                </div>
                            </div>

                            <div class="col-12 mb-4">
                                <div class="input-effect">
                                    <label class="txtlbl"> @lang('Sequence') <span>*</span> </label>
                                    <input class="form-control" type="number" id="edit_sort_id" name="sort_id"
                                        value="" required>
                                    <span class="focus-border"></span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-light" type="submit" id="edit_btnSubmitHead">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Update
                </button>
            </div>

            {{ Form::close() }}
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.EditHeadBTN2').on('click', function() {
            let headid = $(this).data('id');
            console.log("Head ID:", headid);
            $("#loading_bg").show();

            $.ajax({
                url: '{{ url("accountgroup") }}/' + headid + '/get-edit',
                //url: '/accountgroup/' + headid + '/get-edit',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.error) {
                        alert('Error: ' + response.message);
                        $("#loading_bg").hide();
                        return;
                    }

                    let editData = response.editData;
                    $('#editHeadModal2 #edit_title').val(editData.title);
                    $('#editHeadModal2 #edit_group_code').val(editData.group_code);
                    
                    var newHeadCode = "{{ App\SysHelper::get_new_head_code() }}";
                    $('#editHeadModal2 #edit_group_code').val(editData.group_code || newHeadCode);

                    $('#editHeadModal2 #edit_sort_id').val(editData.sort_id);
                    //$('#editHeadForm2').attr('action', '/accountgroup-update/' + editData.id);
                    $('#editHeadForm2').attr('action', '{{ url("accountgroup-update") }}/' + editData.id);

                    $("#loading_bg").hide();
                    $('#editHeadModal2').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    alert('An error occurred while fetching data.');
                    $("#loading_bg").hide();
                }
            });
        });
    });
</script>

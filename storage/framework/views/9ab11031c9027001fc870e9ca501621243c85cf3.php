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
            <?php if(isset($editData)): ?>
                <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'accountgroupsub-update/' . @$editData->id, 'method' => 'PUT', 'enctype' => 'multipart/form-data'])); ?>

            <?php else: ?>
                <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'accountgroupsub-store', 'method' => 'POST', 'enctype' => 'multipart/form-data'])); ?>

            <?php endif; ?>
                    <input type="hidden" name="url" id="url" value="<?php echo e(URL::to('/')); ?>">
                    <input type="hidden" name="date_of_joining" id="date_of_joining" value="<?php echo e(date('Y-m-d')); ?>">
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body bg-white">
                        <div class="row">
                        <div class="col-lg-12 mb-4">
                            <div class="input-effect">
                                <label class="txtlbl"> <?php echo app('translator')->getFromJson('Group Name'); ?> <span>*</span> </label>
                                <input class="form-control <?php echo e($errors->has('title') ? 'is-invalid' : ' '); ?>"
                                    type="text" id="title" name="title"
                                    value="<?php echo e(isset($editData) ? @$editData->title : old('title')); ?>">
                                <span class="focus-border"></span>

                                <?php if($errors->has('title')): ?>
                                    <span class="invalid-feedback" role="alert">
                                        <strong><?php echo e($errors->first('title')); ?></strong>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-lg-12 mb-4">
                            <div class="input-effect">
                                <label class="txtlbl"> <?php echo app('translator')->getFromJson('Select Heads'); ?> <span>*</span> </label>
                                <select class="form-control<?php echo e($errors->has('group_id') ? ' is-invalid' : ''); ?> js-example-basic-single"
                                    name="group_id" id="group_id">
                                    <option value=""></option>
                                    <?php if(isset($accountgroup)): ?>
                                        <?php $__currentLoopData = $accountgroup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e(@$val->id); ?>"
                                                <?php if(isset($editData)): ?> <?php if(@$editData->group_id == @$val->id): ?> selected <?php endif; ?>
                                                <?php endif; ?>
                                                
                                                ><?php echo e(@$val->title); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </select>
                                <span class="focus-border"></span>
                                <?php if($errors->has('group_id')): ?>
                                    <span class="invalid-feedback invalid-select" role="alert">
                                        <strong><?php echo e($errors->first('group_id')); ?></strong>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-light add-btn ms-2" id="btnSubmitGroup">
                    <i class="ico icon-outline-bookmark-opened text-success"></i>
                    <?php if(isset($editData)): ?> <?php echo app('translator')->getFromJson('lang.save'); ?>
                    <?php else: ?>
                        <?php echo app('translator')->getFromJson('lang.save'); ?> <?php endif; ?>
                </button>
            </div>
                    <?php echo e(Form::close()); ?>

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
                                <?php if(session()->has('message-success-delete') != '' || session()->get('message-danger-delete') != ''): ?>
                                    <tr>
                                        <td colspan="6">
                                            <?php if(session()->has('message-success-delete')): ?>
                                                <div class="alert alert-success">
                                                    <?php echo e(session()->get('message-success-delete')); ?>

                                                </div>
                                            <?php elseif(session()->has('message-danger-delete')): ?>
                                                <div class="alert alert-danger">
                                                    <?php echo e(session()->get('message-danger-delete')); ?>

                                                </div>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <tr>
                                    <th style="padding-left: 14px"> <?php echo app('translator')->getFromJson('Main Heads'); ?></th>
                                    <th > <?php echo app('translator')->getFromJson('Group'); ?></th>
                                    <th style="width:100px" class="text-center"> <?php echo app('translator')->getFromJson('Status'); ?></th>
                                    <th style="width:100px" class="text-center"> <?php echo app('translator')->getFromJson('lang.action'); ?></th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if(isset($accountgroupsub)): ?>
                                    <?php $__currentLoopData = $accountgroupsub; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr >
                                            <td style="padding-left: 14px">
                                                <?php echo e(@$value->groupid->title); ?>

                                            </td>
                                            <td>
                                                <?php echo e(@$value->title); ?>

                                            </td>
                                            <td class="text-center">
                                                <?php if(@$value->status == 1): ?>
                                                    <span class="text-success">Active</span>
                                                <?php else: ?>
                                                    <span class="text-danger">InActive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center align-items-center">
                                                    <?php if(Auth::user()->role_id == 1): ?>
                                                        <a class="btn btn-sm btn-light EditGroupBTN2"   data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                            data-bs-content="Edit Group"
                            data-bs-placement="top"
                                                            data-id="<?php echo e($value->id); ?>" ><i
                                                                style="font-size: 16px"
                                                                class="ico icon-outline-pen-2"></i></a>
                                                        <a class="btn btn-sm btn-light" data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                            data-bs-content="Delete Group"
                            data-bs-placement="top"
                                                            href="<?php echo e(url('accountgroupsub/' . @$value->id . '/delete')); ?>"
                                                            onclick="return confirm('Are you sure you want to delete this item?');"><i
                                                                style="font-size: 16px"
                                                                class="ico icon-outline-trash-bin-minimalistic"></i></a>
                                                    <?php endif; ?>
                                                </div>

                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
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

                        <?php echo e(Form::open(['id' => 'editGroupForm2', 'class' => 'form-horizontal', 'files' => true, 'method' => 'PUT', 'enctype' => 'multipart/form-data'])); ?>


                        <input type="hidden" name="url" id="edit_url" value="<?php echo e(URL::to('/')); ?>">
                        <input type="hidden" name="date_of_joining" id="edit_date_of_joining"
                            value="<?php echo e(date('Y-m-d')); ?>">

                        <div class="row">
                            <div class="col-12 mb-4">
                                <div class="input-effect">
                                    <label class="txtlbl"> <?php echo app('translator')->getFromJson('Group Name'); ?> <span>*</span> </label>
                                    <input class="form-control" type="text" id="edit_title" name="title"
                                        value="" required>
                                    <span class="focus-border"></span>
                                </div>
                            </div>

                            <div class="col-12 mb-4">
                                <div class="input-effect">
                                    <label class="txtlbl"> <?php echo app('translator')->getFromJson('Select Main Heads'); ?> <span>*</span> </label>
                                    <select class="form-control js-example-basic-single" name="group_id" id="edit_group_id" required>
                                        <option value=""></option>
                                        <?php if(isset($accountgroup)): ?>
                                            <?php $__currentLoopData = $accountgroup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($val->id); ?>"><?php echo e($val->title); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
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

            <?php echo e(Form::close()); ?>

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
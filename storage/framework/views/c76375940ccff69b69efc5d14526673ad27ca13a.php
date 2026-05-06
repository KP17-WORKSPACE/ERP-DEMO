<?php
$com_id = session('logged_session_data.company_id');
   $sub_accounts = @App\SysChartofAccounts::whereRaw("find_in_set($com_id,sys_chartofaccounts.company_access)")->where('main_account_id', '!=', 0)->orderBy('group', 'asc')->orderBy('subgroup', 'asc')->orderBy('subgroup2', 'asc')->get();
?>

<div class="modal modal-draggable side-panel fade" id="ModalMoveSubAccount" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="staticBackdropLabel">Move Sub Account</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <?php echo Form::open([
                    'class' => 'form-horizontal',
                    'files' => true,
                    'url' => 'sub-account-move',
                    'method' => 'post',
                    'id' => 'sub-account-move',
                ]); ?>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-2">Sub Account
                            <select id="from_subaccount" name="from_subaccount" class="form-control js-example-basic-single"
                                 required>
                                <?php $__currentLoopData = $sub_accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($data->id); ?>"><?php echo e($data->account_code); ?> -
                                        <?php echo e($data->account_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                                                  <?php
    $accountgroupsub = @App\SysAccountGroupSub2::where('status', 1)->orderBy('group_id')->get();
                            ?>

                         <div class="col-md-12 mb-2">Sub Group
                               <select id="subgroup_account" name="subgroup_account" class="form-control js-example-basic-single"
                                     required>

                                        <?php $__currentLoopData = $accountgroupsub; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($data->id); ?>"><?php echo e($data->title); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                </select>
                        </div>







                        
                    </div>
                </div>
                <div class="modal-footer">
                    <button  class="btn btn-light add-btn ms-2" type="submit"
                        >
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Move
                    </button>
                </div>
            
                <?php echo Form::close(); ?>


            </div>
        </div>
    </div>
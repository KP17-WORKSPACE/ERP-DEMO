<?php
$com_id = session('logged_session_data.company_id');
$sub_accounts = @App\SysChartofAccounts::whereRaw("find_in_set($com_id,sys_chartofaccounts.company_access)")->where('main_account_id', '!=', 0)->get();
?>

<div class="modal modal-draggable side-panel fade" id="ModalMergeSubAccount" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="staticBackdropLabel">Merge Sub Account</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <?php echo Form::open([
                    'class' => 'form-horizontal',
                    'files' => true,
                    'url' => 'sub-account-merge',
                    'method' => 'post',
                    'id' => 'sub-account-merge',
                ]); ?>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">From Sub Account
                            <select id="from_account" name="from_account[]" class="form-control js-example-basic-single"
                                multiple required>
                                <?php if(count($sub_accounts) > 0): ?>
                                    <?php $__currentLoopData = $sub_accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($data->id); ?>"><?php echo e($data->account_code); ?> -
                                            <?php echo e($data->account_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6">To Sub Account
                            <select id="to_account" name="to_account" class="form-control js-example-basic-single"
                                required>
                                <option value="">Select</option>
                                <?php if(count($sub_accounts) > 0): ?>
                                    <?php $__currentLoopData = $sub_accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($data->id); ?>"><?php echo e($data->account_code); ?> -
                                            <?php echo e($data->account_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light add-btn ms-2"
                        onclick="return confirm('Are you sure you want to Merge this?');">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Merge
                    </button>
                </div>
                <?php echo Form::close(); ?>


            </div>
        </div>
    </div>
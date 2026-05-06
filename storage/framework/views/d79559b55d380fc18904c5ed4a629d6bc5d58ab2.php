<?php $__env->startSection('mainContent'); ?>

    <?php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>
    <?php try { ?>

    <div class="content-container col-12">
        <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
            <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
                <div class="purchase-order-content-header">
                    <h4 class="purchase-order-content-header-left">
                        Payment Terms
                    </h4>
                    <div class="purchase-order-content-header-right">
                        <a class="btn btn-light text-dark" href="<?php echo e(url('payment-terms')); ?>">
                            <i class="ico icon-outline-add-square text-success"></i> Add
                        </a>

                         <div class="dropdown">
            <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="ico icon-outline-hamburger-menu"></i>
            </button>
            <ul class="dropdown-menu" style="">




                <li>
                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                        href="<?php echo e(url('company/policy')); ?>">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Company Policy
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="<?php echo e(url('/department')); ?>">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Department
                    </a>
                </li>

                <li>
                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                        href="<?php echo e(url('/designation')); ?>">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Designation
                    </a>
                </li>

                <li>
                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                        href="<?php echo e(url('/legal-entity')); ?>">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Business Entity
                    </a>
                </li>

                <li>
                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                        href="<?php echo e(url('/industry')); ?>">
                        <i class="ico icon-outline-layers text-success  title-15 me-2"></i>
                        Industry Type
                    </a>
                </li>

                <li>
                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                        href="<?php echo e(url('/business-activity')); ?>">
                        <i class="ico icon-outline-layers text-success  title-15 me-2"></i>
                        Business Sector
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="<?php echo e(route('role')); ?>">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Role
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="<?php echo e(url('module')); ?>">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Module
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="<?php echo e(route('base_setup')); ?>">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Base Setup
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="<?php echo e(route('daily-quotes.index')); ?>">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Daily Quote
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="<?php echo e(url('currency-settings')); ?>">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Manage Currency
                    </a>
                </li>


                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="<?php echo e(url('company')); ?>">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Company Settings
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="<?php echo e(url('payment-cheque-print-template')); ?>">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Cheque Print Templates
                    </a>
                </li>


                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="<?php echo e(url('shipping-add')); ?>">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Shipping
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="<?php echo e(url('vat-settings')); ?>">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        VAT Settings
                    </a>
                </li>


                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="<?php echo e(url('accountgroup-add')); ?>">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Main Heads
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="<?php echo e(url('book-close')); ?>">
                        <i class="ico icon-outline-settings text-success  title-15 me-2"></i>
                        Book Closed
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="<?php echo e(url('book-close-doc-number')); ?>">
                        <i class="ico icon-outline-settings text-success  title-15 me-2"></i>
                        Book Close Doc No
                    </a>
                </li>


            </ul>
        </div>

                    </div>
                </div>


                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <?php if(isset($editmode)): ?>
                                            <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'payment-terms/' . @$editmode->id, 'method' => 'PUT', 'enctype' => 'multipart/form-data'])); ?>

                                        <?php else: ?>
                                            <?php if(in_array(105, @$module_links) || Auth::user()->role_id == 1): ?>
                                                <?php echo e(Form::open([
                                                    'class' => 'form-horizontal',
                                                    'files' => true,
                                                    'url' => 'payment-terms',
                                                    'method' => 'POST',
                                                    'enctype' => 'multipart/form-data',
                                                ])); ?>

                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <div class="white-box">
                                            <div class="add-visitor">
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <?php if(session()->has('message-success')): ?>
                                                            <div class="alert alert-success">
                                                                <?php echo e(session()->get('message-success')); ?>

                                                            </div>
                                                        <?php elseif(session()->has('message-danger')): ?>
                                                            <div class="alert alert-danger">
                                                                <?php echo e(session()->get('message-danger')); ?>

                                                            </div>
                                                        <?php endif; ?>
                                                        <div class="input-effect">
                                                            <label class="txtlbl"><?php echo app('translator')->getFromJson('Payment Terms'); ?> <?php echo app('translator')->getFromJson('lang.title'); ?>
                                                                <span>*</span></label>
                                                            <input class="form-control" type="text" name="title"
                                                                autocomplete="off"
                                                                value="<?php echo e(isset($editmode) ? @$editmode->title : ''); ?>">
                                                            <input type="hidden" name="id"
                                                                value="<?php echo e(isset($editmode) ? $editmode->id : ''); ?>">

                                                            <span class="focus-border"></span>
                                                            <?php if($errors->has('title')): ?>
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong><?php echo e($errors->first('title')); ?></strong>
                                                                </span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                                    $tooltip = '';
                                                    if (in_array(105, $module_links) || Auth::user()->role_id == 1) {
                                                        $tooltip = '';
                                                    } else {
                                                        $tooltip = 'You have no permission to add';
                                                    }
                                                ?>
                                                <div class="row mt-2">
                                                    <div class="col-lg-12">
                                                        <button class="btn btn-light" type="submit" id="btnSubmit">
                                                            <i class="ico icon-outline-bookmark-opened text-success"></i>
                                                            <?php echo e(isset($editmode) ? 'Update' : 'Save'); ?>

                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php echo e(Form::close()); ?>

                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-9">
                                <div class="row">
                                    <div class="col-lg-12">

                                        <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">

                                            <thead>
                                                <?php if(session()->has('message-success-delete') != '' || session()->get('message-danger-delete') != ''): ?>
                                                    <tr>
                                                        <td colspan="2">
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
                                                    <th><?php echo app('translator')->getFromJson('Payment Terms'); ?></th>
                                                    <th><?php echo app('translator')->getFromJson('Created By'); ?></th>
                                                    <th class="text-center"><?php echo app('translator')->getFromJson('lang.action'); ?></th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php $__currentLoopData = $paymentterms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paymentterm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td><?php echo e(@$paymentterm->title); ?></td>
                                                        <td><?php echo e(@$paymentterm->createdby->full_name); ?></td>
                                                        <td>
                                                            <div class="d-flex justify-content-center">
                                                                <a class="btn btn-sm btn-light"
                                                                    href="<?php echo e(url('payment-terms', [@$paymentterm->id])); ?>"><i
                                                                        class="ico icon-outline-pen-2 text-success"
                                                                        style="font-size: 16px;"></i></a>
                                                                <a class="btn btn-sm btn-light" data-bs-toggle="modal"
                                                                    data-bs-target="#deleteDesignationModal<?php echo e(@$paymentterm->id); ?>"
                                                                    href="#"><i
                                                                        class="ico icon-outline-trash-bin-minimalistic text-danger"
                                                                        style="font-size: 16px;"></i></a>
                                                            </div>

                                                        </td>
                                                    </tr>
                                                    <div class="modal side-panel fade"
                                                        id="deleteDesignationModal<?php echo e(@$paymentterm->id); ?>"
                                                        data-bs-backdrop="false" tabindex="-1"
                                                        aria-labelledby="editModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-sm">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title">Delete Payment Terms</h4>
                                                                    <button type="button" id="ModalPaymentAdjustmentClose"
                                                                        class="btn-close" data-bs-dismiss="modal"
                                                                        aria-label="Close"></button>
                                                                </div>

                                                                <div class="modal-body text-center py-4">
                                                                    <div class="mb-3">
                                                                        <i class="ico icon-bold-trash-bin-2 text-danger"
                                                                            style="font-size: 40px;"></i>
                                                                    </div>
                                                                    <h5 class="fw-semibold mb-2"><?php echo app('translator')->getFromJson('lang.are_you_sure_to_delete'); ?></h5>
                                                                    <p class="text-muted small mb-0">This action cannot be
                                                                        undone.
                                                                    </p>
                                                                </div>
                                                                <div class="modal-footer">

                                                                    <div class="mt-2 text-center">
                                                                        <?php echo e(Form::open(['url' => 'payment-terms/' . @$paymentterm->id, 'method' => 'DELETE', 'enctype' => 'multipart/form-data'])); ?>

                                                                        <button type="submit"
                                                                            class="btn btn-light add-btn ms-2">
                                                                            <i
                                                                                class="ico icon-outline-trash-bin-minimalistic text-danger"></i>
                                                                            Delete
                                                                        </button>
                                                                        <?php echo e(Form::close()); ?>


                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
    <?php }catch (\Exception $e) { ?> <?php echo e($e); ?> <?php  } ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script>
        $(document).ready(function() {
            $("#btnSubmit").click(function() {
                setTimeout(function() {
                    disableButton();
                }, 0);
            });

            function disableButton() {
                $("#btnSubmit").prop('disabled', true);
            }
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backEnd.newmasterpage', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
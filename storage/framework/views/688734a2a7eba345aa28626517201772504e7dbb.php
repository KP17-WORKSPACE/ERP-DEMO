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
                        General Settings
                    </h4>
                    <div class="purchase-order-content-header-right">
                       
                    </div>
                </div>


                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3 col-md-6">

                                <div class="card shadow-sm border-0 rounded-3 mb-4">
                                    <div class="card-body text-center">


                                        <div class="">
                                            <div class="main-title">
                                                <h5 class="primary-color"><?php echo app('translator')->getFromJson('Change Logo'); ?>:</h5>
                                            </div>

                                            <?php if(in_array(183, @$module_links) || Auth::user()->role_id == 1): ?>
                                                <?php if(Illuminate\Support\Facades\Config::get('app.app_sync')): ?>
                                                    <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'admin-dashboard', 'method' => 'GET', 'enctype' => 'multipart/form-data'])); ?>

                                                <?php else: ?>
                                                    <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'update-school-logo', 'method' => 'POST', 'enctype' => 'multipart/form-data'])); ?>

                                                <?php endif; ?>
                                            <?php endif; ?>

                                            <div class="white-box">
                                                <input type="hidden" name="url" id="url"
                                                    value="<?php echo e(URL::to('/')); ?>">
                                                <div class="text-center">
                                                    <?php if(isset($editData->logo) && !empty(@$editData->logo)): ?>
                                                        <img class="img-fluid Img-100" src="<?php echo e(@$editData->logo); ?>"
                                                            alt="">
                                                    <?php else: ?>
                                                        <img class="img-fluid"
                                                            src="<?php echo e(asset('public/uploads/settings/logo.png')); ?>"
                                                            alt="">
                                                    <?php endif; ?>
                                                </div>

                                                <div class="mt-4">
                                                    <div class="text-center">

                                                        <input type="file" class="form-control" name="main_school_logo"
                                                            id="upload_logo">
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="d-flex justify-content-end">
                                                        <?php if(Illuminate\Support\Facades\Config::get('app.app_sync')): ?>
                                                            <span class="d-inline-block" tabindex="0"
                                                                data-toggle="tooltip" title="Disabled For Demo">
                                                                <button class="btn btn-sm btn-light mt-2 small fix-gr-bg demo_view"
                                                                    style="pointer-events: none;" type="button" disabled>
                                                                    <i class="ico icon-outline-pen-2 text-dark"
                                                                        style="font-size: 16px;"></i>
                                                                    <?php echo app('translator')->getFromJson('lang.change_logo'); ?>
                                                                </button>
                                                            </span>
                                                        <?php else: ?>
                                                            <button class="btn btn-sm btn-light mt-2">
                                                                <span class="ti-check"></span>
                                                                <i class="ico icon-outline-pen-2 text-dark"
                                                                    style="font-size: 16px;"></i>
                                                                <?php echo app('translator')->getFromJson('lang.change_logo'); ?>
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                            </div>
                                            <?php echo e(Form::close()); ?>

                                        </div>


                                    </div>
                                </div>

                                <div class="card shadow-sm border-0 rounded-3 mb-4">
                                    <div class="card-body text-center">

                                        <div class="">
                                            <div class="main-title">
                                                <h5 class="primary-color"><?php echo app('translator')->getFromJson('Change Favicon'); ?>:</h5>
                                            </div>

                                            <?php if(in_array(184, @$module_links) || Auth::user()->role_id == 1): ?>
                                                <?php if(Illuminate\Support\Facades\Config::get('app.app_sync')): ?>
                                                    <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'admin-dashboard', 'method' => 'GET', 'enctype' => 'multipart/form-data'])); ?>

                                                <?php else: ?>
                                                    <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'update-school-logo', 'method' => 'POST', 'enctype' => 'multipart/form-data'])); ?>

                                                <?php endif; ?>
                                            <?php endif; ?>

                                            <div class="white-box">
                                                <input type="hidden" name="url" id="url"
                                                    value="<?php echo e(URL::to('/')); ?>">
                                                <div class="text-center">
                                                    <?php if(isset($editData->favicon) && !empty(@$editData->favicon)): ?>
                                                        <img class="img-fluid Img-50" src="<?php echo e(@$editData->favicon); ?>"
                                                            alt="">
                                                    <?php else: ?>
                                                        <img class="img-fluid"
                                                            src="<?php echo e(asset('public/uploads/settings/favicon.png')); ?>"
                                                            alt="">
                                                    <?php endif; ?>
                                                </div>

                                                <div class="mt-4">
                                                    <div class="text-center">

                                                        <input type="file" class="form-control"
                                                            name="main_school_favicon" id="upload_favicon">
                                                    </div>
                                                </div>
                                                <div class="text-center gs_button">

                                                    <div class="d-flex justify-content-end">
 <?php if(Illuminate\Support\Facades\Config::get('app.app_sync')): ?>
                                                        <span class="d-inline-block" tabindex="0" data-toggle="tooltip"
                                                            title="Disabled For Demo "> <button
                                                                class="btn btn-sm btn-light mt-2 small fix-gr-bg  demo_view"
                                                                style="pointer-events: none;" type="button" disabled>
                                                                <i class="ico icon-outline-pen-2 text-dark"
                                                                    style="font-size: 16px;"></i> <?php echo app('translator')->getFromJson('lang.change_fav'); ?>
                                                            </button></span>
                                                    <?php else: ?>
                                                        <button class="btn btn-sm btn-light mt-2">
                                                            <span class="ti-check"></span>
                                                            <i class="ico icon-outline-pen-2 text-dark"
                                                                style="font-size: 16px;"></i> <?php echo app('translator')->getFromJson('lang.change_fav'); ?>
                                                        </button>
                                                    <?php endif; ?>
                                                    </div>




                                                   

                                                </div>
                                            </div>
                                            <?php echo e(Form::close()); ?>

                                        </div>
                                    </div>
                                </div>


                            </div>


                            <div class="col-lg-9">

                                <div class="card shadow-sm border-0 rounded-3">
                                    <div class="card-body">
                                        <h6 class="fw-semibold text-secondary mb-3">General Information</h6>
                                        <div class="table-responsive">
                                            <table class="table table-borderless align-middle">
                                                <tbody class="text-muted">
                                                    <tr>
                                                        <td class="fw-semibold text-dark"><?php echo app('translator')->getFromJson('lang.school_name'); ?></td>
                                                        <td><?php echo e(@$editData->company_name); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold text-dark"><?php echo app('translator')->getFromJson('lang.site_title'); ?></td>
                                                        <td><?php echo e(@$editData->site_title); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold text-dark"><?php echo app('translator')->getFromJson('lang.address'); ?></td>
                                                        <td><?php echo e(@$editData->address); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold text-dark"><?php echo app('translator')->getFromJson('lang.phone'); ?>
                                                            <?php echo app('translator')->getFromJson('lang.no'); ?></td>
                                                        <td><?php echo e(@$editData->phone); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold text-dark"><?php echo app('translator')->getFromJson('lang.email'); ?>
                                                            <?php echo app('translator')->getFromJson('lang.address'); ?></td>
                                                        <td><?php echo e(@$editData->email); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold text-dark"><?php echo app('translator')->getFromJson('lang.language'); ?></td>
                                                        <td><?php echo e(@$editData->languages != '' ? @$editData->languages->language_name : ''); ?>

                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold text-dark"><?php echo app('translator')->getFromJson('lang.date_format'); ?></td>
                                                        <td><?php echo e(@$editData->dateFormats != '' ? @$editData->dateFormats->normal_view : ''); ?>

                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold text-dark"><?php echo app('translator')->getFromJson('lang.time_zone'); ?></td>
                                                        <td><?php echo e(@$editData->dateFormats != '' ? @$editData->timeZone->time_zone : ''); ?>

                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold text-dark"><?php echo app('translator')->getFromJson('lang.currency'); ?></td>
                                                        <td><?php echo e(@$editData->currency); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold text-dark"><?php echo app('translator')->getFromJson('lang.currency'); ?>
                                                            <?php echo app('translator')->getFromJson('lang.symbol'); ?></td>
                                                        <td><?php echo e(@$editData->currency_symbol); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold text-dark"><?php echo app('translator')->getFromJson('lang.copyright_text'); ?></td>
                                                        <td><?php echo e(@$editData->copyright_text); ?></td>
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


            </div>
        </div>
    </div>

    <?php }catch (\Exception $e) { ?> <?php echo e($e); ?> <?php  } ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('backEnd.newmasterpage', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
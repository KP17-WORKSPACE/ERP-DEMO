<?php $__env->startSection('mainContent'); ?>
    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>



    <aside class="left-nav col-12" id="leftSidebar">


        <div class="long-list" id="filters-long">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Opening Stock
                </h4>
                <div class="search-filter-container mb-0">


                      <input type="text" id="tableSearch" class="form-control d-inline-block"
                        style="font-size:13px;width: 350px;" placeholder="Search">


                    <a href="<?php echo e(url('item-store')); ?>" class="btn btn-light text-dark add-btn"><i
                            class="ico icon-outline-add-square text-success"></i>
                        Add</a>


                    <button class="btn btn-light" onclick="toggleLongFilters()">
                        <i class="ico icon-outline-magnifer"></i>
                    </button>

                         <div class="dropdown">
                        <button class="btn btn-light text-dark dropdown-toggle syscom-dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ico icon-outline-hamburger-menu"></i>
                        </button>
                        <ul class="dropdown-menu" style="">
                            <li><a href="<?php echo e(url('brand')); ?>" class="dropdown-item">Brand</a></li>

                            <li><a href="<?php echo e(url('item-category')); ?>" class="dropdown-item">Category</a></li>

                            <li><a href="<?php echo e(url('create-sub-category')); ?>" class="dropdown-item">Sub Category</a></li>

                            <li><hr class="dropdown-divider"></li>
                            <li><a href="<?php echo e(url('item-store-import')); ?>" class="dropdown-item">Import Opening Stock</a></li>
                            <li><a href="<?php echo e(url('product-import')); ?>" class="dropdown-item">Import Products</a></li>
                            <li><a href="<?php echo e(url('brand-import')); ?>" class="dropdown-item">Import Brands</a></li>
                            <li><a href="<?php echo e(url('category-import')); ?>" class="dropdown-item">Import Categories</a></li>
                            <li><a href="<?php echo e(url('subcategory-import')); ?>" class="dropdown-item">Import Sub Categories</a></li>




                        </ul>
                    </div>

                </div>
            </div>


        </div>

        <div class="left-nav-list">


            <div class="table-responsive mb-4 mt-4">
                <table id="long-list" class="table table-hover data-table" style="table-layout: fixed;width:100%">

                    <thead>
                        <tr class="text-center">
                            <th style="width:20px"><?php echo app('translator')->getFromJson('lang.sl'); ?> </th>
                            <th style="width:60px"><?php echo app('translator')->getFromJson('Doc No'); ?></th>
                            <th style="width:60px"><?php echo app('translator')->getFromJson('Doc Date'); ?></th>
                            <th style="width:60px"><?php echo app('translator')->getFromJson('Bill Date'); ?></th>
                            <th style="width:150px" class="text-start"><?php echo app('translator')->getFromJson('Narration'); ?></th>
                            <th style="width:60px"><?php echo app('translator')->getFromJson('lang.action'); ?></th>
                        </tr>
                    </thead>


                    <tbody>

                        <?php $count =1; ?>
                        <?php $__currentLoopData = $ios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="text-center">
                                <td><?php echo e(@$count++); ?></td>
                                <td><?php echo e(@$value->doc_number); ?></td>
                                <td><?php echo e(date('d/m/Y', strtotime(@$value->doc_date))); ?></td>
                                <td><?php echo e(date('d/m/Y', strtotime(@$value->bill_date))); ?></td>
                                <td class="text-start"><?php echo e(@$value->narration); ?></td>
                                <td>

                                    <div class="d-flex justify-content-center align-items-center gap-1">
                                        <a class="btn btn-sm btn-light"
                                            href="<?php echo e(url('item-store/' . $value->id . '/edit')); ?>"><i class="ico icon-outline-pen-2" style="font-size: 16px;"></i></a>
                                        <a class="btn btn-sm btn-light"
                                            href="<?php echo e(url('item-store/' . $value->doc_number . '/delete')); ?>"
                                            onclick="return confirm('Are you sure you want to delete this item?');"><i class="ico icon-bold-trash-bin-2" style="font-size: 16px;"></i></a>
                                    </div>


                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>


                </table>
            </div>
        </div>
    </aside>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backEnd.newmasterpage', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
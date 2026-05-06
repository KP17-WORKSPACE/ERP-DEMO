<?php $__env->startSection('mainContent'); ?>

    <style>
        .report-type-dropdown .dropdown-item.active,
        .report-type-dropdown .dropdown-item.active:hover,
        .report-type-dropdown .dropdown-item.active:focus {
            color: #198754 !important;
            background-color: #eaf7ef;
            font-weight: 600;
        }
    </style>

    <script>
        function toggleLongFilters() {
            document.querySelectorAll('#filters-long .filter-field').forEach(el => {
                el.classList.toggle('d-none');
            });
        }
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof flatpickr !== 'undefined') {
                document.querySelectorAll('.date-picker').forEach(function (el) {
                    if (!el._flatpickr) {
                        flatpickr(el, {
                            dateFormat: 'd/m/Y',
                            allowInput: true,
                            defaultDate: el.value || null,
                            clickOpens: true,
                        });
                    }
                });
            }
        });
    </script>

    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>

    <?php try { ?>

    <aside class="left-nav col-12" id="leftSidebar">

       

        <div class="long-list" id="filters-long">
            <div class="d-flex align-items-center justify-content-between">
                <div class="dropdown report-type-dropdown">
                    <?php
                        $isPartWiseRoute = Request::is('inventory-brand-report') || Request::is('inventory-brand-report/*');
                        $isBrandWiseRoute = Request::is('inventory-brand-wise-report');
                        $isCategoryWiseRoute = Request::is('inventory-category-wise-report');
                        $isSubCategoryWiseRoute = Request::is('inventory-subcategory-wise-report');
                        $isCompanyWiseRoute = Request::is('inventory-company-wise-report');
                        $isCustomerWiseRoute = Request::is('inventory-customer-wise-report');
                        $isSalesPersonWiseRoute = Request::is('inventory-salesperson-wise-report');
                    ?>
                    <a class="text-dark report-type-trigger" href="javascript:void(0);" id="inventoryCustomerReportTypeMenu"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Inventory Report Type <i class="icon-outline-alt-arrow-down ms-1"></i>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="inventoryCustomerReportTypeMenu">
                        <li><a class="text-dark dropdown-item <?php echo e($isPartWiseRoute ? 'active' : ''); ?>" href="<?php echo e(url('inventory-brand-report')); ?>">Part number wise report</a></li>
                        <li><a class="text-dark dropdown-item <?php echo e($isBrandWiseRoute ? 'active' : ''); ?>" href="<?php echo e(url('inventory-brand-wise-report')); ?>">Brand wise report</a></li>
                        <li><a class="text-dark dropdown-item <?php echo e($isCategoryWiseRoute ? 'active' : ''); ?>" href="<?php echo e(url('inventory-category-wise-report')); ?>">Category wise report</a></li>
                        <li><a class="text-dark dropdown-item <?php echo e($isSubCategoryWiseRoute ? 'active' : ''); ?>" href="<?php echo e(url('inventory-subcategory-wise-report')); ?>">Sub category wise report</a></li>
                        <li><a class="text-dark dropdown-item <?php echo e($isCompanyWiseRoute ? 'active' : ''); ?>" href="<?php echo e(url('inventory-company-wise-report')); ?>">Company wise report</a></li>
                        <li><a class="text-dark dropdown-item <?php echo e($isCustomerWiseRoute ? 'active' : ''); ?>" href="<?php echo e(url('inventory-customer-wise-report')); ?>">Customer wise report</a></li>
                        <li><a class="text-dark dropdown-item <?php echo e($isSalesPersonWiseRoute ? 'active' : ''); ?>" href="<?php echo e(url('inventory-salesperson-wise-report')); ?>">Sales person wise report</a></li>
                    </ul>
                    <?php echo $__env->make('backEnd.inventory.partials.inventoryReportPageHeading', [
                        'reportBaseTitle' => '',
                        'ctrlBrand' => '',
                        'brands' => collect(),
                        'ctrlCategory' => '',
                        'categories' => collect(),
                        'ctrlSubCategory' => '',
                        'subCategories' => collect(),
                        'ctrlSupplier' => $ctrl_supplier ?? '',
                        'suppliers' => $supplier_list ?? collect(),
                        'ctrlSalesPerson' => $ctrl_sales_person ?? '',
                        'salesPersons' => $sales_person_list ?? collect(),
                        'ctrlCompany' => $ctrl_company ?? '',
                        'companies' => $company ?? collect(),
                        'ctrlPartNumber' => '',
                    ], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                </div>
                <div class="search-filter-container mb-0">
                    <input type="text" id="tableSearch" class="form-control d-inline-block"
                        style="font-size:13px;width: 350px;" placeholder="Search">
                    <button class="btn btn-light" onclick="toggleLongFilters()">
                        <i class="ico icon-outline-magnifer"></i>
                    </button>
                </div>
            </div>

            <div class="search-filter-container mt-1 mb-4 filter-field d-none border">
                <div class="card" style="width: 100%">
                    <div class="card-body">
                        <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'inventory-customer-wise-report', 'method' => 'POST', 'id' => 'inventory-customer-wise-report'])); ?>

                        <div class="row">
                            <div class="col-2 mb-20">
                                <div class="input-effect">
                                    <label><?php echo app('translator')->getFromJson('From Date'); ?></label>
                                    <input class="form-control date-picker" type="text" name="from_date" value="<?php echo e(@App\SysHelper::normalizeToDmy($from_date)); ?>" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-2 mb-20">
                                <div class="input-effect">
                                    <label><?php echo app('translator')->getFromJson('To Date'); ?></label>
                                    <input class="form-control date-picker" type="text" name="to_date" value="<?php echo e(@App\SysHelper::normalizeToDmy($to_date)); ?>" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-3 mb-20">
                                <div class="input-effect">
                                    <label><?php echo app('translator')->getFromJson('Account Name'); ?></label>
                                    <select class="form-control js-example-basic-single" name="supplier">
                                        <option value=""></option>
                                        <?php $__currentLoopData = $supplier_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e(@$value->id); ?>" <?php if(@$ctrl_supplier == $value->id): ?> selected <?php endif; ?>><?php echo e(@$value->account_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-3 mb-20">
                                <div class="input-effect">
                                    <label><?php echo app('translator')->getFromJson('Sales Person'); ?></label>
                                    <select class="form-control js-example-basic-single" name="sales_person">
                                        <option value=""></option>
                                        <?php $__currentLoopData = $sales_person_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e(@$value->user_id); ?>" <?php if(@$ctrl_sales_person == $value->user_id): ?> selected <?php endif; ?>><?php echo e(@$value->full_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-2 mb-20" <?php if(session('logged_session_data.company_id')!=1): ?> style="display:none;" <?php endif; ?>>
                                <div class="input-effect">
                                    <label><?php echo app('translator')->getFromJson('Company'); ?></label>
                                    <select class="form-control js-example-basic-single" name="company">
                                        <option value="">-Select-</option>
                                        <?php $__currentLoopData = $company; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e(@$value->id); ?>" <?php if($ctrl_company == $value->id): ?> selected <?php endif; ?>><?php echo e(@$value->company_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-1 mb-2">
                                <button type="submit" class="btn btn-light mt-4">
                                    <i class="ico icon-outline-magnifer text-success"></i> Filter
                                </button>
                            </div>
                        </div>
                        <?php echo e(Form::close()); ?>

                    </div>
                </div>
            </div>
        </div>

        <div class="left-nav-list">
            <div class="table-responsive mb-4 mt-4">
                <table id="long-list" class="table table-hover data-table" style="table-layout: fixed;width:100%">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:50px">No.</th>
                            <th class="text-start">Customer</th>
                            <th class="text-center" style="width:90px">Total Qty</th>
                            <th class="text-end" style="width:110px">Avg Rate</th>
                            <th class="text-end" style="width:110px">Value</th>
                            <th class="text-end" style="width:110px">Discount</th>
                            <th class="text-end" style="width:130px">Taxable Amt</th>
                            <th class="text-end" style="width:110px">Vat Amt</th>
                            <th class="text-end" style="width:110px" title="Net: sales invoices minus sales returns">Total Amt (net)</th>
                            <th class="text-center" style="width:100px" title="Distinct sales invoices / sales returns">SI / SR</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $part_wise_base_query = [
                                'from_date' => $from_date ?? '',
                                'to_date' => $to_date ?? '',
                                'sales_person' => $ctrl_sales_person ?? '',
                                'company' => $ctrl_company ?? '',
                            ];
                        ?>
                        <?php $__empty_1 = true; $__currentLoopData = $customer_report_rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $q = $part_wise_base_query;
                                $q['supplier'] = $row->customer_id;
                                $partWiseUrl = url('inventory-brand-report') . '?' . http_build_query(array_filter($q, function ($v) {
                                    return $v !== '' && $v !== null;
                                }));
                            ?>
                            <tr>
                                <td class="text-center"><?php echo e($loop->iteration); ?></td>
                                <td><a href="<?php echo e($partWiseUrl); ?>"><?php echo e($row->customer_name); ?></a></td>
                                <td class="text-center"><?php echo e($row->qty); ?></td>
                                <td class="text-end"><?php echo e(@App\SysHelper::com_curr_format($row->avg_rate,2,'.',',')); ?></td>
                                <td class="text-end"><?php echo e(@App\SysHelper::com_curr_format($row->value,2,'.',',')); ?></td>
                                <td class="text-end"><?php echo e(@App\SysHelper::com_curr_format($row->discount,2,'.',',')); ?></td>
                                <td class="text-end"><?php echo e(@App\SysHelper::com_curr_format($row->taxableamount,2,'.',',')); ?></td>
                                <td class="text-end"><?php echo e(@App\SysHelper::com_curr_format($row->vatamount,2,'.',',')); ?></td>
                                <td class="text-end"><?php echo e(@App\SysHelper::com_curr_format($row->total_amount,2,'.',',')); ?></td>
                                <td class="text-center"><?php echo e($row->si_doc_count); ?> / <?php echo e($row->sr_doc_count); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="10" class="text-center">No records found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <?php echo $__env->make('backEnd.inventory.partials.inventoryWiseReportTableFooter', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                </table>
            </div>
        </div>
    </aside>

    <?php } catch (\Exception $e) { ?> <?php echo e($e); ?> <?php } ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backEnd.newmasterpage', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
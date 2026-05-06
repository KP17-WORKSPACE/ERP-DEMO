<?php $__env->startPush('scripts'); ?>
    <script src="https://cdn.jsdelivr.net/npm/exceljs@4.3.0/dist/exceljs.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
<?php $__env->stopPush(); ?>
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
 


        //added ny kp
        function toggleLongFilters() {
            console.log("clicked");
            document.querySelectorAll('#filters-long .filter-field').forEach(el => {
                el.classList.toggle('d-none');
            });
        }
    </script>


    <style>

    </style>


    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>

    <?php try { ?>

    <style>
        #part_number_list1 ul {
            width: 380px
        }
    </style>

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
                    <a class="text-dark report-type-trigger" href="javascript:void(0);" id="inventoryReportTypeMenu"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Inventory Report Type <i class="icon-outline-alt-arrow-down ms-1"></i>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="inventoryReportTypeMenu">
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
                        'ctrlBrand' => $ctrl_brand ?? '',
                        'brands' => $brand ?? collect(),
                        'ctrlCategory' => $ctrl_category ?? '',
                        'categories' => $category ?? collect(),
                        'ctrlSubCategory' => $ctrl_sub_category ?? '',
                        'subCategories' => $sub_category ?? collect(),
                        'ctrlSupplier' => $ctrl_supplier ?? '',
                        'suppliers' => $supplier_list ?? collect(),
                        'ctrlSalesPerson' => $ctrl_sales_person ?? '',
                        'salesPersons' => $sales_person_list ?? collect(),
                        'ctrlCompany' => $ctrl_company ?? '',
                        'companies' => $company ?? collect(),
                        'ctrlPartNumber' => $ctrl_part_number ?? '',
                    ], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                </div>
                <div class="search-filter-container mb-0">

          


                    <input type="text" id="tableSearch" class="form-control d-inline-block"
                        style="font-size:13px;width: 350px;" placeholder="Search">

                  

                    <button type="button" class="btn btn-light list_style_search_bt mt-1n" id="exportExcelItems">
                        <i class="ico icon-outline-export text-success"></i> Export
                    </button>

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
            <style>
                #part_number_list ul {
                  width: 347px;
                }
            </style>

            <div class="search-filter-container mt-1 mb-4 filter-field d-none border">

                <div class="card" style="width: 100%">
                    <div class="card-body">
        <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'inventory-brand-report', 'method' => 'POST', 'id' => 'inventory-report'])); ?>


                             <div class="row">
                <div class="col-1 mb-20">
                    <div class="no-gutters input-right-icon">
                        <div class="col">
                            <div class="input-effect">
                                <label><?php echo app('translator')->getFromJson('From Date'); ?></label>
                                <input class="form-control date-picker" id="from_date" type="text" name="from_date" value="<?php echo e(@App\SysHelper::normalizeToDmy($from_date)); ?>" autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-1 mb-20">
                    <div class="no-gutters input-right-icon">
                        <div class="col">
                            <div class="input-effect">
                                <label><?php echo app('translator')->getFromJson('To Date'); ?></label>
                                <input class="form-control date-picker" id="to_date" type="text" name="to_date" value="<?php echo e(@App\SysHelper::normalizeToDmy($to_date)); ?>" autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>
                <script>
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
              
                <div class="col-3 mb-2">
                    <label for="" class="form-check-label">Part Number</label>
                    <input class="form-control" type="text" autocomplete="off" name="part_number" id="part_number" value="<?php echo e($ctrl_part_number); ?>">

                    <input class="form-control" type="hidden" id="part_number_array">

                                <div id="part_number_list">
                                </div>

                </div>

                  <script>
                                $(document).ready(function() {

                                    // When typing in input
                                    $('#part_number').keyup(function() {
                                        var query = $(this).val().split(',').pop().trim(); // get last part

                                        if (query != '') {
                                            var _token = $('input[name="_token"]').val();
                                            $.ajax({
                                                url: "<?php echo e(route('autocomplete.fetch_product_partnumber_withcoma')); ?>",
                                                method: "POST",
                                                data: {
                                                    query: query,
                                                    _token: _token
                                                },
                                                success: function(data) {
                                                    $('#part_number_list').fadeIn();
                                                    $('#part_number_list').html(data);
                                                }
                                            });
                                        } else {
                                            $('#part_number_list').fadeOut();
                                        }
                                    });

                                    // When clicking a suggestion
                                    $(document).on('click', 'li', function() {
                                        var current = $('#part_number').val(); // existing input value
                                        var parts = current.split(','); // split into array
                                        parts[parts.length - 1] = $(this).text().trim(); // replace last typed part
                                        var finalVal = parts.join(',').replace(/^,|,$/g, ''); // clean commas

                                        $('#part_number').val(finalVal); // update visible input
                                        $('#part_number_array').val(finalVal); // update hidden field

                                        $('#part_number_list').fadeOut();
                                    });

                                    // Hide suggestion box on outside click
                                    $(document).click(function(e) {
                                        if (!$(e.target).closest('#part_number, #part_number_list').length) {
                                            $('#part_number_list').fadeOut();
                                        }
                                    });

                                });
                            </script>

                  <div class="col-1-5 mb-2">
                    <label for="" class="form-check-label">Brand</label>
                    <select class="form-control js-example-basic-single" name="brand" id="brand">
                        <option value="">-Select-</option>
                        <option value="all" <?php if($ctrl_brand == "all"): ?> selected <?php endif; ?>>All</option>
                        <?php $__currentLoopData = $brand; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e(@$value->id); ?>" <?php if($ctrl_brand == $value->id): ?> selected <?php endif; ?>><?php echo e(@$value->title); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="col-1-5 mb-2">
                    <label for="" class="form-check-label">Category</label>
                    <select class="form-control js-example-basic-single" name="category">
                        <option value="">-Select-</option>
                        <?php $__currentLoopData = $category; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e(@$value->id); ?>" <?php if($ctrl_category == $value->id): ?> selected <?php endif; ?>><?php echo e(@$value->category_name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-1-5 mb-2">
                    <label for="" class="form-check-label">Sub Category</label>
                    <select class="form-control js-example-basic-single" name="sub_category">
                        <option value="">-Select-</option>
                        <?php $__currentLoopData = $sub_category; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e(@$value->id); ?>" <?php if($ctrl_sub_category == $value->id): ?> selected <?php endif; ?>><?php echo e(@$value->sub_category_name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-2-5 mb-20">
                    <div class="no-gutters input-right-icon">
                        <div class="col">
                            <div class="input-effect">
                                <label><?php echo app('translator')->getFromJson('Account Name'); ?></label>
                                <select class="form-control js-example-basic-single" name="supplier" id="supplier">
                                    <option value=""></option>
                                    <?php $__currentLoopData = $supplier_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e(@$value->id); ?>" <?php if(@$ctrl_supplier == $value->id): ?> selected <?php endif; ?>><?php echo e(@$value->account_name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-2 mb-20">
                    <div class="no-gutters input-right-icon">
                        <div class="col">
                            <div class="input-effect">
                                <label><?php echo app('translator')->getFromJson('Sales Person'); ?></label>
                                <select class="form-control js-example-basic-single" name="sales_person" id="sales_person">
                                    <option value=""></option>
                                    <?php $__currentLoopData = $sales_person_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e(@$value->user_id); ?>" <?php if(@$ctrl_sales_person == $value->user_id): ?> selected <?php endif; ?>><?php echo e(@$value->full_name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-3 mb-2" <?php if(session('logged_session_data.company_id')!=1): ?> style="display: none;" <?php endif; ?>>
                    <label for="" class="form-check-label">Company</label>
                    <select class="form-control js-example-basic-single" name="company" id="company">
                        <option value="">-Select-</option>
                        <?php $__currentLoopData = $company; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e(@$value->id); ?>" <?php if($ctrl_company == $value->id): ?> selected <?php endif; ?>><?php echo e(@$value->company_name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
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
                            <th class="text-start" style="width:100px">Part Number</th>
                            <th class="text-start" style="width: 270px;">Description</th>
                            <th class="text-start" style="width:100px">Brand</th>
                            <th class="text-start" style="width:100px">Category</th>
                            <th class="text-start" style="width:100px">Sub Category</th>
                            <th class="text-center" style="width:30px">Qty</th>
                            <th class="text-end" style="width:100px">Avg Rate</th>
                            <th class="text-end" style="width:100px">Value</th>
                            <th class="text-end" style="width:100px">Discount</th>
                            <th class="text-end" style="width:120px">Taxable Amount</th>
                            <th class="text-end" style="width:100px">Val Amount</th>
                            <th class="text-end" style="width:100px">Total Amt</th>
                            <th class="text-center" style="width:100px">Action</th>
                        </tr>
                    </thead>


                         <tbody>
                        <?php
                            $ibr_detail_query = http_build_query(array_filter([
                                'from_date' => $from_date ?? '',
                                'to_date' => $to_date ?? '',
                                'brand' => $ctrl_brand ?? '',
                                'sales_person' => $ctrl_sales_person ?? '',
                                'supplier' => $ctrl_supplier ?? '',
                                'company' => $ctrl_company ?? '',
                            ], function ($v) {
                                return $v !== '' && $v !== null;
                            }));
                            $stocklist2 = $stocklist;
                        ?>
                            <?php $__currentLoopData = $stocklist2; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ind => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $t = $brand_report_totals[$value->partno] ?? [
                                        'qty' => 0,
                                        'avg_rate' => 0,
                                        'value' => 0,
                                        'discount' => 0,
                                        'taxableamount' => 0,
                                        'vatamount' => 0,
                                        'total_amount' => 0,
                                    ];
                                    $detailHref = route('inventory-brand-report-detail', ['partno' => $value->partno]);
                                    if ($ibr_detail_query !== '') {
                                        $detailHref .= '?' . $ibr_detail_query;
                                    }
                                ?>
                                <tr class="brand-report-summary">
                                    <td class="text-center">&nbsp; <?php echo e($ind+1); ?>.</td>
                                    <td><a href="<?php echo e($detailHref); ?>" target="_blank" rel="noopener noreferrer"><?php echo e(@$value->part_number); ?></a></td>
                                    <td class="text-start">
                                        <div style="width:400px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">
                                            <?php echo e($value->description); ?>

                                        </div>
                                    </td>
                                    <td><?php echo e($value->brand); ?></td>
                                    <td><?php echo e($value->categoryname); ?></td>
                                    <td><?php echo e($value->subcategoryname); ?></td>
                                    <td class="text-center"><?php echo e($t['qty']); ?></td>
                                    <td class="text-end"><?php echo e(@App\SysHelper::com_curr_format($t['avg_rate'], 2, '.', ',')); ?></td>
                                    <td class="text-end"><?php echo e(@App\SysHelper::com_curr_format($t['value'], 2, '.', ',')); ?></td>
                                    <td class="text-end"><?php echo e(@App\SysHelper::com_curr_format($t['discount'], 2, '.', ',')); ?></td>
                                    <td class="text-end"><?php echo e(@App\SysHelper::com_curr_format($t['taxableamount'], 2, '.', ',')); ?></td>
                                    <td class="text-end"><?php echo e(@App\SysHelper::com_curr_format($t['vatamount'], 2, '.', ',')); ?></td>
                                    <td class="text-end"><?php echo e(@App\SysHelper::com_curr_format($t['total_amount'], 2, '.', ',')); ?></td>

                                    <td class="text-center">
                                        <div class="d-flex justify-content-center">   
                                            <a href="<?php echo e($detailHref); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-light" data-bs-popover="popover" data-bs-trigger="hover" data-bs-delay="500" data-bs-content="View Sub Report" data-bs-placement="bottom" data-bs-original-title="" title="">
                                                <i class="ico icon-outline-eye text-success" style="font-size:16px" ></i>
                                            </a>
                                        
                                            <a href="<?php echo e(url('stock-ledger/'.$value->part_number)); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-light" data-bs-popover="popover" data-bs-trigger="hover" data-bs-delay="500" data-bs-content="View Stock Ledger" data-bs-placement="bottom" data-bs-original-title="" title="">
                                                <i class="ico icon-outline-notebook text-success" style="font-size:16px"></i>
                                            </a>
                                        </div>
                                    </td>

                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            <?php if(!isset($stocklist2) || count($stocklist2) === 0): ?>
                                <tr>
                                    <td colspan="14" class="text-center">No records found</td>
                                </tr>
                            <?php endif; ?>
                    </tbody>

                    <?php if(isset($stocklist2) && count($stocklist2) > 0): ?>
                         <tfoot>
                        <tr>
                            <th colspan="6" class="text-end">Sales</th>
                            <th class="text-center"><?php echo e($grand_qty_si); ?></th>
                            <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($grand_avg_rate_si,2,'.',',')); ?></th>
                            <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($grand_value_si,2,'.',',')); ?></th>
                            <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($grand_discount_si,2,'.',',')); ?></th>
                            <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($grand_taxableamount_si,2,'.',',')); ?></th>
                            <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($grand_vatamount_si,2,'.',',')); ?></th>
                            <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($grand_total_amount_si,2,'.',',')); ?></th>
                            <th class="text-center"></th>
                        </tr>
                        <tr>
                            <th colspan="6" class="text-end">Sales Return</th>
                            <th class="text-center"><?php echo e($grand_qty_sr); ?></th>
                            <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($grand_avg_rate_sr,2,'.',',')); ?></th>
                            <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($grand_value_sr,2,'.',',')); ?></th>
                            <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($grand_discount_sr,2,'.',',')); ?></th>
                            <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($grand_taxableamount_sr,2,'.',',')); ?></th>
                            <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($grand_vatamount_sr,2,'.',',')); ?></th>
                            <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($grand_total_amount_sr,2,'.',',')); ?></th>
                            <th class="text-center"></th>
                        </tr>
                        <tr id="ibrFooterNet">
                            <th colspan="6" class="text-end">Net Sales</th>
                            <th class="text-center"><?php echo e($grand_qty); ?></th>
                            <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($grand_avg_rate,2,'.',',')); ?></th>
                            <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($grand_value,2,'.',',')); ?></th>
                            <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($grand_discount,2,'.',',')); ?></th>
                            <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($grand_taxableamount,2,'.',',')); ?></th>
                            <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($grand_vatamount,2,'.',',')); ?></th>
                            <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($grand_total_amount,2,'.',',')); ?></th>
                            <th class="text-center"></th>
                        </tr>
                    </tfoot>
                    <?php endif; ?>
                 

                </table>
            </div>
        </div>
    </aside>











    <script>
    $(document).ready(function () {
        $('#exportExcelItems').on('click', function (e) {
            e.preventDefault();

            var companyName = <?php echo json_encode(\App\SysCompany::find(session('logged_session_data.company_id') ?? 0)->trade_name ?? '', 15, 512) ?>;
            var dateFrom    = <?php echo json_encode($from_date ?? '', 15, 512) ?>;
            var dateTo      = <?php echo json_encode($to_date ?? '', 15, 512) ?>;

            function formatDMY(v) {
                if (!v) return '';
                v = String(v).trim();
                var p = v.split(/[\/\-\.]/);
                if (p.length === 3 && p[0].length === 4) return p[2] + '/' + p[1] + '/' + p[0];
                return v;
            }

            // 14 columns: outer table has 13 (No.–Total Amt) + 1 empty to match inner 14
            var N = 14;
            var outerHeaders = [
                'No.', 'Part Number', 'Description', 'Brand', 'Category', 'Sub Category',
                'Qty', 'Avg Rate', 'Value', 'Discount', 'Taxable Amount', 'Vat Amount', 'Total Amt', 'Sales Person'
            ];
            var innerHeaders = [
                '#', 'Part Number', 'Deal Id', 'Doc No', 'Doc Date', 'Account Name',
                'Qty', 'Avg Rate', 'Value', 'Discount', 'Taxable Amt', 'Vat Amt', 'Total Amt', 'Sales Person'
            ];

            // ── Collect summary rows (line detail is on a separate page) ─
            var groups = [];

            $('#long-list tbody tr.brand-report-summary').each(function () {
                var $row = $(this);
                var productRow = [];
                $row.children('td').each(function () {
                    productRow.push($(this).text().trim().replace(/\s+/g, ' '));
                });
                while (productRow.length < N) productRow.push('');
                groups.push({ productRow: productRow, details: [], subTotalRow: null });
            });

            if (groups.length === 0) {
                alert('No data to export.');
                return;
            }

            // ── Grand total from <tfoot> ───────────────────────────────
            // tfoot structure: <th colspan="6">Total</th> <th>qty</th> <th></th>
            //   <th>value</th> <th>discount</th> <th>taxable</th> <th>vat</th> <th>total</th>
            // → 8 th elements; colspan-6 first th covers outer cols 0-5
            // Correct col mapping: 0, [1-5 empty], 6, 7, 8, 9, 10, 11, 12
            var $grandThs = $('#long-list tfoot tr#ibrFooterNet').children('th');
            var grandRow  = new Array(N).fill('');
            grandRow[0]  = $grandThs.eq(0).text().trim(); // "Total" label
            grandRow[6]  = $grandThs.eq(1).text().trim(); // grand Qty
            grandRow[7]  = $grandThs.eq(2).text().trim(); // grand Avg Rate
            grandRow[8]  = $grandThs.eq(3).text().trim(); // grand Value
            grandRow[9]  = $grandThs.eq(4).text().trim(); // grand Discount
            grandRow[10] = $grandThs.eq(5).text().trim(); // grand Taxable Amt
            grandRow[11] = $grandThs.eq(6).text().trim(); // grand Vat Amt
            grandRow[12] = $grandThs.eq(7).text().trim(); // grand Total Amt

            // ── Build workbook ────────────────────────────────────────
            var workbook  = new ExcelJS.Workbook();
            var worksheet = workbook.addWorksheet('Inventory Brand Report');

            worksheet.columns = [
                { width: 7  }, { width: 18 }, { width: 38 }, { width: 22 },
                { width: 16 }, { width: 16 }, { width: 9  }, { width: 13 },
                { width: 14 }, { width: 13 }, { width: 16 }, { width: 13 },
                { width: 13 }, { width: 18 }
            ];

            var wsRowNum = 0;
            function addRow(values, height) {
                wsRowNum++;
                var r = worksheet.addRow(values || []);
                if (height) r.height = height;
                return r;
            }

            // Company name
            addRow([], 28);
            worksheet.mergeCells(wsRowNum, 1, wsRowNum, N);
            worksheet.lastRow.getCell(1).value     = companyName;
            worksheet.lastRow.getCell(1).font      = { bold: true, size: 14 };
            worksheet.lastRow.getCell(1).alignment = { horizontal: 'center', vertical: 'middle' };

            // Report title
            addRow([], 20);
            worksheet.mergeCells(wsRowNum, 1, wsRowNum, N);
            worksheet.lastRow.getCell(1).value     = 'Inventory Brand Report';
            worksheet.lastRow.getCell(1).font      = { bold: true, size: 12 };
            worksheet.lastRow.getCell(1).alignment = { horizontal: 'center', vertical: 'middle' };

            // Date range
            var dp = [];
            if (dateFrom) dp.push('From: ' + formatDMY(dateFrom));
            if (dateTo)   dp.push('To: '   + formatDMY(dateTo));
            if (dp.length) {
                addRow([], 16);
                worksheet.mergeCells(wsRowNum, 1, wsRowNum, N);
                worksheet.lastRow.getCell(1).value     = dp.join('   ');
                worksheet.lastRow.getCell(1).font      = { size: 10 };
                worksheet.lastRow.getCell(1).alignment = { horizontal: 'center', vertical: 'middle' };
            }

            addRow([]); // blank separator

            // Outer column headers
            addRow(outerHeaders, 20);
            worksheet.lastRow.eachCell({ includeEmpty: true }, function (cell) {
                cell.font      = { bold: true, color: { argb: 'FFFFFFFF' }, size: 11 };
                cell.fill      = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF2D5496' } };
                cell.alignment = { horizontal: 'center', vertical: 'middle', wrapText: true };
                cell.border    = { top:{style:'thin'}, left:{style:'thin'}, bottom:{style:'thin'}, right:{style:'thin'} };
            });

            // Data groups
            groups.forEach(function (grp) {

                // Product summary row (bold, light blue)
                addRow(grp.productRow, 18);
                worksheet.lastRow.eachCell({ includeEmpty: true }, function (cell) {
                    cell.font   = { bold: true, size: 10, color: { argb: 'FF1A237E' } };
                    cell.fill   = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFE3EAF5' } };
                    cell.border = { top:{style:'thin',color:{argb:'FFB0BEC5'}}, left:{style:'thin',color:{argb:'FFB0BEC5'}}, bottom:{style:'thin',color:{argb:'FFB0BEC5'}}, right:{style:'thin',color:{argb:'FFB0BEC5'}} };
                });

                if (grp.details.length > 0) {

                    // Inner detail column headers
                    addRow(innerHeaders, 16);
                    worksheet.lastRow.eachCell({ includeEmpty: true }, function (cell) {
                        cell.font      = { bold: true, color: { argb: 'FFFFFFFF' }, size: 10 };
                        cell.fill      = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF455A64' } };
                        cell.alignment = { horizontal: 'center', vertical: 'middle', wrapText: true };
                        cell.border    = { top:{style:'thin'}, left:{style:'thin'}, bottom:{style:'thin'}, right:{style:'thin'} };
                    });

                    // Invoice detail rows
                    var altDetail = false;
                    grp.details.forEach(function (dr) {
                        addRow(dr, 14);
                        worksheet.lastRow.eachCell({ includeEmpty: true }, function (cell) {
                            cell.font   = { size: 10 };
                            cell.fill   = { type: 'pattern', pattern: 'solid', fgColor: { argb: altDetail ? 'FFEFF5FF' : 'FFFFFFFF' } };
                            cell.alignment = { vertical: 'middle' };
                            cell.border = { top:{style:'thin',color:{argb:'FFE0E0E0'}}, left:{style:'thin',color:{argb:'FFE0E0E0'}}, bottom:{style:'thin',color:{argb:'FFE0E0E0'}}, right:{style:'thin',color:{argb:'FFE0E0E0'}} };
                        });
                        altDetail = !altDetail;
                    });

                    // Sub-total row
                    if (grp.subTotalRow) {
                        addRow(grp.subTotalRow, 16);
                        worksheet.lastRow.eachCell({ includeEmpty: true }, function (cell) {
                            cell.font   = { bold: true, size: 10 };
                            cell.fill   = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFF0F4FA' } };
                            cell.border = { top:{style:'thin',color:{argb:'FFBDBDBD'}}, left:{style:'thin',color:{argb:'FFBDBDBD'}}, bottom:{style:'thin',color:{argb:'FFBDBDBD'}}, right:{style:'thin',color:{argb:'FFBDBDBD'}} };
                        });
                    }
                }
            });

            // Grand total row
            addRow([]);
            addRow(grandRow, 20);
            worksheet.lastRow.eachCell({ includeEmpty: true }, function (cell) {
                cell.font   = { bold: true, size: 11, color: { argb: 'FFFFFFFF' } };
                cell.fill   = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF2D5496' } };
                cell.border = { top:{style:'thin'}, left:{style:'thin'}, bottom:{style:'thin'}, right:{style:'thin'} };
            });

            // Download
            workbook.xlsx.writeBuffer().then(function (buffer) {
                var blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                function pad(n) { return n < 10 ? '0' + n : '' + n; }
                var d = new Date();
                saveAs(blob, 'inventory_brand_report_' + pad(d.getDate()) + '-' + pad(d.getMonth() + 1) + '-' + d.getFullYear() + '.xlsx');
            });
        });
    });
    </script>

    <?php } catch (\Exception $e) { ?> <?php echo e($e); ?> <?php  } ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backEnd.newmasterpage', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
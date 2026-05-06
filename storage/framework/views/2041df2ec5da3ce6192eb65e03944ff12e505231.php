<?php $__env->startSection('mainContent'); ?>


    <script>
        //added ny kp
        function toggleLongFilters() {

            document.querySelectorAll('#filters-long .filter-field').forEach(el => {
                el.classList.toggle('d-none');
            });
        }
    </script>

    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>

    <?php try { ?>

    <script src="https://cdn.jsdelivr.net/npm/exceljs@4.3.0/dist/exceljs.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#filters-long .filter-field').removeClass('d-none');
            $('#exportExcelStockRegister').on('click', function () {
                var companyName = <?php echo json_encode(@App\SysCompany::find(session('logged_session_data.company_id') ?? '')->trade_name ?? '', 15, 512) ?>;
                var dateFrom = $('#from_date').length ? $('#from_date').val().trim() : '';
                var dateTo = $('#to_date').length ? $('#to_date').val().trim() : '';
                var tillDate = $('#till_date').length ? $('#till_date').val().trim() : '';
                var $table = $('.stockRegisterTable');
                var visibleColIndexes = [];
                var headerLabels = [];

                $table.find('thead tr th').each(function (i) {
                    if ($(this).css('display') !== 'none') {
                        var label = $(this).text().trim();
                        if (['actions', 'action', 'actions '].includes(label.toLowerCase().trim())) {
                            return;
                        }
                        visibleColIndexes.push(i);
                        headerLabels.push(label);
                    }
                });

                function formatDMY(value) {
                    if (!value) return value;
                    var normalized = value.trim().replace(/\s+/g, '');
                    var parts = normalized.split(/[\/\-\.]/);
                    if (parts.length === 3) {
                        if (parts[0].length === 4) {
                            return parts[2] + '/' + parts[1] + '/' + parts[0];
                        }
                        return parts[0] + '/' + parts[1] + '/' + parts[2];
                    }
                    return value;
                }

                var rows = [];
                rows.push([companyName]);
                rows.push(['Stock Register (' + $table.find('tbody tr').length + ' Items)']);

                if (dateFrom || dateTo) {
                    var parts = [];
                    if (dateFrom) { parts.push('From: ' + formatDMY(dateFrom)); }
                    if (dateTo) { parts.push('To: ' + formatDMY(dateTo)); }
                    rows.push([parts.join('  ')]);
                } else if (tillDate) {
                    rows.push(['As of: ' + formatDMY(tillDate)]);
                }

                rows.push([]);
                rows.push(headerLabels);

                $table.find('tbody tr').each(function () {
                    var $cells = $(this).find('td');
                    var rowData = [];
                    visibleColIndexes.forEach(function (i) {
                        rowData.push($cells.eq(i).text().trim().replace(/\s+/g, ' '));
                    });
                    if (rowData.length) {
                        rows.push(rowData);
                    }
                });

                if (rows.length <= 5) {
                    alert('No data available for export');
                    return;
                }

                var workbook = new ExcelJS.Workbook();
                var worksheet = workbook.addWorksheet('Stock Register');
                worksheet.columns = headerLabels.map(function () { return { width: 22 }; });

                var hdrIdx = rows.indexOf(headerLabels);
                if (hdrIdx < 0) hdrIdx = rows.length - 1;

                var wsRowNum = 0;
                for (var ri = 0; ri < hdrIdx; ri++) {
                    if (!(rows[ri] && rows[ri][0])) continue;
                    wsRowNum++;
                    var wsRow = worksheet.addRow([]);
                    wsRow.height = ri === 0 ? 26 : ri === 1 ? 20 : 16;
                    if (headerLabels.length > 1) worksheet.mergeCells(wsRowNum, 1, wsRowNum, headerLabels.length);
                    wsRow.getCell(1).value = rows[ri][0] || '';
                    if (ri === 0) wsRow.getCell(1).font = { bold: true, size: 14 };
                    else if (ri === 1) wsRow.getCell(1).font = { bold: true, size: 12 };
                    wsRow.getCell(1).alignment = { horizontal: 'center', vertical: 'middle' };
                }

                wsRowNum++;
                worksheet.addRow([]);

                wsRowNum++;
                var wsHdrRow = worksheet.addRow(headerLabels);
                wsHdrRow.height = 20;
                wsHdrRow.eachCell({ includeEmpty: true }, function (cell) {
                    cell.font = { bold: true, color: { argb: 'FFFFFFFF' }, size: 11 };
                    cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF2D5496' } };
                    cell.alignment = { horizontal: 'center', vertical: 'middle' };
                    cell.border = {
                        top: { style: 'thin', color: { argb: 'FFB8C4D8' } },
                        left: { style: 'thin', color: { argb: 'FFB8C4D8' } },
                        bottom: { style: 'thin', color: { argb: 'FFB8C4D8' } },
                        right: { style: 'thin', color: { argb: 'FFB8C4D8' } }
                    };
                });

                for (var di = hdrIdx + 1; di < rows.length; di++) {
                    var wsDataRow = worksheet.addRow(rows[di]);
                    wsDataRow.eachCell({ includeEmpty: true }, function (cell) {
                        cell.border = {
                            top: { style: 'thin', color: { argb: 'FFCCCCCC' } },
                            left: { style: 'thin', color: { argb: 'FFCCCCCC' } },
                            bottom: { style: 'thin', color: { argb: 'FFCCCCCC' } },
                            right: { style: 'thin', color: { argb: 'FFCCCCCC' } }
                        };
                    });
                }

                workbook.xlsx.writeBuffer().then(function (buffer) {
                    var blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                    function pad(n) { return n < 10 ? '0' + n : n; }
                    var d = new Date();
                    var filename = 'stock_register_' + pad(d.getDate()) + '-' + pad(d.getMonth() + 1) + '-' + d.getFullYear() + '.xlsx';
                    saveAs(blob, filename);
                });
            });
        });
    </script>

    <aside class="left-nav col-12" id="leftSidebar">


        <div class="long-list" id="filters-long">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Stock Register
                </h4>
                <div class="search-filter-container mb-0">

                    
                    

                    <input type="text" id="tableSearch" class="form-control d-inline-block"
                        style="font-size:13px;width: 350px;" placeholder="Search">


                    <button class="btn btn-light" onclick="toggleLongFilters()">
                        <i class="ico icon-outline-magnifer"></i>
                    </button>

                    <button type="button" id="exportExcelStockRegister" class="btn btn-light ms-2">
                        <i class="ico icon-outline-export text-success"></i> Export
                    </button>

                    <div class="dropdown">
                        <button class="btn btn-light text-dark dropdown-toggle syscom-dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ico icon-outline-hamburger-menu"></i>
                        </button>
                        <ul class="dropdown-menu" style="">
                            <li><a href="<?php echo e(url('list-price')); ?>" class="dropdown-item">
                                    List Price</a></li>
                            <li><a href="<?php echo e(url('license-key-report')); ?>" class="dropdown-item">
                                    License Key Report</a></li>
                        </ul>
                    </div>


                </div>
            </div>

            <div class="search-filter-container mt-1 mb-4 filter-field d-none border">

                <div class="card" style="width: 100%">
                    <div class="card-body">

                        <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'stock-register', 'method' => 'POST', 'enctype' => 'multipart/form-data'])); ?>


                        <div class="row">

                            <div class="col-1 mb-2 ">
                                <label for="" class="form-label">To Date</label>
                                <?php
                                    $formattedToDate = @$to_date
                                        ? \Carbon\Carbon::parse($to_date)->format('d/m/Y')
                                        : \Carbon\Carbon::now()->format('d/m/Y');
                                ?>
                                <input class="form-control date-picker" id="to_date" type="text" name="to_date"
                                    value="<?php echo e(@$formattedToDate); ?>" autocomplete="off" required>
                            </div>

                            <div class="col-2-5 mb-2 ">
                                <label class="form-label">Find Part Number / Product Name / Description</label>

                                <input class="form-control" name="part_number" autocomplete="off" id="part_number"
                                    value="<?php echo e($r_part_number); ?>" />

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

                            

                            <div class="col-1-5 mb-2 ">
                                <label for="" class="form-label">Brand</label>
                                <select class="form-control js-example-basic-single" name="brand">
                                    <option value="">-Select-</option>
                                    <?php $__currentLoopData = $brand; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e(@$value->id); ?>"
                                            <?php if($r_brand == $value->id): ?> selected <?php endif; ?>><?php echo e(@$value->title); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <div class="col-1-5 mb-2">
                                <label for="" class="form-label">Category</label>
                                <select class="form-control js-example-basic-single" name="category">
                                    <option value="">-Select-</option>
                                    <?php $__currentLoopData = $category; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e(@$value->id); ?>"
                                            <?php if($r_category == $value->id): ?> selected <?php endif; ?>><?php echo e(@$value->category_name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <div class="col-1-5 mb-2">
                                <label for="" class="form-label">Sub Category</label>
                                <select class="form-control js-example-basic-single" name="sub_category">
                                    <option value="">-Select-</option>
                                    <?php $__currentLoopData = $sub_category; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e(@$value->id); ?>"
                                            <?php if($r_sub_category == $value->id): ?> selected <?php endif; ?>>
                                            <?php echo e(@$value->sub_category_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <div class="col-1-5 mb-2">
                                <label for="" class="form-label">Qty</label>
                                <select class="form-control js-example-basic-single" name="qty">
                                    <option value="">All</option>

                                    <option value="positive" <?php if($r_qty == 'positive'): ?> selected <?php endif; ?>>Positive
                                    </option>
                                    <option value="negative" <?php if($r_qty == 'negative'): ?> selected <?php endif; ?>>Negative
                                    </option>
                                    <option value="zero" <?php if($r_qty == 'zero'): ?> selected <?php endif; ?>>Zero</option>
                                    <option value="posneg" <?php if($r_qty == 'posneg'): ?> selected <?php endif; ?>>Positive &
                                        Negative</option>

                                </select>
                            </div>

                            <div class="col-1-5 mb-2">
                                <label class="form-label">Product Type:</label>
                                <div class="form-group">
                                    <select class="form-control" name="filter_product_type" id="filter_product_type">
                                        <option value="">Select</option>
                                        <?php $__currentLoopData = $producttype; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option <?php if($ctrl_product_type == $value->id): ?> selected <?php endif; ?>
                                                value="<?php echo e(@$value->id); ?>"><?php echo e(@$value->title); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>



                            <div class="col-1 filter-field d-none">
                                <button type="submit" class="btn btn-light mt-4 float-end">
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
                <table id="long-list" class="table table-hover data-table stockRegisterTable" style="table-layout: fixed;width:100%">

                    <thead>
                        <tr>
                            <th style="width: 130px;"><?php echo app('translator')->getFromJson('Part Number'); ?></th>
                            <th style="width: 250px"><?php echo app('translator')->getFromJson('Description'); ?></th>
                            <th style="width: 100px;"><?php echo app('translator')->getFromJson('Brand'); ?></th>
                            <th style="width: 100px;"><?php echo app('translator')->getFromJson('Category'); ?></th>
                            <th style="width: 100px;"><?php echo app('translator')->getFromJson('Sub Category'); ?></th>
                            <th class="text-end" style="width: 80px;"><?php echo app('translator')->getFromJson('Bal Qty'); ?></th>
                            <?php if($show_all == 1): ?>
                                <th style="width: 100px;" class="text-end"><?php echo app('translator')->getFromJson('Avg Rate'); ?></th>
                                <th style="width: 100px;" class="text-end"><?php echo app('translator')->getFromJson('Amount'); ?></th>
                            <?php else: ?>
                                <?php if(count($show_brand) > 0): ?>
                                    <th style="width: 100px;" class="text-end"><?php echo app('translator')->getFromJson('Avg Rate'); ?></th>
                                    <th style="width: 100px;" class="text-end"><?php echo app('translator')->getFromJson('Amount'); ?></th>
                                <?php endif; ?>
                            <?php endif; ?>
                            <th style="width: 80px;" class="text-end"><?php echo app('translator')->getFromJson('Reserve Qty'); ?></th>
                            <th style="width: 80px;" class="text-end"><?php echo app('translator')->getFromJson('Avl Qty'); ?></th>

                            <th style="width: 100px;" class="text-end"><?php echo app('translator')->getFromJson('Group Qty'); ?></th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php
                            $count = 1;
                            $total_qty = 0;
                            $total_price = 0;
                            $total_value = 0;
                            $total_amount = 0;
                        ?>

                        <?php
                        if ($r_qty == 'zero') {
                            $stocklist2 = $stocklist->where('balance_qty', 0);
                        } elseif ($r_qty == 'positive') {
                            $stocklist2 = $stocklist->where('balance_qty', '>', 0);
                        } elseif ($r_qty == 'negative') {
                            $stocklist2 = $stocklist->where('balance_qty', '<', 0);
                        } elseif ($r_qty == 'posneg') {
                            $stocklist2 = $stocklist->where('balance_qty', '!=', 0); // positive + negative
                        } else {
                            $stocklist2 = $stocklist;
                        }
                        ?>


                        <?php $__currentLoopData = $stocklist2; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                            $group_qty = App\SysHelper::get_group_qty($value->partno);
                            ?>
                                <?php if(($value->type==2) || $value->type==1): ?>
                                <tr>
                                    <td>
                                        <?php if($show_all == 1): ?>
                                            <a href="<?php echo e(url('stock-ledger/' . $value->part_number)); ?>"
                                                target="_blank"><?php echo e(@$value->part_number); ?></a>
                                        <?php else: ?>
                                            <?php echo e(@$value->part_number); ?>

                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php echo e($value->description); ?>

                                    </td>
                                    <td><?php echo e($value->brand); ?></td>
                                    <td><?php echo e($value->categoryname); ?></td>
                                    <td><?php echo e($value->subcategoryname); ?></td>

                                    <?php
                                        $balance_qty = $value->balance_qty;
                                        $ledger_balance = isset($stockledgerBalances[$value->partno]) ? $stockledgerBalances[$value->partno] : null;
                                        $balanceMismatch = $ledger_balance !== null && floatval($balance_qty) !== floatval($ledger_balance);
                                        //$balance_qty += $stocklist_return->where('partno', $value->partno)->sum('qty');
                                    ?>

                                    <td class="text-end no-toggle">
                                        <a style="cursor: pointer; <?php echo e($balanceMismatch ? 'color: red; font-weight: 700;' : ''); ?>" class="font-weight-600" data-stock='<?php echo json_encode($value, 15, 512) ?>'
                                            data-balance="<?php echo e($balance_qty); ?>"
                                            onclick="openReserveStockModal(this)"><?php echo e(@App\SysHelper::com_curr_format($balance_qty, 2, '.', ',')); ?></a>
                                    </td>



                                    <?php if($show_all == 1): ?>
                                        <?php $avg = App\SysHelper::get_avg_price($value->partno, $to_date); ?>
                                        <td class="text-end no-toggle">
                                            <?php echo e(@App\SysHelper::com_curr_format($avg, 2, '.', ',')); ?>

                                        </td>
                                        <td class="text-end no-toggle">
                                            <?php if($balance_qty > 0): ?>
                                                <?php echo e(@App\SysHelper::com_curr_format($avg * $balance_qty, 2, '.', ',')); ?>

                                                
                                            <?php else: ?>
                                                <?php echo e(@App\SysHelper::com_curr_format($avg * 0, 2, '.', ',')); ?>

                                                
                                            <?php endif; ?>
                                        </td>


                                        <?php
                                            $total_price += $avg;
                                            if ($balance_qty > 0) {
                                                $total_amount += $avg * $balance_qty;
                                            }
                                        ?>
                                    <?php else: ?>
                                        <?php if(count($show_brand) > 0): ?>
                                            <?php if(in_array($value->brandid, $show_brand)): ?>
                                                <?php $avg = App\SysHelper::get_avg_price($value->partno, $to_date); ?>
                                                <td class="text-end no-toggle">
                                                    <?php echo e(@App\SysHelper::com_curr_format($avg, 2, '.', ',')); ?></td>
                                                <td class="text-end no-toggle">
                                                    <?php if($balance_qty > 0): ?>
                                                        <?php echo e(@App\SysHelper::com_curr_format($avg * $balance_qty, 2, '.', ',')); ?>

                                                        
                                                    <?php else: ?>
                                                        <?php echo e(@App\SysHelper::com_curr_format($avg * 0, 2, '.', ',')); ?>

                                                        
                                                    <?php endif; ?>

                                                    <?php
                                                        $total_price += $avg;
                                                        if ($balance_qty > 0) {
                                                            $total_amount += $avg * $balance_qty;
                                                        }
                                                    ?>

                                                </td>
                                            <?php else: ?>
                                                <td class="text-end">0</td>
                                                <td class="text-end">0</td>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php endif; ?>





                                    <?php
                                        $total_qty += $balance_qty;
                                        $reserved_qty = @App\SysHelper::get_reserved_qty(
                                            $value->stockid,
                                            $value->part_number
                                        );
                                    ?>

                                    <td class="text-end no-toggle"
                                      data-stock='<?php echo json_encode($value, 15, 512) ?>'
                                    data-balance="<?php echo e($balance_qty); ?>"
                                        onclick="openReservedStockListModal(this)"
                                        >
                                        <a href="#" style="cursor: pointer;" class="font-weight-600">
                                            <?php if($reserved_qty > 0): ?>
                                                <?php echo e(@App\SysHelper::com_curr_format($reserved_qty, 2, '.', ',')); ?>

                                            <?php else: ?>
                                                0
                                            <?php endif; ?>
                                        </a>
                                    </td>

                                    <td class="text-end no-toggle">
                                        <?php
                                            $avl_qty = $balance_qty - $reserved_qty;
                                        ?>


                                        <?php echo e(@App\SysHelper::com_curr_format($avl_qty, 2, '.', ',')); ?>


                                    </td>

                                    <td class="text-end no-toggle"><a style="cursor: pointer;" class="font-weight-600"
                                            onclick="group_qty(<?php echo e($value->partno); ?>,'<?php echo e($value->part_number); ?>')"><?php echo e(@App\SysHelper::com_curr_format($group_qty, 2, '.', ',')); ?></a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        <?php if(empty($stocklist2)): ?>
                        <tr><td colspan="11" class="text-center">No records found</td></tr>
                            
                        <?php endif; ?>

                    </tbody>

                    <?php try{ ?>
                    <tfoot>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($total_qty, 2, '.', ',')); ?></th>
                            <?php if($show_all == 1): ?>
                                <th class="text-end"></th>
                                <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($total_amount, 2, '.', ',')); ?>

                                </th>
                            <?php else: ?>
                                <?php if(count($show_brand) > 0): ?>
                                    <th class="text-end"></th>
                                    <th class="text-end">
                                        <?php echo e(@App\SysHelper::com_curr_format($total_amount, 2, '.', ',')); ?></th>
                                <?php endif; ?>
                            <?php endif; ?>
                            <th></th>
                            <th></th>

                            <th></th>
                        </tr>
                    </tfoot>
                    <?php }catch (\Exception $e) { } ?>

                </table>
                <div class="d-flex justify-content-center">
                    
                </div>
            </div>
        </div>
    </aside>




    <script>
        function group_qty(pid, pname) {
            $('#lbl_group_qty').text(pname);



            $("#loading_bg").css("display", "block");
            var partno = pid;
            var action = "<?php echo e(URL::to('get-stock-register-group-qty')); ?>";
            $.ajax({
                url: action,
                type: "GET",
                data: {
                    _token: '<?php echo e(csrf_token()); ?>',
                    partno: partno,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var len = 0;
                    if (dataResult['data'] != null) {
                        len = dataResult['data'].length;
                    }
                    if (len > 0) {
                        for (var i = 0; i < len; i++) {
                            var com = dataResult['data'][i].company_id;
                            var qty = dataResult['data'][i].balance_qty;
                            var value = formatAmount(dataResult['data'][i].avg_price);
                            if (dataResult['data'][i].avg_price == 0 || qty == 0) {
                                var rate = '0.00';
                            } else {
                                var rate = Math.abs(formatAmount(dataResult['data'][i].avg_price / qty));
                            }
                            $("#qty_" + com).text(qty);
                            $("#rate_" + com).text(rate);
                            $("#value_" + com).text(value);
                        }
                    } else {

                    }
                    $("#loading_bg").css("display", "none");
                }
            });


            $('#ModalGroupQty').modal('show');
        }
    </script>




    <div class="modal fade" id="ModalGroupQty" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" style="height: 464px !important;">


            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="poexcelimport">Group Qty - <label class="font-weight-500"
                            id="lbl_group_qty"></label></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>


                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0 border-0 shadow-none">
                        <div class="card-body p-0" style="max-height: 500px;">


                            <table class="table table-hover" id="long-list" style="table-layout: fixed;width:100%">
                                <tr>
                                    <th>&nbsp; Company</th>
                                    <th class="text-end">Qty</th>
                                    <?php if($show_all == 1): ?>
                                        <th class="text-end">Rate</th>
                                        <th class="text-end">Value</th>
                                    <?php endif; ?>
                                </tr>
                                <?php if(count($company_list) > 0): ?>
                                    <?php $__currentLoopData = $company_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>&nbsp; <?php echo e($list->company_name); ?></td>
                                            <td class="text-end"><label id="qty_<?php echo e($list->id); ?>">0</label>
                                            </td>
                                            <?php if($show_all == 1): ?>
                                                <td class="text-end"><label id="rate_<?php echo e($list->id); ?>">0.00</label>
                                                </td>
                                                <td class="text-end"><label id="value_<?php echo e($list->id); ?>">0.00</label>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </table>



                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>

    <script>
        function openReserveStockModal(el) {

                const value = JSON.parse(el.dataset.stock);
    const balance_qty = el.dataset.balance;

            // Show loading indicator
            $('#loading_bg').show();

            // value = JSON.parse(value);
            $('#reserve_stock_id').val(value.stockid);
            $('#reserve_part_number').val(value.part_number);

            // Update modal title with available quantity
            $('#reserveStockModalLabel').text('Reserve Stock - ' + value.part_number + ' (' +
                balance_qty + ')');

            // Clear previous values
            $('#reserve_customer_id').val('').trigger('change');
            $('#reserve_sales_person').val('').trigger('change');
            $('#reserve_qty').val('');

            $('#reserveStockModal').modal('show');
            $('#loading_bg').hide(); // Hide loader after modal is shown


        }

     function openReservedStockListModal(el) {
    // Show loading indicator
    $('#loading_bg').show();

    // data-stock is ALREADY an object
    var value = $(el).data('stock');
    var balance_qty = $(el).data('balance');

    console.log('Opening reserved stock list for:', value);

    $('#reservedStockListModalLabel').text('Reserved Stock - ' + value.part_number);
    $('#reserved_stock_partno').val(value.stockid);
    $('#reserved_stock_balance_qty').val(balance_qty);
    $('#reserved_stock_part_number').val(value.part_number);

    // Load reserved stock data via AJAX
    loadReservedStockData(value.stockid, value.part_number, balance_qty);

    $('#reservedStockListModal').modal('show');
}


        function loadReservedStockData(stockId, partNumber, balance_qty) {
            $('#reservedStockTableBody').html('<tr><td colspan="9" class="text-center">Loading...</td></tr>');

            $('#reservedStockListTitle').text('Reserved Stock - ' + partNumber);

            $.ajax({
                url: "<?php echo e(URL::to('get-reserved-stock-list')); ?>",
                type: "GET",
                data: {
                    _token: '<?php echo e(csrf_token()); ?>',
                    stock_id: stockId,
                    part_number: partNumber
                },
                success: function(response) {
                    console.log("response", response);
                    if (response.success && response.data.length > 0) {
                        let tableBody = '';
                        response.data.forEach(function(item) {
                            tableBody += `
                                <tr>
                                    <td class="text-center" style="padding: 1px 3px;">${item.doc_number}</td>
                                    <td class="text-center" style="padding: 1px 3px;">${item.deal_id || '-'}</td>
                                    <td style="padding: 1px 3px;">${item.customer_name}</td>
                                    <td style="padding: 1px 3px;">${item.sales_person || 'N/A'}</td>
                                    <td style="padding: 1px 3px;" class="text-center">${item.reserved_qty}</td>
                                    <td style="padding: 1px 3px;" class="text-center">${item.reserve_date}</td>
                                    <td style="padding: 1px 3px;" class="text-start">${item.created_by} ${item.created_at} </td>
                                    <td style="padding: 1px 3px;" class="text-start">${item.updated_by} ${item.updated_at}</td>
                                    <td style="padding: 1px 3px;" class="text-center">
                                        <div class="d-flex justify-content-center align-items-center">
                                        <button type="button" onclick="editReservedStock(event, ${item.id}, ${stockId}, '${partNumber}', ${balance_qty})" class="btn btn-sm btn-light"><i class="ico icon-outline-pen-2 text-dark" style="font-size: 16px;"></i></button>
                                     <button type="button" class="btn btn-sm btn-light" onclick="deleteReservedStock(${item.id})">
                                            <i class="ico icon-outline-trash-bin-minimalistic text-dark" style="font-size: 16px;"></i>
                                        </button>
                                      </div>
                                    </td>
                                </tr>
                            `;
                        });
                        $('#reservedStockTableBody').html(tableBody);
                    } else {
                        $('#reservedStockTableBody').html(
                            '<tr><td colspan="9" class="text-center text-muted">No reserved stock found</td></tr>'
                        );
                    }
                    $('#loading_bg').hide(); // Hide loader after data is loaded
                },
                error: function() {
                    $('#reservedStockTableBody').html(
                        '<tr><td colspan="5" class="text-center text-danger">Error loading data</td></tr>');
                    $('#loading_bg').hide(); // Hide loader even on error
                }
            });
        }

        function editReservedStock(event, id, stockId, partNumber, balance_qty) {
            // Prevent default action and event bubbling
            console.log('editReservedStock called with id:', id);
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }

            $('#loading_bg').show(); // Hide loader after data is loaded



            // Fetch the reserved stock data
            $.ajax({
                url: "<?php echo e(URL::to('get-reserved-stock-detail')); ?>",
                type: "GET",
                data: {
                    reserve_id: id
                },
                success: function(response) {
                    if (response.success) {
                        const data = response.data;

                        var current_reserved_qty = parseFloat(data.reserve_qty) || 0;


                        // Populate the edit form
                        $('#edit_stock_id').val(stockId);
                        $('#edit_reserve_id').val(data.id);
                        $('#edit_customer_id').val(data.customer_id).trigger('change');
                        $('#edit_reserve_deal_id').val(data.deal_id);
                        $('#edit_sales_person_id').val(data.sales_person_id);
                        $('#edit_reserve_qty').val(data.reserve_qty);
                        $('#edit_reserve_date').val(data.reserve_date ? data.reserve_date
                            .split('-').reverse().join('/') : '');

                        // Set max attribute and show available quantity


                        $('#loading_bg').hide(); // Hide loader after data is loaded


                        $('#editReservedStockModalLabel').text('Edit - ' + data.doc_number);

                        // Show the modal
                        $('#editReservedStockModal').modal('show');

                    } else {
                        $('#loading_bg').hide(); // Hide loader after data is loaded
                        alert('Error loading reserved stock data: ' + response.error);
                    }
                },
                error: function(xhr, status, error) {
                    $('#loading_bg').hide(); // Hide loader after data is loaded
                    console.log('AJAX Error:', xhr.responseText);
                    alert('Error loading reserved stock data');
                }
            });
        }

        function deleteReservedStock(id) {
            if (confirm('Are you sure you want to delete this reserved stock?')) {
                $.ajax({
                    url: "<?php echo e(URL::to('delete-reserved-stock')); ?>",
                    type: "POST",
                    data: {
                        _token: '<?php echo e(csrf_token()); ?>',
                        _method: 'DELETE',
                        reserve_id: id,
                    },
                    success: function(response) {
                        if (response.success) {
                            // Reload the table data
                            let stockId = $('#reserved_stock_partno').val();


                            location.reload();


                        } else {
                            alert('Error deleting reserved stock: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('AJAX Error:', xhr.responseText);
                        let errorMessage = 'Error deleting reserved stock';
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMessage += ': ' + xhr.responseJSON.error;
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage += ': ' + xhr.responseJSON.message;
                        }
                        alert(errorMessage);
                    }
                });
            }
        }
    </script>





    <div class="modal side-panel fade" id="reserveStockModal" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 70rem;top:31%;left:34%">
            <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => false, 'url' => 'store-reserve-qty', 'id' => 'reserve_stock_form', 'method' => 'POST'])); ?>


            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="reserveStockModalLabel"></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body">
                            <input type="hidden" name="reserve_stock_id" id="reserve_stock_id" value="">
                            <input type="hidden" name="reserve_part_number" id="reserve_part_number" value="">
                            <div class="row row-cols-5">



                                <div class="col mb-3">
                                    <label for="reserve_deal_id" class="form-label">Deal ID</label>
                                    <input type="text" class="form-control" id="reserve_deal_id"
                                        name="reserve_deal_id" value="">
                                </div>

                                <div class="col mb-3">
                                    <?php
                                        $customer_list = @App\SysHelper::get_customer_list_deal_lead_all_role();

                                    ?>
                                    <label for="reserve_customer_name" class="form-label">Customer Name <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control js-example-basic-single" name="reserve_customer_id"
                                        id="reserve_customer_id" required>
                                        <option value=""></option>
                                        <?php $__currentLoopData = $customer_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e(@$value->id); ?>"><?php echo e(trim(@$value->name)); ?><?php if(@App\SysHelper::getCompanyCodeSettings()['is_customer_code'] == 1): ?>
                                                    (<?php echo e(trim(@$value->code)); ?>)
                                                <?php endif; ?>
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>

                                <div class="col mb-3">
                                    <label for="reserve_sales_person" class="form-label">Sales Person</label>
                                    <select class="form-control js-example-basic-single" id="reserve_sales_person"
                                        name="reserve_sales_person" required>

                                        <?php
                                            $sales_persons = @App\SysHelper::get_sales_persons();
                                        ?>
                                        <?php $__currentLoopData = $sales_persons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $person): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($person->user_id); ?>"><?php echo e($person->full_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>

                                <div class="col mb-3">
                                    <label for="reserve_qty" class="form-label">Reserve Qty <span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="reserve_qty" name="reserve_qty"
                                        step="1" required>

                                </div>

                                <div class="col mb-3">
                                    <label for="reserve_date" class="form-label">Reserve Date <span
                                            class="text-danger">*</span></label>
                                    <?php
                                        $reserve_date_default = \Carbon\Carbon::now()->addDays(3)->format('d/m/Y');
                                    ?>
                                    <input type="text" class="form-control date-picker" id="reserve_date"
                                        value="<?php echo e($reserve_date_default); ?>" name="reserve_date" autocomplete="off"
                                        required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-light add-btn ms-2" id="add-btn-modal">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                    </button>
                </div>
            </div>
            <?php echo e(Form::close()); ?>


        </div>
    </div>




    <script>
        $(document).ready(function() {

            $('#reserve_deal_id').on('blur', function() {
                let deal_code = $(this).val().trim();
                let part_number_id = $('#reserve_stock_id').val();

                console.log("Checking deal code:", deal_code, "for part number ID:", part_number_id);

                if (deal_code === "") return;

                $.ajax({
                    url: '/check-deal-code',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        deal_code: deal_code,
                        part_number_id: part_number_id
                    },
                    success: function(response) {

                        console.log("AJAX response:", response);

                        // ❌ Deal not found
                        if (!response.deal_id) {
                            toastr.error('Please enter a correct deal code.',
                                'Invalid Deal Code');
                            $('#reserve_deal_id').val('').focus();
                            $('#reserve_qty').removeAttr('max'); // clear max
                            $('#reserve_qty').attr('placeholder', '');
                            return;
                        }

                        // ✔ Deal is valid
                        console.log("Deal ID:", response.deal_id);



                        // If item exists, set max qty
                        if (response.item_exists && response.item_details.qty > 0) {

                            console.log("Item exists in quotation with qty:", response
                                .item_details.qty);

                            let maxQty = parseInt(response.item_details.qty);

                            console.log(response);
                            // Set the max attribute
                            $('#reserve_qty').attr('max', maxQty);
                            $('#reserve_qty').attr('placeholder', 'Max Qty: ' + maxQty);
                            $('#reserve_sales_person').val(response.item_details.sales_person).trigger('change');
                            $('#reserve_customer_id').val(response.item_details.customer_id).trigger('change');


                            toastr.info(
                                'This item already exists in the quotation. Max reservable qty: ' +
                                maxQty,
                                'Item Found'
                            );

                        } else {
                            console.log("Item does not exist in quotation.");
                            $('#reserve_deal_id').val('').focus();
                            toastr.info(
                                'The item does not exist in the quotation. Please check the deal code.',
                                'Item Not Found');
                            // Item not in quotation → remove max limit
                            $('#reserve_qty').removeAttr('max');
                            $('#reserve_qty').attr('placeholder', '');

                        }
                    },
                    error: function() {
                        toastr.error('Something went wrong. Please try again.', 'Error');
                    }
                });
            });

        });


        // $(document).ready(function() {

        //     $('#edit_reserve_deal_id').on('blur', function() {
        //         let deal_code = $(this).val().trim();
        //         let part_number_id = $('#edit_stock_id').val();

        //         if (deal_code === "") return;

        //         $.ajax({
        //             url: '/check-deal-code',
        //             type: 'GET',
        //             dataType: 'json',
        //             data: {
        //                 deal_code: deal_code,
        //                 part_number_id: part_number_id
        //             },
        //             success: function(response) {

        //                 // ❌ Deal not found
        //                 if (!response.deal_id) {
        //                     toastr.error('Please enter a correct deal code.',
        //                         'Invalid Deal Code');
        //                     $('#edit_reserve_deal_id').val('').focus();
        //                     $('#edit_reserve_qty').removeAttr('max'); // clear max
        //                     return;
        //                 }

        //                 // ✔ Deal is valid
        //                 console.log("Deal ID:", response.deal_id);



        //                 // If item exists, set max qty
        //                 if (response.item_exists && response.item_details.qty > 0) {

        //                     console.log("Item exists in quotation with qty:", response
        //                         .item_details.qty);

        //                     let maxQty = parseInt(response.item_details.qty);

        //                     // Set the max attribute
        //                     $('#edit_reserve_qty').attr('max', maxQty);
        //                     $('#edit_reserve_qty').attr('placeholder', 'Max Qty: ' + maxQty);


        //                     toastr.info(
        //                         'This item already exists in the quotation. Max reservable qty: ' +
        //                         maxQty,
        //                         'Item Found'
        //                     );

        //                 } else {
        //                     $('#edit_reserve_deal_id').val('').focus();
        //                     toastr.info(
        //                         'The item does not exist in the quotation. Please check the deal code.',
        //                         'Item Not Found');
        //                     // Item not in quotation → remove max limit
        //                     $('#edit_reserve_qty').removeAttr('max');
        //                     $('#edit_reserve_qty').attr('placeholder', '');
        //                 }
        //             },
        //             error: function() {
        //                 toastr.error('Something went wrong. Please try again.', 'Error');
        //             }
        //         });
        //     });

        // });



        $(document).ready(function() {
            $("#reserve_customer_id").on("change", function() {


                get_sales_person($(this).val());
            });
        });

        function get_sales_person(id) {
            $("#loading_bg").css("display", "block");

            $.ajax({
                url: "<?php echo e(URL::to('get-salesperson-list')); ?>",
                type: "GET",
                data: {
                    _token: '<?php echo e(csrf_token()); ?>',
                    id: id,
                },
                cache: false,
                success: function(dataResult) {

                    // Convert only if string
                    if (typeof dataResult === "string") {
                        dataResult = JSON.parse(dataResult);
                    }

                    if (dataResult.data && dataResult.data.length > 0) {

                        let returnedList = dataResult.data;

                        // Take first salesperson ID from response
                        let firstId = returnedList[0].id;

                        // Match & select it
                        $("#reserve_sales_person").val(firstId).trigger("change");
                    }

                    $("#loading_bg").css("display", "none");
                }
            });
        }
    </script>


    <div class="modal fade" id="reservedStockListModal" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" style="top:10%;left:10%;max-width:100rem">
            <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => false, 'url' => 'store-reserve-qty', 'id' => 'reserve_stock_form', 'method' => 'POST'])); ?>


            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="reservedStockListTitle"></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body p-0">
                            <input type="hidden" id="reserved_stock_partno" value="">
                            <input type="hidden" id="reserved_stock_balance_qty" value="">
                            <input type="hidden" id="reserved_stock_part_number" value="">
                            <div class="table-responsive">
                                <table class="table table-hover" id="long-list" style="table-layout: fixed;width:100%">
                                    <thead>
                                        <tr>
                                            <th width="7%" class="text-center">Doc Number</th>
                                            <th width="7%" class="text-center">Deal Code</th>
                                            <th width="19%">Customer Name</th>
                                            <th width="15%">Sales Person</th>
                                            <th width="5%" class="text-center">Res. Qty</th>
                                            <th width="8%" class="text-center">Res. Date</th>
                                            <th width="15%" class="text-start">Created By</th>
                                            <th width="15%" class="text-start">Updated By</th>
                                            <th width="7%" class="text-center">Actions</th>

                                        </tr>
                                    </thead>
                                    <tbody id="reservedStockTableBody">
                                        <!-- Dynamic content will be loaded here -->
                                    </tbody>
                                </table>
                            </div>

                            <div class="row" id="noDataRow" style="display: none;">
                                <div class="col-md-12 text-center">
                                    <p class="text-muted">No reserved stock found for this item.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">

                </div>
            </div>
            <?php echo e(Form::close()); ?>


        </div>
    </div>





    <div class="modal side-panel fade" id="editReservedStockModal" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 70rem;top:31%;left:34%">
            <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => false, 'url' => 'update-reserved-stock', 'id' => 'editReservedStockForm', 'method' => 'PUT'])); ?>


            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editReservedStockModalLabel">Edit - </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body pb-0">

                            <?php echo e(Form::hidden('stock_id', '', ['id' => 'edit_stock_id'])); ?>

                            <?php echo e(Form::hidden('reserve_id', '', ['id' => 'edit_reserve_id'])); ?>


                            <div class="row row-cols-4">

                                

                                <div class="col">
                                    <div class="primary_input mb-3">
                                        <label class="primary_input_label" for="edit_customer_name">Customer Name <span
                                                class="text-danger">*</span></label>

                                        <select class="form-control js-example-basic-single" name="edit_customer_id"
                                            id="edit_customer_id" required>
                                            <option value=""></option>
                                            <?php $__currentLoopData = $customer_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e(@$value->id); ?>"><?php echo e(trim(@$value->name)); ?><?php if(@App\SysHelper::getCompanyCodeSettings()['is_customer_code'] == 1): ?>
                                                        (<?php echo e(trim(@$value->code)); ?>)
                                                    <?php endif; ?>
                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>

                                        
                                    </div>
                                </div>

                                <div class="col">
                                    <div class="primary_input mb-3">
                                        <label class="primary_input_label" for="edit_sales_person_id">Sales Person <span
                                                class="text-danger">*</span></label>
                                        <?php echo e(Form::select('sales_person_id', ['' => 'Select Sales Person'] + $sales_persons->pluck('full_name', 'user_id')->toArray(), '', ['class' => 'primary_select form-control js-example-basic-single', 'id' => 'edit_sales_person_id', 'required' => true])); ?>

                                    </div>
                                </div>

                                <div class="col">
                                    <div class="primary_input mb-3">
                                        <label class="primary_input_label" for="edit_reserve_qty">Reserve Qty <span
                                                class="text-danger">*</span></label>
                                        <?php echo e(Form::number('reserve_qty', '', ['class' => 'primary_input_field form-control', 'id' => 'edit_reserve_qty', 'min' => 1, 'step' => 1, 'required' => true])); ?>

                                        <small id="edit_available_qty_display" class="text-muted"></small>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="primary_input mb-3">
                                        <label class="primary_input_label" for="edit_reserve_date">Reserve Date <span
                                                class="text-danger">*</span></label>
                                        <?php echo e(Form::text('reserve_date', '', ['class' => 'primary_input_field form-control date-picker', 'id' => 'edit_reserve_date', 'required' => true])); ?>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">


                    <button type="submit" class="btn btn-light add-btn ms-2" id="add-btn-modal">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Update
                    </button>

                    
                    <button type="submit" class="btn btn-light add-btn ms-2" name="release_btn" value="release_stock">
                        <i class="ico icon-bold-transfer-horizontal text-success"></i> Release/Deliver Stock
                    </button>

                </div>
            </div>
            <?php echo e(Form::close()); ?>


        </div>
    </div>







    <?php } catch (\Exception $e) { ?> <?php echo e($e); ?> <?php  } ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backEnd.newmasterpage', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php $__env->startSection('mainContent'); ?>
    <script src="https://cdn.jsdelivr.net/npm/exceljs@4.3.0/dist/exceljs.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
      <script>
        let isFullList = false;

        function list_style_new() {
            const leftNav = document.querySelector('.left-nav');
            const content = document.querySelector('.content-container');



            if (!isFullList) {
                // Switch to FULL LIST VIEW
                isFullList = true;

                leftNav.classList.remove('col-3');
                leftNav.classList.add('col-12');
                leftNav.style.width = '100%';

                content.classList.add('d-none');

                $('#long-list').removeClass('d-none');
                $('#short-list').addClass('d-none');


                $('#filters-long').removeClass('d-none');
                $('#filters-short').addClass('d-none');

                sessionStorage.setItem('listViewSalesReturnList', 'long');
            } else {
                // Switch to COMPACT VIEW
                isFullList = false;


                leftNav.classList.remove('col-12');
                leftNav.classList.add('col-3');
                leftNav.style.width = '';



                content.classList.remove('d-none');

                $('#long-list').addClass('d-none');


                $('#short-list').removeClass('d-none');

                $('#filters-short').removeClass('d-none');
                $('#filters-long').addClass('d-none');

                sessionStorage.setItem('listViewSalesReturnList', 'short');

            }


        }


        //added ny kp
        function toggleLongFilters() {

            document.querySelectorAll('#filters-long .filter-field').forEach(el => {
                el.classList.toggle('d-none');
            });
        }




        // Initialize view from sessionStorage (tab-specific)
        document.addEventListener('DOMContentLoaded', () => {
            // Check if we have customer_action parameter (add/edit mode)
            const urlParams = new URLSearchParams(window.location.search);
            const hasCustomerAction = urlParams.has('sr_action');
            
            // If in add/edit mode, force short view
            if (hasCustomerAction) {
                sessionStorage.setItem('listViewSalesReturnList', 'short');
                isFullList = true; // Set to true so toggle switches to short
                list_style_new(); // Switch to short view
            } else {
                // Normal behavior - use saved view from sessionStorage
                const savedView = sessionStorage.getItem('listViewSalesReturnList');
                if (savedView === 'long') {
                    isFullList = false; // so that toggling once activates full view
                    list_style_new();
                } else {
                    // Default to short view
                    isFullList = true; // so that toggling once activates short view
                    list_style_new();
                }
            }

            // Attach event to sidebar links to force short view on navigation
            document.querySelectorAll('.sub-nav-item').forEach(link => {
                link.addEventListener('click', () => {
                    sessionStorage.setItem('listViewSalesReturnList', 'short');
                });
            });



        });
    </script>



    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>

    <?php try { ?>

    <aside class="left-nav col-3" id="leftSidebar">
        <div class="resizer" id="sidebarResizer"></div>
        <div class="short-list" id="filters-short">
            <h4 class="mb-2">Sales Return
            </h4>



            <div class="search-filter-container mb-4">
                <div class="input-group flex-nowrap">
                    <input type="text" class="form-control" id="search_invoice" placeholder="Document No"
                        aria-label="Search" aria-describedby="addon-wrapping">
                </div>


                <button type="button" class="btn btn-light" id="list_style_button" onclick="list_style_new()">
                    <i class="ico icon-outline-list-down"></i>
                </button>
            </div>



        </div>

        <div class="long-list  d-none" id="filters-long">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="mb-2">Sales Return
                </h4>
                <div class="search-filter-container mb-0">

                    <input type="text" id="tableSearch" class="form-control"
                        style="font-size:13px; width: 350px; 
                        top: 12px;
                        right: 120px;"
                        placeholder="Search">

                    <button class="btn btn-light" id="exportExcelSalesReturn">
                        <i class="ico icon-outline-export text-success"></i> Export
                    </button>

                    





                    <button class="btn btn-light" onclick="toggleLongFilters()">
                        <i class="ico icon-outline-magnifer"></i>
                    </button>
                    <button class="btn btn-light" id="list_style_button" onclick="list_style_new()">
                        <i class="ico icon-outline-list-down"></i>
                    </button>
                </div>
            </div>

            <div class="search-filter-container mt-1 mb-4 filter-field d-none border">

                <div class="card" style="width:100%">
                    <div class="card-body">
                        <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'sales-return', 'method' => 'get', 'id' => 'sales-return-search'])); ?>

                        <div class="row">

                            <div class="col-1-5 mb-2">
                                <label for="" class="form-label">Documents No</label>
                                <input class="form-control" type="text" autocomplete="off" name="documents_number"
                                    value="<?php echo e(@$ctrl_doc_number); ?>">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="" class="form-label">Customer</label>
                                <select class="form-control js-account-select" name="customer" id="">
                                    <option value=""></option>

                                </select>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="" class="form-label">Supplier</label>
                                <input class="form-control" type="text" autocomplete="off" name="supplier"
                                    value="<?php echo e(@$ctrl_supplier); ?>">
                            </div>
                            <div class="col-1-5 mb-2">
                                <label for="" class="form-label">Deal ID</label>
                                <input class="form-control" type="text" autocomplete="off" name="deal_number"
                                    value="<?php echo e(@$ctrl_deal_number); ?>">
                            </div>
                            <div class="col-1-5 mb-2">
                                <label for="" class="form-label">Sales Invoice No</label>
                                <input class="form-control" type="text" autocomplete="off" name="sales_invoice_number"
                                    value="<?php echo e(@$ctrl_sales_invoice_number); ?>">
                            </div>
                            <div class="col-1-5 mb-2">
                                <label for="" class="form-label">DLN No</label>
                                <input class="form-control" type="text" autocomplete="off" name="dln_number"
                                    value="<?php echo e(@$ctrl_dln_number); ?>">
                            </div>
                            <div class="col-1-5 mb-2">
                                <label for="" class="form-label">Amount</label>
                                <input class="form-control" type="number" autocomplete="off" name="amount"
                                    value="<?php echo e(@$ctrl_amount); ?>">
                            </div>
                            <div class="col-1 mb-2">
                                <label for="" class="form-label">Date</label>
                                <input class="form-control date-picker" type="text" autocomplete="off" name="date"
                                    value="<?php echo e(@$ctrl_date); ?>">
                            </div>

                            <div class="col-1"><br />
                                <button type="submit" class="btn btn-light">
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

            <ul id="short-list" class="nav flex-column nav-pills" id="companyTabNavs" role="tablist">
                <?php if(count($salesreturn) > 0): ?>
                    <?php $__currentLoopData = $salesreturn; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="nav-item w-100" role="presentation">
                            <button href="javascript:void(0)"
                                class="nav-link data-item <?php echo e($active_id == $value->id ? 'active' : ''); ?>"
                                data-id="<?php echo e($value->id); ?>">

                                <div class="row w-100">
                                     <div class="col-12">
                                        <label
                                            class="form-control-plaintext truncate-text"><?php echo e($value->accountname->account_name); ?>

                                        
                                            <?php if(@App\SysHelper::getCompanyCodeSettings()['is_customer_code']): ?>
                                                 (<?php echo e($value->accountname->account_code); ?>)
                                                
                                            <?php endif; ?>
                                        </label>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-control-plaintext" style="font-size: 11px"><?php echo e($value->doc_number); ?></div>
                                    </div>
                                    <div class="col-4 pl-2">
                                        <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                                            <?php echo e(date('d/m/Y', strtotime(@$value->doc_date))); ?></div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                                            <?php echo e(@App\SysHelper::com_curr_format($value->amount, 2, '.', ',')); ?>

                                            <?php echo e($value->currency_name->code); ?></div>
                                    </div>
                                   
                                </div>

                            </button>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </ul>

            <div class="table-responsive mb-4 mt-4">
                <table id="long-list" class="table table-hover d-none data-table" style="table-layout: fixed;width:100%">
                    <thead>
                        <tr>
                            <th style="width:100px" class="text-center"><?php echo app('translator')->getFromJson('SRT Date'); ?></th>
                            <th style="width:100px" class="text-center"><?php echo app('translator')->getFromJson('SRT No'); ?></th>
                            <th><?php echo app('translator')->getFromJson('Customer'); ?></th>
                            <th><?php echo app('translator')->getFromJson('Supplier'); ?></th>
                            <th style="width:130px" class="text-end"><?php echo app('translator')->getFromJson('Taxable Amount'); ?></th>
                            <th style="width:100px" class="text-end"><?php echo app('translator')->getFromJson('Tax'); ?></th>
                            <th style="width:100px" class="text-end"><?php echo app('translator')->getFromJson('Amount'); ?></th>
                            <th style="width:100px" class="text-center"><?php echo app('translator')->getFromJson('Deal ID'); ?></th>
                            <th style="width:100px" class="text-start"><?php echo app('translator')->getFromJson('Sales Person'); ?></th>

                            <th style="width:100px" class="text-center"><?php echo app('translator')->getFromJson('SIV No'); ?></th>
                            <th style="width:100px" class="text-center"><?php echo app('translator')->getFromJson('DLN No'); ?></th>
                            <th style="width:30px" class="text-center"><i class="ico icon-bold-paperclip"></i></th>
                            
                            <th style="width:140px" class="text-center"><?php echo app('translator')->getFromJson('lang.action'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $count =1; ?>
                        <?php $__currentLoopData = $salesreturn; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                        <?php
                      $edit_list = @App\SysSalesReturnList::select('id', 'sr_id', 'taxableamount', 'vatamount')
    ->where('sr_id', $value->id)
    ->orderBy('sort_id')
    ->get();

$total_taxable = $edit_list->sum('taxableamount');
$total_vat = $edit_list->sum('vatamount');
$total_grand = $total_taxable + $total_vat;


                        ?>
                    
                            <tr <?php if(@$value->status == 2): ?> class="bg-dark" <?php endif; ?>>
                                <td class="text-center"><?php echo e(date('d/m/Y', strtotime(@$value->doc_date))); ?></td>
                                <td class="text-center data-item" data-id="<?php echo e(@$value->id); ?>" onclick="list_style_new()"><a ><?php echo e(@$value->doc_number); ?></a></td>
                                <td><?php echo e(@$value->accountname->account_name); ?></td>
                                <td><?php echo e(@$value->supplier_name); ?></td>

                                <td class="text-end"><?php echo e(@App\SysHelper::com_curr_format(@$total_taxable, 2, '.', ',')); ?></td>
                                <td class="text-end"><?php echo e(@App\SysHelper::com_curr_format(@$total_vat, 2, '.', ',')); ?></td>
                                 <td class="text-end">
                                    <?php echo e(@App\SysHelper::com_curr_format(@$value->amount, 2, '.', ',')); ?></td>

                                 <td class="text-center">
                                    <?php if(@$value->code == ''): ?>
                                        --
                                    <?php else: ?>
                                        <a href="<?php echo e(url('get-url-deal-track/' . $value->code)); ?>"
                                            target="_blank"><?php echo e(@$value->code); ?></a>
                                    <?php endif; ?>
                                </td>

                                <td><?php echo e(@$value->salesman_name->full_name); ?></td>

                                <td class="text-center">
                                    <?php if(@$value->si_doc_number == ''): ?>
                                        --
                                    <?php else: ?>
                                        <a href="<?php echo e(url('get-url-sales-invoice/' . $value->si_doc_number)); ?>"
                                            target="_blank"><?php echo e(@$value->si_doc_number); ?></a>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if(@$value->dn_doc_number == ''): ?>
                                        --
                                    <?php else: ?>
                                        <a href="<?php echo e(url('get-url-delivery-note/' . $value->dn_doc_number)); ?>"
                                            target="_blank"><?php echo e(@$value->dn_doc_number); ?></a>
                                    <?php endif; ?>
                                </td>

                                
                                      <td class="text-center">
                                         <?php if(!empty($value->attachment)): ?>
                                                <a href="<?php echo e(asset('public/uploads/sales_return_doc/')); ?>/<?php echo e($value->attachment); ?>" target="_blank">
                                                    <i class="ico icon-bold-paperclip" aria-hidden="true"></i>
                                                </a>
                                            <?php endif; ?>

                                        </td>
                               
                               
                                <td class="text-center">


                                    <div class="d-flex justify-content-center align-items-center gap-1">


                                        <a href="<?php echo e(url('sales-return/' . $value->id . '?sr_action=edit')); ?>"
                                            onclick="list_style_new()" class="btn btn-sm btn-light" title="Comments">
                                            <i class="ico icon-outline-pen-2" style="font-size: 16px;"></i>
                                        </a>


                                        <a class=" btn-sm btn-light"
                                            href="<?php echo e(url('sales-return/' . $value->id . '/download')); ?>"><i
                                                class="ico icon-bold-download-minimalistic text-dark"
                                                style="font-size: 16px;"></i></a>


                                      
                                            <?php if($value->status == 2): ?>
                                                <a class="btn btn-light btn-sm"
                                                   href="<?php echo e(url('sales-return/'.$value->id.'/restore')); ?>"
                                                    onclick="return confirm('Are you sure you want to restore this item?');">
                                                    <i class="ico icon-bold-restart" style="font-size: 16px;"></i>
                                                </a>
                                            <?php else: ?>
                                                <a class="btn btn-light btn-sm"
                                                    href="<?php echo e(url('sales-return/'.$value->id.'/delete')); ?>"
                                                    onclick="return confirm('Are you sure you want to delete this item?');">
                                                    <i class="ico  icon-outline-trash-bin-minimalistic text-dark"
                                                        style="font-size: 16px;"></i>
                                                </a>
                                            <?php endif; ?>
                                       

                                    </div>



                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </aside>

    <div class="content-container col-9">
        <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">

            <script>
                $(document).ready(function() {
                    // Delegated click works for both static + dynamic .data-item
                    $(document).on('click', '.data-item', function() {

                        $("#loading_bg").css("display", "block");

                        var id = $(this).data('id');

                        // highlight active
                        $('.data-item').removeClass('active');
                        $(this).addClass('active');


                        var newUrl = "<?php echo e(url('sales-return')); ?>/" + id;
                        window.history.pushState({
                            path: newUrl
                        }, '', newUrl);


                        var action = "<?php echo e(URL::to('sales-return-details')); ?>/" + id;

                        $.ajax({
                            url: action,
                            method: 'GET',
                            success: function(response) {
                                $('#data-details').html(response);
                            },
                            error: function() {
                                $('#data-details').html(
                                    '<p class="text-danger">Error loading details.</p>');
                            },
                            complete: function() {
                                // always hide loading, success or error
                                $("#loading_bg").css("display", "none");
                            }
                        });
                    });
                });
            </script>


            <div class="" role="tabpanel" aria-labelledby="po-tab" id="data-details">

                <?php if($action === 'add'): ?>
                    <?php echo $__env->make('backEnd.salesreturn.sr_add', $addData, \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>


                    
                <?php elseif($action === 'edit'): ?>
                    <?php echo $__env->make('backEnd.salesreturn.sr_edit', $editData, \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                    
                <?php elseif(isset($data) && !empty($data)): ?>
                    <?php echo $__env->make('backEnd.salesreturn.sr_details', $data, \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <?php else: ?>
                    <div onclick="window.location.href='<?php echo e(url('sales-return?sr_action=add')); ?>'"
                        class="container-fluid d-flex flex-column justify-content-center align-items-center"
                        style="min-height: 90vh;">

                        <!-- Icon + Heading -->
                        <div class="text-center mb-4">
                            <div class="rounded-circle bg-success text-white d-flex justify-content-center align-items-center mx-auto"
                                style="width: 80px; height: 80px; font-size: 36px;cursor:pointer">
                                <i class="ico icon-outline-add-square"></i>
                            </div>
                            <h1 class="fw-bold mt-3" style="cursor:pointer"> Sales Return</h1>
                            
                        </div>

                    </div>
                <?php endif; ?>
            </div>




        </div>
    </div>


    <script>
        $(document).ready(function() {

            $('#search_invoice').on('input', function() {
                var query = $(this).val();

                $.ajax({
                    url: "<?php echo e(route('sales-return.search')); ?>",
                    type: "GET",
                    data: {
                        query: query
                    },
                    success: function(data) {
                        $('#short-list').html('');

                        if (data.length > 0) {
                            $.each(data, function(index, invoice) {

                                let ims = `<li class="nav-item w-100" role="presentation">
    <button href="javascript:void(0)" class="nav-link data-item" data-id="${invoice.id}">
        <div class="row w-100">
             <div class="col-12">
                <label class="form-control-plaintext truncate-text">
                    ${invoice.account_code} - ${invoice.account_name}
                </label>
            </div>
            <div class="col-4">
                <div class="form-control-plaintext" style="font-size: 11px">${invoice.doc_number}</div>
            </div>
            <div class="col-4 pl-2">
                <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                    ${get_format_date(invoice.doc_date)}
                </div>
            </div>
            <div class="col-4 text-end">
                <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                    ${Number(invoice.amount).toLocaleString()} ${invoice.currency_code}
                </div>
            </div>
           
        </div>
    </button>
</li>`;
                                $('#short-list').append(ims);
                            });
                        } else {
                            $('#short-list').html(
                                '<div class="p-2">No results found</div>');
                        }
                    }
                });
            });

            $('#exportExcelSalesReturn').on('click', function(e) {
                e.preventDefault();

                var companyName = <?php echo json_encode(@App\SysCompany::find(session('logged_session_data.company_id') ?? '')->trade_name ?? '', 15, 512) ?>;
                var totalReturns = <?php echo json_encode($salesreturn->count() ?? 0, 15, 512) ?>;
                var dateValue = <?php echo json_encode($ctrl_date ?? '', 15, 512) ?>;

                function formatDMY(value) {
                    if (!value) return '';
                    var normalized = value.trim().replace(/-/g, '/');
                    var parts = normalized.split('/');
                    if (parts.length === 3) {
                        if (parts[0].length === 4) {
                            return parts[2] + '/' + parts[1] + '/' + parts[0];
                        }
                        return normalized;
                    }
                    return value;
                }

                var $table = $('#long-list');
                var visibleColIndexes = [];
                var headerLabels = [];
                var lastIndex = $table.find('thead tr th').length - 1;

                $table.find('thead tr th').each(function(i) {
                    if (i === lastIndex) return;
                    if ($(this).css('display') !== 'none') {
                        var label = $(this).text().trim();
                        if (['actions', 'action'].includes(label.toLowerCase().trim())) {
                            return;
                        }
                        visibleColIndexes.push(i);
                        headerLabels.push(label);
                    }
                });

                var rows = [];
                rows.push([companyName]);
                rows.push(['Sales Return (' + totalReturns + ')']);
                if (dateValue) {
                    rows.push(['Date: ' + formatDMY(dateValue)]);
                }
                rows.push([]);
                rows.push(headerLabels);

                $table.find('tbody tr').each(function() {
                    var $cells = $(this).find('td');
                    if ($cells.length === 0) return;
                    var rowData = [];
                    visibleColIndexes.forEach(function(i) {
                        var cellText = $cells.eq(i).text().trim().replace(/\s+/g, ' ');
                        rowData.push(cellText);
                    });
                    rows.push(rowData);
                });

                if (rows.length <= 5) {
                    alert('No data available for export');
                    return;
                }

                var N = headerLabels.length || 1;
            var workbook  = new ExcelJS.Workbook();
            var worksheet = workbook.addWorksheet('SalesReturn');
            var wsCols = [];
            for (var ci = 0; ci < N; ci++) { wsCols.push({ width: 22 }); }
            worksheet.columns = wsCols;

            var hdrIdx = rows.indexOf(headerLabels);
            if (hdrIdx < 0) hdrIdx = rows.length - 1;

            // Meta rows (company name, page title, optional date rows)
            var wsRowNum = 0;
            for (var ri = 0; ri < hdrIdx; ri++) {
                if (!(rows[ri] && rows[ri][0])) continue; // skip blank separators
                wsRowNum++;
                var wsRow = worksheet.addRow([]);
                wsRow.height = ri === 0 ? 26 : ri === 1 ? 20 : 16;
                if (N > 1) worksheet.mergeCells(wsRowNum, 1, wsRowNum, N);
                wsRow.getCell(1).value = rows[ri][0] || '';
                if (ri === 0) wsRow.getCell(1).font = { bold: true, size: 14 };
                else if (ri === 1) wsRow.getCell(1).font = { bold: true, size: 12 };
                wsRow.getCell(1).alignment = { horizontal: 'center', vertical: 'middle' };
            }

            // Blank separator
            wsRowNum++;
            worksheet.addRow([]);

            // Column header row
            wsRowNum++;
            var wsHdrRow = worksheet.addRow(headerLabels);
            wsHdrRow.height = 20;
            wsHdrRow.eachCell({ includeEmpty: true }, function (cell) {
                cell.font      = { bold: true, color: { argb: 'FFFFFFFF' }, size: 11 };
                cell.fill      = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF2D5496' } };
                cell.alignment = { horizontal: 'center', vertical: 'middle' };
                cell.border    = {
                    top:    { style: 'thin', color: { argb: 'FFB8C4D8' } },
                    left:   { style: 'thin', color: { argb: 'FFB8C4D8' } },
                    bottom: { style: 'thin', color: { argb: 'FFB8C4D8' } },
                    right:  { style: 'thin', color: { argb: 'FFB8C4D8' } }
                };
            });

            // Data rows
            for (var di = hdrIdx + 1; di < rows.length; di++) {
                var wsDataRow = worksheet.addRow(rows[di]);
                wsDataRow.eachCell({ includeEmpty: true }, function (cell) {
                    cell.border = {
                        top:    { style: 'thin', color: { argb: 'FFCCCCCC' } },
                        left:   { style: 'thin', color: { argb: 'FFCCCCCC' } },
                        bottom: { style: 'thin', color: { argb: 'FFCCCCCC' } },
                        right:  { style: 'thin', color: { argb: 'FFCCCCCC' } }
                    };
                });
            }

            workbook.xlsx.writeBuffer().then(function (buffer) {
                var blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                function pad(n) { return n < 10 ? '0' + n : n; }
                var d = new Date();
                var filename = 'sales_return_' + pad(d.getDate()) + '-' + pad(d.getMonth() + 1) + '-' + d.getFullYear() + '.xlsx';
                saveAs(blob, filename);
            });
            });

        });
    </script>


<script>
        $(document).ready(function() {
            function initAccountSelect2(selector) {
                $(selector).select2({
                    ajax: {
                        url: '<?php echo e(route('autocomplete.get_cust_account_list_ajax')); ?>',
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                search_text: params.term
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.map(function(item) {
                                    return {
                                        id: item.id,
                                        text: item.account_code + ' - ' + item.account_name
                                    };
                                })
                            };
                        },
                        cache: true
                    },
                    placeholder: 'Select Account',
                    minimumInputLength: 2
                });
            }

            // Initial init
            initAccountSelect2('.js-account-select');

            // Re-initialize on focus (if needed for dynamically added fields)
            $(document).on('focus', '.js-account-select', function() {
                if (!$(this).hasClass("select2-hidden-accessible")) {
                    initAccountSelect2(this);
                }
            });

            // Open dropdown and focus search box on click
            $(document).on('click', '.js-account-select', function() {
                $(this).select2('open');
            });

            // Focus the search input inside the opened Select2 dropdown
            $(document).on('select2:open', function() {
                setTimeout(function() {
                    const searchInput = document.querySelector(
                        '.select2-container--open .select2-search__field');
                    if (searchInput) {
                        searchInput.focus();
                    }
                }, 0);
            });
        });

        $(document).ready(function() {
            $(".list_style_search_btn").on("click", function() {
                $("#search_box").slideToggle(200); // expands/collapses smoothly
            });
        });
    </script>

    <?php } catch (\Exception $e) { ?> <?php echo e($e); ?>

    <?php  } ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backEnd.newmasterpage', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
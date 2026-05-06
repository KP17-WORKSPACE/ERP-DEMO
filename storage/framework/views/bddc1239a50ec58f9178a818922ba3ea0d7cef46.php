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

                localStorage.setItem('listViewQuote', 'long');
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

                localStorage.setItem('listViewQuote', 'short');

            }


        }


        //added ny kp
        function toggleLongFilters() {

            document.querySelectorAll('#filters-long .filter-field').forEach(el => {
                el.classList.toggle('d-none');
            });
        }




        // Initialize view from localStorage
        document.addEventListener('DOMContentLoaded', () => {
            const savedView = localStorage.getItem('listViewQuote');
            if (savedView === 'long') {
                isFullList = false; // so that toggling once activates full view
                list_style_new();
            } else {
                // Default to short view
                isFullList = true; // so that toggling once activates short view
                list_style_new();
            }

            // Attach event to sidebar links to force short view on navigation
            document.querySelectorAll('.sub-nav-item').forEach(link => {
                link.addEventListener('click', () => {
                    localStorage.setItem('listViewQuote', 'short');
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
            <h4 class="mb-2">Quotations
            </h4>

            

            <div class="search-filter-container mb-4">
                <div class="input-group flex-nowrap">
                    <input type="text" name="documents_number" id="search_document_number" class="form-control" placeholder="Document No"
                        aria-label="Search" aria-describedby="addon-wrapping" value="">
                </div>


              
                
                <button type="button" class="btn btn-light" id="list_style_button" onclick="list_style_new()">
                    <i class="ico icon-outline-list-down"></i>
                </button>
            </div>



        </div>

        <div class="long-list  d-none" id="filters-long">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="mb-2">Quotations List
                </h4>
                <div class="search-filter-container mb-0">

                      <input type="text" id="tableSearch" class="form-control"
                        style="font-size:13px; width: 350px; 
                        top: 12px;
                        margin-right: 100px;
                        right: 120px;"
                        placeholder="Search"> 


                    <button type="button" class="btn btn-light list_style_search_btn mt-1" id="exportExcelQuotations" style="margin-right:66px;">
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


        </div>

        <div class="left-nav-list">

            <ul id="short-list" class="nav flex-column nav-pills" id="companyTabNavs" role="tablist">
                <?php if(count($quotations) > 0): ?>
                    <?php $__currentLoopData = $quotations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="nav-item w-100" role="presentation">
                            <button class="nav-link quote-item <?php echo e($active_id == $item->id ? 'active' : ''); ?>"
                                data-id="<?php echo e($item->id); ?>" data-qid="<?php echo e($item->quote_id); ?>" id="purchase-order-1-tab" data-bs-toggle="tab"
                                data-bs-target="#purchase-order-1" type="button" role="tab"
                                aria-controls="purchase-order-1" aria-selected="true">
                                <div class="row w-100">
                                    <div class="col-12">
                                        <label class="form-control-plaintext truncate-text">
                                            <?php echo e(@$item->customername->name); ?></label>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-control-plaintext" style="font-size: 11px"><?php echo e(@$item->code); ?></div>
                                    </div>
                                    <div class="col-4 text-center">
                                        <div class="form-control-plaintext" style="font-size: 11px">
                                            <?php echo e(date('d/m/Y', strtotime(@$item->date))); ?></div>
                                    </div>
                                    <div class="col-4 text-end">

                                        <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                                            <?php echo e(@App\SysHelper::currancy_format_deal($item->deal_value, $item->company_id)); ?>

                                            <?php echo e(@$item->dealcurrency->code); ?>

                                        </div>
                                    </div>
                                    
                                </div>
                            </button>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    No Records
                <?php endif; ?>
            </ul>

            <div class="table-responsive mb-4 mt-4">
                <table id="long-list" class="table table-hover data-table d-none" style="table-layout: fixed;width:100%">

                    <thead>
                        <tr>
                            <th style="width: 90px;" class="text-center"><?php echo app('translator')->getFromJson('Doc Date'); ?></th>
                            <th style="width: 90px;" class="text-center"><?php echo app('translator')->getFromJson('QTN No'); ?></th>
                            <th style="width: 90px;" class="text-center"><?php echo app('translator')->getFromJson('Deal Number'); ?></th>
                            <th style="width: 250px;"><?php echo app('translator')->getFromJson('Customer Name'); ?></th>
                            <th style="width: 150px;"><?php echo app('translator')->getFromJson('Salesman Name'); ?></th>
                            <th style="width: 90px;" class="text-end"><?php echo app('translator')->getFromJson('Amount'); ?></th>
                            <th class="text-center" style="width: 90px;"><?php echo app('translator')->getFromJson('Action'); ?></th>
                        </tr>
                    </thead>


                    <tbody>

                        <?php
                            $count = 1;
                            $total_deal = 0;
                            $total_amount = 0;
                        ?>
                        <?php $__currentLoopData = $quotations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $total_deal += 1; ?>
                            <tr>
                                <td class="text-center"><?php echo e(date('d/m/Y', strtotime(@$value->date))); ?></td>
                                <td class="text-center"><a class=""
                                        href="<?php echo e(url('quotations/' . $value->id )); ?>"><?php echo e(@$value->code); ?></a>
                                </td>

                                <td class="text-center"><a target="_blank" class=""
                                        href="<?php echo e(url('crm-deal-track-approval/' . @$value->track->id)); ?>"><?php echo e(@$value->code); ?></a>
                                </td>
                                <td>

                                    <?php echo e(@$value->customername->name); ?>

                                </td>

                                <td><?php echo e(@$value->ownername->full_name); ?></td>
                                <td class="text-end ">
                                    <?php echo e(@App\SysHelper::currancy_format_deal($value->deal_value, $value->company_id)); ?>


                                    <?php $total_amount += $value->deal_value; ?>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center align-items-center gap-1">
                                        <a class="btn btn-sm btn-light"
                                            href="<?php echo e(url('crm-quote/' . $value->id . '/download/' . $value->quote_id)); ?>"
                                            class="btn-small"><i class="ico icon-bold-download-minimalistic text-dark"
                                                style="font-size: 16px;"></i></a>
                                        
                                    </div>

                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    </tbody>
                    <?php try{ ?>
                    <tfoot>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th class="text-end pr-1">
                                <?php echo e(@App\SysHelper::currancy_format_deal($total_amount, $value->company_id)); ?></th>
                            <th></th>
                        </tr>
                    </tfoot>
                    <?php }catch (\Exception $e) { } ?>

                </table>
            </div>
        </div>
    </aside>

    <div class="content-container col-9">
        <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">

            <script>
                $(document).ready(function() {
                    $(document).on('click', '.quote-item', function() {

                    
                        var id = $(this).data('id');
                        var qid = $(this).data('qid');
                        console.log(id)
                        $('.quote-item').removeClass('active');
                        $('.quote-item[data-id="' + id + '"]').addClass('active');

                        // Update the browser URL to include selected ID (without reloading)
                        var newUrl = "<?php echo e(url('quotations')); ?>/" + id;
                        window.history.pushState({
                            path: newUrl
                        }, '', newUrl);

                        var action = "<?php echo e(URL::to('quotation-details')); ?>/" + id + "/" + qid;
                        $('#loading_bg').show();

                        $.ajax({
                            url: action,
                            method: 'GET',
                            success: function(response) {
                                $('#quote-details').html(response);
                            },
                            error: function() {
                                $('#quote-details').html(
                                    '<p class="text-danger">No Details Available.</p>');
                            },
                            complete: function() {
                                $('#loading_bg').hide(); // Always hide loader after request completes
                            }
                        });
                    });
                });
            </script>




            <div class="" role="tabpanel" aria-labelledby="po-tab" id="quote-details">


             <?php if($action === 'add'): ?>
                    <?php echo $__env->make('backEnd.quotations.quote_add', $addData, \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <?php elseif($action === 'edit'): ?>
                    <?php echo $__env->make('backEnd.quotations.quote_edit', $editData, \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <?php elseif(!empty($selectedQuote) && is_array($selectedQuote)): ?>
                    <?php echo $__env->make('backEnd.quotations.quotation_pdf', $selectedQuote, \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <?php else: ?>
                    

                    <div class="container-fluid d-flex flex-column justify-content-center align-items-center"
                        style="min-height: 90vh;">

                        <!-- Icon + Heading -->
                                 <a href="<?php echo e(url('quotations?qn_action=add')); ?>" class="text-decoration-none text-dark">
                        <div class="text-center mb-4">
                            <div data-bs-toggle="modal" data-bs-target="#addquote"
                                class="rounded-circle bg-success text-white d-flex justify-content-center align-items-center mx-auto"
                                style="width: 80px; height: 80px; font-size: 36px;cursor:pointer">
                                <i class="ico icon-outline-add-square"></i>
                            </div>
                            <h1 class="fw-bold mt-3" style="cursor:pointer" data-bs-toggle="modal"
                                data-bs-target="#addquote">Add New Quote</h1>
                            <p class="text-muted">Create new quotes with ease</p>
                        </div>
                    </a>

                    </div>

                <?php endif; ?>
            </div>


        </div>
    </div>


      <script>
        $(document).ready(function() {
            $('#exportExcelQuotations').on('click', function(e) {
                e.preventDefault();

                var companyName = <?php echo json_encode(@App\SysCompany::find(session('logged_session_data.company_id') ?? '')->trade_name ?? '', 15, 512) ?>;
                var totalQuotes = <?php echo json_encode($quotations->count() ?? 0, 15, 512) ?>;
                var dateFrom = <?php echo json_encode($ctrl_date ?? '', 15, 512) ?>;
                var dateTo = <?php echo json_encode($ctrl_date2 ?? '', 15, 512) ?>;

                var $table = $('#long-list');

                var visibleColIndexes = [];
                var headerLabels = [];
                var lastIndex = $table.find('thead tr th').length - 1;

                $table.find('thead tr th').each(function(i) {
                    if (i === lastIndex) return;
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
                    if (!value) return '-';
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
                rows.push(['Quotations (' + totalQuotes + ')']);

                if (dateFrom || dateTo) {
                    rows.push(['From: ' + formatDMY(dateFrom) + '  To: ' + formatDMY(dateTo)]);
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
            var worksheet = workbook.addWorksheet('Quotations');
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
                var filename = 'quotations_' + pad(d.getDate()) + '-' + pad(d.getMonth() + 1) + '-' + d.getFullYear() + '.xlsx';
                saveAs(blob, filename);
            });
            });

            $('#search_document_number').on('input', function() {
                var query = $(this).val();

                $.ajax({
                    url: "<?php echo e(route('crm-quote.search')); ?>",
                    type: "GET",
                    data: {
                        query: query
                    },
                    success: function(data) {
                        $('#short-list').html('');



                        if (data.length > 0) {
                            $.each(data, function(index, amc_list) {

                                console.log(amc_list);

                                let ims = ` <li class="nav-item w-100" role="presentation">
                            <button class="nav-link quote-item"
                                data-id="${amc_list.id}" data-qid="${amc_list.quote_id}" id="purchase-order-1-tab" data-bs-toggle="tab"
                                data-bs-target="#purchase-order-1" type="button" role="tab"
                                aria-controls="purchase-order-1" aria-selected="true">
                                <div class="row w-100">
                                     <div class="col-12">
                                        <label class="form-control-plaintext truncate-text">
                                            ${amc_list.customername.name}</label>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-control-plaintext" style="font-size: 11px">${amc_list.code}</div>
                                    </div>
                                    <div class="col-4 text-center">
                                        <div class="form-control-plaintext" style="font-size: 11px">
                                            ${get_format_date(amc_list.date)}</div>
                                    </div>
                                    <div class="col-4 text-end">

                                        <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                                            ${amc_list.formatted_deal_value}
                                            ${amc_list.dealcurrency.code}
                                        </div>
                                    </div>
                                   
                                </div>
                            </button>
                        </li>`;

                        //         let ims = `<li class="nav-item w-100" role="presentation">
                        //     <button href="javascript:void(0)"
                        //         class="nav-link data-item"
                        //         data-id="${amc_list.id}">

                        //         <div class="row w-100">
                        //             <div class="col-4">
                        //                 <div class="form-control-plaintext">${amc_list.doc_number}</div>
                        //             </div>
                        //             <div class="col-4 pl-2">
                        //                 <div class="form-control-plaintext truncate-text">
                        //                      ${get_format_date(amc_list.doc_date)}
                        //                 </div>
                        //             </div>
                        //             <div class="col-4 text-end">
                        //                 <div class="form-control-plaintext truncate-text">
                        //                      ${amc_list.formatted_amount}
                        //                     ${amc_list.currency_name.code}</div>
                        //             </div>
                        //             <div class="col-12">
                        //                 <label
                        //                     class="form-control-plaintext truncate-text">${amc_list.accountname.account_code}
                        //                     - ${amc_list.accountname.account_name}</label>
                        //             </div>
                        //         </div>
                              
                        //     </button>
                        // </li>`;








                                $('#short-list').append(ims);
                            });
                        } else {
                            $('#short-list').html('<div class="p-2">No results found</div>');
                        }
                    }
                });
            });

        });
    </script>



<div class="modal side-panel  fade" id="paymenttermsModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm draggable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add </h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-3">
                       

                          
                            <label class="form-label">Payment Terms <span class="text-danger">*</span></label>
                                <input type="text" id="payment_term_title" name="name" class="form-control" required="" autocomplete="off">
            
                        <div class="modal-footer d-flex justify-content-center p-0">
                            <button type="button" id="savePaymentTerm" class="btn btn-light add-btn ms-2">
                                <i class="ico icon-outline-bookmark-opened text-success"></i> Submit
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>



<script>
$(document).on('click', '#savePaymentTerm', function () {

    let title = $('#payment_term_title').val().trim();
    let input = $('#payment_term_title');

    input.removeClass('is-invalid');
    input.next('.invalid-feedback').text('');

    if (!title) {
        input.addClass('is-invalid');
        input.next('.invalid-feedback').text('Payment term is required');
        return;
    }

    $.ajax({
        url: "<?php echo e(url('payment-terms-store-ajax')); ?>", // adjust route
        type: "POST",
        data: {
            title: title,
            _token: "<?php echo e(csrf_token()); ?>"
        },
        beforeSend: function () {
            $('#loading_bg').show();

        },
        success: function (res) {

            if (res.status) {

                // ✅ NEW ID AVAILABLE HERE
                console.log('New ID:', res.data.id);

                // Example: append to dropdown
                $('#payment_terms').append(
                    `<option value="${res.data.id}" selected>${res.data.title}</option>`
                );

                $('#paymenttermsModal').modal('hide');
                $('#payment_term_title').val('');

                toastr.success(res.message, 'Success');
            }
        },
        error: function (xhr) {

            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                if (errors.title) {
                    input.addClass('is-invalid');
                    input.next('.invalid-feedback').text(errors.title[0]);
                }
            } else {
                toastr.error('Something went wrong', 'Error');
            }
        },
        complete: function () {
            $('#loading_bg').hide();
        }
    });
});
</script>





    <?php } catch (\Exception $e) { ?> <?php echo e($e); ?> <?php  } ?>


<?php $__env->stopSection(); ?>

<?php echo $__env->make('backEnd.newmasterpage', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
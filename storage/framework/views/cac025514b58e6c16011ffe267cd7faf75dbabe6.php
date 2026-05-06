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

                sessionStorage.setItem('listViewDNList', 'long');
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

                sessionStorage.setItem('listViewDNList', 'short');

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
            const hasCustomerAction = urlParams.has('di_action');

            // If in add/edit mode, force short view
            if (hasCustomerAction) {
                sessionStorage.setItem('listViewDNList', 'short');
                isFullList = true; // Set to true so toggle switches to short
                list_style_new(); // Switch to short view
            } else {
                // Normal behavior - use saved view from sessionStorage
                const savedView = sessionStorage.getItem('listViewDNList');
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
                    sessionStorage.setItem('listViewDNList', 'short');
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
            <h4 class="mb-2">Delivery Note
            </h4>


            <div class="search-filter-container mb-4">
                <div class="input-group flex-nowrap">
                    <input type="text" name="documents_number" id="search_invoice" class="form-control"
                        placeholder="Document No" aria-label="Search" aria-describedby="addon-wrapping" value="">
                </div>


                <button type="button" class="btn btn-light" id="list_style_button" onclick="list_style_new()">
                    <i class="ico icon-outline-list-down"></i>
                </button>
            </div>



        </div>

        <div class="long-list  d-none" id="filters-long">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="mb-2">Delivery Note List
                </h4>
                <div class="search-filter-container mb-0">

                    <input type="text" id="tableSearch" class="form-control d-inline-block"
                        style="font-size:13px;width: 350px;" placeholder="Search">

                    <button class="btn btn-light" id="exportExcelDeliveryNote">
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

                        <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'delivery-note', 'method' => 'get', 'id' => 'delivery-note-search'])); ?>

                        <div class="row">

                            <div class="col-1-5 mb-2">
                                <label for="" class="form-label">Documents Number</label>
                                <input class="form-control" type="text" autocomplete="off" name="documents_number"
                                    value="<?php echo e(@$ctrl_doc_number); ?>">
                            </div>
                            <div class="col-1-5 mb-2">
                                <label for="" class="form-label">Customer</label>
                                <select class="form-control js-account-select" name="customer" id="customer">
                                    <option value=""></option>

                                </select>
                            </div>
                            <div class="col-1-5 mb-2">
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
                                <label for="" class="form-label">Sales Invoice Number</label>
                                <input class="form-control" type="text" autocomplete="off" name="sales_invoice_number"
                                    value="<?php echo e(@$ctrl_sales_invoice_number); ?>">
                            </div>
                            <div class="col-1-5 mb-2">
                                <label for="" class="form-label">SRT Number</label>
                                <input class="form-control" type="text" autocomplete="off" name="srt"
                                    value="<?php echo e(@$ctrl_srt); ?>">
                            </div>
                            <div class="col-1-5 mb-2">
                                <label for="" class="form-label">Date</label>
                                <input class="form-control date-picker" type="text" autocomplete="off" name="date"
                                    value="<?php echo e(@App\SysHelper::normalizeToDmy($ctrl_date)); ?>">
                            </div>

                            <div class="col-1-5">

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

            <ul id="short-list" class="nav flex-column nav-pills" id="companyTabNavs" role="tablist">
                <?php if(count($deliverynote) > 0): ?>
                    <?php $__currentLoopData = $deliverynote; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="nav-item w-100" role="presentation">
                            <button href="javascript:void(0)"
                                class="nav-link data-item <?php echo e($active_id == $value->id ? 'active' : ''); ?>"
                                data-id="<?php echo e($value->id); ?>">

                                <div class="row w-100">
                                     <div class="col-12">
                                        <label
                                            class="form-control-plaintext truncate-text"><?php echo e($value->accountname->account_code); ?>

                                            - <?php echo e($value->accountname->account_name); ?></label>
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
                            <th class="text-center" style="width:80px"><?php echo app('translator')->getFromJson('DN Date'); ?></th>
                            <th class="text-center" style="width:80px"><?php echo app('translator')->getFromJson('DN No'); ?></th>
                            <th style="width:150px"><?php echo app('translator')->getFromJson('Customer'); ?></th>
                            <th style="width:150px"><?php echo app('translator')->getFromJson('Supplier'); ?></th>
                            <th style="width:80px" class="text-center"><?php echo app('translator')->getFromJson('SIV No'); ?></th>
                            <th style="width:80px" class="text-center"><?php echo app('translator')->getFromJson('SRT No'); ?></th>
                            <th style="width:80px" class="text-center"><?php echo app('translator')->getFromJson('Deal ID'); ?></th>
                            <th style="width:80px" class="text-center"><?php echo app('translator')->getFromJson('Currency'); ?></th>
                            <th style="width:100px" class="text-end"><?php echo app('translator')->getFromJson('Amount'); ?></th>
                            <th style="width:30px" class="text-center"><i class="ico icon-bold-paperclip"></i></th>
                            <th style="width:100px" class="text-center">Action</th>
                        </tr>
                    </thead>


                    <tbody>
                        <?php $count =1; ?>
                        <?php $__currentLoopData = $deliverynote; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($pending_si == 1): ?>
                                <?php if(empty($value->invoice_no)): ?>
                                    <tr <?php if(@$value->status == 2): ?> class="bg-dark" <?php endif; ?>>
                                        <td class="text-center"><?php echo e(date('d/m/Y', strtotime(@$value->doc_date))); ?></td>
                                        <td class="text-center"><a
                                                href="<?php echo e(url('delivery-note/' . $value->id . '/view')); ?>"
                                                target="_blank"><?php echo e(@$value->doc_number); ?></a></td>
                                        <td><?php echo e(@$value->accountname->account_name); ?></td>
                                        <td><?php echo e(@$value->supplier_name); ?></td>

                                        <!-- Sales Invoice Numbers -->
                                        <td class="text-center">
                                            <span class="text-dark">Pending</span>
                                        </td>

                                        <!-- Sales Return Numbers -->
                                        <td class="text-center">
                                            <?php if(empty($value->srtno)): ?>
                                                <span class="text-dark">Pending</span>
                                            <?php else: ?>
                                                <?php $__currentLoopData = explode(',', $value->srtno); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $srt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <a href="<?php echo e(url('get-url-sales-return/' . trim($srt))); ?>"
                                                        target="_blank"><?php echo e(trim($srt)); ?></a>
                                                    <?php if(!$loop->last): ?>
                                                        ,
                                                    <?php endif; ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Deal Codes -->
                                        <td class="text-center">
                                            <?php if(empty($value->code)): ?>
                                                <span class="text-dark">Pending</span>
                                            <?php else: ?>
                                                <?php $__currentLoopData = explode(',', $value->code); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <a href="<?php echo e(url('get-url-deal-track/' . trim($code))); ?>"
                                                        target="_blank"><?php echo e(trim($code)); ?></a>
                                                    <?php if(!$loop->last): ?>
                                                        ,
                                                    <?php endif; ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center"><?php echo e(@$value->currency_name->code); ?></td>
                                        <td class="text-end">
                                            <?php echo e(@App\SysHelper::com_curr_format(@$value->amount, 2, '.', ',')); ?></td>
                                        <td>
                                            <?php if(empty(@$value->attach)): ?>
                                            <?php else: ?>
                                                <?php $__currentLoopData = explode(',', @$value->attach); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <a href="<?php echo e(url(trim($att))); ?>" target="_blank"><i
                                                            class="fa fa-paperclip" aria-hidden="true"></i></a>&nbsp;
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if(in_array(session('logged_session_data.company_id') ?? null, [2, 3, 5, 6])): ?>
                                                <a class="btn-sm btn-light"
                                                    href="<?php echo e(url('delivery-note/' . $value->id . '/download/t')); ?>"
                                                    class="btn-small"><i class="fa fa-download"
                                                        aria-hidden="true"></i></a>
                                            <?php else: ?>
                                                <a class="btn-sm btn-light"
                                                    href="<?php echo e(url('delivery-note/' . $value->id . '/download')); ?>"
                                                    class="btn-small"><i class="fa fa-download"
                                                        aria-hidden="true"></i></a>
                                            <?php endif; ?>
                                            <a class="btn-sm btn-light"
                                                href="<?php echo e(url('delivery-note/' . $value->id . '/edit')); ?>"><i
                                                    class="fa fa-edit" aria-hidden="true"></i></a>
                                            <?php if(@$value->status == 2): ?>
                                                <a class="btn-sm btn-light"
                                                    href="<?php echo e(url('delivery-note/' . $value->id . '/restore')); ?>"
                                                    onclick="return confirm('Are you sure you want to restore this item?');"><i
                                                        class="fa fa-undo" aria-hidden="true"></i></a>
                                            <?php else: ?>
                                                <a class="btn-sm btn-light"
                                                    href="<?php echo e(url('delivery-note/' . $value->id . '/delete')); ?>"
                                                    onclick="return confirm('Are you sure you want to delete this item?');"><i
                                                        class="fa fa-trash" aria-hidden="true"></i></a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php else: ?>
                                <tr <?php if(@$value->status == 2): ?> class="bg-dark" <?php endif; ?>>
                                    <td class="text-center"><?php echo e(date('d/m/Y', strtotime(@$value->doc_date))); ?></td>
                                    <td class="text-center"><a href="<?php echo e(url('delivery-note/' . $value->id . '/view')); ?>"
                                            target="_blank"><?php echo e(@$value->doc_number); ?></a></td>
                                    <td><?php echo e(@$value->accountname->account_name); ?></td>
                                    <td><?php echo e(@$value->supplier_name); ?></td>

                                    <!-- Sales Invoice Numbers -->
                                    <td class="text-center">
                                        <?php if(empty($value->invoice_no)): ?>
                                            <span class="text-dark">Pending</span>
                                        <?php else: ?>
                                            <?php $__currentLoopData = explode(',', $value->invoice_no); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <a href="<?php echo e(url('get-url-sales-invoice/' . trim($inv))); ?>"
                                                    target="_blank"><?php echo e(trim($inv)); ?></a>
                                                <?php if(!$loop->last): ?>
                                                    ,
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Sales Return Numbers -->
                                    <td class="text-center">
                                        <?php if(empty($value->srtno)): ?>
                                            <span class="text-dark">Pending</span>
                                        <?php else: ?>
                                            <?php $__currentLoopData = explode(',', $value->srtno); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $srt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <a href="<?php echo e(url('get-url-sales-return/' . trim($srt))); ?>"
                                                    target="_blank"><?php echo e(trim($srt)); ?></a>
                                                <?php if(!$loop->last): ?>
                                                    ,
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Deal Codes -->
                                    <td class="text-center">
                                        <?php if(empty($value->code)): ?>
                                            <span class="text-dark">Pending</span>
                                        <?php else: ?>
                                            <?php $__currentLoopData = explode(',', $value->code); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <a href="<?php echo e(url('get-url-deal-track/' . trim($code))); ?>"
                                                    target="_blank"><?php echo e(trim($code)); ?></a>
                                                <?php if(!$loop->last): ?>
                                                    ,
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center"><?php echo e(@$value->currency_name->code); ?></td>
                                    <td class="text-end">
                                        <?php echo e(@App\SysHelper::com_curr_format(@$value->amount, 2, '.', ',')); ?>

                                    </td>
                                    <td>
                                        <?php if(empty(@$value->attach)): ?>
                                        <?php else: ?>
                                            <?php $__currentLoopData = explode(',', @$value->attach); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <a href="<?php echo e(url(trim($att))); ?>" target="_blank"><i
                                                        class="fa fa-paperclip" aria-hidden="true"></i></a>&nbsp;
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">


                                        <div class="d-flex justify-content-center align-items-center gap-1">

                                            <a href="<?php echo e(url('delivery-note/' . $value->id . '?di_action=edit')); ?>"
                                                class="btn btn-sm btn-light " title="Comments">
                                                <i class="ico icon-outline-pen-2" style="font-size: 16px;"></i>
                                            </a>

                                            <?php if(in_array(session('logged_session_data.company_id') ?? null, [2, 3, 5, 6])): ?>
                                                <a class="btn btn-light d-block"
                                                    href="<?php echo e(url('delivery-note/' . $value->id . '/download/t')); ?>"><i
                                                        class="ico icon-bold-download-minimalistic text-dark"
                                                        style="font-size: 16px;"></i></a>
                                            <?php else: ?>
                                                <a class="btn btn-light d-block"
                                                    href="<?php echo e(url('delivery-note/' . $value->id . '/download')); ?>"><i
                                                        class="ico icon-bold-download-minimalistic text-dark"
                                                        style="font-size: 16px;"></i></a>
                                            <?php endif; ?>

                                            <?php if(@$value->status == 2): ?>
                                                <a class="btn btn-light btn-sm"
                                                    href="<?php echo e(url('delivery-note/' . $value->id . '/restore')); ?>"
                                                    onclick="return confirm('Are you sure you want to restore this item?');">
                                                    <i class="ico icon-bold-restart" style="font-size: 16px;"></i>
                                                </a>
                                            <?php else: ?>
                                                <a class="btn btn-light btn-sm"
                                                    href="<?php echo e(url('delivery-note/' . $value->id . '/delete')); ?>"
                                                    onclick="return confirm('Are you sure you want to delete this item?');">
                                                    <i class="ico icon-outline-trash-bin-minimalistic text-dark"
                                                        style="font-size: 16px;"></i>
                                                </a>
                                            <?php endif; ?>

                                        </div>



                                    </td>
                                </tr>
                            <?php endif; ?>
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
                    $(document).on('click', '.data-item', function() {
                        var id = $(this).data('id');

                        $('.data-item').removeClass('active');
                        $('.data-item[data-id="' + id + '"]').addClass('active');

                        // Update the browser URL to include selected ID (without reloading)
                        var newUrl = "<?php echo e(url('delivery-note')); ?>/" + id;
                        window.history.pushState({
                            path: newUrl
                        }, '', newUrl);

                        var action = "<?php echo e(URL::to('delivery-note-details')); ?>/" + id;



                        $('#loading_bg').show();

                        $.ajax({
                            url: action,
                            method: 'GET',
                            success: function(response) {
                                $('#data-details').html(response);
                            },
                            error: function() {
                                $('#data-details').html(
                                    '<p class="text-danger">No Details Available.</p>');
                            },
                            complete: function() {
                                $('#loading_bg').hide(); // Always hide loader after request completes
                            }
                        });
                    });
                });
            </script>


            <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
                <?php if($action === 'add'): ?>
                    <?php echo $__env->make('backEnd.deliverynote.dn_add', $addData, \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <?php elseif($action === 'edit'): ?>
                    <?php echo $__env->make('backEnd.deliverynote.dn_edit', $editData, \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <?php elseif(!empty($data) && is_array($data)): ?>
                    <?php echo $__env->make('backEnd.deliverynote.dn_details', $data, \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <?php else: ?>
                    <div onclick="window.location.href='<?php echo e(url('delivery-note-add')); ?>'"
                        class="container-fluid d-flex flex-column justify-content-center align-items-center"
                        style="min-height: 90vh;">

                        <!-- Icon + Heading -->
                        <div class="text-center mb-4">
                            <div class="rounded-circle bg-success text-white d-flex justify-content-center align-items-center mx-auto"
                                style="width: 80px; height: 80px; font-size: 36px;cursor:pointer">
                                <i class="ico icon-outline-add-square"></i>
                            </div>
                            <h1 class="fw-bold mt-3" style="cursor:pointer">Delivery Note</h1>
                            
                        </div>

                    </div>
                <?php endif; ?>
            </div>


        </div>
    </div>



    <script>
    const SHOW_CUSTOMER_CODE = <?php echo e(@App\SysHelper::getCompanyCodeSettings()['is_customer_code'] ? 'true' : 'false'); ?>;

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
                                   let text = "";

                                if (SHOW_CUSTOMER_CODE) {
                                    text = item.account_name + " (" + item.account_code +
                                        ")";
                                } else {
                                    text = item.account_name; // no code
                                }

                                return {
                                    id: item.id,
                                    text: text
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

            $('#search_invoice').on('keyup', function() {
                var query = $(this).val();

                $.ajax({
                    url: "<?php echo e(route('delivery-note.search')); ?>",
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

            $('#exportExcelDeliveryNote').on('click', function(e) {
                e.preventDefault();

                var companyName = <?php echo json_encode(@App\SysCompany::find(session('logged_session_data.company_id') ?? '')->trade_name ?? '', 15, 512) ?>;
                var totalNotes = <?php echo json_encode($deliverynote->count() ?? 0, 15, 512) ?>;
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
                rows.push(['Delivery Note (' + totalNotes + ')']);
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
            var worksheet = workbook.addWorksheet('DeliveryNote');
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
                var filename = 'delivery_note_' + pad(d.getDate()) + '-' + pad(d.getMonth() + 1) + '-' + d.getFullYear() + '.xlsx';
                saveAs(blob, filename);
            });
            });

        });
    </script>


    <?php } catch (\Exception $e) { ?> <?php echo e($e); ?> <?php  } ?>


<?php $__env->stopSection(); ?>

<?php echo $__env->make('backEnd.newmasterpage', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
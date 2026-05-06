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

                sessionStorage.setItem('listViewPIList', 'long');
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

                sessionStorage.setItem('listViewPIList', 'short');

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
            const hasCustomerAction = urlParams.has('pi_action');

            // If in add/edit mode, force short view
            if (hasCustomerAction) {
                sessionStorage.setItem('listViewPIList', 'short');
                isFullList = true; // Set to true so toggle switches to short
                list_style_new(); // Switch to short view
            } else {
                // Normal behavior - use saved view from sessionStorage
                const savedView = sessionStorage.getItem('listViewPIList');
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
                    sessionStorage.setItem('listViewPIList', 'short');
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
            <h4 class="mb-2">Purchase Invoice  
            </h4>


            <div class="search-filter-container mb-4">
                <div class="input-group flex-nowrap">
                    <input type="text" name="documents_number" id="search_pi" class="form-control"
                        placeholder="Document No" aria-label="Search" aria-describedby="addon-wrapping" value="">
                </div>


                <button type="button" class="btn btn-light" id="list_style_button" onclick="list_style_new()">
                    <i class="ico icon-outline-list-down"></i>
                </button>
            </div>



        </div>

        <div class="long-list  d-none" id="filters-long">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="mb-2">Purchase Invoice List
                </h4>
                <div class="search-filter-container mb-0">

                    <input type="text" id="tableSearch" class="form-control d-inline-block"
                        style="font-size:13px;width: 350px;margin-right: 100px;" placeholder="Search">

                    <button type="button" class="btn btn-light list_style_search_btn mt-1" id="exportExcelPurchaseInvoices" style="margin-right: 66px;">
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
                        <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'purchase-invoice', 'method' => 'get', 'id' => 'purchase-invoice-search'])); ?>

                        <div class="row">

                            <div class="col-1-5 mb-2">
                                <label for="" class="form-label">Document No</label>
                                <input class="form-control" type="text" autocomplete="off" name="documents_number"
                                    value="<?php echo e(@$ctrl_doc_no); ?>">
                            </div>
                            <div class="col-3 mb-2">
                                <label for="" class="form-label">Supplier</label>
                                <select class="form-control js-account-select" name="supplier" id="supplier">
                                    <option value=""></option>

                                </select>
                            </div>
                            <div class="col-3 mb-2">
                                <label for="" class="form-label">Customer</label>
                                <input class="form-control" type="text" autocomplete="off" name="customer"
                                    value="<?php echo e(@$ctrl_customer); ?>">
                            </div>
                            <div class="col-1-5 mb-2">
                                <label for="" class="form-label">Purchase Order Number</label>
                                <input class="form-control" type="text" autocomplete="off" name="purchase_order_number"
                                    value="<?php echo e(@$ctrl_po_no); ?>">
                            </div>
                            <div class="col-1-5 mb-2">
                                <label for="" class="form-label">GRN Number</label>
                                <input class="form-control" type="text" autocomplete="off" name="grn_number"
                                    value="<?php echo e(@$ctrl_grn_no); ?>">
                            </div>
                            <div class="col-1-5 mb-2">
                                <label for="" class="form-label">Purchase Return Number</label>
                                <input class="form-control" type="text" autocomplete="off" name="purchase_return_number"
                                    value="<?php echo e(@$ctrl_prt_no); ?>">
                            </div>
                            <div class="col-1-5 mb-2">
                                <label for="" class="form-label">From Date</label>
                                <input class="form-control date-picker" type="text" autocomplete="off" name="from_date"
                                    value="<?php echo e(@App\SysHelper::normalizeToDmy($ctrl_date)); ?>">
                            </div>
                            <div class="col-1-5 mb-2">
                                <label for="" class="form-label">To Date</label>
                                <input class="form-control date-picker" type="text" autocomplete="off" name="to_date"
                                    value="<?php echo e(@App\SysHelper::normalizeToDmy($ctrl_date2)); ?>">
                            </div>
                            <div class="col-1-5 mb-2">
                                <label for="" class="form-label">Sales Person</label>
                                <select class="form-control js-example-basic-single" name="sales_person"
                                    id="">
                                    <option value="">Select</option>
                                    <?php $__currentLoopData = $sales_person_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e(@$value->user_id); ?>" <?php echo e(@$ctrl_sales_person == @$value->user_id ? 'selected' : ''); ?>><?php echo e(@$value->full_name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-1-5 mb-2">
                                <label for="" class="form-label">Currency</label>
                                <select class="form-control js-example-basic-single" name="currency" id="currency">
                                    <option value="">Select</option>
                                    <?php $__currentLoopData = $currency; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e(@$value->id); ?>" <?php echo e(@$ctrl_currency == @$value->id ? 'selected' : ''); ?>><?php echo e(@$value->code); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-1-5 mb-2">
                                <label for="" class="form-label">Attachment</label>
                                <select class="form-control js-example-basic-single" name="attachments" id="attachments">
                                    <option value="">Select</option>
                                    <option value="1" <?php echo e(@$ctrl_attachments == 1 ? 'selected' : ''); ?>>With Attachments Only</option>
                                    <option value="2" <?php echo e(@$ctrl_attachments == 2 ? 'selected' : ''); ?>>Without Attachments Only</option>
                                    <option value="3" <?php echo e(@$ctrl_attachments == 3 ? 'selected' : ''); ?>>All</option>
                                </select>
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
                <?php if(count($purchaseinvoice) > 0): ?>
                    <?php $__currentLoopData = $purchaseinvoice; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="nav-item w-100" role="presentation">
                            <button href="javascript:void(0)"
                                class="nav-link data-item <?php echo e($active_id == $value->id ? 'active' : ''); ?>"
                                data-id="<?php echo e($value->id); ?>">

                                <div class="row w-100">
                                     <div class="col-12">
                                        <label
                                            class="form-control-plaintext truncate-text">
                                            <?php echo e($value->accountname->account_name); ?></label>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-control-plaintext" style="font-size: 11px"><?php echo e($value->doc_number); ?></div>
                                    </div>
                                    <div class="col-4 pl-2">
                                        <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                                            <?php echo e(date('d/m/Y', strtotime(@$value->grn_date))); ?></div>
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
                        <tr >
                            <th class="text-center" style="width:70px"><?php echo app('translator')->getFromJson('PIV No'); ?></th>
                            <th class="text-center" style="width:100px"><?php echo app('translator')->getFromJson('PIV Date'); ?></th>
                            <th style="width:100px"><?php echo app('translator')->getFromJson('Supplier'); ?></th>
                            <th style="width:100px"><?php echo app('translator')->getFromJson('Customer'); ?></th>

                            <th style="width:100px" class="text-end"><?php echo app('translator')->getFromJson('Taxable Amount'); ?></th>
                            <th style="width:100px" class="text-end"><?php echo app('translator')->getFromJson('Tax'); ?></th>
                            <th style="width:100px" class="text-end"><?php echo app('translator')->getFromJson('Amount'); ?></th>
                            <th class="text-center" style="width:70px"><?php echo app('translator')->getFromJson('Deal ID'); ?></th>
                            <th style="width:100px"><?php echo app('translator')->getFromJson('Salesman'); ?></th>
                            <th style="width:70px"><?php echo app('translator')->getFromJson('Bill No'); ?></th>
                            <th class="text-center" style="width:100px"><?php echo app('translator')->getFromJson('Bill Date'); ?></th>
                            <th class="text-center" style="width:70px"><?php echo app('translator')->getFromJson('LPO&nbsp;No'); ?></th>
                            <th class="text-center" style="width:70px"><?php echo app('translator')->getFromJson('GRN&nbsp;No'); ?></th>
                            <th class="text-center" style="width:70px"><?php echo app('translator')->getFromJson('PRT&nbsp;No'); ?></th>
                            <th class="text-center" style="width:70px"><?php echo app('translator')->getFromJson('Currency'); ?></th>
                            <th style="width:70px"><?php echo app('translator')->getFromJson('Payment'); ?></th>
                            <th class="text-center" style="width:70px"><?php echo app('translator')->getFromJson('lang.status'); ?></th>
                            <th style="width:30px"><i class="ico icon-bold-paperclip"></i></th>
                            <th style="width:100px" class="text-center"><?php echo app('translator')->getFromJson('lang.action'); ?></th>
                        </tr>
                    </thead>


                    <tbody>
                        <?php
                            $count = 1;
                            $total_taxable_amount = 0;
                            $total_tax = 0;
                            $total_amount = 0;
                        ?>
                        <?php $__currentLoopData = $purchaseinvoice; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
             
                            <tr <?php if(@$value->status == 2): ?> class="bg-dark" <?php endif; ?>>
                                <td class="text-center"><a href="javascript:void(0)" onclick="list_style_new()"
                                        class="data-item" data-id="<?php echo e($value->id); ?>"><?php echo e(@$value->doc_number); ?></a>
                                </td>
                                <td class="text-center"><?php echo e(date('d/m/Y', strtotime(@$value->pi_date))); ?></td>
                                <td><?php echo e(@$value->accountname->account_name); ?>

                                </td>
                                <td>
                                    <?php
                                    
                                        $selectedCompanies = $value->ref_company_id
        ? explode(',', $value->ref_company_id)
        : [];
      
                                    ?>
                                    
                                      <?php $__empty_1 = true; $__currentLoopData = $selectedCompanies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $companyId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                          <?php
                                              $company = App\SysCustSuppl::find($companyId);
                                          ?>
                                          <?php if($company): ?>
                                              <span><?php echo e($company->name); ?>

                                                <?php if(!$loop->last): ?>
                                                    ,
                                                <?php endif; ?>
                                              </span>
                                          <?php endif; ?>
                                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                          
                                      <?php endif; ?>
                                </td>

                                <td class="text-end">
                                    <?php echo e(@App\SysHelper::com_curr_format(@$value->total_taxableamount, 2, '.', ',')); ?><?php $total_taxable_amount += $value->total_taxableamount; ?>
                                </td>
                                <td class="text-end">
                                    <?php echo e(@App\SysHelper::com_curr_format(@$value->total_vatamount, 2, '.', ',')); ?><?php $total_tax += $value->total_vatamount; ?>
                                </td>
                                <td class="text-end">
                                    <?php echo e(@App\SysHelper::com_curr_format(@$value->amount, 2, '.', ',')); ?><?php $total_amount += $value->amount; ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                $code = explode(',',$value->code);
                                if(count($code)>0){
                                   foreach($code as $c){
                                       $cd = @App\SysHelper::get_code_from_dealid($c);
                                       ?>
                                    <a href="<?php echo e(url('get-url-deal-track/' . $cd)); ?>"
                                        target="_blank"><?php echo e($cd); ?></a>
                                    <?php
                                   }
                                }
                                ?>
                                </td>
                                <td>
                                    <?php if(@$value->sales_person != null): ?>
                                        <?php echo e(@$value->salesperson->first_name.' '.@$value->salesperson->last_name); ?>

                                    <?php elseif(@$value->sales_person_name != null): ?>
                                    <?php echo e(@$value->sales_person_name); ?>

                                    <?php endif; ?>
                                </td>
                                <td>
                                     <?php echo e(@$value->bill_number); ?>

                                </td>
                                <td class="text-center"><?php echo e(date('d/m/Y', strtotime(@$value->bill_date))); ?></td>



                                <td class="text-center">
                                    <?php
                                $lpo = explode(',',$value->lpo_number);
                                if(count($lpo)>0){
                                   foreach($lpo as $p){
                                       ?>
                                    <a href="javacript:void(0);" onclick="list_style()" class="po-item"
                                        data-id="<?php echo e(@App\SysHelper::getPurchaseOrderID($p)); ?>"><?php echo e(@$p); ?></a>
                                    <?php
                                   }
                                }
                                ?>
                                </td>
                                <td class="text-center">
                                    <?php if(empty($value->grn_no)): ?>
                                        <span class="text-dark">Pending</span>
                                    <?php else: ?>
                                        <?php $__currentLoopData = explode(',', $value->grn_no); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <a href="javacript:void(0);" onclick="list_style()" class="grn-item"
                                                data-id="<?php echo e(@App\SysHelper::getGRNID($grn)); ?>"><?php echo e(trim($grn)); ?></a>
                                            <?php if(!$loop->last): ?>
                                                ,
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if(empty($value->prt_no)): ?>
                                        <span class="text-dark">Pending</span>
                                    <?php else: ?>
                                        <?php $__currentLoopData = explode(',', $value->prt_no); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <a href="<?php echo e(url('get-url-purchase-return/' . trim($prt))); ?>"
                                                target="_blank"><?php echo e(trim($prt)); ?></a>
                                            <?php if(!$loop->last): ?>
                                                ,
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </td>

                                <td class="text-center"><?php echo e(@$value->currency_name->code); ?></td>
                                <td class="text-center">
                                    <?php $count = $adj_list->where('bi_doc_no', $value->doc_number)->count(); ?>
                                    <?php if($count == 1): ?>
                                        <span class="text-success">Paid</span>
                                    <?php else: ?>
                                        <span class="text-dark">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if(@$value->return_status == 1): ?>
                                        <span class="text-dark">Returned</span>
                                    <?php elseif(@$value->return_status == 2): ?>
                                        <span class="text-dark">Partial Returned</span>
                                    <?php else: ?>
                                        <span class="text-success">Active</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if(empty(@$value->attach)): ?>
                                    <?php else: ?>
                                        <?php $__currentLoopData = explode(',', @$value->attach); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <a href="<?php echo e(url(trim($att))); ?>" target="_blank"><i class="ico icon-bold-paperclip"
                                                    aria-hidden="true"></i></a>&nbsp;
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                            <div class="d-flex justify-content-center align-items-center gap-1">

                                       <a href="<?php echo e(url('purchase-invoice/' . $value->id . '?pi_action=edit')); ?>"
                                                    class="btn btn-sm btn-light " title="Comments">
                                                    <i class="ico icon-outline-pen-2" style="font-size: 16px;"></i>
                                                </a>

                                                <a href="<?php echo e(url('purchase-invoice/' . $value->id . '/download')); ?>"
                                                    class="btn btn-sm btn-light" title="Comments">
                                                    <i class="ico icon-bold-download-minimalistic"
                                                        style="font-size: 16px;"></i>
                                                </a>

                                                <?php if(@$value->status == 2): ?>
                                                    <a class="btn btn-light btn-sm"
                                                        href="<?php echo e(url('purchase-invoice/'.$value->id.'/restore')); ?>"
                                                        onclick="return confirm('Are you sure you want to restore this item?');">
                                                        <i class="ico icon-bold-restart"
                                                            style="font-size: 16px;"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <a class="btn btn-light btn-sm"
                                                        href="<?php echo e(url('purchase-invoice/'.$value->id.'/delete')); ?>"
                                                        onclick="return confirm('Are you sure you want to delete this item?');">
                                                        <i class="ico icon-outline-trash-bin-minimalistic text-dark" style="font-size: 16px;"></i>
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
                    $(document).on('click', '.data-item', function() {
                        var id = $(this).data('id');

                        $('.data-item').removeClass('active');
                        $('.data-item[data-id="' + id + '"]').addClass('active');

                        // Update the browser URL to include selected ID (without reloading)
                        var newUrl = "<?php echo e(url('purchase-invoice')); ?>/" + id;
                        window.history.pushState({
                            path: newUrl
                        }, '', newUrl);

                        var action = "<?php echo e(URL::to('purchase-invoice-details')); ?>/" + id;

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
                <?php echo $__env->make('backEnd.purchaseinvoice.pi_add', is_array($addData) ? $addData : [], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <?php elseif($action === 'edit'): ?>
                <?php echo $__env->make('backEnd.purchaseinvoice.pi_edit', is_array($editData) ? $editData : [], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                    
                <?php elseif(!empty($data) && is_array($data)): ?>
                    <?php echo $__env->make('backEnd.purchaseinvoice.pi_details', $data, \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <?php else: ?>
                    <div onclick="window.location.href='<?php echo e(url('purchase-invoice?pi_action=add')); ?>'"
                        class="container-fluid d-flex flex-column justify-content-center align-items-center"
                        style="min-height: 90vh;">

                        <!-- Icon + Heading -->
                        <div class="text-center mb-4">
                            <div class="rounded-circle bg-success text-white d-flex justify-content-center align-items-center mx-auto"
                                style="width: 80px; height: 80px; font-size: 36px;cursor:pointer">
                                <i class="ico icon-outline-add-square"></i>
                            </div>
                            <h1 class="fw-bold mt-3" style="cursor:pointer"> Purchase Invoice</h1>
                            
                        </div>

                    </div>
                <?php endif; ?>
            </div>


        </div>
    </div>




    <script>
        $(document).ready(function() {

            $('#search_pi').on('input', function() {
                var query = $(this).val();


                $.ajax({
                    url: "<?php echo e(route('purchase-invoice.search')); ?>",
                    type: "GET",
                    data: {
                        query: query
                    },
                    success: function(data) {




                        console.log(data)


                        $('#short-list').html('');

                        if (data.length > 0) {
                            $.each(data, function(index, purchase) {

                                let ims  = `    <li class="nav-item w-100" role="presentation">
                            <button href="javascript:void(0)"
                                class="nav-link data-item"
                                data-id="${purchase.id}">

                                <div class="row w-100">
                                    <div class="col-12">
                                        <label
                                            class="form-control-plaintext truncate-text">${purchase.accountname.account_code}
                                            - ${purchase.accountname.account_name}</label>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-control-plaintext" style="font-size: 11px">${purchase.doc_number}</div>
                                    </div>
                                    <div class="col-4 pl-2">
                                        <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                                            ${get_format_date(purchase.grn_date)}</div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                                                ${(parseFloat(purchase.amount) || 0).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")}
                                                ${(purchase.currency_name ? purchase.currency_name.code : '')}
                                        </div>
                                    </div>
                                    
                                </div>

                            </button>
                        </li>`;

                               


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

<script>
$(document).ready(function() {
    $('#exportExcelPurchaseInvoices').on('click', function(e) {
        e.preventDefault();

        var companyName = <?php echo json_encode(@App\SysCompany::find(session('logged_session_data.company_id') ?? '')->trade_name ?? '', 15, 512) ?>;
        var totalPI = <?php echo json_encode($purchaseinvoice->count() ?? 0, 15, 512) ?>;
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
        rows.push(['Purchase Invoices (' + totalPI + ')']);

        if (dateFrom || dateTo) {
            var parts = [];
            if (dateFrom) { parts.push('From: ' + formatDMY(dateFrom)); }
            if (dateTo) { parts.push('To: ' + formatDMY(dateTo)); }
            rows.push([parts.join('  ')]);
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
            var worksheet = workbook.addWorksheet('Purchase Invoices');
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
                var filename = 'purchase_invoices_' + pad(d.getDate()) + '-' + pad(d.getMonth() + 1) + '-' + d.getFullYear() + '.xlsx';
                saveAs(blob, filename);
            });
    });
});
</script>


    <?php } catch (\Exception $e) { ?> <?php echo e($e); ?> <?php  } ?>
    <script>
   const SHOW_SUPPLIER_CODE = <?php echo e(@App\SysHelper::getCompanyCodeSettings()['is_supplier_code'] ? 'true' : 'false'); ?>;

        $(document).ready(function() {
            function initAccountSelect2(selector) {
                $(selector).select2({
                    ajax: {
                        url: '<?php echo e(route('autocomplete.get_supp_account_list_ajax')); ?>',
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

                                if (SHOW_SUPPLIER_CODE) {
                                    text = item.account_name + " (" + item.account_code + ")";
                                } else {
                                    text = item.account_name;  // no code
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
    </script>



        <script>

(function () {

    let dragging = false;
    let startX, startY, startLeft, startTop;
    let currentModal = null;

    // Bind drag start
    $(document).on('mousedown', '.modal-dialog.draggable .modal-header', function (e) {
        currentModal = $(this).closest('.modal-dialog');

        dragging = true;

        startX = e.clientX;
        startY = e.clientY;

        const offset = currentModal.offset();
        startLeft = offset.left;
        startTop = offset.top;

        $('body').addClass('unselectable'); // Prevents text selection while dragging
    });

    // Dragging movement
    $(document).on('mousemove', function (e) {
        if (!dragging || !currentModal) return;

        let newLeft = startLeft + (e.clientX - startX);
        let newTop = startTop + (e.clientY - startY);

        currentModal.offset({
            top: newTop,
            left: newLeft
        });
    });

    // Stop drag
    $(document).on('mouseup', function () {
        dragging = false;
        $('body').removeClass('unselectable');
    });

    // Reset modal on open (production behavior)
    $(document).on('show.bs.modal', '.modal', function () {
        let dialog = $(this).find('.modal-dialog.draggable');
        dialog.css({
            top: '10%',
            left: '65%',
            transform: 'translateX(-50%)'
        });
    });

})();
</script> 






<?php $__env->stopSection(); ?>

<?php echo $__env->make('backEnd.newmasterpage', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
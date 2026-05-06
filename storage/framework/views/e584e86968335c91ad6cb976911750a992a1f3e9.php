<?php $__env->startPush('scripts'); ?>
    <script src="https://cdn.jsdelivr.net/npm/exceljs@4.3.0/dist/exceljs.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
<?php $__env->stopPush(); ?>
<?php $__env->startSection('mainContent'); ?>

    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    //$permissions = App\SmRolePermission::where('role_id', 8)->get();
    ?>
    <script>
        $.fn.dataTableExt.sErrMode = 'none';
        $(document).ready(function() {
            if (!$.fn.DataTable.isDataTable('#dataTable')) {
                $('#dataTable').DataTable({
                    "paging": false,
                    "lengthChange": false,
                });
            }
        });
    </script>
    <?php try { ?>

    <div class="content-container col-12">
        <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
            <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
                <div class="purchase-order-content-header">
                    <div class="purchase-order-content-header-left">
                        <div class="dropdown report-type-dropdown">
                            <?php
                                $menuReportGroup = $report_group ?? 'date_wise';
                                $isSalesInvoiceRoute = request()->routeIs('sales.invoice.report.detail');
                                $isSalesReturnRoute = request()->routeIs('sales.return.report.detail');
                            ?>
                            <a class="text-dark report-type-trigger" href="javascript:void(0);" id="salesReportTypeMenu"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                Sales Report Type <i class="icon-outline-alt-arrow-down ms-1"></i>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="salesReportTypeMenu">
                                <li class="dropend">
                                    <a class="dropdown-item dropdown-toggle report-submenu-trigger text-dark" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false">Sales Report</a>
                                    <ul class="dropdown-menu">
                                        <li><a class="text-dark dropdown-item <?php echo e($isSalesInvoiceRoute && $menuReportGroup === 'company_wise' ? 'active' : ''); ?>" href="<?php echo e(route('sales.invoice.report.detail', ['report_group' => 'company_wise'])); ?>">Company Wise</a></li>
                                        <li><a class="text-dark dropdown-item <?php echo e($isSalesInvoiceRoute && $menuReportGroup === 'date_wise' ? 'active' : ''); ?>" href="<?php echo e(route('sales.invoice.report.detail', ['report_group' => 'date_wise'])); ?>">Date Wise</a></li>
                                        <li><a class="text-dark dropdown-item <?php echo e($isSalesInvoiceRoute && $menuReportGroup === 'customer_wise' ? 'active' : ''); ?>" href="<?php echo e(route('sales.invoice.report.detail', ['report_group' => 'customer_wise'])); ?>">Customer Wise</a></li>
                                        <li><a class="text-dark dropdown-item <?php echo e($isSalesInvoiceRoute && $menuReportGroup === 'sales_person_wise' ? 'active' : ''); ?>" href="<?php echo e(route('sales.invoice.report.detail', ['report_group' => 'sales_person_wise'])); ?>">Sales Person Wise</a></li>
                                    </ul>
                                </li>
                                <li class="dropend">
                                    <a class="dropdown-item dropdown-toggle report-submenu-trigger text-dark" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false">Sales Return Report</a>
                                    <ul class="dropdown-menu">
                                        <li><a class="text-dark dropdown-item <?php echo e($isSalesReturnRoute && $menuReportGroup === 'company_wise' ? 'active' : ''); ?>" href="<?php echo e(route('sales.return.report.detail', ['report_group' => 'company_wise'])); ?>">Company Wise</a></li>
                                        <li><a class="text-dark dropdown-item <?php echo e($isSalesReturnRoute && $menuReportGroup === 'date_wise' ? 'active' : ''); ?>" href="<?php echo e(route('sales.return.report.detail', ['report_group' => 'date_wise'])); ?>">Date Wise</a></li>
                                        <li><a class="text-dark dropdown-item <?php echo e($isSalesReturnRoute && $menuReportGroup === 'customer_wise' ? 'active' : ''); ?>" href="<?php echo e(route('sales.return.report.detail', ['report_group' => 'customer_wise'])); ?>">Customer Wise</a></li>
                                        <li><a class="text-dark dropdown-item <?php echo e($isSalesReturnRoute && $menuReportGroup === 'sales_person_wise' ? 'active' : ''); ?>" href="<?php echo e(route('sales.return.report.detail', ['report_group' => 'sales_person_wise'])); ?>">Sales Person Wise</a></li>
                                    </ul>   
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="purchase-order-content-header-right">
                        <button type="button" class="btn btn-light" id="exportSalesInvoiceReport" title="Export to Excel">
                            <i class="ico icon-outline-export text-success"></i> Export
                        </button>
                    
                    </div>
                </div>


                <div class="card mb-3">
                    <div class="card-body">
                        <?php echo e(Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'sales-invoice-report-detail', 'method' => 'get', 'id' => 'sales-invoice-report'])); ?>

                        <input type="hidden" name="report_group" value="<?php echo e($report_group ?? 'date_wise'); ?>">
                        <input type="hidden" name="scope_company_id" value="<?php echo e($scope_company_id ?? ''); ?>">
                        <div class="row">

                            <div class="col-1-5 mb-2">
                                <label for="" class="form-label">Documents Number</label>
                                <input class="form-control" type="text" autocomplete="off" name="documents_number"
                                    value="<?php echo e($ctrl_doc_no); ?>">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="" class="form-label">Customer</label>
                                <select class="form-control js-example-basic-single" name="customer" id="customer">
                                    <option value="">All</option>
                                    <?php $__currentLoopData = $customer_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e(@$value->id); ?>"
                                            <?php if($ctrl_customer == @$value->id): ?> selected <?php endif; ?>><?php echo e(@$value->account_name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-1-5 mb-2">
                                <label for="" class="form-label">Deal ID</label>
                                <input class="form-control" type="text" autocomplete="off" name="deal_number"
                                    value="<?php echo e($ctrl_deal_id); ?>">
                            </div>
                            <div class="col-1 mb-2">
                                <label for="" class="form-label">Amount</label>
                                <input class="form-control" type="number" autocomplete="off" name="amount"
                                    value="<?php echo e($ctrl_amount); ?>">
                            </div>
                            <div class="col-1-5 mb-2">
                                <label for="" class="form-label">From Date</label>
                                <input class="form-control date-picker" type="text" autocomplete="off" name="from_date"
                                    id="from_date" value="<?php echo e($ctrl_date ? \Carbon\Carbon::parse($ctrl_date)->format('d/m/Y') : ''); ?>" onchange="set_filter()">
                            </div>
                            <div class="col-1-5 mb-2">
                                <label for="" class="form-label">To Date</label>
                                <input class="form-control date-picker" type="text" autocomplete="off" name="to_date" id="to_date"
                                    value="<?php echo e($ctrl_date2 ? \Carbon\Carbon::parse($ctrl_date2)->format('d/m/Y') : ''); ?>" onchange="set_filter()">
                            </div>
                            <?php if(($report_group ?? '') === 'customer_wise'): ?>
                                <div class="col-1 mb-2">
                                    <label for="" class="form-label">From Day</label>
                                    <input class="form-control" type="number" min="0" autocomplete="off" name="from_day"
                                        id="from_day" value="<?php echo e($ctrl_from_day ?? ''); ?>" onchange="set_filter_days()">
                                </div>
                                <div class="col-1 mb-2">
                                    <label for="" class="form-label">To Day</label>
                                    <input class="form-control" type="number" min="0" autocomplete="off" name="to_day"
                                        id="to_day" value="<?php echo e($ctrl_to_day ?? ''); ?>" onchange="set_filter_days()">
                                </div>
                            <?php endif; ?>
                            <div class="col-1-5 mb-2">
                                <label for="" class="form-label">Sales Person</label>
                                <select class="form-control js-example-basic-single" name="sales_person" id="sales_person">
                                    <option value="">All</option>
                                    <?php $__currentLoopData = $sales_person_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e(@$value->user_id); ?>"
                                            <?php if($ctrl_sales_person == @$value->user_id): ?> selected <?php endif; ?>><?php echo e(@$value->full_name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <?php if(session('logged_session_data.company_id') == 1): ?>
                                <div class="col-1-5 mb-2">
                                    <label for="" class="form-label">Company</label>
                                    <select class="form-control js-example-basic-single" name="company" id="company">
                                        <option value=""></option>
                                        <?php $__currentLoopData = $company_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e(@$value->id); ?>"
                                                <?php if($ctrl_company == @$value->id): ?> selected <?php endif; ?>>
                                                <?php echo e(@$value->company_name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            <?php endif; ?>

                            <div class="col-1-5 mb-2">
                                <label for="" class="form-label">Filter By</label>
                                <select class="form-control" name="filter_by" id="filter_by">
                                    <option value="" <?php if($filter_by == ''): ?> selected <?php endif; ?>>-Select-
                                    </option>
                                    <option value="this_month" <?php if($filter_by == 'this_month'): ?> selected <?php endif; ?>>This Month
                                    </option>
                                    <option value="today" <?php if($filter_by == 'today'): ?> selected <?php endif; ?>>Today</option>
                                    <option value="this_week" <?php if($filter_by == 'this_week'): ?> selected <?php endif; ?>>This Week
                                    </option>
                                    <option value="last_week" <?php if($filter_by == 'last_week'): ?> selected <?php endif; ?>>Last Week
                                    </option>
                                    <option value="last_month" <?php if($filter_by == 'last_month'): ?> selected <?php endif; ?>>Last Month
                                    </option>
                                    <option value="this_quarter" <?php if($filter_by == 'this_quarter'): ?> selected <?php endif; ?>>This
                                        Quarter</option>
                                    <option value="pre_quarter" <?php if($filter_by == 'pre_quarter'): ?> selected <?php endif; ?>>Previous
                                        Quarter</option>
                                    <option value="this_year" <?php if($filter_by == 'this_year'): ?> selected <?php endif; ?>>This Year
                                    </option>
                                    <option value="last_year" <?php if($filter_by == 'last_year'): ?> selected <?php endif; ?>>Last Year
                                    </option>
                                </select>
                            </div>
                            <div class="col-1-5 mb-2">
                                <label for="" class="form-label">Show All</label>
                                <select class="form-control" name="show_all" id="show_all">
                                    <option value="0" <?php if(($ctrl_show_all ?? 0) != 1): ?> selected <?php endif; ?>>No</option>
                                    <option value="1" <?php if(($ctrl_show_all ?? 0) == 1): ?> selected <?php endif; ?>>Yes</option>
                                </select>
                            </div>
                            <script>
                                function set_filter() {
                                    if ($('#from_date').val() != "" || $('#to_date').val() != "") {
                                        $('#filter_by').val('')
                                    }
                                }
                                function set_filter_days() {
                                    if ($('#from_day').val() != "" || $('#to_day').val() != "") {
                                        $('#filter_by').val('');
                                    }
                                }
                            </script>

                            <div class="col-1"><br />
                                <button type="submit" class="btn btn-light" id="btnSubmit">
                                    <i class="ico icon-outline-magnifer"></i> Search
                                </button>
                            </div>
                        </div>
                        <?php echo e(Form::close()); ?>

                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-body">

                        <?php
                            $activeReportGroup = $report_group ?? 'date_wise';
                            $reportGroupLabels = [
                                'company_wise' => 'Company Wise',
                                'date_wise' => 'Date Wise',
                                'customer_wise' => 'Customer Wise',
                                'sales_person_wise' => 'Sales Person Wise',
                            ];
                            $activeReportLabel = $reportGroupLabels[$activeReportGroup] ?? 'Date Wise';
                            $selectedCompanyName = '';
                            if (!empty($scope_company_id) && !empty($company_list)) {
                                $selectedCompanyName = optional(collect($company_list)->firstWhere('id', $scope_company_id))->company_name ?? '';
                            }
                            if (empty($selectedCompanyName) && !empty($ctrl_company) && !empty($company_list)) {
                                $selectedCompanyName = optional(collect($company_list)->firstWhere('id', $ctrl_company))->company_name ?? '';
                            }
                            $showCompanyContextHeading = in_array($activeReportGroup, ['date_wise', 'customer_wise', 'sales_person_wise']) && !empty($selectedCompanyName);
                            $hideCompanyColumn = $activeReportGroup === 'date_wise' && $showCompanyContextHeading;
                        ?>

                        <form id="receivableOutstandingRedirectForm" method="POST" action="<?php echo e(route('receivable-outstanding')); ?>" target="_blank" style="display:none;">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="account_id[]" id="receivableOutstandingCustomerId" value="">
                            <input type="hidden" name="till_date" id="receivableOutstandingTillDate" value="">
                        </form>
                        <form id="generalLedgerRedirectForm" method="POST" action="<?php echo e(url('generalledger')); ?>" target="_blank" style="display:none;">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="account_id[]" id="generalLedgerCustomerId" value="">
                            <input type="hidden" name="from_date" id="generalLedgerFromDate" value="">
                            <input type="hidden" name="to_date" id="generalLedgerToDate" value="">
                            <input type="hidden" name="filter_by" value="">
                        </form>

                        <?php if($showCompanyContextHeading): ?>
                            <div class="mb-2 fw-bold">
                                <?php echo e($activeReportLabel); ?> Report - Company: <?php echo e($selectedCompanyName); ?>

                            </div>
                        <?php endif; ?>

                        <div class="table-responsive">
                            <table class="table table-hover" id="long-list" style="table-layout: fixed;width:100%">
                                <?php if(($report_group ?? 'date_wise') === 'date_wise'): ?>
                                    <thead>
                                        <tr>
                                            <?php if(!$hideCompanyColumn): ?>
                                                <th style="width: 80px;"><?php echo app('translator')->getFromJson('Company'); ?></th>
                                            <?php endif; ?>
                                            <th style="width: 70px;" class="text-center"><?php echo app('translator')->getFromJson('Deal'); ?></th>
                                            <th class="text-center" style="width: 70px;"><?php echo app('translator')->getFromJson('SI No'); ?></th>
                                            <th class="text-center" style="width: 80px;"><?php echo app('translator')->getFromJson('SI Date'); ?></th>
                                            <th style="width: 130px;"><?php echo app('translator')->getFromJson('Customer'); ?></th>
                                            <th style="width: 80px;" class="text-end"><?php echo app('translator')->getFromJson('Value'); ?></th>
                                            <th style="width: 60px;" class="text-end"><?php echo app('translator')->getFromJson('Discount'); ?></th>
                                            <th style="width: 80px;" class="text-end"><?php echo app('translator')->getFromJson('Taxable'); ?></th>
                                            <th style="width: 80px;" class="text-end"><?php echo app('translator')->getFromJson('Tax'); ?></th>
                                            <th style="width: 80px;" class="text-end"><?php echo app('translator')->getFromJson('Amount'); ?></th>
                                            <th style="width: 80px;" class="text-end"><?php echo app('translator')->getFromJson('GP'); ?></th>
                                            <th class="text-end" style="width: 60px;"><?php echo app('translator')->getFromJson('GP%'); ?></th>
                                            <th style="width: 110px;"><?php echo app('translator')->getFromJson('Sales Person'); ?></th>
                                            <th style="width:60px" class="text-center"><?php echo app('translator')->getFromJson('LPO'); ?></th>
                                            <th style="width:80px" class="text-center"><?php echo app('translator')->getFromJson('LPO Date'); ?></th>
                                            <th style="width:60px" class="text-center"><?php echo app('translator')->getFromJson('Currency'); ?></th>
                                            <th style="width:60px" class="text-center"><?php echo app('translator')->getFromJson('Payment'); ?></th>
                                            <th style="width: 50px;" class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $total_taxable_amount = 0;
                                            $total_tax = 0;
                                            $total_amount = 0;
                                            $total_value = 0;
                                            $total_discount = 0;
                                            $total_gp = 0;
                                        ?>
                                        <?php $__currentLoopData = $salesinvoice; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $deal_value = @App\SysHelper::get_aed_amount_new($value->deal_currency, $value->deal_value);
                                                $deal_profit = @App\SysHelper::get_aed_amount_new($value->deal_currency, $value->deal_profit);
                                                $deal_percentage = $deal_value != 0 ? round(($deal_profit / $deal_value) * 100, 2) : 0;
                                                $gp = (($value->total_taxableamount - $value->deal_discount) * $deal_percentage) / 100;
                                                $total_value += $value->value;
                                                $total_discount += ($value->discount + $value->deal_discount);
                                                $total_taxable_amount += ($value->total_taxableamount - $value->deal_discount);
                                                $total_tax += $value->total_vatamount;
                                                $total_amount += $value->amount;
                                                $total_gp += $gp;
                                            ?>
                                            <tr>
                                                <?php if(!$hideCompanyColumn): ?>
                                                    <td><?php echo e(@$value->company->company_name); ?></td>
                                                <?php endif; ?>
                                                <td class="text-center">
                                                    <?php if(@$value->code == ''): ?>
                                                        --
                                                    <?php else: ?>
                                                        <a href="<?php echo e(url('get-url-deal-track/' . $value->code)); ?>" target="_blank"><?php echo e(@$value->code); ?></a>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center"><a href="<?php echo e(url('sales-invoice/' . $value->id)); ?>" target="_blank"><?php echo e(@$value->doc_number); ?></a></td>
                                                <td class="text-center"><?php echo e(date('d/m/Y', strtotime(@$value->doc_date))); ?></td>
                                                <td><?php echo e(@$value->accountname->account_name); ?></td>
                                                <td class="text-end"><?php echo e(@App\SysHelper::com_curr_format(@$value->value, 2, '.', ',')); ?></td>
                                                <td class="text-end"><?php echo e(@App\SysHelper::com_curr_format(@$value->discount + $value->deal_discount, 2, '.', ',')); ?></td>
                                                <td class="text-end"><?php echo e(@App\SysHelper::com_curr_format(@$value->total_taxableamount - $value->deal_discount, 2, '.', ',')); ?></td>
                                                <td class="text-end"><?php echo e(@App\SysHelper::com_curr_format(@$value->total_vatamount, 2, '.', ',')); ?></td>
                                                <td class="text-end"><?php echo e(@App\SysHelper::com_curr_format(@$value->amount, 2, '.', ',')); ?></td>
                                                <td class="text-end"><?php echo e(@App\SysHelper::com_curr_format($gp, 2, '.', ',')); ?></td>
                                                <td class="text-end"><?php echo e($deal_percentage); ?>%</td>
                                                <td><?php echo e(@$value->salesman->full_name); ?></td>
                                                <td class="text-center"><?php echo e(@$value->lpo_number); ?></td>
                                                <td class="text-center"><?php echo e(!empty($value->lpo_date) ? date('d/m/Y', strtotime($value->lpo_date)) : ''); ?></td>
                                                <td class="text-center"><?php echo e(@$value->currency_name->code); ?></td>
                                                <td class="text-center">
                                                    <?php if(isset($paid_doc_numbers[$value->doc_number])): ?>
                                                        <span class="text-success">Paid</span>
                                                    <?php else: ?>
                                                        <span class="text-danger">Pending</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-end gap-2">
                                                        <?php if(!empty($value->attach)): ?>
                                                            <?php $__currentLoopData = explode(',', $value->attach); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <a href="<?php echo e(url(trim($att))); ?>" target="_blank"><i class="ico icon-bold-paperclip"></i></a>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        <?php endif; ?>
                                                        <a href="<?php echo e(url('sales-invoice/' . $value->id . '/download/t')); ?>" target="_blank">
                                                            <i class="ico icon-bold-download-minimalistic text-dark" style="font-size: 16px;"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($salesinvoice->count() == 0): ?>
                                            <tr><td colspan="<?php echo e($hideCompanyColumn ? 17 : 18); ?>" class="text-center">No data found</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                    <footer>
                                        <tr>
                                            <th colspan="<?php echo e($hideCompanyColumn ? 4 : 5); ?>"></th>
                                            <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($total_value, 2, '.', ',')); ?></th>
                                            <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($total_discount, 2, '.', ',')); ?></th>
                                            <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($total_taxable_amount, 2, '.', ',')); ?></th>
                                            <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($total_tax, 2, '.', ',')); ?></th>
                                            <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($total_amount, 2, '.', ',')); ?></th>
                                            <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($total_gp, 2, '.', ',')); ?></th>
                                            <th colspan="7"></th>
                                        </tr>
                                    </footer>
                                <?php else: ?>
                                    <thead>
                                        <tr>
                                            <th style='width:260px'><?php echo e(in_array($report_group, ['company_wise']) ? 'Company Name' : (in_array($report_group, ['customer_wise']) ? 'Customer Name' : 'Sales Person Name')); ?></th>
                                            <th class="text-center">No. of Invoices</th>
                                            <th class="text-end">Value</th>
                                            <th class="text-end">Discount</th>
                                            <th class="text-end">Taxable Amount</th>
                                            <th class="text-end">Tax</th>
                                            <th class="text-end">Amount</th>
                                            <th class="text-end">GP</th>
                                            <th class="text-end">GP%</th>
                                            <?php if($report_group === 'customer_wise'): ?>
                                                <th class="text-end" style="width:160px">Outstanding</th>
                                                <th class="text-center" style="width:120px">Last Invoice Date</th>
                                                <th class="text-start" style="width:110px">Sales Person</th>
                                                <th class="text-center" style="width:40px">GL</th>
                                            <?php endif; ?>
                                            <?php if($report_group === 'company_wise'): ?>
                                                <th class="text-center" style="width:320px">Reports</th>
                                            <?php endif; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $sum_invoice_count = 0;
                                            $sum_value = 0;
                                            $sum_discount = 0;
                                            $sum_taxable = 0;
                                            $sum_tax = 0;
                                            $sum_amount = 0;
                                            $sum_gp = 0;
                                            $sum_customer_balance = 0;
                                        ?>
                                        <?php $__currentLoopData = $report_rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $sum_invoice_count += $row->invoice_count;
                                                $sum_value += $row->value;
                                                $sum_discount += $row->discount;
                                                $sum_taxable += $row->taxable;
                                                $sum_tax += $row->tax;
                                                $sum_amount += $row->amount;
                                                $sum_gp += $row->gp;
                                                if ($report_group === 'customer_wise') {
                                                    $sum_customer_balance += ($row->customer_balance ?? 0);
                                                }

                                                $drillFilters = ['report_group' => 'date_wise'];
                                                if ($report_group === 'company_wise') {
                                                    $drillFilters['company'] = $row->company_id;
                                                } elseif ($report_group === 'customer_wise') {
                                                    $drillFilters['customer'] = $row->customer;
                                                    if (!empty($scope_company_id)) {
                                                        $drillFilters['company'] = $scope_company_id;
                                                    } elseif (isset($row->company_id) && !empty($row->company_id)) {
                                                        $drillFilters['company'] = $row->company_id;
                                                    }
                                                } else {
                                                    $drillFilters['sales_person'] = $row->sales_man;
                                                    if (!empty($scope_company_id)) {
                                                        $drillFilters['company'] = $scope_company_id;
                                                    } elseif (isset($row->company_id) && !empty($row->company_id)) {
                                                        $drillFilters['company'] = $row->company_id;
                                                    }
                                                }
                                            ?>
                                            <tr>
                                                <td>
                                                    <?php if($report_group === 'company_wise' && !empty($row->company_id)): ?>
                                                        <a href="<?php echo e(url('company?active=' . $row->company_id)); ?>" target="_blank"><?php echo e($row->group_name); ?></a>
                                                    <?php elseif($report_group === 'customer_wise' && !empty($row->customer)): ?>
                                                        <a href="<?php echo e(url('get-url-customer-from-chart-of-accounts/' . $row->customer)); ?>" target="_blank"><?php echo e($row->group_name); ?></a>
                                                    <?php elseif($report_group === 'sales_person_wise' && !empty($row->staff_id)): ?>
                                                        <a href="<?php echo e(url('view-staff/' . $row->staff_id)); ?>" target="_blank"><?php echo e($row->group_name); ?></a>
                                                    <?php else: ?>
                                                        <?php echo e($row->group_name); ?>

                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center"><a href="<?php echo e(route('sales.invoice.report.detail', $drillFilters)); ?>"><?php echo e($row->invoice_count); ?></a></td>
                                                <td class="text-end"><?php echo e(@App\SysHelper::com_curr_format($row->value, 2, '.', ',')); ?></td>
                                                <td class="text-end"><?php echo e(@App\SysHelper::com_curr_format($row->discount, 2, '.', ',')); ?></td>
                                                <td class="text-end"><?php echo e(@App\SysHelper::com_curr_format($row->taxable, 2, '.', ',')); ?></td>
                                                <td class="text-end"><?php echo e(@App\SysHelper::com_curr_format($row->tax, 2, '.', ',')); ?></td>
                                                <td class="text-end"><?php echo e(@App\SysHelper::com_curr_format($row->amount, 2, '.', ',')); ?></td>
                                                <td class="text-end"><?php echo e(@App\SysHelper::com_curr_format($row->gp, 2, '.', ',')); ?></td>
                                                <td class="text-end"><?php echo e(round($row->gp_percent, 2)); ?>%</td>
                                                <?php if($report_group === 'customer_wise'): ?>
                                                    <td class="text-end">
                                                        <a href="javascript:void(0)" class="open-receivable-outstanding"
                                                           data-customer-id="<?php echo e($row->customer); ?>"
                                                           data-till-date="<?php echo e(!empty($ctrl_date2) ? \Carbon\Carbon::parse($ctrl_date2)->format('d/m/Y') : date('d/m/Y')); ?>">
                                                            <?php echo e(@App\SysHelper::com_curr_format($row->customer_balance ?? 0, 2, '.', ',')); ?>

                                                        </a>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php if(!empty($row->last_invoice_date)): ?>
                                                            <?php echo e(date('d/m/Y', strtotime($row->last_invoice_date))); ?>

                                                            (<?php echo e(\Carbon\Carbon::parse($row->last_invoice_date)->diffInDays(\Carbon\Carbon::today())); ?>d)
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo e($row->sales_person_names ?? ''); ?></td>
                                                    <td class="text-center">
                                                        <a href="javascript:void(0)" class="open-general-ledger"
                                                           data-customer-id="<?php echo e($row->customer); ?>"
                                                           data-from-date="<?php echo e(!empty($ctrl_date) ? \Carbon\Carbon::parse($ctrl_date)->format('d/m/Y') : date('01/01/Y')); ?>"
                                                           data-to-date="<?php echo e(!empty($ctrl_date2) ? \Carbon\Carbon::parse($ctrl_date2)->format('d/m/Y') : date('d/m/Y')); ?>"
                                                           title="Open General Ledger">
                                                            <i class="ico icon-outline-eye text-success"></i>
                                                        </a>
                                                    </td>
                                                <?php endif; ?>
                                                <?php if($report_group === 'company_wise'): ?>
                                                    <td class="text-center">
                                                        <div class="d-inline-flex gap-1 flex-nowrap">
                                                            <a class="btn btn-sm btn-light py-0 px-2 text-nowrap" href="<?php echo e(route('sales.invoice.report.detail', ['report_group' => 'date_wise', 'scope_company_id' => $row->company_id])); ?>">Date Wise</a>
                                                            <a class="btn btn-sm btn-light py-0 px-2 text-nowrap" href="<?php echo e(route('sales.invoice.report.detail', ['report_group' => 'customer_wise', 'scope_company_id' => $row->company_id])); ?>">Customer Wise</a>
                                                            <a class="btn btn-sm btn-light py-0 px-2 text-nowrap" href="<?php echo e(route('sales.invoice.report.detail', ['report_group' => 'sales_person_wise', 'scope_company_id' => $row->company_id])); ?>">Sales Person Wise</a>
                                                        </div>
                                                    </td>
                                                <?php endif; ?>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($report_rows->count() == 0): ?>
                                            <tr><td colspan="<?php echo e($report_group === 'company_wise' ? 10 : ($report_group === 'customer_wise' ? 13 : 9)); ?>" class="text-center">No data found</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                    <footer>
                                        <tr>
                                            <th>Total</th>
                                            <th class="text-center"><?php echo e($sum_invoice_count); ?></th>
                                            <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($sum_value, 2, '.', ',')); ?></th>
                                            <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($sum_discount, 2, '.', ',')); ?></th>
                                            <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($sum_taxable, 2, '.', ',')); ?></th>
                                            <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($sum_tax, 2, '.', ',')); ?></th>
                                            <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($sum_amount, 2, '.', ',')); ?></th>
                                            <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($sum_gp, 2, '.', ',')); ?></th>
                                            <th class="text-end"><?php echo e($sum_value != 0 ? round(($sum_gp / $sum_value) * 100, 2) : 0); ?>%</th>
                                            <?php if($report_group === 'customer_wise'): ?>
                                                <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($sum_customer_balance, 2, '.', ',')); ?></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                            <?php endif; ?>
                                            <?php if($report_group === 'company_wise'): ?>
                                                <th></th>
                                            <?php endif; ?>
                                        </tr>
                                    </footer>
                                <?php endif; ?>
                                
                            </table>
                        </div>
                        <style>
                            .report-type-trigger {
                                color: #212529;
                                text-decoration: none;
                                font-weight: 500;
                                display: inline-flex;
                                align-items: center;
                            }
                            .report-type-trigger:hover {
                                color: #499258;
                            }
                            .dropdown-menu .dropend .dropdown-menu {
                                top: 0;
                                left: 100%;
                                margin-top: -1px;
                            }
                            .report-type-dropdown .dropdown-item.active,
                            .report-type-dropdown .dropdown-item.text-success {
                                color: #499258 !important;
                                background-color: transparent !important;
                                font-weight: 600;
                            }
                            .pagination .page-link {
                                color: #499258; /* Bootstrap green */
                        
                            }
                        
                          
                            .pagination .page-item.active .page-link {
                                background-color: #499258;
                                color: #fff;
                            }
                        </style>

                        
                        <?php if(($report_group ?? 'date_wise') === 'date_wise' && ($ctrl_show_all ?? 0) != 1 && $salesinvoice instanceof \Illuminate\Pagination\LengthAwarePaginator): ?>
                            <div class="d-flex justify-content-start mt-3">
                                <?php echo e($salesinvoice->appends(request()->input())->links()); ?>

                            </div>
                        <?php endif; ?>

                        <script>
                            function show_tool_tip(id) {
                                $('#desc_' + id).css('white-space', '');
                            }

                            function hide_tool_tip(id) {
                                $('#desc_' + id).css('white-space', 'nowrap');
                            }
                        </script>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <?php }catch (\Exception $e) { ?> <?php echo e($e); ?> <?php  } ?>

<script>
$(document).ready(function () {
    $(document).on('click', '.open-receivable-outstanding', function (e) {
        e.preventDefault();
        var customerId = $(this).data('customer-id');
        var tillDate = $(this).data('till-date') || '';
        $('#receivableOutstandingCustomerId').val(customerId);
        $('#receivableOutstandingTillDate').val(tillDate);
        $('#receivableOutstandingRedirectForm').trigger('submit');
    });
    $(document).on('click', '.open-general-ledger', function (e) {
        e.preventDefault();
        $('#generalLedgerCustomerId').val($(this).data('customer-id'));
        $('#generalLedgerFromDate').val($(this).data('from-date') || '');
        $('#generalLedgerToDate').val($(this).data('to-date') || '');
        $('#generalLedgerRedirectForm').trigger('submit');
    });

    $('.report-submenu-trigger').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var $submenu = $(this).next('.dropdown-menu');
        var isShown = $submenu.hasClass('show');

        $('.report-type-dropdown .dropend .dropdown-menu').removeClass('show');
        $('.report-submenu-trigger').attr('aria-expanded', 'false');

        if (!isShown) {
            $submenu.addClass('show');
            $(this).attr('aria-expanded', 'true');
        }
    });

    $(document).on('click', function () {
        $('.report-type-dropdown .dropend .dropdown-menu').removeClass('show');
        $('.report-submenu-trigger').attr('aria-expanded', 'false');
    });

    $('.report-type-dropdown .dropdown-menu').on('click', function (e) {
        e.stopPropagation();
    });

    $('#exportSalesInvoiceReport').on('click', function (e) {
        e.preventDefault();

        var companyName = <?php echo json_encode(@App\SysCompany::find(session('logged_session_data.company_id') ?? '')->trade_name ?? '', 15, 512) ?>;
        var fromDate = <?php echo json_encode($ctrl_date ?? '', 15, 512) ?>;
        var toDate = <?php echo json_encode($ctrl_date2 ?? '', 15, 512) ?>;
        var reportGroup = <?php echo json_encode($report_group ?? 'date_wise', 15, 512) ?>;
        var reportGroupLabelMap = {
            company_wise: 'Company Wise',
            date_wise: 'Date Wise',
            customer_wise: 'Customer Wise',
            sales_person_wise: 'Sales Person Wise'
        };
        var reportGroupLabel = reportGroupLabelMap[reportGroup] || 'Date Wise';

        function selectedText(selector) {
            var text = $(selector + ' option:selected').text() || '';
            return text.trim();
        }

        var selectedCompany = selectedText('#company');
        var selectedCustomer = selectedText('#customer');
        var selectedSalesPerson = selectedText('#sales_person');
        if (!selectedCompany) {
            selectedCompany = <?php echo json_encode($selectedCompanyName ?? '', 15, 512) ?>;
        }

        function formatDMY(value) {
            if (!value) return '';
            var normalized = String(value).trim().replace(/\s+/g, '');
            var parts = normalized.split(/[\/\-\.]/);
            if (parts.length === 3) {
                if (parts[0].length === 4) {
                    return parts[2] + '/' + parts[1] + '/' + parts[0];
                }
                return parts[0] + '/' + parts[1] + '/' + parts[2];
            }
            return value;
        }

        function buildRowsFromTable($table) {
            var headers = [];
            var visibleIndexes = [];
            $table.find('thead tr').last().find('th').each(function (index) {
                var label = $(this).text().trim();
                if (!label) return;
                if (/action/i.test(label)) return;
                headers.push(label);
                visibleIndexes.push(index);
            });

            if (headers.length === 0) {
                return null;
            }

            var rows = [];
            rows.push([companyName]);
            rows.push(['Sales Invoice Report']);
            rows.push(['Report Type: ' + reportGroupLabel]);
            if (selectedCompany && selectedCompany.toLowerCase() !== 'all') {
                rows.push(['Company: ' + selectedCompany]);
            }
            if (selectedCustomer && selectedCustomer.toLowerCase() !== 'all') {
                rows.push(['Customer: ' + selectedCustomer]);
            }
            if (selectedSalesPerson && selectedSalesPerson.toLowerCase() !== 'all') {
                rows.push(['Sales Person: ' + selectedSalesPerson]);
            }
            if (fromDate || toDate) {
                var parts = [];
                if (fromDate) parts.push('From: ' + formatDMY(fromDate));
                if (toDate) parts.push('To: ' + formatDMY(toDate));
                rows.push([parts.join('   ')]);
            }
            rows.push([]);
            rows.push(headers);

            $table.find('tbody tr').each(function () {
                var row = [];
                var $cells = $(this).find('td');
                visibleIndexes.forEach(function (idx) {
                    var text = $cells.eq(idx).text().trim().replace(/\s+/g, ' ');
                    row.push(text);
                });
                if (row.length > 0) {
                    rows.push(row);
                }
            });

            var $footer = $table.find('footer tr').first();
            if ($footer.length) {
                var totalRow = [];
                $footer.children('th').each(function () {
                    var text = $(this).text().trim().replace(/\s+/g, ' ');
                    totalRow.push(text);
                });
                if (totalRow.length > 0) {
                    rows.push([]);
                    rows.push(totalRow);
                }
            }

            return { rows: rows, headers: headers };
        }

        function exportRows(rows, headers) {
            var workbook = new ExcelJS.Workbook();
            var worksheet = workbook.addWorksheet('Sales Invoice Report');

            worksheet.columns = headers.map(function () {
                return { width: 18 };
            });

            var rowIndex = 0;
            var headerRowIndex = rows.findIndex(function (r) { return Array.isArray(r) && r.length && r[0] === headers[0]; }) + 1;
            rows.forEach(function (rowData) {
                rowIndex++;
                var row = worksheet.addRow(rowData);
                if (rowData.length === 1 && headers.length > 1 && rowIndex !== 1) {
                    worksheet.mergeCells(rowIndex, 1, rowIndex, headers.length);
                }
                if (rowIndex === 1) {
                    worksheet.mergeCells(rowIndex, 1, rowIndex, headers.length);
                    row.getCell(1).font = { bold: true, size: 14 };
                    row.getCell(1).alignment = { horizontal: 'center', vertical: 'middle' };
                } else if (rowIndex === 2) {
                    row.getCell(1).font = { bold: true, size: 12 };
                    row.getCell(1).alignment = { horizontal: 'center', vertical: 'middle' };
                } else if (rowData.length === 1 && rowIndex < headerRowIndex) {
                    row.getCell(1).font = { size: 10, bold: rowIndex === 3 };
                    row.getCell(1).alignment = { horizontal: 'left', vertical: 'middle' };
                }

                if (rowIndex === headerRowIndex) {
                    row.eachCell({ includeEmpty: true }, function (cell) {
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
                }
            });

            if (rows.length <= 5) {
                alert('No data available for export.');
                return;
            }

            workbook.xlsx.writeBuffer().then(function (buffer) {
                var blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                function pad(n) { return n < 10 ? '0' + n : '' + n; }
                var d = new Date();
                saveAs(blob, 'sales_invoice_report_' + pad(d.getDate()) + '-' + pad(d.getMonth() + 1) + '-' + d.getFullYear() + '.xlsx');
            });
        }

        var isDateWise = <?php echo json_encode(($report_group ?? 'date_wise') === 'date_wise', 15, 512) ?>;
        var isShowAll = <?php echo json_encode((int)($ctrl_show_all ?? 0) === 1, 15, 512) ?>;

        function exportFromCurrentTable() {
            var data = buildRowsFromTable($('#long-list'));
            if (!data) {
                alert('No table headers found for export.');
                return;
            }
            exportRows(data.rows, data.headers);
        }

        if (!isDateWise || isShowAll) {
            exportFromCurrentTable();
            return;
        }

        var params = new URLSearchParams(new FormData(document.getElementById('sales-invoice-report')));
        params.set('show_all', '1');
        params.set('report_group', 'date_wise');
        var exportUrl = "<?php echo e(url('sales-invoice-report-detail')); ?>" + '?' + params.toString();

        $.get(exportUrl, function (html) {
            var parsed = $('<div>').html(html);
            var fullTable = parsed.find('#long-list').first();
            if (!fullTable.length) {
                alert('Unable to prepare full data for export.');
                return;
            }
            var data = buildRowsFromTable(fullTable);
            if (!data) {
                alert('No table headers found for export.');
                return;
            }
            exportRows(data.rows, data.headers);
        }).fail(function () {
            alert('Unable to fetch full data for export.');
        });
    });
});
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('backEnd.newmasterpage', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
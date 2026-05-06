<?php $__env->startSection('mainContent'); ?>


   <?php
                           //pass this year date and session company
                           $total_credit = 0;
                           $total_debit = 0;
                            $_trading_sales = @App\Http\Controllers\SysProfitAndLossAccountController::get_sales($t_from_date, $t_to_date, [session('logged_session_data.company_id')]);
                            $_trading_closing_stock = @App\Http\Controllers\SysProfitAndLossAccountController::get_closing_stock_update($t_from_date, $t_to_date, [session('logged_session_data.company_id')]);
                            $_trading_sales_return =     @App\Http\Controllers\SysProfitAndLossAccountController::get_sales_return($t_from_date, $t_to_date, [session('logged_session_data.company_id')]);
                            $_trading_purchase_return = @App\Http\Controllers\SysProfitAndLossAccountController::get_purchase_return($t_from_date, $t_to_date, [session('logged_session_data.company_id')]); 
                            $_trading_opening_stock = @App\Http\Controllers\SysProfitAndLossAccountController::get_opening_stock_trading_new($t_from_date, $t_to_date, [session('logged_session_data.company_id')]);
                            $_trading_purchase = @App\Http\Controllers\SysProfitAndLossAccountController::get_purchase($t_from_date, $t_to_date, [session('logged_session_data.company_id')]);




                            $cogs = abs($_trading_opening_stock) + (abs($_trading_purchase) - abs($_trading_purchase_return)) - abs($_trading_closing_stock);

                        $_trading_subgruop2= @App\SysAccountGroupSub2::select('sys_account_group_sub2.id', 'sys_account_group_sub2.title', 'sys_account_group_sub2.sub_id', DB::raw('SUM(cat.debit_amount) as total_debit'), DB::raw('SUM(cat.credit_amount) as total_credit'))
                                        ->leftjoin('sys_chartofaccounts as ca','ca.subgroup2','sys_account_group_sub2.id')
                                        ->leftjoin('sys_chartofaccounts_transaction as cat','cat.account_id','ca.id')
                                        ->wherein('sub_id',[13,15]) // 13 Direct Expense, 15 Direct Income
                                        ->where('title','not like', 'sales%')->where('title','not like', 'purchase%')->where('title','not like', '%stock')
                                        ->wherein('ca.company_id',[session('logged_session_data.company_id')])
                                        ->whereRaw("DATE_FORMAT(cat.transaction_date, '%Y-%m-%d') >= '" . $t_from_date . "'")
                                        ->whereRaw("DATE_FORMAT(cat.transaction_date, '%Y-%m-%d') <= '" . $t_to_date . "'")
                                        ->where('cat.status',1)
                                        ->groupby('sys_account_group_sub2.id','sys_account_group_sub2.title', 'sys_account_group_sub2.sub_id')->get();

                        if (count($_trading_subgruop2) > 0){
                            $d_exp = collect($_trading_subgruop2)->where('sub_id',13); //Direct Expense
                            $d_inc = collect($_trading_subgruop2)->where('sub_id',15); //Direct Income
                        } else {
                            $d_exp = collect();
                            $d_inc = collect();
                        }


                            $total_credit += abs($_trading_sales) - abs($_trading_sales_return);
                            $total_credit += $_trading_closing_stock;

                            $total_debit += $_trading_opening_stock;

                            $total_debit += abs($_trading_purchase) - abs($_trading_purchase_return);

                            $total_debit += abs($d_exp->sum('total_debit') - $d_exp->sum('total_credit'));
                            $total_credit += abs($d_inc->sum('total_credit') - $d_inc->sum('total_debit'));

                            

                          

                           ?>

                            <?php
                                $trading_sales_value = abs($_trading_sales) - abs($_trading_sales_return);
                                $gross_profit_value = abs($total_credit) - abs($total_debit);
                                $direct_expense_value = abs(collect($_trading_subgruop2)->where('sub_id', 13)->sum('total_debit') - collect($_trading_subgruop2)->where('sub_id', 13)->sum('total_credit'));
                                $direct_income_value = abs(collect($_trading_subgruop2)->where('sub_id', 15)->sum('total_credit') - collect($_trading_subgruop2)->where('sub_id', 15)->sum('total_debit'));
                                $_net_profit_loss = @App\Http\Controllers\SysProfitAndLossAccountController::get_net_profit_loss($t_from_date, $t_to_date);
                                $_dashboard_net_profit = $_net_profit_loss['net-profit'];
                                $_dashboard_net_loss = $_net_profit_loss['net-loss'];
                            ?>

     <style>
           /* ================================
                   Dashboard Grade Styling
                   ================================ */

        /* ================================
               Reusable Max-Height Scrollable
               ================================ */
        .max-height {
            max-height: 300px;
            /* adjust as needed */
            overflow-y: auto;
            scrollbar-width: thin;
            /* Firefox */
            scrollbar-color: #b0b8c5 #f1f3f9;
            /* thumb + track */
        }

        /* Chrome/Edge Scrollbar */
        .max-height::-webkit-scrollbar {
            width: 6px;
        }

        .max-height::-webkit-scrollbar-track {
            background: #f1f3f9;
            border-radius: 8px;
        }

        .max-height::-webkit-scrollbar-thumb {
            background-color: #b0b8c5;
            border-radius: 8px;
        }


        /* Card Styling */
        .card {
            border: none;

            background: #ffffff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s ease-in-out;
        }



        /* Card Header */
        .card-header {
            background-color: white;
            color: #212529 !important;
            border-bottom: none
        }

        .card-header h6 {
            font-weight: 600;
            font-size: 1rem;
            margin: 0;
            letter-spacing: 0.5px;
        }

        .card-fixed-lg {
            height: 325px;
            /* large card */
            overflow-y: auto;
        }

        /* Rounded Box Metrics */
        .rounded__box {
            border: 2px solid transparent;
            border-radius: 0.75rem;
            padding: 1rem 1.5rem;
            margin: 0.5rem;
            background: rgb(222, 235, 225);
            min-width: 140px;
            text-align: center;
            transition: all 0.3s ease-in-out;
        }

        .dashboard-metric-small .rounded__box {
            padding: 0.5rem 0.75rem;
            margin: 0.2rem;
            min-width: 90px;
            max-width: 170px;
        }

        .dashboard-metric-small .text-xs {
            font-size: 0.68rem;
        }

        .dashboard-metric-small .font-card-large {
            font-size: 1rem;
        }

        .rounded__box:hover {
            background: #eef2fb;
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.08);
        }

        /* Font Sizes for Metrics */
        .font-card-large {
            font-size: 1.6rem;
            font-weight: 700;
            color: #1b1e34;
        }

        .font-card-medium {
            font-size: 1.1rem;
            font-weight: 600;
            color: #444;
        }

        /* Sales Table */
        .sales_tab {
            font-size: 0.85rem;
            color: #4e5d78;
        }

        .sales_tab thead {
            background: #f1f3f9;
            font-weight: 600;
        }

        .sales_tab td {
            padding: 0.75rem;
            vertical-align: middle;
        }

        .sales_tab tbody tr:hover {
            background: #f9fbff;
        }

        /* Table Striping */
        .table-striped2 tbody tr:nth-child(odd) {
            background-color: #f8f9fc;
        }

        /* Links inside Metrics */
        .rounded__box a {
            text-decoration: none;
            color: inherit;
        }

        .rounded__box a:hover {
            color: #0b2262;
        }
    
     </style>

    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>

    <?php try { ?>

    <aside class="left-nav col-12" id="leftSidebar">


        <div class="long-list" id="filters-long">
            <div class="d-flex  justify-content-between ">
                <!-- Left: Heading -->
                <h4 class="mb-0">Administrator Dashboard</h4>
                <input type="hidden" id="base_url" value="<?php echo e(url('/')); ?>" />


              
            </div>

        </div>

        <div class="left-nav-list">


            <div class="row mt-3">
                
                <div class="" style="display: none;">
                    <div class="row">
                        <div class="col-md-4">
                            <select class="form-control form-card-select" id="filter_date">
                                <option value="m" <?php if($_SESSION['page_date_id'] == 'm'): ?> selected <?php endif; ?>>Monthly</option>
                                <option value="pm" <?php if($_SESSION['page_date_id'] == 'pm'): ?> selected <?php endif; ?>>Previous Month
                                </option>
                                <option value="d" <?php if($_SESSION['page_date_id'] == 'd'): ?> selected <?php endif; ?>>Day</option>
                                <option value="q" <?php if($_SESSION['page_date_id'] == 'q'): ?> selected <?php endif; ?>>Quarterly</option>
                                <option value="pq" <?php if($_SESSION['page_date_id'] == 'pq'): ?> selected <?php endif; ?>>Previous Quarter
                                </option>
                                <option value="y" <?php if($_SESSION['page_date_id'] == 'y'): ?> selected <?php endif; ?>>This Year</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            
                            <select class="form-control form-card-select" id="main_filter_company">
                                <?php $com_list = App\SysHelper::get_company_names(); ?>
                                <?php $__currentLoopData = $com_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($list->id); ?>" <?php if(session('logged_session_data.company_id') == $list->id): ?> selected <?php endif; ?>>
                                        <?php echo e($list->company_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            
                        </div>
                    </div>
                </div>

                <?php if(session('logged_session_data.company_id') == 100 &&
                        (Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 15)): ?>
                    <div class="col-lg-8 mb-3">
                        <div class="card p-4 max-height">
                            <div>
                                <h2 class="page-heading mb-3">Sales Performance</h2>
                                <hr>
                            </div>
                            <div>
                                <table class="table table-nowrap table-centered mb-0 table-striped2">
                                    <thead>
                                        <tr>
                                            <th>Company</th>
                                            <th>Target GP</th>
                                            <th>Revenue</th>
                                            <th>On Process Deal</th>
                                            <th>Forecast</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(count($performance) > 0): ?>
                                            <?php $__currentLoopData = $performance; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $top): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td><a><?php echo e($top['company_name']); ?></a></td>
                                                    <td><?php echo e($top['target_gp']); ?></td>
                                                    <td><?php echo e($top['revenue']); ?></td>
                                                    <td><?php echo e($top['on_process_deal']); ?></td>
                                                    <td><?php echo e($top['forcast']); ?></td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-3"></div>
                <?php else: ?>
                    <?php if(1 == 2): ?>
                        <div class="col-lg-3 mb-4">
                            <div class="card shadow h-100">
                                <div class="card-header">
                                    <h5>Target GP</h5>
                                </div>
                                <div class="p-2">
                                    <i class="fa fa-calculator ml-3 mr-4" aria-hidden="true"
                                        style="color: #3a62d7; font-size: 40px; float: left;"></i>
                                    <h2><a href="<?php echo e(url('crm-deals-sales-report')); ?>"><?php echo e($sales_revenue[1]); ?></a></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-4">
                            <div class="card shadow h-100">
                                <div class="card-header">
                                    <h5>Revenue</h5>
                                </div>
                                <div class="p-2">
                                    <i class="fa fa-signal ml-3 mr-4" aria-hidden="true"
                                        style="color: #1cc88a; font-size: 40px; float: left;"></i>
                                    <h2><a href="<?php echo e(url('crm-deals-sales-report')); ?>"><?php echo e(@App\SysHelper::com_curr_format($trading_sales_value, 2, '.', ',')); ?></a></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-4">
                            <div class="card shadow h-100">
                                <div class="card-header">
                                    <h5>On Process Deal</h5>
                                </div>
                                <div class="p-2">
                                    <i class="fa fa-signal ml-3 mr-4" aria-hidden="true"
                                        style="color: #1ca2c8; font-size: 40px; float: left;"></i>
                                    <h2><a href="<?php echo e(url('crm-deals-sales-report')); ?>"><?php echo e($on_process); ?></a></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-4">
                            <div class="card shadow h-100">
                                <div class="card-header">
                                    <h5>Forcast</h5>
                                </div>
                                <div class="p-2">
                                    <i class="fa fa-cart-plus ml-3 mr-4" aria-hidden="true"
                                        style="color: #f6c23e; font-size: 40px; float: left;"></i>
                                    <h2><a href="<?php echo e(url('crm-deals-sales-report')); ?>"><?php echo e($forcast); ?></a></h2>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                <?php endif; ?>


                


                <div class="mb-4 <?php if(session('logged_session_data.company_id') == 0): ?> col-lg-8 <?php else: ?> col-lg-3 <?php endif; ?> ">

                    <div class="card shadow h-100">
                        <div class="card-header">
                            <h6 class="card-head ">Sales Performance</h6>
                        </div>

                        <?php if(session('logged_session_data.company_id') == 0): ?>
                            <div class="row">
                                <div class="col-lg-5">
                                    <div class="s-flex justify-content-center text-center">
                                        <div class="d-inline-block d-flex justify-content-center dashboard-metric-small">
                                            <div
                                                class="text-center mb-2 rounded__box d-flex align-items-center justify-content-center">
                                                <a href="<?php echo e(url('sales-invoice')); ?>" >
                                                    <div class="text-xs font-weight-bold text-dark  text-uppercase">
                                                        Revenue</div>
                                                    <div class="mb-0 font-weight-bold text-gray-800 font-card-large"
                                                        id="revenue"><?php echo e(@App\SysHelper::com_curr_format($trading_sales_value, 2, '.', ',')); ?></div>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="d-inline-block d-flex justify-content-center dashboard-metric-small">
                                            <div
                                                class="text-center mb-2 rounded__box d-flex align-items-center justify-content-center">
                                                <a href="<?php echo e(url('crm-deals/show?stage_id=3')); ?>">
                                                    <div class="text-xs font-weight-bold text-dark text-uppercase">Forecast
                                                    </div>
                                                    <div class="mb-0 font-weight-bold text-gray-800 font-card-large"
                                                        id="forcast"><?php echo e($sales[1]); ?></div>
                                                </a>
                                            </div>
                                        </div>
                                        Lost : <span class="mb-0 font-weight-bold text-gray-800 font-card-medium"
                                            id="lost"><?php echo e($sales[2]); ?></span><br /><br />
                                    </div>
                                </div>
                                <div class="col-lg-7" style="padding-right: 30px;">
                                    <style>
                                        .sales_tab {
                                            font-size: 80%;
                                            color: #7183b9;
                                        }

                                        .sales_tab td {
                                            text-align: right;
                                        }
                                    </style>


                                    <table class="table table-nowrap table-centered mb-0 table-striped2 sales_tab">
                                        <thead>
                                            <tr>
                                                <td style="text-align: left;">Company</td>
                                                <td>Revenue</td>
                                                <td>Forecast</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $com_list = App\SysHelper::get_company_names(); ?>
                                            <?php $__currentLoopData = $com_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php $data = App\SysHelper::get_total_sales_revenue($_SESSION["page_date_id"],$list->id); ?>
                                                <tr>
                                                    <td style="text-align: left;"><i><?php echo e($list->company_name); ?></i></td>
                                                    <td><?php echo e($data[0]); ?></td>
                                                    <td><?php echo e($data[1]); ?></td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td></td>
                                                <td><?php echo e($sales[0]); ?></td>
                                                <td><?php echo e($sales[1]); ?></td>
                                            </tr>
                                        </tfoot>
                                    </table>

                                </div>
                            </div>
                        <?php else: ?>
                            <div class="d-flex justify-content-center text-center">
                                <div class="d-inline-block d-flex justify-content-center dashboard-metric-small">
                                    <div
                                        class="text-center mb-2 rounded__box d-flex align-items-center justify-content-center">
                                        <a href="#" onclick="sales_click('revenue')">
                                            <div class="text-xs font-weight-bold text-dark text-uppercase">Revenue</div>
                                            <div class="mb-0 font-weight-bold text-gray-800 font-card-large"
                                                id="revenue"><?php echo e(@App\SysHelper::com_curr_format($trading_sales_value, 2, '.', ',')); ?></div>
                                        </a>
                                    </div>
                                </div>
                                <div class="d-inline-block d-flex justify-content-center dashboard-metric-small">
                                    <div
                                        class="text-center mb-2 rounded__box d-flex align-items-center justify-content-center">
                                        <a href="<?php echo e(url('crm-deals/show?stage_id=3')); ?>">
                                            <div class="text-xs font-weight-bold text-dark text-uppercase">Forecast</div>
                                            <div class="mb-0 font-weight-bold text-gray-800 font-card-large"
                                                id="forcast"><?php echo e(@App\SysHelper::com_curr_format($sales[1], 2, '.', ',')); ?></div>
                                        </a>
                                    </div>
                                </div>

                                <div class="d-inline-block d-flex justify-content-center dashboard-metric-small">
                                    <div
                                        class="text-center mb-2 rounded__box d-flex align-items-center justify-content-center">
                                        <a href="<?php echo e(url('crm-deals/show?stage_id=4')); ?>">
                                            <div class="text-xs font-weight-bold text-dark text-uppercase">OnProcess</div>
                                            <div class="mb-0 font-weight-bold text-gray-800 font-card-large"
                                                id="on_process"><?php echo e(@App\SysHelper::com_curr_format($on_process, 2, '.', ',')); ?></div>
                                        </a>
                                    </div>
                                </div>

                                

                            </div>

                            <div class="text-center">
                                Lost : <span class="mb-0 font-weight-bold text-gray-800 font-card-medium"
                                    id="lost"><?php echo e($sales[2]); ?></span>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
                <script>
                    function sales_click(id) {
                        var mo = $("#filter_date").val();
                        var co = $("#filter_company").val();
                        if (co == 0) {
                            return false;
                        }
                        var url = $("#base_url").val() + "/crm-deal-sales-performance/" + id + "/" + mo + "/" + co;
                        window.location.href = url;
                    }
                </script>


                <div class="col-lg-3 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-header">
                            <h6 class="card-head ">Project Performance</h6>
                            <div class="" style="display: none;">
                                <div class="row">
                                    <div class="col-md-4">
                                        <select class="form-control form-card-select" id="filter_date_project">
                                            <option value="m" <?php if($_SESSION['page_date_id'] == 'm'): ?> selected <?php endif; ?>>
                                                Monthly</option>
                                            <option value="pm" <?php if($_SESSION['page_date_id'] == 'pm'): ?> selected <?php endif; ?>>
                                                Previous Month</option>
                                            <option value="d" <?php if($_SESSION['page_date_id'] == 'd'): ?> selected <?php endif; ?>>Day
                                            </option>
                                            <option value="q" <?php if($_SESSION['page_date_id'] == 'q'): ?> selected <?php endif; ?>>
                                                Quarterly</option>
                                            <option value="pq" <?php if($_SESSION['page_date_id'] == 'pq'): ?> selected <?php endif; ?>>
                                                Previous Quarter</option>
                                            <option value="y" <?php if($_SESSION['page_date_id'] == 'y'): ?> selected <?php endif; ?>>This
                                                Year</option>
                                        </select>
                                    </div>
                                    <div class="col-md-8">
                                        <select class="form-control form-card-select" id="filter_company_project">
                                            <?php $com_list = App\SysHelper::get_company_names(); ?>
                                            <?php $__currentLoopData = $com_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($list->id); ?>"
                                                    <?php if(session('logged_session_data.company_id') == $list->id): ?> selected <?php endif; ?>>
                                                    <?php echo e($list->company_name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center  text-center">
                            <div class="d-inline-block d-flex justify-content-center dashboard-metric-small">
                                <div
                                    class="text-center mb-2 rounded__box d-flex align-items-center justify-content-center">
                                    <a href="#" onclick="project_click('project_revenue')">
                                        <div class="text-xs font-weight-bold text-dark text-uppercase">Revenue</div>
                                        <div class="mb-0 font-weight-bold text-gray-800 font-card-large"
                                            id="project_revenue"><?php echo e($project[0]); ?></div>
                                    </a>
                                </div>
                            </div>

                            <div class="d-inline-block d-flex justify-content-center dashboard-metric-small">
                                <div
                                    class="text-center mb-2 rounded__box d-flex align-items-center justify-content-center">
                                    <a href="#" onclick="project_click('project_forcast')">
                                        <div class="text-xs font-weight-bold text-dark text-uppercase">Forecast</div>
                                        <div class="mb-0 font-weight-bold text-gray-800 font-card-large"
                                            id="project_forcast"><?php echo e($project[1]); ?></div>
                                    </a>
                                </div>
                            </div>
                            <script>
                                function project_click(id) {
                                    var mo = $("#filter_date_project").val();
                                    var co = $("#filter_company_project").val();
                                    if (co == 0) {
                                        return false;
                                    }
                                    var url = $("#base_url").val() + "/crm-deal-project/" + id + "/" + mo + "/" + co;
                                    window.location.href = url;
                                }
                            </script>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-header">
                            <h6 class="card-head ">Service Performance</h6>
                            <div class="" style="display: none;">
                                <div class="row">
                                    <div class="col-md-4">
                                        <select class="form-control form-card-select" id="filter_date_service">
                                            <option value="m" <?php if($_SESSION['page_date_id'] == 'm'): ?> selected <?php endif; ?>>
                                                Monthly</option>
                                            <option value="pm" <?php if($_SESSION['page_date_id'] == 'pm'): ?> selected <?php endif; ?>>
                                                Previous Month</option>
                                            <option value="d" <?php if($_SESSION['page_date_id'] == 'd'): ?> selected <?php endif; ?>>Day
                                            </option>
                                            <option value="q" <?php if($_SESSION['page_date_id'] == 'q'): ?> selected <?php endif; ?>>
                                                Quarterly</option>
                                            <option value="pq" <?php if($_SESSION['page_date_id'] == 'pq'): ?> selected <?php endif; ?>>
                                                Previous Quarter</option>
                                            <option value="y" <?php if($_SESSION['page_date_id'] == 'y'): ?> selected <?php endif; ?>>This
                                                Year</option>
                                        </select>
                                    </div>
                                    <div class="col-md-8">
                                        <select class="form-control form-card-select" id="filter_company_service">
                                            <?php $com_list = App\SysHelper::get_company_names(); ?>
                                            <?php $__currentLoopData = $com_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($list->id); ?>"
                                                    <?php if(session('logged_session_data.company_id') == $list->id): ?> selected <?php endif; ?>>
                                                    <?php echo e($list->company_name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class=" d-flex justify-content-center text-center">
                            <div class="d-inline-block d-flex justify-content-center dashboard-metric-small">
                                <div
                                    class="text-center mb-2 rounded__box d-flex align-items-center justify-content-center">
                                    <a href="#" onclick="service_click('service_revenue')">
                                        <div class="text-xs font-weight-bold text-dark text-uppercase">Revenue</div>
                                        <div class="mb-0 font-weight-bold text-gray-800 font-card-large"
                                            id="service_revenue"><?php echo e($service[0]); ?></div>
                                    </a>
                                </div>
                            </div>

                            <div class="d-inline-block d-flex justify-content-center dashboard-metric-small">
                                <div
                                    class="text-center mb-2 rounded__box d-flex align-items-center justify-content-center">
                                    <a href="#" onclick="service_click('service_forcast')">
                                        <div class="text-xs font-weight-bold text-dark text-uppercase">Forecast</div>
                                        <div class="mb-0 font-weight-bold text-gray-800 font-card-large"
                                            id="service_forcast"><?php echo e($service[1]); ?></div>
                                    </a>
                                </div>
                            </div>
                            <script>
                                function service_click(id) {
                                    var mo = $("#filter_date_service").val();
                                    var co = $("#filter_company_service").val();
                                    if (co == 0) {
                                        return false;
                                    }
                                    var url = $("#base_url").val() + "/crm-deal-service/" + id + "/" + mo + "/" + co;
                                    window.location.href = url;
                                }
                            </script>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-header">
                            <h6 class="card-head ">AMC Performance</h6>
                            <div class="" style="display: none;">
                                <div class="row">
                                    <div class="col-md-4">
                                        <select class="form-control form-card-select" id="filter_date_amc">
                                            <option value="m" <?php if($_SESSION['page_date_id'] == 'm'): ?> selected <?php endif; ?>>
                                                Monthly</option>
                                            <option value="pm" <?php if($_SESSION['page_date_id'] == 'pm'): ?> selected <?php endif; ?>>
                                                Previous Month</option>
                                            <option value="d" <?php if($_SESSION['page_date_id'] == 'd'): ?> selected <?php endif; ?>>Day
                                            </option>
                                            <option value="q" <?php if($_SESSION['page_date_id'] == 'q'): ?> selected <?php endif; ?>>
                                                Quarterly</option>
                                            <option value="pq" <?php if($_SESSION['page_date_id'] == 'pq'): ?> selected <?php endif; ?>>
                                                Previous Quarter</option>
                                            <option value="y" <?php if($_SESSION['page_date_id'] == 'y'): ?> selected <?php endif; ?>>This
                                                Year</option>
                                        </select>
                                    </div>
                                    <div class="col-md-8">
                                        <select class="form-control form-card-select" id="filter_company_amc">
                                            <?php $com_list = App\SysHelper::get_company_names(); ?>
                                            <?php $__currentLoopData = $com_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($list->id); ?>"
                                                    <?php if(session('logged_session_data.company_id') == $list->id): ?> selected <?php endif; ?>>
                                                    <?php echo e($list->company_name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center text-center">
                            <div class="d-inline-block d-flex justify-content-center dashboard-metric-small">
                                <div
                                    class="text-center mb-2 rounded__box d-flex align-items-center justify-content-center">
                                    <a href="#" onclick="amc_click('amc_revenue')">
                                        <div class="text-xs font-weight-bold text-dark text-uppercase">Revenue</div>
                                        <div class="mb-0 font-weight-bold text-gray-800 font-card-large" id="amc_revenue">
                                            <?php echo e($amc[0]); ?></div>
                                    </a>
                                </div>
                            </div>

                            <div class="d-inline-block d-flex justify-content-center dashboard-metric-small">
                                <div
                                    class="text-center mb-2 rounded__box d-flex align-items-center justify-content-center">
                                    <a href="#" onclick="amc_click('amc_forcast')">
                                        <div class="text-xs font-weight-bold text-dark text-uppercase">Forecast</div>
                                        <div class="mb-0 font-weight-bold text-gray-800 font-card-large" id="amc_forcast">
                                            <?php echo e($amc[1]); ?></div>
                                    </a>
                                </div>
                            </div>
                            <script>
                                function amc_click(id) {
                                    var mo = $("#filter_date_amc").val();
                                    var co = $("#filter_company_amc").val();
                                    if (co == 0) {
                                        return false;
                                    }
                                    var url = $("#base_url").val() + "/crm-deal-amc/" + id + "/" + mo + "/" + co;
                                    window.location.href = url;
                                }
                            </script>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3">
                    <div class="card shadow mb-4 card-fixed-lg">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-6">
                                    <h6 class="card-head mb-0">Leads</h6>
                                </div>
                                <div class="col-6 text-right">
                                    <h6 class="card-head mb-0">Deals</h6>
                                </div>
                            </div>
                            <div class="p-4" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <select class="form-control form-card-select" id="lead_filter_date">
                                                    <option value="m" <?php if($_SESSION['page_date_id'] == 'm'): ?> selected <?php endif; ?>>
                                                        Monthly</option>
                                                    <option value="pm" <?php if($_SESSION['page_date_id'] == 'pm'): ?> selected <?php endif; ?>>
                                                        Previous Month</option>
                                                    <option value="d" <?php if($_SESSION['page_date_id'] == 'd'): ?> selected <?php endif; ?>>Day</option>
                                                    <option value="q" <?php if($_SESSION['page_date_id'] == 'q'): ?> selected <?php endif; ?>>
                                                        Quarterly</option>
                                                    <option value="pq" <?php if($_SESSION['page_date_id'] == 'pq'): ?> selected <?php endif; ?>>
                                                        Previous Quarter</option>
                                                    <option value="y" <?php if($_SESSION['page_date_id'] == 'y'): ?> selected <?php endif; ?>>This Year</option>
                                                </select>
                                            </div>
                                            <div class="col-md-12 mt-2">
                                                <select class="form-control form-card-select" id="lead_filter_company">
                                                    <?php $com_list = App\SysHelper::get_company_names(); ?>
                                                    <?php $__currentLoopData = $com_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($list->id); ?>"
                                                            <?php if(session('logged_session_data.company_id') == $list->id): ?> selected <?php endif; ?>>
                                                            <?php echo e($list->company_name); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <select class="form-control form-card-select" id="deal_filter_date">
                                                    <option value="m" <?php if($_SESSION['page_date_id'] == 'm'): ?> selected <?php endif; ?>>
                                                        Monthly</option>
                                                    <option value="pm" <?php if($_SESSION['page_date_id'] == 'pm'): ?> selected <?php endif; ?>>
                                                        Previous Month</option>
                                                    <option value="d" <?php if($_SESSION['page_date_id'] == 'd'): ?> selected <?php endif; ?>>Day</option>
                                                    <option value="q" <?php if($_SESSION['page_date_id'] == 'q'): ?> selected <?php endif; ?>>
                                                        Quarterly</option>
                                                    <option value="pq" <?php if($_SESSION['page_date_id'] == 'pq'): ?> selected <?php endif; ?>>
                                                        Previous Quarter</option>
                                                    <option value="y" <?php if($_SESSION['page_date_id'] == 'y'): ?> selected <?php endif; ?>>This Year</option>
                                                </select>
                                            </div>
                                            <div class="col-md-12 mt-2">
                                                <select class="form-control form-card-select" id="deal_filter_company">
                                                    <?php $com_list = App\SysHelper::get_company_names(); ?>
                                                    <?php $__currentLoopData = $com_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($list->id); ?>"
                                                            <?php if(session('logged_session_data.company_id') == $list->id): ?> selected <?php endif; ?>>
                                                            <?php echo e($list->company_name); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="row d-flex align-items-start">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                                    <div class="text-left small">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="d-flex align-items-center"><i class="ico icon-outline-earth me-2" style="color: #f6c23e;"></i>New</span>
                                            <span id="lead_new" class=""><?php echo e($total_leads_new); ?></span>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="d-flex align-items-center"><i class="ico icon-outline-earth me-2" style="color: #1cc88a;"></i>Qualified</span>
                                            <span id="lead_qualified" class=""><?php echo e($total_leads_qualified); ?></span>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="d-flex align-items-center"><i class="ico icon-outline-earth me-2" style="color: #f1416c;"></i>Unqualified</span>
                                            <span id="lead_unqualified" class=""><?php echo e($total_leads_unqualified); ?></span>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="d-flex align-items-center"><i class="ico icon-outline-earth me-2" style="color: #1ca2c8;"></i>Pending Response</span>
                                            <span id="lead_pending" class=""><?php echo e($total_leads_pending); ?></span>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="d-flex align-items-center"><i class="ico icon-outline-earth me-2" style="color: #6c757d;"></i>Closed</span>
                                            <span id="lead_closed" class=""><?php echo e($total_leads_closed); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-6 border-start ps-3" style="border-left: 1px solid #e9ecef;">
                                    <div class="text-left small">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="d-flex align-items-center"><i class="ico icon-outline-earth me-2" style="color: #f6c23e;"></i>Prospecting</span>
                                            <span id="deal_prospecting" class=""><?php echo e($total_deals_prospecting); ?></span>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="d-flex align-items-center"><i class="ico icon-outline-earth me-2" style="color: #1ca2c8;"></i>Quote</span>
                                            <span id="deal_quote" class=""><?php echo e($total_deals_quote); ?></span>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="d-flex align-items-center"><i class="ico icon-outline-earth me-2" style="color: #4e73df;"></i>Closure</span>
                                            <span id="deal_closure" class=""><?php echo e($total_deals_closure); ?></span>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="d-flex align-items-center"><i class="ico icon-outline-earth me-2" style="color: #1cc88a;"></i>Won</span>
                                            <span id="deal_won" class=""><?php echo e($total_deals_won); ?></span>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="d-flex align-items-center"><i class="ico icon-outline-earth me-2" style="color: #f1416c;"></i>Lost</span>
                                            <span id="deal_lost" class=""><?php echo e($total_deals_lost); ?></span>
                                        </div>
                                      
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                

                <div class="col-lg-3 mb-3">
                    <div class="card p-4 card-fixed-lg">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-head mb-0">Sales Target This Month</h6>
                            <span class="badge badge-primary">Performance</span>
                        </div>

                        <div class="max-height">
                            <?php $__empty_1 = true; $__currentLoopData = $sales_target; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $top): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php
                                    $total_sales = App\SysHelper::get_total_revenue_all_by_user([
                                        $top->user_id,
                                    ], date('Y-m-01'), date('Y-m-t'), [$top->company_id], []);
                                    $targetValue = $top->revenue_target_monthly ?: 0;
                                    $tp = $targetValue > 0 ? round(($total_sales[0] / $targetValue) * 100, 0) : 0;
                                    $tp = max(0, $tp);
                                    if ($tp > 100) {
                                        $tpcolor = '#6f42c1';
                                    } elseif ($tp >= 80) {
                                        $tpcolor = '#1B7A3F';
                                    } elseif ($tp >= 40) {
                                        $tpcolor = '#CD7815';
                                    } else {
                                        $tpcolor = '#C62828';
                                    }
                                ?>

                                <div class="mb-3 pb-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <p class="mb-1 font-weight-bold text-dark"><?php echo e(optional($top->userid)->full_name ?: 'Unknown'); ?></p>
                                            <p class="mb-0 text-muted small"><?php echo e($tp); ?>% of monthly target</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="mb-1 font-weight-bold text-dark">
                                                <?php echo e(App\SysHelper::com_curr_format($total_sales[0], 2, '.', ',')); ?> AED
                                            </p>
                                            <p class="mb-0 text-muted small">
                                                Target: <?php echo e(App\SysHelper::com_curr_format($top->revenue_target_monthly, 2, '.', ',')); ?> AED
                                            </p>
                                        </div>
                                    </div>
                                    <div class=" mt-3" style="height: 10px;">
                                        
                                         <div class="progress flex-fill" data-bar-color="<?php echo e($tpcolor); ?>" data-percentage="<?php echo e($tp); ?>%"><div class="progress-bar" style="width: <?php echo e($tp); ?>%; background-color: <?php echo e($tpcolor); ?>;"></div></div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="text-center text-muted py-4">
                                    No sales target data available for this month.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>


                   <div class="col-lg-3 mb-3">
                    <div class="card p-4 card-fixed-lg">
                       <div class="d-flex justify-content-between align-items-center">
    <h6 class="card-head mb-0">Trading Account</h6>

    <div style="width: 101px;">
        <select class="form-control form-control-sm js-example-basic-single" 
                name="filter_by" 
                id="filter_by" 
                onchange="set_filter2()">
            <option value="">-Select-</option>
            <option value="this_month" <?php if($t_filter_by=="this_month"): ?> selected <?php endif; ?>>This Month</option>
            <option value="today" <?php if($t_filter_by=="today"): ?> selected <?php endif; ?>>Today</option>
            <option value="this_week" <?php if($t_filter_by=="this_week"): ?> selected <?php endif; ?>>This Week</option>
            <option value="last_week" <?php if($t_filter_by=="last_week"): ?> selected <?php endif; ?>>Last Week</option>
            <option value="last_month" <?php if($t_filter_by=="last_month"): ?> selected <?php endif; ?>>Last Month</option>
            <option value="this_quarter" <?php if($t_filter_by=="this_quarter"): ?> selected <?php endif; ?>>This Quarter</option>
            <option value="pre_quarter" <?php if($t_filter_by=="pre_quarter"): ?> selected <?php endif; ?>>Previous Quarter</option>
            <option value="this_year" <?php if($t_filter_by=="this_year" || $t_filter_by==""): ?> selected <?php endif; ?>>This Year</option>
            <option value="last_year" <?php if($t_filter_by=="last_year"): ?> selected <?php endif; ?>>Last Year</option>
        </select>
    </div>
</div>

<hr style="margin-top:6px">
                        <div class="max-height">
                          

                             <div class="mb-2 text-center">
                             As of: <?php echo e(date('d/m/Y', strtotime($t_from_date))); ?>

                                to 
                                <?php echo e(date('d/m/Y', strtotime($t_to_date))); ?>

                                
                            </div>

                            <div class="dashboard-trading-summary" style="margin-top:24px">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong>Net Sales (YTD)</strong>
                                    <span class=" fw-bold"><?php echo e(@App\SysHelper::com_curr_format($trading_sales_value, 2, '.', ',')); ?> </span>
                                </div>
                               
                            
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong>Gross Profit</strong>
                                    <span class="fw-bold text-success"><?php echo e(@App\SysHelper::com_curr_format($gross_profit_value, 2, '.', ',')); ?> </span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong>Gross Profit %</strong>
                                    <span class="fw-bold text-success"><?php echo e($_trading_sales > 0 ? number_format(($gross_profit_value / $_trading_sales) * 100, 2) . '%' : '0%'); ?>   </span>
                                </div>
                                 <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong><?php if($_dashboard_net_profit != 0): ?> Net Profit <?php else: ?> Net Loss <?php endif; ?></strong>
                                    <span class="fw-bold <?php echo e($_dashboard_net_profit != 0 ? 'text-success' : 'text-danger'); ?>">
                                        <?php echo e(@App\SysHelper::com_curr_format($_dashboard_net_profit != 0 ? $_dashboard_net_profit : $_dashboard_net_loss, 2, '.', ',')); ?>

                                    </span>
                                </div>

                                 <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong>Net Profit %</strong>
                                    <span class="fw-bold text-success"><?php echo e($_trading_sales > 0 ? number_format(($_dashboard_net_profit   / $trading_sales_value) * 100, 2) . '%' : '0%'); ?>   </span>
                                </div>

                                
                                 <!-- <div class="d-flex justify-content-between align-items-center">
                                    <strong>COGS</strong>
                                    <span class="fw-bold"> <?php echo e(@App\SysHelper::com_curr_format($cogs, 2, '.', ',')); ?></span>
                                </div> -->
                            </div>

                           
                    </div>
                </div>
                   </div>


                <div class="col-md-6">
                    <div class="card shadow p-3  card-fixed-lg">
                        <div class="card-header d-flex justify-content-between align-items-center"
                            style="background:white">
                            <h4 class="header-title m-0 text-dark">Payment Pending</h4>
                            <a href="<?php echo e(url('crm-deal-track-list/pendingpayments')); ?>" class=" btn-small">View All</a>
                        </div>
                        <div class="card-body pt-0  max-height">
                            <div class="table-responsive">
                                <table class="table table-nowrap table-hover table-centered mb-0"
                                    style="table-layout: fixed;width:100%" id="long-list">
                                    <thead>
                                        <tr>
                                            <th>Deal</th>
                                            <th>Company</th>
                                            <th>Owner</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(count($pending_payments) > 0): ?>
                                            <?php $__currentLoopData = $pending_payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $top): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td><a href="<?php echo e(url('crm-deal-track/' . $top->deal_id . '/view')); ?>"
                                                            title="View Deal Track"
                                                            class="text-dark"><?php echo e($top->deal_id); ?></a></td>
                                                    <td>
                                                        <?php echo e($top->customername->name); ?>


                                                    </td>
                                                    <td><?php echo e($top->ownername->full_name); ?></td>
                                                    <td><?php echo e(date('d/m/Y', strtotime($top->date))); ?></td>
                                                    <td> <span
                                                            class="badge bg-secondary rejected py-1 px-2"><?php echo App\SysHelper::get_deal_status_log(
                                                                $top->accounts,
                                                                $top->sales,
                                                                $top->purchease,
                                                                $top->invoice,
                                                                $top->delivery,
                                                                $top->receivables,
                                                            ); ?></span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div> <!-- end table-responsive-->

                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div>

                <div class="col-md-6">
                    <div class="card p-3 shadow  card-fixed-lg">
                        <div class="card-header d-flex justify-content-between align-items-center"
                            style="background:white">
                            <h4 class="header-title m-0 text-dark">Order In Process</h4>
                            <a href="<?php echo e(url('crm-deal-track-list/orderinprocess')); ?>" class=" btn-small">View All</a>
                        </div>
                        <div class="card-body pt-0  max-height">
                            <div class="table-responsive">
                                <table class="table table-nowrap table-hover table-centered mb-0 table-striped2">
                                    <thead>
                                        <tr>
                                            <th>Deal</th>
                                            <th>Company</th>
                                            <th>Owner</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(count($order_in_process) > 0): ?>
                                            <?php $__currentLoopData = $order_in_process; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $top): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td><a href="<?php echo e(url('crm-deal-track/' . $top->deal_id . '/view')); ?>"
                                                            title="View Deal Track"
                                                            class="text-dark"><?php echo e($top->deal_id); ?></a></td>

                                                    <td></td>
                                                    <td></td>
                                                    <td><?php echo e(date('d/m/Y', strtotime($top->date))); ?></td>
                                                    <td> <span
                                                            class="rejected badge bg-secondary py-1 px-2"><?php echo App\SysHelper::get_deal_status_log(
                                                                $top->accounts,
                                                                $top->sales,
                                                                $top->purchease,
                                                                $top->invoice,
                                                                $top->delivery,
                                                                $top->receivables,
                                                            ); ?></span>
                                                    </td>

                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div> <!-- end table-responsive-->

                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div>






                <div class="col-md-4 mt-3" style="display: none;">

                    <div class="card shadow  card-fixed-lg">
                        <div class="card-header">
                            <div class="">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h6 class="card-head ">Ecommerce Sale</h6>
                                    </div>
                                    <div class="col-md-4">
                                        <select class="form-control form-card-select" id="ecommerce_filter_date">
                                            <option value="m">Monthly</option>
                                            <option value="d">Day</option>
                                            <option value="q">Quarterly</option>
                                            <option value="y">Yearly</option>
                                        </select>
                                        <script>
                                            $(document).on("change", "#ecommerce_filter_date", function() {
                                                var id = $("#ecommerce_filter_date").val();
                                                $("#loading_bg").css("display", "block");
                                                if (id == 'm') {
                                                    $("#spanD").css("display", "none");
                                                    $("#spanM").css("display", "block");
                                                    $("#spanY").css("display", "none");
                                                    $("#spanQ").css("display", "none");
                                                }
                                                if (id == 'd') {
                                                    $("#spanD").css("display", "block");
                                                    $("#spanM").css("display", "none");
                                                    $("#spanY").css("display", "none");
                                                    $("#spanQ").css("display", "none");
                                                }
                                                if (id == 'q') {
                                                    $("#spanD").css("display", "none");
                                                    $("#spanM").css("display", "none");
                                                    $("#spanY").css("display", "none");
                                                    $("#spanQ").css("display", "block");
                                                }
                                                if (id == 'y') {
                                                    $("#spanD").css("display", "none");
                                                    $("#spanM").css("display", "none");
                                                    $("#spanY").css("display", "block");
                                                    $("#spanQ").css("display", "none");
                                                }
                                                $("#loading_bg").css("display", "none");
                                            });
                                        </script>
                                    </div>
                                    
                                </div>
                                <div class="row">
                                    <div class="col-md-12 mt-4">
                                        <div class="d-flex justify-content-center text-center">
                                            <div class="d-inline-block d-flex justify-content-center dashboard-metric-small">
                                                <div
                                                    class="text-center mb-2 rounded__box d-flex align-items-center justify-content-center">
                                                    <?php $abc = App\SysHelper::get_total_ecommerce_sale(); ?>
                                                    <div class="mb-0 font-weight-bold text-gray-800 font-card-large">
                                                        <span id="spanD"
                                                            style="display: none;"><?php echo e($abc[0]); ?></span>
                                                        <span id="spanM"><?php echo e($abc[1]); ?></span>
                                                        <span id="spanY"
                                                            style="display: none;"><?php echo e($abc[2]); ?></span>
                                                        <span id="spanQ"
                                                            style="display: none;"><?php echo e($abc[3]); ?></span>
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

                <div class="col-md-6 mt-4">
                    <div class="card p-3  card-fixed-lg shadow">
                        <div class="card-header d-flex justify-content-between align-items-center"
                            style="background:white">
                            <h4 class="header-title m-0 text-dark">Payment Reminder</h4>
                            <a href="<?php echo e(url('crm-deal-track-list/paymentreminder')); ?>" class=" btn-small">View All</a>
                        </div>
                        <div class="card-body pt-0  max-height">
                            <div class="table-responsive table-bordered">
                                <table class="table table-nowrap table-centered mb-0 table-striped2 table-hover"
                                    style="table-layout: fixed;width:100%" id="long-list">
                                    <thead>
                                        <tr>
                                            <th>Deal</th>
                                            <th>Company</th>
                                            <th>Owner</th>
                                            <th>Reminder</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(count($payment_reminder) > 0): ?>
                                            <?php $__currentLoopData = $payment_reminder; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $top): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr <?php if(date('d/m/Y', strtotime($top->reminder_date)) == date('d/m/Y')): ?> class="text-danger" <?php endif; ?>>
                                                    <td><a href="<?php echo e(url('crm-deal-track-approval/' . $top->id . '')); ?>"
                                                            title="View Deal Track"
                                                            class="text-dark"><?php echo e($top->deal_id); ?></a></td>
                                                    <td>
                                                        <?php echo e($top->customername->name); ?>


                                                    </td>
                                                    <td><?php echo e($top->ownername->full_name); ?></td>
                                                    <td><?php echo e(date('d/m/Y h:i:A', strtotime($top->reminder_date))); ?></td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div> <!-- end table-responsive-->

                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div>

                <div class="col-md-6 mt-4">
                    <div class="card p-3   card-fixed-lg shadow">
                        <div class="card-header d-flex justify-content-between align-items-center"
                            style="background:white">
                            <h4 class="header-title m-0 text-dark">Pending Payment (After Reminder)</h4>
                            <a href="<?php echo e(url('crm-deal-track-list/paymentpendingafterreminder')); ?>"
                                class=" btn-small">View All</a>
                        </div>
                        <div class="card-body pt-0  max-height">
                            <div class="table-responsive table-bordered">
                                <table class="table table-nowrap table-centered mb-0 table-striped2 table-hover"
                                    style="table-layout: fixed;width:100%" id="long-list">
                                    <thead>
                                        <tr>
                                            <th>Deal</th>
                                            <th>Company</th>
                                            <th>Owner</th>
                                            <th>Reminder</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(count($payment_pending) > 0): ?>
                                            <?php $__currentLoopData = $payment_pending; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $top): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td><a href="<?php echo e(url('crm-deal-track-approval/' . $top->id . '')); ?>"
                                                            title="View Deal Track"
                                                            class="text-dark"><?php echo e($top->deal_id); ?></a></td>
                                                    <td>
                                                        <?php echo e($top->customername->name); ?>

                                                    </td>
                                                    <td><?php echo e($top->ownername->full_name); ?></td>
                                                    <td><?php echo e(date('d/m/Y h:i:A', strtotime($top->reminder_date))); ?></td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div> <!-- end table-responsive-->

                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div>
                <div class="col-md-6 mt-4">
                    <div class="card  card-fixed-lg shadow p-3">
                        <div class="card-header d-flex justify-content-between align-items-center"
                            style="background:white">
                            <h4 class="header-title m-0 text-dark">Partial Invoice</h4>
                        </div>
                        <div class="card-body pt-0  max-height">
                            <div class="table-responsive table-bordered">
                                <table class="table table-nowrap table-centered mb-0 table-striped2 table-hover"
                                    style="table-layout: fixed;width:100%" id="long-list">
                                    <thead>
                                        <tr>
                                            <th>Deal</th>
                                            <th>Company</th>
                                            <th>Owner</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(count($partial_invoice) > 0): ?>
                                            <?php $__currentLoopData = $partial_invoice; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $top): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td><a href="<?php echo e(url('crm-deal-track-approval/' . $top->id . '')); ?>"
                                                            title="View Deal Track"
                                                            class="text-dark"><?php echo e($top->deal_id); ?></a></td>
                                                    <td>
                                                        <?php echo e($top->customername->name); ?>


                                                    </td>
                                                    <td><?php echo e($top->ownername->full_name); ?></td>
                                                    <td><?php echo e(date('d/m/Y', strtotime($top->date))); ?></td>
                                                    <td> <span class="badge bg-warning py-1 px-2">Partial</span></td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div> <!-- end table-responsive-->

                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div>
                <div class="col-md-6 mt-4">
                    <div class="card card-fixed-lg shadow p-3">
                        <div class="card-header d-flex justify-content-between align-items-center"
                            style="background:white">
                            <h4 class="header-title m-0 text-dark">Deals Overdue After Closing Date</h4>
                        </div>
                        <div class="card-body pt-0  max-height">
                            <div class="table-responsive table-bordered">
                                <table class="table  table-centered mb-0 table-striped2 table-hover"
                                    style="table-layout: fixed;width:100%" id="long-list">
                                    <thead>
                                        <tr>
                                            <th>Deal</th>
                                            <th>Company</th>
                                            <th>Owner</th>
                                            <th>Date</th>
                                            <th>Stage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(count($dealsbyclosedate) > 0): ?>
                                            <?php $__currentLoopData = $dealsbyclosedate; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $top): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td><a href="<?php echo e(url('crm-deals/' . $top->id . '/view')); ?>"
                                                            title="View Deal Track"
                                                            class="text-dark"><?php echo e($top->id); ?></a></td>
                                                    <td>
                                                        <?php echo e($top->deal_name); ?>


                                                    </td>
                                                    <td><?php echo e($top->ownername->full_name); ?></td>
                                                    <td><?php echo e(date('d/m/Y', strtotime($top->estimated_close_date))); ?></td>
                                                    <td>
                                                        <?php if($top->stage == 1): ?>
                                                            <span class="badge bg-warning py-1 px-2">Prospecting</span>
                                                        <?php elseif($top->stage == 2): ?>
                                                            <span class="badge bg-warning py-1 px-2">Quote</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-warning py-1 px-2">Closure</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div> <!-- end table-responsive-->

                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div>

                

                

            </div>


        </div>
    </aside>




    <?php } catch (\Exception $e) { ?> <?php echo e($e); ?> <?php  } ?>

    <script>
        $(document).on("change", "#main_filter_company", function() {
            var company = $("#main_filter_company").val();
            var date = $("#main_filter_date").val();
            change_all(company, date);
            $("#loading_bg").css("display", "block");
            location.reload();
        });

        $(document).on("change", "#main_filter_date", function() {
            var company = $("#main_filter_company").val();
            var date = $("#main_filter_date").val();
            change_all(company, date);
            $("#loading_bg").css("display", "block");
            location.reload();
        });

        function change_all(company, date) {
            $("#filter_company").val(company);
            $("#filter_date").val(date);
            get_data(company, date);

            $("#lead_filter_company").val(company);
            $("#lead_filter_date").val(date);
            get_lead_data(company, date);

            $("#deal_filter_company").val(company);
            $("#deal_filter_date").val(date);
            get_deal_data(company, date);

            $("#filter_company_service").val(company);
            $("#filter_date_service").val(date);
            get_service_data(company, date);

            $("#filter_company_amc").val(company);
            $("#filter_date_amc").val(date);
            get_amc_data(company, date);

            $("#filter_company_project").val(company);
            $("#filter_date_project").val(date);
            get_project_data(company, date);

            $("#loading_bg").css("display", "block");

        }

        function set_filter2() {
            var filter = $("#filter_by").val();
            var url = "<?php echo e(url('crm-dashboard')); ?>";
            if (filter && filter !== "") {
                url += "?filter_by=" + encodeURIComponent(filter);
            }
            window.location.href = url;
        }

        $(document).on("change", "#filter_company", function() {
            var company = $("#filter_company").val();
            var date = $("#filter_date").val();
            get_data(company, date);
        });
        $(document).on("change", "#filter_date", function() {
            var company = $("#filter_company").val();
            var date = $("#filter_date").val();
            get_data(company, date);
        });

        function get_data(company, date) {
            $("#loading_bg").css("display", "block");
            var action = "<?php echo e(URL::to('crm-dashboard-sales-filter')); ?>";
            $.ajax({
                url: action,
                type: "GET",
                data: {
                    _token: '<?php echo e(csrf_token()); ?>',
                    company: company,
                    date: date,
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
                        $("#revenue").html(dataResult['data'][0]);
                        $("#forcast").html(dataResult['data'][1]);
                        $("#lost").html(dataResult['data'][2]);
                    } else {
                        $("#revenue").html("0");
                        $("#forcast").html("0");
                        $("#lost").html("0");
                    }
                    $("#loading_bg").css("display", "none");
                }
            });
        }

        $(document).on("change", "#lead_filter_company", function() {
            var company = $("#lead_filter_company").val();
            var date = $("#lead_filter_date").val();
            get_lead_data(company, date);
        });
        $(document).on("change", "#lead_filter_date", function() {
            var company = $("#lead_filter_company").val();
            var date = $("#lead_filter_date").val();
            get_lead_data(company, date);
        });

        function get_lead_data(company, date) {
            $("#loading_bg").css("display", "block");
            var action = "<?php echo e(URL::to('crm-dashboard-lead-filter')); ?>";
            $.ajax({
                url: action,
                type: "GET",
                data: {
                    _token: '<?php echo e(csrf_token()); ?>',
                    company: company,
                    date: date,
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
                        $("#lead_new").html(dataResult['data'][0]);
                        $("#lead_qualified").html(dataResult['data'][1]);
                        $("#lead_unqualified").html(dataResult['data'][2]);
                        $("#lead_pending").html(dataResult['data'][3]);
                        $("#lead_closed").html(dataResult['data'][4]);
                        $("#lead_project").html(dataResult['data'][5]);
                        $("#lead_channel").html(dataResult['data'][6]);
                        $("#lead_corporate").html(dataResult['data'][7]);
                    } else {
                        $("#lead_new").html("0");
                        $("#lead_qualified").html("0");
                        $("#lead_unqualified").html("0");
                        $("#lead_pending").html("0");
                        $("#lead_closed").html("0");
                        $("#lead_project").html("0");
                        $("#lead_channel").html("0");
                        $("#lead_corporate").html("0");
                    }
                    $("#loading_bg").css("display", "none");
                }
            });
        }

        $(document).on("change", "#deal_filter_company", function() {
            var company = $("#deal_filter_company").val();
            var date = $("#deal_filter_date").val();
            get_deal_data(company, date);
        });
        $(document).on("change", "#deal_filter_date", function() {
            var company = $("#deal_filter_company").val();
            var date = $("#deal_filter_date").val();
            get_deal_data(company, date);
        });

        function get_deal_data(company, date) {
            $("#loading_bg").css("display", "block");
            var action = "<?php echo e(URL::to('crm-dashboard-deal-filter')); ?>";
            $.ajax({
                url: action,
                type: "GET",
                data: {
                    _token: '<?php echo e(csrf_token()); ?>',
                    company: company,
                    date: date,
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
                        $("#deal_prospecting").html(dataResult['data'][0]);
                        $("#deal_quote").html(dataResult['data'][1]);
                        $("#deal_closure").html(dataResult['data'][2]);
                        $("#deal_won").html(dataResult['data'][3]);
                        $("#deal_lost").html(dataResult['data'][4]);
                        $("#deal_project").html(dataResult['data'][5]);
                        $("#deal_channel").html(dataResult['data'][6]);
                        $("#deal_corporate").html(dataResult['data'][7]);
                        chart_deal(dataResult['data'][0], dataResult['data'][1], dataResult['data'][2],
                            dataResult['data'][3], dataResult['data'][4], dataResult['data'][5], dataResult[
                                'data'][6], dataResult['data'][7]);
                    } else {
                        $("#deal_prospecting").html("0");
                        $("#deal_quote").html("0");
                        $("#deal_closure").html("0");
                        $("#deal_won").html("0");
                        $("#deal_lost").html("0");
                        $("#deal_project").html("0");
                        $("#deal_channel").html("0");
                        $("#deal_corporate").html("0");
                    }
                    $("#loading_bg").css("display", "none");
                }
            });
        }

        function chart_lead(a, b, c, d, e, f) {
            var ctx = document.getElementById("myPieChart");
            var myPieChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ["New", "Qualified", "Unqualified", "Project", "Channel", "Corporate"],
                    datasets: [{
                        data: [a, b, c, d, e, f],
                        backgroundColor: ['#36b9cc', '#1cc88a', '#f1416c', '#4e51df', '#704edf', '#4e73df'],
                        hoverBackgroundColor: ['#36b9cc', '#1cc88a', '#f1416c', '#4e51df', '#704edf',
                            '#4e73df'
                        ],
                        hoverBorderColor: "rgba(234, 236, 244, 1)",
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    tooltips: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyFontColor: "#858796",
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        caretPadding: 10,
                    },
                    legend: {
                        display: false
                    },
                    cutoutPercentage: 80,
                },
            });
        }

        function chart_deal(a, b, c, d, e, f, g) {
            var ctx = document.getElementById("myPieChart2");
            var myPieChart2 = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ["Prospecting", "Quote", "Closure", "Won", "Lost", "Project", "Channel", "Corporate"],
                    datasets: [{
                        data: [a, b, c, d, e, f, g],
                        backgroundColor: ['#f6c23e', '#1cc2c8', '#1ca2c8', '#1cc88a', '#f1416c', '#4e51df',
                            '#704edf', '#4e73df'
                        ],
                        hoverBackgroundColor: ['#f6c23e', '#1cc2c8', '#1ca2c8', '#1cc88a', '#f1416c',
                            '#4e51df', '#704edf', '#4e73df'
                        ],
                        hoverBorderColor: "rgba(234, 236, 244, 1)",
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    tooltips: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyFontColor: "#858796",
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        caretPadding: 10,
                    },
                    legend: {
                        display: false
                    },
                    cutoutPercentage: 80,
                },
            });
        }


        $(document).on("change", "#filter_company_service", function() {
            var company = $("#filter_company_service").val();
            var date = $("#filter_date_service").val();
            get_service_data(company, date);
        });
        $(document).on("change", "#filter_date_service", function() {
            var company = $("#filter_company_service").val();
            var date = $("#filter_date_service").val();
            get_service_data(company, date);
        });

        function get_service_data(company, date) {
            $("#loading_bg").css("display", "block");
            var action = "<?php echo e(URL::to('crm-dashboard-service-filter')); ?>";
            $.ajax({
                url: action,
                type: "GET",
                data: {
                    _token: '<?php echo e(csrf_token()); ?>',
                    company: company,
                    date: date,
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
                        $("#service_revenue").html(dataResult['data'][0]);
                        $("#service_forcast").html(dataResult['data'][1]);
                    } else {
                        $("#service_revenue").html("0");
                        $("#service_forcast").html("0");
                    }
                    $("#loading_bg").css("display", "none");
                }
            });
        }
        $(document).on("change", "#filter_company_amc", function() {
            var company = $("#filter_company_amc").val();
            var date = $("#filter_date_amc").val();
            get_amc_data(company, date);
        });
        $(document).on("change", "#filter_date_amc", function() {
            var company = $("#filter_company_amc").val();
            var date = $("#filter_date_amc").val();
            get_amc_data(company, date);
        });

        function get_amc_data(company, date) {
            $("#loading_bg").css("display", "block");
            var action = "<?php echo e(URL::to('crm-dashboard-amc-filter')); ?>";
            $.ajax({
                url: action,
                type: "GET",
                data: {
                    _token: '<?php echo e(csrf_token()); ?>',
                    company: company,
                    date: date,
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
                        $("#amc_revenue").html(dataResult['data'][0]);
                        $("#amc_forcast").html(dataResult['data'][1]);
                    } else {
                        $("#amc_revenue").html("0");
                        $("#amc_forcast").html("0");
                    }
                    $("#loading_bg").css("display", "none");
                }
            });
        }
        $(document).on("change", "#filter_company_project", function() {
            var company = $("#filter_company_project").val();
            var date = $("#filter_date_project").val();
            get_project_data(company, date);
        });
        $(document).on("change", "#filter_date_project", function() {
            var company = $("#filter_company_project").val();
            var date = $("#filter_date_project").val();
            get_project_data(company, date);
        });

        function get_project_data(company, date) {
            $("#loading_bg").css("display", "block");
            var action = "<?php echo e(URL::to('crm-dashboard-project-filter')); ?>";
            $.ajax({
                url: action,
                type: "GET",
                data: {
                    _token: '<?php echo e(csrf_token()); ?>',
                    company: company,
                    date: date,
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
                        $("#project_revenue").html(dataResult['data'][0]);
                        $("#project_forcast").html(dataResult['data'][1]);
                    } else {
                        $("#project_revenue").html("0");
                        $("#project_forcast").html("0");
                    }
                    $("#loading_bg").css("display", "none");
                }
            });
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backEnd.newmasterpage', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
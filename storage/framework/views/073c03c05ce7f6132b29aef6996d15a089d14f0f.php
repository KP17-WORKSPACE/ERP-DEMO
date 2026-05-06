<nav class="main-nav sidebar-new">
    <div class="toggle-nav"></div>
    <ul class="nav-list sidenav-list">
        <style>
            .sidenav-list .nav-item{
                text-align: left

            }
        </style>
        <li class="nav-item <?php echo e(@App\SysHelper::isMenuOpen(['crm-dashboard'], 'active show-subnav')); ?>">
            <a href="<?php echo e(url('/crm-dashboard')); ?>" class="nav-link">
                <!-- <i class="ico icon-outline-widget-6"></i> -->
                <img src="<?php echo e(asset('public/design')); ?>/assets/images/icons/dashboard.png" height="24px" title="Dashboard">
                <span class="nav-text">Dashboard</span>
            </a>
        </li>


         
        <?php $crm = $permissions->wherein('module_link_id', [1, 2, 3, 4, 60]); ?>
        <?php if(count($crm->where('is_read', 1)) > 0 || Auth::user()->role_id == 1): ?>
            <li
                class="nav-item <?php echo e(@App\SysHelper::isMenuOpen(['chartofaccounts', 'chartofaccounts-opening-balance', 'journalvoucher', 'cashbook', 'bankbook', 'stl-report','accountgroupsub-add','accountgroupsub2-add','chartofaccounts-add','chartofaccounts-add-sub','stl-supplier-report','chequebook','book-close','book-close-doc-number'], 'active show-subnav')); ?>">
                <div class="sub-menu-nav" data-subnav="subnavAccounts">
                    <!-- <i class="ico icon-outline-calculator"></i> -->
                    <img src="<?php echo e(asset('public/design')); ?>/assets/images/icons/accounts.png" height="24px"
                        title="Accounts">
                    <span class="nav-text">Accounts</span>
                </div>
                <div class="subnav-menu" id="subnavAccounts">
                    <?php if(count($crm->where('is_read', 1)->where('module_link_id', 1)) > 0 || Auth::user()->role_id == 1): ?>
                        <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('chartofaccounts')); ?> <?php echo e(@App\SysHelper::isActiveRoute('accountgroupsub-add')); ?> <?php echo e(@App\SysHelper::isActiveRoute('accountgroupsub2-add')); ?> <?php echo e(@App\SysHelper::isActiveRoute('chartofaccounts-add')); ?> <?php echo e(@App\SysHelper::isActiveRoute('chartofaccounts-add-sub')); ?>">
                            <a href="<?php echo e(url('chartofaccounts')); ?>" class="sub-nav-link ">Chart of Accounts</a>
                        </div>

                        <div
                            class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('chartofaccounts-opening-balance')); ?>">
                            <a href="<?php echo e(url('chartofaccounts-opening-balance')); ?>" class="sub-nav-link ">Opening
                                Balance</a>
                        </div>
                    <?php endif; ?>

                    <?php if(count($crm->where('is_read', 1)->where('module_link_id', 2)) > 0 || Auth::user()->role_id == 1): ?>
                        <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('journalvoucher')); ?>">
                            <a href="<?php echo e(url('journalvoucher')); ?>" class="sub-nav-link">Journal Voucher</a>
                        </div>
                    <?php endif; ?>
                    <?php if(count($crm->where('is_read', 1)->where('module_link_id', 3)) > 0 || Auth::user()->role_id == 1): ?>
                        <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('cashbook')); ?>">
                            <a href="<?php echo e(url('cashbook')); ?>" class="sub-nav-link ">Cash Book</a>
                        </div>
                    <?php endif; ?>
                    <?php if(count($crm->where('is_read', 1)->where('module_link_id', 4)) > 0 || Auth::user()->role_id == 1): ?>
                        <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('bankbook')); ?>">
                            <a href="<?php echo e(url('bankbook')); ?>" class="sub-nav-link ">Bank Book</a>
                        </div>
                    <?php endif; ?>
                    <?php if(count($crm->where('is_read', 1)->where('module_link_id', 4)) > 0 || Auth::user()->role_id == 1): ?>
                        <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('chequebook')); ?>">
                            <a href="<?php echo e(url('chequebook')); ?>" class="sub-nav-link">Cheque Book</a>
                        </div>
                    <?php endif; ?>
                    <?php if(count($crm->where('is_read', 1)->where('module_link_id', 60)) > 0 || Auth::user()->role_id == 1): ?>
                        <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('stl-report')); ?>  <?php echo e(@App\SysHelper::isActiveRoute('stl-supplier-report')); ?>">
                            <a href="<?php echo e(url('stl-report')); ?>" class="sub-nav-link ">STL Report</a>
                        </div>
                    <?php endif; ?>


<!--                    
                    <?php if(count($crm->where('is_read',1)->where('module_link_id',68)) > 0 ||  Auth::user()->role_id == 1): ?>
                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('book-close')); ?>">
                        <a href="<?php echo e(url('book-close')); ?>" class="sub-nav-link"><?php echo app('translator')->getFromJson('Book Closed'); ?></a>
                    </div>
                    
                    <?php endif; ?>
                    <?php if(count($crm->where('is_read',1)->where('module_link_id',69)) > 0 ||  Auth::user()->role_id == 1): ?>
                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('book-close-doc-number')); ?>">
                        <a href="<?php echo e(url('book-close-doc-number')); ?>" class="sub-nav-link"><?php echo app('translator')->getFromJson('Book Close Doc No'); ?></a>
                    </div>
                    <?php endif; ?> -->
                </div>
            </li>
        <?php endif; ?>
        

        
        
        <?php $crm = $permissions->wherein('module_link_id', [5, 6, 7, 8, 51]); ?>
        <?php if(count($crm->where('is_read', 1)) > 0 || Auth::user()->role_id == 1): ?>
            <li
                class="nav-item <?php echo e(@App\SysHelper::isMenuOpen(['crm-leads/show', 'crm-deals', 'crm-deal-track-approval-list', 'crm-deal-track-status', 'crm-deals-sales-report-company', 'crm-deals-brand-sales-report-new','crm-deals-sales-report','crm-deals-sales-report-list'], 'active show-subnav')); ?>">
                <div class="sub-menu-nav" data-subnav="subnavCRM">
                    <!-- <i class="ico icon-outline-calculator"></i> -->
                    <img src="<?php echo e(asset('public/design')); ?>/assets/images/icons/crm.png" height="24px" title="CRM">
                    <span class="nav-text">CRM</span>
                </div>
                <div class="subnav-menu" id="subnavCRM">
                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('crm-leads')); ?>">
                        <?php if(count($crm->where('is_read', 1)->where('module_link_id', 5)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('crm-leads/show')); ?>" class="sub-nav-link">Leads</a>
                        <?php endif; ?>
                    </div>
                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('crm-deals')); ?>">
                        <?php if(count($crm->where('is_read', 1)->where('module_link_id', 6)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('crm-deals/show')); ?>" class="sub-nav-link">Deals</a>
                        <?php endif; ?>
                    </div>
                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('crm-deal-track-approval-list')); ?>">
                        <?php if(count($crm->where('is_read', 1)->where('module_link_id', 7)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('crm-deal-track-approval-list')); ?>" class="sub-nav-link">Deals Track</a>
                        <?php endif; ?>
                    </div>
                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('crm-deal-track-status')); ?>">
                        <?php if(count($crm->where('is_read', 1)->where('module_link_id', 8)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('crm-deal-track-status')); ?>" class="sub-nav-link">Deals Track Status</a>
                        <?php endif; ?>
                    </div>

                    <?php if(count($crm->where('is_read', 1)->where('module_link_id', 51)) > 0 || Auth::user()->role_id == 1): ?>
                        <div
                            class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('crm-deals-sales-report-company')); ?> <?php echo e(@App\SysHelper::isActiveRoute('crm-deals-sales-report')); ?> <?php echo e(@App\SysHelper::isActiveRoute('crm-deals-sales-report-list')); ?>">
                            <a href="<?php echo e(url('crm-deals-sales-report-company')); ?>" class="sub-nav-link">CRM Sales Report</a>
                        </div>
                        <div
                            class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('crm-deals-brand-sales-report-new')); ?>">
                            <a href="<?php echo e(url('crm-deals-brand-sales-report-new')); ?>" class="sub-nav-link">Brand Sales
                                Report</a>
                        </div>
                    <?php endif; ?>

                </div>
            </li>
        <?php endif; ?>
        

       

          
        <?php $crm = $permissions->wherein('module_link_id', [9, 10, 11, 12, 13, 14, 15, 65]); ?>
        <?php if(count($crm->where('is_read', 1)) > 0 || Auth::user()->role_id == 1): ?>
            <li
                class="nav-item <?php echo e(@App\SysHelper::isMenuOpen(['suppliers', 'purchase-order', 'goods-receipt-note-list', 'purchase-invoice', 'purchase-return', 'payment', 'payables-outstanding', 'pi-adjustment-report', 'supplier-ageing-report'], 'active show-subnav')); ?>">
                <div class="sub-menu-nav" data-subnav="subnavPurchase">
                    <!-- <i class="ico icon-bold-cart-large-4"></i> -->
                    <img src="<?php echo e(asset('public/design')); ?>/assets/images/icons/purchase.png" height="24px"
                        title="Purchase">
                    <span class="nav-text">Purchase</span>
                </div>
                <div class="subnav-menu" id="subnavPurchase">
                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('suppliers')); ?>">
                        <?php if(count($crm->where('is_read', 1)->where('module_link_id', 9)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('suppliers')); ?>" class="sub-nav-link">Supplier Register</a>
                        <?php endif; ?>
                    </div>
                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('purchase-order')); ?>">
                        <?php if(count($crm->where('is_read', 1)->where('module_link_id', 10)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('purchase-order')); ?>" class="sub-nav-link">Purchase Order</a>
                        <?php endif; ?>
                    </div>
                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('goods-receipt-note-list')); ?>">
                        <?php if(count($crm->where('is_read', 1)->where('module_link_id', 11)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('goods-receipt-note-list')); ?>" class="sub-nav-link">Goods Receipt
                                Note</a>
                        <?php endif; ?>
                    </div>
                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('purchase-invoice')); ?>">
                        <?php if(count($crm->where('is_read', 1)->where('module_link_id', 12)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('purchase-invoice')); ?>" class="sub-nav-link">Purchase Invoice</a>
                        <?php endif; ?>
                    </div>
                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('purchase-return')); ?>">
                        <?php if(count($crm->where('is_read', 1)->where('module_link_id', 13)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('purchase-return')); ?>" class="sub-nav-link">Purchase Return</a>
                        <?php endif; ?>
                    </div>
                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('payment')); ?>">
                        <?php if(count($crm->where('is_read', 1)->where('module_link_id', 14)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('payment')); ?>" class="sub-nav-link">Payments</a>
                        <?php endif; ?>
                    </div>
                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('payables-outstanding')); ?> <?php echo e(@App\SysHelper::isActiveRoute('supplier-ageing-report')); ?>">
                        <?php if(count($crm->where('is_read', 1)->where('module_link_id', 15)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('payables-outstanding')); ?>" class="sub-nav-link">Payables Outstanding</a>
                        <?php endif; ?>
                    </div>
                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('pi-adjustment-report')); ?>">
                        <?php if(count($crm->where('is_read', 1)->where('module_link_id', 65)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('pi-adjustment-report')); ?>" class="sub-nav-link">PI Adjustment Report</a>
                        <?php endif; ?>
                    </div>
                </div>
            </li>
        <?php endif; ?>
        

        
        <?php $crm = $permissions->wherein('module_link_id', [16, 17, 18, 19, 20, 21, 22, 23, 50, 64]); ?>
        <?php if(count($crm->where('is_read', 1)) > 0 || Auth::user()->role_id == 1): ?>
            <li
                class="nav-item <?php echo e(@App\SysHelper::isMenuOpen(['customers', 'quotations', 'proforma-invoice', 'sales-invoice', 'delivery-note', 'sales-return', 'receipt', 'receivable-outstanding', 'si-adjustment-report', 'clearance','customer-ageing-report'], 'active show-subnav')); ?>">
                <div class="sub-menu-nav" data-subnav="subnavSales">
                    <!-- <i class="ico icon-outline-bag-4"></i> -->
                    <img src="<?php echo e(asset('public/design')); ?>/assets/images/icons/sales.png" height="24px"
                        title="Sales">
                    <span class="nav-text">Sales</span>
                </div>
                <div class="subnav-menu" id="subnavSales">
                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('customers')); ?>">
                        <?php if(count($crm->where('is_read', 1)->where('module_link_id', 16)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('customers')); ?>" class="sub-nav-link">Customer Register</a>
                        <?php endif; ?>
                    </div>
                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('quotations')); ?>">
                        <?php if(count($crm->where('is_read', 1)->where('module_link_id', 17)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('quotations')); ?>" class="sub-nav-link">Quotation</a>
                        <?php endif; ?>
                    </div>
                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('proforma-invoice')); ?>">
                        <?php if(count($crm->where('is_read', 1)->where('module_link_id', 18)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('proforma-invoice')); ?>" class="sub-nav-link">Proforma Invoice</a>
                        <?php endif; ?>
                    </div>
                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('sales-invoice')); ?>">
                        <?php if(count($crm->where('is_read', 1)->where('module_link_id', 19)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('sales-invoice')); ?>" class="sub-nav-link">Sales Invoice</a>
                        <?php endif; ?>
                    </div>
                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('delivery-note')); ?>">
                        <?php if(count($crm->where('is_read', 1)->where('module_link_id', 20)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('delivery-note')); ?>" class="sub-nav-link">Delivery Note</a>
                        <?php endif; ?>
                    </div>
                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('sales-return')); ?>">
                        <?php if(count($crm->where('is_read', 1)->where('module_link_id', 21)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('sales-return')); ?>" class="sub-nav-link">Sales Return</a>
                        <?php endif; ?>
                    </div>
                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('receipt')); ?>">
                        <?php if(count($crm->where('is_read', 1)->where('module_link_id', 22)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('receipt')); ?>" class="sub-nav-link">Receipts</a>
                        <?php endif; ?>
                    </div>
                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('receivable-outstanding')); ?> <?php echo e(@App\SysHelper::isActiveRoute('customer-ageing-report')); ?> ">
                        <?php if(count($crm->where('is_read', 1)->where('module_link_id', 23)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('receivable-outstanding')); ?>" class="sub-nav-link">Receivable
                                Outstanding</a>
                        <?php endif; ?>
                    </div>
                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('si-adjustment-report')); ?>">
                        <?php if(count($crm->where('is_read', 1)->where('module_link_id', 64)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('si-adjustment-report')); ?>" class="sub-nav-link">SI Adjustment
                                Report</a>
                        <?php endif; ?>
                    </div>
                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('clearance')); ?>">
                        <?php if(session('logged_session_data.company_id') == 2): ?>
                            <?php if(count($crm->where('is_read', 1)->where('module_link_id', 50)) > 0 || Auth::user()->role_id == 1): ?>
                                <a href="<?php echo e(url('clearance')); ?>" class="sub-nav-link">Customs Clearance</a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </li>
        <?php endif; ?>
        

          
        <?php $crm = $permissions->wherein('module_link_id', [24, 25, 26, 27, 28, 29, 59]); ?>
        <?php if(count($crm->where('is_read', 1)) > 0 || Auth::user()->role_id == 1): ?>
            <li
                class="nav-item <?php echo e(@App\SysHelper::isMenuOpen(['item-add', 'item-store/show', 'stock-register', 'stock-ledger', 'stock-in', 'stock-out', 'packing-list'], 'active show-subnav')); ?>">
                <div class="sub-menu-nav" data-subnav="subnavInventory">
                    <!-- <i class="ico icon-outline-server"></i> -->
                    <img src="<?php echo e(asset('public/design')); ?>/assets/images/icons/inventory.png" height="24px"
                        title="Inventory">
                    <span class="nav-text">Inventory</span>
                </div>
                <div class="subnav-menu" id="subnavInventory">
                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('item-add')); ?>">
                        <?php if(count($crm->where('is_read', 1)->where('module_link_id', 24)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('item-add')); ?>" class="sub-nav-link">Products</a>
                        <?php endif; ?>
                    </div>
                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('item-store/show')); ?>">
                        <?php if(count($crm->where('is_read', 1)->where('module_link_id', 25)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('item-store/show')); ?>" class="sub-nav-link">Opening Stock</a>
                        <?php endif; ?>
                    </div>
                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('stock-register')); ?>">
                        <?php if(count($crm->where('is_read', 1)->where('module_link_id', 26)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('stock-register')); ?>" class="sub-nav-link">Stock Register</a>
                        <?php endif; ?>
                    </div>
                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('stock-ledger')); ?>">
                        <?php if(count($crm->where('is_read', 1)->where('module_link_id', 27)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('stock-ledger')); ?>" class="sub-nav-link">Stock Ledger</a>
                        <?php endif; ?>
                    </div>
                     <div class="sub-nav-item">
                        <?php if(count($crm->where('is_read', 1)->where('module_link_id', 27)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="#" class="sub-nav-link">Store Ledger</a>
                        <?php endif; ?>
                    </div>
                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('stock-in')); ?>">
                        <?php if(count($crm->where('is_read', 1)->where('module_link_id', 28)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('stock-in')); ?>" class="sub-nav-link">Excess Stock</a>
                        <?php endif; ?>
                    </div>
                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('stock-out')); ?>">
                        <?php if(count($crm->where('is_read', 1)->where('module_link_id', 29)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('stock-out')); ?>" class="sub-nav-link">Shortage Stock</a>
                        <?php endif; ?>
                    </div>
                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('packing-list')); ?>">
                        <?php if(count($crm->where('is_read', 1)->where('module_link_id', 59)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('packing-list')); ?>" class="sub-nav-link">Packing List</a>
                        <?php endif; ?>
                    </div>
                </div>
            </li>
        <?php endif; ?>
        



        
        <?php $hrms = $permissions->wherein('module_link_id', [66, 67, 68, 69, 70]); ?>
        <?php if(count($hrms->where('is_read', 1)) > 0 || Auth::user()->role_id == 1): ?>
            <li
                class="nav-item <?php echo e(@App\SysHelper::isMenuOpen(['company/policy', 'staff-directory', 'approvals', 'employee/leaves/', 'crm-reimbursement-request'], 'active show-subnav')); ?>">
                <div class="sub-menu-nav" data-subnav="subnavHrms">
                    <!-- <i class="ico icon-outline-document-text"></i> -->
                    <img src="<?php echo e(asset('public/design')); ?>/assets/images/icons/management.png" height="24px"
                        title="HRMS">
                    <span class="nav-text">HRMS</span>
                </div>
                <div class="subnav-menu" id="subnavHrms">
                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('company/policy')); ?>">
                        <?php if(count($hrms->where('is_read', 1)->where('module_link_id', 66)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('company/policy')); ?>"  class="sub-nav-link">Company Policy
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('staff-directory')); ?>">

                        <?php if(count($hrms->where('is_read', 1)->where('module_link_id', 67)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('staff-directory')); ?>" class="sub-nav-link">Employee Management</a>
                        <?php endif; ?>

                    </div>

                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('approvals')); ?>">
                        <?php if(count($hrms->where('is_read', 1)->where('module_link_id', 68)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('approvals/inbox')); ?>"  class="sub-nav-link">Leave
                                Management</a>
                        <?php endif; ?>
                    </div>

                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('employee/leaves')); ?>">

                        <?php if(count($hrms->where('is_read', 1)->where('module_link_id', 69)) > 0): ?>
                            <a href="<?php echo e(url('employee/leaves/')); ?>"  class="sub-nav-link">Leaves </a>
                        <?php endif; ?>

                    </div>


                      <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('attendance.index')); ?>">

                        
                            <a href="<?php echo e(route('attendance.index')); ?>"  class="sub-nav-link">Attendance </a>
                        

                    </div>

                       <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('employee.loans.index')); ?>">

                        
                            <a href="<?php echo e(route('employee.loans.index')); ?>"  class="sub-nav-link">Loans &amp; Advance </a>
                        

                    </div>

                     <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('employee.loans.index')); ?>">
                        
                            <a href="<?php echo e(route('staff.compensation.create')); ?>"  class="sub-nav-link">Compensation & Roles Changes </a>
                        
                    </div>


                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('employee.loans.index')); ?>">
                    
                    <a href="<?php echo e(route('staff.resignation.add')); ?>"  class="sub-nav-link">End of Service </a>
                    
                    </div>


                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('crm-reimbursement-request')); ?>">
                        <?php if(count($hrms->where('is_read', 1)->where('module_link_id', 70)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('crm-reimbursement-request')); ?>" class="sub-nav-link">Reimbursement
                                Request</a>
                        <?php endif; ?>
                    </div>


                   

                    

                </div>
            </li>
        <?php endif; ?>


          <?php $hrms = $permissions->wherein('module_link_id', [66, 67, 68, 69, 70]); ?>
        <?php if(count($hrms->where('is_read', 1)) > 0 || Auth::user()->role_id == 1): ?>
            <li
                class="nav-item">
                <div class="sub-menu-nav" data-subnav="subnavMarketing">
                    <!-- <i class="ico icon-outline-document-text"></i> -->
                    <img src="<?php echo e(asset('public/design')); ?>/assets/images/icons/management.png" height="24px"
                        title="HRMS">
                    <span class="nav-text">Marketing</span>
                </div>
                <div class="subnav-menu" id="subnavMarketing">
                    <div class="sub-nav-item">
                            <a href="<?php echo e(url('company/policy')); ?>"  class="sub-nav-link">A
                            </a>
                    </div>
                    <div class="sub-nav-item">
                            <a href="<?php echo e(url('company/policy')); ?>"  class="sub-nav-link">B
                            </a>
                    </div>
                    
                    <div class="sub-nav-item">
                            <a href="<?php echo e(url('company/policy')); ?>"  class="sub-nav-link">C
                            </a>
                    </div>
                    
                    

                </div>
            </li>
        <?php endif; ?>



        
        <?php $service = $permissions->wherein('module_link_id', [53, 54, 55, 57, 58]); ?>
        <?php if(count($service->where('is_read', 1)) > 0 || Auth::user()->role_id == 1): ?>
            <li
                class="nav-item <?php echo e(@App\SysHelper::isMenuOpen(['crm-amc-list', 'crm-ps-track-service-list', 'crm-deal-support-list', 'crm-engineer-tracking', 'crm-amc-service-request-list', 'crm-ps-service-list-req', 'crm-deal-support-requested-list'], 'active show-subnav')); ?>">
                <div class="sub-menu-nav" data-subnav="subnavServiceDesk">
                    <!-- <i class="ico icon-outline-headphones-round"></i> -->
                    <img src="<?php echo e(asset('public/design')); ?>/assets/images/icons/service.png" height="24px"
                        title="Service Desk">
                    <span class="nav-text">Service Desk</span>
                </div>
                <div class="subnav-menu" id="subnavServiceDesk">
                    <div
                        class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('crm-amc-list')); ?> <?php echo e(@App\SysHelper::isActiveRoute('crm-amc-service-request-list')); ?>">
                        <?php if(count($service->where('is_read', 1)->where('module_link_id', 53)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('crm-amc-list')); ?>" class="sub-nav-link">Annual Maintenance Contract</a>
                        <?php endif; ?>
                    </div>
                    <div
                        class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('crm-ps-track-service-list')); ?> <?php echo e(@App\SysHelper::isActiveRoute('crm-ps-service-list-req')); ?>">
                        <?php if(count($service->where('is_read', 1)->where('module_link_id', 54)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('crm-ps-track-service-list')); ?>" class="sub-nav-link">Project Service
                                Request</a>
                        <?php endif; ?>
                    </div>
                    <div
                        class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('crm-deal-support-list')); ?> <?php echo e(@App\SysHelper::isActiveRoute('crm-deal-support-requested-list')); ?>">
                        <?php if(count($service->where('is_read', 1)->where('module_link_id', 55)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('crm-deal-support-list')); ?>" class="sub-nav-link">Pre-Sales Request</a>
                        <?php endif; ?>
                    </div>
                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('crm-engineer-tracking')); ?>">
                        <?php if(count($service->where('is_read', 1)->where('module_link_id', 57)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('crm-engineer-tracking')); ?>" class="sub-nav-link">Service Request
                                List</a>
                        <?php endif; ?>
                    </div>

                </div>
            </li>
        <?php endif; ?>
        

        
        <?php $execution_desk = $permissions->wherein('module_link_id', [63]); ?>
        <?php if(count($execution_desk->where('is_read', 1)) > 0 || Auth::user()->role_id == 1): ?>
            <li
                class="nav-item <?php echo e(@App\SysHelper::isMenuOpen(['crm-user-tasks', 'user-todo-list', 'tasks-assigned-by-me'], 'active show-subnav')); ?>">
                <div class="sub-menu-nav" data-subnav="subnavDatabase">
                    <!-- <i class="ico icon-outline-database"></i> -->
                    <img src="<?php echo e(asset('public/design')); ?>/assets/images/icons/execution-desk.png" height="24px"
                        title="Execution Desk">
                    <span class="nav-text">Execution Desk</span>
                </div>
                <div class="subnav-menu" id="subnavDatabase">
                    <div
                        class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('crm-user-tasks')); ?>  <?php echo e(@App\SysHelper::isActiveRoute('tasks-assigned-by-me')); ?>">
                        <a href="<?php echo e(url('crm-user-tasks')); ?>" class="sub-nav-link">Task</a>
                    </div>
                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('user-todo-list')); ?>">
                        <a href="<?php echo e(url('user-todo-list')); ?>" class="sub-nav-link">Todo List</a>

                    </div>
                    <div class="sub-nav-item">
                        <a href="#" class="sub-nav-link">Notes</a>
                        <a href="#" class="sub-nav-link">Activity Tracker</a>
                    </div>
                </div>
            </li>
        <?php endif; ?>
        

        

      

             <?php $hrms = $permissions->wherein('module_link_id', [66, 67, 68, 69, 70]); ?>
        <?php if(count($hrms->where('is_read', 1)) > 0 || Auth::user()->role_id == 1): ?>
            <li
                class="nav-item">
                <div class="sub-menu-nav" data-subnav="subnavAuditing">
                    <!-- <i class="ico icon-outline-document-text"></i> -->
                    <img src="<?php echo e(asset('public/design')); ?>/assets/images/icons/management.png" height="24px"
                        title="HRMS">
                    <span class="nav-text">Auditing</span>
                </div>
                <div class="subnav-menu" id="subnavAuditing">
                    <div class="sub-nav-item">
                            <a href="<?php echo e(url('company/policy')); ?>"  class="sub-nav-link">Transfer Pricing
                            </a>
                    </div>
                    <div class="sub-nav-item">
                            <a href="<?php echo e(url('company/policy')); ?>"  class="sub-nav-link">Inhouse Financial Statement
                            </a>
                    </div>
                    
                    <div class="sub-nav-item">
                            <a href="<?php echo e(url('company/policy')); ?>"  class="sub-nav-link">Audit Report
                            </a>
                    </div>

                     <div class="sub-nav-item">
                            <a href=""  class="sub-nav-link">Zakat
                            </a>
                    </div>
                    
                    

                </div>
            </li>
        <?php endif; ?>

      
        
        <?php $crm = $permissions->wherein('module_link_id', [30, 31, 32, 33, 34, 61, 62]); ?>
        <?php if(count($crm->where('is_read', 1)) > 0 || Auth::user()->role_id == 1): ?>
            <li
                class="nav-item <?php echo e(@App\SysHelper::isMenuOpen(['inventory-report', 'inventory-brand-report', 'inventory-brand-wise-report', 'inventory-category-wise-report', 'inventory-subcategory-wise-report', 'inventory-company-wise-report', 'inventory-salesperson-wise-report', 'inventory-brand-report-detail', 'sales-invoice-report', 'sales-invoice-report-detail', 'generalledger', 'trial-balance', 'trading-account', 'profit-and-loss-account', 'balancesheet'], 'active show-subnav')); ?>">
                <div class="sub-menu-nav" data-subnav="subnavReports">
                    <!-- <i class="ico icon-outline-document-text"></i> -->
                    <img src="<?php echo e(asset('public/design')); ?>/assets/images/icons/report.png" height="24px"
                        title="Reports">
                    <span class="nav-text">Reports</span>
                </div>
                <div class="subnav-menu" id="subnavReports">
                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('inventory-report')); ?>">
                        <?php if(count($crm->where('is_read', 1)->where('module_link_id', 62)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('inventory-report')); ?>" class="sub-nav-link">Inventory Report</a>
                        <?php endif; ?>
                    </div>
                     <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute(['inventory-brand-report', 'inventory-brand-wise-report', 'inventory-category-wise-report', 'inventory-subcategory-wise-report', 'inventory-company-wise-report', 'inventory-salesperson-wise-report', 'inventory-brand-report-detail'])); ?>">
                        <?php if(count($crm->where('is_read', 1)->where('module_link_id', 62)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('inventory-brand-report')); ?>" class="sub-nav-link">Inventory Brand Report</a>
                        <?php endif; ?>
                    </div>
                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('sales-invoice-report')); ?>">
                        <?php if(count($crm->where('is_read', 1)->where('module_link_id', 61)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('sales-invoice-report')); ?>" class="sub-nav-link">Daily Sales Report</a>
                        <?php endif; ?>
                    </div>
                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('sales-invoice-report-detail')); ?>">
                        <?php if(count($crm->where('is_read', 1)->where('module_link_id', 61)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('sales-invoice-report-detail')); ?>" class="sub-nav-link">Sales Report</a>
                        <?php endif; ?>
                    </div>
                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('generalledger')); ?>">
                        <?php if(count($crm->where('is_read', 1)->where('module_link_id', 30)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('generalledger')); ?>" class="sub-nav-link">General Ledger</a>
                        <?php endif; ?>
                    </div>
                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('trial-balance')); ?>">
                        <?php if(count($crm->where('is_read', 1)->where('module_link_id', 31)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('trial-balance')); ?>" class="sub-nav-link">Trial Balance</a>
                        <?php endif; ?>
                    </div>
                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('trading-account')); ?>">
                        <?php if(count($crm->where('is_read', 1)->where('module_link_id', 32)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('trading-account')); ?>" class="sub-nav-link">Trading Account</a>
                        <?php endif; ?>
                    </div>
                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('profit-and-loss-account')); ?>">
                        <?php if(count($crm->where('is_read', 1)->where('module_link_id', 33)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('profit-and-loss-account')); ?>" class="sub-nav-link">Profit & Loss
                                Account</a>
                        <?php endif; ?>
                    </div>
                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('balancesheet')); ?>">
                        <?php if(count($crm->where('is_read', 1)->where('module_link_id', 34)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('balancesheet')); ?>" class="sub-nav-link">Balancesheet</a>
                        <?php endif; ?>
                    </div>
                </div>
            </li>
        <?php endif; ?>
        

        
        <?php $crm = $permissions->wherein('module_link_id', [35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 52, 56]); ?>
        <?php if(count($crm->where('is_read', 1)) > 0 || Auth::user()->role_id == 1): ?>
            <li
                class="nav-item <?php echo e(@App\SysHelper::isMenuOpen(['company', 'role', 'module', 'base-setup', 'daily-quotes', 'currency-settings', 'payment-terms', 'payment-cheque-print-template', 'shipping-add', 'vat-settings', 'accountgroup-add', 'general-settings', 'background-setting', 'backup-settings'], 'active show-subnav')); ?>">
                <div class="sub-menu-nav" data-subnav="subnavSettings">
                    <!-- <i class="ico icon-outline-settings"></i> -->
                    <img src="<?php echo e(asset('public/design')); ?>/assets/images/icons/settings.png" height="24px"
                        title="Settings">
                    <span class="nav-text">Settings</span>
                </div>
                <div class="subnav-menu" id="subnavSettings">
                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('role')); ?>">
                      
                        
                        

                    </div>

                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('company')); ?>">
                        <?php if(count($crm->where('is_read', 1)->where('module_link_id', 35)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(route('company')); ?>" class="sub-nav-link"><?php echo app('translator')->getFromJson('Company Settings'); ?></a>
                        <?php endif; ?>
                        
                      

                    </div>

                    

                    
                    
                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('general-settings')); ?>">
                        <?php if(count($crm->where('is_read', 1)->where('module_link_id', 47)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('general-settings')); ?>" class="sub-nav-link"><?php echo app('translator')->getFromJson('lang.general_settings'); ?></a>
                        <?php endif; ?>
                    </div>
                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('background-setting')); ?>">
                        <?php if(count($crm->where('is_read', 1)->where('module_link_id', 48)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('background-setting')); ?>" class="sub-nav-link"><?php echo app('translator')->getFromJson('lang.background_settings'); ?></a>
                        <?php endif; ?>
                    </div>
                    <div class="sub-nav-item <?php echo e(@App\SysHelper::isActiveRoute('backup-settings')); ?>">
                        <?php if(count($crm->where('is_read', 1)->where('module_link_id', 49)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('backup-settings')); ?>" class="sub-nav-link"><?php echo app('translator')->getFromJson('lang.backup_settings'); ?></a>
                        <?php endif; ?>
                    </div>
                     <?php if(Auth::user()->role_id == 1 ): ?>
                    <div class="sub-nav-item">
                        <?php if(count($crm->where('is_read', 1)->where('module_link_id', 49)) > 0 || Auth::user()->role_id == 1): ?>
                            <a href="<?php echo e(url('delete-all-data')); ?>" class="sub-nav-link"><?php echo app('translator')->getFromJson('Delete All Data'); ?></a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </li>
        <?php endif; ?>
        
    </ul>
</nav>

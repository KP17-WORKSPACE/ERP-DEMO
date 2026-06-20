<nav class="main-nav sidebar-new">
    <div class="toggle-nav"></div>
    <ul class="nav-list sidenav-list">
        <style>
            .sidenav-list .nav-item {
                text-align: left
            }

            .sidenav-list .subnav-menu .hrms-nested-menu {
                width: 100% !important;
                margin: 0;
            }

            .sidenav-list .subnav-menu .hrms-nested-menu>.sub-menu-nav {
                display: flex !important;
                align-items: center !important;
                justify-content: space-between !important;
                width: 100% !important;
                min-height: auto;
                padding: 5px 0px 5px 0;
                cursor: pointer;
                background: transparent !important;
                border-radius: 0;
                gap: 0;
            }

            .sidenav-list .subnav-menu .hrms-nested-menu>.sub-menu-nav:hover .sub-nav-link {
                font-weight: 700;
            }

            .sidenav-list .subnav-menu .hrms-nested-menu>.sub-menu-nav .sub-nav-link {
                flex: 1 1 auto !important;
                display: block !important;
            }

            .sidenav-list .subnav-menu .hrms-nested-menu>.sub-menu-nav:before {
                content: none !important;
                display: none !important;
            }

            .sidenav-list .subnav-menu .hrms-nested-menu>.sub-menu-nav .hrms-submenu-arrow {
                flex: 0 0 auto !important;
                margin-left: auto !important;
                width: 14px !important;
                height: 14px !important;
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                position: static !important;
                top: auto !important;
                left: auto !important;
                right: auto !important;
                color: var(--color-nav-text);
                font-size: 16px;
                line-height: 1;
                transform-origin: center !important;
                transform: rotate(0deg) !important;
                transition: all 0.2s ease;
            }

            .sidenav-list .subnav-menu .hrms-nested-menu>.subnav-menu {
                display: none !important;
                position: static;
                width: auto;
                min-width: auto;
                margin: 1px 0 0 15px !important;
                padding: 0 !important;
                background: transparent;
                border-radius: 0;
                box-shadow: none;
            }

            .sidenav-list .subnav-menu .hrms-nested-menu>.subnav-menu .sub-nav-item {
                margin-top: 0 !important;
            }

            .sidenav-list .subnav-menu .hrms-nested-menu.show-subnav>.subnav-menu {
                display: block !important;
            }

            .sidenav-list .subnav-menu .hrms-nested-menu.show-subnav>.sub-menu-nav .hrms-submenu-arrow {
                transform: rotate(90deg) !important;
            }
        </style>
        <li class="nav-item {{ request()->is('crm-dashboard*') ? 'active show-subnav' : '' }}">
            <a href="{{ url('/crm-dashboard') }}" class="nav-link">
                <!-- <i class="ico icon-outline-widget-6"></i> -->
                <img src="{{ asset('public/design') }}/assets/images/icons/dashboard.png" height="24px"
                    title="Dashboard">
                <span class="nav-text">Dashboard</span>
            </a>
        </li>


        {{-- Accounts --}}
        <?php $crm = $permissions->wherein('module_link_id', [1, 2, 3, 4, 60]); ?>
        @if (count($crm->where('is_read', 1)) > 0 || Auth::user()->role_id == 1)
            <li
                class="nav-item {{ request()->is('chartofaccounts*') || request()->is('chartofaccounts-opening-balance*') || request()->is('journalvoucher*') || request()->is('cashbook*') || request()->is('bankbook*') || request()->is('stl-report*') || request()->is('accountgroupsub-add*') || request()->is('accountgroupsub2-add*') || request()->is('chartofaccounts-add*') || request()->is('chartofaccounts-add-sub*') || request()->is('stl-supplier-report*') || request()->is('chequebook*') || request()->is('book-close*') || request()->is('book-close-doc-number*') ? 'active show-subnav' : '' }}">
                <div class="sub-menu-nav" data-subnav="subnavAccounts">
                    <!-- <i class="ico icon-outline-calculator"></i> -->
                    <img src="{{ asset('public/design') }}/assets/images/icons/accounts.png" height="24px" title="Accounts">
                    <span class="nav-text">Accounts</span>
                </div>
                <div class="subnav-menu" id="subnavAccounts">
                    @if (count($crm->where('is_read', 1)->where('module_link_id', 1)) > 0 || Auth::user()->role_id == 1)
                        <div
                            class="sub-nav-item {{ request()->is('chartofaccounts*') ? 'active' : '' }} {{ request()->is('accountgroupsub-add*') ? 'active' : '' }} {{ request()->is('accountgroupsub2-add*') ? 'active' : '' }} {{ request()->is('chartofaccounts-add*') ? 'active' : '' }} {{ request()->is('chartofaccounts-add-sub*') ? 'active' : '' }}">
                            <a href="{{ url('chartofaccounts') }}" class="sub-nav-link ">Chart of Accounts</a>
                        </div>

                        <div class="sub-nav-item {{ request()->is('chartofaccounts-opening-balance*') ? 'active' : '' }}">
                            <a href="{{ url('chartofaccounts-opening-balance') }}" class="sub-nav-link ">Opening
                                Balance</a>
                        </div>
                    @endif

                    @if (count($crm->where('is_read', 1)->where('module_link_id', 2)) > 0 || Auth::user()->role_id == 1)
                        <div class="sub-nav-item {{ request()->is('journalvoucher*') ? 'active' : '' }}">
                            <a href="{{ url('journalvoucher') }}" class="sub-nav-link">Journal Voucher</a>
                        </div>
                    @endif
                    @if (count($crm->where('is_read', 1)->where('module_link_id', 3)) > 0 || Auth::user()->role_id == 1)
                        <div class="sub-nav-item {{ request()->is('cashbook*') ? 'active' : '' }}">
                            <a href="{{ url('cashbook') }}" class="sub-nav-link ">Cash Book</a>
                        </div>
                    @endif
                    @if (count($crm->where('is_read', 1)->where('module_link_id', 4)) > 0 || Auth::user()->role_id == 1)
                        <div class="sub-nav-item {{ request()->is('bankbook*') ? 'active' : '' }}">
                            <a href="{{ url('bankbook') }}" class="sub-nav-link ">Bank Book</a>
                        </div>
                    @endif

                    @if (count($crm->where('is_read', 1)->where('module_link_id', 4)) > 0 || Auth::user()->role_id == 1)
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('creditcard') }}">
                    <a href="{{ url('creditcard') }}" class="sub-nav-link ">Credit Card</a>
                    </div>
                    @endif

                    @if (count($crm->where('is_read', 1)->where('module_link_id', 4)) > 0 || Auth::user()->role_id == 1)
                        <div class="sub-nav-item {{ request()->is('chequebook*') ? 'active' : '' }}">
                            <a href="{{ url('chequebook') }}" class="sub-nav-link">Cheque Book</a>
                        </div>
                    @endif
                    @if (count($crm->where('is_read', 1)->where('module_link_id', 60)) > 0 || Auth::user()->role_id == 1)
                        <div
                            class="sub-nav-item {{ request()->is('stl-report*') ? 'active' : '' }}  {{ request()->is('stl-supplier-report*') ? 'active' : '' }}">
                            <a href="{{ url('stl-report') }}" class="sub-nav-link ">STL Report</a>
                        </div>
                    @endif


                    <!--
                                @if(count($crm->where('is_read',1)->where('module_link_id',68)) > 0 ||  Auth::user()->role_id == 1)
                                <div class="sub-nav-item {{ request()->is('book-close*') ? 'active' : '' }}">
                                    <a href="{{ url('book-close') }}" class="sub-nav-link">@lang('Book Closed')</a>
                                </div>

                                @endif
                                @if(count($crm->where('is_read',1)->where('module_link_id',69)) > 0 ||  Auth::user()->role_id == 1)
                                <div class="sub-nav-item {{ request()->is('book-close-doc-number*') ? 'active' : '' }}">
                                    <a href="{{ url('book-close-doc-number') }}" class="sub-nav-link">@lang('Book Close Doc No')</a>
                                </div>
                                @endif -->
                </div>
            </li>
        @endif
        {{-- Accounts --}}


        {{-- CRM --}}
        <?php $crm = $permissions->wherein('module_link_id', [5, 6, 7, 8, 51]); ?>
        @if (count($crm->where('is_read', 1)) > 0 || Auth::user()->role_id == 1)
            <li
                class="nav-item {{ request()->is('crm-leads/show*') || request()->is('crm-deals*') || request()->is('crm-deal-track-approval-list*') || request()->is('crm-deal-track-status*') || request()->is('crm-deals-sales-report-company*') || request()->is('crm-deals-brand-sales-report-new*') || request()->is('crm-deals-sales-report*') || request()->is('crm-deals-sales-report-list*') ? 'active show-subnav' : '' }}">
                <div class="sub-menu-nav" data-subnav="subnavCRM">
                    <!-- <i class="ico icon-outline-calculator"></i> -->
                    <img src="{{ asset('public/design') }}/assets/images/icons/crm.png" height="24px" title="CRM">
                    <span class="nav-text">CRM</span>
                </div>
                <div class="subnav-menu" id="subnavCRM">
                    <div class="sub-nav-item {{ request()->is('crm-leads*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 5)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('crm-leads/show') }}" class="sub-nav-link">Leads</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ request()->is('crm-deals*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 6)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('crm-deals/show') }}" class="sub-nav-link">Deals</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ request()->is('crm-deal-track-approval-list*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 7)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('crm-deal-track-approval-list') }}" class="sub-nav-link">Deals Track</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ request()->is('crm-deal-track-status*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 8)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('crm-deal-track-status') }}" class="sub-nav-link">Deals Track Status</a>
                        @endif
                    </div>

                    @if (count($crm->where('is_read', 1)->where('module_link_id', 51)) > 0 || Auth::user()->role_id == 1)
                        <div
                            class="sub-nav-item {{ request()->is('crm-deals-sales-report-company*') ? 'active' : '' }} {{ request()->is('crm-deals-sales-report*') ? 'active' : '' }} {{ request()->is('crm-deals-sales-report-list*') ? 'active' : '' }}">
                            <a href="{{ url('crm-deals-sales-report-company') }}" class="sub-nav-link">CRM Sales Report</a>
                        </div>
                        <div class="sub-nav-item {{ request()->is('crm-deals-brand-sales-report-new*') ? 'active' : '' }}">
                            <a href="{{ url('crm-deals-brand-sales-report-new') }}" class="sub-nav-link">Brand Sales
                                Report</a>
                        </div>
                    @endif

                </div>
            </li>
        @endif
        {{-- CRM --}}



        {{-- Purchase --}}
        <?php $crm = $permissions->wherein('module_link_id', [9, 10, 11, 12, 13, 14, 15, 65]); ?>
        @if (count($crm->where('is_read', 1)) > 0 || Auth::user()->role_id == 1)
            <li
                class="nav-item {{ request()->is('suppliers*') || request()->is('purchase-order*') || request()->is('goods-receipt-note-list*') || request()->is('purchase-invoice*') || request()->is('purchase-return*') || request()->is('payment*') || request()->is('payables-outstanding*') || request()->is('pi-adjustment-report*') || request()->is('supplier-ageing-report*') ? 'active show-subnav' : '' }}">
                <div class="sub-menu-nav" data-subnav="subnavPurchase">
                    <!-- <i class="ico icon-bold-cart-large-4"></i> -->
                    <img src="{{ asset('public/design') }}/assets/images/icons/purchase.png" height="24px" title="Purchase">
                    <span class="nav-text">Purchase</span>
                </div>
                <div class="subnav-menu" id="subnavPurchase">
                    <div class="sub-nav-item {{ request()->is('suppliers*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 9)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('suppliers') }}" class="sub-nav-link">Supplier Register</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ request()->is('purchase-order*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 10)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('purchase-order') }}" class="sub-nav-link">Purchase Order</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ request()->is('goods-receipt-note-list*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 11)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('goods-receipt-note-list') }}" class="sub-nav-link">Goods Receipt
                                Note</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ request()->is('purchase-invoice*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 12)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('purchase-invoice') }}" class="sub-nav-link">Purchase Invoice</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ request()->is('purchase-return*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 13)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('purchase-return') }}" class="sub-nav-link">Purchase Return</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ request()->is('payment*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 14)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('payment') }}" class="sub-nav-link">Payments</a>
                        @endif
                    </div>
                    <div
                        class="sub-nav-item {{ request()->is('payables-outstanding*') ? 'active' : '' }} {{ request()->is('supplier-ageing-report*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 15)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('payables-outstanding') }}" class="sub-nav-link">Payables Outstanding</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ request()->is('pi-adjustment-report*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 65)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('pi-adjustment-report') }}" class="sub-nav-link">PI Adjustment Report</a>
                        @endif
                    </div>
                </div>
            </li>
        @endif
        {{-- Purchase --}}

        {{-- Sales --}}
        <?php $crm = $permissions->wherein('module_link_id', [16, 17, 18, 19, 20, 21, 22, 23, 50, 64]); ?>
        @if (count($crm->where('is_read', 1)) > 0 || Auth::user()->role_id == 1)
            <li
                class="nav-item {{ request()->is('customers*') || request()->is('quotations*') || request()->is('proforma-invoice*') || request()->is('sales-invoice*') || request()->is('delivery-note*') || request()->is('sales-return*') || request()->is('receipt*') || request()->is('receivable-outstanding*') || request()->is('si-adjustment-report*') || request()->is('clearance*') || request()->is('customer-ageing-report*') ? 'active show-subnav' : '' }}">
                <div class="sub-menu-nav" data-subnav="subnavSales">
                    <!-- <i class="ico icon-outline-bag-4"></i> -->
                    <img src="{{ asset('public/design') }}/assets/images/icons/sales.png" height="24px" title="Sales">
                    <span class="nav-text">Sales</span>
                </div>
                <div class="subnav-menu" id="subnavSales">
                    <div class="sub-nav-item {{ request()->is('customers*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 16)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('customers') }}" class="sub-nav-link">Customer Register</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ request()->is('quotations*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 17)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('quotations') }}" class="sub-nav-link">Quotation</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ request()->is('proforma-invoice*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 18)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('proforma-invoice') }}" class="sub-nav-link">Proforma Invoice</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ request()->is('sales-invoice*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 19)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('sales-invoice') }}" class="sub-nav-link">Sales Invoice</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ request()->is('delivery-note*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 20)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('delivery-note') }}" class="sub-nav-link">Delivery Note</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ request()->is('sales-return*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 21)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('sales-return') }}" class="sub-nav-link">Sales Return</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ request()->is('receipt*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 22)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('receipt') }}" class="sub-nav-link">Receipts</a>
                        @endif
                    </div>
                    <div
                        class="sub-nav-item {{ request()->is('receivable-outstanding*') ? 'active' : '' }} {{ request()->is('customer-ageing-report*') ? 'active' : '' }} ">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 23)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('receivable-outstanding') }}" class="sub-nav-link">Receivable
                                Outstanding</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ request()->is('si-adjustment-report*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 64)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('si-adjustment-report') }}" class="sub-nav-link">SI Adjustment
                                Report</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ request()->is('clearance*') ? 'active' : '' }}">
                        @if (session('logged_session_data.company_id') == 2)
                            @if (count($crm->where('is_read', 1)->where('module_link_id', 50)) > 0 || Auth::user()->role_id == 1)
                                <a href="{{ url('clearance') }}" class="sub-nav-link">Customs Clearance</a>
                            @endif
                        @endif
                    </div>
                </div>
            </li>
        @endif
        {{-- Sales --}}

        {{-- Inventory --}}
        <?php $crm = $permissions->wherein('module_link_id', [24, 25, 26, 27, 28, 29, 59]); ?>
        @if (count($crm->where('is_read', 1)) > 0 || Auth::user()->role_id == 1)
            <li
                class="nav-item {{ request()->is('item-add*') || request()->is('item-store/show*') || request()->is('stock-register*') || request()->is('stock-ledger*') || request()->is('stock-in*') || request()->is('stock-out*') || request()->is('packing-list*') ? 'active show-subnav' : '' }}">
                <div class="sub-menu-nav" data-subnav="subnavInventory">
                    <!-- <i class="ico icon-outline-server"></i> -->
                    <img src="{{ asset('public/design') }}/assets/images/icons/inventory.png" height="24px"
                        title="Inventory">
                    <span class="nav-text">Inventory</span>
                </div>
                <div class="subnav-menu" id="subnavInventory">
                    <div class="sub-nav-item {{ request()->is('item-add*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 24)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('item-add') }}" class="sub-nav-link">Products</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ request()->is('item-store/show*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 25)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('item-store/show') }}" class="sub-nav-link">Opening Stock</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ request()->is('stock-register*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 26)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('stock-register') }}" class="sub-nav-link">Stock Register</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ request()->is('stock-ledger*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 27)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('stock-ledger') }}" class="sub-nav-link">Stock Ledger</a>
                        @endif
                    </div>
                    <div class="sub-nav-item">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 27)) > 0 || Auth::user()->role_id == 1)
                            <a href="#" class="sub-nav-link">Store Ledger</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ request()->is('stock-in*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 28)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('stock-in') }}" class="sub-nav-link">Excess Stock</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ request()->is('stock-out*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 29)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('stock-out') }}" class="sub-nav-link">Shortage Stock</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ request()->is('packing-list*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 59)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('packing-list') }}" class="sub-nav-link">Packing List</a>
                        @endif
                    </div>
                </div>
            </li>
        @endif
        {{-- Inventory --}}



        {{-- HRMS --}}
        <?php
$loanSidebarModuleLinks = \App\SmModuleLink::whereIn('page_name', ['loans_advances', 'loan_track', 'loan_report'])->pluck('id', 'page_name');
$reimbursementSidebarModuleLinks = \App\SmModuleLink::whereIn('page_name', ['crm-reimbursement-request', 'reimbursement_list', 'crm-reimbursement-track'])->pluck('id', 'page_name');
$hrms = $permissions->wherein('module_link_id', array_merge([66, 67, 68, 69, 70], $loanSidebarModuleLinks->values()->toArray(), $reimbursementSidebarModuleLinks->values()->toArray()));
        ?>
        @if (count($hrms->where('is_read', 1)) > 0 || in_array(Auth::user()->role_id, [1, 2]))
            <li
                class="nav-item {{ request()->is('company/policy*') || request()->is('staff-directory*') || request()->is('approvals*') || request()->is('employee/leaves/*') || request()->is('employee/loans*') || request()->is('employee/loan-track-approval-list*') || request()->is('employee/loan-report*') || request()->is('crm-reimbursement-request*') || request()->is('crm-reimbursement-track*') ? 'active show-subnav' : '' }}">
                <div class="sub-menu-nav" data-subnav="subnavHrms">
                    <!-- <i class="ico icon-outline-document-text"></i> -->
                    <img src="{{ asset('public/design') }}/assets/images/icons/management.png" height="24px" title="HRMS">
                    <span class="nav-text">HRMS</span>
                </div>
                <div class="subnav-menu" id="subnavHrms">
                    <div class="sub-nav-item {{ request()->is('company/policy*') ? 'active' : '' }}">
                        @if (count($hrms->where('is_read', 1)->where('module_link_id', 66)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('company/policy') }}" class="sub-nav-link">Company Policy
                            </a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ request()->is('staff-directory*') ? 'active' : '' }}">

                        @if (count($hrms->where('is_read', 1)->where('module_link_id', 67)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('staff-directory') }}" class="sub-nav-link">Employee Management</a>
                        @endif

                    </div>

                    @php
                        $canViewLeaveRequest = count($hrms->where('is_read', 1)->where('module_link_id', 68)) > 0 || Auth::user()->role_id == 1;
                        $canViewLeaveTrack = count($hrms->where('is_read', 1)->where('module_link_id', 68)) > 0 || Auth::user()->role_id == 1;
                        $canViewLeaveMenu = $canViewLeaveRequest || $canViewLeaveTrack;
                        $isLeaveMenuActive = request()->is('approvals/inbox*') || request()->is('approvals/leave-track*');
                    @endphp

                    @if($canViewLeaveMenu)
                        <div class="nav-item sub-nav-item hrms-nested-menu {{ $isLeaveMenuActive ? 'show-subnav' : '' }}">
                            <div class="sub-menu-nav" data-subnav="subnavHrmsLeaves">
                                <span class="sub-nav-link" style="color: #4b505e !important;">Leave Management</span>
                                <span class="hrms-submenu-arrow">&rsaquo;</span>
                            </div>
                            <div class="subnav-menu" id="subnavHrmsLeaves">
                                @if($canViewLeaveRequest)
                                    <div class="sub-nav-item {{ request()->is('approvals/inbox*') ? 'active' : '' }}">
                                        <a href="{{ url('approvals/inbox') }}" class="sub-nav-link">Leave Request</a>
                                    </div>
                                @endif

                                @if($canViewLeaveTrack)
                                    <div class="sub-nav-item {{ request()->is('approvals/leave-track*') ? 'active' : '' }}">
                                        <a href="{{ route('approvals.leave-track') }}" class="sub-nav-link">Leave Track</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="sub-nav-item {{ request()->is('employee/leaves*') ? 'active' : '' }}">

                        @if (count($hrms->where('is_read', 1)->where('module_link_id', 69)) > 0)
                            <a href="{{ url('employee/leaves/') }}" class="sub-nav-link">Leaves </a>
                        @endif

                    </div>


                    <div class="sub-nav-item {{ request()->is('attendance.index*') ? 'active' : '' }}">

                        {{-- @if (count($hrms->where('is_read', 1)->where('module_link_id', 69)) > 0) --}}
                        <a href="{{  route('attendance.index') }}" class="sub-nav-link">Attendance </a>
                        {{-- @endif --}}

                    </div>

                    @php
                        $loanModuleLinks = $loanSidebarModuleLinks;
                        $canViewLoanRequest = in_array(Auth::user()->role_id, [1, 2]) || count($hrms->where('is_read', 1)->where('module_link_id', $loanModuleLinks['loans_advances'] ?? 0)) > 0;
                        $canViewLoanTrack = in_array(Auth::user()->role_id, [1, 2]) || count($hrms->where('is_read', 1)->where('module_link_id', $loanModuleLinks['loan_track'] ?? 0)) > 0;
                        $canViewLoanReport = in_array(Auth::user()->role_id, [1, 2]) || count($hrms->where('is_read', 1)->where('module_link_id', $loanModuleLinks['loan_report'] ?? 0)) > 0;
                        $canViewLoanMenu = $canViewLoanRequest || $canViewLoanTrack || $canViewLoanReport;
                        $isLoanMenuActive = request()->is('employee/loans*') || request()->is('employee/loan-track-approval-list*') || request()->is('employee/loan-report*');
                    @endphp

                    @if($canViewLoanMenu)
                        <div class="nav-item sub-nav-item hrms-nested-menu {{ $isLoanMenuActive ? 'show-subnav' : '' }}">
                            <div class="sub-menu-nav" data-subnav="subnavHrmsLoans">
                                <span class="sub-nav-link">Loans &amp; Advances</span>
                                <span class="hrms-submenu-arrow">&rsaquo;</span>
                            </div>
                            <div class="subnav-menu" id="subnavHrmsLoans">
                                @if($canViewLoanRequest)
                                    <div class="sub-nav-item {{ request()->is('employee/loans*') ? 'active' : '' }}">
                                        <a href="{{  route('employee.loans.index') }}" class="sub-nav-link">Loan Request</a>
                                    </div>
                                @endif

                                @if($canViewLoanTrack)
                                    <div
                                        class="sub-nav-item {{ request()->is('employee/loan-track-approval-list*') ? 'active' : '' }}">
                                        <a href="{{ route('employee.loans.approvals') }}" class="sub-nav-link">Loan Track</a>
                                    </div>
                                @endif

                                @if($canViewLoanReport)
                                    <div class="sub-nav-item {{ request()->is('employee/loan-report*') ? 'active' : '' }}">
                                        <a href="{{ route('employee.loans.report') }}" class="sub-nav-link">Loan Report</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="sub-nav-item {{ request()->is('employee.loans.index*') ? 'active' : '' }}">
                        {{-- @if (count($hrms->where('is_read', 1)->where('module_link_id', 69)) > 0) --}}
                        <a href="{{  route('staff.compensation.create') }}" class="sub-nav-link">Compensation & Roles
                            Changes </a>
                        {{-- @endif --}}
                    </div>


                    <div class="sub-nav-item {{ request()->is('employee.loans.index*') ? 'active' : '' }}">
                        {{-- @if (count($hrms->where('is_read', 1)->where('module_link_id', 69)) > 0) --}}
                        <a href="{{  route('staff.resignation.add') }}" class="sub-nav-link">End of Service </a>
                        {{-- @endif --}}
                    </div>


                    @php
                        $reimbursementRequestLinkIds = $reimbursementSidebarModuleLinks->only(['crm-reimbursement-request', 'reimbursement_list'])->values()->toArray();
                        if (!count($reimbursementRequestLinkIds)) {
                            $reimbursementRequestLinkIds = [70];
                        }
                        $reimbursementTrackLinkId = $reimbursementSidebarModuleLinks['crm-reimbursement-track'] ?? 0;
                        $canViewReimbursementRequest = in_array(Auth::user()->role_id, [1, 2]) || count($hrms->where('is_read', 1)->wherein('module_link_id', $reimbursementRequestLinkIds)) > 0;
                        $canViewReimbursementTrack = in_array(Auth::user()->role_id, [1, 2]) || count($hrms->where('is_read', 1)->where('module_link_id', $reimbursementTrackLinkId)) > 0;
                        $canViewReimbursementMenu = $canViewReimbursementRequest || $canViewReimbursementTrack;
                        $isReimbursementMenuActive = request()->is('crm-reimbursement-request*') || request()->is('crm-reimbursement-track*');
                    @endphp

                    @if($canViewReimbursementMenu)
                        <div
                            class="nav-item sub-nav-item hrms-nested-menu {{ $isReimbursementMenuActive ? 'show-subnav' : '' }}">
                            <div class="sub-menu-nav" data-subnav="subnavHrmsReimbursement">
                                <span class="sub-nav-link">Reimbursement</span>
                                <span class="hrms-submenu-arrow">&rsaquo;</span>
                            </div>
                            <div class="subnav-menu" id="subnavHrmsReimbursement">
                                @if ($canViewReimbursementRequest)
                                    <div class="sub-nav-item {{ request()->is('crm-reimbursement-request*') ? 'active' : '' }}">
                                        <a href="{{ url('crm-reimbursement-request') }}" class="sub-nav-link">Reimb.
                                            Request</a>
                                    </div>
                                @endif

                                @if ($canViewReimbursementTrack)
                                    <div class="sub-nav-item {{ request()->is('crm-reimbursement-track*') ? 'active' : '' }}">
                                        <a href="{{ url('crm-reimbursement-track') }}" class="sub-nav-link">Reimb. Track</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif






                </div>
            </li>
        @endif


        <?php $hrms = $permissions->wherein('module_link_id', [66, 67, 68, 69, 70]); ?>
        @if (count($hrms->where('is_read', 1)) > 0 || Auth::user()->role_id == 1)
            <li class="nav-item">
                <div class="sub-menu-nav" data-subnav="subnavMarketing">
                    <!-- <i class="ico icon-outline-document-text"></i> -->
                    <img src="{{ asset('public/design') }}/assets/images/icons/management.png" height="24px" title="HRMS">
                    <span class="nav-text">Marketing</span>
                </div>
                <div class="subnav-menu" id="subnavMarketing">
                    <div class="sub-nav-item">
                        <a href="{{ url('company/policy') }}" class="sub-nav-link">A
                        </a>
                    </div>
                    <div class="sub-nav-item">
                        <a href="{{ url('company/policy') }}" class="sub-nav-link">B
                        </a>
                    </div>

                    <div class="sub-nav-item">
                        <a href="{{ url('company/policy') }}" class="sub-nav-link">C
                        </a>
                    </div>



                </div>
            </li>
        @endif



        {{-- Service --}}
        <?php $service = $permissions->wherein('module_link_id', [53, 54, 55, 57, 58]); ?>
        @if (count($service->where('is_read', 1)) > 0 || Auth::user()->role_id == 1)
            <li
                class="nav-item {{ request()->is('crm-amc-list*') || request()->is('crm-ps-track-service-list*') || request()->is('crm-deal-support-list*') || request()->is('crm-engineer-tracking*') || request()->is('crm-amc-service-request-list*') || request()->is('crm-ps-service-list-req*') || request()->is('crm-deal-support-requested-list*') ? 'active show-subnav' : '' }}">
                <div class="sub-menu-nav" data-subnav="subnavServiceDesk">
                    <!-- <i class="ico icon-outline-headphones-round"></i> -->
                    <img src="{{ asset('public/design') }}/assets/images/icons/service.png" height="24px"
                        title="Service Desk">
                    <span class="nav-text">Service Desk</span>
                </div>
                <div class="subnav-menu" id="subnavServiceDesk">
                    <div
                        class="sub-nav-item {{ request()->is('crm-amc-list*') ? 'active' : '' }} {{ request()->is('crm-amc-service-request-list*') ? 'active' : '' }}">
                        @if (count($service->where('is_read', 1)->where('module_link_id', 53)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('crm-amc-list') }}" class="sub-nav-link">Annual Maintenance Contract</a>
                        @endif
                    </div>
                    <div
                        class="sub-nav-item {{ request()->is('crm-ps-track-service-list*') ? 'active' : '' }} {{ request()->is('crm-ps-service-list-req*') ? 'active' : '' }}">
                        @if (count($service->where('is_read', 1)->where('module_link_id', 54)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('crm-ps-track-service-list') }}" class="sub-nav-link">Project Service
                                Request</a>
                        @endif
                    </div>
                    <div
                        class="sub-nav-item {{ request()->is('crm-deal-support-list*') ? 'active' : '' }} {{ request()->is('crm-deal-support-requested-list*') ? 'active' : '' }}">
                        @if (count($service->where('is_read', 1)->where('module_link_id', 55)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('crm-deal-support-list') }}" class="sub-nav-link">Pre-Sales Request</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ request()->is('crm-engineer-tracking*') ? 'active' : '' }}">
                        @if (count($service->where('is_read', 1)->where('module_link_id', 57)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('crm-engineer-tracking') }}" class="sub-nav-link">Service Request
                                List</a>
                        @endif
                    </div>

                </div>
            </li>
        @endif
        {{-- Service --}}

        {{-- Execution Desk --}}
        <?php $execution_desk = $permissions->wherein('module_link_id', [63]); ?>
        @if (count($execution_desk->where('is_read', 1)) > 0 || Auth::user()->role_id == 1)
            <li
                class="nav-item {{ request()->is('crm-user-tasks*') || request()->is('user-todo-list*') || request()->is('tasks-assigned-by-me*') ? 'active show-subnav' : '' }}">
                <div class="sub-menu-nav" data-subnav="subnavDatabase">
                    <!-- <i class="ico icon-outline-database"></i> -->
                    <img src="{{ asset('public/design') }}/assets/images/icons/execution-desk.png" height="24px"
                        title="Execution Desk">
                    <span class="nav-text">Execution Desk</span>
                </div>
                <div class="subnav-menu" id="subnavDatabase">
                    <div
                        class="sub-nav-item {{ request()->is('crm-user-tasks*') ? 'active' : '' }}  {{ request()->is('tasks-assigned-by-me*') ? 'active' : '' }}">
                        <a href="{{ url('crm-user-tasks') }}" class="sub-nav-link">Task</a>
                    </div>
                    <div class="sub-nav-item {{ request()->is('user-todo-list*') ? 'active' : '' }}">
                        <a href="{{ url('user-todo-list') }}" class="sub-nav-link">Todo List</a>

                    </div>
                    <div class="sub-nav-item">
                        <a href="#" class="sub-nav-link">Notes</a>
                        <a href="#" class="sub-nav-link">Activity Tracker</a>
                    </div>
                </div>
            </li>
        @endif
        {{-- Execution Desk --}}





        <?php $hrms = $permissions->wherein('module_link_id', [66, 67, 68, 69, 70]); ?>
        @if (count($hrms->where('is_read', 1)) > 0 || Auth::user()->role_id == 1)
            <li class="nav-item">
                <div class="sub-menu-nav" data-subnav="subnavAuditing">
                    <!-- <i class="ico icon-outline-document-text"></i> -->
                    <img src="{{ asset('public/design') }}/assets/images/icons/management.png" height="24px" title="HRMS">
                    <span class="nav-text">Auditing</span>
                </div>
                <div class="subnav-menu" id="subnavAuditing">
                    <div class="sub-nav-item">
                        <a href="{{ url('company/policy') }}" class="sub-nav-link">Transfer Pricing
                        </a>
                    </div>
                    <div class="sub-nav-item">
                        <a href="{{ url('company/policy') }}" class="sub-nav-link">Inhouse Financial Statement
                        </a>
                    </div>

                    <div class="sub-nav-item">
                        <a href="{{ url('company/policy') }}" class="sub-nav-link">Audit Report
                        </a>
                    </div>

                    <div class="sub-nav-item">
                        <a href="" class="sub-nav-link">Zakat
                        </a>
                    </div>



                </div>
            </li>
        @endif


        {{-- Reports --}}
        <?php $crm = $permissions->wherein('module_link_id', [30, 31, 32, 33, 34, 61, 62]); ?>
        @if (count($crm->where('is_read', 1)) > 0 || Auth::user()->role_id == 1)
            <li
                class="nav-item {{ request()->is('inventory-report*') || request()->is('inventory-brand-report*') || request()->is('inventory-brand-wise-report*') || request()->is('inventory-category-wise-report*') || request()->is('inventory-subcategory-wise-report*') || request()->is('inventory-company-wise-report*') || request()->is('inventory-salesperson-wise-report*') || request()->is('inventory-brand-report-detail*') || request()->is('sales-invoice-report*') || request()->is('sales-invoice-report-detail*') || request()->is('generalledger*') || request()->is('trial-balance*') || request()->is('trading-account*') || request()->is('profit-and-loss-account*') || request()->is('balancesheet*') ? 'active show-subnav' : '' }}">
                <div class="sub-menu-nav" data-subnav="subnavReports">
                    <!-- <i class="ico icon-outline-document-text"></i> -->
                    <img src="{{ asset('public/design') }}/assets/images/icons/report.png" height="24px" title="Reports">
                    <span class="nav-text">Reports</span>
                </div>
                <div class="subnav-menu" id="subnavReports">
                    <div class="sub-nav-item {{ request()->is('inventory-report*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 62)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('inventory-report') }}" class="sub-nav-link">Inventory Report</a>
                        @endif
                    </div>
                    <div
                        class="sub-nav-item {{ request()->is('inventory-brand-report*') || request()->is('inventory-brand-wise-report*') || request()->is('inventory-category-wise-report*') || request()->is('inventory-subcategory-wise-report*') || request()->is('inventory-company-wise-report*') || request()->is('inventory-salesperson-wise-report*') || request()->is('inventory-brand-report-detail*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 62)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('inventory-brand-report') }}" class="sub-nav-link">Inventory Brand Report</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ request()->is('sales-invoice-report*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 61)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('sales-invoice-report') }}" class="sub-nav-link">Daily Sales Report</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ request()->is('sales-invoice-report-detail*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 61)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('sales-invoice-report-detail') }}" class="sub-nav-link">Sales Report</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ request()->is('generalledger*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 30)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('generalledger') }}" class="sub-nav-link">General Ledger</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ request()->is('trial-balance*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 31)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('trial-balance') }}" class="sub-nav-link">Trial Balance</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ request()->is('trading-account*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 32)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('trading-account') }}" class="sub-nav-link">Trading Account</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ request()->is('profit-and-loss-account*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 33)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('profit-and-loss-account') }}" class="sub-nav-link">Profit & Loss
                                Account</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ request()->is('balancesheet*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 34)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('balancesheet') }}" class="sub-nav-link">Balancesheet</a>
                        @endif
                    </div>
                </div>
            </li>
        @endif
        {{-- Reports --}}

        {{-- System Settings --}}
        <?php $crm = $permissions->wherein('module_link_id', [35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 52, 56]); ?>
        @if (count($crm->where('is_read', 1)) > 0 || Auth::user()->role_id == 1)
            <li
                class="nav-item {{ request()->is('company*') || request()->is('role*') || request()->is('module*') || request()->is('base-setup*') || request()->is('daily-quotes*') || request()->is('currency-settings*') || request()->is('payment-terms*') || request()->is('payment-cheque-print-template*') || request()->is('shipping-add*') || request()->is('vat-settings*') || request()->is('accountgroup-add*') || request()->is('general-settings*') || request()->is('background-setting*') || request()->is('backup-settings*') ? 'active show-subnav' : '' }}">
                <div class="sub-menu-nav" data-subnav="subnavSettings">
                    <!-- <i class="ico icon-outline-settings"></i> -->
                    <img src="{{ asset('public/design') }}/assets/images/icons/settings.png" height="24px" title="Settings">
                    <span class="nav-text">Settings</span>
                </div>
                <div class="subnav-menu" id="subnavSettings">
                    <div class="sub-nav-item {{ request()->is('role*') ? 'active' : '' }}">

                        {{-- @if (count($crm->where('is_read', 1)->where('module_link_id', 36)) > 0 || Auth::user()->role_id
                        == 1)
                        <a href="{{ url('department') }}" class="sub-nav-link">@lang('Department')</a>
                        @endif
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 37)) > 0 || Auth::user()->role_id ==
                        1)
                        <a href="{{ url('designation') }}" class="sub-nav-link">@lang('Designation')</a>
                        @endif --}}
                        {{-- @if (count($crm->where('is_read', 1)->where('module_link_id', 38)) > 0 || Auth::user()->role_id
                        == 1)
                        <a href="{{ route('role') }}" class="sub-nav-link">@lang('lang.role')</a>
                        @endif --}}

                    </div>

                    <div class="sub-nav-item {{ request()->is('company*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 35)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ route('company') }}" class="sub-nav-link">@lang('Company Settings')</a>
                        @endif
                        {{-- @if (count($crm->where('is_read', 1)->where('module_link_id', 36)) > 0 || Auth::user()->role_id
                        == 1)
                        <a href="{{ url('department') }}" class="sub-nav-link">@lang('Department')</a>
                        @endif
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 37)) > 0 || Auth::user()->role_id ==
                        1)
                        <a href="{{ url('designation') }}" class="sub-nav-link">@lang('Designation')</a>
                        @endif --}}


                    </div>

                    {{-- @if (count($crm->where('is_read', 1)->where('module_link_id', 39)) > 0 || Auth::user()->role_id ==
                    1)
                    <a href="{{ route('staff_directory') }}" class="sub-nav-link">@lang('User')</a>
                    @endif --}}

                    {{-- <div class="sub-nav-item {{ request()->is('module*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 40)) > 0 || Auth::user()->role_id ==
                        1)
                        <a href="{{ url('module') }}" class="sub-nav-link">@lang('Module')</a>
                        @endif
                    </div> --}}
                    {{-- <div class="sub-nav-item {{ request()->is('base-setup*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 41)) > 0 || Auth::user()->role_id ==
                        1)
                        <a href="{{ route('base_setup') }}" class="sub-nav-link">@lang('lang.base_setup')</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ request()->is('daily-quotes*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 52)) > 0 || Auth::user()->role_id ==
                        1)
                        <a href="{{ route('daily-quotes.index') }}" class="sub-nav-link">@lang('Daily Quote')</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ request()->is('currency-settings*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 42)) > 0 || Auth::user()->role_id ==
                        1)
                        <a href="{{ url('currency-settings') }}" class="sub-nav-link">@lang('Manage Currency')</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ request()->is('payment-terms*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 43)) > 0 || Auth::user()->role_id ==
                        1)
                        <a href="{{ url('payment-terms') }}" class="sub-nav-link">@lang('Payment Terms')</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ request()->is('payment-cheque-print-template*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 56)) > 0 || Auth::user()->role_id ==
                        1)
                        <a href="{{ url('payment-cheque-print-template') }}" class="sub-nav-link">@lang('Cheque Print
                            Template')</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ request()->is('shipping-add*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 44)) > 0 || Auth::user()->role_id ==
                        1)
                        <a href="{{ url('shipping-add') }}" class="sub-nav-link">@lang('Shipping')</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ request()->is('vat-settings*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 45)) > 0 || Auth::user()->role_id ==
                        1)
                        <a href="{{ url('vat-settings') }}" class="sub-nav-link">@lang('VAT Settings')</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ request()->is('accountgroup-add*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 46)) > 0 || Auth::user()->role_id ==
                        1)
                        <a href="{{ url('accountgroup-add') }}" class="sub-nav-link">@lang('Main Heads')</a>
                        @endif
                    </div> --}}
                    <div class="sub-nav-item {{ request()->is('general-settings*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 47)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('general-settings') }}" class="sub-nav-link">@lang('lang.general_settings')</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ request()->is('background-setting*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 48)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('background-setting') }}" class="sub-nav-link">@lang('lang.background_settings')</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ request()->is('backup-settings*') ? 'active' : '' }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 49)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('backup-settings') }}" class="sub-nav-link">@lang('lang.backup_settings')</a>
                        @endif
                    </div>
                    @if(Auth::user()->role_id == 1)
                        <div class="sub-nav-item">
                            @if (count($crm->where('is_read', 1)->where('module_link_id', 49)) > 0 || Auth::user()->role_id == 1)
                                <a href="{{ url('delete-all-data') }}" class="sub-nav-link">@lang('Delete All Data')</a>
                            @endif
                        </div>
                    @endif
                </div>
            </li>
        @endif
        {{-- System Settings --}}
    </ul>
</nav>
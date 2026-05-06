<nav class="main-nav sidebar-new">
    <div class="toggle-nav"></div>
    <ul class="nav-list">
        <li class="nav-item {{ @App\SysHelper::isMenuOpen(['crm-dashboard'], 'active show-subnav') }}">
            <a href="{{ url('/crm-dashboard') }}" class="nav-link">
                <!-- <i class="ico icon-outline-widget-6"></i> -->
                <img src="{{ asset('public/design') }}/assets/images/icons/dashboard.png" height="24px" title="Dashboard">
                <span class="nav-text">Dashboard</span>
            </a>
        </li>


        
        {{-- CRM --}}
        <?php $crm = $permissions->wherein('module_link_id', [5, 6, 7, 8, 51]); ?>
        @if (count($crm->where('is_read', 1)) > 0 || Auth::user()->role_id == 1)
            <li
                class="nav-item {{ @App\SysHelper::isMenuOpen(['crm-leads/show', 'crm-deals', 'crm-deal-track-approval-list', 'crm-deal-track-status', 'crm-deals-sales-report-company', 'crm-deals-brand-sales-report-new','crm-deals-sales-report','crm-deals-sales-report-list'], 'active show-subnav') }}">
                <div class="sub-menu-nav" data-subnav="subnavCRM">
                    <!-- <i class="ico icon-outline-calculator"></i> -->
                    <img src="{{ asset('public/design') }}/assets/images/icons/crm.png" height="24px" title="CRM">
                    <span class="nav-text">CRM</span>
                </div>
                <div class="subnav-menu" id="subnavCRM">
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('crm-leads') }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 5)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('crm-leads/show') }}" class="sub-nav-link">Leads</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('crm-deals') }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 6)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('crm-deals/show') }}" class="sub-nav-link">Deals</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('crm-deal-track-approval-list') }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 7)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('crm-deal-track-approval-list') }}" class="sub-nav-link">Deals Track</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('crm-deal-track-status') }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 8)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('crm-deal-track-status') }}" class="sub-nav-link">Deals Track Status</a>
                        @endif
                    </div>

                    @if (count($crm->where('is_read', 1)->where('module_link_id', 51)) > 0 || Auth::user()->role_id == 1)
                        <div
                            class="sub-nav-item {{ @App\SysHelper::isActiveRoute('crm-deals-sales-report-company') }} {{ @App\SysHelper::isActiveRoute('crm-deals-sales-report') }} {{ @App\SysHelper::isActiveRoute('crm-deals-sales-report-list') }}">
                            <a href="{{ url('crm-deals-sales-report-company') }}" class="sub-nav-link">Sales Report</a>
                        </div>
                        <div
                            class="sub-nav-item {{ @App\SysHelper::isActiveRoute('crm-deals-brand-sales-report-new') }}">
                            <a href="{{ url('crm-deals-brand-sales-report-new') }}" class="sub-nav-link">Brand Sales
                                Report</a>
                        </div>
                    @endif

                </div>
            </li>
        @endif
        {{-- CRM --}}

        {{-- Accounts --}}
        <?php $crm = $permissions->wherein('module_link_id', [1, 2, 3, 4, 60]); ?>
        @if (count($crm->where('is_read', 1)) > 0 || Auth::user()->role_id == 1)
            <li
                class="nav-item {{ @App\SysHelper::isMenuOpen(['chartofaccounts', 'chartofaccounts-opening-balance', 'journalvoucher', 'cashbook', 'bankbook', 'stl-report','accountgroupsub-add','accountgroupsub2-add','chartofaccounts-add','chartofaccounts-add-sub','stl-supplier-report'], 'active show-subnav') }}">
                <div class="sub-menu-nav" data-subnav="subnavAccounts">
                    <!-- <i class="ico icon-outline-calculator"></i> -->
                    <img src="{{ asset('public/design') }}/assets/images/icons/accounts.png" height="24px"
                        title="Accounts">
                    <span class="nav-text">Accounts</span>
                </div>
                <div class="subnav-menu" id="subnavAccounts">
                    @if (count($crm->where('is_read', 1)->where('module_link_id', 1)) > 0 || Auth::user()->role_id == 1)
                        <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('chartofaccounts') }} {{ @App\SysHelper::isActiveRoute('accountgroupsub-add') }} {{ @App\SysHelper::isActiveRoute('accountgroupsub2-add') }} {{ @App\SysHelper::isActiveRoute('chartofaccounts-add') }} {{ @App\SysHelper::isActiveRoute('chartofaccounts-add-sub') }}">
                            <a href="{{ url('chartofaccounts') }}" class="sub-nav-link ">Chart of Accounts</a>
                        </div>

                        <div
                            class="sub-nav-item {{ @App\SysHelper::isActiveRoute('chartofaccounts-opening-balance') }}">
                            <a href="{{ url('chartofaccounts-opening-balance') }}" class="sub-nav-link ">Opening
                                Balance</a>
                        </div>
                    @endif

                    @if (count($crm->where('is_read', 1)->where('module_link_id', 2)) > 0 || Auth::user()->role_id == 1)
                        <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('journalvoucher') }}">
                            <a href="{{ url('journalvoucher') }}" class="sub-nav-link">Journal Voucher</a>
                        </div>
                    @endif
                    @if (count($crm->where('is_read', 1)->where('module_link_id', 3)) > 0 || Auth::user()->role_id == 1)
                        <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('cashbook') }}">
                            <a href="{{ url('cashbook') }}" class="sub-nav-link ">Cash Book</a>
                        </div>
                    @endif
                    @if (count($crm->where('is_read', 1)->where('module_link_id', 4)) > 0 || Auth::user()->role_id == 1)
                        <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('bankbook') }}">
                            <a href="{{ url('bankbook') }}" class="sub-nav-link ">Bank Book</a>
                        </div>
                    @endif
                    @if (count($crm->where('is_read', 1)->where('module_link_id', 60)) > 0 || Auth::user()->role_id == 1)
                        <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('stl-report') }}  {{ @App\SysHelper::isActiveRoute('stl-supplier-report') }}">
                            <a href="{{ url('stl-report') }}" class="sub-nav-link ">STL Report</a>
                        </div>
                    @endif
                </div>
            </li>
        @endif
        {{-- Accounts --}}

          {{-- Purchase --}}
        <?php $crm = $permissions->wherein('module_link_id', [9, 10, 11, 12, 13, 14, 15, 65]); ?>
        @if (count($crm->where('is_read', 1)) > 0 || Auth::user()->role_id == 1)
            <li
                class="nav-item {{ @App\SysHelper::isMenuOpen(['suppliers', 'purchase-order', 'goods-receipt-note-list', 'purchase-invoice', 'purchase-return', 'payment', 'payables-outstanding', 'pi-adjustment-report', 'supplier-ageing-report'], 'active show-subnav') }}">
                <div class="sub-menu-nav" data-subnav="subnavPurchase">
                    <!-- <i class="ico icon-bold-cart-large-4"></i> -->
                    <img src="{{ asset('public/design') }}/assets/images/icons/purchase.png" height="24px"
                        title="Purchase">
                    <span class="nav-text">Purchase</span>
                </div>
                <div class="subnav-menu" id="subnavPurchase">
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('suppliers') }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 9)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('suppliers') }}" class="sub-nav-link">Supplier Register</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('purchase-order') }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 10)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('purchase-order') }}" class="sub-nav-link">Purchase Order</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('goods-receipt-note-list') }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 11)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('goods-receipt-note-list') }}" class="sub-nav-link">Goods Receipt
                                Note</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('purchase-invoice') }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 12)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('purchase-invoice') }}" class="sub-nav-link">Purchase Invoice</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('purchase-return') }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 13)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('purchase-return') }}" class="sub-nav-link">Purchase Return</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('payment') }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 14)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('payment') }}" class="sub-nav-link">Payments</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('payables-outstanding') }} {{ @App\SysHelper::isActiveRoute('supplier-ageing-report') }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 15)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('payables-outstanding') }}" class="sub-nav-link">Payables Outstanding</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('pi-adjustment-report') }}">
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
                class="nav-item {{ @App\SysHelper::isMenuOpen(['customers', 'quotations', 'proforma-invoice', 'sales-invoice', 'delivery-note', 'sales-return', 'receipt', 'receivable-outstanding', 'si-adjustment-report', 'clearance','customer-ageing-report'], 'active show-subnav') }}">
                <div class="sub-menu-nav" data-subnav="subnavSales">
                    <!-- <i class="ico icon-outline-bag-4"></i> -->
                    <img src="{{ asset('public/design') }}/assets/images/icons/sales.png" height="24px"
                        title="Sales">
                    <span class="nav-text">Sales</span>
                </div>
                <div class="subnav-menu" id="subnavSales">
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('customers') }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 16)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('customers') }}" class="sub-nav-link">Customer Register</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('quotations') }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 17)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('quotations') }}" class="sub-nav-link">Quotation</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('proforma-invoice') }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 18)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('proforma-invoice') }}" class="sub-nav-link">Proforma Invoice</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('sales-invoice') }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 19)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('sales-invoice') }}" class="sub-nav-link">Sales Invoice</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('delivery-note') }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 20)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('delivery-note') }}" class="sub-nav-link">Delivery Note</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('sales-return') }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 21)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('sales-return') }}" class="sub-nav-link">Sales Return</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('receipt') }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 22)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('receipt') }}" class="sub-nav-link">Receipts</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('receivable-outstanding') }} {{ @App\SysHelper::isActiveRoute('customer-ageing-report') }} ">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 23)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('receivable-outstanding') }}" class="sub-nav-link">Receivable
                                Outstanding</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('si-adjustment-report') }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 64)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('si-adjustment-report') }}" class="sub-nav-link">SI Adjustment
                                Report</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('clearance') }}">
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
                class="nav-item {{ @App\SysHelper::isMenuOpen(['item-add', 'item-store/show', 'stock-register', 'stock-ledger', 'stock-in', 'stock-out', 'packing-list'], 'active show-subnav') }}">
                <div class="sub-menu-nav" data-subnav="subnavInventory">
                    <!-- <i class="ico icon-outline-server"></i> -->
                    <img src="{{ asset('public/design') }}/assets/images/icons/inventory.png" height="24px"
                        title="Inventory">
                    <span class="nav-text">Inventory</span>
                </div>
                <div class="subnav-menu" id="subnavInventory">
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('item-add') }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 24)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('item-add') }}" class="sub-nav-link">Products</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('item-store/show') }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 25)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('item-store/show') }}" class="sub-nav-link">Opening Stock</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('stock-register') }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 26)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('stock-register') }}" class="sub-nav-link">Stock Register</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('stock-ledger') }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 27)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('stock-ledger') }}" class="sub-nav-link">Stock Ledger</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('stock-in') }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 28)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('stock-in') }}" class="sub-nav-link">Excess Stock</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('stock-out') }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 29)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('stock-out') }}" class="sub-nav-link">Shortage Stock</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('packing-list') }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 59)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('packing-list') }}" class="sub-nav-link">Packing List</a>
                        @endif
                    </div>
                </div>
            </li>
        @endif
        {{-- Inventory --}}



        {{-- HRMS --}}
        <?php $hrms = $permissions->wherein('module_link_id', [66, 67, 68, 69, 70]); ?>
        @if (count($hrms->where('is_read', 1)) > 0 || Auth::user()->role_id == 1)
            <li
                class="nav-item {{ @App\SysHelper::isMenuOpen(['company/policy', 'staff-directory', 'approvals', 'employee/leaves/', 'crm-reimbursement-request'], 'active show-subnav') }}">
                <div class="sub-menu-nav" data-subnav="subnavHrms">
                    <!-- <i class="ico icon-outline-document-text"></i> -->
                    <img src="{{ asset('public/design') }}/assets/images/icons/management.png" height="24px"
                        title="HRMS">
                    <span class="nav-text">HRMS</span>
                </div>
                <div class="subnav-menu" id="subnavHrms">
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('company/policy') }}">
                        @if (count($hrms->where('is_read', 1)->where('module_link_id', 66)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('company/policy') }}"  class="sub-nav-link">Company Policy
                            </a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('staff-directory') }}">

                        @if (count($hrms->where('is_read', 1)->where('module_link_id', 67)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('staff-directory') }}" class="sub-nav-link">Employee Management</a>
                        @endif

                    </div>

                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('approvals') }}">
                        @if (count($hrms->where('is_read', 1)->where('module_link_id', 68)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('approvals/inbox') }}"  class="sub-nav-link">Leave
                                Management</a>
                        @endif
                    </div>

                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('employee/leaves') }}">

                        @if (count($hrms->where('is_read', 1)->where('module_link_id', 69)) > 0)
                            <a href="{{ url('employee/leaves/') }}"  class="sub-nav-link">Leaves </a>
                        @endif

                    </div>


                      <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('attendance.index') }}">

                        {{-- @if (count($hrms->where('is_read', 1)->where('module_link_id', 69)) > 0) --}}
                            <a href="{{  route('attendance.index') }}"  class="sub-nav-link">Attendance </a>
                        {{-- @endif --}}

                    </div>

                       <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('employee.loans.index') }}">

                        {{-- @if (count($hrms->where('is_read', 1)->where('module_link_id', 69)) > 0) --}}
                            <a href="{{  route('employee.loans.index') }}"  class="sub-nav-link">Loans &amp; Advance </a>
                        {{-- @endif --}}

                    </div>

                     <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('employee.loans.index') }}">
                        {{-- @if (count($hrms->where('is_read', 1)->where('module_link_id', 69)) > 0) --}}
                            <a href="{{  route('staff.compensation.create') }}"  class="sub-nav-link">Compensation & Roles Changes </a>
                        {{-- @endif --}}
                    </div>


                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('employee.loans.index') }}">
                    {{-- @if (count($hrms->where('is_read', 1)->where('module_link_id', 69)) > 0) --}}
                    <a href="{{  route('staff.resignation.add') }}"  class="sub-nav-link">End of Service </a>
                    {{-- @endif --}}
                    </div>


                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('crm-reimbursement-request') }}">
                        @if (count($hrms->where('is_read', 1)->where('module_link_id', 70)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('crm-reimbursement-request') }}" class="sub-nav-link">Reimbursement
                                Request</a>
                        @endif
                    </div>


                   

                    

                </div>
            </li>
        @endif


          <?php $hrms = $permissions->wherein('module_link_id', [66, 67, 68, 69, 70]); ?>
        @if (count($hrms->where('is_read', 1)) > 0 || Auth::user()->role_id == 1)
            <li
                class="nav-item">
                <div class="sub-menu-nav" data-subnav="subnavMarketing">
                    <!-- <i class="ico icon-outline-document-text"></i> -->
                    <img src="{{ asset('public/design') }}/assets/images/icons/management.png" height="24px"
                        title="HRMS">
                    <span class="nav-text">Marketing</span>
                </div>
                <div class="subnav-menu" id="subnavMarketing">
                    <div class="sub-nav-item">
                            <a href="{{ url('company/policy') }}"  class="sub-nav-link">A
                            </a>
                    </div>
                    <div class="sub-nav-item">
                            <a href="{{ url('company/policy') }}"  class="sub-nav-link">B
                            </a>
                    </div>
                    
                    <div class="sub-nav-item">
                            <a href="{{ url('company/policy') }}"  class="sub-nav-link">C
                            </a>
                    </div>
                    
                    

                </div>
            </li>
        @endif



        {{-- Service --}}
        <?php $service = $permissions->wherein('module_link_id', [53, 54, 55, 57, 58]); ?>
        @if (count($service->where('is_read', 1)) > 0 || Auth::user()->role_id == 1)
            <li
                class="nav-item {{ @App\SysHelper::isMenuOpen(['crm-amc-list', 'crm-ps-track-service-list', 'crm-deal-support-list', 'crm-engineer-tracking', 'crm-amc-service-request-list', 'crm-ps-service-list-req', 'crm-deal-support-requested-list'], 'active show-subnav') }}">
                <div class="sub-menu-nav" data-subnav="subnavServiceDesk">
                    <!-- <i class="ico icon-outline-headphones-round"></i> -->
                    <img src="{{ asset('public/design') }}/assets/images/icons/service.png" height="24px"
                        title="Service Desk">
                    <span class="nav-text">Service Desk</span>
                </div>
                <div class="subnav-menu" id="subnavServiceDesk">
                    <div
                        class="sub-nav-item {{ @App\SysHelper::isActiveRoute('crm-amc-list') }} {{ @App\SysHelper::isActiveRoute('crm-amc-service-request-list') }}">
                        @if (count($service->where('is_read', 1)->where('module_link_id', 53)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('crm-amc-list') }}" class="sub-nav-link">Annual Maintenance Contract</a>
                        @endif
                    </div>
                    <div
                        class="sub-nav-item {{ @App\SysHelper::isActiveRoute('crm-ps-track-service-list') }} {{ @App\SysHelper::isActiveRoute('crm-ps-service-list-req') }}">
                        @if (count($service->where('is_read', 1)->where('module_link_id', 54)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('crm-ps-track-service-list') }}" class="sub-nav-link">Project Service
                                Request</a>
                        @endif
                    </div>
                    <div
                        class="sub-nav-item {{ @App\SysHelper::isActiveRoute('crm-deal-support-list') }} {{ @App\SysHelper::isActiveRoute('crm-deal-support-requested-list') }}">
                        @if (count($service->where('is_read', 1)->where('module_link_id', 55)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('crm-deal-support-list') }}" class="sub-nav-link">Pre-Sales Request</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('crm-engineer-tracking') }}">
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
                class="nav-item {{ @App\SysHelper::isMenuOpen(['crm-user-tasks', 'user-todo-list', 'tasks-assigned-by-me'], 'active show-subnav') }}">
                <div class="sub-menu-nav" data-subnav="subnavDatabase">
                    <!-- <i class="ico icon-outline-database"></i> -->
                    <img src="{{ asset('public/design') }}/assets/images/icons/execution-desk.png" height="24px"
                        title="Execution Desk">
                    <span class="nav-text">Execution Desk</span>
                </div>
                <div class="subnav-menu" id="subnavDatabase">
                    <div
                        class="sub-nav-item {{ @App\SysHelper::isActiveRoute('crm-user-tasks') }}  {{ @App\SysHelper::isActiveRoute('tasks-assigned-by-me') }}">
                        <a href="{{ url('crm-user-tasks') }}" class="sub-nav-link">Task</a>
                    </div>
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('user-todo-list') }}">
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
            <li
                class="nav-item">
                <div class="sub-menu-nav" data-subnav="subnavAuditing">
                    <!-- <i class="ico icon-outline-document-text"></i> -->
                    <img src="{{ asset('public/design') }}/assets/images/icons/management.png" height="24px"
                        title="HRMS">
                    <span class="nav-text">Auditing</span>
                </div>
                <div class="subnav-menu" id="subnavAuditing">
                    <div class="sub-nav-item">
                            <a href="{{ url('company/policy') }}"  class="sub-nav-link">Transfer Pricing
                            </a>
                    </div>
                    <div class="sub-nav-item">
                            <a href="{{ url('company/policy') }}"  class="sub-nav-link">Inhouse Financial Statement
                            </a>
                    </div>
                    
                    <div class="sub-nav-item">
                            <a href="{{ url('company/policy') }}"  class="sub-nav-link">Audit Report
                            </a>
                    </div>
                    
                    

                </div>
            </li>
        @endif

      
        {{-- Reports --}}
        <?php $crm = $permissions->wherein('module_link_id', [30, 31, 32, 33, 34, 61, 62]); ?>
        @if (count($crm->where('is_read', 1)) > 0 || Auth::user()->role_id == 1)
            <li
                class="nav-item {{ @App\SysHelper::isMenuOpen(['inventory-report', 'sales-invoice-report', 'generalledger', 'trial-balance', 'trading-account', 'profit-and-loss-account', 'balancesheet'], 'active show-subnav') }}">
                <div class="sub-menu-nav" data-subnav="subnavReports">
                    <!-- <i class="ico icon-outline-document-text"></i> -->
                    <img src="{{ asset('public/design') }}/assets/images/icons/report.png" height="24px"
                        title="Reports">
                    <span class="nav-text">Reports</span>
                </div>
                <div class="subnav-menu" id="subnavReports">
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('inventory-report') }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 62)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('inventory-report') }}" class="sub-nav-link">Inventory Report</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('sales-invoice-report') }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 61)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('sales-invoice-report') }}" class="sub-nav-link">Sales Report</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('generalledger') }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 30)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('generalledger') }}" class="sub-nav-link">General Ledger</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('trial-balance') }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 31)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('trial-balance') }}" class="sub-nav-link">Trial Balance</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('trading-account') }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 32)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('trading-account') }}" class="sub-nav-link">Trading Account</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('profit-and-loss-account') }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 33)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('profit-and-loss-account') }}" class="sub-nav-link">Profit & Loss
                                Account</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('balancesheet') }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 34)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('balancesheet') }}" class="sub-nav-link">Balancesheet</a>
                        @endif
                    </div>
                </div>
            </li>
        @endif
        {{-- Reports --}}

        {{-- System Settings  --}}
        <?php $crm = $permissions->wherein('module_link_id', [35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 52, 56]); ?>
        @if (count($crm->where('is_read', 1)) > 0 || Auth::user()->role_id == 1)
            <li
                class="nav-item {{ @App\SysHelper::isMenuOpen(['company', 'role', 'module', 'base-setup', 'daily-quotes', 'currency-settings', 'payment-terms', 'payment-cheque-print-template', 'shipping-add', 'vat-settings', 'accountgroup-add', 'general-settings', 'background-setting', 'backup-settings'], 'active show-subnav') }}">
                <div class="sub-menu-nav" data-subnav="subnavSettings">
                    <!-- <i class="ico icon-outline-settings"></i> -->
                    <img src="{{ asset('public/design') }}/assets/images/icons/settings.png" height="24px"
                        title="Settings">
                    <span class="nav-text">Settings</span>
                </div>
                <div class="subnav-menu" id="subnavSettings">
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('role') }}">
                      
                        {{-- @if (count($crm->where('is_read', 1)->where('module_link_id', 36)) > 0 || Auth::user()->role_id == 1)
                                    <a href="{{ url('department') }}" class="sub-nav-link">@lang('Department')</a>
                                @endif
                                @if (count($crm->where('is_read', 1)->where('module_link_id', 37)) > 0 || Auth::user()->role_id == 1)
                                    <a href="{{ url('designation') }}" class="sub-nav-link">@lang('Designation')</a>
                                @endif --}}
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 38)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ route('role') }}" class="sub-nav-link">@lang('lang.role')</a>
                        @endif

                    </div>

                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('company') }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 35)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ route('company') }}" class="sub-nav-link">@lang('Company Settings')</a>
                        @endif
                        {{-- @if (count($crm->where('is_read', 1)->where('module_link_id', 36)) > 0 || Auth::user()->role_id == 1)
                                    <a href="{{ url('department') }}" class="sub-nav-link">@lang('Department')</a>
                                @endif
                                @if (count($crm->where('is_read', 1)->where('module_link_id', 37)) > 0 || Auth::user()->role_id == 1)
                                    <a href="{{ url('designation') }}" class="sub-nav-link">@lang('Designation')</a>
                                @endif --}}
                      

                    </div>

                    {{-- @if (count($crm->where('is_read', 1)->where('module_link_id', 39)) > 0 || Auth::user()->role_id == 1)
                                    <a href="{{ route('staff_directory') }}" class="sub-nav-link">@lang('User')</a>
                                @endif --}}

                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('module') }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 40)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('module') }}" class="sub-nav-link">@lang('Module')</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('base-setup') }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 41)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ route('base_setup') }}" class="sub-nav-link">@lang('lang.base_setup')</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('daily-quotes') }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 52)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ route('daily-quotes.index') }}" class="sub-nav-link">@lang('Daily Quote')</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('currency-settings') }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 42)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('currency-settings') }}" class="sub-nav-link">@lang('Manage Currency')</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('payment-terms') }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 43)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('payment-terms') }}" class="sub-nav-link">@lang('Payment Terms')</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('payment-cheque-print-template') }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 56)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('payment-cheque-print-template') }}"
                                class="sub-nav-link">@lang('Cheque Print Template')</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('shipping-add') }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 44)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('shipping-add') }}" class="sub-nav-link">@lang('Shipping')</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('vat-settings') }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 45)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('vat-settings') }}" class="sub-nav-link">@lang('VAT Settings')</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('accountgroup-add') }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 46)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('accountgroup-add') }}" class="sub-nav-link">@lang('Main Heads')</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('general-settings') }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 47)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('general-settings') }}" class="sub-nav-link">@lang('lang.general_settings')</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('background-setting') }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 48)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('background-setting') }}" class="sub-nav-link">@lang('lang.background_settings')</a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('backup-settings') }}">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 49)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('backup-settings') }}" class="sub-nav-link">@lang('lang.backup_settings')</a>
                        @endif
                    </div>
                     @if(Auth::user()->role_id == 1 )
                    <div class="sub-nav-item">
                        @if (count($crm->where('is_read', 1)->where('module_link_id', 49)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('delete-all-data') }}" class="sub-nav-link">@lang('Delete All Data')</a>
                        @endif
                    </div>
                    @endif
                </div>
            </li>
        @endif
        {{-- System Settings  --}}
    </ul>
</nav>

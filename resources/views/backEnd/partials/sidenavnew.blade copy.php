<nav class="main-nav">
                <div class="toggle-nav"></div>
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="{{ url('/crm-dashboard') }}" class="nav-link">
                            <!-- <i class="ico icon-outline-widget-6"></i> -->
                            <img src="{{ asset('public/design') }}/assets/images/icons/dashboard.png" height="24px" title="Dashboard">
                            <span class="nav-text">Dashboard</span>
                        </a>
                    </li>

         
                    {{-- Accounts --}}
                    <?php $crm = $permissions->wherein('module_link_id',[1,2,3,4,60]);?>
                    @if(count($crm->where('is_read',1)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item">
                        <div class="sub-menu-nav" data-subnav="subnavAccounts">
                            <!-- <i class="ico icon-outline-calculator"></i> -->
                            <img src="{{ asset('public/design') }}/assets/images/icons/accounts.png" height="24px" title="Accounts">
                            <span class="nav-text">Accounts</span>
                        </div>
                        <div class="subnav-menu" id="subnavAccounts">
                            @if(count($crm->where('is_read',1)->where('module_link_id',1)) > 0 ||  Auth::user()->role_id == 1)
                            <div class="sub-nav-item">
                                <a href="{{ url('chartofaccounts') }}" class="sub-nav-link">Chart of Accounts</a>
                                <a href="{{ url('chartofaccounts-opening-balance') }}" class="sub-nav-link">Opening Balance</a>
                            </div>
                            @endif

                            @if(count($crm->where('is_read',1)->where('module_link_id',2)) > 0 ||  Auth::user()->role_id == 1)
                            <div class="sub-nav-item">
                                <a href="{{ url('journalvoucher') }}" class="sub-nav-link">Journal Voucher</a>
                            </div>
                            @endif
                            @if(count($crm->where('is_read',1)->where('module_link_id',3)) > 0 ||  Auth::user()->role_id == 1)
                            <div class="sub-nav-item">
                                <a href="{{ url('cashbook') }}" class="sub-nav-link">Cash Book</a>
                            </div>
                            @endif
                            @if(count($crm->where('is_read',1)->where('module_link_id',4)) > 0 ||  Auth::user()->role_id == 1)
                                    <div class="sub-nav-item">
                                        <a href="{{ url('bankbook') }}" class="sub-nav-link">Bank Book</a>
                                    </div>
                            @endif
                            @if(count($crm->where('is_read',1)->where('module_link_id',60)) > 0 ||  Auth::user()->role_id == 1)
                                    <div class="sub-nav-item">
                                        <a href="{{ url('stl-report') }}" class="sub-nav-link">STL Report</a>
                                    </div>
                            @endif
                        </div>
                    </li>
                    @endif
                    {{-- Accounts --}}

                    {{-- CRM --}}
                    <?php $crm = $permissions->wherein('module_link_id',[5,6,7,8,51]);?>
                    @if(count($crm->where('is_read',1)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item">
                        <div class="sub-menu-nav" data-subnav="subnavCRM">
                            <!-- <i class="ico icon-outline-calculator"></i> -->
                            <img src="{{ asset('public/design') }}/assets/images/icons/crm.png" height="24px" title="CRM">
                            <span class="nav-text">CRM</span>
                        </div>
                        <div class="subnav-menu" id="subnavCRM">
                            <div class="sub-nav-item">
                                @if(count($crm->where('is_read',1)->where('module_link_id',5)) > 0 ||  Auth::user()->role_id == 1)
                                <a href="{{url('crm-leads/show')}}"  class="sub-nav-link">Leads</a>
                                @endif
                                @if(count($crm->where('is_read',1)->where('module_link_id',6)) > 0 ||  Auth::user()->role_id == 1)
                                <a href="{{url('crm-deals/show')}}"  class="sub-nav-link">Deals</a>
                                @endif
                                @if(count($crm->where('is_read',1)->where('module_link_id',7)) > 0 || Auth::user()->role_id == 1)
                                <a href="{{url('crm-deal-track-approval-list')}}"  class="sub-nav-link">Deals Track</a>
                                @endif
                                @if(count($crm->where('is_read',1)->where('module_link_id',8)) > 0 || Auth::user()->role_id == 1)
                                <a href="{{url('crm-deal-track-status')}}"  class="sub-nav-link">Deals Track Status</a>
                                @endif
                                @if(count($crm->where('is_read',1)->where('module_link_id',51)) > 0 || Auth::user()->role_id == 1)
                                <a href="{{url('crm-deals-sales-report-company')}}"  class="sub-nav-link">Sales Report</a>
                                <a href="{{url('crm-deals-brand-sales-report-new')}}"  class="sub-nav-link">Brand Sales Report</a>
                                @endif
                            </div>
                        </div>
                    </li>
                    @endif
                    {{-- CRM --}}

                    {{-- HRMS --}}

                    <?php $crm = $permissions->wherein('module_link_id',[30,31,32,33,34,61,62]);?>
                    @if(count($crm->where('is_read',1)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item">
                        <div class="sub-menu-nav" data-subnav="subnavHrms">
                            <!-- <i class="ico icon-outline-document-text"></i> -->
                            <img src="{{ asset('public/design') }}/assets/images/icons/management.png" height="24px" title="HRMS">
                            <span class="nav-text">HRMS</span>
                        </div>
                        <div class="subnav-menu" id="subnavHrms">
                            <div class="sub-nav-item">
                                @if(count($crm->where('is_read',1)->where('module_link_id',62)) > 0 ||  Auth::user()->role_id == 1)
                                <a href="{{ url('staff-directory') }}" target="_blank" class="sub-nav-link">Company </a>
                                @endif
                                @if(count($crm->where('is_read',1)->where('module_link_id',61)) > 0 ||  Auth::user()->role_id == 1)
                                <a href="{{ url('staff-directory') }}" target="_blank" class="sub-nav-link">Employee Management</a>
                                @endif
                               
                            </div>
                        </div>
                    </li>
                    @endif
                 

                    {{-- Service --}}
                    <?php $service = $permissions->wherein('module_link_id',[53,54,55,57,58]);?>
                    @if(count($service->where('is_read',1)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item">
                        <div class="sub-menu-nav" data-subnav="subnavServiceDesk">
                            <!-- <i class="ico icon-outline-headphones-round"></i> -->
                            <img src="{{ asset('public/design') }}/assets/images/icons/service.png" height="24px" title="Service Desk">
                            <span class="nav-text">Service Desk</span>
                        </div>
                        <div class="subnav-menu" id="subnavServiceDesk">
                            <div class="sub-nav-item">
                                @if(count($service->where('is_read',1)->where('module_link_id',53)) > 0 || Auth::user()->role_id == 1)
                                <a href="{{ url('crm-amc-list') }}" target="_blank" class="sub-nav-link">Annual Maintenance Contract</a>
                                @endif
                                @if(count($service->where('is_read',1)->where('module_link_id',54)) > 0 ||  Auth::user()->role_id == 1)
                                <a href="{{ url('crm-ps-track-service-list') }}" target="_blank" class="sub-nav-link">Project Service Request</a>
                                @endif
                                @if(count($service->where('is_read',1)->where('module_link_id',55)) > 0 ||  Auth::user()->role_id == 1)
                                <a href="{{ url('crm-deal-support-list') }}" target="_blank" class="sub-nav-link">Pre-Sales Request</a>
                                @endif
                                @if(count($service->where('is_read',1)->where('module_link_id',57)) > 0 ||  Auth::user()->role_id == 1)
                                <a href="{{ url('crm-engineer-tracking') }}" target="_blank" class="sub-nav-link">Engineer Tracking</a>
                                @endif

                                @if(count($service->where('is_read',1)->where('module_link_id',58)) > 0 ||  Auth::user()->role_id == 1)
                                <a href="{{ url('crm-reimbursement-request') }}" target="_blank" class="sub-nav-link">Reimbursement Request</a>
                                @endif
                            </div>
                        </div>
                    </li>
                    @endif
                    {{-- Service --}}
                    
                    {{-- Execution Desk --}}
                    <?php $execution_desk = $permissions->wherein('module_link_id',[63]);?>
                    @if(count($execution_desk->where('is_read',1)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item">
                        <div class="sub-menu-nav" data-subnav="subnavDatabase">
                            <!-- <i class="ico icon-outline-database"></i> -->
                            <img src="{{ asset('public/design') }}/assets/images/icons/execution-desk.png" height="24px" title="Execution Desk">
                            <span class="nav-text">Execution Desk</span>
                        </div>
                        <div class="subnav-menu" id="subnavDatabase">
                            <div class="sub-nav-item">
                                <a href="{{ url('crm-user-tasks') }}" target="_blank" class="sub-nav-link">Task</a>
                                <a href="{{url('user-todo-list')}}" target="_blank" class="sub-nav-link">Todo List</a>
                                <a href="#" target="_blank" class="sub-nav-link">Notes</a>
                                <a href="#" target="_blank" class="sub-nav-link">Activity Tracker</a>
                            </div>
                        </div>
                    </li>
                    @endif
                    {{-- Execution Desk --}}
                    
                    {{-- Purchase --}}
                    <?php $crm = $permissions->wherein('module_link_id',[9,10,11,12,13,14,15,65]);?>
                    @if(count($crm->where('is_read',1)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item">
                        <div class="sub-menu-nav" data-subnav="subnavPurchase">
                            <!-- <i class="ico icon-bold-cart-large-4"></i> -->
                            <img src="{{ asset('public/design') }}/assets/images/icons/purchase.png" height="24px" title="Purchase">
                            <span class="nav-text">Purchase</span>
                        </div>
                        <div class="subnav-menu" id="subnavPurchase">
                            <div class="sub-nav-item">
                            @if(count($crm->where('is_read',1)->where('module_link_id',9)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('suppliers') }}" class="sub-nav-link">Supplier Register</a>
                            @endif
                            @if(count($crm->where('is_read',1)->where('module_link_id',10)) > 0 ||  Auth::user()->role_id == 1)
                            <a href="{{url('purchase-order')}}" class="sub-nav-link">Purchase Order</a>
                            @endif
                            @if(count($crm->where('is_read',1)->where('module_link_id',11)) > 0 ||  Auth::user()->role_id == 1)
                            <a href="{{url('goods-receipt-note-list')}}" class="sub-nav-link">Goods Receipt Note</a>
                            @endif
                            @if(count($crm->where('is_read',1)->where('module_link_id',12)) > 0 ||  Auth::user()->role_id == 1)
                            <a href="{{url('purchase-invoice')}}" class="sub-nav-link">Purchase Invoice</a>
                            @endif
                            @if(count($crm->where('is_read',1)->where('module_link_id',13)) > 0 ||  Auth::user()->role_id == 1)
                            <a href="{{url('purchase-return')}}" class="sub-nav-link">Purchase Return</a>
                            @endif
                            @if(count($crm->where('is_read',1)->where('module_link_id',14)) > 0 ||  Auth::user()->role_id == 1)
                            <a href="{{ url('payment') }}" class="sub-nav-link">Payments</a>
                            @endif
                            @if(count($crm->where('is_read',1)->where('module_link_id',15)) > 0 ||  Auth::user()->role_id == 1)
                            <a href="{{ url('payables-outstanding') }}" class="sub-nav-link">Payables Outstanding</a>
                            @endif
                            @if(count($crm->where('is_read',1)->where('module_link_id',65)) > 0 ||  Auth::user()->role_id == 1)
                            <a href="{{ url('pi-adjustment-report') }}" class="sub-nav-link">PI Adjustment Report</a>
                            @endif
                            </div>
                        </div>
                    </li>
                    @endif
                    {{-- Purchase --}}

                    {{-- Sales --}}
                    <?php $crm = $permissions->wherein('module_link_id',[16,17,18,19,20,21,22,23,50,64]);?>
                    @if(count($crm->where('is_read',1)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item">
                        <div class="sub-menu-nav" data-subnav="subnavSales">
                            <!-- <i class="ico icon-outline-bag-4"></i> -->
                            <img src="{{ asset('public/design') }}/assets/images/icons/sales.png" height="24px" title="Sales">
                            <span class="nav-text">Sales</span>
                        </div>
                        <div class="subnav-menu" id="subnavSales">
                            <div class="sub-nav-item">
                                @if(count($crm->where('is_read',1)->where('module_link_id',16)) > 0 ||  Auth::user()->role_id == 1)
                                <a href="{{ url('customers') }}" target="_blank" class="sub-nav-link">Customer Register</a>
                                @endif
                                @if(count($crm->where('is_read',1)->where('module_link_id',17)) > 0 ||  Auth::user()->role_id == 1)
                                <a href="{{url('quotations')}}" target="_blank" class="sub-nav-link">Quotation</a>
                                @endif
                                @if(count($crm->where('is_read',1)->where('module_link_id',18)) > 0 ||  Auth::user()->role_id == 1)
                                <a href="{{url('proforma-invoice')}}" target="_blank" class="sub-nav-link">Proforma Invoice</a>
                                @endif
                                @if(count($crm->where('is_read',1)->where('module_link_id',19)) > 0 ||  Auth::user()->role_id == 1)
                                <a href="{{url('sales-invoice')}}" target="_blank" class="sub-nav-link">Sales Invoice</a>
                                @endif
                                @if(count($crm->where('is_read',1)->where('module_link_id',20)) > 0 ||  Auth::user()->role_id == 1)
                                <a href="{{url('delivery-note')}}" target="_blank" class="sub-nav-link">Delivery Note</a>
                                @endif
                                @if(count($crm->where('is_read',1)->where('module_link_id',21)) > 0 ||  Auth::user()->role_id == 1)
                                <a href="{{url('sales-return')}}" target="_blank" class="sub-nav-link">Sales Return</a>
                                @endif
                                @if(count($crm->where('is_read',1)->where('module_link_id',22)) > 0 ||  Auth::user()->role_id == 1)
                                <a href="{{ url('receipt') }}" class="sub-nav-link">Receipts</a>
                                @endif
                                @if(count($crm->where('is_read',1)->where('module_link_id',23)) > 0 ||  Auth::user()->role_id == 1)
                                <a href="{{ url('receivable-outstanding') }}" class="sub-nav-link">Receivable Outstanding</a>
                                @endif
                                @if(count($crm->where('is_read',1)->where('module_link_id',64)) > 0 ||  Auth::user()->role_id == 1)
                                <a href="{{ url('si-adjustment-report') }}" class="sub-nav-link">SI Adjustment Report</a>
                                @endif

                                @if (session('logged_session_data.company_id')==2)
                                @if(count($crm->where('is_read',1)->where('module_link_id',50)) > 0 ||  Auth::user()->role_id == 1)
                                <a href="{{ url('clearance') }}" class="sub-nav-link">Customs Clearance</a>
                                @endif
                                @endif
                            </div>
                        </div>
                    </li>
                    @endif
                    {{-- Sales --}}
                    
                    {{-- Inventory --}}
                    <?php $crm = $permissions->wherein('module_link_id',[24,25,26,27,28,29,59]);?>
                    @if(count($crm->where('is_read',1)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item">
                        <div class="sub-menu-nav" data-subnav="subnavInventory">
                            <!-- <i class="ico icon-outline-server"></i> -->
                            <img src="{{ asset('public/design') }}/assets/images/icons/inventory.png" height="24px" title="Inventory">
                            <span class="nav-text">Inventory</span>
                        </div>
                        <div class="subnav-menu" id="subnavInventory">
                            <div class="sub-nav-item">
                                @if(count($crm->where('is_read',1)->where('module_link_id',24)) > 0 ||  Auth::user()->role_id == 1)
                                <a href="{{url('item-add')}}" class="sub-nav-link">Products</a>
                                @endif
                                @if(count($crm->where('is_read',1)->where('module_link_id',25)) > 0 ||  Auth::user()->role_id == 1)
                                <a href="{{url('item-store/show')}}" class="sub-nav-link">Opening Stock</a>
                                @endif
                                @if(count($crm->where('is_read',1)->where('module_link_id',26)) > 0 ||  Auth::user()->role_id == 1)
                                <a href="{{url('stock-register')}}" class="sub-nav-link">Stock Register</a>
                                @endif
                                @if(count($crm->where('is_read',1)->where('module_link_id',27)) > 0 ||  Auth::user()->role_id == 1)
                                <a href="{{url('stock-ledger')}}" class="sub-nav-link">Stock Ledger</a>
                                @endif
                                @if(count($crm->where('is_read',1)->where('module_link_id',28)) > 0 ||  Auth::user()->role_id == 1)
                                <a href="{{url('stock-in')}}" class="sub-nav-link">Excess Stock</a>
                                @endif
                                @if(count($crm->where('is_read',1)->where('module_link_id',29)) > 0 ||  Auth::user()->role_id == 1)
                                <a href="{{url('stock-out')}}" class="sub-nav-link">Shortage Stock</a>
                                @endif
                                @if(count($crm->where('is_read',1)->where('module_link_id',59)) > 0 ||  Auth::user()->role_id == 1)
                                <a href="{{url('packing-list')}}" class="sub-nav-link">Packing List</a>
                                @endif
                            </div>
                        </div>
                    </li>
                    @endif
                    {{-- Inventory --}}
                    
                    {{-- Reports --}}
                    <?php $crm = $permissions->wherein('module_link_id',[30,31,32,33,34,61,62]);?>
                    @if(count($crm->where('is_read',1)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item">
                        <div class="sub-menu-nav" data-subnav="subnavReports">
                            <!-- <i class="ico icon-outline-document-text"></i> -->
                            <img src="{{ asset('public/design') }}/assets/images/icons/report.png" height="24px" title="Reports">
                            <span class="nav-text">Reports</span>
                        </div>
                        <div class="subnav-menu" id="subnavReports">
                            <div class="sub-nav-item">
                                @if(count($crm->where('is_read',1)->where('module_link_id',62)) > 0 ||  Auth::user()->role_id == 1)
                                <a href="{{ url('inventory-report') }}" target="_blank" class="sub-nav-link">Inventory Report</a>
                                @endif
                                @if(count($crm->where('is_read',1)->where('module_link_id',61)) > 0 ||  Auth::user()->role_id == 1)
                                <a href="{{ url('sales-invoice-report') }}" target="_blank" class="sub-nav-link">Sales Report</a>
                                @endif
                                @if(count($crm->where('is_read',1)->where('module_link_id',30)) > 0 ||  Auth::user()->role_id == 1)
                                <a href="{{ url('generalledger') }}" class="sub-nav-link">General Ledger</a>
                                @endif
                                @if(count($crm->where('is_read',1)->where('module_link_id',31)) > 0 ||  Auth::user()->role_id == 1)
                                <a href="{{ url('trial-balance') }}" target="_blank" class="sub-nav-link">Trial Balance</a>
                                @endif
                                @if(count($crm->where('is_read',1)->where('module_link_id',32)) > 0 ||  Auth::user()->role_id == 1)
                                <a href="{{ url('trading-account') }}" target="_blank" class="sub-nav-link">Trading Account</a>
                                @endif
                                @if(count($crm->where('is_read',1)->where('module_link_id',33)) > 0 ||  Auth::user()->role_id == 1)
                                <a href="{{ url('profit-and-loss-account') }}" target="_blank" class="sub-nav-link">Profit & Loss Account</a>
                                @endif
                                @if(count($crm->where('is_read',1)->where('module_link_id',34)) > 0 ||  Auth::user()->role_id == 1)
                                <a href="{{ url('balancesheet') }}" target="_blank" class="sub-nav-link">Balancesheet</a>
                                @endif
                            </div>
                        </div>
                    </li>
                    @endif
                    {{-- Reports --}}
                    
                    {{-- System Settings  --}}
                    <?php $crm = $permissions->wherein('module_link_id', [35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,52,56]); ?>
                    @if (count($crm->where('is_read', 1)) > 0 || Auth::user()->role_id == 1)                    
                    <li class="nav-item">
                        <div class="sub-menu-nav" data-subnav="subnavSettings">
                            <!-- <i class="ico icon-outline-settings"></i> -->
                            <img src="{{ asset('public/design') }}/assets/images/icons/settings.png" height="24px" title="Settings">
                            <span class="nav-text">Settings</span>
                        </div>
                        <div class="subnav-menu" id="subnavSettings">
                            <div class="sub-nav-item">
                                @if (count($crm->where('is_read', 1)->where('module_link_id', 35)) > 0 || Auth::user()->role_id == 1)
                                    <a href="{{ route('company') }}" class="sub-nav-link">@lang('Company')</a>
                                @endif
                                {{-- @if (count($crm->where('is_read', 1)->where('module_link_id', 36)) > 0 || Auth::user()->role_id == 1)
                                    <a href="{{ url('department') }}" class="sub-nav-link">@lang('Department')</a>
                                @endif --}}
                                {{-- @if (count($crm->where('is_read', 1)->where('module_link_id', 37)) > 0 || Auth::user()->role_id == 1)
                                    <a href="{{ url('designation') }}" class="sub-nav-link">@lang('Designation')</a>
                                @endif --}}
                                @if (count($crm->where('is_read', 1)->where('module_link_id', 38)) > 0 || Auth::user()->role_id == 1)
                                    <a href="{{ route('role') }}" class="sub-nav-link">@lang('lang.role')</a>
                                @endif
                                @if (count($crm->where('is_read', 1)->where('module_link_id', 39)) > 0 || Auth::user()->role_id == 1)
                                    <a href="{{ route('staff_directory') }}" class="sub-nav-link">@lang('User')</a>
                                @endif
                                @if (count($crm->where('is_read', 1)->where('module_link_id', 40)) > 0 || Auth::user()->role_id == 1)
                                    <a href="{{ url('module') }}" class="sub-nav-link">@lang('Module')</a>
                                @endif
                                @if (count($crm->where('is_read', 1)->where('module_link_id', 41)) > 0 || Auth::user()->role_id == 1)
                                    <a href="{{ route('base_setup') }}" class="sub-nav-link">@lang('lang.base_setup')</a>
                                @endif
                                @if (count($crm->where('is_read', 1)->where('module_link_id', 52)) > 0 || Auth::user()->role_id == 1)
                                    <a href="{{ route('daily-quotes.index') }}" class="sub-nav-link">@lang('Daily Quote')</a>
                                @endif
                                @if (count($crm->where('is_read', 1)->where('module_link_id', 42)) > 0 || Auth::user()->role_id == 1)
                                    <a href="{{ url('currency-settings') }}" class="sub-nav-link">@lang('Manage Currency')</a>
                                @endif
                                @if (count($crm->where('is_read', 1)->where('module_link_id', 43)) > 0 || Auth::user()->role_id == 1)
                                    <a href="{{ url('payment-terms') }}" class="sub-nav-link">@lang('Payment Terms')</a>
                                @endif
                                @if (count($crm->where('is_read', 1)->where('module_link_id', 56)) > 0 || Auth::user()->role_id == 1)
                                    <a href="{{ url('payment-cheque-print-template') }}" class="sub-nav-link">@lang('Cheque Print Template')</a>
                                @endif
                                @if (count($crm->where('is_read', 1)->where('module_link_id', 44)) > 0 || Auth::user()->role_id == 1)
                                    <a href="{{ url('shipping-add') }}" class="sub-nav-link">@lang('Shipping')</a>
                                @endif
                                @if (count($crm->where('is_read', 1)->where('module_link_id', 45)) > 0 || Auth::user()->role_id == 1)
                                    <a href="{{ url('vat-settings') }}" class="sub-nav-link">@lang('VAT Settings')</a>
                                @endif
                                @if (count($crm->where('is_read', 1)->where('module_link_id', 46)) > 0 || Auth::user()->role_id == 1)
                                    <a href="{{ url('accountgroup-add') }}" class="sub-nav-link">@lang('Main Heads')</a>
                                @endif
                                @if (count($crm->where('is_read', 1)->where('module_link_id', 47)) > 0 || Auth::user()->role_id == 1)
                                    <a href="{{ url('general-settings') }}" class="sub-nav-link">@lang('lang.general_settings')</a>
                                @endif
                                @if (count($crm->where('is_read', 1)->where('module_link_id', 48)) > 0 || Auth::user()->role_id == 1)
                                    <a href="{{ url('background-setting') }}" class="sub-nav-link">@lang('lang.background_settings')</a>
                                @endif
                                @if (count($crm->where('is_read', 1)->where('module_link_id', 49)) > 0 || Auth::user()->role_id == 1)
                                    <a href="{{ url('backup-settings') }}" class="sub-nav-link">@lang('lang.backup_settings')</a>
                                @endif
                            </div>
                        </div>
                    </li>
                    @endif
                    {{-- System Settings  --}}
                </ul>
            </nav>
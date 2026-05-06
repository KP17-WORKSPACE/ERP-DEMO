<ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar">
    <!-- Sidebar - Brand -->
      
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ url('/crm-dashboard') }}">
        {{--  <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>  --}}
        {{--  <div class="sidebar-brand-text mx-3">Logo<sup>ERP</sup></div>  --}}
        <div class="sidebar-brand-text mx-3"><img src="{{ asset('public/admin-iroid/') }}/img/erp-logo.png" width="150">
        </div>
    </a>
    <!-- Divider -->
    <hr class="sidebar-divider my-0">
    <!-- Nav Item - Dashboard -->
    <li class="nav-item active">
        <a class="nav-link" href="{{ url('/crm-dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>
    <!-- Divider -->
    <hr class="sidebar-divider">
    <!-- Heading -->
    <div class="sidebar-heading">
        Pages
    </div>
    <!-- Nav Item - Pages Collapse Menu -->

    {{-- Company --}}
    <?php /* $crm = $permissions->wherein('module_link_id', [8, 9, 10, 11]); ?>
    @if (count($crm->where('is_read', 1)) > 0 || Auth::user()->role_id == 1)
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#Company"
                aria-expanded="true" aria-controls="Company">
                <i class="fas fa-fw fa-building"></i>
                <span>Company</span>
            </a>
            <div id="Company" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    
                </div>
            </div>
        </li>
    @endif */ ?>
    {{-- Company --}}

    {{-- Accounts --}}
        <?php $crm = $permissions->wherein('module_link_id',[1,2,3,4,60]);?>
    @if(count($crm->where('is_read',1)) > 0 ||  Auth::user()->role_id == 1)
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#Accounts" aria-expanded="true" aria-controls="Accounts"><i class="fas fa-fw fa-calculator"></i><span>Accounts</span></a>
            <div id="Accounts" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    @if(count($crm->where('is_read',1)->where('module_link_id',1)) > 0 ||  Auth::user()->role_id == 1)
                    <h6 class="collapse-header">Accounts:</h6>
                    <a href="{{ url('chartofaccounts') }}" class="collapse-item">@lang('Chart of Accounts')</a>
                    <a href="{{ url('chartofaccounts-opening-balance') }}" class="collapse-item">@lang('Opening Balance')</a>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',2)) > 0 ||  Auth::user()->role_id == 1)
                    <h6 class="collapse-header">Receipts & Payments:</h6>
                    <?php /*<a href="{{ url('cashreceipt-add') }}" class="collapse-item">Cash Receipt</a>
                    <a href="{{ url('bankreceipt-add') }}" class="collapse-item">Bank Receipt</a>
                    <a href="{{ url('cashpayment-add') }}" class="collapse-item">Cash Payment</a>
                    <a href="{{ url('bankpayment-add') }}" class="collapse-item">Bank Payment</a>
                    <a href="{{ url('postdatedreceipt-add') }}" class="collapse-item">Postdated Receipt</a>
                    <a href="{{ url('postdatedpayment-add') }}" class="collapse-item">Postdated Payment</a> */ ?>
                    <a href="{{ url('journalvoucher') }}" class="collapse-item">@lang('Journal Voucher')</a>
                    @endif
                    <h6 class="collapse-header">Ledger:</h6>
                    @if(count($crm->where('is_read',1)->where('module_link_id',3)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{ url('cashbook') }}" class="collapse-item">@lang('Cash Book')</a>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',4)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{ url('bankbook') }}" class="collapse-item">@lang('Bank Book')</a>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',60)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{ url('stl-report') }}" class="collapse-item">@lang('STL Report')</a>
                    @endif
                </div>
            </div>
        </li>
    @endif
    {{-- Accounts --}}
    
    {{-- CRM --}}
    <?php $crm = $permissions->wherein('module_link_id',[5,6,7,8,51]);?>
    @if(count($crm->where('is_read',1)) > 0 ||  Auth::user()->role_id == 1)
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#CRM"
                aria-expanded="true" aria-controls="CRM">
                <i class="fas fa-fw fa-tasks"></i>
                <span>CRM</span>
            </a>
            <div id="CRM" class="collapse @if(Auth::user()->id==19) show @endif" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">CRM:</h6>                    
                    <?php /*
                    @if(count($crm->where('is_read',1)->where('module_link_id',1)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{url('crm-dashboard')}}"  class="collapse-item">Dashboard</a>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',2)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{url('customers')}}"  class="collapse-item">Customers</a>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',3)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{url('price-book/show')}}"  class="collapse-item">Price Book</a>
                    @endif
                    
                    @if(count($crm->where('is_read',1)->where('module_link_id',77)) > 0 || Auth::user()->role_id == 1)
                    <a href="{{url('crm-deal-return-list')}}"  class="collapse-item">Deals Return</a>
                    @endif
                    
                    {{--  @if(Auth::user()->role_id == 1 || session('logged_session_data.department_id')==3 || Auth::user()->id==20)  --}}
                    @if(count($crm->where('is_read',1)->where('module_link_id',74)) > 0 || Auth::user()->role_id == 1)
                    <a href="{{url('crm-deal-service-list')}}"  class="collapse-item">Pre-Sales Support</a>
                    @endif

                    @if(count($crm->where('is_read',1)->where('module_link_id',75)) > 0 || Auth::user()->role_id == 1)
                    <a href="{{url('crm-deal-support-list')}}"  class="collapse-item">Service Desk</a>
                    @endif

                    @if(count($crm->where('is_read',1)->where('module_link_id',76)) > 0 || Auth::user()->role_id == 1)
                    <a href="{{url('crm-amc-deal-list')}}"  class="collapse-item">AMC List</a>
                    @endif
                    */ ?>

                    @if(count($crm->where('is_read',1)->where('module_link_id',5)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{url('crm-leads/show')}}"  class="collapse-item">Leads</a>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',6)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{url('crm-deals/show')}}"  class="collapse-item">Deals</a>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',7)) > 0 || Auth::user()->role_id == 1)
                    <a href="{{url('crm-deal-track-approval-list')}}"  class="collapse-item">Deals Track</a>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',8)) > 0 || Auth::user()->role_id == 1)
                    <a href="{{url('crm-deal-track-status')}}"  class="collapse-item">Deals Track Status</a>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',51)) > 0 || Auth::user()->role_id == 1)
                    <a href="{{url('crm-deals-sales-report-company')}}"  class="collapse-item">Sales Report</a>
                    <a href="{{url('crm-deals-brand-sales-report-new')}}"  class="collapse-item">Brand Sales Report</a>
                    @endif

                </div>
            </div>
        </li>
    @endif
    {{-- CRM --}}

    {{-- Customer --}}
    <?php /*
    <?php $crm = $permissions->wherein('module_link_id',[45]);?>
    @if(count($crm->where('is_read',1)) > 0 ||  Auth::user()->role_id == 1)
        <li class="nav-item">
            <a class="nav-link collapsed" href="{{url('customers')}}" data-toggle="collapse" data-target="#Customer" aria-expanded="true" aria-controls="Customer">
                <i class="fas fa-fw fa-user"></i>
                <span>Customer</span>
            </a>
            <div id="Customer" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a href="{{ url('customers') }}" class="collapse-item">Customer Register</a>
                    <a href="{{ url('customerledger') }}" class="collapse-item">Customer Ledger</a>
                    <a href="{{ url('customer-outstanding') }}" class="collapse-item">Customer Outstanding</a>
                    <a href="{{ url('customer-outstanding-pdc') }}" class="collapse-item">Customer Outstanding - PDC</a>
                    <a href="{{ url('customer-ageing') }}" class="collapse-item">Customer Ageing</a>
                </div>
            </div>
        </li>
    @endif
    {{-- Customer --}}
    
    {{-- Supplier --}}
    <?php $crm = $permissions->wherein('module_link_id',[46]);?>
    @if(count($crm->where('is_read',1)) > 0 ||  Auth::user()->role_id == 1)
        <li class="nav-item">
            <a class="nav-link collapsed" href="{{url('suppliers')}}" data-toggle="collapse" data-target="#Supplier" aria-expanded="true" aria-controls="Supplier">
                <i class="fas fa-fw fa-users"></i>
                <span>Supplier</span>
            </a>
            <div id="Supplier" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a href="{{ url('suppliers') }}" class="collapse-item">Supplier Register</a>
                    <a href="{{ url('supplierledger') }}" class="collapse-item">Supplier Ledger</a>
                    <a href="{{ url('supplier-outstanding') }}" class="collapse-item">Supplier Outstanding</a>
                    <a href="{{ url('supplier-outstanding-pdc') }}" class="collapse-item">Supplier Outstanding - PDC</a>
                    <a href="{{ url('supplier-ageing') }}" class="collapse-item">Supplier Ageing</a>
                </div>
            </div>
        </li>
    @endif
    {{-- Supplier --}}
    */?>


    {{-- Service --}}
    <?php $service = $permissions->wherein('module_link_id',[53,54,55,57,58]);?>
    @if(count($service->where('is_read',1)) > 0 ||  Auth::user()->role_id == 1)
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#Service"
                aria-expanded="true" aria-controls="Service">
                <i class="fas fa-fw fa-cart-plus"></i>
                <span>Service Desk</span>
            </a>
            <div id="Service" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    @if(count($service->where('is_read',1)->where('module_link_id',53)) > 0 || Auth::user()->role_id == 1)
                    <a href="{{ url('crm-amc-list') }}" target="_blank" class="collapse-item">Annual Maintenance Contract (AMC)</a>
                    @endif
                    @if(count($service->where('is_read',1)->where('module_link_id',54)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{ url('crm-ps-track-service-list') }}" target="_blank" class="collapse-item">Project Service Request</a>
                    @endif
                    @if(count($service->where('is_read',1)->where('module_link_id',55)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{ url('crm-deal-support-list') }}" target="_blank" class="collapse-item">Pre-Sales Request</a>
                    @endif
                    @if(count($service->where('is_read',1)->where('module_link_id',57)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{ url('crm-engineer-tracking') }}" target="_blank" class="collapse-item">Engineer Tracking</a>
                    @endif

                    @if(count($service->where('is_read',1)->where('module_link_id',58)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{ url('crm-reimbursement-request') }}" target="_blank" class="collapse-item">Reimbursement Request</a>
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
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#ExecutionDesk"
                aria-expanded="true" aria-controls="ExecutionDesk">
                <i class="fas fa-fw fa-edit"></i>
                <span>Execution Desk</span>
            </a>
            <div id="ExecutionDesk" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Execution Desk:</h6>
                    <a href="{{ url('crm-user-tasks') }}" target="__blank" class="collapse-item">Task</a>
                    <a href="{{url('user-todo-list')}}" target="__blank" class="collapse-item">Todo List</a>
                    <a href="#" target="__blank" class="collapse-item">Notes</a>
                    <a href="#" target="__blank" class="collapse-item">Activity Tracker</a>
                </div>
            </div>
        </li>
    @endif
    {{-- Execution Desk --}}
    
    {{-- Purchase --}}
    <?php $crm = $permissions->wherein('module_link_id',[9,10,11,12,13,14,15,65]);?>
    @if(count($crm->where('is_read',1)) > 0 ||  Auth::user()->role_id == 1)
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#Purchase"
                aria-expanded="true" aria-controls="Purchase">
                <i class="fas fa-fw fa-cart-plus"></i>
                <span>Purchase</span>
            </a>
            <div id="Purchase" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Purchase:</h6>
                    @if(count($crm->where('is_read',1)->where('module_link_id',9)) > 0 || Auth::user()->role_id == 1)
                    <a href="{{ url('suppliers') }}" target="_blank" class="collapse-item">Supplier Register</a>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',10)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{url('purchase-order')}}" target="_blank" class="collapse-item">Purchase Order</a>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',11)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{url('goods-receipt-note')}}" target="_blank" class="collapse-item">Goods Receipt Note</a>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',12)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{url('purchase-invoice')}}" target="_blank" class="collapse-item">Purchase Invoice</a>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',13)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{url('purchase-return')}}" target="_blank" class="collapse-item">Purchase Return</a>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',14)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{ url('payment') }}" class="collapse-item">Payments</a>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',15)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{ url('payables-outstanding') }}" class="collapse-item">Payables Outstanding</a>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',65)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{ url('pi-adjustment-report') }}" class="collapse-item">PI Adjustment Report</a>
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
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#Sales"
                aria-expanded="true" aria-controls="Sales">
                <i class="fas fa-fw fa-shopping-cart"></i>
                <span>Sales</span>
            </a>
            <div id="Sales" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Sales:</h6>
                    
                    @if(count($crm->where('is_read',1)->where('module_link_id',16)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{ url('customers') }}" target="_blank" class="collapse-item">Customer Register</a>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',17)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{url('quotations')}}" target="_blank" class="collapse-item">Quotation</a>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',18)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{url('proforma-invoice')}}" target="_blank" class="collapse-item">Proforma Invoice</a>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',19)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{url('sales-invoice')}}" target="_blank" class="collapse-item">Sales Invoice</a>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',20)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{url('delivery-note')}}" target="_blank" class="collapse-item">Delivery Note</a>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',21)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{url('sales-return')}}" target="_blank" class="collapse-item">Sales Return</a>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',22)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{ url('receipt') }}" class="collapse-item">Receipts</a>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',23)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{ url('receivable-outstanding') }}" class="collapse-item">Receivable Outstanding</a>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',64)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{ url('si-adjustment-report') }}" class="collapse-item">SI Adjustment Report</a>
                    @endif

                    @if (session('logged_session_data.company_id')==2)
                    @if(count($crm->where('is_read',1)->where('module_link_id',50)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{ url('clearance') }}" class="collapse-item">Customs Clearance</a>
                    @endif                        
                    @endif

                    
                    <?php /*
                    @if(count($crm->where('is_read',1)->where('module_link_id',54)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{url('delivery-note-add')}}" class="collapse-item">Delivery Note</a>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',50)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{url('clearance')}}" class="collapse-item">Customs Clearance</a>
                    @endif 
                    */ ?>
                    
                </div>
            </div>
        </li>
    @endif
    {{-- Sales --}}

    {{-- Product --}}
    @if(1==2)
    <?php $crm = $permissions->wherein('module_link_id',[]);?>
    @if(count($crm->where('is_read',1)) > 0 ||  Auth::user()->role_id == 1)
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#Product"
                aria-expanded="true" aria-controls="Product">
                <i class="fas fa-fw fa-shopping-bag"></i>
                <span>Product</span>
            </a>
            <div id="Product" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Product:</h6>
                    <?php /* @if(count($crm->where('is_read',1)->where('module_link_id',57)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{url('brand')}}" class="collapse-item">Brand</a>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',58)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{url('item-category')}}" class="collapse-item">Category</a>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',59)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{url('create-sub-category')}}" class="collapse-item">Sub Category</a>
                    @endif */ ?>
                    @if(count($crm->where('is_read',1)->where('module_link_id',60)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{url('item-add')}}" class="collapse-item">Products</a>
                    @endif
                </div>
            </div>
        </li>
    @endif
    @endif
    {{-- Product --}}

    {{-- Inventory --}}
    <?php $crm = $permissions->wherein('module_link_id',[24,25,26,27,28,29,59]);?>
    @if(count($crm->where('is_read',1)) > 0 ||  Auth::user()->role_id == 1)
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#Inventory"
                aria-expanded="true" aria-controls="Inventory">
                <i class="fas fa-fw fa-copy"></i>
                <span>Inventory</span>
            </a>

            <div id="Inventory" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Product:</h6>
                    <?php /*@if(count($crm->where('is_read',1)->where('module_link_id',57)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{url('brand')}}" class="collapse-item">Brand</a>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',58)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{url('item-category')}}" class="collapse-item">Category</a>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',59)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{url('create-sub-category')}}" class="collapse-item">Sub Category</a>
                    @endif
                    */ ?>
                    @if(count($crm->where('is_read',1)->where('module_link_id',24)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{url('item-add')}}" class="collapse-item">Products</a>
                    @endif

                    <h6 class="collapse-header">Inventory:</h6>                    
                    @if(count($crm->where('is_read',1)->where('module_link_id',25)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{url('item-store/show')}}" class="collapse-item">Opening Stock</a>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',26)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{url('stock-register')}}" class="collapse-item">Stock Register</a>
                    @endif
                    <?php /* @if(count($crm->where('is_read',1)->where('module_link_id',63)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{url('item-stock')}}" class="collapse-item">Item Stock</a>
                    @endif */ ?>
                    @if(count($crm->where('is_read',1)->where('module_link_id',27)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{url('stock-ledger')}}" class="collapse-item">Stock Ledger</a>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',28)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{url('stock-in')}}" class="collapse-item">Excess Stock</a>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',29)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{url('stock-out')}}" class="collapse-item">Shortage Stock</a>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',59)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{url('packing-list')}}" class="collapse-item">Packing List</a>
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
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#Reports"
                aria-expanded="true" aria-controls="Reports">
                <i class="fas fa-fw fa-book"></i>
                <span>Reports</span>
            </a>
            <div id="Reports" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Reports:</h6>
                    @if(count($crm->where('is_read',1)->where('module_link_id',62)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{ url('inventory-report') }}" target="_blank" class="collapse-item">Inventory Report</a>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',61)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{ url('sales-invoice-report') }}" target="_blank" class="collapse-item">Sales Report</a>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',30)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{ url('generalledger') }}" class="collapse-item">General Ledger</a>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',31)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{ url('trial-balance') }}" target="_blank" class="collapse-item">Trial Balance</a>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',32)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{ url('trading-account') }}" target="_blank" class="collapse-item">Trading Account</a>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',33)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{ url('profit-and-loss-account') }}" target="_blank" class="collapse-item">Profit & Loss Account</a>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',34)) > 0 ||  Auth::user()->role_id == 1)
                    <a href="{{ url('balancesheet') }}" target="_blank" class="collapse-item">Balancesheet</a>
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
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#SystemSettings"
                aria-expanded="true" aria-controls="SystemSettings">
                <i class="fas fa-fw fa-wrench"></i>
                <span>Settings</span>
            </a>
            <div id="SystemSettings" class="collapse" aria-labelledby="headingUtilities"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Company:</h6>
                    @if (count($crm->where('is_read', 1)->where('module_link_id', 35)) > 0 || Auth::user()->role_id == 1)
                        <a href="{{ route('company') }}" class="collapse-item">@lang('Company')</a>
                    @endif
                    @if (count($crm->where('is_read', 1)->where('module_link_id', 36)) > 0 || Auth::user()->role_id == 1)
                        <a href="{{ url('department') }}" class="collapse-item">@lang('Department')</a>
                    @endif
                    @if (count($crm->where('is_read', 1)->where('module_link_id', 37)) > 0 || Auth::user()->role_id == 1)
                        <a href="{{ url('designation') }}" class="collapse-item">@lang('Designation')</a>
                    @endif
                    @if (count($crm->where('is_read', 1)->where('module_link_id', 38)) > 0 || Auth::user()->role_id == 1)
                        <a href="{{ route('role') }}" class="collapse-item">@lang('lang.role')</a>
                    @endif
                    @if (count($crm->where('is_read', 1)->where('module_link_id', 39)) > 0 || Auth::user()->role_id == 1)
                        <a href="{{ route('staff_directory') }}" class="collapse-item">@lang('User')</a>
                    @endif
                    @if (count($crm->where('is_read', 1)->where('module_link_id', 40)) > 0 || Auth::user()->role_id == 1)
                        <a href="{{ url('module') }}" class="collapse-item">@lang('Module')</a>
                    @endif
                    @if (count($crm->where('is_read', 1)->where('module_link_id', 41)) > 0 || Auth::user()->role_id == 1)
                        <a href="{{ route('base_setup') }}" class="collapse-item">@lang('lang.base_setup')</a>
                    @endif
                    @if (count($crm->where('is_read', 1)->where('module_link_id', 52)) > 0 || Auth::user()->role_id == 1)
                        <a href="{{ route('daily-quotes.index') }}" class="collapse-item">@lang('Daily Quote')</a>
                    @endif
                    
                    <h6 class="collapse-header">Operational Settings:</h6>
                    @if (count($crm->where('is_read', 1)->where('module_link_id', 42)) > 0 || Auth::user()->role_id == 1)
                        <a href="{{ url('currency-settings') }}" class="collapse-item">@lang('Manage Currency')</a>
                    @endif
                    @if (count($crm->where('is_read', 1)->where('module_link_id', 43)) > 0 || Auth::user()->role_id == 1)
                        <a href="{{ url('payment-terms') }}" class="collapse-item">@lang('Payment Terms')</a>
                    @endif
                    @if (count($crm->where('is_read', 1)->where('module_link_id', 56)) > 0 || Auth::user()->role_id == 1)
                        <a href="{{ url('payment-cheque-print-template') }}" class="collapse-item">@lang('Cheque Print Template')</a>
                    @endif
                    @if (count($crm->where('is_read', 1)->where('module_link_id', 44)) > 0 || Auth::user()->role_id == 1)
                        <a href="{{ url('shipping-add') }}" class="collapse-item">@lang('Shipping')</a>
                    @endif
                    @if (count($crm->where('is_read', 1)->where('module_link_id', 45)) > 0 || Auth::user()->role_id == 1)
                        <a href="{{ url('vat-settings') }}" class="collapse-item">@lang('VAT Settings')</a>
                    @endif
                    @if (count($crm->where('is_read', 1)->where('module_link_id', 46)) > 0 || Auth::user()->role_id == 1)
                        <a href="{{ url('accountgroup-add') }}" class="collapse-item">@lang('Main Heads')</a>
                    @endif
                    
                    <h6 class="collapse-header">System Settings:</h6>
                    @if (count($crm->where('is_read', 1)->where('module_link_id', 47)) > 0 || Auth::user()->role_id == 1)
                        <a href="{{ url('general-settings') }}" class="collapse-item">@lang('lang.general_settings')</a>
                    @endif
                    @if (count($crm->where('is_read', 1)->where('module_link_id', 48)) > 0 || Auth::user()->role_id == 1)
                        <a href="{{ url('background-setting') }}" class="collapse-item">@lang('lang.background_settings')</a>
                    @endif
                    
                    <h6 class="collapse-header">Backup:</h6>
                    @if (count($crm->where('is_read', 1)->where('module_link_id', 49)) > 0 || Auth::user()->role_id == 1)
                        <a href="{{ url('backup-settings') }}" class="collapse-item">@lang('lang.backup_settings')</a>
                    @endif
                </div>
            </div>
        </li>
    @endif
    {{-- System Settings  --}}

    <!-- Nav Item - Pages Collapse Menu -->


</ul>

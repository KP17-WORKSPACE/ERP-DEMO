@php
    $modules = [];
    $module_links = [];
    $permissions = App\SmRolePermission::select('id','role_id','module_link_id','is_create','is_read','is_edit','is_delete','is_print','is_copy','is_recreate','is_saveprint','is_revice','is_export','is_editprinted','is_attach')->where('role_id', Auth::user()->role_id)->get();

    foreach($permissions as $permission){ @$module_links[] = @$permission->module_link_id; @$modules[] = @$permission->moduleLink->module_id;}
    $modules = array_unique(@$modules);

    $generalSetting=App\SmGeneralSettings::where('id',1)->first();
    if(isset($generalSetting->logo)){  @$logo = @$generalSetting->logo;  }
    else{ @$logo = 'public/uploads/settings/logo.png'; }
@endphp
<input type="hidden" name="url" id="url" value="{{url('/')}}">

<link rel="stylesheet" href="{{ asset('public/adminlite/plugins/fontawesome-free/css/all.min.css') }}">
<link rel="stylesheet" href="{{ asset('public/adminlite/dist/css/adminlte.min.css') }}">
<aside  class="main-sidebar sidebar-dark-primary"><br />
    <a id="menu001" class="nav-link" style="visibility:hidden;" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>    
	{{-- <script>
		window.setTimeout(myTimer, 1000);
		function myTimer() { document.getElementById("menu001").click(); }
	</script> --}}

      <!-- Sidebar -->
      <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <!-- Sidebar Menu -->
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->
                 
            <li class="nav-item">
                @if (Auth::user()->role_id == 3)
                <a href="{{url('/crm-dashboard')}}" id="admin-dashboard" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>@lang('lang.dashboard')</p></a>
                @else
                <a href="{{url('/crm-dashboard')}}" id="admin-dashboard" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>@lang('lang.dashboard')</p></a>
                @endif
            </li>
            
            {{-- Company  --}}
            <?php $crm = $permissions->wherein('module_link_id',[8,9,10,11]);?>
            @if(count($crm->where('is_read',1)) > 0 ||  Auth::user()->role_id == 1)
            <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fas fa-building"></i><p>Company<i class="fas fa-angle-left right"></i></p></a>
                <ul class="nav nav-treeview">
                    @if(count($crm->where('is_read',1)->where('module_link_id',8)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item"><a href="{{route('company')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>@lang('Company')</p></a></li>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',9)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item"><a href="{{route('staff_directory')}}"  class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>@lang('User')</p></a></li>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',10)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item"><a href="{{url('designation')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>@lang('Designation')</p></a></li>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',11)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item"><a href="{{url('department')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>@lang('Department')</p></a></li>
                    @endif
                </ul>
            </li>
            @endif
            
            {{-- System Settings  --}}
            <?php $crm = $permissions->wherein('module_link_id',[12,13,14,15,16,17,18,19,20,21,22,23,24]);?>
            @if(count($crm->where('is_read',1)) > 0 ||  Auth::user()->role_id == 1)
            <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fas fa-cogs"></i><p>System Settings<i class="fas fa-angle-left right"></i></p></a>
                <ul class="nav nav-treeview">
                    @if(count($crm->where('is_read',1)->where('module_link_id',12)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item"><a href="{{url('general-settings')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>@lang('lang.general_settings')</p></a></li>                    
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',13)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item"><a href="{{url('currency-settings')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>@lang('lang.manage_currency')</p></a></li>                    
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',14)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item"><a href="{{url('background-setting')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>@lang('lang.background_settings')</p></a></li>                    
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',15)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item"><a href="{{url('module')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>@lang('Module')</p></a></li>                    
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',16)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item"><a href="{{route('role')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>@lang('lang.role')</p></a></li>                    
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',17)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item"><a href="{{route('base_setup')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>@lang('lang.base_setup')</p></a></li>                    
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',18)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item"><a href="{{url('payment-terms')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>@lang('Payment Terms')</p></a></li>                    
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',19)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item"><a href="{{url('shipping-add')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>@lang('Shipping')</p></a></li>                    
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',20)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item"><a href="{{url('vat-settings')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>@lang('VAT Settings')</p></a></li>                    
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',21)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item"><a href="{{url('accountgroup-add')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>@lang('Main Heads')</p></a></li>                    
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',22)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item"><a href="{{url('accountgroupsub-add')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>@lang('Account Group')</p></a></li>                    
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',23)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item"><a href="{{url('accountgroupsub2-add')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>@lang('Account Sub Group')</p></a></li>                    
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',24)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item"><a href="{{url('backup-settings')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>@lang('lang.backup_settings')</p></a></li>                    
                    @endif
                </ul>
            </li>
            @endif

            {{-- Accounts  --}}
            <?php $account = $permissions->wherein('module_link_id',[25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44]);?>
            @if(count($account->where('is_read',1)) > 0 ||  Auth::user()->role_id == 1)
            <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fas fa-calculator"></i><p>Accounts<i class="fas fa-angle-left right"></i></p></a>
                <ul class="nav nav-treeview">
                    <li class="nav-item"><a href="{{url('chartofaccounts')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>@lang('Chart of Accounts')</p></a></li>
                    <li class="nav-item"><a href="" class="nav-link"><i class="far fa-circle nav-icon"></i><p>@lang('Receipts & Payments')</p><i class="fas fa-angle-left right"></i></a>
                          <ul class="nav nav-treeview">
                            <li class="nav-item"><a href="{{url('cashreceipt-add')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Cash Receipt</p></a></li>
                            <li class="nav-item"><a href="{{url('bankreceipt-add')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Bank Receipt</p></a></li>
                            <li class="nav-item"><a href="{{url('cashpayment-add')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Cash Payment</p></a></li>
                            <li class="nav-item"><a href="{{url('bankpayment-add')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Bank Payment</p></a></li>
                            <li class="nav-item"><a href="{{url('postdatedreceipt-add')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Postdated Receipt</p></a></li>
                            <li class="nav-item"><a href="{{url('postdatedpayment-add')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Postdated Payment</p></a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a href="{{url('journalvoucher-add')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>@lang('Journal Voucher')</p></a></li>
                    <li class="nav-item"><a href="" class="nav-link"><i class="far fa-circle nav-icon"></i><p>@lang('Ledger')</p><i class="fas fa-angle-left right"></i></a>
                          <ul class="nav nav-treeview">
                            <li class="nav-item"><a href="{{url('generalledger')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>General Ledger</p></a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a href="{{url('cashbook')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>@lang('Cash Book')</p></a></li>
                    <li class="nav-item"><a href="{{url('bankbook')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>@lang('Bank Book')</p></a></li>
                    <li class="nav-item"><a href="" class="nav-link"><i class="far fa-circle nav-icon"></i><p>@lang('Final Accounts')</p><i class="fas fa-angle-left right"></i></a>
                          <ul class="nav nav-treeview">
                            <li class="nav-item"><a href="{{url('trial-balance')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Trial Balance</p></a></li>
                            <li class="nav-item"><a href="{{url('trading-account')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Trading Account</p></a></li>
                            <li class="nav-item"><a href="{{url('profit-and-loss-account')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Profit & Loss Account</p></a></li>
                            <li class="nav-item"><a href="{{url('balancesheet')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Balancesheet</p></a></li>
                        </ul>
                    </li>                      
                </ul>
            </li>
            @endif
            
            {{-- customers  --}}
            <?php $crm = $permissions->wherein('module_link_id',[45]);?>
            @if(count($crm->where('is_read',1)) > 0 ||  Auth::user()->role_id == 1)
            <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fas fa-user"></i><p>Customer<i class="fas fa-angle-left right"></i></p></a>
                <ul class="nav nav-treeview">
                    <li class="nav-item"><a href="{{url('customers')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Customer Register</p></a></li>                    
                    <li class="nav-item"><a href="{{url('customerledger')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Customer Ledger</p></a></li>
                    <li class="nav-item"><a href="{{url('customer-outstanding')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Customer Outstanding</p></a></li>
                    <li class="nav-item"><a href="{{url('customer-outstanding-pdc')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Customer Outstanding - PDC</p></a></li>
                    <li class="nav-item"><a href="{{url('customer-ageing')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Customer Ageing</p></a></li>
                </ul>
            </li>
            @endif

            {{-- Supplier  --}}
            <?php $crm = $permissions->wherein('module_link_id',[46]);?>
            @if(count($crm->where('is_read',1)) > 0 ||  Auth::user()->role_id == 1)
            <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fas fa-users"></i><p>Supplier<i class="fas fa-angle-left right"></i></p></a>
                <ul class="nav nav-treeview">
                    <li class="nav-item"><a href="{{url('suppliers')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Supplier Register</p></a></li>                    
                    <li class="nav-item"><a href="{{url('supplierledger')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Supplier Ledger</p></a></li>
                    <li class="nav-item"><a href="{{url('supplier-outstanding')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Supplier Outstanding</p></a></li>
                    <li class="nav-item"><a href="{{url('supplier-outstanding-pdc')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Supplier Outstanding - PDC</p></a></li>
                    <li class="nav-item"><a href="{{url('supplier-ageing')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Supplier Ageing</p></a></li>
                </ul>
            </li>
            @endif

            
            {{-- purchase --}}
            <?php $crm = $permissions->wherein('module_link_id',[47,48,49]);?>
            @if(count($crm->where('is_read',1)) > 0 ||  Auth::user()->role_id == 1)
            <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fas fa-cart-plus"></i><p>Purchase<i class="fas fa-angle-left right"></i></p></a>
                <ul class="nav nav-treeview">
                    @if(count($crm->where('is_read',1)->where('module_link_id',47)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item"><a href="{{url('purchase-order/create')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Purchase Order</p></a></li>
                    <li class="nav-item"><a href="{{url('goods-receipt-note/create')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Goods Receipt Note</p></a></li>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',48)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item"><a href="{{url('purchase-invoice/create')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Purchase Invoice</p></a></li>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',49)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item"><a href="{{url('purchase-return-add')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Purchase Return</p></a></li>
                    @endif
                </ul>
            </li>
            @endif
            
            {{-- sales --}}
            <?php $crm = $permissions->wherein('module_link_id',[50,51,52,53,54,55,56]);?>
            @if(count($crm->where('is_read',1)) > 0 ||  Auth::user()->role_id == 1)
            <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fas fa-shopping-cart"></i><p>Sales<i class="fas fa-angle-left right"></i></p></a>
                <ul class="nav nav-treeview">
                    @if(count($crm->where('is_read',1)->where('module_link_id',51)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item"><a href="{{url('quotations')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Quotation</p></a></li>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',52)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item"><a href="{{url('proforma-invoice/create')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Proforma Invoice</p></a></li>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',53)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item"><a href="{{url('sales-invoice/create')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Sales Invoice</p></a></li>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',54)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item"><a href="{{url('delivery-note-add')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Delivery Note</p></a></li>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',55)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item"><a href="{{url('delivery-advice-add')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Delivery Advice</p></a></li>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',56)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item"><a href="{{url('sales-return-add')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Sales Return</p></a></li>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',50)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item"><a href="{{url('clearance')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Customs Clearance</p></a></li>
                    @endif
                </ul>
            </li>
            @endif

            {{-- product --}}
            <?php $crm = $permissions->wherein('module_link_id',[57,58,59,60]);?>
            @if(count($crm->where('is_read',1)) > 0 ||  Auth::user()->role_id == 1)
            <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fas fa-briefcase"></i><p>Product<i class="fas fa-angle-left right"></i></p></a>
                <ul class="nav nav-treeview">
                    @if(count($crm->where('is_read',1)->where('module_link_id',57)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item"><a href="{{url('brand')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Brand</p></a></li>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',58)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item"><a href="{{url('item-category')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Category</p></a></li>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',59)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item"><a href="{{url('create-sub-category')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Sub Category</p></a></li>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',60)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item"><a href="{{url('item-add')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Product List</p></a></li>
                    @endif
                </ul>
            </li>
            @endif
            
            {{-- Inventory --}}
            <?php $crm = $permissions->wherein('module_link_id',[61,62,63,64,65,66]);?>
            @if(count($crm->where('is_read',1)) > 0 ||  Auth::user()->role_id == 1)
            <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fas fa-copy"></i><p>Inventory<i class="fas fa-angle-left right"></i></p></a>
                <ul class="nav nav-treeview">
                    @if(count($crm->where('is_read',1)->where('module_link_id',61)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item"><a href="{{url('item-store/show')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Opening Stock</p></a></li>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',62)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item"><a href="{{url('stock-register')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Stock Register</p></a></li>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',63)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item"><a href="{{url('item-stock')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Item Stock</p></a></li>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',64)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item"><a href="{{url('stock-ledger')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Stock Ledger</p></a></li>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',65)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item"><a href="{{url('stock-in')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Stock In</p></a></li>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',66)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item"><a href="{{url('stock-out')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Stock Out</p></a></li>
                    @endif
                </ul>
            </li>
            @endif
            
            <?php $crm = $permissions->wherein('module_link_id',[1,2,3,4,5,6,73]);?>
            @if(count($crm->where('is_read',1)) > 0 ||  Auth::user()->role_id == 1)
            <li class="nav-item @if(Auth::user()->role_id != 1) menu-open @endif"><a href="#" class="nav-link"><i class="nav-icon fas fa-building"></i><p>CRM<i class="fas fa-angle-left right"></i></p></a>
                <ul class="nav nav-treeview">
                    @if(count($crm->where('is_read',1)->where('module_link_id',1)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item"><a href="{{url('crm-dashboard')}}"  class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Dashboard</p></a></li>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',2)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item"><a href="{{url('customers')}}"  class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Customers</p></a></li>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',3)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item"><a href="{{url('price-book/show')}}"  class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Price Book</p></a></li>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',4)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item"><a href="{{url('crm-leads/show')}}"  class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Leads</p></a></li>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',5)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item"><a href="{{url('crm-deals/show')}}"  class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Deals</p></a></li>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',73)) > 0 || Auth::user()->role_id == 1)
                    <li class="nav-item"><a href="{{url('crm-deal-track-approval-list')}}"  class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Deals Track</p></a></li>
                    @endif
                    @if(count($crm->where('is_read',1)->where('module_link_id',7)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item"><a href="{{url('crm-deals-sales-report')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Report</p></a></li>
                    <li class="nav-item"><a href="{{url('crm-deals-forecast-report')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Forecast Report</p></a></li>
                    @endif
                </ul>
            </li>
            @endif

            
            <?php $crm = $permissions->wherein('module_link_id',[7]);?>
            @if(count($crm->where('is_read',1)) > 0 ||  Auth::user()->role_id == 1)
            <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fas fa-book"></i><p>Reports<i class="fas fa-angle-left right"></i></p></a>
                <ul class="nav nav-treeview">
                    @if(count($crm->where('is_read',1)->where('module_link_id',7)) > 0 ||  Auth::user()->role_id == 1)
                    <li class="nav-item"><a href="{{url('crm-deals-sales-report')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Sales Report</p></a></li>
                    <li class="nav-item"><a href="{{url('crm-deals-forecast-report')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Forecast Report</p></a></li>
                    @endif
                    
                    @if (1==2)

                    <li class="nav-item"><a href="{{url('#')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Sales Report</p></a></li>
                    <li class="nav-item"><a href="{{url('#')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Purchase Report</p></a></li>
                    <li class="nav-item"><a href="{{url('#')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>List of Outstanding Payment</p></a></li>
                    <li class="nav-item"><a href="{{url('#')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Receipt Register</p></a></li>
                    <li class="nav-item"><a href="{{url('#')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Payment Register</p></a></li>
                    <li class="nav-item"><a href="{{url('#')}}" class="nav-link"><i class="far fa fa-angle-right nav-icon"></i><p>Stock Register</p></a></li>

                    {{-- @if(in_array(42, @$module_links) || Auth::user()->role_id == 1)<li><a href="{{route('profit')}}">@lang('lang.profit') @lang('lang.report')</a></li>@endif

                    @if(in_array(180, @$module_links) ||  Auth::user()->role_id == 1)<li><a href="{{route('user_log')}}">@lang('lang.user_log')</a></li>@endif
                    @if(in_array(273, @$module_links) ||  Auth::user()->role_id == 1)<li><a href="{{url('cost-center-reports')}}">@lang('lang.cost_center') @lang('lang.report')</a></li>@endif

                    @if(in_array(54, @$module_links) || Auth::user()->role_id == 1)<li><a href="{{route('search_account')}}">@lang('lang.details_report')</a></li>@endif

                    @if(in_array(274, @$module_links) || Auth::user()->role_id == 1)<li><a href="{{url('income-statement')}}">@lang('lang.income') @lang('lang.statement')</a></li>@endif 

                    @if(in_array(236, @$module_links) || Auth::user()->role_id == 1)<li><a href="{{url('ledger-report')}}">@lang('lang.ledger') @lang('lang.report')</a></li>@endif

                    @if(Auth::user()->role_id == 1)<li><a href="{{url('bank-ledger')}}">@lang('lang.bank') @lang('lang.ledger')</a></li>@endif
                    @if(in_array(295, @$module_links) || Auth::user()->role_id == 1)<li><a href="{{url('bank-book')}}">@lang('lang.bank') @lang('lang.book')</a></li>@endif
                    @if(in_array(296, @$module_links) || Auth::user()->role_id == 1)<li><a href="{{url('purchase-report')}}">@lang('lang.purchase') @lang('lang.report')</a></li>@endif
                    @if(in_array(275, @$module_links) || Auth::user()->role_id == 1)<li><a href="{{url('sales-report')}}">@lang('lang.sales') @lang('lang.report')</a></li>@endif --}}

                    @endif

                </ul>
            </li>
            @endif

            
            {{-- Ticket System  --}}
            @if(Auth::user()->role_id == 7)
            <li class="nav-item"><a href="{{route('user.ticket')}}" id="admin-dashboard" class="nav-link"><i class="nav-icon fas fa-question-circle"></i><p>@lang('lang.ticket_list')</p></a></li>
            @endif

          </ul>
        </nav>
        <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->
    </aside>


<script src="{{ asset('public/adminlite/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('public/adminlite/dist/js/adminlte.js') }}"></script>
<style>
    .nav-treeview{ background:#eff3e7 !important;}
    .sidebar-dark-primary{ background: #eff3e7 !important; position: fixed !important; overflow: hidden !important; overflow-y: scroll !important; height: 100% !important; border-right: solid 1px #dbdfd3;}
    .main-wrapper ::-webkit-scrollbar {width: 5px !important; background: #33342d}
    .main-wrapper ::-webkit-scrollbar-thumb {background: #eff3e7;border-radius: 10px;}
    .sidebar-dark-primary .nav-sidebar>.nav-item>.nav-link.active, .sidebar-light-primary .nav-sidebar>.nav-item>.nav-link.active{background-color: #33342d;}

.sidebar *::-webkit-scrollbar {
    width: 18px;
    height: 18px;
}
.sidebar *::-webkit-scrollbar-corner {
    background: transparent;
}
.sidebar *::-webkit-scrollbar-thumb {
    border: 6px solid transparent;
    background: rgba(0, 0, 0, 0.2);
    background: var(--palette-black-alpha-20, rgba(0, 0, 0, 0.2));
    border-radius: 10px;
    background-clip: padding-box;
}

</style>

        {{-- Human Resource  --}}
        {{-- @if(in_array(5, @$modules) ||  Auth::user()->role_id == 1)
            <li>
                <a href="#subMenuHumanResource" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                    <span class="flaticon-consultation"></span> @lang('Staff') </a>
                <ul class="collapse list-unstyled" id="subMenuHumanResource">
                    @if(in_array(85, $module_links) ||  Auth::user()->role_id == 1)
                        <li><a href="{{route('staff_directory')}}">@lang('Staff Directory')</a>
                        </li>               
                    @endif
                    @if(in_array(89, @$module_links) ||  Auth::user()->role_id == 1)
                        <li><a href="{{route('staff_attendance')}}">@lang('lang.staff_attendance')</a></li>@endif
                    @if(in_array(93, @$module_links) ||  Auth::user()->role_id == 1)
                        <li><a href="{{route('staff_attendance_report')}}">@lang('lang.staff_attendance_report')</a></li>@endif
                    @if(in_array(94, @$module_links) ||  Auth::user()->role_id == 1)
                        <li><a href="{{url('payroll')}}">@lang('lang.payroll')</a></li>@endif
                    @if(in_array(102, @$module_links) ||  Auth::user()->role_id == 1)
                        <li><a href="{{url('payroll-report')}}">@lang('lang.payroll_report')</a></li>@endif
                    @if(in_array(104, @$module_links) ||  Auth::user()->role_id == 1)
                        <li><a href="{{url('designation')}}">@lang('lang.designation')</a></li>@endif
                    @if(in_array(108, @$module_links) ||  Auth::user()->role_id == 1)
                        <li><a href="{{url('department')}}">@lang('lang.department')</a></li>@endif
                    @if(in_array(291, @$module_links) ||  Auth::user()->role_id == 1)
                        <li><a href="{{url('cash-issue')}}">@lang('lang.cash_issue')</a></li>@endif
                </ul>
            </li>
        @endif --}}
        {{-- @if(in_array(3, @$modules) ||  Auth::user()->role_id == 1)
            <li>
                <a href="#subMenuReceiptsPayments" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                    <span class="flaticon-accounting"></span> @lang('Receipts & Payments') </a>
                    <ul class="collapse list-unstyled" id="subMenuReceiptsPayments">
                        <li><a href="#">Cash Receipt</a></li>
                        <li><a href="#">Bank Receipt</a></li>            
                        <li><a href="#">Cash Payment</a></li>
                        <li><a href="#">Bank Payment</a></li>
                        <li><a href="#">Post Dated Receipt</a></li>
                        <li><a href="#">Post Dated payment</a></li>         
                    </ul>
            </li>
        @endif --}}
        {{-- vendors --}}
        {{-- @if(in_array(8, @$modules) ||  Auth::user()->role_id == 1)
            <li>
                <a href="#subMenuSupplier" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                    <span class="flaticon-inventory"></span> @lang('lang.supplier')
                </a>
                <ul class="collapse list-unstyled" id="subMenuSupplier">
                    @if(in_array(143, @$module_links) ||  Auth::user()->role_id == 1)
                        <li><a href="{{url('enlisted-suppliers')}}">@lang('lang.add') @lang('lang.supplier')</a></li>
                    @endif
                    @if(in_array(269, @$module_links) ||  Auth::user()->role_id == 1)
                        <li><a href="{{url('enlisted-suppliers')}}">@lang('lang.manage') @lang('lang.supplier')</a></li>
                    @endif
                </ul>
            </li>
        @endif --}}
        {{-- @if(in_array(15, @$modules) ||  Auth::user()->role_id == 1)
            <li>
                <a href="#subMenuQuotation" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                    <span class="flaticon-accounting"></span> @lang('lang.quotations') </a>
                <ul class="collapse list-unstyled" id="subMenuQuotation">
                    @if(in_array(356, $module_links) ||  Auth::user()->role_id == 1)
                    <li><a href="{{url('quotations/create')}}">@lang('lang.add') @lang('lang.quotations')</a></li>
                    @endif
                    @if(in_array(355, @$module_links) ||  Auth::user()->role_id == 1)
                    <li><a href="{{url('quotations')}}">@lang('lang.quotations') @lang('lang.list')</a></li>
                    @endif
                </ul>
            </li>
        @endif --}}

        {{-- HIDE --}}
        <div style="display:none;">
            @if(Module::has('Project'))
                    <li>
                    <a href="#p" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle"> 
                        <span class="flaticon-analytics"></span> @lang('lang.projects')
                    </a>
                    <ul class="collapse list-unstyled" id="p">  
                         <li> <a href="{{ route('InfixMyTaskList') }}">@lang('lang.my') @lang('lang.task')</a> </li> 
                    @if(Auth::user()->role_id == 1)
                         <li> <a href="{{ route('InfixProjectList') }}">@lang('lang.projects') </a> </li>   
                         <li> <a href="{{ route('InfixProjectCategoryList') }}">@lang('lang.project') @lang('lang.category')</a> </li>   
                         <li> <a href="{{ route('InfixTeamList') }}">@lang('lang.teams')</a> </li> 
                     @endif
                    @if(Auth::user()->role_id == 3)
                         <li> <a href="{{ route('InfixMyProjectList') }}">@lang('lang.projects') </a> </li> 
                     @endif                    
                    </ul>
                </li>
                @endif
            </div>
            {{-- HIDE --}}
    
    
            {{-- HIDE --}}
            <div style="display:none;">
            @if(in_array(6, @$modules) ||  Auth::user()->role_id == 1)
                <li>
                    <a href="#subMenuLeaveManagement" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <span class="flaticon-slumber"></span> @lang('lang.leave') </a>
                    <ul class="collapse list-unstyled" id="subMenuLeaveManagement">
                        @if(in_array(117, @$module_links) ||  Auth::user()->role_id == 1)
                            <li><a href="{{url('apply-leave')}}">@lang('lang.apply_leave')</a></li>                @endif
                        @if(in_array(113, @$module_links) ||  Auth::user()->role_id == 1)
                            <li><a href="{{url('approve-leave')}}">@lang('lang.approve_leave_request')</a>
                            </li>                @endif
                        @if(in_array(121, @$module_links) ||  Auth::user()->role_id == 1)
                            <li><a href="{{url('leave-define')}}">@lang('lang.leave_define')</a></li>                @endif
                        @if(in_array(125, @$module_links) ||  Auth::user()->role_id == 1)
                            <li><a href="{{url('leave-type')}}">@lang('lang.leave_type')</a></li> @endif
                    </ul>
                </li>
            @endif
            </div>
            {{-- HIDE --}}
    
    
    
            {{-- HIDE --}}
            <div style="display:none;">
            @if(in_array(7, @$modules) ||  Auth::user()->role_id == 1)
                <li>
                    <a href="#subMenuCommunicate" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <span class="flaticon-email"></span> @lang('lang.communicate') </a>
                    <ul class="collapse list-unstyled" id="subMenuCommunicate">
                        @if(in_array(130, @$module_links) ||  Auth::user()->role_id == 1)
                            <li><a href="{{url('notice-list')}}">@lang('lang.notice_board')</a></li>                @endif
                        @if(in_array(135, $module_links) ||  Auth::user()->role_id == 1)
                            <li><a href="{{url('send-email-sms-view')}}">@lang('lang.send_email')</a>
                            </li>                @endif
                        @if(in_array(137, @$module_links) ||  Auth::user()->role_id == 1)
                            <li><a href="{{url('email-sms-log')}}">@lang('lang.email_sms_log')</a>
                            </li>                @endif
                        @if(in_array(138, @$module_links) ||  Auth::user()->role_id == 1)
                            <li><a href="{{url('event')}}">@lang('lang.event')</a></li>@endif
                    </ul>
                </li>
            @endif
            </div>
            {{-- HIDE --}}
    
    
            {{-- HIDE --}}
            <div style="display:none;">
            @if(in_array(14, @$modules) ||  Auth::user()->role_id ==1)
    
                        <li>
                            <a href="#Ticket" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                                <span class="flaticon-settings"></span>
                                @lang('lang.ticket_system')
                            </a>
                            <ul class="collapse list-unstyled" id="Ticket">
                                @if(in_array(277, @$modules) ||  Auth::user()->role_id ==1)
                                    <li><a href="{{ route('ticket.category') }}"> @lang('lang.ticket_category')</a></li>      
                                @endif
    
                                @if(in_array(281, @$modules) || Auth::user()->role_id ==1)
                                    <li><a href="{{ route('ticket.priority') }}">@lang('lang.ticket_priority')</a></li> 
                                @endif
    
                                @if(in_array(285, $modules) ||  Auth::user()->role_id ==1)
                                    <li><a href="{{ route('admin.ticket_list') }}">@lang('lang.ticket_list')</a>
                                    </li>                
                                @endif
                                @if (Auth::user()->role_id ==3)
                                    <li><a href="{{ route('user.ticket') }}">@lang('lang.ticket_list')</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif
            </div>
            {{-- HIDE --}}
@php
    $ageingMenuGroup = $ageingMenuGroup ?? 'part_number';
    $isPartWise = Request::is('ageing-report-stock') || Request::is('ageing-report-stock/*');
    $wiseRoutes = [
        'brand' => 'ageing-report-brand-wise',
        'category' => 'ageing-report-category-wise',
        'sub_category' => 'ageing-report-subcategory-wise',
        'company' => 'ageing-report-company-wise',
        'customer' => 'ageing-report-customer-wise',
        'sales_person' => 'ageing-report-salesperson-wise',
    ];
@endphp
<style>
    .report-type-dropdown .dropdown-item.active,
    .report-type-dropdown .dropdown-item.active:hover,
    .report-type-dropdown .dropdown-item.active:focus {
        color: #198754 !important;
        background-color: #eaf7ef;
        font-weight: 600;
    }
</style>
<div class="dropdown report-type-dropdown">
    <a class="text-dark report-type-trigger" href="javascript:void(0);" id="ageingReportTypeMenu"
        data-bs-toggle="dropdown" aria-expanded="false">
        Ageing Report <i class="icon-outline-alt-arrow-down ms-1"></i>
    </a>
    <ul class="dropdown-menu" aria-labelledby="ageingReportTypeMenu">
        <li>
            <a class="text-dark dropdown-item {{ $isPartWise && $ageingMenuGroup === 'part_number' ? 'active' : '' }}"
                href="{{ url('ageing-report-stock') }}">Part Number Wise</a>
        </li>
        <li>
            <a class="text-dark dropdown-item {{ $ageingMenuGroup === 'brand' ? 'active' : '' }}"
                href="{{ url($wiseRoutes['brand']) }}">Brand Wise</a>
        </li>
        <li>
            <a class="text-dark dropdown-item {{ $ageingMenuGroup === 'category' ? 'active' : '' }}"
                href="{{ url($wiseRoutes['category']) }}">Category Wise</a>
        </li>
        <li>
            <a class="text-dark dropdown-item {{ $ageingMenuGroup === 'sub_category' ? 'active' : '' }}"
                href="{{ url($wiseRoutes['sub_category']) }}">Sub Category Wise</a>
        </li>
        <li>
            <a class="text-dark dropdown-item {{ $ageingMenuGroup === 'company' ? 'active' : '' }}"
                href="{{ url($wiseRoutes['company']) }}">Company Wise</a>
        </li>
        <li>
            <a class="text-dark dropdown-item {{ $ageingMenuGroup === 'customer' ? 'active' : '' }}"
                href="{{ url($wiseRoutes['customer']) }}">Customer Wise</a>
        </li>
        <li>
            <a class="text-dark dropdown-item {{ $ageingMenuGroup === 'sales_person' ? 'active' : '' }}"
                href="{{ url($wiseRoutes['sales_person']) }}">Sales Person Wise</a>
        </li>
    </ul>
</div>

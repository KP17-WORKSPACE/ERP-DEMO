@php
    $isAgeingPartRoute = Request::is('ageing-report-stock') || Request::is('ageing-report-stock/*');
    $ageingWiseActive = [
        'brand' => Request::is('ageing-report-brand-wise'),
        'category' => Request::is('ageing-report-category-wise'),
        'sub_category' => Request::is('ageing-report-subcategory-wise'),
        'company' => Request::is('ageing-report-company-wise'),
        'customer' => Request::is('ageing-report-customer-wise'),
        'sales_person' => Request::is('ageing-report-salesperson-wise'),
    ];
    $isAnyAgeingRoute = $isAgeingPartRoute || in_array(true, $ageingWiseActive, true);
@endphp
<li class="dropend">
    <a class="dropdown-item dropdown-toggle report-submenu-trigger text-dark {{ $isAnyAgeingRoute ? 'active' : '' }}"
        href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false">Ageing Report</a>
    <ul class="dropdown-menu">
        <li><a class="text-dark dropdown-item {{ $isAgeingPartRoute ? 'active' : '' }}" href="{{ url('ageing-report-stock') }}">Part Number Wise</a></li>
        <li><a class="text-dark dropdown-item {{ !empty($ageingWiseActive['brand']) ? 'active' : '' }}" href="{{ url('ageing-report-brand-wise') }}">Brand Wise</a></li>
        <li><a class="text-dark dropdown-item {{ !empty($ageingWiseActive['category']) ? 'active' : '' }}" href="{{ url('ageing-report-category-wise') }}">Category Wise</a></li>
        <li><a class="text-dark dropdown-item {{ !empty($ageingWiseActive['sub_category']) ? 'active' : '' }}" href="{{ url('ageing-report-subcategory-wise') }}">Sub Category Wise</a></li>
        <li><a class="text-dark dropdown-item {{ !empty($ageingWiseActive['company']) ? 'active' : '' }}" href="{{ url('ageing-report-company-wise') }}">Company Wise</a></li>
        <li><a class="text-dark dropdown-item {{ !empty($ageingWiseActive['customer']) ? 'active' : '' }}" href="{{ url('ageing-report-customer-wise') }}">Customer Wise</a></li>
        <li><a class="text-dark dropdown-item {{ !empty($ageingWiseActive['sales_person']) ? 'active' : '' }}" href="{{ url('ageing-report-salesperson-wise') }}">Sales Person Wise</a></li>
    </ul>
</li>

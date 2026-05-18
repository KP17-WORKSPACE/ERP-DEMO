@php
    $menuReportGroup = $menuReportGroup ?? ($report_group ?? 'date_wise');

    $isSalesInvoiceRoute = request()->routeIs('sales.invoice.report.detail');
    $isSalesReturnRoute = request()->routeIs('sales.return.report.detail');

    $isAnyAgeingRoute =
        request()->is('ageing-report-stock') ||
        request()->is('ageing-report-stock/*') ||
        request()->is('ageing-report-brand-wise') ||
        request()->is('ageing-report-category-wise') ||
        request()->is('ageing-report-subcategory-wise') ||
        request()->is('ageing-report-company-wise') ||
        request()->is('ageing-report-customer-wise') ||
        request()->is('ageing-report-salesperson-wise');
@endphp

<div class="dropdown report-type-dropdown">
    <a class="text-dark report-type-trigger"
       href="javascript:void(0);"
       id="salesReportTypeMenu"
       data-bs-toggle="dropdown"
       aria-expanded="false">

        Sales Report Type
        <i class="icon-outline-alt-arrow-down ms-1"></i>
    </a>

    <ul class="dropdown-menu" aria-labelledby="salesReportTypeMenu">

        {{-- Sales Report --}}
        <li class="dropend">
            <a class="dropdown-item dropdown-toggle report-submenu-trigger text-dark {{ $isSalesInvoiceRoute ? 'active' : '' }}"
               href="javascript:void(0);"
               data-bs-toggle="dropdown"
               aria-expanded="false">

                Sales Report
            </a>

            <ul class="dropdown-menu">

                <li>
                    <a class="dropdown-item text-dark {{ $isSalesInvoiceRoute && $menuReportGroup == 'company_wise' ? 'active' : '' }}"
                       href="{{ route('sales.invoice.report.detail', ['report_group' => 'company_wise']) }}">
                        Company Wise
                    </a>
                </li>

                <li>
                    <a class="dropdown-item text-dark {{ $isSalesInvoiceRoute && $menuReportGroup == 'date_wise' ? 'active' : '' }}"
                       href="{{ route('sales.invoice.report.detail', ['report_group' => 'date_wise']) }}">
                        Date Wise
                    </a>
                </li>

                <li>
                    <a class="dropdown-item text-dark {{ $isSalesInvoiceRoute && $menuReportGroup == 'customer_wise' ? 'active' : '' }}"
                       href="{{ route('sales.invoice.report.detail', ['report_group' => 'customer_wise']) }}">
                        Customer Wise
                    </a>
                </li>

                <li>
                    <a class="dropdown-item text-dark {{ $isSalesInvoiceRoute && $menuReportGroup == 'sales_person_wise' ? 'active' : '' }}"
                       href="{{ route('sales.invoice.report.detail', ['report_group' => 'sales_person_wise']) }}">
                        Sales Person Wise
                    </a>
                </li>

            </ul>
        </li>

        {{-- Sales Return Report --}}
        <li class="dropend">

            <a class="dropdown-item dropdown-toggle report-submenu-trigger text-dark {{ $isSalesReturnRoute ? 'active' : '' }}"
               href="javascript:void(0);"
               data-bs-toggle="dropdown"
               aria-expanded="false">

                Sales Return Report
            </a>

            <ul class="dropdown-menu">

                <li>
                    <a class="dropdown-item text-dark {{ $isSalesReturnRoute && $menuReportGroup == 'company_wise' ? 'active' : '' }}"
                       href="{{ route('sales.return.report.detail', ['report_group' => 'company_wise']) }}">
                        Company Wise
                    </a>
                </li>

                <li>
                    <a class="dropdown-item text-dark {{ $isSalesReturnRoute && $menuReportGroup == 'date_wise' ? 'active' : '' }}"
                       href="{{ route('sales.return.report.detail', ['report_group' => 'date_wise']) }}">
                        Date Wise
                    </a>
                </li>

                <li>
                    <a class="dropdown-item text-dark {{ $isSalesReturnRoute && $menuReportGroup == 'customer_wise' ? 'active' : '' }}"
                       href="{{ route('sales.return.report.detail', ['report_group' => 'customer_wise']) }}">
                        Customer Wise
                    </a>
                </li>

                <li>
                    <a class="dropdown-item text-dark {{ $isSalesReturnRoute && $menuReportGroup == 'sales_person_wise' ? 'active' : '' }}"
                       href="{{ route('sales.return.report.detail', ['report_group' => 'sales_person_wise']) }}">
                        Sales Person Wise
                    </a>
                </li>

            </ul>
        </li>

        {{-- Ageing Report Menu --}}
        @includeIf('backEnd.partials.salesAgeingReportMenu')

    </ul>
</div>
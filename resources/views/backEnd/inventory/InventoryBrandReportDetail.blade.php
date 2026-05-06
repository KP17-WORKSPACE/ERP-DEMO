@extends('backEnd.newmasterpage')
@section('mainContent')

    <style>
        .report-type-dropdown .dropdown-item.active,
        .report-type-dropdown .dropdown-item.active:hover,
        .report-type-dropdown .dropdown-item.active:focus {
            color: #198754 !important;
            background-color: #eaf7ef;
            font-weight: 600;
        }
        .inv-brand-detail-sr-row td {
            color: #b02a37 !important;
        }
    </style>

    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    $reportListUrl = url('inventory-brand-report');
    if (!empty($reportIndexQuery ?? '')) {
        $reportListUrl .= '?' . $reportIndexQuery;
    }
    $lineCount = ($linesSi ?? collect())->count() + ($linesSr ?? collect())->count();
    ?>

    <?php try { ?>

    <aside class="left-nav col-12" id="leftSidebar">

        

        <div class="long-list" id="filters-long">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="dropdown report-type-dropdown">
                    @php
                        $isPartWiseRoute = Request::is('inventory-brand-report') || Request::is('inventory-brand-report/*');
                        $isBrandWiseRoute = Request::is('inventory-brand-wise-report');
                        $isCategoryWiseRoute = Request::is('inventory-category-wise-report');
                        $isSubCategoryWiseRoute = Request::is('inventory-subcategory-wise-report');
                        $isCompanyWiseRoute = Request::is('inventory-company-wise-report');
                        $isCustomerWiseRoute = Request::is('inventory-customer-wise-report');
                        $isSalesPersonWiseRoute = Request::is('inventory-salesperson-wise-report');
                    @endphp
                    <a class="text-dark report-type-trigger" href="javascript:void(0);" id="inventoryDetailReportTypeMenu"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Inventory Report Type <i class="icon-outline-alt-arrow-down ms-1"></i>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="inventoryDetailReportTypeMenu">
                        <li><a class="text-dark dropdown-item {{ $isPartWiseRoute ? 'active' : '' }}" href="{{ $reportListUrl }}">Part number wise report</a></li>
                        <li><a class="text-dark dropdown-item {{ $isBrandWiseRoute ? 'active' : '' }}" href="{{ url('inventory-brand-wise-report') }}">Brand wise report</a></li>
                        <li><a class="text-dark dropdown-item {{ $isCategoryWiseRoute ? 'active' : '' }}" href="{{ url('inventory-category-wise-report') }}">Category wise report</a></li>
                        <li><a class="text-dark dropdown-item {{ $isSubCategoryWiseRoute ? 'active' : '' }}" href="{{ url('inventory-subcategory-wise-report') }}">Sub category wise report</a></li>
                        <li><a class="text-dark dropdown-item {{ $isCompanyWiseRoute ? 'active' : '' }}" href="{{ url('inventory-company-wise-report') }}">Company wise report</a></li>
                        <li><a class="text-dark dropdown-item {{ $isCustomerWiseRoute ? 'active' : '' }}" href="{{ url('inventory-customer-wise-report') }}">Customer wise report</a></li>
                        <li><a class="text-dark dropdown-item {{ $isSalesPersonWiseRoute ? 'active' : '' }}" href="{{ url('inventory-salesperson-wise-report') }}">Sales person wise report</a></li>
                    </ul>
                    @include('backEnd.inventory.partials.inventoryReportPageHeading', [
            'reportBaseTitle' => '',
            'ctrlBrand' => '',
            'brands' => collect(),
            'ctrlCategory' => '',
            'categories' => collect(),
            'ctrlSubCategory' => '',
            'subCategories' => collect(),
            'ctrlSupplier' => $ctrl_supplier ?? '',
            'suppliers' => $supplier_list ?? collect(),
            'ctrlSalesPerson' => $ctrl_sales_person ?? '',
            'salesPersons' => $sales_person_list ?? collect(),
            'ctrlCompany' => $ctrl_company ?? '',
            'companies' => $company ?? collect(),
            'ctrlPartNumber' => $itemRow->part_number ?? '',
        ])
                </div>
                <div class="search-filter-container mb-0 d-flex flex-wrap align-items-center gap-2">
                    <input type="text" id="tableSearch" class="form-control d-inline-block"
                        style="font-size:13px;width: 350px;" placeholder="Search">

                    <a href="{{ $reportListUrl }}" class="btn btn-light">
                        <i class="ico icon-outline-arrow-left"></i> Back to report
                    </a>
                  
                </div>
            </div>

         
        </div>

        <div class="left-nav-list">
            <div class="table-responsive mb-4 mt-4">
                <table id="long-list" class="table table-hover data-table" style="table-layout: fixed;width:100%">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:35px">No.</th>
                            <th class="text-start" style="width:100px">Company</th>
                            <th class="text-start" style="width:140px">Part Number</th>
                            <th class="text-center" style="width:70px">Deal Code</th>
                            <th class="text-center" style="width:70px">Doc No</th>
                            <th class="text-start" style="width:100px">Doc Date</th>
                            <th class="text-start" style="width:150px">Account Name</th>
                            <th class="text-center" style="width:40px">Qty</th>
                            <th class="text-end" style="width:100px">Avg Rate</th>
                            <th class="text-end" style="width:100px">Value</th>
                            <th class="text-end" style="width:100px">Discount</th>
                            <th class="text-end" style="width:120px">Taxable Amount</th>
                            <th class="text-end" style="width:100px">Val Amount</th>
                            <th class="text-end" style="width:100px">Total Amt</th>
                            <th class="text-center" style="width:100px">Sales Person</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($lineCount === 0)
                            <tr>
                                <td colspan="15" class="text-center">No sales invoice or sales return lines for this item in the selected period.</td>
                            </tr>
                        @else
                            @php $rowNo = 0; @endphp
                            @foreach($linesSi as $li)
                                @php $rowNo++; @endphp
                                <tr>
                                    <td class="text-center">{{ $rowNo }}</td>
                                    <td>
                                        <div title="{{ @$li->invoice_company_name }}">{{ @$li->invoice_company_name }}</div>
                                    </td>
                                    <td>
                                        <div title="{{ $itemRow->part_number }}">{{ $itemRow->part_number }}</div>
                                    </td>
                                    <td class="text-center">
                                        @if($li->deal_id != 0)
                                            <a href="{{ url('get-url-deal-track/'.$li->deal_code) }}" target="_blank" rel="noopener noreferrer">{{ $li->deal_code }}</a>
                                        @else
                                            Without
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(substr($li->doc_number, 0, 2)=="SI")
                                            <a href="{{ url('get-url-sales-invoice/'.$li->doc_number) }}" target="_blank" rel="noopener noreferrer">{{ @$li->doc_number }}</a>
                                        @else
                                            {{ @$li->doc_number }}
                                        @endif
                                    </td>
                                    <td>{{ @App\SysHelper::normalizeToDmy(@$li->doc_date) }}</td>
                                    <td>
                                        <div style="width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ @$li->account_name }}</div>
                                    </td>
                                    <td class="text-center">{{ @$li->qty }}</td>
                                    <td class="text-end">{{ @App\SysHelper::com_curr_format(@$li->unitprice,2,'.',',') }}</td>
                                    <td class="text-end">{{ @App\SysHelper::com_curr_format(@$li->value,2,'.',',') }}</td>
                                    <td class="text-end">{{ @App\SysHelper::com_curr_format(@$li->discount,2,'.',',') }}</td>
                                    <td class="text-end">{{ @App\SysHelper::com_curr_format(@$li->taxableamount,2,'.',',') }}</td>
                                    <td class="text-end">{{ @App\SysHelper::com_curr_format(@$li->vatamount,2,'.',',') }}</td>
                                    <td class="text-end">{{ @App\SysHelper::com_curr_format($li->taxableamount + $li->vatamount,2,'.',',') }}</td>
                                    <td class="text-start">{{ $li->full_name }}</td>
                                </tr>
                            @endforeach
                            @foreach($linesSr as $li)
                                @php $rowNo++; @endphp
                                <tr class="inv-brand-detail-sr-row">
                                    <td class="text-center">{{ $rowNo }}</td>
                                    <td>
                                        <div title="{{ @$li->invoice_company_name }}">{{ @$li->invoice_company_name }}</div>
                                    </td>
                                    <td>
                                        <div title="{{ $itemRow->part_number }}">{{ $itemRow->part_number }}</div>
                                    </td>
                                    <td class="text-center">
                                        @if($li->deal_id != 0)
                                            <a href="{{ url('get-url-deal-track/'.$li->deal_code) }}" target="_blank" rel="noopener noreferrer">{{ $li->deal_code }}</a>
                                        @else
                                            Without
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ url('get-url-sales-return/'.$li->doc_number) }}" target="_blank" rel="noopener noreferrer">{{ @$li->doc_number }}</a>
                                    </td>
                                    <td>{{ @App\SysHelper::normalizeToDmy(@$li->doc_date) }}</td>
                                    <td>
                                        <div style="width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ @$li->account_name }}</div>
                                    </td>
                                    <td class="text-center">{{ @$li->qty }}</td>
                                    <td class="text-end">{{ @App\SysHelper::com_curr_format(@$li->unitprice,2,'.',',') }}</td>
                                    <td class="text-end">{{ @App\SysHelper::com_curr_format(@$li->value,2,'.',',') }}</td>
                                    <td class="text-end">{{ @App\SysHelper::com_curr_format(@$li->discount,2,'.',',') }}</td>
                                    <td class="text-end">{{ @App\SysHelper::com_curr_format(@$li->taxableamount,2,'.',',') }}</td>
                                    <td class="text-end">{{ @App\SysHelper::com_curr_format(@$li->vatamount,2,'.',',') }}</td>
                                    <td class="text-end">{{ @App\SysHelper::com_curr_format($li->taxableamount + $li->vatamount,2,'.',',') }}</td>
                                    <td class="text-start">{{ $li->full_name }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                    @if($lineCount > 0)
                        <tfoot>
                            <tr>
                                <th colspan="7" class="text-end">Sales</th>
                                <th class="text-center">{{ $footer_si['qty'] }}</th>
                                <th class="text-end">{{ @App\SysHelper::com_curr_format($footer_si['avg_rate_total'],2,'.',',') }}</th>
                                <th class="text-end">{{ @App\SysHelper::com_curr_format($footer_si['value'],2,'.',',') }}</th>
                                <th class="text-end">{{ @App\SysHelper::com_curr_format($footer_si['discount'],2,'.',',') }}</th>
                                <th class="text-end">{{ @App\SysHelper::com_curr_format($footer_si['taxableamount'],2,'.',',') }}</th>
                                <th class="text-end">{{ @App\SysHelper::com_curr_format($footer_si['vatamount'],2,'.',',') }}</th>
                                <th class="text-end">{{ @App\SysHelper::com_curr_format($footer_si['total_amount'],2,'.',',') }}</th>
                                <th class="text-end"></th>
                            </tr>
                            <tr>
                                <th colspan="7" class="text-end">Sales Return</th>
                                <th class="text-center">{{ $footer_sr['qty'] }}</th>
                                <th class="text-end">{{ @App\SysHelper::com_curr_format($footer_sr['avg_rate_total'],2,'.',',') }}</th>
                                <th class="text-end">{{ @App\SysHelper::com_curr_format($footer_sr['value'],2,'.',',') }}</th>
                                <th class="text-end">{{ @App\SysHelper::com_curr_format($footer_sr['discount'],2,'.',',') }}</th>
                                <th class="text-end">{{ @App\SysHelper::com_curr_format($footer_sr['taxableamount'],2,'.',',') }}</th>
                                <th class="text-end">{{ @App\SysHelper::com_curr_format($footer_sr['vatamount'],2,'.',',') }}</th>
                                <th class="text-end">{{ @App\SysHelper::com_curr_format($footer_sr['total_amount'],2,'.',',') }}</th>
                                <th class="text-end"></th>
                            </tr>
                            <tr id="ibrDetailFooterNet">
                                <th colspan="7" class="text-end">Net Sales</th>
                                <th class="text-center">{{ $footer_net['qty'] }}</th>
                                <th class="text-end">{{ @App\SysHelper::com_curr_format($footer_net['avg_rate_total'],2,'.',',') }}</th>
                                <th class="text-end">{{ @App\SysHelper::com_curr_format($footer_net['value'],2,'.',',') }}</th>
                                <th class="text-end">{{ @App\SysHelper::com_curr_format($footer_net['discount'],2,'.',',') }}</th>
                                <th class="text-end">{{ @App\SysHelper::com_curr_format($footer_net['taxableamount'],2,'.',',') }}</th>
                                <th class="text-end">{{ @App\SysHelper::com_curr_format($footer_net['vatamount'],2,'.',',') }}</th>
                                <th class="text-end">{{ @App\SysHelper::com_curr_format($footer_net['total_amount'],2,'.',',') }}</th>
                                <th class="text-end"></th>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </aside>

    <?php } catch (\Exception $e) { ?> {{ $e }} <?php } ?>
@endsection

@extends('backEnd.newmasterpage')
@section('mainContent')

    <style>
        .report-type-trigger {
            color: #212529;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
        }
        .report-type-trigger:hover {
            color: #499258;
        }
        .dropdown-menu .dropend .dropdown-menu {
            top: 0;
            left: 100%;
            margin-top: -1px;
        }
        .report-type-dropdown .dropdown-item.active,
        .report-type-dropdown .dropdown-item.active:hover,
        .report-type-dropdown .dropdown-item.active:focus {
            color: #499258 !important;
            background-color: transparent !important;
            font-weight: 600;
        }
    </style>

    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>

    <script>
        function toggleLongFilters() {
            document.querySelectorAll('#filters-long .filter-field').forEach(el => {
                el.classList.toggle('d-none');
            });
        }
    </script>

    <?php try { ?>

    @php
        $wiseFormUrls = [
            'brand' => 'ageing-report-brand-wise',
            'category' => 'ageing-report-category-wise',
            'sub_category' => 'ageing-report-subcategory-wise',
            'company' => 'ageing-report-company-wise',
            'customer' => 'ageing-report-customer-wise',
            'sales_person' => 'ageing-report-salesperson-wise',
        ];
        $wiseFormUrl = $wiseFormUrls[$groupBy ?? 'brand'] ?? 'ageing-report-brand-wise';
    @endphp

    <aside class="left-nav col-12" id="leftSidebar">
        <div class="long-list" id="filters-long">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                @include('backEnd.partials.salesReportTypeMenu')
                <div class="search-filter-container mb-0 d-flex align-items-center flex-wrap gap-2">
                    <input type="text" id="tableSearch" class="form-control d-inline-block"
                        style="font-size:13px;width: 350px;" placeholder="Search">
                    <button class="btn btn-light" onclick="toggleLongFilters()">
                        <i class="ico icon-outline-magnifer"></i>
                    </button>
                    <button type="button" id="exportAgeingWiseReport" class="btn btn-light ms-2">
                        <i class="ico icon-outline-export text-success"></i> Export
                    </button>
                </div>
            </div>

            <div class="search-filter-container mt-1 mb-4 filter-field d-none border">
                <div class="card" style="width: 100%">
                    <div class="card-body">
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => $wiseFormUrl, 'method' => 'POST']) }}
                        <input type="hidden" name="run" value="1">
                        <div class="row">
                            <div class="col-1 mb-2">
                                <label class="form-label">To Date</label>
                                @php
                                    $formattedToDate = @$to_date
                                        ? \Carbon\Carbon::parse($to_date)->format('d/m/Y')
                                        : \Carbon\Carbon::now()->format('d/m/Y');
                                @endphp
                                <input class="form-control date-picker" type="text" name="to_date"
                                    value="{{ $formattedToDate }}" autocomplete="off" required>
                            </div>
                            <div class="col-1 filter-field d-none">
                                <button type="submit" class="btn btn-light mt-4 float-end">
                                    <i class="ico icon-outline-magnifer text-success"></i> Filter
                                </button>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="left-nav-list">
            <div class="table-responsive mb-4 mt-4">
                <table id="long-list" class="table table-hover data-table ageingWiseTable" style="table-layout: fixed;width:100%">
                    <thead>
                        <tr>
                            <th class="text-start" style="width: 220px;">{{ $groupLabel ?? 'Group' }}</th>
                            <th class="text-end" style="width: 110px;">@lang('Bal Qty')</th>
                            <th class="text-end" style="width: 120px;">@lang('Amount')</th>
                            <th class="text-end" style="width: 110px;">@lang('1-30 Days')</th>
                            <th class="text-end" style="width: 110px;">@lang('31-60 Days')</th>
                            <th class="text-end" style="width: 110px;">@lang('61-90 Days')</th>
                            <th class="text-end" style="width: 110px;">@lang('91-120 Days')</th>
                            <th class="text-end" style="width: 120px;">@lang('121 or More Days')</th>
                            <th class="text-end" style="width: 110px;">@lang('Finance Cost')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $foot_qty = 0;
                            $foot_amount = 0;
                            $foot_1_30 = 0;
                            $foot_31_60 = 0;
                            $foot_61_90 = 0;
                            $foot_91_120 = 0;
                            $foot_121 = 0;
                            $foot_finance = 0;
                        @endphp
                        @forelse($wiseRows ?? [] as $row)
                            @php
                                $foot_qty += (float) ($row['balance_qty'] ?? 0);
                                $foot_amount += (float) ($row['amount'] ?? 0);
                                $foot_1_30 += (float) ($row['buckets']['1_30'] ?? 0);
                                $foot_31_60 += (float) ($row['buckets']['31_60'] ?? 0);
                                $foot_61_90 += (float) ($row['buckets']['61_90'] ?? 0);
                                $foot_91_120 += (float) ($row['buckets']['91_120'] ?? 0);
                                $foot_121 += (float) ($row['buckets']['121_plus'] ?? 0);
                                $foot_finance += (float) ($row['finance_cost'] ?? 0);
                            @endphp
                            <tr>
                                <td class="text-start">
                                    @if (!empty($row['drill_url']) && ($row['group_key'] ?? '') !== '' && ($row['group_key'] ?? '0') !== '0')
                                        <a href="{{ $row['drill_url'] }}">{{ $row['label'] ?? '—' }}</a>
                                    @else
                                        {{ $row['label'] ?? '—' }}
                                    @endif
                                </td>
                                <td class="text-end">{{ @App\SysHelper::com_curr_format($row['balance_qty'] ?? 0, 2, '.', ',') }}</td>
                                <td class="text-end">{{ @App\SysHelper::com_curr_format($row['amount'] ?? 0, 2, '.', ',') }}</td>
                                <td class="text-end">{{ @App\SysHelper::com_curr_format($row['buckets']['1_30'] ?? 0, 2, '.', ',') }}</td>
                                <td class="text-end">{{ @App\SysHelper::com_curr_format($row['buckets']['31_60'] ?? 0, 2, '.', ',') }}</td>
                                <td class="text-end">{{ @App\SysHelper::com_curr_format($row['buckets']['61_90'] ?? 0, 2, '.', ',') }}</td>
                                <td class="text-end">{{ @App\SysHelper::com_curr_format($row['buckets']['91_120'] ?? 0, 2, '.', ',') }}</td>
                                <td class="text-end">{{ @App\SysHelper::com_curr_format($row['buckets']['121_plus'] ?? 0, 2, '.', ',') }}</td>
                                <td class="text-end">{{ @App\SysHelper::com_curr_format($row['finance_cost'] ?? 0, 2, '.', ',') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">
                                    @if (!empty($runReport))
                                        No records found
                                    @else
                                        Select to date and filter to load report
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if (!empty($wiseRows) && count($wiseRows) > 0)
                        <tfoot>
                            <tr>
                                <th class="text-start">Total</th>
                                <th class="text-end">{{ @App\SysHelper::com_curr_format($foot_qty, 2, '.', ',') }}</th>
                                <th class="text-end">{{ @App\SysHelper::com_curr_format($foot_amount, 2, '.', ',') }}</th>
                                <th class="text-end">{{ @App\SysHelper::com_curr_format($foot_1_30, 2, '.', ',') }}</th>
                                <th class="text-end">{{ @App\SysHelper::com_curr_format($foot_31_60, 2, '.', ',') }}</th>
                                <th class="text-end">{{ @App\SysHelper::com_curr_format($foot_61_90, 2, '.', ',') }}</th>
                                <th class="text-end">{{ @App\SysHelper::com_curr_format($foot_91_120, 2, '.', ',') }}</th>
                                <th class="text-end">{{ @App\SysHelper::com_curr_format($foot_121, 2, '.', ',') }}</th>
                                <th class="text-end">{{ @App\SysHelper::com_curr_format($foot_finance, 2, '.', ',') }}</th>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </aside>

    <?php } catch (\Exception $e) { ?> {{ $e }} <?php } ?>

@include('backEnd.partials.salesReportTypeMenuScripts')
@include('backEnd.inventory.partials.ageingReportWiseExport')
@endsection

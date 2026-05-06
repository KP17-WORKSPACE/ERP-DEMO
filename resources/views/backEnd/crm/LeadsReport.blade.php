@extends('backEnd.newmasterpage')
@section('mainContent')
    <script>
        let isFullList = false;

        function list_style_new() {
            const leftNav = document.querySelector('.left-nav');
            const content = document.querySelector('.content-container');

            if (!isFullList) {
                // Switch to FULL LIST VIEW
                isFullList = true;

                leftNav.classList.remove('col-3');
                leftNav.classList.add('col-12');
                leftNav.style.width = '100%';

                content.classList.add('d-none');

                $('#long-list').removeClass('d-none');
                $('#short-list').addClass('d-none');

                $('#filters-long').removeClass('d-none');
                $('#filters-short').addClass('d-none');
            } else {
                // Switch to COMPACT VIEW
                isFullList = false;

                leftNav.classList.remove('col-12');
                leftNav.classList.add('col-3');
                leftNav.style.width = '';

                content.classList.remove('d-none');

                $('#long-list').addClass('d-none');
                $('#short-list').removeClass('d-none');

                $('#filters-short').removeClass('d-none');
                $('#filters-long').addClass('d-none');
            }
        }


        //added ny kp
        function toggleLongFilters() {
            console.log("clicked");
            document.querySelectorAll('#filters-long .filter-field').forEach(el => {
                el.classList.toggle('d-none');
            });
        }
    </script>

    <style>
        /* Card-like style for Bootstrap 3 */
        .task-card {
            border-radius: 6px;
            padding: 10px;
            text-align: center;
            margin-bottom: 10px;

            background-color: #deebe1;
        }

        .task-icon {
            margin-bottom: 8px;
            font-size: 24px;
            /* fa-lg equivalent */
        }

        .task-title {
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 14px;
        }

        .task-count {
            font-weight: bold;
            font-size: 14px;
        }

        .task-row div {
            padding: 5px
        }

        @media (min-width: 1250px) {
            .col-xl-1-8 {
                max-width: 12.5%;
                /* 100 / 8 = 12.5% */
                float: left;
            }
        }

        .sub-status {
            font-size: 12px;
            margin-top: 6px;
            border-top: 1px dashed rgba(255, 255, 255, 0.4);
            padding-top: 6px;
        }

        .sub-status div {
            margin-bottom: 3px;
            font-size: 12px;
            display: flex;
            justify-content: space-between;

            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 5px;
        }

        .sub-status-title {
            font-weight: normal;
            opacity: 0.9;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: inline-block;
            max-width: 120px;
            /* adjust based on layout */
            vertical-align: top;
        }

        .sub-status-value {
            font-weight: bold;
        }
    </style>


    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>

    <?php try { ?>

    <aside class="left-nav col-12" id="leftSidebar">


        <div class="long-list" id="filters-long">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Leads Report ({{ $base_company->company_name }})
                </h4>
                <div class="search-filter-container mb-0">


                    <button class="btn btn-light" onclick="toggleLongFilters()">
                        <i class="ico icon-outline-magnifer"></i>
                    </button>

                    <div class="dropdown">
                        <button class="btn btn-light text-dark dropdown-toggle syscom-dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ico icon-outline-hamburger-menu"></i>
                        </button>
                        <ul class="dropdown-menu" style="">


                        </ul>
                    </div>


                </div>
            </div>

            <div class="search-filter-container mt-1 mb-4 filter-field d-none border">

                <div class="card" style="width: 100%">
                    <div class="card-body">
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-leads-report/' . $ctrl_company_id . '/' . $ctrl_date . '/' . $ctrl_date2, 'method' => 'POST', 'id' => 'crm-leads-report-company']) }}

                        <div class="row">

                            <div class="col-1-5 filter-field d-none">
                                <label for="" class="form-label">Company</label>
                                <select class="form-control js-example-basic-single" name="company_id" id="company_id">
                                    <option value="">-Select-</option>
                                    @foreach ($company as $value)
                                        <option value="{{ @$value->id }}"
                                            @if ($ctrl_company_id == $value->id) selected @endif>
                                            {{ @$value->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">Owner</label>
                                <select class="form-control js-example-basic-single" name="owner_id" id="owner_id">
                                    <option value="">-Select-</option>
                                    @foreach ($staff as $value)
                                        <option value="{{ @$value->user_id }}"
                                            @if ($ctrl_owner == $value->user_id) selected @endif>
                                            {{ @$value->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">Form Date</label>
                                <input class="form-control date-picker" id="date" type="text" autocomplete="off"
                                    name="date"
                                    value="{{ $ctrl_date ? \Carbon\Carbon::parse($ctrl_date)->format('d/m/Y') : '' }}">
                            </div>

                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">To Date</label>
                                <input class="form-control date-picker" id="date" type="text" autocomplete="off"
                                    name="date2"
                                    value="{{ $ctrl_date2 ? \Carbon\Carbon::parse($ctrl_date2)->format('d/m/Y') : '' }}">
                            </div>


                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">Filter By</label>
                                <select class="form-control" name="filter_by" id="filter_by" onchange="this.form.submit()">
                                    <option value="" @if ($filter_by == '') selected @endif>-Select-
                                    </option>
                                    <option value="today">Today</option>
                                    <option value="this_week">This Week
                                    </option>
                                    <option value="last_week">Last Week
                                    </option>
                                    <option value="this_month">This Month
                                    </option>
                                    <option value="last_month">Last Month
                                    </option>
                                    <option value="last_6_months">Last 6
                                        Months
                                    </option>
                                    <option value="this_year">This Year
                                    </option>
                                    <option value="last_year">Last Year
                                    </option>
                                </select>
                            </div>


                            <div class="col-md-3 filter-field d-none">
                                <button type="submit" class="btn btn-success mt-4 rounded-0" id="btnSubmit">Filter</button>
                            </div>

                        </div>
                        {{ Form::close() }}


                    </div>
                </div>
            </div>
        </div>

        <div class="left-nav-list">


            <div class="row task-row">


                <div class="cls-m-12 col-md-6 col-lg-3 col-xl-1-8 filter-by-status" data-status-id="1"
                    style="cursor:pointer;">
                    <div class="task-card">
                        <div><i class="fa 	fa-plus-circle task-icon"></i></div>
                        <h5 class="task-title">New</h5>
                        <div id="totalTasks" class="task-count">{{ $base_total_leads['new'] ?? 0 }}
                            ({{ $base_total_leads['total'] > 0 ? round((($base_total_leads['new'] ?? 0) / $base_total_leads['total']) * 100, 2) : 0 }}%)
                        </div>
                        <div class="sub-status collapse" id="newCollapse">
                            <div><span class="sub-status-title">Just received, uncontacted</span><span
                                    class="sub-status-value">
                                    {{ $base_total_leads['just_received_uncontacted'] ?? 0 }}</span></div>
                        </div>

                        <div class="task-toggle-indicator" data-bs-toggle="collapse" href="#newCollapse" role="button"
                            aria-expanded="false" aria-controls="newCollapse">
                            <i class="ico icon-outline-alt-arrow-down toggle-icon" id="icon-new"></i>
                        </div>
                    </div>
                </div>

                <div class="cls-m-12 col-md-6 col-lg-3 col-xl-1-8 filter-by-status" data-status-id="4"
                    style="cursor:pointer;">
                    <div class="task-card">
                        <div><i class="fa fa-hourglass-half task-icon"></i></div>
                        <h5 class="task-title">Pending</h5>
                        <div class="task-count">
                            {{ $base_total_leads['pending_response'] ?? 0 }}
                            ({{ $base_total_leads['total'] > 0 ? round((($base_total_leads['pending_response'] ?? 0) / $base_total_leads['total']) * 100, 2) : 0 }}%)
                        </div>

                        <div class="sub-status collapse" id="pendingCollapse">
                            <div><span class="sub-status-title">Waiting for EUD</span><span
                                    class="sub-status-value">{{ $base_total_leads['waiting_for_eud'] ?? 0 }}</span>
                            </div>

                            <div><span class="sub-status-title">Waiting for Vendor Price</span><span
                                    class="sub-status-value">{{ $base_total_leads['waiting_for_vendor_price'] ?? 0 }}</span>
                            </div>
                            <div><span class="sub-status-title">Quoted - Waiting for Response</span><span
                                    class="sub-status-value">{{ $base_total_leads['quoted_waiting_response'] ?? 0 }}</span>
                            </div>
                            <div><span class="sub-status-title">Other Reasons</span><span
                                    class="sub-status-value">{{ $base_total_leads['pending_response_other'] ?? 0 }}</span>
                            </div>
                        </div>

                        <div class="task-toggle-indicator" data-bs-toggle="collapse" href="#pendingCollapse"
                            role="button" aria-expanded="false" aria-controls="pendingCollapse">
                            <i class="ico icon-outline-alt-arrow-down toggle-icon" id="icon-new"></i>
                        </div>
                    </div>
                </div>

                <div class="cls-m-12 col-md-6 col-lg-3 col-xl-1-8 filter-by-status" data-status-id="2"
                    style="cursor:pointer;">

                    <div class="task-card">
                        <div><i class="fa fa-thumbs-up task-icon"></i></div>
                        <h5 class="task-title">Qualified</h5>
                        <div id="dueTasks" class="task-count">
                            {{ $base_total_leads['qualified'] ?? 0 }}
                            ({{ $base_total_leads['total'] > 0 ? round((($base_total_leads['qualified'] ?? 0) / $base_total_leads['total']) * 100, 2) : 0 }}%)
                        </div>
                        <div class="sub-status collapse" id="qualifiedCollapse">
                            <div><span class="sub-status-title">Sent to Sales</span><span
                                    class="sub-status-value">{{ $base_total_leads['sent_to_sales'] ?? 0 }}</span>
                            </div>
                            <div><span class="sub-status-title">Prospecting</span><span
                                    class="sub-status-value">{{ $base_total_leads['deal_prospecting'] ?? 0 }}</span>
                            </div>
                            <div><span class="sub-status-title">Quote</span><span
                                    class="sub-status-value">{{ $base_total_leads['deal_quote'] ?? 0 }}</span></div>
                            <div><span class="sub-status-title">Closure</span><span
                                    class="sub-status-value">{{ $base_total_leads['deal_closure'] ?? 0 }}</span></div>
                            <div><span class="sub-status-title">Won</span><span
                                    class="sub-status-value">{{ $base_total_leads['deal_won'] ?? 0 }}</span></div>
                            <div><span class="sub-status-title">Lost</span><span
                                    class="sub-status-value">{{ $base_total_leads['deal_lost'] ?? 0 }}</span></div>
                        </div>

                        <div class="task-toggle-indicator" data-bs-toggle="collapse" href="#qualifiedCollapse"
                            role="button" aria-expanded="false" aria-controls="qualifiedCollapse">
                            <i class="ico icon-outline-alt-arrow-down toggle-icon" id="icon-new"></i>
                        </div>
                    </div>
                </div>

                <div class="cls-m-12 col-md-6 col-lg-3 col-xl-1-8 filter-by-status" data-status-id="3"
                    style="cursor:pointer;">

                    <div class="task-card">
                        <div><i class="fa fa-times-circle task-icon"></i></div>
                        <h5 class="task-title">Unqualified</h5>
                        <div id="notStartedCount" class="task-count">{{ $base_total_leads['unqualified'] ?? 0 }}
                            ({{ $base_total_leads['total'] > 0 ? round((($base_total_leads['unqualified'] ?? 0) / $base_total_leads['total']) * 100, 2) : 0 }}%)
                        </div>
                        <div class="sub-status collapse" id="unqualifiedCollapse">
                            <div><span class="sub-status-title">Budget Issue</span><span
                                    class="sub-status-value">{{ $base_total_leads['budget_issue'] ?? 0 }}</span>
                            </div>
                            <div><span class="sub-status-title">Not Interested</span><span
                                    class="sub-status-value">{{ $base_total_leads['not_interested'] ?? 0 }}</span>
                            </div>
                            <div><span class="sub-status-title">Wrong Contact</span><span
                                    class="sub-status-value">{{ $base_total_leads['wrong_contact'] ?? 0 }}</span>
                            </div>
                            <div><span class="sub-status-title">Timeline not matching</span><span
                                    class="sub-status-value">{{ $base_total_leads['timeline_not_matching'] ?? 0 }}</span>
                            </div>
                            <div><span class="sub-status-title">Product/Service mismatch</span><span
                                    class="sub-status-value">{{ $base_total_leads['product_service_mismatch'] ?? 0 }}</span>
                            </div>
                            <div><span class="sub-status-title">Other Reason</span><span
                                    class="sub-status-value">{{ $base_total_leads['unqualified_other'] ?? 0 }}</span>
                            </div>
                        </div>

                        <div class="task-toggle-indicator" data-bs-toggle="collapse" href="#unqualifiedCollapse"
                            role="button" aria-expanded="false" aria-controls="unqualifiedCollapse">
                            <i class="ico icon-outline-alt-arrow-down toggle-icon" id="icon-new"></i>
                        </div>
                    </div>
                </div>

                <div class="cls-m-12 col-md-6 col-lg-3 col-xl-1-8 filter-by-status" data-status-id="10"
                    style="cursor:pointer;">

                    <div class="task-card">
                        <div><i class="fa 	fa-archive  task-icon"></i></div>
                        <h5 class="task-title">Closed</h5>
                        <div id="inProgressCount" class="task-count">{{ $base_total_leads['closed'] ?? 0 }}
                            ({{ $base_total_leads['total'] > 0 ? round((($base_total_leads['closed'] ?? 0) / $base_total_leads['total']) * 100, 2) : 0 }}%)
                        </div>
                        <div class="sub-status collapse" id="closedCollapse">
                            <div><span class="sub-status-title">No Response</span><span
                                    class="sub-status-value">{{ $base_total_leads['no_response'] ?? 0 }}</span>
                            </div>
                            <div><span class="sub-status-title">Other Reason</span><span class="sub-status-value">
                                    {{ $base_total_leads['closed_other'] ?? 0 }}</span>
                            </div>
                        </div>

                        <div class="task-toggle-indicator" data-bs-toggle="collapse" href="#closedCollapse"
                            role="button" aria-expanded="false" aria-controls="closedCollapse">
                            <i class="ico icon-outline-alt-arrow-down toggle-icon" id="icon-new"></i>
                        </div>
                    </div>
                </div>

                <div class="cls-m-12 col-md-6 col-lg-3 col-xl-1-8">
                    <div class="task-card">
                        <div><i class="fa fa-chart-bar task-icon"></i></div>
                        <h5 class="task-title">Total Leads</h5>
                        <div id="completedCount" class="task-count">{{ $base_total_leads['total'] ?? 0 }} (100%)</div>
                    </div>
                </div>

                <div class="cls-m-12 col-md-6 col-lg-3 col-xl-1-8">

                    <div class="task-card">
                        <div><i class="fa fa-stopwatch task-icon"></i></div>
                        <h5 class="task-title">Av. Aging (Days)</h5>
                        <div id="completedCount" class="task-count">
                            {{ number_format($base_total_leads['avg_aging_days'], 2) ?? 0 }} Days
                        </div>
                    </div>
                </div>

                <div class="cls-m-12 col-md-6 col-lg-3 col-xl-1-8">

                    <div class="task-card">
                        <div><i class="fa  fa-percentage task-icon"></i></div>
                        <h5 class="task-title">Conv. Rate (%)</h5>
                        <div id="completedCount" class="task-count">
                            {{ $base_total_leads['total'] > 0 ? round(($base_total_leads['qualified'] / $base_total_leads['total']) * 100, 2) : 0 }}%
                        </div>
                    </div>
                </div>
            </div>


            <div class="table-responsive mb-4 mt-4">
                <table id="long-list" class="table table-hover" style="table-layout: fixed;width:100%">

                    <thead>
                        <tr>
                            <th>@lang('Name')</th>
                            <th class="text-center">@lang('Total Leads')</th>
                            <th class="text-center">@lang('New')</th>
                            <th class="text-center">@lang('Pending')</th>
                            <th class="text-center">@lang('Qualified')</th>
                            <th class="text-center">@lang('Unqualified')</th>
                            <th class="text-center">@lang('Closed')</th>
                            <th class="text-center">@lang('Conversion Rate')</th>
                            <th class="text-center">@lang('Av. Aging Days')</th>
                            <th class="text-center">@lang('No. Followups')</th>
                            <th class="text-center">@lang('Avg. Followups')</th>
                        </tr>
                    </thead>


                    <tbody>
                        @php $row_counter = 0; @endphp
                        @foreach ($sales_persons as $value)
                            @php $row_counter++; @endphp

                            <tr onclick="view_in_ex_tr({{ $value['user_id'] }})"
                                style="cursor:pointer;background: {{ $row_counter % 2 == 1 ? 'white' : 'rgba(0, 0, 0, 0.05);' }};">

                                <td>
                                    @php
                                        $queryParams = [
                                            'base_company_id' => $base_company->id ?? '',
                                            'date' => $ctrl_date,
                                            'date2' => $ctrl_date2,
                                        ];
                                    @endphp
                                    <a target="_blank"
                                        href="{{ url('crm-leads-staff-report/' . $value['user_id'] . '?' . http_build_query($queryParams)) }}">
                                        {{ $value['full_name'] ?? '' }}
                                    </a>
                                </td>
                                <td class="text-center"><strong>{{ $value['total'] ?? 0 }}</strong></td>
                                <td class="text-center">{{ $value['new'] ?? 0 }}</td>
                                <td class="text-center">{{ $value['pending_response'] ?? 0 }}</td>
                                <td class="text-center">{{ ($value['qualified'] ?? 0) + ($value['converted'] ?? 0) }}
                                </td>
                                <td class="text-center">{{ $value['unqualified'] ?? 0 }}</td>
                                <td class="text-center">{{ $value['closed'] ?? 0 }}</td>
                                <td class="text-center">
                                    {{ ($value['total'] ?? 0) > 0
                                        ? number_format(((($value['converted'] ?? 0) + ($value['qualified'] ?? 0)) / $value['total']) * 100, 2) . '%'
                                        : '0.00%' }}
                                </td>
                                <td class="text-center">
                                    {{ isset($value['avg_aging_days']) ? number_format($value['avg_aging_days'], 2) : '0.00' }}
                                </td>

                                <td class="text-center">{{ $value['total_followups'] ?? 0 }}</td>
                                <td class="text-center">
                                    {{ isset($value['avg_followups']) ? number_format($value['avg_followups'], 2) : '0.00' }}
                                </td>


                            </tr>


                            <tr id="ex_tr_{{ $value['user_id'] }}" style="display:none">

                                <td colspan="11">
                                 
                                        <div class="d-flex flex-wrap justify-content-between">

                                            <div class="card flex-fill mx-2 mb-3" style="min-width: 200px;">
                                                <div class="card-body p-3">
                                                    <strong class="text-primary">New
                                                        ({{ $value['new'] ?? 0 }})</strong><br>
                                                    <small>Just received, uncontacted:
                                                        {{ $value['new'] ?? 0  }}</small>
                                                </div>
                                            </div>

                                            <div class="card flex-fill mx-2 mb-3" style="min-width: 250px;">
                                                <div class="card-body p-3">
                                                    <strong class="text-warning">Pending Response
                                                        ({{ $value['pending_response'] ?? 0 }})</strong><br>
                                                    <small>Waiting for EUD: {{ $value['waiting_for_eud'] }}</small><br>
                                                    <small>Waiting for Vendor Price:
                                                        {{ $value['waiting_for_vendor_price'] }}</small><br>
                                                    <small>Quoted - Waiting for Response:
                                                        {{ $value['quoted_waiting_response'] }}</small><br>
                                                    <small>Other Reasons: {{ $value['pending_response_other'] }}</small>
                                                </div>
                                            </div>

                                            <div class="card flex-fill mx-2 mb-3" style="min-width: 220px;">
                                                <div class="card-body p-3">
                                                    <strong class="text-success">Qualified
                                                        ({{ $value['qualified'] ?? 0 }})</strong><br>
                                                    <small>Sent to Sales: {{  $value['qualified'] ?? 0 }}</small><br><br>
                                                    <strong class="text-success">Deals
                                                        ({{ $value['converted'] ?? 0 }})</strong><br>
                                                    <small>Prospecting: {{ $value['deal_prospecting'] ?? 0 }}</small><br>
                                                    <small>Quote: {{ $value['deal_quote'] ?? 0 }}</small><br>
                                                    <small>Closure: {{ $value['deal_closure'] ?? 0 }}</small><br>
                                                    <small>Won: {{ $value['deal_won'] ?? 0 }}</small><br>
                                                    <small>Lost: {{ $value['deal_lost'] ?? 0 }}</small>
                                                </div>
                                            </div>

                                            <div class="card flex-fill mx-2 mb-3" style="min-width: 250px;">
                                                <div class="card-body p-3">
                                                    <strong class="text-danger">Unqualified
                                                        ({{ $value['unqualified'] ?? 0 }})</strong><br>
                                                    <small>Budget Issue: {{ $value['budget_issue'] }}</small><br>
                                                    <small>Not Interested: {{ $value['not_interested'] }}</small><br>
                                                    <small>Wrong Contact: {{ $value['wrong_contact'] }}</small><br>
                                                    <small>Timeline not matching:
                                                        {{ $value['timeline_not_matching'] }}</small><br>
                                                    <small>Product/Service mismatch:
                                                        {{ $value['product_service_mismatch'] }}</small><br>
                                                    <small>Other Reason: {{ $value['unqualified_other'] }}</small>
                                                </div>
                                            </div>

                                            <div class="card flex-fill mx-2 mb-3" style="min-width: 200px;">
                                                <div class="card-body p-3">
                                                    <strong class="text-secondary">Closed
                                                        ({{ $value['closed'] ?? 0 }})</strong><br>
                                                    <small>No Response: {{ $value['no_response'] }}</small><br>
                                                    <small>Other Reason: {{ $value['closed_other'] }}</small>
                                                </div>
                                            </div>

                                        </div>
                              

                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                    <tfoot style="background: #7e7e7e; color: #ffffff;">
                        <thead onclick="view_in_ex_tr(0)">
                            <th></th>
                            <th class="text-center">{{ $base_total_leads['total'] }}</th>
                            <th class="text-center">{{ $base_total_leads['new'] }}</th>
                            <th class="text-center">{{ $base_total_leads['pending_response'] }}</th>
                            <th class="text-center">{{ $base_total_leads['qualified'] }}</th>
                            <th class="text-center">{{ $base_total_leads['unqualified'] }}</th>
                            <th class="text-center">{{ $base_total_leads['closed'] }}</th>
                            <th></th>
                            <th></th>

                            <th class="text-center">{{ $base_total_leads['total_followups'] }}</th>
                            <th class="text-center">{{ number_format($base_total_leads['avg_followups'], 2) ?? 0 }}
                            </th>
                        </thead>

                        <tr id="ex_tr_0" style="display:none">

                            <td colspan="11">
                                <div class="row text-left px-3 py-2">

                                    <div class="col-md-3">
                                        <strong>New ({{ $base_total_leads['new'] ?? 0 }})</strong><br>
                                        Just received, uncontacted:
                                        {{ $base_total_leads['just_received_uncontacted'] }}
                                    </div>

                                    <div class="col-md-3">
                                        <strong>Pending Response
                                            ({{ $base_total_leads['pending_response'] ?? 0 }})</strong><br>
                                        Waiting for EUD: {{ $base_total_leads['waiting_for_eud'] }}<br>
                                        Waiting for Vendor Price:
                                        {{ $base_total_leads['waiting_for_vendor_price'] }}<br>
                                        Quoted - Waiting for Response:
                                        {{ $base_total_leads['quoted_waiting_response'] }}<br>
                                        Other Reasons: {{ $base_total_leads['pending_response_other'] }}
                                    </div>

                                    <div class="col-md-2">
                                        <strong>Qualified
                                            ({{ ($base_total_leads['qualified'] ?? 0) - ($base_total_leads['converted'] ?? 0) }})</strong><br>
                                        Sent to Sales: {{ $base_total_leads['sent_to_sales'] }}

                                        <br>

                                        <strong>Deals ({{ $base_total_leads['converted'] ?? 0 }})</strong><br>
                                        Prospecting: {{ $base_total_leads['deal_prospecting'] }}<br>
                                        Quote: {{ $base_total_leads['deal_quote'] }}<br>
                                        Closure: {{ $base_total_leads['deal_closure'] }}<br>
                                        Won: {{ $base_total_leads['deal_won'] }} <br>
                                        Lost: {{ $base_total_leads['deal_lost'] }}

                                    </div>

                                    <div class="col-md-2">
                                        <strong>Unqualified ({{ $base_total_leads['unqualified'] ?? 0 }})</strong><br>
                                        Budget Issue: {{ $base_total_leads['budget_issue'] }}<br>
                                        Not Interested: {{ $base_total_leads['not_interested'] }}<br>
                                        Wrong Contact: {{ $base_total_leads['wrong_contact'] }}<br>
                                        Timeline not matching: {{ $base_total_leads['timeline_not_matching'] }}<br>
                                        Product/Service mismatch:
                                        {{ $base_total_leads['product_service_mismatch'] }}<br>
                                        Other Reason: {{ $base_total_leads['unqualified_other'] }}
                                    </div>

                                    <div class="col-md-2">
                                        <strong>Closed ({{ $base_total_leads['closed'] ?? 0 }})</strong><br>
                                        No Response: {{ $base_total_leads['no_response'] }}<br>
                                        Other Reason: {{ $base_total_leads['closed_other'] }}
                                    </div>

                                </div>
                            </td>
                        </tr>

                    </tfoot>



                </table>
            </div>
        </div>
    </aside>









    <script>
        function view_in_ex_tr(id) {

            var tr_ex = $('#ex_tr_' + id);

            if (tr_ex.css('display') === 'none') {
                tr_ex.css('display', '');
            } else {
                tr_ex.css('display', 'none');
            }
        }
    </script>

    <script>
        $(document).ready(function() {
            $('.collapse').on('show.bs.collapse', function() {
                $(this).closest('.task-card').find('.toggle-icon')
                    .removeClass('ico icon-outline-alt-arrow-down ')
                    .addClass('ico icon-outline-alt-arrow-up');
            });

            $('.collapse').on('hide.bs.collapse', function() {
                $(this).closest('.task-card').find('.toggle-icon')
                    .removeClass('ico icon-outline-alt-arrow-up ')
                    .addClass('ico icon-outline-alt-arrow-down');
            });
        });
    </script>



    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@endsection

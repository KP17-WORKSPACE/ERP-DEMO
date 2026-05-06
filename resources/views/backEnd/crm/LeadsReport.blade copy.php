@extends('backEnd.masterpage')
@section('mainContent')

    <?php
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>


    <style>
        /* Card-like style for Bootstrap 3 */
        .task-card {
            border-radius: 6px;
            padding: 10px;
            text-align: center;
            margin-bottom: 15px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);

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



    <?php try { ?>
    <div class="container-fluid">
        <div class="d-sm-flex justify-content-between">
            <div class="mb-3">
                <h2 class="page-heading m-0">Leads Report <span
                        class="text-primary">({{ $base_company->company_name }})</span></h2>
                <span class="page-label">Home - Leads Report</span>
            </div>

            <table>
                <tr>
                    <td>
                        <button type="button" class="btn btn-primary" data-toggle="collapse" href="#collapseExample"
                            role="button" aria-expanded="false" aria-controls="collapseExample"><i
                                class="fa fa-filter mr-1"></i>Search</button>
                    </td>
                </tr>
            </table>
        </div>



        <div class="collapse show" id="collapseExample">
            <div class="card shadow mb-4 p-4">

                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-leads-report/' . $ctrl_company_id . '/' . $ctrl_date . '/' . $ctrl_date2, 'method' => 'POST', 'id' => 'crm-leads-report-company']) }}
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <label for="" class="form-check-label">Company</label>
                        <select class="form-control js-example-basic-single" name="company_id" id="company_id">
                            <option value="">-Select-</option>
                            @foreach ($company as $value)
                                <option value="{{ @$value->id }}" @if ($ctrl_company_id == $value->id) selected @endif>
                                    {{ @$value->company_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 mb-2">
                        <label for="" class="form-check-label">Owner</label>
                        <select class="form-control js-example-basic-single" name="owner_id" id="owner_id">
                            <option value="">-Select-</option>
                            @foreach ($staff as $value)
                                <option value="{{ @$value->user_id }}" @if ($ctrl_owner == $value->user_id) selected @endif>
                                    {{ @$value->full_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 mb-2">
                        <label for="" class="form-check-label">From Date</label>
                        <input class="form-control datepicker" id="date" type="date" autocomplete="off"
                            name="date" value="{{ $ctrl_date }}" required onchange="set_filter()">
                    </div>
                    <div class="col-md-2 mb-2">
                        <label for="" class="form-check-label">To Date</label>
                        <input class="form-control" id="date2" type="date" autocomplete="off" name="date2"
                            value="{{ $ctrl_date2 }}" required onchange="set_filter()">
                    </div>
                    <div class="col-md-2 mb-2">
                        <label for="" class="form-check-label">Filter By</label>
                        <select class="form-control" name="filter_by" id="filter_by" onchange="this.form.submit()">
                            <option value="" @if ($filter_by == '') selected @endif>-Select-</option>
                            <option value="today" @if ($filter_by == 'today') selected @endif>Today</option>
                            <option value="this_week" @if ($filter_by == 'this_week') selected @endif>This Week
                            </option>
                            <option value="last_week" @if ($filter_by == 'last_week') selected @endif>Last Week
                            </option>
                            <option value="this_month" @if ($filter_by == 'this_month') selected @endif>This Month
                            </option>
                            <option value="last_month" @if ($filter_by == 'last_month') selected @endif>Last Month
                            </option>
                            <option value="last_6_months" @if ($filter_by == 'last_6_months') selected @endif>Last 6 Months
                            </option>
                            <option value="this_year" @if ($filter_by == 'this_year') selected @endif>This Year
                            </option>
                            <option value="last_year" @if ($filter_by == 'last_year') selected @endif>Last Year
                            </option>
                        </select>
                    </div>
                    <script>
                        function set_filter() {
                            if ($('#date').val() != "" || $('#date2').val() != "") {
                                $('#filter_by').val('')
                            }
                        }
                    </script>


                    <div class="col-1"><br />
                        <button type="submit" class="btn btn-primary" id="btnSubmit">Filter</button>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>





        <div class="row task-row">


            <div class="cls-m-12 col-md-6 col-lg-3 col-xl-1-8 filter-by-status" data-status-id="1" style="cursor:pointer;">
                <div class="task-card bg-primary text-white">
                    <div><i class="fa 	fa-plus-circle task-icon"></i></div>
                    <h5 class="task-title">New</h5>
                    <div id="totalTasks" class="task-count">{{ $base_total_leads['new'] ?? 0 }}
                        ({{ $base_total_leads['total'] > 0 ? round((($base_total_leads['new'] ?? 0) / $base_total_leads['total']) * 100, 2) : 0 }}%)
                    </div>
                    <div class="sub-status collapse" id="newCollapse">
                        <div><span class="sub-status-title">Just received, uncontacted</span><span class="sub-status-value">
                                {{ $base_total_leads['just_received_uncontacted'] ?? 0 }}</span></div>
                    </div>

                    <div class="task-toggle-indicator" data-toggle="collapse" href="#newCollapse" role="button"
                        aria-expanded="false" aria-controls="newCollapse">
                        <i class="fa fa-chevron-down toggle-icon" id="icon-new"></i>
                    </div>
                </div>
            </div>

            <div class="cls-m-12 col-md-6 col-lg-3 col-xl-1-8 filter-by-status" data-status-id="4" style="cursor:pointer;">
                <div class="task-card bg-warning text-dark">
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

                    <div class="task-toggle-indicator" data-toggle="collapse" href="#pendingCollapse" role="button"
                        aria-expanded="false" aria-controls="pendingCollapse">
                        <i class="fa fa-chevron-down toggle-icon" id="icon-new"></i>
                    </div>
                </div>
            </div>

            <div class="cls-m-12 col-md-6 col-lg-3 col-xl-1-8 filter-by-status" data-status-id="2"
                style="cursor:pointer;">

                <div class="task-card bg-success text-white">
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

                    <div class="task-toggle-indicator" data-toggle="collapse" href="#qualifiedCollapse" role="button"
                        aria-expanded="false" aria-controls="qualifiedCollapse">
                        <i class="fa fa-chevron-down toggle-icon" id="icon-new"></i>
                    </div>
                </div>
            </div>

            <div class="cls-m-12 col-md-6 col-lg-3 col-xl-1-8 filter-by-status" data-status-id="3"
                style="cursor:pointer;">

                <div class="task-card bg-danger text-white">
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

                    <div class="task-toggle-indicator" data-toggle="collapse" href="#unqualifiedCollapse" role="button"
                        aria-expanded="false" aria-controls="unqualifiedCollapse">
                        <i class="fa fa-chevron-down toggle-icon" id="icon-new"></i>
                    </div>
                </div>
            </div>

            <div class="cls-m-12 col-md-6 col-lg-3 col-xl-1-8 filter-by-status" data-status-id="10"
                style="cursor:pointer;">

                <div class="task-card bg-secondary text-white">
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

                    <div class="task-toggle-indicator" data-toggle="collapse" href="#closedCollapse" role="button"
                        aria-expanded="false" aria-controls="closedCollapse">
                        <i class="fa fa-chevron-down toggle-icon" id="icon-new"></i>
                    </div>
                </div>
            </div>

            <div class="cls-m-12 col-md-6 col-lg-3 col-xl-1-8">
                <div class="task-card bg-info text-white">
                    <div><i class="fa fa-chart-bar task-icon"></i></div>
                    <h5 class="task-title">Total Leads</h5>
                    <div id="completedCount" class="task-count">{{ $base_total_leads['total'] ?? 0 }} (100%)</div>
                </div>
            </div>

            <div class="cls-m-12 col-md-6 col-lg-3 col-xl-1-8">

                <div class="task-card bg-secondary text-white">
                    <div><i class="fa fa-stopwatch task-icon"></i></div>
                    <h5 class="task-title">Av. Aging (Days)</h5>
                    <div id="completedCount" class="task-count">
                        {{ number_format($base_total_leads['avg_aging_days'], 2) ?? 0 }} Days
                    </div>
                </div>
            </div>

            <div class="cls-m-12 col-md-6 col-lg-3 col-xl-1-8">

                <div class="task-card bg-success text-white">
                    <div><i class="fa  fa-percentage task-icon"></i></div>
                    <h5 class="task-title">Conv. Rate (%)</h5>
                    <div id="completedCount" class="task-count">
                        {{ $base_total_leads['total'] > 0 ? round(($base_total_leads['qualified'] / $base_total_leads['total']) * 100, 2) : 0 }}%
                    </div>
                </div>
            </div>
        </div>



        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="dataTable1" width="100%" cellspacing="0">
                        <thead>
                            @if (session()->has('message-success') != '' || session()->get('message-danger') != '')
                                <tr>
                                    <td colspan="7">
                                        @if (session()->has('message-success'))
                                            <div class="alert alert-success">
                                                {{ session()->get('message-success') }}
                                            </div>
                                        @elseif(session()->has('message-danger'))
                                            <div class="alert alert-danger">
                                                {{ session()->get('message-danger') }}
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endif

                            <tr>
                                <th>@lang('Name')</th>
                                <th class="text-right">@lang('Total Leads')</th>
                                <th class="text-right">@lang('New')</th>
                                <th class="text-right">@lang('Pending')</th>
                                <th class="text-right">@lang('Qualified')</th>
                                <th class="text-right">@lang('Unqualified')</th>
                                <th class="text-right">@lang('Closed')</th>
                                <th class="text-right">@lang('Conversion Rate')</th>
                                <th class="text-right">@lang('Av. Aging Days')</th>
                                <th class="text-right">@lang('No. Followups')</th>
                                <th class="text-right">@lang('Avg. Followups')</th>

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
                                    <td class="text-right">{{ $value['new'] ?? 0 }}</td>
                                    <td class="text-right">{{ $value['pending_response'] ?? 0 }}</td>
                                    <td class="text-right">{{ ($value['qualified'] ?? 0) + ($value['converted'] ?? 0) }}
                                    </td>
                                    <td class="text-right">{{ $value['unqualified'] ?? 0 }}</td>
                                    <td class="text-right">{{ $value['closed'] ?? 0 }}</td>
                                    <td class="text-right">
                                        {{ ($value['total'] ?? 0) > 0
                                            ? number_format(((($value['converted'] ?? 0) + ($value['qualified'] ?? 0)) / $value['total']) * 100, 2) . '%'
                                            : '0.00%' }}
                                    </td>
                                    <td class="text-right">
                                        {{ isset($value['avg_aging_days']) ? number_format($value['avg_aging_days'], 2) : '0.00' }}
                                    </td>

                                    <td class="text-right">{{ $value['total_followups'] ?? 0 }}</td>
                                    <td class="text-right">
                                        {{ isset($value['avg_followups']) ? number_format($value['avg_followups'], 2) : '0.00' }}
                                    </td>


                                </tr>


                                <tr id="ex_tr_{{ $value['user_id'] }}" style="display:none">

                                    <td colspan="12">
                                        <div class="row text-left px-3 py-2">

                                            <div class="col-md-3">
                                                <strong>New ({{ $value['new'] ?? 0 }})</strong><br>
                                                Just received, uncontacted: {{ $value['just_received_uncontacted'] }}
                                            </div>

                                            <div class="col-md-3">
                                                <strong>Pending Response
                                                    ({{ $value['pending_response'] ?? 0 }})
                                                </strong><br>
                                                Waiting for EUD: {{ $value['waiting_for_eud'] }}<br>
                                                Waiting for Vendor Price: {{ $value['waiting_for_vendor_price'] }}<br>
                                                Quoted - Waiting for Response: {{ $value['quoted_waiting_response'] }}<br>
                                                Other Reasons: {{ $value['pending_response_other'] }}
                                            </div>

                                            <div class="col-md-2">
                                                <strong>Qualified ({{ $value['qualified'] ?? 0 }})</strong><br>
                                                Sent to Sales: {{ $value['sent_to_sales'] }}

                                                <br>

                                                <strong>Deals ({{ $value['converted'] ?? 0 }})</strong><br>
                                                Prospecting: {{ $value['deal_prospecting'] }}<br>
                                                Quote: {{ $value['deal_quote'] }}<br>
                                                Closure: {{ $value['deal_closure'] }}<br>
                                                Won: {{ $value['deal_won'] }} <br>
                                                Lost: {{ $value['deal_lost'] }}

                                            </div>

                                            <div class="col-md-2">
                                                <strong>Unqualified ({{ $value['unqualified'] ?? 0 }})</strong><br>
                                                Budget Issue: {{ $value['budget_issue'] }}<br>
                                                Not Interested: {{ $value['not_interested'] }}<br>
                                                Wrong Contact: {{ $value['wrong_contact'] }}<br>
                                                Timeline not matching: {{ $value['timeline_not_matching'] }}<br>
                                                Product/Service mismatch: {{ $value['product_service_mismatch'] }}<br>
                                                Other Reason: {{ $value['unqualified_other'] }}
                                            </div>

                                            <div class="col-md-2">
                                                <strong>Closed ({{ $value['closed'] ?? 0 }})</strong><br>
                                                No Response: {{ $value['no_response'] }}<br>
                                                Other Reason: {{ $value['closed_other'] }}
                                            </div>

                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                        <tfoot style="background: #7e7e7e; color: #ffffff;">
                            <tr onclick="view_in_ex_tr(0)">
                                <td></td>
                                <td class="text-center">{{ $base_total_leads['total'] }}</td>
                                <td class="text-right">{{ $base_total_leads['new'] }}</td>
                                <td class="text-right">{{ $base_total_leads['pending_response'] }}</td>
                                <td class="text-right">{{ $base_total_leads['qualified'] }}</td>
                                <td class="text-right">{{ $base_total_leads['unqualified'] }}</td>
                                <td class="text-right">{{ $base_total_leads['closed'] }}</td>
                                <td></td>
                                <td></td>

                                <td class="text-right">{{ $base_total_leads['total_followups'] }}</td>
                                <td class="text-right">{{ number_format($base_total_leads['avg_followups'], 2) ?? 0 }}
                                </td>
                            </tr>

                            <tr id="ex_tr_0" style="display:none">

                                <td colspan="12">
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
                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                </div>
            </div>
        </div>
        <script>
            $(document).ready(function() {
                $('.collapse').on('show.bs.collapse', function() {
                    $(this).closest('.task-card').find('.toggle-icon')
                        .removeClass('fa-chevron-down')
                        .addClass('fa-chevron-up');
                });

                $('.collapse').on('hide.bs.collapse', function() {
                    $(this).closest('.task-card').find('.toggle-icon')
                        .removeClass('fa-chevron-up')
                        .addClass('fa-chevron-down');
                });
            });
        </script>


    </div>



    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@endsection

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

        .sub-status div:hover {
            background-color: rgba(255, 255, 255, 0.15);
            /* Light white-ish hover effect */
        }
    </style>

    <?php try { ?>
    <div class="container-fluid">
        <div class="d-sm-flex justify-content-between">
            <div class="mb-3">
                <h2 class="page-heading m-0">Leads Report <span class="text-primary">
                        {{ $base_company ? $base_company->company_name : '' }} ({{ $sales_person->full_name }}) </span>

                    @php
                        $statusLabels = [
                            1 => ['label' => 'New', 'color' => 'primary'], // Blue
                            4 => ['label' => 'Pending', 'color' => 'warning'], // Yellow
                            2 => ['label' => 'Qualified', 'color' => 'success'], // Green
                            3 => ['label' => 'Unqualified', 'color' => 'danger'], // Red
                            10 => ['label' => 'Closed', 'color' => 'secondary'], // Gray
                            5 => ['label' => 'Converted', 'color' => 'info'], // Teal
                        ];

                        $statusLabel = $statusLabels[$ctrl_status ?? -1] ?? '';
                    @endphp

                    @if ($statusLabel != '')
                        <span class="text-{{ $statusLabel['color'] }}">({{ $statusLabel['label'] }})</span>
                    @endif
                </h2>
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


        <div class="collapse" id="collapseExample">
            <div class="card shadow mb-4 p-4">

                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-leads-staff-report/' . $sales_person->user_id, 'method' => 'get', 'id' => 'crm-leads-search2']) }}
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <label for="" class="form-check-label">Lead Id</label>
                        <input class="form-control" id="lead_id" type="text" autocomplete="off" name="lead_id"
                            value="{{ $ctrl_lead_id }}">
                    </div>


                    <div class="col-md-3 mb-2">
                        <label for="" class="form-check-label">Company</label>
                        <select class="form-control js-example-basic-single" name="base_company_id" id="base_company_id">
                            <option value="">-Select-</option>
                            @foreach ($company as $value)
                                <option value="{{ @$value->id }}"
                                    @if ($base_company) @if ($base_company->id == $value->id) selected @endif
                                    @endif>
                                    {{ @$value->company_name }}</option>
                            @endforeach
                        </select>
                    </div>


                    <div class="col-md-3 mb-2">
                        <label for="" class="form-check-label">Customer</label>
                        <select class="form-control js-example-basic-single" name="company_id" id="company_id">
                            <option value="">-Select-</option>
                            @foreach ($vendors as $value)
                                <option value="{{ @$value->id }}" @if ($ctrl_cust_id == $value->id) selected @endif>
                                    {{ @$value->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-2">
                        <label for="" class="form-check-label">Region</label>
                        <select class="form-control js-example-basic-single" name="region_id" id="region_id">
                            <option value="" @if ($ctrl_status == '') selected @endif>-Select-</option>
                            @foreach ($country as $value)
                                <option @if ($ctrl_region_id == $value->id) selected @endif value="{{ @$value->id }}">
                                    {{ @$value->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-2">
                        <label for="" class="form-check-label">Brand</label>
                        <select class="form-control js-example-basic-single" name="brand_id" id="brand_id">
                            <option value="">-Select-</option>
                            @foreach ($brand as $value)
                                <option value="{{ @$value->title }}" @if ($ctrl_brand == $value->title) selected @endif>
                                    {{ @$value->title }}</option>
                            @endforeach
                        </select>
                    </div>


                    <div class="col-md-3 mb-2">
                        <label for="" class="form-check-label">Status</label>
                        <select class="form-control" name="status_id" id="status_id">
                            <option value="" @if ($ctrl_status == '') selected @endif>-Select-</option>
                            <option value="1" @if ($ctrl_status == 1) selected @endif>New</option>
                            <option value="2" @if ($ctrl_status == 2) selected @endif>Qualified</option>
                            <option value="3" @if ($ctrl_status == 3) selected @endif>Unqualified</option>
                            <option value="4" @if ($ctrl_status == 4) selected @endif>Pending Response
                            </option>
                            <option value="10" @if ($ctrl_status == 10) selected @endif>Closed
                            </option>
                            <option value="5" @if ($ctrl_status == 5) selected @endif>Converted</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label for="" class="form-check-label">Sub-Status</label>
                        <select class="form-control" name="sub_status" id="sub_status">
                            <option value="">-Select-</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-2">
                        <label for="" class="form-check-label">Source</label>
                        <select class="form-control" name="source_id" id="source_id">
                            <option value="">-Select-</option>
                            <option value="Gitex 2023" @if ($ctrl_source == 'Gitex 2023') selected @endif>Gitex 2023
                            </option>
                            <option value="Gitex" @if ($ctrl_source == 'Gitex') selected @endif>Gitex</option>
                            <option value="Chat" @if ($ctrl_source == 'Chat') selected @endif>Chat</option>
                            <option value="Call" @if ($ctrl_source == 'Call') selected @endif>Call</option>
                            <option value="Mail" @if ($ctrl_source == 'Mail') selected @endif>Mail</option>
                            <option value="Ecommerce" @if ($ctrl_source == 'Ecommerce') selected @endif>Ecommerce
                            </option>
                            <option value="Other" @if ($ctrl_source == 'Other') selected @endif>Other</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-2">
                        <label for="" class="form-check-label">Type</label>
                        <select class="form-control" name="isproject_id" id="isproject_id">
                            <option value="">-Select-</option>
                            <option value="1" @if (@$ctrl_isproject == '1') selected @endif>Project</option>
                            <option value="2" @if (@$ctrl_isproject == '2') selected @endif>Channel</option>
                            <option value="3" @if (@$ctrl_isproject == '3') selected @endif>Corporate</option>
                            <option value="0" @if (@$ctrl_isproject == '0') selected @endif>Lead</option>
                        </select>
                    </div>




                    <div class="col-md-3 mb-2">
                        <label for="" class="form-check-label">Form Date</label>
                        <input class="form-control datepicker" id="date" type="date" autocomplete="off"
                            name="date" value="{{ $ctrl_date }}">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label for="" class="form-check-label">To Date</label>
                        <input class="form-control" id="date2" type="date" autocomplete="off" name="date2"
                            value="{{ $ctrl_date2 }}">
                    </div>











                    <div class="col-md-3 mb-2">
                        <label for="" class="form-check-label">Follow Up Date</label>
                        <input class="form-control" id="followupdt_filter" type="date" autocomplete="off"
                            name="followupdt_filter" value="{{ $ctrl_followupdt_filter }}">
                    </div>

                    <div class="col-md-3 mb-2">
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

                    <div class="col-12 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary" id="btnSubmit">Filter</button>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>


        @if (!empty($lead_stats['statusCounts']))

            <div class="row task-row">
                <div class="cls-m-12 col-md-6 col-lg-3 col-xl-1-8">
                    <div class="task-card bg-primary text-white">
                        <div class="filter-by-status" data-status-id="1" style="cursor:pointer;">
                            <i class="fa 	fa-plus-circle task-icon"></i>
                            <h5 class="task-title ">New</h5>
                            <div id="totalTasks" class="task-count">{{ $lead_stats['statusCounts'][1] ?? 0 }}
                                ({{ $lead_stats['total_leads'] > 0 ? round((($lead_stats['statusCounts'][1] ?? 0) / $lead_stats['total_leads']) * 100, 2) : 0 }}%)
                            </div>
                        </div>
                        <div class="sub-status collapse" id="newCollapse">
                            <div class="sub-status-div" data-sub-status-id="1"><span class="sub-status-title">Just
                                    received,
                                    uncontacted</span><span class="sub-status-value">
                                    {{ $lead_stats['sub_statusCounts'][1] ?? 0 }}</span></div>
                        </div>

                        <div class="task-toggle-indicator" data-toggle="collapse" href="#newCollapse" role="button"
                            aria-expanded="false" aria-controls="newCollapse">
                            <i class="fa fa-chevron-down toggle-icon" id="icon-new"></i>
                        </div>
                    </div>
                </div>

                <div class="cls-m-12 col-md-6 col-lg-3 col-xl-1-8 " style="cursor:pointer;">
                    <div class="task-card bg-warning text-dark">
                        <div class="filter-by-status" data-status-id="4">
                            <i class="fa 	fa-hourglass-half task-icon"></i>
                            <h5 class="task-title filter-by-status">Pending</h5>
                            <div id="dueToday" class="task-count">{{ $lead_stats['statusCounts'][4] ?? 0 }}
                                ({{ $lead_stats['total_leads'] > 0 ? round((($lead_stats['statusCounts'][4] ?? 0) / $lead_stats['total_leads']) * 100, 2) : 0 }}%)
                            </div>
                        </div>
                        <div class="sub-status collapse" id="pendingCollapse">
                            <div class="sub-status-div" data-sub-status-id="9"><span class="sub-status-title">Waiting for
                                    EUD</span><span
                                    class="sub-status-value">{{ $lead_stats['sub_statusCounts'][9] ?? 0 }}</span>
                            </div>

                            <div class="sub-status-div" data-sub-status-id="10"><span class="sub-status-title">Waiting
                                    for Vendor
                                    Price</span><span
                                    class="sub-status-value">{{ $lead_stats['sub_statusCounts'][10] ?? 0 }}</span></div>
                            <div class="sub-status-div" data-sub-status-id="11"><span class="sub-status-title">Quoted -
                                    Waiting for
                                    Response</span><span
                                    class="sub-status-value">{{ $lead_stats['sub_statusCounts'][11] ?? 0 }}</span></div>
                            <div class="sub-status-div" data-sub-status-id="12"><span class="sub-status-title">Other
                                    Reasons</span><span
                                    class="sub-status-value">{{ $lead_stats['sub_statusCounts'][12] ?? 0 }}</span>
                            </div>
                        </div>

                        <div class="task-toggle-indicator" data-toggle="collapse" href="#pendingCollapse" role="button"
                            aria-expanded="false" aria-controls="pendingCollapse">
                            <i class="fa fa-chevron-down toggle-icon" id="icon-new"></i>
                        </div>
                    </div>


                </div>

                <div class="cls-m-12 col-md-6 col-lg-3 col-xl-1-8 " style="cursor:pointer;">

                    <div class="task-card bg-success text-white">
                        <div class="filter-by-status" data-status-id="2">
                            <i class="fa fa-thumbs-up task-icon"></i>
                            <h5 class="task-title ">Qualified</h5>
                            <div id="dueTasks" class="task-count">
                                {{ ($lead_stats['statusCounts'][2] ?? 0) + ($lead_stats['statusCounts'][0] ?? 0) }}
                                ({{ $lead_stats['total_leads'] > 0 ? round(((($lead_stats['statusCounts'][2] ?? 0) + ($lead_stats['statusCounts'][0] ?? 0)) / $lead_stats['total_leads']) * 100, 2) : 0 }}%)
                            </div>
                        </div>
                        <div class="sub-status collapse" id="qualifiedCollapse">
                            <div class="sub-status-div" data-sub-status-id="2"><span class="sub-status-title">Sent to
                                    Sales</span><span
                                    class="sub-status-value">{{ $lead_stats['sub_statusCounts'][2] ?? 0 }}</span>
                            </div>
                            <div class="sub-status-div" data-sub-status-id="d1"><span
                                    class="sub-status-title">Prospecting</span><span
                                    class="sub-status-value">{{ $lead_stats['deals_statusCounts'][1] ?? 0 }}</span>
                            </div>
                            <div class="sub-status-div" data-sub-status-id="d2"><span
                                    class="sub-status-title">Quote</span><span
                                    class="sub-status-value">{{ $lead_stats['deals_statusCounts'][2] ?? 0 }}</span></div>
                            <div class="sub-status-div" data-sub-status-id="d3"><span
                                    class="sub-status-title">Closure</span><span
                                    class="sub-status-value">{{ $lead_stats['deals_statusCounts'][3] ?? 0 }}</span></div>
                            <div class="sub-status-div" data-sub-status-id="d4"><span
                                    class="sub-status-title">Won</span><span
                                    class="sub-status-value">{{ $lead_stats['deals_statusCounts'][4] ?? 0 }}</span></div>
                            <div class="sub-status-div" data-sub-status-id="d5"><span
                                    class="sub-status-title">Lost</span><span
                                    class="sub-status-value">{{ $lead_stats['deals_statusCounts'][5] ?? 0 }}</span></div>
                        </div>

                        <div class="task-toggle-indicator" data-toggle="collapse" href="#qualifiedCollapse"
                            role="button" aria-expanded="false" aria-controls="qualifiedCollapse">
                            <i class="fa fa-chevron-down toggle-icon" id="icon-new"></i>
                        </div>
                    </div>
                </div>

                <div class="cls-m-12 col-md-6 col-lg-3 col-xl-1-8 " style="cursor:pointer;">

                    <div class="task-card bg-danger text-white">
                        <div class="filter-by-status" data-status-id="3">
                            <i class="fa fa-times-circle task-icon"></i>
                            <h5 class="task-title ">Unqualified</h5>
                            <div id="notStartedCount" class="task-count">{{ $lead_stats['statusCounts'][3] ?? 0 }}
                                ({{ $lead_stats['total_leads'] > 0 ? round((($lead_stats['statusCounts'][3] ?? 0) / $lead_stats['total_leads']) * 100, 2) : 0 }}%)
                            </div>
                        </div>
                        <div class="sub-status collapse" id="unqualifiedCollapse">
                            <div class="sub-status-div" data-sub-status-id="3"><span class="sub-status-title">Budget
                                    Issue</span><span
                                    class="sub-status-value">{{ $lead_stats['sub_statusCounts'][3] ?? 0 }}</span>
                            </div>
                            <div class="sub-status-div" data-sub-status-id="4"><span class="sub-status-title">Not
                                    Interested</span><span
                                    class="sub-status-value">{{ $lead_stats['sub_statusCounts'][4] ?? 0 }}</span>
                            </div>
                            <div class="sub-status-div" data-sub-status-id="5"><span class="sub-status-title">Wrong
                                    Contact</span><span
                                    class="sub-status-value">{{ $lead_stats['sub_statusCounts'][5] ?? 0 }}</span>
                            </div>
                            <div class="sub-status-div" data-sub-status-id="6"><span class="sub-status-title">Timeline
                                    not matching</span><span
                                    class="sub-status-value">{{ $lead_stats['sub_statusCounts'][6] ?? 0 }}</span></div>
                            <div class="sub-status-div" data-sub-status-id="7"><span
                                    class="sub-status-title">Product/Service
                                    mismatch</span><span
                                    class="sub-status-value">{{ $lead_stats['sub_statusCounts'][7] ?? 0 }}</span></div>
                            <div class="sub-status-div" data-sub-status-id="8"><span class="sub-status-title">Other
                                    Reason</span><span
                                    class="sub-status-value">{{ $lead_stats['sub_statusCounts'][8] ?? 0 }}</span>
                            </div>
                        </div>

                        <div class="task-toggle-indicator" data-toggle="collapse" href="#unqualifiedCollapse"
                            role="button" aria-expanded="false" aria-controls="unqualifiedCollapse">
                            <i class="fa fa-chevron-down toggle-icon" id="icon-new"></i>
                        </div>
                    </div>
                </div>

                <div class="cls-m-12 col-md-6 col-lg-3 col-xl-1-8 " style="cursor:pointer;">

                    <div class="task-card bg-secondary text-white">
                        <div class="filter-by-status" data-status-id="10">
                            <i class="fa 	fa-archive  task-icon"></i>
                            <h5 class="task-title ">Closed</h5>
                            <div id="inProgressCount" class="task-count">{{ $lead_stats['statusCounts'][10] ?? 0 }}
                                ({{ $lead_stats['total_leads'] > 0 ? round((($lead_stats['statusCounts'][10] ?? 0) / $lead_stats['total_leads']) * 100, 2) : 0 }}%)
                            </div>
                        </div>
                        <div class="sub-status collapse" id="closedCollapse">
                            <div class="sub-status-div" data-sub-status-id="13"><span class="sub-status-title">No
                                    Response</span><span
                                    class="sub-status-value">{{ $lead_stats['sub_statusCounts'][13] ?? 0 }}</span>
                            </div>
                            <div class="sub-status-div" data-sub-status-id="14"><span class="sub-status-title">Other
                                    Reason</span><span class="sub-status-value">
                                    {{ $lead_stats['sub_statusCounts'][14] ?? 0 }}</span>
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
                        <div id="completedCount" class="task-count">{{ $lead_stats['total_leads'] ?? 0 }} (100%)</div>
                    </div>
                </div>
                <div class="cls-m-12 col-md-6 col-lg-3 col-xl-1-8">

                    <div class="task-card bg-secondary text-white">
                        <div><i class="fa fa-stopwatch task-icon"></i></div>
                        <h5 class="task-title">Av. Aging (Days)</h5>
                        <div id="completedCount" class="task-count">{{ $lead_stats['avg_aging_days'] ?? 0 }} Days</div>
                    </div>
                </div>
                <div class="cls-m-12 col-md-6 col-lg-3 col-xl-1-8">

                    <div class="task-card bg-success text-white">
                        <div><i class="fa  fa-percentage task-icon"></i></div>
                        <h5 class="task-title">Conv. Rate (%)</h5>
                        <div id="completedCount" class="task-count">
                            {{ $lead_stats['total_leads'] > 0 ? round(((($lead_stats['statusCounts'][2] ?? 0) + ($lead_stats['statusCounts'][0] ?? 0)) / $lead_stats['total_leads']) * 100, 2) : 0 }}%
                        </div>
                    </div>
                </div>
            </div>


        @endif


        <script>
            $(document).ready(function() {
                $('.filter-by-status').on('click', function() {
                    var statusId = $(this).data('status-id');
                    $('#status_id').val(statusId).trigger('change');
                    var form = $('#crm-leads-search2');
                    var params = form.serialize();
                    var sortParam = '';

                    const urlParams = new URLSearchParams(window.location.search);
                    if (urlParams.has('sort_id')) {
                        console.log("sort_id is present");
                    } else {
                        sortParam = 'sort_id=7';
                    }
                    var url = form.attr('action') + '?' + params + '&' + sortParam;
                    window.open(url, '_blank');
                });

                $('.sub-status-div').on('click', function() {
                    var substatusId = $(this).data('sub-status-id');
                    console.log(substatusId)
                    $('#sub_status').val(substatusId).trigger('change');
                    var form = $('#crm-leads-search2');
                    var params = form.serialize();
                    var sortParam = '';

                    const urlParams = new URLSearchParams(window.location.search);
                    if (urlParams.has('sort_id')) {
                        console.log("sort_id is present");
                    } else {
                        sortParam = 'sort_id=7';
                    }
                    var url = form.attr('action') + '?' + params + '&' + sortParam;
                    window.open(url, '_blank');
                });


            });
        </script>



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
                                <th style="width:100px">@lang('Lead No')</th>
                                <th style="width:100px">@lang('Deal ID')</th>
                                <th style="width:100px">@lang('Company')</th>
                                <th style="width:150px">@lang('Lead Name')</th>
                                <th style="width:150px">@lang('Customer')</th>
                                <th style="width:100px">@lang('Region')</th>
                                <th style="width: 100px">@lang('Brand')</th>

                                <th style="width:150px">@lang('Stage')</th>
                                <th style="width:150px">@lang('Sub Stage')</th>
                                <th style="width:80px">@lang('Source')</th>
                                <th style="width:100px">@lang('Date')</th>
                                <th style="width:100px">@lang('Updated On')</th>
                                <th style="width:100px">@lang('Aging Days')</th>
                                <th style="width:100px">@lang('No. Followups')</th>
                                <th style="width:200px">@lang('Actions')</th>

                            </tr>
                        </thead>

                        <tbody>
                            <style>
                                /* Fixed width and ellipsis for each td */
                                tr.ellipsis-row td {
                                    white-space: nowrap;
                                    overflow: hidden;
                                    text-overflow: ellipsis;
                                    max-width: 200px;
                                }

                                /* On hover, show full content */
                                tr.ellipsis-row.hovered td {
                                    white-space: normal !important;
                                    overflow: visible !important;
                                    text-overflow: unset !important;
                                    max-width: none;
                                    position: relative;
                                    z-index: 1;
                                }
                            </style>
                            <script>
                                $(document).ready(function() {
                                    // When mouse enters any ellipsis-row
                                    $(document).on('mouseenter', 'tr.ellipsis-row', function() {
                                        $(this).addClass('hovered');
                                    });

                                    // When mouse leaves the row
                                    $(document).on('mouseleave', 'tr.ellipsis-row', function() {
                                        $(this).removeClass('hovered');
                                    });
                                });
                            </script>


                            @foreach ($leads as $value)
                                <tr @if ($value->status == 4) @if ($value->follow_up_date)
                                        @if (\Carbon\Carbon::parse($value->follow_up_date)->isToday()) style="background-color:#fff9db !important; color:#b38600;"
                                        @elseif(
                                            \Carbon\Carbon::parse($value->follow_up_date)->isPast() &&
                                                !\Carbon\Carbon::parse($value->follow_up_date)->isToday()) style="background-color:#ffebeb !important; color:#ff0000;" @endif
                                    @endif
                            @endif

                            class="ellipsis-row 
                                    
                                   


                                    {{ $value->deleted_at ? 'bg-dark' : '' }} 
                                        


                                        ">
                            <td><a target="_blank"
                                    href="{{ url('crm-leads/' . $value->id . '/view') }}">{{ @$value->lead_code->code }}</a>
                            </td>

                            <td>



                                @php
                                    try {
                                        $code = $value->lead_deal_code->code ?? null;
                                    } catch (\Exception $e) {
                                        $code = null;
                                    }
                                @endphp

                                @if ($code)
                                    <a target="_blank"
                                        href="{{ url('crm-deals/' . $value->lead_deal_code->id . '/view') }}">{{ $code }}</a>
                                @else
                                    --
                                @endif

                            </td>

                            @if (session('logged_session_data.company_id') == 1)
                                <td>{{ $value->company->company_name ?? '' }}</td>
                            @endif


                            <td class="ellipsis-cell"><a class="text-dark" target="_blank"
                                    href="{{ url('crm-leads/' . $value->id . '/view') }}">
                                    <div>
                                        {{ @$value->lead_name }}</div>
                                </a>
                            </td>

                            <td class="ellipsis-cell">

                                {{ @$value->customername->name }}
                            </td>

                            <td>
                                {{ @$value->customername->vatcountry->name }}
                            </td>



                            <td>
                                <div>
                                    {{ @$value->tags }}</div>
                            </td>

                            <td>
                                @if ($value->status == 1)
                                    <span class="text-info">New</span>
                                @endif
                                @if ($value->status == 2)
                                    <span class="text-primary">Qualified</span>
                                @endif
                                @if ($value->status == 3)
                                    <span class="text-danger">Unqualified</span>
                                @endif
                                @if ($value->status == 4)
                                    <span class="text-warning">Pending Response</span> <br>
                                    @if ($value->follow_up_date)
                                        <span class="text-dark">Follow Up Date:
                                            {{ $value->follow_up_date ? date('d/m/Y', strtotime(@$value->follow_up_date)) : '' }}
                                        </span>
                                    @endif
                                @endif
                                @if ($value->status == 10)
                                    <span class="text-danger">Closed</span>
                                @endif
                                @if ($value->status == 0)
                                    <span class="text-success">Converted</span>
                                    <?php $d = $deal_det->where('id', $value->deal_id)->first(); ?>
                                    @if ($d && $d->stage == 1)
                                        <span class="warning btn-badge py-1 px-2">Prospecting</span>
                                    @endif
                                    @if ($d && $d->stage == 2)
                                        <span class="success btn-badge py-1 px-2">Quote</span>
                                    @endif
                                    @if ($d && $d->stage == 3)
                                        <span class="info btn-badge py-1 px-2">Closure</span>
                                    @endif
                                    @if ($d && $d->stage == 4)
                                        <?php
                                        $data = App\SysHelper::deal_track_status($d->id);
                                        $color = 'danger';
                                        if ($data == 'Pending') {
                                            $color = 'warning';
                                        } elseif ($data == 'completed') {
                                            $color = 'primary';
                                        } elseif ($data == 'OnProcess') {
                                            $color = 'info';
                                        } else {
                                            $color = 'danger';
                                        }
                                        ?>
                                        @if ($data != 'completed')
                                            <span class="primary btn-badge py-1 px-2">Won</span>
                                            <span class="primary btn-badge py-1 px-2">{{ $data }}</span>
                                        @else
                                            <span class="primary btn-badge py-1 px-2">{{ $data }}</span>
                                        @endif
                                    @endif
                                    @if ($d && $d->stage == 5)
                                        <span class="danger btn-badge py-1 px-2">Lost</span>
                                    @endif
                                    @if ($d && $d->stage == 6)
                                        <span class="dark btn-badge py-1 px-2">Cancelled</span>
                                    @endif
                                @endif
                            </td>

                            @php
                                $subStatusMap = [
                                    1 => 'Just received, uncontacted',
                                    2 => 'Sent to Sales',
                                    3 => 'Budget Issue',
                                    4 => 'Not Interested',
                                    5 => 'Wrong Contact',
                                    6 => 'Timeline not matching',
                                    7 => 'Product/Service mismatch',
                                    8 => 'Other',
                                    9 => 'Waiting for EUD',
                                    10 => 'Waiting for Vendor Price',
                                    11 => 'Quoted - Waiting for Response',
                                    12 => 'Other',
                                    13 => 'No Response',
                                    14 => 'Other',
                                ];
                            @endphp

                            <td class="ellipsis-cell">
                                @if ($value->sub_status == 8 || $value->sub_status == 12 || $value->sub_status == 14)
                                    {{ $value->sub_status_comment }}
                                @else
                                    {{ $subStatusMap[$value->sub_status] ?? '' }}
                                @endif



                            </td>

                            <td>
                                {{ $value->source }}
                            </td>
                            <td>{{ date('d/m/Y', strtotime(@$value->created_at)) }} <br>
                                {{ date('h:i A', strtotime(@$value->created_at)) }}
                            </td>
                            <td>{{ date('d/m/Y', strtotime(@$value->updated_at)) }} <br>
                                {{ date('h:i A', strtotime(@$value->updated_at)) }}
                            </td>
                            <td>{{ $value->getAgingDays() > 0 ? $value->getAgingDays() : '' }}</td>
                            <td>{{ $value->followup_count ?? 0 }}</td>

                            <td class="">

                                <a class="btn-sm btn-primary open-comments-modal" style="cursor: pointer;"
                                    data-lead-id="{{ $value->id }}"><i class="fa fa-comments"
                                        aria-hidden="true"></i></a>

                                <a class="btn-sm btn-info" href="{{ url('crm-leads/' . $value->id . '/view') }}"><i
                                        class="fa fa-eye" aria-hidden="true"></i></a>

                                <a class="btn-sm btn-primary" href="{{ url('crm-leads/' . $value->id . '/edit') }}"><i
                                        class="fa fa-edit" aria-hidden="true"></i></a>


                                @if (Auth::user()->role_id == 1)
                                    @if ($value->deleted_at)
                                        <button data-id="{{ $value->id }}" data-toggle="modal"
                                            data-target="#restoreModal" type="button"
                                            class="btn-sm btn-success open-restore-modal" title="Restore">
                                            <i class="fa fa-undo"></i>
                                        </button>
                                    @else
                                        <button type="button" class="btn-sm btn-danger open-delete-modal"
                                            data-id="{{ $value->id }}" data-toggle="modal"
                                            data-target="#deleteModal">
                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                        </button>
                                    @endif
                                @endif
                            </td>
                            </tr>
                            @endforeach
                        </tbody>

                        <footer>
                            <tr>
                                <td colspan="11">
                                    {{ $leads->appends(request()->input())->links() }}
                                </td>
                            </tr>
                        </footer>
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



        <div class="modal fade" id="commentsModal" tabindex="-1" role="dialog" aria-labelledby="commentsModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content" style="max-height: 90vh;">
                    <div class="modal-header">
                        <h5 class="modal-title">Lead Comments</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body d-flex flex-column" style="height: 80vh;">

                        <!-- Scrollable Comments Table -->
                        <div id="commentsScrollContainer"
                            style="flex: 1 1 auto; overflow-y: auto; border: 1px solid #dee2e6; border-radius: .25rem;">
                            <table class="table table-bordered table-striped mb-0" id="commentsTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="10%">Type</th>
                                        <th width="40%">Comment</th>
                                        <th width="20%">Person</th>
                                        <th width="10%">Attachment</th>
                                        <th width="20%">Date</th>
                                    </tr>
                                </thead>
                                <tbody id="commentsModalBody">
                                    <tr>
                                        <td colspan="5" class="text-center text-muted no-comments-found">No comments
                                            found
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Fixed Comment Form -->
                        <div class="pt-3 mt-3 border-top" style="flex-shrink: 0;">
                            <input type="hidden" name="current_lead_id" id="current_lead_id">
                            <label for="newComment" class="form-label font-weight-bold">Add Internal Note</label>
                            <textarea id="newComment" class="form-control mb-2" cols="10" rows="3" placeholder="Internal Note..."></textarea>
                            <input type="file" class="form-control mb-2" name="commentsdoc" id="commentsdoc">
                            <button id="submitComment" class="btn btn-primary float-right">Add Note</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Delete Reason Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form method="POST" action="" id="deleteForm">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title text-white" id="deleteModalLabel">Delete Lead</h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>Please provide a reason for deleting this lead:</p>
                            <textarea name="delete_reason" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Confirm Delete</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="restoreModal" tabindex="-1" role="dialog" aria-labelledby="restoreModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form method="POST" action="" id="restoreForm">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title text-white" id="restoreModalLabel">Restore Lead</h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>Please provide a reason for restoring this lead:</p>
                            <textarea name="restore_reason" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Confirm Restore</button>
                        </div>
                    </div>
                </form>
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

        <script>
            $(document).ready(function() {
                $('.open-comments-modal').click(function() {
                    $("#loading_bg").css("display", "block");


                    var leadId = $(this).data('lead-id');
                    var $body = $('#commentsModalBody');
                    $body.html('<tr><td colspan="5" class="text-center text-muted">Loading...</td></tr>');
                    $('#current_lead_id').val(leadId);
                    $.ajax({
                        url: '/crm-leads/comments/' + leadId,
                        method: 'GET',
                        dataType: 'json',
                        success: function(res) {
                            $body.empty();
                            if (res.data && res.data.length > 0) {
                                $.each(res.data, function(i, comment) {

                                    var sourceBadge = '';
                                    if (comment.source === 'lead') {
                                        sourceBadge =
                                            `<span class="badge badge-info rounded-0 font-weight-normal">Lead</span>`;
                                    } else if (comment.source === 'deal') {
                                        sourceBadge =
                                            `<span class="badge badge-success rounded-0 font-weight-normal">Deal</span>`;
                                    }


                                    var row = `
                                    <tr>
                                        <td>${sourceBadge}</td>
                                        <td>${comment.comments}</td>
                                         
                                        <td>${comment.createdby.first_name || '-'} ${comment.createdby.last_name || '-'}</td>
                                       <td>
                                        ${comment.source === 'lead' ? `
                                                                                                                                                                                                   ${comment.commentsdoc ? ` <a class="text-info p-0"
                                                                href="{{ asset('public/uploads/crm_lead_doc/') }}/${ comment.commentsdoc }"
                                                                target="_blank"><i class="fa fa-paperclip"
                                                                    aria-hidden="true"></i>&nbsp;&nbsp;View File</a>` : '' }
                                                                                                                                                                                                ` : `
                                                                                                                                                                                                   ${comment.commentsdoc ? ` <a class="text-info p-0"
                                                                href="{{ asset('public/uploads/crm_deal_doc/') }}/${ comment.commentsdoc }"
                                                                target="_blank"><i class="fa fa-paperclip"
                                                                    aria-hidden="true"></i>&nbsp;&nbsp;View File</a>` : '' }
                                                                                                                                                                                                `}</td>
                                        <td>${formatDateTime(comment.created_at)}</td>
                                    </tr>`;
                                    $body.append(row);


                                });
                            } else {
                                $body.html(
                                    '<tr><td colspan="5" class="text-center text-muted no-comments-found">No comments found</td></tr>'
                                );
                            }
                            $("#loading_bg").css("display", "none");


                            $('#commentsModal').modal('show');



                        },
                        error: function() {
                            $body.html(
                                '<tr><td colspan="3" class="text-danger text-center">Error loading comments</td></tr>'
                            );
                        }
                    });



                });

            });

            function formatDateTime(datetime) {
                var date = new Date(datetime);
                return date.toLocaleString('en-IN', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                });
            }

            $(document).on('click', '.open-delete-modal', function() {
                var leadId = $(this).data('id');
                var actionUrl = "{{ url('crm-leads') }}/" + leadId + "/delete";
                $('#deleteForm').attr('action', actionUrl);
            });

            $(document).on('click', '.open-restore-modal', function() {
                var leadId = $(this).data('id');
                var actionUrl = "{{ url('crm-leads') }}/" + leadId + "/restore";
                $('#restoreForm').attr('action', actionUrl);
            });


            $('#submitComment').on('click', function() {
                $("#loading_bg").css("display", "block");

                var commentText = $('#newComment').val().trim();
                var leadId = $('#current_lead_id').val();
                var fileInput = $('#commentsdoc')[0].files[0];


                if (!commentText) return alert("Comment cannot be empty.");

                var formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('comment', commentText);
                formData.append('current_lead_id', leadId);
                if (fileInput) {
                    formData.append('commentsdoc', fileInput);
                }

                $.ajax({
                    url: '/crm-leads-comments-store',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(newComment) {
                        console.log(newComment)
                        // Clear both textarea and file input
                        $('#newComment').val('');
                        $('#commentsdoc').val(''); // This clears file input


                        $('#commentsModalBody').append(
                            ` <tr>
                                        <td>
                                            <span class="badge badge-info rounded-0 font-weight-normal">Lead</span>
                                        </td>
                                        <td>${newComment.comments}</td>
                                        <td>${newComment.createdby.first_name || '-'} ${newComment.createdby.last_name || '-'}</td>
                                        <td>
                                        ${newComment.commentsdoc ? ` <a class="text-info p-0"
                                                                                                                                                                                                                        href="{{ asset('public/uploads/crm_lead_doc/') }}/${ newComment.commentsdoc }"
                                                                                                                                                                                                                        target="_blank"><i class="fa fa-paperclip"
                                                                                                                                                                                                                            aria-hidden="true"></i>&nbsp;&nbsp;View File</a>` : '' }
                                        </td>
                                        <td>${formatDateTime(newComment.created_at)}</td>
                       </tr>`
                        );


                        // Clear the "No comments found" message if it exists
                        $('.no-comments-found').remove();


                        // $('#commentsModalBody').scrollTop($('#commentsModalBody')[0].scrollHeight);
                        $('#commentsScrollContainer').scrollTop($('#commentsScrollContainer')[0]
                            .scrollHeight);

                        $("#loading_bg").css("display", "none");


                    },
                    error: function(xhr, status, error) {
                        $("#loading_bg").css("display", "none");
                        console.error("AJAX Error:", xhr);
                        alert("Error: " + xhr.responseJSON?.message || error);
                    }
                });
            });

            const subStatusOptions = {
                1: [{
                    value: '1',
                    text: 'Just received, uncontacted'
                }],
                2: [{
                    value: '2',
                    text: 'Sent to Sales'
                }],
                3: [{
                        value: '3',
                        text: 'Budget Issue'
                    },
                    {
                        value: '4',
                        text: 'Not Interested'
                    },
                    {
                        value: '5',
                        text: 'Wrong Contact'
                    },
                    {
                        value: '6',
                        text: 'Timeline not matching'
                    },
                    {
                        value: '7',
                        text: 'Product/Service mismatch'
                    },
                    {
                        value: '8',
                        text: 'Other (Unqalified)'
                    },
                ],
                4: [{
                        value: '9',
                        text: 'Waiting for EUD'
                    },
                    {
                        value: '10',
                        text: 'Waiting for Vendor Price'
                    },
                    {
                        value: '11',
                        text: 'Quoted - Waiting for Response'
                    },
                    {
                        value: '12',
                        text: 'Other (Pending Response)'
                    },
                ],
                10: [{
                        value: '13',
                        text: 'No Response'
                    },
                    {
                        value: '14',
                        text: 'Other (Closed)'
                    }
                ],
                5: [{
                        value: 'd1',
                        text: 'Prospecting'
                    },
                    {
                        value: 'd2',
                        text: 'Quote'
                    },
                    {
                        value: 'd3',
                        text: 'Closure'
                    },
                    {
                        value: 'd4',
                        text: 'Won'
                    },
                    {
                        value: 'd5',
                        text: 'Quote'
                    }
                ]
            };

            function populateSubStatus(statusId, selectedValue = '') {
                const $subStatus = $('#sub_status');
                $subStatus.empty().append('<option value="">-Select-</option>');

                if (statusId && subStatusOptions[statusId]) {
                    subStatusOptions[statusId].forEach(opt => {
                        const selected = (opt.value === selectedValue) ? 'selected' : '';
                        $subStatus.append(`<option value="${opt.value}" ${selected}>${opt.text}</option>`);
                    });
                } else {
                    // If no status selected, show all options
                    Object.values(subStatusOptions).flat().forEach(opt => {
                        const selected = (opt.value === selectedValue) ? 'selected' : '';
                        $subStatus.append(`<option value="${opt.value}" ${selected}>${opt.text}</option>`);
                    });
                }
            }

            $(document).ready(function() {
                const initialStatus = $('#status_id').val();
                const selectedSubStatus = @json($ctrl_sub_status ?? '');
                populateSubStatus(initialStatus, selectedSubStatus);

                $('#status_id').on('change', function() {
                    populateSubStatus(this.value);
                });
            });
        </script>

    </div>



    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@endsection

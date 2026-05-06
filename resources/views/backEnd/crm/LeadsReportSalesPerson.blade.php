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

                .sub-status div:hover {
                    background-color: rgba(255, 255, 255, 0.15);
                    /* Light white-ish hover effect */
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
                <h4 class="mb-0">Leads Report - {{ $base_company ? $base_company->company_name : '' }}
                    - {{ $sales_person->full_name }}

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
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-leads-staff-report/' . $sales_person->user_id, 'method' => 'get', 'id' => 'crm-leads-search2']) }}


                        <div class="row">

                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">Lead ID</label>
                                <input class="form-control" type="text" autocomplete="off" name="lead_id"
                                    value="{{ $ctrl_lead_id }}">
                            </div>

                            <div class="col-1-5">
                                <label for="" class="form-label">Company</label>
                                <select class="form-control js-example-basic-single" name="base_company_id"
                                    id="base_company_id">
                                    <option value="">-Select-</option>
                                    @foreach ($company as $value)
                                        <option value="{{ @$value->id }}"
                                            @if ($base_company) @if ($base_company->id == $value->id) selected @endif
                                            @endif>
                                            {{ @$value->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-3 mb-2 filter-field d-none">
                                <label for="" class="form-label">Customer</label>
                                <select class="form-control js-example-basic-single" name="company_id" id="company_id">
                                    <option value="">-Select-</option>
                                    @foreach ($vendors as $value)
                                        <option value="{{ @$value->id }}"
                                            @if ($ctrl_cust_id == $value->id) selected @endif>
                                            {{ @$value->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">Region</label>
                                <select class="form-control js-example-basic-single" name="region_id" id="region_id">
                                    <option value="" @if ($ctrl_status == '') selected @endif>-Select-
                                    </option>
                                    @foreach ($country as $value)
                                        <option @if ($ctrl_region_id == $value->id) selected @endif
                                            value="{{ @$value->id }}">
                                            {{ @$value->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">Brand</label>
                                <select class="form-control js-example-basic-single" name="brand_id" id="brand_id">
                                    <option value="">-Select-</option>
                                    @foreach ($brand as $value)
                                        <option value="{{ @$value->title }}"
                                            @if ($ctrl_brand == $value->title) selected @endif>
                                            {{ @$value->title }}</option>
                                    @endforeach
                                </select>
                            </div>




                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">Status</label>
                                <select class="form-control" name="status_id" id="status_id">
                                    <option value="" @if ($ctrl_status == '') selected @endif>-Select-
                                    </option>
                                    <option value="1" @if ($ctrl_status == 1) selected @endif>New</option>
                                    <option value="2" @if ($ctrl_status == 2) selected @endif>Qualified
                                    </option>
                                    <option value="3" @if ($ctrl_status == 3) selected @endif>Unqualified
                                    </option>
                                    <option value="4" @if ($ctrl_status == 4) selected @endif>Pending
                                        Response
                                    </option>
                                    <option value="10" @if ($ctrl_status == 10) selected @endif>Closed
                                    </option>
                                    <option value="5" @if ($ctrl_status == 5) selected @endif>Converted
                                    </option>
                                </select>
                            </div>

                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">Sub-Status</label>
                                <select class="form-control" name="sub_status" id="sub_status">
                                    <option value="">-Select-</option>
                                </select>
                            </div>

                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">Source</label>
                                <select class="form-control" name="source_id" id="source_id">
                                    <option value="">-Select-</option>
                                    <option value="Gitex 2023" @if ($ctrl_source == 'Gitex 2023') selected @endif>Gitex
                                        2023
                                    </option>
                                    <option value="Gitex" @if ($ctrl_source == 'Gitex') selected @endif>Gitex
                                    </option>
                                    <option value="Chat" @if ($ctrl_source == 'Chat') selected @endif>Chat
                                    </option>
                                    <option value="Call" @if ($ctrl_source == 'Call') selected @endif>Call
                                    </option>
                                    <option value="Mail" @if ($ctrl_source == 'Mail') selected @endif>Mail
                                    </option>
                                    <option value="Ecommerce" @if ($ctrl_source == 'Ecommerce') selected @endif>Ecommerce
                                    </option>
                                    <option value="Other" @if ($ctrl_source == 'Other') selected @endif>Other
                                    </option>
                                </select>
                            </div>

                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">Type</label>
                                <select class="form-control" name="isproject_id" id="isproject_id">
                                    <option value="">-Select-</option>
                                    <option value="1" @if (@$ctrl_isproject == '1') selected @endif>Project
                                    </option>
                                    <option value="2" @if (@$ctrl_isproject == '2') selected @endif>Channel
                                    </option>
                                    <option value="3" @if (@$ctrl_isproject == '3') selected @endif>Corporate
                                    </option>
                                    <option value="0" @if (@$ctrl_isproject == '0') selected @endif>Lead
                                    </option>
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
                                <label for="" class="form-label">Follow Up Date</label>
                                <input class="form-control date-picker" id="followupdt_filter" type="text"
                                    autocomplete="off" name="followupdt_filter"
                                    value="{{ $ctrl_followupdt_filter ? \Carbon\Carbon::parse($ctrl_followupdt_filter)->format('d/m/Y') : '' }}">
                            </div>

                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">Filter By</label>
                                <select class="form-control" name="filter_by" id="filter_by"
                                    onchange="this.form.submit()">
                                    <option value="">-Select-
                                    </option>
                                    <option value="today">Today
                                    </option>
                                    <option value="this_week">This Week
                                    </option>
                                    <option value="last_week">Last Week
                                    </option>
                                    <option value="this_month">This
                                        Month
                                    </option>
                                    <option value="last_month">Last
                                        Month
                                    </option>
                                    <option value="last_6_months">Last
                                        6 Months
                                    </option>
                                    <option value="this_year">This Year
                                    </option>
                                    <option value="last_year">Last Year
                                    </option>
                                </select>
                            </div>



                            <div class="col-md-3 filter-field d-none">
                                <button type="submit" class="btn btn-success mt-4 rounded-0"
                                    id="btnSubmit">Filter</button>
                            </div>
                        </div>
                        {{ Form::close() }}


                    </div>
                </div>
            </div>
        </div>

        <div class="left-nav-list">

             @if (!empty($lead_stats['statusCounts']))

            <div class="row task-row">
                <div class="cls-m-12 col-md-6 col-lg-3 col-xl-1-8">
                    <div class="task-card">
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

                        <div class="task-toggle-indicator" data-bs-toggle="collapse" href="#newCollapse" role="button"
                            aria-expanded="false" aria-controls="newCollapse">
                            <i class="ico icon-outline-alt-arrow-down  toggle-icon" id="icon-new"></i>
                        </div>
                    </div>
                </div>

                <div class="cls-m-12 col-md-6 col-lg-3 col-xl-1-8 " style="cursor:pointer;">
                    <div class="task-card">
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

                        <div class="task-toggle-indicator" data-bs-toggle="collapse" href="#pendingCollapse" role="button"
                            aria-expanded="false" aria-controls="pendingCollapse">
                            <i class="ico icon-outline-alt-arrow-down  toggle-icon" id="icon-new"></i>
                        </div>
                    </div>


                </div>

                <div class="cls-m-12 col-md-6 col-lg-3 col-xl-1-8 " style="cursor:pointer;">

                    <div class="task-card">
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

                        <div class="task-toggle-indicator" data-bs-toggle="collapse" href="#qualifiedCollapse"
                            role="button" aria-expanded="false" aria-controls="qualifiedCollapse">
                            <i class="ico icon-outline-alt-arrow-down  toggle-icon" id="icon-new"></i>
                        </div>
                    </div>
                </div>

                <div class="cls-m-12 col-md-6 col-lg-3 col-xl-1-8 " style="cursor:pointer;">

                    <div class="task-card">
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

                        <div class="task-toggle-indicator" data-bs-toggle="collapse" href="#unqualifiedCollapse"
                            role="button" aria-expanded="false" aria-controls="unqualifiedCollapse">
                            <i class="ico icon-outline-alt-arrow-down  toggle-icon" id="icon-new"></i>
                        </div>
                    </div>
                </div>

                <div class="cls-m-12 col-md-6 col-lg-3 col-xl-1-8 " style="cursor:pointer;">

                    <div class="task-card">
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

                        <div class="task-toggle-indicator" data-bs-toggle="collapse" href="#closedCollapse" role="button"
                            aria-expanded="false" aria-controls="closedCollapse">
                            <i class="ico icon-outline-alt-arrow-down  toggle-icon" id="icon-new"></i>
                        </div>
                    </div>
                </div>


                <div class="cls-m-12 col-md-6 col-lg-3 col-xl-1-8">

                    <div class="task-card">
                        <div><i class="fa fa-chart-bar task-icon"></i></div>
                        <h5 class="task-title">Total Leads</h5>
                        <div id="completedCount" class="task-count">{{ $lead_stats['total_leads'] ?? 0 }} (100%)</div>
                    </div>
                </div>
                <div class="cls-m-12 col-md-6 col-lg-3 col-xl-1-8">

                    <div class="task-card">
                        <div><i class="fa fa-stopwatch task-icon"></i></div>
                        <h5 class="task-title">Av. Aging (Days)</h5>
                        <div id="completedCount" class="task-count">{{ $lead_stats['avg_aging_days'] ?? 0 }} Days</div>
                    </div>
                </div>
                <div class="cls-m-12 col-md-6 col-lg-3 col-xl-1-8">

                    <div class="task-card">
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


            <div class="table-responsive mb-4 mt-4">
                <table id="long-list" class="table table-hover" style="table-layout: fixed;width:100%">

                    <thead>
                        <tr>
                            <th class="text-center" style="width:60px">@lang('Lead No')</th>
                            <th class="text-center" style="width:60px">@lang('Deal ID')</th>

                            <th style="width: 50px;">@lang('Company')</th>

                            <th style="width:150px">@lang('Lead Name')</th>
                            <th style="width:150px">@lang('Customer')</th>
                            <th style="width:60px">@lang('Region')</th>
                            <th style="width: 100px">@lang('Brand')</th>
                            <th style="width:100px">@lang('Sales Person')</th>
                            <th style="width:150px">@lang('Stage')</th>
                            <th style="width:100px">@lang('Sub Stage')</th>
                            <th style="width:60px">@lang('Source')</th>
                            <th style="width:80px">@lang('Date')</th>
                            <th style="width:80px">@lang('Updated On')</th>
                            <th style="width:30px">@lang('Aging Days')</th>
                            <th style="width:30px">@lang('No. Followups')</th>

                        </tr>
                    </thead>


                <tbody>



                        @foreach ($leads as $value)
                            <tr @if ($value->status == 4) @if ($value->follow_up_date)
                                @if (\Carbon\Carbon::parse($value->follow_up_date)->isToday())
                                style="background-color:#fff9db !important; color:#b38600;"
                                @elseif(
                                    \Carbon\Carbon::parse($value->follow_up_date)->isPast() &&
                                        !\Carbon\Carbon::parse($value->follow_up_date)->isToday()) style="background-color:#ffebeb
                                !important; color:#ff0000;" @endif
                                @endif
                        @endif

                        class=" {{ $value->deleted_at ? 'bg-dark' : '' }} ">
                        <td class="text-center"><a target="_blank"
                                href="{{ url('crm-leads/show/' . $value->id) }}">{{ @$value->lead_code->code }}</a>
                        </td>

                        <td class="text-center">

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


                        <td class="ellipsis-cell">
                            {{ @$value->lead_name }}

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
                        <td>{{ @$value->ownername->full_name }}</td>
                        <td>
                            @if ($value->status == 1)

                            <span class="badge bg-primary">New</span>

                                {{-- <span style="font-size:11px;padding:0.25em 0.4em;background-color:#cfe2ff"
                                    class="rounded-1 text-dark">New</span> --}}

                            @endif
                            @if ($value->status == 2)

                                <span 
                                    class="badge bg-success">Qualified</span>
                            @endif
                            @if ($value->status == 3)

                                <span
                                    class="badge bg-danger">Unqualified</span>
                            @endif
                            @if ($value->status == 4)

                                <span 
                                    class="badge bg-warning">Pending Response</span>

                                @if ($value->follow_up_date)
                                    <span class="text-dark">Follow Up Date:
                                        {{ $value->follow_up_date ? date('d/m/Y', strtotime(@$value->follow_up_date)) : '' }}
                                    </span>
                                @endif
                            @endif
                            @if ($value->status == 10)

                                <span 
                                    class="badge bg-secondary">Closed</span>
                            @endif
                            @if ($value->status == 0)

                                <span 
                                    class="badge bg-success">Converted</span>
                                <?php $d = $deal_det->where('id', $value->deal_id)->first(); ?>
                                @if ($d && $d->stage == 1)

                                    <span 
                                        class="badge bg-primary">Prospecting</span>
                                @endif
                                @if ($d && $d->stage == 2)

                                    <span 
                                        class="badge bg-warning">Quote</span>
                                @endif
                                @if ($d && $d->stage == 3)

                                    <span
                                        class="badge bg-secondary">Closure</span>
                                @endif
                                @if ($d && $d->stage == 4)
                                    <?php
                                    $data = App\SysHelper::deal_track_status($d->id);
                                    
                                    ?>
                                    @if ($data != 'completed')
                                        <span 
                                            class="badge bg-success">Won</span>
                                        <span
                                            class="badge bg-success text-capitalize">{{ $data }}</span>
                                    @else
                                        <span 
                                            class="badge bg-success text-capitalize">{{ $data }}</span>
                                    @endif
                                @endif
                                @if ($d && $d->stage == 5)
                                    <span 
                                        class="badge bg-danger">Lost</span>
                                @endif
                                @if ($d && $d->stage == 6)

                                    <span 
                                        class="badge bg-secondary">Cancelled</span>
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

                            // Map colors (soft pastel style)
                            $subStatusColors = [
                                1 => 'bg-primary', // Primary light
                                2 => 'bg-info', // Info light
                                3 => 'bg-danger', // Danger light
                                4 => 'bg-danger', // Danger light
                                5 => 'bg-secondary', // Secondary light
                                6 => 'bg-warning', // Warning light
                                7 => 'bg-neutral', // Neutral
                                8 => 'bg-success', // Success light
                                9 => 'bg-warning', // Warning light
                                10 => 'bg-info', // Info light
                                11 => 'bg-primary', // Primary light
                                12 => 'bg-success', // Success light
                                13 => 'bg-secondary', // Secondary light
                                14 => 'bg-neutral', // Neutral
                            ];

                            $color = $subStatusColors[$value->sub_status] ?? '#e2e3e5'; // default gray
                        @endphp

                        <td class="ellipsis-cell">
                            @if ($value->sub_status == 8 || $value->sub_status == 12 || $value->sub_status == 14)

                                @if ($value->sub_status_comment)
                                    <span 
                                        class="badge {{$color}}">{{ $value->sub_status_comment }}</span>
                                @endif
                            @else
                                @if (isset($subStatusMap[$value->sub_status]))
                                    <span 
                                        class="badge {{$color}}">
                                        {{ $subStatusMap[$value->sub_status] }}
                                    </span>
                                @endif

                            @endif



                        </td>

                        <td>
                            {{ $value->source }}
                        </td>
                        <td class="text-center">{{ date('d/m/Y', strtotime(@$value->created_at)) }}
                            {{ date('h:i A', strtotime(@$value->created_at)) }}
                        </td>
                        <td class="text-center">{{ date('d/m/Y', strtotime(@$value->updated_at)) }}
                            {{ date('h:i A', strtotime(@$value->updated_at)) }}
                        </td>
                        <td class="text-center">{{ $value->getAgingDays() > 0 ? $value->getAgingDays() : '' }}</td>
                        <td class="text-center">{{ $value->followup_count ?? '' }}</td>

                        
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
            </div>
        </div>
    </aside>











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

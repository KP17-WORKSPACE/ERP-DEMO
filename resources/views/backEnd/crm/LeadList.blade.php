@extends('backEnd.newmasterpage')
@section('mainContent')
    <?php try { ?>

   
    <script src="https://cdn.jsdelivr.net/npm/exceljs@4.3.0/dist/exceljs.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

    

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

                sessionStorage.setItem('listViewLeadList', 'long');
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

                sessionStorage.setItem('listViewLeadList', 'short');

            }


        }


        //added ny kp
        function toggleLongFilters() {

            document.querySelectorAll('#filters-long .filter-field').forEach(el => {
                el.classList.toggle('d-none');
            });
        }




        // Initialize view from sessionStorage (tab-specific)
        document.addEventListener('DOMContentLoaded', () => {
            // Check if we have customer_action parameter (add/edit mode)
            const urlParams = new URLSearchParams(window.location.search);
            const hasCustomerAction = urlParams.has('lead_action');
            
            // If in add/edit mode, force short view
            if (hasCustomerAction) {
                sessionStorage.setItem('listViewLeadList', 'short');
                isFullList = true; // Set to true so toggle switches to short
                list_style_new(); // Switch to short view
            } else {
                // Normal behavior - use saved view from sessionStorage
                const savedView = sessionStorage.getItem('listViewLeadList');
                if (savedView === 'long') {
                    isFullList = false; // so that toggling once activates full view
                    list_style_new();
                } else {
                    // Default to short view
                    isFullList = true; // so that toggling once activates short view
                    list_style_new();
                }
            }

            // Attach event to sidebar links to force short view on navigation
            document.querySelectorAll('.sub-nav-item').forEach(link => {
                link.addEventListener('click', () => {
                    sessionStorage.setItem('listViewLeadList', 'short');
                });
            });



        });

        
        function toggleStats() {

            document.querySelectorAll('#task-cards').forEach(el => {
                el.classList.toggle('d-none');
            });
        }

    </script>


    <style>
        /* Smooth collapse transition */
        .collapse {
            transition: height 0.35s ease, opacity 0.35s ease;
        }

        .collapsing {
            opacity: 0.8;
            transition: height 0.35s ease, opacity 0.35s ease;
        }


        .pagination .page-item.active .page-link {
            background-color: #198754 !important;
            /* Bootstrap success green */

            color: #fff !important;
        }


        .col-5-custom {
            flex: 0 0 auto;
            width: 20%;

        }
    </style>
    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>


    <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">

    <aside class="left-nav col-3" id="leftSidebar">
        <div class="resizer" id="sidebarResizer"></div>
        <div class="short-list" id="filters-short">
            <h4 class="mb-2" style=" margin-left: -6px;">Leads @php
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


            <div class="search-filter-container mb-4" style=" margin-left: -6px;">
                <div class="input-group flex-nowrap">
                    <input type="text" name="lead_id" id="search_lead" class="form-control" placeholder="Document No"
                        aria-label="Search" aria-describedby="addon-wrapping" value="">
                </div>




                <button type="button" class="btn btn-light" id="list_style_button" onclick="list_style_new()">
                    <i class="ico icon-outline-list-down"></i>
                </button>
            </div>



        </div>

        <div class="long-list  d-none" id="filters-long">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="mb-2">Leads List @php
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


                <div class="search-filter-container mb-0 d-flex align-items-center justify-content-center">


                    <input type="text" id="tableSearch" class="form-control"
                        style="font-size:13px; width: 350px; 
                        top: 12px;
                        right: 120px;"
                        placeholder="Search">


                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ico icon-outline-hamburger-menu"></i>
                        </button>
                        <ul class="dropdown-menu" style="">
                            <li>
                                <button onclick="toggleStats()"
                                    class="dropdown-item d-flex align-items-center text-success"><i
                                        class="ico icon-outline-chart-square text-success title-15 me-2"></i> Leads
                                    Stats</button>
                            </li>

                            <li>
                                <a
                                    href="{{ url('crm-leads-report-company') }}"class="dropdown-item d-flex align-items-center"><i
                                        class="ico icon-outline-file text-success title-15 me-2"></i> Leads Report</a>
                            </li>

                            <li>
                                <a href="#" id="exportExcelLeads" class="dropdown-item d-flex align-items-center"><i
                                        class="ico icon-outline-export text-success title-15 me-2"></i> Export</a>
                            </li>


                        </ul>
                    </div>



                    <button class="btn btn-light" onclick="toggleLongFilters()">
                        <i class="ico icon-outline-magnifer"></i>
                    </button>

                    <button class="btn btn-light" id="list_style_button" onclick="list_style_new()">
                        <i class="ico icon-outline-list-down"></i>
                    </button>
                </div>
            </div>

            <div class="search-filter-container mt-1 mb-4 filter-field d-none border">

                <div class="card">
                    <div class="card-body">

                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-leads/show', 'method' => 'get', 'id' => 'crm-leads-search2']) }}
                        <div class="row">

                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">Lead ID</label>
                                <input class="form-control" type="text" autocomplete="off" name="lead_id"
                                    value="{{ $ctrl_lead_id }}">
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


                            @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 35)
                                <div class="col-1-5 mb-2">
                                    <label for="" class="form-label">Owner</label>
                                    <select class="form-control js-example-basic-single" name="owner_id" id="owner_id">
                                        <option value="">-Select-</option>
                                        @foreach ($staff as $value)
                                            <option value="{{ @$value->user_id }}"
                                                @if ($ctrl_owner == $value->user_id) selected @endif>{{ @$value->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            @if (Auth::user()->role_id == 13)
                                <div class="col-1-5 mb-2">
                                    <label for="" class="form-label">Sales Person</label>
                                    <select class="form-control js-example-basic-single" name="owner_id" id="owner_id">
                                        <option value="">-Select-</option>
                                        @foreach ($staff as $value)
                                            <option value="{{ @$value->user_id }}"
                                                @if ($ctrl_owner == $value->user_id) selected @endif>{{ @$value->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">Status</label>
                                <select class="form-contro js-example-basic-single" name="status_id" id="status_id">
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
                                <select class="form-control js-example-basic-single" name="sub_status" id="sub_status">
                                    <option value="">-Select-</option>
                                </select>
                            </div>

                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">Source</label>
                                <select class="form-control js-example-basic-single" name="source_id" id="source_id">
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
                                <select class="form-control js-example-basic-single" name="isproject_id" id="isproject_id">
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
                                <select class="form-control js-example-basic-single" name="filter_by" id="filter_by"
                                    onchange="this.form.submit()">
                                    <option value="" @if ($filter_by == '') selected @endif>-Select-
                                    </option>
                                    <option value="today" @if ($filter_by == 'today') selected @endif>Today
                                    </option>
                                    <option value="this_week" @if ($filter_by == 'this_week') selected @endif>This Week
                                    </option>
                                    <option value="last_week" @if ($filter_by == 'last_week') selected @endif>Last Week
                                    </option>
                                    <option value="this_month" @if ($filter_by == 'this_month') selected @endif>This
                                        Month
                                    </option>
                                    <option value="last_month" @if ($filter_by == 'last_month') selected @endif>Last
                                        Month
                                    </option>
                                    <option value="last_6_months" @if ($filter_by == 'last_6_months') selected @endif>Last
                                        6 Months
                                    </option>
                                    <option value="this_year" @if ($filter_by == 'this_year') selected @endif>This Year
                                    </option>
                                    <option value="last_year" @if ($filter_by == 'last_year') selected @endif>Last Year
                                    </option>
                                </select>
                            </div>

                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">Column View</label>
                                <select class="form-control js-example-basic-single" name="column_view" id="column_view">
                                    <option value="all">All Columns</option>
                                    <option value="short" selected>Short View</option>
                                </select>
                            </div>

                            <div class="col-md-1 filter-field d-none">
                                <button type="submit" class="btn btn-light mt-4">
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

            <ul id="short-list" class="nav flex-column nav-pills" id="companyTabNavs" role="tablist">
                @if (count($leads) > 0)

                    @foreach ($leads as $item)
                        <li class="nav-item w-100" role="presentation">
                            <button class="nav-link lead-item {{ $active_id == $item->id ? 'active' : '' }}"
                                data-id="{{ $item->id }}" id="purchase-order-1-tab" data-bs-toggle="tab"
                                data-bs-target="#purchase-order-1" type="button" role="tab"
                                aria-controls="purchase-order-1" aria-selected="true">
                                <div class="row w-100">

                                      <div class="col-12">
                                        <label class="form-control-plaintext truncate-text">
                                            {{ @$item->customername->customer_name_display }}  
                                        
                                            @if (@App\SysHelper::getCompanyCodeSettings()['is_customer_code'])
                                            ({{ @$item->customername->code }})
                                            @endif
                                        </label>
                                    </div>
                                     
                                    <div class="col-4">

                                        <div class="form-control-plaintext" style="font-size:11px">{{ @$item->code }}</div>

                                    </div>
                                    <div class="col-4 text-center">
                                        <div class="form-control-plaintext" style="font-size:11px">
                                            {{ date('d/m/Y', strtotime(@$item->created_at)) }}</div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="form-control-plaintext truncate-text" style="font-size:11px">
                                            @php
                                                try {
                                                    @$itemcode = @$item->lead_deal_code->code ?? null;
                                                } catch (\Exception $e) {
                                                    @$itemcode = null;
                                                }
                                            @endphp

                                            @if (@$itemcode)
                                                {{ @$itemcode }}
                                            @else
                                            @endif
                                        </div>
                                    </div>

                                  
                                   
                                </div>
                            </button>
                        </li>
                    @endforeach
                @else
                    <li class="w-100 text-center">
                        <div class="d-flex flex-column align-items-center justify-content-center text-muted">
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mb-3"
                                style="width:60px; height:60px; font-size:24px;">
                                <i class="ico icon-outline-info-square"></i>
                            </div>
                            <p class="mb-1 fw-semibold">No Records Found</p>
                            <small class="text-secondary">Try adjusting your filters or add a new lead</small>
                        </div>
                    </li>
                @endif
            </ul>
           



            <style>
                /* Card-like style for Bootstrap 3 */
                .task-card {
                    border-radius: 6px;
                    padding: 10px;
                    text-align: center;


                    /* background-color: #deebe1; */

                    min-height: 85px;
                    /* fixed card height */
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                }


                .task-card-count {
                    border-radius: 6px;
                    padding: 10px;
                    text-align: center;

                    /* background-color: #deebe1; */

                    min-height: 85px;
                    /* allow growth if needed */
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    position: relative;
                }


                .sub-status {
                    /* background: #f8f9fa; */
                    border-radius: 4px;
                    margin-top: 6px;
                    padding: 6px 10px;
                    font-size: 13px;
                }

                .sub-status-div {
                    display: flex;
                    justify-content: space-between;
                    padding: 3px 0;
                    border-bottom: 1px solid #eaeaea;
                }

                .sub-status-div:last-child {
                    border-bottom: none;
                }




                .task-card canvas {
                    max-height: 120px !important;
                    /* keeps charts compact */
                    max-width: 100% !important;
                }

                .task-icon {
                    margin-bottom: 8px;
                    font-size: 24px;
                    /* fa-lg equivalent */
                }

                .task-title {
                    margin-bottom: 8px;
                    font-weight: 600;
                    font-size: 13px;
                }

                .task-count {
                    font-weight: bold;
                    font-size: 12px;
                    color: #0b2262;

                }

                . div {
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




            @if (!empty($lead_stats['statusCounts']))

                <div class="row  mt-2 d-none" id="task-cards">
                    <!-- Status Distribution -->
                    <div class="col-md-3 border">
                        <div class="task-card text-center">
                            <h5 class="task-title ">Leads Distribution</h6>
                                <canvas id="statusPie"></canvas>
                        </div>
                    </div>

                    <!-- Conversion Funnel -->
                    <div class="col-md-3 border  border-start-0">
                        <div class="task-card  text-center">
                            <h5 class="task-title ">Conversion Funnel</h6>
                                <canvas id="funnelBar"></canvas>
                        </div>
                    </div>


                    <div class="col-6 ">
                        <div class="row ">
                            <div class="col-md-3 border border-start-0">
                                <div class="task-card-count ">
                                    <div class="filter-by-status" data-status-id="1" style="cursor:pointer;">
                                        <i class="fa 	fa-plus-circle task-icon"></i>
                                        <h5 class="task-title text-muted">New</h6>
                                            <div id="totalTasks" class="task-count">
                                                {{ $lead_stats['statusCounts'][1] ?? 0 }}
                                                ({{ $lead_stats['total_leads'] > 0 ? round((($lead_stats['statusCounts'][1] ?? 0) / $lead_stats['total_leads']) * 100, 2) : 0 }}%)
                                            </div>
                                    </div>
                                    <div class="sub-status collapse" id="newCollapse">
                                        <div class="sub-status-div" data-sub-status-id="1"><span
                                                class="sub-status-title">Just
                                                received,
                                                uncontacted</span><span class="sub-status-value">
                                                {{ $lead_stats['sub_statusCounts'][1] ?? 0 }}</span></div>
                                    </div>

                                    <div class="task-toggle-indicator" data-bs-toggle="collapse" href="#newCollapse"
                                        role="button" aria-expanded="false" aria-controls="newCollapse">
                                        <i class="ico icon-outline-alt-arrow-down toggle-icon" id="icon-new"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 border  border-start-0" style="cursor:pointer;">
                                <div class="task-card-count  ">
                                    <div class="filter-by-status" data-status-id="4">
                                        <i class="fa 	fa-hourglass-half task-icon"></i>
                                        <h5 class="task-title filter-by-status text-muted">Pending</h5>
                                        <div id="dueToday" class="task-count">{{ $lead_stats['statusCounts'][4] ?? 0 }}
                                            ({{ $lead_stats['total_leads'] > 0 ? round((($lead_stats['statusCounts'][4] ?? 0) / $lead_stats['total_leads']) * 100, 2) : 0 }}%)
                                        </div>
                                    </div>
                                    <div class="sub-status collapse" id="pendingCollapse">
                                        <div class="sub-status-div" data-sub-status-id="9"><span
                                                class="sub-status-title">Waiting
                                                for
                                                EUD</span><span
                                                class="sub-status-value">{{ $lead_stats['sub_statusCounts'][9] ?? 0 }}</span>
                                        </div>

                                        <div class="sub-status-div" data-sub-status-id="10"><span
                                                class="sub-status-title">Waiting
                                                for Vendor
                                                Price</span><span
                                                class="sub-status-value">{{ $lead_stats['sub_statusCounts'][10] ?? 0 }}</span>
                                        </div>
                                        <div class="sub-status-div" data-sub-status-id="11"><span
                                                class="sub-status-title">Quoted
                                                -
                                                Waiting for
                                                Response</span><span
                                                class="sub-status-value">{{ $lead_stats['sub_statusCounts'][11] ?? 0 }}</span>
                                        </div>
                                        <div class="sub-status-div" data-sub-status-id="12"><span
                                                class="sub-status-title">Other
                                                Reasons</span><span
                                                class="sub-status-value">{{ $lead_stats['sub_statusCounts'][12] ?? 0 }}</span>
                                        </div>
                                    </div>

                                    <div class="task-toggle-indicator" data-bs-toggle="collapse" href="#pendingCollapse"
                                        role="button" aria-expanded="false" aria-controls="pendingCollapse">
                                        <i class="ico icon-outline-alt-arrow-down toggle-icon" id="icon-new"></i>
                                    </div>
                                </div>


                            </div>

                            <div class="col-md-3 border  border-start-0" style="cursor:pointer;">

                                <div class="task-card-count ">
                                    <div class="filter-by-status" data-status-id="2">
                                        <i class="fa fa-thumbs-up task-icon"></i>
                                        <h5 class="task-title text-muted">Qualified</h5>
                                        <div id="dueTasks" class="task-count">
                                            {{ ($lead_stats['statusCounts'][2] ?? 0) + ($lead_stats['statusCounts'][0] ?? 0) }}
                                            ({{ $lead_stats['total_leads'] > 0 ? round(((($lead_stats['statusCounts'][2] ?? 0) + ($lead_stats['statusCounts'][0] ?? 0)) / $lead_stats['total_leads']) * 100, 2) : 0 }}%)
                                        </div>
                                    </div>
                                    <div class="sub-status collapse" id="qualifiedCollapse">
                                        <div class="sub-status-div" data-sub-status-id="2"><span
                                                class="sub-status-title">Sent to
                                                Sales</span><span
                                                class="sub-status-value">{{ $lead_stats['sub_statusCounts'][2] ?? 0 }}</span>
                                        </div>
                                        <div class="sub-status-div" data-sub-status-id="d1"><span
                                                class="sub-status-title">Prospecting</span><span
                                                class="sub-status-value">{{ $lead_stats['deals_statusCounts'][1] ?? 0 }}</span>
                                        </div>
                                        <div class="sub-status-div" data-sub-status-id="d2"><span
                                                class="sub-status-title">Quote</span><span
                                                class="sub-status-value">{{ $lead_stats['deals_statusCounts'][2] ?? 0 }}</span>
                                        </div>
                                        <div class="sub-status-div" data-sub-status-id="d3"><span
                                                class="sub-status-title">Closure</span><span
                                                class="sub-status-value">{{ $lead_stats['deals_statusCounts'][3] ?? 0 }}</span>
                                        </div>
                                        <div class="sub-status-div" data-sub-status-id="d4"><span
                                                class="sub-status-title">Won</span><span
                                                class="sub-status-value">{{ $lead_stats['deals_statusCounts'][4] ?? 0 }}</span>
                                        </div>
                                        <div class="sub-status-div" data-sub-status-id="d5"><span
                                                class="sub-status-title">Lost</span><span
                                                class="sub-status-value">{{ $lead_stats['deals_statusCounts'][5] ?? 0 }}</span>
                                        </div>
                                    </div>

                                    <div class="task-toggle-indicator" data-bs-toggle="collapse"
                                        href="#qualifiedCollapse" role="button" aria-expanded="false"
                                        aria-controls="qualifiedCollapse">
                                        <i class="ico icon-outline-alt-arrow-down toggle-icon" id="icon-new"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 border  border-start-0" style="cursor:pointer;">

                                <div class="task-card-count  ">
                                    <div class="filter-by-status" data-status-id="3">
                                        <i class="fa fa-times-circle task-icon"></i>
                                        <h5 class="task-title text-muted">Unqualified</h5>
                                        <div id="notStartedCount" class="task-count">
                                            {{ $lead_stats['statusCounts'][3] ?? 0 }}
                                            ({{ $lead_stats['total_leads'] > 0 ? round((($lead_stats['statusCounts'][3] ?? 0) / $lead_stats['total_leads']) * 100, 2) : 0 }}%)
                                        </div>
                                    </div>
                                    <div class="sub-status collapse" id="unqualifiedCollapse">
                                        <div class="sub-status-div" data-sub-status-id="3"><span
                                                class="sub-status-title">Budget
                                                Issue</span><span
                                                class="sub-status-value">{{ $lead_stats['sub_statusCounts'][3] ?? 0 }}</span>
                                        </div>
                                        <div class="sub-status-div" data-sub-status-id="4"><span
                                                class="sub-status-title">Not
                                                Interested</span><span
                                                class="sub-status-value">{{ $lead_stats['sub_statusCounts'][4] ?? 0 }}</span>
                                        </div>
                                        <div class="sub-status-div" data-sub-status-id="5"><span
                                                class="sub-status-title">Wrong
                                                Contact</span><span
                                                class="sub-status-value">{{ $lead_stats['sub_statusCounts'][5] ?? 0 }}</span>
                                        </div>
                                        <div class="sub-status-div" data-sub-status-id="6"><span
                                                class="sub-status-title">Timeline
                                                not matching</span><span
                                                class="sub-status-value">{{ $lead_stats['sub_statusCounts'][6] ?? 0 }}</span>
                                        </div>
                                        <div class="sub-status-div" data-sub-status-id="7"><span
                                                class="sub-status-title">Product/Service
                                                mismatch</span><span
                                                class="sub-status-value">{{ $lead_stats['sub_statusCounts'][7] ?? 0 }}</span>
                                        </div>
                                        <div class="sub-status-div" data-sub-status-id="8"><span
                                                class="sub-status-title">Other
                                                Reason</span><span
                                                class="sub-status-value">{{ $lead_stats['sub_statusCounts'][8] ?? 0 }}</span>
                                        </div>
                                    </div>

                                    <div class="task-toggle-indicator" data-bs-toggle="collapse"
                                        href="#unqualifiedCollapse" role="button" aria-expanded="false"
                                        aria-controls="unqualifiedCollapse">
                                        <i class="ico icon-outline-alt-arrow-down toggle-icon" id="icon-new"></i>
                                    </div>
                                </div>
                            </div>




                            <div class="col-md-3 border border-start-0" style="cursor:pointer;">

                                <div class="task-card-count ">
                                    <div class="filter-by-status" data-status-id="10">
                                        <i class="fa 	fa-archive  task-icon"></i>
                                        <h5 class="task-title text-muted">Closed</h5>
                                        <div id="inProgressCount" class="task-count">
                                            {{ $lead_stats['statusCounts'][10] ?? 0 }}
                                            ({{ $lead_stats['total_leads'] > 0 ? round((($lead_stats['statusCounts'][10] ?? 0) / $lead_stats['total_leads']) * 100, 2) : 0 }}%)
                                        </div>
                                    </div>
                                    <div class="sub-status collapse" id="closedCollapse">
                                        <div class="sub-status-div" data-sub-status-id="13"><span
                                                class="sub-status-title">No
                                                Response</span><span
                                                class="sub-status-value">{{ $lead_stats['sub_statusCounts'][13] ?? 0 }}</span>
                                        </div>
                                        <div class="sub-status-div" data-sub-status-id="14"><span
                                                class="sub-status-title">Other
                                                Reason</span><span class="sub-status-value">
                                                {{ $lead_stats['sub_statusCounts'][14] ?? 0 }}</span>
                                        </div>
                                    </div>

                                    <div class="task-toggle-indicator" data-bs-toggle="collapse" href="#closedCollapse"
                                        role="button" aria-expanded="false" aria-controls="closedCollapse">
                                        <i class="ico icon-outline-alt-arrow-down toggle-icon" id="icon-new"></i>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-3 border  border-start-0">

                                <div class="task-card-count  ">
                                    <div><i class="fa fa-chart-bar task-icon"></i></div>
                                    <h5 class="task-title text-muted">Total Leads</h5>
                                    <div id="completedCount" class="task-count">{{ $lead_stats['total_leads'] ?? 0 }}
                                        (100%)
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 border  border-start-0">

                                <div class="task-card-count  ">
                                    <div><i class="fa fa-stopwatch task-icon"></i></div>
                                    <h5 class="task-title text-muted">Av. Aging (Days)</h5>
                                    <div id="completedCount" class="task-count">{{ $lead_stats['avg_aging_days'] ?? 0 }}
                                        Days
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 border  border-start-0">

                                <div class="task-card-count ">
                                    <div><i class="fa  fa-percentage task-icon"></i></div>
                                    <h5 class="task-title text-muted">Conv. Rate (%)</h5>
                                    <div id="completedCount" class="task-count">
                                        {{ $lead_stats['total_leads'] > 0 ? round(((($lead_stats['statusCounts'][2] ?? 0) + ($lead_stats['statusCounts'][0] ?? 0)) / $lead_stats['total_leads']) * 100, 2) : 0 }}%
                                    </div>
                                </div>
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

                        } else {
                            sortParam = 'sort_id=7';
                        }
                        var url = form.attr('action') + '?' + params + '&' + sortParam;
                        window.open(url, '_blank');
                    });

                    $('.sub-status-div').on('click', function() {
                        var substatusId = $(this).data('sub-status-id');

                        $('#sub_status').val(substatusId).trigger('change');
                        var form = $('#crm-leads-search2');
                        var params = form.serialize();
                        var sortParam = '';

                        const urlParams = new URLSearchParams(window.location.search);
                        if (urlParams.has('sort_id')) {

                        } else {
                            sortParam = 'sort_id=7';
                        }
                        var url = form.attr('action') + '?' + params + '&' + sortParam;
                        window.open(url, '_blank');
                    });


                });
            </script>
  <script>
$(document).ready(function () {

    // 🟢 Define width sets for each view
    const columnWidths = {
        all: [
            @if (session('logged_session_data.company_id') == 1)
            80, 80, 90, 150, 150, 100, 50, 130, 90, 50, 80, 50, 80, 50, 50, 90
            @else
            80, 80, 90, 150, 150, 100, 90, 130, 90, 50, 50, 80, 50, 50, 90
            @endif
        ],
        short: [
            @if (session('logged_session_data.company_id') == 1)
          
            60, 60, 70, 120, 170, 120, 100, 90, 65, 65, 50
            @else
            
            
             60, 60, 70, 120, 170, 120, 100, 90, 65, 65, 50
            @endif
        ]
    };

    /**
     * 🟩 Apply column widths to <thead> and <tbody>
     */
    function setManualWidths(view = 'short') {
        const $table = $('.table-fixed-header');
        const widths = columnWidths[view] || columnWidths.all;
        const $theadTh = $table.find('thead th');

        $theadTh.each(function (i) {
            const width = widths[i] || 100; // fallback
            $(this).css({ width: width + 'px', 'min-width': width + 'px', 'max-width': width + 'px' });
            $table.find('tbody td:nth-child(' + (i + 1) + ')')
                .css({ width: width + 'px', 'min-width': width + 'px', 'max-width': width + 'px' });
        });
    }

    /**
     * 🟩 Toggle visibility of hideable columns
     */
    function toggleColumns(view) {
        const $hideableCols = $('.col-hideable');
        if (view === 'short') {
            $hideableCols.hide();
        } else {
            $hideableCols.show();
        }

        // Save user preference
        localStorage.setItem('lead_column_view', view);

        // Reapply widths once DOM updates
        setTimeout(() => setManualWidths(view), 50);
    }

    /**
     * 🟩 Initialize saved view
     */
    const savedView = localStorage.getItem('lead_column_view') || 'all';
    $('#column_view').val(savedView);
    toggleColumns(savedView);

    /**
     * 🟩 Handle dropdown toggle
     */
    $('#column_view').on('change', function () {
        const selected = $(this).val();
        toggleColumns(selected);
    });

    /**
     * 🟩 Reapply on window resize
     */
    $(window).on('resize', function () {
        const currentView = $('#column_view').val() || 'all';
        setManualWidths(currentView);
    });
});
</script>


            <div class="table-responsive mb-4 mt-2">

                <table id="long-list" class="table table-hover data-table d-none table-fixed-header" style="table-layout: fixed;width:100%">

                    <thead class="">
                        <tr>
                            <th class="text-center" style="width:80px">@lang('Lead No')</th>
                            <th class="text-center" style="width:80px">@lang('Deal No')</th>
                            <th class="text-center" data-column="date" style="width:80px">@lang('Date')</th>
                            <th style="width:150px">@lang('Customer')</th>
                            <th style="width:150px">@lang('Lead Name')</th>

                            <th style="width:100px">@lang('Sales Person')</th>
                            <th style="width: 50px">@lang('Brand')</th>

                            <th style="width:150px">@lang('Status')</th>
                            <th style="width:100px">@lang('Sub Status')</th>
                            <th style="width:50px" class="text-center">@lang('Source')</th>


                            @if (session('logged_session_data.company_id') == 1)
                                <th class="col-hideable" style="width: 80px;">@lang('Company')</th>
                            @endif
                            <th class="col-hideable" data-column="region" style="width:50px">@lang('Region')</th>
                            <th class="col-hideable" data-column="updated" style="width:80px">@lang('Updated On')</th>
                            <th class="col-hideable" data-column="aging" style="width:50px">@lang('Aging Days')</th>
                            <th class="col-hideable" data-column="followups" style="width:50px">@lang('Followups')</th>
                            <th class="text-center" style="width: 90px;">@lang('Action')</th>
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

                        @if ($value->deleted_at) style="background-color:rgba(0, 0, 0, 0.19)" @endif>
                        <td class="text-center"><a class="lead-item" data-id="{{ $value->id }}"
                                onclick="list_style_new()">{{ @$value->code }}</a>
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
                                <a
                                    href="{{ url('crm-deals/show/' . $value->lead_deal_code->id) }}">{{ $code }}</a>
                            @else
                                --
                            @endif

                        </td>

                        <td class="text-center" data-column="date">{{ date('d/m/Y', strtotime(@$value->created_at)) }}
                            {{ date('h:i A', strtotime(@$value->created_at)) }}
                        </td>

                          <td class="ellipsis-cell">

                            {{ @$value->customername->name }}
                        </td>

                         <td class="ellipsis-cell">
                            {{ @$value->lead_name }}

                        </td>

                        <td>{{ @$value->ownername->full_name }}</td>

                         <td>
                            <div>
                                {{ @$value->tags }}</div>
                        </td>

                        <td>
                            @if ($value->status == 1)

                                <span class="badge bg-primary">New</span>

                                {{-- <span style="font-size:11px;padding:0.25em 0.4em;background-color:#cfe2ff"
                                    class="rounded-1 text-dark">New</span> --}}

                            @endif
                            @if ($value->status == 2)

                                <span class="badge bg-success">Qualified</span>
                            @endif
                            @if ($value->status == 3)

                                <span class="badge bg-danger">Unqualified</span>
                            @endif
                            @if ($value->status == 4)

                                <span class="badge bg-warning">Pending Response</span>

                                @if ($value->follow_up_date)
                                    <span class="text-dark">Follow Up Date:
                                        {{ $value->follow_up_date ? date('d/m/Y', strtotime(@$value->follow_up_date)) : '' }}
                                    </span>
                                @endif
                            @endif
                            @if ($value->status == 10)

                                <span class="badge bg-secondary">Closed</span>
                            @endif
                            @if ($value->status == 0)

                                <span class="badge bg-success">Converted</span>
                                <?php $d = $deal_det->where('id', $value->deal_id)->first(); ?>
                                @if ($d && $d->stage == 1)

                                    <span class="badge bg-primary">Prospecting</span>
                                @endif
                                @if ($d && $d->stage == 2)

                                    <span class="badge bg-warning">Quote</span>
                                @endif
                                @if ($d && $d->stage == 3)

                                    <span class="badge bg-secondary">Closure</span>
                                @endif
                                @if ($d && $d->stage == 4)
                                    <?php
                                    $data = App\SysHelper::deal_track_status($d->id);
                                    
                                    ?>
                                    @if ($data != 'completed')
                                        <span class="badge bg-success">Won</span>
                                        <span class="badge bg-success text-capitalize">{{ $data }}</span>
                                    @else
                                        <span class="badge bg-success text-capitalize">{{ $data }}</span>
                                    @endif
                                @endif
                                @if ($d && $d->stage == 5)
                                    <span class="badge bg-danger">Lost</span>
                                @endif
                                @if ($d && $d->stage == 6)

                                    <span class="badge bg-secondary">Cancelled</span>
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
                                    <span class="badge {{ $color }}">{{ $value->sub_status_comment }}</span>
                                @endif
                            @else
                                @if (isset($subStatusMap[$value->sub_status]))
                                    <span class="badge {{ $color }}">
                                        {{ $subStatusMap[$value->sub_status] }}
                                    </span>
                                @elseif($value->status == 1)
                                <span class="badge bg-primary">
                                        Just received, uncontacted
                                    </span>
                                @endif

                            @endif
                        </td>

                        <td class="text-center">
                            {{ $value->source }}
                        </td>


                        @if (session('logged_session_data.company_id') == 1)
                            <td class="col-hideable">{{ $value->company->company_name ?? '' }}</td>
                        @endif


                       

                      

                        <td class="col-hideable" data-column="region">
                            {{ @$value->customername->vatcountry->name }}
                        </td>

            
                        
                        <td class="text-center col-hideable" data-column="updated">{{ date('d/m/Y', strtotime(@$value->updated_at)) }}
                            {{ date('h:i A', strtotime(@$value->updated_at)) }}
                        </td>
                        <td class="text-center col-hideable" data-column="aging">{{ $value->getAgingDays() > 0 ? $value->getAgingDays() : '' }}</td>
                        <td class="text-center col-hideable" data-column="followups">{{ $value->followup_count ?? '' }}</td>

                        <td class="">

                            <div class="d-flex justify-content-center align-items-center">
                                <a class="btn btn-sm btn-light open-comments-modal" style="cursor: pointer;"
                                    data-lead-id="{{ $value->id }}"><i class="ico icon-outline-chat-round-dots"
                                        style="font-size:16px" aria-hidden="true"></i></a>



                                <a href="{{ url('crm-leads/show/' . $value->id . '?lead_action=edit') }}" data-id="{{ $value->id }}" class="btn btn-sm btn-light edit-lead-btn"><i
                                        class="ico icon-outline-pen-2 text-dark" style="font-size: 16px;"></i></a>


                                @if (Auth::user()->role_id == 1)
                                    @if ($value->deleted_at)
                                        <button data-id="{{ $value->id }}" data-bs-toggle="modal"
                                            data-bs-target="#restoreModal" type="button"
                                            class="btn btn-sm btn-light open-restore-modal" title="Restore">
                                            <i class="ico icon-bold-restart text-dark" style="font-size: 16px;"></i>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-sm btn-light open-delete-modal"
                                            data-id="{{ $value->id }}" data-bs-toggle="modal"
                                            data-bs-target="#deleteModal">
                                            <i class="ico icon-outline-trash-bin-minimalistic text-dark"
                                                style="font-size: 16px;"></i>
                                        </button>
                                    @endif
                                @endif
                            </div>


                        </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <?php try { ?>
                    <tfoot>
                        <tr>
                            <td colspan="15" class="text-center border-0">
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $leads->appends(request()->input())->links() }}
                                </div>
                            </td>
                        </tr>
                    </tfoot>

                    <?php } catch (\Exception $e) {
                        } ?>

                </table>
            </div>
        </div>
    </aside>

    <div class="content-container col-9">
        <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">

            <script>
                $(document).ready(function() {
                    // Delegated click works for both static + dynamic .data-item
                    $(document).on('click', '.lead-item', function() {
                        var id = $(this).data('id');

                        $('.lead-item').removeClass('active');
                        $('.lead-item[data-id="' + id + '"]').addClass('active');

                        var queryString = window.location.search; // keep filters
                        var baseUrl = window.location.origin + "/crm-leads/show";

                        // Append the new ID
                        // var newUrl = baseUrl + "/" + id + queryString;

                        // window.history.pushState({
                        //     path: newUrl
                        // }, '', newUrl);

                              var newUrl = "{{ url('crm-leads/show') }}/" + id;
                        window.history.pushState({
                            path: newUrl
                        }, '', newUrl);


                        var action = "{{ URL::to('crm-leads') }}/" + id + "/view";
                        $('#loading_bg').show();

                        $.ajax({
                            url: action,
                            method: 'GET',
                            success: function(response) {
                                $('#leads-details').html(response);
                                flatpickr(".date-picker", {
                                    dateFormat: "d/m/Y", // dd/mm/yyyy
                                    allowInput: true
                                });
                            },
                            error: function() {
                                $('#leads-details').html(
                                    '<p class="text-danger">No Details Available.</p>');
                            },
                            complete: function() {
                                $('#loading_bg').hide(); // Always hide loader after request completes
                            }
                        });
                    });
                });
            </script>



            <div class="" role="tabpanel" aria-labelledby="po-tab" id="leads-details">
                @if ($action === 'add')
                    @include('backEnd.crm.LeadAdd')
                @elseif($action === 'edit')
                    @include('backEnd.crm.LeadEdit', $editData)

                    {{-- @include('backEnd.purchaseorder.manage_purchase_order_edit', $editData) --}}
                @elseif (!empty($selectedLead) && is_array($selectedLead))
                    @include('backEnd.crm.LeadView', $selectedLead)
                @else
                    {{-- <p class="text-danger">No details available.</p> --}}

                    <div class="container-fluid d-flex flex-column justify-content-center align-items-center"
                        style="min-height: 90vh;">

                        <!-- Icon + Heading -->
                       <a href="{{ url('crm-leads/show?lead_action=add') }}" class="text-decoration-none text-dark">
                            <div class="text-center mb-4">
                                <div 
                                    class="rounded-circle bg-success text-white d-flex justify-content-center align-items-center mx-auto"
                                    style="width: 80px; height: 80px; font-size: 36px; cursor:pointer">
                                    <i class="ico icon-outline-add-square"></i>
                                </div>
                                <h1 class="fw-bold mt-3">Add New Lead</h1>
                                <p class="text-muted">Create and track your leads with ease</p>
                            </div>
                        </a>


                    </div>

                @endif
            </div>


        </div>
    </div>







    <div class="modal fade" id="commentsModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" style="top:10%">

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="poexcelimport">Lead Comments</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body">
                            <div id="commentsScrollContainer"
                                style="max-height: 300px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: .25rem;">
                                <table class="table table-hover" id="long-list" style="table-layout: fixed;width:100%">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="10%">Type</th>
                                            <th width="40%">Comment</th>
                                            <th width="20%">Person</th>
                                            <th class="text-center" width="10%"><i
                                                    class="ico icon-bold-paperclip"></i></th>
                                            <th width="20%">Date</th>
                                        </tr>
                                    </thead>

                                    <tbody id="commentsModalBody">
                                        <tr>
                                            <td colspan="5" class="text-center text-muted no-comments-found">No
                                                comments found
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Fixed Comment Form -->
                            <div class="pt-3 mt-3 border-top" style="flex-shrink: 0;">
                                <input type="hidden" name="current_lead_id" id="current_lead_id">
                                <label for="newComment" class="form-label font-weight-bold">Add Comment</label>
                                <textarea id="newComment" class="form-control mb-2" cols="10" rows="3" placeholder="Comment..."></textarea>
                            </div>


                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="submitComment" class="btn btn-light add-btn ms-2">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Add Comment
                    </button>
                </div>
            </div>


        </div>
    </div>



    <div class="modal side-panel fade" id="deleteModal" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="top:10%">
            <form method="POST" action="" id="deleteForm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="poexcelimport">Delete Lead</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body m-0 p-0">
                        <div class="card mb-0 mt-0">
                            <div class="card-body">

                                <p>Please provide a reason for deleting this lead:</p>
                                <textarea name="delete_reason" class="form-control" rows="3" required></textarea>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-light add-btn ms-2">
                            <i class="ico icon-outline-trash-bin-minimalistic text-success"></i> Delete
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <div class="modal side-panel fade" id="restoreModal" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="top:10%">
            <form method="POST" action="" id="restoreForm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="poexcelimport">Restore Lead</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body m-0 p-0">
                        <div class="card mb-0 mt-0">
                            <div class="card-body">

                                <p>Please provide a reason for restoring this lead:</p>
                                <textarea name="restore_reason" class="form-control" rows="3" required></textarea>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-light add-btn ms-2 text-success">
                            <i class="ico icon-bold-restart text-success" style="font-size: 16px;"></i> Restore
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>





    <script>
        $(document).ready(function() {
            if ($("#source").val() == "Other") {
                $("#source_o").css("display", "block");
                $("#source_o").prop('required', true);
                $("#sourcediv").css("display", "block");
            } else {
                $("#source_o").css("display", "none");
                $("#source_o").prop('required', false);
                $("#sourcediv").css("display", "none");
            }
        });


        $(document).ready(function() {
            if ($("#edit_source_o").val() == "Other") {
                $("#edit_source_o").css("display", "block");
                $("#edit_source_o").prop('required', true);
                $("#editsourcediv").css("display", "block");
            } else {
                $("#edit_source_o").css("display", "none");
                $("#edit_source_o").prop('required', false);
                $("#editsourcediv").css("display", "none");
            }
        });



        $(document).ready(function() {
            $(document).on("change", "#company_name", function() {
                var id = $("#company_name").val();
                get_cust_name(id);
                get_sales_person(id);
            });
        });

        $(document).ready(function() {
            $(document).on("change", "#edit_company_name", function() {
                var id = $("#edit_company_name").val();
                get_cust_name_edit_company(id);
                get_sales_person_edit_company(id);
            });
        });

        function get_cust_name(id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('crm-leads-customername') }}";
            $.ajax({
                url: action,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                },
                cache: false,
                success: function(dataResult) {
                    console.log(dataResult)
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var len = 0;
                    if (dataResult['data'] != null) {
                        len = dataResult['data'].length;
                    }
                    if (len > 0) {
                        for (var i = 0; i < len; i++) {

                            var name = dataResult['data'][i].customer_salutation + ' ' + dataResult['data'][i]
                                .first_name + ' ' + dataResult['data'][i].last_name;
                            var address = dataResult['data'][i].flat_office_no + ', ' + dataResult['data'][i]
                                .area + ', ' + dataResult['data'][i].city + ', ' + dataResult['data'][i]
                                .statename + ', ' + dataResult['data'][i].name;
                            $("#cust_name").val(name.replace('null ', '').replace('null', ''));
                            $("#cust_no").val(dataResult['data'][i].mobile);
                            $("#cust_email").val(dataResult['data'][i].email);
                            $("#address").val(address);
                            $("#cust_designation").val(dataResult['data'][i].designation).trigger('change');

                            //1.Reseller
                            if (dataResult['data'][i].account_type == 1) {
                                $("#isproject").val(1).trigger('change');
                            } //2.Enduser
                            if (dataResult['data'][i].account_type == 2) {
                                $("#isproject").val(2).trigger('change');
                            } //3.Ecommerce
                            if (dataResult['data'][i].account_type == 3) {
                                $("#isproject").val(3).trigger('change');
                            }

                        }
                    } else {
                        $("#cust_name").val();
                        $("#cust_no").val();
                        $("#cust_email").val();
                        $("#address").val();
                        $("#cust_designation").val();
                        $("#isproject").val();
                    }
                    $("#loading_bg").css("display", "none");
                }
            });
        }

        function get_sales_person(id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('get-salesperson-list') }}";
            $.ajax({
                url: action,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var len = 0;
                    if (dataResult['data'] != null) {
                        len = dataResult['data'].length;
                    }

                    if (len > 0) {
                        $('#owner').find('option').not(':first').remove();
                        for (var i = 0; i < len; i++) {
                            var id = dataResult['data'][i].id;
                            var name = dataResult['data'][i].full_name;
                            var selected = (i === 0) ? "selected" : "";
                            var option = "<option value='" + id + "'" + selected + ">" + name + "</option>";
                            $("#owner").append(option);
                        }
                    } else {
                        $('#owner').find('option').not(':first').remove();
                    }
                    $("#loading_bg").css("display", "none");
                }
            });
        }



        function get_cust_name_edit_company(id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('crm-leads-customername') }}";
            $.ajax({
                url: action,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var len = 0;
                    if (dataResult['data'] != null) {
                        len = dataResult['data'].length;
                    }
                    if (len > 0) {
                        for (var i = 0; i < len; i++) {

                            var name = dataResult['data'][i].customer_salutation + ' ' + dataResult['data'][i]
                                .first_name + ' ' + dataResult['data'][i].last_name;
                            var address = dataResult['data'][i].address + ', ' + dataResult['data'][i]
                                .address2 + ', ' + dataResult['data'][i].city + ', ' + dataResult['data'][i]
                                .statename + ', ' + dataResult['data'][i].name;
                            $("#edit_cust_name").val(name.replace('null ', '').replace('null', ''));
                            $("#edit_cust_no").val(dataResult['data'][i].mobile);
                            $("#edit_cust_email").val(dataResult['data'][i].email);
                            $("#edit_address").val(address);
                            $("#edit_cust_designation").val(dataResult['data'][i].designation);

                            //1.Reseller
                            if (dataResult['data'][i].account_type == 1) {
                                $("#edit_isproject").val(1).trigger('change');
                            } //2.Enduser
                            if (dataResult['data'][i].account_type == 2) {
                                $("#edit_isproject").val(2).trigger('change');
                            } //3.Ecommerce
                            if (dataResult['data'][i].account_type == 3) {
                                $("#edit_isproject").val(3).trigger('change');
                            }

                        }
                    } else {
                        $("#edit_cust_name").val();
                        $("#edit_cust_no").val();
                        $("#edit_cust_email").val();
                        $("#edit_address").val();
                        $("#edit_cust_designation").val();
                        $("#edit_isproject").val();
                    }
                    $("#loading_bg").css("display", "none");
                }
            });
        }

        function get_sales_person_edit_company(id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('get-salesperson-list') }}";
            $.ajax({
                url: action,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var len = 0;
                    if (dataResult['data'] != null) {
                        len = dataResult['data'].length;
                    }

                    if (len > 0) {
                        $('#edit_owner').find('option').not(':first').remove();
                        for (var i = 0; i < len; i++) {
                            var id = dataResult['data'][i].id;
                            var name = dataResult['data'][i].full_name;
                            var selected = (len === 1) ? "selected" : "";
                            var option = "<option value='" + id + "'" + selected + ">" + name + "</option>";
                            $("#edit_owner").append(option);
                        }
                    } else {
                        $('#edit_owner').find('option').not(':first').remove();
                    }
                    $("#loading_bg").css("display", "none");
                }
            });
        }

        $(document).on("click", "#add_company_leads", function() {

            var company_name_add = $("#company_name_add").val();
            var cust_name_add = $("#cust_name_add").val();
            var designation_add = $("#designation_add").val();
            var cust_no_add = $("#cust_no_add").val();
            var cust_email_add = $("#cust_email_add").val();
            // var cust_address_add = $("#cust_address_add").val();
            // var cust_address_add2 = $("#cust_address_add2").val();
            var country_add = $("#country_ship").val();
            var country_telephone = $("#country_telephone").val();
            var customer_website = $("#customer_website").val();
            var maps_location = $("#maps_location").val();
            var places_id = $("#place_id").val();
            var area = $("#cust_area").val();
            var building_name = $("#cust_building_name").val();
            var flat_no = $("#cust_flat_office_no").val();
            

            // if (!cust_address_add.trim()) {
            //     alert("Address 1 is required");
            //     return;
            // }

            var cust_city = $("#cust_city").val();
            var state_ship = $("#state_ship").val();
            var cust_pobox = $("#cust_pobox").val();
            var sales_person = $("#cust_sales_person").val();

            if(sales_person == null || sales_person == ""){
                alert("Please select Sales Person");
                return;
            }

            var payment_terms = $("#payment_terms").val();
            var account_type = $("#account_type").val();
            var company_id = $("#company").val();
            var customer_salutation_add = $("#salutation_cust").val();

            console.log("Selected Sales Person ID:", customer_salutation_add);

            var action = "{{ URL::to('add-customer-detail-popup') }}";
            $("#loading_bg").show();
            $.ajax({
                url: action,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}',
                    company_name_add: company_name_add,
                    customer_salutation_add: customer_salutation_add,
                    cust_name_add: cust_name_add,
                    designation_add: designation_add,
                    cust_no_add: cust_no_add,
                    cust_email_add: cust_email_add,
                    vat_country: country_add,
                    city: cust_city,
                    vat_state: state_ship,
                    zip_code: cust_pobox,
                    sales_person: sales_person,
                    payment_terms: payment_terms,
                    account_type: account_type,
                    company_id: company_id,
                    country_telephone: country_telephone,
                    customer_website: customer_website,
                    maps_location: maps_location,
                    places_id: places_id,
                    area: area,
                    building_name: building_name,
                    flat_no: flat_no,
                },
                cache: false,
                success: function(dataResult) {
                    //alert(dataResult);
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    if (dataResult['data'] == "ERROR") {
                        alert("Error found in something!!");
                        $("#add_company_leads2").css("display", "block");
                    } else if (dataResult['data'] == "ERROR2") {
                        alert("Company Name already exists!! Please Contact Support");
                        $('#company_name_add').css("border", "1px solid red");
                        $('#company_name_add').focus();
                        $("#add_company_leads2").css("display", "block");
                    } else {
                        if (dataResult['data'] != null) {
                            len = dataResult['data'].length;
                        }
                        if (len > 0) {
                            $('#company_name').find('option').not(':first').remove();
                            var newCompanyId = dataResult['new_company_id'];
                            for (var i = 0; i < len; i++) {
                                var id = dataResult['data'][i].id;
                                var name = dataResult['data'][i].customer_name_display;
                                var name2 = dataResult['data'][i].code;
                                var option = "<option value='" + id + "'>" + name + "</option>";
                                $("#company_name").append(option);

                            }

                            if (newCompanyId) {
                                $("#company_name").val(newCompanyId).trigger('change');
                            }



                            $("#loading_bg").hide();
                            $("#addcompany").modal('hide');

                            toastr.success("Customer added successfully", "Success");


                            // $("#add_company_leads").css("display", "block");

                            //location.reload();
                            //$("#company_name").change();
                        }
                    }
                },
                error: function(xhr, status, error) {
                    // Optional: show error message
                    alert("Failed To Add Company: " + error);
                },
                complete: function() {
                    // This runs always, after success or error
                    $("#loading_bg").hide();
                }
            });
        });




        $(document).ready(function() {
            // Trigger change event only if a country is selected by default
            if ($('#country_ship').val() !== '') {
                $('#country_ship').trigger('change');
            }
        });

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
                                        `<span class="badge bg-info">Lead</span>`;
                                } else if (comment.source === 'deal') {
                                    sourceBadge =
                                        `<span class="badge bg-success">Deal</span>`;
                                }


                                var row = `
                                    <tr>
                                        <td>${sourceBadge}</td>
                                        <td>${comment.comments}</td>
                                         
                                        <td>${comment.createdby.first_name || '-'} ${comment.createdby.last_name || '-'}</td>
                                       <td class="text-center">
                                        ${comment.source === 'lead' ? `
                                                            ${comment.commentsdoc ? ` <a class=" p-0"
                                    href="{{ asset('public/uploads/crm_lead_doc/') }}/${comment.commentsdoc}"
                                    target="_blank"><i class="ico icon-bold-paperclip text-success" style="font-size:16px"
                                        aria-hidden="true"></i></a>` : ''}
                                                            ` : `
                                                            ${comment.commentsdoc ? ` <a class=" p-0"
                                    href="{{ asset('public/uploads/crm_deal_doc/') }}/${comment.commentsdoc}"
                                    target="_blank"><i class="ico icon-bold-paperclip text-success" style="font-size:16px"
                                        aria-hidden="true"></i></a>` : ''}
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
            var fileInput = $("#commentsdoc")[0].files[0];


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

                    // Clear both textarea and file input
                    $('#newComment').val('');
                    $('#commentsdoc').val(''); // This clears file input


                    $('#commentsModalBody').append(
                        ` <tr>
                                        <td>
                                            <span class="badge bg-info">Lead</span>
                                        </td>
                                        <td>${newComment.comments}</td>
                                        <td>${newComment.createdby.first_name || '-'} ${newComment.createdby.last_name || '-'}</td>
                                        <td class="text-center">
                                        ${newComment.commentsdoc ? ` <a class=" p-0"
                                                                href="{{ asset('public/uploads/crm_lead_doc/') }}/${ newComment.commentsdoc }"
                                                                target="_blank"><i class="ico icon-bold-paperclip" style="font-size:16px"
                                                                    aria-hidden="true"></i></a>` : '' }
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

  

    {{-- ===================== Leads Export to Excel ===================== --}}
    <script>
    $(document).ready(function () {
        $('#exportExcelLeads').on('click', function (e) {
            e.preventDefault();

            var companyName = @json(@App\SysCompany::find(session('logged_session_data.company_id') ?? '')->trade_name ?? '');
            var dateFrom    = @json($ctrl_date  ?? '');
            var dateTo      = @json($ctrl_date2 ?? '');
            var totalLeads  = @json($leads->count() ?? 0);

            // ── 1. Collect visible column indices from #long-list thead ──
            var $table = $('#long-list');
            var visibleColIndexes = [];
            var headerLabels = [];
            $table.find('thead tr th').each(function (i) {
                if ($(this).css('display') !== 'none') {
                    var label = $(this).text().trim();
                    // Skip the last "Action" column
                    if (label.toLowerCase() !== 'action') {
                        visibleColIndexes.push(i);
                        headerLabels.push(label);
                    }
                }
            });

            // ── 2. Build rows array for SheetJS ──
            var rows = [];

            // Row 1 – Company name
            rows.push([companyName]);

            // Row 2 – Report title + total count
            rows.push(['Leads (' + totalLeads + ')']);

            function formatDMY(value) {
                if (!value) return '-';
                // normalize from possible slash or dash variations
                var normalized = value.trim().replace(/\s+/g, '');
                var parts = normalized.split(/[\/\-\.]/);
                if (parts.length === 3) {
                    // assume d/m/Y or Y/m/d fallback
                    if (parts[0].length === 4) {
                        // Y/m/d => convert to d/m/Y
                        return parts[2] + '/' + parts[1] + '/' + parts[0];
                    }
                    return parts[0] + '/' + parts[1] + '/' + parts[2];
                }
                return value;
            }

            // Row 3 – Date range (only if from/to are explicitly set)
            if (dateFrom || dateTo) {
                var parts = [];
                if (dateFrom) { parts.push('From: ' + formatDMY(dateFrom)); }
                if (dateTo) { parts.push('To: ' + formatDMY(dateTo)); }
                rows.push([parts.join('  ')]);
            }

            // Blank separator row
            rows.push([]);

            // Header row
            rows.push(headerLabels);

            // Data rows – only visible cells, excluding action column
            $table.find('tbody tr').each(function () {
                var $cells = $(this).find('td');
                var rowData = [];
                visibleColIndexes.forEach(function (ci) {
                    var $cell = $cells.eq(ci);
                    rowData.push($cell.text().trim().replace(/\s+/g, ' '));
                });
                rows.push(rowData);
            });

            if (rows.length <= 5) {
                alert('No data available for export');
                return;
            }

            // ── 3. Build workbook and download ──
            var N = headerLabels.length || 1;
            var workbook  = new ExcelJS.Workbook();
            var worksheet = workbook.addWorksheet('Leads');
            var wsCols = [];
            for (var ci = 0; ci < N; ci++) { wsCols.push({ width: 22 }); }
            worksheet.columns = wsCols;

            var hdrIdx = rows.indexOf(headerLabels);
            if (hdrIdx < 0) hdrIdx = rows.length - 1;

            // Meta rows (company name, page title, optional date rows)
            var wsRowNum = 0;
            for (var ri = 0; ri < hdrIdx; ri++) {
                if (!(rows[ri] && rows[ri][0])) continue; // skip blank separators
                wsRowNum++;
                var wsRow = worksheet.addRow([]);
                wsRow.height = ri === 0 ? 26 : ri === 1 ? 20 : 16;
                if (N > 1) worksheet.mergeCells(wsRowNum, 1, wsRowNum, N);
                wsRow.getCell(1).value = rows[ri][0] || '';
                if (ri === 0) wsRow.getCell(1).font = { bold: true, size: 14 };
                else if (ri === 1) wsRow.getCell(1).font = { bold: true, size: 12 };
                wsRow.getCell(1).alignment = { horizontal: 'center', vertical: 'middle' };
            }

            // Blank separator
            wsRowNum++;
            worksheet.addRow([]);

            // Column header row
            wsRowNum++;
            var wsHdrRow = worksheet.addRow(headerLabels);
            wsHdrRow.height = 20;
            wsHdrRow.eachCell({ includeEmpty: true }, function (cell) {
                cell.font      = { bold: true, color: { argb: 'FFFFFFFF' }, size: 11 };
                cell.fill      = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF2D5496' } };
                cell.alignment = { horizontal: 'center', vertical: 'middle' };
                cell.border    = {
                    top:    { style: 'thin', color: { argb: 'FFB8C4D8' } },
                    left:   { style: 'thin', color: { argb: 'FFB8C4D8' } },
                    bottom: { style: 'thin', color: { argb: 'FFB8C4D8' } },
                    right:  { style: 'thin', color: { argb: 'FFB8C4D8' } }
                };
            });

            // Data rows
            for (var di = hdrIdx + 1; di < rows.length; di++) {
                var wsDataRow = worksheet.addRow(rows[di]);
                wsDataRow.eachCell({ includeEmpty: true }, function (cell) {
                    cell.border = {
                        top:    { style: 'thin', color: { argb: 'FFCCCCCC' } },
                        left:   { style: 'thin', color: { argb: 'FFCCCCCC' } },
                        bottom: { style: 'thin', color: { argb: 'FFCCCCCC' } },
                        right:  { style: 'thin', color: { argb: 'FFCCCCCC' } }
                    };
                });
            }

            workbook.xlsx.writeBuffer().then(function (buffer) {
                var blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                function pad(n) { return n < 10 ? '0' + n : n; }
                var d = new Date();
                var filename = 'leads_' + pad(d.getDate()) + '-' + pad(d.getMonth() + 1) + '-' + d.getFullYear() + '.xlsx';
                saveAs(blob, filename);
            });
        });
    });
    </script>
    {{-- ===================== End Leads Export ===================== --}}

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- 
    <script>
        // Status Distribution
        new Chart(document.getElementById("statusPie"), {
            type: 'doughnut',
            data: {
                labels: ["New", "Pending", "Qualified", "Unqualified", "Closed"],
                datasets: [{
                    data: [
                        {{ $lead_stats['statusCounts'][1] ?? 0 }},
                        {{ $lead_stats['statusCounts'][4] ?? 0 }},
                        {{ ($lead_stats['statusCounts'][2] ?? 0) + ($lead_stats['statusCounts'][0] ?? 0) }},
                        {{ $lead_stats['statusCounts'][3] ?? 0 }},
                        {{ $lead_stats['statusCounts'][10] ?? 0 }}
                    ],
                    backgroundColor: ["#36A2EB", "#FFCE56", "#4BC0C0", "#FF6384", "#9966FF"]
                }]
            },
            options: {
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            generateLabels: function(chart) {
                                // get the default labels first
                                const original = Chart.overrides.doughnut.plugins.legend.labels.generateLabels(
                                    chart);

                                // now modify the text for each label
                                original.forEach((item, i) => {
                                    let value = chart.data.datasets[0].data[i];
                                    item.text = `${chart.data.labels[i]} (${value})`;
                                });

                                return original;
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => {
                                let total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                let val = ctx.raw;
                                let perc = ((val / total) * 100).toFixed(1);
                                return `${ctx.label}: ${val} (${perc}%)`;
                            }
                        }
                    }
                }
            }
        });


        // Conversion Funnel
        new Chart(document.getElementById("funnelBar"), {
            type: 'bar',
            data: {
                labels: ["New", "Qualified", "Won"],
                datasets: [{
                    data: [
                        {{ $lead_stats['statusCounts'][1] ?? 0 }},
                        {{ ($lead_stats['statusCounts'][2] ?? 0) + ($lead_stats['statusCounts'][0] ?? 0) }},
                        {{ $lead_stats['deals_statusCounts'][4] ?? 0 }}
                    ],
                    backgroundColor: ["#36A2EB", "#4BC0C0", "#2ecc71"]
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script> --}}

    <script>
        // Status Distribution
        new Chart(document.getElementById("statusPie"), {
            type: 'doughnut',
            data: {
                labels: ["New", "Pending", "Qualified", "Unqualified", "Closed"],
                datasets: [{
                    data: [
                        {{ $lead_stats['statusCounts'][1] ?? 0 }},
                        {{ $lead_stats['statusCounts'][4] ?? 0 }},
                        {{ ($lead_stats['statusCounts'][2] ?? 0) + ($lead_stats['statusCounts'][0] ?? 0) }},
                        {{ $lead_stats['statusCounts'][3] ?? 0 }},
                        {{ $lead_stats['statusCounts'][10] ?? 0 }}
                    ],
                    backgroundColor: [
                        "#4F8EF7", // New → Fresh blue
                        "#F5A623", // Pending → Vibrant amber
                        "#499258", // Qualified → Modern green
                        "#E74C3C", // Unqualified → Elegant red
                        "#7F8C8D" // Closed → Neutral slate gray
                    ]
                }]
            },
            options: {
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            generateLabels: function(chart) {
                                // get the default labels first
                                const original = Chart.overrides.doughnut.plugins.legend.labels.generateLabels(
                                    chart);

                                // now modify the text for each label
                                original.forEach((item, i) => {
                                    let value = chart.data.datasets[0].data[i];
                                    item.text = `${chart.data.labels[i]} (${value})`;
                                });

                                return original;
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => {
                                let total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                let val = ctx.raw;
                                let perc = ((val / total) * 100).toFixed(1);
                                return `${ctx.label}: ${val} (${perc}%)`;
                            }
                        }
                    }
                }
            }
        });


        // Conversion Funnel
        new Chart(document.getElementById("funnelBar"), {
            type: 'bar',
            data: {
                labels: ["New", "Qualified", "Won"],
                datasets: [{
                    data: [
                        {{ $lead_stats['statusCounts'][1] ?? 0 }},
                        {{ ($lead_stats['statusCounts'][2] ?? 0) + ($lead_stats['statusCounts'][0] ?? 0) }},
                        {{ $lead_stats['deals_statusCounts'][4] ?? 0 }}
                    ],
                    backgroundColor: ["#36A2EB", "#1B7A3F", "#B7950B"]
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            $('.collapse').on('show.bs.collapse', function() {
                $(this).closest('.task-card, .task-card-count').find('.toggle-icon')
                    .removeClass('icon-outline-alt-arrow-down')
                    .addClass('icon-outline-alt-arrow-up');
            });

            $('.collapse').on('hide.bs.collapse', function() {
                $(this).closest('.task-card, .task-card-count').find('.toggle-icon')
                    .removeClass('icon-outline-alt-arrow-up')
                    .addClass('icon-outline-alt-arrow-down');
            });
        });
    </script>

    <script>
        $(document).ready(function() {

            // Clear search input on ESC key press
            $('#search_lead').on('keydown', function(e) {
                if (e.key === 'Escape' || e.keyCode === 27) {
                    $(this).val('');
                    $(this).trigger('input'); // Trigger the search to reset results
                }
            });

            $('#search_lead').on('input', function() {
                var query = $(this).val();

                $.ajax({
                    url: "{{ route('leads.search') }}",
                    type: "GET",
                    data: {
                        query: query
                    },
                    success: function(data) {
                        $('#short-list').html('');

                        console.log("here")
                        console.log(data);

                        if (data.length > 0) {
                            $.each(data, function(index, lead) {

                                let ims = ` <li class="nav-item w-100" role="presentation">
                            <button class="nav-link lead-item"
                                data-id="${lead.id}" id="purchase-order-1-tab" data-bs-toggle="tab"
                                data-bs-target="#purchase-order-1" type="button" role="tab"
                                aria-controls="purchase-order-1" aria-selected="true">
                                <div class="row w-100">

                                    <div class="col-12">
                                        <label class="form-control-plaintext truncate-text">
                                             ${lead.customername.name || ''}</label>
                                    </div>
                                     
                                    <div class="col-4">

                                        <div class="form-control-plaintext" style="font-size: 11px">${lead.code}</div>

                                    </div>
                                    <div class="col-4 text-center">
                                        <div class="form-control-plaintext" style="font-size: 11px">
                                              ${get_format_date(lead.created_at)}</div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                                       ${lead.lead_deal_code?.code || ''}

                                        </div>
                                    </div>

                                      
                                 
                                </div>
                            </button>
                        </li>`;


                                $('#short-list').append(ims);
                            });
                        } else {
                            $('#short-list').html('<div class="p-2">No results found</div>');
                        }
                    }
                });
            });

        });
    </script>
    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@endsection

@extends('backEnd.newmasterpage')
@section('mainContent')
    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>

    <style>
        .badge-danger {
            background-color: #f8d7da;
            color: black
        }

        .badge-primary {
            background-color: #cce5ff;
            color: black
        }
    </style>

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


    <script>
        //added ny kp
        function toggleLongFilters() {
            console.log("clicked");
            document.querySelectorAll('#filters-long .filter-field').forEach(el => {
                el.classList.toggle('d-none');
            });
        }
    </script>
    <?php try { ?>

    <aside class="left-nav col-12" id="leftSidebar">


        <div class="long-list" id="filters-long">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="mb-0"> Deal Track Status List
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
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-status', 'method' => 'get', 'id' => 'crm-deal-track-status']) }}

                        <div class="row">

                            <div class="col-1-5">
                                <label for="" class="form-label">Deal ID</label>
                                <input class="form-control" id="deal_id" type="text" autocomplete="off" name="deal_id"
                                    value="{{ $ctrl_deal_id }}">
                            </div>

                                     @if (session('logged_session_data.company_id') == 1)
                            <div class="col-1-5">
                                <label for="" class="form-label">Company Name</label>
                                <select class="form-control" name="company_id2" id="company_id2_dropdown">
                                    <option value="">-Select-</option>

                                    @if (@isset($company_list))
                                        @foreach ($company_list as $co)
                                            <option value="{{ $co->id }}"
                                                @if (request('company_id2') == $co->id) selected @endif>
                                                {{ $co->company_name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            @endif

                            <div class="col-1-5">
                                <label for="" class="form-label">Customer Name</label>
                                <select class="form-control js-example-basic-single" name="company_id" id="company_id">
                                    <option value="">-Select-</option>
                                    @foreach ($vendors as $value)
                                        <option value="{{ @$value->id }}"
                                            @if ($ctrl_company_id == $value->id) selected @endif>
                                            {{ @$value->name }}</option>
                                    @endforeach
                                </select>
                            </div>


                            @if (Auth::user()->role_id == 1 || Auth::user()->id == 49)
                                <div class="col-1-5">
                                    <label for="" class="form-label">Sales Person</label>
                                    <select class="form-control js-example-basic-single" name="owner_id" id="owner_id">
                                        <option value="">-Select-</option>
                                        @foreach ($staff as $value)
                                            <option value="{{ @$value->user_id }}"
                                                @if ($ctrl_owner_id == $value->user_id) selected @endif>{{ @$value->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif


                              <div class="col-1-5">
                                <label for="" class="form-label">Status</label>
                                <select class="form-control js-example-basic-single" name="status_id" id="status_id">
                                    <option value="10" @if ($ctrl_status_id == '10') selected @endif>-Select-
                                    </option>

                                    @if (session('logged_session_data.designation_id') == 8 || Auth::user()->role_id == 1)
                                        <option value="A1">Accounts Approved</option>
                                        <option value="A2">Accounts Rejected</option>
                                        <option value="A3">Accounts Pending</option>
                                        <option value="A4">Accounts New</option>
                                    @endif

                                    @if (session('logged_session_data.designation_id') == 27 || Auth::user()->role_id == 1)
                                        <option value="S1">Sales Approved</option>
                                        <option value="S2">Sales Rejected</option>
                                        <option value="S3">Sales Pending</option>
                                        <option value="S4">Sales New</option>
                                    @endif

                                    @if (session('logged_session_data.designation_id') == 20 || Auth::user()->role_id == 1)
                                        <option value="P1">Purchase Approved</option>
                                        <option value="P2">Purchase Rejected</option>
                                        <option value="P3">Purchase Pending</option>
                                        <option value="P4">Purchase Partial Delivery</option>
                                        <option value="P" @if ($ctrl_not_applicable == 'P') selected @endif>Purchase
                                            Not
                                            Applicable</option>
                                        <option value="P5">Purchase New</option>
                                    @endif

                                    @if (session('logged_session_data.designation_id') == 35 || Auth::user()->role_id == 1)
                                        <option value="I1">Invoice Approved</option>
                                        <option value="I2">Invoice Rejected</option>
                                        <option value="I3">Invoice Pending</option>
                                        <option value="I" @if ($ctrl_not_applicable == 'I') selected @endif>Invoice Not
                                            Applicable</option>
                                        <option value="I4">Invoice New</option>
                                    @endif

                                    @if (session('logged_session_data.designation_id') == 34 || Auth::user()->role_id == 1)
                                        <option value="D1">Delivery Completed</option>
                                        <option value="D2">Delivery Rejected</option>
                                        <option value="D3">Out For Delivery</option>
                                        <option value="D4">Pending For Delivery</option>
                                        <option value="D5">Ready For Delivery</option>
                                        <option value="D" @if ($ctrl_not_applicable == 'D') selected @endif>Delivery
                                            Not
                                            Applicable</option>
                                        <option value="D7">Delivery New</option>
                                    @endif

                                    @if (session('logged_session_data.designation_id') == 2 || Auth::user()->role_id == 1)
                                        <option value="R1">Payment Received</option>
                                        <option value="R2">Receivables Rejected</option>
                                        <option value="R3">Payment Pending</option>
                                        <option value="R4">Order Cancelled</option>
                                        <option value="R" @if ($ctrl_not_applicable == 'R') selected @endif>
                                            Receivables Not
                                            Applicable</option>
                                        <option value="R7">Receivables New</option>
                                    @endif

                                    <option value="PD1" @if ($ctrl_partial_delivery == 1) selected @endif>Partial
                                        Delivery
                                    </option>


                                </select>
                            </div>

                          
                            



                            <div class="col-1 mb-2 filter-field d-none">
                                <label for="" class="form-label">Form Date</label>
                                <input class="form-control date-picker" id="date" type="text" autocomplete="off"
                                    name="date_from"
                                    value="{{ $ctrl_date_from ? @App\SysHelper::normalizeToDmy($ctrl_date_from) : '' }}">
                            </div>

                            <div class="col-1 mb-2 filter-field d-none">
                                <label for="" class="form-label">To Date</label>
                                <input class="form-control date-picker" id="date" type="text" autocomplete="off"
                                    name="date_to"
                                    value="{{ $ctrl_date_to ? @App\SysHelper::normalizeToDmy($ctrl_date_to) : '' }}">
                            </div>


                            <div class="col-1">
                                <label for="" class="form-label">Filter By</label>
                                <select class="form-control" name="filter_by" id="filter_by"
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





                            <input type="hidden" name="company_id2" id="hidden_company_id2"
                                value="{{ request('company_id2') }}">

                            <div class="col-1-5 filter-field d-none">
                               <button type="submit" class="btn btn-light add-btn mt-4" id="btnSubmit">
                                            <i class="ico icon-outline-minimalistic-magnifer text-success"></i> Filter
                                        </button>
                            </div>

                        </div>
                        {{ Form::close() }}


                    </div>
                </div>
            </div>
        </div>

        <div class="left-nav-list">

            <div class="row task-row mt-3">
                <div class="cls-m-12 col-md-6 col-lg-3 col-xl-2">
                    <div class="task-card ">
                        <div class="filter-by-status" data-status-id="1" style="cursor:pointer;">
                            <i class="fa fa-briefcase task-icon"></i>
                            <h5 class="task-title ">Accounts</h5>
                            <div id="totalTasks" class="task-count">

                            </div>
                        </div>

                        <div class="sub-status collapse" id="accountCollapse">
                            <div class="sub-status-div" data-sub-status-id="A4">
                                <span class="sub-status-title">New</span>
                                <span class="sub-status-value">{{ $deal_stats['account_new'] ?? 0 }}</span>
                            </div>
                            <div class="sub-status-div" data-sub-status-id="A3">
                                <span class="sub-status-title">Pending</span>
                                <span class="sub-status-value">{{ $deal_stats['account_pending'] ?? 0 }}</span>
                            </div>
                            <div class="sub-status-div" data-sub-status-id="A1">
                                <span class="sub-status-title">Approved</span>
                                <span class="sub-status-value">{{ $deal_stats['account_approved'] ?? 0 }}</span>
                            </div>
                            <div class="sub-status-div" data-sub-status-id="A2">
                                <span class="sub-status-title">Rejected</span>
                                <span class="sub-status-value">{{ $deal_stats['account_rejected'] ?? 0 }}</span>
                            </div>
                            <div class="sub-status-div">
                                <span class="sub-status-title">Avg. Time</span>
                                <span class="sub-status-value">{{ $deal_stats_avg['account_approval_time'] ?? 0 }}</span>
                            </div>
                        </div>

                        <div class="task-toggle-indicator" data-bs-toggle="collapse" href="#accountCollapse"
                            role="button" aria-expanded="false" aria-controls="accountCollapse">
                            <i class="fa ico icon-outline-alt-arrow-down toggle-icon" id="icon-new"></i>
                        </div>
                    </div>
                </div>

                <div class="cls-m-12 col-md-6 col-lg-3 col-xl-2">
                    <div class="task-card ">
                        <div class="filter-by-status" data-status-id="1" style="cursor:pointer;">
                            <i class="fa fa-chart-line task-icon"></i>
                            <h5 class="task-title ">Sales</h5>
                            <div id="totalTasks" class="task-count">

                            </div>
                        </div>
                        <div class="sub-status collapse" id="salesCollapse">
                            <div class="sub-status-div" data-sub-status-id="S4">
                                <span class="sub-status-title">New</span>
                                <span class="sub-status-value">{{ $deal_stats['sales_new'] ?? 0 }}</span>
                            </div>
                            <div class="sub-status-div" data-sub-status-id="S3">
                                <span class="sub-status-title">Pending</span>
                                <span class="sub-status-value">{{ $deal_stats['sales_pending'] ?? 0 }}</span>
                            </div>
                            <div class="sub-status-div" data-sub-status-id="S1">
                                <span class="sub-status-title">Approved</span>
                                <span class="sub-status-value">{{ $deal_stats['sales_approved'] ?? 0 }}</span>
                            </div>
                            <div class="sub-status-div" data-sub-status-id="S2">
                                <span class="sub-status-title">Rejected</span>
                                <span class="sub-status-value">{{ $deal_stats['sales_rejected'] ?? 0 }}</span>
                            </div>
                            <div class="sub-status-div">
                                <span class="sub-status-title">Avg. Time</span>
                                <span class="sub-status-value">{{ $deal_stats_avg['sales_approval_time'] ?? 0 }}</span>
                            </div>
                        </div>

                        <div class="task-toggle-indicator" data-bs-toggle="collapse" href="#salesCollapse"
                            role="button" aria-expanded="false" aria-controls="salesCollapse">
                            <i class="fa ico icon-outline-alt-arrow-down toggle-icon" id="icon-new"></i>
                        </div>
                    </div>
                </div>

                <div class="cls-m-12 col-md-6 col-lg-3 col-xl-2">
                    <div class="task-card ">
                        <div class="filter-by-status" data-status-id="1" style="cursor:pointer;">
                            <i class="fa fa-shopping-cart task-icon"></i>
                            <h5 class="task-title ">Purchase</h5>
                            <div id="totalTasks" class="task-count">

                            </div>
                        </div>


                        <div class="sub-status collapse" id="purchaseCollapse">
                            <div class="sub-status-div" data-sub-status-id="P5">
                                <span class="sub-status-title">New</span>
                                <span class="sub-status-value">{{ $deal_stats['purchease_new'] ?? 0 }}</span>
                            </div>
                            <div class="sub-status-div" data-sub-status-id="P3">
                                <span class="sub-status-title">Pending</span>
                                <span class="sub-status-value">{{ $deal_stats['purchease_pending'] ?? 0 }}</span>
                            </div>
                            <div class="sub-status-div" data-sub-status-id="P1">
                                <span class="sub-status-title">Approved</span>
                                <span class="sub-status-value">{{ $deal_stats['purchease_approved'] ?? 0 }}</span>
                            </div>
                            <div class="sub-status-div" data-sub-status-id="P2">
                                <span class="sub-status-title">Rejected</span>
                                <span class="sub-status-value">{{ $deal_stats['purchease_rejected'] ?? 0 }}</span>
                            </div>
                            <div class="sub-status-div">
                                <span class="sub-status-title">Avg. Time</span>
                                <span class="sub-status-value">{{ $deal_stats_avg['purchase_approval_time'] ?? 0 }}</span>
                            </div>
                        </div>

                        <div class="task-toggle-indicator" data-bs-toggle="collapse" href="#purchaseCollapse"
                            role="button" aria-expanded="false" aria-controls="purchaseCollapse">
                            <i class="fa ico icon-outline-alt-arrow-down toggle-icon" id="icon-new"></i>
                        </div>
                    </div>
                </div>

                <div class="cls-m-12 col-md-6 col-lg-3 col-xl-2">
                    <div class="task-card ">
                        <div class="filter-by-status" data-status-id="1" style="cursor:pointer;">
                            <i class="fa fa-file-invoice task-icon"></i>
                            <h5 class="task-title ">Invoice</h5>
                            <div id="totalTasks" class="task-count">

                            </div>
                        </div>

                        <div class="sub-status collapse" id="invoiceCollapse">
                            <div class="sub-status-div" data-sub-status-id="I4">
                                <span class="sub-status-title">New</span>
                                <span class="sub-status-value">{{ $deal_stats['invoice_new'] ?? 0 }}</span>
                            </div>
                            <div class="sub-status-div" data-sub-status-id="I3">
                                <span class="sub-status-title">Pending</span>
                                <span class="sub-status-value">{{ $deal_stats['invoice_pending'] ?? 0 }}</span>
                            </div>
                            <div class="sub-status-div" data-sub-status-id="I1">
                                <span class="sub-status-title">Approved</span>
                                <span class="sub-status-value">{{ $deal_stats['invoice_approved'] ?? 0 }}</span>
                            </div>
                            <div class="sub-status-div" data-sub-status-id="I2">
                                <span class="sub-status-title">Rejected</span>
                                <span class="sub-status-value">{{ $deal_stats['invoice_rejected'] ?? 0 }}</span>
                            </div>
                            <div class="sub-status-div">
                                <span class="sub-status-title">Avg. Time</span>
                                <span class="sub-status-value">{{ $deal_stats_avg['invoice_approval_time'] ?? 0 }}</span>
                            </div>
                        </div>

                        <div class="task-toggle-indicator" data-bs-toggle="collapse" href="#invoiceCollapse"
                            role="button" aria-expanded="false" aria-controls="invoiceCollapse">
                            <i class="fa ico icon-outline-alt-arrow-down toggle-icon" id="icon-new"></i>
                        </div>
                    </div>
                </div>


                <div class="cls-m-12 col-md-6 col-lg-3 col-xl-2">
                    <div class="task-card ">
                        <div class="filter-by-status" data-status-id="1" style="cursor:pointer;">
                            <i class="fa fa-truck task-icon"></i>
                            <h5 class="task-title ">Delivery</h5>
                            <div id="totalTasks" class="task-count">

                            </div>
                        </div>


                        <div class="sub-status collapse" id="deliveryCollapse">
                            <div class="sub-status-div" data-sub-status-id="D7">
                                <span class="sub-status-title">New</span>
                                <span class="sub-status-value">{{ $deal_stats['delivery_new'] ?? 0 }}</span>
                            </div>
                            <div class="sub-status-div" data-sub-status-id="D4">
                                <span class="sub-status-title">Pending</span>
                                <span class="sub-status-value">{{ $deal_stats['delivery_pending'] ?? 0 }}</span>
                            </div>
                            <div class="sub-status-div" data-sub-status-id="D1">
                                <span class="sub-status-title">Completed</span>
                                <span class="sub-status-value">{{ $deal_stats['delivery_completed'] ?? 0 }}</span>
                            </div>
                            <div class="sub-status-div" data-sub-status-id="D2">
                                <span class="sub-status-title">Rejected</span>
                                <span class="sub-status-value">{{ $deal_stats['delivery_rejected'] ?? 0 }}</span>
                            </div>
                            <div class="sub-status-div" data-sub-status-id="D3">
                                <span class="sub-status-title">Out For Delivery</span>
                                <span class="sub-status-value">{{ $deal_stats['out_for_delivery'] ?? 0 }}</span>
                            </div>

                            <div class="sub-status-div" data-sub-status-id="D5">
                                <span class="sub-status-title">Ready For Delivery</span>
                                <span class="sub-status-value">{{ $deal_stats['ready_for_delivery'] ?? 0 }}</span>
                            </div>
                            {{-- <div class="sub-status-div">
                            <span class="sub-status-title">Partial Delivery</span>
                            <span class="sub-status-value">{{ $deal_stats['partial_delivery'] ?? 0 }}</span>
                        </div> --}}
                            <div class="sub-status-div">
                                <span class="sub-status-title">Avg. Time</span>
                                <span class="sub-status-value">{{ $deal_stats_avg['delivery_approval_time'] ?? 0 }}</span>
                            </div>
                        </div>

                        <div class="task-toggle-indicator" data-bs-toggle="collapse" href="#deliveryCollapse"
                            role="button" aria-expanded="false" aria-controls="deliveryCollapse">
                            <i class="fa ico icon-outline-alt-arrow-down toggle-icon" id="icon-new"></i>
                        </div>
                    </div>
                </div>

                <div class="cls-m-12 col-md-6 col-lg-3 col-xl-2">
                    <div class="task-card ">
                        <div class="filter-by-status" data-status-id="1" style="cursor:pointer;">
                            <i class="fa fa-truck task-icon"></i>
                            <h5 class="task-title ">Receivables</h5>
                            <div id="totalTasks" class="task-count">

                            </div>
                        </div>
                        <div class="sub-status collapse" id="receivableCollapse">
                            <div class="sub-status-div" data-sub-status-id="R7">
                                <span class="sub-status-title">New</span>
                                <span class="sub-status-value">{{ $deal_stats['receivables_new'] ?? 0 }}</span>
                            </div>
                            <div class="sub-status-div" data-sub-status-id="R3">
                                <span class="sub-status-title">Payment Pending</span>
                                <span class="sub-status-value">{{ $deal_stats['payment_pending'] ?? 0 }}</span>
                            </div>
                            <div class="sub-status-div" data-sub-status-id="R1">
                                <span class="sub-status-title">Payment Received</span>
                                <span class="sub-status-value">{{ $deal_stats['payment_received'] ?? 0 }}</span>
                            </div>
                            <div class="sub-status-div" data-sub-status-id="R2">
                                <span class="sub-status-title">Payment Rejected</span>
                                <span class="sub-status-value">{{ $deal_stats['payment_rejected'] ?? 0 }}</span>
                            </div>
                            <div class="sub-status-div" data-sub-status-id="R4">
                                <span class="sub-status-title">Order Cancelled</span>
                                <span class="sub-status-value">{{ $deal_stats['orders_cancelled'] ?? 0 }}</span>
                            </div>
                            <div class="sub-status-div">
                                <span class="sub-status-title">Avg. Time</span>
                                <span
                                    class="sub-status-value">{{ $deal_stats_avg['receivables_approval_time'] ?? 0 }}</span>
                            </div>
                        </div>

                        <div class="task-toggle-indicator" data-bs-toggle="collapse" href="#receivableCollapse"
                            role="button" aria-expanded="false" aria-controls="receivableCollapse">
                            <i class="fa ico icon-outline-alt-arrow-down toggle-icon" id="icon-new"></i>
                        </div>
                    </div>
                </div>

            </div>



            <div class="table-responsive mb-4 mt-2">
                <table id="long-list" class="table table-hover" style="table-layout: fixed;width:100%">

                    <thead>
                        <tr>
                            <th style="width:80px">@lang('Deal')</th>
                            @if (session('logged_session_data.company_id') == 1)
                                <th>@lang('Company')</th>
                            @endif
                            <th style="width:250px" class="mobhd">@lang('Deal Name')</th>
                            <th class="mobhd">@lang('Sales Person')</th>
                            {{-- <th class="mobhd">@lang('Payment Terms')</th> --}}
                            <th class="text-end">@lang('Value')</th>

                            <th>@lang('Accounts')</th>
                            <th>@lang('Sales')</th>
                            <th>@lang('Purchase')</th>
                            <th>@lang('Invoice')</th>
                            <th>@lang('Delivery')</th>
                            <th>@lang('Receivables')</th>


                        </tr>
                    </thead>



                    <tbody>

                        @php $count =1; @endphp
                        @foreach ($dealtrack as $value)
                            <tr @if ($value->deal_stage == 6) class="bg-dark" @endif>
                                <td><a
                                        href="{{ url('crm-deal-track-approval/' . $value->id) }}">{{ @$value->deal_code->code }}</a>
                                </td>
                                @if (session('logged_session_data.company_id') == 1)
                                    <td>{{ $value->companyname->company_name }}</td>
                                @endif
                                <td class="mobhd">
                                    {{ @$value->dealid->deal_name }}

                                    {{ @$value->customername->name }}

                                </td>
                                <td class="mobhd">
                                    {{ @$value->ownername->full_name }}


                                    @if (!empty($value->created_date) && $value->created_date != '1970-01-01')
                                        {{ \Carbon\Carbon::parse($value->created_date)->format('d/m/Y') }}
                                        {{ \Carbon\Carbon::parse($value->created_date)->format('h:i A') }}
                                    @endif
                                </td>

                                <td class="mobhd text-end">
                                    @php
                                        $aed = @App\SysHelper::get_aed_amount(
                                            $value->deal_currency,
                                            $value->deal_value
                                        );
                                    @endphp
                                    {{ @App\SysHelper::com_curr_format($aed, 2, '.', ',') }} {{@$value->deal_code->dealcurrency->code}}
                                </td>
                                <td>
                                    @if ($value->accounts == 1)
                                        <span class="badge bg-success py-1 px-2">Accounts Approved</span>
                                    @elseif($value->accounts == 2)
                                        <span class="badge badge-danger py-1 px-2">Accounts Rejected</span>
                                    @elseif($value->accounts == 3)
                                        <span class="badge badge-primary py-1 px-2">Accounts Pending</span>
                                    @else
                                        <span class="badge bg-warning py-1 px-2">Pending</span>
                                    @endif



                                    @if ($value->account && $value->account->created_date)
                                        {{ \Carbon\Carbon::parse($value->account->created_date)->format('d/m/Y h:i A') }}

                                        {{ App\SysHelper::humanTimeDiff($value->created_date, $value->account->created_date) }}
                                    @endif

                                </td>
                                <td>
                                    @if ($value->sales == 1)
                                        <span class="badge bg-success py-1 px-2">Sales Approved</span>
                                    @elseif($value->sales == 2)
                                        <span class="badge badge-danger py-1 px-2">Sales Rejected</span>
                                    @elseif($value->sales == 3)
                                        <span class="badge badge-primary py-1 px-2">Sales Pending</span>
                                    @else
                                        <span class="badge bg-warning py-1 px-2">Pending</span>
                                    @endif


                                    @if ($value->salesApproval && $value->salesApproval->created_date)
                                        {{ \Carbon\Carbon::parse(@$value->salesApproval->created_date)->format('d/m/Y h:i
                                                                                                                                                                                                                                                                                                A') }}
                                        @if ($value->account && $value->account->created_date && in_array(@$value->accounts, [1, 2]))
                                            {{ App\SysHelper::humanTimeDiff(@$value->account->created_date, $value->salesApproval->created_date) }}
                                        @endif
                                    @endif

                                </td>
                                <td>
                                    @if ($value->purchease_approval == 0)
                                        <span class="info btn-badge py-1 px-2">Not Applicable</span>
                                    @else
                                        @if ($value->purchease == 1)
                                            <span class="badge bg-success py-1 px-2">Purchase Approved</span>
                                        @elseif($value->purchease == 2)
                                            <span class="badge badge-danger py-1 px-2">Purchase Rejected</span>
                                        @elseif($value->purchease == 3)
                                            <span class="badge badge-primary py-1 px-2">Purchase Pending</span>
                                        @elseif($value->purchease == 4)
                                            <span class="badge badge-primary py-1 px-2">Partial Delivery</span>
                                        @else
                                            <span class="badge bg-warning py-1 px-2">Pending</span>
                                        @endif
                                        @if ($value->purcheaseApproval && $value->purcheaseApproval->created_date)
                                            {{ \Carbon\Carbon::parse($value->purcheaseApproval->created_date)->format('d/m/Y h:i
                                                                                                                                                                                                                                                                                                                        A') }}

                                            @if ($value->salesApproval && $value->salesApproval->created_date && in_array(@$value->sales, [1, 2]))
                                                {{ App\SysHelper::humanTimeDiff(@$value->salesApproval->created_date, $value->purcheaseApproval->created_date) }}
                                            @elseif($value->account && $value->account->created_date && in_array(@$value->accounts, [1, 2]))
                                                {{ App\SysHelper::humanTimeDiff(@$value->account->created_date, $value->purcheaseApproval->created_date) }}
                                            @endif
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if ($value->invoice_approval == 0)
                                        <span class="info btn-badge py-1 px-2">Not Applicable</span>
                                    @else
                                        @if ($value->invoice == 1)
                                            <span class="badge bg-success py-1 px-2">Invoice Approved</span>
                                        @elseif($value->invoice == 2)
                                            <span class="badge badge-danger py-1 px-2">Invoice Rejected</span>
                                        @elseif($value->invoice == 3)
                                            <span class="badge badge-primary py-1 px-2">Invoice Pending</span>
                                        @else
                                            <span class="badge bg-warning py-1 px-2">Pending</span>
                                        @endif

                                        @if ($value->invoiceApproval && $value->invoiceApproval->created_date)
                                            {{ \Carbon\Carbon::parse(@$value->invoiceApproval->created_date)->format('d/m/Y h:i
                                                                                                                                                                                                                                                                                                                        A') }}

                                            @if (
                                                $value->purcheaseApproval &&
                                                    $value->purcheaseApproval->created_date &&
                                                    $value->purchease_approval != 0 &&
                                                    in_array(@$value->purchease, [1, 2, 3, 4]))
                                                {{ App\SysHelper::humanTimeDiff(
                                                    @$value->purcheaseApproval->created_date,
                                                    $value->invoiceApproval->created_date,
                                                ) }}
                                            @elseif($value->salesApproval && $value->salesApproval->created_date && in_array(@$value->sales, [1, 2]))
                                                {{ App\SysHelper::humanTimeDiff(@$value->salesApproval->created_date, $value->invoiceApproval->created_date) }}
                                            @elseif($value->account && $value->account->created_date && in_array(@$value->accounts, [1, 2]))
                                                {{ App\SysHelper::humanTimeDiff(@$value->account->created_date, $value->invoiceApproval->created_date) }}
                                            @endif
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if ($value->delivery_approval == 0)
                                        <span class=" py-1 px-2">Not Applicable</span>
                                    @else
                                        @if ($value->delivery == 1)
                                            <span class="badge bg-success py-1 px-2">Delivery Completed</span>
                                        @elseif($value->delivery == 2)
                                            <span class="badge badge-danger py-1 px-2">Delivery Rejected</span>
                                        @elseif($value->delivery == 3)
                                            <span class="badge badge-primary py-1 px-2">Out For Delivery</span>
                                        @elseif($value->delivery == 4)
                                            <span class="badge badge-primary py-1 px-2">Pending For Delivery</span>
                                        @elseif($value->delivery == 5)
                                            <span class="badge badge-primary py-1 px-2">Ready For Delivery</span>
                                        @elseif ($value->delivery == 6)
                                            <span class="badge badge-primary py-1 px-2">Partial Delivery</span>
                                        @else
                                            <span class="badge bg-warning py-1 px-2">Pending</span>
                                        @endif
                                        @if ($value->deliveryApproval && $value->deliveryApproval->created_date)
                                            {{ \Carbon\Carbon::parse($value->deliveryApproval->created_date)->format('d/m/Y h:i
                                                                                                                                                                                                                                                                                                                        A') }}

                                            @if (
                                                $value->invoiceApproval &&
                                                    $value->invoiceApproval->created_date &&
                                                    $value->invoice_approval != 0 &&
                                                    in_array(@$value->invoice, [1, 2]))
                                                {{ App\SysHelper::humanTimeDiff($value->invoiceApproval->created_date, $value->deliveryApproval->created_date) }}
                                            @elseif($value->purcheaseApproval && $value->purcheaseApproval->created_date && in_array(@$value->purchease, [1, 2, 3, 4]))
                                                {{ App\SysHelper::humanTimeDiff(
                                                    $value->purcheaseApproval->created_date,
                                                    $value->deliveryApproval->created_date,
                                                ) }}
                                            @elseif($value->salesApproval && $value->salesApproval->created_date && in_array(@$value->sales, [1, 2]))
                                                {{ App\SysHelper::humanTimeDiff($value->salesApproval->created_date, $value->deliveryApproval->created_date) }}
                                            @elseif($value->account && $value->account->created_date && in_array(@$value->accounts, [1, 2]))
                                                {{ App\SysHelper::humanTimeDiff($value->account->created_date, $value->deliveryApproval->created_date) }}
                                            @endif
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if ($value->receivables_approval == 0)
                                        <span class="info btn-badge py-1 px-2">Not Applicable</span>
                                    @else
                                        @if ($value->receivables == 1)
                                            <span class="badge bg-success py-1 px-2">Payment Received</span>
                                        @elseif($value->receivables == 2)
                                            <span class="badge badge-danger py-1 px-2">Rejected</span>
                                        @elseif($value->receivables == 3)
                                            <span class="badge badge-primary py-1 px-2">Payment Pending</span>
                                        @elseif($value->receivables == 4)
                                            <span class="dark btn-badge py-1 px-2">Order Cancelled</span>
                                        @else
                                            <span class="badge bg-warning py-1 px-2">Pending</span>
                                        @endif

                                        @if ($value->receivablesApproval && $value->receivablesApproval->created_date)
                                            {{ \Carbon\Carbon::parse($value->receivablesApproval->created_date)->format('d/m/Y
                                                                                                                                                                                                                                                                                                                        h:i A') }}


                                            @if (
                                                $value->deliveryApproval &&
                                                    $value->deliveryApproval->created_date &&
                                                    $value->delivery_approval != 0 &&
                                                    in_array($value->delivery, [1, 2, 3, 4, 5, 6]))
                                                {{ App\SysHelper::humanTimeDiff(
                                                    $value->deliveryApproval->created_date,
                                                    $value->receivablesApproval->created_date,
                                                ) }}
                                            @elseif($value->invoiceApproval && $value->invoiceApproval->created_date && in_array(@$value->invoice, [1, 2]))
                                                {{ App\SysHelper::humanTimeDiff(
                                                    $value->invoiceApproval->created_date,
                                                    $value->receivablesApproval->created_date,
                                                ) }}
                                            @elseif($value->purcheaseApproval && $value->purcheaseApproval->created_date && in_array(@$value->purchease, [1, 2, 3, 4]))
                                                {{ App\SysHelper::humanTimeDiff(
                                                    $value->purcheaseApproval->created_date,
                                                    $value->receivablesApproval->created_date,
                                                ) }}
                                            @elseif($value->salesApproval && $value->salesApproval->created_date && in_array(@$value->sales, [1, 2]))
                                                {{ App\SysHelper::humanTimeDiff($value->salesApproval->created_date, $value->receivablesApproval->created_date) }}
                                            @elseif($value->account && $value->account->created_date && in_array(@$value->accounts, [1, 2]))
                                                {{ App\SysHelper::humanTimeDiff($value->account->created_date, $value->receivablesApproval->created_date) }}
                                            @endif
                                        @endif
                                    @endif
                                </td>

                            </tr>
                        @endforeach

                    </tbody>

                    <footer>
                        <tr>
                            <td colspan="10">
                                {{ $dealtrack->appends(request()->input())->links() }}
                            </td>
                        </tr>
                    </footer>



                </table>
            </div>
        </div>
    </aside>








    <script>
        document.getElementById('company_id2_dropdown').addEventListener('change', function() {
            // Copy value into hidden input inside the form
            document.getElementById('hidden_company_id2').value = this.value;

            // Submit the main form
            document.getElementById('crm-deal-track-status').submit();
        });
    </script>

    <script>
        $(document).ready(function() {
            $('.collapse').on('show.bs.collapse', function() {
                $(this).closest('.task-card').find('.toggle-icon')
                    .removeClass('ico icon-outline-alt-arrow-down')
                    .addClass('ico icon-outline-alt-arrow-up');
            });

            $('.collapse').on('hide.bs.collapse', function() {
                $(this).closest('.task-card').find('.toggle-icon')
                    .removeClass('ico icon-outline-alt-arrow-up')
                    .addClass('ico icon-outline-alt-arrow-down');
            });
        });
    </script>


    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@endsection

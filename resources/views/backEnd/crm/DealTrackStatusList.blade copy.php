@extends('backEnd.masterpage')
@section('mainContent')

<?php
$permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
?>
<?php try { ?>

    <style>
        @media screen and (max-width: 480px) {
            .mobhd {
                display: none;
            }
        }
    </style>

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
            /* border-top: 1px dashed rgba(255, 255, 255, 0.4); */
            padding-top: 6px;
        }

        .sub-status-div {
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

        .sub-status-div:hover {
            background-color: rgba(255, 255, 255, 0.15);
            /* Light white-ish hover effect */
        }
    </style>


    <div class="container-fluid">
        <div class="d-sm-flex justify-content-between">
            <div class="mb-3">
                <h2 class="page-heading m-0">Deal Track Status List</h2>
                <span class="page-label">Home - Deal Track Status List</span>
            </div>
            <div>
                <button type="button" class="btn btn-primary" data-toggle="collapse" href="#collapseExample" role="button"
                    aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-filter mr-1"></i>Search</button>

            </div>
        </div>


        <div class="collapse" id="collapseExample">
            <div class="card shadow mb-4 p-4">

                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-status', 'method' =>
                'get', 'id' => 'crm-deal-track-status']) }}
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <label for="" class="form-check-label">Deal ID</label>
                        <input class="form-control" id="deal_id" type="text" autocomplete="off" name="deal_id"
                            value="{{ $ctrl_deal_id }}">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label for="" class="form-check-label">Customer Name</label>
                        <select class="form-control js-example-basic-single" name="company_id" id="company_id">
                            <option value="">-Select-</option>
                            @foreach ($vendors as $value)
                            <option value="{{ @$value->id }}" @if ($ctrl_company_id==$value->id) selected @endif>
                                {{ @$value->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label for="" class="form-check-label">Status</label>
                        <select class="form-control js-example-basic-single" name="status_id" id="status_id">
                            <option value="10" @if ($ctrl_status_id=='10' ) selected @endif>-Select-</option>

                            @if (session('logged_session_data.designation_id') == 8 || Auth::user()->role_id == 1)
                            //Account Status
                            <option value="A1">Accounts Approved</option>
                            <option value="A2">Accounts Rejected</option>
                            <option value="A3">Accounts Pending</option>
                            <option value="A4">Accounts New</option>
                            @endif

                            @if (session('logged_session_data.designation_id') == 27 || Auth::user()->role_id == 1)
                            //Sales Status
                            <option value="S1">Sales Approved</option>
                            <option value="S2">Sales Rejected</option>
                            <option value="S3">Sales Pending</option>
                            <option value="S4">Sales New</option>
                            @endif

                            @if (session('logged_session_data.designation_id') == 20 || Auth::user()->role_id == 1)
                            //Purchase Status
                            <option value="P1">Purchase Approved</option>
                            <option value="P2">Purchase Rejected</option>
                            <option value="P3">Purchase Pending</option>
                            <option value="P4">Purchase Partial Delivery</option>
                            <option value="P" @if ($ctrl_not_applicable=='P' ) selected @endif>Purchase Not
                                Applicable</option>
                            <option value="P5">Purchase New</option>
                            @endif

                            @if (session('logged_session_data.designation_id') == 35 || Auth::user()->role_id == 1)
                            //Invoice Status
                            <option value="I1">Invoice Approved</option>
                            <option value="I2">Invoice Rejected</option>
                            <option value="I3">Invoice Pending</option>
                            <option value="I" @if ($ctrl_not_applicable=='I' ) selected @endif>Invoice Not
                                Applicable</option>
                            <option value="I4">Invoice New</option>
                            @endif

                            @if (session('logged_session_data.designation_id') == 34 || Auth::user()->role_id == 1)
                            //Delivery Status
                            <option value="D1">Delivery Completed</option>
                            <option value="D2">Delivery Rejected</option>
                            <option value="D3">Out For Delivery</option>
                            <option value="D4">Pending For Delivery</option>
                            <option value="D5">Ready For Delivery</option>
                            <option value="D" @if ($ctrl_not_applicable=='D' ) selected @endif>Delivery Not
                                Applicable</option>
                            <option value="D7">Delivery New</option>
                            @endif

                            @if (session('logged_session_data.designation_id') == 2 || Auth::user()->role_id == 1)
                            //Receivables Status
                            <option value="R1">Payment Received</option>
                            <option value="R2">Receivables Rejected</option>
                            <option value="R3">Payment Pending</option>
                            <option value="R4">Order Cancelled</option>
                            <option value="R" @if ($ctrl_not_applicable=='R' ) selected @endif>Receivables Not
                                Applicable</option>
                            <option value="R7">Receivables New</option>
                            @endif

                            <option value="PD1" @if ($ctrl_partial_delivery==1) selected @endif>Partial Delivery
                            </option>


                        </select>
                    </div>

                    {{-- <div class="col-md-4 mb-2">
                        <label for="" class="form-check-label">Date</label>
                        <input class="form-control" id="date" type="date" autocomplete="off" name="date"
                            value="{{ $ctrl_date }}">
                    </div> --}}
                    @if (Auth::user()->role_id == 1 || Auth::user()->id == 49)
                    <div class="col-md-4 mb-2">
                        <label for="" class="form-check-label">Salesman</label>
                        <select class="form-control js-example-basic-single" name="owner_id" id="owner_id">
                            <option value="">-Select-</option>
                            @foreach ($staff as $value)
                            <option value="{{ @$value->user_id }}" @if ($ctrl_owner_id==$value->user_id) selected @endif>{{
                                @$value->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    {{-- <div class="col-md-4 mb-2">
                        <label for="" class="form-check-label">Partial Delivery</label>
                        <select class="form-control js-example-basic-single" name="partial_delivery" id="partial_delivery">
                            <option value="">-Select-</option>
                            <option value="1" @if ($ctrl_partial_delivery==1) selected @endif>Partial Delivery</option>
                        </select>
                    </div> --}}
                    {{-- <div class="col-md-4 mb-2">
                        <label for="" class="form-check-label">Not Applicable</label>
                        <select class="form-control js-example-basic-single" name="not_applicable" id="not_applicable">
                            <option value="">-Select-</option>
                            <option value="P" @if ($ctrl_not_applicable=='P' ) selected @endif>Purchase Not Applicable
                            </option>
                            <option value="I" @if ($ctrl_not_applicable=='I' ) selected @endif>Invoice Not Applicable
                            </option>
                            <option value="D" @if ($ctrl_not_applicable=='D' ) selected @endif>Delivery Not Applicable
                            </option>
                            <option value="R" @if ($ctrl_not_applicable=='R' ) selected @endif>Receivables Not Applicable
                            </option>
                        </select>
                    </div> --}}
                    <div class="col-md-2 mb-2">
                        <label for="" class="form-check-label">Date From</label>
                        <input class="form-control datepicker" id="date_from" type="date" autocomplete="off"
                            name="date_from" value="{{ $ctrl_date_from }}">
                    </div>
                    <div class="col-md-2 mb-2">
                        <label for="" class="form-check-label">Date To</label>
                        <input class="form-control" id="date_to" type="date" autocomplete="off" name="date_to"
                            value="{{ $ctrl_date_to }}">
                    </div>

                    <div class="col-md-2 mb-2">
                        <label for="" class="form-check-label">Filter By</label>
                        <select class="form-control" name="filter_by" id="filter_by" onchange="this.form.submit()">
                            <option value="" @if ($filter_by=='' ) selected @endif>-Select-</option>
                            <option value="today" @if ($filter_by=='today' ) selected @endif>Today</option>
                            <option value="this_week" @if ($filter_by=='this_week' ) selected @endif>This Week
                            </option>
                            <option value="last_week" @if ($filter_by=='last_week' ) selected @endif>Last Week
                            </option>
                            <option value="this_month" @if ($filter_by=='this_month' ) selected @endif>This Month
                            </option>
                            <option value="last_month" @if ($filter_by=='last_month' ) selected @endif>Last Month
                            </option>
                            <option value="last_6_months" @if ($filter_by=='last_6_months' ) selected @endif>Last 6 Months
                            </option>
                            <option value="this_year" @if ($filter_by=='this_year' ) selected @endif>This Year
                            </option>
                            <option value="last_year" @if ($filter_by=='last_year' ) selected @endif>Last Year
                            </option>
                        </select>
                    </div>

                    <input type="hidden" name="company_id2" id="hidden_company_id2" value="{{ request('company_id2') }}">


                    <div class="col-12 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary" id="btnSubmit">Filter</button>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>

        <div class="row task-row">
            <div class="cls-m-12 col-md-6 col-lg-3 col-xl-2">
                <div class="task-card bg-primary text-white">
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

                    <div class="task-toggle-indicator" data-toggle="collapse" href="#accountCollapse" role="button"
                        aria-expanded="false" aria-controls="accountCollapse">
                        <i class="fa fa-chevron-down toggle-icon" id="icon-new"></i>
                    </div>
                </div>
            </div>

            <div class="cls-m-12 col-md-6 col-lg-3 col-xl-2">
                <div class="task-card bg-success text-white">
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

                    <div class="task-toggle-indicator" data-toggle="collapse" href="#salesCollapse" role="button"
                        aria-expanded="false" aria-controls="salesCollapse">
                        <i class="fa fa-chevron-down toggle-icon" id="icon-new"></i>
                    </div>
                </div>
            </div>

            <div class="cls-m-12 col-md-6 col-lg-3 col-xl-2">
                <div class="task-card bg-warning text-dark">
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

                    <div class="task-toggle-indicator" data-toggle="collapse" href="#purchaseCollapse" role="button"
                        aria-expanded="false" aria-controls="purchaseCollapse">
                        <i class="fa fa-chevron-down toggle-icon" id="icon-new"></i>
                    </div>
                </div>
            </div>

            <div class="cls-m-12 col-md-6 col-lg-3 col-xl-2">
                <div class="task-card bg-info text-white">
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

                    <div class="task-toggle-indicator" data-toggle="collapse" href="#invoiceCollapse" role="button"
                        aria-expanded="false" aria-controls="invoiceCollapse">
                        <i class="fa fa-chevron-down toggle-icon" id="icon-new"></i>
                    </div>
                </div>
            </div>


            <div class="cls-m-12 col-md-6 col-lg-3 col-xl-2">
                <div class="task-card bg-secondary text-white">
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

                    <div class="task-toggle-indicator" data-toggle="collapse" href="#deliveryCollapse" role="button"
                        aria-expanded="false" aria-controls="deliveryCollapse">
                        <i class="fa fa-chevron-down toggle-icon" id="icon-new"></i>
                    </div>
                </div>
            </div>

            <div class="cls-m-12 col-md-6 col-lg-3 col-xl-2">
                <div class="task-card bg-danger text-white">
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
                            <span class="sub-status-value">{{ $deal_stats_avg['receivables_approval_time'] ?? 0 }}</span>
                        </div>
                    </div>

                    <div class="task-toggle-indicator" data-toggle="collapse" href="#receivableCollapse" role="button"
                        aria-expanded="false" aria-controls="receivableCollapse">
                        <i class="fa fa-chevron-down toggle-icon" id="icon-new"></i>
                    </div>
                </div>
            </div>

        </div>

        <script>
            $('.sub-status-div').on('click', function () {
                var substatusId = $(this).data('sub-status-id');
                console.log(substatusId)
                $('#status_id').val(substatusId).trigger('change');
                var form = $('#crm-deal-track-status');
                var params = form.serialize();
                var sortParam = '';

                var url = form.attr('action') + '?' + params + '&' + sortParam;
                window.open(url, '_blank');
            });
        </script>


        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">

                    @if (session('logged_session_data.company_id') == 1)
                    <table cellspacing="0" style="float: right;">
                        <tr>
                            <th style="width: 150px;">
                                <select class="form-control" name="company_id2" id="company_id2_dropdown">
                                    <option value="">Select Company</option>
                                    @if (@isset($company_list))
                                    @foreach ($company_list as $co)
                                    <option value="{{ $co->id }}" @if (request('company_id2')==$co->id) selected @endif>
                                        {{ $co->company_name }}
                                    </option>
                                    @endforeach
                                    @endif
                                </select>
                            </th>
                            <th></th>
                        </tr>
                    </table>
                    @endif




                    <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
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
                                <th>@lang('Deal')</th>
                                @if (session('logged_session_data.company_id') == 1)
                                <th style="width: 200px;">@lang('Company')</th>
                                @endif
                                <th class="mobhd">@lang('Deal Name')</th>
                                <th class="mobhd">@lang('Salesman')</th>
                                {{-- <th class="mobhd">@lang('Payment Terms')</th> --}}
                                <th class="text-right">@lang('Value')</th>

                                <th>@lang('Accounts')</th>
                                <th>@lang('Sales')</th>
                                <th>@lang('Purchase')</th>
                                <th>@lang('Invoice')</th>
                                <th>@lang('Delivery')</th>
                                <th>@lang('Receivables')</th>

                                <th></th>
                            </tr>
                        </thead>

                        <tbody>

                            @php $count =1; @endphp
                            @foreach ($dealtrack as $value)
                            <tr @if ($value->deal_stage == 6) class="bg-dark" @endif>
                                <td><a href="{{ url('crm-deal-track-approval/' . $value->id) }}">{{ @$value->deal_code->code
                                        }}</a>
                                </td>
                                @if (session('logged_session_data.company_id') == 1)
                                <td>{{ $value->companyname->company_name }}</td>
                                @endif
                                <td class="mobhd">
                                    <div
                                        style="width:150px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">
                                        {{ @$value->dealid->deal_name }}
                                    </div>
                                    <div
                                        style="width:150px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">
                                        {{ @$value->customername->name }}
                                    </div>
                                </td>
                                <td class="mobhd">
                                    <div
                                        style="width:110px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">
                                        {{ @$value->ownername->full_name }}
                                    </div>

                                    @if (!empty($value->created_date) && $value->created_date != '1970-01-01')
                                    <div style="text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">
                                        {{ \Carbon\Carbon::parse($value->created_date)->format('d/m/Y') }}
                                        {{ \Carbon\Carbon::parse($value->created_date)->format('h:i A') }}
                                    </div>
                                    @endif
                                </td>
                                {{-- <td class="mobhd">
                                    <div
                                        style="width:100px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">
                                        {{@$value->paymentterms->title}}</div>
                                </td> --}}
                                <td class="mobhd text-right">
                                    @php $aed=@App\SysHelper::get_aed_amount($value->deal_currency,$value->deal_value);
                                    @endphp
                                    {{ @App\SysHelper::com_curr_format($aed, 2, '.', ',') }}
                                </td>
                                <td>
                                    @if ($value->accounts == 1)
                                    <span class="success btn-badge py-1 px-2">Accounts Approved</span>
                                    @elseif($value->accounts == 2)
                                    <span class="danger btn-badge py-1 px-2">Accounts Rejected</span>
                                    @elseif($value->accounts == 3)
                                    <span class="primary btn-badge py-1 px-2">Accounts Pending</span>
                                    @else
                                    <span class="warning btn-badge py-1 px-2">Pending</span>
                                    @endif



                                    @if ($value->account && $value->account->created_date)
                                    <div
                                        style="text-overflow: ellipsis; white-space: nowrap; overflow: hidden;text-align: center">
                                        {{ \Carbon\Carbon::parse($value->account->created_date)->format('d/m/Y h:i A') }}
                                        <br>
                                        {{ App\SysHelper::humanTimeDiff($value->created_date, $value->account->created_date)
                                        }}
                                    </div>
                                    @endif

                                </td>
                                <td>
                                    @if ($value->sales == 1)
                                    <span class="success btn-badge py-1 px-2">Sales Approved</span>
                                    @elseif($value->sales == 2)
                                    <span class="danger btn-badge py-1 px-2">Sales Rejected</span>
                                    @elseif($value->sales == 3)
                                    <span class="primary btn-badge py-1 px-2">Sales Pending</span>
                                    @else
                                    <span class="warning btn-badge py-1 px-2">Pending</span>
                                    @endif


                                    @if ($value->salesApproval && $value->salesApproval->created_date)
                                    <div
                                        style="text-overflow: ellipsis; white-space: nowrap; overflow: hidden;text-align: center">
                                        {{ \Carbon\Carbon::parse(@$value->salesApproval->created_date)->format('d/m/Y h:i
                                        A') }}
                                        @if ($value->account && $value->account->created_date && in_array(@$value->accounts,
                                        [1, 2]))
                                        <br>
                                        {{ App\SysHelper::humanTimeDiff(@$value->account->created_date,
                                        $value->salesApproval->created_date) }}
                                        @endif
                                    </div>
                                    @endif

                                </td>
                                <td>
                                    @if ($value->purchease_approval == 0)
                                    <span class="info btn-badge py-1 px-2">Not Applicable</span>
                                    @else
                                    @if ($value->purchease == 1)
                                    <span class="success btn-badge py-1 px-2">Purchase Approved</span>
                                    @elseif($value->purchease == 2)
                                    <span class="danger btn-badge py-1 px-2">Purchase Rejected</span>
                                    @elseif($value->purchease == 3)
                                    <span class="primary btn-badge py-1 px-2">Purchase Pending</span>
                                    @elseif($value->purchease == 4)
                                    <span class="primary btn-badge py-1 px-2">Partial Delivery</span>
                                    @else
                                    <span class="warning btn-badge py-1 px-2">Pending</span>
                                    @endif
                                    @if ($value->purcheaseApproval && $value->purcheaseApproval->created_date)
                                    <div
                                        style="text-overflow: ellipsis; white-space: nowrap; overflow: hidden;text-align: center">

                                        {{ \Carbon\Carbon::parse($value->purcheaseApproval->created_date)->format('d/m/Y h:i
                                        A') }}
                                        <br>
                                        @if ($value->salesApproval && $value->salesApproval->created_date &&
                                        in_array(@$value->sales, [1, 2]))
                                        {{ App\SysHelper::humanTimeDiff(@$value->salesApproval->created_date,
                                        $value->purcheaseApproval->created_date) }}
                                        @elseif($value->account && $value->account->created_date &&
                                        in_array(@$value->accounts, [1, 2]))
                                        {{ App\SysHelper::humanTimeDiff(@$value->account->created_date,
                                        $value->purcheaseApproval->created_date) }}
                                        @endif
                                    </div>
                                    @endif
                                    @endif
                                </td>
                                <td>
                                    @if ($value->invoice_approval == 0)
                                    <span class="info btn-badge py-1 px-2">Not Applicable</span>
                                    @else
                                    @if ($value->invoice == 1)
                                    <span class="success btn-badge py-1 px-2">Invoice Approved</span>
                                    @elseif($value->invoice == 2)
                                    <span class="danger btn-badge py-1 px-2">Invoice Rejected</span>
                                    @elseif($value->invoice == 3)
                                    <span class="primary btn-badge py-1 px-2">Invoice Pending</span>
                                    @else
                                    <span class="warning btn-badge py-1 px-2">Pending</span>
                                    @endif

                                    @if ($value->invoiceApproval && $value->invoiceApproval->created_date)
                                    <div
                                        style="text-overflow: ellipsis; white-space: nowrap; overflow: hidden;text-align: center">
                                        {{ \Carbon\Carbon::parse(@$value->invoiceApproval->created_date)->format('d/m/Y h:i
                                        A') }}
                                        <br>
                                        @if (
                                        $value->purcheaseApproval &&
                                        $value->purcheaseApproval->created_date &&
                                        $value->purchease_approval != 0 &&
                                        in_array(@$value->purchease, [1, 2, 3, 4]))
                                        {{ App\SysHelper::humanTimeDiff(@$value->purcheaseApproval->created_date,
                                        $value->invoiceApproval->created_date) }}
                                        @elseif($value->salesApproval && $value->salesApproval->created_date &&
                                        in_array(@$value->sales, [1, 2]))
                                        {{ App\SysHelper::humanTimeDiff(@$value->salesApproval->created_date,
                                        $value->invoiceApproval->created_date) }}
                                        @elseif($value->account && $value->account->created_date &&
                                        in_array(@$value->accounts, [1, 2]))
                                        {{ App\SysHelper::humanTimeDiff(@$value->account->created_date,
                                        $value->invoiceApproval->created_date) }}
                                        @endif
                                    </div>
                                    @endif
                                    @endif
                                </td>
                                <td>
                                    @if ($value->delivery_approval == 0)
                                    <span class="info btn-badge py-1 px-2">Not Applicable</span>
                                    @else
                                    @if ($value->delivery == 1)
                                    <span class="success btn-badge py-1 px-2">Delivery Completed</span>
                                    @elseif($value->delivery == 2)
                                    <span class="danger btn-badge py-1 px-2">Delivery Rejected</span>
                                    @elseif($value->delivery == 3)
                                    <span class="primary btn-badge py-1 px-2">Out For Delivery</span>
                                    @elseif($value->delivery == 4)
                                    <span class="primary btn-badge py-1 px-2">Pending For Delivery</span>
                                    @elseif($value->delivery == 5)
                                    <span class="primary btn-badge py-1 px-2">Ready For Delivery</span>
                                    @elseif ($value->delivery == 6)
                                    <span class="primary btn-badge py-1 px-2">Partial Delivery</span>
                                    @else
                                    <span class="warning btn-badge py-1 px-2">Pending</span>
                                    @endif
                                    @if ($value->deliveryApproval && $value->deliveryApproval->created_date)
                                    <div
                                        style="text-overflow: ellipsis; white-space: nowrap; overflow: hidden;text-align: center">

                                        {{ \Carbon\Carbon::parse($value->deliveryApproval->created_date)->format('d/m/Y h:i
                                        A') }}
                                        <br>
                                        @if (
                                        $value->invoiceApproval &&
                                        $value->invoiceApproval->created_date &&
                                        $value->invoice_approval != 0 &&
                                        in_array(@$value->invoice, [1, 2]))

                                        {{ App\SysHelper::humanTimeDiff($value->invoiceApproval->created_date,
                                        $value->deliveryApproval->created_date) }}
                                        @elseif($value->purcheaseApproval && $value->purcheaseApproval->created_date &&
                                        in_array(@$value->purchease, [1, 2, 3, 4]))
                                        {{ App\SysHelper::humanTimeDiff($value->purcheaseApproval->created_date,
                                        $value->deliveryApproval->created_date) }}
                                        @elseif($value->salesApproval && $value->salesApproval->created_date &&
                                        in_array(@$value->sales, [1, 2]))
                                        {{ App\SysHelper::humanTimeDiff($value->salesApproval->created_date,
                                        $value->deliveryApproval->created_date) }}
                                        @elseif($value->account && $value->account->created_date &&
                                        in_array(@$value->accounts, [1, 2]))
                                        {{ App\SysHelper::humanTimeDiff($value->account->created_date,
                                        $value->deliveryApproval->created_date) }}
                                        @endif
                                    </div>
                                    @endif
                                    @endif
                                </td>
                                <td>
                                    @if ($value->receivables_approval == 0)
                                    <span class="info btn-badge py-1 px-2">Not Applicable</span>
                                    @else
                                    @if ($value->receivables == 1)
                                    <span class="success btn-badge py-1 px-2">Payment Received</span>
                                    @elseif($value->receivables == 2)
                                    <span class="danger btn-badge py-1 px-2">Rejected</span>
                                    @elseif($value->receivables == 3)
                                    <span class="primary btn-badge py-1 px-2">Payment Pending</span>
                                    @elseif($value->receivables == 4)
                                    <span class="dark btn-badge py-1 px-2">Order Cancelled</span>
                                    @else
                                    <span class="warning btn-badge py-1 px-2">Pending</span>
                                    @endif

                                    @if ($value->receivablesApproval && $value->receivablesApproval->created_date)

                                    <div
                                        style="text-overflow: ellipsis; white-space: nowrap; overflow: hidden;text-align: center">

                                        {{ \Carbon\Carbon::parse($value->receivablesApproval->created_date)->format('d/m/Y
                                        h:i A') }} <br>

                                        @if (
                                        $value->deliveryApproval &&
                                        $value->deliveryApproval->created_date &&
                                        $value->delivery_approval != 0 &&
                                        in_array($value->delivery, [1, 2, 3, 4, 5, 6]))
                                        {{ App\SysHelper::humanTimeDiff($value->deliveryApproval->created_date,
                                        $value->receivablesApproval->created_date) }}
                                        @elseif($value->invoiceApproval && $value->invoiceApproval->created_date &&
                                        in_array(@$value->invoice, [1, 2]))
                                        {{ App\SysHelper::humanTimeDiff($value->invoiceApproval->created_date,
                                        $value->receivablesApproval->created_date) }}
                                        @elseif($value->purcheaseApproval && $value->purcheaseApproval->created_date &&
                                        in_array(@$value->purchease, [1, 2, 3, 4]))
                                        {{ App\SysHelper::humanTimeDiff($value->purcheaseApproval->created_date,
                                        $value->receivablesApproval->created_date) }}
                                        @elseif($value->salesApproval && $value->salesApproval->created_date &&
                                        in_array(@$value->sales, [1, 2]))
                                        {{ App\SysHelper::humanTimeDiff($value->salesApproval->created_date,
                                        $value->receivablesApproval->created_date) }}
                                        @elseif($value->account && $value->account->created_date &&
                                        in_array(@$value->accounts, [1, 2]))
                                        {{ App\SysHelper::humanTimeDiff($value->account->created_date,
                                        $value->receivablesApproval->created_date) }}
                                        @endif
                                    </div>
                                    @endif
                                    @endif
                                </td>
                                <td class="text-right">
                                    <a class="btn-sm btn-info" href="{{ url('crm-deal-track-approval/' . $value->id) }}"><i
                                            class="fa fa-eye mobhd" aria-hidden="true"></i></a>
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
        </div>

    

        <script>
            $(document).ready(function () {
                $('.collapse').on('show.bs.collapse', function () {
                    $(this).closest('.task-card').find('.toggle-icon')
                        .removeClass('fa-chevron-down')
                        .addClass('fa-chevron-up');
                });

                $('.collapse').on('hide.bs.collapse', function () {
                    $(this).closest('.task-card').find('.toggle-icon')
                        .removeClass('fa-chevron-up')
                        .addClass('fa-chevron-down');
                });
            });
        </script>
    </div>

<?php } catch (\Exception $e) { ?> {{ $e }} <?php } ?>

@endsection
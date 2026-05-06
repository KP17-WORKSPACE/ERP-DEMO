@extends('backEnd.newmasterpage')
@section('mainContent')
    @php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp

    <script src="https://cdn.jsdelivr.net/npm/exceljs@4.3.0/dist/exceljs.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

    <?php try { ?>

<style>
      .pagination .page-item.active .page-link {
            background-color: #198754 !important;
            /* Bootstrap success green */

            color: #fff !important;
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
    </style>

    <aside class="left-nav col-3" id="leftSidebar">
        <div class="resizer" id="sidebarResizer"></div>
        <h4 class="mb-2">Deal Track Status</h4>

        <div class="search-filter-container mb-4" id="short-list">

            <div class="input-group flex-nowrap">
                <input type="text" class="form-control" id="search_invoice" placeholder="Document No" aria-label="Search"
                    aria-describedby="addon-wrapping">
            </div>
            <button type="button" class="btn btn-light" id="list_style_button" onclick="list_style_search()"
                style="height: 32px;">
                <i class="ico icon-outline-list-down"></i>
            </button>

        </div>

        <div class="left-nav-list" id="invoice_list">
            <ul id="short-list-items" class="nav flex-column nav-pills" id="companyTabNavs" role="tablist">
                @if (count($dealtrack) > 0)
                    @foreach ($dealtrack as $value)
                        <li class="nav-item w-100" role="presentation">
                            <button href="javascript:void(0)"
                                class="nav-link data-item {{ $active_id == $value->id ? 'active' : '' }}"
                                data-id="{{ $value->id }}">
                                {{-- <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="grn-tab" data-bs-toggle="tab" 
                                    data-bs-target="#grn-{{ $value->id }}" type="button" role="tab" aria-controls="grn-{{ $value->id }}"
                                    aria-selected="true"> --}}
                                <div class="row w-100">
                                     <div class="col-12">
                                        <label class="form-control-plaintext truncate-text">{{ @$value->customername->name }} @if (@App\SysHelper::getCompanyCodeSettings()['is_customer_code'])
                                            ({{ @$value->customername->code }})
                                            @endif</label>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-control-plaintext" style="font-size: 11px">{{ @$value->deal_code->code }}</div>
                                    </div>
                                    <div class="col-4 pl-2">
                                        <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                                            {{ date('d/m/Y', strtotime(@$value->delivery_date)) }}</div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                                            @php $aed=@App\SysHelper::get_aed_amount($value->deal_currency,$value->deal_value); @endphp
                                            {{ @App\SysHelper::com_curr_format($aed, 2, '.', ',') }}
                                        </div>
                                    </div>
                                   
                                </div>
                                {{-- </button> --}}
                            </button>
                        </li>
                    @endforeach
                @endif
            </ul>
            <div id="long-list" style="display: none;">


                 <input type="text" id="tableSearch" 
                                    class="form-control" 
                                    style="font-size:13px; width: 350px;
                                    position: absolute;
                                    top: 10px;
                                    right: 231px;" 
                                    placeholder="Search">

                <button type="button" class="btn btn-light list_style_search_btn" id="exportExcelDealTrackStatus" title="Export to Excel" style="margin-right:66px">
                    <i class="ico icon-outline-export text-success"></i> Export
                </button>

                <button type="button" class="btn btn-light list_style_search_btn" onclick="search_box_show_hide()" style="margin-right: 8px;">
                    <i class="ico icon-outline-magnifer"></i>
                </button>

                <button type="button" class="btn btn-light list_style_expand_btn" id="list_style_button"
                    onclick="list_style_search()">
                    <i class="ico icon-outline-list-down"></i>
                </button>

                <div class="card mt-3" id="search_box" style="display: none;">
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

                          
                            



                            <div class="col-1 mb-2 ">
                                <label for="" class="form-label">Form Date</label>
                                <input class="form-control date-picker" id="date" type="text" autocomplete="off"
                                    name="date_from"
                                    value="{{ $ctrl_date_from ? @App\SysHelper::normalizeToDmy($ctrl_date_from) : '' }}">
                            </div>

                            <div class="col-1 mb-2 ">
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

                            <div class="col-1-5 filter-field">
                               <button type="submit" class="btn btn-light add-btn mt-4" id="btnSubmit">
                                            <i class="ico icon-outline-minimalistic-magnifer text-success"></i> Filter
                                        </button>
                            </div>

                        </div>
                        {{ Form::close() }}


                    </div>
                </div>

                  <div class="row task-row sticky-top" style="background-color: white">

                <div class="cls-m-12 col-md-6 col-lg-2 filter-by-status" data-status-id="1"
                    style="cursor:pointer;">
                    <div class="task-card">
                        <div><i class="fa 	fa-plus-circle task-icon"></i></div>
                        <h5 class="task-title">Accounts</h5>
                        
                        <div class="sub-status collapse" id="newCollapse">
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

                        <div class="task-toggle-indicator" data-bs-toggle="collapse" href="#newCollapse" role="button"
                            aria-expanded="false" aria-controls="newCollapse">
                            <i class="ico icon-outline-alt-arrow-down toggle-icon" id="icon-new"></i>
                        </div>
                    </div>
                </div>

                <div class="cls-m-12 col-md-6 col-lg-2 filter-by-status" data-status-id="4"
                    style="cursor:pointer;">
                    <div class="task-card ">
                        <div><i class="fa 	fa-hourglass-half task-icon"></i></div>
                        <h5 class="task-title">Sales</h5>
                        
                        <div class="sub-status collapse" id="pendingCollapse">
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

                        <div class="task-toggle-indicator" data-bs-toggle="collapse" href="#pendingCollapse"
                            role="button" aria-expanded="false" aria-controls="pendingCollapse">
                            <i class="ico icon-outline-alt-arrow-down toggle-icon" id="icon-new"></i>
                        </div>
                    </div>
                </div>

                <div class="cls-m-12 col-md-6 col-lg-2 filter-by-status" data-status-id="2"
                    style="cursor:pointer;">

                    <div class="task-card ">
                        <div><i class="fa fa-thumbs-up task-icon"></i></div>
                        <h5 class="task-title">Purchase</h5>
                       
                        <div class="sub-status collapse" id="qualifiedCollapse">
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

                        <div class="task-toggle-indicator" data-bs-toggle="collapse" href="#qualifiedCollapse"
                            role="button" aria-expanded="false" aria-controls="qualifiedCollapse">
                            <i class="ico icon-outline-alt-arrow-down toggle-icon" id="icon-new"></i>
                        </div>
                    </div>
                </div>

                <div class="cls-m-12 col-md-6 col-lg-2 filter-by-status" data-status-id="3"
                    style="cursor:pointer;">

                    <div class="task-card">
                        <div><i class="fa fa-times-circle task-icon"></i></div>
                        <h5 class="task-title">Invoice</h5>
                       
                        <div class="sub-status collapse" id="unqualifiedCollapse">
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

                        <div class="task-toggle-indicator" data-bs-toggle="collapse" href="#unqualifiedCollapse"
                            role="button" aria-expanded="false" aria-controls="unqualifiedCollapse">
                            <i class="ico icon-outline-alt-arrow-down toggle-icon" id="icon-new"></i>
                        </div>
                    </div>
                </div>

                <div class="cls-m-12 col-md-6 col-lg-2 filter-by-status" data-status-id="10"
                    style="cursor:pointer;">

                    <div class="task-card">
                        <div><i class="fa 	fa-archive  task-icon"></i></div>
                        <h5 class="task-title">Delivery</h5>
                       
                        <div class="sub-status collapse" id="deliveryclosedCollapse">
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

                        <div class="task-toggle-indicator" data-bs-toggle="collapse" href="#deliveryclosedCollapse"
                            role="button" aria-expanded="false" aria-controls="deliveryclosedCollapse">
                            <i class="ico icon-outline-alt-arrow-down toggle-icon" id="icon-new"></i>
                        </div>
                    </div>
                </div>

                    <div class="cls-m-12 col-md-6 col-lg-2 filter-by-status" data-status-id="11"
                    style="cursor:pointer;">

                    <div class="task-card">
                        <div><i class="fa 	fa-archive  task-icon"></i></div>
                        <h5 class="task-title">Receivables</h5>
                       
                        <div class="sub-status collapse" id="closedCollapse">
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

                        <div class="task-toggle-indicator" data-bs-toggle="collapse" href="#closedCollapse"
                            role="button" aria-expanded="false" aria-controls="closedCollapse">
                            <i class="ico icon-outline-alt-arrow-down toggle-icon" id="icon-new"></i>
                        </div>
                    </div>
                </div>


                
            </div>


                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">

                               <table id="long-list" class="table table-hover mt-3 data-table table-fixed-header" style="table-layout: fixed;width:100%">

                    <thead>
                        <tr>
                            <th class="text-center" style="width:80px">@lang('Deal ID')</th>
                            @if (session('logged_session_data.company_id') == 1)
                                <th>@lang('Company')</th>
                            @endif
                            <th style="width:250px" class="mobhd">@lang('Deal Name')</th>
                            <th style="width: 200px;" class="mobhd">@lang('Customer')</th>

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
                                <td class="data-item text-center" data-id="{{ $value->id }}" onclick="list_style_search()"><a
                                        >{{ @$value->deal_code->code }}</a>
                                </td>
                                @if (session('logged_session_data.company_id') == 1)
                                    <td>{{ $value->companyname->company_name }}</td>
                                @endif
                                <td class="mobhd">
                                    {{ @$value->dealid->deal_name }}

                                  

                                </td>
                                            <td class="mobhd">{{ @$value->customername->name }}</td>

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
                                        <span class="badge bg-danger py-1 px-2">Accounts Rejected</span>
                                    @elseif($value->accounts == 3)
                                        <span class="badge bg-primary py-1 px-2">Accounts Pending</span>
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
                                        <span class="badge bg-danger py-1 px-2">Sales Rejected</span>
                                    @elseif($value->sales == 3)
                                        <span class="badge bg-primary py-1 px-2">Sales Pending</span>
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
                                            <span class="badge bg-danger py-1 px-2">Purchase Rejected</span>
                                        @elseif($value->purchease == 3)
                                            <span class="badge bg-primary py-1 px-2">Purchase Pending</span>
                                        @elseif($value->purchease == 4)
                                            <span class="badge bg-primary py-1 px-2">Partial Delivery</span>
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
                                            <span class="badge bg-danger py-1 px-2">Invoice Rejected</span>
                                        @elseif($value->invoice == 3)
                                            <span class="badge bg-primary py-1 px-2">Invoice Pending</span>
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
                                                    $value->invoiceApproval->created_date
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
                                            <span class="badge bg-danger py-1 px-2">Delivery Rejected</span>
                                        @elseif($value->delivery == 3)
                                            <span class="badge bg-primary py-1 px-2">Out For Delivery</span>
                                        @elseif($value->delivery == 4)
                                            <span class="badge bg-primary py-1 px-2">Pending For Delivery</span>
                                        @elseif($value->delivery == 5)
                                            <span class="badge bg-primary py-1 px-2">Ready For Delivery</span>
                                        @elseif ($value->delivery == 6)
                                            <span class="badge bg-primary py-1 px-2">Partial Delivery</span>
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
                                                    $value->deliveryApproval->created_date
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
                                            <span class="badge bg-danger py-1 px-2">Rejected</span>
                                        @elseif($value->receivables == 3)
                                            <span class="badge bg-primary py-1 px-2">Payment Pending</span>
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
                                                    $value->receivablesApproval->created_date
                                                ) }}
                                            @elseif($value->invoiceApproval && $value->invoiceApproval->created_date && in_array(@$value->invoice, [1, 2]))
                                                {{ App\SysHelper::humanTimeDiff(
                                                    $value->invoiceApproval->created_date,
                                                    $value->receivablesApproval->created_date
                                                ) }}
                                            @elseif($value->purcheaseApproval && $value->purcheaseApproval->created_date && in_array(@$value->purchease, [1, 2, 3, 4]))
                                                {{ App\SysHelper::humanTimeDiff(
                                                    $value->purcheaseApproval->created_date,
                                                    $value->receivablesApproval->created_date
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

                          <tfoot>
                        <tr>
                            <td colspan="9" class="text-center border-0">
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $dealtrack->appends(request()->input())->links() }}
                                </div>
                            </td>
                        </tr>
                    </tfoot>

                   



                </table>
                            
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </aside>

         <script>
            $('.sub-status-div').on('click', function() {
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



    <div class="content-container col-9">
        <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
            <script>
                $(document).ready(function() {
                    $(document).on('click', '.data-item', function() {

                        $("#loading_bg").css("display", "block");

                        var id = $(this).data('id');

                        $('.data-item').removeClass('active');
                        $(this).addClass('active');

                        var newUrl = "{{ url('crm-deal-track-status') }}/" + id;
                        window.history.pushState({
                            path: newUrl
                        }, '', newUrl);

                        var action = "{{ URL::to('crm-deal-track-details') }}/" + id;

                        $.ajax({
                            url: action,
                            method: 'GET',
                            success: function(response) {
                                $('#data-details').html(response);
                            },
                            error: function() {
                                $('#data-details').html(
                                    '<p class="text-danger">Error loading details.</p>');
                            },
                            complete: function() {
                                $("#loading_bg").css("display", "none");
                            }
                        });
                    });
                });
            </script>

            <script>
                $(document).ready(function() {

                    $('#search_invoice').on('input', function() {
                        var query = $(this).val();

                        $.ajax({
                            url: "{{ route('crm-deals-track.search') }}",
                            type: "GET",
                            data: {
                                query: query
                            },
                            success: function(data) {
                                $('#short-list-items').html('');

                                if (data.length > 0) {
                                    $.each(data, function(index, invoice) {

                                        let ims = `<li class="nav-item w-100" role="presentation">
    <button href="javascript:void(0)" class="nav-link data-item" data-id="${invoice.id}">
        <div class="row w-100">
             <div class="col-12">
                <label class="form-control-plaintext truncate-text">
                    ${invoice.account_name}  @if (@App\SysHelper::getCompanyCodeSettings()['is_customer_code'])
                    (${invoice.account_code})
                                            @endif
                </label>
            </div>
            <div class="col-4">
                <div class="form-control-plaintext" style="font-size: 11px">${invoice.code}</div>
            </div>
            <div class="col-4 pl-2">
                <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                    ${get_format_date(invoice.date)}
                </div>
            </div>
            <div class="col-4 text-end">
                <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                    ${Number(invoice.deal_profit).toLocaleString()} ${invoice.currency_code}
                </div>
            </div>
           
        </div>
    </button>
</li>`;
                                        $('#short-list-items').append(ims);
                                    });
                                } else {
                                    $('#short-list-items').html(
                                        '<div class="p-2">No results found</div>');
                                }
                            }
                        });
                    });

                });
            </script>
            @if (count($dealtrack) > 0)
                <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
                    @if (count($trackdata) > 0)
                        @include('backEnd.crm.DealTrackApprovalDetail', $trackdata)
                    @endif
                </div>
            @endif
        </div>
    </div>

    <script>
        const leftNav = document.querySelector('.left-nav');
        const content = document.querySelector('.content-container');
        const state = localStorage.getItem("leftNavState");
        if (state === "expanded") {
            leftNav.classList.remove('col-3');
            leftNav.classList.add('col-12');
            if (content) {
                content.classList.remove('col-9');
                content.classList.add('col-0');
            }
            $('#short-list').hide();
            $('#short-list-items').hide();
            $('#long-list').show();
        } else if (state === "collapsed") {
            leftNav.classList.remove('col-12');
            leftNav.classList.add('col-3');
            if (content) {
                content.classList.remove('col-0');
                content.classList.add('col-9');
            }
            $('#short-list').show();
            $('#short-list-items').show();
            $('#long-list').hide();
        }
    </script>

    <script>
        $(document).ready(function() {
            function initAccountSelect2(selector) {
                $(selector).select2({
                    ajax: {
                        url: '{{ route('autocomplete.get_cust_account_list_ajax') }}',
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                search_text: params.term
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.map(function(item) {
                                    return {
                                        id: item.id,
                                        text: item.account_code + ' - ' + item.account_name
                                    };
                                })
                            };
                        },
                        cache: true
                    },
                    placeholder: 'Select Account',
                    minimumInputLength: 2
                });
            }

            // Initial init
            initAccountSelect2('.js-account-select');

            // Re-initialize on focus (if needed for dynamically added fields)
            $(document).on('focus', '.js-account-select', function() {
                if (!$(this).hasClass("select2-hidden-accessible")) {
                    initAccountSelect2(this);
                }
            });

            // Open dropdown and focus search box on click
            $(document).on('click', '.js-account-select', function() {
                $(this).select2('open');
            });

            // Focus the search input inside the opened Select2 dropdown
            $(document).on('select2:open', function() {
                setTimeout(function() {
                    const searchInput = document.querySelector(
                        '.select2-container--open .select2-search__field');
                    if (searchInput) {
                        searchInput.focus();
                    }
                }, 0);
            });
        });

        $(document).ready(function() {
            $(".list_style_search_btn").on("click", function() {
                $("#search_box").slideToggle(200); // expands/collapses smoothly
            });
        });
    </script>

         <script>
$(document).ready(function() {
    function setManualWidths() {
        var $table = $('.table-fixed-header');
        var $theadTh = $table.find('thead th');
       

        
        var  columnWidths = [

          @if (session('logged_session_data.company_id') == 1)
            80, 100, 150, 150, 120, 100, 120, 120, 70, 75, 80, 75
            @else
            80, 100, 150, 150, 120, 100, 120, 120, 70, 75, 75
            @endif

            
        ];


        // Apply widths to <thead> and <tbody>
        $theadTh.each(function(i) {
            var w = columnWidths[i];
            $(this).css('width', w + 'px');
            $table.find('tbody td:nth-child(' + (i + 1) + ')').css('width', w + 'px');
        });

    }

    setManualWidths();
    $(window).on('resize', setManualWidths);
});
</script>

<script>
$(document).ready(function() {
    $('#exportExcelDealTrackStatus').on('click', function (e) {
        e.preventDefault();

        var companyName = @json(@App\SysCompany::find(session('logged_session_data.company_id') ?? '')->trade_name ?? '');
        var totalDeals = @json($dealtrack->count() ?? 0);
        var dateFrom = @json($ctrl_date_from ?? '');
        var dateTo = @json($ctrl_date_to ?? '');

        var $table = $('table.table-fixed-header');
        var visibleColIndexes = [];
        var headerLabels = [];

        $table.find('thead tr th').each(function (i) {
            if ($(this).css('display') === 'none') return;
            var label = $(this).text().trim();
            if (['actions', 'action'].includes(label.toLowerCase())) return;
            visibleColIndexes.push(i);
            headerLabels.push(label);
        });

        function formatDMY(value) {
            if (!value) return '-';
            var normalized = value.trim().replace(/\s+/g, '');
            var parts = normalized.split(/[\/\-\.]/);
            if (parts.length === 3) {
                if (parts[0].length === 4) {
                    return parts[2] + '/' + parts[1] + '/' + parts[0];
                }
                return parts[0] + '/' + parts[1] + '/' + parts[2];
            }
            return value;
        }

        var rows = [];
        rows.push([companyName]);
        rows.push(['Deal Track Status (' + totalDeals + ')']);
        if (dateFrom || dateTo) {
            var parts = [];
            if (dateFrom) { parts.push('From: ' + formatDMY(dateFrom)); }
            if (dateTo) { parts.push('To: ' + formatDMY(dateTo)); }
            rows.push([parts.join('  ')]);
        }
        rows.push([]);
        rows.push(headerLabels);

        $table.find('tbody tr').each(function () {
            var $cells = $(this).find('td');
            var rowData = [];
            visibleColIndexes.forEach(function (i) {
                rowData.push($cells.eq(i).text().trim().replace(/\s+/g, ' '));
            });
            rows.push(rowData);
        });

        if (rows.length <= 5) {
            alert('No data available for export');
            return;
        }

        var N = headerLabels.length || 1;
            var workbook  = new ExcelJS.Workbook();
            var worksheet = workbook.addWorksheet('DealTrackStatus');
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
                var filename = 'dealtrackstatus_' + pad(d.getDate()) + '-' + pad(d.getMonth() + 1) + '-' + d.getFullYear() + '.xlsx';
                saveAs(blob, filename);
            });
    });
});
</script>

    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@endsection

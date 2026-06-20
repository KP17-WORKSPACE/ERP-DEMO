@extends('backEnd.newmasterpage')
@section('mainContent')
<script>
    let isFullList = false;

    function list_style_search() {
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
            localStorage.setItem("leftNavState", "expanded");
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
            $("#search_box").hide();
            localStorage.setItem("leftNavState", "collapsed");
        }
    }

    function search_box_show_hide() {
        document.querySelectorAll('#filters-long .filter-field').forEach(el => {
            el.classList.toggle('d-none');
        });
    }
</script>
@php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    $reimbursementPermissions = $reimbursementPermissions ?? ['create' => false, 'view' => false, 'edit' => false, 'delete' => false, 'export' => false, 'attach' => false];
    $reimbursementTrackPermissions = $reimbursementTrackPermissions ?? ['create' => false, 'view' => false, 'edit' => false, 'delete' => false, 'export' => false, 'attach' => false];
@endphp

    <aside class="left-nav col-3" id="leftSidebar">
        <style>
            /* Force hide Datatables filter box */
            #long-list_filter, .dataTables_filter, .dataTables_wrapper .dataTables_filter {
                display: none !important;
            }
            
            .long-list td, .long-list th {
                white-space: nowrap !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
            }
            
            .long-list tr.expand td {
                white-space: normal !important;
                overflow: visible !important;
                text-overflow: unset !important;
                height: auto !important;
                word-break: break-word !important;
            }
            
            /* Pointer on rows */
            .long-list tbody tr {
                cursor: pointer;
            }
        </style>
        <div class="resizer" id="sidebarResizer"></div>
        <h4 class="mb-2" id="short-title">Reimbursement Track</h4>

        <div class="search-filter-container mb-4" id="short-list">
            <div class="input-group flex-nowrap">
                <input type="text" class="form-control" id="search_invoice" placeholder="Document No" aria-label="Search" aria-describedby="addon-wrapping">
            </div>                        
            <button type="button" class="btn btn-light" id="list_style_button" onclick="list_style_search()" style="height: 32px;">
                <i class="ico icon-outline-list-down"></i>
            </button>
        </div>

        <div class="left-nav-list" id="invoice_list">
            <ul id="short-list-items" class="nav flex-column nav-pills" role="tablist">
                @if(count($data)>0)
                    @foreach($data as $value)
                    <li class="nav-item w-100" role="presentation">
                        <button href="javascript:void(0)" class="nav-link data-item {{ $active_id == $value->id ? 'active' : '' }}" data-id="{{ $value->id }}">
                            <div class="row w-100">
                                <div class="col-12">
                                    <label class="form-control-plaintext truncate-text">{{ $value->deal_code->customername->name ?? 'N/A' }}</label>
                                </div>
                                <div class="col-4">
                                    <div class="form-control-plaintext" style="font-size:11px">{{ $value->reimbursement_no }}</div>
                                </div>
                                <div class="col-4 pl-2">
                                    <div class="form-control-plaintext truncate-text" style="font-size:11px">{{ date('d/m/Y', strtotime($value->date)) }}</div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="form-control-plaintext truncate-text" style="font-size:11px">
                                        {{ number_format($value->amount, 2) }} {{ $value->currencycode->code ?? '' }}
                                    </div>
                                </div>
                            </div>
                        </button>
                    </li>
                    @endforeach
                @endif
            </ul>

            <div id="long-list" style="display: none;">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h4 class="mb-0">Reimbursement Track</h4>
                    
                    <div class="search-filter-container mb-0">
                        <input type="text" id="tableSearch" class="form-control d-inline-block"
                            style="font-size:13px;width: 350px;margin-right: 10px" placeholder="Search">

                        @if(!empty($reimbursementTrackPermissions['export']))
                            <button type="button" class="btn btn-light" id="exportExcelDealTrack" title="Export to Excel" style="margin-right: 10px;">
                                <i class="ico icon-outline-export text-success"></i> Export
                            </button>
                        @endif

                        <button type="button" class="btn btn-light" onclick="search_box_show_hide()" style="margin-right: 10px;">
                            <i class="ico icon-outline-magnifer"></i>
                        </button>

                        <button type="button" class="btn btn-light" id="list_style_button" onclick="list_style_search()">
                            <i class="ico icon-outline-list-down"></i>
                        </button>
                    </div>
                </div>

                <div class="search-filter-container mt-1 mb-4 filter-field border" id="search_box" style="display: {{ request()->has('reimbursement_no') || request()->has('vendor_name') || request()->has('from_date') || request()->has('to_date') || request()->has('expense_category') || request()->has('invoice_no') || request()->has('invoice_date') || request()->has('amount') || request()->has('deal_id') || request()->has('project_id') || request()->has('submitted_by') || request()->has('status_filter') || request()->has('attachments') || request()->has('filter_by') ? 'block' : 'none' }};">
                    <div class="card">
                        <div class="card-body">
                            {{ Form::open(['class' => 'form-horizontal', 'url' => 'crm-reimbursement-track', 'method' => 'get', 'id' => 'crm-reimbursement-search']) }}
                            <div class="row">
                                <div class="col-1-5 mb-2 filter-field">
                                    <label for="" class="form-label">From Date</label>
                                    <input class="form-control date-picker" type="text" autocomplete="off" name="from_date" id="from_date" value="{{ $ctrl_from_date ?? '' }}">
                                </div>
                                <div class="col-1-5 mb-2 filter-field">
                                    <label for="" class="form-label">To Date</label>
                                    <input class="form-control date-picker" type="text" autocomplete="off" name="to_date" id="to_date" value="{{ $ctrl_to_date ?? '' }}">
                                </div>

                                <div class="col-1-5 mb-2 filter-field">
                                    <label for="" class="form-label">Expense Category</label>
                                    <select class="form-control" name="expense_category">
                                        <option value="">-Select-</option>
                                        <option value="Travel Expenses" @if (@$ctrl_expense_category == 'Travel Expenses') selected @endif>Travel Expenses</option>
                                        <option value="Fuel Expenses" @if (@$ctrl_expense_category == 'Fuel Expenses') selected @endif>Fuel Expenses</option>
                                        <option value="Taxi / Transportation" @if (@$ctrl_expense_category == 'Taxi / Transportation') selected @endif>Taxi / Transportation</option>
                                        <option value="Air Ticket" @if (@$ctrl_expense_category == 'Air Ticket') selected @endif>Air Ticket</option>
                                        <option value="Hotel Accommodation" @if (@$ctrl_expense_category == 'Hotel Accommodation') selected @endif>Hotel Accommodation</option>
                                        <option value="Meals & Entertainment" @if (@$ctrl_expense_category == 'Meals & Entertainment') selected @endif>Meals & Entertainment</option>
                                        <option value="Client Meeting Expenses" @if (@$ctrl_expense_category == 'Client Meeting Expenses') selected @endif>Client Meeting Expenses</option>
                                        <option value="Site Visit Expenses" @if (@$ctrl_expense_category == 'Site Visit Expenses') selected @endif>Site Visit Expenses</option>
                                        <option value="Courier & Logistics" @if (@$ctrl_expense_category == 'Courier & Logistics') selected @endif>Courier & Logistics</option>
                                        <option value="Telephone / Mobile" @if (@$ctrl_expense_category == 'Telephone / Mobile') selected @endif>Telephone / Mobile</option>
                                        <option value="Internet Expenses" @if (@$ctrl_expense_category == 'Internet Expenses') selected @endif>Internet Expenses</option>
                                        <option value="Training & Certification" @if (@$ctrl_expense_category == 'Training & Certification') selected @endif>Training & Certification</option>
                                        <option value="Medical Expenses" @if (@$ctrl_expense_category == 'Medical Expenses') selected @endif>Medical Expenses</option>
                                        <option value="Visa Expenses" @if (@$ctrl_expense_category == 'Visa Expenses') selected @endif>Visa Expenses</option>
                                        <option value="Office Supplies" @if (@$ctrl_expense_category == 'Office Supplies') selected @endif>Office Supplies</option>
                                        <option value="Parking & Toll Charges" @if (@$ctrl_expense_category == 'Parking & Toll Charges') selected @endif>Parking & Toll Charges</option>
                                        <option value="Vehicle Maintenance" @if (@$ctrl_expense_category == 'Vehicle Maintenance') selected @endif>Vehicle Maintenance</option>
                                        <option value="Customs Clearance Expenses" @if (@$ctrl_expense_category == 'Customs Clearance Expenses') selected @endif>Customs Clearance Expenses</option>
                                        <option value="Marketing & Promotion" @if (@$ctrl_expense_category == 'Marketing & Promotion') selected @endif>Marketing & Promotion</option>
                                        <option value="Miscellaneous Expenses" @if (@$ctrl_expense_category == 'Miscellaneous Expenses') selected @endif>Miscellaneous Expenses</option>
                                        <option value="Other" @if (@$ctrl_expense_category == 'Other') selected @endif>Other</option>
                                    </select>
                                </div>

                                <div class="col-1-5 mb-2 filter-field">
                                    <label for="" class="form-label">Invoice Number</label>
                                    <input class="form-control" type="text" autocomplete="off" name="invoice_no" value="{{ $ctrl_invoice_no ?? '' }}">
                                </div>

                                <div class="col-1-5 mb-2 filter-field">
                                    <label for="" class="form-label">Invoice Date</label>
                                    <input class="form-control date-picker" type="text" autocomplete="off" name="invoice_date" id="invoice_date" value="{{ $ctrl_invoice_date ?? '' }}">
                                </div>

                                <div class="col-1-5 mb-2 filter-field">
                                    <label for="" class="form-label">Amount</label>
                                    <input class="form-control" type="text" autocomplete="off" name="amount" value="{{ $ctrl_amount ?? '' }}">
                                </div>

                                <div class="col-1-5 mb-2 filter-field">
                                    <label for="" class="form-label">Reimbursement No.</label>
                                    <input class="form-control" type="text" autocomplete="off" name="reimbursement_no" value="{{ $ctrl_reimbursement_no ?? '' }}">
                                </div>

                                <div class="col-1-5 mb-2 filter-field">
                                    <label for="" class="form-label">Deal ID</label>
                                    <input class="form-control" type="text" autocomplete="off" name="deal_id" value="{{ $ctrl_deal_id ?? '' }}">
                                </div>

                                <div class="col-1-5 mb-2 filter-field">
                                    <label for="" class="form-label">Project ID</label>
                                    <input class="form-control" type="text" autocomplete="off" name="project_id" value="{{ $ctrl_project_id ?? '' }}">
                                </div>

                                <div class="col-1-5 mb-2 filter-field">
                                    <label for="" class="form-label">Submitted By</label>
                                    <input class="form-control" type="text" autocomplete="off" name="submitted_by" value="{{ $ctrl_submitted_by ?? '' }}">
                                </div>

                                <div class="col-1-5 mb-2 filter-field">
                                    <label for="" class="form-label">Status</label>
                                    <select class="form-control" name="status_filter" id="status_filter">
                                        <option value="">-Select-</option>
                                        <option value="new_pending" @if (@$ctrl_status == 'new_pending') selected @endif>New / Pending</option>
                                        <option value="dept_head_approved" @if (@$ctrl_status == 'dept_head_approved') selected @endif>Reporting Manager Approved</option>
                                        <option value="dept_head_rejected" @if (@$ctrl_status == 'dept_head_rejected') selected @endif>Reporting Manager Rejected</option>
                                        <option value="accounts_head_approved" @if (@$ctrl_status == 'accounts_head_approved') selected @endif>Finance Approved</option>
                                        <option value="accounts_head_rejected" @if (@$ctrl_status == 'accounts_head_rejected') selected @endif>Finance Rejected</option>
                                        <option value="accounts_approved" @if (@$ctrl_status == 'accounts_approved') selected @endif>Payment Processing Approved</option>
                                        <option value="accounts_rejected" @if (@$ctrl_status == 'accounts_rejected') selected @endif>Payment Processing Rejected</option>
                                    </select>
                                </div>

                                <div class="col-1-5 mb-2 filter-field">
                                    <label for="" class="form-label">Attachment</label>
                                    <select class="form-control" name="attachments" id="attachments">
                                        <option value="">Select</option>
                                        <option value="1" @if (@$ctrl_attachments == 1) selected @endif>With Attachments Only</option>
                                        <option value="2" @if (@$ctrl_attachments == 2) selected @endif>Without Attachments Only</option>
                                        <option value="3" @if (@$ctrl_attachments == 3) selected @endif>All</option>
                                    </select>
                                </div>

                                <div class="col-1-5 mb-2 filter-field">
                                    <label for="" class="form-label">Filter By</label>
                                    <select class="form-control" name="filter_by" id="filter_by">
                                        <option value="" @if (@$filter_by == '') selected @endif>-Select-</option>
                                        <option value="this_month" @if (@$filter_by == 'this_month') selected @endif>This Month</option>
                                        <option value="today" @if (@$filter_by == 'today') selected @endif>Today</option>
                                        <option value="this_week" @if (@$filter_by == 'this_week') selected @endif>This Week</option>
                                        <option value="last_week" @if (@$filter_by == 'last_week') selected @endif>Last Week</option>
                                        <option value="last_month" @if (@$filter_by == 'last_month') selected @endif>Last Month</option>
                                        <option value="this_quarter" @if (@$filter_by == 'this_quarter') selected @endif>This Quarter</option>
                                        <option value="pre_quarter" @if (@$filter_by == 'pre_quarter') selected @endif>Previous Quarter</option>
                                        <option value="this_year" @if (@$filter_by == 'this_year') selected @endif>This Year</option>
                                        <option value="last_year" @if (@$filter_by == 'last_year') selected @endif>Last Year</option>
                                    </select>
                                </div>

                                <div class="col-md-3 filter-field">
                                    <div class="d-flex align-items-center mt-4">
                                        <button type="submit" class="btn btn-light me-2" style="width:auto;">
                                            <i class="ico icon-outline-magnifer"></i> Filter
                                        </button>
                                        <a href="{{ url('crm-reimbursement-track') }}" class="btn btn-light" style="width:auto;">
                                            <i class="ico icon-bold-restart text-success"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </div>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="long-list-table" class="table table-hover data-table mt-2 long-list" style="table-layout: fixed;width:100%">
                                <thead class="text-center">
                                    <tr>
                                        <th style="width: 90px;" class="text-center">@lang('Date')</th>
                                        <th style="width: 80px;" class="text-start">@lang('Reimb. No')</th>
                                        <th style="width: 70px;">@lang('Deal ID')</th>
                                        <th style="width: 170px;" class="text-start">@lang('Customer Name')</th>
                                        <th style="width: 130px;" class="text-start">@lang('Scope of Work')</th>
                                        <th style="width: 80px;" class="text-start">@lang('Invoice No')</th>
                                        <th style="width: 80px;" class="text-start">@lang('Amount')</th>
                                        <th style="width: 120px;" class="text-start">@lang('Expense Category')</th>
                                        <th style="width: 90px;" class="text-start">@lang('Head Count') <br> @lang('& Name')</th>
                                        <th style="width: 90px;" class="text-start">@lang('Submited By')</th>
                                        <th style="width: 100px;">@lang('Status')</th>
                                        <th class="text-center" style="width: 70px;">@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size:12px">
                                    @foreach($data as $value)
                                        <tr class="{{ $value->deleted_at ? 'bg-dark' : '' }}">
                                            <td class="text-center">{{ date('d/m/Y', strtotime(@$value->date)) }}</td>
                                            <td class="text-start data-item" data-id="{{ $value->id }}" onclick="list_style_search()"><a class="text-success" style="cursor: pointer;">{{ @$value->reimbursement_no }}</a></td>
                                            <td class="text-center"><a href="{{ url('get-url-deal-track', @$value->deal_code->code) }}" target="_blank" class="text-success">{{ @$value->deal_code->code }}</a></td>
                                            <td>{{ @$value->site_name }}</td>
                                            <td>{{ @$value->scope_of_work }}</td>
                                            <td>{{ @$value->invoice_no }}</td>
                                            <td class="text-start">{{ number_format((float)@$value->amount, 2) }}</td>
                                            <td class="text-start">{{ @$value->remarks }}</td>
                                            <td class="text-start">{{ @$value->head_count_name }}</td>
                                            <td class="text-start">{{ @$value->createdby->full_name }}</td>
                                            <td>
                                                @if ($value->accounts_status == 1)
                                                    <span class="success btn-badge py-1 px-2">Payment Processing Approved</span>
                                                    {{ @$value->accountsby->full_name }}
                                                @elseif($value->accounts_status == 2)
                                                    <span class="rejected btn-badge py-1 px-2">Payment Processing Rejected</span>
                                                    {{ @$value->accountsby->full_name }}
                                                @elseif($value->acco_head_status == 1)
                                                    <span class="success btn-badge py-1 px-2">Finance Approved</span>
                                                    {{ @$value->accoheadby->full_name }}
                                                @elseif($value->acco_head_status == 2)
                                                    <span class="rejected btn-badge py-1 px-2">Finance Rejected</span>
                                                    {{ @$value->accoheadby->full_name }}
                                                @elseif($value->dept_head_status == 1)
                                                    <span class="success btn-badge py-1 px-2">Reporting Manager Approved</span>
                                                    {{ @$value->deptheadby->full_name }}
                                                @elseif($value->dept_head_status == 2)
                                                    <span class="rejected btn-badge py-1 px-2">Reporting Manager Rejected</span>
                                                    {{ @$value->deptheadby->full_name }}
                                                @else
                                                    <span class="warning btn-badge py-1 px-2">New / Pending</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center align-items-center">
                                                    @php
                                                        $attachments = @$value->attachmant ?: @$value->attachment;
                                                        $attachmentFiles = $attachments ? explode('|', $attachments) : [];
                                                    @endphp
                                                    @foreach ($attachmentFiles as $f)
                                                        @php
                                                            $f = trim($f);
                                                            $attachmentUrl = strpos($f, '/') !== false ? asset($f) : asset('public/uploads/crm_amc_doc/' . $f);
                                                        @endphp
                                                        @if ($f != '')
                                                            <a class="btn btn-sm btn-light" href="{{ $attachmentUrl }}" target="_blank"><i class="ico icon-bold-download-minimalistic text-dark" style="font-size: 16px;"></i></a>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <div class="content-container col-9">
        <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
            
            <script>
                $(document).ready(function () {
                    $(document).on('click', '.data-item', function () {
                        $("#loading_bg").css("display", "block");
                        var id = $(this).data('id');

                        $('.data-item').removeClass('active');
                        $(this).addClass('active');

                        var newUrl = "{{ url('crm-reimbursement-track') }}/" + id;
                        window.history.pushState({
                            path: newUrl
                        }, '', newUrl);

                        var action = "{{ url('crm-reimbursement-track-details') }}/" + id;

                        $.ajax({            
                            url: action,
                            method: 'GET',
                            success: function (response) {
                                $('#data-details').html(response);
                            },
                            error: function () {
                                $('#data-details').html('<p class="text-danger">Error loading details.</p>');
                            },
                            complete: function () {
                                $("#loading_bg").css("display", "none");
                            }
                        });
                    });

                    // Server-side rendering handles the initial detail pane load,
                    // so we do not need to trigger an AJAX fetch on load.
                });
            </script>

            <script>
                $(document).ready(function(){
                    $('#search_invoice').on('input', function(){
                        var query = $(this).val();

                        $.ajax({
                            url: "{{ route('crm-reimbursement.search') }}",
                            type: "GET",
                            data: { query: query, context: 'track' },
                            success: function(data){
                                $('#short-list-items').html('');

                                if(data.length > 0){
                                    $.each(data, function(index, invoice){
                                        let amount = parseFloat(invoice.amount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                        let ims = `<li class="nav-item w-100" role="presentation">
                                            <button href="javascript:void(0)" class="nav-link data-item" data-id="${invoice.id}">
                                                <div class="row w-100">
                                                    <div class="col-12">
                                                        <label class="form-control-plaintext truncate-text">
                                                            ${invoice.customer_name ?? 'N/A'}
                                                        </label>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="form-control-plaintext" style="font-size: 11px">${invoice.reimbursement_no}</div>
                                                    </div>
                                                    <div class="col-4 pl-2">
                                                        <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                                                            ${invoice.date ? invoice.date.split('-').reverse().join('/') : ''}
                                                        </div>
                                                    </div>
                                                    <div class="col-4 text-end">
                                                        <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                                                            ${amount} ${invoice.currency_code}
                                                        </div>
                                                    </div>
                                                </div>
                                            </button>
                                        </li>`;
                                        $('#short-list-items').append(ims);
                                    });
                                } else {
                                    $('#short-list-items').html('<div class="p-2">No results found</div>');
                                }
                            }
                        });
                    });
                });
            </script>

            <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
                @if (isset($selectedReimbursement))
                    @include('backEnd.amc.reimbursement_track_detail', ['selectedReimbursement' => $selectedReimbursement, 'staff' => $staff])
                @endif
            </div>
        </div>
    </div>

    <script>
        const leftNav = document.querySelector('.left-nav');
        const content = document.querySelector('.content-container');
        const hasAppliedFilters = {{ count(request()->query()) > 0 ? 'true' : 'false' }};
        let state = localStorage.getItem("leftNavState");
        
        const hasTrackIdInUrl = /\/crm-reimbursement-track\/\d+/.test(window.location.pathname);
        if (hasTrackIdInUrl) {
            state = "collapsed";
            localStorage.setItem("leftNavState", "collapsed");
        } else if (hasAppliedFilters) {
            state = "expanded";
            localStorage.setItem("leftNavState", "expanded");
        }
        
        if (state === "expanded") {
            leftNav.classList.remove('col-3');
            leftNav.classList.add('col-12');
            if (content) {
                content.classList.remove('col-9');
                content.classList.add('col-0');
            }
            $('#short-title').hide();
            $('#short-list').hide();
            $('#short-list-items').hide();
            $('#long-list').show();
            if (hasAppliedFilters) {
                $('#search_box').show();
            }
        } else if (state === "collapsed") {
            leftNav.classList.remove('col-12');
            leftNav.classList.add('col-3');
            if (content) {
                content.classList.remove('col-0');
                content.classList.add('col-9');
            }
            $('#short-title').show();
            $('#short-list').show();
            $('#short-list-items').show();
            $('#long-list').hide();
        }
    </script>
    
    <script>
        function search_box_show_hide() {
            $("#search_box").slideToggle(200);
        }

        $(document).ready(function() {
            // Function to toggle row expansion on click
            $('.long-list tbody tr').on('click', function(e) {
                // If they clicked the data-item (Reimb No link), don't expand the row, let it load the details pane
                if ($(e.target).closest('.data-item').length > 0) {
                    return;
                }
                
                // Toggle expand class to show full text
                if ($(this).hasClass('expand')) {
                    $(this).removeClass('expand');
                } else {
                    $('.long-list tbody tr').removeClass('expand');
                    $(this).addClass('expand');
                }
            });
        });
    </script>


    <script src="https://cdn.jsdelivr.net/npm/exceljs@4.3.0/dist/exceljs.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#exportExcelDealTrack').on('click', function (e) {
                e.preventDefault();

                var companyName = @json(@App\SysCompany::find(session('logged_session_data.company_id') ?? '')->trade_name ?? '');
                var totalDeals = @json($data->count() ?? 0);

                var $table = $('#long-list table');

                var visibleColIndexes = [];
                var headerLabels = [];

                $table.find('thead tr th').each(function (i) {
                    if ($(this).css('display') !== 'none') {
                        var label = $(this).text().trim();
                        if (['actions', 'action', 'actions '].includes(label.toLowerCase().trim())) {
                            return;
                        }
                        visibleColIndexes.push(i);
                        headerLabels.push(label);
                    }
                });

                var rows = [];
                rows.push([companyName]);
                rows.push(['Reimbursement Track (' + totalDeals + ')']);

                rows.push([]);
                rows.push(headerLabels);

                $table.find('tbody tr').each(function () {
                    var $cells = $(this).find('td');
                    var rowData = [];
                    visibleColIndexes.forEach(function (i) {
                        var cellText = $cells.eq(i).text().trim().replace(/\s+/g, ' ');
                        rowData.push(cellText);
                    });
                    rows.push(rowData);
                });

                if (rows.length <= 4) {
                    alert('No data available for export');
                    return;
                }

                var N = headerLabels.length || 1;
                var workbook = new ExcelJS.Workbook();
                var worksheet = workbook.addWorksheet('ReimbursementTrack');
                var wsCols = [];
                for (var ci = 0; ci < N; ci++) { wsCols.push({ width: 22 }); }
                worksheet.columns = wsCols;

                var hdrIdx = rows.indexOf(headerLabels);
                if (hdrIdx < 0) hdrIdx = rows.length - 1;

                var wsRowNum = 0;
                for (var ri = 0; ri < hdrIdx; ri++) {
                    if (!(rows[ri] && rows[ri][0])) continue;
                    wsRowNum++;
                    var wsRow = worksheet.addRow([]);
                    wsRow.height = ri === 0 ? 26 : ri === 1 ? 20 : 16;
                    if (N > 1) worksheet.mergeCells(wsRowNum, 1, wsRowNum, N);
                    wsRow.getCell(1).value = rows[ri][0] || '';
                    if (ri === 0) wsRow.getCell(1).font = { bold: true, size: 14 };
                    else if (ri === 1) wsRow.getCell(1).font = { bold: true, size: 12 };
                    wsRow.getCell(1).alignment = { horizontal: 'center', vertical: 'middle' };
                }

                wsRowNum++;
                worksheet.addRow([]);

                wsRowNum++;
                var wsHdrRow = worksheet.addRow(headerLabels);
                wsHdrRow.height = 20;
                wsHdrRow.eachCell({ includeEmpty: true }, function (cell) {
                    cell.font      = { bold: true, color: { argb: 'FFFFFFFF' }, size: 11 };
                    cell.fill      = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF2D5496' } };
                    cell.alignment = { horizontal: 'center', vertical: 'middle' };
                    cell.border    = {
                        top: {style:'thin'}, left: {style:'thin'}, bottom: {style:'thin'}, right: {style:'thin'}
                    };
                });

                for (var ri = hdrIdx + 1; ri < rows.length; ri++) {
                    wsRowNum++;
                    var dataRow = worksheet.addRow(rows[ri]);
                    dataRow.eachCell({ includeEmpty: true }, function (cell) {
                        cell.alignment = { vertical: 'middle', wrapText: true };
                        cell.border = {
                            top: {style:'thin'}, left: {style:'thin'}, bottom: {style:'thin'}, right: {style:'thin'}
                        };
                    });
                }

                workbook.xlsx.writeBuffer().then(function (buffer) {
                    var blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                    var url = window.URL.createObjectURL(blob);
                    var a = document.createElement('a');
                    a.href = url;
                    a.download = 'Reimbursement_Track.xlsx';
                    document.body.appendChild(a);
                    a.click();
                    setTimeout(function() {
                        window.URL.revokeObjectURL(url);
                        document.body.removeChild(a);
                    }, 0);
                });
            });
        });
    </script>
@endsection

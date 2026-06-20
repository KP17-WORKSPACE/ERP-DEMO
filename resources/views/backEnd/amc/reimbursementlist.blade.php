@extends('backEnd.newmasterpage')
@section('mainContent')
@php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    $reimbursementPermissions = $reimbursementPermissions ?? ['create' => false, 'view' => false, 'edit' => false, 'delete' => false, 'export' => false, 'attach' => false];
    $reimbursementTrackPermissions = $reimbursementTrackPermissions ?? ['create' => false, 'view' => false, 'edit' => false, 'delete' => false, 'export' => false, 'attach' => false];
    $hasAppliedFilters = request()->has('filter_open') || request()->has('from_date') || request()->has('to_date') || request()->has('expense_category') || request()->has('invoice_no') || request()->has('invoice_date') || request()->has('amount') || request()->has('reimbursement_no') || request()->has('deal_id') || request()->has('project_id') || request()->has('submitted_by') || request()->has('status_filter') || request()->has('attachments') || request()->has('filter_by');
    $expenseCategories = [
        'Travel Expenses',
        'Fuel Expenses',
        'Taxi / Transportation',
        'Air Ticket',
        'Hotel Accommodation',
        'Meals & Entertainment',
        'Client Meeting Expenses',
        'Site Visit Expenses',
        'Courier & Logistics',
        'Telephone / Mobile',
        'Internet Expenses',
        'Training & Certification',
        'Medical Expenses',
        'Visa Expenses',
        'Office Supplies',
        'Parking & Toll Charges',
        'Vehicle Maintenance',
        'Customs Clearance Expenses',
        'Marketing & Promotion',
        'Miscellaneous Expenses',
        'Food Expenses',
        'Other'
    ];
@endphp

<script>
    let isFullList = false;

    function list_style_new() {
        const leftNav = document.querySelector('.left-nav');
        const content = document.querySelector('.content-container');

        if (!isFullList) {
            isFullList = true;
            leftNav.classList.remove('col-3');
            leftNav.classList.add('col-12');
            leftNav.style.width = '100%';

            if (content) {
                content.classList.add('d-none');
            }

            $('#short-title').hide();
            $('#short-list').hide();
            $('#short-list-items').hide();
            $('#long-list').show();
        } else {
            isFullList = false;
            leftNav.classList.remove('col-12');
            leftNav.classList.add('col-3');
            leftNav.style.width = '';

            if (content) {
                content.classList.remove('d-none');
            }

            $('#short-title').show();
            $('#short-list').show();
            $('#short-list-items').show();
            $('#long-list').hide();
            $('#search_box').hide();
        }
    }

    function search_box_show_hide() {
        $('#search_box').slideToggle(200);
    }
</script>

<aside class="left-nav col-3" id="leftSidebar">
    <style>
        #long-list_filter,
        .dataTables_filter,
        .dataTables_wrapper .dataTables_filter {
            display: none !important;
        }

        .long-list td,
        .long-list th {
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

        .long-list tbody tr {
            cursor: pointer;
        }
    </style>

    <div class="resizer" id="sidebarResizer"></div>
    <h4 class="mb-2" id="short-title">Reimbursement List</h4>

    <div class="search-filter-container mb-4" id="short-list">
        <div class="input-group flex-nowrap">
            <input type="text" class="form-control" id="search_invoice" placeholder="Document No" aria-label="Search" aria-describedby="addon-wrapping">
        </div>
        <button type="button" class="btn btn-light" id="list_style_button" onclick="list_style_new()" style="height: 32px;">
            <i class="ico icon-outline-list-down"></i>
        </button>
    </div>

    <div class="left-nav-list" id="invoice_list">
        <ul id="short-list-items" class="nav flex-column nav-pills" role="tablist">
            @if(count($data) > 0)
                @foreach($data as $value)
                    <li class="nav-item w-100" role="presentation">
                        <button href="javascript:void(0)" class="nav-link data-item {{ $active_id == $value->id ? 'active' : '' }}" data-id="{{ $value->id }}">
                            <div class="row w-100">
                                <div class="col-12">
                                    <label class="form-control-plaintext truncate-text">{{ $value->deal_code->customername->name ?? $value->site_name ?? 'N/A' }}</label>
                                </div>
                                <div class="col-4">
                                    <div class="form-control-plaintext" style="font-size:11px">{{ $value->reimbursement_no }}</div>
                                </div>
                                <div class="col-4 pl-2">
                                    <div class="form-control-plaintext truncate-text" style="font-size:11px">{{ $value->date ? date('d/m/Y', strtotime($value->date)) : '' }}</div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="form-control-plaintext truncate-text" style="font-size:11px">
                                        {{ number_format((float)$value->amount, 2) }} {{ $value->currencycode->code ?? '' }}
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
                <h4 class="mb-0">Reimbursement List</h4>

                <div class="search-filter-container mb-0">
                    <input type="text" id="tableSearch" class="form-control d-inline-block" style="font-size:13px;width: 350px;margin-right: 10px" placeholder="Search">

                    @if(!empty($reimbursementPermissions['export']))
                        <a href="{{ url('crm-reimbursement-request-export') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}" class="btn btn-light" style="margin-right: 10px;">
                            <i class="ico icon-outline-export text-success"></i> Export
                        </a>
                    @endif

                    <button type="button" class="btn btn-light" onclick="search_box_show_hide()" style="margin-right: 10px;">
                        <i class="ico icon-outline-magnifer"></i>
                    </button>

                    @if(!empty($reimbursementPermissions['export']) || !empty($reimbursementTrackPermissions['view']))
                        <div class="dropdown" style="display: inline-block; margin-right: 10px;">
                            <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ico icon-outline-hamburger-menu"></i>
                            </button>
                            <ul class="dropdown-menu">
                                @if(!empty($reimbursementPermissions['export']))
                                    <li>
                                        <a class="dropdown-item" href="{{ url('crm-reimbursement-request-export') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}">
                                            <i class="ico icon-outline-export"></i> Download / Export
                                        </a>
                                    </li>
                                @endif
                                @if(!empty($reimbursementTrackPermissions['view']))
                                    <li>
                                        <a class="dropdown-item" href="{{ url('crm-reimbursement-track') }}">
                                            <i class="ico icon-outline-list-down"></i> Reimbursement Track
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    @endif

                    <button type="button" class="btn btn-light" id="list_style_button" onclick="list_style_new()">
                        <i class="ico icon-outline-list-down"></i>
                    </button>
                </div>
            </div>

            <div class="search-filter-container mt-1 mb-4 filter-field border" id="search_box" style="display: {{ $hasAppliedFilters ? 'block' : 'none' }};">
                <div class="card">
                    <div class="card-body">
                        {{ Form::open(['class' => 'form-horizontal', 'url' => 'crm-reimbursement-request', 'method' => 'get', 'id' => 'crm-reimbursement-search']) }}
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
                                    @foreach($expenseCategories as $category)
                                        <option value="{{ $category }}" @if(@$ctrl_expense_category == $category) selected @endif>{{ $category }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-1-5 mb-2 filter-field">
                                <label for="" class="form-label">Invoice Number</label>
                                <input class="form-control" type="text" autocomplete="off" name="invoice_no" value="{{ $ctrl_invoice_no ?? '' }}">
                            </div>
                            <div class="col-1-5 mb-2 filter-field">
                                <label for="" class="form-label">Invoice Date</label>
                                <input class="form-control date-picker" type="text" autocomplete="off" name="invoice_date" id="filter_invoice_date" value="{{ $ctrl_invoice_date ?? '' }}">
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
                                    <option value="draft" @if(@$ctrl_status == 'draft') selected @endif>Draft</option>
                                    <option value="new_pending" @if(@$ctrl_status == 'new_pending') selected @endif>New / Pending</option>
                                    <option value="dept_head_approved" @if(@$ctrl_status == 'dept_head_approved') selected @endif>Reporting Manager Approved</option>
                                    <option value="dept_head_rejected" @if(@$ctrl_status == 'dept_head_rejected') selected @endif>Reporting Manager Rejected</option>
                                    <option value="accounts_head_approved" @if(@$ctrl_status == 'accounts_head_approved') selected @endif>Finance Approved</option>
                                    <option value="accounts_head_rejected" @if(@$ctrl_status == 'accounts_head_rejected') selected @endif>Finance Rejected</option>
                                    <option value="accounts_approved" @if(@$ctrl_status == 'accounts_approved') selected @endif>Payment Processing Approved</option>
                                    <option value="accounts_rejected" @if(@$ctrl_status == 'accounts_rejected') selected @endif>Payment Processing Rejected</option>
                                </select>
                            </div>
                            <div class="col-1-5 mb-2 filter-field">
                                <label for="" class="form-label">Attachment</label>
                                <select class="form-control" name="attachments" id="attachments">
                                    <option value="">Select</option>
                                    <option value="1" @if(@$ctrl_attachments == 1) selected @endif>With Attachments Only</option>
                                    <option value="2" @if(@$ctrl_attachments == 2) selected @endif>Without Attachments Only</option>
                                    <option value="3" @if(@$ctrl_attachments == 3) selected @endif>All</option>
                                </select>
                            </div>
                            <div class="col-1-5 mb-2 filter-field">
                                <label for="" class="form-label">Filter By</label>
                                <select class="form-control" name="filter_by" id="filter_by">
                                    <option value="" @if(@$filter_by == '') selected @endif>-Select-</option>
                                    <option value="this_month" @if(@$filter_by == 'this_month') selected @endif>This Month</option>
                                    <option value="today" @if(@$filter_by == 'today') selected @endif>Today</option>
                                    <option value="this_week" @if(@$filter_by == 'this_week') selected @endif>This Week</option>
                                    <option value="last_week" @if(@$filter_by == 'last_week') selected @endif>Last Week</option>
                                    <option value="last_month" @if(@$filter_by == 'last_month') selected @endif>Last Month</option>
                                    <option value="this_quarter" @if(@$filter_by == 'this_quarter') selected @endif>This Quarter</option>
                                    <option value="pre_quarter" @if(@$filter_by == 'pre_quarter') selected @endif>Previous Quarter</option>
                                    <option value="this_year" @if(@$filter_by == 'this_year') selected @endif>This Year</option>
                                    <option value="last_year" @if(@$filter_by == 'last_year') selected @endif>Last Year</option>
                                </select>
                            </div>
                            <div class="col-md-3 filter-field">
                                <div class="d-flex align-items-center mt-4">
                                    <button type="submit" class="btn btn-light me-2" style="width:auto;">
                                        <i class="ico icon-outline-magnifer"></i> Filter
                                    </button>
                                    <a href="{{ url('crm-reimbursement-request') }}?filter_open=1" class="btn btn-light" style="width:auto;">
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
                                    <th style="width: 90px;" class="text-start">@lang('Submitted By')</th>
                                    <th style="width: 100px;">@lang('Status')</th>
                                    <th class="text-center" style="width: 100px;">@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody style="font-size:12px">
                                @foreach($data as $value)
                                    @php
                                        $ownsReimbursement = (int) $value->created_by === (int) Auth::id() || (int) $value->employee_id === (int) Auth::id();
                                        $canEditRow = (!empty($reimbursementPermissions['edit']) && $ownsReimbursement) || ($ownsReimbursement && (int) $value->approval_status === 0) || in_array(Auth::user()->role_id, [1, 2]);
                                        $canDeleteRow = !empty($reimbursementPermissions['delete']) && ($ownsReimbursement || in_array(Auth::user()->role_id, [1, 2]));
                                        $canDownloadAttachment = !empty($reimbursementPermissions['attach']) || !empty($reimbursementPermissions['view']);
                                    @endphp
                                    <tr class="{{ $value->deleted_at ? 'bg-dark' : '' }}">
                                        <td class="text-center">{{ $value->date ? date('d/m/Y', strtotime($value->date)) : '' }}</td>
                                        <td class="text-start"><a href="{{ url('crm-reimbursement-track/' . @$value->id) }}" target="_blank" class="text-success">{{ @$value->reimbursement_no }}</a></td>
                                        <td class="text-center"><a href="{{ url('get-url-deal-track', @$value->deal_code->code) }}" target="_blank" class="text-success">{{ @$value->deal_code->code }}</a></td>
                                        <td>{{ @$value->site_name }}</td>
                                        <td>{{ @$value->scope_of_work }}</td>
                                        <td>{{ @$value->invoice_no }}</td>
                                        <td class="text-start">{{ number_format((float)@$value->amount, 2) }}</td>
                                        <td class="text-start">{{ @$value->remarks }}</td>
                                        <td class="text-start">{{ @$value->head_count_name }}</td>
                                        <td class="text-start">{{ @$value->createdby->full_name }}</td>
                                        <td>
                                            @if((int)$value->approval_status === 0)
                                                <span class="warning btn-badge py-1 px-2">Draft</span>
                                            @elseif($value->accounts_status == 1)
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
                                            <div class="d-flex justify-content-start align-items-center">
                                                @if($canEditRow)
                                                    <a class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#ModalService" onclick='fun_edit({{ $value->id }}, @json($value->reimbursement_no))'><i class="ico icon-outline-pen-2 text-dark" style="font-size: 16px;"></i></a>
                                                @endif

                                                @php
                                                    $attachments = @$value->attachmant ?: @$value->attachment;
                                                    $attachmentFiles = $attachments ? explode('|', $attachments) : [];
                                                @endphp

                                                @if($canDeleteRow)
                                                    @if($value->status == 1)
                                                        <a class="btn btn-sm btn-light" onclick="fun_delete({{ $value->id }})"><i class="ico icon-bold-trash-bin-minimalistic-2" style="font-size: 16px" aria-hidden="true"></i></a>
                                                    @else
                                                        <a class="btn btn-sm btn-light" onclick="fun_restore({{ $value->id }})"><i class="ico icon-bold-restart text-dark" style="font-size: 16px;"></i></a>
                                                    @endif
                                                @endif

                                                @if($canDownloadAttachment)
                                                    @foreach($attachmentFiles as $f)
                                                        @php
                                                            $f = trim($f);
                                                            $attachmentUrl = strpos($f, '/') !== false ? asset($f) : asset('public/uploads/crm_amc_doc/' . $f);
                                                        @endphp
                                                        @if($f != '')
                                                            <a class="btn btn-sm btn-light" href="{{ $attachmentUrl }}" target="_blank"><i class="ico icon-bold-download-minimalistic text-dark" style="font-size: 16px;"></i></a>
                                                        @endif
                                                    @endforeach
                                                @endif
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
    <div class="tab-content display-flex-tabs" id="reimbursementRequestTabContent">
        <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
            @if(isset($selectedReimbursement))
                @include('backEnd.amc.reimbursement_track_detail', ['selectedReimbursement' => $selectedReimbursement, 'staff' => $staff, 'submitter' => $submitter])
            @else
                <div class="purchase-order-content-header sticky-top" style="background-color: #f7f8fd">
                    <h4 class="purchase-order-content-header-left">Reimbursement List</h4>
                    <div class="purchase-order-content-header-right">
                        @if(!empty($reimbursementPermissions['create']))
                            <button type="button" class="btn btn-light text-dark" data-bs-toggle="modal" data-bs-target="#ModalService" onclick="prepareAddReimbursementForm()">
                                <i class="ico icon-outline-add-square text-success"></i> Add
                            </button>
                        @endif
                        <div class="dropdown" style="display: inline-block; margin-left: 5px;">
                            <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ico icon-outline-hamburger-menu"></i>
                            </button>
                            <ul class="dropdown-menu">
                                @if(!empty($reimbursementTrackPermissions['view']))
                                    <li><a class="dropdown-item" href="{{ url('crm-reimbursement-track') }}"><i class="ico icon-outline-list-down"></i> Reimbursement Track</a></li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="d-none" id="reimbursement-edit-data">
    @foreach($data as $value)
        <input type="hidden" id="edit_reimbursement_no_{{ $value->id }}" value="{{ $value->reimbursement_no }}">
        <input type="hidden" id="edit_date_{{ $value->id }}" value="{{ $value->date ? date('d/m/Y', strtotime($value->date)) : '' }}">
        <input type="hidden" id="edit_deal_id_{{ $value->id }}" value="{{ $value->deal_code->code ?? '' }}">
        <input type="hidden" id="edit_site_name_{{ $value->id }}" value="{{ $value->site_name }}">
        <input type="hidden" id="edit_scope_of_work_{{ $value->id }}" value="{{ $value->scope_of_work }}">
        <input type="hidden" id="edit_invoice_no_{{ $value->id }}" value="{{ $value->invoice_no }}">
        <input type="hidden" id="edit_invoice_date_{{ $value->id }}" value="{{ $value->invoice_date ? \Carbon\Carbon::parse($value->invoice_date)->format('d/m/Y') : '' }}">
        <input type="hidden" id="edit_amount_{{ $value->id }}" value="{{ $value->amount ? number_format((float)$value->amount, 2) : '' }}">
        <input type="hidden" id="edit_reimbursable_amount_{{ $value->id }}" value="{{ $value->reimbursable_amount ? number_format((float)$value->reimbursable_amount, 2) : '' }}">
        <input type="hidden" id="edit_payment_method_{{ $value->id }}" value="{{ $value->payment_method }}">
        <input type="hidden" id="edit_project_id_{{ $value->id }}" value="{{ $value->project_id }}">
        <input type="hidden" id="edit_vendor_name_{{ $value->id }}" value="{{ $value->vendor_name }}">
        <input type="hidden" id="edit_currency_id_{{ $value->id }}" value="{{ $value->currency_id }}">
        <input type="hidden" id="edit_remarks_{{ $value->id }}" value="{{ $value->remarks }}">
        <input type="hidden" id="edit_head_count_name_{{ $value->id }}" value="{{ $value->head_count_name }}">
        <input type="hidden" id="edit_employee_id_{{ $value->id }}" value="{{ $value->employee_id }}">
        <input type="hidden" id="edit_attachment_remarks_{{ $value->id }}" value="{{ $value->attachment_remarks }}">
        <input type="hidden" id="edit_attachment_file_{{ $value->id }}" value="{{ $value->attachmant ?: $value->attachment }}">
        <input type="hidden" id="edit_approval_status_{{ $value->id }}" value="{{ (int)$value->approval_status }}">
    @endforeach
</div>

<div class="modal side-panel fade" id="ModalService" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="left: 30%">
        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-reimbursement-request-add', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-reimbursement-request-add']) }}
        <input type="hidden" name="edit_id" id="edit_id">
        <input type="hidden" name="approval_action" id="approval_action" value="submit">

        <div class="modal-content" style="max-height: 80vh">
            <div class="modal-header">
                <h4 class="modal-title" id="editModalLabel">New ({{ $next_reimbursement_no }})</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body">
                        <div class="row gap-rows">
                            <div class="col-md-3">
                                <label for="" class="form-label">Expense Date*</label>
                                <input type="text" class="form-control date-picker" name="date" id="date" value="{{ date('d/m/Y') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="" class="form-label">Expense Category*</label>
                                <select class="form-control js-example-basic-single" name="remarks" id="remarks" onchange="remarks_change()" required>
                                    <option value="">-Select-</option>
                                    @foreach($expenseCategories as $category)
                                        <option value="{{ $category }}">{{ $category }}</option>
                                    @endforeach
                                </select>
                                <input type="text" class="form-control" name="remarks_other" id="remarks_other" style="display: none; margin-top: 5px;" placeholder="Specify other category">
                            </div>
                            <div class="col-md-3">
                                <label for="" class="form-label">Invoice No.</label>
                                <input type="text" class="form-control text-end" name="invoice_no" id="invoice_no">
                            </div>
                            <div class="col-md-3">
                                <label for="" class="form-label">Invoice Date</label>
                                <input type="text" class="form-control date-picker" name="invoice_date" id="invoice_date" value="{{ date('d/m/Y') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="" class="form-label">Amount*</label>
                                <input type="text" class="form-control text-end" name="amount" id="amount" required>
                            </div>
                            <div class="col-md-3">
                                <label for="" class="form-label">Reimbursable Amount</label>
                                <input type="text" class="form-control text-end" name="reimbursable_amount" id="reimbursable_amount">
                            </div>
                            <div class="col-md-3">
                                <label for="" class="form-label">Payment Method</label>
                                <select class="form-control js-example-basic-single" name="payment_method" id="payment_method">
                                    <option value="">-Select-</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Personal Card">Personal Card</option>
                                    <option value="Company Card">Company Card</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                    <option value="Personal Account">Personal Account</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="" class="form-label">Scope of Work*</label>
                                <input type="text" class="form-control" name="scope_of_work" id="scope_of_work" required>
                            </div>
                            <div class="col-md-3">
                                <label for="" class="form-label">Deal ID</label>
                                <input type="text" class="form-control" name="deal_id" id="deal_id" onchange="get_custName()">
                            </div>
                            <div class="col-md-3">
                                <label for="" class="form-label">Project ID</label>
                                <input type="text" class="form-control" name="project_id" id="project_id">
                            </div>
                            <div class="col-md-6">
                                <label for="" class="form-label">Customer Name</label>
                                <input type="text" class="form-control" name="site_name" id="site_name">
                            </div>
                            <div class="col-md-3">
                                <label for="" class="form-label">Currency</label>
                                <select class="form-control js-example-basic-single" name="currency_id" id="currency_id">
                                    @foreach($currencies as $curr)
                                        <option value="{{ $curr->id }}" {{ $default_currency == $curr->id ? 'selected' : '' }}>{{ $curr->code }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="" class="form-label">Head Count & Name</label>
                                <input type="text" class="form-control" name="head_count_name" id="head_count_name" value="1">
                            </div>
                            <div class="col-md-6">
                                <label for="" class="form-label">Vendor Name</label>
                                <input type="text" class="form-control" name="vendor_name" id="vendor_name">
                            </div>
                            <div class="col-md-6">
                                <label for="" class="form-label">Employee <small>(Optional)</small></label>
                                <select class="form-control js-example-basic-single" name="employee_id" id="employee_id">
                                    <option value="">-Select-</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->user_id }}">{{ $employee->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="" class="form-label">Attachment</label>
                                <input type="file" class="form-control" name="attachment" id="attachment">
                                <div id="existing_attachment_box" class="mt-1"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="" class="form-label">Remarks</label>
                                <input type="text" class="form-control" name="attachment_remarks" id="attachment_remarks">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-light add-btn ms-2" onclick="$('#approval_action').val('draft');">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                </button>
                <button type="submit" class="btn btn-light add-btn ms-2" onclick="$('#approval_action').val('submit');">
                    <i class="ico icon-outline-send-square text-success"></i> Save & Submit for Approval
                </button>
            </div>
        </div>
        {{ Form::close() }}
    </div>
</div>

<div class="modal side-panel fade" id="AccountsModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="left: 30%">
        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-reimbursement-request-account-approve', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-reimbursement-request-account-approve']) }}
        <div class="modal-content" style="max-height: 80vh">
            <div class="modal-header">
                <h4 class="modal-title">Accounts Approval</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body">
                        <label for="" class="form-label">Remarks</label>
                        <input class="form-control" type="text" name="remarks">
                        <input type="hidden" id="account_re_id" name="account_re_id">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" value="2" name="btn_status" class="btn btn-light add-btn ms-2"><i class="ico icon-outline-clipboard-remove text-success"></i> DisApprove</button>
                <button type="submit" value="1" name="btn_status" class="btn btn-light add-btn ms-2"><i class="ico icon-outline-bookmark-opened text-success"></i> Approve</button>
            </div>
        </div>
        {{ Form::close() }}
    </div>
</div>

<div class="modal side-panel fade" id="AccountsHeadModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="left: 30%">
        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-reimbursement-request-accounts-head-approve', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-reimbursement-request-accounts-head-approve']) }}
        <div class="modal-content" style="max-height: 80vh">
            <div class="modal-header">
                <h4 class="modal-title">Accounts Head Approval</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body">
                        <label for="" class="form-label">Remarks</label>
                        <input class="form-control" type="text" name="remarks">
                        <input type="hidden" id="acco_head_re_id" name="acco_head_re_id">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" value="2" name="btn_status" class="btn btn-light add-btn ms-2"><i class="ico icon-outline-clipboard-remove text-success"></i> DisApprove</button>
                <button type="submit" value="1" name="btn_status" class="btn btn-light add-btn ms-2"><i class="ico icon-outline-bookmark-opened text-success"></i> Approve</button>
            </div>
        </div>
        {{ Form::close() }}
    </div>
</div>

<div class="modal side-panel fade" id="DepartmentHeadModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="left: 30%">
        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-reimbursement-request-dept-head-approve', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-reimbursement-request-dept-head-approve']) }}
        <div class="modal-content" style="max-height: 80vh">
            <div class="modal-header">
                <h4 class="modal-title">Department Head Approval</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body">
                        <label for="" class="form-label">Remarks</label>
                        <input class="form-control" type="text" name="remarks">
                        <input type="hidden" id="dept_head_re_id" name="dept_head_re_id">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" value="2" name="btn_status" class="btn btn-light add-btn ms-2"><i class="ico icon-outline-clipboard-remove text-success"></i> DisApprove</button>
                <button type="submit" value="1" name="btn_status" class="btn btn-light add-btn ms-2"><i class="ico icon-outline-bookmark-opened text-success"></i> Approve</button>
            </div>
        </div>
        {{ Form::close() }}
    </div>
</div>

<script>
    function fun_account(id) {
        $('#account_re_id').val(id);
        $('#AccountsModal').modal('show');
    }

    function fun_account_head(id) {
        $('#acco_head_re_id').val(id);
        $('#AccountsHeadModal').modal('show');
    }

    function fun_dept_head(id) {
        $('#dept_head_re_id').val(id);
        $('#DepartmentHeadModal').modal('show');
    }

    function prepareAddReimbursementForm() {
        $('#ModalService').data('mode', 'add').data('reimbursement-no', '{{ $next_reimbursement_no }}');
        $('#crm-reimbursement-request-add').attr('action', "{{ url('crm-reimbursement-request-add') }}");
        $('#crm-reimbursement-request-add')[0].reset();
        $('#edit_id').val('');
        $('#approval_action').val('submit');
        reimbursableAmountManual = false;
        $('#ModalService #editModalLabel').text('New ({{ $next_reimbursement_no }})');
        $('#date').val('{{ date('d/m/Y') }}');
        $('#invoice_date').val('{{ date('d/m/Y') }}');
        $('#head_count_name').val('1');
        $('#currency_id').val('{{ $default_currency }}').trigger('change');
        $('#existing_attachment_box').html('');
        $('#remarks_other').hide().prop('required', false);
        $('#remarks').val('').trigger('change');
        $('#employee_id').val('').trigger('change');
        $('#payment_method').val('').trigger('change');
        showDuplicateInvoiceWarning(false);
    }

    function fun_edit(id, reimbursementNo) {
        var existingReimbursementNo = $.trim(reimbursementNo || $('#edit_reimbursement_no_' + id).val() || '');
        $('#ModalService').data('mode', 'edit').data('reimbursement-no', existingReimbursementNo);
        $('#crm-reimbursement-request-add').attr('action', "{{ url('crm-reimbursement-request-update') }}");
        $('#edit_id').val(id);
        $('#approval_action').val($('#edit_approval_status_' + id).val() == '0' ? 'draft' : 'submit');
        $('#ModalService #editModalLabel').text('Update (' + existingReimbursementNo + ')');
        $('#date').val($('#edit_date_' + id).val());
        $('#deal_id').val($('#edit_deal_id_' + id).val());
        $('#site_name').val($('#edit_site_name_' + id).val());
        $('#scope_of_work').val($('#edit_scope_of_work_' + id).val());
        $('#invoice_no').val($('#edit_invoice_no_' + id).val());
        $('#invoice_date').val($('#edit_invoice_date_' + id).val());
        $('#amount').val($('#edit_amount_' + id).val());
        $('#reimbursable_amount').val($('#edit_reimbursable_amount_' + id).val());
        reimbursableAmountManual = cleanAmountValue($('#amount').val()) !== cleanAmountValue($('#reimbursable_amount').val());
        $('#payment_method').val($('#edit_payment_method_' + id).val()).trigger('change');
        $('#project_id').val($('#edit_project_id_' + id).val());
        $('#vendor_name').val($('#edit_vendor_name_' + id).val());
        $('#currency_id').val($('#edit_currency_id_' + id).val()).trigger('change');
        $('#head_count_name').val($('#edit_head_count_name_' + id).val());
        $('#employee_id').val($('#edit_employee_id_' + id).val()).trigger('change');
        $('#attachment_remarks').val($('#edit_attachment_remarks_' + id).val());

        let category = $('#edit_remarks_' + id).val();
        let knownCategories = @json($expenseCategories);
        if (knownCategories.indexOf(category) >= 0) {
            $('#remarks').val(category).trigger('change');
            $('#remarks_other').hide().prop('required', false).val('');
        } else {
            $('#remarks').val('Other').trigger('change');
            $('#remarks_other').show().prop('required', true).val(category);
        }

        renderExistingAttachments($('#edit_attachment_file_' + id).val());
        showDuplicateInvoiceWarning(false);
    }

    function renderExistingAttachments(files) {
        let html = '';
        if (files) {
            files.split('|').forEach(function(file) {
                file = $.trim(file);
                if (file) {
                    let url = file.indexOf('/') >= 0 ? "{{ asset('') }}" + file : "{{ asset('public/uploads/crm_amc_doc') }}/" + file;
                    html += '<a class="text-success" href="' + url + '" target="_blank">' + file.split('/').pop() + '</a><br>';
                }
            });
        }
        $('#existing_attachment_box').html(html);
    }

    function cleanAmountValue(value) {
        return String(value || '').replace(/,/g, '').trim();
    }

    function formatAmountValue(value) {
        let cleanValue = cleanAmountValue(value);
        if (cleanValue === '' || isNaN(cleanValue)) {
            return '';
        }
        return parseFloat(cleanValue).toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    let reimbursableAmountManual = false;
    let lastDuplicateInvoiceMessage = '';

    function showDuplicateInvoiceWarning(show, message) {
        if (!show) {
            lastDuplicateInvoiceMessage = '';
            return;
        }

        message = message || 'Invoice number already exists.';
        if (lastDuplicateInvoiceMessage === message) {
            return;
        }

        lastDuplicateInvoiceMessage = message;
        if (typeof toastr !== 'undefined') {
            toastr.error(message);
        }
    }

    function syncReimbursableAmount() {
        if (!reimbursableAmountManual) {
            $('#reimbursable_amount').val(formatAmountValue($('#amount').val()));
        }
    }

    function checkDuplicateInvoice(callback) {
        let invoiceNo = $('#invoice_no').val();
        let amount = cleanAmountValue($('#amount').val());

        showDuplicateInvoiceWarning(false);

        if (!invoiceNo) {
            if (typeof callback === 'function') {
                callback(false);
            }
            return;
        }

        $.ajax({
            url: "{{ url('crm-reimbursement-request-check-invoice') }}",
            type: "GET",
            data: {
                invoice_no: invoiceNo,
                amount: amount,
                edit_id: $('#edit_id').val()
            },
            success: function(response) {
                let invoiceExists = response && response.invoice_exists;
                let sameAmountExists = response && response.exists;
                if (sameAmountExists) {
                    showDuplicateInvoiceWarning(true, 'Invoice number with this amount already exists.');
                } else if (invoiceExists) {
                    showDuplicateInvoiceWarning(true, 'Invoice number already exists.');
                } else {
                    showDuplicateInvoiceWarning(false);
                }
                if (typeof callback === 'function') {
                    callback(sameAmountExists);
                }
            },
            error: function() {
                if (typeof callback === 'function') {
                    callback(false);
                }
            }
        });
    }

    function remarks_change() {
        if ($('#remarks').val() === 'Other') {
            $('#remarks_other').show().prop('required', true);
        } else {
            $('#remarks_other').hide().prop('required', false).val('');
        }
    }

    function fun_delete(id) {
        var result = confirm("Are you sure you want to delete this?");
        if (!result) {
            return false;
        }
        $("#loading_bg").css("display", "block");
        var action = "{{ URL::to('crm-reimbursement-request-delete') }}";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                id: id,
            },
            cache: false,
            success: function(dataResult) {
                var dataResult = JSON.parse(dataResult);
                if (dataResult == "SUCCESS") {
                    alert('Deleted Successfully!');
                } else {
                    alert('Something went wrong, please try again!');
                }
                location.reload();
                $("#loading_bg").css("display", "none");
            }
        });
    }

    function fun_restore(id) {
        var result = confirm("Are you sure you want to restore this?");
        if (!result) {
            return false;
        }
        $("#loading_bg").css("display", "block");
        var action = "{{ URL::to('crm-reimbursement-request-restore') }}";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                id: id,
            },
            cache: false,
            success: function(dataResult) {
                var dataResult = JSON.parse(dataResult);
                if (dataResult == "SUCCESS") {
                    alert('Restored Successfully!');
                } else {
                    alert('Something went wrong, please try again!');
                }
                location.reload();
                $("#loading_bg").css("display", "none");
            }
        });
    }

    function get_custName() {
        $("#loading_bg").css("display", "block");
        var action = "{{ URL::to('crm-reimbursement-request-get-custname') }}";
        $.ajax({
            url: action,
            type: "GET",
            data: {
                _token: '{{ csrf_token() }}',
                deal_id: $('#deal_id').val(),
            },
            cache: false,
            success: function(dataResult) {
                var dataResult = JSON.parse(dataResult);
                if (dataResult['data'] != null && dataResult['data'].length > 0) {
                    $("#site_name").val(dataResult['data'][0].name);
                } else {
                    $("#site_name").val('');
                }
                $("#loading_bg").css("display", "none");
            }
        });
    }

    function loadReimbursementDetails(id) {
        $("#loading_bg").css("display", "block");
        $('.data-item').removeClass('active');
        $('.data-item[data-id="' + id + '"]').addClass('active');

        var newUrl = "{{ url('crm-reimbursement-request') }}#reimbursement-" + id;
        window.history.pushState({ path: newUrl }, '', newUrl);

        $.ajax({
            url: "{{ url('crm-reimbursement-track-details') }}/" + id + "?context=request",
            method: 'GET',
            success: function(response) {
                $('#data-details').html(response);
            },
            error: function() {
                $('#data-details').html('<p class="text-danger">Error loading details.</p>');
            },
            complete: function() {
                $("#loading_bg").css("display", "none");
            }
        });
    }

    $(document).ready(function() {
        $('#amount, #reimbursable_amount').on('focus', function() {
            $(this).val(cleanAmountValue($(this).val()));
        });

        $('#amount').on('input', function() {
            if (!reimbursableAmountManual) {
                $('#reimbursable_amount').val($(this).val());
            }
        });

        $('#amount').on('blur', function() {
            $(this).val(formatAmountValue($(this).val()));
            syncReimbursableAmount();
        });

        $('#reimbursable_amount').on('input', function() {
            reimbursableAmountManual = cleanAmountValue($(this).val()) !== '';
        });

        $('#reimbursable_amount').on('blur', function() {
            $(this).val(formatAmountValue($(this).val()));
        });

        $('#invoice_no, #amount').on('blur change', function() {
            checkDuplicateInvoice();
        });

        $('#crm-reimbursement-request-add').on('submit', function(e) {
            let form = this;

            if ($(form).data('duplicate-checked') === true) {
                $(form).data('duplicate-checked', false);
                return true;
            }

            e.preventDefault();
            checkDuplicateInvoice(function(exists) {
                if (!exists) {
                    $(form).data('duplicate-checked', true);
                    form.submit();
                }
            });
        });

        $('#ModalService').on('shown.bs.modal', function() {
            $('#ModalService .js-example-basic-single').select2({
                dropdownParent: $('#ModalService')
            });
            var mode = $('#ModalService').data('mode') || 'add';
            var reimbursementNo = $('#ModalService').data('reimbursement-no') || '{{ $next_reimbursement_no }}';
            $('#ModalService #editModalLabel').text((mode === 'edit' ? 'Update' : 'New') + ' (' + reimbursementNo + ')');
        });

        $('#ModalService').on('hidden.bs.modal', function() {
            prepareAddReimbursementForm();
        });

        $(document).on('click', '.data-item', function() {
            loadReimbursementDetails($(this).data('id'));
        });

        $('#search_invoice').on('input', function() {
            var query = $(this).val();
            $.ajax({
                url: "{{ route('crm-reimbursement.search') }}",
                type: "GET",
                data: { query: query, context: 'request' },
                success: function(data) {
                    $('#short-list-items').html('');

                    if (data.length > 0) {
                        $.each(data, function(index, invoice) {
                            let amount = parseFloat(invoice.amount || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            let dateValue = invoice.date ? invoice.date.split('-').reverse().join('/') : '';
                            let row = `<li class="nav-item w-100" role="presentation">
                                <button href="javascript:void(0)" class="nav-link data-item" data-id="${invoice.id}">
                                    <div class="row w-100">
                                        <div class="col-12">
                                            <label class="form-control-plaintext truncate-text">${invoice.customer_name || 'N/A'}</label>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-control-plaintext" style="font-size:11px">${invoice.reimbursement_no || ''}</div>
                                        </div>
                                        <div class="col-4 pl-2">
                                            <div class="form-control-plaintext truncate-text" style="font-size:11px">${dateValue}</div>
                                        </div>
                                        <div class="col-4 text-end">
                                            <div class="form-control-plaintext truncate-text" style="font-size:11px">${amount} ${invoice.currency_code || ''}</div>
                                        </div>
                                    </div>
                                </button>
                            </li>`;
                            $('#short-list-items').append(row);
                        });
                    } else {
                        $('#short-list-items').html('<div class="p-2">No results found</div>');
                    }
                }
            });
        });

        $('#tableSearch').on('input', function() {
            var value = $(this).val().toLowerCase();
            $('#long-list-table tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });

        $('.long-list tbody tr').on('click', function(e) {
            if ($(e.target).closest('.data-item, a, button').length > 0) {
                return;
            }
            $('.long-list tbody tr').removeClass('expand');
            $(this).addClass('expand');
        });

        @if($hasAppliedFilters)
            list_style_new();
            $('#search_box').show();
        @endif
    });
</script>
@endsection

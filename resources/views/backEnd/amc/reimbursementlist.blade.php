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


    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>

    <?php try { ?>

    <aside class="left-nav col-12" id="leftSidebar">


        <div class="long-list" id="filters-long">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Reimbursement List
                </h4>
                <div class="search-filter-container mb-0">


                    <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#ModalService">
                        <i class="ico icon-outline-add-square text-success"></i> Add
                    </button>

                    <button class="btn btn-light" onclick="toggleLongFilters()">
                        <i class="ico icon-outline-magnifer"></i>
                    </button>

                    <div class="dropdown">
                        <button class="btn btn-light text-dark dropdown-toggle syscom-dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ico icon-outline-hamburger-menu"></i>
                        </button>
                        <ul class="dropdown-menu" style="">
                            <li>
                                <a class="dropdown-item" href="{{ url('crm-reimbursement-track') }}">
                                    <i class="ico icon-outline-list"></i> Reimbursement Track
                                </a>
                            </li>
                        </ul>
                    </div>


                </div>
            </div>


        </div>

        <div class="left-nav-list">



            <div class="table-responsive mb-4 mt-4">
                <table id="long-list" class="table table-hover" style="table-layout: fixed;width:100%">

                    <thead class="text-center">
                        <tr>
                            <th width="70px">@lang('Expense Date')</th>

                            <th width="70px">@lang('Deal ID')</th>
                            <th width="130px" class="text-start">@lang('Customer Name')</th>
                            <th width="130px" class="text-start">@lang('Scope of Work')</th>
                            <th width="70px" class="text-start">@lang('Invoice No')</th>
                            <th width="70px" class="text-end">@lang('Amount')</th>
                            <th width="130px" class="text-start">@lang('Expense Category')</th>
                            <th width="130px" class="text-start">@lang('Head Count & Name')</th>
                            <th width="80px" class="text-start">@lang('Submited By')</th>
                            <th width="70px">@lang('Status')</th>
                            <th width="30px"><i class="ico icon-bold-paperclip"></i></th>

                            <th class="text-center" style="width: 100px;">@lang('Action')</th>
                        </tr>
                    </thead>


                    <tbody>

                        @foreach ($data as $value)
                            <tr @if ($value->status == 2) class="bg-dark" @endif>
                                <td class="text-center">{{ date('d/m/Y', strtotime(@$value->date)) }}</td>
                                <td class="text-center">{{ @$value->deal_code->code }}</td>
                                <td>{{ @$value->site_name }}</td>
                                <td>{{ @$value->scope_of_work }}</td>
                                <td>{{ @$value->invoice_no }}</td>
                                <td class="text-end">{{ @$value->amount }}</td>
                                <td class="text-start">{{ @$value->remarks }}</td>
                                <td class="text-start">{{ @$value->head_count_name }}</td>
                                <td class="text-start">{{ @$value->createdby->full_name }}</td>


                                <td>
                                    @if ($value->accounts_status == 1)
                                        <span class="success btn-badge py-1 px-2">Accounts Approved</span>
                                        {{ @$value->accountsby->full_name }}
                                    @elseif($value->accounts_status == 2)
                                        <span class="rejected btn-badge py-1 px-2">Accounts Rejected</span>
                                        {{ @$value->accountsby->full_name }}
                                    @elseif($value->acco_head_status == 1)
                                        <span class="success btn-badge py-1 px-2">Accounts Head Approved</span>
                                        {{ @$value->accoheadby->full_name }}
                                    @elseif($value->acco_head_status == 2)
                                        <span class="rejected btn-badge py-1 px-2">Accounts Head Rejected</span>
                                        {{ @$value->accoheadby->full_name }}
                                    @elseif($value->dept_head_status == 1)
                                        <span class="success btn-badge py-1 px-2">Dept Head Approved</span>
                                        {{ @$value->deptheadby->full_name }}
                                    @elseif($value->dept_head_status == 2)
                                        <span class="rejected btn-badge py-1 px-2">Dept Head Rejected</span>
                                        {{ @$value->deptheadby->full_name }}
                                    @else
                                        <span class="warning btn-badge py-1 px-2">New / Pending</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    @if ($value->attachmant != '')
                                        <?php $file = explode('|', $value->attachmant); ?>
                                        @foreach ($file as $f)
                                            <a class="text-dark"
                                                href="{{ asset('public/uploads/crm_amc_doc/') }}/{{ $f }}"
                                                target="_blank"><i class="ico icon-bold-paperclip"></i></a>
                                        @endforeach
                                    @endif
                                </td>
                                <td class="text-center">
                                    <input type="hidden" id="edit_date_{{ $value->id }}"
                                        value="{{ $value->date ? \Carbon\Carbon::parse($value->date)->format('d/m/Y') : '' }}" />
                                    <input type="hidden" id="edit_deal_id_{{ $value->id }}"
                                        value="{{ @$value->deal_code->code }}" />
                                    <input type="hidden" id="edit_site_name_{{ $value->id }}"
                                        value="{{ $value->site_name }}" />
                                    <input type="hidden" id="edit_scope_of_work_{{ $value->id }}"
                                        value="{{ $value->scope_of_work }}" />
                                    <input type="hidden" id="edit_invoice_no_{{ $value->id }}"
                                        value="{{ $value->invoice_no }}" />
                                    <input type="hidden" id="edit_amount_{{ $value->id }}"
                                        value="{{ $value->amount }}" />
                                    <input type="hidden" id="edit_remarks_{{ $value->id }}"
                                        value="{{ $value->remarks }}" />
                                    <input type="hidden" id="edit_head_count_name_{{ $value->id }}"
                                        value="{{ $value->head_count_name }}" />
                                    <input type="hidden" id="edit_invoice_date_{{ $value->id }}" value="{{ $value->invoice_date ? \Carbon\Carbon::parse($value->invoice_date)->format('d/m/Y') : '' }}" />
                                    <input type="hidden" id="edit_reimbursable_amount_{{ $value->id }}" value="{{ $value->reimbursable_amount }}" />
                                    <input type="hidden" id="edit_payment_method_{{ $value->id }}" value="{{ $value->payment_method }}" />
                                    <input type="hidden" id="edit_project_id_{{ $value->id }}" value="{{ $value->project_id }}" />
                                    <input type="hidden" id="edit_vendor_name_{{ $value->id }}" value="{{ $value->vendor_name }}" />
                                    <input type="hidden" id="edit_currency_id_{{ $value->id }}" value="{{ $value->currency_id }}" />
                                    <input type="hidden" id="edit_attachment_{{ $value->id }}" value="{{ $value->attachmant }}" />
                                    <input type="hidden" id="edit_reimbursement_no_{{ $value->id }}" value="{{ $value->reimbursement_no }}" />

                                    <div class="d-flex justify-content-start align-items-center">
                                        @if($value->dept_head_status != 1 && $value->acco_head_status != 1 && $value->accounts_status != 1)
                                            <a class="btn btn-sm btn-light" onclick="fun_edit({{ $value->id }})"><i
                                                    class="ico icon-outline-pen-2 text-dark" style="font-size: 16px;"></i></a>

                                            @if ($value->status == 1)
                                                <a class="btn btn-sm btn-light" onclick="fun_delete({{ $value->id }})"><i
                                                        class="ico icon-bold-trash-bin-minimalistic-2" style="font-size: 16px"
                                                        aria-hidden="true"></i></a>
                                            @else
                                                <a class="btn btn-sm btn-light" onclick="fun_restore({{ $value->id }})"><i
                                                        class="ico icon-bold-restart text-dark"
                                                        style="font-size: 16px;"></i></a>
                                            @endif
                                        @endif

                                        @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
                                            @if ($value->dept_head_status == 0)
                                                <a class="btn btn-sm btn-light"
                                                    onclick="fun_dept_head({{ $value->id }})">Update</a>
                                            @elseif($value->dept_head_status == 1 && $value->acco_head_status == 0)
                                                <a class="btn btn-sm btn-light"
                                                    onclick="fun_account_head({{ $value->id }})">Update</a>
                                            @elseif($value->dept_head_status == 1 && $value->acco_head_status == 1 && $value->accounts_status == 0)
                                                <a class="btn btn-sm btn-light"
                                                    onclick="fun_account({{ $value->id }})">Update</a>
                                            @else
                                            @endif
                                        @endif

                                        @if (Auth::user()->role_id == 28)
                                            <a class="btn btn-sm btn-light"
                                                onclick="fun_account({{ $value->id }})">Update</a>
                                        @endif

                                        @if (Auth::user()->role_id == 27)
                                            <a class="btn btn-sm btn-light"
                                                onclick="fun_account_head({{ $value->id }})">Update</a>
                                        @endif

                                        @if (Auth::user()->role_id == 8)
                                            <a class="btn btn-sm btn-light"
                                                onclick="fun_dept_head({{ $value->id }})">Update</a>
                                        @endif
                                    </div>



                                </td>
                            </tr>
                        @endforeach

                    </tbody>

                </table>
            </div>
        </div>
    </aside>





    <div class="modal side-panel  fade" id="ModalService" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" style="max-width: 90%;">
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-reimbursement-request-add', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-reimbursement-request-add']) }}

            <div class="modal-content" style="max-height: 80vh">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel">New ({{ $next_reimbursement_no }})</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body">
                            <div class="row gap-rows">
                                <div class="col-md-2">
                                    <label for="date" class="form-label"> Expense Date*</label>
                                    <input type="text" class="form-control date-picker" name="date" id="date" value="{{ date('d/m/Y') }}" required>
                                </div>
                                <div class="col-md-2">
                                    <label for="remarks" class="form-label">Expense Category*</label>
                                    <select class="form-control" name="remarks" id="remarks" onchange="remarks_change()">
                                        <option value="">-Select-</option>
                                        <option value="Travel Expenses">Travel Expenses</option>
                                        <option value="Fuel Expenses">Fuel Expenses</option>
                                        <option value="Taxi / Transportation">Taxi / Transportation</option>
                                        <option value="Air Ticket">Air Ticket</option>
                                        <option value="Hotel Accommodation">Hotel Accommodation</option>
                                        <option value="Meals & Entertainment">Meals & Entertainment</option>
                                        <option value="Client Meeting Expenses">Client Meeting Expenses</option>
                                        <option value="Site Visit Expenses">Site Visit Expenses</option>
                                        <option value="Courier & Logistics">Courier & Logistics</option>
                                        <option value="Telephone / Mobile">Telephone / Mobile</option>
                                        <option value="Internet Expenses">Internet Expenses</option>
                                        <option value="Training & Certification">Training & Certification</option>
                                        <option value="Medical Expenses">Medical Expenses</option>
                                        <option value="Visa Expenses">Visa Expenses</option>
                                        <option value="Office Supplies">Office Supplies</option>
                                        <option value="Parking & Toll Charges">Parking & Toll Charges</option>
                                        <option value="Vehicle Maintenance">Vehicle Maintenance</option>
                                        <option value="Customs Clearance Expenses">Customs Clearance Expenses</option>
                                        <option value="Marketing & Promotion">Marketing & Promotion</option>
                                        <option value="Miscellaneous Expenses">Miscellaneous Expenses</option>
                                        <option value="Other">Other</option>
                                    </select>
                                    <input type="text" class="form-control mt-1" name="remarks_other" id="remarks_other" style="display: none;" placeholder="Please specify">
                                    <script>
                                        function remarks_change() {
                                            if ($('#remarks').val() == "Other") {
                                                $('#remarks_other').css('display', 'block');
                                                $('#remarks_other').prop("required", true);
                                            } else {
                                                $('#remarks_other').css('display', 'none');
                                                $('#remarks_other').prop("required", false);
                                            }
                                        }
                                    </script>
                                </div>
                                <div class="col-md-2">
                                    <label for="invoice_no" class="form-label">Invoice No.</label>
                                    <input type="text" class="form-control" name="invoice_no" id="invoice_no">
                                </div>
                                <div class="col-md-2">
                                    <label for="invoice_date" class="form-label">Invoice Date</label>
                                    <input type="text" class="form-control date-picker" name="invoice_date" id="invoice_date" value="{{ date('d/m/Y') }}">
                                </div>
                                <div class="col-md-2">
                                    <label for="amount" class="form-label">Amount*</label>
                                    <input type="number" class="form-control" name="amount" step="Any" id="amount" required>
                                </div>
                                <div class="col-md-2">
                                    <label for="reimbursable_amount" class="form-label">Reimbursable Amount</label>
                                    <input type="number" class="form-control" name="reimbursable_amount" step="Any" id="reimbursable_amount">
                                </div>
                                <div class="col-md-2">
                                    <label for="payment_method" class="form-label">Payment Method</label>
                                    <select class="form-control" name="payment_method" id="payment_method">
                                        <option value="">-Select-</option>
                                        <option value="Cash">Cash</option>
                                        <option value="Personal Card">Personal Card</option>
                                        <option value="Personal Account">Personal Account</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="scope_of_work" class="form-label">Scope of Work*</label>
                                    <textarea class="form-control" name="scope_of_work" id="scope_of_work" rows="1" required></textarea>
                                </div>
                                <div class="col-md-2">
                                    <label for="deal_id" class="form-label">Deal ID</label>
                                    <input type="text" class="form-control" name="deal_id" id="deal_id" onchange="get_custName()">
                                </div>
                                <div class="col-md-2">
                                    <label for="site_name" class="form-label">Customer Name</label>
                                    <input type="text" class="form-control" name="site_name" id="site_name">
                                </div>
                                <div class="col-md-2">
                                    <label for="project_id" class="form-label">Project ID</label>
                                    <input type="text" class="form-control" name="project_id" id="project_id">
                                </div>
                                <div class="col-md-2">
                                    <label for="vendor_name" class="form-label">Vendor Name</label>
                                    <input type="text" class="form-control" name="vendor_name" id="vendor_name">
                                </div>
                                <div class="col-md-2">
                                    <label for="currency_id" class="form-label">Currency</label>
                                    <select class="form-control" name="currency_id" id="currency_id">
                                        <option value="">-Select-</option>
                                        @foreach($currencies as $currency)
                                            <option value="{{ $currency->id }}" {{ $default_currency == $currency->id ? 'selected' : '' }}>{{ $currency->name }} ({{ $currency->code }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="head_count_name" class="form-label">Head Count & Name</label>
                                    <input type="text" class="form-control" name="head_count_name" id="head_count_name">
                                </div>
                                <div class="col-md-2">
                                    <label for="attachment" class="form-label">Attachment</label>
                                    <input type="file" class="form-control" name="attachment" id="attachment">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-light add-btn ms-2">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Add
                    </button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>


    <div class="modal side-panel  fade" id="ModalServiceEdit" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" style="max-width: 90%;">
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-reimbursement-request-update', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-reimbursement-request-update']) }}

            <div class="modal-content" style="max-height: 80vh">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel">Edit <span id="edit_modal_reimbursement_no"></span></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body">
                            <div class="row gap-rows">
                                <div class="col-md-2">
                                    <label for="edit_date" class="form-label"> Expense Date*</label>
                                    <input type="text" class="form-control date-picker" name="date" id="edit_date" required>
                                </div>
                                <div class="col-md-2">
                                    <label for="edit_remarks" class="form-label">Expense Category*</label>
                                    <select class="form-control" name="remarks" id="edit_remarks" onchange="edit_remarks_change()">
                                        <option value="">-Select-</option>
                                        <option value="Travel Expenses">Travel Expenses</option>
                                        <option value="Fuel Expenses">Fuel Expenses</option>
                                        <option value="Taxi / Transportation">Taxi / Transportation</option>
                                        <option value="Air Ticket">Air Ticket</option>
                                        <option value="Hotel Accommodation">Hotel Accommodation</option>
                                        <option value="Meals & Entertainment">Meals & Entertainment</option>
                                        <option value="Client Meeting Expenses">Client Meeting Expenses</option>
                                        <option value="Site Visit Expenses">Site Visit Expenses</option>
                                        <option value="Courier & Logistics">Courier & Logistics</option>
                                        <option value="Telephone / Mobile">Telephone / Mobile</option>
                                        <option value="Internet Expenses">Internet Expenses</option>
                                        <option value="Training & Certification">Training & Certification</option>
                                        <option value="Medical Expenses">Medical Expenses</option>
                                        <option value="Visa Expenses">Visa Expenses</option>
                                        <option value="Office Supplies">Office Supplies</option>
                                        <option value="Parking & Toll Charges">Parking & Toll Charges</option>
                                        <option value="Vehicle Maintenance">Vehicle Maintenance</option>
                                        <option value="Customs Clearance Expenses">Customs Clearance Expenses</option>
                                        <option value="Marketing & Promotion">Marketing & Promotion</option>
                                        <option value="Miscellaneous Expenses">Miscellaneous Expenses</option>
                                        <option value="Other">Other</option>
                                    </select>
                                    <input type="text" class="form-control mt-1" name="remarks_other" id="edit_remarks_other" style="display: none;" placeholder="Please specify">
                                    <script>
                                        function edit_remarks_change() {
                                            if ($('#edit_remarks').val() == "Other") {
                                                $('#edit_remarks_other').css('display', 'block');
                                                $('#edit_remarks_other').prop("required", true);
                                            } else {
                                                $('#edit_remarks_other').css('display', 'none');
                                                $('#edit_remarks_other').prop("required", false);
                                            }
                                        }
                                    </script>
                                </div>
                                <div class="col-md-2">
                                    <label for="edit_invoice_no" class="form-label">Invoice No.</label>
                                    <input type="text" class="form-control" name="invoice_no" id="edit_invoice_no">
                                </div>
                                <div class="col-md-2">
                                    <label for="edit_invoice_date" class="form-label">Invoice Date</label>
                                    <input type="text" class="form-control date-picker" name="invoice_date" id="edit_invoice_date">
                                </div>
                                <div class="col-md-2">
                                    <label for="edit_amount" class="form-label">Amount*</label>
                                    <input type="number" class="form-control" name="amount" step="Any" id="edit_amount" required>
                                </div>
                                <div class="col-md-2">
                                    <label for="edit_reimbursable_amount" class="form-label">Reimbursable Amount</label>
                                    <input type="number" class="form-control" name="reimbursable_amount" step="Any" id="edit_reimbursable_amount">
                                </div>
                                <div class="col-md-2">
                                    <label for="edit_payment_method" class="form-label">Payment Method</label>
                                    <select class="form-control" name="payment_method" id="edit_payment_method">
                                        <option value="">-Select-</option>
                                        <option value="Cash">Cash</option>
                                        <option value="Personal Card">Personal Card</option>
                                        <option value="Personal Account">Personal Account</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="edit_scope_of_work" class="form-label">Scope of Work*</label>
                                    <textarea class="form-control" name="scope_of_work" id="edit_scope_of_work" rows="1" required></textarea>
                                </div>
                                <div class="col-md-2">
                                    <label for="edit_deal_id" class="form-label">Deal ID</label>
                                    <input type="text" class="form-control" name="deal_id" id="edit_deal_id" onchange="get_editcustName()">
                                </div>
                                <div class="col-md-2">
                                    <label for="edit_site_name" class="form-label">Customer Name</label>
                                    <input type="text" class="form-control" name="site_name" id="edit_site_name">
                                </div>
                                <div class="col-md-2">
                                    <label for="edit_project_id" class="form-label">Project ID</label>
                                    <input type="text" class="form-control" name="project_id" id="edit_project_id">
                                </div>
                                <div class="col-md-2">
                                    <label for="edit_vendor_name" class="form-label">Vendor Name</label>
                                    <input type="text" class="form-control" name="vendor_name" id="edit_vendor_name">
                                </div>
                                <div class="col-md-2">
                                    <label for="edit_currency_id" class="form-label">Currency</label>
                                    <select class="form-control" name="currency_id" id="edit_currency_id">
                                        <option value="">-Select-</option>
                                        @foreach($currencies as $currency)
                                            <option value="{{ $currency->id }}">{{ $currency->name }} ({{ $currency->code }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="edit_head_count_name" class="form-label">Head Count & Name</label>
                                    <input type="text" class="form-control" name="head_count_name" id="edit_head_count_name">
                                </div>
                                <div class="col-md-4">
                                    <label for="attachment" class="form-label">Attachment</label>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <input type="file" class="form-control" name="attachment" id="attachment" style="flex: 1;">
                                        <div id="edit_attachment_link" style="font-size: 13px; white-space: nowrap;"></div>
                                    </div>
                                </div>

                                <input type="hidden" id="edit_id" name="edit_id" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-light add-btn ms-2">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Update
                    </button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>

    <div class="modal side-panel  fade" id="AccountsModal" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg " style="left: 30%">
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-reimbursement-request-account-approve', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-reimbursement-request-update']) }}

            <div class="modal-content" style="max-height: 80vh">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel">Accounts Approval</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <label for="" class="form-label">Remarks</label>
                                    <input class="form-control" type="text" id="remarks" name="remarks">
                                    <input type="hidden" id="account_re_id" name="account_re_id" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" value="2" name="btn_status" class="btn btn-light add-btn ms-2">
                        <i class="ico icon-outline-clipboard-remove text-success"></i> DisApprove
                    </button>
                    <button type="submit" value="1" name="btn_status" class="btn btn-light add-btn ms-2">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Approve
                    </button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>

    <div class="modal side-panel  fade" id="AccountsHeadModal" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg " style="left: 30%">
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-reimbursement-request-accounts-head-approve', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-reimbursement-request-accounts-head-approve']) }}

            <div class="modal-content" style="max-height: 80vh">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel">Accounts Head Approval</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <label for="" class="form-label">Remarks</label>
                                    <input class="form-control" type="text" id="remarks" name="remarks">
                                    <input type="hidden" id="acco_head_re_id" name="acco_head_re_id" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" value="2" name="btn_status" class="btn btn-light add-btn ms-2">
                        <i class="ico icon-outline-clipboard-remove text-success"></i> DisApprove
                    </button>
                    <button type="submit" value="1" name="btn_status" class="btn btn-light add-btn ms-2">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Approve
                    </button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>

    <div class="modal side-panel  fade" id="DepartmentHeadModal" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg " style="left: 30%">
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-reimbursement-request-dept-head-approve', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-reimbursement-request-dept-head-approve']) }}

            <div class="modal-content" style="max-height: 80vh">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel">Department Head Approval</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <label for="" class="form-label">Remarks</label>
                                    <input class="form-control" type="text" id="remarks" name="remarks">
                                    <input type="hidden" id="dept_head_re_id" name="dept_head_re_id" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" value="2" name="btn_status" class="btn btn-light add-btn ms-2">
                        <i class="ico icon-outline-clipboard-remove text-success"></i> DisApprove
                    </button>
                    <button type="submit" value="1" name="btn_status" class="btn btn-light add-btn ms-2">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Approve
                    </button>
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
    </script>
    <script>
        function fun_edit(id) {
            $('#edit_id').val(id);
            $('#edit_date').val($('#edit_date_' + id).val());
            $('#edit_deal_id').val($('#edit_deal_id_' + id).val());
            $('#edit_site_name').val($('#edit_site_name_' + id).val());
            $('#edit_scope_of_work').val($('#edit_scope_of_work_' + id).val());
            $('#edit_invoice_no').val($('#edit_invoice_no_' + id).val());
            $('#edit_amount').val($('#edit_amount_' + id).val());
            
            var fixed_categories = [
                "Travel Expenses", "Fuel Expenses", "Taxi / Transportation", "Air Ticket", 
                "Hotel Accommodation", "Meals & Entertainment", "Client Meeting Expenses", 
                "Site Visit Expenses", "Courier & Logistics", "Telephone / Mobile", 
                "Internet Expenses", "Training & Certification", "Medical Expenses", 
                "Visa Expenses", "Office Supplies", "Parking & Toll Charges", 
                "Vehicle Maintenance", "Customs Clearance Expenses", "Marketing & Promotion", 
                "Miscellaneous Expenses", "Other"
            ];
            var remarks_val = $('#edit_remarks_' + id).val();
            if (fixed_categories.includes(remarks_val)) {
                $('#edit_remarks').val(remarks_val);
                if (remarks_val === "Other") {
                    $('#edit_remarks_other').css('display', 'block');
                    $('#edit_remarks_other').prop("required", true);
                } else {
                    $('#edit_remarks_other').css('display', 'none');
                    $('#edit_remarks_other').prop("required", false);
                }
            } else if (remarks_val) {
                $('#edit_remarks').val("Other");
                $('#edit_remarks_other').val(remarks_val);
                $('#edit_remarks_other').css('display', 'block');
                $('#edit_remarks_other').prop("required", true);
            }
            
            $('#edit_head_count_name').val($('#edit_head_count_name_' + id).val());
            $('#edit_invoice_date').val($('#edit_invoice_date_' + id).val());
            $('#edit_reimbursable_amount').val($('#edit_reimbursable_amount_' + id).val());
            $('#edit_payment_method').val($('#edit_payment_method_' + id).val());
            $('#edit_project_id').val($('#edit_project_id_' + id).val());
            $('#edit_vendor_name').val($('#edit_vendor_name_' + id).val());
            $('#edit_currency_id').val($('#edit_currency_id_' + id).val());
            
            $('#edit_modal_reimbursement_no').text($('#edit_reimbursement_no_' + id).val());
            
            var attachment = $('#edit_attachment_' + id).val();
            if(attachment) {
                var files = attachment.split('|');
                var links = '';
                for(var i = 0; i < files.length; i++) {
                    if (files[i]) {
                        links += '<a href="{{ asset('public/uploads/crm_amc_doc/') }}/'+files[i]+'" target="_blank" class="text-primary">'+files[i]+'</a><br/>';
                    }
                }
                $('#edit_attachment_link').html(links);
            } else {
                $('#edit_attachment_link').html('No file uploaded');
            }
            
            $('#ModalServiceEdit').modal('show');
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
                    var len = 0;
                    var len = 0;
                    if (dataResult['data'] != null) {
                        len = dataResult['data'].length;
                    }
                    if (len > 0) {
                        for (var i = 0; i < len; i++) {
                            $("#site_name").val(dataResult['data'][i].name);
                        }
                    } else {
                        $("#site_name").val();
                    }
                    $("#loading_bg").css("display", "none");
                }
            });
        }


        function get_editcustName() {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('crm-reimbursement-request-get-custname') }}";
            $.ajax({
                url: action,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}',
                    deal_id: $('#edit_deal_id').val(),
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
                            $("#edit_site_name").val(dataResult['data'][i].name);
                        }
                    } else {
                        $("#edit_site_name").val();
                    }
                    $("#loading_bg").css("display", "none");
                }
            });
        }

        $(document).ready(function() {
            // Default syncing for Add Modal
            $('#amount').on('input', function() {
                if (!$('#reimbursable_amount').data('manual_edit')) {
                    $('#reimbursable_amount').val($(this).val());
                }
            });
            $('#reimbursable_amount').on('input', function() {
                $(this).data('manual_edit', true);
                if ($(this).val() === '') {
                    $(this).data('manual_edit', false);
                }
            });

            $('#date').on('change', function() {
                if (!$('#invoice_date').data('manual_edit')) {
                    $('#invoice_date').val($(this).val());
                }
            });
            $('#invoice_date').on('change', function() {
                $(this).data('manual_edit', true);
                if ($(this).val() === '') {
                    $(this).data('manual_edit', false);
                }
            });

            // Default syncing for Edit Modal
            $('#edit_amount').on('input', function() {
                if (!$('#edit_reimbursable_amount').data('manual_edit')) {
                    $('#edit_reimbursable_amount').val($(this).val());
                }
            });
            $('#edit_reimbursable_amount').on('input', function() {
                $(this).data('manual_edit', true);
                if ($(this).val() === '') {
                    $(this).data('manual_edit', false);
                }
            });

            $('#edit_date').on('change', function() {
                if (!$('#edit_invoice_date').data('manual_edit')) {
                    $('#edit_invoice_date').val($(this).val());
                }
            });
            $('#edit_invoice_date').on('change', function() {
                $(this).data('manual_edit', true);
                if ($(this).val() === '') {
                    $(this).data('manual_edit', false);
                }
            });
            
            // Reset manual edit flags when opening modals
            $('#ModalService').on('show.bs.modal', function () {
                $('#reimbursable_amount').data('manual_edit', false);
                $('#invoice_date').data('manual_edit', false);
            });
            $('#ModalServiceEdit').on('show.bs.modal', function () {
                $('#edit_reimbursable_amount').data('manual_edit', false);
                $('#edit_invoice_date').data('manual_edit', false);
            });
        });
    </script>



    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@endsection

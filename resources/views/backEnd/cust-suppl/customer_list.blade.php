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

                sessionStorage.setItem('listViewCustomerList', 'long');
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

                sessionStorage.setItem('listViewCustomerList', 'short');

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
            const hasCustomerAction = urlParams.has('customer_action');

            // If in add/edit mode, force short view
            if (hasCustomerAction) {
                sessionStorage.setItem('listViewCustomerList', 'short');
                isFullList = true; // Set to true so toggle switches to short
                list_style_new(); // Switch to short view
            } else {
                // Normal behavior - use saved view from sessionStorage
                const savedView = sessionStorage.getItem('listViewCustomerList');
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
                    sessionStorage.setItem('listViewCustomerList', 'short');
                });
            });



        });
    </script>





    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>

    <?php try { ?>

    <aside class="left-nav col-3" id="leftSidebar">
        <div class="resizer" id="sidebarResizer"></div>
        <div class="short-list" id="filters-short">
            <h4 class="mb-2">Customers List
            </h4>

            {{-- {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'customers', 'method' => 'get', 'id' => 'crm-deals-search']) }} --}}


            <div class="search-filter-container mb-4">
                <div class="input-group flex-nowrap">
                    <input type="text" name="documents_number" id="search_customer_id" class="form-control"
                        placeholder="Document No" aria-label="Search" aria-describedby="addon-wrapping" value="">
                </div>


                {{-- {{ Form::close() }} --}}
                <button type="button" class="btn btn-light" id="list_style_button" onclick="list_style_new()">
                    <i class="ico icon-outline-list-down"></i>
                </button>
            </div>



        </div>

        <div class="long-list  d-none" id="filters-long">
            <div class="d-flex align- align-items-center justify-content-between">
                <h4 class="mb-2">Customers List
                </h4>

                <div class="search-filter-container mb-0">





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
                            @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 27)
                                <li>
                                    <button data-bs-toggle="modal" data-bs-target="#ModalMergeDuplicateCustomer"
                                        class="dropdown-item d-flex align-items-center  text-danger "><i
                                            class="ico ico icon-outline-link-square  text-danger title-15 me-2"></i>Merge
                                        Duplicate</button>
                                </li>

                                <li>
                                    <button data-bs-toggle="modal" data-bs-target="#ModalMergeCustomer"
                                        class="dropdown-item d-flex align-items-center  text-danger "><i
                                            class="ico ico icon-outline-link-square  text-danger  title-15 me-2"></i>Merge
                                    </button>
                                </li>
                            @endif


                            <li>
                                <button id="copy-button" class="dropdown-item d-flex align-items-center text-success"><i
                                        class="ico icon-outline-copy  text-success title-15 me-2"></i>Copy URL
                                </button>
                            </li>

                            <li>
                                <a href="{{ url('customer-from-list') }}"
                                    class="dropdown-item d-flex align-items-center text-success"><i
                                        class="ico icon-outline-document-text text-success title-15 me-2"></i>Form
                                    Submited
                                </a>
                            </li>

                            <li>
                                <a href="{{ url('customers-pending') }}"
                                    class="dropdown-item d-flex align-items-center text-success"><i
                                        class="ico icon-outline-document-text text-success title-15 me-2"></i>Sales
                                    Pending
                                </a>
                            </li>


                            <li>
                                <a href="{{ url('customer-import') }}"
                                    class="dropdown-item d-flex align-items-center text-success"><i
                                        class="ico icon-outline-import text-success title-15 me-2"></i>Import
                                </a>
                            </li>




                        </ul>
                    </div>


                    <input type="hidden" name="copy_url" id="copy_url"
                        value="{{ url('customer-from/' . session('logged_session_data.company_id')) }}" />


                    <script>
                        $('#copy-button').click(function() {
                            var textToCopy = $('#copy_url').val();
                            var tempTextarea = $('<textarea>');
                            $('body').append(tempTextarea);
                            tempTextarea.val(textToCopy).select();
                            document.execCommand('copy');
                            tempTextarea.remove();
                            alert("Copied!");
                        });
                    </script>





                    <button class="btn btn-light" onclick="toggleLongFilters()">
                        <i class="ico icon-outline-magnifer"></i>
                    </button>
                    <button class="btn btn-light" id="list_style_button" onclick="list_style_new()">
                        <i class="ico icon-outline-list-down"></i>
                    </button>


                </div>
            </div>

            <div class="search-filter-container mt-1 mb-4 filter-field d-none border">

                <div class="card" style="width: 100%">
                    <div class="card-body">
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'customers', 'method' => 'get', 'id' => 'crm-deals-search']) }}
                        <div class="row">
                            <div class="col-3 mb-2">
                                <label for="" class="form-label">Company Name</label>
                                <input class="form-control" type="text" autocomplete="off" name="company_name"
                                    value="{{ @$ctrl_company_name }}">
                            </div>
                            <div class="col-1-5">
                                <label for="" class="form-label">Contact Name</label>
                                <input class="form-control" type="text" autocomplete="off" name="contact_name"
                                    value="{{ @$ctrl_contact_name }}">
                            </div>

                            <div class="col-1-5">
                                <label for="" class="form-label">Email</label>
                                <input class="form-control" type="text" autocomplete="off" name="email"
                                    value="{{ @$ctrl_email }}">
                            </div>
                            <div class="col-1-5">
                                <label for="" class="form-label">Sales Person</label>
                                <select class="form-control js-example-basic-single" name="sales_person">
                                    <option value="">-Select-</option>
                                    @foreach ($staff as $value)
                                        <option value="{{ @$value->user_id }}"
                                            @if ($ctrl_sales_person == $value->user_id) selected @endif>{{ @$value->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-1-5">
                                <label for="" class="form-label">Country</label>
                                <select class="form-control js-example-basic-single" name="vat_country">
                                    <option value="">-Select-</option>
                                    @foreach ($countries as $value)
                                        <option value="{{ @$value->id }}"
                                            @if ($ctrl_vat_country == $value->id) selected @endif>
                                            {{ @$value->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-1-5">
                                <label for="" class="form-label">State</label>
                                <select class="form-control js-example-basic-single" name="vat_state">
                                    <option value="">-Select-</option>
                                    @foreach ($states as $value)
                                        <option value="{{ @$value->id }}"
                                            @if ($ctrl_vat_state == $value->id) selected @endif>
                                            {{ @$value->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-1-5">
                                <label for="" class="form-label">Status</label>
                                <select class="form-control" id="statusFilter" name="status_filter">
                                    <option value="">All Status</option>
                                    <option value="1" @if ($status_filter == 1) selected @endif>Active
                                    </option>
                                    <option value="3" @if ($status_filter == 3) selected @endif>Inactive
                                    </option>
                                    <option value="2" @if ($status_filter == 2) selected @endif>Deleted
                                    </option>
                                </select>
                            </div>
                            <div class="col-1-5">
                                <label for="" class="form-label">Information</label>
                                <select class="form-control" id="informationFilter" name="information_filter">
                                    <option value="">All Information</option>
                                    <option value="complete" @if ($information_filter == 'complete') selected @endif>Complete
                                    </option>
                                    <option value="incomplete" @if ($information_filter == 'incomplete') selected @endif>Incomplete
                                    </option>
                                </select>
                            </div>
                            <div class="col-3">
                                <label for="" class="form-label">Added By</label>
                                <select class="form-control js-example-basic-single" id="assignedFilter"
                                    name="assigned_filter[]" multiple>
                                    @foreach ($staff as $value)
                                        <option value="{{ @$value->user_id }}"
                                            @if (is_array($assigned_filter) && in_array($value->user_id, $assigned_filter)) selected @endif>
                                            {{ @$value->full_name }}
                                        </option>
                                    @endforeach
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


                            <div class="col-1-5 filter-field d-none">
                                <button class="btn btn-light mt-4" type="submit">
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

            <ul id="short-list" class="nav flex-column nav-pills" id="companyTabNavs" role="tablist">
                @if (count($customer) > 0)
                    @foreach ($customer as $value)
                        <li class="nav-item w-100" role="presentation">
                            <button class="nav-link cust-item {{ $active_id == $value->id ? 'active' : '' }}"
                                data-id="{{ $value->id }}" id="purchase-order-1-tab" data-bs-toggle="tab"
                                data-bs-target="#purchase-order-1" type="button" role="tab"
                                aria-controls="purchase-order-1" aria-selected="true">
                                <div class="row w-100">
                                    <div class="col-12">
                                        <label class="form-control-plaintext truncate-text">
                                            {{ strtoupper(@$value->name) }}@if (@$value->internal == 1)
                                                <i class="ico icon-bold-info-circle text-primary" aria-hidden="true"
                                                    title="Internal Customer"></i>
                                            @endif
                                            <label>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-control-plaintext" style="font-size:11px">
                                            {{ @$value->code }}
                                        </div>
                                    </div>
                                    <div class="col-4 text-center">
                                        <div class="form-control-plaintext" style="font-size:11px">
                                            {{ date('d/m/Y', strtotime($value->created_at)) }}</div>
                                    </div>
                                    <div class="col-4 text-end ">
                                        <div class="form-control-plaintext truncate-text" style="font-size:11px">
                                            {{ str_replace(' ', '', $value->mobile) }}

                                        </div>
                                    </div>

                                </div>
                            </button>
                        </li>
                    @endforeach
                @else
                    No Records
                @endif
            </ul>

            <div class="table-responsive mb-4 mt-4">
                <table id="long-list" class="table table-hover data-table d-none" style="table-layout: fixed;width:100%">

                    <thead class="text-start">
                        <tr>
                            <th style="width: 300px;">@lang('Customer Name')</th>
                            <th style="width: 150px">@lang('Contact Person')</th>
                            <th style="width: 170px">@lang('Mobile')</th>
                            <th style="width: 220px">@lang('Email')</th>
                            <th style="width: 100px">@lang('Added By')</th>
                            <th style="width: 100px">@lang('Updated By')</th>
                            <th style="width: 90px">@lang('Status')</th>
                            <th style="width: 90px">@lang('Information')</th>
                            <th class="text-center" style="width: 90px">@lang('lang.action')</th>
                        </tr>
                    </thead>

                    <tbody>

                        @php $serialcount = 1; @endphp
                        @foreach ($customer as $value)
                            <tr @if ($value->status == 2) style="background-color: rgba(0, 0, 0, 0.19);" @endif>

                                <td class="" onclick="handleCustomerClick(event, {{ @$value->id }})"><a
                                        class="">
                                        {{ @$value->code }} - {{ @$value->name }}</a>
                                    @if (@$value->internal == 1)
                                        <i class="ico icon-bold-info-circle text-primary" aria-hidden="true"
                                            title="Internal Customer"></i>
                                    @endif
                                </td>
                                <td>
                                    {{ @$value->contcat_person }}
                                </td>
                                <td>
                                    {{ @$value->mobile }}
                                </td>
                                <td>
                                    {{ @$value->email }}
                                </td>





                                <td>{{ @$value->createdBy->full_name }}

                                    <small
                                        class="text-muted">{{ @$value->created_at ? $value->created_at->format('d/m/Y h:i A') : '—' }}</small>



                                </td>

                                <td>
                                    @if (@$value->updatedBy)
                                        {{ @$value->updatedBy->full_name }}

                                        <small
                                            class="text-muted">{{ @$value->updated_at ? $value->updated_at->format('d/m/Y  h:i A') : '—' }}</small>
                                    @endif


                                </td>

                                <td>
                                    @if (@$value->status == 2)
                                        <span class="text-danger">Deleted</span>
                                    @elseif (@$value->status == 3)
                                        <span class="text-dark">Inactive</span>
                                    @else
                                        <span class="text-success">Active</span>
                                    @endif
                                </td>
                                <td>
                                    @if (@$value->status == 2)
                                        <span class="text-dark">Deleted</span>
                                    @elseif(App\SysHelper::get_company_status($value) == 0)
                                        <span class="text-danger">Incomplete</span>
                                    @else
                                        <span class="text-success">Complete</span>
                                    @endif
                                </td>
                                <td>





                                    <div class="d-flex justify-content-center align-items-center gap-1">
                                        <a href="{{ url('customers/' . $value->id . '?customer_action=edit') }}"
                                            onclick="list_style_new()" class="btn btn-sm btn-light" title="Comments">
                                            <i class="ico icon-outline-pen-2" style="font-size: 16px;"></i>
                                        </a>


                                        @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
                                            @if ($value->status == 2)
                                                <a class="btn btn-light btn-sm"
                                                    href="{{ url('customer-restore/' . $value->id) }}"
                                                    onclick="return confirm('Are you sure you want to restore this item?');">
                                                    <i class="ico icon-bold-restart" style="font-size: 16px;"></i>
                                                </a>
                                            @else
                                                <a class="btn btn-light btn-sm"
                                                    href="{{ url('customer-inactive/' . $value->id) }}"
                                                    onclick="return confirm('Are you sure you want to delete this item?');">
                                                    <i class="ico  icon-outline-trash-bin-minimalistic text-dark"
                                                        style="font-size: 16px;"></i>
                                                </a>
                                            @endif
                                        @endif

                                    </div>

                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-3 mb-4">
                <div class="pagination-wrapper">
                    {{ $customer->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </aside>



    <div class="modal fade" id="ModalMergeDuplicateCustomer" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="height: 464px !important;">

            {!! Form::open([
                'class' => 'form-horizontal',
                'files' => true,
                'url' => 'customer-merge-duplicate',
                'method' => 'post',
                'id' => 'customer-merge-duplicate',
            ]) !!}


            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="poexcelimport">Merge Duplicate Customer</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0 border-0 shadow-none">
                        <div class="card-body p-3" style="max-height: 500px; overflow-y: auto;">
                            @php
                                $duplicate_customer = collect($duplicate_customer);
                            @endphp

                            @foreach ($duplicate_customer->groupBy('duplicate_name') as $duplicateName => $groupedCustomers)
                                <div class="border rounded p-2 mb-2">
                                    <div class="d-flex align-items-start">
                                        <input type="checkbox" class="form-check-input mt-1 me-2"
                                            id="duplicate_name_{{ $duplicateName }}" name="duplicate_name[]"
                                            value="{{ $duplicateName }}" checked>
                                        <div>
                                            @foreach ($groupedCustomers as $customer_group)
                                                <label for="duplicate_name_{{ $duplicateName }}" class="d-block mb-1">
                                                    {{ $customer_group->account_code }} -
                                                    {{ $customer_group->account_name }}
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" onclick="return confirm('Are you sure you want to Merge this?');"
                        class="btn btn-light add-btn ms-2">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Merge
                    </button>
                </div>
            </div>
            {!! Form::close() !!}



        </div>
    </div>



    <div class="modal fade" id="ModalMergeCustomer" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="height: 464px !important;">

            {!! Form::open([
                'class' => 'form-horizontal',
                'files' => true,
                'url' => 'customer-merge',
                'method' => 'post',
                'id' => 'customer-merge',
            ]) !!}


            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="poexcelimport">Merge Customer</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0 border-0 shadow-none">
                        <div class="card-body p-3" style="max-height: 500px; overflow-y: auto;">
                            <div class="row">
                                <div class="col-md-6">From Customer
                                    <select id="from_account" name="from_account[]"
                                        class="form-control js-example-basic-single" multiple required>
                                        @foreach ($customer_list as $data)
                                            <option value="{{ $data->id }}">{{ $data->account_code }} -
                                                {{ $data->account_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">To Customer
                                    <select id="to_account" name="to_account"
                                        class="form-control js-example-basic-single" required>
                                        <option value="">Select</option>
                                        @foreach ($customer_list as $data)
                                            <option value="{{ $data->id }}">{{ $data->account_code }} -
                                                {{ $data->account_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" onclick="return confirm('Are you sure you want to Merge this?');"
                        class="btn btn-light add-btn ms-2">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Merge
                    </button>
                </div>
            </div>

            {!! Form::close() !!}



        </div>
    </div>

    <div class="content-container col-9">
        <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">


            <script>
                function handleCustomerClick(event, customerId) {
                    // Check if Ctrl key (or Cmd on Mac) is pressed
                    if (event.ctrlKey || event.metaKey) {
                        // Open in new tab using a temporary link with target="_blank"
                        const link = document.createElement('a');
                        link.href = "{{ url('customers') }}/" + customerId;
                        link.target = '_blank';
                        link.rel = 'noopener noreferrer'; // Security best practice
                        link.click();
                    } else {
                        list_style_new();
                        // Normal click - load in same tab
                        var id = customerId;



                        // Update the browser URL to include selected ID (without reloading)
                        var newUrl = "{{ url('customers') }}/" + id;
                        window.history.pushState({
                            path: newUrl
                        }, '', newUrl);

                        var action = "{{ URL::to('customer-details') }}/" + id;
                        $('#loading_bg').show();

                        $.ajax({
                            url: action,
                            method: 'GET',
                            success: function(response) {
                                $('#cust-details').html(response);
                            },
                            error: function() {
                                $('#cust-details').html(
                                    '<p class="text-danger">No Details Available.</p>');
                            },
                            complete: function() {
                                $('#loading_bg').hide(); // Always hide loader after request completes
                            }
                        });
                    }
                }

                $(document).ready(function() {
                    $(document).on('click', '.cust-item', function() {


                        var id = $(this).data('id');

                        $('.cust-item').removeClass('active');
                        $('.cust-item[data-id="' + id + '"]').addClass('active');

                        // Update the browser URL to include selected ID (without reloading)
                        var newUrl = "{{ url('customers') }}/" + id;
                        window.history.pushState({
                            path: newUrl
                        }, '', newUrl);

                        var action = "{{ URL::to('customer-details') }}/" + id;
                        $('#loading_bg').show();

                        $.ajax({
                            url: action,
                            method: 'GET',
                            success: function(response) {
                                $('#cust-details').html(response);
                            },
                            error: function() {
                                $('#cust-details').html(
                                    '<p class="text-danger">No Details Available.</p>');
                            },
                            complete: function() {
                                $('#loading_bg').hide(); // Always hide loader after request completes
                            }
                        });
                    });
                });
            </script>






            <div class="" role="tabpanel" aria-labelledby="po-tab" id="cust-details">
                @if ($action === 'add')
                    @include('backEnd.cust-suppl.addCustomer', $addCustomer)
                @elseif($action === 'edit')
                    @include('backEnd.cust-suppl.editCustomer', $editData)
                @elseif($action === 'createcustomer')
                    @include('backEnd.cust-suppl.AddSupplierToCustomer', $editData)
                @elseif (!empty($selectedCus) && is_array($selectedCus))
                    @include('backEnd.cust-suppl.viewCustomer', $selectedCus)
                @else
                    <form id="supplierForm" method="GET" action="{{ url('customers') }}">
                        <input type="hidden" name="customer_action" value="add">
                        <div class="container-fluid d-flex flex-column justify-content-center align-items-center"
                            style="min-height: 90vh;">

                            <!-- Icon + Heading -->
                            <div onclick="document.getElementById('supplierForm').submit();" class="text-center mb-4">
                                <div class="rounded-circle bg-success text-white d-flex justify-content-center align-items-center mx-auto"
                                    style="width: 80px; height: 80px; font-size: 36px;cursor:pointer">
                                    <i class="ico icon-outline-add-square"></i>
                                </div>
                                <h1 class="fw-bold mt-3" style="cursor:pointer">Customer</h1>
                                {{-- <p class="text-muted">Create and track your leads with ease</p> --}}
                            </div>

                        </div>
                    </form>
                @endif
            </div>


        </div>
    </div>




    <script>
        $(document).ready(function() {
            $('#search_customer_id').on('input', function() {
                let query = $(this).val().trim();



                $.ajax({
                    url: "{{ url('customer-search-record') }}",
                    method: "GET",
                    data: {
                        query
                    },
                    success: function(data) {
                        $('#short-list').empty();



                        if (data.data && data.data.length > 0) {
                            $.each(data.data, function(_, amc_list) {

                                let ims = ` <li class="nav-item w-100" role="presentation">
                            <button class="nav-link cust-item"
                                data-id="${amc_list.id}" id="purchase-order-1-tab" data-bs-toggle="tab"
                                data-bs-target="#purchase-order-1" type="button" role="tab"
                                aria-controls="purchase-order-1" aria-selected="true">
                                <div class="row w-100">
                                     <div class="col-12">
                                        <label class="form-control-plaintext truncate-text">
                                            ${amc_list.name} ${amc_list.internal === 1 ? '<i class="ico icon-bold-info-circle text-primary" title="Internal Customer"></i>' : ''}
                                            <label>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-control-plaintext" style="font-size: 11px">
                                            ${amc_list.code}
                                        </div>
                                    </div>
                                    <div class="col-4 text-center">
                                        <div class="form-control-plaintext" style="font-size: 11px">
                                            ${get_format_date(amc_list.created_at)}</div>
                                    </div>
                                    <div class="col-4 text-end ">
                                        <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                                            ${amc_list.mobile}

                                        </div>
                                    </div>
                                   
                                </div>
                            </button>
                        </li>`;

                                // let ims = `
                            //     <li class="nav-item w-100">
                            //         <button class="nav-link cust-item" data-id="${amc_list.id}">
                            //             <div class="row w-100">
                            //                 <div class="col-4">${amc_list.code}</div>
                            //                 <div class="col-4 text-center">${get_format_date(amc_list.created_at)}</div>
                            //                 <div class="col-4 text-end">${amc_list.mobile}</div>
                            //                 <div class="col-12">
                            //                     <label>${amc_list.name}
                            //                         ${amc_list.internal === 1 ? '<i class="ico icon-bold-info-circle text-primary" title="Internal Customer"></i>' : ''}
                            //                     </label>
                            //                 </div>
                            //             </div>
                            //         </button>
                            //     </li>
                            // `;
                                $('#short-list').append(ims);
                            });
                        } else {
                            $('#short-list').html('<div class="p-2">No results found</div>');
                        }
                    },
                    error: function(xhr) {
                        console.error("AJAX error:", xhr.responseText);
                    }
                });
            });
        });
    </script>


    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>



    <script>
        $(document).on('click', '#savePaymentTerm', function() {

            let title = $('#payment_term_title').val().trim();
            let input = $('#payment_term_title');

            input.removeClass('is-invalid');
            input.next('.invalid-feedback').text('');

            if (!title) {
                input.addClass('is-invalid');
                input.next('.invalid-feedback').text('Payment term is required');
                return;
            }

            $.ajax({
                url: "{{ url('payment-terms-store-ajax') }}", // adjust route
                type: "POST",
                data: {
                    title: title,
                    _token: "{{ csrf_token() }}"
                },
                beforeSend: function() {
                    $('#savePaymentTerm').prop('disabled', true);
                },
                success: function(res) {

                    if (res.status) {

                        // ✅ NEW ID AVAILABLE HERE
                        console.log('New ID:', res.data.id);

                        // Example: append to dropdown
                        $('#payment_terms').append(
                            `<option value="${res.data.id}" selected>${res.data.title}</option>`
                        );

                        $('#paymenttermsModal').modal('hide');
                        $('#payment_term_title').val('');

                        toastr.success(res.message, 'Success');
                    }
                },
                error: function(xhr) {

                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        if (errors.title) {
                            input.addClass('is-invalid');
                            input.next('.invalid-feedback').text(errors.title[0]);
                        }
                    } else {
                        toastr.error('Something went wrong', 'Error');
                    }
                },
                complete: function() {
                    $('#savePaymentTerm').prop('disabled', false);
                }
            });
        });
    </script>

    <div class="modal side-panel  fade" id="paymenttermsModal" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm draggable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-3">



                    <label class="form-label">Payment Terms <span class="text-danger">*</span></label>
                    <input type="text" id="payment_term_title" name="name" class="form-control" required=""
                        autocomplete="off">

                    <div class="modal-footer d-flex justify-content-center p-0">
                        <button type="button" id="savePaymentTerm" class="btn btn-light add-btn ms-2">
                            <i class="ico icon-outline-bookmark-opened text-success"></i> Submit
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Modal PO --}}

    @php
        $department_modal = @App\SmHumanDepartment::select('id', 'name')
            ->where('active_status', 1)
            ->orderby('name', 'asc')
            ->get();

    @endphp


    <script>
        $(document).on('click', '#saveDesignation', function() {

            let title = $('#designation_title').val().trim();
            let input = $('#designation_title');
            let department_id = $('#department_modal').val();
            let department_text = $('#department_modal option:selected').text();

            console.log('Department ID:', department_id);
            console.log('Designation Title:', title);
            console.log('Department Text:', department_text);

            input.removeClass('is-invalid');
            input.next('.invalid-feedback').text('');

            if (!title) {
                input.addClass('is-invalid');
                input.next('.invalid-feedback').text('Designation term is required');
                return;
            }

            $.ajax({
                url: "{{ url('designation-store-ajax') }}", // adjust route
                type: "POST",
                data: {
                    title: title,
                    department_id: department_id,
                    _token: "{{ csrf_token() }}"
                },
                beforeSend: function() {
                    $('#loading_bg').show();

                },
                success: function(res) {

                    if (res.status) {


                        // ✅ NEW ID AVAILABLE HERE
                        console.log('New ID:', res.data.id);

                        // Example: append to dropdown
                        $('#e_designation_1').append(
                            `<option value="${res.data.title}" selected>${res.data.title}</option>`
                        );

                        let departmentText = department_text.trim().toLowerCase();

                        $('#e_department_1 option').each(function() {
                            if ($(this).val().trim().toLowerCase() === departmentText) {
                                $(this).prop('selected', true);
                                return false;
                            }
                        });

                        $('#e_department_1').trigger('change');

                        $('[id^="e_designation_"]').not('#e_designation_1').each(function() {
                            $(this).append(
                                `<option value="${res.data.title}">${res.data.title}</option>`
                            );
                        });


                            // Example: append to dropdown
                        $('#company_designation').append(
                            `<option value="${res.data.title}">${res.data.title}</option>`
                        );

                        $('#adddesignationModal').modal('hide');
                        $('#designation_title').val('');

                        toastr.success(res.message, 'Success');
                    }
                },
                error: function(xhr) {

                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        if (errors.title) {
                            input.addClass('is-invalid');
                            input.next('.invalid-feedback').text(errors.title[0]);
                        }
                    } else {
                        toastr.error('Something went wrong', 'Error');
                    }
                },
                complete: function() {
                    $('#loading_bg').hide();
                }
            });
        });
    </script>


    <div class="modal side-panel  fade" id="adddesignationModal" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm draggable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-3">

                    <label class="form-label">Department <span class="text-danger">*</span></label>

                    <select class="form-control js-example-basic-single" name="department_modal" id="department_modal">

                        @if (count($department_modal) > 0)
                            @foreach ($department_modal as $val)
                                <option value="{{ $val->id }}">{{ $val->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>

                    <style>
                        #saveDesignation {
                            color: var(--color-btn-light);
                            border: 1px solid var(--color-btn-light-border);
                            background-color: var(--color-btn-light-bg);
                        }

                        #saveDesignation {
                            display: flex;
                            align-items: center;
                            font-size: 12px;
                            padding: 3px 10px;
                            gap: 5px;
                            border-radius: 8px;
                            min-height: 25px;
                        }
                    </style>

                    <label class="form-label mt-3">Designation <span class="text-danger">*</span></label>
                    <input type="text" id="designation_title" name="name" class="form-control" required=""
                        autocomplete="off" style="    padding: 2px 5px;">

                    <div class="modal-footer d-flex justify-content-center p-0 pt-3">
                        <button type="button" id="saveDesignation"
                            style="color: var(--color-btn-light);
    border: 1px solid var(--color-btn-light-border);
    background-color: var(--color-btn-light-bg);"
                            class="btn btn-light add-btn ms-2">
                            <i class="ico icon-outline-bookmark-opened text-success" style="font-size:20px"></i> Submit
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        $(document).on('click', '#saveDesignation2', function() {

            let title = $('#designation_title2').val().trim();
            let input = $('#designation_title2');
            let department_id = $('#department_modal2').val();
            let department_text = $('#department_modal2 option:selected').text();



            input.removeClass('is-invalid');
            input.next('.invalid-feedback').text('');

            if (!title) {
                input.addClass('is-invalid');
                input.next('.invalid-feedback').text('Designation term is required');
                return;
            }

            $.ajax({
                url: "{{ url('designation-store-ajax') }}", // adjust route
                type: "POST",
                data: {
                    title: title,
                    department_id: department_id,
                    _token: "{{ csrf_token() }}"
                },
                beforeSend: function() {
                    $('#loading_bg').show();

                },
                success: function(res) {

                    if (res.status) {


                        // ✅ NEW ID AVAILABLE HERE
                        console.log('New ID:', res.data.id);

                        // Example: append to dropdown
                        $('#company_designation').append(
                            `<option value="${res.data.title}" selected>${res.data.title}</option>`
                        );


                        $('#adddesignationModal2').modal('hide');
                        $('#designation_title2').val('');

                        toastr.success(res.message, 'Success');
                    }
                },
                error: function(xhr) {

                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        if (errors.title) {
                            input.addClass('is-invalid');
                            input.next('.invalid-feedback').text(errors.title[0]);
                        }
                    } else {
                        toastr.error('Something went wrong', 'Error');
                    }
                },
                complete: function() {
                    $('#loading_bg').hide();
                }
            });
        });
    </script>

    <div class="modal side-panel  fade" id="adddesignationModal2" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm draggable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-3">

                    <label class="form-label">Department <span class="text-danger">*</span></label>

                    <select class="form-control js-example-basic-single" name="department_modal2" id="department_modal2">

                        @if (count($department_modal) > 0)
                            @foreach ($department_modal as $val)
                                <option value="{{ $val->id }}">{{ $val->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>

                    <style>
                        #saveDesignation2 {
                            color: var(--color-btn-light);
                            border: 1px solid var(--color-btn-light-border);
                            background-color: var(--color-btn-light-bg);
                        }

                        #saveDesignation2 {
                            display: flex;
                            align-items: center;
                            font-size: 12px;
                            padding: 3px 10px;
                            gap: 5px;
                            border-radius: 8px;
                            min-height: 25px;
                        }
                    </style>

                    <label class="form-label mt-3">Designation <span class="text-danger">*</span></label>
                    <input type="text" id="designation_title2" name="name" class="form-control" required=""
                        autocomplete="off" style="    padding: 2px 5px;">

                    <div class="modal-footer d-flex justify-content-center p-0 pt-3">
                        <button type="button" id="saveDesignation2"
                            style="color: var(--color-btn-light);
    border: 1px solid var(--color-btn-light-border);
    background-color: var(--color-btn-light-bg);"
                            class="btn btn-light add-btn ms-2">
                            <i class="ico icon-outline-bookmark-opened text-success" style="font-size:20px"></i> Submit
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Modal PO --}}




    <script>
        $(document).on('click', '#saveDepartment', function() {

            let title = $('#department_title').val().trim();
            let input = $('#department_title');


            console.log('Department Title:', title);

            input.removeClass('is-invalid');
            input.next('.invalid-feedback').text('');

            if (!title) {
                input.addClass('is-invalid');
                input.next('.invalid-feedback').text('Department is required');
                return;
            }

            $.ajax({
                url: "{{ url('department-store-ajax') }}", // adjust route
                type: "POST",
                data: {
                    title: title,
                    _token: "{{ csrf_token() }}"
                },
                beforeSend: function() {
                    $('#loading_bg').show();

                },
                success: function(res) {

                    if (res.status) {


                        // ✅ NEW ID AVAILABLE HERE
                        console.log('New ID:', res.data.id);

                        // Example: append to dropdown


                        $('#e_department_1').append(
                            `<option value="${res.data.name}" selected>${res.data.name}</option>`
                        );



                        $('#department_modal').append(
                            `<option value="${res.data.id}">${res.data.name}</option>`
                        );





                        $('#e_department_1').trigger('change');

                        $('[id^="e_department_"]').not('#e_department_1').each(function() {
                            $(this).append(
                                `<option value="${res.data.name}">${res.data.name}</option>`
                            );
                        });


                        $('#adddepartmentModal').modal('hide');
                        $('#department_title').val('');

                        toastr.success(res.message, 'Success');
                    }
                },
                error: function(xhr) {

                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        if (errors.title) {
                            input.addClass('is-invalid');
                            input.next('.invalid-feedback').text(errors.title[0]);
                        }
                    } else {
                        toastr.error('Something went wrong', 'Error');
                    }
                },
                complete: function() {
                    $('#loading_bg').hide();
                }
            });
        });
    </script>

    <div class="modal side-panel  fade" id="adddepartmentModal" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm draggable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-3">



                    <style>
                        #saveDepartment {
                            color: var(--color-btn-light);
                            border: 1px solid var(--color-btn-light-border);
                            background-color: var(--color-btn-light-bg);
                        }

                        #saveDepartment {
                            display: flex;
                            align-items: center;
                            font-size: 12px;
                            padding: 3px 10px;
                            gap: 5px;
                            border-radius: 8px;
                            min-height: 25px;
                        }
                    </style>

                    <label class="form-label">Department <span class="text-danger">*</span></label>
                    <input type="text" id="department_title" name="name" class="form-control" required=""
                        autocomplete="off" style="    padding: 2px 5px;">

                    <div class="modal-footer d-flex justify-content-center p-0 pt-3">
                        <button type="button" id="saveDepartment"
                            style="color: var(--color-btn-light);
    border: 1px solid var(--color-btn-light-border);
    background-color: var(--color-btn-light-bg);"
                            class="btn btn-light add-btn ms-2">
                            <i class="ico icon-outline-bookmark-opened text-success" style="font-size:20px"></i> Submit
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>


      

@endsection

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

                localStorage.setItem('listViewCustomerFormList', 'long');
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

                localStorage.setItem('listViewCustomerFormList', 'short');

            }


        }


        //added ny kp
        function toggleLongFilters() {

            document.querySelectorAll('#filters-long .filter-field').forEach(el => {
                el.classList.toggle('d-none');
            });
        }




        // Initialize view from localStorage
        document.addEventListener('DOMContentLoaded', () => {
            const savedView = localStorage.getItem('listViewCustomerFormList');
            if (savedView === 'long') {
                isFullList = false; // so that toggling once activates full view
                list_style_new();
            } else {
                // Default to short view
                isFullList = true; // so that toggling once activates short view
                list_style_new();
            }

            // Attach event to sidebar links to force short view on navigation
            document.querySelectorAll('.sub-nav-item').forEach(link => {
                link.addEventListener('click', () => {
                    localStorage.setItem('listViewCustomerFormList', 'short');
                });
            });



        });
    </script>





    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>



    <aside class="left-nav col-3" id="leftSidebar">
        <div class="resizer" id="sidebarResizer"></div>
        <div class="short-list" id="filters-short">
            <h4 class="mb-2">Customers Pending List
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
                <h4 class="mb-2">Customers Pending List
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



                            <li>
                                <button id="copy-button" class="dropdown-item d-flex align-items-center text-success"><i
                                        class="ico icon-outline-copy  text-success title-15 me-2"></i>Copy URL
                                </button>
                            </li>

                            <li>
                                <a href="{{ url('customers') }}"
                                    class="dropdown-item d-flex align-items-center text-success"><i
                                        class="ico icon-outline-document-text text-success title-15 me-2"></i>Customer List
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
                                    value="">
                            </div>
                            <div class="col-1-5">
                                <label for="" class="form-label">Contact Name</label>
                                <input class="form-control" type="text" autocomplete="off" name="contact_name"
                                    value="">
                            </div>

                            <div class="col-1-5">
                                <label for="" class="form-label">Email</label>
                                <input class="form-control" type="text" autocomplete="off" name="email"
                                    value="">
                            </div>
                            <div class="col-1-5">
                                <label for="" class="form-label">Sales Person</label>
                                <select class="form-control js-example-basic-single" name="sales_person">
                                    <option value="">-Select-</option>
                                    @foreach ($staff as $value)
                                        <option value="{{ @$value->user_id }}"
                                            >{{ @$value->full_name }}
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
                                            >
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
                                            >
                                            {{ @$value->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-1-5">
                                <label for="" class="form-label">Status</label>
                                <select class="form-control" id="statusFilter" name="status_filter">
                                    <option value="">All Status</option>
                                    <option value="1">Active
                                    </option>
                                    <option value="3">Inactive
                                    </option>
                                    <option value="2">Deleted
                                    </option>
                                </select>
                            </div>
                            <div class="col-1-5">
                                <label for="" class="form-label">Information</label>
                                <select class="form-control" id="informationFilter" name="information_filter">
                                    <option value="">All Information</option>
                                    <option value="complete" >Complete
                                    </option>
                                    <option value="incomplete">Incomplete
                                    </option>
                                </select>
                            </div>
                            <div class="col-3">
                                <label for="" class="form-label">Added By</label>
                                <select class="form-control js-example-basic-single" id="assignedFilter"
                                    name="assigned_filter[]" multiple>
                                    @foreach ($staff as $value)
                                        <option value="{{ @$value->user_id }}"
                                            >
                                            {{ @$value->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>


                              <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">Form Date</label>
                                <input class="form-control date-picker" id="date" type="text" autocomplete="off"
                                    name="date"
                                    value="">
                            </div>

                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">To Date</label>
                                <input class="form-control date-picker" id="date" type="text" autocomplete="off"
                                    name="date2"
                                    value="">
                            </div>

                                <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">Filter By</label>
                                <select class="form-control" name="filter_by" id="filter_by"
                                    onchange="this.form.submit()">
                                    <option value="" >-Select-
                                    </option>
                                    <option value="today">Today
                                    </option>
                                    <option value="this_week">This Week
                                    </option>
                                    <option value="last_week">Last Week
                                    </option>
                                    <option value="this_month" >This
                                        Month
                                    </option>
                                    <option value="last_month" >Last
                                        Month
                                    </option>
                                    <option value="last_6_months" >Last
                                        6 Months
                                    </option>
                                    <option value="this_year" >This Year
                                    </option>
                                    <option value="last_year" >Last Year
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
                                    <div class="col-4">
                                        <div class="form-control-plaintext">
                                            {{ @$value->contcat_person }}
                                        </div>
                                    </div>
                                    <div class="col-4 text-center">
                                        <div class="form-control-plaintext">
                                            {{ date('d/m/Y', strtotime($value->created_at)) }}</div>
                                    </div>
                                    <div class="col-4 text-end ">
                                        <div class="form-control-plaintext truncate-text">
                                            {{ str_replace(' ', '', $value->mobile) }}

                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-control-plaintext truncate-text">
                                            {{ @$value->name }}@if (@$value->internal == 1)
                                                <i class="ico icon-bold-info-circle text-primary" aria-hidden="true"
                                                    title="Internal Customer"></i>
                                            @endif
                                            <label>
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

                    <thead>
                        <tr>
                            {{-- <th>@lang('lang.sl') @lang('lang.no')</th> --}}
                            {{-- <th>@lang('lang.photo')</th> --}}
                            <th>@lang('Customer Name')</th>
                            <th>@lang('Contcat Person')</th>
                            <th>@lang('Mobile')</th>
                            <th>@lang('Email')</th>
                            <th class="text-center">@lang('lang.action')</th>
                        </tr>
                    </thead>

                    <tbody>

                        @php $serialcount=1; @endphp
                        @foreach ($customer as $value)
                            <tr>
                                {{-- <td>{{@$serialcount++}}</td> --}}
                                {{-- <td>
                                <img height="100" width="100" src="{{ file_exists(@$value->staff_photo) ? asset($value->staff_photo) : asset('public/uploads/staff/demo/staff.png') }}" alt="">
                            </td> --}}
                                <td><a class="text-dark">
                                        <div
                                            style="width:250px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">
                                            {{ @$value->name }}</div>
                                    </a>
                                </td>
                                <td>
                                    {{ @$value->first_name }}
                                </td>
                                <td>
                                    {{ @$value->mobile }}
                                </td>
                                <td>
                                    <div
                                        style="width:250px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">
                                        {{ @$value->email }}</div>
                                </td>
                                <td>


                                    <div class="d-flex justify-content-center align-items-center gap-1">
                                        <a href="{{ url('customer-from-list/'.$value->id.'?customerform_action=edit') }}" onclick="list_style_new()"
                                            class="btn btn-sm btn-light" title="Comments">
                                            <i class="ico icon-outline-pen-2" style="font-size: 16px;"></i>
                                        </a>






                                        <a class="btn btn-light btn-sm"
                                            href="{{ url('customer-form-delete', $value->id) }}"
                                            onclick="return confirm('Are you sure you want to delete this item?');">
                                            <i class="ico  icon-outline-trash-bin-minimalistic text-dark"
                                                style="font-size: 16px;"></i>
                                        </a>



                                    </div>


                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                    <footer>
                        <tr>
                            <td colspan="5">
                                {{ $customer->appends(request()->input())->links() }}
                            </td>
                        </tr>
                    </footer>
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





    <div class="content-container col-9">
        <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">


            <script>
                $(document).ready(function() {
                    $(document).on('click', '.cust-item', function() {


                        var id = $(this).data('id');

                        $('.cust-item').removeClass('active');
                        $('.cust-item[data-id="' + id + '"]').addClass('active');

                        // Update the browser URL to include selected ID (without reloading)
                        var newUrl = "{{ url('customer-from-list') }}/" + id;
                        window.history.pushState({
                            path: newUrl
                        }, '', newUrl);

                        var action = "{{ URL::to('customer-from-list-view') }}/" + id;
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
                    @include('backEnd.cust-suppl.editCustomerForm_new', $editData)
                @elseif (!empty($selectedCus) && is_array($selectedCus))
                    @include('backEnd.cust-suppl.viewCustomerForm', $selectedCus)
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
                    url: "{{ url('customer-list-search-record') }}",
                    method: "GET",
                    data: {
                        query
                    },
                    success: function(data) {
                        $('#short-list').empty();



                        if (data.data && data.data.length > 0) {
                            $.each(data.data, function(_, amc_list) {

                                let ims = `     <li class="nav-item w-100" role="presentation">
                            <button class="nav-link cust-item"
                                data-id="${amc_list.id}" id="purchase-order-1-tab" data-bs-toggle="tab"
                                data-bs-target="#purchase-order-1" type="button" role="tab"
                                aria-controls="purchase-order-1" aria-selected="true">
                                <div class="row w-100">
                                    <div class="col-4">
                                        <div class="form-control-plaintext">
                                            ${amc_list.contcat_person}
                                        </div>
                                    </div>
                                    <div class="col-4 text-center">
                                        <div class="form-control-plaintext">
                                            ${get_format_date(amc_list.created_at)}</div>
                                    </div>
                                    <div class="col-4 text-end ">
                                        <div class="form-control-plaintext truncate-text">
                                               ${amc_list.mobile}


                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-control-plaintext truncate-text">
                                            ${amc_list.name} ${amc_list.internal === 1 ? '<i class="ico icon-bold-info-circle text-primary" title="Internal Customer"></i>' : ''}

                                            <label>
                                    </div>
                                </div>
                            </button>
                        </li>`;

                  


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





@endsection

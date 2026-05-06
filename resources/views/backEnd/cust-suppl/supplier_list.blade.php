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

                localStorage.setItem('listViewsupplier', 'long');
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

                localStorage.setItem('listViewsupplier', 'short');
            }
        }

        function toggleLongFilters() {
            document.querySelectorAll('#filters-long .filter-field').forEach(el => {
                el.classList.toggle('d-none');
            });
        }

        // Initialize view from localStorage
        document.addEventListener('DOMContentLoaded', () => {
            const savedView = localStorage.getItem('listViewsupplier');
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
                    localStorage.setItem('listViewsupplier', 'short');
                });
            });

            document.querySelectorAll('.edit-btn').forEach(link => {
                link.addEventListener('click', () => {
                    localStorage.setItem('listViewsupplier', 'short');
                });
            });

        });
    </script>






    <style>
        #to_account.select2-hidden-accessible+.select2-container--default .select2-selection--single {
            height: 32px !important;
            line-height: 28px !important;
        }

        #to_account.select2-hidden-accessible+.select2-container--default .select2-selection__rendered {
            line-height: 31px !important;
            font-size: 14px;
        }
    </style>

    <style>
        /* Target Select2 for #from_account only */
        #from_account.select2-hidden-accessible+.select2-container--default .select2-selection--multiple {
            border: 1px solid #d9d9d9 !important;
            border-radius: 0px;
        }

        #from_account.select2-hidden-accessible+.select2-container--default.select2-container--focus .select2-selection--multiple {
            border: 1px solid #d9d9d9 !important;
            border-radius: 0px;
        }
    </style>



    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>

    <?php try { ?>

    <aside class="left-nav col-3" id="leftSidebar">
        <div class="resizer" id="sidebarResizer"></div>
        <div class="short-list" id="filters-short">
            <h4 class="mb-2">Supplier Register
            </h4>


            <div class="search-filter-container mb-4">
                <div class="input-group flex-nowrap">
                    <input type="text" name="documents_number" id="search_invoice" class="form-control"
                        placeholder="Search" aria-label="Search" aria-describedby="addon-wrapping" value="">
                </div>

                <button type="button" class="btn btn-light" id="list_style_button" onclick="list_style_new()">
                    <i class="ico icon-outline-list-down"></i>
                </button>
            </div>
        </div>

        <div class="long-list  d-none" id="filters-long">


            <div class="d-flex align-items-center justify-content-between">
                <h4 class="mb-2">Supplier Register
                </h4>
                <div class="search-filter-container mb-0">

                    <input type="text" id="tableSearch" class="form-control d-inline-block"
                        style="font-size:13px;width: 350px;" placeholder="Search">

                    {{-- <input type="text" id="tableSearch" class="form-control w-25 list_style_expand_btn" style="margin: 2px 100px 0 0" placeholder="Search in List"> --}}


                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ico icon-outline-hamburger-menu"></i>
                        </button>
                        <ul class="dropdown-menu" style="">

                            @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 27)
                                <li><a class="dropdown-item d-flex align-items-center" data-bs-toggle="modal"
                                        data-bs-target="#ModalMergeDuplicateSupplier">
                                        <i class="ico icon-outline-link-square title-15 me-2"></i>
                                        Merge Duplicate</a></li>

                                <li><a class="dropdown-item d-flex align-items-center" data-bs-toggle="modal"
                                        data-bs-target="#ModalMergeSupplier">
                                        <i class="ico icon-outline-link-square title-15 me-2"></i>
                                        Merge</a></li>
                            @endif

                            <li>
                                <input type="hidden" name="copy_url" id="copy_url"
                                    value="{{ url('supplier-from/' . session('logged_session_data.company_id')) }}" />
                                <a id="copy-button" class="dropdown-item d-flex align-items-center">
                                    <i class="ico icon-outline-copy title-15 me-2"></i>
                                    Copy URL</a>

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
                            </li>

                            <li>

                                <a href="{{ url('supplier-from-list') }}" class="dropdown-item d-flex align-items-center">
                                    <i class="ico icon-outline-clock-circle title-15 me-2"></i>
                                    Pending List</a>
                            </li>

                            <li>

                                <a href="{{ url('supplier-import') }}" class="dropdown-item d-flex align-items-center">
                                    <i class="ico icon-outline-import title-15 me-2"></i>
                                    Import</a>
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

                <div class="card" style="width: 100%">
                    <div class="card-body">
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'suppliers', 'method' => 'get', 'id' => 'crm-deals-search']) }}

                        <div class="row">
                            <div class="col-md-3 mb-2 filter-field d-none">
                                <label for="" class="form-check-label">Supplier Name</label>
                                <input class="form-control" type="text" autocomplete="off" name="company_name"
                                    value="{{ $ctrl_company_name }}">
                            </div>
                            <div class="col-md-2 mb-2 filter-field d-none">
                                <label for="" class="form-check-label">Contact Person</label>
                                <input class="form-control" type="text" autocomplete="off" name="contact_name"
                                    value="{{ $ctrl_contact_name }}">
                            </div>

                            <div class="col-md-2 mb-2 filter-field d-none">
                                <label for="" class="form-check-label">Email</label>
                                <input class="form-control" type="text" autocomplete="off" name="email"
                                    value="{{ $ctrl_email }}">
                            </div>
                            <div class="col-md-2 mb-2 filter-field d-none">
                                <label for="" class="form-check-label">VAT Number</label>
                                <input class="form-control" type="text" autocomplete="off" name="vat"
                                    value="{{ $ctrl_vat }}">
                            </div>
                            <div class="col-md-2 filter-field d-none">

                                <button type="submit" class="btn btn-light mt-4 ">
                                    <i class="ico icon-outline-magnifer"></i> Filter
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
                @if (count($supplier) > 0)
                    @foreach ($supplier as $value)
                        <li class="nav-item w-100" role="presentation">
                            <button class="nav-link supp-item {{ $active_id == $value->id ? 'active' : '' }}"
                                data-id="{{ $value->id }}" id="purchase-order-1-tab" data-bs-toggle="tab"
                                data-bs-target="#purchase-order-1" type="button" role="tab"
                                aria-controls="purchase-order-1" aria-selected="true">
                                <div class="row w-100">
                                     <div class="col-12">
                                        <label class="form-control-plaintext truncate-text">
                                            {{ strtoupper($value->name) }}@if (@$value->internal == 1)
                                                <i class="ico icon-bold-info-circle text-primary" aria-hidden="true"
                                                    title="Internal Supplier"></i>
                                            @endif</label>
                                    </div>
                                    <div class="col-4">
                                        <div
                                            class="form-control-plaintext" style="font-size: 11px">
                                            {{ $value->code }}</div>
                                    </div>
                                    <div class="col-4 text-center">
                                        <div class="form-control-plaintext" style="font-size: 11px">
                                            {{ date('d/m/Y', strtotime($value->created_at)) }}</div>
                                    </div>
                                    <div class="col-4 text-end ">
                                        <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                                            {{ str_replace(' ', '', $value->contcat_number) }}

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

            <div class="table-responsive mb-4 mt-2">



                <table id="long-list" class="table table-hover d-none data-table" style="table-layout: fixed;width:100%">

                    <thead class="text-start">

                        <tr>
                            <th style="width: 350px;">@lang('Supplier Name')</th>
                            <th style="width: 200px">@lang('Contact Person')</th>
                            <th style="width: 130px">@lang('Contact Number')</th>
                            <th style="width: 130px">@lang('VAT Number')</th>
                            <th style="width: 130px">@lang('Mobile')</th>
                            <th style="width: 130px">@lang('Email')</th>
                            <th class="text-center" style="width: 90px">@lang('Status')</th>
                            <th class="text-center" style="width: 90px;">@lang('Action')
                            </th>
                        </tr>
                    </thead>

                    <tbody>

                        @php $serialcount = 1; @endphp
                        @foreach ($supplier as $value)
                            <tr @if ($value->status == 2) style="background-color: rgba(0, 0, 0, 0.19);" @endif>

                                <td>
                                    <a href="{{ url('suppliers') }}/{{ $value->id }}">
                                        {{ $value->code }} - {{ $value->name }}   @if (@$value->internal == 1)
                                        <i class="ico icon-bold-info-circle text-primary" aria-hidden="true"
                                            title="Internal Customer"></i>
                                    @endif</a>
                                </td>
                                <td>
                                    {{ $value->contcat_person }}
                                </td>
                                <td>
                                    {{ $value->contcat_number }}
                                </td>
                                <td>
                                    {{ $value->vat_number }}
                                </td>
                                <td>
                                    {{ $value->mobile }}
                                </td>
                                <td>
                                    {{ $value->email }}
                                </td>
                                <td class="text-center">
                                    @if ($value->status == 2)
                                        <span class="text-danger">Inactive</span>
                                    @else
                                        <span class="text-success">Active</span>
                                    @endif
                                </td>
                                <td class="text-center">

                                    <div class="d-flex justify-content-center align-items-center gap-1">
                                        <a href="{{ url('suppliers', $value->id) }}?supplier_action=edit"
                                            class="btn btn-sm btn-light edit-btn" title="Comments">
                                            <i class="ico icon-outline-pen-2" style="font-size: 16px;"></i>
                                        </a>


                                        @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
                                            @if ($value->status == 2)
                                                <button class="btn btn-light btn-sm open-restore-modal" data-id="{{$value->id}}"
                                                    data-reason="{{ $value->delete_reason }}" data-bs-toggle="modal"
                                                    data-bs-target="#restoreModal" type="button" {{-- href="{{ url('supplier-restore/' . $value->id) }}" --}}>
                                                    <i class="ico icon-bold-restart " style="font-size: 16px;"></i>
                                                </button>
                                            @else
                                                <button class="btn btn-light btn-sm open-delete-modal"
                                                    data-id="{{ $value->id }}" data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal">
                                                    <i class="ico icon-outline-trash-bin-minimalistic text-dark" style="font-size: 16px;"></i>
                                                </button>
                                            @endif
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



    <div class="modal fade" id="ModalMergeDuplicateSupplier" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="height: 464px !important;">

            {!! Form::open([
                'class' => 'form-horizontal',
                'files' => true,
                'url' => 'supplier-merge-duplicate',
                'method' => 'post',
                'id' => 'supplier-merge-duplicate',
            ]) !!}

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="poexcelimport">Merge Duplicate Supplier</h4>
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
                                            value="{{ $duplicateName }}">
                                        <div>
                                            @foreach ($groupedCustomers as $customer)
                                                <label for="duplicate_name_{{ $duplicateName }}" class="d-block mb-1">
                                                    {{ $customer->account_code }} - {{ $customer->account_name }}
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



    <div class="modal fade" id="ModalMergeSupplier" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="height: 464px !important;">

            {!! Form::open([
                'class' => 'form-horizontal',
                'files' => true,
                'url' => 'supplier-merge',
                'method' => 'post',
                'id' => 'supplier-merge',
            ]) !!}

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="poexcelimport">Merge Supplier</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0 border-0 shadow-none">
                        <div class="card-body p-3" style="max-height: 500px; overflow-y: auto;">
                            <div class="row">
                                <div class="col-md-6">From Supplier
                                    <select id="from_account" name="from_account[]"
                                        class="form-control js-example-basic-single" multiple required>
                                        @foreach ($supplier_list as $data)
                                            <option value="{{ $data->id }}">{{ $data->account_code }} -
                                                {{ $data->account_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">To Supplier
                                    <select id="to_account" name="to_account"
                                        class="form-control js-example-basic-single" required>
                                        <option value="">Select</option>
                                        @foreach ($supplier_list as $data)
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


    <div class="modal side-panel fade" id="deleteModal" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="top:10%">
            <form method="POST" action="" id="deleteForm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="poexcelimport">Delete Supplier</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body m-0 p-0">
                        <div class="card mb-0 mt-0">
                            <div class="card-body">

                                <p>Please provide a reason for deleting this supplier:</p>
                                <textarea name="delete_reason" class="form-control" rows="3" required></textarea>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-light add-btn ms-2 text-danger">
                            <i class="ico icon-outline-trash-bin-minimalistic text-danger"></i> Delete
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
                        <h4 class="modal-title" id="poexcelimport">Restore Supplier</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body m-0 p-0">
                        <div class="card mb-0 mt-0">
                            <div class="card-body">

                                <div class="mb-2">
                                    <label for="delete_reason" class="form-label text-dark">Delete Reason:</label>
                                </div>
                                <p id="delete_reason" class="border rounded p-2 bg-light text-dark">

                                </p>


                                <p>Please provide a reason for restoring this supplier:</p>
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




    <div class="content-container col-9">
        <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">


            <script>
                $(document).ready(function() {
                    $(document).on('click', '.supp-item', function() {
                        // $('.supp-item').on('click', function() {
                        var id = $(this).data('id');

                        $('.supp-item').removeClass('active');
                        $('.supp-item[data-id="' + id + '"]').addClass('active');

                        // Update the browser URL to include selected ID (without reloading)
                        var newUrl = "{{ url('suppliers') }}/" + id;
                        window.history.pushState({
                            path: newUrl
                        }, '', newUrl);

                        var action = "{{ URL::to('supplier-details') }}/" + id;
                        $('#loading_bg').show();

                        $.ajax({
                            url: action,
                            method: 'GET',
                            success: function(response) {
                                $('#supp-details').html(response);
                            },
                            error: function() {
                                $('#supp-details').html(
                                    '<p class="text-danger">No Details Available.</p>');
                            },
                            complete: function() {
                                $('#loading_bg').hide(); // Always hide loader after request completes
                            }
                        });
                    });
                });
            </script>


            <div class="" role="tabpanel" aria-labelledby="po-tab" id="supp-details">
                @if ($action === 'add')
                    @include('backEnd.cust-suppl.addSupplier', $addSupplier)
                @elseif($action === 'edit')
                    @include('backEnd.cust-suppl.editSupplier', $editData)
                @elseif($action === 'createsupplier')
               
                    @include('backEnd.cust-suppl.AddCustomerToSupplier', $editData)
                    
                @elseif (!empty($selectedSupp) && is_array($selectedSupp))
                    @include('backEnd.cust-suppl.viewSupplier', $selectedSupp)
                @else
                    <form id="supplierForm" method="GET" action="{{ url('suppliers') }}">


                        <input type="hidden" name="supplier_action" value="add">

                        <div onclick="document.getElementById('supplierForm').submit();"
                            class="container-fluid d-flex flex-column justify-content-center align-items-center"
                            style="min-height: 90vh;">

                            <!-- Icon + Heading -->
                            <div class="text-center mb-4">
                                <div class="rounded-circle bg-success text-white d-flex justify-content-center align-items-center mx-auto"
                                    style="width: 80px; height: 80px; font-size: 36px;cursor:pointer">
                                    <i class="ico icon-outline-add-square"></i>
                                </div>
                                <h1 class="fw-bold mt-3" style="cursor:pointer"> Supplier</h1>
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

            $('#search_invoice').on('keyup', function() {
                var query = $(this).val();

                $.ajax({
                    url: "{{ route('suppliers.search') }}",
                    type: "GET",
                    data: {
                        query: query
                    },
                    success: function(data) {




                        $('#short-list').html('');

                        if (data.length > 0) {
                            $.each(data, function(index, suppliers) {





                                let ims = `<li class="nav-item w-100" role="presentation">
                            <button class="nav-link supp-item"
                                data-id="${suppliers.id}" type="button">
                                <div class="row w-100">
                                     <div class="col-12">
                                        <label class="form-control-plaintext truncate-text">
                                               ${suppliers.name}</label>
                                    </div>
                                    <div class="col-4">
                                        <div
                                            class="form-control-plaintext" style="font-size: 11px">
                                            ${suppliers.code}</div>
                                    </div>
                                    <div class="col-4 text-center">
                                        <div class="form-control-plaintext" style="font-size: 11px">
                                               ${get_format_date(suppliers.created_at)}</div>
                                    </div>
                                    <div class="col-4 text-end ">
                                        <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                                               ${suppliers.contcat_number}

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


        $(document).ready(function() {
            $(".list_style_search_btn").on("click", function() {
                $("#search_box").slideToggle(200); // expands/collapses smoothly
            });
        });


        $(document).ready(function() {
            $(document).on('click', '.open-delete-modal', function() {
                var supplierId = $(this).data('id');
                var actionUrl = "{{ url('supplier-inactive') }}/" + supplierId;
                $('#deleteForm').attr('action', actionUrl);
            });

            $(document).on('click', '.open-restore-modal', function() {
                var supplierId = $(this).data('id');
                var delete_reason = $(this).data('reason');
               if (!delete_reason || delete_reason.trim() === '') { 
                    delete_reason = "Not Provided";
                } 
                $('#delete_reason').text(delete_reason);
                var actionUrl = "{{ url('supplier-restore') }}/" + supplierId;
                $('#restoreForm').attr('action', actionUrl);
            });
        });
    </script>


<script>
    $(document).ready(function() {
    let loading = false;
    let page = 1; // current page

    function loadMoreSuppliers() {
        if (loading) return;
        loading = true;

        page++; // next page

        let filters = {
            company_name: $('#filter-company_name').val(),
            contact_name: $('#filter-contact_name').val(),
            email: $('#filter-email').val(),
            vat: $('#filter-vat').val(),
            sales_person: $('#filter-sales_person').val()
        };

        $('#supplier-loader').show();

        $.ajax({
            url: '{{ url("suppliers") }}',
            method: 'GET',
            data: {
                page: page,
                ...filters
            },
            success: function(data) {
                if ($.trim(data) === '') {
                    // no more suppliers
                    $(window).off('scroll');
                } else {
                    $('#supplier-list').append(data);
                }
                loading = false;
                $('#supplier-loader').hide();
            },
            error: function() {
                loading = false;
                $('#supplier-loader').hide();
            }
        });
    }

    // Infinite scroll
    $(window).scroll(function() {
        if ($(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
            loadMoreSuppliers();
        }
    });
});

</script>

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

     @php
        $department_modal = @App\SmHumanDepartment::select('id', 'name')
            ->where('active_status', 1)
            ->orderby('name', 'asc')
            ->get();

    @endphp

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



    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>


@endsection

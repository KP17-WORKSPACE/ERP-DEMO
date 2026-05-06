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

                localStorage.setItem('listViewsupplierlist', 'long');
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

                localStorage.setItem('listViewsupplierlist', 'short');
            }
        }

        function toggleLongFilters() {
            document.querySelectorAll('#filters-long .filter-field').forEach(el => {
                el.classList.toggle('d-none');
            });
        }

        // Initialize view from localStorage
        document.addEventListener('DOMContentLoaded', () => {
            const savedView = localStorage.getItem('listViewsupplierlist');
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
                    localStorage.setItem('listViewsupplierlist', 'short');
                });
            });

            document.querySelectorAll('.edit-btn').forEach(link => {
                link.addEventListener('click', () => {
                    localStorage.setItem('listViewsupplierlist', 'short');
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
            <h4 class="mb-2">Supplier Pending List
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
                <h4 class="mb-2">Supplier Pending List
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

                                <a href="{{ url('suppliers') }}" class="dropdown-item d-flex align-items-center">
                                    <i class="ico icon-outline-document-text title-15 me-2"></i>
                                    Supplier List</a>
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
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'supplier-from-list', 'method' => 'get', 'id' => 'crm-deals-search']) }}

                        <div class="row">
                            <div class="col-md-3 mb-2 filter-field d-none">
                                <label for="" class="form-check-label">Supplier Name</label>
                                <input class="form-control" type="text" autocomplete="off" name="company_name"
                                    value="">
                            </div>
                            <div class="col-md-2 mb-2 filter-field d-none">
                                <label for="" class="form-check-label">Contact Person</label>
                                <input class="form-control" type="text" autocomplete="off" name="contact_name"
                                    value="">
                            </div>

                            <div class="col-md-2 mb-2 filter-field d-none">
                                <label for="" class="form-check-label">Email</label>
                                <input class="form-control" type="text" autocomplete="off" name="email" value="">
                            </div>
                            <div class="col-md-2 mb-2 filter-field d-none">
                                <label for="" class="form-check-label">VAT Number</label>
                                <input class="form-control" type="text" autocomplete="off" name="vat" value="">
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
                @if (count($customer) > 0)
                    @foreach ($customer as $value)
                        <li class="nav-item w-100" role="presentation">
                            <button class="nav-link supp-item {{ $active_id == $value->id ? 'active' : '' }}"
                                data-id="{{ $value->id }}" id="purchase-order-1-tab" data-bs-toggle="tab"
                                data-bs-target="#purchase-order-1" type="button" role="tab"
                                aria-controls="purchase-order-1" aria-selected="true">
                                <div class="row w-100">
                                    <div class="col-4">
                                        <div
                                            class="form-control-plaintext truncate-text @if ($value->status == 2) text-danger @endif">
                                            {{ $value->first_name }} {{ $value->last_name }}</div>
                                    </div>
                                    <div class="col-4 text-center">
                                        <div class="form-control-plaintext">
                                            {{ @App\SysHelper::normalizeToDmy($value->created_at) }}</div>
                                    </div>
                                    <div class="col-4 text-end ">
                                        <div class="form-control-plaintext truncate-text">
                                            {{ str_replace(' ', '', $value->contcat_number) }}

                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-control-plaintext truncate-text">
                                            {{ $value->name }}</label>
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

                            <th class="text-center" style="width: 90px;">@lang('Action')
                            </th>
                        </tr>
                    </thead>

                    <tbody>

                        @php $serialcount = 1; @endphp
                        @foreach ($customer as $value)
                            <tr @if ($value->status == 2) style="background-color: rgba(0, 0, 0, 0.19);" @endif>

                                <td>
                                    <a href="{{ url('suppliers') }}/{{ $value->id }}">
                                       {{ $value->name }}</a>
                                </td>
                                <td>
                                    {{ $value->first_name }} {{$value->last_name}}
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

                                    <div class="d-flex justify-content-center align-items-center gap-1">
                                        <a href="{{ url('supplier-from-list', $value->id) }}?supplier_action=edit"
                                            class="btn btn-sm btn-light edit-btn" title="Comments">
                                            <i class="ico icon-outline-pen-2" style="font-size: 16px;"></i>
                                        </a>


                                        @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
                                            <a class="btn btn-light btn-sm"
                                                href="{{ url('supplier-form-delete/' . $value->id) }}"
                                                onclick="return confirm('Are you sure you want to delete this item?');">
                                                <i class="ico icon-bold-trash-bin-2" style="font-size: 16px;"></i>
                                            </a>
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
                    @include('backEnd.cust-suppl.editSupplierForm', $editData)
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
                                    <div class="col-4">
                                        <div
                                            class="form-control-plaintext">
                                            ${suppliers.code}</div>
                                    </div>
                                    <div class="col-4 text-center">
                                        <div class="form-control-plaintext">
                                               ${get_format_date(suppliers.created_at)}</div>
                                    </div>
                                    <div class="col-4 text-end ">
                                        <div class="form-control-plaintext truncate-text">
                                               ${suppliers.contcat_number}

                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-control-plaintext truncate-text">
                                               ${suppliers.name}</label>
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
    </script>




    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>


@endsection

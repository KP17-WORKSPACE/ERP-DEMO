@extends('backEnd.newmasterpage')
@section('mainContent')
    <style>
        .venus-app .table.table-hover td {
            background-color: inherit;
            padding: 5px 5px;
            vertical-align: middle;
        }
    </style>

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

    <style>
        .venus-app .table.table-hover td {
            padding: 1px 5px;
        }
    </style>

    <style>
        #long-list td,
        #long-list th {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #long-list tr.expand td {
            white-space: normal !important;
            overflow: visible !important;
            text-overflow: unset !important;
            height: auto !important;
        }

        /* Optional for pointer on rows */
        #long-list tbody tr {
            cursor: pointer;
        }
    </style>

    <script>
        $(document).ready(function() {
            $('#long-list tbody tr').on('click', function() {
                $(this).toggleClass('expand');
            });
        });
    </script>

    <style>
        .select2-container--default .select2-selection--single {
            border: 1px solid #d9d9d9 !important;
            border-radius: 0px;
            height: 25px;
            line-height: 10px;
            width: 100%
        }


        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 24px;
            font-weight: 500;

        }

        .select2-container {
            font-size: 13px
        }

        .col-1-5 {
            flex: 0 0 auto;
            width: 12.5%;
        }

        @media (min-width: 576px) {
            .col-sm-1-5 {
                flex: 0 0 auto;
                width: 12.5%;
            }
        }

        @media (min-width: 768px) {
            .col-md-1-5 {
                flex: 0 0 auto;
                width: 12.5%;
            }
        }

        @media (min-width: 992px) {
            .col-lg-1-5 {
                flex: 0 0 auto;
                width: 12.5%;
            }
        }

        @media (min-width: 1200px) {
            .col-xl-1-5 {
                flex: 0 0 auto;
                width: 12.5%;
            }
        }

        a {
            color: #198754 !important
        }
    </style>
    <style>
        #to_partno.select2-hidden-accessible+.select2-container--default .select2-selection--single {
            height: 32px !important;
            line-height: 28px !important;
        }

        #to_partno.select2-hidden-accessible+.select2-container--default .select2-selection__rendered {
            line-height: 31px !important;
            font-size: 14px;
        }
    </style>


    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>

    <?php try { ?>

    <aside class="left-nav col-12" id="leftSidebar">


        <div class="long-list" id="filters-long">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Product List
                </h4>
                <div class="search-filter-container mb-0">

                    {{-- @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 27)
                        <a type="button" class="btn btn-light text-danger add-btn"
                            href="{{ url('item-company-access-update') }}">Update All
                            Product</a>
                        <a type="button" class="btn btn-light text-danger add-btn" data-bs-toggle="modal"
                            data-bs-target="#ModalMergeDuplicateProduct">Merge Duplicate</a>
                        <a type="button" class="btn btn-light text-danger add-btn" data-bs-toggle="modal"
                            data-bs-target="#ModalMergeProduct">Merge</a>
                    @endif

                    <a href="{{ url('brand') }}" class="btn btn-light text-dark add-btn"><i
                            class="ico icon-outline-add-square text-success"></i>
                        Brand</a>
                    <a href="{{ url('item-category') }}" class="btn btn-light text-dark add-btn"><i
                            class="ico icon-outline-add-square text-success"></i> Category</a>
                    <a href="{{ url('create-sub-category') }}" class="btn btn-light text-dark add-btn"><i
                            class="ico icon-outline-add-square text-success"></i> Sub Category</a>
                    
                     --}}
                    <a href="#" data-bs-toggle="modal" data-bs-target="#addproductModal"
                        class="btn btn-light text-dark add-btn"><i class="ico icon-outline-add-square text-success"></i>
                        Add</a>
                    <a href="{{ url('product-import') }}" class="btn btn-light text-dark add-btn"><i
                            class="ico icon-outline-import  text-success"></i> Import</a>

                    <button class="btn btn-light" onclick="toggleLongFilters()">
                        <i class="ico icon-outline-magnifer"></i>
                    </button>

                    <div class="dropdown">
                        <button class="btn btn-light text-dark dropdown-toggle syscom-dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ico icon-outline-hamburger-menu"></i>
                        </button>
                        <ul class="dropdown-menu" style="">
                            <li><a href="{{ url('brand') }}" class="dropdown-item">
                                    Brand</a></li>

                            <li><a href="{{ url('item-category') }}" class="dropdown-item">
                                    Category</a></li>

                            <li><a href="{{ url('create-sub-category') }}" class="dropdown-item">
                                    Sub Category</a></li>

                            @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 27)
                                <li><a href="{{ url('item-company-access-update') }}" class="dropdown-item text-danger">
                                        Update All Product</a></li>

                                <li><a href="#" class="dropdown-item text-danger" data-bs-toggle="modal"
                                        data-bs-target="#ModalMergeDuplicateProduct">
                                        Merge Duplicate</a></li>

                                <li><a href="#" class="dropdown-item  text-danger" data-bs-toggle="modal"
                                        data-bs-target="#ModalMergeProduct">
                                        Merge</a></li>
                            @endif



                        </ul>
                    </div>


                </div>
            </div>

            <div class="search-filter-container mt-1 mb-4 filter-field d-none border">

                <div class="card" style="width: 100%">
                    <div class="card-body">

                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'item-add', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}

                        <div class="row">

                            <div class="col-3 mb-2 ">
                                <label class="form-label">Find Part Number / Product Name / Description</label>

                                <input class="form-control" name="part_number" autocomplete="off" id="part_number1"
                                    value="{{ $ctrl_part_number }}" />

                                <div id="part_number_list1">
                                </div>

                            </div>

                            <script>
                                $(document).ready(function() {

                                    $('#part_number1').keyup(function() {
                                        var query = $(this).val();
                                        if (query != '') {
                                            var _token = $('input[name="_token"]').val();
                                            $.ajax({
                                                url: "{{ route('autocomplete.fetch_product_partnumber') }}",
                                                method: "POST",
                                                data: {
                                                    query: query,
                                                    _token: _token
                                                },
                                                success: function(data) {
                                                    $('#part_number_list1').fadeIn();
                                                    $('#part_number_list1').html(data);
                                                }
                                            });
                                        }
                                    });

                                    $(document).on('click', 'li', function() {
                                        $('#part_number1').val($(this).text());
                                        $('#part_number_list1').fadeOut();
                                    });

                                    $(document).click(function(e) {
                                        if (!$(e.target).closest('#part_number1, #part_number_list1').length) {
                                            $('#part_number_list1').fadeOut();
                                        }
                                    });

                                });
                            </script>

                            <div class="col-2 mb-2 ">
                                <label for="" class="form-label">Brand</label>
                                <select class="form-control js-example-basic-single" name="brand">
                                    <option value="">-Select-</option>
                                    @foreach ($brand as $value)
                                        <option value="{{ @$value->id }}"
                                            @if ($ctrl_brand == $value->id) selected @endif>{{ @$value->title }}</option>
                                    @endforeach
                                </select>

                            </div>

                            <div class="col-2 mb-2">
                                <label for="" class="form-label">Category</label>
                                <select class="form-control js-example-basic-single" name="category">
                                    <option value="">-Select-</option>
                                    @foreach ($category as $value)
                                        <option value="{{ @$value->id }}"
                                            @if ($ctrl_category == $value->id) selected @endif>{{ @$value->category_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-2 mb-2">
                                <label for="" class="form-label">Sub Category</label>
                                <select class="form-control js-example-basic-single" name="sub_category">
                                    <option value="">-Select-</option>
                                    @foreach ($sub_category as $value)
                                        <option value="{{ @$value->id }}"
                                            @if ($ctrl_sub_category == $value->id) selected @endif>
                                            {{ @$value->sub_category_name }}</option>
                                    @endforeach
                                </select>
                            </div>



                            <div class="col-md-3 filter-field d-none">
                                <button type="submit" class="btn btn-success mt-4 rounded-0" id="btnSubmit">Filter</button>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="left-nav-list">

            {{-- <ul id="short-list" class="nav flex-column nav-pills" id="companyTabNavs" role="tablist">
                @if (isset($items))
                    @foreach ($items as $value)
                        <li class="nav-item w-100" role="presentation">
                            <button class="nav-link po-item {{ 1 == $value->id ? 'active' : '' }}"
                                data-id="{{ $value->id }}" id="purchase-order-1-tab" data-bs-toggle="tab"
                                data-bs-target="#purchase-order-1" type="button" role="tab"
                                aria-controls="purchase-order-1" aria-selected="true">
                                <div class="row w-100">
                                    <div class="col-4">
                                        <div class="form-control-plaintext">{{ @$value->item_code }}</div>
                                    </div>
                                    <div class="col-4 text-center">
                                        <div class="form-control-plaintext truncate-text">
                                            {{ @$value->part_number }} </div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="form-control-plaintext truncate-text">
                                            {{ App\SmItem::getBrandName(@$value->brand) }}
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-control-plaintext truncate-text">
                                            {{ @$value->description }} </label>
                                    </div>
                                </div>
                            </button>
                        </li>
                    @endforeach
                @else
                    No Records
                @endif
            </ul> --}}

            <div class="table-responsive mb-4 mt-4">
                <table id="long-list" class="table table-hover" style="table-layout: fixed;width:100%">

                    <thead>
                        <tr>
                            <th class="text-center" width="120px">Product Code</th>
                            <th class="text-start" width="170px">Part Number</th>
                            <th class="text-start">Description</th>
                            <th class="text-start" width="120px">Brand</th>
                            <th class="text-start" width="125px">Category</th>
                            <th class="text-start" width="125px">Sub Category</th>
                            <th class="text-center" width="70px">Status</th>
                            <th class="text-center" style="width: 90px;">@lang('Action')</th>
                        </tr>
                    </thead>


                    <tbody>
                        @if (isset($items))
                            @foreach ($items as $value)
                                <tr>
                                    <td class="text-center" onclick="fn_data({{ @$value->id }})"
                                        data-bs-target="#add_to_do"data-bs-toggle="modal">
                                        <a href="#">{{ @$value->item_code }}</a>
                                    </td>
                                    <td data-bs-target="#add_to_do" data-bs-toggle="modal"
                                        onclick="fn_data({{ @$value->id }})">
                                        <a href="#"> {{ @$value->part_number }}</a>
                                    </td>
                                    <td>{{ @$value->description }}</td>
                                    <td>{{ App\SmItem::getBrandName(@$value->brand) }}</td>
                                    <td>{{ @$value->category != '' ? @$value->category->category_name : '' }}
                                    </td>
                                    <td>{{ App\SmItem::getSubcategoryName(@$value->subcategory_name) }}</td>
                                    <td class="text-center">
                                        @if (@$value->status == 1)
                                            <span class="text-success">Active</span>
                                        @else
                                            <span class="text-danger">Deleted</span>
                                        @endif
                                    </td>
                                    <input type="hidden" id="item_code_{{ @$value->id }}"
                                        value="{{ @$value->item_code }}">
                                    <input type="hidden" id="part_number_{{ @$value->id }}"
                                        value="{{ @$value->part_number }}">
                                    <input type="hidden" id="description_{{ @$value->id }}"
                                        value="{{ @$value->description }}">
                                    <input type="hidden" id="category_name_{{ @$value->id }}"
                                        value="{{ @$value->category->category_name }}">
                                    <input type="hidden" id="subcategory_name_{{ @$value->id }}"
                                        value="{{ App\SmItem::getSubcategoryName(@$value->subcategory_name) }}">
                                    <input type="hidden" id="brand_{{ @$value->id }}"
                                        value="{{ App\SmItem::getBrandName(@$value->brand) }}">
                                    <input type="hidden" id="product_type_{{ @$value->id }}"
                                        value="{{ App\SmItem::getProductType(@$value->product_type) }}">
                                    <input type="hidden" id="vat_{{ @$value->id }}" value="{{ @$value->vat }}">
                                    <input type="hidden" id="uom_{{ @$value->id }}" value="{{ @$value->uom }}">
                                    <input type="hidden" id="coo_{{ @$value->id }}" value="{{ @$value->coo }}">
                                    <input type="hidden" id="hscode_{{ @$value->id }}"
                                        value="{{ @$value->hscode }}">
                                    <input type="hidden" id="weight_{{ @$value->id }}"
                                        value="{{ @$value->weight }}">
                                    @php $cb = $user_list->where('user_id',$value->created_by)->max('full_name'); @endphp
                                    <input type="hidden" id="created_by_{{ @$value->id }}"
                                        value="{{ @$cb }}">
                                    <input type="hidden" id="created_at_{{ @$value->id }}"
                                        value="{{ date('d/m/Y', strtotime(@$value->created_at)) }}">



                                    <td>
                                        {{-- <a class="btn-sm btn-primary"
                                            href="{{ url('item-add/' . @$value->id . '/edit') }}">@lang('lang.edit')</a>
                                        <a data-modal-size="modal-md" data-target="#add_to_do" data-toggle="modal"
                                            class="btn-sm btn-success" href="#"
                                            onclick="fn_data({{ @$value->id }})">@lang('View')</a>
                                        @if (in_array(156, @$module_links) || Auth::user()->role_id == 1)
                                            <a class="btn-sm btn-danger" data-modal-size="modal-md" title="Delete Item"
                                                href="{{ url('delete-item-view/' . @$value->id) }}"
                                                onclick="return confirm('Are you sure?')">@lang('lang.delete')</a>
                                        @endif --}}
                                        <div class="d-flex justify-content-center align-items-center gap-1">
                                            <a href="#" data-id="{{ @$value->id }}"
                                                class="btn btn-sm btn-light edit-product-btn" data-bs-toggle="modal"
                                                data-bs-target="#editproductModal" {{-- href="{{ url('item-add/' . @$value->id . '/edit') }}" --}}>
                                                <i class="ico icon-outline-pen-2" style="font-size: 16px;"></i></a>

                                            @if (in_array(156, @$module_links) || Auth::user()->role_id == 1)
                                                <a class="btn btn-light btn-sm" data-modal-size="modal-md"
                                                    title="Delete Item" href="{{ url('delete-item/' . @$value->id) }}"
                                                    onclick="return confirm('Are you sure?')"> <i
                                                        class="ico icon-bold-trash-bin-2"
                                                        style="font-size: 16px;"></i></a>
                                            @endif

                                        </div>


                                    </td>
                            @endforeach
                        @endif
                    </tbody>
                    <?php try{ ?>
                    <tfoot>
                        <tr>
                            <td colspan="7" class="text-center">
                                @if (!$hasFilters && count($items) > 0)
                                    {{ $items->appends(request()->input())->links() }}
                                @endif
                            </td>
                        </tr>
                    </tfoot>
                    <?php }catch (\Exception $e) { } ?>

                </table>
            </div>
        </div>
    </aside>






    @include('backEnd.inventory.itemAddModal')

    @include('backEnd.inventory.itemEditModal')







    <div class="modal fade" id="ModalMergeDuplicateProduct" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="height: 464px !important;">

            {!! Form::open([
                'class' => 'form-horizontal',
                'files' => true,
                'url' => 'product-merge-duplicate',
                'method' => 'post',
                'id' => 'product-merge-duplicate',
            ]) !!}

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="poexcelimport">Merge Duplicate Product</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>


                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0 border-0 shadow-none">
                        <div class="card-body p-3" style="max-height: 500px; overflow-y: auto;">

                            <div class="form-control" style="height: 300px; overflow-y: scroll; overflow-x: hidden;">
                                @foreach ($dup_item_list as $index => $data)
                                    @php $inputId = 'dup_part_no_' . $index; @endphp
                                    <input type="checkbox" id="{{ $inputId }}" name="dup_part_no[]"
                                        value="{{ $data }}" checked>
                                    <label for="{{ $inputId }}">{{ $data }}</label><br>
                                @endforeach
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



    <div class="modal fade" id="ModalMergeProduct" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="height: 464px !important;">

            {!! Form::open([
                'class' => 'form-horizontal',
                'files' => true,
                'url' => 'product-merge',
                'method' => 'post',
                'id' => 'product-merge',
            ]) !!}


            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="poexcelimport">Merge Product</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>


                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0 border-0 shadow-none">
                        <div class="card-body p-3" style="max-height: 500px; overflow-y: auto;">

                            <div class="row">
                                <div class="col-md-6">From Part Number
                                    <select id="from_partno" name="from_partno[]"
                                        class="form-control js-example-basic-single" multiple required>
                                        @foreach ($item_list as $data)
                                            <option value="{{ $data->id }}">{{ $data->part_number }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">To Part Number
                                    <select id="to_partno" name="to_partno" class="form-control js-example-basic-single"
                                        required>
                                        <option value="">Select</option>
                                        @foreach ($item_list as $data)
                                            <option value="{{ $data->id }}">{{ $data->part_number }}</option>
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

    <div class="modal fade" id="add_to_do" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel"
        aria-hidden="true">
        <div class="modal-dialog " style="height: 464px !important;">



            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="poexcelimport">Product Detail</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>


                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0 border-0 shadow-none">
                        <div class="card-body p-3" style="max-height: 500px; overflow-y: auto;">
                            <style>
                                .modal-body .item-label {
                                    font-weight: 500;
                                    color: #444;
                                    min-width: 140px;
                                }

                                .modal-body .item-value {
                                    color: #222;
                                    font-weight: 400;
                                }

                                .modal-body .info-row {
                                    display: flex;

                                    padding: 4px 0;
                                    border-bottom: 1px dashed #ddd;
                                }
                            </style>

                            <div class="modal-body">
                                <div class="info-row">
                                    <div class="item-label">Item Code</div>
                                    <div class="item-value" id="lbl_item_code"></div>
                                </div>
                                <div class="info-row">
                                    <div class="item-label">Part Number</div>
                                    <div class="item-value" id="lbl_part_number"></div>
                                </div>
                                <div class="info-row">
                                    <div class="item-label">Description</div>
                                    <div class="item-value" id="lbl_description"></div>
                                </div>
                                <div class="info-row">
                                    <div class="item-label">Category</div>
                                    <div class="item-value" id="lbl_category_name"></div>
                                </div>
                                <div class="info-row">
                                    <div class="item-label">Sub Category</div>
                                    <div class="item-value" id="lbl_subcategory_name"></div>
                                </div>
                                <div class="info-row">
                                    <div class="item-label">Brand</div>
                                    <div class="item-value" id="lbl_brand"></div>
                                </div>
                                <div class="info-row">
                                    <div class="item-label">Product Type</div>
                                    <div class="item-value" id="lbl_product_type"></div>
                                </div>
                                <div class="info-row">
                                    <div class="item-label">VAT</div>
                                    <div class="item-value" id="lbl_vat"></div>
                                </div>
                                <div class="info-row">
                                    <div class="item-label">UOM</div>
                                    <div class="item-value" id="lbl_uom"></div>
                                </div>
                                <div class="info-row">
                                    <div class="item-label">COO</div>
                                    <div class="item-value" id="lbl_coo"></div>
                                </div>
                                <div class="info-row">
                                    <div class="item-label">HS Code</div>
                                    <div class="item-value" id="lbl_hscode"></div>
                                </div>
                                <div class="info-row">
                                    <div class="item-label">Weight</div>
                                    <div class="item-value" id="lbl_weight"></div>
                                </div>
                                <div class="info-row">
                                    <div class="item-label">Created By</div>
                                    <div class="item-value" id="lbl_created_by"></div>
                                </div>
                                <div class="info-row">
                                    <div class="item-label">Created Date</div>
                                    <div class="item-value" id="lbl_created_at"></div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="ico icon-outline-close"></i> Close
                    </button>

                </div>
            </div>




        </div>
    </div>
    <script>
        function fn_data(id) {
            var item_code = $('#item_code_' + id + '').val();
            var part_number = $('#part_number_' + id + '').val();
            var description = $('#description_' + id + '').val();
            var category_name = $('#category_name_' + id + '').val();
            var subcategory_name = $('#subcategory_name_' + id + '').val();
            var brand = $('#brand_' + id + '').val();
            var product_type = $('#product_type_' + id + '').val();
            var vat = $('#vat_' + id + '').val();
            var uom = $('#uom_' + id + '').val();
            var coo = $('#coo_' + id + '').val();
            var hscode = $('#hscode_' + id + '').val();
            var weight = $('#weight_' + id + '').val();
            var created_by = $('#created_by_' + id + '').val();
            var created_at = $('#created_at_' + id + '').val();

            $('#lbl_item_code').html(item_code);
            $('#lbl_part_number').html(part_number);
            $('#lbl_description').html(description);
            $('#lbl_category_name').html(category_name);
            $('#lbl_subcategory_name').html(subcategory_name);
            $('#lbl_brand').html(brand);
            $('#lbl_product_type').html(product_type);
            $('#lbl_vat').html(vat);
            $('#lbl_uom').html(uom);
            $('#lbl_coo').html(coo);
            $('#lbl_hscode').html(hscode);
            $('#lbl_weight').html(weight);
            $('#lbl_created_by').html(created_by);
            $('#lbl_created_at').html(created_at);
        }
    </script>

    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@endsection

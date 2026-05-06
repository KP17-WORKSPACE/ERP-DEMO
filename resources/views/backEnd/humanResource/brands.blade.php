@extends('backEnd.newmasterpage')
@section('mainContent')
    <style>
        .venus-app .table.table-hover td {
            background-color: inherit;
            padding: 5px 5px;
            vertical-align: middle;
        }
    </style>


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



    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>

    <?php try { ?>

    <aside class="left-nav col-12" id="leftSidebar">


        <div class="long-list" id="filters-long">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="mb-2">Brand List
                </h4>
                <div class="search-filter-container mb-0">

                    <input type="text" id="tableSearch" class="form-control d-inline-block"
                        style="font-size:13px;width: 350px;" placeholder="Search">
                

                    @if (in_array(105, @$module_links) || Auth::user()->role_id == 1)
                        <button class="btn btn-light text-dark add-btn" data-bs-toggle="modal" data-bs-target="#addBrand"><i
                                class="ico icon-outline-add-square text-success"></i>
                            Add</button>
                    @endif
                   
            

                    <div class="dropdown">
                        <button class="btn btn-light text-dark dropdown-toggle syscom-dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ico icon-outline-hamburger-menu"></i>
                        </button>
                        <ul class="dropdown-menu" style="">
                            <li><a href="{{ url('item-add') }}" class="dropdown-item">Products</a></li>
                            <li><a href="{{ url('item-category') }}" class="dropdown-item">Category</a></li>
                            <li><a href="{{ url('create-sub-category') }}" class="dropdown-item">Sub Category</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a href="{{ url('item-store-import') }}" class="dropdown-item">Import Opening Stock</a></li>
                            <li><a href="{{ url('product-import') }}" class="dropdown-item">Import Products</a></li>
                            <li><a href="{{ url('brand-import') }}" class="dropdown-item">Import Brands</a></li>
                            <li><a href="{{ url('category-import') }}" class="dropdown-item">Import Categories</a></li>
                            <li><a href="{{ url('subcategory-import') }}" class="dropdown-item">Import Sub Categories</a></li>
                        </ul>
                    </div>

                </div>
            </div>


        </div>

        <div class="left-nav-list">

            <div class="table-responsive mb-4 mt-4">
                <table id="long-list" class="table table-hover data-table" style="table-layout: fixed;width:100%">
                    <thead>
                        <tr class="">
                            <th>Brands</th>
                            <th>Created By</th>
                            <th>Updated By</th>
                            <th>Company</th>

                            <th class="text-center" style="width: 90px;">@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($brands as $brand)
                            <tr class="">
                                <td>
                                    <div> {{ @$brand->title }}</div>


                                </td>
                                <td>{{ @$brand->createdby->full_name }}
                                     <span class="text-muted ms-1">({{ date('d/m/Y h:i A', strtotime($brand->created_at)) }})</span>
                                </td>
                                <td>{{ @$brand->updatedby->full_name }}
                                    <span class="text-muted"> ({{ date('d/m/Y h:i A', strtotime($brand->updated_at)) }})</span></td>
                                <td>{{ @$brand->companyid->company_name }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center align-items-center gap-1">
                                        @if (in_array(106, @$module_links) || Auth::user()->role_id == 1)
                                            <a class="btn btn-sm btn-light edit-brand-btn" data-bs-toggle="modal"
                                                data-id="{{ $brand->id }}" data-title="{{ $brand->title }}"
                                                data-bs-target="#editBrand"> <i class="ico icon-outline-pen-2"
                                                    style="font-size: 16px;"></i></a>
                                            </a>
                                        @endif
                                        @if (in_array(107, @$module_links) || Auth::user()->role_id == 1)
                                            <a class="btn btn-sm btn-light"
                                                href="{{ url('brand-delete/' . $brand->id) }}"
                                                onclick="return confirm('Are you sure you want to delete this brand?')"><i
                                                    class="ico icon-bold-trash-bin-2" style="font-size: 16px;"></i></a>
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



    <div class="modal side-panel fade" id="addBrand" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" style="height: 157px !important;">


            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="poexcelimport">Add Brand</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                {{ Form::open([
                    'class' => 'form-horizontal',
                    'files' => true,
                    'url' => 'brand',
                    'method' => 'POST',
                    'enctype' => 'multipart/form-data',
                ]) }}


                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0 border-0 shadow-none">
                        <div class="card-body p-3" style="max-height: 500px; overflow-y: auto;">

                            <div class="white-box">
                                <div class="add-visitor">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <label class="form-label">@lang('Brand') @lang('Name')
                                                <span>*</span></label>
                                            <input class="primary-input form-control" type="text" name="title"
                                                autocomplete="off" value="">
                                            <input type="hidden" name="id" value="">
                                            <span class="focus-border"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-light add-btn ms-2">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                    </button>
                </div>
                {{ Form::close() }}

            </div>




        </div>
    </div>


    <div class="modal side-panel fade" id="editBrand" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" style="height: 157px !important;">


            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="poexcelimport">Edit Brand</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                {{ Form::open([
                    'class' => 'form-horizontal',
                    'files' => true,
                
                    'method' => 'POST',
                    'enctype' => 'multipart/form-data',
                    'id' => 'editBrandForm',
                ]) }}

                @method('PUT')

                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="id" id="edit_brand_id">

                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0 border-0 shadow-none">
                        <div class="card-body p-3" style="max-height: 500px; overflow-y: auto;">

                            <div class="white-box">
                                <div class="add-visitor">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <label class="form-label">@lang('Brand') @lang('Name')
                                                <span>*</span></label>
                                            <input class="primary-input form-control" id="edit_brand_title"
                                                type="text" name="title" autocomplete="off" value="">

                                            <span class="focus-border"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-light add-btn ms-2">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Update
                    </button>
                </div>
                {{ Form::close() }}

            </div>




        </div>
    </div>


    <script>
        $(document).on('click', '.edit-brand-btn', function() {
            var id = $(this).data('id');
            var title = $(this).data('title');

            $('#edit_brand_id').val(id);
            $('#edit_brand_title').val(title);

            console.log(id, title)

            // Update form action dynamically
            $('#editBrandForm').attr('action', '/brand/' + id);
        });
    </script>

    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@endsection

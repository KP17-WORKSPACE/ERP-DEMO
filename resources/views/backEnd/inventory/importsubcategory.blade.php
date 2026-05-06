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

    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>

    <?php try { ?>

    <div class="content-container col-9">
        <div class="tab-content display-flex-tabs" id="subCategoryImportTabContent">
            <div class="tab-pane fade show active" id="sub-category-import-1" role="tabpanel" aria-labelledby="sub-category-import-1-tab">
                <div class="purchase-order-content-header">
                    <h4 class="purchase-order-content-header-left mt-2">Import Sub Categories</h4>
                    <div class="purchase-order-content-header-right">
                        <div class="dropdown">
                            <button class="btn btn-light text-dark dropdown-toggle syscom-dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ico icon-outline-hamburger-menu"></i>
                            </button>
                            <ul class="dropdown-menu" style="">
                                <li><a href="{{ url('item-add') }}" class="dropdown-item">Products</a></li>
                                <li><a href="{{ url('brand') }}" class="dropdown-item">Brand</a></li>
                                <li><a href="{{ url('item-category') }}" class="dropdown-item">Category</a></li>
                                <li><a href="{{ url('create-sub-category') }}" class="dropdown-item">Sub Category</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                {{ Form::open(['class' => 'form-horizontal', 'url' => 'subcategory-import-list', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}

                                <div class="boxed-formctrl">
                                    <div class="add-visitor">
                                        <div class="row mb-10">
                                            <div class="col-lg-12">
                                                @if (session()->has('message-success'))
                                                    <div class="alert alert-success mb-20">
                                                        {{ session()->get('message-success') }}
                                                    </div>
                                                @elseif(session()->has('message-danger'))
                                                    <div class="alert alert-danger">
                                                        {{ session()->get('message-danger') }}
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="col-lg-4 mb-2">
                                                <div class="input-effect">
                                                    <label class="txtlbl">Choose File <span>*.xlsx, *.xls, *.csv</span> (<a href="{{ url('public/uploads/subcategory_upload/subcategory_import_sample_file.csv') }}" target="_blank">Sample File</a>)</label>
                                                    <input class="form-control" type="file" accept=".csv,.xlsx,.xls" name="import_file" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="input-effect d-flex gap-2" style="margin-top: 12px">
                                                    <button class="btn btn-light mt-2">Submit</button>
                                                    @if (count($data) > 0)
                                                        <a href="{{ url('subcategory-import-clear') }}" class="btn btn-light mt-2">Clear Data</a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>

                            <div class="col-lg-12 " id="long-list" style="overflow: scroll;">
                                <table class="table table-hover" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th class="text-start" width="150px">Category</th>
                                            <th class="text-start" width="150px">Sub Category</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $existingCategories = collect($itemCategories)->pluck('category_name')->map(function ($t) {
                                                return mb_strtolower(trim($t));
                                            })->toArray();
                                        @endphp

                                        @if (count($data) > 0)
                                            @foreach ($data as $value)
                                                @php
                                                    $category = trim(@$value->category_name);
                                                    $subCategory = trim(@$value->sub_category_name);
                                                    $isCatDuplicate = in_array(mb_strtolower($category), $existingCategories);
                                                @endphp
                                                <tr @if(!$isCatDuplicate) class="bg-warning" @endif>
                                                    <td>{{ $category }}</td>
                                                    <td>{{ $subCategory }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                            @if (count($data) > 0)
                                <div class="col-lg-12 text-center">
                                    {{ Form::open(['class' => 'form-horizontal', 'url' => 'subcategory-import-data', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}

                                    <div class="d-flex justify-content-center mt-2">
                                        <button class="btn btn-light">
                                            <i class="ico icon-outline-import" style="font-size: 16px"></i> Import Data
                                        </button>
                                    </div>

                                    {{ Form::close() }}
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $("input:text").keypress(function(event) {
                if (event.keyCode == 13) {
                    event.preventDefault();
                    return false;
                }
            });
        });
    </script>

    <?php } catch (\Exception $e) { ?> {{ $e }} <?php } ?>

@endsection
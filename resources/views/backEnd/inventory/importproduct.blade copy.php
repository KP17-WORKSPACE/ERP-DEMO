@extends('backEnd.masterpage')
@section('mainContent')

@php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
@endphp

<?php try { ?>
<div class="container-fluid">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h2 class="page-heading m-0">Import Product</h2>
            <span class="page-label">Home - Import Product</span>
        </div>
        <div>
            <a href="{{url('brand')}}" class="btn btn-info"><i class="far fa fa-plus" aria-hidden="true"></i> Brand</a>
            <a href="{{url('item-category')}}" class="btn btn-info"><i class="far fa fa-plus" aria-hidden="true"></i> Category</a>
            <a href="{{url('create-sub-category')}}" class="btn btn-info"><i class="far fa fa-plus" aria-hidden="true"></i> Sub Category</a>
            <a href="{{url('item-add')}}" class="btn btn-info"><i class="far fa fa-plus" aria-hidden="true"></i> Product</a>
            <a href="{{url('product-import')}}" class="btn btn-warning"><i class="far fa fa-plus" aria-hidden="true"></i> Import Product</a>
        </div>
    </div>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-12">
                    {{ Form::open(['class' => 'form-horizontal','url' => 'product-import-list', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
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
                                
                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="txtlbl">Choose File<span>*.csv</span> (<a href="{{ url('public/uploads/product_upload/product_import_sample_file.xlsx') }}" target="_blank">Sample File</a>)</label>
                                        <input class="form-control" type="file" accept=".csv" name="import_file" required>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="input-effect">
                                        <label class="txtlbl"><br />
                                        <button class="btn btn-primary mt-2">
                                            <span class="ti-check"></span> Submit
                                        </button>
                                        @if (count($data)>0)
                                        <a href="{{ url('product-import-clear') }}" class="btn btn-info mt-2">Clear Data</a> @endif
                                    </div>
                                </div>
                            </div>                            
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>


                
                <div class="col-lg-12">
                    <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                            <thead>
                                @if (session()->has('message-success-delete') != '' || session()->get('message-danger-delete') != '')
                                    <tr>
                                        <td colspan="11">
                                            @if (session()->has('message-success-delete'))
                                                <div class="alert alert-success">
                                                    {{ session()->get('message-success-delete') }}
                                                </div>
                                            @elseif(session()->has('message-danger-delete'))
                                                <div class="alert alert-danger">
                                                    {{ session()->get('message-danger-delete') }}
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <th width="150px">part_number</th>
                                    <th width="150px">brand</th>
                                    <th width="100px">product_type</th>
                                    <th width="150px">category_name</th>
                                    <th width="150px">subcategory_name</th>
                                    <th>description</th>
                                    <th width="100px">vat</th>
                                    <th width="100px">uom</th>
                                    <th width="100px">coo</th>
                                    <th width="100px">hscode</th>
                                    <th width="100px">weight</th>
                                </tr>
                            </thead>

                            <tbody>
                                @php $brand_id=0; $category_id=0; $subcategory_id=0; $product_type=0; @endphp
                                @if (count($data)>0)
                                    @foreach ($data as $value)
                                    @php 
                                        $brand_id = $brand->where('title',$value->brand)->max('id');
                                        $category_id = $cat->where('category_name',$value->category_name)->max('id');
                                        $subcategory_id = $subcat->where('sub_category_name',$value->subcategory_name)->max('id');
                                    @endphp

                                        <tr>
                                            <td>{{ @$value->part_number }}</td>
                                            <td @if ($brand_id == "") class="bg-warning" @endif>{{ @$value->brand }}</td>
                                            <td>{{ @$value->product_type }}</td>
                                            <td @if ($category_id == "") class="bg-warning" @endif>{{ @$value->category_name }}</td>
                                            <td @if ($subcategory_id == "") class="bg-warning" @endif>{{ @$value->subcategory_name }}</td>
                                            <td>{{ @$value->description }}</td>
                                            <td>{{ @$value->vat }}</td>
                                            <td>{{ @$value->uom }}</td>
                                            <td>{{ @$value->coo }}</td>
                                            <td>{{ @$value->hscode }}</td>
                                            <td>{{ @$value->weight }}</td>
                                    @endforeach
                                @endif
                            </tbody>
                            <?php try{ ?>
                            <footer>
                                <tr>
                                    <td colspan="11">
                                        
                                    </td>
                                </tr>
                            </footer>
                            <?php }catch (\Exception $e) { } ?>
                        </table>
                </div>
                @if (count($data)>0)
                <div class="col-lg-12 text-center">
                    {{ Form::open(['class' => 'form-horizontal','url' => 'product-import-data', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                        @if (session()->has('message-success'))
                            <div class="alert alert-success mb-20">
                                {{ session()->get('message-success') }}
                            </div>
                        @elseif(session()->has('message-danger'))
                            <div class="alert alert-danger">
                                {{ session()->get('message-danger') }}
                            </div>
                        @endif
                            <button class="btn btn-danger mt-2">
                                <span class="ti-check"></span> Import Data
                            </button>
                    </div>
                    {{ Form::close() }}
                </div>
                @endif

            </div>
        </div>
    </div>    

</div>
<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>


@endsection

@section('script')
    <script>

$(document).ready(function()
    {
        // Stop user to press enter in textbox
        $("input:text").keypress(function(event) {
            if (event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
        });
});

    </script>
@endsection

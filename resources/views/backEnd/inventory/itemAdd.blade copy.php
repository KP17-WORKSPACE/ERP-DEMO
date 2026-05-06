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
            <h2 class="page-heading m-0">Add Product</h2>
            <span class="page-label">Home - Add Product</span>
        </div>
        <div>
            @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 27)
            <a type="button" class="btn btn-danger" href="{{url('item-company-access-update')}}">Update All Product</a>
            <a type="button" class="btn btn-danger" data-toggle="modal" data-target="#ModalMergeDuplicateProduct">Merge Duplicate</a>
            <a type="button" class="btn btn-danger" data-toggle="modal" data-target="#ModalMergeProduct">Merge</a>
            @endif
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
                    @if (isset($editData))
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'item-list/' . @$editData->id, 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}
                    @else
                            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'item-list', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                    @endif
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

                                {{-- <div class="col-lg-4">
                                    <div class="input-effect">
                                        <input
                                            class="form-control"
                                            type="text" name="item_name" autocomplete="off"
                                            value="{{ isset($editData) ? @$editData->item_name : '' }}">
                                        <label>@lang('lang.item') @lang('lang.name') <span>*</span> </label>
                                    </div>
                                </div> --}}
                                
                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="txtlbl">Product Code<span>*</span> </label>
                                        <input
                                            class="form-control"
                                            type="text" name="item_code"
                                            value="{{ isset($editData) ? @$editData->item_code : @App\SysHelper::get_new_code('sm_items','ITM','item_code') }}"
                                            required readonly>
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="txtlbl">Part Number<span>*</span> </label>
                                        <input class="form-control" type="text" id="part_number" name="part_number" autocomplete="off" required value="{{ isset($editData) ? @$editData->part_number : '' }}">
                                            <div id="part_number_list">
                                            </div>                            
                                            <script>
                                                $(document).ready(function(){
                                                
                                                 $('#part_number').keyup(function(){ 
                                                        var query = $(this).val();
                                                        if(query != '')
                                                        {
                                                         var _token = $('input[name="_token"]').val();
                                                         $.ajax({
                                                          url:"{{ route('autocomplete.fetch_product_partnumber') }}",
                                                          method:"POST",
                                                          data:{query:query, _token:_token},
                                                          success:function(data){
                                                           $('#part_number_list').fadeIn();  
                                                                    $('#part_number_list').html(data);
                                                          }
                                                         });
                                                        }
                                                    });
                                                
                                                    $(document).on('click', 'li', function(){  
                                                        $('#part_number').val($(this).text());  
                                                        $('#part_number_list').fadeOut();  
                                                    });  
                                                
                                                });
                                                </script>

                                    </div>
                                </div>
                                <div class="col-lg-6 mb-2">
                                    <div class="input-effect">
                                        <label class="txtlbl">Description<span>*</span> </label>
                                        <input class="form-control" name="description" id="description" value="{{isset($editData)?@$editData->description:''}}" required />
                                    </div>
                                </div>

                                <div class="col-lg-2 mb-2">
                                    <div class="input-effect">
                                        <label class="txtlbl">Brand<span>*</span> </label>
                                        <select
                                            class="form-control"
                                            name="brand" id="brand" required>
                                            <option value=""></option>
                                            @foreach ($brands as $key => $value)
                                                <option value="{{ @$value->id }}"
                                                    @if (isset($editData)) @if ($editData->brand == $value->id)
                                                        @lang('lang.selected') @endif
                                                    @endif
                                                    >{{ @$value->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2 mb-2">
                                    <div class="input-effect">
                                        <label class="txtlbl">Category<span>*</span> </label>
                                        <select
                                            class="form-control"
                                            name="category_name" id="category_name" required>
                                            @foreach ($itemCategories as $key => $value)
                                                <option value="{{ @$value->id }}"
                                                    @if (isset($editData)) @if ($editData->category_name == $value->id)
                                                        @lang('lang.selected') @endif
                                                    @endif
                                                    >{{ @$value->category_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2 mb-2">
                                    <div class="input-effect">
                                        <div class="input-effect" id="sectionSubcategoryDiv">
                                            <label class="txtlbl">Sub Category<span>*</span> </label>
                                            <select
                                                class="form-control"
                                                name="subcategory_name" id="sectionSelectSubcategory" required>
                                                @foreach ($SuCategories as $key => $value)
                                                    <option value="{{ @$value->id }}"
                                                        @if (isset($editData)) @if ($editData->subcategory_name == $value->id)
                                                            @lang('lang.selected') @endif
                                                        @endif
                                                        >{{ @$value->sub_category_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-2 mb-2">
                                    <div class="input-effect">
                                        <label class="txtlbl">Product Type<span>*</span> </label>
                                        <select class="form-control" name="product_type" id="product_type" required>
                                            @foreach ($producttype as $key => $value)
                                                <option value="{{ @$value->id }}"
                                                    @if (isset($editData)) @if ($editData->product_type == $value->id)
                                                        @lang('lang.selected') @endif
                                                    @endif
                                                    >{{ @$value->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4 mb-2">
                                    <div class="input-effect">
                                        <label class="txtlbl">Company<span>*</span> </label>
                                        <select
                                            class="form-control js-example-basic-single" name="company_id[]" id="company_id" multiple required>
                                            
                                            @foreach ($company as $value)
                                                <option value="{{ @$value->id }}" selected >{{ @$value->company_name }}</option>
                                            @endforeach
                                            <?php /*
                                            @if(isset($editData))
                                                        @if(!empty($editData->company_id))
                                                            @if(str_contains($editData->company_id, $value->id)) selected @endif
                                                        @endif
                                                    @else
                                                        @if($value->id==1) selected @endif
                                                    @endif
                                                    */ ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-10">
                                <div class="col-lg-2">
                                    <div class="input-effect">
                                        <label class="txtlbl">VAT<span></span> </label>
                                        <input
                                            class="form-control"
                                            type="text" name="vat" autocomplete="off"
                                            value="{{ isset($editData) ? @$editData->vat : '5' }}">
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="input-effect">
                                        <label class="txtlbl">UOM<span></span> </label>
                                        <input class="form-control" type="text" name="uom" autocomplete="off"
                                            value="{{ isset($editData) ? @$editData->uom : 'Nos' }}">
                                    </div>
                                </div>
                                <div class="col-lg-2" style="display: none;">
                                    <div class="input-effect">
                                        <label class="txtlbl">Status<span></span> </label>
                                        <select
                                            class="form-control"
                                            name="status" id="status">
                                            <option value="1"
                                                @if (isset($editData)) @if (@$editData->status == 1) selected @endif
                                                @endif>Active</option>
                                            <option value="2"
                                                @if (isset($editData)) @if (@$editData->status == 2) selected @endif
                                                @endif>In Active</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="input-effect">
                                        <label class="txtlbl">COO<span></span> </label>
                                        <input
                                            class="form-control"
                                            type="text" name="coo" autocomplete="off"
                                            value="{{ isset($editData) ? @$editData->coo : '' }}">
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="input-effect">
                                        <label class="txtlbl">HS Code<span></span> </label>
                                        <input
                                            class="form-control"
                                            type="text" name="hscode" autocomplete="off"
                                            value="{{ isset($editData) ? @$editData->hscode : '' }}">
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="input-effect">
                                        <label class="txtlbl">Weight<span></span> </label>
                                        <input
                                            class="form-control"
                                            type="text" name="weight" autocomplete="off"
                                            value="{{ isset($editData) ? @$editData->weight : '' }}">
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="input-effect">
                                        <label class="txtlbl"><br />
                                        <button class="btn btn-primary mt-2" data-toggle="tooltip" title="{{ @$tooltip }}" id="btnSubmit">
                                            <span class="ti-check"></span> @if (isset($editData)) Update Product @else Save Product @endif
                                        </button>
                                    </div>
                                </div>

                            </div>
                            
                        </div>
                        <div class="row mt-1">
                            <div class="col-lg-12 text-right">
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>


                
                <div class="col-lg-12">

                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'item-add', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                        <div class="row">
                            <div class="col-lg-2">
                                <div class="input-effect">
                                    <label class="txtlbl font-weight-bold">Find Part Number / Product Name</label>
                                    <input class="form-control" name="part_number" autocomplete="off" id="part_number" required />
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="input-effect mt-2"><br />
                                    <button type="submit" class="btn btn-warning" id="btnSubmit"><i class="fa fa-search" aria-hidden="true"></i> Find</button>
                                </div>
                            </div>
                        </div>
                        <br />
                        {{ Form::close() }}


                        <table class="table table-bordered table-striped" id="dataTable_exclude" width="100%" cellspacing="0">
                            <thead>
                                @if (session()->has('message-success-delete') != '' || session()->get('message-danger-delete') != '')
                                    <tr>
                                        <td colspan="6">
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
                                    <th width="120px">Product Code</th>
                                    <th width="170px">Part Number</th>
                                    <th>Description</th>
                                    <th width="120px">Brand</th>
                                    <th width="250px">Category & Subcategory</th>
                                    <th width="50px">Status</th>
                                    <th width="150px">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @if (isset($items))
                                    @foreach ($items as $value)
                                        <tr>
                                            <td>{{ @$value->item_code }}</td>
                                            <td>{{ @$value->part_number }}</td>
                                            <td>{{ @$value->description }}</td>
                                            <td>{{ App\SmItem::getBrandName(@$value->brand) }}</td>
                                            <td>{{ @$value->category != '' ? @$value->category->category_name : '' }} ->
                                                {{ App\SmItem::getSubcategoryName(@$value->subcategory_name) }}</td>
                                            <td>@if(@$value->status==1)
                                                    <span class="badge-success pl-1 pr-1" >Active</span>
                                                @else
                                                    <span class="badge-danger pl-1 pr-1" >Deleted</span>
                                                @endif
                                            </td>
                                            <input type="hidden" id="item_code_{{ @$value->id }}" value="{{ @$value->item_code }}">
                                            <input type="hidden" id="part_number_{{ @$value->id }}" value="{{ @$value->part_number }}">
                                            <input type="hidden" id="description_{{ @$value->id }}" value="{{ @$value->description }}">
                                            <input type="hidden" id="category_name_{{ @$value->id }}" value="{{ @$value->category->category_name }}">
                                            <input type="hidden" id="subcategory_name_{{ @$value->id }}" value="{{ App\SmItem::getSubcategoryName(@$value->subcategory_name) }}">
                                            <input type="hidden" id="brand_{{ @$value->id }}" value="{{ App\SmItem::getBrandName(@$value->brand) }}">
                                            <input type="hidden" id="product_type_{{ @$value->id }}" value="{{ App\SmItem::getProductType(@$value->product_type) }}">
                                            <input type="hidden" id="vat_{{ @$value->id }}" value="{{ @$value->vat }}">
                                            <input type="hidden" id="uom_{{ @$value->id }}" value="{{ @$value->uom }}">
                                            <input type="hidden" id="coo_{{ @$value->id }}" value="{{ @$value->coo }}">
                                            <input type="hidden" id="hscode_{{ @$value->id }}" value="{{ @$value->hscode }}">
                                            <input type="hidden" id="weight_{{ @$value->id }}" value="{{ @$value->weight }}">
                                            @php $cb = $user_list->where('user_id',$value->created_by)->max('full_name'); @endphp
                                            <input type="hidden" id="created_by_{{ @$value->id }}" value="{{ @$cb }}">
                                            <input type="hidden" id="created_at_{{ @$value->id }}" value="{{ date('d/m/Y', strtotime(@$value->created_at)) }}">

                                            {{-- <td>
                                                @php
                                                $product_in_stock = App\SmItem::getProductNo(@$value->id);
                                                echo $product_in_stock; 
                                                @endphp
                                            </td> --}}

                                            <td>                                                
                                                <a class="btn-sm btn-primary" href="{{ url('item-add/' . @$value->id . '/edit') }}">@lang('lang.edit')</a>
                                                <a data-modal-size="modal-md" data-target="#add_to_do" data-toggle="modal" class="btn-sm btn-success" href="#" onclick="fn_data({{ @$value->id }})">@lang('View')</a>
                                                @if (in_array(156, @$module_links) || Auth::user()->role_id == 1)
                                                    <a class="btn-sm btn-danger" data-modal-size="modal-md" title="Delete Item" href="{{ url('delete-item-view/' . @$value->id) }}" onclick="return confirm('Are you sure?')">@lang('lang.delete')</a>
                                                @endif
                                            </td>
                                    @endforeach
                                @endif
                            </tbody>
                            <?php try{ ?>
                            <footer>
                                <tr>
                                    <td colspan="9">
                                        @if (count($items)>0)
                                        {{ $items->appends(request()->input())->links() }}
                                        @endif
                                    </td>
                                </tr>
                            </footer>
                            <?php }catch (\Exception $e) { } ?>
                        </table>
                </div>


            </div>
        </div>
    </div>

    @if (2 == 1)
    <div>
    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'item-remove-duplicate', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
    <div class="row mb-5">
        <div class="col-lg-4">
    <input class="form-control" type="text" name="item_partno" autocomplete="off">
        </div>
    <div class="col-lg-4">
    <button type="submit" class="btn btn-danger" >Remove</button>
    </div>
    </div>
    {{ Form::close() }}
    </div>
    @endif

</div>
<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>



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

    <div class="modal fade admin-query" id="add_to_do">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header m-0 p-3">
                    <h4 class="modal-title">
                        Product Detail
                    </h4>
                    <button class="close" data-dismiss="modal" type="button">
                        ×
                    </button>
                </div>
                <div class="modal-body pl-3 pr-3">
                    <style>
                        .modal-body b{color: #000000; font-weight: 100;}
                        label:not(.form-check-label):not(.custom-file-label){font-weight: 100;}
                    </style>                    
                    <div class="row">
                        <div class="col-lg-12">
                            <b>Item Code</b> : <b><label id="lbl_item_code"></label></b>
                            <hr class="m-0">
                            <b>Part Number</b> : <b><label id="lbl_part_number"></b>
                            <hr class="m-0">
                            <b>Description</b> : <b><label id="lbl_description"></b>
                            <hr class="m-0">
                            <b>Category Name</b> : <b><label id="lbl_category_name"></b>
                            <hr class="m-0">
                            <b>Subcategory Name</b> : <b><label id="lbl_subcategory_name"></b>
                            <hr class="m-0">
                            <b>Brand</b> : <b><label id="lbl_brand"></b>
                            <hr class="m-0">
                            <b>Product Type</b> : <b><label id="lbl_product_type"></b>
                            <hr class="m-0">
                            <b>VAT</b> : <b><label id="lbl_vat"></b>
                            <hr class="m-0">
                            <b>UOM</b> : <b><label id="lbl_uom"></b>
                            <hr class="m-0">
                            <b>COO</b> : <b><label id="lbl_coo"></b>
                            <hr class="m-0">
                            <b>HS Code</b> : <b><label id="lbl_hscode"></b>
                            <hr class="m-0">
                            <b>Weight</b> : <b><label id="lbl_weight"></b>
                            <hr class="m-0">
                            <b>Created By</b> : <b><label id="lbl_created_by"></b>
                            <hr class="m-0">
                            <b>Created Date</b> : <b><label id="lbl_created_at"></b>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-right">
                            <div class="mt-40 ">
                                <button class="btn btn-warning" data-dismiss="modal" type="button">
                                    @lang('Close')
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ModalMergeProduct" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="staticBackdropLabel">Merge Product</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            
            {!! Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'product-merge', 'method' => 'post', 'id' => 'product-merge']) !!}
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">From Part Number
                        <select id="from_partno" name="from_partno[]" class="form-control js-example-basic-single" multiple required>
                            @foreach ($item_list as $data)
                                <option value="{{ $data->id }}">{{ $data->part_number }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">To Part Number
                        <select id="to_partno" name="to_partno" class="form-control js-example-basic-single" required>
                            <option value="">Select</option>
                            @foreach ($item_list as $data)
                                <option value="{{ $data->id }}">{{ $data->part_number }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary" onclick="return confirm('Are you sure you want to Merge this?');">Merge</button>
            </div>
            {!! Form::close() !!}
    
          </div>
        </div>
      </div>

      <div class="modal fade" id="ModalMergeDuplicateProduct" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="staticBackdropLabel">Merge Duplicate Product</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            
            {!! Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'product-merge-duplicate', 'method' => 'post', 'id' => 'product-merge-duplicate']) !!}
            <div class="modal-body">
                <div class="row">

                    <div class="col-md-12">Duplicate Part Number

                        <div class="form-control" style="height: 300px; overflow-y: scroll; overflow-x: hidden;">
                            @foreach ($dup_item_list as $data)
                            <input type="checkbox" id="dup_part_no" name="dup_part_no[]" value="{{ $data }}" checked> <label for="vehicle1"> {{ $data }}</label><br>
                            @endforeach
                        </div>
                    {{-- </div>
                    <div class="col-md-6">To Part Number
                        <select id="to_partno" name="to_partno" class="form-control js-example-basic-single" required>
                            <option value="">Select</option>
                            @foreach ($item_list as $data)
                                <option value="{{ $data->id }}">{{ $data->part_number }}</option>
                            @endforeach
                        </select>
                    </div> --}}
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary" onclick="return confirm('Are you sure you want to Merge this?');">Merge</button>
            </div>
            {!! Form::close() !!}
    
          </div>
        </div>
      </div>

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

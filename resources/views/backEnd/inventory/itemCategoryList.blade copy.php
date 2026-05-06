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
            <h2 class="page-heading m-0">Category</h2>
            <span class="page-label">Home - Category</span>
        </div>
        <div>            
            <a href="{{url('brand')}}" class="btn btn-info"><i class="far fa fa-plus" aria-hidden="true"></i> Brand</a>
            <a href="{{url('item-category')}}" class="btn btn-info"><i class="far fa fa-plus" aria-hidden="true"></i> Category</a>
            <a href="{{url('create-sub-category')}}" class="btn btn-info"><i class="far fa fa-plus" aria-hidden="true"></i> Sub Category</a>
            <a href="{{url('item-add')}}" class="btn btn-info"><i class="far fa fa-plus" aria-hidden="true"></i> Product</a>
            <a href="javascript:location.reload();" class="btn btn-info"><i class="far fa fa-refresh" aria-hidden="true"></i> Refresh</a>
        </div>
    </div>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-4">
                    @if(isset($editData))
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'item-category/'.@$editData->id, 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}
                    @else
                    @if(in_array(150, @$module_links) ||  Auth::user()->role_id == 1)
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'item-category',
                    'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                    @endif
                    @endif
                    <div class="white-box">
                        <div class="add-visitor">
                            <div class="row">
                                @if(session()->has('message-success'))
                                <div class="alert alert-success mb-20">
                                    {{ session()->get('message-success') }}
                                </div>
                                @elseif(session()->has('message-danger'))
                                <div class="alert alert-danger">
                                    {{ session()->get('message-danger') }}
                                </div>
                                @endif

                                <div class="col-lg-12 mb-20">
                                    <div class="input-effect">
                                        <label class="txtlbl">@lang('lang.category') @lang('lang.name') <span>*</span> </label>
                                        <input class="primary-input form-control{{ $errors->has('category_name') ? ' is-invalid' : '' }}"
                                        type="text" name="category_name" autocomplete="off" value="{{isset($editData)? @$editData->category_name : '' }}">
                                        <span class="focus-border"></span>
                                        @if ($errors->has('category_name'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('category_name') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">

                            </div>

                            @php 
                              $tooltip = "";
                               if(in_array(150, @$module_links) ||  Auth::user()->role_id == 1){
                                    $tooltip = "";
                                }else{
                                    $tooltip = "You have no permission to add";
                                }
                            @endphp
                            <div class="row mt-1">
                                <div class="col-lg-12">
                                    <button class="btn btn-info" data-toggle="tooltip" title="{{@$tooltip}}" id="btnSubmit">
                                        <span class="ti-check"></span>
                                        @if(isset($editData))
                                            Update Category
                                        @else
                                            Save Category
                                        @endif
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
                <div class="col-lg-8">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
        
                            <thead>
                                @if(session()->has('message-success-delete') != "" ||
                                        session()->get('message-danger-delete') != "")
                                        <tr>
                                            <td colspan="2">
                                                 @if(session()->has('message-success-delete'))
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
                                <tr >
                                    <th>Category</th>
                                    <th>View Subcategory</th>
                                    <th>Created By</th>
                                    <th>Company</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
        
                            <tbody>
                                @if(isset($itemCategories))
                                @foreach($itemCategories as $value)
                                <tr>
                                    <td>{{@$value->category_name}}</td>
                                    <td>
                                        <a class="btn-sm btn-warning"  href="{{url('create-sub-category/'.@$value->id)}}"> @lang('lang.view') @lang('lang.subcategory')</a>
                                    </td>
                                    <td>{{ @$value->createdby->full_name }}</td>
                                    <td>{{ @$value->companyid->company_name }}</td>
                                    <td>
                                        @if(in_array(151, @$module_links) ||  Auth::user()->role_id == 1)
        
                                                <a class="btn-sm btn-info" href="{{url('item-category/'.@$value->id.'/edit')}}"> @lang('lang.edit')</a>
                                            @endif  
                                            @if(in_array(152, @$module_links) ||  Auth::user()->role_id == 1)        
                                                <a class="btn-sm btn-danger" data-modal-size="modal-md" title="Delete Item Category" href="{{url('delete-item-category-view/'.@$value->id)}}" onclick="return confirm('Are you sure?')"> @lang('lang.delete')</a>
                                            @endif  
                                    </td>
                                </tr>
        
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>    

</div>
<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>



<section class="admin-visitor-area up_table_btn">
    <div class="container-fluid p-0">
        @if(in_array(150, @$module_links) ||  Auth::user()->role_id == 1)
       @if(isset($editData))
        <div class="row">
            <div class="offset-lg-10 col-lg-2 text-right col-md-12 mb-20">
                <a href="{{url('item-category')}}" class="primary-btn small fix-gr-bg">
                    <span class="ti-plus pr-2"></span>
                    @lang('lang.add')
                </a>
            </div>
        </div>
        @endif
        @endif
        <div class="row">
            <div class="col-lg-3">
                
            </div>

            <div class="col-lg-9">
              

        <div class="row">

            
        </div>
    </div>
</div>
</div>
</section>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            $("#btnSubmit").click(function () {
                setTimeout(function () { disableButton(); }, 0);
            });
            function disableButton() {
                $("#btnSubmit").prop('disabled', true);
            }
        });
    </script>
@endsection
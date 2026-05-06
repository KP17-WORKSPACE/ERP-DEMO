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
            <h2 class="page-heading m-0">Sub Category</h2>
            <span class="page-label">Home - Sub Category</span>
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
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'update-item-sub-category', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                    <input type="hidden" name="id" value="{{@$editData->id}}">
                    @else
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'store-item-sub-category',
                    'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
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

                                <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">

                            </div>

                            <div class="row mt-40">
                                <div class="col-lg-12 mb-20">
                                    <label class="txtlbl">Select Category <span>*</span> </label>
                                    <select class="w-100 bb niceSelect form-control {{ $errors->has('category') ? ' is-invalid' : '' }}" id="category" name="category">
                                        <option value=""></option>
                                        @foreach($itemCategories as $row) 
                                         <option value="{{@$row->id}}">{{@$row->category_name}}</option> 
                                        @endforeach
                                    </select>
                                    @if ($errors->has('category'))
                                    <span class="invalid-feedback invalid-select" role="alert">
                                        <strong>{{ $errors->first('category') }}</strong>
                                    </span>
                                    @endif

                                </div>
                            </div>
                            
                            <div class="row mt-40">
                                <div class="col-lg-12 mb-20">
                                    <div class="input-effect">
                                        <label class="txtlbl">@lang('lang.sub') @lang('lang.category') @lang('lang.name') <span>*</span> </label>
                                        <input class="primary-input form-control{{ $errors->has('sub_category_name') ? ' is-invalid' : '' }}"
                                        type="text" name="sub_category_name" autocomplete="off" value="{{isset($editData)? @$editData->sub_category_name : '' }}">
                                        
                                        <span class="focus-border"></span>
                                        @if ($errors->has('sub_category_name'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('sub_category_name') }}</strong>
                                        </span>
                                        @endif
                                    </div>

                                </div>
                            </div>

                            <div class="row mt-1">
                                <div class="col-lg-12">
                                    <button class="btn btn-info" id="btnSubmit">
                                        <span class="ti-check"></span>
                                        @if(isset($editData))
                                            Update Sub Category
                                        @else
                                            Save Sub Category
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
                                <tr>
                                    <th>Subcategory Name</th>
                                    <th>Created By</th>
                                    <th>Company</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
        
                            <tbody>
                                @if(isset($subCategories))
                                @foreach($subCategories as $value)
                                <tr>
        
                                    <td>{{@$value->sub_category_name}}</td>
                                    <td>{{ @$value->createdby->full_name }}</td>
                                    <td>{{ @$value->companyid->company_name }}</td>
                                    <td>
                                        @if (in_array(163, @$module_links) || Auth::user()->role_id == 1)
                                        <a class="btn-sm btn-info" href="{{url('edit-sub-category/'.$value->id)}}"> @lang('lang.edit')</a>
                                        <a class="btn-sm btn-danger" data-modal-size="modal-md" title="Delete Sub Category" href="{{url('delete-sub-category-view/'.@$value->id)}}" onclick="return confirm('Are you sure?')"> @lang('lang.delete')</a>
                                        @endif
                                            </div>
                                        </div>
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



<section class="admin-visitor-area">
    <div class="container-fluid p-0">
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
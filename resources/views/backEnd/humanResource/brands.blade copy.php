@extends('backEnd.masterpage')
@section('mainContent')

@php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
@endphp

<div class="container-fluid">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h2 class="page-heading m-0">Brand</h2>
            <span class="page-label">Home - Brand</span>
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
                @if(isset($editmode))
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'brand/'.@$editmode->id, 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}
                        @else
                        @if(in_array(105, @$module_links) ||  Auth::user()->role_id == 1)
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'brand',
                        'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                        @endif
                        @endif
                        <div class="white-box">
                            <div class="add-visitor">
                                <div class="row">
                                    <div class="col-lg-12">
                                        @if(session()->has('message-success'))
                                        <div class="alert alert-success">
                                            {{ session()->get('message-success') }}
                                        </div>
                                        @elseif(session()->has('message-danger'))
                                        <div class="alert alert-danger">
                                            {{ session()->get('message-danger') }}
                                        </div>
                                        @endif
                                        <div class="input-effect">
                                            <label class="txtlbl">@lang('Brand') @lang('Name') <span>*</span></label>
                                            <input class="primary-input form-control{{ $errors->has('title') ? ' is-invalid' : '' }}"
                                                type="text" name="title" autocomplete="off" value="{{isset($editmode)? @$editmode->title:''}}">
                                            <input type="hidden" name="id" value="{{isset($editmode)? $editmode->id: ''}}">
                                            <span class="focus-border"></span>
                                            @if ($errors->has('title'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('title') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @php 
                                  $tooltip = "";
                                   if(in_array(105, $module_links) ||  Auth::user()->role_id == 1){
                                        $tooltip = "";
                                    }else{
                                        $tooltip = "You have no permission to add";
                                    }
                                @endphp
                                <div class="row mt-1">
                                    <div class="col-lg-12">
                                        <button class="btn btn-info" data-toggle="tooltip" title="{{@$tooltip}}" id="btnSubmit">
                                            <span class="ti-check"></span>
                                            {{isset($editmode)? 'Update':'Save'}} @lang('Brand')
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
                                <th>Brands</th>
                                <th>Created By</th>
                                <th>Company</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($brands as $brand)
                            <tr>
                                <td>{{ @$brand->title }}</td>
                                <td>{{ @$brand->createdby->full_name }}</td>
                                <td>{{ @$brand->companyid->company_name }}</td>
                                <td>
                                    @if(in_array(106, @$module_links) ||  Auth::user()->role_id == 1)
                                        <a class="btn-sm btn-info" href="{{url('brand', [@$brand->id])}}">@lang('lang.edit')</a>
                                    @endif
                                    @if(in_array(107, @$module_links) ||  Auth::user()->role_id == 1)
                                        <a class="btn-sm btn-danger" data-toggle="modal" data-target="#deleteDesignationModal{{@$brand->id}}" href="#" onclick="return confirm('Are you sure?')">@lang('lang.delete')</a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                </div>

            </div>
        </div>
    </div>    

</div>


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
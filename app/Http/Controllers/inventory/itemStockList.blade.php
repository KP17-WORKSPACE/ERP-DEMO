@extends('backEnd.master')
@section('mainContent')
@php
$modules = [];
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();

    foreach($permissions as $permission){ @$module_links[] = @$permission->module_link_id; @$modules[] = @$permission->moduleLink->module_id;}
 
    $modules = array_unique(@$modules);
@endphp
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('Product Stock List')</h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                <a href="#">@lang('lang.inventory')</a>
                <a href="#">@lang('Product Stock List')</a>
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area">
    <div class="container-fluid p-0">
        @if(in_array(158, @$module_links) ||  Auth::user()->role_id == 1)
        @if(isset($editData))
        <div class="row">
            <div class="offset-lg-10 col-lg-2 text-right col-md-12 mb-20">
                <a href="{{url('item-store')}}" class="primary-btn small fix-gr-bg">
                <a href="{{url('item-category')}}" class="primary-btn small fix-gr-bg">
                    <span class="ti-plus pr-2"></span>
                    @lang('lang.add')
                </a>
            </div>
        </div>
        @endif
        @endif
       <div class="row">
            {{-- <div class="col-lg-3">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-title">
                            <h3 class="mb-30">@if(isset($editData))
                                    @lang('lang.edit')
                                @else
                                    @lang('lang.add')
                                @endif
                                @lang('lang.item_store')
                            </h3>
                        </div>
                        @if(isset($editData))
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'item-store/'.@$editData->id, 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}
                        @else
                        @if(in_array(158, @$module_links) ||  Auth::user()->role_id == 1)
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'item-store',
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
                                            <input class="primary-input form-control{{ $errors->has('store_name') ? ' is-invalid' : '' }}"
                                            type="text" name="store_name" autocomplete="off" value="{{isset($editData)? @$editData->store_name : '' }}">
                                            <label> @lang('lang.store_name') <span>*</span> </label>
                                            <span class="focus-border"></span>
                                            @if ($errors->has('store_name'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('store_name') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>

                                     <div class="col-lg-12 mb-20">
                                        <div class="input-effect">
                                            <input class="primary-input form-control{{ $errors->has('store_no') ? ' is-invalid' : '' }}"
                                            type="number" name="store_no" autocomplete="off" value="{{isset($editData)? @$editData->store_no : '' }}">
                                            <label> @lang('lang.number') </label>
                                            <span class="focus-border"></span>
                                            @if ($errors->has('store_no'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('store_no') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>

                                     <div class="col-lg-12 mb-20">
                                <div class="input-effect">
                                    <textarea class="primary-input form-control" cols="0" rows="4" name="description" id="description">{{isset($editData) ? @$editData->description : ''}}</textarea>
                                    <label> @lang('lang.description') <span></span> </label>
                                    <span class="focus-border textarea"></span>

                                </div>
                            </div>

                            @php 
                                  $tooltip = "";
                                   if(in_array(158, @$module_links) ||  Auth::user()->role_id == 1){
                                        $tooltip = "";
                                    }else{
                                        $tooltip = "You have no permission to add";
                                    }
                                @endphp

                                </div>
                                <div class="row mt-40">
                                    <div class="col-lg-12 text-center">
                                        <button class="primary-btn fix-gr-bg" data-toggle="tooltip" title="{{@$tooltip}}">
                                            <span class="ti-check"></span>
                                            @if(isset($editData))
                                                @lang('lang.update')
                                            @else
                                                @lang('lang.save')
                                            @endif
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div> --}}

            <div class="col-lg-12">
             
          <div class="row">
            <div class="col-lg-4 no-gutters">
                <div class="main-title">
                    <h3 class="mb-0">@lang('Product Stock List')</h3>
                </div>
            </div>
        </div>

        <div class="row">

            <div class="col-lg-12">
                <table id="table_id" class="display school-table" cellspacing="0" width="100%">

                    <thead>
                        @if(session()->has('message-success-delete') != "" ||
                                session()->get('message-danger-delete') != "")
                                <tr>
                                    <td colspan="4">
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
                            <th>@lang('Partno')</th>
                            <th>@lang('SlNo')</th>
                            <th>@lang('In Stock')</th>
                            <th>@lang('Out Stock')</th>
                        </tr>
                    </thead>

                    <tbody>
                        @if(isset($itemstores))
                        @foreach($itemstores as $value)
                        <tr>
                            <td>{{ @$value->part_no->part_number}}</td>
                            <td>{{@$value->slno}}</td>
                            <td>@if($value->status==1) <i class="fa fa-check text-success" aria-hidden="true"></i> [ {{date('d-m-Y', strtotime(@$value->created_at))}} ] @endif</td>
                            <td>@if($value->status==2) <i class="fa fa-times text-danger" aria-hidden="true"></i> [ {{date('d-m-Y', strtotime(@$value->created_at))}} ] @endif</td>
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
</section>
@endsection

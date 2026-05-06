@extends('backEnd.master')
@section('mainContent')
@php
    $modules = [];
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    foreach($permissions as $permission){ $module_links[] = $permission->module_link_id; $modules[] = $permission->moduleLink->module_id;}
    $modules = array_unique($modules);
    $generalSetting=App\SmGeneralSettings::where('id',1)->first();
    if(isset($generalSetting->logo)){  $logo = $generalSetting->logo;  } else{ $logo = 'public/uploads/settings/logo.png'; } 
@endphp
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('lang.brand_manage')</h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                <a href="#">@lang('lang.accounts')</a>
                <a href="#">@lang('lang.brand_manage')</a>
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area">
    <div class="container-fluid p-0"> 
        @if(isset($brand_manage))
        <div class="row">
            <div class="offset-lg-10 col-lg-2 text-right col-md-12 mb-20">
                <a href="{{url('chart-of-account')}}" class="primary-btn small fix-gr-bg">
                    <span class="ti-plus pr-2"></span>
                    @lang('lang.add')
                </a>
            </div>
        </div>
        @endif 
        <div class="row">
            <div class="col-lg-3">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-title">
                            <h3 class="mb-30">@if(isset($brand_manage))
                                    @lang('lang.edit')
                                @else
                                    @lang('lang.add')
                                @endif
                                @lang('lang.brand_manage')
                            </h3>
                        </div>
                        @if(isset($brand_manage))
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true,  'url' => 'manage-brand-modified', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                        <input type="hidden" name="unit_id" value="{{isset($brand_manage)? $brand_manage->id: ''}}">
                        @else
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'manage-brand-modified', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                        @endif
                        <div class="white-box">
                            <div class="add-visitor">

                                <div class="row">
                                    <div class="col-lg-12">
                                        @if(session()->has('message-success'))
                                            <div class="alert alert-success"> {{ session()->get('message-success') }} </div>
                                        @elseif(session()->has('message-danger'))
                                            <div class="alert alert-danger"> {{ session()->get('message-danger') }} </div>
                                        @endif
                                    </div>


                                    <div class="col-lg-12 mt-40">
                                        <div class="input-effect">
                                            <input class="primary-input form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                                                type="text" name="name" autocomplete="off" value="{{isset($brand_manage)? $brand_manage->name:''}}">
                                            <label>@lang('lang.name') <span>*</span></label>
                                            <span class="focus-border"></span>
                                            @if ($errors->has('name'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('name') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-12 mt-40">
                                        <div class="input-effect">

                                         <select class="niceSelect w-100 bb form-control" name="parent_id">
                                            <option data-display="@lang('lang.select') @lang('lang.parent') @lang('lang.brand')" value="0"> @lang('lang.parent') @lang('lang.brand')</option>
                                            @if(isset($data))
                                                @foreach($data as $row)
                                                    <option  value="{{$row->id}}" {{isset($brand_manage)? ($brand_manage->id ==  $row->id? 'selected':''):'selected'}}> {{$row->name}}</option>
                                                @endforeach
                                            @endif

                                        </select> 
                                            @if ($errors->has('short_form'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('short_form') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-12 mt-40">
                                        <div class="input-effect">

                                            <input class="primary-input form-control{{ $errors->has('description') ? ' is-invalid' : '' }}"
                                                type="text" name="description" autocomplete="off" value="{{isset($brand_manage)? $brand_manage->description:''}}"> 
                                            <label>@lang('lang.description')</label>
                                            <span class="focus-border"></span>
                                            @if ($errors->has('description'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('description') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>


                                    <div class="col-lg-12 mt-40">
                                        <div class="input-effect">

                                         <select class="niceSelect w-100 bb form-control" name="active_status">
                                            <option data-display="@lang('lang.select_status')" value="">@lang('lang.Status')</option>
                                            <option  value="1" {{isset($status_id)? ($status_id ==  '1'? 'selected':''):'selected'}}>@lang('lang.active')</option>
                                            <option  value="2" {{isset($status_id)? ($status_id == '2'? 'selected':''):''}}>@lang('lang.inactive')</option>
                                        </select> 
                                            @if ($errors->has('active_status'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('active_status') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>


                                </div> 



                                <div class="row mt-40">
                                    <div class="col-lg-12 text-center">
                                        <button class="primary-btn fix-gr-bg">
                                            <span class="ti-check"></span>
                                            @if(isset($brand_manage))
                                                @lang('lang.update')
                                            @else
                                                @lang('lang.save')
                                            @endif
                                            @lang('lang.brand')
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="row">
                    <div class="col-lg-4 no-gutters">
                        <div class="main-title">
                            <h3 class="mb-0">@lang('lang.brand_manage') @lang('lang.list')</h3>
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
                                    <td colspan="3">
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
                                    <th>@lang('lang.sl')</th>
                                    <th>@lang('lang.name')</th>
                                    <th>@lang('lang.short_form')</th>
                                    <th>@lang('lang.description')</th>
                                    <th>@lang('lang.status')</th>
                                    <th>@lang('lang.action')</th>
                                    <th>@lang('lang.author_details')</th> 
                                </tr>
                            </thead>

 

                            <tbody>
                                @php $count=1; @endphp
                                @foreach($data as $row)
                                <tr>
                                    <td>{{$count++}}</td>
                                    <td>{{$row->name}}</td>
                                    <td>{{$row->short_form}}</td>
                                    <td>{{$row->description}}</td>
                                    <td>
                                        @if($row->active_status==1)
                                        <button class="btn primary-btn small fix-gr-bg">Published</button>
                                        @else
                                        <button class="primary-btn small tr-bg">Unpublished</button>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">
                                                @lang('lang.select')
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right"> 
                                                <a class="dropdown-item" href="{{url('manage-brand-edit', [$row->id])}}">@lang('lang.edit')</a> 
                                                <a class="dropdown-item" data-toggle="modal" data-target="#deleteBrand{{$row->id}}"
                                                    href="#">@lang('lang.delete')</a> 
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        Created: {{App\User::getUserDetails($row->created_by)}} At {{date('d F Y, h:i:s A',strtotime($row->created_at))}}
                                    @if(!empty($row->updated_by)) <br> 
                                    Last Updated: {{App\User::getUserDetails($row->updated_by)}} At {{date('d F Y, h:i:s A',strtotime($row->updated_at))}} @endif
                                    </td>
                                </tr>

                                </tr>
                                <div class="modal fade admin-query" id="deleteBrand{{$row->id}}" >
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title">@lang('lang.delete') @lang('lang.brand_manage')</h4>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="text-center">
                                                    <h4>@lang('lang.are_you_sure_to_delete')</h4>
                                                </div>

                                                <div class="mt-40 d-flex justify-content-between">
                                                    <button type="button" class="primary-btn tr-bg" data-dismiss="modal">@lang('lang.cancel')</button>
                                                     {{ Form::open(['url' => 'manage-brand-delete/'.$row->id, 'method' => 'GET', 'enctype' => 'multipart/form-data']) }}
                                                    <button class="primary-btn fix-gr-bg" type="submit">@lang('lang.delete')</button>
                                                     {{ Form::close() }}
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

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
            <h1>@lang('lang.accounts_name')</h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                <a href="#">@lang('lang.accounts')</a>
                <a href="#">@lang('lang.accounts_name')</a>
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area">
    <div class="container-fluid p-0">
        @if(isset($singleData))
        @if(in_array(238, @$module_links) || Auth::user()->role_id == 1)
        <div class="row">
            <div class="offset-lg-10 col-lg-2 text-right col-md-12 mb-20">
                <a href="{{url('sub-account')}}" class="primary-btn small fix-gr-bg">
                    <span class="ti-plus pr-2"></span>
                    @lang('lang.add')
                </a>
            </div>
        </div>
        @endif
        @endif
        <div class="row">
            <div class="col-lg-3">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-title">
                            <h3 class="mb-30">@if(isset($singleData))
                                    @lang('lang.edit')
                                @else
                                    @lang('lang.add')
                                @endif
                                @lang('lang.accounts_name')
                            </h3>
                        </div>
                        @if(isset($singleData))
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true,  'url' => 'sub-account-update', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                        @else
                        @if(in_array(238, @$module_links) || Auth::user()->role_id == 1)
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'sub-account-store', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
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
                                            <input class="primary-input form-control{{ $errors->has('sub_head') ? ' is-invalid' : '' }}"
                                                type="text" name="sub_head" autocomplete="off" value="{{isset($singleData)? @$singleData->sub_head:''}}">

                                            <input type="hidden" name="id" value="{{isset($singleData)? @$singleData->id: ''}}">
                                            <label>@lang('lang.name') <span>*</span></label>
                                            <span class="focus-border"></span>
                                            @if ($errors->has('sub_head'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('sub_head') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>


                                <div class="row mt-40">
                                    <div class="col-lg-12">
                                        <select class="w-100 bb niceSelect form-control{{ $errors->has('head_id') ? ' is-invalid' : '' }}" name="head_id">
                                            <option data-display="@lang('lang.select') @lang('lang.account_of_head')*" value="">@lang('lang.account_of_head')*</option>
                                            @foreach($SmChartOfAccount as $row)
                                                <option value="{{@$row->id}}" {{isset($singleData)? (@$singleData->head_id == @$row->id? 'selected':''):''}}>  {{@$row->head}}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('head_id'))
                                        <span class="invalid-feedback invalid-select" role="alert">
                                            <strong>{{ $errors->first('head_id') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mt-40">
                                    <div class="col-lg-12">
                                        <div class="input-effect">
                                            <textarea class="primary-input form-control{{ $errors->has('description') ? ' is-invalid' : '' }}" cols="0" rows="4" name="description">{{isset($singleData)? @$singleData->description: old('description')}}</textarea>
                                            <label>@lang('lang.description')  </label>
                                            <span class="focus-border textarea"></span>
                                        </div>
                                        @if($errors->has('description'))
                                            <span class="error text-danger"><strong class="validate-textarea">{{ $errors->first('description') }}</strong></span>
                                        @endif
                                    </div>
                                </div>


                                @php 
                                  $tooltip = "";
                                   if(in_array(238, @$module_links) ||  Auth::user()->role_id == 1){
                                        $tooltip = "";
                                    }else{
                                        $tooltip = "You have no permission to add";
                                    }
                                @endphp



                                <div class="row mt-40">
                                    <div class="col-lg-12 text-center">
                                        <button class="primary-btn fix-gr-bg" data-toggle="tooltip" title="{{@$tooltip}}">
                                            <span class="ti-check"></span>
                                            @if(isset($singleData))
                                                @lang('lang.update')
                                            @else
                                                @if(in_array(238, @$module_links) || Auth::user()->role_id == 1)
                                                    @lang('lang.save')
                                                @endif
                                            @endif
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
                            <h3 class="mb-0 tab_m_30">@lang('lang.accounts_name') @lang('lang.list')</h3>
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
                                    <th>@lang('lang.account_of_head')</th>
                                    <th>@lang('lang.name')</th>
                                    <th>@lang('lang.description')</th>  
                                    <th>@lang('lang.action')</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($costCenters as $row)
                                    <tr>
                                        <td>{{App\SmChartOfAccount::getHeadChartOfAccount(@$row->head_id)}}</td>
                                        <td>{{@$row->sub_head}}</td>
                                        <td>{{@$row->description}}</td>  
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">
                                                    @lang('lang.select')
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                @if(in_array(239, @$module_links) || Auth::user()->role_id == 1)
                                                    <a class="dropdown-item" href="{{url('sub-account-edit', [@$row->id])}}">@lang('lang.edit')</a>
                                                @endif
                                                @if(in_array(240, @$module_links) || Auth::user()->role_id == 1)
                                                    <a class="dropdown-item" data-toggle="modal" data-target="#deleteChartOfAccountModal{{@$row->id}}"
                                                        href="#">@lang('lang.delete')</a>
                                                @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <div class="modal fade admin-query" id="deleteChartOfAccountModal{{@$row->id}}" >
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title">@lang('lang.delete') @lang('lang.account')</h4>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>

                                                <div class="modal-body">
                                                    <div class="text-center">
                                                        <h4>@lang('lang.are_you_sure_to_delete')</h4>
                                                    </div>

                                                    <div class="mt-40 d-flex justify-content-between">
                                                        <button type="button" class="primary-btn tr-bg" data-dismiss="modal">@lang('lang.cancel')</button>
                                                        <a href="{{url('sub-account-delete', [@$row->id])}}" class="text-light">
                                                        <button class="primary-btn fix-gr-bg" type="submit">@lang('lang.delete')</button>
                                                     </a>
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

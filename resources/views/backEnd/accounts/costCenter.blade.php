@extends('backEnd.master')
@section('mainContent')
@php
    function showPicName($data){
        $name = explode('/', $data);
        return $name[3];
    }

    @$modules = [];
    @$module_links = [];
    @$permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();


    foreach($permissions as $permission){ @$module_links[] = @$permission->module_link_id; @$modules[] = @$permission->moduleLink->module_id;}

    @$modules = array_unique(@$modules);

    @$generalSetting=App\SmGeneralSettings::where('id',1)->first();
    if(isset($generalSetting->logo)){  @$logo = @$generalSetting->logo;  }
    else{ $logo = 'public/uploads/settings/logo.png'; }

@endphp
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('lang.cost_center')</h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                <a href="#">@lang('lang.accounts')</a>
                <a href="#">@lang('lang.cost_center')</a>
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area">
    <div class="container-fluid p-0">
        @if(in_array(60, @$module_links) || Auth::user()->role_id == 1)
        @if(isset($singleData))
        <div class="row">
            <div class="offset-lg-10 col-lg-2 text-right col-md-12 mb-20">
                <a href="{{url('chart-of-account')}}" class="primary-btn small fix-gr-bg">
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
                                @lang('lang.cost_center')
                            </h3>
                        </div>
                        @if(isset($singleData))
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true,  'url' => 'cost-center-update', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                        @else
                        @if(in_array(60, @$module_links) || Auth::user()->role_id == 1)
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'cost-center-store', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
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
                                            <input class="primary-input form-control{{ @$errors->has('name') ? ' is-invalid' : '' }}"
                                                type="text" name="name" autocomplete="off" value="{{isset($singleData)? @$singleData->name:''}}">

                                            <input type="hidden" name="id" value="{{isset($singleData)? @$singleData->id: ''}}">
                                            <label>@lang('lang.name') <span>*</span></label>
                                            <span class="focus-border"></span>
                                            @if (@$errors->has('name'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ @$errors->first('name') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>


                                <div class="row mt-40">
                                    <div class="col-lg-12">
                                        <select class="w-100 bb niceSelect form-control{{ @$errors->has('item_id') ? ' is-invalid' : '' }}" name="item_id">
                                            <option data-display="@lang('lang.select') @lang('lang.project')" value="">@lang('lang.select') @lang('lang.project')</option>
                                            @foreach($product_list as $product)
                                                <option value="{{@$product->id}}" {{isset($singleData)? (@$singleData->item_id == @$product->id? 'selected':''):''}}> {{@$product->name}}</option>
                                                {{-- <option value="{{@$product->id}}" {{isset($singleData)? (@$singleData->item_id == @$product->id? 'selected':''):''}}> {{@str_limit($product->name,30)}}</option> --}}
                                            @endforeach
                                        </select>
                                        @if ($errors->has('item_id'))
                                        <span class="invalid-feedback invalid-select" role="alert">
                                            <strong>{{ @$errors->first('item_id') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mt-40">
                                    <div class="col-lg-12">
                                        <div class="input-effect">
                                            <textarea class="primary-input form-control{{ @$errors->has('description') ? ' is-invalid' : '' }}" cols="0" rows="4" name="description">{{isset($singleData)? @$singleData->description: old('description')}}</textarea>
                                            <label>@lang('lang.description')  </label>
                                            <span class="focus-border textarea"></span>
                                        </div>
                                        @if($errors->has('description'))
                                            <span class="error text-danger"><strong class="validate-textarea">{{ @$errors->first('description') }}</strong></span>
                                        @endif
                                    </div>
                                </div>


                                @php
                                  $tooltip = "";
                                   if(in_array(60, @$module_links) ||  Auth::user()->role_id == 1){
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
            </div>

            <div class="col-lg-9">
                <div class="row">
                    <div class="col-lg-4 no-gutters">
                        <div class="main-title">
                            <h3 class="mb-0 tab_m_30">@lang('lang.cost_center') @lang('lang.list')</h3>
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
                                    <th>@lang('lang.name')</th>
                                    <th>@lang('lang.description')</th>
                                    <th>@lang('lang.project') @lang('lang.name')</th>
                                    <th>@lang('lang.action')</th>
                                </tr>
                            </thead>

                            

                            <tbody>
                                @foreach($costCenters as $row)
                                    <tr>
                                        <td>{{@$row->name}}</td>
                                        <td>{{@$row->description}}</td>
                                        <td>{!! \App\SmItem::getProjectName(@$row->item_id) !!}</td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">
                                                    @lang('lang.select')
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    @if(in_array(61, @$module_links) || Auth::user()->role_id == 1)
                                                    <a class="dropdown-item" href="{{url('cost-center-edit', [$row->id])}}">@lang('lang.edit')</a>
                                                    @endif
                                                    @if(in_array(62, @$module_links) || Auth::user()->role_id == 1)
                                                    <a class="dropdown-item" data-toggle="modal" data-target="#deleteChartOfAccountModal{{$row->id}}"
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
                                                    <h4 class="modal-title">@lang('lang.delete') @lang('lang.cost_center')</h4>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>

                                                <div class="modal-body">
                                                    <div class="text-center">
                                                        <h4>@lang('lang.are_you_sure_to_delete')</h4>
                                                    </div>

                                                    <div class="mt-40 d-flex justify-content-between">
                                                        <button type="button" class="primary-btn tr-bg" data-dismiss="modal">@lang('lang.cancel')</button>
                                                        <a href="{{url('cost-center-delete', [@$row->id])}}" class="text-light">
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

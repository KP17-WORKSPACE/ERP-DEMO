@extends('backEnd.master')
@section('mainContent')
@php 
    $modules = [];
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get(); 
    foreach($permissions as $permission){
        @$module_links[] = @$permission->module_link_id;
        @$modules[] = @$permission->moduleLink->module_id;

        
        echo @$permission->module_link_id;
        echo @$permission->moduleLink->module_id;
    }
    $modules = array_unique(@$modules);
@endphp

<section class="sms-breadcrumb mb-20 white-box">
    <div class="container-fluid">
        <div class="row" style="float: left;">
            <h1>@lang('Modules') {{isset($editmode)? '-'.@$editmode->name:''}}</h1>
        </div>
        <div class="row" style="float: right;">
            <a href="{{ route('user.dashboard') }}" class="top-btn-r-l"><i class="far fa fa-home" aria-hidden="true"></i> Home</a>
            <a href="{{ url('module') }}" class="top-btn-r"><i class="far fa fa-file-text" aria-hidden="true"></i> View</a>
            <a href="javascript:location.reload();" class="top-btn-r-nobar"><i class="far fa fa-refresh" aria-hidden="true"></i> Refresh</a>
        </div>
    </div>
</section>
<hr style="margin-top: 33px;" />
<div style="clear: both;"></div>

<section class="admin-visitor-area">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-lg-3">
                <div class="row">
                    <div class="col-lg-12">
                        @if(isset($editpage))
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'module-pages/'.@$editpage->id, 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}
                        @else
                        @if(in_array(105, @$module_links) ||  Auth::user()->role_id == 1)
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'module-pages',
                        'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                        @endif
                        @endif
                        <div class="white-box">
                            <div class="add-visitor">
                                @if(session()->has('message-success'))
                                <div class="alert alert-success">
                                    {{ session()->get('message-success') }}
                                </div>
                                @elseif(session()->has('message-danger'))
                                <div class="alert alert-danger">
                                    {{ session()->get('message-danger') }}
                                </div>
                                @endif

                                <div class="row">
                                    <div class="col-lg-12">                                        
                                        <div class="input-effect">
                                            <label>@lang('Page')<span>*</span></label>
                                            <input class="primary-input dynamicstxt_s form-control{{ $errors->has('title') ? ' is-invalid' : '' }}"
                                                type="text" name="name" autocomplete="off" value="{{isset($editpage)? @$editpage->name:''}}" required>
                                            <input type="hidden" name="id" value="{{isset($editpage)? $editpage->id: ''}}">
                                            <input type="hidden" name="mid" value="{{isset($editmode)? $editmode->id: ''}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">                                        
                                        <div class="input-effect">
                                            <label>@lang('Page Name') <span>*</span></label>
                                            <input class="primary-input dynamicstxt_s form-control{{ $errors->has('page_name') ? ' is-invalid' : '' }}"
                                                type="text" name="page_name" autocomplete="off" value="{{isset($editpage)? @$editpage->page_name:''}}" required>
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
                                <div class="row mt-40">
                                    <div class="col-lg-12 text-center">
                                        <button class="primary-btn fix-gr-bg" data-toggle="tooltip" title="{{@$tooltip}}">
                                            <span class="ti-check"></span>
                                            {{isset($editmode)? 'update':'save'}} @lang('Page')
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
                <?php /*
                <div class="row">
                    <div class="col-lg-4 no-gutters">
                        <div class="main-title">
                            <h3 class="mb-0">@lang('Module') @lang('lang.list')</h3>
                            <?php $permissions = App\SmRolePermission::where('role_id', 3)->get();
                                    foreach($permissions as $permission){        
                                        echo @$permission->moduleLink->module_id;
                                        echo " - ";
                                        echo @$permission->module_link_id;
                                        echo "<br />";
                                    } ?>
                        </div>
                    </div>
                </div>
                */?>

                <div class="row">
                    <div class="col-lg-12">

                        <table id="table_id" class="display school-table" cellspacing="0" width="100%">

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
                                    <th>@lang('Page')</th>
                                    <th>@lang('Page Name')</th>
                                    {{-- <th>@lang('Created By')</th> --}}
                                    <th>@lang('lang.action')</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($pagelist as $mdle)
                                <tr>
                                    <td>{{@$mdle->name }}</td>
                                    <td>{{@$mdle->page_name }}</td>
                                    {{-- <td>{{@$mdle->created_by->createdby }}</td> --}}
                                    <td>
                                        <a class="btn btn-sm btn-warning text-dark" href="{{url('module-pages', [@$mdle->id,'edit'])}}">@lang('lang.edit')</a>
                                        {{--  <div class="dropdown">
                                            <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">
                                                @lang('lang.select')
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                @if(in_array(106, @$module_links) ||  Auth::user()->role_id == 1)
                                                <a class="dropdown-item" href="{{url('module-pages', [@$mdle->id,'edit'])}}">@lang('lang.edit')</a>
                                                @endif
                                                @if(in_array(107, @$module_links) ||  Auth::user()->role_id == 1)
                                                <a class="dropdown-item" data-toggle="modal" data-target="#deleteDesignationModal{{@$mdle->id}}"
                                                    href="#">@lang('lang.delete')</a>
                                                @endif
                                            </div>
                                        </div>  --}}
                                    </td>
                                </tr>
                                <div class="modal fade admin-query" id="deleteDesignationModal{{@$mdle->id}}" >
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title">@lang('lang.delete') @lang('lang.paymentterms')</h4>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="text-center">
                                                    <h4>@lang('lang.are_you_sure_to_delete')</h4>
                                                </div>

                                                <div class="mt-40 d-flex justify-content-between">
                                                    <button type="button" class="primary-btn tr-bg" data-dismiss="modal">@lang('lang.cancel')</button>
                                                     {{ Form::open(['url' => 'module/'.@$mdle->id, 'method' => 'DELETE', 'enctype' => 'multipart/form-data']) }}
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

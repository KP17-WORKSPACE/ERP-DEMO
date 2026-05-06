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
            <h1>@lang('lang.ticket_list')</h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
            <a href="{{ route('admin.ticket_list')}}">@lang('lang.ticket_system')</a>
                <a href="#">@lang('lang.ticket_list')</a>
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area">
    <div class="container-fluid p-0">
        <div class="row justify-content-between p-3">
            <div class="bc-pages">
             
            </div>
            @if(in_array(287, @$modules) || Auth::user()->role_id != 7)
            <div class="bc-pages">
                    <a href="{{ route('admin.add_ticket') }}" class="primary-btn small fix-gr-bg">
                            <span class="ti-plus pr-2"></span>
                            @lang('lang.add')
                        </a>
            </div>
            @endif

    </div>


            @if(in_array(286, @$modules) || Auth::user()->role_id != 3)
            <div class="row">
                <div class="col-lg-12">
                        
                    <div class="">
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => ['admin.ticket_search'], 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'search_studentA']) }}
                            <div class="row white-box">
                                <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                                <div class="col-lg-4 mt-30-md">
                                    <select class="niceSelect w-100 bb form-control{{ $errors->has('category') ? ' is-invalid' : '' }}" id="select_class" name="category">
                                        <option data-display="@lang('lang.ticket_category') *" value="">@lang('lang.ticket_category') @lang('lang.select') *</option>
                                        @foreach($category as $item)
                                        <option value="{{@$item->id}}">{{@$item->name}}</option>
                                        @endforeach
                                    </select>
                                     @if ($errors->has('category'))
                                    <span class="invalid-feedback invalid-select" role="alert">
                                        <strong>{{ $errors->first('category') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="col-lg-4 mt-30-md">
                                    <select class="niceSelect w-100 bb form-control{{ $errors->has('priority') ? ' is-invalid' : '' }}" id="select_class" name="priority">
                                        <option data-display="@lang('lang.ticket_priority') *" value="">@lang('lang.ticket_priority') @lang('lang.select') *</option>
                                        @foreach($priority as $item)
                                        <option value="{{@$item->id}}">{{@$item->name}}</option>
                                        @endforeach
                                    </select>
                                     @if ($errors->has('priority'))
                                    <span class="invalid-feedback invalid-select" role="alert">
                                        <strong>{{ $errors->first('priority') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="col-lg-4 mt-30-md">
                                    <select class="niceSelect w-100 bb form-control{{ $errors->has('active_status') ? ' is-invalid' : '' }}" id="select_class" name="active_status">
                                        <option data-display="@lang('lang.Status') *" value="">@lang('lang.select_status') *</option>
                                        <option value="0">@lang('lang.pending')</option>
                                        <option value="1">@lang('lang.on_going')</option>
                                        <option value="2">@lang('lang.complete')</option>
                                        <option value="3">@lang('lang.close')</option>
                                    </select>
                                     @if ($errors->has('active_status'))
                                    <span class="invalid-feedback invalid-select" role="alert">
                                        <strong>{{ $errors->first('active_status') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="col-lg-12 mt-20 text-right">
                                    <button type="submit" class="primary-btn small fix-gr-bg">
                                        <span class="ti-search pr-2"></span>
                                        @lang('lang.search')
                                    </button>
                                </div>
                            </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
            @endif

            <div class="row mt-40">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 no-gutters">
                            <div class="main-title">
                                <h3 class="mb-0">@lang('lang.ticket_list')</h3>
                            </div>
                        </div>
                    </div>
                    

                        
                    <!-- </div> -->
                    <div class="row">
                        <div class="col-lg-12">
                            <table id="table_id" class="display school-table pl-2" cellspacing="0" width="100%">
                                <thead>
                                     @if(session()->has('message-success') != "" ||
                                    session()->get('message-danger') != "")
                                    <tr>
                                        <td colspan="7">
                                            @if(session()->has('message-success'))
                                            <div class="alert alert-success">
                                                {{ session()->get('message-success') }}
                                            </div>
                                            @elseif(session()->has('message-danger'))
                                            <div class="alert alert-danger">
                                                {{ session()->get('message-danger') }}
                                            </div>
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <th width="25%">@lang('lang.subject')</th>
                                        <th width="15%">@lang('lang.category')</th>
                                        <th width="10%">@lang('lang.user') @lang('lang.name')</th>
                                        <th width="15%">@lang('lang.ticket_priority')</th>
                                        <th width="10%">@lang('lang.user_agent')</th>
                                        <th width="10%">@lang('lang.Status')</th>
                                        <th width="15%">@lang('lang.action')</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($ticket as $value)
                                    @if (Auth::user()->role_id == 1)
                                    <tr>
                                            <td>{{str_limit(@$value->subject,35)}}</td>
                                            <td>{{@$value->category->name}}</td>
                                            <td>{{$value->user->username}}</td>
                                            <td>{{@$value->priority->name}}</td>
                                            <td>{{@$value->agent_user?@$value->agent_user->username:'Not assign yet !'}}</td>
                                            @if (@$value->active_status == 0)
                                            <td>@lang('lang.pending')</td>
                                            @endif
                                            @if (@$value->active_status == 1)
                                            <td>@lang('lang.ongoing')</td>
                                            @endif
                                            @if (@$value->active_status == 2)
                                            <td>@lang('lang.complete')</td>
                                            @endif
                                            @if (@$value->active_status == 3)
                                            <td>@lang('lang.close')</td>
                                            @endif
                                            <td>
                                                    <div class="row">
                                                    <div class="col-sm-6">
                    
                                                    <div class="dropdown">
                                                        <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">
                                                            @lang('lang.select')
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-right">
                     
                                                    @if(in_array(288, @$modules) || Auth::user()->role_id != 7)
                    
                                                            <a class="dropdown-item" href="{{ route('admin.ticket_view',@$value->id)}}"> @lang('lang.view')</a>
                                                    @endif
                                                    @if(in_array(289, @$modules) || Auth::user()->role_id != 7)
                                                            <a class="dropdown-item" href="{{ route('admin.ticket_edit',@$value->id)}}"> @lang('lang.edit')</a>
                                                    @endif
                                                    @if(in_array(290, @$modules) || Auth::user()->role_id != 7)
                                                            <a class="deleteUrl dropdown-item" data-modal-size="modal-md" title="Delete Ticket" href="{{ route('admin.ticket_delete_view',@$value->id)}}"> @lang('lang.delete')</a>
                                                    @endif
                    
                                                            
                    
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                                </td>
                                    </tr>  
                                    @else
                                    @if (isset($value->assign_user) && @Auth::user()->id == @$value->assign_user)
                                     
                                    <tr>
                                            <td>{{str_limit(@$value->subject,35)}}</td>
                                            <td>{{@$value->category->name}}</td>
                                            <td>{{$value->user->username}}</td>
                                            <td>{{@$value->priority->name}}</td>
                                            <td>{{@$value->agent_user?@$value->agent_user->username:'Not assign yet !'}}</td>
                                            @if (@$value->active_status == 0)
                                            <td>@lang('lang.pending')</td>
                                            @endif
                                            @if (@$value->active_status == 1)
                                            <td>@lang('lang.ongoing')</td>
                                            @endif
                                            @if (@$value->active_status == 2)
                                            <td>@lang('lang.complete')</td>
                                            @endif
                                            @if (@$value->active_status == 3)
                                            <td>@lang('lang.close')</td>
                                            @endif
                                            <td>
                                                    <div class="row">
                                                    <div class="col-sm-6">
                    
                                                    <div class="dropdown">
                                                        <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">
                                                            @lang('lang.select')
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-right">
                     
                                                    
                    
                                                            <a class="dropdown-item" href="{{ route('admin.ticket_view',@$value->id)}}"> @lang('lang.view')</a>
                                                            <a class="dropdown-item" href="{{ route('admin.ticket_edit',@$value->id)}}"> @lang('lang.edit')</a>
                                                            <a class="deleteUrl dropdown-item" data-modal-size="modal-md" title="Delete Ticket" href="{{ route('admin.ticket_delete_view',@$value->id)}}"> @lang('lang.delete')</a>
                    
                                                            
                    
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                                </td>
                                    </tr>   
                                    @endif
                                    @endif
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

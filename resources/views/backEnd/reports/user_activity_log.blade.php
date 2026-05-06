@extends('backEnd.master')
@section('mainContent')
@php
    $modules = [];
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();

 
    foreach($permissions as $permission){ @$module_links[] = @$permission->module_link_id; @$modules[] = @$permission->moduleLink->module_id;}

 
    $modules = array_unique(@$modules);

@endphp

<link href="{{asset('public/css/user_activity.css')}}" type="text/css" rel="stylesheet">

<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('lang.user_log')</h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                <a href="#">@lang('lang.reports')</a>
                <a href="#">@lang('lang.user_log')</a>
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area">
    
    <div class="container-fluid p-0">
            

            <div class="row mt-40">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-6 no-gutters">
                            <div class="main-title">
                                <h3 class="mb-0">@lang('lang.user_log') @lang('lang.report')</h3>
                            </div>
                        </div>
                    </div> 

                    <div class="row">
                        <div class="col-lg-12">
                            <table id="table_id" class="display school-table" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>@lang('lang.sl')</th>
                                        <th>@lang('lang.note')</th>
                                        <th>@lang('lang.action')</th>
                                        <th>@lang('lang.table')</th>
                                        <th>@lang('lang.author') @lang('lang.name')</th>
                                        <th>@lang('lang.date') @lang('lang.time')</th>
                                        <th>@lang('lang.details')</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @php $count = 1 ; @endphp
                                    @foreach($data as $user_log)
                                    <tr>
                                        <td>{{@$count++}}</td>
                                        <td>{{@$user_log->note}}</td>
                                        <td>{{@$user_log->action}}</td>
                                        <td>{{$@user_log->model_name}}</td>
                                        <td>{{App\User::GetStaffname(@$user_log->author_id)}}</td>
                                        <td>{{date('jS M, Y h:i A', strtotime(@$user_log->created_at))}}</td> 
                                        <td>



                                        <div class="dropdown">
                                            <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">
                                                @lang('lang.select')
                                            </button>

                                            <div class="dropdown-menu dropdown-menu-right"> 
                                                @if(in_array(352, @$module_links) || Auth::user()->role_id == 1)
                                                <a class="dropdown-item" data-toggle="modal" data-target="#ViewDetails{{@$user_log->id}}"  href="#">View Details </a>
                                                @endif
                                            </div>

                                        </div>


                                              <div class="modal fade admin-query" id="ViewDetails{{@$user_log->id}}" >
                                                <div class="modal-dialog modal-dialog-centered  modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">User Activities: {{@$user_log->note}}</h4>
                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                        </div>

                                                        <div class="modal-body">
                                                            <table cellspacing="0" width="100%"  class="user_activity_log_table" >
                                                                <thead>
                                                                    <tr>
                                                                       <th>@lang('lang.note')</th>
                                                                        <th>@lang('lang.action')</th>
                                                                        <th>@lang('lang.new') @lang('lang.date')</th>
                                                                        <th>@lang('lang.old') @lang('lang.date')</th>
                                                                    </tr>
                                                                </thead> 
                                                                <tbody>
                                                                    <tr>
                                                                        <td>{{@$user_log->note}}</td>
                                                                        <td>{{@$user_log->action}}</td>
                                                                        <td>
                                                                            @php  @$new_data  = json_decode(@$user_log->new_data);  
                                                                            if(!empty(@$new_data )){
                                                                                foreach ($new_data as $key => $value){
                                                                                    echo '<b>'.@$key.'</b> : '.@$value.'<br>';
                                                                                }
                                                                            }
                                                                            @endphp
                                                                        </td>
                                                                        <td>
                                                                            
                                                                            @php  @$old_data  = json_decode(@$user_log->old_data);  
                                                                            if(!empty(@$old_data )){
                                                                                foreach ($old_data as $key => $value){
                                                                                    echo '<b>'.@$key.'</b> : '.@$value.'<br>';
                                                                                }
                                                                            }
                                                                            @endphp
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>

                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                
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
</section>


@endsection

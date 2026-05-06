@extends('backEnd.masterpage')
@section('mainContent')

@php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
@endphp

<div class="container-fluid">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h2 class="page-heading m-0">User Authentication List</h2>
            <span class="page-label">Home - User Authentication List</span>
        </div>
        <div>
            {{--  <a href="{{ url('add-staff') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> User</a>  --}}
        </div>
    </div>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>@lang('User') @lang('lang.no')</th>
                            <th>@lang('lang.name')</th>
                            <th>@lang('lang.role')</th>
                            <th>@lang('lang.department')</th>
                            <th>@lang('lang.designation')</th>
                            <th>@lang('lang.mobile')</th>
                            <th>@lang('lang.email')</th>
                            <th>@lang('lang.status')</th>
                            <th style="width: 110px;">@lang('lang.action')</th>
                        </tr>
                    </thead>

                    <tbody>
                        @if(count($data)>0)
                        @foreach($data as $value)
                        <tr id="{{$value->id}}">
                            <td>{{ @$value->staff_no }} </td>
                            <td>{{@$value->first_name}}&nbsp;{{@$value->last_name}}</td>
                            <td>{{!empty(@$value->roles->name)?@$value->roles->name:''}}</td>
                            <td>{{@$value->departments !=""?@$value->departments->name:""}}</td>
                            <td>{{@$value->designations !=""?@$value->designations->title:""}}</td>
                            <td>{{@$value->mobile}}</td>
                            <td>{{@$value->email}}</td>
                            <td>
                                @if (@$value->active_status == 1)
                                <i class="fa fa-check text-success" aria-hidden="true"></i>
                                @else
                                <i class="fa fa-times text-danger" aria-hidden="true"></i>
                                @endif
                            </td>
                            <td>
                                @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
                                <a class="btn-sm btn-primary" href="{{ route('staff-auth-approve', @$value->id) }}">Approve</a>
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
@endsection

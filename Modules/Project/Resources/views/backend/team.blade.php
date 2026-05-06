@extends('backEnd.master')
@section('mainContent')
<link rel="stylesheet" href="{{ url('/') }}/Modules/Project/Resources/assets/css/team.css">

<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('lang.team')</h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                <a href="#">@lang('lang.project') @lang('lang.management')</a>
                <a href="#">@lang('lang.team')</a>
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area">
    <div class="container-fluid p-0">
        @if(isset($category))
          
            <div class="row">
                <div class="offset-lg-10 col-lg-2 text-right col-md-12 mb-20">
                    <a href="{{ route('InfixProjectCategoryList')}}" class="primary-btn small fix-gr-bg">
                        <span class="ti-plus pr-2"></span>
                        @lang('lang.add')
                    </a>
                </div>
            </div>
           
        @endif
        <div class="row">
            <div class="col-lg-4">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-title">
                            <h3 class="mb-30">@if(isset($team))
                                    @lang('lang.edit')
                                @else
                                    @lang('lang.add')
                                @endif
                                @lang('lang.team')
                            </h3>
                        </div>
                        @if(isset($team))
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'InfixTeamUpdate', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                        @else
                       
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'InfixTeamStore',
                        'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
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
                                            <input class="primary-input form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                                                type="text" name="name" autocomplete="off" value="{{isset($team)? @$team->name:''}}">
                                            <input type="hidden" name="id" value="{{isset($team)? @$team->id: ''}}">
                                            <label>@lang('lang.team') @lang('lang.name') <span>*</span></label>

                                            <span class="focus-border"></span>
                                            @if ($errors->has('name'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('name') }}</strong>
                                            </span>
                                            @endif
                                        </div>

                                            <br>
                                        <div class="row mt-40">
                                            <div class="col-lg-12">
                                                <div class="input-effect">
                                                    <textarea class="primary-input form-control{{ $errors->has('description') ? ' is-invalid' : '' }}"" cols="0" rows="4" name="description">{{isset($team)? @$team->description:''}}</textarea>
                                                    <label>@lang('lang.description')  <span>*</span></label>
                                                    <span class="focus-border textarea"></span>
                                                     @if ($errors->has('description'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('description') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                         </div>
                                          <div class="row mt-30" >
                                              <div class="col-lg-12">
                                               <b> <label class="text-uppercase text-bold">@lang('lang.assign') @lang('lang.member') *</label> </b>

                                              </div>
                                    <div class="col-lg-12 div-scroll">
                                        @foreach($staffs as $staff)
                                        <div class="">
                                            @if(isset($memberId))
                                            <input type="checkbox" id="staff{{$staff->id}}" class="common-checkbox form-control{{ $errors->has('staff') ? ' is-invalid' : '' }}"  name="staff[]" value="{{$staff->id}}" {{in_array($staff->id, $memberId)? 'checked': ''}}>
                                            <label for="staff{{$staff->id}}"> 
                                               <img class="team-image" src="{{ file_exists(@$staff->staff_photo) ? asset($staff->staff_photo) : asset('public/uploads/staff/demo/staff.png') }}"> {{$staff->full_name}} 
                                            </label>
                                            @else
                                            
                                            <input type="checkbox" id="staff{{@$staff->id}}" class="common-checkbox form-control{{ $errors->has('staff') ? ' is-invalid' : '' }}" name="staff[]" value="{{@$staff->id}}">

                                            
                                            <label for="staff{{$staff->id}}"> 
                                                
                                                <img class="team-image" src="{{ file_exists(@$staff->staff_photo) ? asset($staff->staff_photo) : asset('public/uploads/staff/demo/staff.png') }}" > {{$staff->full_name}} 
                                            </label>


                                            @endif
                                        </div>
                                        @endforeach
                                        
                                    </div>
                                    <div class="pl-2 mt-2">
                                        @if($errors->has('staff'))
                                            <span class="text-danger validate-textarea-checkbox" role="alert">
                                                <strong>{{ @$errors->first('staff') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                    </div>
                                </div>
                                
                                <div class="row mt-40">
                                    <div class="col-lg-12 text-center">
                                        <button class="primary-btn fix-gr-bg" data-toggle="tooltip">
                                            <span class="ti-check"></span>
                                            {{isset($team)? 'update':'save'}} @lang('lang.team')
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="row">
                    <div class="col-lg-4 no-gutters">
                        <div class="main-title">
                            <h3 class="mb-0">@lang('lang.team') @lang('lang.list')</h3>
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
                                    <th>@lang('lang.sl')</th>
                                    <th>@lang('lang.team') @lang('lang.name')</th>
                                    <th>@lang('lang.member') </th>
                                    <th>@lang('lang.description')</th>
                                    <th>@lang('lang.action')</th>
                                </tr>
                            </thead>

                            <tbody>
                                @php
                                    $x=1;
                                @endphp
                                @foreach($teams as $team)
                                <tr>
                                    <td>{{ $x}}</td>
                                    <td>{{@$team->name}}</td>
                                    <td class="">
                                        <div class="team_member_slider">
                                    @php
                                           $team_members=DB::table('infix_team_member')->where('team_id',@$team->id)->get(); 
                                           $x++;
                                    @endphp

                                        @foreach ($team_members as $team_member)
                                            
                                            @php
                                                $member_details=DB::table('sm_staffs')->where('id',@$team_member->staff_id)->first();
                                            @endphp
                                          
                                        <a target="_blank" href="{{route('viewStaff', @$member_details->id)}}" title="{{ @$member_details->full_name }}"> 
                                            <img class="team-image" src="{{ file_exists(@$member_details->staff_photo) ? asset($member_details->staff_photo) : asset('public/uploads/staff/demo/staff.png') }}" >
                                        </a>       
                                           
                                       
                                    @endforeach
                                        </div>
                                       
                                    </td>
                                        
                                       

                                    <td>{{@$team->description}}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">
                                                @lang('lang.select')
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                               
                                                <a class="dropdown-item" href="{{ route('InfixTeamEdit', [@$team->id
                                                    ])}}">@lang('lang.edit')</a>
                                                <a class="dropdown-item" data-toggle="modal" data-target="#deleteDesignationModal{{$team->id}}"
                                                    href="#">@lang('lang.delete')</a>
                                               
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <div class="modal fade admin-query" id="deleteDesignationModal{{@$team->id}}" >
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title">@lang('lang.delete') @lang('lang.team')</h4>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="text-center">
                                                    <h4>@lang('lang.are_you_sure_to_delete')</h4>
                                                </div>

                                                <div class="mt-40 d-flex justify-content-between">
                                                    <button type="button" class="primary-btn tr-bg" data-dismiss="modal">@lang('lang.cancel')</button>
                                                     {{ Form::open(['url' => 'project/team/delete/'.$team->id, 'method' => 'GET', 'enctype' => 'multipart/form-data']) }}
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

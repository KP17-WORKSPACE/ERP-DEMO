@extends('backEnd.master')
@section('mainContent')
<link rel="stylesheet" href="{{ url('/')}}/Modules/Project/Resources/assets/css/project_dashboard.css"/>
    <link rel="stylesheet" href="{{ url('/')}}/Modules/Project/Resources/assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="{{ url('/')}}/Modules/Project/Resources/assets/css/magnific-popup.css">
    <link rel="stylesheet" href="{{ url('/')}}/Modules/Project/Resources/assets/css/animate.css">
    <link rel="stylesheet" href="{{ url('/')}}/Modules/Project/Resources/assets/css/material-components-web.min.css">
    <link rel="stylesheet" href="{{ url('/')}}/Modules/Project/Resources/assets/css/slicknav.css">
    <link rel="stylesheet" href="{{ url('/')}}/Modules/Project/Resources/assets/css/icon.css">
    <link rel="stylesheet" href="{{ url('/')}}/Modules/Project/Resources/assets/css/style.css">
    <link rel="stylesheet" href="{{ url('/')}}/Modules/Project/Resources/assets/css/gijgo.css">
    <link rel="stylesheet" href="{{ url('/') }}/Modules/Project/Resources/assets/css/team.css">

    <link rel="stylesheet" href="{{ url('/') }}/Modules/Project/Resources/assets/css/task/project.css">


<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('lang.project')</h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                <a href="#">@lang('lang.project') @lang('lang.management')</a>
                <a href="#">@lang('lang.project')</a>
            </div>
        </div>
    </div>
</section>
<section class="mb-40">
    <div class="row">
        <div class="col-lg-3">
            <div class="white-box">
                    <div class="project_heading_top">
                        <div class="row align-items-center">
                            <div class="col-xl-12">
                                    <h4>@lang('add') @lang('lang.project')</h4>
                            </div>
                        </div>
                    </div>

                    @if (isset($edit))
                        <form id="Create_Project" action="{{ route('InfixProjectUpdate') }}" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="{{ @$edit->id }}">
                            <input type="hidden" name="old_image" value="{{ @$edit->photo }}">
                    @else
                       <form id="Create_Project" action="{{ route('InfixProjectStore') }}" method="POST" enctype="multipart/form-data">

                    @endif

                    @csrf
                    {{-- {{ dd($edit) }} --}}
                    <div class="row mt-30 mb-20">
                        <div class="col">
                            <select class="pl-0 niceSelect w-100 bb form-control{{ @$errors->has('category') ? ' is-invalid' : '' }}" name="category">
                                <option data-display="@lang('lang.select') @lang('lang.category') *" value="">@lang('lang.select')   *</option>
                                @foreach($project_categories as $category)

                                @if (isset($edit))
                                    <option @if(@$edit->category_id==@$category->id) ? selected :'' @endif value="{{@$category->id}}" >{{@$category->name}}</option>


                                @else
                                    <option value="{{@$category->id}}" >{{@$category->name}}</option>
                                @endif

                                @endforeach
                            </select>
                            @if ($errors->has('category'))
                                <span class="invalid-feedback invalid-select" role="alert">
                                    <strong>{{ $errors->first('category') }}</strong>
                                </span>
                            @endif
                         </div>
                    </div>

                    {{--start project_name --}}
                    <div class="row mt-30 mb-20">
                        <div class="col">
                            <div class="input-effect">
                                <input type="text" class="primary-input form-control{{ $errors->has('project_name') ? ' is-invalid' : '' }}" name="project_name" value="{{isset($edit)? @$edit->name:old('project_name')}}">
                                <label>@lang('lang.project') @lang('lang.name')  <span>*</span></label>
                                <span class="focus-border textarea"></span>
                                 @if ($errors->has('project_name'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('project_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                     </div>
                    {{--end project_name --}}

                    {{--start description --}}
                    <div class="row mt-30 mb-20">
                        <div class="col">
                            <div class="input-effect">
                                <textarea class="primary-input form-control{{ $errors->has('description') ? ' is-invalid' : '' }}" cols="0" rows="4" name="description">{{isset($edit)? @$edit->description:old('description')}}</textarea>
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
                    {{--end description --}}





                    <div class="row mt-30 mb-20">
                        <div class="col">
                            <select class="pl-0 niceSelect w-100 bb form-control{{ $errors->has('customer') ? ' is-invalid' : '' }}" name="customer">
                                <option data-display="@lang('lang.select') Customer *" value="">Select Customer *</option>
                                    @foreach($customers as $customer)

                                    @if (isset($edit))
                                         <option @if(@$edit->customer_id==@$customer->id)? selected : '' @endif  value="{{@$customer->id}}" >{{@$customer->full_name}}</option>
                                    @else
                                         <option  value="{{@$customer->id}}" >{{@$customer->full_name}}</option>
                                    @endif



                                    @endforeach
                            </select>
                            @if ($errors->has('customer'))
                                <span class="invalid-feedback invalid-select" role="alert">
                                    <strong>{{ $errors->first('customer') }}</strong>
                                </span>
                            @endif
                         </div>
                    </div>


                    <div class="row mt-30 mb-20 no-gutters input-right-icon">
                        <div class="col">
                            <div class="input-effect">
                                <input class="primary-input date form-control{{ $errors->has('start_date') ? ' is-invalid' : '' }}" id="event_start_date" type="text"
                                name="start_date" value="{{isset($edit)? date('m/d/Y', strtotime(@$edit->start_date)): date('m/d/Y')}}" autocomplete="off">
                                <label>@lang('lang.start_date') <span></span> </label>
                                <span class="focus-border"></span>
                                @if ($errors->has('start_date'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('start_date') }}</strong>
                                </span>
                                @endif
                            </div>

                        </div>
                        <div class="col-auto">
                            <button class="" type="button">
                                <i class="ti-calendar" id="event_start_date"></i>
                            </button>
                        </div>
                    </div>

                    {{-- start due_date --}}
                    <div class="row mt-30 mb-20 no-gutters input-right-icon">
                        <div class="col">
                            <div class="input-effect">
                                <input class="primary-input date form-control{{ $errors->has('due_date') ? ' is-invalid' : '' }}" id="event_due_date" type="text"
                                name="due_date" value="{{isset($edit)? date('m/d/Y', strtotime(@$edit->due_date)): date('m/d/Y')}}" autocomplete="off">
                                <label>@lang('lang.due_date') <span></span> </label>
                                <span class="focus-border"></span>
                                @if ($errors->has('due_date'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('due_date') }}</strong>
                                </span>
                                @endif
                            </div>

                        </div>
                        <div class="col-auto">
                            <button class="" type="button">
                                <i class="ti-calendar" id="event_start_date"></i>
                            </button>
                        </div>
                    </div>
                    <div class="row mt-30 mb-20">
                        <div class="col">
                            <select class="pl-0 niceSelect w-100 bb form-control{{ $errors->has('team') ? ' is-invalid' : '' }}" name="team">
                                <option data-display="@lang('lang.select') Team *" value="">Select Team *</option>
                                    @foreach($teams as $team)

                                    @if (isset($edit))
                                        <option @if(@$edit->team_id==@$team->id)? selected : '' @endif  value="{{@$team->id}}" >{{@$team->name}}</option>
                                    @else
                                         <option  value="{{@$team->id}}" >{{ @$team->name}}</option>
                                    @endif


                                    @endforeach
                            </select>
                            @if ($errors->has('team'))
                                <span class="invalid-feedback invalid-select" role="alert">
                                    <strong>{{ $errors->first('team') }}</strong>
                                </span>
                            @endif
                         </div>
                    </div>
                    <div class="row mt-30">
                            <div class="col-lg-12">
                                <div class="row no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            <input class="primary-input" type="text"
                                                id="placeholderTaskFile"
                                                placeholder="@lang('lang.project') @lang('lang.image')"
                                                disabled>
                                            <span class="focus-border"></span>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <button class="primary-btn-small-input" type="button">
                                            <label class="primary-btn small fix-gr-bg"
                                                for="project_image">@lang('lang.browse')</label>
                                            <input type="file" class="d-none" name="project_image"
                                                id="project_image">
                                        </button>
                                    </div>
                                </div>
                            </div>
                    </div>
                    <div class="row mt-40">
                        <div class="col-lg-12 text-center">
                            <button class="primary-btn fix-gr-bg" data-toggle="tooltip">
                                <span class="ti-check"></span>
                                {{isset($edit)? 'update':'save'}}
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
        <div class="col-lg-9">
             <div class="content_body">
                <div class="project_main_content">
                    <div class="container-fluid p-0">
                        <div class="project_heading_top">
                            <div class="row align-items-center">
                                <div class="col-xl-12">
                                    <h4>@lang('lang.current') @lang('lang.projects')</h4>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card-columns project project_part">
                                    @foreach($projects as $project)
                                        <div class="card">
                                            <div class="">

                                                <div class="single_project ">
                                                    <div class="project_top d-flex align-items-center justify-content-between">
                                                        <div class="icon bg-transparent">
                                                            {{--  @if (!empty(@$project->photo))
                                                                <img style="max-width: 100% !important; height:auto" class="img-fluid img-responsive" src="{{ url('/')}}/{{@$project->photo}}" alt="">

                                                            @else
                                                            <img width="200" height="200" class="img-fluid img-responsive" src="{{ url('/')}}/public/uploads/projects/project.png" alt="">

                                                            @endif   --}}
                                                           <img src="{{ file_exists(@$project->photo) ? asset($project->photo) : asset('public/uploads/projects/project.png') }}" alt="">
                                                        </div>
                                                        <div class="edit_delete">
                                                            <div class="d-flex">
                                                                 <a href="{{url('project/project-edit',@$project->id)}}">@lang('lang.edit')</a>
                                                                <a href="{{url('project/project-delete',@$project->id)}}">@lang('lang.delete')</a>
                                                                <a href="{{url('project/project-task',@$project->id)}}">@lang('lang.tasks')</a>
                                                            </div>


                                                        <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">


                                                        @if ($project->is_complete==0)
                                                            <div class="common_radio_box">
                                                                <input class="common-radio complete_task" type="checkbox" onclick="projectComplete({{ @$project->id }})" value="{{ @$project->id }}" id="{{ @$project->id }}">
                                                                <label class="d-inline" for="{{ @$project->id }}"></label>@lang('lang.complete')
                                                            </div>
                                                        @else
                                                            <div class="common_radio_box">
                                                                <input class="common-radio complete_task" type="checkbox" onclick="projectIncomplete({{ @$project->id }})" value="{{ @$project->id }}" id="{{ @$project->id }}" @if (@$project->is_complete==1) checked @endif>
                                                                <label class="d-inline" for="{{ @$project->id }}"></label>@lang('lang.complete')
                                                            </div>
                                                        @endif


                                                        </div>

                                                    </div>

                                                    <div class="content d-block">
                                                            <h3 class="text-uppercase">{{@$project->name}}</h3>
                                                            @php
                                                                $get_customer_details = App\SmStaff::find(@$project->customer_id);
                                                            @endphp
                                                            <p>{{isset($get_customer_details)?@$get_customer_details->full_name:'' }}</p>
                                                        <p>
                                                            @php
                                                            $description= @$project->description;
                                                            $s = substr(@$description, 0, 120);
                                                            $result = substr($s, 0, strrpos($s, ' '));
                                                            @endphp
                                                            {{@$result}}...
                                                        </p>
                                                    </div>

                                                        @php
                                                            $total_task=DB::table('infix_project_tasks')->where('project_id','=',@$project->id)->get()->count();

                                                        @endphp
                                                        @if ($total_task>0)
                                                    <div class="progress">
                                                        @php
                                                        $comp_task=DB::table('infix_project_tasks')->where('project_id','=',@$project->id)->where('is_complete','=',1)->get()->count();
                                                        $incomp_task=DB::table('infix_project_tasks')->where('project_id','=',@$project->id)->where('is_complete','=',0)->get()->count();

                                                        $total_task=@$comp_task+@$incomp_task;
                                                        $percentage=round((@$comp_task*100)/@$total_task,2);
                                                        @endphp
                                                            <div class="progress-bar" role="progressbar" style="width: {{ @$percentage }}%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                                                <span class="progress_parcent">{{ @$percentage }}%</span>
                                                            </div>
                                                    </div>
                                                    @else
                                                            <p class="text-danger">@lang('lang.Task_Not_Assigned')</p>
                                                        @endif

                                                    <div class="project_footer d-flex justify-content-between">
                                                        <div class="team_member_slider">
                                                            @php
                                                            $team_members=DB::table('infix_team_member')->where('team_id',@$project->team_id)->get();
                                                            $current_date=date('Y-m-d H:i:s');
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


                                                    </div>
                                                    <div class="d-flex justify-content-between">

                                                        <p>@lang('lang.due') @lang('lang.date')</p>
                                                        @if ($project->due_date < $current_date)
                                                                    <span class="text-danger" >
                                                                    <i class="fa fa-calendar"></i> {{ Carbon::createFromFormat('Y-m-d H:i:s', @$project->due_date)->diffForHumans() }}
                                                                    </span>
                                                                    @else
                                                                    <span class="text-success" >
                                                                        <i class="fa fa-calendar"></i>   {{ \Carbon\Carbon::parse(@$project->due_date)->format('jS M, Y') }}
                                                                    </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center project_list_page">
                            {{ @$projects->onEachSide(1)->links() }}
                        </div>




                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script src="{{ asset('/') }}Modules/Project/Resources/assets/js/project/project.js"></script>

@endsection

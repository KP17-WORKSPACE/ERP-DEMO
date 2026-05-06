@extends('backEnd.master')
@section('mainContent')
    <link rel="stylesheet" href="{{ url('/') }}/Modules/Project/Resources/assets/css/style.css">
    <link rel="stylesheet" href="{{ url('/') }}/Modules/Project/Resources/assets/css/task/task.css">
    <link rel="stylesheet" href="{{ url('/') }}/Modules/Project/Resources/assets/css/team.css">

<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('lang.task')</h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                <a href="#">@lang('lang.project') @lang('lang.management')</a>
                <a href="#">@lang('lang.task')</a>
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area">
    <div class="container-fluid p-0">

        <div class="row">
            <div class="col-lg-3">
                <div class="row">
                    <div class="col-lg-12">
                        @if(isset($single_task))
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'InfixProjectTaskUpdate', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                        @else

                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'InfixProjectTaskStore',
                        'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                        @endif

                        <div class="white-box">
                            <div class="add-visitor">

                            <h3 class="mb-30">@if(isset($single_task))
                                    @lang('lang.edit')
                                @else
                                    @lang('lang.add')
                                @endif
                                @lang('lang.task') on {{ @$project->name }}
                            </h3>

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
                                            <input class="primary-input form-control{{ $errors->has('title') ? ' is-invalid' : '' }}"
                                                type="text" name="title" autocomplete="off" value="{{isset($single_task)? @$single_task->title:''}}">
                                            <input type="hidden" name="id" value="{{isset($single_task)? @$single_task->id: ''}}">
                                            <label>@lang('lang.task') @lang('lang.title') <span>*</span></label>

                                            <span class="focus-border"></span>
                                            @if ($errors->has('title'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('title') }}</strong>
                                            </span>
                                            @endif
                                        </div>

                                            <br>
                                        <div class="row mt-40">
                                            <div class="col-lg-12">
                                                <div class="input-effect">
                                                    <textarea class="primary-input form-control{{ $errors->has('description') ? ' is-invalid' : '' }}"" cols="0" rows="4" name="description">{{isset($single_task)? @$single_task->description:''}}</textarea>
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



                                        <div class="row mt-30">
                                            <div class="col-lg-12">
                                                <select class="w-100 bb niceSelect form-control{{ $errors->has('assign') ? ' is-invalid' : '' }}" id="assign" name="assign">
                                                    <option data-display="@lang('lang.assign') *" value="">@lang('lang.assign')*</option>
                                                    @foreach ($team_members as $team_member)

                                                    @if (isset($single_task))
                                                       <option  @if (@$single_task->assigned_to==@$team_member->staff_id) selected : '' @endif  value="{{ @$team_member->staff_id }}">{{ @$team_member->full_name }}</option>

                                                   @else
                                                       <option  value="{{ @$team_member->staff_id }}">{{ @$team_member->full_name }}</option>

                                                   @endif

                                                    @endforeach
                                                </select>
                                                @if ($errors->has('assign'))
                                                <span class="invalid-feedback invalid-select" role="alert">
                                                    <strong>{{ $errors->first('assign') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="row mt-30">
                                        <div class="col-lg-12">
                                        <div class="no-gutters input-right-icon">
                                            <div class="col">
                                                <div class="input-effect">
                                                    <input class="primary-input date form-control{{ $errors->has('due_date') ? ' is-invalid' : '' }}"
                                                           id="due_date" type="text" name="due_date"
                                                           value="{{isset($single_task)? @$single_task->due_date:''}}"
                                                           readonly="true">
                                                    <label>@lang('lang.due') @lang('lang.date')
                                                        <span>*</span></label>
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
                                                    <i class="ti-calendar" id="homework_date_icon"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    </div>

                                        <input type="hidden" name="project_id" id="" value="{{ @$project->id }}">
                                         <input type="hidden" name="id" value="{{isset($single_task)? @$single_task->id: ''}}">
                                         <input type="hidden" id="old_file" name="old_file" value="{{isset($single_task)? @$single_task->image: ''}}">

                                        <div class="row mt-30">
                                                <div class="col-lg-12">
                                                    <div class="row no-gutters input-right-icon">
                                                        <div class="col">
                                                            <div class="input-effect">
                                                                <input class="primary-input" type="text"
                                                                    id="placeholderTaskFile"
                                                                    placeholder="@lang('lang.task') @lang('lang.file')"
                                                                    disabled>
                                                                <span class="focus-border"></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-auto">
                                                            <button class="primary-btn-small-input" type="button">
                                                                <label class="primary-btn small fix-gr-bg"
                                                                    for="task_file">@lang('lang.browse')</label>
                                                                <input type="file" class="d-none" name="task_file"
                                                                    id="task_file">
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="row mt-40">
                                    <div class="col-lg-12 text-center">
                                        <button class="primary-btn fix-gr-bg" data-toggle="tooltip">
                                            <span class="ti-check"></span>
                                            {{isset($single_task)? 'update':'save'}} @lang('lang.task')
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

          
                    <div class="student-details ">
                        <ul class="nav nav-tabs justify-content-end" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">@lang('lang.incomplete')</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">@lang('lang.complete')</a>
                            </li>
                        </ul>
                    </div>

                  <div class="tab-content py-3 px-3 px-sm-0" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">

                         <div class="todo_list">
                            <div class="todo_heading">
                                <h3>
                                  {{ @$project->name }}
                                </h3>
                            </div>

                            @foreach ($team_members as $team_member)

                            <div class="d-block">
                                <a target="_blank" href="{{route('viewStaff', $team_member->staff_id)}}" title="{{ $team_member->full_name }}">
                                    <img class="team-image" src="{{ file_exists(@$team_member->staff_photo) ? asset($team_member->staff_photo) : asset('public/uploads/staff/demo/staff.png') }}" width="200" height="200" alt="{{$team_member->id}}">
                                        {{ $team_member->full_name }}
                                </a>
                            </div>
                            @php
                                $member_im_tasks=DB::table('infix_project_tasks')
                                ->where('assigned_to','=',@$team_member->staff_id)
                                ->where('is_complete','=',0)
                                ->where('project_id','=',@$project->id)
                                ->orderBy('due_date', 'asc')
                                ->get();
                                    $current_date=date('m/d/Y');
                           @endphp

                          
                                <ul id="list-items">

                                @foreach($member_im_tasks as $task)
                                    <li id="imcomplete_task{{ $task->id }}" class="task_list_project">
                                        <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                                        <input class="checkbox" type="checkbox" onclick="taskComplete({{ @$task->id }})" value="{{ @$task->id }}" id="{{ @$task->id }}" @if (@$task->is_complete==1) checked @endif>
                                        <label for="checkbox"></label>
                                        @if (@$task->due_date < @$current_date)
                                            <a data-toggle="modal" data-target="#viewTask{{$task->id}}"  href="#"> {{@$task->title}} -  <span class="text-danger" >{{ \Carbon\Carbon::parse(@$task->due_date)->format('jS M, Y') }}</span> </a>
                                        @else
                                            <a data-toggle="modal" data-target="#viewTask{{$task->id}}"  href="#">  {{@$task->title}} -  <span class="text-success" > {{ \Carbon\Carbon::parse(@$task->due_date)->format('jS M, Y') }} </span>  </a>
                                        @endif
                                    </li>

                                    <div class="modal fade admin-query" id="viewTask{{@$task->id}}" >
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title">@lang('lang.task') @lang('lang.details')</h4>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>

                                                <div class="modal-body">
                                                    <div class="text-center">
                                                        <h4>{{ @$task->title }}</h4>
                                                    </div>
                                                    <hr>

                                                    <div class="row">
                                                        <div class="col-lg-4">
                                                                <div class="team_member_slider">
                                                                <a target="_blank" href="{{route('viewStaff', @$team_member->staff_id)}}" title="{{ @$team_member->full_name }}"> <img class="team-image" src="{{ asset(@$team_member->staff_photo) }}" width="200" height="200" alt="{{@$team_member->id}}">
                                                                        {{ @$team_member->full_name }}</a>

                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4">
                                                            @if (@$task->due_date < @$current_date)
                                                            <span class="text-danger" > <i class="fa fa-calendar"></i>{{ \Carbon\Carbon::parse(@$task->due_date)->format('jS M, Y') }}</span>

                                                            @else
                                                            <span class="text-success" ><i class="fa fa-calendar"></i>
                                                                    {{ \Carbon\Carbon::parse(@$task->due_date)->format('jS M, Y') }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <li id="imcomplete_task{{ @$task->id }}" class="d-flex">
                                                                <input id="task{{ @$task->id }}" class="checkbox" type="checkbox" onclick="taskComplete({{ @$task->id }})" value="{{ @$task->id }}"  @if (@$task->is_complete==1) checked @endif>
                                                                <label for="task{{ @$task->id }}"></label>@lang('lang.pending')
                                                            </li>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <p>{{ @$task->description }}</p>
                                                    @php

                                                            if(!function_exists('showDocumentName')){
                                                                function showDocumentName($data){
                                                                $name = explode('/', $data);
                                                                return $name[3];
                                                            }
                                                            }

                                                        @endphp

                                                                @php
                                                                    $path = $task->image;
                                                                    $ext = pathinfo($path, PATHINFO_EXTENSION);

                                                                @endphp

                                                                @if (@$ext=='jpg' || @$ext=='png' || @$ext=='jpeg')
                                                                <form action="{{ url('project/download-task-attachment') }}" method="post">
                                                                <a target="_blank" href="{{ asset(@$task->image)}}" class="avatar avatar-lg">

                                                                <img src="{{ asset(@$task->image) }}" width="500" height="500" class="img-fluid img-responsive" alt="{{ @$task->title }}">
                                                                <p class="card-text small text-muted mb-1">

                                                                        @php
                                                                            $size=filesize(@$task->image);
                                                                            $size_mb=@$size/1024;
                                                                            $path = @$task->image;
                                                                            $ext = pathinfo(@$path, PATHINFO_EXTENSION);

                                                                            echo round(@$size_mb,2).' KB  '. strtoupper(@$ext);
                                                                            @endphp
                                                                        </p>
                                                                </a>
                                                                @csrf
                                                                        <input type="hidden" name="file_name" value="{{ @$task->image }}">
                                                                    <button class="primary-btn fix-gr-bg" data-toggle="tooltip">
                                                                            <span class="ti-down"></span>
                                                                        @lang('lang.download')
                                                                        </button>

                                                                    </form>


                                                                @elseif(@$ext=='doc' || @$ext=='docx')

                                                                <form action="{{ url('project/download-task-attachment') }}" method="post">


                                                                <a href="#" class="avatar avatar-lg">
                                                                        <img width="100"  height="100" src="{{ asset('public/uploads/doc.png') }}" alt="..." class="avatar-img rounded">

                                                                    <p class="card-text small text-muted mb-1">

                                                                        @php
                                                                            $size=filesize(@$task->image);
                                                                            $size_mb=@$size/1024;
                                                                            $path = @$task->image;
                                                                            $ext = pathinfo(@$path, PATHINFO_EXTENSION);

                                                                            echo round(@$size_mb,2).' KB  '. strtoupper(@$ext);
                                                                            @endphp
                                                                        </p>
                                                                    </a>
                                                                    @csrf
                                                                        <input type="hidden" name="file_name" value="{{ @$task->image }}">
                                                                    <button class="primary-btn fix-gr-bg" data-toggle="tooltip">
                                                                            <span class="ti-down"></span>
                                                                        @lang('lang.download')
                                                                        </button>

                                                                    </form>





                                                                @elseif($ext=='pdf' )

                                                                <a href="#" class="avatar avatar-lg">
                                                                        <form action="{{ url('project/download-task-attachment') }}" method="post">


                                                                <a href="#" class="avatar avatar-lg">
                                                                        <img width="100"  height="100" src="{{ asset('public/uploads/pdf.png') }}" alt="..." class="avatar-img rounded">

                                                                    <p class="card-text small text-muted mb-1">

                                                                        @php
                                                                            $size=filesize(@$task->image);
                                                                            $size_mb=@$size/1024;
                                                                            $path = @$task->image;
                                                                            $ext = pathinfo(@$path, PATHINFO_EXTENSION);

                                                                            echo round(@$size_mb,2).' KB  '. strtoupper(@$ext);
                                                                            @endphp
                                                                        </p>
                                                                    </a>
                                                                    @csrf
                                                                        <input type="hidden" name="file_name" value="{{ @$task->image }}">
                                                                    <button class="primary-btn fix-gr-bg" data-toggle="tooltip">
                                                                            <span class="ti-down"></span>
                                                                        @lang('lang.download')
                                                                        </button>

                                                                    </form>
                                                                @elseif($ext=='txt' )
                                                                <a href="#" class="avatar avatar-lg">
                                                                        <form action="{{ url('project/download-task-attachment') }}" method="post">


                                                                <a href="#" class="avatar avatar-lg">
                                                                        <img width="100"  height="100" src="{{ asset('public/uploads/txt.png') }}" alt="..." class="avatar-img rounded">

                                                                    <p class="card-text small text-muted mb-1">

                                                                        @php
                                                                            $size=filesize(@$task->image);
                                                                            $size_mb=@$size/1024;
                                                                            $path = @$task->image;
                                                                            $ext = pathinfo(@$path, PATHINFO_EXTENSION);

                                                                            echo round(@$size_mb,2).' KB  '. strtoupper(@$ext);
                                                                            @endphp
                                                                        </p>
                                                                    </a>
                                                                    @csrf
                                                                        <input type="hidden" name="file_name" value="{{ @$task->image }}">
                                                                    <button class="primary-btn fix-gr-bg" data-toggle="tooltip">
                                                                            <span class="ti-down"></span>
                                                                        @lang('lang.download')
                                                                        </button>

                                                                    </form>
                                                                @elseif($ext=='zip' )
                                                                <a href="#" class="avatar avatar-lg">
                                                                        <form action="{{ url('project/download-task-attachment') }}" method="post">


                                                                <a href="#" class="avatar avatar-lg">
                                                                        <img width="100"  height="100" src="{{ asset('public/uploads/zip.png') }}" alt="..." class="avatar-img rounded">

                                                                    <p class="card-text small text-muted mb-1">

                                                                        @php
                                                                            $size=filesize(@$task->image);
                                                                            $size_mb=@$size/1024;
                                                                            $path = @$task->image;
                                                                            $ext = pathinfo(@$path, PATHINFO_EXTENSION);

                                                                            echo round(@$size_mb,2).' KB  '. strtoupper(@$ext);
                                                                            @endphp
                                                                        </p>
                                                                    </a>
                                                                    @csrf
                                                                        <input type="hidden" name="file_name" value="{{ @$task->image }}">
                                                                    <button class="primary-btn fix-gr-bg" data-toggle="tooltip">
                                                                            <span class="ti-down"></span>
                                                                        @lang('lang.download')
                                                                        </button>

                                                                    </form>
                                                                @endif



                                                    <div class="mt-40 d-flex justify-content-between">

                                                        <a href="{{ route('InfixProjectTaskDelete',[@$task->id]) }}" class="text-light">
                                                        <button class="primary-btn fix-gr-bg" type="submit">@lang('lang.delete')</button>
                                                        </a>
                                                        <a href="{{ route('InfixProjectTaskEdit',[@$task->id]) }}" class="text-light">
                                                        <button class="primary-btn fix-gr-bg" type="submit">@lang('lang.edit')</button>
                                                        </a>
                                                        <button type="button" class="primary-btn tr-bg" data-dismiss="modal">@lang('lang.cancel')</button>

                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    @endforeach

                                </ul>
                            <hr>
                            @endforeach




                    </div>


                    </div>

                       {{-- Incomplete Task Panel --}}
                    <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">

                        <div class="todo_list">
                            <div class="todo_heading">
                                <h3>
                                    {{ @$project->name }}
                                </h3>
                            </div>
                              @foreach ($team_members as $team_member)
                                  <div class="team_member_slider">
                                <a target="_blank" href="{{route('viewStaff', @$team_member->staff_id)}}" title="{{ @$team_member->full_name }}"> <img class="team-image" src="{{ asset(@$team_member->staff_photo) }}" width="200" height="200" alt="{{@$team_member->id}}">
                                        {{ @$team_member->full_name }}</a>

                            </div>

                            @php
                                $member_c_tasks=DB::table('infix_project_tasks')
                                ->where('assigned_to','=',@$team_member->staff_id)
                                ->where('is_complete','=',1)
                                ->where('project_id','=',@$project->id)
                                ->orderBy('due_date', 'asc')
                                ->get();
                            @endphp

                              <ul id="list-items" class="complete_task_list{{ @$team_member->staff_id }}">

                                @foreach($member_c_tasks as $task)

                                    <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">

                                    <li id="imcomplete_task{{ @$task->id }}" class="task_list_project">
                                        <input disabled class="checkbox" type="checkbox" onclick="taskComplete({{ @$task->id }})" value="{{ @$task->id }}" id="{{ @$task->id }}" @if (@$task->is_complete==1) checked @endif>
                                        <label for="checkbox"></label>
                                         <a data-toggle="modal" data-target="#viewTask{{@$task->id}}"  href="#">
                                        {{@$task->title}}-{{ \Carbon\Carbon::parse(@$task->due_date)->format('jS M, Y') }}

                                         </a>

                                    </li>

                                          <div class="modal fade admin-query" id="viewTask{{@$task->id}}" >
                                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">@lang('lang.task') @lang('lang.details')</h4>
                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    </div>

                                                                {{-- Incomplete Task Modal --}}


                                                    <div class="modal-body">
                                                        <div class="text-center">
                                                            <h4>{{ @$task->title }}</h4>
                                                        </div>
                                                        <hr>

                                                        <div class="row">
                                                            <div class="col-lg-4">
                                                                    <div class="team_member_slider">
                                                                    <a target="_blank" href="{{route('viewStaff', @$team_member->staff_id)}}" title="{{ @$team_member->full_name }}"> <img class="team-image" src="{{ asset(@$team_member->staff_photo) }}" width="200" height="200" alt="{{@$team_member->id}}">
                                                                            {{ @$team_member->full_name }}</a>

                                                                </div>
                                                            </div>
                                                            <div class="col-lg-4">

                                                                @if (@$task->due_date < @$current_date)
                                                                <span class="text-danger" ><i class="fa fa-calendar"></i> {{ \Carbon\Carbon::parse(@$task->due_date)->format('jS M, Y') }}</span>

                                                                @else
                                                                 <span class="text-success" >  <i class="fa fa-calendar"></i>
                                                                        {{ \Carbon\Carbon::parse(@$task->due_date)->format('jS M, Y') }}
                                                                    </span>

                                                                @endif
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <li id="imcomplete_task{{ @$task->id }}" class="d-flex">
                                                                    <input id="task{{ @$task->id }}" class="checkbox" type="checkbox" disabled value="{{ @$task->id }}"  @if (@$task->is_complete==1) checked @endif>
                                                                    <label for="task{{ @$task->id }}"></label>@lang('lang.complete')
                                                                </li>
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        <p>{{ @$task->description }}</p>
                                                        @php

                                                                if(!function_exists('showDocumentName')){
                                                                    function showDocumentName($data){
                                                                    $name = explode('/', $data);
                                                                    return $name[3];
                                                                }
                                                                }

                                                            @endphp

                                                                    @php
                                                                        $path = @$task->image;
                                                                        $ext = pathinfo(@$path, PATHINFO_EXTENSION);

                                                                    @endphp

                                                                    @if (@$ext=='jpg' || @$ext=='png' || @$ext=='jpeg')
                                                                     <form action="{{ url('project/download-task-attachment') }}" method="post">
                                                                    <a target="_blank" href="{{ asset(@$task->image)}}" class="avatar avatar-lg">

                                                                     <img src="{{ asset(@$task->image) }}" width="500" height="500" class="img-fluid img-responsive" alt="{{ @$task->title }}">
                                                                    <p class="card-text small text-muted mb-1">

                                                                            @php
                                                                                $size=filesize(@$task->image);
                                                                                $size_mb=@$size/1024;
                                                                                $path = @$task->image;
                                                                                $ext = pathinfo(@$path, PATHINFO_EXTENSION);

                                                                                echo round(@$size_mb,2).' KB  '. strtoupper(@$ext);
                                                                                @endphp
                                                                            </p>
                                                                    </a>
                                                                    @csrf
                                                                            <input type="hidden" name="file_name" value="{{ @$task->image }}">
                                                                           <button class="primary-btn fix-gr-bg" data-toggle="tooltip">
                                                                                <span class="ti-down"></span>
                                                                               @lang('lang.download')
                                                                            </button>

                                                                        </form>


                                                                    @elseif(@$ext=='doc' || @$ext=='docx')

                                                                    <form action="{{ url('project/download-task-attachment') }}" method="post">


                                                                    <a href="#" class="avatar avatar-lg">
                                                                            <img width="100"  height="100" src="{{ asset('public/uploads/doc.png') }}" alt="..." class="avatar-img rounded">

                                                                          <p class="card-text small text-muted mb-1">

                                                                            @php
                                                                                $size=filesize(@$task->image);
                                                                                $size_mb=@$size/1024;
                                                                                $path = @$task->image;
                                                                                $ext = pathinfo(@$path, PATHINFO_EXTENSION);

                                                                                echo round(@$size_mb,2).' KB  '. strtoupper(@$ext);
                                                                                @endphp
                                                                            </p>
                                                                        </a>
                                                                         @csrf
                                                                            <input type="hidden" name="file_name" value="{{ @$task->image }}">
                                                                           <button class="primary-btn fix-gr-bg" data-toggle="tooltip">
                                                                                <span class="ti-down"></span>
                                                                               @lang('lang.download')
                                                                            </button>

                                                                        </form>





                                                                    @elseif($ext=='pdf' )

                                                                    <a href="#" class="avatar avatar-lg">
                                                                            <form action="{{ url('project/download-task-attachment') }}" method="post">


                                                                    <a href="#" class="avatar avatar-lg">
                                                                            <img width="100"  height="100" src="{{ asset('public/uploads/pdf.png') }}" alt="..." class="avatar-img rounded">

                                                                          <p class="card-text small text-muted mb-1">

                                                                            @php
                                                                                $size=filesize(@$task->image);
                                                                                $size_mb=@$size/1024;
                                                                                $path = @$task->image;
                                                                                $ext = pathinfo(@$path, PATHINFO_EXTENSION);

                                                                                echo round(@$size_mb,2).' KB  '. strtoupper(@$ext);
                                                                                @endphp
                                                                            </p>
                                                                        </a>
                                                                         @csrf
                                                                            <input type="hidden" name="file_name" value="{{ @$task->image }}">
                                                                           <button class="primary-btn fix-gr-bg" data-toggle="tooltip">
                                                                                <span class="ti-down"></span>
                                                                               @lang('lang.download')
                                                                            </button>

                                                                        </form>
                                                                    @elseif($ext=='txt' )
                                                                    <a href="#" class="avatar avatar-lg">
                                                                            <form action="{{ url('project/download-task-attachment') }}" method="post">


                                                                    <a href="#" class="avatar avatar-lg">
                                                                            <img width="100"  height="100" src="{{ asset('public/uploads/txt.png') }}" alt="..." class="avatar-img rounded">

                                                                          <p class="card-text small text-muted mb-1">

                                                                            @php
                                                                                $size=filesize(@$task->image);
                                                                                $size_mb=@$size/1024;
                                                                                $path = @$task->image;
                                                                                $ext = pathinfo(@$path, PATHINFO_EXTENSION);

                                                                                echo round(@$size_mb,2).' KB  '. strtoupper(@$ext);
                                                                                @endphp
                                                                            </p>
                                                                        </a>
                                                                         @csrf
                                                                            <input type="hidden" name="file_name" value="{{ @$task->image }}">
                                                                           <button class="primary-btn fix-gr-bg" data-toggle="tooltip">
                                                                                <span class="ti-down"></span>
                                                                               @lang('lang.download')
                                                                            </button>

                                                                        </form>
                                                                    @elseif(@$ext=='zip' )
                                                                    <a href="#" class="avatar avatar-lg">
                                                                             <form action="{{ url('project/download-task-attachment') }}" method="post">


                                                                    <a href="#" class="avatar avatar-lg">
                                                                            <img width="100"  height="100" src="{{ asset('public/uploads/zip.png') }}" alt="..." class="avatar-img rounded">

                                                                          <p class="card-text small text-muted mb-1">

                                                                            @php
                                                                                $size=filesize(@$task->image);
                                                                                $size_mb=@$size/1024;
                                                                                $path = @$task->image;
                                                                                $ext = pathinfo(@$path, PATHINFO_EXTENSION);

                                                                                echo round(@$size_mb,2).' KB  '. strtoupper(@$ext);
                                                                                @endphp
                                                                            </p>
                                                                        </a>
                                                                         @csrf
                                                                            <input type="hidden" name="file_name" value="{{ @$task->image }}">
                                                                           <button class="primary-btn fix-gr-bg" data-toggle="tooltip">
                                                                                <span class="ti-down"></span>
                                                                               @lang('lang.download')
                                                                            </button>

                                                                        </form>
                                                                      @endif


                                                        <div class="mt-40 d-flex justify-content-between float-right">
                                                            <div class="float-right">
                                                              <button type="button" class="primary-btn tr-bg" data-dismiss="modal">@lang('lang.cancel')</button>

                                                            </div>

                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </ul>
                                <hr>
                             @endforeach
                    </div>

                    </div>
                    </div>
        </div>
        </div>
    </div>
</section>
<script src="{{ asset('/') }}Modules/Project/Resources/assets/js/task/task.js"></script>
 
@endsection

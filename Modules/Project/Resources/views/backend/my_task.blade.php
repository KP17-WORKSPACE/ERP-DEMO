@extends('backEnd.master')
@section('mainContent')

    <link rel="stylesheet" href="{{ url('/') }}/Modules/Project/Resources/assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="{{ url('/') }}/Modules/Project/Resources/assets/css/magnific-popup.css">
    <link rel="stylesheet" href="{{ url('/') }}/Modules/Project/Resources/assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{ url('/') }}/Modules/Project/Resources/assets/css/themify-icons.css">
    <link rel="stylesheet" href="{{ url('/') }}/Modules/Project/Resources/assets/css/nice-select.css">
    <link rel="stylesheet" href="{{ url('/') }}/Modules/Project/Resources/assets/css/animate.css">
    <link rel="stylesheet" href="{{ url('/') }}/Modules/Project/Resources/assets/css/material-components-web.min.css">
    <link rel="stylesheet" href="{{ url('/') }}/Modules/Project/Resources/assets/css/slicknav.css">
    <link rel="stylesheet" href="{{ url('/') }}/Modules/Project/Resources/assets/css/icon.css">
    <link rel="stylesheet" href="{{ url('/') }}/Modules/Project/Resources/assets/css/style.css">
    <link rel="stylesheet" href="{{ url('/') }}/Modules/Project/Resources/assets/css/task/task.css">
    <link rel="stylesheet" href="{{ url('/') }}/Modules/Project/Resources/assets/css/team.css">
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('lang.project') @lang('lang.task')</h1>
            <div class="bc-pages">
               <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                <a href="#">@lang('lang.project') @lang('lang.management')</a>
                <a href="#">@lang('lang.task')</a>
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area ">
    <div class="container-fluid p-0">

        <div class="row">
           @php
                $current_date=date('Y-m-d H:i:s');
           @endphp

               <div class="col-lg-12">
                  <div class="student-details">
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

                                </h3>
                            </div>

                            @foreach ($projects as $project)
                            {{-- {{ dd($project) }} --}}
                            <div class="row">
                                <div class="col-lg-6">
                                <div class="team_member_slider">
                                    <a  href="#" > <img class="team-image" src="{{ file_exists(@$project->photo) ? asset($project->photo) : asset('public/uploads/projects/project.png') }}" width="200" height="200" >
                                        {{ @$project->name }}</a>

                            </div>
                                </div>
                                <div class="col-lg-6">
                                     @lang('lang.due') @lang('lang.date') :
                                            @if (@$project->due_date < @$current_date)

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

                            @php
                                $member_im_tasks=DB::table('infix_project_tasks')
                                ->where('assigned_to','=',$staff->id)
                                ->where('is_complete','=',0)
                                ->where('project_id','=',$project->id)
                                ->orderBy('due_date', 'asc')
                                ->get();
                                    @$current_date=date('m/d/Y');
                           @endphp


                                <ul id="list-items">

                                @foreach($member_im_tasks as $task)


                                <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">

                                <li id="imcomplete_task{{ @$task->id }}" class="task_list_project">
                                    <input class="checkbox" type="checkbox" onclick="taskComplete({{ @$task->id }})" value="{{ @$task->id }}" id="{{ @$task->id }}" @if (@$task->is_complete==1) checked @endif>
                                    <label for="checkbox"></label>

                                    @if (@$task->due_date < @$current_date)
                                    <a data-toggle="modal" data-target="#viewTask{{@$task->id}}"  href="#">
                                        {{@$task->title}} -  <span class="text-danger" >{{ \Carbon\Carbon::parse(@$task->due_date)->format('jS M, Y') }}</span> </a>
                                     @else
                                      <a data-toggle="modal" data-target="#viewTask{{@$task->id}}"  href="#">
                                        {{@$task->title}} -  <span class="text-success" >
                                            {{ \Carbon\Carbon::parse(@$task->due_date)->format('jS M, Y') }}
                                        </span>
                                    </a>
                                    @endif


                                </li>

                                            <div class="modal fade admin-query" id="viewTask{{@$task->id}}" >
                                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">@lang('lang.task') @lang('lang.details')</h4>
                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    </div>
                                                    <hr>
                                                    <div class="modal-body">
                                                    <div class="text-center">
                                                        <h4>{{ @$task->title }}</h4>
                                                    </div>
                                                        <hr>

                                                        <div class="row">
                                                            <div class="col-lg-4">
                                                                    <div class="team_member_slider">
                                                                        <a  href="#" > <img class="team-image" src="{{ file_exists(@$project->photo) ? asset($project->photo) : asset('public/uploads/projects/project.png') }}" width="200" height="200" >
                                                                            {{ @$project->name }}</a>

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
                                                                        $path = @$task->image;
                                                                        $ext = pathinfo(@$path, PATHINFO_EXTENSION);

                                                                    @endphp

                                                                    @if (@$ext=='jpg' || @$ext=='png' || @$ext=='jpeg')
                                                                     <form action="{{ url('project/download-task-attachment') }}" method="post">
                                                                    <a target="_blank" href="{{ asset(@$task->image)}}" class="avatar avatar-lg">

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

                                                                <a href="#" class="avatar avatar-lg">
                                                                        <form action="{{ url('project/download-task-attachment') }}" method="post">


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

                                                                <a href="#" class="avatar avatar-lg">
                                                                        <img width="100"  height="100" src="{{ asset('public/uploads/txt.png') }}" alt="..." class="avatar-img rounded">

                                                                    <p class="card-text small text-muted mb-1">

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



                                                        <div class="mt-40 d-flex justify-content-between float-right">

                                                           
                                                             <button type="button" class="primary-btn tr-bg " data-dismiss="modal">@lang('lang.cancel')</button>

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
                           
                              @foreach ($projects as $project)
                               <div class="todo_heading">
                                <h3>
                                    {{ @$project->name }}
                                </h3>
                            </div>
                                  <div class="team_member_slider">
                                    <a  href="#" > <img class="team-image" src="{{ file_exists(@$project->photo) ? asset($project->photo) : asset('public/uploads/projects/project.png') }}" width="200" height="200" >
                                        {{ @$project->name }}</a>
                            </div>

                            @php
                                $member_c_tasks=DB::table('infix_project_tasks')
                                ->where('assigned_to','=',@$staff->id)
                                ->where('is_complete','=',1)
                                ->where('project_id','=',@$project->id)
                                ->orderBy('due_date', 'asc')
                                ->get();
                            @endphp

                              <ul id="list-items" class="complete_task_list{{ @$project->id }}">

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
                                                                        <a  href="#" > <img class="team-image" src="{{ file_exists(@$project->photo) ? asset($project->photo) : asset('public/uploads/projects/project.png') }}" width="200" height="200" >
                                                                            {{ @$project->name }}</a>

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


                                                                    @elseif(@$ext=='doc' || $ext=='docx')

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





                                                                    @elseif(@$ext=='pdf' )

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
<script src="{{ asset('/') }}Modules/Project/Resources/assets/js/task/my_task.js"></script>
 

@endsection

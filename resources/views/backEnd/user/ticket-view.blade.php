@extends('backEnd.master')
@section('mainContent')
<link rel="stylesheet" href="{{asset('/public/css')}}/ticket_view.css">
@php
function showPicName($data){
    $name = explode('/', $data);
    return $name[3];
}
@endphp
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('lang.ticket_system')</h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                @if (Auth::user()->role_id == 7)
                <a href="{{ route('user.ticket') }}">@lang('lang.ticket_system')</a>
                @endif
                @if (Auth::user()->role_id != 7)
                <a href="{{ route('admin.ticket_list') }}">@lang('lang.ticket_system')</a>
                @endif
                <a href="#">@lang('lang.ticket_system') @lang('lang.view')</a>
            </div>
        </div>
    </div>
</section>

<style>
    .ticket_admin_edit{
        vertical-align: inherit;
    }
    

</style>

<section class="admin-visitor-area">
    <div class="container-fluid p-0">
            <div class="row mt-40">
                <div class="col-lg-12 mt-20 text-right">
                  @if (Auth::user()->role_id != 7)
                  <span class="pull-right"><a href="{{ route('admin.ticket_edit',@$data->id)}}" class="primary-btn small fix-gr-bg"><font class="ticket_admin_edit">@lang('lang.edit')</font></a></span>
                  @else
                  <span class="pull-right"><a href="{{ route('user.reopen_ticket',@$data->id) }}" class=" {{@$data->active_status == 3?'primary-btn small fix-gr-bg':'btn btn-secondary'}}"><font class="ticket_admin_edit">{{@$data->active_status == 3?'Reopen':'Active'}}</font></a></span>
                  @endif  
                </div>
            </div>
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
                </div>
            </div>
            <div class="row mt-0 p-3">
                <div class="col-lg-12 white-box">
                        
                      <div class="row">
                          <div class="col-lg-9">
                               <div class="row">
                                   <div class="col-lg-12">
                                        <h2>{{ @$data->subject }}</h2>
                                   </div>
                                   <div class="col-lg-12">
                                        <p>{{ strip_tags(@$data->description) }}</p>
                                   </div>
                               </div>
                          </div>
                          <style>
                          .ticket_complete{
                              color:#15a000
                          }
                          .ticket_close{
                             color:#ddd
                          }
                          .ticket_priority{
                            color: #e1d200
                          }
                          .ticket_category{
                           color: #7e0099
                          }
                          .ticket_form{
                              display:block !important; margin-top: -44px;
                          }
                          .ticket_username_input{
                              width:30% !important
                          }
                          </style>
                          <div class="col-lg-3">
                                <p> <strong>@lang('lang.owner')</strong>: {{ @$data->user->username }}</p>
                                <p>
                                   <strong>@lang('lang.Status')</strong>: 
                                   @if (@$data->active_status == 0)
                                   <span class="text-danger">@lang('lang.pending')</span>
                                   @endif
                                   @if (@$data->active_status == 1)
                                   <span class="text-danger">@lang('lang.ongoing')</span>
                                   @endif
                                   @if (@$data->active_status == 2)
                                   <span class="ticket_complete">@lang('lang.complete')</span>
                                   @endif
                                   @if (@$data->active_status == 3)
                                   <span class="ticket_close">@lang('lang.close')</span>
                                   @endif
                                    
                                </p>
                                <p>
                                    <strong>@lang('lang.priority')</strong>: 
                                    <span class="ticket_priority">{{ @$data->priority->name}}</span>
                                </p>
                                <p> <strong>@lang('lang.Responsible')</strong>: {{@$data->agent_user?@$data->agent_user->username:'Not assign yet !'}}</p>
                              
                                <p>
                                    <strong>@lang('lang.category')</strong>: 
                                    <span class="ticket_category">
                                        {{ @$data->category->name }}
                                    </span>
                                </p>
                                <p> <strong>@lang('lang.created')</strong>: {{ @$data->created_at ->diffForHumans()}}</p>
                                <p> <strong>@lang('last') @lang('lang.update')</strong>: {{ @$data->updated_at->diffForHumans() }}</p>
                          </div>
                      </div>

                      <div class="">
                             <div class="row">
                                    <input type="hidden" name="id" id="url" value="{{ @$data->id }}">
                                    <div class="col-lg-8">

                                                <div class="infix_form_area">
                                                    <div class="container">
                                                            @if(Auth::user()->role_id == 7)
                                                            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => ['user.comment_store'], 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'search_studentA']) }}
                                                            @else 
                                                            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => ['admin.comment_store'], 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'search_studentA']) }}
                                                           @endif
                                                            <input type="text" hidden value="{{ @$data->id}}" name="id">
                                                            <textarea class="common_text_area {{ $errors->has('comment') ? ' is-invalid' : '' }}" name="comment" id="" cols="50" rows="10" placeholder="Messege.."></textarea>
                                                                <span class="focus-border"></span>
                                                                @if ($errors->has('comment'))
                                                                <span class="invalid-feedback pb-4 ticket_form" role="alert" >
                                                                    <strong>{{ $errors->first('comment') }}</strong>
                                                                </span>
                                                                @endif
                                                            <div class="form_btn d-flex justify-content-between">
                                                                <div class="file_upload">
                                                                    <div class="input-group">
                                                                        <div class="custom-file">
                                                                            <input type="file" class="custom-file-input {{ $errors->has('file') ? ' is-invalid' : '' }}" id="inputGroupFile04"
                                                                                aria-describedby="inputGroupFileAddon04" name="file">
                                                                            <label class="custom-file-label" for="inputGroupFile04">
                                                                                <svg class="upload_file svg-inline--fa fa-paperclip fa-w-14 icon mr-10"
                                                                                    aria-hidden="true" data-prefix="fas" data-icon="paperclip" role="img"
                                                                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"
                                                                                    data-fa-i2svg="">
                                                                                    <path fill="currentColor"
                                                                                        d="M43.246 466.142c-58.43-60.289-57.341-157.511 1.386-217.581L254.392 34c44.316-45.332 116.351-45.336 160.671 0 43.89 44.894 43.943 117.329 0 162.276L232.214 383.128c-29.855 30.537-78.633 30.111-107.982-.998-28.275-29.97-27.368-77.473 1.452-106.953l143.743-146.835c6.182-6.314 16.312-6.422 22.626-.241l22.861 22.379c6.315 6.182 6.422 16.312.241 22.626L171.427 319.927c-4.932 5.045-5.236 13.428-.648 18.292 4.372 4.634 11.245 4.711 15.688.165l182.849-186.851c19.613-20.062 19.613-52.725-.011-72.798-19.189-19.627-49.957-19.637-69.154 0L90.39 293.295c-34.763 35.56-35.299 93.12-1.191 128.313 34.01 35.093 88.985 35.137 123.058.286l172.06-175.999c6.177-6.319 16.307-6.433 22.626-.256l22.877 22.364c6.319 6.177 6.434 16.307.256 22.626l-172.06 175.998c-59.576 60.938-155.943 60.216-214.77-.485z">
                                                                                    </path>
                                                                                </svg>
                                                                                @lang('lang.add') @lang('lang.Attachment')</label>
                                                                                <span class="focus-border"></span>
                                                                                @if ($errors->has('file'))
                                                                                <span class="invalid-feedback" role="alert">
                                                                                    <strong>{{ $errors->first('file') }}</strong>
                                                                                </span>
                                                                                @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <button class="primary-btn small fix-gr-bg" type="submit">@lang('lang.ticket_comment')</button>
                                                            </div>
                                                            
                                                            {{ Form::close() }}
                                                        <div class="comments_public pt-4">
                                                            {{-- coming --}}
                                                            @if (count(@$comment)>0)
                                                            @foreach ($comment as $item)
                                                                @if (!@$item->comment_id)
                                                                @php 
                                                                    $path = @$item->user->staff->staff_photo;
                                                                    if(empty(@$path)){
                                                                    $path = 'public/backEnd/img/client/user.png';
        
                                                                    }
                                                                @endphp
                                                                    <div class="single_comment mb-3">
                                                                        <div class="comments-thumb d-flex">
                                                                        <img src="{{asset($path)}}" alt="" class="img-fluid">
                                                                            <p class="comment-meta"><span><a href="#"> {{ @$item->user->username}}</a></span> <br> <small>{{ @$item->created_at->diffForHumans() }}</small></p>
                                                                        </div>
                                                                        
                                                                        <div class="comments_public-info">
                                                                            @if(Auth::user()->role_id == 7)
                                                                            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => ['user.comment_reply'], 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'search_studentA']) }}
                                                                            @else 
                                                                            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => ['admin.comment_reply'], 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'search_studentA']) }}
                                                                            @endif
                                                                               <p class="pl-4 ml-5 mb-4"><span class="text-justify text-capitalize">{{ strip_tags(@$item->comment) }}</span>
                                                                                
                                                                                @if ($item->file)
                                                                                    @if (exif_imagetype(@$item->file))
                                                                                       <br><img src="{{ asset(@$item->file)}}" class="pt-2 ticket_username_input">
                                                                                    @endif
                                                                                <br> <small><a target="_blank" href="{{url('download-comment-document/'.showPicName(@$item->file))}}"> Download file</a></small>
                                                                                @endif
                                                                               </p>
                                                                               <div class="row">
                                                                                <div class="col-lg-6">
                                                                                </div> 

                                                                                <div class="col-lg-6">
                                                                                    <div id="{{@$item->id}}"></div>
                                                                                </div>
                                                                               </div>
                                                                                
                                                                                <input value="" hidden class=" comment_id"  name="comment_id" id="comment_id">
                                                                                
                                                                            {{ Form::close() }}
                                                                             <button class="primary-btn small fix-gr-bg submit_comment{{@$item->id}}" id="t" type="submit" onclick="submit_comment({{@$item->id}})">@lang('lang.comment_reply')</button>
                                                                        </div>
                                                                    </div>
                                                                    @endif 
                                                            {{-- send --}}
                                                          

                                                           

                                                            @foreach ($comment as $value)
                                                            @if (@$item->id == @$value->comment_id)
                                                            @php 
                                                                $path = @$value->user->staff->staff_photo;
                                                                if(empty(@$path)){
                                                                $path = 'public/backEnd/img/client/user.png';
                                                            }
                                                            @endphp
                                                              <div class="single_comment_replay mb-3">
                                                                    <div class="comments-thumb d-flex">
                                                                    <p class="comment-meta"><span><a href="#"> {{ @$value->user->username}}</a></span> <br> <small>{{ @$value->created_at->diffForHumans() }}</small></p>
                                                                            <img src="{{asset(@$path)}}" alt="" class="img-fluid">
                                                                    </div>
                                                                    <div class="comments_public-info" id="reply_comment">
                                                                        <p class="pr-4 mr-5 mb-3" class="text-right"><span class="text-left text-capitalize">{{ strip_tags(@$value->comment) }}</span>
                                                                                @if (@$value->file)
                                                                                @if (exif_imagetype(@$value->file))
                                                                                       <br><img src="{{ asset(@$value->file)}}" class="pt-2 ticket_username_input" width="200" >
                                                                                    @endif
                                                                                   <br> <small><a target="_blank" href="{{url('download-comment-document/'.showPicName(@$value->file))}}"> Download file</a></small>
                                                                             @endif
                                                                        </p>
                                                                        
                                                                    </div>
                                                                </div>
                                                                
                                                            @endif
                                                            @endforeach
                                                           
                                                            @endforeach 
                                                          @endif 
                                                        </div>
                                                    </div>
                                                </div>  
                                    </div>  
                                </div>
                        </div>
                </div> 
            </div>

    </div>
</section>


@endsection
@section('script')
    <script>
      function submit_comment(id) {
           $('.comment').css("display","none")
           $('#t').css("display","inline")
           $('.displaynone').css("display","inline")
           $('.submit_comment'+id).css("display","none")
           $('.submit_comment'+id).addClass("displaynone")
            $('.comment_id').val(id)
          $('.submit_com').css("display","none")
          var data=$('<textarea class="form-control textarea-reply comment{{ $errors->has('comment') ? ' is-invalid' : '' }}" name="comment" id="" cols="20" rows="3" placeholder="Reply here..."></textarea>'+
                      '<div class="form_btn d-flex justify-content-between"> <div class="file_upload"> <div class="input-group"> <div class="custom-file"> <input type="file" class="custom-file-input {{ $errors->has('file') ? ' is-invalid' : '' }}" id="inputGroupFile04"'+
                      'aria-describedby="inputGroupFileAddon04" name="file"> <label class="custom-file-label" for="inputGroupFile04"> <svg class="upload_file svg-inline--fa fa-paperclip fa-w-14 icon mr-10" aria-hidden="true" data-prefix="fas" data-icon="paperclip" role="img"'+
                       'xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg=""> <path fill="currentColor"'+
                       'd="M43.246 466.142c-58.43-60.289-57.341-157.511 1.386-217.581L254.392 34c44.316-45.332 116.351-45.336 160.671 0 43.89 44.894 43.943 117.329 0 162.276L232.214 383.128c-29.855 30.537-78.633 30.111-107.982-.998-28.275-29.97-27.368-77.473 1.452-106.953l143.743-146.835c6.182-6.314 16.312-6.422 22.626-.241l22.861 22.379c6.315 6.182 6.422 16.312.241 22.626L171.427 319.927c-4.932 5.045-5.236 13.428-.648 18.292 4.372 4.634 11.245 4.711 15.688.165l182.849-186.851c19.613-20.062 19.613-52.725-.011-72.798-19.189-19.627-49.957-19.637-69.154 0L90.39 293.295c-34.763 35.56-35.299 93.12-1.191 128.313 34.01 35.093 88.985 35.137 123.058.286l172.06-175.999c6.177-6.319 16.307-6.433 22.626-.256l22.877 22.364c6.319 6.177 6.434 16.307.256 22.626l-172.06 175.998c-59.576 60.938-155.943 60.216-214.77-.485z">'+
                       '</path> </svg> Add Attachment</label> </div> </div> </div> <button class="primary-btn small fix-gr-bg submit_com current" type="submit">@lang('lang.comment_reply')</button> </div>')
          $(this).css("display","block")
          for (let index = 0; index < 1; index++) {
          $('#'+id).append(data)
          }
          
          
      }
    </script>
     <script> 
           $( document ).ready(function() {
               var a = $('.linkk').data("id"); 
               if (a) {
                $.ajax({
                    headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                    type: 'post',
                    url: "{{ route('admin.ticket_show')}}",
                    data: {
                        id:a
                    },
                    dataType : 'json',
                    success: function(data) {
                       
                    }
                });
               }
            });
           </script>
@endsection

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
            <h1>@lang('lang.inspecting_departments') </h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                <a href="#">@lang('lang.inspecting_departments')</a> 
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area">
    <div class="container-fluid p-0">
         @if(isset($editData))
        @if(in_array(322, @$module_links) ||  Auth::user()->role_id == 1)
        <div class="row">
            <div class="offset-lg-10 col-lg-2 text-right col-md-12 mb-20">
                <a href="{{url('suppliers')}}" class="primary-btn small fix-gr-bg">
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
                            <h3 class="mb-30">@if(isset($editData))
                                    @lang('lang.edit')
                                @else
                                    @lang('lang.add')
                                @endif
                                 @lang('lang.new')
                            </h3>
                        </div>
                        @if(isset($editData))
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'inspecting-department/'.@$editData->id, 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}
                        @else
                        @if(in_array(322, @$module_links) ||  Auth::user()->role_id == 1)
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'inspecting-department','method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                        @endif
                        @endif
                        <div class="white-box">
                            <div class="add-visitor">
                                <div class="row">
                                    @if(session()->has('message-success'))
                                    <div class="alert alert-success mb-20">
                                        {{ session()->get('message-success') }}
                                    </div>
                                    @elseif(session()->has('message-danger'))
                                    <div class="alert alert-danger">
                                        {{ session()->get('message-danger') }}
                                    </div>
                                    @endif
                                   <div class="col-lg-12 mb-30">
                                        <div class="input-effect">
                                            <input class="primary-input form-control{{ $errors->has('department_name') ? ' is-invalid' : '' }}"
                                            type="text" name="department_name" autocomplete="off" value="{{isset($editData)? @$editData->department_name : old('department_name') }}">
                                            <label>  @lang('lang.department')  @lang('lang.name') <span>*</span> </label>
                                            <span class="focus-border"></span>
                                            @if ($errors->has('department_name'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('department_name') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                   <div class="col-lg-12 mb-30">
                                        <div class="input-effect">
                                            <input class="primary-input form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                                            type="text" name="name" autocomplete="off" value="{{isset($editData)? @$editData->name : old('name') }}">
                                            <label> @lang('lang.contact_person')  @lang('lang.name') <span>*</span> </label>
                                            <span class="focus-border"></span>
                                            @if ($errors->has('name'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('name') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
 

                                    <div class="col-lg-12 mb-30">
                                        <div class="input-effect">
                                            <input class="primary-input form-control{{ $errors->has('contact_person_mobile') ? ' is-invalid' : '' }}"
                                            type="text" name="contact_person_mobile" autocomplete="off" value="{{isset($editData)? @$editData->phone : old('contact_person_mobile') }}">
                                            <label> @lang('lang.mobile') @lang('lang.number') <span>*</span></label>
                                            <span class="focus-border"></span>
                                            @if ($errors->has('contact_person_mobile'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('contact_person_mobile') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-lg-12 mb-30">
                                        <div class="input-effect">
                                            <input class="primary-input form-control{{ $errors->has('contact_person_email') ? ' is-invalid' : '' }}"
                                            type="text" name="contact_person_email" autocomplete="off" value="{{isset($editData)? @$editData->email : old('contact_person_email') }}">
                                            <label> @lang('lang.email') </label>
                                            <span class="focus-border"></span>
                                            @if ($errors->has('contact_person_email'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('contact_person_email') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-lg-12 mb-30">
                                        <div class="input-effect">
                                            <textarea class="primary-input form-control" cols="0" rows="4" name="description" id="description">{{isset($editData) ? @$editData->description : old('description')}}</textarea>
                                            <label>@lang('lang.description') <span></span> </label>
                                            <span class="focus-border textarea"></span>

                                        </div>
                                    </div>

                                </div>
                                @php 
                                  $tooltip = "";
                                   if(in_array(322, @$module_links) ||  Auth::user()->role_id == 1){
                                        $tooltip = "";
                                    }else{
                                        $tooltip = "You have no permission to add";
                                    }
                                @endphp
                                <div class="row mt-40">
                                    <div class="col-lg-12 text-center">
                                        <button class="primary-btn fix-gr-bg" data-toggle="tooltip" title="{{$tooltip}}">
                                            <span class="ti-check"></span>
                                            @if(isset($editData))
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
                        <h3 class="mb-0"> @lang('lang.inspecting_departments') </h3>
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
                                    <td colspan="8">
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
                                <th> @lang('lang.sl') </th>
                                <th> @lang('lang.department') @lang('lang.name') </th> 
                                <th> @lang('lang.name')</th>
                                <th> @lang('lang.email')</th>
                                <th> @lang('lang.mobile')</th> 
                                <th> @lang('lang.action')</th>
                            </tr>
                        </thead>

                        <tbody>
                            @if(isset($InspectingDepartment))
                            @php $listCount=1; @endphp
                            @foreach($InspectingDepartment as $value)
                            <tr>

                                <td>{{@$listCount++}}</td>
                                <td>{{@$value->department_name}}</td> 
                                <td>{{@$value->name}}</td> 
                                <td>{{@$value->email}}</td>
                                <td>{{@$value->phone}}</td>  
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">
                                            @lang('lang.select')
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right"> 
                                            <a class="dropdown-item" href="{{url('inspecting-department/'.@$value->id.'/edit')}}"> @lang('lang.edit')</a> 
                                            <a class="deleteUrl dropdown-item" data-modal-size="modal-md" title="Delete Inspecting Department" href="{{url('delete-inspecting-department-view/'.@$value->id)}}"> @lang('lang.delete')</a> 
                                        </div>
                                    </div>
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
</div>
</section>
@endsection

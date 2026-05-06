@extends('backEnd.master')
@section('mainContent')
    @php
    function showPicName($data){
    $name = explode('/', $data);
    return $name[3];
    }


    @endphp
    <link href="{{ asset('public/css/add_staff.css') }}" type="text/css" rel="stylesheet">

    @php
    $modules = [];
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();

    foreach($permissions as $permission){ @$module_links[] = @$permission->module_link_id; @$modules[] =
    @$permission->moduleLink->module_id;}

    $modules = array_unique(@$modules);
    @endphp
    <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">

                @if(isset($editData))
                    <h1>@lang('Account Type') @lang('Edit')</h1>
                @else
                    <h1>@lang('Account Type') @lang('Add')</h1>
                @endif

                <div class="bc-pages">
                    <a href="{{ url('dashboard') }}">@lang('lang.dashboard')</a>
                    <a href="#">@lang('Account Type')</a>

                    @if(isset($editData))
                    <a href="#">@lang('Account Type Edit')</a>
                    @else
                    <a href="#">@lang('Account Type Add')</a>
                    @endif


                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-9 col-md-9 col-sm-6 col-xs-12">
                    <div class="main-title">
                        <h3 class="mb-30">  @lang('Account Type Information')</h3>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 text-right">
                    <a href="{{url('accounttype-add')}}" class="primary-btn small fix-gr-bg">
                         @lang('Add')
                    </a>
                    @if(isset($editData))
                        {{-- <a href="{{url('company',@$editData->id)}}" class="primary-btn small fix-gr-bg">  @lang('lang.view') </a> --}}
                    @endif
                </div>

            </div>



            @if(isset($editData))
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'accounttype-update/'. @$editData->id, 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}
            @else
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'accounttype-store', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
            @endif

            <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
            <input type="hidden" name="date_of_joining" id="date_of_joining" value="{{date('Y-m-d')}}">
            <div class="row">
                <div class="col-lg-4">
                  <div class="white-box">
                        <div class="row mb-0">
                            <div class="col-lg-12">
                                <div class="main-title">
                                    <h4>  @lang('lang.basic')   @lang('lang.information')</h4>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-0">
                            <div class="col-lg-12">
                                <hr>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12 mb-20">
                                <div class="input-effect">
                                    <input class="primary-input form-control {{$errors->has('title') ? 'is-invalid' : ' '}}" type="text"  id="title" name="title" value="{{isset($editData)?@$editData->title:old('title')}}" >
                                    <span class="focus-border"></span>
                                    <label>  @lang('Account Type') <span>*</span> </label>
                                    @if ($errors->has('title'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                <!-- Bank Info Details -->

                <!-- end row -->

                <div class="row mt-40">
                    <div class="col-lg-12 text-center">
                        <button class="primary-btn fix-gr-bg" id="btnSubmit">
                            <span class="ti-check"></span>
                            @if(isset($editData)) @lang('lang.update') @else @lang('lang.add') @endif @lang('Account Type')
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <table id="table_id" class="display school-table pl-2" cellspacing="0" width="100%">
                <thead>
                    @if (session()->has('message-success-delete') != '' || session()->get('message-danger-delete') != '')
                        <tr>
                            <td colspan="6">
                                @if (session()->has('message-success-delete'))
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
                        <th> @lang('Account Type')</th>
                        <th> @lang('Status')</th>
                        <th> @lang('lang.action')</th>
                    </tr>
                </thead>

                <tbody>
                    @if (isset($accounttype))
                        @foreach ($accounttype as $value)
                            <tr>
                                <td>
                                    {{ @$value->title }}
                                </td>
                                <td>
                                    @if(@$value->status==1)
                                        Active
                                    @else
                                        Inactive
                                    @endif
                                </td>                                
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn dropdown-toggle"
                                            data-toggle="dropdown">
                                            @lang('lang.select')
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            @if (in_array(163, @$module_links) || Auth::user()->role_id == 1)
                                                <a class="dropdown-item"
                                                    href="{{ url('accounttype/' . @$value->id . '/edit') }}">
                                                    @lang('lang.edit')</a>

                                            @endif
                                            {{-- @if (in_array(164, @$module_links) || Auth::user()->role_id == 1)

                                                <a class="deleteUrl dropdown-item"
                                                    data-modal-size="modal-md" title="Delete Company"
                                                    href="{{ url('delete-company/' . @$value->id) }}">
                                                    @lang('lang.delete')</a>
                                            @endif --}}

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
    {{ Form::close() }}
    </div>
</section>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            $("#btnSubmit").click(function () {
                setTimeout(function () { disableButton(); }, 0);
            });
            function disableButton() {
                $("#btnSubmit").prop('disabled', true);
            }
        });
    </script>
@endsection
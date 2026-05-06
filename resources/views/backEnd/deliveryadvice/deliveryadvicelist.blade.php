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
    {{-- <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">

                @if(isset($editData))
                    <h1>@lang('Account') @lang('Edit')</h1>
                @else
                    <h1>@lang('Account') @lang('Add')</h1>
                @endif

                <div class="bc-pages">
                    <a href="{{ url('dashboard') }}">@lang('lang.dashboard')</a>
                    <a href="#">@lang('Accounts')</a>

                    @if(isset($editData))
                    <a href="#">@lang('Account Edit')</a>
                    @else
                    <a href="#">@lang('Account Add')</a>
                    @endif


                </div>
            </div>
        </div>
    </section> --}}

    <section class="admin-visitor-area">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-9 col-md-9 col-sm-6 col-xs-12">
                    <div class="main-title">
                        <h3 class="mb-30">  @lang('Delivery Advice')</h3>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 text-right">
                    <a href="{{url('delivery-advice-add')}}" class="primary-btn small fix-gr-bg">
                         @lang('Add')
                    </a>
                    @if(isset($editData))
                        {{-- <a href="{{url('company',@$editData->id)}}" class="primary-btn small fix-gr-bg">  @lang('lang.view') </a> --}}
                    @endif
                </div>

            </div>




            <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
            <input type="hidden" name="date_of_joining" id="date_of_joining" value="{{date('Y-m-d')}}">
            <div class="row">
        <div class="col-lg-12">
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
                        <th> @lang('Doc Number')</th>
                        <th> @lang('Doc Date')</th>
                        <th> @lang('Created By')</th>
                        <th> @lang('lang.action')</th>
                    </tr>
                </thead>

                <tbody>
                    @if (isset($journalvoucher))
                        @foreach ($journalvoucher as $value)
                            <tr>
                                <td><a href="{{ url('sales-return/' . @$value->id) }}">{{ @$value->doc_number }}</a>
                                </td>
                                <td>
                                    {{ @$value->doc_date }}
                                </td>
                                <td>
                                    {{ @$value->createdby->full_name }}
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
                                                    href="{{ url('sales-return/' . @$value->id . '/edit') }}">
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
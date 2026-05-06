@extends('backEnd.master')
@section('mainContent')
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
                <h1>@lang('lang.supplier') @lang('lang.list')</h1>
                <div class="bc-pages">
                    <a href="{{ url('dashboard') }}">@lang('lang.dashboard')</a>
                    <a href="#">@lang('lang.inventory')</a>
                    <a href="#">@lang('lang.supplier') @lang('lang.list')</a>
                </div>
            </div>
        </div>
    </section> --}}
    <section class="admin-visitor-area">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-9 col-md-9 col-sm-6 col-xs-12">
                    <div class="main-title">
                        <h3 class="mb-30">Supplier List</h3>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 text-right">
                    @if (isset($editData))
                    <a href="{{ url('suppliers') }}" class="primary-btn small fix-gr-bg">
                        <span class="ti-plus pr-2"></span>
                        @lang('lang.add')
                    </a>
                    @endif
                    <a href="{{url('add-supplier')}}" class="primary-btn small fix-gr-bg">
                        <span class="ti-plus pr-2"></span>
                        @lang('lang.new') @lang('Supplier')
                    </a>
                </div>      
            </div>
           

            <div class="row">
                
                <div class="col-lg-12">


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
                                        <th> @lang('lang.supplier') @lang('lang.code')</th>
                                        <th> @lang('lang.supplier') @lang('lang.name')</th>
                                        <th> @lang('Contact Number')</th>
                                        <th> @lang('Email')</th>
                                        <th> @lang('lang.action')</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @if (isset($suppliers))
                                        @foreach ($suppliers as $value)
                                            <tr>
                                                <td>{{ @$value->supplier_code }}</td>
                                                <td>{{ @$value->supplier_name }}</td>
                                                <td>{{ @$value->contact_person_mobile }}</td>
                                                <td>{{ @$value->contact_person_email }}</td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button type="button" class="btn dropdown-toggle"
                                                            data-toggle="dropdown">
                                                            @lang('lang.select')
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            @if (in_array(163, @$module_links) || Auth::user()->role_id == 1)
                                                                <a class="dropdown-item"
                                                                    href="{{ url('suppliers/' . @$value->id . '/edit') }}">
                                                                    @lang('lang.edit')</a>

                                                            @endif
                                                            @if (in_array(164, @$module_links) || Auth::user()->role_id == 1)

                                                                <a class="deleteUrl dropdown-item"
                                                                    data-modal-size="modal-md" title="Delete Supplier"
                                                                    href="{{ url('delete-supplier-view/' . @$value->id) }}">
                                                                    @lang('lang.delete')</a>
                                                            @endif

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

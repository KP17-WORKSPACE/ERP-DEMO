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
    <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('Purchase Order') @lang('lang.list')</h1>
                <div class="bc-pages">
                    <a href="{{ url('dashboard') }}">@lang('lang.dashboard')</a>
                    <a href="#">@lang('Purchase Order')</a>
                    <a href="#">@lang('Purchase Order List')</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area">
        <div class="container-fluid p-0">
            @if (in_array(162, @$module_links) || Auth::user()->role_id == 1)
                    <div class="row">
                        <div class="offset-lg-10 col-lg-2 text-right col-md-12 mb-20">
                            <a href="{{ url('purchase-order-add') }}" class="primary-btn small fix-gr-bg">
                                <span class="ti-plus pr-2"></span>
                                @lang('New Purchase Order')
                            </a>
                        </div>
                    </div>
            @endif

            <div class="row">
                
                <div class="col-lg-12">

                    <div class="row">
                        <div class="col-lg-4 no-gutters">
                            <div class="main-title">
                                <h3 class="mb-0"> @lang('Purchase Order') @lang('lang.list')</h3>
                            </div>
                        </div>
                    </div>

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
                                        <th> @lang('Purchase Order') @lang('lang.name')</th>
                                        <th> @lang('Number')</th>
                                        <th> @lang('')</th>
                                        <th> @lang('')</th>
                                        <th> @lang('lang.action')</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @if (isset($company))
                                        @foreach ($company as $value)
                                            <tr>

                                                <td>{{ @$value->company_name }}</td>
                                                <td>{{ @$value->vat_number }}<br />
                                                    TL No : {{ @$value->trade_license_no }}</td>
                                                <td>{{ @$value->email }}<br />
                                                    Tel : {{ @$value->telephone }}<br />
                                                    Mob : {{ @$value->mobile }}</td>
                                                <td>{{ @$value->company_address }}</td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button type="button" class="btn dropdown-toggle"
                                                            data-toggle="dropdown">
                                                            @lang('lang.select')
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            @if (in_array(163, @$module_links) || Auth::user()->role_id == 1)
                                                                <a class="dropdown-item"
                                                                    href="{{ url('company/' . @$value->id . '/edit') }}">
                                                                    @lang('lang.edit')</a>

                                                            @endif
                                                            @if (in_array(164, @$module_links) || Auth::user()->role_id == 1)

                                                                <a class="deleteUrl dropdown-item"
                                                                    data-modal-size="modal-md" title="Delete Company"
                                                                    href="{{ url('delete-company/' . @$value->id) }}">
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

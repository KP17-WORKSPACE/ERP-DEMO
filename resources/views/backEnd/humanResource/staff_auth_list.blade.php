@extends('backEnd.newmasterpage')
@section('mainContent')
    @php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp

    <div class="content-container col-12">
        <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
            <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
                <div class="purchase-order-content-header">
                    <h4 class="purchase-order-content-header-left">
                        User Authentication List
                    </h4>
                    <div class="purchase-order-content-header-right">
                        <a class="btn btn-light" href="{{ url('staff-directory') }}">
                            User List
                        </a>

                    </div>
                </div>



                <div class="card mb-3">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="long-list" style="table-layout: fixed;width:100%">
                                <thead>
                                    <tr>
                                        <th style="width: 80px;">@lang('User') @lang('lang.no')</th>
                                        <th>@lang('lang.name')</th>
                                        <th>@lang('lang.role')</th>

                                        <th>@lang('lang.department')</th>
                                        <th>@lang('lang.designation')</th>
                                        <th>@lang('lang.mobile')</th>
                                        <th>@lang('lang.email')</th>
                                        <th class="text-center" style="width:70px">@lang('lang.status')</th>
                                        <th class="text-center" style="width: 140px;">@lang('lang.action')</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @if (count($data) > 0)
                                        @foreach ($data as $value)
                                            <tr id="{{ $value->id }}">
                                                <td> <a href="{{ route('viewStaff', @$value->id) }}">
                                                        {{ @$value->staff_no }} </a> </td>
                                                <td>{{ @$value->first_name }}&nbsp;{{ @$value->last_name }}</td>
                                                <td>{{ !empty(@$value->roles->name) ? @$value->roles->name : '' }}</td>
                                                <td>{{ @$value->departments != '' ? @$value->departments->name : '' }}</td>
                                                <td>{{ @$value->designations != '' ? @$value->designations->title : '' }}
                                                </td>
                                                <td>{{ @$value->mobile }}</td>
                                                <td>{{ @$value->email }}</td>
                                                <td class="text-center">
                                                    @if (@$value->active_status == 1)
                                                        <i class="ico icon-outline-check-read text-success"
                                                            aria-hidden="true"></i>
                                                    @else
                                                        <i class="ico icon-outline-close text-danger"
                                                            aria-hidden="true"></i>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex justify-content-center">
                                                        @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
                                                            <a class="btn btn-sm btn-light text-dark"
                                                                href="{{ route('staff-auth-approve', @$value->id) }}"> <i
                                                                    class="ico icon-outline-check-square text-success"
                                                                    style="font-size:16px"></i> Approve</a>
                                                        @endif
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
    </div>
@endsection

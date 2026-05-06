@extends('backEnd.master')


@section('mainContent')
@php
    $modules = [];
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    foreach($permissions as $permission){ @$module_links[] = @$permission->module_link_id; @$modules[] = @$permission->moduleLink->module_id;}
    $modules = array_unique(@$modules);


    $generalSetting=App\SmGeneralSettings::where('id',1)->first();
    $currency_symbol = @$generalSetting->currency_symbol;

    if(isset($generalSetting->logo)){  @$logo = @$generalSetting->logo;  }
    else{ @$logo = 'public/uploads/settings/logo.png'; }
@endphp

<section class="sms-breadcrumb mb-20 white-box">
    <div class="container-fluid">
        <div class="row" style="float: left;">
            <h1>@lang('My Dashboard')</h1>
        </div>
            <div class="row" style="float: right;">
                <a href="javascript:location.reload();" class="top-btn-r-nobar"><i class="far fa fa-refresh" aria-hidden="true"></i> Refresh</a>
            {{-- <div class="bc-pages">
                <a href="{{ url('dashboard') }}">@lang('lang.dashboard')</a>
                <a href="{{ url('purchase-invoice') }}">@lang('Purchase Invoice')</a>
                <a href="{{ url('purchase-invoice/create') }}" class="active">@lang('lang.create')</a>
            </div> --}}
        </div>
    </div>
</section>
<hr style="margin-top: 33px;"/>
    <div style="clear: both;"></div>
    
    <section class="sms-breadcrumb mb-20 white-box top-2">
        <div class="container-fluid">
            <div class="row">
                <div class="col-4">{{-- <img src="{{ asset($company->company_logo) }}" class="tender-create-logo">--}}
                </div>
                <div class="col-8" style="text-align: right;">
                    <div class="top-2-text top-2-text-last"><span>{{ Auth::user()->full_name }}</span><br />Owner</div>
                </div>
                {{-- <div class="bc-pages">
                    <a href="{{ url('dashboard') }}">@lang('lang.dashboard')</a>
                    <a href="{{ url('purchase-invoice') }}">@lang('Purchase Invoice')</a>
                    <a href="{{ url('purchase-invoice/create') }}" class="active">@lang('lang.create')</a>
                </div> --}}
            </div>
        </div>
    </section>

    <section class="mb-40 up_dashboard">
        <div class="row">
            <div class="col-lg-4 mb-20">
                <div class="boxed-formctrl">
                    <h5>Top Sales</h5>
                    <hr class="w-100"/>
                    <br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
                    {{--  <i>12/05/2022</i>
                    <p class="m-0">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                    <b>2500 AED</b>
                    <hr class="w-100"/>
                    <i>12/05/2022</i>
                    <p class="m-0">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                    <b>2500 AED</b>
                    <hr class="w-100"/>
                    <i>12/05/2022</i>
                    <p class="m-0">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                    <b>2500 AED</b>  --}}
                </div>
            </div>
            <div class="col-lg-4 mb-20">
                <div class="boxed-formctrl">
                    <h5>Total Revenue</h5>
                    <hr class="w-100"/>
                    <br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
                </div>
            </div>
            <div class="col-lg-4 mb-20">
                <div class="boxed-formctrl">
                    <h5>Top Purchease</h5>
                    <hr class="w-100"/>
                    <br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
                    {{--  <i>12/05/2022</i>
                    <p class="m-0">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                    <b>2500 AED</b>
                    <hr class="w-100"/>
                    <i>12/05/2022</i>
                    <p class="m-0">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                    <b>2500 AED</b>
                    <hr class="w-100"/>
                    <i>12/05/2022</i>
                    <p class="m-0">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                    <b>2500 AED</b>  --}}
                </div>
            </div>
        </div>
    </section>


<section class="mb-40 up_dashboard" style="display: none;">
    <div class="container-fluid p-0">
        @section('topContent')
            <h5 class="mb-0" style="color: #7b7b7b;">
                @lang('lang.welcome') {{Auth::user()->full_name}}
            </h5>
        @endsection<br /><br />
        <div class="row">
            <style>            
                .white-box.single-summery:before, .white-box.single-summery:after{min-height:0px;}
                .white-box.single-summery {padding: 5px 20px;}
                .txt-sm{font-size: 11px;}
                .d-block1 a:hover{color:#FFFFFF !important;}
                .bg-skblue{ background: #e3e3e3; box-shadow: none;}            
            </style>
            <div class="col-lg-3 col-sm-4 mb-10 d-block1">
                <a class="d-block" href="{{url('customers')}}">
                    <div class="white-box single-summery dashboard_single_item mt-0 bg-skblue">
                        <div class="d-flex justify-content-between">
                            <div  class="m-0 txt-sm">Customer</div>
                            {{-- <h5 class="m-0 txt-sm gradient-color2">{{App\SmStaff::getNumberCustomer()}}</h5> --}}
                        </div>
                    </div>
                </a>
                <div style="padding: 5px 0 5px 0;"></div>
                <a class="d-block" href="{{url('suppliers')}}">
                    <div class="white-box single-summery dashboard_single_item mt-0 bg-skblue">
                        <div class="d-flex justify-content-between">
                            <div class="m-0 txt-sm">Supplier</div>
                            {{-- <h5 class="m-0 txt-sm gradient-color2">{{App\SmStaff::getNumberVendor()}}</h5> --}}
                        </div>
                    </div>
                </a>                
                <div style="padding: 5px 0 5px 0;"></div>
                <a class="d-block" href="{{url('item-list')}}">
                    <div class="white-box single-summery dashboard_single_item mt-0 bg-skblue">
                        <div class="d-flex justify-content-between">
                            <div class="m-0 txt-sm">Product</div>
                            <h5 class="m-0 txt-sm gradient-color2"></h5>
                        </div>
                    </div>
                </a>
                <div style="padding: 5px 0 5px 0;"></div>
                <a class="d-block" href="{{url('chartofaccounts')}}">
                    <div class="white-box single-summery dashboard_single_item mt-0 bg-skblue">
                        <div class="d-flex justify-content-between">
                            <div class="m-0 txt-sm">Account</div>
                            {{-- <h5 class="m-0 txt-sm gradient-color2">{{App\SysChartofAccounts::count()}}</h5> --}}
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-lg-3 col-sm-4 mb-10 d-block1">
                <a class="d-block" href="{{url('purchase-order')}}">
                    <div class="white-box single-summery dashboard_single_item mt-0 bg-skblue">
                        <div class="d-flex justify-content-between">
                            <div class="m-0 txt-sm">Purchase Order</div>
                            <h5 class="m-0 txt-sm gradient-color2"></h5>
                        </div>
                    </div>
                </a>
                <div style="padding: 5px 0 5px 0;"></div>
                <a class="d-block" href="{{url('purchase-invoice')}}">
                    <div class="white-box single-summery dashboard_single_item mt-0 bg-skblue">
                        <div class="d-flex justify-content-between">
                            <div class="m-0 txt-sm">Purchase Invoice</div>
                            <h5 class="m-0 txt-sm gradient-color2"></h5>
                        </div>
                    </div>
                </a>
                <div style="padding: 5px 0 5px 0;"></div>
                <a class="d-block" href="{{url('purchase-return')}}">
                    <div class="white-box single-summery dashboard_single_item mt-0 bg-skblue">
                        <div class="d-flex justify-content-between">
                            <div class="m-0 txt-sm">Purchase Return</div>
                            <h5 class="m-0 txt-sm gradient-color2"></h5>
                        </div>
                    </div>
                </a>
                <div style="padding: 5px 0 5px 0;"></div>
                <a class="d-block" href="{{url('quotations')}}">
                    <div class="white-box single-summery dashboard_single_item mt-0 bg-skblue">
                        <div class="d-flex justify-content-between">
                            <div class="m-0 txt-sm">Quotation</div>
                            <h5 class="m-0 txt-sm gradient-color2"></h5>
                        </div>
                    </div>
                </a>
            </div>            
            <div class="col-lg-3 col-sm-4 mb-10 d-block1">
                <a class="d-block" href="#">
                    <div class="white-box single-summery dashboard_single_item mt-0 bg-skblue">
                        <div class="d-flex justify-content-between">
                            <div class="m-0 txt-sm">Sales Order</div>
                            <h5 class="m-0 txt-sm gradient-color2"></h5>
                        </div>
                    </div>
                </a>
                <div style="padding: 5px 0 5px 0;"></div>
                <a class="d-block" href="{{url('sales-invoice/create')}}">
                    <div class="white-box single-summery dashboard_single_item mt-0 bg-skblue">
                        <div class="d-flex justify-content-between">
                            <div class="m-0 txt-sm">Sales Invoice</div>
                            <h5 class="m-0 txt-sm gradient-color2"></h5>
                        </div>
                    </div>
                </a>
                <div style="padding: 5px 0 5px 0;"></div>
                <a class="d-block" href="{{url('#')}}">
                    <div class="white-box single-summery dashboard_single_item mt-0 bg-skblue">
                        <div class="d-flex justify-content-between">
                            <div class="m-0 txt-sm">Delivery Note</div>
                            <h5 class="m-0 txt-sm gradient-color2"></h5>
                        </div>
                    </div>
                </a>
                <div style="padding: 5px 0 5px 0;"></div>
                <a class="d-block" href="{{url('#')}}">
                    <div class="white-box single-summery dashboard_single_item mt-0 bg-skblue">
                        <div class="d-flex justify-content-between">
                            <div class="m-0 txt-sm">Sales Return</div>
                            <h5 class="m-0 txt-sm gradient-color2"></h5>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-lg-3 col-sm-4 mb-10 d-block1">
                <a class="d-block" href="#">
                    <div class="white-box single-summery dashboard_single_item mt-0 bg-skblue">
                        <div class="d-flex justify-content-between">
                            <div class="m-0 txt-sm">Bank Payment</div>
                            <h5 class="m-0 txt-sm gradient-color2"></h5>
                        </div>
                    </div>
                </a>
                <div style="padding: 5px 0 5px 0;"></div>
                <a class="d-block" href="#">
                    <div class="white-box single-summery dashboard_single_item mt-0 bg-skblue">
                        <div class="d-flex justify-content-between">
                            <div class="m-0 txt-sm">Cash Payment</div>
                            <h5 class="m-0 txt-sm gradient-color2"></h5>
                        </div>
                    </div>
                </a>
                <div style="padding: 5px 0 5px 0;"></div>
                <a class="d-block" href="{{url('#')}}">
                    <div class="white-box single-summery dashboard_single_item mt-0 bg-skblue">
                        <div class="d-flex justify-content-between">
                            <div class="m-0 txt-sm">Bank Receipt</div>
                            <h5 class="m-0 txt-sm gradient-color2"></h5>
                        </div>
                    </div>
                </a>
                <div style="padding: 5px 0 5px 0;"></div>
                <a class="d-block" href="{{url('#')}}">
                    <div class="white-box single-summery dashboard_single_item mt-0 bg-skblue">
                        <div class="d-flex justify-content-between">
                            <div class="m-0 txt-sm">Cash Receipt</div>
                            <h5 class="m-0 txt-sm gradient-color2"></h5>
                        </div>
                    </div>
                </a>
            </div>
        </div>


        <div class="row">
            {{-- @if(in_array(225, @$module_links) ||  Auth::user()->role_id == 1)
            <div class="col-lg-3 col-sm-6">
                <a class="d-block" href="{{url('sales-invoice/create')}}">
                    <div class="white-box single-summery dashboard_single_item">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h3>
                                    @lang('Create Invoice')
                                </h3>
                                <p class="mb-0">
                                    @lang('lang.total') {{App\SmStaff::getNumberVendor()}} @lang('Invoice')
                                </p>
                            </div>
                            <h1 class="gradient-color2">
                                {{App\SmStaff::getNumberVendor()}}
                            </h1>
                        </div>
                    </div>
                </a>
            </div>
            @endif
            @if(in_array(225, @$module_links) ||  Auth::user()->role_id == 1)
            <div class="col-lg-3 col-sm-6">
                <a class="d-block" href="{{url('purchase-order/create')}}">
                    <div class="white-box single-summery dashboard_single_item">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h3>
                                    @lang('Create PO')
                                </h3>
                                <p class="mb-0">
                                    @lang('lang.total') {{App\SmStaff::getNumberVendor()}} @lang('Purchase Order')
                                </p>
                            </div>
                            <h1 class="gradient-color2">
                                {{App\SmStaff::getNumberVendor()}}
                            </h1>
                        </div>
                    </div>
                </a>
            </div>
            @endif
            @if(in_array(225, @$module_links) ||  Auth::user()->role_id == 1)
            <div class="col-lg-3 col-sm-6">
                <a class="d-block" href="{{url('add-supplier')}}">
                    <div class="white-box single-summery dashboard_single_item">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h3>
                                    @lang('Add Supplier')
                                </h3>
                                <p class="mb-0">
                                    @lang('lang.total') {{App\SmStaff::getNumberVendor()}} @lang('Suppliers')
                                </p>
                            </div>
                            <h1 class="gradient-color2">
                                {{App\SmStaff::getNumberVendor()}}
                            </h1>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            @if(in_array(226, @$module_links) ||  Auth::user()->role_id == 1 )
            <div class="col-lg-3 col-sm-6">
                <a class="d-block" href="{{url('add-customer')}}">
                    <div class="white-box single-summery">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h3>
                                    @lang('Add Customer')
                                </h3>
                                <p class="mb-0">
                                    @lang('lang.total') {{App\SmStaff::getNumberCustomer()}} @lang('lang.customers')
                                </p>
                            </div>
                            <h1 class="gradient-color2">
                                {{App\SmStaff::getNumberCustomer()}}
                            </h1>
                        </div>
                    </div>
                </a>
            </div>
            @endif
                @if(in_array(227, @$module_links) ||  Auth::user()->role_id == 1)
            <div class="col-lg-3 col-sm-6">
                <a class="d-block" href="#">
                    <div class="white-box single-summery">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h3>
                                    @lang('Inventory')
                                </h3>
                                <p class="mb-0">
                                    @lang('lang.total') {{App\SmStaff::getNumberItemStock()}} @lang('Inventory')
                                </p>
                            </div>
                            <h1 class="gradient-color2">
                                {{App\SmStaff::getNumberItemStock()}}
                            </h1>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            @if(in_array(327, @$module_links) || Auth::user()->role_id == 1)
            <div class="col-lg-3 col-sm-6">
                <a class="d-block" href="#">
                    <div class="white-box single-summery">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h3>
                                    @lang('Create Quote')
                                </h3>
                                <p class="mb-0 bank_account_style" >
                                  @lang('lang.total')  {{@$quotations}} @lang('Quote')
                                </p>
                            </div>
                            <h1 class="gradient-color2">
                                {{@$quotations}}
                            </h1>
                        </div>
                    </div>
                </a>
            </div>
            @endif
            
            @if(in_array(327, @$module_links) || Auth::user()->role_id == 1)
            <div class="col-lg-3 col-sm-6">
                <a class="d-block" href="#">
                    <div class="white-box single-summery">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h3>
                                    @lang('Add Receipt')
                                </h3>
                                <p class="mb-0 bank_account_style" >
                                  @lang('lang.total')  {{@$quotations}} @lang('Receipt')
                                </p>
                            </div>
                            <h1 class="gradient-color2">
                                {{@$quotations}}
                            </h1>
                        </div>
                    </div>
                </a>
            </div>
            @endif --}}

            



            {{-- @if(in_array(327, @$module_links) || Auth::user()->role_id == 1)
            <div class="col-lg-3 col-sm-6">
                <a class="d-block" href="">
                    <div class="white-box single-summery">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h3>
                                    @lang('lang.Complete_Projects')
                                </h3>
                                <p class="mb-0 bank_account_style" >
                                    {{ @$complete_project }} @lang('lang.Complete_Projects')
                                </p>
                            </div>
                            <h1 class="gradient-color2">
                                {{ @$complete_project }}
                            </h1>
                        </div>
                    </div>
                </a>
            </div>
            @endif


            @if(in_array(327, @$module_links) || Auth::user()->role_id == 1)
            <div class="col-lg-3 col-sm-6">
                <a class="d-block" href="">
                    <div class="white-box single-summery">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h3>
                                   @lang('lang.Running_Projects')
                                </h3>
                                <p class="mb-0 bank_account_style" >
                                    {{ @$incomplete_project }} @lang('lang.Running_Projects')
                                </p>
                            </div>
                            <h1 class="gradient-color2">
                                {{ @$incomplete_project }}
                            </h1>
                        </div>
                    </div>
                </a>
            </div>
            @endif --}}



        </div>







    </div>
</section>

<section class="mt-50" style="display: none;">
    <div class="container-fluid p-0">
        <div class="row">
            @if(in_array(6, @$module_links) ||  Auth::user()->role_id ==1 )
            @php

                // $upcoming_tenders = App\SmUpcomingTender::where('open_date','>=',date('Y-m-d'))->OrderBy('open_date','ASC')->get();
             @endphp
            <div class="col-lg-12">
                <div class="white-box">
                    <div class="main-title">
                        <h3 class="mb-10">
                            @lang('Sales Report')
                        </h3>
                    </div>


                    <table id="dashboard_table_id" class="p-0 school-table-style w-100 without-box-shadow">
                        <thead>
                            <tr>
                                <th>
                                    @lang('lang.sl')
                                </th>
                                <th nowrap>
                                    @lang('lang.project')    @lang('lang.code')
                                </th>
                                <th>
                                    @lang('lang.project')    @lang('lang.name')
                                </th>
                                <th nowrap>
                                    @lang('lang.due')      @lang('lang.date')
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $count=1; @endphp
                            @foreach($projects as $project)
                            <tr>
                                <td>
                                    {{$count++}}
                                </td>
                                <td nowrap>
                                    <a class="gradient-color2" href="{{url('project/project-task',@$project->id)}}"> {{@$project->code}} </a>
                                </td>
                                <td>
                                    {!! $project->name !!}
                                </td>
                                <td nowrap>
                                    {{date('jS M, Y', strtotime(@$project->due_date))}}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif


        @if(in_array(224, @$module_links) ||  Auth::user()->role_id == 1)
            <div class="col-lg-12 mt-40 ">
                <div class="white-box">
                    <div class="main-title">
                        <h3 class="mb-10">
                            @lang('Bank Book')
                        </h3>
                    </div>
                    <table id="dashboard_table_id" class="p-0 school-table-style w-100 without-box-shadow">
                        <thead>
                            <tr>
                                <th>
                                    @lang('lang.sl')
                                </th>
                                <th>
                                    @lang('lang.name')
                                </th>
                                {{--
                                <th>
                                    @lang('lang.type')
                                </th>
                                --}}
                                <th>
                                    @lang('lang.duration')
                                </th>
                                <th>
                                    @lang('lang.apply_date')
                                </th>
                                <th>
                                    @lang('lang.Status')
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $count=1; @endphp
                        @foreach($apply_leaves as $apply_leave)
                            <tr>
                                <td>
                                    {{$count++}}
                                </td>
                                <td>
                                    <a class="gradient-color2 dropdown-item modalLink" data-modal-size="modal-lg" href="{{url('view-leave-details-approve', @$apply_leave->id)}}" title="View pending Leave Details">
                                        {{@$apply_leave->staffs != ""? @$apply_leave->staffs->full_name:''}}
                                    </a>
                                </td>
                                <td class="d-flex">
                                    {{date('jS M, Y', strtotime(@$apply_leave->leave_from))}} to {{date('jS M, Y', strtotime(@$apply_leave->leave_to))}}
                                </td>
                                <td>
                                    {{date('jS M, Y', strtotime(@$apply_leave->apply_date))}}
                                </td>
                                <td>
                                    @if(@$apply_leave->approve_status == 'P')
                                    <button class="primary-btn small tr-bg">
                                        @lang('lang.pending')
                                    </button>
                                    @endif

                                    @if(@$apply_leave->approve_status == 'A')
                                    <button class="primary-btn small tr-bg">
                                        @lang('lang.approved')
                                    </button>
                                    @endif

                                    @if(@$apply_leave->approve_status == 'C')
                                    <button class="primary-btn small tr-bg">
                                        @lang('lang.cancelled')
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</section>







@if(in_array(7, @$module_links) ||  Auth::user()->role_id == 1)
<!-- Income and Expenses for 2019 -->
<section class="mt-50" id="incomeExpenseDiv" style="display:none;">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-lg-8 col-9">
                <div class="main-title">
                    <h3 class="mb-30">
                        @lang('lang.income_and_expenses_for') {{date('M Y')}}
                    </h3>
                </div>
            </div>
            <div class="offset-lg-2 col-lg-2 text-right col-3 hide_btn">
                <button class="primary-btn small tr-bg icon-only" id="barChartBtn" type="button">
                    <span class="pr ti-move">
                    </span>
                </button>
                <button class="primary-btn small fix-gr-bg icon-only ml-10" id="barChartBtnRemovetn" type="button">
                    <span class="pr ti-close">
                    </span>
                </button>
            </div>
            <div class="col-lg-12">
                <div class="white-box" id="barChartDiv">
                    <div class="row">
                        <div class="col-lg-3 col-sm-6">
                            <div class="text-center">
                                <h1>
                                    {{@$currency_symbol}}{{App\User::NumberToBangladeshiTakaFormat(@$m_total_income)}}
                                </h1>
                                <p>
                                    @lang('lang.total_income')
                                </p>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <div class="text-center">
                                <h1>
                                    {{@$currency_symbol}} {{App\User::NumberToBangladeshiTakaFormat(@$m_total_expense)}}
                                </h1>
                                <p>
                                    @lang('lang.total_expenses')
                                </p>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <div class="text-center">
                                <h1>
                                    {{@$currency_symbol}} {{App\User::NumberToBangladeshiTakaFormat(@$m_total_income - @$m_total_expense)}}
                                </h1>
                                <p>
                                    @lang('lang.total_profit')
                                </p>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <div class="text-center">
                                <h1>
                                    {{@$currency_symbol}} {{App\User::NumberToBangladeshiTakaFormat(@$m_total_income)}}
                                </h1>
                                <p>
                                    @lang('lang.total_revenue')
                                </p>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div id="commonBarChart" class="revenue_chart">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endif





@if(in_array(8, @$module_links) ||  Auth::user()->role_id == 1)
<!-- income Expense -->
<section class="mt-50" id="incomeExpenseSessionDiv" style="display:none;">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-lg-8 col-9">
                <div class="main-title">
                    <h3 class="mb-30">
                        @lang('lang.income_and_expenses_for') {{date('Y')}}
                    </h3>
                </div>
            </div>
            <div class="offset-lg-2 col-lg-2 text-right col-3 hide_btn">
                <button class="primary-btn small tr-bg icon-only" id="areaChartBtn" type="button">
                    <span class="pr ti-move">
                    </span>
                </button>
                <button class="primary-btn small fix-gr-bg icon-only ml-10" id="areaChartBtnRemovetn" type="button">
                    <span class="pr ti-close">
                    </span>
                </button>
            </div>
            <div class="col-lg-12">
                <div class="white-box" id="areaChartDiv">
                    <div class="row">
                        <div class="col-lg-3 col-sm-6">
                            <div class="text-center">
                                <h1>
                                    {{@$currency_symbol}}{{App\User::NumberToBangladeshiTakaFormat(@$y_total_income)}}
                                </h1>
                                <p>
                                    @lang('lang.total_income')
                                </p>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <div class="text-center">
                                <h1>
                                    {{@$currency_symbol}}{{App\User::NumberToBangladeshiTakaFormat(@$y_total_expense)}}
                                </h1>
                                <p>
                                    @lang('lang.total_expenses')
                                </p>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <div class="text-center">
                                <h1>
                                    {{@$currency_symbol}}{{App\User::NumberToBangladeshiTakaFormat(@$y_total_income - @$y_total_expense)}}
                                </h1>
                                <p>
                                    @lang('lang.total_profit')
                                </p>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <div class="text-center">
                                <h1>
                                    {{@$currency_symbol}}{{App\User::NumberToBangladeshiTakaFormat(@$y_total_income)}}
                                </h1>
                                <p>
                                    @lang('lang.total_revenue')
                                </p>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div id="commonAreaChart" class="revenue_chart">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endif



@if(in_array(9, @$module_links) ||  Auth::user()->role_id == 1)
<!-- notice_board -->
<section class="mt-50" style="display:none;">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-6 col-sm-8">
                <div class="main-title">
                    <h3 class="mb-30">
                        @lang('lang.notice_board')
                    </h3>
                </div>
            </div>
            <div class="col-6 col-sm-4 text-right">
                <a href="{{url('create-notice')}}" class="primary-btn small fix-gr-bg up_sm_btn">
                    <span class="ti-plus pr-2"></span>
                    @lang('lang.add') @lang('lang.notice')
                </a>
            </div>
            <div class="col-lg-12">
                <table class="school-table-style w-100">
                    <thead>
                        <tr>
                            <th>
                                @lang('lang.date')
                            </th>
                            <th>
                                @lang('lang.title')
                            </th>
                            <th>
                                @lang('lang.actions')
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php @$role_id = Auth()->
    user()->role_id;?>
                        <?php if (isset($notices)) {
    foreach ($notices as $notice) {
        $inform_to = explode(',', @$notice->
                inform_to);
        if (in_array($role_id, @$inform_to)) {
            ?>
                        <tr>
                            <td>
                                {{date('jS M, Y', strtotime(@$notice->publish_on))}}
                            </td>
                            <td>
                                {{@$notice->notice_title}}
                            </td>
                            <td>
                                <a class="primary-btn small tr-bg modalLink" data-modal-size="modal-lg" href="{{url('view/notice/'.@$notice->id)}}" title="View notice">
                                    @lang('lang.view')
                                </a>
                            </td>
                        </tr>
                        <?php
}
    }
}

?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endif
<!-- calendar -->
<section class="mt-50" style="display: none;">
    <div class="container-fluid p-0">
        <div class="row">
            @if(in_array(10, @$module_links) ||  Auth::user()->role_id == 1 ||  Auth::user()->role_id == 3)
            <div class="col-lg-6 col-xl-6">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-title">
                            <h3 class="mb-30">
                                @lang('lang.calendar')
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="white-box calendar_style">
                            <div class="common-calendar">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if(in_array(11, @$module_links) ||  Auth::user()->role_id == 1 ||  Auth::user()->role_id == 3)
            <div class="col-lg-4 col-xl-3 mt-50-md" id="todo-list">
                <div class="row">
                    <div class="col-6 col-xl-6">
                        <div class="main-title">
                            <h3 class="mb-30">
                                @lang('lang.to_do_list')
                            </h3>
                        </div>
                    </div>
                    <div class="text-right col-6 col-xl-6">
                        <a class="primary-btn small fix-gr-bg up_md_btn" data-modal-size="modal-md" data-target="#add_to_do" data-toggle="modal" href="#" title="Add To Do">
                            <span class="ti-plus pr-2">
                            </span>
                            @lang('lang.add')
                        </a>
                    </div>
                </div>
                <div class="modal fade admin-query" id="add_to_do">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">
                                     @lang('lang.add') @lang('lang.To_Do')
                                </h4>
                                <button class="close" data-dismiss="modal" type="button">
                                    ×
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="container-fluid">
                                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'saveToDoData',
                               'method' => 'POST', 'enctype' => 'multipart/form-data', 'onsubmit' => 'return validateToDoForm()']) }}
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="row mt-25">
                                                <div class="col-lg-12" id="sibling_class_div">
                                                    <div class="input-effect">
                                                        <input class="primary-input form-control" id="todo_title" name="todo_title" type="text">
                                                            <label>
                                                                @lang('lang.to_do_title')
                                                                <span>
                                                                </span>
                                                            </label>
                                                            <span class="focus-border">
                                                            </span>
                                                            <span class="modal_input_validation red_alert">
                                                            </span>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-30">
                                                <div class="col-lg-12" id="">
                                                    <div class="no-gutters input-right-icon">
                                                        <div class="col">
                                                            <div class="input-effect">
                                                                <input autocomplete="off" class="read-only-input primary-input date form-control{{ $errors->has('date') ? ' is-invalid' : '' }}" id="startDate" name="date" readonly="true" type="text" value="{{date('m/d/Y')}}">
                                                                    <label>
                                                                        @lang('lang.date')
                                                                        <span>
                                                                        </span>
                                                                    </label>
                                                                    @if ($errors->has('date'))
                                                                    <span class="invalid-feedback" role="alert">
                                                                        <strong>
                                                                            {{ $errors->first('date') }}
                                                                        </strong>
                                                                    </span>
                                                                    @endif
                                                                </input>
                                                            </div>
                                                        </div>
                                                        <div class="col-auto">
                                                            <button class="" type="button">
                                                                <i class="ti-calendar" id="start-date-icon">
                                                                </i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-12 text-center">
                                                <div class="mt-40 d-flex justify-content-between">
                                                    <button class="primary-btn tr-bg" data-dismiss="modal" type="button">
                                                        @lang('lang.cancel')
                                                    </button>
                                                    <input class="primary-btn fix-gr-bg" type="submit" value="save">

                                                </div>
                                            </div>
                                        </div>
                                        {{ Form::close() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="white-box school-table to_do_list_iner to_list_style">
                            <div class="row to-do-list mb-20">
                                <div class="col-sm-6 col-6 col-xl-6">
                                    <button class="primary-btn small fix-gr-bg mb_2"  id="toDoList">
                                        @lang('lang.incomplete')
                                    </button>
                                </div>
                                <div class="col-sm-6 col-6 col-xl-6 text-right">
                                    <button class="primary-btn small tr-bg" id="toDoListsCompleted">
                                        @lang('lang.completed')
                                    </button>
                                </div>
                            </div>
                            <input id="url" type="hidden" value="{{url('/')}}">
                                <div class="toDoList">
                                    @if(count($toDoLists)>0)

                            @foreach($toDoLists as $toDoList)
                                    <div class="single-to-do d-flex justify-content-between toDoList" id="to_do_list_div{{$toDoList->id}}">
                                        <div>
                                            <input class="common-checkbox complete_task" id="midterm{{$toDoList->id}}" name="complete_task" type="checkbox" value="{{@$toDoList->id}}">
                                                <label for="midterm{{$toDoList->id}}">
                                                    <input id="id" type="hidden" value="{{@$toDoList->id}}">
                                                        <input id="url" type="hidden" value="{{url('/')}}">
                                                            <h5 class="d-inline">
                                                                {{@$toDoList->todo_title}}
                                                            </h5>
                                                            <p class="ml-35">
                                                                {{ date('jS M, Y', strtotime(@$toDoList->date)) }}
                                                            </p>
                                                        </input>
                                                    </input>
                                                </label>
                                            </input>
                                        </div>
                                    </div>
                                    @endforeach
                            @else
                                    <div class="single-to-do d-flex justify-content-between">
                                        @lang('lang.no_do_lists_assigned_yet')
                                    </div>
                                    @endif
                                </div>
                                <div class="toDoListsCompleted">
                                    @if(count(@$toDoListsCompleteds)>0)

                            @foreach($toDoListsCompleteds as $toDoListsCompleted)
                                    <div class="single-to-do d-flex justify-content-between" id="to_do_list_div{{@$toDoListsCompleted->id}}">
                                        <div>
                                            <h5 class="d-inline">
                                                {{@$toDoListsCompleted->todo_title}}
                                            </h5>
                                            <p class="">
                                                {{ date('jS M, Y', strtotime(@$toDoListsCompleted->date)) }}
                                            </p>
                                        </div>
                                    </div>
                                    @endforeach
                            @else
                                    <div class="single-to-do d-flex justify-content-between">
                                        @lang('lang.no_do_lists_assigned_yet')
                                    </div>
                                    @endif
                                </div>
                            </input>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</section>

@php
    $chart_data = "";

    for($i = 1; $i <= date('d'); $i++){

    $i = $i < 10? '0'.$i:$i;
        $income = App\SmAddIncome::monthlyIncome($i);
        $expense = App\SmAddIncome::monthlyExpense($i);

        $chart_data .= "{ day: '" . $i . "', income: " . $income . ", expense:" . $expense . " },";
    }

@endphp


@php
    $chart_data_yearly = "";

    for($i = 1; $i <= date('m'); $i++){

    $i = $i < 10? '0'.$i:$i;

        $yearlyIncome = App\SmAddIncome::yearlyIncome($i);

        $yearlyExpense = App\SmAddIncome::yearlyExpense($i);

        $chart_data_yearly .= "{ y: '" . $i . "', income: " . @$yearlyIncome . ", expense:" . @$yearlyExpense . " },";
    }

@endphp


@endsection

@section('script')
<script type="text/javascript">
    function barChart(idName) {
        window.barChart = Morris.Bar({
            element: 'commonBarChart',
            data: [ <?php echo $chart_data; ?> ],
            xkey: 'day',
            ykeys: ['income', 'expense'],
            labels: ['Income', 'Expense'],
            barColors: ['#8a33f8', '#f25278'],
            resize: true,
            redraw: true,
            gridTextColor: '#415094',
            gridTextSize: 12,
            gridTextFamily: '"Poppins", sans-serif',
            barGap: 4,
            barSizeRatio: 0.3
        });
    }



    const monthNames = ["", "Jan", "Feb", "Mar", "Apr", "May", "Jun",
        "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
    ];

    function areaChart() {
        window.areaChart = Morris.Area({
            element: 'commonAreaChart',
            data: [ <?php echo $chart_data_yearly; ?> ],
            xkey: 'y',
            parseTime: false,
            gridTextColor: '#415094',
            ykeys: ['income', 'expense'],
            labels: ['Income', 'Expense'],
            xLabelFormat: function (x) {
                var index = parseInt(x.src.y);
                return monthNames[index];
            },
            xLabels: "month",
            labels: ['Income', 'Expense'],
            hideHover: 'auto',
            lineColors: ['rgba(124, 50, 255, 0.5)', 'rgba(242, 82, 120, 0.5)'],
            });
    }
</script>
<?php

$events = array();
foreach ($holidays as $k => $holiday) {

    $events[$k]['title'] = @$holiday->holiday_title;
    $events[$k]['start'] = date('D M Y', strtotime(@$holiday->from_date));
    $events[$k]['end'] = date('D M Y', strtotime(@$holiday->to_date));

}

?>
<script type="text/javascript">
    /*-------------------------------------------------------------------------------
       Full Calendar Js
    -------------------------------------------------------------------------------*/
    if ($('.common-calendar').length) {
        $('.common-calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            height: 650,
            events: <?php echo json_encode($events); ?>
             ,
        });
    }
</script>
@endsection

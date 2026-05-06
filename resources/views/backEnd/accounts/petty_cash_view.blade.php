@extends('backEnd.master')
@section('mainContent')
@php 
    $modules = [];
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();

 
    foreach($permissions as $permission){ @$module_links[] = @$permission->module_link_id; @$modules[] = @$permission->moduleLink->module_id;}

 
    $modules = array_unique($modules);




    $generalSetting=App\SmGeneralSettings::where('id',1)->first();
    $currency_symbol = @$generalSetting->currency_symbol; 
    if(isset($generalSetting->logo)){  @$logo = @$generalSetting->logo;  }
    else{ @$logo = 'public/uploads/settings/logo.png'; } 
 
@endphp


<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('lang.bank_account') @lang('lang.view')</h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                <a href="{{url('bank-account')}}">@lang('lang.bank') @lang('lang.account')</a>
                <a href="#">@lang('lang.bank_account') @lang('lang.view')</a>
            </div>
        </div>
    </div>
</section>
<section class="student-details mb-40">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-lg-4 no-gutters">
                <div class="main-title">
                    <h3 class="mb-30">@lang('lang.bank_account') @lang('lang.info')</h3>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="student-meta-box">
                    <div class="white-box">
                        <div class="single-meta mt-20">
                            <div class="row">
                                <div class="col-lg-3 col-md-6">
                                    <div class="value text-left">
                                        @lang('lang.account_name')
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="name">
                                        {{@$opening_balance->account_name}}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="single-meta">
                            <div class="row">
                                <div class="col-lg-3 col-md-6">
                                    <div class="value text-left">
                                        @lang('lang.opening_balance')({{@$currency_symbol}})
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="name">
                                        {{App\User::NumberToBangladeshiTakaFormat(@$opening_balance->opening_balance)}}
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="value text-left">
                                        @lang('lang.income')({{@$currency_symbol}})
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="name">
                                       {{App\User::NumberToBangladeshiTakaFormat(@$income_amount)}}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="single-meta">
                            <div class="row">
                                <div class="col-lg-3 col-md-6">
                                    <div class="value text-left">
                                        @lang('lang.expense')({{@$currency_symbol}})
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="name">
                                       {{App\User::NumberToBangladeshiTakaFormat(@$expense_amount)}}
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="value text-left">
                                        @lang('lang.remaining_balance')({{@$currency_symbol}})
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="name">
                                        {{App\User::NumberToBangladeshiTakaFormat(@$total_balance)}}
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


<section class="student-details mb-40">
    <div class="container-fluid p-0">


        <div class="row">
            <div class="col-lg-6">
                <div class="main-title">
                    <h3 class="mb-30">@lang('lang.income')/@lang('lang.transfer') @lang('lang.list')</h3>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        
                        <table class="display school-table school-table-style" cellspacing="0" width="100%">

                            <thead>
                                <tr>
                                    <th>@lang('lang.details')</th>
                                    <th>@lang('lang.date')</th>
                                    <th>@lang('lang.amount') ({{@$currency_symbol}})</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($incomes as $value)
                                <tr>
                                    <td>{{@$value->name}}</td>
                                    <td>{{date('jS M, Y', strtotime(@$value->date))}}</td>
                                    <td>{{App\User::NumberToBangladeshiTakaFormat(@$value->amount)}}</td>
                                </tr>
                                @endforeach
                                @foreach($transfers as $value)
                                <tr>
                                    <td>{{'from investment '}}</td>
                                    <td>{{date('jS M, Y', strtotime(@$value->date))}}</td>
                                    <td>{{App\User::NumberToBangladeshiTakaFormat(@$value->amount)}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="main-title">
                    <h3 class="mb-30">@lang('lang.deduct') @lang('lang.list')</h3>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        
                        <table class="display school-table school-table-style" cellspacing="0" width="100%">

                            <thead>
                                <tr>
                                    <th>@lang('lang.details')</th>
                                    <th>@lang('lang.date')</th>
                                    <th>@lang('lang.amount') ({{@$currency_symbol}})</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($expenses as $value)
                                <tr>
                                    <td>{{@$value->name}}</td>
                                    <td>{{date('jS M, Y', strtotime(@$value->created_at))}}</td>
                                    <td>{{App\User::NumberToBangladeshiTakaFormat(@$value->amount)}}</td>
                                </tr>
                                @endforeach

                                @foreach($payroll_deducts as $value)
                                <tr>
                                    <td>{{'Salary pay to '}}<strong>{{ @$value->staffs != ""? @$value->staffs->full_name:""}}</strong></td>
                                    <td>{{date('jS M, Y', strtotime(@$value->created_at))}}</td>
                                    <td>{{App\User::NumberToBangladeshiTakaFormat(@$value->net_salary)}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


@endsection

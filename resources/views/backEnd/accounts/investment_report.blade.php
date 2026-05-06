@extends('backEnd.master')
@section('mainContent')
<?php 
    $modules = [];
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();

 
    foreach($permissions as $permission){ @$module_links[] = @$permission->module_link_id; @$modules[] = @$permission->moduleLink->module_id;}

 
    $modules = array_unique(@$modules);

    $generalSetting=App\SmGeneralSettings::where('id',1)->first();
    $currency_symbol = @$generalSetting->currency_symbol; 
?>
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('lang.investment') @lang('lang.report')</h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                <a href="#">@lang('lang.accounts')</a>
                <a href="#">@lang('lang.investment') @lang('lang.report')</a>
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area">
    <div class="container-fluid p-0">
        
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="main-title">
                        <h3 class="mb-30">@lang('lang.select_criteria') </h3>
                    </div>
                </div>
                @if(in_array(249, @$module_links) || Auth::user()->role_id == 1)
                <div class="col-lg-6 text-right">
                    <a href="{{url('investment')}}" class="primary-btn small fix-gr-bg">
                        <span class="ti-plus pr-2"></span>
                        @lang('lang.add') @lang('lang.new')
                    </a>
                </div>
                @endif

            </div>
            @if(in_array(248, @$module_links) || Auth::user()->role_id == 1)
            <div class="row">
                <div class="col-lg-12">
                    @if(session()->has('message-success') != "")
                        @if(session()->has('message-success'))
                        <div class="alert alert-success">
                            {{ session()->get('message-success') }}
                        </div>
                        @endif
                    @endif
                    <div class="white-box">
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'invesment-search', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'search_student']) }}
                            <div class="row">
                                <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                                <div class="col-lg-6 mt-30-md">
                                    <div class="no-gutters input-right-icon">
                                        <div class="col">
                                            <div class="input-effect">
                                                <input class="primary-input date form-control{{ $errors->has('date_from') ? ' is-invalid' : '' }}" id="startDate" type="text"
                                                     name="date_from" value="{{isset($date_from)? date('m/d/Y', strtotime($date_from)): date('m/d/Y')}}" readonly>
                                                    <label>@lang('lang.date_from')</label>
                                                    <span class="focus-border"></span>
                                                @if ($errors->has('date_from'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('date_from') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <button class="" type="button">
                                                <i class="ti-calendar" id="start-date-icon"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mt-30-md">
                                    <div class="no-gutters input-right-icon">
                                        <div class="col">
                                            <div class="input-effect">
                                                <input class="primary-input date form-control{{ $errors->has('date_to') ? ' is-invalid' : '' }}" id="startDate" type="text"
                                                     name="date_to" value="{{isset($date_to)? date('m/d/Y', strtotime($date_to)) : date('m/d/Y')}}" readonly>
                                                    <label>@lang('lang.date_to')</label>
                                                    <span class="focus-border"></span>
                                                @if ($errors->has('date_to'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('date_to') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <button class="" type="button">
                                                <i class="ti-calendar" id="start-date-icon"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 mt-20 text-right">
                                    <button type="submit" class="primary-btn small fix-gr-bg">
                                        <span class="ti-search pr-2"></span>
                                        @lang('lang.search')
                                    </button>
                                </div>
                            </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>

        @endif



            @isset($dates)

            <div class="row mt-40">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-6 no-gutters">
                            <div class="main-title">
                                <h3 class="mb-0">@lang('lang.result')</h3>
                            </div>
                        </div>
                    </div>

                
                    <div class="row">
                        <div class="col-lg-12">
                            <table id="table_id" class="display school-table" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>@lang('lang.date')</th>
                                        <th>@lang('lang.staff')/@lang('lang.account')</th>
                                        <th>@lang('lang.investment')({{@$currency_symbol}})</th>
                                        <th>@lang('lang.expense')({{@$currency_symbol}})</th>
                                        <th>@lang('lang.balance')({{@$currency_symbol}})</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @php
                                        $investment_total = 0;
                                        $transfer_total = 0;
                                        $balance_total = 0;
                                    @endphp
                                    @foreach($dates as $date)
                                    @php
                                        $investments = App\SmInvestment::where('date', @$date)->get();
                                        $transfers = App\SmFundTransfer::where('date', @$date)->get();
                                    @endphp

                                        @foreach($investments as $investment)
                                        @php @$investment_total += @$investment->amount; @endphp

                                        @php @$balance_total += @$investment->amount; @endphp
                                        <tr>
                                            <td>{{date('jS M, Y', strtotime(@$date))}}</td>
                                            <td>{{@$investment->staffDetail != ""? @$investment->staffDetail->full_name:''}}</td>
                                            <td>{{App\User::NumberToBangladeshiTakaFormat(@$investment->amount)}}</td>
                                            <td>{{'-.--'}}</td>
                                            <td>{{App\User::NumberToBangladeshiTakaFormat(@$balance_total)}}</td>
                                        </tr>

                                        @endforeach

                                        @foreach($transfers as $transfer)
                                        @php @$transfer_total += @$transfer->amount; @endphp
                                        @php @$balance_total -= @$transfer->amount; @endphp
                                        <tr>
                                            <td>{{date('jS M, Y', strtotime(@$date))}}</td>
                                            <td>{{@$transfer->bankDeatil != ""? @$transfer->bankDeatil->account_name:""}}</td>
                                            <td>{{'-.--'}}</td>
                                            <td>{{App\User::NumberToBangladeshiTakaFormat(@$transfer->amount)}}</td>
                                            <td>{{App\User::NumberToBangladeshiTakaFormat(@$balance_total)}}</td>
                                        </tr>

                                        @endforeach


                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <th></th>
                                    <th>@lang('lang.grand_total'): </th>
                                    <th>{{App\User::NumberToBangladeshiTakaFormat(@$investment_total)}}</th>
                                    <th>{{App\User::NumberToBangladeshiTakaFormat(@$transfer_total)}}</th>
                                    <th>{{App\User::NumberToBangladeshiTakaFormat(@$investment_total - @$transfer_total)}}</th>
                                </tfoot>
                                   
                           
                                    
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            @endisset
    </div>
</section>


@endsection

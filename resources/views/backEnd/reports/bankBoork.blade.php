@extends('backEnd.master')
@section('mainContent')
<?php 
    @$generalSetting=App\SmGeneralSettings::where('id',1)->first();
    @$currency_symbol = @$generalSetting->currency_symbol; 
?>
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('lang.bank') @lang('lang.book') @lang('lang.report')</h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                <a href="#">@lang('lang.reports')</a>
                <a href="#">@lang('lang.bank') @lang('lang.book') @lang('lang.report')</a>
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area">
    <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="main-title">
                        <h3 class="mb-30">@lang('lang.select_criteria') </h3>
                    </div>
                </div>
            </div>
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
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'bank-book', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'search_student']) }}
                            <div class="row">
                                <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                                <div class="col-lg-4 mt-30-md">
                                    <div class="no-gutters input-right-icon">
                                        <div class="col">
                                            <div class="input-effect">
                                                <input class="primary-input date form-control{{ $errors->has('date_from') ? ' is-invalid' : '' }}" id="startDate" type="text"
                                                     name="date_from" value="{{date('m/d/Y')}}" readonly>
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
                                <div class="col-lg-4 mt-30-md">
                                    <div class="no-gutters input-right-icon">
                                        <div class="col">
                                            <div class="input-effect">
                                                <input class="primary-input date form-control{{ $errors->has('date_to') ? ' is-invalid' : '' }}" id="startDate" type="text"
                                                     name="date_to" value="{{date('m/d/Y')}}" readonly>
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
                                <div class="col-lg-4">

                                        <select class="niceSelect w-100 bb form-control{{ $errors->has('head') ? ' is-invalid' : '' }}" name="bank"  id="head">
                                            <option data-display="@lang('lang.bank')" value="">@lang('lang.bank')</option>
                                            @foreach($banks as $bank)
                                                <option value="{{@$bank->id}}" >{{@$bank->account_name}}</option>
                                              
                                            @endforeach
                                        </select>
                                        @if ($errors->has('bank'))
                                        <span class="invalid-feedback invalid-select" role="alert">
                                            <strong>{{ $errors->first('bank') }}</strong>
                                        </span>
                                        @endif
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
            
 


@if(isset($bank_accounts))


            <div class="row mt-40">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-6 no-gutters">
                            <div class="main-title">
                                <h3 class="mb-30">@lang('lang.result')</h3>
                            </div>
                        </div>
                    </div>
                    <!-- </div> -->
                    <div class="row">
                        <div class="col-lg-12">
                            <table class="display school-table school-table-style" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>@lang('lang.date')</th>
                                        <th>@lang('lang.bank')</th>
                                        <th>@lang('lang.opening_balance')</th>
                                        <th>@lang('lang.income')</th>
                                        <th>@lang('lang.expense')</th>
                                        <th>@lang('lang.closing') @lang('lang.balance')({{@$currency_symbol}})</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bank_accounts as $bank_account)
                                    @php

                                        @$bank_detail = App\SmBankAccount::bankDetails(@$date_from, $date_to, @$bank_account->id);
                                        



                                    @endphp
                                    <tr>
                                        <td>{{date('jS M, Y', strtotime(@$date_from)) .' to '. date('jS M, Y', strtotime(@$date_to)) }}</td>
                                        <td>{{@$bank_account->account_name}}</td>
                                        <td>{{App\User::NumberToBangladeshiTakaFormat(@$bank_detail['opening_balance'])}}</td>
                                        <td>{{App\User::NumberToBangladeshiTakaFormat(@$bank_detail['income'])}}</td>
                                        <td>{{App\User::NumberToBangladeshiTakaFormat(@$bank_detail['expense'])}}</td>
                                        <td>{{App\User::NumberToBangladeshiTakaFormat(@$bank_detail['opening_balance'] + @$bank_detail['income'] - @$bank_detail['expense'])}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

@endif




    </div>
</section>


@endsection

@extends('backEnd.master')
@section('mainContent')
<?php 
    @$modules = [];
    @$module_links = [];
    @$permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();

 
    foreach($permissions as $permission){ @$module_links[] = @$permission->module_link_id; @$modules[] = @$permission->moduleLink->module_id;}

 
    @$modules = array_unique(@$modules);
    @$generalSetting=App\SmGeneralSettings::where('id',1)->first();
    @$currency_symbol = @$generalSetting->currency_symbol; 
?>
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('lang.bank') @lang('lang.ledger') @lang('lang.report')</h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                <a href="#">@lang('lang.accounts')</a>
                <a href="#">@lang('lang.bank') @lang('lang.ledger') @lang('lang.report')</a>
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
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'bank-ledger', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'search_student']) }}
                            <div class="row">
                                <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                                <div class="col-lg-4 mt-30-md">
                                    <div class="no-gutters input-right-icon">
                                        <div class="col">
                                            <div class="input-effect">
                                                <input class="primary-input date form-control{{ @$errors->has('date_from') ? ' is-invalid' : '' }}" id="startDate" type="text"
                                                     name="date_from" value="{{isset($date_from)? date('m/d/Y', strtotime(@$date_from)): date('m/d/Y')}}" readonly>
                                                    <label>@lang('lang.date_from')</label>
                                                    <span class="focus-border"></span>
                                                @if ($errors->has('date_from'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ @$errors->first('date_from') }}</strong>
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
                                                <input class="primary-input date form-control{{ @$errors->has('date_to') ? ' is-invalid' : '' }}" id="startDate" type="text"
                                                     name="date_to" value="{{isset($date_to)? date('m/d/Y', strtotime(@$date_to)) : date('m/d/Y')}}" readonly>
                                                    <label>@lang('lang.date_to')</label>
                                                    <span class="focus-border"></span>
                                                @if (@$errors->has('date_to'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ @$errors->first('date_to') }}</strong>
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
                                    <select class="niceSelect w-100 bb form-control{{ @$errors->has('account') ? ' is-invalid' : '' }}" name="account" id="to_account">
                                                <option data-display="select @lang('lang.account')" value="">select @lang('lang.account')</option>
                                                @foreach($bank_accounts as $value)
                                                    <option value="{{@$value->id}}">{{@$value->account_name}}</option>
                                                @endforeach
                                            </select>
                                            @if (@$errors->has('account'))
                                                <span class="invalid-feedback invalid-select" role="alert">
                                                    <strong>{{ @$errors->first('account') }}</strong>
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
                                        <th>@lang('lang.account') @lang('lang.name')</th>
                                        <th>@lang('lang.details') </th>
                                        <th>@lang('lang.income')({{@$currency_symbol}})</th>
                                        <th>@lang('lang.expense')({{@$currency_symbol}})</th>
                                        <th>@lang('lang.balance')({{@$currency_symbol}})</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @php
                                    @$total_balance = 0;
                                    @$income_balance = 0;
                                    @$expense_balance = 0;
                                    @endphp
                                    @foreach($dates as $date)

                                    @php

                                        if(@$account != ""){

                                            @$bank_accounts = App\SmBankAccount::where('id', @$account)->where('updated_at', 'LIKE', '%'.@$date.'%')->get();

                                            @$fund_transfers = App\SmFundTransfer::where('bank_account_id', @$account)->where('date', @$date)->get();


                                            @$incomes = App\SmAddIncome::where('account_id', @$account)->where('payment_method_id', 3)->where('updated_at', 'LIKE', '%'.@$date.'%')->get();


                                            @$expenses = App\SmAddExpense::where('account_id', @$account)->where('payment_method_id', 3)->where('updated_at', 'LIKE', '%'.@$date.'%')->get();

                                            @$payroll_deducts = App\SmHrPayrollGenerate::where('account_id', @$account)->where('payment_mode', 3)->where('updated_at', 'LIKE', '%'.@$date.'%')->get();
                                        }else{

                                            @$bank_accounts = App\SmBankAccount::where('updated_at', 'LIKE', '%'.@$date.'%')->get();

                                            @$fund_transfers = App\SmFundTransfer::where('date', @$date)->get();


                                            @$incomes = App\SmAddIncome::where('payment_method_id', 3)->where('updated_at', 'LIKE', '%'.@$date.'%')->get();

                                            @$expenses = App\SmAddExpense::where('payment_method_id', 3)->where('updated_at', 'LIKE', '%'.@$date.'%')->get();

                                            @$payroll_deducts = App\SmHrPayrollGenerate::where('payment_mode', 3)->where('updated_at', 'LIKE', '%'.@$date.'%')->get();
                                        }
                                        


                                    @endphp

                                        @foreach($bank_accounts as $value)
                                        @php 
                                            @$income_balance = @$income_balance + @$value->opening_balance;
                                            @$total_balance = @$total_balance + @$value->opening_balance;
                                        @endphp
                                        <tr>
                                            <td>{{date('jS M, Y', strtotime(@$value->updated_at))}}</td>
                                            <td>{{@$value->account_name}}</td>
                                            <td>{{'Opening Balance'}}</td>
                                            <td>{{App\User::NumberToBangladeshiTakaFormat(@$value->opening_balance)}}</td>
                                            <td>{{'-.--'}}</td>
                                            <td>{{App\User::NumberToBangladeshiTakaFormat(@$total_balance)}}</td>
                                        </tr>

                                        @endforeach

                                        @foreach($fund_transfers as $value)
                                        @php 
                                            @$income_balance = @$income_balance + @$value->amount;
                                            @$total_balance = @$total_balance + @$value->amount;
                                        @endphp
                                        <tr>
                                            <td>{{date('jS M, Y', strtotime(@$value->date))}}</td>
                                            <td>{{@$value->bankDeatil != ""? @$value->bankDeatil->account_name:""}}</td>
                                            <td>{{'Investment balance'}}</td>
                                            <td>{{App\User::NumberToBangladeshiTakaFormat(@$value->amount)}}</td>
                                            <td>{{'-.--'}}</td>
                                            <td>{{App\User::NumberToBangladeshiTakaFormat(@$total_balance)}}</td>
                                        </tr>

                                        @endforeach

                                        @foreach($incomes as $value)
                                        @php 
                                            @$income_balance = @$income_balance + @$value->amount;
                                            @$total_balance = @$total_balance + @$value->amount;
                                        @endphp
                                        <tr>
                                            <td>{{date('jS M, Y', strtotime(@$value->updated_at))}}</td>
                                            <td>{{@$value->account != ""? @$value->account->account_name:""}}</td>
                                            <td>{{@$value->name}}</td>
                                            <td>{{App\User::NumberToBangladeshiTakaFormat(@$value->amount)}}</td>
                                            <td>{{'-.--'}}</td>
                                            <td>{{App\User::NumberToBangladeshiTakaFormat(@$total_balance)}}</td>
                                        </tr>

                                        @endforeach

                                        @foreach($expenses as $value)
                                        @php 
                                            @$expense_balance = @$expense_balance + @$value->amount;
                                            @$total_balance = @$total_balance - @$value->amount;
                                        @endphp
                                        <tr>
                                            <td>{{date('jS M, Y', strtotime(@$value->updated_at))}}</td>
                                            <td>{{ @$value->account != ""? @$value->account->account_name:"" }}</td>
                                            <td>{{ @$value->name }}</td>
                                            <td>{{'-.--'}}</td>
                                            <td>{{App\User::NumberToBangladeshiTakaFormat(@$value->amount)}}</td>
                                            
                                            <td>{{App\User::NumberToBangladeshiTakaFormat(@$total_balance)}}</td>
                                        </tr>

                                        @endforeach

                                        @foreach($payroll_deducts as $value)
                                        @php 
                                            @$expense_balance = @$expense_balance + @$value->net_salary;
                                            @$total_balance = @$total_balance - @$value->net_salary;
                                        @endphp
                                        <tr>
                                            <td>{{date('jS M, Y', strtotime(@$value->updated_at))}}</td>
                                            <td>{{@$value->accountInfo != ""? @$value->accountInfo->account_name:""}}</td>
                                            <td>{{'Salary for  '.@$value->payroll_month.' '.@$value->payroll_year.' to '.@$value->staffDetails->full_name}}</td>
                                            <td>{{'-.--'}}</td>
                                            <td>{{App\User::NumberToBangladeshiTakaFormat(@$value->net_salary)}}</td>
                                            
                                            <td>{{App\User::NumberToBangladeshiTakaFormat(@$total_balance)}}</td>
                                        </tr>

                                        @endforeach
                                    @endforeach
                                </tbody>

                                <tfoot>
                                    <th></th>
                                    <th></th>
                                    <th>@lang('lang.grand_total') </th>
                                    <th>{{App\User::NumberToBangladeshiTakaFormat(@$income_balance)}}</th>
                                    <th>{{App\User::NumberToBangladeshiTakaFormat(@$expense_balance)}}</th>
                                    <th>{{App\User::NumberToBangladeshiTakaFormat(@$income_balance - @$expense_balance)}}</th>
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

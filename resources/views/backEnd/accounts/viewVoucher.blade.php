@extends('backEnd.master')
@section('mainContent')
<link rel="stylesheet" href="{{asset('public/')}}/css/viewVoucher.css"/>

@php
    $generalSetting=App\SmGeneralSettings::where('id',1)->first();
    $currency_symbol = @$generalSetting->currency_symbol; 
@endphp 
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>{{ __('Debit/Credit Voucher')}}</h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                <a href="#">@lang('lang.accounts')</a>
                <a href="#">{{ __('Debit/Credit Voucher')}}</a>
            </div>
        </div>
    </div>
</section>
@if(@$voucher->type == 'C')
<section class="admin-visitor-area">
    <div class="container-fluid p-0">

            <div class="col-lg-8 offset-lg-2">
                <div class="row">
                    <div class="col-lg-4 no-gutters">
                        <div class="main-title">
                            <h3 class="mb-30">{{ __('Voucher View')}}</h3>
                        </div>
                    </div>
                </div>

                <div class="row white-box">
                    <div class="col-lg-12" id="credit-voucher">
                        
                       <table class="display school-table school-table-style credit_voucher_table" cellspacing="0" width="100%" id="printTable" >

                            <thead>
                                <tr>
                                    <td colspan="3" class="text-center">{{ __('Credit Voucher')}}</td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        <div class="row">
                                            <div class="col-md-3 text-left">
                                                {{ __('Voucher No.')}} {{@$voucher->voucher_no}}
                                            </div>
                                            <div class="col-md-6 text-center">
                                                {{@$voucher->customer}}
                                                {{@$voucher->company_or_address}}
                                            </div>
                                            <div class="col-md-3 text-right">
                                                {{date('jS M, Y', strtotime(@$voucher->date))}}<br>
                                                <span>{{ __('Amount')}} ({{ @$currency_symbol}})</span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" valign="top">
                                        <table width="100%">
                                            <tr><td>Credit: dvd fev efv evwdc</td></tr>
                                            <tr><td class="text-right">@lang('lang.total')</td></tr>
                                        </table>
                                        
                                    </td>
                                    <td align="right" valign="top">
                                        <table>
                                            <tr><td class="text-right" valign="top">{{@$voucher->amount}}</td></tr>
                                            <tr><td class="text-right">{{@$voucher->amount}} ({{ @$currency_symbol}})</td></tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <table height='200' width="100">
                                                    <tr><td>
                                                        @if(@$voucher->authorised_signature != "")
                                                    
                                                    <img src="{{asset($voucher->authorised_signature)}}" width="20%">
                                                        @endif
                                                    </td></tr>
                                                    <tr><td>@lang('lang.Authorized') @lang('lang.signature')</td></tr>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <table height='200' width="100" align="right">
                                                    <tr><td>
                                                        @if(@$voucher->accountant_signature != "")
                                                    
                                                    <img src="{{asset($voucher->accountant_signature)}}" width="100%">

                                                    @endif
                                                    </td></tr>
                                                    <tr><td>@lang('lang.Accountant') @lang('lang.signature')</td></tr>
                                                </table>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </thead>
                            </tbody>
                        </table>
                        
                    </div>
                    <div class="col-md-12 text-center mt-40">
                        <button class="primary-btn fix-gr-bg" id="printCreditVoucher">@lang('lang.print')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@else
<section class="admin-visitor-area">
    <div class="container-fluid p-0">

            <div class="col-lg-8 offset-lg-2">
                <div class="row">
                    <div class="col-lg-4 no-gutters">
                        <div class="main-title">
                            <h3 class="mb-30">{{ __('Voucher View')}}</h3>
                        </div>
                    </div>
                </div>

                <div class="row white-box">
                    <div class="col-lg-12" id="credit-voucher">
                        
                       <table class="display school-table school-table-style credit_voucher_table" cellspacing="0" width="100%" id="printTable" >

                            <thead>
                                <tr>
                                    <td colspan="4" class="text-center" ><h4>@lang('lang.Debit') @lang('lang.voucher')</h4></td>
                                </tr>
                                <tr>
                                    <td width="25%" class="rotate" valign="top">
                                        <span>@lang('lang.received'): <span class="credit_voucher_table_p"> {{@$voucher->receiver}} </span> </span>
                                    </td>

                                    <td colspan="3">
                                        <table width="100%">
                                            <tr>
                                                <td colspan="3">
                                                    <div class="row">
                                                        <div class="col-md-3 text-left">
                                                            @lang('lang.voucher') @lang('lang.no_') {{@$voucher->voucher_no}}
                                                        </div>
                                                        <div class="col-md-6 text-center">
                                                            {{@$voucher->customer}}
                                                            {{@$voucher->company_or_address}}
                                                        </div>
                                                        <div class="col-md-3 text-right">
                                                            {{date('jS M, Y', strtotime(@$voucher->date))}}<br>
                                                            <span>@lang('lang.amount') ({{ @$currency_symbol}})</span>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" valign="top">
                                                    <table width="100%">
                                                        <tr><td>@lang('lang.Credit'):</td></tr>
                                                        <tr><td class="text-right">@lang('lang.total')</td></tr>
                                                    </table>
                                                    
                                                </td>
                                                <td align="right" valign="top">
                                                    <table>
                                                        <tr><td class="text-right" valign="top">{{@$voucher->amount}}</td></tr>
                                                        <tr><td class="text-right">{{@$voucher->amount}} {{ @$currency_symbol}}</td></tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <table height='200' width="100">
                                                                <tr><td>
                                                                    @if(@$voucher->authorised_signature != "")
                                                                
                                                                <img src="{{asset($voucher->authorised_signature)}}" width="100%">
                                                                    @endif
                                                                </td></tr>
                                                                <tr><td>@lang('lang.Authorized') @lang('lang.signature')</td></tr>
                                                            </table>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <table height='200' width="100" align="right">
                                                                <tr><td>
                                                                    @if(@$voucher->accountant_signature != "")
                                                                
                                                                <img src="{{asset($voucher->accountant_signature)}}" width="100%">

                                                                @endif
                                                                </td></tr>
                                                                <tr><td>@lang('lang.Accountant') @lang('lang.signature')</td></tr>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                
                            </thead>
                            </tbody>
                        </table>
                        
                    </div>
                    <div class="col-md-12 text-center mt-40">
                        <button class="primary-btn fix-gr-bg" id="printCreditVoucher">@lang('lang.print')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endif
@endsection

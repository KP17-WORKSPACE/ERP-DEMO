<?php 
    $generalSetting=App\SmGeneralSettings::where('id',1)->first();
    $currency_symbol = $generalSetting->currency_symbol; 

    $modules = [];
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();

    foreach($permissions as $permission){ @$module_links[] = @$permission->module_link_id; @$modules[] = @$permission->moduleLink->module_id;}

    $modules = array_unique(@$modules);
?>

<script src="{{asset('public/backEnd/')}}/js/main.js"></script>
<div class="container-fluid">
    <div class="student-details">
        <div class="text-center mb-4">
            <div class="d-flex justify-content-center">
                <div>
                    <img class="logo-img" src="http://localhost/naim/schoolmanagementsystem/public/backEnd/img/logo.png"
                        alt="">
                </div>
                <div class="ml-30">
                    <h2>@if(isset($schoolDetails)){{@$schoolDetails->school_name}} @endif</h2>
                    <p class="mb-0">@if(isset($schoolDetails)){{@$schoolDetails->address}} @endif</p>
                </div>
            </div>
            <h3 class="mt-3">@lang('lang.Payslip_for_the_period_of') {{@$payrollDetails->payroll_month}} {{@$payrollDetails->payroll_year}}</h3>
        </div>

        <div class="single-meta d-flex justify-content-between mb-4">
            <div class="value text-left">
                @lang('lang.payslip') #@if(isset($payrollDetails)){{$payrollDetails->id}} @endif
            </div>
            <div class="name">
               @lang('lang.payment')    @lang('lang.date'): @if(isset($payrollDetails)){{date('jS M, Y', strtotime(@$payrollDetails->payment_date))}}
                @endif
            </div>
        </div>


        <div class="student-meta-box">
            <div class="single-meta">
                <div class="row">
                    <div class="col-lg-3 col-md-5">
                        <div class="value text-left">
                           {{ __('Staff ID') }} 
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-7">
                        <div class="name">
                            @if(isset($payrollDetails)){{@$payrollDetails->staffs->staff_no}} @endif
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-5">
                        <div class="value text-left">
                            {{ __('Name') }}
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-7">
                        <div class="name">
                            @if(isset($payrollDetails)){{@$payrollDetails->staffDetails->full_name}} @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="single-meta">
                <div class="row">
                    <div class="col-lg-3 col-md-5">
                        <div class="value text-left">
                            {{ __('Department') }}
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-7">
                        <div class="name">
                            @if(isset($payrollDetails)){{@$payrollDetails->staffDetails->departments->name}} @endif

                        </div>
                    </div>
                    <div class="col-lg-3 col-md-5">
                        <div class="value text-left">
                            {{ __('Designation') }}
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-7">
                        <div class="name">
                            @if(isset($payrollDetails)){{@$payrollDetails->staffDetails->designations->title}} @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="single-meta">
                <div class="row">
                    <div class="col-lg-3 col-md-5">
                        <div class="value text-left">
                            {{ __('ayment Method') }}P
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-7">
                        <div class="name">
                            @if(@$payrollDetails->payment_mode != "")
                            {{@$payrollDetails->paymentMethods->method}}
                            @endif

                        </div>
                    </div>
                    <div class="col-lg-3 col-md-5">
                        <div class="value text-left">
                           {{ __('Basic Salary') }}  ({{@$currency_symbol}})
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-7">
                        <div class="name">
                             @if(isset($payrollDetails)){{App\User::NumberToBangladeshiTakaFormat(@$payrollDetails->basic_salary)}} @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="single-meta">
                <div class="row">
                    <div class="col-lg-3 col-md-5">
                        <div class="value text-left">
                            {{ __('Gross Salary') }} ({{@$currency_symbol}})
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-7">
                        <div class="name">
                            @if(isset($payrollDetails)){{App\User::NumberToBangladeshiTakaFormat(@$payrollDetails->gross_salary)}} @endif
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-5">
                        <div class="value text-left">
                           {{ __('Net Salary') }}  ({{@$currency_symbol}})
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-7">
                        <div class="name">
                            @if(isset($payrollDetails)){{App\User::NumberToBangladeshiTakaFormat(@$payrollDetails->net_salary)}} @endif
                        </div>
                    </div>
                </div>
            </div>
            @if($payrollDetails->payment_mode == 3 )
                <div class="single-meta">
                    <div class="row">
                        <div class="col-lg-3 col-md-5">
                            <div class="value text-left">
                               {{ __('Bank Name') }} 
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-7">
                            <div class="name">
                                {{@$payrollDetails->accountInfo != ""? @$payrollDetails->accountInfo->bank_name:""}}
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-5">
                            <div class="value text-left">
                               {{ __('Account Name') }} 
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-7">
                            <div class="name">
                                {{@$payrollDetails->accountInfo != ""? @$payrollDetails->accountInfo->account_name:""}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="single-meta">
                    <div class="row">
                        <div class="col-lg-3 col-md-5">
                            <div class="value text-left">
                               {{ __('Account  No.') }} 
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-7">
                            <div class="name">
                                {{@$payrollDetails->accountInfo != ""? @$payrollDetails->accountInfo->account_no:""}}
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-5">
                            <div class="value text-left">
                                {{ __('Deposite date') }}
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-7">
                            <div class="name">
                                {{@$payrollDetails->cheque_deposite_date != ""? date('jS M, Y', strtotime(@$payrollDetails->cheque_deposite_date)):''}}
                            </div>
                        </div>
                    </div>
                </div>
                @elseif(@$payrollDetails->payment_mode == 2)
                <div class="single-meta">
                    <div class="row">
                        <div class="col-lg-3 col-md-5">
                            <div class="value text-left">
                               {{ __(' Bank Name') }}
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-7">
                            <div class="name">
                                {{@$payrollDetails->bank_name}}
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-5">
                            <div class="value text-left">
                                {{ __('Cheque No.') }}
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-7">
                            <div class="name">
                                {{@$payrollDetails->cheque_no}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="single-meta">
                    <div class="row">
                        <div class="col-lg-3 col-md-5">
                            <div class="value text-left">
                              {{ __('Cheque issue date') }}  
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-7">
                            <div class="name">
                                {{@$payrollDetails->cheque_deposite_date != ""? date('jS M, Y', strtotime(@$payrollDetails->cheque_deposite_date)):''}}
                            </div>
                        </div>
                    </div>
                </div>

                @endif

            <div class="row mt-50">
                @if(in_array(334, @$module_links) ||  Auth::user()->role_id == 1)
                <div class="col-lg-6 text-right">
                    <a href="{{url('send-payslip', [@$payrollDetails->id])}}" id="fees-groups-print-button" class="primary-btn medium fix-gr-bg" target="">
                        <i class="ti-printer pr-2"></i>
                       {{ __('Send Mail') }} 
                    </a>
                </div>
                @endif

                @if(in_array(333, @$module_links) ||  Auth::user()->role_id == 1)

                <div class="col-lg-6">
                    <a href="{{url('print-payslip', [@$payrollDetails->id])}}" id="fees-groups-print-button" class="primary-btn medium fix-gr-bg" target="_blank">
                        <i class="ti-printer pr-2"></i>
                        @lang('lang.print')
                    </a>
                </div>
                @endif

            </div>

        </div>
    </div>
</div>

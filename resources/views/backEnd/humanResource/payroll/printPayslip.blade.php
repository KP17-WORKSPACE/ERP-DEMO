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

    $sm_staff= App\SmStaff::where('user_id',Auth::user()->id)->first();
    if(!empty(@$sm_staff)){
        $profile_image = @$sm_staff->staff_photo;
        if(empty(@$profile_image)){
            @$profile_image ='public/uploads/staff/staff1.jpg';
        }
    }
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<link  href="{{asset('/public/css')}}/bootstrap.min.css"  rel="stylesheet">
<link  href="{{asset('/public/css')}}/printPayslip.min.css"  rel="stylesheet">
<link  href="{{asset('/public/css')}}/paySlip_Print.css"  rel="stylesheet">

</head>
<body>
    <section class="admin-visitor-area" >
        <div class="container-fluid cs_print">
            <div class="row mt-40">
                <div class="col-lg-12">
                    <div class="row" id="purchaseInvoice">
                        <div class="container-fluid">
                            <div class="row mb-20">
                                <table class="business_info_table">
                                    <tr>
                                        <td class="table_td">
                                            <div class="business-info text-left">
                                            <img src="{{asset($logo)}}"  class="table_image">
                                                <h3 class="mt-10 primary-color" class="table_image_h3">@if(isset($schoolDetails)){{@$schoolDetails->school_name}} @endif</h3>
                                                <div class="table_div">
                                                    <p class="table_image_p"></p>
                                                    <p class="table_image_p2">@if(isset($schoolDetails)){{@$schoolDetails->address}} @endif</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="table_td_invoice" class="primary-color">
                                            <div class="invoice-details-right">
                                                <h2 class="text-left" class="table_invoice_table">Payslip for the period of {{@$payrollDetails->payroll_month}} {{@$payrollDetails->payroll_year}}</h2>
                                                
                                                <table class="invoice_table">
                                                    <tr>
                                                        <th class="table_tr_30">{{ __('Staff name') }}.</th> 
                                                        <td class="table_tr_70">  @if(isset($payrollDetails)){{@$payrollDetails->staffDetails->full_name}} @endif</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="table_tr_30">{{ __('Staff ID') }}</th> 
                                                        <td class="table_tr_70">  @if(isset($payrollDetails)){{@$payrollDetails->staffs->staff_no}} @endif</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="table_tr_30">{{ __('Department') }}</th> 
                                                        <td class="table_tr_70">  @if(isset($payrollDetails)){{@$payrollDetails->staffDetails->departments->name}} @endif</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="table_tr_30">{{ __('Designation') }}</th> 
                                                        <td class="table_tr_70">  @if(isset($payrollDetails)){{@$payrollDetails->staffDetails->designations->title}} @endif</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="td_payslip">
                                            <p>{{ __('Payslip') }} #@if(isset($payrollDetails)){{$payrollDetails->id}} @endif</p>
                                        </td>
                                        <td class="payment_date">
                                            <p>{{ __('Payment Date') }} @if(isset($payrollDetails)){{date('jS M, Y', strtotime(@$payrollDetails->payment_date))}} @endif</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="primary-color" class="td_table">
                                            <h5 class="primary-color text-uppercase" class="td_earning">Earnings</h5>
                                            <table class="table table-bordered" class="earning_table">
                                                
                                                    <tr>
                                                        <th class="table_title">{{ __('Title') }}</th>
                                                        <td class="table_title">{{ __('Amount') }} ({{'BDT'}})</td>
                                                    </tr>

                                                    <tr>
                                                        <th class="table_title">{{ __('Basic Salary') }} </th>
                                                        <td class="table_title"> <span>@if(isset($payrollDetails)){{App\User::NumberToBangladeshiTakaFormat(@$payrollDetails->basic_salary)}} @endif</span></td>
                                                    </tr>

                                                    @php $gross_earning = @$payrollDetails->basic_salary; @endphp

                                                @foreach($payrollEarnDetails as $payrollEarnDetail)

                                                @php $gross_earning = @$gross_earning + @$payrollEarnDetail->amount; @endphp
                                                    <tr>
                                                        <th class="table_title">{{@$payrollEarnDetail->type_name}}</th>
                                                        <td class="table_title"> <span>@if(isset($payrollDetails)){{App\User::NumberToBangladeshiTakaFormat(@$payrollEarnDetail->amount)}} @endif</span></td>
                                                    </tr>
                                                @endforeach
                                                    


                                                    <tr>
                                                        <th class="table_title">{{ __('Gross earnings') }} ({{'BDT'}})</th>
                                                        <td class="table_title"> <span>@if(isset($payrollDetails)){{App\User::NumberToBangladeshiTakaFormat(@$gross_earning)}} @endif</span></td>
                                                    </tr>  
                                            </table>
                                            
                                        </td>
                                      
                                        <td class="primary-color table_deduction">
                                            <h5 class="primary-color text-uppercase" class="table_deduction_h5">{{ __('Deductions') }}</h5>
                                             
                                            <table class="table table-bordered" class="earning_table">
                                                
                                                    <tr>
                                                        <th class="table_title">{{ __('Title') }}</th>
                                                        <td class="table_title">{{ __('Amount') }} ({{'BDT'}})</td>
                                                    </tr>
                                                    @php $total_deduction = 0; @endphp
                                                   @foreach($payrollDedcDetails as $payrollDedcDetail)
                                                   @php @$total_deduction = @$total_deduction + @$payrollDedcDetail->amount; @endphp
                                                        <tr>
                                                            <th class="table_title">{{@$payrollDedcDetail->type_name}}</th>
                                                            <td class="table_title"> <span>@if(isset($payrollDetails)){{App\User::NumberToBangladeshiTakaFormat(@$payrollDedcDetail->amount)}} @endif</span></td>
                                                        </tr>
                                                    @endforeach  

                                                    <tr>
                                                        <th class="table_title">{{ __('Total Deductions') }} ({{'BDT'}})</th>
                                                        <td class="table_title"> <span> @if(isset($payrollDetails)){{App\User::NumberToBangladeshiTakaFormat(@$total_deduction)}} @endif</span></td>
                                                    </tr> 
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="primary-color"  class="gross_earn_td">
                                            <table class="table table-bordered" class="earning_table ">
                                                
                                                    <tr>
                                                        <th>{{ __('Gross Earnings') }} ({{'BDT'}})</th>
                                                        <th>{{ __('Total Deductions') }} ({{'BDT'}})</th>
                                                        <th>{{ __('Net Salary') }} ({{'BDT'}})</th>
                                                    </tr> 
                                                    <tr>
                                                        <td>@if(isset($payrollDetails)){{App\User::NumberToBangladeshiTakaFormat(@$gross_earning)}} @endif</td>
                                                        <td>@if(isset($payrollDetails)){{App\User::NumberToBangladeshiTakaFormat(@$total_deduction)}} @endif</td>
                                                        <td>

                                                        

                                                    @if(isset($payrollDetails)){{App\User::NumberToBangladeshiTakaFormat(@$gross_earning - @$total_deduction)}} @endif</td>
                                                    </tr> 
                                            </table>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="in_word_td" class="primary-color">
                                            <div class="invoice-details-right invoice-details-content">
                                                <p class="fw-500 primary-color in_word_p"> <b> {{-- In Words : --}}</b> <span class="text-left" >{{-- Fifty two thousands taka only. --}}</span></p>
                                            </div>
                                        </td>


                                        @if($payrollDetails->payment_mode == 3)

                                        <td  class="primary-color">
                                            <table class="bank_info">
                                                <tr>
                                                    <th class="width_35">{{ __('Bank Name') }}.</th> 
                                                    <td class="width_65_color">  {{@$payrollDetails->accountInfo != ""? @$payrollDetails->accountInfo->bank_name:""}}</td>
                                                </tr>
                                                <tr>
                                                    <th class="width_35">{{ __('Account Name') }}.</th> 
                                                    <td class="width_65_color">  {{@$payrollDetails->accountInfo != ""? @$payrollDetails->accountInfo->account_name:""}}</td>
                                                </tr>
                                                <tr>
                                                    <th class="width_35"d>{{ __('Account  No.') }}</th> 
                                                    <td class="width_65_color">  {{@$payrollDetails->accountInfo != ""? @$payrollDetails->accountInfo->account_no:""}}</td>
                                                </tr>
                                                <tr>
                                                    <th class="width_35"d>{{ __('Deposite date.') }}</th> 
                                                    <td class="width_65_color">  {{@$payrollDetails->cheque_deposite_date != ""? date('jS M, Y', strtotime(@$payrollDetails->cheque_deposite_date)):''}}</td>
                                                </tr>
                                            </table>
                                           
                                        </td>


                                        @elseif(@$payrollDetails->payment_mode == 2)
                                        <td  class="primary-color">
                                            <table class="invoice_table">
                                                <tr>
                                                    <th class="width_35">{{ __('Bank Name.') }}</th> 
                                                    <td class="width_65_color"> : {{@$payrollDetails->bank_name}}</td>
                                                </tr>
                                                <tr>
                                                    <th class="width_35">{{ __('Cheque No.') }}</th> 
                                                    <td class="width_65_color"> : {{@$payrollDetails->cheque_no}}</td>
                                                </tr>
                                                <tr>
                                                    <th class="width_35"d>{{ __('Cheque issue date.') }}</th> 
                                                    <td class="width_65_color"> : {{@$payrollDetails->cheque_deposite_date != ""? date('jS M, Y', strtotime(@$payrollDetails->cheque_deposite_date)):''}}.</td>
                                                </tr>
                                            </table>
                                           
                                        </td>
                                        @endif



                                    </tr>

                                    <tr>
                                        <td>
                                            <div class="signature">
                                                
                                            </div>
                                        </td>
                                        <td>
                                            <div class="signature">
                                                <p>{{ __('Signature') }}</p>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

 
</body>
</html>

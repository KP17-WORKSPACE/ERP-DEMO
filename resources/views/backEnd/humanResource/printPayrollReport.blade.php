@php
    $modules = [];
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    foreach($permissions as $permission){ @$module_links[] = @$permission->module_link_id; @$modules[] = @$permission->moduleLink->module_id;}


    $modules = array_unique(@$modules);


    @$generalSetting=App\SmGeneralSettings::where('id',1)->first();
    @$currency_symbol = @$generalSetting->currency_symbol;
    if(isset($generalSetting->logo)){  @$logo = @$generalSetting->logo;  }
    else{ @$logo = 'public/uploads/settings/logo.png'; }

    $sm_staff= App\SmStaff::where('user_id',Auth::user()->id)->first();
    if(!empty(@$sm_staff)){
        @$profile_image = @$sm_staff->staff_photo;
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
<link  href="{{asset('/public/css')}}/PayrollReportPrint.css"  rel="stylesheet">
</head>
<body>
    <section class="admin-visitor-area" >
        <div class="container-fluid cs_print">
            <div class="row mt-40">
                <div class="col-lg-12">
                    <div class="row" id="purchaseInvoice">
                        <div class="container-fluid">
                            <div class="row mb-20">
                                <table class="payroll_report_main_table">
                                    <tr>
                                        <td class="payroll_report_main_table_td">
                                            <div class="business-info text-center">
                                                <img src="{{asset($logo)}}"  class="payroll_report_main_table_img">
                                                <h3 class="mt-10 primary-color" class="payroll_report_main_table_h3">@if(isset($schoolDetails)){{@$schoolDetails->school_name}} @endif</h3>
                                                <div class="payroll_report_main_table_div">
                                                    <p class="payroll_report_main_table_p1"></p>
                                                    <p class="payroll_report_main_table_p2">@if(isset($schoolDetails)){{@$schoolDetails->address}} @endif</p>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="primary-color"  class="payroll_report_main_table_td2">
                                            <table class="table table-bordered" class="payroll_report_content">
                                                
                                                    <thead>
                                                        <tr>
                                                            <th>@lang('lang.staff') @lang('lang.name')</th>
                                                            <th>@lang('lang.month') - @lang('lang.year')</th>
                                                            <th>@lang('lang.payslip') #</th>
                                                            <th>@lang('lang.basic_salary')({{'BDT'}})</th>
                                                            <th>@lang('lang.earnings')({{'BDT'}})</th>
                                                            <th>@lang('lang.deductions')({{'BDT'}})</th>
                                                            <th>@lang('lang.gross_salary')({{'BDT'}})</th>
                                                            <th>@lang('lang.tax')({{'BDT'}})</th>
                                                            <th>@lang('lang.net_salary')({{'BDT'}})</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                      @php 
                                                        $basic_salary = 0; 
                                                        $earnings = 0;
                                                        $deductions = 0;
                                                        $gross_salary = 0;
                                                        $tax = 0;
                                                        $net_salary = 0;
                                                     @endphp
                                                      @foreach($staffsPayroll as $value)
                                                      <tr>
                                                        <td>{{@$value->full_name}}</td>
                                                        <td>{{@$value->payroll_month}} - {{@$value->payroll_year}}</td>
                                                        <td>{{@$value->id}}</td>
                                                        <td>{{App\User::NumberToBangladeshiTakaFormat(@$value->basic_salary)}}</td>
                                                        <td>
                                                            @php
                                                            $totalEarnings = App\SmHrPayrollEarnDeduc::getTotalEarnings(@$value->id);
                                                            @endphp
                                                            @if(@$totalEarnings>0)
                                                            {{@$totalEarnings}}
                                                            @php @$earnings +=@$totalEarnings; @endphp
                                                            @else
                                                            {{0}}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @php
                                                            $totalDeductions = App\SmHrPayrollEarnDeduc::getTotalDeductions(@$value->id);
                                                            @endphp
                                                            @if(@$totalDeductions>0)
                                                            {{@$totalDeductions}}
                                                            @php @$deductions +=@$totalDeductions; @endphp
                                                            @else
                                                            {{0}}
                                                            @endif
                                                        </td>
                                                        <td>{{App\User::NumberToBangladeshiTakaFormat(@$value->gross_salary)}}</td>
                                                        <td>{{App\User::NumberToBangladeshiTakaFormat(@$value->tax)}}</td>
                                                        <td>{{App\User::NumberToBangladeshiTakaFormat(@$value->net_salary)}}</td>
                                                        @php 
                                                        @$basic_salary += @$value->basic_salary;
                                                        @$gross_salary += @$value->gross_salary;
                                                        @$tax += @$value->tax;
                                                        @$net_salary += @$value->net_salary;
                                                        @endphp
                                                    </tr>
                                                    @endforeach

                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th></th>
                                                        <th></th>
                                                        <th>@lang('lang.grand_total')</th>
                                                        <th>{{App\User::NumberToBangladeshiTakaFormat(@$basic_salary)}}</th>
                                                        <th>{{App\User::NumberToBangladeshiTakaFormat(@$earnings)}}</th>
                                                        <th>{{App\User::NumberToBangladeshiTakaFormat(@$deductions)}}</th>
                                                        <th>{{App\User::NumberToBangladeshiTakaFormat(@$gross_salary)}}</th>
                                                        <th>{{App\User::NumberToBangladeshiTakaFormat(@$tax)}}</th>
                                                        <th>{{App\User::NumberToBangladeshiTakaFormat(@$net_salary)}}</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
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
    </script>
</body>
</html>



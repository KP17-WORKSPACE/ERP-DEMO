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
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('lang.loan') @lang('lang.view')</h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                <a href="#">@lang('lang.loan') @lang('lang.view')</a>
            </div>
        </div>
    </div>
</section>
<section class="student-details mb-40">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-lg-4 no-gutters">
                <div class="main-title">
                    <h3 class="mb-30">@lang('lang.staff') @lang('lang.info')</h3>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="student-meta-box">
                    <div class="student-meta-top staff-meta-top"></div>
                    <img class="student-meta-img img-100" src="{{asset(@$staffDetails->staff_photo)}}"  alt="">
                    <div class="white-box">
                        <div class="single-meta mt-20">
                            <div class="row">
                                <div class="col-lg-3 col-md-6">
                                    <div class="name">
                                        @lang('lang.name')
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="value text-left">
                                        @if(isset($staffDetails)){{@$staffDetails->full_name}}@endif
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="name">
                                        @lang('lang.staff_no')
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="value text-left">
                                        @if(isset($staffDetails)){{@$staffDetails->staff_no}}@endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="single-meta">
                            <div class="row">
                                <div class="col-lg-3 col-md-6">
                                    <div class="name">
                                        @lang('lang.mobile')
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="value text-left">
                                       @if(isset($staffDetails)){{@$staffDetails->mobile}}@endif
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="name">
                                        @lang('lang.email')
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="value text-left">
                                        @if(isset($staffDetails)){{@$staffDetails->email}}@endif
                                    </div>
                                </div>
                               
                            </div>
                        </div>
                        <div class="single-meta">
                            <div class="row">
                                <div class="col-lg-3 col-md-6">
                                    <div class="name">
                                        @lang('lang.role')
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="value text-left">
                                        @if(isset($staffDetails)){{@$staffDetails->roles->name}}@endif
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="name">
                                        @lang('lang.department')
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="value text-left">
                                        @if(isset($staffDetails)){{@$staffDetails->departments->name}}@endif
                                    </div>
                                </div>
                                 
                            </div>
                        </div>
                        <div class="single-meta">
                            <div class="row">
                                <div class="col-lg-3 col-md-6">
                                    <div class="name">
                                        @lang('lang.designation')
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="value text-left">
                                       @if(isset($staffDetails)){{@$staffDetails->designations->title}}@endif
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="name">
                                        @lang('lang.date_of_joining')
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="value text-left">
                                        @if(isset($staffDetails))
                                            {{date('jS M, Y', strtotime(@$staffDetails->date_of_joining))}}
                                        @endif
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
            <div class="col-lg-7">
                <div class="row">
                    <div class="col-lg-4 no-gutters">
                        <div class="main-title">
                            <h3 class="mb-30">@lang('lang.loan') @lang('lang.list')</h3>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        
                        <table class="display school-table school-table-style" cellspacing="0" width="100%">

                            <thead>
                                <tr>
                                    <th>@lang('lang.date')</th>
                                    <th>@lang('lang.note')</th>
                                    <th>@lang('lang.amount') ({{@$currency_symbol}})</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($loan_lists as $value)
                                <tr>
                                    <td>{{date('jS M, Y', strtotime(@$value->date))}}</td>
                                    <td>{{$value->note}}</td>
                                    <td>{{App\User::NumberToBangladeshiTakaFormat(@$value->amount)}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="row">
                    <div class="col-lg-4 no-gutters">
                        <div class="main-title">
                            <h3 class="mb-30">@lang('lang.deduct') @lang('lang.list')</h3>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        
                        <table class="display school-table school-table-style" cellspacing="0" width="100%">

                            <thead>
                                <tr>
                                    <th>@lang('lang.type')</th>
                                    <th>@lang('lang.date')</th>
                                    <th>@lang('lang.amount') ({{@$currency_symbol}})</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($deduct_lists as $deduct_list)
                                @php 
                                    $deduct_list = App\SmHrPayrollGenerate::deductionList(@$deduct_list->id);

                                @endphp
                                @foreach($deduct_list as $value)
                                <tr>
                                    <td>{{@$value->type_name}}</td>
                                    <td>{{date('jS M, Y', strtotime(@$value->created_at))}}</td>
                                    <td>{{App\User::NumberToBangladeshiTakaFormat(@$value->amount)}}</td>
                                </tr>
                                @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- End Modal Area -->
@endsection

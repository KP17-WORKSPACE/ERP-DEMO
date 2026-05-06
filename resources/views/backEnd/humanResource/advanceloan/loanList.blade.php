@extends('backEnd.master')
@section('mainContent')
@php 
    $modules = [];
    $module_links = [];
    @$permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();

    foreach($permissions as $permission){ @$module_links[] = @$permission->module_link_id; @$modules[] = @$permission->moduleLink->module_id;}
 
    $modules = array_unique(@$modules);

    @$generalSetting=App\SmGeneralSettings::where('id',1)->first();
    @$currency_symbol = @$generalSetting->currency_symbol; 
    if(isset($generalSetting->logo)){  @$logo = @$generalSetting->logo;  }
    else{ @$logo = 'public/uploads/settings/logo.png'; } 
 
@endphp
<link href="{{asset('public/css/add_loan.css')}}" type="text/css" rel="stylesheet">


<section class="sms-breadcrumb mb-40 white-box">
  <div class="container-fluid">
    <div class="row justify-content-between">
      <h1>@lang('lang.advance_salary')/@lang('lang.loan') @lang('lang.list')</h1>
      <div class="bc-pages">
        <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
        <a href="#">@lang('lang.inventory')</a>
        <a href="#">@lang('lang.advance_salary')/@lang('lang.loan') @lang('lang.list')</a>
      </div>
    </div>
  </div>
</section>
<section class="admin-visitor-area">
  <div class="container-fluid p-0">
    <div class="row">

      <div class="col-lg-12">
      
      <div class="row">
        <div class="col-lg-4 no-gutters">
          <div class="main-title">
            <h3 class="mb-0"> @lang('lang.loan') @lang('lang.list')</h3>
          </div>
        </div>
      </div>

      <div class="row">

        <div class="col-lg-12">
          <table id="table_id" class="display school-table" cellspacing="0" width="100%">

            <thead>
              @if(session()->has('message-success-delete') != "" ||
              session()->get('message-danger-delete') != "")
              <tr>
                  <td colspan="6">
                       @if(session()->has('message-success-delete'))
                        <div class="alert alert-success">
                            {{ session()->get('message-success-delete') }}
                        </div>
                      @elseif(session()->has('message-danger-delete'))
                        <div class="alert alert-danger">
                            {{ session()->get('message-danger-delete') }}
                        </div>
                      @endif
                  </td>
              </tr>
               @endif
              <tr>
                <th> @lang('lang.issue_to')</th>
                <th> @lang('lang.total') @lang('lang.amount') ({{@$currency_symbol}})</th>
                <th> @lang('lang.deduct') @lang('lang.amount') ({{@$currency_symbol}})</th>
                <th> @lang('lang.due') @lang('lang.amount') ({{@$currency_symbol}})</th>
                <th> @lang('lang.action')</th>
              </tr>
            </thead>

            <tbody>
              @foreach($loan_lists as $key => $loan_list)


              @php

                $staffDetail = App\SmAdvanceloan::staffDetail($key); 

              @endphp


              <tr>

                

                <td> {{$staffDetail->full_name}} </td>

                  <td>
                    @php $total_amount = 0; @endphp
                    @foreach($loan_list as $value)
                        @php $total_amount = @$total_amount + @$value->amount; @endphp
                    @endforeach
                    {{App\User::NumberToBangladeshiTakaFormat(@$total_amount)}}
                  </td>
                  <td>
                      @php 
                        @$total_deduct = App\SmAdvanceloan::totalDeduction(@$key); 
                      @endphp
                      {{App\User::NumberToBangladeshiTakaFormat(@$total_deduct)}}

                  </td> 

                  <td>{{App\User::NumberToBangladeshiTakaFormat(@$total_amount - @$total_deduct)}}</td>

                   <td>
                   @if($value->is_return == 0)
                    <div class="dropdown">
                      <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">
                          @lang('lang.select')
                      </button>

                      <div class="dropdown-menu dropdown-menu-right">
                      @if(in_array(268, @$module_links) ||  Auth::user()->role_id == 1)
                       <a class="dropdown-item" href="{{url('loan-view/'.@$key)}}">@lang('lang.view')</a>
                      @endif

                    </div>

                   </div>
                   @endif
                 </td>
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

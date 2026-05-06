@extends('backEnd.master')
@section('mainContent')
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('lang.cost_center') @lang('lang.report')</h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                <a href="#">@lang('lang.reports')</a>
                <a href="#">@lang('lang.cost_center') @lang('lang.report')</a>
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
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'cost-center-report-search', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'search_student']) }}
                            <div class="row">
                                <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">


                                <div class="col-lg-3 mt-30-md">
                                    <div class="no-gutters input-right-icon">
                                        <div class="col">
                                            <div class="input-effect">

                                                 <select class="niceSelect w-100 bb form-control{{ @$errors->has('cost_center') ? ' is-invalid' : '' }}" name="cost_center" id="cost_center">
                                                    <option data-display="Select Cost Center *" value="all">Select Cost Center *</option>
                                                    @foreach($cost_centers as $row)
                                                        <option value="{{@$row->id}}">{{@$row->name}}</option>
                                                    @endforeach     

                                                        <option value="all">All</option>                                     
                                                </select>
                                                @if ($errors->has('cost_center'))
                                                    <span class="invalid-feedback invalid-select" role="alert">
                                                        <strong>{{ @$errors->first('cost_center') }}</strong>
                                                    </span>
                                                @endif


                                            </div>
                                        </div> 
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <select class="niceSelect w-100 bb form-control{{ @$errors->has('filtering') ? ' is-invalid' : '' }}" name="expense_head" id="filtering_section">
                                        <option data-display="Select Head " value="">@lang('lang.select') @lang('lang.head')</option>
                                        @foreach($expense_heads as $row)
                                        <option value="{{@$row->id}}">{{@$row->head}}</option>
                                        @endforeach
                                    </select>
                                </div>


                                <div class="col-lg-3 mt-30-md">
                                    <div class="no-gutters input-right-icon">
                                        <div class="col">
                                            <div class="input-effect">
                                                <input class="primary-input date form-control{{ @$errors->has('date_from') ? ' is-invalid' : '' }}" id="startDate" type="text"
                                                     name="date_from" value="{{date('m/d/Y')}}" readonly>
                                                    <label>@lang('lang.date_from')</label>
                                                    <span class="focus-border"></span>
                                                @if (@$errors->has('date_from'))
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


                                <div class="col-lg-3 mt-30-md">
                                    <div class="no-gutters input-right-icon">
                                        <div class="col">
                                            <div class="input-effect">
                                                <input class="primary-input date form-control{{ @$errors->has('date_to') ? ' is-invalid' : '' }}" id="startDate" type="text"
                                                     name="date_to" value="{{date('m/d/Y')}}" readonly>
                                                    <label>@lang('lang.date_to')</label>
                                                    <span class="focus-border"></span>
                                                @if ($errors->has('date_to'))
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
            
 

 


@if(isset($all_expenses))


            <div class="row mt-40">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-6 no-gutters">
                            <div class="main-title">
                                <h3 class="mb-0">@lang('lang.expense') @lang('lang.result')</h3>
                            </div>
                        </div>
                    </div>

                <?php 
                    @$generalSetting=App\SmGeneralSettings::where('id',1)->first();
                    @$currency_symbol = @$generalSetting->currency_symbol; 
                ?>
                    <!-- </div> -->
                    <div class="row">
                        <div class="col-lg-12">
                            <table id="table_id" class="display school-table" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>@lang('lang.name')</th>
                                        <th>@lang('lang.expense') @lang('lang.head')</th>
                                        <th>@lang('lang.cost_center')</th>
                                        <th>@lang('lang.payment_method')</th>
                                        <th>@lang('lang.date')</th>
                                        <th>@lang('lang.amount')({{@$currency_symbol}})</th>
                                    </tr>
                                </thead>
                                @php @$total_expense = 0;@endphp
 
                                <tbody>
                                    @foreach($all_expenses as $add_expense)
                                    @php @$total_expense = @$total_expense + @$add_expense->amount; @endphp
                                    <tr>
                                        <td>{{@$add_expense->name}}</td>
                                        <td>{{@$add_expense->ACHead!=""?@$add_expense->ACHead->head:""}}</td>
                                        <td>{{App\SmCostCenter::getCostCenterName(@$add_expense->cost_center_id)}}</td>
                                        <td>{{@$add_expense->paymentMethod!=""?@$add_expense->paymentMethod->method:""}}</td>
                                        <td>{{@date('jS M, Y', strtotime(@$add_expense->date))}}</td>
                                        <td>{{@App\SysHelper::com_curr_format(@$add_expense->amount, 2)}}</td>
                                    </tr>
                                    @endforeach 



                                    @foreach($daily_expenses as $add_expense)
                                    @php @$total_expense = @$total_expense + @$add_expense->amount; @endphp
                                    <tr>
                                        <td>{{@$add_expense->name}}</td>
                                        <td>{{@$add_expense->expenseHead != ""? @$add_expense->expenseHead->head:""}}</td>

                                        <td>{{App\SmCostCenter::getCostCenterName(@$add_expense->cost_center_id)}}</td>
                                        <td></td>
                                        <td>{{date('jS M, Y', strtotime(@$add_expense->date))}}</td>
                                        <td>{{@App\SysHelper::com_curr_format(@$add_expense->amount, 2)}}</td>
                                    </tr>
                                    @endforeach


                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th>@lang('lang.grand_total')</th>
                                        <th></th>
                                        <th>{{@App\SysHelper::com_curr_format(@$total_expense, 2)}}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

@endif


    </div>
</section>


@endsection

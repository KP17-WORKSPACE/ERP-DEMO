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
            <h1>@lang('lang.income') @lang('lang.expense') @lang('lang.report')</h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                <a href="#">@lang('lang.accounts')</a>
                <a href="#">@lang('lang.income') @lang('lang.expense') @lang('lang.report')</a>
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
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'search_account', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'search_income_expense']) }}
                            <div class="row">
                                <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                                <div class="col-lg-3 mt-30-md">
                                    <div class="no-gutters input-right-icon">
                                        <div class="col">
                                            <div class="input-effect">
                                                <input class="primary-input date form-control{{ $errors->has('date_from') ? ' is-invalid' : '' }}" id="startDate" type="text"
                                                     name="date_from" value="{{isset($from_date)? date('m/d/Y', strtotime($from_date)):date('m/d/Y')}}" readonly>
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
                                <div class="col-lg-3 mt-30-md">
                                    <div class="no-gutters input-right-icon">
                                        <div class="col">
                                            <div class="input-effect">
                                                <input class="primary-input date form-control{{ $errors->has('date_to') ? ' is-invalid' : '' }}" id="startDate" type="text"
                                                     name="date_to" value="{{isset($to_date)? date('m/d/Y', strtotime($to_date)):date('m/d/Y')}}" readonly>
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
                                <div class="col-lg-3">
                                    <select class="niceSelect w-100 bb form-control{{ $errors->has('type') ? ' is-invalid' : '' }}" name="type" id="account-type">
                                        <option data-display="Select Type *" value="">@lang('lang.search') @lang('lang.type')*</option>
                                        <option value="In" {{isset($type_id)? (@$type_id == "In"? 'selected':''):''}}>@lang('lang.income')</option>
                                        <option value="Ex" {{isset($type_id)? (@$type_id == "Ex"? 'selected':''):''}}>@lang('lang.expense')</option>
                                    </select>
                                    @if ($errors->has('type'))
                                    <span class="invalid-feedback invalid-select" role="alert">
                                        <strong>{{ $errors->first('type') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="col-lg-3" id="filtering_div">
                                    <select class="niceSelect w-100 bb form-control{{ $errors->has('filtering') ? ' is-invalid' : '' }}" name="filtering" id="filtering_section">
                                    </select>
                                    @if ($errors->has('type'))
                                    <span class="invalid-feedback invalid-select" role="alert">
                                        <strong>{{ $errors->first('type') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="col-lg-3" id="income_div">
                                    <select class="niceSelect w-100 bb form-control{{ $errors->has('filtering') ? ' is-invalid' : '' }}" name="filtering_income" id="filtering_section">
                                        <option data-display="Select Head " value="all">@lang('lang.select') @lang('lang.head')</option>
                                        @foreach($income_heads as $row)
                                        <option value="{{$row->id}}">{{@$row->head}}</option>
                                        @endforeach
                                    </select>
                                    
                                </div>
                                <div class="col-lg-3" id="expense_div">
                                    <select class="niceSelect w-100 bb form-control{{ $errors->has('filtering') ? ' is-invalid' : '' }}" name="filtering_expense" id="filtering_section">
                                        <option data-display="Select Head " value="all">@lang('lang.select') @lang('lang.head')</option>
                                        @foreach($expense_heads as $row)
                                        <option value="{{@$row->id}}">{{@$row->head}}</option>
                                        @endforeach
                                    </select>
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




            
@if(isset($add_incomes))


            <div class="row mt-40">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-6 no-gutters">
                            <div class="main-title">
                                <h3 class="mb-0">@lang('lang.income') @lang('lang.result')</h3>
                            </div>
                        </div>
                    </div>

                
                    <div class="row">
                        <div class="col-lg-12">
                            <table id="table_id" class="display school-table" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>@lang('lang.name')</th>
                                        <th>@lang('lang.income_Head')</th>
                                        <th>@lang('lang.amount')({{@$currency_symbol }})</th>
                                    </tr>
                                </thead>
                                @php $total_income = 0;@endphp
                                <tbody>
                                    @foreach($add_incomes as $add_income)
                                    @php $total_income = @$total_income + @$add_income->amount; @endphp
                                    <tr>
                                        <td>{{@$add_income->name}}</td>
                                        <td>{{@$add_income->ACHead!=""?@$add_income->ACHead->head:""}}</td>
                                        <td>{{@App\SysHelper::com_curr_format(@$add_income->amount, 2)}}</td>
                                    </tr>
                                    @endforeach 
                                    @if($fees_payments != "")
                                        @php $total_income = @$total_income + @$fees_payments; @endphp
                                        <tr>
                                            <td>@lang('lang.fees_collection')</td>
                                            <td>@lang('lang.fees')</td>
                                            <td>{{@App\SysHelper::com_curr_format(@$fees_payments, 2)}}</td>
                                        </tr>
                                    @endif
                                    @if($item_sells != "")
                                    @php @$total_income = @$total_income + @$item_sells; @endphp
                                    <tr>
                                        <td>@lang('lang.item_sell')</td>
                                        <td>@lang('lang.sells')</td>
                                        <td>{{@App\SysHelper::com_curr_format(@$item_sells, 2)}}</td>
                                    </tr>
                                    @endif
                                    @if($dormitory != 0)
                                    @php $total_income = @$total_income + @$dormitory; @endphp
                                    <tr>
                                        <td>@lang('lang.dormitory') @lang('lang.fees')</td>
                                        <td>@lang('lang.dormitory')</td>
                                        <td>{{@App\SysHelper::com_curr_format(@$dormitory, 2)}}</td>
                                    </tr>
                                    @endif
                                    
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th>@lang('lang.grand_total')</th>
                                        <th>{{@App\SysHelper::com_curr_format(@$total_income, 2)}}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

@endif

@if(isset($add_expenses))


            <div class="row mt-40">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-6 no-gutters">
                            <div class="main-title">
                                <h3 class="mb-0">@lang('lang.expense') @lang('lang.result')</h3>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <table id="table_id" class="display school-table" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>@lang('lang.name')</th>
                                        <th>@lang('lang.expense_head')</th>
                                        <th>@lang('lang.amount')({{@$currency_symbol}})</th>
                                    </tr>
                                </thead>
                                @php $total_expense = 0;@endphp
                                <tbody>
                                    @foreach($add_expenses as $add_expense)
                                    @php $total_expense = @$total_expense + @$add_expense->amount; @endphp
                                    <tr>
                                        <td>{{@$add_expense->name}}</td>
                                        <td>{{@$add_expense->ACHead!=""?@$add_expense->ACHead->head:""}}</td>
                                        <td>{{@App\SysHelper::com_curr_format(@$add_expense->amount, 2)}}</td>
                                    </tr>
                                    @endforeach
                                    @if(@$item_receives != 0)
                                    @php @$total_expense = @$total_expense + @$item_receives; @endphp
                                    <tr>
                                        <td>@lang('lang.item') @lang('lang.purchase')</td>
                                        <td>@lang('lang.purchase')</td>
                                        <td>{{@App\SysHelper::com_curr_format(@$item_receives, 2)}}</td>
                                    </tr>
                                    @endif
                                    @if(@$payroll_payments != 0)
                                    @php $total_expense = @$total_expense + @$payroll_payments; @endphp
                                    <tr>
                                        <td>@lang('lang.from') @lang('lang.payroll')</td>
                                        <td>@lang('lang.payroll')</td>
                                        <td>{{@App\SysHelper::com_curr_format(@$payroll_payments, 2)}}</td>
                                    </tr>
                                    @endif  
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th>@lang('lang.grand_total')</th>
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

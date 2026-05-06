@extends('backEnd.master')
@section('mainContent')
<?php 
    @$generalSetting=App\SmGeneralSettings::where('id',1)->first();
    @$currency_symbol = @$generalSetting->currency_symbol; 
?>
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('lang.income') @lang('lang.statement') @lang('lang.report')</h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                <a href="#">@lang('lang.reports')</a>
                <a href="#">@lang('lang.income') @lang('lang.statement') @lang('lang.report')</a>
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
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'income-statement', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'search_student']) }}
                            <div class="row">
                                <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                                <div class="col-lg-3 mt-30-md">
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
                                <div class="col-lg-3 mt-30-md">
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
                                <div class="col-lg-3">

                                        <select class="niceSelect w-100 bb form-control{{ $errors->has('income_head') ? ' is-invalid' : '' }}" name="income_head"  id="expense-head">
                                            <option data-display="@lang('lang.a_c_Head')" value="">@lang('lang.a_c_Head')</option>
                                            @foreach($income_heads as $income_head)
                                                @if(isset($add_income))
                                                <option value="{{@$income_head->id}}"
                                                    {{@$add_income->income_head_id == @$income_head->id? 'selected': ''}}>{{@$income_head->head}}</option>
                                                @else
                                                <option value="{{@$income_head->id}}" {{old('income_head') == @$income_head->id? 'selected' : ''}}>{{@$income_head->head}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        @if ($errors->has('income_head'))
                                        <span class="invalid-feedback invalid-select" role="alert">
                                            <strong>{{ $errors->first('income_head') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                     <div class="col-lg-3" id="sub-head-div">
                                         <select class="niceSelect w-100 bb form-control{{ $errors->has('sub_head') ? ' is-invalid' : '' }}" name="sub_head" id="sub-head">
                                            <option data-display="Sub Head" value="">sub head</option>
                                            @if(isset($sub_heads))
                                            @foreach($sub_heads as $sub_head)
                                                <option value="{{@$sub_head->id}}" {{@$expense->sub_head_id == $sub_head->id? 'selected':''}}>{{@$sub_head->sub_head}}</option>
                                            @endforeach
                                            @endif


                                            
                                        </select>
                                        @if ($errors->has('sub_head'))
                                            <span class="invalid-feedback invalid-select" role="alert">
                                                <strong>{{ $errors->first('sub_head') }}</strong>
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
                    <!-- </div> -->
                    <div class="row">
                        <div class="col-lg-12">
                            <table id="table_id" class="display school-table" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>@lang('lang.name')</th>
                                        <th>@lang('lang.income_Head')</th>
                                        <th>@lang('lang.payment_method')</th>
                                        <th>@lang('lang.date')</th>
                                        <th>@lang('lang.amount')({{$currency_symbol}})</th>
                                    </tr>
                                </thead>
                                @php $total_income = 0; @endphp
                                <tbody>
                                    @foreach($add_incomes as $add_income)

                                    @php
                                        $name=$add_income->name;
                                        $check_invoice=Illuminate\Support\Str::contains($name, 'invoiceNnumber');
                                        $invoice_text_array=explode("_",$name);
                                        $invoice_number=$invoice_text_array[array_key_last ($invoice_text_array)];
                                     @endphp



                                    @php $total_income = $total_income + $add_income->amount; @endphp
                                    <tr>
                                        @if ($check_invoice)
                                    <td><a target="_blank" href="{{url('infix/invoice-view/'.$invoice_number)}}">Inv-{{@$invoice_text_array[1]}}</a></td>
                                        @else
                                        <td>{{@$add_income->name}}</td>
                                        @endif
                                        <td>{{$add_income->ACHead!=""?$add_income->ACHead->head:""}}</td>
                                        <td>{{$add_income->paymentMethod!=""?$add_income->paymentMethod->method:""}}</td>
                                        <td>{{date('jS M, Y', strtotime($add_income->date))}}</td>
                                        <td>{{@App\SysHelper::com_curr_format($add_income->amount, 2)}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th>@lang('lang.grand_total')</th>
                                        <th></th>
                                        <th>{{@App\SysHelper::com_curr_format($total_income, 2)}}</th>
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

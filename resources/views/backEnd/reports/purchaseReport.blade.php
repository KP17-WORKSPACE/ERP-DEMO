@extends('backEnd.master')
@section('mainContent')
<?php 
    @$generalSetting=App\SmGeneralSettings::where('id',1)->first();
    @$currency_symbol = @$generalSetting->currency_symbol; 
?>
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('lang.purchase') @lang('lang.report')</h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                <a href="#">@lang('lang.reports')</a>
                <a href="#">@lang('lang.purchase') @lang('lang.report')</a>
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
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'purchase-report', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'search_student']) }}
                            <div class="row">
                                <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                                <div class="col-lg-4 mt-30-md">
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
                                <div class="col-lg-4 mt-30-md">
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
                                <div class="col-lg-4">

                                        <select class="niceSelect w-100 bb form-control{{ $errors->has('head') ? ' is-invalid' : '' }}" name="item"  id="item">
                                            <option data-display="@lang('lang.item')" value="">@lang('lang.item')</option>
                                            @foreach($items as $item)
                                                <option value="{{@$item->id}}" >{{@$item->item_name}}</option>
                                              
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
            
 


@if(isset($purchase_items))


            <div class="row mt-40">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-6 no-gutters">
                            <div class="main-title">
                                <h3 class="mb-30">@lang('lang.result')</h3>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <table class="display school-table school-table-style" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>@lang('lang.date')</th>
                                        <th> @lang('lang.product_name') </th>
                                        <th> @lang('lang.supplier_name') </th>
                                        <th> @lang('lang.received_date') </th>
                                        <th> @lang('lang.denomination') </th>
                                        <th> @lang('lang.quantity') </th>
                                        <th>@lang('lang.unit_price') ({{ @$currency_symbol}})</th>
                                        <th>@lang('lang.sub_total') ({{ @$currency_symbol}})</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $total = 0;
                                    @$total_quantity = 0; @endphp
                                    @foreach($purchase_items as $purchase_item)
                                        @php 
                                            @$total = @$total + @$purchase_item->unit_price * @$purchase_item->qnt;
                                            @$total_quantity = @$total_quantity + @$purchase_item->qnt;
                                        @endphp

                                    <tr>
                                        <td>{{date('jS M, Y', strtotime(@$date_from)) .' to '. date('jS M, Y', strtotime(@$date_to)) }}</td>

                                        <td>{{App\SmItem::getItemName(@$purchase_item->product_id)}}</td>
                               
                                        <td>{{@$purchase_item->supplier !=""? @$purchase_item->supplier->company_name:""}}</td>
                                        <td>{{ date('jS M, Y', strtotime(@$purchase_item->received_date)) }}</td>
                                        <td>{{@$purchase_item->denomination}}</td>
                                        <td>{{@$purchase_item->qnt}}</td>
                                        <td>{{App\User::NumberToBangladeshiTakaFormat(  @$purchase_item->unit_price )}}</td>
                                        <td>{{App\User::NumberToBangladeshiTakaFormat(  @$purchase_item->unit_price * @$purchase_item->qnt)}}</td>

                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <th></th>
                                    <th> </th>
                                    <th> </th>
                                    <th> </th>
                                    <th> @lang('lang.total'): </th>
                                    <th> {{@$total_quantity}}</th>
                                    <th></th>
                                    <th>{{App\User::NumberToBangladeshiTakaFormat(  @$total )}} ({{ @$currency_symbol}})</th>
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

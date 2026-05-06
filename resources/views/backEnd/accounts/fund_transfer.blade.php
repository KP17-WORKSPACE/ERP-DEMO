@extends('backEnd.master')
@section('mainContent')
@php
    $modules = [];
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();

 
    foreach($permissions as $permission){ @$module_links[] = @$permission->module_link_id; @$modules[] = @$permission->moduleLink->module_id;}

 
    $modules = array_unique(@$modules);

    function showPicName($data){
        $name = explode('/', $data);
        return @$name[3];
    }
       @$generalSetting=App\SmGeneralSettings::where('id',1)->first();
    @$currency_symbol = @$generalSetting->currency_symbol; 
@endphp
    <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('lang.transfer')</h1>
                <div class="bc-pages">
                    <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                    <a href="{{ url('sub-account') }}">@lang('lang.accounts')</a>
                    <a href="#">@lang('lang.transfer')</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area">
        <div class="container-fluid p-0">
            @if(isset($fund_transfer))
            @if(in_array(244, @$module_links) || Auth::user()->role_id == 1)
                <div class="row">
                    <div class="offset-lg-10 col-lg-2 text-right col-md-12 mb-20">
                        <a href="{{url('transfer')}}" class="primary-btn small fix-gr-bg">
                            <span class="ti-plus pr-2"></span>
                            @lang('lang.add')
                        </a>
                    </div>
                </div>
            @endif
            @endif
            <div class="row">
                <div class="col-lg-3">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="main-title">
                                <h3 class="mb-30">
                                    @if(isset($fund_transfer))
                                        @lang('lang.edit')
                                    @else
                                        @lang('lang.add')
                                    @endif
                                    @lang('lang.transfer')
                                </h3>
                            </div>
                            @if(isset($fund_transfer))
                                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'transfer-update',
                                'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'fund-transfer']) }}
                            @else
                            @if(in_array(244, @$module_links) || Auth::user()->role_id == 1)
                                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'transfer-store',
                                'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'fund-transfer']) }}
                            @endif
                            @endif

                            <input type="hidden" name="investment_amount" id="investment_amount" value="{{isset($fund_transfer)? @$investment_amount + @$fund_transfer->amount:@$investment_amount}}">
                            <input type="hidden" name="id" value="{{isset($fund_transfer)? @$fund_transfer->id:""}}">

                            <div class="white-box">
                                <div class="add-visitor">
                                    <div class="row">
                                        <div class="col-lg-12"> 
                                            @if(session()->has('message-success'))
                                                <div class="alert alert-success">
                                                   {{session()->get('message-success')}}
                                                </div>
                                            @elseif(session()->has('message-danger'))
                                                <div class="alert alert-danger">
                                                    {{session()->get('message-danger')}}
                                                </div>
                                            @endif


                                             <select class="niceSelect w-100 bb form-control{{ $errors->has('from') ? ' is-invalid' : '' }}" name="from" id="from">
                                                <option data-display="from *" value="">@lang('lang.from') *</option>
                                                <option value="investment" {{isset($fund_transfer)? 'selected':""}}>@lang('lang.investment')</option>
                                            </select>
                                            @if ($errors->has('from'))
                                                <span class="invalid-feedback invalid-select" role="alert">
                                                    <strong>{{ $errors->first('from') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>


                                    <div class="row mt-40">
                                        <div class="col-lg-12"> 
                                             <select class="niceSelect w-100 bb form-control{{ $errors->has('to_account') ? ' is-invalid' : '' }}" name="to_account" id="to_account">
                                                <option data-display="to @lang('lang.account') *" value="">@lang('lang.to') @lang('lang.account') *</option>
                                                @foreach($banks as $value)
                                                    <option value="{{@$value->id}}" {{isset($fund_transfer)? (@$fund_transfer->bank_account_id == @$value->id? 'selected':""):""}}>{{@$value->account_name}}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('to_account'))
                                                <span class="invalid-feedback invalid-select" role="alert">
                                                    <strong>{{ $errors->first('to_account') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row mt-35">
                                        <div class="col-lg-12">
                                            <div class="input-effect">
                                                <input
                                                    class="primary-input form-control{{ $errors->has('amount') ? ' is-invalid' : '' }}"
                                                    type="number" name="amount" id="amount" value="{{isset($fund_transfer)? @$fund_transfer->amount:""}}">
                                                <label>@lang('lang.amount') <span>*</span></label>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('amount'))
                                                    <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('amount') }}</strong>
                                            </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row no-gutters input-right-icon mt-40">
                                        <div class="col">
                                            <div class="input-effect">
                                                <input class="primary-input date form-control{{ $errors->has('date') ? ' is-invalid' : '' }}" id="startDate" type="text" name="date"
                                                       value="{{isset($fund_transfer)? date('m/d/Y', strtotime(@$fund_transfer->date)): date('m/d/Y')}}">
                                                <label>@lang('lang.date') <span>*</span></label>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('date'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('date') }}</strong>
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



                                    @php 
                                    $tooltip = "";
                                       if(in_array(244, @$module_links) ||  Auth::user()->role_id == 1){
                                            $tooltip = "";
                                        }else{
                                            $tooltip = "You have no permission to add";
                                        }
                                    @endphp



                                    <div class="row mt-40">
                                        <div class="col-lg-12 text-center">
                                            <button class="primary-btn fix-gr-bg" data-toggle="tooltip" title="{{@$tooltip}}">
                                                <span class="ti-check"></span>
                                                @if(isset($fund_transfer))
                                                    @lang('lang.update')
                                                @else
                                                    @if(in_array(244, @$module_links) || Auth::user()->role_id == 1)
                                                        @lang('lang.save')
                                                    @endif
                                                @endif
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>

                <div class="col-lg-9">
                    <div class="row">
                        <div class="col-lg-4 no-gutters">
                            <div class="main-title">
                                <h3 class="mb-0">@lang('lang.transfer')</h3>
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
                                        <td colspan="5">
                                            @if(session()->has('message-success-delete'))
                                                <div class="alert alert-success">
                                                    @lang('lang.deleted_message')
                                                </div>
                                            @elseif(session()->has('message-danger-delete'))
                                                <div class="alert alert-danger">
                                                    @lang('lang.error_message')
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <th>@lang('lang.from')</th>
                                    <th>@lang('lang.to') </th>
                                    <th>@lang('lang.date')</th>
                                    <th>@lang('lang.amount')({{@$currency_symbol}})</th>
                                    <th>@lang('lang.actions')</th>
                                </tr>
                                </thead>

                                <tbody> 
                                    @foreach($fund_transfers as $value)
                                        <tr>
                                            <td>@lang('lang.investment')</td>
                                            <td>{{@$value->bankDeatil != ""? @$value->bankDeatil->account_name:""}}</td>
                                            <td>{{date('jS M, Y', strtotime(@$value->date))}}</td>
                                            <td>{{App\User::NumberToBangladeshiTakaFormat(@$value->amount)}}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn dropdown-toggle"
                                                            data-toggle="dropdown">
                                                        @lang('lang.select')
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        @if(in_array(245, @$module_links) || Auth::user()->role_id == 1)
                                                        <a class="dropdown-item"
                                                           href="{{url('transfer-edit', [$value->id])}}">@lang('lang.edit')</a>
                                                        @endif
                                                        @if(in_array(246, @$module_links) || Auth::user()->role_id == 1)
                                                        <a class="dropdown-item" data-toggle="modal"
                                                           data-target="#deleteVisitorModal{{@$value->id}}"
                                                           href="#">@lang('lang.delete')</a>
                                                           @endif
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <div class="modal fade admin-query" id="deleteVisitorModal{{@$value->id}}">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">@lang('lang.delete') @lang('lang.tranfer') @lang('lang.amount')</h4>
                                                        <button type="button" class="close" data-dismiss="modal">&times;
                                                        </button>
                                                    </div>

                                                    <div class="modal-body">
                                                        <div class="text-center">
                                                            <h4>@lang('lang.are_you_sure_to_delete')</h4>
                                                        </div>

                                                        <div class="mt-40 d-flex justify-content-between">
                                                            <button type="button" class="primary-btn tr-bg"
                                                                    data-dismiss="modal">@lang('lang.cancel')
                                                            </button>

                                                            <a href="{{url('transfer-delete', [@$value->id])}}"
                                                               class="primary-btn fix-gr-bg">@lang('lang.delete')</a>

                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

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

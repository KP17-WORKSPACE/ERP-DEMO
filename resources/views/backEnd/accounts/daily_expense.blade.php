@extends('backEnd.master')
@section('mainContent')
@php
    $modules = [];
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();


    foreach($permissions as $permission){ @$module_links[] = @$permission->module_link_id; @$modules[] = @$permission->moduleLink->module_id;}


    @$modules = array_unique(@$modules);




    @$generalSetting=App\SmGeneralSettings::where('id',1)->first();
    @$currency_symbol = @$generalSetting->currency_symbol;
    if(isset($generalSetting->logo)){  @$logo = @$generalSetting->logo;  }
    else{ @$logo = 'public/uploads/settings/logo.png'; }

@endphp
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('lang.Daily_Expense')</h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                <a href="#">@lang('lang.accounts')</a>
                <a href="#">@lang('lang.Daily_Expense')</a>
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area">
    <div class="container-fluid p-0">
        @if(in_array(229, @$module_links) || Auth::user()->role_id == 1)
        @if(isset($expense))
        <div class="row">
            <div class="offset-lg-10 col-lg-2 text-right col-md-12 mb-20">
                <a href="{{url('daily-expense')}}" class="primary-btn small fix-gr-bg">
                    <span class="ti-plus pr-2"></span>
                    @lang('lang.add')
                </a>

            </div>
            @endif
        @endif
        <div class="row">
            <div class="col-lg-3">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-title">
                            <h3 class="mb-30">
                                @if(isset($expense))
                                    @lang('lang.edit')
                                @else
                                    @lang('lang.add')
                                @endif
                               @lang('lang.Daily_Expense')
                            </h3>
                        </div>
                        @if(isset($expense))
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'daily-expense-update', 'method' => 'POST']) }}
                        @else
                        @if(in_array(229, @$module_links) || Auth::user()->role_id == 1)
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'daily-expense-store', 'method' => 'POST']) }}
                        @endif
                        @endif
                        <input type="hidden" name="id" value="{{isset($expense)? @$expense->id:''}}">
                        <input type="hidden" name="url" value="{{url('/')}}">
                        <div class="white-box">
                            <div class="add-visitor">
                                <div class="row">
                                    <div class="col-lg-12">
                                        @if(session()->has('message-success'))
                                          <div class="alert alert-success">
                                              {{ session()->get('message-success') }}
                                          </div>
                                        @elseif(session()->has('message-danger'))
                                          <div class="alert alert-danger">
                                              {{ session()->get('message-danger') }}
                                          </div>
                                        @endif
                                        <input type="hidden" name="expense_head" value="{{@$heads->id}}">

                                    </div>

                                </div>
                                <div class="row mt-30 mb-20">
                                    <div class="col-lg-12" id="sub-head-div">
                                         <select class="niceSelect w-100 bb form-control{{ @$errors->has('sub_head') ? ' is-invalid' : '' }}" name="sub_head" id="sub-head">
                                            <option data-display="Select Account*" value="">@lang('lang.select') @lang('lang.account') *</option>

                                            @foreach($sub_heads as $sub_head)
                                                <option value="{{@$sub_head->id}}" {{isset($expense)?@$expense->sub_head_id == @$sub_head->id? 'Selected':'':'' }}>{{@$sub_head->sub_head}}</option>
                                            @endforeach


                                        </select>
                                        @if ($errors->has('sub_head'))
                                            <span class="invalid-feedback invalid-select" role="alert">
                                                <strong>{{ @$errors->first('sub_head') }}</strong>
                                            </span>
                                        @endif
                                    </div>

                                </div>
                                <div class="row mt-25">
                                    <div class="col-lg-12">

                                        <div class="input-effect">
                                            <input class="primary-input form-control{{ @$errors->has('amount') ? ' is-invalid' : '' }}" type="number" step="any" name="amount" autocomplete="off" value="{{isset($expense)? @$expense->amount:''}}">
                                            <label>@lang('lang.amount') <span>*</span></label>
                                            <span class="focus-border"></span>
                                            @if (@$errors->has('amount'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ @$errors->first('amount') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>


                                <div class="row no-gutters input-right-icon mt-40">
                                    <div class="col">
                                        <div class="input-effect">
                                            <input class="primary-input date form-control{{ @$errors->has('date') ? ' is-invalid' : '' }}" id="startDate" type="text"
                                                placeholder="@lang('lang.date') " name="date" value="{{isset($expense)? date('m/d/Y',strtotime(@$expense->date)) : date('m/d/Y')}}">
                                            <span class="focus-border"></span>
                                             @if ($errors->has('date'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ @$errors->first('date') }}</strong>
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

                                <div class="row  mt-40">
                                    <div class="col-lg-12">

                                        <select class="niceSelect w-100 bb form-control{{ @$errors->has('cost_center') ? ' is-invalid' : '' }}" name="cost_center">
                                            <option data-display="Cost Center" value="">Cost Center</option>
                                            @foreach($cost_centers as $cost_center)
                                                @if(isset($expense))
                                                <option value="{{@$cost_center->id}}"
                                                    {{@$expense->cost_center_id == @$cost_center->id? 'selected': ''}}>{{@$cost_center->name}}</option>
                                                @else
                                                <option value="{{@$cost_center->id}}" {{old('cost_center') == @$cost_center->id? 'selected': ''}}>{{@$cost_center->name}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        @if (@$errors->has('cost_center'))
                                        <span class="invalid-feedback invalid-select" role="alert">
                                            <strong>{{ @$errors->first('cost_center') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>



                                <div class="row mt-25">
                                    <div class="col-lg-12">
                                        <div class="input-effect">
                                            <textarea class="primary-input form-control" cols="0" rows="4" name="description">{{isset($expense)? @$expense->description:''}}</textarea>
                                            <label>@lang('lang.description')  <span></span></label>
                                            <span class="focus-border textarea"></span>
                                        </div>
                                    </div>
                                </div>
                                @if(isset($expense))
                                @if(in_array(232, @$module_links) || Auth::user()->role_id == 1)
                                <div class="row mt-25">
                                    <div class="col-lg-12">

                                         <select class="niceSelect w-100 bb form-control{{ @$errors->has('is_approved') ? ' is-invalid' : '' }}" name="is_approved" id="is_approved">
                                            <option data-display="Select Status" value="">Select Status *</option>
                                                <option value="1" {{@$expense->is_approved == 1? 'selected':''}}> Approved</option>
                                                <option value="0" {{@$expense->is_approved == 0 ? 'selected':''}}>Pending</option>

                                        </select>
                                        @if ($errors->has('sub_head'))
                                            <span class="invalid-feedback invalid-select" role="alert">
                                                <strong>{{ @$errors->first('sub_head') }}</strong>
                                            </span>
                                        @endif


                                    </div>
                                </div>
                                @endif
                                @endif


                                @php
                                  $tooltip = "";
                                   if(in_array(229, @$module_links) ||  Auth::user()->role_id == 1){
                                        $tooltip = "";
                                    }else{
                                        $tooltip = "You have no permission to add";
                                    }
                                @endphp


                                <div class="row mt-40">
                                    <div class="col-lg-12 text-center">
                                        <button class="primary-btn fix-gr-bg" data-toggle="tooltip" title="{{@$tooltip}}">
                                            <span class="ti-check"></span>
                                            @if(isset($expense))
                                                @lang('lang.update')
                                            @else
                                                @lang('lang.save')
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
                        <div class="main-title tab_mt_30">
                            <h3 class="mb-0">Expense @lang('lang.list')</h3>
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
                                    <td colspan="9">
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
                                    <th>@lang('lang.sl')</th>
                                    <th>@lang('lang.head')</th>
                                    <th>@lang('lang.accounts') @lang('lang.name')</th>
                                    <th>@lang('lang.date')</th>
                                    <th>@lang('lang.cost_center')</th>
                                    <th>@lang('lang.amount') ({{$currency_symbol}})</th>

                                    <th>@lang('lang.description')</th>
                                    <th>@lang('lang.status')</th>
                                    <th>@lang('lang.action')</th>
                                    <th>@lang('lang.author_details')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $count =1 ; @endphp
                                @foreach($expenses as $value)
                                <tr>
                                    <td>{{@$count++}}</td>
                                    <td>{{@$value->expenseHead->head}}</td>
                                    <td>{{@$value->SubHead != ""? @$value->SubHead->sub_head:''}}</td>

                                    <td>{{date('jS M, Y', strtotime(@$value->date))}}</td>
                                    <td>{{@$value->costCenter != ""? @$value->costCenter->name: ''}}</td>
                                    <td>{{@App\SysHelper::com_curr_format((float)@$value->amount, 2, '.', '')}}</td>


                                    <td>{{@$value->description}}</td>
                                    <td>
                                        @if(@$value->is_approved==0)
                                        <button  class="btn primary-btn small tr-bg">Pending</button>

                                        @else
                                        <button  class="btn primary-btn small fix-gr-bg">Approved</button>
                                        @endif

                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">
                                                @lang('lang.select')
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                @if(in_array(230, @$module_links) || Auth::user()->role_id == 1)
                                                <a class="dropdown-item" href="{{url('daily-expense-edit', [@$value->id])}}">@lang('lang.edit')</a>
                                                @endif
                                                @if(in_array(231, @$module_links) || Auth::user()->role_id == 1)
                                                <a class="dropdown-item" data-toggle="modal" data-target="#deleteClassModal{{@$value->id}}"  href="#">@lang('lang.delete')</a>
                                                @endif
                                            </div>
                                        </div>
                                            <div class="modal fade admin-query" id="deleteClassModal{{@$value->id}}" >
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">@lang('lang.delete') daily @lang('lang.expense')</h4>
                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                        </div>

                                                        <div class="modal-body">
                                                            <div class="text-center">
                                                                <h4>@lang('lang.are_you_sure_to_delete')</h4>
                                                            </div>

                                                            <div class="mt-40 d-flex justify-content-between">
                                                                <button type="button" class="primary-btn tr-bg" data-dismiss="modal">@lang('lang.cancel')</button>
                                                                <a href="{{url('daily-expense-delete', [$value->id])}}" class="text-light">
                                                                <button class="primary-btn fix-gr-bg" type="submit">@lang('lang.delete')</button>
                                                                 </a>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                    </td>
                                    <td>
                                        Created: {{App\User::getUserDetails(@$value->created_by)}} At {{date('d F Y, h:i:s A',strtotime(@$value->created_at))}}
                                    @if(!empty(@$value->updated_by)) <br>
                                    Last Updated: {{App\User::getUserDetails(@$value->updated_by)}} At {{date('d F Y, h:i:s A',strtotime(@$value->updated_at))}} @endif
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

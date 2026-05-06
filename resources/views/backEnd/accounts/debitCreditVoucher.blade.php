@extends('backEnd.master')
@section('mainContent')
@section('mainContent')
    @php
        function showPicName($data){
            $name = explode('/', $data);
            return $name[3];
        }


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
            <h1>@lang('lang.Debit_Credit_Voucher')</h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                <a href="#">@lang('lang.accounts')</a>
                <a href="#">@lang('lang.Debit_Credit_Voucher')</a>
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area">
    <div class="container-fluid p-0">
        @if(in_array(72, @$module_links) || Auth::user()->role_id == 1)
        @if(isset($voucher))
        <div class="row">
            <div class="offset-lg-10 col-lg-2 text-right col-md-12 mb-20">
                <a href="{{url('debit-credit-voucher')}}" class="primary-btn small fix-gr-bg">
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
                                @if(isset($voucher))
                                    @lang('lang.edit')
                                @else
                                    @lang('lang.add')
                                @endif
                               @lang('lang.Debit_Credit_Voucher')
                            </h3>
                        </div>
                        @if(isset($voucher))
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'debit-credit-voucher/update', 'method' => 'POST']) }}
                        @else
                        @if(in_array(72, @$module_links) || Auth::user()->role_id == 1)
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'debit-credit-voucher/store', 'method' => 'POST']) }}
                        @endif
                        @endif
                        <input type="hidden" name="id" value="{{isset($voucher)? @$voucher->id:''}}">
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

                                        <div class="input-effect">
                                            <input class="primary-input form-control{{ @$errors->has('voucher_no') ? ' is-invalid' : '' }}" type="text" name="voucher_no" autocomplete="off" value="{{isset($voucher)? @$voucher->voucher_no: @$max_voucher_no}}" readonly="true">
                                            <label>Voucher No <span>*</span></label>
                                            <span class="focus-border"></span>
                                            @if ($errors->has('voucher_no'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ @$errors->first('voucher_no') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row no-gutters input-right-icon mt-35">
                                    <div class="col">
                                        <div class="input-effect">
                                            <input class="primary-input date" id="startDate" type="text" name="date"
                                                   value="{{isset($voucher)? date('m/d/Y', strtotime(@$voucher->date)): date('m/d/Y')}}">
                                            <label>@lang('lang.date')</label>
                                            <span class="focus-border"></span>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <button class="" type="button">
                                            <i class="ti-calendar" id="start-date-icon"></i>
                                        </button>
                                    </div>
                                </div>
                            <div class="row mt-35">
                                <div class="col-lg-12 d-flex">
                                    <p class="text-uppercase fw-500 mb-10">@lang('lang.select') @lang('lang.type') *</p>
                                </div>
                                
                                <div class="col-lg-12 d-flex">
                                    <div class="d-flex radio-btn-flex ml-20">
                                        <div class="mr-30">
                                            <input type="radio" name="type" id="relationFather" value="D" class="common-radio relationButton" {{isset($voucher)? (@$voucher->type=='D'? 'checked':''):'checked'}}>
                                            <label for="relationFather">@lang('lang.Debit')</label>
                                        </div>
                                        <div class="mr-30">
                                            <input type="radio" name="type" id="relationMother" value="C" class="common-radio relationButton" {{isset($voucher)? (@$voucher->type=='C'? 'checked':''):''}}>
                                            <label for="relationMother">@lang('lang.Credit')</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                                <div class="row mt-35">
                                    <div class="col-lg-12">

                                        <div class="input-effect mt-20">
                                            <textarea class="primary-input form-control{{ @$errors->has('note') ? ' is-invalid' : '' }}" cols="0" rows="3" name="note" id="permanent_address">{{isset($voucher)? @$voucher->note:old('note')}}</textarea>
                                            <label>@lang('lang.note')  <span></span> </label>
                                            <span class="focus-border textarea"></span>
                                           @if (@$errors->has('note'))
                                            <span class="invalid-feedback">
                                                <strong>{{ @$errors->first('note') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>


                                 <div class="row mt-35">
                                    <div class="col-lg-12 {{isset($voucher)? (@$voucher->type == 'D'? 'hide':'show'):'hide'}}" id="customer">
                                        <select class="w-100 bb niceSelect form-control {{ $errors->has('customer') ? ' is-invalid' : '' }}" id="customer" name="customer">
                                            <option data-display="Select Customer *" value="">Select Customer *</option>
                                            @foreach($customers as $customer)
                                            <option value="{{@$customer->id}}" {{isset($voucher)? (@$customer->id == @$voucher->customer? 'selected':''):''}}>{{@$customer->full_name}}</option>
                                            @endforeach
                                        </select>
                                        @if (@$errors->has('customer'))
                                        <span class="invalid-feedback invalid-select" role="alert">
                                            <strong>{{ @$errors->first('customer') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="col-lg-12 {{isset($voucher)? (@$voucher->type == 'C'? 'hide':'show'):''}}" id="received-by">

                                        <div class="input-effect">
                                            <input class="primary-input form-control{{ @$errors->has('received_by') ? ' is-invalid' : '' }}" type="text" name="received_by" autocomplete="off"  value="{{isset($voucher)? @$voucher->receiver: old('received_by')}}">
                                            <label>@lang('lang.received_by') <span>*</span></label>
                                            <span class="focus-border"></span>
                                            @if (@$errors->has('received_by'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ @$errors->first('received_by') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>




                                <div class="row mt-35">
                                    <div class="col-lg-12">

                                        <div class="input-effect mt-20">
                                            <textarea class="primary-input form-control{{ @$errors->has('company_address') ? ' is-invalid' : '' }}" cols="0" rows="3" name="company_address" id="company_address">{{isset($voucher)? @$voucher->company_or_address:old('company_address')}}</textarea>
                                            <label>@lang('lang.company') @lang('lang.address')  <span></span> </label>
                                            <span class="focus-border textarea"></span>
                                           @if (@$errors->has('company_address'))
                                            <span class="invalid-feedback">
                                                <strong>{{ @$errors->first('company_address') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-35">
                                    <div class="col-lg-12">

                                        <div class="input-effect">
                                            <input class="primary-input form-control{{ @$errors->has('amount') ? ' is-invalid' : '' }}" type="text" name="amount" autocomplete="off" value="{{isset($voucher)? @$voucher->amount: old('amount')}}">
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

                                <div class="row no-gutters input-right-icon mt-35">
                                        <div class="col">
                                            <div class="input-effect">
                                                <input class="primary-input" id="placeholderInput" type="text"
                                                       placeholder="{{isset($voucher)? (@$voucher->authorised_signature != ""? showPicName(@$voucher->authorised_signature):'Authorized Signature'):'Authorized Signature'}}"
                                                       readonly>
                                                <span class="focus-border"></span>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <button class="primary-btn-small-input" type="button">
                                                <label class="primary-btn small fix-gr-bg"
                                                       for="browseFile">@lang('lang.browse')</label>
                                                <input type="file" class="d-none" id="browseFile" name="authorized_signature">
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row no-gutters input-right-icon mt-35">
                                        <div class="col">
                                            <div class="input-effect">
                                                <input class="primary-input" id="placeholderFileOneName" type="text"
                                                       placeholder="{{isset($voucher)? (@$voucher->authorised_signature != ""? showPicName(@$voucher->accountant_signature):'Accountant Signature'):'Accountant Signature'}}"
                                                       readonly>
                                                <span class="focus-border"></span>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <button class="primary-btn-small-input" type="button">
                                                <label class="primary-btn small fix-gr-bg"
                                                       for="document_file_1">@lang('lang.browse')</label>
                                                <input type="file" class="d-none" id="document_file_1" name="accountant_signature">
                                            </button>
                                        </div>
                                    </div>


                                @php 
                                $tooltip = "";
                                   if(in_array(72, @$module_links) ||  Auth::user()->role_id == 1){
                                        $tooltip = "";
                                    }else{
                                        $tooltip = "You have no permission to add";
                                    }
                                @endphp

                                <div class="row mt-40">
                                    <div class="col-lg-12 text-center">
                                        <button class="primary-btn fix-gr-bg" data-toggle="tooltip" title="{{@$tooltip}}">
                                            <span class="ti-check"></span>
                                            @if(isset($voucher))
                                                @lang('lang.update')
                                            @else
                                                @lang('lang.save')
                                            @endif
                                           @lang('lang.voucher')

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
                            <h3 class="mb-0"> @lang('lang.voucher') @lang('lang.list')</h3>
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
                                    <td colspan="7">
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
                                    <th> @lang('lang.voucher')  @lang('lang.no_')</th>
                                    <th> @lang('lang.date')</th>
                                    <th> @lang('lang.type')</th>
                                    <th>@lang('lang.Customer_Receiver')</th>
                                    <th> @lang('lang.amount') ({{@$currency_symbol}})</th>
                                    <th> @lang('lang.address')</th>
                                    <th> @lang('lang.action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($vouchers as $value)
                                <tr>
                                    <td valign="top">{{@$value->voucher_no}}</td>
                                    <td>{{date('jS M, Y', strtotime(@$value->date))}}</td>
                                    <td valign="top">{{@$value->type == 'D'? 'Debit':'Credit'}}</td>
                                    <td valign="top">{{@$value->type == 'D'? @$value->receiver:@$value->customer}}</td>
                                    <td valign="top">{{@App\SysHelper::com_curr_format( @$value->amount, 2, '.', '' )}}</td>
                                    <td valign="top">{{@$value->company_or_address}}</td>
                                 
                                    
                                    <td valign="top">
                                        <div class="dropdown">
                                            <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">
                                                @lang('lang.select')
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                @if(in_array(73, @$module_links) || Auth::user()->role_id == 1)
                                                <a class="dropdown-item" href="{{url('debit-credit-voucher/edit', [@$value->id])}}">@lang('lang.edit')</a>
                                                @endif

                                                <a class="dropdown-item" href="{{url('debit-credit-voucher/view', [@$value->id])}}">@lang('lang.view')</a>
                                                @if(in_array(74, @$module_links) || Auth::user()->role_id == 1)
                                                <a class="dropdown-item" data-toggle="modal" data-target="#deleteClassModal{{@$value->id}}"  href="{{route('class_delete', [@$value->id])}}">@lang('lang.delete')</a>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                  <div class="modal fade admin-query" id="deleteClassModal{{@$value->id}}" >
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title">@lang('lang.delete') voucher</h4>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="text-center">
                                                    <h4>@lang('lang.are_you_sure_to_delete')</h4>
                                                </div>

                                                <div class="mt-40 d-flex justify-content-between">
                                                    <button type="button" class="primary-btn tr-bg" data-dismiss="modal">@lang('lang.cancel')</button>
                                                    <a href="{{url('debit-credit-voucher/delete', [@$value->id])}}" class="text-light">
                                                    <button class="primary-btn fix-gr-bg" type="submit">@lang('lang.delete')</button>
                                                     </a>
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

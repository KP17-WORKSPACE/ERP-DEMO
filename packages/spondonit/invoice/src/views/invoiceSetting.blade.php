@extends('backEnd.master')
@section('mainContent')
@php
$modules = [];
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();

 
    foreach($permissions as $permission){ @$module_links[] = @$permission->module_link_id; @$modules[] = @$permission->moduleLink->module_id;}

 
    $modules = array_unique(@$modules);
@endphp
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('lang.invoice') @lang('lang.settting') </h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                <a href="#">@lang('lang.invoice')</a>
                <a href="#">@lang('lang.invoice') @lang('lang.settting')</a>
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-title">
                            <h3 class="mb-30"> @lang('lang.update') @lang('lang.invoice') @lang('lang.settting')
                            </h3>
                        </div>
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'infix/invoice-setting-update', 'method' => 'POST']) }}
                       
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
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6 mt-40">
                                        <div class="input-effect">
                                            <input class="primary-input form-control{{ $errors->has('tax') ? ' is-invalid' : '' }}" type="number" name="tax" autocomplete="off" value="{{@$invoiceSetting->tax}}" step="any">
                                            <label>@lang('lang.TAX_VAT_GST') <span>*</span></label>
                                            <span class="focus-border"></span>
                                            @if ($errors->has('tax'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('tax') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-lg-6 mt-40">
                                        <select class="niceSelect w-100 bb form-control{{ $errors->has('tax_type') ? ' is-invalid' : '' }}" name="tax_type" id="tax_type">
                                            <option data-display="TAX/VAT/GST *" value="">@lang('lang.TAX_VAT_GST') *</option>
                                            <option value="AD" {{@$invoiceSetting->tax_type == "AD"? 'selected':''}}>@lang('lang.After') @lang('lang.discount')</option>
                                            <option value="BD" {{@$invoiceSetting->tax_type == "BD"? 'selected':''}}>@lang('lang.before') @lang('lang.discount')</option>
                                        </select>
                                        @if ($errors->has('tax_type'))
                                        <span class="invalid-feedback invalid-select" role="alert">
                                            <strong>{{ $errors->first('tax_type') }}</strong>
                                        </span>
                                        @endif
                                    </div>

                                    <div class="col-lg-6 mt-40">
                                        <div class="input-effect">
                                            <input class="primary-input form-control{{ $errors->has('prefix') ? ' is-invalid' : '' }}" type="text" name="prefix" autocomplete="off" value="{{@$invoiceSetting->prefix}}">
                                            <label>@lang('lang.invoice') @lang('lang.prefix') <span></span></label>
                                            <span class="focus-border"></span>
                                            @if ($errors->has('prefix'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('prefix') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                     
                                </div>

                                <div class="row mt-40">
                                    <div class="col-lg-12 text-center">
                                        <button class="primary-btn fix-gr-bg">
                                            <span class="ti-check"></span>
                                            @if(in_array(261, @$module_links) || Auth::user()->role_id == 1)
                                                @lang('lang.update')
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
        </div>
    </div>
</section>
@endsection

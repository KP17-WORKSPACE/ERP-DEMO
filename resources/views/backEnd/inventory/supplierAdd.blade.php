@extends('backEnd.master')
@section('mainContent')
    @php
    $modules = [];
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();

    foreach($permissions as $permission){ @$module_links[] = @$permission->module_link_id; @$modules[] =
    @$permission->moduleLink->module_id;}

    $modules = array_unique(@$modules);
    @endphp
    {{-- <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('Add Supplier')</h1>
                <div class="bc-pages">
                    <a href="{{ url('dashboard') }}">@lang('lang.dashboard')</a>
                    <a href="#">@lang('lang.inventory')</a>
                    <a href="#">@lang('Add Supplier')</a>
                </div>
            </div>
        </div>
    </section> --}}
    <section class="admin-visitor-area">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-9 col-md-9 col-sm-6 col-xs-12">
                    <div class="main-title">
                        <h3 class="mb-30">
                            @if (isset($editData))
                                @lang('lang.edit')
                            @else
                                @lang('lang.add')
                            @endif
                            @lang('lang.supplier')
                        </h3>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 text-right">
                    <a href="{{ url('suppliers') }}" class="primary-btn small fix-gr-bg"><span
                            class="ti-plus pr-2"></span>@lang('') @lang('Supplier List')</a>
                </div>

            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-12">
                            @if (isset($editData))
                                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'suppliers/' . @$editData->id, 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}
                            @else
                                @if (in_array(162, $module_links) || Auth::user()->role_id == 1)
                                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'suppliers', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                                @endif
                            @endif
                            <div class="white-box">
                                <div class="add-visitor">
                                    <div class="row">
                                        @if (session()->has('message-success'))
                                            <div class="alert alert-success mb-20">
                                                {{ session()->get('message-success') }}
                                            </div>
                                        @elseif(session()->has('message-danger'))
                                            <div class="alert alert-danger">
                                                {{ session()->get('message-danger') }}
                                            </div>
                                        @endif
                                        <div class="col-lg-4 mb-10">
                                            <div class="input-effect">
                                                <input
                                                    class="primary-input form-control{{ $errors->has('supplier_code') ? ' is-invalid' : '' }}"
                                                    type="text" name="supplier_code" autocomplete="off"
                                                    value="{{ isset($editData) ? @$editData->supplier_code : old('supplier_code') }}">
                                                <label> @lang('Supplier Code') <span>*</span> </label>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('supplier_code'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('supplier_code') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-lg-4 mb-10">
                                            <div class="input-effect">
                                                <input
                                                    class="primary-input form-control{{ $errors->has('supplier_name') ? ' is-invalid' : '' }}"
                                                    type="text" name="supplier_name" autocomplete="off"
                                                    value="{{ isset($editData) ? @$editData->supplier_name : old('supplier_name') }}">
                                                <label> @lang('Supplier Name') <span>*</span> </label>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('supplier_name'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('supplier_name') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-lg-4 mb-10">
                                            <div class="input-effect">
                                                <textarea
                                                    class="primary-input form-control{{ $errors->has('supplier_address') ? 'is-invalid' : '' }}"
                                                    cols="0" rows="2" name="supplier_address"
                                                    id="supplier_address">{{ isset($editData) ? @$editData->supplier_address : old('supplier_address') }}</textarea>
                                                <label> @lang('Supplier Address') * <span></span> </label>
                                                <span class="focus-border textarea"></span>
                                                @if ($errors->has('supplier_address'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('supplier_address') }}</strong>
                                                    </span>
                                                @endif

                                            </div>
                                        </div>

                                        <div class="col-lg-4 mb-10">
                                            <div class="input-effect">
                                                <input
                                                    class="primary-input form-control{{ $errors->has('contact_person_name') ? ' is-invalid' : '' }}"
                                                    type="text" name="contact_person_name" autocomplete="off"
                                                    value="{{ isset($editData) ? @$editData->contact_person_name : old('contact_person_name') }}">
                                                <label>@lang('lang.contact_person_name') <span>*</span></label>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('contact_person_name'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('contact_person_name') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-lg-4 mb-10">
                                            <div class="input-effect">
                                                <input
                                                    class="primary-input form-control{{ $errors->has('contact_person_mobile') ? ' is-invalid' : '' }}"
                                                    type="number" name="contact_person_mobile" autocomplete="off"
                                                    value="{{ isset($editData) ? @$editData->contact_person_mobile : old('contact_person_mobile') }}">
                                                <label>@lang('lang.contact_person') @lang('lang.mobile')
                                                    <span>*</span></label>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('contact_person_mobile'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('contact_person_mobile') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-lg-4 mb-10">
                                            <div class="input-effect">
                                                <input
                                                    class="primary-input form-control{{ $errors->has('contact_person_email') ? ' is-invalid' : '' }}"
                                                    type="text" name="contact_person_email" autocomplete="off"
                                                    value="{{ isset($editData) ? @$editData->contact_person_email : old('contact_person_email') }}">
                                                <label>@lang('lang.contact_person') @lang('lang.email') </label>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('contact_person_email'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('contact_person_email') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-lg-4 mb-10">
                                            <div class="input-effect">
                                                <input
                                                    class="primary-input form-control{{ $errors->has('vat_number') ? ' is-invalid' : '' }}"
                                                    type="text" name="vat_number" autocomplete="off"
                                                    value="{{ isset($editData) ? @$editData->vat_number : old('vat_number') }}">
                                                <label>@lang('VAT Number') </label>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('vat_number'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('vat_number') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-4 mb-10">
                                            <div class="input-effect">
                                                <select
                                                    class="niceSelect w-100 bb form-control{{ $errors->has('payment_terms') ? ' is-invalid' : '' }}"
                                                    name="payment_terms" id="payment_terms">
                                                    <option data-display="Payment Terms *" value="">@lang('lang.select')
                                                    </option>
                                                    @foreach ($paymentterms as $key => $value)
                                                        <option value="{{ @$value->id }}" @if (isset($editData))
                                                            @if (@$editData->payment_terms == @$value->id)
                                                                selected @endif
                                                        @else
                                                            {{ old('payment_terms') == @$value->id ? 'selected' : '' }}
                                                    @endif
                                                    >{{ @$value->title }}</option>
                                                    @endforeach
                                                </select>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('payment_terms'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('payment_terms') }}</strong>
                                                    </span>
                                                @endif

                                                {{-- <input
                                                    class="primary-input form-control{{ $errors->has('payment_terms') ? ' is-invalid' : '' }}"
                                                    type="text" name="payment_terms" autocomplete="off"
                                                    value="{{ isset($editData) ? @$editData->payment_terms : old('payment_terms') }}">
                                                <label>@lang('Payment Terms') </label>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('payment_terms'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('payment_terms') }}</strong>
                                                    </span>
                                                @endif --}}
                                            </div>
                                        </div>
                                        <div class="col-lg-4 mb-10">
                                            <div class="input-effect">
                                                <input
                                                    class="primary-input form-control{{ $errors->has('credit_days') ? ' is-invalid' : '' }}"
                                                    type="number" name="credit_days" autocomplete="off"
                                                    value="{{ isset($editData) ? @$editData->credit_days : old('credit_days') }}">
                                                <label>@lang('Credit Days') </label>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('credit_days'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('credit_days') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Bank Info Details -->
                                    <div class="row mt-20">
                                        <div class="col-lg-12">
                                            <div class="main-title">
                                                <h4>@lang('Bank Details')</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-0">
                                        <div class="col-lg-12">
                                            <hr>
                                        </div>
                                    </div>
                                    <div class="row mb-10">
                                        <div class="col-lg-4">
                                            <div class="input-effect">
                                                <input
                                                    class="primary-input form-control{{ $errors->has('accountant_name') ? ' is-invalid' : '' }}"
                                                    type="text" name="accountant_name"
                                                    value="{{ isset($editData) ? @$editData->accountant_name : old('accountant_name') }}">
                                                <label>@lang('Bank Name')</label>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('payment_terms'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('payment_terms') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-lg-4">
                                            <div class="input-effect">
                                                <input
                                                    class="primary-input form-control{{ $errors->has('accountant_email') ? ' is-invalid' : '' }}"
                                                    type="text" name="accountant_email"
                                                    value="{{ isset($editData) ? @$editData->accountant_email : old('accountant_email') }}">
                                                <label>@lang('Branch Name') </label>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('payment_terms'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('payment_terms') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-lg-4">
                                            <div class="input-effect">
                                                <input
                                                    class="primary-input form-control{{ $errors->has('accountant_number') ? ' is-invalid' : '' }}"
                                                    type="text" name="accountant_number"
                                                    value="{{ isset($editData) ? @$editData->accountant_number : old('accountant_number') }}">
                                                <label>@lang('Account Number') </label>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('payment_terms'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('payment_terms') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-10">
                                        <div class="col-lg-12 text-right">
                                            <button type="button" class="primary-btn small fix-gr-bg"
                                                id="{{ @$edit->quotation_type == 'equipment' ? 'addRowEquipment' : 'addRowProduct' }}">
                                                <span class="ti-plus pr-2"></span>
                                                @lang('Add') @lang('Bank')
                                            </button>
                                        </div>
                                    </div>
                                    <!-- end row -->
                                    <!-- Accountant Info Details -->
                                    <div class="row mt-20">
                                        <div class="col-lg-12">
                                            <div class="main-title">
                                                <h4>@lang('Accountant Details')</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-0">
                                        <div class="col-lg-12">
                                            <hr>
                                        </div>
                                    </div>
                                    <div class="row mb-10">
                                        <div class="col-lg-4">
                                            <div class="input-effect">
                                                <input
                                                    class="primary-input form-control{{ $errors->has('accountant_name') ? ' is-invalid' : '' }}"
                                                    type="text" name="accountant_name"
                                                    value="{{ isset($editData) ? @$editData->accountant_name : old('accountant_name') }}">
                                                <label>@lang('Accountant Name')</label>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('payment_terms'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('payment_terms') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-lg-4">
                                            <div class="input-effect">
                                                <input
                                                    class="primary-input form-control{{ $errors->has('accountant_email') ? ' is-invalid' : '' }}"
                                                    type="text" name="accountant_email"
                                                    value="{{ isset($editData) ? @$editData->accountant_email : old('accountant_email') }}">
                                                <label>@lang('Accountant Email') </label>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('payment_terms'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('payment_terms') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-lg-4">
                                            <div class="input-effect">
                                                <input
                                                    class="primary-input form-control{{ $errors->has('accountant_number') ? ' is-invalid' : '' }}"
                                                    type="text" name="accountant_number"
                                                    value="{{ isset($editData) ? @$editData->accountant_number : old('accountant_number') }}">
                                                <label>@lang('Accountant Contact Number') </label>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('payment_terms'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('payment_terms') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-10">
                                        <div class="col-lg-12 text-right">
                                            <button type="button" class="primary-btn small fix-gr-bg"
                                                id="{{ @$edit->quotation_type == 'equipment' ? 'addRowEquipment' : 'addRowProduct' }}">
                                                <span class="ti-plus pr-2"></span>
                                                @lang('Add') @lang('Accountant')
                                            </button>
                                        </div>
                                    </div>
                                    <!-- end row -->


                                </div>
                                @php
                                $tooltip = "";
                                if(in_array(162, @$module_links) || Auth::user()->role_id == 1){
                                $tooltip = "";
                                }else{
                                $tooltip = "You have no permission to add";
                                }
                                @endphp
                                <div class="row mt-10">
                                    <div class="col-lg-12 text-center">
                                        <button class="primary-btn fix-gr-bg" data-toggle="tooltip" title="{{ @$tooltip }}">
                                            <span class="ti-check"></span>
                                            @if (isset($editData))
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
        </div>
        </div>
    </section>
@endsection

@extends('backEnd.master')
@section('mainContent')
    @php
    $modules = [];
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    foreach($permissions as $permission){ @$module_links[] = @$permission->module_link_id; @$modules[] =
    @$permission->moduleLink->module_id;}
    $modules = array_unique(@$modules);
    $generalSetting=App\SmGeneralSettings::where('id',1)->first();
    $currency_symbol = @$generalSetting->currency_symbol;
    
    if(isset($generalSetting->logo)){ @$logo = @$generalSetting->logo; }
    else{ $logo = 'public/uploads/settings/logo.png'; }

    $sm_staff= App\SmStaff::where('user_id',Auth::user()->id)->first();
    if(!empty(@$sm_staff)){
        @$profile_image = @$sm_staff->staff_photo;
        if(empty(@$profile_image)){
            @$profile_image ='public/uploads/staff/staff1.jpg';
        }
    }
    @endphp
    <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1> @lang('Purchase Invoice')</h1>
                <div class="bc-pages">
                    <a href="{{ url('dashboard') }}">@lang('lang.dashboard')</a>
                    <a href="{{ url('purchase-invoice') }}">@lang('Purchase Invoice')</a>
                    <a href="{{ url('purchase-invoice/create') }}" class="active">@lang('lang.create')</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-9 col-md-9 col-sm-6 col-xs-12">
                    <div class="main-title">
                        <h3 class="mb-30">Create Purchase Invoice</h3>
                    </div>
                </div>
                {{-- <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 text-right">
                    @if (in_array(355, @$module_links) || Auth::user()->role_id == 1)
                        <a href="{{ url('purchase-order') }}" class="primary-btn small fix-gr-bg">
                            @lang('View List')</a>
                    @endif
                </div> --}}
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 text-right">
                    <div>
                        <div class="add-visitor">
                            <div class="row mb-0">
                                <div class="col-lg-12 text-right">
                                    <button class="primary-btn fix-gr-bg" data-toggle="tooltip" title="General">
                                        <span class="ti-files"></span>
                                    </button>                            
                                    <button class="primary-btn fix-gr-bg" data-toggle="tooltip" title="PI List" onclick="location.href='.';">
                                        <span class="ti-list"></span>
                                    </button>
                                    <button class="primary-btn fix-gr-bg" data-toggle="tooltip" title="Post & Print" onclick="location.href = '#';">
                                        <span class="ti-save"></span>{{-- <span class="ti-printer"></span> --}}
                                    </button>
                                    <button class="primary-btn fix-gr-bg" data-toggle="tooltip" title="Print" onclick="location.href = '#';">
                                        <span class="ti-printer"></span>
                                    </button>
                                    <button class="primary-btn fix-gr-bg" data-toggle="tooltip" title="Print Preview">
                                        <span class="ti-clipboard"></span>
                                    </button>
                                    <button class="primary-btn fix-gr-bg" data-modal-size="modal-md" data-target="#add_to_do" data-toggle="modal">
                                        <span data-toggle="tooltip" title="Attach" class="ti-pin-alt"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-12">
                            {{-- @if (isset($edit))
                                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => '/quotations/' . $edit->id, 'method' => 'PUT', 'id' => 'tender-create-form']) }}
                            @else --}}
                                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'purchase-invoice-update', 'method' => 'POST', 'id' => 'tender-create-form']) }}
                            {{-- @endif --}}
                            <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                            <input type="hidden" name="id" value="{{ isset($edit_pi) ? $edit_pi->id : '' }}">
                            <div class="white-box">

                                <div class="col-lg-12 text-right">
                                    @if (session()->has('message-success') != '' || session()->get('message-danger') != '')
                                        @if (session()->has('message-success'))
                                            <p class="text-success">
                                                {{ session()->get('message-success') }}
                                            </p>
                                        @elseif(session()->has('message-danger'))
                                            <p class="text-danger">
                                                {{ session()->get('message-danger') }}
                                            </p>
                                        @endif
                                    @endif
                                </div>

                                <div class="add-visitor">
                                    <div class="row ">
                                        <div class="col-lg-4">
                                            <div class="invoice-details-left">
                                                <div class="mb-20">
                                                    <img src="{{ asset($company->company_logo) }}" class="tender-create-logo">
                                                </div>
                                                <div class="business-info">
                                                    <h3>{{ @$company->company_name }}</h3>
                                                    <p class="textWrap">{{ @$company->company_address }}</p>
                                                    <input type="hidden" id="net_vat" name="net_vat" value="{{round($company->net_vat)}}">
                                                </div>
                                                <hr>
                                                <br />
                                            </div>
                                        </div>
                                        <div class="col-lg-8">
                                            <div class="row">
                                                <div class="col-lg-6 mb-10">
                                                    <div class="input-effect">
                                                        <input
                                                            class="primary-input form-control {{ $errors->has('doc_number') ? ' is-invalid' : '' }}"
                                                            type="text" name="doc_number" autocomplete="off" id="doc_number"
                                                            value="{{ isset($edit_pi) ? (!empty(@$edit_pi->doc_number) ? @$edit_pi->doc_number : old('doc_number')) : sprintf('%03d', @App\SysPurchaseInvoice::max('id') + 1) }}"
                                                            readonly>
                                                        <label>@lang('Doc') @lang('Number')<span>*</span></label>
                                                        
                                                        @if ($errors->has('doc_number'))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('doc_number') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 mb-10">
                                                    <div class="no-gutters input-right-icon">
                                                        <div class="col">
                                                            <div class="input-effect">
                                                                @php
                                                                $value = date('m/d/Y');
                                                                if(isset($edit_pi) && !empty($edit_pi->pi_date) ){ @$value =
                                                                date('m/d/Y', strtotime(@$edit_pi->pi_date)); }
                                                                else{ if(!empty(old('pi_date'))){ @$value = old('pi_date');
                                                                }else{
                                                                @$value = date('m/d/Y'); } }
                                                                @endphp
                                                                <input class="primary-input date" id="pi_date" type="text"
                                                                    name="pi_date" value="{{ @$value }}">
                                                                <label>@lang('PI') @lang('lang.date')</label>
                                                                
                                                                @if ($errors->has('pi_date'))
                                                                    <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $errors->first('pi_date') }}</strong>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-auto">
                                                            <button class="" type="button">
                                                                <i class="ti-calendar" id="end-date-icon"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- vendors --}}
                                                <div class="col-lg-6 mb-10">
                                                    <select
                                                        class="niceSelect w-100 bb form-control {{ $errors->has('vendors') ? ' is-invalid' : '' }}"
                                                        name="vendors" id="vendors">
                                                        <option data-display="@lang('Supplier')" value="">@lang('Supplier')</option>
                                                        @foreach ($supplier as $value)
                                                            <option value="{{ @$value->id }}"
                                                                {{ isset($edit_pi) ? (!empty($edit_pi->vendors) ? (@$edit_pi->vendors == @$value->id ? 'selected' : '') : '') : '' }}>{{ @$value->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @if ($errors->has('vendors'))
                                                        <span class="invalid-feedback invalid-select" role="alert">
                                                            <strong>{{ $errors->first('vendors') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="col-lg-6 mb-10">
                                                    <select
                                                        class="niceSelect w-100 bb form-control {{ $errors->has('currency') ? ' is-invalid' : '' }}"
                                                        name="currency" id="currency">
                                                        {{-- <option data-display="@lang('Currency') *" value="">@lang('Currency') *</option> --}}
                                                        @foreach ($currency as $value)
                                                            <option value="{{ @$value->id }}"
                                                                {{ isset($edit_pi) ? (!empty(@$edit_pi->currency) ? (@$edit_pi->currency == @$value->id ? 'selected' : '') : '') : '' }}>
                                                                {{ @$value->code }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @if ($errors->has('currency'))
                                                        <span class="invalid-feedback invalid-select" role="alert">
                                                            <strong>{{ $errors->first('currency') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="col-lg-6 mb-10">
                                                    <div class="input-effect">
                                                        <input
                                                            class="primary-input form-control {{ $errors->has('narration') ? ' is-invalid' : '' }}"
                                                            type="text" name="narration" autocomplete="off"
                                                            value="{{ isset($edit_pi) ? (!empty(@$edit_pi->narration) ? @$edit_pi->narration : old('narration')) : old('narration') }}"
                                                            id="narration">
                                                        <label>@lang('Narration') <span>*</span></label>
                                                        
                                                        @if ($errors->has('narration'))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('narration') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 mb-10">
                                                    <div class="input-effect">
                                                        <input
                                                            class="primary-input form-control {{ $errors->has('createdby') ? ' is-invalid' : '' }}"
                                                            type="text" name="createdby" autocomplete="off" id="createdby"
                                                            value="{{ isset($edit_pi) ? (!empty(@$edit_pi->created_by) ? @$edit_pi->createdby->full_name : old('createdby')) : Auth::user()->full_name }}"
                                                            readonly>
                                                        <label>@lang('Created') @lang('By')<span>*</span></label>
                                                        
                                                        @if ($errors->has('createdby'))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('createdby') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                    </div>



                                    <div class="row mb-10">
                                        <div class="col-lg-3">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="input-effect">
                                                        @php
                                                        $value = date('m/d/Y');
                                                        if(isset($edit_pi) && !empty($edit_pi->lpo_date) ){ @$value =
                                                        date('m/d/Y', strtotime(@$edit_pi->lpo_date)); }
                                                        else{ if(!empty(old('lpo_date'))){ @$value = old('lpo_date'); }else{
                                                        @$value = date('m/d/Y'); } }
                                                        @endphp
                                                        <input class="primary-input date" id="lpo_date" type="text"
                                                            name="lpo_date" value="{{ @$value }}">
                                                        <label>@lang('LPO Date')</label>
                                                        
                                                        @if ($errors->has('lpo_date'))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('lpo_date') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <button class="" type="button">
                                                        <i class="ti-calendar" id="end-date-icon"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 mb-10">
                                            <div class="input-effect">
                                                <input class="primary-input form-control {{ $errors->has('lpo_number') ? ' is-invalid' : '' }}"
                                                    type="text" name="lpo_number" autocomplete="off"
                                                    id="lpo_number"
                                                    value="{{ isset($edit_pi) ? (!empty(@$edit_pi->lpo_number) ? @$edit_pi->lpo_number : old('lpo_number')) : '' }}">
                                                <label>@lang('LPO Number')<span>*</span></label>
                                                
                                                @if ($errors->has('lpo_number'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('lpo_number') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-3 mb-10">
                                            <div class="input-effect">
                                                <select class="niceSelect w-100 bb form-control {{ $errors->has('payment_terms') ? ' is-invalid' : '' }}" name="payment_terms" id="payment_terms" onchange="fn_payment_terms()">
                                                    <option data-display="@lang('Payment Terms') *" value="" >@lang('Payment Terms') *</option>
                                                    @foreach($paymentterms as $value)
                                                         <option value="{{@$value->id}}" {{isset($edit_pi)? !empty(@$edit_pi->payment_terms)? @$edit_pi->payment_terms==@$value->id ? 'selected':'':'':''}} >{{@$value->title}}</option>
                                                    @endforeach
                                                    <option value="22" {{isset($edit_pi)? !empty(@$edit_pi->payment_terms)? @$edit_pi->payment_terms==22 ? 'selected':'':'':''}}>Other</option>
                                                </select>
                                                @if ($errors->has('payment_terms'))
                                                <span class="invalid-feedback invalid-select" role="alert">
                                                    <strong>{{ $errors->first('payment_terms') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-3 mb-10" id="div_payment_terms" @if(isset($edit_pi)) @if(@$edit_pi->payment_terms != 150) style="display: none;" @endif @endif >
                                            <div class="input-effect">
                                                <input class="primary-input form-control {{ $errors->has('payment_terms2') ? ' is-invalid' : '' }}"
                                                    type="text" name="payment_terms2" autocomplete="off"
                                                    id="payment_terms2"
                                                    value="{{ isset($edit_pi) ? (!empty(@$edit_pi->payment_terms2) ? @$edit_pi->payment_terms2 : old('payment_terms2')) : '' }}">
                                                <label>@lang('Other Payment Terms')<span>*</span></label>
                                                
                                                @if ($errors->has('payment_terms2'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('payment_terms2') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        {{-- <div class="col-lg-3 mb-10">
                                            <div class="input-effect">
                                                <input
                                                    class="primary-input form-control{{ $errors->has('supplier_remarks') ? ' is-invalid' : '' }}"
                                                    type="text" name="supplier_remarks" autocomplete="off"
                                                    id="supplier_remarks"
                                                    value="{{ isset($edit) ? (!empty(@$edit->supplier_remarks) ? @$edit->supplier_remarks : old('supplier_remarks')) : '' }}">
                                                <label>@lang('Supplier Remarks')<span>*</span></label>
                                                
                                                @if ($errors->has('supplier_remarks'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('supplier_remarks') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div> --}}
                                    </div>
                                    
                                    <div class="row mt-20 mb-0">
                                        <div class="col-lg-12">
                                            <h4>@lang('Bill To Address')</h4>
                                        </div>
                                    </div>
                                    <div class="row mb-10">
                                        <div class="col-lg-3">
                                            <div class="input-effect">
                                            <input type="text" class="primary-input form-control" value="{{@$company->company_name}}">                                                    
                                                <label>@lang('Name') <span></span></label>
                                                <span class="focus-border textarea"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="input-effect">
                                            <input type="text" class="primary-input form-control" value="{{@$company->company_address}}">                                                    
                                                <label>@lang('Address') <span></span></label>
                                                <span class="focus-border textarea"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 mb-10">
                                            <div class="input-effect">
                                                <input class="primary-input form-control {{ $errors->has('bill_number') ? ' is-invalid' : '' }}"
                                                    type="text" name="bill_number" autocomplete="off"
                                                    id="bill_number"
                                                    value="{{ isset($edit_pi) ? (!empty(@$edit_pi->bill_number) ? @$edit_pi->bill_number : old('bill_number')) : '' }}">
                                                <label>@lang('Bill Number')<span>*</span></label>
                                                
                                                @if ($errors->has('bill_number'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('bill_number') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="input-effect">
                                                        @php
                                                        $value = date('m/d/Y');
                                                        if(isset($edit_pi) && !empty($edit_pi->bill_date) ){ @$value =
                                                        date('m/d/Y', strtotime(@$edit_pi->bill_date)); }
                                                        else{ if(!empty(old('bill_date'))){ @$value = old('bill_date'); }else{
                                                        @$value = date('m/d/Y'); } }
                                                        @endphp
                                                        <input class="primary-input date" id="bill_date" type="text"
                                                            name="bill_date" value="{{ @$value }}">
                                                        <label>@lang('Bill Date')</label>
                                                        
                                                        @if ($errors->has('bill_date'))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('bill_date') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <button class="" type="button">
                                                        <i class="ti-calendar" id="end-date-icon"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-20 mb-0">
                                        <div class="col-lg-12">
                                            <h4>@lang('VAT Details')</h4>
                                        </div>
                                    </div>


                                    <div class="row mb-10">
                                        <div class="col-lg-3">
                                            <div class="input-effect">
                                                {{-- <input type="text" class="primary-input form-control" cols="0" rows="4"
                                                    name="supplier_type">{{ isset($edit) ? (!empty(@$edit->supplier_type) ? @$edit->supplier_type : '') : old('supplier_type') }}</textarea>
                                                <label>@lang('Supplier Type') <span></span></label>
                                                <span class="focus-border textarea"></span> --}}
                                                <select class="niceSelect w-100 bb form-control {{ $errors->has('supplier_type') ? ' is-invalid' : '' }}" name="supplier_type" id="supplier_type">
                                                    <option data-display="@lang('Supplier Type') *" value="" >@lang('Supplier Type') *</option>
                                                    @foreach($suppliertype as $value)
                                                            <option value="{{@$value->id}}" {{isset($edit_pi)? !empty(@$edit_pi->supplier_type)? @$edit_pi->supplier_type==@$value->id ? 'selected':'':'':''}} >{{@$value->title}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="input-effect">
                                                <select class="niceSelect w-100 bb form-control {{ $errors->has('purchase_type') ? ' is-invalid' : '' }}" name="purchase_type" id="purchase_type">
                                                    <option data-display="@lang('Purchase Type') *" value="" >@lang('Purchase Type') *</option>
                                                    <option value="Standard-Rated Purchase" {{isset($edit_pi)? !empty(@$edit_pi->purchase_type)? @$edit_pi->purchase_type=='Standard-Rated Purchase' ? 'selected':'':'':''}} >Standard-Rated Purchase</option>
                                                    <option value="Zero-Rated Purchase" {{isset($edit_pi)? !empty(@$edit_pi->purchase_type)? @$edit_pi->purchase_type=='Zero-Rated Purchase' ? 'selected':'':'':''}} >Zero-Rated Purchase</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="input-effect">
                                                <input type="text" class="primary-input form-control" 
                                                    name="supplier_country" value="{{ isset($edit_pi) ? (!empty(@$edit_pi->supplier_country) ? @$edit_pi->supplier_country : '') : old('supplier_country') }}"">
                                                <label>@lang('Supplier Country') <span></span></label>
                                                <span class="focus-border textarea"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="input-effect">
                                                <input type="text" class="primary-input form-control" cols="0" rows="4"
                                                    name="supplier_state" value="{{ isset($edit_pi) ? (!empty(@$edit_pi->supplier_state) ? @$edit_pi->supplier_state : '') : old('supplier_state') }}">
                                                <label>@lang('Supplier State') <span></span></label>
                                                <span class="focus-border textarea"></span>
                                            </div>
                                        </div>
                                    </div>

              


                                        <div class="equipment comon-status row mt-40 d-block">
                                            <table class="sstable" cellspacing="0" width="100%" id="po-table">
                                                <thead>
                                                    <tr>
                                                        <th style="width:100px;">@lang('Part No')</th>
                                                        <th style="width:350px;">@lang('Description')</th>
                                                        <th style="width:70px;">@lang('Tax')</th>
                                                        <th style="width:70px;">@lang('Qty')</th>
                                                        <th style="width:80px;">@lang('Unit Price')</th>
                                                        <th style="width:70px;">@lang('Value')</th>
                                                        <th style="width:70px;">@lang('Discount')</th>
                                                        <th style="width:130px;">@lang('Custom Charges')</th>
                                                        <th style="width:120px;">@lang('Taxable Amount')</th>
                                                        <th style="width:100px;">@lang('VAT Amount')</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @for ($roid= 1;  $roid <= count($edit_pi_items) ; $roid++)
                                                    <tr id="rowone{{$roid}}" onclick="fn_addRow({{$roid}})">
                                                        <td><select class="w-100 sstxtbx" name="part_number[]" id="part_number_{{$roid}}" onchange="ddl_part_change({{$roid}})">
                                                                <option value="none"></option>
                                                                @foreach ($items as $key => $value)
                                                                    <option value="{{ @$value->id }}" {{isset($edit_pi_items[$roid-1])? !empty(@$edit_pi_items[$roid-1]->part_number)? @$edit_pi_items[$roid-1]->part_number==@$value->id ? 'selected':'':'':''}}>{{ @$value->part_number }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select class="w-100 sstxtbx" name="part_number_txt[]" id="part_number_txt_{{$roid}}" readonly="true" hidden>
                                                                <option value="none"></option>
                                                                @foreach ($items as $key => $value)
                                                                    <option value="{{ @$value->id }}" {{isset($edit_pi_items[$roid-1])? !empty(@$edit_pi_items[$roid-1]->part_number_txt)? @$edit_pi_items[$roid-1]->part_number_txt==@$value->id ? 'selected':'':'':''}}>{{ @$value->description }}</option>
                                                                @endforeach
                                                            </select>
                                                        
                                                            @if (isset($edit_pi_items[$roid-1])) @if(!empty(@$edit_pi_items[$roid-1]->part_number))
                                                            @php $abc =  @App\SmItem::select('description')->where('id',@$edit_pi_items[$roid-1]->part_number)->first(); @endphp
                                                            @endif @endif
                                                            <input class="w-100 sstxtbx" type="text" id="description_{{$roid}}" name="description[]" autocomplete="off" readonly="true"
                                                            value="{{ $abc->description  }}" >
                                                        </td>
                                                        <td>
                                                            <select class="w-100 sstxtbx" name="tax[]" id="tax_{{$roid}}" readonly="true" onchange="calc_change({{$roid}})">
                                                                <option value="{{round($company->net_vat)}}" {{isset($edit_pi_items[$roid-1])? !empty(@$edit_pi_items[$roid-1]->tax)? @$edit_pi_items[$roid-1]->tax==@$value->id ? 'selected':'':'':''}} >VAT {{round($company->net_vat)}}%</option>
                                                                <option value="0" {{isset($edit_pi_items[$roid-1])? !empty(@$edit_pi_items[$roid-1]->tax)? @$edit_pi_items[$roid-1]->tax==0 ? 'selected':'':'':''}} >None</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input class="w-100 sstxtbx" type="number" id="qty_{{$roid}}" name="qty[]" autocomplete="off" min="0" onchange="calc_change({{$roid}})"
                                                            value="{{@$edit_pi_items[$roid-1]->qty}}">
                                                        </td>
                                                        <td>
                                                            <input class="w-100 sstxtbx" type="number" id="unitprice_{{$roid}}" name="unitprice[]" autocomplete="off" min="0" onchange="calc_change({{$roid}})" value="{{@$edit_pi_items[$roid-1]->unitprice}}">
                                                        </td>
                                                        <td>
                                                            <input class="w-100 sstxtbx" type="number" id="value_{{$roid}}" name="value[]" autocomplete="off" min="0" readonly value="{{@$edit_pi_items[$roid-1]->value}}">
                                                        </td>
                                                        <td>
                                                            <input class="w-100 sstxtbx" type="number" id="discount_{{$roid}}" name="discount[]" autocomplete="off" min="0" onchange="calc_change({{$roid}})" value="{{@$edit_pi_items[$roid-1]->discount}}">
                                                        </td>
                                                        <td>
                                                            <input class="w-100 sstxtbx" type="number" id="customcharges_{{$roid}}" name="customcharges[]" autocomplete="off" min="0" onchange="calc_change({{$roid}})" value="{{@$edit_pi_items[$roid-1]->customcharges}}">
                                                        </td>
                                                        <td>
                                                            <input class="w-100 sstxtbx" type="number" id="taxableamount_{{$roid}}" name="taxableamount[]" autocomplete="off" min="0" readonly value="{{@$edit_pi_items[$roid-1]->taxableamount}}">
                                                        </td>
                                                        <td>
                                                            <input class="w-100 sstxtbx" type="number" id="vatamount_{{$roid}}" name="vatamount[]" autocomplete="off" min="0" readonly value="{{@$edit_pi_items[$roid-1]->vatamount}}">
                                                        </td>
                                                    </tr>
                                                    @endfor
                                                    <?php $roid--;?>
                                                    <input type="hidden" id="po-row-count" value="{{$roid}}">
                                                    
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                      <td></td>
                                                      <td></td>
                                                      <td class="sstablefoot">0.00</td>
                                                      <td class="sstablefoot">0</td>
                                                      <td class="sstablefoot">0.00</td>
                                                      <td class="sstablefoot">0.00</td>
                                                      <td class="sstablefoot">0.00</td>
                                                      <td class="sstablefoot">0.00</td>
                                                      <td class="sstablefoot">0.00</td>
                                                      <td class="sstablefoot">0.00</td>
                                                    </tr>
                                                  </tfoot>
                                            </table>

                                            <div style="display: none;">
                                            <button type="button" class="primary-btn small fix-gr-bg" id="addRowPO"><span class="ti-plus pr-2"></span>@lang('lang.item')</button>
                                            </div>
                                            

<script>
    function fn_addRow(id)
    {
        var rownum = document.getElementById('po-row-count').value;        
        if(id==rownum)
        {
            document.getElementById('po-row-count').value = (Number(rownum) + Number(1));
            document.getElementById('addRowPO').click();
        }
    }
    function ddl_part_change(id)
    {
        var selOpt = $('#part_number_'+id+' :selected').val();
        $('#part_number_txt_'+id+' option[value='+selOpt+']').attr('selected','selected');        
        var selOpt2 = $('#part_number_txt_'+id+' :selected').text();
        $('#description_'+id+'').val(selOpt2);
        $('#description_'+id+'').focus();
    }

    function calc_change(id)
    {
        //var net_vat = $('#net_vat').val();
        var net_vat = $('#tax_'+id+'').val();
        
        var qty = $('#qty_'+id+'').val();
        var unitprice = $('#unitprice_'+id+'').val();
        var value = $('#value_'+id+'').val();
        var discount = $('#discount_'+id+'').val();
        var customcharges = $('#customcharges_'+id+'').val();

        qty = (qty === '') ? '0' : qty;
        unitprice = (unitprice === '') ? '0' : unitprice;
        var fin_value = (unitprice * qty);
        $('#value_'+id+'').val(fin_value.toFixed(@json(session('logged_session_data.decimal_point'))));

        
        value = (value === '') ? '0' : value;
        discount = (discount === '') ? '0' : discount;
        customcharges = (customcharges === '') ? '0' : customcharges;
        var fin_taxableamount = ((unitprice * qty) + Number(customcharges) - Number(discount)) * ((Number(net_vat) + 100)/100);
        $('#taxableamount_'+id+'').val(fin_taxableamount.toFixed(@json(session('logged_session_data.decimal_point'))));

        var fin_vatamount = ((unitprice * qty) + Number(customcharges) - Number(discount)) * ((Number(net_vat))/100);
        var vatamount = $('#vatamount_'+id+'').val(fin_vatamount.toFixed(@json(session('logged_session_data.decimal_point'))));
    }


    function fn_payment_terms()
    {
        var val_payment_terms = $('#payment_terms').val();
        if(val_payment_terms==22)
        {
            $('#div_payment_terms').css('display','block');
        }
        else
        {
            $('#div_payment_terms').css('display','none');
        }
    }
    function fn_shipping_name()
    {
        var shipping_id = $('#shipping_name').val();
        var shipping_data = $('#ship_'+shipping_id).val();        
        var ret = shipping_data.split("#");
        $('#shipping_address_1').val(ret[0]);
        $('#shipping_address_1').focus();
        $('#shipping_address_2').val(ret[1]);
        $('#shipping_address_2').focus();
        $('#shipping_contact_no').val(ret[2]);
        $('#shipping_contact_no').focus();
    }

    
</script>



                                        </div>

                                        <div class="equipment comon-status row mt-25 d-block" style="display:none !important;">
                                            <div class="col-lg-12 text-right">
                                                <button type="button" class="primary-btn small fix-gr-bg"
                                                    id="addRowEquipment">
                                                    <span class="ti-plus pr-2"></span>@lang('lang.item')</button>
                                            </div>
                                        </div>
                                        
                                   

                                    <div class="row mt-40">
                                        <div class="col-lg-12">
                                            <div class="input-effect">
                                                <textarea class="primary-input form-control" cols="0" rows="4"
                                                    name="note">{{ isset($edit_pi) ? (!empty(@$edit_pi->note) ? @$edit_pi->note : '') : old('description') }}</textarea>
                                                <label>@lang('lang.note') <span></span></label>
                                                <span class="focus-border textarea"></span>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="equipment comon-status row mt-40 d-block">
                                        <table class="sstable" cellspacing="0" width="100%" id="po-table">
                                            <thead>
                                                <tr>
                                                    <th style="width:100px;">@lang('Name')</th>
                                                    <th style="width:350px;">@lang('Credit Account')</th>
                                                    <th style="width:70px;">@lang('Amount')</th>
                                                    <th style="width:70px;">@lang('Calculated Amount')</th>
                                                    <th style="width:80px;">@lang('Remarks')</th>
                                                    <th style="width:70px;">@lang('Currency')</th>
                                                    <th style="width:70px;">@lang('Exchange Rate')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <select class="w-100 sstxtbx" name="cfc_name[]" id="cfc_name_1" readonly="true">
                                                            <option value=""></option>
                                                            <option value="customs" {{isset($edit_cfc[0])? !empty(@$edit_cfc[0]->cfc_name)? @$edit_cfc[0]->cfc_name=='customs' ? 'selected':'':'':''}}>Customs</option>
                                                            <option value="freight" {{isset($edit_cfc[0])? !empty(@$edit_cfc[0]->cfc_name)? @$edit_cfc[0]->cfc_name=='freight' ? 'selected':'':'':''}}>Freight</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select class="w-100 sstxtbx" name="cfc_credit_account[]" id="cfc_credit_account_1" readonly="true">
                                                            <option value="none"></option>
                                                            @foreach ($chartofaccounts as $key => $value)
                                                                <option value="{{ @$value->id }}" {{isset($edit_cfc[0])? !empty(@$edit_cfc[0]->cfc_credit_account)? @$edit_cfc[0]->cfc_credit_account==@$value->id ? 'selected':'':'':''}}>{{ @$value->account_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input class="w-100 sstxtbx" type="number" id="cfc_amount_1" name="cfc_amount[]" autocomplete="off" min="0"
                                                        value="{{ isset($edit_cfc[0]) ? (!empty(@$edit_cfc[0]->cfc_amount) ? @$edit_cfc[0]->cfc_amount : old('')) : old('') }}" onchange="cfc_amount_change(1)">
                                                        
                                                    </td>
                                                    <td>
                                                        <input class="w-100 sstxtbx" type="number" id="cfc_cal_amount_1" name="cfc_cal_amount[]" autocomplete="off" min="0"
                                                        value="{{ isset($edit_cfc[0]) ? (!empty(@$edit_cfc[0]->cfc_cal_amount) ? @$edit_cfc[0]->cfc_cal_amount : old('')) : old('') }}" >
                                                    </td>
                                                    <td>
                                                        <input class="w-100 sstxtbx" type="text" id="cfc_remarks_1" name="cfc_remarks[]" autocomplete="off"
                                                        value="{{ isset($edit_cfc[0]) ? (!empty(@$edit_cfc[0]->cfc_remarks) ? @$edit_cfc[0]->cfc_remarks : old('')) : old('') }}" >
                                                    </td>
                                                    <td>
                                                        <input class="w-100 sstxtbx" type="text" id="cfc_currency_1" name="cfc_currency[]" autocomplete="off"
                                                        value="{{ isset($edit_cfc[0]) ? (!empty(@$edit_cfc[0]->cfc_currency) ? @$edit_cfc[0]->cfc_currency : old('')) : old('') }}" >
                                                    </td>
                                                    <td>
                                                        <input class="w-100 sstxtbx" type="text" id="cfc_exe_rate_1" name="cfc_exe_rate[]" autocomplete="off"
                                                        value="{{ isset($edit_cfc[0]) ? (!empty(@$edit_cfc[0]->cfc_exe_rate) ? @$edit_cfc[0]->cfc_exe_rate : old('')) : old('') }}" >
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <select class="w-100 sstxtbx" name="cfc_name[]" id="cfc_name_2" readonly="true">
                                                            <option value=""></option>
                                                            <option value="customs" {{isset($edit_cfc[1])? !empty(@$edit_cfc[1]->cfc_name)? @$edit_cfc[1]->cfc_name=='customs' ? 'selected':'':'':''}}>Customs</option>
                                                            <option value="freight" {{isset($edit_cfc[1])? !empty(@$edit_cfc[1]->cfc_name)? @$edit_cfc[1]->cfc_name=='freight' ? 'selected':'':'':''}}>Freight</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select class="w-100 sstxtbx" name="cfc_credit_account[]" id="cfc_credit_account_2" readonly="true">
                                                            <option value="none"></option>
                                                            @foreach ($chartofaccounts as $key => $value)
                                                                <option value="{{ @$value->id }}" {{isset($edit_cfc[1])? !empty(@$edit_cfc[1]->cfc_credit_account)? @$edit_cfc[1]->cfc_credit_account==@$value->id ? 'selected':'':'':''}}>{{ @$value->account_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input class="w-100 sstxtbx" type="number" id="cfc_amount_2" name="cfc_amount[]" autocomplete="off" min="0"
                                                        value="{{ isset($edit_cfc[1]) ? (!empty(@$edit_cfc[1]->cfc_amount) ? @$edit_cfc[1]->cfc_amount : old('')) : old('') }}" onchange="cfc_amount_change(2)">
                                                        
                                                    </td>
                                                    <td>
                                                        <input class="w-100 sstxtbx" type="number" id="cfc_cal_amount_2" name="cfc_cal_amount[]" autocomplete="off" min="0"
                                                        value="{{ isset($edit_cfc[1]) ? (!empty(@$edit_cfc[1]->cfc_cal_amount) ? @$edit_cfc[1]->cfc_cal_amount : old('')) : old('') }}" >
                                                    </td>
                                                    <td>
                                                        <input class="w-100 sstxtbx" type="text" id="cfc_remarks_2" name="cfc_remarks[]" autocomplete="off"
                                                        value="{{ isset($edit_cfc[1]) ? (!empty(@$edit_cfc[1]->cfc_remarks) ? @$edit_cfc[1]->cfc_remarks : old('')) : old('') }}" >
                                                    </td>
                                                    <td>
                                                        <input class="w-100 sstxtbx" type="text" id="cfc_currency_2" name="cfc_currency[]" autocomplete="off"
                                                        value="{{ isset($edit_cfc[1]) ? (!empty(@$edit_cfc[1]->cfc_currency) ? @$edit_cfc[1]->cfc_currency : old('')) : old('') }}" >
                                                    </td>
                                                    <td>
                                                        <input class="w-100 sstxtbx" type="text" id="cfc_exe_rate_2" name="cfc_exe_rate[]" autocomplete="off"
                                                        value="{{ isset($edit_cfc[1]) ? (!empty(@$edit_cfc[1]->cfc_exe_rate) ? @$edit_cfc[1]->cfc_exe_rate : old('')) : old('') }}" >
                                                    </td>
                                                </tr>                                                
                                                <tr>
                                                    <td>
                                                        <select class="w-100 sstxtbx" name="cfc_name[]" id="cfc_name_3" readonly="true">
                                                            <option value=""></option>
                                                            <option value="customs" {{isset($edit_cfc[2])? !empty(@$edit_cfc[2]->cfc_name)? @$edit_cfc[2]->cfc_name=='customs' ? 'selected':'':'':''}}>Customs</option>
                                                            <option value="freight" {{isset($edit_cfc[2])? !empty(@$edit_cfc[2]->cfc_name)? @$edit_cfc[2]->cfc_name=='freight' ? 'selected':'':'':''}}>Freight</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select class="w-100 sstxtbx" name="cfc_credit_account[]" id="cfc_credit_account_3" readonly="true">
                                                            <option value="none"></option>
                                                            @foreach ($chartofaccounts as $key => $value)
                                                                <option value="{{ @$value->id }}" {{isset($edit_cfc[2])? !empty(@$edit_cfc[2]->cfc_credit_account)? @$edit_cfc[2]->cfc_credit_account==@$value->id ? 'selected':'':'':''}}>{{ @$value->account_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input class="w-100 sstxtbx" type="number" id="cfc_amount_3" name="cfc_amount[]" autocomplete="off" min="0"
                                                        value="{{ isset($edit_cfc[2]) ? (!empty(@$edit_cfc[2]->cfc_amount) ? @$edit_cfc[2]->cfc_amount : old('')) : old('') }}" onchange="cfc_amount_change(2)">
                                                        
                                                    </td>
                                                    <td>
                                                        <input class="w-100 sstxtbx" type="number" id="cfc_cal_amount_3" name="cfc_cal_amount[]" autocomplete="off" min="0"
                                                        value="{{ isset($edit_cfc[2]) ? (!empty(@$edit_cfc[2]->cfc_cal_amount) ? @$edit_cfc[2]->cfc_cal_amount : old('')) : old('') }}" >
                                                    </td>
                                                    <td>
                                                        <input class="w-100 sstxtbx" type="text" id="cfc_remarks_3" name="cfc_remarks[]" autocomplete="off"
                                                        value="{{ isset($edit_cfc[2]) ? (!empty(@$edit_cfc[2]->cfc_remarks) ? @$edit_cfc[2]->cfc_remarks : old('')) : old('') }}" >
                                                    </td>
                                                    <td>
                                                        <input class="w-100 sstxtbx" type="text" id="cfc_currency_3" name="cfc_currency[]" autocomplete="off"
                                                        value="{{ isset($edit_cfc[2]) ? (!empty(@$edit_cfc[2]->cfc_currency) ? @$edit_cfc[2]->cfc_currency : old('')) : old('') }}" >
                                                    </td>
                                                    <td>
                                                        <input class="w-100 sstxtbx" type="text" id="cfc_exe_rate_3" name="cfc_exe_rate[]" autocomplete="off"
                                                        value="{{ isset($edit_cfc[2]) ? (!empty(@$edit_cfc[2]->cfc_exe_rate) ? @$edit_cfc[2]->cfc_exe_rate : old('')) : old('') }}" >
                                                    </td>
                                                </tr>
                                                
                                                <tr>
                                                    <td>
                                                        <select class="w-100 sstxtbx" name="cfc_name[]" id="cfc_name_4" readonly="true">
                                                            <option value=""></option>
                                                            <option value="customs" {{isset($edit_cfc[3])? !empty(@$edit_cfc[3]->cfc_name)? @$edit_cfc[3]->cfc_name=='customs' ? 'selected':'':'':''}}>Customs</option>
                                                            <option value="freight" {{isset($edit_cfc[3])? !empty(@$edit_cfc[3]->cfc_name)? @$edit_cfc[3]->cfc_name=='freight' ? 'selected':'':'':''}}>Freight</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select class="w-100 sstxtbx" name="cfc_credit_account[]" id="cfc_credit_account_4" readonly="true">
                                                            <option value="none"></option>
                                                            @foreach ($chartofaccounts as $key => $value)
                                                                <option value="{{ @$value->id }}" {{isset($edit_cfc[3])? !empty(@$edit_cfc[3]->cfc_credit_account)? @$edit_cfc[3]->cfc_credit_account==@$value->id ? 'selected':'':'':''}}>{{ @$value->account_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input class="w-100 sstxtbx" type="number" id="cfc_amount_4" name="cfc_amount[]" autocomplete="off" min="0"
                                                        value="{{ isset($edit_cfc[3]) ? (!empty(@$edit_cfc[3]->cfc_amount) ? @$edit_cfc[3]->cfc_amount : old('')) : old('') }}" onchange="cfc_amount_change(2)">
                                                        
                                                    </td>
                                                    <td>
                                                        <input class="w-100 sstxtbx" type="number" id="cfc_cal_amount_4" name="cfc_cal_amount[]" autocomplete="off" min="0"
                                                        value="{{ isset($edit_cfc[3]) ? (!empty(@$edit_cfc[3]->cfc_cal_amount) ? @$edit_cfc[3]->cfc_cal_amount : old('')) : old('') }}" >
                                                    </td>
                                                    <td>
                                                        <input class="w-100 sstxtbx" type="text" id="cfc_remarks_4" name="cfc_remarks[]" autocomplete="off"
                                                        value="{{ isset($edit_cfc[3]) ? (!empty(@$edit_cfc[3]->cfc_remarks) ? @$edit_cfc[3]->cfc_remarks : old('')) : old('') }}" >
                                                    </td>
                                                    <td>
                                                        <input class="w-100 sstxtbx" type="text" id="cfc_currency_4" name="cfc_currency[]" autocomplete="off"
                                                        value="{{ isset($edit_cfc[3]) ? (!empty(@$edit_cfc[3]->cfc_currency) ? @$edit_cfc[3]->cfc_currency : old('')) : old('') }}" >
                                                    </td>
                                                    <td>
                                                        <input class="w-100 sstxtbx" type="text" id="cfc_exe_rate_4" name="cfc_exe_rate[]" autocomplete="off"
                                                        value="{{ isset($edit_cfc[3]) ? (!empty(@$edit_cfc[3]->cfc_exe_rate) ? @$edit_cfc[3]->cfc_exe_rate : old('')) : old('') }}" >
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="row mt-40">
                                        <div class="col-lg-12 text-right">
                                            <button type="submit" class="primary-btn fix-gr-bg">
                                                <span class="ti-check"></span>
                                                @if (isset($edit))
                                                    @lang('lang.update')
                                                @else
                                                    @lang('lang.save')
                                                @endif
                                                @lang('Purchase Invoice')

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


    <div class="modal fade admin-query" id="add_to_do">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header m-0 p-3">
                    <h4 class="modal-title">Add Shipping</h4>
                    <button class="close" data-dismiss="modal" type="button">
                        ×
                    </button>
                </div>
                <div class="modal-body m-0 p-3">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12 mb-20">
                                <div class="input-effect">
                                    <input class="primary-input form-control {{$errors->has('shipping_name') ? 'is-invalid' : ' '}}" type="text" id="shipping_name_add" name="shipping_name" value="{{isset($editData)?@$editData->shipping_name:old('shipping_name')}}" >
                                    <label>  @lang('Shipping Name') <span>*</span> </label>
                                    
                                    <span class="modal_input_validation_1 red_alert"></span>                                    
                                </div>
                            </div>
                            <div class="col-lg-12 mb-20">
                                <div class="input-effect">
                                    <input class="primary-input form-control {{$errors->has('contact_name') ? 'is-invalid' : ' '}}" type="text" id="contact_name_add" name="contact_name" value="{{isset($editData)?@$editData->contact_name:old('contact_name')}}" >
                                    <label>  @lang('Contact Name') <span>*</span> </label>
                                    
                                    <span class="modal_input_validation_2 red_alert"></span>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-20">
                                <div class="input-effect">
                                    <input class="primary-input form-control{{ $errors->has('contact_no') ? ' is-invalid' : '' }}" type="number" id="contact_no_add" name="contact_no" value="{{isset($editData)?@$editData->contact_no:old('contact_no')}}">
                                    <label>  @lang('Contact No') <span>*</span> </label>
                                    
                                    <span class="modal_input_validation_3 red_alert"></span>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-20">
                                <div class="input-effect">
                                    <input class="primary-input form-control{{ $errors->has('address1') ? ' is-invalid' : '' }}" type="text" id="address1_add" name="address1" value="{{isset($editData)?@$editData->address1:old('address1')}}">
                                    <label>  @lang('Address 1') <span>*</span> </label>  
                                    
                                    <span class="modal_input_validation_4 red_alert"></span>                                  
                                </div>
                            </div>
                            <div class="col-lg-12 mb-20">
                                <div class="input-effect">
                                    <input class="primary-input form-control{{ $errors->has('address2') ? ' is-invalid' : '' }}" type="text" id="address2_add" name="address2" value="{{isset($editData)?@$editData->address2:old('address2')}}">
                                    <label>  @lang('Address 2') <span>*</span> </label>    
                                    
                                    <span class="modal_input_validation_5 red_alert"></span>                              
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">    
                                <div class="col-lg-12 text-center">
                                    <div class="mt-40 d-flex justify-content-between">
                                        <button class="primary-btn tr-bg" data-dismiss="modal" type="button" id="btn_close2">
                                            @lang('lang.cancel')
                                        </button>
                                        <input class="primary-btn fix-gr-bg" type="submit" value="save" onclick="return validateAttachForm()">
    
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script>
    function validateAttachForm() {
    var val1 = $("#shipping_name_add").val();
    var val2 = $("#contact_name_add").val();
    var val3 = $("#contact_no_add").val();
    var val4 = $("#address1_add").val();
    var val5 = $("#address2_add").val();

    if (val1 === "") {
        $('.modal_input_validation_1').show();
        $(".modal_input_validation_1").html("<font style='color:red;'>Must be Fill Up</font>");
        $("span.modal_input_validation_1").addClass("red_alert");
        return false;
    }
    if (val2 === "") {
        $('.modal_input_validation_2').show();
        $(".modal_input_validation_2").html("<font style='color:red;'>Must be Fill Up</font>");
        $("span.modal_input_validation_2").addClass("red_alert");
        return false;
    }
    if (val3 === "") {
        $('.modal_input_validation_3').show();
        $(".modal_input_validation_3").html("<font style='color:red;'>Must be Fill Up</font>");
        $("span.modal_input_validation_3").addClass("red_alert");
        return false;
    }
    if (val4 === "") {
        $('.modal_input_validation_4').show();
        $(".modal_input_validation_4").html("<font style='color:red;'>Must be Fill Up</font>");
        $("span.modal_input_validation_4").addClass("red_alert");
        return false;
    }
    if (val5 === "") {
        $('.modal_input_validation_5').show();
        $(".modal_input_validation_5").html("<font style='color:red;'>Must be Fill Up</font>");
        $("span.modal_input_validation_5").addClass("red_alert");
        return false;
    }
    //return true;

    $.ajax({
        type: "POST",
        data: {
            shipping_name: val1,
            contact_name: val2,
            contact_no: val3,
            address1: val4,
            address2: val5
        },

        //url: 'http://syscom.company/venus-erp/shipping-store2',
        url: 'http://localhost:81/venus-erp/shipping-store2',
              cache: false,
        success: function(response) {
            var response = JSON.parse(response);
            var len = 0;
            if(response['data']=="ERROR")
            {
                alert("Error found in something!!");
            }
            else{
                if (response['data'] != null) {
                len = response['data'].length;
                }
                if(len > 0){
                    
                    //$('#shipping_name').find('option').not(':first').remove();

                    for(var i=0; i<len; i++){
                        var id = response['data'][i].id;
                        var name = response['data'][i].shipping_name;
                        var option = "<option value='"+id+"'>"+name+"</option>";
                        //$("#shipping_name").append($(option));
                        //$('#shipping_name').append(new Option(name, id));
                        $("#shipping_name").append(option);
                        $("#vendor").append(option);
                    }
                    
                    alert('Shipping Added Successfully!!');
                        $('#btn_close2').click();
                }
            }            
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {}
    });


    //preventDefault();
    }

    function cfc_amount_change(id)
    {
        var amt = $("#cfc_amount_"+id).val();
        $("#cfc_cal_amount_"+id).val(amt);
    }

    </script>
@endsection
@section('script')
	<script>
        jQuery(function (){
            $('input').keydown( function(e) {
                var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
                if(key == 13) {
                    e.preventDefault();
                    var inputs = $(this).closest('form').find(':input:visible');
                    inputs.eq( inputs.index(this)+ 1 ).focus();
                }
            });
        });
	</script>
@endsection
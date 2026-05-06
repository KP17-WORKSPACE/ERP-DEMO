@extends('backEnd.master')
@section('mainContent')
@php
function showPicName($data){
$name = explode('/', $data);
return $name[3];
}
@endphp
<link href="{{asset('public/css/add_customer.css')}}" type="text/css" rel="stylesheet">

{{-- <section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('lang.add_new_customer')</h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                <a href="#">{{ __('Human Resource') }} @lang('lang.customer')</a>
                <a href="#"> @lang('lang.add_customer')</a>
            </div>
        </div>
    </div>
</section> --}}
<section class="admin-visitor-area">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-lg-9 col-md-9 col-sm-6 col-xs-12">
                <div class="main-title">
                    <h3 class="mb-30"> Add New Customer</h3>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 text-right">
                <a href="{{url('customers')}}" class="primary-btn small fix-gr-bg">
                     @lang('lang.customer_list') 
                </a>
                @if(isset($editData))
                    <a href="{{url('view-customer',@$editData->id)}}" class="primary-btn small fix-gr-bg">  @lang('lang.view') </a> 
                @endif   
            </div>
  
        </div>

        @if(isset($editData))
        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'customer-update', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
        <input type="hidden" value="{{@$editData->id}}" name="cust_id">
        @else
        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'customer-store', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
        @endif

        <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">  
        <input type="hidden" name="date_of_joining" id="date_of_joining" value="{{date('Y-m-d')}}"> 
        <div class="row">
            <div class="col-lg-12"> 
              <div class="white-box">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="main-title">
                                <h4>  @lang('lang.basic')   @lang('lang.information')</h4>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-10">
                        <div class="col-lg-12">
                            <hr>
                        </div>
                    </div>

                    <div class="row mb-10">
                        <div class="col-lg-3">
                            <div class="input-effect">
                                <input class="primary-input form-control{{ $errors->has('customer_code') ? ' is-invalid' : '' }}" type="text"  name="customer_code" value="{{isset($editData)?@$editData->customer_code:old('customer_code')}}">
                                <span class="focus-border"></span>
                                <label>  @lang('Customer Code') <span>*</span></label>
                                @if ($errors->has('customer_code'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('customer_code') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="input-effect">
                                <input class="primary-input form-control {{$errors->has('customer_name') ? 'is-invalid' : ' '}}" type="text"  name="customer_name" value="{{isset($editData)?@$editData->customer_name:old('customer_name')}}">
                                <span class="focus-border"></span>
                                <label>  @lang('Customer Name') <span>*</span> </label>
                                @if ($errors->has('customer_name'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('customer_name') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="input-effect">
                                <input class="primary-input form-control{{ $errors->has('contcat_person') ? ' is-invalid' : '' }}" type="text"  name="contcat_person" value="{{isset($editData)?@$editData->contcat_person:old('contcat_person')}}">
                                <span class="focus-border"></span>
                                <label>  @lang('Contcat Person') <span>*</span> </label>
                                @if ($errors->has('contcat_person'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('contcat_person') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="input-effect">
                                <input class="primary-input form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" type="email"  name="email" value="{{isset($editData)?@$editData->email:old('email')}}">
                                <span class="focus-border"></span>
                                <label>  @lang('lang.email') <span>*</span> </label>
                                @if ($errors->has('email'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-10">
                        <div class="col-lg-3">
                            <div class="input-effect">
                                <input class="primary-input form-control{{ $errors->has('mobile') ? ' is-invalid' : '' }}" type="number"  name="mobile" value="{{isset($editData)?@$editData->mobile:old('mobile')}}">
                                <span class="focus-border"></span>
                                <label>  @lang('mobile number') <span>*</span> </label>
                                @if ($errors->has('mobile'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('mobile') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="input-effect">
                                <input class="primary-input form-control{{ $errors->has('telephone') ? ' is-invalid' : '' }}" type="number"  name="telephone" value="{{isset($editData)?@$editData->telephone:old('telephone')}}">
                                <span class="focus-border"></span>
                                <label>  @lang('telephone number') <span>*</span> </label>
                                @if ($errors->has('telephone'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('telephone') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="input-effect">
                                <input class="primary-input form-control{{ $errors->has('fax') ? ' is-invalid' : '' }}" type="number"  name="fax" value="{{isset($editData)?@$editData->fax:old('fax')}}">
                                <span class="focus-border"></span>
                                <label>  @lang('fax number') <span>*</span> </label>
                                @if ($errors->has('fax'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('fax') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="col-lg-3">
                            <div class="input-effect">
                                <input class="primary-input form-control{{ $errors->has('vat_number') ? ' is-invalid' : '' }}" type="text"  name="vat_number" value="{{isset($editData)?@$editData->vat_number:old('vat_number')}}">
                                <span class="focus-border"></span>
                                <label>  @lang('VAT Number') <span>*</span> </label>
                                @if ($errors->has('vat_number'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('vat_number') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>                    
                    <div class="row mb-10">
                        <div class="col-lg-6">
                            <div class="input-effect">
                                <textarea class="primary-input form-control {{ $errors->has('address') ? 'is-invalid' : ''}}" cols="0" rows="2" name="address" id="address">{{isset($editData)?@$editData->address:old('address')}}</textarea>
                                <label> @lang('lang.address') <span>*</span> </label>
                                <span class="focus-border textarea"></span>
                                @if ($errors->has('address'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('address') }}</strong>
                                </span>
                                @endif 
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="input-effect">                        
                                <select class="niceSelect w-100 bb form-control{{ $errors->has('salesperson') ? ' is-invalid' : '' }}"
                                    name="salesperson" id="salesperson">
                                    <option data-display="Sales Person *" value="">@lang('lang.select') </option>
                                    @foreach ($staffs as $key => $value)
                                        <option value="{{ @$value->id }}"
                                        
                                        @if(isset($editData))
                                            @if(@$editData->staffs == @$value->id) selected @endif
                                        @else
                                            {{ old('salesperson') == @$value->id ? 'selected' : '' }}
                                        @endif
                                        >{{ @$value->full_name }}</option>
                                    @endforeach
                                </select>
                                <span class="focus-border"></span>
                                @if ($errors->has('payment_terms'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('payment_terms') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                       
                       
                    </div>





                    <div class="row mt-20">
                        <div class="col-lg-12">
                            <div class="main-title">
                                <h4>@lang('Other Details')</h4>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-0">
                        <div class="col-lg-12">
                            <hr>
                        </div>
                    </div>

                    <div class="row mb-10">

                        <div class="col-lg-3">
                            <div class="input-effect">
                        
                                <input class="primary-input form-control{{ $errors->has('sales_person_name') ? ' is-invalid' : '' }}" type="text"  name="sales_person_name" value="{{isset($editData)?@$editData->sales_person_name:old('sales_person_name')}}">
                                <span class="focus-border"></span>
                                <label>@lang('Sales Person Name') * </label>
                                @if ($errors->has('sales_person_name'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('sales_person_name') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-3">
                            <div class="input-effect">
                        
                                <input class="primary-input form-control{{ $errors->has('credit_limit') ? ' is-invalid' : '' }}" type="text"  name="credit_limit" value="{{isset($editData)?@$editData->credit_limit:old('credit_limit')}}">
                                <span class="focus-border"></span>
                                <label>@lang('Credit Limit') * </label>
                                @if ($errors->has('credit_limit'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('credit_limit') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-3">
                            <div class="input-effect">                        
                                <input class="primary-input form-control{{ $errors->has('credit_days') ? ' is-invalid' : '' }}" type="text"  name="credit_days" value="{{isset($editData)?@$editData->credit_days:old('credit_days')}}">
                                <span class="focus-border"></span>
                                <label>@lang('Credit Days') * </label>
                                @if ($errors->has('credit_days'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('credit_days') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
            
                <div class="col-lg-3">
                    <div class="input-effect">                        
                        <select class="niceSelect w-100 bb form-control{{ $errors->has('payment_terms') ? ' is-invalid' : '' }}"
                            name="payment_terms" id="payment_terms">
                            <option data-display="Payment Terms *" value="">@lang('lang.select') </option>
                            @foreach ($paymentterms as $key => $value)
                                <option value="{{ @$value->id }}"
                                
                                @if(isset($editData))
                                    @if(@$editData->payment_terms == @$value->id) selected @endif
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
                        <input class="primary-input form-control{{ $errors->has('accountant_name') ? ' is-invalid' : '' }}" type="text"  name="accountant_name" value="{{isset($editData)?@$editData->accountant_name:old('accountant_name')}}">
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
                        <input class="primary-input form-control{{ $errors->has('accountant_email') ? ' is-invalid' : '' }}" type="text"  name="accountant_email" value="{{isset($editData)?@$editData->accountant_email:old('accountant_email')}}">
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
                        <input class="primary-input form-control{{ $errors->has('accountant_number') ? ' is-invalid' : '' }}" type="text"  name="accountant_number" value="{{isset($editData)?@$editData->accountant_number:old('accountant_number')}}">
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
                    <button type="button" class="primary-btn small fix-gr-bg" id="{{@$edit->quotation_type=="equipment"? 'addRowEquipment':'addRowProduct'}}">
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
                        <input class="primary-input form-control{{ $errors->has('accountant_name') ? ' is-invalid' : '' }}" type="text"  name="accountant_name" value="{{isset($editData)?@$editData->accountant_name:old('accountant_name')}}">
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
                        <input class="primary-input form-control{{ $errors->has('accountant_email') ? ' is-invalid' : '' }}" type="text"  name="accountant_email" value="{{isset($editData)?@$editData->accountant_email:old('accountant_email')}}">
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
                        <input class="primary-input form-control{{ $errors->has('accountant_number') ? ' is-invalid' : '' }}" type="text"  name="accountant_number" value="{{isset($editData)?@$editData->accountant_number:old('accountant_number')}}">
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
                    <button type="button" class="primary-btn small fix-gr-bg" id="{{@$edit->quotation_type=="equipment"? 'addRowEquipment':'addRowProduct'}}">
                    <span class="ti-plus pr-2"></span>
                    @lang('Add') @lang('Accountant')
                </button>
                </div>
            </div>
            <!-- end row -->         


            <div class="row mt-10">
                <div class="col-lg-12 text-center">
                    <button class="primary-btn fix-gr-bg">
                        <span class="ti-check"></span>
                        @if(isset($editData)) @lang('lang.update') @else @lang('lang.add') @endif @lang('lang.customer')
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
{{ Form::close() }} 
</div>
</section>
@endsection

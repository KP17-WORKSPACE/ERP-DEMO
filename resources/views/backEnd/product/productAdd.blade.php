@extends('backEnd.master')
@section('mainContent')
    @php
    function showPicName($data){
    $name = explode('/', $data);
    return $name[3];
    }


    @endphp
    <link href="{{ asset('public/css/add_staff.css') }}" type="text/css" rel="stylesheet">

    @php
    $modules = [];
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();

    foreach($permissions as $permission){ @$module_links[] = @$permission->module_link_id; @$modules[] =
    @$permission->moduleLink->module_id;}

    $modules = array_unique(@$modules);
    @endphp
    <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">

                @if(isset($editData))
                    <h1>@lang('Purchase Order') @lang('Edit')</h1>
                @else
                    <h1>@lang('Purchase Order') @lang('Add')</h1>
                @endif

                <div class="bc-pages">
                    <a href="{{ url('dashboard') }}">@lang('lang.dashboard')</a>
                    <a href="#">@lang('Purchase Order')</a>

                    @if(isset($editData))
                    <a href="#">@lang('Purchase Order Edit')</a>
                    @else
                    <a href="#">@lang('Purchase Order Add')</a>
                    @endif
                    

                </div>
            </div>
        </div>
    </section>
    
    <section class="admin-visitor-area">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-9 col-md-9 col-sm-6 col-xs-12">
                    <div class="main-title">
                        <h3 class="mb-30">  @lang('Purchase Order Information')</h3>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 text-right">
                    <a href="{{url('company')}}" class="primary-btn small fix-gr-bg">
                         @lang('Purchase Order List') 
                    </a>
                    @if(isset($editData))
                        {{-- <a href="{{url('company',@$editData->id)}}" class="primary-btn small fix-gr-bg">  @lang('lang.view') </a> --}}
                    @endif   
                </div>
      
            </div>
    

        
            @if(isset($editData))
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'company-update/'. @$editData->id, 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}
            <input type="hidden" value="{{@$editData->id}}" name="cust_id">
            @else
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'company-store', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
            @endif
    
            <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">  
            <input type="hidden" name="date_of_joining" id="date_of_joining" value="{{date('Y-m-d')}}"> 
            <div class="row">
                <div class="col-lg-12"> 
                  <div class="white-box">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="main-title">
                                    <h4>  @lang('Purchase Order Detail')</h4>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-20">
                            <div class="col-lg-12">
                                <hr>
                            </div>
                        </div>
    
                        <div class="row mb-40">
                            <div class="col-lg-4">
                                <div class="input-effect">
                                    <input class="primary-input form-control {{$errors->has('product_name') ? 'is-invalid' : ' '}}" type="text"  name="product_name" value="{{isset($editData)?@$editData->company_name:old('product_name')}}">
                                    <span class="focus-border"></span>
                                    <label>  @lang('Product Name') <span>*</span> </label>
                                    @if ($errors->has('product_name'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('product_name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="input-effect">
                                    <input class="primary-input form-control{{ $errors->has('product_code') ? ' is-invalid' : '' }}" type="text"  name="product_code" value="{{isset($editData)?@$editData->product_code:old('product_code')}}">
                                    <span class="focus-border"></span>
                                    <label>  @lang('Product Code') <span>*</span></label>
                                    @if ($errors->has('product_code'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('product_code') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="input-effect">
                                    <input class="primary-input form-control{{ $errors->has('part_number') ? ' is-invalid' : '' }}" type="text"  name="part_number" value="{{isset($editData)?@$editData->part_number:old('part_number')}}">
                                    <span class="focus-border"></span>
                                    <label>  @lang('Part Number') <span>*</span> </label>
                                    @if ($errors->has('part_number'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('part_number') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div> 
                            </div>
                        
                            <div class="row mb-40">
                                <div class="col-lg-4">
                                    <div class="input-effect">
                                        <input class="primary-input form-control{{ $errors->has('describtion') ? ' is-invalid' : '' }}" type="text"  name="describtion" value="{{isset($editData)?@$editData->describtion:old('describtion')}}">
                                        <span class="focus-border"></span>
                                        <label>  @lang('Describtion') <span>*</span> </label>
                                        @if ($errors->has('describtion'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('describtion') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="input-effect">
                                        <input class="primary-input form-control{{ $errors->has('website') ? ' is-invalid' : '' }}" type="website"  name="website" value="{{isset($editData)?@$editData->website:old('website')}}">
                                        <span class="focus-border"></span>
                                        <label>  @lang('Website') <span>*</span> </label>
                                        @if ($errors->has('website'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('website') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="input-effect">
                                        <textarea class="primary-input form-control {{ $errors->has('company_address') ? 'is-invalid' : ''}}" cols="0" rows="4" name="company_address" id="company_address">{{isset($editData)?@$editData->company_address:old('company_address')}}</textarea>
                                        <label> @lang('Company Address') <span>*</span> </label>
                                        <span class="focus-border textarea"></span>
                                        @if ($errors->has('company_address'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('company_address') }}</strong>
                                        </span>
                                        @endif 
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-40">
                                <div class="col-lg-4">
                                    <div class="input-effect">
                                        <input class="primary-input form-control{{ $errors->has('telephone') ? ' is-invalid' : '' }}" type="telephone"  name="telephone" value="{{isset($editData)?@$editData->telephone:old('telephone')}}">
                                        <span class="focus-border"></span>
                                        <label>  @lang('Telephone') <span>*</span> </label>
                                        @if ($errors->has('telephone'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('telephone') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="input-effect">
                                        <input class="primary-input form-control{{ $errors->has('fax') ? ' is-invalid' : '' }}" type="fax"  name="fax" value="{{isset($editData)?@$editData->fax:old('fax')}}">
                                        <span class="focus-border"></span>
                                        <label>  @lang('Fax') <span>*</span> </label>
                                        @if ($errors->has('fax'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('fax') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="input-effect">
                                        <input class="primary-input form-control{{ $errors->has('mobile') ? ' is-invalid' : '' }}" type="mobile"  name="mobile" value="{{isset($editData)?@$editData->mobile:old('mobile')}}">
                                        <span class="focus-border"></span>
                                        <label>  @lang('Mobile') <span>*</span> </label>
                                        @if ($errors->has('mobile'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('mobile') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-40">
                                <div class="col-lg-12">
                                    <div class="main-title">
                                        <h4>@lang('Other Details')</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-20">
                                <div class="col-lg-12">
                                    <hr>
                                </div>
                            </div>
                            <div class="row mt-40">
                            <div class="col-lg-4">
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
                            <div class="col-lg-4">
                                 <div class="input-effect">
                                     <input class="primary-input form-control{{ $errors->has('trade_license_no') ? ' is-invalid' : '' }}" type="trade_license_no"  name="trade_license_no" value="{{isset($editData)?@$editData->trade_license_no:old('trade_license_no')}}">
                                     <span class="focus-border"></span>
                                     <label>  @lang('Trade License Number') <span>*</span> </label>
                                     @if ($errors->has('trade_license_no'))
                                     <span class="invalid-feedback" role="alert">
                                         <strong>{{ $errors->first('trade_license_no') }}</strong>
                                     </span>
                                     @endif
                                 </div>
                             </div>
                             <div class="col-lg-4">
                                <div class="no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            <input
                                                class="primary-input date form-control{{ $errors->has('trade_license_exp_date') ? ' is-invalid' : '' }}"
                                                id="trade_license_exp_date" type="text" name="trade_license_exp_date"
                                                value="{{ date('m/d/Y') }}">
                                            <span class="focus-border"></span>
                                            <label>@lang('Trade License Expiry Date')<span>*</span> </label>
                                            @if ($errors->has('trade_license_exp_date'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('trade_license_exp_date') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <button class="" type="button">
                                            <i class="ti-calendar" id="trade_license_exp_date"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>    
                        <div class="row mt-40">
                            <div class="col-lg-12">
                                <div class="main-title">
                                    <h4>@lang('Bank Details')</h4>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-20">
                            <div class="col-lg-12">
                                <hr>
                            </div>
                        </div>
    
                        <div class="row mb-30">
    
                            <div class="col-lg-4">
                                <div class="input-effect">
                            
                                    <input class="primary-input form-control{{ $errors->has('bank_name') ? ' is-invalid' : '' }}" type="text"  name="bank_name" value="{{isset($editData)?@$editData->bank_name:old('bank_name')}}">
                                    <span class="focus-border"></span>
                                    <label>@lang('Bank Name') * </label>
                                    @if ($errors->has('bank_name'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('bank_name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
    
                            <div class="col-lg-4">
                                <div class="input-effect">
                            
                                    <input class="primary-input form-control{{ $errors->has('account_number') ? ' is-invalid' : '' }}" type="text"  name="account_number" value="{{isset($editData)?@$editData->account_number:old('account_number')}}">
                                    <span class="focus-border"></span>
                                    <label>@lang('Account Number') * </label>
                                    @if ($errors->has('account_number'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('account_number') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                    </div> 
    
    
                <!-- Bank Info Details -->
                <div class="row mt-40">
                    <div class="col-lg-12">
                        <div class="main-title">
                            <h4>@lang('Images')</h4>
                        </div>
                    </div>
                </div>
                <div class="row mb-30">
                    <div class="col-lg-12">
                        <hr>
                    </div>
                </div>
                <div class="row mb-40">
                    <div class="col-lg-4">
                        <div class="row no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">

                                    <input
                                        class="primary-input form-control {{ $errors->has('company_logo') ? ' is-invalid' : '' }}"
                                        type="text" id="company_logo"
                                        placeholder="{{ isset($editData->company_logo) && @$editData->company_logo != '' ? showPicName(@$editData->company_logo) : 'Company Logo *' }}"
                                        disabled>
                                    <span class="focus-border"></span>

                                    @if ($errors->has('company_logo'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('company_logo') }}</strong>
                                        </span>
                                    @endif

                                </div>
                            </div>
                            <div class="col-auto">
                                <button class="primary-btn-small-input" type="button">
                                    <label class="primary-btn small fix-gr-bg"
                                        for="company_logo">@lang('lang.browse')</label>
                                    <input type="file" class="d-none" name="company_logo" id="company_logo">
                                </button>
                            </div>
                        </div>
                    </div>
    
                    <div class="col-lg-4">
                        <div class="row no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">

                                    <input
                                        class="primary-input form-control {{ $errors->has('digital_stamp') ? ' is-invalid' : '' }}"
                                        type="text" id="digital_stamp"
                                        placeholder="{{ isset($editData->digital_stamp) && @$editData->digital_stamp != '' ? showPicName(@$editData->digital_stamp) : 'Digital Stamp *' }}"
                                        disabled>
                                    <span class="focus-border"></span>

                                    @if ($errors->has('digital_stamp'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('digital_stamp') }}</strong>
                                        </span>
                                    @endif

                                </div>
                            </div>
                            <div class="col-auto">
                                <button class="primary-btn-small-input" type="button">
                                    <label class="primary-btn small fix-gr-bg"
                                        for="digital_stamp">@lang('lang.browse')</label>
                                    <input type="file" class="d-none" name="digital_stamp" id="digital_stamp">
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end row -->          
    
    
                <div class="row mt-40">
                    <div class="col-lg-12 text-center">
                        <button class="primary-btn fix-gr-bg">
                            <span class="ti-check"></span>
                            @if(isset($editData)) @lang('lang.update') @else @lang('lang.add') @endif @lang('Purchase Order')
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

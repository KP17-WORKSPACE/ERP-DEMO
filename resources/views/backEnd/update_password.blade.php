@extends('backEnd.newmasterpage')
@section('mainContent')
@php
	$module_links = [];
	$permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
@endphp

<div class="content-container col-12">
    <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
        <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
            <div class="purchase-order-content-header">
                <h4 class="purchase-order-content-header-left">
                    Change Password
                </h4>
                <div class="purchase-order-content-header-right">
                    <a class="btn btn-light" href="{{url('crm-dashboard')}}">Dashboard
                    </a>
                </div>
            </div>

            
            <div class="card mb-3">
                <div class="card-body">
					<div class="row">
			<div class="col-lg-12">
                
                <div class="white-box">
                	<div class="col-lg-6 offset-lg-3"> 


		@if(Illuminate\Support\Facades\Config::get('app.app_sync'))
        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'admin-dashboard', 'method' => 'GET', 'enctype' => 'multipart/form-data']) }}
        @else
		{{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'change-password', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'search_student']) }}
        @endif


	                	@if(session()->has('message-success') != "")
		                    @if(session()->has('message-success'))
		                    <div class="alert alert-success">
		                        {{ session()->get('message-success') }}
		                    </div>
		                    @endif
		                @endif
		                 @if(session()->has('message-danger') != "")
		                    @if(session()->has('message-danger'))
		                    <div class="alert alert-danger">
		                        {{ session()->get('message-danger') }}
		                    </div>
		                    @endif
		                @endif
		            </div>

                        
                            <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">

                            <div class="row mb-4">
	                            <div class="col-lg-6 offset-lg-3">
		                            <div class="input-effect">
		                                <label>@lang('lang.current') @lang('lang.password')</label>
		                                <input class="primary-input dynamicstxt_s form-control{{ $errors->has('current_password') || session()->has('password-error') ? ' is-invalid' : '' }}" type="password" name="current_password">
		                                <span class="focus-border"></span>
		                                @if ($errors->has('current_password'))
		                                <span class="invalid-feedback" role="alert">
		                                    <strong>{{ $errors->first('current_password') }}</strong>
		                                </span>
		                                @endif
		                                @if (session()->has('password-error'))
		                                <span class="invalid-feedback" role="alert">
		                                    <strong>{{ session()->get('password-error') }}</strong>
		                                </span>
		                                @endif
		                            </div>
		                        </div>
		                    </div>

                            <div class="row mb-4">
	                            <div class="col-lg-6 offset-lg-3">
		                            <div class="input-effect">
		                                <label>@lang('New') @lang('lang.password')</label>
		                                <input class="primary-input dynamicstxt_s form-control{{ $errors->has('new_password') ? ' is-invalid' : '' }}" type="password" name="new_password">
		                                <span class="focus-border"></span>
		                                @if ($errors->has('new_password'))
		                                <span class="invalid-feedback" role="alert">
		                                    <strong>{{ $errors->first('new_password') }}</strong>
		                                </span>
		                                @endif
		                            </div>
		                        </div>
		                    </div>

                            <div class="row mb-4">
	                            <div class="col-lg-6 offset-lg-3">
		                            <div class="input-effect">
		                                <label>@lang('lang.confirm') @lang('lang.password')</label>
		                                <input class="primary-input dynamicstxt_s form-control{{ $errors->has('confirm_password') ? ' is-invalid' : '' }}" type="password" name="confirm_password">
		                                <span class="focus-border"></span>
		                                @if ($errors->has('confirm_password'))
		                                <span class="invalid-feedback" role="alert">
		                                    <strong>{{ $errors->first('confirm_password') }}</strong>
		                                </span>
		                                @endif
		                            </div>
		                        </div>
		                    </div>


                            

                            <div class="row">
	                            <div class="col-lg-6 offset-lg-3">


									@if(Illuminate\Support\Facades\Config::get('app.app_sync'))
									<span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="Disabled For Demo "> <button class="primary-btn small fix-gr-bg  demo_view" style="pointer-events: none;" type="button" disabled> @lang('lang.change') @lang('lang.password')</button></span>
								@else
<button class="btn btn-light" type="submit">
	<i class="ico icon-outline-bookmark-square text-success"></i> @lang('lang.change') @lang('lang.password')
</button>
								@endif 
	                               
	                            </div>
	                        </div>
                       
                    {{ Form::close() }}
                </div>
            </div>
		</div>
                </div>
            </div>
			
			
		</div>
	</div>
</div>

@endsection
<?php
$setting = App\SmGeneralSettings::find(1);
if(isset($setting->copyright_text)){ $copyright_text = $setting->copyright_text; }else{ $copyright_text = 'Copyright © 2019 All rights reserved | This template is made with by Codethemes'; }
if(isset($setting->logo)) { $logo = $setting->logo; } else{ $logo = 'public/uploads/settings/logo.png'; }
if(isset($setting->favicon)) { $favicon = $setting->favicon; } else{ $favicon = 'public/backEnd/img/favicon.png'; }
$login_background = App\SmBackgroundSetting::where([['is_default',1],['title','Login Background']])->first(); 
if(empty($login_background)){ $css = "background: url(".url('public/backEnd/img/login-bg.jpg').")  no-repeat center; background-size: cover; ";}
else{ if(!empty($login_background->image)){  $css = "background: url('". url($login_background->image) ."')  no-repeat center;  background-size: cover;"; }else{ $css = "background:".$login_background->color; } } 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{asset($favicon)}}" type="image/png"/>
    <title>{{ __('Login  | ')}}{{ !empty($setting->site_title)?$setting->site_title:'Infix Business ERP' }}</title>
    <meta name="_token" content="{!! csrf_token() !!}"/>
    <link rel="stylesheet" href="{{asset('public/backEnd/')}}/vendors/css/bootstrap.css" />
    <link rel="stylesheet" href="{{asset('public/backEnd/')}}/vendors/css/themify-icons.css" />
    <link rel="stylesheet" href="{{asset('public/backEnd/')}}/css/style.css" />
</head>
<body class="login admin hight_100" style="{{$css}}">

    <!--================ Start Login Area =================-->
	<section class="login-area up_login">
		<div class="container"> 
			<input type="hidden" id="url" value="{{url('/')}}">
			<div class="row login-height justify-content-center align-items-center">
				@php $s= DB::table('users')->first(); $email=$s->email; @endphp 
				<div class="col-lg-5 col-md-8">
					<div class="form-wrap text-center">
						<div class="logo-container">
							<a href="{{url('/')}}">
								<img src="{{asset($setting->logo)}}" alt="" class="logoimage">
							</a>
						</div>
						<h5 class="text-uppercase">Login Details</h5>

						<?php if(session()->has('message-success') != ""): ?>
		                    <?php if(session()->has('message-success')): ?>
		                    <p class="text-success"><?php echo e(session()->get('message-success')); ?></p>
		                    <?php endif; ?>
		                <?php endif; ?>
		                <?php if(session()->has('message-danger') != ""): ?>
		                    <?php if(session()->has('message-danger')): ?>
		                    <p class="text-danger"><?php echo e(session()->get('message-danger')); ?></p>
		                    <?php endif; ?>
		                <?php endif; ?>
						<form method="POST" class="" action="<?php echo e(route('login')); ?>">
                        <?php echo csrf_field(); ?>

							<div class="form-group input-group mb-4 mx-3">
								<span class="input-group-addon">
									<i class="ti-email"></i>
								</span>
								<input class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" type="email" name='email' id="email" placeholder="Enter Email address" value="{{$email}}" />
								@if ($errors->has('email'))
                                    <span class="invalid-feedback text-left pl-3" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
							</div>

							<div class="form-group input-group mb-4 mx-3">
								<span class="input-group-addon">
									<i class="ti-key"></i>
								</span>
								<input class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" type="password" name='password' id="password" placeholder="Enter Password" value="123456" />
								@if ($errors->has('password'))
                                    <span class="invalid-feedback text-left pl-3" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
							</div>
							
							<div class="d-flex justify-content-between pl-30">
								<div class="checkbox">
									<input class="form-check-input" type="checkbox" name="remember" id="rememberMe" {{ old('remember') ? 'checked' : '' }}>
									<label for="rememberMe">{{ __('Remember Me')}}</label>
								</div>
								<div>
									<a href="<?php echo e(url('recovery/passord')); ?>">{{ __('Forget Password?')}}</a>
								</div>
							</div>

							<div class="form-group mt-30 mb-30">
								<button type="submit" class="primary-btn fix-gr-bg">
									<span class="ti-lock mr-2"></span>
									{{ __('Login')}}
                                </button>
							</div>
						</form>
					</div>
					
				</div>
			</div>
		</div>
	</section> 
	<footer class="footer_area">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-lg-12 text-center"> 
					<p> {{$copyright_text}} </p>   
				</div>
			</div>
		</div>
	</footer>
	<!--================ End Footer Area =================-->
    <script src="{{asset('public/backEnd/')}}/vendors/js/jquery-3.2.1.min.js"></script>
    <script src="{{asset('public/backEnd/')}}/vendors/js/popper.js"></script>
	<script src="{{asset('public/backEnd/')}}/vendors/js/bootstrap.min.js"></script>
	<script src="{{asset('public/backEnd/')}}/js/login.js"></script>
</body>
</html>

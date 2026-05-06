<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Venus ERP - Login</title>
    <link rel="icon" href="{{ asset('public/design') }}/assets/images/erp-logo-icon.png" type="image/png"/>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('public/design') }}/assets/css/_main.css" />
    <link rel="stylesheet" href="{{ asset('public/design') }}/assets/css/_icomoon.css" />
    <link rel="stylesheet" href="{{ asset('public/design') }}/assets/css/login.css" />
</head>

<body>
    <div class="venus-app page-login">
        <section class="content-container">
            <div class="content-bg">
                <div class="company-details">
                    <div class="logo"><img src="{{ asset('public/design') }}/assets/images/logo-color.png" alt="LOGO" /></div>
                    <div class="caption">Empowering you to close more deals and <span class="caption-highlight">Grow smarter every day.</span></div>
                    <div class="caption-details">Welcome to your ultimate sales companion! Track performance, manage leads, monitor deals, and stay on top of targets effortlessly.</div>
                </div>
                <form action="{{ route('login') }}" name="login" method="POST" id="infix_form2" class="login-box" >
                    @csrf
											@if(session()->has('message-success') != "")
												@if(session()->has('message-success'))
													<p class="text-success">{{session()->get('message-success')}}</p>
												@endif
											@endif
											@if(session()->has('message-danger') != "")
												@if(session()->has('message-danger'))
													<p class="text-danger">{{session()->get('message-danger')}}</p>
												@endif
											@endif
                    <h3>Login to Venus</h3>
                    <div class="login-fields">
                        <div class="login-field">
                            <label for="exampleFormControlInput1" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="exampleFormControlInput1" name="email" placeholder="Email address" />
                        </div>
                        <div class="login-field">
                            <label for="exampleFormControlInput1" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password" />
                            <span class="show-password-btn"><img src="{{ asset('public/design') }}/assets/images/ic_eye.png" alt="EYE" onclick="togglePassword()" /></span>
                        </div>
                    </div>
                    <div class="forgot-password"><a href="{{ url('recovery/passord') }}">Forgot password?</a></div>
                    <button type="submit" class="btn btn-dark btn-login-submit">Login</button>
                    <div class="create-account">New user? <a href="#">Create an account</a></div>
                    
                    <input type="hidden" id="latitude" name="latitude"/>
                    <input type="hidden" id="longitude" name="longitude"/>
                    <input type="hidden" id="location" name="location"/>
                    <script>
                        function togglePassword(){
                            if($('#password').attr('type') === 'password' ){
                                $('#password').attr('type','text');
                            } else { $('#password').attr('type','password'); }
                        }


                        const Http = new XMLHttpRequest();
                        getApi("https://api.bigdatacloud.net/data/reverse-geocode-client");
                        function getApi(bdcApi) {
                            Http.open("GET", bdcApi);
                            Http.send();
                            Http.onreadystatechange = function () {
                                if (this.readyState == 4 && this.status == 200) {
                                    //result.innerHTML = this.responseText;
                                    get_loc = JSON.parse(this.responseText);
                    
                                    $('#latitude').val(get_loc.latitude);
                                    $('#longitude').val(get_loc.longitude);
                                    $('#location').val(get_loc.locality);
                                }
                                else
                                {
                                
                                }
                            };
                        }
                        </script>

                </form>
            </div>
        </section>
        <footer class="login-footer">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-8">Copyright © {{ date('Y') }}  Venus, All Rights Reserved</div>
                    <div class="col-4 d-flex justify-content-end">
                        <div class="powered-by">
                            <span>Powered By</span>
                            <img src="{{ asset('public/design') }}/assets/images/powered-by.png" alt="Powered by" />
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
    <script src="{{ asset('public/design') }}/assets/js/_main.js" type="text/javascript"></script>
</body>

</html>



<?php /*
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="{{ asset('public/admin-iroid/') }}/img/erp-logo-icon.png" type="image/png"/>
    <title>Venus ERP - Login</title>
    <link href="{{asset('public/admin-iroid/')}}/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="{{asset('public/admin-iroid/')}}/css/style.css" rel="stylesheet">
    <style>
        input:-webkit-autofill,
input:-webkit-autofill:hover, 
input:-webkit-autofill:focus, 
input:-webkit-autofill:active{
    -webkit-box-shadow: 0 0 0 30px white inset !important;
}
    </style>
</head>

<body class="" style="background-image: url({{asset('public/admin-iroid/')}}/img/crm-login-bg.jpg); background-position: center; background-size: cover;">
    <div class="container">
        <!-- Outer Row -->
        <div class="content-container">
            <div class="row justify-content-center" id="login__class">
                <div class="col-xl-10 col-lg-12 col-md-10">
                    <div class="card o-hidden border-0 shadow-lg">
                        <div class="card-body p-0">
                            <!-- Nested Row within Card Body -->
                            <div class="row">
                                <div class="col-lg-5 bg-login-image d-none align-items-end d-lg-flex">
                                    <!-- <div class="p-4 w-100">
                                        <h4 class="font-weight-bold mb-3 text-white">Welcome Back</h4>
                                        <div>
                                            <button class="btn btn-white btn-block py-2">Email Login</button>
                                            <button class="btn btn-dark btn-block py-2">I Am Login</button>
                                        </div>
                                    </div> -->
                                </div>
                                <div class="col-lg-7">
                                    <div class="main-body">
                                        <div class=" title-container text-center mb-4">
                                            {{--  <h4 class="font-weight-bolder text-uppercase">LOGO</h4>  --}}
                                            <img src="{{asset('public/admin-iroid/')}}/img/erp-logo-b.png" width="200" alt="">
                                         
                                        </div>

                                        <form action="{{ route('login') }}" name="login" method="POST" id="infix_form2" class="user" >
                                            @csrf
											@if(session()->has('message-success') != "")
												@if(session()->has('message-success'))
													<p class="text-success">{{session()->get('message-success')}}</p>
												@endif
											@endif
											@if(session()->has('message-danger') != "")
												@if(session()->has('message-danger'))
													<p class="text-danger">{{session()->get('message-danger')}}</p>
												@endif
											@endif
                                        <div class="row alert-box error-row hide">
                                            <span class="error" id="error"></span>
                                        </div>
                                        <div class="input-group position-relative">
                                            <label for="id_username " class="mb-0">Email</label>
                                            <input type="email" name="email" id="exampleInputEmail" aria-describedby="emailHelp" placeholder="Account ID/Email">
                                            <i class="fa fa-envelope email__1"></i>
											@if ($errors->has('email'))
												<span class="invalid-feedback d-block email_input" role="alert" >
													<strong>{{ $errors->first('email') }}</strong>
												</span>
											@endif
                                        </div>

                                        <div class="input-group position-relative">
                                            <label for="id_password" class="mb-0">Password</label>
                                            <input type="password" name="password" id="exampleInputPassword" placeholder="***************">
                                            <i class="fa fa-eye email__1"></i>
													@if ($errors->has('password'))
												<span class="invalid-feedback d-block email_input" role="alert" >
													<strong>{{ $errors->first('password') }}</strong>
												</span>
											@endif                                                
                                        </div>

                                        <div class="row mt-4">
                                            <div class="col-lg-6 mb-2">
                                                <button type="submit" id="btnsubmit2" class="btn btn-primary btn-block">@lang('lang.login')</button>
                                            </div>
                                            <div class="col-lg-6">
                                                <a class="btn btn-info btn-block">Register</a>
                                            </div>
                                        </div>

                                        <div class="forgot-row d-flex justify-content-between mt-2">
                                            <div class="left">
                                                {{--  <a href="" target="_self" id="forgot-email">Forgot Email?</a>  --}}
                                            </div>
                                            <div class="right">
                                                <a href="{{ url('recovery/passord') }}" target="_self" id="forgot-password">Forgot password?</a>
                                            </div>
                                        </div>

                                        
                                        </form>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>


    </div>
    <!-- script -->
    <script src="{{asset('public/admin-iroid/')}}/vendor/jquery/jquery.min.js"></script>
    <script src="{{asset('public/admin-iroid/')}}/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{{asset('public/admin-iroid/')}}/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="{{asset('public/admin-iroid/')}}/js/sb-admin-2.min.js"></script>
    <script src="{{asset('public/admin-iroid/')}}/vendor/chart.js/Chart.min.js"></script>
    <script src="{{asset('public/admin-iroid/')}}/js/demo/chart-area-demo.js"></script>
    <script src="{{asset('public/admin-iroid/')}}/js/demo/chart-pie-demo.js"></script>
    <!-- script -->
</body>

</html>l>

*/
 ?>
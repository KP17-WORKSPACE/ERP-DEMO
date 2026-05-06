
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Venus ERP - Login</title>
    <link rel="icon" href="{{ asset('public/design') }}/assets/images/erp-logo-icon.png" type="image/png" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('public/design') }}/assets/css/_main.css" />
    <link rel="stylesheet" href="{{ asset('public/design') }}/assets/css/_icomoon.css" />
    <link rel="stylesheet" href="{{ asset('public/design') }}/assets/css/login.css" />
</head>

<body>



    <style>
        .input-group {
            position: relative;
            width: 100%;
        }

        .input-group input {
            border-top-right-radius: 25px;
            border-bottom-right-radius: 25px;
        }


        .show-password-btn {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
        }

        .show-password-btn img {
            width: 20px;
            height: 20px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #2d5a3d 0%, #4a7c59 50%, #6ba16f 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 900px;
            display: flex;
            justify-content: center;
        }

        .login-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            display: flex;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            width: 100%;
            min-height: 500px;
        }

        .welcome-section {
            background: linear-gradient(135deg, #2d5a3d 0%, #4a7c59 50%, #6ba16f 100%);
            color: white;
            padding: 60px 40px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
        }

        .welcome-section::before {
            content: '';
            position: absolute;
            bottom: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .logo {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            font-size: 24px;
            font-weight: bold;
        }

        .flame-icon {
            background: white;
            color: #4a7c59;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-size: 20px;
        }

        .logo-text {
            font-weight: normal;
        }

        .welcome-section h1 {
            font-size: 32px;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .welcome-section p {
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 30px;
            opacity: 0.9;
        }

        .sign-in-btn {
            background: transparent;
            border: 2px solid white;
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .sign-in-btn:hover {
            background: white;
            color: #4a7c59;
        }

        .footer-text {
            font-size: 12px;
            opacity: 0.8;
        }

        .footer-text span {
            font-weight: bold;
        }

        .form-section {
            flex: 1;
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .form-header h2 {
            color: #2d5a3d;
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .form-header p {
            color: #666;
            font-size: 14px;
        }

        .login-form {
            width: 100%;
            max-width: 300px;
            margin: 0 auto;
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-group input {
            width: 100%;
            padding: 15px 20px;
            border: 1px solid #e0e0e0;
            border-radius: 25px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.3s ease;
            background: #f8f9fa;
            border-top-right-radius: 25px !important;
            border-bottom-right-radius: 25px !important;
        }

        .input-group input:focus {
            border-color: #4a7c59;
            background: white;
        }

        .forgot-password {
            text-align: center;
            margin-bottom: 30px;
        }

        .forgot-password a {
            color: #666;
            text-decoration: none;
            font-size: 12px;
        }

        .forgot-password a:hover {
            color: #4a7c59;
        }

        .login-btn {
            width: 100%;
            background: #4a7c59;
            color: white;
            border: none;
            padding: 15px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-bottom: 20px;
        }

        .login-btn:hover {
            background: #2d5a3d;
        }

        .signup-link {
            text-align: center;
        }

        .signup-link span {
            color: #666;
            font-size: 12px;
        }

        .signup-link a {
            color: #4a7c59;
            text-decoration: none;
            font-weight: bold;
        }

        .signup-link a:hover {
            text-decoration: underline;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .login-card {
                flex-direction: column;
                max-width: 400px;
            }

            .welcome-section {
                padding: 40px 30px;
                order: 2;
            }

            .form-section {
                padding: 40px 30px;
                order: 1;
            }

            .welcome-section h1 {
                font-size: 24px;
            }

            .form-header h2 {
                font-size: 24px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 10px;
            }

            .welcome-section,
            .form-section {
                padding: 30px 20px;
            }

            .login-form {
                max-width: 100%;
            }
        }
    </style>

    <div class="container">
        <div class="login-card">
            <div class="welcome-section">
                <div class="logo">
                    <div class="flame-icon">🔥</div>
                    <span class="logo-text">Venus ERP</span>
                </div>
                <h1>Welcome Back!</h1>
                <p>Hi there! join us today,<br>please create your account to get started</p>

                <button class="sign-in-btn" onclick="window.location.href='http://erpdemo.venushrms.com/public/design/new-signup.html'">SIGN UP</button>
                <p class="footer-text">ALREADY HAVE AN ACCOUNT ? <span> <a href="{{ url('login') }}" class="text-white fw-bold">LOG
                            IN</a> </span></p>
            </div>

            <div class="form-section">
                <div class="form-header">
                    <h2>Welcome</h2>
                    <p>Login to your account to continue</p>
                </div>

                <form action="{{ route('login') }}" name="login" method="POST" id="infix_form2" class="login-box">
                    @csrf

                    @if (session()->has('message-success') != '')
                        @if (session()->has('message-success'))
                            <p class="text-success">{{ session()->get('message-success') }}</p>
                        @endif
                    @endif
                    @if (session()->has('message-danger') != '')
                        @if (session()->has('message-danger'))
                            <p class="text-danger">{{ session()->get('message-danger') }}</p>
                        @endif
                    @endif

                    <div class="input-group">
                        <input type="email" name="email" placeholder="Email" required>
                    </div>


                    <div class="input-group">
                        <input type="password" placeholder="Password" id="password" name="password" required>
                        <span class="show-password-btn" onclick="togglePassword()">
                            <img src="{{ asset('public/design') }}/assets/images/ic_eye.png" alt="EYE" />
                        </span>
                    </div>


                    <input type="hidden" id="latitude" name="latitude" />
                    <input type="hidden" id="longitude" name="longitude" />
                    <input type="hidden" id="location" name="location" />
                    <script>
                        function togglePassword() {
                            if ($('#password').attr('type') === 'password') {
                                $('#password').attr('type', 'text');
                            } else {
                                $('#password').attr('type', 'password');
                            }
                        }


                        const Http = new XMLHttpRequest();
                        getApi("https://api.bigdatacloud.net/data/reverse-geocode-client");

                        function getApi(bdcApi) {
                            Http.open("GET", bdcApi);
                            Http.send();
                            Http.onreadystatechange = function() {
                                if (this.readyState == 4 && this.status == 200) {
                                    //result.innerHTML = this.responseText;
                                    get_loc = JSON.parse(this.responseText);

                                    $('#latitude').val(get_loc.latitude);
                                    $('#longitude').val(get_loc.longitude);
                                    $('#location').val(get_loc.locality);
                                } else {

                                }
                            };
                        }
                    </script>

                    <div class="forgot-password">
                        <a href="{{ url('recovery/passord') }}">Forgot your password?</a>
                    </div>

                    <button type="submit" class="login-btn btn-login-submit">LOG IN</button>

                    <div class="signup-link">
                        <span>Don't have an account? <a href="http://erpdemo.venushrms.com/public/design/new-signup.html">Sign up</a></span>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script src="{{ asset('public/design') }}/assets/js/_main.js" type="text/javascript"></script>
</body>

</html>

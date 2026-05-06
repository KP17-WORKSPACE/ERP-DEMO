<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <title>Venus ERP - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'greenenergy': {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                        }
                    },
                    fontFamily: {
                        'sans': ['Inter', 'ui-sans-serif', 'system-ui'],
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(120deg, #63c377 0%, #397c47 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .login-container {
            background: white;
            display: flex;
            max-width: 950px;
            width: 100%;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            position: relative;
        }

        .left-panel {
            background: linear-gradient(135deg, #68cb7c 0%, #499258 100%);
            flex: 1;
            padding: 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
            border-radius: 0 180px 0px 120px;
            overflow: hidden;
        }

        .right-panel {
            flex: 1;
            padding: 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* Leaf design for top-right corner of right panel */
        .right-panel::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
        }

        /* Additional decorative border curve */
        .right-panel::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 120px;
            background: linear-gradient(135deg, #22c55e 0%, #14532d 100%);
            border-bottom-left-radius: 100px;
            z-index: 1;
        }

        .leaf-shape {
            position: absolute;
            border-radius: 42% 58% 70% 30% / 30% 43% 57% 70%;
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.15) 0%, rgba(20, 83, 45, 0.1) 100%);
            animation: morph 20s linear infinite;
            z-index: 1;
        }

        @keyframes morph {

            0%,
            100% {
                border-radius: 42% 58% 70% 30% / 30% 43% 57% 70%;
            }

            33% {
                border-radius: 70% 30% 46% 54% / 30% 29% 71% 70%;
            }

            66% {
                border-radius: 100% 60% 60% 100% / 100% 100% 60% 60%;
            }
        }

        .input-field {
            transition: all 0.3s ease;
        }

        .input-field:focus {
            box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.1);
        }

        .btn-login {
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(34, 197, 94, 0.2), 0 2px 4px -1px rgba(34, 197, 94, 0.06);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(34, 197, 94, 0.3), 0 4px 6px -2px rgba(34, 197, 94, 0.05);
        }

        .leaf-icon {
            filter: drop-shadow(0 0 8px rgba(34, 197, 94, 0.4));
        }

        .bg-greenenergy-500 {
            background-color: #499258 !important;
        }

        .border-gray-300 {
            border-color: #499258 !important;
        }

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                border-radius: 16px;
            }

            .left-panel {
                border-radius: 0 0 16px 16px;
                padding: 2rem 1.5rem;
            }

            .right-panel {
                border-radius: 16px 16px 0 0;
                padding: 2rem 1.5rem;
            }

            .right-panel::before,
            .right-panel::after {
                display: none;
            }
        }
    </style>
</head>

<body>
    <!-- Background leaf shapes -->
    <div class="leaf-shape w-64 h-64 top-1/4 -left-10 opacity-70"></div>
    <div class="leaf-shape w-80 h-80 bottom-1/4 -right-16 opacity-50" style="animation-delay: 5000ms;"></div>
    <div class="leaf-shape w-56 h-56 top-1/3 right-1/4 opacity-30" style="animation-delay: 7000ms;"></div>

    <div class="login-container">
        <!-- Left side - Brand Section -->
        <div class="left-panel">
            <div class="text-center mb-8 z-20">
                <div class="leaf-icon inline-flex items-center justify-center w-16 h-16 rounded-full bg-white bg-opacity-20 mb-6"
                    style="background: #ffffff;">
                    <!-- <i class="fas fa-leaf text-3xl text-white"></i> -->
                    <img src="{{asset('public/design/assets/images/erp-logo-icon.png')}}" alt="Logo" class="w-10 h-10">
                </div>
                <h1 class="text-4xl font-bold mb-2 text-white">Venus ERP</h1>
                <p class="text-greenenergy-100">Smarter Deals, Stronger Business</p>
            </div>

            <div class="text-center max-w-xs z-20">
                <p class="text-greenenergy-100 mb-8">Empower your business to close more deals and achieve smarter
                    growth.</p>
                <p class="text-greenenergy-100 mb-8">Your all-in-one sales companion — track performance, manage leads,
                    monitor deals, and stay on top of your targets effortlessly.</p>

                <!-- <button class="border-2 border-white border-opacity-30 rounded-full px-6 py-2 text-sm font-semibold text-white hover:bg-white hover:bg-opacity-10 transition-all duration-300">
                    SIGN IN
                </button> -->
            </div>

            <!-- <div class="mt-16 text-center text-greenenergy-200 text-xs z-20">
                <p>ALREADY HAVE AN ACCOUNT ? LOG IN</p>
            </div> -->

            <!-- Additional decorative elements -->
            <div class="leaf-shape w-48 h-48 -bottom-16 -left-16 opacity-20"></div>
            <div class="leaf-shape w-32 h-32 top-16 -right-8 opacity-30"></div>
        </div>

        <!-- Right side - Login Form -->
        <div class="right-panel">
            <div class="mb-8 text-center md:text-left">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">Welcome</h2>
                <p class="text-gray-500">Login to your account to continue</p>
            </div>

            <form action="{{ route('login') }}" name="login" method="POST" id="infix_form2" class="space-y-6">
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
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2" for="email">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="far fa-envelope text-gray-400"></i>
                        </div>
                        <input required id="email" type="email" name="email"
                            class="input-field w-full pl-10 pr-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:border-greenenergy-500"
                            placeholder="Enter your email">
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2" for="password">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input id="password" type="password" name="password"
                            class="input-field w-full pl-10 pr-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:border-greenenergy-500"
                            placeholder="Enter your password" required>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <button type="button" class="text-gray-400 hover:text-greenenergy-500 focus:outline-none">
                                <i class="fas fa-eye-slash"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <a href="{{ url('recovery/passord') }}" class="text-sm text-greenenergy-600 hover:text-greenenergy-800 font-medium">Forgot your
                        password?</a>
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
                        Http.onreadystatechange = function () {
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
                <button type="submit"
                    class="btn-login w-full bg-greenenergy-500 text-white py-3 rounded-xl font-semibold hover:bg-greenenergy-600">
                    LOG IN
                </button>
            </form>

            <div class="mt-8 text-center">
                <p class="text-gray-600">Don't have an account? <a href="http://erpdemo.venushrms.com/public/design/new-signup.html"
                        class="text-greenenergy-600 font-medium hover:text-greenenergy-800">Sign up</a></p>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        document.querySelector('.fa-eye-slash').parentElement.addEventListener('click', function (e) {
            e.preventDefault();
            const passwordInput = document.getElementById('password');
            const eyeIcon = this.querySelector('i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            }
        });
    </script>
    <script src="{{ asset('public/design') }}/assets/js/_main.js" type="text/javascript"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        toastr.options = { closeButton: true, progressBar: true, positionClass: 'toast-top-right', timeOut: 5000 };
        @if ($errors->any())
            toastr.error('{{ $errors->first() }}', 'Login Failed');
        @endif
        @if (session('message-danger'))
            toastr.error('{{ session('message-danger') }}', 'Access Denied');
        @endif
        @if (session('message-success'))
            toastr.success('{{ session('message-success') }}', 'Success');
        @endif
    </script>
</body>

</html>
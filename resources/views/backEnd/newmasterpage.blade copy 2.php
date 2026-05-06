<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('public/admin-iroid/') }}/img/erp-logo-icon.png" type="image/png"/>
    <title>Venus ERP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('public/design') }}/assets/css/_main.css" />
    <link rel="stylesheet" href="{{ asset('public/design') }}/assets/css/_icomoon.css" />
    <link rel="stylesheet" href="{{ asset('public/design') }}/assets/css/_header.css" />
    <link rel="stylesheet" href="{{ asset('public/design') }}/assets/css/_navbar.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="{{ asset('public/admin-iroid/') }}/vendor/jquery/jquery.min.js"></script>

</head>
<body>
    <?php
    if(App\SysHelper::password_update()==1){
        header("Location: ".url('/')."/password-exp");
        exit();
    } ?>



    <div id="loading_bg"
        style="width: 100vw; height: 100vh; background: #00000085; position: fixed; z-index:9999; text-align: center; display:none;">
        <img src="{!! asset('public/backEnd/img/loader.gif') !!}" style="width: 50px; margin: 20%;">
    </div>

    <div class="venus-app page-company">
        <header class="main-header">
            <div class="logo"><img src="{{ asset('public/design') }}/assets/images/logo-white.png" alt="VENUS LOGO" /></div>
            <div class="right-section">
            @php 
                $com_list = App\SysHelper::get_company_names(); 
                $current_company_id = session('logged_session_data.company_id');
                $current_company = $com_list->firstWhere('id', $current_company_id);
                $staff = App\SmStaff::where('user_id',Auth::id())->first();
            @endphp

                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="dropdown-toggle-text">{{ $current_company ? $current_company->company_name : 'Select Company' }}</div>
                        <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>
                    </button>
                    <ul class="dropdown-menu">
                     @foreach ($com_list as $list)
                        <li>
                            <a class="dropdown-item {{ $current_company_id == $list->id ? 'active' : '' }}"
                            
                            onclick="app_company_change({{$list->id}})">
                                {{ $list->company_name }}
                            </a>
                        </li>
                     @endforeach
                    </ul>
                </div>
                 <script>
            function app_company_change (companyid) {
                
                var action = "{{ URL::to('set-company-id') }}";
                    $.ajax({
                        url: action,
                        type: "POST",
                        data: {
                            _token: '{{ csrf_token() }}',
                            companyid: companyid,
                        },
                        cache: false,
                        success: function(dataResult) {
                            var returl = "{{ URL::to('crm-dashboard') }}";
                            window.location.href = returl;
                            //location.reload();
                        },
                    });
                $("#loading_bg").css("display", "block");
            }
        </script>

                <button class="btn btn-light add-btn"><i class="ico icon-outline-add-circle"></i></button>
                <button class="btn btn-light notification-unread"><i class="ico icon-outline-bell"></i></button>
                <div class="profile-dropdown">
                    <div class="profile-img">
                    @if ($staff)
                        <img width="26px" height="26px" src="{{ $staff->staff_photo ? asset($staff->staff_photo) : asset('public/uploads/staff/demo/staff.jpg') }}" alt="">
                    @else
                        <img width="26px" height="26px" src="{{ asset('/') }}public/uploads/staff/demo/staff.jpg" alt="">
                    @endif
                    </div>
                    <div class="dropdown">
                        <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <div class="dropdown-toggle-text">
                              
                                <div class="title">{{Auth::user()->full_name}}</div>
                                <div class="role">{{Auth::user()->email}}</div>
                            </div>
                            <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico text-white"></i>
                        </button>
                        <div class="dropdown-menu profile-dropdown-body py-4 px-3">
                            <div class="profile-content">
                                <img width="26px" height="26px" src="{{ $staff->staff_photo ? asset($staff->staff_photo) : asset('public/uploads/staff/demo/staff.jpg') }}" alt="profile_img" class="profile-image">
                                <h4 class="profile_name">{{Auth::user()->full_name}}</h4>
                               <div class=""> <h4 class="second-text">{{ $staff->roles->name }}</h4></div>
                                <div class="company-name">{{$staff->maincompany ? $staff->maincompany->company_name : 'Not Found'}}</div>
                                <div class="profile-second-section py-3">
                                    <h4 class="company-details-text mb-3">Company Details</h4>
                                    <div class="row">
                                        <div class="col-7">Designation:
                                            <h6 class="">{{ $staff->designations->title }}</h6>
                                            Staff ID:
                                            <h6>{{$staff->staff_no}}</h6>
                                        </div>
                                        <div class="col-5">Departments:
                                            <h6>{{$staff->departments->name}}</h6>
                                            Date Of Joining:
                                            <h6> {{ date('jS M, Y', strtotime(@$staff->date_of_joining)) }}</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="profile-third-section py-3">
                                    <h4 class="personal-details-text">Personal Details</h4>
                                    <div class="contact-email">
                                        <img src="{{ asset('public/design') }}/assets/images/telephone_Vector.png" class="telephone" alt="telephone_icon">
                                        <h6>{{$staff->mobile ?? '--' }}</h6>
                                    </div>
                                    <div class="contact-email">
                                        <img src="{{ asset('public/design') }}/assets/images/envelop_Vector.png" class="email" alt="email_icon">
                                        <h6>{{$staff->email ?? '--'}}</h6>
                                    </div>
                                </div>
                                <div class="profile-last-section">
                                    <div>
                                        <button class="btn ">
                                            <i class="ico icon-outline-pen-2"></i>
                                        </button>
                                        <p class="button-text">Edit Profile</p>
                                    </div>
                                    <div>
                                        <button class="btn">
                                            <img src="{{ asset('public/design') }}/assets/images/Lock Keyhole Minimalistic.png" alt="lock_img">
                                        </button>
                                        <p class="button-text">Change Password</p>
                                    </div>
                                    <div >
                                        <button class="btn">
                                            <img src="{{ asset('public/design') }}/assets/images/Logout 2.png" alt="logout">
                                        </button>
                                        <p class="button-text-logout">Logout</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <main>
            @include('backEnd.partials.sidenavnew')
            
            <section class="main-content">
                @yield('mainContent')
            </section>
        </main>
    </div>
    <!-- <div class="mockup-overlay">
        <img src="{{ asset('public/design') }}/assets/mockup-deal-list.jpg" alt="MOCKUP" />
    </div> -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="{{ asset('public/design') }}/assets/js/_main.js" type="text/javascript"></script>
    <script src="{{ asset('public/design') }}/assets/js/deal-list.js" type="text/javascript"></script>
    <script src="{{ asset('public/design') }}/assets/js/company.js" type="text/javascript"></script>
    <script src="{{ asset('public/design') }}/assets/js/aditional.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('public/admin-iroid/') }}/vendor/jquery-easing/jquery.easing.min.js"></script>

    <script>
        $(document).ready(function () {
            $('.js-example-basic-single').select2({
                   width: '100%' 
            });
        });
    </script>

</body>
</html>

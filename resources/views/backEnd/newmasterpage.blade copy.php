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
    <link rel="stylesheet" href="{{ asset('public/design') }}/assets/css/deal-list.css" />
    <link rel="stylesheet" href="{{ asset('public/design') }}/assets/css/company.css" />
    <link rel="stylesheet" href="{{ asset('public/design') }}/assets/css/support.css" />



</head>
<body>
    <?php
    if(App\SysHelper::password_update()==1){
        header("Location: ".url('/')."/password-exp");
        exit();
    } ?>

    <?php
    if(session('logged_session_data.auth_status')==0){
        header("Location: ".url('/')."/crm-auth");
        exit();
    } ?>


    <div id="loading_bg"
        style="width: 100vw; height: 100vh; background: #00000085; position: fixed; z-index:9999; text-align: center; display:none;">
        <img src="{!! asset('public/backEnd/img/loader.gif') !!}" style="width: 50px; margin: 20%;">
    </div>

    <div class="venus-app page-company">
        <header class="main-header">
            <div class="logo"><img src="{{ asset('public/design') }}/assets/images/logo-gray.png" alt="VENUS LOGO" /></div>
            <div class="right-section">
                <div class="dropdown">
        @php $com_list = App\SysHelper::get_company_names(); @endphp
        <select class="btn btn-light dropdown-toggle syscom-dropdown-toggle text-left" id="main_company_id" style="text-align: left;" onchange="app_company_change()">
            @foreach ($com_list as $list)
                <option value="{{ $list->id }}" @if (session('logged_session_data.company_id') == $list->id) selected @endif>{{ $list->company_name }}</option>
            @endforeach
        </select>
        <script>
            function app_company_change () {
                var companyid = $("#main_company_id").val();
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
                </div>
                <button class="btn btn-light add-btn"><i class="ico icon-outline-add-circle"></i></button>
                <button class="btn btn-light notification-unread"><i class="ico icon-outline-bell"></i></button>
                <div class="profile-dropdown">
                    <div class="profile-img">
                @if (file_exists(@$profile_image))
                    <img width="40px" height="40px" src="{{ file_exists(@$profile_image) ? asset($profile_image) : asset('public/uploads/staff/demo/staff.jpg') }}" alt="">
                @else
                    <img width="40px" height="40px" src="{{ asset('/') }}public/uploads/staff/demo/staff.jpg" alt="">
                @endif
                        {{--  <img src="{{ asset('public/design') }}/assets/images/profile.png" alt="PROFILE">  --}}
                    </div>
                    <div class="dropdown">
                        <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="dropdown-toggle-text">
                                <div class="title">{{Auth::user()->full_name}}</div>
                                <div class="role">{{Auth::user()->email}}</div>
                            </div>
                            <i class="ico icon-Outline-Alt-Arrow-Down dropdown-toggle-ico"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                Logout
                            </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
<!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="{{ route('logout')}}">Logout</a>
                </div>
            </div>
        </div>
    </div>
<!-- Logout Modal-->

        </header>
        <main>
            <nav class="main-nav">
                <div class="toggle-nav"></div>
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="" class="nav-link">
                            <i class="ico icon-outline-widget-6"></i>
                            <span class="nav-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item dropend">
                        <div class="sub-menu-nav dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ico icon-outline-calculator"></i>
                            <span class="nav-text">CRM</span>
                        </div>
                        <div class="dropdown-menu">
                            <div class="sub-nav-item">
                                <a href="lead-list.html" class="sub-nav-link">Leads</a>
                            </div>
                            <div class="sub-nav-item">
                                <a href="deal-list.html" class="sub-nav-link">Deals</a>
                            </div>
                            <div class="sub-nav-item">
                                <a href="" class="sub-nav-link">Deals Track</a>
                            </div>
                            <div class="sub-nav-item">
                                <a href="" class="sub-nav-link">Deals Track Status</a>
                            </div>
                            <div class="sub-nav-item">
                                <a href="" class="sub-nav-link">Sales Report</a>
                            </div>
                            <div class="sub-nav-item">
                                <a href="" class="sub-nav-link">Brand Sales Report</a>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item dropend">
                        <div class="sub-menu-nav dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ico icon-outline-checklist-minimalistic"></i>
                            <span class="nav-text">Service Desk</span>
                        </div>
                        <div class="dropdown-menu">
                            <div class="sub-nav-item">
                                <a href="" class="sub-nav-link">Service Desk Sub-menu Item 1</a>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item dropend">
                        <div class="sub-menu-nav dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ico icon-outline-database"></i>
                            <span class="nav-text">Database</span>
                        </div>
                        <div class="dropdown-menu">
                            <div class="sub-nav-item">
                                <a href="" class="sub-nav-link">Database Sub-menu Item 1</a>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item dropend">
                        <div class="sub-menu-nav dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ico icon-outline-cart-large-4"></i>
                            <span class="nav-text">Purchase</span>
                        </div>
                        <div class="dropdown-menu">
                            <div class="sub-nav-item">
                                <a href="supplier-register.html" class="sub-nav-link">Supplier Register</a>
                            </div>
                            <div class="sub-nav-item">
                                <a href="purchase-order.html" class="sub-nav-link">Purchase Order</a>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item dropend">
                        <div class="sub-menu-nav dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ico icon-outline-bag-4"></i>
                            <span class="nav-text">Sales</span>
                        </div>
                        <div class="dropdown-menu">
                            <div class="sub-nav-item">
                                <a href="quotations.html" class="sub-nav-link">Quotations</a>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item dropend">
                        <div class="sub-menu-nav dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ico icon-outline-server"></i>
                            <span class="nav-text">Inventory</span>
                        </div>
                        <div class="dropdown-menu">
                            <div class="sub-nav-item">
                                <a href="" class="sub-nav-link">Inventory Sub-menu Item 1</a>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item dropend">
                        <div class="sub-menu-nav dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ico icon-outline-document-text"></i>
                            <span class="nav-text">Reports</span>
                        </div>
                        <div class="dropdown-menu">
                            <div class="sub-nav-item">
                                <a href="" class="sub-nav-link">Reports Sub-menu Item 1</a>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item active dropend">
                        <div class="sub-menu-nav dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ico icon-bold-settings"></i>
                            <span class="nav-text">Settings</span>
                        </div>
                        <div class="dropdown-menu">
                            <div class="sub-nav-item">
                                <a href="company.html" class="sub-nav-link">Company</a>
                            </div>
                            <div class="sub-nav-item">
                                <a href="shipping.html" class="sub-nav-link">Shipping</a>
                            </div>
                            <div class="sub-nav-item">
                                <a href="role.html" class="sub-nav-link">Role</a>
                            </div>
                        </div>
                    </li>
                </ul>
            </nav>
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
</body>
</html>

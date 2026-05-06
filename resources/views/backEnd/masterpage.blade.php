<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="{{ asset('public/admin-iroid/') }}/img/erp-logo-icon.png" type="image/png"/>
    <title>Venus ERP</title>

    <link href="{{ asset('public/admin-iroid/') }}/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link href="{{ asset('public/admin-iroid/') }}/css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('public/admin-iroid/') }}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <script src="{{ asset('public/admin-iroid/') }}/vendor/jquery/jquery.min.js"></script>
    
    <link rel="stylesheet" href="{{asset('public/backEnd/')}}/vendors/css/toastr.min.css"/>

</head>

<body id="page-top">

    <?php
    if(App\SysHelper::password_update()==1){
        header("Location: ".url('/')."/password-exp");
        exit();
    } ?>
    <div id="loading_bg"
        style="width: 100vw; height: 100vh; background: #00000085; position: fixed; z-index:9999; text-align: center; display:none;">
        <img src="{!! asset('public/backEnd/img/loader.gif') !!}" style="width: 50px; margin: 20%;">
    </div>

    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        @include('backEnd.partials.sidenav')
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                @include('backEnd.partials.topnav')
                <script>
                let currentCompanyId = "{{ session('logged_session_data.company_id') }}";
                localStorage.setItem("active_company_id", currentCompanyId);
                </script>

                <script>
                window.addEventListener("storage", function (event) {

                    if (event.key === "active_company_id") {
                        let newCompanyId = event.newValue;
                        let currentPageCompany = "{{ session('logged_session_data.company_id') }}";
                        if (newCompanyId != currentPageCompany) {
                            window.close();
                            setTimeout(function () {
                                window.location.href = "{{ url('company-error') }}";
                            }, 100);
                        }
                    }
                });
                </script>
                @yield('mainContent')


            </div>
        </div>
    </div>
    </div>
    </div>
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- script -->
    <script src="{{ asset('public/admin-iroid/') }}/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('public/admin-iroid/') }}/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="{{ asset('public/admin-iroid/') }}/js/sb-admin-2.min.js"></script>
    <script src="{{ asset('public/admin-iroid/') }}/vendor/chart.js/Chart.min.js"></script>
    <script src="{{ asset('public/admin-iroid/') }}/js/demo/chart-area-demo.js"></script>
    <script src="{{ asset('public/admin-iroid/') }}/js/demo/chart-pie-demo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('public/admin-iroid/') }}/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ asset('public/admin-iroid/') }}/vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <script src="{{ asset('public/admin-iroid/') }}/js/demo/datatables-demo.js"></script>

    
<script type="text/javascript" src="{{asset('public/backEnd/')}}/vendors/js/toastr.min.js"></script>


<script src="{{asset('public/backEnd/')}}/js/custom.js"></script>
<script src="{{asset('public/backEnd/')}}/js/developer.js"></script>
<script src="{{asset('public/backEnd/')}}/js/erpjs.js"></script>


    <script>
        $(document).ready(function () {
            $('.js-example-basic-single').select2();
        });
    </script>

    
{!! Toastr::message() !!}

    <!-- script -->
</body>

</html>
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
    <title>Customer Form</title>
    <meta name="_token" content="{!! csrf_token() !!}"/>
    <link rel="stylesheet" href="{{asset('public/backEnd/')}}/vendors/css/themify-icons.css" />

	{{--  -----------------  --}}
	
    <link href="{{ asset('public/admin-iroid/') }}/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link href="{{ asset('public/admin-iroid/') }}/css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('public/admin-iroid/') }}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <script src="{{ asset('public/admin-iroid/') }}/vendor/jquery/jquery.min.js"></script>    
    <link rel="stylesheet" href="{{asset('public/backEnd/')}}/vendors/css/toastr.min.css"/>


</head>
<body class="hight_100" style="background: #c2c2c2;">

	
    <!--================ Start Login Area =================-->
	

	<div class="row">
		<div class="col-md-2"></div>
	<div class="col-md-8 mt-4 p-4" style="background: #ffffff;">
		<b style="font-size: 25px;">Customer Form</b>
		<img src="{{asset('public/backEnd/img/syscom.png')}}" align="right"/><br /><br />


		<hr>
			



		<input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">

		<div class="row">
			<div class="col-md-12 text-center"><br /><br /><br /><br /><br /><br />
				<h3>Thank you for submitting the details.</h3>
				<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
			</div>				
		</div>

		</div>
		<div class="col-md-2"></div>
	</div>

		<script>
			$('#btnSubmit').click(function () {
				$('input:invalid').each(function () {
					var $closest = $(this).closest('.tab-pane');
					var id = $closest.attr('id');
					$('.nav a[href="#' + id + '"]').tab('show');
					return false;
				});
			});
		</script>



	<!--================ Start End Login Area =================-->

	<!--================ Footer Area =================-->
	<footer class="footer_area">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-lg-12 text-center"> 
				</div>
			</div>
		</div>
	</footer>
	<!--================ End Footer Area =================-->

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
</body>
</html>
